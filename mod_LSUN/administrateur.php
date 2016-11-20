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

//===== Mettre à jour les responsables
$metJourResp = filter_input(INPUT_POST, 'MetJourResp');
if ($metJourResp == 'y') {
	MetAJourResp();
}


//===== Création du fichier =====
$creeFichier = filter_input(INPUT_POST, 'creeFichier');


if ($creeFichier == 'y') {
	include_once 'creeFichier.php';
}


//===== Suppression ou modification des AP =====
$supprimerAp = filter_input(INPUT_POST, 'supprimerAp');
$modifierAp = filter_input(INPUT_POST, 'modifierAp');

if ($supprimerAp) {
	delAP($supprimerAp);
}

if ($modifierAp) {
	$changeIntituleAp = filter_input(INPUT_POST, 'intituleAp', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$changeApDescription = filter_input(INPUT_POST, 'ApDescription', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$changeLiaisonApAid = filter_input(INPUT_POST, 'liaisonApAid', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$changeApDisciplines = filter_input(INPUT_POST, 'ApDisciplines'.$modifierAp, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	
	modifieAP($modifierAp, $changeIntituleAp[$modifierAp], $changeApDescription[$modifierAp], $changeLiaisonApAid[$modifierAp], $changeApDisciplines);
}



//===== On récupère les données =====
$scolarites = getUtilisateurSurStatut('scolarite');
$cpes = getUtilisateurSurStatut('cpe');
$Enseignants = getUtilisateurSurStatut('professeur');
$responsables = getResponsables();
$parcoursCommuns = getParcoursCommuns();
$listeMatieres = getMatiereLSUN();

$listeEPICommun = getEPICommun();

$listeAPCommun = getAPCommun();
//var_dump($listeAPCommun);
$listeAp = getApCommun();
$listeAidAp = getApAid();



//===== on charge les nomenclatures de LSUN =====
if (file_exists('LSUN_nomenclatures.xml')) {
    $xml = simplexml_load_file('LSUN_nomenclatures.xml');
} else {
    exit('Echec lors de l\'ouverture du fichier LSUN_nomenclatures.xml.');
}


//===== on charge les périodes =====
$periodes = getPeriodes();
$classes = getClasses();


?>

<form action="index.php" method="post" id="responsables">
	<fieldset>
		<legend title="Données saisies dans les paramètres des classes" >Responsables de l'établissement</legend>
		<ul>
<?php while ($responsable = $responsables->fetch_object()){ ?>
		<li> 
			<?php echo $responsable->suivi_par; ?>
		</li>
 
<?php }  ?>
		</ul>
		<p class="center">
			<button type="submit" name="MetJourResp" id="MetJourResp" value="y" >
				Mettre à jour
			</button>
		</p>
		
  </fieldset>
</form>


<form action="index.php" method="post" id="selectionClasse">
	<fieldset>
		<legend>Classes à exporter</legend>
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
						   <?php if(count($selectionClasse) && in_array($afficheClasse->id, $selectionClasse)){echo 'checked';} ?>
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
		<legend>Parcours communs</legend>
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
						<option value="<?php echo $parcours['code'] ?>" <?php if($parcours['code'] == $parcoursCommun->codeParcours){echo " selected ";} ?> >
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
		<legend>EPIs</legend>		
		<div id="div_epi">
			<p>Enseignements Pratiques Interdisciplinaires</p>
<?php while ($epiCommun = $listeEPICommun->fetch_object()) { 
	$tableauMatieresEPI = array();
	$listeMatieresEPI=getMatieresEPICommun($epiCommun->id);
	while ($matiereEPI = $listeMatieresEPI->fetch_object()) {
		$tableauMatieresEPI[] = array('matiere'=>$matiereEPI->id_matiere, 'modalite'=>$matiereEPI->modalite);
	}
?>
					<div class="lsun_cadre">
				<div>Période de fin :
						<input type="hidden" 
							   name="modifieEpiId[<?php echo $epiCommun->id; ?>]" 
							   value="<?php echo $epiCommun->id; ?>" />
						<input type="hidden" 
							   name="modifieEpiPeriode1[<?php echo $epiCommun->id; ?>]" 
							   value="<?php echo $epiCommun->periode; ?>" />
						<?php //echo $epiCommun->periode; ?>
						
						
						<select name="modifieEpiPeriode[<?php echo $epiCommun->id; ?>]">
							<option value=""></option>
	<?php $periodes->data_seek(0);
	while ($periode = $periodes->fetch_object()) { ?>
							<option value="<?php echo $periode->num_periode; ?>"
									<?php if ($periode->num_periode == $epiCommun->periode) {echo " selected ";} ?> >
								<?php echo $periode->num_periode; ?>
							</option>
	<?php } ?>
						</select>
						
						-
						Division :
						
						<select name="modifieEpiClasse<?php echo $epiCommun->id; ?>[]" multiple >
							<option value=""></option>
<?php $classes->data_seek(0);
while ($classe = $classes->fetch_object()) { ?>
							<option value="<?php echo $classe->id; ?>"
									<?php if (estClasseEPI($epiCommun->id,$classe->id)) {echo " selected "; } ?> >
								<?php echo $classe->classe; ?> <?php echo $classe->nom_complet; ?>
							</option>
<?php } ?>
						</select>
						-
						Thématique :
						<select name="modifieEpiCode[<?php echo $epiCommun->id; ?>]">
<?php foreach ($xml->{'thematiques-epis'}->{'thematique-epi'} as $thematiqueEpi) { ?>
							<option value="<?php echo $thematiqueEpi['code'] ?>" 
								<?php if($thematiqueEpi['code'] == $epiCommun->codeEPI){echo " selected ";} ?>
									title="<?php echo $thematiqueEpi['libelle']; ?>" >
								<?php //echo substr($epi['libelle'],0,40); ?>
								<?php echo substr($thematiqueEpi['libelle'],0,40); ?>
							</option>
<?php } ?>
						</select>
						-
						Intitulé :
						<input type="text" size="40" name="modifieEpiIntitule[<?php echo $epiCommun->id; ?>]" value="<?php echo $epiCommun->intituleEpi; ?>" />	
				</div>
				<div>
						Disciplines :
<?php	foreach ($tableauMatieresEPI as $matiereEPI) {
	echo getMatiereOnMatiere($matiereEPI['matiere'])->nom_complet;
	if ($matiereEPI['modalite'] =="O") { echo " option obligatoire"; } elseif ($matiereEPI['modalite'] =="F") {echo " option facultative";}
	echo " - ";
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
						-
						Description :
						<textarea rows="6" cols="50" name="modifieEpiDescription[<?php echo $epiCommun->id; ?>]" /><?php echo $epiCommun->descriptionEpi; ?></textarea> 
				</div>
						<div>
						Liaison :
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
						
<?php } 
?>	
						
<?php
$tableauClasses = array();

if (count($_SESSION['afficheClasse'])) {
	foreach ($_SESSION['afficheClasse'] as $classeSelectionne) {
		$tableauClasses[]=$classeSelectionne;
	}
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
//$listeCours = getEpiCours();
$listeCours = getEpiAid();

$lastCours = NULL;
while ($cours = $listeCours->fetch_object()) {
//var_dump($cours);
	
	if($cours->id_groupe == $lastCours) {
		echo ' - '.$cours->id_classe;
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
				</div>
				<div>
						Action :
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
				</div>
				</div>
<?php } ?>
			
			
			<div class="lsun_cadre">
				<div>
					<p>
						Période de fin :
						<select name="newEpiPeriode">
							<option value=""></option>
	<?php $periodes->data_seek(0);
	while ($periode = $periodes->fetch_object()) { ?>
							<option value="<?php echo $periode->num_periode; ?>"><?php echo $periode->num_periode; ?></option>
	<?php } ?>
						</select>
						
						Division :
						<select name="newEpiClasse[]" multiple >
							<option value=""></option>
<?php $classes->data_seek(0);
while ($classe = $classes->fetch_object()) { ?>
							<option value="<?php echo $classe->id; ?>">
								<?php echo $classe->classe; ?> <?php echo $classe->nom_complet; ?>
							</option>
<?php } ?>
						</select>
						
						Thématique :
						<select name="newEpiCode">
							<option value=""></option>
<?php foreach ($xml->{'thematiques-epis'}->{'thematique-epi'} as $epi) { ?>
							<option value="<?php echo $epi['code']; ?>" title="<?php echo $epi['libelle']; ?>" >
								<?php echo substr($epi['libelle'],0,40); ?>
							</option>
<?php } ?>
						</select>
						Intitule :
						<input type="text" name="newEpiIntitule" size="40" />
					</p>
					<p>
						Disciplines :
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
						
						Description :
						<textarea rows="4" cols="50" name="newEpiDescription" /></textarea> 
					</p>
				<div>
					<p>
						Action
						<input type="submit" class="btnValide" 
							   alt="Submit button" 
							   name="ajouteEPI" 
							   value="y"
							   title="Ajouter cet EPI" />
					</p>
				</div>
			</div>
			
		</div> 
	
		</div> 
	</fieldset>
</form>



<form action="index.php" method="post" id="definitionAP">
	<fieldset>
		<legend>AP</legend>		
		<div id="div_ap">
			<p>Accompagnements personnalisés</p>
			
<?php
$listeAPCommun->data_seek(0);
$cpt2 = 0;
while ($ap = $listeAPCommun->fetch_object()) { ?>
			
			<div class="lsun_cadre">
				<!-- AP <?php //echo $ap->id; ?> -->
				Intitulé : 
				<input type="text" name="intituleAp[<?php echo $ap->id; ?>]" value="<?php echo $ap->intituleAP; ?>" />
				-
				
				Description : 
				<textarea rows="4" cols="50" id="ApDescription<?php echo $ap->id; ?>" name="ApDescription[<?php echo  $ap->id; ?>]" /><?php echo $ap->descriptionAP; ?></textarea> 				
				-
				
				Liaison <?php echo getAidConfig($ap->id_aid)->fetch_object()->nom ; ?>
<?php $listeAidAp->data_seek(0); ?>
				<select name="liaisonApAid[<?php echo $ap->id; ?>]">
<?php 
//var_dump($listeAidAp);
$listeAidAp->data_seek(0);
while ($liaison = $listeAidAp->fetch_object()) { ?>
					<option value="<?php echo $liaison->indice_aid; ?>" 
							 <?php if($liaison->indice_aid == $ap->id_aid) {echo 'selected'; } ?> >
						<?php echo $liaison->groupe; ?>
					</option>
<?php } ?>
				</select>								
<?php $listeAidAp->data_seek(0); ?>	
				
				<br />
				
				<label for="ApDisciplines<?php echo  $ap->id; ?>">
					Discipline(s) de référence
<?php $listeMatiereAP = disciplineAP($ap->id);
	$tableauMatiere=array();
while ($matiereAP = $listeMatiereAP->fetch_object()) { ?>
					<?php //echo $matiereAP->id_enseignements.' '.$matiereAP->modalite ?> <?php echo getMatiereSurMEF($matiereAP->id_enseignements)->fetch_object()->nom_complet ?>
<?php 	

$tableauMatiere[] = $matiereAP->id_enseignements.$matiereAP->modalite;
	if ($matiereAP->modalite == 'O') {
		echo '- option obligatoire';
	} elseif ($matiereAP->modalite == 'F') {
		echo '- option facultative';
	}
} ?>	
				</label>
				<select multiple name="ApDisciplines<?php echo $ap->id; ?>[]">
<?php $listeMatieres->data_seek(0);
while ($matiere = $listeMatieres->fetch_object()) { ?>
					<option value="<?php echo $matiere->matiere.$matiere->code_modalite_elect; ?>"
							<?php if (in_array($matiere->code_matiere.$matiere->code_modalite_elect, $tableauMatiere)) { echo ' selected ';} ?> >
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
				
				<p>
					<button name="modifierAp" value="<?php echo  $ap->id; ?>" id="modifierAp_<?php echo  $ap->id; ?>" title="Enregistrer les modifications pour cet Accompagnement Personnalisé" >Modifier</button>
					<button name="supprimerAp" value="<?php echo  $ap->id; ?>" id="supprimeAp_<?php echo  $ap->id; ?>" title="Supprimer cet Accompagnement Personnalisé" >Supprimer</button>
				</p>
				
			</div>
<?php 
	$cpt2 ++;
}  ?>	
			
			<div class="lsun_cadre">
				<div>
					<p>
						<label for="newApIntituleAP">intitulé :</label>
						<input type="text" id="newApIntituleAP" name="newApIntituleAP" maxlength="150" />
						-
						<label for="newApDescription">Description :</label>
						<textarea rows="4" cols="50" id="newApDescription" name="newApDescription" /></textarea> 
						-
						<label for="newApDisciplines">Discipline(s) de référence</label>
						<select multiple name="newApDisciplines[]">
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
						-
						<label for="newApLiaisonAID">Liaison</label>
						<select name="newApLiaisonAID">
							<option>
							</option>
<?php 
//var_dump($listeAidAp);
$listeAidAp->data_seek(0);
while ($liaison = $listeAidAp->fetch_object()) { ?>
							<option value="<?php echo $liaison->indice_aid; ?>">
								<?php echo $liaison->groupe; ?>
							</option>
<?php } ?>
						</select>
					</p>
					<p>
						<button type="submit" name="creeAP" value="y">Créer cet AP</button>
					</p>
					
				</div>
			</div>
			
		</div> 
	</fieldset>
</form>



<form action="index.php" method="post" id="definitionAP">
	<fieldset>
		<legend>Export des données</legend>		
			<p class="lsun_cadre" >
				<a href="lib/creeXML.php" target="exportLSUN.xml">Afficher l'export</a>
			</p>
			<p class="center">
				<button type="submit" name="creeFichier" value="y">Créer le fichier</button>
			</p>
	</fieldset>
</form>

