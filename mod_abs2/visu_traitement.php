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

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
    die("acces interdit");
}

//récupération des paramètres de la requète
$id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
if (isset($id_traitement) && $id_traitement != null) $_SESSION['id_traitement'] = $id_traitement;
$menu = isset($_POST["menu"]) ? $_POST["menu"] :(isset($_GET["menu"]) ? $_GET["menu"] : Null);
//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

$dojo = true;
$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dijit.form.Button");
    dojo.require("dijit.Menu");
    dojo.require("dijit.form.Form");
    dojo.require("dijit.form.CheckBox");
    dojo.require("dijit.form.DateTextBox");
    dojo.require("dojo.parser");
</script>';

if(!$menu){
   $titre_page = "Les absences"; 
}
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

if(!$menu){
    include('menu_abs2.inc.php');
}
//===========================
//debug_var();
echo "<div class='css-panes' style='background-color:#ebedb5;' id='containDiv' style='overflow : auto;'>\n";


$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
if ($traitement == null) {
    $criteria = new Criteria();
    $criteria->addDescendingOrderByColumn(AbsenceEleveTraitementPeer::UPDATED_AT);
    $criteria->setLimit(1);
    $traitement = $utilisateur->getAbsenceEleveTraitements($criteria)->getFirst();
    if ($traitement == null) {
	echo "Traitement non trouvé";
	die();
    }
}

if (isset($message_enregistrement)) {
    echo "<span style='color:green'>".$message_enregistrement."</span>";
}

//=============================
$tab_resp_legal_1_ou_2=array();
$tab_resp_legal_1=array();
$tab_resp_legal_2=array();
$select_saisie=array();
foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
	/*
	echo $saisie->getEleve()->getLogin()." - ".$saisie->getEleve()->getEleId()."<br />";
	echo "<pre>";
	print_r($saisie->getEleve());
	echo "</pre><hr />";
	*/
	$select_saisie[]=$saisie->getId();

	//$sql="SELECT DISTINCT pers_id FROM responsables2 WHERE ele_id='".$saisie->getEleve()->getEleId()."' AND (resp_legal='1' OR resp_legal='2');";
	$sql="SELECT DISTINCT pers_id FROM responsables2 WHERE ele_id='".$saisie->getEleve()->getEleId()."' AND resp_legal='1';";
	//echo "$sql<br />";
	$res_resp_legal=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_resp_legal)>0) {
		while($lig_resp_legal=mysqli_fetch_object($res_resp_legal)) {
			if(!in_array($lig_resp_legal->pers_id, $tab_resp_legal_1_ou_2)) {
				$tab_resp_legal_1_ou_2[]=$lig_resp_legal->pers_id;
			}

			if(!in_array($lig_resp_legal->pers_id, $tab_resp_legal_1)) {
				$tab_resp_legal_1[]=$lig_resp_legal->pers_id;
			}
		}
	}

	$sql="SELECT DISTINCT pers_id FROM responsables2 WHERE ele_id='".$saisie->getEleve()->getEleId()."' AND resp_legal='2';";
	//echo "$sql<br />";
	$res_resp_legal=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_resp_legal)>0) {
		while($lig_resp_legal=mysqli_fetch_object($res_resp_legal)) {
			if(!in_array($lig_resp_legal->pers_id, $tab_resp_legal_1_ou_2)) {
				$tab_resp_legal_1_ou_2[]=$lig_resp_legal->pers_id;
			}

			if(!in_array($lig_resp_legal->pers_id, $tab_resp_legal_2)) {
				$tab_resp_legal_2[]=$lig_resp_legal->pers_id;
			}
		}
	}
}

if((count($tab_resp_legal_1_ou_2)>2)||(count($tab_resp_legal_1)>1)||(count($tab_resp_legal_2)>1)) {

	echo "
<form action='traitements_par_lots.php' method='post'>
	<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>

		<p style='color:red'>Il semble que les saisies sélectionnées concernent plus de deux responsables légaux.<br />
		Vous devriez peut-être plutôt créer un lot de traitements avec des notifications individuelles plutôt qu'un seul traitement avec une notification commune.</p>

		<input type='hidden' name='menu' value='".$menu."' />
		<input type='hidden' name='creation_lot_traitements' value='yes' />
		<input type='hidden' name='suppr_traitement' value='".$traitement->getId()."' />
		<!--input type='hidden' name='validation_creation_lot_traitements' value='yes' /-->
		".add_token_field();

for($loop=0;$loop<count($select_saisie);$loop++) {
	echo "
		<input type='hidden' name='select_saisie[]' value='".$select_saisie[$loop]."' />";
}
	echo "
		<p><input type='submit' value='Créer un lot de traitements pour les saisies ci-dessous'/></p>
	</fieldset>
</form>";
}
//=============================

echo '<table class="normal">';
echo '<tbody>';
echo '<tr><td>';
echo 'N° de traitement';
echo '</td><td>';
echo $traitement->getPrimaryKey();
echo '</td></tr>';

echo '<tr><TD>';
echo 'Créé par : ';
echo '</TD><TD>';
if ($traitement->getUtilisateurProfessionnel() != null) {
	echo $traitement->getUtilisateurProfessionnel()->getCivilite().' '.$traitement->getUtilisateurProfessionnel()->getNom().' '.mb_substr($traitement->getUtilisateurProfessionnel()->getPrenom(), 0, 1).'.';
}
echo '</TD></tr>';

if ($traitement->getModifieParUtilisateurId() != null && $traitement->getUtilisateurId() != $traitement->getModifieParUtilisateurId()) {
    echo '<tr><TD>';
    echo 'Modifié par : ';
    echo '</TD><TD>';
    echo $traitement->getModifieParUtilisateur()->getCivilite().' '.$traitement->getModifieParUtilisateur()->getNom().' '.mb_substr($traitement->getModifieParUtilisateur()->getPrenom(), 0, 1).'.';
    echo '</TD></tr>';
}

echo '<tr><td>';
echo 'Saisies : ';
echo '</td><td>';
echo '<table style="background-color:#cae7cb;">';
$eleve_prec_id = null;

$tab_saisie=array();
$heure_min_saisie="";
$heure_max_saisie="";
$date_min_saisie="";
$date_max_saisie="";
$timestamp_min_debut="";
$timestamp_max_fin="";

$cpt_tour_dans_boucle_saisies=0;
$tab_id_eleves_traitement=array();
foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
    $cpt_tour_dans_boucle_saisies++;

    //$saisie = new AbsenceEleveSaisie();
    if ($saisie->getEleve() == null) {
		if (!$traitement->getAbsenceEleveSaisies()->isFirst()) {
			echo '</td></tr>';
		}
		echo '<tr><td>';
		echo 'Aucune absence';
		if ($saisie->getGroupe() != null) {
			echo ' pour le groupe ';
			echo $saisie->getGroupe()->getNameAvecClasses();
		}
		if ($saisie->getClasse() != null) {
			echo ' pour la classe ';
			echo $saisie->getClasse()->getNom();
		}
		if ($saisie->getAidDetails() != null) {
			echo ' pour l\'aid ';
			echo $saisie->getAidDetails()->getNom();
		}
		echo ' ';
		echo $saisie->getTypesDescription();
		echo '<tr><td>';
    } elseif ($eleve_prec_id != $saisie->getEleve()->getPrimaryKey()) {
		if (!$traitement->getAbsenceEleveSaisies()->isFirst()) {
			echo '</td></tr>';
		}
		echo '<tr><td>';

		$tab_id_eleves_traitement[]=$saisie->getEleve()->getPrimaryKey();

		echo '<div>';
		echo $saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom();
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
			//echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."' >";
			echo ' (voir fiche)';
			echo "</a>";
		}
		echo '<div style="float: right; margin-top:0.35em; margin-left:0.2em;">';
		if ($traitement->getAbsenceEleveSaisies()->isEmpty() && $traitement->getModifiable()) {
			echo '<form method="post" action="liste_saisies_selection_traitement.php">';
		    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
		echo '<p>';
			echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
			echo '<input type="hidden" name="filter_eleve" value="'.$saisie->getEleve()->getNom().'"/>';
			echo '<button type="submit">Ajouter</button>';
		echo '</p>';
			echo '</form>';
		}
		echo '</div>';
		echo '</div>';
		echo '<br/>';
		$eleve_prec_id = $saisie->getEleve()->getPrimaryKey();
    }

    echo '<div>';
	/*
	echo "<pre>";
	print_r($saisie);
	echo "</pre>";
	*/
    $tab_saisie[]=$saisie;

    $current_debut_abs=$saisie->getDebutAbs();
    $tmp_tab=explode(" ", $current_debut_abs);
    $tmp_tab2=explode("-", $tmp_tab[0]);
    $tmp_tab3=explode(":", $tmp_tab[1]);
    $timestamp_courant=mktime($tmp_tab3[0], $tmp_tab3[1], $tmp_tab3[2], $tmp_tab2[1], $tmp_tab2[2], $tmp_tab2[0]);
    if($timestamp_min_debut=="") {
        $timestamp_min_debut=$timestamp_courant;
    }
    elseif($timestamp_courant<$timestamp_min_debut) {
        $timestamp_min_debut=$timestamp_courant;
    }

    $current_fin_abs=$saisie->getFinAbs();
    $tmp_tab=explode(" ", $current_fin_abs);
    $tmp_tab2=explode("-", $tmp_tab[0]);
    $tmp_tab3=explode(":", $tmp_tab[1]);
    $timestamp_courant=mktime($tmp_tab3[0], $tmp_tab3[1], $tmp_tab3[2], $tmp_tab2[1], $tmp_tab2[2], $tmp_tab2[0]);
    if($timestamp_max_fin=="") {
        $timestamp_max_fin=$timestamp_courant;
    }
    elseif($timestamp_courant>$timestamp_max_fin) {
        $timestamp_max_fin=$timestamp_courant;
    }

    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."";
    if($menu){
        echo"&menu=false";
    }
    echo"' style='height: 100%;'> ";
    echo $saisie->getDateDescription();
    echo ' ';
    echo $saisie->getTypesDescription();
    echo "</a>";
    echo '<div style="float: right;  margin-top:-0.22em; margin-left:0.2em;">';
    if ($traitement->getModifiable()) {
		echo '<form method="post" action="enregistrement_modif_traitement.php">';
		echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
		echo '<p>';
		echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
		echo '<input type="hidden" name="modif" value="enlever_saisie"/>';
		echo '<input type="hidden" name="id_saisie" value="'.$saisie->getPrimaryKey().'"/>';
		echo '<button type="submit">Enlever</button>';
		echo '</p>';
		echo '</form>';
    }
    echo '</div>';
    echo '</div>';
    if (!$traitement->getAbsenceEleveSaisies()->isLast()) {
		echo '<br/>';
    }
}

if($cpt_tour_dans_boucle_saisies==0) {
  $sql="SELECT a_saisie_id FROM j_traitements_saisies WHERE a_traitement_id='".$traitement->getPrimaryKey()."';";
  $res_saisies=mysqli_query($mysqli, $sql);
  if(mysqli_num_rows($res_saisies)>0) {
      echo "<span style='color:red'>Il existe des saisies associées, mais elles ont peut-être été supprimées.</span><br /><span style='color:red; font-weight:bold;'>Liste des saisies&nbsp;:</span> ";
      $cpt_saisie_cachees=0;
      while($lig_saisie=mysqli_fetch_object($res_saisies)) {
           if($cpt_saisie_cachees>0) {echo " - ";}
           echo " <a href='visu_saisie.php?id_saisie=$lig_saisie->a_saisie_id' title='Voir la saisie n°$lig_saisie->a_saisie_id'>$lig_saisie->a_saisie_id</a>";
           $cpt_saisie_cachees++;
      }
  }
}

if (!$traitement->getAbsenceEleveSaisies()->isEmpty()) {
    echo '<br/>';

	// S'il y a plusieurs élèves à afficher dabs saisir_eleve.php, on ne parvient pas à ne récupérer qu'eux.
	// Du coup, on n'affiche le lien que s'il n'y a qu'un élève pour le traitement.
	if(count($tab_id_eleves_traitement)==1) {
		echo '<div style="float:right; width:3em;">';
		echo '<a href="saisir_eleve.php?type_selection=id_eleve&id_eleve='.$saisie->getEleve()->getPrimaryKey().'" title="Pour compléter/étendre la saisie après contact de la famille">Saisir</a>';
		/*
		echo "<form action='saisir_eleve.php' method='post'>\n";
		for($loop=0;$loop<count($tab_id_eleves_traitement);$loop++) {
			echo "<input type='hidden' name='id_eleve[]' value='".$tab_id_eleves_traitement[$loop]."' />\n";
		}
		echo "<input type='submit' value='Saisir' title='Pour compléter/étendre la saisie après contact de la famille'>\n";
		echo "</form>\n";
		*/
		echo '</div>';
	}

    echo '<form method="post" action="liste_saisies_selection_traitement.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
    echo '<p>';
    echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
    echo '<input type="hidden" name="filter_recherche_saisie_a_rattacher" value="oui"/>';
    echo '<button type="submit">Chercher des saisies à rattacher</button>';
    echo '</p>';
    echo '</form>';

    if ($traitement->getModifiable()) {
		if(count($tab_saisie)>0) {
			echo '<form method="post" action="enregistrement_modif_traitement.php">';
			echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
			echo '<p>';
			echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
			echo '<input type="hidden" name="modif" value="modifier_heures_saisies"/>';
			foreach($tab_saisie as $current_saisie) {
				echo '<input type="hidden" name="id_saisie[]" value="'.$current_saisie->getPrimaryKey().'"/>';
			}

			$heure_debut_modif="";
			$date_debut_modif=strftime("%Y-%m-%d");
			if($timestamp_min_debut!="") {
				$heure_debut_modif=strftime("%H:%M", $timestamp_min_debut);
				$date_debut_modif=strftime("%Y-%m-%d", $timestamp_min_debut);
			}

			$heure_fin_modif="";
			$date_fin_modif=strftime("%Y-%m-%d");
			if($timestamp_max_fin!="") {
				$heure_fin_modif=strftime("%H:%M", $timestamp_max_fin);
				$date_fin_modif=strftime("%Y-%m-%d", $timestamp_max_fin);
			}

			echo '<nobr>Début : <input name="heure_debut" id="heure_debut" value="'.$heure_debut_modif.'" type="text" maxlength="5" size="4" onkeydown="clavier_heure(this.id,event);" autocomplete="off" title="Vous pouvez modifier l\'heure en utilisant les flèches Haut/Bas et PageUp/PageDown du clavier" />&nbsp;
			<input id="trigger_calendrier_debut" name="date_debut"  type="text" dojoType="dijit.form.DateTextBox"  value="'. $date_debut_modif.'"  style="width : 8em"/>
			 -&gt; 
			 Fin : <input name="heure_fin" id="heure_fin" value="'.$heure_fin_modif.'" type="text" maxlength="5" size="4" onkeydown="clavier_heure(this.id,event);" autocomplete="off" title="Vous pouvez modifier l\'heure en utilisant les flèches Haut/Bas et PageUp/PageDown du clavier" />&nbsp;
			<input id="trigger_calendrier_fin" name="date_fin" type="text" dojoType="dijit.form.DateTextBox"  value="'. $date_fin_modif.'"  style="width : 8em"/></nobr> ';

			if(count($tab_saisie)==1) {
				echo '<button type="submit" title="Vous pouvez étendre la durée de la saisie initiale.">Modifier la saisie</button>';
			}
			else {
				echo '<button type="submit" title="Vous pouvez étendre la durée des saisies sélectionnées.">Modifier les saisies</button>';
			}
			echo '</p>';
			echo '</form>';
		}
	}
}
echo '</td></tr>';
echo '</table>';

echo '</td></tr>';

echo '<tr><td>';
echo 'Type : ';
echo '</td><td>';
//on ne modifie le type que si aucun envoi n'a ete fait //on fait non
//if ($traitement->getModifiable()) {
    $type_autorises = AbsenceEleveTypeStatutAutoriseQuery::create()->filterByStatut($utilisateur->getStatut())->useAbsenceEleveTypeQuery()->orderBySortableRank()->endUse()->find();
    if ($type_autorises->count() != 0) {
	echo '<form method="post" action="enregistrement_modif_traitement.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
	echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
	echo '<input type="hidden" name="modif" value="type"/>';
	echo ("<select name=\"id_type\" onchange='submit()'>");
	echo "<option value='-1'></option>\n";
	$type_in_list = false;
	foreach ($type_autorises as $type) {
	    //$type = new AbsenceEleveTypeStatutAutorise();
		echo "<option value='".$type->getAbsenceEleveType()->getId()."'";
		if ($type->getAbsenceEleveType()->getId() == $traitement->getATypeId()) {
		    echo " selected='selected'";
		    $type_in_list = true;
		}
		echo ">";
		echo $type->getAbsenceEleveType()->getNom();
		echo "</option>\n";
	}
	if (!$type_in_list && $traitement->getAbsenceEleveType() != null) {
	    echo "<option value='".$traitement->getAbsenceEleveType()->getId()."'";
	    echo " selected='selected'";
	    echo ">";
	    echo $traitement->getAbsenceEleveType()->getNom();
	    echo "</option>\n";
	}
	echo "</select>";
	echo '<button type="submit">Modifier</button>';
	echo '</p>';
	echo '</form>';
    }
//} else {
//    if ($traitement->getAbsenceEleveType() != null) {
//	echo $traitement->getAbsenceEleveType()->getNom();
//    }
//}
echo '</td></tr>';

echo '<tr><td>';
echo 'Motif : ';
echo '</td><td>';
$motifs = AbsenceEleveMotifQuery::create()->orderByRank()->find();
echo '<form method="post" action="enregistrement_modif_traitement.php">';
echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
echo '<input type="hidden" name="modif" value="motif"/>';
echo ("<select name=\"id_motif\" onchange='submit()'>");
echo "<option value='-1'></option>\n";
foreach ($motifs as $motif) {
    //$justification = new AbsenceEleveJustification();
    echo "<option value='".$motif->getId()."'";
    if ($motif->getId() == $traitement->getAMotifId()) {
	echo " selected='selected'";
    }
    echo ">";
    echo $motif->getNom();
    echo "</option>\n";
}
echo "</select>";
echo '<button type="submit">Modifier</button>';
	echo '</p>';
echo '</form>';
echo '</td></tr>';

echo '<tr><td>';
echo 'Justification : ';
echo '</td><td>';
$justifications = AbsenceEleveJustificationQuery::create()->orderByRank()->find();
echo '<form method="post" action="enregistrement_modif_traitement.php">';
echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
echo '<input type="hidden" name="modif" value="justification"/>';
echo ("<select name=\"id_justification\" onchange='submit()'>");
echo "<option value='-1'></option>\n";
foreach ($justifications as $justification) {
    //$justification = new AbsenceEleveJustification();
    echo "<option value='".$justification->getId()."'";
    if ($justification->getId() == $traitement->getAJustificationId()) {
	echo " selected='selected'";
    }
    echo ">";
    echo $justification->getNom();
    echo "</option>\n";
}
echo "</select>";
echo '<button type="submit">Modifier</button>';
	echo '</p>';
echo '</form>';
echo '</td></tr>';

echo '<tr><td>';
echo 'Commentaire : ';
echo '</td><td>';
echo '<form method="post" action="enregistrement_modif_traitement.php">';
echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
echo '<input type="hidden" name="modif" value="commentaire"/>';
echo '<input type="text" name="commentaire" size="30" value="'.$traitement->getCommentaire().'" />';
echo '<button type="submit">Modifier</button>';
	echo '</p>';
echo '</form>';
echo '</td></tr>';

echo '<tr><td>';
echo 'Notification : ';
echo '</td><td>';
echo '<table style="background-color:#c7e3ec;">';
$eleve_prec_id = null;
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
echo '<tr><td>';
echo '<form method="post" action="enregistrement_modif_notification.php">';
echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
echo '<input type="hidden" name="creation_notification" value="oui"/>';
echo '<button type="submit">Nouvelle notification à la famille</button>';
	echo '</p>';
echo '</form>';
echo '</td></tr>';

echo '</table>';
echo '</td></tr>';

echo '<tr><td>';
echo 'Créé par : ';
echo '</td><td>';
if ($traitement->getUtilisateurProfessionnel() != null) {
    echo $traitement->getUtilisateurProfessionnel()->getCivilite();
    echo ' ';
    echo $traitement->getUtilisateurProfessionnel()->getNom();
}
echo '</td></tr>';

echo '<tr><td>';
echo 'Créé le : ';
echo '</td><td>';
echo (strftime("%a %d/%m/%Y %H:%M", $traitement->getCreatedAt('U')));
echo '</td></tr>';

if ($traitement->getCreatedAt() != $traitement->getUpdatedAt()) {
    echo '<tr><td>';
    echo 'Modifiée le : ';
    echo '</td><td>';
    echo (strftime("%a %d/%m/%Y %H:%M", $traitement->getUpdatedAt('U')));
    echo '</td></tr>';
}

if ($traitement->getModifiable()) {
    echo '<tr><td colspan="2" align="center">';
    echo '<form method="post" action="enregistrement_modif_traitement.php">';
    echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<p>';
    echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
    echo '<input type="hidden" name="modif" value="supprimer"/>';
    echo '<button type="submit">Supprimer le traitement</button>';
	echo '</p>';
    echo '</form>';
    echo '</td></tr>';
}

if((($_SESSION['statut']=='cpe')||($_SESSION['statut']=='scolarite')||
((($_SESSION['statut']=='professeur')&&(getSettingAOui('GepiAccesGestElevesProf')))))&&
(isset($saisie))&&($saisie->getEleve() != null)) {
	echo '<tr><td style=\"vertical-align:top;\">';
	echo 'Contact&nbsp;: ';
	echo '</td><td>';
	echo tableau_tel_resp_ele($saisie->getEleve()->getLogin());
	echo '</td></tr>';
	//flush();
}

echo '</tbody>';

echo '</table>';


echo "</div>\n";

require_once("../lib/footer.inc.php");

?>
