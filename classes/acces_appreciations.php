<?php
/*
 * $Id$
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

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$msg="";

$sql="CREATE TABLE IF NOT EXISTS `matieres_appreciations_acces` (
`id_classe` INT( 11 ) NOT NULL ,
`statut` VARCHAR( 255 ) NOT NULL ,
`periode` INT( 11 ) NOT NULL ,
`date` DATE NOT NULL ,
`acces` ENUM( 'y', 'n', 'date' ) NOT NULL
);";
$creation_table=mysql_query($sql);

if(isset($_POST['submit'])) {
	$max_per=isset($_POST['max_per']) ? $_POST['max_per'] : 0;
	$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
	$nb_classe=isset($_POST['nb_classe']) ? $_POST['nb_classe'] : 0;

	unset($tab);
	$tab=array();
	$tab['ele']='eleve';
	$tab['resp']='responsable';

	$cpt=0;

	foreach($tab as $pref => $statut) {
		for($j=0;$j<$nb_classe;$j++){
			if(isset($id_classe[$j])) {
				for($i=1;$i<=$max_per;$i++){
					if(isset($_POST[$pref.'_mode_'.$j.'_'.$i])) {
						$mode=$_POST[$pref.'_mode_'.$j.'_'.$i];
						if($mode=="manuel") {
							if(isset($_POST[$pref.'_acces_'.$j.'_'.$i])) {
								$accessible="y";
							}
							else {
								$accessible="n";
							}
							$sql="DELETE FROM matieres_appreciations_acces
									WHERE id_classe='$id_classe[$j]' AND
											statut='$statut' AND
											periode='$i';";
							$suppr=mysql_query($sql);

							$sql="INSERT INTO matieres_appreciations_acces
									SET id_classe='$id_classe[$j]',
											statut='$statut',
											periode='$i',
											acces='$accessible';";
							$insert=mysql_query($sql);
							if(!$insert) {$msg.="Erreur sur l'accès aux appréciations de la classe ".get_class_from_id($id_classe[$j])." en $statut pour la période $i.<br />\n";}else{$cpt++;}
						}
						else {
							if(isset($_POST[$pref.'_display_date_'.$j.'_'.$i])) {
								$tmp_date=$_POST[$pref.'_display_date_'.$j.'_'.$i];
								// Contrôler le format de la date et sa validité.

								$tabdate=explode("/",$tmp_date);

								if(checkdate($tabdate[1],$tabdate[0],$tabdate[2])) {
									$date=sprintf("%04d",$tabdate[2])."-".$tabdate[1]."-".$tabdate[0];

									$sql="DELETE FROM matieres_appreciations_acces
											WHERE id_classe='$id_classe[$j]' AND
													statut='$statut' AND
													periode='$i';";
									$suppr=mysql_query($sql);

									$sql="INSERT INTO matieres_appreciations_acces
											SET id_classe='$id_classe[$j]',
													statut='$statut',
													periode='$i',
													date='$date',
													acces='date';";
									$insert=mysql_query($sql);
									if(!$insert) {$msg.="Erreur sur l'accès aux appréciations de la classe ".get_class_from_id($id_classe[$j])." en $statut pour la période $i.<br />\n";}else{$cpt++;}
								}
								else {
									$msg.="La date $tmp_date n'est pas valide pour la classe ".get_class_from_id($id_classe[$j])." en $statut pour la période $i.<br />\n";
								}
							}
						}
					}
				}
			}
		}
	}
	if(($msg=="")&&($cpt>0)) {
		if($cpt==1) {
			$msg="Enregistrement effectué.<br />\n";
		}
		else{
			$msg="Enregistrements effectués ($cpt).<br />\n";
		}
	}
}


$javascript_specifique="classes/acces_appreciations";

//include "../lib/periodes.inc.php";
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Accès aux appréciations";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href='../accueil.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a></p>\n";

if($_SESSION['statut']=="professeur") {
	$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');

	$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0){
		echo "<p>Vous n'êtes pas ".$gepi_prof_suivi.".<br />Vous ne devriez donc pas accéder à cette page.</p>\n";
		echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
		exit();
	}

	$sql="SELECT DISTINCT c.* FROM j_eleves_professeurs jep, j_eleves_classes jec, classes c
					WHERE jep.professeur='".$_SESSION['login']."' AND
						jep.login=jec.login AND
						jec.id_classe=c.id
					ORDER BY c.classe;";
}
elseif($_SESSION['statut']=="scolarite") {
	$sql="SELECT DISTINCT c.* FROM j_scol_classes jsc, classes c
					WHERE jsc.login='".$_SESSION['login']."' AND
						jsc.id_classe=c.id
					ORDER BY c.classe;";
}
elseif($_SESSION['statut']=="administrateur") {
	$sql="SELECT DISTINCT c.* FROM classes c ORDER BY c.classe;";
}
$res_classe=mysql_query($sql);

if(mysql_num_rows($res_classe)==0) {
	echo "<p>Vous n'avez accès à aucune classe.</p>\n";
	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	exit();
}

$tab_classe=array();
$cpt=0;
$max_per=0;
while($lig=mysql_fetch_object($res_classe)){

	$sql="SELECT MAX(num_periode) AS max_per FROM periodes WHERE id_classe='$lig->id';";
	$res_per=mysql_query($sql);

	if(mysql_num_rows($res_per)!=0) {
		$tab_classe[$cpt]=array();
		$tab_classe[$cpt]['id']=$lig->id;
		$tab_classe[$cpt]['classe']=$lig->classe;

		$lig_per=mysql_fetch_object($res_per);
		if($lig_per->max_per>$max_per) {$max_per=$lig_per->max_per;}

		$cpt++;
	}
}

echo "<p>Vous pouvez définir ici quand les comptes utilisateurs pour des responsables et des élèves peuvent accéder aux appréciations des professeurs et avis du conseil de classe.<br />
Il est souvent apprécié de pouvoir interdire l'accès aux élèves et responsables avant que le conseil de classe se soit déroulé.<br />
Cet accès est conditionné par l'existence des comptes responsables et élèves.</p>\n";

echo "<form method='post' action='".$_SERVER['PHP_SELF']."' name='form'>\n";
echo "<p align='center'><input type='submit' name='submit' value='Valider' /></p>\n";

echo "<table class='boireaus'>\n";
echo "<tr>\n";
echo "<th rowspan='2'>Classe</th>\n";
echo "<th rowspan='2'>Statut</th>\n";
echo "<th colspan='$max_per'>Périodes</th>\n";
echo "</tr>\n";

echo "<tr>\n";
for($i=1;$i<=$max_per;$i++) {
	$sql="SELECT DISTINCT nom_periode FROM periodes WHERE num_periode='$i';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==1) {
		$lig_per=mysql_fetch_object($test);
		echo "<th>$lig_per->nom_periode</th>\n";
	}
	else{
		echo "<th>Période $i</th>\n";
	}
}
echo "</tr>\n";

$annee = strftime("%Y");
$mois = strftime("%m");
$jour = strftime("%d");

$display_date=$jour."/".$mois."/".$annee;

/*
echo "<script type='text/javascript'>
	// <![CDATA[
	function f_date(id_div,date) {
		//new Ajax.Updater($('id_div'),'acces_appreciations_ajax.php?date=date',{method: 'get'});
		new Ajax.Updater($(id_div),'acces_appreciations_ajax.php?date='+date,{method: 'get'});
	}

	function f_manuel(id_div,classe_periode,statut) {
		new Ajax.Updater($(id_div),'acces_appreciations_ajax.php?classe_periode='+classe_periode+'&statut='+statut,{method: 'get'});
	}

	function modif_couleur(id_check,id_span) {
		if(document.getElementById(id_check)) {
			if(document.getElementById(id_span)) {
				if(document.getElementById(id_check).checked==true) {
					document.getElementById(id_span).style.color='green';
				}
				else {
					document.getElementById(id_span).style.color='red';
				}
			}
		}
	}
	//]]>
</script>\n";
*/

include("../lib/calendrier/calendrier.class.php");

$alt=1;
for($j=0;$j<count($tab_classe);$j++) {
	$alt=$alt*(-1);
	$id_classe=$tab_classe[$j]['id'];
	unset($nom_periode);
	unset($ver_periode);
	include "../lib/periodes.inc.php";
	if(isset($nom_periode)) {
		if(count($nom_periode)>0){
			echo "<tr class='lig$alt'>\n";
			echo "<td rowspan='2'>".$tab_classe[$j]['classe'];
			echo "<input type='hidden' name='id_classe[$j]' value='$id_classe' />\n";
			echo "</td>\n";
			echo "<td>Elève</td>\n";

			for($i=1;$i<=count($nom_periode);$i++) {
				$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='eleve';";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)==0) {
					$mode="manuel";
					$display_date=$jour."/".$mois."/".$annee;
					$accessible="n";
				}
				else {
					$lig=mysql_fetch_object($res);
					//if($lig->date=="0000-00-00") {
					if($lig->acces!="date") {
						$mode="manuel";
						$display_date=$jour."/".$mois."/".$annee;
					}
					else {
						$mode="date";
						$tabdate=explode("-",$lig->date);
						$display_date=$tabdate[2]."/".$tabdate[1]."/".$tabdate[0];
					}
					$accessible=$lig->acces;
				}

				echo "<td>";
					//$cal = new Calendrier("form", "display_date_".$j."_".$i);

					echo "<table border='0' style='border:0px;' width='100%'>\n";
					echo "<tr>\n";
					echo "<td valign='top' style='border:0px;'><input type='radio' name='ele_mode_".$j."_".$i."' id='ele_mode_".$j."_".$i."_manuel' value='manuel' ";
					if($mode=="manuel") {echo "checked ";}

					//echo "onfocus=\"f_manuel('ele_".$j."_".$i."','".$j."_".$i."','$accessible')\" ";
					echo "onfocus=\"f_manuel('ele_".$j."_".$i."','".$j."_".$i."','ele')\" ";

					echo "onchange=\"changement();\" ";

					echo "/></td>\n";
					echo "<td valign='top' style='border:0px; text-align:left;'>\n";
					echo "<label for='ele_mode_".$j."_".$i."_manuel' style='cursor: pointer;'>Manuel</label>";
					echo "</td valign='top' style='border:0px;'>\n";
					echo "<td rowspan='2' valign='top' style='border:0px;'>\n";

					/*
					echo "<div id='ele_manuel_".$j."_".$i."'>\n";
					echo "<label for='ele_acces_".$j."_".$i."' style='cursor: pointer;'><input type='checkbox' name='ele_acces_".$j."_".$i."' id='ele_acces_".$j."_".$i."' value='y' ";
					if($accessible=="y") {echo "checked ";}
					echo "/> Accessible</label>\n";
					echo "</div>\n";
					echo "<div id='ele_date_".$j."_".$i."'></div>\n";
					*/

					echo "<div align='center'>\n";
					echo "<div id='ele_".$j."_".$i."'>\n";
					if($mode=="manuel") {
						echo "<label for='ele_acces_".$j."_".$i."' style='cursor: pointer;'><input type='checkbox' name='ele_acces_".$j."_".$i."' id='ele_acces_".$j."_".$i."' value='y' ";
						echo "onchange=\"modif_couleur('ele_acces_".$j."_".$i."','ele_accessible_".$j."_".$i."'); changement();\" ";
						if($accessible=="y") {
							echo "checked ";
							echo "/> <span id='ele_accessible_".$j."_".$i."' style='color:green;'>Accessible</span></label>\n";
						}
						else {
							echo "/> <span id='ele_accessible_".$j."_".$i."' style='color:red;'>Accessible</span></label>\n";
						}
					}
					else {
						$timestamp_limite=mktime(0,0,0,$tabdate[1],$tabdate[2],$tabdate[0]);
						$timestamp_courant=time();

						if($timestamp_courant>$timestamp_limite) {echo "<span style='color:green;'>Accessible à la date du jour</span>";} else {echo "<span style='color:red;'>Inaccessible à la date du jour</span>";}

					}
					echo "</div>\n";
					echo "</div>\n";

					echo "</td>\n";
					echo "</tr>\n";

					$cal = new Calendrier("form", "ele_display_date_".$j."_".$i);

					echo "<tr>\n";
					echo "<td valign='top' style='border:0px;'><input type='radio' name='ele_mode_".$j."_".$i."' id='ele_mode_".$j."_".$i."_date' value='date' ";

					//echo "onchange=\"f_date('ele_date_".$j."_".$i."','truc $j $i')\" ";
					echo "onfocus=\"f_date('ele_".$j."_".$i."',document.getElementById('ele_display_date_".$j."_".$i."').value)\" ";

					echo "onchange=\"changement();\" ";

					if($mode!="manuel") {echo "checked ";}

					echo "/></td>\n";
					echo "<td valign='top' style='border:0px; text-align:left;'>\n";
					echo "<label for='ele_mode_".$j."_".$i."_date' style='cursor: pointer;'>Par date<br />";
					echo "</label>\n";
					echo "<input type='text' name='ele_display_date_".$j."_".$i."' id='ele_display_date_".$j."_".$i."' size='10' value='$display_date' onfocus=\"document.getElementById('ele_mode_".$j."_".$i."_date').checked='true';changement();\" ";

					echo "onchange=\"f_date('ele_".$j."_".$i."',document.getElementById('ele_display_date_".$j."_".$i."').value);changement();\" ";

					echo "/><a href='#calend' onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170).";document.getElementById('ele_mode_".$j."_".$i."_date').checked='true';\"><img src='../lib/calendrier/petit_calendrier.gif' alt='Calendrier' border='0' /></a>";
					//echo "</label>";
					echo "</td>\n";
					echo "</tr>\n";
					echo "</table>\n";

				echo "</td>\n";
			}
			echo "</tr>\n";

			echo "<tr class='lig$alt'>\n";
			echo "<td>Responsable</td>\n";

			for($i=1;$i<=count($nom_periode);$i++) {
				$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)==0) {
					$mode="manuel";
					$display_date=$jour."/".$mois."/".$annee;
					$accessible="n";
				}
				else {
					$lig=mysql_fetch_object($res);
					//if($lig->date=="0000-00-00") {
					if($lig->acces!="date") {
						$mode="manuel";
						$display_date=$jour."/".$mois."/".$annee;
					}
					else {
						$mode="date";
						$tabdate=explode("-",$lig->date);
						$display_date=$tabdate[2]."/".$tabdate[1]."/".$tabdate[0];
					}
					$accessible=$lig->acces;
				}

				echo "<td>";
					//$cal = new Calendrier("form", "display_date_".$j."_".$i);

					echo "<table border='0' style='border:0px;' width='100%'>\n";
					echo "<tr>\n";
					echo "<td valign='top' style='border:0px;'><input type='radio' name='resp_mode_".$j."_".$i."' id='resp_mode_".$j."_".$i."_manuel' value='manuel' ";
					if($mode=="manuel") {echo "checked ";}

					//echo "onfocus=\"f_manuel('resp_".$j."_".$i."','".$j."_".$i."','$accessible')\" ";
					echo "onfocus=\"f_manuel('resp_".$j."_".$i."','".$j."_".$i."','resp')\" ";

					echo "/></td>\n";
					echo "<td valign='top' style='border:0px; text-align:left;'>\n";
					echo "<label for='resp_mode_".$j."_".$i."_manuel' style='cursor: pointer;'>Manuel</label>";
					echo "</td valign='top' style='border:0px;'>\n";
					echo "<td rowspan='2' valign='top' style='border:0px;'>\n";

					/*
					echo "<div id='resp_manuel_".$j."_".$i."'>\n";
					echo "<label for='resp_acces_".$j."_".$i."' style='cursor: pointer;'><input type='checkbox' name='resp_acces_".$j."_".$i."' id='resp_acces_".$j."_".$i."' value='y' ";
					if($accessible=="y") {echo "checked ";}
					echo "/> Accessible</label>\n";
					echo "</div>\n";
					echo "<div id='resp_date_".$j."_".$i."'></div>\n";
					*/

					echo "<div align='center'>\n";
					echo "<div id='resp_".$j."_".$i."'>\n";
					if($mode=="manuel") {
						echo "<label for='resp_acces_".$j."_".$i."' style='cursor: pointer;'><input type='checkbox' name='resp_acces_".$j."_".$i."' id='resp_acces_".$j."_".$i."' value='y' ";
						echo "onchange=\"modif_couleur('resp_acces_".$j."_".$i."','resp_accessible_".$j."_".$i."');\" ";
						if($accessible=="y") {
							echo "checked ";
							echo "/> <span id='resp_accessible_".$j."_".$i."' style='color:green;'>Accessible</span></label>\n";
						}
						else {
							echo "/> <span id='resp_accessible_".$j."_".$i."' style='color:red;'>Accessible</span></label>\n";
						}
					}
					else {
						$timestamp_limite=mktime(0,0,0,$tabdate[1],$tabdate[2],$tabdate[0]);
						$timestamp_courant=time();

						if($timestamp_courant>$timestamp_limite) {echo "<span style='color:green;'>Accessible à la date du jour</span>";} else {echo "<span style='color:red;'>Inaccessible à la date du jour</span>";}

					}
					echo "</div>\n";
					echo "</div>\n";

					echo "</td>\n";
					echo "</tr>\n";

					$cal = new Calendrier("form", "resp_display_date_".$j."_".$i);

					echo "<tr>\n";
					echo "<td valign='top' style='border:0px;'><input type='radio' name='resp_mode_".$j."_".$i."' id='resp_mode_".$j."_".$i."_date' value='date' ";

					//echo "onchange=\"f_date('resp_date_".$j."_".$i."','truc $j $i')\" ";
					echo "onfocus=\"f_date('resp_".$j."_".$i."',document.getElementById('resp_display_date_".$j."_".$i."').value)\" ";

					if($mode!="manuel") {echo "checked ";}

					echo "/></td>\n";
					echo "<td valign='top' style='border:0px; text-align:left;'>\n";
					echo "<label for='resp_mode_".$j."_".$i."_date' style='cursor: pointer;'>Par date<br />";
					echo "</label>\n";
					echo "<input type='text' name='resp_display_date_".$j."_".$i."' id='resp_display_date_".$j."_".$i."' size='10' value='$display_date' onfocus=\"document.getElementById('resp_mode_".$j."_".$i."_date').checked='true';\" ";

					echo "onchange=\"f_date('resp_".$j."_".$i."',document.getElementById('resp_display_date_".$j."_".$i."').value)\" ";

					echo "/><a href='#calend' onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170).";document.getElementById('resp_mode_".$j."_".$i."_date').checked='true';\"><img src='../lib/calendrier/petit_calendrier.gif' alt='Calendrier' border='0' /></a>";
					//echo "</label>";
					echo "</td>\n";
					echo "</tr>\n";
					echo "</table>\n";

				echo "</td>\n";
			}
			echo "</tr>\n";
		}
	}
}

echo "</table>\n";
echo "<input type='hidden' name='max_per' value='$max_per' />\n";
echo "<input type='hidden' name='nb_classe' value='$j' />\n";
echo "<p align='center'><input type='submit' name='submit' value='Valider' /></p>\n";
echo "</form>\n";

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>