<?php
/**
 * Création d'évaluations cumules
 * 
 *
 * @copyright Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL, 
 * @package Carnet_de_notes
 * @subpackage affichage
 */

/*
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

/**
 * Fichiers d'initialisation
 */
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

$appel_cahier_notes = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe = old_mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group = get_group($id_groupe);
$periode_num = old_mysql_result($appel_cahier_notes, 0, 'periode');
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
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)==0) {
				$msg="Le $nom_cc n°$id_dev n'est pas associé à ce Carnet de notes.<br />";
			}
			else {
				$sql="DELETE FROM cc_notes_eval WHERE id_eval IN (SELECT id FROM cc_eval WHERE id_dev='$id_dev');";
				//echo "$sql<br />";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$del) {
					$msg="Erreur lors de la suppression des notes associées au $nom_cc n°$id_dev.<br />";
				}
				else {
					// On poursuit
					$sql="DELETE FROM cc_eval WHERE id_dev='$id_dev';";
					//echo "$sql<br />";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						$msg="Erreur lors de la suppression des évaluations associées au $nom_cc n°$id_dev.<br />";
					}
					else {
						$sql="DELETE FROM cc_dev WHERE id='$id_dev';";
						//echo "$sql<br />";
						$del=mysqli_query($GLOBALS["mysqli"], $sql);
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
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)==0) {
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
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						$msg="Erreur lors de la suppression des notes associées à l'évaluation n°$id_eval du $nom_cc n°$id_dev.<br />";
					}
					else {
						// On poursuit
						$sql="DELETE FROM cc_eval WHERE id='$id_eval';";
						//echo "$sql<br />";
						$del=mysqli_query($GLOBALS["mysqli"], $sql);
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

$message_dev = "Etes-vous sûr de vouloir supprimer ce(tte) $nom_cc, les évaluations et les notes qu\\'elle contient ?";
$message_eval = "Etes-vous sûr de vouloir supprimer cette évaluation et les notes qu\\'elle contient ?";
//**************** EN-TETE *****************
$titre_page = "Carnet de notes - Ajout/modification d'un $nom_cc";

/**
 * Entête de la page
 */
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<form enctype=\"multipart/form-data\" name= \"form0\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

echo "<div class='norme'>\n";
echo "<p class='bold'>\n";
echo "<a href='index.php?id_racine=$id_racine'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo " | <a href='add_modif_cc_dev.php?id_racine=$id_racine'>Ajouter une $nom_cc</a>";

$sql="SELECT DISTINCT ccn.id_cahier_notes, g.*, c.classe FROM cn_cahier_notes ccn, groupes g, j_groupes_professeurs jgp, j_groupes_classes jgc, classes c WHERE (login='".$_SESSION['login']."'
						AND jgp.id_groupe=ccn.id_groupe
						AND jgp.id_groupe=g.id
						AND ccn.periode='$periode_num'
						AND c.id=jgc.id_classe
						AND jgc.id_groupe=g.id
						)
						GROUP BY g.id
						ORDER BY g.name, g.description, c.classe;";
//echo "$sql<br/>";
$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
echo " | <select name='id_racine' onchange=\"document.forms['form0'].submit();\">\n";
while($lig=mysqli_fetch_object($res_grp)) {
	$sql="SELECT 1=1 FROM j_groupes_visibilite WHERE id_groupe='' AND domaine='cahier_notes' AND visible='n';";
	$test_vis=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_vis)==0) {
		echo "<option value='$lig->id_cahier_notes'";
		if($lig->id_cahier_notes==$id_racine) {echo " selected='true'";}
		echo ">";
		echo $lig->name." (<i>".$lig->description."</i>) en ".$lig->classe;
		echo "</option>\n";
	}
}
echo "</select>\n";

echo " | ";
if($periode_num>1) {
	$periode_prec=$periode_num-1;
	$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE id_groupe='$id_groupe' AND periode='$periode_prec';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		echo "<a href='".$_SERVER['PHP_SELF']."?id_racine=$lig->id_cahier_notes'><img src='../images/icons/back.png' class='icone16' title='Période $periode_prec' alt='Période $periode_prec' /></a> ";
	}
}
echo "Période $periode_num";
if($periode_num<$current_group['nb_periode']) {
	$periode_suiv=$periode_num+1;
	$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE id_groupe='$id_groupe' AND periode='$periode_suiv';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		echo "<a href='".$_SERVER['PHP_SELF']."?id_racine=$lig->id_cahier_notes'><img src='../images/icons/forward.png' class='icone16' title='Période $periode_suiv' alt='Période $periode_suiv' /></a>";
	}
}


echo "</p>\n";
echo "</div>\n";
echo "</form>\n";

echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
echo add_token_field();

echo "<h2>".$current_group['name']." (<i>".$current_group['description']."</i>) en ".$current_group['classlist_string']." (<i>période $periode_num</i>)</h2>\n";

$liste_eleves = $current_group["eleves"][$periode_num]["users"];
$nb_eleves=count($current_group["eleves"][$periode_num]["users"]);

//echo "<p>Liste des $nom_cc non rattachées à un devoir du carnet de notes&nbsp;: <br />\n";
echo "<p>Liste des $nom_cc&nbsp;: <br />\n";
//$sql="SELECT * FROM cc_dev WHERE id_groupe='$id_groupe' AND id_cn_dev NOT IN (SELECT id FROM cn_devoirs);";
$sql="SELECT * FROM cc_dev WHERE id_groupe='$id_groupe';";
//echo "$sql<br />\n";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	//echo "Aucun $nom_cc non rattachée n'est encore définie.</p>\n";
	echo "Aucun $nom_cc n'est encore définie.</p>\n";
}
else {
	echo "<ul>\n";
	while($lig=mysqli_fetch_object($res)) {
		echo "<li>\n";
		echo "<p>";
		echo "$lig->nom_court ";
		echo "<a href='visu_cc.php?id_racine=$id_racine&amp;id_dev=$lig->id'>Visualisation</a>";
		echo " | ";
		echo "<a href='add_modif_cc_dev.php?id_racine=$id_racine&amp;id_dev=$lig->id'>Configuration</a>";
		echo " | ";
		echo "<a href='add_modif_cc_eval.php?id_racine=$id_racine&amp;id_dev=$lig->id'>Ajouter une évaluation</a>";
		echo " | ";
		echo "<a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$lig->id&amp;action=suppr_dev".add_token_in_url()."' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($lig->nom_court)."', '".$message_dev."')\">Supprimer</a>";
		echo " | ";
		if($ver_periode[$periode_num]=='N') {
			if(file_exists("transfert_cc_vers_cn.php")) {
				echo "<a href='transfert_cc_vers_cn.php?id_racine=$id_racine&amp;id_dev_cc=$lig->id".add_token_in_url()."'>Transférer vers le carnet de notes</a>";
			}
			else {
				echo "Transférer vers le carnet de notes (<span style='color:red'>A FAIRE</span>)";
			}
		}
		else {
			echo "Transfert impossible vers le carnet de notes (<em>Période $periode_num fermée</em>)";
		}

		if($lig->id_cn_dev!='0') {
			echo " <img src='../images/icons/chaine.png' class='icone16' title='Devoir rattaché à ".get_infos_devoir($lig->id_cn_dev)."' />";
		}

		echo "<br />\n";
		$sql="SELECT * FROM cc_eval WHERE id_dev='$lig->id' ORDER BY date, nom_court;";
		$res2=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res2)>0) {
			echo "<ul>\n";
			while($lig2=mysqli_fetch_object($res2)) {
				echo "<li>\n";
				echo "$lig2->nom_court ";
				$sql="SELECT 1=1 FROM cc_notes_eval WHERE id_eval='$lig2->id' AND statut!='v';";
				$res_nb=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_notes=mysqli_num_rows($res_nb);
				if($nb_notes!=$nb_eleves) {$couleur='red';} else {$couleur='green';}
				echo "<a href='saisie_notes_cc.php?id_racine=$id_racine&amp;id_dev=$lig->id&amp;id_eval=$lig2->id'>Saisir</a> (<span style='color:$couleur'>$nb_notes/$nb_eleves</span>)";
				echo " | ";
				echo "<a href='add_modif_cc_eval.php?id_racine=$id_racine&amp;id_dev=$lig->id&amp;id_eval=$lig2->id'>Configuration</a>";
				echo " | ";
				echo "<a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$lig->id&amp;id_eval=$lig2->id&amp;action=suppr_eval".add_token_in_url()."' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($lig->nom_court)."', '".$message_eval."')\">Supprimer</a>";
				echo "</li>\n";
			}
			echo "</ul>\n";
		}
		echo "</li>\n";
	}
	echo "</ul>\n";
}

//echo "<p>Liste des $nom_cc rattachées à un devoir du carnet de notes&nbsp;: <br />\n";
//echo "<span style='color:red'>A FAIRE</span>";

echo "</form>\n";
echo "<br />\n";

echo "<p style='text-indent:-3em; margin-left:3em;'><em>NOTE&nbsp;:</em><br />Les $nom_cc ne sont pas rattachées à une période.<br />Elles peuvent être à cheval sur plusieurs périodes.<br />Cependant, le transfert des notes vers un carnet de notes n'est possible que vers une période ouverte en saisie.</p>\n";

/**
 * inclusion du pied de page
 */
require("../lib/footer.inc.php");
?>
