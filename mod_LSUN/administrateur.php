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

debug_var();

$selectionClasse = $_SESSION['afficheClasse'];

//===== Mettre à jour les responsables
$metJourResp = filter_input(INPUT_POST, 'MetJourResp');
if ($metJourResp == 'y') {
	MetAJourResp();
}
//===== Choix des données à exporter =====
//===== Création du fichier =====
$creeFichier = filter_input(INPUT_POST, 'creeFichier');

if ($creeFichier == 'y') {
	if(filter_input(INPUT_POST, 'traiteVieSco')) {
		saveSetting('LSU_commentaire_vie_sco', filter_input(INPUT_POST, 'traiteVieSco'));
	}	else {
		saveSetting('LSU_commentaire_vie_sco', "n");
	}
	if(filter_input(INPUT_POST, 'traiteParent')) {
		saveSetting('LSU_Donnees_responsables', filter_input(INPUT_POST, 'traiteParent'));
	}	else {
		saveSetting('LSU_Donnees_responsables',  "n");
	}
	
	if(filter_input(INPUT_POST, 'traiteEPI')) {
		saveSetting('LSU_traite_EPI', filter_input(INPUT_POST, 'traiteEPI'));
	}	else {
		saveSetting('LSU_traite_EPI', "n");
	}
	
	if(filter_input(INPUT_POST, 'traiteEpiElv')) {
		saveSetting('LSU_traite_EPI_Elv', filter_input(INPUT_POST, 'traiteEPIElv'));
	}	else {
		saveSetting('LSU_traite_EPI_Elv', "n");
	}
	
	if(filter_input(INPUT_POST, 'traiteAP')) {
		saveSetting('LSU_traite_AP', filter_input(INPUT_POST, 'traiteAP'));
	}	else {
		saveSetting('LSU_traite_AP', "n");
	}
	
	if(filter_input(INPUT_POST, 'traiteAPElv')) {
		saveSetting('LSU_traite_AP_Elv', filter_input(INPUT_POST, 'traiteAPElv'));
	}	else {
		saveSetting('LSU_traite_AP_Elv', "n");
	}
	
	if (0 == count($selectionClasse)) {
		echo "<p class='rouge center gras'>Vous devez valider la sélection d'au moins une classe</p>";
	}	else if ($creeFichier == 'y') {
		include_once 'creeFichier.php';
	}
		
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
$cptClasse = 0;
$coupe = ceil($toutesClasses->num_rows/4);
while ($afficheClasse = $toutesClasses->fetch_object()) {
	if (!$cpt) {echo "			<div class='colonne'>\n";}
?>
				<p>
					<input type="checkbox" 
						   name="afficheClasse[<?php echo $afficheClasse->id; ?>]"
						   <?php if(count($selectionClasse) && in_array($afficheClasse->id, $selectionClasse)){echo 'checked';} ?>
						   id="afficheClasse_<?php echo $cptClasse; ?>"
						   onchange="checkbox_change(this.id)"
						   />
					<label for="afficheClasse_<?php echo $cptClasse; ?>" id="texte_afficheClasse_<?php echo $cptClasse; ?>">
						<?php echo $afficheClasse->classe; ?>
					</label>
				</p>
<?php 
	$cpt=$cpt+1;
	$cptClasse ++;
	if ($cpt > $coupe) {echo "			</div>\n"; $cpt = 0;}
}
if ($cpt) {echo "			</div>\n";}
?>
		</div>
		
		<p class="center"><a href='#' onClick='CocherClasses(true)'>Tout cocher</a> / <a href='#' onClick='CocherClasses(false)'>Tout décocher</a></p>

<script type='text/javascript'> 
	<?php echo js_checkbox_change_style(); ?>

    function CocherClasses(mode) {
        for (var k=0;k<<?php echo $cptClasse; ?>;k++) {
			//alert('afficheClasse_'+k);
            if(document.getElementById('afficheClasse_'+k)){
                document.getElementById('afficheClasse_'+k).checked = mode;
                checkbox_change('afficheClasse_'+k);
            }
        }
    }

    // Pour re-mettre en gras les classes sélectionnées lors du re-chargement de la page
    for (var k=0;k<<?php echo $cptClasse; ?>;k++) {
        if(document.getElementById('afficheClasse_'+k)){
            checkbox_change('afficheClasse_'+k);
        }
    }
</script>

		<p class="center">
			<button type="submit" name="soumetSelection" value="y" >
				Sélectionner
			</button>
		</p>


  </fieldset>
</form>

<form action="index.php" method="post" id="parcours">
	<fieldset>
		<legend title="Contient l’ensemble des informations relatives aux parcours éducatifs communs à une classe (contrainte d’unicité sur les combinaison de champs 'periodes', 'division' et 'Type de parcours').">
				Parcours communs
	</legend>
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
						<option value="<?php echo $classe->id; ?>"><?php echo $classe->nom_complet; ?></option>
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
						Intitulé&nbsp;:&nbsp;<input type="text" size="40" name="modifieEpiIntitule[<?php echo $epiCommun->id; ?>]" value="<?php echo $epiCommun->intituleEpi; ?>" />	
				</div>
				<div>
					Disciplines&nbsp;:&nbsp;<?php	foreach ($tableauMatieresEPI as $matEPI) {
	echo getMatiereOnMatiere($matEPI['matiere'])->nom_complet;
	if ($matEPI['modalite'] =="O") { echo " option obligatoire"; } 
	elseif ($matEPI['modalite'] =="F") {echo " option facultative";} 
	elseif ($matEPI['modalite'] =="X") {echo " modalité X";}
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
} elseif ($matiere->code_modalite_elect == 'X') {
	echo '- modalité X';
}
									?>
							</option>
<?php } ?>
						</select>
						-
						Description&nbsp;:&nbsp;<textarea rows="6" cols="50" name="modifieEpiDescription[<?php echo $epiCommun->id; ?>]" /><?php echo $epiCommun->descriptionEpi; ?></textarea> 
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
			
		} 
		/*else {
			$enseignements = getCoursById($liaison->id_enseignements);
			$enseignements->data_seek(0);
			$lastClasse = NULL;
			while ($enseignement = $enseignements->fetch_object()) {
				if ($lastClasse != $enseignement->id_groupe) {
					echo $enseignement->id_matiere." → ";
				} else {
					echo " - ";
				}
				$lastClasse = $enseignement->id_groupe;
				echo $enseignement->classe;
			
				
			}
		}
		 * 
		 */
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
						<button type="submit" name="supprimeEpi" value="<?php echo $epiCommun->id; ?>" ><img src='../images/disabled.png' style="width: 16px;" /> Supprimer cet EPI</button>
						<button type="submit" name="modifieEpi" value="<?php echo $epiCommun->id; ?>" ><img src='../images/enabled.png' />Modifier cet EPI</button>
						<button type="submit" name="creeAidEpi" value="<?php echo $epiCommun->id; ?>" disabled hidden><img src='../images/icons/copy-16.png' /> Créer un AID pour cet EPI</button>
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
						<select multiple name="newEpiMatiere[]" size="8">
<?php $listeMatieres->data_seek(0);
 while ($matiere = $listeMatieres->fetch_object()) { ?>
							<option value="<?php echo $matiere->matiere.$matiere->code_modalite_elect; ?>">
								<?php echo $matiere->nom_complet; ?>
<?php 								
if ($matiere->code_modalite_elect == 'O') {
	echo '- option obligatoire';
} elseif ($matiere->code_modalite_elect == 'F') {
	echo '- option facultative';
} elseif ($matiere->code_modalite_elect == 'X') {
	echo '- modalité X';
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
						<button type="submit" name="ajouteEPI" value="y" ><img src='../images/enabled.png' />Ajouter cet EPI</button>
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
					<?php echo getMatiereSurMEF($matiereAP->id_enseignements)->fetch_object()->nom_complet ?>
<?php 	

$tableauMatiere[] = $matiereAP->id_enseignements.$matiereAP->modalite;
	if ($matiereAP->modalite == 'O') {
		echo '- option obligatoire';
	} elseif ($matiereAP->modalite == 'F') {
		echo '- option facultative';
	} elseif ($matiereAP->modalite == 'X') {
		echo '- modalité X';
	}
} ?>
					-
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
} elseif ($matiere->code_modalite_elect == 'X') {
	echo '- modalité X';
}
?>
					</option>
<?php } ?>
				</select>
				
				<p>
					<button type="submit" name="supprimerAp" value="<?php echo  $ap->id; ?>" id="supprimeAp_<?php echo  $ap->id; ?>" title="Supprimer cet Accompagnement Personnalisé" ><img src='../images/disabled.png' style="width: 16px;" /> Supprimer</button>
					<button type="submit" name="modifierAp" value="<?php echo  $ap->id; ?>" id="modifierAp_<?php echo  $ap->id; ?>" title="Enregistrer les modifications pour cet Accompagnement Personnalisé" ><img src='../images/enabled.png' /> Modifier</button>
					<button type="submit" name="creeAidAp" value="<?php echo $ap->id; ?>" disabled hidden ><img src='../images/icons/copy-16.png' /> Créer un AID pour cet AP</button>
					
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
						<select multiple name="newApDisciplines[]" size="8">
<?php $listeMatieres->data_seek(0);
 while ($matiere = $listeMatieres->fetch_object()) { ?>
							<option value="<?php echo $matiere->matiere.$matiere->code_modalite_elect; ?>">
								<?php echo $matiere->nom_complet ?>
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
						<button type="submit" name="creeAP" value="y"><img src='../images/enabled.png' /> Créer cet AP</button>
					</p>
					
				</div>
			</div>
			
		</div> 
	</fieldset>
</form>



<form action="index.php" method="post" id="definitionAP">
	<fieldset>
		<legend>Export des données</legend>
		<div class="lsun3colonnes">
			<div style='text-align:left;'>
				<ul class='pasPuces' disable>
					<li>
						<input type="checkbox" name="traiteEPI" id="traiteEPI" value="y" 
							   <?php if (getSettingValue("LSU_traite_EPI") != "n") {echo ' checked '; }  ?> />
						<label for="traiteEPI" label="Exporter les données générales des EPI">enseignements pratiques interdisciplinaires (EPI)</label>
					</li>
					<li>
						<input type="checkbox" name="traiteEpiElv" id="traiteEpiElv" value="y"
							   <?php if ((getSettingValue("LSU_traite_EPI") != "n") && (getSettingValue("LSU_traite_EPI_Elv") != "n")) {echo ' checked '; }  ?> />
						<label for="traiteEpiElv">données élèves des EPI</label>
					</li>
					<li>
						<input type="checkbox" name="traiteElemProg" id="traiteElemProg" value="y" checked disabled />
						<label for="traiteElemProg">éléments de programme</label>
					</li>
					<li>
						<input type="checkbox" name="traiteVieSco" id="traiteVieSco" value="y"
							   <?php if (getSettingValue("LSU_commentaire_vie_sco") != "n") {echo ' checked '; }  ?> />
						<label for="traiteVieSco" title="Exporter les commentaires de vie scolaire en plus des absences">commentaires de vie scolaires</label>
					</li>
				</ul>
			</div>
			<div style='text-align:left;'>
				<ul class='pasPuces' disable>
					<li>
						<input type="checkbox" name="traiteAP" id="traiteAP" value="y"     
							   <?php if (getSettingValue("LSU_traite_AP") != "n") {echo ' checked '; }  ?>  />
						<label for="traiteAP">accompagnements personnalisés (AP)</label>
					</li>
					<li>
						<input type="checkbox" name="traiteAPElv" id="traiteAPElv" value="y"      
							   <?php if ((getSettingValue("LSU_traite_AP") != "n") && (getSettingValue("LSU_traite_AP_Elv") != "n")) {echo ' checked '; }  ?>  />
						<label for="traiteAPElv">données élèves des AP</label>
					</li>
					<li>
						<input type="checkbox" name="traiteModSpeElv" id="traiteModSpeElv" value="y" disabled />
						<label for="traiteModSpeElv"  class="desactive" >modalités spécifiques d’accompagnement des élèves</label>
					</li>
					<li>
						<input type="checkbox" name="traiteParent" id="traiteParent" value="y"  
<<<<<<< bae0828fd34f56c2c80b6fbd640fa3818a388c2b
<<<<<<< b86cdb3ff11ae9315fd89fea47380346dac07a62
=======
>>>>>>> Export de EPI
							   <?php if (getSettingValue("LSU_Donnees_responsables") != "n") {echo ' checked '; }  ?> />
						<label for="traiteParent" title="Exporter les informations relatives aux responsables (nom prénom adresse">
							informations relatives aux responsables de l’élève
						</label>
<<<<<<< bae0828fd34f56c2c80b6fbd640fa3818a388c2b
=======
							   <?php if (getSettingValue("LSU_donnee_parent") != "n") {echo ' checked '; }  ?> />
						<label for="traiteParent">informations relatives aux responsables de l’élève</label>
>>>>>>> Export responsables
=======
>>>>>>> Export de EPI
					</li>
				</ul>
			</div>
			<div style='text-align:left;'>
				<ul class='pasPuces' disable>
					<li>
						<input type="checkbox" name="traiteParcours" id="traiteParcours" value="y"  
							   <?php if (getSettingValue("LSU_Parcours") != "n") {echo ' checked '; }  ?> />
						<label for="traiteParcours">parcours éducatifs</label>
					</li>
					<li>
						<input type="checkbox" name="traiteParcoursElv" id="traiteParcoursElv" value="y" disabled />
						<label for="traiteParcoursElv">données élèves des Parcours</label>
					</li>
					<li>
						<input type="checkbox" name="traiteProfP" id="traiteProfP" value="y" checked disabled />
						<label for="traiteAP">professeur(s) principal(aux)</label>
					</li>
					<li>
						<input type="checkbox" name="traiteSocle" id="traiteSocle" value="y" disabled />
						<label for="traiteSocle" class="desactive" >positionnement des élèves sur les domaines du socle commun</label>
					</li>
				</ul>
			</div>
		</div>
		
		<p class="lsun_cadre" >
			<a href="lib/creeXML.php" target="exportLSUN.xml" title="Affiche le fichier dans un nouvel onglet en interceptant les erreurs" >Afficher l'export</a>
		</p>
		<p class="center">
			<button type="submit" name="creeFichier" value="y">Créer le fichier</button>
		</p>
	</fieldset>
</form>

