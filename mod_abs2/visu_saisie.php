<?php
/**
 *
 *
 * Copyright 2010 Josselin Jacquard
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

if ($utilisateur->getStatut()=="professeur" &&  getSettingValue("active_module_absence_professeur")!='y') {
    die("Le module n'est pas activé.");
}

if(isset($_GET['test_heure_ouverture'])) {
	$date_debut=$_GET['date_debut'];
	$heure_debut=$_GET['heure_debut'];

	if(preg_match("#[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}#", $date_debut)) {
		$tab=explode("/", $date_debut);

		$jour=$tab[0];
		$mois=$tab[1];
		$annee=$tab[2];
	}
	elseif(preg_match("/[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}/", $date_debut)) {
		$tab=explode("-", $date_debut);

		$jour=$tab[2];
		$mois=$tab[1];
		$annee=$tab[0];
	}

	if((isset($jour))&&
	(preg_match("/[0-9]{1,2}:[0-9]{1,2}/", $heure_debut))) {

		$tab=explode(":", $heure_debut);
		$h=$tab[0];
		$min=$tab[1];

		$ts=mktime($h, $min, 0, $mois, $jour, $annee);

		$num_jour=strftime("%u", $ts);

		$tab_sem[1] = 'lundi';
		$tab_sem[2] = 'mardi';
		$tab_sem[3] = 'mercredi';
		$tab_sem[4] = 'jeudi';
		$tab_sem[5] = 'vendredi';
		$tab_sem[6] = 'samedi';
		$tab_sem[7] = 'dimanche';

		$sql="SELECT ouverture_horaire_etablissement FROM horaires_etablissement WHERE jour_horaire_etablissement='".$tab_sem[$num_jour]."';";
		//echo "$sql<br />";
		$res=mysqli_query($mysqli, $sql);
		if($res->num_rows > 0) {
			$lig=mysqli_fetch_object($res);

			if(strftime("%H:%M:%S", $ts)<$lig->ouverture_horaire_etablissement) {
				echo " <img src='../images/icons/flag.png' class='icone16' alt='Anomalie' title=\"L'heure de début est antérieure à l'heure d'ouverture de l'établissement.
Dans le cas d'une absence ou d'un retard, il se peut qu'il ne soit pas pris en compte dans le décompte.\" />";
			}
		}
	}
	else {
		//echo "\$date_debut=$date_debut<br />";
		//echo "\$heure_debut=$heure_debut<br />";
	}

	die();
}

//récupération des paramètres de la requète
$id_saisie = isset($_POST["id_saisie"]) ? $_POST["id_saisie"] :(isset($_GET["id_saisie"]) ? $_GET["id_saisie"] :(isset($_SESSION["id_saisie"]) ? $_SESSION["id_saisie"] : NULL));
$menu = isset($_POST["menu"]) ? $_POST["menu"] :(isset($_GET["menu"]) ? $_GET["menu"] : NULL);
if (isset($id_saisie) && $id_saisie != null) $_SESSION['id_saisie'] = $id_saisie;

//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
if(!$menu){
$titre_page = "Les absences";
}
//$utilisation_jsdivdrag = "non";
$dojo = true;
$_SESSION['cacher_header'] = "y";

require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
if(!$menu){
include('menu_abs2.inc.php');
}
echo "<div class='css-panes' style='background-color:#cae7cb;' id='containDiv' style='overflow : auto;'>\n";


$saisie = AbsenceEleveSaisieQuery::create()->includeDeleted()->findPk($id_saisie);
if ($saisie == null) {
    $criteria = new Criteria();
    $criteria->addDescendingOrderByColumn(AbsenceEleveSaisiePeer::UPDATED_AT);
    $criteria->setLimit(1);
    $saisie_col = $utilisateur->getAbsenceEleveSaisiesJoinEdtCreneau($criteria);
    $saisie = $saisie_col->getFirst();
    if ($saisie == null) {
	echo "Saisie non trouvée";
	die();
    }
}


//on va mettre dans la session l'identifiant de la saisie pour faciliter la navigation par onglet
if ($saisie != null) {
    $_SESSION['id_saisie_visu'] = $saisie->getPrimaryKey();
}


//la saisie est-elle modifiable ?
//Une saisie est modifiable ssi : elle appartient à l'utilisateur de la session si c'est un prof,
//elle date de moins d'une heure et l'option a ete coché partie admin
$modifiable = true;
if ($utilisateur->getStatut() == 'professeur') {    
	if (!getSettingValue("abs2_modification_saisie_une_heure")=='y' || !$saisie->getUtilisateurId() == $utilisateur->getPrimaryKey() || !($saisie->getVersionCreatedAt('U') > (time() - 3600))) {
	   $modifiable = false;
	}
} else {
	if ($utilisateur->getStatut() != 'cpe' && $utilisateur->getStatut() != 'scolarite') {
	    $modifiable = false;
	}
}

if (!$modifiable) {
    echo "La saisie n'est pas modifiable<br/>";
}

if (isset($message_enregistrement)) {
    echo $message_enregistrement;
}

echo '<table class="normal">';
echo '<tbody>';
echo '<tr><td>';
echo 'N° de saisie : ';
echo '</td><td>';
echo $saisie->getPrimaryKey();
    if ($saisie->getDeletedAt()!=null) {
    	echo ' <font color="red">(supprimée le ';
    	echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getDeletedAt('U')));
    	$suppr_utilisateur = UtilisateurProfessionnelQuery::create()->findPK($saisie->getDeletedBy());
    	if ($suppr_utilisateur != null) {
    		echo ' par '.  $suppr_utilisateur->getCivilite().' '.$suppr_utilisateur->getNom().' '.mb_substr($suppr_utilisateur->getPrenom(), 0, 1).'.';;
    	}
    	echo ')</font> ';
    }
echo '</td><td>';

$temoin_plus_dans_le_grp="n";
if (($saisie->getEleve() != null)&&($saisie->getGroupe() != null)) {
	if(!is_eleve_du_groupe($saisie->getEleve()->getLogin(), $saisie->getGroupe()->getId())) {
		echo "<div style='float:right; width:22px;'><img src='../images/icons/ico_attention.png' width='22' height='19' alt='Attention' title=\"L'élève n'est plus membre du groupe ".$saisie->getGroupe()->getNameAvecClasses()." actuellement.
Il en a peut-être été membre plus tôt dans l'année.
Mais il n'en n'est plus membre aujourd'hui.

Si cette saisie est une erreur, vous devriez la traiter
pour la marquer en 'Erreur de saisie'.\" /></div>";
		$temoin_plus_dans_le_grp="y";
	}
}

if ($modifiable) {
	// Il faudrait pouvoir supprimer des saisies même si l'élève a été viré du groupe, mais on a alors une erreur au niveau des tests sur l'objet propel
	if($temoin_plus_dans_le_grp=="n") {
		echo '<form dojoType="dijit.form.Form" jsId="suppression_restauration" id="suppression_restauration"  method="post" action="./enregistrement_modif_saisie.php">';
		echo '<input type="hidden" name="id_saisie" value="' . $saisie->getPrimaryKey() . '"/>';
		echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
		if ($saisie->getDeletedAt() == null) {
			echo '<img src="../images/delete16.png"/>';
			//echo '<a href="enregistrement_modif_saisie.php?id_saisie='.$saisie->getPrimaryKey().'&action=suppression">';
			echo'<input type="hidden" name="action" value="suppression">';
			echo '<button dojoType="dijit.form.Button" type="submit">Supprimer la saisie</button>';
			//echo '</a>';
		} else {
			//on autorise la restauration pour un autre que cpe ou scola uniquement si c'est l'utilisateur en cours qui a fait auparavant la suppression
			if ($utilisateur->getStatut() == "cpe" || $utilisateur->getStatut() == "scolarite"
			|| ($saisie->getDeletedBy() == $utilisateur->getLogin())) {
				//echo '<a href="enregistrement_modif_saisie.php?id_saisie='.$saisie->getPrimaryKey().'&action=restauration">';
				echo'<input type="hidden" name="action" value="restauration">';
				echo '<button dojoType="dijit.form.Button" type="submit">Restaurer la saisie</button>';
				//echo '</a>';
			}
		}
		echo'</form>';
	}
}
echo '</td></tr>';
echo '</tbody>';

echo '</table>';
echo '<form dojoType="dijit.form.Form" jsId="modification" id="modification"  method="post" action="./enregistrement_modif_saisie.php">';
echo '<input type="hidden" name="id_saisie" value="' . $saisie->getPrimaryKey() . '"/>';
echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
echo '<table class="normal">';
echo '<tbody>';
echo '<tr>';
if ($saisie->getEleve() == null) {
    echo '<td colspan="3">';
    echo "Marqueur d'appel effectué";
    echo '</td>';
} else {
    echo '<td>Élève : </td>';
    echo '<td colspan="2">';
    echo $saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom();
    echo ' '.$saisie->getEleve()->getClasseNom();
    if ((getSettingValue("active_module_trombinoscopes")=='y') && $saisie->getEleve() != null) {
	$nom_photo = $saisie->getEleve()->getNomPhoto(1);
	$photos = $nom_photo;
	//if (($nom_photo == "") or (!(file_exists($photos)))) {
	if (($nom_photo == NULL) or (!(file_exists($photos)))) {
		$photos = "../mod_trombinoscopes/images/trombivide.jpg";
	}
	$valeur = redimensionne_image_petit($photos);
	echo ' <img src="'.$photos.'" style="width: '.$valeur[0].'px; height: '.$valeur[1].'px; border: 0px; vertical-align: middle;" alt="" title="" />';
    }
    if ($utilisateur->getAccesFicheEleve($saisie->getEleve())) {
	echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."&amp;onglet=responsable&amp;quitter_la_page=y' target='_blank'>";
	echo ' (voir fiche)';
	echo "</a>";
    }
echo '</td>';
}
echo '</tr>';

if ($saisie->getClasse() != null) {
    echo '<tr><td>';
    echo 'Classe : ';
    echo '</td><td colspan="2">';
    echo $saisie->getClasse()->getNom();
    echo '</td></tr>';
}

if ($saisie->getGroupe() != null) {
    echo '<tr><td>';
    echo 'Groupe : ';
    echo '</td><td colspan="2">';
    echo $saisie->getGroupe()->getNameAvecClasses();
    echo '</td></tr>';
}

if ($saisie->getAidDetails() != null) {
    echo '<tr><td>';
    echo 'Aid : ';
    echo '</td><td colspan="2">';
    echo $saisie->getAidDetails()->getNom();
    echo '</td></tr>';
}

if ($saisie->getEdtEmplacementCours() != null) {
    echo '<tr><td>';
    echo 'Cours : ';
    echo '</td><td colspan="2">';
    echo $saisie->getEdtEmplacementCours()->getDescription();
    echo '</td></tr>';
}

if ($saisie->getEdtCreneau() != null) {
    echo '<tr><td>';
    echo 'Créneau : ';
    echo '</td><td colspan="2">';
    echo $saisie->getEdtCreneau()->getDescription();
    echo '</td></tr>';
}

echo '<tr><td>';
echo 'Début : ';
echo '</td><td colspan="2">';
if (!$modifiable || $saisie->getDeletedAt() != null ) {
    echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getDebutAbs('U')));
} else {
    echo '<nobr><input name="heure_debut" id="heure_debut" value="'.$saisie->getDebutAbs("H:i").'" type="text" maxlength="5" size="4" onkeydown="clavier_heure(this.id,event);" autocomplete="off" title="Vous pouvez modifier l\'heure en utilisant les flèches Haut/Bas et PageUp/PageDown du clavier" onchange="teste_validite_heure_debut_abs()" onblur="teste_validite_heure_debut_abs()" />&nbsp;';
    if ($utilisateur->getStatut() == 'professeur') {//on autorise pas au professeur a changer la date
	echo (strftime(" %a %d/%m/%Y", $saisie->getDebutAbs('U')));
	echo '<input name="date_debut" id="trigger_calendrier_debut" value="'.$saisie->getDebutAbs('d/m/Y').'" type="hidden"/></nobr> ';
?>
<button type="button" style="cursor:pointer;" onclick="heureActuelle('heure_debut')">
  Maintenant
</button>
<?php
    } else {
	echo '<input id="trigger_calendrier_debut" name="date_debut"  type="text" dojoType="dijit.form.DateTextBox"  value="'. $saisie->getDebutAbs('Y-m-d').'"  style="width : 8em"/></nobr> ';

echo choix_heure(array('heure_debut', 'heure_fin'), 'div_choix_heure');

    //    echo '<img id="trigger_date_debut" src="../images/icons/calendrier.gif"/>';
	echo '</nobr>';
	/*echo '
	<script type="text/javascript">
	    Calendar.setup({
		inputField     :    "trigger_calendrier_debut",     // id of the input field
		ifFormat       :    "%d/%m/%Y",      // format of the input field
		button         :    "trigger_calendrier_debut",  // trigger for the calendar (button ID)
		align          :    "Tl",           // alignment (defaults to "Bl")
		singleClick    :    true
	    });
	</script>';*/
    }
}
echo '<span id="commentaire_heure_debut_abs"></span></td></tr>';

echo '<tr><td>';
echo 'Fin : ';
echo '</td><td colspan="2">';
if (!$modifiable || $saisie->getDeletedAt() != null) {
    echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getFinAbs('U')));
} else {
    echo '<nobr><input name="heure_fin" id="heure_fin" value="'.$saisie->getFinAbs("H:i").'" type="text" maxlength="5" size="4" onkeydown="clavier_heure(this.id,event);" autocomplete="off" title="Vous pouvez modifier l\'heure en utilisant les flèches Haut/Bas et PageUp/PageDown du clavier" />&nbsp;';
    //if ($utilisateur->getStatut() == 'professeur' && getSettingValue("abs2_saisie_prof_decale") != 'y') {
    if ($utilisateur->getStatut() == 'professeur') {
	echo (strftime(" %a %d/%m/%Y", $saisie->getFinAbs('U')));
	echo '<input name="date_fin" value="'.$saisie->getFinAbs('d/m/Y').'" type="hidden"/></nobr> ';
?>
<button type="button" style="cursor:pointer;" onclick="heureActuelle('heure_fin')">
  Maintenant
</button>
<?php
    } else {
	echo '<input id="trigger_calendrier_fin" name="date_fin" type="text" dojoType="dijit.form.DateTextBox"  value="'. $saisie->getFinAbs('Y-m-d').'"  style="width : 8em"/></nobr> ';

	//echo '<img id="trigger_date_debut" src="../images/icons/calendrier.gif"/>';
	echo '</nobr>';
	/*echo '
	<script type="text/javascript">
	    Calendar.setup({
		inputField     :    "trigger_calendrier_fin",     // id of the input field
		ifFormat       :    "%d/%m/%Y",      // format of the input field
		button         :    "trigger_calendrier_fin",  // trigger for the calendar (button ID)
		align          :    "Tl",           // alignment (defaults to "Bl")
		singleClick    :    true
	    });
	</script>';*/
    }
}
echo '</td></tr>';

echo '<tr><td>';
echo 'Traitement : ';
echo '</td><td style="background-color:#ebedb5;" colspan="2">';
$type_autorises = AbsenceEleveTypeStatutAutoriseQuery::create()->filterByStatut($utilisateur->getStatut())->useAbsenceEleveTypeQuery()->orderBySortableRank()->endUse()->find();
$total_traitements_modifiable = 0;
$total_traitements_modifiable_non_prof = 0;
$tab_traitements_deja_affiches=array();
foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
	if(!in_array($traitement->getId(), $tab_traitements_deja_affiches)) {
		//si c'est un traitement créé par un prof on va afficher une select box de modification si possible
		echo "<nobr>";
		if ($utilisateur->getStatut() == 'professeur' && $traitement->getUtilisateurId() == $utilisateur->getPrimaryKey() && $traitement->getModifiable()) {
		$total_traitements_modifiable = $total_traitements_modifiable + 1;
		$type_autorises->getFirst();
		echo $traitement->getDescription().' : ';
		if ($type_autorises->count() != 0) {
			echo '<input type="hidden" name="id_traitement[';
			echo ($total_traitements_modifiable - 1);
			echo ']" value="'.$traitement->getId().'"/>';
			echo ("<select name=\"type_traitement[");
			echo ($total_traitements_modifiable - 1);
			echo ("]\">");
			echo "<option value='-1'></option>\n";
			foreach ($type_autorises as $type) {
				//$type = new AbsenceEleveTypeStatutAutorise();
				echo "<option value='".$type->getAbsenceEleveType()->getId()."'";
				if ($type->getAbsenceEleveType()->getId() == $traitement->getATypeId()) {
					echo "selected";
				}
				echo ">";
				echo $type->getAbsenceEleveType()->getNom();
				echo "</option>\n";
			}
			echo "</select>";
			echo '<button dojoType="dijit.form.Button" type="submit" name="modifier_type" value="vrai">Mod. le type</button>';
		}
		}else {
		if ($utilisateur->getStatut() != 'professeur') {
			$total_traitements_modifiable_non_prof++;
			echo "<a href='visu_traitement.php?id_traitement=".$traitement->getId()."&id_saisie_appel=".$id_saisie."";
		    if($menu){
		            echo"&menu=false";
		        } 
		    echo"' style='display: block; height: 100%;'> ";
			echo $traitement->getDescription();
			echo "</a>";
		} else {
			echo $traitement->getDescription();
		}
		}
		echo "</nobr>";
		echo "<br/>";
		$tab_traitements_deja_affiches[]=$traitement->getId();
	}
}
//on autorise un ajout rapide seulement si il n'y a aucun traitement rapidement modifiable
if ($total_traitements_modifiable == 0 && $utilisateur->getStatut() == 'professeur') {
    echo ("<select name=\"ajout_type_absence\">");
    echo "<option value='-1'></option>\n";
    foreach ($type_autorises as $type) {
	//$type = new AbsenceEleveTypeStatutAutorise();
	    echo "<option value='".$type->getAbsenceEleveType()->getId()."'";
	    echo ">";
	    echo $type->getAbsenceEleveType()->getNom();
	    echo "</option>\n";
    }
    echo "</select>";
    echo '<button dojoType="dijit.form.Button" type="submit" name="modifier_type" value="vrai">Ajouter</button>';
}

echo '<input type="hidden" name="total_traitements" value="'.$total_traitements_modifiable.'"/>';

if ($saisie->getManquementObligationPresenceEnglobante()){
    echo 'globalement manquement à l\'obligation de présence<br/>';
    if ($saisie->getJustifieeEnglobante()){
        echo 'globalement justifiée<br/>';
    }
    if ($saisie->getNotifieeEnglobante()){
        echo 'globalement notifiée<br/>';
    }
}


echo '</td></tr>';

echo '<tr><td>';
echo 'Notification : ';
echo '</td><td>';
echo '<table style="background-color:#c7e3ec;">';
foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
foreach ($traitement->getAbsenceEleveNotifications() as $notification) {
    echo '<tr><td>';
    echo "<a href='visu_notification.php?id_notification=".$notification->getId()."";
    if($menu){
                echo"&menu=false";
            } 
    echo"' style='display: block; height: 100%;'> ";
    if ($notification->getDateEnvoi() != null) {
	echo (strftime("%a %d/%m/%Y %H:%M", $notification->getDateEnvoi('U')));
    } else {
	echo (strftime("%a %d/%m/%Y %H:%M", $notification->getCreatedAt('U')));
    }
    if ($notification->getTypeNotification() != null) {
	echo ', type : '.$notification->getTypeNotification();
    }
    echo ', statut : '.$notification->getStatutEnvoi();
    echo "</a>";
    echo '</td></tr>';
}
}
echo '</td></tr>';
echo '</table>';
echo '</td></tr>';

echo '<tr><td>';

if ($modifiable  || ($saisie->getCommentaire() != null && $saisie->getCommentaire() != "")) {
    echo '<tr><td>';
    echo 'Commentaire : ';
    echo '</td><td colspan="2">';
    if (!$modifiable || $saisie->getDeletedAt() != null) {
	echo ($saisie->getCommentaire());
    } else {
	echo '<input name="commentaire" value="'.$saisie->getCommentaire().'" type="text" maxlength="150" size="25"/>';
    }
    echo '</td></tr>';
}

echo '<tr><td>';
echo 'Enregistré le : ';
echo '</td><td colspan="2">';
echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getCreatedAt('U')));
echo ' par '.  $saisie->getUtilisateurProfessionnel()->getCivilite().' '.$saisie->getUtilisateurProfessionnel()->getNom().' '.mb_substr($saisie->getUtilisateurProfessionnel()->getPrenom(), 0, 1).'.';
echo '</td></tr>';

if ($saisie->getCreatedAt('U') != $saisie->getVersionCreatedAt('U')) {
    echo '<tr><td>';
    echo 'Modifiée le : ';
    echo '</td><td colspan="2">';
    echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getVersionCreatedAt('U')));
    $modifie_par_utilisateur = UtilisateurProfessionnelQuery::create()->filterByLogin($saisie->getVersionCreatedBy())->findOne();
    if ($modifie_par_utilisateur != null) {
		echo ' par '.  $modifie_par_utilisateur->getCivilite().' '.$modifie_par_utilisateur->getNom().' '.mb_substr($modifie_par_utilisateur->getPrenom(), 0, 1).'.';
    }
    echo '</td></tr>';
}

if ($saisie->getIdSIncidents() !== null) {
    echo '<tr><td>';
    echo 'Discipline : ';
    echo '</td><td colspan="2">';
    echo "<a href='../mod_discipline/saisie_incident.php?id_incident=".
    $saisie->getIdSIncidents()."&step=2&return_url=no_return'>Visualiser l'incident </a>";
    echo '</td></tr>';
} elseif ($modifiable && $saisie->hasModeInterfaceDiscipline()) {
    echo '<tr><td>';
    echo 'Discipline : ';
    echo '</td><td colspan="2">';
    echo "<a href='../mod_discipline/saisie_incident_abs2.php?id_absence_eleve_saisie=".
	$saisie->getId()."&return_url=no_return'>Saisir un incident disciplinaire</a>";
    echo '</td></tr>';
}
$saisies_conflit_col = $saisie->getSaisiesContradictoiresManquementObligation();
if (!$saisies_conflit_col->isEmpty()) {
    echo '<tr><td>';
    echo 'La saisie est en contradiction avec : ';
    echo '</td><td colspan="2">';
    foreach ($saisies_conflit_col as $saisie_conflit) {
	echo "<a href='visu_saisie.php?id_saisie=".$saisie_conflit->getPrimaryKey()."' style=''> ";
	echo $saisie_conflit->getId();
	echo "</a>";
	if (!$saisies_conflit_col->isLast()) {
	    echo ' - ';
	}
    }
    echo '</td></tr>';
}
$saisies_englobante_col = $saisie->getAbsenceEleveSaisiesEnglobantes();
if (!$saisies_englobante_col->isEmpty()) {
    echo '<tr><td>';
    echo 'La saisie est englobée par : ';
    echo '</td><td colspan="2">';
    foreach ($saisies_englobante_col as $saisies_englobante) {
	echo "<a href='visu_saisie.php?id_saisie=".$saisies_englobante->getPrimaryKey()."' style='color:".$saisies_englobante->getColor()."'> ";
	echo $saisies_englobante->getDateDescription();
        echo ' '.$saisies_englobante->getTypesTraitements();
	echo "</a>";
	if (!$saisies_englobante_col->isLast()) {
	    echo ' - ';
	}
    }
    echo '</td></tr>';
}

echo '</td></tr>';
if ($modifiable) {
    echo '<tr><td colspan= "3" style="text-align : center;">';
    echo '<button dojoType="dijit.form.Button" type="submit"';
    if ($saisie->getDeletedAt() != null) echo 'disabled';
    echo '>Enregistrer les modifications</button>';
    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    echo '</td></tr>';
}

if ($utilisateur->getStatut()=="cpe" || $utilisateur->getStatut()=="scolarite") {
    echo '<tr><td colspan="3" style="text-align : center;">';
    echo '<button dojoType="dijit.form.Button" type="submit" name="creation_traitement" value="oui"';
    if ($saisie->getDeletedAt() != null) echo 'disabled';
    if(($total_traitements_modifiable>0)||($total_traitements_modifiable_non_prof>0)) {
        echo ' title="Il existe déjà au moins un traitement modifiable pour la saisie.
Il serait sans doute préférable de modifier le traitement existant ci-dessus,
mais vous pouvez aussi en créer un nouveau.">Créer un *nouveau* traitement pour la saisie</button>';
    }
    else {
        echo '>Traiter la saisie</button>';
    }
    echo '<button dojoType="dijit.form.Button" type="submit" name="creation_notification" value="oui"';
    if ($saisie->getDeletedAt() != null) echo 'disabled';
    echo '>Notifier la saisie</button>';
    echo '</td></tr>';
}

if (($utilisateur->getStatut()=="cpe" || $utilisateur->getStatut()=="scolarite") && $saisie->getAllVersions()->count()!=1) {
    echo '<tr><td colspan="3" style="text-align : center;">';
    echo 'Versions précédentes';
    echo '<table>';
    foreach($saisie->getAllVersions() as $version) {
    	echo '<tr>';
    	echo '<td>'.$version->getVersion().'</td>';
	    echo '<td>';
    	if ($saisie->getEleve() == null) {
		    echo "Marqueur d'appel effectué";
		} else {
		    echo $saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom();
		    echo ' '.$saisie->getEleve()->getClasseNom();
		}
	    echo '</td>';
		echo '<td>'.$version->getDateDescription().'</td>';
	    echo '<td>';
	    if ($version->getVersion() == 1) {
	    	echo 'Créée le : ';
	    } else {
	    	echo 'Modifiée le : ';
	    }
	    echo (strftime("%a %d/%m/%Y %H:%M", $version->getVersionCreatedAt('U')));
	    $modifie_par_utilisateur = UtilisateurProfessionnelQuery::create()->filterByLogin($version->getVersionCreatedBy())->findOne();
	    if ($modifie_par_utilisateur != null) {
			echo ' par '.  $modifie_par_utilisateur->getCivilite().' '.$modifie_par_utilisateur->getNom().' '.mb_substr($modifie_par_utilisateur->getPrenom(), 0, 1).'.';
	    }
	    echo '</td>';
    	echo '<td>';
    	if ($version->getVersion() != $saisie->getVersion() && $saisie->getDeletedAt() == null) {
    		echo '<a href="enregistrement_modif_saisie.php?id_saisie='.$saisie->getPrimaryKey().'&version='.$version->getVersion().'';
            if($menu){
                echo'&menu=false';
            } 
            echo' ">Revenir à cette version</a>';
    	}
    	echo '</td>';
    	echo '</tr>';
    }
    echo '</table>';
    echo '</td></tr>';
}

echo '</tbody>';

echo '</table>';
echo '</form>';
echo "</div>\n";
$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dijit.form.Button");
    dojo.require("dijit.Menu");
    dojo.require("dijit.form.Form");
    dojo.require("dijit.form.CheckBox");
    dojo.require("dijit.form.DateTextBox");
    dojo.require("dojo.parser");
</script>';
?>
<script type="text/javascript">
	//<![CDATA[
	function heureActuelle(e) {
		maintenant = new Date();
		document.getElementById(e).value = maintenant.getHours()+':'+maintenant.getMinutes();
		delete (maintenant);
	}

	function teste_validite_heure_debut_abs() {
		if((document.getElementById('trigger_calendrier_debut'))&&(document.getElementById('heure_debut'))) {
			date_debut=document.getElementById('trigger_calendrier_debut').value;
			heure_debut=document.getElementById('heure_debut').value;

			//alert('heure_debut='+heure_debut);

			new Ajax.Updater($('commentaire_heure_debut_abs'),'visu_saisie.php?test_heure_ouverture=y&date_debut='+date_debut+'&heure_debut='+heure_debut,{method: 'get'});
		}
	}

	teste_validite_heure_debut_abs();
  //]]>
</script>
<?php
require_once("../lib/footer.inc.php");
?>
