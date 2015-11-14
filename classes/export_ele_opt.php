<?php
/*
 * $Id$
 *
 * Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/classes/export_ele_opt.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/classes/export_ele_opt.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Export options élèves',
statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//debug_var();

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$choix_periodes=isset($_POST['choix_periodes']) ? $_POST['choix_periodes'] : NULL;
$max_per=isset($_POST['max_per']) ? $_POST['max_per'] : NULL;
$choix_matieres=isset($_POST['choix_matieres']) ? $_POST['choix_matieres'] : NULL;
$choix_donnees=isset($_POST['choix_donnees']) ? $_POST['choix_donnees'] : NULL;

if(isset($choix_donnees)) {
	check_token();

	$id_groupe=array();
	$tab_id_groupe=array();
	$tab_matieres=array();
	if($choix_matieres=="toutes") {
		for($loop=0;$loop<count($id_classe);$loop++) {
			$sql="SELECT g.*, jgm.matiere FROM groupes g, j_groupes_classes jgc, j_groupes_matieres jgm WHERE g.id=jgc.id_groupe AND jgc.id_groupe=jgm.id_groupe AND jgc.id_classe='".$id_classe[$loop]."';";
			$res=mysqli_query($GLOBALS["mysqli"],$sql);
			while($lig=mysqli_fetch_object($res)) {
				if(!array_key_exists($lig->id, $id_groupe)) {
					$id_groupe[$lig->id]=get_group($lig->id, array('matieres', 'eleves'));
					$tab_id_groupe[]=$lig->id;

					if(!in_array($id_groupe[$lig->id]['matiere']['matiere'], $tab_matieres)) {
						$tab_matieres[]=$id_groupe[$lig->id]['matiere']['matiere'];
					}
				}
			}
		}
	}
	else {
		for($loop=0;$loop<count($id_classe);$loop++) {
			if(isset($_POST["id_groupe_".$id_classe[$loop]])) {
				for($loop2=0;$loop2<count($_POST["id_groupe_".$id_classe[$loop]]);$loop2++) {
					if(!array_key_exists($_POST["id_groupe_".$id_classe[$loop]][$loop2], $id_groupe)) {
						$id_groupe[$_POST["id_groupe_".$id_classe[$loop]][$loop2]]=get_group($_POST["id_groupe_".$id_classe[$loop]][$loop2], array('matieres', 'eleves'));
						$tab_id_groupe[]=$_POST["id_groupe_".$id_classe[$loop]][$loop2];

						if(!in_array($id_groupe[$_POST["id_groupe_".$id_classe[$loop]][$loop2]]['matiere']['matiere'], $tab_matieres)) {
							$tab_matieres[]=$id_groupe[$_POST["id_groupe_".$id_classe[$loop]][$loop2]]['matiere']['matiere'];
						}
					}
				}
			}
		}
	}


	$tab_ele=array();
	$tab_ele_mat=array();
	foreach($id_groupe as $current_id_groupe => $current_group) {
		if($_POST['choix_periodes']=='toutes') {
			for($loop=0;$loop<count($current_group['eleves']["all"]["list"]);$loop++) {
				if(!in_array($current_group['eleves']["all"]["list"][$loop], $tab_ele)) {
					$tab_ele[]=$current_group['eleves']["all"]["list"][$loop];
				}
				$tab_ele_mat[$current_group['eleves']["all"]["list"][$loop]][]=$current_group['matiere']["matiere"];
			}
		}
		else {
			foreach($current_group['eleves'] as $current_periode => $current_tab_ele_per) {
				if(isset($current_tab_ele_per['telle_classe'])) {
					foreach($current_tab_ele_per['telle_classe'] as $current_id_classe => $current_tab_ele_per_classe) {
						if((isset($_POST['num_periode_'.$current_id_classe]))&&($_POST['num_periode_'.$current_id_classe]==$current_periode)) {
							for($loop=0;$loop<count($current_tab_ele_per_classe);$loop++) {
								if(!in_array($current_tab_ele_per_classe[$loop], $tab_ele)) {
									$tab_ele[]=$current_tab_ele_per_classe[$loop];
								}
								$tab_ele_mat[$current_tab_ele_per_classe[$loop]][]=$current_group['matiere']["matiere"];
							}
						}
					}
				}
			}
		}
	}

	$csv="";
	$ligne_entete="";
	$lignes_csv="";

	$tab_champs_retenus=array();
	if(in_array($_SESSION['statut'], array('administrateur', 'scolarite'))) {
		$tab_champ_eleve=array('login', 'ele_id', 'elenoet', 'no_gep', 'nom', 'prenom', 'sexe', 'naissance', 'id_classe', 'classe');
		$tab_descr_champ_eleve=array('Login', 'Identifiant ele_id', 'Identifiant elenoet', 'Identifiant national (INE)', 'Nom', 'Prénom', 'Sexe', 'Date de naissance', 'Identifiant de classe', 'Classe');
	}
	else {
		$tab_champ_eleve=array('login', 'nom', 'prenom', 'sexe', 'naissance', 'classe');
		$tab_descr_champ_eleve=array('Login', 'Nom', 'Prénom', 'Sexe', 'Date de naissance', 'Classe');
	}

	for($loop=0;$loop<count($tab_champ_eleve);$loop++) {
		if(in_array($tab_champ_eleve[$loop], $_POST['champ_eleve'])) {
			$tab_champs_retenus[]=$tab_champ_eleve[$loop];
			$ligne_entete.=$tab_descr_champ_eleve[$loop].";";
		}
	}

	if(($_POST['format_export']==1)||($_POST['format_export']==2)) {
		for($loop=0;$loop<count($tab_matieres);$loop++) {
			$ligne_entete.=$tab_matieres[$loop].";";
		}
		$ligne_entete.="\n";
	}
	elseif($_POST['format_export']==3) {
		//Il faudra compléter la ligne d'entête une fois connu le nombre max d'options
		//$ligne_entete.="\n";
	}
	elseif($_POST['format_export']==4) {
		for($loop=0;$loop<count($tab_id_groupe);$loop++) {
			$ligne_entete.=preg_replace("/;/", ",", get_info_grp($tab_id_groupe[$loop], array('description', 'matieres', 'classes', 'profs'),"txt")).";";
		}
		$ligne_entete.="\n";
	}
	elseif($_POST['format_export']==5) {
		for($loop=0;$loop<count($tab_id_groupe);$loop++) {
			$ligne_entete.=preg_replace("/;/", ",", get_info_grp($tab_id_groupe[$loop], array('description', 'matieres', 'classes', 'profs'),"txt")).";";
		}
		$ligne_entete.="\n";
	}

	$nb_max_opt=0;
	for($loop_classe=0;$loop_classe<count($id_classe);$loop_classe++) {
		$sql="SELECT distinct e.* FROM j_eleves_classes jec,
							eleves e
						WHERE jec.id_classe='".$id_classe[$loop_classe]."' AND 
							jec.login=e.login 
						ORDER BY e.nom, e.prenom, e.naissance;";
		$res=mysqli_query($GLOBALS["mysqli"],$sql);
		while($lig=mysqli_fetch_object($res)) {
			if(in_array($lig->login, $tab_ele)) {
				for($loop=0;$loop<count($tab_champs_retenus);$loop++) {
					if($tab_champs_retenus[$loop]=='id_classe') {
						if($_POST['choix_periodes']=='toutes') {
							$sql="SELECT distinct id_classe FROM j_eleves_classes jec
											WHERE jec.login='".$lig->login."'
											ORDER BY periode;";
							$res_clas=mysqli_query($GLOBALS["mysqli"],$sql);
							$cpt_clas=0;
							while($lig_clas=mysqli_fetch_object($res_clas)) {
								if($cpt_clas>0) {
									$lignes_csv.=", ";
								}
								$lignes_csv.=$lig_clas->id_classe;
								$cpt_clas++;
							}
						}
						else {
							$sql="SELECT distinct id_classe, periode FROM j_eleves_classes jec
											WHERE jec.login='".$lig->login."'
											ORDER BY periode;";
							$res_clas=mysqli_query($GLOBALS["mysqli"],$sql);
							$cpt_clas=0;
							while($lig_clas=mysqli_fetch_object($res_clas)) {
								if($cpt_clas>0) {
									$lignes_csv.=", ";
								}
								if((isset($_POST['num_periode_'.$lig_clas->id_classe]))&&($_POST['num_periode_'.$lig_clas->id_classe]==$lig_clas->periode)) {
									$lignes_csv.=$lig_clas->id_classe;
									$cpt_clas++;
								}
							}
						}
						$lignes_csv.=";";
					}
					elseif($tab_champs_retenus[$loop]=='classe') {
						if($_POST['choix_periodes']=='toutes') {
							$sql="SELECT distinct classe FROM j_eleves_classes jec, 
													classes c
											WHERE c.id=jec.id_classe AND 
												jec.login='".$lig->login."'
											ORDER BY periode;";
							$res_clas=mysqli_query($GLOBALS["mysqli"],$sql);
							$cpt_clas=0;
							while($lig_clas=mysqli_fetch_object($res_clas)) {
								if($cpt_clas>0) {
									$lignes_csv.=", ";
								}
								$lignes_csv.=$lig_clas->classe;
								$cpt_clas++;
							}
						}
						else {
							$sql="SELECT distinct id_classe, classe, periode FROM j_eleves_classes jec,
													classes c
											WHERE c.id=jec.id_classe AND 
												jec.login='".$lig->login."'
											ORDER BY periode;";
							$res_clas=mysqli_query($GLOBALS["mysqli"],$sql);
							$cpt_clas=0;
							while($lig_clas=mysqli_fetch_object($res_clas)) {
								if($cpt_clas>0) {
									$lignes_csv.=", ";
								}
								if((isset($_POST['num_periode_'.$lig_clas->id_classe]))&&($_POST['num_periode_'.$lig_clas->id_classe]==$lig_clas->periode)) {
									$lignes_csv.=$lig_clas->classe;
									$cpt_clas++;
								}
							}
						}
						$lignes_csv.=";";
					}
					else {
						$champ_courant=$tab_champs_retenus[$loop];
						if($champ_courant=="naissance") {
							$lignes_csv.=formate_date($lig->$champ_courant).";";
						}
						else {
							$lignes_csv.=$lig->$champ_courant.";";
						}
					}
				}

				// Traiter les colonnes d'options
				if($_POST['format_export']==1) {
					for($loop=0;$loop<count($tab_matieres);$loop++) {
						if(in_array($tab_matieres[$loop], $tab_ele_mat[$lig->login])) {
							$lignes_csv.=1;
						}
						$lignes_csv.=";";
					}
				}
				elseif($_POST['format_export']==2) {
					for($loop=0;$loop<count($tab_matieres);$loop++) {
						if(in_array($tab_matieres[$loop], $tab_ele_mat[$lig->login])) {
							$lignes_csv.=$tab_matieres[$loop];
						}
						$lignes_csv.=";";
					}
				}
				elseif($_POST['format_export']==3) {
					$cpt_opt_ele=0;
					for($loop=0;$loop<count($tab_matieres);$loop++) {
						if(in_array($tab_matieres[$loop], $tab_ele_mat[$lig->login])) {
							$lignes_csv.=$tab_matieres[$loop].";";
							$cpt_opt_ele++;
						}
					}
					if($cpt_opt_ele>$nb_max_opt) {
						$nb_max_opt=$cpt_opt_ele;
					}
				}
				elseif($_POST['format_export']==4) {
					for($loop=0;$loop<count($tab_id_groupe);$loop++) {
						if((isset($id_groupe[$tab_id_groupe[$loop]]['matiere']['matiere']))&&(in_array($id_groupe[$tab_id_groupe[$loop]]['matiere']['matiere'], $tab_ele_mat[$lig->login]))) {
							$lignes_csv.=1;
						}
						$lignes_csv.=";";
					}
				}
				elseif($_POST['format_export']==5) {
					for($loop=0;$loop<count($tab_id_groupe);$loop++) {
						if((isset($id_groupe[$tab_id_groupe[$loop]]['matiere']['matiere']))&&(in_array($id_groupe[$tab_id_groupe[$loop]]['matiere']['matiere'], $tab_ele_mat[$lig->login]))) {
							$lignes_csv.=$id_groupe[$tab_id_groupe[$loop]]['matiere']['matiere'];
						}
						$lignes_csv.=";";
					}
				}

				$lignes_csv.="\n";
			}
		}
	}

	if($_POST['format_export']==3) {
		for($loop=0;$loop<$nb_max_opt;$loop++) {
			$n=$loop+1;
			$ligne_entete.="OPT$n;";
		}
		$ligne_entete.="\n";
	}

	$csv=$ligne_entete.$lignes_csv;

	$nom_fic="liste_options_eleves_".strftime("%Y%m%d_%H%M%S").".csv";
	send_file_download_headers('text/x-csv',$nom_fic);
	echo echo_csv_encoded($csv);
	die();
}


$themessage = 'Des modifications ont été effectuées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *******************************
$titre_page = "Export matières élèves";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE ****************************

if((isset($id_classe))) {
	if(is_array($id_classe)) {
		if(isset($id_classe[0])) {
			$id_premiere_classe=$id_classe[0];
		}
	}
	elseif((preg_match("/^$[0-9]*$/", $id_classe))&&($id_classe>0)) {
		$id_premiere_classe=$id_classe;
	}
}

if((acces("/classes/classes_const.php", $_SESSION['statut']))&&(isset($id_premiere_classe))) {
	echo "<p class='bold'><a href='classes_const.php?id_classe=$id_premiere_classe'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}
elseif(acces("/classes/index.php", $_SESSION['statut'])) {
	echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}
else {
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}

if(!isset($id_classe)) {
	echo "</p>\n";

	echo "<p class='bold' style='margin-top:1em;'>Choix des classes&nbsp;:</p>\n";

	// Liste des classes avec élève:
	$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) ORDER BY c.classe;";
	$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);

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

	echo "
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

	echo "<p class='bold' style='margin-top:1em;'>Choix des périodes&nbsp;:</p>\n";

	//echo "<p style='color:red;'>A FAIRE: afficher les périodes closes...</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

	echo "<p>Extraire les élèves inscrits dans les enseignements (<em>que vous choisirez plus loin</em>) sur les périodes suivantes&nbsp;:</p>";
	echo "<ul style='list-style-type: none;'>\n";
	echo "<li>\n";
	echo "<input type='radio' name='choix_periodes' id='choix_periodes_toutes' value='toutes' onchange='display_div_liste_periodes()' checked /><label for='choix_periodes_toutes'> Toutes les périodes (<em>comprendre des élèves inscrits sur une période au moins quelle qu'elle soit</em>)</label>\n";
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
		$call_per=mysqli_query($GLOBALS["mysqli"], $sql);
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
				//echo "<input type='checkbox' name='num_periode_".$id_classe[$i]."[]' id='num_periode_$cpt' value='$lig_per->num_periode' onchange='change_style_per($cpt)' checked />\n";
				echo "<input type='radio' name='num_periode_".$id_classe[$i]."' id='num_periode_$cpt' value='$lig_per->num_periode' onchange='change_style_per($cpt)' checked />\n";
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

	echo "<p class='bold' style='margin-top:1em;'>Choix des matières/enseignements&nbsp;:</p>\n";

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
		//$sql="SELECT DISTINCT g.id, g.name, g.description, jgm.id_matiere FROM groupes g, j_groupes_classes jgc, j_groupes_matieres jgm WHERE (g.id=jgc.id_groupe AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='".$id_classe[$i]."') ORDER BY jgc.priorite, g.name";
		$sql="SELECT DISTINCT g.id, g.name, g.description, jgm.id_matiere FROM groupes g, j_groupes_classes jgc, j_groupes_matieres jgm WHERE (g.id=jgc.id_groupe AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='".$id_classe[$i]."') ORDER BY jgm.id_matiere, jgc.priorite, g.name";
		//echo "$sql<br />";
		$call_group = mysqli_query($GLOBALS["mysqli"], $sql);
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
							//echo "<input type='hidden' name='num_periode_".$id_classe[$i]."[]' value='$tmp_per[$loop]' />\n";
							echo "<input type='hidden' name='num_periode_".$id_classe[$i]."' value='$tmp_per[$loop]' />\n";
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
					$res_prof_grp=mysqli_query($GLOBALS["mysqli"], $sql);
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

	echo "<div id='fixe'><input type='submit' value='Valider' /></div>\n";

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
elseif(!isset($choix_donnees)) {

	// Anonymat souhaité

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix des classes</a>";
	echo " | <a href='javascript: history.go(-3);'>Retour au choix des périodes</a>";
	echo " | <a href='javascript: history.go(-2);'>Retour au choix des enseignements</a>";
	echo "</p>\n";

	echo "<p class='bold' style='margin-top:1em;'>Choix des données à exporter&nbsp;:</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire' target='_blank'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='choix_periodes' value='$choix_periodes' />\n";
	echo "<input type='hidden' name='max_per' value='$max_per' />\n";
	echo "<input type='hidden' name='choix_matieres' value='$choix_matieres' />\n";

	for($i=0;$i<count($id_classe);$i++) {
		echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";

		if($choix_matieres=='certaines') {
			// Parcours de la liste des groupes
			if(isset($_POST['id_groupe_'.$id_classe[$i]])) {
				$tmp_grp=$_POST['id_groupe_'.$id_classe[$i]];
				for($loop=0;$loop<count($tmp_grp);$loop++) {
					echo "<input type='hidden' name='id_groupe_".$id_classe[$i]."[]' value='$tmp_grp[$loop]' />\n";
				}
			}
		}

		if($choix_periodes=='certaines') {
			// Parcours de la liste des périodes
			if(isset($_POST['num_periode_'.$id_classe[$i]])) {
				$tmp_per=$_POST['num_periode_'.$id_classe[$i]];
				for($loop=0;$loop<$max_per;$loop++) {
					if(isset($tmp_per[$loop])) {
						//echo "<input type='hidden' name='num_periode_".$id_classe[$i]."[]' value='$tmp_per[$loop]' />\n";
						echo "<input type='hidden' name='num_periode_".$id_classe[$i]."' value='$tmp_per[$loop]' />\n";
					}
				}
			}
		}
	}

	if(in_array($_SESSION['statut'], array('administrateur', 'scolarite'))) {
		$tab_champ_eleve=array('login', 'ele_id', 'elenoet', 'no_gep', 'nom', 'prenom', 'sexe', 'naissance', 'id_classe', 'classe');
		$tab_descr_champ_eleve=array('Login', 'Identifiant ele_id', 'Identifiant elenoet', 'Identifiant national (INE)', 'Nom', 'Prénom', 'Sexe', 'Date de naissance', 'Identifiant de classe', 'Classe');
	}
	else {
		$tab_champ_eleve=array('login', 'nom', 'prenom', 'sexe', 'naissance', 'classe');
		$tab_descr_champ_eleve=array('Login', 'Nom', 'Prénom', 'Sexe', 'Date de naissance', 'Classe');
	}
	// Cochés par défaut:
	$tab_incl_champ_eleve=array('login', 'nom', 'prenom','sexe', 'naissance');

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

	echo "<div style='float:left; width:60%'>\n";
	echo "<p class='bold'>Format de l'export&nbsp;:</p>
<p style='text-indent:-3em; margin-left:3em;'><input type='radio' name='format_export' id='format_export_1' value='1' checked /><label for='format_export_1'>Groupés par nom de matières, avec des 1 pour faire une somme&nbsp;:<br />
NOM;PRENOM;...;AGL1;ALL1;ESP2;<br />
Dupre;Thomas;...;1;;1;</label></p>

<p style='text-indent:-3em; margin-left:3em;'><input type='radio' name='format_export' id='format_export_2' value='2' /><label for='format_export_2'>Groupés par nom de matières, avec rappel du nom de matière&nbsp;:<br />
NOM;PRENOM;...;AGL1;ALL1;ESP2;<br />
Dupre;Thomas;...;AGL1;;ESP2;</label></p>

<p style='text-indent:-3em; margin-left:3em;'><input type='radio' name='format_export' id='format_export_3' value='3' /><label for='format_export_3'>Liste des options&nbsp;:<br />
NOM;PRENOM;...;OPT1;OPT2;OPT3;<br />
Dupre;Thomas;...;AGL1;ESP2;<br />
Dupont;Simone;...;ALL1;AGL2;LATIN;<br />
</label></p>

<p style='text-indent:-3em; margin-left:3em;'><input type='radio' name='format_export' id='format_export_4' value='4' /><label for='format_export_4'>Liste des enseignements&nbsp;:<br />
NOM;PRENOM;...;AGL1_groupe_1;AGL1_groupe_2;ESP2_groupe_1;ESP2_groupe_2;<br />
Dupre;Thomas;...;1;;1;<br />
Dupont;Simone;...;;1;1;;<br />
</label></p>

<p style='text-indent:-3em; margin-left:3em;'><input type='radio' name='format_export' id='format_export_5' value='5' /><label for='format_export_5'>Liste des enseignements&nbsp;:<br />
NOM;PRENOM;...;AGL1_groupe_1;AGL1_groupe_2;ESP2_groupe_1;ESP2_groupe_2;<br />
Dupre;Thomas;...;AGL1;;ESP2;<br />
Dupont;Simone;...;;AGL1;ESP2;;<br />
</label></p>
";
	echo "</div>\n";

	echo "<div style='clear:both'></div>\n";

	echo "<input type='hidden' name='choix_donnees' value='y' />\n";
	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";


}
else {
	// On ne devrait pas arriver là
	echo "<p style='color:red'>Soit on a envoyé via header() le CSV, soit il faut fournir ici un lien de telechargement depuis temp.</p>\n";

	if(isset($csv)) {
		echo "<pre>$csv</pre>";
	}
}

echo "<p><br /></p>

<p style='text-indent:-4em;margin-left:4em;'><em>NOTE&nbsp;:</em> Cette page est destinée à effectuer l'extraction des certains enseignements (<em>de votre choix</em>) suivis par les élèves de classes de votre choix.<br />
Dans un premier temps, vous choisissez la ou les classes,<br />
puis la période sur laquelle les élèves sont inscrits,<br />
puis les enseignements à retenir<br />
et enfin la liste des informations à extraire dans le CSV.</p>";

require("../lib/footer.inc.php");
?>
