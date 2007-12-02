<?php
//@set_time_limit(0);
/*
* $Id
*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
extract($_POST, EXTR_OVERWRITE);


// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
die();
} else if ($resultat_session == '0') {
header("Location: ../logout.php?auto=1");
die();
};


// INSERT INTO `droits` VALUES ('/cahier_notes/import_cahier_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'Import CSV du cahier de notes', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
    die("Le module n'est pas activé.");
}



unset($id_racine);
$id_racine=isset($_POST["id_racine"]) ? $_POST["id_racine"] : (isset($_GET["id_racine"]) ? $_GET["id_racine"] : NULL);

// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
    header("Location: index.php?msg=$mess");
    die();
}

$appel_cahier_notes = mysql_query("SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe = mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group = get_group($id_groupe);
$id_classe = $current_group["classes"]["list"][0];
$periode_num = mysql_result($appel_cahier_notes, 0, 'periode');

if (count($current_group["classes"]["list"]) > 1) {
    $multiclasses = true;
} else {
    $multiclasses = false;
    $order_by = "nom";
}


include "../lib/periodes.inc.php";

// On teste si la periode est vérouillée !
if (($current_group["classe"]["ver_periode"]["all"][$periode_num] <= 1) and (isset($id_devoir)) and ($id_devoir!='') ) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes dont la période est bloquée !");
    header("Location: index.php?msg=$mess");
    die();
}


$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];


$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
$nom_periode = mysql_result($periode_query, $periode_num-1, "nom_periode");


$instant=getdate();
$heure=sprintf("%02d",$instant['hours']);
$minute=sprintf("%02d",$instant['minutes']);
$seconde=sprintf("%02d",$instant['seconds']);
$mois=sprintf("%02d",$instant['mon']);
$jour=sprintf("%02d",$instant['mday']);
$annee=$instant['year'];



function recherche_enfant($id_parent_tmp){
	global $current_group, $periode_num, $id_racine;
	$sql="SELECT * FROM cn_conteneurs WHERE parent='$id_parent_tmp'";
	//echo "<!-- $sql -->\n";
	$res_enfant=mysql_query($sql);
	if(mysql_num_rows($res_enfant)>0){
		while($lig_conteneur_enfant=mysql_fetch_object($res_enfant)){
			recherche_enfant($lig_conteneur_enfant->id);
		}
	}
	else{
		$arret = 'no';
		$id_conteneur_enfant=$id_parent_tmp;
		mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur_enfant,$arret);
	}
}



//**************** EN-TETE *****************
$titre_page = "Import de devoirs dans le cahier de notes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="index.php?id_racine=<?php echo $id_racine;?>"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a></p>
<?php

$titre=htmlentities($current_group['description'])." (".$nom_periode.")";
$titre .= " - IMPORT";

echo "<center><h3 class='gepi'>Import de devoirs dans le cahier de notes</h3></center>\n";

if (!isset($is_posted)) {
	echo "<p>Pour importer des devoirs dans le carnet de notes, vous devez fournir un fichier correctement formaté...</p>";
	echo "<p>Veuillez préciser le nom complet du fichier <b>CSV</b> à importer.";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method=post>\n";
	echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo "<input type=\"hidden\" name=\"id_racine\" value=\"$id_racine\" />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";

	/*
	// Ajouter une case à cocher pour ajouter ou remplacer les devoirs de même nom existants... A FAIRE
	echo "<p><input type=\"radio\" name=\"mode\" value=\"ajouter\" />Ajouter le contenu du CSV comme de nouveaux devoirs,<br />\n";
	echo "ou <input type=\"radio\" name=\"mode\" value=\"remplacer\" /> remplacer les notes pour les devoirs de même nom.</p>\n";
	*/

	echo "<p><input type=submit value='Valider' /></p>\n";
	echo "</form>\n";
}
else{
	if(!isset($_POST['valide_insertion_devoirs'])) {
		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

		if (trim($csv_file['name'])=='') {
			echo "<p>Aucun fichier n'a été sélectionné !<br />\n";
			echo "<a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine'>Cliquer ici</a> pour recommencer !</center></p>";
		}
		else{

			//$fp = dbase_open($csv_file['tmp_name'], 0);
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp){
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
				echo "<p><a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine'>Cliquer ici</a> pour recommencer !</center></p>\n";
			}
			else{

				// on constitue le tableau des champs à extraire
				$tabchamps=array("GEPI_INFOS","GEPI_LOGIN_ELEVE","GEPI_COL_1ER_DEVOIR");

				$ligne=fgets($fp, 4096);
				$temp=explode(";",$ligne);
				for($i=0;$i<sizeof($temp);$i++){
					$en_tete[$i]=ereg_replace('"','',$temp[$i]);
				}
				$nbchamps=sizeof($en_tete);
				fclose($fp);

				// On range dans tabindice les indices des champs retenus
				$temoin=0;
				for($k=0;$k<count($tabchamps);$k++){
					for($i=0;$i<count($en_tete);$i++){
						if(trim($en_tete[$i])==$tabchamps[$k]){
							$tabindice[$k]=$i;
							//echo "\$tabindice[$k]=$tabindice[$k]<br />";
							$temoin++;
						}
					}
				}

				if($temoin!=count($tabchamps)){
					echo "<p><b>ERREUR:</b> La ligne d'entête du fichier n'est pas conforme à ce qui est attendu.</p>\n";
					echo "<p><a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine'>Cliquer ici</a> pour recommencer !</center></p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				$fp=fopen($csv_file['tmp_name'],"r");
				// On lit une ligne pour passer la ligne d'entête:
				$ligne = fgets($fp, 4096);
				//=========================
				unset($tab_dev);
				$tab_dev=array();
				$cpt_ele=0;
				$info_erreur="";

				while(!feof($fp)){
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!=""){
						$ligne=trim($ligne);
						//echo "<p>ligne=$ligne<br />\n";
						$tabligne=explode(";",ereg_replace('"','',$ligne));

						switch($tabligne[$tabindice[0]]){
							case "GEPI_DEV_NOM_COURT":
								unset($nomc_dev);
								$nomc_dev=array();
								for($i=$tabindice[2];$i<sizeof($tabligne);$i++){
									// Contrôler qu'il n'y a pas de caractères invalides...
									//corriger_caracteres()

									// On ne compte pas les champs avec un nom de devoir vide
									// Si: il faut que les nomc_dev, coef_dev et date_dev aient le même nombre de colonnes...
									// ... le test est fait plus loin pour ne pas créer de devoir avec un nom vide.
									//if(trim($tabligne[$i])!=""){
										$nomc_dev[]=ereg_replace("[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü_. - ]","",corriger_caracteres($tabligne[$i]));
									//}
									/*
									if($mode=="remplacer"){
										$sql="SELECT id FROM cn_devoirs WHERE (nom_court='".."' AND id_racine='$id_racine')";
									}
									*/
								}
								break;
							case "GEPI_DEV_COEF":
								unset($coef_dev);
								$coef_dev=array();
								for($i=$tabindice[2];$i<sizeof($tabligne);$i++){
									// Reformater le coef...
									if(ereg("^[0-9\.\,]{1,}$",$tabligne[$i])){
										$coef_dev[]=strtr($tabligne[$i],",",".");
									}
									else{
										$coef_dev[]="1.0";
									}
								}
								break;
							case "GEPI_DEV_DATE":
								unset($date_dev);
								$date_dev=array();
								for($i=$tabindice[2];$i<sizeof($tabligne);$i++){
									// Comment la date va-t-elle être formatée?
									//$date_dev[]=$tabligne[$i];

									//echo "\$tabligne[$i]=$tabligne[$i]<br />\n";

									// Dans le cas d'un import de CSV réalisé depuis l'enregistrement ODS->CSV, on a 46 colonnes de devoirs
									// Le tabeau $date_dev[] est rempli jusqu'à l'indice 45.
									// Par contre, pour les devoirs, ne sont créés que ceux dont le nomc_dev[] est non vide
									if((strlen(ereg_replace("[0-9/]","",$tabligne[$i]))!=0)||($tabligne[$i]=="")){
										$tabligne[$i]="$jour/$mois/$annee";
									}
									//echo "\$tabligne[$i]=$tabligne[$i]<br />\n";

									$tmpdate=explode("/",$tabligne[$i]);
									if(strlen($tmpdate[0])==4){
										// Ce cas ne devrait pas se produire...
										$date="$tmpdate[0]-$tmpdate[1]-$tmpdate[2] 00:00:00";
									}
									else{
										if(strlen($tmpdate[2])==2){
											$tmpdate[2]="20".$tmpdate[2];
										}
										$date="$tmpdate[2]-$tmpdate[1]-$tmpdate[0] 00:00:00";
									}
									//echo "date=$date<br />\n";
									$date_dev[]=$date;
								}
								break;
							case "GEPI_LOGIN_ELEVE":
								if(trim($tabligne[$tabindice[1]])!=""){
									unset($tab_dev[$cpt_ele]);
									$tab_dev[$cpt_ele]=array();
									$tab_dev[$cpt_ele]['login']=$tabligne[$tabindice[1]];
									// Il faudrait tester qu'il n'y a pas de caractères invalides dans le login...

									if(strlen(ereg_replace("[A-Z0-9_]","",$tabligne[$tabindice[1]]))==0){
										// L'élève fait-il partie du groupe?
										$sql="SELECT 1=1 FROM j_eleves_groupes WHERE (login='".$tab_dev[$cpt_ele]['login']."' AND id_groupe='$id_groupe' AND periode='$periode_num')";
										$test=mysql_query($sql);
										if(mysql_num_rows($test)>0){
											$tab_dev[$cpt_ele]['note']=array();
											$tab_dev[$cpt_ele]['statut']=array();
											for($i=$tabindice[2];$i<sizeof($tabligne);$i++){
												// Reformater la note... et générer un statut...

												$note=$tabligne[$i];
												$elev_statut='';

												if($note=='disp'){
													$note='0';
													$elev_statut='disp';
												}
												elseif($note=='abs'){
													$note='0';
													$elev_statut='abs';
												}
												elseif($note=='-'){
													$note='0';
													$elev_statut='-';
												}
												elseif(ereg("^[0-9\.\,]{1,}$",$note)){
													$note=str_replace(",",".","$note");
													if(($note<0)or($note > 20)){
														$note='';
														$elev_statut='';
													}
												}
												else{
													$note='';
													$elev_statut='v';
												}

												$tab_dev[$cpt_ele]['note'][]="$note";
												$tab_dev[$cpt_ele]['statut'][]="$elev_statut";
											}
											$cpt_ele++;
										}
										else{
											$info_erreur.=$tab_dev[$cpt_ele]['login']." n'est pas membre du groupe sur la période choisie. <br />\n";
										}
									}
								}
								break;
						}
					}
				}
				fclose($fp);

				if(count($nomc_dev)==0){
					echo "<p><b>Erreur:</b> Aucun nom de devoir n'a été trouvé.</p>\n";
					echo "<p><a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine'>Cliquer ici</a> pour recommencer !</center></p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				// Et si il n'y a pas de ligne coef? ou pas de ligne date?

				if((count($nomc_dev)!=count($date_dev))||(count($nomc_dev)!=count($coef_dev))){
					echo "<p><b>Erreur:</b> Le nombre de champs ne coïncide pas pour les noms courts, coefficients et dates.</p>\n";
					echo "<p><a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine'>Cliquer ici</a> pour recommencer !</center></p>\n";
					require("../lib/footer.inc.php");
					die();
				}


				//================
				// A FAIRE:
				// AFFICHER UN TABLEAU DE CE QUI VA ETRE CRéé à CE STADE... AVEC DES CASES à COCHER POUR CONFIRMER.

				echo "<div align='center'>\n";
				echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
				echo "<input type='hidden' name='is_posted' value='y' />\n";
				echo "<input type='hidden' name='valide_insertion_devoirs' value='y' />\n";
				echo "<input type='hidden' name=\"id_racine\" value=\"$id_racine\" />\n";
				echo "<p align=\"center\"><input type=submit value=\"Importer\" /></p>\n";

				$nb_dev=0;
				echo "<table class='boireaus'>\n";
				echo "<tr>\n";
				echo "<th>&nbsp;</th>\n";
				for($i=0;$i<count($nomc_dev);$i++){
					if($nomc_dev[$i]!=""){
						echo "<th>";
						echo "<input type='hidden' name='nomc_dev[$i]' value=\"".$nomc_dev[$i]."\" />\n";
						echo $nomc_dev[$i];
						echo "</th>\n";
						$nb_dev++;
					}
				}
				echo "</tr>\n";

				echo "<tr>\n";
				echo "<th>&nbsp;</th>\n";
				for($i=0;$i<count($nomc_dev);$i++){
					if($nomc_dev[$i]!=""){
						//echo "<th>".formate_date($date_dev[$i])."</th>\n";
						echo "<th>";
						echo "<input type='hidden' name='date_dev[$i]' value=\"".$date_dev[$i]."\" />\n";
						echo formate_date($date_dev[$i]);
						echo "</th>\n";
					}
				}
				echo "</tr>\n";

				echo "<tr>\n";
				echo "<th>&nbsp;</th>\n";
				for($i=0;$i<count($nomc_dev);$i++){
					if($nomc_dev[$i]!=""){
						//echo "<th>$coef_dev[$i]</th>\n";
						echo "<th>";
						echo "<input type='hidden' name='coef_dev[$i]' value=\"".$coef_dev[$i]."\" />\n";
						echo $coef_dev[$i];
						echo "</th>\n";
					}
				}
				echo "</tr>\n";

				echo "<tr>\n";
				echo "<th>&nbsp;</th>\n";
				for($i=0;$i<count($nomc_dev);$i++){
					if($nomc_dev[$i]!=""){
						echo "<th><input type='checkbox' name='valide_import_dev[$i]' value='y' /></th>\n";
					}
				}
				echo "</tr>\n";

				// Les notes des élèves
				$alt=1;
				for($i=0;$i<count($tab_dev);$i++){
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					if(isset($tab_dev[$i]['login'])){
						echo "<td>";
						echo "<input type='hidden' name='login_ele[$i]' value=\"".$tab_dev[$i]['login']."\" />\n";
						echo $tab_dev[$i]['login'];
						echo "</td>\n";
						//for($j=0;$j<count($id_dev);$j++){
						for($j=0;$j<$nb_dev;$j++){
							if((isset($tab_dev[$i]['note'][$j]))&&(isset($tab_dev[$i]['statut'][$j]))){

								if($tab_dev[$i]['statut'][$j]!=""){
									/*
									//echo "<td>".$tab_dev[$i]['statut'][$j]."</td>\n";
									echo "<td>";
									//echo $tab_dev[$i]['statut'][$j];
									echo "<input type='text' name='tab_dev_".$i."_statut_".$j."' value=\"".$tab_dev[$i]['statut'][$j]."\" />\n";
									echo "</td>\n";
									*/
									$note=$tab_dev[$i]['statut'][$j];
									if($note=="v"){
										$note="";
									}
								}
								else{
									/*
									//echo "<td>".$tab_dev[$i]['note'][$j]."</td>\n";
									echo "<td>";
									//echo $tab_dev[$i]['statut'][$j];
									echo "<input type='text' name='tab_dev_".$i."_note_".$j."' value=\"".$tab_dev[$i]['note'][$j]."\" />\n";
									echo "</td>\n";
									*/
									$note=$tab_dev[$i]['note'][$j];
								}
								echo "<td>";
								echo "<input type='text' name='tab_dev_".$i."_note[".$j."]' value=\"".$note."\" size='4' />\n";
								echo "</td>\n";
							}
						}
					}
					echo "</tr>\n";
				}

				echo "</table>\n";
				echo "<p align=\"center\"><input type=submit value=\"Importer\" /></p>\n";
				echo "</form>\n";

				echo "</div>\n";
				//================
			}
		}
	}
	else{

		// RECUPERER LES SAISIES/VALIDATIONS...
		$nomc_dev=isset($_POST['nomc_dev']) ? $_POST['nomc_dev'] : NULL;
		if(!isset($nomc_dev)){
			echo "<p>ERREUR: Aucun devoir importé.</p>\n";
			require("../lib/footer.inc.php");
		}

		$date_dev=isset($_POST['date_dev']) ? $_POST['date_dev'] : NULL;
		if(!isset($date_dev)){
			echo "<p>ERREUR: Aucune date définie.</p>\n";
			require("../lib/footer.inc.php");
		}

		$coef_dev=isset($_POST['coef_dev']) ? $_POST['coef_dev'] : NULL;
		if(!isset($coef_dev)){
			echo "<p>ERREUR: Aucun coefficient défini.</p>\n";
			require("../lib/footer.inc.php");
		}

		$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : NULL;
		if(!isset($login_ele)){
			echo "<p>ERREUR: Aucun élève importé.</p>\n";
			require("../lib/footer.inc.php");
		}

		$valide_import_dev=isset($_POST['valide_import_dev']) ? $_POST['valide_import_dev'] : NULL;


		for($i=0;$i<count($login_ele);$i++){
			$tab_dev_note[$i]=$_POST['tab_dev_'.$i.'_note'];
		}

		// Création des devoirs:
		// On crée les devoirs à la racine... pas de gestion des boites pour le moment
		$id_conteneur=$id_racine;
		echo "<p>\n";
		//unset($temoin_dev);
		//$temoin_dev=array();
		//echo "count(\$nomc_dev)=".count($nomc_dev)."<br />";
		for($i=0;$i<count($nomc_dev);$i++){
			if($nomc_dev[$i]!=""){
				if(isset($valide_import_dev[$i])){
					/*
					$sql="INSERT INTO cn_devoirs SET id_racine='$id_racine',
													id_conteneur='$id_conteneur',
													nom_court='".$nomc_dev[$i]."'
													nom_complet='".$nomc_dev[$i]."',
													date='".$date_dev[$i]."',
													coef='".$coef_dev[$i]."',
													display_parents='1';";
					*/
					$sql="INSERT INTO cn_devoirs SET id_racine='$id_racine',
													id_conteneur='$id_conteneur',
													nom_court='Nouveau';";
					//echo "$sql<br />\n";
					$res_insert=mysql_query($sql);
					if($res_insert){
						$id_dev[$i]=mysql_insert_id();
					}
					else{
						echo "<p><b>Erreur</b> lors de la création du devoir n°$i (<i>$nomc_dev[$i]</i>).</p>\n";
						echo "<p><a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine'>Cliquer ici</a> pour recommencer !</center></p>\n";
						require("../lib/footer.inc.php");
						die();
					}

					$sql="UPDATE cn_devoirs SET nom_court='".$nomc_dev[$i]."',
												nom_complet='".$nomc_dev[$i]."',
												date='".$date_dev[$i]."',
												coef='".$coef_dev[$i]."',
												display_parents='1'
											WHERE id='$id_dev[$i]';";
					echo "Création du devoir n°$i: $nomc_dev[$i]<br />\n";
					//echo "$sql<br />\n";
					$res_update=mysql_query($sql);
					if(!$res_update){
					/*
						$temoin_dev[$i]="ERREUR";
					}
					else{
						$temoin_dev[$i]="OK";
					*/
						echo "<p><b>Erreur</b> lors de la création du devoir n°$i (<i>$nomc_dev[$i]</i>).</p>\n";
						echo "<p><a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine'>Cliquer ici</a> pour recommencer !</center></p>\n";
						require("../lib/footer.inc.php");
						die();
					}
					flush();
				}
			}
		}
		echo "</p>\n";

		// On passe à l'insertion des notes
		// $tab_dev[$cpt_ele]['login']
		// $tab_dev[$cpt_ele]['note'][]
		// $tab_dev[$cpt_ele]['statut'][]
		echo "<p>Insertion des notes pour ";
		/*
		for($i=0;$i<count($tab_dev);$i++){
			if($i>0){echo ", ";}
			if(isset($tab_dev[$i]['login'])){
				echo $tab_dev[$i]['login'];
				for($j=0;$j<count($id_dev);$j++){
					if((isset($tab_dev[$i]['note'][$j]))&&(isset($tab_dev[$i]['statut'][$j]))){
						$sql="INSERT INTO cn_notes_devoirs SET login='".$tab_dev[$i]['login']."',
																id_devoir='".$id_dev[$j]."',
																note='".$tab_dev[$i]['note'][$j]."',
																statut='".$tab_dev[$i]['statut'][$j]."';";
						//echo "$sql<br />\n";
						//echo "OK<br />\n";
						$res_insert=mysql_query($sql);
						// METTRE LES ERREURS DANS UN $msg?
					}

					if($i==count($tab_dev)-1){
						//echo " (recalcul) ";
						$arret = 'no';
						mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur,$arret);
						// La boite courante est mise à jour...
						// ... mais pas la boite destination.
						// Il faudrait rechercher pour $id_racine les derniers descendants et lancer la mise à jour sur chacun de ces descendants.
						// C'est fait là:
						recherche_enfant($id_racine);
					}
					flush();
				}
			}
		}
		*/

		//for($i=0;$i<count($tab_dev);$i++){
		//for($i=0;$i<count($nomc_dev);$i++){
		for($i=0;$i<count($login_ele);$i++){
			if($i>0){echo ", ";}
			if(isset($login_ele[$i])) {
				echo $login_ele[$i];
				//for($j=0;$j<count($id_dev);$j++){
				for($j=0;$j<count($nomc_dev);$j++){
					if(isset($valide_import_dev[$j])){
						if(isset($id_dev[$j])){
							if(isset($tab_dev_note[$i][$j])) {

								if(strtolower($tab_dev_note[$i][$j])=="abs"){
									$note=0;
									$elev_statut="abs";
								}
								elseif(strtolower($tab_dev_note[$i][$j])=="disp"){
									$note=0;
									$elev_statut="disp";
								}
								elseif(strtolower($tab_dev_note[$i][$j])=="-"){
									$note=0;
									$elev_statut="-";
								}
								elseif(ereg("^[0-9\.\,]{1,}$",$tab_dev_note[$i][$j])){
									$note=str_replace(",",".",$tab_dev_note[$i][$j]);
									$elev_statut='';
									if(($note<0)or($note > 20)){
										$note='';
										$elev_statut='';
									}
								}
								else{
									$note='';
									$elev_statut='v';
								}

								/*
								$sql="INSERT INTO cn_notes_devoirs SET login='".$tab_dev[$i]['login']."',
																		id_devoir='".$id_dev[$j]."',
																		note='".$tab_dev[$i]['note'][$j]."',
																		statut='".$tab_dev[$i]['statut'][$j]."';";
								*/
								$sql="INSERT INTO cn_notes_devoirs SET login='".$login_ele[$i]."',
																		id_devoir='".$id_dev[$j]."',
																		note='".$note."',
																		statut='".$elev_statut."';";
								//echo "$sql<br />\n";
								//echo "OK<br />\n";
								$res_insert=mysql_query($sql);
								// METTRE LES ERREURS DANS UN $msg?
							}

							//if($i==count($tab_dev)-1){
							//if($i==count($nomc_dev)-1){
							if($i==count($login_ele)-1){
								//echo " (recalcul) ";
								$arret = 'no';
								mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur,$arret);
								// La boite courante est mise à jour...
								// ... mais pas la boite destination.
								// Il faudrait rechercher pour $id_racine les derniers descendants et lancer la mise à jour sur chacun de ces descendants.
								// C'est fait là:
								recherche_enfant($id_racine);
							}
							flush();
						}
					}
				}
			}
		}

		echo "</p>\n";

		echo "<p><a href='saisie_notes.php?id_conteneur=$id_racine'>Visualiser les devoirs</a></p>\n";
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>