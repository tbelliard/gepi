<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
include("../lib/initialisationsPropel.inc.php");
require_once("./fonctions_annees_anterieures.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();};

// INSERT INTO droits VALUES ('/mod_annees_anterieures/gerer_annees_anterieures.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les années antérieures', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$action=isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : NULL);
$annee=isset($_POST['annee']) ? $_POST['annee'] : (isset($_GET['annee']) ? $_GET['annee'] : NULL);
$annee_nouveau_nom=isset($_POST['annee_nouveau_nom']) ? $_POST['annee_nouveau_nom'] : (isset($_GET['annee_nouveau_nom']) ? $_GET['annee_nouveau_nom'] : NULL);

// Si le module n'est pas activé...
if($gepiSettings['active_annees_anterieures'] !="y"){
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";

// Suppression des données archivées pour une année donnée.
if (isset($_GET['action']) and ($_GET['action']=="supp_annee")) {
	check_token();

	$sql="DELETE FROM archivage_disciplines WHERE annee='".$_GET["annee_supp"]."';";
	$res_suppr1=mysql_query($sql);

	// Maintenant, on regarde si l'année est encore utilisée dans archivage_types_aid
	// Sinon, on supprime les entrées correspondantes à l'année dans archivage_eleves2 car elles ne servent plus à rien.
	$test = sql_query1("select count(annee) from archivage_types_aid where annee='".$_GET['annee_supp']."'");
	if ($test == 0) {
		$sql="DELETE FROM archivage_eleves2 WHERE annee='".$_GET["annee_supp"]."';";
		$res_suppr2=mysql_query($sql);
	} else {
		$res_suppr2 = 1;
	}

	$sql="DELETE FROM archivage_ects WHERE annee='".$_GET["annee_supp"]."';";
	$res_suppr3=mysql_query($sql);

	$sql="DELETE FROM archivage_appreciations_aid WHERE annee='".$_GET["annee_supp"]."';";
	$res_suppr4=mysql_query($sql);

	$sql="DELETE FROM archivage_aids WHERE annee='".$_GET["annee_supp"]."';";
	$res_suppr5=mysql_query($sql);

	// Maintenant, il faut supprimer les données élèves qui ne servent plus à rien
	suppression_donnees_eleves_inutiles();

	if (($res_suppr1) and ($res_suppr2) and ($res_suppr3) and ($res_suppr4) and ($res_suppr5)) {
		$msg = "La suppression des données a été correctement effectuée.";
	} else {
		$msg = "Un ou plusieurs problèmes ont été rencontrés lors de la suppression.";
	}

}
elseif((isset($action))&&($action=="renommer_annee")&&(isset($annee))&&($annee!="")&&(isset($annee_nouveau_nom))&&($annee_nouveau_nom!="")) {
	check_token();

	$sql="SELECT 1=1 FROM archivage_disciplines WHERE annee='".mysql_real_escape_string($annee)."';";
	$res_annee=mysql_query($sql);
	if(mysql_num_rows($res_annee)==0) {
		$msg="L'année <strong>$annee</strong> n'est pas enregistrée comme année antérieure.<br />\n";
		unset($action);
	}
	else {
		$sql="SELECT 1=1 FROM archivage_disciplines WHERE annee='".mysql_real_escape_string($annee_nouveau_nom)."';";
		$res_annee=mysql_query($sql);
		if(mysql_num_rows($res_annee)>0) {
			$msg="Le nom <strong>$annee_nouveau_nom</strong> est déjà pris pour une autre année antérieure.<br />\n";
		}
		else {
			$msg="";
			$table=array('archivage_aids', 'archivage_appreciations_aid', 'archivage_disciplines', 'archivage_ects', 'archivage_eleves2', 'archivage_types_aid');
			for($i=0;$i<count($table);$i++) {
				$sql="UPDATE $table[$i] SET annee='".mysql_real_escape_string($annee_nouveau_nom)."' WHERE annee='".mysql_real_escape_string($annee)."';";
				$res=mysql_query($sql);
				if(!$res) {
					$msg.="Erreur lors du renommage dans la table $table.<br />\n";
				}
			}
			if($msg=="") {
				$msg="Renommage effectué.<br />";
				unset($action);
			}
		}
	}
}

$themessage  = 'Etes-vous sûr de vouloir supprimer toutes les données concerant cette année ?';
//**************** EN-TETE *****************
$titre_page = "Gérer les années antérieures";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

if((!isset($action))||($action!='renommer_annee')) {
	echo "<div class='norme'><p class=bold><a href='";
	echo "./index.php";
	echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";
	echo "</p></div>\n";

	$sql="SELECT DISTINCT annee FROM archivage_disciplines ORDER BY annee";
	$res_annee=mysql_query($sql);
	if(mysql_num_rows($res_annee)==0){
		echo "<p>Concernant les données autres que les AIDs, aucune année n'est encore sauvegardée.</p>\n";
	}
	else{
		echo "<p>Voici la liste des années sauvegardées:</p>\n";
		echo "<ul>\n";
		while($lig_annee=mysql_fetch_object($res_annee)){
			$annee_scolaire=$lig_annee->annee;
			echo "<li><b>Année $annee_scolaire (<a href='".$_SERVER['PHP_SELF']."?action=supp_annee&amp;annee_supp=".$annee_scolaire.add_token_in_url()."'>Supprimer</a> - <a href='".$_SERVER['PHP_SELF']."?action=renommer_annee&amp;annee=".$annee_scolaire.add_token_in_url()."'>Renommer</a>) :<br /></b> ";
			$sql="SELECT DISTINCT classe FROM archivage_disciplines WHERE annee='$annee_scolaire' ORDER BY classe;";
			$res_classes=mysql_query($sql);
			if(mysql_num_rows($res_classes)==0){
				echo "Aucune classe???";
			}
			else{
				$lig_classe=mysql_fetch_object($res_classes);
				echo $lig_classe->classe;

				while($lig_classe=mysql_fetch_object($res_classes)){
					echo ", ".$lig_classe->classe;
				}
			}
			echo "</li>\n";
		}
		echo "</ul>\n";
		echo "<p><br /></p>\n";

	}


}
else {
	echo "<div class='norme'><p class=bold><a href='";
	echo "./index.php";
	echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	echo "</div>\n";

	$annee=isset($_POST['annee']) ? $_POST['annee'] : (isset($_GET['annee']) ? $_GET['annee'] : NULL);

	$sql="SELECT 1=1 FROM archivage_disciplines WHERE annee='".mysql_real_escape_string($annee)."';";
	$res_annee=mysql_query($sql);
	if(mysql_num_rows($res_annee)==0) {
		echo "<p style='color:red'>L'année <strong>$annee</strong> n'est pas enregistrée comme année antérieure.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT DISTINCT annee FROM archivage_disciplines WHERE annee!='".mysql_real_escape_string($annee)."' ORDER BY annee";
	$res_annee=mysql_query($sql);
	if(mysql_num_rows($res_annee)>0) {
		echo "<p>Vous souhaitez renommer l'année <strong>$annee</strong>.</p>\n";
		echo "<p>Certains noms sont déjà pris&nbsp;: ";
		$cpt=0;
		while($lig_annee=mysql_fetch_object($res_annee)){
			if($cpt>0) {echo ", ";}
			echo $lig_annee->annee;
			$cpt++;
		}
		echo "</p>\n";
	}

	echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo add_token_field();
	echo "<input type=\"hidden\" name='action' value=\"renommer_annee\" />\n";
	echo "<input type=\"hidden\" name='annee' value=\"$annee\" />\n";
	echo "Nouveau nom&nbsp;: <input type=\"text\" name='annee_nouveau_nom' value=\"$annee\" />\n";
	echo " <input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" />\n";
	echo "</form>\n";
}

echo "<br />\n";
require("../lib/footer.inc.php");
?>
