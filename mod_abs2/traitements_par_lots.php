<?php
/**
 *
 *
 * Copyright 2010-2013 Josselin Jacquard, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs2/traitements_par_lots.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_abs2/traitements_par_lots.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Abs2: Creation lot de traitements',
statut='';";
$insert=mysql_query($sql);
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

$menu = isset($_POST["menu"]) ? $_POST["menu"] :(isset($_GET["menu"]) ? $_GET["menu"] : Null);
$modif = isset($_POST["modif"]) ? $_POST["modif"] :(isset($_GET["modif"]) ? $_GET["modif"] : Null);
//$select_saisie = isset($_POST["select_saisie"]) ? $_POST["select_saisie"] :(isset($_GET["select_saisie"]) ? $_GET["select_saisie"] : Null);
$select_saisie=isset($_POST['select_saisie']) ? $_POST['select_saisie'] : NULL;

// Ménage quand on "transforme" un traitement pour plusieurs parents de familles différentes (visu_traitement.php) en un lot de traitements:
if(isset($_POST['suppr_traitement'])) {
	check_token();

	$id_suppr_traitement=$_POST['suppr_traitement'];
	$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_suppr_traitement);
	if ($traitement == null) {
		$message_erreur_traitement="Le traitement initial n'a pas été trouvé, donc non supprimé.<br />";
	}
	else {
		$traitement->delete();
	}
}

// Ménage quand on "transforme" une notification pour plusieurs parents de familles différentes (visu_notification.php) en un lot de traitements:
if(isset($_POST['suppr_notification'])) {
	check_token();

	$id_suppr_notification=$_POST['suppr_notification'];

	$notification = new AbsenceEleveNotification();
	$notification = AbsenceEleveNotificationQuery::create()->findPk($id_suppr_notification);

	if ($notification != null) {
		if ($notification->getAbsenceEleveTraitement() != null) {
			$id_suppr_traitement=$notification->getATraitementId();
			$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_suppr_traitement);

			if ($traitement == null) {
				$message_erreur_traitement="Le traitement initial n'a pas été trouvé, donc non supprimé.<br />";
			}
			else {
				$traitement->delete();

				$notification->delete();
			}
		}
		else {
			$notification->delete();
		}
	}
}

if ($modif == 'modifier_heures_saisies') {

	$message_enregistrement="";

	// Tableau des id_saisie à modifier
	//$id_saisie=$_POST['id_saisie'];
	$id_saisie=$select_saisie;

	// Date de début transmise au format aaaa-mm-jj
	try {
		$tab_date=explode("-",$_POST['date_debut']);
		$tmp_date=$tab_date[2].".".$tab_date[1].".".$tab_date[0];
		$date_debut = new DateTime($tmp_date);
	} catch (Exception $x) {
		$message_enregistrement .= "Mauvais format de date.<br/>";
	}

	// Date de fin transmise au format aaaa-mm-jj
	try {
		$tab_date=explode("-",$_POST['date_fin']);
		$tmp_date=$tab_date[2].".".$tab_date[1].".".$tab_date[0];
		$date_fin = new DateTime($tmp_date);
	} catch (Exception $x) {
		$message_enregistrement .= "Mauvais format de date.<br/>";
	}

	// Heure de début transmise au format HH:MM
	try {
		$heure_debut = new DateTime($_POST['heure_debut']);
		$date_debut->setTime($heure_debut->format('H'), $heure_debut->format('i'));
	} catch (Exception $x) {
		$message_enregistrement .= "Mauvais format d'heure.<br/>";
	}

	// Heure de fin transmise au format HH:MM
	try {
		$heure_fin = new DateTime($_POST['heure_fin']);
		$date_fin->setTime($heure_fin->format('H'), $heure_fin->format('i'));
	} catch (Exception $x) {
		$message_enregistrement .= "Mauvais format d'heure.<br/>";
	}

	if ($message_enregistrement == "") {
		for($loop=0;$loop<count($id_saisie);$loop++) {
			$saisie = AbsenceEleveSaisieQuery::create()->includeDeleted()->findPk($id_saisie[$loop]);
			if ($saisie != null) {
				$saisie->setDebutAbs($date_debut);
				$saisie->setFinAbs($date_fin);
				$saisie->save();
			}
		}
		$message_enregistrement.="Modification des heures de saisies effectuée.<br />";
	}

}
//=====================================================
if(isset($_POST['validation_creation_lot_traitements'])) {
	check_token();

	/*
	$_POST['select_saisie']=	Array (*)
	$_POST[select_saisie]['0']=	45464
	$_POST[select_saisie]['1']=	45465
	$_POST[select_saisie]['2']=	45466
	$_POST['id_type']=	30
	$_POST['id_motif']=	18
	$_POST['id_justification']=	7
	$_POST['commentaire']=	blabla bli
	*/

	$tab_ele=array();
	for($loop=0;$loop<count($select_saisie);$loop++) {
		$saisie = AbsenceEleveSaisieQuery::create()->includeDeleted()->findPk($select_saisie[$loop]);
		if ($saisie != null) {
			$tab_ele[$saisie->getEleve()->getPrimaryKey()][]=$select_saisie[$loop];
		}
	}

	$message_erreur_traitement="";
	$message_enregistrement="";
	$nb_reg=0;
	$nb_notifications=0;
	$tab_traitement_cree=array();
	$tab_notification_creee=array();
	foreach($tab_ele as $key_ele => $tab_saisies_ele) {
		$traitement = new AbsenceEleveTraitement();
		$traitement->setUtilisateurProfessionnel($utilisateur);
		for($i=0;$i<count($tab_saisies_ele); $i++) {
			$traitement->addAbsenceEleveSaisie(AbsenceEleveSaisieQuery::create()->findPk($tab_saisies_ele[$i]));
		}
		if ($traitement->getAbsenceEleveSaisies()->isEmpty()) {
			$message_erreur_traitement.=' Erreur : aucune saisie sélectionnée pour l élève n°'.$key_ele.'<br />';
		} else {

			if(isset($_POST["id_type"])) {
				$traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->findPk($_POST["id_type"]));   
			}

			if(isset($_POST["commentaire"])) {
				$traitement->setCommentaire($_POST["commentaire"]);
			}

			if(isset($_POST["id_justification"])) {
				$traitement->setAbsenceEleveJustification(AbsenceEleveJustificationQuery::create()->findPk($_POST["id_justification"]));
			}

			if(isset($_POST["id_motif"])) {
				$traitement->setAbsenceEleveMotif(AbsenceEleveMotifQuery::create()->findPk($_POST["id_motif"]));
			}

			$traitement->save();

			$tab_traitement_cree[$key_ele]=$traitement->getId();

			$nb_reg++;


			if((isset($_POST['type_notification']))&&($_POST['type_notification']!="")) {

				$notification = new AbsenceEleveNotification();
				$notification->setUtilisateurProfessionnel($utilisateur);
				$notification->setAbsenceEleveTraitement($traitement);

				//on met le type courrier par défaut
				//$notification->setTypeNotification(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER);

				$notification->setTypeNotification($_POST['type_notification']);
				$notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL);

				$responsable_eleve1 = null;
				$responsable_eleve2 = null;
				foreach ($traitement->getResponsablesInformationsSaisies() as $responsable_information) {
					if ($responsable_information->getNiveauResponsabilite() == '1') {
						$responsable_eleve1 = $responsable_information->getResponsableEleve();
					} else if ($responsable_information->getNiveauResponsabilite() == '2') {
						$responsable_eleve2 = $responsable_information->getResponsableEleve();
					}
				}

				if ($responsable_eleve1 != null) {
					$notification->setEmail($responsable_eleve1->getMel());
					$notification->setTelephone($responsable_eleve1->getTelPort());
					$notification->setAdresseId($responsable_eleve1->getAdresseId());
					$notification->addResponsableEleve($responsable_eleve1);
				}
				if ($responsable_eleve2 != null) {
					if ($responsable_eleve1 == null
					|| $responsable_eleve2->getAdresseId() == $responsable_eleve1->getAdresseId()) {
						$notification->addResponsableEleve($responsable_eleve2);
					}
				}
				$notification->save();
				$tab_notification_creee[$key_ele]=$notification->getId();
				$nb_notifications++;

				//$url='./visu_notification.php?id_notification='.$notification->getId().'';



				/*
					$notification->setTypeNotification($_POST['type_notification']);
					$notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL);
					
					
						} elseif ($modif == 'ajout_responsable') {
							$responsable = ResponsableEleveQuery::create()->findOneByResponsableEleveId($_POST["pers_id"]);
							if ($responsable != null && !$notification->getResponsableEleves()->contains($responsable)) {
							$notification->addResponsableEleve($responsable);
							$notification->save();
								$message_enregistrement .= 'Responsable ajouté';
								include("visu_notification.php");
								die;
							}
						} elseif ($modif == 'email') {
							$notification->setEmail($_POST["email"]);
						} elseif ($modif == 'tel') {
							$notification->setTelephone($_POST["tel"]);
						} elseif ($modif == 'adresse') {
							$notification->setAdresseId($_POST["adr_id"]);
						} elseif ($modif == 'duplication') {
							$clone = $notification->copy(); //no deep copy
							$clone->save();
							$id = $clone->getId();
							//this is done to avoid a bug in deepcopy
							$notification->copyInto($clone, true);// deep copy
							$clone->setId($id);
							$clone->setNew(false);
							$clone->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL);
							$clone->setDateEnvoi(null);
							$clone->setErreurMessageEnvoi(null);
							$clone->save();
							$_POST["id_notification"] = $clone->getId();
							$message_enregistrement .= 'Nouvelle notification';
							include("visu_notification.php");
							die();
						} elseif ($modif == 'duplication_par_responsable') {    
							$responsablesToAdd = new PropelCollection;
							$responsables_informations = $notification->getAbsenceEleveTraitement()->getResponsablesInformationsSaisies();
							Foreach ($responsables_informations as $responsable_information) {
								$responsable = $responsable_information->getResponsableEleve();
								if ($responsable == null || $notification->getResponsableEleves()->contains($responsable) || $responsable_information->getNiveauResponsabilite() == '0') {
									continue;
								}
								$responsablesToAdd->append($responsable);
							}
							foreach ($responsablesToAdd as $responsableToAdd) {
								$clone = $notification->copy(); //no deep copy
								$clone->save();
								$id = $clone->getId();
								//this is done to avoid a bug in deepcopy
								$notification->copyInto($clone, true); // deep copy        
								$clone->setId($id);
								$clone->setNew(false);
								$clone->setEmail($responsableToAdd->getMel());
								$clone->setTelephone($responsableToAdd->getTelPort());
								$clone->setAdresseId($responsableToAdd->getAdresseId());
								$clone->save();
								// On supprime les anciens responsables de la notification initiale
								$responsables = JNotificationResponsableEleveQuery::create()->filterByAbsenceEleveNotification($clone)->find();
								foreach ($responsables as $responsable) {
									$responsable->delete();
								}
								$clone->addResponsableEleve($responsableToAdd);
								$clone->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL);
								$clone->setDateEnvoi(null);
								$clone->setErreurMessageEnvoi(null);
								$clone->save();        
								$message_enregistrement .= 'Nouvelle notification <a href="./visu_notification.php?id_notification='.$clone->getId();
								if ($menu) {
									$message_enregistrement .='&menu=false';
								}
								$message_enregistrement .= '">'.$clone->getId().'</a> créée pour '.$responsableToAdd->getCivilite().' '.$responsableToAdd->getPrenom().' '.$responsableToAdd->getNom().' <br />';
							}
					
				*/

			}

			/*
			$url='./visu_traitement.php?id_traitement='.$traitement->getId().'';
			if($menu){
			$url.='&menu=false';
			}
			header("Location:".$url);
			die;
			*/
		}
	}
	if($nb_reg>0) {
		$message_enregistrement.=$nb_reg." traitement(s) créé(s).<br />";
	}
	if($nb_notifications>0) {
		$message_enregistrement.=$nb_notifications." notification(s) créée(s).<br />";
	}
}
/*
$_POST['creation_traitement']=	no
$_POST['creation_notification']=	no
$_POST['ajout_traitement']=	no
$_POST['id_traitement']=	
$_POST['creation_lot_traitements']=	yes
$_POST['select_saisie']=	Array (*)
$_POST[select_saisie]['0']=	45469
$_POST[select_saisie]['1']=	45470
$_POST[select_saisie]['2']=	45471
*/
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

$temoin_submit=0;
if (isset($message_erreur_traitement)) {
    echo "<span style='color:red'>".$message_erreur_traitement."</span>";
    $temoin_submit++;
}

if (isset($message_enregistrement)) {
    echo "<span style='color:green'>".$message_enregistrement."</span>";
    $temoin_submit++;
}

// Ajouter des liens de retour vers les Absences du jour et les Traitements
if($temoin_submit>0) {
    echo "<p>Retour vers <a href='absences_du_jour.php'>les absences du jour</a></p>";
}

if(!isset($select_saisie)) {
	echo "<p style='color:red'>Aucune saisie n'a été sélectionnée.</p>\n";
	require_once("../lib/footer.inc.php");
	die();
}

$tab_saisie=array();
$heure_min_saisie="";
$heure_max_saisie="";
$date_min_saisie="";
$date_max_saisie="";
$timestamp_min_debut="";
$timestamp_max_fin="";

//=================================================
echo "<table class='normal'>
	<tr>
		<td>Saisies</td>
		<td style='background-color:#cae7cb;'>
		";
for($loop=0;$loop<count($select_saisie);$loop++) {
	$id_saisie=$select_saisie[$loop];

	$saisie = AbsenceEleveSaisieQuery::create()->includeDeleted()->findPk($id_saisie);
	if ($saisie == null) {
		$criteria = new Criteria();
		$criteria->addDescendingOrderByColumn(AbsenceEleveSaisiePeer::UPDATED_AT);
		$criteria->setLimit(1);
		$saisie_col = $utilisateur->getAbsenceEleveSaisiesJoinEdtCreneau($criteria);
		$saisie = $saisie_col->getFirst();
		if ($saisie == null) {
			echo "Saisie ".$id_saisie." non trouvée";
			die();
		}
	}

	echo '<div>';
	//echo "<p>".$saisie->getEleve()->getPrimaryKey()."<br />";

	echo $saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom();
	if ((getSettingValue("active_module_trombinoscopes")=='y') && $saisie->getEleve() != null) {
		$nom_photo = $saisie->getEleve()->getNomPhoto(1);
		$photos = $nom_photo;
		//if (($nom_photo == "") or (!(file_exists($photos)))) {
		if (($nom_photo == NULL) or (!(file_exists($photos)))) {
			$photos = "../mod_trombinoscopes/images/trombivide.jpg";
		}
		$valeur = redimensionne_image_petit_bis($photos);
		echo ' <img src="'.$photos.'" style="width: '.$valeur[0].'px; height: '.$valeur[1].'px; border: 0px; vertical-align: middle;" alt="" title="" />';
	}
	if ($utilisateur->getAccesFicheEleve($saisie->getEleve())) {
		echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."&amp;onglet=responsable&amp;quitter_la_page=y' target='_blank'>";
		//echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."' >";
		echo ' (voir fiche)';
		echo "</a>";
	}
	echo "<br />";

	echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."";
	if($menu){
		echo"&menu=false";
	}
	echo"' style='height: 100%;'> ";
	echo $saisie->getDateDescription();
	echo ' ';
	echo $saisie->getTypesDescription();
	echo "</a>";

	if(isset($tab_traitement_cree[$saisie->getEleve()->getId()])) {
		echo "<br />";
		echo "Voir <a href='visu_traitement.php?id_traitement=".$tab_traitement_cree[$saisie->getEleve()->getId()]."' target='_blank'>le traitement n°".$tab_traitement_cree[$saisie->getEleve()->getId()]."</a>";
		if(isset($tab_notification_creee[$saisie->getEleve()->getId()])) {
			echo ", <a href='visu_notification.php?id_notification=".$tab_notification_creee[$saisie->getEleve()->getId()]."' target='_blank'>la notification n°".$tab_notification_creee[$saisie->getEleve()->getId()]."</a>";
		}
	}

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


	echo '<div style="float: right;  margin-top:-0.22em; margin-left:0.2em;">';
		//echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
		echo '<form method="post" action="traitements_par_lots.php">';
		echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
		echo '<input type="hidden" name="creation_lot_traitements" value="yes"/>';
		echo '<p>';
		echo '<input type="hidden" name="modif" value="enlever_saisie"/>';
		for($loop2=0;$loop2<count($select_saisie);$loop2++) {
			if($loop2!=$loop) {
				echo '<input type="hidden" name="select_saisie[]" value="'.$select_saisie[$loop2].'"/>';
			}
		}
		echo '<button type="submit">Enlever</button>';
		echo '</p>';
		echo '</form>';
	echo '</div>';

	/*
	echo "<pre>";
	print_r($saisie);
	echo "</pre>";
	*/

	echo '</div>';

	//echo "<hr />";
}

if(count($select_saisie)>0) {
	//echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo '<form method="post" action="traitements_par_lots.php">';
	echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
	echo '<input type="hidden" name="creation_lot_traitements" value="yes"/>';
	echo '<p>';
	//echo '<input type="hidden" name="id_traitement" value="'.$traitement->getPrimaryKey().'"/>';
	echo '<input type="hidden" name="modif" value="modifier_heures_saisies"/>';
	for($loop=0;$loop<count($select_saisie);$loop++) {
		echo '<input type="hidden" name="select_saisie[]" value="'.$select_saisie[$loop].'"/>';
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

	if(count($select_saisie)==1) {
		echo '<button type="submit" title="Vous pouvez étendre la durée de la saisie initiale.">Modifier la saisie</button>';
	}
	else {
		echo '<button type="submit" title="Vous pouvez étendre la durée des saisies sélectionnées.">Modifier les saisies</button>';
	}
	echo '</p>';
	echo '</form>';
}

echo "		</td>
	</tr>
</table>";
//=================================================

echo "
<form method='post' action='traitements_par_lots.php'>
<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
<input type='hidden' name='menu' value='".$menu."'/>
<input type='hidden' name='creation_lot_traitements' value='yes'/>
<input type='hidden' name='validation_creation_lot_traitements' value='yes'/>
".add_token_field();

for($loop=0;$loop<count($select_saisie);$loop++) {
	echo "
<input type='hidden' name='select_saisie[]' value='".$select_saisie[$loop]."'/>";
}


$motifs = AbsenceEleveMotifQuery::create()->orderByRank()->find();
$justifications = AbsenceEleveJustificationQuery::create()->orderByRank()->find();
$type_autorises = AbsenceEleveTypeStatutAutoriseQuery::create()->filterByStatut($utilisateur->getStatut())->useAbsenceEleveTypeQuery()->orderBySortableRank()->endUse()->find();
echo "
<table class='normal'>
	<tr>
		<td>Type : </td>
		<td>";
if ($type_autorises->count() != 0) {
	echo "
			<p>
				<select name=\"id_type\" onchange='changement()'>
					<option value='-1'></option>";
	$type_in_list = false;
	foreach ($type_autorises as $type) {
		echo "
					<option value='".$type->getAbsenceEleveType()->getId()."'>".$type->getAbsenceEleveType()->getNom()."</option>";
	}
	echo "
				</select>
			</p>";
}
echo "
		</td>
	</tr>
	<tr>
		<td>
			Motif : 
		</td>
		<td>
			<p>
				<select name=\"id_motif\" onchange='changement()'>
					<option value='-1'></option>";
foreach ($motifs as $motif) {
	echo "
					<option value='".$motif->getId()."'>".$motif->getNom()."</option>";
}
echo "			</select>
			</p>
		</td>
	</tr>
	<tr>
		<td>
			Justification : 
		</td>
		<td>
			<p>
				<select name=\"id_justification\" onchange='changement()'>
					<option value='-1'></option>";
foreach ($justifications as $justification) {
	echo "
					<option value='".$justification->getId()."'>".$justification->getNom()."</option>";
}
echo "
				</select>
			</p>
		</td>
	</tr>
	<tr>
		<td>
			Commentaire : 
		</td>
		<td>
			<p>
				<input type='text' name='commentaire' size='30' value='' onchange='changement()' />
			</p>
		</td>
	</tr>
	<tr>
		<td>
			Notification : 
		</td>
		<td>
			<table style='background-color:#c7e3ec;'>
				<tr>
					<td>
						Type de notification : 
					</td>
					<td>
						<p>
							<select name=\"type_notification\" onchange='changement()'>
								<option value=''>Aucune</option>";

foreach (AbsenceEleveNotificationPeer::getValueSet(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION) as $type) {
	if ($type === AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_SMS && (getSettingValue("abs2_sms") != 'y')) {
		//pas d'option sms
	}
	else {
		echo "
								<option value='$type' id='type_notification_".preg_replace("/[^A-zA-z0-9]/", "_", $type)."'>".$type."</option>";
	}
}
echo "
							</select>
						</p>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='checkbox' name='creer_meme_notification_resp_1_et_2' id='creer_meme_notification_resp_1_et_2' value='y' /><label for=''>Créer la même notification pour les responsables 1 et 2</label>
						<br /><span style='color:red'>Cette possibilité n'est pas encore implémentée.</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<input type='submit' value='Valider' />
</fieldset>
</form>

<p style='text-indent:-4em; margin-left:4em; margin-top:2em;'><em>NOTES&nbsp;:</em> Prenez soin de modifier si nécessaire les saisies avant de choisir les paramètres pour créer les traitements et notifications.</p>";

require_once("../lib/footer.inc.php");

//fonction redimensionne les photos petit format
function redimensionne_image_petit_bis($photo)
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

?>
