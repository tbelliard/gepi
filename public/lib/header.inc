<?php
/*
 * Last modification  : 04/04/2005
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
<meta HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
<META HTTP-EQUIV="Expires" CONTENT="0" />
<title><?php echo getSettingValue("gepiSchoolName");
if ($id_classe != -1) echo " - $classe_nom";
if (isset($id_groupe)) echo "- $matiere_nom ";
?></title>
<link rel="stylesheet" type="text/css" href="<?php echo($gepiPath); ?>/style.css" />
<script src="../lib/functions.js" type="text/javascript" language="javascript"></script>
<LINK REL="SHORTCUT ICON" type="image/x-icon" href="./favicon.ico" /></head>
<body>
<?php if (isset($titre_page)) {?>
  <!-- Header start -->
  <div class="header">
  <!-- Page title, access rights -->
    <div class="headerLeft">
    <?php
    echo("<h3>" . $titre_page . "</h3>");
    ?>
    </div>

    <!-- User name, status, main matter, home, logout, account management -->
    <div class="headerRight" style="font:normal small-caps normal 12pt Tahoma;line-height: 200%;">
    <?php echo getSettingValue("gepiSchoolName"); ?> : module public de consultation

    </div>

    <!-- Contact -->
    <div class="headerBottom">
    <?php
    echo "<a href=\"index.php\">Accueil</a> - ";
    echo "<a href=\"http://gepi.mutualibre.org\">Visiter le site de GEPI</a> - ";
    echo "<a href=\"javascript:centrerpopup('contacter_admin_pub.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Contacter l'administrateur</a>";
    ?>
    </div>
    <hr class="header"/>
  </div>
 <?php } ?>