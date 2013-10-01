<?php
/*
 *
 * @version $Id$
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions complémentaires et/ou librairies utiles

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/statistiques/stat_connexions.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/statistiques/stat_connexions.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Statistiques de connexion',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}


if($_SESSION['statut']=='administrateur') {
	// On n'a pas de restrictions
}
elseif($_SESSION['statut']=='scolarite') {
	if((!getSettingAOui('AccesStatConnexionEleScolarite'))&&
	(!getSettingAOui('AccesDetailConnexionEleScolarite'))&&
	(!getSettingAOui('AccesStatConnexionRespScolarite'))&&
	(!getSettingAOui('AccesDetailConnexionRespScolarite'))) {
		header("Location: ../accueil.php?msg=Accès non autorisé.");
		die();
	}
}
elseif($_SESSION['statut']=='cpe') {
	if((!getSettingAOui('AccesStatConnexionEleCpe'))&&
	(!getSettingAOui('AccesDetailConnexionEleCpe'))&&
	(!getSettingAOui('AccesStatConnexionRespCpe'))&&
	(!getSettingAOui('AccesDetailConnexionRespCpe'))) {
		header("Location: ../accueil.php?msg=Accès non autorisé.");
		die();
	}
}
elseif($_SESSION['statut']=='professeur') {
	$acces="n";

	if((getSettingAOui('AccesStatConnexionEleProfesseur'))||
	(getSettingAOui('AccesDetailConnexionEleProfesseur'))||
	(getSettingAOui('AccesStatConnexionRespProfesseur'))||
	(getSettingAOui('AccesDetailConnexionRespProfesseur'))) {
		$acces="y";
	}

	if($acces=="n") {
		if(is_pp($_SESSION['login'])) {
			if((getSettingAOui('AccesStatConnexionEleProfP'))||
			(getSettingAOui('AccesDetailConnexionEleProfP'))||
			(getSettingAOui('AccesStatConnexionRespProfP'))||
			(getSettingAOui('AccesDetailConnexionRespProfP'))) {
				$acces="y";
			}
		}
	}

	if($acces=="n") {
		header("Location: ../accueil.php?msg=Accès non autorisé.");
		die();
	}
}
else {
	// On ne devrait pas arriver là pour le moment
	header("Location: ../accueil.php?msg=Accès non autorisé.");
	die();
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

if(isset($mode)) {
	if($_SESSION['statut']=='administrateur') {
		// On n'a pas de restrictions
	}
	elseif($_SESSION['statut']=='scolarite') {
		if(($mode==2)&&(!getSettingAOui('AccesStatConnexionRespScolarite'))&&(!getSettingAOui('AccesDetailConnexionRespScolarite'))) {
			unset($mode);
		}

		if(($mode==3)&&(!getSettingAOui('AccesStatConnexionEleScolarite'))&&(!getSettingAOui('AccesDetailConnexionEleScolarite'))) {
			unset($mode);
		}
	}
	elseif($_SESSION['statut']=='cpe') {
		if(($mode==2)&&(!getSettingAOui('AccesStatConnexionRespCpe'))&&(!getSettingAOui('AccesDetailConnexionRespCpe'))) {
			unset($mode);
		}
		if(($mode==3)&&(!getSettingAOui('AccesStatConnexionEleCpe'))&&(!getSettingAOui('AccesDetailConnexionEleCpe'))) {
			unset($mode);
		}
	}
	elseif($_SESSION['statut']=='professeur') {
		if($mode==2) {
			if(!isset($id_classe)) {
				$is_pp=is_pp($_SESSION['login']);

				$acces="n";
				if((getSettingAOui('AccesStatConnexionRespProfesseur'))||(getSettingAOui('AccesDetailConnexionRespProfesseur'))||
					(($is_pp)&&((getSettingAOui('AccesStatConnexionRespProfP'))||(getSettingAOui('AccesDetailConnexionRespProfP'))))) {
					$acces="y";
				}

				if($acces=="n") {
					unset($mode);
					$msg="Accès non autorisé<br />";
				}
			}
			else {
				$is_pp=is_pp($_SESSION['login'], $id_classe);
				$is_prof_classe=is_prof_classe($_SESSION['login'], $id_classe);

				$acces="n";
				if(($is_prof_classe)&&(getSettingAOui('AccesStatConnexionRespProfesseur'))||(getSettingAOui('AccesDetailConnexionRespProfesseur'))||
					(($is_pp)&&((getSettingAOui('AccesStatConnexionRespProfP'))||(getSettingAOui('AccesDetailConnexionRespProfP'))))) {
					$acces="y";
				}

				if($acces=="n") {
					unset($mode);
					$msg="Accès non autorisé<br />";
				}
			}
		}

		if($mode==3) {
			if(!isset($id_classe)) {
				$is_pp=is_pp($_SESSION['login']);

				$acces="n";
				if((getSettingAOui('AccesStatConnexionEleProfesseur'))||(getSettingAOui('AccesDetailConnexionEleProfesseur'))||
					(($is_pp)&&((getSettingAOui('AccesStatConnexionEleProfP'))||(getSettingAOui('AccesDetailConnexionEleProfP'))))) {
					$acces="y";
				}

				if($acces=="n") {
					unset($mode);
					$msg="Accès non autorisé<br />";
				}
			}
			else {
				$is_pp=is_pp($_SESSION['login'], $id_classe);
				$is_prof_classe=is_prof_classe($_SESSION['login'], $id_classe);

				$acces="n";
				if(($is_prof_classe)&&(getSettingAOui('AccesStatConnexionEleProfesseur'))||(getSettingAOui('AccesDetailConnexionEleProfesseur'))||
					(($is_pp)&&((getSettingAOui('AccesStatConnexionEleProfP'))||(getSettingAOui('AccesDetailConnexionEleProfP'))))) {
					$acces="y";
				}

				if($acces=="n") {
					unset($mode);
					$msg="Accès non autorisé<br />";
				}
			}
		}
	}
}

$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : NULL;
$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : NULL;


$sql="SELECT DISTINCT id, classe FROM classes ORDER BY classe;";
//echo "$sql<br />\n";
$res_classes=mysql_query($sql);
$nb_classes_0=mysql_num_rows($res_classes);

if($_SESSION['statut']=='administrateur') {
	$sql="SELECT DISTINCT id, classe FROM classes ORDER BY classe;";
}
elseif($_SESSION['statut']=='scolarite') {
	$sql="SELECT DISTINCT c.id, c.classe FROM classes c, j_scol_classes jsc WHERE c.id=jsc.id_classe AND jsc.login='".$_SESSION['login']."' ORDER BY c.classe;";
}
elseif($_SESSION['statut']=='cpe') {
	$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe=c.id ORDER BY c.classe";
}
elseif($_SESSION['statut']=='professeur') {
	//***********
	// A FAIRE
	//***********
	// Il faut restreindre la liste des classes selon qu'on a donné des accès PP ou Prof
	if(!isset($mode)) {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgp.id_groupe=jgc.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
	}
	elseif($mode==1) {
		if((getSettingAOui('AccesStatConnexionEleProfesseur'))||
		(getSettingAOui('AccesStatConnexionRespProfesseur'))||
		(getSettingAOui('AccesDetailConnexionEleProfesseur'))||
		(getSettingAOui('AccesDetailConnexionRespProfesseur'))
		) {
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgp.id_groupe=jgc.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
		}
		elseif((is_pp($_SESSION['login']))&&(
				(getSettingAOui('AccesStatConnexionEleProfP'))||
				(getSettingAOui('AccesStatConnexionRespProfP'))||
				(getSettingAOui('AccesDetailConnexionEleProfP'))||
				(getSettingAOui('AccesDetailConnexionRespProfP'))
				)) {
					$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_eleves_classes jec, j_eleves_professeurs jep WHERE jep.professeur = '".$_SESSION['login']."' AND jep.login=jec.login AND jec.id_classe=c.id ORDER BY c.classe";
		}
		else {
			header("Location: ./index.php?msg=Aucune classe n'a été trouvée pour vous.");
			die();
		}
	}
	elseif($mode==2) {
		if((getSettingAOui('AccesStatConnexionRespProfesseur'))||
		(getSettingAOui('AccesDetailConnexionRespProfesseur'))
		) {
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgp.id_groupe=jgc.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
		}
		elseif((is_pp($_SESSION['login']))&&(
				(getSettingAOui('AccesStatConnexionRespProfP'))||
				(getSettingAOui('AccesDetailConnexionRespProfP'))
				)) {
					$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_eleves_classes jec, j_eleves_professeurs jep WHERE jep.professeur = '".$_SESSION['login']."' AND jep.login=jec.login AND jec.id_classe=c.id ORDER BY c.classe";
		}
		else {
			header("Location: ./index.php?msg=Aucune classe n'a été trouvée pour vous.");
			die();
		}
	}
	elseif($mode==3) {
		if((getSettingAOui('AccesStatConnexionEleProfesseur'))||
		(getSettingAOui('AccesDetailConnexionEleProfesseur'))
		) {
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgp.id_groupe=jgc.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
		}
		elseif((is_pp($_SESSION['login']))&&(
				(getSettingAOui('AccesStatConnexionEleProfP'))||
				(getSettingAOui('AccesDetailConnexionEleProfP'))
				)) {
					$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_eleves_classes jec, j_eleves_professeurs jep WHERE jep.professeur = '".$_SESSION['login']."' AND jep.login=jec.login AND jec.id_classe=c.id ORDER BY c.classe";
		}
		else {
			header("Location: ./index.php?msg=Aucune classe n'a été trouvée pour vous.");
			die();
		}
	}
	else {
		// On ne devrait pas arriver là
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgp.id_groupe=jgc.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
	}
}
else {
	// On ne devrait pas arriver là.
	header("Location: ../accueil.php?msg=Accès non autorisé.");
	die();
}
//echo "$sql<br />\n";
$res_classes=mysql_query($sql);
$nb_classes=mysql_num_rows($res_classes);

$acces_toutes_classes="n";
if($nb_classes==$nb_classes_0) {
	$acces_toutes_classes="y";
}
//echo "\$acces_toutes_classes=$acces_toutes_classes<br />";

if($nb_classes>0) {
	$tab_classe=array();
	$cpt=0;
	while($lig_classe=mysql_fetch_object($res_classes)) {
		$tab_classe[$cpt]=array();
		$tab_classe[$cpt]['id']=$lig_classe->id;
		$tab_classe[$cpt]['classe']=$lig_classe->classe;

		$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$lig_classe->id';";
		$res_eff=mysql_query($sql);
		$tab_classe[$cpt]['effectif']=mysql_num_rows($res_eff);

		$cpt++;
	}
}

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

// ===================== entete Gepi ======================================//
$titre_page = "Statistiques de connexion";
require_once("../lib/header.inc.php");
// ===================== fin entete =======================================//

//debug_var();

//function tableau_php_tableau_html($tab, $nb_lig_max=5) {
function tableau_php_tableau_html($tab, $avec_lien="n", $statut="") {
	$retour="";

	$acces_lien=false;
	if(($avec_lien=="y")&&($statut=='responsable')) {
		$acces_lien=acces("/responsables/modify_resp.php", $_SESSION['statut']);
	}
	elseif(($avec_lien=="y")&&($statut=='eleve')) {
		$acces_lien=acces("/eleves/modify_eleve.php", $_SESSION['statut']);
	}

	//$retour.="<div float:left; width: 15em;'>\n";
	$retour.="<table class='boireaus'>\n";
	$alt=1;
	//$compteur=0;
	for($loop=0;$loop<count($tab);$loop++) {
		/*
		if($compteur>$nb_lig_max) {
			$retour.="</table>\n";
			$retour.="</div>\n";

			$retour.="<div float:left; width: 10em;'>\n";
			$retour.="<table class='boireaus'>\n";
			$compteur=0;
		}
		*/
		$alt=$alt*(-1);
		$retour.="<tr class='lig$alt white_hover'>\n";
		$retour.="<td>";
		if(($avec_lien=="y")&&($statut=='responsable')&&($acces_lien)) {
			$retour.="<a href='../responsables/modify_resp.php?login_resp=".$tab[$loop]."&amp;journal_connexions=y#connexion' target='_blank' title='Voir le journal des connexions de ce responsable.'>".civ_nom_prenom($tab[$loop])."</a>";
		}
		elseif(($avec_lien=="y")&&($statut=='eleve')&&($acces_lien)) {
			$retour.="<a href='../eleves/modify_eleve.php?eleve_login=".$tab[$loop]."&amp;journal_connexions=y#connexion' target='_blank' title='Voir le journal des connexions de cet(te) élève.'>".civ_nom_prenom($tab[$loop])."</a>";
		}
		else {
			$retour.=civ_nom_prenom($tab[$loop]);
		}
		$retour.="</td>\n";
		$retour.="</tr>\n";
		//$compteur++;
	}
	$retour.="</table>\n";
	//$retour.="</div>\n";

	return $retour;
}


/*
# 0 : logout normal
# 2 : logout renvoyé par la fonction checkAccess (problème gepiPath ou accès interdit)
# 3 : logout lié à un timeout
# 4 : logout lié à une nouvelle connexion sous un nouveau profil
*/

$AccesStatConnexionEle=AccesInfoEle("AccesStatConnexionEle");
$AccesDetailConnexionEle=AccesInfoEle("AccesDetailConnexionEle");
$AccesStatConnexionResp=AccesInfoResp("AccesStatConnexionResp");
$AccesDetailConnexionResp=AccesInfoResp("AccesDetailConnexionResp");

if(!isset($mode)) {
	echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
	
	if($nb_classes==0) {
		echo "<p style='color:red'>Aucune classe n'existe encore.</p>\n";
	
		require_once("../lib/footer.inc.php");
		die();
	}

	echo "<p>Choisissez&nbsp;:</p>\n";
	echo "<ul>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=1'>Statistiques globales de connexions élèves et responsables</a></li>\n";
	if(($AccesStatConnexionResp)||($AccesDetailConnexionResp)) {
		echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=2'>Statistiques des connexions parents d'une classe</a></li>\n";
	}
	if(($AccesStatConnexionEle)||($AccesDetailConnexionEle)) {
		echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=3'>Statistiques des connexions élèves d'une classe</a></li>\n";
	}
	echo "</ul>\n";

	echo "<p style='color:red'>Faire une autre graphique avec le nombre de connexions par semaine.</p>";
}
elseif($mode==1) {
	echo "<p class='bold'><a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	
	$sql="select START from log order by START ASC limit 1;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		// Toujours vrai si on est connecté pour consulter cette page
		$date_premier_log=mysql_result($res, 0);
		echo "<p>Les journaux de connexion remontent au ".formate_date($date_premier_log)."</p>";
	}
	
	$begin_bookings=getSettingValue('begin_bookings');
	$mysql_begin_bookings=strftime("%Y-%m-%d 00:00:00", $begin_bookings);
	echo "<p>Les journaux antérieurs à ".formate_date($mysql_begin_bookings)." ne seront pas pris en compte.</p>\n";

	$sql="SELECT DISTINCT l.login from log l, resp_pers rp where rp.login=l.login and autoclose>='0' AND autoclose<='3' AND START>='$mysql_begin_bookings';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>Aucun compte parent n'a encore essayé de (<em>ou réussi à</em>) se connecter.</p>\n";
	}
	else {
		echo "<p>".mysql_num_rows($res)." parent(s) a(ont) réussi à se connecter à ce jour (<em>pour toutes les classes de l'établissement</em>).</p>\n";
	
		$tab_parents_connectes_avec_succes=array();
		while($lig=mysql_fetch_object($res)) {
			$tab_parents_connectes_avec_succes[]=$lig->login;
		}

		/*
		// Pb: Il ne faut pas compter deux fois des parents d'enfants de 2 classes différentes
		//$nb_parents_differents_toutes_classes=0;
		$sql="SELECT DISTINCT e.login FROM log l, resp_pers rp, eleves e, responsables2 r, j_eleves_classes jec WHERE jec.login=e.login AND e.ele_id=r.ele_id AND rp.pers_id=r.pers_id AND rp.login=l.login AND l.autoclose>='0' AND l.autoclose<='3' AND START>='$mysql_begin_bookings' ORDER BY l.login;";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		$nb_parents_differents_toutes_classes=mysql_num_rows($res);
		// ERREUR: On compte les enfants et on peut avoir deux enfants d'une même famille
		*/
		$tab_ele_id_login_resp=array();
		$tab_login_resp_ele_id=array();

		$tab_ele_id_enfants_dont_un_parent_au_moins_a_reussi_a_se_connecter=array();
		$tab_parents_enfants_differents_connectes_avec_succes=array();
		$sql="SELECT DISTINCT l.login, r.pers_id FROM log l, resp_pers rp, eleves e, responsables2 r, j_eleves_classes jec WHERE jec.login=e.login AND e.ele_id=r.ele_id AND rp.pers_id=r.pers_id AND rp.login=l.login AND l.autoclose>='0' AND l.autoclose<='3' AND START>='$mysql_begin_bookings' ORDER BY r.ele_id, l.login;";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		while($lig=mysql_fetch_object($res)) {
			$sql="SELECT DISTINCT ele_id FROM responsables2 WHERE pers_id='$lig->pers_id';";
			$res2=mysql_query($sql);
			while($lig2=mysql_fetch_object($res2)) {
				if((!in_array($lig2->ele_id, $tab_ele_id_enfants_dont_un_parent_au_moins_a_reussi_a_se_connecter))&&(!in_array($lig->login, $tab_parents_enfants_differents_connectes_avec_succes))) {
					$tab_parents_enfants_differents_connectes_avec_succes[]=$lig->login;
				}
				$tab_ele_id_enfants_dont_un_parent_au_moins_a_reussi_a_se_connecter[]=$lig2->ele_id;

				$tab_ele_id_login_resp[$lig2->ele_id][]=$lig->login;
				$tab_login_resp_ele_id[$lig->login][]=$lig2->ele_id;
			}
		}
		$nb_parents_differents_toutes_classes=count($tab_parents_enfants_differents_connectes_avec_succes);

		$effectif_total_eleve=0;
		$effectif_total_eleve_connectes=0;
		$effectif_total_eleve_en_echec=0;

		$tab_liste_parents_erreur_mdp_et_jamais_connectes_avec_succes_toutes_classes=array();
		// Mettre les pourcentages aussi
		echo "<p>Nombre d'élèves et responsables connectés au moins une fois&nbsp;:</p>\n";
		echo "<table class='boireaus'>\n";
		echo "<tr>\n";
		echo "<th rowspan='2'>Classe</th>\n";
		echo "<th rowspan='2'>Effectif</th>\n";
		echo "<th colspan='2'>Elèves</th>\n";
		echo "<th rowspan='2'>Elèves toujours<br />en échec de<br />mot de passe</th>\n";
		echo "<th rowspan='2'>Parents</th>\n";
		echo "<th colspan='2' title=\"Si les deux parents d'un enfant se sont connectés, on ne les compte dans cette colonne que pour 1 (au sens d'une seule famille).\">Parents d'enfants<br />différents</th>\n";
		echo "<th rowspan='2'>Parents toujours<br />en échec de<br />mot de passe</th>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<th>Eff.</th>\n";
		echo "<th>%</th>\n";
		echo "<th>Eff.</th>\n";
		echo "<th>%</th>\n";
		echo "</tr>\n";
		$alt=1;
		for($i=0;$i<count($tab_classe);$i++) {

			if($_SESSION['statut']=='professeur') {
				$AccesStatConnexionEle=AccesInfoEle("AccesStatConnexionEle", "", $tab_classe[$i]['id']);
				$AccesDetailConnexionEle=AccesInfoEle("AccesDetailConnexionEle", "", $tab_classe[$i]['id']);
				$AccesStatConnexionResp=AccesInfoResp("AccesStatConnexionResp", "", "", $tab_classe[$i]['id']);
				$AccesDetailConnexionResp=AccesInfoResp("AccesDetailConnexionResp", "", "", $tab_classe[$i]['id']);
			}

			$sql="SELECT DISTINCT l.login FROM log l, j_eleves_classes jec WHERE jec.login=l.login AND jec.id_classe='".$tab_classe[$i]['id']."' AND l.autoclose>='0' AND l.autoclose<='3' AND l.login!='' AND START>='$mysql_begin_bookings' ORDER BY l.login;";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			$nb_ele=mysql_num_rows($res);
			$tab_ele=array();
			if($nb_ele>0) {
				while($lig=mysql_fetch_object($res)) {
					$tab_ele[]=$lig->login;
				}
			}

			$sql="SELECT DISTINCT l.login FROM log l, j_eleves_classes jec WHERE jec.login=l.login AND jec.id_classe='".$tab_classe[$i]['id']."' AND l.login!='' AND START>='$mysql_begin_bookings' ORDER BY l.login;";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			$nb_ele=mysql_num_rows($res);
			$tab_ele_en_echec=array();
			if($nb_ele>0) {
				while($lig=mysql_fetch_object($res)) {
					if(!in_array($lig->login, $tab_ele)) {
						$tab_ele_en_echec[]=$lig->login;
					}
				}
			}
			$effectif_total_eleve_en_echec+=count($tab_ele_en_echec);

			if(($AccesStatConnexionEle)||($AccesDetailConnexionEle)) {
				$titre_infobulle="Elèves connectés au moins une fois (<em>".$tab_classe[$i]['classe']."</em>)\n";
				$texte_infobulle="<div align='center'>".tableau_php_tableau_html($tab_ele, "y", "eleve")."</div>";
				$tabdiv_infobulle[]=creer_div_infobulle('div_ele_'.$i,$titre_infobulle,"",$texte_infobulle,"",25,0,'y','y','n','n');

				$titre_infobulle="Elèves toujours en échec de connexion (<em>".$tab_classe[$i]['classe']."</em>)\n";
				$texte_infobulle="<div align='center'>".tableau_php_tableau_html($tab_ele_en_echec)."</div>";
				$tabdiv_infobulle[]=creer_div_infobulle('div_ele_en_echec_'.$i,$titre_infobulle,"",$texte_infobulle,"",25,0,'y','y','n','n');
			}

			$sql="SELECT DISTINCT l.login FROM log l, resp_pers rp, eleves e, j_eleves_classes jec, responsables2 r WHERE jec.id_classe='".$tab_classe[$i]['id']."' AND jec.login=e.login AND e.ele_id=r.ele_id AND rp.pers_id=r.pers_id AND rp.login=l.login AND l.autoclose>='0' AND l.autoclose<='3' AND l.login!='' AND START>='$mysql_begin_bookings' ORDER BY l.login;";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			$nb_parents=mysql_num_rows($res);
			$tab_resp=array();
			if($nb_parents>0) {
				while($lig=mysql_fetch_object($res)) {
					$tab_resp[]=mb_strtoupper($lig->login);
					
				}
			}
			/*
			if($tab_classe[$i]['classe']=='5 D') {
				print_r($tab_resp);
			}
			*/
			if(($AccesStatConnexionResp)||($AccesDetailConnexionResp)) {
				$titre_infobulle="Parents connectés au moins une fois\n";
				$texte_infobulle="<div align='center'>".tableau_php_tableau_html($tab_resp, "y", "responsable")."</div>";
				$tabdiv_infobulle[]=creer_div_infobulle('div_resp_'.$i,$titre_infobulle,"",$texte_infobulle,"",25,0,'y','y','n','n');
			}

			$sql="SELECT DISTINCT l.login, r.pers_id FROM log l, resp_pers rp, eleves e, j_eleves_classes jec, responsables2 r WHERE jec.id_classe='".$tab_classe[$i]['id']."' AND jec.login=e.login AND e.ele_id=r.ele_id AND rp.pers_id=r.pers_id AND rp.login=l.login AND l.autoclose='4' AND l.login!='' AND START>='$mysql_begin_bookings' ORDER BY l.login;";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			$nb_parents_erreur_mdp=mysql_num_rows($res);
			$nb_parents_erreur_mdp_et_jamais_connectes_avec_succes=0;
			$tab_liste_parents_erreur_mdp_et_jamais_connectes_avec_succes=array();
			if($nb_parents_erreur_mdp>0) {
				while($lig=mysql_fetch_object($res)) {
					if(!in_array(mb_strtoupper($lig->login), $tab_resp)) {
						$nb_parents_erreur_mdp_et_jamais_connectes_avec_succes++;
						$tab_liste_parents_erreur_mdp_et_jamais_connectes_avec_succes[]=$lig->login;

						if(!in_array($lig->login, $tab_liste_parents_erreur_mdp_et_jamais_connectes_avec_succes_toutes_classes)) {
							if(($AccesStatConnexionResp)||($AccesDetailConnexionResp)) {
								$tab_liste_parents_erreur_mdp_et_jamais_connectes_avec_succes_toutes_classes[]=$lig->login;
							}

							$sql="SELECT DISTINCT ele_id FROM responsables2 WHERE pers_id='$lig->pers_id';";
							$res2=mysql_query($sql);
							while($lig2=mysql_fetch_object($res2)) {
								if(!isset($tab_login_resp_ele_id[$lig->login])) {$tab_login_resp_ele_id[$lig->login]=array();}
								if(!in_array($lig2->ele_id, $tab_login_resp_ele_id[$lig->login])) {
									$tab_login_resp_ele_id[$lig->login][]=$lig2->ele_id;
								}
							}

						}
					}
				}
			}
			/*
			if($tab_classe[$i]['classe']=='5 D') {
				print_r($tab_liste_parents_erreur_mdp_et_jamais_connectes_avec_succes);
			}
			*/
			if(($AccesStatConnexionResp)||($AccesDetailConnexionResp)) {
				$titre_infobulle="Parents en échec de connexion\n";
				$texte_infobulle="<div align='center'>".tableau_php_tableau_html($tab_liste_parents_erreur_mdp_et_jamais_connectes_avec_succes)."</div>";
				$tabdiv_infobulle[]=creer_div_infobulle('div_resp_echec_'.$i,$titre_infobulle,"",$texte_infobulle,"",25,0,'y','y','n','n');
			}

			// **********************************************************************************************
			/*
			// ERREUR: On compte les enfants et on peut avoir deux enfants d'une même famille dans une classe
			$sql="SELECT DISTINCT jec.login FROM log l, resp_pers rp, eleves e, j_eleves_classes jec, responsables2 r WHERE jec.id_classe='".$tab_classe[$i]['id']."' AND jec.login=e.login AND e.ele_id=r.ele_id AND rp.pers_id=r.pers_id AND rp.login=l.login AND l.autoclose>='0' AND l.autoclose<='3' AND START>='$mysql_begin_bookings' ORDER BY l.login;";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			$nb_parents_differents=mysql_num_rows($res);
			*/

			$tab_ele_id_enfants_dont_un_parent_au_moins_a_reussi_a_se_connecter_cette_classe=array();
			$tab_parents_enfants_differents_connectes_avec_succes_cette_classe=array();
			$sql="SELECT DISTINCT l.login, r.pers_id FROM log l, resp_pers rp, eleves e, responsables2 r, j_eleves_classes jec WHERE jec.id_classe='".$tab_classe[$i]['id']."' AND jec.login=e.login AND e.ele_id=r.ele_id AND rp.pers_id=r.pers_id AND rp.login=l.login AND l.autoclose>='0' AND l.autoclose<='3' AND START>='$mysql_begin_bookings' ORDER BY l.login;";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			while($lig=mysql_fetch_object($res)) {
				$sql="SELECT DISTINCT ele_id FROM responsables2 WHERE pers_id='$lig->pers_id';";
				$res2=mysql_query($sql);
				while($lig2=mysql_fetch_object($res2)) {
					if((!in_array($lig2->ele_id, $tab_ele_id_enfants_dont_un_parent_au_moins_a_reussi_a_se_connecter_cette_classe))&&(!in_array($lig->login, $tab_parents_enfants_differents_connectes_avec_succes_cette_classe))) {
						$tab_parents_enfants_differents_connectes_avec_succes_cette_classe[]=$lig->login;
					}
					$tab_ele_id_enfants_dont_un_parent_au_moins_a_reussi_a_se_connecter_cette_classe[]=$lig2->ele_id;
				}
			}
			$nb_parents_differents=count($tab_parents_enfants_differents_connectes_avec_succes_cette_classe);
			// **********************************************************************************************

			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
			echo "<td>";
			echo $tab_classe[$i]['classe'];
			echo "</td>\n";
	
			echo "<td>";
			echo $tab_classe[$i]['effectif'];
			$effectif_total_eleve+=$tab_classe[$i]['effectif'];
			echo "</td>\n";
	
			if($nb_ele>0) {
				echo "<td>";
				if(($nb_ele>0)&&(($AccesStatConnexionEle)||($AccesDetailConnexionEle))) {
					echo "<a href=\"#\" onclick=\"afficher_div_stat('div_ele_$i');return false;\"  onmouseover=\"afficher_div_stat('div_ele_$i')\">";
					echo $nb_ele."/".$tab_classe[$i]['effectif'];
					echo "</a>";
				}
				else {
					echo $nb_ele."/".$tab_classe[$i]['effectif'];
				}
				$effectif_total_eleve_connectes+=$nb_ele;
				echo "</td>\n";
				echo "<td>\n";
				echo (round(100*10*$nb_ele/$tab_classe[$i]['effectif'])/10);
				//echo "%";
				echo "</td>\n";
			}
			else {
				echo "<td>\n";
				echo $nb_ele;
				echo "</td>\n";
				echo "<td>\n";
				echo $nb_ele;
				echo "</td>\n";
			}

			echo "<td>\n";
			if((count($tab_ele_en_echec)>0)&&(($AccesStatConnexionEle)||($AccesDetailConnexionEle))) {
				echo "<a href=\"#\" onclick=\"afficher_div_stat('div_ele_en_echec_$i');return false;\"  onmouseover=\"afficher_div_stat('div_ele_en_echec_$i')\">";
				echo count($tab_ele_en_echec);
				echo "</a>";
			}
			else {
				echo count($tab_ele_en_echec);
			}
			echo "</td>\n";

			echo "<td>";
			if($nb_parents>0) {
				if(($AccesStatConnexionResp)||($AccesDetailConnexionResp)) {
					echo "<a href=\"#\" onclick=\"afficher_div_stat('div_resp_$i');return false;\"  onmouseover=\"afficher_div_stat('div_resp_$i')\">";
					echo $nb_parents;
					echo "</a>";
				}
				else {
					echo $nb_parents;
				}
			}
			else {
				echo $nb_parents;
			}
			echo "</a>";
			echo "</td>\n";
	
			if($nb_parents_differents>0) {
				echo "<td>";
				echo $nb_parents_differents;
				echo "</td>\n";
				echo "<td>\n";
				echo (round(100*10*$nb_parents_differents/$tab_classe[$i]['effectif'])/10);
				//echo "%";
				echo "</td>\n";
			}
			else {
				echo "<td>\n";
				echo $nb_parents_differents;
				echo "</td>\n";
				echo "<td>\n";
				echo $nb_parents_differents;
				echo "</td>\n";
			}
	
			echo "<td>";
			if($nb_parents_erreur_mdp_et_jamais_connectes_avec_succes>0) {
				if(($AccesStatConnexionResp)||($AccesDetailConnexionResp)) {
					echo "<a href=\"#\" onclick=\"afficher_div_stat('div_resp_echec_$i');return false;\"  onmouseover=\"afficher_div_stat('div_resp_echec_$i')\" style='color:red'>";
					echo $nb_parents_erreur_mdp_et_jamais_connectes_avec_succes;
					echo "</a>";
				}
				else {
					echo $nb_parents_erreur_mdp_et_jamais_connectes_avec_succes;
				}
			}
			else {
				echo $nb_parents_erreur_mdp_et_jamais_connectes_avec_succes;
			}
			echo "</td>\n";
			echo "</tr>\n";
			flush();
		}

		$nb_parents_erreur_mdp_et_jamais_connectes_avec_succes_toutes_classes=count($tab_liste_parents_erreur_mdp_et_jamais_connectes_avec_succes_toutes_classes);

		if($acces_toutes_classes=="y") {
			echo "<tr>\n";
			echo "<th>Total</th>\n";
			echo "<th title=\"Effectif total des élèves\">$effectif_total_eleve</th>\n";
			echo "<th title=\"Total des élèves connectés avec succès\">$effectif_total_eleve_connectes</th>\n";
			echo "<th title=\"Pourcentage d'élèves connectés avec succès\">".(100*round($effectif_total_eleve_connectes/$effectif_total_eleve,3))."</th>\n";
			echo "<th title=\"Total des élèves ayant échoué à se connecter\">".$effectif_total_eleve_en_echec."</th>\n";
			echo "<th title=\"Total des parents connectés avec succès\">".count($tab_parents_connectes_avec_succes)."</th>\n";
			echo "<th title=\"Total des familles connectées avec succès\">$nb_parents_differents_toutes_classes</th>\n";
			echo "<th>-</th>\n";
			echo "<th title=\"Total des parents ayant échoué à se connecter\">".$nb_parents_erreur_mdp_et_jamais_connectes_avec_succes_toutes_classes."</th>\n";
			echo "</tr>\n";
		}

		echo "</table>\n";

		echo "<p><br /></p>\n";
		if($nb_parents_erreur_mdp_et_jamais_connectes_avec_succes_toutes_classes>0) {
			echo "<p><strong>Liste des parents ayant tenté de se connecter, mais jamais avec succès&nbsp;: (<em title=\"$nb_parents_erreur_mdp_et_jamais_connectes_avec_succes_toutes_classes responsables ne se sont jamais connectés avec succès.\">$nb_parents_erreur_mdp_et_jamais_connectes_avec_succes_toutes_classes</em>) </strong><br />";
			for($loop=0;$loop<$nb_parents_erreur_mdp_et_jamais_connectes_avec_succes_toutes_classes;$loop++) {
				if($loop>0) {echo ", ";}

/*
				$tab_ele_id_login_resp[$lig2->ele_id][]=$lig->login;
				$tab_login_resp_ele_id[$lig->login][]=$lig2->ele_id;
*/
				$color="red";
				for($loop2=0;$loop2<count($tab_login_resp_ele_id[$tab_liste_parents_erreur_mdp_et_jamais_connectes_avec_succes_toutes_classes[$loop]]);$loop2++) {
					if(isset($tab_ele_id_login_resp[$tab_login_resp_ele_id[$tab_liste_parents_erreur_mdp_et_jamais_connectes_avec_succes_toutes_classes[$loop]][$loop2]])) {
						$color="green";
						break;
					}
				}
				echo "<span style='color:$color'";
				if($color=="red") {
					echo " title=\"Ni ce responsable ni son conjoint ou sa conjointe ne s'est connecté(e).\"";
				}
				else {
					echo " title=\"Le conjoint ou la conjointe est parvenu(e) à connecter.\"";
				}
				echo ">";
				echo civ_nom_prenom($tab_liste_parents_erreur_mdp_et_jamais_connectes_avec_succes_toutes_classes[$loop]);
				echo "</span>";
			}
			echo ".</p>\n";
		}
	}

	echo "<p><br /></p>\n";

	echo "<script type='text/javascript'>
	function afficher_div_stat(id) {
		if(document.getElementById(id)) {
			for(i=0;i<".count($tab_classe).";i++) {
				if(document.getElementById('div_ele_'+i)) {cacher_div('div_ele_'+i);}
				if(document.getElementById('div_resp_'+i)) {cacher_div('div_resp_'+i);}
				if(document.getElementById('div_resp_echec_'+i)) {cacher_div('div_resp_echec_'+i);}
			}
			afficher_div(id,'y',10,10);
		}
	}
</script>\n";
}
elseif(($mode==2)||($mode==3)) {
	echo "<p class='bold'><a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

	$categorie_utilisateur[2]="responsable";
	$categorie_utilisateur[3]="élève";

	echo "<p class='bold'>Connexions ".$categorie_utilisateur[$mode]."s&nbsp;:</p>\n";

	//=======================
	//Configuration du calendrier
	//include("../lib/calendrier/calendrier.class.php");
	//$cal1 = new Calendrier("formulaire", "display_date_debut");
	//$cal2 = new Calendrier("formulaire", "display_date_fin");
	
	$annee = strftime("%Y");
	$mois = strftime("%m");
	$jour = strftime("%d");
	
	if($mois>8) {$date_debut_tmp="01/09/$annee";} else {$date_debut_tmp="01/09/".($annee-1);}
	
	//$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : (isset($_SESSION['display_date_debut']) ? $_SESSION['display_date_debut'] : $jour."/".$mois."/".$annee);
	$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : (isset($_SESSION['display_date_debut']) ? $_SESSION['display_date_debut'] : $date_debut_tmp);
	
	$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : (isset($_SESSION['display_date_fin']) ? $_SESSION['display_date_fin'] : $jour."/".$mois."/".$annee);
	//=======================
	
	echo "<br />\n";
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<fieldset id='connexions' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); '>\n";
	echo "<legend style='border: 1px solid grey; background-color: white;  '>Connexions ".$categorie_utilisateur[$mode]."</legend>\n";
	echo add_token_field();
	
	// Choix de la classe
	
	// =================================
	// AJOUT: boireaus
	$chaine_options_classes="";

	if(count($tab_classe)>0) {
		$id_class_prec=0;
		$id_class_suiv=0;
		$temoin_tmp=0;
		//$cpt_classe=0;
		$num_classe=-1;
		for($loop=0;$loop<count($tab_classe);$loop++) {
			if((isset($id_classe))&&($tab_classe[$loop]['id']==$id_classe)) {
				// Index de la classe dans les <option>
				$num_classe=$loop;

				$chaine_options_classes.="<option value='".$tab_classe[$loop]['id']."' selected='true'>".$tab_classe[$loop]['classe']."</option>\n";
				$temoin_tmp=1;
				if($loop+1<count($tab_classe)) {
					$id_class_suiv=$tab_classe[$loop+1]['id'];
				}
				else{
					$id_class_suiv=0;
				}
			}
			else {
				$chaine_options_classes.="<option value='".$tab_classe[$loop]['id']."'>".$tab_classe[$loop]['classe']."</option>\n";
			}
	
			if($temoin_tmp==0){
				$id_class_prec=$tab_classe[$loop]['id'];
			}
		}
		/*
		while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
			if((isset($id_classe))&&($lig_class_tmp->id==$id_classe)) {
				// Index de la classe dans les <option>
				$num_classe=$cpt_classe;
	
				$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
				$temoin_tmp=1;
				if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
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
	
			if($temoin_tmp==0){
				$id_class_prec=$lig_class_tmp->id;
			}
			$cpt_classe++;
		}
		*/
	}// =================================
	
	echo "Classe&nbsp;: <select name='id_classe' id='id_classe'>\n";
	echo $chaine_options_classes;
	echo "</select>\n";
	
	echo "<br />\n";
	
	echo "De la date&nbsp;: ";
	
	echo "<input type='text' name = 'display_date_debut' id = 'display_date_debut' size='10' value = \"".$display_date_debut."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />";
	
	//echo "<a href=\"#calend\" onClick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
	echo img_calendrier_js("display_date_debut", "img_bouton_display_date_debut");

	echo "&nbsp;à la date&nbsp;: ";
	echo "<input type='text' name = 'display_date_fin' id = 'display_date_fin' size='10' value = \"".$display_date_fin."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />";
	echo "<label for='choix_periode_dates' style='cursor: pointer;'>";
	//echo "<a href=\"#calend\" onClick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
	echo img_calendrier_js("display_date_fin", "img_bouton_display_date_fin");

	echo "<br />\n";
	echo " (<i>Veillez à respecter le format jj/mm/aaaa</i>)\n";
	echo "<input type='hidden' name='mode' value='$mode' />\n";
	echo "<input type='submit' value='Valider' />\n";
	echo "</fieldset>\n";
	echo "</form>\n";
	echo "<br />\n";

	$tab=explode("/", $display_date_debut);
	$jour_debut=$tab[0];
	$mois_debut=$tab[1];
	$annee_debut=$tab[2];

	$tab=explode("/", $display_date_fin);
	$jour_fin=$tab[0];
	$mois_fin=$tab[1];
	$annee_fin=$tab[2];

	//$timestamp_debut=gmmktime(0, 0, 0, $mois_debut, $jour_debut, $annee_debut);
	//$timestamp_fin=gmmktime(0, 0, 0, $mois_fin, $jour_fin, $annee_fin);

	$date_mysql_debut="$annee_debut-$mois_debut-$jour_debut 00:00:00";
	$date_mysql_fin="$annee_fin-$mois_fin-$jour_fin 00:00:00";

	if($id_classe!='') {

		$AccesStatConnexionEle=AccesInfoEle("AccesStatConnexionEle", "", $id_classe);
		$AccesDetailConnexionEle=AccesInfoEle("AccesDetailConnexionEle", "", $id_classe);
		$AccesStatConnexionResp=AccesInfoResp("AccesStatConnexionResp", "", "", $id_classe);
		$AccesDetailConnexionResp=AccesInfoResp("AccesDetailConnexionResp", "", "", $id_classe);

		if(($mode==2)&&(!$AccesStatConnexionResp)&&(!$AccesDetailConnexionResp)) {
			echo "<p style='color:red'>Vous n'avez pas accès à cette classe.</p>\n";
			require_once("../lib/footer.inc.php");
			die();
		}

		if(($mode==3)&&(!$AccesStatConnexionEle)&&(!$AccesDetailConnexionEle)) {
			echo "<p style='color:red'>Vous n'avez pas accès à cette classe.</p>\n";
			require_once("../lib/footer.inc.php");
			die();
		}

		$sql_partie[2]="SELECT DISTINCT l.login, l.START from log l, resp_pers rp, responsables2 r, eleves e, j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND e.login=jec.login AND r.ele_id=e.ele_id AND r.pers_id=rp.pers_id AND rp.login=l.login AND l.login!='' AND autoclose>='0' AND autoclose<='3' AND START>='".$date_mysql_debut."' AND END<='".$date_mysql_fin."' ORDER BY l.START, l.login;";

		$sql_partie[3]="SELECT DISTINCT l.login, l.START from log l, eleves e, j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND e.login=jec.login AND e.login=l.login AND l.login!='' AND autoclose>='0' AND autoclose<='3' AND START>='".$date_mysql_debut."' AND END<='".$date_mysql_fin."' ORDER BY l.START, l.login;";

		$sql=$sql_partie[$mode];
		//echo "$sql<br />";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p>Aucun compte ".$categorie_utilisateur[$mode]." n'a encore essayé de (<em>ou réussi à</em>) se connecter.</p>\n";
		}
		else {

			$tab_connexions=array();
			while($lig=mysql_fetch_object($res)) {
				$tab=explode(" ", $lig->START);
				$date=$tab[0];
				/*
				$tmp_date=explode("-", $date);
				$jour=$tmp_date[2];
				$mois=$tmp_date[1];
				$annee=$tmp_date[0];
	
				$timestamp=gmmktime(0, 0, 0, $mois, $jour, $annee);
				//$date=$timestamp;
				*/
				if(!isset($tab_connexions[$date]['login'])) {
					$tab_connexions[$date]['login']=array();
				}
				if(!in_array(mb_strtoupper($lig->login), $tab_connexions[$date]['login'])) {
					$tab_connexions[$date]['login'][]=mb_strtoupper($lig->login);
				}
			}
	
			/*
			echo "<pre>";
			print_r($tab_connexions);
			echo "</pre>";
			*/
	
			$timestamp_debut=gmmktime(0, 0, 0, $mois_debut, $jour_debut, $annee_debut);
			$timestamp_fin=gmmktime(0, 0, 0, $mois_fin, $jour_fin, $annee_fin);
	
			$js_jour_sem="";
			$js_jour="";
			$js_mois="";
	
			$my_echo_debug=0;
			$mode_my_echo_debug="";
	
			my_echo_debug("<table class='boireaus'>\n");
			my_echo_debug("<tr>\n");
			$timestamp=$timestamp_debut;
			while($timestamp<=$timestamp_fin) {
				$date=strftime("%Y-%m-%d", $timestamp);
	
				my_echo_debug("<td style='font-size:x-small'>".$date."</td>\n");
		
				if($js_jour_sem!="") {$js_jour_sem.=",";}
				$js_jour_sem.="'".strftime("%a", $timestamp)."'";
				
				if($js_jour!="") {$js_jour.=",";}
				$js_jour.="'".strftime("%d", $timestamp)."'";
				
				if($js_mois!="") {$js_mois.=",";}
				$js_mois.="'".strftime("%m", $timestamp)."'";
	
				$timestamp+=3600*24;
			}
			my_echo_debug("</tr>\n");
	
			$js_eff="";
			my_echo_debug("<tr>\n");
			$timestamp=$timestamp_debut;
			$compteur_jour=0;
			$max_eff=0;
			while($timestamp<=$timestamp_fin) {
				$date=strftime("%Y-%m-%d", $timestamp);
	
				my_echo_debug("<td>");
				if(isset($tab_connexions["$date"])) {
					$eff=count($tab_connexions["$date"]['login']);
					if($eff>$max_eff) {$max_eff=$eff;}
				}
				else {
					$eff=0;
				}
				my_echo_debug($eff);
	
				if($js_eff!="") {$js_eff.=",";}
				$js_eff.="'".$eff."'";
	
				my_echo_debug("</td>\n");
	
				$timestamp+=3600*24;
				$compteur_jour++;
			}
			my_echo_debug("</tr>\n");
			my_echo_debug("</table>\n");
	
			
			$largeur_barre=10;
			$largeur_totale=ceil(1.5*$largeur_barre*$compteur_jour)+20*2;
			
			$unite_y=20;
			
			$hauteur_totale=max(100,$unite_y*$max_eff)+3*$unite_y;
			$hauteur_totale_min=$hauteur_totale-40;
			$nb_grad_y=floor($hauteur_totale_min/$unite_y);
		
			echo "<canvas id='diagramme' width='$largeur_totale' height='$hauteur_totale'></canvas>\n";
	
			echo "<script type='text/javascript'>
	var texte_mois=new Array();
	texte_mois[1]='Janvier';
	texte_mois[2]='Février';
	texte_mois[3]='Mars';
	texte_mois[4]='Avril';
	texte_mois[5]='Mai';
	texte_mois[6]='Juin';
	texte_mois[7]='Juillet';
	texte_mois[8]='Aout';
	texte_mois[9]='Septembre';
	texte_mois[10]='Octobre';
	texte_mois[11]='Novembre';
	texte_mois[12]='Décembre';
	
	// Fonction de tracer de trait
	function tracer_ligne(ctx,x1,y1,x2,y2)  {
		ctx.beginPath();
		ctx.moveTo(x1, y1);
		ctx.lineTo(x2, y2);
		ctx.closePath();
		ctx.stroke();
	}
	
	var canvas = document.getElementById('diagramme');
	var context = canvas.getContext('2d');
	
	// Fond
	context.beginPath();
	context.rect(0, 0, $largeur_totale, $hauteur_totale);
	context.closePath();
	context.stroke();
	context.fillStyle = 'white';
	context.fill();
	
	var jour = [$js_jour];
	var jour_sel = [$js_jour_sem];
	var mois = [$js_mois];
	var eff = [$js_eff];
	
	// Origine du repere
	context.translate(20,".$hauteur_totale_min.");
	var x0 = 0;
	var y0 = 0;
	
	var largeur_barre = $largeur_barre;
	context.lineWidth = '1.0';
	
	// Couleur et largeur du trait
	context.fillStyle = '#000';
	context.lineWidth = '1.0';
	
	tracer_ligne(context,x0,y0,x0,-$hauteur_totale_min);
	
	context.font = '7pt Calibri,Geneva,Arial';
	context.lineWidth = '0.6';
	
	context.fillStyle = 'black';
	//context.strokeStyle = 'green';
	for(i=0;i<$nb_grad_y;i++) {
		//context.lineWidth = '0.5';
		//context.strokeText(i, x0-10, y0-i*$unite_y);
		context.fillText(i, x0-10, y0-i*$unite_y);
		//context.lineWidth = '0.1';
		tracer_ligne(context,x0,y0-i*$unite_y,$largeur_totale-20,y0-i*$unite_y);
	}
	
	context.lineWidth = '1.0';
	
	/*
	context.fillStyle = 'red';
	context.beginPath();
	context.rect(x0, y0, 30, 10);
	context.closePath();
	context.stroke();
	context.fill();
	context.fillStyle = '#000';
	
	context.fillStyle = 'red';
	context.beginPath();
	context.rect(x0+40, y0, 30, -50);
	context.closePath();
	context.stroke();
	context.fill();
	context.fillStyle = '#000';
	*/

	context.fillStyle = 'black';
	context.lineWidth = '1.0';
	context.fillText('Connexions ".$categorie_utilisateur[$mode]."s', x0+20, -$hauteur_totale_min+10);

	mois_precedent='';
	for (i=0; i<jour.length; i++) {
		context.fillStyle = 'steelblue';
		context.beginPath();
		//context.rect(x0+10 + (i * largeur_barre) +5*i, y0 -1 - eff[i], largeur_barre, 10*eff[i]);
		context.rect(x0+10+Math.round(i*1.5*largeur_barre), y0, largeur_barre, -$unite_y*eff[i]);
		context.closePath();
		context.stroke();
		context.fill();
	
		//context.lineWidth = '0.5';
		//context.lineWidth = '0.6';
		context.fillStyle = 'black';
		context.lineWidth = '1.0';
		//context.strokeText(jour[i], x0+10+Math.round(i*1.5*largeur_barre), y0+10);
		context.fillText(jour[i], x0+10+Math.round(i*1.5*largeur_barre), y0+10);
		j=jour_sel[i];
		//context.strokeText(j.substr(0,1), x0+10+Math.round(i*1.5*largeur_barre), y0+20);
		context.fillText(j.substr(0,1).toUpperCase(), x0+10+Math.round(i*1.5*largeur_barre), y0+20);
	
		if(mois[i]!=mois_precedent) {
			context.fillText(texte_mois[eval(mois[i])], x0+10+Math.round(i*1.5*largeur_barre), y0+30);
			//context.fillText(mois[i], x0+10+Math.round(i*1.5*largeur_barre), y0+30);
			mois_precedent=mois[i];
		}
	
		//context.fillStyle = '#000';
	
		//var mesure_texte = context.measureText(ages [i]).width;
		//var centrer_texte = (largeur_barre - mesure_texte)/2;
		//context.fillText(ages[i], x0+10 +centrer_texte + (i * largeur_barre) + 5*i, y0 + 18);
	}
	
</script>\n";


			if((($mode==2)&&($AccesDetailConnexionResp))||
				(($mode==3)&&($AccesDetailConnexionEle))) {

				$ligne_date="<tr>";
				$ligne_eff="<tr>";
				$ligne_personnes="<tr>";
				$alt=1;
				$tab_designation=array();
				foreach($tab_connexions as $date => $tab_login) {
					$alt=$alt*(-1);
					$ligne_date.="<td class='lig$alt'>".formate_date($date)."</td>";
					$ligne_eff.="<td class='lig$alt' title=\"Effectif de connexion\">".count($tab_login['login'])."</td>";
					$ligne_personnes.="<td class='lig$alt' style='vertical-align:top'>";
					for($loop=0;$loop<count($tab_login['login']);$loop++) {
						if($mode==2) {
							if(!array_key_exists($tab_login['login'][$loop], $tab_designation)) {$tab_designation[$tab_login['login'][$loop]]=civ_nom_prenom($tab_login['login'][$loop]);}
						}
						else {
							if(!array_key_exists($tab_login['login'][$loop], $tab_designation)) {$tab_designation[$tab_login['login'][$loop]]=get_nom_prenom_eleve($tab_login['login'][$loop]);}
						}
						$ligne_personnes.=$tab_designation[$tab_login['login'][$loop]]."<br />";
					}
					$ligne_personnes.="</td>";
				}
				$ligne_date.="</tr>";
				$ligne_eff.="</tr>";
				$ligne_personnes.="</tr>";
				echo "<table class='boireaus' summary='Identité des personnes connectées'>
	$ligne_date
	$ligne_eff
	$ligne_personnes
</table>";
			}
		}
	}
}
/*
for($i=0;$i<30;$i++) {
	echo "<p><br /></p>\n";
}
*/
//echo "<p><em>NOTES&nbsp;:</em></p>\n";

require_once("../lib/footer.inc.php");
?>
