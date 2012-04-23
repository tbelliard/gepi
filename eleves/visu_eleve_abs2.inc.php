<?php

/**
 * Affichage des saisies de Abs2 dans la fiche élève
 * 
 */
$non_traitees=1;
$tri='';
$type_extrait = 1; // filtrer par manquement aux obligations scolaires


// Initialisation de l'élève
$eleve_id = $eleve->getId();
$donnees[$eleve_id]['nom'] = $eleve->getNom();
$donnees[$eleve_id]['prenom'] = $eleve->getPrenom();
$donnees[$eleve_id]['classe'] = $eleve->getClasseNom();        
$donnees[$eleve_id]['nbre_lignes_total'] = 0;

$debutAnnee = NULL;
$finAnnee = NULL;
foreach($eleve->getPeriodeNotes() as $periode_note) {
  if (!$debutAnnee) {
	$debutAnnee = $periode_note->getDateDebut("d-m-Y");
  }
  $finAnnee = $periode_note->getDateFin("d-m-Y");
}

/***** Gestion des dates *****/

//date de début et fin d'année par défaut
$_SESSION["date_absence_eleve_debut"] = isset($_SESSION["date_absence_eleve_debut"]) ? $_SESSION["date_absence_eleve_debut"] : $debutAnnee ;
$_SESSION["date_absence_eleve_fin"] = isset($_SESSION["date_absence_eleve_fin"]) ? $_SESSION["date_absence_eleve_fin"] : $finAnnee ;

// récupérer les dates postées
$date_absence_eleve_debut = isset($_POST["date_absence_eleve_debut"]) ? $_POST["date_absence_eleve_debut"] : (isset($_GET["date_absence_eleve_debut"]) ? $_GET["date_absence_eleve_debut"] : $_SESSION["date_absence_eleve_debut"]);
$date_absence_eleve_fin = isset($_POST["date_absence_eleve_fin"]) ? $_POST["date_absence_eleve_fin"] : (isset($_GET["date_absence_eleve_fin"]) ? $_GET["date_absence_eleve_fin"] : $_SESSION["date_absence_eleve_fin"]);

if ($date_absence_eleve_debut != null) {
    $dt_date_absence_eleve_debut = new DateTime(str_replace("/", ".", $date_absence_eleve_debut));
} else {
    $dt_date_absence_eleve_debut = new DateTime('now');
    $dt_date_absence_eleve_debut->setDate($dt_date_absence_eleve_debut->format('Y'), $dt_date_absence_eleve_debut->format('m') , $dt_date_absence_eleve_debut->format('d'));
}
if ($date_absence_eleve_fin != null) {
    $dt_date_absence_eleve_fin = new DateTime(str_replace("/", ".", $date_absence_eleve_fin));
} else {
    $dt_date_absence_eleve_fin = new DateTime('now');
}
$dt_date_absence_eleve_debut->setTime(0, 0, 0);
$dt_date_absence_eleve_fin->setTime(23, 59, 59);
$inverse_date=false;
if($dt_date_absence_eleve_debut->format("U")>$dt_date_absence_eleve_fin->format("U")){
    $date2=clone $dt_date_absence_eleve_fin;
    $dt_date_absence_eleve_fin= $dt_date_absence_eleve_debut;
    $dt_date_absence_eleve_debut= $date2;
    $inverse_date=true;
    $_SESSION['date_absence_eleve_debut'] = $dt_date_absence_eleve_debut->format('d/m/Y');
    $_SESSION['date_absence_eleve_fin'] = $dt_date_absence_eleve_fin->format('d/m/Y');
}
/***** Fin gestion des dates *****/

$eleve_id = $eleve->getId();

//on initialise les donnees pour l'élève
$donnees[$eleve_id]['nom'] = $eleve->getNom();
$donnees[$eleve_id]['prenom'] = $eleve->getPrenom();
$donnees[$eleve_id]['classe'] = $eleve->getClasseNom();        
$donnees[$eleve_id]['nbre_lignes_total'] = 0;
	
// on récupère les saisies de l'élève
$saisie_query = AbsenceEleveSaisieQuery::create()
				->filterByPlageTemps($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
				->filterByEleveId($eleve->getId());
if ($type_extrait == '1') {
	$saisie_query->filterByManquementObligationPresence(true);
}
$saisie_query->orderByDebutAbs();    
$saisie_col = $saisie_query->find();

// on traite les saisies et on stocke les informations dans un tableau
foreach ($saisie_col as $saisie) {
  if ($type_extrait == '1' && !$saisie->getManquementObligationPresence()) {
	continue;
  }
 /* 
  if (!is_null($non_traitees) && $non_traitees != '' && $saisie->getTraitee() && $saisie->hasModeInterface()) {
	continue;
  }
  */
  
  // on repère si retards, manquements, sans_manquements si $tri n'est pas vide
  if ($saisie->getRetard()) {
	  if ($tri != null && $tri != '') {
		  $type_tab = 'retard';
	  } else {
		  $type_tab = 'sans';
	  }
	  $type_css = 'couleur_retard';
  } elseif ($saisie->getManquementObligationPresence()) {
	  if ($tri != null && $tri != '') {
		  $type_tab = 'manquement';
	  } else {
		  $type_tab = 'sans';
	  }
	  $type_css = 'couleur_manquement';
  } else {
	  if ($tri != null && $tri != '') {
		  $type_tab = 'sans_manquement';
	  } else {
		  $type_tab = 'sans';
	  }
	  $type_css = '';
  }
  if ($saisie->getTraitee()) {
	// La saisie est traitée, on stocke les infos
	foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
	  if (!isset($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()])) {
		// on avait pas de saisie de ce type pour ce traitement ce jour
		$donnees[$eleve_id]['nbre_lignes_total']++;
	  }
	  // On ajoute la saisie
	  $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['saisies'][] = $saisie->getId();               
	  if (isset($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates'])) {
		// On a déjà des saisies pour ce type, ce jour
		if ($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates']['debut'] > $saisie->getDebutAbs('U')) {
		  // On met à jour la date et l'heure de début au besoin
		  $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates']['debut'] = $saisie->getDebutAbs('U');
		}
		if ($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates']['fin'] < $saisie->getFinAbs('U')) {
		  // On met à jour la date et l'heure de fin au besoin
		  $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates']['fin'] = $saisie->getFinAbs('U');
		}
	  } else {
		// On met à jour la date et l'heure de début et de fin
		$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates'] = Array('debut' => $saisie->getDebutAbs('U'), 'fin' => $saisie->getFinAbs('U'));
	  }

	  if ($traitement->getAbsenceEleveType() != Null) {
		// On met à jour le type
		$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['type'] = $traitement->getAbsenceEleveType()->getNom();
	  } else {
		 $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['type'] = 'Non défini';
	  }

	  if ($traitement->getAbsenceEleveMotif() != Null) {
		// On met à jour le motif
		$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['motif'] = $traitement->getAbsenceEleveMotif()->getNom();
	  } else {
		$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['motif'] = '-';
	  }

	  if ($traitement->getAbsenceEleveJustification() != Null) {
		// On met à jour la justification
		$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['justification'] = $traitement->getAbsenceEleveJustification()->getNom();
	  } else {
		$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['justification'] = '-';
	  }

	  if ($saisie->getCommentaire() !== '') {
		// On met à jour le commentaire
		  $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['commentaires'][] = $saisie->getCommentaire();
	  }

	  // On récupère le style css
	  $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['type_css'] = $type_css;
	}
  } else {
	// La saisie n'est pas traitée, on stocke les infos
	if (!isset($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees'])) {
	  // on avait pas de saisie de ce type pour ce jour
	  $donnees[$eleve_id]['nbre_lignes_total']++;
	}

	// On ajoute la saisie
	$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['saisies'][] = $saisie->getId(); 
	// On met à jour le type   
	$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['type'] = 'Non traitée(s)';
	// On met à jour le motif
	$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['motif'] = '-';
	// On met à jour la justification
	$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['justification'] = '-';
	// On met à jour la date
	if (isset($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates'])) {
	  // La date existe déjà
	  if ($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates']['debut'] > $saisie->getDebutAbs('U')) {
		// On met à jour la date et l'heure de début au besoin
		$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates']['debut'] = $saisie->getDebutAbs('U');
	  }
	  if ($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates']['fin'] < $saisie->getFinAbs('U')) {
		// On met à jour la date et l'heure de fin au besoin
		$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates']['fin'] = $saisie->getFinAbs('U');
	  }
	} else {
	  // La date n'existe pas
	  $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates'] = Array('debut' => $saisie->getDebutAbs('U'), 'fin' => $saisie->getFinAbs('U'));
	}

	// On met à jour le commentaire
	if ($saisie->getCommentaire() !== '') {
		$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['commentaires'][] = $saisie->getCommentaire();
	}

	// On récupère le style css
	$donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['type_css'] = $type_css;
  }        
}  

// inclusion des éléments Dojo
$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dojo.parser");
    dojo.require("dijit.form.Button");    
    dojo.require("dijit.form.Form");
    dojo.require("dijit.form.CheckBox");
    dojo.require("dijit.form.DateTextBox");    
    dojo.require("dijit.form.Select");
    dojo.require("dijit.form.NumberTextBox");
    dojo.require("dijit.form.TextBox");
    </script>';

//debug_var();
?>


<form dojoType="dijit.form.Form" 
	  id="dates_saisies" 
	  name="dates_saisies" 
	  action="visu_eleve.php?ele_login=<?php echo $eleve->getLogin(); ?>&onglet=absences" 
	  method="post">
  <h2>Bilan des saisies du
    <input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve_debut" name="date_absence_eleve_debut" value="<?php echo $dt_date_absence_eleve_debut->format('Y-m-d')?>" />
    au               
    <input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve_fin" name="date_absence_eleve_fin" value="<?php echo $dt_date_absence_eleve_fin->format('Y-m-d')?>" />
    <button type="submit"  style="font-size:12px" dojoType="dijit.form.Button" name="affichage" value="date">
	  Valider
	</button>
  </h2>
</form>

<form dojoType="dijit.form.Form" 
	  style="display:inline-block;"
	  id="annee" 
	  action="visu_eleve.php?ele_login=<?php echo $eleve->getLogin(); ?>&onglet=absences" 
	  method="post">
  <p>
	<input type="hidden" name="date_absence_eleve_debut" value="<?php echo $debutAnnee; ?>" />
	<input type="hidden" name="date_absence_eleve_fin" value="<?php echo $finAnnee; ?>" />
  </p>
 <button type="submit"  style="font-size:12px" dojoType="dijit.form.Button" name="affichage" value="date">
	  Année
  </button>
</form>

<?php
// Boucle sur les périodes
$i=0;
foreach($eleve->getPeriodeNotes() as $periode_note) {
?>
<form dojoType="dijit.form.Form" 
	  style="display:inline-block;"
	  id="periode_<?php echo $i; ?>" 
	  action="visu_eleve.php?ele_login=<?php echo $eleve->getLogin(); ?>&onglet=absences" 
	  method="post">
  <p>
	<input type="hidden" name="date_absence_eleve_debut" value="<?php echo $periode_note->getDateDebut("d-m-Y"); ?>" />
	<input type="hidden" name="date_absence_eleve_fin" value="<?php echo $periode_note->getDateFin("d-m-Y"); ?>" />
  </p>
 <button type="submit"  style="font-size:12px;" dojoType="dijit.form.Button" name="affichage" value="date">
	  <?php echo $periode_note->getNomPeriode(); ?>
  </button>
</form>
  
<?php
  $i++;
  
}
?>


<div id="sortie_ecran">
  <table class="sortable resizable" 
		 id="saisie" 
		 style="border:1px; cellspacing:0; text-align: center; width:100%; background-color: #e5e3e1;"
		 >
	<tr >
	  <th class="number" title='cliquez pour trier sur la colonne'>
		N°
	  </th>
	  <th title='cliquez pour trier sur la colonne' >
		Saisies
	  </th>
	  <th title='cliquez pour trier sur la colonne'>
		Type
	  </th>
	  <th title='cliquez pour trier sur la colonne'>
		Motif
	  </th>
	  <th title='cliquez pour trier sur la colonne'>
		Justification
	  </th>
	  <th title='cliquez pour trier sur la colonne'>
		Commentaire(s)
	  </th>
	</tr>

<?php
$ligne = 0;
foreach ($donnees as $id => $eleve) {
    if(!isset($eleve['infos_saisies'])){
        continue;
    }
    foreach ($eleve['infos_saisies'] as $type_tab=>$value2) {
        foreach ($value2 as $journee) {
            foreach ($journee as $key => $value) {                
                $style=$value['type_css'];
?>
	<tr>
	  <?php $ligne++; ?>

	  
	  <td  align="center" class="<?php echo $style; ?>">
		<?php echo $ligne; ?>
	  </td>
	  <td  align="center" class="<?php echo $style; ?>">
		<?php echo getDateDescription($value['dates']['debut'], $value['dates']['fin']) ; ?>
	  </td>
	  
	  <td align="center" class="<?php echo $style; ?>">
		  <?php if ($value['type'] !== 'Non traitée(s)') {
                    $class = '';
                    if ($value['type'] == 'Non défini') {
                        $class = 'orange';
                    } ?>
		<span class="<?php echo $class; ?>"><?php echo $value['type']; ?></span>
		  <?php }else { ?>
		<span class="orange"><?php echo $value['type']; ?></span>
          <?php } ?>
	  </td>
	  
	  <td class="<?php echo $style; ?>">
		<?php echo $value['motif']; ?>
	  </td>
	  
	  <td class="<?php echo $style; ?>">
		<?php echo $value['justification']; ?>
	  </td>
	  
	  <td class="<?php echo $style; ?>">
<?php if (isset($value['commentaires'])) {
  $besoin_echo_virgule = false;
  foreach ($value['commentaires'] as $commentaire) {
	if ($besoin_echo_virgule) {
	  echo ', ';
	}
	echo $commentaire;
	$besoin_echo_virgule = true;
  }
} ?>
	  </td>
	</tr>
<?php
            }
        }
    }
}
?>
	
  </table>
  </div>