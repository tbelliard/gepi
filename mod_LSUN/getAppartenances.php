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

//$_SESSION['fichier_sts_emp'] = filter_input(INPUT_POST, 'fichier_sts_emp');
// echo $_FILES['fichier_sts_emp']['name'];
$_SESSION['fichier_sts_emp'] = isset($_FILES['fichier_sts_emp']['name']) ? $_FILES['fichier_sts_emp']['name'] : (isset($_SESSION['fichier_sts_emp']) ? $_SESSION['fichier_sts_emp'] : NULL);

$xml = simplexml_load_file($_FILES['fichier_sts_emp']['tmp_name']);

if (filter_input(INPUT_POST, 'corrigeMEF')) {
	// On enregistre les MEF
	$classeBase = filter_input(INPUT_POST, 'classeBase', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	//print_r($classeBase);
	//var_dump($classeBase);
	$nom_completBase = filter_input(INPUT_POST, 'nom_completBase', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$codeMefBase = filter_input(INPUT_POST, 'codeMefBase', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$classeFichier = filter_input(INPUT_POST, 'classeFichier', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$nom_completFichier = filter_input(INPUT_POST, 'nom_completFichier', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	/**
	$codeMefFichier = filter_input(INPUT_POST, 'codeMefFichier', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	
	foreach ($classeBase as $key=>$classeActuelle) {
		//echo $key." ".$classeActuelle." ".$nom_completBase[$key].'<br>';
		$sql = "UPDATE classes SET mef_code = '$codeMefFichier[$key]' WHERE classe = '$classeActuelle' AND nom_complet = '$nom_completBase[$key]'  ";
		//echo $sql.'<br>';
		$mysqli->query($sql);
	}
	 * 
	 */
}

$tbs_CSS_spe[] = array('rel'=>"stylesheet", 'type'=>"text/css", 'fichier'=>"lib/style.css", 'media'=>"screen");
$titre_page = "AP - EPI - parcours";
if (!suivi_ariane($_SERVER['PHP_SELF'],'AP-EPI')) {
	echo "erreur lors de la création du fil d'ariane";
}
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

debug_var();
?>
<form action="index.php" method="post" enctype="multipart/form-data" id="formFichier">
	<p class="center">
		<label for="fichier_sts_emp">Choisissez le fichier sts_emp :</label>
		<input type="file" name="fichier_sts_emp" id="fichier_sts_emp" />
		<button>valider</button>
	</p>
</form>

<form action="index.php" method="post"  id="formCodeMef">
<table>
	<thead>
	<tr>
		<th colspan="3">Base GEPI</th>
		<th colspan="3"><?php echo $_SESSION['fichier_sts_emp']; ?></th>
	</tr>
	<tr>
		<th>classe</th>
		<th>nom complet</th>
		<th>code mef</th>
		<th>classe</th>
		<th>nom complet</th>
		<th>code mef</th>
	</tr>
	</thead>
<?php 
$listeClasse = getClasses();
//$cpt = 0;
while ($classe = $listeClasse->fetch_object()) {
	$result = $xml->xpath("/STS_EDT/DONNEES/STRUCTURE/DIVISIONS/DIVISION[@CODE='".$classe->classe."']");
?>
	<tr>
		<td>
			<input type="hidden" name="classeBase[<?php echo $classe->id; ?>]" value="<?php echo $classe->classe ; ?>" />
			<?php echo $classe->classe ; ?>
		</td>
		<td>
			<input type="hidden" name="nom_completBase[<?php echo $classe->id; ?>]" value="<?php echo $classe->nom_complet ; ?>" />
			<?php echo $classe->nom_complet ; ?>
		</td>
		<td>
			<?php echo $classe->mef_code ; ?>
		</td>
		<td>
			<?php echo $result[0]['CODE'] ; ?>
		</td>
		<td>
			<?php echo $result[0]->LIBELLE_LONG ; ?>
		</td>
		<td>
			<input type="text" name="mefAppartenance[<?php echo $classe->id; ?>]" value="<?php echo $result[0]->MEFS_APPARTENANCE->MEF_APPARTENANCE['CODE'] ; ?>" />
		</td>
	</tr>
<?php 
//$cpt ++;
}

?>
</table>

	<p class="center">
		<button name="corrigeMEF" value="y">Enregistrer les MEF</button>
	</p>
</form>
	
<?php 

//debug_var();
//**************** Pied de page *****************
require_once("../lib/footer.inc.php");
//**************** Fin de pied de page *****************
