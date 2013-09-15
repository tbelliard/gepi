<?php

/*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/groupes/ajout_groupes_csv.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/groupes/ajout_groupes_csv.php',
	administrateur='V',
	professeur='F',
	cpe='F',
	scolarite='F',
	eleve='F',
	responsable='F',
	secours='F',
	autre='F',
	description='Groupes : Ajout de groupes depuis un CSV',
	statut='';";
	$insert=mysql_query($sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

function get_nom_complet_from_matiere($mat) {
	$sql="SELECT nom_complet FROM matieres WHERE matiere='$mat';";
	$res_mat=mysql_query($sql);
	if(mysql_num_rows($res_mat)>0) {
		$lig_mat=mysql_fetch_object($res_mat);
		return $lig_mat->nom_complet;
	}
}

$step=isset($_POST['step']) ? $_POST['step'] : NULL;

//**************** EN-TETE *****************
$titre_page = "Ajout de groupes depuis un CSV";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//include("init_xml_lib.php");

//debug_var();

// On va uploader le CSV dans le tempdir de l'utilisateur (administrateur)
$tempdir=get_user_temp_directory();
if(!$tempdir) {
	echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
	// Il ne faut pas aller plus loin...
	// SITUATION A GERER
}


	echo "<center><h3 class='gepi'>Ajout de groupes par lots depuis un CSV</h3></center>\n";

	if(!isset($step)) {
		echo "<p class=bold><a href='../classes/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
		echo "</p>\n";

		echo "
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset style='border: 1px solid grey;background-image: url(\"../images/background/opacite50.png\"); '>
		<p class='bold'>Upload du fichier CSV.</p>
		<p>Veuillez fournir le fichier CSV&nbsp;:<br />
		<input type=\"file\" size=\"65\" name=\"csv_file\" style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /><br />
		<input type='hidden' name='step' value='0' />
		<input type='hidden' name='is_posted' value='yes' />
		".add_token_field()."
		<p><input type='submit' value='Valider' /></p>
	</fieldset>
</form>

<p><i>Remarques</i>&nbsp;:</p>
<ul>
	<li>
		Les champs du CSV sont&nbsp;:<br />
		NOM_GROUPE;DESCRIPTION_GROUPE;ID_MATIERE_GEPI;NOM_DES_CLASSES;LOGIN_PROFS<br />
		Si plusieurs classes sont associées au groupe, les séparer par un | (<em>'pipe' obtenu par AltGr+6</em>).<br />
		Si plusieurs professeurs sont associées au groupe, les séparer par un | (<em>'pipe' obtenu par AltGr+6</em>).<br />
	</li>
	<li>
		La ligne d'entête<br />
		NOM_GROUPE;DESCRIPTION_GROUPE;ID_MATIERE_GEPI;NOM_DES_CLASSES;LOGIN_PROFS<br />
		est requise dans le CSV.
	</li>
	<li>
		<span style='color:red'>Pour le moment, aucun élève n'est mis dans les groupes créés, mais il faudra permettre de mettre tous/aucun/1ère_moitié/...</span>
	</li>
	<li>
		<span style='color:red'>Si le prof proposé n'est pas prof dans la matière indiquée, faire l'association.</span>
	</li>
</ul>\n";

		/*
			grep AIDE udt12-09.csv |cut -d";" -f3,4,5,7,8
		*/
	}
	else {
		echo "<p class=bold><a href='../classes/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
		echo " | <a href='".$_SERVER['PHP_SELF']."'>Autre import</a>";
		echo "</p>\n";

		$post_max_size=ini_get('post_max_size');
		$upload_max_filesize=ini_get('upload_max_filesize');
		$max_execution_time=ini_get('max_execution_time');
		$memory_limit=ini_get('memory_limit');

		if($step==0) {
			$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

			if(!is_uploaded_file($csv_file['tmp_name'])) {
				echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

				echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
				echo "post_max_size=$post_max_size<br />\n";
				echo "upload_max_filesize=$upload_max_filesize<br />\n";
				echo "</p>\n";

				// Il ne faut pas aller plus loin...
				// SITUATION A GERER
				require("../lib/footer.inc.php");
				die();
			}
			else {
				if(!file_exists($csv_file['tmp_name'])) {
					echo "<p style='color:red;'>Le fichier aurait été uploadé... mais ne serait pas présent/conservé.</p>\n";

					echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
					echo "post_max_size=$post_max_size<br />\n";
					echo "upload_max_filesize=$upload_max_filesize<br />\n";
					echo "et le volume de ".$csv_file['name']." serait<br />\n";
					echo "\$csv_file['size']=".volume_human($csv_file['size'])."<br />\n";
					echo "</p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				echo "<p>Le fichier a été uploadé.</p>\n";

				//$source_file=stripslashes($csv_file['tmp_name']);
				$source_file=$csv_file['tmp_name'];
				$dest_file="../temp/".$tempdir."/export_udt.csv";
				$res_copy=copy("$source_file" , "$dest_file");

				if(!$res_copy) {
					echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
					require("../lib/footer.inc.php");
					die();
				}
				else {
					echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

					echo "<br /><p>Veuillez maintenant choisir les enseignements à créer&nbsp;:</p>\n";

					$tabchamps = array("NOM_GROUPE", "DESCRIPTION_GROUPE", "ID_MATIERE_GEPI", "NOM_DES_CLASSES", "LOGIN_PROFS");

					// Lire ligne 1 et la mettre dans $temp
					$fp=fopen($dest_file,"r");
					if(!$fp) {
						echo "<p>ERREUR lors de la tentative d'ouverture du fichier $dest_file</p>\n";
						require("../lib/footer.inc.php");
						die();
					}

					$sql="TRUNCATE tempo2;";
					$menage=mysql_query($sql);

					// Lecture de la ligne 1 et la mettre dans $temp
					$temp=fgets($fp,4096);
					$en_tete=explode(";",$temp);

					$sql="INSERT INTO tempo2 SET col1='en_tete', col2='".mysql_real_escape_string($temp)."';";
					$menage=mysql_query($sql);

					// On range dans tabindice les indices des champs retenus
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (remplace_accents($en_tete[$i]) == remplace_accents($tabchamps[$k])) {
								$tabindice[] = $i;
							}
						}
					}
					if(count($tabindice)==0) {
						echo "<p style='color:red'>Ligne d'entête non trouvée.</p>";
						require("../lib/footer.inc.php");
						die();
					}

					// Lire le fichier
					$ligne=array();
					while(!feof($fp)){
						$ligne[]=fgets($fp,4096);
					}
					fclose($fp);

					echo "
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>
	<input type='hidden' name='step' value='1' />
	".add_token_field()."
	<table class='boireaus boireaus_alt' summary=''>
		<tr>
			<th>Cocher<br />
			<a href='javascript:CocheColonne()'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/
			<a href='javascript:DecocheColonne()'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>
			</th>
			<th>CSV</th>
			<th>Nom</th>
			<th>Description</th>
			<th>Matière</th>
			<th>Classes</th>
			<th>Professeurs</th>
			<th>Groupes existants avec ces professeurs (<em>dans ces classes</em>)</th>
		</tr>\n";

					for($i=0;$i<count($ligne);$i++) {
						//echo "\$ligne[$i]=$ligne[$i]<br />";
						if($ligne[$i]!='') {
							$tab=explode(";",$ligne[$i]);

							$nom_groupe=$tab[$tabindice[0]];
							if($nom_groupe=="") {
								echo "
		<tr>
			<td></td>
			<td>".$ligne[$i]."</td>
			<td colspan='6' style='color:red'>Le nom du groupe ne peut pas être vide.</td>
		</tr>";
							}
							else {
								$description_groupe=$tab[$tabindice[1]];
								if($description_groupe=="") {
									$description_groupe=$nom_groupe;
								}

								$matiere_inconnue="y";
								$matiere=$tab[$tabindice[2]];
								$sql="SELECT * FROM matieres WHERE matiere='".$matiere."';";
								$res_mat=mysql_query($sql);
								if(mysql_num_rows($res_mat)>0) {
									$matiere_inconnue="n";
								}

								$chaine_jgc="";
								$classe_inconnue="n";
								$tab_classes_inconnues=array();
								$nom_des_classes=$tab[$tabindice[3]];
								$tab_classe=array();
								if($nom_des_classes=="") {
									$classe_inconnue="y";
								}
								else {
									if(preg_match("/|/", $nom_des_classes)) {
										$tab_classe=explode("|", $nom_des_classes);
									}
									else {
										$tab_classe[]=$nom_des_classes;
									}
									for($loop=0;$loop<count($tab_classe);$loop++) {
										$sql="SELECT * FROM classes WHERE classe='".$tab_classe[$loop]."';";
										$res_classe=mysql_query($sql);
										if(mysql_num_rows($res_classe)==0) {
											$classe_inconnue="y";
											$tab_classes_inconnues[]=$tab_classe[$loop];
										}
										else {
											$id_classe=mysql_result($res_classe, 0, "id");
											if($chaine_jgc!="") {$chaine_jgc.=" OR ";}
											$chaine_jgc.="jgc.id_classe='$id_classe'";
										}
									}
								}

								$chaine_jgp="";
								$prof_inconnu="n";
								$tab_profs_inconnus=array();
								$login_profs=$tab[$tabindice[4]];
								$tab_profs=array();
								if($login_profs=="") {
									$prof_inconnu="y";
								}
								else {
									if(preg_match("/|/", $login_profs)) {
										$tab_profs=explode("|", $login_profs);
									}
									else {
										$tab_profs[]=$login_profs;
									}
									for($loop=0;$loop<count($tab_profs);$loop++) {
										$sql="SELECT 1=1 FROM utilisateurs WHERE login='".$tab_profs[$loop]."' AND statut='professeur';";
										$res_prof=mysql_query($sql);
										if(mysql_num_rows($res_prof)==0) {
											$prof_inconnu="y";
											$tab_profs_inconnus[]=$tab_profs[$loop];
										}
										else {
											if($chaine_jgp!="") {$chaine_jgp.=" OR ";}
											$chaine_jgp.="login='".$tab_profs[$loop]."'";
										}
									}
								}

								$sql="INSERT INTO tempo2 SET col1='$i', col2='".mysql_real_escape_string($ligne[$i])."';";
								$insert=mysql_query($sql);

								echo "
		<tr>
			<td>";
								if(($matiere_inconnue=="n")&&($classe_inconnue=="n")&&($prof_inconnu="n")) {
									echo "
				<input type='checkbox' name='ligne_a_enregistrer[]' id='ligne_a_enregistrer_$i' value='$i' onchange=\"checkbox_change('ligne_a_enregistrer_$i')\" />";
								}
								echo "
			</td>
			<td><label for='ligne_a_enregistrer_$i' id='texte_ligne_a_enregistrer_$i'>".$ligne[$i]."</label></td>
			<td>".$nom_groupe."</td>
			<td>".$description_groupe."</td>
			<td>".(($matiere_inconnue=="y") ? "<span style='color:red'>".$matiere."</span>" : $matiere)."</td>
			<td>";
								for($loop=0;$loop<count($tab_classe);$loop++) {
									if($loop>0) {
										echo ", ";
									}
									if(!in_array($tab_classe[$loop], $tab_classes_inconnues)) {
										echo $tab_classe[$loop];
									}
									else {
										echo "<span style='color:red'>".$tab_classe[$loop]."</span>";
									}
								}
								echo "
			</td>
			<td>";
								for($loop=0;$loop<count($tab_profs);$loop++) {
									if($loop>0) {
										echo ", ";
									}
									if(!in_array($tab_profs[$loop], $tab_profs_inconnus)) {
										echo $tab_profs[$loop];
									}
									else {
										echo "<span style='color:red'>".$tab_profs[$loop]."</span>";
									}
								}
								echo "
			</td>
			<td>";
								if($chaine_jgp!="") {
									$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe";
									if($chaine_jgc!="") {$sql.=" AND ($chaine_jgc)";}
									if($chaine_jgp!="") {$sql.=" AND ($chaine_jgp);";}
									//echo "$sql<br />";
									$res_grp=mysql_query($sql);
									if(mysql_num_rows($res_grp)>0) {
										echo "<span style='font-size:x-small'>";
										$cpt_grp=0;
										while($lig_grp=mysql_fetch_object($res_grp)) {
											if($cpt_grp>0) {
												echo "<br />";
											}
											echo get_info_grp($lig_grp->id_groupe, array('classes', 'profs'));
											$cpt_grp++;
										}
										echo "</span>";
									}
								}
								echo "
			</td>
		</tr>";
/*
        $sql="SELECT * FROM matieres WHERE matiere='$reg_matiere'";
        $resultat_recup_matiere=mysql_query($sql);


    $reg_nom_groupe = html_entity_decode($_POST['groupe_nom_court']);
    $reg_nom_complet = html_entity_decode($_POST['groupe_nom_complet']);
    $reg_matiere = $_POST['matiere'];
    $reg_categorie = $_POST['categorie'];
    
    
	$reg_nom_groupe = html_entity_decode($_POST['groupe_nom_court'],ENT_QUOTES,"UTF-8");
	//$reg_nom_complet = html_entity_decode($_POST['groupe_nom_complet']);
	$reg_nom_complet = html_entity_decode($_POST['groupe_nom_complet'],ENT_QUOTES,"UTF-8");



*/


							}
						}
					}
					echo "
	</table>
	<input type='hidden' name='is_posted' value='yes' />
	<p style='text-align:center;'><input type='submit' value='Valider' /></p>
</form>

<script type='text/javascript'>
	function CocheColonne() {
		for (var ki=0;ki<$i;ki++) {
			if(document.getElementById('ligne_a_enregistrer_'+ki)){
				document.getElementById('ligne_a_enregistrer_'+ki).checked = true;
				checkbox_change('ligne_a_enregistrer_'+ki);
			}
		}
	}

	function DecocheColonne() {
		for (var ki=0;ki<$i;ki++) {
			if(document.getElementById('ligne_a_enregistrer_'+ki)){
				document.getElementById('ligne_a_enregistrer_'+ki).checked = false;
				checkbox_change('ligne_a_enregistrer_'+ki);
			}
		}
	}

	".js_checkbox_change_style('checkbox_change', 'texte_', 'n')."

</script>";
				}

				// Ménage:
				unlink($dest_file);

				echo "<p><em>NOTES&nbsp;:</em> <span style='color:red'>A FAIRE : AJOUTER UN TEST POUR DETECTER SI LES GROUPES PROPOSES EXISTENT DEJA.</span></p>";

			}
		}
		elseif($step==1) {

			if(!isset($_POST['is_posted'])) {
				echo "<p style='color:red'>ERREUR&nbsp;: Une partie des variables n'as pas été POSTée.<br />Vous avez probablement un module PHP qui limite le nombre de variables transmises (<i>suhosin?</i>)</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			$ligne_a_enregistrer=isset($_POST['ligne_a_enregistrer']) ? $_POST['ligne_a_enregistrer'] : array();
			if(count($ligne_a_enregistrer)==0) {
				echo "<p style='color:red'>Aucune ligne n'a été sélectionnée.</p>\n";
				echo "<p'><a href='".$_SERVER['PHP_SELF']."'>Retour</a></p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			echo "<p class='bold'>Enregistrement des enseignements&nbsp;:</p>\n";

			$tabchamps = array("NOM_GROUPE", "DESCRIPTION_GROUPE", "ID_MATIERE_GEPI", "NOM_DES_CLASSES", "LOGIN_PROFS");

			$sql="SELECT col2 FROM tempo2 WHERE col1='en_tete';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				echo "<p style='color:red'>Ligne d'entête non trouvée.</p>\n";
				echo "<p'><a href='".$_SERVER['PHP_SELF']."'>Retour</a></p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			$en_tete=explode(";", mysql_result($res, 0, "col2"));

			// On range dans tabindice les indices des champs retenus
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					if (remplace_accents($en_tete[$i]) == remplace_accents($tabchamps[$k])) {
						$tabindice[] = $i;
					}
				}
			}
			if(count($tabindice)==0) {
				echo "<p style='color:red'>Ligne d'entête non trouvée.</p>";
				require("../lib/footer.inc.php");
				die();
			}


			for($i=0;$i<count($ligne_a_enregistrer);$i++) {
				$sql="SELECT * FROM tempo2 WHERE col1='".$ligne_a_enregistrer[$i]."';";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)==0) {
					echo "<span style='color:red'>Ligne n°".$ligne_a_enregistrer[$i]." non trouvée.</span><br />\n";
				}
				else {
					$col2=mysql_result($res, 0, "col2");
					if($col2!='') {
						$tab=explode(";",$col2);

						$nom_groupe=$tab[$tabindice[0]];
						if($nom_groupe!="") {
							$description_groupe=$tab[$tabindice[1]];
							if($description_groupe=="") {
								$description_groupe=$nom_groupe;
							}
							elseif($description_groupe!=$nom_groupe) {
								$description_groupe.=" ($nom_groupe)";
							}

							$matiere=$tab[$tabindice[2]];
							$sql="SELECT * FROM matieres WHERE matiere='".$matiere."';";
							$res_mat=mysql_query($sql);
							if(mysql_num_rows($res_mat)>0) {
								$categorie_id=mysql_result($res_mat,0,"categorie_id");

								$tab_classes_inconnues=array();
								$nom_des_classes=$tab[$tabindice[3]];
								$tab_classe=array();
								if($nom_des_classes!="") {
									if(preg_match("/|/", $nom_des_classes)) {
										$tab_classe=explode("|", $nom_des_classes);
									}
									else {
										$tab_classe[]=$nom_des_classes;
									}

// IL FAUDRAIT VERIFIER QUE LES CLASSES ONT LE MEME NOMBRE DE PERIODES

									$reg_clazz=array();
									for($loop=0;$loop<count($tab_classe);$loop++) {
										$sql="SELECT * FROM classes WHERE classe='".$tab_classe[$loop]."';";
										$res_classe=mysql_query($sql);
										if(mysql_num_rows($res_classe)==0) {
											$tab_classes_inconnues[]=$tab_classe[$loop];
										}
										else {
											$id_classe=mysql_result($res_classe, 0, "id");
											if(!in_array($id_classe, $reg_clazz)) {
												$reg_clazz[]=$id_classe;
											}
										}
									}

									if(count($tab_classes_inconnues)<count($tab_classe)) {
										$tab_profs_inconnus=array();
										$login_profs=$tab[$tabindice[4]];
										$tab_profs=array();
										$tmp_tab_profs=array();
										if($login_profs!="") {
											if(preg_match("/|/", $login_profs)) {
												$tmp_tab_profs=explode("|", $login_profs);
											}
											else {
												$tmp_tab_profs[]=$login_profs;
											}

											for($loop=0;$loop<count($tmp_tab_profs);$loop++) {
												$sql="SELECT 1=1 FROM utilisateurs WHERE login='".$tmp_tab_profs[$loop]."' AND statut='professeur';";
												$res_prof=mysql_query($sql);
												if(mysql_num_rows($res_prof)==0) {
													$tab_profs_inconnus[]=$tmp_tab_profs[$loop];
												}
												else {
													$tab_profs[]=$tmp_tab_profs[$loop];
												}
											}


											echo "<p><span style='color:green'>".$col2."</span><br />";
											$create = create_group($nom_groupe, $description_groupe, $matiere, $reg_clazz, $categorie_id);
											if (!$create) {
												echo "<span style='color:red'>Erreur lors de la création du groupe.</span></p>\n";
											} else {
												echo "Enseignement <a href='edit_group.php?id_groupe=$create&amp;mode=regroupement'>n°$create ($nom_groupe (<span style='font-size:x-small'>$description_groupe</span>))</a> bien créé.<br />\n";

												if(count($tab_profs)>0) {

													$tab_profs_matiere=array();
													$sql="SELECT DISTINCT id_professeur FROM j_professeurs_matieres WHERE id_matiere='$matiere';";
													$res_prof_matiere=mysql_query($sql);
													if(mysql_num_rows($res_prof_matiere)>0){
														while($lig_prof_matiere=mysql_fetch_object($res_prof_matiere)){
															$tab_profs_matiere[]=$lig_prof_matiere->id_professeur;
														}
													}

													// On vérifie que les profs de la liste sont bien associés à la matière:
													for($loo=0;$loo<count($tab_profs);$loo++) {
														if(!in_array($tab_profs[$loo], $tab_profs_matiere)) {
															$sql="SELECT MAX(ordre_matieres) AS max_ordre_matiere FROM j_professeurs_matieres WHERE id_professeur='".$tab_profs[$loo]."';";
															//echo "$sql<br />";
															$res_ordre=mysql_query($sql);
															if(mysql_num_rows($res_ordre)==0) {
																$ordre_matiere=1;
															}
															else {
																$ordre_matiere=mysql_result($res_ordre, 0, "max_ordre_matiere")+1;
															}

															$sql="INSERT INTO j_professeurs_matieres SET id_professeur='".$tab_profs[$loo]."', id_matiere='$matiere', ordre_matieres='$ordre_matiere';";
															//echo "$sql<br />";
															$insert=mysql_query($sql);
														}
													}

													$reg_eleves=array();
													$current_group=get_group($create);
													foreach ($current_group["periodes"] as $period) {
														$reg_eleves[$period['num_periode']]=array();
														/*
														if((isset($_POST['eleves_order_by']))&&($_POST['eleves_order_by']=='classe')) {
															$cpt_clas=0;
															$sql="";
															foreach($reg_clazz as $tmp_id_classe){
																if($cpt_clas>0) {$sql.=" UNION ";}
																$sql.="(SELECT jec.login FROM j_eleves_classes jec, eleves e, classes c WHERE id_classe='$tmp_id_classe' AND periode='".$period['num_periode']."' AND jec.login=e.login AND jec.id_classe=c.id ORDER BY e.nom, e.prenom)";
																$cpt_clas++;
															}
															//$sql.=" ORDER BY c.classe, e.nom, e.prenom;";
															//echo "$sql<br />";
															$res_ele=mysql_query($sql);
															$nb_ele=mysql_num_rows($res_ele);
															if($nb_ele>0){
																$cpt_ele=1;
																while($lig_ele=mysql_fetch_object($res_ele)) {
																	if((!isset($_POST['eleves_frac_classe']))||
																		(($_POST['eleves_frac_classe']=='tous'))||
																		(($_POST['eleves_frac_classe']==1)&&($cpt_ele<=ceil($nb_ele/2)))||
																		(($_POST['eleves_frac_classe']==2)&&($cpt_ele>ceil($nb_ele/2)))) {
																		$reg_eleves[$period['num_periode']][]=$lig_ele->login;
																		//echo $lig_ele->login."<br />";
																	}
																	$cpt_ele++;
																}
															}
														}
														else {
															foreach($reg_clazz as $tmp_id_classe){
																$sql="SELECT jec.login FROM j_eleves_classes jec, eleves e, classes c WHERE id_classe='$tmp_id_classe' AND periode='".$period['num_periode']."' AND jec.login=e.login AND jec.id_classe=c.id ORDER BY e.nom, e.prenom;";
																//echo "$sql<br />";
																$res_ele=mysql_query($sql);
																$nb_ele=mysql_num_rows($res_ele);
																if($nb_ele>0){
																	$cpt_ele=1;
																	while($lig_ele=mysql_fetch_object($res_ele)) {
																		if((!isset($_POST['eleves_frac_classe']))||
																			(($_POST['eleves_frac_classe']=='tous'))||
																			(($_POST['eleves_frac_classe']==1)&&($cpt_ele<=ceil($nb_ele/2)))||
																			(($_POST['eleves_frac_classe']==2)&&($cpt_ele>ceil($nb_ele/2)))) {
																			$reg_eleves[$period['num_periode']][]=$lig_ele->login;
																			//echo $lig_ele->login."<br />";
																		}
																		$cpt_ele++;
																	}
																}
															}
														}
														*/
													}

													if ((count($tab_profs) > 0)||(count($reg_eleves) > 0)) {
														$res = update_group($create, $nom_groupe, $description_groupe, $matiere, $reg_clazz, $tab_profs, $reg_eleves);
													}
												}
											}

										}
									}
								}

							}
						}

						echo "<hr />\n";
					}
				}
			}

			echo "";

		}
	}


require("../lib/footer.inc.php");
?>
