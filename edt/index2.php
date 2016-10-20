<?php
@set_time_limit(0);

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/edt/index2.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/edt/index2.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='V',
autre='F',
description='EDT 2 : Index',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(((($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable'))&&((getSettingAOui('autorise_edt_eleve'))||(getSettingAOui('autorise_edt2_eleve'))))||
((in_array($_SESSION['statut'], array('professeur', 'cpe', 'scolarite')))&&(getSettingAOui('autorise_edt_tous')))||
(($_SESSION['statut']=='administrateur')&&(getSettingAOui('autorise_edt_admin')))) {
	// On va afficher l'EDT
}
else {
	header("Location: ../accueil.php?msg=Accès non autorisé");
	die();
}

$msg="";

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

//debug_var();
//============================================================
$complement_liens_edt="";
$complement_liens_edt2="";
if((isset($mode))&&($mode=="afficher_edt")) {
	$complement_liens_edt="mode=afficher_edt";
	$complement_liens_edt2="&$complement_liens_edt";
}
$affichage_complementaire_sur_edt=isset($_POST['affichage_complementaire_sur_edt']) ? $_POST['affichage_complementaire_sur_edt'] : (isset($_GET['affichage_complementaire_sur_edt']) ? $_GET['affichage_complementaire_sur_edt'] : NULL);
if(isset($affichage_complementaire_sur_edt)) {
	if($complement_liens_edt!="") {
		$complement_liens_edt.="&";
	}
	$complement_liens_edt.="affichage_complementaire_sur_edt=$affichage_complementaire_sur_edt";
	$complement_liens_edt2.="&affichage_complementaire_sur_edt=$affichage_complementaire_sur_edt";
}
//============================================================

require("edt_ics_lib.php");

//$type_edt=isset($_POST['type_edt']) ? $_POST['type_edt'] : (isset($_GET['type_edt']) ? $_GET['type_edt'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : "");
$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : (isset($_GET['login_prof']) ? $_GET['login_prof'] : "");
$num_semaine_annee=isset($_POST['num_semaine_annee']) ? $_POST['num_semaine_annee'] : (isset($_GET['num_semaine_annee']) ? $_GET['num_semaine_annee'] : NULL);
$affichage=isset($_POST['affichage']) ? $_POST['affichage'] : (isset($_GET['affichage']) ? $_GET['affichage'] : "semaine");

$type_affichage=isset($_POST['type_affichage']) ? $_POST['type_affichage'] : (isset($_GET['type_affichage']) ? $_GET['type_affichage'] : NULL);

$display_date=isset($_POST['display_date']) ? $_POST['display_date'] : (isset($_GET['display_date']) ? $_GET['display_date'] : NULL);

$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : (isset($_GET['login_eleve']) ? $_GET['login_eleve'] : NULL);
$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : (isset($_GET['login_prof']) ? $_GET['login_prof'] : NULL);


if((isset($_GET['action_js']))&&(isset($_GET['id_cours']))&&(preg_match("/^[0-9]{1,}$/", $_GET['id_cours']))) {
	$sql="SELECT * FROM edt_cours ec, edt_creneaux ecr WHERE ec.id_cours='".$_GET['id_cours']."' AND ec.id_definie_periode=ecr.id_definie_periode;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Cours n°".$_GET['id_cours']." non trouvé.</p>";
	}
	else {
		$lig=mysqli_fetch_object($res);

		// Afficher des détails sur le créneau
		echo "<p>Cours du ".$lig->jour_semaine;
		if($lig->heuredeb_dec==0) {
			if($lig->duree==2) {
				echo " en ".$lig->nom_definie_periode;
				echo " (<em>".preg_replace("/:[0-9]*$/", "", $lig->heuredebut_definie_periode)."-&gt;".preg_replace("/:[0-9]*$/", "", $lig->heurefin_definie_periode)."</em>)";
			}
			else {
				echo " commençant en ".$lig->nom_definie_periode." pour une durée de ".($lig->duree/2)."h.";
			}




			$hms=$lig->heuredebut_definie_periode;




		}
		else {
			echo " commençant en milieu de créneau ".$lig->nom_definie_periode;
			echo " pour une durée de ".($lig->duree/2)."h.";




			$hms=$lig->heuredebut_definie_periode;

// OU mktime créer une date avec heure, min,...

		}
		echo "</p>";

		if($lig->id_groupe!=0) {
			$current_group=get_group($lig->id_groupe, array('matieres', 'classes', 'profs'));
			$info_grp=get_info_grp($lig->id_groupe);
			echo "<p>".$info_grp."</p>";

			echo "<p>Voir l'EDT de la classe&nbsp: ";
			$cpt_classe=0;
			foreach($current_group['classes']['classes'] as $current_id_classe => $current_classe) {
				if($cpt_classe>0) {
					echo " - ";
				}
				//echo "<a href='".$_SERVER['PHP_SELF']."?login_prof=$login_prof&amp;id_classe=$current_id_classe&amp;type_affichage=$type_affichage&amp;login_eleve=$login_eleve&amp;num_semaine_annee=$num_semaine_annee&amp;affichage=$affichage&amp;mode=afficher_edt".add_token_in_url()."' target='_blank' title=\"Afficher l'EDT seul\"><img src='../images/icons/edt.png' class='icone16' alt='EDT seul' /></a>";
				echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$current_id_classe&amp;type_affichage=classe&amp;num_semaine_annee=$num_semaine_annee&amp;affichage=$affichage&amp;mode=afficher_edt".add_token_in_url()."' target='_blank' title=\"Afficher l'EDT seul\"><img src='../images/icons/edt.png' class='icone16' alt='EDT seul' />".$current_classe['classe']."</a>";
				$cpt_classe++;
			}
			echo "</p>";
		}
		elseif($lig->id_aid!=0) {
			$tab_aid=get_tab_aid($lig->id_aid);

			echo "<p>".$tab_aid['nom_general_court']." (".$tab_aid['nom_general_complet'].") (".$tab_aid['nom_aid'].")</p>";

			echo "<p>Voir l'EDT de la classe&nbsp: ";
			$cpt_classe=0;
			foreach($tab_aid['classes'] as $current_id_classe => $current_classe) {
				if($cpt_classe>0) {
					echo " - ";
				}
				echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$current_id_classe&amp;type_affichage=classe&amp;num_semaine_annee=$num_semaine_annee&amp;affichage=$affichage&amp;mode=afficher_edt".add_token_in_url()."' target='_blank' title=\"Afficher l'EDT seul\"><img src='../images/icons/edt.png' class='icone16' alt='EDT seul' />".$current_classe['classe']."</a>";
				$cpt_classe++;
			}
			echo "</p>";
		}

// Pour un prof afficher des liens vers le CDT, les notes,...

// Pour un EDT classe, mettre des liens EDT prof,...

// Afficher les infos liées à la classe (pp), edt classe,...

		// Récupérer l'heure du créneau
		if(peut_poster_message($_SESSION['statut'])) {
			$ts=time();
			if(mb_strtolower(strftime("%A"))!=$lig->jour_semaine) {
				for($i=1;$i<7;$i++) {
					$ts+=3600*24;
					if(mb_strtolower(strftime("%A", $ts))==$lig->jour_semaine) {
						break;
					}
				}
			}

			echo "<a href='../mod_alerte/form_message.php?message_envoye=y&login_dest=".$lig->login_prof."&date_visibilite=".strftime("%d/%m/%Y", $ts)."&heure_visibilite=".strftime("%H:%M:%S").add_token_in_url()."' title=\"Déposer une alerte à destination de ".civ_nom_prenom($lig->login_prof)."\" target='_blank'><img src='../images/icons/$icone_deposer_alerte' class='icone16' alt='Alerte' />Déposer une alerte/rappel, pour ".civ_nom_prenom($lig->login_prof).", à afficher le ".strftime("%d/%m/%Y", $ts)." à ".strftime("%H:%M")."</a><br />";
		}
	}

	die();
}

//===================================================
// Contrôler si le jour est dans la période de l'année scolaire courante
$ts_debut_annee=getSettingValue('begin_bookings');
$ts_fin_annee=getSettingValue('end_bookings');
//===================================================

//===================================================
if($affichage!="semaine") {
	if(!isset($display_date)) {
		if((isset($num_semaine_annee))&&(preg_match("/^[0-9]{1,}\|[0-9]{4}$/", $num_semaine_annee))) {
			$tmp_tab=explode("|", $num_semaine_annee);
			if(!isset($tmp_tab[1])) {
				$display_date=strftime("%d/%m/%Y");
				$affichage=strftime("%u");
			}
			else {
				$tmp_tab2=get_days_from_week_number($tmp_tab[0] ,$tmp_tab[1]);
				/*
				echo "<pre>";
				print_r($tmp_tab2);
				echo "</pre>";
				*/
				if(isset($tmp_tab2['num_jour'][$affichage])) {
					$display_date=$tmp_tab2['num_jour'][$affichage]['jjmmaaaa'];
				}
				else {
					$display_date=$tmp_tab2['num_jour'][1]['jjmmaaaa'];
					$affichage=1;
				}
			}
		}
		else {
			$display_date=strftime("%d/%m/%Y");
			$affichage=strftime("%u");
		}
	}
	elseif(!preg_match("#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#", $display_date)) {
		$msg.="Date $display_date invalide.<br />";
		unset($display_date);
		$display_date=strftime("%d/%m/%Y");
		$affichage=strftime("%u");
	}

	$tmp_tab=explode("/", $display_date);
	$ts_display_date=mktime(12, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
	$ts_debut_jour=mktime(0, 0, 0, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
	$ts_debut_jour_suivant=mktime(23, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2])+1;
	$num_semaine=strftime("%V", $ts_display_date);

	$num_semaine_annee=$num_semaine."|".$tmp_tab[2];

	if($affichage=="jour") {
		$affichage=strftime("%u", $ts_display_date);
	}
	elseif($affichage!=strftime("%u", $ts_display_date)) {
		$msg.="Le jour choisi '$affichage' ne correspond pas à la date $display_date<br />";
		$affichage=strftime("%u", $ts_display_date);
	}

	$tab_jour=get_tab_jour_ouverture_etab();

	if(!in_array(strftime("%A", $ts_display_date), $tab_jour)) {
		// Jour suivant
		// Boucler sur 7 jours pour trouver le jour ouvré suivant
		// Il faudrait même chercher une date hors vacances
		$ts_display_date_suivante="";
		$display_date_suivante="";
		$display_date_suivante_num_jour="";
		$ts_test=$ts_display_date;
		$cpt=0;
		while(($cpt<7)&&($ts_test<$ts_fin_annee)) {
			$ts_test+=3600*24;
			if(in_array(strftime("%A", $ts_test), $tab_jour)) {
				$ts_display_date_suivante=$ts_test;
				$display_date_suivante=strftime("%d/%m/%Y", $ts_test);
				$display_date_suivante_num_jour=strftime("%u", $ts_test);
				break;
			}
			$cpt++;
		}
		if($display_date_suivante!="") {
			$ts_display_date=$ts_display_date_suivante;
			$display_date=$display_date_suivante;
			$affichage=$display_date_suivante_num_jour;

			$tmp_tab=explode("/", $display_date);
			$ts_display_date=mktime(12, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
			$ts_debut_jour=mktime(0, 0, 0, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
			$ts_debut_jour_suivant=mktime(23, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2])+1;
			$num_semaine=strftime("%V", $ts_display_date);

			$num_semaine_annee=$num_semaine."|".$tmp_tab[2];
		}
	}

	if($ts_display_date<$ts_debut_annee) {
		$msg.="Première date possible&nbsp;: Début de l'année scolaire.<br />";
		$ts_display_date=$ts_debut_annee;

		$display_date=strftime("%d/%m/%Y", $ts_display_date);
		$affichage=strftime("%u", $ts_display_date);

		$tmp_tab=explode("/", $display_date);
		$ts_debut_jour=mktime(0, 0, 0, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
		$ts_debut_jour_suivant=mktime(23, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2])+1;
		$num_semaine=strftime("%V", $ts_display_date);

		$num_semaine_annee=$num_semaine."|".$tmp_tab[2];
	}
	elseif($ts_display_date>$ts_fin_annee) {
		$msg.="Dernière date possible&nbsp;: Fin de l'année scolaire.<br />";
		$ts_display_date=$ts_fin_annee;

		$display_date=strftime("%d/%m/%Y", $ts_display_date);
		$affichage=strftime("%u", $ts_display_date);

		$tmp_tab=explode("/", $display_date);
		$ts_debut_jour=mktime(0, 0, 0, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
		$ts_debut_jour_suivant=mktime(23, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2])+1;
		$num_semaine=strftime("%V", $ts_display_date);

		$num_semaine_annee=$num_semaine."|".$tmp_tab[2];
	}
}
//===================================================
if((!isset($num_semaine_annee))||($num_semaine_annee=="")||(!preg_match("/[0-9]{2}\|[0-9]{4}/", $num_semaine_annee))) {
	//$num_semaine_annee="36|".((strftime("%m")>7) ? strftime("%Y") : (strftime("%Y")-1));
	//$num_semaine_annee=strftime("%V")."|".((strftime("%m")>7) ? (strftime("%Y")-1) : strftime("%Y"));
	$tmp_mois_courant=strftime("%m");
	if($tmp_mois_courant==7) {
		$num_semaine_annee="27|".strftime("%Y");
	}
	elseif($tmp_mois_courant==8) {
		$num_semaine_annee="36|".strftime("%Y");
	}
	else {
		$num_semaine_annee=strftime("%V")."|".strftime("%Y");
	}
}
//===================================================
if($affichage=="semaine") {
	$tmp_tab=explode("|", $num_semaine_annee);
	$num_semaine=$tmp_tab[0];
	$annee=$tmp_tab[1];
	$jours=get_days_from_week_number($num_semaine, $annee);

	$ts_display_date=$jours['num_jour'][1]['timestamp'];
}
//===================================================
// A ce stade, on a forcément $ts_display_date renseigné
// A ce stade, on a forcément $num_semaine_annee renseigné
//===================================================
// Filtrage/contrôle de l'id_classe dans le cas élève/responsable
if($_SESSION['statut']=="eleve") {
	$login_eleve=$_SESSION['login'];

	if(!isset($type_affichage)) {
		$type_affichage="eleve";
	}

	$tab_classes=array();
	$sql="SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_classes jec, 
										classes c
									WHERE jec.id_classe=c.id AND 
										jec.login='".$_SESSION['login']."' 
									ORDER BY classe;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_classes[$lig->id_classe]=$lig->classe;
		}
	}

	if($type_affichage=="classe") {
		// Contrôler que c'est une des classes de l'élève
		if(!isset($id_classe)) {
			$type_affichage="eleve";
			$msg.="L'affichage classe a été demandé, mais sans choisir de classe.<br />";
		}
		else {
			if(!array_key_exists($id_classe, $tab_classes)) {
				$type_affichage="eleve";
				$msg.="La classe choisie ne vous est pas associée.<br />";
			}
			else {
				//unset($login_eleve);
			}
		}
	}

}
elseif($_SESSION['statut']=="responsable") {

	if(!isset($type_affichage)) {
		$type_affichage="eleve";
	}

	$tab_classes=array();
	$sql="SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_classes jec, 
										classes c, 
										eleves e, 
										responsables2 r, 
										resp_pers rp 
									WHERE jec.login=e.login AND 
										jec.id_classe=c.id AND 
										e.ele_id=r.ele_id AND 
										r. pers_id=rp.pers_id AND 
										rp.login='".$_SESSION['login']."' AND 
										(r.resp_legal='1' OR r.resp_legal='2' OR (r.resp_legal='0' AND r.acces_sp='y')) 
									ORDER BY classe;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_classes[$lig->id_classe]=$lig->classe;
		}
	}

	if($type_affichage=="classe") {
		// Contrôler que c'est une des classes des élèves associés au parent
		if(!isset($id_classe)) {
			if(isset($login_eleve)) {
				$id_classe=get_id_classe_ele_d_apres_date($login_eleve, $ts_display_date);
				if($id_classe=="") {
					$id_classe=get_id_classe_derniere_classe_ele($login_eleve);
				}
			}
			else {
				$type_affichage="eleve";
				$msg.="L'affichage classe a été demandé, mais sans choisir de classe.<br />";
			}
		}
		else {
			if(!array_key_exists($id_classe, $tab_classes)) {
				$type_affichage="eleve";
				$msg.="La classe choisie n'est pas associée à un de vos élèves/enfants.<br />";
			}
		}
	}

	$tab_ele=get_enfants_from_resp_login($_SESSION['login'], 'simple');
	if($type_affichage=="eleve") {
		$tab_ele2=array();
		for($loop=0;$loop<count($tab_ele);$loop+=2) {
			$tab_ele2[]=$tab_ele[$loop];
		}

		if(count($tab_ele2)==0) {
			header("Location: ../accueil.php?msg=Aucun élève trouvé");
			die();
		}

		if((!isset($login_eleve))||(!in_array($login_eleve, $tab_ele2))) {
			$login_eleve=$tab_ele2[0];
		}
		// Il faudra proposer le choix si count($tab_ele2)>1

		$login_ele_prec="";
		$login_ele_suiv="";
		$nom_prenom_ele_prec="";
		$nom_prenom_ele_suiv="";
		$login_ele_trouve=0;
		for($loop=0;$loop<count($tab_ele);$loop+=2) {
			if(($tab_ele[$loop]!=$login_eleve)&&($login_ele_trouve==0)) {
				$login_ele_prec=$tab_ele[$loop];
				$nom_prenom_ele_prec=$tab_ele[$loop+1];
			}
			elseif($tab_ele[$loop]==$login_eleve) {
				$login_ele_trouve++;
			}
			elseif($login_ele_trouve==1) {
				$login_ele_suiv=$tab_ele[$loop];
				$nom_prenom_ele_suiv=$tab_ele[$loop+1];
				$login_ele_trouve++;
			}
		}
	}
}
else {
	if($_SESSION['statut']=="professeur") {
		if(!isset($type_affichage)) {
			$type_affichage="prof";
			$login_prof=$_SESSION['login'];
		}
		elseif(($type_affichage=="prof")&&($login_prof!=$_SESSION['login'])&&(!getSettingAOui('AccesProf_EdtProfs'))) {
			$msg.="Accès non autorisé aux EDT des collègues.<br />";

			$type_affichage="prof";
			$login_prof=$_SESSION['login'];
		}
	}

	$tab_classes=array();
	$sql="SELECT DISTINCT c.classe, p.id_classe FROM classes c, periodes p WHERE p.id_classe=c.id ORDER BY classe;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_classes[$lig->id_classe]=$lig->classe;
		}
	}
	/*
	if((!isset($login_eleve))&&(!isset($id_classe))&&(!isset($login_prof))) {
		header("Location: ../accueil.php?msg=Elève non choisi");
		die();
	}
	*/
}

if(isset($type_affichage)) {
	//============================
	$info_edt="";
	if((isset($login_eleve))&&($type_affichage=="eleve")) {
		$info_eleve=get_nom_prenom_eleve($login_eleve, "avec_classe");
		if(isset($id_classe)) {
			if(!is_eleve_classe($login_eleve, $id_classe)) {
				unset($id_classe);
			}
			// Sinon, on accepte la classe proposée (pour gérer le cas des élèves changeant de classe en cours d'année)
		}

		if(!isset($id_classe)) {
			$id_classe=get_id_classe_ele_d_apres_date($login_eleve, $ts_display_date);
			if($id_classe=="") {
				$id_classe=get_id_classe_derniere_classe_ele($login_eleve);
			}
		}
		$info_edt=$info_eleve;
	}
	elseif((isset($id_classe))&&($type_affichage=="classe")) {
		$info_edt=get_nom_classe($id_classe);
	}
	elseif((isset($login_prof))&&($type_affichage=="prof")) {
		$info_edt=affiche_utilisateur($login_prof, "", "cni");
	}
	//============================
	if($type_affichage=="eleve") {
		$login_prof="";
		if((!isset($login_eleve))||($login_eleve=="")) {
			unset($type_affichage);
			$msg.="Élève non choisi.<br />";
		}
	}
	elseif($type_affichage=="classe") {
		$login_eleve="";
		$login_prof="";
		if((!isset($id_classe))||($id_classe=="")) {
			unset($type_affichage);
			$msg.="Classe non choisie.<br />";
		}
	}
	elseif($type_affichage=="prof") {
		$login_eleve="";
		$id_classe="";
		if((!isset($login_prof))||($login_prof=="")) {
			unset($type_affichage);
			$msg.="Professeur non choisi.<br />";
		}
	}
	//============================
}

if((isset($_GET['mode']))&&($_GET['mode']=='afficher_edt_js')) {
	$tab_jour=get_tab_jour_ouverture_etab();
	$tab_horaire_jour=get_horaires_jour();

	/*
	if($affichage=="semaine") {
		$largeur_edt=800;
	}
	else {
		$largeur_edt=114;
	}
	$y0=10;
	$hauteur_une_heure=60;
	*/
	$x0=50;

	$largeur_edt=isset($_GET['largeur_edt']) ? $_GET['largeur_edt'] : 800;
	$y0=isset($_GET['y0']) ? $_GET['y0'] : 10;
	$hauteur_une_heure=isset($_GET['hauteur_une_heure']) ? $_GET['hauteur_une_heure'] : 60;
	$hauteur_jour=isset($_GET['hauteur_jour']) ? $_GET['hauteur_jour'] : 800;

	$mode_infobulle="y";
	$html=affiche_edt2($login_eleve, $id_classe, $login_prof, $type_affichage, $ts_display_date, $affichage, $x0, $y0, $largeur_edt, $hauteur_une_heure);
	echo $html;
	die();
}

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

//**************** EN-TETE *****************
if($mode!="afficher_edt") {
	$titre_page = "EDT";
}
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();
function echo_selon_mode($texte) {
	global $mode;

	if($mode!="afficher_edt") {
		echo $texte;
	}
}

// onclick=\"return confirm_abandon (this, change, '$themessage')\"
echo_selon_mode("<p class='bold'><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>");

//============================================================
if(!isset($type_affichage)) {
	// Cas admin, scol, cpe

	// Choisir le type d'affichage souhaité
	echo_selon_mode("
<p class='bold'>Afficher un emploi du temps classe&nbsp;:</p>");
	//$sql="SELECT DISTINCT c.classe, p.id_classe FROM classes c, periodes p WHERE p.id_classe=c.id ORDER BY classe;";
	//$res=mysqli_query($GLOBALS["mysqli"], $sql);
	//if(mysqli_num_rows($res)==0) {
	if(count($tab_classes)==0) {
		echo_selon_mode("
<p style='color:red;'>Aucune classe n'a été trouvée.</p>");
	}
	else {
		$tab_txt=array();
		$tab_lien=array();
		/*
		while($lig=mysqli_fetch_object($res)) {
			$tab_txt[]=$lig->classe;
			$tab_lien[]=$_SERVER['PHP_SELF']."?affichage=semaine&amp;type_affichage=classe&amp;id_classe=".$lig->id_classe;
		}
		*/
		foreach($tab_classes as $current_id_classe => $current_nom_classe) {
			$tab_txt[]=$current_nom_classe;
			$tab_lien[]=$_SERVER['PHP_SELF']."?affichage=semaine&amp;type_affichage=classe&amp;id_classe=".$current_id_classe;
		}
		$nbcol=6;
		echo_selon_mode(tab_liste($tab_txt,$tab_lien,$nbcol));
	}

	if(($_SESSION['statut']=='professeur')&&(!getSettingAOui('AccesProf_EdtProfs'))) {
		echo_selon_mode("
<p class='bold'>Afficher un emploi du temps professeur&nbsp;: <a href='".$_SERVER['PHP_SELF']."?affichage=semaine&amp;type_affichage=prof&amp;login_prof=".$_SESSION['login']."'>".$_SESSION['civilite']." ".casse_mot($_SESSION['nom'], "maj")." ".casse_mot($_SESSION['prenom'], "majf2")."</a></p>");
	}
	else {
		echo_selon_mode("
<p class='bold'>Afficher un emploi du temps professeur&nbsp;:</p>");
		$page_lien=$_SERVER['PHP_SELF'];
		$nom_var_login="login_prof";
		$tab_statuts=array("professeur");
		$autres_parametres_lien="&amp;affichage=semaine&amp;type_affichage=prof";
		echo_selon_mode(liens_user($page_lien, $nom_var_login, $tab_statuts, $autres_parametres_lien));
	}

	require("../lib/footer.inc.php");
	die();
}
//============================================================

//debug_var();

$tab_jour=get_tab_jour_ouverture_etab();
$tab_horaire_jour=get_horaires_jour();
/*
echo "\$tab_jour<pre>";
print_r($tab_jour);
echo "</pre>";
*/

//============================================================
// Formulaire de choix de la semaine

$selected_semaine="";
$selected_lundi="";
$selected_mardi="";
$selected_mercredi="";
$selected_jeudi="";
$selected_vendredi="";
$selected_samedi="";
$selected_dimanche="";
if(in_array($affichage, array("1", "2", "3", "4", "5", "6", "7"))) {
	$tab_jours_aff=array($affichage);
	if($affichage==1) {
		$selected_lundi=" selected";
	}
	elseif($affichage==2) {
		$selected_mardi=" selected";
	}
	elseif($affichage==3) {
		$selected_mercredi=" selected";
	}
	elseif($affichage==4) {
		$selected_jeudi=" selected";
	}
	elseif($affichage==5) {
		$selected_vendredi=" selected";
	}
	elseif($affichage==6) {
		$selected_samedi=" selected";
	}
	elseif($affichage==7) {
		$selected_dimanche=" selected";
	}
}
else {
	// Affichage semaine
	$tab_jours_aff=array();

	if(in_array("lundi", $tab_jour)) {
		$tab_jours_aff[]=1;
	}
	if(in_array("mardi", $tab_jour)) {
		$tab_jours_aff[]=2;
	}
	if(in_array("mercredi", $tab_jour)) {
		$tab_jours_aff[]=3;
	}
	if(in_array("jeudi", $tab_jour)) {
		$tab_jours_aff[]=4;
	}
	if(in_array("vendredi", $tab_jour)) {
		$tab_jours_aff[]=5;
	}
	if(in_array("samedi", $tab_jour)) {
		$tab_jours_aff[]=6;
	}
	if(in_array("dimanche", $tab_jour)) {
		$tab_jours_aff[]=7;
	}

	$selected_semaine=" selected";
}

/*
echo "\$tab_jours_aff<pre>";
print_r($tab_jours_aff);
echo "</pre>";
*/

echo_selon_mode("
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field());
//=======================================
if($_SESSION['statut']=="responsable") {
	$checked_eleve="";
	$checked_classe="";
	if($type_affichage=="eleve") {
		$checked_eleve=" checked";
		$checked_classe="";
	}
	elseif($type_affichage=="classe") {
		$checked_eleve="";
		$checked_classe=" checked";
	}


	// Affichage élève ou classe
	/*
	echo "
		<p>Affichage&nbsp;: 
		<label for='type_affichage_eleve'>élève</label><input type='radio' name='type_affichage' id='type_affichage_eleve' value='eleve' /> ou <input type='radio' name='type_affichage' id='type_affichage_eleve' value='classe' /><label for='type_affichage_classe'>classe</label></p>";
	echo "
		<input type='hidden' name='login_eleve' value=\"$login_eleve\" />";
	*/


	echo_selon_mode("
		<p>Affichage&nbsp;: <input type='radio' name='type_affichage' id='type_affichage_classe' value='classe' ".$checked_classe."/><label for='type_affichage_classe'>classe</label>
		<select name='id_classe' id='id_classe' style='width:5em;' 
			onchange=\"if(document.getElementById('id_classe').options[document.getElementById('id_classe').selectedIndex].value!='') {
						document.getElementById('type_affichage_classe').checked=true;
					};document.getElementById('form_envoi').submit();\">
			<option value=''>---</option>");
	if(count($tab_classes)==0) {
		echo_selon_mode("
			<option value='' style='color:red'>Aucune classe trouvée</option>");
	}
	else {
		foreach($tab_classes as $current_id_classe => $current_nom_classe) {
			$selected="";
			if((isset($id_classe))&&($current_id_classe==$id_classe)) {
				$selected=" selected";
			}
			echo_selon_mode("
			<option value='".$current_id_classe."'".$selected.">".$current_nom_classe."</option>");
		}
	}
	echo_selon_mode("
		</select>");

	if(count($tab_ele)>0) {
		echo_selon_mode("
		 ou <input type='radio' name='type_affichage' id='type_affichage_eleve' value='eleve' ".$checked_eleve."/><label for='type_affichage_eleve'>élève</label>
		<select name='login_eleve' id='login_eleve' style='width:10em;' 
			onchange=\"if(document.getElementById('login_eleve').options[document.getElementById('login_eleve').selectedIndex].value!='') {
						document.getElementById('type_affichage_eleve').checked=true;
					};document.getElementById('form_envoi').submit();\">
			<option value=''>---</option>");
		for($loop=0;$loop<count($tab_ele);$loop+=2) {
			$selected="";
			if((isset($login_eleve))&&($tab_ele[$loop]==$login_eleve)) {
				$selected=" selected";
			}
			echo_selon_mode("
			<option value='".$tab_ele[$loop]."'".$selected.">".$tab_ele[$loop+1]."</option>");
		}
		echo_selon_mode("
		</select>");
	}

}
//=======================================
elseif($_SESSION['statut']=="eleve") {
	// Affichage élève ou classe
	if($type_affichage=="eleve") {
		$checked_eleve=" checked";
		$checked_classe="";
	}
	else {
		$checked_eleve="";
		$checked_classe=" checked";
	}
	$tab_classes_ele=get_class_periode_from_ele_login($_SESSION['login']);
	echo_selon_mode("
		<p>Affichage&nbsp;: <label for='type_affichage_eleve'>".$_SESSION['nom']." ".$_SESSION['prenom']."</label><input type='radio' name='type_affichage' id='type_affichage_eleve' value='eleve' ".$checked_eleve."/> ou <input type='radio' name='type_affichage' id='type_affichage_classe' value='classe' ".$checked_classe."/>");
	if(count($tab_classes_ele['classe']==1)) {
		foreach($tab_classes_ele['classe'] as $current_id_classe => $tab_clas) {
			$current_nom_classe=$tab_clas['classe'];
		}
		echo_selon_mode("<label for='type_affichage_classe'>classe de ".$current_nom_classe."</label>
		<input type='hidden' name='id_classe' value='$current_id_classe' />");
	}
	else {
		echo_selon_mode("<label for='type_affichage_classe'>classe de </label>
		<select name='id_classe' onchange=\"document.getElementById('type_affichage_classe').checked=true;document.getElementById('type_affichage_eleve').checked=false;document.getElementById('form_envoi').submit();\">");
		foreach($tab_classes_ele['classe'] as $current_id_classe => $tab_clas) {
			$current_nom_classe=$tab_clas['classe'];
			$selected="";
			if((isset($id_classe))&&($current_id_classe==$id_classe)) {
				$selected=" selected";
			}
			echo_selon_mode("
			<option value='$current_id_classe'$selected>$current_nom_classe</option>");
		}
		echo_selon_mode("
		</select>");
	}
	echo_selon_mode("
		<input type='hidden' name='login_eleve' value=\"$login_eleve\" />");
	echo_selon_mode("</p>");
}
//=======================================
else {
	// Personnel de l'établissement
	$checked_eleve="";
	$checked_classe="";
	$checked_prof="";
	if($type_affichage=="eleve") {
		$checked_eleve=" checked";
		$checked_classe="";
		$checked_prof="";
	}
	elseif($type_affichage=="classe") {
		$checked_eleve="";
		$checked_classe=" checked";
		$checked_prof="";
	}
	elseif($type_affichage=="prof") {
		$checked_eleve="";
		$checked_classe="";
		$checked_prof=" checked";
	}

	echo_selon_mode("
		<p>Affichage&nbsp;: <input type='radio' name='type_affichage' id='type_affichage_prof' value='prof' ".$checked_prof."/>");
	if(($_SESSION['statut']=='professeur')&&(!getSettingAOui('AccesProf_EdtProfs'))) {
		echo_selon_mode("<label for='type_affichage_prof'>".$_SESSION['civilite']." ".casse_mot($_SESSION['nom'], "maj")." ".casse_mot($_SESSION['prenom'], "majf2")."</label>
		<input type='hidden' name='login_prof' value=\"".$_SESSION['login']."\" />");
	}
	else {
		echo_selon_mode("<label for='type_affichage_prof'>professeur</label>
		<select name='login_prof' id='login_prof' style='width:10em;' 
			onchange=\"if(document.getElementById('login_prof').options[document.getElementById('login_prof').selectedIndex].value!='') {
						document.getElementById('type_affichage_prof').checked=true;
						document.getElementById('id_classe').selectedIndex=0;
					};document.getElementById('form_envoi').submit();\">
			<option value=''>---</option>");
		$sql="SELECT login, civilite, nom, prenom FROM utilisateurs WHERE statut='professeur' AND etat='actif' ORDER BY nom,prenom;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo_selon_mode("
			<option value='' style='color:red'>Aucun professeur trouvé</option>");
		}
		else {
			while($lig=mysqli_fetch_object($res)) {
				$selected="";
				if((isset($login_prof))&&($lig->login==$login_prof)) {
					$selected=" selected";
				}
				echo_selon_mode("
			<option value='".$lig->login."'$selected>".$lig->civilite." ".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, "majf2")."</option>");
			}
		}
		echo_selon_mode("
		</select>");
	}

	echo_selon_mode("
		 ou <input type='radio' name='type_affichage' id='type_affichage_classe' value='classe' ".$checked_classe."/><label for='type_affichage_classe'>classe</label>
		<select name='id_classe' id='id_classe' style='width:5em;' 
			onchange=\"if(document.getElementById('id_classe').options[document.getElementById('id_classe').selectedIndex].value!='') {
						document.getElementById('type_affichage_classe').checked=true;
						document.getElementById('login_prof').selectedIndex=0;
					};document.getElementById('form_envoi').submit();\">
			<option value=''>---</option>");
	//$sql="SELECT DISTINCT p.id_classe, c.classe FROM periodes p, classes c WHERE p.id_classe=c.id ORDER BY c.classe;";
	//$res=mysqli_query($GLOBALS["mysqli"], $sql);
	//if(mysqli_num_rows($res)==0) {
	if(count($tab_classes)==0) {
		echo_selon_mode("
			<option value='' style='color:red'>Aucune classe trouvée</option>");
	}
	else {
		/*
		while($lig=mysqli_fetch_object($res)) {
			$selected="";
			if((isset($id_classe))&&($lig->id_classe==$id_classe)) {
				$selected=" selected";
			}
			echo "
			<option value='".$lig->id_classe."'".$selected.">".$lig->classe."</option>";
		}
		*/
		foreach($tab_classes as $current_id_classe => $current_nom_classe) {
			$selected="";
			if((isset($id_classe))&&($current_id_classe==$id_classe)) {
				$selected=" selected";
			}
			echo_selon_mode("
			<option value='".$current_id_classe."'".$selected.">".$current_nom_classe."</option>");
		}
	}
	echo_selon_mode("
		</select>");

	if(($type_affichage=='classe')||($type_affichage=='eleve')) {
		// Afficher un formulaire de choix de l'élève de la classe
		$sql="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND id_classe='$id_classe' ORDER BY nom, prenom;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			echo_selon_mode("
		 ou <input type='radio' name='type_affichage' id='type_affichage_eleve' value='eleve' ".$checked_eleve."/><label for='type_affichage_eleve'>élève</label>
		<select name='login_eleve' id='login_eleve' style='width:10em;' 
			onchange=\"if(document.getElementById('login_eleve').options[document.getElementById('login_eleve').selectedIndex].value!='') {
						document.getElementById('type_affichage_eleve').checked=true;
						document.getElementById('login_prof').selectedIndex=0;
					};document.getElementById('form_envoi').submit();\">
			<option value=''>---</option>");
			while($lig=mysqli_fetch_object($res)) {
				$selected="";
				if((isset($login_eleve))&&($lig->login==$login_eleve)) {
					$selected=" selected";
				}
				echo_selon_mode("
			<option value='".$lig->login."'".$selected.">".casse_mot($lig->nom, 'maj')." ".casse_mot($lig->prenom, 'majf2')."</option>");
			}
			echo_selon_mode("
		</select>");
		}
	}

	if((in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe', 'professeur')))&&(getSettingValue('active_module_absence')=='2')) {
		echo_selon_mode("
		 <select name='affichage_complementaire_sur_edt' id='affichage_complementaire_sur_edt' title=\"Affichage complémentaire\" onchange=\"document.getElementById('form_envoi').submit();\">
		 	<option value=''".((!isset($affichage_complementaire_sur_edt))||($affichage_complementaire_sur_edt=="") ? " selected" : "").">---</option>
		 	<option value='absences2'".((isset($affichage_complementaire_sur_edt))&&($affichage_complementaire_sur_edt=="absences2") ? " selected" : "").">Absences</option>
		</select>");
	}

	/*
	if((isset($login_eleve))&&($login_eleve!="")) {
		// Affichage élève ou classe ou prof
		// Dans le cas prof, pouvoir limiter selon le paramétrage AccesProf_EdtProfs

		echo "
		<input type='hidden' name='login_eleve' value=\"$login_eleve\" />";
	}
	else {
		// Affichage classe ou prof


	}
	*/
}
//=======================================
echo_selon_mode("
		<p>
			Semaine choisie&nbsp;: <select name='num_semaine_annee' onchange=\"document.getElementById('form_envoi').submit();\">
				<option value=''></option>");

if(strftime("%m")>=8) {
	$annee=strftime("%Y");
}
else {
	$annee=strftime("%Y")-1;
}
for($n=36;$n<52;$n++) {
	$tmp_tab=get_days_from_week_number($n ,$annee);

	$selected="";
	if("$n|$annee"==$num_semaine_annee) {
		$selected=" selected='selected'";
	}

	echo_selon_mode("
				<option value='$n|$annee'$selected>Semaine n° $n   - (du ".$tmp_tab['num_jour'][1]['jjmmaaaa']." au ".$tmp_tab['num_jour'][7]['jjmmaaaa'].")</option>");
}
$annee++;
for($n=1;$n<28;$n++) {
	$m=(($n<10) ? "0".$n : $n);
	$tmp_tab=get_days_from_week_number($m ,$annee);

	$selected="";
	if("$m|$annee"==$num_semaine_annee) {
		$selected=" selected='selected'";
	}
	echo_selon_mode("
				<option value='".$m."|$annee'$selected>Semaine n° $m   - (du ".$tmp_tab['num_jour'][1]['jjmmaaaa']." au ".$tmp_tab['num_jour'][7]['jjmmaaaa'].")</option>");
}

echo_selon_mode("
			</select><br />

			Afficher <select name='affichage' onchange=\"document.getElementById('form_envoi').submit();\">
				<option value='semaine'>semaine</option>");
if(in_array("lundi" , $tab_jour)) {
	echo_selon_mode("
				<option value='1'$selected_lundi>lundi</option>");
}
if(in_array("mardi" , $tab_jour)) {
	echo_selon_mode("
				<option value='2'$selected_mardi>mardi</option>");
}
if(in_array("mercredi" , $tab_jour)) {
	echo_selon_mode("
				<option value='3'$selected_mercredi>mercredi</option>");
}
if(in_array("jeudi" , $tab_jour)) {
	echo_selon_mode("
				<option value='4'$selected_jeudi>jeudi</option>");
}
if(in_array("vendredi" , $tab_jour)) {
	echo_selon_mode("
				<option value='5'$selected_vendredi>vendredi</option>");
}
if(in_array("samedi" , $tab_jour)) {
	echo_selon_mode("
				<option value='6'$selected_samedi>samedi</option>");
}
if(in_array("dimanche" , $tab_jour)) {
	echo_selon_mode("
				<option value='7'$selected_dimanche>dimanche</option>");
}
echo_selon_mode("

			</select>");

// Pour afficher l'EDT seul sans titre, menu,... et l'EDT des semaines A/B
echo_selon_mode("<div style='float:right; width:5em;'>
	<a href='".$_SERVER['PHP_SELF']."?login_prof=$login_prof&amp;id_classe=$id_classe&amp;type_affichage=$type_affichage&amp;login_eleve=$login_eleve&amp;num_semaine_annee=$num_semaine_annee&amp;affichage=$affichage&amp;mode=afficher_edt".add_token_in_url()."' target='_blank' title=\"Afficher l'EDT seul\"><img src='../images/icons/edt.png' class='icone16' alt='EDT seul' /></a>
	-
	<a href='".$_SERVER['PHP_SELF']."?login_prof=$login_prof&amp;id_classe=$id_classe&amp;type_affichage=$type_affichage&amp;login_eleve=$login_eleve&amp;num_semaine_annee=$num_semaine_annee&amp;affichage=$affichage&amp;mode=afficher_edt&amp;afficher_sem_AB=y".add_token_in_url()."' target='_blank' title=\"Afficher l'EDT seul avec les semaines A et B\"><img src='../images/icons/edt_semAB.png' class='icone16' alt='EDT seul' /></a>
</div>");


echo_selon_mode("
			<input type='submit' id='input_submit' value='Valider' />
		</p>

	</fieldset>
</form>");

//============================================================
//echo "affichage=$affichage<br />";
if($affichage=="semaine") {
	$largeur_edt=800;
}
else {
	$largeur_edt=114;
}
//============================================================

echo "<div style='width:".($largeur_edt+2*40)."px; text-align:center;'>
	<p class='bold'>";

if((isset($login_ele_prec))&&($login_ele_prec!="")) {
	if($affichage=="semaine") {
		echo "
		<a href='".$_SERVER['PHP_SELF']."?login_eleve=".$login_ele_prec."&amp;affichage=semaine&amp;num_semaine_annee=$num_semaine_annee".$complement_liens_edt2."' title=\"Voir la page pour $nom_prenom_ele_prec\"><img src=\"../images/arrow_left.png\" class='icone16' alt=\"$nom_prenom_ele_prec\" /></a> ";
	}
	else {
		echo "
		<a href='".$_SERVER['PHP_SELF']."?login_eleve=".$login_ele_prec."&amp;display_date=$display_date&amp;affichage=jour".$complement_liens_edt2."' title=\"Voir la page pour $nom_prenom_ele_prec\"><img src=\"../images/arrow_left.png\" class='icone16' alt=\"$nom_prenom_ele_prec\" /></a> ";
	}
}

//https://127.0.0.1/steph/gepi_git_trunk/edt/index2.php?login_prof=BOIREAUS&id_classe=&type_affichage=prof&login_eleve=&num_semaine_annee=38|2015&affichage=semaine&mode=afficher_edt&afficher_sem_AB=y&csrf_alea=gHJVdqiyp4K8qlok7d5lN3ZMwvu6KGW578OZ2ov
$afficher_sem_AB=isset($_POST['afficher_sem_AB']) ? $_POST['afficher_sem_AB'] : (isset($_GET['afficher_sem_AB']) ? $_GET['afficher_sem_AB'] : "n");
if($afficher_sem_AB=="y") {
	$info_edt.=" <span title='Avec affichage des semaines A et B'>(A/B)</span>";
}

//echo $info_eleve;
echo "
		Emploi du temps de ".$info_edt;

if((isset($login_ele_suiv))&&($login_ele_suiv!="")) {
	if($affichage=="semaine") {
		echo "
		<a href='".$_SERVER['PHP_SELF']."?login_eleve=".$login_ele_suiv."&amp;affichage=semaine&amp;num_semaine_annee=$num_semaine_annee".$complement_liens_edt2."' title=\"Voir la page pour $nom_prenom_ele_suiv\"><img src=\"../images/arrow_right.png\" class='icone16' alt=\"$nom_prenom_ele_suiv\" /></a> ";
	}
	else {
		echo "
		 <a href='".$_SERVER['PHP_SELF']."?login_eleve=".$login_ele_suiv."&amp;display_date=$display_date&amp;affichage=jour".$complement_liens_edt2."' title=\"Voir la page pour $nom_prenom_ele_suiv\"><img src=\"../images/arrow_right.png\" class='icone16' alt=\"$nom_prenom_ele_suiv\" /></a>";
	}
}

echo "</p>
</div>";

//============================================================
if(($type_affichage=='eleve')&&(isset($login_eleve))&&($affichage_complementaire_sur_edt=="absences2")) {
	echo "<div style='float:right;width:;'><a href='#' id='lien_permuter_display_abs_visible' onclick=\"permuter_display_div_abs('')\" title=\"Afficher les absences.\"><img src='../images/icons/absences_visibles.png' width='26' height='26' alt='Afficher abs' /></a><a href='#' id='lien_permuter_display_abs_invisible' onclick=\"permuter_display_div_abs('none')\" title=\"Masquer les absences.\"><img src='../images/icons/absences_invisibles.png' width='26' height='26' alt='Masquer abs' /></a></div>";
}

//$affichage_complementaire_sur_edt="absences2";
//============================================================

$x0=50;
$y0=10;
$hauteur_une_heure=60;

//$html=affiche_edt2_eleve($login_eleve, $id_classe, $ts_display_date, $affichage, $x0, $y0, $largeur_edt, $hauteur_une_heure);
$html=affiche_edt2($login_eleve, $id_classe, $login_prof, $type_affichage, $ts_display_date, $affichage, $x0, $y0, $largeur_edt, $hauteur_une_heure);

$x1=10;
//$y1=150;
//echo $mode;
if($mode!="afficher_edt") {
	$y1=252;

	if(isset($_SESSION['ariane'])) {
		$y_decalage_1_js=210;
		$y_decalage_2_js=272;
	}
	else {
		$y_decalage_1_js=190;
		$y_decalage_2_js=252;
	}
}
else {
	$y1=30;

	$y_decalage_1_js=0;
	$y_decalage_2_js=30;
}
$marge_droite=5;
$largeur1=$largeur_edt+2*35;
$hauteur1=$hauteur_jour+$hauteur_entete+6;
$hauteur_div_sous_bandeau=20;
// border:1px solid black; background-color:".$tab_couleur_onglet['edt'].";
// border: 1px dashed red;
echo "<div id='div_edt' style='position:absolute; top:".$y1."px; left:".$x1."px; width:".$largeur1."px; height:".$hauteur1."px; margin-right:".$marge_droite."px; margin-bottom:".$marge_droite."px;'>".$html."</div>";

/*
//++++++++++++++++++++++++++++++++++++++++
// TMP
$login_eleve="baronc";
$id_classe="";
$login_prof="";
$ts_display_date=time();
$x0=50;
$y0=820;
$affichage="semaine";
$html=affiche_edt2($login_eleve, $id_classe, $login_prof, $type_affichage, $ts_display_date, $affichage, $x0, $y0);
echo $html;
//++++++++++++++++++++++++++++++++++++++++
// TMP
$login_eleve="";
$id_classe="20";
$login_prof="";
$ts_display_date=time();
$x0=50;
$y0=820+$hauteur_jour+100;
$affichage="semaine";
$html=affiche_edt2($login_eleve, $id_classe, $login_prof, $type_affichage, $ts_display_date, $affichage, $x0, $y0);
echo $html;
//++++++++++++++++++++++++++++++++++++++++
// TMP
$login_eleve="";
$id_classe="";
$login_prof="boireaus";
$ts_display_date=time();
$x0=50;
$y0=820+2*($hauteur_jour+100);
$affichage="semaine";
$html=affiche_edt2($login_eleve, $id_classe, $login_prof, $type_affichage, $ts_display_date, $affichage, $x0, $y0);
echo $html;
//++++++++++++++++++++++++++++++++++++++++
*/

$titre_infobulle="Action EDT";
$texte_infobulle="<!--p>Choix&nbsp;:</p-->
<div id='div_action_edt'></div>";
$tabdiv_infobulle[]=creer_div_infobulle('infobulle_action_edt',$titre_infobulle,"",$texte_infobulle,"",30,0,'y','y','n','n',4000);


//===============================================
/*
// Test infobulle
echo "<div style='clear:both'></div>";
$mode_infobulle="y";
$largeur_edt=600;
$y0=30;
$hauteur_une_heure=40;
$hauteur_jour=800;
$unite_div_infobulle="px";
$titre_infobulle="EDT";
$html=affiche_edt2($login_eleve, $id_classe, $login_prof, $type_affichage, $ts_display_date, $affichage, $x0, $y0, $largeur_edt, $hauteur_une_heure);
$texte_infobulle="<div class='infobulle_corps' style='width:700px;height:".($hauteur_jour+100)."px;'>&nbsp;</div>".$html;
$tabdiv_infobulle[]=creer_div_infobulle('edt_test',$titre_infobulle,"",$texte_infobulle,"","700",($hauteur_jour+100),'y','y','n','n');
echo "<p><a href=\"javascript:afficher_div('edt_test','y',-10,20)\">Afficher en infobulle l'EDT choisi</a></p>";
*/
//===============================================


if((isset($type_affichage))&&($type_affichage!="eleve")) {
	echo "
<script type='text/javascript'>
	if(document.getElementById('affichage_complementaire_sur_edt')) {
		document.getElementById('affichage_complementaire_sur_edt').style.display='none';
	}
</script>
";
}


echo "<!--div id='div_action_edt'></div-->

<script type='text/javascript'>
	// Action lancée lors du clic dans le div_edt
	function action_edt_cours(id_cours) {
		// Actuellement, aucune action n'est lancée.
		// La fonction est là pour éviter une erreur JavaScript

		new Ajax.Updater($('div_action_edt'),'".$_SERVER['PHP_SELF']."?action_js=y&id_cours='+id_cours+'&num_semaine_annee=".$num_semaine_annee."&affichage=".$affichage."',{method: 'get'});

		afficher_div('infobulle_action_edt', 'y', 10, 10);
		document.getElementById('infobulle_action_edt').style.zIndex=5000;
	}

	// Adaptation de l'emplacement vertical du div_edt
	function check_top_div_edt() {
		var temoin_petit_bandeau=0;
		var liste_pt_bandeau=document.getElementsByClassName('pt_bandeau');
		for(i=0;i<liste_pt_bandeau.length;i++) {
			id=liste_pt_bandeau[i].getAttribute('id');
			if(id=='bandeau') {
				document.getElementById('div_edt').style.top=".$y_decalage_1_js."+'px';
				temoin_petit_bandeau++;
				break;
			}
		}
		if(temoin_petit_bandeau==0) {
			document.getElementById('div_edt').style.top=".$y_decalage_2_js."+'px';
		}

		setTimeout('check_top_div_edt()',1000);
	}

	check_top_div_edt();
</script>";

require("../lib/footer.inc.php");
?>
