<?php
/*
 * @version: $Id$
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
//==============================
// PREPARATIFS boireaus 20080422
// Pour passer à no_anti_inject comme pour les autres saisies d'appréciations
// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$mode_commentaire_20080422="";
//$mode_commentaire_20080422="no_anti_inject";

if($mode_commentaire_20080422=="no_anti_inject") {
	$variables_non_protegees = 'yes';
}
//==============================

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


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
	die("Le module n'est pas activé.");
}

require('cc_lib.php');

unset($id_racine);
$id_racine = isset($_POST["id_racine"]) ? $_POST["id_racine"] : (isset($_GET["id_racine"]) ? $_GET["id_racine"] : NULL);
// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
    header("Location: index.php?msg=$mess");
    die();
}

$appel_cahier_notes=mysql_query("SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe=mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group=get_group($id_groupe);
$periode_num=mysql_result($appel_cahier_notes, 0, 'periode');
include "../lib/periodes.inc.php";

unset($id_dev);
$id_dev = isset($_POST["id_dev"]) ? $_POST["id_dev"] : (isset($_GET["id_dev"]) ? $_GET["id_dev"] : NULL);
if(!isset($id_dev)) {
	$mess="$nom_cc non précisé.<br />";
	header("Location: index_cc.php?id_racine=$id_racine&msg=$mess");
	die();
}

$sql="SELECT * FROM cc_dev WHERE id='$id_dev' AND id_groupe='$id_groupe';";
$query=mysql_query($sql);
if($query) {
	$id_cn_dev=mysql_result($query, 0, 'id_cn_dev');
	$nom_court_dev=mysql_result($query, 0, 'nom_court');
	$nom_complet_dev=mysql_result($query, 0, 'nom_complet');
	$description_dev=mysql_result($query, 0, 'description');
	$precision=mysql_result($query, 0, 'arrondir');
}
else {
	header("Location: index.php?msg=".rawurlencode("Le numéro de devoir n est pas associé à ce groupe."));
	die();
}

$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];

//debug_var();
//-------------------------------------------------------------------------------------------------------------------

if(isset($_GET['export_csv'])) {
	$csv="INFO_DEV;$id_dev;$nom_court_dev;$nom_complet_dev;$precision;;".";\r\n";

	$sql="SELECT * FROM cc_eval WHERE id_dev='$id_dev' ORDER BY date, nom_court, nom_complet;";
	//echo "$sql<br />";
	$res_eval=mysql_query($sql);
	if(mysql_num_rows($res_eval)==0) {
		$msg="Aucune évaluation n'est associée au $nom_cc n°$id_dev<br />";
	}
	else {
		$cpt=0;
		$tab_eval=array();
		$tab_ele=array();

		$ligne1="INFO_EV;NOM_COURT_EVAL;;;;";
		$ligne2="INFO_EV;DATE_EVAL;;;;";
		$ligne3="INFO_EV;NOTE_SUR_EVAL;;;;";
		$ligne4="INFO_EV;LOGIN;NOM;PRENOM;CLASSE;";

		while($lig_eval=mysql_fetch_object($res_eval)) {
			$csv.="INFO_EVAL;$lig_eval->id;$lig_eval->nom_court;$lig_eval->nom_complet;".formate_date($lig_eval->date).";$lig_eval->note_sur;".";\r\n";

			$ligne1.=$lig_eval->nom_court.";";
			$ligne2.=formate_date($lig_eval->date).";";
			$ligne3.=strtr($lig_eval->note_sur,'.',',').";";
			$ligne4.=";";

			$tab_eval[$cpt]['id_eval']=$lig_eval->id;
			$tab_eval[$cpt]['note_sur']=$lig_eval->note_sur;

			$sql="SELECT cc.* FROM cc_notes_eval cc WHERE cc.id_eval='$lig_eval->id' ORDER BY cc.login;";
			//echo "$sql<br />";
			$res_en=mysql_query($sql);
			if(mysql_num_rows($res_en)>0) {
				while($lig_en=mysql_fetch_object($res_en)) {

					//if(!in_array($lig_en->login,$tab_ele)) {
					if(!isset($tab_ele[$lig_en->login])) {
						$sql="SELECT c.classe, e.nom, e.prenom FROM classes c, eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe=c.id AND jec.periode='$periode_num' AND e.login='$lig_en->login';";
						//echo "$sql<br />";
						$res_ele=mysql_query($sql);
						if(mysql_num_rows($res_ele)>0) {
							$lig_ele=mysql_fetch_object($res_ele);
							$tab_ele[$lig_en->login]['classe']=$lig_ele->classe;
							$tab_ele[$lig_en->login]['nom']=$lig_ele->nom;
							$tab_ele[$lig_en->login]['prenom']=$lig_ele->prenom;
						}
						else {
							$tab_ele[$lig_en->login]['classe']='Classe_inconnue';
							$tab_ele[$lig_en->login]['nom']='Nom_inconnu';
							$tab_ele[$lig_en->login]['prenom']='Prenom_inconnu';
						}
					}

					if($lig_en->statut=='v') {
						$tab_ele[$lig_en->login]['eval'][$lig_eval->id]="";
					}
					elseif($lig_en->statut!='') {
						$tab_ele[$lig_en->login]['eval'][$lig_eval->id]=$lig_en->statut;
					}
					else {
						$tab_ele[$lig_en->login]['eval'][$lig_eval->id]=$lig_en->note;
					}
				}
			}

			$cpt++;
		}

		$ligne1.=";\r\n";
		$ligne2.=";\r\n";
		$ligne3.=";\r\n";
		$ligne4.="TOTAL;TOTAL_SUR;MOYENNE;\r\n";

		$csv.=$ligne1;
		$csv.=$ligne2;
		$csv.=$ligne3;
		$csv.=$ligne4;

		foreach($tab_ele as $ele_login => $tmp_tab) {
			$total=0;
			$total_sur=0;

			$csv.="ELEVE;".$ele_login.";".$tmp_tab['nom'].";".$tmp_tab['prenom'].";".$tmp_tab['classe'].";";
			for($i=0;$i<count($tab_eval);$i++) {
				if(isset($tmp_tab['eval'][$tab_eval[$i]['id_eval']])) {
					$csv.=strtr($tmp_tab['eval'][$tab_eval[$i]['id_eval']],'.',',');

					if(($tmp_tab['eval'][$tab_eval[$i]['id_eval']]!='')&&(preg_match('/^[0-9.]*$/',$tmp_tab['eval'][$tab_eval[$i]['id_eval']]))) {
						$total+=$tmp_tab['eval'][$tab_eval[$i]['id_eval']];
						$total_sur+=$tab_eval[$i]['note_sur'];
					}
				}
				$csv.=";";
			}
			$csv.=strtr($total,'.',',').";".strtr($total_sur,'.',',').";";
			if($total_sur>0) {
				$moy=strtr(precision_arrondi(20*$total/$total_sur,$precision),'.',',');
			}
			else {
				$moy='-';
			}
			$csv.="$moy;\r\n";
		}

		header('Content-Type:  text/x-csv');
		$now = gmdate('D, d M Y H:i:s') . ' GMT';
		header('Expires: ' . $now);
		// lem9 & loic1: IE need specific headers
		//nom du fichier à telecharger
	
		if (my_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
			header('Content-Disposition: inline; filename="cc_dev_'.$id_dev.'_'.date("dmY").'.csv"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header('Content-Disposition: attachment; filename="cc_dev_'.$id_dev.'_'.date("dmY").'.csv"');
			header('Pragma: no-cache');
		}
		echo $csv;
		die();
	}
}

//$message_enregistrement = "Les modifications ont été enregistrées !";
//$themessage  = 'Des notes ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Visualisation des notes CC";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var();

//unset($_SESSION['chemin_retour']);

?>
<script type="text/javascript" language=javascript>
chargement = false;
</script>

<?php
echo "<p id='LiensSousBandeau' class='bold'>\n";
echo "<a href=\"index_cc.php?id_racine=$id_racine\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";
echo " | Export <a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$id_dev&amp;export_csv=y'>CSV</a>";
//echo "|";
echo "</p>\n";

echo "<h2 class='noprint'>$nom_cc n°$id_dev&nbsp;: $nom_court_dev (<i>$nom_complet_dev</i>)</h2>\n";

$sql="SELECT * FROM cc_eval WHERE id_dev='$id_dev' ORDER BY date, nom_court;";
$res2=mysql_query($sql);
if(mysql_num_rows($res2)>0) {
	$cc_eval=array();
	$i=0;
	while($lig2=mysql_fetch_object($res2)) {
		$cc_eval[$i]=array();
		$cc_eval[$i]['nom_court']=$lig2->nom_court;
		$cc_eval[$i]['nom_complet']=$lig2->nom_court;
		$cc_eval[$i]['description']=$lig2->description;
		$cc_eval[$i]['note_sur']=$lig2->note_sur;
		$cc_eval[$i]['date']=formate_date($lig2->date);

		$sql="SELECT * FROM cc_notes_eval WHERE id_eval='$lig2->id' ORDER BY login;";
		$res_note=mysql_query($sql);
		if(mysql_num_rows($res_note)>0) {
			while($lig_note=mysql_fetch_object($res_note)) {
				if($lig_note->statut=='v') {
					$cc_eval[$i]['note'][$lig_note->login]='';
				}
				elseif($lig_note->statut!='') {
					$cc_eval[$i]['note'][$lig_note->login]=$lig_note->statut;
				}
				else {
					$cc_eval[$i]['note'][$lig_note->login]=$lig_note->note;
				}
			}
		}

		$i++;
	}
	echo "</ul>\n";
}

$nb_eval=$i;

$liste_eleves = $current_group["eleves"][$periode_num]["users"];

$i=0;
$alt=1;
foreach ($liste_eleves as $eleve) {
	$eleve_login[$i] = $eleve["login"];
	$eleve_nom[$i] = $eleve["nom"];
	$eleve_prenom[$i] = $eleve["prenom"];
	$eleve_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["classe"];
	$eleve_id_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["id"];

	echo "<div style='float:left; width:30%; margin-left: 2em;; margin-bottom: 2em'>\n";
	echo "<table class='boireaus table_no_split' summary=\"$nom_cc de $eleve_nom[$i] $eleve_prenom[$i]\">\n";
	echo "<tr class='table_no_split'>\n";
	echo "<th colspan='$nb_eval'><b>$nom_cc</b>&nbsp;: $nom_court_dev</th>\n";
	echo "</tr>\n";

	echo "<tr class='table_no_split'>\n";
	echo "<th colspan='$nb_eval'><b>Classe</b>&nbsp;: $eleve_classe[$i]</th>\n";
	//echo "<th rowspan='2'><b>$nom_cc</b>&nbsp;: $nom_court_dev</th>\n";
	echo "</tr>\n";

	echo "<tr class='table_no_split'>\n";
	echo "<th colspan='$nb_eval'><b>Elève</b>&nbsp;: $eleve_nom[$i] $eleve_prenom[$i]</th>\n";
	echo "</tr>\n";

	echo "<tr class='table_no_split'>\n";
	echo "<th>Nom</th>\n";
	echo "<th>Date</th>\n";
	echo "<th>Note</th>\n";
	echo "<th>Sur</th>\n";
	echo "</tr>\n";

	$total=0;
	$total_sur=0;
	for($j=0;$j<count($cc_eval);$j++) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover table_no_split'>\n";
		echo "<td>".$cc_eval[$j]['nom_court']."</td>\n";
		echo "<td>".$cc_eval[$j]['date']."</td>\n";
		echo "<td>";
		if(isset($cc_eval[$j]['note'][$eleve_login[$i]])) {
			echo $cc_eval[$j]['note'][$eleve_login[$i]];
			if(($cc_eval[$j]['note'][$eleve_login[$i]]!='')&&(preg_match('/^[0-9.]*$/',$cc_eval[$j]['note'][$eleve_login[$i]]))) {
				$total+=$cc_eval[$j]['note'][$eleve_login[$i]];
				$total_sur+=$cc_eval[$j]['note_sur'];
			}
		}
		echo "</td>\n";
		echo "<td>";
		echo $cc_eval[$j]['note_sur'];
		echo "</td>\n";
		echo "</tr>\n";
	}

	echo "<tr class='table_no_split'>\n";
	echo "<th>Total</th>\n";
	echo "<th>-</th>\n";
	echo "<th>$total</th>\n";
	echo "<th>$total_sur</th>\n";
	echo "</tr>\n";

	echo "<tr class='table_no_split'>\n";
	echo "<th>Moyenne</th>\n";
	echo "<th>-</th>\n";
	if($total_sur!=0) {
		//$moy=round(10*20*$total/$total_sur)/10;
		$moy=precision_arrondi(20*$total/$total_sur,$precision);
	}
	else {
		$moy='-';
	}
	echo "<th>$moy</th>\n";
	echo "<th>20</th>\n";
	echo "</tr>\n";

	echo "</table>\n";
	//echo "<br />\n";
	echo "</div>\n";

	$i++;
}

echo "<script type='text/javascript'>
	document.getElementById('bandeau').className+=' noprint';
	if(document.getElementById('essaiMenu')) {document.getElementById('essaiMenu').className+=' noprint';}
	document.getElementById('LiensSousBandeau').className+=' noprint';
</script>

<style type='text/css'>
// Ca n'a pas l'air de fonctionner
.table_no_split {
	page-break-inside: avoid;
}
</style>\n";

require("../lib/footer.inc.php");
?>
