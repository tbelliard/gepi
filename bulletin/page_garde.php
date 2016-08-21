<?php
/*
 * @version: $Id$
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
// Global configuration file

echo "<style type=\"text/css\">\n";
echo "div.adresse{
    padding-top:".getSettingValue("page_garde_padding_top")."cm;
    padding-bottom:0cm;
    padding-left:".getSettingValue("page_garde_padding_left")."cm;
    text-align:left;

}

div.texte{
    padding-top:".getSettingValue("page_garde_padding_text")."cm;
    padding-left:2cm;
    padding-right:2cm;
    text-align:justify;

}
p {
text-indent : 1.5cm;
}

div.info_eleve{
	float:left;
}
</style>\n";

// Affichage de l'info sur le nom, prénom et classe de l'élève
echo "<div class='info_eleve'>$info_eleve_page_garde</div>\n";

//Affichage du bloc adresse
echo "<div class=adresse>\n";
echo $ligne1."<br />".$ligne2."<br />".$ligne3;
echo "</div>\n";

// Affichage du bloc texte
echo "<div class=texte>\n";
$content = getSettingValue("page_garde_texte");
echo $content;
echo "</div>\n";
echo "<style type=\"text/css\">
    p {
    text-indent : 0cm;
    }
    </style>\n";

?>