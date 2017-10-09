<?php
/**
 *
 *
 * Copyright 2010-2015 Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs2/visu_saisies_ele_jour.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_abs2/visu_saisies_ele_jour.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Voir les saisies d absences pour un élève tel jour',
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

if ($utilisateur->getStatut()=="professeur" &&  getSettingValue("active_module_absence_professeur")!='y') {
    die("Le module n'est pas activé.");
}
/*
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

		$num_jour=id_j_semaine($ts);

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
*/

//récupération des paramètres de la requète
$date_jour=isset($_POST['date_jour']) ? $_POST['date_jour'] : (isset($_GET['date_jour']) ? $_GET['date_jour'] : strftime("%Y-%m-%d"));
$id_eleve = isset($_POST["id_eleve"]) ? $_POST["id_eleve"] :(isset($_GET["id_eleve"]) ? $_GET["id_eleve"] : NULL);
$menu = isset($_POST["menu"]) ? $_POST["menu"] :(isset($_GET["menu"]) ? $_GET["menu"] : NULL);

//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
if((!$menu)&&(!$affichage_depuis_edt2)) {
$titre_page = "Les absences";
}
//$utilisation_jsdivdrag = "non";
$dojo = true;
$_SESSION['cacher_header'] = "y";

require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
if(!$menu) {
	include('menu_abs2.inc.php');
}
echo "<div class='css-panes' style='background-color:#cae7cb;' id='containDiv' style='overflow : auto;'>\n";

if(!isset($id_eleve)) {
	echo "<p style='color:red'>Aucun identifiant élève n'a été fourni.</p></div>";
	die();
}

$eleve = EleveQuery::create()->findPk($id_eleve);
/*
echo "<pre>";
print_r($eleve);
echo "</pre>";
*/
if($eleve == null) {
	echo "<p style='color:red'>L'élève n°$id_eleve n'a pas été identifié.</p></div>";
	die();
}

function redimensionne_image($photo)
{
	// prendre les informations sur l'image
	$info_image = getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur = $info_image[0];
	$hauteur = $info_image[1];
	// largeur et/ou hauteur maximum à afficher
	if(basename($_SERVER['PHP_SELF'],".php") === "trombi_impr") {
		// si pour impression
		$taille_max_largeur = getSettingValue("l_max_imp_trombinoscopes");
		$taille_max_hauteur = getSettingValue("h_max_imp_trombinoscopes");
	} else {
	// si pour l'affichage écran
		$taille_max_largeur = getSettingValue("l_max_aff_trombinoscopes");
		$taille_max_hauteur = getSettingValue("h_max_aff_trombinoscopes");
	}

	// calcule le ratio de redimensionnement
	$ratio_l = $largeur / $taille_max_largeur;
	$ratio_h = $hauteur / $taille_max_hauteur;
	$ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

	// définit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur = $largeur / $ratio;
	$nouvelle_hauteur = $hauteur / $ratio;

	return array($nouvelle_largeur, $nouvelle_hauteur);
}

if ((getSettingValue("active_module_trombinoscopes")=='y') && $eleve != null) {
	$nom_photo = $eleve->getNomPhoto(1);
	$photos = $nom_photo;
	//if (($nom_photo == "") or (!(file_exists($photos)))) {
	if (($nom_photo == NULL) or (!(file_exists($photos)))) {
		$photos = "../mod_trombinoscopes/images/trombivide.jpg";
	}
	//$valeur = redimensionne_image_petit($photos);
	$valeur = redimensionne_image($photos);
	echo '<div style="float:right;width:'.$valeur[0].'px;"><img src="'.$photos.'" style="width: '.$valeur[0].'px; height: '.$valeur[1].'px; border: 0px; vertical-align: middle;" alt="" title="" /></div>';
}

echo "<p class='bold'>Saisies concernant ".$eleve->getNom()." ".$eleve->getPrenom()." le ".formate_date($date_jour)."</p>";


foreach($eleve->getAbsenceEleveSaisiesDuJour($date_jour) as $saisie) {

			if ($saisie == null) {
				echo " <span style='color:red'>(<em>non trouvée</em>)</span>
	</p>";
			}
			else {
				$id_saisie=$saisie->getId();
				// Récupérer le contenu de visu_saisie.php et élaguer
				echo "
	</p>";

				echo '
	<!--table class="normal"-->
	<table class="boireaus boireaus_alt">
		<tbody>
			<tr>
				<th>N° de saisie&nbsp;: </th>
				<td><a href="visu_saisie.php?id_saisie='.$saisie->getPrimaryKey().'&menu=false">'.$saisie->getPrimaryKey()."</a>";
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
					<a href='visu_traitement.php?id_traitement=".$traitement->getId()."&id_saisie_appel=".$id_saisie."&menu=false";
							//echo"' style='display: block; height: 100%;' target='_blank' title=\"Voir le traitement dans un nouvel onglet\"> ";
							echo "' style='display: block; height: 100%;' title=\"Voir le traitement n°".$traitement->getId()."\"> ";
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
							<td>'."<a href='visu_notification.php?id_notification=".$notification->getId()."&menu=false";
						//echo "' style='display: block; height: 100%;' target='_blank' title=\"Voir la notification n°".$notification->getId()." dans un nouvel onglet\"> ";
						echo "' style='display: block; height: 100%;' title=\"Voir la notification n°".$notification->getId()."\"> ";
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

}

echo "</div>";

require_once("../lib/footer.inc.php");
//$_SESSION['ni_menu_ni_titre']=false;
?>
