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

$titre_page = "Gestion des connexions";



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

/*
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
*/

// Load settings

if (!loadSettings()) {
    die("Erreur chargement settings");
}


/*// Suppression du journal de connexion

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
*/

//
// Protection contre les attaques.
//
if (isset($_POST['valid_param_mdp'])) {
    settype($_POST['nombre_tentatives_connexion'],"integer");
    settype($_POST['temps_compte_verrouille'],"integer");
    if ($_POST['nombre_tentatives_connexion'] < 1) $_POST['nombre_tentatives_connexion'] = 1;
    if ($_POST['temps_compte_verrouille'] < 0) $_POST['temps_compte_verrouille'] = 0;
    if (!saveSetting("nombre_tentatives_connexion", $_POST['nombre_tentatives_connexion'])) {
        $msg1 = "Il y a eu un problème lors de l'enregistrement du paramètre nombre_tentatives_connexion.";
    } else {
        $msg1 = "";
    }
    if (!saveSetting("temps_compte_verrouille", $_POST['temps_compte_verrouille'])) {
        $msg2 = "Il y a eu un problème lors de l'enregistrement du paramètre temps_compte_verrouille.";
    } else {
        $msg2 = "";
    }
    if (($msg1 == "") and ($msg2 == ""))
        $msg = "Les paramètres ont été correctement enregistrées";
    else
        $msg = $msg1." ".$msg2;
}



//Activation / désactivation du login
if (isset($_POST['disable_login'])) {
    if (!saveSetting("disable_login", $_POST['disable_login'])) {
        $msg = "Il y a eu un problème lors de l'enregistrement du paramètre d'activation/désactivation des connexions.";
    } else {
        $msg = "l'enregistrement du paramètre d'activation/désactivation des connexions a été effectué avec succès.";
    }
}

//Activation / désactivation de la procédure de réinitialisation du mot de passe par email
if (isset($_POST['enable_password_recovery'])) {
    if (!saveSetting("enable_password_recovery", $_POST['enable_password_recovery'])) {
        $msg = "Il y a eu un problème lors de l'enregistrement du paramètre d'activation/désactivation des connexions.";
    } else {
        $msg = "l'enregistrement du paramètre d'activation/désactivation des connexions a été effectué avec succès.";
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
// Affichage des personnes connectées
//
echo "<h3 class='gepi'>Utilisateurs connectés en ce moment</h3>";
echo "<div title=\"Utilisateurs connectés\">";
echo "<ul>";
// compte le nombre d'enregistrement dans la table
$sql = "select u.login, concat(u.prenom, ' ', u.nom) utilisa, u.email from log l, utilisateurs u where (l.LOGIN = u.login and l.END > now())";

$res = sql_query($sql);
if ($res) {
    for ($i = 0; ($row = sql_row($res, $i)); $i++)
    {
    echo("<li>" . $row[1]. " | <a href=\"mailto:" . $row[2] . "\">Envoyer un mail</a> |");
    if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon"  and getSettingValue("use_sso") != "lcs" and getSettingValue("use_sso") != "ldap_scribe"))
        echo "<a href=\"../utilisateurs/change_pwd.php?user_login=" . $row[0] . "\">Déconnecter en changeant le mot de passe</a>";
    echo "</li>";
    }
}

?>
</ul>
</div>

<hr class="header" style="margin-top: 32px; margin-bottom: 24px;"/>
<?php
//
// Activation/désactivation des connexions
//
echo "<h3 class='gepi'>Activation/désactivation des connexions</h3>\n";
echo "<p><b>ATTENTION : </b>En désactivant les connexions, vous rendez impossible la connexion au site pour les utilisateurs, hormis les administrateurs. De plus, les utilisateurs actuellement connectés sont automatiquement déconnectés.</p>\n";
echo "<form action=\"gestion_connect.php\" name=\"form_acti_connect\" method=\"post\">\n";

echo "<table border='0'>\n";
echo "<tr>\n";
echo "<td valign='top'>\n";
echo "<input type='radio' name='disable_login' value='yes' id='label_1a'";
if (getSettingValue("disable_login")=='no') echo " checked ";
echo " />\n";
echo "</td>\n";
echo "<td>\n";
echo "<label for='label_1a'>Désactiver les connexions</label>";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td valign='top'>\n";
echo "<input type='radio' name='disable_login' value='soft' id='label_3a'";
if (getSettingValue("disable_login")=='soft') echo " checked ";
echo " />\n";
echo "</td>\n";
echo "<td>\n";
echo "<label for='label_3a'>Désactiver les futures connexions</label>\n";
echo "<br />(<i>et attendre la fin des connexions actuelles pour pouvoir désactiver les connexions et procéder à une opération de maintenance, par exemple</i>)\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td valign='top'>\n";
echo "<input type='radio' name='disable_login' value='no' id='label_2a'";
if (getSettingValue("disable_login")=='yes') echo " checked ";
echo " />\n";
echo "</td>\n";
echo "<td>\n";
echo "<label for='label_2a'>Activer les connexions</label>";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "<center><input type=\"submit\" name=\"valid_acti_mdp\" value=\"Valider\" /></center>";
echo "</form>";

echo"<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>";

/*
//
// Activation/désactivation de la procédure de récupération du mot de passe
//
echo "<h3 class='gepi'>Mots de passe perdus</h3>";
echo "<form action=\"gestion_connect.php\" method=\"post\">";
echo "<input type='radio' name='enable_password_recovery' value='no' id='label_1b'";
if (getSettingValue("enable_password_recovery")=='no') echo " checked ";
echo " /> <label for='label_1b'>Désactiver la procédure automatisée de récupération de mot de passe</label>";

echo "<br /><input type='radio' name='enable_password_recovery' value='yes' id='label_2b'";
if (getSettingValue("enable_password_recovery")=='yes') echo " checked ";
echo " /> <label for='label_2b'>Activer la procédure automatisée de récupération de mot de passe</label>";

echo "<center><input type=\"submit\" value=\"Valider\" /></center>";
echo "</form>";

echo"<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>";
*/
//
// Protection contre les attaques.
//
echo "<h3 class='gepi'>Protection contre les attaques forces brutes.</h3>";
echo "<p>Configuration de GEPI de manière à bloquer temporairement le compte d'un utilisateur après un certain nombre de tentatives de connexion infructueuses.
<br />En contrepartie, un pirate peut se servir de ce mécanisme d'auto-défense pour bloquer en permanence des comptes utilisateur ou administrateur.
<br />Si vous ête un jour confronté à cette situation d'urgence, vous pourrez dans le fichier \"config.inc.php\", forcer le débloquage des comptes administrateur
et/ou mettre en liste noire, la ou les adresses IP incriminées.<br /></p>";

echo "<form action=\"gestion_connect.php\" name=\"form_param_mdp\" method=\"post\">";
echo "<table><tr>";
echo "<td>Nombre maximum de tentatives de connexion infructueuses: </td>";
echo "<td><input type=\"text\" name=\"nombre_tentatives_connexion\" value=\"".getSettingValue("nombre_tentatives_connexion")."\" size=\"20\" /></td>";
echo "</tr><tr>";
echo "<td>Temps en minutes pendant lequel un compte est temporairement verrouillé suite à un trop grand nombre d'essais infructueux : </td>";
echo "<td><input type=\"text\" name=\"temps_compte_verrouille\" value=\"".getSettingValue("temps_compte_verrouille")."\" size=\"20\" /></td>";
echo "</tr></table>";

echo "<center><input type=\"submit\" name=\"valid_param_mdp\" value=\"Valider\" /></center>";
echo "</form><hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\" />";

/*
//
// Changement du mot de passe obligatoire
//
if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon"  and getSettingValue("use_sso") != "lcs" and getSettingValue("use_sso") != "ldap_scribe")) {
echo "<h3 class='gepi'>Changement du mot de passe obligatoire lors de la prochaine connexion</h3>";
echo "<p><b>ATTENTION : </b>En validant le bouton ci-dessous, <b>tous les utilisateurs</b> seront amenés à changer leur mot de passe lors de leur prochaine connexion.</p>";
echo "<form action=\"gestion_connect.php\" name=\"form_chgt_mdp\" method=\"post\">";
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
echo "<form action=\"gestion_connect.php\" name=\"form_auth\" method=\"post\">";

echo "<input type='radio' name='use_sso' value='no' id='label_1'";
if (getSettingValue("use_sso")=='no' OR !getSettingValue("use_sso")) echo " checked ";
echo " /> <label for='label_1'>Authentification autonome (sur la base de données de Gepi) [défaut]</label>";

echo "<br/><input type='radio' name='use_sso' value='lcs' id='lcs'";
if (getSettingValue("use_sso")=='lcs') echo " checked ";
echo " /> <label for='lcs'>Authentification sur serveur LCS</label>";

echo "<br/><input type='radio' name='use_sso' value='ldap_scribe' id='label_ldap_scribe'";
if (getSettingValue("use_sso")=='ldap_scribe') echo " checked ";
echo " /> <label for='label_ldap_scribe'>Authentification sur serveur Eole SCRIBE (LDAP)</label>";

echo "<br /><input type='radio' name='use_sso' value='cas' id='label_2'";
if (getSettingValue("use_sso")=='cas') echo " checked ";
echo " /> <label for='label_2'>Authentification SSO par un serveur CAS</label>";

echo "<br /><input type='radio' name='use_sso' value='lemon' id='label_3'";
if (getSettingValue("use_sso")=='lemon') echo " checked ";
echo " /> <label for='label_3'>Authentification SSO par LemonLDAP</label>";

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
echo "<form action=\"gestion_connect.php\" name=\"form_chgt_duree\" method=\"post\">";
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
echo "<form action=\"gestion_connect.php\" name=\"form_sup_logs\" method=\"post\">";
echo "<center><input type=\"submit\" name=\"valid_sup_logs\" value=\"Valider\" onclick=\"return confirmlink(this, 'Êtes-vous sûr de vouloir supprimer tout l\'historique du journal de connexion ?', 'Confirmation')\" /></center>";
echo "<input type=hidden name=mode_navig value='$mode_navig' />";
echo "</form>";
*/
//
// Journal des connections
//
?>
<!--<hr class="header" style="margin-top: 32px; margin-bottom: 24px;"/>-->
<?php
if (isset($_POST['duree2'])) {
   $duree2 = $_POST['duree2'];
} else {
   $duree2 = '20dernieres';
}
switch( $duree2 ) {
   case '20dernieres' :
   $display_duree="les 20 dernières";
   break;
   case 2:
   $display_duree="depuis deux jours";
   break;
   case 7:
   $display_duree="depuis une semaine";
   break;
   case 15:
   $display_duree="depuis quinze jours";
   break;
   case 30:
   $display_duree="depuis un mois";
   break;
   case 60:
   $display_duree="depuis deux mois";
   break;
   case 183:
   $display_duree="depuis six mois";
   break;
   case 365:
   $display_duree="depuis un an";
   break;
   case 'all':
   $display_duree="depuis le début";
   break;
}

echo "<h3 class='gepi'>Journal des connexions <b>".$display_duree."</b></H3>";

?>
<div title="Journal des connections" style="width: 100%;">
<ul>
<li>Les lignes en rouge signalent une tentative de connexion avec un mot de passe erroné.</li>
<li>Les lignes en orange signalent une session close pour laquelle l'utilisateur ne s'est pas déconnecté correctement.</li>
<li>Les lignes en noir signalent une session close normalement.</li>
<li>Les lignes en vert indiquent les sessions en cours (cela peut correspondre à une connexion actuellement close mais pour laquelle l'utilisateur ne s'est pas déconnecté correctement).</li>
</ul>

<?php

echo "<form action=\"gestion_connect.php\" name=\"form_affiche_log\" method=\"post\">";
echo "Afficher le journal des connexions : <select name=\"duree2\" size=\"1\">";
echo "<option ";
if ($duree2 == '20dernieres') echo "selected";
echo " value='20dernieres'>les 20 dernières</option>";
echo "<option ";
if ($duree2 == 2) echo "selected";
echo " value=2>depuis Deux jours</option>";
echo "<option ";
if ($duree2 == 7) echo "selected";
echo " value=7>depuis Une semaine</option>";
echo "<option ";
if ($duree2 == 15) echo "selected";
echo " value=15 >depuis Quinze jours</option>";
echo "<option ";
if ($duree2 == 30) echo "selected";
echo " value=30>depuis Un mois</option>";
echo "<option ";
if ($duree2 == 60) echo "selected";
echo " value=60>depuis Deux mois</option>";
echo "<option ";
if ($duree2 == 183) echo "selected";
echo " value=183>depuis Six mois</option>";
echo "<option ";
if ($duree2 == 365) echo "selected";
echo " value=365>depuis Un an</option>";
echo "<option ";
if ($duree2 == 'all') echo "selected";
echo " value='all'>depuis Le début</option>";
echo "</select>";
echo "<input type=\"submit\" name=\"Valider\" value=\"Valider\" /><br /><br />";
echo "<input type=hidden name=mode_navig value='$mode_navig' />";
echo "</form>";

?>


<table class="col" style="width: 90%; margin-left: auto; margin-right: auto; margin-bottom: 32px;" cellpadding="5" cellspacing="0">
    <tr>
        <th class="col">Identifiant</th>
        <th class="col">Début session</th>
        <th class="col">Fin session</th>
        <th class="col">Adresse IP et nom de la machine cliente</th>
        <th class="col">Navigateur</th>
        <th class="col">Provenance</th>
    </tr>
<?php
$requete = '';
$requete1 = '';
if ($duree2 != 'all') $requete = "where l.START > now() - interval " . $duree2 . " day";
if ($duree2 == '20dernieres') {$requete1 = "LIMIT 0,20"; $requete='';}
$sql = "select l.LOGIN, concat(prenom, ' ', nom) utili, l.START, l.SESSION_ID, l.REMOTE_ADDR, l.USER_AGENT, l.REFERER,
 l.AUTOCLOSE, l.END, u.email
from log l LEFT JOIN utilisateurs u ON l.LOGIN = u.login ".$requete." order by START desc ".$requete1;

// $row[0] : log.LOGIN
// $row[1] : USER
// $row[2] : START
// $row[3] : SESSION_ID
// $row[4] : REMOTE_ADDR
// $row[5] : USER_AGENT
// $row[6] : REFERER
// $row[7] : AUTOCLOSE
// $row[8] : END
// $row[9] : EMAIL

$day_now   = date("d");
$month_now = date("m");
$year_now  = date("Y");
$hour_now  = date("H");
$minute_now = date("i");
$now = mktime($hour_now, $minute_now, 0, $month_now, $day_now, $year_now);
$res = sql_query($sql);
if ($res) {
    for ($i = 0; ($row = sql_row($res, $i)); $i++)
    {
        $annee_f = substr($row[8],0,4);
        $mois_f =  substr($row[8],5,2);
        $jour_f =  substr($row[8],8,2);
        $heures_f = substr($row[8],11,2);
        $minutes_f = substr($row[8],14,2);
        $secondes_f = substr($row[8],17,2);
        $date_fin_f = $jour_f."/".$mois_f."/".$annee_f." à ".$heures_f." h ".$minutes_f;
        $end_time = mktime($heures_f, $minutes_f, $secondes_f, $mois_f, $jour_f, $annee_f);
        $annee_b = substr($row[2],0,4);
        $mois_b =  substr($row[2],5,2);
        $jour_b =  substr($row[2],8,2);
        $heures_b = substr($row[2],11,2);
        $minutes_b = substr($row[2],14,2);
        $secondes_b = substr($row[2],17,2);
        $date_debut = $jour_b."/".$mois_b."/".$annee_b." à ".$heures_b." h ".$minutes_b;
        $temp1 = '';
        $temp2 = '';
        if ($end_time > $now) {
            $temp1 = "<font color=green>";
            $temp2 = "</font>";
        }
        if ($row[1] == '') $row[1] = "<font color=red><b>Utilisateur inconnu</b></font>";
        echo("<tr>\n");
        echo "<td class=\"col\"><span class='small'>".$temp1.$row[0]."<br /><a href=\"mailto:" .$row[9]. "\">".$row[1] . "</a>".$temp2."</span></td>\n";
        echo "<td class=\"col\"><span class='small'>".$temp1.$date_debut.$temp2."</span></td>";
        if ($row[7] == 4) {
           echo "<td class=\"col\" style=\"color: red;\"><span class='small'><b>Tentative de connexion<br />avec mot de passe erroné.</b></span></td>";
        } else if ($end_time > $now) {
            echo "<td class=\"col\" style=\"color: green;\"><span class='small'>" .$date_fin_f. "</span></td>";
        } else if (($row[7] == 1) or ($row[7] == 2) or ($row[7] == 3)) {
            echo "<td class=\"col\" style=\"color: orange;\"><span class='small'>" .$date_fin_f. "</span></td>";
        } else {
            echo "<td class=\"col\"><span class='small'>" .$date_fin_f. "</span></td>";
        }
        if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all"))
            $result_hostbyaddr = " - ".@gethostbyaddr($row[4]);
        else if ($active_hostbyaddr == "no_local")
            if ((substr($row[4],0,3) == 127) or (substr($row[4],0,3) == 10.) or (substr($row[4],0,7) == 192.168))
                $result_hostbyaddr = "";
            else
                $result_hostbyaddr = " - ".@gethostbyaddr($row[4]);
        else
            $result_hostbyaddr = "";

        echo "<td class=\"col\"><span class='small'>".$temp1.$row[4].$result_hostbyaddr.$temp2. "</span></td>";
        echo "<td class=\"col\"><span class='small'>".$temp1. detect_browser($row[5]) .$temp2. "</span></td>";
        echo "<td class=\"col\"><span class='small'>".$temp1. $row[6] .$temp2. "</span></td>";
    }
}

?>
</table>
</div>
<?php require("../lib/footer.inc.php");?>