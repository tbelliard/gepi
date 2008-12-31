<?php
/*
 * @version: $Id: liste_tous_devoirs.php 1360 2008-01-13 20:03:09Z jjocal $
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


// Initialisations files
require_once("../lib/initialisations.inc.php");
require_once("../lib/transform_functions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
    <head>
        <title>Liste de tous les devoirs</title>
        <link rel="stylesheet" type="text/css" href="../style.css"/>
        <style type="text/css">
            body {
                margin: none;
                padding: none;
            }

            table.devoir {
                background-color: <?php echo $color_fond_notices['t'];?>;
                border: 1px solid black;
                width: 100%;
                margin-left: auto;
                margin-right: auto;
                margin-top: 8px;
                margin-bottom: 8px;
            }

            table.devoir td {
                padding: 5px;
            }
        </style>
    </head>
    <body>
<?php
//On vérifie si le module est activé

if (getSettingValue("active_cahiers_texte")!='y') {
    die("Le module n'est pas activé.");
}

if (!isset($_GET['debut']) || !isset($_GET['classe']) || !intval($_GET['debut']) || !intval($_GET['classe'])) {
    die("<p><em>Paramètres invalides !</em></p>\n</body>\n</html>");
}

$groups = get_groups_for_class($_GET['classe']);
?>
        <div style="width: 240px; margin: auto;">

<?php


foreach ($groups as $group) {

$req_devoirs =
    "select d.id_ct, d.id_groupe, d.contenu, d.date_ct, m.nom_complet, m.matiere
    from ct_devoirs_entry d, matieres m, j_groupes_matieres j
    where m.matiere = j.id_matiere
    and j.id_groupe = " . $group["id"] . "
    and d.contenu != ''
    and d.id_groupe = " . $group["id"] . "
    and d.date_ct > " . $_GET['debut'] . "
    order by d.date_ct desc";
$res_devoirs = mysql_query($req_devoirs);
if (!$res_devoirs) echo mysql_error($res_devoirs);
    if (mysql_num_rows($res_devoirs) > 0) {
        while ($devoir = mysql_fetch_object($res_devoirs)) {
            $content = &$devoir->contenu;
            include ("../lib/transform.php");
        ?>
                    <table class="devoir">
                        <thead>
                            <tr>
                                <th><?php echo($devoir->matiere); ?></th>
                                <th><?php echo(strftime("%a %d %b %y", $devoir->date_ct)); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2">
        <?php echo($html); ?>
        <?php
            // fichier joint
            $architecture="cl_dev".$devoir->id_groupe;
                $req_docs = "SELECT titre, emplacement FROM ct_documents WHERE id_ct = $devoir->id_ct AND emplacement LIKE '%".$architecture."%' ORDER BY titre";
            $res1 = sql_query($req_docs);
            if (($res1) and (sql_count($res1)!=0)) {
                $html_dos = "<small style=\"font-weight: bold;\">Document(s) joint(s):</small>";
                    $html_dos .= "<ul type=\"disc\" style=\"padding-left: 15px;\">";
                    $res_docs = mysql_query($req_docs);
                    while ($doc = mysql_fetch_object($res_docs)) {
                        $html_dos .= "<li style=\"padding: 0px; margin: 0px; \"><a href=\"$doc->emplacement\">$doc->titre</a></li>";
                    }
                $html_dos .= "</ul>";
                echo $html_dos;
            }
            $html_dos = '';
        ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
        <?php
        }
    }
}
 ?>
        </div>
    </body>
</html>