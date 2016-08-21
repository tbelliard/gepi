<?php 
/*
 *
 * Copyright 2015 Bouguin Régis
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

echo add_token_field();

$handle=opendir('./bases'); ?>

<select name="csv_file_<?php echo $RNE->id_etablissement; ?>" 
		id="csv_file"
		size="1" 
		title="Choisissez le fichier d'établissements où chercher" >
	<option>choisissez un fichier</option>
<?php
$file_tab = array();
while ($file = readdir($handle)) {
	if (($file != '.') and ($file != '..'))
// On met le fichier dans un tableau, histoire de pouvoir classer tout ça
		$files_tab[] = $file;
}
sort($files_tab);
foreach ($files_tab as $file) { ?>
	<option><?php echo $file; ?></option>
<?php } ?>
</select>
<?php closedir($handle); ?>
<button 
	   name='recherche'
	   id="recherche_<?php echo $RNE->id_etablissement; ?>_<?php echo $file; ?>"
	   title="Rechercher l'établissement dans le fichier .csv choisi"
	   value="<?php echo $RNE->id_etablissement; ?>"/>
	Rechercher
</button>
<?php unset($files_tab); ?>


