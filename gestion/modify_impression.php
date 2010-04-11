<?php
/*
 * $Id$
 *
 * Copyright 2001-2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
include("../fckeditor/fckeditor.php") ;

if(!isset($msg)){$msg="";}

if (isset($_POST['ok'])) {
	$error = false;

	if	(isset($_POST['impression_personnelFCK'])) {
		$imp = html_entity_decode_all_version($_POST['impression_personnelFCK']);
		if (!saveSetting("Impression", $imp)) {
			$msg .= "Erreur lors de l'enregistrement de la fiche bienvenue pour le personnel !";
			$erreur = true;
		}
    }

    if	(isset($_POST['impression_parentFCK'])) {
		$imp = html_entity_decode_all_version($_POST['impression_parentFCK']);
		if (!saveSetting("ImpressionFicheParent", $imp)) {
			$msg .= "Erreur lors de l'enregistrement de la fiche bienvenue pour les ".$gepiSettings['denomination_responsables']." !";
			$erreur = true;
		}
    }

    if (isset($_POST['impression_eleveFCK'])) {
		$imp = html_entity_decode_all_version($_POST['impression_eleveFCK']);
		if (!saveSetting("ImpressionFicheEleve", $imp)) {
			$msg .= "Erreur lors de l'enregistrement de la fiche bienvenue pour les ".$gepiSettings['denomination_eleves']." !";
			$erreur = true;
		}
    }


    $nb = isset($_POST['nb_impression']) ? (is_numeric($_POST['nb_impression']) ? $_POST['nb_impression'] : "1") : 1;
    if (!saveSetting("ImpressionNombre", $nb)) {
    	$error = true;
    }
    $nb = isset($_POST['nb_impression_parent']) ? (is_numeric($_POST['nb_impression_parent']) ? $_POST['nb_impression_parent'] : "1") : 1;
    if (!saveSetting("ImpressionNombreParent", $nb)) {
    	$error = true;
    }
    $nb = isset($_POST['nb_impression_eleve']) ? (is_numeric($_POST['nb_impression_eleve']) ? $_POST['nb_impression_eleve'] : "1") : 1;
    if (!saveSetting("ImpressionNombreEleve", $nb)) {
    	$error = true;
    }

    if (!$error) {
    	$msg = "Les paramètres ont bien été enregistrés.";
    }
}
//**************** EN-TETE *****************
$titre_page = "Outil de gestion | Impression des paramètres";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<form enctype="multipart/form-data" action="modify_impression.php" method=post name=formulaire>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>|<a href="modify_impression.php?fiche=personnels"> Fiche Personnels Etablissement </a>|<a href="modify_impression.php?fiche=responsables"> Fiche <?php echo $gepiSettings['denomination_responsables']; ?></a>|<a href="modify_impression.php?fiche=eleves"> Fiche <?php echo $gepiSettings['denomination_eleves'];?> </a></p>

<?php

if (!loadSettings()) {
    die("Erreur chargement settings");
}

echo "<br />";
echo "<p>Lors de la création d'un utilisateur, il vous est possible d'imprimer une feuille d'information contenant les paramètres de connexion à GEPI, le texte diffère selon le statut de l'utilisateur créé. Attention, ce texte est au format html !</p>\n";

$fiche=isset($_POST["fiche"]) ? $_POST["fiche"] : (isset($_GET["fiche"]) ? $_GET["fiche"] : "personnels");

//echo "<table width=600>\n";
//echo "<tr>\n<td>\n";
echo "<div style='width: 600px;'>\n";

switch ($fiche) {
case 'personnels' :
		$impression = getSettingValue("Impression");
		$nb_impression = getSettingValue("ImpressionNombre");

		echo "<h3 class='gepi' align='center'>Fiche d'information : Personnels de l'établissement</h3>\n";
		echo "<p>Cette fiche est imprimée lors de la création d'un nouvel utilisateur au statut 'professeur', 'cpe', 'scolarite' .</p>\n";
		echo "<p>Nombre de fiches à imprimer par page : \n";
		echo "<select name='nb_impression' size='1'>\n";
		for ($i=1;$i<25;$i++) {
			echo "<option value='$i'";
			if ($nb_impression == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<input type=\"hidden\" name=\"fiche\" value=\"$fiche\" />\n";
		echo "<br />Conseil : faites des tests pour éviter de mauvaises surprises lors de l'impression en masse.</p>\n";
		echo "<br /><i>Mise en forme du message :</i>\n";

		$oFCKeditor = new FCKeditor('impression_personnelFCK') ;
		$oFCKeditor->BasePath = '../fckeditor/' ;
		$oFCKeditor->Config['DefaultLanguage']  = 'fr' ;
		$oFCKeditor->ToolbarSet = 'Basic' ;
		$oFCKeditor->Value      = $impression ;
		$oFCKeditor->Create() ;

		//echo "</div>\n";
    break;

case 'responsables' :
		$impression_parent = getSettingValue("ImpressionFicheParent");
		$nb_impression_parent = getSettingValue("ImpressionNombreParent");

		echo "<h3 class='gepi' align='center'>Fiche d'information : ".$gepiSettings['denomination_responsables']."</h3>\n";
		echo "<p>Cette fiche est imprimée lors de la création d'un nouvel utilisateur au statut 'responsable'.</p>\n";
		echo "<p>Nombre de fiches à imprimer par page : \n";
		echo "<select name='nb_impression_parent' size='1'>\n";
		for ($i=1;$i<25;$i++) {
			echo "<option value='$i'";
			if ($nb_impression_parent == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<input type=\"hidden\" name=\"fiche\" value=\"$fiche\" />\n";
		echo "<br />Conseil : faites des tests pour éviter de mauvaises surprises lors de l'impression en masse.</p>\n";
		echo "<br /><i>Mise en forme du message :</i>\n";

		$oFCKeditor = new FCKeditor('impression_parentFCK') ;
		$oFCKeditor->BasePath = '../fckeditor/' ;
		$oFCKeditor->Config['DefaultLanguage']  = 'fr' ;
		$oFCKeditor->ToolbarSet = 'Basic' ;
		$oFCKeditor->Value      = $impression_parent ;
		$oFCKeditor->Create() ;

		//echo "</div>\n";
    break;

case 'eleves' :

		$impression_eleve = getSettingValue("ImpressionFicheEleve");
		$nb_impression_eleve = getSettingValue("ImpressionNombreEleve");

		echo "<h3 class='gepi' align='center'>Fiche d'information : ".$gepiSettings['denomination_eleves']."</h3>\n";
		echo "<p>Cette fiche est imprimée lors de la création d'un nouvel utilisateur au statut 'eleve'.</p>\n";
		echo "<p>Nombre de fiches à imprimer par page : \n";
		echo "<select name='nb_impression_eleve' size='1'>\n";
		for ($i=1;$i<25;$i++) {
			echo "<option value='$i'";
			if ($nb_impression_eleve == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<input type=\"hidden\" name=\"fiche\" value=\"$fiche\" />\n";
		echo "<br />Conseil : faites des tests pour éviter de mauvaises surprises lors de l'impression en masse.</p>\n";
		echo "<br /><i>Mise en forme du message :</i>\n";

		$oFCKeditor = new FCKeditor('impression_eleveFCK') ;
		$oFCKeditor->BasePath = '../fckeditor/' ;
		$oFCKeditor->Config['DefaultLanguage']  = 'fr' ;
		$oFCKeditor->ToolbarSet = 'Basic' ;
		$oFCKeditor->Value      = $impression_eleve ;
		$oFCKeditor->Create() ;

		//echo "</div>\n";
	break;
}
echo "<input type='submit' name=\"ok\" value='Enregistrer' />\n";

echo "<br /><br />\n";
echo "<b><a href=\"./modele_fiche_information.php?fiche=$fiche\" target='_blank' >Aperçu de la fiche d'information</a></b>\n";
echo "Attention : la mise en page des fiches est très différente à l'écran et à l'impression.";
echo "Veillez à utiliser la fonction \"aperçu avant impression\" afin de vous rendre compte du résultat.\n";


//echo "</td>\n</tr>\n";
//echo "</table>\n";
echo "</div>\n";

?>
</form>
<br /><br />
<?php require("../lib/footer.inc.php");?>