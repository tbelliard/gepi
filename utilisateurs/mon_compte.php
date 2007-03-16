<?php
/*
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On indique qu'il faut crée des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

// On teste si on affiche le message de changement de mot de passe
if (isset($_GET['change_mdp'])) $affiche_message = 'yes';
$message_enregistrement = "Par sécurité, vous devez changer votre mot de passe.";

// Resume session
if (resumeSession() == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if (($_SESSION['statut'] == 'professeur') or ($_SESSION['statut'] == 'cpe') or ($_SESSION['statut'] == 'responsable') or ($_SESSION['statut'] == 'eleve'))
    // Mot de passe comportant des lettres et des chiffres
    $flag = 0;
else
    // Mot de passe comportant des lettres et des chiffres et au moins un caractère spécial
    $flag = 1;


if ((isset($_POST['valid'])) and ($_POST['valid'] == "yes"))  {
    $msg = '';
    $no_modif = "yes";
    $no_anti_inject_password_a = isset($_POST["no_anti_inject_password_a"]) ? $_POST["no_anti_inject_password_a"] : NULL;
    $no_anti_inject_password1 = isset($_POST["no_anti_inject_password1"]) ? $_POST["no_anti_inject_password1"] : NULL;
    $reg_password2 = isset($_POST["reg_password2"]) ? $_POST["reg_password2"] : NULL;
    $reg_email = isset($_POST["reg_email"]) ? $_POST["reg_email"] : NULL;
    $reg_show_email = isset($_POST["reg_show_email"]) ? $_POST["reg_show_email"] : "no";
    if ($no_anti_inject_password_a != '') {
        $reg_password_a_c = md5($NON_PROTECT['password_a']);
        if ($_SESSION['password'] == $reg_password_a_c) {
            if ($no_anti_inject_password1 != $reg_password2) {
                $msg = "Erreur lors de la saisie du mot de passe, les deux mots de passe ne sont pas identiques. Veuillez recommencer !";
            } else if  ($no_anti_inject_password_a == $no_anti_inject_password1) {
                $msg = "ERREUR : Vous devez choisir un nouveau mot de passe différent de l'ancien.";
            } else if (!(verif_mot_de_passe($NON_PROTECT['password1'],$flag))) {
                $msg = "Erreur lors de la saisie du mot de passe (voir les recommandations), veuillez recommencer !";
            } else {
                $reg_password1 = md5($NON_PROTECT['password1']);
                $reg = mysql_query("UPDATE utilisateurs SET password = '$reg_password1', change_mdp='n' WHERE login = '" . $_SESSION['login'] . "'");
                if ($reg) {
                    $msg = "Le mot de passe a ete modifié !";
                    $no_modif = "no";
                    $_SESSION['password'] = $reg_password1;
                    if (isset($_POST['retour'])) {
                        header("Location:../accueil.php?msg=$msg");
                        die();
                    }
                }
            }
        } else {
            $msg = "L'ancien mot de passe n'est pas correct !";
        }
    }
    $call_email = mysql_query("SELECT email,show_email FROM utilisateurs WHERE login='" . $_SESSION['login'] . "'");
    $user_email = mysql_result($call_email, 0, "email");
    $user_show_email = mysql_result($call_email, 0, "show_email");
    if ($user_email != $reg_email) {
        $reg = mysql_query("UPDATE utilisateurs SET email = '$reg_email' WHERE login = '" . $_SESSION['login'] . "'");
        if ($reg) {
            $msg = $msg."<br />L'adresse e_mail a été modifiéé !";
            $no_modif = "no";
        }
    }
    if ($user_show_email != $reg_show_email) {
    	if ($reg_show_email != "no" and $reg_show_email != "yes") $reg_show_email = "no";
        $reg = mysql_query("UPDATE utilisateurs SET show_email = '$reg_show_email' WHERE login = '" . $_SESSION['login'] . "'");
        if ($reg) {
            $msg = $msg."<br />Le paramétrage d'affichage de votre email a été modifié !";
            $no_modif = "no";
        }
    }
    
    if ($no_modif == "yes") {
        $msg = $msg."<br />Aucune modification n'a été apportée !";
    }
}


// On appelle les informations de l'utilisateur pour les afficher :
$call_user_info = mysql_query("SELECT nom,prenom,statut,email,show_email,civilite FROM utilisateurs WHERE login='" . $_SESSION['login'] . "'");
$user_civilite = mysql_result($call_user_info, "0", "civilite");
$user_nom = mysql_result($call_user_info, "0", "nom");
$user_prenom = mysql_result($call_user_info, "0", "prenom");
$user_statut = mysql_result($call_user_info, "0", "statut");
$user_email = mysql_result($call_user_info, "0", "email");
$user_show_email = mysql_result($call_user_info, "0", "show_email");

//**************** EN-TETE *****************
$titre_page = "Gérer son compte";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// dans le cas de LCS, existence d'utilisateurs locaux reprérés grâce au champ password non vide.
$testpassword = sql_query1("select password from utilisateurs where login = '".$_SESSION['login']."'");
if ($testpassword == -1) $testpassword = '';
// Test SSO
$test_sso = ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon"  and ((getSettingValue("use_sso") != "lcs") or ($testpassword !='')) and getSettingValue("use_sso") != "ldap_scribe") OR $block_sso);

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
if ($test_sso)
    echo "<form enctype=\"multipart/form-data\" action=\"mon_compte.php\" method=\"post\">\n";
echo "<h2>Informations personnelles *</h2>";
echo "<table>";
echo "<tr><td>Identifiant GEPI : </td><td>" . $_SESSION['login']."</td></tr>";
echo "<tr><td>Civilité : </td><td>".$user_civilite."</td></tr>";
echo "<tr><td>Nom : </td><td>".$user_nom."</td></tr>";
echo "<tr><td>Prénom : </td><td>".$user_prenom."</td></tr>";
if ($test_sso) {
    echo "<tr><td>Email : </td><td><input type=text name=reg_email size=30";
    if ($user_email) { echo " value=\"".$user_email."\"";}
    echo " /></td></tr>\n";
} else {
    echo "<tr><td>Email : </td><td>".$user_email."<input type=\"hidden\" name=\"reg_email\" value=\"".$user_email."\" /></td></tr>\n";
}
if ($_SESSION['statut'] == "scolarite" OR $_SESSION['statut'] == "professeur" OR $_SESSION['statut'] == "cpe") {
echo "<tr><td></td><td><input type='checkbox' name='reg_show_email' value='yes'";
if ($user_show_email == "yes") echo " CHECKED";
echo "/> Autoriser l'affichage de mon adresse email pour les utilisateurs non personnels de l'établissement **</td></tr>";
}
echo "<tr><td>Statut : </td><td>".$user_statut."</td></tr>";
echo "</table>";

/*
//Supp ERIC
$tab_class_mat =  make_tables_of_classes_matieres();
if (count($tab_class_mat)!=0) {
    echo "<br /><br />Vous êtes professeur dans les classes et matières suivantes :";
    $i = 0;
    echo "<ul>";
    while ($i < count($tab_class_mat['id_c'])){
        //echo "<li>".$tab_class_mat['nom_m'][$i]." dans la classe : ".$tab_class_mat['nom_c'][$i]."</li>";
		echo "<li>".$tab_class_mat['nom_c'][$i]." : ".$tab_class_mat['nom_m'][$i]."</li>";
        $i++;
    }
    echo "</ul>";
}
*/

// AJOUT Eric
$groups = get_groups_for_prof($_SESSION["login"]);
if (empty($groups)) {
	echo "<br /><br />";
} else {
	echo "<br /><br />Vous êtes professeur dans les classes et matières suivantes :";
	echo "<ul>";
	foreach($groups as $group) {
	   echo "<p><li><span class='norme'><b>" . $group["classlist_string"] . "</b> : ";
	   echo "" . htmlentities($group["description"]) . "</li>";
	   echo "</span></p>\n";
	}
	echo "</ul>";
}
	
$call_prof_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
$nombre_classe = mysql_num_rows($call_prof_classe);
if ($nombre_classe != "0") {
    $j = "0";
    echo "<p>Vous êtes ".getSettingValue("gepi_prof_suivi")." dans la classe de :</p>";
    echo "<ul>";
    while ($j < $nombre_classe) {
        $id_classe = mysql_result($call_prof_classe, $j, "id");
        $classe_suivi = mysql_result($call_prof_classe, $j, "classe");
        echo "<li><b>$classe_suivi</b></li>";
        $j++;
    }
    echo "</ul>";
}





echo "<p class='small'>* Toutes les données nominatives présentes dans la base GEPI et vous concernant vous sont communiquées sur cette page.
Conformément à la loi française n° 78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés,
vous pouvez demander auprès du Chef d'établissement ou auprès de l'<a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">administrateur</a> du site,
la rectification de ces données.
Les rectifications sont effectuées dans les 48 heures hors week-end et jours fériés qui suivent la demande.";
if ($_SESSION['statut'] == "scolarite" OR $_SESSION['statut'] == "professeur" OR $_SESSION['statut'] == "cpe") {
	echo "<p class='small'>** Votre email sera affichée sur certaines pages seulement si leur affichage a été activé de manière globale par l'administrateur et si vous avez autorisé l'affichage de votre email en cochant la case appropriée. ";
	echo "Dans l'hypothèse où vous autorisez l'affichage de votre email, celle-ci ne sera accessible que par les élèves que vous avez en classe et/ou leurs responsables légaux disposant d'un identifiant pour se connecter à Gepi.</p>";
}
// Changement du mot de passe
if ($test_sso) {
    echo "<hr /><a name=\"changemdp\"></a><H2>Changement du mot de passe</H2>";
    echo "<p><b>Attention : le mot de passe doit comporter ".getSettingValue("longmin_pwd") ." caractères minimum. ";
    if ($flag == 1)
        echo "Il doit comporter au moins une lettre, au moins un chiffre et au moins un caractère spécial parmi&nbsp;: ".htmlentities($char_spec);
    else
        echo "Il doit comporter au moins une lettre et au moins un chiffre.";


    echo "<br />Il est fortement conseillé de ne pas choisir un mot de passe trop simple</b>.
    <br /><b>Votre mot de passe est strictement personnel, vous ne devez pas le diffuser, il garantit la sécurité de votre travail.</b></p>";

    echo "<table><tr>\n";
    echo "<td>Ancien mot de passe : </td><td><input type=password name=no_anti_inject_password_a size=20 /></td>\n";
    echo "</tr><tr>\n";
    echo "<td>Nouveau mot de passe (".getSettingValue("longmin_pwd") ." caractères minimum) :</td><td> <input type=password name=no_anti_inject_password1 size=20 /></td>\n";
    echo "</tr><tr>\n";
    echo "<td>Nouveau mot de passe (à confirmer) : </td><td><input type=password name=reg_password2 size=20 /></td>\n";
    echo "</tr></table>\n";
    echo "<input type=\"hidden\" name=\"valid\" value=\"yes\" />\n";
    if ((isset($_GET['retour'])) or (isset($_POST['retour'])))
        echo "<input type=\"hidden\" name=\"retour\" value=\"accueil\" />";
    echo "<br /><center><input type=\"submit\" value=\"Enregistrer\" /></center>\n";
    //echo "</span></form>\n";
    echo "</form>\n";
}
echo "  <hr />\n";
// Journal des connexions
echo "<a name=\"connexion\"></a>";
if (isset($_POST['duree'])) {
   $duree = $_POST['duree'];
} else {
   $duree = '7';
}
switch( $duree ) {
   case 7:
   $display_duree="une semaine";
   break;
   case 15:
   $display_duree="quinze jours";
   break;
   case 30:
   $display_duree="un mois";
   break;
   case 60:
   $display_duree="deux mois";
   break;
   case 183:
   $display_duree="six mois";
   break;
   case 365:
   $display_duree="un an";
   break;
   case 'all':
   $display_duree="le début";
   break;
}

echo "<h2>Journal de vos connexions depuis <b>".$display_duree."</b>**</h2>\n";
$requete = '';
if ($duree != 'all') $requete = "and START > now() - interval " . $duree . " day";

$sql = "select START, SESSION_ID, REMOTE_ADDR, USER_AGENT, AUTOCLOSE, END from log where LOGIN = '".$_SESSION['login']."' ".$requete." order by START desc";

$day_now   = date("d");
$month_now = date("m");
$year_now  = date("Y");
$hour_now  = date("H");
$minute_now = date("i");
$seconde_now = date("s");
$now = mktime($hour_now, $minute_now, $seconde_now, $month_now, $day_now, $year_now);

?>
<ul>
<li>Les lignes en rouge signalent une tentative de connexion avec un mot de passe erroné.</li>
<li>Les lignes en orange signalent une session close pour laquelle vous ne vous êtes pas déconnecté correctement.</li>
<li>Les lignes en noir signalent une session close normalement.</li>
<li>Les lignes en vert indiquent les sessions en cours (cela peut correspondre à une connexion actuellement close mais pour laquelle vous ne vous êtes pas déconnecté correctement).</li>
</ul>
<table class="col" style="width: 90%; margin-left: auto; margin-right: auto; margin-bottom: 32px;" cellpadding="5" cellspacing="0">
    <tr>
        <th class="col">Début session</th>
        <th class="col">Fin session</th>
        <th class="col">Adresse IP et nom de la machine cliente</th>
        <th class="col">Navigateur</th>
    </tr>
<?php
$res = sql_query($sql);
if ($res) {
    for ($i = 0; ($row = sql_row($res, $i)); $i++)
    {
        $annee_b = substr($row[0],0,4);
        $mois_b =  substr($row[0],5,2);
        $jour_b =  substr($row[0],8,2);
        $heures_b = substr($row[0],11,2);
        $minutes_b = substr($row[0],14,2);
        $secondes_b = substr($row[0],17,2);
        $date_debut = $jour_b."/".$mois_b."/".$annee_b." à ".$heures_b." h ".$minutes_b;

        $annee_f = substr($row[5],0,4);
        $mois_f =  substr($row[5],5,2);
        $jour_f =  substr($row[5],8,2);
        $heures_f = substr($row[5],11,2);
        $minutes_f = substr($row[5],14,2);
        $secondes_f = substr($row[5],17,2);
        $date_fin = $jour_f."/".$mois_f."/".$annee_f." à ".$heures_f." h ".$minutes_f;
        $end_time = mktime($heures_f, $minutes_f, $secondes_f, $mois_f, $jour_f, $annee_f);

        $temp1 = '';
        $temp2 = '';
        if ($end_time > $now) {
            $temp1 = "<font color=green>";
            $temp2 = "</font>";
        } else if (($row[4] == 1) or ($row[4] == 2) or ($row[4] == 3)) {
            $temp1 = "<font color=orange>";
            $temp2 = "</font>";
        } else if ($row[4] == 4) {
            $temp1 = "<b><font color=red>";
            $temp2 = "</font></b>";

        }

        echo("<tr>\n");
        echo "<td class=\"col\">".$temp1.$date_debut.$temp2."</td>";
        if ($row[4] == 2)
            echo "<td class=\"col\">".$temp1."Tentative de connexion<br>avec mot de passe erroné.".$temp2."</td>";
        else
            echo "<td class=\"col\">".$temp1.$date_fin.$temp2."</td>";
        if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all"))
            $result_hostbyaddr = " - ".@gethostbyaddr($row[2]);
        else if ($active_hostbyaddr == "no_local")
            if ((substr($row[2],0,3) == 127) or (substr($row[2],0,3) == 10.) or (substr($row[2],0,7) == 192.168))
                $result_hostbyaddr = "";
            else
                $result_hostbyaddr = " - ".@gethostbyaddr($row[2]);
        else
            $result_hostbyaddr = "";

        echo "<td class=\"col\"><span class='small'>".$temp1.$row[2].$result_hostbyaddr.$temp2. "</span></td>";
        echo "<td class=\"col\">".$temp1. detect_browser($row[3]) .$temp2. "</td>";
     }
}


echo "</table>";

echo "<form action=\"mon_compte.php\" name=\"form_affiche_log\" method=\"post\">";
echo "Afficher le journal des connexions depuis : <select name=\"duree\" size=\"1\">";
echo "<option ";
if ($duree == 7) echo "selected";
echo " value=7>Une semaine</option>";
echo "<option ";
if ($duree == 15) echo "selected";
echo " value=15 >Quinze jours</option>";
echo "<option ";
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
echo "<option ";
if ($duree == 'all') echo "selected";
echo " value='all'>Le début</option>";
echo "</select>";
echo "<input type=\"submit\" name=\"Valider\" value=\"Valider\" />";


echo "</form>";
echo "<p class='small'>** Les renseignements ci-dessus peuvent vous permettre de vérifier qu'une connexion pirate n'a pas été effectuée sur votre compte.
Dans le cas contraire, vous devez immédiatement en avertir l'<a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">administrateur</a>.";
require("../lib/footer.inc.php");
?>