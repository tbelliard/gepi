<?php
/*
* Copyright 2001, 2020 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

//$variables_non_protegees = 'yes';

// Initialisations files
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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/genere_convocation.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/genere_convocation.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: Génération convocation',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$id_epreuve=isset($_POST['id_epreuve']) ? $_POST['id_epreuve'] : (isset($_GET['id_epreuve']) ? $_GET['id_epreuve'] : NULL);
$imprime=isset($_POST['imprime']) ? $_POST['imprime'] : (isset($_GET['imprime']) ? $_GET['imprime'] : NULL);
//$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

include('lib_eb.php');

if(isset($imprime)) {
	check_token();

	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		$msg="L'épreuve n°$id_epreuve n'existe pas.";
	}
	else {
		$lig_ep=mysqli_fetch_object($res);
		$intitule_epreuve=$lig_ep->intitule;
		$description_epreuve=$lig_ep->description;

		$mysql_date_epreuve=$lig_ep->date;
		$date_epreuve=formate_date("$lig_ep->date");
	
		$sql="SELECT * FROM eb_salles WHERE id_epreuve='$id_epreuve' ORDER BY salle;";
		$res_salle=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig_salle=mysqli_fetch_object($res_salle)) {
			$salle[$lig_salle->id]=$lig_salle->salle;
		}

		$tab_lignes_OOo_eleve=array();

		// Tri par classe, puis par élève

		$sql="SELECT DISTINCT e.nom, 
				e.prenom, 
				e.login, 
				e.naissance, 
				c.classe, 
				ec.id_salle,
				ec.n_anonymat 
			FROM eb_copies ec, 
				j_eleves_classes jec, 
				classes c,
				eleves e 
			WHERE e.login=ec.login_ele AND 
				e.login=jec.login AND 
				jec.id_classe=c.id AND 
				ec.id_epreuve='$id_epreuve' 
			ORDER BY c.classe, e.nom,e.prenom;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$cpt=0;
			//$rang=1;
			while($lig=mysqli_fetch_object($res)) {
				$tab_lignes_OOo_eleve[$cpt]=array();

				$tab_lignes_OOo_eleve[$cpt]['nom']=casse_mot($lig->nom);
				$tab_lignes_OOo_eleve[$cpt]['prenom']=casse_mot($lig->prenom, 'majf2');
				$tab_lignes_OOo_eleve[$cpt]['naissance']=formate_date($lig->naissance);

				$tab_lignes_OOo_eleve[$cpt]['classe']=$lig->classe;
				$tab_lignes_OOo_eleve[$cpt]['n_anonymat']=$lig->n_anonymat;
				if(($lig->id_salle!='')&&(isset($salle[$lig->id_salle]))) {
					$tab_lignes_OOo_eleve[$cpt]['salle']=$salle[$lig->id_salle];
				}
				else {
					$tab_lignes_OOo_eleve[$cpt]['salle']='';
				}
				//$tab_lignes_OOo_eleve[$cpt]['rang']=$rang;

				// Epreuve
				$tab_lignes_OOo_eleve[$cpt]['ep_intitule']=$intitule_epreuve;
				$tab_lignes_OOo_eleve[$cpt]['ep_description']=$description_epreuve;
				$tab_lignes_OOo_eleve[$cpt]['ep_date']=$date_epreuve;

				// Chef d'établissement (ou gestionnaire de Gepi?)
				$tab_lignes_OOo_eleve[$cpt]['fonction_chef']=getSettingValue("gepiAdminFonction");
				$tab_lignes_OOo_eleve[$cpt]['nom_chef']=getSettingValue("gepiAdminNom");
				$tab_lignes_OOo_eleve[$cpt]['prenom_chef']=getSettingValue("gepiAdminPrenom");

				// Infos établissement
				$tab_lignes_OOo_eleve[$cpt]['etab']=getSettingValue("gepiSchoolName");

				$tab_lignes_OOo_eleve[$cpt]['adresse']=getSettingValue("gepiSchoolAdress1");
				if(getSettingValue("gepiSchoolAdress2")!='') {
					if($tab_lignes_OOo_eleve[$cpt]['adresse']!='') {
						$tab_lignes_OOo_eleve[$cpt]['adresse'].=' '.getSettingValue("gepiSchoolAdress2");
					}
					else {
						$tab_lignes_OOo_eleve[$cpt]['adresse']=getSettingValue("gepiSchoolAdress2");
					}
				}

				$tab_lignes_OOo_eleve[$cpt]['acad']=getSettingValue("gepiSchoolAcademie");

				$tab_lignes_OOo_eleve[$cpt]['adr1']=getSettingValue("gepiSchoolAdress1")." ".getSettingValue("gepiSchoolAdress2");

				$tab_lignes_OOo_eleve[$cpt]['cp']=getSettingValue("gepiSchoolZipCode");
				$tab_lignes_OOo_eleve[$cpt]['ville']=getSettingValue("gepiSchoolCity");
				$tab_lignes_OOo_eleve[$cpt]['annee_scolaire']=getSettingValue("gepiYear");

				$cpt++;
				//$rang++;
			}
		}

		/*
		echo "<pre>";
		print_r($tab_lignes_OOo_eleve);
		echo "</pre>";
		*/

		$mode_ooo="imprime";
		
		include_once('../tbs/tbs_class.php');
		include_once('../tbs/plugins/tbs_plugin_opentbs.php');
		
		// Création d'une classe  TBS OOo class

		$OOo = new clsTinyButStrong;
		$OOo->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
		

		$fichier_a_utiliser="mod_epreuve_blanche_convocation.odt";

		//$tableau_a_utiliser=$tab_lignes_OOo;
		$tableau_a_utiliser=$tab_lignes_OOo_eleve;
		// Nom à utiliser dans le fichier ODT:
		$nom_a_utiliser="eleve";
		/*
			[eleve;block=begin]

			[eleve.ep_intitule]
			[eleve.ep_description]
			[eleve.ep_date]

			[eleve.nom]
			[eleve.prenom]
			[eleve.naissance]
			[eleve.classe]
			[eleve.n_anonymat]

			[eleve.salle]

			[eleve.etab]
			[eleve.adresse]
			[eleve.cp]
			[eleve.ville]
			[eleve.acad]
			[eleve.annee_scolaire]

			[eleve;block=end]


			$tab_lignes_OOo_eleve[$cpt]['etab']
			...
			$tab_lignes_OOo_eleve[$cpt]['ep_intitule']
			$tab_lignes_OOo_eleve[$cpt]['ep_description']
			$tab_lignes_OOo_eleve[$cpt]['ep_date']
			$tab_lignes_OOo_eleve[$cpt]['salle']

			$tab_lignes_OOo_eleve[$cpt]['nom']
			$tab_lignes_OOo_eleve[$cpt]['prenom']
			$tab_lignes_OOo_eleve[$cpt]['naissance']
			...
		*/


		$prefixe_generation_hors_dossier_mod_ooo="../mod_ooo/";
		include_once('../mod_ooo/lib/lib_mod_ooo.php'); // les fonctions
		$nom_fichier_modele_ooo = $fichier_a_utiliser;
		include_once('../mod_ooo/lib/chemin.inc.php'); // le chemin des dossiers contenant les  modèles

		$OOo->LoadTemplate($nom_dossier_modele_a_utiliser."/".$nom_fichier_modele_ooo, OPENTBS_ALREADY_UTF8);

		// $OOo->MergeBlock('eleves',$tab_eleves_OOo);
		$OOo->MergeBlock($nom_a_utiliser, $tableau_a_utiliser);
		
		$nom_fic = $fichier_a_utiliser;
		
		$OOo->Show(OPENTBS_DOWNLOAD, $nom_fic);
		
		$OOo->remove(); //suppression des fichiers de travail
		
		$OOo->close();

		die();

	}
}


//**************** EN-TETE *****************
$titre_page = "Epreuve blanche: Emargement";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

//echo "<div class='noprint'>\n";
//echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
echo "<p class='bold'><a href='index.php?id_epreuve=$id_epreuve&amp;mode=modif_epreuve'>Retour</a>";
//echo "</p>\n";
//echo "</div>\n";

if(!isset($imprime)) {
	echo "</p>\n";

	// Générer des fiches par salles

	echo "<p class='bold'>Epreuve n°$id_epreuve</p>\n";
	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>L'épreuve choisie (<i>$id_epreuve</i>) n'existe pas.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	
	$lig=mysqli_fetch_object($res);
	echo "<blockquote>\n";
	echo "<p><b>".$lig->intitule."</b> (<i>".formate_date($lig->date)."</i>)<br />\n";
	if($lig->description!='') {
		echo nl2br(trim($lig->description))."<br />\n";
	}
	else {
		echo "Pas de description saisie.<br />\n";
	}
	echo "</blockquote>\n";

	//========================================================
	$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve';";
	$test1=mysqli_query($GLOBALS["mysqli"], $sql);
	
	$sql="SELECT DISTINCT n_anonymat FROM eb_copies WHERE id_epreuve='$id_epreuve';";
	$test2=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test1)!=mysqli_num_rows($test2)) {
		echo "<p style='color:red;'>Les numéros anonymats ne sont pas uniques sur l'épreuve (<i>cela ne devrait pas arriver</i>).</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT login_ele FROM eb_copies WHERE n_anonymat='' AND id_epreuve='$id_epreuve';";
	$test3=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test3)>0) {
		echo "<p style='color:red;'>Un ou des numéros anonymats ne sont pas valides sur l'épreuve&nbsp;: ";
		$cpt=0;
		while($lig=mysqli_fetch_object($test3)) {
			if($cpt>0) {echo ", ";}
			echo get_nom_prenom_eleve($lig->login_ele);
			$cpt++;
		}
		echo "<br />Cela ne devrait pas arriver.<br />La saisie n'est pas possible.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	//========================================================

	//========================================================
	//echo "<p style='color:red;'>A FAIRE&nbsp;: Contrôler si certains élèves n'ont pas été affectés dans des salles.</p>\n";
	$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND id_salle='-1';";
	//echo "$sql<br />";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_tmp=mysqli_num_rows($test);
	if($nb_tmp==1) {
		echo "<p style='color:red;'>$nb_tmp élève n'est pas affecté dans une salle.</p>\n";
	}
	elseif($nb_tmp>1) {
		echo "<p style='color:red;'>$nb_tmp élèves n'ont pas été affectés dans des salles.</p>\n";
	}
	//========================================================

	echo "<div style='padding:0.5em; width:20em; text-align:center;' class='fieldset_opacite50'>
		<p><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;mode=odt&amp;imprime=odt".add_token_in_url()."' target='_blank'>Imprimer les convocations au format LibreOffice/OpenOffice.org</a></p>
	</div>
	
	<p style='margin-top:2em;'><em>NOTES&nbsp;:</em></p>
	<ul>
		<li>
			<p>Il est possible de personnaliser le modèle dans <strong>Modèles OpenOffice/Gérer les modèles de document OOo de l'établissement</strong>.<br />";

	$prefixe_generation_hors_dossier_mod_ooo="../mod_ooo/";
	include_once('../mod_ooo/lib/lib_mod_ooo.php'); //les fonctions
	$nom_fichier_modele_ooo ='mod_epreuve_blanche_convocation.odt'; // Modèle
	include_once('../mod_ooo/lib/chemin.inc.php'); // le chemin des dossiers contenant les  modèles

	echo "Le modèle utilisé est actuellement <a href='".$nom_dossier_modele_a_utiliser."/".$nom_fichier_modele_ooo."' target='_blank' class='bold'>celui-ci</a>";

	if(acces('/mod_ooo/gerer_modeles_ooo.php', $_SESSION['statut'])) {
		echo ".<br />
			<a href='../mod_ooo/gerer_modeles_ooo.php#MODULE_Epreuves_blanches'>Mettre en place un modèle de Convocation modifié, ou revenir au modèle par défaut</a>.";
	}
	else {
		echo ".<br />Ce modèle peut être mis en place avec un compte <strong>administrateur</strong>.";
	}

	$acces_gestion_param_gen=acces('/gestion/param_gen.php', $_SESSION['statut']);
	echo "
			</p>
			<br />
		</li>
		<li>
			<p>Les champs disponibles sont&nbsp;: </p>
			<ul>
				<li>
					<p>Les champs du document ODT qui sont remplacés par des données de la base Gepi sont les suivants&nbsp;:</p>
					<table class='boireaus boireaus_alt'>
						<thead>
							<tr>
								<th>Champ</th>
								<th>Signification</th>
							</tr>
						</thead>
						<tbody>
							<tr><td class='bold'>[eleve.ep_intitule]</td><td>Intitulé de l'épreuve</td></tr>
							<tr><td class='bold'>[eleve.ep_description]</td><td>Description de l'épreuve</td></tr>
							<tr><td class='bold'>[eleve.ep_date]</td><td>Date de l'épreuve</td></tr>
							<tr><td class='bold'>[eleve.nom]</td><td>Nom de l'élève</td></tr>
							<tr><td class='bold'>[eleve.prenom]</td><td>Prénom de l'élève</td></tr>
							<tr><td class='bold'>[eleve.naissance]</td><td>Date de naissance de l'élève</td></tr>
							<tr><td class='bold'>[eleve.classe]</td><td>Classe de l'élève</td></tr>
							<tr><td class='bold'>[eleve.n_anonymat]</td><td>Numéro anonymat de l'élève dans l'épreuve</td></tr>
							<tr><td class='bold'>[eleve.salle]</td><td>Salle de l'élève lors de l'épreuve</td></tr>
							<tr><td class='bold'>[eleve.etab]</td><td>Nom de l'établissement tel que défini en administrateur dans ".($acces_gestion_param_gen ? "<a href='../gestion/param_gen.php' target='_blank'>Gestion générale/Configuration générale</a>" : "<strong>Gestion générale/Configuration générale</strong>")."</td></tr>
							<tr><td class='bold'>[eleve.adresse]</td><td>Adresse de l'établissement telle que définie en administrateur dans ".($acces_gestion_param_gen ? "<a href='../gestion/param_gen.php' target='_blank'>Gestion générale/Configuration générale</a>" : "<strong>Gestion générale/Configuration générale</strong>")."</td></tr>
							<tr><td class='bold'>[eleve.cp]</td><td>Code postal de l'établissement tel que défini en administrateur dans ".($acces_gestion_param_gen ? "<a href='../gestion/param_gen.php' target='_blank'>Gestion générale/Configuration générale</a>" : "<strong>Gestion générale/Configuration générale</strong>")."</td></tr>
							<tr><td class='bold'>[eleve.ville]</td><td>Ville de l'établissement telle que définie en administrateur dans ".($acces_gestion_param_gen ? "<a href='../gestion/param_gen.php' target='_blank'>Gestion générale/Configuration générale</a>" : "<strong>Gestion générale/Configuration générale</strong>")."</td></tr>
							<tr><td class='bold'>[eleve.acad]</td><td>Académie de l'établissement telle que définie en administrateur dans ".($acces_gestion_param_gen ? "<a href='../gestion/param_gen.php' target='_blank'>Gestion générale/Configuration générale</a>" : "<strong>Gestion générale/Configuration générale</strong>")."</td></tr>
							<tr><td class='bold'>[eleve.annee_scolaire]</td><td>Année scolaire tel que définie en administrateur dans ".($acces_gestion_param_gen ? "<a href='../gestion/param_gen.php' target='_blank'>Gestion générale/Configuration générale</a>" : "<strong>Gestion générale/Configuration générale</strong>")."</td></tr>
							<tr><td class='bold'>[eleve.fonction_chef]</td><td>Fonction de l'administrateur du site tel que défini en administrateur dans ".($acces_gestion_param_gen ? "<a href='../gestion/param_gen.php' target='_blank'>Gestion générale/Configuration générale</a>" : "<strong>Gestion générale/Configuration générale</strong>")."</td></tr>
							<tr><td class='bold'>[eleve.nom_chef]</td><td>Nom de l'administrateur du site tel que défini en administrateur dans ".($acces_gestion_param_gen ? "<a href='../gestion/param_gen.php' target='_blank'>Gestion générale/Configuration générale</a>" : "<strong>Gestion générale/Configuration générale</strong>")."</td></tr>
							<tr><td class='bold'>[eleve.prenom_chef]</td><td>Prénom de l'administrateur du site tel que défini en administrateur dans ".($acces_gestion_param_gen ? "<a href='../gestion/param_gen.php' target='_blank'>Gestion générale/Configuration générale</a>" : "<strong>Gestion générale/Configuration générale</strong>")."</td></tr>
						</tbody>
					</table>
					<br />
				</li>
				<li>
					<p>La personnalisation d'un modèle présente quelques difficultés au niveau des champs mentionnés ci-dessus.<br />
					Quelques explications à ce sujet avec la <a href='http://www.sylogix.org/projects/gepi/wiki/Mod_discipline_OOo_avertissements' target='_blank'>personnalisation d'un autre modèle</a>, dans le cadre du module Discipline.</p>
				</li>
			</ul>
		</li>
	</ul>\n";

}

require("../lib/footer.inc.php");
?>
