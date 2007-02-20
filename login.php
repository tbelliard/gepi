<?php
/*
 * Last modification  : 13/07/2006
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

// Vérification de la bonne installation de GEPI
require_once("./utilitaires/verif_install.php");

$niveau_arbo = 0;

// On indique qu'il faut crée des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("./lib/initialisations.inc.php");

// On se charge immédiatement de l'authentification par SSO, si besoin

$use_sso = null;
$use_sso = getSettingValue('use_sso');
if (!(isset($_GET['local']))) $_GET['local'] = false;

if (isset($use_sso) and ($use_sso == "cas") and !$block_sso) {
    require_once("./lib/cas.inc.php");
    // A ce stade, l'utilisateur est authentifié par CAS

    $password = '';
    $sso_login = 'cas';
    $result = openSession($login,$password,$sso_login);
    session_write_close();
    header("Location:accueil.php");
    die();
} elseif (isset($use_sso) and ($use_sso == "lemon") and !$block_sso) {
    if (isset($_GET['login'])) $login = $_GET['login']; else $login = "";
    if (isset($_COOKIE['user'])) $cookie_user=$_COOKIE['user']; else $cookie_user="";
    if(empty($cookie_user) or $cookie_user != $login) {
      header("Location: ./login.php");
      // Echec de l'authentification lemonldap
      die();
      echo "</body></html>";
    }
  // A ce stade, l'utilisateur est authentifié par Lemonldap
    $sso_login = 'lemon';
    $password = '';
    $login = strtoupper($login);
    $result = openSession($login,$password,$sso_login) ;
    session_write_close();
    header("Location:accueil.php");
    die();
} elseif (!($_GET['local']) and isset($use_sso) and ($use_sso == "lcs") and !$block_sso and
 !(isset($_POST['login']) && isset($_POST['no_anti_inject_password']))) {
  include LCS_PAGE_AUTH_INC_PHP;
  include LCS_PAGE_LDAP_INC_PHP;
  list ($idpers,$login) = isauth();
  if ($idpers) {
      list($user, $groups)=people_get_variables($login, false);
      $lcs_tab_login["nom"] = $user["nom"];
      $lcs_tab_login["email"] = $user["email"];
      $long = strlen($user["fullname"]) - strlen($user["nom"]);
      $lcs_tab_login["fullname"] = substr($user["fullname"], 0, $long) ;
      // A ce stade, l'utilisateur est authentifié par CAS
      // Etablir à nouveau la connexion à la base
      if (empty($db_nopersist))
          $db_c = mysql_pconnect($dbHost, $dbUser, $dbPass);
      else
          $db_c = mysql_connect($dbHost, $dbUser, $dbPass);
      if (!$db_c || !mysql_select_db ($dbDb)) {
          echo "\n<p>Erreur : Echec de la connexion à la base de données";
          exit;
      }
      if (is_eleve($login)) {
         // On renvoie à la page d'accueil des cahiers de texte
         session_write_close();
         header("Location: ./public/index.php");
         die();
      }
      $password = '';
      $result = openSession($login,$password,"lcs",$lcs_tab_login) ;
      $message = '';
      if ($result=="1") {
        // on efface les logs conformément à la durée de conservation des logs
        sql_query("delete from log where START < now() - interval " . getSettingValue("duree_conservation_logs") . " day and END < now()");
        // On renvoie à la page d'accueil
        session_write_close();
        header("Location: ./accueil.php");
        die();
      } else if ($result=="dl") {
        $message = "GEPI est momentanément inaccessible.";
      } else if ($result=="verrouillage") {
        $message = "Trop de tentatives de connexion infructueuses : votre compte est momentanément verrouillé.";
      } else if ($result=="liste_noire") {
        $message = "Connexion impossible : vous tentez de vous connecter à partir d'une adresse IP interdite.";
      } else if ($result=="2") {
        $message = "Vous avez bien été identifié mais la mise à jour de votre profil dans GEPI n'a pas pu s'effectuer correctement. Impossible de continuer. Veuillez signaler ce problème à l'administrateur du site.";
      } else if ($result=="3") {
        $message = "Vous avez bien été identifié mais un utilisateur \"local\" dans la base de GEPI, ayant le même login, existe déjà. Impossible de continuer. Veuillez signaler ce problème à l'administrateur du site.";
      } else if ($result=="4") {
        $message = "Vous avez bien été identifié mais vous ne figurez pas parmi les utilisateurs dans la base de GEPI. Impossible de continuer. Veuillez signaler ce problème à l'administrateur du site.";
      } else {
        $message = "Vous avez bien été identifié mais un problème est survenu. Impossible de continuer. Veuillez signaler ce problème à l'administrateur du site.";
      }
      if ($message != '') {
          echo $message;
          echo "</body></html>";
          die();
      }
      if (resumeSession() ) {
        // On renvoie à la page d'accueil
        session_write_close();
        header("Location: ./accueil.php");
        die();
      } else {
    // L'utilisateur n'a pas été identifié'
         header("Location:".LCS_PAGE_AUTHENTIF);
      }
   } else {
    // L'utilisateur n'a pas été identifié'
         header("Location:".LCS_PAGE_AUTHENTIF);
   }

}


// User wants to be authentified
if (isset($_POST['login']) && isset($_POST['no_anti_inject_password'])) {
    $md5password = md5($NON_PROTECT['password']);

    if (isset($use_sso) and ($use_sso == "ldap_scribe") and !$block_sso) {
        $temp = openSession($_POST['login'], $NON_PROTECT['password'], $use_sso);
    } else {
        $temp = openSession($_POST['login'], $md5password);
    }

    if ($temp=="1") {
        // on efface les logs conformément à la durée de conservation des logs
        sql_query("delete from log where START < now() - interval " . getSettingValue("duree_conservation_logs") . " day and END < now()");
        // On renvoie à la page d'accueil
        session_write_close();
        header("Location: ./accueil.php");
        die();
    } else if ($temp=="c") {
    	session_write_close();
        header("Location: ./utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
        die();
    } else if ($temp=="dl") {
        $message = "Site momentanément inaccessible.";
    } else if ($temp=="verrouillage") {
        $message = "Trop de tentatives de connexion infructueuses : votre compte est momentanément verrouillé.";
    } else if ($temp=="liste_noire") {
        $message = "Connexion impossible : vous tentez de vous connecter à partir d'une adresse IP interdite.";
    } else {
        $message = "Identifiant ou mot de passe incorrect";
    }
} else {
    // on ferme une éventuelle session ouverte précédemment
    //closeSession($_GET['auto']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
<META HTTP-EQUIV="Expires" CONTENT="0" />
<title><?php echo getSettingValue("gepiSchoolName"); ?> : base de données élèves | Identifiez vous...</title>
<link rel="stylesheet" type="text/css" href="./style.css" />
<script src="lib/functions.js" type="text/javascript" language="javascript"></script>
<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
<link rel="icon" type="image/ico" href="./favicon.ico" />
</head>
<body onload="document.getElementById('login').focus()">
<h1 class='gepi'>Gestion et visualisation graphique des résultats scolaires</h1>
<h2 class='gepi'><?php echo getSettingValue("gepiSchoolName"). " - année scolaire " . getSettingValue("gepiYear"); ?></h2>
<div class="center">

<p>En raison du caractère personnel du contenu, ce site est soumis à des restrictions utilisateurs. Pour accéder aux outils de gestion, identifiez-vous :</p>
<?php
//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")=='y') {
   echo "<a href=\"./public/index.php\">Consulter les cahiers de texte</a> (tout public)";
}
if ((getSettingValue("disable_login"))=='yes') echo "<br><br><font color=\"red\" size=\"+1\">Le site est en cours de maintenance et temporairement inaccessible.<br />Veuillez nous excuser de ce dérangement et réessayer de vous connecter ultérieurement.</font><br>";

?>
<form action="login.php" method="post" style="width: 100%; margin-top: 24px; margin-bottom: 48px;">
<?php
if (isset($message)) echo("<p><font color=red>" . $message . "</font></p>");
?>
<fieldset style="padding-top: 8px; padding-bottom: 8px; width: 40%; margin-left: auto; margin-right: auto;">
<legend style="font-variant: small-caps;">Identification</legend>
<table style="width: 100%; border: 0;" cellpadding="5" cellspacing="0">
  <tr>
    <td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="login">Identifiant</label></td>
    <td style="text-align: center; width: 60%;"><input type="text" id="login" name="login" size="16" tabindex="1" /></td>
  </tr>
  <tr>
    <td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="no_anti_inject_password">Mot de passe</label></td>
    <td style="text-align: center; width: 60%;"><input type="password" id="no_anti_inject_password" name="no_anti_inject_password" size="16" onkeypress="capsDetect(arguments[0]);" tabindex="2" /></td>
  </tr>
</table>
<input type="submit" name="submit" value="Valider" style="font-variant: small-caps;" tabindex="3" />
</fieldset>
</form>
</div>
<div class="center" style="margin-bottom: 32px;">
<p><a href="javascript:centrerpopup('gestion/info_vie_privee.php',700,480,'scrollbars=yes,statusbar=no,resizable=yes')">Informations vie privée</a></p>
<p><a href="mailto:<?php echo getSettingValue("gepiAdminAdress"); ?>">[Contacter l'administrateur]</a></p>
</div>

<div class="center" style="width: 200px; margin-bottom: 32px;">
<a href='http://www.php.net'><img src="./php4.gif" alt="Powered by php4" width="88" height="31" style="border: 0; float: left;" /></a><a href='http://www.mysql.org'><img src="./mysqllogo.gif" alt="Powered by MySQL" width="88" height="31" style="border: 0; float: right;" /></a><br />
</div>

<div class="center">
<p class="small">
<a href="http://gepi.mutualibre.org">GEPI : Outil de gestion, de suivi, et de visualisation graphique des résultats scolaires (écoles, collèges, lycées)</a><br />
Copyright &copy; 2001-2005
<?php
reset($gepiAuthors);
$i = 0;
while (list($name, $adress) = each($gepiAuthors)) {
    if ($i != "0") echo ", ";
    echo("<a href=\"mailto:" . $adress . "\">" . $name . "</a> ");
    $i++;
}

?>
</p>
</div>
</body>
</html>