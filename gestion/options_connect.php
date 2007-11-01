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

// Begin standart header

$titre_page = "Options de connexion";



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


// Enregistrement de la durée de conservation des données

if (isset($_POST['duree'])) {
    if (!saveSetting(("duree_conservation_logs"), $_POST['duree'])) {
        $msg = "Erreur lors de l'enregistrement de la durée de conservation des connexions !";
    } else {
        $msg = "La durée de conservation des connexions a été enregistrée.<br />Le changement sera pris en compte après la prochaine connexion à GEPI.";
    }
}


if (isset($_POST['use_sso'])) {
    if (!saveSetting(("use_sso"), $_POST['use_sso'])) {
        $msg = "Erreur lors de l'enregistrement du mode d'authentification !";
    } else {
        $msg = "Le mode d'authentification a été enregistré.";
    }
}


// Load settings

if (!loadSettings()) {
    die("Erreur chargement settings");
}



// Suppression du journal de connexion

if (isset($_POST['valid_sup_logs']) ) {
    $sql = "delete from log where END < now()";
    $res = sql_query($sql);
    if ($res) {
       $msg = "La suppression des entrées dans le journal de connexion a été effectuée.";
    } else {
       $msg = "Il y a eu un problème lors de la suppression des entrées dans le journal de connexion.";
    }
}

// Changement de mot de passe obligatoire
if (isset($_POST['valid_chgt_mdp'])) {
    $sql = "update utilisateurs set change_mdp='y' where login != '".$_SESSION['login']."'";
    $res = sql_query($sql);
    if ($res) {
       $msg = "La demande de changement obligatoire de mot de passe a été enregistrée.";
    } else {
       $msg = "Il y a eu un problème lors de l'enregistrement de la demande de changement obligatoire de mot de passe.";
    }
}


//Activation / désactivation de la procédure de réinitialisation du mot de passe par email
if (isset($_POST['enable_password_recovery'])) {
    if (!saveSetting("enable_password_recovery", $_POST['enable_password_recovery'])) {
        $msg = "Il y a eu un problème lors de l'enregistrement du paramètre d'activation/désactivation de la procédure de récupération automatisée des mots de passe.";
    } else {
        $msg = "L'enregistrement du paramètre d'activation/désactivation de la procédure de récupération automatisée des mots de passe a été effectué avec succès.";
    }
}

// End standart header
require_once("../lib/header.inc");
isset($mode_navig);
$mode_navig = isset($_POST["mode_navig"]) ? $_POST["mode_navig"] : (isset($_GET["mode_navig"]) ? $_GET["mode_navig"] : NULL);
if ($mode_navig == 'accueil') {
    $retour = "../accueil.php";
} else {
    $retour = "index.php";
}

echo "<p class=bold><a href=\"".$retour."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";


//
// Activation/désactivation de la procédure de récupération du mot de passe
//
echo "<h3 class='gepi'>Mots de passe perdus</h3>";
echo "<form action=\"options_connect.php\" method=\"post\">";
echo "<input type='radio' name='enable_password_recovery' value='no' id='label_1b'";
if (getSettingValue("enable_password_recovery")=='no') echo " checked ";
echo " /> <label for='label_1b' style='cursor: pointer;'>Désactiver la procédure automatisée de récupération de mot de passe</label>";

echo "<br /><input type='radio' name='enable_password_recovery' value='yes' id='label_2b'";
if (getSettingValue("enable_password_recovery")=='yes') echo " checked ";
echo " /> <label for='label_2b' style='cursor: pointer;'>Activer la procédure automatisée de récupération de mot de passe</label>";

echo "<center><input type=\"submit\" value=\"Valider\" /></center>";
echo "</form>";

echo"<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>";

//
// Changement du mot de passe obligatoire
//
if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon"  and getSettingValue("use_sso") != "lcs" and getSettingValue("use_sso") != "ldap_scribe")) {
echo "<h3 class='gepi'>Changement du mot de passe obligatoire lors de la prochaine connexion</h3>";
echo "<p><b>ATTENTION : </b>En validant le bouton ci-dessous, <b>tous les utilisateurs</b> seront amenés à changer leur mot de passe lors de leur prochaine connexion.</p>";
echo "<form action=\"options_connect.php\" name=\"form_chgt_mdp\" method=\"post\">";
echo "<center><input type=\"submit\" name=\"valid_chgt_mdp\" value=\"Valider\" onclick=\"return confirmlink(this, 'Êtes-vous sûr de vouloir forcer le changement de mot de passe de tous les utilisateurs ?', 'Confirmation')\" /></center>";
echo "<input type=hidden name=mode_navig value='$mode_navig' />";
echo "</form><hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>";
}
//
// Paramétrage du Single Sign-On
//

echo "<h3 class='gepi'>Mode d'authentification</h3>";
echo "<p><b>ATTENTION :</b> Dans le cas d'une authentification en Single Sign-On avec CAS, LemonLDAP ou LCS, seuls les utilisateurs pour lesquels aucun mot de passe n'est présent dans la base de données pourront se connecter. Toutefois, il est recommandé de conserver un compte administrateur avec un mot de passe afin de pouvoir vous connecter en bloquant le SSO par le biais de la variable 'block_sso' du fichier /lib/global.inc.</p>";
echo "<p>Si vous utilisez CAS, vous devez entrer les coordonnées du serveur CAS dans le fichier /lib/CAS/cas.sso.php.</p>";
echo "<p>Si vous utilisez l'authentification sur serveur LDAP, vous devez renseigner le fichier /lib/config_ldap.inc.php avec les informations nécessaires pour se connecter au serveur.</p>";
echo "<form action=\"options_connect.php\" name=\"form_auth\" method=\"post\">";

echo "<input type='radio' name='use_sso' value='no' id='label_1'";
if (getSettingValue("use_sso")=='no' OR !getSettingValue("use_sso")) echo " checked ";
echo " /> <label for='label_1' style='cursor: pointer;'>Authentification autonome (sur la base de données de Gepi) [défaut]</label>";

echo "<br/><input type='radio' name='use_sso' value='lcs' id='lcs'";
if (getSettingValue("use_sso")=='lcs') echo " checked ";
echo " /> <label for='lcs' style='cursor: pointer;'>Authentification sur serveur LCS</label>";

echo "<br/><input type='radio' name='use_sso' value='ldap_scribe' id='label_ldap_scribe'";
if (getSettingValue("use_sso")=='ldap_scribe') echo " checked ";
echo " /> <label for='label_ldap_scribe' style='cursor: pointer;'>Authentification sur serveur Eole SCRIBE (LDAP)</label>";

echo "<br /><input type='radio' name='use_sso' value='cas' id='label_2'";
if (getSettingValue("use_sso")=='cas') echo " checked ";
echo " /> <label for='label_2' style='cursor: pointer;'>Authentification SSO par un serveur CAS</label>";

echo "<br /><input type='radio' name='use_sso' value='lemon' id='label_3'";
if (getSettingValue("use_sso")=='lemon') echo " checked ";
echo " /> <label for='label_3' style='cursor: pointer;'>Authentification SSO par LemonLDAP</label>";

echo "<p>Remarque : les changements n'affectent pas les sessions en cours.";

echo "<center><input type=\"submit\" name=\"auth_mode_submit\" value=\"Valider\" onclick=\"return confirmlink(this, 'Êtes-vous sûr de vouloir changer le mode d\' authentification ?', 'Confirmation')\" /></center>";

echo "<input type=hidden name=mode_navig value='$mode_navig' />";

echo "</form>

<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\" />";



//
// Durée de conservation des logs
//
echo "<h3 class='gepi'>Durée de conservation des connexions</h3>";
echo "<p>Conformément à la loi loi informatique et liberté 78-17 du 6 janvier 1978, la durée de conservation de ces données doit être déterminée et proportionnée aux finalités de leur traitement.
Cependant par sécurité, il est conseillé de conserver une trace des connexions sur un laps de temps suffisamment long.
</p>";
echo "<form action=\"options_connect.php\" name=\"form_chgt_duree\" method=\"post\">";
echo "Durée de conservation des informations sur les connexions : <select name=\"duree\" size=\"1\">";
echo "<option ";
$duree = getSettingValue("duree_conservation_logs");
if ($duree == 30) echo "selected";
echo " value=30>Un mois</option>";
echo "<option ";
if ($duree == 60) echo "selected";
echo " value=60>Deux mois</option>";
echo "<option ";
if ($duree == 183) echo "selected";
echo " value=183>Six mois</option>";
echo "<option ";
if ($duree == 365) echo "selected";
echo " value=365>Un an</option>";
echo "</select>";
echo "<input type=\"submit\" name=\"Valider\" value=\"Enregistrer\" />";
echo "<input type=hidden name=mode_navig value='$mode_navig' />";
echo "</form>";
//
// Nettoyage du journal
//
?>
<hr class="header" style="margin-top: 32px; margin-bottom: 24px;"/>
<h3 class='gepi'>Suppression de toutes les entrées du journal de connexion</h3>
<?php
$sql = "select START from log order by END";
$res = sql_query($sql);
$logs_number = sql_count($res);
$row = sql_row($res, 0);
$annee = substr($row[0],0,4);
$mois =  substr($row[0],5,2);
$jour =  substr($row[0],8,2);
echo "<p>Nombre d'entrées actuellement présentes dans le journal de connexion : <b>".$logs_number."</b><br />";
echo "Actuellement, le journal contient l'historique des connexions depuis le <b>".$jour."/".$mois."/".$annee."</b></p>";
echo "<p><b>ATTENTION : </b>En validant le bouton ci-dessous, <b>toutes les entrées du journal de connexion (hormis les connexions en cours) seront supprimées</b>.</p>";
echo "<form action=\"options_connect.php\" name=\"form_sup_logs\" method=\"post\">";
echo "<center><input type=\"submit\" name=\"valid_sup_logs\" value=\"Valider\" onclick=\"return confirmlink(this, 'Êtes-vous sûr de vouloir supprimer tout l\'historique du journal de connexion ?', 'Confirmation')\" /></center>";
echo "<input type=hidden name=mode_navig value='$mode_navig' />";
echo "</form><br/>";

require("../lib/footer.inc.php");
?>