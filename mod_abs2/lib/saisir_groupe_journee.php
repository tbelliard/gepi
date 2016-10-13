<?php
if ($eleve_col->isEmpty()) {
?>
    <p>Aucun groupe selectionné</p>
<?php
} else {

include_once '../edt_organisation/fonctions_calendrier.php';
include_once '../edt_organisation/fonctions_edt.php';
include_once '../edt_organisation/req_database.php';
include_once '../edt_organisation/fonctions_edt_eleve.php';

function getTypeCurrentWeek($numero_sem_actu){
	$retour = '';
	$query = mysqli_query($GLOBALS["mysqli"], "SELECT type_edt_semaine FROM edt_semaines WHERE num_edt_semaine = '".$numero_sem_actu."'");
	if (count($query) == 1) {
		$type = old_mysql_result($query, 0);
		$retour = $type;
	}
	return $retour;
}

$coursCreneaux = isset ($_SESSION['creneau_cours_eleve']) ? $_SESSION['creneau_cours_eleve'] : (isset ($coursCreneaux) ? $coursCreneaux : 'cours');

if (isset ($_POST['creneau_cours_eleve'])) {
	$coursCreneaux = ($_POST['creneau_cours_eleve'] == 'cours') ? 'creneaux' : 'cours' ;
}

$_SESSION['creneau_cours_eleve'] = $coursCreneaux;

$col_creneaux = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();

$TypeSemaineCourante =  getTypeCurrentWeek($dt_date_absence_eleve->format('W'));

$dt_premiere_heure = clone $dt_date_absence_eleve;
$dt_premiere_heure->setTime($col_creneaux->getFirst()->getHeuredebutDefiniePeriode('H'),$col_creneaux->getFirst()->getHeuredebutDefiniePeriode('i'));
$premiere_heure = $dt_premiere_heure->format('U');

$dt_derniere_heure = clone $dt_date_absence_eleve;
$dt_derniere_heure->setTime($col_creneaux->getLast()->getHeurefinDefiniePeriode('H'),$col_creneaux->getLast()->getHeurefinDefiniePeriode('i'));
$derniere_heure = $dt_derniere_heure->format('U');
$derniere_heure = min($derniere_heure, date('U'));

$jour = strftime  ('%A',  $dt_date_absence_eleve->format('U'));

if ('cours' == $coursCreneaux) {

$indice=0;

	// chaque creneau fait 2 sequences
foreach($col_creneaux as $edt_creneau){ 
	$tableCreneau[$indice]['id'] = $edt_creneau->getIdDefiniePeriode();
	$tableCreneau[$indice]['heureDebut'] = $edt_creneau->getHeuredebutDefiniePeriode('U');
	$tableCreneau[$indice]['heureFin'] = $edt_creneau->getHeurefinDefiniePeriode('U');
	$tableCreneau[$indice]['typeCreneau'] = $edt_creneau->getTypeCreneaux();
	$indice++;
}

$tab_cours =array();



	//$absences_saisie = 
    $query = AbsenceEleveSaisieQuery::create();
    if ($current_aid != null) {
	$query->filterByIdAid($current_aid->getId());
    }
    if ($current_groupe != null) {
	$query->filterByIdGroupe($current_groupe->getId());
    }
    if ($current_classe != null) {
	$query->filterByIdClasse($current_classe->getId());
    }
    $query->filterByUtilisateurProfessionnel($utilisateur);
	
	
	$nb_creneau_a_saisir=0;

foreach ($afficheEleve as &$eleveCourant) {
	$login_e =$eleveCourant['accesFiche'];
	$periode = ReturnIdPeriod($dt_date_absence_eleve->format('U'));
	$tab_enseignement_final = array();
	$cpt = 0;
	$cptSeq = 0;
	$decalage = 0;
	$dureeCreneau = 0;
	$echap = 0;
	$pause = 0;
	while ((($cptSeq +1) / 2) < count($tableCreneau)) {
		if ('pause' == $tableCreneau[intval ($cptSeq / 2)]['typeCreneau']) {
			$eleveCourant['sequence'][$cptSeq]['duree'] = 'Pause';
			$cptSeq++ ;
			$eleveCourant['sequence'][$cptSeq]['duree'] = 'Pause';
			$cptSeq++ ;
		}
		
		if ($echap > 50) {
			echo ('ça boucle');
			exit ();
		}
		$echap++;
		
		$unCours = RecupereEnseignementsIDEleve($cpt, $jour, $login_e, $tab_enseignement_final, $periode);
		
		// il faut traiter les semaines A et B
		// id_semaine renvoie 0 toutes semaines ou A ou B (voir appellation des semaines quinzaines)
		if ($unCours) {	
			if ($tab_enseignement_final['id_semaine'][0] == '0') {
				$indiceSemaine = 0;
			} else {
				foreach ($tab_enseignement_final['id_semaine'] as $key=>$value) {
					$unCours = FALSE;
					if ($value == $TypeSemaineCourante) {
						$indiceSemaine = $key;
						$unCours = TRUE;
						break;
					}
				}	
			}
		}
			
		
		if ($unCours) {
			
			$duree = $tab_enseignement_final['duree'][$indiceSemaine];
			if ((.5 == $tab_enseignement_final['heuredeb_dec'][$indiceSemaine]) && ($decalage == 0)) {
				// On est en première heure, il faut décaler le compteur
				$eleveCourant['sequence'][$cptSeq]['duree'] = 'Etude';
				$cptSeq++;
			}
			$eleveCourant['sequence'][$cptSeq]['duree'] = $duree;
			$eleveCourant['sequence'][$cptSeq]['heureDebut'] = $tableCreneau[intval ($cptSeq / 2)]['heureDebut'];
			$eleveCourant['sequence'][$cptSeq]['heureFin'] = $tableCreneau[intval ($cptSeq / 2)]['heureFin'];
			$eleveCourant['sequence'][$cptSeq]['id_creneau'] = intval ($cptSeq / 2);
			$eleveCourant['sequence'][$cptSeq]['id_cours'] = $tab_enseignement_final['id_cours'][$indiceSemaine];
			$eleveCourant['sequence'][$cptSeq]['id_groupe'] = $tab_enseignement_final['id_groupe'][$indiceSemaine];

			// 20161013

			// on vérifie si une absence n'est pas déjà saisie
			$eleveCourant['sequence'][$cptSeq]['style'] = '';

			//on cherche l'élève
			$eleve = EleveQuery::create()->findPk($eleveCourant['id']);
			if ($eleve == null) {
				$message_enregistrement = "Probleme avec l'id élève : ".$eleveCourant['id'];
				die($message_enregistrement);
			}
	
			$edt_creneau = $col_creneaux[intval ($cptSeq / 2)];
			$absences_du_creneau = $eleve->getAbsenceEleveSaisiesDuCreneau($edt_creneau, $dt_date_absence_eleve);
	
			if (!$absences_du_creneau->isEmpty()) {
				foreach ($absences_du_creneau as $abs_saisie) {
					foreach ($abs_saisie->getAbsenceEleveTraitements() as $bou_traitement) {
						if ($bou_traitement->getAbsenceEleveType() != null) {
							$eleveCourant['sequence'][$cptSeq]['style'] = 'fondJaune';

							if(!isset($eleveCourant['sequence'][$cptSeq]['info_saisie'])) {
								$eleveCourant['sequence'][$cptSeq]['info_saisie']="";
							}
							$commentaire_associe="";
							if($abs_saisie->getCommentaire()!=null) {$commentaire_associe=" (".$saisie->getCommentaire().")";}
							$eleveCourant['sequence'][$cptSeq]['info_saisie'].=$bou_traitement->getAbsenceEleveType()->getNom().$commentaire_associe."\n";
							/*
							echo "<hr />bou_traitement<pre>";
							print_r($bou_traitement);
							echo "</pre>";
							*/
						}
					}

					if ($abs_saisie->getManquementObligationPresence()) {
						$eleveCourant['sequence'][$cptSeq]['style'] = 'fondRouge';
						break;
					}
				}
			} else if ($deja_saisie && $nb_creneau_a_saisir > 0) {
				$eleveCourant['sequence'][$cptSeq]['style'] = 'fondVert';
			}


			// Si le cours commence en milieu de créneau
			if (.5 == $tab_enseignement_final['heuredeb_dec'][$indiceSemaine]) {
				$debutCours = intval (($eleveCourant['sequence'][$cptSeq]['heureFin'] - $eleveCourant['sequence'][$cptSeq]['heureDebut']) / 2);
				$eleveCourant['sequence'][$cptSeq]['heureDebut'] = $eleveCourant['sequence'][$cptSeq]['heureDebut'] + $debutCours;
			}
			
			$derSeq = $cptSeq;
					$cptSeq++ ;
			$dep = $cptSeq;
			for ($i=1 ; $i < $duree ; $i++) {
				// On vérifie qu'on n'est pas dans une pose
				while ('pause' == $tableCreneau[intval (($cptSeq)/2)]['typeCreneau']) {
					$eleveCourant['sequence'][$cptSeq]['duree'] = 'Pause';
					$eleveCourant['sequence'][$derSeq]['duree']++; // on ajoute la sequence à la dernière séquence de cours
					$cptSeq++ ;
				}
				$eleveCourant['sequence'][$cptSeq]['duree'] = 'Cours';
				$cptSeq++;
			}
			// Si on a plus de 2 sequences, on va avoir un décalage avec les créneaux
			if ($duree > 2) {
				$decalage = $decalage + ($duree - 2);
				if (intval ($decalage/2) > 0) {
					// on a un decalage
					for ($i=0 ; $i < intval ($decalage/2) ; $i++) {
						$cpt++;
					}
					// on récupère le reste si $decalage était impaire
					$decalage = $decalage % 2;
					$eleveCourant['sequence'][$derSeq]['heureFin'] = $tableCreneau[intval (($cptSeq -1) / 2)]['heureFin'];
				}
			}
			// Si on a 1 décalage, le cours fini au milieu du créneau suivant
			if ($decalage) {
				if (.5 == $tab_enseignement_final['heuredeb_dec'][$indiceSemaine]) {
					$finCours = 0;
					$cptSeq--;
					$eleveCourant['sequence'][$derSeq]['heureFin'] = $tableCreneau[intval (($cptSeq) / 2)]['heureFin'];
				} else {
					$finCours = intval (($tableCreneau[intval ($cptSeq / 2)]['heureFin'] - $tableCreneau[intval ($cptSeq / 2)]['heureDebut']) / 2);
					$eleveCourant['sequence'][$derSeq]['heureFin'] = $tableCreneau[intval ($cptSeq / 2)]['heureFin'] - $finCours;
				}
			}
			
			// Si on a 1 décalage et pas de cours sur le créneau, on a 1/2 heure d'étude
			if ($decalage && !RecupereEnseignementsIDEleve(($cpt+1), $jour, $login_e, $tab_enseignement_final, $periode)) {
				$eleveCourant['sequence'][$cptSeq]['duree'] = 'Etude';
				$cptSeq++;
				$decalage = 0;
			$cpt++;
			}
			
			$cpt++;
		} else {
			$eleveCourant['sequence'][$cptSeq]['duree'] = 'Etude';
			$cptSeq++ ;
			$eleveCourant['sequence'][$cptSeq]['duree'] = 'Etude';
			$cptSeq++ ;
			$cpt++;
		}
	}
}

}

$tab_suhosin=array('suhosin.post.max_vars', 'suhosin.request.max_vars');

$suhosin = array();
for($i=0;$i<count($tab_suhosin);$i++) {
	if (ini_get($tab_suhosin[$i]) && ini_get($tab_suhosin[$i]) < 2000) {
		$suhosin[$i] = ini_get($tab_suhosin[$i]);
	}
}





?>
	<script type="text/javascript">
	//<![CDATA[
		function change_bouton() {
			var texte = "Enregistrement en cours!";
			var depart_rd = 0;
			var taille_rd = document.getElementById("enregistre").firstChild.nodeValue.length;
			document.getElementById("enregistre").firstChild.replaceData(depart_rd,taille_rd,texte);
			document.getElementById("enregistre").disabled=true;
			var taille_rd = document.getElementById("SavePrint").firstChild.nodeValue.length;
			document.getElementById("SavePrint").firstChild.replaceData(depart_rd,taille_rd,texte);
			document.getElementById("SavePrint").disabled=true;
			var taille_rd = document.getElementById("enregistre2").firstChild.nodeValue.length;
			document.getElementById("enregistre2").firstChild.replaceData(depart_rd,taille_rd,texte);
			document.getElementById("enregistre2").disabled=true;
			var taille_rd = document.getElementById("SavePrint2").firstChild.nodeValue.length;
			document.getElementById("SavePrint2").firstChild.replaceData(depart_rd,taille_rd,texte);
			document.getElementById("SavePrint2").disabled=true;
			return true;
}
			//]]>
	</script>
		
<div>
<?php if(count($suhosin)) { ?>
	<p style="color: red;">
		Votre serveur est protégé par Suhosin, les valeurs de 'suhosin.post.max_vars' et de 'suhosin.request.max_vars' seront
		peut-être trop basses pour enregistrer tous les élèves
	</p>
<?php } ?>
	<form method="post" action="saisir_groupe.php" id="creneau_cours_eleve">
		<p class="center" style="margin-bottom: .5em">
		  <button type='submit' style='width:25em;margin:0 auto;' name='creneau_cours_eleve' value='<?php echo $coursCreneaux; ?>'>
			 <?php if ('cours' == $coursCreneaux) { ?>
				 Affichage des créneaux de l'établissement
			 <?php } else { ?>
				 Affichage des cours des élèves
			 <?php } ?>
		  </button>
		</p>
	</form>
	<form method="post" action="enregistrement_saisie_journee.php" id="liste_absence_eleve">
		<p>
			<input type="hidden" name="total_eleves" value="<?php echo($eleve_col->count()); ?>"/>
			<input type="hidden" name="id_aid" value="<?php echo($id_aid); ?>"/>
			<input type="hidden" name="id_groupe" value="<?php echo($id_groupe); ?>"/>
			<input type="hidden" name="id_classe" value="<?php echo($id_classe); ?>"/>
<?php
if ('cours' == $coursCreneaux) {
 ?>
			<input type="hidden" name="type_selection" value="<?php echo($type_selection); ?>"/>
<?php } elseif (isset ($id_classe)) { ?>
			 <input type="hidden" name="type_selection" value="id_classe"/>
<?php } elseif (isset ($id_groupe)) { ?>
			 <input type="hidden" name="type_selection" value="id_groupe"/>
<?php } elseif (isset ($id_aid)) {  ?>
			 <input type="hidden" name="type_selection" value="id_aid"/>
<?php } ?>
			<input type="hidden" name="id_semaine" value="<?php echo($id_semaine); ?>"/>
			<input type="hidden" name="date_absence_eleve" value="<?php echo($dt_date_absence_eleve->format('d/m/Y')); ?>"/>
		</p>
		<p class="expli_page choix_fin center">
			Saisie des absences du
			<strong><?php echo strftime  ('%A %d/%m/%Y',  $dt_date_absence_eleve->format('U')); ?></strong>
			pour 
			<strong>
			<?php if (isset($current_groupe) && $current_groupe != null) {
				echo 'le groupe '.$current_groupe->getNameAvecClasses();
			} else if (isset($current_aid) && $current_aid != null) {
				echo 'l\'aid '.$current_aid->getNom();
			} else if (isset($current_classe) && $current_classe != null) {
				echo 'la classe '.$current_classe->getNom();
			} ?>
			</strong>
			(Semaine <?php echo $TypeSemaineCourante; ?>)
			<br/>
			(les élèves non cochés seront considérés présents)
		</p>
		
		<p class="choix_fin center">
			<button type="submit"
					name="Valider"
					id="enregistre"
					value="Enregistrer" 
					style='width:25em;margin:0 auto;'
					onclick="change_bouton();" >
				Enregistrer
			</button>
			
			<input type="hidden"
				   name="Valider"
				   id="sauveImprime"
				   value="Enregistrer" />
			
			<button type="submit"
					name="Valider"
					id="SavePrint"
					value="SavePrint" 
					style='width:25em;margin:0 auto;'
					title ="Enregistre et crée une notification à 'Pret à envoyer'"
					onclick="document.getElementById('sauveImprime').value='SavePrint';change_bouton();" >
				Enregistrer + notifications
			</button>
			
		
			
		</p>
		<p class="center"><?php echo $eleve_col->count(); ?> élèves.</p>
		<p class="choix_fin center">	
<?php	
if (isset ($afficheEleve[0]['type_autorises'][0])) { ?>
						
			<label for="type_absence_eleve">Type d'absence : </label>
			<select class="selects"
					name="type_absence_eleve"
					id="type_absence_eleve">
				<option class="pc88" value="-1"> </option>
<?php foreach ($afficheEleve[0]['type_autorises'][0] as $type) { ?>
				<option class="pc88" value="<?php echo $type['type']; ?>">
					<?php echo $type['nom']; ?>
				</option>
<?php } ?>
			</select>
<?php } ?>

                        <label for="type_motif_eleve">Motif : </label>
			<select class="selects"
					name="type_motif_eleve"
					id="type_motif_eleve">
				<option class="pc88" value="-1"> </option>

<?php foreach (AbsenceEleveMotifQuery::create()->orderByRank()->find() as $motif) { ?>
				<option class="pc88" value="<?php echo $motif->getId(); ?>">
					<?php echo $motif->getNom(); ?>
				</option>
<?php } ?>

			</select>

			<input type="hidden" 
				   name="heure_debut_journee"
				   value="<?php echo $premiere_heure; ?>"
				   />
			
			<input type="hidden" 
				   name="heure_fin_journee"
				   value="<?php echo $derniere_heure; ?>"
				   />
		</p>
<?php
if ('cours' == $coursCreneaux) {
	/* ===== On affiche les cours de chaque élève ===== */
 ?>
		<table class="tb_absences" style="padding-bottom:1em;">
			<caption class="invisible no_print"><?php echo $eleve_col->count(); ?> élèves.</caption>
			<thead>
				<tr class="titre_tableau_gestion" style="white-space: nowrap;">
					<th class="center" >Veille</th>
					<th class="center" abbr="élèves">Liste des élèves</th>
					<th colspan="<?php echo (EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime()->count()*2); ?>" 
						class="th_abs_suivi" abbr="Créneaux">Suivi sur la journée</th>
					<th></th>
					<th></th>
				</tr>
				<tr>
					<td></td>
					<td class="center" ></td>
					<?php $i=0;
					foreach(EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $edt_creneau){ ?>
					<td class="td_nom_creneau center" colspan="2">
						&nbsp;<?php echo $edt_creneau->getNomDefiniePeriode();?>&nbsp;
					</td>
			<?php $i++;

			}?>
					<td colspan="2" title="Créer un traitement différent pour chaque saisie">Plusieurs traitements</td>
				</tr>
			</thead>
			<tbody>
<?php 

$cpt=0;
foreach($afficheEleve as $eleve) { 
	
	?>
				<tr class='<?php echo $eleve['background'];?>'>
					<td class = "<?php if($eleve['class_hier'] !=''){echo 'absentHier';} ?>">
						<?php echo $eleve['text_hier']; ?>
					</td>
				
					<td class='td_abs_eleves'>
						<span class="td_abs_eleves">
							<?php echo strtoupper($eleve['nom']).' '.ucfirst($eleve['prenom']).' ('.$eleve['civilite'].')'; ?>
							<?php if (isset ($eleve['classe'])) echo $eleve['classe']; ?>
						</span>
<?php if (isset ($eleve['accesFiche'])) { ?>
						<a href='../eleves/visu_eleve.php?ele_login=<?php echo $eleve['accesFiche']; ?>&amp;onglet=responsables&amp;quitter_la_page=y' target='_blank'>
							(voir&nbsp;fiche)
						</a>
<?php } ?>
					</td>
	
<?php 
for($i = 0; $i<count($eleve['sequence']); $i++) {
	// 20161013
	$title="";
	if(isset($eleve['sequence'][$i]['info_saisie'])) {
		$title=" title=\"".preg_replace('/"/',"'",$eleve['sequence'][$i]['info_saisie'])."\"";
	}

	if (intval($eleve['sequence'][$i]['duree'])) {
		$colspan = intval($eleve['sequence'][$i]['duree']);
		$couleur = " ".$eleve['sequence'][$i]['style'];
	} else {
		$colspan = 1;
		$couleur = '';
	}
?>	
					<td colspan="<?php echo $colspan; ?>" 
						class="center <?php echo $couleur; ?>"<?php echo $title;?>
						>
<?php if (intval($eleve['sequence'][$i]['duree'])) { ?>
						<label for="active_absence_eleve_<?php echo $cpt; ?>" class="invisible"><?php echo $cpt; ?> actif</label>
						<input type="checkbox" 
							   name="active_absence_eleve[<?php echo $cpt; ?>]" 
							   id="active_absence_eleve_<?php echo $cpt; ?>"
							   />  
						<input type="hidden" 
							   name="id_eleve_absent[<?php echo $cpt; ?>]" 
							   value="<?php echo $eleve['id']; ?>" />
						
<?php } ?>
						<br />
<?php if (isset ($eleve['sequence'][$i]['heureDebut'])) {		 ?>	
						
						<label for="heure_debut_appel_<?php echo $cpt; ?>" class="invisible"><?php echo $cpt; ?> heure début appel</label>
						<input type="text" 
							   name="heure_debut_appel[<?php echo $cpt; ?>]"
							   id="heure_debut_appel_<?php echo $cpt; ?>"
							   size="5"
							   style="font-family:sans-serif;text-align: center;"
							   value="<?php echo date ('H:i', $eleve['sequence'][$i]['heureDebut']); ?>"
							   />
						
						<input type="hidden" 
							   name="id_cours[<?php echo $cpt; ?>]"
							   value="<?php echo $eleve['sequence'][$i]['id_cours']; ?>"
							   />
						
						<input type="hidden" 
							   name="id_groupe_el[<?php echo $cpt; ?>]"
							   value="<?php echo $eleve['sequence'][$i]['id_groupe']; ?>"
							   />
						
						<label for="heure_fin_appel_<?php echo $cpt; ?>" class="invisible"><?php echo $cpt; ?> heure fin appel</label>
						<input type="text" 
							   name="heure_fin_appel[<?php echo $cpt; ?>]"
							   id="heure_fin_appel_<?php echo $cpt; ?>"
							   size="5"
							   style="font-family:sans-serif;text-align: center;"
							   value="<?php echo date ('H:i', $eleve['sequence'][$i]['heureFin']); ?>"
							   />
<?php } ?>
					</td>
						
						
<?php
$i = $i + $colspan - 1;
$cpt++;
} ?>		
					<td class="center" title="Créer un traitement différent pour chaque saisie" >
						<label for="multi_traitement_<?php echo $eleve['id']; ?>" class="invisible"><?php echo $cpt; ?> traitement différent pour chaque saisie</label>					
						<input type="checkbox" 
							   name="multi_traitement[<?php echo $eleve['id']; ?>]" 
							   id="multi_traitement_<?php echo $eleve['id']; ?>"
							   />
					</td>
<?php if (isset ($eleve['nom_photo'])) { ?>
						<td>
							<img src="<?php echo $eleve['nom_photo']; ?>"
								 class="trombine"
								 alt="photo identité" 
								 title="<?php echo strtoupper($eleve['nom']).' '.ucfirst($eleve['prenom']); ?>" />
							
						</td>
<?php } ?>
				</tr>
<?php
$cpt++;
} 
?>
			</tbody>
		</table>
<?php
} else {
	/* ===== On affiche les créneaux de l'établissement =====*/
?>
		<table class="tb_absences">
			<caption class="invisible no_print"><?php echo $eleve_col->count(); ?> élèves.</caption>
			<thead>
				<tr class="titre_tableau_gestion" style="white-space: nowrap;">
					<th class="center" >Veille</th>
					<th class="center" abbr="élèves">Liste des élèves</th>
					<th colspan="<?php echo ($col_creneaux->count()); ?>" 
						class="th_abs_suivi" abbr="Créneaux">Suivi sur la journée</th>
					<th></th>
					<th></th>
				</tr>
				<tr>
					<td></td>
					<td class="center" ></td>
					<?php $i=0;
					foreach($col_creneaux as $edt_creneau){ ?>
					<td class="td_nom_creneau center">
						&nbsp;<?php echo $edt_creneau->getNomDefiniePeriode();?>&nbsp;
					</td>
			<?php $i++;

			}?>
					<td colspan="2" title="Créer un traitement différent pour chaque saisie">Plusieurs traitements</td>
				</tr>
			</thead>
			<tbody>
				
<?php 

$numElv=0;
$cpt=0;
foreach($eleve_col as $eleve) { 
	
	?>
				<tr class='<?php if(0 == ($numElv % 2)) {echo "pair";} else {echo "impair";}?>'>
					<td class = "<?php if($eleve->getAbsenceEleveSaisiesDuJour($Yesterday)->count()){echo 'absentHier';} ?>">
						<?php if($eleve->getAbsenceEleveSaisiesDuJour($Yesterday)->count()){echo $eleve->getAbsenceEleveSaisiesDuJour($Yesterday)->count().' enr.';} ?>
					</td>
				
					<td class='td_abs_eleves'>
						<span class="td_abs_eleves">	
							<?php echo strtoupper($eleve->getNom()).' '.ucfirst($eleve->getPrenom()).' ('.$eleve->getCivilite().')'; ?>
							<?php if ($eleve->getClasse()) echo $eleve->getClasse()->getNom(); ?>
						</span>
<?php if ($utilisateur->getAccesFicheEleve($eleve)) { ?>
						<a href='../eleves/visu_eleve.php?ele_login=<?php echo $eleve->getLogin(); ?>&amp;onglet=responsables&amp;quitter_la_page=y' target='_blank' >
							(voir&nbsp;fiche)
						</a>
<?php } ?>
					</td>
<?php 
foreach ($col_creneaux as $edt_creneau) {
	//$creneaux = ->;
	//die();
?>	
<?php 
	 $absences_du_creneau = $eleve->getAbsenceEleveSaisiesDuCreneau($edt_creneau, $dt_date_absence_eleve);
	 $style = '';
	 if (!$absences_du_creneau->isEmpty()) {
			foreach ($absences_du_creneau as $abs_saisie) {
				if ($abs_saisie->getManquementObligationPresence()) {
					$style = " fondRouge";
					break;
				}
			}
		}
?>	
					<td class="center<?php echo $style; ?>" title="<?php echo $edt_creneau->getNomDefiniePeriode();?>" >
	<?php if ($edt_creneau->getTypeCreneaux() != 'pause') {  ?>	
						<input type="checkbox" 
							   name="active_absence_eleve[<?php echo $cpt; ?>]" 
							   />  
						<input type="hidden" 
							   name="id_eleve_absent[<?php echo $cpt; ?>]" 
							   value="<?php echo $eleve->getId(); ?>" />
						<br />
						<input type="hidden" 
							   name="id_creneau[<?php echo $cpt; ?>]" 
							   value="<?php echo $edt_creneau->getIdDefiniePeriode(); ?>" />
						<br />
						
						<input type="text" 
							   name="heure_debut_appel[<?php echo $cpt; ?>]"
							   size="5"
							   style="font-family:sans-serif;text-align: center;"
							   value="<?php echo date ('H:i', $edt_creneau->getHeuredebutDefiniePeriode('U')); ?>"
							   />
												
						<input type="text" 
							   name="heure_fin_appel[<?php echo $cpt; ?>]"
							   size="5"
							   style="font-family:sans-serif;text-align: center;"
							   value="<?php echo date ('H:i', $edt_creneau->getHeurefinDefiniePeriode('U')); ?>"
							   />
						

	<?php 
	
	$cpt++;
	} 
	?>
					</td>
					
<?php } ?>	
					<td class="center" title="Créer un traitement différent pour chaque saisie">					
						<input type="checkbox" 
							   name="multi_traitement[<?php echo $eleve->getId(); ?>]" 
							   />
					</td>
<?php 

	if ((getSettingValue("active_module_trombinoscopes")=='y')) {
		$nom_photo = $eleve->getNomPhoto(1);
		$photos = $nom_photo;
		if (($photos == NULL) or (!(file_exists($photos)))) {
			$photos = "../mod_trombinoscopes/images/trombivide.jpg";
		}
	}


if (isset ($photos)) { ?>
					<td>
						<img src="<?php echo $photos; ?>"
							 class="trombine"
							 alt="photo identité" 
							 title="<?php echo $eleve->getNom(); ?> <?php echo $eleve->getPrenom(); ?>" />
					</td>
						
<?php } ?>	
				</tr>
<?php
$cpt++;
$numElv++;

} 
?>			
			</tbody>
		</table>
	
<?php } ?>
		<p class="center">
			<button type="submit"
					name="Valider"
					id="enregistre2"
					value="Enregistrer" 
					style='width:25em;margin:0 auto;'
					onclick="change_bouton();" >
				Enregistrer
			</button>
			
			<button type="submit"
					name="Valider"
					id="SavePrint2"
					value="SavePrint" 
					style='width:25em;margin:0 auto;'
					title ="Enregistre et crée une notification à 'Pret à envoyer'"
					onclick="document.getElementById('sauveImprime').value='SavePrint';change_bouton();" >
				Enregistrer + notifications
			</button>
		</p>
		
		
		<input type='hidden' name='temoin_dernier_champ_formulaire_transmis' value='y' />
	</form>
</div>
<?php
}
?>
</div>
<?php
require_once("../lib/footer.inc.php");
?>

<?php

// $affiche_debug=debug_var();

die();
?>
