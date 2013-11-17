<?php

/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Didier Blanqui
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

$variables_non_protegees = 'yes';
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

if(mb_strtolower(mb_substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

$msg = "";

$cpt = isset($_POST['cpt']) ? $_POST['cpt'] : 0;
$suppr_delegation = isset($_POST['suppr_delegation']) ? $_POST['suppr_delegation'] : NULL;
$fct_delegation=isset($_POST['fct_delegation']) ? $_POST['fct_delegation'] : NULL;
$fct_autorite=isset($_POST['fct_autorite']) ? $_POST['fct_autorite'] : NULL;
$nom_autorite=isset($_POST['nom_autorite']) ? $_POST['nom_autorite'] : NULL;

if (isset($NON_PROTECT["fct_delegation"])){
			$fct_delegation=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["fct_delegation"]));
			// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
			$fct_delegation=preg_replace('/(\\\r\\\n)+/',"\r\n",$fct_delegation);
			$fct_delegation=preg_replace('/(\\\r)+/',"\r",$fct_delegation);
			$fct_delegation=preg_replace('/(\\\n)+/',"\n",$fct_delegation);
		}
		else {
			$fct_delegation="";
		}

if (isset($suppr_delegation)) {
	check_token();
    for ($i = 0; $i < $cpt; $i++) {
        if (isset($suppr_delegation[$i])) {
            $sql = "DELETE FROM s_delegation WHERE id_delegation='$suppr_delegation[$i]';";
			//echo $sql;
            $suppr = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
            if (!$suppr) {
                $msg.="ERREUR lors de la suppression de la délégation n°" . $suppr_delegation[$i] . ".<br />\n";
            } else {
                  $msg.="Suppression de la delegation n°" . $suppr_delegation[$i] . ".<br />\n";
                 $sql = "UPDATE s_exclusions SET id_signataire=0 WHERE id_signataire=" . $suppr_delegation[$i] . ";";
                 $res = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
                if (!$res) {
                    $msg.="ERREUR lors de la mise à jour la delegation aux exclusions prononcées ! <br />\n";
                } else {
                    $msg.="Mise à jour de la delegation aux exclusions prononcées effectuée.<br />\n";
                }
            }
        }
    }
}

if ((isset($fct_autorite)) && ($fct_autorite != '')) {
	check_token();

    $a_enregistrer = 'y';

    $sql = "SELECT fct_autorite FROM s_delegation ORDER BY fct_autorite;";
	//echo $sql;
    $res = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
    if (mysqli_num_rows($res) > 0) {
        $tab_delegation = array();
        while ($lig = mysqli_fetch_object($res)) {
            $tab_delegation[] = $lig->fct_autorite;
        }

        if (in_array($delegation, $tab_delegation)) {
            $a_enregistrer = 'n';
        }
    }
	
    if ($a_enregistrer == 'y') {
        $sql = "INSERT INTO s_delegation SET fct_delegation='" . $fct_delegation . "', fct_autorite='" . $fct_autorite . "', nom_autorite='" . $nom_autorite. "';";
		
        $res = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
        if (!$res) {
            $msg.="ERREUR lors de l'enregistrement de " . $fct_autorite . "<br />\n";
        } else {
            $msg.="Enregistrement de " . $fct_autorite . "<br />\n";
        }
    }
}

$themessage = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
//$titre_page = "Sanctions: Définition des qualités";
$titre_page = "Discipline: Gestion des délégations d'exclusion temporaire";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "</p>\n";

echo "<form enctype='multipart/form-data' action='" . $_SERVER['PHP_SELF'] . "' method='post' name='formulaire'>\n";
echo add_token_field();

//echo "<p class='bold'>Saisie des qualités dans un incident&nbsp;:</p>\n";
echo "<p class='bold'>Saisie des délégations&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt = 0;
$sql = "SELECT * FROM s_delegation ORDER BY id_delegation;";
$res = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if (mysqli_num_rows($res) == 0) {
    //echo "<p>Aucune qualité n'est encore définie.</p>\n";
    echo "<p>Aucune délégation n'est encore définie.</p>\n";
} else {
    //echo "<p>Qualités existantes&nbsp;:</p>\n";
    echo "<p>Délégations existantes&nbsp;:</p>\n";
    echo "<table class='boireaus' border='1' summary='Tableau des délégations existantes'>\n";
    echo "<tr>\n";
    echo "<th>Texte de délégation du chef d'établissement</th>\n";
    echo "<th>Fonction de l'autorité signataire</th>\n";
	echo "<th>Nom de l'autorité signataire</th>\n";
    echo "<th>Supprimer</th>\n";
    echo "</tr>\n";
    $alt = 1;
	
    while ($lig = mysqli_fetch_object($res)) {
        $alt = $alt * (-1);
        echo "<tr class='lig$alt'>\n";

        echo "<td>\n";
        echo "<label for='suppr_delegation_$cpt' style='cursor:pointer;'>";
        echo $lig->fct_delegation;
        echo "</label>";
        echo "</td>\n";

        echo "<td>\n";
        echo "<label for='suppr_delegation_$cpt' style='cursor:pointer;'>";
        echo $lig->fct_autorite;
        echo "</label>";
        echo "</td>\n";
		
		echo "<td>\n";
        echo "<label for='suppr_delegation_$cpt' style='cursor:pointer;'>";
        echo $lig->nom_autorite;
        echo "</label>";
        echo "</td>\n";


        echo "<td><input type='checkbox' name='suppr_delegation[]' id='suppr_delegation_$cpt' value=\"$lig->id_delegation\" onchange='changement();' /></td>\n";
        echo "</tr>\n";

        $cpt++;
    }

    echo "</table>\n";
}
	echo "</blockquote>\n";
	echo "<table class='boireaus' border='1' summary='Saisie des informations de délégation'>\n";
	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Texte de la délégation du chef d'établissement&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<textarea name='no_anti_inject_fct_delegation' cols='50' onchange='changement();'></textarea>\n";
	echo "<i>(facultatif) Ex : Pour le Chef d'établissement,</BR></BR>et par délégation,</i></td>\n";
	echo "</tr>\n";
	
	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Fonction de l'autorité signataire&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='fct_autorite' id='fct_autorite' value='' onchange='changement();' />\n";
	echo "<i>Fonction du personnel de direction ou du délégataire</i></td>\n";
	echo "</tr>\n";
	
	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Nom de l'autorité signataire&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='nom_autorite' id='nom_autorite' value='' onchange='changement();' />\n";
	echo "<i>Nom du personnel de direction ou du délégataire</i></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
echo "</form>\n";

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
