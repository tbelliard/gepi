<?php
/**
 *
 *
 * Copyright 2010-2014 Josselin Jacquard - Bouguin Régis
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activé.");
}

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
    die("acces interdit");
}

include('include_requetes_filtre_de_recherche.php');

include('include_pagination.php');

$affichage = isset($_POST["affichage"]) ? $_POST["affichage"] :(isset($_GET["affichage"]) ? $_GET["affichage"] : NULL);
$menu = isset($_POST["menu"]) ? $_POST["menu"] :(isset($_GET["menu"]) ? $_GET["menu"] : Null);
$imprime = isset($_POST["imprime"]) ? $_POST["imprime"] :(isset($_GET["imprime"]) ? $_GET["imprime"] : Null);

$ne_pas_afficher_traitements_saisies_rattachees=isset($_POST["ne_pas_afficher_traitements_saisies_rattachees"]) ? $_POST["ne_pas_afficher_traitements_saisies_rattachees"] : "n";

//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
if(!$menu){
   $titre_page = "Les absences"; 
}
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";

$query = AbsenceEleveTraitementQuery::create();
if (isFiltreRechercheParam('filter_traitement_id')) {
    $query->filterById(getFiltreRechercheParam('filter_traitement_id'));
}
if (isFiltreRechercheParam('filter_utilisateur')) {
    $query->useUtilisateurProfessionnelQuery()->filterByNom('%'.getFiltreRechercheParam('filter_utilisateur').'%', Criteria::LIKE)->endUse();
}
if (isFiltreRechercheParam('filter_eleve')) {
    $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useEleveQuery()
	    ->filterByNomOrPrenomLike(getFiltreRechercheParam('filter_eleve'))
	    ->endUse()->endUse()->endUse();
}

// filtre classe
// $classe = ClasseQuery::create()->filterByNom("6 D")->findOne();
//$id_classe = 14;
//$classe = ClasseQuery::create()->findPk($id_classe);
if (isFiltreRechercheParam('filter_classe')) {
/*
echo "<pre>";
print_r(getFiltreRechercheParam('filter_classe'));
echo "</pre>";
*/
    if((is_array(getFiltreRechercheParam('filter_classe')))&&(in_array('SANS',getFiltreRechercheParam('filter_classe')))) {
	   $_SESSION['filtre_recherche']['filter_classe']=array('SANS');
    } else {
	  $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useEleveQuery()
		 ->useJEleveClasseQuery()->filterByIdClasse(getFiltreRechercheParam('filter_classe'))->endUse()
		 ->endUse()->endUse()->endUse();		 
    }
}


if (isFiltreRechercheParam('filter_type')) {
    if (getFiltreRechercheParam('filter_type') == 'SANS') {
	$query->filterByATypeId(null);
    } else {
	$query->filterByATypeId(getFiltreRechercheParam('filter_type'));
    }
}
if (isFiltreRechercheParam('filter_motif')) {
    if (getFiltreRechercheParam('filter_motif') == 'SANS') {
	$query->filterByAMotifId(null);
    } else {
	$query->filterByAMotifId(getFiltreRechercheParam('filter_motif'));
    }
}
if (isFiltreRechercheParam('filter_justification')) {
    if (getFiltreRechercheParam('filter_justification') == 'SANS') {
	$query->filterByAJustificationId(null);
    } else {
	$query->filterByAJustificationId(getFiltreRechercheParam('filter_justification'));
    }
}
if (isFiltreRechercheParam('filter_date_creation_traitement_debut_plage')) {
    $date_creation_traitement_debut_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_creation_traitement_debut_plage')));
    $query->filterByCreatedAt($date_creation_traitement_debut_plage, Criteria::GREATER_EQUAL);
}
if (isFiltreRechercheParam('filter_date_creation_traitement_fin_plage')) {
    $date_creation_traitement_fin_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_creation_traitement_fin_plage')));
    $query->filterByCreatedAt($date_creation_traitement_fin_plage, Criteria::LESS_EQUAL);
}
if (isFiltreRechercheParam('filter_date_modification')) {
    $query->where('AbsenceEleveTraitement.CreatedAt != AbsenceEleveTraitement.UpdatedAt');
}
if (isFiltreRechercheParam('filter_statut_notification')) {
    if (getFiltreRechercheParam('filter_statut_notification') == 'SANS') {
	$query->leftJoin('AbsenceEleveTraitement.AbsenceEleveNotification');
	$query->where('AbsenceEleveNotification.Id is null');
    } else {
	$query->useAbsenceEleveNotificationQuery()->filterByStatutEnvoi(getFiltreRechercheParam('filter_statut_notification'))->endUse();
    }
}
if (isFiltreRechercheParam('filter_manqement_obligation')) {
    $query->filterByManquementObligationPresence(getFiltreRechercheParam('filter_manqement_obligation')=='y');
}

if (getFiltreRechercheParam('order') == "asc_id") {
    $query->orderBy('Id', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_id") {
    $query->orderBy('Id', Criteria::DESC);
} else if (getFiltreRechercheParam('order') == "asc_utilisateur") {
    $query->useUtilisateurProfessionnelQuery()->orderBy('Nom', Criteria::ASC)->endUse();
} else if (getFiltreRechercheParam('order') == "des_utilisateur") {
    $query->useUtilisateurProfessionnelQuery()->orderBy('Nom', Criteria::DESC)->endUse();
} else if (getFiltreRechercheParam('order') == "asc_eleve") {
    $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useEleveQuery()->orderBy('Nom', Criteria::ASC)->orderBy('Prenom', Criteria::ASC)->orderBy('Login', Criteria::ASC)->endUse()->endUse()->endUse();
} else if (getFiltreRechercheParam('order') == "des_eleve") {
    $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useEleveQuery()->orderBy('Nom', Criteria::DESC)->orderBy('Prenom', Criteria::ASC)->orderBy('Login', Criteria::ASC)->endUse()->endUse()->endUse();
} else if (getFiltreRechercheParam('order') == "asc_classe") {
    $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useClasseQuery()->orderBy('NomComplet', Criteria::ASC)->endUse()->endUse()->endUse();
} else if (getFiltreRechercheParam('order') == "des_classe") {
    $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useClasseQuery()->orderBy('NomComplet', Criteria::DESC)->endUse()->endUse()->endUse();
} else if (getFiltreRechercheParam('order') == "asc_type") {
    $query->orderBy('ATypeId', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_type") {
    $query->orderBy('ATypeId', Criteria::DESC);
} else if (getFiltreRechercheParam('order') == "asc_motif") {
    $query->orderBy('AMotifId', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_motif") {
    $query->orderBy('AMotifId', Criteria::DESC);
} else if (getFiltreRechercheParam('order') == "asc_justification") {
    $query->orderBy('AJustificationId', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_justification") {
    $query->orderBy('AJustificationId', Criteria::DESC);
} else if (getFiltreRechercheParam('order') == "asc_date_creation") {
    $query->orderBy('CreatedAt', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_date_creation") {
    $query->orderBy('CreatedAt', Criteria::DESC);
} else if (getFiltreRechercheParam('order') == "asc_date_modification") {
    $query->orderBy('UpdatedAt', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_date_modification") {
    $query->orderBy('UpdatedAt', Criteria::DESC);
} else if (getFiltreRechercheParam('order') == "asc_notification") {
    $query->leftJoinAbsenceEleveNotification()->orderBy('AbsenceEleveNotification.StatutEnvoi', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_notification") {
    $query->leftJoinAbsenceEleveNotification()->orderBy('AbsenceEleveNotification.StatutEnvoi', Criteria::DESC);
}

$query->distinct();
$traitements_col = $query->paginate($page_number, $item_per_page);

$nb_pages = (floor($traitements_col->getNbResults() / $item_per_page) + 1);
if ($page_number > $nb_pages) {
    $page_number = $nb_pages;
}
$results = $traitements_col->getResults();

if ($affichage == 'tableur') {
    include_once 'lib/function.php';
    // load the TinyButStrong libraries    
	include_once('../tbs/tbs_class.php'); // TinyButStrong template engine
    
    //include_once('../tbs/plugins/tbsdb_php.php');
    $TBS = new clsTinyButStrong; // new instance of TBS
    include_once('../tbs/plugins/tbs_plugin_opentbs.php');
    $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

    // Load the template
    $extraction_traitement=repertoire_modeles('absence_extraction_traitements.ods');
    $TBS->LoadTemplate($extraction_traitement, OPENTBS_ALREADY_UTF8);

    $titre = 'Extrait des traitement d\'absences';

    $TBS->MergeField('titre', $titre);

    $traitement_array_avec_data = Array();
    foreach ($results as $traitement) {
        if(isset($_POST['envoye_depuis_liste_traitements'])) {
            if((isset($_POST['liste_traitements_id_traitement']))&&(in_array($traitement->getPrimaryKey(), $_POST['liste_traitements_id_traitement']))) {
                $extraire_ce_traitement="y";
            }
            else {
                $extraire_ce_traitement="n";
            }
        }
        else {
            $extraire_ce_traitement="y";
        }

	  if($extraire_ce_traitement=="y") {
		  $traitement_data = Array();

		  $traitement_data['traitement'] = $traitement;

		  if ($traitement->getUtilisateurProfessionnel() != null) {
		      $traitement_data['utilisateur'] = $traitement->getUtilisateurProfessionnel()->getCivilite().' '.$traitement->getUtilisateurProfessionnel()->getNom();
		  }

		  $eleve_col = new PropelObjectCollection();
		  foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
		      if ($saisie->getEleve() != null) {
		          $eleve_col->add($saisie->getEleve());
		      }
		  }
		  $traitement_data['eleve_str'] = '';
		      // Ajout: 20160914
		$traitement_data['eleve_nom_str']="";
		$traitement_data['eleve_prenom_str']="";
		  $traitement_data['eleve_classe_str'] = '';
		  foreach ($eleve_col as $eleve) {
		      if (!$eleve_col->isFirst()) {
		          $traitement_data['eleve_str'] .= '; ';
		          $traitement_data['eleve_nom_str'] .= '; ';
		          $traitement_data['eleve_prenom_str'] .= '; ';
		          $traitement_data['eleve_classe_str'] .= '; ';
		      }
		      $traitement_data['eleve_str'] .= ($eleve->getCivilite().' '.$eleve->getNom().' '.$eleve->getPrenom());
		      // Ajout: 20160914
		      $traitement_data['eleve_nom_str'] .= $eleve->getNom();
		      $traitement_data['eleve_prenom_str'] .= $eleve->getPrenom();
		      if($eleve->getClasse()!=null) {
		        $traitement_data['eleve_classe_str'] .= $eleve->getClasse()->getNom();
		      }
		  }

		  $traitement_data['saisie_str'] = '';
		  foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
		      $traitement_data['saisie_str'] .= $saisie->getDescription().'; ';
		  }

		// 20160914: La récup de la classe associée à la saisie a l'air d'échouer
		  $classe_col = new PropelObjectCollection();
		  foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
		      if ($saisie->getClasse() != null) {
		          $classe_col->add($saisie->getClasse());
		      }
		  }
		  $traitement_data['classe_str'] = '';
		  foreach ($classe_col as $classe) {
		      $traitement_data['classe_str'] .= $classe->getNom().'; ';
		  }

		  if ($traitement->getAbsenceEleveMotif() != null) {
		      $traitement_data['motif_str'] = $traitement->getAbsenceEleveMotif()->getNom();
		  } else {
		      $traitement_data['motif_str'] = '';
		  }

		  if ($traitement->getAbsenceEleveJustification() != null) {
		      $traitement_data['justification_str'] = $traitement->getAbsenceEleveJustification()->getNom();
		  } else {
		      $traitement_data['justification_str'] = '';
		  }
		  
		  $traitement_data['notification_str'] = '';
		  foreach ($traitement->getAbsenceEleveNotifications() as $notification) {
		      $traitement_data['notification_str'] .= $notification->getDescription().'; ';
		  }

		  $traitement_data['creation_str'] = strftime("%a %d/%m/%Y %H:%M", $traitement->getCreatedAt('U'));
		  $traitement_data['modification_str'] = strftime("%a %d/%m/%Y %H:%M", $traitement->getUpdatedAt('U'));

		  $traitement_array_avec_data[] = $traitement_data;
	  }
    }


    $TBS->MergeBlock('traitement_col', $traitement_array_avec_data);

    // Output as a download file (some automatic fields are merged here)
    $nom_fichier = 'extrait_traitement_';
    $now = new DateTime();
    $nom_fichier .=  $now->format("d_m_Y").'.ods';
    $TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, $nom_fichier);
} elseif ('lot' == $imprime) {
	include 'lib/traitements_vers_imprime_lot.php';
	
}


//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
//debug_var();
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

if(!$menu){
    include('menu_abs2.inc.php');
}

echo "<div class='css-panes' style='background-color:#ebedb5;' id='containDiv' style='overflow : none; float : left; margin-top : -1px; border-width : 1px;'>\n";

echo '<form method="post" action="liste_traitements.php" id="liste_traitements">';

// 20150404
echo "<input type='hidden' name='envoye_depuis_liste_traitements' value='y' />";

echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
  echo "<p>";
  
if ($traitements_col->haveToPaginate()) {
    echo "Page ";
    echo '<input type="submit" name="page_deplacement" value="-"/>';
    echo '<input type="text" name="page_number" size="1" value="'.$page_number.'"/>';
    echo '<input type="submit" name="page_deplacement" value="+"/> ';
    echo "sur ".$nb_pages." page(s) ";
    echo "| ";
}
echo "Voir ";
echo '<input type="text" name="item_per_page" size="1" value="'.$item_per_page.'"/>';
echo "par page&nbsp;&nbsp;&nbsp;";
echo '<button type="submit">Rechercher</button>';
echo '<button type="submit" name="reinit_filtre" value="y" >Reinitialiser les filtres</button> ';
echo '<button type="submit" name="affichage" value="tableur" >Exporter au format ods</button> ';
?>
<button type="submit" name="imprime" value="lot" title="Crée un courrier pour chaque élève de la liste affichée ci-dessous" >
	Courriers par lot
</button>

<input type='checkbox' name='ne_pas_afficher_traitements_saisies_rattachees' id='ne_pas_afficher_traitements_saisies_rattachees' value='y' <?php
	if((isset($ne_pas_afficher_traitements_saisies_rattachees))&&($ne_pas_afficher_traitements_saisies_rattachees=="y")) {
		echo "checked ";
	}
?>/><label for='ne_pas_afficher_traitements_saisies_rattachees'>Ne pas afficher les traitements/saisies rattachés</label>
<?php
echo "</p>";

// 20150404
//echo '<table id="table_liste_absents" class="tb_absences joss_alt" style="border-spacing:0; width:100%">';
echo '<table id="table_liste_absents" class="tb_absences" style="border-spacing:0; width:100%">';

echo '<thead>';
echo '<tr>';

$order = getFiltreRechercheParam('order');
//en tete filtre id
echo '<th>';
//echo '<nobr>';
echo '<input type="hidden" name="order" value="'.$order.'" />'; 
echo '<span style="white-space: nowrap;"> ';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_id") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_id" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_id") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_id" onclick="this.form.order.value = this.value"/>';
//echo '</nobr> ';
echo '</span>';
echo '<br/> ';
echo 'N°';
echo '<input type="text" name="filter_traitement_id" value="'.getFiltreRechercheParam('filter_traitement_id').'" size="3"/>';
echo '</th>';

//en tete filtre utilisateur
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Utilisateur';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_utilisateur") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_utilisateur" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_utilisateur") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_utilisateur" onclick="this.form.order.value = this.value"/>';
//echo '</nobr>';
echo '</span>';
echo '<br /><input type="text" name="filter_utilisateur" value="'.getFiltreRechercheParam('filter_utilisateur').'" size="12"/>';
echo '</th>';

//en tete filtre eleve
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Élève';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_eleve") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_eleve" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_eleve") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_eleve" onclick="this.form.order.value = this.value"/>';
//echo '</nobr>';
echo '</span>';
echo '<br /><input type="text" name="filter_eleve" value="'.getFiltreRechercheParam('filter_eleve').'" size="8"/>';



	//on affiche une boite de selection avec les classe
	if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
		$classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
	} else {
		$classe_col = $utilisateur->getClasses();
	}
	if (!$classe_col->isEmpty()) {
		echo '<br />';
		// echo '<input type="hidden" name="type_selection" value="id_classe"/>';
		echo ("<select multiple name='filter_classe[]' onchange='submit()' style='width:100%' title='Sélectionnez une ou plusieurs classes'>");
		// echo "<option value='SANS'>choisissez une classe</option>\n";
		echo "<option value='SANS'>Toutes les classes</option>\n";
		foreach ($classe_col as $classe) {
			echo "<option value='".$classe->getId()."'";
			if (isFiltreRechercheParam('filter_classe') && (getFiltreRechercheParam('filter_classe') != "SANS")) {
			   if(is_array(getFiltreRechercheParam('filter_classe'))) {
			      if ((in_array($classe->getId(), getFiltreRechercheParam('filter_classe')))) {
				     echo " selected='selected' ";
			      }
			   }
			   else {
			      if ($classe->getId()==getFiltreRechercheParam('filter_classe')) {
				     echo " selected='selected' ";
			      }
			   }
			}
			
			echo ">";
			echo $classe->getNom();
			echo "</option>\n";
		}
		echo "</select>&nbsp;";
	} else {
		echo 'Aucune classe avec élève affecté n\'a été trouvée';
	}

echo '</th>';

//en tete filtre saisies
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo 'Saisies';
echo '</span>';
//echo '</nobr>';
echo '</th>';

//en tete type d'absence
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo 'Type';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_type") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_type" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_type") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_type" onclick="this.form.order.value = this.value"/>';
echo '</span>';
//echo '</nobr>';
echo '<br />';
echo ("<select name=\"filter_type\" onchange='submit()'>");
echo "<option value=''></option>\n";
echo "<option value='SANS'";
if (getFiltreRechercheParam('filter_type') == 'SANS') echo " selected='selected' ";
echo ">SANS TYPE</option>\n";
foreach (AbsenceEleveTypeQuery::create()->orderBySortableRank()->find() as $type) {
	echo "<option value='".$type->getId()."'";
	if (getFiltreRechercheParam('filter_type') === (string) $type->getId()) echo " selected='selected' ";
	echo ">";
	echo $type->getNom();
	echo "</option>\n";
}
echo "</select>";
echo '</th>';

//en tete filtre manqement_obligation
echo '<th>';
echo ("<select name=\"filter_manqement_obligation\" onchange='submit()'>");
echo "<option value=''";
if (!isFiltreRechercheParam('filter_manqement_obligation')) {echo " selected='selected'";}
echo "></option>\n";
echo "<option value='y' ";
if (getFiltreRechercheParam('filter_manqement_obligation') == 'y') {echo " selected='selected'";}
echo ">oui</option>\n";
echo "<option value='n' ";
if (getFiltreRechercheParam('filter_manqement_obligation') == 'n') {echo " selected='selected'";}
echo ">non</option>\n";
echo "</select>";
echo '<br/>Manquement obligation scolaire (bulletin)';
echo '</th>';

//en tete filtre sous_responsabilite_etablissement
echo '<th>';
//echo '<input type="checkbox" value="y" name="filter_sous_responsabilite_etablissement" onchange="submit()"';
//if (isFiltreRechercheParam('filter_sous_responsabilite_etablissement') && getFiltreRechercheParam('filter_sous_responsabilite_etablissement') == 'y') {echo "checked='checked'";}
//echo '/><br/>sous resp. etab.';
echo 'Sous resp. étab.';
echo '</th>';
//en tete motif d'absence
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_motif") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_motif" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_motif") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_motif" onclick="this.form.order.value = this.value"/>';
echo '</span>';
echo '<br />';
echo 'Motif';
echo '<br />';
echo ("<select name=\"filter_motif\" onchange='submit()'>");
echo "<option value=''></option>\n";
echo "<option value='SANS'";
if (getFiltreRechercheParam('filter_motif') == 'SANS') echo " selected='selected' ";
echo ">";
echo 'SANS MOTIF';
echo "</option>\n";
foreach (AbsenceEleveMotifQuery::create()->orderByRank()->find() as $motif) {
	echo "<option value='".$motif->getId()."'";
	if (getFiltreRechercheParam('filter_motif') === (string) $motif->getId()) echo " selected='selected' ";
	echo ">";
	echo $motif->getNom();
	echo "</option>\n";
}
echo "</select>";
echo '</th>';
//en tete justification d'absence
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_justification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_justification" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_justification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_justification" onclick="this.form.order.value = this.value"/>';
echo '</span>';
echo '<br />';
echo 'Justification';
echo '<br />';
echo ("<select name=\"filter_justification\" onchange='submit()'>");
echo "<option value=''></option>\n";
echo "<option value='SANS'";
if (getFiltreRechercheParam('filter_justification') == 'SANS') echo " selected='selected' ";
echo ">";
echo 'SANS JUSTIFICATION';
echo "</option>\n";
foreach (AbsenceEleveJustificationQuery::create()->orderByRank()->find() as $justification) {
	echo "<option value='".$justification->getId()."'";
	if (getFiltreRechercheParam('filter_justification') === (string) $justification->getId()) echo " selected='selected' ";
	echo ">";
	echo $justification->getNom();
	echo "</option>\n";
}
echo "</select>";
echo '</th>';

//en tete notification d'absence
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_notification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_notification" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_notification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_notification" onclick="this.form.order.value = this.value"/>';
echo '</span>';
echo '<br/>';
echo 'Notification';
echo '<br />';
echo ("<select name=\"filter_statut_notification\" onchange='submit()'>");
echo "<option value=''></option>\n";
echo "<option value='SANS'";
if (getFiltreRechercheParam('filter_statut_notification') == 'SANS') echo " selected='selected' ";
echo ">";
echo 'SANS NOTIFICATION';
echo "</option>\n";
$i = 0;
foreach (AbsenceEleveNotificationPeer::getValueSet(AbsenceEleveNotificationPeer::STATUT_ENVOI) as $status) {
    echo "<option value='$status'";
    if (getFiltreRechercheParam('filter_statut_notification') === $status) {
	echo 'selected';
    }
    echo ">".$status."</option>\n";
}
echo "</select>";
echo '</th>';

//en tete filtre date creation
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo 'Date création';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_date_creation") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_creation" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_date_creation") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_creation" onclick="this.form.order.value = this.value"/>';
echo '</span>';
//echo '</nobr>';
echo '<br />';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo 'Entre : <input size="13" id="filter_date_creation_traitement_debut_plage" name="filter_date_creation_traitement_debut_plage" value="';
if (isFiltreRechercheParam('filter_date_creation_traitement_debut_plage')) {echo getFiltreRechercheParam('filter_date_creation_traitement_debut_plage');}
echo '" onKeyDown="clavier_date2(this.id,event);" AutoComplete="off" />&nbsp;';
echo '<img id="trigger_filter_date_creation_traitement_debut_plage" src="../images/icons/calendrier.gif" alt="" />';
echo '</span>';
//echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_creation_traitement_debut_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_creation_traitement_debut_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '<br />';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo 'Et : <input size="13" id="filter_date_creation_traitement_fin_plage" name="filter_date_creation_traitement_fin_plage" value="';
if (isFiltreRechercheParam('filter_date_creation_traitement_fin_plage') != null) {echo getFiltreRechercheParam('filter_date_creation_traitement_fin_plage');}
echo '" onKeyDown="clavier_date2(this.id,event);" AutoComplete="off" />&nbsp;';
echo '<img id="trigger_filter_date_creation_traitement_fin_plage" src="../images/icons/calendrier.gif" alt="" />';
echo '</span>';
//echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_creation_traitement_fin_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_creation_traitement_fin_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '</th>';

//en tete filtre date modification
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo '';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_date_modification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_modification" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_date_modification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_modification" onclick="this.form.order.value = this.value"/>';
echo '</span>';
//echo '</nobr> ';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo '<input type="hidden" value="y" name="filter_checkbox_posted"/>';
echo '<input type="checkbox" value="y" name="filter_date_modification" onchange="submit()"';
if (isFiltreRechercheParam('filter_date_modification') != null && getFiltreRechercheParam('filter_date_modification') == 'y') {echo "checked";}
echo '/> Modifié';
echo '</span>';
//echo '</nobr>';
echo '</th>';

//en tete commentaire
echo "
	<th>Com.</th>
</tr>
</thead>
<tbody>";

$ligne_traitement=array();
$cpt_traitement=0;
foreach ($results as $traitement) {
	$ligne_traitement[$cpt_traitement]="";

	//$traitement = new AbsenceEleveTraitement();
	if ($results->getPosition() %2 == '1') {
		$background_couleur="rgb(220, 220, 220);";
	} else {
		$background_couleur="rgb(210, 220, 230);";
	}

	//======================================
	$ligne_traitement[$cpt_traitement].="
	<tr style='background-color :$background_couleur'>\n";

	//donnees id
		$ligne_traitement[$cpt_traitement].="
		<td title=\"Voir/modifier le traitement n°".$traitement->getPrimaryKey()."\">
			<input type='hidden' name='liste_traitements_id_traitement[]' value='".$traitement->getPrimaryKey()."' />
			<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey();
	if($menu){
			$ligne_traitement[$cpt_traitement].="&menu=false";
	} 
	$ligne_traitement[$cpt_traitement].="' style='display: block; height: 100%;'> 
				".$traitement->getId()."
			</a>
		</td>";

	//======================================
	//donnees utilisateur
	$ligne_traitement[$cpt_traitement].="
		<td>
			<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'> ";
	if ($traitement->getUtilisateurProfessionnel() != null) {
		$ligne_traitement[$cpt_traitement].=$traitement->getUtilisateurProfessionnel()->getCivilite().' '.$traitement->getUtilisateurProfessionnel()->getNom();
	}
	$ligne_traitement[$cpt_traitement].="</a>
		</td>";

	//======================================
	//donnees eleve
	$ligne_traitement[$cpt_traitement].="
		<td>";
	$eleve_col = new PropelObjectCollection();
	foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
		if ($saisie->getEleve() != null) {
			$eleve_col->add($saisie->getEleve());
		}
	}
	$cpt_eleve_col=0;
	foreach ($eleve_col as $eleve) {
		/*
		echo "<pre>";
		print_r($eleve);
		echo "</pre>";
		*/
		$ligne_traitement[$cpt_traitement].="
			<table style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%; width:100%'>
				<tr style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>
					<td style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>
						<a href='liste_traitements.php?filter_eleve=".$eleve->getNom()."&order=asc_eleve' style='display: block; height: 100%;' title = 'Uniquement les absences de ".$eleve->getNom().' '.$eleve->getPrenom()."'> 
							".($eleve->getCivilite().' '.$eleve->getNom().' '.$eleve->getPrenom())."
						</a>";
		if($eleve->getClasse()!=NULL) {
			$ligne_traitement[$cpt_traitement].="
						<a href='liste_traitements.php?filter_classe[]=".$eleve->getClasse()->getId()."&order=asc_eleve' style='display: block; height: 100%;' title = 'Uniquement les absences de la classe ".$eleve->getClasse()->getNom()."'>
							".($eleve->getClasse()->getNom())."
						</a>";
		}
		if ($utilisateur->getAccesFicheEleve($eleve)) {
			$ligne_traitement[$cpt_traitement].="
						<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."&amp;onglet=responsables&amp;quitter_la_page=y' target='_blank'>
							 (voir fiche)
						 </a>";
		}
		$ligne_traitement[$cpt_traitement].="
					</td>
					<td style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>
						<a href='liste_traitements.php?filter_eleve=".$eleve->getNom()."&order=asc_eleve";
		if($menu){
			$ligne_traitement[$cpt_traitement].="&menu=false";
		} 
		$ligne_traitement[$cpt_traitement].="' style='display: block; height: 100%;'> ";
		if ((getSettingValue("active_module_trombinoscopes")=='y')) {
			$nom_photo = $eleve->getNomPhoto(1);
			$photos = $nom_photo;
			//if (($nom_photo != "") && (file_exists($photos))) {
			if (($nom_photo != NULL) && (file_exists($photos))) {
				$valeur = redimensionne_image_petit($photos);
				$ligne_traitement[$cpt_traitement].=' <img src="'.$photos.'" style="align:right; width:'.$valeur[0].'px; height:'.$valeur[1].'px;" alt="" title="" /> ';
			}
		}
		$ligne_traitement[$cpt_traitement].="
						</a>
					</td>
				</tr>
			</table>";
		$cpt_eleve_col++;
	}

	// Les saisies ont dû être supprimées.
	if($cpt_eleve_col==0) {
		$chaine_saisies_supprimees="";
		$sql="SELECT a_saisie_id FROM j_traitements_saisies WHERE a_traitement_id='".$traitement->getPrimaryKey()."';";
		//echo "$sql<br />";
		$res_saisies=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_saisies)>0) {
			// Vérifier que ce n'est pas un marqueur d'appel:
			$sql="SELECT 1=1 FROM a_saisies a_s, j_traitements_saisies jts WHERE jts.a_traitement_id='".$traitement->getPrimaryKey()."' AND a_s.id=jts.a_saisie_id AND a_s.eleve_id IS NOT NULL;";
			//echo "$sql<br />";
			$test_marqueur_appel=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test_marqueur_appel)>0) {
				$ligne_traitement[$cpt_traitement].="
				<span style='color:red' title=\"Saisie supprimée.\">";
				$cpt_saisie_cachees=0;
				while($lig_saisie=mysqli_fetch_object($res_saisies)) {
					if($cpt_saisie_cachees>0) {
						$ligne_traitement[$cpt_traitement].=" - ";
						$chaine_saisies_supprimees.=" - ";
					}
					$chaine_saisies_supprimees.=" <a href='visu_saisie.php?id_saisie=$lig_saisie->a_saisie_id' title='Voir la saisie supprimée n°$lig_saisie->a_saisie_id' style='color:red'>$lig_saisie->a_saisie_id</a>";

					$saisie_suppr = AbsenceEleveSaisieQuery::create()->includeDeleted()->findPk($lig_saisie->a_saisie_id);
					// Problème avec les marqueurs d'appel
					//if ($saisie_suppr != null) {
					if (($saisie_suppr != null)&&($saisie_suppr->getEleveId()!=null)) {
						$ligne_traitement[$cpt_traitement].=$saisie_suppr->getEleve()->getCivilite().' '.$saisie_suppr->getEleve()->getNom().' '.$saisie_suppr->getEleve()->getPrenom();
						if ($utilisateur->getAccesFicheEleve($saisie_suppr->getEleve())) {
							$ligne_traitement[$cpt_traitement].="
				<br />
				<a href='../eleves/visu_eleve.php?ele_login=".$saisie_suppr->getEleve()->getLogin()."&amp;onglet=responsables&amp;quitter_la_page=y' target='_blank' style='color:red'> (voir fiche)</a>";
						}
					}


					$cpt_saisie_cachees++;
				}
				$ligne_traitement[$cpt_traitement].="</span>";
			}
		}
	}

	$ligne_traitement[$cpt_traitement].="
		</td>";

	//======================================
	$tab_traitement[$cpt_traitement]=$traitement->getPrimaryKey();

	//donnees saisies
	$ligne_traitement[$cpt_traitement].="
		<td>";
	if (!$traitement->getAbsenceEleveSaisies()->isEmpty()) {
		$ligne_traitement[$cpt_traitement].="
			<table style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%; min-width: 150px; width:100%'>";

		$tab_traitement_avec_plusieurs_saisies[$traitement->getPrimaryKey()]=array();
	}
	foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
		$ligne_traitement[$cpt_traitement].="
				<tr style='border-spacing:0px; border-style : solid; border-size : 1px; margin : 0px; padding : 0px; font-size:100%;'>
					<td style='border-spacing:0px; border-style : solid; border-size : 1px; çargin : 0px; padding-top : 3px; font-size:100%;'>
						<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."";
		if($menu){
			$ligne_traitement[$cpt_traitement].="&menu=false";
		} 
		$ligne_traitement[$cpt_traitement].="' style='display: block; height: 100%;'>
				".$saisie->getDescription()."
						</a>
					</td>
				</tr>";
		if(isset($tab_traitement_avec_plusieurs_saisies[$traitement->getPrimaryKey()])) {
			$tab_traitement_avec_plusieurs_saisies[$traitement->getPrimaryKey()][]=$saisie->getPrimaryKey();
		}

		//if ($traitement->getAbsenceEleveSaisies()->isEmpty()) {
			$tab_traitement_saisies[$cpt_traitement][]=$saisie->getPrimaryKey();
		//}
	}
	if (!$traitement->getAbsenceEleveSaisies()->isEmpty()) {
		$ligne_traitement[$cpt_traitement].="
			</table>";
	}

	// Les saisies ont dû être supprimées.
	if($cpt_eleve_col==0) {
		$ligne_traitement[$cpt_traitement].="
			<span style='color:red' title=\"Saisie supprimée.\">".$chaine_saisies_supprimees."</span>";
	}
	$ligne_traitement[$cpt_traitement].="
		</td>";
	//======================================

	//donnees type
	$ligne_traitement[$cpt_traitement].="
		<td>
			<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."";
	if($menu){
		$ligne_traitement[$cpt_traitement].="&menu=false";
	} 
	$ligne_traitement[$cpt_traitement].="' style='display: block; height: 100%; color: #330033'>";
	if ($traitement->getAbsenceEleveType() != null) {
		$ligne_traitement[$cpt_traitement].=$traitement->getAbsenceEleveType()->getNom();
	} else {
		$ligne_traitement[$cpt_traitement].="&nbsp;";
	}
	$ligne_traitement[$cpt_traitement].="</a>
		</td>
		<td>";
	if ($traitement->getManquementObligationPresence()) {
		$ligne_traitement[$cpt_traitement].="oui";
	} else {
		$ligne_traitement[$cpt_traitement].="non";
	}
		$ligne_traitement[$cpt_traitement].="
		</td>
		<td>";
	if ($traitement->getSousResponsabiliteEtablissement()) {
		$ligne_traitement[$cpt_traitement].="oui";
	} else {
		$ligne_traitement[$cpt_traitement].="non";
	}
	$ligne_traitement[$cpt_traitement].="
		</td>";

	//donnees motif
	$ligne_traitement[$cpt_traitement].="
		<td>";
	if ($traitement->getAbsenceEleveMotif() != null) {
		$ligne_traitement[$cpt_traitement].=$traitement->getAbsenceEleveMotif()->getNom();
	} else {
		$ligne_traitement[$cpt_traitement].="&nbsp;";
	}
	$ligne_traitement[$cpt_traitement].="</a>";

	//donnees justification
	$ligne_traitement[$cpt_traitement].="
		<td>
			<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."";
	if($menu){
		$ligne_traitement[$cpt_traitement].="&menu=false";
	}
	$ligne_traitement[$cpt_traitement].="' style='display: block; height: 100%; color: #330033'>\n";
	if ($traitement->getAbsenceEleveJustification() != null) {
		$ligne_traitement[$cpt_traitement].=$traitement->getAbsenceEleveJustification()->getNom();
	} else {
		$ligne_traitement[$cpt_traitement].="&nbsp;";
	}
	$ligne_traitement[$cpt_traitement].="</a>";
	$ligne_traitement[$cpt_traitement].="
		</td>";

	//donnees notification
	$ligne_traitement[$cpt_traitement].="
		<td>
			<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."";
	if($menu){
		$ligne_traitement[$cpt_traitement].="&menu=false";
	} 
	$ligne_traitement[$cpt_traitement].="' style='display: block; height: 100%; color: #330033'> </a>";
	if (count($traitement->getAbsenceEleveNotifications())){
		$ligne_traitement[$cpt_traitement].="
			<table style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%; min-width:150px; width: 100%;'>";
		foreach ($traitement->getAbsenceEleveNotifications() as $notification) {
			$ligne_traitement[$cpt_traitement].="
				<tr style='border-spacing:0px; border-style : solid; border-size : 1px; margin : 0px; padding : 0px; font-size:100%;'>
					<td style='border-spacing:0px; border-style : solid; border-size : 1px; çargin : 0px; padding-top : 3px; font-size:100%;'>
						<a href='visu_notification.php?id_notification=".$notification->getPrimaryKey();
			if($menu){
				$ligne_traitement[$cpt_traitement].="&menu=false";
			} 
			$ligne_traitement[$cpt_traitement].="' style='display: block; height: 100%;'>
				".$notification->getDescription().
						"</a>
					</td>
				</tr>";
		}
		$ligne_traitement[$cpt_traitement].="
			</table>";
	}
	$ligne_traitement[$cpt_traitement].="
		</td>
		<td>
			<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey();
	if($menu){
		$ligne_traitement[$cpt_traitement].="&menu=false";
	} 
	$ligne_traitement[$cpt_traitement].="' style='display: block; height: 100%; color: #330033'>
				".strftime("%a %d/%m/%Y %H:%M", $traitement->getCreatedAt('U')).
			"</a>
		</td>
		<td>
			<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey();
	if($menu){
		$ligne_traitement[$cpt_traitement].="&menu=false";
	} 
	$ligne_traitement[$cpt_traitement].="' style='display: block; height: 100%; color: #330033'>
				".strftime("%a %d/%m/%Y %H:%M", $traitement->getUpdatedAt('U'))."
			</a>
		</td>
		<td>
			<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."";
	if($menu){
		$ligne_traitement[$cpt_traitement].="&menu=false";
	} 
	$ligne_traitement[$cpt_traitement].="' style='display: block; height: 100%; color: #330033'>
				".$traitement->getCommentaire()."
				&nbsp;
			</a>
		</td>
	</tr>";
	$cpt_traitement++;
}

/*
echo "<div style='float:left; width:30%'>
\$tab_traitement<pre>";
print_r($tab_traitement);
echo "</pre>
</div>
<div style='float:left; width:30%'>
\$tab_traitement_saisies<pre>";
print_r($tab_traitement_saisies);
echo "</pre>
</div>
<div style='float:left; width:30%'>
\$tab_traitement_avec_plusieurs_saisies<pre>";
print_r($tab_traitement_avec_plusieurs_saisies);
echo "</pre>
</div>";
*/

if($ne_pas_afficher_traitements_saisies_rattachees=="n") {
	for($loop=0;$loop<count($ligne_traitement);$loop++) {
		echo $ligne_traitement[$loop];
	}
}
else {
	for($loop=0;$loop<count($ligne_traitement);$loop++) {
		if((array_key_exists($tab_traitement[$loop], $tab_traitement_avec_plusieurs_saisies))&&(count($tab_traitement_avec_plusieurs_saisies[$tab_traitement[$loop]])>1)) {
			echo $ligne_traitement[$loop];
		}
		else {
			$afficher_ligne="y";
			if(isset($tab_traitement_saisies[$loop])) {
				for($loop2=0;$loop2<count($tab_traitement_saisies[$loop]);$loop2++) {
					foreach($tab_traitement_avec_plusieurs_saisies as $current_traitement_englobant => $current_saisie_englobee) {
						if(count($current_saisie_englobee)>1) {
							for($loop3=0;$loop3<count($current_saisie_englobee);$loop3++) {
								if($tab_traitement_saisies[$loop][$loop2]==$current_saisie_englobee[$loop3]) {
									$afficher_ligne="n";
									break;
								}
							}
						}
					}
				}
			}

			if($afficher_ligne=="y") {
				echo $ligne_traitement[$loop];
			}
		}
	}
}

echo '</tbody>';
//echo '</tbody>';

echo '</table>';

echo '</form>';

echo "</div>\n";

require_once("../lib/footer.inc.php");

?>
