<?php
/*
 *
 * Copyright 2009-2015 Josselin Jacquard
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
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) {$traite_anti_inject = "yes";}
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	//header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	//header("Location: ../logout.php?auto=1");
	die();
}

if (!checkAccess()) {
	//header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

$tab_termes_CDT2=get_texte_CDT2();

//récupération de la notice
$id_devoir = isset($_POST["id_devoir"]) ? $_POST["id_devoir"] :(isset($_GET["id_devoir"]) ? $_GET["id_devoir"] :NULL);
$succes = isset($_POST["succes"]) ? $_POST["succes"] :(isset($_GET["succes"]) ? $_GET["succes"] :NULL);
$today = isset($_POST["today"]) ? $_POST["today"] :(isset($_GET["today"]) ? $_GET["today"] :NULL);
$ajout_nouvelle_notice = isset($_POST["ajout_nouvelle_notice"]) ? $_POST["ajout_nouvelle_notice"] :(isset($_GET["ajout_nouvelle_notice"]) ? $_GET["ajout_nouvelle_notice"] : NULL);

$ctTravailAFaire = CahierTexteTravailAFairePeer::retrieveByPK($id_devoir);
if ($ctTravailAFaire != null) {
	$groupe = $ctTravailAFaire->getGroupe();
	$today = $ctTravailAFaire->getDateCt();

	if ($groupe == null) {
		echo("Erreur edition de devoir : Pas de groupe associé au devoir");
		die;
	}

	// Vérification : est-ce que l'utilisateur a le droit de travailler sur ce groupe ?
	if (!$groupe->belongsTo($utilisateur)) {
		echo "Erreur edition de devoir : le groupe n'appartient pas au professeur";
		die();
	}

} else {
	//si pas de notice précisé, récupération du groupe dans la requete et recherche d'une notice pour la date précisée ou création d'une nouvelle notice
	//pas de notices, on lance une création de notice
	$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
	$groupe = GroupePeer::retrieveByPK($id_groupe);
	if ($groupe == null) {
		echo("Erreur edition de devoir : pas de groupe spécifié");
		die;
	}

	// Vérification : est-ce que l'utilisateur a le droit de travailler sur ce groupe ?
	if (!$groupe->belongsTo($utilisateur)) {
		echo "Erreur edition de devoir : le groupe n'appartient pas au professeur";
		die();
	}

	if ($ajout_nouvelle_notice != "oui") {
		//on cherche si il y a une notice pour le groupe à la date précisée
		$criteria = new Criteria(CahierTexteTravailAFairePeer::DATABASE_NAME);
		$criteria->add(CahierTexteTravailAFairePeer::DATE_CT, $today, '=');
		$criteria->add(CahierTexteTravailAFairePeer::ID_LOGIN, $utilisateur->getLogin());
		$ctTravailAFaires = $groupe->getCahierTexteTravailAFaires($criteria);
		$ctTravailAFaire = isset($ctTravailAFaires[0]) ? $ctTravailAFaires[0] : NULL;
	}

	if ($ctTravailAFaire == null) {
		//pas de notices, on initialise un nouvel objet
		$ctTravailAFaire = new CahierTexteTravailAFaire();
		$ctTravailAFaire->setIdGroupe($groupe->getId());
		$ctTravailAFaire->setDateCt($today);
		$ctTravailAFaire->setIdLogin($utilisateur->getLogin());
	}
}

// Vérification : est-ce que l'utilisateur a le droit de modifier cette entré ?
if ((my_strtolower($ctTravailAFaire->getIdLogin()) != my_strtolower($utilisateur->getLogin()))&&
(getSettingValue("cdt_autoriser_modif_multiprof")!="yes")) {
	echo("Erreur edition de devoir : vous n'avez pas le droit de modifier cette notice car elle appartient à un autre professeur.");
	die();
}


if ($ctTravailAFaire->getVise() == 'y') {
	// interdire la modification d'un visa par le prof si c'est un visa
	echo("Erreur edition de devoir : Notice signée, edition impossible");
	die();
}

if(isset($_GET['change_visibilite'])) {
	check_token();

	//$ctDocument->setVisibleEleveParent(false);
	//$id_ct_devoir=$_GET['id_ct_devoir'];
	$id_ct_devoir=$id_devoir;
	$id_document=$_GET['id_document'];
	if(($id_ct_devoir!='')&&(preg_match("/^[0-9]*$/", $id_ct_devoir))&&($id_document!='')&&(preg_match("/^[0-9]*$/", $id_document))) {
		$sql="SELECT visible_eleve_parent FROM ct_devoirs_documents WHERE id='$id_document' AND id_ct_devoir='$id_ct_devoir';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			if($lig->visible_eleve_parent=='0') {$visible_eleve_parent='1';} else {$visible_eleve_parent='0';}
			$sql="UPDATE ct_devoirs_documents SET visible_eleve_parent='$visible_eleve_parent' WHERE id='$id_document' AND id_ct_devoir='$id_ct_devoir';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if($res) {
				if($visible_eleve_parent=='1') {
					echo "<img src='../images/icons/visible.png' width='19' height='16' alt='Document visible des élèves et responsables' title='Document visible des élèves et responsables' />";
				}
				else {
					echo "<img src='../images/icons/invisible.png' width='19' height='16' alt='Document invisible des élèves et responsables' title='Document invisible des élèves et responsables' />";
				}
			}
		}
	}
	die();
}

//on mets le groupe dans le session, pour naviguer entre absence, cahier de texte et autres
$_SESSION['id_groupe_session'] = $ctTravailAFaire->getIdGroupe();

// Initialisation du type de couleur (voir global.inc.php)
$type_couleur = "t";

// **********************************************
// Affichage des différents groupes du professeur
//\$A($('id_groupe_colonne_gauche').options).find(function(option) { return option.selected; }).value is a javascript trick to get selected value.
echo "<div id=\"div_chaine_edition_notice\" style=\"display:inline;\"><img id=\"chaine_edition_notice\" onLoad=\"updateChaineIcones()\" HEIGHT=\"16\" WIDTH=\"16\" style=\"border: 0px; vertical-align : middle\" src=\"../images/blank.gif\"  alt=\"Lier\" title=\"Lier la liste avec la liste des de notices\" /></div>&nbsp;";

echo "<div style='float:right; width: 150px; text-align: center;' title=\"".$tab_termes_CDT2['attribut_title_CDT2_Travaux_pour_ce_jour']."\">\n";
echo "Travaux pour ce jour en";
$classes=$groupe->getClasses();
foreach($classes as $classe){
	$id_classe=$classe->getId();
	$nomClasse=$classe->getNom();
	$nomCompletClasse=$classe->getNomComplet();
	/*
	echo " <button style='background-color:plum' onclick=\"javascript:
							getWinDevoirsDeLaClasse().setAjaxContent('./ajax_devoirs_classe.php?id_classe=$id_classe&today='+getCalendarUnixDate(),{ onComplete:function(transport) {initWysiwyg();}});
						\">$nomClasse</button>";
	*/
	echo " <button style='background-color:plum' onclick=\"javascript:
							getWinDevoirsDeLaClasse().setAjaxContent('./ajax_devoirs_classe.php?id_classe=$id_classe&today='+getCalendarUnixDate());
						\">$nomClasse</button>";
}
echo "</div>\n";

if(getSettingAOui('cdt_afficher_volume_docs_joints')) {
	$volume_cdt_groupe=volume_docs_joints($groupe->getId());
	if($volume_cdt_groupe!=0) {
		$volume_cdt_groupe_cr=volume_docs_joints($groupe->getId(), "devoirs");
		$volume_cdt_groupe_cr_h=volume_human($volume_cdt_groupe_cr);
		$volume_cdt_groupe_h=volume_human($volume_cdt_groupe);
		$info_volume=$volume_cdt_groupe_cr_h."/".$volume_cdt_groupe_h;
		//mb_strlen($info_volume)
		echo "<div style='float:right; width:10em; text-align:center; background: ".$color_fond_notices[$type_couleur].";' title=\"Les documents joints aux devoirs occupent $volume_cdt_groupe_cr_h sur un total de $volume_cdt_groupe_h pour l'enseignement de ".$groupe->getName()." ".$groupe->getDescriptionAvecClasses().".\">".$info_volume."</div>";
	}
}

echo ("<select id=\"id_groupe_colonne_droite\" onChange=\"javascript:
			updateListeNoticesChaine();
			id_groupe = (\$A($('id_groupe_colonne_droite').options).find(function(option) { return option.selected; }).value);
			getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?today=".$today."&id_groupe=' + id_groupe,
      			 { onComplete:function() {initWysiwyg();}}
      		);
			compte_rendu_en_cours_de_modification('aucun');
		\">");
echo "<option value='-1'>choisissez un groupe</option>\n";
foreach ($utilisateur->getGroupes() as $group_iter) {
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
echo "</select>&nbsp;&nbsp;";

echo "<button style='background-color:".$color_fond_notices['c']."' onclick=\"javascript:
						getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe='+ ".$groupe->getId()." + '&today='+getCalendarUnixDate(),{ onComplete:function(transport) {initWysiwyg();}});
						object_en_cours_edition = 'compte_rendu';
					\">Editer les comptes rendus</button>\n";
echo "<button style='background-color:".$color_fond_notices['p']."' onclick=\"javascript:
						getWinEditionNotice().setAjaxContent('./ajax_edition_notice_privee.php?id_groupe='+ ".$groupe->getId()." + '&today='+getCalendarUnixDate(),{ onComplete:function(transport) {initWysiwyg();}});
						object_en_cours_edition = 'notice_privee';
					\">Editer les notices priv&eacute;es</button>\n";
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

echo "<a href=\"javascript:insere_texte_dans_ckeditor(document.getElementById('div_tableau_eleves').innerHTML)\" title='Insérer un tableau de la liste des élèves dans le texte de la notice'><img src='../images/icons/tableau.png' width='16' height='16' alt='Insérer un tableau de la liste des élèves dans le texte de la notice' /></a>";

//echo "<br><br>\n";
echo "<br />\n";
// Retour aux notices d'aujourd'hui:
$timestamp_du_jour=mktime(0,0,0,date('n'),date('j'),date('Y'));
if($timestamp_du_jour!=$today) {
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button style='background-color:".$color_fond_notices['c']."' onclick=\"javascript:
							getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe='+ ".$groupe->getId()." + '&today='+$timestamp_du_jour,{ onComplete:function(transport) {initWysiwyg();updateCalendarWithUnixDate($timestamp_du_jour)}});
							object_en_cours_edition = 'compte_rendu';
						\">CR : Retour au ".date('d')."/".date('m')."</button>\n";
}
//echo "\$timestamp_du_jour=$timestamp_du_jour<br />";
//echo "\$today=$today<br />";
echo "<br />\n";
//==============================================

//fin affichage des groupes

// 20150415
$tempdir=get_user_temp_directory($_SESSION['login']);
if(file_exists("../temp/".$tempdir."/cdt_selection.txt")) {
	$chaine_selection_archives=ensure_utf8(file_get_contents("../temp/".$tempdir."/cdt_selection.txt"));
	echo "<textarea id='textarea_selection_archives' name='textarea_selection_archives' style='display:none;'>".base64_encode($chaine_selection_archives)."</textarea>";
}

echo "<fieldset style=\"border: 1px solid grey; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto; background: ".$color_fond_notices[$type_couleur].";\">\n";
echo "<legend style=\"border: 1px solid grey; background: ".$color_fond_notices[$type_couleur]."; font-variant: small-caps;\"> Travaux Personnels \n";

if (!$ctTravailAFaire->isNew()) {
	echo " - <b><font color=\"red\">Modification de la notice</font></b>\n";
	echo " - 
			<a href=\"#\" onclick=\"javascript:
				getWinEditionNotice().setAjaxContent('ajax_edition_devoir.php?id_groupe=".$groupe->getId()."&today=".$ctTravailAFaire->getDateCt()."&ajout_nouvelle_notice=oui',
					{ onComplete:
						function(transport) {
							getWinEditionNotice().updateWidth();
						}
					}
				);
				return false;
			\">
			Ajouter une notice
			</a> - \n";
	echo " - <a href=\"#\" onclick=\"javascript:
				$('dupplication_notice').show();
				new Ajax.Updater($('dupplication_notice'), 'ajax_affichage_duplication_notice.php?id_groupe=".$groupe->getId()."&type=CahierTexteTravailAFaire&id_ct=".$ctTravailAFaire->getIdCt()."',
					{ onComplete:
						function(transport) {
							calendarDuplicationInstanciation = Calendar.setup({
									flat         : 'calendar-duplication-container', // ID of the parent element
									daFormat     : '%s' ,   			   //date format
									weekNumbers  : false
							})
							calendarDuplicationInstanciation.setDate(calendarInstanciation.date);
							$('dupplication_notice').show();
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
			new Ajax.Updater($('deplacement_notice'), 'ajax_affichage_deplacement_notice.php?id_groupe=".$groupe->getId()."&type=CahierTexteTravailAFaire&id_ct=".$ctTravailAFaire->getIdCt()."',
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

//il faut échapper les single quote pour le contenu à importer
$ct_a_importer_class = isset($_SESSION['ct_a_importer']) ? get_class($_SESSION['ct_a_importer']) : '';
$id_ct_a_importer = isset($_SESSION['ct_a_importer']) ? $_SESSION['ct_a_importer']->getPrimaryKey() : '';
//pour le contenu à copier, on regarde d'abord si on a du contenu en javascript puis dans la session php
echo (" <a href=\"#\" onclick=\"javascript: /*ct_a_importer_class est globale*/
    if (window.ct_a_importer_class == undefined) {
        ct_a_importer_class='".$ct_a_importer_class."';
        id_ct_a_importer='".$id_ct_a_importer."';
    }
    var hiddenField1 = document.createElement('input');
    hiddenField1.setAttribute('type', 'hidden');
    hiddenField1.setAttribute('name', 'ct_a_importer_class');
    hiddenField1.setAttribute('value', ct_a_importer_class);
    $('modification_compte_rendu_form').appendChild(hiddenField1);
    var hiddenField2 = document.createElement('input');
    hiddenField2.setAttribute('type', 'hidden');
    hiddenField2.setAttribute('name', 'id_ct_a_importer');
    hiddenField2.setAttribute('value', id_ct_a_importer);
    $('modification_compte_rendu_form').appendChild(hiddenField2);
    $('contenu').value = CKEDITOR.instances['contenu'].getData();
    $('modification_compte_rendu_form').request({
        onComplete : function (transport) {updateWindows('');}
    });");
echo("\"><img style=\"border: 0px;\" src=\"../images/icons/copy-16-gold-trombone.png");
echo("\" alt=\"Coller\" title=\"Coller les fichiers joints\" /></a>\n");

// 20150415
if(isset($chaine_selection_archives)) {
	//echo "<a href=\"javascript:insere_texte_dans_ckeditor(atob(document.getElementById('textarea_selection_archives').value))\" title='Insérer le contenu de la sélection effectuée dans les archives'><img src='../images/icons/copy-16.png' width='16' height='16' alt='Insérer sélection archives' />A</a>";
	echo "<a href=\"javascript:insere_texte_dans_ckeditor(Base64.decode(document.getElementById('textarea_selection_archives').value))\" title='Insérer le contenu de la sélection effectuée dans les archives'><img src='../images/icons/paste_A.png' width='16' height='16' alt='Insérer sélection archives' /></a>";
}

echo "</legend>\n";

echo "<div id=\"dupplication_notice\" style='display: none;'></div>\n";
echo "<div id=\"deplacement_notice\" style='display: none;'>oulalala</div>\n";

//debug_var();

echo "<form enctype=\"multipart/form-data\" name=\"modification_compte_rendu_form\" id=\"modification_compte_rendu_form\" action=\"ajax_enregistrement_devoir.php\" method=\"post\" onsubmit=\"return AIM.submit(this, {'onComplete' : completeEnregistrementDevoirCallback})\" style=\"width: 100%;\">\n";
echo add_token_field();
// uid de pour ne pas refaire renvoyer plusieurs fois le meme formulaire
// autoriser la validation de formulaire $uid_post==$_SESSION['uid_prime']
$uid = md5(uniqid(microtime(), 1));
echo("<input type='hidden' name='uid_post' value='".$uid."' />\n");
echo("<input type='hidden' id='id_groupe' name='id_groupe' value='".$groupe->getId()."' />\n");

//hidden input utilise pour indiquer a la fenetre ListeNotice a quel endroit mettre un petit texte rouge "modification"
echo("<input type='hidden' id='div_id_ct' value='devoir_".$ctTravailAFaire->getIdCt()."' />\n");

//si on vient d'efftuer un enregistrement, le label du bonton enregistrer devient Succès
$succes_modification = isset($_POST["succes_modification"]) ? $_POST["succes_modification"] :(isset($_GET["succes_modification"]) ? $_GET["succes_modification"] :NULL);
$label_enregistrer = "Enregistrer";
if ($succes_modification == 'oui') {$label_enregistrer='Succès';}

//echo $ctTravailAFaire->getDateVisibiliteEleve();
if($ctTravailAFaire->getDateVisibiliteEleve()=='') {
	$heure_courante=strftime("%H:%M");
	$jour_courant=strftime("%d/%m/%Y");
}
else {
	$heure_courante=get_heure_2pt_minute_from_mysql_date($ctTravailAFaire->getDateVisibiliteEleve());
	$jour_courant=get_date_slash_from_mysql_date($ctTravailAFaire->getDateVisibiliteEleve());
}

?>
<table border="0" width="99%" summary="Tableau de saisie de notice">
	<tr>
		<td style="width: 80%"><b>Pour le <?php echo strftime("%A %d %B %Y", $ctTravailAFaire->getDateCt()); ?></b>&nbsp;
		<button type="submit" id="bouton_enregistrer_1" name="Enregistrer"
			style='font-variant: small-caps;'><?php echo($label_enregistrer); ?></button>
		<button type="submit" style='font-variant: small-caps;'
			onClick="javascript:$('passer_a').value = 'passer_compte_rendu';">Enr. et
		passer aux comptes rendus</button>

		<?php
/*
echo "<script type='text/javascript'>
	function verif_date_visibilite() {
		date_v=document.getElementById('jour_visibilite').value;
		tab=date_v.split('/');
		jour_v=tab[0];
		mois_v=tab[1];
		annee_v=tab[2];
		if(!checkdate(mois_v, jour_v, annee_v)) {
			alert(\"La date de visibilité saisie n'est pas valide.\");
		}
	}
</script>\n";
*/
			echo "<br />\n";
			echo "<span title='Vous pouvez modifier les dates et heure de visibilité avec les flèches Haut/Bas, PageUp/PageDown du clavier.'>Visibilité</span>&nbsp;:\n";
			echo " <input type='text' name='jour_visibilite' id='jour_visibilite' value='$jour_courant' size='7' onkeydown='clavier_date(this.id,event)' 
			onblur=\"date_v=document.getElementById('jour_visibilite').value;
				tab=date_v.split('/');
				jour_v=tab[0];
				mois_v=tab[1];
				annee_v=tab[2];
				if(!checkdate(mois_v, jour_v, annee_v)) {
					alert('La date de visibilité saisie n est pas valide.');
				}
			\" />\n";
		// onblur='verif_date_visibilite()' />\n";
			echo " à <input type='text' name='heure_visibilite' id='heure_visibilite' value='$heure_courante' size='3' onkeydown='clavier_heure(this.id,event)' 
			onblur=\"instant_v=document.getElementById('heure_visibilite').value;
				var exp=new RegExp('^[0-9]{1,2}:[0-9]{0,2}$','g');
				erreur='n';
				if (exp.test(instant_v)) {
					tab=instant_v.split(':');
					heure_v=eval(tab[0]);
					min_v=eval(tab[1]);

					if((heure_v<0)||(heure_v>=24)||(min_v<0)||(min_v>=60)) {erreur='y';}
				}
				else {
					erreur='y';
				}

				if(erreur=='y') {
					alert('L heure de visibilité saisie n est pas valide.');
				}
			\" />\n";
			// Les devoirs ne sont pas visibles par les élèves/responsables dans le futur au delà de getSettingValue('delai_devoirs')
			if($today>time()+getSettingValue('delai_devoirs')*24*3600) {
				$message_visibilite="Les devoirs à faire ne sont visibles des élèves que pour les ".getSettingValue('delai_devoirs')." jours suivant la date courante.";
				$message_visibilite.="\nCette notice ne sera donc visible qu'à compter du ".strftime("%d/%m/%Y",$today-getSettingValue('delai_devoirs')*24*3600);
				echo " <img src='../images/icons/ico_attention.png' width='22' height='19' alt=\"$message_visibilite\" title=\"$message_visibilite\" />\n";
			}

			if(isset($id_devoir)) {
				$sql="SELECT 1=1 FROM ct_devoirs_entry WHERE id_ct='$id_devoir' AND special='controle';";
				$res_special=mysqli_query($GLOBALS['mysqli'], $sql);
				if(mysqli_num_rows($res_special)>0) {
					$checked_controle=" checked";
					$style_controle=" style='font-weight:bold'";
				}
				else {
					$checked_controle="";
					$style_controle="";
				}
			}
			else {
				$checked_controle="";
				$style_controle="";
			}

			echo " <input type='checkbox' name='controle' id='controle' value='controle'$checked_controle onchange=\"checkbox_change(this.id);\" /><label for='controle' id='texte_controle' title=\"Cocher la case si la séance comportera un contrôle/évaluation.\nUn témoin apparaîtra dans l'interface élève pour attirer l'attention.\"$style_controle>Contrôle</label>";

		?>
		<input type='hidden' id='passer_a' name='passer_a'
			value='passer_devoir' /> <input type="hidden" name="date_devoir"
			value="<?php echo $ctTravailAFaire->getDateCt(); ?>" /> <input
			type="hidden" id="id_devoir" name="id_devoir"
			value="<?php echo $ctTravailAFaire->getIdCt(); ?>" /><input
			type="hidden" id="id_ct" name="id_devoir"
			value="<?php echo $ctTravailAFaire->getIdCt(); ?>" /> <input
			type="hidden" name="id_groupe"
			value="<?php echo $ctTravailAFaire->getGroupe()->getId(); ?>" /></td>
		<td><?php

		if (!isset($info)) {
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
			echo "\n";
		}
		?>

	<tr>
		<td colspan="5"><?php

		echo "<textarea name=\"contenu\" style=\"background-color: white;\" id=\"contenu\">".$ctTravailAFaire->getContenu()."</textarea>\n";

		if(getPref($_SESSION["login"], 'cdt2_car_spec_sous_textarea', "no")=="yes") {
			echo cdt2_affiche_car_spec_sous_textarea();
		}

		//// gestion des fichiers attaché
		echo '<div style="border-style:solid; border-width:1px; border-color: '.$couleur_bord_tableau_notice.'; background-color: '.$couleur_cellule[$type_couleur].';  padding: 2px; margin: 2px;">';
		echo "<b>Fichier(s) attaché(s) : </b><br />\n";
		echo '<div id="div_fichier">';
		// Affichage des documents joints
		$document = new CahierTexteTravailAFaireFichierJoint(); //for ide completion
		$documents = $ctTravailAFaire->getCahierTexteTravailAFaireFichierJoints();
		echo "<table style=\"border-style:solid; border-width:0px; border-color: ".$couleur_bord_tableau_notice."; background-color: #000000; width: 100%\" cellspacing=\"1\" summary=\"Tableau des documents joints\">\n";
		"<tr style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: $couleur_entete_fond[$type_couleur];\"><td style=\"text-align: center;\"><b>Titre</b></td><td style=\"text-align: center; width: 100px\"><b>Taille en Ko</b></td><td style=\"text-align: center; width: 100px\"></td></tr>\n";
		if (!empty($documents)) {
			$nb_documents_joints=0;
			foreach ($documents as $document) {
				//if ($ic=='1') { $ic='2'; $couleur_cellule_=$couleur_cellule[$type_couleur]; } else { $couleur_cellule_=$couleur_cellule_alt[$type_couleur]; $ic='1'; }
				//			$id_document[$i] = $document->getId();
				//			$titre_[$i] = $document->getTitre();
				//			$taille = round( $document->getTaille()/1024,1);
				//			$emplacement =  $document->getEmplacement();
				echo "<tr style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: #FFFFFF;\">
						<td>\n";

				if(preg_match("/(png|gif|jpg)$/i",$document->getEmplacement())) {
					echo insere_lien_insertion_image_dans_ckeditor($document->getEmplacement());
				}
				elseif(preg_match("/ggb$/i",$document->getEmplacement())) {
					echo insere_lien_insertion_lien_geogebra_dans_ckeditor($document->getTitre(), $document->getEmplacement());
				}

				echo "
							<a href='".$document->getEmplacement()."' target=\"_blank\">".$document->getTitre()."</a>
						</td>
						<td style=\"text-align: center;\" title=\"Taille du fichier\">".round($document->getTaille()/1024,1)."</td>\n";
						if(getSettingValue('cdt_possibilite_masquer_pj')=='y') {
							echo "<td style=\"text-align: center;\">";
							echo "<a href='javascript:modif_visibilite_doc_joint(\"devoir\", ".$ctTravailAFaire->getIdCt().", ".$document->getId().")'>";
							echo "<span id='span_document_joint_".$document->getId()."'>";
							if($document->getVisibleEleveParent()) {
								echo "<img src='../images/icons/visible.png' width='19' height='16' alt='Document visible des élèves et responsables' title='Document visible des élèves et responsables' />";
							}
							else {
								echo "<img src='../images/icons/invisible.png' width='19' height='16' alt='Document invisible des élèves et responsables' title='Document invisible des élèves et responsables' />";
							}
							echo "</span>";
							echo "</a>";
							echo "</td>\n";
						}
						echo "<td style=\"text-align: center;\"><a href='#' onclick=\"javascript:suppressionDevoirDocument('suppression du document joint ".$document->getTitre()." ?', '".$document->getId()."', '".$ctTravailAFaire->getIdCt()."', '".$ctTravailAFaire->getIdGroupe()."','".add_token_in_js_func()."')\">Supprimer</a></td></tr>\n";
				$nb_documents_joints++;
			}
			echo "</table>\n";
			//gestion de modification du nom d'un document

			if($nb_documents_joints>0) {
				echo "Nouveau nom <input type=\"text\" name=\"doc_name_modif\" size=\"25\" /> pour\n";
				echo "<select name=\"id_document\">\n";
				echo "<option value='-1'>(choisissez)</option>\n";
				foreach ($documents as $document) {
					echo "<option value='".$document->getId()."'>".$document->getTitre()."</option>\n";
				}
				echo "</select>\n";
				echo "<br /><br />\n";
			}
		}

		echo add_token_field(true);

		?>
		<table style="border-style:solid; border-width:0px; border-color: <?php echo $couleur_bord_tableau_notice;?> ; background-color: #000000; width: 100%" cellspacing="1" summary="Tableau de...">
			<tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice; ?>; background-color: <?php echo $couleur_entete_fond[$type_couleur]; ?>;">
				<td style="font-weight: bold; text-align: center; width: 20%">Titre
				(facultatif)</td>
				<td style="font-weight: bold; text-align: center; width: 60%">Emplacement</td>
				<?php
					if(getSettingValue('cdt_possibilite_masquer_pj')=='y') {
						$nb_col_tableau_pj=3;
						echo "<td style='font-weight: bold; text-align: center;'>Masquer</td>\n";
					}
					else {
						$nb_col_tableau_pj=2;
					}
				?>
			</tr>
			<?php
			$nb_doc_choisi='3';
			$nb_doc_choisi_compte='0';
			while($nb_doc_choisi_compte<$nb_doc_choisi) { ?>
			<tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice; ?>; background-color: <?php echo $couleur_cellule[$type_couleur]; ?>;">
				<td style="text-align: center;"><input type="text" name="doc_name[]"
					size="20" /></td>
				<td style="text-align: center;"><input type="file" name="doc_file[]"
					size="20" /></td>
				<?php
					if(getSettingValue('cdt_possibilite_masquer_pj')=='y') {
						echo "<td style='border-style: 1px solid $couleur_bord_tableau_notice; background-color: ".$couleur_cellule[$type_couleur]."; text-align: center;'>";
						echo "<input type='checkbox' name='doc_masque[]' value='y' ";
						echo "/>";
						echo "</td>\n";
					}
				?>
			</tr>
			<?php $nb_doc_choisi_compte++;
			}
			//fin gestion des fichiers attaché
			?>

			<tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice;?>; background-color: <?php echo $couleur_cellule[$type_couleur]; ?>;">
				<td colspan="<?php echo $nb_col_tableau_pj;?>" style="text-align: center;">
				<button type="submit" id="bouton_enregistrer_2" name="Enregistrer"
					style='font-variant: small-caps;'><?php echo($label_enregistrer); ?></button>
				<button type="submit" style='font-variant: small-caps;'
					onClick="javascript:$('passer_a').value = 'passer_compte_rendu';">Enr. et passer aux comptes rendus</button>
				</td>
			</tr>
			<tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice; ?>; background-color: <?php echo $couleur_entete_fond[$type_couleur]; ?>;">
				<td colspan="<?php echo $nb_col_tableau_pj;?>" style="text-align: center;"><?php  echo "Tous les documents ne sont pas acceptés, voir <a href='javascript:centrerpopup(\"limites_telechargement.php?id_groupe=" . $groupe->getId() . "\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")'>les limites et restrictions</a>\n"; ?>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<?php echo "</form>\n";
echo "</fieldset>\n";

$ouverture_auto_WinDevoirsDeLaClasse=getPref($_SESSION['login'], 'ouverture_auto_WinDevoirsDeLaClasse', 'y');
if($ouverture_auto_WinDevoirsDeLaClasse=='y') {
	echo "<script type='text/javascript'>
	getWinDevoirsDeLaClasse().setAjaxContent('./ajax_devoirs_classe.php?id_classe=$id_classe&today='+getCalendarUnixDate());
</script>\n";
}

if((isset($_GET['mettre_a_jour_cal']))&&($_GET['mettre_a_jour_cal']=='y')) {
echo "<script type='text/javascript'>
	object_en_cours_edition='devoir';
	updateCalendarWithUnixDate($today);
	dateChanged(calendarInstanciation);
</script>\n";
}

echo "<div id='div_tableau_eleves' style='display:none'>\n";
echo tableau_html_eleves_du_groupe($id_groupe, 3);
echo "</div>\n";

?>
