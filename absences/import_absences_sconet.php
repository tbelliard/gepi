<?php
@set_time_limit(0);

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

// INSERT INTO droits VALUES ('/absences/import_absences_sconet.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'Saisie des absences', '');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//**************** EN-TETE *****************
$titre_page = "Import absences SCONET";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

function extr_valeur($lig){
	unset($tabtmp);
	$tabtmp=explode(">",ereg_replace("<",">",$lig));
	return trim($tabtmp[2]);
}

function get_nom_class_from_id($id){
	$classe=NULL;

	$sql="SELECT classe FROM classes WHERE id='$id';";
	$res_class=mysql_query($sql);

	if(mysql_num_rows($res_class)>0){
		$lig_tmp=mysql_fetch_object($res_class);
		$classe=$lig_tmp->classe;
	}
	return $classe;
}


?>
	<div class="content">
		<?php

			$is_posted=isset($_POST['is_posted']) ? $_POST['is_posted'] : NULL;
			$etape=isset($_POST['etape']) ? $_POST['etape'] : (isset($_GET['etape']) ? $_GET['etape'] : NULL);

			if(isset($_GET['ad_retour'])){
				$_SESSION['ad_retour']=$_GET['ad_retour'];
			}

			// Initialisation du répertoire actuel de sauvegarde
			//$dirname = getSettingValue("backup_directory");

			echo "<h2 align='center'>Import des absences de Sconet</h2>\n";

			//echo "<p><a href='index.php'>Retour</a>|\n";
			echo "<p class=bold><a href='";
			if(isset($_SESSION['ad_retour'])){
				echo $_SESSION['ad_retour'];
			}
			else{
				echo "index.php";
			}
			echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
			//echo "</p>\n";


			// Il faudra pouvoir gérer id_classe comme un tableau
			$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
			$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);

			if(!isset($id_classe)){
				echo "</p>\n";

				// Il faudra pouvoir gérer les cpe responsables seulement dans certaines classes...
				$sql="SELECT * FROM classes ORDER BY classe";
				$res_classe=mysql_query($sql);

				$nb_classes = mysql_num_rows($res_classe);
				if ($nb_classes==0) {
					echo "<p>Aucune classe n'a été trouvée.</p>\n";
				}
				else{
					echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

					echo "<p>Choisissez les classes à importer.</p>\n";

					$nb_class_par_colonne=round($nb_classes/3);
					//echo "<table width='100%' border='1'>\n";
					echo "<table width='100%'>\n";
					echo "<tr valign='top' align='center'>\n";

					$i = '0';

					echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
					echo "<td align='left'>\n";

					while ($lig_classe=mysql_fetch_object($res_classe)) {

						$id_classe=$lig_classe->id;
						$classe=$lig_classe->classe;

						if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
							echo "</td>\n";
							//echo "<td style='padding: 0 10px 0 10px'>\n";
							echo "<td align='left'>\n";
						}

						//echo "<span class = \"norme\"><input type='checkbox' name='$temp' value='yes' onclick=\"verif1()\" />";
						//echo "Classe : $classe </span><br />\n";
						echo "<input type='checkbox' name='id_classe[]' id='case_".$i."' value='$id_classe' />";
						echo "Classe : $classe<br />\n";
						$i++;
					}
					echo "</td>\n";
					echo "</tr>\n";
					echo "</table>\n";
					echo "<p><input type='submit' value='Valider' /></p>\n";

					echo "<p><a href='#' onClick='Coche_ou_pas(true); return false;'>Tout cocher</a> / <a href='#' onClick='Coche_ou_pas(false); return false;'>Tout décocher</a></p>\n";

					echo "</form>\n";

					echo "<script type='text/javascript' language='javascript'>

function Coche_ou_pas(mode) {
	for(i=0;i<$i;i++) {
		if(document.getElementById('case_'+i)){
			document.getElementById('case_'+i).checked = mode;
		}
	}
}

</script>\n";

				}
			}
			else{

				echo " | <a href='".$_SERVER['PHP_SELF']."'>Importer plusieurs classes</a>";
				echo "</p>\n";

				// VERIFIER SI TOUTES LES CLASSES ONT LE MÊME NOMBRE DE PERIODES
				$temoin_pb_periodes='n';
				//unset($tab_max_per);
				//$tab_max_per=array();
				if(is_array($id_classe)){
					$max_per_precedent="";
					for($i=0;$i<count($id_classe);$i++){
						$sql="SELECT MAX(num_periode) as max_num FROM periodes WHERE id_classe='$id_classe[$i]'";
						$test=mysql_query($sql);

						if(mysql_num_rows($test)==0){
							$temoin_pb_periodes='o';
							echo "<p><span class='color:red;'>ERREUR:</span> Aucune période ne semble définie pour la classe ";
							$tmp_classe=get_nom_class_from_id($id_classe[$i]);
							if($tmp_classe){
								echo $tmp_classe;
							}
							else{
								echo "<span class='color:red;'>pas de nom???</span>";
							}
							echo "(<i>$id_classe[$i]</i>).</p>\n";

							require("../lib/footer.inc.php");
							exit();
						}
						else{
							$lig_tmp=mysql_fetch_object($test);
							if($max_per_precedent!=""){
								if($lig_tmp->max_num!=$max_per_precedent){
									echo "<p><span class='color:red;'>ERREUR:</span> Toutes les classes choisies n'ont pas le même nombre de périodes.</p>\n";

									require("../lib/footer.inc.php");
									exit();
								}
							}
							$max_per_precedent=$lig_tmp->max_num;
						}
					}
				}



				if(!isset($num_periode)){
					echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

					echo "<p>Choisissez la période à importer ";

					if(is_array($id_classe)){
						echo "pour la(es) classe(s) de ";

						for($i=0;$i<count($id_classe);$i++){
							$tmp_classe=get_nom_class_from_id($id_classe[$i]);
							if($tmp_classe){
								if($i>0){echo ", ";}
								echo "$tmp_classe";

								echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
							}
						}

						$sql="SELECT DISTINCT num_periode, nom_periode FROM periodes WHERE id_classe='$id_classe[0]' ORDER BY num_periode;";
					}
					else{
						echo "pour la classe de ";

						$tmp_classe=get_nom_class_from_id($id_classe);
						if($tmp_classe){
							echo "$tmp_classe";

							echo "<input type='hidden' name='id_classe[]' value='$id_classe' />\n";
						}
						else{
							echo "<span class='color:red;'>ERREUR:</span> La classe $id_classe n'aurait pas de nom?";
						}

						$sql="SELECT DISTINCT num_periode, nom_periode FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode;";
					}

					echo ".</p>\n";

					$res_per=mysql_query($sql);
					while($lig_tmp=mysql_fetch_object($res_per)){
						// Il ne faudrait proposer que les périodes ouvertes en saisie, non?
						echo "<input type='radio' name='num_periode' value='$lig_tmp->num_periode' /> $lig_tmp->nom_periode<br />\n";
					}

					echo "<p><input type='submit' value='Valider' /></p>\n";
					echo "</form>\n";
				}
				else{

					if(!isset($_POST['is_posted'])){
						$etape=1;

						//echo "<p>Cette page permet de remplir des tableaux PHP avec les informations élèves, responsables,...<br />\n";
						echo "<p>Cette page permet de remplir des tables temporaires avec les informations extraites du XML.<br />\n";
						echo "</p>\n";
						echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

						echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";

						for($i=0;$i<count($id_classe);$i++){
							echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
						}

						echo "<p>Veuillez fournir le fichier exportAbsence.xml:<br />\n";
						echo "<input type=\"file\" size=\"80\" name=\"absences_xml_file\" /><br />\n";
						echo "<input type='hidden' name='etape' value='$etape' />\n";
						echo "<input type='hidden' name='is_posted' value='yes' />\n";
						echo "</p>\n";

						echo "<p><input type='submit' value='Valider' /></p>\n";
						echo "</form>\n";

						echo "<p><b>ATTENTION</b>: Fournir un export d'une seule période.<br />Si plusieurs périodes sont cochées lors de votre export, les informations importées risquent d'être erronées.</p>\n";

					}
					else{
						$post_max_size=ini_get('post_max_size');
						$upload_max_filesize=ini_get('upload_max_filesize');
						$max_execution_time=ini_get('max_execution_time');
						$memory_limit=ini_get('memory_limit');


						if($etape==1){
							$xml_file = isset($_FILES["absences_xml_file"]) ? $_FILES["absences_xml_file"] : NULL;
							$fp=fopen($xml_file['tmp_name'],"r");
							if($fp){
								//echo "<h3>Lecture du fichier absences...</h3>\n";
								//echo "<blockquote>\n";
								while(!feof($fp)){
									$ligne[]=fgets($fp,4096);
								}
								fclose($fp);
								//echo "<p>Terminé.</p>\n";
								flush();


								//echo "<h3>Analyse du fichier pour extraire les informations...</h3>\n";
								//echo "<blockquote>\n";

								$cpt=0;
								$eleves=array();
								$temoin_eleves=0;
								$temoin_ele=0;
								$temoin_options=0;
								$temoin_scol=0;
								//Compteur élève:
								$i=-1;

								$tab_champs_parametres=array("uaj",
								"annee_scolaire",
								"date_export",
								"horodatage");


								// PARTIE <PARAMETRES>
								$temoin_parametres=0;
								$tab_parametres=array();
								while($cpt<count($ligne)){
									//echo "<p>".htmlentities($ligne[$cpt])."<br />\n";
									if(strstr($ligne[$cpt],"<PARAMETRES>")){
										//echo "Début de la section PARAMETRES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_parametres++;
									}
									if(strstr($ligne[$cpt],"</PARAMETRES>")){
										//echo "Fin de la section PARAMETRES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_parametres++;
										break;
									}
									if($temoin_parametres==1){
										if(strstr($ligne[$cpt],"<UAJ>")){
											$tab_parametres['uaj']=extr_valeur($ligne[$cpt]);
										}

										if(strstr($ligne[$cpt],"<ANNEE_SCOLAIRE>")){
											$tab_parametres['annee_scolaire']=extr_valeur($ligne[$cpt]);
										}

										if(strstr($ligne[$cpt],"<DATE_EXPORT>")){
											$tab_parametres['date_export']=extr_valeur($ligne[$cpt]);
										}

										if(strstr($ligne[$cpt],"<HORODATAGE>")){
											$tab_parametres['horodatage']=extr_valeur($ligne[$cpt]);
										}
									}
									$cpt++;
								}



								$tab_champs_periode=array("libelle",
								"date_debut",
								"date_fin");

								// PARTIE <PERIODE>
								$temoin_periode=0;
								$tab_periode=array();
								while($cpt<count($ligne)){
									//echo "<p>".htmlentities($ligne[$cpt])."<br />\n";
									if(strstr($ligne[$cpt],"<PERIODE>")){
										//echo "Début de la section PERIODE à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_periode++;
									}
									if(strstr($ligne[$cpt],"</PERIODE>")){
										//echo "Fin de la section PERIODE à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_periode++;
										break;
									}
									if($temoin_periode==1){
										if(strstr($ligne[$cpt],"<LIBELLE>")){
											$tab_periode['libelle']=extr_valeur($ligne[$cpt]);
										}

										if(strstr($ligne[$cpt],"<DATE_DEBUT>")){
											$tab_periode['date_debut']=extr_valeur($ligne[$cpt]);
										}

										if(strstr($ligne[$cpt],"<DATE_FIN>")){
											$tab_periode['date_fin']=extr_valeur($ligne[$cpt]);
										}
									}
									$cpt++;
								}

								$tab_champs_eleve=array("elenoet",
								"libelle",
								"nbAbs",
								"nbNonJustif",
								"nbRet",
								"nomEleve",
								"prenomEleve"
								);

								// PARTIE <ELEVES>
								$i=-1;
								while($cpt<count($ligne)){
									if(strstr($ligne[$cpt],"<eleve ")){
										$i++;
										$eleves[$i]=array();

										$ligne_courante=$ligne[$cpt];
										while(!ereg("/>",$ligne[$cpt])){
											$cpt++;
											$ligne_courante.=" ".trim($ligne[$cpt]);
										}

										//echo "<p>".$ligne_courante."<br />\n";

										unset($tab_tmp);
										# En coupant aux espaces, on a des blagues sur les noms avec espaces...
										$tab_tmp=explode(" ",$ligne_courante);
										for($j=0;$j<count($tab_tmp);$j++){
											//echo "\$tab_tmp[$j]=".$tab_tmp[$j]."<br />";
											if(ereg("=",$tab_tmp[$j])) {
												unset($tab_tmp2);
												$tab_tmp2=explode("=",ereg_replace('"','',$tab_tmp[$j]));
												//echo "\$tab_tmp2[0]=".$tab_tmp[0]."<br />";
												//echo "\$tab_tmp2[1]=".$tab_tmp[1]."<br />";
												$eleves[$i][trim($tab_tmp2[0])]=trim($tab_tmp2[1]);
											}
										}
									}
									$cpt++;
								}

								/*
								echo "<table border='1'>";
								for($i=0;$i<count($eleves);$i++){
									echo "<tr>";
									echo "<td>$i</td>";
									foreach($eleves[$i] as $cle => $valeur){
										echo "<td>\$eleves[$i][\"$cle\"]=".$eleves[$i][$cle]."</td>\n";
									}
									echo "</tr>";
								}
								echo "</table>";
								*/



								/*
								$num_periode="NaN";
								if(isset($tab_periode['libelle'])) {
									$num_periode=trim(ereg_replace("^T","",$tab_periode['libelle']));
								}
								*/


								echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
								for($i=0;$i<count($id_classe);$i++){
									echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
								}

								echo "<table class='boireaus'>\n";
								echo "<tr>\n";
								echo "<th>&nbsp;</th>\n";
								echo "<th>Elenoet</th>\n";
								echo "<th>Nom</th>\n";
								echo "<th>Prénom</th>\n";
								echo "<th>Classe</th>\n";
								echo "<th>Nombre d'absences</th>\n";
								echo "<th>Non justifiées</th>\n";
								echo "<th>Nombre de retards</th>\n";
								echo "</tr>\n";

								$chaine_liste_classes="(";
								for($i=0;$i<count($id_classe);$i++){
									if($i>0){$chaine_liste_classes.=" OR ";}
									$chaine_liste_classes.="id_classe='$id_classe[$i]'";
								}
								$chaine_liste_classes.=")";

								$nb_err=0;
								$alt=-1;
								for($i=0;$i<count($eleves);$i++){

									//******************************
									// A FAIRE
									// CONTRÔLER SI L'ELEVE EST DANS UNE DES CLASSES IMPORTEES
									//******************************

									if(isset($eleves[$i]['elenoet'])){

										// Est-ce que l'élève fait bien partie d'une des classes importées pour la période importée?
										//$sql="SELECT 1=1 FROM j_eleves_classes jec, eleves e WHERE jec.login=e.login AND e.no_gep='".$eleves[$i]['elenoet']."' AND periode='$num_periode' AND $chaine_liste_classes;";
										$sql="SELECT 1=1 FROM j_eleves_classes jec, eleves e WHERE jec.login=e.login AND (e.elenoet='".$eleves[$i]['elenoet']."' OR e.elenoet='0".$eleves[$i]['elenoet']."') AND periode='$num_periode' AND $chaine_liste_classes;";
										$test=mysql_query($sql);

										if(mysql_num_rows($test)>0){
											$alt=$alt*(-1);
											echo "<tr class='lig$alt'>\n";
											echo "<td>$i</td>\n";


											/*
											$sql="SELECT DISTINCT e.login,e.nom,e.prenom,c.classe
														FROM eleves e,
															j_eleves_classes jec,
															classes c
														WHERE (e.no_gep='".$eleves[$i]['elenoet']."' OR e.no_gep='0".$eleves[$i]['elenoet']."') AND
															e.login=jec.login AND
															jec.id_classe=c.id";
											*/
											$sql="SELECT e.login,e.nom,e.prenom,e.elenoet
														FROM eleves e
														WHERE (e.elenoet='".$eleves[$i]['elenoet']."' OR e.elenoet='0".$eleves[$i]['elenoet']."')";
											$res1=mysql_query($sql);
											if(mysql_num_rows($res1)==0){
												echo "<td>".$eleves[$i]['elenoet']."</td>\n";
												echo "<td style='color:red;' colspan='3'>Elève absent de votre table 'eleves'???</td>\n";
												$nb_err++;
											}
											elseif(mysql_num_rows($res1)==1){
												$lig1=mysql_fetch_object($res1);
												echo "<td>$lig1->elenoet\n";
												echo "<input type='hidden' name='log_eleve[$i]' value='$lig1->login' />\n";
												echo "</td>\n";
												echo "<td>$lig1->nom</td>\n";
												echo "<td>$lig1->prenom</td>\n";

												echo "<td>\n";
												$sql="SELECT c.classe FROM j_eleves_classes jec, classes c
														WHERE jec.login='$lig1->login' AND
															jec.id_classe=c.id AND periode='$num_periode'";
												$res2=mysql_query($sql);
												if(mysql_num_rows($res2)==0){
													echo "<span style='color:red;'>NA</span>\n";
												}
												else {
													$cpt=0;
													while($lig2=mysql_fetch_object($res2)){
														if($cpt>0){
															echo ", ";
														}
														echo $lig2->classe;
													}
												}
												echo "</td>\n";
											}
											else{
												echo "<td>".$eleves[$i]['elenoet']."</td>\n";
												echo "<td style='color:red;' colspan='3'>Plus d'un élève correspond à cet ELENOET ???</td>\n";
												$nb_err++;
											}

											echo "<td>\n";
											if(isset($eleves[$i]['nbAbs'])){
												echo $eleves[$i]['nbAbs'];
												echo "<input type='hidden' name='nbabs_eleve[$i]' value='".$eleves[$i]['nbAbs']."' />\n";
											}
											else{
												//echo "&nbsp;";
												echo "<span style='color:red;'>ERR</span>\n";
												//echo "<input type='hidden' name='nbabs_eleve[$i]' value='0' />\n";
												$nb_err++;
											}
											echo "</td>\n";

											echo "<td>\n";
											if(isset($eleves[$i]['nbNonJustif'])){
												echo $eleves[$i]['nbNonJustif'];
												echo "<input type='hidden' name='nbnj_eleve[$i]' value='".$eleves[$i]['nbNonJustif']."' />\n";
											}
											else{
												//echo "&nbsp;";
												echo "<span style='color:red;'>ERR</span>\n";
												//echo "<input type='hidden' name='nbnj_eleve[$i]' value='0' />\n";
												$nb_err++;
											}
											echo "</td>\n";

											echo "<td>\n";
											if(isset($eleves[$i]['nbRet'])){
												echo $eleves[$i]['nbRet'];
												//echo " -&gt; <input type='text' size='4' name='nbret_eleve[$i]' value='".$eleves[$i]['nbRet']."' />\n";
												echo "<input type='hidden' size='4' name='nbret_eleve[$i]' value='".$eleves[$i]['nbRet']."' />\n";
											}
											else{
												//echo "&nbsp;";
												echo "<span style='color:red;'>ERR</span>\n";
												//echo "<input type='hidden' name='nbret_eleve[$i]' value='0' />\n";
												$nb_err++;
											}
											echo "</td>\n";

											echo "</tr>\n";
										}
									}
								}
								echo "</table>\n";
								echo "<input type='hidden' name='nb_eleves' value='$i' />\n";
								echo "<input type='hidden' name='is_posted' value='y' />\n";
								echo "<input type='hidden' name='etape' value='2' />\n";

								# A RENSEIGNER D'APRES L'EXTRACTION:
								echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";

								echo "<p align='center'><input type='submit' value='Importer' /></p>\n";
								echo "</form>\n";

								echo "<p><i>NOTE:</i> Si des lignes sont marquées d'un <span style='color:red;'>ERR</span>, les valeurs ne seront pas importées pour cet(s) élève(s).</p>\n";

							}
							else{
								// PB $fp
							}
						}
						if($etape==2){
							$log_eleve=isset($_POST['log_eleve']) ? $_POST['log_eleve'] : NULL;
							$nbabs_eleve=isset($_POST['nbabs_eleve']) ? $_POST['nbabs_eleve'] : NULL;
							$nbnj_eleve=isset($_POST['nbnj_eleve']) ? $_POST['nbnj_eleve'] : NULL;
							$nbret_eleve=isset($_POST['nbret_eleve']) ? $_POST['nbret_eleve'] : NULL;
							$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : NULL;

							$nb_eleves=isset($_POST['nb_eleves']) ? $_POST['nb_eleves'] : NULL;


							// On initialise à zéro les absences, retards,... pour tous les élèves des classes importées et les valeurs extraites du XML de Sconet écraseront ces initialisations.
							// Si on ne fait pas cette initialisation, les élèves qui n'ont aucune absence ni retard apparaissent avec un '?' au lieu d'un Zéro/Aucune.
							for($i=0;$i<count($id_classe);$i++){
								$sql="SELECT login FROM j_eleves_classes WHERE id_classe='$id_classe[$i]' AND periode='$num_periode';";
								$res_ele=mysql_query($sql);
								if(mysql_num_rows($res_ele)>0){
									while($lig_tmp=mysql_fetch_object($res_ele)){
										$sql="DELETE FROM absences WHERE login='$lig_tmp->login' AND periode='$num_periode';";
										$res_menage=mysql_query($sql);

										$sql="INSERT INTO absences SET login='$lig_tmp->login', periode='$num_periode', nb_absences='0', non_justifie='0', nb_retards='0';";
										$res_ini=mysql_query($sql);
									}
								}
							}


							$nb_err=0;
							$nb_ok=0;
							echo "<p>Importation: ";
							//for($i=0;$i<count($log_eleve);$i++){
							for($i=0;$i<$nb_eleves;$i++){
								if((isset($log_eleve[$i]))&&
									(isset($nbabs_eleve[$i]))&&
									(isset($nbnj_eleve[$i]))&&
									(isset($nbret_eleve[$i]))
								) {
									if(($nb_ok>0)||($nb_err>0)){echo ", ";}

									$sql="SELECT 1=1 FROM absences WHERE periode='$num_periode' AND login='".$log_eleve[$i]."';";
									$test1=mysql_query($sql);
									if(mysql_num_rows($test1)==0){
										$sql="INSERT INTO absences SET periode='$num_periode',
																		login='".$log_eleve[$i]."',
																		nb_absences='".$nbabs_eleve[$i]."',
																		nb_retards='".$nbret_eleve[$i]."',
																		non_justifie='".$nbnj_eleve[$i]."';";
										$insert=mysql_query($sql);
										if($insert){
											$nb_ok++;
											echo "<span style='color:green;'>".$log_eleve[$i]."</span>";
										}
										else{
											$nb_err++;
											echo "<span style='color:red;'>".$log_eleve[$i]."</span>";
										}
									}
									else{
										$sql="UPDATE absences SET nb_absences='".$nbabs_eleve[$i]."',
																	nb_retards='".$nbret_eleve[$i]."',
																	non_justifie='".$nbnj_eleve[$i]."'
																WHERE periode='$num_periode' AND
																		login='".$log_eleve[$i]."';";
										$update=mysql_query($sql);
										if($update){
											$nb_ok++;
											echo "<span style='color:green;'>".$log_eleve[$i]."</span>";
										}
										else{
											$nb_err++;
											echo "<span style='color:red;'>".$log_eleve[$i]."</span>";
										}
									}
								}
							}
							echo "</p>\n";
							echo "<p>Importation effectuée";
							if($nb_err==0){
								echo " sans erreur.</p>\n";
							}
							elseif($nb_err==1){
								echo " avec une erreur.</p>\n";
							}
							else{
								echo " avec $nb_err erreurs.</p>\n";
							}
							echo "<p><br /></p>\n";


							echo "<p>Contrôler les saisies pour la classe de:</p>\n";
							/*
							echo "<ul>\n";
							for($i=0;$i<count($id_classe);$i++){
								echo "<li><a href='saisie_absences.php?id_classe=$id_classe[$i]&amp;periode_num=$num_periode' target='_blank'>".get_nom_class_from_id($id_classe[$i])."</a></li>\n";
							}
							echo "</ul>\n";
							*/

							$nb_classes=count($id_classe);
							$nb_class_par_colonne=round($nb_classes/3);
							echo "<table width='100%'>\n";
							echo "<tr valign='top' align='center'>\n";

							$i = '0';

							echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
							echo "<td align='left'>\n";

							for($i=0;$i<count($id_classe);$i++){

								if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
									echo "</td>\n";
									echo "<td align='left'>\n";
								}

								echo "<a href='saisie_absences.php?id_classe=$id_classe[$i]&amp;periode_num=$num_periode' target='_blank'>".get_nom_class_from_id($id_classe[$i])."</a><br />\n";
							}
							echo "</td>\n";
							echo "</tr>\n";
							echo "</table>\n";

						}
					} // Fin is_posted
				}
			}
			echo "<p><br /></p>\n";

		?>
	</div>
<?php
	require("../lib/footer.inc.php");
?>
