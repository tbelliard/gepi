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
//id du compte rendu
$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);
//si on vient d'enregistrer des modification, on va afficher un message de confirmation
$succes_modification = isset($_POST["succes_modification"]) ? $_POST["succes_modification"] :(isset($_GET["succes_modification"]) ? $_GET["succes_modification"] :NULL);
//si pas de compte rendu passé en paramètre, on récupère la date du jour pour se caler dessus, sinon on prendra la date du compte rendu
$today = isset($_POST["today"]) ? $_POST["today"] :(isset($_GET["today"]) ? $_GET["today"] :NULL);
$ajout_nouvelle_notice = isset($_POST["ajout_nouvelle_notice"]) ? $_POST["ajout_nouvelle_notice"] :(isset($_GET["ajout_nouvelle_notice"]) ? $_GET["ajout_nouvelle_notice"] :NULL);

$ctCompteRendu = CahierTexteCompteRenduPeer::retrieveByPK($id_ct);
if ($ctCompteRendu != null) {
	$groupe = $ctCompteRendu->getGroupe();
	$today = $ctCompteRendu->getDateCt();

	if ($groupe == null) {
		echo("Erreur enregistrement de devoir : Pas de groupe associé au compte-rendu");
		die;
	}

	// Vérification : est-ce que l'utilisateur a le droit de travailler sur ce groupe ?
	if (!$groupe->belongsTo($utilisateur)) {
		echo "Erreur edition de compte rendu : le groupe n'appartient pas au professeur";
		die();
	}

} else {
	//si pas de notice précisée, récupération du groupe dans la requete et recherche d'une notice pour la date précisée ou création d'une nouvelle notice
	$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
	$groupe = GroupePeer::retrieveByPK($id_groupe);
	if ($groupe == null) {
		echo("Erreur edition de compte rendu : pas de groupe spécifié");
		die;
	}

	// Vérification : est-ce que l'utilisateur a le droit de travailler sur ce groupe ?
	if (!$groupe->belongsTo($utilisateur)) {
		echo "Erreur edition de compte rendu : le groupe n'appartient pas au professeur";
		die();
	}

	if ($ajout_nouvelle_notice != "oui") {
		//on cherche si il y a une notice pour le groupe à la date précisée
		$criteria = new Criteria(CahierTexteCompteRenduPeer::DATABASE_NAME);
		$criteria->add(CahierTexteCompteRenduPeer::DATE_CT, $today, '=');
		$criteria->add(CahierTexteCompteRenduPeer::ID_LOGIN, $utilisateur->getLogin());
		$ctCompteRendus = $groupe->getCahierTexteCompteRendus($criteria);
		$ctCompteRendu = isset($ctCompteRendus[0]) ? $ctCompteRendus[0] : NULL;
	}

	if ($ctCompteRendu == null) {
		//pas de notices, on initialise un nouvel objet
		$ctCompteRendu = new CahierTexteCompteRendu();
		$ctCompteRendu->setIdGroupe($groupe->getId());
		$ctCompteRendu->setDateCt($today);
		$ctCompteRendu->setIdLogin($utilisateur->getLogin());
	}
}

// Vérification : est-ce que l'utilisateur a le droit de modifier cette entré ?
if((my_strtolower($ctCompteRendu->getIdLogin()) != my_strtolower($utilisateur->getLogin()))&&
(getSettingValue("cdt_autoriser_modif_multiprof")!="yes")) {
	echo("Erreur edition de compte rendu : vous n'avez pas le droit de modifier cette notice car elle appartient à un autre professeur.");
	die();
}

if ($ctCompteRendu->getVise() == 'y') {
	// interdire la modification d'un visa par le prof si c'est un visa
	echo("Erreur edition de compte rendu : Notices signée, edition impossible");
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

if(isset($_GET['change_visibilite'])) {
	check_token();

	//$ctDocument->setVisibleEleveParent(false);
	$id_ct=$_GET['id_ct'];
	$id_document=$_GET['id_document'];
	if(($id_ct!='')&&(preg_match("/^[0-9]*$/", $id_ct))&&($id_document!='')&&(preg_match("/^[0-9]*$/", $id_document))) {
		$sql="SELECT visible_eleve_parent FROM ct_documents WHERE id='$id_document' AND id_ct='$id_ct';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			if($lig->visible_eleve_parent=='0') {$visible_eleve_parent='1';} else {$visible_eleve_parent='0';}
			$sql="UPDATE ct_documents SET visible_eleve_parent='$visible_eleve_parent' WHERE id='$id_document' AND id_ct='$id_ct';";
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


// Initialisation du type de couleur (voir global.inc.php)
if ($ctCompteRendu->getDateCt() == null) {
	//CompteRendu information générale
	$info='yes';
	$type_couleur = "i";
} else {
	//CompteRendu normal
	$type_couleur = "c";
}

//on met le groupe dans le session, pour naviguer entre absence, cahier de texte et autres
$_SESSION['id_groupe_session'] = $ctCompteRendu->getIdGroupe();
$id_groupe=$ctCompteRendu->getIdGroupe();

//================================================
// Recherche du cours suivant mais pas dans la journée à partir du lendemain (pour donner du travail à faire)

// Pour pouvoir sauter les jours de vacances
$tab_jours_vacances=array();
foreach($groupe->getClasses() as $current_classe) {
	$id_premiere_classe=$current_classe->getId();
	$tab_jours_vacances=get_tab_jours_vacances($id_premiere_classe);
	break;
}

$date_ct_cours_suivant="";
$ts_date_ct_cours_suivant="";
$tab_jours=array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
//echo "today=$today<br />";
$jour_courant=french_strftime("%A", $today);
//echo "jour=".$jour_courant."<br />";
//$num_semaine=strftime("%V", $today);
$num_semaine=id_num_semaine($today);
//echo "num_sem=".$num_semaine."<br />";
$sql="SELECT * FROM edt_semaines WHERE num_edt_semaine='".$num_semaine."';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
$type_edt_semaine="";
if(mysqli_num_rows($res)>0) {
	$lig=mysqli_fetch_object($res);
	$type_edt_semaine=$lig->type_edt_semaine;
}
//echo "Type semaine : ".$type_edt_semaine."<br />";

// Ou boucler sur les 21 jours en faisant $ts+=3600*24;
$tab_creneau=get_tab_creneaux();
$ts_test=$today;
for($loop=1;$loop<21;$loop++) {
	$ts_test+=3600*24;
	if(!in_array(strftime("%Y%m%d", $ts_test), $tab_jours_vacances)) {
		$jour_test=french_strftime("%A", $ts_test);
		//$num_semaine_test=strftime("%V", $ts_test);
		$num_semaine_test=id_num_semaine($ts_test);

		$ajout_sql="";
		$sql="SELECT * FROM edt_semaines WHERE num_edt_semaine='".$num_semaine_test."';";
		//echo "$sql<br />";
		$res_sem=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_sem)>0) {
			$lig=mysqli_fetch_object($res_sem);
			$type_edt_semaine=$lig->type_edt_semaine;
			$ajout_sql=" AND (id_semaine='' OR id_semaine='0' OR id_semaine='".$type_edt_semaine."')";
		}

		$sql="SELECT ec.*, ecr.nom_definie_periode, ecr.heuredebut_definie_periode, ecr.heurefin_definie_periode FROM edt_cours ec, edt_creneaux ecr WHERE ec.id_groupe='$id_groupe' AND ec.jour_semaine='".$jour_test."'".$ajout_sql." AND ec.id_definie_periode=ecr.id_definie_periode ORDER BY heuredebut_definie_periode;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			// Le jour est trouvé

			$lig=mysqli_fetch_object($res);

			//echo "Cours suivant le ".french_strftime("%a %d/%m/%Y", $ts_test)." en ".$lig->nom_definie_periode." (".$lig->heuredebut_definie_periode." - ".$lig->heuredebut_definie_periode.")<br />";
			$date_ct_cours_suivant="Cours suivant le ".french_strftime("%a %d/%m/%Y", $ts_test)." en ".$lig->nom_definie_periode." (".$lig->heuredebut_definie_periode." - ".$lig->heuredebut_definie_periode.")";
			$ts_date_ct_cours_suivant=$ts_test;
			break;
		}

		// Chercher si une notice existe pour un groupe associé à la classe/groupe (3B et dédoublement en 3B_gr1, 3B_gr2)
		// Chercher si une notice existe hors EDT avant la date trouvée (par exemple, pour un remplacement)

	}
}
//================================================
// Cours suivant dans la journée... à chercher

//================================================
echo "<br />";
//================================================
//debug_var();
// **********************************************

// Affichage des différents groupes du professeur
//\$A($('id_groupe_colonne_gauche').options).find(function(option) { return option.selected; }).value is a javascript trick to get selected value.
echo "<div id=\"div_chaine_edition_notice\" style=\"display:inline;\"><img id=\"chaine_edition_notice\" onLoad=\"updateChaineIcones()\" HEIGHT=\"16\" WIDTH=\"16\" style=\"border: 0px; vertical-align : middle\" src=\"../images/blank.gif\"  alt=\"Lier\" title=\"Lier la liste avec la liste des notices\" /></div>&nbsp;\n";
echo ("<select id=\"id_groupe_colonne_droite\" onChange=\"javascript:
			updateListeNoticesChaine();
			id_groupe = (\$A($('id_groupe_colonne_droite').options).find(function(option) { return option.selected; }).value);
			getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?today=".$today."&id_groupe=' + id_groupe,
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
echo "</select>&nbsp;&nbsp;\n";
//fin affichage des groupes

//echo "<a href=\"javascript:alert(chaineActive)\">Test</a>";

// Editer les devoirs:
echo "<button style='background-color:".$color_fond_notices['t']."' onclick=\"javascript:
						getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_groupe='+ ".$groupe->getId()." + '&today='+getCalendarUnixDate(),{ onComplete:function(transport) {initWysiwyg();}});
						object_en_cours_edition = 'devoir';
					\">Editer les devoirs</button>\n";

// Editer les notices privees:
echo "<button style='background-color:".$color_fond_notices['p']."' onclick=\"javascript:
						getWinEditionNotice().setAjaxContent('./ajax_edition_notice_privee.php?id_groupe='+ ".$groupe->getId()." + '&today='+getCalendarUnixDate(),{ onComplete:function(transport) {initWysiwyg();}});
						object_en_cours_edition = 'notice_privee';
					\">Editer les notices priv&eacute;es</button>\n";

// Voir les notices privees:
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
						getWinDocsJoints().setAjaxContent('./documents_cdt.php?id_groupe=".$groupe->getId()."&entete=n&type_ct=c',{});
					\" title=\"".$tab_termes_CDT2['attribut_title_CDT2_PJ']."\">PJ</button>\n";


// 20220312
if(getSettingValue('url_latex2image')!='') {
	// insert into setting set name='url_latex2image', value='https://latex2image.joeraut.com/';
	echo "<a href='".getSettingValue('url_latex2image')."' target='_blank'><img src='../images/equation.png' class='icone16' /></a> ";
}

echo "<a href=\"javascript:insere_texte_dans_ckeditor(document.getElementById('div_tableau_eleves').innerHTML)\" title='Insérer un tableau de la liste des élèves dans le texte de la notice'><img src='../images/icons/tableau.png' width='16' height='16' alt='Insérer un tableau de la liste des élèves dans le texte de la notice' /></a>";

echo " <a href=\"#\" onclick=\"fen=window.open('../groupes/popup.php?id_groupe=".$groupe->getId()."&avec_icone_visu_ele=y','','width=400,height=400,menubar=yes,scrollbars=yes'); setTimeout('fen.focus()',500); return false;\" title='Afficher en popup la liste des élèves pour accéder aux fiches élèves (et vérifier par exemple les absences de tel élève,...)'><img src='../images/icons/ele_onglets.png' width='16' height='16' alt='Popup' /></a>";

if((isset($ctCompteRendu))&&($ctCompteRendu->getIdCt()!=null)) {
	echo " <a href='affiche_notice.php?id_ct=".$ctCompteRendu->getIdCt()."&type_notice=c' target='_blank' title=\"Afficher la notice dans un nouvel onglet.\"><img src='../images/icons/chercher.png' class='icone16' alt='Afficher' /></a>";
}

//================================
// 20210127
if(getSettingAOui('cdt_restreindre_notice_tels_eleves')) {
	// Il faudrait tester la période correspondant au groupe à la date donnée
	$sql="SHOW TABLES LIKE 'ct_restriction_notice';";
	$test=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)>0) {
		$tab_ele=array();
		$sql="SELECT DISTINCT e.* FROM eleves e, 
					j_eleves_groupes jeg 
				WHERE id_groupe='".$groupe->getId()."' AND
					(e.date_sortie>".strftime('%Y-%m-%d %H:%M:%S')." OR e.date_sortie IS NULL)
				ORDER BY e.nom, e.prenom;";
		$res_ele=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_ele)) {
			while($lig=mysqli_fetch_object($res_ele)) {
				$tab_ele[]=$lig;
			}
		}

		$tab_restr=array();
		$sql="SELECT * FROM ct_restriction_notice 
				WHERE id_ct='".$ctCompteRendu->getIdCt()."' AND
					type_ct='t';";
		$res_ele=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_ele)) {
			while($lig=mysqli_fetch_object($res_ele)) {
				$tab_restr[]=$lig;
			}
		}









	}
}
//================================

echo " <a href=\"#\" style=\"font-size: 11pt;\" title=\"Recharger la page. Cela peut être utile si le champ de saisie ne s'affiche pas.\" onclick=\"javascript:
				id_groupe = '".$id_groupe."';
				getWinDernieresNotices().hide();
				getWinListeNotices();
				new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$id_groupe."', {encoding: 'utf-8'});
				getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=".$id_groupe."&today='+getCalendarUnixDate(), { 
				    		encoding: 'utf-8',
				    		onComplete : 
				    		function() {
				    			initWysiwyg();
							}
						}
				);
				return false;
			\"><img src='../images/icons/actualiser.png' class='icone16' /></a>";


if(getSettingAOui('cdt_afficher_volume_docs_joints')) {
	$volume_cdt_groupe=volume_docs_joints($groupe->getId());
	if($volume_cdt_groupe!=0) {
		$volume_cdt_groupe_cr=volume_docs_joints($groupe->getId(), "compte_rendus");
		$volume_cdt_groupe_cr_h=volume_human($volume_cdt_groupe_cr);
		$volume_cdt_groupe_h=volume_human($volume_cdt_groupe);
		$info_volume=$volume_cdt_groupe_cr_h."/".$volume_cdt_groupe_h;
		//mb_strlen($info_volume)
		echo "<div style='float:right; width:10em; text-align:center; background: ".$color_fond_notices[$type_couleur].";' title=\"Les documents joints aux compte-rendus occupent $volume_cdt_groupe_cr_h sur un total de $volume_cdt_groupe_h pour l'enseignement de ".$groupe->getName()." ".$groupe->getDescriptionAvecClasses().".\">".$info_volume."</div>";
	}
}
echo "<br /><br />\n";

//debug_var();

// 20150415
$tempdir=get_user_temp_directory($_SESSION['login']);
if(file_exists("../temp/".$tempdir."/cdt_selection.txt")) {
	$chaine_selection_archives=ensure_utf8(file_get_contents("../temp/".$tempdir."/cdt_selection.txt"));
	echo "<textarea id='textarea_selection_archives' name='textarea_selection_archives' style='display:none;'>".base64_encode($chaine_selection_archives)."</textarea>";
}

// Nombre de notices pour ce jour :
$num_notice = NULL;

echo "<fieldset style=\"border: 1px solid grey; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto; background: ".$color_fond_notices[$type_couleur].";\">\n";
if (isset($info)) {
	echo "<legend style=\"border: 1px solid grey; background: ".$color_fond_notices[$type_couleur]."; font-variant: small-caps;\"> Informations générales - ".$groupe->getNameAvecClasses();
} else {
	echo "<legend style=\"border: 1px solid grey; background: ".$color_fond_notices[$type_couleur]."; font-variant: small-caps;\"> Compte rendu - ".$groupe->getNameAvecClasses();
}
if (!$ctCompteRendu->isNew() || isset($info)) {
	echo " - <b><font color=\"red\">Modification de la notice</font></b> - 
			<a href=\"#\" onclick=\"javascript:
				getWinEditionNotice().setAjaxContent('ajax_edition_compte_rendu.php?id_groupe=".$groupe->getId()."&today=".$ctCompteRendu->getDateCt()."&ajout_nouvelle_notice=oui',
					{ onComplete:
						function(transport) {
							getWinEditionNotice().updateWidth();
							initWysiwyg();
						}
					}
				);
				compte_rendu_en_cours_de_modification('aucun');
				return false;
			\">
			Ajouter une notice
			</a> - \n";
	echo "<a href=\"#\" onclick=\"javascript:
				new Ajax.Updater($('dupplication_notice'), 'ajax_affichage_duplication_notice.php?id_groupe=".$groupe->getId()."&type=CahierTexteCompteRendu&id_ct=".$ctCompteRendu->getIdCt()."',
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
			new Ajax.Updater($('deplacement_notice'), 'ajax_affichage_deplacement_notice.php?id_groupe=".$groupe->getId()."&type=CahierTexteCompteRendu&id_ct=".$ctCompteRendu->getIdCt()."',
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

echo "<div id=\"dupplication_notice\" style='display: none;'>oulalala</div>";
echo "<div id=\"deplacement_notice\" style='display: none;'>oulalala</div>";

echo "<form enctype=\"multipart/form-data\" name=\"modification_compte_rendu_form\" id=\"modification_compte_rendu_form\" action=\"ajax_enregistrement_compte_rendu.php\" method=\"post\" onsubmit=\"return AIM.submit(this, {'onComplete' : completeEnregistrementCompteRenduCallback})\" style=\"width: 100%;\">\n";
echo add_token_field();
// uid de pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post==$_SESSION['uid_prime']
$uid = md5(uniqid(microtime(), 1));
echo("<input type='hidden' name='uid_post' value='".$uid."' />");
//hidden input utilise pour indiquer a la fenetre ListeNotice a quel endroit mettre un petit texte rouge "modification"
echo("<input type='hidden' id='div_id_ct' value='compte_rendu_".$ctCompteRendu->getIdCt()."' />");
echo("<input type='hidden' id='id_groupe' name='id_groupe' value='".$groupe->getId()."' />");
echo("<input type='hidden' name='heure_entry' value=\"");
if ($ctCompteRendu->getHeureEntry() == null) {
	echo date('G:i');
} else {
	echo $ctCompteRendu->getHeureEntry();
}
echo "\" />\n";

if (isset($info)) {
	$titre = "Informations Générales : ";
} elseif (!isset($info)) {
	$titre = french_strftime("%A %d %B %Y", $ctCompteRendu->getDateCt());
}

//si on vient d'effectuer un enregistrement, le label du bouton enregistrer devient Succès

$label_enregistrer = "Enregistrer";
if ($succes_modification == 'oui') {$label_enregistrer='Succès';}
?>
<table border="0" width="100%" summary="Tableau de saisie de notice">
	<tr>
		<td style="width: 80%"><b><?php echo $titre; ?></b>&nbsp;
		<button type="submit" id="bouton_enregistrer_1" name="Enregistrer"
			onclick="document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';"
			style='font-variant: small-caps;'><?php echo($label_enregistrer); ?></button>
			
		<?php if (!isset($info)) { ?>
		<!--button type="submit" style='font-variant: small-caps;'
			onClick="javascript:$('passer_a').value = 'passer_devoir';">Enr. et
		passer aux devoirs du lendemain</button-->
		<?php
			if($date_ct_cours_suivant=="") {
				echo "
		<input type=\"submit\" style='font-variant: small-caps;'
			onClick=\"javascript:$('passer_a').value = 'passer_devoir';document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';\" value=\"Enr. et passer aux devoirs du lendemain\" />";
			}
			else {
				$cdt2_afficher_passer_au_cours_suivant=getPref($_SESSION['login'], 'cdt2_afficher_passer_au_cours_suivant', "");

				if(($cdt2_afficher_passer_au_cours_suivant=="")||($cdt2_afficher_passer_au_cours_suivant=="les_2")) {
					echo "
		<input type=\"submit\" style='font-variant: small-caps;'
			onClick=\"javascript:$('passer_a').value = 'passer_devoir';document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';\" value=\"Enr. et passer aux&#13;&#10;devoirs du lendemain\" />";

					echo "
		<input type='submit' style='font-variant: small-caps;'
			onClick=\"javascript:$('passer_a').value = 'passer_devoir2';document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';\" value=\"Enr. et passer aux&#13;&#10;devoirs du cours suivant\" title=\"$date_ct_cours_suivant

Vous pouvez choisir dans 'Gérer mon compte' quel(s) bouton(s) vous souhaitez faire apparaitre ici.\" />";
				}
				elseif($cdt2_afficher_passer_au_cours_suivant=="cours_suivant") {
					echo "
		<input type='submit' style='font-variant: small-caps;'
			onClick=\"javascript:$('passer_a').value = 'passer_devoir2';document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';\" value=\"Enr. et passer aux&#13;&#10;devoirs du cours suivant\" title=\"$date_ct_cours_suivant\" />";
				}
				else {
					echo "
		<input type=\"submit\" style='font-variant: small-caps;'
			onClick=\"javascript:$('passer_a').value = 'passer_devoir';document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';\" value=\"Enr. et passer aux devoirs du lendemain\" />";
				}
			}
		}
		?>

		<img src='../images/spinner.gif' id='spinner_gif' class='icone16' style='display:none' />

		<?php
			$sql="SELECT * FROM ct_devoirs_entry WHERE id_groupe='$id_groupe' AND date_ct='".$ctCompteRendu->getDateCt()."';";
			//echo "$sql<br />";
			$res_devoirs=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_devoirs)==1) {
				$lig_dev=mysqli_fetch_object($res_devoirs);
				echo "<button type='submit' style='font-variant: small-caps;'
			onClick=\"javascript:$('get_devoirs_du_jour').value='y';\">Import trav.</button>";
			}

			//============================================================
			// Notice précédente/suivante
			if(preg_match("/^[0-9]{1,}$/", $ctCompteRendu->getIdCt())) {
				//id_groupe: $ctCompteRendu->getGroupe()->getId();
				//$ctCompteRendu->getDateCt();
				$type_notice='c';
				$table_ct='ct_entry';

				// Lien séance précédente/suivante

				// Notice précédente:
				echo "
					<div style='display:inline; width:20px; text-align:center; '>";

				// Problème lorsqu'on a deux heures dans la même journée (ajouter un test)
				$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$ctCompteRendu->getGroupe()->getId()."' AND id_ct<'".$ctCompteRendu->getIdCt()."' AND date_ct='".$ctCompteRendu->getDateCt()."' ORDER BY id_ct DESC;";
				//echo "$sql<br />";
				$res_mult=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_mult)>0) {
					$lig_prec=mysqli_fetch_object($res_mult);

					echo "
						<a href=\"#\" onclick=\"javascript:
							getWinEditionNotice().setAjaxContent('ajax_edition_compte_rendu.php?id_ct=".$lig_prec->id_ct."&today=0&id_groupe=".$ctCompteRendu->getGroupe()->getId()."',
								{ onComplete:
									function(transport) {
										initWysiwyg();
									}
								}
							);
							object_en_cours_edition = 'compte_rendu';
						\"
						 title=\"Afficher la notice de la séance du ".french_strftime("%A %d/%m/%Y", $lig_prec->date_ct)."\">
							<img src='../images/icons/back.png' class='icone16' alt='Séance précédente' />
						</a>";
				}
				else {
					$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$ctCompteRendu->getGroupe()->getId()."' AND id_ct!='".$ctCompteRendu->getIdCt()."' AND date_ct<'".$ctCompteRendu->getDateCt()."' ORDER BY date_ct DESC limit 1;";
					//echo "$sql<br />";
					$res_prec=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res_prec)>0) {
						$lig_prec=mysqli_fetch_object($res_prec);

						$ts_seance_prec=$lig_prec->date_ct;

						echo "
						<a href=\"#\" onclick=\"javascript:
							getWinEditionNotice().setAjaxContent('ajax_edition_compte_rendu.php?id_ct=".$lig_prec->id_ct."&today=0&id_groupe=".$ctCompteRendu->getGroupe()->getId()."',
								{ onComplete:
									function(transport) {
										initWysiwyg();
									}
								}
							);
							object_en_cours_edition = 'compte_rendu';
							updateCalendarWithUnixDate($ts_seance_prec);
							dateChanged(calendarInstanciation);
							setTimeout('initWysiwyg();', 1000);
						\"
						 title=\"Afficher la notice de la séance du ".french_strftime("%A %d/%m/%Y", $lig_prec->date_ct)."\">
							<img src='../images/icons/back.png' class='icone16' alt='Séance précédente' />
						</a>";
					}
				}

				echo "
					</div>

					<div style='display:inline; width:20px; text-align:center;' title=\"Passer à la notice précédente/suivante, sans (ré)enregistrer la notice courante.\">
						<img src=\"../images/icons/notices_CDT_compte_rendu.png\" class=\"icone16\" />
					</div>

					<div style='display:inline; width:20px; text-align:center; '>";

				// Notice suivante:
				$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$ctCompteRendu->getGroupe()->getId()."' AND id_ct>'".$ctCompteRendu->getIdCt()."' AND date_ct='".$ctCompteRendu->getDateCt()."' ORDER BY id_ct ASC;";
				$res_mult=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_mult)>0) {
					$lig_suiv=mysqli_fetch_object($res_mult);
					echo "
						<a href=\"#\" onclick=\"javascript:
							getWinEditionNotice().setAjaxContent('ajax_edition_compte_rendu.php?id_ct=".$lig_suiv->id_ct."&today=0&id_groupe=".$ctCompteRendu->getGroupe()->getId()."',
								{ onComplete:
									function(transport) {
										initWysiwyg();
									}
								}
							);
							object_en_cours_edition = 'compte_rendu';
						\"
						 title=\"Afficher la notice de la séance du ".french_strftime("%A %d/%m/%Y", $lig_suiv->date_ct)."\">
							<img src='../images/icons/forward.png' class='icone16' alt='Séance suivante' />
						</a>";
				}
				else {
					$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$ctCompteRendu->getGroupe()->getId()."' AND id_ct!='".$ctCompteRendu->getIdCt()."' AND date_ct>'".$ctCompteRendu->getDateCt()."' ORDER BY date_ct ASC limit 1;";
					$res_suiv=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res_suiv)>0) {
						$lig_suiv=mysqli_fetch_object($res_suiv);

						$ts_seance_suiv=$lig_suiv->date_ct;

						echo "
						<a href=\"#\" onclick=\"javascript:
							getWinEditionNotice().setAjaxContent('ajax_edition_compte_rendu.php?id_ct=".$lig_suiv->id_ct."&today=0&id_groupe=".$ctCompteRendu->getGroupe()->getId()."',
								{ onComplete:
									function(transport) {
										initWysiwyg();
									}
								}
							);
							object_en_cours_edition = 'compte_rendu';
							updateCalendarWithUnixDate($ts_seance_suiv);
							dateChanged(calendarInstanciation);
							setTimeout('initWysiwyg();', 1000);
						\"
						 title=\"Afficher la notice de la séance du ".french_strftime("%A %d/%m/%Y", $lig_suiv->date_ct)."\">
							<img src='../images/icons/forward.png' class='icone16' alt='Séance suivante' />
						</a>";
					}
				}

				echo "
					</div>";
			}
			//============================================================

			// 20200306
			// Tester si le ts est celui d'un jour non ouvré
			//echo "Test ".strftime('%w', $ctCompteRendu->getDateCt())." ".strftime('%A', $ctCompteRendu->getDateCt());
			if(!in_array(strftime('%w', $ctCompteRendu->getDateCt()), get_tab_id_jours_ouvres())) {
				echo " <span style='color:red' title=\"Le ".strftime("%A", $ctCompteRendu->getDateCt())." ne fait pas partie des jours d'ouverture de l'établissement.\">Fermé</span>";
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
					if(in_array(strftime('%Y%m%d', $ctCompteRendu->getDateCt()), $tab_ts_vacances)) {
						echo " <span style='color:red' title='Ce jour est un jour férié ou un jour de vacances.'>Vacances</span>";
					}
				}
			}

		?>
		<input type='hidden' name='get_devoirs_du_jour' id='get_devoirs_du_jour' value='' />

		<input type='hidden' id='passer_a' name='passer_a' value='compte_rendu' /> 
		<input type="hidden" name="date_ct" value="<?php echo $ctCompteRendu->getDateCt(); ?>" /> 
		<input type="hidden" name="date_ct_cours_suivant" id="date_ct_cours_suivant" value="<?php echo $ts_date_ct_cours_suivant; ?>" /> 
		<input type="hidden" id="id_ct" name="id_ct" value="<?php echo $ctCompteRendu->getIdCt(); ?>" /> 
		<input type="hidden" name="id_groupe" value="<?php echo $groupe->getId(); ?>" />

		<?php
			$tab_tag_type=get_tab_tag_cdt();

			if(preg_match("/^[0-9]{1,}$/", $ctCompteRendu->getIdCt())) {
				$tab_tag_notice=get_tab_tag_notice($ctCompteRendu->getIdCt(), 'c');

				/*
				echo "\$id_ct=".$ctCompteRendu->getIdCt();
				echo "<pre>";
				print_r($tab_tag_notice);
				echo "</pre>";
				*/
			}

			if(isset($tab_tag_type["tag_compte_rendu"])) {
				echo "<br />";

				/*
				echo "<pre>";
				print_r($tab_tag_notice);
				echo "</pre>";
				*/

				//$cpt_tag=0;
				foreach($tab_tag_type["tag_compte_rendu"] as $id_tag => $tag_courant) {
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
					//echo " onchange=\"checkbox_change(this.id);\" /><label for='tag_".$id_tag."' id='texte_tag_".$id_tag."' title=\"Cocher la case si la séance comporte un ".$tag_courant['nom_tag'].".\nUn témoin apparaîtra dans l'interface élève pour attirer l'attention.\"$style_label>".$tag_courant['nom_tag']."</label>";
					echo " onchange=\"checkbox_change(this.id); if(this.checked==true) {document.getElementById('tag_commentaire_".$id_tag."').style.display=''} else {document.getElementById('tag_commentaire_".$id_tag."').style.display='none'}\" /><label for='tag_".$id_tag."' id='texte_tag_".$id_tag."' title=\"Cocher la case si la séance comporte un ".$tag_courant['nom_tag'].".\nUn témoin apparaîtra dans l'interface élève pour attirer l'attention.\"$style_label>".$tag_courant['nom_tag']."</label>";
					echo "<input type='text' name='tag_commentaire[".$id_tag."]' id='tag_commentaire_".$id_tag."' value=\"".(isset($tab_tag_notice["commentaire"][$id_tag]) ? $tab_tag_notice["commentaire"][$id_tag] : '')."\" style='display: ".$style_commentaire."' size='8' />";
					//$cpt_tag++;
				}
			}
		?>

		</td>
		<td><?php
		if (!isset($info)) {

			$hier = $today - 3600*24;
			$demain = $today + 3600*24;

			$test_hier=get_timestamp_jour_precedent($today);
			if($test_hier) {$hier=$test_hier;}

			$test_demain=get_timestamp_jour_suivant($today);
			if($test_demain) {$demain=$test_demain;}

			// Semaine precedente et suivante
			$semaine_precedente= $today - 3600*24*7;
			$semaine_suivante= $today + 3600*24*7;
			
			//$date_du_jour=getdate();
			//$date_de_la_notice=getdate($today);
			echo "</td>\n";

			echo "<td>\n";
			echo "<a href=\"javascript:
						getWinCalendar().setLocation(0, GetWidth() - 245);
				\" 
				title=\"Afficher le calendrier.\"><img src=\"../images/icons/date.png\" width='16' height='16' alt='Calendrier' /></a>\n";
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
		echo "<tr>
				<td colspan='6'>";

		echo "<textarea name=\"contenu\" style=\"background-color: white;\" id=\"contenu\">".$ctCompteRendu->getContenu()."</textarea>";

		if(getPref($_SESSION["login"], 'cdt2_car_spec_sous_textarea', "no")=="yes") {
			echo cdt2_affiche_car_spec_sous_textarea();
		}

		// gestion des fichiers attaché
		echo '<div style="border-style:solid; border-width:1px; border-color: '.$couleur_bord_tableau_notice.'; background-color: '.$couleur_cellule[$type_couleur].';  padding: 2px; margin: 2px;">';
		echo "<b>Fichier(s) attaché(s) : </b><br />";
		echo '<div id="div_fichier">';
		$architecture= "/documents/cl_dev".$groupe->getId();
		// Affichage des documents joints
		//$document = new CtDocument(); //for ide completion
		$documents = $ctCompteRendu->getCahierTexteCompteRenduFichierJoints();
		echo "<table style=\"border-style:solid; border-width:0px; border-color: ".$couleur_bord_tableau_notice."; background-color: #000000; width: 100%\" cellspacing=\"1\" summary=\"Tableau des documents joints\" class='sortable resizable'>\n";
		if (!empty($documents)) {
			$nb_documents_joints=0;
			foreach ($documents as $document) {
				$nb_documents_joints++;
			}
			if($nb_documents_joints>0) {
				echo "<thead>
		<tr style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: $couleur_entete_fond[$type_couleur];\">
			<th class='text' style=\"text-align: center;\"><b>Titre</b></th>
			<th class='number' style=\"text-align: center; width: 100px\"><b>Taille en Ko</b></th>
			<th class='nosort' style=\"text-align: center; width: 100px\">Visibilité</th>
			<th class='nosort' style=\"text-align: center;\">
				<a href='javascript:cocher_decocher_suppr_doc_joint()' title='Cocher/décocher (inverser la sélection)'>Supprimer</a>
			</th>
		</tr>
	</thead>\n";
			}

			$nb_documents_joints=0;
			foreach ($documents as $document) {
				//if ($ic=='1') { $ic='2'; $couleur_cellule_=$couleur_cellule[$type_couleur]; } else { $couleur_cellule_=$couleur_cellule_alt[$type_couleur]; $ic='1'; }
				echo "<tr style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: #FFFFFF;\" onmouseOver=\"this.style.backgroundColor='".$couleur_cellule[$type_couleur]."';\" onmouseOut=\"this.style.backgroundColor='white';\">
						<td>\n";

				if(preg_match("/(png|gif|jpg|jpeg|webp)$/i",$document->getEmplacement())) {
					echo insere_lien_insertion_lien_document_dans_ckeditor($document->getTitre(), $document->getEmplacement());
					echo "";
					echo insere_lien_insertion_image_dans_ckeditor($document->getEmplacement());
				}
				elseif(preg_match("/ggb$/i",$document->getEmplacement())) {
					echo insere_lien_insertion_lien_document_dans_ckeditor($document->getTitre(), $document->getEmplacement());
					echo "";
					echo insere_lien_insertion_lien_geogebra_dans_ckeditor($document->getTitre(), $document->getEmplacement());
				}
				elseif(preg_match("/xml$/i",$document->getEmplacement())) {
					echo insere_lien_insertion_lien_document_dans_ckeditor($document->getTitre(), $document->getEmplacement());
					echo "";
					echo insere_lien_insertion_lien_instrumenpoche_dans_ckeditor($document->getTitre(), $document->getEmplacement());
				}
				//elseif(preg_match("/doc$|docx$|xls$|xlsx$|odt$|ods$|pdf$|txt$/i",$document->getEmplacement())) {
				else {
					echo insere_lien_insertion_lien_document_dans_ckeditor($document->getTitre(), $document->getEmplacement());
				}
				
				echo "
							<a href='".$document->getEmplacement()."' target=\"_blank\">".$document->getTitre()."</a>
						</td>
						<td style=\"text-align: center;\" title=\"Taille du fichier\">".round($document->getTaille()/1024,1)."</td>\n";
				if(getSettingValue('cdt_possibilite_masquer_pj')=='y') {
					//echo "<td style=\"text-align: center;\" id='td_document_joint_".$document->getId()."'>";
					echo "<td style=\"text-align: center;\">";
					//echo "<a href='#' onclick='modif_visibilite_doc_joint(".$document->getId().");return false;'>";
					echo "<a href='javascript:modif_visibilite_doc_joint(\"compte_rendu\", ".$ctCompteRendu->getIdCt().", ".$document->getId().")'>";
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
				echo "<td style=\"text-align: center;\">
					<div style='float: right; width:16px; margin-right:5px;' title=\"Supprimer ce fichier lors de l'enregistrement de la notice.\">
						<input type='checkbox' name='suppr_doc_joint[]' id='suppr_doc_joint_".$document->getId()."' value='".$document->getId()."' />
					</div>
					<a href='#' onclick=\"javascript:suppressionDocument('suppression du document joint ".$document->getTitre()." ?', '".$document->getId()."', '".$ctCompteRendu->getIdCt()."','".add_token_in_js_func()."')\" title=\"Supprimer ce document, sans enregistrer les modifications éventuelles de la notice ci-dessus.\">Supprimer</a>
				</td></tr>\n";
				$nb_documents_joints++;
			}
			echo "</table>\n";

			//gestion de modification du nom d'un documents
			if($nb_documents_joints>0) {
				echo "Nouveau nom <input type=\"text\" name=\"doc_name_modif\" size=\"25\" /> pour\n";
				echo "<select name=\"id_document\">\n";
				echo "<option value='-1'>(choisissez)</option>\n";
				foreach ($documents as $document) {
					echo "<option value='".$document->getId()."'>".$document->getTitre()."</option>\n";
				}
				echo "</select>\n";
				echo "<br /><br />";
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
			// 20210126
			if(getSettingAOui('cdt2_input_file_multiple')) {
			?>
			<tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice; ?>; background-color: <?php echo $couleur_cellule[$type_couleur]; ?>;" title="La première ligne permet de sélectionner plusieurs fichiers d'un coup. Ne pas abuser en tentant d'uploader un trop grand nombre de fichiers ou un trop grand volume, sous peine d'échec.">
				<td style="text-align: center; color:darkorange;">Sélectionner plusieurs fichiers d'un coup</td>
				<td style="text-align: center;"><input type="file" name="doc_file2[]" 	size="20" multiple="oui" style='border:1px solid darkorange;' /></td>
				<?php
					if(getSettingValue('cdt_possibilite_masquer_pj')=='y') {
						echo "<td style='border-style: 1px solid $couleur_bord_tableau_notice; background-color: ".$couleur_cellule[$type_couleur]."; text-align: center; color:orange;' title=\"Si vous cochez la case, tous les fichiers sélectionnés dans le champ à gauche seront masqués.\">";
						echo "<input type='checkbox' name='doc_masque2' value='y'";
						echo "/>";
						echo "</td>\n";
					}
				?>
			</tr>
			<?php
			}

			// 20201117
			$nb_doc_choisi='3';
			// INSERT INTO setting SET name='cdt_nb_doc_joints', value='10';
			//saveSetting('cdt_nb_doc_joints', 10);
			if(preg_match('/[0-9]{1,}/', getSettingValue('cdt_nb_doc_joints'))) {
				$nb_doc_choisi=getSettingValue('cdt_nb_doc_joints');
			}
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
					onclick="document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';"
					style='font-variant: small-caps;'><?php echo($label_enregistrer); ?></button>
				<?php if (!isset($info)) { ?>
				<!--button type="submit" style='font-variant: small-caps;'
					onClick="javascript:$('passer_a').value = 'passer_devoir';">Enr. et
				passer aux devoirs du lendemain</button-->
				<?php 
					if($date_ct_cours_suivant=="") {
						echo "
				<input type=\"submit\" style='font-variant: small-caps;'
					onClick=\"javascript:$('passer_a').value = 'passer_devoir';document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';\" value=\"Enr. et passer aux devoirs du lendemain\" />";
					}
					else {
						$cdt2_afficher_passer_au_cours_suivant=getPref($_SESSION['login'], 'cdt2_afficher_passer_au_cours_suivant', "");

						if(($cdt2_afficher_passer_au_cours_suivant=="")||($cdt2_afficher_passer_au_cours_suivant=="les_2")) {
							echo "
				<input type=\"submit\" style='font-variant: small-caps;'
					onClick=\"javascript:$('passer_a').value = 'passer_devoir';document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';\" value=\"Enr. et passer aux devoirs du lendemain\" />";

							echo "
				<input type='submit' style='font-variant: small-caps;'
					onClick=\"javascript:$('passer_a').value = 'passer_devoir2';document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';\" value=\"Enr. et passer aux devoirs du cours suivant\" title=\"$date_ct_cours_suivant

Vous pouvez choisir dans 'Gérer mon compte' quel(s) bouton(s) vous souhaitez faire apparaitre ici.\" />";
						}
						elseif($cdt2_afficher_passer_au_cours_suivant=="cours_suivant") {
							echo "
				<input type='submit' style='font-variant: small-caps;'
					onClick=\"javascript:$('passer_a').value = 'passer_devoir2';document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';\" value=\"Enr. et passer aux devoirs du cours suivant\" title=\"$date_ct_cours_suivant\" />";
						}
						else {
							echo "
				<input type=\"submit\" style='font-variant: small-caps;'
					onClick=\"javascript:$('passer_a').value = 'passer_devoir';document.getElementById('spinner_gif').style.display='';document.getElementById('spinner_gif2').style.display='';\" value=\"Enr. et passer aux devoirs du lendemain\" />";
						}
					}
				}
				?>
				<img src='../images/spinner.gif' id='spinner_gif2' class='icone16' style='display:none' />
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
			<?php echo "</form>";
			echo "</fieldset>";

if((isset($_GET['mettre_a_jour_cal']))&&($_GET['mettre_a_jour_cal']=='y')) {
echo "<script type='text/javascript'>
	object_en_cours_edition = 'compte_rendu';
	updateCalendarWithUnixDate($today);
	dateChanged(calendarInstanciation);
</script>\n";
}
//echo "<a href=\"#\" onclick=\"javascript: document.getElementById('contenu').value=document.getElementById('contenu').value+'TRUC'; return false;\">CLIC</a>";
//echo "<a href=\"#\" onclick=\"javascript: document.getElementById('contenu').value='TRUC'; return false;\">CLIC</a>";
//echo "<a href=\"#\" onclick=\"javascript: alert(document.getElementById('contenu').value); return false;\">CLOC</a>";

echo "<div id='div_tableau_eleves' style='display:none'>\n";
echo tableau_html_eleves_du_groupe($id_groupe, 3);
echo "</div>\n";
/*
echo "<script type='text/javascript'>
	//alert('plip');
	initWysiwyg();
	//alert('plop');
</script>\n";
*/

/*
echo "<script type='text/javascript'>
	if(document.getElementById('cke_contenu')) {
		// OK
	}
	else {
		id_groupe = '".$id_groupe."';
		getWinDernieresNotices().hide();
		getWinListeNotices();
		new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$id_groupe."', {encoding: 'utf-8'});
		getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=".$id_groupe."&today='+getCalendarUnixDate(), { 
		    		encoding: 'utf-8',
		    		onComplete : 
		    		function() {
		    			initWysiwyg();
					}
				}
		);
	}
</script>\n";
*/

/*
echo "<script type='text/javascript'>
	setTimeout('check_presence_CKEDITOR_cr($id_groupe)', 2000);
</script>\n";
*/


?>
