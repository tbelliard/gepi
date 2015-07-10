<?php
/*
* $Id$
*
* Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Laurent Viénot-Hauger
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
//$variables_non_protegees = 'yes';

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


// INSERT INTO droits VALUES('/mod_notanet/saisie_socle_commun.php','V','F','F','V','F','F','F','F','Notanet: Saisie socle commun','');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


$type_brevet=isset($_POST['type_brevet']) ? $_POST['type_brevet'] : (isset($_GET['type_brevet']) ? $_GET['type_brevet'] : NULL);

$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$matiere = isset($_POST['matiere']) ? $_POST['matiere'] : (isset($_GET['matiere']) ? $_GET['matiere'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$msg="";

include("./lib_brevets.php");

//debug_var();

if((getSettingAOui("notanet_saisie_note_ouverte"))&&(isset($_POST['is_posted']))&&(isset($id_groupe))&&(isset($matiere))) {
	check_token();

	$poursuivre="y";
	if($_SESSION['statut']=='professeur') {
		if(!verif_groupe_appartient_prof($id_groupe)) {
			header("Location: ../accueil.php?msg=Accès non autorisé.");
			die();
		}
	}

	// A FAIRE : Ajouter des tests sur l'association matiere_notanet/id_groupe/matiere_gepi

	if($poursuivre=="y") {
		$pb_record="no";

		$ele_login=isset($_POST["ele_login"]) ? $_POST["ele_login"] : array();
		$note=isset($_POST["note"]) ? $_POST["note"] : array();

		foreach($ele_login as $key => $value) {
			// Vérifier si l'élève est bien dans la classe?
			// Inutile si seul l'admin accède et qu'on ne limite pas l'accès à telle ou telle classe

			if(isset($note[$key])) {
				$note_courante=preg_replace("/,/",".",$note[$key]);
				if(($note_courante!="")&&
				(!preg_match("/^[0-9]{1,}$/", $note_courante))&&
				(!preg_match("/^[0-9]{1,}.[0-9]*$/", $note_courante))&&
				(!preg_match("/^.[0-9]{1,}$/", $note_courante))&&
				(mb_strtolower($note_courante)!="di")&&
				(mb_strtolower($note_courante)!="ab")
				) {
					$msg.="Note pour ".$ele_login[$key]." invalide : ".$note[$key]."<br />";
					$pb_record="yes";
				}
				else {
					$sql="DELETE FROM notanet_saisie WHERE login='".$ele_login[$key]."' AND matiere='$matiere';";
					$del=mysql_query($sql);

					//$sql="INSERT INTO notanet_saisie SET login='".$ele_login[$key]."', matiere='$matiere', note='".$note[$key]."';";
					$sql="INSERT INTO notanet_saisie SET login='".$ele_login[$key]."', matiere='$matiere', note='".$note_courante."';";
					//echo "$sql<br />";
					$register=mysql_query($sql);
					if (!$register) {
						$msg .= "Erreur lors de l'enregistrement des données pour $ele_login[$i]<br />";
						//echo "ERREUR<br />";
						$pb_record = 'yes';
					}
				}
			}
		}

		if ($pb_record == 'no') {
			//$affiche_message = 'yes';
			$msg="Les modifications ont été enregistrées !";
		}
	}
}
elseif((isset($_POST['action']))&&($_POST['action']=='upload_file')&&(isset($id_groupe))) {
	check_token();

	if($_SESSION['statut']=='professeur') {
		if(!verif_groupe_appartient_prof($id_groupe)) {
			header("Location: ../accueil.php?msg=Accès non autorisé.");
			die();
		}
	}

	// A FAIRE : Ajouter des tests sur l'association matiere_notanet/id_groupe/matiere_gepi

	$current_group=get_group($id_groupe);

	$type_csv = isset($_POST["type_csv"]) ? $_POST["type_csv"] : NULL;
	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
	if((isset($csv_file))&&(isset($type_csv))) {
		$fp=fopen($csv_file['tmp_name'],"r");

		if(!$fp) {
			$msg.="Impossible d'ouvrir le fichier CSV !<br />";
		}
		else {

			$nb_reg=0;
			$nb_ele_autre_groupe=0;
			$info_ele_autre_groupe="";
			if($type_csv==1) {
				while (!feof($fp)) {
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!="") {
						if(!preg_match("/;Moyenne DNB/", $ligne)) {
							$tab=explode(";",trim($ligne));
							/*
							echo "<pre>";
							print_r($tab);
							echo "</pre>";
							*/
							if((isset($tab[0]))&&($tab[0]!='')&&(isset($tab[3]))&&($tab[3]!='')) {

								$sql="SELECT login FROM eleves WHERE no_gep='".$tab[0]."';";
								//echo "$sql<br />";
								$res_ele=mysql_query($sql);
								if(mysql_num_rows($res_ele)==0) {
									$msg.="L'élève n°".$tab[0]." n'a pas été trouvé dans la table 'eleves'.<br />";
								}
								else {
									$lig_ele=mysql_fetch_object($res_ele);

									if(!in_array($lig_ele->login, $current_group['eleves']['all']['list'])) {
										$nb_ele_autre_groupe++;
										$info_ele_autre_groupe.=", ".get_nom_prenom_eleve($lig_ele->login);
									}
									else {
										$note_courante=preg_replace("/,/",".",$tab[3]);
										if(($note_courante!="")&&
										(!preg_match("/^[0-9]{1,}$/", $note_courante))&&
										(!preg_match("/^[0-9]{1,}.[0-9]*$/", $note_courante))&&
										(!preg_match("/^.[0-9]{1,}$/", $note_courante))&&
										(mb_strtolower($note_courante)!="di")&&
										(mb_strtolower($note_courante)!="ab")
										) {
											$msg.="Note pour ".$lig_ele->login." invalide : ".$tab[3]."<br />";
											$pb_record="yes";
										}
										else {
											$sql="DELETE FROM notanet_saisie WHERE login='".$lig_ele->login."' AND matiere='$matiere';";
											$del=mysql_query($sql);

											//$sql="INSERT INTO notanet_saisie SET login='".$lig_ele->login."', matiere='$matiere', note='".$tab[3]."';";
											$sql="INSERT INTO notanet_saisie SET login='".$lig_ele->login."', matiere='$matiere', note='".$note_courante."';";
											//echo "$sql<br />";
											$register=mysql_query($sql);
											if (!$register) {
												$msg .= "Erreur lors de l'enregistrement des données pour ".$lig_ele->login.".<br />";
											}
											else {
												$nb_reg++;
											}
										}
									}
								}
							}
						}
					}
				}
			}
			else {
				while (!feof($fp)) {
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!="") {
						if(!preg_match("/;Moyenne DNB/", $ligne)) {
							$tab=explode(";",trim($ligne));
							if((isset($tab[0]))&&($tab[0]!='')&&(isset($tab[1]))&&($tab[1]!='')) {
								$current_login_ele=$tab[0];

								if(!in_array_i($current_login_ele, $current_group['eleves']['all']['list'])) {
									$nb_ele_autre_groupe++;
									$info_ele_autre_groupe.=", ".get_nom_prenom_eleve($current_login_ele);
								}
								else {
									$note_courante=preg_replace("/,/",".",$tab[1]);
									if(($note_courante!="")&&
									(!preg_match("/^[0-9]{1,}$/", $note_courante))&&
									(!preg_match("/^[0-9]{1,}.[0-9]*$/", $note_courante))&&
									(!preg_match("/^.[0-9]{1,}$/", $note_courante))&&
									(mb_strtolower($note_courante)!="di")&&
									(mb_strtolower($note_courante)!="ab")
									) {
										$msg.="Note pour ".$current_login_ele." invalide : ".$tab[1]."<br />";
										$pb_record="yes";
									}
									else {
										$sql="DELETE FROM notanet_saisie WHERE login='".$current_login_ele."' AND matiere='$matiere';";
										$del=mysql_query($sql);

										//$sql="INSERT INTO notanet_saisie SET login='".$current_login_ele."', matiere='$matiere', note='".$tab[1]."';";
										$sql="INSERT INTO notanet_saisie SET login='".$current_login_ele."', matiere='$matiere', note='".$note_courante."';";
										//echo "$sql<br />";
										$register=mysql_query($sql);
										if (!$register) {
											$msg .= "Erreur lors de l'enregistrement des données pour ".$current_login_ele.".<br />";
										}
										else {
											$nb_reg++;
										}
									}
								}
							}
						}
					}
				}
			}
			if($nb_reg>0) {
				$msg.="$nb_reg enregistrement(s) effectué(s).<br />";
			}
			if($nb_ele_autre_groupe>0) {
				$msg.="<br /><span title=\"".substr($info_ele_autre_groupe,2)."\">$nb_ele_autre_groupe élève(s) n'appartiennent pas au groupe courant.<br />Ils n'ont pas été enregistrés.<br />(<em>La liste des élèves vous sera affichée lors du survol du présent message</em>)</span>.<br />";
			}
		}
	}
}

$themessage = 'Des modifications ont été effectuées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";

//**************** EN-TETE *****************
$titre_page = "Notanet | Saisie notes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

/*

$notanet_saisie_note_ouverte=getSettingValue("notanet_saisie_note_ouverte");
if($notanet_saisie_note_ouverte) {
	$notanet_saisie_note_ouverte="n";
}
*/

$tmp_timeout=(getSettingValue("sessionMaxLength"))*60;

if((isset($_POST['temoin_suhosin_1']))&&(!isset($_POST['temoin_suhosin_2']))) {
	echo "<p style='color:red; font-weight:bold; text-align:center;'>Il semble que certaines variables n'ont pas été transmises.<br />Cela peut arriver lorsqu'on tente de transmettre trop de variables.<br />Vous devriez opter pour un autre mode d'extraction.</p>\n";
	echo "<div style='margin-left:3em; background-image: url(\"../images/background/opacite50.png\");'>";
	echo alerte_config_suhosin();
	echo "</div>\n";
	echo "<p><br /></p>\n";
}

?>
<script type="text/javascript" language="javascript">
change = 'no';
</script>

<div class='norme'>
<p class="bold"><a href="../accueil.php" onclick="return confirm_abandon(this, change, '<?php echo $themessage; ?>')"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>

<?php

echo " | <a href='index.php'>Accueil Notanet</a>";

// On verra s'il y a des possibilité d'importation:
if((isset($type_brevet))&&(isset($id_classe))&&(isset($id_groupe))&&(isset($matiere))) {
	echo " | <a href='".$_SERVER['PHP_SELF']."?mode=import_csv&amp;type_brevet=$type_brevet&amp;id_classe=$id_classe&amp;id_groupe=$id_groupe&amp;matiere=$matiere'>Importer un CSV</a>\n";
}
//echo " | <a href='".$_SERVER['PHP_SELF']."?mode=import_xml'>Importer un XML</a>\n";

$sql="SELECT DISTINCT type_brevet FROM notanet_corresp WHERE $sql_indices_types_brevets ORDER BY type_brevet";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association matières/type de brevet n'a encore été réalisée.";
	if(acces("/mod_notanet/select_matieres.php", $_SESSION['statut'])) {
		echo "<br />Commencez par <a href='select_matieres.php'>sélectionner les matières</a>";
	}
	echo "</p>\n";

	require("../lib/footer.inc.php");
	die();
}

$sql="SELECT DISTINCT type_brevet FROM notanet_corresp WHERE $sql_indices_types_brevets AND mode='saisie' ORDER BY type_brevet";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune matière Notanet n'a été paramétrée comme devant être saisie ici.";
	if(acces("/mod_notanet/select_matieres.php", $_SESSION['statut'])) {
		echo "<br />Commencez par <a href='select_matieres.php'>définir les matières devant être saisies</a>";
	}
	echo "</p>\n";

	require("../lib/footer.inc.php");
	die();
}

if(mysql_num_rows($res)==1) {
	$lig=mysql_fetch_object($res);
	$type_brevet=$lig->type_brevet;
}
elseif(!isset($type_brevet)) {
	echo "</p>\n";
	echo "</div>\n";

	if(!getSettingAOui("notanet_saisie_note_ouverte")) {
		echo "<p style='color:red'>La saisie de notes est actuellement fermée.<br />Seule la consultation est possible.</p>";
	}

	echo "<p>Pour quel type de brevet souhaitez-vous effectuer des saisies&nbsp;?";
	echo "</p>\n";
	echo "<ul>\n";
	while($lig=mysql_fetch_object($res)) {
		echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>Saisir des notes pour la série ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
	}
	echo "</ul>\n";

	require("../lib/footer.inc.php");
	die();
}


if((!isset($id_classe))||(!isset($id_groupe))) {
	// Classes concernées:

	if($_SESSION['statut']=='professeur') {
		$sql="SELECT DISTINCT jec.id_classe, c.classe FROM notanet_ele_type net,
				j_eleves_groupes jeg,
				j_groupes_professeurs jgp,
				j_groupes_matieres jgm,
				notanet_corresp nc,
				j_eleves_classes jec,
				classes c
			WHERE net.login=jeg.login AND
				jeg.login=jec.login AND
				jeg.id_groupe=jgp.id_groupe AND
				jgp.login='".$_SESSION['login']."' AND
				jeg.id_groupe=jgm.id_groupe AND
				jgm.id_matiere=nc.matiere AND
				nc.mode='saisie' AND
				jec.id_classe=c.id;";
	}
	else {
		$sql="SELECT DISTINCT id_classe, classe FROM classes c, j_eleves_classes jec, notanet_ele_type n
			WHERE c.id=jec.id_classe AND
					jec.login=n.login AND
					n.type_brevet='$type_brevet'
			ORDER BY c.classe";
	}
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre série</a></p>\n";
		echo "</div>\n";

		echo "<h2>Saisie pour le brevet série ".$tab_type_brevet[$type_brevet]."</h2>";

		echo "<p>Aucune classe n'est associée à ce type de brevet.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre série</a></p>\n";
	echo "</div>\n";

	echo "<h2>Saisie pour le brevet série ".$tab_type_brevet[$type_brevet]."</h2>";

	if(!getSettingAOui("notanet_saisie_note_ouverte")) {
		echo "<p style='color:red'>La saisie de notes est actuellement fermée.<br />Seule la consultation est possible.</p>";
	}

	echo "<p>Pour quelle classe/enseignement, souhaitez-vous effectuer une saisie&nbsp;:</p>";
	echo "<table class='boireaus boireaus_alt'>";
	while($lig=mysql_fetch_object($res)) {
		echo "
	<tr>
		<th>".$lig->classe."</th>
		<td>";


		if($_SESSION['statut']=='professeur') {
			$sql="SELECT DISTINCT nc.matiere FROM j_eleves_classes jec, 
						j_eleves_groupes jeg,
						j_groupes_professeurs jgp,
						j_groupes_matieres jgm,
						notanet_corresp nc
					WHERE jec.login=jeg.login AND
						jec.id_classe='$lig->id_classe' AND
						jeg.id_groupe=jgp.id_groupe AND
						jgp.login='".$_SESSION['login']."' AND
						jeg.id_groupe=jgm.id_groupe AND
						jgm.id_matiere=nc.matiere AND
						nc.type_brevet='$type_brevet' AND
						nc.mode='saisie';";
		}
		else {
			$sql="SELECT DISTINCT nc.matiere FROM j_eleves_classes jec, 
						j_eleves_groupes jeg,
						j_groupes_matieres jgm,
						notanet_corresp nc
					WHERE jec.login=jeg.login AND
						jec.id_classe='$lig->id_classe' AND
						jeg.id_groupe=jgm.id_groupe AND
						jgm.id_matiere=nc.matiere AND
						nc.type_brevet='$type_brevet' AND
						nc.mode='saisie';";
		}
		//echo "$sql<br />";
		$res_matiere_notanet=mysql_query($sql);
		while($lig_mn=mysql_fetch_object($res_matiere_notanet)) {
			echo $lig_mn->matiere."</td>
		</td>
		<td style='text-align:left'>";

			$sql="SELECT DISTINCT jeg.id_groupe FROM j_eleves_classes jec, 
						j_eleves_groupes jeg,
						j_groupes_matieres jgm
					WHERE jec.login=jeg.login AND
						jec.id_classe='$lig->id_classe' AND
						jeg.id_groupe=jgm.id_groupe AND
						jgm.id_matiere='$lig_mn->matiere';";
			//echo "$sql<br />";
			$res_grp=mysql_query($sql);
			while($lig_grp=mysql_fetch_object($res_grp)) {
				$sql="SELECT DISTINCT ns.* FROM notanet_saisie ns, 
					j_eleves_classes jec, 
					j_eleves_groupes jeg,
					j_groupes_matieres jgm,
					notanet_corresp nc
				WHERE ns.login=jec.login AND
					ns.note!='' AND
					jec.login=jeg.login AND
					jec.id_classe='$lig->id_classe' AND
					jeg.id_groupe=jgm.id_groupe AND
					jeg.id_groupe='$lig_grp->id_groupe' AND
					jgm.id_matiere='$lig_mn->matiere' AND
					jgm.id_matiere=nc.matiere AND
					nc.type_brevet='$type_brevet';";
				//echo "$sql<br />";
				$res_eff=mysql_query($sql);
				echo "<span class='bold' title=\"Nombre de notes saisies\">".mysql_num_rows($res_eff)."&nbsp;:</span> ";
				echo "<a href='".$_SERVER['PHP_SELF']."?type_brevet=$type_brevet&amp;id_classe=$lig->id_classe&amp;id_groupe=$lig_grp->id_groupe&amp;matiere=$lig_mn->matiere'>".get_info_grp($lig_grp->id_groupe)."</a><br />";
			}

		}

		echo "
		</td>
	</tr>";
	}
	echo "
</table>";

	require("../lib/footer.inc.php");
	die();
}

echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre série</a>";
echo " | <a href='".$_SERVER['PHP_SELF']."?type_brevet=$type_brevet'>Choisir une autre classe</a></p>\n";
echo "</div>\n";

if((isset($mode))&&($mode=='import_csv')) {
	echo "</p>\n";

	$current_group=get_group($id_groupe);

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		<p>Vous pouvez importer les saisies pour l'enseignement ".$current_group['name']." (<em>".$current_group['description']." en ".$current_group['classlist_string']."</em>).<br />
		Deux formats de fichier CSV sont acceptés&nbsp;:</p>

		<br />

		<p style='text-indent: -2em; margin-left: 2em;'><input type='radio' name='type_csv' id='type_csv_1' value='1' checked /><label for='type_csv_1'> Fichier au format <strong>INE;Nom;Prenom;Moyenne</strong><br />
		(<em>dans ce fichier seuls les champs INE et Moyenne sont vraiment pris en compte</em>)</label></p>

		<p style='text-indent: -2em; margin-left: 2em;'><input type='radio' name='type_csv' id='type_csv_2' value='2' /><label for='type_csv_2'> Fichier au format <strong>Identifiant GEPI;Moyenne DNB</strong><br />
		(<em>ATTENTION&nbsp;: Le PackEPS suppose que l'identifiant est au format &lt;NOM&gt;_&lt;Initiale_du_Prenom&gt;;<br />ce n'est pas nécessairement le cas sur votre GEPI</em>)</label></p>

		<br />

		<p>Veuillez fournir le fichier&nbsp;:<br />
		".add_token_field()."
		<input type='hidden' name='type_brevet' value='$type_brevet' />
		<input type='hidden' name='id_classe' value='$id_classe' />
		<input type='hidden' name='id_groupe' value='$id_groupe' />
		<input type='hidden' name='matiere' value='$matiere' />
		<input type='hidden' name='action' value='upload_file' />
		<input type=\"file\" size=\"80\" name=\"csv_file\" /><br />
		<input type='submit' value='Valider' /></p>
	</fieldset>
</form>\n";

	require("../lib/footer.inc.php");
	die();
}

echo "<h2>Saisie pour le brevet série ".$tab_type_brevet[$type_brevet]."</h2>";

// VERIFIER QUE LA CLASSE ET LE GROUPE CONVIENNENT:
$sql="SELECT 1=1 FROM j_eleves_classes jec, 
	j_eleves_groupes jeg,
	j_groupes_matieres jgm,
	notanet_corresp nc
WHERE jec.login=jeg.login AND
	jec.id_classe='$id_classe' AND
	jeg.id_groupe=jgm.id_groupe AND
	jeg.id_groupe='$id_groupe' AND
	jgm.id_matiere='$matiere' AND
	jgm.id_matiere=nc.matiere AND
	nc.type_brevet='$type_brevet';";
//echo "$sql<br />";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {

	echo "<p>Le choix effectué ne convient pas<br />$sql</p>";

	require("../lib/footer.inc.php");
	die();
}

if(!getSettingAOui("notanet_saisie_note_ouverte")) {
	echo "<p style='color:red'>La saisie de notes est actuellement fermée.<br />Seule la consultation est possible.</p>";
}

$notanet_saisie_note_ouverte=getSettingAOui("notanet_saisie_note_ouverte");

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='is_posted' value='y' />
		<input type='hidden' name='type_brevet' value='$type_brevet' />
		<input type='hidden' name='id_classe' value='$id_classe' />
		<input type='hidden' name='id_groupe' value='$id_groupe' />
		<input type='hidden' name='matiere' value='$matiere' />

		<p>Saisie des notes de $matiere pour les élèves du groupe ".get_info_grp($id_groupe)." en classe de ".get_classe_from_id($id_classe)."&nbsp;:</p>";

$sql="SELECT * FROM notanet_saisie ns,
	j_eleves_classes jec, 
	j_eleves_groupes jeg,
	notanet_corresp nc
WHERE ns.login=jec.login AND
	ns.matiere=nc.matiere AND
	jec.login=jeg.login AND
	jec.id_classe='$id_classe' AND
	jeg.id_groupe='$id_groupe' AND
	nc.matiere='$matiere' AND
	nc.type_brevet='$type_brevet';";
//echo "$sql<br />";
$res_notes_deja_saisies=mysql_query($sql);
$tab_notes_saisies=array();
while($lig=mysql_fetch_object($res_notes_deja_saisies)) {
	$tab_notes_saisies[$lig->login]=$lig->note;
}

$sql="SELECT DISTINCT jeg.login FROM j_eleves_classes jec, 
	j_eleves_groupes jeg,
	j_groupes_matieres jgm,
	notanet_corresp nc
WHERE jec.login=jeg.login AND
	jec.id_classe='$id_classe' AND
	jeg.id_groupe=jgm.id_groupe AND
	jeg.id_groupe='$id_groupe' AND
	jgm.id_matiere='$matiere' AND
	jgm.id_matiere=nc.matiere AND
	nc.type_brevet='$type_brevet';";
//echo "$sql<br />";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {

	echo "
		<p>Aucun élève n'a été trouvé.<br />$sql</p>
	</fieldset>
</form>";

	require("../lib/footer.inc.php");
	die();
}

if($notanet_saisie_note_ouverte) {
	echo "		<div id=\"fixe\"><p><input type='submit' value='Enregistrer' /></p></div>";
}
echo "
		<div style='float:right; width:10em; color:red;'>
			A FAIRE : Préciser la liste des valeur acceptées (AB, DI,...)
		</div>

		<input type='hidden' name='temoin_suhosin_1' value='y' />

		<table class='boireaus boireaus_alt'>
			<tr>
				<th rowspan='2'>Élève</th>
				<th colspan='3'>Périodes</th>
				<th rowspan='2' title=\"Moyenne des trois périodes\">Moyenne</th>
				<th rowspan='2'>Note</th>
			</tr>
			<tr>";
echo "
				<th>P.1</th>
				<th>P.2</th>
				<th>P.3</th>";
echo "
			</tr>";
$cpt=10;
while($lig=mysql_fetch_object($res)) {
	echo "
			<tr>
				<td id='td_nom_$cpt'>
					<input type='hidden' name='ele_login[$cpt]' id='ele_login_$cpt' value='".$lig->login."' />
					".get_nom_prenom_eleve($lig->login)."
				</td>";

	for($i=1;$i<=3;$i++) {
		$note="-";

		$sql="SELECT round(avg(mn.note),1) as moyenne FROM matieres_notes mn WHERE (mn.id_groupe='".$id_groupe."' AND mn.login='".$lig->login."' AND mn.statut ='' AND periode='$i');";
		$res_note=mysql_query($sql);
		if(mysql_num_rows($res_note)>0) {
			$lig_note=mysql_fetch_object($res_note);
			$note=$lig_note->moyenne;
		}
		echo "
				<td>
					$note
				</td>";
	}

	$note="-";
	$sql="SELECT round(avg(mn.note),1) as moyenne FROM matieres_notes mn WHERE (mn.id_groupe='".$id_groupe."' AND mn.login='".$lig->login."' AND mn.statut ='');";
	$res_note=mysql_query($sql);
	if(mysql_num_rows($res_note)>0) {
		$lig_note=mysql_fetch_object($res_note);
		$note=$lig_note->moyenne;
	}
	echo "
				<td>
					$note
				</td>";

	echo "
				<td id='td_note_$cpt'>";
	if($notanet_saisie_note_ouverte) {
		echo "
					<input type='text' name='note[$cpt]' id='n$cpt' value='";
		if(isset($tab_notes_saisies[$lig->login])) {
			echo $tab_notes_saisies[$lig->login];
		}
		echo "' size='3' autocomplete='off' onKeyDown=\"clavier(this.id,event);\" onchange=\"verifcol($cpt);changement();\" />";
	}
	else {
		if(isset($tab_notes_saisies[$lig->login])) {
			echo $tab_notes_saisies[$lig->login];
		}
	}
	echo "
				</td>
			</tr>";
	$cpt++;
}
echo "
		</table>
		<input type='hidden' name='temoin_suhosin_2' value='y' />";
if($notanet_saisie_note_ouverte) {
	echo "
		<p><input type='submit' value='Enregistrer' /></p>";
}
echo "
	</fieldset>
</form>";

// A revoir: utiliser tabmatieres pour avoir le note_sur_verif
$note_sur_verif=20;
$couleur_devoirs="";

echo "
<script type='text/javascript' language='JavaScript'>

function verifcol(num_id){
	document.getElementById('n'+num_id).value=document.getElementById('n'+num_id).value.toLowerCase();
	if(document.getElementById('n'+num_id).value=='a'){
		document.getElementById('n'+num_id).value='AB';
	}
	if(document.getElementById('n'+num_id).value=='ab'){
		document.getElementById('n'+num_id).value='AB';
	}
	if(document.getElementById('n'+num_id).value=='d'){
		document.getElementById('n'+num_id).value='DI';
	}
	if(document.getElementById('n'+num_id).value=='di'){
		document.getElementById('n'+num_id).value='DI';
	}

	note=document.getElementById('n'+num_id).value;

	if((note!='DI')&&(note!='AB')&&(note!='')){
		note=note.replace(',','.');

		//if((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0))){

		if(((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0)))||
		((note.search(/^[0-9,]+$/)!=-1)&&(note.lastIndexOf(',')==note.indexOf(',',0)))){
			if((note>".$note_sur_verif.")||(note<0)){
				couleur='red';
			}
			else{
				couleur='$couleur_devoirs';
			}
		}
		else{
			couleur='red';
		}
	}
	else{
		couleur='$couleur_devoirs';
	}
	eval('document.getElementById(\'td_nom_'+num_id+'\').style.background=couleur');
	eval('document.getElementById(\'td_note_'+num_id+'\').style.background=couleur');
}

for(i=0;i<$cpt;i++) {
	if(document.getElementById('td_nom_'+i)) {
		verifcol(i);
	}
}
</script>
";

require("../lib/footer.inc.php");
die();
?>
