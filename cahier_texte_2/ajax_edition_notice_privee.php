<?php
/*
 *
 * Copyright 2009-2024 Josselin Jacquard, Stephane Boireau
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

header('Content-Type: text/html; charset=utf-8');

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
//echo("Debug Locale : ".setLocale(LC_TIME,0));

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (!acces_cdt()) {
	die("Le module n'est pas activé.");
}

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

$tab_termes_CDT2=get_texte_CDT2();

//récupération des parametres
//id du notice_privee
$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);
//si on vient d'enregistrer des modification, on va afficher un message de confirmation
$succes_modification = isset($_POST["succes_modification"]) ? $_POST["succes_modification"] :(isset($_GET["succes_modification"]) ? $_GET["succes_modification"] :NULL);
//si pas de notice_privee passé en paramètre, on récupère la date du jour pour se caler dessus, sinon on prendra la date du notice_privee
$today = isset($_POST["today"]) ? $_POST["today"] :(isset($_GET["today"]) ? $_GET["today"] :NULL);
$ajout_nouvelle_notice = isset($_POST["ajout_nouvelle_notice"]) ? $_POST["ajout_nouvelle_notice"] :(isset($_GET["ajout_nouvelle_notice"]) ? $_GET["ajout_nouvelle_notice"] :NULL);

$cahierTexteNoticePrivee = CahierTexteNoticePriveePeer::retrieveByPK($id_ct);
if ($cahierTexteNoticePrivee != null) {
	$groupe = $cahierTexteNoticePrivee->getGroupe();
	$today = $cahierTexteNoticePrivee->getDateCt();
} else {
	//si pas de notice précisé, récupération du groupe dans la requete et recherche d'une notice pour la date précisée ou création d'une nouvelle notice
	//pas de notices, on lance une création de notice
	$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
	$groupe = GroupePeer::retrieveByPK($id_groupe);
	if ($groupe == null) {
		echo("Erreur edition de notice privee : pas de groupe spécifié");
		die;
	}

	// Vérification : est-ce que l'utilisateur a le droit de travailler sur ce groupe ?
	if (!$groupe->belongsTo($utilisateur)) {
		echo "Erreur edition de notice privee : le groupe n'appartient pas au professeur";
		die();
	}

	if ($ajout_nouvelle_notice != "oui") {
		//on cherche si il y a une notice pour le groupe à la date précisée
		$criteria = new Criteria(CahierTexteNoticePriveePeer::DATABASE_NAME);
		$criteria->add(CahierTexteNoticePriveePeer::DATE_CT, $today, '=');
		$criteria->add(CahierTexteNoticePriveePeer::ID_LOGIN, $utilisateur->getLogin());
		$cahierTexteNoticePrivees = $groupe->getCahierTexteNoticePrivees($criteria);
		$cahierTexteNoticePrivee = isset($cahierTexteNoticePrivees[0]) ? $cahierTexteNoticePrivees[0] : NULL;
	}

	if ($cahierTexteNoticePrivee == null) {
		//pas de notices, on initialise un nouvel objet
		$cahierTexteNoticePrivee = new CahierTexteNoticePrivee();
		$cahierTexteNoticePrivee->setIdGroupe($groupe->getId());
		$cahierTexteNoticePrivee->setDateCt($today);
		$cahierTexteNoticePrivee->setIdLogin($utilisateur->getLogin());
	}

}

// Vérification : est-ce que l'utilisateur a le droit de modifier cette entré ?
if (my_strtolower($cahierTexteNoticePrivee->getIdLogin()) != my_strtolower($utilisateur->getLogin())) {
	echo("Erreur edition de notice privee : vous n'avez pas le droit de modifier cette notice car elle appartient à un autre professeur.");
	die();
}

//================================================
$sql="SELECT 1=1 FROM j_groupes_visibilite WHERE id_groupe='".$groupe->getId()."' AND domaine='cahier_texte' AND visible='n';";
$test_grp_visib=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test_grp_visib)!=0) {
	echo "<p style='color:red'>Le groupe n°".$groupe->getId()." n'est pas autorisé pour les cahiers de textes.</p>";
	die();
}
//================================================

//on met le groupe dans le session, pour naviguer entre absence, cahier de texte et autres
$_SESSION['id_groupe_session'] = $cahierTexteNoticePrivee->getIdGroupe();
$id_groupe=$cahierTexteNoticePrivee->getIdGroupe();

// **********************************************

// Affichage des différents groupes du professeur
//\$A($('id_groupe_colonne_gauche').options).find(function(option) { return option.selected; }).value is a javascript trick to get selected value.
echo "<div id=\"div_chaine_edition_notice\" style=\"display:inline;\"><img id=\"chaine_edition_notice\" onLoad=\"updateChaineIcones()\" style=\"border: 0px; vertical-align : middle\" HEIGHT=\"16\" WIDTH=\"16\" src=\"../images/blank.gif\" alt=\"Lier\" title=\"Lier la liste avec la fenetre la liste des notices\" /></div>&nbsp;\n";
echo ("<select id=\"id_groupe_colonne_droite\" onChange=\"javascript:
			updateListeNoticesChaine();
			id_groupe = (\$A($('id_groupe_colonne_droite').options).find(function(option) { return option.selected; }).value);
			getWinEditionNotice().setAjaxContent('./ajax_edition_notice_privee.php?today=".$today."&id_groupe=' + id_groupe,
      			 { onComplete:function() {initWysiwyg();}}
      		);
			compte_rendu_en_cours_de_modification('aucun');
		\">\n");
echo "<option value='-1'>choisissez un groupe</option>\n";
$groups = $utilisateur->getGroupes();
foreach ($groups as $group_iter) {
	$sql="SELECT 1=1 FROM j_groupes_visibilite WHERE id_groupe='".$group_iter->getId()."' AND domaine='cahier_texte' AND visible='n';";
	$test_grp_visib=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_grp_visib)==0) {
		echo "<option id='colonne_droite_select_group_option_".$group_iter->getId()."' value='".$group_iter->getId()."'";
		if ($groupe->getId() == $group_iter->getId()) echo " SELECTED ";

		echo " title=\"".$group_iter->getName()." - ".$group_iter->getDescriptionAvecClasses()." (";
		$cpt_prof=0;
		foreach($group_iter->getProfesseurs() as $prof) {
			if($cpt_prof>0) {echo ", ";}
			echo casse_mot($prof->getNom(),"maj")." ".casse_mot($prof->getPrenom(),"majf2");
			$cpt_prof++;
		}
		echo ").\"";

		echo ">";
		echo $group_iter->getDescriptionAvecClasses();
		echo "</option>\n";
	}
}
echo "</select>\n&nbsp;&nbsp;\n";
//fin affichage des groupes

echo "<button style='background-color:".$color_fond_notices['c']."' onclick=\"javascript:
						getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe='+ ".$groupe->getId()." + '&today='+getCalendarUnixDate(),{ onComplete:function(transport) {initWysiwyg();}});
						object_en_cours_edition = 'compte_rendu';
					\">Editer les comptes rendus</button>\n";
echo "<button style='background-color:".$color_fond_notices['t']."' onclick=\"javascript:
						getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_groupe='+ ".$groupe->getId()." + '&today='+getCalendarUnixDate(),{ onComplete:function(transport) {initWysiwyg();}});
						object_en_cours_edition = 'devoir';
					\">Editer les devoirs</button>\n";

/*
echo " <button style='background-color:".$color_fond_notices['p']."' onclick=\"javascript:
						getWinListeNoticesPrivees().setAjaxContent('./ajax_liste_notices_privees.php?id_groupe=".$groupe->getId()."&today='+getCalendarUnixDate(),{ onComplete:function(transport) {initWysiwyg();}});
					\">Voir NP</button>\n";
*/
echo " <button style='background-color:".$color_fond_notices['p']."' onclick=\"javascript:
						getWinListeNoticesPrivees().setAjaxContent('./ajax_liste_notices_privees.php?id_groupe=".$groupe->getId()."&today='+getCalendarUnixDate());
					\" title=\"".$tab_termes_CDT2['attribut_title_CDT2_Voir_NP']."\">Voir NP</button>\n";

echo "<button style='background-color:lightblue' onclick=\"javascript:
						getWinBanqueTexte().setAjaxContent('./ajax_affichage_banque_texte.php',{});
					\" title=\"".$tab_termes_CDT2['attribut_title_CDT2_Banque']."\">Banque</button>\n";

echo "<button style='background-color:lightblue' onclick=\"javascript:
						getWinCarSpec().setAjaxContent('./ajax_affichage_car_spec.php',{});
					\" title=\"".$tab_termes_CDT2['attribut_title_CDT2_CarSpec']."\">&Omega;</button>\n";

if(file_exists("./archives.php")) {
	// Mon fichier contient juste:
	/* <?php echo "<iframe src='../documents/archives/index.php' width='100%' height='100%'/>"; ?> */
	echo "<button style='background-color:bisque' onclick=\"javascript:
						getWinArchives().setAjaxContent('./archives.php',{});
					\" title=\"".$tab_termes_CDT2['attribut_title_CDT2_Archives']."\">Archives</button>\n";
}
echo "<button style='background-color:azure' onclick=\"javascript:
						getWinDocsJoints().setAjaxContent('./documents_cdt.php?id_groupe=".$groupe->getId()."&entete=n',{});
					\" title=\"".$tab_termes_CDT2['attribut_title_CDT2_PJ']."\">PJ</button>\n";

// 20220312
if(getSettingValue('url_latex2image')!='') {
	// insert into setting set name='url_latex2image', value='https://latex2image.joeraut.com/';
	echo "<a href='".getSettingValue('url_latex2image')."' target='_blank'><img src='../images/equation.png' class='icone16' /></a> ";
}

echo "<a href=\"javascript:insere_texte_dans_ckeditor(document.getElementById('div_tableau_eleves').innerHTML)\" title='Insérer un tableau de la liste des élèves dans le texte de la notice'><img src='../images/icons/tableau.png' width='16' height='16' alt='Insérer un tableau de la liste des élèves dans le texte de la notice' /></a>";

echo " <a href=\"#\" onclick=\"fen=window.open('../groupes/popup.php?id_groupe=".$groupe->getId()."&avec_icone_visu_ele=y','','width=400,height=400,menubar=yes,scrollbars=yes'); setTimeout('fen.focus()',500); return false;\" title='Afficher en popup la liste des élèves pour accéder aux fiches élèves (et vérifier par exemple les absences de tel élève,...)'><img src='../images/icons/ele_onglets.png' width='16' height='16' alt='Popup' /></a>";

if((isset($cahierTexteNoticePrivee))&&($cahierTexteNoticePrivee->getIdCt()!=null)) {
	echo " <a href='affiche_notice.php?id_ct=".$cahierTexteNoticePrivee->getIdCt()."&type_notice=p' target='_blank' title=\"Afficher la notice dans un nouvel onglet.\"><img src='../images/icons/chercher.png' class='icone16' alt='Afficher' /></a>";
}

echo " <a href=\"#\" style=\"font-size: 11pt;\" title=\"Recharger la page. Cela peut être utile si le champ de saisie ne s'affiche pas.\" onclick=\"javascript:
				id_groupe = '".$id_groupe."';
				getWinDernieresNotices().hide();
				getWinListeNotices();
				new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$id_groupe."', {encoding: 'utf-8'});
				getWinEditionNotice().setAjaxContent('./ajax_edition_notice_privee.php?id_groupe=".$id_groupe."&today='+getCalendarUnixDate(), { 
				    		encoding: 'utf-8',
				    		onComplete : 
				    		function() {
				    			initWysiwyg();
							}
						}
				);
				return false;
			\"><img src='../images/icons/actualiser.png' class='icone16' /></a>";


// 20150415
$tempdir=get_user_temp_directory($_SESSION['login']);
if(file_exists("../temp/".$tempdir."/cdt_selection.txt")) {
	$chaine_selection_archives=ensure_utf8(file_get_contents("../temp/".$tempdir."/cdt_selection.txt"));
	echo "<textarea id='textarea_selection_archives' name='textarea_selection_archives' style='display:none;'>".base64_encode($chaine_selection_archives)."</textarea>";
}

// Nombre de notices pour ce jour :
$num_notice = NULL;

echo "<fieldset style=\"border: 1px solid grey; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto; background: ".$color_fond_notices['p'].";\">\n";
echo "<legend style=\"border: 1px solid grey; background: ".$color_fond_notices['p']."; font-variant: small-caps;\"> Notice priv&eacute;e - ".$groupe->getNameAvecClasses();

if (!$cahierTexteNoticePrivee->isNew() || isset($info)) {
	echo " - <b><font color=\"red\">Modification de la notice</font></b> -
			<a href=\"#\" onclick=\"javascript:
				getWinEditionNotice().setAjaxContent('ajax_edition_notice_privee.php?id_groupe=".$groupe->getId()."&today=".$cahierTexteNoticePrivee->getDateCt()."&ajout_nouvelle_notice=oui',
					 { onComplete:function() {initWysiwyg();}}
				);
				compte_rendu_en_cours_de_modification('aucun');
				return false;
			\">
			Ajouter une notice
			</a> - \n";
	echo "<a href=\"#\" onclick=\"javascript:
				new Ajax.Updater($('dupplication_notice'), 'ajax_affichage_duplication_notice.php?id_groupe=".$groupe->getId()."&type=CahierTexteNoticePrivee&id_ct=".$cahierTexteNoticePrivee->getIdCt()."',
					{ onComplete:
						function() {
							$('dupplication_notice').show();
							calendarDuplicationInstanciation = null;";
	if (!isset($info)) {
		//on affiche le calendrier de duplication uniquement si ce n'est pas une notice d'information generale
		echo "calendarDuplicationInstanciation = Calendar.setup({
										flat         : 'calendar-duplication-container', // ID of the parent element
										daFormat     : '%s' ,   			   //date format
										weekNumbers  : false
								})
								calendarDuplicationInstanciation.setDate(calendarInstanciation.date);";
	}
	echo "
						}
					}
				);
				return false;
				\">
		Dupliquer la notice</a> - \n";
} else {
	echo " - <b><font color=\"red\">Nouvelle notice</font></b> - \n";
}
echo "<a href=\"#\" onclick=\"javascript:
			new Ajax.Updater($('deplacement_notice'), 'ajax_affichage_deplacement_notice.php?id_groupe=".$groupe->getId()."&type=CahierTexteNoticePrivee&id_ct=".$cahierTexteNoticePrivee->getIdCt()."',
				{ onComplete:
					function() {
						$('deplacement_notice').show();
						calendarDeplacementInstanciation = null;";
if (!isset($info)) {
	//on affiche le calendrier de duplication uniquement si ce n'est pas une notice d'information generale
	echo "calendarDeplacementInstanciation = Calendar.setup({
									flat         : 'calendar-deplacement-container', // ID of the parent element
									daFormat     : '%s' ,   			   //date format
									weekNumbers  : false
							})
							calendarDeplacementInstanciation.setDate(calendarInstanciation.date);";
}
echo "
					}
				}
			);
			return false;
			\">
	Deplacer la notice</a>\n";

//il faut échapper les single quote pour le contenu à importer
$contenu_a_copier =  isset($_SESSION['ct_a_importer']) ? $_SESSION['ct_a_importer']->getContenu() : '';
echo (" <a href=\"#\" onclick=\"javascript: /*contenu_a_copier est globale*/
    if (window.contenu_a_copier == undefined) {
        contenu_a_copier = '".addslashes(htmlspecialchars($contenu_a_copier))."';
    }
    CKEDITOR.instances['contenu'].insertHtml(contenu_a_copier);");
echo("\"><img style=\"border: 0px;\" src=\"../images/icons/copy-16-gold.png");
echo("\" alt=\"Coller\" title=\"Coller le contenu\" /></a>\n");

// 20150415
if(isset($chaine_selection_archives)) {
	//echo "<a href=\"javascript:insere_texte_dans_ckeditor(atob(document.getElementById('textarea_selection_archives').value))\" title='Insérer le contenu de la sélection effectuée dans les archives'><img src='../images/icons/copy-16.png' width='16' height='16' alt='Insérer sélection archives' />A</a>";
	echo "<a href=\"javascript:insere_texte_dans_ckeditor(Base64.decode(document.getElementById('textarea_selection_archives').value))\" title='Insérer le contenu de la sélection effectuée dans les archives'><img src='../images/icons/paste_A.png' width='16' height='16' alt='Insérer sélection archives' /></a>";
}

echo "</legend>\n";

echo "<div id=\"dupplication_notice\" style='display: none;'>oulalala</div>\n";
echo "<div id=\"deplacement_notice\" style='display: none;'>oulalala</div>\n";
echo "<form enctype=\"multipart/form-data\" name=\"modification_compte_rendu\" id=\"modification_compte_rendu_form\" action=\"ajax_enregistrement_notice_privee.php\" method=\"post\" onsubmit=\"return AIM.submit(this, {'onComplete' : completeEnregistrementNoticePriveeCallback})\" style=\"width: 100%;\">\n";
// uid de pour ne pas refaire renvoyer plusieurs fois le meme formulaire
// autoriser la validation de formulaire $uid_post==$_SESSION['uid_prime']
echo add_token_field();
$uid = md5(uniqid(microtime(), 1));
echo("<input type='hidden' name='uid_post' value='".$uid."' />\n");
//hidden input utilise pour indiquer a la fenetre ListeNotice a quel endroit mettre un petit texte rouge "modification"
echo("<input type='hidden' id='div_id_ct' value='notice_privee_".$cahierTexteNoticePrivee->getIdCt()."' />\n");
echo("<input type='hidden' name='id_groupe' id='id_groupe' value='".$groupe->getId()."' />\n");
echo("<input type='hidden' name='heure_entry' value=\"");
if ($cahierTexteNoticePrivee->getHeureEntry() == null) {
	echo date('G:i');
} else {
	echo $cahierTexteNoticePrivee->getHeureEntry();
}
echo "\" />\n";

if (isset($info)) {
	$titre = "Informations Générales : ";
} elseif (!isset($info)) {
	$titre = french_strftime("%A %d %B %Y", $cahierTexteNoticePrivee->getDateCt());
}

//si on vient d'efftuer un enregistrement, le label du bonton enregistrer devient Succès
$label_enregistrer = "Enregistrer";
if ($succes_modification == 'oui') $label_enregistrer='Succès';
?>
<table border="0" width="100%" summary="Tableau de saisie de notice">
	<tr>
	<td style="width: 80%"><b><?php echo $titre; ?></b>&nbsp;
		<button type="submit" id="bouton_enregistrer_1" name="Enregistrer" 
			onclick="document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';"
			style='font-variant: small-caps;'><?php echo($label_enregistrer); ?></button>
		<img src='../images/spinner.gif' id='spinner_gif' class='icone16' style='display:none' />
		Ces notices ne sont visibles que de leur auteur.

		<?php

			//============================================================
			// Notice précédente/suivante
			if(preg_match("/^[0-9]{1,}$/", $cahierTexteNoticePrivee->getIdCt())) {
				//id_groupe: $cahierTexteNoticePrivee->getGroupe()->getId();
				//$cahierTexteNoticePrivee->getDateCt();
				$type_notice='p';
				$table_ct='ct_private_entry';

				// Lien séance précédente/suivante

				// Notice précédente:
				echo "
					<div style='display:inline; width:20px; text-align:center; '>";

				// Problème lorsqu'on a deux heures dans la même journée (ajouter un test)
				$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$cahierTexteNoticePrivee->getGroupe()->getId()."' AND id_ct<'".$cahierTexteNoticePrivee->getIdCt()."' AND date_ct='".$cahierTexteNoticePrivee->getDateCt()."' ORDER BY id_ct DESC;";
				//echo "$sql<br />";
				$res_mult=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_mult)>0) {
					$lig_prec=mysqli_fetch_object($res_mult);
					echo "
						<a href=\"#\" onclick=\"javascript:
							getWinEditionNotice().setAjaxContent('ajax_edition_notice_privee.php?id_ct=".$lig_prec->id_ct."&today=0&id_groupe=".$cahierTexteNoticePrivee->getGroupe()->getId()."',
								{ onComplete:
									function(transport) {
										initWysiwyg();
									}
								}
							);
							object_en_cours_edition = 'notice_privee';
						\"
						 title=\"Afficher la notice privée du ".french_strftime("%A %d/%m/%Y", $lig_prec->date_ct)."\">
							<img src='../images/icons/back.png' class='icone16' alt='Séance précédente' />
						</a>";
				}
				else {
					$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$cahierTexteNoticePrivee->getGroupe()->getId()."' AND id_ct!='".$cahierTexteNoticePrivee->getIdCt()."' AND date_ct<'".$cahierTexteNoticePrivee->getDateCt()."' ORDER BY date_ct DESC limit 1;";
					//echo "$sql<br />";
					$res_prec=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res_prec)>0) {
						$lig_prec=mysqli_fetch_object($res_prec);

						$ts_seance_prec=$lig_prec->date_ct;

						echo "
						<a href=\"#\" onclick=\"javascript:
							getWinEditionNotice().setAjaxContent('ajax_edition_notice_privee.php?id_ct=".$lig_prec->id_ct."&today=0&id_groupe=".$cahierTexteNoticePrivee->getGroupe()->getId()."',
								{ onComplete:
									function(transport) {
										initWysiwyg();
									}
								}
							);
							object_en_cours_edition = 'notice_privee';
							updateCalendarWithUnixDate($ts_seance_prec);
							dateChanged(calendarInstanciation);
							setTimeout('initWysiwyg();', 1000);
						\"
						 title=\"Afficher la notice privée du ".french_strftime("%A %d/%m/%Y", $lig_prec->date_ct)."\">
							<img src='../images/icons/back.png' class='icone16' alt='Séance précédente' />
						</a>";
					}
				}

				echo "
					</div>

					<div style='display:inline; width:20px; text-align:center;' title=\"Passer à la notice précédente/suivante, sans (ré)enregistrer la notice courante.\">
						<img src=\"../images/icons/notices_CDT_privee.png\" class=\"icone16\" />
					</div>

					<div style='display:inline; width:20px; text-align:center; '>";

				// Notice suivante:
				$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$cahierTexteNoticePrivee->getGroupe()->getId()."' AND id_ct>'".$cahierTexteNoticePrivee->getIdCt()."' AND date_ct='".$cahierTexteNoticePrivee->getDateCt()."' ORDER BY id_ct ASC;";
				$res_mult=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_mult)>0) {
					$lig_suiv=mysqli_fetch_object($res_mult);
					echo "
						<a href=\"#\" onclick=\"javascript:
							getWinEditionNotice().setAjaxContent('ajax_edition_notice_privee.php?id_ct=".$lig_suiv->id_ct."&today=0&id_groupe=".$cahierTexteNoticePrivee->getGroupe()->getId()."',
								{ onComplete:
									function(transport) {
										initWysiwyg();
									}
								}
							);
							object_en_cours_edition = 'notice_privee';
						\"
						 title=\"Afficher la notice privée du ".french_strftime("%A %d/%m/%Y", $lig_suiv->date_ct)."\">
							<img src='../images/icons/forward.png' class='icone16' alt='Séance suivante' />
						</a>";
				}
				else {
					$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$cahierTexteNoticePrivee->getGroupe()->getId()."' AND id_ct!='".$cahierTexteNoticePrivee->getIdCt()."' AND date_ct>'".$cahierTexteNoticePrivee->getDateCt()."' ORDER BY date_ct ASC limit 1;";
					$res_suiv=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res_suiv)>0) {
						$lig_suiv=mysqli_fetch_object($res_suiv);

						$ts_seance_suiv=$lig_prec->date_ct;

						echo "
						<a href=\"#\" onclick=\"javascript:
							getWinEditionNotice().setAjaxContent('ajax_edition_notice_privee.php?id_ct=".$lig_suiv->id_ct."&today=0&id_groupe=".$cahierTexteNoticePrivee->getGroupe()->getId()."',
								{ onComplete:
									function(transport) {
										initWysiwyg();
									}
								}
							);
							object_en_cours_edition = 'notice_privee';
							updateCalendarWithUnixDate($ts_seance_suiv);
							dateChanged(calendarInstanciation);
							setTimeout('initWysiwyg();', 1000);
						\"
						 title=\"Afficher la notice privée du ".french_strftime("%A %d/%m/%Y", $lig_suiv->date_ct)."\">
							<img src='../images/icons/forward.png' class='icone16' alt='Séance suivante' />
						</a>";
					}
				}

				echo "
					</div>";
			}

			// 20200306
			// Tester si le ts est celui d'un jour non ouvré
			if(!in_array(strftime('%w', $cahierTexteNoticePrivee->getDateCt()), get_tab_id_jours_ouvres())) {
				echo " <span style='color:red' title=\"Le ".strftime("%A", $cahierTexteNoticePrivee->getDateCt())." ne fait pas partie des jours d'ouverture de l'établissement.\">Fermé</span>";
			}
			else {
				// Sinon, tester si c'est un jour de vacances
				$sql="SELECT id_classe FROM j_groupes_classes WHERE id_groupe='".$id_groupe."';";
				$res_clas_grp=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_clas_grp)) {
					$tab_ts_vacances=get_tab_jours_vacances();
					/*
					echo "<pre>";
					print_r($tab_ts_vacances);
					echo "</pre>";
					*/
					if(in_array(strftime('%Y%m%d', $cahierTexteNoticePrivee->getDateCt()), $tab_ts_vacances)) {
						echo " <span style='color:red' title='Ce jour est un jour férié ou un jour de vacances.'>Vacances</span>";
					}
				}
			}

			//============================================================
		?>

		<input type="hidden" name="date_ct" value="<?php echo $cahierTexteNoticePrivee->getDateCt(); ?>" />
		<input type="hidden" id="id_ct" name="id_ct" value="<?php echo $cahierTexteNoticePrivee->getIdCt(); ?>" />
		<input type="hidden" name="id_groupe" id="id_ct" value="<?php echo $groupe->getId(); ?>" />

		<input type='hidden' name='importer_notice' id='importer_notice' value='' />
		<input type='hidden' name='id_ct_a_importer' id='id_ct_a_importer' value='' />
		<button type='submit' id='affichage_import_notice' style='font-variant: small-caps; display:none; background-color:red;' onClick="javascript:$('importer_notice').value='y';">Importer la notice</button>

		<?php
			$tab_tag_type=get_tab_tag_cdt();

			if(preg_match("/^[0-9]{1,}$/", $cahierTexteNoticePrivee->getIdCt())) {
				$tab_tag_notice=get_tab_tag_notice($cahierTexteNoticePrivee->getIdCt(), 'p');

				/*
				echo "\$id_ct=".$cahierTexteNoticePrivee->getIdCt();
				echo "<pre>";
				print_r($tab_tag_notice);
				echo "</pre>";
				*/
			}

			if(isset($tab_tag_type["tag_notice_privee"])) {
				echo "<br />";
				foreach($tab_tag_type["tag_notice_privee"] as $id_tag => $tag_courant) {
					echo " <input type='checkbox' name='tag[]' id='tag_".$id_tag."' value='".$id_tag."'";
					$style_label="";
					if((isset($tab_tag_notice["id"]))&&(in_array($id_tag, $tab_tag_notice["id"]))) {
						echo " checked";
						$style_label=" style='font-weight:bold'";

						//20240111
						$style_commentaire='';
					}
					else {
						$style_commentaire='none';
					}
					// 20240111
					//echo " onchange=\"checkbox_change(this.id);\" /><label for='tag_".$id_tag."' id='texte_tag_".$id_tag."' title=\"Cocher la case si la séance comporte un ".$tag_courant['nom_tag'].".\"$style_label>".$tag_courant['nom_tag']."</label>";
					echo " onchange=\"checkbox_change(this.id); if(this.checked==true) {document.getElementById('tag_commentaire_".$id_tag."').style.display=''} else {document.getElementById('tag_commentaire_".$id_tag."').style.display='none'}\" /><label for='tag_".$id_tag."' id='texte_tag_".$id_tag."' title=\"Cocher la case si la séance comporte un ".$tag_courant['nom_tag'].".\nUn témoin apparaîtra dans l'interface élève pour attirer l'attention.\"$style_label>".$tag_courant['nom_tag']."</label>";
					echo "<input type='text' name='tag_commentaire[".$id_tag."]' id='tag_commentaire_".$id_tag."' value=\"".(isset($tab_tag_notice["commentaire"][$id_tag]) ? $tab_tag_notice["commentaire"][$id_tag] : '')."\" style='display: ".$style_commentaire."' size='8' />";
				}
			}
		?>

		</td>
	<td><?php
	if (!isset($info)) {
		//echo "\$today=$today<br />";
		$hier = $today - 3600*24;
		$demain = $today + 3600*24;

		$test_hier=get_timestamp_jour_precedent($today);
		if($test_hier) {$hier=$test_hier;}

		$test_demain=get_timestamp_jour_suivant($today);
		if($test_demain) {$demain=$test_demain;}

		$semaine_precedente= $today - 3600*24*7;
		$semaine_suivante= $today + 3600*24*7;
		echo "</td>\n";

		echo "<td>\n";
		echo "<a href=\"javascript:
					getWinCalendar().setLocation(0, GetWidth() - 245);
			\"><img src=\"../images/icons/date.png\" width='16' height='16' alt='Calendrier' /></a>\n";
		echo "</td>\n";

		echo "<td style='text-align:center; width: 16px;'>\n";
		echo "<a title=\"Aller à la semaine précédente\" href=\"#\" onclick='javascript:updateCalendarWithUnixDate($semaine_precedente);dateChanged(calendarInstanciation);'><img src='../images/icons/arrow-left-double.png' width='16' height='16' title='Aller à la semaine précédente' alt='Aller à la semaine précédente' /></a> ";
		echo "</td>\n";
		echo "<td style='text-align:center; width: 16px;'>\n";
		echo "<a title=\"Aller au jour précédent\" href=\"#\" onclick='javascript:updateCalendarWithUnixDate($hier);dateChanged(calendarInstanciation);'><img src='../images/icons/arrow-left.png' width='16' height='16' title='Aller au jour précédent' alt='Aller au jour précédent' /></a>\n";
		echo "</td>\n";
		echo "<td align='center'>";
		if(date("d/m/Y")==date("d/m/Y",$today)) {
			echo "Aujourd'hui";
		}
		else {
			echo jour_fr(date("D",$today),'majf2')." ".date("d/m",$today);
		}
		echo "</td>\n";
		echo "<td style='text-align:center; width: 16px;'>\n";
		echo "<a title=\"Aller au jour suivant\" href=\"#\" onclick='javascript:updateCalendarWithUnixDate($demain);dateChanged(calendarInstanciation);'><img src='../images/icons/arrow-right.png' width='16' height='16' title='Aller au jour suivant' alt='Aller au jour suivant' /></a>\n";
		echo "</td>\n";
		echo "<td style='text-align:center; width: 16px;'>\n";
		echo " <a title=\"Aller à la semaine suivante\" href=\"#\" onclick='javascript:updateCalendarWithUnixDate($semaine_suivante);dateChanged(calendarInstanciation);'><img src='../images/icons/arrow-right-double.png' width='16' height='16' title='Aller à la semaine suivante' alt='Aller à la semaine suivante' /></a>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "<tr>
					<td colspan='5'>";

	echo "<textarea name=\"contenu\" style=\"background-color: white;\" id=\"contenu\">".$cahierTexteNoticePrivee->getContenu()."</textarea>\n";

	if(getPref($_SESSION["login"], 'cdt2_car_spec_sous_textarea', "no")=="yes") {
		echo cdt2_affiche_car_spec_sous_textarea();
	}

	echo "</td>\n";
	echo "</tr>\n";
	?>
	
	<tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice;?>; background-color: <?php echo $couleur_cellule['p']; ?>;">
		<td colspan="2" style="text-align: center;">
			<button type="submit" id="bouton_enregistrer_2" name="Enregistrer"
				onclick="document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';"
				style='font-variant: small-caps;'><?php echo($label_enregistrer); ?></button>
				<img src='../images/spinner.gif' id='spinner_gif2' class='icone16' style='display:none' />
		</td>
	</tr>
</table>
</td>
</tr>
</table>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Il ne faut pas saisir en notice privée d'informations concernant des élèves en particulier.<br />
Comme ces notices ne sont pas rattachées à un élève en particulier, il n'est pas possible d'assurer simplement le droit d'accès des parents/élèves à leurs données si elles sont inscrites en Notices privées.</p>
<?php echo "</form>";
echo "</fieldset>";

if((isset($_GET['mettre_a_jour_cal']))&&($_GET['mettre_a_jour_cal']=='y')) {
echo "<script type='text/javascript'>
	object_en_cours_edition='notice_privee';
	updateCalendarWithUnixDate($today);
	dateChanged(calendarInstanciation);
</script>\n";
}

echo "<div id='div_tableau_eleves' style='display:none'>\n";
echo tableau_html_eleves_du_groupe($id_groupe, 3);
echo "</div>\n";

?>
