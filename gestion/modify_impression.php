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



if (isset($_POST['impression'])) {
	$error = false;
    $imp = html_entity_decode_all_version($_POST['impression']);
    if (!saveSetting("Impression", $imp)) {
        $msg = "Erreur lors de l'enregistrement de la fiche bienvenue pour les personnels !<br/>";
        $error = true;
    }
    $imp = html_entity_decode_all_version($_POST['impression_parent']);
    if (!saveSetting("ImpressionFicheParent", $imp)) {
        $msg = "Erreur lors de l'enregistrement de la fiche bienvenue pour les parents !<br/>";
        $error = true;
    }
    $imp = html_entity_decode_all_version($_POST['impression_eleve']);
    if (!saveSetting("ImpressionFicheEleve", $imp)) {
        $msg = "Erreur lors de l'enregistrement de la fiche bienvenue pour les élèves !<br/>";
        $error = true;
    }
    $nb = is_numeric($_POST['nb_impression']) ? $_POST['nb_impression'] : "1";
    if (!saveSetting("ImpressionNombre", $nb)) {
    	$error = true;
    }
    $nb = is_numeric($_POST['nb_impression_parent']) ? $_POST['nb_impression_parent'] : "1";
    if (!saveSetting("ImpressionNombreParent", $nb)) {
    	$error = true;
    }
    $nb = is_numeric($_POST['nb_impression_eleve']) ? $_POST['nb_impression_eleve'] : "1";
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
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>| <input type=submit value=Enregistrer></p>

<?php

if (!loadSettings()) {
    die("Erreur chargement settings");
}

echo "<br/>";
echo "<p>Lors de la création d'un utilisateur, il vous est possible d'imprimer une feuille d'information contenant les paramètres de connexion à GEPI, ainsi que l'un des trois textes ci-dessous, selon le statut de l'utilisateur créé. Attention, ce texte est au format html !</p>";

$impression = getSettingValue("Impression");
$nb_impression = getSettingValue("ImpressionNombre");
echo "<h3 class='gepi'>Fiche d'information : personnels de l'établissement (professeurs, scolarité, CPE)</h3>";
echo "<p>Nombre de fiches à imprimer par page : ";
echo "<select name='nb_impression' size='1'>";
for ($i=1;$i<25;$i++) {
	echo "<option value='$i'";
	if ($nb_impression == $i) echo " SELECTED";
	echo ">$i</option>";
}
echo "</select>";
echo "<br/>Conseil : faites des tests pour éviter de mauvaises surprises lors de l'impression en masse.</p>";
echo "<div class='small'><textarea name='impression' rows=25 cols=100 wrap='virtual'>";
echo "$impression";
echo "</textarea></div>";

$impression_parent = getSettingValue("ImpressionFicheParent");
$nb_impression_parent = getSettingValue("ImpressionNombreParent");
echo "<h3 class='gepi'>Fiche d'information : parents</h3>";
echo "<p>Cette fiche est imprimée lors de la création d'un nouvel utilisateur au statut 'responsable'.</p>";
echo "<p>Nombre de fiches à imprimer par page : ";
echo "<select name='nb_impression_parent' size='1'>";
for ($i=1;$i<25;$i++) {
	echo "<option value='$i'";
	if ($nb_impression_parent == $i) echo " SELECTED";
	echo ">$i</option>";
}
echo "</select>";
echo "<br/>Conseil : faites des tests pour éviter de mauvaises surprises lors de l'impression en masse.</p>";
echo "<div class='small'><textarea name='impression_parent' rows=25 cols=100 wrap='virtual'>";
echo $impression_parent;
echo "</textarea></div>";

$impression_eleve = getSettingValue("ImpressionFicheEleve");
$nb_impression_eleve = getSettingValue("ImpressionNombreEleve");
echo "<h3 class='gepi'>Fiche d'information : élèves</h3>";
echo "<p>Cette fiche est imprimée lors de la création d'un nouvel utilisateur au statut 'eleve'.</p>";
echo "<p>Nombre de fiches à imprimer par page : ";
echo "<select name='nb_impression_eleve' size='1'>";
for ($i=1;$i<25;$i++) {
	echo "<option value='$i'";
	if ($nb_impression_eleve == $i) echo " SELECTED";
	echo ">$i</option>";
}
echo "</select>";
echo "<br/>Conseil : faites des tests pour éviter de mauvaises surprises lors de l'impression en masse.</p>";
echo "<div class='small'><textarea name='impression_eleve' rows=25 cols=100 wrap='virtual'>";
echo $impression_eleve;
echo "</textarea></div>";

?>
</form>
<br/><br/>
<?php require("../lib/footer.inc.php");?>