<?php

/*
*
* Copyright 2016 Régis Bouguin
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
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

include_once 'lib/chargeXML.php';

$nomFichier = "LSUN_".date("d-m-Y_H:i").".xml1";

$dirTemp = "../temp/";
$dirTemp .= get_user_temp_directory()."/";


$file = $dirTemp.$nomFichier;

$xml->save($file);


$schema = "../xsd/import-complet-strict";

// active la gestion d'erreur personnalisée
libxml_use_internal_errors(true);

// Validation du document XML
/*
$validate = $dom->schemaValidate($schema) ?
"<p class='center grand vert'>Le schéma XML paraît valide !</p>" :
"<p class='center grand rouge'>Schéma XML non valide !</p>";
 * 
 */
if (!$xml->schemaValidate($schema)) { ?>
<p class='center grand rouge'>Validation du schema d'export → Votre fichier <?php echo $dirTemp.$nomFichier; ?> n'est pas valide</p>
<?php	    //libxml_display_errors();

	$errors = libxml_get_errors();
	
    foreach ($errors as $error) {
        echo display_xml_error($error);
    }


}


unset($xml);
// Affichage du résultat
