<?php
/**
 * Affichage des saisies de Abs2 dans la fiche élève
 * 
 */
$non_traitees=1;
$tri='';

//$type_extrait=(!isset($_POST['type_extrait'])) ? 1 : "";
$type_extrait="";
if(!isset($_POST['visu_eleve_abs2_is_posted'])) {
	if(isset($_SESSION['abs2_type_extrait'])) {
		$type_extrait=$_SESSION['abs2_type_extrait'];
	}
	else {
		$type_extrait=1; // filtrer par manquement aux obligations scolaires
	}
}
elseif(isset($_POST['type_extrait'])) {
	//$type_extrait=1;
	$type_extrait=$_POST['type_extrait'];
}
else {
	$type_extrait=1; // filtrer par manquement aux obligations scolaires
}

$_SESSION['abs2_type_extrait']=$type_extrait;

//debug_var();

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

// ===============================
if($_SESSION['statut']=='cpe') {
	$val_defaut_afficher_strictement_englobee="y";
}
else {
	$val_defaut_afficher_strictement_englobee="n";
}

if(!isset($_POST['visu_eleve_abs2_is_posted'])) {
	$afficher_strictement_englobee=isset($_POST['afficher_strictement_englobee']) ? $_POST['afficher_strictement_englobee'] : (isset($_SESSION['abs2_afficher_strictement_englobee']) ? $_SESSION['abs2_afficher_strictement_englobee'] : $val_defaut_afficher_strictement_englobee);
}
else {
	if(isset($_POST['afficher_strictement_englobee'])) {
		$afficher_strictement_englobee=$_POST['afficher_strictement_englobee'];
	}
	else {
		$afficher_strictement_englobee="n";
	}
}

$_SESSION['abs2_afficher_strictement_englobee']=$afficher_strictement_englobee;
// ===============================

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

//$afficher_strictement_englobee="y";
if($afficher_strictement_englobee!="y") {
  $strictement_englobee = false;
    foreach ($saisie->getAbsenceEleveSaisiesEnglobantes() as $saisie_englobante) {
        if ($saisie_englobante->getManquementObligationPresenceSpecifie_NON_PRECISE()) continue;
        if ($saisie_englobante->getDebutAbs(null) != $saisie->getDebutAbs(null) || $saisie_englobante->getFinAbs(null) != $saisie->getFinAbs(null)) {
            $strictement_englobee = true;
            break;
        }
    }
    if ($strictement_englobee) continue;
}

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
		  $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['commentaires'][] = stripslashes($saisie->getCommentaire());
	  }

	  if(trim($traitement->getCommentaire()) !== '') {
		  if((!isset($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['commentaires']))||(!in_array(stripslashes($traitement->getCommentaire()), $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['commentaires']))) {
			  $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['commentaires'][] = stripslashes($traitement->getCommentaire());
		  }
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
	<input type='checkbox' name='afficher_strictement_englobee' id='afficher_strictement_englobee' value='y' style='font-size:small;' onchange="maj_afficher_strictement_englobee()" <?php
		if($afficher_strictement_englobee=="y") {echo "checked ";}
	?>/><label for='afficher_strictement_englobee' style='font-size:small; font-variant: normal;'>Afficher les saisies englobées</label>
	<input type='checkbox' name='type_extrait' id='type_extrait' value='1' style='font-size:small;' onchange="maj_type_extrait()" <?php
		if($type_extrait!="") {echo "checked ";}
	?>/><label for='type_extrait' style='font-size:small; font-variant: normal;'>N'afficher que les manquements à l'obligation de présence</label>

  </h2>
	<input type="hidden" name="visu_eleve_abs2_is_posted" value="y" />
</form>

<form dojoType="dijit.form.Form" 
	  style="display:inline-block;"
	  id="annee" 
	  action="visu_eleve.php?ele_login=<?php echo $eleve->getLogin(); ?>&onglet=absences" 
	  method="post">
  <p>
	<input type="hidden" name="date_absence_eleve_debut" value="<?php echo $debutAnnee; ?>" />
	<input type="hidden" name="date_absence_eleve_fin" value="<?php echo $finAnnee; ?>" />
	<input type="hidden" name="visu_eleve_abs2_is_posted" value="y" />
	<input type="hidden" name="afficher_strictement_englobee" id="afficher_strictement_englobee_annee" value="<?php echo $afficher_strictement_englobee;?>" />
	<input type="hidden" name="date_absence_eleve_debut" value="<?php echo $debutAnnee; ?>" />
	<input type="hidden" name="type_extrait" id="type_extrait_annee" value="<?php echo $type_extrait; ?>" />
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
	<input type="hidden" name="visu_eleve_abs2_is_posted" value="y" />
	<input type="hidden" name="afficher_strictement_englobee" id="afficher_strictement_englobee_p_<?php echo ($i+1);?>" value="<?php echo $afficher_strictement_englobee;?>" />
	<input type="hidden" name="type_extrait" id="type_extrait_p_<?php echo ($i+1);?>" value="<?php echo $type_extrait; ?>" />
  </p>
 <button type="submit"  style="font-size:12px;" dojoType="dijit.form.Button" name="affichage" value="date">
	  <?php echo $periode_note->getNomPeriode(); ?>
  </button>
</form>
  
<?php
  $i++;
  
}
$nb_per=$i;
//echo "nb_per=$nb_per<br />";
?>

<script type='text/javascript'>
function maj_afficher_strictement_englobee() {
	if(document.getElementById('afficher_strictement_englobee').checked==true) {
		document.getElementById('afficher_strictement_englobee_annee').value='y';
		for(j=1;j<=<?php echo $nb_per;?>;j++) {
			document.getElementById('afficher_strictement_englobee_p_'+j).value='y';
		}
	}
	else {
		document.getElementById('afficher_strictement_englobee_annee').value='n';
		for(j=1;j<=<?php echo $nb_per;?>;j++) {
			document.getElementById('afficher_strictement_englobee_p_'+j).value='n';
		}
	}
}
maj_afficher_strictement_englobee();

function maj_type_extrait() {
	if(document.getElementById('type_extrait').checked==true) {
		document.getElementById('type_extrait_annee').value='1';
		for(j=1;j<=<?php echo $nb_per;?>;j++) {
			document.getElementById('type_extrait_p_'+j).value='1';
		}
	}
	else {
		document.getElementById('type_extrait_annee').value='';
		for(j=1;j<=<?php echo $nb_per;?>;j++) {
			document.getElementById('type_extrait_p_'+j).value='';
		}
	}
}
maj_type_extrait();
</script>


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
$jour_debut_ligne_precedente="";
$jour_debut_ligne_courante="";
$alt=1;
foreach ($donnees as $id => $eleve) {
    if(!isset($eleve['infos_saisies'])){
        continue;
    }

    foreach ($eleve['infos_saisies'] as $type_tab=>$value2) {
        foreach ($value2 as $journee) {
            foreach ($journee as $key => $value) {                
                $style=$value['type_css'];
                /*
                echo "<tr><td colspan='6' style='background-color:white; text-align:left;'><pre>";
                print_r($value);
                echo "</pre></td></tr>";
                */
?>
	<tr<?php
		$jour_debut_ligne_courante=strftime("%Y%m%d", $value['dates']['debut']);
		if($jour_debut_ligne_courante!=$jour_debut_ligne_precedente) {
			$alt=$alt*(-1);
		}
		$jour_debut_ligne_precedente=$jour_debut_ligne_courante;

		$style_ajout="";
		if($alt==1) {
			//echo " style='background-color:plum'";
			$style_ajout='" style="background-color:plum;';
		}
		$style.=$style_ajout;
	?>>
	  <?php $ligne++; ?>

	  
	  <td  align="center" class="<?php echo $style; ?>">
		<?php echo $ligne;?>
	  </td>
	  <td  align="center" class="<?php echo $style; ?>">
		<?php echo getDateDescription($value['dates']['debut'], $value['dates']['fin'], 'y') ; ?>
	  </td>
	  
	  <td align="center" class="<?php echo $style; ?>">
		  <?php
			$contenu_cellule=$value['type'];
			if ($value['type'] !== 'Non traitée(s)') {
				$class = '';
				if ($value['type'] == 'Non défini') {
					$class = 'orange';
				}
			}
			else {
				$class="orange";
			}

			if(($_SESSION['statut']=='cpe')&&(isset($value['saisies'][0]))) {
				$contenu_cellule="<a href='../mod_abs2/visu_saisie.php?id_saisie=".$value['saisies'][0]."' target='_blank'><span class='$class'>".$value['type']."</span></a>";
			}

			echo $contenu_cellule;
		  ?>
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
