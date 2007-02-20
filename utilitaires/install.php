<?php
/*
 * Last modification  : 10/03/2005
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
require_once("../lib/global.inc");
$nom_fic = "../secure/connect.inc.php";

function test_ecriture_secure() {
    $ok = 'no';
    if ($f = @fopen("../secure/test", "w")) {
        @fputs($f, '<'.'?php $ok = "yes"; ?'.'>');
        @fclose($f);
        include("../secure/test");
        $del = @unlink("../secure/test");
    }
    return $ok;
}


function begin_html() {
    ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Installation de GEPI</title>
<meta HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
<META HTTP-EQUIV="Expires" CONTENT="0" />
<LINK REL="stylesheet" href="../style.css" type="text/css" />
<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
<link rel="icon" type="image/ico" href="../favicon.ico" />
</head>
<body>
<center>
<table width="450">
<tr><td width="450">
    <?php
}

function end_html() {
    echo '
    </td></tr></table>
    </center>
    </body>
    </html>
    ';
}

unset($etape);
$etape = isset($_POST["etape"]) ? $_POST["etape"] : (isset($_GET["etape"]) ? $_GET["etape"] : NULL);


if (file_exists($nom_fic)) {
    require_once("../secure/connect.inc.php");
    if (@mysql_connect("$dbHost", "$dbUser", "$dbPass")) {
        if (@mysql_select_db("$dbDb")) {
            // Premier test
            $liste2 = array();
            $tableNames = @mysql_list_tables($dbDb);
            $j = '0';
            while ($j < @mysql_num_rows($tableNames)) {
                $liste2[$j] = @mysql_tablename($tableNames, $j);
                $j++;
            }
            $j = '0';
            $test1='yes';
            while ($j < count($liste_tables)) {
                $temp = $liste_tables[$j];
                if (!(in_array($temp, $liste2))) {
                    $correct_install='no';
                    $test1 = 'no';
                }
                $j++;
            }
            $call_test = @mysql_query("SELECT * FROM setting WHERE NAME='sessionMaxLength'");
            $test2 = @mysql_num_rows($call_test);
            $call_test = @mysql_query("SELECT * FROM utilisateurs");
            $test3 = @mysql_num_rows($call_test);
            if (($test2 !=0) and ($test1 != 'no') and ($test3 !=0)) {
                begin_html();
                if ($etape == 5) {
                    echo "<br /><h2 class='gepi'>Dernière étape : C'est terminé !</h2>";
                    echo "<P>";
                    echo "<P>Vous pouvez maintenant commencer à utiliser GEPI ...</p>";
                    echo "<P>Pour vous connecter la première fois en tant qu'administrateur, utilisez le nom de connection \"admin\" et le mot de passe \"azerty\". N'oubliez pas de changer le mot de passe !</p>";
                    echo "<br /><center><a href = '../login.php'>Se connecter à GEPI</a></center>";
                } else {
                    echo "<h2 class='gepi'>Espace interdit - GEPI est déjà installé.</h2>";
                }
                end_html();
                die();
            }
        }
    }
}

if ($etape == 4) {

    begin_html();

    echo "<br /><h2 class='gepi'>Quatrième étape : Création des tables de la base</h2>";
    echo "<P>";

    $link = mysql_connect($_POST['adresse_db'], $_POST['login_db'], $_POST['pass_db']);

    if ($_POST['choix_db'] == "new_gepi") {
        $sel_db = $_POST['table_new'];
        $result=mysql_query("CREATE DATABASE $sel_db;");
    }
    else {
        $sel_db = $_POST['choix_db'];
    }
    mysql_select_db("$sel_db");

    $fd = fopen("../sql/structure_gepi.sql", "r");
    $result_ok = 'yes';
    while (!feof($fd)) {
       $query = fgets($fd, 5000);
       $query = trim($query);
       if (substr($query,-1)==";") {
            $reg = mysql_query($query);
            if (!$reg) {
                echo "<p><font color=red>ERROR</font> : '$query'";
                $result_ok = 'no';
            }
       }
    }
    fclose($fd);

    if ($result_ok == 'yes') {
        $fd = fopen("../sql/data_gepi.sql", "r");
        while (!feof($fd)) {
            $query = fgets($fd, 5000);
            $query = trim($query);
            if (substr($query,-1)==";") {
                $reg = mysql_query($query);
                if (!$reg) {
                    echo "<p><font color=red>ERROR</font> : '$query'";
                    $result_ok = 'no';
                }
            }
        }
        fclose($fd);
    }

    if ($result_ok == 'yes') {
        $ok = 'yes';
        if (file_exists($nom_fic)) @unlink($nom_fic);
        if (file_exists("../secure/connect.cfg")) @unlink("../secure/connect.cfg");
        $f = @fopen($nom_fic, "wb");
        if (!$f) {
            $ok = 'no';
        } else {
            $url = parse_url($_SERVER['REQUEST_URI']);
            //$pathgepi = explode("/",$url['path']);
            //$gepipath = "/".$pathgepi[1];
			$gepipath = substr($url['path'], 0, -24);
            $conn = "<"."?php\n";
            $conn .= "# Les cinq lignes suivantes sont à modifier selon votre configuration\n";
            $conn .= "# Pensez à renommer ce fichier connect.cfg.php en connect.inc.php\n";
            $conn .= "#\n";
            $conn .= "# ligne suivante : le nom du serveur qui herberge votre base mysql.\n";
            $conn .= "# Si c'est le même que celui qui heberge les scripts, mettre \"localhost\"\n";
            $conn .= "\$dbHost=\"".$_POST['adresse_db']."\";\n";
            $conn .= "# ligne suivante : le nom de votre base mysql\n";
            $conn .= "\$dbDb=\"$sel_db\";\n";
            $conn .= "# ligne suivante : le nom de l'utilisateur mysql qui a les droits sur la base\n";
            $conn .= "\$dbUser=\"".$_POST['login_db']."\";\n";
            $conn .= "# ligne suivante : le mot de passe de l'utilisateur mysql ci-dessus\n";
            $conn .= "\$dbPass=\"".$_POST['pass_db']."\";\n";
            $conn .= "# Chemin relatif vers GEPI\n";
            $conn .= "\$gepiPath=\"$gepipath\";\n";
            $conn .= "#\n";
            $conn .= "# Authentification par CAS ?\n";
            $conn .= "# Si vous souhaitez intégrer Gepi dans un environnement SSO avec CAS,\n";
            $conn .= "# vous devrez renseigner le fichier /lib/CAS/cas.sso.php avec les\n";
            $conn .= "# informations nécessaires à l'identification du serveur CAS\n";
            $conn .= "\$use_cas = false; // false|true\n";
            $conn .= "?".">";

            @fputs($f, $conn);
            if (!@fclose($f)) $ok='no';
        }

        if ($ok == 'yes') {
            echo "<B>La structure de votre base de données est installée.</B><P>Vous pouvez passer à l'étape suivante.";
            echo "<FORM ACTION='install.php' METHOD='post'>";
            echo "<INPUT TYPE='hidden' NAME='etape' VALUE='5' />";
            echo "<DIV align='right'><INPUT TYPE='submit' CLASS='fondl' NAME='Valider' VALUE='Suivant >>' />";
            echo "</FORM>";
        }
    }

    if (($result_ok != 'yes') or ($ok != 'yes')) {
        echo "<p><B>L'opération a échoué.</B> Retournez à la page précédente, sélectionnez une autre base ou créez-en une nouvelle. Vérifiez les informations fournies par votre hébergeur.</p>";
    }

    end_html();

}

else if ($etape == 3) {

    begin_html();

    echo "<br /><h2 class='gepi'>Troisième étape : Choix de votre base</h2>";

    echo "<P>";

    echo "<FORM ACTION='install.php' METHOD='post'>";
    echo "<INPUT TYPE='hidden' NAME='etape' VALUE='4' />";
    echo "<INPUT TYPE='hidden' NAME='adresse_db'  VALUE=\"".$_POST['adresse_db']."\" SIZE='40' />";
    echo "<INPUT TYPE='hidden' NAME='login_db' VALUE=\"".$_POST['login_db']."\" />";
    echo "<INPUT TYPE='hidden' NAME='pass_db' VALUE=\"".$_POST['pass_db']."\" /><P>";

    $link = @mysql_connect($_POST['adresse_db'],$_POST['login_db'],$_POST['pass_db']);
    $result = @mysql_list_dbs();

    echo "<fieldset><label><B>Choisissez votre base :</B><br /></label>";
    $checked = false;
    if ($result AND (($n = @mysql_num_rows($result)) > 0)) {
        echo "<p><B>Le serveur MySQL contient plusieurs bases de données.<br />Sélectionnez celle dans laquelle vous voulez implanter GEPI</b></p>";
        echo "<UL>";
        $bases = "";
        for ($i = 0; $i < $n; $i++) {
            $table_nom = mysql_dbname($result, $i);
            $base = "<INPUT NAME=\"choix_db\" VALUE=\"".$table_nom."\" TYPE=Radio id='tab$i'";
            $base_fin = " /><label for='tab$i'>".$table_nom."</label><br />\n";
            if ($table_nom == $_POST['login_db']) {
                $bases = "$base CHECKED$base_fin".$bases;
                $checked = true;
            }
            else {
                $bases .= "$base$base_fin\n";
            }
        }
        echo $bases."</UL>";
        echo "ou... ";
    }
    else {
        echo "<B>Le programme d'installation n'a pas pu lire les noms des bases de données installées.</B>Soit aucune base n'est disponible, soit la fonction permettant de lister les bases a été désactivée pour des raisons de sécurité.<P>";
        if ($_POST['login_db']) {
            echo "Dans la seconde alternative, il est probable qu'une base portant votre nom de connexion soit utilisable :";
            echo "<UL>";
            echo "<INPUT NAME=\"choix_db\" VALUE=\"".$_POST['login_db']."\" TYPE=Radio id='stand' CHECKED />";
            echo "<label for='stand'>".$_POST['login_db']."</label><br />\n";
            echo "</UL>";
            echo "ou... ";
            $checked = true;
        }
    }
    echo "<INPUT NAME=\"choix_db\" VALUE=\"new_gepi\" TYPE=Radio id='nou'";
    if (!$checked) echo " CHECKED";
    echo " /> <label for='nou'>Créer une nouvelle base de données :</label> ";
    echo "<INPUT TYPE='text' NAME='table_new' CLASS='fondo' VALUE=\"gepi\" SIZE='20' /></fieldset><P>";
    echo "<p><b>Attention</b> : lors de la prochaine étape :";
    echo "<ul>";
    if (file_exists($nom_fic)) echo "<li>le fichier \"".$nom_fic."\" sera actualisé avec les données que vous avez fourni,</li>";
    echo "<LI>les tables GEPI seront créées dans la base sélectionnée. Si celle-ci contient déjà des tables GEPI, ces tables, ainsi que les données qu'elles contiennent, seront supprimées et remplacées par une nouvelle structure.</LI></ul>";

    echo "<DIV align='right'><INPUT TYPE='submit' CLASS='fondl' NAME='Valider' VALUE='Suivant >>' />";


    echo "</FORM>";

    end_html();

}

else if ($etape == 2) {
    begin_html();

    echo "<br /><h2 class='gepi'>Deuxième étape : Essai de connexion au serveur Mysql</H2>";

    echo "<!--";
    $link = mysql_connect($_POST['adresse_db'],$_POST['login_db'],$_POST['pass_db']);
    $db_connect = mysql_errno();
    echo "-->";

    echo "<P>";

    if (($db_connect=="0") && $link){
        echo "<B>La connexion a réussi.</B><P> Vous pouvez passer à l'étape suivante.</p>";

        echo "<FORM ACTION='install.php' METHOD='post'>";
        echo "<INPUT TYPE='hidden' NAME='etape' VALUE='3' />";
        echo "<INPUT TYPE='hidden' NAME='adresse_db'  VALUE=\"".$_POST['adresse_db']."\" SIZE='40' />";
        echo "<INPUT TYPE='hidden' NAME='login_db' VALUE=\"".$_POST['login_db']."\" />";
        echo "<INPUT TYPE='hidden' NAME='pass_db' VALUE=\"".$_POST['pass_db']."\" /><P>";

        echo "<DIV align='right'><INPUT TYPE='submit' CLASS='fondl' NAME='Valider' VALUE='Suivant >>' />";
        echo "</FORM>";
    }
    else {
        echo "<B>La connexion au serveur MySQL a échoué.</B>";
        echo "<P>Revenez à la page précédente, et vérifiez les informations que vous avez fournies.</p>";
    }

    end_html();

}
else if ($etape == 1) {
    begin_html();

    echo "<br /><h2 class='gepi'>Première étape : la connexion MySQL</h2>";

    echo "<P>Vous devez avoir en votre possession les codes de connexion au serveur MySQL. Si ce n'est pas le cas, contactez votre hébergeur ou bien l'administrateur technique du serveur sur lequel vous voulez implanter GEPI.</p>";

    unset($adresse_db);
    $adresse_db = isset($_POST["adresse_db"]) ? $_POST["adresse_db"] : 'localhost';
    $login_db = '';
    $pass_db = '';

    echo "<p><FORM ACTION='install.php' METHOD='post'>";
    echo "<INPUT TYPE='hidden' NAME='etape' VALUE='2' />";
    echo "<fieldset><label><B>Adresse de la base de donnée</B><br /></label>";
    echo "(Souvent cette adresse correspond à celle de votre site, parfois elle correspond à la mention &laquo;localhost&raquo;, parfois elle est laissée totalement vide.)<br />";
    echo "<INPUT  TYPE='text' NAME='adresse_db' CLASS='formo' VALUE=\"$adresse_db\" SIZE='40' /></fieldset><P>";
    echo "<fieldset><label><B>L'identifiant de connexion</B><br /></label>";
    echo "<INPUT TYPE='text' NAME='login_db' CLASS='formo' VALUE=\"$login_db\" SIZE='40' /></fieldset><P>";
    echo "<fieldset><label><B>Le mot de passe de connexion</B><br /></label>";
    echo "<INPUT TYPE='password' NAME='pass_db' CLASS='formo' VALUE=\"$pass_db\" SIZE='40' /></fieldset><P>";
    echo "<DIV align='right'><INPUT TYPE='submit' CLASS='fondl' NAME='Valider' VALUE='Suivant >>' />";
    echo "</FORM>";

    end_html();

} else if (!$etape) {
    $affiche_etape0 = 'no';
    $file_existe = 'no';
    if (file_exists($nom_fic)) {
        $affiche_etape0 = 'yes';
        $file_existe = 'yes';
    }
    // on test la possibilité d'écrire dans le répertoire
    $test_write = test_ecriture_secure();
    if ($test_write == 'no') $affiche_etape0 = 'yes';

    if ($affiche_etape0 == 'yes') {
        begin_html();
        echo "<h2 class='gepi'>Installation de la base Mysql</h2>";
        echo "<FORM ACTION='install.php' METHOD='post'>";
        if ($test_write == 'no') {
            echo "<h3 class='gepi'>Problème de droits d'accès :</h3";
            echo "<p>Le répertoire \"/secure\" n'est pas accessible en écriture.</p>";
            echo "<P>Utilisez votre client FTP afin de régler ce problème ou bien contactez l'administrateur technique. Une fois cette manipulation effectuée, vous pourrez continuer en cliquant sur le bouton en bas de la page.</p>";
            echo "<INPUT TYPE='hidden' NAME='etape' VALUE='' />";
        } else {
            echo "<INPUT TYPE='hidden' NAME='etape' VALUE='1' />";
        }
        if ($file_existe == 'yes') {
            echo "<h3 class='gepi'>Présence d'un fichier ".$nom_fic." :</h3>";
            echo "<p>Un fichier nommé <b>\"connect.inc.php\"</b> est actuellement présent dans le répertoire \"/secure\".
            C'est peut-être la trace d'une ancienne installation. Par ailleurs, ce fichier contient peut-être les informations de connexion à la base MySql que vous souhaitez conserver.
            <br /><b>Attention : ce fichier et ce qu'il contient sera supprimé lors de cette nouvelle installation</b>.</p>";
        }


        echo "<INPUT TYPE='submit' CLASS='fondl' Value = 'Continuer' NAME='Continuer' />";
        echo "</FORM>";
        end_html();
    } else {
        header("Location: ./install.php?etape=1");
    }
}
?>