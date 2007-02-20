<?php
/*
 * Last modification  : 17/10/2006
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Initialisation des variables
$user_login = isset($_POST["user_login"]) ? $_POST["user_login"] : (isset($_GET["user_login"]) ? $_GET["user_login"] : NULL);


if (isset($_POST['valid']) and ($_POST['valid'] == "yes")) {
    // Cas LCS : on teste s'il s'agit d'un utilisateur local ou non
    if (getSettingValue("use_sso") == "lcs")
        if ($_POST['is_lcs'] == "y") $is_pwd = 'n'; else $is_pwd = 'y';
    else
        $is_pwd = "y";

    if ($_POST['reg_nom'] == '')  {
        $msg = "Veuillez entrer un nom pour l'utilisateur !";
    } else {
        $k = 0;
        while ($k < $_POST['max_mat']) {
            $temp = "matiere_".$k;
            $reg_matiere[$k] = $_POST[$temp];
            $k++;
        }
//
// actions si un nouvel utilisateur a été défini
//
        if ((isset($_POST['new_login'])) and ($_POST['new_login']!='') and (ereg ("^[a-zA-Z_]{1}[a-zA-Z0-9_]{0,".($longmax_login-1)."}$", $_POST['new_login'])) ) {
            $_POST['new_login'] = strtoupper($_POST['new_login']);
            $reg_password_c = md5($NON_PROTECT['password1']);
            $resultat = "";
            if (($_POST['no_anti_inject_password1'] != $_POST['reg_password2']) and ($is_pwd == "y")) {
                $msg = "Erreur lors de la saisie : les deux mots de passe ne sont pas identiques, veuillez recommencer !";
            } else if ((!(verif_mot_de_passe($_POST['no_anti_inject_password1'],0)))  and ($is_pwd == "y")) {
                $msg = "Erreur lors de la saisie du mot de passe (voir les recommandations), veuillez recommencer !";

            } else {
                $test = mysql_query("SELECT * FROM utilisateurs WHERE login = '".$_POST['new_login']."'");
                $nombreligne = mysql_num_rows($test);
                if ($nombreligne != 0) {
                    $resultat = "NON";
                    $msg = "*** Attention ! Un utilisateur ayant le même identifiant existe déjà. Enregistrement impossible ! ***";
                }
                if ($resultat != "NON") {
                    if ($is_pwd == "y")
                        $reg_data = mysql_query("INSERT INTO utilisateurs SET nom='".$_POST['reg_nom']."',prenom='".$_POST['reg_prenom']."',civilite='".$_POST['reg_civilite']."',login='".$_POST['new_login']."',password='$reg_password_c',statut='".$_POST['reg_statut']."',email='".$_POST['reg_email']."',etat='actif', change_mdp='y'");
                    else
                        $reg_data = mysql_query("INSERT INTO utilisateurs SET nom='".$_POST['reg_nom']."',prenom='".$_POST['reg_prenom']."',civilite='".$_POST['reg_civilite']."',login='".$_POST['new_login']."',password='',statut='".$_POST['reg_statut']."',email='".$_POST['reg_email']."',etat='actif', change_mdp='n'");
                    if ($_POST['reg_statut'] == "professeur") {
                        $del = mysql_query("DELETE FROM j_professeurs_matieres WHERE id_professeur = '".$_POST['new_login']."'");
                        $m = 0;
                        while ($m < $_POST['max_mat']) {
                            if ($reg_matiere[$m] != '') {
                                $test = mysql_query("SELECT * FROM j_professeurs_matieres WHERE (id_professeur = '".$_POST['new_login']."' and id_matiere = '$reg_matiere[$m]')");
                                $resultat = mysql_num_rows($test);
                                if ($resultat == 0) {
                                    $reg = mysql_query("INSERT INTO j_professeurs_matieres SET id_professeur = '".$_POST['new_login']."', id_matiere = '$reg_matiere[$m]', ordre_matieres = '0'");
                                }
                            }
                            $reg_matiere[$m] = '';
                            $m++;
                        }
                    }
                    $msg="Vous venez de créer un nouvel utilisateur !<br />Par défaut, cet utilisateur est considéré comme actif.";
                    //$msg = $msg."<br />Pour imprimer les paramètres de l'utilisateur (identifiant, mot de passe, ...), cliquez <a href='impression_bienvenue.php?user_login=".$_POST['new_login']."&mot_de_passe=".urlencode($NON_PROTECT['password1'])."' target='_blank'>ici</a> !";
                    $msg = $msg."<br />Pour imprimer les paramètres de l'utilisateur (identifiant, mot de passe, ...), cliquez <a href='impression_bienvenue.php?user_login=".$_POST['new_login']."&amp;mot_de_passe=".urlencode($NON_PROTECT['password1'])."' target='_blank'>ici</a> !";
                    $msg = $msg."<br />Attention : ultérieurement, il vous sera impossible d'imprimer à nouveau le mot de passe d'un utilisateur ! ";
                    $user_login = $_POST['new_login'];
                }
            }
//
//action s'il s'agit d'une modification
//
        } else if ((isset($user_login)) and ($user_login!='')) {
            if (isset($_POST['deverrouillage'])) {
                $reg_data = sql_query("UPDATE utilisateurs SET date_verrouillage=now() - interval " . getSettingValue("temps_compte_verrouille") . " minute  WHERE login='".strtoupper($user_login)."'");
            }

            $change = "yes";
            $flag = '';
            if ($_POST['reg_statut'] != "professeur") {
                $test = mysql_query("SELECT * FROM j_groupes_professeurs WHERE login='".$user_login."'");
                $nb = mysql_num_rows($test);
                if ($nb != 0) {
                    $msg = "Impossible de changer le statut. Cet utilisateur est actuellement professeur dans certaines classes !";
                    $change = "no";
                } else {
                    $k = 0;
                    while ($k < $_POST['max_mat']) {
                        $reg_matiere[$k] = '';
                        $k++;
                    }
                }
            }
            if ($_POST['reg_statut'] == "professeur") {
            $test = mysql_query("SELECT jgm.id_matiere FROM j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE (" .
                    "jgp.login = '".$user_login."' and " .
                    "jgm.id_groupe = jgp.id_groupe)");
                $nb = mysql_num_rows($test);
                if ($nb != 0) {
                    $k = 0;
                    $change = "yes";
                    while ($k < $nb) {
                        $id_matiere = mysql_result($test, $k, 'id_matiere');
                        $m = 0;
                        while ($m < $_POST['max_mat']) {
                            if ($id_matiere == $reg_matiere[$m]) {$flag = "yes";}
                            $m++;
                        }
                        if ($flag != "yes") {$change = "no";}
                        $k++;
                    }
                    if ($change == "no") {$msg = "Impossible de changer les matières. Cet utilisateur est actuellement professeur dans certaines classes des matières que vous voulez supprimer !";}
                }
            }
            if ($change == "yes") {
            $reg_data = mysql_query("UPDATE utilisateurs SET nom='".$_POST['reg_nom']."',prenom='".$_POST['reg_prenom']."',civilite='".$_POST['reg_civilite']."', login='".$_POST['reg_login']."',statut='".$_POST['reg_statut']."',email='".$_POST['reg_email']."',etat='".$_POST['reg_etat']."' WHERE login='".$user_login."'");
            $del = mysql_query("DELETE FROM j_professeurs_matieres WHERE id_professeur = '".$user_login."'");
                $m = 0;
                while ($m < $_POST['max_mat']) {
                    $num=$m+1;
                    if ($reg_matiere[$m] != '') {
                        $test = mysql_query("SELECT * FROM j_professeurs_matieres WHERE (id_professeur = '".$user_login."' and id_matiere = '$reg_matiere[$m]')");
                        $resultat = mysql_num_rows($test);
                        if ($resultat == 0) {
                        $reg = mysql_query("INSERT INTO j_professeurs_matieres SET id_professeur = '".$user_login."', id_matiere = '$reg_matiere[$m]', ordre_matieres = '$num'");
                        }
                        $reg_matiere[$m] = '';
                    }
                    $m++;
                }
                if (!$reg_data) {
                    $msg = "Erreur lors de l'enregistrement des données";
                } else {
                    $msg="Les modifications ont bien été enregistrées !";
                }
            }
        } else {
            $msg = "L'identifiant de l'utilisateur doit être constitué uniquement de lettres et de chiffres !";

        }
    }
}

// On appelle les informations de l'utilisateur pour les afficher :
if (isset($user_login) and ($user_login!='')) {
    $call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE login='".$user_login."'");
    $user_nom = mysql_result($call_user_info, "0", "nom");
    $user_prenom = mysql_result($call_user_info, "0", "prenom");
    $user_civilite = mysql_result($call_user_info, "0", "civilite");
    $user_statut = mysql_result($call_user_info, "0", "statut");
    $user_email = mysql_result($call_user_info, "0", "email");
    $user_etat = mysql_result($call_user_info, "0", "etat");
    $date_verrouillage = mysql_result($call_user_info, "0", "date_verrouillage");

    $call_matieres = mysql_query("SELECT * FROM j_professeurs_matieres j WHERE j.id_professeur = '".$user_login."' ORDER BY ordre_matieres");
    $nb_mat = mysql_num_rows($call_matieres);
    $k = 0;
    while ($k < $nb_mat) {
        $user_matiere[$k] = mysql_result($call_matieres, $k, "id_matiere");
        $k++;
    }
} else {
    $nb_mat = 0;
    if (isset($_POST['reg_civilite']))
        $user_civilite = $_POST['reg_civilite'];
    else
        $user_civilite = 'M.';
    if (isset($_POST['reg_nom'])) $user_nom = $_POST['reg_nom'];
    if (isset($_POST['reg_prenom'])) $user_prenom = $_POST['reg_prenom'];
    if (isset($_POST['reg_statut'])) $user_statut = $_POST['reg_statut'];
    if (isset($_POST['reg_email'])) $user_email = $_POST['reg_email'];
    if (isset($_POST['reg_etat'])) $user_etat = $_POST['reg_etat'];
}

//**************** EN-TETE *****************
$titre_page = "Gestion des utilisateurs | Modifier un utilisateur";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold>
|<a href="index.php">Retour</a>|<a href='javascript:centrerpopup("help.php",600,480,"scrollbars=yes,statusbar=no,resizable=yes")'>Aide</a>|

<?php
// dans le cas de LCS, existence d'utilisateurs locaux reprérés grâce au champ password non vide.
$testpassword = sql_query1("select password from utilisateurs where login = '".$user_login."'");
if ($testpassword == -1) $testpassword = '';
if (isset($user_login) and ($user_login!='')) {
    if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon"  and ((getSettingValue("use_sso") != "lcs") or ($testpassword !='')) and getSettingValue("use_sso") != "ldap_scribe") OR $block_sso) {
        echo "<a href=\"change_pwd.php?user_login=".$user_login."\">Changer le mot de passe</a>|";
    }
    echo "<a href=\"modify_user.php\">Ajouter un nouvel utilisateur</a>|";
}

?>
</p>
<form enctype="multipart/form-data" action="modify_user.php" method=post>

<!--span class = "norme"-->
<div class = "norme">
<b>Identifiant <?php
if (!isset($user_login)) echo "(" . $longmax_login . " caractères maximum) ";?>:</b>
<?php
if (isset($user_login) and ($user_login!='')) {
    echo "<b>".$user_login."</b>";
    echo "<input type=hidden name=reg_login value=\"".$user_login."\" />";
} else {
    echo "<input type=text name=new_login size=20 value=\"";
    if (isset($user_login)) echo $user_login;
    echo "\" />";
}
?>
<table>
<tr><td>Nom : </td><td><input type=text name=reg_nom size=20 <?php if (isset($user_nom)) { echo "value=\"".$user_nom."\"";}?> /></td></tr>
<tr><td>Prénom : </td><td><input type=text name=reg_prenom size=20 <?php if (isset($user_prenom)) { echo "value=\"".$user_prenom."\"";}?> /></td></tr>
<tr><td>Civilité : </td><td><select name="reg_civilite" size="1">
<option value=''>(néant)</option>
<option value='M.' <?php if ($user_civilite=='M.') echo " selected ";  ?>>M.</option>
<option value='Mme' <?php if ($user_civilite=='Mme') echo " selected ";  ?>>Mme</option>
<option value='Mlle' <?php if ($user_civilite=='Mlle') echo " selected ";  ?>>Mlle</option>
</select>
</td></tr>
<tr><td>Email : </td><td><input type=text name=reg_email size=30 <?php if (isset($user_email)) { echo "value=\"".$user_email."\"";}?> /></td></tr>
</table>
<?php
if (!(isset($user_login)) or ($user_login=='')) {
    if (getSettingValue("use_sso") == "lcs") {
        echo "<table border=\"1\" cellpadding=\"5\" cellspacing=\"1\"><tr><td>";
        echo "<input type=\"radio\" name=\"is_lcs\" value=\"y\" checked /> Utilisateur LCS";
        echo "<br /><i>Un utilisateur LCS est un utilisateur authentifié par LCS : dans ce cas, ne pas remplir les champs \"mot de passe\" ci-dessous.</i>";
        echo "</td></tr><tr><td>";
        echo "<input type=\"radio\" name=\"is_lcs\" value=\"n\" /> Utilisateur local";
        echo "<br /><i>Un utilisateur local doit systématiquement s'identifier sur GEPI avec le mot de passe ci-dessous, même s'il est un utilisateur authentifié par LCS.</i>";
        echo "<br /><i><b>Remarque</b> : l'adresse pour se connecter localement est du type : http://mon.site.fr/gepi/login.php?local=y (ne pas omettre \"<b>?local=y</b>\").</i>";
        echo "<br /><br />";
    }
    echo "<table><tr><td>Mot de passe (".getSettingValue("longmin_pwd") ." caractères minimum) : </td><td><input type=password name=no_anti_inject_password1 size=20 /></td></tr>";
    echo "<tr><td>Mot de passe (à confirmer) : </td><td><input type=password name=reg_password2 size=20 /></td></tr></table>";
    echo "<br /><b>Attention : le mot de passe doit comporter ".getSettingValue("longmin_pwd")." caractères minimum et doit être composé à la fois de lettres et de chiffres.</b>";
    echo "<br /><b>Remarque</b> : lors de la création d'un utilisateur, il est recommandé de choisir le NUMEN comme mot de passe.<br />";
    if (getSettingValue("use_sso") == "lcs") echo "</td></tr></table>";

}
?>
<br />Statut (consulter l'<a href='javascript:centrerpopup("help.php",600,480,"scrollbars=yes,statusbar=no,resizable=yes")'>aide</a>) : <SELECT name=reg_statut size=1>
<?php if (!isset($user_statut)) $user_statut = "professeur"; ?>
<option value=professeur <?php if ($user_statut == "professeur") { echo "SELECTED";}?>>Professeur
<option value=administrateur <?php if ($user_statut == "administrateur") { echo "SELECTED";}?>>Administrateur
<option value=cpe <?php if ($user_statut == "cpe") { echo "SELECTED";}?>>C.P.E.
<option value=scolarite <?php if ($user_statut == "scolarite") { echo "SELECTED";}?>>Scolarité
<option value=secours <?php if ($user_statut == "secours") { echo "SELECTED";}?>>Secours
</select>
<br />

<br />Etat :<select name=reg_etat size=1>
<?php if (!isset($user_etat)) $user_etat = "actif"; ?>
<option value=actif <?php if ($user_etat == "actif") { echo "SELECTED";}?>>Actif
<option value=inactif <?php if ($user_etat == "inactif") { echo "SELECTED";}?>>Inactif
</select>
<br />

<?php
$k = 0;
while ($k < $nb_mat+1) {
    $num_mat = $k+1;
    echo "Matière N°$num_mat (si professeur): ";
    $temp = "matiere_".$k;
    echo "<select size=1 name='$temp'>\n";
    $calldata = mysql_query("SELECT * FROM matieres ORDER BY matiere");
    $nombreligne = mysql_num_rows($calldata);
    echo "<option value='' "; if (!(isset($user_matiere[$k]))) {echo " SELECTED";} echo ">(vide)</option>";
    $i = 0;
    while ($i < $nombreligne){
        $matiere_list = mysql_result($calldata, $i, "matiere");
        $matiere_complet_list = mysql_result($calldata, $i, "nom_complet");
        //echo "<option value=$matiere_list "; if (isset($user_matiere[$k]) and ($matiere_list == $user_matiere[$k])) {echo " SELECTED";} echo ">$matiere_list | $matiere_complet_list</option>";
        echo "<option value=$matiere_list "; if (isset($user_matiere[$k]) and ($matiere_list == $user_matiere[$k])) {echo " SELECTED";} echo ">$matiere_list | ".htmlentities($matiere_complet_list)."</option>\n";
        $i++;
    }
    echo "</select><br />\n";
    $k++;
}
$nb_mat++;

// Déverrouillage d'un compte
if (isset($user_login) and ($user_login!='')) {
    $day_now   = date("d");
    $month_now = date("m");
    $year_now  = date("Y");
    $hour_now  = date("H");
    $minute_now = date("i");
    $seconde_now = date("s");
    $now = mktime($hour_now, $minute_now, $seconde_now, $month_now, $day_now, $year_now);

    $annee_verrouillage = substr($date_verrouillage,0,4);
    $mois_verrouillage =  substr($date_verrouillage,5,2);
    $jour_verrouillage =  substr($date_verrouillage,8,2);
    $heures_verrouillage = substr($date_verrouillage,11,2);
    $minutes_verrouillage = substr($date_verrouillage,14,2);
    $secondes_verrouillage = substr($date_verrouillage,17,2);
    $date_verrouillage = mktime($heures_verrouillage, $minutes_verrouillage, $secondes_verrouillage, $mois_verrouillage, $jour_verrouillage, $annee_verrouillage);
    if ($date_verrouillage  > ($now- getSettingValue("temps_compte_verrouille")*60)) {
        echo "<br /><center><table border=\"1\" cellpadding=\"5\" width = \"90%\" bgcolor=\"#FFB0B8\"><tr><td>";
        echo "<H2>Verrouillage/Déverrouillage du compte</h2>";
        echo "Suite à un trop grand nombre de tentatives de connexions infructueuses, le compte est actuellement verrouillé.";
        echo "<br /><input type=\"checkbox\" name=\"deverrouillage\" value=\"yes\" /> Cochez la case pour deverrouiller le compte";
        echo "</td></tr></table></center>\n";
    }
}

echo "<input type=hidden name=max_mat value=$nb_mat />\n";
?>
<input type=hidden name=valid value="yes" />
<?php if (isset($user_login)) echo "<input type=hidden name=user_login value=\"".$user_login."\" />\n"; ?>
<center><input type=submit value=Enregistrer /></center>
<!--/span-->
</div>
</form>

</body>
</html>