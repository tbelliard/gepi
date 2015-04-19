		
<?php echo add_token_field(); ?>

<?php $handle=opendir('./bases'); ?>

<select name="csv_file_<?php echo $INE->id_etablissement; ?>" 
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
	   id="recherche_<?php echo $INE->id_etablissement; ?>_<?php echo $file; ?>"
	   title="Rechercher l'établissement dans le fichier .csv choisi"
	   value="<?php echo $INE->id_etablissement; ?>"/>
	Rechercher
</button>
<?php unset($files_tab); ?>


