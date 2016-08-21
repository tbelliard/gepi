<?php

/**
 *
 *
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$titre_page = "Emploi du temps - Transfert";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
// ajout de la ligne suivante dans 'sql/data_gepi.sql' et 'utilitaires/updates/access_rights.inc.php'
// INSERT INTO droits VALUES ('/edt_organisation/transferer_edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'transférer un edt', '');

$sql="SELECT 1=1 FROM droits WHERE id='/edt_organisation/transferer_edt.php';";
$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/edt_organisation/transferer_edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F','transférer un edt', '');";
	$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
if ($_SESSION["statut"] != "administrateur") {
	Die('Vous devez demander à votre administrateur l\'autorisation de voir cette page.');
}

// ===== Initialisation des variables =====
$supprimer = isset($_GET["supprimer"]) ? $_GET["supprimer"] : (isset($_POST["supprimer"]) ? $_POST["supprimer"] : NULL);
$login = isset($_GET["login"]) ? $_GET["login"] : (isset($_POST["login"]) ? $_POST["login"] : NULL);
$couper = isset($_GET["couper"]) ? $_GET["couper"] : (isset($_POST["couper"]) ? $_POST["couper"] : NULL);
$coller = isset($_GET["coller"]) ? $_GET["coller"] : (isset($_POST["coller"]) ? $_POST["coller"] : NULL);
$message = "";

// ============================================ Suppression d'un emploi du temps

if (isset($supprimer) AND isset($login)) {
    if ($supprimer == "ok") {
        $message = '<div class="cadreInformation">
                            Veuillez confirmer la suppression :
                            <a style="background-color:white;border:2px solid black;padding:4px;" href="./transferer_edt.php?supprimer=confirme_suppression&amp;login='.addslashes($login).'">Confirmer</a>
                            <a style="background-color:white;border:2px solid black;padding:2px;" href="./transferer_edt.php?supprimer=annuler_suppression&amp;login='.addslashes($login).'">Annuler</a>
                    </div>
                    ';    

    }
    else if ($supprimer== "confirme_suppression") {
        // ====================== Vérifier que $login est bien un professeur
        $req_statut = mysqli_query($GLOBALS["mysqli"], "SELECT statut FROM utilisateurs WHERE login = '".addslashes($login)."' ");
        $rep_statut = mysqli_fetch_array($req_statut);
        if ($rep_statut["statut"] == "professeur") {
            $req_suppression = mysqli_query($GLOBALS["mysqli"], "DELETE FROM edt_cours WHERE login_prof = '".addslashes($login)."' ");
            $deletedRows = mysqli_affected_rows($GLOBALS["mysqli"]);
            if ($deletedRows != 0) {
                $message = "<div class=\"cadreInformation\">L'emploi du temps a été supprimé avec succès.</div>";
            }
            else {
                $message =  "<div class=\"cadreInformation\">Il n'y a rien à supprimer !</div>";
            }
        } else {
            $message = "<div class=\"cadreInformation\">Le compte concerné n'est pas celui d'un professeur !</div>";
        }
    }

}

// ============================================ Copier un emploi du temps dans le "presse-papier"
if (isset($couper) AND isset($login)) {
    if ($couper=="ok") {
        $_SESSION["couper_edt"] = $login;
        $message="<div class=\"cadreInformation\">L'emploi du temps est prêt à être transféré</div>";
    }
}

// ============================================ Transférer un emploi du temps
if (isset($coller) AND isset($login) AND isset($_SESSION["couper_edt"])) {
    if ($login != $_SESSION["couper_edt"]) {
        // ====================== Vérifier que $login est bien un professeur
        $req_statut = mysqli_query($GLOBALS["mysqli"], "SELECT statut FROM utilisateurs WHERE login = '".addslashes($login)."' ");
        $rep_statut = mysqli_fetch_array($req_statut);
        if ($rep_statut["statut"] == "professeur") {

            $req_compare_groupes = mysqli_query($GLOBALS["mysqli"], "SELECT id_groupe FROM j_groupes_professeurs WHERE 
                                    login = '".$_SESSION["couper_edt"]."' AND
                                    id_groupe NOT IN (SELECT id_groupe FROM j_groupes_professeurs WHERE
                                                    login = '".$login."' ) 
                                    ");
            if (mysqli_num_rows($req_compare_groupes) == 0) {
                $req_edt_prof = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM edt_cours WHERE 
                                                            login_prof = '".$login."'
                                                            ") or die(mysqli_error($GLOBALS["mysqli"]));  
                if (mysqli_num_rows($req_edt_prof) == 0) {
                        $remplacement = mysqli_query($GLOBALS["mysqli"], "UPDATE edt_cours SET login_prof = '".$login."' WHERE login_prof = '".$_SESSION["couper_edt"]."' ");
                        $message = "<div class=\"cadreInformation\">transfert réalisé. Les cours ont été déplacés avec succès</div>";
                }
                else {
                    $message = "<div class=\"cadreInformation\">L'emploi du temps du prof destinataire n'est pas vide.</div>";
                }
            }
            else {
                $message = "<div class=\"cadreInformation\">Les groupes d'enseignements des deux professeurs sont incompatibles.</div>";
            }
        }
        else {
            $message = "<div class=\"cadreInformation\">Le destinataire de l'emploi du temps doit être un professeur.</div>";
        }
    }
    else {
        $message = "<div class=\"cadreInformation\">Vous ne pouvez pas transférer un emploi du temps sur lui-même !</div>";
    }
}

// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";

//++++++++++ l'entête de Gepi +++++
require_once("../lib/header.inc.php");
//++++++++++ fin entête +++++++++++
//++++++++++ le menu EdT ++++++++++
require_once("./menu.inc.php");
//++++++++++ fin du menu ++++++++++

?>


<br/>
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

	<?php 
        if ($message != "") {
            echo $message;
        }
        require_once("./menu.inc.new.php"); ?>


<h2><strong>Transférer/Supprimer des emplois du temps</strong></h2>
<?php

$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        

		echo "<div class=\"titre_nom_t_edt\"><strong>nom</strong></div>";
		echo "<div class=\"titre_prenom_t_edt\">prénom</div>";
		echo "<div class=\"titre_creneau_t_edt\">créneaux</div>";
        echo "<div style=\"clear:both;\"></div>";

	$req_profs = mysqli_query($GLOBALS["mysqli"], "SELECT login, nom , prenom FROM utilisateurs WHERE
					statut = 'professeur' ORDER BY nom ASC");
	while ($rep_profs = mysqli_fetch_array($req_profs)) {
		$req_cours = mysqli_query($GLOBALS["mysqli"], "SELECT id_cours FROM edt_cours WHERE
					login_prof = '".$rep_profs['login']."'");
		echo "<div class=\"texte_nom_t_edt\"><strong>".$rep_profs['nom']."</strong></div>";
		echo "<div class=\"texte_prenom_t_edt\">".$rep_profs['prenom']."</div>";
		echo "<div class=\"texte_creneau_t_edt\">".mysqli_num_rows($req_cours)."</div>";
		echo "<div class=\"bouton_supprimer_t_edt\"><a href=\"./transferer_edt.php?supprimer=ok&amp;login=".$rep_profs['login']." \" ><img src=\"../templates/".NameTemplateEDT()."/images/erase.png\" title=\"Supprimer l'emploi du temps\" alt=\"Supprimer\" /></a></div>";
		echo "<div class=\"bouton_copier_t_edt\"><a href=\"./transferer_edt.php?couper=ok&amp;login=".$rep_profs['login']."\" title=\"Déplacer cet emploi du temps\"><img src=\"../templates/".NameTemplateEDT()."/images/copy.png\" title=\"Déplacer cet emploi du temps\" alt=\"Copier\" /></a></div>";
		echo "<div class=\"bouton_coller_t_edt\"><a href=\"./transferer_edt.php?coller=ok&amp;login=".$rep_profs['login']."\" ><img src=\"../templates/".NameTemplateEDT()."/images/paste.png\" title=\"Enseignant destinataire\" alt=\"Coller\" /></a></div>";
        echo "<div style=\"clear:both;\"></div>";
	}

if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}

?>
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>