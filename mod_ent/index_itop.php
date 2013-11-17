<?php
/*
 *
 * Copyright 2001-2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_ent/index_itop.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_ent/index_itop.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Rapprochement des comptes ENT/GEPI : ENT ITOP',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$msg="";
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

function formate_date2($chaine, $from, $to) {
	$retour=$chaine;

	if($from=="jj/mm/aaaa") {
		$tab=explode("/", $chaine);

		if(isset($tab[2])) {
			if($to=="aaaammjj") {
				if($tab[2]>100) {
					$retour=$tab[2].sprintf($tab[1], "%1$02d").sprintf($tab[0], "%1$02d");
				}
			}
		}
	}

	return $retour;
}

function echo_debug_itop($chaine) {
	$debug="n";
	if($debug=="y") {
		echo $chaine;
	}
}

// Menage:
$sql="DELETE FROM sso_table_correspondance WHERE login_gepi='' OR login_sso='';";
$menage=mysqli_query($GLOBALS["mysqli"], $sql);

if((isset($_POST['temoin_suhosin_1']))&&(!isset($_POST['temoin_suhosin_2']))) {
	$msg.="Il semble que certaines variables n'ont pas été transmises.<br />Cela peut arriver lorsqu'on tente de transmettre (<em>cocher trop de cases</em>) trop de variables.<br />Vous devriez tenter de cocher moins de cases et vous y prendre en plusieurs fois.<br />";
}

if(isset($_GET['supprimer_comptes_parents'])) {
	check_token();

	$sql="DELETE FROM utilisateurs WHERE statut='responsable';";
	$suppr=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="UPDATE resp_pers SET login='';";
	$vider_login=mysqli_query($GLOBALS["mysqli"], $sql);

	$msg.="Les comptes d'utilisateurs responsables ont été supprimés et leur login vidé dans la table 'resp_pers'.<br />";
}

if(isset($_POST['recherche'])) {
	check_token();

	$nom_rech=isset($_POST['nom_rech']) ? $_POST['nom_rech'] : "";
	$prenom_rech=isset($_POST['prenom_rech']) ? $_POST['prenom_rech'] : "";

	$nom_rech=preg_replace("/[^A-Za-z\-]/","%",$nom_rech);
	$prenom_rech=preg_replace("/[^A-Za-z\-]/","%",$prenom_rech);

	if(($nom_rech!="")||($prenom_rech!="")) {

		if($_POST['recherche']=='recherche_eleve') {

			$sql="SELECT login,nom,prenom,naissance FROM eleves WHERE nom LIKE '%".$nom_rech."%' AND prenom LIKE '%".$prenom_rech."%';";

			$res=@mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($res)==0){
				$chaine="Aucun résultat retourné.";
			}
			else {
				$chaine="<table class='boireaus'>";
				$chaine.="<tr style='background-color: white;'>";

				$chaine.="<th>";
				$chaine.="Nom";
				$chaine.="</th>";

				$chaine.="<th>";
				$chaine.="Prenom";
				$chaine.="</th>";

				$chaine.="<th>";
				$chaine.="Naissance";
				$chaine.="</th>";

				$chaine.="<th>";
				$chaine.="Classe";
				$chaine.="</th>";

				$chaine.="<th>";
				$chaine.="Login";
				$chaine.="</th>";

				$chaine.="</tr>";

				$alt=-1;
				while($lig=mysqli_fetch_object($res)){
					//$chaine.="<tr>";

					$alt=$alt*(-1);
					$chaine.="<tr style='background-color:";
					if($alt==1){
						$chaine.="silver";
					}
					else{
						$chaine.="white";
					}
					$chaine.="; text-align: center;'>";

					$chaine.="<td>";
					$chaine.="$lig->nom";
					$chaine.="</td>";

					$chaine.="<td>";
					$chaine.="$lig->prenom";
					$chaine.="</td>";

					$chaine.="<td>";
					$chaine.=formate_date($lig->naissance);
					$chaine.="</td>";

					$chaine.="<td>";
					if($lig->login!=""){
						$tmp_tab_class=get_class_from_ele_login($lig->login);
						if(isset($tmp_tab_class['liste_nbsp'])) {
							$chaine.=$tmp_tab_class['liste_nbsp'];
						}
					}
					$chaine.="</td>";

					$chaine.="<td>";
					if($lig->login!=""){
						$chaine.="<a href='#' onClick=\"document.getElementById(document.getElementById('login_recherche').value).value='$lig->login';cacher_div('div_search');return false;\">$lig->login</a>";
					}
					else{
						$chaine.="<span style='color:red'>Non renseigné</span>";
					}
					$chaine.="</td>";

					$chaine.="</tr>";
				}
				$chaine.="</table>";
				//$chaine.="$sql";
			}
			echo $chaine;
		}
		elseif($_POST['recherche']=='recherche_responsable') {

			$sql="SELECT login,nom,prenom,pers_id FROM resp_pers WHERE nom LIKE '%".$nom_rech."%' AND prenom LIKE '%".$prenom_rech."%';";

			$res=@mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($res)==0){
				$chaine="Aucun résultat retourné.";
			}
			else {
				$chaine="<table class='boireaus'>";
				$chaine.="<tr style='background-color: white;'>";

				$chaine.="<th>";
				$chaine.="Nom";
				$chaine.="</th>";

				$chaine.="<th>";
				$chaine.="Prenom";
				$chaine.="</th>";

				$chaine.="<th>";
				$chaine.="Resp.de";
				$chaine.="</th>";

				$chaine.="<th>";
				$chaine.="Login";
				$chaine.="</th>";

				$chaine.="</tr>";

				$alt=-1;
				while($lig=mysqli_fetch_object($res)){
					//$chaine.="<tr>";

					$alt=$alt*(-1);
					$chaine.="<tr style='background-color:";
					if($alt==1){
						$chaine.="silver";
					}
					else{
						$chaine.="white";
					}
					$chaine.="; text-align: center;'>";

					$chaine.="<td>";
					$chaine.="$lig->nom";
					$chaine.="</td>";

					$chaine.="<td>";
					$chaine.="$lig->prenom";
					$chaine.="</td>";

					$chaine.="<td style='font-size:x-small'>";
					$tmp_tab_ele=get_enfants_from_pers_id($lig->pers_id,'avec_classe');
					$cpt_ele=0;
					for($loop=0;$loop<count($tmp_tab_ele);$loop++) {
						if($cpt_ele>0) {
							$chaine.=", ";
						}
						$chaine.=$tmp_tab_ele[$loop];
					}
					$chaine.="</td>";

					$chaine.="<td>";
					if($lig->login!=""){
						$chaine.="<a href='#' onClick=\"document.getElementById(document.getElementById('login_recherche').value).value='$lig->login';cacher_div('div_search');return false;\">$lig->login</a>";
					}
					else{
						$chaine.="<span style='color:red'>Non renseigné</span>";
					}
					$chaine.="</td>";

					$chaine.="</tr>";
				}
				$chaine.="</table>";
				//$chaine.="$sql";
			}
			echo $chaine;
		}
		elseif($_POST['recherche']=='recherche_personnel') {

			$sql="SELECT login,nom,prenom,statut FROM utilisateurs WHERE nom LIKE '%".$nom_rech."%' AND prenom LIKE '%".$prenom_rech."%';";

			$res=@mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($res)==0){
				$chaine="Aucun résultat retourné.";
			}
			else {
				$chaine="<table class='boireaus'>";
				$chaine.="<tr style='background-color: white;'>";

				$chaine.="<th>";
				$chaine.="Nom";
				$chaine.="</th>";

				$chaine.="<th>";
				$chaine.="Prenom";
				$chaine.="</th>";

				$chaine.="<th>";
				$chaine.="Statut";
				$chaine.="</th>";

				$chaine.="<th>";
				$chaine.="Login";
				$chaine.="</th>";

				$chaine.="</tr>";

				$alt=-1;
				while($lig=mysqli_fetch_object($res)){
					//$chaine.="<tr>";

					$alt=$alt*(-1);
					$chaine.="<tr style='background-color:";
					if($alt==1){
						$chaine.="silver";
					}
					else{
						$chaine.="white";
					}
					$chaine.="; text-align: center;'>";

					$chaine.="<td>";
					$chaine.="$lig->nom";
					$chaine.="</td>";

					$chaine.="<td>";
					$chaine.="$lig->prenom";
					$chaine.="</td>";

					$chaine.="<td style='font-size:x-small'>";
					$chaine.=$lig->statut;
					$chaine.="</td>";

					$chaine.="<td>";
					if($lig->login!=""){
						$chaine.="<a href='#' onClick=\"document.getElementById(document.getElementById('login_recherche').value).value='$lig->login';cacher_div('div_search');return false;\">$lig->login</a>";
					}
					else{
						$chaine.="<span style='color:red'>Non renseigné</span>";
					}
					$chaine.="</td>";

					$chaine.="</tr>";
				}
				$chaine.="</table>";
				//$chaine.="$sql";
			}
			echo $chaine;
		}
	}
	die();
}

if(isset($_POST['enregistrement_eleves'])) {
	check_token();

	$ligne=isset($_POST['ligne']) ? $_POST['ligne'] : NULL;

	$nb_reg=0;
	$nb_pas_dans_eleves=0;

	if(!isset($ligne)) {
		$msg="Aucun enregistrement d'association n'a été demandé.<br />";
	}
	else {
		$ligne_tempo2=array();
		$sql="SELECT * FROM tempo2_sso;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig=mysqli_fetch_object($res)) {
			$ligne_tempo2[$lig->col1]=$lig->col2;
		}

		for($loop=0;$loop<count($ligne);$loop++) {
			// On a eu un choix de login
			if((isset($_POST['login_'.$ligne[$loop]]))&&($_POST['login_'.$ligne[$loop]]!='')) {
				if(isset($ligne_tempo2[$ligne[$loop]])) {
					$tab=explode(";", $ligne_tempo2[$ligne[$loop]]);

					$sql="SELECT login FROM eleves WHERE login='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $_POST['login_'.$ligne[$loop]]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)==0) {
						$msg.="Le login élève Gepi ".$_POST['login_'.$ligne[$loop]]." proposé n'existe pas.<br />\n";
					}
					else {
						$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test)==0) {
							$sql="SELECT * FROM sso_table_correspondance WHERE login_gepi='".$_POST['login_'.$ligne[$loop]]."';";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test)==0) {
								$sql="INSERT INTO sso_table_correspondance SET login_gepi='".$_POST['login_'.$ligne[$loop]]."', login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$insert) {
									$msg.="Erreur lors de l'insertion de l'association ".$tab[0]." &gt; ".$_POST['login_'.$ligne[$loop]]."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
							else {
								$sql="UPDATE sso_table_correspondance SET login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' WHERE login_gepi='".$_POST['login_'.$ligne[$loop]]."';";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$update) {
									$msg.="Erreur lors de la mise à jour de l'association ".$tab[0]." &gt; ".$_POST['login_'.$ligne[$loop]]."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
						}
						else {
							$lig=mysqli_fetch_object($test);
							$msg.="Le GUID $tab[0] est déjà associé à $lig->login_gepi<br />\n";
						}
					}
				}
				else {
					$msg.="Aucun enregistrement pour la ligne $loop<br />";
				}
			}
			else {
				if(isset($ligne_tempo2[$ligne[$loop]])) {
					$tab=explode(";", $ligne_tempo2[$ligne[$loop]]);

					$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
					echo_debug_itop("$sql<br />");
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)==0) {
						$naissance=(isset($tab[5])) ? $tab[5] : "";
						if(!preg_match("#[0-9]{2}/[0-9]{2}/[0-9]{4}#", $naissance)) {$naissance="";}

						/*
						if($naissance!="") {
							$sql="SELECT * FROM eleves WHERE nom='".mysql_real_escape_string($tab[1])."' AND prenom='".mysql_real_escape_string($tab[2])."' AND naissance='".formate_date2($naissance, "jj/mm/aaaa", "aaaammjj")."'";
						}
						else {
							$sql="SELECT * FROM eleves WHERE nom='".mysql_real_escape_string($tab[1])."' AND prenom='".mysql_real_escape_string($tab[2])."' ORDER BY naissance;";
						}
						*/
						// Les accents ne sont pas correctement interprétés
						$nom_remplacement=preg_replace("/[^A-Za-z]/","%",$tab[1]);
						$prenom_remplacement=preg_replace("/[^A-Za-z]/","%",$tab[2]);
						if($naissance!="") {
							$sql="SELECT * FROM eleves WHERE nom LIKE '".$nom_remplacement."' AND prenom LIKE '".$prenom_remplacement."' AND naissance='".formate_date2($naissance, "jj/mm/aaaa", "aaaammjj")."'";
						}
						else {
							$sql="SELECT * FROM eleves WHERE nom LIKE '".$nom_remplacement."' AND prenom LIKE '".$prenom_remplacement."' ORDER BY naissance;";
						}
						echo_debug_itop("$sql<br />");
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)==0) {
							$msg.="Aucun enregistrement dans la table 'eleves' pour ".$tab[1]." ".$tab[2]." !<br />\n";
							$nb_pas_dans_eleves++;
						}
						elseif(mysqli_num_rows($res)==1) {
							// Un seul élève correspond
							$lig=mysqli_fetch_object($res);

							$sql="SELECT 1=1 FROM sso_table_correspondance WHERE login_gepi='$lig->login';";
							echo_debug_itop("$sql<br />");
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test)==0) {
								$sql="INSERT INTO sso_table_correspondance SET login_gepi='$lig->login', login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
								echo_debug_itop("$sql<br />");
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$insert) {
									$msg.="Erreur lors de l'insertion de l'association ".$tab[0]." &gt; ".$lig->login."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
							else {
								$sql="UPDATE sso_table_correspondance SET login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' WHERE login_gepi='$lig->login';";
								echo_debug_itop("$sql<br />");
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$update) {
									$msg.="Erreur lors de la mise à jour de l'association ".$tab[0]." &gt; ".$lig->login."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
						}
						else {
							// On ne doit pas arriver là
							$msg.="Plusieurs enregistrements dans la table 'eleves' pour ".$tab[1]." ".$tab[2]." !<br />\n";
						}
					}
					else {
						$lig=mysqli_fetch_object($test);
						$msg.="Le GUID $tab[0] est déjà associé à $lig->login_gepi<br />\n";
					}
				}
				else {
					$msg.="Aucun enregistrement pour la ligne $loop<br />";
				}
			}
		}
	}

	if($nb_pas_dans_eleves>0) {
		$msg.="<br />$nb_pas_dans_eleves comptes de l'ENT n'ont pas été trouvés dans la table 'eleves' de Gepi.<br />Sont-ce des élèves de l'année précédente ?<br />\n";
	}

	if($nb_reg>0) {
		$msg.="<br />$nb_reg enregistrement(s) effectué(s).<br />\n";
	}

	$mode="consult_eleves";
}


if(isset($_POST['enregistrement_responsables'])) {
	check_token();

	$ligne=isset($_POST['ligne']) ? $_POST['ligne'] : NULL;

	$nb_reg=0;

	if(!isset($ligne)) {
		$msg="Aucun enregistrement d'association n'a été demandée.<br />";
	}
	else {
		$sql="SELECT col2 FROM tempo2_sso WHERE col1='Ligne_entete';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$msg="La ligne d'entête du CSV n'a pas été trouvée.<br />";
		}
		else {

			// Lire la ligne d'entête pour repérer les indices des colonnes recherchées
			$tabchamps = array("Guid", "Nom", "Prénom", "Prenom", "Profil", "Groupe", "Guid_Enfant1", "Guid_Enfant2", "Guid_Enfant3");

			$ligne_entete=mysqli_fetch_object($res);
			$en_tete=explode(";", trim($ligne_entete->col2));

			$tabindice=array();

			// On range dans tabindice les indices des champs retenus
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					if (casse_mot(remplace_accents($en_tete[$i]),'min') == casse_mot(remplace_accents($tabchamps[$k]), 'min')) {
						$tabindice[$tabchamps[$k]] = $i;
					}
				}
			}

			if((!isset($tabindice['Nom']))||((!isset($tabindice['Prénom']))&&(!isset($tabindice['Prenom'])))||(!isset($tabindice['Guid']))||(!isset($tabindice['Guid_Enfant1']))||(!isset($tabindice['Profil']))) {
				$msg="La ligne d'entête ne comporte pas un des champs indispensables (<em>Guid, Nom, Prénom, Profil, Guid_enfant1</em>).<br />";
			}
			else {
				$ligne_tempo2=array();
				$sql="SELECT * FROM tempo2_sso;";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig=mysqli_fetch_object($res)) {
					$ligne_tempo2[$lig->col1]=$lig->col2;
				}

				for($loop=0;$loop<count($ligne);$loop++) {
					// On a eu un choix de login
					if((isset($_POST['login_'.$ligne[$loop]]))&&($_POST['login_'.$ligne[$loop]]!='')) {
						if(isset($ligne_tempo2[$ligne[$loop]])) {
							$tab=explode(";", $ligne_tempo2[$ligne[$loop]]);

							$guid_courant=$tab[$tabindice['Guid']];
							$nom_courant=$tab[$tabindice['Nom']];
							$prenom_courant=$tab[$tabindice['Prénom']];

							$sql="SELECT login FROM resp_pers WHERE login='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $_POST['login_'.$ligne[$loop]]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test)==0) {
								$msg.="Le login responsable Gepi ".$_POST['login_'.$ligne[$loop]]." proposé n'existe pas.<br />\n";
							}
							else {
								$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $guid_courant) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
								$test=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test)==0) {
									$sql="SELECT * FROM sso_table_correspondance WHERE login_gepi='".$_POST['login_'.$ligne[$loop]]."';";
									$test=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($test)==0) {
										$sql="INSERT INTO sso_table_correspondance SET login_gepi='".$_POST['login_'.$ligne[$loop]]."', login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $guid_courant) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$insert) {
											$msg.="Erreur lors de l'insertion de l'association ".$guid_courant." &gt; ".$_POST['login_'.$ligne[$loop]]."<br />\n";
										}
										else {
											$nb_reg++;
										}
									}
									else {
										$sql="UPDATE sso_table_correspondance SET login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $guid_courant) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' WHERE login_gepi='".$_POST['login_'.$ligne[$loop]]."';";
										$update=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$update) {
											$msg.="Erreur lors de la mise à jour de l'association ".$guid_courant." &gt; ".$_POST['login_'.$ligne[$loop]]."<br />\n";
										}
										else {
											$nb_reg++;
										}
									}
								}
								else {
									$lig=mysqli_fetch_object($test);
									$msg.="Le GUID $guid_courant est déjà associé à $lig->login_gepi<br />\n";
								}
							}
						}
						else {
							$msg.="Aucun enregistrement pour la ligne $loop<br />";
						}
					}
					else {
						if(isset($ligne_tempo2[$ligne[$loop]])) {
							$tab=explode(";", $ligne_tempo2[$ligne[$loop]]);

							$guid_courant=$tab[$tabindice['Guid']];
							$nom_courant=$tab[$tabindice['Nom']];
							$prenom_courant=$tab[$tabindice['Prénom']];

							$guid_enfant1="";
							$guid_enfant2="";
							$guid_enfant3="";
							if(isset($tab[$tabindice['Guid_Enfant1']])) {
								$guid_enfant1=$tab[$tabindice['Guid_Enfant1']];
							}
							if(isset($tab[$tabindice['Guid_Enfant2']])) {
								$guid_enfant2=$tab[$tabindice['Guid_Enfant2']];
							}
							if(isset($tab[$tabindice['Guid_Enfant3']])) {
								$guid_enfant3=$tab[$tabindice['Guid_Enfant3']];
							}

							$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $guid_courant) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test)==0) {

								$chaine="";
								if($guid_enfant1!="") {
									$chaine.="s.login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $guid_enfant1) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."'";
								}
								if($guid_enfant2!="") {
									if($chaine!="") {$chaine.=" OR ";}
									$chaine.="s.login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $guid_enfant2) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."'";
								}
								if($guid_enfant3!="") {
									if($chaine!="") {$chaine.=" OR ";}
									$chaine.="s.login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $guid_enfant3) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."'";
								}

								$cpt_resp=0;
								$tab_resp=array();
								$tab_resp_login=array();
								if($chaine!="") {
									$sql="SELECT e.* FROM eleves e, sso_table_correspondance s WHERE ($chaine) AND e.login=s.login_gepi ORDER BY e.nom, e.prenom;";
									echo_debug_itop("$sql<br />");
									$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_ele)>0) {
										$cpt_ele=0;
										while($lig_ele=mysqli_fetch_object($res_ele)) {
											$sql="SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r WHERE r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' AND rp.nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $nom_courant) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' AND rp.prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $prenom_courant) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' AND rp.login!='';";
											echo_debug_itop("$sql<br />");
											$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
											if(mysqli_num_rows($res_resp)>0) {
												while($lig_resp=mysqli_fetch_object($res_resp)) {
													if(!in_array($lig_resp->login, $tab_resp_login)) {
														$tab_resp_login[]=$lig_resp->login;
														$tab_resp[$cpt_resp]['login']=$lig_resp->login;
														$tab_resp[$cpt_resp]['civilite']=$lig_resp->civilite;
														$tab_resp[$cpt_resp]['nom']=$lig_resp->nom;
														$tab_resp[$cpt_resp]['prenom']=$lig_resp->prenom;
														$tab_resp[$cpt_resp]['resp_legal']=$lig_resp->resp_legal;
														$tab_resp[$cpt_resp]['info']=$lig_resp->civilite." ".casse_mot($lig_resp->nom,'maj')." ".casse_mot($lig_resp->prenom,'majf2');
														$tab_resp[$cpt_resp]['info'].=" (N° ".$lig_resp->pers_id.")";

														$tab_resp[$cpt_resp]['info'].=" (Légal ".$lig_resp->resp_legal.")";
														$cpt_resp++;
													}
												}
											}
											else {
												$sql="SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r WHERE r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' AND rp.login!='';";
												echo_debug_itop("$sql<br />");
												$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
												if(mysqli_num_rows($res_resp)>0) {
													while($lig_resp=mysqli_fetch_object($res_resp)) {
														if(!in_array($lig_resp->login, $tab_resp_login)) {
															$tab_resp_login[]=$lig_resp->login;
															$tab_resp[$cpt_resp]['login']=$lig_resp->login;
															$tab_resp[$cpt_resp]['civilite']=$lig_resp->civilite;
															$tab_resp[$cpt_resp]['nom']=$lig_resp->nom;
															$tab_resp[$cpt_resp]['prenom']=$lig_resp->prenom;
															$tab_resp[$cpt_resp]['resp_legal']=$lig_resp->resp_legal;
															$tab_resp[$cpt_resp]['info']=$lig_resp->civilite." ".casse_mot($lig_resp->nom,'maj')." ".casse_mot($lig_resp->prenom,'majf2');
															$tab_resp[$cpt_resp]['info'].=" (N° ".$lig_resp->pers_id.")";
															$tab_resp[$cpt_resp]['info'].=" (Légal ".$lig_resp->resp_legal.")";
															$cpt_resp++;
														}
													}
												}
											}
										}
									}
									/*
									echo "<pre>";
									print_r($tab_resp);
									echo "</pre>";
									*/
									if(count($tab_resp)==1) {
										// Un seul responsable correspond
										$lig=mysqli_fetch_object($res);

										$sql="SELECT 1=1 FROM sso_table_correspondance WHERE login_gepi='".$tab_resp_login[0]."';";
										$test=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($test)==0) {
											$sql="INSERT INTO sso_table_correspondance SET login_gepi='".$tab_resp_login[0]."', login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $guid_courant) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);
											if(!$insert) {
												$msg.="Erreur lors de l'insertion de l'association ".$guid_courant." &gt; ".$tab_resp_login[0]."<br />\n";
											}
											else {
												$nb_reg++;
											}
										}
										else {
											$sql="UPDATE sso_table_correspondance SET login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $guid_courant) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' WHERE login_gepi='".$tab_resp_login[0]."';";
											$update=mysqli_query($GLOBALS["mysqli"], $sql);
											if(!$update) {
												$msg.="Erreur lors de la mise à jour de l'association ".$guid_courant." &gt; ".$tab_resp_login[0]."<br />\n";
											}
											else {
												$nb_reg++;
											}
										}
									}
									else {
										$msg.="Responsable ".$nom_courant." ".$prenom_courant." non identifié.<br />\n";
									}
								}
								else {
									$msg.="Aucun élève associé n'a été trouvé.<br />\n";
								}
							}
							else {
								$lig=mysqli_fetch_object($test);
								$msg.="Le GUID $guid_courant est déjà associé à $lig->login_gepi<br />\n";
							}
						}
						else {
							$msg.="Aucun enregistrement pour la ligne $loop<br />";
						}
					}
				}
			}
		}
	}

	if($nb_reg>0) {
		$msg.="$nb_reg enregistrement(s) effectué(s).<br />\n";
	}

	$mode="consult_responsables";
}


if(isset($_POST['enregistrement_personnels'])) {
	check_token();

	$ligne=isset($_POST['ligne']) ? $_POST['ligne'] : NULL;

	$nb_reg=0;

	if(!isset($ligne)) {
		$msg="Aucun enregistrement d'association n'a été demandée.<br />";
	}
	else {
		$ligne_tempo2=array();
		$sql="SELECT * FROM tempo2_sso;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig=mysqli_fetch_object($res)) {
			$ligne_tempo2[$lig->col1]=$lig->col2;
		}

		for($loop=0;$loop<count($ligne);$loop++) {
			// On a eu un choix de login
			if((isset($_POST['login_'.$ligne[$loop]]))&&($_POST['login_'.$ligne[$loop]]!='')) {
				if(isset($ligne_tempo2[$ligne[$loop]])) {
					$tab=explode(";", $ligne_tempo2[$ligne[$loop]]);

					$sql="SELECT login FROM utilisateurs WHERE login='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $_POST['login_'.$ligne[$loop]]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' AND statut!='eleve' AND statut!='responsable';";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)==0) {
						$msg.="Le login de personnel Gepi ".$_POST['login_'.$ligne[$loop]]." proposé n'existe pas.<br />\n";
					}
					else {
						$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test)==0) {
							$sql="SELECT * FROM sso_table_correspondance WHERE login_gepi='".$_POST['login_'.$ligne[$loop]]."';";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test)==0) {
								$sql="INSERT INTO sso_table_correspondance SET login_gepi='".$_POST['login_'.$ligne[$loop]]."', login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$insert) {
									$msg.="Erreur lors de l'insertion de l'association ".$tab[0]." &gt; ".$_POST['login_'.$ligne[$loop]]."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
							else {
								$sql="UPDATE sso_table_correspondance SET login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' WHERE login_gepi='".$_POST['login_'.$ligne[$loop]]."';";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$update) {
									$msg.="Erreur lors de la mise à jour de l'association ".$tab[0]." &gt; ".$_POST['login_'.$ligne[$loop]]."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
						}
						else {
							$lig=mysqli_fetch_object($test);
							$msg.="Le GUID $tab[0] est déjà associé à $lig->login_gepi<br />\n";
						}
					}
				}
				else {
					$msg.="Aucun enregistrement pour la ligne $loop<br />";
				}
			}
			else {
				if(isset($ligne_tempo2[$ligne[$loop]])) {
					$tab=explode(";", $ligne_tempo2[$ligne[$loop]]);

					$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)==0) {

						$sql="SELECT * FROM utilisateurs WHERE nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[1]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' AND prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[2]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' AND statut!='eleve' AND statut!='responsable';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)==1) {
							// Un seul élève correspond
							$lig=mysqli_fetch_object($res);

							$sql="SELECT 1=1 FROM sso_table_correspondance WHERE login_gepi='$lig->login';";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test)==0) {
								$sql="INSERT INTO sso_table_correspondance SET login_gepi='$lig->login', login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$insert) {
									$msg.="Erreur lors de l'insertion de l'association ".$tab[0]." &gt; ".$lig->login."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
							else {
								$sql="UPDATE sso_table_correspondance SET login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' WHERE login_gepi='$lig->login';";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$update) {
									$msg.="Erreur lors de la mise à jour de l'association ".$tab[0]." &gt; ".$lig->login."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
						}
						elseif(mysqli_num_rows($res)==0) {
							$msg.="Aucun enregistrement dans la table 'utilisateurs' pour ".$tab[1]." ".$tab[2]." !<br />\n";
						}
						else {
							// On ne doit pas arriver là
							$msg.="Plusieurs enregistrements dans la table 'utilisateurs' pour ".$tab[1]." ".$tab[2]." !<br />\n";
						}
					}
					else {
						$lig=mysqli_fetch_object($test);
						$msg.="Le GUID $tab[0] est déjà associé à $lig->login_gepi<br />\n";
					}
				}
				else {
					$msg.="Aucun enregistrement pour la ligne $loop<br />";
				}
			}
		}
	}

	if($nb_reg>0) {
		$msg.="$nb_reg enregistrement(s) effectué(s).<br />\n";
	}

	$mode="consult_personnels";
}

if(isset($_POST['enregistrement_saisie_manuelle'])) {
	check_token();

	$login_gepi=isset($_POST["login_gepi"]) ? $_POST["login_gepi"] : NULL;
	$login_sso=isset($_POST["login_sso"]) ? $_POST["login_sso"] : NULL;

	$nb_reg=0;
	if((!isset($login_gepi))||(!isset($login_gepi))||($login_sso=='')||($login_gepi=='')) {
		$msg.="Un des champs login_gepi ou guid n'a pas été transmis.<br />\n";
	}
	else {
		$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".$login_sso."';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			$sql="SELECT statut FROM utilisateurs WHERE login='$login_gepi';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==1) {
				$statut_compte=old_mysql_result($res, 0, "statut");

				$sql="SELECT 1=1 FROM sso_table_correspondance WHERE login_gepi='$login_gepi';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)==0) {
					$sql="INSERT INTO sso_table_correspondance SET login_gepi='$login_gepi', login_sso='$login_sso';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'insertion de l'association ".$login_sso." &gt; ".$login_gepi."<br />\n";
					}
					else {
						$nb_reg++;
					}
				}
				else {
					$sql="UPDATE sso_table_correspondance SET login_sso='".$login_sso."' WHERE login_gepi='$login_gepi';";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$update) {
						$msg.="Erreur lors de la mise à jour de l'association ".$login_sso." &gt; ".$login_gepi."<br />\n";
					}
					else {
						$nb_reg++;
					}
				}
			}
			elseif(mysqli_num_rows($res)==0) {
				$msg.="Aucun enregistrement dans la table 'eleves' pour $login_gepi<br />\n";
			}
			else {
				$msg.="Anomalie : Plusieurs enregistrements dans la table 'eleves' pour $login_gepi<br />\n";
			}
		}
		else {
			$lig=mysqli_fetch_object($test);
			$msg.="Le GUID $login_sso est déjà associé à $lig->login_gepi<br />\n";
		}
	}

	if($nb_reg>0) {
		$msg.="$nb_reg enregistrement effectué.<br />\n";

		if($statut_compte=='eleve') {
			$mode="consult_eleves";
		}
		elseif($statut_compte=='responsable') {
			$mode="consult_eleves";
		}
		else {
			$mode="consult_personnels";
		}
	}
}

if(isset($_POST['suppr'])) {
	check_token();

	$cpt_suppr=0;
	$suppr=$_POST['suppr'];
	for($loop=0;$loop<count($suppr);$loop++) {
		$sql="DELETE FROM sso_table_correspondance WHERE login_gepi='".$suppr[$loop]."';";
		$menage=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$menage) {
			$msg.="Erreur lors de la suppression de ".$suppr[$loop]."<br />";
		}
		else {
			$cpt_suppr++;
		}
	}

	if($cpt_suppr>0) {
		$msg.="$cpt_suppr association(s) supprimée(s).<br />";
	}
}

if($mode=='vider') {
	check_token();

	$sql="TRUNCATE sso_table_correspondance;";
	$menage=mysqli_query($GLOBALS["mysqli"], $sql);
	if($menage) {
		$msg.="Table sso_table_correspondance vidée.<br />";
	}
	else {
		$msg.="Erreur lors du 'vidage' de sso_table_correspondance.<br />";
	}
	//unset($mode);
	$mode="";
}

if($mode=='suppr_scories') {
	check_token();

	$sql="SELECT login_gepi FROM sso_table_correspondance WHERE login_gepi NOT IN (SELECT login FROM utilisateurs);";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_scories=mysqli_num_rows($res);
	if($nb_scories>0) {
		$sql="DELETE FROM sso_table_correspondance WHERE login_gepi NOT IN (SELECT login FROM utilisateurs);";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if($res) {
			$msg.="$nb_scories association(s) obsolète(s) supprimée(s).<br />";
		}
		else {
			$msg.="Erreur lors de la suppression des $nb_scories association(s) obsolète(s).<br />";
		}
	}
	else {
		$msg.="Aucune association .<br />";
	}

	//unset($mode);
	$mode="";
}

if($mode=='valider_forcer_logins_mdp_responsables') {
	check_token();

	$nb_nouveaux_comptes=0;
	$nb_comptes_remplaces=0;
	$nb_erreur=0;
	$ligne=isset($_POST['ligne']) ? $_POST['ligne'] : array();

	/*
		echo "<pre>";
		print_r($ligne);
		echo "</pre>";

		Posté depuis le formulaire:
			Array
			(
				[col1] => pers_id choisi
				[0] => 1510775
				[49] => 1432901
				[50] => 1432902
				[106] => 1432905
			)

		Enregistré préalablement dans la table tempo4;
		mysql> select * from tempo4 where col1='0' or col1='49' or col1='50' or col1='106';
		+------+----------------------+----------------------------------+------+
		| col1 | col2                 | col3                             | col4 |
		+------+----------------------+----------------------------------+------+
		| 0    | denis.XXXX1          | 64ce0ed8cXXXXXXXXXXXXXXXXXXXXXXX |      |
		| 49   | christelle.XXXXXXXX1 | e5e610953XXXXXXXXXXXXXXXXXXXXXXX |      |
		| 50   | joel.XXXXXXXXX       | f8bad8df0XXXXXXXXXXXXXXXXXXXXXXX |      |
		| 106  | ludovic.XXXXXX       | fac5cb6f2XXXXXXXXXXXXXXXXXXXXXXX |      |
		+------+----------------------+----------------------------------+------+
		4 rows in set (0.01 sec)

		mysql> 
	*/

	$tab_tempo4=array();
	$sql="SELECT * FROM tempo4;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_tempo4[$lig->col1]['login']=$lig->col2;
			$tab_tempo4[$lig->col1]['md5_password']=$lig->col3;
		}
	}

	foreach($ligne as $id_col1 => $pers_id) {
		if($pers_id!="") {
			$sql="SELECT * FROM resp_pers WHERE pers_id='$pers_id';";
			echo_debug_itop("$sql<br />");
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				$msg.="ERREUR : Le responsable n°$pers_id n'existe pas dans la table 'resp_pers'.<br />";
				$nb_erreur++;
			}
			else {
				$lig=mysqli_fetch_object($res);

				if(!isset($tab_tempo4[$id_col1])) {
					$msg.="ERREUR : Le numéro $id_col1 de l'enregistrement 'tempo4' que vous souhaitez associer au responsable n°$pers_id (<em>$lig->nom $lig->prenom</em>) n'existe pas dans la table 'tempo4'.<br />";
					$nb_erreur++;
				}
				else {
					$sql="SELECT * FROM utilisateurs WHERE login='".$tab_tempo4[$id_col1]['login']."';";
					echo_debug_itop("$sql<br />");
					$test_u=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test_u)>0) {
						$lig_u=mysqli_fetch_object($test_u);

						$msg.="ERREUR : Le login ".$tab_tempo4[$id_col1]['login']." que vous souhaitez associer au responsable n°$pers_id (<em>$lig->nom $lig->prenom</em>) est déjà associé à un utilisateur de statut '$lig_u->statut' nommé $lig_u->nom $lig_u->prenom.<br />";
						$nb_erreur++;
					}
					else {
						if($lig->login!="") {
							$sql="SELECT * FROM utilisateurs WHERE login='".$lig->login."' AND statut='responsable';";
							echo_debug_itop("$sql<br />");
							$test_u=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test_u)>0) {
								$sql="DELETE FROM utilisateurs WHERE login='".$lig->login."' AND statut='responsable';";
								echo_debug_itop("$sql<br />");
								$menage=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$menage) {
									$msg.="ERREUR : La suppression de l'ancien compte d'utilisateur $lig->login associé au responsable n°$pers_id (<em>$lig->nom $lig->prenom</em>) a échoué.<br />";
									$nb_erreur++;
								}
								else {
									$sql="INSERT INTO utilisateurs SET login='".$tab_tempo4[$id_col1]['login']."', 
												password='".$tab_tempo4[$id_col1]['md5_password']."', 
												salt='', 
												nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig->nom) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', 
												prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig->prenom) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', 
												civilite='$lig->civilite', 
												change_mdp='n', 
												email='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig->mel) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', 
												auth_mode='gepi', 
												statut='responsable', 
												etat='inactif';";
									echo_debug_itop("$sql<br />");
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if($insert) {
										$sql="UPDATE resp_pers SET login='".$tab_tempo4[$id_col1]['login']."' WHERE pers_id='$pers_id';";
										echo_debug_itop("$sql<br />");
										$update=mysqli_query($GLOBALS["mysqli"], $sql);
										if($update) {
											$nb_comptes_remplaces++;
										}
										else {
											$msg.="ERREUR : Le remplacement du login dans 'resp_pers' par ".$tab_tempo4[$id_col1]['login']." pour le responsable n°$pers_id (<em>$lig->nom $lig->prenom</em>) a échoué.<br />";
											$nb_erreur++;
										}
									}
									else {
										$nb_erreur++;
									}
								}
							}
							else {
								// Il y avait un login dans resp_pers, mais il n'existait pas d'enregistrement dans utilisateurs
								$sql="INSERT INTO utilisateurs SET login='".$tab_tempo4[$id_col1]['login']."', 
											password='".$tab_tempo4[$id_col1]['md5_password']."', 
											salt='', 
											nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig->nom) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', 
											prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig->prenom) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', 
											civilite='$lig->civilite', 
											change_mdp='n', 
											email='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig->mel) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', 
											auth_mode='gepi', 
											statut='responsable', 
											etat='inactif';";
								echo_debug_itop("$sql<br />");
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {
									$sql="UPDATE resp_pers SET login='".$tab_tempo4[$id_col1]['login']."' WHERE pers_id='$pers_id';";
									echo_debug_itop("$sql<br />");
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									if($update) {
										$nb_nouveaux_comptes++;
									}
									else {
										$msg.="ERREUR : Le remplacement du login dans 'resp_pers' par ".$tab_tempo4[$id_col1]['login']." pour le responsable n°$pers_id (<em>$lig->nom $lig->prenom</em>) a échoué.<br />";
										$nb_erreur++;
									}
								}
								else {
									$nb_erreur++;
								}
							}
						}
						else {
							$sql="INSERT INTO utilisateurs SET login='".$tab_tempo4[$id_col1]['login']."', 
										password='".$tab_tempo4[$id_col1]['md5_password']."', 
										salt='', 
										nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig->nom) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', 
										prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig->prenom) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', 
										civilite='$lig->civilite', 
										change_mdp='n', 
										email='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig->mel) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', 
										auth_mode='gepi', 
										statut='responsable', 
										etat='inactif';";
							echo_debug_itop("$sql<br />");
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if($insert) {
								$sql="UPDATE resp_pers SET login='".$tab_tempo4[$id_col1]['login']."' WHERE pers_id='$pers_id';";
								echo_debug_itop("$sql<br />");
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
								if($update) {
									$nb_nouveaux_comptes++;
								}
								else {
									$msg.="ERREUR : L'enregistrement du login dans 'resp_pers' par ".$tab_tempo4[$id_col1]['login']." pour le responsable n°$pers_id (<em>$lig->nom $lig->prenom</em>) a échoué.<br />";
									$nb_erreur++;
								}
							}
							else {
								$msg.="ERREUR : L'enregistrement du compte d'utilisateur ".$tab_tempo4[$id_col1]['login']." pour le responsable n°$pers_id (<em>$lig->nom $lig->prenom</em>) a échoué.<br />";
								$nb_erreur++;
							}
						}
					}
				}
			}
		}
	}

	// Les comptes créés sont pour le moment inactifs.
	if($nb_erreur>0) {
		$msg.="<br />";
		$msg.="$nb_erreur erreurs se sont produites.<br />";
	}

	if($nb_nouveaux_comptes>0) {
		$msg.="<br />";
		$msg.="$nb_nouveaux_comptes nouveaux comptes ont été enregistrés.<br />";
	}

	if($nb_comptes_remplaces>0) {
		$msg.="<br />";
		$msg.="$nb_comptes_remplaces comptes d'utilisateurs ont été remplacés.<br />";
	}

	$msg.="<br />";
	$msg.="NOTE : Les comptes créés ou modifiés n'ont pas été activés.<br />";
	$msg.="Vous devrez activer ces comptes dans Gestion des bases/Gestion des comptes d'utilisateurs/Responsables.<br />";

	// Ménage:
	//$sql="TRUNCATE tempo4;";
	$menage=mysqli_query($GLOBALS["mysqli"], $sql);

	//unset($mode);
	$mode="";
}

//**************** EN-TETE *****************
$titre_page = "ENT ITOP : Rapprochement";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

if($msg!="") {
	echo "<div style='position:absolute; top:100px; left:20px; width:18px;' class='noprint'><a href='#lien_retour'><img src='../images/down.png' width='18' height='18' title='Passer les messages' /></a></div>\n";
}

$module_suhosin_actif="n";
$suhosin_post_max_totalname_length=ini_get('suhosin.post.max_totalname_length');
if($suhosin_post_max_totalname_length!='') {
	$module_suhosin_actif="y";
}

//debug_var();
$sql="CREATE TABLE IF NOT EXISTS tempo2_sso ( col1 varchar(100) NOT NULL default '', col2 TEXT NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

?>

<script type="text/javascript" language="JavaScript">

	function recherche_utilisateur(statut) {
		// Ce n'est pas tout à fait le statut, mais 'eleve', 'responsable' ou 'personnel'
		recherche="recherche_"+statut;
		nom_rech=document.getElementById('nom_rech').value;
		prenom_rech=document.getElementById('prenom_rech').value;
		csrf_alea=document.getElementById('csrf_alea').value

		new Ajax.Updater($('div_resultat'),"<?php echo $_SERVER['PHP_SELF']?>",{method: 'post',
		parameters: {
			nom_rech: nom_rech,
			prenom_rech: prenom_rech,
			recherche: recherche,
			csrf_alea: csrf_alea
		}});
	}

</script>

<?php

echo "<a name='lien_retour'></a>
<p class='bold noprint'>
<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if((!isset($mode))||($mode=="")) {
	echo "
</p>

<h2>Rapprochement des comptes ENT ITOP/GEPI</h2>

<div style='margin-left:4em;'>
<p><a href='".$_SERVER['PHP_SELF']."?mode=saisie_manuelle'>Saisir manuellement une association</a></p>
<p>Ou importer un CSV&nbsp;:</p>
<ul>
	<li><a href='".$_SERVER['PHP_SELF']."?mode=import_eleves'>Importer un CSV élèves</a></li>
	<li><a href='".$_SERVER['PHP_SELF']."?mode=import_responsables'>Importer un CSV responsables</a></li>
	<li><a href='".$_SERVER['PHP_SELF']."?mode=import_personnels'>Importer un CSV personnels de l'établissement</a></li>
</ul>
<p> ou consulter&nbsp;:</p>
<ul>";

	//$sql="SELECT e.*, s.* FROM eleves e, sso_table_correspondance s WHERE s.login_gepi=e.login ORDER BY e.nom, e.prenom LIMIT 1;";
	$sql="SELECT 1=1 FROM eleves e, sso_table_correspondance s WHERE s.login_gepi=e.login;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "
	<li>Aucune association élève n'est encore enregistrée.</li>";
	}
	else {
		echo "
	<li><a href='".$_SERVER['PHP_SELF']."?mode=consult_eleves'>Consulter les associations élèves</a> (<em>".mysqli_num_rows($res)." association(s) enregistrée(s)</em>)</li>";
	}

	//$sql="SELECT rp.*, s.* FROM resp_pers rp, sso_table_correspondance s WHERE s.login_gepi=rp.login ORDER BY rp.nom, rp.prenom LIMIT 1;";
	$sql="SELECT 1=1 FROM resp_pers rp, sso_table_correspondance s WHERE s.login_gepi=rp.login;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "
	<li>Aucune association responsable n'est encore enregistrée.</li>";
	}
	else {
		echo "
	<li><a href='".$_SERVER['PHP_SELF']."?mode=consult_responsables'>Consulter les associations responsables</a> (<em>".mysqli_num_rows($res)." association(s) enregistrée(s)</em>)</li>";
	}

	//$sql="SELECT u.*, s.* FROM utilisateurs u, sso_table_correspondance s WHERE s.login_gepi=u.login AND u.statut!='eleve' AND u.statut!='responsable' LIMIT 1;";
	$sql="SELECT 1=1 FROM utilisateurs u, sso_table_correspondance s WHERE s.login_gepi=u.login AND u.statut!='eleve' AND u.statut!='responsable';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "
	<li>Aucune association n'est encore enregistrée pour les personnels de l'établissement.</li>";
	}
	else {
		echo "
	<li><a href='".$_SERVER['PHP_SELF']."?mode=consult_personnels'>Consulter les associations personnels</a> (<em>".mysqli_num_rows($res)." association(s) enregistrée(s)</em>)</li>";
	}

	echo "
</ul>";

	//======================================================================
	// Fiches bienvenue Elèves
	//﻿Nom;Prénom;Login;Numéro de jointure;Mot de passe;Email;Classe;Etat;Date de désactivation
	//DUPRE;Thomas;thomas.dupre;MENESR$12345;mdp&*;Thomas.DUPRE@ent27.fr;6 A;Actif
	echo "<p> ou générer des Fiches Bienvenue&nbsp;:</p>
<ul>";

	$sql="SELECT 1=1 FROM eleves e, sso_table_correspondance s WHERE s.login_gepi=e.login;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_corresp_ele=mysqli_num_rows($res);
	if($nb_corresp_ele==0) {
		echo "
	<li>Aucune association élève n'est encore enregistrée.</li>";
	}
	else {
		echo "
	<li><a href='".$_SERVER['PHP_SELF']."?mode=publipostage_eleves'>Générer les Fiches Bienvenue élèves</a> (<em>".mysqli_num_rows($res)." association(s) enregistrée(s)</em>)</li>";
	}

	//===================================================
	// Fiches bienvenue Responsables
	//$sql="SELECT rp.*, s.* FROM resp_pers rp, sso_table_correspondance s WHERE s.login_gepi=rp.login ORDER BY rp.nom, rp.prenom LIMIT 1;";
	$sql="SELECT 1=1 FROM resp_pers rp, sso_table_correspondance s WHERE s.login_gepi=rp.login;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_corresp_resp=mysqli_num_rows($res);
	if($nb_corresp_resp==0) {
		echo "
	<li>Aucune association responsable n'est encore enregistrée.</li>";
	}
	else {
		echo "
	<li><a href='".$_SERVER['PHP_SELF']."?mode=publipostage_responsables'>Générer les Fiches Bienvenue responsables</a> (<em>".mysqli_num_rows($res)." association(s) enregistrée(s)</em>)</li>";
	}

	//===================================================
	// Fiches bienvenue Personnels
	//$sql="SELECT u.*, s.* FROM utilisateurs u, sso_table_correspondance s WHERE s.login_gepi=u.login AND u.statut!='eleve' AND u.statut!='responsable' LIMIT 1;";
	$sql="SELECT 1=1 FROM utilisateurs u, sso_table_correspondance s WHERE s.login_gepi=u.login AND u.statut!='eleve' AND u.statut!='responsable';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_corresp_pers=mysqli_num_rows($res);
	if($nb_corresp_pers==0) {
		echo "
	<li>Aucune association n'est encore enregistrée pour les personnels de l'établissement.</li>";
	}
	else {
		echo "
	<li><a href='".$_SERVER['PHP_SELF']."?mode=publipostage_personnels'>Générer les Fiches Bienvenue personnels</a> (<em>".mysqli_num_rows($res)." association(s) enregistrée(s)</em>)</li>";
	}

	echo "
</ul>
<p>Cette rubrique permet de fournir les fichiers CSV de rénitialisation de mots de passe générés par l'ENT, ou les CSV des nouveaux élèves.</p>";

	//===================================================
	// Scories:
	$sql="SELECT login_gepi FROM sso_table_correspondance WHERE login_gepi NOT IN (SELECT login FROM utilisateurs);";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_scories=mysqli_num_rows($res);
	if($nb_scories>0) {
		echo "
<br />
<p><strong style='color:red'>SCORIES&nbsp;:</strong> ".$nb_scories." association(s) existent dans la table 'sso_table_correspondance' pour des login qui n'existent plus dans Gepi.<br />
Ces scories peuvent perturber l'association GUID_ENT/Login_GEPI.<br />
<a href='".$_SERVER['PHP_SELF']."?mode=suppr_scories".add_token_in_url()."' >Supprimer ces scories</a></p>";
	}

	//===================================================
	// Vider:
	$sql="SELECT 1=1 FROM sso_table_correspondance;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "
<br />
<p>ou <a href='".$_SERVER['PHP_SELF']."?mode=vider".add_token_in_url()."' onclick=\"return confirmlink(this, 'ATTENTION !!! Êtes-vous vraiment sûr de vouloir vider la table sso_table_correspondance ?', 'Confirmation du vidage')\">vider la table des correspondances</a></p>";
		echo "<p>La table de correspondances contient actuellement ".mysqli_num_rows($res)." enregistrements.</p>\n";
	}

	//===================================================
	// Associations manquantes
	if(($nb_corresp_resp>0)||($nb_corresp_ele>0)||($nb_corresp_pers>0)) {
		echo "<br />
<p><strong>Associations manquantes&nbsp;:</strong></p>";

		$sql="select distinct e.login, e.nom, e.prenom from eleves e, utilisateurs u where e.login=u.login AND u.auth_mode='sso' and e.login not in (select login_gepi from sso_table_correspondance);";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_assoc_manquantes_ele=mysqli_num_rows($res);
		if($nb_assoc_manquantes_ele>0) {
			echo "
<br />
<p>Il manque $nb_assoc_manquantes_ele association(s) élève(s)&nbsp;: ";
			$cpt=0;
			while($lig=mysqli_fetch_object($res)) {
				if($cpt>0) {echo ", ";}
				echo "<a href='../eleves/modify_eleve.php?eleve_login=$lig->login' target='_blank'>".casse_mot($lig->nom, 'maj')." ".casse_mot($lig->prenom, 'majf2')."</a>";
				$cpt++;
			}
			echo "</p>";
		}

		$sql="select distinct rp.login, rp.nom, rp.prenom, rp.civilite, rp.pers_id FROM resp_pers rp, utilisateurs u where u.auth_mode='sso' AND u.login=rp.login AND rp.login!='' and rp.login not in (select login_gepi from sso_table_correspondance);";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_assoc_manquantes_resp=mysqli_num_rows($res);
		if($nb_assoc_manquantes_resp>0) {
			echo "
<br />
<p>Il manque $nb_assoc_manquantes_resp association(s) responsable(s)&nbsp;: ";
			$cpt=0;
			while($lig=mysqli_fetch_object($res)) {
				if($cpt>0) {echo ", ";}
				echo "<a href='../responsables/modify_resp.php?pers_id=$lig->pers_id' target='_blank'>".$lig->civilite." ".casse_mot($lig->nom, 'maj')." ".casse_mot($lig->prenom, 'majf2')."</a>";
				$cpt++;
			}
			echo "</p>";
		}

		$sql="select distinct u.login, u.nom, u.prenom, u.civilite from utilisateurs u where u.auth_mode='sso' AND u.statut!='eleve' and u.statut!='responsable' and u.login not in (select login_gepi from sso_table_correspondance);";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_assoc_manquantes_resp=mysqli_num_rows($res);
		if($nb_assoc_manquantes_resp>0) {
			echo "
<br />
<p>Il manque $nb_assoc_manquantes_resp association(s) personnel(s)&nbsp;: ";
			$cpt=0;
			while($lig=mysqli_fetch_object($res)) {
				if($cpt>0) {echo ", ";}
				echo "<a href='../utilisateurs/modify_user.php?user_login=$lig->login' target='_blank'>".$lig->civilite." ".casse_mot($lig->nom, 'maj')." ".casse_mot($lig->prenom, 'majf2')."</a>";
				$cpt++;
			}
			echo "</p>";
		}

		echo "<br /><p>Ces utilisateurs disposent d'un compte dans Gepi, mais n'ont pas d'association SSO.<br />
		Vous devriez refaire un import des fichiers ExportSSO_...</p>";
	}

	//===================================================
	echo "
<br />
<p style='color:red'>A FAIRE : Détecter et permettre de supprimer des associations pour des élèves,... qui ne sont plus dans le CSV.</p>
<br />
\n";

	if($module_suhosin_actif=="y") {
		echo "<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Le module PHP <span style='color:red'>Suhosin</span> est actif.<br />Il peut perturber l'enregistrement des associations lorsqu'un grand nombre de champs est envoyé.<br />Vous devrez peut-être soumettre plusieurs fois les mêmes fichiers CSV pour enregistrer par tranches les associations.</p>
<br />
";
	}

	$sql="SELECT 1=1 FROM utilisateurs WHERE statut!='eleve' AND statut!='responsable' AND auth_mode='sso';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_pers_sso=mysqli_num_rows($res);
	$sql="SELECT 1=1 FROM utilisateurs WHERE statut!='eleve' AND statut!='responsable' AND auth_mode='ldap';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_pers_ldap=mysqli_num_rows($res);
	$sql="SELECT 1=1 FROM utilisateurs WHERE statut!='eleve' AND statut!='responsable' AND auth_mode='gepi';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_pers_gepi=mysqli_num_rows($res);

	$sql="SELECT 1=1 FROM utilisateurs u, eleves e WHERE u.statut='eleve' AND e.login=u.login AND auth_mode='sso';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_ele_sso=mysqli_num_rows($res);
	$sql="SELECT 1=1 FROM utilisateurs u, eleves e WHERE u.statut='eleve' AND e.login=u.login AND auth_mode='ldap';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_ele_ldap=mysqli_num_rows($res);
	$sql="SELECT 1=1 FROM utilisateurs u, eleves e WHERE u.statut='eleve' AND e.login=u.login AND auth_mode='gepi';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_ele_gepi=mysqli_num_rows($res);

	$sql="SELECT 1=1 FROM utilisateurs u, resp_pers rp WHERE u.statut='responsable' AND rp.login=u.login AND auth_mode='sso';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_resp_sso=mysqli_num_rows($res);
	$sql="SELECT 1=1 FROM utilisateurs u, resp_pers rp WHERE u.statut='responsable' AND rp.login=u.login AND auth_mode='ldap';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_resp_ldap=mysqli_num_rows($res);
	$sql="SELECT 1=1 FROM utilisateurs u, resp_pers rp WHERE u.statut='responsable' AND rp.login=u.login AND auth_mode='gepi';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_resp_gepi=mysqli_num_rows($res);

	echo "<p><em>NOTES&nbsp;:</em></p>
<ul>
	<li>
		<p>Les CSV réclamés dans les pages d'importation sont accessibles en suivant le cheminement suivant&nbsp;:<br />
		Se connecter avec un compte administrateur de l'ENT.<br />
		Menu Administration puis Gérer les utilisateurs puis Outils puis Traitement en masse puis Action (<em>Choisir Exportation SSO au format CSV</em>) puis dans Profil sélectionner le profil (<em>Elève, Parent,...</em>)<br />
		puis Traiter cette action puis Valider.</p>
	</li>
	<li>
		<p>Les CSV pour les Fiches bienvenue peuvent aussi être ceux des nouveaux élèves ou parents<br />(<em>[V2]CLG-".getSettingValue('gepiSchoolName')."-ac-ROUEN - [".getSettingValue('gepiSchoolRne')."] - [ANNEEMOISJOURHEURE].xlsx<br />ou ".getSettingValue('gepiSchoolRne')."_CSV_ANNEEMOISJOURHEURE.zip</em>).</p>
	</li>
	<li>
		<p>Votre base compte des utilisateurs des statuts suivants avec les modes d'authentification suivants&nbsp;:</p>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th rowspan='2'>Statut</th>
				<th colspan='3'>Mode d'authentification</th>
			</tr>
			<tr>
				<th>Gepi</th>
				<th>SSO</th>
				<th>LDAP</th>
			</tr>
			<tr>
				<td>Personnels</td>
				<td>$nb_pers_gepi</td>
				<td>$nb_pers_sso</td>
				<td>$nb_pers_ldap</td>
			</tr>
			<tr>
				<td>Responsables</td>
				<td>$nb_resp_gepi</td>
				<td>$nb_resp_sso</td>
				<td>$nb_resp_ldap</td>
			</tr>
			<tr>
				<td>Élèves</td>
				<td>$nb_ele_gepi</td>
				<td>$nb_ele_sso</td>
				<td>$nb_ele_ldap</td>
			</tr>
		</table>
		<p>Certains comptes peuvent être inactifs (<em style='color:red'>à détailler dans le futur</em>).</p>
	</li>
</ul>

</div>

<h2>Forcer les logins des responsables (<em>expérimental</em>)</h2>

<div style='margin-left:4em;'>
	<p>Si l'accès SSO de l'ENT vers Gepi tarde à être mis en place, vous pouvez ouvrir l'accès aux parents en limitant les difficultés&nbsp;:<br />
	Il s'agit de créer des comptes dans Gepi avec les logins et mots de passe proposés par l'ENT.<br />
	Les parents auront donc les mêmes comptes et mots de passe initiaux dans l'ENT et dans Gepi<br />(<em>s'ils changent leur mot de passe d'un côté ou de l'autre, la synchronisation des mots de passe n'est pas assurée</em>)</p>
	<p>L'authentification des parents sera locale (<em>sur la base GEPI et non en SSO CAS</em>).</p>

	<p><br /></p>

	<p><a href='".$_SERVER['PHP_SELF']."?supprimer_comptes_parents=y".add_token_in_url()."' onclick=\"return confirmlink(this, 'ATTENTION !!! Êtes-vous vraiment sûr de vouloir supprimer les comptes d utilisateurs des parents d élèves ?', 'Confirmation de la suppression ?')\">Supprimer les comptes d'utilisateurs des parents actuels</a>.<br />
	Dans cette opération (<em>irréversible</em>), les entrées parents sont supprimées de la table 'utilisateurs' et les logins des responsables sont réinitialisés/vidés dans la table 'resp_pers'.<br />
	Les responsables ne sont pas pour autant supprimés (<em>simplement, ils n'auront plus de compte utilisateur</em>).<br />
	Les bulletins pourront toujours porter leurs noms et adresses, les courriers d'absence, pourront leur être adressés,...</p>

	<p><br /></p>

	<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
		<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
			".add_token_field()."
			<p>Fournir le fichier CSV de mots de passe de l'ENT pour créer les comptes parents avec ces logins et mots de passe.<br />
			</p>
			<p>Veuillez fournir le fichier <strong>".getSettingValue("gepiSchoolRne")."_MiseaJour_Motdepasse_Parent_JJ_MM_AAAA_HH_MM_SS.csv</strong> généré par l'ENT.</p>
			<input type='hidden' name='mode' value='forcer_logins_mdp_responsables' />
			<input type=\"file\" size=\"65\" name=\"csv_file\" style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /><br />
			<input type='submit' value='Envoyer' />
		</fieldset>
	</form>

	<p><br /></p>

	<p style='text-indent:-4em; margin-left:4em;'><em>NOTES&nbsp;:</em></p>
	<ul>
		<li>Le fichier CSV attendu doit avoir le format suivant&nbsp;:<br />
		﻿﻿Nom;Prénom;Login;Numéro de jointure;Mot de passe;Email;Adresse;Code postal;Ville;Nom enfant 1;Prénom enfant 1;Classe enfant 1;Etat;Date de désactivation<br />
		DUPRE;Denis;denis.dupre1;MENESR$1234567;azerty&*;Denis.DUPRE1@ent27.fr;3 RUE DES PRIMEVERES;27300;BERNAY;DUPRE;Thomas;6 A;Actif<br />
		...</li>
	</ul>

	<p><br /></p>

	<p><strong>Si les logins des responsables ont été forcés pour coïncider avec leurs logins dans l'ENT</strong>, et si votre base Sconet contenait les adresses email des parents (<em>si elles étaient demandées sur le dossier d'inscription dans l'établissement, si votre secrétaire s'est embêté(e) à les saisir;</em>), vous pouvez <a href='".$_SERVER['PHP_SELF']."?mode=envoi_mail_logins_mdp'>envoyer par mail les fiches bienvenues avec les logins et mots de passe</a>.</p>
	<p>Pour que l'envoi fonctionne, il faut que les logins coïncident.<br />
	En revanche, que les comptes parents soient en authentification locale ou en authentification SSO (<em>CAS</em>) importe peu.</p>

</div>
\n";

	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="saisie_manuelle") {
		echo " | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
</p>

<h2>Rapprochement des comptes ENT ITOP/GEPI</h2>

<p>Saisir manuellement une association Login_gepi / Guid&nbsp;:</p>
<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	".add_token_field()."
	<input type='hidden' name='mode' value='saisie_manuelle' />
	<input type='hidden' name='enregistrement_saisie_manuelle' value='y' />
	<table border='0'>
		<tr><th style='text-align:left;'>Login Gepi&nbsp;:</th><td><input type=\"text\" name=\"login_gepi\" size=\"50\" value=\"".(isset($_GET['login_gepi']) ? $_GET['login_gepi'] : "")."\" /></td></tr>
		<tr><th style='text-align:left;'>Guid ENT&nbsp;:</th><td><input type=\"text\" name=\"login_sso\" size=\"50\" value=\"".(isset($_GET['login_sso']) ? $_GET['login_sso'] : "")."\" /></td></tr>
	</table>
	<input type='submit' value='Valider' />
</form>

";
	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="import_eleves") {
	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
		echo " | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
</p>";

	if(!isset($csv_file)) {
		echo "
<h2>Rapprochement des comptes ENT ITOP/GEPI</h2>

<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		".add_token_field()."
		<input type='hidden' name='mode' value='import_eleves' />
		<input type=\"file\" size=\"65\" name=\"csv_file\" style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /><br />
		<input type=\"checkbox\" id=\"exclure_classes_anormales\" name=\"exclure_classes_anormales\" value=\"y\" checked /><label for='exclure_classes_anormales'>Exclure les classes nommées BASE20XXXXXX (<em>plus précisément contenant la chaine 'BASE20'</em>)</label><br />
		<input type='submit' value='Envoyer' />
	</fieldset>
</form>

<p><br /></p>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li>Le fichier CSV attendu doit avoir le format suivant&nbsp;:<br />
	Guid;Nom;Prénom;Profil;Classes;Groupe;Naissance<br />
	f73d0f72-0958-4b8f-85f7-a58a96d95220;DISSOIR;Alain;National_1;0310000Z$1L1;16/06/1987<br />
	...</li>
	<li>Il peut arriver que le CSV fourni contienne des élèves de l'année précédente.<br />
	La classe est alors par exemple&nbsp;: BASE2011-2012<br />
	Proposer d'effectuer un rapprochement pour des élèves qui ne sont plus là n'est pas souhaitable.<br />
	Vous pouvez aussi avoir un même élève qui apparaît avec deux lignes dans le fichier&nbsp;:<br />
	Une ligne avec le GUID de l'année passée et associé à une classe BASE20XX-20XX<br />
	et une ligne avec le GUID de cette année (<em>c'est celui qu'il faut retenir</em>).</li>
	<li>Il est recommandé d'envoyer deux fois le même CSV.<br />
	La première fois pour prendre en compte les élèves correctement identifiés.<br />
	La deuxième pour voir quels élèves du CSV n'ont pas été associés à un login Gepi.</li>
</ul>\n";
	}
	else {
		check_token(false);
		$fp=fopen($csv_file['tmp_name'],"r");

		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$motif_nom_fichier="ExportSSO_Eleve_";
		echo "<p>Le fichier fourni se nomme <strong>".$csv_file['name']."</strong>";
		if(!preg_match("/$motif_nom_fichier/", $csv_file['name'])) {
			echo "<br />
<span style='color:red'>Le nom du fichier contient habituellement la chaine <strong>$motif_nom_fichier</strong>.<br />
Vous seriez-vous trompé de fichier&nbsp;?</span>";
		}
		echo "</p>\n";

		$sql="TRUNCATE tempo2_sso;";
		$menage=mysqli_query($GLOBALS["mysqli"], $sql);

		echo creer_div_infobulle("div_search","Formulaire de recherche dans la table 'eleves'","","<p>Saisir une portion du nom à rechercher...</p>
<form name='recherche' action='".$_SERVER['PHP_SELF']."' method='post'>
".add_token_field(true)."
<input type='hidden' name='login_recherche' id='login_recherche' value='' />
<table border='0' summary='Recherche'>
	<tr>
		<th>Nom: </th>
		<td><input type='text' name='nom_rech' id='nom_rech' value='' onBlur='recherche_utilisateur(\"eleve\")' /></td>
		<td rowspan='2'><input type='button' name='chercher' value='Chercher' onClick='recherche_utilisateur(\"eleve\")' /></td>
	</tr>
	<tr>
		<th>Prénom: </th>
		<td><input type='text' name='prenom_rech' id='prenom_rech' value='' onBlur='recherche_utilisateur(\"eleve\")' /></td>
	</tr>
</table>
</form>

<div id='div_resultat' style='margin: 1px;'></div>

","",32,0,"y","y","n","n");

		echo "
<h2>Rapprochement des comptes élèves ENT ITOP/GEPI</h2>

<form action='".$_SERVER['PHP_SELF']."' method='post' name='form_import' enctype='multipart/form-data'>
".add_token_field()."
<input type='hidden' name='mode' value='import_eleves' />
<input type='hidden' name='enregistrement_eleves' value='y' />
<input type='hidden' name='temoin_suhosin_1' value='eleve' />

<table class='boireaus'>
	<tr>
		<th rowspan='2'>
			Enregistrer
			<span id='tout_cocher_decocher' style='display:none;'>
				<br />
				<a href=\"javascript:tout_cocher()\" title='Tout cocher'><img src='../images/enabled.png' width='20' height='20' /></a>
				/
				<a href=\"javascript:tout_decocher()\" title='Tout décocher'><img src='../images/disabled.png' width='20' height='20' /></a>
			</span>
		</th>
		<th colspan='6'>Informations ENT</th>
		<th rowspan='2'>Login Gepi</th>
	</tr>
	<tr>
		<th>Guid</th>
		<th>Nom</th>
		<th>Prénom</th>
		<th>Profil</th>
		<th>Classe</th>
		<th>Naissance</th>
	</tr>
";

		//$tab_login_associe_a_un_guid=array();
		$tab_guid_associe_a_un_login=array();
		$sql="SELECT u.statut, u.nom, u.prenom, stc.* FROM sso_table_correspondance stc, utilisateurs u WHERE stc.login_gepi=u.login;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig=mysqli_fetch_object($res)) {
			//$tab_login_associe_a_un_guid[$lig->login_sso]['login_gepi']=$lig->login_gepi;
			//$tab_login_associe_a_un_guid[$lig->login_sso]['info']=$lig->nom." ".$lig->prenom." (".$lig->statut.")";

			$tab_guid_associe_a_un_login[$lig->login_gepi]['login_sso']=$lig->login_sso;
			$tab_guid_associe_a_un_login[$lig->login_gepi]['info']=$lig->nom." ".$lig->prenom." (".$lig->statut.")";
		}

		$alt=1;
		$cpt=0;
		$cpt_deja_enregistres=0;
		$tab_nom_prenom_deja_aff=array();
		$tab_doublon_possible=array();
		while (!feof($fp)) {
			$ligne = trim(fgets($fp, 4096));
			if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
				$ligne=substr($ligne,3);
			}

			if($ligne!='') {
				$tab=explode(";", ensure_utf8($ligne));
				if(!preg_match("/^Guid;/i", trim($ligne))) {

					// Si un élève a déjà une association enregistrée, ne pas proposer de refaire l'association.
					// Juste stocker ce qui est déjà associé et l'afficher dans un 2è tableau.
					// Pouvoir supprimer une association du 2è tableau (ou voir dans mode=consult_eleves)

					$sql="SELECT login_gepi FROM sso_table_correspondance WHERE login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)>0) {
						$cpt_deja_enregistres++;
					}
					elseif((!isset($_POST['exclure_classes_anormales']))||(!preg_match("/BASE20/", $tab[4]))) {
						$naissance=(isset($tab[5])) ? $tab[5] : "";
						if(!preg_match("#[0-9]{2}/[0-9]{2}/[0-9]{4}#", $naissance)) {$naissance="";}

						if(in_array_i($tab[1]." ".$tab[2], $tab_nom_prenom_deja_aff)) {
							$chaine_tmp=$cpt."_".remplace_accents($tab[1]." ".$tab[2],"all");
							$tab_doublon_possible[]=$chaine_tmp;
							$ancre_doublon_ou_pas="<a name='doublon_possible_$chaine_tmp'></a>";
							$style_css=" style='background-color:red' title=\"Il existe au moins un homonyme dans le CSV.
Si les homonymes correspondent à un même élève, vous allez devoir identifier le bon GUID
(choisir un des homonymes au hasard, et demander à l'élève de tester.
si cela ne fonctionne pas, corriger l'association élève en mettant le GUID de l'homonyme).\"";
						}
						else {
							$ancre_doublon_ou_pas="";
							$style_css="";
						}
						$tab_nom_prenom_deja_aff[]=$tab[1]." ".$tab[2];

						$alt=$alt*(-1);
						echo "
	<tr class='lig$alt white_hover'$style_css>
		<td><input type='checkbox' name='ligne[]' id='ligne_$cpt' value='$cpt' onchange=\"change_graisse($cpt)\" />$ancre_doublon_ou_pas</td>
		<td><label for='ligne_$cpt'>".$tab[0]."</label></td>
		<td><label for='ligne_$cpt'><span id='nom_$cpt'>".$tab[1]."</span></label></td>
		<td><label for='ligne_$cpt'><span id='prenom_$cpt'>".$tab[2]."</span></label></td>
		<td><label for='ligne_$cpt'>".$tab[3]."</label></td>
		<td><label for='ligne_$cpt'>".preg_replace("/".getSettingValue('gepiSchoolRne')."\\$/", "", $tab[4])."</label></td>
		<td><label for='ligne_$cpt'>".$naissance."</label></td>";

						// Recherche dans la table eleves de personnes pouvant correspondre à la ligne courante du CSV.
						if($naissance!="") {
							$sql="SELECT * FROM eleves WHERE nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[1]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' AND prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[2]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' AND naissance='".formate_date2($naissance, "jj/mm/aaaa", "aaaammjj")."'";
						}
						else {
							$sql="SELECT * FROM eleves WHERE nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[1]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' AND prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[2]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' ORDER BY naissance;";
						}
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)==1) {
							// Un seul élève correspond
							$lig=mysqli_fetch_object($res);

							echo "
		<td title=\"$lig->nom $lig->prenom ".formate_date($lig->naissance)."\">
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>
			$lig->login";

							// On vérifie quand même la classe?
							$tab_classe=get_class_from_ele_login($lig->login);
							if(isset($tab_classe['liste_nbsp'])) {
								echo " (<em>".$tab_classe['liste_nbsp']."</em>)";
							}
							else {
								echo " (<em style='color:red'>Aucune classe???</em>)";
							}

							echo "
			<span id='saisie_$cpt'></span>

			<a href='#' onClick=\"document.getElementById('nom_rech').value=document.getElementById('nom_$cpt').innerHTML;
									document.getElementById('prenom_rech').value=document.getElementById('prenom_$cpt').innerHTML;
									document.getElementById('login_recherche').value='login_$cpt';
									document.getElementById('div_resultat').innerHTML='';
									afficher_div('div_search','y',-400,20);
									if(!document.getElementById('login_$cpt')) {ajout_champ_saisie_login($cpt);};
									return false;\">
				<img src='../images/icons/chercher.png' width='16' height='16' alt='Chercher' title='Effectuer une recherche' />
			</a>";

							//<!-- 20130912 : AJOUTER UN TEST SUR LE FAIT QUE LE LOGIN EST DEJA ASSOCIE -->
							if(array_key_exists($lig->login, $tab_guid_associe_a_un_login)) {
								echo "<img src='../images/icons/ico_attention.png' width='22' height='19' title=\"Ce login est associé à un autre GUID ENT\n".$tab_guid_associe_a_un_login[$lig->login]['login_sso']."\net concerne l'utilisateur Gepi ".$tab_guid_associe_a_un_login[$lig->login]['info']."\" />";
							}

							echo "
		</td>
	</tr>
";
						}
						else {
							// Plusieurs élèves correspondent
							// Il va falloir choisir
							$chaine_options="";
							while($lig=mysqli_fetch_object($res)) {
								$chaine_options.="				<option value=\"$lig->login\"";
								// 20130912
								if(array_key_exists($lig->login, $tab_guid_associe_a_un_login)) {
									$chaine_options.=" title=\"Ce login est associé à un autre GUID ENT\n".$tab_guid_associe_a_un_login[$lig->login]['login_sso']."\net concerne l'utilisateur Gepi ".$tab_guid_associe_a_un_login[$lig->login]['info']."\"";
								}
								$chaine_options.=">$lig->nom $lig->prenom (".formate_date($lig->naissance).")";
								$tab_classe=get_class_from_ele_login($lig->login);
								if(isset($tab_classe['liste'])) {
									$chaine_options.=" en ".$tab_classe['liste'];
								}
								$chaine_options.="</option>\n";
							}

							if($chaine_options!="") {
								echo "
		<td>
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>

			<span id='saisie_$cpt'>
			<select name='login_$cpt' id='login_$cpt' onchange=\"if(document.getElementById('login_$cpt').options[if(document.getElementById('login_$cpt').selectedIndex].value!='') {document.getElementById('ligne_$cpt').checked=true;} else{document.getElementById('ligne_$cpt').checked=false;}\">
				<option value=''>---</option>
				$chaine_options
			</select>
			</span>

			<a href='#' onClick=\"document.getElementById('nom_rech').value=document.getElementById('nom_$cpt').innerHTML;
									document.getElementById('prenom_rech').value=document.getElementById('prenom_$cpt').innerHTML;
									document.getElementById('login_recherche').value='login_$cpt';
									document.getElementById('div_resultat').innerHTML='';
									afficher_div('div_search','y',-400,20);
									if(!document.getElementById('login_$cpt')) {ajout_champ_saisie_login($cpt);};
									return false;\">
				<img src='../images/icons/chercher.png' width='16' height='16' alt='Chercher' title='Effectuer une recherche' />
			</a>
		</td>
	</tr>
";
							}
							else {
								echo "
		<td>";
								if($module_suhosin_actif!="y") {
									echo "
			<input type='text' name='login_$cpt' id='login_$cpt' value=\"\" onchange=\"if(document.getElementById('login_$cpt').value!='') {document.getElementById('ligne_$cpt').checked=true;} else{document.getElementById('ligne_$cpt').checked=false;}\" />";
								}
								else {
									echo "
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>

			<span id='saisie_$cpt'></span>";
								}

								// On renseigne le formulaire de recherche avec le nom et le prénom:
								// ...
								// On fait le nettoyage pour ne pas laisser les traces d'une précédente requête:
								echo "
			<a href='#' onClick=\"document.getElementById('nom_rech').value=document.getElementById('nom_$cpt').innerHTML;
									document.getElementById('prenom_rech').value=document.getElementById('prenom_$cpt').innerHTML;
									document.getElementById('login_recherche').value='login_$cpt';
									document.getElementById('div_resultat').innerHTML='';
									afficher_div('div_search','y',-400,20);
									if(!document.getElementById('login_$cpt')) {ajout_champ_saisie_login($cpt);};
									return false;\">
				<img src='../images/icons/chercher.png' width='16' height='16' alt='Chercher' title='Effectuer une recherche' />
			</a>
		</td>
	</tr>
";
							}
						}

						$sql="INSERT INTO tempo2_sso SET col1='$cpt', col2='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $ligne) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							echo "<span style='color:red'>ERREUR</span>";
						}
						$cpt++;
					}
				}
			}
		}

		echo "
</table>
<input type='hidden' name='temoin_suhosin_2' value='eleve' />
<input type='submit' id='bouton_submit_import' value='Enregistrer' />
<input type='button' id='bouton_button_import' value='Enregistrer' style='display:none;' onclick='verif_puis_submit()' />
</form>
";

		if(count($tab_doublon_possible)>0) {
			echo "<p style='text-indent:-8em; margin-left:8em;'><strong style='color:red'>ATTENTION&nbsp:</strong> Un ou des doublons possibles ont été repérés.<br />
Veuillez contrôler manuellement s'il s'agit ou non de doublons&nbsp;:<br />";
			for($loop=0;$loop<count($tab_doublon_possible);$loop++) {
				echo "<a href='#doublon_possible_".$tab_doublon_possible[$loop]."'>".$tab_doublon_possible[$loop]."</a><br />\n";
			}
			echo "
</p>\n";
		}

		echo "<p>$cpt ligne(s) affichée(s).</p>\n";

		if($cpt_deja_enregistres>0) {
			echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=consult_eleves'>$cpt_deja_enregistres association(s) élève(s) déjà enregistrée(s)</a>.<br />\n";
		}
		else {
			echo "<p>Aucune association élève n'est encore enregistrée.<br />\n";
		}

		echo "
<p style='color:red'>A FAIRE : Pouvoir trier par classe</p>

<script type='text/javascript'>
	document.getElementById('tout_cocher_decocher').style.display='';
	document.getElementById('bouton_button_import').style.display='';
	document.getElementById('bouton_submit_import').style.display='none';

	function tout_cocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				document.getElementById('ligne_'+i).checked=true;
				change_graisse(i);
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				document.getElementById('ligne_'+i).checked=false;
				change_graisse(i);
			}
		}
	}

	function ajout_champ_saisie_login(num) {
		if(document.getElementById('saisie_'+num)) {
			document.getElementById('saisie_'+num).innerHTML='<input type=\"text\" name=\"login_'+num+'\" id=\"login_'+num+'\" value=\"\" onchange=\"if(document.getElementById(\'login_'+num+'\').value!=\'\') {document.getElementById(\'ligne_'+num+'\').checked=true;} else {document.getElementById(\'ligne_'+num+'\').checked=false;}\" />';
		}
	}

	function verif_puis_submit() {
		temoin='n';
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				if(document.getElementById('ligne_'+i).checked==true) {
					temoin='y';
					break;
				}
			}
		}
		if(temoin=='y') {
			document.forms['form_import'].submit();
		}
		else {
			alert('Vous n avez rien coché!?');
		}
	}

	function change_graisse(num) {
		if((document.getElementById('ligne_'+num))&&(document.getElementById('nom_'+num))&&(document.getElementById('prenom_'+num))) {
			if(document.getElementById('ligne_'+num).checked==true) {
				document.getElementById('nom_'+num).style.fontWeight='bold';
				document.getElementById('prenom_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('nom_'+num).style.fontWeight='normal';
				document.getElementById('prenom_'+num).style.fontWeight='normal';
			}
		}
	}
</script>
";
		// En fin d'enregistrement, renvoyer vers consult_eleves pour afficher les associations
		// Compter/afficher ce total des associations avant... et après dans consult_eleves
	}
	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="consult_eleves") {
	echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
 | <a href='".$_SERVER['PHP_SELF']."?mode=import_eleves'>Importer un CSV élève</a>
</p>

<h2>Rapprochement actuels des comptes élèves ENT ITOP/GEPI</h2>
";

	$sql="SELECT e.*, s.* FROM eleves e, sso_table_correspondance s WHERE s.login_gepi=e.login ORDER BY e.nom, e.prenom";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucun rapprochement élève n'est enregistré.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo "
<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<p>Les rapprochements enregistrés (".mysqli_num_rows($res).") sont les suivants&nbsp;:</p>
	".add_token_field()."
	<input type='hidden' name='mode' value='consult_eleves' />
	<input type='hidden' name='temoin_suhosin_1' value='eleve' />

	<table class='boireaus'>
		<tr>
			<th rowspan='2'>
				<input type='submit' value='Supprimer' />
				<span id='tout_cocher_decocher' style='display:none;'>
					<br />
					<a href=\"javascript:tout_cocher()\" title='Tout cocher'><img src='../images/enabled.png' width='20' height='20' /></a>
					/
					<a href=\"javascript:tout_decocher()\" title='Tout décocher'><img src='../images/disabled.png' width='20' height='20' /></a>
				</span>
			</th>
			<th>Informations ENT</th>
			<th colspan='6'>Informations GEPI</th>
			<th rowspan='2'>Corriger</th>
		</tr>
		<tr>
			<th>Guid</th>
			<th>Login</th>
			<th title=\"Pour une connexion via un ENT, le champ auth_mode doit en principe avoir pour valeur 'sso'\">Auth_mode</th>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Classe</th>
			<th>Naissance</th>
		</tr>
";

	$cpt=0;
	$alt=1;
	while($lig=mysqli_fetch_object($res)) {
		$tab_classe=get_class_from_ele_login($lig->login);
		$classe=isset($tab_classe['liste_nbsp']) ? $tab_classe['liste_nbsp'] : "";

		$alt=$alt*(-1);
		echo "
		<tr class='lig$alt white_hover'>
			<td><input type='checkbox' name='suppr[]' id='suppr_$cpt' value=\"$lig->login\" onchange=\"change_graisse($cpt)\" /></td>
			<td><label for='suppr_$cpt'>$lig->login_sso</label></td>
			<td><label for='suppr_$cpt'>$lig->login</label></td>
			<td>";
		$sql="SELECT auth_mode, etat FROM utilisateurs WHERE login='".$lig->login."' AND statut='eleve';";
		$test_u=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test_u)==0) {
			echo "<a href='../utilisateurs/create_eleve.php?filtrage=Afficher&amp;critere_recherche=".preg_replace("/[^A-Za-z]/", "%", $lig->nom)."&amp;afficher_tous_les_eleves=n' title='Pas de compte utilisateur dans Gepi pour cet élève.
Créer le compte?' target='_blank'>-</a>";
		}
		else {
			echo "<label for='suppr_$cpt'>";
			$lig_u=mysqli_fetch_object($test_u);
			if($lig_u->etat=='actif') {
				echo "<div style='float:right;width:16px;'><img src='../images/icons/buddy.png' width='16' height='16' title='Compte actif' /></div>\n";
			}
			else {
				echo "<div style='float:right;width:16px;'><img src='../images/icons/buddy_no.png' width='16' height='16' title='Compte inactif' /></div>\n";
			}
			echo $lig_u->auth_mode;
			echo "</label>";
		}
		echo "</td>
			<td><label for='suppr_$cpt'><span id='nom_$cpt'>$lig->nom</span></label></td>
			<td><label for='suppr_$cpt'><span id='prenom_$cpt'>$lig->prenom</span></label></td>
			<td><label for='suppr_$cpt'>$classe</label></td>
			<td><label for='suppr_$cpt'>".formate_date($lig->naissance)."</label></td>
			<td><a href='".$_SERVER['PHP_SELF']."?login_gepi=$lig->login_gepi&amp;login_sso=$lig->login_sso&amp;mode=saisie_manuelle'><img src='../images/edit16.png' width='16' height='16' title=\"Corriger l'association\" /></label></td>
		</tr>
";
		$cpt++;
	}

	echo "
	</table>
	<input type='hidden' name='temoin_suhosin_2' value='eleve' />
</form>

<script type='text/javascript'>
	document.getElementById('tout_cocher_decocher').style.display='';

	function tout_cocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=true;
				change_graisse(i);
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=false;
				change_graisse(i);
			}
		}
	}

	function change_graisse(num) {
		if((document.getElementById('suppr_'+num))&&(document.getElementById('nom_'+num))&&(document.getElementById('prenom_'+num))) {
			if(document.getElementById('suppr_'+num).checked==true) {
				document.getElementById('nom_'+num).style.fontWeight='bold';
				document.getElementById('prenom_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('nom_'+num).style.fontWeight='normal';
				document.getElementById('prenom_'+num).style.fontWeight='normal';
			}
		}
	}
</script>
";

	require("../lib/footer.inc.php");
	die();
}
//==================================================================================

if($mode=="import_responsables") {
	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
</p>";

	if(!isset($csv_file)) {
		echo "
<h2>Rapprochement des comptes responsables ENT ITOP/GEPI</h2>

<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		".add_token_field()."
		<input type='hidden' name='mode' value='import_responsables' />
		<input type=\"file\" size=\"65\" name=\"csv_file\" style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /><br />
		<input type='submit' value='Envoyer' />
	</fieldset>
</form>

<p><br /></p>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li>Le fichier CSV attendu doit avoir le format suivant&nbsp;:<br />
	﻿Guid;Nom;Prénom;Profil;Classes;Groupe;Guid_Enfant1;Guid_Enfant2;Guid_Enfant3<br />
	f7ebe441-14e0-4c48-b9ec-53e603829fb3;DISSOIR;Amar;National_2;;;f73d0f72-0958-4b8f-85f7-a58a96d95220<br />
	...</li>
	<li>Il est recommandé d'envoyer une première fois le CSV, d'enregistrer les associations correctement détectées (<em>en contrôlant tout de même les éventuels doublons repérés</em>).<br />
	Puis, envoyer à nouveau le même fichier pour traiter les indéterminés restants.<br />
	Le deuxième envoi permet aussi de repérer ce qui n'a pas été enregistré au premier envoi.</li>
</ul>\n";
	}
	else {
		check_token(false);
		$fp=fopen($csv_file['tmp_name'],"r");

		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$motif_nom_fichier="ExportSSO_Parent_";
		echo "<p>Le fichier fourni se nomme <strong>".$csv_file['name']."</strong>";
		if(!preg_match("/$motif_nom_fichier/", $csv_file['name'])) {
			echo "<br />
<span style='color:red'>Le nom du fichier contient habituellement la chaine <strong>$motif_nom_fichier</strong>.<br />
Vous seriez-vous trompé de fichier&nbsp;?</span>";
		}
		echo "</p>\n";


		// 20131014
		// ﻿Guid;Nom;Prénom;Profil;Classes;Groupe;Guid_Enfant1;Guid_Enfant2;Guid_Enfant3

		// Lire la ligne d'entête pour repérer les indices des colonnes recherchées
		$tabchamps = array("Guid", "Nom", "Prénom", "Prenom", "Profil", "Groupe", "Guid_Enfant1", "Guid_Enfant2", "Guid_Enfant3");

		// Lecture de la ligne 1 et la mettre dans $temp
		$ligne_entete=trim(fgets($fp,4096));
		//echo "$ligne_entete<br />";
		$en_tete=explode(";", $ligne_entete);

		$tabindice=array();

		// On range dans tabindice les indices des champs retenus
		for ($k = 0; $k < count($tabchamps); $k++) {
			//echo "<br /><p style='text-indent:-4em;margin-left:4em'>Recherche du champ ".$tabchamps[$k]."<br />";
			for ($i = 0; $i < count($en_tete); $i++) {
				//echo "\$en_tete[$i]=$en_tete[$i]<br />";
				//echo casse_mot(remplace_accents($en_tete[$i]),'min')."<br />";
				//echo casse_mot(remplace_accents($tabchamps[$k]), 'min')."<br />";
				if (casse_mot(remplace_accents($en_tete[$i]),'min') == casse_mot(remplace_accents($tabchamps[$k]), 'min')) {
					$tabindice[$tabchamps[$k]] = $i;
					//echo "\$tabindice[$tabchamps[$k]]=$i<br />";
				}
			}
		}
		if((!isset($tabindice['Nom']))||((!isset($tabindice['Prénom']))&&(!isset($tabindice['Prenom'])))||(!isset($tabindice['Guid']))||(!isset($tabindice['Guid_Enfant1']))||(!isset($tabindice['Profil']))) {
			echo "<p style='color:red'>La ligne d'entête ne comporte pas un des champs indispensables (<em>Guid, Nom, Prénom, Profil, Guid_enfant1</em>).</p>";
			require("../lib/footer.inc.php");
			die();
		}

		if(!isset($tabindice['Prénom'])) {
			$tabindice['Prénom']=$tabindice['Prenom'];
		}

		$sql="TRUNCATE tempo2_sso;";
		$menage=mysqli_query($GLOBALS["mysqli"], $sql);

		$sql="INSERT INTO tempo2_sso SET col1='Ligne_entete', col2='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $ligne_entete) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);

		echo creer_div_infobulle("div_search","Formulaire de recherche dans la table 'resp_pers'","","<p>Saisir une portion du nom à rechercher...</p>
<form name='recherche' action='".$_SERVER['PHP_SELF']."' method='post'>
".add_token_field(true)."
<input type='hidden' name='login_recherche' id='login_recherche' value='' />
<table border='0' summary='Recherche'>
	<tr>
		<th>Nom: </th>
		<td><input type='text' name='nom_rech' id='nom_rech' value='' onBlur='recherche_utilisateur(\"responsable\")' /></td>
		<td rowspan='2'><input type='button' name='chercher' value='Chercher' onClick='recherche_utilisateur(\"responsable\")' /></td>
	</tr>
	<tr>
		<th>Prénom: </th>
		<td><input type='text' name='prenom_rech' id='prenom_rech' value='' onBlur='recherche_utilisateur(\"responsable\")' /></td>
	</tr>
</table>
</form>

<div id='div_resultat' style='margin: 1px;'></div>

","",32,0,"y","y","n","n");

		echo "
<h2>Rapprochement des comptes responsables ENT ITOP/GEPI</h2>

<form action='".$_SERVER['PHP_SELF']."' method='post' name='form_import' enctype='multipart/form-data'>
".add_token_field()."
<input type='hidden' name='mode' value='import_responsables' />
<input type='hidden' name='enregistrement_responsables' value='y' />
<input type='hidden' name='temoin_suhosin_1' value='responsable' />

<table class='boireaus'>
	<tr>
		<th rowspan='2'>
			Enregistrer
			<span id='tout_cocher_decocher' style='display:none;'>
				<br />
				<a href=\"javascript:tout_cocher()\" title='Tout cocher'><img src='../images/enabled.png' width='20' height='20' /></a>
				/
				<a href=\"javascript:cocher_tous_les_resp_avec_login_et_eleve_associe()\" title='Cocher les responsables avec login et élève associé'><img src='../images/icons/wizard.png' width='20' height='20' /></a>
				/
				<a href=\"javascript:tout_decocher()\" title='Tout décocher'><img src='../images/disabled.png' width='20' height='20' /></a>
			</span>
		</th>
		<th colspan='6'>Informations ENT</th>
		<th rowspan='2'>Login Gepi</th>
	</tr>
	<tr>
		<th>Guid</th>
		<th>Nom</th>
		<th>Prénom</th>
		<th>Profil</th>
		<th>Groupe</th>
		<th>Enfants</th>
	</tr>
";

		//$tab_login_associe_a_un_guid=array();
		$tab_guid_associe_a_un_login=array();
		$sql="SELECT u.statut, u.nom, u.prenom, stc.* FROM sso_table_correspondance stc, utilisateurs u WHERE stc.login_gepi=u.login;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig=mysqli_fetch_object($res)) {
			//$tab_login_associe_a_un_guid[$lig->login_sso]['login_gepi']=$lig->login_gepi;
			//$tab_login_associe_a_un_guid[$lig->login_sso]['info']=$lig->nom." ".$lig->prenom." (".$lig->statut.")";

			$tab_guid_associe_a_un_login[$lig->login_gepi]['login_sso']=$lig->login_sso;
			$tab_guid_associe_a_un_login[$lig->login_gepi]['info']=$lig->nom." ".$lig->prenom." (".$lig->statut.")";
		}

		$alt=1;
		$cpt=0;
		$cpt_deja_enregistres=0;
		$tab_nom_prenom_deja_aff=array();
		$tab_doublon_possible=array();
		$chaine_resp_avec_login_et_eleve_associe="";
		while (!feof($fp)) {
			$ligne = trim(fgets($fp, 4096));
			if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
				$ligne=substr($ligne,3);
			}

			if($ligne!='') {
				$tab=explode(";", ensure_utf8($ligne));
				if(!preg_match("/^Guid;/i", trim($ligne))) {

					// Si un élève a déjà une association enregistrée, ne pas proposer de refaire l'association.
					// Juste stocker ce qui est déjà associé et l'afficher dans un 2è tableau.
					// Pouvoir supprimer une association du 2è tableau (ou voir dans mode=consult_eleves)

					$sql="SELECT login_gepi FROM sso_table_correspondance WHERE login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[$tabindice['Guid']]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)>0) {
						$cpt_deja_enregistres++;
					}
					else {
/*
// 20131014
echo "<tr>
<td><pre>";
print_r($tab);
echo "</pre></td>
</tr>";
*/
						$guid_courant=$tab[$tabindice['Guid']];
						$nom_courant=$tab[$tabindice['Nom']];
						$prenom_courant=$tab[$tabindice['Prénom']];
						$profil_courant=$tab[$tabindice['Profil']];

						$groupe_courant="";
						if(isset($tab[$tabindice['Groupe']])) {
							$groupe_courant=$tab[$tabindice['Groupe']];
						}

						$guid_enfant1="";
						$guid_enfant2="";
						$guid_enfant3="";
						if(isset($tab[$tabindice['Guid_Enfant1']])) {
							$guid_enfant1=$tab[$tabindice['Guid_Enfant1']];
						}
						if(isset($tab[$tabindice['Guid_Enfant2']])) {
							$guid_enfant2=$tab[$tabindice['Guid_Enfant2']];
						}
						if(isset($tab[$tabindice['Guid_Enfant3']])) {
							$guid_enfant3=$tab[$tabindice['Guid_Enfant3']];
						}

						if(in_array_i($nom_courant." ".$prenom_courant, $tab_nom_prenom_deja_aff)) {
							$chaine_tmp=$cpt."_".remplace_accents($nom_courant." ".$prenom_courant,"all");
							$tab_doublon_possible[]=$chaine_tmp;
							$ancre_doublon_ou_pas="<a name='doublon_possible_$chaine_tmp'></a>";
							$style_css=" style='background-color:red' title=\"Il existe au moins un homonyme dans le CSV.
Si les homonymes correspondent à un même élève, vous allez devoir identifier le bon GUID
(choisir un des homonymes au hasard, et demander à l'élève de tester.
si cela ne fonctionne pas, corriger l'association élève en mettant le GUID de l'homonyme).\"";
						}
						else {
							$ancre_doublon_ou_pas="";
							$style_css="";
						}
						$tab_nom_prenom_deja_aff[]=$nom_courant." ".$prenom_courant;

						$alt=$alt*(-1);
						echo "
	<tr class='lig$alt white_hover'$style_css>
		<td><input type='checkbox' name='ligne[]' id='ligne_$cpt' value='$cpt' onchange=\"change_graisse($cpt)\" />$ancre_doublon_ou_pas</td>
		<td><label for='ligne_$cpt'>".$guid_courant."</label></td>
		<td><label for='ligne_$cpt'><span id='nom_$cpt'>".$nom_courant."</span></label></td>
		<td><label for='ligne_$cpt'><span id='prenom_$cpt'>".$prenom_courant."</span></label></td>
		<td><label for='ligne_$cpt'>".$profil_courant."</label></td>
		<td><label for='ligne_$cpt'>".$groupe_courant."</label></td>
		<td><label for='ligne_$cpt'>";

						$chaine="";
						if($guid_enfant1!="") {
							$chaine.="s.login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $guid_enfant1) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."'";
						}
						if($guid_enfant2!="") {
							if($chaine!="") {$chaine.=" OR ";}
							$chaine.="s.login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $guid_enfant2) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."'";
						}
						if($guid_enfant3!="") {
							if($chaine!="") {$chaine.=" OR ";}
							$chaine.="s.login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $guid_enfant3) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."'";
						}

						$temoin_eleve_associe="n";
						$tab_resp=array();
						$tab_resp_login=array();
						$cpt_resp=0;
						// Si la chaine est vide, proposer un champ TEXT
						if($chaine!="") {
							$sql="SELECT e.* FROM eleves e, sso_table_correspondance s WHERE ($chaine) AND e.login=s.login_gepi ORDER BY e.nom, e.prenom;";
							echo_debug_itop("$sql<br />");
							$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_ele)>0) {
								$temoin_eleve_associe="y";
								$cpt_ele=0;
								while($lig_ele=mysqli_fetch_object($res_ele)) {
									if($cpt_ele>0) {echo "<br />";}
									echo $lig_ele->nom." ".$lig_ele->prenom;
									$tab_classe=get_class_from_ele_login($lig_ele->login);
									if(isset($tab_classe['liste_nbsp'])) {echo " (<em>".$tab_classe['liste_nbsp']."</em>)";}

									$sql="SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r WHERE r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' AND rp.nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $nom_courant) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' AND rp.prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $prenom_courant) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' AND rp.login!='';";
									echo_debug_itop("$sql<br />");
									$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_resp)>0) {
										while($lig_resp=mysqli_fetch_object($res_resp)) {
											if(!in_array($lig_resp->login, $tab_resp_login)) {
												$tab_resp_login[]=$lig_resp->login;
												$tab_resp[$cpt_resp]['login']=$lig_resp->login;
												$tab_resp[$cpt_resp]['civilite']=$lig_resp->civilite;
												$tab_resp[$cpt_resp]['nom']=$lig_resp->nom;
												$tab_resp[$cpt_resp]['prenom']=$lig_resp->prenom;
												$tab_resp[$cpt_resp]['resp_legal']=$lig_resp->resp_legal;
												$tab_resp[$cpt_resp]['info']=$lig_resp->civilite." ".casse_mot($lig_resp->nom,'maj')." ".casse_mot($lig_resp->prenom,'majf2');
												$tab_resp[$cpt_resp]['info'].=" (N° ".$lig_resp->pers_id.")";

												$tab_resp[$cpt_resp]['info'].=" (Légal ".$lig_resp->resp_legal.")";
												$cpt_resp++;
											}
										}
									}
									else {
										$sql="SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r WHERE r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' AND rp.login!='';";
										$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_resp)>0) {
											while($lig_resp=mysqli_fetch_object($res_resp)) {
												if(!in_array($lig_resp->login, $tab_resp_login)) {
													$tab_resp_login[]=$lig_resp->login;
													$tab_resp[$cpt_resp]['login']=$lig_resp->login;
													$tab_resp[$cpt_resp]['civilite']=$lig_resp->civilite;
													$tab_resp[$cpt_resp]['nom']=$lig_resp->nom;
													$tab_resp[$cpt_resp]['prenom']=$lig_resp->prenom;
													$tab_resp[$cpt_resp]['resp_legal']=$lig_resp->resp_legal;
													$tab_resp[$cpt_resp]['info']=$lig_resp->civilite." ".casse_mot($lig_resp->nom,'maj')." ".casse_mot($lig_resp->prenom,'majf2');
													$tab_resp[$cpt_resp]['info'].=" (N° ".$lig_resp->pers_id.")";
													$tab_resp[$cpt_resp]['info'].=" (Légal ".$lig_resp->resp_legal.")";
													$cpt_resp++;
												}
											}
										}
									}
									$cpt_ele++;
								}
							}
						}
						echo "</label></td>\n";

						if(count($tab_resp)==0) {
							echo "
		<td>";
								if($module_suhosin_actif!="y") {
									echo "
			<input type='text' name='login_$cpt' id='login_$cpt' value=\"\" onchange=\"if(document.getElementById('login_$cpt').value!='') {document.getElementById('ligne_$cpt').checked=true;} else{document.getElementById('ligne_$cpt').checked=false;}\" />";
								}
								else {
									echo "
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>

			<span id='saisie_$cpt'></span>";
								}

								// On renseigne le formulaire de recherche avec le nom et le prénom:
								// ...
								// On fait le nettoyage pour ne pas laisser les traces d'une précédente requête:
								echo "
			<a href='#' onClick=\"document.getElementById('nom_rech').value=document.getElementById('nom_$cpt').innerHTML;
									document.getElementById('prenom_rech').value=document.getElementById('prenom_$cpt').innerHTML;
									document.getElementById('login_recherche').value='login_$cpt';
									document.getElementById('div_resultat').innerHTML='';
									afficher_div('div_search','y',-400,20);
									if(!document.getElementById('login_$cpt')) {ajout_champ_saisie_login($cpt);};
									return false;\">
				<img src='../images/icons/chercher.png' width='16' height='16' alt='Chercher' title='Effectuer une recherche' />
			</a>
		</td>
	</tr>
";
						}
						elseif(count($tab_resp)==1) {
							echo "
		<td title=\"".$tab_resp[0]['info']."\">
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>
			".$tab_resp[0]['login']."
			<span id='saisie_$cpt'></span>

			<a href='#' onClick=\"document.getElementById('nom_rech').value=document.getElementById('nom_$cpt').innerHTML;
									document.getElementById('prenom_rech').value=document.getElementById('prenom_$cpt').innerHTML;
									document.getElementById('login_recherche').value='login_$cpt';
									document.getElementById('div_resultat').innerHTML='';
									afficher_div('div_search','y',-400,20);
									if(!document.getElementById('login_$cpt')) {ajout_champ_saisie_login($cpt);};
									return false;\">
				<img src='../images/icons/chercher.png' width='16' height='16' alt='Chercher' title='Effectuer une recherche' />
			</a>";

							//<!-- 20130912 : AJOUTER UN TEST SUR LE FAIT QUE LE LOGIN EST DEJA ASSOCIE -->
							if(array_key_exists($tab_resp[0]['login'], $tab_guid_associe_a_un_login)) {
								echo "<img src='../images/icons/ico_attention.png' width='22' height='19' title=\"Ce login est associé à un autre GUID ENT\n".$tab_guid_associe_a_un_login[$tab_resp[0]['login']]['login_sso']."\net concerne l'utilisateur Gepi ".$tab_guid_associe_a_un_login[$tab_resp[0]['login']]['info']."\" />";
							}

							echo "
		</td>
	</tr>
";
							if($temoin_eleve_associe=="y") {
								if($chaine_resp_avec_login_et_eleve_associe!="") {
									$chaine_resp_avec_login_et_eleve_associe.=", ";
								}
								$chaine_resp_avec_login_et_eleve_associe.=$cpt;
							}
						}
						else {
							$chaine_options="";
							for($loop=0;$loop<count($tab_resp);$loop++) {
								$chaine_options.="				<option value=\"".$tab_resp[$loop]['login']."\"";
								// 20130912
								if(array_key_exists($tab_resp[0]['login'], $tab_guid_associe_a_un_login)) {
									$chaine_options.=" title=\"Ce login est associé à un autre GUID ENT\n".$tab_guid_associe_a_un_login[$tab_resp[0]['login']]['login_sso']."\net concerne l'utilisateur Gepi ".$tab_guid_associe_a_un_login[$tab_resp[0]['login']]['info']."\"";
								}
								$chaine_options.=">".$tab_resp[$loop]['info']."</option>\n";
							}
							echo "
		<td>
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>

			<span id='saisie_$cpt'>
			<select name='login_$cpt' id='login_$cpt' onchange=\"if(document.getElementById('login_$cpt').options[if(document.getElementById('login_$cpt').selectedIndex].value!='') {document.getElementById('ligne_$cpt').checked=true;} else{document.getElementById('ligne_$cpt').checked=false;}\">
				<option value=''>---</option>
				$chaine_options
			</select>
			</span>

			<a href='#' onClick=\"document.getElementById('nom_rech').value=document.getElementById('nom_$cpt').innerHTML;
									document.getElementById('prenom_rech').value=document.getElementById('prenom_$cpt').innerHTML;
									document.getElementById('login_recherche').value='login_$cpt';
									document.getElementById('div_resultat').innerHTML='';
									afficher_div('div_search','y',-400,20);
									if(!document.getElementById('login_$cpt')) {ajout_champ_saisie_login($cpt);};
									return false;\">
				<img src='../images/icons/chercher.png' width='16' height='16' alt='Chercher' title='Effectuer une recherche' />
			</a>
		</td>
	</tr>
";
						}

						$sql="INSERT INTO tempo2_sso SET col1='$cpt', col2='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $ligne) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							echo "<span style='color:red'>ERREUR</span>";
						}
						$cpt++;
					}
				}
			}
		}

		echo "
</table>
<input type='hidden' name='temoin_suhosin_2' value='responsable' />
<input type='submit' id='bouton_submit_import' value='Enregistrer' />
<input type='button' id='bouton_button_import' value='Enregistrer' style='display:none;' onclick='verif_puis_submit()' />

</form>
";

		if(count($tab_doublon_possible)>0) {
			echo "<p style='text-indent:-8em; margin-left:8em;'><strong style='color:red'>ATTENTION&nbsp:</strong> Un ou des doublons possibles ont été repérés.<br />
Veuillez contrôler manuellement s'il s'agit ou non de doublons&nbsp;:<br />";
			for($loop=0;$loop<count($tab_doublon_possible);$loop++) {
				echo "<a href='#doublon_possible_".$tab_doublon_possible[$loop]."'>".$tab_doublon_possible[$loop]."</a><br />\n";
			}
			echo "
</p>\n";
		}

		echo "<p>$cpt ligne(s) affichée(s).</p>\n";

		if($cpt_deja_enregistres>0) {
			echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=consult_responsables'>$cpt_deja_enregistres association(s) responsable(s) déjà enregistrée(s)</a>.<br />\n";
		}
		else {
			echo "<p>Aucune association responsable n'est encore enregistrée.<br />\n";
		}

		echo "
<p style='color:red'>A FAIRE:<br />
Pouvoir trier par classe<br />
Ajouter une variable en fin de formulaire pour détecter les pb de transmission de trop de variables avec suhosin.</p>

<script type='text/javascript'>
	document.getElementById('tout_cocher_decocher').style.display='';
	document.getElementById('bouton_button_import').style.display='';
	document.getElementById('bouton_submit_import').style.display='none';

	function change_graisse(num) {
		if((document.getElementById('ligne_'+num))&&(document.getElementById('nom_'+num))&&(document.getElementById('prenom_'+num))) {
			if(document.getElementById('ligne_'+num).checked==true) {
				document.getElementById('nom_'+num).style.fontWeight='bold';
				document.getElementById('prenom_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('nom_'+num).style.fontWeight='normal';
				document.getElementById('prenom_'+num).style.fontWeight='normal';
			}
		}
	}

	function tout_cocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				document.getElementById('ligne_'+i).checked=true;
				change_graisse(i);
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				document.getElementById('ligne_'+i).checked=false;
				change_graisse(i);
			}
		}
	}

	var tab_resp_avec_login_et_eleve_associe=new Array($chaine_resp_avec_login_et_eleve_associe);
	function cocher_tous_les_resp_avec_login_et_eleve_associe() {
		for(i=0;i<tab_resp_avec_login_et_eleve_associe.length;i++) {
			if(document.getElementById('ligne_'+tab_resp_avec_login_et_eleve_associe[i])) {
				document.getElementById('ligne_'+tab_resp_avec_login_et_eleve_associe[i]).checked=true;
				change_graisse(tab_resp_avec_login_et_eleve_associe[i]);
			}
		}
	}

	function ajout_champ_saisie_login(num) {
		if(document.getElementById('saisie_'+num)) {
			document.getElementById('saisie_'+num).innerHTML='<input type=\"text\" name=\"login_'+num+'\" id=\"login_'+num+'\" value=\"\" onchange=\"if(document.getElementById(\'login_'+num+'\').value!=\'\') {document.getElementById(\'ligne_'+num+'\').checked=true;} else {document.getElementById(\'ligne_'+num+'\').checked=false;}\" />';
		}
	}

	function verif_puis_submit() {
		temoin='n';
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				if(document.getElementById('ligne_'+i).checked==true) {
					temoin='y';
					break;
				}
			}
		}
		if(temoin=='y') {
			document.forms['form_import'].submit();
		}
		else {
			alert('Vous n avez rien coché!?');
		}
	}

</script>
";
		// En fin d'enregistrement, renvoyer vers consult_eleves pour afficher les associations
		// Compter/afficher ce total des associations avant... et après dans consult_eleves
	}
	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="consult_responsables") {
	echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
 | <a href='".$_SERVER['PHP_SELF']."?mode=import_responsables'>Importer un CSV responsable</a>
</p>

<h2>Rapprochement actuels des comptes responsables ENT ITOP/GEPI</h2>
";

	$sql="SELECT rp.*, s.* FROM resp_pers rp, sso_table_correspondance s WHERE s.login_gepi=rp.login ORDER BY rp.nom, rp.prenom";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucun rapprochement élève n'est enregistré.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo "
<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<p>Les rapprochements enregistrés (".mysqli_num_rows($res).") sont les suivants&nbsp;:</p>
	".add_token_field()."
	<input type='hidden' name='mode' value='consult_responsables' />
	<input type='hidden' name='temoin_suhosin_1' value='responsable' />

	<table class='boireaus'>
		<tr>
			<th rowspan='2'>
				<input type='submit' value='Supprimer' />
				<span id='tout_cocher_decocher' style='display:none;'>
					<br />
					<a href=\"javascript:tout_cocher()\" title='Tout cocher'><img src='../images/enabled.png' width='20' height='20' /></a>
					/
					<a href=\"javascript:tout_decocher()\" title='Tout décocher'><img src='../images/disabled.png' width='20' height='20' /></a>
				</span>
			</th>
			<th>Informations ENT</th>
			<th colspan='5'>Informations GEPI</th>
			<th rowspan='2'>Corriger</th>
		</tr>

		<tr>
			<th>Guid</th>
			<th>Login</th>
			<th title=\"Pour une connexion via un ENT, le champ auth_mode doit en principe avoir pour valeur 'sso'\">Auth_mode</th>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Responsable de</th>
		</tr>
";

	$cpt=0;
	$alt=1;
	while($lig=mysqli_fetch_object($res)) {
		$tab_ele=get_enfants_from_resp_login($lig->login, 'avec_classe');
		$chaine_ele="";
		for($loop=1;$loop<count($tab_ele);$loop+=2) {
			if($loop>1) {
				$chaine_ele.=", ";
			}
			$chaine_ele.=$tab_ele[$loop];
		}

		$alt=$alt*(-1);
		echo "
		<tr class='lig$alt white_hover'>
			<td><input type='checkbox' name='suppr[]' id='suppr_$cpt' value=\"$lig->login\" onchange=\"change_graisse($cpt)\" /></td>
			<td><label for='suppr_$cpt'>$lig->login_sso</label></td>
			<td><label for='suppr_$cpt'>$lig->login</label></td>
			<td><label for='suppr_$cpt'>";
		$sql="SELECT auth_mode, etat FROM utilisateurs WHERE login='".$lig->login."' AND statut='responsable';";
		$test_u=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test_u)==0) {
			echo "<span title='Pas de compte utilisateur pour ce responsable.'>-</span>";
		}
		else {
			$lig_u=mysqli_fetch_object($test_u);
			if($lig_u->etat=='actif') {
				echo "<div style='float:right;width:16px;'><img src='../images/icons/buddy.png' width='16' height='16' title='Compte actif' /></div>\n";
			}
			else {
				echo "<div style='float:right;width:16px;'><img src='../images/icons/buddy_no.png' width='16' height='16' title='Compte inactif' /></div>\n";
			}
			echo $lig_u->auth_mode;
		}
		echo "</label></td>
			<td><label for='suppr_$cpt'><span id='nom_$cpt'>$lig->nom</span></label></td>
			<td>
				<a href='../responsables/modify_resp.php?pers_id=$lig->pers_id' target='_blank' style='float:right;' title=\"Voir la fiche responsable\"><img src='../images/icons/chercher.png' width='16' height='16' /></a>
				<label for='suppr_$cpt'><span id='prenom_$cpt'>$lig->prenom</span></label>
			</td>
			<td>
				<label for='suppr_$cpt'>$chaine_ele</label>
			</td>
			<td><a href='".$_SERVER['PHP_SELF']."?login_gepi=$lig->login_gepi&amp;login_sso=$lig->login_sso&amp;mode=saisie_manuelle'><img src='../images/edit16.png' width='16' height='16' title=\"Corriger l'association\" /></a></td>
		</tr>
";
		$cpt++;
	}

	echo "
	</table>
	<input type='hidden' name='temoin_suhosin_2' value='responsable' />
</form>


<script type='text/javascript'>
	document.getElementById('tout_cocher_decocher').style.display='';
	document.getElementById('bouton_button_import').style.display='';
	document.getElementById('bouton_submit_import').style.display='none';

	function tout_cocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=true;
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=false;
			}
		}
	}

	function change_graisse(num) {
		if((document.getElementById('suppr_'+num))&&(document.getElementById('nom_'+num))&&(document.getElementById('prenom_'+num))) {
			if(document.getElementById('suppr_'+num).checked==true) {
				document.getElementById('nom_'+num).style.fontWeight='bold';
				document.getElementById('prenom_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('nom_'+num).style.fontWeight='normal';
				document.getElementById('prenom_'+num).style.fontWeight='normal';
			}
		}
	}
</script>
";

	require("../lib/footer.inc.php");
	die();
}
//==================================================================================

if($mode=="import_personnels") {
	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
</p>";

	if(!isset($csv_file)) {
		echo "
<h2>Rapprochement des comptes ENT ITOP/GEPI</h2>

<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		".add_token_field()."
		<input type='hidden' name='mode' value='import_personnels' />
		<input type=\"file\" size=\"65\" name=\"csv_file\" style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /><br />
		<input type='submit' value='Envoyer' />
	</fieldset>
</form>

<p><br /></p>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li> Le fichier CSV attendu doit avoir le format suivant&nbsp;:<br />
	Guid;Nom;Prénom;Profil;Classes;Groupe<br />
	f73d0f72-0958-4b8f-85f7-a58a96d95220;BACQUET;Michel;National_3;;<br />
	...</li>
	<li>Il est recommandé d'envoyer une première fois le CSV, d'enregistrer les associations correctement détectées (<em>en contrôlant tout de même les éventuels doublons repérés</em>).<br />
	Puis, envoyer à nouveau le même fichier pour traiter les indéterminés restants.<br />
	Le deuxième envoi permet aussi de repérer ce qui n'a pas été enregistré au premier envoi.</li>
</ul>\n";
	}
	else {
		check_token(false);
		$fp=fopen($csv_file['tmp_name'],"r");

		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$motif_nom_fichier="ExportSSO_Professeur_";
		echo "<p>Le fichier fourni se nomme <strong>".$csv_file['name']."</strong>";
		if(!preg_match("/$motif_nom_fichier/", $csv_file['name'])) {
			echo "<br />
<span style='color:red'>Le nom du fichier contient habituellement la chaine <strong>$motif_nom_fichier</strong>.<br />
Vous seriez-vous trompé de fichier&nbsp;?</span>";
		}
		echo "</p>\n";

		$sql="TRUNCATE tempo2_sso;";
		$menage=mysqli_query($GLOBALS["mysqli"], $sql);

		echo creer_div_infobulle("div_search","Formulaire de recherche dans la table 'utilisateurs'","","<p>Saisir une portion du nom à rechercher...</p>
<form name='recherche' action='".$_SERVER['PHP_SELF']."' method='post'>
".add_token_field(true)."
<input type='hidden' name='login_recherche' id='login_recherche' value='' />
<table border='0' summary='Recherche'>
	<tr>
		<th>Nom: </th>
		<td><input type='text' name='nom_rech' id='nom_rech' value='' onBlur='recherche_utilisateur(\"personnel\")' /></td>
		<td rowspan='2'><input type='button' name='chercher' value='Chercher' onClick='recherche_utilisateur(\"personnel\")' /></td>
	</tr>
	<tr>
		<th>Prénom: </th>
		<td><input type='text' name='prenom_rech' id='prenom_rech' value='' onBlur='recherche_utilisateur(\"personnel\")' /></td>
	</tr>
</table>
</form>

<div id='div_resultat' style='margin: 1px;'></div>

","",32,0,"y","y","n","n");

		echo "
<h2>Rapprochement des comptes de personnels ENT ITOP/GEPI</h2>

<form action='".$_SERVER['PHP_SELF']."' method='post' name='form_import' enctype='multipart/form-data'>
".add_token_field()."
<input type='hidden' name='mode' value='import_personnels' />
<input type='hidden' name='enregistrement_personnels' value='y' />
<input type='hidden' name='temoin_suhosin_1' value='personnel' />

<table class='boireaus'>
	<tr>
		<th rowspan='2'>
			Enregistrer
			<span id='tout_cocher_decocher' style='display:none;'>
				<br />
				<a href=\"javascript:tout_cocher()\" title='Tout cocher'><img src='../images/enabled.png' width='20' height='20' /></a>
				/
				<a href=\"javascript:tout_decocher()\" title='Tout décocher'><img src='../images/disabled.png' width='20' height='20' /></a>
			</span>
		</th>
		<th colspan='4'>Informations ENT</th>
		<th rowspan='2'>Login Gepi</th>
	</tr>
	<tr>
		<th>Guid</th>
		<th>Nom</th>
		<th>Prénom</th>
		<th>Profil</th>
	</tr>
";
		$alt=1;
		$cpt=0;
		$cpt_deja_enregistres=0;
		$tab_nom_prenom_deja_aff=array();
		$tab_doublon_possible=array();
		while (!feof($fp)) {
			$ligne = trim(fgets($fp, 4096));
			if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
				$ligne=substr($ligne,3);
			}

			if($ligne!='') {
				$tab=explode(";", ensure_utf8($ligne));
				if(!preg_match("/^Guid;/i", trim($ligne))) {

					$sql="SELECT login_gepi FROM sso_table_correspondance WHERE login_sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)>0) {
						$cpt_deja_enregistres++;
					}
					else {

						if(in_array_i($tab[1]." ".$tab[2], $tab_nom_prenom_deja_aff)) {
							$chaine_tmp=$cpt."_".remplace_accents($tab[1]." ".$tab[2],"all");
							$tab_doublon_possible[]=$chaine_tmp;
							$ancre_doublon_ou_pas="<a name='doublon_possible_$chaine_tmp'></a>";
							$style_css=" style='background-color:red' title=\"Il existe au moins un homonyme dans le CSV.
Si les homonymes correspondent à un même élève, vous allez devoir identifier le bon GUID
(choisir un des homonymes au hasard, et demander à l'élève de tester.
si cela ne fonctionne pas, corriger l'association élève en mettant le GUID de l'homonyme).\"";
						}
						else {
							$ancre_doublon_ou_pas="";
							$style_css="";
						}
						$tab_nom_prenom_deja_aff[]=$tab[1]." ".$tab[2];

						$alt=$alt*(-1);
						echo "
	<tr class='lig$alt white_hover'$style_css>
		<td><input type='checkbox' name='ligne[]' id='ligne_$cpt' value='$cpt' onchange=\"change_graisse($cpt)\" />$ancre_doublon_ou_pas</td>
		<td><label for='ligne_$cpt'>".$tab[0]."</label></td>
		<td><label for='ligne_$cpt'><span id='nom_$cpt'>".$tab[1]."</span></label></td>
		<td><label for='ligne_$cpt'><span id='prenom_$cpt'>".$tab[2]."</span></label></td>
		<td><label for='ligne_$cpt'>".$tab[3]."</label></td>";

						$sql="SELECT * FROM utilisateurs WHERE nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[1]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' AND prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab[2]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' AND statut!='eleve' AND statut!='responsable' ORDER BY statut;";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)==1) {
							// Un seul personnel correspond
							$lig=mysqli_fetch_object($res);

							echo "
		<td title=\"$lig->nom $lig->prenom ($lig->statut)\">
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>
			$lig->login
			<span id='saisie_$cpt'></span>

			<a href='#' onClick=\"document.getElementById('nom_rech').value=document.getElementById('nom_$cpt').innerHTML;
									document.getElementById('prenom_rech').value=document.getElementById('prenom_$cpt').innerHTML;
									document.getElementById('login_recherche').value='login_$cpt';
									document.getElementById('div_resultat').innerHTML='';
									afficher_div('div_search','y',-400,20);
									if(!document.getElementById('login_$cpt')) {ajout_champ_saisie_login($cpt);};
									return false;\">
				<img src='../images/icons/chercher.png' width='16' height='16' alt='Chercher' title='Effectuer une recherche' />
			</a>
		</td>
	</tr>
";
						}
						else {
							// Plusieurs personnels correspondent
							// Il va falloir choisir
							$chaine_options="";
							while($lig=mysqli_fetch_object($res)) {
								$chaine_options.="				<option value=\"$lig->login\">$lig->nom $lig->prenom (".$lig->statut.")";
								$chaine_options.="</option>\n";
							}

							if($chaine_options!="") {
								echo "
		<td>
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>

			<span id='saisie_$cpt'>
			<select name='login_$cpt' id='login_$cpt' onchange=\"if(document.getElementById('login_$cpt').options[if(document.getElementById('login_$cpt').selectedIndex].value!='') {document.getElementById('ligne_$cpt').checked=true;} else{document.getElementById('ligne_$cpt').checked=false;}\">
				<option value=''>---</option>
				$chaine_options
			</select>
			</span>

			<a href='#' onClick=\"document.getElementById('nom_rech').value=document.getElementById('nom_$cpt').innerHTML;
									document.getElementById('prenom_rech').value=document.getElementById('prenom_$cpt').innerHTML;
									document.getElementById('login_recherche').value='login_$cpt';
									document.getElementById('div_resultat').innerHTML='';
									afficher_div('div_search','y',-400,20);
									if(!document.getElementById('login_$cpt')) {ajout_champ_saisie_login($cpt);};
									return false;\">
				<img src='../images/icons/chercher.png' width='16' height='16' alt='Chercher' title='Effectuer une recherche' />
			</a>
		</td>
	</tr>
";
							}
							else {
								echo "
		<td>";
								if($module_suhosin_actif!="y") {
									echo "
			<input type='text' name='login_$cpt' id='login_$cpt' value=\"\" onchange=\"if(document.getElementById('login_$cpt').value!='') {document.getElementById('ligne_$cpt').checked=true;} else{document.getElementById('ligne_$cpt').checked=false;}\" />";
								}
								else {
									echo "
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>

			<span id='saisie_$cpt'></span>";
								}

								// On renseigne le formulaire de recherche avec le nom et le prénom:
								// ...
								// On fait le nettoyage pour ne pas laisser les traces d'une précédente requête:
								echo "
			<a href='#' onClick=\"document.getElementById('nom_rech').value=document.getElementById('nom_$cpt').innerHTML;
									document.getElementById('prenom_rech').value=document.getElementById('prenom_$cpt').innerHTML;
									document.getElementById('login_recherche').value='login_$cpt';
									document.getElementById('div_resultat').innerHTML='';
									afficher_div('div_search','y',-400,20);
									if(!document.getElementById('login_$cpt')) {ajout_champ_saisie_login($cpt);};
									return false;\">
				<img src='../images/icons/chercher.png' width='16' height='16' alt='Chercher' title='Effectuer une recherche' />
			</a>
		</td>
	</tr>
";
								// A FAIRE: Mettre un dispositif de recherche comme dans mod_annees_anterieures
							}
						}

						$sql="INSERT INTO tempo2_sso SET col1='$cpt', col2='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $ligne) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							echo "<span style='color:red'>ERREUR</span>";
						}
						$cpt++;
					}
				}
			}
		}

		echo "
</table>
<input type='hidden' name='temoin_suhosin_2' value='personnel' />
<input type='submit' id='bouton_submit_import' value='Enregistrer' />
<input type='button' id='bouton_button_import' value='Enregistrer' style='display:none;' onclick='verif_puis_submit()' />
</form>
";

		if(count($tab_doublon_possible)>0) {
			echo "<p style='text-indent:-8em; margin-left:8em;'><strong style='color:red'>ATTENTION&nbsp:</strong> Un ou des doublons possibles ont été repérés.<br />
Veuillez contrôler manuellement s'il s'agit ou non de doublons&nbsp;:<br />";
			for($loop=0;$loop<count($tab_doublon_possible);$loop++) {
				echo "<a href='#doublon_possible_".$tab_doublon_possible[$loop]."'>".$tab_doublon_possible[$loop]."</a><br />\n";
			}
			echo "
</p>\n";
		}

		echo "<p>$cpt ligne(s) affichée(s).</p>\n";

		if($cpt_deja_enregistres>0) {
			echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=consult_personnels'>$cpt_deja_enregistres association(s) personnel(s) déjà enregistrée(s)</a>.<br />\n";
		}
		else {
			echo "<p>Aucune association de compte personnel n'est encore enregistrée.<br />\n";
		}

		echo "
<p style='color:red'>A FAIRE:<br />
Ajouter une variable en fin de formulaire pour détecter les pb de transmission de trop de variables avec suhosin.</p>

<script type='text/javascript'>
	document.getElementById('tout_cocher_decocher').style.display='';
	document.getElementById('bouton_button_import').style.display='';
	document.getElementById('bouton_submit_import').style.display='none';

	function tout_cocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				document.getElementById('ligne_'+i).checked=true;
				change_graisse(i);
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				document.getElementById('ligne_'+i).checked=false;
				change_graisse(i);
			}
		}
	}

	function ajout_champ_saisie_login(num) {
		if(document.getElementById('saisie_'+num)) {
			document.getElementById('saisie_'+num).innerHTML='<input type=\"text\" name=\"login_'+num+'\" id=\"login_'+num+'\" value=\"\" onchange=\"if(document.getElementById(\'login_'+num+'\').value!=\'\') {document.getElementById(\'ligne_'+num+'\').checked=true;} else {document.getElementById(\'ligne_'+num+'\').checked=false;}\" />';
		}
	}

	function verif_puis_submit() {
		temoin='n';
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				if(document.getElementById('ligne_'+i).checked==true) {
					temoin='y';
					break;
				}
			}
		}
		if(temoin=='y') {
			document.forms['form_import'].submit();
		}
		else {
			alert('Vous n avez rien coché!?');
		}
	}

	function change_graisse(num) {
		if((document.getElementById('ligne_'+num))&&(document.getElementById('nom_'+num))&&(document.getElementById('prenom_'+num))) {
			if(document.getElementById('ligne_'+num).checked==true) {
				document.getElementById('nom_'+num).style.fontWeight='bold';
				document.getElementById('prenom_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('nom_'+num).style.fontWeight='normal';
				document.getElementById('prenom_'+num).style.fontWeight='normal';
			}
		}
	}
</script>
";
		// En fin d'enregistrement, renvoyer vers consult_eleves pour afficher les associations
		// Compter/afficher ce total des associations avant... et après dans consult_eleves
	}
	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="consult_personnels") {
	echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
 | <a href='".$_SERVER['PHP_SELF']."?mode=import_personnels'>Importer un CSV personnels</a>
</p>

<h2>Rapprochement actuels des comptes de personnels ENT ITOP/GEPI</h2>
";

	$sql="SELECT u.*, s.* FROM utilisateurs u, sso_table_correspondance s WHERE s.login_gepi=u.login AND u.statut!='eleve' AND u.statut!='responsable' ORDER BY u.nom, u.prenom";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucun rapprochement de personnel n'est enregistré.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo "
<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<p>Les rapprochements enregistrés (".mysqli_num_rows($res).") sont les suivants&nbsp;:</p>
	".add_token_field()."
	<input type='hidden' name='mode' value='consult_personnels' />
	<input type='hidden' name='temoin_suhosin_1' value='personnel' />

	<table class='boireaus'>
		<tr>
			<th>
				<input type='submit' value='Supprimer' />
				<span id='tout_cocher_decocher' style='display:none;'>
					<br />
					<a href=\"javascript:tout_cocher()\" title='Tout cocher'><img src='../images/enabled.png' width='20' height='20' /></a>
					/
					<a href=\"javascript:tout_decocher()\" title='Tout décocher'><img src='../images/disabled.png' width='20' height='20' /></a>
				</span>
			</th>
			<th>Guid</th>
			<th>Login</th>
			<th title=\"Pour une connexion via un ENT, le champ auth_mode doit en principe avoir pour valeur 'sso'\">Auth_mode</th>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Statut</th>
			<th>Corriger</th>
		</tr>
";

	$cpt=0;
	$alt=1;
	while($lig=mysqli_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "
		<tr class='lig$alt white_hover'>
			<td><input type='checkbox' name='suppr[]' id='suppr_$cpt' value=\"$lig->login\" onchange=\"change_graisse($cpt)\" /></td>
			<td><label for='suppr_$cpt'>$lig->login_sso</label></td>
			<td><label for='suppr_$cpt'>$lig->login</label></td>
			<td><label for='suppr_$cpt'>";
		$sql="SELECT auth_mode, etat FROM utilisateurs WHERE login='".$lig->login."';";
		$test_u=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test_u)==0) {
			echo "<span title='Pas de compte utilisateur pour ce personnel.' style='color:red'>???</span>";
		}
		else {
			$lig_u=mysqli_fetch_object($test_u);
			if($lig_u->etat=='actif') {
				echo "<div style='float:right;width:16px;'><img src='../images/icons/buddy.png' width='16' height='16' title='Compte actif' /></div>\n";
			}
			else {
				echo "<div style='float:right;width:16px;'><img src='../images/icons/buddy_no.png' width='16' height='16' title='Compte inactif' /></div>\n";
			}
			echo $lig_u->auth_mode;
		}
		echo "</label></td>
			<td><label for='suppr_$cpt'><span id='nom_$cpt'>$lig->nom</label></span></td>
			<td><label for='suppr_$cpt'><span id='prenom_$cpt'>$lig->prenom</span></label></td>
			<td><label for='suppr_$cpt'>$lig->statut</label></td>
			<td><a href='".$_SERVER['PHP_SELF']."?login_gepi=$lig->login_gepi&amp;login_sso=$lig->login_sso&amp;mode=saisie_manuelle'><img src='../images/edit16.png' width='16' height='16' title=\"Corriger l'association\" /></label></td>
		</tr>
";
		$cpt++;
	}

	echo "
	</table>
	<input type='hidden' name='temoin_suhosin_2' value='personnel' />
</form>


<script type='text/javascript'>
	document.getElementById('tout_cocher_decocher').style.display='';

	function tout_cocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=true;
				change_graisse(i);
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=false;
				change_graisse(i);
			}
		}
	}

	function change_graisse(num) {
		if((document.getElementById('suppr_'+num))&&(document.getElementById('nom_'+num))&&(document.getElementById('prenom_'+num))) {
			if(document.getElementById('suppr_'+num).checked==true) {
				document.getElementById('nom_'+num).style.fontWeight='bold';
				document.getElementById('prenom_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('nom_'+num).style.fontWeight='normal';
				document.getElementById('prenom_'+num).style.fontWeight='normal';
			}
		}
	}
</script>
";

	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="publipostage_eleves") {
	echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>";

	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	if(!isset($csv_file)) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=publipostage_eleves'>Fiches bienvenue élèves</a></p>

<h2 class='noprint'>Fiches bienvenue élèves</h2>";

		// Liste des classes avec élève:
		$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) ORDER BY c.classe;";
		$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);

		$nb_classes=mysqli_num_rows($call_classes);
		if($nb_classes==0){
			echo "<p>Aucune classe avec élève affecté n'a été trouvée.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		// Affichage sur 3 colonnes
		$nb_classes_par_colonne=round($nb_classes/3);


		echo "<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		".add_token_field()."

		<p><input type='radio' name='toutes_les_classes' id='toutes_les_classes_y' value='y' checked /><label for='toutes_les_classes_y'> Générer les Fiches bienvenue pour toutes les classes</label><br />
		ou<br />
		<input type='radio' name='toutes_les_classes' id='toutes_les_classes_n' value='n' /><label for='toutes_les_classes_n'> Générer les Fiches bienvenue pour une sélection de classes<br />
		(<em>si votre navigateur peine à effectuer l'impression pour un trop grand nombre de pages par exemple</em>)</label><br />
		";
		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";

		$cpt = 0;

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while($lig_clas=mysqli_fetch_object($call_classes)) {

			//affichage 2 colonnes
			if(($cpt>0)&&(round($cpt/$nb_classes_par_colonne)==$cpt/$nb_classes_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}

			//echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='id_classe[]' id='tab_id_classe_$cpt' value='$lig_clas->id' onchange=\"change_style_classe($cpt)\" /> $lig_clas->classe</label>";
			echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='classe[]' id='tab_id_classe_$cpt' value='$lig_clas->classe' onchange=\"change_style_classe($cpt)\" /> $lig_clas->classe</label>";
			echo "<br />\n";
			$cpt++;
		}

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";

		echo "
		<br />

		<p>Veuillez fournir le fichier <strong>".getSettingValue("gepiSchoolRne")."_MiseaJour_Motdepasse_Eleve_JJ_MM_AAAA_HH_MM_SS.csv</strong> généré par l'ENT.</p>
		<input type='hidden' name='mode' value='publipostage_eleves' />
		<input type=\"file\" size=\"65\" name=\"csv_file\" style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /><br />
		<p><input type='submit' value='Envoyer' /></p>
	</fieldset>
</form>

<p><br /></p>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li>Cette rubrique est destinée à générer des Fiches Bienvenue avec compte et mot de passe de l'ENT.<br />
	<!--
		Ne seront pris en compte que les comptes ENT pour lesquels le rapprochement avec un compte Gepi est effectué.
		Ce n'est pas le cas.
		Il faudrait enregistrer davantage d'informations dans sso_table_correspondance pour s'en assurer.
	-->
	</li>
	<li>Modifier les <a href='../gestion/modify_impression.php?fiche=eleves'>Fiches Bienvenue élèves</a></li>
	<li>Le fichier CSV attendu doit avoir le format suivant&nbsp;:<br />
	﻿Nom;Prénom;Login;Numéro de jointure;Mot de passe;Email;Classe;Etat;Date de désactivation<br />
	DUPRE;Thomas;thomas.dupre;MENESR$12345;mdp&*;Thomas.DUPRE@ent27.fr;6 A;Actif<br />
	...</li>
	<li>Le fichier CSV attendu doit comporter une ligne d'entête avec au moins les champs <strong>Nom;Prénom;Login;Mot de passe;Classe</strong></li>
	<li>Le fichier CSV attendu peut être&nbsp;:<br />
		<ul>
			<li>
				<p>celui de regénération de tous les mots de passe élèves.<br />
				Il aura alors le format suivant&nbsp;:<br />
				<strong>﻿﻿Nom</strong>;<strong>Prénom</strong>;<strong>Login</strong>;Numéro de jointure;<strong>Mot de passe</strong>;Email;<strong>Classe</strong>;<span style='color:green'>Etat</span>;Date de désactivation<br />
				<strong>DUPRE</strong>;<strong>Denis</strong>;<strong>denis.dupre1</strong>;MENESR$1234567;<strong>azerty&*</strong>;Denis.DUPRE1@ent27.fr;<strong>6 A</strong>;<span style='color:green'>Actif</span><br />
				...<br />
				Avec le champ Etat, on peut exclure les comptes désactivés.</p>
				<br />
			</li>
			<li>
				<p>un fichier CSV obtenu en enregistrant au format CSV avec séparateur point-virgule (<em>édition des paramètres du filtre requise</em>) le feuillet Elève d'un fichier [V2]CLG-".getSettingValue('gepiSchoolName')."-ac-ROUEN - [".getSettingValue('gepiSchoolRne')."] - [ANNEEMOISJOURHEURE].xlsx de l'espace Documents.
				Il aura alors le format suivant&nbsp;:<br />
				<strong>﻿﻿Nom</strong>;<strong>Prénom</strong>;<strong>Login</strong>;<strong>Mot de passe</strong>;Adresse Mail;<strong>Classe</strong><br />
				<strong>DUPRE</strong>;<strong>Denis</strong>;<strong>denis.dupre1</strong>;<strong>azerty&*</strong>;Denis.DUPRE1@ent27.fr;<strong>6 A</strong><br />
				...<br />
				Il manque le champ Etat, mais le reste y est.<br /></p>
				<br />
			</li>
			<li>
				<p>le fichier CSV ".getSettingValue('gepiSchoolRne')."_Extraction_Elève.csv que vous pouvez trouver dans les fichiers ".getSettingValue('gepiSchoolRne')."_CSV_ANNEEMOISJOURHEURE.zip de l'espace Documents.
				Il aura alors le format suivant&nbsp;:<br />
				<strong>﻿﻿Nom</strong>;<strong>Prénom</strong>;<strong>Login</strong>;<strong>Mot de passe</strong>;Email;<strong>Classe</strong><br />
				<strong>DUPRE</strong>;<strong>Denis</strong>;<strong>denis.dupre1</strong>;<strong>azerty&*</strong>;Thomas.DUPRE@ent27.fr;<strong>6 A</strong><br />
				...<br />
				</p>
			</li>
		</ul>
	</li>
</ul>

<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('tab_id_classe_'+k)){
				document.getElementById('tab_id_classe_'+k).checked = mode;
				change_style_classe(k);
			}
		}
	}

	function change_style_classe(num) {
		if(document.getElementById('tab_id_classe_'+num)) {
			if(document.getElementById('tab_id_classe_'+num).checked) {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='bold';
				if(document.getElementById('toutes_les_classes_n')){
					document.getElementById('toutes_les_classes_n').checked=true;
				}
			}
			else {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";


	}
	else {
		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=publipostage_eleves'>Fiches bienvenue élèves</a></p>

<h2 class='noprint'>Fiches bienvenue élèves</h2>";

		check_token(false);
		$fp=fopen($csv_file['tmp_name'],"r");

		$impression=getSettingValue('ImpressionFicheEleve');

		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$motif_nom_fichier="Miseajour_Motdepasse_Eleve_";
		echo "<p class='noprint'>Le fichier fourni se nomme <strong>".$csv_file['name']."</strong>";
		if(!preg_match("/$motif_nom_fichier/", $csv_file['name'])) {
			echo "<br />
<span style='color:red'>Le nom du fichier contient habituellement la chaine <strong>$motif_nom_fichier</strong>.<br />
Vous seriez-vous trompé de fichier&nbsp;?</span><br />
<span style='color:blue'>Si vous n'avez fourni qu'un fichier CSV des nouveaux arrivants (<em>sans regénérer tous les mots de passe</em>), le nom de fichier sera celui de votre choix; ne tenez donc pas compte de cette alerte.</span>";
		}
		echo "</p>\n";

		// 20130916
		// Lire la ligne d'entête pour repérer les indices des colonnes recherchées
		$tabchamps = array("Nom", "Prénom", "Prenom", "Login", "Mot de passe", "Email", "Adresse Mail", "Classe", "Etat", "Date de désactivation");

		// Lecture de la ligne 1 et la mettre dans $temp
		$temp=fgets($fp,4096);
		//echo "$temp<br />";
		$en_tete=explode(";", trim($temp));

		$tabindice=array();

		// On range dans tabindice les indices des champs retenus
		for ($k = 0; $k < count($tabchamps); $k++) {
			//echo "<br /><p style='text-indent:-4em;margin-left:4em'>Recherche du champ ".$tabchamps[$k]."<br />";
			for ($i = 0; $i < count($en_tete); $i++) {
				//echo "\$en_tete[$i]=$en_tete[$i]<br />";
				//echo casse_mot(remplace_accents($en_tete[$i]),'min')."<br />";
				//echo casse_mot(remplace_accents($tabchamps[$k]), 'min')."<br />";
				if (casse_mot(remplace_accents($en_tete[$i]),'min') == casse_mot(remplace_accents($tabchamps[$k]), 'min')) {
					$tabindice[$tabchamps[$k]] = $i;
					//echo "\$tabindice[$tabchamps[$k]]=$i<br />";
				}
			}
		}
		if((!isset($tabindice['Nom']))||((!isset($tabindice['Prénom']))&&(!isset($tabindice['Prenom'])))||(!isset($tabindice['Login']))||(!isset($tabindice['Mot de passe']))||(!isset($tabindice['Classe']))) {
			echo "<p style='color:red'>La ligne d'entête ne comporte pas un des champs indispensables (<em>Nom, Prénom, Login, Mot de passe, Classe</em>).</p>";
			require("../lib/footer.inc.php");
			die();
		}

		if(!isset($tabindice['Prénom'])) {
			$tabindice['Prénom']=$tabindice['Prenom'];
		}

		echo "
<hr class='noprint'/>";

		$cpt=0;
		$tab_classe_eleve=array();
		while (!feof($fp)) {
			$ligne = trim(fgets($fp, 4096));
			if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
				$ligne=substr($ligne,3);
			}

			if($ligne!='') {
				$tab=explode(";", ensure_utf8($ligne));

				$ligne_a_prendre_en_compte="y";
				if(preg_match("/^Nom;Pr/i", trim($ligne))) {
					$ligne_a_prendre_en_compte="n";
				}
				elseif((isset($tabindice['Classe']))&&(preg_match("/^BASE20/",$tab[$tabindice['Classe']]))) {
					// On exclut l'année précédente
					$ligne_a_prendre_en_compte="n";
				}
				elseif((isset($tabindice['Etat']))&&($tab[$tabindice['Etat']]!='Actif')) {
					// On exclut les comptes "Désactivé"
					$ligne_a_prendre_en_compte="n";
				}

				//if((!preg_match("/^Nom;Pr/i", trim($ligne)))&&(preg_match("/Actif$/", $ligne))) {
				if($ligne_a_prendre_en_compte=="y") {
					if(($_POST['toutes_les_classes']=="y")||(in_array($tab[$tabindice['Classe']], $_POST['classe']))) {
						if(!isset($tab_classe_eleve[$tab[$tabindice['Classe']]])) {
							$cpt=0;
						}
						else {
							$cpt=count($tab_classe_eleve[$tab[$tabindice['Classe']]]);
						}
						$tab_classe_eleve[$tab[$tabindice['Classe']]][$cpt]['nom_prenom']=$tab[$tabindice['Nom']]." ".$tab[$tabindice['Prénom']];
						//echo "\$tab_classe_parent[$tab[11]][$cpt]['nom_prenom']=".$tab_classe_parent[$tab[11]][$cpt]['nom_prenom']."<br />";
						$tab_classe_eleve[$tab[$tabindice['Classe']]][$cpt]['login_ent']=$tab[$tabindice['Login']];
						$tab_classe_eleve[$tab[$tabindice['Classe']]][$cpt]['mdp_ent']=$tab[$tabindice['Mot de passe']];
						if(isset($tabindice['Email'])) {
							$tab_classe_eleve[$tab[$tabindice['Classe']]][$cpt]['email_ent']=$tab[$tabindice['Email']];
						}
						elseif(isset($tabindice['Adresse Mail'])) {
							$tab_classe_eleve[$tab[$tabindice['Classe']]][$cpt]['email_ent']=$tab[$tabindice['Adresse Mail']];
						}
						//$cpt++;
					}
				}
			}
		}

		$saut=1;
		$nb_fiches=getSettingValue("ImpressionNombreEleve");
		foreach($tab_classe_eleve as $classe => $tab_eleve) {
			/*
			echo "<pre>";
			print_r($tab_parent);
			echo "</pre>";
			*/
			for($loop=0;$loop<count($tab_eleve);$loop++) {
				echo "<table>
	<tr>
		<th style='text-align:left;'>A l'attention de </th>
		<th>: </th>
		<td>".$tab_eleve[$loop]['nom_prenom']."</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Login ENT</th>
		<th>: </th>
		<td>".$tab_eleve[$loop]['login_ent']."</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Mot de passe ENT</th>
		<th>: </th>
		<td>".$tab_eleve[$loop]['mdp_ent']."</td>
	</tr>";
				if(isset($tab_eleve[$loop]['email_ent'])) {
					echo "
	<tr>
		<th style='text-align:left;'>Email ENT</th>
		<th>: </th>
		<td>".$tab_eleve[$loop]['email_ent']."</td>
	</tr>";
				}
				echo "
	<tr>
		<th style='text-align:left;'>Classe</th>
		<th>: </th>
		<td>".$classe."</td>
	</tr>
</table>
$impression";

				// Saut de page toutes les $nb_fiches fiches
				if ($saut == $nb_fiches) {
					echo "<p class='saut'>&nbsp;</p>\n";
					$saut = 1;
				} else {
					// Mettre le saut de ligne dans la fiche bienvenue elle-même.
					//echo "<br />";
					$saut++;
				}

				echo "<hr class='noprint'/>";
			}
		}

	}

	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="publipostage_responsables") {
	echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>";

	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	if(!isset($csv_file)) {

		echo "</p>

<h2 class='noprint'>Fiches bienvenue responsables</h2>";

		// Liste des classes avec élève:
		$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) ORDER BY c.classe;";
		$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);

		$nb_classes=mysqli_num_rows($call_classes);
		if($nb_classes==0){
			echo "<p>Aucune classe avec élève affecté n'a été trouvée.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		// Affichage sur 3 colonnes
		$nb_classes_par_colonne=round($nb_classes/3);


		echo "<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		".add_token_field()."

		<p><input type='radio' name='toutes_les_classes' id='toutes_les_classes_y' value='y' checked /><label for='toutes_les_classes_y'> Générer les Fiches bienvenue pour les parents des élèves de toutes les classes</label><br />
		ou<br />
		<input type='radio' name='toutes_les_classes' id='toutes_les_classes_n' value='n' /><label for='toutes_les_classes_n'> Générer les Fiches bienvenue pour une sélection de classes<br />
		(<em>si votre navigateur peine à effectuer l'impression pour un trop grand nombre de pages par exemple</em>)</label><br />
		";
		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";

		$cpt = 0;

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while($lig_clas=mysqli_fetch_object($call_classes)) {

			//affichage 2 colonnes
			if(($cpt>0)&&(round($cpt/$nb_classes_par_colonne)==$cpt/$nb_classes_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}

			//echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='id_classe[]' id='tab_id_classe_$cpt' value='$lig_clas->id' onchange=\"change_style_classe($cpt)\" /> $lig_clas->classe</label>";
			echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='classe[]' id='tab_id_classe_$cpt' value='$lig_clas->classe' onchange=\"change_style_classe($cpt)\" /> $lig_clas->classe</label>";
			echo "<br />\n";
			$cpt++;
		}

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";

		echo "
		<br />
		<p><input type='checkbox' name='avec_adresse' id='avec_adresse' value='y' /><label for='avec_adresse'> Inclure le rappel de l'adresse.</p>
		<br />

		<p>Veuillez fournir le fichier <strong>".getSettingValue("gepiSchoolRne")."_MiseaJour_Motdepasse_Parent_JJ_MM_AAAA_HH_MM_SS.csv</strong> généré par l'ENT.</p>
		<input type='hidden' name='mode' value='publipostage_responsables' />
		<input type=\"file\" size=\"65\" name=\"csv_file\" style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /><br />
		<input type='submit' value='Envoyer' />
	</fieldset>
</form>

<p><br /></p>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li>Cette rubrique est destinée à générer des Fiches Bienvenue avec compte et mot de passe de l'ENT.<br />
	<!--
		Ne seront pris en compte que les comptes ENT pour lesquels le rapprochement avec un compte Gepi est effectué.
		Ce n'est pas le cas.
		Il faudrait enregistrer davantage d'informations dans sso_table_correspondance pour s'en assurer.
	-->
	</li>
	<li>Modifier les <a href='../gestion/modify_impression.php?fiche=responsables'>Fiches Bienvenue responsables</a></li>
	<li>Le fichier CSV attendu doit comporter une ligne d'entête avec au moins les champs <strong>Nom;Prénom;Login;Mot de passe</strong></li>
	<li>Le fichier CSV attendu peut être&nbsp;:<br />
		<ul>
			<li>
				<p>celui de regénération de tous les mots de passe parents.<br />
				Il aura alors le format suivant&nbsp;:<br />
				<strong>﻿﻿Nom</strong>;<strong>Prénom</strong>;<strong>Login</strong>;Numéro de jointure;<strong>Mot de passe</strong>;Email;Adresse;Code postal;Ville;<span style='color:blue'>Nom enfant 1</span>;<span style='color:blue'>Prénom enfant 1</span>;<span style='color:blue'>Classe enfant 1</span>;<span style='color:green'>Etat</span>;Date de désactivation<br />
				<strong>DUPRE</strong>;<strong>Denis</strong>;<strong>denis.dupre1</strong>;MENESR$1234567;<strong>azerty&*</strong>;Denis.DUPRE1@ent27.fr;3 RUE DES PRIMEVERES;27300;BERNAY;<span style='color:blue'>DUPRE</span>;<span style='color:blue'>Thomas</span>;<span style='color:blue'>6 A</span>;<span style='color:green'>Actif</span><br />
				...<br />
				Avec les nom, prénom et classe de l'enfant sont présents, il est plus simple de distribuer les Fiches bienvenues aux bonnes personnes.<br />
				Avec le champ Etat, on peut exclure les comptes désactivés.</p>
				<br />
			</li>
			<li>
				<p>un fichier CSV obtenu en enregistrant au format CSV avec séparateur point-virgule (<em>édition des paramètres du filtre requise</em>) le feuillet Parent d'un fichier [V2]CLG-".getSettingValue('gepiSchoolName')."-ac-ROUEN - [".getSettingValue('gepiSchoolRne')."] - [ANNEEMOISJOURHEURE].xlsx de l'espace Documents.
				Il aura alors le format suivant&nbsp;:<br />
				<strong>﻿﻿Nom</strong>;<strong>Prénom</strong>;<strong>Login</strong>;<strong>Mot de passe</strong>;Email;Adresse;Code postal;Ville;<span style='color:blue'>Nom enfant 1</span>;<span style='color:blue'>Prénom enfant 1</span>;<span style='color:blue'>Classe enfant 1</span><br />
				<strong>DUPRE</strong>;<strong>Denis</strong>;<strong>denis.dupre1</strong>;<strong>azerty&*</strong>;Denis.DUPRE1@ent27.fr;3 RUE DES PRIMEVERES;27300;BERNAY;<span style='color:blue'>DUPRE</span>;<span style='color:blue'>Thomas</span>;<span style='color:blue'>6 A</span><br />
				...<br />
				Il manque le champ Etat, mais le reste y est.<br /></p>
				<br />
			</li>
			<li>
				<p>le fichier CSV ".getSettingValue('gepiSchoolRne')."_Extraction_Parent.csv que vous pouvez trouver dans les fichiers ".getSettingValue('gepiSchoolRne')."_CSV_ANNEEMOISJOURHEURE.zip de l'espace Documents.
				Il aura alors le format suivant&nbsp;:<br />
				<strong>﻿﻿Nom</strong>;<strong>Prénom</strong>;<strong>Login</strong>;<strong>Mot de passe</strong><br />
				<strong>DUPRE</strong>;<strong>Denis</strong>;<strong>denis.dupre1</strong>;<strong>azerty&*</strong><br />
				...<br />
				On a le minimum autorisé de champs... et plus de difficultés pour faire le tri et distribuer.</p>
			</li>
		</ul>
	</li>
	<li>
		Le fichier demandé n'associe chaque parent qu'à un seul enfant.<br />
		Un parent de plusieurs enfants dans l'établissement n'apparaitra que dans les fiches bienvenue de la classe de l'enfant auquel il est associé dans le fichier.
	</li>
</ul>


<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('tab_id_classe_'+k)){
				document.getElementById('tab_id_classe_'+k).checked = mode;
				change_style_classe(k);
			}
		}
	}

	function change_style_classe(num) {
		if(document.getElementById('tab_id_classe_'+num)) {
			if(document.getElementById('tab_id_classe_'+num).checked) {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='bold';
				if(document.getElementById('toutes_les_classes_n')){
					document.getElementById('toutes_les_classes_n').checked=true;
				}
			}
			else {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";
	}
	else {

		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=publipostage_responsables'>Fiches bienvenue responsables</a></p>

<h2 class='noprint'>Fiches bienvenue responsables</h2>";

		check_token(false);
		$fp=fopen($csv_file['tmp_name'],"r");

		$impression=getSettingValue('ImpressionFicheParent');

		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$motif_nom_fichier="Miseajour_Motdepasse_Parent_";
		echo "<p class='noprint'>Le fichier fourni se nomme <strong>".$csv_file['name']."</strong>";
		if(!preg_match("/$motif_nom_fichier/", $csv_file['name'])) {
			echo "<br />
<span style='color:red'>Le nom du fichier contient habituellement la chaine <strong>$motif_nom_fichier</strong>.<br />
Vous seriez-vous trompé de fichier&nbsp;?</span><br />
<span style='color:blue'>Si vous n'avez fourni qu'un fichier CSV des nouveaux arrivants (<em>sans regénérer tous les mots de passe</em>), le nom de fichier sera celui de votre choix; ne tenez donc pas compte de cette alerte.</span>";
		}
		echo "</p>\n";

		// 20130916
		// Lire la ligne d'entête pour repérer les indices des colonnes recherchées
		$tabchamps = array("Nom", "Prénom", "Login", "Mot de passe", "Email", "Adresse", "Code postal", "Ville", "Nom enfant 1", "Prénom enfant 1", "Classe enfant 1", "Etat", "Date de désactivation");

		// Lecture de la ligne 1 et la mettre dans $temp
		$temp=fgets($fp,4096);
		//echo "$temp<br />";
		$en_tete=explode(";", trim($temp));

		$tabindice=array();

		// On range dans tabindice les indices des champs retenus
		for ($k = 0; $k < count($tabchamps); $k++) {
			//echo "<p style='text-indent:-4em;margin-left:4em'>Recherche du champ ".$tabchamps[$k]."<br />";
			for ($i = 0; $i < count($en_tete); $i++) {
				//echo "\$en_tete[$i]=$en_tete[$i]<br />";
				if (casse_mot(remplace_accents($en_tete[$i]),'min') == casse_mot(remplace_accents($tabchamps[$k]), 'min')) {
					$tabindice[$tabchamps[$k]] = $i;
					//echo "\$tabindice[$tabchamps[$k]]=$i<br />";
				}
			}
		}
		if((!isset($tabindice['Nom']))||(!isset($tabindice['Prénom']))||(!isset($tabindice['Login']))||(!isset($tabindice['Mot de passe']))) {
			echo "<p style='color:red'>La ligne d'entête ne comporte pas un des champs indispensables (<em>Nom, Prénom, Login, Mot de passe</em>).</p>";
			require("../lib/footer.inc.php");
			die();
		}

		echo "
<hr class='noprint'/>";

		$cpt=0;
		//$classe_precedente="";
		$tab_classe_parent=array();
		while (!feof($fp)) {
			$ligne = trim(fgets($fp, 4096));
			if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
				$ligne=substr($ligne,3);
			}

			if($ligne!='') {
				$tab=explode(";", ensure_utf8($ligne));

				$ligne_a_prendre_en_compte="y";
				if(preg_match("/^Nom;Pr/i", trim($ligne))) {
					$ligne_a_prendre_en_compte="n";
				}
				elseif((isset($tabindice['Classe enfant 1']))&&(preg_match("/^BASE20/",$tab[$tabindice['Classe enfant 1']]))) {
					// On exclut l'année précédente
					$ligne_a_prendre_en_compte="n";
				}
				elseif((isset($tabindice['Etat']))&&($tab[$tabindice['Etat']]!='Actif')) {
					// On exclut les comptes "Désactivé"
					$ligne_a_prendre_en_compte="n";
				}

				//if((!preg_match("/^Nom;Pr/i", trim($ligne)))&&(isset($tab[11]))&&(isset($tab[12]))&&(!preg_match("/^BASE20/",$tab[11]))&&($tab[12]=='Actif')) {
				if($ligne_a_prendre_en_compte=="y") {
					if(($_POST['toutes_les_classes']=="y")||
						(!isset($tabindice['Classe enfant 1']))||
						((isset($tabindice['Classe enfant 1']))&&(in_array($tab[$tabindice['Classe enfant 1']], $_POST['classe'])))) {

						if(!isset($tabindice['Classe enfant 1'])) {
							$classe_courante="classe_inconnue";
						}
						else {
							$classe_courante=$tab[$tabindice['Classe enfant 1']];
						}

						if(!isset($tab_classe_parent[$classe_courante])) {
							$cpt=0;
						}
						else {
							$cpt=count($tab_classe_parent[$classe_courante]);
						}

						$tab_classe_parent[$classe_courante][$cpt]['nom_prenom']=$tab[$tabindice['Nom']]." ".$tab[$tabindice['Prénom']];
						//echo "\$tab_classe_parent[$classe_courante][$cpt]['nom_prenom']=".$tab_classe_parent[$classe_courante][$cpt]['nom_prenom']."<br />";
						$tab_classe_parent[$classe_courante][$cpt]['login_ent']=$tab[$tabindice['Login']];
						$tab_classe_parent[$classe_courante][$cpt]['mdp_ent']=$tab[$tabindice['Mot de passe']];

						if(isset($tabindice['Email'])) {
							$tab_classe_parent[$classe_courante][$cpt]['email_ent']=$tab[$tabindice['Email']];
						}

						if((isset($tabindice['Adresse']))&&(isset($tabindice['Code postal']))&&(isset($tabindice['Ville']))) {
							$tab_classe_parent[$classe_courante][$cpt]['adresse']=$tab[$tabindice['Adresse']]."<br />".$tab[$tabindice['Code postal']]." ".$tab[$tabindice['Ville']];
						}

						if((isset($tabindice['Nom enfant 1']))&&(isset($tabindice['Prénom enfant 1']))) {
							$tab_classe_parent[$classe_courante][$cpt]['resp_de']=$tab[$tabindice['Nom enfant 1']]." ".$tab[$tabindice['Prénom enfant 1']];
							if($classe_courante!='classe_inconnue') {
								$tab_classe_parent[$classe_courante][$cpt]['resp_de'].=" (".$classe_courante.")";
							}
						}
						//$cpt++;
					}
				}
			}
		}

		$saut=1;
		$nb_fiches=getSettingValue("ImpressionNombreParent");
		foreach($tab_classe_parent as $classe => $tab_parent) {
			/*
			echo "<pre>";
			print_r($tab_parent);
			echo "</pre>";
			*/
			for($loop=0;$loop<count($tab_parent);$loop++) {
				echo "<table>
	<tr>
		<th style='text-align:left;'>A l'attention de </th>
		<th>: </th>
		<td>".$tab_parent[$loop]['nom_prenom']."</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Login ENT</th>
		<th>: </th>
		<td>".$tab_parent[$loop]['login_ent']."</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Mot de passe ENT</th>
		<th>: </th>
		<td>".$tab_parent[$loop]['mdp_ent']."</td>
	</tr>";

				if(isset($tab_parent[$loop]['email_ent'])) {
					echo "
	<tr>
		<th style='text-align:left;'>Email ENT</th>
		<th>: </th>
		<td>".$tab_parent[$loop]['email_ent']."</td>
	</tr>";
				}

				if((isset($_POST['avec_adresse']))&&(isset($tab_parent[$loop]['adresse']))) {
					echo "
	<tr>
		<th style='text-align:left; vertical-align:top;'>Adresse</th>
		<th style='vertical-align:top;'>: </th>
		<td>".$tab_parent[$loop]['adresse']."</td>
	</tr>";
				}

				if(isset($tab_parent[$loop]['resp_de'])) {
					echo "
	<tr>
		<th style='text-align:left;'>Responsable notamment de</th>
		<th>: </th>
		<td>".$tab_parent[$loop]['resp_de']."</td>
	</tr>";
				}

				echo "
</table>
$impression";

				// Saut de page toutes les $nb_fiches fiches
				if ($saut == $nb_fiches) {
					echo "<p class='saut'>&nbsp;</p>\n";
					$saut = 1;
				} else {
					$saut++;
				}

				echo "
<hr class='noprint'/>";
					//}
				flush();
			}
		}

	}

	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="publipostage_personnels") {
	echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>";

	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	if(!isset($csv_file)) {

		echo "</p>

<h2 class='noprint'>Fiches bienvenue professeurs</h2>";


		echo "<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		".add_token_field()."
		<input type='hidden' name='mode' value='publipostage_personnels' />
		<input type=\"file\" size=\"65\" name=\"csv_file\" style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /><br />
		<input type='submit' value='Envoyer' />
	</fieldset>
</form>

<p><br /></p>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li>Cette rubrique est destinée à générer des Fiches Bienvenue avec compte et mot de passe de l'ENT.<br />
	<!--
		Ne seront pris en compte que les comptes ENT pour lesquels le rapprochement avec un compte Gepi est effectué.
		Ce n'est pas le cas.
		Il faudrait enregistrer davantage d'informations dans sso_table_correspondance pour s'en assurer.
	-->
	</li>
	<li>Modifier les <a href='../gestion/modify_impression.php'>Fiches Bienvenue professeurs</a></li>
	<li>Le fichier CSV attendu doit avoir le format suivant&nbsp;:<br />
	﻿Nom;Prénom;Login;Numéro de jointure;Mot de passe;Email;Classe(s);Etat;Date de désactivation<br />
ZETOFREY;Melanie;melanie.zetofrey;MENESR$12345;azerty&*;Melanie.ZETOFREY@ent27.fr;4 B, 4 D, 5 B, 6 B, 6 D;Actif
<br />
	...</li>
</ul>\n";
	}
	else {
		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=publipostage_personnels'>Fiches bienvenue personnels</a></p>

<h2 class='noprint'>Fiches bienvenue personnels</h2>";

		check_token(false);
		$fp=fopen($csv_file['tmp_name'],"r");

		$impression=getSettingValue('Impression');

		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$motif_nom_fichier="Miseajour_Motdepasse_Professeur_";
		echo "<p class='noprint'>Le fichier fourni se nomme <strong>".$csv_file['name']."</strong>";
		if(!preg_match("/$motif_nom_fichier/", $csv_file['name'])) {
			echo "<br />
<span style='color:red'>Le nom du fichier contient habituellement la chaine <strong>$motif_nom_fichier</strong>.<br />
Vous seriez-vous trompé de fichier&nbsp;?</span>";
		}
		echo "</p>\n";

		$saut=1;
		$nb_fiches=getSettingValue("ImpressionNombre");
		while (!feof($fp)) {
			$ligne = trim(fgets($fp, 4096));
			if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
				$ligne=substr($ligne,3);
			}

			if($ligne!='') {
				$tab=explode(";", ensure_utf8($ligne));
				//if(!preg_match("/^Nom;Pr/i", trim($ligne))) {
				//if((!preg_match("/^Nom;Pr/i", trim($ligne)))&&(!preg_match("/^BASE20/",$tab[11]))) {
				// On exclut également les comptes "Désactivé"
				if((!preg_match("/^Nom;Pr/i", trim($ligne)))&&(preg_match("/Actif$/", $ligne))) {
					/*
					$sql="SELECT e.* FROM eleves e, sso_table_correspondance stc WHERE stc.login_gepi=e.login AND ;";
					$res_ele=mysql_query($sql);
					if(mysql_fetch_object($res_ele)>0) {
					*/
						echo "<table>
	<tr>
		<th style='text-align:left;'>A l'attention de </th>
		<th>: </th>
		<td>".$tab[0]." ".$tab[1]."</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Login ENT</th>
		<th>: </th>
		<td>".$tab[2]."</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Mot de passe ENT</th>
		<th>: </th>
		<td>".$tab[4]."</td>
	</tr>";
						/*
						echo "
	<tr>
		<th style='text-align:left;'>Email Gepi</th>
		<th>: </th>
		<td>".$tab[4]."</td>
	</tr>";
						*/
						echo "
	<tr>
		<th style='text-align:left;'>Email ENT</th>
		<th>: </th>
		<td>".$tab[5]."</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Classe(s)</th>
		<th>: </th>
		<td>".$tab[6]."</td>
	</tr>
</table>
$impression";

						// Saut de page toutes les $nb_fiches fiches
						if ($saut == $nb_fiches) {
							echo "<p class='saut'>&nbsp;</p>\n";
							$saut = 1;
						} else {
							$saut++;
						}

						echo "<hr class='noprint'/>";
					//}

				}
			}
		}

	}

	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="forcer_logins_mdp_responsables") {
	echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
</p>";

	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	echo "
<h2>Création des comptes responsables</h2>";

	if((!isset($csv_file))||($csv_file['tmp_name']=='')) {
		echo "<p>Aucun fichier n'a été fourni.</p>";
	}
	else {
		check_token(false);
		$fp=fopen($csv_file['tmp_name'],"r");

		$impression=getSettingValue('ImpressionFicheParent');

		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			require("../lib/footer.inc.php");
			die();
		}


		$motif_nom_fichier="Miseajour_Motdepasse_Parent_";
		echo "<p class='noprint'>Le fichier fourni se nomme <strong>".$csv_file['name']."</strong>";
		if(!preg_match("/$motif_nom_fichier/", $csv_file['name'])) {
			echo "<br />
<span style='color:red'>Le nom du fichier contient habituellement la chaine <strong>$motif_nom_fichier</strong>.<br />
Vous seriez-vous trompé de fichier&nbsp;?</span>";
		}
		echo "</p>\n";


		// 20131106
		//﻿﻿Nom;Prénom;Login;Numéro de jointure;Mot de passe;Email;Adresse;Code postal;Ville;Nom enfant 1;Prénom enfant 1;Classe enfant 1;Etat;Date de désactivation<br />
		// Lire la ligne d'entête pour repérer les indices des colonnes recherchées
		$tabchamps = array("Nom", "Prénom", "Login", "Mot de passe", "Email", "Adresse", "Code postal", "Ville", "Nom enfant 1", "Prénom enfant 1", "Classe enfant 1", "Etat", "Date de désactivation");

		// Lecture de la ligne 1 et la mettre dans $temp
		$temp=fgets($fp,4096);
		//echo "$temp<br />";
		$en_tete=explode(";", trim($temp));

		$tabindice=array();

		// On range dans tabindice les indices des champs retenus
		for ($k = 0; $k < count($tabchamps); $k++) {
			//echo "<p style='text-indent:-4em;margin-left:4em'>Recherche du champ ".$tabchamps[$k]."<br />";
			for ($i = 0; $i < count($en_tete); $i++) {
				//echo "\$en_tete[$i]=$en_tete[$i]<br />";
				if (casse_mot(remplace_accents($en_tete[$i]),'min') == casse_mot(remplace_accents($tabchamps[$k]), 'min')) {
					$tabindice[$tabchamps[$k]] = $i;
					//echo "\$tabindice[$tabchamps[$k]]=$i<br />";
				}
			}
		}
		if((!isset($tabindice['Nom']))||(!isset($tabindice['Prénom']))||(!isset($tabindice['Login']))||(!isset($tabindice['Mot de passe']))) {
			echo "<p style='color:red'>La ligne d'entête ne comporte pas un des champs indispensables (<em>Nom, Prénom, Login, Mot de passe</em>).</p>";
			require("../lib/footer.inc.php");
			die();
		}


		$sql="TRUNCATE tempo4;";
		$menage=mysqli_query($GLOBALS["mysqli"], $sql);

		$cpt=0;
		$cpt2=0;
		//$classe_precedente="";
		$tab_classe_parent=array();
		while (!feof($fp)) {
			$ligne = trim(fgets($fp, 4096));
			if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
				$ligne=substr($ligne,3);
			}

			if($ligne!='') {
				$tab=explode(";", ensure_utf8($ligne));

				// On exclut la ligne Nom;Prénom
				//if(!preg_match("/^Nom;Pr/i", trim($ligne))) {
				// On exclut aussi les classes BASE2012-2013
				//if((!preg_match("/^Nom;Pr/i", trim($ligne)))&&(!preg_match("/^BASE20/",$tab[11]))) {
				// On exclut également les comptes "Désactivé"
				//if((!preg_match("/^Nom;Pr/i", trim($ligne)))&&(isset($tab[11]))&&(isset($tab[12]))&&(!preg_match("/^BASE20/",$tab[11]))&&($tab[12]=='Actif')) {

				$ligne_a_prendre_en_compte="y";
				if(preg_match("/^Nom;Pr/i", trim($ligne))) {
					$ligne_a_prendre_en_compte="n";
				}
				elseif((isset($tabindice['Classe enfant 1']))&&(preg_match("/^BASE20/",$tab[$tabindice['Classe enfant 1']]))) {
					// On exclut l'année précédente
					$ligne_a_prendre_en_compte="n";
				}
				elseif((isset($tabindice['Etat']))&&($tab[$tabindice['Etat']]!='Actif')) {
					// On exclut les comptes "Désactivé"
					$ligne_a_prendre_en_compte="n";
				}


				if($ligne_a_prendre_en_compte=="y") {

					if(!isset($tabindice['Classe enfant 1'])) {
						$classe_courante="classe_inconnue";
					}
					else {
						$classe_courante=$tab[$tabindice['Classe enfant 1']];
					}

					/*
					if($tab[11]!=$classe_precedente) {
						$cpt=0;
						$classe_precedente=$tab[11];
					}
					*/
					if(!isset($tab_classe_parent[$classe_courante])) {
						$cpt=0;
					}
					else {
						$cpt=count($tab_classe_parent[$classe_courante]);
					}

					$tab_classe_parent[$classe_courante][$cpt]['nom']=$tab[$tabindice['Nom']];
					$tab_classe_parent[$classe_courante][$cpt]['prenom']=$tab[$tabindice['Prénom']];
					$tab_classe_parent[$classe_courante][$cpt]['nom_prenom']=$tab[$tabindice['Nom']]." ".$tab[$tabindice['Prénom']];
					//echo "\$tab_classe_parent[$classe_courante][$cpt]['nom_prenom']=".$tab_classe_parent[$classe_courante][$cpt]['nom_prenom']."<br />";
					$tab_classe_parent[$classe_courante][$cpt]['login_ent']=$tab[$tabindice['Login']];
					$tab_classe_parent[$classe_courante][$cpt]['mdp_ent']=$tab[$tabindice['Mot de passe']];

					$tab_classe_parent[$classe_courante][$cpt]['email_ent']="";
					if(isset($tabindice['Email'])) {
						$tab_classe_parent[$classe_courante][$cpt]['email_ent']=$tab[$tabindice['Email']];
					}

					$tab_classe_parent[$classe_courante][$cpt]['adresse']="";
					if((isset($tabindice['Adresse']))&&(isset($tabindice['Code postal']))&&(isset($tabindice['Ville']))) {
						$tab_classe_parent[$classe_courante][$cpt]['adresse']=$tab[$tabindice['Adresse']]."<br />".$tab[$tabindice['Code postal']]." ".$tab[$tabindice['Ville']];
					}

					$tab_classe_parent[$classe_courante][$cpt]['classe']=$classe_courante;

					$tab_classe_parent[$classe_courante][$cpt]['enfant']="";
					$tab_classe_parent[$classe_courante][$cpt]['resp_de']="";
					if((isset($tabindice['Nom enfant 1']))&&(isset($tabindice['Prénom enfant 1']))) {
						$tab_classe_parent[$classe_courante][$cpt]['enfant']=$tab[$tabindice['Nom enfant 1']]." ".$tab[$tabindice['Prénom enfant 1']];
						$tab_classe_parent[$classe_courante][$cpt]['resp_de']=$tab_classe_parent[$classe_courante][$cpt]['enfant']." (".$classe_courante.")";
					}

					$tab_classe_parent[$classe_courante][$cpt]['cpt_tempo4']=$cpt2;

					$sql="INSERT INTO tempo4 SET col1='$cpt2', col2='".$tab[$tabindice['Login']]."', col3=MD5('".$tab[$tabindice['Mot de passe']]."');";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);

					$cpt2++;
				}
			}
		}

		echo "
<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	".add_token_field()."
	<input type='hidden' name='temoin_suhosin_1' value='forcer_logins_mdp_responsables' />
	<input type='hidden' name='mode' value='valider_forcer_logins_mdp_responsables' />
	<input type='hidden' name='temoin_suhosin_1' value='forcer_logins_mdp_responsables' />

<table class='boireaus boireaus_alt' summary='Tableau des responsables'>
	<tr>
		<th colspan='6'>Informations ENT</th>
		<th colspan='4'>Informations Gepi</th>
	</tr>
	<tr>
		<th>Nom prénom</th>
		<th>Adresse</th>
		<th>Enfant</th>
		<th>Classe</th>
		<th>Login</th>
		<th>Mot de passe</th>

		<th>
			Cocher
			<span id='tout_cocher_decocher' style='display:none;'>
				<br />
				<a href=\"javascript:tout_cocher()\" title='Cocher tous les parents pour lesquels un seul nom_prénom est trouvé.'><img src='../images/enabled.png' width='20' height='20' /></a>
				/
				<a href=\"javascript:tout_decocher()\" title='Tout décocher'><img src='../images/disabled.png' width='20' height='20' /></a>
			</span>
		</th>
		<th>Nom prénom</th>
		<th title=\"Existence ou non d'un compte dans la base GEPI\">C.</th>
		<th>Adresse</th>
		<th>Enfants</th>
	</tr>";
		$cpt=0;
		$ancre_doublon_ou_pas="";
		$style_css="";
		$nb_comptes_login_deja_ok=0;
		foreach($tab_classe_parent as $classe => $tab_parent) {
			/*
			echo "<pre>";
			print_r($tab_parent);
			echo "</pre>";
			*/
			for($loop=0;$loop<count($tab_parent);$loop++) {
				$rowspan="";
				$sql="SELECT * FROM resp_pers WHERE nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab_parent[$loop]['nom']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' AND prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $tab_parent[$loop]['prenom']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
				$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_resp=mysqli_num_rows($res_resp);
				if($nb_resp>1) {
					$rowspan=" rowspan='".($nb_resp+1)."'";
				}

				//==============================================================
				if($nb_resp==0) {
					// Aucun nom prénom identique trouvé
					echo "
	<tr class='white_hover'".$style_css.">
		<td$rowspan>".$tab_parent[$loop]['nom_prenom']."</td>
		<td$rowspan>".$tab_parent[$loop]['adresse']."</td>
		<td$rowspan>".$tab_parent[$loop]['enfant']."</td>
		<td$rowspan>".$tab_parent[$loop]['classe']."</td>
		<td$rowspan>".$tab_parent[$loop]['login_ent']."</td>
		<td$rowspan>".$tab_parent[$loop]['mdp_ent']."</td>";

					echo "
		<td></td>
		<td style='color:red' colspan='4'>Aucun nom prénom identique</td>
	</tr>";
					$cpt++;
				}
				//==============================================================
				elseif($nb_resp==1) {
					// Un seul nom prénom identique trouvé
					$lig_resp=mysqli_fetch_object($res_resp);

					if($lig_resp->login==$tab_parent[$loop]['login_ent']) {
						$nb_comptes_login_deja_ok++;
					}
					else {
						echo "
	<tr class='white_hover'".$style_css.">
		<td$rowspan><label for='ligne_$cpt'>".$tab_parent[$loop]['nom_prenom']."</label></td>
		<td$rowspan><label for='ligne_$cpt'>".$tab_parent[$loop]['adresse']."</label></td>
		<td$rowspan><label for='ligne_$cpt'>".$tab_parent[$loop]['enfant']."</label></td>
		<td$rowspan><label for='ligne_$cpt'>".$tab_parent[$loop]['classe']."</label></td>
		<td$rowspan><label for='ligne_$cpt'>".$tab_parent[$loop]['login_ent']."</label></td>
		<td$rowspan><label for='ligne_$cpt'>".$tab_parent[$loop]['mdp_ent']."</label></td>";

						$tab_ele=get_enfants_from_pers_id($lig_resp->pers_id, 'avec_classe');
						$chaine_ele="";
						for($loop_ele=1;$loop_ele<count($tab_ele);$loop_ele+=2) {
							if($loop_ele>1) {$chaine_ele.=",<br />";}
							$chaine_ele.=$tab_ele[$loop_ele];
						}

						$tab_adresse=get_adresse_responsable($lig_resp->pers_id);
						$chaine_adresse=$tab_adresse['en_ligne'];

						echo "
		<td><input type='checkbox' name='ligne[".$tab_parent[$loop]['cpt_tempo4']."]' id='ligne_$cpt' value='".$lig_resp->pers_id."' onchange=\"change_graisse($cpt)\" />$ancre_doublon_ou_pas</td>
		<td><label for='ligne_$cpt'><span id='nom_prenom_$cpt'>$lig_resp->civilite $lig_resp->nom $lig_resp->prenom</span></label></td>
		<td>".lien_image_compte_utilisateur($lig_resp->login, '', '_blank', 'y')."</td>
		<td><label for='ligne_$cpt'>$chaine_adresse</label></td>
		<td><label for='ligne_$cpt'>$chaine_ele</label></td>
	</tr>";
						$cpt++;
					}
				}
				//==============================================================
				else {
					// Plusieurs nom prénom identiques trouvés
					echo "
	<tr class='white_hover'".$style_css.">
		<td$rowspan>".$tab_parent[$loop]['nom_prenom']."</td>
		<td$rowspan>".$tab_parent[$loop]['adresse']."</td>
		<td$rowspan>".$tab_parent[$loop]['enfant']."</td>
		<td$rowspan>".$tab_parent[$loop]['classe']."</td>
		<td$rowspan>".$tab_parent[$loop]['login_ent']."</td>
		<td$rowspan>".$tab_parent[$loop]['mdp_ent']."</td>";

					$chaine_change_graisse="";
					for($loop_resp=0;$loop_resp<=$nb_resp;$loop_resp++) {
						if($loop_resp>0) {
							$chaine_change_graisse.=";";
						}
						$chaine_change_graisse.="change_graisse(".($cpt+$loop_resp).")";
					}

					// Ne pas associer
					echo "
		<td><input type='radio' name='ligne[".$tab_parent[$loop]['cpt_tempo4']."]' id='ligne_$cpt' value='' ";
					echo "onchange=\"$chaine_change_graisse\" ";
					echo " />$ancre_doublon_ou_pas";
					//echo $cpt;
					echo "</td>
		<td style='color:red'><label for='ligne_$cpt'>Ne pas associer</label></td>
		<td></td>
		<td></td>
	</tr>";
					$cpt++;
					$cpt_resp=0;
					while($lig_resp=mysqli_fetch_object($res_resp)) {
						/*
						if($cpt_resp>0) {
							echo "
	<tr>";
						}
						*/
						echo "
	<tr>";
						$tab_ele=get_enfants_from_pers_id($lig_resp->pers_id, 'avec_classe');
						$chaine_ele="";
						for($loop_ele=1;$loop_ele<count($tab_ele);$loop_ele+=2) {
							if($loop_ele>1) {$chaine_ele.=",<br />";}
							$chaine_ele.=$tab_ele[$loop_ele];
						}

						$tab_adresse=get_adresse_responsable($lig_resp->pers_id);
						$chaine_adresse=$tab_adresse['en_ligne'];

						// Responsable n°$cpt_resp trouvé pour le nom_prenom proposé dans le CSV
						echo "
		<td><input type='radio' name='ligne[".$tab_parent[$loop]['cpt_tempo4']."]' id='ligne_$cpt' value='".$lig_resp->pers_id."' ";
						echo "onchange=\"$chaine_change_graisse\" ";
						echo "/>$ancre_doublon_ou_pas";
						//echo $cpt;
						echo "</td>
		<td><label for='ligne_$cpt'><span id='nom_prenom_$cpt'>$lig_resp->civilite $lig_resp->nom $lig_resp->prenom</span></label></td>
		<td>".lien_image_compte_utilisateur($lig_resp->login, '', '_blank', 'y')."</td>
		<td><label for='ligne_$cpt'>$chaine_adresse</label></td>
		<td><label for='ligne_$cpt'>$chaine_ele</label></td>
	</tr>";
						$cpt_resp++;
						$cpt++;
					}
				}
				//==============================================================

				//$cpt++;
				flush();
			}
		}
		echo "
</table>

	<p><input type='submit' value='Valider' /></p>
	<input type='hidden' name='temoin_suhosin_2' value='forcer_logins_mdp_responsables' />
	<p><br /></p>

	<p><em>NOTES&nbsp;:</em></p>
	<ul>
		<li>Les comptes vont être créés dans Gepi d'après les login/mdp ENT pour les responsables sélectionnés.</li>
		".(($nb_comptes_login_deja_ok>0) ? "<li><strong>$nb_comptes_login_deja_ok comptes parents ont déjà été créés d'après le login ENT</strong> (<em>ils peuvent en revanche avoir depuis modifié leur mot de passe</em>).</li>" : "")."
		<li>Les nouveaux comptes créés sont inactifs.</li>
		<li>Le changement de mot de passe n'est pas imposé pour les nouveaux comptes.<br />
		Ce serait préférable, mais si l'accès via l'ENT est mis en place par la suite avec les comptes et mots de passe présentement mis en place, ne pas changer de mot de passe peut simplifier des choses.</li>
	</ul>

	<input type='hidden' name='temoin_suhosin_2' value='forcer_logins_mdp_responsables' />
</form>

<script type='text/javascript'>
	document.getElementById('tout_cocher_decocher').style.display='';
	/*
	document.getElementById('bouton_button_import').style.display='';
	document.getElementById('bouton_submit_import').style.display='none';
	*/

	function tout_cocher() {
		var i;
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				if(document.getElementById('ligne_'+i).getAttribute('type')=='checkbox') {
					document.getElementById('ligne_'+i).checked=true;
					change_graisse(i);
				}
			}
		}
	}

	function tout_decocher() {
		var i;
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				document.getElementById('ligne_'+i).checked=false;
				change_graisse(i);
			}
		}
	}

	function change_graisse(num) {
		if((document.getElementById('ligne_'+num))&&(document.getElementById('nom_prenom_'+num))) {
			//alert(num);
			if(document.getElementById('ligne_'+num).checked==true) {
				document.getElementById('nom_prenom_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('nom_prenom_'+num).style.fontWeight='';
			}
		}
	}
</script>
\n";
	}

	require("../lib/footer.inc.php");
	die();
}

//==================================================================================

if($mode=="envoi_mail_logins_mdp") {
	echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>";

	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	if(!isset($csv_file)) {
	echo "</p>

<h2 class='noprint'>Envoi par mail des fiches bienvenue responsables</h2>";

		echo "<p><strong style='color:red'>ATTENTION&nbsp;:</strong> Cette démarche ne fonctionne que dans le cas où les logins Gepi des responsables et leurs logins ENT coïncident (<em>et si les adresses mail sont correctement renseignées dans votre table 'resp_pers'</em>).</p>";

		// Liste des classes avec élève:
		$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) ORDER BY c.classe;";
		$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);

		$nb_classes=mysqli_num_rows($call_classes);
		if($nb_classes==0){
			echo "<p>Aucune classe avec élève affecté n'a été trouvée.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		// Affichage sur 3 colonnes
		$nb_classes_par_colonne=round($nb_classes/3);


		echo "<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		".add_token_field()."

		<p><input type='radio' name='toutes_les_classes' id='toutes_les_classes_y' value='y' checked /><label for='toutes_les_classes_y'> Générer les Fiches bienvenue pour les parents des élèves de toutes les classes</label><br />
		ou<br />
		<input type='radio' name='toutes_les_classes' id='toutes_les_classes_n' value='n' /><label for='toutes_les_classes_n'> Générer les Fiches bienvenue pour une sélection de classes<br />
		(<em>si votre navigateur peine à effectuer l'impression pour un trop grand nombre de pages par exemple</em>)</label><br />
		";
		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";

		$cpt = 0;

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while($lig_clas=mysqli_fetch_object($call_classes)) {

			//affichage 2 colonnes
			if(($cpt>0)&&(round($cpt/$nb_classes_par_colonne)==$cpt/$nb_classes_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}

			//echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='id_classe[]' id='tab_id_classe_$cpt' value='$lig_clas->id' onchange=\"change_style_classe($cpt)\" /> $lig_clas->classe</label>";
			echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='classe[]' id='tab_id_classe_$cpt' value='$lig_clas->classe' onchange=\"change_style_classe($cpt)\" /> $lig_clas->classe</label>";
			echo "<br />\n";
			$cpt++;
		}

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";

		echo "
		<br />

		<p>Veuillez fournir le fichier <strong>".getSettingValue("gepiSchoolRne")."_MiseaJour_Motdepasse_Parent_JJ_MM_AAAA_HH_MM_SS.csv</strong> généré par l'ENT.</p>
		<input type='hidden' name='mode' value='envoi_mail_logins_mdp' />
		<input type=\"file\" size=\"65\" name=\"csv_file\" style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /><br />
		<input type='submit' value='Envoyer' />
	</fieldset>
</form>

<p><br /></p>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li>Cette rubrique est destinée à générer des Fiches Bienvenue avec compte et mot de passe de l'ENT et à les envoyer à l'adresse mail saisie pour le responsable dans <a href='../responsables/index.php'>Gestion des responsables</a>.</li>
	<li>Modifier les <a href='../gestion/modify_impression.php?fiche=responsables'>Fiches Bienvenue responsables</a></li>
	<li>Le fichier CSV attendu doit avoir le format suivant&nbsp;:<br />
	﻿﻿Nom;Prénom;Login;Numéro de jointure;Mot de passe;Email;Adresse;Code postal;Ville;Nom enfant 1;Prénom enfant 1;Classe enfant 1;Etat;Date de désactivation<br />
	DUPRE;Denis;denis.dupre1;MENESR$1234567;azerty&*;Denis.DUPRE1@ent27.fr;3 RUE DES PRIMEVERES;27300;BERNAY;DUPRE;Thomas;6 A;Actif<br />
	...</li>
	<li>
		Le fichier demandé n'associe chaque parent qu'à un seul enfant.<br />
		Un parent de plusieurs enfants dans l'établissement n'apparaitra que dans les fiches bienvenue de la classe de l'enfant auquel il est associé dans le fichier.
	</li>
</ul>


<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('tab_id_classe_'+k)){
				document.getElementById('tab_id_classe_'+k).checked = mode;
				change_style_classe(k);
			}
		}
	}

	function change_style_classe(num) {
		if(document.getElementById('tab_id_classe_'+num)) {
			if(document.getElementById('tab_id_classe_'+num).checked) {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='bold';
				if(document.getElementById('toutes_les_classes_n')){
					document.getElementById('toutes_les_classes_n').checked=true;
				}
			}
			else {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";
	}
	else {
		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=envoi_mail_logins_mdp'>Envoi par mail</a></p>

<h2 class='noprint'>Envoi par mail des fiches bienvenue responsables</h2>";

		check_token(false);
		$fp=fopen($csv_file['tmp_name'],"r");

		$impression=getSettingValue('ImpressionFicheParent');

		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$motif_nom_fichier="Miseajour_Motdepasse_Parent_";
		echo "<p class='noprint'>Le fichier fourni se nomme <strong>".$csv_file['name']."</strong>";
		if(!preg_match("/$motif_nom_fichier/", $csv_file['name'])) {
			echo "<br />
<span style='color:red'>Le nom du fichier contient habituellement la chaine <strong>$motif_nom_fichier</strong>.<br />
Vous seriez-vous trompé de fichier&nbsp;?</span>";
		}
		echo "</p>\n";

		echo "<br /><p class='noprint'>Les parents pour lesquels les fiches bienvenues n'auront pas pu être envoyées par mail, apparaitront dans la page (<em>pour que vous puissiez les imprimer et les remettre manuellement</em>).<br />
		Les parents pour lesquels l'envoi aura réussi seront listés en bas de page.<br /></p><hr class='noprint'/>";

		$cpt=0;
		//$classe_precedente="";
		$tab_classe_parent=array();
		while (!feof($fp)) {
			$ligne = trim(fgets($fp, 4096));
			if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
				$ligne=substr($ligne,3);
			}

			if($ligne!='') {
				$tab=explode(";", ensure_utf8($ligne));
				//if(!preg_match("/^Nom;Pr/i", trim($ligne))) {
				//if((!preg_match("/^Nom;Pr/i", trim($ligne)))&&(!preg_match("/^BASE20/",$tab[11]))) {
				// On exclut également les comptes "Désactivé"
				//if((!preg_match("/^Nom;Pr/i", trim($ligne)))&&(!preg_match("/^BASE20/",$tab[11]))&&($tab[12]=='Actif')) {
				if((!preg_match("/^Nom;Pr/i", trim($ligne)))&&(isset($tab[11]))&&(isset($tab[12]))&&(!preg_match("/^BASE20/",$tab[11]))&&($tab[12]=='Actif')) {
					if(($_POST['toutes_les_classes']=="y")||(in_array($tab[11], $_POST['classe']))) {
						/*
						if($tab[11]!=$classe_precedente) {
							$cpt=0;
							$classe_precedente=$tab[11];
						}
						*/
						if(!isset($tab_classe_parent[$tab[11]])) {
							$cpt=0;
						}
						else {
							$cpt=count($tab_classe_parent[$tab[11]]);
						}
						$tab_classe_parent[$tab[11]][$cpt]['nom_prenom']=$tab[0]." ".$tab[1];
						//echo "\$tab_classe_parent[$tab[11]][$cpt]['nom_prenom']=".$tab_classe_parent[$tab[11]][$cpt]['nom_prenom']."<br />";
						$tab_classe_parent[$tab[11]][$cpt]['login_ent']=$tab[2];
						$tab_classe_parent[$tab[11]][$cpt]['mdp_ent']=$tab[4];
						$tab_classe_parent[$tab[11]][$cpt]['email_ent']=$tab[5];
						$tab_classe_parent[$tab[11]][$cpt]['adresse']=$tab[6]."<br />".$tab[7]." ".$tab[8];
						$tab_classe_parent[$tab[11]][$cpt]['resp_de']=$tab[9]." ".$tab[10]." (".$tab[11].")";

						$sql="SELECT mel FROM resp_pers WHERE login='".$tab[2]."' AND mel LIKE '%@%';";
						$res_mel=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_mel)>0) {
							$mel=old_mysql_result($res_mel, 0, 'mel');
							if(check_mail($mel, "", "y")) {
								$tab_classe_parent[$tab[11]][$cpt]['email_gepi']=$mel;
							}
						}

						//$cpt++;
					}
				}
			}
		}

		$saut=1;
		$nb_fiches=getSettingValue("ImpressionNombreParent");

		$tab_envoi_reussi=array();
		foreach($tab_classe_parent as $classe => $tab_parent) {
			/*
			echo "<pre>";
			print_r($tab_parent);
			echo "</pre>";
			*/
			for($loop=0;$loop<count($tab_parent);$loop++) {
				$chaine="<table>
	<tr>
		<th style='text-align:left;'>A l'attention de </th>
		<th>: </th>
		<td>".$tab_parent[$loop]['nom_prenom']."</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Login ENT</th>
		<th>: </th>
		<td>".$tab_parent[$loop]['login_ent']."</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Mot de passe ENT</th>
		<th>: </th>
		<td>".$tab_parent[$loop]['mdp_ent']."</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Email ENT</th>
		<th>: </th>
		<td>".$tab_parent[$loop]['email_ent']."</td>
	</tr>
	<tr>
		<th style='text-align:left; vertical-align:top;'>Adresse</th>
		<th style='vertical-align:top;'>: </th>
		<td>".$tab_parent[$loop]['adresse']."</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Responsable notamment de</th>
		<th>: </th>
		<td>".$tab_parent[$loop]['resp_de']."</td>
	</tr>
</table>
$impression";

				if((isset($tab_parent[$loop]['email_gepi']))&&($envoi=envoi_mail("Compte Gepi", "Bonjour(soir),\n".$chaine, $tab_parent[$loop]['email_gepi'], "", "html"))) {
					$tab_envoi_reussi[]=$tab_parent[$loop]['nom_prenom']." (".$tab_parent[$loop]['email_gepi'].") parent de ".$tab_parent[$loop]['resp_de'];
				}
				else {
					echo $chaine."";

					// Saut de page toutes les $nb_fiches fiches
					if ($saut == $nb_fiches) {
						echo "<p class='saut'>&nbsp;</p>\n";
						$saut = 1;
					} else {
						$saut++;
					}

					echo "<hr class='noprint'/>";
				}
				flush();
			}
		}


		$nb_envois=count($tab_envoi_reussi);
		echo "<hr class='noprint' /><p class='noprint'><strong>Récapitulatif&nbsp;:</strong> ".$nb_envois." mail ont été envoyés avec succès.</p>";
		if($nb_envois>0) {
			echo "<p class='noprint'>Liste des responsables contactés par mail&nbsp;:<br />";
			for($loop=0;$loop<$nb_envois;$loop++) {
				echo $tab_envoi_reussi[$loop]."<br />";
			}
		}
	}

	require("../lib/footer.inc.php");
	die();
}

//==================================================================================

echo "<p style='color:red'>Mode non encore développé</p>\n";
echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
