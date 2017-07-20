<?php
/*
* $Id$
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

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_notes_admin/copie_tous_dev.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits VALUES ('/cahier_notes_admin/copie_tous_dev.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Création de devoirs copie d une autre classe', '1');";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// INSERT INTO droits VALUES ('/cahier_notes_admin/copie_tous_dev.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Création de devoirs copie d une autre classe', '1');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$id_classe_source=isset($_POST['id_classe_source']) ? $_POST['id_classe_source'] : (isset($_GET['id_classe_source']) ? $_GET['id_classe_source'] : NULL);
$id_classe_dest=isset($_POST['id_classe_dest']) ? $_POST['id_classe_dest'] : (isset($_GET['id_classe_dest']) ? $_GET['id_classe_dest'] : NULL);
$creation_copie=isset($_POST['creation_copie']) ? $_POST['creation_copie'] : NULL;

$id_groupe_src=isset($_POST['id_groupe_src']) ? $_POST['id_groupe_src'] : NULL;
$new_groupe_dest=isset($_POST['new_groupe_dest']) ? $_POST['new_groupe_dest'] : NULL;
$id_groupe_dest=isset($_POST['id_groupe_dest']) ? $_POST['id_groupe_dest'] : NULL;
$cpt_grp=isset($_POST['cpt_grp']) ? $_POST['cpt_grp'] : NULL;

$notes_aleatoires=isset($_POST['notes_aleatoires']) ? $_POST['notes_aleatoires'] : "n";
$copier_cdt=isset($_POST['copier_cdt']) ? $_POST['copier_cdt'] : "n";

//**************** EN-TETE *****************
$titre_page = "Tests : Copie de devoirs";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'>\n";
echo "<a href='index.php'\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if(getSettingAOui('gepi_en_production')) {
	echo "</p>\n";

	echo "<p style='color:red'>Cette page ne doit pas être utilisée sur un Gepi en production.<br />Vous copieriez des devoirs d'une classe vers une autre, avec la possibilité d'insérer des notes aléatoires.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

if(!isset($id_classe_source)) {
	echo "</p>\n";

	echo "<p class='bold'>De quelle classe voulez-vous copier les devoirs&nbsp;?</p>\n";
	$classes_list = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe");
	$nb = mysqli_num_rows($classes_list);
	if ($nb !=0) {
		$nb_class_par_colonne=round($nb/3);
		//echo "<table width='100%' border='1'>\n";
		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='center'>\n";

		$i = '0';

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while ($i < $nb) {
			$t_id_classe = old_mysql_result($classes_list, $i, 'id');
			$t_classe = old_mysql_result($classes_list, $i, 'classe');

			if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}

			echo "<a href='".$_SERVER['PHP_SELF']."?id_classe_source=$t_id_classe'>$t_classe</a><br />\n";
			$i++;
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
}
elseif(!isset($id_classe_dest)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix de la classe source</a></p>\n";
	echo "</p>\n";

	$classe_source=get_class_from_id($id_classe_source);

	echo "<p class='bold'>Vers quelle classe voulez-vous copier les devoirs de <strong>$classe_source</strong>&nbsp;?</p>\n";

	$classes_list = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe");
	$nb = mysqli_num_rows($classes_list);
	if ($nb !=0) {
		$nb_class_par_colonne=round($nb/3);
		//echo "<table width='100%' border='1'>\n";
		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='center'>\n";

		$i = '0';

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while ($i < $nb) {
			$t_id_classe = old_mysql_result($classes_list, $i, 'id');
			if($t_id_classe!=$id_classe_source) {
				$t_classe = old_mysql_result($classes_list, $i, 'classe');

				if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
					echo "</td>\n";
					echo "<td align='left'>\n";
				}

				echo "<a href='".$_SERVER['PHP_SELF']."?id_classe_source=$id_classe_source&amp;id_classe_dest=$t_id_classe'>$t_classe</a><br />\n";
			}
			$i++;
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
}
elseif(!isset($creation_copie)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix de la classe source</a></p>\n";
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe_source=$id_classe_source'>Retour au choix de la classe destination</a></p>\n";
	$classe_source=get_class_from_id($id_classe_source);
	$classe_dest=get_class_from_id($id_classe_dest);

	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";

	echo "<p class='bold'>Copie de devoirs de la classe de <strong>$classe_source</strong> vers la classe de <strong>$classe_dest</strong></p>\n";
	$get_groups_for_class_avec_proflist="y";
	$get_groups_for_class_avec_visibilite="y";
	$groups_src=get_groups_for_class($id_classe_source);
	$groups_dest=get_groups_for_class($id_classe_dest);

	echo "<input type='hidden' name='id_classe_source' value='$id_classe_source' />\n";
	echo "<input type='hidden' name='id_classe_dest' value='$id_classe_dest' />\n";
	echo "<input type='hidden' name='creation_copie' value='y' />\n";

	echo "<table class='boireaus'>\n
	<tr>
		<th>Groupe de $classe_source</th>
		<th>Groupe de $classe_dest</th>
	</tr>\n";
	$alt=1;
	$cpt=0;
	foreach($groups_src as $current_group_src) {
		$alt=$alt*(-1);

		if($current_group_src['visibilite']['cahier_notes']!='n') {
			echo "	<tr class='lig$alt white_hover'>
		<td title=\"Professeur(s) du groupe : ".$current_group_src['proflist_string']."\"><input type='hidden' name='id_groupe_src[$cpt]' value='".$current_group_src['id']."' />".$current_group_src['name']." (<em>".$current_group_src['description']."</em>) en ".$current_group_src['classlist_string']."</td>
		<td>
			<input type='checkbox' name='new_groupe_dest[$cpt]' id='new_groupe_dest_$cpt' value='y' title=\"Ce choix n'est pris en compte que si aucun groupe existant n'est sélectionné pour cette association\" /><label for='new_groupe_dest_$cpt'>Créer un nouvel enseignement</label><br />\n";

			if(count($groups_dest>0)) {
				echo "
			<select name='id_groupe_dest[$cpt]'>
				<option value=''>---</option>";
				foreach($groups_dest as $current_group_dest) {
					if($current_group_dest['visibilite']['cahier_notes']!='n') {
						echo "
				<option value='".$current_group_dest['id']."' title=\"Professeur(s) du groupe : ".$current_group_dest['proflist_string']."\"";
						if($current_group_dest['matiere']['matiere']==$current_group_src['matiere']['matiere']) {echo " style='color:blue'";}
						echo ">".$current_group_dest['name']." (<em>".$current_group_dest['description']."</em>) en ".$current_group_dest['classlist_string']."</option>";
					}
				}
				echo "
			</select>\n";
			}
			echo "
		</td>
	</tr>\n";
			$cpt++;
		}
	}
	echo "
	</table>\n";
	echo "<input type='hidden' name='cpt_grp' value='$cpt' />\n";
	echo "<input type='hidden' name='creation_copie' value='y' />\n";
	echo "<p><input type='checkbox' name='notes_aleatoires' id='notes_aleatoires' value='y' /><label for='notes_aleatoires'> Enregistrer des notes aléatoires</label></p>\n";
	if(acces_cdt()) {
		echo "<p><input type='checkbox' name='copier_cdt' id='copier_cdt' value='y' /><label for='copier_cdt'> Copier aussi les Cahiers de textes</label></p>\n";
	}
	echo "<p><input type='submit' value='Créer/copier' /></p>\n";
	echo "</form>\n";

}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix de la classe source</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe_source=$id_classe_source'>Retour au choix de la classe destination</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe_source=$id_classe_source&amp;id_classe_dest=$id_classe_dest'>Choix des enseignements</a></p>\n";

	$classe_source=get_class_from_id($id_classe_source);
	$classe_dest=get_class_from_id($id_classe_dest);

	echo "<p class='bold'>Copie de devoirs de la classe de <strong>$classe_source</strong> vers la classe de <strong>$classe_dest</strong></p>\n";
	echo "<br />\n";
	/*
	$get_groups_for_class_avec_proflist="y";
	$groups_src=get_groups_for_class($id_classe_source);
	$groups_dest=get_groups_for_class($id_classe_dest);
	*/

	$sql="DELETE FROM matieres_appreciations_acces WHERE id_classe='$id_classe_dest';";
	$menage=mysqli_query($GLOBALS["mysqli"], $sql);
	$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe_dest' ORDER BY num_periode;";
	$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig_per=mysqli_fetch_object($res_per)) {
		$sql="INSERT INTO matieres_appreciations_acces SET id_classe='$id_classe_dest', periode='$lig_per->num_periode', statut='responsable', date='0000-00-00', acces='y';";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		$sql="INSERT INTO matieres_appreciations_acces SET id_classe='$id_classe_dest', periode='$lig_per->num_periode', statut='eleve', date='0000-00-00', acces='y';";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	}

	for($i=0;$i<$cpt_grp;$i++) {
		if((isset($id_groupe_dest[$i]))&&($id_groupe_dest[$i]!='')) {
			$current_group_src=get_group($id_groupe_src[$i]);
			echo "<br/><p class='bold'>Groupe source&nbsp;: ".$current_group_src['name']." (<em>".$current_group_src['description']."</em>) en ".$current_group_src['classlist_string']."<br />\n";

			if($id_groupe_dest[$i]==$id_groupe_src[$i]) {
				echo "Le groupe destination est le même.<br />On ne fait rien.</p>\n";
			}
			else {
				echo "<blockquote>\n";
				$current_group_dest=get_group($id_groupe_dest[$i]);
				$sql="SELECT * FROM cn_cahier_notes WHERE id_groupe='".$id_groupe_src[$i]."' ORDER BY periode;";
				$res_ccn_src=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_ccn_src)>0) {
					while($lig_ccn_src=mysqli_fetch_object($res_ccn_src)) {
						unset($id_cahier_notes_dest);

						echo "<p><strong>Période $lig_ccn_src->periode</strong></p>\n";
						echo "<blockquote>\n";
						$id_cahier_notes_src=$lig_ccn_src->id_cahier_notes;

						$sql="SELECT * FROM cn_cahier_notes WHERE id_groupe='".$id_groupe_dest[$i]."' AND periode='$lig_ccn_src->periode';";
						$res_ccn_dest=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_ccn_dest)>0) {
							$lig_ccn_dest=mysqli_fetch_object($res_ccn_dest);
							$id_cahier_notes_dest=$lig_ccn_dest->id_cahier_notes;
						}
						else {
							// Création d'un cahier de notes 
							$sql="INSERT INTO cn_conteneurs SET id_racine='', nom_court='".mysqli_real_escape_string($GLOBALS["mysqli"], $current_group_dest["description"])."', nom_complet='". mysqli_real_escape_string($GLOBALS["mysqli"], $current_group_dest["matiere"]["nom_complet"])."', description = '', mode = '2', coef = '1.0', arrondir = 's1', ponderation = '0.0', display_parents = '0', display_bulletin = '1', parent = '0'";
							$creation_cnc=mysqli_query($GLOBALS["mysqli"], $sql);
							if($creation_cnc) {
								$id_cahier_notes_dest=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

								$sql="UPDATE cn_conteneurs SET id_racine='$id_cahier_notes_dest', parent = '0' WHERE id='$id_cahier_notes_dest';";
								$update_cn = mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$update_cn) {
									echo "<span style='color:red'>Erreur en créant le cahier de notes destination en période $lig_ccn_src->periode.</span><br />\n";
									unset($id_cahier_notes_dest);
								}
								else {
									$sql="INSERT INTO cn_cahier_notes SET id_cahier_notes='$id_cahier_notes_dest', id_groupe='$id_groupe_dest[$i]', periode='$lig_ccn_src->periode';";
									$creation_ccn=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$creation_ccn) {
										echo "<span style='color:red'>Erreur en créant le cahier de notes destination en période $lig_ccn_src->periode.</span><br />\n";
										unset($id_cahier_notes_dest);
									}
								}
							}
							else {
								echo "<span style='color:red'>Erreur en créant le cahier de notes destination en période $lig_ccn_src->periode.</span><br />\n";
							}
						}

						if(isset($id_cahier_notes_dest)) {
							$sql="SELECT * FROM cn_devoirs WHERE id_racine='$id_cahier_notes_src';";
							$res_dev_src=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_dev_src)>0) {
								while($lig_dev_src=mysqli_fetch_object($res_dev_src)) {
									echo "Création de $lig_dev_src->nom_court ($lig_dev_src->date)&nbsp;: ";
									$sql="INSERT INTO cn_devoirs SET id_racine='$id_cahier_notes_dest', 
										id_conteneur='$id_cahier_notes_dest', 
										nom_court='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_dev_src->nom_court)."', 
										nom_complet='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_dev_src->nom_complet)."', 
										description='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_dev_src->description)."', 
										facultatif='".$lig_dev_src->facultatif."', 
										date='".$lig_dev_src->date."', 
										coef='".$lig_dev_src->coef."', 
										note_sur='".$lig_dev_src->note_sur."', 
										ramener_sur_referentiel='".$lig_dev_src->ramener_sur_referentiel."', 
										display_parents='".$lig_dev_src->display_parents."', 
										display_parents_app='".$lig_dev_src->display_parents_app."', 
										date_ele_resp='".$lig_dev_src->date_ele_resp."';";
									//echo "$sql<br />\n";
									$creation_dev=mysqli_query($GLOBALS["mysqli"], $sql);
									if($creation_dev) {
										echo "<span style='color:green;'>SUCCES</span>";
										$id_devoir_dest=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

										if(isset($notes_aleatoires)) {
											$nb_notes=0;
											foreach($current_group_dest['eleves'][$lig_ccn_src->periode]['list'] as $login_ele) {
												$note=rand(0, $lig_dev_src->note_sur+1);
												if($note>$lig_dev_src->note_sur) {
													$sql="INSERT INTO cn_notes_devoirs SET id_devoir='$id_devoir_dest', login='$login_ele', note='0.0', statut='abs';";
												}
												else {
													$sql="INSERT INTO cn_notes_devoirs SET id_devoir='$id_devoir_dest', login='$login_ele', note='$note.0';";
												}
												$insert_note=mysqli_query($GLOBALS["mysqli"], $sql);
												if($insert_note) {
													$nb_notes++;
												}
											}
											echo " $nb_notes note(s) générée(s)";
										}

									}
									else {
										echo "<span style='color:red;'>ECHEC</span>";
									}
									echo "<br />";
								}
								echo "Mise à jour des moyennes de conteneurs.<br />";
								$arret = 'no';
								mise_a_jour_moyennes_conteneurs($current_group_dest, $lig_ccn_src->periode,$id_cahier_notes_dest,$id_cahier_notes_dest,$arret);

								foreach($current_group_dest['eleves'][$lig_ccn_src->periode]['list'] as $login_ele) {
									$sql="SELECT * FROM cn_notes_conteneurs WHERE login='$login_ele' AND id_conteneur='$id_cahier_notes_dest' AND statut='y'";
									$res_moy_carnet=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_moy_carnet)==0){
										$moy_carnet="-";
									}
									else{
										$lig_moy_carnet=mysqli_fetch_object($res_moy_carnet);
										$moy_carnet=$lig_moy_carnet->note;

										$sql="DELETE FROM matieres_notes WHERE id_groupe='".$id_groupe_dest[$i]."' AND login='$login_ele' AND periode='$lig_ccn_src->periode';";
										$menage=mysqli_query($GLOBALS["mysqli"], $sql);

										$sql="INSERT INTO matieres_notes SET id_groupe='".$id_groupe_dest[$i]."', login='$login_ele', periode='$lig_ccn_src->periode', note='$moy_carnet';";
										//echo "$sql<br />";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);

										if($moy_carnet>=15) {
											$sql="DELETE FROM matieres_appreciations WHERE id_groupe='".$id_groupe_dest[$i]."' AND login='$login_ele' AND periode='$lig_ccn_src->periode';";
											$menage=mysqli_query($GLOBALS["mysqli"], $sql);

											$sql="INSERT INTO matieres_appreciations SET id_groupe='".$id_groupe_dest[$i]."', login='$login_ele', periode='$lig_ccn_src->periode', appreciation='Bon travail. Continuez.';";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										}
										elseif($moy_carnet>=12) {
											$sql="DELETE FROM matieres_appreciations WHERE id_groupe='".$id_groupe_dest[$i]."' AND login='$login_ele' AND periode='$lig_ccn_src->periode';";
											$menage=mysqli_query($GLOBALS["mysqli"], $sql);

											$sql="INSERT INTO matieres_appreciations SET id_groupe='".$id_groupe_dest[$i]."', login='$login_ele', periode='$lig_ccn_src->periode', appreciation='Ensemble correct. Vous pouvez mieux faire en vous investissant davantage.';";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										}
										elseif($moy_carnet>=9) {
											$sql="DELETE FROM matieres_appreciations WHERE id_groupe='".$id_groupe_dest[$i]."' AND login='$login_ele' AND periode='$lig_ccn_src->periode';";
											$menage=mysqli_query($GLOBALS["mysqli"], $sql);

											$sql="INSERT INTO matieres_appreciations SET id_groupe='".$id_groupe_dest[$i]."', login='$login_ele', periode='$lig_ccn_src->periode', appreciation='C\'est trop moyen. Vous ne faites pas le maximum.';";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										}
										elseif($moy_carnet>=6) {
											$sql="DELETE FROM matieres_appreciations WHERE id_groupe='".$id_groupe_dest[$i]."' AND login='$login_ele' AND periode='$lig_ccn_src->periode';";
											$menage=mysqli_query($GLOBALS["mysqli"], $sql);

											$sql="INSERT INTO matieres_appreciations SET id_groupe='".$id_groupe_dest[$i]."', login='$login_ele', periode='$lig_ccn_src->periode', appreciation='Il faut se mettre au travail.';";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										}
										else {
											$sql="DELETE FROM matieres_appreciations WHERE id_groupe='".$id_groupe_dest[$i]."' AND login='$login_ele' AND periode='$lig_ccn_src->periode';";
											$menage=mysqli_query($GLOBALS["mysqli"], $sql);

											$sql="INSERT INTO matieres_appreciations SET id_groupe='".$id_groupe_dest[$i]."', login='$login_ele', periode='$lig_ccn_src->periode', appreciation='Ensemble faible. La bonne volonté est-elle au rendez-vous?';";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										}

									}
								}
							}
						}
						echo "</blockquote>\n";
					}
				}

				if($copier_cdt=="y") {
					$cpt_cte=0;
					$cpt_ctd=0;
					$sql="SELECT * FROM ct_entry WHERE id_groupe='".$id_groupe_src[$i]."' ORDER BY date_ct;";
					$res_cte=mysqli_query($GLOBALS["mysqli"], $sql);
					while($lig_cte=mysqli_fetch_object($res_cte)) {
						$sql="INSERT INTO ct_entry SET id_groupe='".$id_groupe_dest[$i]."', heure_entry='".$lig_cte->heure_entry."', date_ct='".$lig_cte->date_ct."', id_login='".$lig_cte->id_login."', contenu='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_cte->contenu)."', vise='".$lig_cte->vise."', id_sequence='".$lig_cte->id_sequence."', visa='".$lig_cte->visa."';";
						//echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if($insert) {
							$id_ct_cte=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
							$sql="SELECT * FROM ct_documents WHERE id_ct='$id_ct_cte';";
							//echo "$sql<br />";
							$res_ctd=mysqli_query($GLOBALS["mysqli"], $sql);
							while($lig_ctd=mysqli_fetch_object($res_ctd)) {
								// ATTENTION: On pointe vers le même fichier... donc attention en cas de suppression, c'est pour les deux.
								$sql="INSERT INTO ct_documents SET id_ct='$id_ct_cte', titre='".$lig_ctd->titre."', taille='".$lig_ctd->taille."', emplacement='".$lig_ctd->emplacement."', visible_eleve_parent='".$lig_ctd->visible_eleve_parent."';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {
									$cpt_ctd++;
								}
							}
							$cpt_cte++;
						}
					}

					$cpt_ctde=0;
					$cpt_ctdd=0;
					$sql="SELECT * FROM ct_devoirs_entry WHERE id_groupe='".$id_groupe_src[$i]."' ORDER BY date_ct;";
					$res_cte=mysqli_query($GLOBALS["mysqli"], $sql);
					while($lig_cte=mysqli_fetch_object($res_cte)) {
						$sql="INSERT INTO ct_devoirs_entry SET id_groupe='".$id_groupe_dest[$i]."',date_ct='".$lig_cte->date_ct."', id_login='".$lig_cte->id_login."', contenu='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_cte->contenu)."', vise='".$lig_cte->vise."', id_sequence='".$lig_cte->id_sequence."', date_visibilite_eleve='".$lig_cte->date_visibilite_eleve."';";
						//echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if($insert) {
							$id_ct_cte=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
							$sql="SELECT * FROM ct_devoirs_documents WHERE id_ct_devoir='$id_ct_cte';";
							//echo "$sql<br />";
							$res_ctd=mysqli_query($GLOBALS["mysqli"], $sql);
							while($lig_ctd=mysqli_fetch_object($res_ctd)) {
								// ATTENTION: On pointe vers le même fichier... donc attention en cas de suppression, c'est pour les deux.
								$sql="INSERT INTO ct_devoirs_documents SET id_ct='$id_ct_cte', titre='".$lig_ctd->titre."', taille='".$lig_ctd->taille."', emplacement='".$lig_ctd->emplacement."', visible_eleve_parent='".$lig_ctd->visible_eleve_parent."';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {
									$cpt_ctdd++;
								}
							}
							$cpt_ctde++;
						}
					}

					echo "<p><span class='bold'>CDT</span><br />";
					echo "$cpt_cte notice(s) de comptes-rendus copiée(s).<br />";
					echo "$cpt_ctd document(s) joint(s) à des notices de comptes-rendus copiée(s).<br />";
					echo "$cpt_ctde notice(s) de devoirs copiée(s).<br />";
					echo "$cpt_ctdd document(s) joint(s) à des notices de devoirs copiée(s).<br />";
				}

				echo "</blockquote>\n";
			}
		}
		elseif(isset($new_groupe_dest[$i])) {
			$current_group_src=get_group($id_groupe_src[$i]);
			echo "<p><span class='bold'>Groupe source&nbsp;: ".$current_group_src['name']." (<em>".$current_group_src['description']."</em>) en ".$current_group_src['classlist_string']."</span><br />\n";

			echo "Création d'un nouveau groupe dans la classe destination...<br />";
			$create = create_group($current_group_src['matiere']['matiere'], $current_group_src['matiere']['nom_complet'], $current_group_src['matiere']['matiere'], array($id_classe_dest));
			if (!$create) {
				//echo "<!-- erreur -->\n";
				echo "<span style='color:red'>Erreur lors de la création du groupe ".$current_group_src['matiere']['matiere']."</span><br />";
			}
			else {
				$id_groupe_dest[$i]=$create;

				$sql="SELECT * FROM periodes WHERE id_classe='$id_classe_dest'";
				$result_list_periodes=mysqli_query($GLOBALS["mysqli"], $sql);
				while($ligne_periode=mysqli_fetch_object($result_list_periodes)){
					$reg_eleves[$ligne_periode->num_periode]=array();
					//$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY periode,login";
					$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe_dest' AND periode='$ligne_periode->num_periode' ORDER BY periode,login";
					$result_list_eleves=mysqli_query($GLOBALS["mysqli"], $sql);
					while($ligne_eleve=mysqli_fetch_object($result_list_eleves)){
						$reg_eleves[$ligne_periode->num_periode][]=$ligne_eleve->login;
					}
				}

				$reg_professeurs=array();

				$tmp_tab_prof=get_profs_for_matiere($current_group_src['matiere']['matiere']);
				if(isset($tmp_tab_prof[0]['login'])) {
					$reg_professeurs=array($tmp_tab_prof[0]['login']);
				}

				$code_modalite_elect_eleves=$current_group_src["modalites"];

				$create = update_group($id_groupe_dest[$i], $current_group_src['matiere']['matiere'], $current_group_src['matiere']['nom_complet'], $current_group_src['matiere']['matiere'], array($id_classe_dest), $reg_professeurs, $reg_eleves, $code_modalite_elect_eleves);
				if (!$create) {
					echo "<span style='color:red'>Erreur lors de la mise à jour du groupe ".$current_group_src['matiere']['matiere']."</span><br />";
				}
				else {
					echo "<blockquote>\n";
					$current_group_dest=get_group($id_groupe_dest[$i]);

					$sql="SELECT * FROM cn_cahier_notes WHERE id_groupe='".$id_groupe_src[$i]."' ORDER BY periode;";
					$res_ccn_src=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_ccn_src)>0) {
						while($lig_ccn_src=mysqli_fetch_object($res_ccn_src)) {
							unset($id_cahier_notes_dest);

							echo "<p><strong>Période $lig_ccn_src->periode</strong></p>\n";
							echo "<blockquote>\n";

							$id_cahier_notes_src=$lig_ccn_src->id_cahier_notes;

							$sql="SELECT * FROM cn_cahier_notes WHERE id_groupe='".$id_groupe_dest[$i]."' AND periode='$lig_ccn_src->periode';";
							$res_ccn_dest=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_ccn_dest)>0) {
								$lig_ccn_dest=mysqli_fetch_object($res_ccn_dest);
								$id_cahier_notes_dest=$lig_ccn_dest->id_cahier_notes;
							}
							else {
								// Création d'un cahier de notes
								$sql="INSERT INTO cn_conteneurs SET id_racine='', nom_court='".mysqli_real_escape_string($GLOBALS["mysqli"], $current_group_dest["description"])."', nom_complet='". mysqli_real_escape_string($GLOBALS["mysqli"], $current_group_dest["matiere"]["nom_complet"])."', description = '', mode = '2', coef = '1.0', arrondir = 's1', ponderation = '0.0', display_parents = '0', display_bulletin = '1', parent = '0'";
								$creation_cnc=mysqli_query($GLOBALS["mysqli"], $sql);
								if($creation_cnc) {
									$id_cahier_notes_dest=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

									$sql="INSERT INTO cn_cahier_notes SET id_cahier_notes='$id_cahier_notes_dest', id_groupe='$id_groupe_dest[$i]', periode='$lig_ccn_src->periode';";
									$creation_ccn=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$creation_ccn) {
										echo "<span style='color:red'>Erreur en créant le cahier de notes destination en période $lig_ccn_src->periode.</span><br />\n";
										unset($id_cahier_notes_dest);
									}
								}
								else {
									echo "<span style='color:red'>Erreur en créant le cahier de notes destination en période $lig_ccn_src->periode.</span><br />\n";
								}
							}

							if(isset($id_cahier_notes_dest)) {
								$sql="SELECT * FROM cn_devoirs WHERE id_racine='$id_cahier_notes_src';";
								$res_dev_src=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_dev_src)>0) {
									while($lig_dev_src=mysqli_fetch_object($res_dev_src)) {
										echo "Création de $lig_dev_src->nom_court ($lig_dev_src->date)&nbsp;: ";
										$sql="INSERT INTO cn_devoirs SET id_racine='$id_cahier_notes_dest', 
											id_conteneur='$id_cahier_notes_dest', 
											nom_court='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_dev_src->nom_court)."', 
											nom_complet='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_dev_src->nom_complet)."', 
											description='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_dev_src->description)."', 
											facultatif='".$lig_dev_src->facultatif."', 
											date='".$lig_dev_src->date."', 
											coef='".$lig_dev_src->coef."', 
											note_sur='".$lig_dev_src->note_sur."', 
											ramener_sur_referentiel='".$lig_dev_src->ramener_sur_referentiel."', 
											display_parents='".$lig_dev_src->display_parents."', 
											display_parents_app='".$lig_dev_src->display_parents_app."', 
											date_ele_resp='".$lig_dev_src->date_ele_resp."';";
										//echo "$sql<br />\n";
										$creation_dev=mysqli_query($GLOBALS["mysqli"], $sql);
										if($creation_dev) {
											echo "<span style='color:green;'>SUCCES</span>";
											$id_devoir_dest=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

											if(isset($notes_aleatoires)) {
												$nb_notes=0;
												foreach($reg_eleves[$lig_ccn_src->periode] as $login_ele) {
													$note=rand(0, $lig_dev_src->note_sur+1);
													if($note>$lig_dev_src->note_sur) {
														$sql="INSERT INTO cn_notes_devoirs SET id_devoir='$id_devoir_dest', login='$login_ele', note='0.0', statut='abs';";
													}
													else {
														$sql="INSERT INTO cn_notes_devoirs SET id_devoir='$id_devoir_dest', login='$login_ele', note='$note.0';";
													}
													$insert_note=mysqli_query($GLOBALS["mysqli"], $sql);
													if($insert_note) {
														$nb_notes++;
													}
												}
												echo " $nb_notes note(s) générée(s)";
											}
										}
										else {
											echo "<span style='color:red;'>ECHEC</span>";
										}
										echo "<br />";
									}
									echo "Mise à jour des moyennes de conteneurs.<br />";
									$arret = 'no';
									mise_a_jour_moyennes_conteneurs($current_group_dest, $lig_ccn_src->periode,$id_cahier_notes_dest,$id_cahier_notes_dest,$arret);

									foreach($reg_eleves[$lig_ccn_src->periode] as $login_ele) {
										$sql="SELECT * FROM cn_notes_conteneurs WHERE login='$login_ele' AND id_conteneur='$id_cahier_notes_dest' AND statut='y'";
										$res_moy_carnet=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_moy_carnet)==0){
											$moy_carnet="-";
										}
										else{
											$lig_moy_carnet=mysqli_fetch_object($res_moy_carnet);
											$moy_carnet=$lig_moy_carnet->note;

											$sql="DELETE FROM matieres_notes WHERE id_groupe='".$id_groupe_dest[$i]."' AND login='$login_ele' AND periode='$lig_ccn_src->periode';";
											$menage=mysqli_query($GLOBALS["mysqli"], $sql);

											$sql="INSERT INTO matieres_notes SET id_groupe='".$id_groupe_dest[$i]."', login='$login_ele', periode='$lig_ccn_src->periode', note='$moy_carnet';";
											//echo "$sql<br />";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);

											if($moy_carnet>=15) {
												$sql="DELETE FROM matieres_appreciations WHERE id_groupe='".$id_groupe_dest[$i]."' AND login='$login_ele' AND periode='$lig_ccn_src->periode';";
												$menage=mysqli_query($GLOBALS["mysqli"], $sql);

												$sql="INSERT INTO matieres_appreciations SET id_groupe='".$id_groupe_dest[$i]."', login='$login_ele', periode='$lig_ccn_src->periode', appreciation='Bon travail. Continuez.';";
												//echo "$sql<br />";
												$insert=mysqli_query($GLOBALS["mysqli"], $sql);
											}
											elseif($moy_carnet>=12) {
												$sql="DELETE FROM matieres_appreciations WHERE id_groupe='".$id_groupe_dest[$i]."' AND login='$login_ele' AND periode='$lig_ccn_src->periode';";
												$menage=mysqli_query($GLOBALS["mysqli"], $sql);

												$sql="INSERT INTO matieres_appreciations SET id_groupe='".$id_groupe_dest[$i]."', login='$login_ele', periode='$lig_ccn_src->periode', appreciation='Ensemble correct. Vous pouvez mieux faire en vous investissant davantage.';";
												//echo "$sql<br />";
												$insert=mysqli_query($GLOBALS["mysqli"], $sql);
											}
											elseif($moy_carnet>=9) {
												$sql="DELETE FROM matieres_appreciations WHERE id_groupe='".$id_groupe_dest[$i]."' AND login='$login_ele' AND periode='$lig_ccn_src->periode';";
												$menage=mysqli_query($GLOBALS["mysqli"], $sql);

												$sql="INSERT INTO matieres_appreciations SET id_groupe='".$id_groupe_dest[$i]."', login='$login_ele', periode='$lig_ccn_src->periode', appreciation='C\'est trop moyen. Vous ne faites pas le maximum.';";
												//echo "$sql<br />";
												$insert=mysqli_query($GLOBALS["mysqli"], $sql);
											}
											elseif($moy_carnet>=6) {
												$sql="DELETE FROM matieres_appreciations WHERE id_groupe='".$id_groupe_dest[$i]."' AND login='$login_ele' AND periode='$lig_ccn_src->periode';";
												$menage=mysqli_query($GLOBALS["mysqli"], $sql);

												$sql="INSERT INTO matieres_appreciations SET id_groupe='".$id_groupe_dest[$i]."', login='$login_ele', periode='$lig_ccn_src->periode', appreciation='Il faut se mettre au travail.';";
												//echo "$sql<br />";
												$insert=mysqli_query($GLOBALS["mysqli"], $sql);
											}
											elseif($moy_carnet>=0) {
												$sql="DELETE FROM matieres_appreciations WHERE id_groupe='".$id_groupe_dest[$i]."' AND login='$login_ele' AND periode='$lig_ccn_src->periode';";
												$menage=mysqli_query($GLOBALS["mysqli"], $sql);

												$sql="INSERT INTO matieres_appreciations SET id_groupe='".$id_groupe_dest[$i]."', login='$login_ele', periode='$lig_ccn_src->periode', appreciation='Ensemble faible. La bonne volonté est-elle au rendez-vous?';";
												//echo "$sql<br />";
												$insert=mysqli_query($GLOBALS["mysqli"], $sql);
											}

										}
									}
								}
							}
							echo "</blockquote>\n";
						}
					}

					if($copier_cdt=="y") {
						$cpt_cte=0;
						$cpt_ctd=0;
						$sql="SELECT * FROM ct_entry WHERE id_groupe='".$id_groupe_src[$i]."' ORDER BY date_ct;";
						$res_cte=mysqli_query($GLOBALS["mysqli"], $sql);
						while($lig_cte=mysqli_fetch_object($res_cte)) {
							$sql="INSERT INTO ct_entry SET id_groupe='".$id_groupe_dest[$i]."', heure_entry='".$lig_cte->heure_entry."', date_ct='".$lig_cte->date_ct."', id_login='".$lig_cte->id_login."', contenu='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_cte->contenu)."', vise='".$lig_cte->vise."', id_sequence='".$lig_cte->id_sequence."', visa='".$lig_cte->visa."';";
							//echo "$sql<br />";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if($insert) {
								$id_ct_cte=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
								$sql="SELECT * FROM ct_documents WHERE id_ct='$id_ct_cte';";
								//echo "$sql<br />";
								$res_ctd=mysqli_query($GLOBALS["mysqli"], $sql);
								while($lig_ctd=mysqli_fetch_object($res_ctd)) {
									// ATTENTION: On pointe vers le même fichier... donc attention en cas de suppression, c'est pour les deux.
									$sql="INSERT INTO ct_documents SET id_ct='$id_ct_cte', titre='".$lig_ctd->titre."', taille='".$lig_ctd->taille."', emplacement='".$lig_ctd->emplacement."', visible_eleve_parent='".$lig_ctd->visible_eleve_parent."';";
									//echo "$sql<br />";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if($insert) {
										$cpt_ctd++;
									}
								}
								$cpt_cte++;
							}
						}

						$cpt_ctde=0;
						$cpt_ctdd=0;
						$sql="SELECT * FROM ct_devoirs_entry WHERE id_groupe='".$id_groupe_src[$i]."' ORDER BY date_ct;";
						$res_cte=mysqli_query($GLOBALS["mysqli"], $sql);
						while($lig_cte=mysqli_fetch_object($res_cte)) {
							$sql="INSERT INTO ct_devoirs_entry SET id_groupe='".$id_groupe_dest[$i]."',date_ct='".$lig_cte->date_ct."', id_login='".$lig_cte->id_login."', contenu='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_cte->contenu)."', vise='".$lig_cte->vise."', id_sequence='".$lig_cte->id_sequence."', date_visibilite_eleve='".$lig_cte->date_visibilite_eleve."';";
							//echo "$sql<br />";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if($insert) {
								$id_ct_cte=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
								$sql="SELECT * FROM ct_devoirs_documents WHERE id_ct_devoir='$id_ct_cte';";
								//echo "$sql<br />";
								$res_ctd=mysqli_query($GLOBALS["mysqli"], $sql);
								while($lig_ctd=mysqli_fetch_object($res_ctd)) {
									// ATTENTION: On pointe vers le même fichier... donc attention en cas de suppression, c'est pour les deux.
									$sql="INSERT INTO ct_devoirs_documents SET id_ct='$id_ct_cte', titre='".$lig_ctd->titre."', taille='".$lig_ctd->taille."', emplacement='".$lig_ctd->emplacement."', visible_eleve_parent='".$lig_ctd->visible_eleve_parent."';";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if($insert) {
										$cpt_ctdd++;
									}
								}
								$cpt_ctde++;
							}
						}

						echo "<p><span class='bold'>CDT</span><br />";
						echo "$cpt_cte notice(s) de comptes-rendus copiée(s).<br />";
						echo "$cpt_ctd document(s) joint(s) à des notices de comptes-rendus copiée(s).<br />";
						echo "$cpt_ctde notice(s) de devoirs copiée(s).<br />";
						echo "$cpt_ctdd document(s) joint(s) à des notices de devoirs copiée(s).<br />";

					}

					echo "</blockquote>\n";
				}
			}
		}
	}


}

echo "<br />
<p style='text-indent:-5em;margin-left:5em;'><i>NOTES&nbsp;:</i> Page destinée à mettre en place rapidement des groupes et devoirs dans une classe de test.<br />
Cette page a été réalisée pour insérer rapidement des données afin de présenter Gepi à des parents d'élèves lors d'une journée portes ouvertes.</p>\n";
require("../lib/footer.inc.php");
?>
