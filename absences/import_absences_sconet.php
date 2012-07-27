<?php
@set_time_limit(0);

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

// INSERT INTO droits VALUES ('/absences/import_absences_sconet.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'Saisie des absences', '');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//**************** EN-TETE *****************
$titre_page = "Import absences SCONET";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();
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

			//==============================================================
			$res = mysql_query('select * from temp_abs_import LIMIT 1;');
			$numOfCols = mysql_num_fields($res);
			// Même si la table est vide, on récupère bien la liste des champs
			//$result .= "Nombre de colonnes dans la table 'temp_abs_import' : $numOfCols<br />";
			//$result .= "Nombre d'enregistrements dans la table 'temp_abs_import' : ".mysql_num_rows($res)."<br />";

			$tab_champs_temp_abs_import=array('id', 'login', 'cpe_login', 'elenoet', 'libelle', 'nbAbs', 'nbNonJustif', 'nbRet');
			$nb_champs_temp_abs_import_trouves=0;
			for($i=0;$i<$numOfCols;$i++) {
				//$result .= mysql_field_name($res, $i) . "<br />\n";
				$nom_du_champ=mysql_field_name($res, $i);
				for($j=0;$j<count($tab_champs_temp_abs_import);$j++) {
					if($nom_du_champ==$tab_champs_temp_abs_import[$j]) {
						$nb_champs_temp_abs_import_trouves++;
					}
				}
			}
			if($nb_champs_temp_abs_import_trouves!=count($tab_champs_temp_abs_import)) {
				echo "<p style='color:red; margin-left: 6em; text-indent: -6em;'><strong>ERREUR&nbsp;:</strong> La table 'temp_abs_import' n'a pas la bonne structure.<br />Contactez l'administrateur pour qu'il <strong>force</strong> une <strong>Mise à jour de la base</strong><br />(<em>cela se trouve dans <strong>Gestion générale/Mise à jour de la base/Forcer la mise à jour</strong></em>)</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			//==============================================================


			// Il faudra pouvoir gérer id_classe comme un tableau
			$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
			$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);
			$max_per=isset($_POST['max_per']) ? $_POST['max_per'] : (isset($_GET['max_per']) ? $_GET['max_per'] : NULL);


			include "../lib/periodes.inc.php";

			if(!isset($num_periode)) {

				$sql="SELECT MAX(num_periode) AS max_per, id_classe FROM periodes GROUP BY id_classe ORDER BY max_per;";
				$res1=mysql_query($sql);

				unset($tab_max_per);
				$tab_max_per=array();
				while($lig1=mysql_fetch_object($res1)){
					if(!in_array($lig1->max_per,$tab_max_per)){
						//echo "$lig1->id_classe: $lig1->max_per<br />\n";
						$tab_max_per[]=$lig1->max_per;
					}
				}
				sort($tab_max_per);

				if(count($tab_max_per)==0){
					echo "<p><span style='color:red;'>ERREUR:</span> Il semble qu'aucune classe n'ait de période définie.</p>\n";
					require("../lib/footer.inc.php");
					exit();
				}
				elseif(count($tab_max_per)==1){
					echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

					echo "<p>Choisissez la période à importer:<br />\n";

					$i=0;
					for($j=1;$j<=$tab_max_per[$i];$j++){
						//echo "<input type='radio' name='num_periode' value='$j' /> Période $j<br />\n";
						if($j==1){$checked=" checked";}else{$checked="";}
						echo "<input type='radio' name='num_periode' id='num_periode_$j' value='$j'$checked /><label for='num_periode_$j' style='cursor: pointer;'> Période $j</label><br />\n";
					}
					echo "<input type='hidden' name='max_per' value='$tab_max_per[$i]' />\n";

					echo "<p><input type='submit' value='Valider' /></p>\n";
					echo "</form>\n";
				}
				else{

					echo "<p>Choisissez la période à importer:</p>\n";
					//echo "<ul>\n";
					echo "<table class='boireaus'>\n";
					$alt=1;
					for($i=0;$i<count($tab_max_per);$i++){
						//echo "<li>\n";

						$alt=$alt*(-1);
						echo "<tr class='lig$alt'><td>\n";

							echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
							echo "<table border='0'>\n";
							echo "<tr><td valign='top' style='border:0px;'>Classes à $tab_max_per[$i] périodes:</td>\n";
							echo "<td style='border:0px;'>\n";
							for($j=1;$j<=$tab_max_per[$i];$j++){
								if($j==1){$checked=" checked";}else{$checked="";}
								echo "<input type='radio' name='num_periode' id='num_periode_".$j."_".$tab_max_per[$i]."' value='$j'$checked /><label for='num_periode_".$j."_".$tab_max_per[$i]."' style='cursor: pointer;'> Période $j</label><br />\n";
							}
							echo "</td>\n";

							echo "<td valign='top' style='border:0px;'>\n";
							echo "<input type='hidden' name='max_per' value='$tab_max_per[$i]' />\n";
							echo "<p><input type='submit' value='Valider' /></p>\n";
							echo "</td>\n";
							echo "</tr>\n";
							echo "</table>\n";
							echo "</form>\n";

						//echo "</li>\n";
						echo "</td></tr>\n";

					}
					//echo "</ul>\n";
					echo "</table>\n";

					echo "<p><i>NOTE:</i> Il n'est pas possible d'importer simultanément des absences de classes dont le nombre de périodes diffère.</p>\n";
				}
			}
			else {

				// =======================================================================

				if(!isset($id_classe)) {
					echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre période</a>\n";
					echo "</p>\n";

					if ((($_SESSION['statut']=="cpe")&&(getSettingValue('GepiAccesAbsTouteClasseCpe')=='yes'))||($_SESSION['statut']!="cpe")) {
						$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  AND p.num_periode='$num_periode' ORDER BY classe;";
					} else {
						$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_cpe e, j_eleves_classes jc, periodes p WHERE (e.cpe_login = '".$_SESSION['login']."' AND jc.login = e.e_login AND c.id = jc.id_classe AND p.id_classe = c.id  AND p.num_periode='$num_periode')  ORDER BY classe;";
					}

					//echo "$sql<br />\n";

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

						echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
						echo "<td align='left'>\n";

							echo "<table border='0'>\n";
						$i=0;
						while ($lig_classe=mysql_fetch_object($res_classe)) {

							$id_classe=$lig_classe->id;
							$classe=$lig_classe->classe;

							if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
									echo "</table>\n";
								echo "</td>\n";
								//echo "<td style='padding: 0 10px 0 10px'>\n";
								echo "<td align='left'>\n";
									echo "<table border='0'>\n";
							}

							$sql="SELECT MAX(num_periode) AS max_per FROM periodes WHERE id_classe='$id_classe';";
							$test=mysql_query($sql);

							if(mysql_num_rows($test)==0){
								echo "<tr><td>&nbsp;</td><td>Classe: $classe (<i>pas de période?</i>)</td></tr>\n";
							}
							else{
								$lig_tmp=mysql_fetch_object($test);
								if($lig_tmp->max_per!=$max_per){
									echo "<tr><td>&nbsp;</td><td>Classe: $classe (<i>$lig_tmp->max_per périodes</i>)</td></tr>\n";
								}
								else{
									// Un compte secours peut saisir en période partiellement close
									if($_SESSION['statut']=='secours') {
										$sql="SELECT verouiller FROM periodes WHERE (verouiller='N' OR verouiller='P') AND id_classe='$id_classe' AND num_periode='$num_periode';";
									}
									else {
										$sql="SELECT verouiller FROM periodes WHERE verouiller='N' AND id_classe='$id_classe' AND num_periode='$num_periode';";
									}
									$test=mysql_query($sql);
									if(mysql_num_rows($test)==0){
										echo "<tr><td>&nbsp;</td><td>Classe: $classe (<i>période close</i>)</td></tr>\n";
									}
									else{
										echo "<tr>\n";
										echo "<td>\n";
										echo "<input type='checkbox' name='id_classe[]' id='case_".$i."' value='$id_classe' />";
										echo "</td>\n";
										echo "<td>\n";
										echo "<label for='case_".$i."' style='cursor: pointer;'>Classe : $classe</label><br />\n";
									}
								}
							}


							//echo "<span class = \"norme\"><input type='checkbox' name='$temp' value='yes' onclick=\"verif1()\" />";
							//echo "Classe : $classe </span><br />\n";
							$i++;
						}
							echo "</table>\n";
						echo "</td>\n";
						echo "</tr>\n";
						echo "</table>\n";
						echo "<p><input type='submit' value='Valider' /></p>\n";
						echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";

						echo "<p><a href='#' onClick='Coche_ou_pas(true); return false;'>Tout cocher</a> / <a href='#' onClick='Coche_ou_pas(false); return false;'>Tout décocher</a></p>\n";

						echo "</form>\n";


						echo "<p><i>NOTE:</i> Seules les absences des classes cochées seront importées (<i>même si l'ExportSconet contient les absences de toutes les classes</i>).</p>\n";


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
				else {

						if(!isset($_POST['is_posted'])) {
							$etape=1;

							//echo "<p>Cette page permet de remplir des tableaux PHP avec les informations élèves, responsables,...<br />\n";
							echo "<p>Cette page permet de remplir des tables temporaires avec les informations extraites du XML.<br />\n";
							echo "</p>\n";
							echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
							echo add_token_field();

							echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";

							// Il faudrait ajouter un test ici... on pourrait injecter une classe pour laquelle la période $num_periode est close.
							if(is_array($id_classe)){
								for($i=0;$i<count($id_classe);$i++){
									echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
								}
							}
							else{
								echo "<input type='hidden' name='id_classe[]' value='$id_classe' />\n";
							}

							echo "<p>Veuillez fournir le fichier exportAbsence.xml:<br />\n";
							echo "<input type=\"file\" size=\"60\" name=\"absences_xml_file\" /><br />\n";
							echo "<input type='hidden' name='etape' value='$etape' />\n";
							echo "<input type='hidden' name='is_posted' value='yes' />\n";
							echo "</p>\n";

							echo "<p><input type='submit' value='Valider' /></p>\n";
							echo "</form>\n";

							echo "<p><b>ATTENTION</b>&nbsp;:</p>\n";
							echo "<ul>\n";
							echo "<li><p>Fournir un export d'une seule période.<br />Si plusieurs périodes sont cochées lors de votre export, les informations importées risquent d'être erronées.</p></li>\n";
							echo "<li><p>Pour récupérer l'export de Sconet:<br />\n";
							echo "Sur le menu de gauche :<br />\n";
							echo "IMPORT/EXPORT -> Export Absences et Retard -> Sélectionner la période (T1, T2, T3)<br />\n";
							echo "Puis cliquer sur le bouton 'Exporter les périodes sélectionnées'.</p>\n";
							echo "</ul>\n";
						}
						else {
							check_token();

							$post_max_size=ini_get('post_max_size');
							$upload_max_filesize=ini_get('upload_max_filesize');
							$max_execution_time=ini_get('max_execution_time');
							$memory_limit=ini_get('memory_limit');


							if($etape==1) {
								$xml_file = isset($_FILES["absences_xml_file"]) ? $_FILES["absences_xml_file"] : NULL;

								$sconet_xml=simplexml_load_file($xml_file['tmp_name']);
								if(!$sconet_xml) {
									echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
									require("../lib/footer.inc.php");
									die();
								}
				
								$nom_racine=$sconet_xml->getName();
								if(my_strtolower($nom_racine)!='exportabsence') {
									echo "<p style='color:red;'><b>ERREUR&nbsp;:</b> Le fichier XML fourni n'a pas l'air d'être un fichier XML <b>exportAbsence.xml</b></p>\n";
									require("../lib/footer.inc.php");
									die();
								}

								$eleves=array();
								$i=0;
								foreach($sconet_xml->eleve as $objet_eleve) {
									//echo "<p>Elève $i<br />";
									foreach($objet_eleve->attributes() as $key => $value) {
										//$eleves[$i][my_strtolower($key)]=trim($value);
										$eleves[$i][trim($key)]=trim($value);
										//echo "$key : $value<br />";
									}
									//echo "<br />";
									$i++;
								}

								// Menage:
								$sql="DELETE FROM temp_abs_import WHERE cpe_login='".$_SESSION['login']."';";
								//echo "$sql<br />";
								$res=mysql_query($sql);
				
								echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
								// On a fait en sorte à l'étape précédente, qu'il n'y ait qu'une classe ou plusieurs, que l'on transmette un tableau id_classe[]
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
				
									$ligne_tableau="";
									$affiche_ligne="n";
				
									if(isset($eleves[$i]['elenoet'])){
										// Est-ce que l'élève fait bien partie d'une des classes importées pour la période importée?
										$sql="SELECT 1=1 FROM j_eleves_classes jec, eleves e WHERE jec.login=e.login AND (e.elenoet='".$eleves[$i]['elenoet']."' OR e.elenoet='0".$eleves[$i]['elenoet']."') AND periode='$num_periode' AND $chaine_liste_classes;";
										//echo "<!--\n$sql\n-->\n";
										//echo "$sql<br />\n";
										$test=mysql_query($sql);
				
										if(mysql_num_rows($test)>0){
				
											$alt=$alt*(-1);
											$ligne_tableau.="<tr class='lig$alt white_hover'>\n";
											$ligne_tableau.="<td>$i</td>\n";
											$ligne_tableau.="<td>".$eleves[$i]['elenoet']."</td>\n";
				
											// Récupération des infos sur l'élève (on a au moins besoin du login pour tester si le CPE a cet élève.
											$sql="SELECT e.login,e.nom,e.prenom,e.elenoet
														FROM eleves e
														WHERE (e.elenoet='".$eleves[$i]['elenoet']."' OR e.elenoet='0".$eleves[$i]['elenoet']."')";
											//echo "<!--\n$sql\n-->\n";
											$res1=mysql_query($sql);
											if(mysql_num_rows($res1)==0){
												$ligne_tableau.="<td style='color:red;' colspan='3'>Elève absent de votre table 'eleves'???</td>\n";
												$nb_err++;
											}
											elseif(mysql_num_rows($res1)>1){
												$ligne_tableau.="<td style='color:red;' colspan='3'>Plus d'un élève correspond à cet ELENOET ???</td>\n";
												$nb_err++;
											}
											else{
				
												$lig1=mysql_fetch_object($res1);

												$acces_a_cet_eleve="y";
												if (($_SESSION['statut']=="cpe")&&(getSettingValue('GepiAccesAbsTouteClasseCpe')!='yes')) {
													// Le CPE a-t-il bien cet élève:
													$sql="SELECT 1=1 FROM j_eleves_cpe jec WHERE jec.e_login='$lig1->login' AND jec.cpe_login='".$_SESSION['login']."'";
													//echo "<!--\n$sql\n-->\n";
													$test=mysql_query($sql);

													if((mysql_num_rows($test)==0)) {
														$acces_a_cet_eleve="n";
													}
												}

												//if((mysql_num_rows($test)>0)||($_SESSION['statut']=='secours')) {
												if($acces_a_cet_eleve=="y") {
													$affiche_ligne="y";
				
													$ligne_tableau.="<td>";
													//$ligne_tableau.="<input type='hidden' name='log_eleve[$i]' value='$lig1->login' />\n";
													$ligne_tableau.="$lig1->nom</td>\n";
													$ligne_tableau.="<td>$lig1->prenom</td>\n";
				
													$ligne_tableau.="<td>\n";
													$sql="SELECT c.classe FROM j_eleves_classes jec, classes c
															WHERE jec.login='$lig1->login' AND
																jec.id_classe=c.id AND periode='$num_periode'";
													$res2=mysql_query($sql);
													if(mysql_num_rows($res2)==0){
														$ligne_tableau.="<span style='color:red;'>NA</span>\n";
													}
													else {
														$cpt=0;
														while($lig2=mysql_fetch_object($res2)){
															if($cpt>0){
																$ligne_tableau.=", ";
															}
															$ligne_tableau.=$lig2->classe;
														}
													}
													$ligne_tableau.="</td>\n";
												}
				
				
												if("$affiche_ligne"=="y"){
													echo $ligne_tableau;
													echo "<td>\n";
				
													if((isset($eleves[$i]['elenoet']))&&(isset($eleves[$i]['nbAbs']))&&(isset($eleves[$i]['nbNonJustif']))&&(isset($eleves[$i]['nbRet']))) {
														// Les absences de l'élève ont pu être importées par un autre cpe sans que l'opération soit menée à bout.
														$sql="DELETE FROM temp_abs_import WHERE login='$lig1->login';";
														$menage=mysql_query($sql);

														$sql="INSERT INTO temp_abs_import SET login='$lig1->login',
																							cpe_login='".$_SESSION['login']."',
																							elenoet='".$eleves[$i]['elenoet']."',
																							nbAbs='".$eleves[$i]['nbAbs']."',
																							nbNonJustif='".$eleves[$i]['nbNonJustif']."',
																							nbRet='".$eleves[$i]['nbRet']."';";
														//echo "$sql<br />";
														$insert=mysql_query($sql);
														if(!$insert) {
															echo "<span style='color:red;'>Erreur&nbsp;: $sql</span><br />\n";
														}
													}
				
													if(isset($eleves[$i]['nbAbs'])){
														echo $eleves[$i]['nbAbs'];
														//echo "<input type='hidden' name='nbabs_eleve[$i]' value='".$eleves[$i]['nbAbs']."' />\n";
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
														//echo "<input type='hidden' name='nbnj_eleve[$i]' value='".$eleves[$i]['nbNonJustif']."' />\n";
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
														//echo "<input type='hidden' size='4' name='nbret_eleve[$i]' value='".$eleves[$i]['nbRet']."' />\n";
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
									}
								}
								echo "</table>\n";
								echo add_token_field();
								//echo "<input type='hidden' name='nb_eleves' value='$i' />\n";
								echo "<input type='hidden' name='is_posted' value='y' />\n";
								echo "<input type='hidden' name='etape' value='2' />\n";

								# A RENSEIGNER D'APRES L'EXTRACTION:
								echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";

								echo "<p align='center'><input type='submit' value='Importer' /></p>\n";
								echo "</form>\n";

								echo "<p><i>NOTE:</i> Si des lignes sont marquées d'un <span style='color:red;'>ERR</span>, les valeurs ne seront pas importées pour cet(s) élève(s).</p>\n";
								
								echo "<script type='text/javascript'>
									alert('ATTENTION : Rien n\'est encore enregistré. Vous devez confirmer l\'importation en bas de page.');
								</script>\n";
							}
							if($etape==2) {

								// Pour ne retenir que les saisies du cpe courant (dans de gros etab, il se peut que plusieurs cpe gerent differentes classes,...)
								$sql="SELECT * FROM temp_abs_import WHERE cpe_login='".$_SESSION['login']."';";
								$res_t_a_i=mysql_query($sql);
								if(mysql_num_rows($res_t_a_i)==0) {
									echo "<p style='color:red'>Aucune absence, retard,... n'ont été trouvés&nbsp;???</p>\n";
									echo "<p><a href='".$_SERVER['PHP_SELF']."'>Recommencer</a></p>\n";
									require("../lib/footer.inc.php");
									die();
								}
					
								$log_eleve=array();
								$nbabs_eleve=array();
								$nbnj_eleve=array();
								$nbret_eleve=array();
								while($lig_abs=mysql_fetch_object($res_t_a_i)) {
									$log_eleve[]=$lig_abs->login;
									$nbabs_eleve[]=$lig_abs->nbAbs;
									$nbnj_eleve[]=$lig_abs->nbNonJustif;
									$nbret_eleve[]=$lig_abs->nbRet;
								}
								$nb_eleves=count($log_eleve);

								// On initialise à zéro les absences, retards,... pour tous les élèves des classes importées et les valeurs extraites du XML de Sconet écraseront ces initialisations.
								// Si on ne fait pas cette initialisation, les élèves qui n'ont aucune absence ni retard apparaissent avec un '?' au lieu d'un Zéro/Aucune.
								for($i=0;$i<count($id_classe);$i++){

									// Ajout d'un test sur le caractère clos de la période pour la classe
									if($_SESSION['statut']=='secours'){
										$sql="SELECT 1=1 FROM periodes WHERE id_classe='$id_classe[$i]' AND num_periode='$num_periode' AND (verouiller='N' OR verouiller='P');";
									}
									else {
										$sql="SELECT 1=1 FROM periodes WHERE id_classe='$id_classe[$i]' AND num_periode='$num_periode' AND verouiller='N';";
									}
									$test_ver=mysql_query($sql);

									if(mysql_num_rows($test_ver)>0) {
										if((($_SESSION['statut']=="cpe")&&(getSettingValue('GepiAccesAbsTouteClasseCpe')=='yes'))||($_SESSION['statut']=='secours')) {
											$sql="SELECT login FROM j_eleves_classes WHERE id_classe='$id_classe[$i]' AND periode='$num_periode';";
										}
										else{
											// Pour ne réinitialiser que les absences des élèves associés au CPE:
											$sql="SELECT jecl.login FROM j_eleves_classes jecl, j_eleves_cpe jec WHERE jecl.id_classe='$id_classe[$i]' AND jecl.periode='$num_periode' AND jecl.login=jec.e_login AND jec.cpe_login='".$_SESSION['login']."';";
										}
	
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
								}


								$nb_err=0;
								$nb_ok=0;
								echo "<p>Importation&nbsp;: ";
								//for($i=0;$i<count($log_eleve);$i++){
								for($i=0;$i<$nb_eleves;$i++){
									if((isset($log_eleve[$i]))&&
										(isset($nbabs_eleve[$i]))&&
										(isset($nbnj_eleve[$i]))&&
										(isset($nbret_eleve[$i]))
									) {

										if($_SESSION['statut']=='secours'){
											$test0=true;

											// Requête pour tester que la période est bien close ou partiellement close pour cette classe
											$sql="SELECT 1=1 FROM periodes p,j_eleves_classes jec WHERE p.num_periode='$num_periode' AND (p.verouiller='N' OR p.verouiller='P') AND jec.login='$log_eleve[$i]' AND p.id_classe=jec.id_classe AND p.num_periode=jec.periode;";
										}
										else {
											$test0=true;
											if (($_SESSION['statut']=="cpe")&&(getSettingValue('GepiAccesAbsTouteClasseCpe')!='yes')) {
												// L'élève est-il associé au CPE:
												// Il faudrait vraiment une tentative frauduleuse pour que ce ne soit pas le cas...
												$sql="SELECT 1=1 FROM j_eleves_cpe jec WHERE jec.e_login='".$log_eleve[$i]."' AND jec.cpe_login='".$_SESSION['login']."';";
												$res_test0=mysql_query($sql);
												if(mysql_num_rows($res_test0)!=0){
													$test0=true;
												}
												else{
													$test0=false;
												}
											}

											// Requête pour tester que la période est bien close pour cette classe
											$sql="SELECT 1=1 FROM periodes p,j_eleves_classes jec WHERE p.num_periode='$num_periode' AND p.verouiller='N' AND jec.login='$log_eleve[$i]' AND p.id_classe=jec.id_classe AND p.num_periode=jec.periode;";
										}
										//echo "$sql<br />";
										$test2=mysql_query($sql);

										//if($test0==true){
										if(($test0==true)&&(mysql_num_rows($test2)>0)) {
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
										else {
											if(!isset($liste_non_importes)) {
												$liste_non_importes=$log_eleve[$i];
											}
											else {
												$liste_non_importes.=", $log_eleve[$i]";
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

								if(isset($liste_non_importes)) {
									echo "<p>Aucune importation n'a été effectuée pour <span style='color:red'>$liste_non_importes</span>.<br />La période était peut-être close pour ces élèves.</p>\n";
									echo "<p><br /></p>\n";
								}

								echo "<p>Contrôler les saisies pour la classe de&nbsp;:</p>\n";

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

									echo "<a href='saisie_absences.php?id_classe=$id_classe[$i]&amp;periode_num=$num_periode' target='_blank'>".get_class_from_id($id_classe[$i])."</a><br />\n";
								}
								echo "</td>\n";
								echo "</tr>\n";
								echo "</table>\n";

							}
						} // Fin is_posted
					//}
				}
			}
			echo "<p><br /></p>\n";

		?>
	</div>
<?php
	require("../lib/footer.inc.php");
?>
