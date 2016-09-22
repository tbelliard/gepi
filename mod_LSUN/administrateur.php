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
						<th>code du parcours éducatifs</th>
						<th>Description</th>
						<th>Action</th>
					</tr>
				<thead>
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
<?php } ?>				</select>
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


<p class="lsun_cadre" >
	   <a href="lib/creeXML.php" target="exportLSUN.xml">Afficher l'export</a>
</p>

