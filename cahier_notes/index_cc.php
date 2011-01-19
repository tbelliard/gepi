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

$id_racine = isset($_POST["id_racine"]) ? $_POST["id_racine"] : (isset($_GET["id_racine"]) ? $_GET["id_racine"] : NULL);

/*
// A FAIRE LORSQU'ON TENTE DE CREER UN DEVOIR D'APRES LE CC

// On teste si la periode est vérrouillée !
if ($current_group["classe"]["ver_periode"]["all"][$periode_num] <= 1) {
	$mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes dont la période est bloquée !");
	header("Location: index.php?msg=$mess");
	die();
}
*/

if(!isset($id_racine)) {
	$mess="Racine non précisée pour $nom_cc.<br />";
	header("Location: index.php?msg=$mess");
	die();
}

// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
    header("Location: index.php?msg=$mess");
    die();
}

$appel_cahier_notes = mysql_query("SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe = mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group = get_group($id_groupe);
$periode_num = mysql_result($appel_cahier_notes, 0, 'periode');
include "../lib/periodes.inc.php";

$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];

// enregistrement des données
if (isset($_GET['action'])) {
	check_token();

	if($_GET['action']=='suppr_dev') {
		$id_dev=isset($_GET['id_dev']) ? $_GET['id_dev'] : '';
		$id_dev=preg_replace('/[^0-9]/','',$id_dev);
		if($id_dev=='') {
			$msg="Identifiant de $nom_cc invalide.<br />";
		}
		else {
			$sql="SELECT 1=1 FROM cc_dev WHERE id='$id_dev' AND id_groupe='$id_groupe';";
			//echo "$sql<br />";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0) {
				$msg="Le $nom_cc n°$id_dev n'est pas associé à ce Carnet de notes.<br />";
			}
			else {
				$sql="DELETE FROM cc_notes_eval WHERE id_eval IN (SELECT id FROM cc_eval WHERE id_dev='$id_dev');";
				//echo "$sql<br />";
				$del=mysql_query($sql);
				if(!$del) {
					$msg="Erreur lors de la suppression des notes associées au $nom_cc n°$id_dev.<br />";
				}
				else {
					// On poursuit
					$sql="DELETE FROM cc_eval WHERE id_dev='$id_dev';";
					//echo "$sql<br />";
					$del=mysql_query($sql);
					if(!$del) {
						$msg="Erreur lors de la suppression des évaluations associées au $nom_cc n°$id_dev.<br />";
					}
					else {
						$sql="DELETE FROM cc_dev WHERE id='$id_dev';";
						//echo "$sql<br />";
						$del=mysql_query($sql);
						if(!$del) {
							$msg="Erreur lors de la suppression du $nom_cc n°$id_dev.<br />";
						}
						else {
							$msg="Suppression du $nom_cc n°$id_dev effectuée.<br />";
						}
					}
				}
			}
		}
	}
	elseif($_GET['action']=='suppr_eval') {
		$id_dev=isset($_GET['id_dev']) ? $_GET['id_dev'] : '';
		$id_dev=preg_replace('/[^0-9]/','',$id_dev);
		if($id_dev=='') {
			$msg="Identifiant de $nom_cc invalide.<br />";
		}
		else {
			$sql="SELECT 1=1 FROM cc_dev WHERE id='$id_dev' AND id_groupe='$id_groupe';";
			//echo "$sql<br />";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0) {
				$msg="Le $nom_cc n°$id_dev n'est pas associé à ce Carnet de notes.<br />";
			}
			else {
				$id_eval=isset($_GET['id_eval']) ? $_GET['id_eval'] : '';
				$id_eval=preg_replace('/[^0-9]/','',$id_eval);
				if($id_eval=='') {
					$msg="Identifiant d'évaluation invalide.<br />";
				}
				else {
					$sql="DELETE FROM cc_notes_eval WHERE id_eval='$id_eval';";
					//echo "$sql<br />";
					$del=mysql_query($sql);
					if(!$del) {
						$msg="Erreur lors de la suppression des notes associées à l'évaluation n°$id_eval du $nom_cc n°$id_dev.<br />";
					}
					else {
						// On poursuit
						$sql="DELETE FROM cc_eval WHERE id='$id_eval';";
						//echo "$sql<br />";
						$del=mysql_query($sql);
						if(!$del) {
							$msg="Erreur lors de la suppression de l'évaluation n°$id_eval du $nom_cc n°$id_dev.<br />";
						}
						else {
							$msg="Suppression de l'évaluation n°$id_eval du $nom_cc n°$id_dev effectuée.<br />";
						}
					}
				}
			}
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Carnet de notes - Ajout/modification d'un $nom_cc";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
echo add_token_field();

echo "<div class='norme'>\n";
echo "<p class='bold'>\n";
echo "<a href='index.php?id_racine=$id_racine'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo " | <a href='add_modif_cc_dev.php?id_racine=$id_racine'>Ajouter un $nom_cc</a>";
echo "</p>\n";
echo "</div>\n";

$liste_eleves = $current_group["eleves"][$periode_num]["users"];
$nb_eleves=count($current_group["eleves"][$periode_num]["users"]);

echo "<p>Liste des $nom_cc non rattachés à un devoir du carnet de notes&nbsp;: <br />\n";
$sql="SELECT * FROM cc_dev WHERE id_groupe='$id_groupe' AND id_cn_dev NOT IN (SELECT id FROM cn_devoirs);";
//echo "$sql<br />\n";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "Aucun $nom_cc non rattaché n'est encore défini.</p>\n";
}
else {
	echo "<ul>\n";
	while($lig=mysql_fetch_object($res)) {
		echo "<li>\n";
		echo "<p>";
		echo "$lig->nom_court ";
		echo "<a href='visu_cc.php?id_racine=$id_racine&amp;id_dev=$lig->id'>Visualisation</a>";
		echo " | ";
		echo "<a href='add_modif_cc_dev.php?id_racine=$id_racine&amp;id_dev=$lig->id'>Configuration</a>";
		echo " | ";
		echo "<a href='add_modif_cc_eval.php?id_racine=$id_racine&amp;id_dev=$lig->id'>Ajouter une évaluation</a>";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$lig->id&amp;action=suppr_dev".add_token_in_url()."'>Supprimer</a>";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$lig->id".add_token_in_url()."'></a>Transférer vers le carnet de notes (<span style='color:red'>A FAIRE</span>)";
		echo "<br />\n";
		$sql="SELECT * FROM cc_eval WHERE id_dev='$lig->id' ORDER BY date, nom_court;";
		$res2=mysql_query($sql);
		if(mysql_num_rows($res2)>0) {
			echo "<ul>\n";
			while($lig2=mysql_fetch_object($res2)) {
				echo "<li>\n";
				echo "$lig2->nom_court ";
				$sql="SELECT 1=1 FROM cc_notes_eval WHERE id_eval='$lig2->id' AND statut!='v';";
				$res_nb=mysql_query($sql);
				$nb_notes=mysql_num_rows($res_nb);
				if($nb_notes!=$nb_eleves) {$couleur='red';} else {$couleur='green';}
				echo "<a href='saisie_notes_cc.php?id_racine=$id_racine&amp;id_dev=$lig->id&amp;id_eval=$lig2->id'>Saisir</a> (<span style='color:$couleur'>$nb_notes/$nb_eleves</span>)";
				echo " | ";
				echo "<a href='add_modif_cc_eval.php?id_racine=$id_racine&amp;id_dev=$lig->id&amp;id_eval=$lig2->id'>Configuration</a>";
				echo " | ";
				echo "<a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$lig->id&amp;id_eval=$lig2->id&amp;action=suppr_eval".add_token_in_url()."'>Supprimer</a>";
				echo "</li>\n";
			}
			echo "</ul>\n";
		}
		echo "</li>\n";
	}
	echo "</ul>\n";
}

echo "<p>Liste des $nom_cc rattachés à un devoir du carnet de notes&nbsp;: <br />\n";
echo "<span style='color:red'>A FAIRE</span>";
/*
$sql="SELECT * FROM cc_dev ccd, WHERE id_groupe='$id_groupe' AND id_cn_dev IN (SELECT id FROM cn_devoirs) ORDER BY ;";
// BOUCLER SUR LES PERIODES... récupérer les cn_cahier_notes, puis devoirs associés...

$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "Aucun $nom_cc n'est encore rattaché à un devoir du carnet de notes.</p>\n";
}
else {

}
*/


echo "</form>\n";
echo "<br />\n";
require("../lib/footer.inc.php");
?>
