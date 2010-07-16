<?php
/**
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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
$gabarit="y";

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

if ($utilisateur->getStatut()=="professeur" &&  getSettingValue("active_module_absence_professeur")!='y') {
    die("Le module n'est pas activé.");
}

//récupération des paramètres de la requète
$id_saisie = isset($_POST["id_saisie"]) ? $_POST["id_saisie"] :(isset($_GET["id_saisie"]) ? $_GET["id_saisie"] :(isset($_SESSION["id_saisie"]) ? $_SESSION["id_saisie"] : NULL));
if (isset($id_saisie) && $id_saisie != null) $_SESSION['id_saisie'] = $id_saisie;

//==============================================
$table_style_specifique[] = "mod_abs2/lib/abs_style";
$table_style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$table_javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$table_javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$table_javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

$titre_page = "Les absences";
$niveau_arbo = 1;
$gepiPathJava="./..";

//===== Classes d'affichage =====

include "classes/affichage/class_menu_abs2_inc.php";
include "classes/affichage/class_visu_saisie.php";


$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
// require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
// ====== Inclusion des balises head et du bandeau =====

include_once("../lib/header_template.inc");

/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

//echo "<div id='aidmenu' style='display: none;'>test</div>\n";

include('menu_abs2.inc.php');
//===========================
//echo "<div class='css-panes' id='containDiv' style='overflow : auto;'>\n";

$affiche_visu_saisie= new class_visu_saisie();
$saisie = AbsenceEleveSaisieQuery::create()->findPk($id_saisie);

if ($saisie == null) {
  $criteria = new Criteria();
  $criteria->addDescendingOrderByColumn(AbsenceEleveSaisiePeer::UPDATED_AT);
  $criteria->setLimit(1);
  $saisie_col = $utilisateur->getAbsenceEleveSaisiesJoinEdtCreneau($criteria);
  $saisie = $saisie_col->getFirst();

  //TODO : inverser le test pour quand même passer au gabarit
  if ($saisie == null) {
	//echo "saisie non trouvée";
	if (!$affiche_visu_saisie->set_non_trouvee())
		echo "erreur lors de l'enregistrement de non_trouvee dans visu_saisie.php" ;
	//die();
  }
}

if ($saisie != null) {
	//on va mettre dans la session l'identifiant de la saisie pour faciliter la navigation par onglet
	//if ($saisie != null) {
		$_SESSION['id_saisie_visu'] = $saisie->getPrimaryKey();
	//}


	//la saisie est-elle modifiable ?
	//Une saisie est modifiable si : elle appartient à l'utilisateur de la session,
	//elle date de moins d'une heure et l'option a été cochée par admin
	$modifiable = FALSE;
	if (getSettingValue("abs2_modification_saisie_une_heure")=='y') {
	  if ($saisie->getUtilisateurId() == $utilisateur->getPrimaryKey() && $saisie->getCreatedAt('U') > (time() - 3600)) {
		$modifiable = TRUE;
	  }
	}
	if (!$modifiable) {
	  // echo "La saisie n'est pas modifiable";
	  if (!$affiche_visu_saisie->set_non_modifiable())
		  echo "erreur lors de l'enregistrement du statut modifiable dans visu_saisie.php" ;
	}

	if (isset($message_enregistrement)) {
		//echo $message_enregistrement;
		if (!$affiche_visu_saisie->set_message_enregistrement($message_enregistrement))
			echo "erreur lors de l'enregistrement du message d'enregistrement dans visu_saisie.php" ;
	}

	$affiche_visu_saisie->set_saisie($saisie);

	//echo '<table class="normal">';
	//echo '<form method="post" action="enregistrement_modif_saisie.php">';
	//echo '<input type="hidden" name="id_saisie" value="'.$saisie->getPrimaryKey().'"/>';
	if (!$affiche_visu_saisie->set_cle($saisie->getPrimaryKey()))
			echo "erreur lors de l'enregistrement de la clé dans visu_saisie.php" ;
	//echo '<TBODY>';
	//echo '<tr><TD>';
	//echo 'N° de saisie : ';
	//echo '</TD><TD>';
	//echo $saisie->getPrimaryKey();
	//echo '</TD></tr>';

	//echo '<tr><TD>';
	//echo 'Eleve : ';
	//echo '</TD><TD>';
	if ($saisie->getEleve() != null) {
	  //echo $saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom();

	  if (!$affiche_visu_saisie->set_tableau_eleve("Eleve", $saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom()))
		echo "erreur lors de l'enregistrement des infos élèves dans visu_saisie.php"  ;

	  $nom_photo = "";
	  if ((getSettingValue("active_module_trombinoscopes")=='y') && $saisie->getEleve() != null) {
		$nom_photo = $saisie->getEleve()->getNomPhoto(1);
		$photos = "../photos/eleves/".$nom_photo;
		if (($nom_photo == "") or (!(file_exists($photos)))) {
		  $photos = "../mod_trombinoscopes/images/trombivide.jpg";
		}

		if (!$affiche_visu_saisie->set_photo($photos,$saisie->getPrimaryKey()))
			  echo "erreur lors de l'enregistrement du lien photo dans visu_saisie.php"  ;

		//$valeur = redimensionne_image_petit($photos);
		//echo ' <img src="'.$photos.'" style="width: '.$valeur[0].'px; height: '.$valeur[1].'px; border: 0px; vertical-align: middle;" alt="" title="" />';
	  }
  /*if (!$affiche_visu_saisie->set_tableau_eleve("Eleve", $saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom()).' '.$photos)
		echo "erreur lors de l'enregistrement des infos élèves dans visu_saisie.php"  ;*/

	  if ($utilisateur->getEleves()->contains($saisie->getEleve()) && ($utilisateur->getStatut() != 'professeur' || (getSettingValue("voir_fiche_eleve") == "y"))) {
	  //echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."&amp;onglet=absences' target='_blank'>";
	  //echo ' (voir fiche)';
	  //echo "</a>";

		if (!$affiche_visu_saisie->set_voir_fiche())
				  echo "erreur lors de l'enregistrement de l'autorisation de voir la fiche dans visu_saisie.php" ;
		if (!$affiche_visu_saisie->set_voir_login($saisie->getEleve()->getLogin()))
			  echo "erreur lors de l'enregistrement du login élève dans visu_saisie.php" ;

	  }
	} else {
    //echo "Aucun élève absent";
	  if (!$affiche_visu_saisie->set_tableau_eleve("Eleve", "Aucun élève absent"))
		echo "erreur lors de l'enregistrement des infos élèves dans visu_saisie.php"  ;
	}
// echo '</TD></tr>';

	if ($saisie->getClasse() != null) {
	  // echo '<tr><TD>';
	  // echo 'Classe : ';
	  // echo '</TD><TD>';
	  // echo $saisie->getClasse()->getNomComplet();
	  // echo '</TD></tr>';

	  if (!$affiche_visu_saisie->set_tableau_eleve("Classe",$saisie->getClasse()->getNomComplet()))
			  echo "erreur lors de l'enregistrement de la classe dans visu_saisie.php";
	}

if ($saisie->getGroupe() != null) {
    // echo '<tr><TD>';
    // echo 'Groupe : ';
    // echo '</TD><TD>';
    // echo $saisie->getGroupe()->getNameAvecClasses();
    // echo '</TD></tr>';

	if (!$affiche_visu_saisie->set_tableau_eleve("Groupe",$saisie->getGroupe()->getNameAvecClasses()))
			  echo "erreur lors de l'enregistrement du groupe dans visu_saisie.php";
}

if ($saisie->getAidDetails() != null) {
    // echo '<tr><TD>';
   //  echo 'Aid : ';
    // echo '</TD><TD>';
    // echo $saisie->getAidDetails()->getNom();
    // echo '</TD></tr>';

	if (!$affiche_visu_saisie->set_tableau_eleve("Aid",$saisie->getAidDetails()->getNom()))
			  echo "erreur lors de l'enregistrement de AID dans visu_saisie.php";
}


if ($saisie->getEdtCreneau() != null) {
    // echo '<tr><TD>';
   //  echo 'Creneau : ';
    // echo '</TD><TD>';
    // echo $saisie->getEdtCreneau()->getDescription();
   //  echo '</TD></tr>';

	if (!$affiche_visu_saisie->set_tableau_eleve("Créneau",$saisie->getEdtCreneau()->getDescription()))
			  echo "erreur lors de l'enregistrement de Créneau dans visu_saisie.php";
}

// echo '<tr><TD>';
// echo 'Debut : ';
// echo '</TD><TD>';
if (!$modifiable) {
    //echo (strftime("%a %d %b %Y %H:%M", $saisie->getDebutAbs('U')));  
	if (!$affiche_visu_saisie->set_tableau_eleve("Debut",$saisie->getDebutAbs('U')))
			  echo "erreur lors de l'enregistrement de Debut dans visu_saisie.php";
} else {
    // echo '<nobr><input name="heure_debut" value="'.$saisie->getDebutAbs("H:i").'" type="text" maxlength="5" size="4"/>&nbsp;';

	if (!$affiche_visu_saisie->set_tableau_eleve("Debut",$saisie->getDebutAbs('U')))
			  echo "erreur lors de l'enregistrement de Debut dans visu_saisie.php";
	
    //if ($utilisateur->getStatut() == 'professeur' && getSettingValue("abs2_saisie_prof_decale") != 'y') {
	  //if (true) {
	  // echo (strftime(" %a %d %b %Y", $saisie->getDebutAbs('U')));
	  
	  //echo '<input name="date_debut" value="'.$saisie->getDebutAbs('d/m/Y').'" type="hidden"/></nobr> ';
	  
   // } else {
	//echo '<input id="trigger_calendrier_debut" name="date_debut" value="'.$saisie->getDebutAbs('d/m/Y').'" type="text" maxlength="10" size="8"/></nobr> ';
	
	  //    echo '<img id="trigger_date_debut" src="../images/icons/calendrier.gif"/>';

	  //echo '</nobr>';
	  //
	  /*
	  echo '
	  <script type="text/javascript">
		  Calendar.setup({
		  inputField     :    "trigger_calendrier_debut",     // id of the input field
		  ifFormat       :    "%d/%m/%Y",      // format of the input field
		  button         :    "trigger_calendrier_debut",  // trigger for the calendar (button ID)
		  align          :    "Tl",           // alignment (defaults to "Bl")
		  singleClick    :    true
		  });
	  </script>';
	   *
	 */
   // }
}
//echo '</TD></tr>';

//echo '<tr><TD>';
//echo 'Fin : ';
//echo '</TD><TD>';
if (!$modifiable) {
   // echo (strftime("%a %d %b %Y %H:%M", $saisie->getFinAbs('U')));
	if (!$affiche_visu_saisie->set_tableau_eleve("Fin",$saisie->getFinAbs('U')))
			  echo "erreur lors de l'enregistrement de Fin dans visu_saisie.php";
} else {
  // echo '<nobr><input name="heure_fin" value="'.$saisie->getFinAbs("H:i").'" type="text" maxlength="5" size="4"/>&nbsp;';
  
	if (!$affiche_visu_saisie->set_tableau_eleve("Fin",$saisie->getFinAbs('U')))
			  echo "erreur lors de l'enregistrement de Fin dans visu_saisie.php";
	
  if ($utilisateur->getStatut() == 'professeur' && getSettingValue("abs2_saisie_prof_decale") != 'y') {

	if (!$affiche_visu_saisie->set_prof_decale(FALSE))
			  echo "erreur lors de l'enregistrement de 'prof_decale' dans visu_saisie.php";

    //if (true) {
	  //echo (strftime(" %a %d %b %Y", $saisie->getDebutAbs('U')));
	  
	  //echo '<input name="date_fin" value="'.$saisie->getFinAbs('d/m/Y').'" type="hidden"/></nobr> ';
	 
    } else {
	if (!$affiche_visu_saisie->set_prof_decale(TRUE))
			  echo "erreur lors de l'enregistrement de 'prof_decale' dans visu_saisie.php";

	  // echo '<input id="trigger_calendrier_fin" name="date_fin" value="'.$saisie->getFinAbs('d/m/Y').'" type="text" maxlength="10" size="8"/></nobr> ';
	  // echo '<img id="trigger_date_debut" src="../images/icons/calendrier.gif"/>';
	  // echo '</nobr>';

	/*
	echo '
	<script type="text/javascript">
	    Calendar.setup({
		inputField     :    "trigger_calendrier_fin",     // id of the input field
		ifFormat       :    "%d/%m/%Y",      // format of the input field
		button         :    "trigger_calendrier_fin",  // trigger for the calendar (button ID)
		align          :    "Tl",           // alignment (defaults to "Bl")
		singleClick    :    true
	    });
	</script>';
	 *
	 */
    }
}
// echo '</TD></tr>';

if ($saisie->getEdtEmplacementCours() != null) {
    // echo '<tr><TD>';
    // echo 'Cours : ';
    // echo '</TD><TD>';
   //  echo $saisie->getEdtEmplacementCours()->getDescription();
    // echo '</TD></tr>';
	if (!$affiche_visu_saisie->set_tableau_eleve("Cours",$saisie->getEdtEmplacementCours()->getDescription()))
	  echo "erreur lors de l'enregistrement de Cours dans visu_saisie.php";
}

// echo '<tr><TD>';
// echo 'Traitement : ';
// echo '</TD><TD>';
if (!$affiche_visu_saisie->set_tableau_eleve("Traitement","Traitement"))
  echo "erreur lors de l'enregistrement de Traitement Non modifiable dans visu_saisie.php";
foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
    //on affiche les traitements uniquement si ils ne sont pas modifiables, car si ils sont modifiables on va afficher un input pour pouvoir les modifier
    if ($traitement->getUtilisateurId() != $utilisateur->getPrimaryKey() || !$modifiable) {
	// echo "<nobr>";
	// echo "<a href='visu_traitement.php?id_traitement=".$traitement->getId()."' style='display: block; height: 100%;'> ";
	// echo $traitement->getDescription();
	// echo "</a>";
	// echo "</nobr><br/>";
	  if (!$affiche_visu_saisie->set_traitement_Non_modifiable($traitement->getId(),$traitement->getDescription() ))
	  echo "erreur lors de l'enregistrement des traitements non modifiables dans visu_saisie.php";
    }
}
unset ($traitement);

$total_traitements = 0;
$type_autorises = AbsenceEleveTypeStatutAutoriseQuery::create()->filterByStatut($utilisateur->getStatut())->find();
foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
    //on affiche les traitements uniquement si ils ne sont pas modifiables, car si ils sont modifiables on va afficher un input pour pouvoir les modifier
    if ($traitement->getUtilisateurId() == $utilisateur->getPrimaryKey() && $modifiable) {
	$total_traitements = $total_traitements + 1;
	$type_autorises->getFirst();
	if ($type_autorises->count() != 0) {
		// echo '<input type="hidden" name="id_traitement[';
		// echo ($total_traitements - 1);
		// echo ']" value="'.$traitement->getId().'"/>';
		// echo ("<select name=\"type_traitement[");
		// echo ($total_traitements - 1);
		// echo ("]\">");
		// echo "<option value='-1'></option>\n";
	  if (!$affiche_visu_saisie->set_traitement_autorise($traitement->getId()))
		echo "erreur lors de l'enregistrement des traitements autorisé dans visu_saisie.php";

	  foreach ($type_autorises as $type) {
		    //$type = new AbsenceEleveTypeStatutAutorise();
			// echo "<option value='".$type->getAbsenceEleveType()->getId()."'";
			if ($type->getAbsenceEleveType()->getId() == $traitement->getATypeId()) {
			   //  echo "selected='selected'";
			  if (!$affiche_visu_saisie->set_traitement_modifiable($type->getAbsenceEleveType()->getId(),$type->getAbsenceEleveType()->getNom(),TRUE ))
				echo "erreur lors de l'enregistrement des traitements modifiables dans visu_saisie.php";
			}else{
			  if (!$affiche_visu_saisie->set_traitement_modifiable($type->getAbsenceEleveType()->getId(),$type->getAbsenceEleveType()->getNom() ))
				echo "erreur lors de l'enregistrement des traitements modifiables dans visu_saisie.php";
			  
			}
			// echo ">";
			// echo $type->getAbsenceEleveType()->getNom();
			// echo "</option>\n";
		}
		// echo "</select><br>";
	}
    }
}
unset ($traitement);
// echo '<input type="hidden" name="total_traitements" value="'.$total_traitements.'"/>';

if ($modifiable && $total_traitements == 0) {
   // echo ("<select name=\"ajout_type_absence\">");
   // echo "<option value='-1'></option>\n";
	  if (!$affiche_visu_saisie->set_traitement_autorise("ajout_type_absence"))
		echo "erreur lors de l'enregistrement des traitements autorisé dans visu_saisie.php";

    foreach ($type_autorises as $type) {

	//$type = new AbsenceEleveTypeStatutAutorise();
	    // echo "<option value='".$type->getAbsenceEleveType()->getId()."'";
	    // echo ">";
	    // echo $type->getAbsenceEleveType()->getNom();
	    // echo "</option>\n";
  if (!$affiche_visu_saisie->set_traitement_modifiable($type->getAbsenceEleveType()->getId(),$type->getAbsenceEleveType()->getNom()))
	echo "erreur lors de l'enregistrement des types d'absences dans visu_saisie.php";
    }
	unset($type);
    // echo "</select>";
}

// echo '</TD></tr>';

if ($modifiable || ($saisie->getCommentaire() != null && $saisie->getCommentaire() != "")) {
    // echo '<tr><TD>';
    // echo 'Commentaire : ';
    // echo '</TD><TD>';
   //  if (!$modifiable) {
	// echo ($saisie->getCommentaire());
    // } else {
	// echo '<input name="commentaire" value="'.$saisie->getCommentaire().'" type="text" maxlength="150" size="25"/>';
   //  }
if (!$affiche_visu_saisie->set_tableau_eleve('Commentaire', $saisie->getCommentaire()))
  echo "erreur lors de l'enregistrement des commentaires dans visu_saisie.php";
    // echo '</TD></tr>';
}

// echo '<tr><TD>';
// echo 'Saisie le : ';
// echo '</TD><TD>';
// echo (strftime("%a %d %b %Y %H:%M", $saisie->getCreatedAt('U')));
if (!$affiche_visu_saisie->set_tableau_eleve('Saisie le',strftime("%a %d %b %Y %H:%M", $saisie->getCreatedAt('U')) ))
  echo "erreur lors de l'enregistrement de 'saisie le' dans visu_saisie.php";
// echo '</TD></tr>';

if ($saisie->getCreatedAt() != $saisie->getUpdatedAt()) {
   //  echo '<tr><TD>';
   //  echo 'Modifiée le : ';
   //  echo '</TD><TD>';
   //  echo (strftime("%a %d %b %Y %H:%M", $saisie->getUpdatedAt('U')));
if (!$affiche_visu_saisie->set_tableau_eleve('Modifiée le',strftime("%a %d %b %Y %H:%M", $saisie->getUpdatedAt('U')) ))
  echo "erreur lors de l'enregistrement de 'Modifiée le' dans visu_saisie.php";
    // echo '</TD></tr>';
}

if ($saisie->getIdSIncidents() !== null) {
    // echo '<tr><TD>';
   //  echo 'Discipline : ';
    // echo '</TD><TD>';
    /* echo "<a href='../mod_discipline/saisie_incident.php?id_incident=".
    $saisie->getIdSIncidents()."&step=2&return_url=no_return'>Visualiser l'incident </a>";*/
   //  echo '</TD></tr>';
    $texte= "<a href='../mod_discipline/saisie_incident.php?id_incident=".
    $saisie->getIdSIncidents()."&step=2&return_url=no_return'>Visualiser l'incident </a>";
	if (!$affiche_visu_saisie->set_tableau_eleve('Incidents',$saisie->getIdSIncidents()))
	  echo "erreur lors de l'enregistrement de 'Incidents' dans visu_saisie.php";
} elseif ($modifiable && $saisie->hasTypeSaisieDiscipline()) {
    // echo '<tr><TD>';
    // echo 'Discipline : ';
    // echo '</TD><TD>';
    /*echo "<a href='../mod_discipline/saisie_incident_abs2.php?id_absence_eleve_saisie=".
	$saisie->getId()."&return_url=no_return'>Saisir un incident disciplinaire</a>";*/
    // echo '</TD></tr>';
    $texte= "<a href='../mod_discipline/saisie_incident_abs2.php?id_absence_eleve_saisie=".
	$saisie->getId()."&return_url=no_return'>Saisir un incident disciplinaire</a>";
	if (!$affiche_visu_saisie->set_tableau_eleve('Discipline',$texte))
	  echo "erreur lors de l'enregistrement de 'Discipline' dans visu_saisie.php";
}

// echo '</TD></tr>';
/*
if ($modifiable) {
    echo '<tr><TD colspan="2" style="text-align : center;">';
    echo '<button type="submit">Enregistrer les modifications</button>';
    echo '</TD></tr>';
}
 *
 */

if ($utilisateur->getStatut()=="cpe" || $utilisateur->getStatut()=="scolarite") {
/*
    echo '<tr><TD colspan="2" style="text-align : center;">';
    echo '<button type="submit" name="creation_traitement" value="oui">Traiter la saisie</button>';
    echo '</TD></tr>';
 */
	if (!$affiche_visu_saisie->set_peut_traiter(TRUE))
	  echo "erreur lors de l'enregistrement de 'peut_traiter' dans visu_saisie.php";

}

// echo '</TBODY>';

// echo '</form>';
// echo '</table>';

}

// max-height et max-width fait la même chose
//fonction redimensionne les photos petit format
/*
function redimensionne_image_petit($photo)
 {
    // prendre les informations sur l'image
    $info_image = getimagesize($photo);
    // largeur et hauteur de l'image d'origine
    $largeur = $info_image[0];
    $hauteur = $info_image[1];
    // largeur et/ou hauteur maximum à afficher
             $taille_max_largeur = 35;
             $taille_max_hauteur = 35;

    // calcule le ratio de redimensionnement
     $ratio_l = $largeur / $taille_max_largeur;
     $ratio_h = $hauteur / $taille_max_hauteur;
     $ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

    // définit largeur et hauteur pour la nouvelle image
     $nouvelle_largeur = $largeur / $ratio;
     $nouvelle_hauteur = $hauteur / $ratio;

   // on renvoit la largeur et la hauteur
    return array($nouvelle_largeur, $nouvelle_hauteur);
 }
 *
 */

/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime	="";
$tbs_pmv="";
require_once ("../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var();


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/mod_abs2/visu_saisie_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($affiche_visu_saisie);





?>