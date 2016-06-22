<?php
/*
* $Id$
*
* Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
//require_once("../lib/initialisationsPropel.inc.php");
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

// ajout des droits pour scolarité en 1.6.3
$sql = "UPDATE `gepi`.`droits` SET `scolarite` = 'V' WHERE `droits`.`id` = '/mod_abs2/export_stat.php';";
$resp=mysqli_query($GLOBALS["mysqli"], $sql);
//INSERT INTO droits SET id='/mod_abs2/export_stat.php',administrateur='V',professeur='F',cpe='V',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Exports statistiques',statut='';
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//recherche de l'utilisateur avec propel
/*
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}
*/

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='2') {
	die("Le module n'est pas activé.");
}

include_once 'lib/function.php';

$msg="";

$mois=isset($_POST['mois']) ? $_POST['mois'] : (isset($_GET['mois']) ? $_GET['mois'] : NULL);
$annee=isset($_POST['annee']) ? $_POST['annee'] : (isset($_GET['annee']) ? $_GET['annee'] : NULL);

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$tab_stat=array();

// Problème pour les cités scolaires...
$tab_stat['RNE']=getSettingValue('gepiSchoolRne');
$tab_stat['UAI']=getSettingValue('gepiSchoolRne');
// Proposer le choix des MEF à retenir? et de modifier le RNE avant export?

$extraire="n";
if(isset($mois)) {
	if((!preg_match("/^[0-9]*$/", $mois))||($mois<1)||($mois>12)) {
		$msg.="Le mois choisi '$mois' n'est pas valide.<br />";
		unset($mois);
	}
	elseif(!isset($annee)) {
		$msg.="L'année n'a pas été choisie.<br />";
	}
	elseif(!preg_match("/^[0-9]{4}-[0-9]{4}$/", $annee)) {
		$msg.="L'année choisie '$annee' n'a pas un format valide.<br />";
		unset($annee);
	}
	else {
		$tab_annee=explode("-",$annee);
		if($mois<9) {
			$annee_extract=$tab_annee[1];
		}
		else {
			$annee_extract=$tab_annee[0];
		}

		$extraire="y";
	}
}

$debug="n";

$tabdiv_infobulle_0=array();
//$tabid_infobulle=array();

if($extraire=="y") {
	$tab_stat['mois']=strftime("%b-%y", strtotime($mois."/01/$annee_extract"));
	$tab_stat['ville']=getSettingValue('gepiSchoolCity');

	$mois_suiv=$mois+1;
	$annee_mois_suiv=$annee_extract;
	if($mois==12) {
		$mois_suiv=+1;
		$annee_mois_suiv=$annee_extract+1;
	}

	$dernier_jour_du_mois=30;
	if(($mois==1)||($mois==3)||($mois==5)||($mois==7)||($mois==8)||($mois==10)||($mois==12)) {
		$dernier_jour_du_mois=31;
	}
	if($mois==2) {
		$dernier_jour_du_mois=28;
	}

	// Il faudrait un champ eleves.date_entree pour repérer les élèves arrivés en cours d'année.
	//$tab_stat['effectif_total']=-1;
	//$sql="SELECT DISTINCT e.nom,e.login,e.date_sortie FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND (date_sortie IS NULL OR date_sortie>'".$annee_extract."-".$mois."-01 00:00:00');";
	$sql="SELECT DISTINCT e.login FROM eleves e WHERE (date_entree IS NULL OR date_entree<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00') AND (date_sortie IS NULL OR date_sortie LIKE '0000-00-00 %' OR date_sortie>'".$annee_extract."-".$mois."-01 00:00:00') AND e.login IN (SELECT login FROM j_eleves_classes);";
	if($debug=="y") {echo "$sql<br/>";}
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$tab_stat['effectif_total']=mysqli_num_rows($res);

	// Recherche des mef associés à des élèves:
	$cpt_mef=0;
	$sql="SELECT * FROM mef WHERE mef_code IN (SELECT DISTINCT mef_code FROM eleves) ORDER BY libelle_court, libelle_long;";
	$res_mef=mysqli_query($GLOBALS["mysqli"], $sql);
	$cpt_mef=0;
	while($lig_mef=mysqli_fetch_object($res_mef)) {
		// Désignation et détails MEF
		$tab_stat['mef'][$cpt_mef]=array();
		$tab_stat['mef'][$cpt_mef]['mef_code']=$lig_mef->mef_code;
		$tab_stat['mef'][$cpt_mef]['code_mefstat']=$lig_mef->code_mefstat;
		$tab_stat['mef'][$cpt_mef]['mef_2']=mb_substr($lig_mef->code_mefstat, 0, 2);
		$tab_stat['mef'][$cpt_mef]['libelle_court']=$lig_mef->libelle_court;
		$tab_stat['mef'][$cpt_mef]['libelle_long']=$lig_mef->libelle_long;
		$tab_stat['mef'][$cpt_mef]['libelle_edition']=$lig_mef->libelle_edition;
		$tab_stat['mef'][$cpt_mef]['ele']=array();

		// Effectif MEF
		$sql="SELECT DISTINCT e.login FROM eleves e WHERE (date_entree IS NULL OR date_entree<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00') AND (date_sortie IS NULL OR date_sortie LIKE '0000-00-00 %' OR date_sortie>'".$annee_extract."-".$mois."-01 00:00:00') AND e.mef_code='".$lig_mef->mef_code."' AND e.login IN (SELECT login FROM j_eleves_classes);";
		if($debug=="y") {echo "$sql<br/>";}
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$tab_stat['mef'][$cpt_mef]['effectif']=mysqli_num_rows($res);

		//======================================
		$tab_motifs_non_valables=array();
		$sql="SELECT * FROM a_motifs WHERE valable!='y';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			if($debug=="y") {
				echo "Motifs non valables:";
			}
			while($lig=mysqli_fetch_object($res)) {
				$tab_motifs_non_valables[]=$lig->id;
				if($debug=="y") {
					echo "<pre>";
					print_r($lig);
					echo "</pre>";
				}
			}
		}
		//======================================

		//======================================
		// A partir de 4 demi-journées
		// Justifiée ou non:
		// PROBLEME: Un élève qui a un retard et qui dans la même demi-journée sèche des cours, si aucun motifs_absences n'est saisi, il ne sera pas totalisé ici.
		//(a.retards='0' OR a.motifs_absences!='') AND 
		$sql="SELECT e.login,e.nom,e.prenom,a.* FROM a_agregation_decompte a, eleves e 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
		a.manquement_obligation_presence='1' AND 
		a.eleve_id=e.id_eleve AND 
		e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		GROUP BY a.eleve_id HAVING COUNT(a.manquement_obligation_presence)>=4;";
		if($debug=="y") {echo "$sql<br/>";}
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$tab_stat['mef'][$cpt_mef]['abs_sup_egal_4']=mysqli_num_rows($res);
		if(mysqli_num_rows($res)>0) {

			//$titre_infobulle=$lig_mef->libelle_edition." (nb_abs&gt;=4)";
			//$texte_infobulle="";

			//$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_4']="";
			$cpt_ele=0;
			while($lig_ele=mysqli_fetch_object($res)) {
				$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["id_eleve"]=$lig_ele->eleve_id;
				$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nom"]=$lig_ele->nom;
				$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["prenom"]=$lig_ele->prenom;
				$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["designation"]=$lig_ele->nom." ".$lig_ele->prenom;
				$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nb_abs"]=0;
				$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nj_au_sens_gepi"]=0;
				$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nj"]=0;
				$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["non_valable"]=0;
				$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["aucun_motif"]=0;

				if($debug=="y") {
					echo "<hr />";
					echo "<pre>";
					print_r($lig_ele);
					echo "</pre>";
				}

				$sql="SELECT a.* FROM a_agregation_decompte a WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
					a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
					a.manquement_obligation_presence='1' AND 
					a.eleve_id='".$lig_ele->eleve_id."';";
				if($debug=="y") {echo "$sql<br/>";}
				$res_abs=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_abs)>0) {
					while($lig_abs=mysqli_fetch_object($res_abs)) {

						if($debug=="y") {
							echo "<pre>";
							print_r($lig_abs);
							echo "</pre>";
						}

						$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nb_abs"]++;
						if($debug=="y") {
							echo "\$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]['nb_abs']=".$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nb_abs"]."<br />";
						}

						if($lig_abs->non_justifiee!='0') {
							$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nj_au_sens_gepi"]++;
							if($debug=="y") {
								echo "\$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]['nj_au_sens_gepi']=".$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nj_au_sens_gepi"]."<br />";
							}
							//$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nj"]++;
							// Si les parents n'ont pas justifié, on ne se préoccupe pas du motif? C'est non valable?
							// On ne devrait pas saisir de motif sans justification de la famille, mais bon...

							if($lig_abs->motifs_absences!="") {
								$tmp_tab=explode("|", preg_replace("/[^0-9|]/", "", $lig_abs->motifs_absences));
								for($loop=0;$loop<count($tmp_tab);$loop++) {
									if(in_array($tmp_tab[$loop], $tab_motifs_non_valables)) {
										$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["non_valable"]++;
										$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nj"]++;
										if($debug=="y") {
											echo "\$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]['non_valable']=".$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["non_valable"]."<br />";
											echo "\$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]['nj']=".$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nj"]."<br />";
										}
										break;
									}
								}
							}
							else {
								$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nj"]++;
								$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["aucun_motif"]++;
								if($debug=="y") {
									echo "\$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]['nj']=".$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nj"]."<br />";
									echo "\$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]['aucun_motif']=".$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["aucun_motif"]."<br />";
								}
							}

						}
						elseif($lig_abs->motifs_absences!="") {
							// Absence justifiée par les parents... mais le motif est-il valable?
							$tmp_tab=explode("|", preg_replace("/[^0-9|]/", "", $lig_abs->motifs_absences));
							for($loop=0;$loop<count($tmp_tab);$loop++) {
								if(in_array($tmp_tab[$loop], $tab_motifs_non_valables)) {
									$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["non_valable"]++;
									$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nj"]++;
									if($debug=="y") {
										echo "\$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]['non_valable']=".$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["non_valable"]."<br />";
										echo "\$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]['nj']=".$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nj"]."<br />";
									}
									break;
								}
							}
						}
						else {
							// Absence justifiée par les parents avec motif vide???
							$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["aucun_motif"]++;
							//$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["non_valable"]++;
							// Non justifié au sens de l'export E.N, même si c'est justifié au sens Gepi.
							$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nj"]++;
							if($debug=="y") {
								echo "\$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]['aucun_motif']=".$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["aucun_motif"]."<br />";
								echo "\$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]['nj']=".$tab_stat['mef'][$cpt_mef]['ele'][$lig_ele->login]["nj"]."<br />";
							}
						}

						//$tab_stat['mef'][$cpt_mef]['nj'][]=$lig_ele->nom." ".$lig_ele->prenom;
					}
				}

				//$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_4'].=$lig_ele->nom." ".$lig_ele->prenom;
				//$tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_4'][$cpt_ele]['login']=$lig_ele->login;
				//$tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_4'][$cpt_ele]['nom_prenom']=$lig_ele->nom." ".$lig_ele->prenom;

				$cpt_ele++;
			}
		}

		$titre_infobulle_abs_sup_egal_4=$lig_mef->libelle_edition." (nb_abs&ge;4)";
		$texte_infobulle_abs_sup_egal_4="";
		$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_4']="";

		$titre_infobulle_abs_sup_egal_11=$lig_mef->libelle_edition." (nb_abs&ge;11)";
		$texte_infobulle_abs_sup_egal_11="";
		$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_11']="";

		$titre_infobulle_abs_4_a_10=$lig_mef->libelle_edition." (4&le;nb_abs&le;10)";
		$texte_infobulle_abs_4_a_10="";
		$tab_stat['mef'][$cpt_mef]['liste_abs_4_a_10']="";


		$titre_infobulle_nj_sup_egal_4=$lig_mef->libelle_edition." (nb_nj&ge;4)";
		$texte_infobulle_nj_sup_egal_4="";
		$tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_4']="";

		$titre_infobulle_nj_sup_egal_11=$lig_mef->libelle_edition." (nb_nj&ge;4)";
		$texte_infobulle_nj_sup_egal_11="";
		$tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_11']="";

		$titre_infobulle_nj_4_a_10=$lig_mef->libelle_edition." (nb_nj&ge;4)";
		$texte_infobulle_nj_4_a_10="";
		$tab_stat['mef'][$cpt_mef]['liste_nj_4_a_10']="";


		$titre_infobulle_non_valable_nj_sup_egal_4=$lig_mef->libelle_edition." (non_valable_nj&ge;4)";
		$texte_infobulle_non_valable_nj_sup_egal_4="";
		$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_4']="";

		$titre_infobulle_non_valable_nj_sup_egal_11=$lig_mef->libelle_edition." (non_valable_nj&ge;11)";
		$texte_infobulle_non_valable_nj_sup_egal_11="";
		$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_11']="";

		$titre_infobulle_non_valable_nj_4_a_10=$lig_mef->libelle_edition." (4&le;non_valable_nj&le;10)";
		$texte_infobulle_non_valable_nj_4_a_10="";
		$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_4_a_10']="";


		$titre_infobulle_aucun_motif_nj_sup_egal_4=$lig_mef->libelle_edition." (aucun_motif_nj&ge;4)";
		$texte_infobulle_aucun_motif_nj_sup_egal_4="";
		$tab_stat['mef'][$cpt_mef]['liste_aucun_motif_nj_sup_egal_4']="";

		$titre_infobulle_aucun_motif_nj_sup_egal_11=$lig_mef->libelle_edition." (aucun_motif_nj&ge;11)";
		$texte_infobulle_aucun_motif_nj_sup_egal_11="";
		$tab_stat['mef'][$cpt_mef]['liste_aucun_motif_nj_sup_egal_11']="";

		$titre_infobulle_aucun_motif_nj_4_a_10=$lig_mef->libelle_edition." (4&le;aucun_motif_nj&le;10)";
		$texte_infobulle_aucun_motif_nj_4_a_10="";
		$tab_stat['mef'][$cpt_mef]['liste_aucun_motif_nj_4_a_10']="";


		$tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_4']=array();
		$tab_stat['mef'][$cpt_mef]['ele_abs_4_a_10']=array();
		$tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_11']=array();
		$tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_4']=array();
		$tab_stat['mef'][$cpt_mef]['ele_nj_4_a_10']=array();
		$tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_11']=array();
		$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_4']=array();
		$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_4_a_10']=array();
		$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_11']=array();
		$tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_sup_egal_4']=array();
		$tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_4_a_10']=array();
		$tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_sup_egal_11']=array();

		$cpt_ele=0;
		foreach($tab_stat['mef'][$cpt_mef]['ele'] as $login_ele => $tab_ele) {
			// Absences
			// Au moins 4 absences
			if($tab_ele["nb_abs"]>=4) {
				if($tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_4']!="") {
					$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_4'].=", ";
				}
				$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_4'].=$tab_ele["designation"];
				$tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_4'][$cpt_ele]['login']=$login_ele;
				$tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_4'][$cpt_ele]['nom_prenom']=$tab_ele["designation"];

				$texte_infobulle_abs_sup_egal_4.="<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$tab_ele["designation"]."</a>";
				$texte_infobulle_abs_sup_egal_4.=" - <a href='bilan_individuel.php?id_eleve=".$tab_ele["id_eleve"]."&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
				$texte_infobulle_abs_sup_egal_4.="<br />\n";

				// Au moins 11 absences
				if($tab_ele["nb_abs"]>=11) {
					if($tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_11']!="") {
						$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_11'].=", ";
					}
					$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_11'].=$tab_ele["designation"];
					$tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_11'][$cpt_ele]['login']=$login_ele;
					$tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_11'][$cpt_ele]['nom_prenom']=$tab_ele["designation"];

					$texte_infobulle_abs_sup_egal_11.="<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$tab_ele["designation"]."</a>";
					$texte_infobulle_abs_sup_egal_11.=" - <a href='bilan_individuel.php?id_eleve=".$tab_ele["id_eleve"]."&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
					$texte_infobulle_abs_sup_egal_11.="<br />\n";
				}
				else {
					// De 4 à 10 absences
					if($tab_stat['mef'][$cpt_mef]['liste_abs_4_a_10']!="") {
						$tab_stat['mef'][$cpt_mef]['liste_abs_4_a_10'].=", ";
					}
					$tab_stat['mef'][$cpt_mef]['liste_abs_4_a_10'].=$tab_ele["designation"];
					$tab_stat['mef'][$cpt_mef]['ele_abs_4_a_10'][$cpt_ele]['login']=$login_ele;
					$tab_stat['mef'][$cpt_mef]['ele_abs_4_a_10'][$cpt_ele]['nom_prenom']=$tab_ele["designation"];

					$texte_infobulle_abs_4_a_10.="<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$tab_ele["designation"]."</a>";
					$texte_infobulle_abs_4_a_10.=" - <a href='bilan_individuel.php?id_eleve=".$tab_ele["id_eleve"]."&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
					$texte_infobulle_abs_4_a_10.="<br />\n";
				}
			}


			// Non justifiée (au sens export E.N)
			// Au moins 4 absences non-justifiables
			if($tab_ele["nj"]>=4) {
				if($tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_4']!="") {
					$tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_4'].=", ";
				}
				$tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_4'].=$tab_ele["designation"];
				$tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_4'][$cpt_ele]['login']=$login_ele;
				$tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_4'][$cpt_ele]['nom_prenom']=$tab_ele["designation"];

				$texte_infobulle_nj_sup_egal_4.="<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$tab_ele["designation"]."</a>";
				$texte_infobulle_nj_sup_egal_4.=" - <a href='bilan_individuel.php?id_eleve=".$tab_ele["id_eleve"]."&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
				$texte_infobulle_nj_sup_egal_4.="<br />\n";

				// Non valable
				if($tab_ele["non_valable"]>0) {
					if($tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_4']!="") {
						$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_4'].=", ";
					}
					$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_4'].=$tab_ele["designation"];
					$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_4'][$cpt_ele]['login']=$login_ele;
					$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_4'][$cpt_ele]['nom_prenom']=$tab_ele["designation"];

					$texte_infobulle_non_valable_nj_sup_egal_4.="<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$tab_ele["designation"]."</a>";
					$texte_infobulle_non_valable_nj_sup_egal_4.=" - <a href='bilan_individuel.php?id_eleve=".$tab_ele["id_eleve"]."&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
					$texte_infobulle_non_valable_nj_sup_egal_4.="<br />\n";
				}

				// Aucun motif
				if($tab_ele["aucun_motif"]>0) {
					if($tab_stat['mef'][$cpt_mef]['liste_aucun_motif_nj_sup_egal_4']!="") {
						$tab_stat['mef'][$cpt_mef]['liste_aucun_motif_nj_sup_egal_4'].=", ";
					}
					$tab_stat['mef'][$cpt_mef]['liste_aucun_motif_nj_sup_egal_4'].=$tab_ele["designation"];
					$tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_sup_egal_4'][$cpt_ele]['login']=$login_ele;
					$tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_sup_egal_4'][$cpt_ele]['nom_prenom']=$tab_ele["designation"];

					$texte_infobulle_aucun_motif_nj_sup_egal_4.="<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$tab_ele["designation"]."</a>";
					$texte_infobulle_aucun_motif_nj_sup_egal_4.=" - <a href='bilan_individuel.php?id_eleve=".$tab_ele["id_eleve"]."&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
					$texte_infobulle_aucun_motif_nj_sup_egal_4.="<br />\n";
				}

				// Au moins 11 absences non-justifiables
				if($tab_ele["nj"]>=11) {
					if($tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_11']!="") {
						$tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_11'].=", ";
					}
					$tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_11'].=$tab_ele["designation"];
					$tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_11'][$cpt_ele]['login']=$login_ele;
					$tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_11'][$cpt_ele]['nom_prenom']=$tab_ele["designation"];

					$texte_infobulle_nj_sup_egal_11.="<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$tab_ele["designation"]."</a>";
					$texte_infobulle_nj_sup_egal_11.=" - <a href='bilan_individuel.php?id_eleve=".$tab_ele["id_eleve"]."&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
					$texte_infobulle_nj_sup_egal_11.="<br />\n";


					// Non valable
					if($tab_ele["non_valable"]>0) {
						if($tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_11']!="") {
							$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_11'].=", ";
						}
						$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_11'].=$tab_ele["designation"];
						$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_11'][$cpt_ele]['login']=$login_ele;
						$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_11'][$cpt_ele]['nom_prenom']=$tab_ele["designation"];

						$texte_infobulle_non_valable_nj_sup_egal_11.="<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$tab_ele["designation"]."</a>";
						$texte_infobulle_non_valable_nj_sup_egal_11.=" - <a href='bilan_individuel.php?id_eleve=".$tab_ele["id_eleve"]."&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
						$texte_infobulle_non_valable_nj_sup_egal_11.="<br />\n";
					}


					// Aucun motif
					if($tab_ele["aucun_motif"]>0) {
						// Aucun motif
						if($tab_stat['mef'][$cpt_mef]['liste_aucun_motif_nj_sup_egal_11']!="") {
							$tab_stat['mef'][$cpt_mef]['liste_aucun_motif_nj_sup_egal_11'].=", ";
						}
						$tab_stat['mef'][$cpt_mef]['liste_aucun_motif_nj_sup_egal_11'].=$tab_ele["designation"];
						$tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_sup_egal_11'][$cpt_ele]['login']=$login_ele;
						$tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_sup_egal_11'][$cpt_ele]['nom_prenom']=$tab_ele["designation"];

						$texte_infobulle_aucun_motif_nj_sup_egal_11.="<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$tab_ele["designation"]."</a>";
						$texte_infobulle_aucun_motif_nj_sup_egal_11.=" - <a href='bilan_individuel.php?id_eleve=".$tab_ele["id_eleve"]."&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
						$texte_infobulle_aucun_motif_nj_sup_egal_11.="<br />\n";
					}
				}
				else {
					// De 4 à 11 absences non-justifiables
					if($tab_stat['mef'][$cpt_mef]['liste_nj_4_a_10']!="") {
						$tab_stat['mef'][$cpt_mef]['liste_nj_4_a_10'].=", ";
					}
					$tab_stat['mef'][$cpt_mef]['liste_nj_4_a_10'].=$tab_ele["designation"];
					$tab_stat['mef'][$cpt_mef]['ele_nj_4_a_10'][$cpt_ele]['login']=$login_ele;
					$tab_stat['mef'][$cpt_mef]['ele_nj_4_a_10'][$cpt_ele]['nom_prenom']=$tab_ele["designation"];

					$texte_infobulle_nj_4_a_10.="<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$tab_ele["designation"]."</a>";
					$texte_infobulle_nj_4_a_10.=" - <a href='bilan_individuel.php?id_eleve=".$tab_ele["id_eleve"]."&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
					$texte_infobulle_nj_4_a_10.="<br />\n";


					// Non valable
					if($tab_ele["non_valable"]>0) {
						if($tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_4_a_10']!="") {
							$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_4_a_10'].=", ";
						}
						$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_4_a_10'].=$tab_ele["designation"];
						$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_4_a_10'][$cpt_ele]['login']=$login_ele;
						$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_4_a_10'][$cpt_ele]['nom_prenom']=$tab_ele["designation"];

						$texte_infobulle_non_valable_nj_4_a_10.="<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$tab_ele["designation"]."</a>";
						$texte_infobulle_non_valable_nj_4_a_10.=" - <a href='bilan_individuel.php?id_eleve=".$tab_ele["id_eleve"]."&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
						$texte_infobulle_non_valable_nj_4_a_10.="<br />\n";
					}


					// Aucun motif
					if($tab_ele["aucun_motif"]>0) {
						if($tab_stat['mef'][$cpt_mef]['liste_aucun_motif_nj_4_a_10']!="") {
							$tab_stat['mef'][$cpt_mef]['liste_aucun_motif_nj_4_a_10'].=", ";
						}
						$tab_stat['mef'][$cpt_mef]['liste_aucun_motif_nj_4_a_10'].=$tab_ele["designation"];
						$tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_4_a_10'][$cpt_ele]['login']=$login_ele;
						$tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_4_a_10'][$cpt_ele]['nom_prenom']=$tab_ele["designation"];

						$texte_infobulle_aucun_motif_nj_4_a_10.="<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$tab_ele["designation"]."</a>";
						$texte_infobulle_aucun_motif_nj_4_a_10.=" - <a href='bilan_individuel.php?id_eleve=".$tab_ele["id_eleve"]."&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
						$texte_infobulle_aucun_motif_nj_4_a_10.="<br />\n";
					}
				}
			}
			$cpt_ele++;
		}
		$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_nj_sup_egal_4',$titre_infobulle_nj_sup_egal_4,"",$texte_infobulle_nj_sup_egal_4,"",35,0,'y','y','n','n');
		$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_nj_sup_egal_11',$titre_infobulle_nj_sup_egal_11,"",$texte_infobulle_nj_sup_egal_11,"",35,0,'y','y','n','n');
		$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_nj_4_a_10',$titre_infobulle_nj_4_a_10,"",$texte_infobulle_nj_4_a_10,"",35,0,'y','y','n','n');

		$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_aucun_motif_nj_sup_egal_4',$titre_infobulle_aucun_motif_nj_sup_egal_4,"",$texte_infobulle_aucun_motif_nj_sup_egal_4,"",35,0,'y','y','n','n');
		$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_aucun_motif_nj_sup_egal_11',$titre_infobulle_aucun_motif_nj_sup_egal_11,"",$texte_infobulle_aucun_motif_nj_sup_egal_11,"",35,0,'y','y','n','n');
		$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_aucun_motif_nj_4_a_10',$titre_infobulle_aucun_motif_nj_4_a_10,"",$texte_infobulle_aucun_motif_nj_4_a_10,"",35,0,'y','y','n','n');


		$tab_stat['mef'][$cpt_mef]['abs_sup_egal_4']=count($tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_4']);
		$tab_stat['mef'][$cpt_mef]['abs_4_a_10']=count($tab_stat['mef'][$cpt_mef]['ele_abs_4_a_10']);
		$tab_stat['mef'][$cpt_mef]['abs_sup_egal_11']=count($tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_11']);

		$tab_stat['mef'][$cpt_mef]['nj_sup_egal_4']=count($tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_4']);
		$tab_stat['mef'][$cpt_mef]['nj_4_a_10']=count($tab_stat['mef'][$cpt_mef]['ele_nj_4_a_10']);
		$tab_stat['mef'][$cpt_mef]['nj_sup_egal_11']=count($tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_11']);

		$tab_stat['mef'][$cpt_mef]['non_valable_nj_sup_egal_4']=count($tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_4']);
		$tab_stat['mef'][$cpt_mef]['non_valable_nj_4_a_10']=count($tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_4_a_10']);
		$tab_stat['mef'][$cpt_mef]['non_valable_nj_sup_egal_11']=count($tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_11']);

		$tab_stat['mef'][$cpt_mef]['aucun_motif_nj_sup_egal_4']=count($tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_sup_egal_4']);
		$tab_stat['mef'][$cpt_mef]['aucun_motif_nj_4_a_10']=count($tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_4_a_10']);
		$tab_stat['mef'][$cpt_mef]['aucun_motif_nj_sup_egal_11']=count($tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_sup_egal_11']);

		if($tab_stat['mef'][$cpt_mef]['effectif']==0) {
			$tab_stat['mef'][$cpt_mef]['pourcentage_abs_sup_egal_4']=0;
			$tab_stat['mef'][$cpt_mef]['pourcentage_abs_4_a_10']=0;
			$tab_stat['mef'][$cpt_mef]['pourcentage_abs_sup_egal_11']=0;

			$tab_stat['mef'][$cpt_mef]['pourcentage_nj_sup_egal_4']=0;
			$tab_stat['mef'][$cpt_mef]['pourcentage_nj_4_a_10']=0;
			$tab_stat['mef'][$cpt_mef]['pourcentage_nj_sup_egal_11']=0;

			$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_sup_egal_4']=0;
			$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_4_a_10']=0;
			$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_sup_egal_11']=0;

			$tab_stat['mef'][$cpt_mef]['pourcentage_aucun_motif_nj_sup_egal_4']=0;
			$tab_stat['mef'][$cpt_mef]['pourcentage_aucun_motif_nj_4_a_10']=0;
			$tab_stat['mef'][$cpt_mef]['pourcentage_aucun_motif_nj_sup_egal_11']=0;
		}
		else {
			$tab_stat['mef'][$cpt_mef]['pourcentage_abs_sup_egal_4']=round(count($tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_4'])/$tab_stat['mef'][$cpt_mef]['effectif'],2);
			$tab_stat['mef'][$cpt_mef]['pourcentage_abs_4_a_10']=round(count($tab_stat['mef'][$cpt_mef]['ele_abs_4_a_10'])/$tab_stat['mef'][$cpt_mef]['effectif'],2);
			$tab_stat['mef'][$cpt_mef]['pourcentage_abs_sup_egal_11']=round(count($tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_11'])/$tab_stat['mef'][$cpt_mef]['effectif'],2);

			$tab_stat['mef'][$cpt_mef]['pourcentage_nj_sup_egal_4']=round(count($tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_4'])/$tab_stat['mef'][$cpt_mef]['effectif'],2);
			$tab_stat['mef'][$cpt_mef]['pourcentage_nj_4_a_10']=round(count($tab_stat['mef'][$cpt_mef]['ele_nj_4_a_10'])/$tab_stat['mef'][$cpt_mef]['effectif'],2);
			$tab_stat['mef'][$cpt_mef]['pourcentage_nj_sup_egal_11']=round(count($tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_11'])/$tab_stat['mef'][$cpt_mef]['effectif'],2);

			$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_sup_egal_4']=round(count($tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_4'])/$tab_stat['mef'][$cpt_mef]['effectif'],2);
			$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_4_a_10']=round(count($tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_4_a_10'])/$tab_stat['mef'][$cpt_mef]['effectif'],2);
			$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_sup_egal_11']=round(count($tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_11'])/$tab_stat['mef'][$cpt_mef]['effectif'],2);

			$tab_stat['mef'][$cpt_mef]['pourcentage_aucun_motif_nj_sup_egal_4']=round(count($tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_sup_egal_4'])/$tab_stat['mef'][$cpt_mef]['effectif'],2);
			$tab_stat['mef'][$cpt_mef]['pourcentage_aucun_motif_nj_4_a_10']=round(count($tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_4_a_10'])/$tab_stat['mef'][$cpt_mef]['effectif'],2);
			$tab_stat['mef'][$cpt_mef]['pourcentage_aucun_motif_nj_sup_egal_11']=round(count($tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_sup_egal_11'])/$tab_stat['mef'][$cpt_mef]['effectif'],2);

			if($debug=="y") {
				// On passe à trois décimales pour les pourcentages
				$tab_stat['mef'][$cpt_mef]['pourcentage_abs_sup_egal_4']=round(count($tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_4'])/$tab_stat['mef'][$cpt_mef]['effectif'],3);
				$tab_stat['mef'][$cpt_mef]['pourcentage_abs_4_a_10']=round(count($tab_stat['mef'][$cpt_mef]['ele_abs_4_a_10'])/$tab_stat['mef'][$cpt_mef]['effectif'],3);
				$tab_stat['mef'][$cpt_mef]['pourcentage_abs_sup_egal_11']=round(count($tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_11'])/$tab_stat['mef'][$cpt_mef]['effectif'],3);

				$tab_stat['mef'][$cpt_mef]['pourcentage_nj_sup_egal_4']=round(count($tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_4'])/$tab_stat['mef'][$cpt_mef]['effectif'],3);
				$tab_stat['mef'][$cpt_mef]['pourcentage_nj_4_a_10']=round(count($tab_stat['mef'][$cpt_mef]['ele_nj_4_a_10'])/$tab_stat['mef'][$cpt_mef]['effectif'],3);
				$tab_stat['mef'][$cpt_mef]['pourcentage_nj_sup_egal_11']=round(count($tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_11'])/$tab_stat['mef'][$cpt_mef]['effectif'],3);

				$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_sup_egal_4']=round(count($tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_4'])/$tab_stat['mef'][$cpt_mef]['effectif'],3);
				$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_4_a_10']=round(count($tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_4_a_10'])/$tab_stat['mef'][$cpt_mef]['effectif'],3);
				$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_sup_egal_11']=round(count($tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_11'])/$tab_stat['mef'][$cpt_mef]['effectif'],3);

				$tab_stat['mef'][$cpt_mef]['pourcentage_aucun_motif_nj_sup_egal_4']=round(count($tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_sup_egal_4'])/$tab_stat['mef'][$cpt_mef]['effectif'],3);
				$tab_stat['mef'][$cpt_mef]['pourcentage_aucun_motif_nj_4_a_10']=round(count($tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_4_a_10'])/$tab_stat['mef'][$cpt_mef]['effectif'],3);
				$tab_stat['mef'][$cpt_mef]['pourcentage_aucun_motif_nj_sup_egal_11']=round(count($tab_stat['mef'][$cpt_mef]['ele_aucun_motif_nj_sup_egal_11'])/$tab_stat['mef'][$cpt_mef]['effectif'],3);
			}
		}


		/*
		//======================================
		// A partir de 4 demi-journées
		// Justifiée ou non:
		// PROBLEME: Un élève qui a un retard et qui dans la même demi-journée sèche des cours, si aucun motifs_absences n'est saisi, il ne sera pas totalisé ici.
		$sql="SELECT e.login,e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
		a.manquement_obligation_presence='1' AND 
		(a.retards='0' OR a.motifs_absences!='') AND 
		a.eleve_id=e.id_eleve AND 
		e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		GROUP BY a.eleve_id HAVING COUNT(a.manquement_obligation_presence)>=4;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$tab_stat['mef'][$cpt_mef]['abs_sup_egal_4']=mysqli_num_rows($res);
		if(mysqli_num_rows($res)>0) {

			$titre_infobulle=$lig_mef->libelle_edition." (nb_abs&gt;=4)";
			$texte_infobulle="";

			$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_4']="";
			$cpt_ele=0;
			while($lig_ele=mysqli_fetch_object($res)) {
				if($cpt_ele>0) {
					$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_4'].=", ";
				}
				$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_4'].=$lig_ele->nom." ".$lig_ele->prenom;
				$tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_4'][$cpt_ele]['login']=$lig_ele->login;
				$tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_4'][$cpt_ele]['nom_prenom']=$lig_ele->nom." ".$lig_ele->prenom;

				$texte_infobulle.="<a href='../eleves/visu_eleve.php?ele_login=$lig_ele->login&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$lig_ele->nom." ".$lig_ele->prenom."</a>";
				$texte_infobulle.=" - <a href='bilan_individuel.php?id_eleve=$lig_ele->eleve_id&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
				$texte_infobulle.="<br />\n";

				$cpt_ele++;
			}

			$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_abs_sup_egal_4',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		}

		// Non justifiée: // IL Y A NON-JUSTIFIEE AU SENS GEPI et NON JUSTIFIEE AU SENS export E-N
		$sql="SELECT e.login,e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
		a.manquement_obligation_presence='1' AND 
		(a.retards='0' OR a.motifs_absences!='') AND 
		a.non_justifiee!='0' AND 
		a.eleve_id=e.id_eleve AND 
		e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		GROUP BY a.eleve_id HAVING COUNT(a.non_justifiee)>=4;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$tab_stat['mef'][$cpt_mef]['nj_sup_egal_4']=mysqli_num_rows($res);
		if($tab_stat['mef'][$cpt_mef]['effectif']==0) {
			$tab_stat['mef'][$cpt_mef]['pourcentage_nj_sup_egal_4']=0;
		}
		else {
			$tab_stat['mef'][$cpt_mef]['pourcentage_nj_sup_egal_4']=round(mysqli_num_rows($res)/$tab_stat['mef'][$cpt_mef]['effectif'],2);
		}
		if(mysqli_num_rows($res)>0) {

			$titre_infobulle=$lig_mef->libelle_edition." (nj&gt;=4)";
			$texte_infobulle="";

			$tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_4']="";
			$cpt_ele=0;
			while($lig_ele=mysqli_fetch_object($res)) {
				if($cpt_ele>0) {
					$tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_4'].=", ";
				}
				$tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_4'].=$lig_ele->nom." ".$lig_ele->prenom;
				$tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_4'][$cpt_ele]['login']=$lig_ele->login;
				$tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_4'][$cpt_ele]['nom_prenom']=$lig_ele->nom." ".$lig_ele->prenom;

				$texte_infobulle.="<a href='../eleves/visu_eleve.php?ele_login=$lig_ele->login&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$lig_ele->nom." ".$lig_ele->prenom."</a>";
				$texte_infobulle.=" - <a href='bilan_individuel.php?id_eleve=$lig_ele->eleve_id&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
				$texte_infobulle.="<br />\n";

				$cpt_ele++;
			}

			$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_nj_sup_egal_4',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');
		}

		// Aucun motif (i.e. non valable)
		// Comment récupérer ça?
		// Motif on valable: C'est une partie des non justifié?

		$sql="SELECT e.login,e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
		a.manquement_obligation_presence='1' AND 
		(a.retards='0' OR a.motifs_absences!='') AND 
		a.non_justifiee!='0' AND 
		a.eleve_id=e.id_eleve AND 
		e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		GROUP BY a.eleve_id HAVING COUNT(a.non_justifiee)>=4;";

		$sql="SELECT e.login,e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
		a.manquement_obligation_presence='1' AND 
		(a.retards='0' OR a.motifs_absences!='') AND 
		a.eleve_id=e.id_eleve AND 
		e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		GROUP BY a.eleve_id HAVING COUNT(a.manquement_obligation_presence)>=4;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		//$tab_stat['mef'][$cpt_mef]['non_valable_nj_sup_egal_4']=mysqli_num_rows($res);
		$tab_stat['mef'][$cpt_mef]['non_valable_nj_sup_egal_4']=0;
		$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_sup_egal_4']=0;

		$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_4']="";
		$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_4']=array();
		if(mysqli_num_rows($res)>0) {

			$titre_infobulle=$lig_mef->libelle_edition." (non_valable&gt;=4)";
			$texte_infobulle="";

			$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_4']="";
			$cpt_ele=0;
			while($lig_ele=mysqli_fetch_object($res)) {
				$cpt_non_valable=0;

				$sql="SELECT * FROM a_agregation_decompte a WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
				a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
				a.manquement_obligation_presence='1' AND 
				(a.retards='0' OR a.motifs_absences!='') AND 
				a.non_justifiee!='0' AND 
				a.eleve_id='".$lig_ele->eleve_id."';";

				$sql="SELECT * FROM a_agregation_decompte a WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
				a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
				a.manquement_obligation_presence='1' AND 
				(a.retards='0' OR a.motifs_absences!='') AND 
				a.eleve_id='".$lig_ele->eleve_id."';";
				$res2=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res2)>0) {
					while($lig2=mysqli_fetch_object($res2)) {
						if(is_null($lig2->motifs_absences)) {
							$cpt_non_valable++;
						}
						else {
							$tmp_tab=explode("|", preg_replace("/[^0-9|]/", "", $lig2->motifs_absences));
							for($loop=0;$loop<count($tmp_tab);$loop++) {
								if(in_array($tmp_tab[$loop], $tab_motifs_non_valables)) {
									$cpt_non_valable++;
									break;
								}
							}
						}
					}
				}

				if($cpt_non_valable>=4) {
					if($cpt_ele>0) {
						$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_4'].=", ";
					}
					$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_4'].=$lig_ele->nom." ".$lig_ele->prenom;
					$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_4'][$cpt_ele]['login']=$lig_ele->login;
					$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_4'][$cpt_ele]['nom_prenom']=$lig_ele->nom." ".$lig_ele->prenom;

					$texte_infobulle.="<a href='../eleves/visu_eleve.php?ele_login=$lig_ele->login&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$lig_ele->nom." ".$lig_ele->prenom."</a>";
					$texte_infobulle.=" - <a href='bilan_individuel.php?id_eleve=$lig_ele->eleve_id&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
					$texte_infobulle.="<br />\n";

					$cpt_ele++;
				}
			}
			$tab_stat['mef'][$cpt_mef]['non_valable_nj_sup_egal_4']=count($tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_4']);
			$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_sup_egal_4']=round($tab_stat['mef'][$cpt_mef]['non_valable_nj_sup_egal_4']/$tab_stat['mef'][$cpt_mef]['effectif'],2);

			$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_non_valable_nj_sup_egal_4',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		}
		//======================================

		//======================================
		// De 4 à 10 demi-journées
		// Justifiée ou non:
		// PROBLEME: Un élève qui a un retard et qui dans la même demi-journée sèche des cours, si aucun motifs_absences n'est saisi, il ne sera pas totalisé ici.
		$sql="SELECT e.login,e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
		a.manquement_obligation_presence='1' AND 
		(a.retards='0' OR a.motifs_absences!='') AND 
		a.eleve_id=e.id_eleve AND 
		e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		GROUP BY a.eleve_id HAVING COUNT(a.manquement_obligation_presence)>=4 and COUNT(a.manquement_obligation_presence)<=10;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$tab_stat['mef'][$cpt_mef]['abs_4_a_10']=mysqli_num_rows($res);
		if(mysqli_num_rows($res)>0) {

			$titre_infobulle=$lig_mef->libelle_edition." (4=&lt;nb_abs&lt;=10)";
			$texte_infobulle="";

			$tab_stat['mef'][$cpt_mef]['liste_abs_4_a_10']="";
			$cpt_ele=0;
			while($lig_ele=mysqli_fetch_object($res)) {
				if($cpt_ele>0) {
					$tab_stat['mef'][$cpt_mef]['liste_abs_4_a_10'].=", ";
				}
				$tab_stat['mef'][$cpt_mef]['liste_abs_4_a_10'].=$lig_ele->nom." ".$lig_ele->prenom;
				$tab_stat['mef'][$cpt_mef]['ele_abs_4_a_10'][$cpt_ele]['login']=$lig_ele->login;
				$tab_stat['mef'][$cpt_mef]['ele_abs_4_a_10'][$cpt_ele]['nom_prenom']=$lig_ele->nom." ".$lig_ele->prenom;

				$texte_infobulle.="<a href='../eleves/visu_eleve.php?ele_login=$lig_ele->login&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$lig_ele->nom." ".$lig_ele->prenom."</a>";
				$texte_infobulle.=" - <a href='bilan_individuel.php?id_eleve=$lig_ele->eleve_id&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
				$texte_infobulle.="<br />\n";

				$cpt_ele++;
			}

			$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_abs_4_a_10',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');
		}

		// Non justifiées
		$sql="SELECT e.login,e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
		a.manquement_obligation_presence='1' AND 
		(a.retards='0' OR a.motifs_absences!='') AND 
		a.non_justifiee!='0' AND 
		a.eleve_id=e.id_eleve AND 
		e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		GROUP BY a.eleve_id HAVING COUNT(a.non_justifiee)>=4 and COUNT(a.non_justifiee)<=10;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$tab_stat['mef'][$cpt_mef]['nj_4_a_10']=mysqli_num_rows($res);
		if($tab_stat['mef'][$cpt_mef]['effectif']==0) {
			$tab_stat['mef'][$cpt_mef]['pourcentage_nj_4_a_10']=0;
		}
		else {
			$tab_stat['mef'][$cpt_mef]['pourcentage_nj_4_a_10']=round(mysqli_num_rows($res)/$tab_stat['mef'][$cpt_mef]['effectif'],2);
		}
		if(mysqli_num_rows($res)>0) {

			$titre_infobulle=$lig_mef->libelle_edition." (4=&lt;nj&lt;=10)";
			$texte_infobulle="";

			$tab_stat['mef'][$cpt_mef]['liste_nj_4_a_10']="";
			$cpt_ele=0;
			while($lig_ele=mysqli_fetch_object($res)) {
				if($cpt_ele>0) {
					$tab_stat['mef'][$cpt_mef]['liste_nj_4_a_10'].=", ";
				}
				$tab_stat['mef'][$cpt_mef]['liste_nj_4_a_10'].=$lig_ele->nom." ".$lig_ele->prenom;
				$tab_stat['mef'][$cpt_mef]['ele_nj_4_a_10'][$cpt_ele]['login']=$lig_ele->login;
				$tab_stat['mef'][$cpt_mef]['ele_nj_4_a_10'][$cpt_ele]['nom_prenom']=$lig_ele->nom." ".$lig_ele->prenom;

				$texte_infobulle.="<a href='../eleves/visu_eleve.php?ele_login=$lig_ele->login&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$lig_ele->nom." ".$lig_ele->prenom."</a>";
				$texte_infobulle.=" - <a href='bilan_individuel.php?id_eleve=$lig_ele->eleve_id&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
				$texte_infobulle.="<br />\n";

				$cpt_ele++;
			}

			$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_nj_4_a_10',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');
		}

		// Aucun motif (i.e. non valable)
		// Comment récupérer ça?
		// Motif on valable: C'est une partie des non justifié?

		$sql="SELECT e.login,e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
		a.manquement_obligation_presence='1' AND 
		(a.retards='0' OR a.motifs_absences!='') AND 
		a.non_justifiee!='0' AND 
		a.eleve_id=e.id_eleve AND 
		e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		GROUP BY a.eleve_id HAVING COUNT(a.non_justifiee)>=4 AND COUNT(a.non_justifiee)<=10;";

		$sql="SELECT e.login,e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
		a.manquement_obligation_presence='1' AND 
		(a.retards='0' OR a.motifs_absences!='') AND 
		a.eleve_id=e.id_eleve AND 
		e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		GROUP BY a.eleve_id HAVING COUNT(a.manquement_obligation_presence)>=4;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		//$tab_stat['mef'][$cpt_mef]['non_valable_nj_4_a_10']=mysqli_num_rows($res);
		$tab_stat['mef'][$cpt_mef]['non_valable_nj_4_a_10']=0;
		$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_4_a_10']=0;

		$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_4_a_10']="";
		$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_4_a_10']=array();
		if(mysqli_num_rows($res)>0) {

			$titre_infobulle=$lig_mef->libelle_edition." (4&gt;=non_valable&gt;=10)";
			$texte_infobulle="";

			$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_4_a_10']="";
			$cpt_ele=0;
			while($lig_ele=mysqli_fetch_object($res)) {
				$cpt_non_valable=0;

				$sql="SELECT * FROM a_agregation_decompte a WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
				a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
				a.manquement_obligation_presence='1' AND 
				(a.retards='0' OR a.motifs_absences!='') AND 
				a.non_justifiee!='0' AND 
				a.eleve_id='".$lig_ele->eleve_id."';";

				$sql="SELECT * FROM a_agregation_decompte a WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
				a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
				a.manquement_obligation_presence='1' AND 
				(a.retards='0' OR a.motifs_absences!='') AND 
				a.eleve_id='".$lig_ele->eleve_id."';";
				$res2=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res2)>0) {
					while($lig2=mysqli_fetch_object($res2)) {
						if(is_null($lig2->motifs_absences)) {
							$cpt_non_valable++;
						}
						else {
							$tmp_tab=explode("|", preg_replace("/[^0-9|]/", "", $lig2->motifs_absences));
							for($loop=0;$loop<count($tmp_tab);$loop++) {
								if(in_array($tmp_tab[$loop], $tab_motifs_non_valables)) {
									$cpt_non_valable++;
									break;
								}
							}
						}
					}
				}

				if(($cpt_non_valable>=4)&&($cpt_non_valable<=10)) {
					if($cpt_ele>0) {
						$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_4_a_10'].=", ";
					}
					$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_4_a_10'].=$lig_ele->nom." ".$lig_ele->prenom;
					$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_4_a_10'][$cpt_ele]['login']=$lig_ele->login;
					$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_4_a_10'][$cpt_ele]['nom_prenom']=$lig_ele->nom." ".$lig_ele->prenom;

					$texte_infobulle.="<a href='../eleves/visu_eleve.php?ele_login=$lig_ele->login&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$lig_ele->nom." ".$lig_ele->prenom."</a>";
					$texte_infobulle.=" - <a href='bilan_individuel.php?id_eleve=$lig_ele->eleve_id&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
					$texte_infobulle.="<br />\n";

					$cpt_ele++;
				}
			}
			$tab_stat['mef'][$cpt_mef]['non_valable_nj_4_a_10']=count($tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_4_a_10']);
			$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_4_a_10']=round($tab_stat['mef'][$cpt_mef]['non_valable_nj_4_a_10']/$tab_stat['mef'][$cpt_mef]['effectif'],2);

			$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_non_valable_nj_4_a_10',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		}
		//======================================

		//======================================
		// A partir de 11 demi-journées
		// Justifiée ou non:
		// PROBLEME: Un élève qui a un retard et qui dans la même demi-journée sèche des cours, si aucun motifs_absences n'est saisi, il ne sera pas totalisé ici.
		$sql="SELECT e.login,e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
		a.manquement_obligation_presence='1' AND 
		(a.retards='0' OR a.motifs_absences!='') AND 
		a.eleve_id=e.id_eleve AND 
		e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		GROUP BY a.eleve_id HAVING COUNT(a.manquement_obligation_presence)>=11;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$tab_stat['mef'][$cpt_mef]['abs_sup_egal_11']=mysqli_num_rows($res);
		if(mysqli_num_rows($res)>0) {

			$titre_infobulle=$lig_mef->libelle_edition." (nb_abs&gt;=11)";
			$texte_infobulle="";

			$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_11']="";
			$cpt_ele=0;
			while($lig_ele=mysqli_fetch_object($res)) {
				if($cpt_ele>0) {
					$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_11'].=", ";
				}
				$tab_stat['mef'][$cpt_mef]['liste_abs_sup_egal_11'].=$lig_ele->nom." ".$lig_ele->prenom;
				$tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_11'][$cpt_ele]['login']=$lig_ele->login;
				$tab_stat['mef'][$cpt_mef]['ele_abs_sup_egal_11'][$cpt_ele]['nom_prenom']=$lig_ele->nom." ".$lig_ele->prenom;

				$texte_infobulle.="<a href='../eleves/visu_eleve.php?ele_login=$lig_ele->login&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$lig_ele->nom." ".$lig_ele->prenom."</a>";
				$texte_infobulle.=" - <a href='bilan_individuel.php?id_eleve=$lig_ele->eleve_id&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
				$texte_infobulle.="<br />\n";

				$cpt_ele++;
			}

			$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_abs_sup_egal_11',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');
		}

		// Non justifiées
		$sql="SELECT e.login,e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
		a.manquement_obligation_presence='1' AND 
		(a.retards='0' OR a.motifs_absences!='') AND 
		a.non_justifiee!='0' AND 
		a.eleve_id=e.id_eleve AND 
		e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		GROUP BY a.eleve_id HAVING COUNT(a.non_justifiee)>=11;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$tab_stat['mef'][$cpt_mef]['nj_sup_egal_11']=mysqli_num_rows($res);
		if($tab_stat['mef'][$cpt_mef]['effectif']==0) {
			$tab_stat['mef'][$cpt_mef]['pourcentage_nj_sup_egal_11']=0;
		}
		else {
			$tab_stat['mef'][$cpt_mef]['pourcentage_nj_sup_egal_11']=round(mysqli_num_rows($res)/$tab_stat['mef'][$cpt_mef]['effectif'],2);
		}
		if(mysqli_num_rows($res)>0) {

			$titre_infobulle=$lig_mef->libelle_edition." (nj&gt;=11)";
			$texte_infobulle="";

			$tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_11']="";
			$cpt_ele=0;
			while($lig_ele=mysqli_fetch_object($res)) {
				if($cpt_ele>0) {
					$tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_11'].=", ";
				}
				$tab_stat['mef'][$cpt_mef]['liste_nj_sup_egal_11'].=$lig_ele->nom." ".$lig_ele->prenom;
				$tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_11'][$cpt_ele]['login']=$lig_ele->login;
				$tab_stat['mef'][$cpt_mef]['ele_nj_sup_egal_11'][$cpt_ele]['nom_prenom']=$lig_ele->nom." ".$lig_ele->prenom;

				$texte_infobulle.="<a href='../eleves/visu_eleve.php?ele_login=$lig_ele->login&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$lig_ele->nom." ".$lig_ele->prenom."</a>";
				$texte_infobulle.=" - <a href='bilan_individuel.php?id_eleve=$lig_ele->eleve_id&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
				$texte_infobulle.="<br />\n";

				$cpt_ele++;
			}

			$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_nj_sup_egal_11',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');
		}

		// Aucun motif (i.e. non valable)
		// Comment récupérer ça?
		// Motif on valable: C'est une partie des non justifié? Pas forcément: Cas du parent qui met comme justification "Enlevé par les ovnis"

		$sql="SELECT e.login,e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
		a.manquement_obligation_presence='1' AND 
		(a.retards='0' OR a.motifs_absences!='') AND 
		a.non_justifiee!='0' AND 
		a.eleve_id=e.id_eleve AND 
		e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		GROUP BY a.eleve_id HAVING COUNT(a.non_justifiee)>=11;";

		$sql="SELECT e.login,e.nom,e.prenom,a.eleve_id FROM a_agregation_decompte a, eleves e 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
		a.manquement_obligation_presence='1' AND 
		(a.retards='0' OR a.motifs_absences!='') AND 
		a.eleve_id=e.id_eleve AND 
		e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		GROUP BY a.eleve_id HAVING COUNT(a.manquement_obligation_presence)>=11;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		//$tab_stat['mef'][$cpt_mef]['non_valable_nj_sup_egal_11']=mysqli_num_rows($res);
		$tab_stat['mef'][$cpt_mef]['non_valable_nj_sup_egal_11']=0;
		$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_sup_egal_11']=0;

		$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_11']="";
		$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_11']=array();
		if(mysqli_num_rows($res)>0) {

			$titre_infobulle=$lig_mef->libelle_edition." (non_valable&gt;=11)";
			$texte_infobulle="";

			$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_11']="";
			$cpt_ele=0;
			while($lig_ele=mysqli_fetch_object($res)) {
				$cpt_non_valable=0;

				$sql="SELECT * FROM a_agregation_decompte a WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
				a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
				a.manquement_obligation_presence='1' AND 
				(a.retards='0' OR a.motifs_absences!='') AND 
				a.non_justifiee!='0' AND 
				a.eleve_id='".$lig_ele->eleve_id."';";

				$sql="SELECT * FROM a_agregation_decompte a WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
				a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
				a.manquement_obligation_presence='1' AND 
				(a.retards='0' OR a.motifs_absences!='') AND 
				a.eleve_id='".$lig_ele->eleve_id."';";
				$res2=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res2)>0) {
					while($lig2=mysqli_fetch_object($res2)) {
						if(is_null($lig2->motifs_absences)) {
							$cpt_non_valable++;
						}
						else {
							$tmp_tab=explode("|", preg_replace("/[^0-9|]/", "", $lig2->motifs_absences));
							for($loop=0;$loop<count($tmp_tab);$loop++) {
								if(in_array($tmp_tab[$loop], $tab_motifs_non_valables)) {
									$cpt_non_valable++;
									break;
								}
							}
						}
					}
				}

				if($cpt_non_valable>=11) {
					if($cpt_ele>0) {
						$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_11'].=", ";
					}
					$tab_stat['mef'][$cpt_mef]['liste_non_valable_nj_sup_egal_11'].=$lig_ele->nom." ".$lig_ele->prenom;
					$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_11'][$cpt_ele]['login']=$lig_ele->login;
					$tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_11'][$cpt_ele]['nom_prenom']=$lig_ele->nom." ".$lig_ele->prenom;

					$texte_infobulle.="<a href='../eleves/visu_eleve.php?ele_login=$lig_ele->login&amp;onglet=absences&amp;afficher_strictement_englobee=n&amp;quitter_la_page=y&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir la fiche élève (onglet Absences).\" target='_blank'>".$lig_ele->nom." ".$lig_ele->prenom."</a>";
					$texte_infobulle.=" - <a href='bilan_individuel.php?id_eleve=$lig_ele->eleve_id&amp;affichage=html&amp;tri=&amp;sans_commentaire=&amp;texte_conditionnel=&amp;filtrage=&amp;ndj=&amp;ndjnj=&amp;nr=&amp;date_absence_eleve_debut=01/$mois/$annee_extract&amp;date_absence_eleve_fin=$dernier_jour_du_mois/$mois/$annee_extract' title=\"Voir le bilan de l'élève pour le mois choisi.\" target='_blank'>Bilan</a>\n";
					$texte_infobulle.="<br />\n";

					$cpt_ele++;
				}
			}
			$tab_stat['mef'][$cpt_mef]['non_valable_nj_sup_egal_11']=count($tab_stat['mef'][$cpt_mef]['ele_non_valable_nj_sup_egal_11']);
			$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_sup_egal_11']=round($tab_stat['mef'][$cpt_mef]['non_valable_nj_sup_egal_11']/$tab_stat['mef'][$cpt_mef]['effectif'],2);

			$tabdiv_infobulle_0[]=creer_div_infobulle('infobulle_mef_'.$cpt_mef.'_non_valable_nj_sup_egal_11',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		}
		//======================================
		// Correctif pour les arrondis
		$tab_stat['mef'][$cpt_mef]['pourcentage_nj_sup_egal_4']=$tab_stat['mef'][$cpt_mef]['pourcentage_nj_4_a_10']+$tab_stat['mef'][$cpt_mef]['pourcentage_nj_sup_egal_11'];
		$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_sup_egal_4']=$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_4_a_10']+$tab_stat['mef'][$cpt_mef]['pourcentage_non_valable_nj_sup_egal_11'];
		//======================================
		*/

		$cpt_mef++;
	}

	// TOTAUX
	$tab_categories=array('abs_sup_egal_4',
	'nj_sup_egal_4',
	'non_valable_nj_sup_egal_4',
	'aucun_motif_nj_sup_egal_4',
	'abs_4_a_10',
	'nj_4_a_10',
	'non_valable_nj_4_a_10',
	'aucun_motif_nj_4_a_10',
	'abs_sup_egal_11',
	'nj_sup_egal_11',
	'non_valable_nj_sup_egal_11',
	'aucun_motif_nj_sup_egal_11');
	for($loop_c=0;$loop_c<count($tab_categories);$loop_c++) {
		$tab_stat['totaux'][$tab_categories[$loop_c]]=0;
	}
	for($loop_mef=0;$loop_mef<count($tab_stat['mef']);$loop_mef++) {
		for($loop_c=0;$loop_c<count($tab_categories);$loop_c++) {
			$tab_stat['totaux'][$tab_categories[$loop_c]]+=$tab_stat['mef'][$loop_mef][$tab_categories[$loop_c]];
		}
	}

	/*
	$tab_prefixe_mois=array();
	$tab_prefixe_mois["01"]="janv";
	$tab_prefixe_mois["02"]="fevr";
	$tab_prefixe_mois["03"]="mars";
	$tab_prefixe_mois["04"]="avr";
	$tab_prefixe_mois["05"]="mai";
	$tab_prefixe_mois["06"]="juin";
	$tab_prefixe_mois["07"]="juil";
	$tab_prefixe_mois["08"]="aout";
	$tab_prefixe_mois["09"]="sept";
	$tab_prefixe_mois["10"]="oct";
	$tab_prefixe_mois["11"]="nov";
	$tab_prefixe_mois["12"]="dec";


	$sql="SELECT DISTINCT eleve_id FROM a_agregation_decompte a 
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
			a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND 
			a.manquement_obligation_presence='1' AND 
			(a.retards='0' OR a.motifs_absences!='') AND 
			a.non_justifiee!='0' AND ";
	*/

	$non_calcule="";
	//$csv="Mois ;".$tab_prefixe_mois[$mois]."-".mb_substr($annee_extract,2).";;;;;;;;;;;;
	$csv="Mois ;".$tab_stat["mois"].";;;;;;;;;;;;
Nom établissement;".getSettingValue("gepiSchoolName").";;;;;;;;;;;;
Ville;".getSettingValue("gepiSchoolCity").";;;;;;;;;;;;
Logiciel;GEPI;;;;;;;;;;;;
Numéro UAI (RNE);".getSettingValue("gepiSchoolRne").";;;;;;;;;;;;
Nombre total d'élèves scolarisés dans l'établissement;".$tab_stat["effectif_total"].";;;;;;;;;;;;
Nombre total d'heures d'enseignement estimé dans le mois;".$non_calcule.";;;;;;;;;;;;
% d'heures d'enseignement perdu pour cause d'absences;".$non_calcule.";;;;;;;;;;;;
Nombre total d'élèves signalés à l'inspection académique dans le mois;".$non_calcule.";;;;;;;;;;;;
Nombre d'heures d'absences ayant pour attribut 'AUCUN MOTIF';".$non_calcule.";;;;;;;;;;;;
Nombre d'heures d'absences ayant pour attribut 'NON JUSTIFIE';".$non_calcule.";;;;;;;;;;;;
Nombre total d'heures d'absences tous motifs confondus (totalisé des absences);".$non_calcule.";;;;;;;;;;;;
Nombre d'élève par niveau scolaire dont les absences ont pour attribut 'AUCUN MOTIF' ou 'NON JUSTIFIE';;;;;;;;;;;;;
NIVEAU - MEF;MEF_2;A partir de 4 demi-journées;;;;De 4 à 10 demi-journées;;;;A partir de 11 demi-journées;;;
;;% d'éléves absents;;nombre d'élèves;;% d'éléves absents;;nombre d'élèves;;% d'éléves absents;;nombre d'élèves;
;;Aucun motif;Non justifié;Aucun motif;Non justifié;Aucun motif;Non justifié;Aucun motif;Non justifié;Aucun motif;Non justifié;Aucun motif;Non justifié\n";
	for($loop=0;$loop<count($tab_stat["mef"]);$loop++) {
		$csv.=$tab_stat["mef"][$loop]["libelle_long"].";".$tab_stat["mef"][$loop]["mef_2"].";".
		$tab_stat["mef"][$loop]["pourcentage_non_valable_nj_sup_egal_4"].";".$tab_stat["mef"][$loop]["pourcentage_nj_sup_egal_4"].";".
		$tab_stat["mef"][$loop]["non_valable_nj_sup_egal_4"].";".$tab_stat['mef'][$loop]['nj_sup_egal_4'].";".
		$tab_stat["mef"][$loop]["pourcentage_non_valable_nj_4_a_10"].";".$tab_stat["mef"][$loop]["pourcentage_nj_4_a_10"].";".
		$tab_stat["mef"][$loop]["non_valable_nj_4_a_10"].";".$tab_stat['mef'][$loop]['nj_4_a_10'].";".
		$tab_stat["mef"][$loop]["pourcentage_non_valable_nj_sup_egal_11"].";".$tab_stat["mef"][$loop]["pourcentage_nj_sup_egal_11"].";".
		$tab_stat["mef"][$loop]["non_valable_nj_sup_egal_11"].";".$tab_stat['mef'][$loop]['nj_sup_egal_11']."\n";
	}
	$csv.="TOTAL;;;;".
	$tab_stat["totaux"]["non_valable_nj_sup_egal_4"].";".$tab_stat["totaux"]['nj_sup_egal_4'].";".
	";;".
	$tab_stat["totaux"]["non_valable_nj_4_a_10"].";".$tab_stat["totaux"]['nj_4_a_10'].";".
	";;".
	$tab_stat["totaux"]["non_valable_nj_sup_egal_11"].";".$tab_stat["totaux"]['nj_sup_egal_11']."\n";
}

if($mode=="export_csv") {
/*
Mois ;sept-15;;;;;;;;;;;;
Nom établissement;LPO X;;;;;;;;;;;;
Ville;PARIS;;;;;;;;;;;;
Logiciel;ENT Paris;;;;;;;;;;;;
Numéro UAI (RNE);0750000X;;;;;;;;;;;;
Nombre total d'élèves scolarisés dans l'établissement;1438;;;;;;;;;;;;
Nombre total d'heures d'enseignement estimé dans le mois;11664.0;;;;;;;;;;;;
% d'heures d'enseignement perdu pour cause d'absences;0,01 (1806.0/1.6772832E7);;;;;;;;;;;;
Nombre total d'élèves signalés à l'inspection académique dans le mois;0;;;;;;;;;;;;
Nombre d'heures d'absences ayant pour attribut 'AUCUN MOTIF';253;;;;;;;;;;;;
Nombre d'heures d'absences ayant pour attribut 'NON JUSTIFIE';487;;;;;;;;;;;;
Nombre total d'heures d'absences tous motifs confondus (totalisé des absences);1806.0;;;;;;;;;;;;
Nombre d'élève par niveau scolaire dont les absences ont pour attribut 'AUCUN MOTIF' ou 'NON JUSTIFIE';;;;;;;;;;;;;
NIVEAU - MEF;MEF_2;A partir de 4 demi-journées;;;;De 4 à 10 demi-journées;;;;A partir de 11 demi-journées;;;
;;% d'éléves absents;;nombre d'élèves;;% d'éléves absents;;nombre d'élèves;;% d'éléves absents;;nombre d'élèves;
;;Aucun motif;Non justifié;Aucun motif;Non justifié;Aucun motif;Non justifié;Aucun motif;Non justifié;Aucun motif;Non justifié;Aucun motif;Non justifié
2DE DETERMINATION;22;0.56;0.56;1;1;0.0;0.0;0;0;0.56;0.56;1;1
CPGE1  LETTRES  1ERE ANNEE;31;2.22;2.22;1;1;2.22;2.22;1;1;0.0;0.0;0;0
CPGE2 LETTRES ENS FONTENAY-ST CLOUD;31;0.0;0.0;0;0;0.0;0.0;0;0;0.0;0.0;0;0
PREMIERE ECONOMIQUE ET SOCIALE;22;0.0;0.0;0;0;0.0;0.0;0;0;0.0;0.0;0;0
PREMIERE SCIENTIFIQUE SI;22;0.0;0.0;0;0;0.0;0.0;0;0;0.0;0.0;0;0
PREMIERE SCIENTIFIQUE SVT;22;0.0;0.0;0;0;0.0;0.0;0;0;0.0;0.0;0;0
TERMINALE ECONOMIQUE ET SOCIALE;22;2.85;5.71;1;2;2.85;5.71;1;2;0.0;0.0;0;0
TERMINALE SCIENTIFIQUE SI;22;3.7;3.7;1;1;3.7;3.7;1;1;0.0;0.0;0;0
TERMINALE SCIENTIFIQUE SVT;22;0.0;0.0;0;0;0.0;0.0;0;0;0.0;0.0;0;0
1ère année de CAP (en 2 ans) production;23;;;0;0;;;0;0;;;0;0
1ère année de CAP (en 2 ans) services;23;;;0;0;;;0;0;;;0;0
1ère professionnelle (BAC PRO en 3 ans) production;23;;;1;2;;;1;2;;;0;0
1ère professionnelle (BAC PRO en 3 ans) services;23;;;0;0;;;0;0;;;0;0
2ème année de CAP (en 2 ans) production;23;;;0;0;;;0;0;;;0;0
2ème année de CAP (en 2 ans) services;23;;;0;0;;;0;0;;;0;0
2nde professionnelle (BEP en 2 ans) production;23;;;0;2;;;0;1;;;0;1
2nde professionnelle (BEP en 2 ans) services;23;;;0;0;;;0;0;;;0;0
3ème technologique et à voie professionnelle;21;;;0;0;;;0;0;;;0;0
4ème technologique;21;;;0;0;;;0;0;;;0;0
Autres niveaux de second cycle professionnel;23;;;0;0;;;0;0;;;0;0
Terminale Diplôme de technicien;23;;;0;0;;;0;0;;;0;0
Terminale professionnelle (BAC PRO en 3 ans) production;23;;;0;0;;;0;0;;;0;0
Terminale professionnelle (BAC PRO en 3 ans) services;23;;;1;1;;;0;0;;;1;1
Terminale professionnelle (BEP en 2 ans) production;23;;;0;0;;;0;0;;;0;0
Terminale professionnelle (BEP en 2 ans) services;23;;;0;0;;;0;0;;;0;0
TOTAL;;;;6;10;;;4;7;;;2;3
*/

	$nom_fic="export_stat_absences_".$tab_stat["mois"]."_extrait_le_".strftime("%Y%m%d_%H%M%S").".csv";
	send_file_download_headers('text/x-csv',$nom_fic);
	echo echo_csv_encoded($csv);
	die();
}

$tabid_infobulle_0=$tabid_infobulle;

$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
//$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";

$javascript_specifique[] = "lib/tablekit";
//$dojo=true;
$utilisation_tablekit="ok";
//**************** EN-TETE *****************
$titre_page = "Exports statistiques";
require_once("../lib/header.inc.php");
//**************** EN-TETE *****************
include('menu_abs2.inc.php');
include('menu_bilans.inc.php');

$tabdiv_infobulle=$tabdiv_infobulle_0;
$tabid_infobulle=$tabid_infobulle_0;

?>
<div id="contain_div" class="css-panes">
     <?php if (isset($message)){
      echo'<h2 class="no">'.$message.'</h2>';
    }?>
<?php

//echo "<p style='color:red; font-weight:bold'>Cette page, réclamée peu de temps avant la sortie de la 1.6.3, est inachevée.</p>\n";

if($debug=="y") {
	debug_var();
}

$sql="SELECT 1=1 FROM a_agregation_decompte;";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$chaine_agregation="remplissage de cette table";
	if(acces("/mod_abs2/admin/admin_table_agregation.php", $_SESSION['statut'])) {
		if(($_SESSION['statut']=="cpe")&&(getSettingAOui('AccesCpeAgregationAbs2'))) {
			$chaine_agregation="<a href='$gepiPath/mod_abs2/admin/admin_table_agregation.php'>remplissage de cette table</a>";
		}
		elseif(($_SESSION['statut']=="administrateur")) {
			$chaine_agregation="<a href='$gepiPath/mod_abs2/admin/admin_table_agregation.php'>remplissage de cette table</a>";
		}
	}
	echo "
	<p style='color:red'>La table 'a_agregation_decompte' est vide.<br />
	Commencez par provoquer le $chaine_agregation.<br />
	<strong>Attention&nbsp;:</strong> L'opération est longue et gourmande en ressources.<br />
	Il est préférable de lancer l'opération en dehors des heures de cours.</p>";
}

// Choix du mois
if(!isset($mois)) {
	$mois=strftime("%m")-1;
	if($mois==0) {$mois=12;}
	echo "<form id='choix_mois' name='choix_mois' action='".$_SERVER['PHP_SELF']."' method='post'>
	<p>Pour quel mois souhaitez-vous extraire les statistiques&nbsp;?<br />\n";
	for($loop=1;$loop<=12;$loop++) {
		echo "<input type='radio' name='mois' id='mois_$loop' value='$loop' ";
		if($loop==$mois) {echo "checked ";}
		echo "/><label for='mois_$loop'> ".strftime("%B", strtotime($loop."/01/2000"))."</label><br />\n";
	}

	if(date("n")<9) {
		$annee_courante=(date("Y")-1)."-".date("Y");
	}
	else {
		$annee_courante=date("Y")."-".(date("Y")+1);
	}

	echo "	<p>Année scolaire&nbsp;: <input type='text' name='annee' id='annee' value='".$annee_courante."' size='7' /></p>
	<p><input type='submit' value='Valider' /></p>
</form>

<p><br /></p>
<ul style='list-style-type:circle;display: list-item;'>
	<li style='color:red;'>Proposer plutôt de choisir le mois en choisissant un jour dans un tableau JS.</li>
	<li>L'extraction des nombres d'élèves dépassant tant de demi-journée,... nécessite le remplissage de la table d'agrégation.</li>
	<li>Entre la version 1.6.2 et la version 1.6.3 de Gepi, le type du champ MEF_CODE a changé.<br />
	Cela peut impliquer de re-remplir les MEF et de faire une mise à jour d'après Sconet pour importer les MEF associés aux élèves.<br />
	Sans cela, il se peut que vos totaux apparaissent à zéro.</li>
	<li>Dans l'export demandé par le Ministère, une 'absence non justifiée' est une absence sans motif ou une absence avec un motif non valable <em>(qu'elle ait été justifiée ou non par les parents)</em>.</li>
</ul>
</div>";
	require_once("../lib/footer.inc.php");
	die();
}

echo "<h2>Extraction statistique</h2>
<p>";
if(($mois==1)) {
	echo "<a href='".$_SERVER['PHP_SELF']."?annee=".$annee."&amp;mois=12' title='Mois précédent'><img src='../images/icons/back.png' width='16' height='16' /></a> | ";
}
else {
	echo "<a href='".$_SERVER['PHP_SELF']."?annee=".$annee."&amp;mois=".($mois-1)."' title='Mois précédent'><img src='../images/icons/back.png' width='16' height='16' /></a> | ";
}
echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir un autre mois</a>";
if(($mois==12)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."?annee=".$annee."&amp;mois=1' title='Mois suivant'><img src='../images/icons/forward.png' width='16' height='16' /></a>";
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."?annee=".$annee."&amp;mois=".($mois+1)."' title='Mois suivant'><img src='../images/icons/forward.png' width='16' height='16' /></a>";
}
echo "</p>

<p class='bold'>Tableau \$tab_stat extrait pour le mois de ".$tab_stat['mois']."&nbsp;: <a href='".$_SERVER['PHP_SELF']."?mode=export_csv&mois=".$mois."&annee=".$annee."' target='_blank'><img src='../images/icons/csv.png' class='icone16' alt='CSV' /></a></p>";

if($debug=="y") {
	echo "
<pre>";
print_r($tab_stat);
echo "</pre>";
}

echo "<table class='boireaus boireaus_alt sortable resizable' border='1'>
	<thead>
		<tr>
			<th colspan='3'>MEF</th>
			<th colspan='6' title=\"Au moins 4 absences non justifiées au sens non justifiable (sans motif ou motif non valable).\nIndépendamment du fait que la famille ait choisi de couvrir/justifier l'absence.\">4 absences non justifiées ou plus</th>
			<th colspan='6' title=\"De 4 à 10 absences non justifiées au sens non justifiable (sans motif ou motif non valable).\nIndépendamment du fait que la famille ait choisi de couvrir/justifier l'absence.\">De 4 à 10 absences non justifiées</th>
			<th colspan='6' title=\"Au moins 11 absences non justifiées au sens non justifiable (sans motif ou motif non valable).\nIndépendamment du fait que la famille ait choisi de couvrir/justifier l'absence.\">11 absences non justifiées ou plus</th>
		</tr>
		<tr>
			<th class='text'>Libellé court</th>
			<th class='text'>Libellé long</th>
			<th class='text'>Libellé édition</th>

			<th colspan='2' class='number' title=\"Au moins 4 absences non justifiées au sens non justifiable (sans motif ou motif non valable).\nIndépendamment du fait que la famille ait choisi de couvrir/justifier l'absence.\">
				<!--nj_sup_egal_4-->
				Effectif/%
			</th>
			<th colspan='2' class='number'>
				<!--non_valable_nj_sup_egal_4-->
				Motif non valable
			</th>
			<th colspan='2' class='number'>
				<!--aucun_motif_nj_sup_egal_4_nj_sup_egal_4-->
				Aucun motif
			</th>

			<th colspan='2' class='number' title=\"De 4 à 10 absences non justifiées au sens non justifiable (sans motif ou motif non valable).\nIndépendamment du fait que la famille ait choisi de couvrir/justifier l'absence.\">
				nj_4_a_10
			</th>
			<th colspan='2' class='number'>
				<!--non_valable_nj_4_a_10-->
				Motif non valable
			</th>
			<th colspan='2' class='number'>
				<!--aucun_motif_nj_4_a_10-->
				Aucun motif
			</th>

			<th colspan='2' class='number' title=\"Au moins 11 absences non justifiées au sens non justifiable (sans motif ou motif non valable).\nIndépendamment du fait que la famille ait choisi de couvrir/justifier l'absence.\">
				<!--nj_sup_egal_11-->
				Effectif/%
			</th>
			<th colspan='2' class='number'>
				<!--non_valable_nj_sup_egal_11-->
				Motif non valable
			</th>
			<th colspan='2' class='number'>
				<!--aucun_motif_nj_sup_egal_11-->
				Aucun motif
			</th>
		</tr>
	<thead>
	<tbody>";
for($loop=0;$loop<count($tab_stat['mef']);$loop++) {
	echo "
		<tr>
			<td>".$tab_stat['mef'][$loop]['libelle_court']."</td>
			<td>".$tab_stat['mef'][$loop]['libelle_long']."</td>
			<td>".$tab_stat['mef'][$loop]['libelle_edition']."</td>";

	//===================================
	// Non justifiées
	if($tab_stat['mef'][$loop]['nj_sup_egal_4']>0) {
		echo "
			<td style='font-weight:bold' title=\"Pour ce MEF, les élèves suivants ont au moins 4 absences sans motif ou avec motif non valable\n(ce qui est qualifié de 'non justifié' dans cet export):\n".$tab_stat['mef'][$loop]['liste_nj_sup_egal_4']."\" onclick=\"afficher_div('infobulle_mef_".$loop."_nj_sup_egal_4', 'y', 10, 10); return false;\">
				<a href='#' onclick=\"afficher_div('infobulle_mef_".$loop."_nj_sup_egal_4', 'y', 10, 10); return false;\">".$tab_stat['mef'][$loop]['nj_sup_egal_4']."</a>
			</td>
			<td title=\"Pour ce MEF, le pourcentage d'élèves (par rapport à l'effectif du MEF) ayant au moins 4 absences sans motif ou avec motif non valable\n(ce qui est qualifié de 'non justifié' dans cet export) est:\n".$tab_stat['mef'][$loop]['pourcentage_nj_sup_egal_4']." soit ".($tab_stat['mef'][$loop]['pourcentage_nj_sup_egal_4']*100)."%\">
				".$tab_stat['mef'][$loop]['pourcentage_nj_sup_egal_4']."
			</td>";
	}
	else {
		echo "
			<td style='font-weight:bold'>0</td>
			<td></td>";
	}
	// Motif non valable
	if($tab_stat['mef'][$loop]['non_valable_nj_sup_egal_4']>0) {
		echo "
			<td title=\"Pour ce MEF, parmi les élèves qui ont au moins 4 absences 'non justifiées', les suivants ont fourni un motif non valable:\n".$tab_stat['mef'][$loop]['liste_non_valable_nj_sup_egal_4']."\" onclick=\"afficher_div('infobulle_mef_".$loop."_non_valable_nj_sup_egal_4', 'y', 10, 10); return false;\">
				<a href='#' onclick=\"afficher_div('infobulle_mef_".$loop."_non_valable_nj_sup_egal_4', 'y', 10, 10); return false;\">".$tab_stat['mef'][$loop]['non_valable_nj_sup_egal_4']."</a>
			</td>
			<td title=\"Pour ce MEF, parmi les élèves qui ont au moins 4 absences 'non justifiées', le pourcentage d'élèves (par rapport à l'effectif du MEF) ayant présenté un motif non valable est:\n".$tab_stat['mef'][$loop]['pourcentage_non_valable_nj_sup_egal_4']." soit ".($tab_stat['mef'][$loop]['pourcentage_non_valable_nj_sup_egal_4']*100)."%\">
				".$tab_stat['mef'][$loop]['pourcentage_non_valable_nj_sup_egal_4']."

			</td>";
	}
	else {
		echo "
			<td>0</td>
			<td></td>";
	}
	// Aucun motif
	if($tab_stat['mef'][$loop]['aucun_motif_nj_sup_egal_4']>0) {
		echo "
			<td title=\"Pour ce MEF, parmi les élèves qui ont au moins 4 absences 'non justifiées', les suivants n'ont pas fourni de motif:\n".$tab_stat['mef'][$loop]['liste_aucun_motif_nj_sup_egal_4']."\" onclick=\"afficher_div('infobulle_mef_".$loop."_aucun_motif_nj_sup_egal_4', 'y', 10, 10); return false;\">
				<a href='#' onclick=\"afficher_div('infobulle_mef_".$loop."_aucun_motif_nj_sup_egal_4', 'y', 10, 10); return false;\">".$tab_stat['mef'][$loop]['aucun_motif_nj_sup_egal_4']."</a>
			</td>
			<td title=\"Pour ce MEF, parmi les élèves qui ont au moins 4 absences 'non justifiées', le pourcentage d'élèves (par rapport à l'effectif du MEF) n'ayant pas fourni de motif est:\n".$tab_stat['mef'][$loop]['pourcentage_aucun_motif_nj_sup_egal_4']." soit ".($tab_stat['mef'][$loop]['pourcentage_aucun_motif_nj_sup_egal_4']*100)."%\">
				".$tab_stat['mef'][$loop]['pourcentage_aucun_motif_nj_sup_egal_4']."

			</td>";
	}
	else {
		echo "
			<td>0</td>
			<td></td>";
	}
	//===================================
	// 4 à 10
	if($tab_stat['mef'][$loop]['nj_4_a_10']>0) {
		echo "
			<td style='font-weight:bold' title=\"Pour ce MEF, les élèves suivants ont de 4 à 10 absences sans motif ou avec motif non valable\n(ce qui est qualifié de 'non justifié' dans cet export):\n".$tab_stat['mef'][$loop]['liste_nj_4_a_10']."\" onclick=\"afficher_div('infobulle_mef_".$loop."_nj_4_a_10', 'y', 10, 10); return false;\">
				<a href='#' onclick=\"afficher_div('infobulle_mef_".$loop."_nj_4_a_10', 'y', 10, 10); return false;\">".$tab_stat['mef'][$loop]['nj_4_a_10']."</a>
			</td>
			<td title=\"Pour ce MEF, le pourcentage d'élèves (par rapport à l'effectif du MEF) ayant 4 à 10 absences sans motif ou avec motif non valable\n(ce qui est qualifié de 'non justifié' dans cet export) est:\n".$tab_stat['mef'][$loop]['pourcentage_nj_4_a_10']." soit ".($tab_stat['mef'][$loop]['pourcentage_nj_4_a_10']*100)."%\">
				".$tab_stat['mef'][$loop]['pourcentage_nj_4_a_10']."

			</td>";
	}
	else {
		echo "
			<td style='font-weight:bold'>0</td>
			<td></td>";
	}
	if($tab_stat['mef'][$loop]['non_valable_nj_4_a_10']>0) {
		echo "
			<td title=\"Pour ce MEF, parmi les élèves qui ont de 4 à 10 absences 'non justifiées', les suivants ont fourni un motif non valable:".$tab_stat['mef'][$loop]['liste_non_valable_nj_4_a_10']."\" onclick=\"afficher_div('infobulle_mef_".$loop."_non_valable_nj_4_a_10', 'y', 10, 10); return false;\">
				<a href='#' onclick=\"afficher_div('infobulle_mef_".$loop."_non_valable_nj_4_a_10', 'y', 10, 10); return false;\">".$tab_stat['mef'][$loop]['non_valable_nj_4_a_10']."</a>
			</td>
			<td title=\"Pour ce MEF, parmi les élèves qui ont de 4 à 10 absences 'non justifiées', le pourcentage d'élèves (par rapport à l'effectif du MEF) ayant fourni un motif non valable est:\n".$tab_stat['mef'][$loop]['pourcentage_non_valable_nj_4_a_10']." soit ".($tab_stat['mef'][$loop]['pourcentage_non_valable_nj_4_a_10']*100)."%\">
				".$tab_stat['mef'][$loop]['pourcentage_non_valable_nj_4_a_10']."

			</td>";
	}
	else {
		echo "
			<td>0</td>
			<td></td>";
	}
	if($tab_stat['mef'][$loop]['aucun_motif_nj_4_a_10']>0) {
		echo "
			<td title=\"Pour ce MEF, parmi les élèves qui ont de 4 à 10 absences 'non justifiées', les suivants n'ont pas fourni de motif:\n".$tab_stat['mef'][$loop]['liste_aucun_motif_nj_4_a_10']."\" onclick=\"afficher_div('infobulle_mef_".$loop."_aucun_motif_nj_4_a_10', 'y', 10, 10); return false;\">
				<a href='#' onclick=\"afficher_div('infobulle_mef_".$loop."_aucun_motif_nj_4_a_10', 'y', 10, 10); return false;\">".$tab_stat['mef'][$loop]['aucun_motif_nj_4_a_10']."</a>
			</td>
			<td title=\"Pour ce MEF, parmi les élèves qui ont de 4 à 10 absences 'non justifiées', le pourcentage d'élèves (par rapport à l'effectif du MEF) n'ayant présenté aucun motif est:\n".$tab_stat['mef'][$loop]['pourcentage_aucun_motif_nj_4_a_10']." soit ".($tab_stat['mef'][$loop]['pourcentage_aucun_motif_nj_4_a_10']*100)."%\">
				".$tab_stat['mef'][$loop]['pourcentage_aucun_motif_nj_4_a_10']."

			</td>";
	}
	else {
		echo "
			<td>0</td>
			<td></td>";
	}
	//===================================
	// 11 ou plus
	if($tab_stat['mef'][$loop]['nj_sup_egal_11']>0) {
		echo "
			<td style='font-weight:bold' title=\"Pour ce MEF, les élèves suivants ont au moins 11 absences sans motif ou avec motif non valable\n(ce qui est qualifié de 'non justifié' dans cet export):\n".$tab_stat['mef'][$loop]['liste_nj_sup_egal_11']."\" onclick=\"afficher_div('infobulle_mef_".$loop."_nj_sup_egal_11', 'y', 10, 10); return false;\">
				<a href='#' onclick=\"afficher_div('infobulle_mef_".$loop."_nj_sup_egal_11', 'y', 10, 10); return false;\">".$tab_stat['mef'][$loop]['nj_sup_egal_11']."</a>
			<td title=\"Pour ce MEF, le pourcentage d'élèves (par rapport à l'effectif du MEF) ayant au moins 11 absences sans motif ou avec motif non valable\n(ce qui est qualifié de 'non justifié' dans cet export) est:\n".$tab_stat['mef'][$loop]['pourcentage_nj_sup_egal_11']." soit ".($tab_stat['mef'][$loop]['pourcentage_nj_sup_egal_11']*100)."%\">
				".$tab_stat['mef'][$loop]['pourcentage_nj_sup_egal_11']."
			</td>";
	}
	else {
		echo "
			<td style='font-weight:bold'>0</td>
			<td></td>";
	}
	if($tab_stat['mef'][$loop]['non_valable_nj_sup_egal_11']>0) {
		echo "
			<td title=\"Pour ce MEF, parmi les élèves qui ont au moins 11 absences 'non justifiées', les suivants ont fourni un motif non valable:\n".$tab_stat['mef'][$loop]['liste_non_valable_nj_sup_egal_11']."\" onclick=\"afficher_div('infobulle_mef_".$loop."_non_valable_nj_sup_egal_11', 'y', 10, 10); return false;\">
				<a href='#' onclick=\"afficher_div('infobulle_mef_".$loop."_non_valable_nj_sup_egal_11', 'y', 10, 10); return false;\">".$tab_stat['mef'][$loop]['non_valable_nj_sup_egal_11']."</a>
			</td>
			<td title=\"Pour ce MEF, parmi les élèves qui ont au moins 11 absences 'non justifiées', le pourcentage d'élèves (par rapport à l'effectif du MEF) ayant présenté un motif non valable est:\n".$tab_stat['mef'][$loop]['pourcentage_non_valable_nj_sup_egal_11']." soit ".($tab_stat['mef'][$loop]['pourcentage_non_valable_nj_sup_egal_11']*100)."%\">
				".$tab_stat['mef'][$loop]['pourcentage_non_valable_nj_sup_egal_11']."

			</td>";
	}
	else {
		echo "
			<td>0</td>
			<td></td>";
	}
	if($tab_stat['mef'][$loop]['aucun_motif_nj_sup_egal_11']>0) {
		echo "
			<td title=\"Pour ce MEF, parmi les élèves qui ont au moins 11 absences 'non justifiées', les suivants n'ont pas fourni de motif:\n".$tab_stat['mef'][$loop]['liste_aucun_motif_nj_sup_egal_11']."\" onclick=\"afficher_div('infobulle_mef_".$loop."_aucun_motif_nj_sup_egal_11', 'y', 10, 10); return false;\">
				<a href='#' onclick=\"afficher_div('infobulle_mef_".$loop."_aucun_motif_nj_sup_egal_11', 'y', 10, 10); return false;\">".$tab_stat['mef'][$loop]['aucun_motif_nj_sup_egal_11']."</a>
			</td>
			<td title=\"Pour ce MEF, parmi les élèves qui ont au moins 11 absences 'non justifiées', le pourcentage d'élèves (par rapport à l'effectif du MEF) n'ayant pas fourni de motif est:\n".$tab_stat['mef'][$loop]['pourcentage_aucun_motif_nj_sup_egal_11']." soit ".($tab_stat['mef'][$loop]['pourcentage_aucun_motif_nj_sup_egal_11']*100)."%\">
				".$tab_stat['mef'][$loop]['pourcentage_aucun_motif_nj_sup_egal_11']."

			</td>";
	}
	else {
		echo "
			<td>0</td>
			<td></td>";
	}
	//===================================
	echo "
		</tr>";
}
echo "
	</tbody>
	</tfoot>
		<tr>
			<td>TOTAL</td>
			<td></td>
			<td></td>
			<td>".$tab_stat['totaux']['nj_sup_egal_4']."</td>
			<td></td>
			<td>".$tab_stat['totaux']['non_valable_nj_sup_egal_4']."</td>
			<td></td>
			<td>".$tab_stat['totaux']['aucun_motif_nj_sup_egal_4']."</td>
			<td></td>

			<td>".$tab_stat['totaux']['nj_4_a_10']."</td>
			<td></td>
			<td>".$tab_stat['totaux']['non_valable_nj_4_a_10']."</td>
			<td></td>
			<td>".$tab_stat['totaux']['aucun_motif_nj_4_a_10']."</td>
			<td></td>

			<td>".$tab_stat['totaux']['nj_sup_egal_11']."</td>
			<td></td>
			<td>".$tab_stat['totaux']['non_valable_nj_sup_egal_11']."</td>
			<td></td>
			<td>".$tab_stat['totaux']['aucun_motif_nj_sup_egal_11']."</td>
			<td></td>
		</tr>
	</tfoot>
</table>";

//echo "<pre>$csv</pre>";

//echo count($tabdiv_infobulle);
//echo count($tabid_infobulle);
echo "</div>";

require_once("../lib/footer.inc.php");
?>
