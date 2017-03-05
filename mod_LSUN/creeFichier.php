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

$nomFichier = "LSU_".date("d-m-Y_H:i").".xml";

$dirTemp = "../temp/";
$dirTemp .= get_user_temp_directory()."/";


$file = $dirTemp.$nomFichier;

$xml->save($file);


$schema = "xsd/import-bilan-complet.xsd";

// active la gestion d'erreur personnalisée
libxml_use_internal_errors(true);
	?>
<div class="lsun_cadre">
<?php
// Validation du document XML

// Affichage du résultat
if((isset($msg_erreur_remplissage))&&($msg_erreur_remplissage!="")) {
	echo "<p class='rouge center gras' style='margin-bottom:1em;'>".$msg_erreur_remplissage."</p>";
}

if((isset($msgErreur))&&($msgErreur!="")) {
	echo "<p class='rouge center gras' style='margin-bottom:1em;'>".$msgErreur."</p>";
}

// A désactiver peut-être pour ne pas faire peur inutilement?
$afficher_liste_absence_EP=false;
if (isset($absenceEP))  {
	echo "<p class='rouge center gras' style='margin-bottom:1em;'>Des élèves n'ont pas d'éléments de programme dans un <em>(ou plusieurs)</em> enseignement(s), vous devez vous assurer que c'est normal <em>(mais ce n'est pas bloquant)</em>.</p>";
	if($afficher_liste_absence_EP) {
		if(isset($liste_absenceEP)) {
			echo $liste_absenceEP;
		}
	}
}

// Activer "user error handling"
//libxml_use_internal_errors(true);

$dom = new DOMDocument("1.0");
$dom->Load($file);

$validate = $dom->schemaValidate($schema);
if ($validate) {
    echo "<p class='vert'>Le fichier $nomFichier semble valide !</p>\n";
} else {
	?>
<p class ="rouge">Le fichier <?php echo $nomFichier; ?> n'est pas valide, vous devez le vérifier et corriger les erreurs.</p>
<?php echo "<p class='rouge'>".libxml_display_errors()."</p>"; ?>
<p>Vous pouvez récupérer le schéma du fichier pour votre validateur en <a href="<?php echo $schema; ?>" target="_BLANK">cliquant ici</a></p>
<?php
}

unset($xml);

?>


<p>
	<a class="bold"  href='../temp/<?php echo $dirTemp ; ?><?php echo $nomFichier; ?>' target='_blank'>
		Récupérer le fichier XML
	</a>
	(<em>effectuer un clic-droit → enregistrer la cible </em>)
</p>
<p class='rouge'>Vous pouvez vérifier votre fichier sur <a href="http://www.xmlvalidation.com/index.php" target="_blank">xmlvalidation.com</a></p>

</div>

