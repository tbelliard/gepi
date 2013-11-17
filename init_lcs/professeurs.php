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

function connect_ldap($l_adresse,$l_port,$l_login,$l_pwd) {
    $ds = @ldap_connect($l_adresse, $l_port);
    if($ds) {
       // On dit qu'on utilise LDAP V3, sinon la V2 par d?faut est utilis? et le bind ne passe pas.
       $norme = @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
       // Acc?s non anonyme
       if ($l_login != '') {
          // On tente un bind
          $b = @ldap_bind($ds, $l_login, $l_pwd);
       } else {
          // Acc?s anonyme
          $b = @ldap_bind($ds);
       }
       if ($b) {
           return $ds;
       } else {
           return false;
       }
    } else {
       return false;
    }
}

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

function add_user($_login, $_nom, $_prenom, $_sexe, $_statut, $_email) {
    // Fonction d'ajout de l'utilisateur

        if ($_sexe == "M") {
            $_civilite = "M.";
        } else {
            $_civilite = "Mme";
        }


    // Si l'utilisateur existe déjà, on met simplement à jour ses informations...
    $test = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM utilisateurs WHERE login = '" . $_login . "'");
    if (mysqli_num_rows($test) > 0) {
        $record = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs SET
        nom = '" . $_nom . "',
        prenom = '" . $_prenom . "',
        civilite = '" . $_civilite . "',
        email = '" . $_email . "',
        statut = '" . $_statut . "',
        etat = 'actif',
        auth_mode='sso'
        WHERE login = '" . $_login . "'");
    } else {
        $query = "INSERT into utilisateurs SET
        login= '" . $_login . "',
        nom = '" . $_nom . "',
        prenom = '" . $_prenom . "',
        password = '',
        civilite = '" . $_civilite . "',
        email = '" . $_email . "',
        statut = '" . $_statut . "',
        etat ='actif',
        auth_mode='sso',
        change_mdp = 'n'";
        $record = mysqli_query($GLOBALS["mysqli"], $query);
    }

    if ($record) {
        return true;
    } else {
        return false;
    }
}


// Initialisation
$lcs_ldap_people_dn = 'ou=people,'.$lcs_ldap_base_dn;
$lcs_ldap_groups_dn = 'ou=groups,'.$lcs_ldap_base_dn;

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des professeurs";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href='../init_lcs/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

if (isset($_POST['is_posted'])) {
	check_token();

    // L'admin a validé la procédure, on procède donc...

    // On se connecte au LDAP
    $ds = connect_ldap($lcs_ldap_host,$lcs_ldap_port,"","");
    // LDAP attribute
    $ldap_people_attr = array(
        "uid",               // login
        "cn",                // Prenom  Nom
        "sn",               // Nom
        "givenname",            // Pseudo
        "mail",              // Mail
        "homedirectory",           // Home directory personnal web space
        "description",
        "loginshell",
        "gecos"             // Date de naissance,Sexe (F/M),
        );

    echo "<table border=\"1\" cellpadding=\"3\" cellspacing=\"3\">\n<tr><td>Login Professeur</td><td>Nom </td><td>Prénom</td><td>Sexe</td><td>Email</td></tr>\n";
    // On commence par récupérer tous les profs depuis le LDAP
    $attr[] = "memberuid";
    $result = ldap_read ( $ds, "cn=Profs,".$lcs_ldap_groups_dn, "(objectclass=*)",$attr);

    // On met tous les professeurs en état inactif
    $update = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs SET etat='inactif' WHERE statut='professeur'");
    $info = ldap_get_entries ( $ds, $result );
    if ( $info["count"]) {
         for($i=0;$i<$info[0]["memberuid"]["count"];$i++) {
             $uid = $info[0]["memberuid"][$i];
             if (($uid != "etabw") and ($uid!="webmaster.etab") and ($uid!="spip.manager")) {

             // Extraction des infos sur le professeur :
             $result2 = @ldap_read ( $ds, "uid=".$uid.",".$lcs_ldap_people_dn, "(objectclass=posixAccount)", $ldap_people_attr );
             if ($result2) {
                 $info2 = @ldap_get_entries ( $ds, $result2 );
                 if ( $info2["count"]) {
                     // Traitement du champ gecos pour extraction de date de naissance, sexe
                     $gecos = $info2[0]["gecos"][0];
                     $tmp = split ("[\,\]",$info2[0]["gecos"][0],4);
                     $ret_people = array (
                     "nom"         => stripslashes($info2[0]["sn"][0]),
                     "fullname"    => stripslashes($info2[0]["cn"][0]),
                     "email"       => $info2[0]["mail"][0],
                     "sexe"        => $tmp[2],
                     );
                     $long = mb_strlen($ret_people["fullname"]) - mb_strlen($ret_people["nom"]);
                     $prenom = mb_substr($ret_people["fullname"], 0, $long) ;
                 }
                 @ldap_free_result ( $result2 );
             }
             // On ajoute l'utilisateur. La fonction s'occupe toute seule de vérifier que
             // le login n'existe pas déjà dans la base. S'il existe, on met simplement à jour
             // les informations
             // function add_user($_login, $_nom, $_prenom, $_statut) {
             $add = add_user($uid,$ret_people["nom"],$prenom,$ret_people["sexe"],"professeur",$ret_people["email"]);
             echo "<tr><td>".$uid."</td><td>".$ret_people["nom"]."</td><td>".$prenom."</td><td>".$tmp[2]."</td><td>".$ret_people["email"]."</td></tr>\n";
             }
         }
         echo "<table>";
    }

    echo "<p>Opération effectuée.</p>";
    echo "<p>Vous pouvez vérifier l'importation en allant sur la page de <a href='../utilisateurs/index.php'>gestion des utilisateurs</a>.</p>";

} else {
    echo "<p>L'opération d'importation des professeurs depuis le LDAP de LCS va effectuer les opérations suivantes :</p>";
    echo "<ul>";
    echo "<li>Passage à l'état 'inactif' de tous les professeurs déjà présents dans la base Gepi.</li>";
    echo "<li>Tentative d'ajout de chaque utilisateur 'professeur' présent dans l'annuaire LDAP de LCS.</li>";
    echo "<li>Si l'utilisateur n'existe pas, il est créé et est directement utilisable.</li>";
    echo "<li>Si l'utilisateur existe déjà, ses informations de base sont mises à jour et il passe en état 'actif', devenant directement utilisable.</li>";
    echo "</ul>";
    echo "<form enctype='multipart/form-data' action='professeurs.php' method=post>";
	echo add_token_field();
    echo "<input type=hidden name='is_posted' value='yes'>";

    echo "<p>Etes-vous sûr de vouloir importer tous les utilisateurs depuis l'annuaire du serveur LCS vers Gepi ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='Je suis sûr'>";
    echo "</form>";
}
require("../lib/footer.inc.php");
?>
