<?php
/**
 *
 *
 * Copyright 2010-2012 Josselin Jacquard Bouguin Régis
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


//**************** FONCTIONS *****************
//fonction redimensionne les photos petit format
function redimensionne_image_petit($photo)
 {
    // prendre les informations sur l'image
    $info_image = getimagesize($photo);
    // largeur et hauteur de l'image d'origine
    $largeur = $info_image[0];
    $hauteur = $info_image[1];
    // largeur et/ou hauteur maximum à afficher
             $taille_max_largeur = 45;
             $taille_max_hauteur = 45;

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

 function format_selectbox_heure($utilisateur, $id_creneau, $dt_date_absence_eleve, $id_box) {
     	if ($utilisateur->getStatut() != 'professeur' || getSettingValue("abs2_saisie_prof_decale_journee")=='y' || getSettingValue("abs2_saisie_prof_decale")=='y') {
?>
<label class="invisible" for="<?php echo $id_box; ?>">heure</label>
<select id="<?php echo $id_box; ?>" name="id_creneau" class="small">
<?php
	    //echo ("<select name=\"id_creneau\" class=\"small\">");
	    $edt_creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime(); ?>
	<option value='-1'>choisissez un créneau</option>
<?php
	    foreach ($edt_creneau_col as $edt_creneau) {
		    if ($edt_creneau->getTypeCreneaux() == EdtCreneau::TYPE_PAUSE
			    || $edt_creneau->getTypeCreneaux() == EdtCreneau::TYPE_REPAS) {
			continue;
		    }
		    echo "<option value='".$edt_creneau->getIdDefiniePeriode()."'";
		    if ($id_creneau == $edt_creneau->getIdDefiniePeriode()) echo " selected='selected' ";
		    echo ">";
		    echo $edt_creneau->getDescription();
		    echo "</option>\n";
	    } 
?>
	    </select>
<?php
	} else {
	    $current_creneau = EdtCreneauPeer::retrieveEdtCreneauActuel();
	    if ($current_creneau != null) {
		echo $current_creneau->getDescription().' ';
		echo '<input type="hidden" name="id_creneau" value="'.$current_creneau->getIdDefiniePeriode().'"/>';
	    } else {
		echo "Aucun creneau actuellement.&nbsp;";
	    }
	}

	if ($utilisateur->getStatut() != 'professeur' || (getSettingValue("abs2_saisie_prof_decale")=='y' && getSettingValue("abs2_saisie_prof_decale_journee")=='y')) {
	    $rand_id = rand(0,10000000);
		echo '<label class="invisible" for="date_absence_eleve_'.$rand_id.'">date</label>';
	    echo '<input size="9" id="date_absence_eleve_'.$rand_id.'" name="date_absence_eleve" value="'.$dt_date_absence_eleve->format('d/m/Y').'" />&nbsp;';
	    echo '
	    <script type="text/javascript">
		Calendar.setup({
		    inputField     :    "date_absence_eleve_'.$rand_id.'",     // id of the input field
		    ifFormat       :    "%d/%m/%Y",      // format of the input field
		    button         :    "date_absence_eleve_1",  // trigger for the calendar (button ID)
		    align          :    "Bl",           // alignment (defaults to "Bl")
		    singleClick    :    true
		});
	    </script>';
	} else {
	    echo ' Le '.$dt_date_absence_eleve->format('d/m/Y').' ';
	}
 }
 
$journeePossible = FALSE;
if ('cpe' == $_SESSION['statut'] || 'scolarite' == $_SESSION['statut']) {
	$journeePossible = TRUE;
}

// $affiche_debug=debug_var();
// Initialiser la requête
if (isset($_POST["initialise"]) && $_POST["initialise"]==TRUE) {
  unset ($_SESSION['id_groupe_abs'],
  $_SESSION['id_classe_abs'],
  $_SESSION['id_aid'],
  $_SESSION['id_creneau'],
  $_SESSION['id_cours'],
  $_SESSION['type_selection'],
  $_SESSION['date_absence_eleve'],
  $_SESSION['id_semaine']);
}

$_SESSION['showJournee'] = isset($_POST["journee"]) ? $_POST["journee"] : (isset($_SESSION['showJournee']) ? $_SESSION['showJournee'] : FALSE);

//récupération des paramètres de la requète
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :(isset($_SESSION["id_groupe_abs"]) ? $_SESSION["id_groupe_abs"] : NULL));
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :(isset($_SESSION["id_classe_abs"]) ? $_SESSION["id_classe_abs"] : NULL));
$id_aid = isset($_POST["id_aid"]) ? $_POST["id_aid"] :(isset($_GET["id_aid"]) ? $_GET["id_aid"] :(isset($_SESSION["id_aid"]) ? $_SESSION["id_aid"] : NULL));
$id_creneau = isset($_POST["id_creneau"]) ? $_POST["id_creneau"] :(isset($_GET["id_creneau"]) ? $_GET["id_creneau"] :(isset($_SESSION["id_creneau"]) ? $_SESSION["id_creneau"] : NULL));
$id_cours = isset($_POST["id_cours"]) ? $_POST["id_cours"] :(isset($_GET["id_cours"]) ? $_GET["id_cours"] :(isset($_SESSION["id_cours"]) ? $_SESSION["id_cours"] : NULL));
$type_selection = isset($_POST["type_selection"]) ? $_POST["type_selection"] :(isset($_GET["type_selection"]) ? $_GET["type_selection"] :(isset($_SESSION["type_selection"]) ? $_SESSION["type_selection"] : NULL));
$date_absence_eleve = isset($_POST["date_absence_eleve"]) ? $_POST["date_absence_eleve"] :(isset($_GET["date_absence_eleve"]) ? $_GET["date_absence_eleve"] :(isset($_SESSION["date_absence_eleve"]) ? $_SESSION["date_absence_eleve"] : NULL));
$id_semaine = isset($_POST["id_semaine"]) ? $_POST["id_semaine"] :(isset($_GET["id_semaine"]) ? $_GET["id_semaine"] :(isset($_SESSION["id_semaine"]) ? $_SESSION["id_semaine"] : NULL));
$cahier_texte = isset($_POST["cahier_texte"]) ? $_POST["cahier_texte"] :(isset($_GET["cahier_texte"]) ? $_GET["cahier_texte"] :NULL);

if (isset($id_groupe) && $id_groupe != null) $_SESSION['id_groupe_abs'] = $id_groupe;
if (isset($id_classe) && $id_classe != null) $_SESSION['id_classe_abs'] = $id_classe;
if (isset($id_aid) && $id_aid != null) $_SESSION['id_aid'] = $id_aid;
if (isset($id_creneau) && $id_creneau != null) $_SESSION['id_creneau'] = $id_creneau;
if (isset($id_cours) && $id_cours != null) $_SESSION['id_cours'] = $id_cours;
if (isset($type_selection) && $type_selection != null) $_SESSION['type_selection'] = $type_selection;
if (isset($date_absence_eleve) && $date_absence_eleve != null) $_SESSION['date_absence_eleve'] = $date_absence_eleve;
if (isset($id_semaine) && $id_semaine != null) $_SESSION['id_semaine'] = $id_semaine;

if (TRUE == $_SESSION['showJournee']) {
	$id_creneau = '1';
}

//initialisation des variables
$current_cours = null;
$current_classe = null;
$current_groupe = null;
$current_aid = null;
$current_creneau = null;
$current_semaine = null;
if ($id_semaine == null || $id_semaine == -1 || !is_numeric($id_semaine) || $id_semaine > 53
	|| ($utilisateur->getStatut() == 'professeur' && (getSettingValue("abs2_saisie_prof_decale")!='y'))) {
    $id_semaine = date('W');
}
$current_semaine = EdtSemaineQuery::create()->findPk($id_semaine);

if ($utilisateur->getStatut() == 'professeur' && getSettingValue("abs2_saisie_prof_decale")!='y' && getSettingValue("abs2_saisie_prof_decale_journee")!='y') {
    $id_creneau = null;
    $id_cours = null;
}

if ($utilisateur->getStatut() == 'professeur' && (getSettingValue("abs2_saisie_prof_decale")!='y')) {
    $dt_date_absence_eleve = new DateTime('now');
} elseif ($date_absence_eleve != null) {
    try {
	$dt_date_absence_eleve = new DateTime(str_replace("/",".",$date_absence_eleve));
    } catch (Exception $x) {
	echo "<span style='color :red'>Erreur : Mauvais format de date d'absence.</span><br/>";
	$dt_date_absence_eleve = new DateTime('now');
    }
} else {
    $dt_date_absence_eleve = new DateTime('now');
}

if ($type_selection == 'id_cours') {
    if ($utilisateur->getStatut() == "professeur") {
	$current_cours = EdtEmplacementCoursQuery::create()->filterByUtilisateurProfessionnel($utilisateur)->findPk($id_cours);
    } else {
	$current_cours = EdtEmplacementCoursQuery::create()->findPk($id_cours);
    }
    $current_creneau = null;
    if ($current_cours != null) {
	$current_creneau = $current_cours->getEdtCreneau();
	$current_groupe = $current_cours->getGroupe();
	$current_aid = $current_cours->getAidDetails();
	$dt_date_absence_eleve = $current_cours->getDate($id_semaine);
    }
} else {
    if ($id_creneau == null) {
	$current_creneau = EdtCreneauPeer::retrieveEdtCreneauActuel();
    } else {
	$current_creneau = EdtCreneauPeer::retrieveByPK($id_creneau);
    }
}
if ($type_selection == 'id_groupe') {
    if ($utilisateur->getStatut() == "professeur") {
	$current_groupe = GroupeQuery::create()->filterByUtilisateurProfessionnel($utilisateur)->findPk($id_groupe);
    } else {
	$current_groupe = GroupeQuery::create()->findPk($id_groupe);
    }
} else if ($type_selection == 'id_aid') {
    $current_aid = AidDetailsQuery::create()->findPk($id_aid);
} else if ($type_selection == 'id_classe') {
    $current_classe = ClasseQuery::create()->findPk($id_classe);
} else if ($type_selection != 'id_cours' && getSettingValue("autorise_edt_tous") == 'y'){//rien n'as ete selectionner, on va regarder le cours actuel
    $current_cours = $utilisateur->getEdtEmplacementCours();
    if ($current_cours != null) {
	$current_creneau = $current_cours->getEdtCreneau();
	$current_groupe = $current_cours->getGroupe();
	$current_aid = $current_cours->getAidDetails();
	$type_selection = 'id_cours';
    } else {
	if (isset($_SESSION['id_groupe_session'])) {
	    $id_groupe =  $_SESSION['id_groupe_session'];
	    $current_groupe = GroupeQuery::create()->filterByUtilisateurProfessionnel($utilisateur)->findPk($id_groupe);
	    $type_selection = 'id_groupe';
	}
    }
}
$id_groupe = null;
$id_classe = null;
$id_aid = null;
$id_creneau = null;
$id_cours = null;
if ($current_groupe != null) {$id_groupe = $current_groupe->getId();}
if ($current_classe != null) {$id_classe = $current_classe->getId();}
if ($current_aid != null) {$id_aid = $current_aid->getId();}
if ($current_creneau != null) {$id_creneau = $current_creneau->getIdDefiniePeriode();}
if ($current_cours != null) {$id_cours = $current_cours->getIdCours();}
if ($cahier_texte != null && $cahier_texte != "") {
    $location = "Location: ../cahier_texte/index.php";
    if ($id_groupe != null) {
	$location .= "?id_groupe=".$id_groupe;
    } else if ($current_cours != null) {
	$location .= "?id_groupe=".$current_cours->getIdGroupe();
    }
    header($location);
    die();
}


//**************** GROUPES ET AID *****************
//on affiche une boite de selection avec les groupes et les creneaux
if (getSettingValue("abs2_saisie_prof_hors_cours")!='y'
	&& $utilisateur->getStatut() == "professeur") {
	//le reglage specifie que le prof n'a pas le droit de saisir autre chose que son cours
	//donc on affiche pas de selection, le cours est automatiquement selectionné
} else {
    if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
		$groupe_col = GroupeQuery::create()->orderByName()->useJGroupesClassesQuery()->useClasseQuery()->orderByNom()->endUse()->endUse()
							->leftJoinWith('Groupe.JGroupesClasses')
							->leftJoinWith('JGroupesClasses.Classe')
							->find();
		$aid_col = AidDetailsQuery::create()->find();
    } else {
		$groupe_col = $utilisateur->getGroupes();
		$aid_col = $utilisateur->getAidDetailss();
    }
} 

//**************** COURS SEMAINES *****************
if (getSettingValue("abs2_saisie_prof_decale_journee")!='y'
	&& getSettingValue("abs2_saisie_prof_decale")!='y'
	&& $utilisateur->getStatut() == "professeur") {
	//le reglage specifie que le prof n'a pas le droit de saisir autre chose que son cours
	//donc on affiche pas de selection, le cours est automatiquement selectionné
} else if (getSettingValue("autorise_edt_tous") != 'y') {
    //edt desactivé
} else {
	$edt_cours_aff = new PropelCollection();
    //on affiche une boite de selection avec les cours
    if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
		//la collection entière des cours est trop grosse et inexploitable sous la forme d'une liste. ça consomme de la ressource donc c'est désactivé
		$edt_cours_col = new PropelCollection();
    } else {
		$edt_cours_col = $utilisateur->getEdtEmplacementCourssPeriodeCalendrierActuelle();
    }
	if (!$edt_cours_col->isEmpty()) {
		foreach ($edt_cours_col as $edt_cours) {
			if ($edt_cours->getEdtCreneau() == NULL) {
				//on affiche pas le cours si il n'est associé avec aucun creneau
				continue;
			}
			if (getSettingValue("abs2_saisie_prof_decale") != 'y' && $utilisateur->getStatut() == "professeur") {
				if ($edt_cours->getJourSemaineNumeric() != date('w')) {
				//on affiche pas ce cours car il n'est pas aujourd'hui
				continue;
				}
				if ($edt_cours->getTypeSemaine() != '' && $edt_cours->getTypeSemaine() != '0' && $edt_cours->getTypeSemaine() != $current_semaine->getTypeEdtSemaine()) {
				//on affiche pas ce cours car il n'est pas aujourd'hui
				continue;
				}
			}
			$edt_cours_aff->append($edt_cours);
		}
		foreach ($edt_cours_col as $edt_cours) {
			if ($edt_cours->getEdtCreneau() == NULL) {
				//on affiche pas le cours si il n'est associé avec aucun creneau
				continue;
			}
			if (getSettingValue("abs2_saisie_prof_decale") != 'y' && $utilisateur->getStatut() == "professeur") {
				if ($edt_cours->getJourSemaineNumeric() != date('w')) {
				//on affiche pas ce cours car il n'est pas aujourd'hui
				continue;
				}
				if ($edt_cours->getTypeSemaine() != '' && $edt_cours->getTypeSemaine() != '0' && $edt_cours->getTypeSemaine() != $current_semaine->getTypeEdtSemaine()) {
				//on affiche pas ce cours car il n'est pas aujourd'hui
				continue;
				}
			}
		}

		if (getSettingValue("abs2_saisie_prof_decale")=='y' || $utilisateur->getStatut() != "professeur") {
			$col = EdtSemaineQuery::create()->find();
			$semaineAff = new PropelCollection();
			//on va commencer la liste à la semaine 31 (milieu des vacances d'ete)
			for ($i = 0; $i < $col->count(); $i++) {
				$pos = ($i + 30) % $col->count();
				$semaine = $col[$pos];
				$semaineAff->append($col[$pos]);
			}
		} else {
			$semaineAff = new PropelCollection();
				$semaineAff->append($current_semaine);
		}

		if ($current_cours != null && $current_cours->getTypeSemaine() != '' && $current_cours->getTypeSemaine() != '0' && $current_semaine != null && $current_cours->getTypeSemaine() != $current_semaine->getTypeEdtSemaine()) {
			$erreurSemaine=TRUE;
			$current_cours = null;
			$current_groupe = null;
			$current_classe = null;
			$current_aid = null;
		}
	}
}

//**************** CLASSES *****************
if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    $classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
} else {
    $classe_col = $utilisateur->getClasses();
}

//**************** ELEVES *****************
//affichage des eleves. Il nous faut au moins un groupe ou une aid
$eleve_col = new PropelCollection();
if (isset($current_groupe) && $current_groupe != null) {
    $query = EleveQuery::create();
    $periode_cur = $current_groupe->getPeriodeNote($dt_date_absence_eleve);
    if ($periode_cur != null) {
        $query->useJEleveGroupeQuery()->filterByGroupe($current_groupe)->filterByPeriode($periode_cur->getNumPeriode())->endUse();
    } else {
        $query->useJEleveGroupeQuery()->filterByGroupe($current_groupe)->endUse();
    }
    $query->where('Eleve.DateSortie<?','0')
            ->orWhere('Eleve.DateSortie is NULL')
            ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve->format('U'))
            ->orderBy('Eleve.Nom','asc')
            ->orderBy('Eleve.Prenom','asc')
            ->distinct();
     $eleve_col = $query->find();
} else if (isset($current_aid) && $current_aid != null) {
    $query = EleveQuery::create();
    $eleve_col = $query->useJAidElevesQuery()
                        ->filterByIdAid($current_aid->getId())
                        ->endUse()
            ->where('Eleve.DateSortie<?','0')
            ->orWhere('Eleve.DateSortie is NULL')
            ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve->format('U'))
            ->orderBy('Eleve.Nom','asc')
            ->orderBy('Eleve.Prenom','asc')
            ->distinct()
            ->find();
} else if (isset($current_classe) && $current_classe != null) {
    $query = EleveQuery::create();
    $periode_cur = $current_classe->getPeriodeNote($dt_date_absence_eleve);
    if ($periode_cur != null) {
        $query->useJEleveClasseQuery()->filterByClasse($current_classe)->filterByPeriode($periode_cur->getNumPeriode())->endUse();
    } else {
        $query->useJEleveClasseQuery()->filterByClasse($current_classe)->endUse();
    }
    $query->where('Eleve.DateSortie<?','0')
            ->orWhere('Eleve.DateSortie is NULL')
            ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve->format('U'))
            ->orderBy('Eleve.Nom','asc')
            ->orderBy('Eleve.Prenom','asc')
            ->distinct();
    $eleve_col = $query->find();
}

//l'utilisateurs a-t-il deja saisie ce creneau ?
$deja_saisie = false;
if ($current_cours != null) {
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
    $dt = clone $dt_date_absence_eleve;
    $dt->setTime($current_cours->getHeureDebut('H'), $current_cours->getHeureDebut('i'));
    $dt_end = clone $dt;
    $dt_end->setTime($current_cours->getHeureFin('H'), $current_cours->getHeureFin('i'));
    $query->filterByPlageTemps($dt, $dt_end);
    if ($query->count() > 0) {
	$deja_saisie = true;
    }
} elseif ($current_creneau != null) {
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
    $dt = clone $dt_date_absence_eleve;
    $dt->setTime($current_creneau->getHeuredebutDefiniePeriode('H'), $current_creneau->getHeuredebutDefiniePeriode('i'));
    $dt_end = clone $dt;
    $dt_end->setTime($current_creneau->getHeurefinDefiniePeriode('H'), $current_creneau->getHeurefinDefiniePeriode('i'));
    $query->filterByPlageTemps($dt, $dt_end);
    if ($query->count() > 0) {
	$deja_saisie = true;
    }
}

if ($current_creneau == null) {
    //on vide la liste des eleves pour eviter de proposer une saisie
    $eleve_col = new PropelObjectCollection();
}


//**************** TABLEAU DES ELEVES *****************
$tab_regimes=array();
$tab_regimes_eleves=array();
$afficheEleve = array();
$elv = 0;
foreach($eleve_col as $eleve) {
	$saisie_affiches = array ();
	if ($eleve_col->isOdd()) {
		$afficheEleve[$elv]['background']="impair";
	} else {
		$afficheEleve[$elv]['background']="pair";
	}
	
	$Yesterday = date("Y-m-d",mktime(0,0,0,$dt_date_absence_eleve->format("m") ,$dt_date_absence_eleve->format("d")-1,$dt_date_absence_eleve->format("Y")));
	$abs_hier = false;
        $traitee_hier = true;//les saisies de la veille ont-elle été traitées intégralement
        $justifiee_hier = true;//les saisies de la veille ont-elle été justifiées intégralement
        $afficheEleve[$elv]['bulle_hier'] = '';
        foreach ($eleve->getAbsenceEleveSaisiesDuJour($Yesterday) as $saisie) {
            if (!$saisie->getManquementObligationPresence()) continue;
            $abs_hier = true;
            $traitee_hier = $traitee_hier && $saisie->getTraitee();
            $justifiee_hier = $justifiee_hier && $saisie->getJustifiee();
            $afficheEleve[$elv]['bulle_hier'] .= $saisie->getTypesDescription();
        }
        if ($abs_hier) {
            $afficheEleve[$elv]['class_hier'] = $justifiee_hier ? "justifieeHier" : 'absentHier';
            $afficheEleve[$elv]['text_hier'] = $traitee_hier ? 'T' : '';
        } else {
            $afficheEleve[$elv]['class_hier'] = '';
            $afficheEleve[$elv]['text_hier'] = '';
        }
	$afficheEleve[$elv]['position'] = $eleve_col->getPosition();
	$afficheEleve[$elv]['id'] = $eleve->getId();
	$afficheEleve[$elv]['nom'] = $eleve->getNom();
	$afficheEleve[$elv]['prenom'] = $eleve->getPrenom();
	$afficheEleve[$elv]['civilite'] = $eleve->getCivilite();
	// 20120618
	//$afficheEleve[$elv]['regime'] = $eleve->getRegime();
	$afficheEleve[$elv]['regime'] = '';
	$sql="SELECT regime FROM j_eleves_regime WHERE login='".$eleve->getLogin()."';";
	$res_regime=mysql_query($sql);
	if(mysql_num_rows($res_regime)>0) {
		$afficheEleve[$elv]['regime'] = mysql_result($res_regime, 0, 'regime');
		if(!in_array($afficheEleve[$elv]['regime'], $tab_regimes)) {
			$tab_regimes[]=$afficheEleve[$elv]['regime'];
		}
		$tab_regimes_eleves[$afficheEleve[$elv]['regime']][]=$afficheEleve[$elv]['position'];
	}

	if ((isset($current_groupe) && $current_groupe != null && $current_groupe->getClasses()->count() == 1)
		|| (isset($current_classe) && $current_classe != null)) {
		//si le groupe a une seule classe ou si c'est une classe qui est sélectionner pas la peine d'afficher la classe.
	} else {
		if ($eleve->getClasse() != null) {
			$afficheEleve[$elv]['classe'] = $eleve->getClasse()->getNom();
		}
	}
	
	if ($utilisateur->getAccesFicheEleve($eleve)) {
		$afficheEleve[$elv]['accesFiche'] = $eleve->getLogin();
	}
	
	$col_creneaux = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();
	$afficheEleve[$elv]['creneaux_possibles'] = $col_creneaux->count();
	for($i = 0; $i<$col_creneaux->count(); $i++){
		$edt_creneau = $col_creneaux[$i];
		$nb_creneau_a_saisir = 0; //il faut calculer le nombre de creneau a saisir pour faire un colspan
		if ($current_creneau != null && $current_creneau->getPrimaryKey() == $edt_creneau->getPrimaryKey()) {
			$creneau_courant=$i;
			$afficheEleve[$elv]['creneau_courant']=$i;
			// on va faire une boucle pour calculer le nombre de creneaux dans ce cours
			if ($current_cours == null) {
				$nb_creneau_a_saisir = 1;
				$absences_du_creneau = $eleve->getAbsenceEleveSaisiesDuCreneau($edt_creneau, $dt_date_absence_eleve);
			} else {
				//$nb_creneau_a_saisir = 0;
				$dt_fin_cours = $current_cours->getHeureFin(null);
				$it_creneau = $edt_creneau;
				$absences_du_creneau = new PropelObjectCollection();
				while ($it_creneau != null && $dt_fin_cours->format('U') > $it_creneau->getHeuredebutDefiniePeriode('U')) {
					foreach ($eleve->getAbsenceEleveSaisiesDuCreneau($it_creneau, $dt_date_absence_eleve) as $abs) {
						if (!$absences_du_creneau->contains($abs)) {
							$absences_du_creneau->append($abs);
						}
					}
					$it_creneau = $it_creneau->getNextEdtCreneau();
					$nb_creneau_a_saisir++;
				}
			}
			// pour le creneau en cours on garde uniquement les absences de l'utilisateur pour ne pas l'influencer par d'autres saisies sauf si configuré autrement
			if (getSettingValue("abs2_afficher_saisies_creneau_courant")!='y') {
				$absences_du_creneau_du_prof = new PropelObjectCollection();
				foreach ($absences_du_creneau as $abs) {
						if ($abs->getUtilisateurId() == $utilisateur->getPrimaryKey()) {
								$absences_du_creneau_du_prof->append($abs);
						}
				}
				$absences_du_creneau = $absences_du_creneau_du_prof;
			}
		} else if ($current_creneau != null && $edt_creneau->getHeuredebutDefiniePeriode('U') > $current_creneau->getHeuredebutDefiniePeriode('U')) {
			//on n'affiche pas les informations apres le creneau en cours pour ne pas influencer la saisie si c'est un enseignant
			if($utilisateur->getStatut() == "professeur"){
				$absences_du_creneau = new PropelCollection();
			}else{
				$absences_du_creneau = $eleve->getAbsenceEleveSaisiesDuCreneau($edt_creneau, $dt_date_absence_eleve);
			}
		} else {
			//on affiche  les informations pour les crenaux avant la saisie sauf si configuré autrement
			if (getSettingValue("abs2_montrer_creneaux_precedents")=='y') {
				$absences_du_creneau = $eleve->getAbsenceEleveSaisiesDuCreneau($edt_creneau, $dt_date_absence_eleve);
			} else {
				$absences_du_creneau = new PropelCollection();
			}
		}
		$afficheEleve[$elv]['style'][$i] = "";
                if ($deja_saisie && $nb_creneau_a_saisir > 0) {
			$afficheEleve[$elv]['style'][$i] = "fondVert";
		}
		if (!$absences_du_creneau->isEmpty()) {
			foreach ($absences_du_creneau as $abs_saisie) {
				if ($abs_saisie->getManquementObligationPresence()) {
					$afficheEleve[$elv]['style'][$i] = "fondRouge";
					break;
				}
			}
		}
		
		if ($nb_creneau_a_saisir>1) {
			$afficheEleve[$elv]['nb_creneaux_a_saisir'][$i] = $nb_creneau_a_saisir;
		} else {
			$afficheEleve[$elv]['nb_creneaux_a_saisir'][$i]= 1;
		}
		
		//si il y a des absences de l'utilisateurs on va proposer de les modifier
		if (getSettingValue("abs2_modification_saisie_une_heure")=='y') {
			foreach ($absences_du_creneau as $saisie) {
				if (in_array($saisie->getPrimaryKey(), $saisie_affiches)) {
					// on affiche les saisies une seule fois
					$afficheEleve[$elv]['saisie'][$i]="";
					continue;
				}
				$saisie_affiches[] = $saisie->getPrimaryKey();
				if ($saisie->getUtilisateurId() == $utilisateur->getPrimaryKey() && $saisie->getCreatedAt('U') > (time() - 3600)) {
					$afficheEleve[$elv]['saisie'][$i]['primaryKey'] = $saisie->getPrimaryKey();
					$afficheEleve[$elv]['saisie'][$i]['createdAt'] = $saisie->getCreatedAt("H:i");
					$besoin_echo_virgule = false;
					foreach ($saisie->getAbsenceEleveTraitements() as $bou_traitement) {
						if ($bou_traitement->getAbsenceEleveType() != null) {
							$afficheEleve[$elv]['saisie'][$i]['traitements'][] = $bou_traitement->getAbsenceEleveType()->getNom();
						}
					}
				}
			}
		}
		
		//on va afficher des renseignements sur les heures précédentes
		foreach ($absences_du_creneau as $abs_saisie) {
			if ($abs_saisie->getTraitee() && $abs_saisie->getManquementObligationPresence()) {
				$txt = $abs_saisie->getTypesDescription();
				if ($txt != '') {
					$afficheEleve[$elv]['saisieDescription'][$i][] = $abs_saisie->getTypesDescription();
				}
			}
		}
		
		if ($nb_creneau_a_saisir > 0) {
			// le message d'erreur de l'enregistrement precedent provient du fichier enregistrement_saisies_groupe.php
			if (isset($message_erreur_eleve[$eleve->getId()]) && $message_erreur_eleve[$eleve->getId()] != '') {
				$afficheEleve[$elv]['erreurEnregistre'][$i] = $message_erreur_eleve[$eleve->getId()];
			}
			
			//la saisie sur ce creneau
			$type_autorises = AbsenceEleveTypeQuery::create()->orderByRank()->useAbsenceEleveTypeStatutAutoriseQuery()->filterByStatut($utilisateur->getStatut())->endUse()->find();
			if ($type_autorises->count() != 0) {
				$afficheEleve[$elv]['type_autorises'][$i] = array();
				foreach ($type_autorises as $type) {
					$afficheEleve[$elv]['type_autorises'][$i][]= array('type'=>$type->getId(), 'nom'=>$type->getNom(), 'modeInterface'=>$type->getModeInterface());
				}
			}
		}
	}
	
	if ((getSettingValue("active_module_trombinoscopes")=='y')) {
		$nom_photo = $eleve->getNomPhoto(1);
		$photos = $nom_photo;
		if (($photos == NULL) or (!(file_exists($photos)))) {
			$photos = "../mod_trombinoscopes/images/trombivide.jpg";
		}
		$afficheEleve[$elv]['nom_photo'] = $photos;
	}
	$elv++;
}

// 20120618
$chaine_coche_decoche_regime="";
$js_var_coche_decoche_regime="var etat_cocher_regime=new Array();\n";
$js_var_coche_decoche_regime.="var nom_regime=new Array();\n";
if(count($tab_regimes)>0) {

	$chaine_coche_decoche_regime.="<span id='effectifs_regimes_js' style='display: none;'>";
	for($i=0;$i<count($tab_regimes);$i++) {
		//$chaine_coche_decoche_regime.="<div style='float:left; width:2em; margin-right: 0.5em;'><a href='#' onclick=\"cocher_decocher_regime($i); return false;\">".$tab_regimes[$i]."</a></div>\n";
		$chaine_coche_decoche_regime.="<span style='margin-right: 0.2em; margin-left: 0.2em;'><a href='#' onclick=\"cocher_decocher_regime($i); return false;\">".count($tab_regimes_eleves[$tab_regimes[$i]])." ".$tab_regimes[$i]."</a></span>\n";
		$js_var_coche_decoche_regime.="etat_cocher_regime[$i]=false;\n";
		$js_var_coche_decoche_regime.="nom_regime[$i]='".$tab_regimes[$i]."';\n";
		$js_var_coche_decoche_regime.="var ele_regime_$i=new Array(";
		for($j=0;$j<count($tab_regimes_eleves[$tab_regimes[$i]]);$j++) {
			if($j>0) {$js_var_coche_decoche_regime.=", ";}
			$js_var_coche_decoche_regime.=$tab_regimes_eleves[$tab_regimes[$i]][$j];
		}
		$js_var_coche_decoche_regime.=");\n";
	}
	$chaine_coche_decoche_regime.="</span>";

	$chaine_coche_decoche_regime.="<span id='effectifs_regimes_non_js'>";
	for($i=0;$i<count($tab_regimes);$i++) {
		$chaine_coche_decoche_regime.="<span style='margin-right: 0.2em; margin-left: 0.2em;'>".count($tab_regimes_eleves[$tab_regimes[$i]])." ".$tab_regimes[$i]."</span>\n";
	}
	$chaine_coche_decoche_regime.="</span>";
}

//==============================================
$style_specifique[] = "templates/origine/css/bandeau";
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$style_specifique[] = "mod_abs2/lib/saisie_smart_large";
$CSS_smartphone = "mod_abs2/lib/saisie_smart_mini";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
$javascript_specifique[] = "mod_abs2/lib/include";
$titre_page = "Les absences";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
$tbs_last_connection = "";

//**************** EN-TETE *****************
require_once("../lib/header_template.inc.php");
//include("../templates/origine/bandeau_template.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<!-- on inclut l'entête -->
<?php 
include('../templates/origine/header_template.php');
//include("../templates/origine/bandeau_template.php");
?>

<!-- corrections internet Exploreur -->
	<!--[if lte IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/accueil_ie.css' media='screen' />
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/bandeau_ie.css' media='screen' />
	<![endif]-->
	<!--[if lte IE 6]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/accueil_ie6.css' media='screen' />
	<![endif]-->
	<!--[if IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/accueil_ie7.css' media='screen' />
	<![endif]-->

<!-- Fin des styles -->


</head>

<!-- ******************************************** -->
<!-- Appelle les sous-modèles                     -->
<!-- templates/origine/header_template.php        -->
<!-- templates/origine/accueil_menu_template.php  -->
<!-- templates/origine/bandeau_template.php      -->
<!-- ******************************************** -->

<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion();<?php if($tbs_charger_observeur) echo $tbs_charger_observeur;?>">


<!-- on inclut le bandeau -->
	<?php include('../templates/origine/bandeau_template.php');?>

<div id='container'>

<?php 
//**************** FIN EN-TETE *****************

include('menu_abs2.inc.php');
//===========================
?>
<a name='haut_de_page'></a>
<div class='css-panes' id='containDiv'>

	<form class="center" action="./saisir_groupe.php" method="post" style="width: 100%;">
		<p>
			  <button type='submit' 
					  style='width:25em;margin:0 auto;' 
					  name='initialise' 
					  value='<?php echo TRUE; ?>'
					  title="Efface tous les filtres puis recharge la page">
				  Ré-initialiser la page
			  </button>
<?php if ($journeePossible) { ?>			
	<?php if ($_SESSION['showJournee']) { ?>
			  <button type='submit' 
					  style='width:25em;margin:0 auto;' 
					  name='journee' 
					  value='<?php echo FALSE; ?>'
					  title="Saisie d'un seul créneau">
				  Affichage 1 créneau
			  </button>
	<?php } else { ?>
			  <button type='submit' 
					  style='width:25em;margin:0 auto;' 
					  name='journee' 
					  value='<?php echo TRUE; ?>'
					  title="Saisie de tous les créneaux de la journée">
				  Affichage journée
			  </button>
	<?php } ?>
<?php } ?>
		</p>
	</form >

	<!-- Choix du groupe à afficher -->
	<div class="choix">
<?php 
// ===== Affichage des groupes ======
if (isset ($groupe_col) && !$groupe_col->isEmpty()) {
?>
		<form class="colonne" action="./saisir_groupe.php" method="post">
			<p>
				<input type="hidden" name="type_selection" value="id_groupe"/>
				<label for="id_groupe">Groupe : </label>
				<select id="id_groupe" name="id_groupe" class="small">
					<option value='-1'>choisissez un groupe</option>
<?php
foreach ($groupe_col as $group) {	
?>
					<option value='<?php echo $group->getId(); ?>'
						<?php if ($id_groupe == $group->getId()) { ?>
							selected='selected'
						<?php } ?>>
						<?php echo $group->getNameAvecClasses(); ?>
					</option>
<?php } ?>
				</select>
<?php echo format_selectbox_heure($utilisateur, $id_creneau, $dt_date_absence_eleve,"groupe"); ?>
				<button type="submit">Afficher les élèves</button>
			</p>
		</form>	
<?php }

// ===== Affichage des AID ======							

if (isset ($aid_col) && !$aid_col->isEmpty()) {
?>	
		<form class="colonne" action="./saisir_groupe.php" method="post">
			<p>
				<input type="hidden" name="type_selection" value="id_aid"/>
				<label for="id_aid">Aid : </label>
				<select id="id_aid" name="id_aid" class="small">
					<option value='-1'>choisissez une aid</option>
<?php foreach ($aid_col as $aid) { ?>
					<option value='<?php echo $aid->getPrimaryKey(); ?>'
						<?php if ($id_aid == $aid->getPrimaryKey()) { ?>
							selected='selected'
						<?php } ?>>
						<?php echo $aid->getNom(); ?>
					</option>
<?php 
}
?>
				</select>
				<?php echo format_selectbox_heure($utilisateur, $id_creneau, $dt_date_absence_eleve, "aid"); ?>
				<button type="submit">Afficher les élèves</button>
			</p>
		</form>	
<?php 
}
					
// ===== Affichage des cours ======	
if (isset ($edt_cours_aff) && !$edt_cours_aff->isEmpty()) { 
?>
		<form class="colonne" action="./saisir_groupe.php" method="post">
			<p>
				<input type="hidden" name="type_selection" value="id_cours"/>
				<label for="id_cours">Cours :</label>
				<select id="id_cours" name="id_cours" class="small">
					<option value='-1'>choisissez un cours</option>
<?php foreach ($edt_cours_aff as $edt_cours) { ?>
					<option value='<?php echo $edt_cours->getIdCours(); ?>'
						<?php if ($id_cours == $edt_cours->getIdCours()) { ?>
							selected='selected'
						<?php } ?>>
						<?php echo $edt_cours->getDescription(); ?>
					</option>
<?php } ?>
				</select>
<?php 
 if (isset ($semaineAff) && !$semaineAff->isEmpty()) {
	if (count($semaineAff) > 1) {
?>
				<label class="invisible" for="id_semaine">Semaine :</label>
				<select id="id_semaine" name="id_semaine" class="small">
					<option value='-1'>choisissez une semaine</option>
<?php foreach ($semaineAff as $semaine) { ?>
					<option value='<?php echo $semaine->getPrimaryKey(); ?>'
						<?php if ($id_semaine == $semaine->getPrimaryKey()) { ?>
							selected='selected'
						<?php } ?>>
						Semaine <?php echo $semaine->getNumEdtSemaine(); ?> <?php echo $semaine->getTypeEdtSemaine(); ?>
						du <?php echo $semaine->getLundi('d/m'); ?>
						au <?php echo $semaine->getSamedi('d/m'); ?>
					</option>
<?php } ?>
				</select>
						
<?php } else { ?>
				<label for="id_semaine">
					Semaine <?php echo $current_semaine->getNumEdtSemaine(); ?>
					<?php echo $current_semaine->getTypeEdtSemaine(); ?>
				</label>
				<input id="id_semaine" type="hidden" name="id_semaine" value="<?php echo $id_semaine; ?>"/>	
<?php } ?>
				<button type="submit">Afficher les élèves</button>
<?php if(isset ($erreurSemaine) && $erreurSemaine ==TRUE) { ?>
				<br />
				Erreur : le cours ne correspond pas au type de semaine.
<?php } ?>
			</p>
		</form>
<?php 	
	}
}
	
// ===== Affichage des classes ======	
if (!$classe_col->isEmpty()) {
?>
		<form class="colonne" action="./saisir_groupe.php" method="post">
			<p>
				<input type="hidden" name="type_selection" value="id_classe"/>
				<label for="id_classe">Classe :</label>
				<select id="id_classe" name="id_classe" class="small">
					<option value='-1'>choisissez une classe</option>
<?php
    foreach ($classe_col as $classe) {
?>
					<option value='<?php echo $classe->getId(); ?>'
						<?php if ($id_classe == $classe->getId()){ ?>
							selected='selected'
						<?php } ?>>
						<?php echo $classe->getNom(); ?>
					</option>
<?php
    }
?>
				</select>
				<?php format_selectbox_heure($utilisateur, $id_creneau, $dt_date_absence_eleve, "classe"); ?>
				<button type="submit">Afficher les élèves</button>
			</p>
		</form>
<?php }
?>
		</div>
<?php if (isset($message_enregistrement)) { ?>
	<p><?php echo($message_enregistrement); ?></p>
<?php
}

//**************** ELEVES *****************

if (TRUE == $_SESSION['showJournee']) {
	include 'lib/saisir_groupe_journee.php';
} else {
if ($eleve_col->isEmpty()) {
?>
    <p>Aucun créneau selectionné</p>
<?php
} else {
?>
    <div>
		<form method="post" action="enregistrement_saisie_groupe.php" id="liste_absence_eleve">
			<p>
				<input type="hidden" name="total_eleves" value="<?php echo($eleve_col->count()); ?>"/>
				<input type="hidden" name="id_aid" value="<?php echo($id_aid); ?>"/>
				<input type="hidden" name="id_groupe" value="<?php echo($id_groupe); ?>"/>
				<input type="hidden" name="id_classe" value="<?php echo($id_classe); ?>"/>
				<input type="hidden" name="id_creneau" value="<?php echo($id_creneau); ?>"/>
				<input type="hidden" name="id_cours" value="<?php echo($id_cours); ?>"/>
				<input type="hidden" name="type_selection" value="<?php echo($type_selection); ?>"/>
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
				<?php if ($current_creneau != null) { ?>
				<label for="heure_debut_appel">de</label>
				<input class="pc88" 
					   name="heure_debut_appel" 
					   id="heure_debut_appel" 
					   value="<?php
				    if (isset($_POST['heure_debut_appel'])) {$heure_debut_appel = ($_POST['heure_debut_appel']);}
				    elseif (isset($_GET['heure_debut_appel'])) {$heure_debut_appel = ($_GET['heure_debut_appel']);}
				    elseif ($current_cours != null) {
					if ($current_cours->getHeureDebut('s') > 0) {
					    //on arrondi le debut de saisie au-dessus pour ne pas depasser l'heure du cours
					    if ($current_cours->getHeureDebut('i') == 59) {
						$heure_debut_appel = ($current_cours->getHeureDebut('H') + 1).':00';
					    } else {
						$heure_debut_appel = $current_cours->getHeureDebut('H').':'.($current_cours->getHeureDebut('i') + 1);
					    }
					} else {
					    $heure_debut_appel = $current_cours->getHeureDebut('H:i');
					}
				    } elseif ($current_creneau != null) {
					$heure_debut_appel = $current_creneau->getHeuredebutDefiniePeriode('H:i');
				    };
				    echo $heure_debut_appel;
				?>"
				   type="text" 
				   maxlength="5" 
				   size="4"/>
				<label for="heure_fin_appel">à</label>
				<input class="pc88" 
					   name="heure_fin_appel" 
					   id="heure_fin_appel" 
					   value="<?php
				    if (isset($_POST['heure_fin_appel'])) {$heure_fin_appel = ($_POST['heure_fin_appel']);}
				    elseif (isset($_GET['heure_fin_appel'])) {$heure_fin_appel = ($_GET['heure_fin_appel']);}
				    elseif ($current_cours != null) {$heure_fin_appel = $current_cours->getHeureFin('H:i');}
				    elseif ($current_creneau != null) { $heure_fin_appel = $current_creneau->getHeurefinDefiniePeriode('H:i');};
				    echo $heure_fin_appel;
				?>" 
					   type="text" 
					   maxlength="5" 
					   size="4"/>
				<button onclick="SetAllTextFields('liste_absence_eleve', 'heure_debut_absence_eleve','',document.getElementById('heure_debut_appel').value);
							    SetAllTextFields('liste_absence_eleve', 'heure_fin_absence_eleve','',document.getElementById('heure_fin_appel').value);
							    return false;"
                                        id ="bouton_changer_horaire">
					Changer
				</button>
				<?php } ?>
				<br/>
				(les élèves non cochés seront considérés présents)
			</p>
			<p class="choix_fin center">
				<input value="Enregistrer" 
					   name="Valider" 
					   type="submit"  
					   onclick="this.form.submit();this.disabled=true;this.value='En cours'" />
			</p>
			<?php if ($utilisateur->getStatut() == 'professeur' && getSettingValue("active_cahiers_texte")=='y') { ?>
			<p class="choix_fin center">
				<input value="Enregistrer et passer au cahier de texte" name="cahier_texte" type="submit"/>
			</p>
			<?php } ?>
<!-- Afichage du tableau de la liste des élèves -->
<!-- Legende du tableau-->
			<p class="center"><?php echo $eleve_col->count(); ?> élèves (<?php echo $chaine_coche_decoche_regime;
?>).</p>
<!-- Fin de la legende -->
			<table class="tb_absences">
				<caption class="invisible no_print">Absences</caption>
				<thead>
					<tr class="titre_tableau_gestion" style="white-space: nowrap;">
						<th class="center<?php
							if ($afficheEleve['0']['creneau_courant'] != 0) {
								echo ' noSmartphone';
							} ?>" >Veille</th>
						<th class="center" abbr="élèves">Liste des élèves</th>
						<th colspan="<?php echo EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime()->count(); ?>" 
							class="th_abs_suivi" abbr="Créneaux">Suivi sur la journée</th>
					</tr>
					<tr>
						<td></td>
						<td class="center<?php
							if ($afficheEleve['0']['creneau_courant'] != 0) {
								echo ' noSmartphone';
							} ?>" ></td>
						<?php $i=0;
						foreach(EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $edt_creneau){ ?>
						<td class="td_nom_creneau<?php
							if (($afficheEleve['0']['creneau_courant'] != $i) 
									&& ($afficheEleve['0']['creneau_courant']  != ($i + 1)) 
									&& ($afficheEleve['0']['creneau_courant']  != ($i + 2))) {
								echo ' noSmartphone';
							}?> center">
							<?php
								// 20120618
								if($afficheEleve['0']['creneau_courant'] == $i) {
									//echo "<a href=\"#\" onclick=\"cocher_decocher_abs_eleves();return false;\">".$edt_creneau->getNomDefiniePeriode()."</a>";
									echo "<a href=\"javascript:cocher_decocher_abs_eleves();\">".$edt_creneau->getNomDefiniePeriode()."</a>";
									//echo $chaine_coche_decoche_regime;
								}
								else {
									echo $edt_creneau->getNomDefiniePeriode();
								}
							?>
						</td>
				<?php $i++;

				}?>
					</tr>
				</thead>
				<tbody>
<?php 
	$compteur_eleve=0;
	foreach($afficheEleve as $eleve) {
		$compteur_eleve++;
?>
					<tr class='<?php echo $eleve['background'];?>'>
						<td class = "<?php echo($eleve['class_hier']);
							if ($eleve['creneau_courant'] != 0) {
								echo ' noSmartphone';
							} ?>">
                                                        <span class="description" title="<?php echo htmlspecialchars($eleve['bulle_hier']); ?>"><?php echo $eleve['text_hier']; ?></span>
						</td>
						<td class='td_abs_eleves'>
							<input type="hidden" 
								   name="id_eleve_absent[<?php echo $eleve['position']; ?>]" 
								   value="<?php echo $eleve['id']; ?>" />
							<span class="td_abs_eleves">
								<?php echo strtoupper($eleve['nom']).' '.ucfirst($eleve['prenom']).' ('.$eleve['civilite'].')'; ?>
								<?php if (isset ($eleve['classe'])) echo $eleve['classe']; ?>
							</span>
<?php if (isset ($eleve['accesFiche'])) { ?>
							<a href='../eleves/visu_eleve.php?ele_login=<?php echo $eleve['accesFiche']; ?>&amp;onglet=responsables&amp;quitter_la_page=y' target='_blank' >
								(voir&nbsp;fiche)
							</a>
							<?php
								/*
								// DEBUG: 20120618
								echo "<pre>";
								echo print_r($eleve);
								echo "</pre>";
								*/
							?>
<?php } ?>
						</td>
<?php 
for($i = 0; $i<$eleve['creneaux_possibles']; $i++){ ?>
						<td colspan="<?php echo $eleve['nb_creneaux_a_saisir'][$i]; ?>" 
							class="<?php echo $eleve['style'][$i];
							if (($eleve['creneau_courant'] != $i) 
									&& ($eleve['creneau_courant']  != ($i + 1)) 
									&& ($eleve['creneau_courant']  != ($i + 2))) {
								echo ' noSmartphone';
							}
								?>">
<?php if (isset ($eleve['saisie'][$i]) && !empty ($eleve['saisie'][$i])) { ?>
							<a class="saisieAnte" href='visu_saisie.php?id_saisie=<?php echo $eleve['saisie'][$i]['primaryKey']; ?>'>
								Modif.&nbsp;saisie de <?php echo $eleve['saisie'][$i]['createdAt']; ?>

<?php
		$besoin_echo_virgule = false;
		if (isset ($eleve['saisie'][$i]['traitements'])) {
		foreach ($eleve['saisie'][$i]['traitements'] as $bou_traitement) {
			if ($besoin_echo_virgule) {
				echo ', ';
			} 
?>
								<?php echo $bou_traitement; ?>
<?php 
		$besoin_echo_virgule = true;
	}
}
?>
							</a>
							<br />
<?php } elseif (isset ($eleve['saisieDescription'][$i]) && count($eleve['saisieDescription'][$i])) {
	$hover = 'traitements - ';
	foreach ($eleve['saisieDescription'][$i] as $text) {
		$hover .= $text.'; ';
	}
?>
							<span class="description" title="<?php echo htmlspecialchars($hover); ?>">T</span>
<?php } 
if (isset ($eleve['erreurEnregistre'][$i])) { ?>	
	Erreur : <?php echo $eleve['erreurEnregistre'][$i]; ?>
<?php }
if (isset ($eleve['type_autorises'][$i])) {
        $radioButtonType = FALSE;
        foreach ($eleve['type_autorises'][$i] as $type) {
                if ($type['modeInterface'] == AbsenceEleveType::MODE_INTERFACE_CHECKBOX
                                || $type['modeInterface'] == AbsenceEleveType::MODE_INTERFACE_CHECKBOX_HIDDEN) {
                        $radioButtonType = TRUE;
                        break;
                }
        }
        $radioButtonTypeOnlyHidden = $radioButtonType;
        foreach ($eleve['type_autorises'][$i] as $type) {
                if ($type['modeInterface'] == AbsenceEleveType::MODE_INTERFACE_CHECKBOX) {
                        $radioButtonTypeOnlyHidden = false;
                        break;
                }
        }
        $radioButtonTypeHasHidden = false;
        foreach ($eleve['type_autorises'][$i] as $type) {
                if ($type['modeInterface'] == AbsenceEleveType::MODE_INTERFACE_CHECKBOX_HIDDEN) {
                        $radioButtonTypeHasHidden = true;
                        break;
                }
        } ?>

                                                    <label class="invisible" for="type_absence_eleve_<?php echo $eleve['position']; ?>">Type d'absence</label>
						    <select class="selects"
									onchange="this.form.elements['active_absence_eleve_<?php echo $eleve['position']; ?>'].checked = (this.options[this.selectedIndex].value != -1);
                                                                                    <?php if ($radioButtonType) {?>this.form.elements['radio_sans_type_absence_eleve_<?php echo $eleve['position']; ?>'].checked = true; <?php } ?>"
									name="type_absence_eleve[<?php echo $eleve['position']; ?>]"
									id="liste_type_absence_eleve_<?php echo $eleve['position']; ?>" >
								<option class="pc88" value="-1"> </option>
<?php foreach ($eleve['type_autorises'][$i] as $type) { ?>
								<option class="pc88" value="<?php echo $type['type']; ?>">
								<?php echo $type['nom']; ?> 
								</option>
<?php } ?>
							</select>
<?php 
foreach ($eleve['type_autorises'][$i] as $type) {
	if ($type['modeInterface'] == AbsenceEleveType::MODE_INTERFACE_CHECKBOX
			|| $type['modeInterface'] == AbsenceEleveType::MODE_INTERFACE_CHECKBOX_HIDDEN) { ?>
							<p>
								<input type="radio"
                                                                            onchange="this.form.elements['liste_type_absence_eleve_<?php echo $eleve['position']; ?>'].selectedIndex = 0; this.form.elements['active_absence_eleve_<?php echo $eleve['position']; ?>'].checked = true;"
									   id="radio_<?php if ($type['modeInterface'] == AbsenceEleveType::MODE_INTERFACE_CHECKBOX_HIDDEN) echo 'hidden_'?>type_absence_eleve_<?php echo $type['type']; ?>_<?php echo $eleve['position']; ?>"
									   name="check[<?php echo $eleve['position']; ?>]"
									   value="<?php echo $type['type']; ?>"/>
								<label for ="radio_<?php if ($type['modeInterface'] == AbsenceEleveType::MODE_INTERFACE_CHECKBOX_HIDDEN) echo 'hidden_'?>type_absence_eleve_<?php echo $type['type']; ?>_<?php echo $eleve['position']; ?>">
									<?php echo $type['nom']; ?>
								</label>
							</p>
<?php   }
    }//on affiche une case vide pour saisier un élève sans option
    if ($radioButtonType) {?>
							<p>
                                                                <input type="radio"
                                                                        onchange="this.form.elements['active_absence_eleve_<?php echo $eleve['position']; ?>'].checked = false;"
                                                                        id="radio_sans_type_absence_eleve_<?php echo $eleve['position']; ?>"
                                                                        name="check[<?php echo $eleve['position']; ?>]"
                                                                        value="<?php echo false ?>"
                                                                        checked='checked' />
								<label for ="faux_<?php echo $eleve['position']; ?>">
								</label>
                                                        </p>
<?php 	}
}
if ($eleve['creneau_courant'] == $i) { ?>
							<label id="label_active_absence_eleve_<?php echo $eleve['position']; ?>" class="invisible smartphone" 
								   for="active_absence_eleve_<?php echo $eleve['position']; ?>">
								Abs.
							</label>
							<input class="pc88"
								   onclick="click_active_absence('<?php echo $eleve['position']; ?>')"
								   id="active_absence_eleve_<?php echo $eleve['position']; ?>"
								   name="active_absence_eleve[<?php echo $eleve['position']; ?>]"
								   value="1"
								   type="checkbox" />
								<label class="invisible" for="heure_debut_absence_eleve_<?php echo $eleve['position']; ?>">de</label>
								<input class="pc88"
									   onchange="this.form.elements['active_absence_eleve[<?php echo $eleve['position']; ?>]'].checked = true;"
									   name="heure_debut_absence_eleve[<?php echo $eleve['position']; ?>]"
									   id="heure_debut_absence_eleve_<?php echo $eleve['position']; ?>"
									   value="<?php echo $heure_debut_appel; ?>"
									   type="text"
									   maxlength="5"
									   size="4"/>
								<input class="pc88"
									   name="date_debut_absence_eleve[<?php echo $eleve['position']; ?>]"
									   value="<?php echo $dt_date_absence_eleve->format('d/m/Y') ?>"
									   type="hidden"/>
								<label class="invisible" for="heure_fin_absence_eleve_<?php echo $eleve['position']; ?>">à</label>
								<input class="pc88"
									   id="heure_fin_absence_eleve_<?php echo $eleve['position']; ?>"
									   onchange="this.form.elements['active_absence_eleve[<?php echo $eleve['position']; ?>]'].checked = true;"
									   name="heure_fin_absence_eleve[<?php echo $eleve['position']; ?>]"
									   value="<?php echo $heure_fin_appel ?>"
									   type="text"
									   maxlength="5"
									   size="4"/>
								<input class="pc88"
									   name="date_fin_absence_eleve[<?php echo $eleve['position']; ?>]"
									   value="<?php echo $dt_date_absence_eleve->format('d/m/Y') ?>"
									   type="hidden"/>
<?php } ?>
						</td>
<?php 
	if ($eleve['nb_creneaux_a_saisir'][$i] > 0) {
		$i = $i + $eleve['nb_creneaux_a_saisir'][$i] - 1;
	}
	
} ?>
<?php if (isset ($eleve['nom_photo'])) { ?>
						<td>
							<img src="<?php echo $eleve['nom_photo']; ?>"
								 class="trombine"
								 alt="" 
								 title="" />
							
						</td>
<?php } ?>
<?php 				if ($current_creneau != null) {
?>
						<td class="commentaire">
							<label for="commentaire_absence_eleve_<?php echo $eleve['position']; ?>">Commentaire :</label>
							<input id="commentaire_absence_eleve_<?php echo $eleve['position']; ?>"
									name="commentaire_absence_eleve[<?php echo $eleve['position']; ?>]" 
									value="" 
									type="text" 
									maxlength="150" 
									size="13"/>
						</td>
<?php } ?>
					</tr>
<?php } ?>	
				</tbody>
			</table>
		<p class="choix_fin center">
			<input value="Enregistrer"
				   name="Valider"
				   type="submit"
				   onclick="this.form.submit();this.disabled=true;this.value='En cours'" />
		</p>
<?php
if ($utilisateur->getStatut() == 'professeur' && getSettingValue("active_cahiers_texte")=='y') {
?>
    <p class="choix_fin">
	    <input value="Enregistrer et passer au cahier de texte" name="cahier_texte" type="submit"/>
    </p>
<?php
}
?>
	</form>
</div>

<?php
	}
}
?>
</div>
<?php
// 20120618
if(isset($compteur_eleve)) {
	echo "<script type='text/javascript'>
	var etat_tout_cocher=false;
	function cocher_decocher_abs_eleves() {
		if(etat_tout_cocher==false) {
			etat_tout_cocher=true;
		}
		else {
			etat_tout_cocher=false;
		}
		for(i=0;i<".$compteur_eleve.";i++) {
			if(document.getElementById('active_absence_eleve_'+i)) {
				document.getElementById('active_absence_eleve_'+i).checked=etat_tout_cocher;
			}
		}
	}
	
	$js_var_coche_decoche_regime
	function cocher_decocher_regime(num) {
		if(etat_cocher_regime[num]==false) {
			etat_cocher_regime[num]=true;
		}
		else {
			etat_cocher_regime[num]=false;
		}

		tab=eval('ele_regime_'+num);
		//alert(tab.length);
		for(i=0;i<tab.length;i++) {
			if(document.getElementById('active_absence_eleve_'+tab[i])) {
				document.getElementById('active_absence_eleve_'+tab[i]).checked=etat_cocher_regime[num];

				if(document.getElementById('liste_type_absence_eleve_'+tab[i])) {
					if(etat_cocher_regime[num]==false) {
						document.getElementById('liste_type_absence_eleve_'+tab[i]).selectedIndex=0;
					}
					else {
						for(j=0;j<document.getElementById('liste_type_absence_eleve_'+tab[i]).options.length;j++) {
							/*
							if(i==0) {
								alert(document.getElementById('liste_type_absence_eleve_'+tab[i]).options[j].innerHTML+' et '+nom_regime[num])
							}
							*/
							if(document.getElementById('liste_type_absence_eleve_'+tab[i]).options[j].innerHTML.replace(/^\s+/g,'').replace(/\s+$/g,'')==nom_regime[num].replace(/^\s+/g,'').replace(/\s+$/g,'')) {
								//if(i<3) {alert('j='+j)}
								document.getElementById('liste_type_absence_eleve_'+tab[i]).selectedIndex=j;
							}
						}
					}
				}
			}
		}
	}

	if(document.getElementById('effectifs_regimes_non_js')) {
		document.getElementById('effectifs_regimes_non_js').style.display='none';
	}
	if(document.getElementById('effectifs_regimes_js')) {
		document.getElementById('effectifs_regimes_js').style.display='';
	}
</script>\n";
}

if (isset($radioButtonType)) {
    $javascript_footer_texte_specifique = '<script type="text/javascript">';
    if ((isset($radioButtonTypeOnlyHidden))&&($radioButtonTypeOnlyHidden)) {
        $javascript_footer_texte_specifique .= '$$(\'input[type="radio"][id^="radio_sans_type_absence_eleve_"]\').each(Element.hide);';
    }
    $javascript_footer_texte_specifique .= '$$(\'input[type="radio"][id^="radio_hidden_"]\').each(Element.hide);';
    $javascript_footer_texte_specifique .= '$$(\'label[for^="radio_hidden_"]\').each(Element.hide);';

    $javascript_footer_texte_specifique .= '$(\'bouton_changer_horaire\').insert({after : \'';
    $javascript_footer_texte_specifique .= ' 				<button id="bouton_afficher_radio_hidden" onclick="return false;">';
    $javascript_footer_texte_specifique .= ' 					Aff. cases cachées';
    $javascript_footer_texte_specifique .= ' 				</button>\'';
    $javascript_footer_texte_specifique .= '});';
    $javascript_footer_texte_specifique .= '$(\'bouton_afficher_radio_hidden\').observe(\'click\', function( event )
    {
    $$(\'input[type="radio"][id^="radio_sans_type_absence_eleve_"]\').each(Element.show);
    $$(\'input[type="radio"][id^="radio_hidden_"]\').each(Element.show);
    $$(\'label[for^="radio_hidden_"]\').each(Element.show);
    });';
    $javascript_footer_texte_specifique .= '</script>';
}
require_once("../lib/footer.inc.php");

// $affiche_debug=debug_var();
 
