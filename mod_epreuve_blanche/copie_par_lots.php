<?php
/*
* Copyright 2001, 2021 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$variables_non_protegees = 'yes';

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



//======================================================================================
$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/copie_par_lots.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/copie_par_lots.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: Copie par lots',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

include('lib_eb.php');

//=========================================================

//debug_var();

//=========================================================

$sql="SELECT * FROM eb_epreuves WHERE etat!='clos' ORDER BY date, intitule;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_epreuves=mysqli_num_rows($res);

$sql="SELECT * FROM eb_epreuves WHERE etat='clos' ORDER BY date, intitule;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_epreuves_closes=mysqli_num_rows($res);

if(($nb_epreuves==0)||($nb_epreuves+$nb_epreuves_closes<1)) {
	header("Location: ./index.php?msg=".rawurlencode("Il n'existe pas encore assez d'épreuves pour réaliser une copie. Il faut notamment au moins une épreuve non close."));
	die();
}
//=========================================================

//$id_epreuve=isset($_POST['id_epreuve']) ? $_POST['id_epreuve'] : (isset($_GET['id_epreuve']) ? $_GET['id_epreuve'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$id_epreuve_modele=isset($_POST['id_epreuve_modele']) ? $_POST['id_epreuve_modele'] : (isset($_GET['id_epreuve_modele']) ? $_GET['id_epreuve_modele'] : NULL);


//$modif_epreuve=isset($_POST['modif_epreuve']) ? $_POST['modif_epreuve'] : (isset($_GET['modif_epreuve']) ? $_GET['modif_epreuve'] : NULL);

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {

	if(isset($id_epreuve_modele)) {
		$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve_modele';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$msg="L'épreuve choisie (<i>$id_epreuve_modele</i>) n'existe pas.\n";
	}

	if((isset($id_epreuve_modele))&&($mode=='copier_choix')&&(isset($_POST['copier_les_parametres']))) {
		check_token();

		$id_epreuve_dest=isset($_POST['id_epreuve_dest']) ? $_POST['id_epreuve_dest'] : NULL;

		if(!isset($id_epreuve_dest)) {
			$msg="Vous n'avez sélectionné aucune épreuve vers laquelle copier les paramètres.<br />";
		}
		else {

			$id_epreuve_modele=isset($_POST['id_epreuve_modele']) ? $_POST['id_epreuve_modele'] : NULL;

			$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : NULL;
			$id_salle=isset($_POST['id_salle']) ? $_POST['id_salle'] : NULL;
			$copie_affect_ele_salle=isset($_POST['copie_affect_ele_salle']) ? $_POST['copie_affect_ele_salle'] : NULL;
			$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : NULL;
			$copie_affect_copie_prof=isset($_POST['copie_affect_copie_prof']) ? $_POST['copie_affect_copie_prof'] : NULL;


			foreach($id_epreuve_dest as $id_epreuve) {
				if(!isset($msg)) {$msg="";}

				$msg.="<strong>".get_info_epreuve_blanche($id_epreuve)."&nbsp;:</strong> ";

				$nb_groupes_ajoutes=0;
				if(isset($id_groupe)) {

					//$sql="DELETE FROM eb_groupes WHERE id_epreuve='$id_epreuve';";
					//$del=mysql_query($sql);

					for($loop=0;$loop<count($id_groupe);$loop++) {
						$sql="SELECT 1=1 FROM eb_groupes WHERE id_groupe='$id_groupe[$loop]' AND  id_epreuve='$id_epreuve';";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test)==0) {
							$sql="INSERT INTO eb_groupes SET id_groupe='$id_groupe[$loop]', id_epreuve='$id_epreuve', transfert='n';";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							$nb_groupes_ajoutes++;
						}
					}
					if($nb_groupes_ajoutes>0) {
						$msg.=$nb_groupes_ajoutes." enseignement(s) ajouté(s), ";
					}
				}

				// A REVOIR UN JOUR: La gestion des salles est mal foutue.
				// Il faudrait avoir une table eb_salles ne dépendant pas de id_epreuve
				if(isset($id_salle)) {
					$nb_salles_ajoutees=0;

					$sql="DELETE FROM eb_salles WHERE id_epreuve='$id_epreuve';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);

					$sql="UPDATE eb_copies SET id_salle='' WHERE id_epreuve='$id_epreuve';";
					//echo "$sql<br />";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);

					$tab_corresp_id_salle=array();

					for($loop=0;$loop<count($id_salle);$loop++) {
						$sql="SELECT * FROM eb_salles WHERE id='$id_salle[$loop]' AND id_epreuve='$id_epreuve_modele';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)>0) {
							$lig=mysqli_fetch_object($res);

							$sql="INSERT INTO eb_salles SET salle='$lig->salle', id_epreuve='$id_epreuve';";
							//echo "$sql<br />";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							$tmp_id_salle=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
							$tab_salle[$tmp_id_salle]=$lig->salle;
							$tab_corresp_id_salle[$id_salle[$loop]]=$tmp_id_salle;

							//echo "\$tab_salle[$tmp_id_salle]=".$tab_salle[$tmp_id_salle]."<br />";
							//echo "\$tab_corresp_id_salle[$id_salle[$loop]]=\$tab_corresp_id_salle[$id_salle[$loop]]=".$tab_corresp_id_salle[$id_salle[$loop]]."<br />";
							$nb_salles_ajoutees++;
						}
					}
					if($nb_salles_ajoutees>0) {
						$msg.=$nb_salles_ajoutees." salles(s) choisie(s), ";
					}


					if(isset($copie_affect_ele_salle)) {
						$nb_copie_affect_ele_salle=0;
						for($loop=0;$loop<count($copie_affect_ele_salle);$loop++) {
							if(!in_array($copie_affect_ele_salle[$loop],$id_salle)) {
								$msg.="Il n'est pas possible de copier les affectations élèves/salles si la salle n'est pas copiée.<br />";
							}
							else {
								$sql="SELECT ec.login_ele FROM eb_copies ec WHERE ec.id_epreuve='$id_epreuve_modele' AND ec.id_salle='$copie_affect_ele_salle[$loop]';";
								//echo "$sql<br />";
								$res=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res)>0) {
									while($lig=mysqli_fetch_object($res)) {

										$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_ele='$lig->login_ele';";
										//echo "$sql<br />";
										$test=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($test)>0) {
											$sql="UPDATE eb_copies SET id_salle='".$tab_corresp_id_salle[$copie_affect_ele_salle[$loop]]."' WHERE id_epreuve='$id_epreuve' AND login_ele='$lig->login_ele';";
											//echo "$sql<br />";
											$update=mysqli_query($GLOBALS["mysqli"], $sql);
											if(!$update) {
												$msg.="Erreur lors de l'affectation de $lig->login_ele dans ".$tab_salle[$tab_corresp_id_salle[$copie_affect_ele_salle[$loop]]]."<br />";
											}
											else {
												$nb_copie_affect_ele_salle++;
											}
										}
										else {
											$sql="INSERT INTO eb_copies SET id_salle='".$tab_corresp_id_salle[$copie_affect_ele_salle[$loop]]."', id_epreuve='$id_epreuve', login_ele='".$lig->login_ele."', statut='v';";
											//echo "$sql<br />";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);
											if(!$insert) {
												$msg.="Erreur lors de l'affectation de $lig->login_ele dans ".$tab_salle[$tab_corresp_id_salle[$copie_affect_ele_salle[$loop]]]."<br />";
											}
											else {
												$nb_copie_affect_ele_salle++;
											}
										}

									}
								}
							}
						}
						if($nb_copie_affect_ele_salle>0) {
							$msg.=$nb_copie_affect_ele_salle." affectation(s) élève(s) dans les salles effectuée(s), ";
						}
					}
				}

				// Si on n'a pas copié les groupes, on ne doit pas pouvoir copier les affectations... sauf si ce sont des profs qui ont les mêmes élèves dans plusieurs groupes.
				if(isset($login_prof)) {
					$nb_prof_choisis=0;
					$sql="DELETE FROM eb_profs WHERE id_epreuve='$id_epreuve';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);

					$sql="UPDATE eb_copies SET login_prof='' WHERE id_epreuve='$id_epreuve';";
					//echo "$sql<br />";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);

					for($loop=0;$loop<count($login_prof);$loop++) {
						$sql="INSERT INTO eb_profs SET login_prof='$login_prof[$loop]', id_epreuve='$id_epreuve';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						$nb_prof_choisis++;
					}
					if($nb_prof_choisis>0) {
						$msg.=$nb_prof_choisis." correcteur(s) choisi(s), ";
					}

					if(isset($copie_affect_copie_prof)) {
						$nb_copie_affect_copie_prof=0;
						for($loop=0;$loop<count($copie_affect_copie_prof);$loop++) {
							if(!in_array($copie_affect_copie_prof[$loop],$login_prof)) {
								$msg.="Il n'est pas possible de copier les affectations copies_élèves/correcteurs si le correcteur n'est pas copié.<br />";
							}
							else {
								$sql="SELECT ec.login_ele FROM eb_copies ec WHERE ec.id_epreuve='$id_epreuve_modele' AND ec.login_prof='$copie_affect_copie_prof[$loop]';";
								$res=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res)>0) {
									while($lig=mysqli_fetch_object($res)) {

										$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_ele='$lig->login_ele';";
										$test=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($test)>0) {
											$sql="UPDATE eb_copies SET login_prof='".$copie_affect_copie_prof[$loop]."' WHERE id_epreuve='$id_epreuve' AND login_ele='$lig->login_ele';";
											//echo "$sql<br />";
											$update=mysqli_query($GLOBALS["mysqli"], $sql);
											if(!$update) {
												$msg.="Erreur lors de l'affectation de la copie $lig->login_ele au correcteur ".$copie_affect_copie_prof[$loop]."<br />";
											}
											else {
												$nb_copie_affect_copie_prof++;
											}
										}
										else {
											$sql="INSERT INTO eb_copies SET login_prof='".$copie_affect_copie_prof[$loop]."', id_epreuve='$id_epreuve', login_ele='".$lig->login_ele."';";
											//echo "$sql<br />";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);
											if(!$insert) {
												$msg.="Erreur lors de l'affectation de la copie $lig->login_ele au correcteur ".$copie_affect_copie_prof[$loop]."<br />";
											}
											else {
												$nb_copie_affect_copie_prof++;
											}
										}

									}
								}
							}
						}
						if($nb_copie_affect_copie_prof>0) {
							$msg.=$nb_copie_affect_copie_prof." copie(s) attribuée(s) aux professeurs, ";
						}
					}
				}
				$msg=preg_replace('/, $/', '', $msg);
				$msg.="<br />";
			}

			//$mode="modif_epreuve";
		}
	}
}


$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Epreuve blanche: Copie paramètres";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();


echo "<p class='bold'><a href='../accueil.php'>Accueil</a>";
echo " | <a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Menu épreuves blanches</a>\n";


if(!isset($id_epreuve_modele)) {
	echo "</p>\n";

	echo "<h2>Copie de paramétrages d'épreuves blanches</h2>
	<p>De quelle épreuve voulez-vous copier des paramètres&nbsp;?</p>
	<ul>";
	$sql="SELECT * FROM eb_epreuves ORDER BY intitule, date";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		// Afficher aussi le nombre de paramètres salle, élèves,...
		echo "
		<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve_modele=".$lig->id."'>".$lig->intitule." (".formate_date($lig->date).")</a></li>";
	}
	echo "
	</ul>";

	require("../lib/footer.inc.php");
	die();

}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choisir une autre épreuve modèle</a>\n";
	echo "</p>\n";

	$sql="SELECT * FROM eb_epreuves WHERE id='".$id_epreuve_modele."';";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)==0) {

		echo "<p style='color:red'>L'épreuve n°$id_epreuve_modele n'a pas été trouvée.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}
	$epreuve_modele=mysqli_fetch_assoc($res);
	// Récupérer les autres infos:
	// Groupes, profs, salles, affectation dans les salles

	echo "<form action=".$_SERVER['PHP_SELF']." method='post'>
<fieldset class='fieldset_opacite50'>
	<h2>Copie de paramétrages d'épreuves blanches</h2>
	
	<p>Le modèle choisi est l'épreuve n°<strong>$id_epreuve_modele</strong> intitulée <strong>".$epreuve_modele['intitule']."</strong> du <strong>".formate_date($epreuve_modele['date'])."</strong></p>
	".(trim($epreuve_modele['description'])!='' ? "<p style='margin-left: 3em;'>".nl2br($epreuve_modele['description'])."</p>" : "")."

	<p>Quels paramètres voulez vous copier&nbsp;?</p>";

	// Liste des groupes
	$sql="SELECT * FROM eb_groupes eg WHERE id_epreuve='$id_epreuve_modele';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='margin-top:1em'>L'épreuve n°$id_epreuve_modele n'est associée à aucun groupe/enseignement.</p>\n";
	}
	else {
		echo "<p style='margin-top:1em'>Liste des enseignements de l'épreuve modèle&nbsp;: 
		<a href='#' onclick=\"cocher_decocher_tous_champs_tel_prefixe('id_groupe_', true); return false;\" title='Tout cocher'><img src='../images/enabled.png' class='icone20' /></a> / <a href='#' onclick=\"cocher_decocher_tous_champs_tel_prefixe('id_groupe_', false); return false;\" title='Tout décocher'><img src='../images/disabled.png' class='icone20' /></a></p>\n";
		echo "<p style='margin-left: 3em;'>";
		while($lig=mysqli_fetch_object($res)) {
			$tmp_grp=get_group($lig->id_groupe);
			echo "<input type='checkbox' name='id_groupe[]' id='id_groupe_".$lig->id_groupe."' value='$lig->id_groupe' onchange='checkbox_change(this.id);changement();' checked />
			<label for='id_groupe_".$lig->id_groupe."' id='texte_id_groupe_".$lig->id_groupe."'> ".$tmp_grp['name']." (<i>".$tmp_grp['classlist_string']."</i>)</label>\n";
		}
		echo "</p>\n";
	}

	// Liste des salles
	$sql="SELECT * FROM eb_salles es WHERE id_epreuve='$id_epreuve_modele' ORDER BY salle;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='margin-top:1em'>L'épreuve n°$id_epreuve_modele n'est associée à aucune salle.</p>\n";
	}
	else {
		echo "<p style='margin-top:1em'>Liste des salles&nbsp;: 
		<a href='#' onclick=\"cocher_decocher_tous_champs_tel_prefixe('id_salle_', true); cocher_decocher_tous_champs_tel_prefixe('copie_affect_ele_salle_', true); return false;\" title='Tout cocher'><img src='../images/enabled.png' class='icone20' /></a> / <a href='#' onclick=\"cocher_decocher_tous_champs_tel_prefixe('id_salle_', false); cocher_decocher_tous_champs_tel_prefixe('copie_affect_ele_salle_', false); return false;\" title='Tout décocher'><img src='../images/disabled.png' class='icone20' /></a>
		</p>\n";
		echo "<p style='margin-left: 3em;'>";
		while($lig=mysqli_fetch_object($res)) {
			echo "<input type='checkbox' name='id_salle[]' id='id_salle_".$lig->id."' value='$lig->id' onchange='checkbox_change(this.id);changement()' checked />
			<label for='id_salle_".$lig->id."' id='texte_id_salle_".$lig->id."'> ".$lig->salle."</label>\n";
			$tab_salle[$lig->id]=$lig->salle;
		}
		echo "</p>\n";

		// Liste des associations élèves/salles
		$sql="SELECT * FROM eb_copies WHERE id_epreuve='$id_epreuve_modele' ORDER BY id_salle;";
		$res_ele_salle=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele_salle)==0) {
			echo "<p>Aucun élève n'est affecté dans une salle pour l'épreuve n°$id_epreuve_modele.</p>\n";
		}
		else {
			$current_id_salle="";
			$cpt=0;
			while($lig=mysqli_fetch_object($res_ele_salle)) {

				if($lig->id_salle!='-1') {

					if($lig->id_salle!=$current_id_salle) {
						//if($current_id_salle!="";) {echo "</table>\n";}
						if($current_id_salle!="") {
							echo "</span>\n";
							echo "</span>\n";
							echo "</p>\n";
						}

						$current_id_salle=$lig->id_salle;
						echo "<p style='margin-left: 6em;'>";
						echo "<span class='conteneur_infobulle_css'>\n";
						echo "<input type='checkbox' name='copie_affect_ele_salle[]' id='copie_affect_ele_salle_".$lig->id_salle."' value='".$lig->id_salle."' onchange='checkbox_change(this.id);changement()' checked  />";
						echo "<label for='copie_affect_ele_salle_".$lig->id_salle."' id='texte_copie_affect_ele_salle_".$lig->id_salle."'>";
							echo "Copier les affectations d'élèves en ".$tab_salle[$lig->id_salle]."</label>";
						echo "<br />\n";
						echo "<span class='infobulle_css'>\n";
						$cpt=0;
					}

					//echo "<input type='checkbox' name='' value='' />";
					if($cpt>0) {
						echo ", ";
						if($cpt%5==0) {echo "<br />";}
					}
					echo get_nom_prenom_eleve($lig->login_ele,'avec_classe');
					$cpt++;
				}
			}
			//echo "</table>\n";
			echo "</span>\n";
			echo "</span>\n";
			echo "</p>\n";

		}
	}

	// Liste des correcteurs
	$sql="SELECT * FROM eb_profs ep WHERE id_epreuve='$id_epreuve_modele' ORDER BY login_prof;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='margin-top:1em'>L'épreuve n°$id_epreuve_modele n'est associée à aucun correcteur.</p>\n";
	}
	else {
		echo "<p style='margin-top:1em'>Liste des correcteurs&nbsp;: 
		<a href='#' onclick=\"cocher_decocher_tous_champs_tel_prefixe('login_prof_', true); return false;\" title='Tout cocher'><img src='../images/enabled.png' class='icone20' /></a> / <a href='#' onclick=\"cocher_decocher_tous_champs_tel_prefixe('login_prof_', false); return false;\" title='Tout décocher'><img src='../images/disabled.png' class='icone20' /></a></p>\n";
		echo "<p style='margin-left: 3em;'>";
		while($lig=mysqli_fetch_object($res)) {
			$tab_prof[$lig->login_prof]=civ_nom_prenom($lig->login_prof);

			echo "<input type='checkbox' name='login_prof[]' id='login_prof_".$lig->login_prof."' value='$lig->login_prof' onchange='checkbox_change(this.id);changement()' checked />
			<label for='login_prof_".$lig->login_prof."' id='texte_login_prof_".$lig->login_prof."'> ".$tab_prof[$lig->login_prof]."</label>\n";
		}
		echo "</p>\n";

		// Liste des associations professeur/copies
		$sql="SELECT * FROM eb_copies WHERE id_epreuve='$id_epreuve_modele' AND login_prof!='' ORDER BY login_prof;";
		//echo "$sql<br />";
		$res_ele_prof=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele_prof)==0) {
			echo "<p style='margin-top:1em;'>Aucun copie élève n'est affectée à un correcteur pour l'épreuve n°$id_epreuve_modele.</p>\n";
		}
		else {
			$current_login_prof="";
			$cpt=0;
			while($lig=mysqli_fetch_object($res_ele_prof)) {

				if($lig->login_prof!=$current_login_prof) {
					if($current_login_prof!="") {
						echo "</span>\n";
						echo "</span>\n";
						echo "</p>\n";
					}
					$current_login_prof=$lig->login_prof;

					echo "<p style='margin-left: 6em;'>";
					echo "<span class='conteneur_infobulle_css'>\n";
					echo "<input type='checkbox' name='copie_affect_copie_prof[]' id='copie_affect_copie_prof_".$lig->login_prof."' value='".$lig->login_prof."' onchange='changement()' checked  />";
					echo "<label for='copie_affect_copie_prof_".$lig->login_prof."' id='texte_copie_affect_copie_prof_".$lig->login_prof."'>Copier les affectations copie d'élèves au correcteur ".$tab_prof[$lig->login_prof]."</label>";
					//echo "<table class='boireaus' summary=\"Liste des élèves affectés en Salle $tab_salle[$lig->id]\">\n";
					echo "<br />\n";
					echo "<span class='infobulle_css'>\n";
					$cpt=0;
				}

				//echo "<input type='checkbox' name='' value='' />";
				if($cpt>0) {
					echo ", ";
					if($cpt%5==0) {echo "<br />";}
				}
				echo get_nom_prenom_eleve($lig->login_ele,'avec_classe');
				$cpt++;
			}
			//echo "</table>\n";
			echo "</span>\n";
			echo "</span>\n";
			echo "</p>\n";

		}
	}

	echo "<p style='margin-top:1em'>Vers quelles épreuves voulez-vous copier les paramètres choisis&nbsp;?</p>";
	$sql="SELECT * FROM eb_epreuves WHERE id!='".$id_epreuve_modele."' AND etat!='clos' ORDER BY intitule, date";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Il n'existe pas d'autre épreuve <em>(non close)</em> que l'épreuve modèle <em>(n°$id_epreuve_modele)</em>.</p>\n";
		echo "</fieldset>\n";
		echo "</form>\n";

		require("../lib/footer.inc.php");
		die();
	}
	echo "<p>";
	while($lig=mysqli_fetch_object($res)) {
		// Afficher aussi le nombre de paramètres salle, élèves,...
		echo "
		<input type='checkbox' name='id_epreuve_dest[]' id='id_epreuve_dest_".$lig->id."' value='".$lig->id."' onchange='checkbox_change(this.id);changement();' /><label for='id_epreuve_dest_".$lig->id."' id='texte_id_epreuve_dest_".$lig->id."'>".$lig->intitule." (".$lig->date.")</label><br />";
	}
	echo "
	</p>";


	echo "<input type='hidden' name='id_epreuve_modele' value='$id_epreuve_modele' />\n";
	echo "<input type='hidden' name='mode' value='copier_choix' />\n";
	echo "<input type='submit' name='copier_les_parametres' value='Copier les paramètres sélectionnés' />\n";

	echo add_token_field();
	echo "</fieldset>\n";
	echo "</form>\n";

	// Espace pour que les infobulles CSS puissent s'afficher.
	echo "<div style='height:10em;'>&nbsp;</div>";

	echo "<script type='text/javascript'>
	".js_checkbox_change_style('checkbox_change', 'texte_')."

item=document.getElementsByTagName('input');
for(i=0;i<item.length;i++) {
	if(item[i].getAttribute('type')=='checkbox') {
		checkbox_change(item[i].getAttribute('id'));
	}
}

function cocher_decocher_tous_champs_tel_prefixe(prefixe, mode) {
	//var pattern=new RegExp('^'+prefixe, 'g');

	item=document.getElementsByTagName('input');
	for(i=0;i<item.length;i++) {
		if(item[i].getAttribute('type')=='checkbox') {
			id_item=item[i].getAttribute('id');

			var pattern=new RegExp('^'+prefixe, 'g');

			if(pattern.test(id_item)) {
				item[i].checked=mode;
				checkbox_change(item[i].getAttribute('id'));
			}
		}
	}
}
</script>";


	require("../lib/footer.inc.php");
	die();
}
}

require("../lib/footer.inc.php");
?>
