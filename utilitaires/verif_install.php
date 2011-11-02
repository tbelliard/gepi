<?php
/*
 * $Id: verif_install.php 7854 2011-08-21 12:33:55Z jjocal $
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

if (file_exists("./secure/connect.inc.php")) {
    require_once("./secure/connect.inc.php");
    $correct_install = 'yes';
    $maj = 'no';
    if (@mysql_connect("$dbHost", "$dbUser", "$dbPass")) {
        if (@mysql_select_db("$dbDb")) {
            require_once("./lib/global.inc.php");
            // Premier test
            $liste2 = array();
            
            $tableNames = mysql_query("SHOW TABLES FROM `$dbDb`");
            while ($row = mysql_fetch_row($tableNames)) {
                $liste2[] = $row[0];
            }
            
            $flag = 'no';
            /*
            $j = '0';
            while ($j < count($liste_tables)) {
                $temp = $liste_tables[$j];
                if (!(in_array($temp, $liste2))) {
                    $correct_install='no';
                    $flag = 'yes';
                }
                $j++;
            }
            */
            if ($flag == 'yes') {
                $msg = "<p>La connexion au serveur Mysql est établie mais certaines tables sont absentes de la base $dbDb.</p>";
                $correct_install = 'no';
                $maj = 'yes';
            } else {
                //test sur le contenu des tables
                $sql="SELECT * FROM utilisateurs;";
                $req = mysql_query($sql);
                $test = mysql_num_rows($req);
                if ($test == '0') {
                    //$msg = "<p>Il n'y a aucun utilisateur créé !</p>";
                    $msg = "<p>Aucun utilisateur n'existe !</p>";
                    $correct_install = 'no';
                }

            }
        } else {
            $msg = "<p>La connexion au serveur Mysql est établie mais impossible de sélectionner la base contenant les tables GEPI.</p>";
            $correct_install = 'no';
        }
    } else {
        $msg = "<p>Erreur de connexion au serveur Mysql. Le fichier \"connect.inc.php\" ne contient peut-être pas les bonnes informations de connexion.</p>";
        $correct_install = 'no';
    }
} else {
    $msg = "<p>Le fichier \"connect.inc.php\" contenant les informations de connexion est introuvable dans le répertoire /secure.</p>";
    if (file_exists("./secure/connect.inc")) {
        $msg .= "<p>Un fichier \"connect.inc\" est présent dans le répertoire. Renommez ce fichier sous le nom \"connect.inc.php\" puis rechargez cette page.</p>";
        $maj = '';
    } else {
        $maj = 'no';
    }
    $correct_install = 'no';
}

if ($correct_install=='no') {
    ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
    <html>
    <head>
    <title>GEPI</title>
    <meta HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1" />
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
    <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
    <META HTTP-EQUIV="Expires" CONTENT="0" />
    <LINK REL="stylesheet" href="style.css" type="text/css" />
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/ico" href="../favicon.ico" />
    </head>
    <body>
    <?php
    echo "<h1 class='gepi'>GEPI</h1>";
    echo $msg;
    if ($maj == 'no') {
        echo "<p>L'installation de GEPI n'est peut-être pas terminée.</p>";
        echo "<center><a href='./utilitaires/install.php'>Installer la base Mysql</a></center>";
    } else if ($maj == 'yes') {
        echo "<p>Il s'agit sans doute d'une mise à jour vers une nouvelle version de GEPI. Dans ce cas, vous devez procéder à une mise à jour de la base de données MySql.</p>";
        echo "<center><b><a href='./utilitaires/maj.php'>Mettre à jour la base Mysql</a></b></center>";
        echo "<hr />";
        echo "<p>Sinon, l'installation de GEPI n'est peut-être pas terminée. Vous pouvez procéder à une installation/réinstallation de la base.</p>";
        echo "<center><a href='./utilitaires/install.php'>Installer/Réinstaller la base Mysql</a></center>";
    }
    ?>
    </body>
    </html>
    <?php
    die();
}
?>
