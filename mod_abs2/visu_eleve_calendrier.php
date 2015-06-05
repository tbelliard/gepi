<?php
/**
 *
 * Copyright 2015 Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs2/visu_eleve_calendrier.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_abs2/visu_eleve_calendrier.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Absences2 : Visualisation absences élève dans un calendrier',
statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
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

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : (isset($_GET['login_ele']) ? $_GET['login_ele'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$date_rech=isset($_POST['date_rech']) ? $_POST['date_rech'] : (isset($_GET['date_rech']) ? $_GET['date_rech'] : NULL);
$demi_j=isset($_POST['demi_j']) ? $_POST['demi_j'] : (isset($_GET['demi_j']) ? $_GET['demi_j'] : NULL);

$mois=isset($_POST['mois']) ? $_POST['mois'] : (isset($_GET['mois']) ? $_GET['mois'] : NULL);
$annee=isset($_POST['annee']) ? $_POST['annee'] : (isset($_GET['annee']) ? $_GET['annee'] : NULL);

$tab_creneaux=get_heures_debut_fin_creneaux();

if((isset($mode))&&($mode=="details_date")&&(isset($date_rech))&&(isset($login_ele))) {
	//$sql="SELECT DISTINCT * FROM a_saisies a, eleves e WHERE a.eleve_id=e.id_eleve AND e.login='".$login_ele."' AND debut_abs<='".$date_rech."' AND fin_abs>='".$date_rech."'";
	//$sql="SELECT DISTINCT a.*, e.*, datediff(fin_abs,debut_abs) as duree FROM a_saisies a, eleves e WHERE a.eleve_id=e.id_eleve AND e.login='".$login_ele."' AND debut_abs<='".$date_rech."' AND fin_abs>='".$date_rech."' ORDER BY duree DESC, debut_abs ASC;";
	if($demi_j=="matin") {
		$sql="SELECT DISTINCT a.*, e.*, datediff(fin_abs,debut_abs) as duree FROM a_saisies a, 
													eleves e 
												WHERE a.eleve_id=e.id_eleve AND 
													e.login='".$login_ele."' AND 
													(debut_abs<='".$date_rech." 10:00:00' OR (debut_abs>'".$date_rech." 00:00:00' AND debut_abs<'".$date_rech." 12:00:00' )) AND 
													(fin_abs>='".$date_rech."' OR (fin_abs>'".$date_rech." 00:00:00' AND fin_abs<'".$date_rech." 12:00:00'))
												ORDER BY duree DESC, debut_abs ASC;";
	}
	else {
		$sql="SELECT DISTINCT a.*, e.*, datediff(fin_abs,debut_abs) as duree FROM a_saisies a, 
													eleves e 
												WHERE a.eleve_id=e.id_eleve AND 
													e.login='".$login_ele."' AND 
													(debut_abs<='".$date_rech." 10:00:00' OR (debut_abs>'".$date_rech." 11:59:59' AND debut_abs<'".$date_rech." 23:59:59' )) AND 
													(fin_abs>='".$date_rech."' OR (fin_abs>'".$date_rech." 12:00:00' AND fin_abs<'".$date_rech." 23:59:59'))
												ORDER BY duree DESC, debut_abs ASC;";
	}
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Aucun enregistrement pour $login_ele à la date choisie.</p>";
	}
	else {
		while($lig=mysqli_fetch_object($res)) {
			echo "<div style='margin:0.5em; padding:0.2em;' class='fieldset_opacite50'>
	<p><a href='visu_saisie.php?id_saisie=".$lig->id."' target='_blank' title=\"Voir la saisie dans un nouvel onglet\">Saisie n°".$lig->id."</a>";

			$id_saisie=$lig->id;
			$saisie = AbsenceEleveSaisieQuery::create()->includeDeleted()->findPk($id_saisie);
			if ($saisie == null) {
				echo " <span style='color:red'>(<em>non trouvée</em>)</span>
	</p>";
			}
			else {
				// Récupérer le contenu de visu_saisie.php et élaguer
				echo "
	</p>";

				echo '
	<!--table class="normal"-->
	<table class="boireaus boireaus_alt">
		<tbody>
			<tr>
				<th>N° de saisie&nbsp;: </th>
				<td>'.$saisie->getPrimaryKey();
				if ($saisie->getDeletedAt()!=null) {
					echo ' <font color="red">(supprimée le ';
					echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getDeletedAt('U')));
					$suppr_utilisateur = UtilisateurProfessionnelQuery::create()->findPK($saisie->getDeletedBy());
					if ($suppr_utilisateur != null) {
						echo ' par '.  $suppr_utilisateur->getCivilite().' '.$suppr_utilisateur->getNom().' '.mb_substr($suppr_utilisateur->getPrenom(), 0, 1).'.';;
					}
					echo ')</font> ';
				}
				echo '</td>';

				$temoin_plus_dans_le_grp="n";
				if (($saisie->getEleve() != null)&&($saisie->getGroupe() != null)) {
					if(!is_eleve_du_groupe($saisie->getEleve()->getLogin(), $saisie->getGroupe()->getId())) {
						echo "
				<td>
					<div style='float:right; width:22px;'>
						<img src='../images/icons/ico_attention.png' width='22' height='19' alt='Attention' title=\"L'élève n'est plus membre du groupe ".$saisie->getGroupe()->getNameAvecClasses()." actuellement.
						Il en a peut-être été membre plus tôt dans l'année.
						Mais il n'en n'est plus membre aujourd'hui.

						Si cette saisie est une erreur, vous devriez la traiter
						pour la marquer en 'Erreur de saisie'.\" />
					</div>
				</td>";
						$temoin_plus_dans_le_grp="y";
					}
				}
				echo '
			</tr>
		</tbody>
	</table>';

				echo '
	<!--table class="normal"-->
	<table class="boireaus boireaus_alt">
		<tbody>
			<tr>';
				if ($saisie->getEleve() == null) {
					echo '
				<td colspan="3">Marqueur d\'appel effectué</td>';
				} else {
					echo '
					<th>Élève&nbsp;: </th>
					<td colspan="2">'.$saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom().' '.$saisie->getEleve()->getClasseNom();
				/*
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
				*/
					if ($utilisateur->getAccesFicheEleve($saisie->getEleve())) {
						echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."&amp;onglet=responsable&amp;quitter_la_page=y' target='_blank' title=\"Voir la fiche dans un nouvel onglet\"> (voir fiche)</a>";
					}
					echo '</td>';
				}
				echo '
			</tr>';

				if ($saisie->getClasse() != null) {
					echo '
			<tr>
				<th>Classe&nbsp;: </th>
				<td colspan="2">'.$saisie->getClasse()->getNom().'</td>
			</tr>';
				}

				if ($saisie->getGroupe() != null) {
				echo '
			<tr>
				<th>Groupe&nbsp;: </th>
				<td colspan="2">'.$saisie->getGroupe()->getNameAvecClasses().'</td>
			</tr>';
				}

				if ($saisie->getAidDetails() != null) {
					echo '<tr><th>';
					echo 'Aid&nbsp;: ';
					echo '</th><td colspan="2">';
					echo $saisie->getAidDetails()->getNom();
					echo '</td></tr>';
				}

				if ($saisie->getEdtEmplacementCours() != null) {
				echo '<tr><th>';
				echo 'Cours&nbsp;: ';
				echo '</th><td colspan="2">';
				echo $saisie->getEdtEmplacementCours()->getDescription();
				echo '</td></tr>';
				}

				if ($saisie->getEdtCreneau() != null) {
				echo '<tr><th>';
				echo 'Créneau&nbsp;: ';
				echo '</th><td colspan="2">';
				echo $saisie->getEdtCreneau()->getDescription();
				echo '</td></tr>';
				}

				echo '<tr><th>';
				echo 'Début&nbsp;: ';
				echo '</th><td colspan="2" class="bold">';
				echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getDebutAbs('U')));
				echo '</td></tr>';

				echo '<tr><th>';
				echo 'Fin&nbsp;: ';
				echo '</th><td colspan="2" class="bold">';
				echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getFinAbs('U')));
				echo '</td></tr>';

				echo '
			<tr>
				<th>Traitement&nbsp;: </th>
				<td style="background-color:#ebedb5;" colspan="2">';
				$type_autorises = AbsenceEleveTypeStatutAutoriseQuery::create()->filterByStatut($utilisateur->getStatut())->useAbsenceEleveTypeQuery()->orderBySortableRank()->endUse()->find();
				$total_traitements_modifiable = 0;
				$total_traitements_modifiable_non_prof = 0;
				$tab_traitements_deja_affiches=array();
				foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
					if(!in_array($traitement->getId(), $tab_traitements_deja_affiches)) {
						if ($utilisateur->getStatut() != 'professeur') {
							$total_traitements_modifiable_non_prof++;
							echo "
					<a href='visu_traitement.php?id_traitement=".$traitement->getId()."&id_saisie_appel=".$id_saisie."";
							echo"' style='display: block; height: 100%;' target='_blank' title=\"Voir le traitement dans un nouvel onglet\"> ";
							echo $traitement->getDescription();
							echo "</a>";
						} else {
							echo "
					".$traitement->getDescription();
						}
					}
					echo "<br/>";
					$tab_traitements_deja_affiches[]=$traitement->getId();
				}

				if ($saisie->getManquementObligationPresenceEnglobante()){
					echo 'globalement manquement à l\'obligation de présence<br/>';
					if ($saisie->getJustifieeEnglobante()) {
						echo 'globalement justifiée<br/>';
					}
					if ($saisie->getNotifieeEnglobante()) {
						echo 'globalement notifiée<br/>';
					}
				}


				echo '</td>
			</tr>
			<tr>
				<th>Notification&nbsp;: </th>
				<td>
					<table style="background-color:#c7e3ec;">';
				foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
					foreach ($traitement->getAbsenceEleveNotifications() as $notification) {
						echo '
						<tr>
							<td>'."<a href='visu_notification.php?id_notification=".$notification->getId()."";
						echo"' style='display: block; height: 100%;' target='_blank' title=\"Voir la notification dans un nouvel onglet\"> ";
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
						echo '</td>
						</tr>';
					}
				}
				//echo '</td></tr>';
				echo '
					</table>';
				echo '
				</td>
			</tr>';

				//echo '<tr><td>';

				if ($saisie->getCommentaire() != null && $saisie->getCommentaire() != "") {
					echo '
			<tr>
				<th>Commentaire&nbsp;: </th>
				<td colspan="2">';
					if ($saisie->getDeletedAt() != null) {
						echo ($saisie->getCommentaire());
					}
					echo '</td>
			</tr>';
				}

				echo '
			<tr>
				<th>Enregistré le&nbsp;: </th>
				<td colspan="2">';
				echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getCreatedAt('U')));
				echo ' par '.  $saisie->getUtilisateurProfessionnel()->getCivilite().' '.$saisie->getUtilisateurProfessionnel()->getNom().' '.mb_substr($saisie->getUtilisateurProfessionnel()->getPrenom(), 0, 1).'.';
				echo '</td>
			</tr>';

				if ($saisie->getCreatedAt('U') != $saisie->getVersionCreatedAt('U')) {
					echo '
			<tr>
				<th>Modifiée le : </th>
				<td colspan="2">';
					echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getVersionCreatedAt('U')));
					$modifie_par_utilisateur = UtilisateurProfessionnelQuery::create()->filterByLogin($saisie->getVersionCreatedBy())->findOne();
					if ($modifie_par_utilisateur != null) {
						echo ' par '.  $modifie_par_utilisateur->getCivilite().' '.$modifie_par_utilisateur->getNom().' '.mb_substr($modifie_par_utilisateur->getPrenom(), 0, 1).'.';
					}
					echo '</td>
			</tr>';
				}

				if ($saisie->getIdSIncidents() !== null) {
					echo '
			<tr>
				<th>Discipline&nbsp;: </th>
				<td colspan="2">';
					echo "<a href='../mod_discipline/saisie_incident.php?id_incident=".
					$saisie->getIdSIncidents()."&step=2&return_url=no_return' target='_blank' title=\"Voir l'incident dans un nouvel onglet\">Visualiser l'incident </a>";
					echo '</td>
			</tr>';
				}

				$saisies_conflit_col = $saisie->getSaisiesContradictoiresManquementObligation();
				if (!$saisies_conflit_col->isEmpty()) {
					echo '
			<tr>
				<th>La saisie est en contradiction avec&nbsp;: </th>
				<td colspan="2">';
					foreach ($saisies_conflit_col as $saisie_conflit) {
						echo "<a href='visu_saisie.php?id_saisie=".$saisie_conflit->getPrimaryKey()."' style='' target='_blank' title=\"Voir la saisie dans un nouvel onglet\"> ";
						echo $saisie_conflit->getId();
						echo "</a>";
						if (!$saisies_conflit_col->isLast()) {
							echo ' - ';
						}
					}
					echo '</td>
			</tr>';
				}

				$saisies_englobante_col = $saisie->getAbsenceEleveSaisiesEnglobantes();
				if (!$saisies_englobante_col->isEmpty()) {
					echo '
			<tr>
				<th>La saisie est englobée par&nbsp;: </th>
				<td colspan="2">';
					foreach ($saisies_englobante_col as $saisies_englobante) {
						echo "<a href='visu_saisie.php?id_saisie=".$saisies_englobante->getPrimaryKey()."' style='color:".$saisies_englobante->getColor()."' target='_blank' title=\"Voir la saisie dans un nouvel onglet\"> ";
						echo $saisies_englobante->getDateDescription();
						echo ' '.$saisies_englobante->getTypesTraitements();
						echo "</a>";
						if (!$saisies_englobante_col->isLast()) {
							echo ' - ';
						}
					}
					echo '</td>
			</tr>';
				}

				//echo '</td></tr>';

				if (($utilisateur->getStatut()=="cpe" || $utilisateur->getStatut()=="scolarite") && $saisie->getAllVersions()->count()!=1) {
					echo '
			<tr>
				<td colspan="3" style="text-align : center;">Versions précédentes
					<table class="boireaus boireaus_alt2">';
					foreach($saisie->getAllVersions() as $version) {
						echo '
						<tr>
							<td>'.$version->getVersion().'</td>
							<td>';
						if ($saisie->getEleve() == null) {
							echo "Marqueur d'appel effectué";
						} else {
							echo $saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom();
							echo ' '.$saisie->getEleve()->getClasseNom();
						}
						echo '</td>
							<td>'.$version->getDateDescription().'</td>
							<td>';
						if ($version->getVersion() == 1) {
							echo 'Créée le : ';
						} else {
							echo 'Modifiée le&nbsp;: ';
						}
						echo (strftime("%a %d/%m/%Y %H:%M", $version->getVersionCreatedAt('U')));
						$modifie_par_utilisateur = UtilisateurProfessionnelQuery::create()->filterByLogin($version->getVersionCreatedBy())->findOne();
						if ($modifie_par_utilisateur != null) {
							echo ' par '.  $modifie_par_utilisateur->getCivilite().' '.$modifie_par_utilisateur->getNom().' '.mb_substr($modifie_par_utilisateur->getPrenom(), 0, 1).'.';
						}
						echo '</td>';
						/*
						echo '
						<td>';
						if ($version->getVersion() != $saisie->getVersion() && $saisie->getDeletedAt() == null) {
							echo '<a href="enregistrement_modif_saisie.php?id_saisie='.$saisie->getPrimaryKey().'&version='.$version->getVersion().'';
							echo'" target="_blank">Revenir à cette version</a>';
						}
						echo '</td>';
						*/
						echo '
						</tr>';
					}
					echo '
					</table>
				</td>
			</tr>';
				}

				echo '
		</tbody>
	</table>';

			}
			echo "
</div>";

		}
	}

	die();
}

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** DEBUT EN-TETE ***************
$titre_page = "Absences élève";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

$ts_debut_annee_scolaire=getSettingValue('begin_bookings');
$ts_fin_annee_scolaire=getSettingValue('end_bookings');
//echo "\$ts_debut_annee_scolaire=$ts_debut_annee_scolaire<br />";
//echo "\$ts_fin_annee_scolaire=$ts_fin_annee_scolaire<br />";

// Choix du jour
if(!isset($jour)) {
	$jour=strftime("%d/%m/%Y");
	//$ts_jour=;
}

$temoin_debug=0;

if(!isset($login_ele)) {
	echo "
<p style='margin-bottom:1em;'>
	<a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>
<h1>Visualisation des absences d'un élève dans un calendrier</h1>
<br />";

	$page="visu_eleve_calendrier.php";

	// Portion d'AJAX:
	echo "<script type='text/javascript'>

	function cherche_eleves(type) {
		rech_nom_ou_prenom=document.getElementById('rech_'+type).value;

		//var url = 'liste_eleves.php';
		var url = '../eleves/liste_eleves.php';
		var myAjax = new Ajax.Request(
			url,
			{
				method: 'post',
				postBody: 'rech_'+type+'='+rech_nom_ou_prenom+'&page=$page',
				onComplete: affiche_eleves
			});

	}

	function affiche_eleves(xhr) {
		if (xhr.status == 200) {
			document.getElementById('liste_eleves').innerHTML = xhr.responseText;
		}
		else {
			document.getElementById('liste_eleves').innerHTML = xhr.status;
		}
	}

	function affichage_et_action(type) {
		if(document.getElementById('rech_'+type).value=='') {
			document.getElementById('Recherche_'+type).style.display='none';
		}
		else {
			document.getElementById('Recherche_'+type).style.display='';
			cherche_eleves(type);
		}
	}

	/*
	function cherche_eleves(type) {
		rech_nom_ou_prenom=document.getElementById('rech_'+type).value;

		new Ajax.Updater($('liste_eleves'),'../eleves/liste_eleves.php?rech_'+type+'='+rech_nom_ou_prenom+'&page=$page',{method: 'get'});
	}
	*/
</script>\n";


	// DIV avec formulaire pour navigateur AVEC Javascript:
	echo "<div id='recherche_avec_js' style='display:none;'>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' onsubmit=\"cherche_eleves('nom');return false;\" method='post' name='formulaire'>";
	echo "<p>\n";
	echo "Afficher les ".$gepiSettings['denomination_eleves']." dont le <strong>nom</strong> contient&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type='text' name='rech_nom' id='rech_nom' value='' onchange=\"affichage_et_action('nom')\" />\n";
	echo "<input type='hidden' name='page' value='$page' />\n";
	echo "<input type='button' name='Recherche' id='Recherche_nom' value='Rechercher' onclick=\"cherche_eleves('nom')\" />\n";
	//echo $champ_quitter_page_ou_non;
	echo "</p>\n";
	echo "</form>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' onsubmit=\"cherche_eleves('prenom');return false;\" method='post' name='formulaire'>";
	echo "<p>\n";
	echo "Afficher les ".$gepiSettings['denomination_eleves']." dont le <strong>prénom</strong> contient&nbsp;: <input type='text' name='rech_prenom' id='rech_prenom' value='' onchange=\"affichage_et_action('prenom')\" />\n";
	echo "<input type='hidden' name='page' value='$page' />\n";
	echo "<input type='button' name='Recherche' id='Recherche_prenom' value='Rechercher' onclick=\"cherche_eleves('prenom')\" />\n";
	//echo $champ_quitter_page_ou_non;
	echo "</p>\n";
	echo "</form>\n";

	echo "<div id='liste_eleves'></div>\n";

	echo "</div>\n";
	echo "<script type='text/javascript'>
document.getElementById('recherche_avec_js').style.display='';
affichage_et_action('nom');
affichage_et_action('prenom');

if(document.getElementById('rech_nom')) {document.getElementById('rech_nom').focus();}
</script>\n";


	if(isset($id_classe)) {
		$sql="SELECT DISTINCT e.login,e.nom,e.prenom FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe' ORDER BY e.nom,e.prenom;";
		//echo "$sql<br />";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele)>0) {
			echo "<a name='classe'></a><p class='bold'>".casse_mot($gepiSettings['denomination_eleves'], 'majf2')." de la classe de ".get_class_from_id($id_classe).":</p>\n";

			$tab_txt=array();
			$tab_lien=array();

			while($lig_ele=mysqli_fetch_object($res_ele)) {
				$tab_txt[]=casse_mot($lig_ele->prenom,'majf2')." ".my_strtoupper($lig_ele->nom);
				$tab_lien[]=$_SERVER['PHP_SELF']."?login_ele=".$lig_ele->login."&amp;id_classe=".$id_classe;
			}

			echo "<blockquote>\n";
			tab_liste($tab_txt,$tab_lien,3);
			echo "</blockquote>\n";

		}
	}

	if($_SESSION['statut']=='scolarite') {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur') {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc,j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgc.id_groupe=jgp.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
	}
	elseif(($_SESSION['statut']=='cpe')&&
		(getSettingAOui('GepiAccesReleveCpeTousEleves'))||
		(getSettingAOui('GepiRubConseilCpeTous'))||
		(getSettingAOui('GepiAccesCdtCpe'))||
		(getSettingAOui('AACpeTout'))||
		(getSettingAOui('GepiAccesTouteFicheEleveCpe'))||
		(getSettingAOui('GepiAccesAbsTouteClasseCpe'))
	) {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	elseif($_SESSION['statut']=='cpe') {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe=c.id ORDER BY c.classe";
	}
	elseif(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='secours')) {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	elseif($_SESSION['statut'] == 'autre'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	//echo "$sql<br />";
	$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_clas)>0) {
		echo "<p>Ou choisir un ".$gepiSettings['denomination_eleve']." dans une classe:</p>\n";

		$tab_txt=array();
		$tab_lien=array();

		while($lig_clas=mysqli_fetch_object($res_clas)) {
			$tab_txt[]=$lig_clas->classe;
			$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id."#classe";
		}

		echo "<blockquote>\n";
		tab_liste($tab_txt,$tab_lien,4);
		echo "</blockquote>\n";
	}

	//=============================================

	require_once("../lib/footer.inc.php");
	die();
}

echo "
<p style='margin-bottom:1em;'>
	<a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre élève</a>
</p>
<h1>Visualisation des absences d'un élève dans un calendrier</h1>
<br />";

/*
$login_ele="bejae";
$login_ele="coiffeyl";
*/
$jour=1;
if(!isset($mois)) {
	$mois=strftime("%m");
}
if(!isset($annee)) {
	$annee=strftime("%Y");
}

$ts=mktime(12, 0, 0, $mois, $jour, $annee);
$num_jsem=strftime("%u", $ts);
$nom_mois=strftime("%B", $ts);

if($temoin_debug==1) {
	echo "<p>Le $jour/$mois/$annee est un ".strftime("%A", $ts)."</p>";
}
if($num_jsem!="1") {
	$ts=$ts-($num_jsem-1)*24*3600;
	if($temoin_debug==1) {
		echo "<p>Le lundi précédent le $jour/$mois/$annee est le ".strftime("%A %d/%m/%Y", $ts)."</p>";
	}
}

// Repérer le premier dimanche après le mois
if($mois<12) {
	$jour_suivant=1;
	$mois_suivant=$mois+1;
	$annee_suivante=$annee;
}
else {
	$jour_suivant=1;
	$mois_suivant=1;
	$annee_suivante=$annee+1;
}

if($mois>1) {
	$jour_prec=1;
	$mois_prec=$mois-1;
	$annee_prec=$annee;
}
else {
	$jour_prec=1;
	$mois_prec=12;
	$annee_prec=$annee-1;
}

echo "<div style='text-align:center;'>
<h2>".get_nom_prenom_eleve($login_ele, "avec_classe")."</h2>
<p>
	<a href='".$_SERVER['PHP_SELF']."?login_ele=$login_ele&amp;annee=$annee_prec&amp;mois=$mois_prec'>Préc.</a>
	- $nom_mois $annee -
	<a href='".$_SERVER['PHP_SELF']."?login_ele=$login_ele&amp;annee=$annee_suivante&amp;mois=$mois_suivant'>Suivant</a>
</p>";

$ts_j1_mois_suiv=mktime(12, 0, 0, $mois_suivant, $jour_suivant, $annee_suivante);
$num_jsem_suiv=strftime("%u", $ts_j1_mois_suiv);

$ts_dim_suiv=$ts_j1_mois_suiv;
if($temoin_debug==1) {
	echo "<p>Le $jour_suivant/$mois_suivant/$annee_suivante est un ".strftime("%A", $ts_j1_mois_suiv)."</p>";
}
if($num_jsem_suiv!="1") {
	$ts_dim_suiv=$ts_j1_mois_suiv+(7-$num_jsem_suiv)*24*3600;
	if($temoin_debug==1) {
		echo "<p>Le premier dimanche suivant le mois $mois est le ".strftime("%A %d/%m/%Y", $ts_dim_suiv)." ($ts_dim_suiv)"."</p>";
	}
}

$tab_classe=get_clas_ele_telle_date($login_ele, $annee."-".$mois."-".$jour." 00:00:00");
if(isset($tab_classe['id_classe'])) {
	$id_classe=$tab_classe['id_classe'];
}
else {
	$id_classe="";
}
$tab_jour_vacance=get_tab_jours_vacances($id_classe);

$tab_abs=array();
// A FAIRE : Pouvoir choisir ce que l'on veut faire apparaitre (absences, retards,...)
//$sql="SELECT a.*, e.login FROM a_agregation_decompte a, eleves e WHERE a.eleve_id=e.id_eleve AND e.login='".$login_ele."' ORDER BY date_demi_jounee;";
$sql="SELECT a.*, e.login FROM a_agregation_decompte a, eleves e WHERE a.eleve_id=e.id_eleve AND (a.manquement_obligation_presence>'0' OR a.retards>'0') AND e.login='".$login_ele."' ORDER BY date_demi_jounee;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p style='color:red'>Aucun enregistrement pour $login_ele.</p>";
}
else {
	while($lig=mysqli_fetch_array($res)) {
		$tab_abs[$lig['date_demi_jounee']][]=$lig;
	}

	foreach($tab_abs as $current_date => $tmp_tab) {
		// Normalement, on ne fait qu'un tour dans la boucle
		for($loop=0;$loop<count($tmp_tab);$loop++) {
			if($tmp_tab[$loop]['manquement_obligation_presence']!=0) {
				$tab_abs[$current_date][$loop]['couleur']='red';

				$tab_abs[$current_date][$loop]['title']=$tmp_tab[$loop]['manquement_obligation_presence']." manquement à obligation de présence.";
				if($tmp_tab[$loop]['non_justifiee']!=0) {
					$tab_abs[$current_date][$loop]['title'].="\n".$tmp_tab[$loop]['non_justifiee']." non justifiée(s).";
				}
				if($tmp_tab[$loop]['notifiee']!=0) {
					$tab_abs[$current_date][$loop]['title'].="\n".$tmp_tab[$loop]['notifiee']." notifiée(s).";
				}
				if($tmp_tab[$loop]['retards']!=0) {
					$tab_abs[$current_date][$loop]['title'].="\n".$tmp_tab[$loop]['retards']." retard(s)";
					if($tmp_tab[$loop]['retards_non_justifies']!=0) {
						$tab_abs[$current_date][$loop]['title'].=", ".$tmp_tab[$loop]['retards_non_justifies']." non justifié(s).";
					}
				}
				// Récupérer aussi les motifs_absences et motifs_retards
			}
			elseif($tmp_tab[$loop]['retards']!=0) {
				$tab_abs[$current_date][$loop]['couleur']='orange';

				$tab_abs[$current_date][$loop]['title']=$tmp_tab[$loop]['retards']." retard(s)";
				if($tmp_tab[$loop]['retards_non_justifies']!=0) {
					$tab_abs[$current_date][$loop]['title'].=", ".$tmp_tab[$loop]['retards_non_justifies']." non justifié(s).";
				}
			}
		}
	}
}

//mysql> select * from a_agregation_decompte where eleve_id='4897' and date_demi_jounee>'2014-09-01 00:00:00';
/*
mysql> select * from a_agregation_decompte where eleve_id='4897' and date_demi_jounee like '2015-02-16%';
+----------+---------------------+--------------------------------+---------------+----------+---------+-----------------------+-----------------+----------------+---------------------+---------------------+
| eleve_id | date_demi_jounee    | manquement_obligation_presence | non_justifiee | notifiee | retards | retards_non_justifies | motifs_absences | motifs_retards | created_at          | updated_at          |
+----------+---------------------+--------------------------------+---------------+----------+---------+-----------------------+-----------------+----------------+---------------------+---------------------+
|     4897 | 2015-02-16 00:00:00 |                              1 |             0 |        0 |       0 |                     0 | | 1 |           | NULL           | 2015-04-21 16:52:22 | 2015-04-21 16:52:22 |
|     4897 | 2015-02-16 12:00:00 |                              1 |             0 |        0 |       0 |                     0 | | 1 |           | NULL           | 2015-04-21 16:52:22 | 2015-04-21 16:52:22 |
+----------+---------------------+--------------------------------+---------------+----------+---------+-----------------------+-----------------+----------------+---------------------+---------------------+
2 rows in set (0.01 sec)

mysql> 

*/


$tab_jour_ouverture=get_tab_jour_ouverture_etab();
$tab_jfr=array();
$tab_jfr[1]="lundi";
$tab_jfr[2]="mardi";
$tab_jfr[3]="mercredi";
$tab_jfr[4]="jeudi";
$tab_jfr[5]="vendredi";
$tab_jfr[6]="samedi";
$tab_jfr[7]="dimanche";

echo "<!--div id='div_details_date' style='float:right; width:30em;'></div-->

<table class='boireaus boireaus_alt' align='center'>
	<thead>
		<tr>
			<th>L</th>
			<th>Ma</th>
			<th>Me</th>
			<th>J</th>
			<th>V</th>
			<th>S</th>
			<th>D</th>
		</tr>
	</thead>
	<tbody>";
$chaine_debug="";
$ts_courant=$ts;
$temoin_mois_suiv=0;
while($ts_courant-2*3600<$ts_dim_suiv) {
//while($temoin_mois_suiv==0) {
	$jour_courant=strftime("%d", $ts_courant);
	$mois_courant=strftime("%m", $ts_courant);
	$annee_courant=strftime("%Y", $ts_courant);

	$num_jsem_courant=strftime("%u", $ts_courant);
	if($num_jsem_courant==1) {
		echo "
		<tr>";
	}

	$ajout="";
	if(($ts_courant<$ts_debut_annee_scolaire)||($ts_courant>$ts_fin_annee_scolaire)) {
		$style=" style='background-color:grey;'";
		$ajout="<br /><div style='width:2em; margin-right:1px; float:left;'></div><div style='width:2em;float:left; '>&nbsp;</div>";
	}
	elseif(in_array(strftime("%d/%m/%Y", $ts_courant), $tab_jour_vacance)) {
		$style=" style='background-color:grey;'";
		$ajout="<br /><div style='width:2em; margin-right:1px; float:left;'></div><div style='width:2em;float:left; '>&nbsp;</div>";
	}
	elseif(strftime("%m", $ts_courant)!=$mois) {
		$style=" style='background-color:grey;'";
		$ajout="<br /><div style='width:2em; margin-right:1px; float:left;'></div><div style='width:2em;float:left; '>&nbsp;</div>";
	}
	elseif(!in_array($tab_jfr[$num_jsem_courant], $tab_jour_ouverture)) {
		$style=" style='background-color:grey;'";
		$ajout="<br /><div style='width:2em; margin-right:1px; float:left;'></div><div style='width:2em; float:left;'>&nbsp;</div>";
	}
	else {
		$style="";

		// A VERIFIER : Est-ce que le 12 est standard ou fonction d'un paramètre de mod_abs2?
		if((isset($tab_abs[$annee_courant."-".$mois_courant."-".$jour_courant." 00:00:00"]))||
		(isset($tab_abs[$annee_courant."-".$mois_courant."-".$jour_courant." 12:00:00"]))) {
			if(isset($tab_abs[$annee_courant."-".$mois_courant."-".$jour_courant." 00:00:00"])) {
				// Contrôler qu'il n'y a qu'un enregistrement pour une demi-journée
				// Adapter la couleur au type...
				// Remplacer le &nbsp; par une indication justifié ou pas, ou type (on n'a pas l'info dans a_agregation_decompte)?,...
				//$ajout.="<span style='background-color:red;' title=\"\">&nbsp;</span>";
				$couleur='red';
				if(isset($tab_abs[$annee_courant."-".$mois_courant."-".$jour_courant." 00:00:00"][0]['couleur'])) {
					$couleur=$tab_abs[$annee_courant."-".$mois_courant."-".$jour_courant." 00:00:00"][0]['couleur'];
				}
				$title="";
				if(isset($tab_abs[$annee_courant."-".$mois_courant."-".$jour_courant." 00:00:00"][0]['title'])) {
					$title=" title=\"".$tab_abs[$annee_courant."-".$mois_courant."-".$jour_courant." 00:00:00"][0]['title']."\"";
				}
				//$ajout.="<br /><div style='width:2em; margin-right:1px; background-color:$couleur; float:left;' onclick=\"cherche_absences('".$annee_courant."-".$mois_courant."-".$jour_courant." 00:00:00"."')\"$title>&nbsp;</div>";
				//$ajout.="<br /><div style='width:2em; margin-right:1px; background-color:$couleur; float:left;' onclick=\"cherche_absences('".$annee_courant."-".$mois_courant."-".$jour_courant." 10:00:00"."', 'matin')\"$title>&nbsp;</div>";
				$ajout.="<br /><div style='width:2em; margin-right:1px; background-color:$couleur; float:left;' onclick=\"cherche_absences('".$annee_courant."-".$mois_courant."-".$jour_courant."', 'matin')\"$title>&nbsp;</div>";
			}
			else {
				//$ajout.="&nbsp;";
				$ajout.="<br /><div style='width:2em; margin-right:1px; float:left;'>&nbsp;</div>";
			}
			if(isset($tab_abs[$annee_courant."-".$mois_courant."-".$jour_courant." 12:00:00"])) {
				// Contrôler qu'il n'y a qu'un enregistrement pour une demi-journée
				// Adapter la couleur au type...
				// Remplacer le &nbsp; par une indication justifié ou pas, ou type (on n'a pas l'info dans a_agregation_decompte)?,...
				//$ajout.="<span style='background-color:red;' title=\"\">&nbsp;</span>";
				$couleur='red';
				if(isset($tab_abs[$annee_courant."-".$mois_courant."-".$jour_courant." 12:00:00"][0]['couleur'])) {
					$couleur=$tab_abs[$annee_courant."-".$mois_courant."-".$jour_courant." 12:00:00"][0]['couleur'];
				}
				$title="";
				if(isset($tab_abs[$annee_courant."-".$mois_courant."-".$jour_courant." 12:00:00"][0]['title'])) {
					$title=" title=\"".$tab_abs[$annee_courant."-".$mois_courant."-".$jour_courant." 12:00:00"][0]['title']."\"";
				}
				//$ajout.="<div style='width:2em; background-color:$couleur; float:left;' onclick=\"cherche_absences('".$annee_courant."-".$mois_courant."-".$jour_courant." 12:00:00"."', 'apres-midi')\"$title>&nbsp;</div>";
				$ajout.="<div style='width:2em; background-color:$couleur; float:left;' onclick=\"cherche_absences('".$annee_courant."-".$mois_courant."-".$jour_courant."', 'apres-midi')\"$title>&nbsp;</div>";
			}
			else {
				$ajout.="&nbsp;";
				$ajout.="<div style='width:2em; float:left;'>&nbsp;</div>";
			}
		}
		else {
			$ajout="<br /><div style='width:2em; margin-right:1px; float:left;'></div><div style='width:2em; float:left;'>&nbsp;</div>";
		}
	}

	if($temoin_debug==1) {
		$chaine_debug="<br />$ts_courant<br />".(($ts_dim_suiv-$ts_courant)/3600)."h<br />$temoin_mois_suiv<br />".strftime("%m", $ts_courant);
	}

	echo "
			<td$style>".strftime("%d", $ts_courant).$ajout.$chaine_debug."</td>";

	$ts_courant+=3600*24;

	// On considère le mois du jour qui suit ce tour dans la boucle while()
	$mois_courant=strftime("%m", $ts_courant);
	if(($mois<12)&&($mois_courant!=12)&&($mois_courant>$mois)) {
		//($mois_courant!=12) pour le cas 12/14 -> 01/15
		$temoin_mois_suiv++;
	}
	elseif(($mois==12)&&($mois_courant==1)) {
		$temoin_mois_suiv++;
	}
	else {
		// Si le dernier jour du mois est un dimanche (11/2014)
		if(($mois_courant!=$mois)&&(strftime("%d", $ts_courant)==1)) {
			$temoin_mois_suiv++;
		}
	}

	if($num_jsem_courant==7) {
		echo "
		</tr>";

		if($temoin_mois_suiv>0) {
			break;
		}
	}
}
echo "
	</tbody>
</table>

<div id='div_details_date'></div>
</div>

<script type='text/javascript'>
	function cherche_absences(date_rech,demi_j) {
		new Ajax.Updater($('div_details_date'),'../mod_abs2/visu_eleve_calendrier.php?mode=details_date&login_ele=$login_ele&date_rech='+date_rech+'&demi_j='+demi_j,{method: 'get'});
	}
</script>";

/*
echo "<pre>";
print_r($tab_abs);
echo "</pre>";
*/

require_once("../lib/footer.inc.php");
?>
