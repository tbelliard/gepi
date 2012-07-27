<?php
/*
 * $Id : $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Initialisations files
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

// INSERT INTO droits VALUES ('/mod_annees_anterieures/consultation_annee_anterieure.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Consultation des données d années antérieures', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	//echo "Refus checkaccess";
    die();
}


// Si le module n'est pas activé...
if(getSettingValue('active_annees_anterieures')!="y"){
	// A DEGAGER
	// A VOIR: Comment enregistrer une tentative d'accès illicite?
	tentative_intrusion(1, "Tentative d'un ".$_SESSION["statut"]." (".$_SESSION["login"].") d'accéder au module Années antérieures qui n'est pas activé.");

	header("Location: ../logout.php?auto=1");
	//echo "active_annees_anterieures=".getSettingValue('active_annees_anterieures');
	die();
}

$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;

$logineleve=isset($_GET['logineleve']) ? $_GET['logineleve'] : NULL;

$annee_scolaire=isset($_GET['annee_scolaire']) ? $_GET['annee_scolaire'] : NULL;
$num_periode=isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL;

$mode=isset($_GET['mode']) ? $_GET['mode'] : NULL;

$aff_classe=isset($_GET['aff_classe']) ? $_GET['aff_classe'] : NULL;



$acces="n";
if($_SESSION['statut']=="administrateur"){
	$acces="y";
	$sql_classes="SELECT DISTINCT id,classe FROM classes ORDER BY classe";

	if(isset($id_classe)){
		$sql_ele="SELECT DISTINCT e.nom,e.prenom,e.login FROM eleves e,j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND jec.login=e.login ORDER BY e.nom,e.prenom";
	}
}
elseif($_SESSION['statut']=="professeur"){
	// $AAProfTout
	// $AAProfPrinc
	// $AAProfClasses
	// $AAProfGroupes

	$AAProfTout=getSettingValue('AAProfTout');
	$AAProfPrinc=getSettingValue('AAProfPrinc');
	$AAProfClasses=getSettingValue('AAProfClasses');
	$AAProfGroupes=getSettingValue('AAProfGroupes');

	//echo "\$AAProfTout=$AAProfTout<br />";
	//echo "\$AAProfPrinc=$AAProfPrinc<br />";
	//echo "\$AAProfClasses=$AAProfClasses<br />";
	//echo "\$AAProfGroupes=$AAProfGroupes<br />";

	if($AAProfTout=="yes"){
		// Le professeur a accès aux données antérieures de tous les élèves
		$acces="y";

		$sql_classes="SELECT DISTINCT id,classe FROM classes ORDER BY classe";

		if(isset($id_classe)){
			$sql_ele="SELECT DISTINCT e.nom,e.prenom,e.login FROM eleves e,j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND jec.login=e.login ORDER BY e.nom,e.prenom";
			//echo "$sql_ele<br />";
		}
	}
	elseif($AAProfClasses=="yes"){
		$acces="y";

		$sql_classes="SELECT DISTINCT c.id,c.classe FROM classes c,
										j_eleves_groupes jeg,
										j_groupes_classes jgc,
										j_groupes_professeurs jgp
								WHERE jeg.id_groupe=jgc.id_groupe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='".$_SESSION['login']."' AND
										jgc.id_classe=c.id
										ORDER BY c.classe;";
		//echo "$sql_classes<br />";

		if(isset($id_classe)){
			$sql_ele="SELECT DISTINCT e.nom,e.prenom,e.login FROM eleves e,
											j_eleves_classes jec
								WHERE jec.id_classe='$id_classe' AND
										jec.login=e.login
								ORDER BY e.nom,e.prenom";
			//echo "$sql_ele<br />";
		}

		// On vérifie qu'il n'y a pas tentative d'intrusion illicite:
		if(isset($logineleve)){
			$sql="SELECT 1=1 FROM j_eleves_classes jec, j_groupes_classes jgc, j_groupes_professeurs jgp
							WHERE jec.login='$logineleve' AND
									jec.id_classe=jgc.id_classe AND
									jgc.id_groupe=jgp.id_groupe AND
									jgp.login='".$_SESSION['login']."';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0){
				// A DEGAGER
				// A VOIR: Comment enregistrer une tentative d'accès illicite?
				//echo "$sql<br />";
				tentative_intrusion(1, "Tentative d'un ".$_SESSION["statut"]." (".$_SESSION["login"].") d'accéder aux données d'Années antérieures de $logineleve qui n'est pas élève d'une de ses classes.");
				header("Location: ../logout.php?auto=1");
				die();
			}
		}
	}
	elseif($AAProfGroupes=="yes"){
		$acces="y";

		$sql_classes="SELECT DISTINCT c.id,c.classe FROM classes c,
														j_eleves_groupes jeg,
														j_groupes_professeurs jgp,
														j_eleves_classes jec
												WHERE jeg.id_groupe=jgp.id_groupe AND
														jgp.login='".$_SESSION['login']."' AND
														jeg.login=jec.login AND
														jec.id_classe=c.id
														ORDER BY c.classe;";
		//echo "$sql_classes<br />";

		if(isset($id_classe)){
			$sql_ele="SELECT DISTINCT e.nom,e.prenom,e.login FROM eleves e,
											j_eleves_classes jec,
											j_eleves_groupes jeg,
											j_groupes_classes jgc,
											j_groupes_professeurs jgp
								WHERE jec.id_classe='$id_classe' AND
										jec.login=e.login AND
										jeg.login=jec.login AND
										jeg.id_groupe=jgc.id_groupe AND
										jgp.id_groupe=jgc.id_groupe AND
										jgp.login='".$_SESSION['login']."'
								ORDER BY e.nom,e.prenom";
			//echo "$sql_ele<br />";
		}

		// On vérifie qu'il n'y a pas tentative d'intrusion illicite:
		if(isset($logineleve)){
			$sql="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_professeurs jgp
							WHERE jeg.login='$logineleve' AND
									jeg.id_groupe=jgp.id_groupe AND
									jgp.login='".$_SESSION['login']."';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0){
				// A DEGAGER
				// A VOIR: Comment enregistrer une tentative d'accès illicite?
				//echo "$sql<br />";
				tentative_intrusion(1, "Tentative d'un ".$_SESSION["statut"]." (".$_SESSION["login"].") d'accéder aux données d'Années antérieures de $logineleve qui n'est pas élève d'un de ses enseignements.");
				header("Location: ../logout.php?auto=1");
				die();
			}
		}
	}
	elseif($AAProfPrinc=="yes"){
		$acces="y";

		$sql_classes="SELECT DISTINCT c.id,c.classe FROM classes c,
														j_eleves_professeurs jep
												WHERE jep.professeur='".$_SESSION['login']."' AND
														jep.id_classe=c.id
														ORDER BY c.classe";
		//echo "$sql_classes<br />";

		if(isset($id_classe)){
			$sql_ele="SELECT DISTINCT e.nom,e.prenom,e.login FROM eleves e,
											j_eleves_professeurs jep
								WHERE jep.id_classe='$id_classe' AND
										jep.login=e.login AND
										jep.professeur='".$_SESSION['login']."'
								ORDER BY e.nom,e.prenom";
			//echo "$sql_ele<br />";
		}

		// On vérifie qu'il n'y a pas tentative d'intrusion illicite:
		if(isset($logineleve)){
			$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."' AND
															login='$logineleve';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0){
				// A DEGAGER
				// A VOIR: Comment enregistrer une tentative d'accès illicite?
				//echo "$sql<br />";
				tentative_intrusion(1, "Tentative d'un ".$_SESSION["statut"]." (".$_SESSION["login"].") d'accéder aux données d'Années antérieures de $logineleve dont il n'est pas ".getSettingValue("gepi_prof_suivi").".");
				header("Location: ../logout.php?auto=1");
				die();
			}
		}
	}
}
elseif($_SESSION['statut']=="cpe"){
	// $AACpeTout
	// $AACpeResp

	$AACpeTout=getSettingValue('AACpeTout');
	$AACpeResp=getSettingValue('AACpeResp');

	if($AACpeTout=="yes"){
		// Le CPE a accès aux données antérieures de tous les élèves
		$acces="y";

		$sql_classes="SELECT DISTINCT id,classe FROM classes ORDER BY classe";

		if(isset($id_classe)){
			$sql_ele="SELECT DISTINCT e.nom,e.prenom,e.login FROM eleves e,j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND jec.login=e.login ORDER BY e.nom,e.prenom";
		}
	}
	elseif($AACpeResp=="yes"){
		$sql="SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='".$_SESSION['login']."'";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			$acces="y";

			$sql_classes="SELECT DISTINCT c.id,c.classe FROM classes c,
															j_eleves_cpe jec,
															j_eleves_classes jecl
							WHERE jec.cpe_login='".$_SESSION['login']."' AND
									jecl.login=jec.e_login AND
									jecl.id_classe=c.id
							ORDER BY c.classe;";

			if(isset($id_classe)){
				$sql_ele="SELECT DISTINCT e.nom,e.prenom,e.login FROM eleves e,
															j_eleves_cpe jec,
															j_eleves_classes jecl
									WHERE jecl.id_classe='$id_classe' AND
											jecl.login=e.login AND
											jec.e_login=e.login AND
											jec.cpe_login='".$_SESSION['login']."'
									ORDER BY e.nom,e.prenom";
			}

			// On vérifie qu'il n'y a pas tentative d'intrusion illicite:
			if(isset($logineleve)){
				$sql="SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='".$_SESSION['login']."' AND
															e_login='$logineleve'";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0){
					// A DEGAGER
					// A VOIR: Comment enregistrer une tentative d'accès illicite?
					tentative_intrusion(1, "Tentative d'un ".$_SESSION["statut"]." (".$_SESSION["login"].") d'accéder aux données d'Années antérieures de $logineleve dont il n'est pas responsable.");
					header("Location: ../logout.php?auto=1");
					die();
				}
			}
		}
	}
}
elseif($_SESSION['statut']=="scolarite"){
	// $AAScolTout
	// $AAScolResp

	$AAScolTout=getSettingValue('AAScolTout');
	$AAScolResp=getSettingValue('AAScolResp');

	if($AAScolTout=="yes"){
		// Les comptes Scolarité ont accès aux données antérieures de tous les élèves
		$acces="y";

		$sql_classes="SELECT DISTINCT id,classe FROM classes ORDER BY classe";

		if(isset($id_classe)){
			$sql_ele="SELECT DISTINCT e.nom,e.prenom,e.login FROM eleves e,j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND jec.login=e.login ORDER BY e.nom,e.prenom";
		}
	}
	elseif($AAScolResp=="yes"){
		$sql="SELECT 1=1 FROM j_scol_classes jsc
						WHERE jsc.login='".$_SESSION['login']."';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			$acces="y";

			$sql_classes="SELECT DISTINCT c.id,c.classe FROM classes c,
															j_scol_classes jsc
							WHERE jsc.login='".$_SESSION['login']."' AND
									jsc.id_classe=c.id
							ORDER BY c.classe;";

			if(isset($id_classe)){
				$sql_ele="SELECT DISTINCT e.nom,e.prenom,e.login FROM eleves e,
															j_scol_classes jsc,
															j_eleves_classes jec
									WHERE jec.id_classe='$id_classe' AND
											jec.login=e.login AND
											jec.id_classe=jsc.id_classe AND
											jsc.login='".$_SESSION['login']."'
									ORDER BY e.nom,e.prenom";
			}

			// On vérifie qu'il n'y a pas tentative d'intrusion illicite:
			if(isset($logineleve)){
				$sql="SELECT 1=1 FROM j_eleves_classes jec, j_scol_classes jsc
								WHERE jec.login='$logineleve' AND
										jec.id_classe=jsc.id_classe AND
										jsc.login='".$_SESSION['login']."';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0){
					// A DEGAGER
					// A VOIR: Comment enregistrer une tentative d'accès illicite?
					tentative_intrusion(1, "Tentative d'un ".$_SESSION["statut"]." (".$_SESSION["login"].") d'accéder aux données d'Années antérieures de $logineleve qui n'est pas élève d'une des classes dont le CPE est responsable.");
					header("Location: ../logout.php?auto=1");
					die();
				}
			}
		}
	}
}
elseif($_SESSION['statut']=="responsable"){
	$AAResponsable=getSettingValue('AAResponsable');

	if($AAResponsable=="yes"){
		// Est-ce que le responsable est bien associé à un élève?
		$sql="SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e WHERE rp.pers_id=r.pers_id AND
																			r.ele_id=e.ele_id AND
																			rp.login='".$_SESSION['login']."'";
		$test=mysql_query($sql);
		//echo "mysql_num_rows(\$test)=".mysql_num_rows($test)."<br />\n";
		if(mysql_num_rows($test)>0){
			$acces="y";

			$tab_eleves_resp=array();
			if($_SESSION['statut']=='responsable') {
				$tmp_tab_eleves_resp=get_enfants_from_resp_login($_SESSION['login']);
				// On récupère un tableau avec login pour le premier indice et nom_prenom pour le 2è, puis on passe à l'élève suivant.
				if(count($tmp_tab_eleves_resp)==2) {
					$tab_eleves_resp[0]=array();
					$tab_eleves_resp[0]['login']=$tmp_tab_eleves_resp[0];
					$tab_eleves_resp[0]['nom_prenom']=$tmp_tab_eleves_resp[1];
					$tab_class_ele=get_class_from_ele_login($tab_eleves_resp[0]['login']);
					if(count($tab_class_ele)>0) {
						$tab_eleves_resp[0]['id_classe']=$tab_class_ele['id0'];
					}
					else {
						$tab_eleves_resp[0]['id_classe']=0;
					}
				}
				elseif(count($tmp_tab_eleves_resp)>2) {
					$cpt=0;
					for($loop=0;$loop<count($tmp_tab_eleves_resp);$loop+=2) {
						$tab_eleves_resp[$cpt]=array();
						$tab_eleves_resp[$cpt]['login']=$tmp_tab_eleves_resp[$loop];
						$tab_eleves_resp[$cpt]['nom_prenom']=$tmp_tab_eleves_resp[$loop+1];
						$tab_class_ele=get_class_from_ele_login($tab_eleves_resp[$cpt]['login']);
						if(count($tab_class_ele)>0) {
							$tab_eleves_resp[$cpt]['id_classe']=$tab_class_ele['id0'];
						}
						else {
							$tab_eleves_resp[$cpt]['id_classe']=0;
						}
						$cpt++;
					}
				}
			}


			if(!isset($id_classe)) {
				if(count($tab_eleves_resp)==1) {
					$logineleve=$tab_eleves_resp[0]['login'];
					if(isset($tab_eleves_resp[0]['id_classe'])) {$id_classe=$tab_eleves_resp[0]['id_classe'];}
					//$aff_classe="y";
				}
				/*
				$sql_classes="SELECT DISTINCT c.id,c.classe FROM classes c,
																j_eleves_classes jec,
																eleves e,
																responsables2 r,
																resp_pers rp
								WHERE rp.login='".$_SESSION['login']."' AND
										rp.pers_id=r.pers_id AND
										(r.resp_legal='1' OR r.resp_legal='2') AND
										r.ele_id=e.ele_id AND
										e.login=jec.login AND
										jec.id_classe=c.id
								ORDER BY c.classe;";
				$res_classe=mysql_query($sql_classes);
				if(mysql_num_rows($res_classe)==1){
					$lig_classe=mysql_fetch_object($res_classe);
					$id_classe=$lig_classe->id;
				}
				*/
			}

			if(isset($id_classe)){
				$sql_ele="SELECT DISTINCT e.nom,e.prenom,e.login FROM eleves e,
																j_eleves_classes jec,
																responsables2 r,
																resp_pers rp
									WHERE jec.id_classe='$id_classe' AND
											jec.login=e.login AND
											rp.login='".$_SESSION['login']."' AND
											rp.pers_id=r.pers_id AND
											(r.resp_legal='1' OR r.resp_legal='2') AND
											r.ele_id=e.ele_id
									ORDER BY e.nom,e.prenom;";
			}

			if(isset($logineleve)){
				$sql="SELECT 1=1 FROM resp_pers rp,
										responsables2 r,
										eleves e
								WHERE rp.login='".$_SESSION['login']."' AND
										rp.pers_id=r.pers_id AND
										r.ele_id=e.ele_id AND
										(r.resp_legal='1' OR r.resp_legal='2') AND
										e.login='$logineleve'";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0){
					// A DEGAGER
					// A VOIR: Comment enregistrer une tentative d'accès illicite?
					tentative_intrusion(1, "Tentative d'un ".$_SESSION["statut"]." (".$_SESSION["login"].") d'accéder aux données d'Années antérieures de $logineleve dont il n'est pas responsable.");
					header("Location: ../logout.php?auto=1");
					die();
				}
			}

		}
	}
}
elseif($_SESSION['statut']=="eleve"){
	$AAEleve=getSettingValue('AAEleve');

	if($AAEleve=="yes"){
		$logineleve=$_SESSION['login'];
		$acces="y";

		$sql_classes="SELECT DISTINCT c.id,c.classe FROM classes c,
														j_eleves_classes jec
						WHERE jec.login='".$_SESSION['login']."' AND
								jec.id_classe=c.id
						ORDER BY c.classe DESC;";
		$res_classe=mysql_query($sql_classes);
		if(mysql_num_rows($res_classe)>0){
			$lig_classe=mysql_fetch_object($res_classe);
			$id_classe=$lig_classe->id;
		}
	}
}
elseif($_SESSION['statut']=="autre") {
	$sql="SELECT 1=1 FROM droits_speciaux ds WHERE ds.id_statut='".$_SESSION['statut_special_id']."' AND ds.nom_fichier='/mod_annees_anterieures/consultation_annee_anterieure.php' AND ds.autorisation='V';";
	$res_acces=mysql_query($sql);

	if(mysql_num_rows($res_acces)>0){
		$acces="y";

		$sql_classes="SELECT DISTINCT id,classe FROM classes ORDER BY classe";
	
		if(isset($id_classe)){
			$sql_ele="SELECT DISTINCT e.nom,e.prenom,e.login FROM eleves e,j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND jec.login=e.login ORDER BY e.nom,e.prenom";
		}

	}
}

if($acces!="y"){
	// A DEGAGER
	// A VOIR: Comment enregistrer une tentative d'accès illicite?

	tentative_intrusion(1, "Tentative illicite d'un ".$_SESSION["statut"]." (".$_SESSION["login"].") d'accéder à des données d'Années antérieures.");

	header("Location: ../logout.php?auto=1");
	die();
}




$msg="";

$style_specifique="mod_annees_anterieures/annees_anterieures";

//**************** EN-TETE *****************
$titre_page = "Consultation des données antérieures";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<div class='norme'>\n";
echo "<form action='".$_SERVER['PHP_SELF']."' id='form_change_eleve' method='get'>\n";
echo "<p class='bold'><a href='";
if($_SESSION['statut']=="administrateur"){
	echo "index.php";
}
else{
	echo "../accueil.php";
}
echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

//==================================================================
if(($_SESSION['statut']=='administrateur')&&(isset($_GET['ine']))) {
	require("fonctions_annees_anterieures.inc.php");

	echo " | <a href='nettoyer_annee_anterieure.php'>Nettoyage</a>";
	if(($mode!='bull_simp')&&($mode!='avis_conseil')) {
		echo "</p>\n";
		echo "</div>\n";

		echo "<h2 style='text-align: center;'>Choix des informations antérieures</h2>\n";
		tab_choix_anterieure('','',$_GET['ine']);
	}
	else {
		echo " | <a href='".$_SERVER['PHP_SELF']."?ine=".$_GET['ine']."'>Choix des informations</a>\n";
		echo "</p>\n";
		echo "</div>\n";

			echo "<h2 style='text-align: center;'>Bulletin simplifié d'une année antérieure</h2>\n";
			if(!isset($annee_scolaire)){
				echo "<p><strong>ERREUR:</strong> L'année scolaire antérieure ne semble pas avoir été choisie.</p>\n";
			}
			elseif(!isset($num_periode)){
				echo "<p><strong>ERREUR:</strong> La période ne semble pas avoir été choisie.</p>\n";
			}
			else{
				bull_simp_annee_anterieure('', '', $annee_scolaire, $num_periode, $_GET['ine']);
			}
	}

	require("../lib/footer.inc.php");
	die();
}
//==================================================================
/*
echo "<pre>";
print_r($tab_eleves_resp);
echo "</pre>";
*/
//==================================================================
if((!isset($id_classe))&&($_SESSION['statut']=='responsable')) {
	if(count($tab_eleves_resp)==1) {
		// Normalement, ce cas est géré plus haut
		$logineleve=$tab_eleves_resp[0]['login'];
		if(isset($tab_eleves_resp[0]['id_classe'])) {$id_classe=$tab_eleves_resp[0]['id_classe'];}
		$aff_classe="y";
	}
	else {
		// Il faut choisir l'élève
		echo "<p>Choisissez l'".$gepiSettings['denomination_eleve']." pour lequel vous souhaitez consulter les données d'années antérieures.</p>\n";
		echo "<ul>\n";
		for($loop=0;$loop<count($tab_eleves_resp);$loop++) {
			echo "<li><a href='".$_SERVER['PHP_SELF']."?logineleve=".$tab_eleves_resp[$loop]['login']."&amp;id_classe=".$tab_eleves_resp[$loop]['id_classe']."'>".$tab_eleves_resp[$loop]['nom_prenom']."</a></li>\n";
		}
		echo "</ul>\n";
		require("../lib/footer.inc.php");
		die();
	}
}

//==================================================================

if(!isset($id_classe)){
	echo "</p></form>\n";
	echo "</div>\n";

	echo "<h2>Choix de la classe</h2>\n";

	echo "<p>Choisissez la classe dans laquelle se trouve actuellement un ".$gepiSettings['denomination_eleve']." pour lequel vous souhaitez consulter les données d'années antérieures.</p>";

	if(!isset($sql_classes)){
		echo "<p>ERREUR: Il semble que la requête de choix de la classe n'ait pas été initialisée.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$res1=mysql_query($sql_classes);
	$nb_classes=mysql_num_rows($res1);
	if($nb_classes==0){
		echo "<p>ERREUR: Il semble qu'aucune classe ne soit encore définie.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	// Affichage sur 3 colonnes
	$nb_classes_par_colonne=round($nb_classes/3);

	echo "<table width='100%' summary='Choix de la classe'>\n";
	echo "<tr valign='top' style='text-align: center;'>\n";

	$i = 0;

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	while ($i < $nb_classes) {

		if(($i>0)&&(round($i/$nb_classes_par_colonne)==$i/$nb_classes_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		$lig_classe=mysql_fetch_object($res1);

		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$lig_classe->id'>$lig_classe->classe</a><br />\n";

		$i++;
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}
else {
	if($_SESSION['statut']=='responsable') {
		if(count($tab_eleves_resp)>1) {
			echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre ".$gepiSettings['denomination_eleve']."</a>";
		}
	}
	elseif($_SESSION['statut']!='eleve') {
		echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>";
	}

	if((!isset($logineleve))&&(!isset($aff_classe))) {
		echo "</p></form>\n";
		echo "</div>\n";

		if(!isset($sql_ele)){
			echo "<p>ERREUR: Il semble que la requête de choix de l'".$gepiSettings['denomination_eleve']." n'ait pas été initialisée.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		//echo "$sql_ele<br />\n";
		$res_ele=mysql_query($sql_ele);

		if(mysql_num_rows($res_ele)==0){
			echo "<p>ERREUR: Il semble qu'l n'y ait aucun ".$gepiSettings['denomination_eleve']." dans cette classe.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		else{
			echo "<p>Choisissez un ".$gepiSettings['denomination_eleve']." pour lequel vous souhaitez consulter les informations antérieures.</p>\n";

			$nb_eleves=mysql_num_rows($res_ele);

			// Affichage sur 3 colonnes
			$nb_par_colonne=round($nb_eleves/3);

			echo "<table width='100%' summary=\"Choix de l'élève\">\n";
			echo "<tr valign='top' style='text-align: center;'>\n";

			$i = 0;

			echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
			echo "<td align='left'>\n";

			while ($i < $nb_eleves) {

				if(($i>0)&&(round($i/$nb_par_colonne)==$i/$nb_par_colonne)){
					echo "</td>\n";
					echo "<td align='left'>\n";
				}

				$lig_ele=mysql_fetch_object($res_ele);

				echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;logineleve=$lig_ele->login'>$lig_ele->nom $lig_ele->prenom</a><br />\n";

				$i++;
			}
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			flush();

		}


		if(($_SESSION['statut']=='administrateur')||
		($_SESSION['statut']=='scolarite')||
		($_SESSION['statut']=='cpe')||
		($_SESSION['statut']=='professeur')) {

			require("fonctions_annees_anterieures.inc.php");

			echo "<p>Ou afficher les informations pour toute la classe sur la période choisie:</p>\n";
			echo "<blockquote>\n";

			$sql="SELECT DISTINCT ad.annee FROM archivage_disciplines ad, eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe' AND ad.INE=e.no_gep ORDER BY annee ASC;";
			//echo "$sql<br />\n";
			$res_ant=mysql_query($sql);
			if(mysql_num_rows($res_ant)==0){
				echo "<p>Aucun résultat antérieur n'a été conservé pour cette classe.</p>\n";
			}
			else{

				unset($tab_annees);

				$nb_annees=mysql_num_rows($res_ant);

				$alt=1;
				echo "<table class='boireaus table_annee_anterieure' summary='Bulletins'>\n";
				echo "<tr class='lig$alt'>\n";
				echo "<th rowspan='".$nb_annees."' valign='top'>Bulletins simplifiés:</th>";
				$cpt=0;
				while($lig_ant=mysql_fetch_object($res_ant)){

					$tab_annees[]=$lig_ant->annee;

					if($cpt>0){
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
					}
					echo "<td style='font-weight:bold;'>$lig_ant->annee : </td>\n";

					//$sql="SELECT DISTINCT num_periode,nom_periode FROM archivage_disciplines WHERE annee='$lig_ant->annee' ORDER BY num_periode ASC";
					$sql="SELECT DISTINCT num_periode FROM archivage_disciplines WHERE annee='$lig_ant->annee' ORDER BY num_periode ASC";
					$res_ant2=mysql_query($sql);

					if(mysql_num_rows($res_ant2)==0){
						echo "<td>Aucun résultat antérieur n'a été conservé pour cet ".$gepiSettings['denomination_eleve'].".</td>\n";
					}
					else{
						$cpt=0;
						while($lig_ant2=mysql_fetch_object($res_ant2)){
							//echo "<td style='text-align:center;'><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;aff_classe=y&amp;annee_scolaire=$lig_ant->annee&amp;num_periode=$lig_ant2->num_periode&amp;mode=bull_simp'>$lig_ant2->nom_periode</a></td>\n";
							echo "<td style='text-align:center;'><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;aff_classe=y&amp;annee_scolaire=$lig_ant->annee&amp;num_periode=$lig_ant2->num_periode&amp;mode=bull_simp'>Période $lig_ant2->num_periode</a></td>\n";
							$cpt++;
						}
					}
					echo "</tr>\n";
					flush();
					$cpt++;
				}
				echo "</table>\n";

				echo "<p><br /></p>";

				$alt=1;
				echo "<table class='boireaus table_annee_anterieure' summary='Avis des conseils'>\n";
				echo "<tr class='lig$alt'>\n";
				echo "<th rowspan='".$nb_annees."' valign='top'>Avis des conseils de classes:</th>";
				$cpt=0;
				for($i=0;$i<count($tab_annees);$i++){
					if($cpt>0){
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
					}
					echo "<td>\n";

					echo "Année-scolaire <a href='".$_SERVER['PHP_SELF']."?aff_classe=y&amp;annee_scolaire=".$tab_annees[$i]."&amp;mode=avis_conseil";
					if(isset($id_classe)){echo "&amp;id_classe=$id_classe";}
					echo "'>$tab_annees[$i]</a>";

					echo "</td>\n";
					echo "</tr>\n";
					flush();
					$cpt++;
				}
				echo "</table>\n";

			}
			echo "</blockquote>\n";
		}


	}
	elseif((isset($aff_classe))&&(isset($sql_ele))&&(
		($_SESSION['statut']=='administrateur')||
		($_SESSION['statut']=='scolarite')||
		($_SESSION['statut']=='cpe')||
		($_SESSION['statut']=='professeur')
	)) {
			echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Choisir une autre période ou ".$gepiSettings['denomination_eleve']."</a></p>\n";
			echo "</form>\n";
			echo "</div>\n";

		$res_liste_ele=mysql_query($sql_ele);
		if(mysql_num_rows($res_liste_ele)==0) {
			echo "<p>Aucun ".$gepiSettings['denomination_eleve']." n'a semble-t-il été trouvé.</p>\n";
		}
		else {
			require("fonctions_annees_anterieures.inc.php");

			while($lig_ele=mysql_fetch_object($res_liste_ele)) {
				bull_simp_annee_anterieure($lig_ele->login,$id_classe,$annee_scolaire,$num_periode);
			}

		}


	}
	else{
		if(($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
			echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Choisir un autre ".$gepiSettings['denomination_eleve']."</a>\n";

			if(isset($sql_ele)) {
				$lignes_options_select_eleve=lignes_options_select_eleve($id_classe,$logineleve,$sql_ele);
			}
			else {
				$lignes_options_select_eleve=lignes_options_select_eleve($id_classe,$logineleve);
			}

			echo "<select name='logineleve' onchange=\"document.forms['form_change_eleve'].submit();\">\n";
			echo $lignes_options_select_eleve;
			echo "</select>\n";
			echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";

			if(isset($annee_scolaire)) {
				echo "<input type='hidden' name='annee_scolaire' value='$annee_scolaire' />\n";
			}
			if(isset($num_periode)) {
				echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";
			}
			if(isset($mode)) {
				echo "<input type='hidden' name='mode' value='$mode' />\n";
			}
		}

		require("fonctions_annees_anterieures.inc.php");

		if(my_eregi("gecko",$_SERVER['HTTP_USER_AGENT'])){
			//echo "gecko=true<br />";
			$gecko=true;
		}
		else{
			$gecko=false;
		}

		if((!isset($logineleve))||(($mode!='bull_simp')&&($mode!='avis_conseil'))) {
			echo "</p></form>\n";
			echo "</div>\n";
			echo "<h2 style='text-align: center;'>Choix des informations antérieures</h2>\n";
			tab_choix_anterieure($logineleve,$id_classe);
		}
		else{
			echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;logineleve=$logineleve'>Choix des informations</a>\n";
			echo "</p></form>\n";
			echo "</div>\n";
			if($mode=='bull_simp'){
				echo "<h2 style='text-align: center;'>Bulletin simplifié d'une année antérieure</h2>\n";
				if(!isset($annee_scolaire)){
					echo "<p><strong>ERREUR:</strong> L'année scolaire antérieure ne semble pas avoir été choisie.</p>\n";
				}
				elseif(!isset($num_periode)){
					echo "<p><strong>ERREUR:</strong> La période ne semble pas avoir été choisie.</p>\n";
				}
				elseif(!isset($id_classe)){
					echo "<p><strong>ERREUR:</strong> L'identifiant de la classe actuelle de cet ".$gepiSettings['denomination_eleve']." ne semble pas avoir été fourni.</p>\n";
				}
				else{
					bull_simp_annee_anterieure($logineleve, $id_classe, $annee_scolaire, $num_periode);
				}
			}
			elseif($mode=='avis_conseil'){
				echo "<h2 style='text-align: center;'>Avis des Conseils de classe d'une année antérieure</h2>\n";
				if(!isset($annee_scolaire)){
					echo "<p><strong>ERREUR:</strong> L'année scolaire antérieure ne semble pas avoir été choisie.</p>\n";
				}
				else{
					avis_conseils_de_classes_annee_anterieure($logineleve,$annee_scolaire);
				}
			}
		}

	}
}

require("../lib/footer.inc.php");
?>
