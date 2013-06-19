<?php
/*
* $Id$
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
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

//=====================================================
// Pour pouvoir enregistrer l'avis du conseil de classe:
// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';
//=====================================================

// Initialisations files
require_once("../lib/initialisations.inc.php");

/*
$chemin="/tmp/infos_session_graphe.txt";
$type="a+";
$fich=fopen($chemin,$type);
$chaine="\n".strftime("%Y%m%d %H%M%S").": Dans affiche_eleve.php on recupere pour ".$_SESSION['login']." connecté\n";
fwrite($fich,$chaine);
fclose($fich);
*/

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}


// Ajouter une gestion des droits par la suite
// dans la table MySQL appropriée et décommenter ce passage.
// INSERT INTO droits VALUES ('/visualisation/affiche_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '1');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$taille_max_police=10;

//$debug=1;
$debug=0;

function affiche_debug($texte) {
	global $debug;
	if($debug==1) {
		echo "$texte\n";
	}
}

$delais_apres_cloture=getSettingValue("delais_apres_cloture");

$gepi_denom_mention=getSettingValue("gepi_denom_mention");
if($gepi_denom_mention=="") {
	$gepi_denom_mention="mention";
}

/*
$datay1 = array();
$datay2 = array();
$etiquette = array();
$graph_title = "";
$v_legend1 = "";
$v_legend2 = "";
*/

// Initialisations sans lesquelles EasyPHP râle:
$seriemin="";
$seriemax="";
$seriemoy="";
$graph_title="Graphe";
$compteur=0;
//$mgen[1]="Non_calculee";
//$mgen[2]="Non_calculee";
$mgen[1]="";
$mgen[2]="";

//$periode=1;
//$temoin_imageps="";

//===================================
// Durée en millisecondes pendant laquelle la souris ne doit pas sortir d'un rectangle
// pour que l'affichage d'une appréciation en infobulle se fasse.
$duree_delais_afficher_div=500;
// Hauteur du rectangle pour le graphe en ligne-brisée
$hauteur_rect_delais_afficher_div=20;
// Pour opter pour le clic plutôt que le survol pour provoquer l'affichage d'une appréciation,
// passer la valeur à 'y'
$click_plutot_que_survol_aff_app="n";
//===================================

if(!isset($msg)) {
	$msg="";
}

if(isset($_POST['valider_raz_param'])) {
	$champ_aff=array('graphe_affiche_deroulant_appreciations',
'graphe_affiche_mgen',
'graphe_affiche_minmax',
'graphe_affiche_moy_annuelle',
'graphe_affiche_photo',
'graphe_champ_saisie_avis_fixe',
'graphe_click_plutot_que_survol_aff_app',
'graphe_epaisseur_traits',
'graphe_epaisseur_croissante_traits_periodes',
'graphe_hauteur_affichage_deroulant',
'graphe_app_deroulantes_taille_police',
'graphe_hauteur_graphe',
'graphe_largeur_graphe',
'graphe_largeur_imposee_photo',
'graphe_mode_graphe',
'graphe_taille_police',
'graphe_temoin_image_escalier',
'graphe_pointille',
'graphe_tronquer_nom_court',
'affiche_moy_classe',
'graphe_star_decalage_y',
'graphe_star_modif_rayon');
	for($loop=0;$loop<count($champ_aff);$loop++) {
		$sql="DELETE FROM preferences WHERE login='".$_SESSION['login']."' AND name='$champ_aff[$loop]';";
		$del=mysql_query($sql);
	}
}

// On permet au compte scolarité d'enregistrer les paramètres d'affichage du graphe
if($_SESSION['statut']=='scolarite') {
/*
affiche_photo
largeur_imposee_photo
affiche_mgen
affiche_minmax
affiche_moy_annuelle
largeur_graphe
hauteur_graphe
taille_police
epaisseur_traits
temoin_image_escalier
tronquer_nom_court

affiche_moy_classe
*/

	if(isset($_POST['save_params'])) {
		check_token();
		if($_POST['save_params']=="y") {

			function save_params_graphe($nom,$valeur) {
				global $msg;
				if(!saveSetting("$nom", $valeur)) {
					$msg.="Erreur lors de l'enregistrement du paramètre $nom<br />";
				}
			}

			//$erreur_save_params="";
			if(isset($_POST['affiche_photo'])) {save_params_graphe('graphe_affiche_photo',$_POST['affiche_photo']);}
			else{save_params_graphe('graphe_affiche_photo','non');}
			if(isset($_POST['largeur_imposee_photo'])) {save_params_graphe('graphe_largeur_imposee_photo',$_POST['largeur_imposee_photo']);}
			if(isset($_POST['affiche_mgen'])) {save_params_graphe('graphe_affiche_mgen',$_POST['affiche_mgen']);}
			else{save_params_graphe('graphe_affiche_mgen','non');}
			if(isset($_POST['affiche_minmax'])) {save_params_graphe('graphe_affiche_minmax',$_POST['affiche_minmax']);}
			else{save_params_graphe('graphe_affiche_minmax','non');}
			if(isset($_POST['affiche_moy_annuelle'])) {save_params_graphe('graphe_affiche_moy_annuelle',$_POST['affiche_moy_annuelle']);}
			else{save_params_graphe('graphe_affiche_moy_annuelle','non');}

			if(isset($_POST['type_graphe'])) {save_params_graphe('graphe_type_graphe',$_POST['type_graphe']);}

			if(isset($_POST['mode_graphe'])) {save_params_graphe('graphe_mode_graphe',$_POST['mode_graphe']);}

			if(isset($_POST['largeur_graphe'])) {save_params_graphe('graphe_largeur_graphe',$_POST['largeur_graphe']);}
			if(isset($_POST['hauteur_graphe'])) {save_params_graphe('graphe_hauteur_graphe',$_POST['hauteur_graphe']);}
			if(isset($_POST['taille_police'])) {save_params_graphe('graphe_taille_police',$_POST['taille_police']);}
			if(isset($_POST['epaisseur_traits'])) {save_params_graphe('graphe_epaisseur_traits',$_POST['epaisseur_traits']);}

			if(isset($_POST['epaisseur_croissante_traits_periodes'])) {save_params_graphe('graphe_epaisseur_croissante_traits_periodes',$_POST['epaisseur_croissante_traits_periodes']);}

			if(isset($_POST['temoin_image_escalier'])) {save_params_graphe('graphe_temoin_image_escalier',$_POST['temoin_image_escalier']);}
			else{save_params_graphe('graphe_temoin_image_escalier','non');}
			if(isset($_POST['tronquer_nom_court'])) {save_params_graphe('graphe_tronquer_nom_court',$_POST['tronquer_nom_court']);}

			if(isset($_POST['graphe_champ_saisie_avis_fixe'])) {save_params_graphe('graphe_champ_saisie_avis_fixe',$_POST['graphe_champ_saisie_avis_fixe']);}

			// Ajout Eric 11/12/10			
			if(isset($_POST['graphe_affiche_deroulant_appreciations'])){save_params_graphe('graphe_affiche_deroulant_appreciations',$_POST['graphe_affiche_deroulant_appreciations']);}
			if(isset($_POST['graphe_hauteur_affichage_deroulant'])) {save_params_graphe('graphe_hauteur_affichage_deroulant',$_POST['graphe_hauteur_affichage_deroulant']);}

			if(isset($_POST['graphe_app_deroulantes_taille_police'])) {save_params_graphe('graphe_app_deroulantes_taille_police',$_POST['graphe_app_deroulantes_taille_police']);}

			if(isset($_POST['click_plutot_que_survol_aff_app'])) {save_params_graphe('graphe_click_plutot_que_survol_aff_app',$_POST['click_plutot_que_survol_aff_app']);}

			if(isset($_POST['graphe_pointille'])) {save_params_graphe('graphe_pointille',$_POST['graphe_pointille']);}
			else{save_params_graphe('graphe_pointille','yes');}

			if(isset($_POST['affiche_moy_classe'])) {save_params_graphe('graphe_affiche_moy_classe',$_POST['affiche_moy_classe']);}
			else{save_params_graphe('graphe_affiche_moy_classe','oui');}


			if(isset($_POST['graphe_star_decalage_y'])) {save_params_graphe('graphe_star_decalage_y',$_POST['graphe_star_decalage_y']);}
			else{save_params_graphe('graphe_star_decalage_y',0);}

			if(isset($_POST['graphe_star_modif_rayon'])) {save_params_graphe('graphe_star_modif_rayon',$_POST['graphe_star_modif_rayon']);}
			else{save_params_graphe('graphe_star_modif_rayon',0);}

			if($msg=='') {
				$msg="Paramètres enregistrés.";
			}
		}
	}
}

if(isset($_POST['parametrage_affichage'])) {
	check_token();

	// Enregistrer les préférences

	if(isset($_POST['affiche_photo'])) {savePref($_SESSION['login'],'graphe_affiche_photo',$_POST['affiche_photo']);}
	else{savePref($_SESSION['login'],'graphe_affiche_photo','non');}
	if(isset($_POST['largeur_imposee_photo'])) {savePref($_SESSION['login'],'graphe_largeur_imposee_photo',$_POST['largeur_imposee_photo']);}

	if(isset($_POST['affiche_mgen'])) {savePref($_SESSION['login'],'graphe_affiche_mgen',$_POST['affiche_mgen']);}
	else{savePref($_SESSION['login'],'graphe_affiche_mgen','non');}

	if(isset($_POST['affiche_minmax'])) {savePref($_SESSION['login'],'graphe_affiche_minmax',$_POST['affiche_minmax']);}
	else{savePref($_SESSION['login'],'graphe_affiche_minmax','non');}
	if(isset($_POST['affiche_moy_annuelle'])) {savePref($_SESSION['login'],'graphe_affiche_moy_annuelle',$_POST['affiche_moy_annuelle']);}
	else{savePref($_SESSION['login'],'graphe_affiche_moy_annuelle','non');}

	//if(isset($_POST['type_graphe'])) {savePref($_SESSION['login'],'graphe_type_graphe',$_POST['type_graphe']);}

	if(isset($_POST['mode_graphe'])) {savePref($_SESSION['login'],'graphe_mode_graphe',$_POST['mode_graphe']);}

	if(isset($_POST['largeur_graphe'])) {savePref($_SESSION['login'],'graphe_largeur_graphe',$_POST['largeur_graphe']);}
	if(isset($_POST['hauteur_graphe'])) {savePref($_SESSION['login'],'graphe_hauteur_graphe',$_POST['hauteur_graphe']);}
	if(isset($_POST['taille_police'])) {savePref($_SESSION['login'],'graphe_taille_police',$_POST['taille_police']);}
	if(isset($_POST['epaisseur_traits'])) {savePref($_SESSION['login'],'graphe_epaisseur_traits',$_POST['epaisseur_traits']);}

	if(isset($_POST['epaisseur_croissante_traits_periodes'])) {savePref($_SESSION['login'],'graphe_epaisseur_croissante_traits_periodes',$_POST['epaisseur_croissante_traits_periodes']);}

	if(isset($_POST['temoin_image_escalier'])) {savePref($_SESSION['login'],'graphe_temoin_image_escalier',$_POST['temoin_image_escalier']);}
	else{savePref($_SESSION['login'],'graphe_temoin_image_escalier','non');}
	if(isset($_POST['tronquer_nom_court'])) {savePref($_SESSION['login'],'graphe_tronquer_nom_court',$_POST['tronquer_nom_court']);}

	if(isset($_POST['graphe_champ_saisie_avis_fixe'])) {savePref($_SESSION['login'],'graphe_champ_saisie_avis_fixe',$_POST['graphe_champ_saisie_avis_fixe']);}

	// Ajout Eric 11/12/10
	if(isset($_POST['graphe_affiche_deroulant_appreciations'])){savePref($_SESSION['login'],'graphe_affiche_deroulant_appreciations',$_POST['graphe_affiche_deroulant_appreciations']);}
	if(isset($_POST['graphe_hauteur_affichage_deroulant'])) {savePref($_SESSION['login'],'graphe_hauteur_affichage_deroulant',$_POST['graphe_hauteur_affichage_deroulant']);}

	if(isset($_POST['graphe_app_deroulantes_taille_police'])) {savePref($_SESSION['login'],'graphe_app_deroulantes_taille_police',$_POST['graphe_app_deroulantes_taille_police']);}

	if(isset($_POST['click_plutot_que_survol_aff_app'])) {savePref($_SESSION['login'],'graphe_click_plutot_que_survol_aff_app',$_POST['click_plutot_que_survol_aff_app']);}

	if(isset($_POST['graphe_pointille'])) {savePref($_SESSION['login'],'graphe_pointille',$_POST['graphe_pointille']);}

	if(isset($_POST['affiche_moy_classe'])) {savePref($_SESSION['login'],'graphe_affiche_moy_classe',$_POST['affiche_moy_classe']);}
	else{savePref($_SESSION['login'],'graphe_affiche_moy_classe','oui');}

	if(isset($_POST['graphe_star_decalage_y'])) {savePref($_SESSION['login'],'graphe_star_decalage_y',$_POST['graphe_star_decalage_y']);}
	else{savePref($_SESSION['login'],'graphe_star_decalage_y',0);}

	if(isset($_POST['graphe_star_modif_rayon'])) {savePref($_SESSION['login'],'graphe_star_modif_rayon',$_POST['graphe_star_modif_rayon']);}
	else{savePref($_SESSION['login'],'graphe_star_modif_rayon',0);}

	if($msg=='') {
		$msg.="Préférences personnelles enregistrées.";
	}

}

unset($id_classe);
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
// Vérifier s'il peut y avoir des accents dans un id_classe.
if(!is_numeric($id_classe)) {$id_classe=NULL;}

//===============================================
// Enregistrement de l'avis du conseil de classe:
if(
	(
		(($_SESSION['statut']=='professeur')&&(getSettingValue('GepiRubConseilProf')=="yes"))||
		(($_SESSION['statut']=='scolarite')&&(getSettingValue('GepiRubConseilScol')=="yes"))||
		(($_SESSION['statut']=='cpe')&&((getSettingValue('GepiRubConseilCpe')=="yes")||(getSettingValue('GepiRubConseilCpeTous')=="yes")))
	)&&(isset($_POST['enregistrer_avis']))&&($_POST['enregistrer_avis']=="y")
) {
	check_token();

	$eleve_saisie_avis = isset($_POST['eleve_saisie_avis']) ? $_POST['eleve_saisie_avis'] : NULL;
	// Contrôler les caractères utilisés...
	$eleve_saisie_avis=preg_replace("/[^A-Za-z0-9_.-]/","",$eleve_saisie_avis);

	$num_periode_saisie = isset($_POST['num_periode_saisie']) ? $_POST['num_periode_saisie'] : NULL;

	//if(!is_numeric($num_periode_saisie)) {
	if(mb_strlen(preg_replace("/[0-9]/","",$num_periode_saisie))==0) {
		$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='$num_periode_saisie' AND login='$eleve_saisie_avis';";
		//echo "$sql<br />";
		$verif=mysql_query($sql);
		if (mysql_num_rows($verif)==0) {
			tentative_intrusion(2, "Tentative de saisie d'avis du conseil de classe pour un élève non inscrit dans la classe.");
			$mess=rawurlencode("Tentative de saisie d'avis du conseil de classe pour un élève non inscrit dans la classe.");
			header("Location: ../accueil.php?msg=$mess");
			die();
		}

		if($_SESSION['statut']=='professeur') {
			$sql="SELECT 1=1 FROM j_groupes_classes jgc,
									j_groupes_professeurs jgp,
									j_eleves_professeurs jep
							WHERE jgc.id_classe='$id_classe' AND
									jgc.id_groupe=jgp.id_groupe AND
									jgp.login=jep.professeur AND
									jep.login='$eleve_saisie_avis' AND
									jgp.login ='".$_SESSION['login']."';";
			$verif=mysql_query($sql);
			if (mysql_num_rows($verif)==0) {
				tentative_intrusion(2, "Tentative de saisie d'avis du conseil de classe pour un élève ($eleve_saisie_avis) dont vous n'êtes pas ".getSettingValue('gepi_prof_suivi').".");
				$mess=rawurlencode("Tentative de saisie d'avis du conseil de classe pour un élève dont vous n'êtes pas ".getSettingValue('gepi_prof_suivi').".");
				header("Location: ../accueil.php?msg=$mess");
				die();
			}
		}
		elseif($_SESSION['statut']=='cpe') {
			if(getSettingValue('GepiRubConseilCpeTous')!="yes") {
				$sql="SELECT 1=1 FROM j_eleves_cpe
								WHERE e_login='$eleve_saisie_avis' AND
										cpe_login ='".$_SESSION['login']."';";
				$verif=mysql_query($sql);
				if (mysql_num_rows($verif)==0) {
					tentative_intrusion(2, "Tentative de saisie d'avis du conseil de classe pour un élève ($eleve_saisie_avis) dont vous n'êtes pas CPE.");
					$mess=rawurlencode("Tentative de saisie d'avis du conseil de classe pour un élève non inscrit dans la classe.");
					header("Location: ../accueil.php?msg=$mess");
					die();
				}
			}
		}
		elseif($_SESSION['statut']=='scolarite') {
			// Compte scolarité
			$sql="SELECT 1=1 FROM j_scol_classes jsc,
								j_eleves_classes jec
							WHERE jsc.id_classe=jec.id_classe AND
								jec.periode='$num_periode_saisie' AND
								jec.login='$eleve_saisie_avis' AND
								jsc.login='".$_SESSION['login']."';";
			$verif=mysql_query($sql);
			if (mysql_num_rows($verif)==0) {
				tentative_intrusion(2, "Tentative de saisie d'avis du conseil de classe pour un élève ($eleve_saisie_avis) d'une classe dont le compte scolarité n'est pas responsable.");
				$mess=rawurlencode("Tentative de saisie d'avis du conseil de classe pour un élève d'une classe dont vous n'êtes pas responsable.");
				header("Location: ../accueil.php?msg=$mess");
				die();
			}
		}
		else {
			tentative_intrusion(2, "Tentative de saisie d'avis du conseil de classe pour ".$eleve_saisie_avis.".");
			$mess=rawurlencode("Tentative non autorisée de saisie d'avis du conseil de classe.");
			header("Location: ../accueil.php?msg=$mess");
			die();
		}

		$sql="SELECT verouiller FROM periodes WHERE id_classe='$id_classe' AND num_periode='$num_periode_saisie';";
		//echo "$sql<br />";
		$test_verr_per=mysql_query($sql);
		$lig_verr_per=mysql_fetch_object($test_verr_per);
		if($lig_verr_per->verouiller!='O') {

			$current_eleve_login_ap = isset($NON_PROTECT["current_eleve_login_ap"]) ? traitement_magic_quotes(corriger_caracteres($NON_PROTECT["current_eleve_login_ap"])) :NULL;

			// ***** AJOUT POUR LES MENTIONS *****
			$current_eleve_login_me = isset($_POST["current_eleve_login_me"]) ? $_POST["current_eleve_login_me"] : NULL;
			// ***** FIN DE L'AJOUT POUR LES MENTIONS *****

			//echo "\$current_eleve_login_ap=$current_eleve_login_ap<br />";

			$test_eleve_avis_query = mysql_query("SELECT * FROM avis_conseil_classe WHERE (login='$eleve_saisie_avis' AND periode='$num_periode_saisie')");
			$test = mysql_num_rows($test_eleve_avis_query);
			if ($test != "0") {
				$sql="UPDATE avis_conseil_classe SET avis='$current_eleve_login_ap',";
				if(isset($current_eleve_login_me)) {$sql.="id_mention='$current_eleve_login_me',";}
				$sql.="statut='' WHERE (login='$eleve_saisie_avis' AND periode='$num_periode_saisie');";
				$register = mysql_query($sql);
			}
			else {
				$sql="INSERT INTO avis_conseil_classe SET login='$eleve_saisie_avis',periode='$num_periode_saisie',avis='$current_eleve_login_ap',";
				if(isset($current_eleve_login_me)) {$sql.="id_mention='$current_eleve_login_me',";}
				$sql.="statut='';";
				$register = mysql_query($sql);
			}

			if (!$register) {
				$msg = "Erreur lors de l'enregistrement des données.";
			}
			else {
				$msg="Enregistrement de l'avis effectué.";
			}
		}
		else {
			$msg = "La période sur laquelle vous voulez enregistrer est verrouillée";
		}

		if((isset($_POST['passer_a_recapitulatif_avis']))&&($_POST['passer_a_recapitulatif_avis']=='y')&&(acces('/saisie/saisie_avis2.php', $_SESSION['statut']))) {

			if(
				(($_SESSION['statut']=='professeur')&&(getSettingValue('GepiRubConseilProf')=="yes"))||
				(($_SESSION['statut']=='scolarite')&&(getSettingValue('GepiRubConseilScol')=="yes"))||
				(($_SESSION['statut']=='cpe')&&((getSettingValue('GepiRubConseilCpe')=="yes")||(getSettingValue('GepiRubConseilCpeTous')=="yes")))
			) {

				$droit_saisie_avis="y";
				// Contrôler si le prof est PP de l'élève
				if($_SESSION['statut']=='professeur') {
					$droit_saisie_avis="n";
					$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."' AND id_classe='$id_classe';";
					$verif_pp=mysql_query($sql);
					if(mysql_num_rows($verif_pp)>0) {
						$droit_saisie_avis="y";
					}
				}
				elseif(($_SESSION['statut']=='cpe')&&(getSettingValue('GepiRubConseilCpeTous')!="yes")) {
					$droit_saisie_avis="n";
					$sql="SELECT 1=1 FROM j_eleves_cpe jecpe, j_eleves_classes jec WHERE jecpe.cpe_login='".$_SESSION['login']."' AND jecpe.e_login=jec.login AND jec.id_classe='".$id_classe."';";
					$verif_cpe=mysql_query($sql);
					if(mysql_num_rows($verif_cpe)>0) {
						$droit_saisie_avis="y";
					}
				}

				if($droit_saisie_avis=="y") {
					header("Location: ../saisie/saisie_avis2.php?id_classe=$id_classe&periode_num=$num_periode_saisie&msg=".rawurlencode($msg));
					die();
				}
			}
		}

	}
	else {echo "Periode non numérique: $num_periode_saisie<br />";}
	unset($eleve_saisie_avis);
}
//===============================================

$style_specifique="visualisation/affiche_eleve";
//**************** EN-TETE *****************
$titre_page = "Outil de visualisation";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

// Vérifications droits d'accès
if (
	($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesGraphParent") != "yes") OR
	($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesGraphEleve") != "yes")
	) {
	tentative_intrusion(1, "Tentative d'accès à l'outil de visualisation graphique sans y être autorisé.");
	echo "<p>Vous n'êtes pas autorisé à visualiser cette page.</p>";
	require "../lib/footer.inc.php";
	die();
}

// Récupération des variables:

unset($login_eleve);
$login_eleve = isset($_POST["login_eleve"]) ? $_POST["login_eleve"] : (isset($_GET["login_eleve"]) ? $_GET["login_eleve"] : NULL);

// Quelques filtrages de départ pour pré-initialiser la variable qui nous importe ici : $login_eleve
if ($_SESSION['statut'] == "responsable") {
	$sql="(SELECT e.login, e.prenom, e.nom " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$_SESSION['login']."' AND (re.resp_legal='1' OR re.resp_legal='2')))";
	if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
		$sql.=" UNION (SELECT e.login, e.prenom, e.nom " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$_SESSION['login']."' AND re.resp_legal='0' AND re.acces_sp='y'))";
	}
	$sql.=";";
	$get_eleves = mysql_query($sql);

	if (mysql_num_rows($get_eleves) == 1) {
		// Un seul élève associé : on initialise tout de suite la variable $login_eleve
		$login_eleve = mysql_result($get_eleves, 0);
	} elseif (mysql_num_rows($get_eleves) == 0) {
		echo "<p>Il semble que vous ne soyez associé à aucun élève. Contactez l'administrateur pour résoudre cette erreur.</p>";
		require "../lib/footer.inc.php";
		die();
	} else {
		if ($login_eleve != null) {
			// $login_eleve a été défini mais l'utilisateur a plusieurs élèves associés. On vérifie
			// qu'il a le droit de visualiser les données pour l'élève sélectionné.
			$sql="(SELECT e.login " .
					"FROM eleves e, responsables2 re, resp_pers r " .
					"WHERE (" .
					"e.login = '" . $login_eleve . "' AND " .
					"e.ele_id = re.ele_id AND " .
					"re.pers_id = r.pers_id AND " .
					"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2')))";
			if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
				$sql.=" UNION (SELECT e.login " .
					"FROM eleves e, responsables2 re, resp_pers r " .
					"WHERE (" .
					"e.login = '" . $login_eleve . "' AND " .
					"e.ele_id = re.ele_id AND " .
					"re.pers_id = r.pers_id AND " .
					"r.login = '" . $_SESSION['login'] . "' AND re.resp_legal='0' AND re.acces_sp='y'))";
			}
			$sql.=";";
			$test = mysql_query($sql);
			if (mysql_num_rows($test) == 0) {
			    tentative_intrusion(2, "Tentative par un parent de visualisation graphique des résultats d'un élève dont il n'est pas responsable légal.");
			    echo "<p>Vous ne pouvez visualiser que les graphiques des élèves pour lesquels vous êtes responsable légal.</p>\n";
			    require("../lib/footer.inc.php");
				die();
			}
		}
	}
} else if ($_SESSION['statut'] == "eleve") {
	// Si l'utilisateur identifié est un élève, pas le choix, il ne peut consulter que son équipe pédagogique
	if ($login_eleve != null and (my_strtoupper($login_eleve) != my_strtoupper($_SESSION['login']))) {
		tentative_intrusion(2, "Tentative par un élève de visualisation graphique des résultats d'un autre élève.");
	}
	$login_eleve = $_SESSION['login'];
}

if ($login_eleve and $login_eleve != null) {
	// On récupère la classe de l'élève, pour déterminer automatiquement le nombre de périodes
	// On part du postulat que même si l'élève change de classe en cours d'année, c'est pour aller
	// dans une classe qui a le même nombre de périodes...
	$id_classe = mysql_result(mysql_query("SELECT id_classe FROM j_eleves_classes jec WHERE login = '".$login_eleve."' LIMIT 1"), 0);
	$req = mysql_query("SELECT nom, prenom FROM eleves WHERE login='".$login_eleve."'");
	$nom_eleve = mysql_result($req, 0, "nom");
	$prenom_eleve = mysql_result($req, 0, "prenom");
}


include "../lib/periodes.inc.php";
// Cette bibliothèque permet de récupérer des tableaux de $nom_periode et $ver_periode (et $nb_periode)
// pour la classe considérée (valeur courante de $id_classe).

// Choix de la classe:
if (!isset($id_classe) and $_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve") {
	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='index.php'>Autre outil de visualisation</a></p>\n";
	echo "</div>\n";

	echo "<p>Sélectionnez la classe : </p>\n";
	echo "<blockquote>\n";
	if($_SESSION['statut']=='scolarite') {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur') {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
	}
	elseif($_SESSION['statut']=='cpe') {
		/*
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
		*/
		// Les cpe ont accès à tous les bulletins, donc aussi aux courbes
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}

	if(((getSettingValue("GepiAccesReleveProfToutesClasses")=="yes")&&($_SESSION['statut']=='professeur'))||
		((getSettingValue("GepiAccesReleveScol")=='yes')&&($_SESSION['statut']=='scolarite'))) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}
	/*
	if(((getSettingValue("GepiAccesReleveProfToutesClasses")=="yes")&&($_SESSION['statut']=='professeur'))||
		((getSettingValue("GepiAccesReleveScol")=='yes')&&($_SESSION['statut']=='scolarite'))||
		((getSettingValue("GepiAccesReleveCpeTousEleves")=='yes')&&($_SESSION['statut']=='cpe'))) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}
	elseif((getSettingValue("GepiAccesReleveCpe")=='yes')&&($_SESSION['statut']=='cpe')) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
	}
	*/
	//echo "$sql<br />";
	$call_data=mysql_query($sql);

	$nombre_lignes = mysql_num_rows($call_data);

	// Courbe ou étoile
	$type_graphe=(isset($_GET['type_graphe'])) ? $_GET['type_graphe'] : NULL;
	$chaine_type_graphe=isset($type_graphe) ? "&amp;type_graphe=$type_graphe" : "";

	// PNG ou SVG
	//$mode_graphe=(isset($_GET['mode_graphe'])) ? $_GET['mode_graphe'] : NULL;
	//$chaine_mode_graphe=isset($mode_graphe) ? "&amp;mode_graphe=$mode_graphe" : "";

	if($nombre_lignes>0) {
		unset($lien_classe);
		unset($txt_classe);
		$i = 0;
		while ($i < $nombre_lignes) {
			$lien_classe[]=$_SERVER['PHP_SELF']."?id_classe=".mysql_result($call_data, $i, "id").$chaine_type_graphe;
			//$lien_classe[]=$_SERVER['PHP_SELF']."?id_classe=".mysql_result($call_data, $i, "id").$chaine_type_graphe.$chaine_mode_graphe;
			$txt_classe[]=ucfirst(mysql_result($call_data, $i, "classe"));
			$i++;
		}

		tab_liste($txt_classe,$lien_classe,3);
	}
	else {
		echo "<p style='color:red'>Vous n'êtes associé à aucun élève.</p>\n";
	}
	echo "</blockquote>\n";
	//echo "</p>\n";
	//echo "</form>\n";

	// Après ça, on arrive en fin de page avec le require("../lib/footer.inc.php");

} elseif ($_SESSION['statut'] == "responsable" and $login_eleve == null) {
	// On demande à l'utilisateur de choisir l'élève pour lequel il souhaite visualiser les données
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
	echo "<p>Cliquez sur le nom de l'élève pour lequel vous souhaitez visualiser les moyennes :</p><ul>";
	while ($current_eleve = mysql_fetch_object($get_eleves)) {
		echo "<li><a href='affiche_eleve.php?login_eleve=".$current_eleve->login."'>".$current_eleve->prenom." ".$current_eleve->nom."</a></li>";
	}
	echo "</ul>\n";
	// Après ça, on arrive en fin de page avec le require("../lib/footer.inc.php");

} else {
	// A ce stade:
	// - la classe est choisie (prof, scol ou cpe) ou récupérée d'après le login élève choisi (responsable, eleve): $id_classe
	// - le login élève est imposé pour un utilisateur connecté élève ou responsable: $login_eleve et $eleve1=$login_eleve
	//   sinon, on récupère $_POST['eleve1']

	// Capture des mouvements de la souris et affichage des cadres d'info
	// Remonté pour éviter/limiter des erreurs JavaScript lors du chargement...
	//echo "<script type='text/javascript' src='cadre_info.js'></script>\n";
	// On utilise maintenant /lib/position.js




	//==========================================================
	//        Dispositif de restriction des accès aux appréciations pour les comptes responsables/eleves
	unset($tab_acces_app);
	$tab_acces_app=array();

	if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
		$tab_acces_app=acces_appreciations(1, $nb_periode, $id_classe, $_SESSION['statut']);
	}
	else {
		// Pas de limitations d'accès pour les autres statuts.
		for($i=1;$i<=$nb_periode;$i++) {
			$tab_acces_app[$i]="y";
		}
	}
	//==========================================================




	if(isset($_POST['type_graphe'])) {

		//echo "\$_POST['type_graphe']=".$_POST['type_graphe']."<br />\n";

		if($_POST['type_graphe']=='etoile') {
			$type_graphe='etoile';
		}
		else{
			$type_graphe='courbe';
		}
	}
	elseif(isset($_GET['type_graphe'])) {

		//echo "\$_GET['type_graphe']=".$_GET['type_graphe']."<br />\n";

		if($_GET['type_graphe']=='etoile') {
			$type_graphe='etoile';
		}
		else{
			$type_graphe='courbe';
		}
	}
	else{
		if(getSettingValue('graphe_type_graphe')) {
			$type_graphe=getSettingValue('graphe_type_graphe');
		}
		else{
			$type_graphe='courbe';
		}
	}



	// PNG ou SVG

	if(isset($_POST['mode_graphe'])) {
		//echo "\$_POST['mode_graphe']=".$_POST['mode_graphe']."<br />\n";
		if($_POST['mode_graphe']=='svg') {
			$mode_graphe='svg';
		}
		else{
			$mode_graphe='png';
		}
	}
	elseif(isset($_GET['mode_graphe'])) {
		//echo "\$_GET['mode_graphe']=".$_GET['mode_graphe']."<br />\n";
		if($_GET['mode_graphe']=='svg') {
			$mode_graphe='svg';
		}
		else{
			$mode_graphe='png';
		}
	}
	else{
		$pref_mode_graphe=getPref($_SESSION['login'],'graphe_mode_graphe','');
		if(($pref_mode_graphe=='png')||($pref_mode_graphe=='svg')) {
			$mode_graphe=$pref_mode_graphe;
		}
		else {
			if(getSettingValue('graphe_mode_graphe')) {
				$mode_graphe=getSettingValue('graphe_mode_graphe');
			}
			else{
				$mode_graphe='png';
			}
		}
	}





	//echo "\$type_graphe=".$type_graphe."<br />\n";


	if ($_SESSION['statut'] != "responsable" and $_SESSION['statut'] != "eleve") {
		/*
		foreach($_POST as $post => $val) {
			echo $post.' : '.$val."<br />\n";
		}
		*/

		echo "<div class='noprint'>\n";

		echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

		echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='index.php'>Autre outil de visualisation</a>";
		// La classe est choisie.

		echo " | ";

		// On ajoute l'accès/retour à une autre classe:
		//echo "<a href=\"$_PHP_SELF\">Choisir une autre classe</a>|";
		//echo " | <a href=\"".$_SERVER['PHP_SELF']."\">Choisir une autre classe</a></p>";
		/*
		echo " | <a href=\"".$_SERVER['PHP_SELF'];
		echo "?type_graphe=$type_graphe";
		echo "\">Choisir une autre classe</a>";
		*/

		// =================================
		// AJOUT: boireaus
		// Pour proposer de passer à la classe suivante ou à la précédente
		//$sql="SELECT id, classe FROM classes ORDER BY classe";
		if($_SESSION['statut']=='scolarite') {
			$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
		}
		elseif($_SESSION['statut']=='professeur') {
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
		}
		elseif($_SESSION['statut']=='cpe') {
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
				p.id_classe = c.id AND
				jec.id_classe=c.id AND
				jec.periode=p.num_periode AND
				jecpe.e_login=jec.login AND
				jecpe.cpe_login='".$_SESSION['login']."'
				ORDER BY classe";
		}

		$chaine_options_classes="";

		$res_class_tmp=mysql_query($sql);
		if(mysql_num_rows($res_class_tmp)>0) {
			$id_class_prec=0;
			$id_class_suiv=0;
			$temoin_tmp=0;
			while($lig_class_tmp=mysql_fetch_object($res_class_tmp)) {
				if($lig_class_tmp->id==$id_classe) {
					$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
					$temoin_tmp=1;
					if($lig_class_tmp=mysql_fetch_object($res_class_tmp)) {
						$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
						$id_class_suiv=$lig_class_tmp->id;
					}
					else{
						$id_class_suiv=0;
					}
				}
				else {
					$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
				}
				if($temoin_tmp==0) {
					$id_class_prec=$lig_class_tmp->id;
				}
			}
		}
		// =================================

		if(isset($id_class_prec)) {
			if($id_class_prec!=0) {
				echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec";
				echo "&amp;type_graphe=$type_graphe";
				echo "&amp;mode_graphe=$mode_graphe";
				echo "'>Classe précédente</a> | ";
			}
		}

		echo "<input type='hidden' name='type_graphe' value='$type_graphe' />\n";
		echo "<input type='hidden' name='mode_graphe' value='$mode_graphe' />\n";

		if($chaine_options_classes!="") {
			echo "<select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
			echo $chaine_options_classes;
			echo "</select> | \n";
		}

		if(isset($id_class_suiv)) {
			if($id_class_suiv!=0) {
				echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv";
				echo "&amp;type_graphe=$type_graphe";
				echo "&amp;mode_graphe=$mode_graphe";
				echo "'>Classe suivante</a>";
				}
		}
		echo "</p>\n";

		echo "</form>\n";

		echo "</div>\n";
	} else {
		echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
	}

	//===============================================
	// Récupération des variables:
	//$id_classe=$_POST['id_classe']; // Récupérée plus haut...
	$eleve1=isset($_POST['eleve1']) ? $_POST['eleve1'] : NULL;
	// Login d'un élève réclamé par Précédent/Suivant:
	$eleve1b=isset($_POST['eleve1b']) ? $_POST['eleve1b'] : NULL;
	if((isset($eleve1b))&&($eleve1b!='')) {
		$eleve1=$eleve1b;
	}
	$eleve2=isset($_POST['eleve2']) ? $_POST['eleve2'] : NULL;

	// Possibilité de désactiver l'affichage des infobulles via un JavaScript:
	$desactivation_infobulle=isset($_POST['desactivation_infobulle']) ? $_POST['desactivation_infobulle'] : 'n';

	// Vérification de sécurité
	if ($_SESSION['statut'] == "eleve") {
		$eleve1 = $login_eleve;
	}
	if ($_SESSION['statut'] == "responsable") {
		if ($login_eleve != null) {
			$eleve1 = $login_eleve;
		}
		$sql="(SELECT e.login " .
				"FROM eleves e, responsables2 re, resp_pers r " .
				"WHERE (" .
				"e.login = '" . $eleve1 . "' AND " .
				"e.ele_id = re.ele_id AND " .
				"re.pers_id = r.pers_id AND " .
				"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2')))";
		if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
			$sql.=" UNION (SELECT e.login " .
				"FROM eleves e, responsables2 re, resp_pers r " .
				"WHERE (" .
				"e.login = '" . $eleve1 . "' AND " .
				"e.ele_id = re.ele_id AND " .
				"re.pers_id = r.pers_id AND " .
				"r.login = '" . $_SESSION['login'] . "' AND re.resp_legal='0' AND re.acces_sp='y'))";
		}
		$sql.=";";
		$test = mysql_query($sql);
		if (mysql_num_rows($test) == 0) {
		    tentative_intrusion(3, "Tentative (forte) d'un parent de visualisation graphique des résultats d'un élève dont il n'est pas responsable légal.");
		    echo "<p>Vous ne pouvez visualiser que les graphiques des élèves pour lesquels vous êtes responsable légal.\n";
		    require("../lib/footer.inc.php");
			die();
		}
	}
	if ($_SESSION['statut'] == "eleve" OR $_SESSION['statut'] == "responsable") {
		// On filtre eleve2 :
		if(!isset($eleve2)) {$eleve2 = "moyclasse";}
		if((($_SESSION['statut'] == "eleve")&&(getSettingAOui('GepiAccesGraphRangEleve')))||(($_SESSION['statut'] == "responsable")&&(getSettingAOui('GepiAccesGraphRangParent')))) {
			if ($eleve2 != "moyclasse" and $eleve2 != "moymin" and $eleve2 != "moymax" and $eleve2 != "rang_eleve") {
				tentative_intrusion(3, "Tentative de manipulation de la seconde source de données sur la visualisation graphique des résultats (détournement de _eleve2_, qui ne peut, dans le cas d'un utilisateur parent ou eleve, ne correspondre qu'à une moyenne et non un autre élève).");
				$eleve2 = "moyclasse";
			}
		}
		else {
			if ($eleve2 != "moyclasse" and $eleve2 != "moymin" and $eleve2 != "moymax") {
				tentative_intrusion(3, "Tentative de manipulation de la seconde source de données sur la visualisation graphique des résultats (détournement de _eleve2_, qui ne peut, dans le cas d'un utilisateur parent ou eleve, ne correspondre qu'à une moyenne et non un autre élève).");
				$eleve2 = "moyclasse";
			}
		}
	}

	// On évite d'initialiser à NULL pour permettre de pré-cocher le choix_periode.
	//$choix_periode=isset($_POST['choix_periode']) ? $_POST['choix_periode'] : NULL;
	//$choix_periode=isset($_POST['choix_periode']) ? $_POST['choix_periode'] : "toutes_periodes";
	$choix_periode=isset($_POST['choix_periode']) ? $_POST['choix_periode'] : "periode";
	//if($choix_periode!='toutes_periodes') {
	if(($choix_periode!='toutes_periodes')&&(isset($_POST['periode']))) {
		$periode=$_POST['periode'];
	}
	else{
		$periode="";
	}



	//======================================================================
	//======================================================================
	//======================================================================

	// On récupère de $_POST les paramètres d'affichage s'ils ont été transmis, sinon, on les récupère dans la base MySQL.

	//$affiche_photo=isset($_POST['affiche_photo']) ? $_POST['affiche_photo'] : '';
	if(isset($_POST['affiche_photo'])) {
		$affiche_photo=$_POST['affiche_photo'];
	}
	else{
		$pref_affiche_photo=getPref($_SESSION['login'],'graphe_affiche_photo','');
		if(($pref_affiche_photo=='oui')||($pref_affiche_photo=='non')) {
			$affiche_photo=$pref_affiche_photo;
		}
		else {
			if(getSettingValue('graphe_affiche_photo')) {
				$affiche_photo=getSettingValue('graphe_affiche_photo');
			}
			else{
				$affiche_photo="non";
			}
		}
	}

	if(isset($_POST['largeur_imposee_photo'])) {
		$largeur_imposee_photo=$_POST['largeur_imposee_photo'];
	}
	else{
		$pref_largeur_imposee_photo=getPref($_SESSION['login'],'graphe_largeur_imposee_photo','');
		if(($pref_largeur_imposee_photo!='')&&(preg_replace('/[0-9]/','',$pref_largeur_imposee_photo)=='')) {
			$largeur_imposee_photo=$pref_largeur_imposee_photo;
		}
		else {
			if(getSettingValue('graphe_largeur_imposee_photo')) {
				$largeur_imposee_photo=getSettingValue('graphe_largeur_imposee_photo');
			}
			else{
				$largeur_imposee_photo=100;
			}
		}
	}
	// On s'assure que la largeur est valide:
	if((mb_strlen(preg_replace("/[0-9]/","",$largeur_imposee_photo))!=0)||($largeur_imposee_photo=="")) {$largeur_imposee_photo=100;}


	if(isset($_POST['affiche_mgen'])) {
		$affiche_mgen=$_POST['affiche_mgen'];
	}
	else{
		$pref_affiche_mgen=getPref($_SESSION['login'],'graphe_affiche_mgen','');
		if(($pref_affiche_mgen=='oui')||($pref_affiche_mgen=='non')) {
			$affiche_mgen=$pref_affiche_mgen;
		}
		else {
			if(getSettingValue('graphe_affiche_mgen')) {
				$affiche_mgen=getSettingValue('graphe_affiche_mgen');
			}
			else{
				$affiche_mgen="non";
			}
		}
	}

	if(isset($_POST['affiche_moy_classe'])) {
		$affiche_moy_classe=$_POST['affiche_moy_classe'];
	}
	else{
		$pref_affiche_moy_classe=getPref($_SESSION['login'],'graphe_affiche_moy_classe','');
		if(($pref_affiche_moy_classe=='oui')||($pref_affiche_moy_classe=='non')) {
			$affiche_moy_classe=$pref_affiche_moy_classe;
		}
		else {
			if(getSettingValue('graphe_affiche_moy_classe')) {
				$affiche_moy_classe=getSettingValue('graphe_affiche_moy_classe');
			}
			else{
				$affiche_moy_classe="oui";
			}
		}
	}


	if(isset($_POST['graphe_star_decalage_y'])) {
		$graphe_star_decalage_y=$_POST['graphe_star_decalage_y'];
	}
	else{
		$pref_graphe_star_decalage_y=getPref($_SESSION['login'],'graphe_star_decalage_y','');
		if(($pref_graphe_star_decalage_y!='')&&
			((preg_replace('/[0-9]/','',$pref_graphe_star_decalage_y)=='')||
			(preg_replace('/^-[0-9]*/','',$pref_graphe_star_decalage_y)==''))
		) {
			$graphe_star_decalage_y=$pref_graphe_star_decalage_y;
		}
		else {
			if(getSettingValue('graphe_star_decalage_y')) {
				$graphe_star_decalage_y=getSettingValue('graphe_star_decalage_y');
			}
			else{
				$graphe_star_decalage_y=0;
			}
		}
	}
	// On s'assure que la largeur est valide:
		if(
		($graphe_star_decalage_y=="")||
		($graphe_star_decalage_y=="-")||
		((!preg_match("/^[0-9]*$/",$graphe_star_decalage_y))&&
		(!preg_match("/^-[0-9]*$/",$graphe_star_decalage_y)))
	) {$graphe_star_decalage_y=0;}

	if(isset($_POST['graphe_star_modif_rayon'])) {
		$graphe_star_modif_rayon=$_POST['graphe_star_modif_rayon'];
	}
	else{
		$pref_graphe_star_modif_rayon=getPref($_SESSION['login'],'graphe_star_modif_rayon','');
		if(($pref_graphe_star_modif_rayon!='')&&
			((preg_replace('/[0-9]/','',$pref_graphe_star_modif_rayon)=='')||
			(preg_replace('/^-[0-9]*/','',$pref_graphe_star_modif_rayon)==''))
		) {
			$graphe_star_modif_rayon=$pref_graphe_star_modif_rayon;
		}
		else {
			if(getSettingValue('graphe_star_modif_rayon')) {
				$graphe_star_modif_rayon=getSettingValue('graphe_star_modif_rayon');
			}
			else{
				$graphe_star_modif_rayon=0;
			}
		}
	}
	// On s'assure que la largeur est valide:
	//cho "\$graphe_star_modif_rayon=$graphe_star_modif_rayon<br />";
	if(
		($graphe_star_modif_rayon=="")||
		($graphe_star_modif_rayon=="-")||
		((!preg_match("/^[0-9]*$/",$graphe_star_modif_rayon))&&
		(!preg_match("/^-[0-9]*$/",$graphe_star_modif_rayon)))
	) {$graphe_star_modif_rayon=0;}


	// 20121205
	$affiche_choix_rang="y";
	if((($_SESSION['statut']=='responsable')&&(!getSettingAOui('GepiAccesGraphRangParent')))||
	(($_SESSION['statut']=='eleve')&&(!getSettingAOui('GepiAccesGraphRangEleve')))) {$affiche_choix_rang="n";}

	if(isset($_POST['affiche_minmax'])) {
		$affiche_minmax=$_POST['affiche_minmax'];
	}
	else{
		$pref_affiche_minmax=getPref($_SESSION['login'],'graphe_affiche_minmax','');
		if(($pref_affiche_minmax=='oui')||($pref_affiche_minmax=='non')) {
			$affiche_minmax=$pref_affiche_minmax;
		}
		else {
			if(getSettingValue('graphe_affiche_minmax')) {
				$affiche_minmax=getSettingValue('graphe_affiche_minmax');
			}
			else{
				$affiche_minmax="oui";
			}
		}
	}

	if(isset($_POST['affiche_moy_annuelle'])) {
		$affiche_moy_annuelle=$_POST['affiche_moy_annuelle'];
	}
	else{
		$pref_affiche_moy_annuelle=getPref($_SESSION['login'],'graphe_affiche_moy_annuelle','');
		if(($pref_affiche_moy_annuelle=='oui')||($pref_affiche_moy_annuelle=='non')) {
			$affiche_moy_annuelle=$pref_affiche_moy_annuelle;
		}
		else {
			if(getSettingValue('graphe_affiche_moy_annuelle')) {
				$affiche_moy_annuelle=getSettingValue('graphe_affiche_moy_annuelle');
			}
			else{
				$affiche_moy_annuelle="non";
			}
		}
	}


	if(isset($_POST['graphe_pointille'])) {
		$graphe_pointille=$_POST['graphe_pointille'];
	}
	else{
		$pref_graphe_pointille=getPref($_SESSION['login'],'graphe_pointille','');
		if(($pref_graphe_pointille=='yes')||($pref_graphe_pointille=='no')) {
			$graphe_pointille=$pref_graphe_pointille;
		}
		else {
			if(getSettingValue('graphe_pointille')) {
				$graphe_pointille=getSettingValue('graphe_pointille');
			}
			else{
				$graphe_pointille="yes";
			}
		}
	}



	if(isset($_POST['largeur_graphe'])) {
		$largeur_graphe=$_POST['largeur_graphe'];
	}
	else{
		$pref_largeur_graphe=getPref($_SESSION['login'],'graphe_largeur_graphe','');
		if($pref_largeur_graphe!='') {
			$largeur_graphe=$pref_largeur_graphe;
		}
		else {
			if(getSettingValue('graphe_largeur_graphe')) {
				$largeur_graphe=getSettingValue('graphe_largeur_graphe');
			}
			else{
				$largeur_graphe=600;
			}
		}
	}
	if((mb_strlen(preg_replace("/[0-9]/","",$largeur_graphe))!=0)||($largeur_graphe=="")) {
		$largeur_graphe=600;
	}


	if(isset($_POST['hauteur_graphe'])) {
		$hauteur_graphe=$_POST['hauteur_graphe'];
		//echo "\$hauteur_graphe=$hauteur_graphe<br />";
	}
	else{
		$pref_hauteur_graphe=getPref($_SESSION['login'],'graphe_hauteur_graphe','');
		if($pref_hauteur_graphe!='') {
			$hauteur_graphe=$pref_hauteur_graphe;
		}
		else {
			if(getSettingValue('graphe_hauteur_graphe')) {
				$hauteur_graphe=getSettingValue('graphe_hauteur_graphe');
			}
			else{
				$hauteur_graphe=400;
			}
		}
	}
	if((mb_strlen(preg_replace("/[0-9]/","",$hauteur_graphe))!=0)||($hauteur_graphe=="")) {
		$hauteur_graphe=400;
	}


	if(isset($_POST['taille_police'])) {
		$taille_police=$_POST['taille_police'];
	}
	else{
		$pref_taille_police=getPref($_SESSION['login'],'graphe_taille_police','');
		if($pref_taille_police!='') {
			$taille_police=$pref_taille_police;
		}
		else {
			if(getSettingValue('graphe_taille_police')) {
				$taille_police=getSettingValue('graphe_taille_police');
			}
			else{
				$taille_police=2;
			}
		}
	}
	if((mb_strlen(preg_replace("/[0-9]/","",$taille_police))!=0)||($taille_police<1)||($taille_police>$taille_max_police)||($taille_police=="")) {
		$taille_police=2;
	}



	if(isset($_POST['epaisseur_traits'])) {
		$epaisseur_traits=$_POST['epaisseur_traits'];
	}
	else{
		$pref_epaisseur_traits=getPref($_SESSION['login'],'graphe_epaisseur_traits','');
		if($pref_epaisseur_traits!='') {
			$epaisseur_traits=$pref_epaisseur_traits;
		}
		else {
			if(getSettingValue('graphe_epaisseur_traits')) {
				$epaisseur_traits=getSettingValue('graphe_epaisseur_traits');
			}
			else{
				$epaisseur_traits=2;
			}
		}
	}
	if((mb_strlen(preg_replace("/[0-9]/","",$epaisseur_traits))!=0)||($epaisseur_traits<1)||($epaisseur_traits>6)||($epaisseur_traits=="")) {
		$epaisseur_traits=2;
	}


	if(isset($_POST['epaisseur_croissante_traits_periodes'])) {
		$epaisseur_croissante_traits_periodes=$_POST['epaisseur_croissante_traits_periodes'];
	}
	else{
		$pref_epaisseur_croissante_traits_periodes=getPref($_SESSION['login'],'graphe_epaisseur_croissante_traits_periodes','');
		if(($pref_epaisseur_croissante_traits_periodes=='oui')||($pref_epaisseur_croissante_traits_periodes=='non')) {
			$epaisseur_croissante_traits_periodes=$pref_epaisseur_croissante_traits_periodes;
		}
		else {
			if(getSettingValue('graphe_epaisseur_croissante_traits_periodes')) {
				$epaisseur_croissante_traits_periodes=getSettingValue('graphe_epaisseur_croissante_traits_periodes');
			}
			else{
				$epaisseur_croissante_traits_periodes="non";
			}
		}
	}


	// Pour présenter ou non, les noms longs en entier en travers sous le graphe.
	if(isset($_POST['temoin_image_escalier'])) {
		$temoin_image_escalier=$_POST['temoin_image_escalier'];
	}
	else{
		$pref_temoin_image_escalier=getPref($_SESSION['login'],'graphe_temoin_image_escalier','');
		if(($pref_temoin_image_escalier=='oui')||($pref_temoin_image_escalier=='non')) {
			$temoin_image_escalier=$pref_temoin_image_escalier;
		}
		else {
			if(getSettingValue('graphe_temoin_image_escalier')) {
				$temoin_image_escalier=getSettingValue('graphe_temoin_image_escalier');
			}
			else{
				$temoin_image_escalier="non";
			}
		}
	}


	// A zéro caractères, on ne tronque pas
	if(isset($_POST['tronquer_nom_court'])) {
		$tronquer_nom_court=$_POST['tronquer_nom_court'];
	}
	else{
		$pref_tronquer_nom_court=getPref($_SESSION['login'],'graphe_tronquer_nom_court','');
		if(($pref_tronquer_nom_court=='oui')||($pref_tronquer_nom_court=='non')) {
			$tronquer_nom_court=$pref_tronquer_nom_court;
		}
		else {
			if(getSettingValue('graphe_tronquer_nom_court')) {
				$tronquer_nom_court=getSettingValue('graphe_tronquer_nom_court');
			}
			else{
				$tronquer_nom_court=0;
			}
		}
	}

	if(isset($_POST['click_plutot_que_survol_aff_app'])) {
		$click_plutot_que_survol_aff_app=$_POST['click_plutot_que_survol_aff_app'];
	}
	else{
		$pref_click_plutot_que_survol_aff_app=getPref($_SESSION['login'],'graphe_click_plutot_que_survol_aff_app','');
		if(($pref_click_plutot_que_survol_aff_app=='y')||($pref_click_plutot_que_survol_aff_app=='n')) {
			$click_plutot_que_survol_aff_app=$pref_click_plutot_que_survol_aff_app;
		}
		else {
			if(getSettingValue('graphe_click_plutot_que_survol_aff_app')) {
				$click_plutot_que_survol_aff_app=getSettingValue('graphe_click_plutot_que_survol_aff_app');
			}
			else{
				$click_plutot_que_survol_aff_app="n";
			}
		}
	}


	//===============================================

	//echo "\$temoin_imageps=$temoin_imageps<br />";

	//========================
	// AJOUT boireaus 20090115
	if(isset($_POST['graphe_champ_saisie_avis_fixe'])) {
		$graphe_champ_saisie_avis_fixe=$_POST['graphe_champ_saisie_avis_fixe'];
		//echo "On prend la valeur POSTée: ";
	}
	else{
		$pref_champ_saisie_avis_fixe=getPref($_SESSION['login'],'graphe_champ_saisie_avis_fixe','');
		if(($pref_champ_saisie_avis_fixe=='y')||($pref_champ_saisie_avis_fixe=='n')) {
			$graphe_champ_saisie_avis_fixe=$pref_champ_saisie_avis_fixe;
			//echo "On prend la préférence de ".$_SESSION['login'].": ";
		}
		else {
			if(getSettingValue('graphe_champ_saisie_avis_fixe')) {
				//insert into setting set name='graphe_champ_saisie_avis_fixe',value='y';
				$graphe_champ_saisie_avis_fixe=getSettingValue('graphe_champ_saisie_avis_fixe');
				//echo "On prend la valeur définie globalement pour l'établissement: ";
			}
			else{
				$graphe_champ_saisie_avis_fixe="n";
				//echo "On prend la valeur par défaut: ";
			}
		}
	}
	//echo "\$graphe_champ_saisie_avis_fixe=$graphe_champ_saisie_avis_fixe<br />";
	//========================

	// AJOUT Eric 11/12/10
	if(isset($_POST['graphe_affiche_deroulant_appreciations'])){
		$graphe_affiche_deroulant_appreciations=$_POST['graphe_affiche_deroulant_appreciations'];
	}
	else{
		$pref_affiche_deroulant_appreciations=getPref($_SESSION['login'],'graphe_affiche_deroulant_appreciations','');
		if(($pref_affiche_deroulant_appreciations=='oui')||($pref_affiche_deroulant_appreciations=='non')) {
			$graphe_affiche_deroulant_appreciations=$pref_affiche_deroulant_appreciations;
		}
		else {
			if(getSettingValue('graphe_affiche_deroulant_appreciations')){
				$graphe_affiche_deroulant_appreciations=getSettingValue('graphe_affiche_deroulant_appreciations');
			}
			else{
				$graphe_affiche_deroulant_appreciations="non";
			}
		}
	}
	
	if(isset($_POST['graphe_hauteur_affichage_deroulant'])) {
		$graphe_hauteur_affichage_deroulant=$_POST['graphe_hauteur_affichage_deroulant'];
	}
	else{
		$pref_graphe_hauteur_affichage_deroulant=getPref($_SESSION['login'],'graphe_graphe_hauteur_affichage_deroulant','');
		if($pref_graphe_hauteur_affichage_deroulant!='') {
			$graphe_hauteur_affichage_deroulant=$pref_graphe_hauteur_affichage_deroulant;
		}
		else {
			if(getSettingValue('graphe_hauteur_affichage_deroulant')) {
				$graphe_hauteur_affichage_deroulant=getSettingValue('graphe_hauteur_affichage_deroulant');
			}
			else{
				$graphe_hauteur_affichage_deroulant=200;
			}
		}
	}
	if((mb_strlen(preg_replace("/[0-9]/","",$graphe_hauteur_affichage_deroulant))!=0)||($graphe_hauteur_affichage_deroulant=="")) {
		$graphe_hauteur_affichage_deroulant=200;
	}


	if(isset($_POST['graphe_app_deroulantes_taille_police'])) {
		$graphe_app_deroulantes_taille_police=$_POST['graphe_app_deroulantes_taille_police'];
	}
	else{
		$pref_graphe_app_deroulantes_taille_police=getPref($_SESSION['login'],'graphe_graphe_app_deroulantes_taille_police','');
		if($pref_graphe_app_deroulantes_taille_police!='') {
			$graphe_app_deroulantes_taille_police=$pref_graphe_app_deroulantes_taille_police;
		}
		else {
			if(getSettingValue('graphe_app_deroulantes_taille_police')) {
				$graphe_app_deroulantes_taille_police=getSettingValue('graphe_app_deroulantes_taille_police');
			}
			else{
				$graphe_app_deroulantes_taille_police=16;
			}
		}
	}
	if((mb_strlen(preg_replace("/[0-9]/","",$graphe_app_deroulantes_taille_police))!=0)||($graphe_app_deroulantes_taille_police=="")) {
		$graphe_app_deroulantes_taille_police=16;
	}


	//======================================================================
	//======================================================================
	//======================================================================

	if(isset($_POST['parametrer_affichage'])) {
		if($_POST['parametrer_affichage']=='y') {
			/*
			foreach($_POST as $post => $val) {
				echo $post.' : '.$val."<br />\n";
			}
			*/

			echo "<h2>Paramétrage de l'affichage du graphique</h2>\n";

			echo "<form action='".$_SERVER['PHP_SELF']."#graph' name='form_parametrage_affichage' method='post'>\n";
			echo add_token_field();
			echo "<p align='center'><input type='submit' name='Valider' value='Valider' /></p>\n";

			echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
			echo "<input type='hidden' name='is_posted' value='y' />\n";

			echo "<input type='hidden' name='parametrage_affichage' value='y' />\n";

			if($_SESSION['statut'] == "eleve" OR $_SESSION['statut'] == "responsable") {
				echo "<input type='hidden' name='eleve1' value='".$login_eleve."'/>\n";
				echo "<input type='hidden' name='login_eleve' value='".$login_eleve."'/>\n";
			}
			else {
				echo "<input type='hidden' name='eleve1' value='".$eleve1."'/>\n";
				echo "<input type='hidden' name='numeleve1' value='".$_POST['numeleve1']."'/>\n";
			}
			echo "<input type='hidden' name='eleve2' value='".$eleve2."'/>\n";
			echo "<input type='hidden' name='choix_periode' value='".$choix_periode."'/>\n";
			//echo "<input type='hidden' name='periode' value='".$periode."'/>\n";
			echo "<input type='hidden' name='periode' value=\"".$periode."\"/>\n";

			// Paramètres:
			echo "<p><b>Moyennes et périodes</b></p>\n";
			echo "<blockquote>\n";

			echo "<table border='0' summary='Paramètres'>\n";

			//===========================
			// 20121204
			if($affiche_moy_classe=='oui') {$checked=" checked='yes'";} else {$checked="";}
			echo "<tr valign='top'><td>Afficher la moyenne de la classe:</td><td>";
			if($affiche_moy_classe=='oui') {$checked=" checked='yes'";} else {$checked="";}
			echo "<input type='radio' name='affiche_moy_classe' id='affiche_moy_classe_oui' value='oui'$checked /><label for='affiche_moy_classe_oui' style='cursor: pointer;'> Oui </label>/";
			if($affiche_moy_classe!='oui') {$checked=" checked='yes'";} else {$checked="";}
			echo "<label for='affiche_moy_classe_non' style='cursor: pointer;'> Non </label><input type='radio' name='affiche_moy_classe' id='affiche_moy_classe_non' value='non'$checked />";
			echo "</td></tr>\n";
			//===========================

			if($affiche_mgen=='oui') {$checked=" checked='yes'";} else {$checked="";}
			//echo "<tr valign='top'><td><label for='affiche_mgen' style='cursor: pointer;'>Afficher la moyenne générale:</label></td><td><input type='checkbox' name='affiche_mgen' id='affiche_mgen' value='oui'$checked /></td></tr>\n";
			echo "<tr valign='top'><td>Afficher la moyenne générale:</td><td>";
			if($affiche_mgen=='oui') {$checked=" checked='yes'";} else {$checked="";}
			echo "<input type='radio' name='affiche_mgen' id='affiche_mgen_oui' value='oui'$checked /><label for='affiche_mgen_oui' style='cursor: pointer;'> Oui </label>/";
			if($affiche_mgen!='oui') {$checked=" checked='yes'";} else {$checked="";}
			echo "<label for='affiche_mgen_non' style='cursor: pointer;'> Non </label><input type='radio' name='affiche_mgen' id='affiche_mgen_non' value='non'$checked />";

			$sql="SELECT DISTINCT coef FROM j_groupes_classes WHERE id_classe='$id_classe' AND coef!='0.0';";
			$test_coef_non_nul=mysql_query($sql);
			if(mysql_num_rows($test_coef_non_nul)==0) {
				echo " <img src='../images/icons/ico_attention.png' width='22' height='19' alt='Tous les coefficients sont nuls' title='Tous les coefficients sont nuls. Aucun calcul de moyenne générale ne sera possible.' />\n";
			}
			echo "</td></tr>\n";

			//if($affiche_minmax=='oui') {$checked=" checked='yes'";} else {$checked="";}
			//echo "<tr valign='top'><td><label for='affiche_minmax' style='cursor: pointer;'>Afficher les bandes moyenne minimale/maximale:<br />(<i>cet affichage n'est pas appliqué en mode 'Toutes_les_periodes'</i>)</label></td><td><input type='checkbox' name='affiche_minmax' id='affiche_minmax' value='oui'$checked /></td></tr>\n";
			echo "<tr valign='top'><td>Afficher les bandes moyenne minimale/maximale:<br />(<i>cet affichage n'est pas appliqué en mode 'Toutes_les_periodes'</i>)</td><td>";
			if($affiche_minmax=='oui') {$checked=" checked='yes'";} else {$checked="";}
			echo "<input type='radio' name='affiche_minmax' id='affiche_minmax_oui' value='oui'$checked /><label for='affiche_minmax_oui' style='cursor: pointer;'> Oui </label>/\n";
			if($affiche_minmax!='oui') {$checked=" checked='yes'";} else {$checked="";}
			echo "<label for='affiche_minmax_non' style='cursor: pointer;'> Non </label><input type='radio' name='affiche_minmax' id='affiche_minmax_non' value='non'$checked /></label>";
			echo "</td></tr>\n";

			//$affiche_moy_annuelle
			echo "<tr valign='top'><td>Afficher les moyennes annuelles:<br />(<i>en mode 'Toutes_les_periodes' uniquement</i>)</td><td>";
			if($affiche_moy_annuelle=='oui') {$checked=" checked='yes'";} else {$checked="";}
			echo "<input type='radio' name='affiche_moy_annuelle' id='affiche_moy_annuelle_oui' value='oui'$checked /><label for='affiche_moy_annuelle_oui' style='cursor: pointer;'> Oui </label>/\n";
			if($affiche_moy_annuelle!='oui') {$checked=" checked='yes'";} else {$checked="";}
			echo "<label for='affiche_moy_annuelle_non' style='cursor: pointer;'> Non </label><input type='radio' name='affiche_moy_annuelle' id='affiche_moy_annuelle_non' value='non'$checked /></td></tr>\n";

			echo "</table>\n";
			echo "</blockquote>\n";

			//echo "<hr width='150' />\n";

			// Paramètres d'affichage:
			echo "<p><b>Graphe</b></p>\n";
			echo "<blockquote>\n";
			echo "<table border='0' summary='Paramètres'>\n";

			// Graphe en courbe ou étoile
			echo "<tr><td>Graphe en </td>\n";
			if($type_graphe=='courbe') {$checked=" checked='yes'";} else {$checked="";}
			echo "<td><label for='type_graphe_courbe' style='cursor: pointer;'><input type='radio' name='type_graphe' id='type_graphe_courbe' value='courbe'$checked /> courbe</label><br />\n";
			if($type_graphe=='etoile') {$checked=" checked='yes'";} else {$checked="";}
			echo "<label for='type_graphe_etoile' style='cursor: pointer;'><input type='radio' name='type_graphe' id='type_graphe_etoile' value='etoile'$checked /> étoile</label>\n";
			echo "</td></tr>\n";

			// - dimensions de l'image
			echo "<tr><td><label for='largeur_graphe' style='cursor: pointer;'>Largeur (<i>en pixels</i>):</label></td><td><input type='text' name='largeur_graphe' id='largeur_graphe' value='$largeur_graphe' size='3' onkeydown=\"clavier_2(this.id,event,0,2000);\" /></td></tr>\n";
			//echo " - \n";
			echo "<tr><td><label for='hauteur_graphe' style='cursor: pointer;'>Hauteur (<i>en pixels</i>):</label></td><td><input type='text' name='hauteur_graphe' id='hauteur_graphe' value='$hauteur_graphe' size='3' onkeydown=\"clavier_2(this.id,event,0,2000);\" /></td></tr>\n";

			// - taille des polices
			echo "<tr><td><label for='taille_police' style='cursor: pointer;'>Taille des polices:</label></td><td><select name='taille_police' id='taille_police'>\n";
			for($i=1;$i<=$taille_max_police;$i++) {
				if($taille_police==$i) {$selected=" selected='yes'";} else {$selected="";}
				echo "<option value='$i'$selected>$i</option>\n";
			}
			echo "</select></td></tr>\n";

			// - epaisseur des traits
			echo "<tr><td><label for='epaisseur_traits' style='cursor: pointer;'>Epaisseur des courbes:</label></td><td><select name='epaisseur_traits' id='epaisseur_traits'>\n";
			for($i=1;$i<=6;$i++) {
				if($epaisseur_traits==$i) {$selected=" selected='yes'";} else {$selected="";}
				echo "<option value='$i'$selected>$i</option>\n";
			}
			echo "</select></td></tr>\n";

			// - epaisseur croissante des traits
			echo "<tr><td>Epaisseur croissante des courbes de période en période:</td><td>\n";
			if($epaisseur_croissante_traits_periodes=='oui') {$checked=" checked='yes'";} else {$checked="";}
			echo "<input type='radio' name='epaisseur_croissante_traits_periodes' id='epaisseur_croissante_traits_periodes_oui' value='oui'$checked /><label for='epaisseur_croissante_traits_periodes_oui' style='cursor: pointer;'> Oui </label>/";
			if($epaisseur_croissante_traits_periodes!='oui') {$checked=" checked='yes'";} else {$checked="";}
			echo "<label for='epaisseur_croissante_traits_periodes_non' style='cursor: pointer;'> Non </label><input type='radio' name='epaisseur_croissante_traits_periodes' id='epaisseur_croissante_traits_periodes_non' value='non'$checked />";
			echo "</td></tr>\n";


			echo "<tr><td>Utiliser des pointillés quand <strong title='Une, pas plus'>une</strong> note manque&nbsp;:</td><td>\n";
			if($graphe_pointille!='no') {$checked=" checked='yes'";} else {$checked="";}
			echo "<input type='radio' name='graphe_pointille' id='graphe_pointille_oui' value='yes'$checked /><label for='graphe_pointille_oui' style='cursor: pointer;'> Oui </label>/";
			if($graphe_pointille=='no') {$checked=" checked='yes'";} else {$checked="";}
			echo "<label for='graphe_pointille_non' style='cursor: pointer;'> Non </label><input type='radio' name='graphe_pointille' id='graphe_pointille_non' value='no'$checked />";
			echo "</td></tr>\n";



			// - Décalage vertical du centre de l'étoile (Graphe en étoile)
			echo "<tr><td><label for='graphe_star_decalage_y' style='cursor: pointer;'>Décalage vertical du centre du polygone/étoile (<i>+ ou - tant de pixels</i>):<br />
			(<em>Graphe en étoile/polygone</em>)</label></td><td><input type='text' name='graphe_star_decalage_y' id='graphe_star_decalage_y' value='$graphe_star_decalage_y' size='3' onkeydown=\"clavier_2(this.id,event,-300,300);\" /></td></tr>\n";

			// - Modification sur le rayon du polygone/étoile (Graphe en étoile)
			echo "<tr><td><label for='graphe_star_modif_rayon' style='cursor: pointer;'>Modification du rayon du polygone/étoile (<i>+ ou - tant de pixels</i>):<br />
			(<em>Graphe en étoile/polygone</em>)</label></td><td><input type='text' name='graphe_star_modif_rayon' id='graphe_star_modif_rayon' value='$graphe_star_modif_rayon' size='3' onkeydown=\"clavier_2(this.id,event,-300,300);\" /></td></tr>\n";


			// - modèle de couleurs

			//if($temoin_imageps=='oui') {$checked=" checked='yes'";}else{$checked="";}
			//if($temoin_image_escalier=='oui') {$checked=" checked='yes'";} else {$checked="";}
			//echo "Utiliser ImagePs: <input type='checkbox' name='temoin_imageps' value='oui'$checked /><br />\n";
			//echo "<tr><td><label for='temoin_image_escalier' style='cursor: pointer;'>Afficher les noms longs de matières:<br />(<i>en légende sous le graphe</i>)</label></td><td><input type='checkbox' name='temoin_image_escalier' id='temoin_image_escalier' value='oui'$checked /></td></tr>\n";
			echo "<tr><td>Afficher les noms longs de matières:<br />(<i>en légende sous le graphe</i>)</td><td>";
			if($temoin_image_escalier=='oui') {$checked=" checked='yes'";} else {$checked="";}
			echo "<input type='radio' name='temoin_image_escalier' id='temoin_image_escalier_oui' value='oui'$checked /><label for='temoin_image_escalier_oui' style='cursor: pointer;'> Oui </label>/";
			if($temoin_image_escalier!='oui') {$checked=" checked='yes'";} else {$checked="";}
			echo "<label for='temoin_image_escalier_non' style='cursor: pointer;'> Non </label><input type='radio' name='temoin_image_escalier' id='temoin_image_escalier_non' value='non'$checked />";
			echo "</td></tr>\n";

			//echo "<tr><td>Tronquer le nom court<br />de matière à <a href='javascript:alert(\"A zéro caractères, on ne tronque pas le nom court de matière affiché en haut du graphe.\")'>X</a> caractères:</td><td><select name='tronquer_nom_court'>\n";
			echo "<tr><td><label for='tronquer_nom_court' style='cursor: pointer;'>Tronquer le nom court de la matière à <a href='#' onclick='alert(\"A zéro caractères, on ne tronque pas le nom court de matière affiché en haut du graphe.\");return false;'>X</a> caractères:<br />(<i>pour éviter des collisions de légendes en haut du graphe</i>)</label></td><td><select name='tronquer_nom_court' id='tronquer_nom_court'>\n";
			for($i=0;$i<=10;$i++) {
				if($tronquer_nom_court==$i) {$selected=" selected='yes'";} else {$selected="";}
				echo "<option value='$i'$selected>$i</option>\n";
			}
			echo "</select></td></tr>\n";

			echo "<tr>\n";
			echo "<td>\n";
			echo "<label for='click_plutot_que_survol_aff_app' style='cursor: pointer;'>Afficher les appréciations en infobulles lors du&nbsp;:</label>\n";
			echo "</td>\n";
			echo "<td>\n";

			if($click_plutot_que_survol_aff_app=='y') {$checked=" checked='yes'";} else {$checked="";}
			echo "<input type='radio' name='click_plutot_que_survol_aff_app' id='click_plutot_que_survol_aff_app_y' value='y'$checked /><label for='click_plutot_que_survol_aff_app_y' style='cursor: pointer;'> clic</label>/";
			if($click_plutot_que_survol_aff_app!='y') {$checked=" checked='yes'";} else {$checked="";}
			echo "<label for='click_plutot_que_survol_aff_app_n' style='cursor: pointer;'> survol</label><input type='radio' name='click_plutot_que_survol_aff_app' id='click_plutot_que_survol_aff_app_n' value='n'$checked />";
			echo "</td>\n";
			echo "</tr>\n";

			//========================
			// AJOUT boireaus 20090115
			if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")) {
				echo "<tr>\n";
				echo "<td valign='top'>Permettre la saisie de l'avis du conseil:</td>\n";
				echo "<td>\n";
				if($graphe_champ_saisie_avis_fixe!="y") {$checked=" checked";} else {$checked="";}
				echo "<input type='radio' name='graphe_champ_saisie_avis_fixe' id='graphe_champ_saisie_avis_fixe_n' value='n'$checked /> <label for='graphe_champ_saisie_avis_fixe_n' style='cursor: pointer;'>en infobulle</label><br />\n";
				if($graphe_champ_saisie_avis_fixe=="y") {$checked=" checked";} else {$checked="";}
				echo "<input type='radio' name='graphe_champ_saisie_avis_fixe' id='graphe_champ_saisie_avis_fixe_y' value='y'$checked /> <label for='graphe_champ_saisie_avis_fixe_y' style='cursor: pointer;'>en champ fixe sous le graphe</label>\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
			//========================


			// Graphe PNG ou SVG
			echo "<tr><td valign='top'>Générer des graphes en PNG ou SVG<br />\n";
			echo "(<i>Les graphes SVG donnent un aspect plus lissé,<br />mais nécessitent, avec certains navigateurs,<br />l'installation d'un plugin.<br />Uniquement disponible pour les graphes<br />en courbe pour le moment</i>)";
			echo "</td>\n";
			if($mode_graphe=='png') {$checked=" checked='yes'";} else {$checked="";}
			echo "<td valign='top'><label for='mode_graphe_png' style='cursor: pointer;'><input type='radio' name='mode_graphe' id='mode_graphe_png' value='png'$checked /> PNG</label><br />\n";
			if($mode_graphe=='svg') {$checked=" checked='yes'";} else {$checked="";}
			echo "<label for='mode_graphe_svg' style='cursor: pointer;'><input type='radio' name='mode_graphe' id='mode_graphe_svg' value='svg'$checked /> SVG</label>\n";
			echo "</td></tr>\n";
			echo "</table>\n";
			
			//Ajout Eric 11/12/10
			echo "<table border='0' summary='affiche_deroulant_appreciations'>\n";
			if(($graphe_affiche_deroulant_appreciations=='')||($graphe_affiche_deroulant_appreciations=='oui')) {$checked=" checked='yes'";} else {$checked="";}
			echo "<tr><td>Afficher une fenêtre déroulante contenant les appréciations:</td><td><label for='affiche_deroulant_appreciations_oui' style='cursor: pointer;'><input type='radio' name='graphe_affiche_deroulant_appreciations' id='affiche_deroulant_appreciations_oui' value='oui'$checked />Oui</label> / \n";
			if($graphe_affiche_deroulant_appreciations=='non') {$checked=" checked='yes'";} else {$checked="";}
			echo "<label for='affiche_deroulant_appreciations_non' style='cursor: pointer;'>Non<input type='radio' name='graphe_affiche_deroulant_appreciations' id='affiche_deroulant_appreciations_non' value='non'$checked /></label></td></tr>\n";

			echo "<tr><td><label for='graphe_hauteur_affichage_deroulant' style='cursor: pointer;'>Hauteur de la zone déroulante (<i>en pixels</i>):</label></td><td><input type='text' name='graphe_hauteur_affichage_deroulant' id='graphe_hauteur_affichage_deroulant' value='$graphe_hauteur_affichage_deroulant' size='3' onkeydown=\"clavier_2(this.id,event,0,2000);\" /></td></tr>\n";

			echo "<tr><td><label for='graphe_app_deroulantes_taille_police' style='cursor: pointer;'>Taille de la police (<i>en points</i>):</label></td><td><input type='text' name='graphe_app_deroulantes_taille_police' id='graphe_app_deroulantes_taille_police' value='$graphe_app_deroulantes_taille_police' size='3' onkeydown=\"clavier_2(this.id,event,1,100);\" /></td></tr>\n";

			echo "</table>\n";
			
			echo "</blockquote>\n";

			

			// - Affichage de la photo
			echo "<p><b>Paramètres des photos</b></p>\n";
			echo "<blockquote>\n";
			echo "<table border='0' summary='Paramètres des photos'>\n";
			if(($affiche_photo=='')||($affiche_photo=='oui')) {$checked=" checked='yes'";} else {$checked="";}
			echo "<tr><td>Afficher la photo de l'élève si elle existe:</td><td><label for='affiche_photo_oui' style='cursor: pointer;'><input type='radio' name='affiche_photo' id='affiche_photo_oui' value='oui'$checked />Oui</label> / \n";
			if($affiche_photo=='non') {$checked=" checked='yes'";} else {$checked="";}
			echo "<label for='affiche_photo_non' style='cursor: pointer;'>Non<input type='radio' name='affiche_photo' id='affiche_photo_non' value='non'$checked /></label></td></tr>\n";

			// - Largeur imposée pour la photo
			echo "<tr><td><label for='largeur_imposee_photo' style='cursor: pointer;'>Largeur de la photo (<i>en pixels</i>):</label></td><td><input type='text' name='largeur_imposee_photo' id='largeur_imposee_photo' value='$largeur_imposee_photo' size='3' onkeydown=\"clavier_2(this.id,event,0,2000);\" /></td></tr>\n";
			//echo "</p>\n";
			echo "</table>\n";
			echo "</blockquote>\n";



			if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")) {
				echo "<p><b>Couleurs</b></p>\n";
				echo "<blockquote>\n";
				//echo "<hr width='150' />\n";
				//echo "<p>\n";
				echo "<a href='choix_couleurs.php' target='blank'>Modifier les couleurs</a>\n";
				//echo "</p>\n";
				echo "</blockquote>\n";
			}


			echo "<p align='center'>";
			if($_SESSION['statut']=='scolarite') {
				//echo "<input type='checkbox' name='save_params' value='y' /> <b>Enregistrer les paramètres</b>\n";
				echo "<input type='hidden' name='save_params' value='' />\n";
				echo "<input type='button' onClick=\"document.forms['form_parametrage_affichage'].save_params.value='y';document.forms['form_parametrage_affichage'].submit();\" name='Enregistrer' value='Enregistrer les paramètres dans la base' />\n";
				echo "<br />\n";
			}

			echo "<input type='submit' name='Valider' value='Valider' /></p>\n";

			echo "</form>\n";


			echo "<hr />\n";

			echo "<form action='".$_SERVER['PHP_SELF']."#graph' name='form_raz_parametrage_affichage' method='post'>\n";
			echo add_token_field();

			echo "<p align='center'>Si, après des essais, vous souhaitez abandonner vos paramètres personnels et revenir aux paramètres enregistrés dans la base, validez ci-dessous&nbsp;:<br />";
			echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
			echo "<input type='hidden' name='is_posted' value='y' />\n";

			echo "<input type='hidden' name='parametrage_affichage' value='y' />\n";

			if($_SESSION['statut'] == "eleve" OR $_SESSION['statut'] == "responsable") {
				echo "<input type='hidden' name='eleve1' value='".$login_eleve."'/>\n";
				echo "<input type='hidden' name='login_eleve' value='".$login_eleve."'/>\n";
			}
			else {
				echo "<input type='hidden' name='eleve1' value='".$eleve1."'/>\n";
				echo "<input type='hidden' name='numeleve1' value='".$_POST['numeleve1']."'/>\n";
			}
			echo "<input type='hidden' name='eleve2' value='".$eleve2."'/>\n";
			echo "<input type='hidden' name='choix_periode' value='".$choix_periode."'/>\n";
			//echo "<input type='hidden' name='periode' value='".$periode."'/>\n";
			echo "<input type='hidden' name='periode' value=\"".$periode."\"/>\n";

			echo "<input type='submit' name=\"valider_raz_param\" value=\"Prendre les paramètres par défaut de l'établissement\" /></p>\n";

			echo "</form>\n";

			require("../lib/footer.inc.php");
			die();
		}
	}





	// Nom de la classe:
	$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe';");
	$classe = mysql_result($call_classe, "0", "classe");



	/*
	if ($_SESSION['statut'] != "responsable" and $_SESSION['statut'] != "eleve") {
		if(!isset($eleve1)) {
			$call_eleve = mysql_query("SELECT DISTINCT e.login FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' and e.login = c.login) ORDER BY nom,prenom LIMIT 1");
			if(mysql_num_rows($call_eleve)!=0) {
				$ligtmp=mysql_fetch_object($call_eleve);
				$eleve1=$ligtmp->login;
				$eleve2='moyclasse';
				$num_periode=1;
				$periode=1;
				$choix_periode="periode";
			}
		}
	}
	*/

	// Infos DEBUG:
	//echo "<p>classe=$classe<br />eleve1=$eleve1<br />eleve2=$eleve2<br />choix_periode=$choix_periode<br />periode=$periode<br />largeur_imposee_photo=$largeur_imposee_photo</p>\n";


	// Capture des mouvements de la souris et affichage des cadres d'info
	//echo "<script type='text/javascript' src='cadre_info.js'></script>\n";

	// 20121206
	if ($_SESSION['statut'] == "responsable" and $login_eleve != null) {
		while ($current_eleve = mysql_fetch_object($get_eleves)) {
			if($current_eleve->login==$login_eleve) {echo " | <strong>".$current_eleve->prenom." ".$current_eleve->nom."</strong> ";}
			else {echo " | <a href='affiche_eleve.php?login_eleve=".$current_eleve->login."'>".$current_eleve->prenom." ".$current_eleve->nom."</a> ";}
		}
		echo "<br />\n";
	}

	echo "<table summary='Présentation'>\n";
	echo "<tr valign='top'>\n";
	//====================================================================
	// Bande de pilotage:
	echo "<td class='noprint' align='center'>\n";
	//echo "<form action='$_PHP_SELF#graph' name='form_choix_eleves' method='post'>\n";
	echo "<form action='".$_SERVER['PHP_SELF']."#graph' name='form_choix_eleves' method='post'>\n";
	echo add_token_field();
	//echo "<form action='$_PHP_SELF' name='form_choix_eleves' method='POST'>\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";

	echo "<input type='hidden' name='graphe_champ_saisie_avis_fixe' value='$graphe_champ_saisie_avis_fixe' />\n";

	echo "<input type='hidden' name='is_posted' value='y' />\n";

	//echo "\$eleve1=$eleve1 et \$affiche_photo=$affiche_photo<br />";

	// Affichage de la photo si elle existe:
	if((isset($eleve1))&&($affiche_photo!="non")) {
		//$chemin_photos='/var/wwws/gepi/photos';

		$sql="SELECT elenoet FROM eleves WHERE login='$eleve1'";
		$res_elenoet=mysql_query($sql);
		if(mysql_num_rows($res_elenoet)==1) {
			$lig_elenoet=mysql_fetch_object($res_elenoet);
			$elenoet1=$lig_elenoet->elenoet;

			$photo=nom_photo($elenoet1);
			//if("$photo"!="") {
			if ($photo) {
				$dimimg=getimagesize($photo);
				if(!$dimimg) {
					echo "<span style='color:red'>Erreur sur $photo</span>";
				}
				else {

					$largimg=$largeur_imposee_photo;
					$hautimg=round($dimimg[1]*$largeur_imposee_photo/$dimimg[0]);

					echo "<img src='".$photo."' width='$largimg' height='$hautimg' alt='Photo de $eleve1' />\n";
				}
			}

		}
	}

	echo "<p>\n";
	echo "<b>Classe de $classe</b>\n";
	echo "<br />\n";

	if ($_SESSION['statut'] != "responsable" and $_SESSION['statut'] != "eleve") {
		// Choix des élèves:
		$call_eleve = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' and e.login = c.login) order by nom,prenom");
		$nombreligne = mysql_num_rows($call_eleve);

		// Pour afficher le nom/prénom plutôt que le login:
		$tab_nom_prenom_eleve=array();

		echo "Choisir l'élève:<br />\n";
		echo "<select name='eleve1' onchange=\"document.forms['form_choix_eleves'].submit();\">\n";
		$cpt=1;
		$numeleve1=0;
		while($ligne=mysql_fetch_object($call_eleve)) {
			// Le login est la clé liant les tables eleves et j_eleves_classes
			$tab_login_eleve[$cpt]="$ligne->login";
			$tab_nomprenom_eleve[$cpt]="$ligne->nom $ligne->prenom";

			$tab_nom_prenom_eleve["$ligne->login"]=$tab_nomprenom_eleve[$cpt];

			if($tab_login_eleve[$cpt]==$eleve1) {
				$selected=" selected='yes'";
				$numeleve1=$cpt;
			}
			else{
				$selected="";
			}
			echo "<option value='$tab_login_eleve[$cpt]'$selected>$tab_nomprenom_eleve[$cpt]</option>\n";
			$cpt++;
		}
		echo "</select>\n";
		echo "<br />\n";



		echo "et comparer avec:<br />\n";
		echo "<select name='eleve2' onchange=\"document.forms['form_choix_eleves'].submit();\">\n";
		for($cpt=1;$cpt<=$nombreligne;$cpt++) {
			if($tab_login_eleve[$cpt]==$eleve2) {
				$selected=" selected='yes'";
				$numeleve2=$cpt;
			}
			else{
				$selected="";
			}
			echo "<option value='$tab_login_eleve[$cpt]'$selected>$tab_nomprenom_eleve[$cpt]</option>\n";
		}
		//if($affiche_moy_classe!="non") {
			if($eleve2=='moyclasse') {$selected=" selected='yes'";}else{$selected="";}
			if(!isset($eleve2)) {$selected=" selected='yes'";}
			echo "<option value='moyclasse'$selected>Moyenne classe</option>\n";
			if($eleve2=='moymax') {$selected=" selected='yes'";}else{$selected="";}
			echo "<option value='moymax'$selected>Moyenne max.</option>\n";
			if($eleve2=='moymin') {$selected=" selected='yes'";}else{$selected="";}
			echo "<option value='moymin'$selected>Moyenne min.</option>\n";
		//}
		// 20121205
		// On est dans une partie non élève/resp
		/*
		if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
		(($_SESSION['statut']='responsable')&&(getSettingAOui('GepiAccesGraphRangParent')))||
		(($_SESSION['statut']='responsable')&&(getSettingAOui('GepiAccesGraphRangEleve')))) {
		*/
			if($eleve2=='rang_eleve') {$selected=" selected='yes'";}else{$selected="";}
			echo "<option value='rang_eleve'$selected>Rang élève</option>\n";
		//}
		echo "</select>\n";
		echo "<br />\n";

		// Pour passer à l'élève précédent ou au suivant:
		echo "<script type='text/javascript' language='JavaScript'>\n";

		$precedent=$numeleve1-1;
		$suivant=$numeleve1+1;
		echo "precedent=$precedent\n";
		echo "suivant=$suivant\n";
		echo "function eleve_precedent() {
	if(document.getElementById('numeleve1').value>1) {";
	    // On effectue un test pour éviter de tenter de chercher $tab_login_eleve[$precedent] si $precedent=0
	    if($precedent>0) {
	        echo "		document.getElementById('eleve1b').value='$tab_login_eleve[$precedent]';
		document.forms['form_choix_eleves'].submit();";
	    }
		echo "
		return true;
	}
	else{
		document.getElementById('eleve1b').value='';
	}
}

function eleve_suivant() {
	if(document.getElementById('numeleve1').value<$nombreligne) {";
	    if($suivant<$nombreligne+1) {
	        echo "		document.getElementById('eleve1b').value='$tab_login_eleve[$suivant]';
		document.forms['form_choix_eleves'].submit();";
	    }
			echo "
		return true;
	}
	else{
		document.getElementById('eleve1b').value='';
	}
}
</script>\n";

		//echo "<p>\n";
		echo "<input type='hidden' name='numeleve1' id='numeleve1' value='$numeleve1' size='3' />\n";
		// 'eleve1b' est destiné au passage du nom de l'élève par les boutons Précédent/Suivant
	 	// Cette valeur l'emporte sur le contenu de 'eleve1'
		echo "<input type='hidden' name='eleve1b' id='eleve1b' value='' />\n";

	    if($precedent>0) {
			//echo "<input type='button' name='precedent' value='<<' onClick='eleve_precedent();' />\n";
			echo "<a href='javascript:eleve_precedent();'>Élève précédent</a><br />\n";
		}

		//echo "<input type='submit' name='choix_eleves' value='Afficher' />\n";
		echo "<a href=\"javascript:document.forms['form_choix_eleves'].submit();\">Actualiser</a>\n";

	    if($suivant<$nombreligne+1) {
			echo "<br />\n";
			//echo "<input type='button' name='suivant' value='>>' onClick='eleve_suivant();' />\n";
			echo "<a href='javascript:eleve_suivant();'>Élève suivant</a>";
		}
		echo "</p>\n";

		echo "<hr width='150' />\n";

	} else {
		// Cas d'un responsable ou d'un élève :
		// Pas de sélection de l'élève, il est déjà fixé.
		// Pas de sélection non plus de la comparaison : c'est la moyenne de la classe (ou moy min ou max).

		echo "<p>Eleve : ".$prenom_eleve . " " .$nom_eleve."</p>\n";
		echo "<input type='hidden' name='eleve1' value='".$login_eleve."' />\n";
		echo "<input type='hidden' name='login_eleve' value='".$login_eleve."' />\n";
		// 20121205
		if(($affiche_moy_classe=='non')&&($affiche_choix_rang=='n')) {
			// La valeur/masquage sera forcée dans la page draw_graphe*.php
			echo "<input type='hidden' name='eleve2' value='moyclasse' />\n";
		}
		elseif($affiche_choix_rang=='n') {
			echo "et <select name='eleve2'>\n";
			if($eleve2=='moyclasse') {$selected=" selected='yes'";}else{$selected="";}
			if(!isset($eleve2)) {$selected=" selected='yes'";}
			echo "<option value='moyclasse'$selected>Moyenne classe</option>\n";
			if($eleve2=='moymax') {$selected=" selected='yes'";}else{$selected="";}
			echo "<option value='moymax'$selected>Moyenne max.</option>\n";
			if($eleve2=='moymin') {$selected=" selected='yes'";}else{$selected="";}
			echo "<option value='moymin'$selected>Moyenne min.</option>\n";
			echo "</select>\n";
			echo "<br />\n";
		}
		elseif($affiche_moy_classe=='non') {
			echo "<input type='hidden' name='eleve2' value='rang_eleve' />\n";
		}
		else {
			echo "et <select name='eleve2'>\n";
			if($eleve2=='moyclasse') {$selected=" selected='yes'";}else{$selected="";}
			if(!isset($eleve2)) {$selected=" selected='yes'";}
			echo "<option value='moyclasse'$selected>Moyenne classe</option>\n";
			if($eleve2=='moymax') {$selected=" selected='yes'";}else{$selected="";}
			echo "<option value='moymax'$selected>Moyenne max.</option>\n";
			if($eleve2=='moymin') {$selected=" selected='yes'";}else{$selected="";}
			echo "<option value='moymin'$selected>Moyenne min.</option>\n";
			if($eleve2=='rang_eleve') {$selected=" selected='yes'";}else{$selected="";}
			echo "<option value='rang_eleve'$selected>Rang élève</option>\n";
			echo "</select>\n";
			echo "<br />\n";
		}
		echo "<input type='submit' name='choix_eleves' value='Afficher' style='margin-bottom: 3px;'/><br />\n";
	}

	if ($graphe_affiche_deroulant_appreciations=='oui') {
		echo "<div class='appreciations_deroulantes_graphe' style='height:$graphe_hauteur_affichage_deroulant'>";
		//echo "<div style='border:1px solid black; background-color:white; width: 320px;' style='height:$graphe_hauteur_affichage_deroulant'>";
		echo "<b><i><center>Appréciations - $periode</center></i></b>";
		echo "<div id='appreciations_deroulantes'";
		if((isset($graphe_app_deroulantes_taille_police))&&(preg_match("/^[0-9]*$/", $graphe_app_deroulantes_taille_police))&&($graphe_app_deroulantes_taille_police>0)) {
			echo " style='font-size: ".$graphe_app_deroulantes_taille_police."pt'";
		}
		echo ">";
		echo "<span id='appreciations_defile'>";
		//echo $txt_appreciations_deroulantes;
		echo "</span></div></div>";
		echo "<hr width='150' />\n";
	}

	// Choix de la période
	echo "Choisir la période:<br />\n";
	if($choix_periode=='periode') {$checked=" checked='yes'";}else{$checked="";}
	//echo "<input type='radio' name='choix_periode' id='choix_periode' value='periode' checked='true'$checked />\n";
	echo "<input type='radio' name='choix_periode' id='choix_periode' value='periode' $checked onchange=\"document.forms['form_choix_eleves'].submit();\" />\n";
	echo "<select name='periode' onfocus=\"document.getElementById('choix_periode').checked='true'\" onchange=\"document.forms['form_choix_eleves'].submit();\">\n";
	$num_periode_choisie=1;
	for($i=1;$i<$nb_periode;$i++) {
		if($periode==$nom_periode[$i]) {$selected=" selected='yes'";$num_periode_choisie=$i;}else{$selected="";}
		echo "<option value='$nom_periode[$i]'$selected>$nom_periode[$i]</option>\n";
	}
	echo "</select>\n";
	echo "<br />\n";
	if($choix_periode=='toutes_periodes') {$checked=" checked='yes'";}else{$checked="";}
	echo "<label for='choix_toutes_periodes' style='cursor: pointer;'><input type='radio' name='choix_periode' id='choix_toutes_periodes' value='toutes_periodes'$checked onchange=\"document.forms['form_choix_eleves'].submit();\" /> Toutes les périodes</label>\n";

	echo "<hr width='150' />\n";

	//======================================================================
	//======================================================================
	//======================================================================


	$sql="SELECT DISTINCT coef FROM j_groupes_classes WHERE id_classe='$id_classe' AND coef!='0.0';";
	$test_coef_non_nul=mysql_query($sql);
	if(mysql_num_rows($test_coef_non_nul)==0) {
		$affiche_mgen="non";
	}

	//========================
	// PARAMETRES D'AFFICHAGE
	//========================

	echo "<input type='hidden' name='affiche_mgen' value='$affiche_mgen' />\n";
	echo "<input type='hidden' name='affiche_minmax' value='$affiche_minmax' />\n";
	echo "<input type='hidden' name='affiche_moy_annuelle' value='$affiche_moy_annuelle' />\n";
	echo "<input type='hidden' name='type_graphe' value='$type_graphe' />\n";
	echo "<input type='hidden' name='mode_graphe' value='$mode_graphe' />\n";
	echo "<input type='hidden' name='largeur_graphe' value='$largeur_graphe' />\n";
	echo "<input type='hidden' name='hauteur_graphe' value='$hauteur_graphe' />\n";
	echo "<input type='hidden' name='taille_police' value='$taille_police' />\n";
	echo "<input type='hidden' name='epaisseur_traits' value='$epaisseur_traits' />\n";
	echo "<input type='hidden' name='epaisseur_croissante_traits_periodes' value='$epaisseur_croissante_traits_periodes' />\n";
	echo "<input type='hidden' name='temoin_image_escalier' value='$temoin_image_escalier' />\n";
	echo "<input type='hidden' name='tronquer_nom_court' value='$tronquer_nom_court' />\n";
	echo "<input type='hidden' name='affiche_photo' value='$affiche_photo' />\n";
	echo "<input type='hidden' name='largeur_imposee_photo' value='$largeur_imposee_photo' />\n";
	
	//Ajout Eric 11/12/10
	echo "<input type='hidden' name='graphe_affiche_deroulant_appreciations' value='$graphe_affiche_deroulant_appreciations' />\n";
	echo "<input type='hidden' name='graphe_hauteur_affichage_deroulant' value='$graphe_hauteur_affichage_deroulant' />\n";

	echo "<input type='hidden' name='graphe_app_deroulantes_taille_police' value='$graphe_app_deroulantes_taille_police' />\n";

	echo "<input type='hidden' name='affiche_moy_classe' value='$affiche_moy_classe' />\n";

	echo "<input type='hidden' name='parametrer_affichage' value='' />\n";

	if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
	(($_SESSION['statut']=='eleve')&&(getSettingAOui('GepiAccesGraphParamEleve')))||
	(($_SESSION['statut']=='responsable')&&(getSettingAOui('GepiAccesGraphParamParent')))) {
		echo "<a href='".$_SERVER['PHP_SELF']."' onClick='document.forms[\"form_choix_eleves\"].parametrer_affichage.value=\"y\";document.forms[\"form_choix_eleves\"].submit();return false;'>Paramétrer l'affichage</a>.<br />\n";

		echo "<hr width='150' />\n";
	}


	//======================================================================
	//======================================================================
	//======================================================================

	echo "<script type='text/javascript'>
	function fct_desactivation_infobulle() {
		if(document.getElementById('desactivation_infobulle')) {
			if(document.getElementById('desactivation_infobulle').checked==true) {
				desactivation_infobulle='y';
			}
			else{
				desactivation_infobulle='n';
			}
		}
	}
</script>\n";

	echo "<label for='desactivation_infobulle' style='cursor: pointer;'><input type='checkbox' name='desactivation_infobulle' id='desactivation_infobulle' value='y' onchange='fct_desactivation_infobulle();' ";
	if($desactivation_infobulle=="y") {echo "checked ";}
	echo "/> Désactiver l'affichage des appréciations</label>\n";
	if($desactivation_infobulle=="y") {
		echo "<script type='text/javascript'>desactivation_infobulle='y';</script>\n";
	}
	else{
		echo "<script type='text/javascript'>desactivation_infobulle='n';</script>\n";
	}

	echo "<input type='hidden' id='passer_a_recapitulatif_avis' name='passer_a_recapitulatif_avis' value='n' />";

	//echo "<input type='text' id='id_truc' name='truc' value='' />";
	//echo "</form>\n";

	//================
	// Déplacement: boireaus 20090727
	// Initialisation:
	$texte_saisie_avis_fixe="";
	//================

	//if(($_SESSION['statut']=='professeur')&&(getSettingValue('GepiRubConseilProf')=="yes")) {
	if(
		(($_SESSION['statut']=='professeur')&&(getSettingValue('GepiRubConseilProf')=="yes"))||
		(($_SESSION['statut']=='scolarite')&&(getSettingValue('GepiRubConseilScol')=="yes"))||
		(($_SESSION['statut']=='cpe')&&((getSettingValue('GepiRubConseilCpe')=="yes")||(getSettingValue('GepiRubConseilCpeTous')=="yes")))
	) {

		$droit_saisie_avis="y";
		// Contrôler si le prof est PP de l'élève
		if($_SESSION['statut']=='professeur') {
			$droit_saisie_avis="n";
			$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."' AND login='".$eleve1."' AND id_classe='$id_classe';";
			$verif_pp=mysql_query($sql);
			if(mysql_num_rows($verif_pp)>0) {
				$droit_saisie_avis="y";
			}
		}
		elseif(($_SESSION['statut']=='cpe')&&(getSettingValue('GepiRubConseilCpeTous')!="yes")) {
			$droit_saisie_avis="n";
			$sql="SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='".$_SESSION['login']."' AND e_login='".$eleve1."';";
			$verif_cpe=mysql_query($sql);
			if(mysql_num_rows($verif_cpe)>0) {
				$droit_saisie_avis="y";
			}
		}

		//================
		// Ajout: boireaus 20090115
		// Initialisation:
		//$texte_saisie_avis_fixe="";
		//================
		if($droit_saisie_avis=="y") {
			//if ($_POST['choix_periode']=="periode") {
			if ($choix_periode=="periode") {
				$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe' AND login='$eleve1'  AND periode='$num_periode_choisie';";
				$test_appartenance_ele_classe_periode=mysql_query($sql);
				if(mysql_num_rows($test_appartenance_ele_classe_periode)>0) {
					// $num_periode_choisie
					$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' AND num_periode='$num_periode_choisie';";
					//echo "$sql<br />";
					$test_verr_per=mysql_query($sql);
					$lig_verr_per=mysql_fetch_object($test_verr_per);
					if($lig_verr_per->verouiller!='O') {
	
						$current_eleve_avis="";
						// ***** AJOUT POUR LES MENTIONS *****
						$current_eleve_mention="";
						// ***** FIN DE L'AJOUT POUR LES MENTIONS *****
						$sql="SELECT * FROM avis_conseil_classe WHERE login='$eleve1' AND periode='$num_periode_choisie';";
						//echo "$sql<br />";
						$res_avis=mysql_query($sql);
						if(mysql_num_rows($res_avis)>0) {
							$lig_avis=mysql_fetch_object($res_avis);
							$current_eleve_avis=$lig_avis->avis;
							// ***** AJOUT POUR LES MENTIONS *****
							$current_eleve_mention=$lig_avis->id_mention;
							// ***** FIN DE L'AJOUT POUR LES MENTIONS *****
						}


						echo "<div style='display:none;'>
<textarea name='no_anti_inject_current_eleve_login_ap' id='no_anti_inject_current_eleve_login_ap' rows='5' cols='20' wrap='virtual' onchange=\"changement()\">$current_eleve_avis</textarea>
<input type='hidden' name='num_periode_saisie' value='$num_periode_choisie' />
<input type='hidden' name='eleve_saisie_avis' value='$eleve1' />
<input type='hidden' name='enregistrer_avis' id='enregistrer_avis' value='' />
</div>\n";

						// ***** AJOUT POUR LES MENTIONS *****
						echo "<div style='display:none;'>
<textarea name='current_eleve_login_me' id='current_eleve_login_me' rows='1' cols='2' wrap='virtual' onchange=\"changement()\">$current_eleve_mention</textarea>
<input type='hidden' name='enregistrer_mention' id='enregistrer_mention' value='' />
</div>\n";
						// ***** FIN DE L'AJOUT POUR LES MENTIONS *****

							echo "<script type='text/javascript'>
		function save_avis(mode) {
			document.getElementById('no_anti_inject_current_eleve_login_ap').value=document.getElementById('no_anti_inject_current_eleve_login_ap2').value;
			document.getElementById('enregistrer_avis').value='y';

			if(document.getElementById('current_eleve_login_me2')) {
				document.getElementById('current_eleve_login_me').value=document.getElementById('current_eleve_login_me2').value;
				document.getElementById('enregistrer_mention').value='y';
			}
			//alert('La mention actuelle est : '+document.getElementById('current_eleve_login_me').value+'.');

			if(mode=='recap') {
				document.getElementById('passer_a_recapitulatif_avis').value='y';
			}
			else {
				document.getElementById('passer_a_recapitulatif_avis').value='n';
			}

			if(mode=='suivant') {
				eleve_suivant();
			}
			else {
				document.forms['form_choix_eleves'].submit();
			}
		}
	</script>\n";
	
						//================
						// Ajout: boireaus 20090115
	
						// Pour forcer la valeur avant de la mettre en choix dans les paramètres:
						//$graphe_champ_saisie_avis_fixe="y";
	
						if($graphe_champ_saisie_avis_fixe!="y") {
						//================
							echo "<br />\n<a href=\"#graph\" onClick=\"afficher_div('saisie_avis','y',100,100);return false;\">Saisir l'avis du conseil</a>\n";
	
							$titre="Avis du conseil de classe: $lig_verr_per->nom_periode";
	
							//$texte="<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."#graph' method='post'>\n";
							$texte="<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."#graph' method='post'>\n";
							$texte.=add_token_field();
							$texte.="<div style='text-align:center;'>\n";
							$texte.="<textarea name='no_anti_inject_current_eleve_login_ap2' id='no_anti_inject_current_eleve_login_ap2' rows='5' cols='60' wrap='virtual' onchange=\"changement()\">";
							//$texte.="\n";
							$texte.="$current_eleve_avis";
							$texte.="</textarea>\n";

							// ***** AJOUT POUR LES MENTIONS *****
							if(test_existence_mentions_classe($id_classe)) {
								$texte.="<br/>\n";
								$texte.=ucfirst($gepi_denom_mention)." : ";
	
								$texte.=champ_select_mention('current_eleve_login_me2',$id_classe,$current_eleve_mention);
								/*
								// Essai d'ajout de listes déroulantes en vue de l'intégration des mentions au bulletin :
								$selectedF="";
								$selectedM="";
								$selectedE="";
								$selectedB="";
								if($current_eleve_mention=='F') {$selectedF=" selected";}
								else if($current_eleve_mention=='M') {$selectedM=" selected";}
								else if($current_eleve_mention=='E') {$selectedE=" selected";}
								else {$selectedB=" selected";}
								$texte.="<select name='current_eleve_login_me2'>\n";
								$texte.="<option value='B'$selectedB> </option>\n";
								$texte.="<option value='E'$selectedE>Encouragements</option>\n";
								$texte.="<option value='M'$selectedM>Mention honorable</option>\n";
								$texte.="<option value='F'$selectedF>Félicitations</option>\n";
								$texte.="</select>\n";
								*/
								$texte.="<br/>\n";
							}
							// ***** FIN DE L'AJOUT POUR LES MENTIONS *****
			

							//$texte.="<input type='submit' NAME='ok1' value='Enregistrer' />\n";
							$texte.="<input type='button' NAME='ok1' value='Enregistrer' onClick=\"save_avis('');\" />\n";
							if($suivant<$nombreligne+1) {
								$texte.=" <input type='button' NAME='ok2' value='Enregistrer et passer au suivant' onClick=\"save_avis('suivant');\" />\n";
							}
							elseif(acces('/saisie/saisie_avis2.php', $_SESSION['statut'])) {
								$texte.=" <input type='button' NAME='ok2' value='Enregistrer et passer au récapitulatif' onClick=\"save_avis('recap');\" />\n";
							}
	
							// METTRE AUSSI UN BOUTON POUR Enregistrer puis lancer eleve_suivant();
							//require("insere_cmnt_type.php");
							if((($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiRubConseilProf")=='yes')&&(getSettingValue('CommentairesTypesPP')=='yes'))
							||(($_SESSION['statut'] == 'scolarite')&&(getSettingValue("GepiRubConseilScol")=='yes')&&(getSettingValue('CommentairesTypesScol')=='yes'))
							||(($_SESSION['statut'] == 'cpe')&&((getSettingValue("GepiRubConseilCpe")=='yes')||(getSettingValue("GepiRubConseilCpeTous")=='yes'))&&(getSettingValue('CommentairesTypesCpe')=='yes'))) {
								$texte.=div_cmnt_type();
							}

							$texte.="</div>\n";
							$texte.="</form>\n";
	
							$tabdiv_infobulle[]=creer_div_infobulle('saisie_avis',$titre,"",$texte,"",35,0,'y','y','n','n');
						}
						else {
							$texte_saisie_avis_fixe="<div style='border:1px solid black;'>\n";
							$texte_saisie_avis_fixe.="<p class='bold' style='text-align:center;'>Saisie de l'avis du conseil</p>\n";
							$texte_saisie_avis_fixe.="<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."#graph' method='post'>\n";
							$texte_saisie_avis_fixe.=add_token_field();
							$texte_saisie_avis_fixe.="<div style='text-align:center;'>\n";
							$texte_saisie_avis_fixe.="<textarea name='no_anti_inject_current_eleve_login_ap2' id='no_anti_inject_current_eleve_login_ap2' rows='5' cols='60' wrap='virtual' onchange=\"changement()\">";
							//$texte_saisie_avis_fixe.="\n";
							$texte_saisie_avis_fixe.="$current_eleve_avis";
							$texte_saisie_avis_fixe.="</textarea>\n";

							// ***** AJOUT POUR LES MENTIONS *****
							if(test_existence_mentions_classe($id_classe)) {
								$texte_saisie_avis_fixe.="<br/>\n";
								$texte_saisie_avis_fixe.=ucfirst($gepi_denom_mention)." : ";
	
								$texte_saisie_avis_fixe.=champ_select_mention('current_eleve_login_me2',$id_classe,$current_eleve_mention);
								/*
								// Essai d'ajout de listes déroulantes en vue de l'intégration des mentions au bulletin :
								$selectedF="";
								$selectedM="";
								$selectedE="";
								$selectedB="";
								if($current_eleve_mention=='F') {$selectedF=" selected";}
								else if($current_eleve_mention=='M') {$selectedM=" selected";}
								else if($current_eleve_mention=='E') {$selectedE=" selected";}
								else {$selectedB=" selected";}
								$texte_saisie_avis_fixe.="<select name='current_eleve_login_me2'>\n";
								$texte_saisie_avis_fixe.="<option value='B'$selectedB> </option>\n";
								$texte_saisie_avis_fixe.="<option value='E'$selectedE>Encouragements</option>\n";
								$texte_saisie_avis_fixe.="<option value='M'$selectedM>Mention honorable</option>\n";
								$texte_saisie_avis_fixe.="<option value='F'$selectedF>Félicitations</option>\n";
								$texte_saisie_avis_fixe.="</select>\n";
								*/
								$texte_saisie_avis_fixe.="<br/>\n";
							}
							// ***** FIN DE L'AJOUT POUR LES MENTIONS *****

	
							//$texte_saisie_avis_fixe.="<input type='submit' NAME='ok1' value='Enregistrer' />\n";
							$texte_saisie_avis_fixe.="<br /><input type='button' NAME='ok1' value='Enregistrer' onClick=\"save_avis('');\" />\n";
							if($suivant<$nombreligne+1) {
								$texte_saisie_avis_fixe.=" <input type='button' NAME='ok2' value='Enregistrer et passer au suivant' onClick=\"save_avis('suivant');\" />\n";
							}
							elseif(acces('/saisie/saisie_avis2.php', $_SESSION['statut'])) {
								$texte_saisie_avis_fixe.=" <input type='button' NAME='ok2' value='Enregistrer et passer au récapitulatif' onClick=\"save_avis('recap');\" />\n";
							}

							// METTRE AUSSI UN BOUTON POUR Enregistrer puis lancer eleve_suivant();
							//require("insere_cmnt_type.php");
							if((($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiRubConseilProf")=='yes')&&(getSettingValue('CommentairesTypesPP')=='yes'))
							||(($_SESSION['statut'] == 'scolarite')&&(getSettingValue("GepiRubConseilScol")=='yes')&&(getSettingValue('CommentairesTypesScol')=='yes'))
							||(($_SESSION['statut'] == 'cpe')&&((getSettingValue("GepiRubConseilCpe")=='yes')||(getSettingValue("GepiRubConseilCpeTous")=='yes'))&&(getSettingValue('CommentairesTypesCpe')=='yes'))) {
								$texte_saisie_avis_fixe.=div_cmnt_type();
							}


							$texte_saisie_avis_fixe.="</div>\n";
							$texte_saisie_avis_fixe.="</form>\n";
							$texte_saisie_avis_fixe.="</div>\n";
						}
					}
				}
			}
			//elseif($_POST['choix_periode']=="toutes_periodes") {
			elseif($choix_periode=="toutes_periodes") {
				// On doit trouver quelle période est ouverte en saisie d'avis.

				$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' AND verouiller!='O';";
				$res_verr_per=mysql_query($sql);
				if(mysql_num_rows($res_verr_per)==1) {
					// On ne propose la saisie d'avis que si une seule période est ouverte en saisie (N ou P)
					// ... pour le moment.
					$lig_per=mysql_fetch_object($res_verr_per);

					$num_periode_choisie=$lig_per->num_periode;

					$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe' AND login='$eleve1'  AND periode='$num_periode_choisie';";
					$test_appartenance_ele_classe_periode=mysql_query($sql);
					if(mysql_num_rows($test_appartenance_ele_classe_periode)>0) {
						$current_eleve_avis="";
						// ***** AJOUT POUR LES MENTIONS *****
						$current_eleve_mention="";
						// ***** FIN DE L'AJOUT POUR LES MENTIONS *****
						$sql="SELECT * FROM avis_conseil_classe WHERE login='$eleve1' AND periode='$num_periode_choisie';";
						//echo "$sql<br />";
						$res_avis=mysql_query($sql);
						if(mysql_num_rows($res_avis)>0) {
							$lig_avis=mysql_fetch_object($res_avis);
							$current_eleve_avis=$lig_avis->avis;
							$current_eleve_mention=$lig_avis->id_mention;
						}
	
						echo "<div style='display:none;'>
<textarea name='no_anti_inject_current_eleve_login_ap' id='no_anti_inject_current_eleve_login_ap' rows='5' cols='20' wrap='virtual' onchange=\"changement()\">$current_eleve_avis</textarea>
<input type='hidden' name='num_periode_saisie' value='$num_periode_choisie' />
<input type='hidden' name='eleve_saisie_avis' value='$eleve1' />
<input type='hidden' name='enregistrer_avis' id='enregistrer_avis' value='' />
</div>\n";

						// ***** AJOUT POUR LES MENTIONS *****
						echo "<div style='display:none;'>
<textarea name='current_eleve_login_me' id='current_eleve_login_me' rows='1' cols='2' wrap='virtual' onchange=\"changement()\">$current_eleve_mention</textarea>
<input type='hidden' name='enregistrer_mention' id='enregistrer_mention' value='' />
</div>\n";
						// ***** FIN DE L'AJOUT POUR LES MENTIONS *****

						echo "<script type='text/javascript'>
	function save_avis(mode) {
		document.getElementById('no_anti_inject_current_eleve_login_ap').value=document.getElementById('no_anti_inject_current_eleve_login_ap2').value;
		document.getElementById('enregistrer_avis').value='y';

		if(document.getElementById('current_eleve_login_me2')) {
			document.getElementById('current_eleve_login_me').value=document.getElementById('current_eleve_login_me2').value;
			document.getElementById('enregistrer_mention').value='y';
		}
		//alert('La mention actuelle est : '+document.getElementById('current_eleve_login_me').value+'.');

		if(mode=='recap') {
			document.getElementById('passer_a_recapitulatif_avis').value='y';
		}
		else {
			document.getElementById('passer_a_recapitulatif_avis').value='n';
		}

		if(mode=='suivant') {
			eleve_suivant();
		}
		else {
			document.forms['form_choix_eleves'].submit();
		}
	}
</script>\n";
	
						//================
						// Ajout: boireaus 20090115
						if($graphe_champ_saisie_avis_fixe!="y") {
						//================
							echo "<br />\n<a href=\"#graph\" onClick=\"afficher_div('saisie_avis','y',100,100);\">Saisir l'avis du conseil</a>\n";
	
							$titre="Avis du conseil de classe: $lig_per->nom_periode";
	
							//$texte="<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."#graph' method='post'>\n";
							$texte="<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."#graph' method='post'>\n";
							$texte.=add_token_field();
							$texte.="<div style='text-align:center;'>\n";
							$texte.="<textarea name='no_anti_inject_current_eleve_login_ap2' id='no_anti_inject_current_eleve_login_ap2' rows='5' cols='60' wrap='virtual' onchange=\"changement()\">";
							//$texte.="\n";
							$texte.="$current_eleve_avis";
							$texte.="</textarea>\n";

							// ***** AJOUT POUR LES MENTIONS *****
							if(test_existence_mentions_classe($id_classe)) {
								$texte.="<br/>\n";
								$texte.=ucfirst($gepi_denom_mention)." : ";
	
								$texte.=champ_select_mention('current_eleve_login_me2',$id_classe,$current_eleve_mention);
								/*
								// Essai d'ajout de listes déroulantes en vue de l'intégration des mentions au bulletin :
								$selectedF="";
								$selectedM="";
								$selectedE="";
								$selectedB="";
								if($current_eleve_mention=='F') {$selectedF=" selected";}
								else if($current_eleve_mention=='M') {$selectedM=" selected";}
								else if($current_eleve_mention=='E') {$selectedE=" selected";}
								else {$selectedB=" selected";}
								$texte.="<select name='current_eleve_login_me2'>\n";
								$texte.="<option value='B'$selectedB> </option>\n";
								$texte.="<option value='E'$selectedE>Encouragements</option>\n";
								$texte.="<option value='M'$selectedM>Mention honorable</option>\n";
								$texte.="<option value='F'$selectedF>Félicitations</option>\n";
								$texte.="</select>\n";
								*/
								$texte.="<br/>\n";
							}
							// ***** FIN DE L'AJOUT POUR LES MENTIONS *****

							//$texte.="<input type='submit' NAME='ok1' value='Enregistrer' />\n";
							$texte.="<input type='button' NAME='ok1' value='Enregistrer' onClick=\"save_avis('');\" />\n";
							if($suivant<$nombreligne+1) {
								$texte.=" <input type='button' NAME='ok2' value='Enregistrer et passer au suivant' onClick=\"save_avis('suivant');\" />\n";
							}
							elseif(acces('/saisie/saisie_avis2.php', $_SESSION['statut'])) {
								$texte.=" <input type='button' NAME='ok2' value='Enregistrer et passer au récapitulatif' onClick=\"save_avis('recap');\" />\n";
							}
	
							// METTRE AUSSI UN BOUTON POUR Enregistrer puis lancer eleve_suivant();
							//require("insere_cmnt_type.php");
							if((($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiRubConseilProf")=='yes')&&(getSettingValue('CommentairesTypesPP')=='yes'))
							||(($_SESSION['statut'] == 'scolarite')&&(getSettingValue("GepiRubConseilScol")=='yes')&&(getSettingValue('CommentairesTypesScol')=='yes'))
							||(($_SESSION['statut'] == 'cpe')&&((getSettingValue("GepiRubConseilCpe")=='yes')||(getSettingValue("GepiRubConseilCpeTous")=='yes')))) {
								$texte.=div_cmnt_type();
							}

							$texte.="</div>\n";
							$texte.="</form>\n";
	
							$tabdiv_infobulle[]=creer_div_infobulle('saisie_avis',$titre,"",$texte,"",35,0,'y','y','n','n');
						}
						else {
							$texte_saisie_avis_fixe="<div style='border:1px solid black;'>\n";
							$texte_saisie_avis_fixe.="<p class='bold' style='text-align:center;'>Saisie de l'avis du conseil: $lig_per->nom_periode</p>\n";
							$texte_saisie_avis_fixe.="<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."#graph' method='post'>\n";
							$texte_saisie_avis_fixe.=add_token_field();
							$texte_saisie_avis_fixe.="<div style='text-align:center;'>\n";
							$texte_saisie_avis_fixe.="<textarea name='no_anti_inject_current_eleve_login_ap2' id='no_anti_inject_current_eleve_login_ap2' rows='5' cols='60' wrap='virtual' onchange=\"changement()\">";
							//$texte_saisie_avis_fixe.="\n";
							$texte_saisie_avis_fixe.="$current_eleve_avis";
							$texte_saisie_avis_fixe.="</textarea>\n";

							// ***** AJOUT POUR LES MENTIONS *****
							if(test_existence_mentions_classe($id_classe)) {
								$texte_saisie_avis_fixe.="<br/>\n";
								$texte_saisie_avis_fixe.=ucfirst($gepi_denom_mention)." : ";
	
								$texte_saisie_avis_fixe.=champ_select_mention('current_eleve_login_me2',$id_classe,$current_eleve_mention);
								/*
								// Essai d'ajout de listes déroulantes en vue de l'intégration des mentions au bulletin :
								$selectedF="";
								$selectedM="";
								$selectedE="";
								$selectedB="";
								if($current_eleve_mention=='F') {$selectedF=" selected";}
								else if($current_eleve_mention=='M') {$selectedM=" selected";}
								else if($current_eleve_mention=='E') {$selectedE=" selected";}
								else {$selectedB=" selected";}
								$texte_saisie_avis_fixe.="<select name='current_eleve_login_me2'>\n";
								$texte_saisie_avis_fixe.="<option value='B'$selectedB> </option>\n";
								$texte_saisie_avis_fixe.="<option value='E'$selectedE>Encouragements</option>\n";
								$texte_saisie_avis_fixe.="<option value='M'$selectedM>Mention honorable</option>\n";
								$texte_saisie_avis_fixe.="<option value='F'$selectedF>Félicitations</option>\n";
								$texte_saisie_avis_fixe.="</select>\n";
								*/
								$texte_saisie_avis_fixe.="<br/>\n";
							}
							// ***** FIN DE L'AJOUT POUR LES MENTIONS *****
			

							//$texte_saisie_avis_fixe.="<input type='submit' NAME='ok1' value='Enregistrer' />\n";
							$texte_saisie_avis_fixe.="<br /><input type='button' NAME='ok1' value='Enregistrer' onClick=\"save_avis('');\" />\n";
							if($suivant<$nombreligne+1) {
								$texte_saisie_avis_fixe.=" <input type='button' NAME='ok2' value='Enregistrer et passer au suivant' onClick=\"save_avis('suivant');\" />\n";
							}
							elseif(acces('/saisie/saisie_avis2.php', $_SESSION['statut'])) {
								$texte_saisie_avis_fixe.=" <input type='button' NAME='ok2' value='Enregistrer et passer au récapitulatif' onClick=\"save_avis('recap');\" />\n";
							}
	
							// METTRE AUSSI UN BOUTON POUR Enregistrer puis lancer eleve_suivant();
							//require("insere_cmnt_type.php");
							if((($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiRubConseilProf")=='yes')&&(getSettingValue('CommentairesTypesPP')=='yes'))
							||(($_SESSION['statut'] == 'scolarite')&&(getSettingValue("GepiRubConseilScol")=='yes')&&(getSettingValue('CommentairesTypesScol')=='yes'))
							||(($_SESSION['statut'] == 'cpe')&&((getSettingValue("GepiRubConseilCpe")=='yes')||(getSettingValue("GepiRubConseilCpeTous")=='yes'))&&(getSettingValue('CommentairesTypesCpe')=='yes'))) {
								$texte_saisie_avis_fixe.=div_cmnt_type();
							}

							$texte_saisie_avis_fixe.="</div>\n";
							$texte_saisie_avis_fixe.="</form>\n";
							$texte_saisie_avis_fixe.="</div>\n";
						}
					}
				}
			}
		}
	}


	echo "<div id='debug_fixe' style='position: fixed; bottom: 20%; right: 5%;'></div>";

	echo "</form>\n";



	echo "</td>\n";

	echo "<td>\n";
	//====================================================================

	// Récupération des infos personnelles sur l'élève (nom, prénom, sexe, date de naissance et redoublant)
	// Et calcul de l'age (si le serveur est à l'heure;o).
	
	if((isset($eleve1) AND $_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve")
		OR (($_SESSION['statut'] == "responsable" OR $_SESSION['statut'] == "eleve") AND $periode != "")
		OR (($_SESSION['statut'] == "responsable" OR $_SESSION['statut'] == "eleve") AND $choix_periode == "toutes_periodes")) {
		// Informations sur l'élève $eleve1:
		$sql="SELECT * FROM eleves WHERE login='$eleve1'";
		$result_infos_eleve=mysql_query($sql);
		if(mysql_num_rows($result_infos_eleve)==1) {
			$ligne=mysql_fetch_object($result_infos_eleve);
			$sexe1=$ligne->sexe;
			$nom1=$ligne->nom;
			$prenom1=$ligne->prenom;
			$naissance1=explode("-",$ligne->naissance);
			$ereno1=$ligne->ereno;
		}



		$anneedatenais1=$naissance1[0];
		$moisdatenais1=$naissance1[1];
		$jourdatenais1=$naissance1[2];

		$aujourdhui = getdate();
		$mois = $aujourdhui['mon'];
		//$mjour = $aujourdhui['mday'];
		$jour = $aujourdhui['mday'];
		$annee = $aujourdhui['year'];

		if($mois>$moisdatenais1) {
			$age1=$annee-$anneedatenais1;
			$precision1=$mois-$moisdatenais1;
			$precision1="ans et $precision1 mois";
		}
		else{
			if($mois<$moisdatenais1) {
				$age1=$annee-$anneedatenais1-1;
				$precision1=12-($moisdatenais1-$mois);
				$precision1="ans et $precision1 mois";
			}
			else{
				if($jour>=$jourdatenais1) {
					$age1=$annee-$anneedatenais1;
					$precision1="ans ce mois-ci";
				}
				else{
					$age1=$annee-$anneedatenais1-1;
					$precision1="ans et 1 de plus ce mois-ci";
				}
			}
		}

		$sql="SELECT * FROM j_eleves_regime WHERE login='$eleve1'";
		$result_infos_eleve=mysql_query($sql);

		if(mysql_num_rows($result_infos_eleve)==1) {
			$ligne=mysql_fetch_object($result_infos_eleve);
			$doublant1=$ligne->doublant;
			if("$doublant1"=="R") {
				if($sexe1=="M") {$doublant1="Redoublant";}else{$doublant1="Redoublante";}
			}
		}
	//}

		// Initialisation de la liste des matières.
		$liste_matieres="";
		$matiere=array();
		$matiere_nom=array();
		$txt_appreciations_deroulantes="";

		// Séries:
		if($choix_periode=="periode") {
			$nb_series=2;
			$serie=array();
			for($i=1;$i<=$nb_series;$i++) {$serie[$i]="";}

			//echo "Elève: $eleve1<br />periode=$periode<br />";

			//$num_periode
			$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' AND nom_periode='".$periode."'";
			$resultat=mysql_query($sql);
			if(mysql_num_rows($resultat)==0) {
				//??? Toutes les périodes ?
				echo "<p>PB periode... $periode</p>";
			}
			else{
				$ligne=mysql_fetch_object($resultat);
				$num_periode=$ligne->num_periode;
			}


			// Des coefficients sont-ils saisis pour les différentes matières dans le cadre du calcul de la moyenne générale?
			//$call_classe_infos = mysql_query("SELECT DISTINCT  m.* FROM matieres m,j_classes_matieres_professeurs j WHERE (m.matiere = j.id_matiere AND j.id_classe='$id_classe')");


			$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
			if ($affiche_categories == "y") {
				$affiche_categories = true;
			} else {
				$affiche_categories = false;
			}


			// On calcule les moyennes:
			// Doivent être initialisées, les variables:
			// - $id_classe : la classe concernée
			// - $periode_num
			$periode_num=$num_periode;

			$coefficients_a_1="non";
			$affiche_graph="n";
			// 20121205
			if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
			(($_SESSION['statut']=='responsable')&&(getSettingAOui('GepiAccesGraphRangParent')))||
			(($_SESSION['statut']=='responsable')&&(getSettingAOui('GepiAccesGraphRangEleve')))) {
				$affiche_rang="y";
			}
			include('../lib/calcul_moy_gen.inc.php');

			// Récupérer la ligne de l'élève courant
			// Remplir $liste_matieres, $serie[1] et $serie[2] (selon que c'est moymin, moymax, moyclasse ou un autre élève)
			// Remplir seriemin et seriemax?
			// Récupérer les appréciations et générer les infobulles

			$tab_imagemap=array();
			$tab_imagemap_commentaire_present=array();

			// On recherche l'élève courant:
			$indice_eleve1=-1;
			for($loop=0;$loop<count($current_eleve_login);$loop++) {
				//if($current_eleve_login[$loop]==$eleve1) {
				if(my_strtolower($current_eleve_login[$loop])==my_strtolower($eleve1)) {
					$indice_eleve1=$loop;
					break;
				}
			}

			if($indice_eleve1==-1) {
				
				echo "<div style='margin: 5% 2em; padding: 1em; border: 1px dotted #2a6167'>\n";
				echo "<div style='text-align: center; margin-bottom: 1em; font-weight: bold; color: #ee2222'>";
				if((isset($tab_nom_prenom_eleve))&&isset($tab_nom_prenom_eleve["$eleve1"])) {
					echo $tab_nom_prenom_eleve["$eleve1"];
				}
				else {
					echo $eleve1;
				}
				echo "</div>\n";
				echo "<p>L'élève a changé de classe, est arrivé en cours d'année<br />ou a quitté l'établissement, mais il n'est pas dans la classe de $classe<br />pour la période $periode.</p>\n";
				echo "<p>Si ces informations vous semblent erronées,<br />\n";
				echo "vous pouvez <a href=\"javascript:centrerpopup('$gepiPath/gestion/contacter_admin.php',600, 480,'scrollbars=yes,statusbar=no,resizable=yes')\">contacter l'administrateur</a>.</p>\n";
				echo "</div>\n";

				require("../lib/footer.inc.php");
				die();
			}

			$mgen[1]=$moy_gen_eleve[$indice_eleve1];
			if(preg_match("/^[0-9.,]*$/", $mgen[1])) {
				$mgen[1]=round(preg_replace('/,/', '.', $mgen[1]),1);
			}


			// On recherche l'élève2 et on récupère la moyenne générale 2:
			$indice_eleve2=-1;
			//echo "\$eleve2=$eleve2<br />";
			// 20121205
			if(($eleve2!='moyclasse')&&($eleve2!='moymin')&&($eleve2!='moymax')&&($eleve2!='rang_eleve')) {
				for($loop=0;$loop<count($current_eleve_login);$loop++) {
					if($current_eleve_login[$loop]==$eleve2) {
						$indice_eleve2=$loop;
						break;
					}
				}

				$mgen[2]=$moy_gen_eleve[$indice_eleve2];
			}
			elseif($eleve2=='moyclasse') {
				$mgen[2]=$moy_generale_classe;
				//$mgen[2]=5;
			}
			elseif($eleve2=='moymin') {
				$mgen[2]=$moy_min_classe;
			}
			elseif($eleve2=='moymax') {
				$mgen[2]=$moy_max_classe;
			}
			// 20121205
			elseif($eleve2=='rang_eleve') {
				if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
				(($_SESSION['statut']=='responsable')&&(getSettingAOui('GepiAccesGraphRangParent')))||
				(($_SESSION['statut']=='responsable')&&(getSettingAOui('GepiAccesGraphRangEleve')))) {
					$mgen[2]=get_rang_eleve($eleve1, $id_classe, $periode_num, "n", "y");
				}
				else {
					$mgen[2]="-";
				}
			}

			if(preg_match("/^[0-9.,]*$/", $mgen[2])) {
				$mgen[2]=round(preg_replace('/,/', '.', $mgen[2]),1);
			}

			// On remplit $liste_matieres, $serie[1], les tableaux d'appréciations et on génère les infobulles
			$cpt=0;
			for($loop=0;$loop<count($current_group);$loop++) {
				if(isset($current_eleve_note[$loop][$indice_eleve1])) {
					// L'élève suit l'enseignement

					if($liste_matieres!="") {
						$liste_matieres.="|";
						$serie[1].="|";
						$serie[2].="|";
						$seriemin.="|";
						$seriemax.="|";
					}

					// Groupe:
					$id_groupe=$current_group[$loop]["id"];

					// Matières
					$matiere[$cpt]=$current_group[$loop]["matiere"]["matiere"];
					$matiere_nom[$cpt]=$current_group[$loop]["matiere"]["nom_complet"];
					$liste_matieres.=$matiere[$cpt];

					// Elève 1:
					if($current_eleve_statut[$loop][$indice_eleve1]!="") {
						// Mettre le statut pose des problèmes pour le tracé de la courbe... abs, disp,... passent pour des zéros
						//$serie[1].=$current_eleve_statut[$loop][$indice_eleve1];
						$serie[1].="-";
					}
					else {
						$serie[1].=$current_eleve_note[$loop][$indice_eleve1];
					}

					// Elève 2:
					if($indice_eleve2!=-1) {
						// Si le deuxième élève suit le même enseignement:
						if(isset($current_eleve_note[$loop][$indice_eleve2])) {
							if($current_eleve_statut[$loop][$indice_eleve2]!="") {
								// Mettre le statut pose des problèmes pour le tracé de la courbe... abs, disp,... passent pour des zéros
								//$serie[2].=$current_eleve_statut[$loop][$indice_eleve2];
								$serie[2].="-";
							}
							else {
								$serie[2].=$current_eleve_note[$loop][$indice_eleve2];
							}
						}
						else {
								$serie[2].="-";
						}
					}
					elseif($eleve2=='moyclasse') {
						$serie[2].=$current_classe_matiere_moyenne[$loop];
					}
					elseif($eleve2=='moymin') {
						//$serie[2].=min($current_eleve_note[$loop]);
						$serie[2].=$moy_min_classe_grp[$loop];
					}
					elseif($eleve2=='moymax') {
						//$serie[2].=max($current_eleve_note[$loop]);
						$serie[2].=$moy_max_classe_grp[$loop];
					}
					elseif($eleve2=='rang_eleve') {
						if(isset($current_eleve_rang[$loop][$indice_eleve1])) {
							if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
							(($_SESSION['statut']='responsable')&&(getSettingAOui('GepiAccesGraphRangParent')))||
							(($_SESSION['statut']='responsable')&&(getSettingAOui('GepiAccesGraphRangEleve')))) {
								$serie[2].=$current_eleve_rang[$loop][$indice_eleve1];
							}
						}
					}

					// Série min et série max pour les bandes min/max:
					// Avec min($current_eleve_note[$loop]) on n'a que les élève de la classe pas ceux de tout l'enseignement si à cheval sur plusieurs classes
					//$seriemin.=min($current_eleve_note[$loop]);
					$seriemin.=$moy_min_classe_grp[$loop];
					//$seriemax.=max($current_eleve_note[$loop]);
					$seriemax.=$moy_max_classe_grp[$loop];


					// Dispositif de restriction des accès aux appréciations pour les comptes responsables/eleves
					if($tab_acces_app[$num_periode]=="y") {
					//==========================================================
						//=========================
						// MODIF: boireaus 20081214
						//$sql="SELECT ma.* FROM matieres_appreciations ma, j_groupes_matieres jgm WHERE (ma.login='$eleve1' AND ma.periode='$num_periode' AND jgm.id_matiere='$current_matiere' AND ma.id_groupe=jgm.id_groupe)";

						//$sql="SELECT ma.* FROM matieres_appreciations ma, j_groupes_matieres jgm WHERE (ma.login='$eleve1' AND ma.periode='$num_periode' AND jgm.id_matiere='".$matiere[$cpt]."' AND ma.id_groupe=jgm.id_groupe AND jgm.id_groupe='$id_groupe');";
						$sql="SELECT ma.* FROM matieres_appreciations ma, j_groupes_matieres jgm WHERE (ma.login='$eleve1' AND ma.periode='$num_periode' AND ma.id_groupe=jgm.id_groupe AND jgm.id_groupe='$id_groupe');";
						//=========================
						affiche_debug("$sql<br />");
						$app_eleve_query=mysql_query($sql);

						if(mysql_num_rows($app_eleve_query)>0) {
							$ligtmp=mysql_fetch_object($app_eleve_query);
							
							$titre_bulle=htmlspecialchars($matiere_nom[$cpt])." (<i>".htmlspecialchars($periode)."</i>)";
							$texte_bulle="<div align='center'>\n";
							$texte_bulle.=htmlspecialchars($ligtmp->appreciation)."\n";
							$texte_bulle.="</div>\n";
							//$tabdiv_infobulle[]=creer_div_infobulle('div_app_'.$cpt,$titre_bulle,"",$texte_bulle,"",14,0,'y','y','n','n');

							//Ajout Eric pour le déroulant des appréciations
							$app_tmp = $ligtmp->appreciation;
							$app_tmp = str_replace("\n", "", $app_tmp);
							$app_tmp = str_replace("\r\n", "", $app_tmp);
							$app_tmp = str_replace("\r", "", $app_tmp); 

							$txt_appreciations_deroulantes.="<li><b>".htmlspecialchars($matiere_nom[$cpt])." : </b></br>".$app_tmp."</br></li>";
							
							if($type_graphe=='etoile'){

								$tabdiv_infobulle[]=creer_div_infobulle('div_app_'.$cpt,$titre_bulle,"",$texte_bulle,"",20,0,'y','y','n','n');
							}
							else{
								$tabdiv_infobulle[]=creer_div_infobulle('div_app_'.$cpt,$titre_bulle,"",$texte_bulle,"",20,0,'n','n','n','n');
							}

							$tab_imagemap_commentaire_present[]=$cpt;
						}
					}

					//$tab_nom_matiere[]=$current_group[$loop]["matiere"]["matiere"];
					$tab_nom_matiere[]=$matiere[$cpt];
					// On stocke dans un tableau, les numéros $cpt correspondant aux matières que l'élève a.
					$tab_imagemap[]=$cpt;

					$cpt++;
				}
				else{
					// L'élève n'a pas cette matière.
					echo "<!-- $eleve1 n'a pas la matière ".$current_group[$loop]["matiere"]["matiere"]." -->\n";
				}
			}
			//=========================================================
			//=========================================================
			//=========================================================


			// Ajout Eric 11/12/2010 Boite déroulante pour les appréciations.
			if ($graphe_affiche_deroulant_appreciations=='oui') {
				$graphe_hauteur_affichage_deroulant=$graphe_hauteur_affichage_deroulant."px";
				echo "<script type='text/javascript'>
				// <![CDATA[
					var pas=1;
					var h_fen='$graphe_hauteur_affichage_deroulant';
					function scrollmrq(){
						if (parseInt(mrq.style.top) > -h_mrq ) 
						mrq.style.top = parseInt(mrq.style.top)-pas+'px'
						else mrq.style.top=parseInt(h_fen)+'px'
					}
					function init_mrq(){
						mrq=document.getElementById('appreciations_defile');
						fen=document.getElementById('appreciations_deroulantes');
						fen.onmouseover=function(){stoc=pas;pas=0};
						fen.onmouseout=function(){pas=stoc};fen.style.height=h_fen;
						h_mrq=mrq.offsetHeight;
						with(mrq.style){position='absolute';top=h_fen;}
						setInterval('scrollmrq()',50);
					}
		
					document.getElementById('appreciations_defile').innerHTML='".addslashes($txt_appreciations_deroulantes)."';
		
					window.onload =init_mrq;
				//]]>
				</script>\n";
				/*
				echo "<div class='appreciations_deroulantes_graphe' style='height:$graphe_hauteur_affichage_deroulant'>";
				echo "<b><i><center>Appréciations - $periode</center></i></b>";
				echo "<div id='appreciations_deroulantes'>";
				echo "<span id='appreciations_defile'>";
				echo $txt_appreciations_deroulantes;
				echo "</span></div></div>";
				*/
			}
			// Fin ajout Eric
			
			// Avis du conseil de classe
			$temoin_avis_present="n";
			// Dispositif de restriction des accès aux appréciations pour les comptes responsables/eleves
			if($tab_acces_app[$num_periode]=="y") {
				$sql="SELECT * FROM avis_conseil_classe WHERE login='$eleve1' AND periode='$num_periode' ORDER BY periode";
				$res_avis=mysql_query($sql);
				if(mysql_num_rows($res_avis)>0) {
					$lig_avis=mysql_fetch_object($res_avis);
					if($lig_avis->avis!="") {
						$titre_bulle="Avis du Conseil de classe";

						$texte_bulle="<div align='center'>\n";
						//$texte_bulle.=htmlspecialchars($lig_avis->avis)."\n";
						$texte_bulle.=nl2br($lig_avis->avis)."\n";
						// ***** AJOUT POUR LES MENTIONS *****
						if((!isset($tableau_des_mentions_sur_le_bulletin))||(!is_array($tableau_des_mentions_sur_le_bulletin))||(count($tableau_des_mentions_sur_le_bulletin)==0)) {
							$tableau_des_mentions_sur_le_bulletin=get_mentions();
						}

						//if(($lig_avis->id_mention!='')&&($lig_avis->mention!='-')&&($lig_avis->mention!='B')) {
						if(isset($tableau_des_mentions_sur_le_bulletin[$lig_avis->id_mention])) {
							$texte_bulle.="<br />\n";
							$texte_bulle.="<b>".ucfirst($gepi_denom_mention)."</b> : ";
							//$texte_bulle.=htmlspecialchars(traduction_mention($lig_avis->mention))."\n";
							$texte_bulle.=$tableau_des_mentions_sur_le_bulletin[$lig_avis->id_mention]."\n";
						}
						// ***** FIN DE L'AJOUT POUR LES MENTIONS *****
						$texte_bulle.="</div>\n";
						//$tabdiv_infobulle[]=creer_div_infobulle('div_app_'.$cpt,$titre_bulle,"",$texte_bulle,"",14,0,'y','y','n','n');
						$tabdiv_infobulle[]=creer_div_infobulle('div_avis_1',$titre_bulle,"",$texte_bulle,"",20,0,'n','n','n','n');

						$temoin_avis_present="y";
					}
				}
			}

			// ImageMap:

			//$chaine_map="<map name='imagemap'>\n";
			// $largeurGrad -> 50
			// $largeurBandeDroite=80;
			// $largeur=$largeurTotale-$largeurGrad-$largeurBandeDroite;
			// $largeur=$largeur_graphe-$largeurGrad-$largeurBandeDroite;
			// $nbMat=count($matiere);
			// $largeurMat=round($largeur/$nbMat);

			if(count($tab_imagemap)>0) {
				$largeurGrad=50;
				$largeurBandeDroite=80;
				$largeur_utile=$largeur_graphe-$largeurGrad-$largeurBandeDroite;
				$nbMat=count($tab_imagemap);
				$largeurMat=round($largeur_utile/$nbMat);

				echo "<map name='imagemap'>\n";
				for($i=0;$i<count($tab_imagemap);$i++) {
					$x0=$largeurGrad+$i*$largeurMat;
					$x1=$x0+$largeurMat;
					//echo "<area href=\"javascript:return false;\" onMouseover=\"document.getElementById('div_matiere_".$tab_imagemap[$i]."').style.display=''\" onMouseout=\"document.getElementById('div_matiere_".$tab_imagemap[$i]."').style.display='none'\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";
					//echo "<area href=\"#\" onClick='return false;' onMouseover=\"div_info('div_matiere_',$tab_imagemap[$i],'affiche');\" onMouseout=\"div_info('div_matiere_',$tab_imagemap[$i],'cache');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";
					if(in_array($tab_imagemap[$i],$tab_imagemap_commentaire_present)) {
						//echo "<area href=\"#\" onClick='return false;' onMouseover=\"afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20);\" onMouseout=\"cacher_div('div_app_".$tab_imagemap[$i]."');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">\n";

						if($click_plutot_que_survol_aff_app=="y") {
							//echo "<area href=\"#\" onClick=\"delais_afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20,1,$largeurMat,$hauteur_rect_delais_afficher_div);return false;\" onMouseout=\"cacher_div('div_app_".$tab_imagemap[$i]."');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\" alt=\"\">\n";
							echo "<area href=\"#\" onClick=\"affiche_eleve_afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20);return false;\" onMouseout=\"cacher_div('div_app_".$tab_imagemap[$i]."');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\" alt=\"\">\n";
						}
						else {
							//echo "<area href=\"#\" onClick='return false;' onMouseover=\"delais_afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20,$duree_delais_afficher_div,$largeurMat,$hauteur_rect_delais_afficher_div);\" onMouseout=\"cacher_div('div_app_".$tab_imagemap[$i]."');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\" alt=\"\">\n";
							echo "<area href=\"#\" onClick=\"affiche_eleve_afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20);return false;\" onMouseover=\"affiche_eleve_delais_afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20,$duree_delais_afficher_div,$largeurMat,$hauteur_rect_delais_afficher_div);\" onMouseout=\"cacher_div('div_app_".$tab_imagemap[$i]."');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\" alt=\"\">\n";
						}
					}
				}

				$x0=$largeurGrad+$i*$largeurMat;
				$x1=$largeur_graphe;
				//echo "<area href=\"#\" onClick='return false;' onMouseover=\"div_info('div_avis_','1','affiche');\" onMouseout=\"div_info('div_avis_','1','cache');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";
				if($temoin_avis_present=="y") {
					//echo "<area href=\"#\" onClick='return false;' onMouseover=\"afficher_div('div_avis_1','y',-10,20);\" onMouseout=\"cacher_div('div_avis_1');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";

					if($click_plutot_que_survol_aff_app=="y") {
						echo "<area href=\"#\" onClick=\"affiche_eleve_delais_afficher_div('div_avis_1','y',-10,20,1,$largeurMat,$hauteur_rect_delais_afficher_div);return false;\" onMouseout=\"cacher_div('div_avis_1');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\" alt=\"\">";
					}
					else {
						echo "<area href=\"#\" onClick='return false;' onMouseover=\"affiche_eleve_delais_afficher_div('div_avis_1','y',-10,20,$duree_delais_afficher_div,$largeurMat,$hauteur_rect_delais_afficher_div);\" onMouseout=\"cacher_div('div_avis_1');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\" alt=\"\">";
					}
				}

				echo "</map>\n";




				//***********************************************************
				// Image Map pour le graphe en étoile
				// J'ai repris une portion du code de draw_graphe_star.php
				// pour juste récupérer les coordonnées des textes de matières
				echo "<map name='imagemap_star'>\n";

				$largeurTotale=$largeur_graphe;
				$hauteurTotale=$hauteur_graphe;
				$legendy[2]=$choix_periode;
				$x0=round($largeurTotale/2);
				if($legendy[2]=='Toutes_les_périodes') {
					$L=round(($hauteurTotale-6*(ImageFontHeight($taille_police)+5))/2);
					//$y0=round(3*(ImageFontHeight($taille_police))+5)+$L;
					$y0=round(4*(ImageFontHeight($taille_police))+5)+$L;
				}
				else{
					$L=round(($hauteurTotale-4*(ImageFontHeight($taille_police)+5))/2);
					$y0=round(2*(ImageFontHeight($taille_police))+5)+$L;
				}

				$pi=pi();

				function coordcirc($note,$angle) {
					// $note sur 20 (s'assurer qu'il y a le point pour séparateur et non la virgule)
					// $angle en degrés
					global $pi;
					global $L;
					global $x0;
					global $y0;

					$x=round($note*$L*cos($angle*$pi/180)/20)+$x0;
					$y=round($note*$L*sin($angle*$pi/180)/20)+$y0;

					return array($x,$y);
				}

				//=================================
				// Polygone 20/20
				unset($tab20);
				$tab20=array();
				for($i=0;$i<$nbMat;$i++) {
					$angle=round($i*360/$nbMat);
					$tab=coordcirc(20,$angle);

					$tab20[]=$tab[0];
					$tab20[]=$tab[1];
				}
				//ImageFilledPolygon($img,$tab20,count($tab20)/2,$bande2);
				//=================================

				//=================================
				// Légendes Matières: -> Coordonnées des textes de matières
				for($i=0;$i<count($tab20)/2;$i++) {
					$angle=round($i*360/$nbMat);

					//$texte=$matiere[$i+1];
					//$texte=$matiere_nom_long[$i+1];
					$texte=$tab_nom_matiere[$i];

					$tmp_taille_police=$taille_police;

					if($angle==0) {
						$x=$tab20[2*$i]+5;

						$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police);

						if($x_verif>$largeurTotale) {
							for($j=$taille_police;$j>1;$j--) {
								$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j);
								if($x_verif<=$largeurTotale) {
									break;
								}
							}
							if($x_verif>$largeurTotale) {
								$j=1;
							}
							$tmp_taille_police=$j;
						}

						$y=round($tab20[2*$i+1]+$angle*(ImageFontHeight($taille_police)+2)/90);
					}
					elseif(($angle>0)&&($angle<90)) {
						$x=$tab20[2*$i]+5;
						$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police);

						if($x_verif>$largeurTotale) {
							for($j=$taille_police;$j>1;$j--) {
								$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j);
								if($x_verif<=$largeurTotale) {
									break;
								}
							}
							if($x_verif>$largeurTotale) {
								$j=1;
							}
							$tmp_taille_police=$j;
						}

						$y=round($tab20[2*$i+1]+$angle*(ImageFontHeight($taille_police)+2)/90);
					}
					elseif($angle==90) {
						$x=round($tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($taille_police)/2);
						$y=$tab20[2*$i+1]+ImageFontHeight($taille_police)+2;
					}
					elseif(($angle>90)&&($angle<180)) {
						$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($taille_police)+5);

						if($x<0) {
							for($j=$taille_police;$j>1;$j--) {
								$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($j)+5);
								if($x>=0) {
									break;
								}
							}
							if($x<0) {
								$x=1;
								$j=1;
							}
							$tmp_taille_police=$j;
						}

						$y=round($tab20[2*$i+1]+($angle-90)*(ImageFontHeight($taille_police)-2)/90);
					}
					elseif($angle==180) {
						$x=$tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($taille_police)-5;

						if($x<0) {
							for($j=$taille_police;$j>1;$j--) {
								$x=$tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($j)-5;
								if($x>=0) {
									break;
								}
							}
							if($x<0) {
								$x=1;
								$j=1;
							}
							$tmp_taille_police=$j;
						}

						$y=round($tab20[2*$i+1]-ImageFontHeight($taille_police)/2);
					}
					elseif(($angle>180)&&($angle<270)) {
						$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($taille_police)+5);

						if($x<0) {
							for($j=$taille_police;$j>1;$j--) {
								$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($j)+5);
								if($x>=0) {
									break;
								}
							}
							if($x<0) {
								$x=1;
								$j=1;
							}
							$tmp_taille_police=$j;
						}

						$y=round($tab20[2*$i+1]-($angle-180)*(ImageFontHeight($taille_police)-2)/90);
					}
					elseif($angle==270) {
						$x=round($tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($taille_police)/2);
						//$y=$tab20[2*$i+1]-ImageFontHeight($taille_police)-2;
						$y=$tab20[2*$i+1]-2*ImageFontHeight($taille_police)-2;
					}
					else{
						$x=$tab20[2*$i]+5;
						$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police);

						if($x_verif>$largeurTotale) {
							for($j=$taille_police;$j>1;$j--) {
								$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j);
								if($x_verif<=$largeurTotale) {
									break;
								}
							}
							if($x_verif>$largeurTotale) {
								$j=1;
							}
							$tmp_taille_police=$j;
						}

						$y=round($tab20[2*$i+1]-(90-($angle-270))*(ImageFontHeight($taille_police)-2)/90);
					}


					$x2=$x+mb_strlen($texte)*ImageFontWidth($tmp_taille_police);
					$y2=$y+20;

					//echo "<area href=\"#\" onClick='return false;' onMouseover=\"div_info('div_matiere_',$tab_imagemap[$i],'affiche');\" onMouseout=\"div_info('div_matiere_',$tab_imagemap[$i],'cache');\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\">\n";
					if(in_array($tab_imagemap[$i],$tab_imagemap_commentaire_present)) {
						//echo "<area href=\"#\" onClick='return false;' onMouseover=\"afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20);\" onMouseout=\"cacher_div('div_app_".$tab_imagemap[$i]."');\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\">\n";

						if($click_plutot_que_survol_aff_app=="y") {
							//echo "<area href=\"#\" onClick=\"delais_afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20,1,50,50);return false;\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\" alt=\"\">\n";
							echo "<area href=\"#\" onClick=\"affiche_eleve_afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20);return false;\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\" alt=\"\">\n";
						}
						else {
							//echo "<area href=\"#\" onClick='return false;' onMouseover=\"delais_afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20,$duree_delais_afficher_div,50,50);\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\" alt=\"\">\n";
							echo "<area href=\"#\" onClick=\"affiche_eleve_afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20);return false;\" onMouseover=\"affiche_eleve_delais_afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20,$duree_delais_afficher_div,50,50);\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\" alt=\"\">\n";
						}
					}

				}
				//=================================
				echo "</map>\n";
				//***********************************************************

			}







			// Graphe:
			echo "<a name='graph'></a>\n";
			//echo "<img src='draw_artichow_fig7.php?temp1=$temp1&temp2=$temp2&etiquette=$etiq&titre=$graph_title&v_legend1=$v_legend1&v_legend2=$v_legend2&compteur=$compteur&nb_data=3'>";
			//echo "<img src='draw_artichow_fig7.php?temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=$eleve2&compteur=$compteur&nb_data=3'>";
			//echo "<p>img src='draw_artichow_fig7.php?&temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=$eleve2&compteur=$compteur&nb_series=$nb_series&id_classe=$id_classe'</p>";
			//echo "<img src='draw_artichow_fig7.php?&temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=$eleve2&compteur=$compteur&nb_series=$nb_series&id_classe=$id_classe'>";
			//echo "<img src='draw_artichow_fig7.php?&temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=$eleve2&compteur=$compteur&nb_series=$nb_series&id_classe=$id_classe&mgen1=$mgen[1]&mgen2=$mgen[2]&largeur_graphe=$largeur_graphe&hauteur_graphe=$hauteur_graphe&taille_police=$taille_police'>";

			//echo "<a href=\"javascript:document.getElementById('div_matiere_2').style.display=''\" onMouseover=\"document.getElementById('div_matiere_2').style.display=''\" onMouseout=\"document.getElementById('div_matiere_2').style.display='none'\">";

			//echo "\$type_graphe=".$type_graphe."<br />\n";

			if($type_graphe=='courbe') {
				if(count($matiere)>0) {

					if($mode_graphe=='png') {
						echo "<img src='draw_graphe.php?";
						//echo "&amp;temp1=$serie[1]";
						echo "temp1=$serie[1]";
						echo "&amp;temp2=$serie[2]";
						echo "&amp;etiquette=$liste_matieres";
						echo "&amp;titre=$graph_title";
						echo "&amp;v_legend1=$eleve1";
						echo "&amp;v_legend2=$eleve2";
						echo "&amp;compteur=$compteur";
						echo "&amp;nb_series=$nb_series";
						echo "&amp;id_classe=$id_classe";
						if($affiche_mgen=='oui') {
							echo "&amp;mgen1=$mgen[1]";
							echo "&amp;mgen2=$mgen[2]";
						}
						//echo "&amp;periode=$periode";
						echo "&amp;periode=".rawurlencode($periode);
						echo "&amp;largeur_graphe=$largeur_graphe";
						echo "&amp;hauteur_graphe=$hauteur_graphe";
						echo "&amp;taille_police=$taille_police";
						echo "&amp;epaisseur_traits=$epaisseur_traits";
						echo "&amp;epaisseur_croissante_traits_periodes=$epaisseur_croissante_traits_periodes";
						if($affiche_minmax=="oui") {
							echo "&amp;seriemin=$seriemin";
							echo "&amp;seriemax=$seriemax";
						}
						echo "&amp;tronquer_nom_court=$tronquer_nom_court";
						//echo "'>";
						//echo "&amp;temoin_imageps=$temoin_imageps";
						echo "&amp;temoin_image_escalier=$temoin_image_escalier";
						if($affiche_moy_classe!='oui') {
							echo "&amp;avec_moy_classe=n";
						}
						echo "' style='border: 1px solid black;' height='$hauteur_graphe' width='$largeur_graphe' alt='Graphe' ";
						echo "usemap='#imagemap' ";
						echo "/>\n";
						//echo "</a>\n";

					}
					else {

						//echo "<hr />";
						//echo "<embed src='rect.svg' width='600' height='400' />\n";


						//echo "<hr />";
						//echo "<embed src='draw_graphe_svg.php?";
						echo "<div id='graphe_svg' style='position: relative;'>\n";

						# Image Map
						//$chaine_map="<map name='imagemap'>\n";
						// $largeurGrad -> 50
						// $largeurBandeDroite=80;
						// $largeur=$largeurTotale-$largeurGrad-$largeurBandeDroite;
						// $largeur=$largeur_graphe-$largeurGrad-$largeurBandeDroite;
						// $nbMat=count($matiere);
						// $largeurMat=round($largeur/$nbMat);

						if(count($tab_imagemap)>0) {
							$largeurGrad=50;
							$largeurBandeDroite=80;
							$largeur_utile=$largeur_graphe-$largeurGrad-$largeurBandeDroite;
							$nbMat=count($tab_imagemap);
							$largeurMat=round($largeur_utile/$nbMat);

							//echo "<map name='imagemap'>\n";
							for($i=0;$i<count($tab_imagemap);$i++) {
								$x0=$largeurGrad+$i*$largeurMat;
								$x1=$x0+$largeurMat;
								//echo "<area href=\"javascript:return false;\" onMouseover=\"document.getElementById('div_matiere_".$tab_imagemap[$i]."').style.display=''\" onMouseout=\"document.getElementById('div_matiere_".$tab_imagemap[$i]."').style.display='none'\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";
								//echo "<area href=\"#\" onClick='return false;' onMouseover=\"div_info('div_matiere_',$tab_imagemap[$i],'affiche');\" onMouseout=\"div_info('div_matiere_',$tab_imagemap[$i],'cache');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";
								if(in_array($tab_imagemap[$i],$tab_imagemap_commentaire_present)) {

									if($click_plutot_que_survol_aff_app=="y") {
										echo "<div onclick=\"affiche_eleve_delais_afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20,1,$largeurMat,$hauteur_rect_delais_afficher_div);\" onMouseout=\"cacher_div('div_app_".$tab_imagemap[$i]."');\" style='position: absolute; left: ".$x0."px; top: 0px; width: ".$largeurMat."px; height: ".$hauteur_graphe."px;";
										//echo " border: 1px dashed green;";
										echo "'></div>\n";
									}
									else {
										echo "<div onMouseover=\"affiche_eleve_delais_afficher_div('div_app_".$tab_imagemap[$i]."','y',-10,20,$duree_delais_afficher_div,$largeurMat,$hauteur_rect_delais_afficher_div);\" onMouseout=\"cacher_div('div_app_".$tab_imagemap[$i]."');\" style='position: absolute; left: ".$x0."px; top: 0px; width: ".$largeurMat."px; height: ".$hauteur_graphe."px;";
										//echo " border: 1px dashed green;";
										echo "'></div>\n";
									}
								}
							}


							$x0=$largeurGrad+$i*$largeurMat;
							$x1=$largeur_graphe;
							//echo "<area href=\"#\" onClick='return false;' onMouseover=\"div_info('div_avis_','1','affiche');\" onMouseout=\"div_info('div_avis_','1','cache');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";
							if($temoin_avis_present=="y") {
								if($click_plutot_que_survol_aff_app=="y") {
									echo "<div onclick=\"affiche_eleve_delais_afficher_div('div_avis_1','y',-10,20,1,$largeurMat,$hauteur_rect_delais_afficher_div);\" onMouseout=\"cacher_div('div_avis_1');\" style='position: absolute; left: ".$x0."px; top: 0px; width: ".$largeurMat."px; height: ".$hauteur_graphe."px;";
									//echo " border: 1px dashed green;";
									echo "'></div>\n";
								}
								else {
									echo "<div onMouseover=\"affiche_eleve_delais_afficher_div('div_avis_1','y',-10,20,$duree_delais_afficher_div,$largeurMat,$hauteur_rect_delais_afficher_div);\" onMouseout=\"cacher_div('div_avis_1');\" style='position: absolute; left: ".$x0."px; top: 0px; width: ".$largeurMat."px; height: ".$hauteur_graphe."px;";
									//echo " border: 1px dashed green;";
									echo "'></div>\n";
								}
							}

							//echo "</map>\n";
						}


						echo "<object data='draw_graphe_svg.php?";
						//echo "<img src='draw_graphe_svg.php?";
						//echo "&amp;temp1=$serie[1]";
						echo "temp1=$serie[1]";
						echo "&amp;temp2=$serie[2]";
						echo "&amp;etiquette=$liste_matieres";
						echo "&amp;titre=$graph_title";
						echo "&amp;v_legend1=$eleve1";
						echo "&amp;v_legend2=$eleve2";
						echo "&amp;compteur=$compteur";
						echo "&amp;nb_series=$nb_series";
						echo "&amp;id_classe=$id_classe";
						if($affiche_mgen=='oui') {
							echo "&amp;mgen1=$mgen[1]";
							echo "&amp;mgen2=$mgen[2]";
						}
						//echo "&amp;periode=$periode";
						echo "&amp;periode=".rawurlencode($periode);
						echo "&amp;largeur_graphe=$largeur_graphe";
						echo "&amp;hauteur_graphe=$hauteur_graphe";
						echo "&amp;taille_police=$taille_police";
						echo "&amp;epaisseur_traits=$epaisseur_traits";
						echo "&amp;epaisseur_croissante_traits_periodes=$epaisseur_croissante_traits_periodes";
						if($affiche_minmax=="oui") {
							echo "&amp;seriemin=$seriemin";
							echo "&amp;seriemax=$seriemax";
						}
						echo "&amp;tronquer_nom_court=$tronquer_nom_court";
						//echo "'>";
						//echo "&amp;temoin_imageps=$temoin_imageps";
						echo "&amp;temoin_image_escalier=$temoin_image_escalier";
						if($affiche_moy_classe!='oui') {
							echo "&amp;avec_moy_classe=n";
						}

						echo "'";

						//echo "' style='border: 1px solid black;' height='$hauteur_graphe' width='$largeur_graphe' alt='Graphe' ";
						//echo "usemap='#imagemap' ";

						//echo " name='SVG1' width='$largeur_graphe' height='$hauteur_graphe' type='image/svg-xml' pluginspage='http://www.adobe.com/svg/viewer/install/'";
						//echo " name='SVG1' width='$largeur_graphe' height='$hauteur_graphe' type='image/svg-xml'";
						echo " width='$largeur_graphe' height='$hauteur_graphe'";
						//echo " width='$largeur_graphe' height='$hauteur_graphe' type='image/svg-xml'";

						echo " type=\"image/svg+xml\"></object>\n";
						//echo " type=\"image/svg+xml\" usemap='#imagemap'></object>\n";

						echo "</div>\n";


						//echo "/>\n";
						//echo "</a>\n";
					}
				}
			}
			else{
				if(count($matiere)>0) {
					echo "<img src='draw_graphe_star.php?";
					//echo "&amp;temp1=$serie[1]";
					echo "temp1=$serie[1]";
					echo "&amp;temp2=$serie[2]";
					echo "&amp;etiquette=$liste_matieres";
					echo "&amp;titre=$graph_title";
					echo "&amp;v_legend1=$eleve1";
					echo "&amp;v_legend2=$eleve2";
					echo "&amp;compteur=$compteur";
					echo "&amp;nb_series=$nb_series";
					echo "&amp;id_classe=$id_classe";
					if($affiche_mgen=='oui') {
						echo "&amp;mgen1=$mgen[1]";
						echo "&amp;mgen2=$mgen[2]";
					}
					//echo "&amp;periode=$periode";
					echo "&amp;periode=".rawurlencode($periode);
					echo "&amp;largeur_graphe=$largeur_graphe";
					echo "&amp;hauteur_graphe=$hauteur_graphe";
					echo "&amp;taille_police=$taille_police";
					echo "&amp;epaisseur_traits=$epaisseur_traits";
					echo "&amp;epaisseur_croissante_traits_periodes=$epaisseur_croissante_traits_periodes";
					if($affiche_minmax=="oui") {
						echo "&amp;seriemin=$seriemin";
						echo "&amp;seriemax=$seriemax";
					}
					echo "&amp;tronquer_nom_court=$tronquer_nom_court";
					//echo "'>";
					//echo "&amp;temoin_imageps=$temoin_imageps";
					echo "&amp;temoin_image_escalier=$temoin_image_escalier";
					if($affiche_moy_classe!='oui') {
						echo "&amp;avec_moy_classe=n";
					}
					echo "' style='border: 1px solid black;' height='$hauteur_graphe' width='$largeur_graphe' alt='Graphe' ";
					echo "usemap='#imagemap_star' ";
					echo "/>\n";
					//echo "</a>\n";
				}
			}
			//===================================

			//echo "<img src='draw_artichow_fig7.php?eleves=$eleves&temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=$eleve2&compteur=$compteur&nb_series=$nb_series'>";


			/*
			if(isset($_SESSION['graphe_largeurMat'])) {echo "\$_SESSION['graphe_largeurMat']=".$_SESSION['graphe_largeurMat']."<br />";}
			if(isset($_SESSION['graphe_x0'])) {echo "\$_SESSION['graphe_x0']=".$_SESSION['graphe_x0']."<br />";}
			*/

			// $largeurGrad -> 50
			// $largeurBandeDroite=80;
			// $largeur=$largeurTotale-$largeurGrad-$largeurBandeDroite;
			// $nbMat=count($matiere);
			// $largeurMat=round($largeur/$nbMat);


		}
		else{
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			// On va afficher toutes les périodes

			$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
			if ($affiche_categories == "y") {
				$affiche_categories = true;
			} else {
				$affiche_categories = false;
			}

			// Récupération de la liste des matières dans l'ordre souhaité:
			if ($affiche_categories) {
				/*
				$sql="SELECT DISTINCT jgc.id_groupe, m.* FROM matieres m,
															j_groupes_classes jgc,
															j_groupes_matieres jgm,
															j_matieres_categories_classes jmcc 
														WHERE (m.matiere=jgm.id_matiere AND 
															jgm.id_groupe=jgc.id_groupe AND 
															jgc.id_classe='$id_classe' AND 
															jgc.categorie_id = jmcc.categorie_id) 
															ORDER BY jmcc.priority,jgc.priorite,m.matiere";
				//ORDER BY jmcc.priority,mc.priority,jgc.priorite,m.nom_complet
				*/
				$sql="SELECT DISTINCT jgc.id_groupe, m.* FROM matieres m,
															j_groupes_classes jgc,
															j_groupes_matieres jgm,
															j_matieres_categories_classes jmcc,
															matieres_categories mc
														WHERE (mc.id=jmcc.categorie_id AND 
															jgc.id_classe=jmcc.classe_id AND 
															m.matiere=jgm.id_matiere AND 
															jgm.id_groupe=jgc.id_groupe AND 
															jgc.id_classe='$id_classe' AND 
															jgc.categorie_id = jmcc.categorie_id AND 
															jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n')) 
															ORDER BY jmcc.priority,mc.priority,jgc.priorite,m.nom_complet";
			}
			else{
				//$sql="SELECT DISTINCT jgc.id_groupe, m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm WHERE (m.matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe') ORDER BY jgc.priorite,m.matiere";
				$sql="SELECT DISTINCT jgc.id_groupe, m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm WHERE (m.matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe' AND 
				jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n')) ORDER BY jgc.priorite,m.matiere";
			}
			//echo "$sql<br />";

			$call_classe_infos = mysql_query($sql);
			$nombre_lignes = mysql_num_rows($call_classe_infos);
			affiche_debug("\$nombre_lignes=$nombre_lignes<br />");

			$id_groupe=array();
			$liste_matieres="";
			$matiere=array();
			$matiere_nom=array();
			// Pour le déroulant des appréciations
			$txt_appreciations_deroulantes="";

			$cpt=0;
			// Boucle sur l'ordre des matières:
			// On ne va retenir que les matières du premier élève.
			while($ligne=mysql_fetch_object($call_classe_infos)) {

				$sql="SELECT * FROM j_eleves_groupes jeg WHERE (jeg.login='$eleve1' AND jeg.id_groupe='$ligne->id_groupe');";
				//echo "$sql<br />";
				affiche_debug("$sql<br />");
				$eleve_option_query=mysql_query($sql);
				//if(mysql_num_rows($eleve_option_query)==0) {
				if(mysql_num_rows($eleve_option_query)!=0) {
					$id_groupe[$cpt]=$ligne->id_groupe;
					$matiere[$cpt]=$ligne->matiere;
					$matiere_nom[$cpt]=$ligne->nom_complet;

					if($liste_matieres=="") {
						$liste_matieres="$matiere[$cpt]";
					}
					else{
						$liste_matieres=$liste_matieres."|$matiere[$cpt]";
					}
					// DEBUG
					// echo "$liste_matieres<br />";

					$cpt++;
				}
			}

			// Toutes les périodes...
			$sql="SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode";
			$result_periode=mysql_query($sql);
			$nb_periode=mysql_num_rows($result_periode);

			// Initialisation des séries:
			$nb_series=$nb_periode;
			for($i=1;$i<=$nb_series;$i++) {$serie[$i]="";}


			unset($tab_imagemap);
			$tab_imagemap=array();

			//$temoin_au_moins_une_vraie_moyenne="";
			// $liste_temp va contenir les séries à envoyer au graphe et éventuellement les moyennes générales sur les différentes périodes.
			$liste_temp="";
			$cpt=1;
			while($lign_periode=mysql_fetch_object($result_periode)) {
				// DEBUG
				//echo "<p>Période $cpt<br />";

				$num_periode[$cpt]=$lign_periode->num_periode;
				//$nom_periode[$cpt]=$lign_periode->nom_periode;
				$tab_imagemap[$cpt]=array();

				$coefficients_a_1="non";
				$affiche_graph="n";
				$periode_num=$num_periode[$cpt];

				// Réinitialisations:
				unset($current_eleve_login);
				unset($current_group);
				unset($moy_gen_eleve);
				unset($current_eleve_note);
				unset($current_eleve_statut);
				// Puis extraction de la période $periode_num
				include('../lib/calcul_moy_gen.inc.php');

				// On recherche l'indice de l'élève courant: $eleve1
				$indice_eleve1=-1;
				for($loop=0;$loop<count($current_eleve_login);$loop++) {
					//if($current_eleve_login[$loop]==$eleve1) {
					if(my_strtolower($current_eleve_login[$loop])==my_strtolower($eleve1)) {
						$indice_eleve1=$loop;
						break;
					}
				}

				// DEBUG
				//echo "\$indice_eleve1=$indice_eleve1<br />";

				if($indice_eleve1==-1) {
					// L'élève n'est pas dans la classe sur la période?
					for($loop=0;$loop<count($matiere);$loop++) {
						if($serie[$cpt]!="") {$serie[$cpt].="|";}
						$serie[$cpt].="-";
					}

					$mgen[$cpt]="-";
				}
				else {
					// Moyenne générale de l'élève $eleve1 sur la période $cpt
					$mgen[$cpt]=$moy_gen_eleve[$indice_eleve1];

					// DEBUG
					//echo "\$mgen[$cpt]=$mgen[$cpt]<br />";

					// Boucle sur les groupes:
					for($j=0;$j<count($id_groupe);$j++) {
						if($serie[$cpt]!="") {$serie[$cpt].="|";} // Cette ligne impose que si un élève n'a pas la première matière de la liste sur une période, on mette quand même quelque chose (tiret,... mais pas vide sans quoi on a un décalage dans le nombre de champs entre $liste_matieres et $serie[$cpt])

						// Recherche de l'indice du groupe retourné en $current_group par calcul_moy_gen.inc.php
						$indice_groupe=-1;
						for($loop=0;$loop<count($current_group);$loop++) {
							if($current_group[$loop]['id']==$id_groupe[$j]) {
								$indice_groupe=$loop;
								// DEBUG
								//echo "\$current_group[$loop]['name']=".$current_group[$loop]['name']."<br />";
								break;
							}
						}

						// DEBUG
						//echo "\$indice_groupe=$indice_groupe<br />";

						if($indice_groupe==-1) {
							$serie[$cpt].="-";
						}
						else {
							if(isset($current_eleve_note[$indice_groupe][$indice_eleve1])) {
								// L'élève suit l'enseignement
								if($current_eleve_statut[$indice_groupe][$indice_eleve1]!="") {
									// Mettre le statut pose des problèmes pour le tracé de la courbe... abs, disp,... passent pour des zéros
									//$serie[$cpt].=$current_eleve_statut[$indice_groupe][$indice_eleve1];
									$serie[$cpt].="-";
								}
								else {
									$serie[$cpt].=$current_eleve_note[$indice_groupe][$indice_eleve1];
								}

								// REMPLIR $tab_imagemap[$k_num_periode][$m_num_groupe]

								$sql="SELECT ma.* FROM matieres_appreciations ma WHERE (ma.login='$eleve1' AND ma.periode='$num_periode[$cpt]' AND ma.id_groupe='$id_groupe[$j]');";
								affiche_debug("$sql<br />");
								$app_eleve_query=mysql_query($sql);
								// Dispositif de restriction des accès aux appréciations pour les comptes responsables/eleves
								if((mysql_num_rows($app_eleve_query)>0)&&($tab_acces_app[$cpt]=="y")) {
									$ligtmp=mysql_fetch_object($app_eleve_query);

									$tab_imagemap[$cpt][$j]=htmlspecialchars($ligtmp->appreciation);
									$info_imagemap[$j]="Au moins une appréciation";
								}
								else{
									$tab_imagemap[$cpt][$j]="";
								}
							}
							else{
								// L'élève n'a pas cette matière sur la période...
								// Pas sûr qu'on puisse arriver là: si, cf ci-dessous
								echo "<!-- $eleve1 n'a pas la matière ".$current_group[$indice_groupe]["matiere"]["matiere"]." sur la période ".$num_periode[$cpt]." -->\n";
								// mais en mode 'toutes les périodes', il faut afficher un champ (cas de l'Histoire des arts au T3 seulement)
								$serie[$cpt].="-";
							}
						}
					}
				}

				if((isset($mgen[$cpt]))&&(preg_match("/^[0-9.,]*$/", $mgen[$cpt]))) {
					$mgen[$cpt]=round(preg_replace('/,/', '.', $mgen[$cpt]),1);
				}

				$cpt++;
			}



			for($i=0;$i<count($id_groupe);$i++) {

				if(isset($info_imagemap[$i])) {
					$titre_bulle=htmlspecialchars($matiere_nom[$i]);

					$compteur_periodes_app_deroul=0;

					$texte_bulle="<table class='boireaus' style='margin:2px;' width='99%' summary='Imagemap'>\n";
					$alt=1;
					for($j=1;$j<=count($num_periode);$j++) {
						//if($tab_imagemap[$j][$i]!="") {
						if((isset($tab_imagemap[$j][$i]))&&($tab_imagemap[$j][$i]!="")) {
							$alt=$alt*(-1);
							$texte_bulle.="<tr class='lig$alt'><td style='font-weight:bold;'>$j</td><td style='text-align:center;'>".$tab_imagemap[$j][$i]."</td></tr>\n";

							// Pour le déroulant des appréciations
							$app_tmp = $tab_imagemap[$j][$i];
							$app_tmp = str_replace("\n", "", $app_tmp);
							$app_tmp = str_replace("\r\n", "", $app_tmp);
							$app_tmp = str_replace("\r", "", $app_tmp); 

							//if($j==1) {
							if($compteur_periodes_app_deroul==0) {
								$alt_defile=1;
								//$txt_appreciations_deroulantes.="<li><table class='boireaus'><tr class='lig$alt_defile'><th rowspan='".count($num_periode)."'>".htmlspecialchars($matiere_nom[$i])."</th>";

								//$txt_appreciations_deroulantes.="<li><strong>".htmlspecialchars($matiere_nom[$i])."&nbsp;:</strong>";
								//$txt_appreciations_deroulantes.="<table class='boireaus'><tr class='lig$alt_defile'>";

								$txt_appreciations_deroulantes.="<li><table class='boireaus' width='100%'><tr class='lig$alt_defile'><th colspan='2'>".htmlspecialchars($matiere_nom[$i])."</th></tr>";
								$alt_defile=$alt_defile*(-1);
								$txt_appreciations_deroulantes.="<tr class='lig$alt_defile'>";

								$compteur_periodes_app_deroul++;
							}
							else {
								$alt_defile=$alt_defile*(-1);
								$txt_appreciations_deroulantes.="<tr class='lig$alt_defile'>";
							}
							$txt_appreciations_deroulantes.="<td style='width:1em;'>".$j."</td>";
							$txt_appreciations_deroulantes.="<td>".$app_tmp."</td></tr>";
							if($j==count($num_periode)) {
								$txt_appreciations_deroulantes.="</table></li>";
							}
						}
					}
					$texte_bulle.="</table>\n";

					//$tabdiv_infobulle[]=creer_div_infobulle('div_app_'.$cpt,$titre_bulle,"",$texte_bulle,"",14,0,'y','y','n','n');

					if($type_graphe=='etoile') {
						//$tabdiv_infobulle[]=creer_div_infobulle('div_app_'.$i,$titre_bulle,"",$texte_bulle,"",20,0,'y','n','y','n');
						$tabdiv_infobulle[]=creer_div_infobulle('div_app_'.$i,$titre_bulle,"",$texte_bulle,"",20,0,'y','y','n','n');
					}
					else{
						$tabdiv_infobulle[]=creer_div_infobulle('div_app_'.$i,$titre_bulle,"",$texte_bulle,"",20,0,'n','n','n','n');
					}
					//$tab_imagemap_commentaire_present[]=$i;
				}


			}


			// Pour le déroulant des appréciations
			if ($graphe_affiche_deroulant_appreciations=='oui') {
				$graphe_hauteur_affichage_deroulant=$graphe_hauteur_affichage_deroulant."px";
				echo "<script type='text/javascript'>
				// <![CDATA[
					var pas=1;
					var h_fen='$graphe_hauteur_affichage_deroulant';
					function scrollmrq(){
						if (parseInt(mrq.style.top) > -h_mrq ) 
						mrq.style.top = parseInt(mrq.style.top)-pas+'px'
						else mrq.style.top=parseInt(h_fen)+'px'
					}
					function init_mrq(){
						mrq=document.getElementById('appreciations_defile');
						fen=document.getElementById('appreciations_deroulantes');
						fen.onmouseover=function(){stoc=pas;pas=0};
						fen.onmouseout=function(){pas=stoc};fen.style.height=h_fen;
						h_mrq=mrq.offsetHeight;
						with(mrq.style){position='absolute';top=h_fen;}
						setInterval('scrollmrq()',50);
					}
		
					document.getElementById('appreciations_defile').innerHTML='".addslashes($txt_appreciations_deroulantes)."';
		
					window.onload =init_mrq;
				//]]>
				</script>\n";
				//echo "<div style='display:none'><ul>$txt_appreciations_deroulantes</ul></div>";
				//echo "<div><ul>$txt_appreciations_deroulantes</ul></div>";
			}


			$sql="SELECT * FROM avis_conseil_classe WHERE login='$eleve1' ORDER BY periode;";
			$res_avis=mysql_query($sql);

			$temoin_avis_present="n";
			if(mysql_num_rows($res_avis)>0) {
				$titre_bulle="Avis du Conseil de classe";

				$texte_bulle="<table class='boireaus' style='margin:2px;' width='99%' summary='Avis'>\n";
				while($lig_avis=mysql_fetch_object($res_avis)) {
					//==========================================================
					// AJOUT: boireaus 20080218
					//        Dispositif de restriction des accès aux appréciations pour les comptes responsables/eleves
					//if($tab_acces_app[$lig_avis->periode]=="y") {
					if(($tab_acces_app[$lig_avis->periode]=="y")&&($lig_avis->avis!="")) {
					//==========================================================
						$texte_bulle.="<tr><td style='font-weight:bold;'>$lig_avis->periode</td><td style='text-align:center;'>".htmlspecialchars($lig_avis->avis)."</td></tr>\n";
					//==========================================================
					// AJOUT: boireaus 20080218
					//        Dispositif de restriction des accès aux appréciations pour les comptes responsables/eleves
						$temoin_avis_present="y";
					}
					//==========================================================
				}
				$texte_bulle.="</table>\n";

				//$tabdiv_infobulle[]=creer_div_infobulle('div_app_'.$cpt,$titre_bulle,"",$texte_bulle,"",14,0,'y','y','n','n');
				$tabdiv_infobulle[]=creer_div_infobulle('div_avis_1',$titre_bulle,"",$texte_bulle,"",20,0,'n','n','n','n');

				//==========================================================
				// COMMENTé ET REMONTé: boireaus 20080218
				//$temoin_avis_present="y";
				//==========================================================
			}

			//if(count($tab_imagemap)>0) {
				$largeurGrad=50;
				$largeurBandeDroite=80;
				$largeur_utile=$largeur_graphe-$largeurGrad-$largeurBandeDroite;

				$nbMat=count($matiere);

				// Pour éviter des pb de division par zero
				$largeurMat=$largeur_utile;
				if($nbMat>0) {
					$largeurMat=round($largeur_utile/$nbMat);
				}

				echo "<map name='imagemap'>\n";
				//for($i=0;$i<count($tab_imagemap);$i++) {
				//for($i=1;$i<=count($matiere);$i++) {
				for($i=0;$i<count($matiere);$i++) {
					//$x0=$largeurGrad+($i-1)*$largeurMat;
					$x0=$largeurGrad+$i*$largeurMat;
					$x1=$x0+$largeurMat;

					if(isset($info_imagemap[$i])) {
						//echo "<area href=\"#\" onClick='return false;' onMouseover=\"div_info('div_matiere_',$i,'affiche');\" onMouseout=\"div_info('div_matiere_',$i,'cache');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";

						if($click_plutot_que_survol_aff_app=="y") {
							echo "<area href=\"#\" onClick=\"affiche_eleve_delais_afficher_div('div_app_".$i."','y',-10,20,1,$largeurMat,$hauteur_rect_delais_afficher_div);return false;\" onMouseout=\"cacher_div('div_app_".$i."');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\" alt=\"\">";
						}
						else {
							echo "<area href=\"#\" onClick='return false;' onMouseover=\"affiche_eleve_delais_afficher_div('div_app_".$i."','y',-10,20,$duree_delais_afficher_div,$largeurMat,$hauteur_rect_delais_afficher_div);\" onMouseout=\"cacher_div('div_app_".$i."');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\" alt=\"\">";
						}
					}
				}

				$x0=$largeurGrad+($i-1)*$largeurMat;
				$x1=$largeur_graphe;
				//echo "<area href=\"#\" onClick='return false;' onMouseover=\"div_info('div_avis_','1','affiche');\" onMouseout=\"div_info('div_avis_','1','cache');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";
				if($temoin_avis_present=="y") {
					if($click_plutot_que_survol_aff_app=="y") {
						echo "<area href=\"#\" onClick=\"delais_afficher_div('div_avis_1','y',-10,20,1,$largeurMat,$hauteur_rect_delais_afficher_div);return false;\" onMouseout=\"cacher_div('div_avis_1');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\" alt=\"\">";
					}
					else {
						echo "<area href=\"#\" onClick='return false;' onMouseover=\"delais_afficher_div('div_avis_1','y',-10,20,$duree_delais_afficher_div,$largeurMat,$hauteur_rect_delais_afficher_div);\" onMouseout=\"cacher_div('div_avis_1');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\" alt=\"\">";
					}
				}
				echo "</map>\n";
			//}



			//===============================================================
			// Image Map pour le graphe en étoile
			// J'ai repris une portion du code de draw_graphe_star.php
			// pour juste récupérer les coordonnées des textes de matières
			echo "<map name='imagemap_star'>\n";

			$largeurTotale=$largeur_graphe;
			$hauteurTotale=$hauteur_graphe;
			$legendy[2]=$choix_periode;
			$x0=round($largeurTotale/2);
			if($legendy[2]=='Toutes_les_périodes') {
				$L=round(($hauteurTotale-6*(ImageFontHeight($taille_police)+5))/2);
				//$y0=round(3*(ImageFontHeight($taille_police))+5)+$L;
				$y0=round(4*(ImageFontHeight($taille_police))+5)+$L;
			}
			else{
				$L=round(($hauteurTotale-4*(ImageFontHeight($taille_police)+5))/2);
				$y0=round(2*(ImageFontHeight($taille_police))+5)+$L;
			}

			$pi=pi();

			function coordcirc($note,$angle) {
				// $note sur 20 (s'assurer qu'il y a le point pour séparateur et non la virgule)
				// $angle en degrés
				global $pi;
				global $L;
				global $x0;
				global $y0;

				$x=round($note*$L*cos($angle*$pi/180)/20)+$x0;
				$y=round($note*$L*sin($angle*$pi/180)/20)+$y0;

				return array($x,$y);
			}

			//=================================
			// Polygone 20/20
			unset($tab20);
			$tab20=array();
			for($i=0;$i<$nbMat;$i++) {
				$angle=round($i*360/$nbMat);
				$tab=coordcirc(20,$angle);

				$tab20[]=$tab[0];
				$tab20[]=$tab[1];
			}
			//ImageFilledPolygon($img,$tab20,count($tab20)/2,$bande2);
			//=================================

			//=================================
			// Légendes Matières: -> Coordonnées des textes de matières
			for($i=0;$i<count($tab20)/2;$i++) {
				$angle=round($i*360/$nbMat);

				//$texte=$matiere[$i+1];
				//$texte=$matiere_nom_long[$i+1];
				//$texte=$tab_nom_matiere[$i];
				//$texte=$matiere_nom[$i];
				//$k=$i+1;
				$k=$i;
				$texte=$matiere_nom[$k];

				$tmp_taille_police=$taille_police;

				if($angle==0) {
					$x=$tab20[2*$i]+5;

					$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police);

					if($x_verif>$largeurTotale) {
						for($j=$taille_police;$j>1;$j--) {
							$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j);
							if($x_verif<=$largeurTotale) {
								break;
							}
						}
						if($x_verif>$largeurTotale) {
							$j=1;
						}
						$tmp_taille_police=$j;
					}

					$y=round($tab20[2*$i+1]+$angle*(ImageFontHeight($taille_police)+2)/90);
				}
				elseif(($angle>0)&&($angle<90)) {
					$x=$tab20[2*$i]+5;
					$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police);

					if($x_verif>$largeurTotale) {
						for($j=$taille_police;$j>1;$j--) {
							$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j);
							if($x_verif<=$largeurTotale) {
								break;
							}
						}
						if($x_verif>$largeurTotale) {
							$j=1;
						}
						$tmp_taille_police=$j;
					}

					$y=round($tab20[2*$i+1]+$angle*(ImageFontHeight($taille_police)+2)/90);
				}
				elseif($angle==90) {
					$x=round($tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($taille_police)/2);
					$y=$tab20[2*$i+1]+ImageFontHeight($taille_police)+2;
				}
				elseif(($angle>90)&&($angle<180)) {
					$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($taille_police)+5);

					if($x<0) {
						for($j=$taille_police;$j>1;$j--) {
							$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($j)+5);
							if($x>=0) {
								break;
							}
						}
						if($x<0) {
							$x=1;
							$j=1;
						}
						$tmp_taille_police=$j;
					}

					$y=round($tab20[2*$i+1]+($angle-90)*(ImageFontHeight($taille_police)-2)/90);
				}
				elseif($angle==180) {
					$x=$tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($taille_police)-5;

					if($x<0) {
						for($j=$taille_police;$j>1;$j--) {
							$x=$tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($j)-5;
							if($x>=0) {
								break;
							}
						}
						if($x<0) {
							$x=1;
							$j=1;
						}
						$tmp_taille_police=$j;
					}

					$y=round($tab20[2*$i+1]-ImageFontHeight($taille_police)/2);
				}
				elseif(($angle>180)&&($angle<270)) {
					$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($taille_police)+5);

					if($x<0) {
						for($j=$taille_police;$j>1;$j--) {
							$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($j)+5);
							if($x>=0) {
								break;
							}
						}
						if($x<0) {
							$x=1;
							$j=1;
						}
						$tmp_taille_police=$j;
					}

					$y=round($tab20[2*$i+1]-($angle-180)*(ImageFontHeight($taille_police)-2)/90);
				}
				elseif($angle==270) {
					$x=round($tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($taille_police)/2);
					//$y=$tab20[2*$i+1]-ImageFontHeight($taille_police)-2;
					$y=$tab20[2*$i+1]-2*ImageFontHeight($taille_police)-2;
				}
				else{
					$x=$tab20[2*$i]+5;
					$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police);

					if($x_verif>$largeurTotale) {
						for($j=$taille_police;$j>1;$j--) {
							$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j);
							if($x_verif<=$largeurTotale) {
								break;
							}
						}
						if($x_verif>$largeurTotale) {
							$j=1;
						}
						$tmp_taille_police=$j;
					}

					$y=round($tab20[2*$i+1]-(90-($angle-270))*(ImageFontHeight($taille_police)-2)/90);
				}


				$x2=$x+strlen($texte)*ImageFontWidth($tmp_taille_police);
				$y2=$y+20;

				//echo "<area href=\"#\" onClick='return false;' onMouseover=\"div_info('div_matiere_',$tab_imagemap[$i],'affiche');\" onMouseout=\"div_info('div_matiere_',$tab_imagemap[$i],'cache');\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\">\n";
				//if(in_array($tab_imagemap[$i],$tab_imagemap_commentaire_present)) {
				//if(isset($info_imagemap[$i])) {
				if(isset($info_imagemap[$k])) {
					//echo "<area href=\"#\" onClick='return false;' onMouseover=\"afficher_div('div_app_".$tab_imagemap[$i]."','y',-100,20);\" onMouseout=\"cacher_div('div_app_".$tab_imagemap[$i]."');\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\">\n";
					//echo "<area href=\"#\" onClick='return false;' onMouseover=\"afficher_div('div_app_".$i."','y',-100,20);\" onMouseout=\"cacher_div('div_app_".$i."');\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\">\n";
					//echo "<area href=\"#\" onClick='return false;' onMouseover=\"afficher_div('div_app_".$k."','y',-100,20);\" onMouseout=\"cacher_div('div_app_".$k."');\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\">\n";
					//echo "<area href=\"#\" onClick='return false;' onMouseover=\"afficher_div('div_app_".$k."','y',-10,20);\" onMouseout=\"cacher_div('div_app_".$k."');\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\">\n";

					if($click_plutot_que_survol_aff_app=="y") {
						//echo "<area href=\"#\" onClick=\"delais_afficher_div('div_app_".$k."','y',-10,20,1,50,50);return false;\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\" alt=\"\">\n";
						echo "<area href=\"#\" onClick=\"affiche_eleve_afficher_div('div_app_".$k."','y',-10,20,1,50,50);return false;\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\" alt=\"\">\n";
					}
					else {
						//echo "<area href=\"#\" onClick='return false;' onMouseover=\"delais_afficher_div('div_app_".$k."','y',-10,20,$duree_delais_afficher_div,50,50);\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\" alt=\"\">\n";
						echo "<area href=\"#\" onClick='return false;' onMouseover=\"affiche_eleve_delais_afficher_div('div_app_".$k."','y',-10,20,$duree_delais_afficher_div,50,50);\" shape=\"rect\" coords=\"$x,$y,$x2,$y2\" alt=\"\">\n";
					}
				}

			}
			//=================================
			echo "</map>\n";
			//==================================================================





			// On génère les lignes de moyennes
			$liste_temp="";
			for($loop=1;$loop<=count($serie);$loop++) {
				if($liste_temp!="") {$liste_temp.="&amp;";}
				$liste_temp.="temp$loop=".$serie[$loop];
				if($affiche_mgen=='oui') {
					$liste_temp.="&amp;mgen$loop=".$mgen[$loop];
				}
			}
			//echo "\$affiche_mgen=$affiche_mgen<br />";
			//echo "\$liste_temp=$liste_temp<br />";



			$nbp=$nb_periode+1;

			echo "<a name='graph'></a>\n";

			if($type_graphe=='courbe') {

				if($mode_graphe=='png') {
					//echo "<img src='draw_artichow_fig7.php?temp1=$temp1&temp2=$temp2&etiquette=$etiq&titre=$graph_title&v_legend1=$v_legend1&v_legend2=$v_legend2&compteur=$compteur&nb_data=3'>";
					//echo "<img src='draw_artichow_fig7.php?temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=Toutes_les_périodes&compteur=$compteur&nb_data=$nbp'>";
					//echo "<img src='draw_artichow_fig7.php?$liste_temp&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=Toutes_les_périodes&compteur=$compteur&nb_data=$nbp'>";
					//echo "<img src='draw_artichow_fig7.php?$liste_temp&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=Toutes_les_périodes&compteur=$compteur&nb_series=$nb_series&id_classe=$id_classe'>";
					echo "<img src='draw_graphe.php?";
					// $liste_temp contient les séries et les moyennes générales.
					echo "$liste_temp";
					echo "&amp;etiquette=$liste_matieres";
					echo "&amp;titre=$graph_title";
					echo "&amp;v_legend1=$eleve1";
					//echo "&amp;v_legend2=Toutes_les_périodes";
					echo "&amp;v_legend2=".rawurlencode("Toutes_les_périodes");
					echo "&amp;compteur=$compteur";
					echo "&amp;nb_series=$nb_series";
					echo "&amp;id_classe=$id_classe";
					echo "&amp;largeur_graphe=$largeur_graphe";
					echo "&amp;hauteur_graphe=$hauteur_graphe";
					echo "&amp;taille_police=$taille_police";
					echo "&amp;epaisseur_traits=$epaisseur_traits";
					echo "&amp;epaisseur_croissante_traits_periodes=$epaisseur_croissante_traits_periodes";
					if($affiche_moy_annuelle=="oui") {
						echo "&amp;affiche_moy_annuelle=$affiche_moy_annuelle";
					}
					echo "&amp;tronquer_nom_court=$tronquer_nom_court";
					//echo "'>";
					//echo "&amp;temoin_imageps=$temoin_imageps";
					echo "&amp;temoin_image_escalier=$temoin_image_escalier";
					echo "' style='border: 1px solid black;' height='$hauteur_graphe' width='$largeur_graphe' alt='Graphe' ";
					echo "usemap='#imagemap' ";
					echo "/>\n";


				}
				else {
					echo "<div id='graphe_svg' style='position: relative;'>\n";

					# Image Map
					//$chaine_map="<map name='imagemap'>\n";
					// $largeurGrad -> 50
					// $largeurBandeDroite=80;
					// $largeur=$largeurTotale-$largeurGrad-$largeurBandeDroite;
					// $largeur=$largeur_graphe-$largeurGrad-$largeurBandeDroite;
					// $nbMat=count($matiere);
					// $largeurMat=round($largeur/$nbMat);

					$largeurGrad=50;
					$largeurBandeDroite=80;
					$largeur_utile=$largeur_graphe-$largeurGrad-$largeurBandeDroite;
					$nbMat=count($matiere);
					$largeurMat=round($largeur_utile/$nbMat);

					for($i=1;$i<=count($matiere);$i++) {
						$x0=$largeurGrad+($i-1)*$largeurMat;
						$x1=$x0+$largeurMat;

						if(isset($info_imagemap[$i])) {
							if($click_plutot_que_survol_aff_app=="y") {
								echo "<div onclick=\"delais_afficher_div('div_app_".$i."','y',-10,20,500,$largeurMat,10,1,$largeurMat,$hauteur_rect_delais_afficher_div);\" onMouseout=\"cacher_div('div_app_".$i."');\" style='position: absolute; left: ".$x0."px; top: 0px; width: ".$largeurMat."px; height: ".$hauteur_graphe."px;'>&nbsp;</div>\n";
							}
							else {
								echo "<div onMouseover=\"delais_afficher_div('div_app_".$i."','y',-10,20,500,$largeurMat,10,$duree_delais_afficher_div,$largeurMat,$hauteur_rect_delais_afficher_div);\" onMouseout=\"cacher_div('div_app_".$i."');\" style='position: absolute; left: ".$x0."px; top: 0px; width: ".$largeurMat."px; height: ".$hauteur_graphe."px;'>&nbsp;</div>\n";
							}
						}
					}

					$x0=$largeurGrad+($i-1)*$largeurMat;
					$x1=$largeur_graphe;
					if($temoin_avis_present=="y") {
						if($click_plutot_que_survol_aff_app=="y") {
							echo "<div onclick=\"delais_afficher_div('div_avis_1','y',-10,20,1,$largeurMat,$hauteur_rect_delais_afficher_div);\" onMouseout=\"cacher_div('div_avis_1');\" style='position: absolute; left: ".$x0."px; top: 0px; width: ".$largeurMat."px; height: ".$hauteur_graphe."px;'>&nbsp;</div>\n";
						}
						else {
							echo "<div onMouseover=\"delais_afficher_div('div_avis_1','y',-10,20,$duree_delais_afficher_div,$largeurMat,$hauteur_rect_delais_afficher_div);\" onMouseout=\"cacher_div('div_avis_1');\" style='position: absolute; left: ".$x0."px; top: 0px; width: ".$largeurMat."px; height: ".$hauteur_graphe."px;'>&nbsp;</div>\n";
						}
					}


					echo "<object data='draw_graphe_svg.php?";

					// $liste_temp contient les séries et les moyennes générales.
					echo "$liste_temp";
					echo "&amp;etiquette=$liste_matieres";
					echo "&amp;titre=$graph_title";
					echo "&amp;v_legend1=$eleve1";
					//echo "&amp;v_legend2=Toutes_les_périodes";
					echo "&amp;v_legend2=".rawurlencode("Toutes_les_périodes");
					echo "&amp;compteur=$compteur";
					echo "&amp;nb_series=$nb_series";
					echo "&amp;id_classe=$id_classe";
					echo "&amp;largeur_graphe=$largeur_graphe";
					echo "&amp;hauteur_graphe=$hauteur_graphe";
					echo "&amp;taille_police=$taille_police";
					echo "&amp;epaisseur_traits=$epaisseur_traits";
					echo "&amp;epaisseur_croissante_traits_periodes=$epaisseur_croissante_traits_periodes";
					if($affiche_moy_annuelle=="oui") {
						echo "&amp;affiche_moy_annuelle=$affiche_moy_annuelle";
					}
					echo "&amp;tronquer_nom_court=$tronquer_nom_court";
					echo "&amp;temoin_image_escalier=$temoin_image_escalier";

					echo "'";

					//echo " name='SVG1' width='$largeur_graphe' height='$hauteur_graphe' type='image/svg-xml' pluginspage='http://www.adobe.com/svg/viewer/install/'";
					//echo " name='SVG1' width='$largeur_graphe' height='$hauteur_graphe' type='image/svg-xml'";
					echo " width='$largeur_graphe' height='$hauteur_graphe'";
					//echo " width='$largeur_graphe' height='$hauteur_graphe' type='image/svg-xml'";
					echo " type=\"image/svg+xml\"></object>\n";

					echo "</div>\n";

				}

			}
			else{
				echo "<img src='draw_graphe_star.php?";
				//echo "<img src='draw_graphe.php?";
				// $liste_temp contient les séries et les moyennes générales.
				echo "$liste_temp";
				echo "&amp;etiquette=$liste_matieres";
				echo "&amp;titre=$graph_title";
				echo "&amp;v_legend1=$eleve1";
				//echo "&amp;v_legend2=Toutes_les_périodes";
				echo "&amp;v_legend2=".rawurlencode("Toutes_les_périodes");
				echo "&amp;compteur=$compteur";
				echo "&amp;nb_series=$nb_series";
				echo "&amp;id_classe=$id_classe";
				echo "&amp;largeur_graphe=$largeur_graphe";
				echo "&amp;hauteur_graphe=$hauteur_graphe";
				echo "&amp;taille_police=$taille_police";
				echo "&amp;epaisseur_traits=$epaisseur_traits";
				echo "&amp;epaisseur_croissante_traits_periodes=$epaisseur_croissante_traits_periodes";
				if($affiche_moy_annuelle=="oui") {
					echo "&amp;affiche_moy_annuelle=$affiche_moy_annuelle";
				}
				echo "&amp;tronquer_nom_court=$tronquer_nom_court";
				//echo "'>";
				//echo "&amp;temoin_imageps=$temoin_imageps";
				echo "&amp;temoin_image_escalier=$temoin_image_escalier";
				echo "' style='border: 1px solid black;' height='$hauteur_graphe' width='$largeur_graphe' alt='Graphe' ";
				//echo "usemap='#imagemap' ";
				echo "usemap='#imagemap_star' ";
				echo "/>\n";
			}

			//===================================

		}


	/*
		echo "<p>\n";
		echo "\$liste_matieres=$liste_matieres<br />\n";
		for($i=1;$i<=count($serie);$i++) {
			echo "\$serie[$i]=$serie[$i]<br />\n";
		}
		echo "</p>\n";
	*/



	/*
		echo "\$nb_periode=$nb_periode<br />";
		$num_periode=1;

		$cpt=1;
		// Boucle sur l'ordre des matières:
		while($ligne=mysql_fetch_object($call_classe_infos)) {
			// Nom court/long de la matière:
			$matiere[$cpt]=$ligne->matiere;
			$matiere_nom[$cpt]=$ligne->nom_complet;
			$cpt++;
		}

		for() {
	*/

		if(isset($prenom1)) {
			echo "<p align='center'>$prenom1 $nom1";
			//if($doublant1!="-") {echo " (<i>$doublant1</i>)";}
			if(($doublant1!="-")&&($doublant1!="")) {echo " (<i>$doublant1</i>)";}
			echo " né";
			if($sexe1=="F") {echo "e";}
			echo " le $naissance1[2]/$naissance1[1]/$naissance1[0] (<i>soit $age1 $precision1</i>).</p>";



			$acces_bull_simp="n";
			if(($_SESSION['statut']=="responsable")&&(getSettingValue('GepiAccesBulletinSimpleParent')=='yes')) {
				$acces_bull_simp="y";
			}
			elseif(($_SESSION['statut']=="eleve")&&(getSettingValue('GepiAccesBulletinSimpleEleve')=='yes')) {
				$acces_bull_simp="y";
			}
			elseif($_SESSION['statut']=="professeur") {

				if(getSettingValue('GepiAccesBulletinSimplePP')=='yes') {
					$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE login='$eleve1' AND professeur='".$_SESSION['login']."';";
					$test_acces_bull_simp=mysql_query($sql);
					if(mysql_num_rows($test_acces_bull_simp)>0) {
						$acces_bull_simp="y";
					}
				}

				if(getSettingValue('GepiAccesBulletinSimpleProfToutesClasses')=='yes') {
					$acces_bull_simp="y";
				}
				elseif(getSettingValue('GepiAccesBulletinSimpleProfTousEleves')=='yes') {
					$sql="SELECT 1=1 FROM j_eleves_classes jec, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jec.login='$eleve1' AND jec.id_classe=jgc.id_classe AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."';";
					$test_acces_bull_simp=mysql_query($sql);
					if(mysql_num_rows($test_acces_bull_simp)>0) {
						$acces_bull_simp="y";
					}
				}
				elseif(getSettingValue('GepiAccesBulletinSimpleProf')=='yes') {
					$sql="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_professeurs jgp WHERE jeg.login='$eleve1' AND jgp.id_groupe=jeg.id_groupe AND jgp.login='".$_SESSION['login']."';";
					$test_acces_bull_simp=mysql_query($sql);
					if(mysql_num_rows($test_acces_bull_simp)>0) {
						$acces_bull_simp="y";
					}
				}
			}
			elseif($_SESSION['statut']=="scolarite") {
				$acces_bull_simp="y";
			}
			elseif($_SESSION['statut']=="cpe") {
				$acces_bull_simp="y";
			}
			elseif($_SESSION['statut']=="administrateur") {
				$acces_bull_simp="y";
			}

			$acces_aa="n";
			if(isset($eleve1)) {
				require_once('../mod_annees_anterieures/fonctions_annees_anterieures.inc.php');
				$acces_aa=check_acces_aa($eleve1);
			}

			//A FAIRE variable à utiliser et à initialiser pour afficher les absences sous le graphique
			$afficher_absences='y';

			echo "<p align='center'>";

			if($acces_bull_simp=="y") {
				if($choix_periode=='toutes_periodes') {
					//echo "<a href=\"../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=".$eleve1."&id_classe=$id_classe&periode1=1&periode2=$nb_periode\" onclick=\"sauve_desactivation_infobulle();afficher_div('div_bull_simp','y',-100,-200); affiche_bull_simp('$eleve1','$id_classe','1','$nb_periode');restaure_desactivation_infobulle();return false;\" target=\"_blank\">";
					echo "<a href=\"../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=".$eleve1."&id_classe=$id_classe&periode1=1&periode2=$nb_periode\" onclick=\"afficher_div('div_bull_simp','y',-100,-200); affiche_bull_simp('$eleve1','$id_classe','1','$nb_periode');return false;\" target=\"_blank\">";
					echo "Voir le bulletin simplifié";
					//echo "<img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='Bulletin simple toutes périodes en infobulle' title='Bulletin simple toutes périodes en infobulle' />";
					echo "</a>";
				}
				else {
					//echo "<a href=\"../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=".$eleve1."&id_classe=$id_classe&periode1=$num_periode_choisie&periode2=$num_periode_choisie\" onclick=\"sauve_desactivation_infobulle();afficher_div('div_bull_simp','y',-100,-200); affiche_bull_simp('$eleve1','$id_classe','$num_periode_choisie','$num_periode_choisie');restaure_desactivation_infobulle();return false;\" target=\"_blank\">";
					echo "<a href=\"../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=".$eleve1."&id_classe=$id_classe&periode1=$num_periode_choisie&periode2=$num_periode_choisie\" onclick=\"afficher_div('div_bull_simp','y',-100,-200); affiche_bull_simp('$eleve1','$id_classe','$num_periode_choisie','$num_periode_choisie');return false;\" target=\"_blank\">";
					echo "Voir le bulletin simplifié";
					//echo "<img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='Bulletin simple toutes périodes en infobulle' title='Bulletin simple toutes périodes en infobulle' />";
					echo "</a>";
				}
			}

			if(check_droit_acces('/eleves/visu_eleve.php',$_SESSION['statut'])) {
				echo " | ";

				echo "<a href=\"../eleves/visu_eleve.php?ele_login=".$eleve1."&id_classe=".$id_classe."\" target=\"_blank\">";
				echo "Consultation";
				echo "</a>";
			}

			if((getSettingValue('active_annees_anterieures')=='y')&&($acces_aa=='y')) {
				//$sql="SELECT annee FROM archivage_disciplines a, eleves e WHERE e.login='$eleve1' AND e.no_gep=a.INE ORDER BY annee DESC LIMIT 1;";
				$sql="SELECT annee FROM archivage_disciplines a, eleves e WHERE e.login='$eleve1' AND e.no_gep=a.INE ORDER BY annee ASC LIMIT 1;";
				//echo "$sql<br />";
				$res_aa=mysql_query($sql);
				if(mysql_num_rows($res_aa)>0) {
					echo " | ";
					$lig_aa=mysql_fetch_object($res_aa);
					echo "<a href=\"../mod_annees_anterieures/popup_annee_anterieure.php?id_classe=$id_classe&logineleve=$eleve1&annee_scolaire=$lig_aa->annee&num_periode=3&mode=bull_simp\" onclick=\"afficher_div('div_annees_anterieures','y',-100,-200); affiche_annees_anterieures('$eleve1','$id_classe','$lig_aa->annee');return false;\" target=\"_blank\">";
					echo "Années antérieures";
					echo "</a>";
				}
			}

			// Bibliothèque de fonctions de la page consultation élève.
			include("../eleves/visu_ele_func.lib.php");
			// On extrait un tableau de l'ensemble des infos sur l'élève (bulletins, relevés de notes,... inclus)
			$tab_ele=info_eleve($eleve1);

			if($afficher_absences=="y") {
				if((getSettingValue("active_module_absence")=='y')||
					(getSettingValue("active_module_absence")=='2'&& getSettingValue("abs2_import_manuel_bulletin")=='y')||
					((count($tab_ele['absences'])!=0)&&(getSettingValue("active_module_absence")!='y' && getSettingValue("active_module_absence")!='2'))) {
					// On affiche les absences par défaut
				}
				elseif (getSettingValue("active_module_absence")=='2') {
					echo " | ";
					echo "<span id='pliage_abs'>\n";
					echo "(<em>";
					echo "<a href='#' onclick=\"document.getElementById('div_aff_abs').style.display='';return false;\">Afficher</a>";
					echo " / \n";
					echo "<a href='#' onclick=\"document.getElementById('div_aff_abs').style.display='none';return false;\">Masquer</a>";
					echo " les absences</em>)";
					echo "</span>\n";
				}
			}
			echo "</p>\n";

			//La variable 	$num_periode_choisie 	  contient le numéro de la période en cours 
				
			if($afficher_absences=="y") {
				if((getSettingValue("active_module_absence")=='y')||
					(getSettingValue("active_module_absence")=='2'&& getSettingValue("abs2_import_manuel_bulletin")=='y')||
					((count($tab_ele['absences'])!=0)&&(getSettingValue("active_module_absence")!='y' && getSettingValue("active_module_absence")!='2'))) {

					/*
					$tmp_p=$num_periode_choisie-1;
					foreach($tab_ele['absences'][$num_periode_choisie-1] as $key => $value) {
						echo "\$tab_ele['absences'][".$tmp_p."][$key]=$value<br />";
					}
					*/

				   // Affichage ligne
					if (isset($tab_ele['absences'][$num_periode_choisie-1])) {
						if($choix_periode!="toutes_periodes") {

							$info_absence="<center>";

							if((count($tab_ele['absences'])==0)) {
								$info_absence.="Aucun bilan d'absences n'est enregistré.";
							}
							else {
								if($tab_ele['absences'][$num_periode_choisie-1]['nb_absences'] == '0')
								{
									$info_absence.="Aucune demi-journée d'absence.";
								} else {
									$info_absence.="Nombre de demi-journées d'absence ";
									if ($tab_ele['absences'][$num_periode_choisie-1]['nb_absences'] == '0') {
										$info_absence = $info_absence."justifiées ";
									}
									$info_absence = $info_absence.": ".$tab_ele['absences'][$num_periode_choisie-1]['nb_absences']."</b>";
									if ($tab_ele['absences'][$num_periode_choisie-1]['non_justifie'] != '0')
									{
										$info_absence = $info_absence." (<em>dont <b>".$tab_ele['absences'][$num_periode_choisie-1]['non_justifie']."</b> non justifiée";
										if ($tab_ele['absences'][$num_periode_choisie-1]['non_justifie'] != '1') { $info_absence = $info_absence."s"; }
										$info_absence = $info_absence."</em>)";
									}
									$info_absence = $info_absence.".";
								}
						
								if($tab_ele['absences'][$num_periode_choisie-1]['nb_retards'] != '0')
								{
									$info_absence = $info_absence."<i> Nombre de retards : </i><b>".$tab_ele['absences'][$num_periode_choisie-1]['nb_retards']."</b>";
								}
							}
							echo $info_absence."</center>";
						}
						else {
							echo "<div align='center'>\n";
							echo "<table class='boireaus' summary='Bilan des absences'>\n";
							echo "<tr>\n";
							echo "<th>Nombre 1/2 journées d'absence sur la période</th>\n";
							echo "<th>dont non justifiées</th>\n";
							echo "<th>Nombre de retards</th>\n";
							echo "</tr>\n";
							$alt=-1;
							for($loop_per=0;$loop_per<$nb_periode;$loop_per++) {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'>\n";
								echo "<td>";
								if (isset($tab_ele['absences'][$loop_per]['nb_absences'])) {
									echo $tab_ele['absences'][$loop_per]['nb_absences'];
								}
								else {
									echo "-";
								}
								echo "</td>\n";

								echo "<td>";
								if (isset($tab_ele['absences'][$loop_per]['non_justifie'])) {
									echo $tab_ele['absences'][$loop_per]['non_justifie'];
								}
								else {
									echo "-";
								}
								echo "</td>\n";

								echo "<td>";
								if (isset($tab_ele['absences'][$loop_per]['nb_retards'])) {
									echo $tab_ele['absences'][$loop_per]['nb_retards'];
								}
								else {
									echo "-";
								}
								echo "</td>\n";
/*
								echo "<td>".$tab_ele['absences'][$loop_per]['nb_absences']."</td>\n";
								echo "<td>".$tab_ele['absences'][$loop_per]['non_justifie']."</td>\n";
								echo "<td>".$tab_ele['absences'][$loop_per]['nb_retards']."</td>\n";
*/
								echo "</tr>\n";
							}
							echo "</table>\n";
							echo "</div>\n";
						}
					}
/*  A supprimer				   
				    //Affichage tableau
				    if(count($tab_ele['absences'])==0) {
					    echo "<p>Aucun bilan d'absences n'est enregistré.</p>\n";
				    }
				    else {
					    echo "<table class='boireaus' summary='Bilan des absences'>\n";
					    echo "<tr>\n";
					    echo "<th>Nombre 1/2 journées d'absence sur la période</th>\n";
					    echo "<th>dont non justifiées</th>\n";
					    echo "<th>Nombre de retards</th>\n";
					    echo "</tr>\n";
					    $alt=-1; 
						echo "<tr class='lig$alt'>\n";
						//echo "<td>N° ".$tab_ele['absences'][$num_periode_choisie-1]['periode']."</td>\n";
						echo "<td>".$tab_ele['absences'][$num_periode_choisie-1]['nb_absences']."</td>\n";
						echo "<td>".$tab_ele['absences'][$num_periode_choisie-1]['non_justifie']."</td>\n";
						echo "<td>".$tab_ele['absences'][$num_periode_choisie-1]['nb_retards']."</td>\n";
						echo "</tr>\n";
					    echo "</table>\n";
				    }

*/
				}
				elseif (getSettingValue("active_module_absence")=='2') {
					// Initialisations files
					echo "<div id='div_aff_abs'style='display:none'>\n";
					require_once("../lib/initialisationsPropel.inc.php");
					$eleve = EleveQuery::create()->findOneByLogin($eleve1);

					echo "<table class='boireaus' summary='Bilan des absences'>\n";
					echo "<tr>\n";
					echo "<th>Absences sur la période</th>\n";
					echo "<th>Nombre de 1/2 journées</th>\n";
					echo "<th>dont non justifiées</th>\n";
					echo "<th>Nombre de retards</th>\n";
					echo "</tr>\n";
					$alt=1;
				
					// Il ne faudrait afficher que le T1, T2 ou T3 en se basant sur la variable $num_periode_choisie
				
					foreach($eleve->getPeriodeNotes() as $periode_note) {
						//$periode_note = new PeriodeNote();
						if ($periode_note->getDateDebut() == null) {
						//periode non commencee
						continue;
						}
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td>".$periode_note->getNomPeriode();
						echo " du ".$periode_note->getDateDebut('d/m/Y');
						echo " au ";
						if ($periode_note->getDateFin() == null) {
						$now = new DateTime('now');
						echo $now->format('d/m/Y');
						} else {
						echo $periode_note->getDateFin('d/m/Y');
						}
						echo "</td>\n";
						echo "<td>";
						echo $eleve->getDemiJourneesAbsence($periode_note->getDateDebut(null), $periode_note->getDateFin(null))->count();
						echo "</td>\n";
						echo "<td>";
						echo $eleve->getDemiJourneesNonJustifieesAbsence($periode_note->getDateDebut(null), $periode_note->getDateFin(null))->count();
						echo "</td>\n";
						echo "<td>";
						echo $eleve->getRetards($periode_note->getDateDebut(null), $periode_note->getDateFin(null))->count();
						echo "</td>\n";
						echo "</tr>\n";
					}
					echo "</table>\n";

					echo "</div>\n";
				}
		}

	}

	    // FIN DE L'AFFICHAGE DES ABSENCES
		
		//=========================
		// AJOUT: boireaus 20090115
		// La variable peut être vide si on n'a pas choisi ce mode d'affichage ou si on n'a pas le droit de saisie, ou péridoe close,...
		echo $texte_saisie_avis_fixe;
		//=========================

	}
	else{
		if ($_SESSION['statut'] == "eleve" OR $_SESSION['statut'] == "responsable") {
			echo "<p align='center'>Choisissez une période et validez.</p>\n";
		} else {
			echo "<p align='center'>Choisissez un élève et validez.</p>\n";
		}
	}
	echo "</td>\n";
	//====================================================================
/*
	// Bande d'affichage de l'image:
	echo "<td>\n";
	echo "<a name='graph'></a>\n";
	//echo "<img src='draw_artichow_fig7.php?temp1=$temp1&temp2=$temp2&etiquette=$etiq&titre=$graph_title&v_legend1=$v_legend1&v_legend2=$v_legend2&compteur=$compteur&nb_data=3'>";
	echo "<img src='draw_artichow_fig7.php?temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=$eleve2&compteur=$compteur&nb_data=3'>";
	echo "</td>\n";
*/
	//====================================================================
	echo "</tr>\n";
	echo "</table>\n";

	if(!isset($_POST['is_posted'])) {
		// Pour la première validation lors de l'accès à la page de graphe et ainsi obtenir directement le premier affichage:
		echo "<script type='text/javascript'>
	document.forms['form_choix_eleves'].submit();
</script>\n";
	}

	//echo "<div id='div_truc' style='position: absolute; z-index: 1000; top: 300px; left: 0px; width: 0px; border: 1px solid black; background-color:white; display:none;'>BLABLA</div>\n";
	//echo "<div id='div_truc' class='infodiv'>BLABLA</div>\n";
	//echo "<div id='divtruc' class='infodiv'>BLABLA</div>\n";

}


function div_cmnt_type() {
	global $id_classe;
	global $num_periode_choisie;
	global $graphe_champ_saisie_avis_fixe;

	// Récupération du numéro de la période de saisie de l'avis du conseil:
	$periode_num=$num_periode_choisie;

	$sql="show tables;";
	$res_tables=mysql_query($sql);
	$temoin_commentaires_types="";
	while($lig_table=mysql_fetch_array($res_tables)) {
		if($lig_table[0]=='commentaires_types') {
			$temoin_commentaires_types="oui";
		}
	}

	//$retour_lignes_cmnt_type="_o_";
	//$retour_lignes_cmnt_type="\$periode_num=$periode_num";
	$retour_lignes_cmnt_type="";

	if($temoin_commentaires_types=="oui") {
		$sql="select * from commentaires_types where id_classe='$id_classe' and num_periode='$periode_num' order by commentaire";
		//$retour_lignes_cmnt_type.="<p>$sql</p>\n";
		$resultat_commentaire=mysql_query($sql);
		if(mysql_num_rows($resultat_commentaire)>0) {

			//$retour_lignes_cmnt_type.="<p>Ajouter un <a href='#' onClick=\"afficher_div('commentaire_type','y',30,20);";
			//if($graphe_champ_saisie_avis_fixe!='y') {$retour_lignes_cmnt_type.="ajuste_pos('commentaire_type');";}
			//$retour_lignes_cmnt_type.="return false;\">Commentaire-type</a></p>\n";

			$retour_lignes_cmnt_type.=" <a href='#' onClick=\"afficher_div('commentaire_type','y',30,20);";
			if($graphe_champ_saisie_avis_fixe!='y') {$retour_lignes_cmnt_type.="ajuste_pos('commentaire_type');";}
			$retour_lignes_cmnt_type.="return false;\">CT</a>\n";

			$retour_lignes_cmnt_type.="<div id='commentaire_type' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; height: 10em 5px; width: 400px;'>\n";
			$retour_lignes_cmnt_type.="<div class='infobulle_entete' style='color: #ffffff; cursor: move; font-weight: bold; padding: 0px;'  onmousedown=\"dragStart(event, 'commentaire_type')\">\n";
			$retour_lignes_cmnt_type.="<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 1em;'><a href='#' onClick=\"document.getElementById('commentaire_type').style.display='none';return false;\">X</a></div>\n";
			$retour_lignes_cmnt_type.="Commentaires-types";
			$retour_lignes_cmnt_type.="</div>\n";

			$retour_lignes_cmnt_type.="<div style='height: 9em; overflow: auto;'>\n";
			$cpt=0;
			while($ligne_commentaire=mysql_fetch_object($resultat_commentaire)) {
				$retour_lignes_cmnt_type.="<div style='border: 1px solid black; margin: 1px; padding: 1px;'";

				if(preg_match("/firefox/i",$_SERVER['HTTP_USER_AGENT'])) {
					$retour_lignes_cmnt_type.=" onClick=\"document.getElementById('no_anti_inject_current_eleve_login_ap2').value=document.getElementById('no_anti_inject_current_eleve_login_ap2').value+document.getElementById('commentaire_type_'+$cpt).value;changement();document.getElementById('commentaire_type').style.display='none'; document.getElementById('no_anti_inject_current_eleve_login_ap2').focus();\"";
				}
				$retour_lignes_cmnt_type.=">\n";

				$retour_lignes_cmnt_type.="<input type='hidden' name='commentaire_type_$cpt' id='commentaire_type_$cpt' value=\" ".htmlspecialchars(stripslashes(trim($ligne_commentaire->commentaire)))."\" />\n";

				if(!preg_match("/firefox/i",$_SERVER['HTTP_USER_AGENT'])) {
					// Avec konqueror, pour document.getElementById('textarea_courant').value, on obtient [Object INPUT]
					// En sortant, la commande du onClick et en la mettant dans une fonction javascript externe, ca passe.
					//$retour_lignes_cmnt_type.="<a href='#' onClick=\"complete_textarea_courant($cpt); return false;\" style='text-decoration:none; color:black;'>";
					$retour_lignes_cmnt_type.="<a href='#' onClick=\"complete_textarea_avis($cpt); return false;\" style='text-decoration:none; color:black;'>";
				}

				// Pour conserver le code HTML saisi dans les commentaires-type...
				if((preg_match("/</",$ligne_commentaire->commentaire))&&(preg_match("/>/",$ligne_commentaire->commentaire))) {
					/* Si le commentaire contient du code HTML, on ne remplace pas les retours à la ligne par des <br> pour éviter des doubles retours à la ligne pour un code comme celui-ci:
						<p>Blabla<br>
						Blibli</p>
					*/
					$retour_lignes_cmnt_type.=htmlspecialchars(stripslashes(trim($ligne_commentaire->commentaire)));
				}
				else{
					//Si le commentaire ne contient pas de code HTML, on remplace les retours à la ligne par des <br>:
					$retour_lignes_cmnt_type.=htmlspecialchars(stripslashes(nl2br(trim($ligne_commentaire->commentaire))));
				}

				if(!preg_match("/firefox/i",$_SERVER['HTTP_USER_AGENT'])) {
					$retour_lignes_cmnt_type.="</a>";
				}

				$retour_lignes_cmnt_type.="</div>\n";
				$cpt++;
			}
			$retour_lignes_cmnt_type.="</div>\n";
			$retour_lignes_cmnt_type.="</div>\n";

			$retour_lignes_cmnt_type.="<script type='text/javascript'>
	document.getElementById('commentaire_type').style.display='none';
</script>\n";


$retour_lignes_cmnt_type.="<script type='text/javascript'>
function ajuste_pos(id_div) {
	if(browser.isIE) {
		document.getElementById(id_div).style.left=0;
		document.getElementById(id_div).style.top=0;
	}
	else{
		document.getElementById(id_div).style.left='0px';
		document.getElementById(id_div).style.top='0px';
	}
}

// Pour konqueror...
function complete_textarea_avis(num) {
	// Récupération de l'identifiant du TEXTAREA à remplir
	id_textarea_courant='no_anti_inject_current_eleve_login_ap2'
	//alert('id_textarea_courant='+id_textarea_courant);

	// Contenu initial du TEXTAREA
	contenu_courant_textarea_courant=eval(\"document.getElementById('\"+id_textarea_courant+\"').value\");
	//alert('contenu_courant_textarea_courant='+contenu_courant_textarea_courant);

	// Commentaire à ajouter
	commentaire_a_ajouter=eval(\"document.getElementById('commentaire_type_\"+num+\"').value\");
	//alert('commentaire_a_ajouter='+commentaire_a_ajouter);

	// Ajout
	textarea_courant=eval(\"document.getElementById('\"+id_textarea_courant+\"')\")
	textarea_courant.value=contenu_courant_textarea_courant+commentaire_a_ajouter;

	// On cache la liste des commentaires-types
	document.getElementById('commentaire_type').style.display='none';

	// On redonne le focus au TEXTAREA
	document.getElementById(id_textarea_courant).focus();

	changement();
}
</script>\n";

			//$retour_lignes_cmnt_type.="<script type='text/javascript' src='../lib/brainjar_drag.js'></script>\n";
			//$retour_lignes_cmnt_type.="<script type='text/javascript' src='../lib/position.js'></script>\n";
		}
	}

	return $retour_lignes_cmnt_type;
}


//===========================================================
//if(isset($eleve1)) {
if((isset($eleve1))&&(isset($nom1))&&(isset($prenom1))) {
	echo "<div id='div_bull_simp' class='infobulle_corps' style='position: absolute; top: 220px; right: 20px; width: 700px; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>\n";

		echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: 700px; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_bull_simp')\">\n";
			echo "<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>\n";
			echo "<a href='#' onClick=\"cacher_div('div_bull_simp');return false;\">\n";
			echo "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />\n";
			echo "</a>\n";
			echo "</div>\n";

			echo "<div id='titre_entete_bull_simp'>";
			echo "Bulletin simplifié de $prenom1 $nom1 ";
			if($choix_periode=='periode') {
				echo "en période $num_periode_choisie";
			}
			else {
				echo "de la période 1 à la $nb_periode";
			}
			echo "</div>\n";
		echo "</div>\n";
	
		echo "<div id='corps_bull_simp' class='infobulle_corps' style='color: #000000; cursor: auto; font-weight: bold; padding: 0px; height: 15em; width: 700px; overflow: auto;'>";
		if($acces_bull_simp=="y") {
			if($choix_periode=='periode') {
				$periode1=$num_periode_choisie;
				$periode2=$num_periode_choisie;
			}
			else {
				$periode1=1;
				$periode2=$nb_periode;
			}
			$choix_edit=2;
			$login_eleve=$eleve1;
			$inclusion_depuis_graphes="y";
			include "../lib/bulletin_simple.inc.php";
			include("../saisie/edit_limite.inc.php");
		}
		echo "</div>\n";

	echo "</div>\n";
}

echo "<script type='text/javascript'>
	// <![CDATA[
	function affiche_bull_simp(login_eleve,id_classe,num_per1,num_per2) {
		//document.getElementById('titre_entete_bull_simp').innerHTML='Bulletin simplifié de '+login_eleve+' période '+num_per1+' à '+num_per2;

		//new Ajax.Updater($('corps_bull_simp'),'../saisie/ajax_edit_limite.php?choix_edit=2&login_eleve='+login_eleve+'&id_classe='+id_classe+'&periode1='+num_per1+'&periode2='+num_per2,{method: 'get'});
	}

	/*
	var svg_desactivation_infobulle;
	function sauve_desactivation_infobulle() {
		svg_desactivation_infobulle=desactivation_infobulle;
		desactivation_infobulle='n';
	}

	function restaure_desactivation_infobulle() {
		desactivation_infobulle=svg_desactivation_infobulle;
	}
	*/

	function affiche_eleve_afficher_div(id,positionner,dx,dy) {
		if(desactivation_infobulle!='y') {
			afficher_div(id,positionner,dx,dy);
		}
	}

	function affiche_eleve_delais_afficher_div(id,positionner,dx,dy,delais,DX,DY) {
		if(desactivation_infobulle!='y') {
			delais_afficher_div(id,positionner,dx,dy,delais,DX,DY);
		}
	}

	//]]>
</script>\n";


if(getSettingValue('active_annees_anterieures')=='y') {

	//require("../mod_annees_anterieures/fonctions_annees_anterieures.inc.php");
	//require("../mod_annees_anterieures/check_acces_et_liste_periodes.php");
	//require('../mod_annees_anterieures/fonctions_annees_anterieures.inc.php');

	require_once('../mod_annees_anterieures/fonctions_annees_anterieures.inc.php');

	if((isset($eleve1))&&(isset($id_classe))) {
		$tab_periodes_aa=check_acces_et_liste_periodes($eleve1,$id_classe);
		affiche_onglets_aa($eleve1, $id_classe, $tab_periodes_aa, 0);
	}
}
//===========================================================

//===========================================================
echo "<p><em>NOTE&nbsp;:</em></p>\n";
require("../lib/textes.inc.php");
echo "<p style='margin-left: 3em;'>$explication_bulletin_ou_graphe_vide</p>\n";
//===========================================================

require("../lib/footer.inc.php");
?>
