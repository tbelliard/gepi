<?php
/*
 *
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions complémentaires et/ou librairies utiles

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/statistiques/export_donnees_bulletins.php';";
$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/statistiques/export_donnees_bulletins.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Export de données des bulletins',
statut='';";
$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}


$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

$choix_periodes=isset($_POST['choix_periodes']) ? $_POST['choix_periodes'] : NULL;
//num_periode_".$id_classe[$i]."[] à récupérer
$max_per=isset($_POST['max_per']) ? $_POST['max_per'] : NULL;

$choix_matieres=isset($_POST['choix_matieres']) ? $_POST['choix_matieres'] : NULL;
//id_groupe_".$id_classe[$i]."[] à récupérer

$choix_eleves=isset($_POST['choix_eleves']) ? $_POST['choix_eleves'] : NULL;
//login_eleve_".$id_classe[$i]."[] à récupérer

$choix_donnees=isset($_POST['choix_donnees']) ? $_POST['choix_donnees'] : NULL;
$champ_eleve=isset($_POST['champ_eleve']) ? $_POST['champ_eleve'] : NULL;
$champ_enseignant=isset($_POST['champ_enseignant']) ? $_POST['champ_enseignant'] : NULL;
$champ_autre=isset($_POST['champ_autre']) ? $_POST['champ_autre'] : NULL;

function clean_string_csv($texte) {
	// Pour remplacer les ; par ., et les " par '' et virer les retours à la ligne
	$texte=my_ereg_replace(";",".,",$texte);
	$texte=my_ereg_replace('"',"''",$texte);
	$texte=my_ereg_replace('\\\r\\\n','',$texte);
	return $texte;
}

if((isset($id_classe))&&
(isset($choix_periodes))&&
(isset($choix_matieres))&&
(isset($choix_eleves))&&
(isset($choix_donnees))&&
(isset($champ_eleve))
) {
	//debug_var();
	//$csv="";
	$csv='"ID_ELEVE"';
	for($loop=0;$loop<count($champ_eleve);$loop++) {
		// Pour éviter les colonnes app et note ici
		if(($champ_eleve[$loop]!='note')&&($champ_eleve[$loop]!='app')) {
			$csv.=';"'.strtoupper($champ_eleve[$loop]).'"';
		}
	}

	if(in_array("id_classe",$champ_autre)) {$csv.=';"ID_CLASSE"';}
	if(in_array("classe",$champ_autre)) {
		$csv.=';"NOM_DE_CLASSE"';
	}

	if(in_array("id_groupe",$champ_autre)) {$csv.=';"ID_GROUPE"';}
	if(in_array("nom_groupe",$champ_autre)) {$csv.=';"NOM_DE_GROUPE"';}

	if(in_array("matiere",$champ_autre)) {$csv.=';"NOM_COURT_DE_MATIERE"';}
	if(in_array("matiere_nom_complet",$champ_autre)) {$csv.=';"NOM_LONG_DE_MATIERE"';}

	for($loop=0;$loop<count($champ_enseignant);$loop++) {
		$csv.=';"'.strtoupper($champ_enseignant[$loop]).'"';
	}

	$csv.=';"NUMERO_DE_PERIODE"';
	$csv.=';"NOM_DE_PERIODE"';

	if(in_array('note',$champ_eleve)) {$csv.=';"NOTE_BULLETIN"';}
	if(in_array('app',$champ_eleve)) {$csv.=';"APPRECIATION_BULLETIN"';}

	$csv.="\r\n";



	$tab_id_csv_eleve=array();
	$cpt=0;

	// BOUCLE CLASSES
	for($i=0;$i<count($id_classe);$i++) {

		$tab_per=array();
		$temoin_periode="y";
		if($choix_periodes=='certaines') {
			// TESTER si il y a au moins une période sélectionnée
			if(isset($_POST['num_periode_'.$id_classe[$i]])) {
				$tmp_per=$_POST['num_periode_'.$id_classe[$i]];
				for($loop=0;$loop<count($tmp_per);$loop++) {
					$tab_per[]=$tmp_per[$loop];
				}
			}
			else {
				// La classe a été sélectionnée, mais n'est associée à aucune période???
				$temoin_periode="n";
			}
		}
		else {
			// On boucle sur les périodes jusqu'à $max_per
			for($loop=1;$loop<=$max_per;$loop++) {
				$tab_per[]=$loop;
			}
		}

		if($temoin_periode=="y") {
			$temoin_ele="y";
			$tab_ele=array();
			$tab_info_ele=array();
			// STOCKER TABLEAU DES ELEVES SI certains SEULEMENT
			if($choix_eleves=='certains') {
				if(isset($_POST['login_eleve_'.$id_classe[$i]])) {
					$tmp_ele=$_POST['login_eleve_'.$id_classe[$i]];
					for($loop=0;$loop<count($tmp_ele);$loop++) {
						// On contrôle que l'élève existe dans la table eleves
						$sql="SELECT * FROM eleves WHERE login='$tmp_ele[$loop]';";
						$res_ele_info=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_ele_info)>0) {
							$tab_ele[]=$tmp_ele[$loop];

							$tab_info_ele[$tmp_ele[$loop]]=array();
							$tab_info_ele[$tmp_ele[$loop]]=mysqli_fetch_assoc($res_ele_info);

							if(!isset($tab_id_csv_eleve[$tmp_ele[$loop]])) {
								$tab_id_csv_eleve[$tmp_ele[$loop]]=$cpt;
								$cpt++;
							}
						}
					}
				}
				else {
					// La classe a été sélectionnée, mais n'est associée à aucune période???
					$temoin_ele="n";
				}
	
			}
			else {
				$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe[$i]';";
				$res_ele_clas=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_ele_clas)>0) {
					while($lig_ele=mysqli_fetch_object($res_ele_clas)) {
						// On contrôle que l'élève existe dans la table eleves
						$sql="SELECT * FROM eleves WHERE login='$lig_ele->login';";
						$res_ele_info=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_ele_clas)>0) {
							$tab_ele[]=$lig_ele->login;

							$tab_info_ele[$lig_ele->login]=array();
							$tab_info_ele[$lig_ele->login]=mysqli_fetch_assoc($res_ele_info);

							if(!isset($tab_id_csv_eleve[$lig_ele->login])) {
								$tab_id_csv_eleve[$lig_ele->login]=$cpt;
								$cpt++;
							}
						}
					}
				}
			}

			if($temoin_ele=="y") {
				// BOUCLE PERIODES
				for($j=0;$j<count($tab_per);$j++) {
					// BOUCLE ENSEIGNEMENTS
					$tab_grp=array();
					$tab_id_groupe=array();
					$temoin_grp="y";
					if($choix_matieres=='certaines') {
						// TESTER si il y a au moins une matière sélectionnée
						if(isset($_POST['id_groupe_'.$id_classe[$i]])) {
							$tmp_grp=$_POST['id_groupe_'.$id_classe[$i]];
							for($loop=0;$loop<count($tmp_grp);$loop++) {
								$tab_id_groupe[]=$tmp_grp[$loop];

								$tab_grp[$tmp_grp[$loop]]=get_group($tmp_grp[$loop]);
							}
						}
						else {
							// La classe a été sélectionnée, mais n'est associée à aucune période???
							$temoin_grp="n";
						}
					}
					else {
						$sql="SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe[$i]';";
						$res_grp=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_grp)==0) {
							$temoin_grp="n";
						}
						else {
							while($lig_grp=mysqli_fetch_object($res_grp)) {
								$tab_id_groupe[]=$lig_grp->id_groupe;

								$tab_grp[$lig_grp->id_groupe]=get_group($lig_grp->id_groupe);
							}
						}
					}

					if($temoin_grp=="y") {
						// EXTRAIRE DONNNES BULLETINS
						for($k=0;$k<count($tab_id_groupe);$k++) {
	
							$tab_ele_note_grp=array();
							if(in_array('note',$champ_eleve)) {
								$sql="SELECT * FROM matieres_notes WHERE id_groupe='$tab_id_groupe[$k]' AND periode='$tab_per[$j]' ORDER BY login;";
								$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
								if(mysqli_num_rows($res_note)>0) {
									while($lig_note=mysqli_fetch_object($res_note)) {
		
										for($m=0;$m<count($tab_ele);$m++) {
											// On contrôle si l'élève est dans la classe pour la période
											$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$tab_ele[$m]' AND periode='$tab_per[$j]';";
											$test_ele_clas_per=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
											if(mysqli_num_rows($test_ele_clas_per)>0) {
												if(in_array($lig_note->login,$tab_ele)) {
													$tab_ele_note_grp[$lig_note->login]=$lig_note->note;
												}
											}
										}
									}
								}
							}
	
							$tab_ele_app_grp=array();
							if(in_array('app',$champ_eleve)) {
								$sql="SELECT * FROM matieres_appreciations WHERE id_groupe='$tab_id_groupe[$k]' ORDER BY login;";
								$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
								if(mysqli_num_rows($res_app)>0) {
									while($lig_app=mysqli_fetch_object($res_app)) {
		
										for($m=0;$m<count($tab_ele);$m++) {
											// On contrôle si l'élève est dans la classe pour la période
											$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$tab_ele[$m]' AND periode='$tab_per[$j]';";
											$test_ele_clas_per=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
											if(mysqli_num_rows($test_ele_clas_per)>0) {
												if(in_array($lig_app->login,$tab_ele)) {
													$tab_ele_app_grp[$lig_app->login]=$lig_app->appreciation;
												}
											}
										}
									}
								}
							}
	
							for($m=0;$m<count($tab_ele);$m++) {
								// On contrôle si l'élève est dans la classe pour la période
								$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$tab_ele[$m]' AND periode='$tab_per[$j]';";
								$test_ele_clas_per=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
								if(mysqli_num_rows($test_ele_clas_per)>0) {

									// DONNEES PROFS -> STOCKER DANS TABLEAU
									if(!isset($tab_prof[$tab_id_groupe[$k]])) {
										$tab_prof[$tab_id_groupe[$k]]=array();
										// PROBLEME: On ne récupère qu'un seul prof si on fait un unique fichier CSV monolithique
										$sql="SELECT u.* FROM utilisateurs u, j_groupes_professeurs jgp WHERE u.login=jgp.login AND jgp.id_groupe='$tab_id_groupe[$k]';";
										$res_prof_grp=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
										if(mysqli_num_rows($res_prof_grp)>0) {
											$cpt_prof=0;
											while($lig_prof_grp=mysqli_fetch_object($res_prof_grp)) {
												$tab_prof[$tab_id_groupe[$k]][$cpt_prof]=array();
												$tab_prof[$tab_id_groupe[$k]][$cpt_prof]['login']=$lig_prof_grp->login;
												$tab_prof[$tab_id_groupe[$k]][$cpt_prof]['nom']=$lig_prof_grp->nom;
												$tab_prof[$tab_id_groupe[$k]][$cpt_prof]['prenom']=$lig_prof_grp->prenom;
												$tab_prof[$tab_id_groupe[$k]][$cpt_prof]['civilite']=$lig_prof_grp->civilite;
												$cpt_prof++;
											}
										}
									}

									$csv.='"'.$tab_id_csv_eleve[$tab_ele[$m]].'"';
									for($loop=0;$loop<count($champ_eleve);$loop++) {
										// Pour éviter les colonnes app et note ici
										if(isset($tab_info_ele[$tab_ele[$m]][$champ_eleve[$loop]])) {
											$csv.=';"'.$tab_info_ele[$tab_ele[$m]][$champ_eleve[$loop]].'"';
										}
									}

									if(in_array("id_classe",$champ_autre)) {$csv.=';"'.$id_classe[$i].'"';}
									if(in_array("classe",$champ_autre)) {
										if(!isset($tab_classe[$id_classe[$i]])) {
											$tab_classe[$id_classe[$i]]=clean_string_csv(get_class_from_id($id_classe[$i]));
										}
										$csv.=';"'.$tab_classe[$id_classe[$i]].'"';
									}

									if(in_array("id_groupe",$champ_autre)) {$csv.=';"'.$tab_id_groupe[$k].'"';}
									if(in_array("nom_groupe",$champ_autre)) {$csv.=';"'.$tab_grp[$tab_id_groupe[$k]]['name'].'"';}
		
									if(in_array("matiere",$champ_autre)) {$csv.=';"'.$tab_grp[$tab_id_groupe[$k]]["matiere"]["matiere"].'"';}
									if(in_array("matiere_nom_complet",$champ_autre)) {$csv.=';"'.clean_string_csv($tab_grp[$tab_id_groupe[$k]]["matiere"]["nom_complet"]).'"';}
		
									for($loop=0;$loop<count($champ_enseignant);$loop++) {
										$csv.=';"'.$tab_prof[$tab_id_groupe[$k]][0][$champ_enseignant[$loop]].'"';
									}
		
									$csv.=';"'.$tab_per[$j].'"';
									//if(!isset($tab_nom_per[$id_classe[$i]][$tab_per[$j]])) {
									if(!isset($tab_nom_per[$id_classe[$i]])) {
										$sql="SELECT * FROM periodes WHERE id_classe='$id_classe[$i]' ORDER BY num_periode;";
										$res_nom_per=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
										if(mysqli_num_rows($res_nom_per)>0) {
											while($lig_nom_per=mysqli_fetch_object($res_nom_per)) {
												$tab_nom_per[$id_classe[$i]][$lig_nom_per->num_periode]=$lig_nom_per->nom_periode;
											}
										}
									}
									$csv.=';"'.$tab_nom_per[$id_classe[$i]][$tab_per[$j]].'"';
		
		
									if(in_array('note',$champ_eleve)) {
										$csv.=';"';
										// Si on fait l'export avant que les bulletins ne soient remplis, on ne récupère rien:
										if(isset($tab_ele_note_grp[$tab_ele[$m]])) {$csv.=$tab_ele_note_grp[$tab_ele[$m]];}
										$csv.='"';
									}
		
									if(in_array('app',$champ_eleve)) {
										$csv.=';"';
										// Si on fait l'export avant que les bulletins ne soient remplis, on ne récupère rien:
										//echo "\$tab_ele[$m]=$tab_ele[$m]<br />";
										//echo "\$tab_ele_app_grp[$tab_ele[$m]]=".$tab_ele_app_grp[$tab_ele[$m]]."<br />";
										if(isset($tab_ele_app_grp[$tab_ele[$m]])) {$csv.=clean_string_csv($tab_ele_app_grp[$tab_ele[$m]]);}
										$csv.='"';
									}
		
									// PROPOSER: avec ou sans nom de classe...
									//CSV: id_csv_eleve;CHAMPS_ELEVE;id_classe;classe;id_groupe;nom_grp;matiere;matiere_nom_complet;CHAMPS_PROF;num_periode;nom_periode;note;appreciation
		
									$csv.="\r\n";
								}
							}
						}
					}
				}
			}

		}
	}
	// AU CAS OU ON NE VEUT PAS LES IDENTIFIANTS ELEVES, CREER SYSTEMATIQUEMENT UN IDENTIFIANT TEMPORAIRE ASSOCIé A UN LOGIN ELEVE
	// $tab_id_csv_eleve[login]=id_temp

	$nom_fic = "export_donnees_bulletins_".date("Ymd_His").".csv";
	send_file_download_headers('text/x-csv',$nom_fic);
	//echo $csv;
	echo echo_csv_encoded($csv);
	die();
}



// ======================== CSS et js particuliers ========================
$utilisation_win = "non";
$utilisation_jsdivdrag = "non";
//$javascript_specifique = ".js";
//$style_specifique = ".css";

// ===================== entete Gepi ======================================//
$titre_page = "Export de données des bulletins";
require_once("../lib/header.inc.php");
// ===================== fin entete =======================================//

//debug_var();

//echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if(!isset($id_classe)) {
	echo "</p>\n";

	echo "<p class='bold'>Choix des classes&nbsp;:</p>\n";

	// Liste des classes avec élève:
	$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) ORDER BY c.classe;";
	$call_classes=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

	$nb_classes=mysqli_num_rows($call_classes);
	if($nb_classes==0){
		echo "<p>Aucune classe avec élève affecté n'a été trouvée.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	// Affichage sur 3 colonnes
	$nb_classes_par_colonne=round($nb_classes/3);

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

		echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='id_classe[]' id='tab_id_classe_$cpt' value='$lig_clas->id' onchange='change_style_classe($cpt)' /> $lig_clas->classe</label>";
		echo "<br />\n";
		$cpt++;
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
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
			}
			else {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";
}
elseif(!isset($choix_periodes)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix des classes</a>";
	echo "</p>\n";

	echo "<p class='bold'>Choix des périodes&nbsp;:</p>\n";

	//echo "<p style='color:red;'>A FAIRE: afficher les périodes closes...</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

	echo "<ul style='list-style-type: none;'>\n";
	echo "<li>\n";
	echo "<input type='radio' name='choix_periodes' id='choix_periodes_toutes' value='toutes' onchange='display_div_liste_periodes()' checked /><label for='choix_periodes_toutes'> Toutes les périodes</label>\n";
	echo "</li>\n";
	echo "<li>\n";
	echo "<input type='radio' name='choix_periodes' id='choix_periodes_certaines' onchange='display_div_liste_periodes()' value='certaines' /><label for='choix_periodes_certaines'> Certaines périodes seulement</label>\n";

	echo "<div id='div_liste_periodes' style='margin-left: 2em;'>\n";

	echo "<div id='div_coche_lot' style='float: right; width: 20em;'></div>\n";

	$max_per=0;
	$cpt=0;
	for($i=0;$i<count($id_classe);$i++) {
		$sql="SELECT * FROM periodes WHERE id_classe='".$id_classe[$i]."' ORDER BY num_periode;";
		//echo "$sql<br />";
		$call_per=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		$nombre_ligne=mysqli_num_rows($call_per);
		if($nombre_ligne==0) {
			echo "<p style='color:red;'>Aucune période  n'est définie dans la classe de ".get_class_from_id($id_classe[$i]).".</p>\n";
		}
		else {
			echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";

			$first_per[$id_classe[$i]]=$cpt;
			echo "<table class='boireaus' summary='Classe n°$id_classe[$i]'/>\n";
			echo "<tr>\n";
			echo "<th colspan='4'>\n";
			echo "Classe de ".get_class_from_id($id_classe[$i])."\n";
			echo "</th>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<th>\n";
			//echo "Cocher/décocher\n";
			echo "<p><a href='#' onClick='ModifCase(".$id_classe[$i].",true);return false;'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href='#' onClick='ModifCase(".$id_classe[$i].",false);return false;'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a></p>\n";
			echo "</th>\n";
			echo "<th>Num</th>\n";
			echo "<th>Période</th>\n";
			echo "<th>Etat</th>\n";
			echo "</tr>\n";

			$alt=1;
			while($lig_per=mysqli_fetch_object($call_per)) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='num_periode_".$id_classe[$i]."[]' id='num_periode_$cpt' value='$lig_per->num_periode' onchange='change_style_per($cpt)' checked />\n";
				echo "</td>\n";

				echo "<td>\n";
				if($lig_per->num_periode>$max_per) {$max_per=$lig_per->num_periode;}
				echo $lig_per->num_periode;
				echo "</td>\n";

				echo "<td style='text-align:left; font-weight: bold;'><label for='num_periode_$cpt' id='label_periode_$cpt'>$lig_per->nom_periode</label></td>\n";

				echo "<td style='text-align:center;'>";
				if($lig_per->verouiller=='O') {echo "Close";}
				elseif($lig_per->verouiller=='P') {echo "Partiellement close";}
				elseif($lig_per->verouiller=='N') {echo "Ouverte en saisie";}
				else {echo "???";}
				echo "</td>\n";

				echo "</tr>\n";
				$cpt++;
			}
			echo "</table>\n";
			$last_per[$id_classe[$i]]=$cpt;
		}
		echo "<br />\n";
	}

	echo "<p><a href='javascript:ModifToutesCases(true)'>Cocher</a> / <a href='javascript:ModifToutesCases(false)'>décocher</a>  toutes les périodes</p>\n";

	echo "</div>\n";

	echo "</li>\n";

	echo "</ul>\n";

	echo "<input type='hidden' name='max_per' value='$max_per' />\n";
	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	$chaine_div_coche_lot="Pour toutes les classes,<br />";
	for($j=1;$j<=$max_per;$j++) {
		$chaine_div_coche_lot.="<a href='javascript:coche_lot($j,true)'>Cocher</a> / <a href='javascript:coche_lot($j,false)'>décocher</a> la période $j<br />";
	}
	$chaine_div_coche_lot.="<a href='javascript:ModifToutesCases(true)'>Cocher</a> / <a href='javascript:ModifToutesCases(false)'>décocher</a>  toutes les périodes";

	echo "<script type='text/javascript'>
	document.getElementById('div_liste_periodes').style.display='none';

	function display_div_liste_periodes() {
		if(document.getElementById('choix_periodes_certaines').checked==true) {
			document.getElementById('div_liste_periodes').style.display='block';
		}
		else {
			document.getElementById('div_liste_periodes').style.display='none';
		}
	}

	if(document.getElementById('div_coche_lot')) {
		document.getElementById('div_coche_lot').innerHTML=\"$chaine_div_coche_lot\";
	}

	function coche_lot(num,mode) {";

	for($i=0;$i<count($id_classe);$i++) {
		echo "for(k=0;k<$cpt;k++) {
	if(document.getElementById('num_periode_'+k)) {
		if(document.getElementById('num_periode_'+k).value==num) {
			document.getElementById('num_periode_'+k).checked = mode;
			change_style_per(k);
		}
	}
}\n";
	}

	echo "
	}

	function ModifToutesCases(mode) {
";
	for($i=0;$i<count($id_classe);$i++) {
		echo "		ModifCase(".$id_classe[$i].",mode);\n";
	}

	echo "	}

	function ModifCase(id_classe,mode) {
		var first_per=new Array();
		var last_per=new Array();\n";

	for($i=0;$i<count($id_classe);$i++) {
		echo "		first_per[".$id_classe[$i]."]=".$first_per[$id_classe[$i]].";
		last_per[".$id_classe[$i]."]=".$last_per[$id_classe[$i]].";\n";
	}

	echo "
		for (var k=first_per[id_classe];k<last_per[id_classe];k++) {
			if(document.getElementById('num_periode_'+k)){
				document.getElementById('num_periode_'+k).checked = mode;
				change_style_per(k);
			}
		}
	}

	function change_style_per(num) {
		if((document.getElementById('num_periode_'+num))&&(document.getElementById('label_periode_'+num))) {
			if(document.getElementById('num_periode_'+num).checked) {
				document.getElementById('label_periode_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_periode_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";

}
elseif(!isset($choix_matieres)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix des classes</a>";
	echo " | <a href='javascript: history.go(-1);'>Retour au choix des périodes</a>";
	echo "</p>\n";

	echo "<p class='bold'>Choix des matières/enseignements&nbsp;:</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	echo "<input type='hidden' name='choix_periodes' value='$choix_periodes' />\n";
	echo "<input type='hidden' name='max_per' value='$max_per' />\n";

	echo "<ul style='list-style-type: none;'>\n";
	echo "<li>\n";
	echo "<input type='radio' name='choix_matieres' id='choix_matieres_toutes' value='toutes' onchange='display_div_liste_enseignements()' checked /><label for='choix_matieres_toutes'> Tous les enseignements/matières</label>\n";
	echo "</li>\n";
	echo "<li>\n";
	echo "<input type='radio' name='choix_matieres' id='choix_matieres_certaines' onchange='display_div_liste_enseignements()' value='certaines' /><label for='choix_matieres_certaines'> Certains enseignements/matières seulement</label>\n";

	echo "<div id='div_liste_enseignements' style='margin-left: 2em;'>\n";

	echo "<div id='div_coche_lot' style='float: right; width: 20em;'></div>\n";

	$tab_id_matiere=array();
	$tab_liste_index_grp_matiere=array();
	$cpt=0;
	for($i=0;$i<count($id_classe);$i++) {
		//$sql="SELECT DISTINCT g.id, g.name, g.description FROM groupes g, j_groupes_classes jgc WHERE (g.id=jgc.id_groupe and jgc.id_classe='".$id_classe[$i]."') ORDER BY jgc.priorite, g.name";
		$sql="SELECT DISTINCT g.id, g.name, g.description, jgm.id_matiere FROM groupes g, j_groupes_classes jgc, j_groupes_matieres jgm WHERE (g.id=jgc.id_groupe AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='".$id_classe[$i]."') ORDER BY jgc.priorite, g.name";
		//echo "$sql<br />";
		$call_group = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		$nombre_ligne = mysqli_num_rows($call_group);
		if($nombre_ligne==0) {
			echo "<p style='color:red;'>Aucun enseignement n'est défini dans la classe de ".get_class_from_id($id_classe[$i]).".</p>\n";
		}
		else {

			$temoin_classe[$i]='y';
			if($choix_periodes=='certaines') {
				// =============
				// AJOUTER UN TEST... si on a choisi 'certaines' périodes, mais sans aucune période cochée
				// =============
				if(isset($_POST['num_periode_'.$id_classe[$i]])) {
					$tmp_per=$_POST['num_periode_'.$id_classe[$i]];
					for($loop=0;$loop<$max_per;$loop++) {
						if(isset($tmp_per[$loop])) {
							echo "<input type='hidden' name='num_periode_".$id_classe[$i]."[]' value='$tmp_per[$loop]' />\n";
						}
					}
				}
				else {
					$temoin_classe[$i]='n';
				}
			}

			if($temoin_classe[$i]=='y') {
				echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";

				$first_grp[$id_classe[$i]]=$cpt;
				echo "<table class='boireaus' summary='Classe n°$id_classe[$i]'/>\n";
				echo "<tr>\n";
				echo "<th colspan='3'>\n";
				echo "Classe de ".get_class_from_id($id_classe[$i])."\n";
				echo "</th>\n";
				echo "</tr>\n";
	
				echo "<tr>\n";
				echo "<th>\n";
				//echo "Cocher/décocher\n";
				echo "<p><a href='#' onClick='ModifCase(".$id_classe[$i].",true);return false;'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href='#' onClick='ModifCase(".$id_classe[$i].",false);return false;'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a></p>\n";
				echo "</th>\n";
				echo "<th>Enseignement</th>\n";
				echo "<th>Professeur</th>\n";
				echo "</tr>\n";

				$alt=1;
				while($lig_grp=mysqli_fetch_object($call_group)) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'>\n";
					echo "<td>\n";
					echo "<input type='checkbox' name='id_groupe_".$id_classe[$i]."[]' id='id_groupe_$cpt' value='$lig_grp->id' onchange='change_style_groupe($cpt)' checked />\n";
					echo "</td>\n";
					echo "<td style='text-align:left; font-weight: bold;'><label for='id_groupe_$cpt' id='label_groupe_$cpt'>$lig_grp->name (<i>$lig_grp->description</i>)</label></td>\n";
					echo "<td style='text-align:left;'>\n";
					$sql="SELECT DISTINCT nom,prenom,civilite FROM utilisateurs u, j_groupes_professeurs jgp WHERE u.login=jgp.login AND jgp.id_groupe='$lig_grp->id' ORDER BY u.nom, u.prenom;";
					$res_prof_grp=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res_prof_grp)>0) {
						$lig_prof_grp=mysqli_fetch_object($res_prof_grp);
						echo $lig_prof_grp->civilite." ".strtoupper($lig_prof_grp->nom)." ".casse_mot($lig_prof_grp->prenom,"majf2");
						while($lig_prof_grp=mysqli_fetch_object($res_prof_grp)) {
							echo ", ";
							echo $lig_prof_grp->civilite." ".strtoupper($lig_prof_grp->nom)." ".casse_mot($lig_prof_grp->prenom,"majf2");
						}
					}
					echo "</td>\n";
					echo "</tr>\n";

					$tab_liste_index_grp_matiere[$lig_grp->id_matiere][]=$cpt;
					if(!in_array($lig_grp->id_matiere, $tab_id_matiere)) {$tab_id_matiere[]=$lig_grp->id_matiere;}

					$cpt++;
				}
				echo "</table>\n";
				$last_grp[$id_classe[$i]]=$cpt;
			}
		}
		echo "<br />\n";
	}


	echo "<p><a href='javascript:ModifToutesCases(true)'>Cocher</a> / <a href='javascript:ModifToutesCases(false)'>décocher</a>  tous les enseignements</p>\n";


	echo "</div>\n";

	echo "</li>\n";

	echo "</ul>\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	$chaine_div_coche_lot="Pour toutes les classes,<br />";

	for($j=0;$j<count($tab_id_matiere);$j++) {
		$chaine_div_coche_lot.="<a href='javascript:coche_lot($j,true)'>Cocher</a> / <a href='javascript:coche_lot($j,false)'>décocher</a> $tab_id_matiere[$j]<br />";

		for($k=0;$k<count($tab_liste_index_grp_matiere[$tab_id_matiere[$j]]);$k++) {
			if(!isset($chaine_array_index[$j])) {
				//$chaine_array_index[$j]="tab_index_$j=new Array(";
				$chaine_array_index[$j]="tab_index[$j]=new Array(";
				//$chaine_array_index[$j].=$tab_liste_index_grp_matiere[$tab_id_matiere[$j]][$k];
				$chaine_array_index[$j].="'".$tab_liste_index_grp_matiere[$tab_id_matiere[$j]][$k]."'";
			}
			else {
				//$chaine_array_index[$j].=", ".$tab_liste_index_grp_matiere[$tab_id_matiere[$j]][$k];
				$chaine_array_index[$j].=", "."'".$tab_liste_index_grp_matiere[$tab_id_matiere[$j]][$k]."'";
			}
		}
		if(isset($chaine_array_index[$j])) {
			$chaine_array_index[$j].=");";
		}
	}
	$chaine_div_coche_lot.="<a href='javascript:ModifToutesCases(true)'>Cocher</a> / <a href='javascript:ModifToutesCases(false)'>décocher</a>  tous les enseignements";

	echo "<script type='text/javascript'>
	document.getElementById('div_liste_enseignements').style.display='none';

	function display_div_liste_enseignements() {
		if(document.getElementById('choix_matieres_certaines').checked==true) {
			document.getElementById('div_liste_enseignements').style.display='block';
		}
		else {
			document.getElementById('div_liste_enseignements').style.display='none';
		}
	}

	if(document.getElementById('div_coche_lot')) {
		document.getElementById('div_coche_lot').innerHTML=\"$chaine_div_coche_lot\";
	}

	function coche_lot(num,mode) {
		tab_index=new Array();
";

	for($j=0;$j<count($tab_id_matiere);$j++) {
		echo "		".$chaine_array_index[$j];
	}

	echo "
		tab=tab_index[num];
		for(k=0;k<tab.length;k++) {
			//alert('id_groupe_'+tab[k]);
			if(document.getElementById('id_groupe_'+tab[k])) {
				document.getElementById('id_groupe_'+tab[k]).checked = mode;
				change_style_groupe(tab[k]);
			}
		}
	}

	function ModifToutesCases(mode) {
";
	for($i=0;$i<count($id_classe);$i++) {
		if($temoin_classe[$i]=='y') {
			echo "		ModifCase(".$id_classe[$i].",mode);\n";
		}
	}

	echo "	}

	function ModifCase(id_classe,mode) {
		var first_grp=new Array();
		var last_grp=new Array();\n";

	for($i=0;$i<count($id_classe);$i++) {
		if($temoin_classe[$i]=='y') {
			echo "		first_grp[".$id_classe[$i]."]=".$first_grp[$id_classe[$i]].";
		last_grp[".$id_classe[$i]."]=".$last_grp[$id_classe[$i]].";\n";
		}
	}

	echo "
		for (var k=first_grp[id_classe];k<last_grp[id_classe];k++) {
			if(document.getElementById('id_groupe_'+k)){
				document.getElementById('id_groupe_'+k).checked = mode;
				change_style_groupe(k);
			}
		}
	}

	function change_style_groupe(num) {
		//if(document.getElementById('id_groupe_'+num)) {
		if((document.getElementById('id_groupe_'+num))&&(document.getElementById('label_groupe_'+num))) {
			if(document.getElementById('id_groupe_'+num).checked) {
				document.getElementById('label_groupe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_groupe_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";

}
elseif(!isset($choix_eleves)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix des classes</a>";
	echo " | <a href='javascript: history.go(-2);'>Retour au choix des périodes</a>";
	echo " | <a href='javascript: history.go(-1);'>Retour au choix des enseignements</a>";
	echo "</p>\n";

	echo "<p class='bold'>Choix des élèves&nbsp;:</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	echo "<input type='hidden' name='choix_periodes' value='$choix_periodes' />\n";
	echo "<input type='hidden' name='max_per' value='$max_per' />\n";
	echo "<input type='hidden' name='choix_matieres' value='$choix_matieres' />\n";

	echo "<ul style='list-style-type: none;'>\n";
	echo "<li>\n";
	echo "<input type='radio' name='choix_eleves' id='choix_eleves_tous' value='tous' onchange='display_div_liste_eleves()' checked /><label for='choix_eleves_tous'> Tous les élèves</label>\n";
	echo "</li>\n";
	echo "<li>\n";
	echo "<input type='radio' name='choix_eleves' id='choix_eleves_certains' onchange='display_div_liste_eleves()' value='certains' /><label for='choix_eleves_certains'> Certains élèves seulement</label>\n";

	echo "<div id='div_liste_eleves' style='margin-left: 2em;'>\n";

	$cpt=0;
	for($i=0;$i<count($id_classe);$i++) {
		$sql="SELECT DISTINCT e.login, e.nom, e.prenom, e.sexe, e.naissance FROM eleves e, j_eleves_classes jec WHERE (e.login=jec.login AND jec.id_classe='".$id_classe[$i]."') ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />";
		$call_eleves=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		$nombre_ligne=mysqli_num_rows($call_eleves);
		if($nombre_ligne==0) {
			echo "<p style='color:red;'>Aucun élève n'est inscrit dans la classe de ".get_class_from_id($id_classe[$i]).".</p>\n";
		}
		else {

			$temoin_classe[$i]='y';
			if($choix_matieres=='certaines') {
				// Parcours de la liste des groupes
				// =============
				// AJOUTER UN TEST... si on a choisi 'certains' enseignements, mais sans aucun enseignement coché
				// =============
				if(isset($_POST['id_groupe_'.$id_classe[$i]])) {
					$tmp_grp=$_POST['id_groupe_'.$id_classe[$i]];
					for($loop=0;$loop<count($tmp_grp);$loop++) {
						echo "<input type='hidden' name='id_groupe_".$id_classe[$i]."[]' value='$tmp_grp[$loop]' />\n";
					}
				}
				else {
					$temoin_classe[$i]='n';
				}
			}

			if($choix_periodes=='certaines') {
				// Parcours de la liste des périodes
				// =============
				// AJOUTER UN TEST... si on a choisi 'certaines' périodes, mais sans aucune période cochée
				// =============
				if(isset($_POST['num_periode_'.$id_classe[$i]])) {
					$tmp_per=$_POST['num_periode_'.$id_classe[$i]];
					for($loop=0;$loop<$max_per;$loop++) {
						if(isset($tmp_per[$loop])) {
							echo "<input type='hidden' name='num_periode_".$id_classe[$i]."[]' value='$tmp_per[$loop]' />\n";
						}
					}
				}
				else {
					$temoin_classe[$i]='n';
				}
			}

			if($temoin_classe[$i]=='y') {
				echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
	
				$first_ele[$id_classe[$i]]=$cpt;
				echo "<table class='boireaus' summary='Classe n°$id_classe[$i]'/>\n";
				echo "<tr>\n";
				echo "<th colspan='4'>\n";
				echo "Classe de ".get_class_from_id($id_classe[$i])."\n";
				echo "</th>\n";
				echo "</tr>\n";
	
				echo "<tr>\n";
				echo "<th>\n";
				//echo "Cocher/décocher\n";
				echo "<p><a href='#' onClick='ModifCase(".$id_classe[$i].",true);return false;'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href='#' onClick='ModifCase(".$id_classe[$i].",false);return false;'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a></p>\n";
				echo "</th>\n";
				echo "<th>Elève</th>\n";
				echo "<th>Sexe</th>\n";
				echo "<th>Naissance</th>\n";
				echo "</tr>\n";
	
				$alt=1;
				while($lig_ele=mysqli_fetch_object($call_eleves)) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'>\n";
					echo "<td>\n";
					echo "<input type='checkbox' name='login_eleve_".$id_classe[$i]."[]' id='login_eleve_$cpt' value='$lig_ele->login' onchange='change_style_eleve($cpt)' checked />\n";
					echo "</td>\n";
	
					echo "<td style='text-align:left; font-weight: bold;'><label for='login_eleve_$cpt' id='label_eleve_$cpt'>$lig_ele->nom $lig_ele->prenom</label></td>\n";
	
					echo "<td>\n";
					echo "$lig_ele->sexe";
					echo "</td>\n";
	
					echo "<td>\n";
					echo formate_date($lig_ele->naissance);
					echo "</td>\n";
					echo "</tr>\n";
					$cpt++;
				}
				echo "</table>\n";
				$last_ele[$id_classe[$i]]=$cpt;
			}
		}
		echo "<br />\n";
	}

	echo "<p><a href='javascript:ModifToutesCases(true)'>Cocher</a> / <a href='javascript:ModifToutesCases(false)'>décocher</a>  tous les élèves</p>\n";

	echo "</div>\n";

	echo "</li>\n";

	echo "</ul>\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";


	echo "<script type='text/javascript'>
	document.getElementById('div_liste_eleves').style.display='none';

	function display_div_liste_eleves() {
		if(document.getElementById('choix_eleves_certains').checked==true) {
			document.getElementById('div_liste_eleves').style.display='block';
		}
		else {
			document.getElementById('div_liste_eleves').style.display='none';
		}
	}

	function ModifToutesCases(mode) {
";
	for($i=0;$i<count($id_classe);$i++) {
		if($temoin_classe[$i]=='y') {
			echo "		ModifCase(".$id_classe[$i].",mode);\n";
		}
	}

	echo "	}

	function ModifCase(id_classe,mode) {
		var first_ele=new Array();
		var last_ele=new Array();\n";

	for($i=0;$i<count($id_classe);$i++) {
		if($temoin_classe[$i]=='y') {
			echo "		first_ele[".$id_classe[$i]."]=".$first_ele[$id_classe[$i]].";
		last_ele[".$id_classe[$i]."]=".$last_ele[$id_classe[$i]].";\n";
		}
	}

	echo "
		for (var k=first_ele[id_classe];k<last_ele[id_classe];k++) {
			if(document.getElementById('login_eleve_'+k)){
				document.getElementById('login_eleve_'+k).checked = mode;
				change_style_eleve(k);
			}
		}
	}

	function change_style_eleve(num) {
		if((document.getElementById('login_eleve_'+num))&&(document.getElementById('label_eleve_'+num))) {
			if(document.getElementById('login_eleve_'+num).checked) {
				document.getElementById('label_eleve_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_eleve_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";

}
elseif(!isset($choix_donnees)) {

	// Anonymat souhaité

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix des classes</a>";
	echo " | <a href='javascript: history.go(-3);'>Retour au choix des périodes</a>";
	echo " | <a href='javascript: history.go(-2);'>Retour au choix des enseignements</a>";
	echo " | <a href='javascript: history.go(-1);'>Retour au choix des élèves</a>";
	echo "</p>\n";

	echo "<p class='bold'>Choix des données à exporter&nbsp;:</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	echo "<input type='hidden' name='choix_periodes' value='$choix_periodes' />\n";
	echo "<input type='hidden' name='max_per' value='$max_per' />\n";
	echo "<input type='hidden' name='choix_matieres' value='$choix_matieres' />\n";
	echo "<input type='hidden' name='choix_eleves' value='$choix_eleves' />\n";

	for($i=0;$i<count($id_classe);$i++) {
		echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";

		if($choix_matieres=='certaines') {
			// Parcours de la liste des groupes
			if(isset($_POST['id_groupe_'.$id_classe[$i]])) {
// =============
// AJOUTER UN TEST... si on a choisi 'certains' enseignements, mais sans aucun enseignement coché
// =============
				$tmp_grp=$_POST['id_groupe_'.$id_classe[$i]];
				for($loop=0;$loop<count($tmp_grp);$loop++) {
					echo "<input type='hidden' name='id_groupe_".$id_classe[$i]."[]' value='$tmp_grp[$loop]' />\n";
				}
			}
		}

		if($choix_periodes=='certaines') {
			// Parcours de la liste des périodes
			if(isset($_POST['num_periode_'.$id_classe[$i]])) {
// =============
// AJOUTER UN TEST... si on a choisi 'certaines' périodes, mais sans aucune période cochée
// =============
				$tmp_per=$_POST['num_periode_'.$id_classe[$i]];
				for($loop=0;$loop<$max_per;$loop++) {
					if(isset($tmp_per[$loop])) {
						echo "<input type='hidden' name='num_periode_".$id_classe[$i]."[]' value='$tmp_per[$loop]' />\n";
					}
				}
			}
		}

		if($choix_eleves=='certains') {
			// Parcours de la liste des périodes
			if(isset($_POST['login_eleve_'.$id_classe[$i]])) {
// =============
// AJOUTER UN TEST...
// =============
				$tmp_ele=$_POST['login_eleve_'.$id_classe[$i]];
				for($loop=0;$loop<count($tmp_ele);$loop++) {
					echo "<input type='hidden' name='login_eleve_".$id_classe[$i]."[]' value='$tmp_ele[$loop]' />\n";
				}
			}
		}
	}

	$tab_champ_eleve=array('login', 'ele_id', 'elenoet', 'no_gep', 'nom', 'prenom', 'sexe', 'naissance', 'note', 'app');
	$tab_descr_champ_eleve=array('Login', 'Identifiant ele_id', 'Identifiant elenoet', 'Identifiant national (INE)', 'Nom', 'Prénom', 'Sexe', 'Date de naissance', 'Moyenne du bulletin', 'Appréciation du bulletin');
	$tab_incl_champ_eleve=array('sexe', 'naissance', 'note', 'app');

	echo "<div style='float:left; width:30%'>\n";
	echo "<table class='boireaus' summary='Données élève à inclure'>\n";
	echo "<tr>\n";
	echo "<th>\n";
	echo "</th>\n";
	echo "<th>Données élève à inclure</th>\n";
	echo "</tr>\n";

	$alt=1;
	for($loop=0;$loop<count($tab_champ_eleve);$loop++) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td><input type='checkbox' name='champ_eleve[]' id='champ_eleve_$loop' value='$tab_champ_eleve[$loop]' ";
		if(in_array($tab_champ_eleve[$loop],$tab_incl_champ_eleve)) {echo "checked ";}
		echo "/></td>\n";
		echo "<td><label for='champ_eleve_$loop'>$tab_descr_champ_eleve[$loop]</label></td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "</div>\n";

	$tab_champ_autre=array('id_classe', 'classe', 'id_groupe', 'nom_groupe', 'matiere', 'matiere_nom_complet');
	$tab_descr_champ_autre=array('Identifiant de classe', 'Nom de classe', 'Identifiant de groupe', 'Nom de groupe', 'Nom court de matière', 'Nom long de matière');
	//$tab_incl_champ_autre=array('id_classe', 'classe', 'id_groupe', 'nom_groupe', 'matiere', 'matiere_nom_complet');
	$tab_incl_champ_autre=array('matiere_nom_complet');

	echo "<div style='float:left; width:30%'>\n";
	echo "<table class='boireaus' summary='Données autres à inclure'>\n";
	echo "<tr>\n";
	echo "<th>\n";
	echo "</th>\n";
	echo "<th>Autres données à inclure</th>\n";
	echo "</tr>\n";

	$alt=1;
	for($loop=0;$loop<count($tab_champ_autre);$loop++) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td><input type='checkbox' name='champ_autre[]' id='champ_autre_$loop' value='$tab_champ_autre[$loop]' ";
		if(in_array($tab_champ_autre[$loop],$tab_incl_champ_autre)) {echo "checked ";}
		echo "/></td>\n";
		echo "<td><label for='champ_autre_$loop'>$tab_descr_champ_autre[$loop]</label></td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "</div>\n";


	$tab_champ_enseignant=array('login', 'nom', 'prenom', 'civilite');
	$tab_descr_champ_enseignant=array('Login', 'Nom', 'Prénom', 'Civilité');
	$tab_incl_champ_enseignant=array('civilite');

	echo "<div style='float:left; width:30%'>\n";
	echo "<table class='boireaus' summary='Données enseignant à inclure'>\n";
	echo "<tr>\n";
	echo "<th>\n";
	echo "</th>\n";
	echo "<th>Données enseignant à inclure</th>\n";
	echo "</tr>\n";

	$alt=1;
	for($loop=0;$loop<count($tab_champ_enseignant);$loop++) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td><input type='checkbox' name='champ_enseignant[]' id='champ_enseignant_$loop' value='$tab_champ_enseignant[$loop]' ";
		if(in_array($tab_champ_enseignant[$loop],$tab_incl_champ_enseignant)) {echo "checked ";}
		echo "/></td>\n";
		echo "<td><label for='champ_enseignant_$loop'>$tab_descr_champ_enseignant[$loop]</label></td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "</div>\n";

	echo "<input type='hidden' name='choix_donnees' value='y' />\n";
	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<div style='clear:both'></div>\n";

	echo "<br />\n";

	echo "<p><i>NOTE&nbsp;:</i></p>
	<p style='margin-left:3em;'>L'export CSV monolithique a un inconvénient&nbsp;:<br />On ne récupère que le premier professeur dans le cas d'enseignements assurés par plusieurs professeurs pour un même groupe.</p>\n";
}
else {
	// On ne devrait pas arriver là
	echo "<p style='color:red'>Soit on a envoyé via header() le CSV, soit il faut fournir ici un lien de telechargement depuis temp.</p>\n";

	if(isset($csv)) {
		echo "<pre>$csv</pre>";
	}


}
//echo "<p>The end!</p>";

require_once("../lib/footer.inc.php");
?>
