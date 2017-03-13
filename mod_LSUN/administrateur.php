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

//debug_var();

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

	if(filter_input(INPUT_POST, 'traiteSocle')) {
		saveSetting('LSU_Donnees_socle', filter_input(INPUT_POST, 'traiteSocle'));
	}	else {
		saveSetting('LSU_Donnees_socle',  "n");
	}

	if(filter_input(INPUT_POST, 'traiteBilanFinCycle')) {
		saveSetting('LSU_Donnees_BilanFinCycle', filter_input(INPUT_POST, 'traiteBilanFinCycle'));
	}	else {
		saveSetting('LSU_Donnees_BilanFinCycle',  "n");
	}

	if(filter_input(INPUT_POST, 'CreerAutomatiquementElementsProgrammes')) {
		saveSetting('LSU_CreerAutomatiquementElementsProgrammes', filter_input(INPUT_POST, 'CreerAutomatiquementElementsProgrammes'));
	}	else {
		saveSetting('LSU_CreerAutomatiquementElementsProgrammes',  "n");
	}

	if (0 == count($selectionClasse)) {
		echo "<p class='rouge center gras'>Vous devez valider la sélection d'au moins une classe</p> <p><a href = 'index.php'>Cliquez ici pour recharger la page</a></p>";
	}	else if ($creeFichier == 'y') {
		include_once 'creeFichier.php';
	}

	include_once 'creeFichier.php';

}




//===== On récupère les données =====
$scolarites = getUtilisateurSurStatut('scolarite');
$cpes = getUtilisateurSurStatut('cpe');
$Enseignants = getUtilisateurSurStatut('professeur');
$responsables = getResponsables();
$parcoursCommuns = getParcoursCommuns();
$AidParcours = getAidParcours();
$ListeAidParcours = getLiaisonsAidParcours();

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

if(isset($msg_requetesAdmin)) {
	echo "<div align='center'>".$msg_requetesAdmin."</div>";
}
?>

<h2>Procédure</h2>
<div style='margin-left:3em;'>
	<h3>Les grandes lignes</h3>
	<div style='margin-left:3em;'>
		<p>L'opération de génération d'un fichier export XML à destination de l'application Livret Scolaire Unique <em title="Livret Scolaire Unique Numérique">(LSUN)</em> se déroule en plusieurs étapes&nbsp;:</p>
		<ol>
			<li>Sélection des classes</li>
			<li>Sélection/définition des EPI, AP et Parcours</li>
			<li>Sélection des éléments à exporter <em>(Bilans périodiques, positionnement sur le Socle de composantes, Bilan de fin de cycle,...)</em><br />
			Certains éléments cochés/grisés doivent impérativement être présents <em>(d'où les champs cochés et grisés)</em>.</li>
		</ol>
		<p>Si lors de l'export des erreurs sont signalées, vous devrez compléter/corriger dans Gepi <em>(nomenclatures ou modalités de matières manquantes, identifiants de professeurs manquants,...)</em></p>
		<p>Si l'export produit est conforme, vous pourrez l'importer dans l'application LSUN.</p>
	</div>
	<h3>Prérequis</h3>
	<div style='margin-left:3em;'>
		<p>Les nomenclatures, identifiants,... doivent être à jour.<br />
		S'il manque des éléments, les erreurs vous seront signalées et vous devrez corriger.</p>
		<p>L'étape de définition des EPI nécessite <em title="Il est envisagé de permettre la création complète des EPI depuis la présente page, mais ce n'est pas encore réalisé/finalisé.">actuellement (*)</em> d'avoir <a href='../aid/index.php' target='_blank'>créé les AID</a> correspondants préalablement.<br />
		Les AID peuvent être <a href='../aid/transfert_groupe_aid.php' target='_blank'>créés/migrés depuis des enseignements classiques</a>.<br />
		Et dans la présente page, le lien est fait entre ces AID et les EPI que vous souhaitez exporter vers LSUN.</p>
	</div>
</div>

<h2>Formulaires</h2>

<form action="index.php" method="post" id="responsables">
	<fieldset class='fieldset_opacite50'>
		<legend class='fieldset_opacite50' title="Données saisies dans les paramètres des classes" >Responsables de l'établissement</legend>
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
	<fieldset class='fieldset_opacite50'>
		<legend class='fieldset_opacite50'>Classes à exporter</legend>
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
		
		<p class="center"><a href='#' onClick='CocherClasses(true);return false;'>Tout cocher</a> / <a href='#' onClick='CocherClasses(false);return false;'>Tout décocher</a></p>

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

<!-- ======================================================================= -->
<!-- Formulaire Parcours -->

<form action="index.php" method="post" id="parcours">
	<fieldset class='fieldset_opacite50'>
		<legend class='fieldset_opacite50' title="Contient l’ensemble des informations relatives aux parcours éducatifs communs à une classe (contrainte d’unicité sur les combinaison de champs 'periodes', 'division' et 'Type de parcours').">
				Parcours communs
	</legend>
		<table>
			<caption style="caption-side:bottom">Parcours éducatifs communs à une classe pour une période</caption>
			<thead>
				<tr>
					<th>Période</th>
					<th>Division</th>
					<th>Type de parcours éducatifs</th>
					<th>Description</th>
					<th>liaison AID</th>
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
					<input type="text" name="modifieParcoursTexte[<?php echo $parcoursCommun->id; ?>]" size="70" value="<?php echo htmlspecialchars($parcoursCommun->description); ?>"/>
				</td>
				<td>
				
					<select name="modifieParcoursLien[<?php echo $parcoursCommun->id; ?>]">
						<option value=""></option>
<?php $AidParcours->data_seek(0);
while ($AidParc = $AidParcours->fetch_object()) { ?>
						<option value="<?php echo $AidParc->idAid; ?>" <?php if (getLiaisonsAidParcours($AidParc->idAid, $parcoursCommun->id)->num_rows && getLiaisonsAidParcours($AidParc->idAid, $parcoursCommun->id)->fetch_object()->id_aid) {echo " selected";} ?> >
							<?php echo $AidParc->aid; ?>
						</option>
<?php } ?>
					</select>
				
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
					<input type="text" name="newParcoursTexte" size="70" />
				</td>
				<td>
					<select name="newParcoursLien">
						<option value=""></option>
<?php $AidParcours->data_seek(0);
while ($APCommun = $AidParcours->fetch_object()) { ?>
						<option value="<?php echo $APCommun->indice_aid; ?>"><?php echo $APCommun->aid; ?></option>
<?php } ?>
					</select>
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
		<p style='margin-top:1em; margin-left:4em; text-indent:-4em;'><em>NOTE&nbsp;:</em> Les parcours affichés sont ceux des classes sélectionnées.<br />
		Si vous voulez voir tous les parcours saisis, sélectionnez toutes les classes dans le premier formulaire et validez la sélection.</p>
	</fieldset>
</form>

<style type='text/css'>
	.table_no_border {
		border:0px;
	}
	.table_no_border td {
		border:0px;
		vertical-align:top;
		text-align:left;
	}
	.table_no_border th {
		border:0px;
		vertical-align:top;
	}
</style>

<!-- ======================================================================= -->
<!-- Formulaire EPI -->

<form action="index.php" method="post" id="definitionEPI">
	<fieldset class='fieldset_opacite50'>
		<legend class='fieldset_opacite50'>EPIs</legend>
		<div id="div_epi">
			<p>Enseignements Pratiques Interdisciplinaires</p>
<?php
	/*
	// A quoi sert cette section?
	$tableauClasses = array();
	if (count($_SESSION['afficheClasse'])) {
		foreach ($_SESSION['afficheClasse'] as $classeSelectionne) {
			$tableauClasses[]=$classeSelectionne;
		}
	}
	*/

	$cpt_EPI_tmp=0;
	while ($epiCommun = $listeEPICommun->fetch_object()) {
		$cpt_EPI_tmp++;
		$tableauMatieresEPI = array();
		$listeMatieresEPI=getMatieresEPICommun($epiCommun->id);
		while ($matiereEPI = $listeMatieresEPI->fetch_object()) {
			$tableauMatieresEPI[] = array('matiere'=>$matiereEPI->id_matiere, 'modalite'=>$matiereEPI->modalite);
		}
?>
			<div class="lsun_cadre fieldset_opacite50">
				<div style='float:left; width:50em; font-weight:bold; font-size:x-large; text-align:left; margin-left:1em;'><?php echo $epiCommun->intituleEpi;?></div>
				<div align='center'>
					<input type="hidden" 
						   name="modifieEpiId[<?php echo $epiCommun->id; ?>]" 
						   value="<?php echo $epiCommun->id; ?>" />

					<table class='table_no_border'>
						<tr>
							<th>Thématique</th>
							<th>Intitulé</th>
							<th>Description</th>
						</tr>
						<tr>
							<td>
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
							</td>
							<td>
								<input type="text" size="40" name="modifieEpiIntitule[<?php echo $epiCommun->id; ?>]" value="<?php echo $epiCommun->intituleEpi; ?>" />
							</td>
							<td>
								<textarea rows="6" cols="50" name="modifieEpiDescription[<?php echo $epiCommun->id; ?>]" /><?php echo $epiCommun->descriptionEpi; ?></textarea>
							</td>
						</tr>
					</table>

					<table class='table_no_border'>
						<tr>
							<th colspan='2'>Divisions</th>
							<th>Période de fin</th>
						</tr>
						<tr>
							<td>
<?php
		$classes->data_seek(0);
		$cpt_classe=0;
		$cpt_classe_associees=0;
		$tab_classes_non_associees=array();
		while ($classe = $classes->fetch_object()) { 
			if(estClasseEPI($epiCommun->id,$classe->id)) {
				echo "
									<input type='checkbox' name='modifieEpiClasse".$epiCommun->id."[]' id='modifieEpiClasse".$epiCommun->id."_".$cpt_classe."' value='".$classe->id."' checked /><label for='modifieEpiClasse".$epiCommun->id."_".$cpt_classe."'>".$classe->classe." <em>(".$classe->classe.")</em></label><br />";
				$cpt_classe_associees++;
			}
			else {
				$tab_classes_non_associees[]=$classe->id;
			}
			$cpt_classe++;
		}
?>
							</td>

<?php

		if(count($tab_classes_non_associees)==0) {
			echo "
			<td style='color:red'>Aucune classe ne reste à associer</td>";
		}
		else {
?>
							<td onmouseover="document.getElementById('span_ajout_modifieEpiClasse_<?php echo $epiCommun->id;?>').style.display=''" 
								onmouseout="affiche_masque_select_EPI_AP_Parcours('modifieEpiClasse', <?php echo $epiCommun->id;?>)">
<?php
		if($cpt_classe_associees==0) {
			echo "
								<span style='color:red'><img src='../images/icons/ico_attention.png' class='icone16' alt='Attention' />Aucune classe n'est associée.</span><br />";
		}
?>
								<img src='../images/icons/add.png' class='icone16' alt='Ajouter' />
								Ajouter des divisions<br />
<!--
								<span id='span_ajout_modifieEpiClasse_<?php echo $cpt_EPI_tmp;?>'>
-->
<?php
$tab_span_champs_select[]='span_ajout_modifieEpiClasse_'.$epiCommun->id;
?>
								<span id='span_ajout_modifieEpiClasse_<?php echo $epiCommun->id;?>'>
								<select name="modifieEpiClasse<?php echo $epiCommun->id; ?>[]" 
										id="modifieEpiClasse<?php echo $epiCommun->id; ?>"
										multiple 
										title="Pour sélectionner plusieurs classes, effectuer CTRL+Clic sur chaque classe.">
<?php
			$classes->data_seek(0);
			while ($classe = $classes->fetch_object()) { 
				if (!estClasseEPI($epiCommun->id,$classe->id)) {
?>
									<option value="<?php echo $classe->id; ?>">
										<?php echo $classe->classe; ?> <?php echo $classe->nom_complet; ?>
									</option>
<?php
				}
			}
?>
								</select>
								</span>
							</td>
<?php
		}
?>
							<td>
								<input type="hidden" 
									   name="modifieEpiPeriode1[<?php echo $epiCommun->id; ?>]" 
									   value="<?php echo $epiCommun->periode; ?>" />
								<?php //echo $epiCommun->periode; ?>

								<select name="modifieEpiPeriode[<?php echo $epiCommun->id; ?>]">
									<option value=""></option>
<?php
		$periodes->data_seek(0);
		while ($periode = $periodes->fetch_object()) {
?>
									<option value="<?php echo $periode->num_periode; ?>"
										<?php if ($periode->num_periode == $epiCommun->periode) {echo " selected ";} ?> >
										<?php echo $periode->num_periode; ?>
									</option>
<?php
		}
?>
								</select>
<?php
		if(($epiCommun->periode=="")||($epiCommun->periode=="0")) {
			echo "
								<span style='color:red'><img src='../images/icons/ico_attention.png' class='icone16' alt='Attention' />Période de fin non définie.</span><br />";
		}
?>
							</td>
						</tr>
					</table>

					<table class='table_no_border'>
						<tr>
<?php
		if(count($tableauMatieresEPI)==0) {
			// Aucune matière n'est encore associée !
			echo "
							<th style='vertical-align:top; border:0px;'>
								Disciplines&nbsp;:&nbsp;
							</th>

							<td style='vertical-align:top; border:0px;' title=\"Associer des disciplines\">
								<span style='color:red'><img src='../images/icons/ico_attention.png' class='icone16' alt='Attention' />Aucune discipline n'est encore associée.</span><br />
								Associer des disciplines&nbsp;:<br />
								<select multiple 
										size='6' 
										name=\"modifieEpiMatiere".$epiCommun->id."[]\" 
										id=\"modifieEpiMatiere".$epiCommun->id."\" 
										title=\"Pour sélectionner plusieurs matières, effectuer CTRL+Clic sur chaque matière.\">";
			$listeMatieres->data_seek(0);
			while ($matiere = $listeMatieres->fetch_object()) { 
				if($matiere->code_modalite_elect=="") {
					echo "
									<option value='' disabled title=\"Modalité non définie.\" style='color:orange'>";
				}
				else {
					$style_matiere="";
					if($matiere->code_matiere=="") {
						$style_matiere=" style='color:red' title=\"La nomenclature de la matière ".$matiere->matiere." n'est pas renseignée.\nL'export ne sera pas valide sans que les nomenclatures soient corrigées.\"";
					}
					echo "
								<option value=\"".$matiere->matiere.$matiere->code_modalite_elect."\"".$style_matiere.">";
				}
				echo $matiere->matiere." (".$matiere->nom_complet;

				if ($matiere->code_modalite_elect == 'O') {
					echo '- option obligatoire';
				} elseif ($matiere->code_modalite_elect == 'F') {
					echo '- option facultative';
				} elseif ($matiere->code_modalite_elect == 'X') {
					echo '- mesure spécifique';
				}
				elseif ($matiere->code_modalite_elect =="N") {echo "- obligatoire ou facultatif";}
				elseif ($matiere->code_modalite_elect =="L") {echo "- ajout académique";}
				elseif ($matiere->code_modalite_elect =="R") {echo "- enseignement religieux";}
				echo ")</option>";
			}
			echo "
								</select>
							</td>
						</tr>";
		}
		else {
			// Il y a déjà des matières associées
			echo "
							<th style='vertical-align:top; border:0px;' rowspan='".count($tableauMatieresEPI)."'>
								Disciplines&nbsp;:&nbsp;
							</th>";
			$cpt_row=0;
			foreach ($tableauMatieresEPI as $matEPI) {
				if($cpt_row>0) {
					echo "
						<tr>";
				}

				// Matières déjà associées:
				echo "
								<td style='border:0px;'>
									<input type='checkbox' name='modifieEpiMatiere".$epiCommun->id."[]' id='modifieEpiMatiere".$epiCommun->id."_".$cpt_row."' value=\"".$matEPI['matiere'].$matEPI['modalite']."\" checked />
								</td>
								<td style='text-align:left; border:0px;'>
									<label for='modifieEpiMatiere".$epiCommun->id."_".$cpt_row."'>";
				echo $matEPI['matiere']." (";
				echo getMatiereOnMatiere($matEPI['matiere'])->nom_complet;
				echo "<span style='color:grey'>";
				if ($matEPI['modalite'] =="O") { echo " option obligatoire"; } 
				elseif ($matEPI['modalite'] =="F") {echo " option facultative";} 
				elseif ($matEPI['modalite'] =="X") {echo " mesure spécifique";}
				elseif ($matEPI['modalite'] =="N") {echo " obligatoire ou facultatif";}
				elseif ($matEPI['modalite'] =="L") {echo " ajout académique";}
				elseif ($matEPI['modalite'] =="R") {echo " enseignement religieux";}
				echo "</span>)</label>";
				if(getMatiereOnMatiere($matEPI['matiere'])->code_matiere=="") {
					echo "<img src='../images/icons/ico_attention.png' class='icone16' alt='Attention' title=\"La nomenclature de la matière ".$matEPI['matiere']." n'est pas renseignée.\nL'export ne sera pas valide sans que les nomenclatures soient corrigées.\" />";
				}
				echo "<br />";
				echo "
								</td>";

				// Ajouter des matières
				if($cpt_row==0) {

					$tab_matieres_non_associees=array();
					$listeMatieres->data_seek(0);
					while ($matiere = $listeMatieres->fetch_assoc()) { 
						if(!in_array(array('matiere'=>$matiere['matiere'],'modalite'=>$matiere['code_modalite_elect']), $tableauMatieresEPI)) {
							$tab_matieres_non_associees[]=$matiere;
						}
					}

					if(count($tab_matieres_non_associees)==0) {
						echo "
						<td style='color:red'>Aucune matière ne reste à associer</td>";
					}
					else {

						$tab_span_champs_select[]='span_ajout_modifieEpiMatiere_'.$epiCommun->id;
						echo "
								<td style='vertical-align:top; border:0px;' 
									title=\"Associer d'autres disciplines\" 
									rowspan='".count($tableauMatieresEPI)."' 
									onmouseover=\"document.getElementById('span_ajout_modifieEpiMatiere_".$epiCommun->id."').style.display=''\" 
									onmouseout=\"affiche_masque_select_EPI_AP_Parcours('modifieEpiMatiere', ".$epiCommun->id.")\">

									<img src='../images/icons/add.png' class='icone16' alt='Ajouter' />
									Associer d'autres disciplines<br />
									<span id='span_ajout_modifieEpiMatiere_".$epiCommun->id."'>
									<select multiple 
											size='6' 
											name=\"modifieEpiMatiere".$epiCommun->id."[]\" 
											id=\"modifieEpiMatiere".$epiCommun->id."\" 
											title=\"Pour sélectionner plusieurs matières, effectuer CTRL+Clic sur chaque matière.\">";
						$listeMatieres->data_seek(0);
						while ($matiere = $listeMatieres->fetch_object()) { 
							if(!in_array(array('matiere'=>$matiere->matiere,'modalite'=>$matiere->code_modalite_elect), $tableauMatieresEPI)) {
								if($matiere->code_modalite_elect=="") {
									echo "
										<option value='' disabled title=\"Modalité non définie.\" style='color:orange'>";
								}
								else {
									$style_matiere="";
									if($matiere->code_matiere=="") {
										$style_matiere=" style='color:red' title=\"La nomenclature de la matière ".$matiere->matiere." n'est pas renseignée.\nL'export ne sera pas valide sans que les nomenclatures soient corrigées.\"";
									}
									echo "
										<option value=\"".$matiere->matiere.$matiere->code_modalite_elect."\"".$style_matiere.">";
								}

								echo $matiere->matiere." (".$matiere->nom_complet;
								if ($matiere->code_modalite_elect == 'O') {
									echo '- option obligatoire';
								} elseif ($matiere->code_modalite_elect == 'F') {
									echo '- option facultative';
								} elseif ($matiere->code_modalite_elect == 'X') {
									echo '- mesure spécifique';
								}
								elseif ($matiere->code_modalite_elect =="N") {echo "- obligatoire ou facultatif";}
								elseif ($matiere->code_modalite_elect =="L") {echo "- ajout académique";}
								elseif ($matiere->code_modalite_elect =="R") {echo "- enseignement religieux";}
								echo ")</option>";
							}
						}
					}
					echo "
									</select>
									</span>
								</td>";
				}
				echo "
							</tr>";
				$cpt_row++;
			}
		}
?>
					</table>

<?php
		$listeLiaisons = getLiaisonEpiEnseignementByIdEpi($epiCommun->id); 
?>
					<table class='table_no_border'>
<?php
		if(mysqli_num_rows($listeLiaisons)==0) {
			// Aucune liaison AID à ce stade
			echo "
						<tr>
							<th>Liaisons&nbsp;:</th>
							<td>
								<span style='color:red'><img src='../images/icons/ico_attention.png' class='icone16' alt='Attention' />Aucune liaison AID n'est encore effectuée.</span><br />
								Associer des AID aux EPI<br />
								<select multiple 
										size='6' 
										name=\"modifieEpiLiaison".$epiCommun->id."[]\" 
										id=\"modifieEpiLiaison".$epiCommun->id."\" 
										title=\"Pour sélectionner plusieurs AID, effectuer CTRL+Clic sur chaque AID à associer à cet EPI.\">
									<option value=\"\"></option>";

							$listeAids = getEpiAid(); 
							while ($aid = $listeAids->fetch_object()) {
								if(!estCoursEpi($epiCommun->id ,"aid-".$aid->id_enseignement)) {
									echo "
									<option value=\"aid-".$aid->id_enseignement."\">aid-".$aid->description."</option>";
								}
							}

							echo "
								</select>
							</td>
						</tr>";
		}
		else {
			// Il y a déjà des liaisons AID
			$chaine_rowspan="";
			if(mysqli_num_rows($listeLiaisons)>1) {
				$chaine_rowspan=" rowspan='".mysqli_num_rows($listeLiaisons)."'";
			}
			echo "
						<tr>
							<th".$chaine_rowspan.">Liaison&nbsp;:</th>";

			$cpt_row=0;
			while ($liaison = $listeLiaisons->fetch_object()) {
				if ($liaison->aid) {
					if($cpt_row>0) {
						echo "
						<tr>";
					}
					echo "
							<td>
								<input type='checkbox' name='modifieEpiLiaison".$epiCommun->id."[]' id='modifieEpiLiaison".$epiCommun->id."_".$cpt_row."' value=\"aid-".$liaison->id_enseignements."\" checked />
							</td>
							<td>
								<label for='modifieEpiLiaison".$epiCommun->id."_".$cpt_row."'>AID - ".getAID($liaison->id_enseignements)->nom."</label>
							</td>";

					if($cpt_row==0) {
						// Ajouter une liaison

						$tab_aid_non_associes=array();
						$listeAids = getEpiAid(); 
						while ($aid = $listeAids->fetch_assoc()) {
							if(!estCoursEpi($epiCommun->id ,"aid-".$aid['id_enseignement'])) {
								$tab_aid_non_associes[]=$aid;
							}
						}

						if(count($tab_aid_non_associes)==0) {
							echo "
							<td style='color:red'>Aucun AID restant à associer</td>";
						}
						else {
							$tab_span_champs_select[]='span_ajout_modifieEpiLiaison_'.$epiCommun->id;
							echo "
							<td ".$chaine_rowspan." 
								onmouseover=\"document.getElementById('span_ajout_modifieEpiLiaison_".$epiCommun->id."').style.display=''\" 
								onmouseout=\"affiche_masque_select_EPI_AP_Parcours('modifieEpiLiaison', ".$epiCommun->id.")\">
								<img src='../images/icons/add.png' class='icone16' alt='Ajouter' />
								Associer d'autres AID à l'EPI<br />
								<span id='span_ajout_modifieEpiLiaison_".$epiCommun->id."'>
									<select multiple 
											size='6' 
											name=\"modifieEpiLiaison".$epiCommun->id."[]\" 
											id=\"modifieEpiLiaison".$epiCommun->id."\" 
											title=\"Pour sélectionner plusieurs AID, effectuer CTRL+Clic sur chaque AID à associer à cet EPI.\">
										<option value=\"\"></option>";

							$listeAids = getEpiAid(); 
							while ($aid = $listeAids->fetch_object()) {
								if(!estCoursEpi($epiCommun->id ,"aid-".$aid->id_enseignement)) {
									echo "
										<option value=\"aid-".$aid->id_enseignement."\">aid-".$aid->description."</option>";
								}
							}

							echo "
									</select>
								</span>
							</td>";
						}
					}
					echo "
						</tr>";
					$cpt_row++;
				}
			}
		}
?>
					</table>

					<!-- Validation des modifications de cet EPI -->

					<div>
							<button type="submit" name="supprimeEpi" value="<?php echo $epiCommun->id; ?>" ><img src='../images/disabled.png' style="width: 16px;" /> Supprimer cet EPI</button>
							<button type="submit" name="modifieEpi" value="<?php echo $epiCommun->id; ?>" ><img src='../images/enabled.png' />Modifier cet EPI</button>
							<button type="submit" name="creeAidEpi" value="<?php echo $epiCommun->id; ?>" disabled hidden><img src='../images/icons/copy-16.png' /> Créer un AID pour cet EPI</button>
					</div>
				</div>
			</div>
<?php
	}
?>

			<!-- Nouvel EPI -->

			<div class="lsun_cadre fieldset_opacite50">
				<div style='float:left; width:5em; font-weight:bold;'>Nouvel EPI</div>
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
						<select name="newEpiClasse[]" multiple size='6' title="Pour sélectionner plusieurs classes, effectuer CTRL+Clic sur chaque classe.">
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
						<select multiple size='6' name="newEpiMatiere[]" size="8" title="Pour sélectionner plusieurs matières, effectuer CTRL+Clic sur chaque matière.">
<?php
	$listeMatieres->data_seek(0);
	while ($matiere = $listeMatieres->fetch_object()) {
		if($matiere->code_modalite_elect=="") {
			echo "
							<option value='' disabled title=\"Modalité non définie.\" style='color:orange'>";
		}
		else {
			$style_matiere="";
			if($matiere->code_matiere=="") {
				$style_matiere=" style='color:red' title=\"La nomenclature de la matière ".$matiere->matiere." n'est pas renseignée.\nL'export ne sera pas valide sans que les nomenclatures soient corrigées.\"";
			}
			echo "
							<option value=\"".$matiere->matiere.$matiere->code_modalite_elect."\"".$style_matiere.">";
		}

		echo $matiere->nom_complet;
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

<!-- ======================================================================= -->
<!-- Formulaire AP -->

<form action="index.php" method="post" id="definitionAP">
	<fieldset class='fieldset_opacite50'>
		<legend class='fieldset_opacite50'>AP</legend>
		<div id="div_ap">
			<p>Accompagnements personnalisés</p>
			
<?php

	// Les AP déjà définis

	$listeAPCommun->data_seek(0);
	$cpt2 = 0;
	while ($ap = $listeAPCommun->fetch_object()) { ?>
			
			<div class="lsun_cadre fieldset_opacite50">
				<!-- AP <?php //echo $ap->id; ?> -->

				<div style='float:left; width:50em; font-weight:bold; font-size:x-large; text-align:left; margin-left:1em;'><?php echo $ap->intituleAP;?></div>
				<div align='center'>

					<table class='table_no_border'>
						<tr>
							<th>Intitulé</th>
							<th>Description</th>
							<th>Liaison</th>
						</tr>
						<tr>
							<td>
								<input type="text" name="intituleAp[<?php echo $ap->id; ?>]" value="<?php echo $ap->intituleAP; ?>" />
							</td>
							<td>
								<textarea rows="4" cols="50" id="ApDescription<?php echo $ap->id; ?>" name="ApDescription[<?php echo  $ap->id; ?>]" /><?php echo $ap->descriptionAP; ?></textarea>
							</td>
							<td>
<?php 
	$res_liaison=getAidConfig($ap->id_aid);
	if(mysqli_num_rows($res_liaison)==0) {
		echo "
								<span style='color:red'><img src='../images/icons/ico_attention.png' class='icone16' alt='Attention' />Aucune liaison AID n'est encore effectuée.</span><br />Sélectionnez un AID et Enregistrez la modification&nbsp;:<br />";
	}
?>
								<select name="liaisonApAid[<?php echo $ap->id; ?>]">
<?php
	$listeAidAp->data_seek(0);

	while ($liaison = $listeAidAp->fetch_object()) {
		$selected="";
		if($liaison->indice_aid == $ap->id_aid) {$selected='selected';}
		echo "
									<option value=\"".$liaison->indice_aid."\"".$selected.">".$liaison->groupe."</option>";
	}
?>
								</select>
							</td>
						</tr>
					</table>

<?php
	$listeAidAp->data_seek(0);


	$listeMatiereAP = disciplineAP($ap->id);
	//echo "\$listeMatiereAP = disciplineAP(".$ap->id.")<br />";
	$tableauMatiere=array();
	while ($matiereAP = $listeMatiereAP->fetch_object()) {
		/*
		echo "<pre>";
		print_r($matiereAP);
		echo "</pre>";
		echo "getMatiereSurMEF(".$matiereAP->id_enseignements.")<br />";
		*/
		//$tableauMatiere[] = $matiereAP->id_enseignements.$matiereAP->modalite;
		$matiere_courante=getMatiereSurMEF($matiereAP->id_enseignements)->fetch_object();
		$tableauMatiere[] = $matiere_courante->matiere.$matiereAP->modalite;
	}
	/*
	echo "<pre>";
	print_r($tableauMatiere);
	echo "</pre>";
	*/

?>

					<table class='table_no_border'>
						<tr>
<?php
		if(count($tableauMatiere)==0) {
			// Aucune matière n'est encore associée !
			echo "
							<th style='vertical-align:top; border:0px;'>
								Disciplines de référence&nbsp;:&nbsp;
							</th>

							<td style='vertical-align:top; border:0px;' title=\"Associer des disciplines\">
								<span style='color:red'><img src='../images/icons/ico_attention.png' class='icone16' alt='Attention' />Aucune discipline n'est encore associée.</span><br />
								Associer des disciplines&nbsp;:<br />
								<select multiple 
										size='6' 
										name=\"ApDisciplines".$ap->id."[]\" 
										id=\"ApDisciplines".$ap->id."\" 
										title=\"Pour sélectionner plusieurs matières, effectuer CTRL+Clic sur chaque matière.\">";
			$listeMatieres->data_seek(0);
			while ($matiere = $listeMatieres->fetch_object()) {
				if($matiere->code_matiere=="") {
					echo "
							<option value='' disabled title=\"La nomenclature de la matière ".$matiere->matiere." n'est pas renseignée.\" style='color:red'>";
					echo $matiere->matiere." (".$matiere->nom_complet;
					if ($matiere->code_modalite_elect == 'O') {
						echo '- option obligatoire';
					} elseif ($matiere->code_modalite_elect == 'F') {
						echo '- option facultative';
					} elseif ($matiere->code_modalite_elect == 'X') {
						echo '- mesure spécifique';
					}
					elseif ($matiere->code_modalite_elect =="N") {echo " - obligatoire ou facultatif";}
					elseif ($matiere->code_modalite_elect =="L") {echo " - ajout académique";}
					elseif ($matiere->code_modalite_elect =="R") {echo " - enseignement religieux";}
					echo ")</option>";
				}
				elseif($matiere->code_modalite_elect=="") {
					echo "
							<option value='' disabled title=\"Modalité non définie.\" style='color:orange'>";
					echo $matiere->matiere." (".$matiere->nom_complet;
					if ($matiere->code_modalite_elect == 'O') {
						echo '- option obligatoire';
					} elseif ($matiere->code_modalite_elect == 'F') {
						echo '- option facultative';
					} elseif ($matiere->code_modalite_elect == 'X') {
						echo '- mesure spécifique';
					}
					elseif ($matiere->code_modalite_elect =="N") {echo " - obligatoire ou facultatif";}
					elseif ($matiere->code_modalite_elect =="L") {echo " - ajout académique";}
					elseif ($matiere->code_modalite_elect =="R") {echo " - enseignement religieux";}
					echo ")</option>";
				}
				//elseif(!in_array($matiere->code_matiere.$matiere->code_modalite_elect ,$tableauMatiere)) {
				elseif(!in_array($matiere->matiere.$matiere->code_modalite_elect ,$tableauMatiere)) {
					echo "
							<option value=\"".$matiere->matiere.$matiere->code_modalite_elect."\">";
					echo $matiere->matiere." (".$matiere->nom_complet;
					if ($matiere->code_modalite_elect == 'O') {
						echo '- option obligatoire';
					} elseif ($matiere->code_modalite_elect == 'F') {
						echo '- option facultative';
					} elseif ($matiere->code_modalite_elect == 'X') {
						echo '- mesure spécifique';
					}
					elseif ($matiere->code_modalite_elect =="N") {echo "- obligatoire ou facultatif";}
					elseif ($matiere->code_modalite_elect =="L") {echo "- ajout académique";}
					elseif ($matiere->code_modalite_elect =="R") {echo "- enseignement religieux";}
					echo ")</option>";
				}
			}

			echo "
								</select>
							</td>
						</tr>";
		}
		else {

			// Il y a déjà des matières associées
			echo "
							<th style='vertical-align:top; border:0px;' rowspan='".count($tableauMatiere)."'>
								Disciplines de référence&nbsp;:&nbsp;
							</th>";
			$cpt_row=0;
			//$tableauMatiere=array();
			$listeMatiereAP->data_seek(0);
			while ($matiereAP = $listeMatiereAP->fetch_object()) {
				if(($matiereAP->id_enseignements!="")&&($matiereAP->modalite!="")) {
					if($cpt_row>0) {
						echo "
							<tr>";
					}

					// Matières déjà associées:
					$matiere_courante=getMatiereSurMEF($matiereAP->id_enseignements)->fetch_object();
					echo "
									<td style='border:0px;'>
										<input type='checkbox' name='ApDisciplines".$ap->id."[]' id='ApDisciplines".$ap->id."_".$cpt_row."' value=\"".$matiere_courante->matiere.$matiereAP->modalite."\" checked />
									</td>
									<td style='text-align:left; border:0px;'>
										<label for='ApDisciplines".$ap->id."_".$cpt_row."'>";
					echo $matiere_courante->matiere." (";
					echo $matiere_courante->nom_complet;
					echo "<span style='color:grey'>";
					if ($matiereAP->modalite =="O") { echo " option obligatoire"; } 
					elseif ($matiereAP->modalite =="F") {echo " option facultative";} 
					elseif ($matiereAP->modalite =="X") {echo " mesure spécifique";}
					elseif ($matiereAP->modalite =="N") {echo " obligatoire ou facultatif";}
					elseif ($matiereAP->modalite =="L") {echo " ajout académique";}
					elseif ($matiereAP->modalite =="R") {echo " enseignement religieux";}
					echo "</span>)</label>";
					echo "<br />";
					echo "
									</td>";

					// Ajouter des matières
					if($cpt_row==0) {

						$tab_span_champs_select[]='span_ajout_ApDisciplines_'.$ap->id;
						echo "
								<td style='vertical-align:top; border:0px;' 
									title=\"Associer d'autres disciplines\" 
									rowspan='".count($tableauMatiere)."' 
									onmouseover=\"document.getElementById('span_ajout_ApDisciplines_".$ap->id."').style.display=''\" 
									onmouseout=\"affiche_masque_select_EPI_AP_Parcours('ApDisciplines', ".$ap->id.")\">

									<img src='../images/icons/add.png' class='icone16' alt='Ajouter' />
									Associer d'autres disciplines<br />
									<span id='span_ajout_ApDisciplines_".$ap->id."'>
									<select multiple 
											size='6' 
											name=\"ApDisciplines".$ap->id."[]\" 
											id=\"ApDisciplines".$ap->id."\" 
											title=\"Pour sélectionner plusieurs matières, effectuer CTRL+Clic sur chaque matière.\">";
						$listeMatieres->data_seek(0);
						while ($matiere = $listeMatieres->fetch_object()) {
							if($matiere->code_matiere=="") {
								echo "
										<option value='' disabled title=\"La nomenclature de la matière ".$matiere->matiere." n'est pas renseignée.\" style='color:red'>";
								echo $matiere->matiere." (".$matiere->nom_complet;
								if ($matiere->code_modalite_elect == 'O') {
									echo '- option obligatoire';
								} elseif ($matiere->code_modalite_elect == 'F') {
									echo '- option facultative';
								} elseif ($matiere->code_modalite_elect == 'X') {
									echo '- mesure spécifique';
								}
								elseif ($matiere->code_modalite_elect =="N") {echo " - obligatoire ou facultatif";}
								elseif ($matiere->code_modalite_elect =="L") {echo " - ajout académique";}
								elseif ($matiere->code_modalite_elect =="R") {echo " - enseignement religieux";}
								echo ")</option>";
							}
							elseif($matiere->code_modalite_elect=="") {
								echo "
										<option value='' disabled title=\"Modalité non définie.\" style='color:orange'>";
								echo $matiere->matiere." (".$matiere->nom_complet;
								if ($matiere->code_modalite_elect == 'O') {
									echo '- option obligatoire';
								} elseif ($matiere->code_modalite_elect == 'F') {
									echo '- option facultative';
								} elseif ($matiere->code_modalite_elect == 'X') {
									echo '- mesure spécifique';
								}
								elseif ($matiere->code_modalite_elect =="N") {echo " - obligatoire ou facultatif";}
								elseif ($matiere->code_modalite_elect =="L") {echo " - ajout académique";}
								elseif ($matiere->code_modalite_elect =="R") {echo " - enseignement religieux";}
								echo ")</option>";
							}
							//elseif(!in_array($matiere->code_matiere.$matiere->code_modalite_elect ,$tableauMatiere)) {
							elseif(!in_array($matiere->matiere.$matiere->code_modalite_elect ,$tableauMatiere)) {
								echo "
										<option value=\"".$matiere->matiere.$matiere->code_modalite_elect."\">";
								echo $matiere->matiere." (".$matiere->nom_complet;
								if ($matiere->code_modalite_elect == 'O') {
									echo '- option obligatoire';
								} elseif ($matiere->code_modalite_elect == 'F') {
									echo '- option facultative';
								} elseif ($matiere->code_modalite_elect == 'X') {
									echo '- mesure spécifique';
								}
								elseif ($matiere->code_modalite_elect =="N") {echo "- obligatoire ou facultatif";}
								elseif ($matiere->code_modalite_elect =="L") {echo "- ajout académique";}
								elseif ($matiere->code_modalite_elect =="R") {echo "- enseignement religieux";}
								//echo $matiere->matiere.$matiere->code_modalite_elect;
								echo ")</option>";
							}
						}

						echo "
										</select>
										</span>
									</td>";
					}
					echo "
								</tr>";
					$cpt_row++;
				}
			}

		}
?>
						</table>

				<p>
					<button type="submit" name="supprimerAp" value="<?php echo  $ap->id; ?>" id="supprimeAp_<?php echo  $ap->id; ?>" title="Supprimer cet Accompagnement Personnalisé" ><img src='../images/disabled.png' style="width: 16px;" /> Supprimer</button>
					<button type="submit" name="modifierAp" value="<?php echo  $ap->id; ?>" id="modifierAp_<?php echo  $ap->id; ?>" title="Enregistrer les modifications pour cet Accompagnement Personnalisé" ><img src='../images/enabled.png' /> Modifier</button>
					<button type="submit" name="creeAidAp" value="<?php echo $ap->id; ?>" disabled hidden ><img src='../images/icons/copy-16.png' /> Créer un AID pour cet AP</button>
					
					</p>
				
			</div>
			</div>
<?php 
	$cpt2 ++;
}  ?>	

			<!-- Nouvel AP -->
			<div class="lsun_cadre fieldset_opacite50">
				<div style='float:left; width:5em; font-weight:bold;'>Nouvel AP</div>

				<div align='center'>

				<div>
					<p>
						<label for="newApIntituleAP">intitulé :</label>
						<input type="text" id="newApIntituleAP" name="newApIntituleAP" maxlength="150" />
						-
						<label for="newApDescription">Description :</label>
						<textarea rows="4" cols="50" id="newApDescription" name="newApDescription" /></textarea> 
						-
						<label for="newApDisciplines">Discipline(s) de référence</label>
						<select multiple size='6' name="newApDisciplines[]" size="8">
<?php
	$listeMatieres->data_seek(0);
	while ($matiere = $listeMatieres->fetch_object()) {
		if($matiere->code_modalite_elect=="") {
			echo "
							<option value='' disabled title=\"Modalité non définie.\" style='color:orange'>";
		}
		else {
			$style_matiere="";
			if($matiere->code_matiere=="") {
				$style_matiere=" style='color:red' title=\"La nomenclature de la matière ".$matiere->matiere." n'est pas renseignée.\nL'export ne sera pas valide sans que les nomenclatures soient corrigées.\"";
			}
			echo "
							<option value=\"".$matiere->matiere.$matiere->code_modalite_elect."\"".$style_matiere.">";
		}
		echo $matiere->nom_complet;

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
			
		</div> 
	</fieldset>
</form>


<script type='text/javascript'>
	function affiche_masque_select_EPI_AP_Parcours(prefixe_champ, idEPI) {
		if(document.getElementById(prefixe_champ+idEPI).selectedIndex=='-1') {
			document.getElementById("span_ajout_"+prefixe_champ+"_"+idEPI).style.display='none';
		}
	}

<?php
	//$tab_span_champs_select
	for($loop=0;$loop<count($tab_span_champs_select);$loop++) {
		echo "
	document.getElementById(\"".$tab_span_champs_select[$loop]."\").style.display='none';";
	}
?>
</script>

<!-- ======================================================================= -->
<!-- Formulaire Export des données -->

<form action="index.php" method="post" id="exportDonnees">
	<fieldset class='fieldset_opacite50'>
		<legend class='fieldset_opacite50'>Export des données</legend>
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
						<label for="traiteModSpeElv"  class="desactive" title="À saisir directement dans LSU" >modalités spécifiques d’accompagnement des élèves</label>
					</li>
					<li>
						<input type="checkbox" name="traiteParent" id="traiteParent" value="y"  
							   <?php if (getSettingValue("LSU_Donnees_responsables") != "n") {echo ' checked '; }  ?> />
						<label for="traiteParent" title="Exporter les informations relatives aux responsables (nom prénom adresse">
							informations relatives aux responsables de l’élève
						</label>
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
						<input type="checkbox" name="traiteParcoursElv" id="traiteParcoursElv" value="y"   
							   <?php if ((getSettingValue("LSU_Parcours") != "n") && (getSettingValue("LSU_ParcoursElv") != "n")) {echo ' checked '; }  ?>  />
						<label for="traiteParcoursElv">données élèves des Parcours</label>
					</li>
					<li>
						<input type="checkbox" name="traiteProfP" id="traiteProfP" value="y" checked disabled />
						<label for="traiteAP">professeur(s) principal(aux)</label>
					</li>
					<li>
						<input type="checkbox" name="traiteSocle" id="traiteSocle" value="y"  
							   <?php if (getSettingValue("LSU_Donnees_socle") == "y") {echo ' checked '; }  ?> />
						<label for="traiteSocle">positionnement des élèves sur les domaines du socle commun</label>
					</li>
					<li>
						<input type="checkbox" name="traiteBilanFinCycle" id="traiteBilanFinCycle" value="y"  
							   <?php if (getSettingValue("LSU_Donnees_BilanFinCycle") == "y") {echo ' checked '; }  ?> />
						<label for="traiteBilanFinCycle">Bilan de fin de Cycle</label>
					</li>
				</ul>
			</div>
		</div>
		
		<p class="lsun_cadre fieldset_opacite50" >
			<a href="lib/creeXML.php" target="exportLSUN.xml" title="Affiche le fichier dans un nouvel onglet en interceptant les erreurs" >Afficher l'export</a>
		</p>
		<p class="center">
			<button type="submit" name="creeFichier" value="y">Créer le fichier</button>
		</p>
	</fieldset>
</form>


