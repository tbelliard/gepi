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

$selectionClasse = $_SESSION['afficheClasse'];

//===== On récupère les données =====
$scolarites = getUtilisateurSurStatut('scolarite');
$cpes = getUtilisateurSurStatut('cpe');
$Enseignants = getUtilisateurSurStatut('professeur');
$responsables = getResponsables();
$parcoursCommuns = getParcoursCommuns();
$listeMatieres = getMatiereLSUN();
$listeEPICommun = getEPICommun();



//===== on charge les nomenclatures de LSUN =====
if (file_exists('LSUN_nomenclatures.xml')) {
    $xml = simplexml_load_file('LSUN_nomenclatures.xml');
} else {
    exit('Echec lors de l\'ouverture du fichier LSUN_nomenclatures.xml.');
}


//===== on charge les périodes =====
$periodes = getPeriodes();
$classes = getClasses();

/*
$sqlDisciplines = "SELECT DISTINCT m.matiere , m.nom_complet , m.code_matiere , mm.code_modalite_elect FROM mef_matieres AS mm "
	. "INNER JOIN matieres AS m ON m.code_matiere = mm.code_matiere ";

echo $sqlDisciplines;
*/

//===== On récupère les matières enseignées =====


?>

<form action="index.php" method="post" id="responsables">
	<fieldset>
		<legend>Liste des responsables de l'établissement</legend>
<?php while ($responsable = $responsables->fetch_object()){ ?>
		<p>
			<input type="submit" class="btnEfface" 
				   alt="Submit button" 
				   name="supprimeResponsable"  
				   value="<?php echo $responsable->login ?>"
				   title="Supprimer <?php echo $responsable->nom; ?> <?php echo $responsable->prenom; ?>" /> - 
			<?php echo $responsable->civilite; ?> <?php echo $responsable->nom; ?> <?php echo $responsable->prenom; ?>
		</p>
 
<?php } ?>
		
		<select name="responsableAdmin">
			<option value="">Ajouter un compte scolarité</option>
<?php while ($scolarite = $scolarites->fetch_object()){ ?>
			<option value="<?php echo $scolarite->login; ?>"><?php echo $scolarite->nom; ?> <?php echo $scolarite->prenom; ?></option>
<?php } ?>
		</select>
		
		<select name="responsableCPE">
			<option value="">Ajouter un compte CPE</option>
<?php while ($cpe = $cpes->fetch_object()){ ?>
			<option value="<?php echo $cpe->login; ?>"><?php echo $cpe->nom; ?> <?php echo $cpe->prenom; ?></option>
<?php } ?>
		</select>
		
		<select name="responsableEnseignant">
			<option value="">Ajouter un compte enseignant</option>
<?php while ($Enseignant = $Enseignants->fetch_object()){ ?>
			<option value="<?php echo $Enseignant->login; ?>"><?php echo $Enseignant->nom; ?> <?php echo $Enseignant->prenom; ?></option>
<?php } ?>
		</select>
		
		<p class="center">
			<button type="submit" id="soumetResponsable" >
				Ajouter
			</button>
		</p>
		
  </fieldset>
</form>


<form action="index.php" method="post" id="selectionClasse">
	<fieldset>
		<legend>Classes</legend>
		<div class="lsun3colonnes" >
<?php 
$toutesClasses = getClasses();
$cpt = 0;
$coupe = ceil($toutesClasses->num_rows/4);
while ($afficheClasse = $toutesClasses->fetch_object()) {
	if (!$cpt) {echo "			<div class='colonne'>\n";}
?>
				<p>
					<input type="checkbox" 
						   name="afficheClasse[<?php echo $afficheClasse->id; ?>]"
						   <?php if(in_array($afficheClasse->id, $selectionClasse)){echo 'checked';} ?>
						   />
						<?php echo $afficheClasse->classe; ?>
				</p>
<?php 
	$cpt=$cpt+1;
	if ($cpt > $coupe) {echo "			</div>\n"; $cpt = 0;}
}
if ($cpt) {echo "			</div>\n";}
?>
		</div>
		<p class="center">
			<button type="submit" name="soumetSelection" value="y" >
				Sélectionner
			</button>
		</p>


  </fieldset>
</form>

<form action="index.php" method="post" id="parcours">
	<fieldset>
		<legend>Liste des parcours communs</legend>
		<table>
			<caption style="caption-side:bottom">parcours éducatifs communs à une classe pour une période</caption>
			<thead>
				<tr>
					<th>Période</th>
					<th>Division</th>
					<th>Type de parcours éducatifs</th>
					<th>Description</th>
					<th>Action</th>
				</tr>
			</thead>
<?php while ($parcoursCommun = $parcoursCommuns->fetch_object()) { ?>
			<tr>
				<td>
					<input type="hidden" name="modifieParcoursId[<?php echo $parcoursCommun->id; ?>]" value="<?php echo $parcoursCommun->id; ?>" />
					<input type="hidden" name="modifieParcoursPeriode[<?php echo $parcoursCommun->id; ?>]" value="<?php echo $parcoursCommun->periode; ?>" />
					<?php echo $parcoursCommun->periode; ?>
				</td>
				<td>
					<input type="hidden" name="modifieParcoursClasse[<?php echo $parcoursCommun->id; ?>]" value="<?php echo $parcoursCommun->classe; ?>" />
					<?php echo getClasses($parcoursCommun->classe)->fetch_object()->nom_complet; ?>
				</td>
				<td>
					<select name="modifieParcoursCode[<?php echo $parcoursCommun->id; ?>]">
<?php foreach ($xml->{'liste-parcours'}->parcours as $parcours) { ?>
						<option value="<?php echo $parcours['code'] ?>" <?php if($parcours['code'] == $parcoursCommun->codeParcours){echo "selected='selected'";} ?> >
							<?php echo $parcours['libelle'] ?>
						</option>
<?php } ?>
					</select>
				</td>
				<td>
					<input type="text" name="modifieParcoursTexte[<?php echo $parcoursCommun->id; ?>]" size="80" value="<?php echo $parcoursCommun->description; ?>"/>
				</td>
				<td>
					<input type="submit" class="btnSupprime" 
						   alt="Boutton supprimer" 
						   name="supprimeParcours[<?php echo $parcoursCommun->id; ?>]" 
						   value="y"
						   title="Supprimer ce parcours" />
					/
					<input type="submit" class="btnValide" 
						   alt="Submit button" 
						   name="modifieParcours" 
						   value="<?php echo $parcoursCommun->id; ?>"
						   title="Modifier ce parcours" />
				</td>
			</tr>
<?php } ?>
				
			<tr>
				<td>
					<select name="newParcoursPeriode">
						<option value=""></option>
<?php while ($periode = $periodes->fetch_object()) { ?>
						<option value="<?php echo $periode->num_periode; ?>"><?php echo $periode->num_periode; ?></option>
<?php } ?>
					</select>
				</td>
				<td>
					<select name="newParcoursClasse">
						<option value=""></option>
<?php while ($classe = $classes->fetch_object()) { ?>
					<option value="<?php echo $classe->id; ?>"><?php echo $classe->classe; ?> <?php echo $classe->nom_complet; ?></option>
<?php } ?>
					</select>
				</td>
				<td>
					<select name="newParcoursCode">
						<option value=""></option>
<?php foreach ($xml->{'liste-parcours'}->parcours as $parcours) { ?>
						<option value="<?php echo $parcours['code'] ?>"><?php echo $parcours['libelle'] ?></option>
<?php } ?>
					</select>
				</td>
				<td>
					<input type="text" name="newParcoursTexte" size="80" />
				</td>
				<td>
					<input type="submit" class="btnValide" 
						   alt="Submit button" 
						   name="ajouteParcours" 
						   value="y"
						   title="Ajouter ce parcours" />
				</td>
			</tr>
		</table> 
	</fieldset>
</form>


<form action="index.php" method="post" id="definitionEPI">
	<fieldset>
		<legend>Liste des EPIs</legend>		
		<table>
			<caption>Enseignements Pratiques Interdisciplinaires</caption>
			<thead>
				<tr>
					<th>Période</th>
					<th>Division</th>
					<th>Thématique/Intitule</th>
					<th>Disciplines</th>
					<th>Description</th>
					<th>Liaison</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
<?php while ($epiCommun = $listeEPICommun->fetch_object()) { 
	$tableauMatieresEPI = array();
	$listeMatieresEPI=getMatieresEPICommun($epiCommun->id);
	while ($matiereEPI = $listeMatieresEPI->fetch_object()) {
		$tableauMatieresEPI[] = array('matiere'=>$matiereEPI->id_matiere, 'modalite'=>$matiereEPI->modalite);
	}
?>
				<tr>
					<td>
						<input type="hidden" 
							   name="modifieEpiId[<?php echo $epiCommun->id; ?>]" 
							   value="<?php echo $epiCommun->id; ?>" />
						<input type="hidden" 
							   name="modifieEpiPeriode[<?php echo $epiCommun->id; ?>]" 
							   value="<?php echo $epiCommun->periode; ?>" />
						<?php echo $epiCommun->periode; ?>
					</td>
					<td>
						
						<select name="modifieEpiClasse<?php echo $epiCommun->id; ?>[]" multiple >
							<option value=""></option>
<?php $classes->data_seek(0);
while ($classe = $classes->fetch_object()) { ?>
							<option value="<?php echo $classe->id; ?>"
									<?php if (estClasseEPI($epiCommun->id,$classe->id)) {echo "selected = 'selected'"; } ?> >
								<?php echo $classe->classe; ?> <?php echo $classe->nom_complet; ?>
							</option>
<?php } ?>
						</select>
						
					</td>
					<td>
						<select name="modifieEpiCode[<?php echo $epiCommun->id; ?>]">
<?php foreach ($xml->{'thematiques-epis'}->{'thematique-epi'} as $thematiqueEpi) { ?>
							<option value="<?php echo $thematiqueEpi['code'] ?>" 
								<?php if($thematiqueEpi['code'] == $epiCommun->codeEPI){echo "selected='selected'";} ?>
									title="<?php echo $thematiqueEpi['libelle']; ?>" >
								<?php //echo substr($epi['libelle'],0,40); ?>
								<?php echo substr($thematiqueEpi['libelle'],0,40); ?>
							</option>
<?php } ?>
						</select>
						<br />
						<input type="text" size="40" name="modifieEpiIntitule[<?php echo $epiCommun->id; ?>]" value="<?php echo $epiCommun->intituleEpi; ?>" />
					</td>
					<td>
<?php	foreach ($tableauMatieresEPI as $matiereEPI) {
	echo getMatiereOnMatiere($matiereEPI['matiere'])->nom_complet;
	if ($matiereEPI['modalite'] =="O") { echo " option obligatoire"; } elseif ($matiereEPI['modalite'] =="F") {echo " option facultative";}
	echo "<br />";
	}	?>
						<select multiple name="modifieEpiMatiere<?php echo $epiCommun->id; ?>[]">
<?php $listeMatieres->data_seek(0);
while ($matiere = $listeMatieres->fetch_object()) { 
?>
							<option value="<?php echo $matiere->matiere.$matiere->code_modalite_elect; ?>" 
								<?php if(in_array(array('matiere'=>$matiere->matiere,'modalite'=>$matiere->code_modalite_elect), $tableauMatieresEPI)) {echo " selected";} ?> >
								<?php echo $matiere->nom_complet; ?>
								<?php 
if ($matiere->code_modalite_elect == 'O') {
	echo '- option obligatoire';
} elseif ($matiere->code_modalite_elect == 'F') {
	echo '- option facultative';
}

									?>
							</option>
<?php } ?>
						</select>
					</td>
					<td>
						<textarea rows="6" cols="50" name="modifieEpiDescription[<?php echo $epiCommun->id; ?>]" /><?php echo $epiCommun->descriptionEpi; ?></textarea> 
					</td>
					<td>
						<?php // echo estCoursEpi($epiCommun->id ,"aid-10"); ?>
<?php 
	$listeLiaisons = getLiaisonEpiEnseignementByIdEpi($epiCommun->id); 
	while ($liaison = $listeLiaisons->fetch_object()) { ?>
<?php
		if ($liaison->aid) {
			echo "AID - ".getAID($liaison->id_enseignements)->nom;
			
		} else {
			$enseignements = getCoursById($liaison->id_enseignements);
			//var_dump($enseignements);
				// echo '<br>';
				// echo '<br>';
				// echo '<br>';
			$enseignements->data_seek(0);
			$lastClasse = NULL;
			while ($enseignement = $enseignements->fetch_object()) {
				// echo '<br>';
				// echo '<br>';
				// echo '<br>';
			// var_dump($enseignement);
				if ($lastClasse != $enseignement->id_groupe) {
					echo $enseignement->id_matiere." → ";
				} else {
					echo " - ";
				}
				$lastClasse = $enseignement->id_groupe;
				echo $enseignement->classe;
			
				
			}
		}
?>
						<br />
<?php } 
$tableauClasses = array();

foreach ($_SESSION['afficheClasse'] as $classeSelectionne) {
	$tableauClasses[]=$classeSelectionne;
}

?>
						<select multiple  name="modifieEpiLiaison<?php echo $epiCommun->id; ?>[]">
							<option value=""></option>
<?php 
$listeAids = getEpiAid(); 
while ($aid = $listeAids->fetch_object()) {
?>
							<option value="aid-<?php echo $aid->id_enseignement; ?>" <?php 
	if(estCoursEpi($epiCommun->id ,"aid-".$aid->id_enseignement)) {echo 'selected';}
?> >
								aid
								-
								<?php echo $aid->description; ?>
							</option>
<?php } ?>
<?php 
$listeCours = getEpiCours();
$lastCours = NULL;
while ($cours = $listeCours->fetch_object()) {
	if($cours->id_groupe == $lastCours) {
		echo ' - '.$cours->classe;
		 continue;
	} else if ($lastCours) {
		echo '</option>';
	}
	$lastCours = $cours->id_groupe;
?>
							<option value="cours-<?php echo $cours->id_groupe; ?>" <?php
	if(estCoursEpi($epiCommun->id ,"cours-".$cours->id_groupe)) {echo 'selected';}			
									?> >
								cours
								-
								<?php echo $cours->id_matiere; ?> <?php echo $cours->classe; ?>
 <?php } ?>
							</option>
						</select>
						
<?php 
if (isset($cours)) {
	estCoursEpi($epiCommun->id ,"cours-".$cours->id_groupe);
}


?>
					</td>
					<td>
						<input type="submit" class="btnSupprime" 
							   alt="Boutton supprimer" 
							   name="supprimeEpi" 
							   value="<?php echo $epiCommun->id; ?>"
							   title="Supprimer cet EPI" />
						/
						<input type="submit" class="btnValide" 
							   alt="Submit button" 
							   name="modifieEpi" 
							   value="<?php echo $epiCommun->id; ?>"
							   title="Modifier cet EPI" />
					</td>
					
				</tr>
<?php } ?>
				<tr>
					<td>
						<select name="newEpiPeriode">
							<option value=""></option>
	<?php $periodes->data_seek(0);
	while ($periode = $periodes->fetch_object()) { ?>
							<option value="<?php echo $periode->num_periode; ?>"><?php echo $periode->num_periode; ?></option>
	<?php } ?>
						</select>
					</td>
					<td>
						<select name="newEpiClasse[]" multiple >
							<option value=""></option>
<?php $classes->data_seek(0);
while ($classe = $classes->fetch_object()) { ?>
							<option value="<?php echo $classe->id; ?>">
								<?php echo $classe->classe; ?> <?php echo $classe->nom_complet; ?>
							</option>
<?php } ?>
						</select>
					</td>
					<td>
						<select name="newEpiCode">
							<option value=""></option>
<?php foreach ($xml->{'thematiques-epis'}->{'thematique-epi'} as $epi) { ?>
							<option value="<?php echo $epi['code']; ?>" title="<?php echo $epi['libelle']; ?>" >
								<?php echo substr($epi['libelle'],0,40); ?>
							</option>
<?php } ?>
						</select>
						<input type="text" name="newEpiIntitule" size="40" />
					</td>
					<td>
						<select multiple name="newEpiMatiere[]">
<?php $listeMatieres->data_seek(0);
 while ($matiere = $listeMatieres->fetch_object()) { ?>
							<option value="<?php echo $matiere->matiere.$matiere->code_modalite_elect; ?>">
								<?php echo $matiere->nom_complet; ?>
<?php 								
if ($matiere->code_modalite_elect == 'O') {
	echo '- option obligatoire';
} elseif ($matiere->code_modalite_elect == 'F') {
	echo '- option facultative';
}
?>
							</option>
<?php } ?>
						</select>
					</td>
					<td>
						<textarea rows="3" cols="50" name="newEpiDescription" /></textarea> 
					</td>
					<td>
<!-- TODO : Ajouter des boutons qui créent l'EPI et un AID ou l'EPI et un cours -->
						<!--
						<input type="submit" class="btnLien" 
							   alt="Submit button" 
							   name="lieEPI" 
							   value="y"
							   title="Lier cet EPI à un AID ou un enseignement" />
						-->
					</td>
					<td>
						<input type="submit" class="btnValide" 
							   alt="Submit button" 
							   name="ajouteEPI" 
							   value="y"
							   title="Ajouter cet EPI" />
					</td>
			</tbody>
		</table> 
  </fieldset>
</form>

<p class="lsun_cadre" >
	   <a href="lib/creeXML.php" target="exportLSUN.xml">Afficher l'export</a>
</p>

