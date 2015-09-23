<?php
/**
 *
 *
 * Copyright 2015 Stephane Boireau
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_discipline/saisie_pointages.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_discipline/saisie_pointages.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Discipline: Pointages petits incidents',
statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if(!getSettingAOui("active_mod_discipline")) {
	die("Le module n'est pas activé.");
}

if(!getSettingAOui("active_mod_disc_pointage")) {
	die("Le dispositif de pointages disciplinaires n'est pas activé.");
}

/*
$sql="CREATE TABLE IF NOT EXISTS sp_saisies (
id int(11) NOT NULL AUTO_INCREMENT,
id_type int(11) NOT NULL,
login VARCHAR(50) NOT NULL default '',
date_sp datetime NOT NULL default '0000-00-00 00:00:00',
commentaire text NOT NULL,
created_at datetime NOT NULL default '0000-00-00 00:00:00',
created_by VARCHAR(50) NOT NULL default '',
deleted_at datetime NOT NULL default '0000-00-00 00:00:00',
deleted_by VARCHAR(50) NOT NULL default '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
*/
$sql="CREATE TABLE IF NOT EXISTS sp_saisies (
id int(11) NOT NULL AUTO_INCREMENT,
id_type int(11) NOT NULL,
login VARCHAR(50) NOT NULL default '',
date_sp datetime NOT NULL default '0000-00-00 00:00:00',
commentaire text NOT NULL,
created_at datetime NOT NULL default '0000-00-00 00:00:00',
created_by VARCHAR(50) NOT NULL DEFAULT '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS sp_types_saisies (
id_type int(11) NOT NULL AUTO_INCREMENT,
nom VARCHAR(255) NOT NULL default '',
description TEXT NOT NULL,
PRIMARY KEY (id_type)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create=mysqli_query($GLOBALS["mysqli"], $sql);


/*
$sql="CREATE TABLE IF NOT EXISTS sp_communication (
id int(11) NOT NULL AUTO_INCREMENT,
id_type int(11) NOT NULL,
seuil int(11) NOT NULL,
mail VARCHAR(255) NOT NULL default '',
observation VARCHAR(255) NOT NULL default '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create=mysqli_query($GLOBALS["mysqli"], $sql);
*/

$tab_type_pointage_discipline=get_tab_type_pointage_discipline();

if(count($tab_type_pointage_discipline)==0) {
	$sql="INSERT INTO sp_types_saisies SET nom='Travail', description='Travail non fait';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	$sql="INSERT INTO sp_types_saisies SET nom='Matériel', description='Matériel manquant';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	$sql="INSERT INTO sp_types_saisies SET nom='Comportement', description='Comportement gênant';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$display_date=isset($_POST['display_date']) ? $_POST['display_date'] : (isset($_GET['display_date']) ? $_GET['display_date'] : NULL);
$id_creneau=isset($_POST['id_creneau']) ? $_POST['id_creneau'] : (isset($_GET['id_creneau']) ? $_GET['id_creneau'] : NULL);

$tab_creneaux=get_heures_debut_fin_creneaux();

// On suppose pour le moment $mode=='groupe'
if((isset($_POST['validation_saisie']))&&(isset($id_creneau))&&(isset($tab_creneaux[$id_creneau]))&&(isset($display_date))&&(preg_match("#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#", $display_date))&&
(
	(($mode=="groupe")&&(isset($id_groupe)))||
	(($mode=="classe")&&(isset($id_classe)))
)) {
	check_token();

	$msg="";

	$saisie=isset($_POST['saisie']) ? $_POST['saisie'] : array();
	$commentaire=isset($_POST['commentaire']) ? $_POST['commentaire'] : array();

	// Formater l'horaire d'après jour et créneau
	$tmp_tab=explode("/", $display_date);
	$date_sp=$tmp_tab[2]."-".$tmp_tab[1]."-".$tmp_tab[0];
	$date_sp.=" ".$tab_creneaux[$id_creneau]['debut'];

	$jour_date_sp=$tmp_tab[0];
	$mois_date_sp=$tmp_tab[1];
	$annee_date_sp=$tmp_tab[2];

	$instant_mysql_courant=strftime("%Y-%m-%d %H:%M:%S");


	$tab_seuil_periode=array();
	$sql="SELECT * FROM sp_seuils WHERE periode='y' ORDER BY seuil, type;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig=mysqli_fetch_assoc($res)) {
		$tab_seuil_periode[$lig['seuil']][]=$lig;
	}
	$tab_seuil_annuel=array();
	$sql="SELECT * FROM sp_seuils WHERE periode='n' ORDER BY seuil, type;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig=mysqli_fetch_assoc($res)) {
		$tab_seuil_annuel[$lig['seuil']][]=$lig;
	}

	$numero_periode=array();
	$tab_ele_clas=array();
	$tab_pp=array();
	if($mode=='classe') {
		// Trouver les dates de début et fin de la période courante pour calculer le nombre de pointages sur la période... et le total
		//$ts=gmstrftime("%s");
		$ts=gmmktime (12, 0, 0, $mois_date_sp, $jour_date_sp, $annee_date_sp);
		//$sql="SELECT e.* FROM edt_calendrier e WHERE (classe_concerne_calendrier LIKE '%;$id_classe;%' OR classe_concerne_calendrier LIKE '$id_classe;%') AND etabferme_calendrier='1' AND '$ts'<fin_calendrier_ts AND '$ts'>debut_calendrier_ts;";
		$sql="SELECT e.* FROM edt_calendrier e, periodes p WHERE (classe_concerne_calendrier LIKE '%;$id_classe;%' OR classe_concerne_calendrier LIKE '$id_classe;%') AND etabferme_calendrier='1' AND '$ts'<fin_calendrier_ts AND '$ts'>debut_calendrier_ts AND e.numero_periode=p.num_periode AND p.id_classe='$id_classe';";
		//echo htmlentities($sql)."<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			// On ne fait en principe qu'un seul tour dans la boucle
			while($lig=mysqli_fetch_assoc($res)) {
				$tab_per[$id_classe]=$lig;
				$numero_periode[$id_classe]=$lig['numero_periode'];

				$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='".$lig['numero_periode']."';";
				//echo "$sql<br />";
				$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_ele=mysqli_fetch_object($res_ele)) {
					$tab_ele_clas[$lig_ele->login]=$id_classe;
				}
			}
		}

		$tab_pp[$id_classe]=get_tab_prof_suivi($id_classe);
	}
	else {
		// Relever les dates pour les différentes classes du groupe
		//$ts=gmstrftime("%s");
		$ts=gmmktime (12, 0, 0, $mois_date_sp, $jour_date_sp, $annee_date_sp);
		$current_group=get_group($id_groupe, array('classes', 'periodes', 'eleves'));
		/*
		echo "<pre>";
		print_r($current_group);
		echo "</pre>";
		*/
		for($loop=0;$loop<count($current_group["classes"]["list"]);$loop++) {
			$current_id_classe=$current_group["classes"]["list"][$loop];

			$tab_pp[$current_id_classe]=get_tab_prof_suivi($current_id_classe);

			//$sql="SELECT e.* FROM edt_calendrier e WHERE (classe_concerne_calendrier LIKE '%;$current_id_classe;%' OR classe_concerne_calendrier LIKE '$current_id_classe;%') AND etabferme_calendrier='1' AND '$ts'<fin_calendrier_ts AND '$ts'>debut_calendrier_ts;";
			$sql="SELECT e.* FROM edt_calendrier e, periodes p WHERE (classe_concerne_calendrier LIKE '%;$current_id_classe;%' OR classe_concerne_calendrier LIKE '$current_id_classe;%') AND etabferme_calendrier='1' AND '$ts'<fin_calendrier_ts AND '$ts'>debut_calendrier_ts AND e.numero_periode=p.num_periode AND p.id_classe='$current_id_classe';";
			//echo htmlentities($sql)."<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				// On ne fait en principe qu'un seul tour dans la boucle
				while($lig=mysqli_fetch_assoc($res)) {
					$tab_per[$current_id_classe]=$lig;
					$numero_periode[$current_id_classe]=$lig['numero_periode'];

					//echo "Test de \$current_group[\"eleves\"][".$lig['numero_periode']."][\"telle_classe\"][$current_id_classe]<br />";

					// Dans le cas $mode=='groupe', pour trouver la classe de l'élève, faire un relevé dès ce stade pour ne pas faire une requête par élève
					if(!isset($current_group["eleves"][$lig['numero_periode']]["telle_classe"][$current_id_classe])) {
						$msg.="La liste des élèves de la classe ".get_nom_classe($current_id_classe)." n'a pas été trouvée.<br />Les éventuels seuils définis par période ne pourront pas être traités.<br />";
					}
					else {
						//$tab_ele_clas[$current_id_classe]=$current_group["eleves"][$lig['numero_periode']]["telle_classe"][$current_id_classe];
						for($loop2=0;$loop2<count($current_group["eleves"][$lig['numero_periode']]["telle_classe"][$current_id_classe]);$loop2++) {
							$tab_ele_clas[$current_group["eleves"][$lig['numero_periode']]["telle_classe"][$current_id_classe][$loop2]]=$current_id_classe;
						}
					}
				}
			}
		}
	}

	/*
	echo "\$tab_ele_clas<pre>";
	print_r($tab_ele_clas);
	echo "</pre>";
	*/

	$tab_saisies_effectuees=array();
	foreach($saisie as $key => $saisie_courante) {
		$tmp_tab=explode("|", $saisie_courante);
		if(isset($tmp_tab[1])) {
			$login_ele=$tmp_tab[0];
			$id_type=$tmp_tab[1];
			$nom_prenom_eleve=get_nom_prenom_eleve($login_ele);

			if(isset($tab_type_pointage_discipline['id_type'][$id_type])) {
				$sql="SELECT * FROM sp_saisies WHERE id_type='$id_type' AND login='$login_ele' AND date_sp='$date_sp';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
//										commentaire='".mysqli_real_escape_string($GLOBALS['mysqli'], $commentaire[$key])."',
					$sql="INSERT INTO sp_saisies SET id_type='$id_type', 
										commentaire='".$commentaire[$key]."',
										login='$login_ele', 
										date_sp='$date_sp', 
										created_by='".$_SESSION['login']."', 
										created_at='".$instant_mysql_courant."';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if($insert) {
						$tab_saisies_effectuees[]=mysqli_insert_id($GLOBALS['mysqli']);

						if(isset($tab_ele_clas[$login_ele])) {
							$current_id_classe=$tab_ele_clas[$login_ele];

							// Pour les actions seuils par période
							if(isset($tab_per[$current_id_classe])) {
								$sql="SELECT * FROM sp_saisies WHERE login='".$login_ele."' AND id_type='$id_type' AND date_sp>='".$tab_per[$current_id_classe]['jourdebut_calendrier']." ".$tab_per[$current_id_classe]['heuredebut_calendrier']."' AND date_sp<='".$tab_per[$current_id_classe]['jourfin_calendrier']." ".$tab_per[$current_id_classe]['heurefin_calendrier']."';";
								//echo "$sql<br />";
								$res_compte=mysqli_query($GLOBALS["mysqli"], $sql);
								$nb_pointages=mysqli_num_rows($res_compte);
								if(array_key_exists($nb_pointages, $tab_seuil_periode)) {
									for($loop=0;$loop<count($tab_seuil_periode[$nb_pointages]);$loop++) {
										if($tab_seuil_periode[$nb_pointages][$loop]['type']=='mail') {

											$tab_u=array();
											$cpt_u=0;
											if($tab_seuil_periode[$nb_pointages][$loop]['professeur_principal']=='y') {
												for($loop_pp=0;$loop_pp<count($tab_pp[$current_id_classe]);$loop_pp++) {
													$sql="SELECT civilite, nom, prenom, email FROM utilisateurs u WHERE login='".$tab_pp[$current_id_classe][$loop_pp]."';";
													$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
													while($lig_u=mysqli_fetch_object($res_u)) {
														$tab_u[$cpt_u]['civilite']=$lig_u->civilite;
														$tab_u[$cpt_u]['nom']=$lig_u->nom;
														$tab_u[$cpt_u]['prenom']=$lig_u->prenom;
														$tab_u[$cpt_u]['email']=$lig_u->email;
														$cpt_u++;
													}
												}
											}


											$chaine_statuts="";
											if($tab_seuil_periode[$nb_pointages][$loop]['administrateur']=='y') {
												$chaine_statuts.="statut='administrateur' OR ";
											}
											if($tab_seuil_periode[$nb_pointages][$loop]['cpe']=='y') {
												$chaine_statuts.="statut='cpe' OR ";
											}
											if($tab_seuil_periode[$nb_pointages][$loop]['scolarite']=='y') {
												$chaine_statuts.="statut='scolarite' OR ";
											}
											$sql="SELECT civilite, nom, prenom, email FROM utilisateurs u WHERE (".preg_replace("/ OR $/","",$chaine_statuts).") AND email!='' AND etat='actif';";
											$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
											while($lig_u=mysqli_fetch_object($res_u)) {
												$tab_u[$cpt_u]['civilite']=$lig_u->civilite;
												$tab_u[$cpt_u]['nom']=$lig_u->nom;
												$tab_u[$cpt_u]['prenom']=$lig_u->prenom;
												$tab_u[$cpt_u]['email']=$lig_u->email;
												$cpt_u++;
											}

											$tab_deja=array();
											for($loop_u=0;$loop_u<count($tab_u);$loop_u++) {
												if((!in_array($tab_u[$loop_u]['email'], $tab_deja))&&(check_mail($tab_u[$loop_u]['email']))) {

													$texte_mail="Bonjour ".$tab_u[$loop_u]['civilite']." ".$tab_u[$loop_u]['nom']." ".$tab_u[$loop_u]['prenom'].",

Le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") a été atteint le ".strftime("%a %d/%m/%Y")." par ".$nom_prenom_eleve." pour la période ".$tab_per[$current_id_classe]['nom_calendrier'].".

Cordialement.
-- 
Message automatique Gepi.";
													$sujet_mail="[Gepi]: ".$tab_type_pointage_discipline['id_type'][$id_type]['description']." ($nom_prenom_eleve)";

													$tab_param_mail=array();
													$headers = "";
													//if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
													//	$headers.="Reply-to:".$_SESSION['email']."\r\n";
													//	$tab_param_mail['replyto']=$_SESSION['email'];
													//}

													// On envoie le mail
													$envoi = envoi_mail($sujet_mail, $texte_mail, $tab_u[$loop_u]['email'], $headers, "plain", $tab_param_mail);
													$tab_deja[]=$tab_u[$loop_u]['email'];

												}
											}

											if($tab_seuil_periode[$nb_pointages][$loop]['responsable']=='y') {
												$tab_resp=get_resp_from_ele_login($login_ele,"yy");
												for($loop_resp=0;$loop_resp<count($tab_resp);$loop_resp++) {
													if((!in_array($tab_resp[$loop_resp]['mel'], $tab_deja))&&(check_mail($tab_resp[$loop_resp]['mel']))) {

														$texte_mail="Bonjour ".$tab_resp[$loop_resp]['civilite']." ".$tab_resp[$loop_resp]['nom']." ".$tab_resp[$loop_resp]['prenom'].",

Le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") a été atteint le ".strftime("%a %d/%m/%Y")." par ".$nom_prenom_eleve." pour la période ".$tab_per[$current_id_classe]['nom_calendrier'].".

Cordialement.
-- 
Message automatique Gepi.";
														$sujet_mail="[Gepi]: ".$tab_type_pointage_discipline['id_type'][$id_type]['description']." ($nom_prenom_eleve)";

														$tab_param_mail=array();
														$headers = "";
														//if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
														//	$headers.="Reply-to:".$_SESSION['email']."\r\n";
														//	$tab_param_mail['replyto']=$_SESSION['email'];
														//}

														// On envoie le mail
														$envoi = envoi_mail($sujet_mail, $texte_mail, $tab_resp[$loop_resp]['mel'], $headers, "plain", $tab_param_mail);
														$tab_deja[]=$tab_resp[$loop_resp]['mel'];
													}

													if($tab_resp[$loop_resp]['login']!="") {
														$mail_u=get_mail_user($tab_resp[$loop_resp]['login']);

														if((!in_array($mail_u, $tab_deja))&&(check_mail($mail_u))) {

															$texte_mail="Bonjour ".$tab_resp[$loop_resp]['civilite']." ".$tab_resp[$loop_resp]['nom']." ".$tab_resp[$loop_resp]['prenom'].",

Le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") a été atteint le ".strftime("%a %d/%m/%Y")." par ".$nom_prenom_eleve." pour la période ".$tab_per[$current_id_classe]['nom_calendrier'].".

Cordialement.
-- 
Message automatique Gepi.";
												$sujet_mail="[Gepi]: ".$tab_type_pointage_discipline['id_type'][$id_type]['description']." ($nom_prenom_eleve)";

															$tab_param_mail=array();
															$headers = "";
															//if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
															//	$headers.="Reply-to:".$_SESSION['email']."\r\n";
															//	$tab_param_mail['replyto']=$_SESSION['email'];
															//}

															// On envoie le mail
															$envoi = envoi_mail($sujet_mail, $texte_mail, $mail_u, $headers, "plain", $tab_param_mail);
															$tab_deja[]=$mail_u;
														}
													}
												}
											}

											if($tab_seuil_periode[$nb_pointages][$loop]['eleve']=='y') {

												$texte_mail="Bonjour ".$nom_prenom_eleve.",

Vous avez atteint le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") le ".strftime("%a %d/%m/%Y")." pour la période ".$tab_per[$current_id_classe]['nom_calendrier'].".
Il faudrait veiller à réagir.


Cordialement.
-- 
Message automatique Gepi.";
												$sujet_mail="[Gepi]: ".$tab_type_pointage_discipline['id_type'][$id_type]['description']." ($nom_prenom_eleve)";

												$tab_param_mail=array();
												$headers = "";
												//if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
												//	$headers.="Reply-to:".$_SESSION['email']."\r\n";
												//	$tab_param_mail['replyto']=$_SESSION['email'];
												//}

												$sql="(SELECT email FROM eleves WHERE login='".$login_ele."') UNION (SELECT email FROM utilisateurs WHERE login='".$login_ele."');";
												$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
												while($lig_u=mysqli_fetch_object($res_u)) {
													if((!in_array($mail_u, $tab_deja))&&(check_mail($mail_u))) {
														// On envoie le mail
														$envoi = envoi_mail($sujet_mail, $texte_mail, $lig_u->email, $headers, "plain", $tab_param_mail);
														$tab_deja[]=$tab_u[$loop_u]['email'];
													}
												}
											}
										}
										elseif($tab_seuil_periode[$nb_pointages][$loop]['type']=='message') {

											$tab_u=array();

											if($tab_seuil_periode[$nb_pointages][$loop]['professeur_principal']=='y') {
												$tab_u=$tab_pp[$current_id_classe];
											}

											$chaine_statuts="";
											if($tab_seuil_periode[$nb_pointages][$loop]['administrateur']=='y') {
												$chaine_statuts.="statut='administrateur' OR ";
											}
											if($tab_seuil_periode[$nb_pointages][$loop]['cpe']=='y') {
												$chaine_statuts.="statut='cpe' OR ";
											}
											if($tab_seuil_periode[$nb_pointages][$loop]['scolarite']=='y') {
												$chaine_statuts.="statut='scolarite' OR ";
											}
											$sql="SELECT login FROM utilisateurs u WHERE (".preg_replace("/ OR $/","",$chaine_statuts).") AND etat='actif';";
											$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
											while($lig_u=mysqli_fetch_object($res_u)) {
												$tab_u[]=$lig_u->login;
											}

											for($loop_u=0;$loop_u<count($tab_u);$loop_u++) {
												$contenu_cor=mysqli_real_escape_string($GLOBALS['mysqli'], "Le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") a été atteint le ".strftime("%a %d/%m/%Y")." par <a href='$gepiPath/eleves/visu_eleve.php?ele_login='>".$nom_prenom_eleve."</a> pour la période ".$tab_per[$current_id_classe]['nom_calendrier'].".");
												$id_message=set_message2($contenu_cor,time(),time()+3600*24*7,time()+3600*24*7,"_",$tab_u[$loop_u]);
												ajout_bouton_supprimer_message($contenu_cor,$id_message);
											}

											if($tab_seuil_periode[$nb_pointages][$loop]['responsable']=='y') {
												$tab_resp=get_resp_from_ele_login($login_ele,"yy");
												for($loop_resp=0;$loop_resp<count($tab_resp);$loop_resp++) {
													$contenu_cor=mysqli_real_escape_string($GLOBALS['mysqli'], "Le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") a été atteint le ".strftime("%a %d/%m/%Y")." par ".$nom_prenom_eleve." pour la période ".$tab_per[$current_id_classe]['nom_calendrier'].".");
													$id_message=set_message2($contenu_cor,time(),time()+3600*24*7,time()+3600*24*7,"_",$tab_resp[$loop_resp]['login']);
													//if($id_message) {
														ajout_bouton_supprimer_message($contenu_cor,$id_message);
													//}
													//else {
													//	$msg.="Echec de l'ajout du lien de suppression du message pour ".$tab_resp[$loop_resp]['login'].".<br />";
													//}
												}
											}

											if($tab_seuil_periode[$nb_pointages][$loop]['eleve']=='y') {
												$contenu_cor=mysqli_real_escape_string($GLOBALS['mysqli'], "Vous avez atteint le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") le ".strftime("%a %d/%m/%Y")." pour la période ".$tab_per[$current_id_classe]['nom_calendrier'].".<br />Il faudrait réagir.<br />");
												$id_message=set_message2($contenu_cor,time(),time()+3600*24*7,time()+3600*24*7,"_",$login_ele);
												ajout_bouton_supprimer_message($contenu_cor,$id_message);
											}

										}
										else {
											// Pas d'autre type actuellement
											$msg.="Le type d'action seuil ".$tab_seuil_periode[$nb_pointages][$loop]['type']." est inconnu.<br />";
										}
									}
								}
							}



							// Pour les actions seuils annuels

							$sql="SELECT * FROM sp_saisies WHERE login='".$login_ele."' AND id_type='$id_type';";
							//echo "$sql<br />";
							$res_compte=mysqli_query($GLOBALS["mysqli"], $sql);
							$nb_pointages=mysqli_num_rows($res_compte);
							if(array_key_exists($nb_pointages, $tab_seuil_annuel)) {
								for($loop=0;$loop<count($tab_seuil_annuel[$nb_pointages]);$loop++) {
									if($tab_seuil_annuel[$nb_pointages][$loop]['type']=='mail') {

										$tab_u=array();
										$cpt_u=0;
										if($tab_seuil_annuel[$nb_pointages][$loop]['professeur_principal']=='y') {
											for($loop_pp=0;$loop_pp<count($tab_pp[$current_id_classe]);$loop_pp++) {
												$sql="SELECT civilite, nom, prenom, email FROM utilisateurs u WHERE login='".$tab_pp[$current_id_classe][$loop_pp]."';";
												$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
												while($lig_u=mysqli_fetch_object($res_u)) {
													$tab_u[$cpt_u]=array($lig_u->civilite, $lig_u->nom, $lig_u->prenom, $lig_u->email);
													$cpt_u++;
												}
											}
										}


										$chaine_statuts="";
										if($tab_seuil_annuel[$nb_pointages][$loop]['administrateur']=='y') {
											$chaine_statuts.="statut='administrateur' OR ";
										}
										if($tab_seuil_annuel[$nb_pointages][$loop]['cpe']=='y') {
											$chaine_statuts.="statut='cpe' OR ";
										}
										if($tab_seuil_annuel[$nb_pointages][$loop]['scolarite']=='y') {
											$chaine_statuts.="statut='scolarite' OR ";
										}
										$sql="SELECT civilite, nom, prenom, email FROM utilisateurs u WHERE (".preg_replace("/ OR $/","",$chaine_statuts).") AND email!='' AND etat='actif';";
										$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
										while($lig_u=mysqli_fetch_object($res_u)) {
											$tab_u[$cpt_u]=array($lig_u->civilite, $lig_u->nom, $lig_u->prenom, $lig_u->email);
											$cpt_u++;
										}

										$tab_deja=array();
										for($loop_u=0;$loop_u<count($tab_u);$loop_u++) {
											if((!in_array($tab_u[$loop_u]['email'], $tab_deja))&&(check_mail($tab_u[$loop_u]['email']))) {

												$texte_mail="Bonjour ".$tab_u[$loop_u]['civilite']." ".$tab_u[$loop_u]['nom']." ".$tab_u[$loop_u]['prenom'].",

Le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") a été atteint le ".strftime("%a %d/%m/%Y")." par ".$nom_prenom_eleve.".

Cordialement.
-- 
Message automatique Gepi.";
												$sujet_mail="[Gepi]: ".$tab_type_pointage_discipline['id_type'][$id_type]['description']." ($nom_prenom_eleve)";

												$tab_param_mail=array();
												$headers = "";
												//if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
												//	$headers.="Reply-to:".$_SESSION['email']."\r\n";
												//	$tab_param_mail['replyto']=$_SESSION['email'];
												//}

												// On envoie le mail
												$envoi = envoi_mail($sujet_mail, $texte_mail, $tab_u[$loop_u]['email'], $headers, "plain", $tab_param_mail);
												$tab_deja[]=$tab_u[$loop_u]['email'];

											}
										}

										if($tab_seuil_annuel[$nb_pointages][$loop]['responsable']=='y') {
											$tab_resp=get_resp_from_ele_login($login_ele,"yy");
											for($loop_resp=0;$loop_resp<count($tab_resp);$loop_resp++) {
												if((!in_array($tab_resp[$loop_resp]['mel'], $tab_deja))&&(check_mail($tab_resp[$loop_resp]['mel']))) {

													$texte_mail="Bonjour ".$tab_resp[$loop_resp]['civilite']." ".$tab_resp[$loop_resp]['nom']." ".$tab_resp[$loop_resp]['prenom'].",

Le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") a été atteint le ".strftime("%a %d/%m/%Y")." par ".$nom_prenom_eleve.".

Cordialement.
-- 
Message automatique Gepi.";
													$sujet_mail="[Gepi]: ".$tab_type_pointage_discipline['id_type'][$id_type]['description']." ($nom_prenom_eleve)";

													$tab_param_mail=array();
													$headers = "";
													//if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
													//	$headers.="Reply-to:".$_SESSION['email']."\r\n";
													//	$tab_param_mail['replyto']=$_SESSION['email'];
													//}

													// On envoie le mail
													$envoi = envoi_mail($sujet_mail, $texte_mail, $tab_resp[$loop_resp]['mel'], $headers, "plain", $tab_param_mail);
													$tab_deja[]=$tab_resp[$loop_resp]['mel'];
												}

												if($tab_resp[$loop_resp]['login']!="") {
													$mail_u=get_mail_user($tab_resp[$loop_resp]['login']);

													if((!in_array($mail_u, $tab_deja))&&(check_mail($mail_u))) {

														$texte_mail="Bonjour ".$tab_resp[$loop_resp]['civilite']." ".$tab_resp[$loop_resp]['nom']." ".$tab_resp[$loop_resp]['prenom'].",

Le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") a été atteint le ".strftime("%a %d/%m/%Y")." par ".$nom_prenom_eleve.".

Cordialement.
-- 
Message automatique Gepi.";
											$sujet_mail="[Gepi]: ".$tab_type_pointage_discipline['id_type'][$id_type]['description']." ($nom_prenom_eleve)";

														$tab_param_mail=array();
														$headers = "";
														//if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
														//	$headers.="Reply-to:".$_SESSION['email']."\r\n";
														//	$tab_param_mail['replyto']=$_SESSION['email'];
														//}

														// On envoie le mail
														$envoi = envoi_mail($sujet_mail, $texte_mail, $mail_u, $headers, "plain", $tab_param_mail);
														$tab_deja[]=$mail_u;
													}
												}
											}
										}

										if($tab_seuil_annuel[$nb_pointages][$loop]['eleve']=='y') {

											$texte_mail="Bonjour ".$nom_prenom_eleve.",

Vous avez atteint le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") le ".strftime("%a %d/%m/%Y").".
Il faudrait veiller à réagir.


Cordialement.
-- 
Message automatique Gepi.";
											$sujet_mail="[Gepi]: ".$tab_type_pointage_discipline['id_type'][$id_type]['description']." ($nom_prenom_eleve)";

											$tab_param_mail=array();
											$headers = "";
											//if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
											//	$headers.="Reply-to:".$_SESSION['email']."\r\n";
											//	$tab_param_mail['replyto']=$_SESSION['email'];
											//}

											$sql="(SELECT email FROM eleves WHERE login='".$login_ele."') UNION (SELECT email FROM utilisateurs WHERE login='".$login_ele."');";
											$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
											while($lig_u=mysqli_fetch_object($res_u)) {
												if((!in_array($mail_u, $tab_deja))&&(check_mail($mail_u))) {
													// On envoie le mail
													$envoi = envoi_mail($sujet_mail, $texte_mail, $lig_u->email, $headers, "plain", $tab_param_mail);
													$tab_deja[]=$tab_u[$loop_u]['email'];
												}
											}
										}
									}
									elseif($tab_seuil_annuel[$nb_pointages][$loop]['type']=='message') {

										$tab_u=array();

										if($tab_seuil_annuel[$nb_pointages][$loop]['professeur_principal']=='y') {
											$tab_u=$tab_pp[$current_id_classe];
										}

										$chaine_statuts="";
										if($tab_seuil_annuel[$nb_pointages][$loop]['administrateur']=='y') {
											$chaine_statuts.="statut='administrateur' OR ";
										}
										if($tab_seuil_annuel[$nb_pointages][$loop]['cpe']=='y') {
											$chaine_statuts.="statut='cpe' OR ";
										}
										if($tab_seuil_annuel[$nb_pointages][$loop]['scolarite']=='y') {
											$chaine_statuts.="statut='scolarite' OR ";
										}
										$sql="SELECT login FROM utilisateurs u WHERE (".preg_replace("/ OR $/","",$chaine_statuts).") AND etat='actif';";
										$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
										while($lig_u=mysqli_fetch_object($res_u)) {
											$tab_u[]=$lig_u->login;
										}

										for($loop_u=0;$loop_u<count($tab_u);$loop_u++) {
											$contenu_cor=mysqli_real_escape_string($GLOBALS['mysqli'], "Le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") a été atteint le ".strftime("%a %d/%m/%Y")." par <a href='$gepiPath/eleves/visu_eleve.php?ele_login='>".$nom_prenom_eleve."</a>.");
											$id_message=set_message2($contenu_cor,time(),time()+3600*24*7,time()+3600*24*7,"_",$tab_u[$loop_u]);
											ajout_bouton_supprimer_message($contenu_cor,$id_message);
										}

										if($tab_seuil_annuel[$nb_pointages][$loop]['responsable']=='y') {
											$tab_resp=get_resp_from_ele_login($login_ele,"yy");
											for($loop_resp=0;$loop_resp<count($tab_resp);$loop_resp++) {
												$contenu_cor=mysqli_real_escape_string($GLOBALS['mysqli'], "Le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") a été atteint le ".strftime("%a %d/%m/%Y")." par ".$nom_prenom_eleve.".");
												$id_message=set_message2($contenu_cor,time(),time()+3600*24*7,time()+3600*24*7,"_",$tab_resp[$loop_resp]['login']);
												ajout_bouton_supprimer_message($contenu_cor,$id_message);
											}
										}

										if($tab_seuil_annuel[$nb_pointages][$loop]['eleve']=='y') {
											$contenu_cor=mysqli_real_escape_string($GLOBALS['mysqli'], "Vous avez atteint le seuil de ".$nb_pointages." ".$tab_type_pointage_discipline['id_type'][$id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$id_type]['description'].") le ".strftime("%a %d/%m/%Y").".<br />Il faudrait réagir.<br />");
											$id_message=set_message2($contenu_cor,time(),time()+3600*24*7,time()+3600*24*7,"_",$login_ele);
											ajout_bouton_supprimer_message($contenu_cor,$id_message);
										}

									}
									else {
										// Pas d'autre type actuellement
										$msg.="Le type d'action seuil ".$tab_seuil_annuel[$nb_pointages][$loop]['type']." est inconnu.<br />";
									}
								}
							}

						}
					}
					else {
						$msg.="Erreur lors de l'enregistrement d'une saisie de type n°$id_type (".$tab_type_pointage_discipline['id_type'][$id_type]['nom'].") pour ".get_nom_prenom_eleve($login_ele)." ce jour sur ce créneau.<br />";
					}
				}
				else {
					$lig=mysqli_fetch_object($res);
					if($lig->created_by==$_SESSION['login']) {
						// Mettre à jour la saisie (commentaire)

						$sql="UPDATE sp_saisies SET commentaire='".$commentaire[$key]."'
										WHERE id='".$lig->id."';";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if($update) {
							$tab_saisies_effectuees[]=$lig->id;
						}
						else {
							$msg.="Erreur lors de la mise à jour de l'enregistrement d'une saisie de type n°$id_type (".$tab_type_pointage_discipline['id_type'][$id_type]['nom'].") pour ".get_nom_prenom_eleve($login_ele)." ce jour sur ce créneau.<br />";
						}
					}
					else {
						$msg.="Saisie de type n°$id_type (".$tab_type_pointage_discipline['id_type'][$id_type]['nom'].") pour ".get_nom_prenom_eleve($lig->login)." déjà effectuée par ".civ_nom_prenom($lig->created_by)." ce jour sur ce créneau.<br />";
					}
				}
			}
			else {
				$msg.="Type n°$id_type inconnu.<br />";
			}

		}
	}

	/*
	echo "<pre>";
	print_r($tab_saisies_effectuees);
	echo "</pre>";
	*/

	// Suppressions de saisies
	$nb_suppr=0;
	if($mode=="groupe") {
		$sql="SELECT DISTINCT sp.* FROM sp_saisies sp, j_eleves_groupes jeg WHERE created_by='".$_SESSION['login']."' AND date_sp='$date_sp' AND jeg.login=sp.login AND jeg.id_groupe='$id_groupe';";
	}
	else {
		$sql="SELECT DISTINCT sp.* FROM sp_saisies sp, j_eleves_classes jec WHERE created_by='".$_SESSION['login']."' AND date_sp='$date_sp' AND jec.login=sp.login AND jec.id_classe='$id_classe';";
	}
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if((!in_array($lig->id, $tab_saisies_effectuees))&&($lig->created_by==$_SESSION['login'])) {
				$sql="DELETE FROM sp_saisies WHERE id='".$lig->id."';";
				//echo "$sql<br />";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
				if($del) {
					$nb_suppr++;
				}
				else {
					$msg.="Erreur lors de la suppression de la saisie de type n°".$lig->id_type." (".$tab_type_pointage_discipline['id_type'][$lig->id_type]['nom'].") pour ".get_nom_prenom_eleve($lig->login).".<br />";
				}
			}
		}
	}

	if(count($tab_saisies_effectuees)>0) {
		$msg.=count($tab_saisies_effectuees)." saisie(s) effectuée(s) ou mise(s) à jour.<br />";
	}

	if($nb_suppr>0) {
		$msg.=$nb_suppr." saisie(s) supprimée(s).<br />";
	}

}

$active_module_trombinoscopes=getSettingAOui('active_module_trombinoscopes');

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** DEBUT EN-TETE ***************
$titre_page = "Pointages disciplinaires";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

$ajout_lien="";
if(acces("/mod_discipline/param_pointages.php", $_SESSION['statut'])) {
	$ajout_lien=" | <a href='param_pointages.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Paramétrer, définir les types de pointages</a>";
}
//debug_var();

// Choix du jour
if(!isset($jour)) {
	$jour=strftime("%d/%m/%Y");
	//$ts_jour=;
}
// Choix de l'enseignement ou de la classe ou d'un élève
if(!isset($mode)) {
		echo "
<p style='margin-bottom:1em;'>
	<a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
	$ajout_lien
</p>";

	$sql="SELECT id, classe FROM classes ORDER BY classe;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Aucune classe n'a été trouvée.</p>";
	}
	else {
		echo "<div class='fieldset_opacite50' style='float:left; width:45%; padding:0.5em; margin-right:0.5em;'>
	<p style='text-indent:-4em; margin-left:4em;'>Choisissez une classe pour laquelle effectuer une saisie&nbsp;:<br />";
		while($lig=mysqli_fetch_object($res)) {
			echo "
		<a href='".$_SERVER['PHP_SELF']."?mode=classe&amp;id_classe=".$lig->id."'>".$lig->classe."</a><br />";
		}
		echo "</p>
</div>";

		if($_SESSION['statut']=='professeur') {
			$groups=get_groups_for_prof($_SESSION['login']);

			echo "<div class='fieldset_opacite50' style='float:left; width:45%; padding:0.5em;'>
	<p style='text-indent:-4em; margin-left:4em;'>Choisissez un enseignement pour lequel effectuer une saisie&nbsp;:<br />";
			foreach($groups as $current_group) {
				echo "
		<a href='".$_SERVER['PHP_SELF']."?mode=groupe&amp;id_groupe=".$current_group['id']."'>".$current_group['name']." (".$current_group['description'].") en ".$current_group['classlist_string']."</a><br />";
			}
			echo "</p>
</div>";

		}
	}

	require_once("../lib/footer.inc.php");
	die();
}

// On va commencer par limiter à une saisie prof pour un groupe
//$mode="groupe";
if(($mode=="groupe")||($mode=="classe")) {

		if($mode=='groupe') {
			$tab_ele=get_group($id_groupe);
			if(count($tab_ele)==0) {
				echo "<p style='margin-bottom:1em;'>
	<a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/>Choisir une autre classe</a>
	$ajout_lien
</p>

<p style='color:red'>Aucun élève n'a été trouvé.</p>";

				require_once("../lib/footer.inc.php");
				die();
			}
			$param_lien="mode=groupe&amp;id_groupe=$id_groupe";
			$message_groupe_ou_classe="<p class='bold' style='text-align:center;'>".get_info_grp($id_groupe)."</p>";
		}
		else {
			$tab_ele=get_tab_eleves_classe($id_classe);
			if(count($tab_ele)==0) {
				echo "<p style='margin-bottom:1em;'>
	<a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/>Choisir une autre classe</a>
	$ajout_lien
</p>

<p style='color:red'>Aucun élève n'a été trouvé.</p>";

				require_once("../lib/footer.inc.php");
				die();
			}
			$param_lien="mode=classe&amp;id_classe=$id_classe";
			$message_groupe_ou_classe="<p class='bold' style='text-align:center;'>Classe de ".get_nom_classe($id_classe)."</p>";
		}

		// DEBUG
		/*
		echo "<pre>";
		print_r($tab_ele);
		echo "</pre>";
		*/

		if(!isset($display_date)) {
			$display_date=strftime("%d/%m/%Y");
		}
		$tab_date=explode("/", $display_date);
		$mois=$tab_date[1];
		$jour=$tab_date[0];
		$annee=$tab_date[2];
		$ts_display_date=mktime(0, 0, 0, $mois, $jour, $annee);

		$tab_creneaux=get_heures_debut_fin_creneaux();
		$tab_id_creneau=array();
		foreach($tab_creneaux as $id_definie_periode => $current_creneau) {
			$tab_id_creneau[$current_creneau['debut']]=$id_definie_periode;
		}

		// Chercher le créneau courant par défaut
		$message_creneau="";
		if(!isset($id_creneau)) {
			$HMS_courant=strftime("%H:%M:%S");
			$sql="SELECT * FROM edt_creneaux WHERE heuredebut_definie_periode<='".$HMS_courant."' AND heurefin_definie_periode>'".$HMS_courant."';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				$message_creneau="<p style='text-align:center; color:red'>Créneau courant non trouvé (<em>l'heure courante ne correspond pas à un créneau</em>).<br />On prend le premier créneau de la journée (<em>à vous de faire un autre choix si nécessaire</em>).</p>";
				// On fait juste un tour dans la boucle pour ne récupérer que le premier créneau
				foreach($tab_creneaux as $current_id_creneau => $current_creneau) {
					$id_creneau=$current_id_creneau;
					break;
				}

				$message_creneau="<p style='text-align:center;'>Créneau&nbsp;: ".$tab_creneaux[$id_creneau]['nom_creneau']." (<em>".$tab_creneaux[$id_creneau]['debut_court']." - ".$tab_creneaux[$id_creneau]['fin_court']."</em>)</p>";
			}
			else {
				$lig=mysqli_fetch_object($res);
				$id_creneau=$lig->id_definie_periode;
				$message_creneau="<p style='text-align:center; color:green'>Créneau courant&nbsp;: ".$lig->nom_definie_periode." (<em>".$tab_creneaux[$id_creneau]['debut_court']." - ".$tab_creneaux[$id_creneau]['fin_court']."</em>)</p>";
			}
		}
		else {
			$message_creneau="<p style='text-align:center;'>Créneau choisi&nbsp;: ".$tab_creneaux[$id_creneau]['nom_creneau']." (<em>".$tab_creneaux[$id_creneau]['debut_court']." - ".$tab_creneaux[$id_creneau]['fin_court']."</em>)</p>";
		}

		/*
		echo "<p>Créneau choisi&nbsp;:</p>
<pre>";
		print_r($tab_creneaux[$id_creneau]);
		echo "
</pre>";
		*/

		$tab_saisies=array();
		if($mode=='groupe') {
			$sql="SELECT DISTINCT sp.* FROM sp_saisies sp, 
						j_eleves_groupes jeg 
					WHERE sp.login=jeg.login AND 
						jeg.id_groupe='$id_groupe' AND 
						sp.date_sp>='".$annee."-".$mois."-".$jour." 00:00:00' AND 
						sp.date_sp<='".$annee."-".$mois."-".$jour." 23:59:59';";
		}
		else {
			$sql="SELECT DISTINCT sp.* FROM sp_saisies sp, 
						j_eleves_classes jec 
					WHERE sp.login=jec.login AND 
						jec.id_classe='$id_classe' AND 
						sp.date_sp>='".$annee."-".$mois."-".$jour." 00:00:00' AND 
						sp.date_sp<='".$annee."-".$mois."-".$jour." 23:59:59';";
		}
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				// $lig->date_sp : on met pour le moment toujours le début du créneau à l'enregistrement
				$tmp_tab=explode(" ", $lig->date_sp);
				$id_definie_periode=$tab_id_creneau[$tmp_tab[1]];

				// A revoir par la suite si on permet plusieurs pointages du même type sur un même créneau pour un élève
				$tab_saisies[$id_definie_periode][$lig->login][$lig->id_type]['id']=$lig->id;
				$tab_saisies[$id_definie_periode][$lig->login][$lig->id_type]['login']=$lig->login;
				$tab_saisies[$id_definie_periode][$lig->login][$lig->id_type]['id_type']=$lig->id_type;
				$tab_saisies[$id_definie_periode][$lig->login][$lig->id_type]['commentaire']=$lig->commentaire;
				$tab_saisies[$id_definie_periode][$lig->login][$lig->id_type]['created_at']=$lig->created_at;
				$tab_saisies[$id_definie_periode][$lig->login][$lig->id_type]['created_by']=$lig->created_by;
				//$tab_saisies[$id_definie_periode][$lig->login][$lig->id_type]['deleted_at']=$lig->deleted_at;
				//$tab_saisies[$id_definie_periode][$lig->login][$lig->id_type]['deleted_by']=$lig->deleted_by;
			}
		}

		// A REVOIR pour ne compter que la période courante.
		//          cherche_periode_courante_eleve($login_eleve)
		//          cherche_periode_courante($id_classe) si une seule classe... ou bien trouver la liste des classes du groupe
		//          et récupérer la classe courante de l'élève
		//          Pour la présente page, le numéro de période de la classe courante peut suffire.
		//          Pour visu_eleve.php on affichera toutes les périodes
		//          Et pour un éventuel témoin en page d'accueil élève ou responsable, préférer cherche_periode_courante_eleve($login_eleve)
		//
		//          Faire une fonction pour afficher les totaux par période et id_type... et mettre dans eleves/visu_eleve.php (optionnel)
		//
		//          Faire une page de définition des types de pontages
		//          Ajouter un champ 'rang' à 'sp_types_saisies'
		//
		$tab_totaux=array();
		if($mode=='groupe') {
			$sql="SELECT DISTINCT sp.* FROM sp_saisies sp, j_eleves_groupes jeg WHERE jeg.id_groupe='$id_groupe' AND jeg.login=sp.login;";
		}
		else {
			$sql="SELECT DISTINCT sp.* FROM sp_saisies sp, j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND jec.login=sp.login;";
		}
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				if(!isset($tab_totaux[$lig->login][$lig->id_type])) {
					$tab_totaux[$lig->login][$lig->id_type]=0;
				}
				$tab_totaux[$lig->login][$lig->id_type]++;
			}
		}

		$tab_per=array();
		$tab_totaux_per=array();
		if($mode=='classe') {
			// Trouver les dates de début et fin de la période courante pour calculer le nombre de pointages sur la période... et le total
			//$ts=gmstrftime("%s");
			$ts=gmmktime (12, 0, 0, $mois, $jour, $annee);
			//$sql="SELECT e.* FROM edt_calendrier e WHERE (classe_concerne_calendrier LIKE '%;$id_classe;%' OR classe_concerne_calendrier LIKE '$id_classe;%') AND etabferme_calendrier='1' AND '$ts'<fin_calendrier_ts AND '$ts'>debut_calendrier_ts;";
			$sql="SELECT e.* FROM edt_calendrier e, periodes p WHERE (classe_concerne_calendrier LIKE '%;$id_classe;%' OR classe_concerne_calendrier LIKE '$id_classe;%') AND etabferme_calendrier='1' AND '$ts'<fin_calendrier_ts AND '$ts'>debut_calendrier_ts AND e.numero_periode=p.num_periode AND p.id_classe='$id_classe';";
			//echo htmlentities($sql)."<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				// On ne fait en principe qu'un seul tour dans la boucle
				$tab_num_periode_passee=array();
				while($lig=mysqli_fetch_assoc($res)) {
					if(!in_array($lig['numero_periode'] ,$tab_num_periode_passee)) {
						$tab_per[$id_classe]=$lig;

						$sql="SELECT sp.* FROM sp_saisies sp, j_eleves_classes jec WHERE sp.login=jec.login AND jec.periode='".$tab_per[$id_classe]['numero_periode']."' AND date_sp>='".$tab_per[$id_classe]['jourdebut_calendrier']." ".$tab_per[$id_classe]['heuredebut_calendrier']."' AND date_sp<='".$tab_per[$id_classe]['jourfin_calendrier']." ".$tab_per[$id_classe]['heurefin_calendrier']."' ORDER BY sp.login, sp.id_type;";
						//echo "$sql<br />";
						$res_sp=mysqli_query($GLOBALS["mysqli"], $sql);
						while($lig_sp=mysqli_fetch_object($res_sp)) {
							if(!isset($tab_totaux_per[$lig_sp->login][$lig_sp->id_type])) {
								$tab_totaux_per[$lig_sp->login][$lig_sp->id_type]=0;
							}
							$tab_totaux_per[$lig_sp->login][$lig_sp->id_type]++;
						}
						$tab_num_periode_passee[]=$lig['numero_periode'];
					}
				}
			}
		}
		else {
			// Relever les dates pour les différentes classes du groupe
			//$ts=gmstrftime("%s");
			$ts=gmmktime (12, 0, 0, $mois, $jour, $annee);
			//$current_group=get_group($id_groupe, array('classes', 'periodes', 'eleves'));
			$current_group=$tab_ele;
			for($loop=0;$loop<count($current_group["classes"]["list"]);$loop++) {
				$current_id_classe=$current_group["classes"]["list"][$loop];

				//$sql="SELECT e.* FROM edt_calendrier e WHERE (classe_concerne_calendrier LIKE '%;$current_id_classe;%' OR classe_concerne_calendrier LIKE '$current_id_classe;%') AND etabferme_calendrier='1' AND '$ts'<fin_calendrier_ts AND '$ts'>debut_calendrier_ts;";
				$sql="SELECT e.* FROM edt_calendrier e, periodes p WHERE (classe_concerne_calendrier LIKE '%;$current_id_classe;%' OR classe_concerne_calendrier LIKE '$current_id_classe;%') AND etabferme_calendrier='1' AND '$ts'<fin_calendrier_ts AND '$ts'>debut_calendrier_ts AND e.numero_periode=p.num_periode AND p.id_classe='$current_id_classe';";
				//echo htmlentities($sql)."<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					// On ne fait en principe qu'un seul tour dans la boucle
					// Sauf si on a un remplissage bizarre de edt_calendrier avec des classes dans deux types de périodes... associées toutes les deux à une période de cours (ce qui devrait être interdit dans la page de remplissage de edt_calendrier)
					$tab_num_periode_passee=array();
					while($lig=mysqli_fetch_assoc($res)) {
						if(!in_array($lig['numero_periode'] ,$tab_num_periode_passee)) {

							/*
							echo "<pre>";
							print_r($lig);
							echo "</pre>";
							*/
							$tab_per[$current_id_classe]=$lig;

							$sql="SELECT sp.* FROM sp_saisies sp, j_eleves_classes jec WHERE sp.login=jec.login AND jec.id_classe='$current_id_classe' AND jec.periode='".$tab_per[$current_id_classe]['numero_periode']."' AND date_sp>='".$tab_per[$current_id_classe]['jourdebut_calendrier']." ".$tab_per[$current_id_classe]['heuredebut_calendrier']."' AND date_sp<='".$tab_per[$current_id_classe]['jourfin_calendrier']." ".$tab_per[$current_id_classe]['heurefin_calendrier']."';";
							//echo "$sql<br />";
							$res_sp=mysqli_query($GLOBALS["mysqli"], $sql);
							while($lig_sp=mysqli_fetch_object($res_sp)) {
								if(!isset($tab_totaux_per[$lig_sp->login][$lig_sp->id_type])) {
									$tab_totaux_per[$lig_sp->login][$lig_sp->id_type]=0;
								}
								$tab_totaux_per[$lig_sp->login][$lig_sp->id_type]++;
							}
							$tab_num_periode_passee[]=$lig['numero_periode'];
						}
					}
				}
			}
		}





		echo "
<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_choix_date\">
	<!--fieldset class='fieldset_opacite50' style='margin-bottom:1em;'-->
	<p style='margin-bottom:1em;'>
		<a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
		 | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>
		$ajout_lien
		 | 
		<input type='text' name='display_date' id='display_date' size='10' value='$display_date' 
					onkeydown='clavier_date_plus_moins(this.id,event);' />".img_calendrier_js("display_date", "img_bouton_display_date")."
		<input type='submit' value='Changer de date' />";
		if(isset($id_groupe)) {
			echo "
		<input type='hidden' name='id_groupe' value='$id_groupe' />";
		}
		if(isset($id_classe)) {
			echo "
		<input type='hidden' name='id_classe' value='$id_classe' />";
		}
		echo "
		<input type='hidden' name='id_creneau' value='$id_creneau' />
		<input type='hidden' name='mode' value='$mode' />
	</p>
	<!--/fieldset-->
</form>

$message_groupe_ou_classe

$message_creneau

<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_saisie_sp\">
	<fieldset class='fieldset_opacite50'>
		<div style='float:right; width:20em;'><input type='submit' value=\"Enregistrer les saisies pour le créneau ".$tab_creneaux[$id_creneau]['nom_creneau']."\" /></div>
		<p class='bold'>Saisies pour le ".strftime("%A %d/%m/%Y", $ts_display_date)."</p>
		".add_token_field()."
		<input type='hidden' name='validation_saisie' value='y' />
		".(isset($id_groupe) ? "<input type='hidden' name='id_groupe' value='$id_groupe' />" : "")."
		".(isset($id_classe) ? "<input type='hidden' name='id_classe' value='$id_classe' />" : "")."
		<input type='hidden' name='id_creneau' value='$id_creneau' />
		<input type='hidden' name='display_date' value='$display_date' />
		<input type='hidden' name='mode' value='$mode' />

		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th>Élève</th>
					<th>Classe</th>
					<th>Totaux<br />annuels</th>
					<th>Totaux<br />période</th>";
		foreach($tab_creneaux as $current_id_creneau => $current_creneau) {
			if($current_id_creneau==$id_creneau) {
				echo "
					<th title=\"".$current_creneau['nom_creneau']." : ".$current_creneau['debut_court']." -> ".$current_creneau['fin_court']."\">".$current_creneau['nom_creneau']."</th>";
			}
			else {
				echo "
					<th title=\"".$current_creneau['nom_creneau']." : ".$current_creneau['debut_court']." -> ".$current_creneau['fin_court']."\"><a href='".$_SERVER['PHP_SELF']."?$param_lien&amp;display_date=$display_date&amp;id_creneau=$current_id_creneau' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Passer au créneau ".$current_creneau['nom_creneau']."\">".$current_creneau['nom_creneau']."</a></th>";
			}
		}
		if($active_module_trombinoscopes) {
			echo "
					<th>Photo</th>";
		}
		echo "
				</tr>
			</thead>
			<tbody>";
		$cpt_checkbox=0;
		$tab_classe=array();
		$tab_totaux_tfoot=array('total', 'total_per', 'creneau');
		$tab_totaux_tfoot['total']=array();
		$tab_totaux_tfoot['total_per']=array();
		$tab_totaux_tfoot['creneau']=array();
		$tab_totaux_per_tfoot=array('total', 'total_per', 'creneau');
		$tab_totaux_per_tfoot['total']=array();
		$tab_totaux_per_tfoot['total_per']=array();
		$tab_totaux_per_tfoot['creneau']=array();
		for($loop=0;$loop<count($tab_ele['eleves']['all']['list']);$loop++) {
			$current_eleve_login=$tab_ele['eleves']['all']['list'][$loop];
			$current_eleve_nom=$tab_ele["eleves"]["all"]["users"][$current_eleve_login]['nom'];
			$current_eleve_prenom=$tab_ele["eleves"]["all"]["users"][$current_eleve_login]['prenom'];
			$current_eleve_id_classe=$tab_ele["eleves"]["all"]["users"][$current_eleve_login]['id_classe'];

			if(!isset($tab_classe[$current_eleve_id_classe]['effectif'])) {
				$tab_classe[$current_eleve_id_classe]['nom_classe']=get_nom_classe($current_eleve_id_classe);
				$tab_classe[$current_eleve_id_classe]['effectif']=1;
			}
			else {
				$tab_classe[$current_eleve_id_classe]['effectif']++;
			}

			$current_eleve_classe=$tab_classe[$current_eleve_id_classe]['nom_classe'];
			echo "
				<tr>
					<td>
						<div style='float:right; width:16px;'>
							<a href='../eleves/visu_eleve.php?ele_login=$current_eleve_login' target='_blank' title=\"Voir la fiche élève dans une nouvelle page.\">
								<img src='../images/icons/ele_onglets.png' class='icone16' />
							</a>
						</div>
						".$current_eleve_nom." ".$current_eleve_prenom."
					</td>
					<td>
						".$current_eleve_classe."
					</td>
					<td>";
			for($loop2=0;$loop2<count($tab_type_pointage_discipline['indice']);$loop2++) {
				$current_id_type=$tab_type_pointage_discipline['indice'][$loop2]['id_type'];
				if(isset($tab_totaux[$current_eleve_login][$current_id_type])) {
					$current_nom_sp=$tab_type_pointage_discipline['indice'][$loop2]['nom'];
					echo "
						<span title=\"$current_nom_sp : ".$tab_totaux[$current_eleve_login][$current_id_type]."\">".mb_substr($current_nom_sp,0,2)."&nbsp;: ".$tab_totaux[$current_eleve_login][$current_id_type]."</span><br />";

					if(!isset($tab_totaux_tfoot['total'][$current_id_type])) {
						$tab_totaux_tfoot['total'][$current_id_type]=0;
					}
					$tab_totaux_tfoot['total'][$current_id_type]+=$tab_totaux[$current_eleve_login][$current_id_type];
				}
			}
			echo "
					</td>
					<td>";
			for($loop2=0;$loop2<count($tab_type_pointage_discipline['indice']);$loop2++) {
				$current_id_type=$tab_type_pointage_discipline['indice'][$loop2]['id_type'];
				if(isset($tab_totaux_per[$current_eleve_login][$current_id_type])) {
					$current_nom_sp=$tab_type_pointage_discipline['indice'][$loop2]['nom'];
					echo "
						<span title=\"$current_nom_sp : ".$tab_totaux_per[$current_eleve_login][$current_id_type]."\">".mb_substr($current_nom_sp,0,2)."&nbsp;: ".$tab_totaux_per[$current_eleve_login][$current_id_type]."</span><br />";

					if(!isset($tab_totaux_per_tfoot['total'][$current_id_type])) {
						$tab_totaux_per_tfoot['total'][$current_id_type]=0;
					}
					$tab_totaux_per_tfoot['total'][$current_id_type]+=$tab_totaux_per[$current_eleve_login][$current_id_type];
				}
			}
			echo "
					</td>";
			foreach($tab_creneaux as $current_id_creneau => $current_creneau) {
				$cpt_saisies_creneau_courant=0;
				echo "
					<td style='text-align:left'>";
				if($current_id_creneau==$id_creneau) {
					for($loop2=0;$loop2<count($tab_type_pointage_discipline['indice']);$loop2++) {
						$current_id_type=$tab_type_pointage_discipline['indice'][$loop2]['id_type'];
						$current_nom_sp=$tab_type_pointage_discipline['indice'][$loop2]['nom'];
						$current_description_sp=$tab_type_pointage_discipline['indice'][$loop2]['description'];

						if(isset($tab_saisies[$current_id_creneau][$current_eleve_login][$current_id_type])) {
							if($tab_saisies[$current_id_creneau][$current_eleve_login][$current_id_type]['created_by']==$_SESSION['login']) {
								echo "
						<input type='checkbox' name='saisie[$cpt_checkbox]' id='checkbox_$cpt_checkbox' value='$current_eleve_login|$current_id_type' onchange=\"traite_pointage($cpt_checkbox);\" checked /><label for='checkbox_$cpt_checkbox' id='texte_checkbox_$cpt_checkbox' style='font-weight:bold;'>$current_nom_sp</label>
						<textarea name='commentaire[$cpt_checkbox]' id='commentaire_$cpt_checkbox' style='vertical-align:top;' title=\"Commentaire\">".$tab_saisies[$current_id_creneau][$current_eleve_login][$current_id_type]['commentaire']."</textarea><br />";
								$cpt_checkbox++;
							}
							else {
								/*
								echo "
						<input type='hidden' name='' value='' /><span title=\"$current_description_sp\n".$tab_saisies[$current_id_creneau][$current_eleve_login][$current_id_type]['commentaire']."\nSaisi par ".civ_nom_prenom($tab_saisies[$current_id_creneau][$current_eleve_login][$current_id_type]['created_by'])."\">$current_nom_sp</span><br />";
								*/
								echo "
						<span title=\"$current_description_sp\n".$tab_saisies[$current_id_creneau][$current_eleve_login][$current_id_type]['commentaire']."\nSaisi par ".civ_nom_prenom($tab_saisies[$current_id_creneau][$current_eleve_login][$current_id_type]['created_by'])."\">$current_nom_sp</span><br />";
							}

							if(!isset($tab_totaux_tfoot['creneau'][$current_id_creneau][$current_id_type])) {
								$tab_totaux_tfoot['creneau'][$current_id_creneau][$current_id_type]=0;
							}
							$tab_totaux_tfoot['creneau'][$current_id_creneau][$current_id_type]++;

						}
						else {
							echo "
						<input type='checkbox' name='saisie[$cpt_checkbox]' id='checkbox_$cpt_checkbox' value='$current_eleve_login|$current_id_type' onchange=\"traite_pointage($cpt_checkbox);\" /><label for='checkbox_$cpt_checkbox' id='texte_checkbox_$cpt_checkbox'>$current_nom_sp</label>
						<textarea name='commentaire[$cpt_checkbox]' id='commentaire_$cpt_checkbox' style='display:none; vertical-align:top;' title=\"Commentaire\"></textarea><br />";
							$cpt_checkbox++;
						}
					}
				}
				else {
					for($loop2=0;$loop2<count($tab_type_pointage_discipline['indice']);$loop2++) {
						$current_id_type=$tab_type_pointage_discipline['indice'][$loop2]['id_type'];
						$current_nom_sp=$tab_type_pointage_discipline['indice'][$loop2]['nom'];
						$current_description_sp=$tab_type_pointage_discipline['indice'][$loop2]['description'];

						if(isset($tab_saisies[$current_id_creneau][$current_eleve_login][$current_id_type])) {
							if($cpt_saisies_creneau_courant>0) {
								echo "<br />";
							}
							if($tab_saisies[$current_id_creneau][$current_eleve_login][$current_id_type]['created_by']!=$_SESSION['login']) {
							}
							echo "
						<span title=\"".$current_description_sp."\n".$tab_saisies[$current_id_creneau][$current_eleve_login][$current_id_type]['commentaire']."\nSaisi par ".civ_nom_prenom($tab_saisies[$current_id_creneau][$current_eleve_login][$current_id_type]['created_by'])."\">".$current_nom_sp."</span>";
							$cpt_saisies_creneau_courant++;

							if(!isset($tab_totaux_tfoot['creneau'][$current_id_creneau][$current_id_type])) {
								$tab_totaux_tfoot['creneau'][$current_id_creneau][$current_id_type]=0;
							}
							$tab_totaux_tfoot['creneau'][$current_id_creneau][$current_id_type]++;
						}
					}
				}
				echo "
					</td>";
			}
			if($active_module_trombinoscopes) {
				$photo=nom_photo($tab_ele["eleves"]["all"]["users"][$current_eleve_login]['elenoet']);
				if (($photo == NULL) or (!(file_exists($photo)))) {
					$photo = "../mod_trombinoscopes/images/trombivide.jpg";
				}
				echo "
					<td>".nom_photo($current_eleve_login)."<img src='".$photo."' width='50' alt='Photo' /></td>";
			}
			echo "
				</tr>";
		}
		echo "
			</tbody>
			<tfoot>
				<tr>
					<th>Totaux</th>
					<th>";
		foreach($tab_classe as $current_id_classe => $current_clas) {
			echo preg_replace("/ /","&nbsp;",$current_clas['nom_classe'])."<span style='font-size:xx-small' title='Effectif'>&nbsp;(".$current_clas['effectif'].")</span> ";
		}
		echo "</th>
					<th>";
		foreach($tab_totaux_tfoot['total'] as $current_id_type => $current_effectif) {
			echo "<span title=\"".$tab_type_pointage_discipline['id_type'][$current_id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$current_id_type]['description'].")\">".mb_substr($tab_type_pointage_discipline['id_type'][$current_id_type]['nom'], 0, 2)."&nbsp;:&nbsp;".$current_effectif."</span><br />";
		}
		echo "</th>
					<th>";
		foreach($tab_totaux_per_tfoot['total'] as $current_id_type => $current_effectif) {
			echo "<span title=\"".$tab_type_pointage_discipline['id_type'][$current_id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$current_id_type]['description'].")\">".mb_substr($tab_type_pointage_discipline['id_type'][$current_id_type]['nom'], 0, 2)."&nbsp;:&nbsp;".$current_effectif."</span><br />";
		}
		echo "</th>";
		// <!-- Boucle sur les créneaux -->
		foreach($tab_creneaux as $current_id_creneau => $current_creneau) {
			echo "
					<th>";
			if(isset($tab_totaux_tfoot['creneau'][$current_id_creneau])) {
				foreach($tab_totaux_tfoot['creneau'][$current_id_creneau] as $current_id_type => $current_effectif) {
					echo "<span title=\"".$tab_type_pointage_discipline['id_type'][$current_id_type]['nom']." (".$tab_type_pointage_discipline['id_type'][$current_id_type]['description'].")\">".mb_substr($tab_type_pointage_discipline['id_type'][$current_id_type]['nom'], 0, 2)."&nbsp;:&nbsp;".$current_effectif."</span><br />";
				}
			}
		echo "</th>";
		}
		if($active_module_trombinoscopes) {
			echo "
					<th>Photos</th>";
		}
		echo "
				</tr>
			</tfoot>
		</table>
		<!--p><input type='submit' value='Enregistrer' /></p-->
		<p style='text-align:center;'><input type='submit' value=\"Enregistrer les saisies pour le créneau ".$tab_creneaux[$id_creneau]['nom_creneau']."\" /></p>
	</fieldset>
</form>

<script type='text/javascript'>
	".js_checkbox_change_style()."

	function traite_pointage(num) {
		checkbox_change('checkbox_'+num);
		changement();

		if(document.getElementById('checkbox_'+num).checked==true) {
			document.getElementById('commentaire_'+num).style.display='';
		}
		else {
			document.getElementById('commentaire_'+num).style.display='none';
		}
	}
</script>";
		/*
		// DEBUG
		echo "<pre>";
		print_r($tab_classe);
		echo "</pre>";
		*/
}
else {
	echo "<p>Mode $mode non encore implémenté.</p>";
}

//echo "<p style='margin-top:1em;'><span style='color:red'>A FAIRE&nbsp;:</span> Afficher le total annuel et le total de la période courante.</p>";
require_once("../lib/footer.inc.php");
?>
