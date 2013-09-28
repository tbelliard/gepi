<?php
/*
* $Id$
*
* Copyright 2001-2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
require_once("../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

$reg_ok = 'yes';
$msg = '';
if (isset($_POST['option_modele_bulletin'])) {
	check_token();
	// Sauvegarde des paramétrages par défaut des choix de modèles pour les classes
	if (!saveSetting("option_modele_bulletin", $_POST['option_modele_bulletin'])) {
		$msg .= "Erreur lors de l'enregistrement de option_modele_bulletin !";
		$reg_ok = 'no';
	}
}

/*
	// Pour ajouter un paramètre, il faut ajouter la case à cocher dans la présente page (param_bull_pdf.php), mais il faut aussi déclarer le champ correspondant et sa valeur par défaut dans la page bulletin_pdf.inc.php

	if (empty($_GET['telle_var']) and empty($_POST['telle_var'])) {
		$telle_var = '';
	}
	else {
		if (isset($_GET['telle_var'])) { 
			$telle_var = $_GET['telle_var']; 
		}
		if (isset($_POST['telle_var'])) {
			$telle_var = $_POST['telle_var'];
		}
	}

	// A VOIR: Remplacer par:
	$telle_var=isset($_POST['telle_var']) ? $_POST['telle_var'] : (isset($_GET['telle_var']) ? $_GET['telle_var'] : "");

	<?php
		echo "<input type='checkbox' name='telle_var' id='telle_var' value='1' ";
		if(isset($telle_var) and $telle_var=='1') { 
			echo "checked='checked'";
		}
		echo "/><label for='telle_var'></label><br />\n";
	?>
*/

//=========================
// AJOUT: boireaus 20081224
if(isset($_POST['valide_modif_model'])) {
	$affiche_nom_etab=isset($_POST['affiche_nom_etab']) ? $_POST['affiche_nom_etab'] : 0;
	$affiche_adresse_etab=isset($_POST['affiche_adresse_etab']) ? $_POST['affiche_adresse_etab'] : 0;
}
//=========================
//===================================================
// Christian renvoye vers le fichier PDF bulletin
	if (empty($_GET['classe']) and empty($_POST['classe'])) {$classe="";}
		else { if (isset($_GET['classe'])) {$classe=$_GET['classe'];} if (isset($_POST['classe'])) {$classe=$_POST['classe'];} }
	if (empty($_GET['eleve']) and empty($_POST['eleve'])) {$eleve="";}
		else { if (isset($_GET['eleve'])) {$eleve=$_GET['eleve'];} if (isset($_POST['eleve'])) {$eleve=$_POST['eleve'];} }
	if (empty($_GET['periode']) and empty($_POST['periode'])) {$periode="";}
		else { if (isset($_GET['periode'])) {$periode=$_GET['periode'];} if (isset($_POST['periode'])) {$periode=$_POST['periode'];} }
	if (empty($_GET['creer_pdf']) and empty($_POST['creer_pdf'])) {$creer_pdf="";}
		else { if (isset($_GET['creer_pdf'])) {$creer_pdf=$_GET['creer_pdf'];} if (isset($_POST['creer_pdf'])) {$creer_pdf=$_POST['creer_pdf'];} }

	if (empty($_GET['type_bulletin']) and empty($_POST['type_bulletin'])) {$type_bulletin="";}
		else { if (isset($_GET['type_bulletin'])) {$type_bulletin=$_GET['type_bulletin'];} if (isset($_POST['type_bulletin'])) {$type_bulletin=$_POST['type_bulletin'];} }

	if (empty($_GET['periode_ferme']) and empty($_POST['periode_ferme'])) { $periode_ferme = ''; }
	else { if (isset($_GET['periode_ferme'])) { $periode_ferme = $_GET['periode_ferme']; } if (isset($_POST['periode_ferme'])) { $periode_ferme = $_POST['periode_ferme']; } }
	if (empty($_GET['selection_eleve']) and empty($_POST['selection_eleve'])) { $selection_eleve = ''; }
	else { if (isset($_GET['selection_eleve'])) { $selection_eleve = $_GET['selection_eleve']; } if (isset($_POST['selection_eleve'])) { $selection_eleve = $_POST['selection_eleve']; } }

	$message_erreur = '';
	if ( !empty($classe[0]) and empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oubliez pas de sélectionner la ou les période(s) !'; }
	if ( empty($classe[0]) and !empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oubliez pas de sélectionner la ou les classe(s) !'; }
	if ( empty($classe[0]) and empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oubliez pas de sélectionner la ou les classe(s) et la ou les période(s) !'; }

	$_SESSION['classe'] = $classe;
	$_SESSION['eleve'] = $eleve;
	$_SESSION['periode'] = $periode;
	$_SESSION['periode_ferme'] = $periode_ferme;
	$_SESSION['type_bulletin'] = $type_bulletin;


	//==========================================
	// CETTE PAGE N'EXISTE PLUS
	//if(!empty($creer_pdf) and !empty($periode[0]) and !empty($classe[0]) and !empty($type_bulletin) and empty($selection_eleve) ) {  header("Location: buletin_pdf.php"); }
	//==========================================

// FIN Christian renvoye vers le fichier PDF bulletin
//===================================================


//===================================================
// Modif Christian pour les variable PDF
	$selection = isset($_POST["selection"]) ? $_POST["selection"] :NULL;
	$selection_eleve = isset($_POST["selection_eleve"]) ? $_POST["selection_eleve"] :NULL;
	$bt_select_periode = isset($_POST["bt_select_periode"]) ? $_POST["bt_select_periode"] :NULL;
	$valide_modif_model = isset($_POST["valide_modif_model"]) ? $_POST["valide_modif_model"] :NULL;

	if (empty($_FILES['fichier'])) { $fichier = ""; } else { $fichier = $_FILES['fichier']; }
	if (empty($_GET['format']) and empty($_POST['format'])) {$format="";}
		else { if (isset($_GET['format'])) {$format=$_GET['format'];} if (isset($_POST['format'])) {$format=$_POST['format'];} }
	if (empty($_GET['modele']) and empty($_POST['modele'])) {$modele="";}
		else { if (isset($_GET['modele'])) {$modele=$_GET['modele'];} if (isset($_POST['modele'])) {$modele=$_POST['modele'];} }
	if (empty($_GET['action_model']) and empty($_POST['action_model'])) {$action_model="";}
		else { if (isset($_GET['action_model'])) {$action_model=$_GET['action_model'];} if (isset($_POST['action_model'])) {$action_model=$_POST['action_model'];} }
	if (empty($_GET['modele_action']) and empty($_POST['modele_action'])) {$modele_action='';}
		else { if (isset($_GET['modele_action'])) {$modele_action=$_GET['modele_action'];} if (isset($_POST['modele_action'])) {$modele_action=$_POST['modele_action'];} }
	if (empty($_GET['action']) and empty($_POST['action'])) {$action="";}
		else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }


	if (empty($_GET['id_model_bulletin']) and empty($_POST['id_model_bulletin'])) {$id_model_bulletin="";}
	    else { if (isset($_GET['id_model_bulletin'])) {$id_model_bulletin=$_GET['id_model_bulletin'];} if (isset($_POST['id_model_bulletin'])) {$id_model_bulletin=$_POST['id_model_bulletin'];} }
	//if (empty($_GET['id_modele_bulletin']) and empty($_POST['id_modele_bulletin'])) {$id_modele_bulletin="";}
	//	else { if (isset($_GET['id_modele_bulletin'])) {$id_modele_bulletin=$_GET['id_modele_bulletin'];} if (isset($_POST['id_modele_bulletin'])) {$id_modele_bulletin=$_POST['id_modele_bulletin'];} }

	if (empty($_GET['active_bloc_datation']) and empty($_POST['active_bloc_datation'])) { $active_bloc_datation = ''; }
	else { if (isset($_GET['active_bloc_datation'])) { $active_bloc_datation = $_GET['active_bloc_datation']; } if (isset($_POST['active_bloc_datation'])) { $active_bloc_datation = $_POST['active_bloc_datation']; } }
	if (empty($_GET['active_bloc_eleve']) and empty($_POST['active_bloc_eleve'])) { $active_bloc_eleve = ''; }
	else { if (isset($_GET['active_bloc_eleve'])) { $active_bloc_eleve = $_GET['active_bloc_eleve']; } if (isset($_POST['active_bloc_eleve'])) { $active_bloc_eleve = $_POST['active_bloc_eleve']; } }
	if (empty($_GET['active_bloc_adresse_parent']) and empty($_POST['active_bloc_adresse_parent'])) { $active_bloc_adresse_parent = ''; }
	else { if (isset($_GET['active_bloc_adresse_parent'])) { $active_bloc_adresse_parent = $_GET['active_bloc_adresse_parent']; } if (isset($_POST['active_bloc_adresse_parent'])) { $active_bloc_adresse_parent = $_POST['active_bloc_adresse_parent']; } }

	// 20130215
	if (empty($_GET['active_bloc_absence']) and empty($_POST['active_bloc_absence'])) { $active_bloc_absence = ''; }
	else { if (isset($_GET['active_bloc_absence'])) { $active_bloc_absence = $_GET['active_bloc_absence']; } if (isset($_POST['active_bloc_absence'])) { $active_bloc_absence = $_POST['active_bloc_absence']; } }

	if (empty($_GET['afficher_abs_tot']) and empty($_POST['afficher_abs_tot'])) { $afficher_abs_tot = ''; }
	else { if (isset($_GET['afficher_abs_tot'])) { $afficher_abs_tot = $_GET['afficher_abs_tot']; } if (isset($_POST['afficher_abs_tot'])) { $afficher_abs_tot = $_POST['afficher_abs_tot']; } }
	if (empty($_GET['afficher_abs_nj']) and empty($_POST['afficher_abs_nj'])) { $afficher_abs_nj = ''; }
	else { if (isset($_GET['afficher_abs_nj'])) { $afficher_abs_nj = $_GET['afficher_abs_nj']; } if (isset($_POST['afficher_abs_nj'])) { $afficher_abs_nj = $_POST['afficher_abs_nj']; } }
	if (empty($_GET['afficher_abs_ret']) and empty($_POST['afficher_abs_ret'])) { $afficher_abs_ret = ''; }
	else { if (isset($_GET['afficher_abs_ret'])) { $afficher_abs_ret = $_GET['afficher_abs_ret']; } if (isset($_POST['afficher_abs_ret'])) { $afficher_abs_ret = $_POST['afficher_abs_ret']; } }

	if (empty($_GET['active_bloc_note_appreciation']) and empty($_POST['active_bloc_note_appreciation'])) { $active_bloc_note_appreciation = ''; }
	else { if (isset($_GET['active_bloc_note_appreciation'])) { $active_bloc_note_appreciation = $_GET['active_bloc_note_appreciation']; } if (isset($_POST['active_bloc_note_appreciation'])) { $active_bloc_note_appreciation = $_POST['active_bloc_note_appreciation']; } }
	if (empty($_GET['active_bloc_avis_conseil']) and empty($_POST['active_bloc_avis_conseil'])) { $active_bloc_avis_conseil = ''; }
	else { if (isset($_GET['active_bloc_avis_conseil'])) { $active_bloc_avis_conseil = $_GET['active_bloc_avis_conseil']; } if (isset($_POST['active_bloc_avis_conseil'])) { $active_bloc_avis_conseil = $_POST['active_bloc_avis_conseil']; } }
	if (empty($_GET['active_bloc_chef']) and empty($_POST['active_bloc_chef'])) { $active_bloc_chef = ''; }
	else { if (isset($_GET['active_bloc_chef'])) { $active_bloc_chef = $_GET['active_bloc_chef']; } if (isset($_POST['active_bloc_chef'])) { $active_bloc_chef = $_POST['active_bloc_chef']; } }
	if (empty($_GET['active_photo']) and empty($_POST['active_photo'])) { $active_photo = ''; }
	else { if (isset($_GET['active_photo'])) { $active_photo = $_GET['active_photo']; } if (isset($_POST['active_photo'])) { $active_photo = $_POST['active_photo']; } }
	if (empty($_GET['active_coef_moyenne']) and empty($_POST['active_coef_moyenne'])) { $active_coef_moyenne = ''; }
	else { if (isset($_GET['active_coef_moyenne'])) { $active_coef_moyenne = $_GET['active_coef_moyenne']; } if (isset($_POST['active_coef_moyenne'])) { $active_coef_moyenne = $_POST['active_coef_moyenne']; } }
	if (empty($_GET['active_nombre_note']) and empty($_POST['active_nombre_note'])) { $active_nombre_note = ''; }
	else { if (isset($_GET['active_nombre_note'])) { $active_nombre_note = $_GET['active_nombre_note']; } if (isset($_POST['active_nombre_note'])) { $active_nombre_note = $_POST['active_nombre_note']; } }
	if (empty($_GET['active_nombre_note_case']) and empty($_POST['active_nombre_note_case'])) { $active_nombre_note_case = ''; }
	else { if (isset($_GET['active_nombre_note_case'])) { $active_nombre_note_case = $_GET['active_nombre_note_case']; } if (isset($_POST['active_nombre_note_case'])) { $active_nombre_note_case = $_POST['active_nombre_note_case']; } }
	if (empty($_GET['active_moyenne']) and empty($_POST['active_moyenne'])) { $active_moyenne = ''; }
	else { if (isset($_GET['active_moyenne'])) { $active_moyenne = $_GET['active_moyenne']; } if (isset($_POST['active_moyenne'])) { $active_moyenne = $_POST['active_moyenne']; } }
	if (empty($_GET['active_moyenne_eleve']) and empty($_POST['active_moyenne_eleve'])) { $active_moyenne_eleve = ''; }
	else { if (isset($_GET['active_moyenne_eleve'])) { $active_moyenne_eleve = $_GET['active_moyenne_eleve']; } if (isset($_POST['active_moyenne_eleve'])) { $active_moyenne_eleve = $_POST['active_moyenne_eleve']; } }
	if (empty($_GET['active_moyenne_classe']) and empty($_POST['active_moyenne_classe'])) { $active_moyenne_classe = ''; }
	else { if (isset($_GET['active_moyenne_classe'])) { $active_moyenne_classe = $_GET['active_moyenne_classe']; } if (isset($_POST['active_moyenne_classe'])) { $active_moyenne_classe = $_POST['active_moyenne_classe']; } }
	if (empty($_GET['active_moyenne_min']) and empty($_POST['active_moyenne_min'])) { $active_moyenne_min = ''; }
	else { if (isset($_GET['active_moyenne_min'])) { $active_moyenne_min = $_GET['active_moyenne_min']; } if (isset($_POST['active_moyenne_min'])) { $active_moyenne_min = $_POST['active_moyenne_min']; } }
	if (empty($_GET['active_moyenne_max']) and empty($_POST['active_moyenne_max'])) { $active_moyenne_max = ''; }
	else { if (isset($_GET['active_moyenne_max'])) { $active_moyenne_max = $_GET['active_moyenne_max']; } if (isset($_POST['active_moyenne_max'])) { $active_moyenne_max = $_POST['active_moyenne_max']; } }
	if (empty($_GET['active_regroupement_cote']) and empty($_POST['active_regroupement_cote'])) { $active_regroupement_cote = ''; }
	else { if (isset($_GET['active_regroupement_cote'])) { $active_regroupement_cote = $_GET['active_regroupement_cote']; } if (isset($_POST['active_regroupement_cote'])) { $active_regroupement_cote = $_POST['active_regroupement_cote']; } }
	if (empty($_GET['active_entete_regroupement']) and empty($_POST['active_entete_regroupement'])) { $active_entete_regroupement = ''; }
	else { if (isset($_GET['active_entete_regroupement'])) { $active_entete_regroupement = $_GET['active_entete_regroupement']; } if (isset($_POST['active_entete_regroupement'])) { $active_entete_regroupement = $_POST['active_entete_regroupement']; } }
	if (empty($_GET['active_moyenne_regroupement']) and empty($_POST['active_moyenne_regroupement'])) { $active_moyenne_regroupement = ''; }
	else { if (isset($_GET['active_moyenne_regroupement'])) { $active_moyenne_regroupement = $_GET['active_moyenne_regroupement']; } if (isset($_POST['active_moyenne_regroupement'])) { $active_moyenne_regroupement = $_POST['active_moyenne_regroupement']; } }
	if (empty($_GET['active_rang']) and empty($_POST['active_rang'])) { $active_rang = ''; }
	else { if (isset($_GET['active_rang'])) { $active_rang = $_GET['active_rang']; } if (isset($_POST['active_rang'])) { $active_rang = $_POST['active_rang']; } }
	if (empty($_GET['active_graphique_niveau']) and empty($_POST['active_graphique_niveau'])) { $active_graphique_niveau = ''; }
	else { if (isset($_GET['active_graphique_niveau'])) { $active_graphique_niveau = $_GET['active_graphique_niveau']; } if (isset($_POST['active_graphique_niveau'])) { $active_graphique_niveau = $_POST['active_graphique_niveau']; } }
	if (empty($_GET['active_appreciation']) and empty($_POST['active_appreciation'])) { $active_appreciation = ''; }
	else { if (isset($_GET['active_appreciation'])) { $active_appreciation = $_GET['active_appreciation']; } if (isset($_POST['active_appreciation'])) { $active_appreciation = $_POST['active_appreciation']; } }

	if (empty($_GET['cell_ajustee_texte_matiere']) and empty($_POST['cell_ajustee_texte_matiere'])) {
		$cell_ajustee_texte_matiere = 0;
	}
	else {
		if (isset($_GET['cell_ajustee_texte_matiere'])) {
			$cell_ajustee_texte_matiere = $_GET['cell_ajustee_texte_matiere'];
		}
		if (isset($_POST['cell_ajustee_texte_matiere'])) {
			$cell_ajustee_texte_matiere = $_POST['cell_ajustee_texte_matiere'];
		}
	}

	if (empty($_GET['cell_ajustee_texte_matiere_ratio_min_max']) and empty($_POST['cell_ajustee_texte_matiere_ratio_min_max'])) {
		$cell_ajustee_texte_matiere_ratio_min_max = 3;
	}
	else {
		if (isset($_GET['cell_ajustee_texte_matiere_ratio_min_max'])) {
			$cell_ajustee_texte_matiere_ratio_min_max = $_GET['cell_ajustee_texte_matiere_ratio_min_max'];
		}
		if (isset($_POST['cell_ajustee_texte_matiere_ratio_min_max'])) {
			$cell_ajustee_texte_matiere_ratio_min_max = $_POST['cell_ajustee_texte_matiere_ratio_min_max'];
		}
	}
	if((!is_numeric($cell_ajustee_texte_matiere_ratio_min_max))||($cell_ajustee_texte_matiere_ratio_min_max<=0)) {
		$cell_ajustee_texte_matiere_ratio_min_max=3;
	}

	if (empty($_GET['affiche_doublement']) and empty($_POST['affiche_doublement'])) { $affiche_doublement = ''; }
	else { if (isset($_GET['affiche_doublement'])) { $affiche_doublement = $_GET['affiche_doublement']; } if (isset($_POST['affiche_doublement'])) { $affiche_doublement = $_POST['affiche_doublement']; } }
	if (empty($_GET['affiche_date_naissance']) and empty($_POST['affiche_date_naissance'])) { $affiche_date_naissance = ''; }
	else { if (isset($_GET['affiche_date_naissance'])) { $affiche_date_naissance = $_GET['affiche_date_naissance']; } if (isset($_POST['affiche_date_naissance'])) { $affiche_date_naissance = $_POST['affiche_date_naissance']; } }
	if (empty($_GET['affiche_dp']) and empty($_POST['affiche_dp'])) { $affiche_dp = ''; }
	else { if (isset($_GET['affiche_dp'])) { $affiche_dp = $_GET['affiche_dp']; } if (isset($_POST['affiche_dp'])) { $affiche_dp = $_POST['affiche_dp']; } }
	if (empty($_GET['affiche_nom_court']) and empty($_POST['affiche_nom_court'])) { $affiche_nom_court = ''; }
	else { if (isset($_GET['affiche_nom_court'])) { $affiche_nom_court = $_GET['affiche_nom_court']; } if (isset($_POST['affiche_nom_court'])) { $affiche_nom_court = $_POST['affiche_nom_court']; } }
	if (empty($_GET['affiche_effectif_classe']) and empty($_POST['affiche_effectif_classe'])) { $affiche_effectif_classe = ''; }
	else { if (isset($_GET['affiche_effectif_classe'])) { $affiche_effectif_classe = $_GET['affiche_effectif_classe']; } if (isset($_POST['affiche_effectif_classe'])) { $affiche_effectif_classe = $_POST['affiche_effectif_classe']; } }
	if (empty($_GET['affiche_numero_impression']) and empty($_POST['affiche_numero_impression'])) { $affiche_numero_impression = ''; }
	else { if (isset($_GET['affiche_numero_impression'])) { $affiche_numero_impression = $_GET['affiche_numero_impression']; } if (isset($_POST['affiche_numero_impression'])) { $affiche_numero_impression = $_POST['affiche_numero_impression']; } }
	if (empty($_GET['active_reperage_eleve']) and empty($_POST['active_reperage_eleve'])) { $active_reperage_eleve = ''; }
	else { if (isset($_GET['active_reperage_eleve'])) { $active_reperage_eleve = $_GET['active_reperage_eleve']; } if (isset($_POST['active_reperage_eleve'])) { $active_reperage_eleve = $_POST['active_reperage_eleve']; } }
	if (empty($_GET['couleur_reperage_eleve1']) and empty($_POST['couleur_reperage_eleve1'])) { $couleur_reperage_eleve1 = ''; }
	else { if (isset($_GET['couleur_reperage_eleve1'])) { $couleur_reperage_eleve1 = $_GET['couleur_reperage_eleve1']; } if (isset($_POST['couleur_reperage_eleve1'])) { $couleur_reperage_eleve1 = $_POST['couleur_reperage_eleve1']; } }
	if (empty($_GET['couleur_reperage_eleve2']) and empty($_POST['couleur_reperage_eleve2'])) { $couleur_reperage_eleve2 = ''; }
	else { if (isset($_GET['couleur_reperage_eleve2'])) { $couleur_reperage_eleve2 = $_GET['couleur_reperage_eleve2']; } if (isset($_POST['couleur_reperage_eleve2'])) { $couleur_reperage_eleve2 = $_POST['couleur_reperage_eleve2']; } }
	if (empty($_GET['couleur_reperage_eleve3']) and empty($_POST['couleur_reperage_eleve3'])) { $couleur_reperage_eleve3 = ''; }
	else { if (isset($_GET['couleur_reperage_eleve3'])) { $couleur_reperage_eleve3 = $_GET['couleur_reperage_eleve3']; } if (isset($_POST['couleur_reperage_eleve3'])) { $couleur_reperage_eleve3 = $_POST['couleur_reperage_eleve3']; } }
	if (empty($_GET['couleur_categorie_entete']) and empty($_POST['couleur_categorie_entete'])) { $couleur_categorie_entete = ''; }
	else { if (isset($_GET['couleur_categorie_entete'])) { $couleur_categorie_entete = $_GET['couleur_categorie_entete']; } if (isset($_POST['couleur_categorie_entete'])) { $couleur_categorie_entete = $_POST['couleur_categorie_entete']; } }
	if (empty($_GET['couleur_categorie_entete1']) and empty($_POST['couleur_categorie_entete1'])) { $couleur_categorie_entete1 = ''; }
	else { if (isset($_GET['couleur_categorie_entete1'])) { $couleur_categorie_entete1 = $_GET['couleur_categorie_entete1']; } if (isset($_POST['couleur_categorie_entete1'])) { $couleur_categorie_entete1 = $_POST['couleur_categorie_entete1']; } }
	if (empty($_GET['couleur_categorie_entete2']) and empty($_POST['couleur_categorie_entete2'])) { $couleur_categorie_entete2 = ''; }
	else { if (isset($_GET['couleur_categorie_entete2'])) { $couleur_categorie_entete2 = $_GET['couleur_categorie_entete2']; } if (isset($_POST['couleur_categorie_entete2'])) { $couleur_categorie_entete2 = $_POST['couleur_categorie_entete2']; } }
	if (empty($_GET['couleur_categorie_entete3']) and empty($_POST['couleur_categorie_entete3'])) { $couleur_categorie_entete3 = ''; }
	else { if (isset($_GET['couleur_categorie_entete3'])) { $couleur_categorie_entete3 = $_GET['couleur_categorie_entete3']; } if (isset($_POST['couleur_categorie_entete3'])) { $couleur_categorie_entete3 = $_POST['couleur_categorie_entete3']; } }
	if (empty($_GET['couleur_categorie_cote']) and empty($_POST['couleur_categorie_cote'])) { $couleur_categorie_cote = ''; }
	else { if (isset($_GET['couleur_categorie_cote'])) { $couleur_categorie_cote = $_GET['couleur_categorie_cote']; } if (isset($_POST['couleur_categorie_cote'])) { $couleur_categorie_cote = $_POST['couleur_categorie_cote']; } }
	if (empty($_GET['couleur_categorie_cote1']) and empty($_POST['couleur_categorie_cote1'])) { $couleur_categorie_cote1 = ''; }
	else { if (isset($_GET['couleur_categorie_cote1'])) { $couleur_categorie_cote1 = $_GET['couleur_categorie_cote1']; } if (isset($_POST['couleur_categorie_cote1'])) { $couleur_categorie_cote1 = $_POST['couleur_categorie_cote1']; } }
	if (empty($_GET['couleur_categorie_cote2']) and empty($_POST['couleur_categorie_cote2'])) { $couleur_categorie_cote2 = ''; }
	else { if (isset($_GET['couleur_categorie_cote2'])) { $couleur_categorie_cote2 = $_GET['couleur_categorie_cote2']; } if (isset($_POST['couleur_categorie_cote2'])) { $couleur_categorie_cote2 = $_POST['couleur_categorie_cote2']; } }
	if (empty($_GET['couleur_categorie_cote3']) and empty($_POST['couleur_categorie_cote3'])) { $couleur_categorie_cote3 = ''; }
	else { if (isset($_GET['couleur_categorie_cote3'])) { $couleur_categorie_cote3 = $_GET['couleur_categorie_cote3']; } if (isset($_POST['couleur_categorie_cote3'])) { $couleur_categorie_cote3 = $_POST['couleur_categorie_cote3']; } }
	if (empty($_GET['couleur_moy_general']) and empty($_POST['couleur_moy_general'])) { $couleur_moy_general = ''; }
	else { if (isset($_GET['couleur_moy_general'])) { $couleur_moy_general = $_GET['couleur_moy_general']; } if (isset($_POST['couleur_moy_general'])) { $couleur_moy_general = $_POST['couleur_moy_general']; } }
	if (empty($_GET['couleur_moy_general1']) and empty($_POST['couleur_moy_general1'])) { $couleur_moy_general1 = ''; }
	else { if (isset($_GET['couleur_moy_general1'])) { $couleur_moy_general1 = $_GET['couleur_moy_general1']; } if (isset($_POST['couleur_moy_general1'])) { $couleur_moy_general1 = $_POST['couleur_moy_general1']; } }
	if (empty($_GET['couleur_moy_general2']) and empty($_POST['couleur_moy_general2'])) { $couleur_moy_general2 = ''; }
	else { if (isset($_GET['couleur_moy_general2'])) { $couleur_moy_general2 = $_GET['couleur_moy_general2']; } if (isset($_POST['couleur_moy_general2'])) { $couleur_moy_general2 = $_POST['couleur_moy_general2']; } }
	if (empty($_GET['couleur_moy_general3']) and empty($_POST['couleur_moy_general3'])) { $couleur_moy_general3 = ''; }
	else { if (isset($_GET['couleur_moy_general3'])) { $couleur_moy_general3 = $_GET['couleur_moy_general3']; } if (isset($_POST['couleur_moy_general3'])) { $couleur_moy_general3 = $_POST['couleur_moy_general3']; } }
	if (empty($_GET['titre_entete_matiere']) and empty($_POST['titre_entete_matiere'])) { $titre_entete_matiere = ''; }
	else { if (isset($_GET['titre_entete_matiere'])) { $titre_entete_matiere = $_GET['titre_entete_matiere']; } if (isset($_POST['titre_entete_matiere'])) { $titre_entete_matiere = $_POST['titre_entete_matiere']; } }
	if (empty($_GET['titre_entete_coef']) and empty($_POST['titre_entete_coef'])) { $titre_entete_coef = ''; }
	else { if (isset($_GET['titre_entete_coef'])) { $titre_entete_coef = $_GET['titre_entete_coef']; } if (isset($_POST['titre_entete_coef'])) { $titre_entete_coef = $_POST['titre_entete_coef']; } }
	if (empty($_GET['titre_entete_nbnote']) and empty($_POST['titre_entete_nbnote'])) { $titre_entete_nbnote = ''; }
	else { if (isset($_GET['titre_entete_nbnote'])) { $titre_entete_nbnote = $_GET['titre_entete_nbnote']; } if (isset($_POST['titre_entete_nbnote'])) { $titre_entete_nbnote = $_POST['titre_entete_nbnote']; } }
	if (empty($_GET['titre_entete_rang']) and empty($_POST['titre_entete_rang'])) { $titre_entete_rang = ''; }
	else { if (isset($_GET['titre_entete_rang'])) { $titre_entete_rang = $_GET['titre_entete_rang']; } if (isset($_POST['titre_entete_rang'])) { $titre_entete_rang = $_POST['titre_entete_rang']; } }
	if (empty($_GET['titre_entete_appreciation']) and empty($_POST['titre_entete_appreciation'])) { $titre_entete_appreciation = ''; }
	else { if (isset($_GET['titre_entete_appreciation'])) { $titre_entete_appreciation = $_GET['titre_entete_appreciation']; } if (isset($_POST['titre_entete_appreciation'])) { $titre_entete_appreciation = $_POST['titre_entete_appreciation']; } }
	if (empty($_GET['caractere_utilse']) and empty($_POST['caractere_utilse'])) { $caractere_utilse = ''; }
	else { if (isset($_GET['caractere_utilse'])) { $caractere_utilse = $_GET['caractere_utilse']; } if (isset($_POST['caractere_utilse'])) { $caractere_utilse = $_POST['caractere_utilse']; } }
	if (empty($_GET['X_parent']) and empty($_POST['X_parent'])) { $X_parent = ''; }
	else { if (isset($_GET['X_parent'])) { $X_parent = $_GET['X_parent']; } if (isset($_POST['X_parent'])) { $X_parent = $_POST['X_parent']; } }
	if (empty($_GET['Y_parent']) and empty($_POST['Y_parent'])) { $Y_parent = ''; }
	else { if (isset($_GET['Y_parent'])) { $Y_parent = $_GET['Y_parent']; } if (isset($_POST['Y_parent'])) { $Y_parent = $_POST['Y_parent']; } }
	if (empty($_GET['X_eleve']) and empty($_POST['X_eleve'])) { $X_eleve = ''; }
	else { if (isset($_GET['X_eleve'])) { $X_eleve = $_GET['X_eleve']; } if (isset($_POST['X_eleve'])) { $X_eleve = $_POST['X_eleve']; } }
	if (empty($_GET['Y_eleve']) and empty($_POST['Y_eleve'])) { $Y_eleve = ''; }
	else { if (isset($_GET['Y_eleve'])) { $Y_eleve = $_GET['Y_eleve']; } if (isset($_POST['Y_eleve'])) { $Y_eleve = $_POST['Y_eleve']; } }
	if (empty($_GET['cadre_eleve']) and empty($_POST['cadre_eleve'])) { $cadre_eleve = ''; }
	else { if (isset($_GET['cadre_eleve'])) { $cadre_eleve = $_GET['cadre_eleve']; } if (isset($_POST['cadre_eleve'])) { $cadre_eleve = $_POST['cadre_eleve']; } }
	if (empty($_GET['X_datation_bul']) and empty($_POST['X_datation_bul'])) { $X_datation_bul = ''; }
	else { if (isset($_GET['X_datation_bul'])) { $X_datation_bul = $_GET['X_datation_bul']; } if (isset($_POST['X_datation_bul'])) { $X_datation_bul = $_POST['X_datation_bul']; } }
	if (empty($_GET['Y_datation_bul']) and empty($_POST['Y_datation_bul'])) { $Y_datation_bul = ''; }
	else { if (isset($_GET['Y_datation_bul'])) { $Y_datation_bul = $_GET['Y_datation_bul']; } if (isset($_POST['Y_datation_bul'])) { $Y_datation_bul = $_POST['Y_datation_bul']; } }
	if (empty($_GET['cadre_datation_bul']) and empty($_POST['cadre_datation_bul'])) { $cadre_datation_bul = ''; }
	else { if (isset($_GET['cadre_datation_bul'])) { $cadre_datation_bul = $_GET['cadre_datation_bul']; } if (isset($_POST['cadre_datation_bul'])) { $cadre_datation_bul = $_POST['cadre_datation_bul']; } }
	if (empty($_GET['hauteur_info_categorie']) and empty($_POST['hauteur_info_categorie'])) { $hauteur_info_categorie = ''; }
	else { if (isset($_GET['hauteur_info_categorie'])) { $hauteur_info_categorie = $_GET['hauteur_info_categorie']; } if (isset($_POST['hauteur_info_categorie'])) { $hauteur_info_categorie = $_POST['hauteur_info_categorie']; } }
	if (empty($_GET['X_note_app']) and empty($_POST['X_note_app'])) { $X_note_app = ''; }
	else { if (isset($_GET['X_note_app'])) { $X_note_app = $_GET['X_note_app']; } if (isset($_POST['X_note_app'])) { $X_note_app = $_POST['X_note_app']; } }
	if (empty($_GET['Y_note_app']) and empty($_POST['Y_note_app'])) { $Y_note_app = ''; }
	else { if (isset($_GET['Y_note_app'])) { $Y_note_app = $_GET['Y_note_app']; } if (isset($_POST['Y_note_app'])) { $Y_note_app = $_POST['Y_note_app']; } }
	if (empty($_GET['longeur_note_app']) and empty($_POST['longeur_note_app'])) { $longeur_note_app = ''; }
	else { if (isset($_GET['longeur_note_app'])) { $longeur_note_app = $_GET['longeur_note_app']; } if (isset($_POST['longeur_note_app'])) { $longeur_note_app = $_POST['longeur_note_app']; } }
	if (empty($_GET['hauteur_note_app']) and empty($_POST['hauteur_note_app'])) { $hauteur_note_app = ''; }
	else { if (isset($_GET['hauteur_note_app'])) { $hauteur_note_app = $_GET['hauteur_note_app']; } if (isset($_POST['hauteur_note_app'])) { $hauteur_note_app = $_POST['hauteur_note_app']; } }
	if (empty($_GET['largeur_coef_moyenne']) and empty($_POST['largeur_coef_moyenne'])) { $largeur_coef_moyenne = ''; }
	else { if (isset($_GET['largeur_coef_moyenne'])) { $largeur_coef_moyenne = $_GET['largeur_coef_moyenne']; } if (isset($_POST['largeur_coef_moyenne'])) { $largeur_coef_moyenne = $_POST['largeur_coef_moyenne']; } }
	if (empty($_GET['largeur_nombre_note']) and empty($_POST['largeur_nombre_note'])) { $largeur_nombre_note = ''; }
	else { if (isset($_GET['largeur_nombre_note'])) { $largeur_nombre_note = $_GET['largeur_nombre_note']; } if (isset($_POST['largeur_nombre_note'])) { $largeur_nombre_note = $_POST['largeur_nombre_note']; } }
	if (empty($_GET['largeur_d_une_moyenne']) and empty($_POST['largeur_d_une_moyenne'])) { $largeur_d_une_moyenne = ''; }
	else { if (isset($_GET['largeur_d_une_moyenne'])) { $largeur_d_une_moyenne = $_GET['largeur_d_une_moyenne']; } if (isset($_POST['largeur_d_une_moyenne'])) { $largeur_d_une_moyenne = $_POST['largeur_d_une_moyenne']; } }
	if (empty($_GET['largeur_niveau']) and empty($_POST['largeur_niveau'])) { $largeur_niveau = ''; }
	else { if (isset($_GET['largeur_niveau'])) { $largeur_niveau = $_GET['largeur_niveau']; } if (isset($_POST['largeur_niveau'])) { $largeur_niveau = $_POST['largeur_niveau']; } }
	if (empty($_GET['largeur_rang']) and empty($_POST['largeur_rang'])) { $largeur_rang = ''; }
	else { if (isset($_GET['largeur_rang'])) { $largeur_rang = $_GET['largeur_rang']; } if (isset($_POST['largeur_rang'])) { $largeur_rang = $_POST['largeur_rang']; } }

	if (empty($_GET['X_absence']) and empty($_POST['X_absence'])) { $X_absence = ''; }
	else { if (isset($_GET['X_absence'])) { $X_absence = $_GET['X_absence']; } if (isset($_POST['X_absence'])) { $X_absence = $_POST['X_absence']; } }

	$largeur_cadre_absences=isset($_GET['largeur_cadre_absences']) ? $_GET['largeur_cadre_absences'] : (isset($_POST['largeur_cadre_absences']) ? $_POST['largeur_cadre_absences'] : 200);

	if (empty($_GET['hauteur_entete_moyenne_general']) and empty($_POST['hauteur_entete_moyenne_general'])) { $hauteur_entete_moyenne_general = ''; }
	else { if (isset($_GET['hauteur_entete_moyenne_general'])) { $hauteur_entete_moyenne_general = $_GET['hauteur_entete_moyenne_general']; } if (isset($_POST['hauteur_entete_moyenne_general'])) { $hauteur_entete_moyenne_general = $_POST['hauteur_entete_moyenne_general']; } }
	if (empty($_GET['X_avis_cons']) and empty($_POST['X_avis_cons'])) { $X_avis_cons = ''; }
	else { if (isset($_GET['X_avis_cons'])) { $X_avis_cons = $_GET['X_avis_cons']; } if (isset($_POST['X_avis_cons'])) { $X_avis_cons = $_POST['X_avis_cons']; } }

	if (empty($_GET['cadre_avis_cons']) and empty($_POST['cadre_avis_cons'])) { $cadre_avis_cons = ''; }
	else { if (isset($_GET['cadre_avis_cons'])) { $cadre_avis_cons = $_GET['cadre_avis_cons']; } if (isset($_POST['cadre_avis_cons'])) { $cadre_avis_cons = $_POST['cadre_avis_cons']; } }

	$affich_mentions=isset($_GET['affich_mentions']) ? $_GET['affich_mentions'] : (isset($_POST['affich_mentions']) ? $_POST['affich_mentions'] : (isset($_POST['is_posted']) ? 'n' : 'y'));
	$affich_intitule_mentions=isset($_GET['affich_intitule_mentions']) ? $_GET['affich_intitule_mentions'] : (isset($_POST['affich_intitule_mentions']) ? $_POST['affich_intitule_mentions'] : (isset($_POST['is_posted']) ? 'n' : 'y'));
	$affich_coches_mentions=isset($_GET['affich_coches_mentions']) ? $_GET['affich_coches_mentions'] : (isset($_POST['affich_coches_mentions']) ? $_POST['affich_coches_mentions'] : (isset($_POST['is_posted']) ? 'n' : 'y'));

	if (empty($_GET['X_sign_chef']) and empty($_POST['X_sign_chef'])) { $X_sign_chef = ''; }
	else { if (isset($_GET['X_sign_chef'])) { $X_sign_chef = $_GET['X_sign_chef']; } if (isset($_POST['X_sign_chef'])) { $X_sign_chef = $_POST['X_sign_chef']; } }
	if (empty($_GET['cadre_sign_chef']) and empty($_POST['cadre_sign_chef'])) { $cadre_sign_chef = ''; }
	else { if (isset($_GET['cadre_sign_chef'])) { $cadre_sign_chef = $_GET['cadre_sign_chef']; } if (isset($_POST['cadre_sign_chef'])) { $cadre_sign_chef = $_POST['cadre_sign_chef']; } }
	if (empty($_GET['affiche_filigrame']) and empty($_POST['affiche_filigrame'])) { $affiche_filigrame = ''; }
	else { if (isset($_GET['affiche_filigrame'])) { $affiche_filigrame = $_GET['affiche_filigrame']; } if (isset($_POST['affiche_filigrame'])) { $affiche_filigrame = $_POST['affiche_filigrame']; } }
	if (empty($_GET['texte_filigrame']) and empty($_POST['texte_filigrame'])) { $texte_filigrame = ''; }
	else { if (isset($_GET['texte_filigrame'])) { $texte_filigrame = $_GET['texte_filigrame']; } if (isset($_POST['texte_filigrame'])) { $texte_filigrame = $_POST['texte_filigrame']; } }
	if (empty($_GET['affiche_logo_etab']) and empty($_POST['affiche_logo_etab'])) { $affiche_logo_etab = ''; }
	else { if (isset($_GET['affiche_logo_etab'])) { $affiche_logo_etab = $_GET['affiche_logo_etab']; } if (isset($_POST['affiche_logo_etab'])) { $affiche_logo_etab = $_POST['affiche_logo_etab']; } }
	if (empty($_GET['nom_etab_gras']) and empty($_POST['nom_etab_gras'])) { $nom_etab_gras = ''; }
	else { if (isset($_GET['nom_etab_gras'])) { $nom_etab_gras = $_GET['nom_etab_gras']; } if (isset($_POST['nom_etab_gras'])) { $nom_etab_gras = $_POST['nom_etab_gras']; } }
	if (empty($_GET['entente_mel']) and empty($_POST['entente_mel'])) { $entente_mel = ''; }
	else { if (isset($_GET['entente_mel'])) { $entente_mel = $_GET['entente_mel']; } if (isset($_POST['entente_mel'])) { $entente_mel = $_POST['entente_mel']; } }
	if (empty($_GET['entente_tel']) and empty($_POST['entente_tel'])) { $entente_tel = ''; }
	else { if (isset($_GET['entente_tel'])) { $entente_tel = $_GET['entente_tel']; } if (isset($_POST['entente_tel'])) { $entente_tel = $_POST['entente_tel']; } }

	if (empty($_GET['entente_fax']) and empty($_POST['entente_fax'])) { $entente_fax = ''; }
	else { if (isset($_GET['entente_fax'])) { $entente_fax = $_GET['entente_fax']; } if (isset($_POST['entente_fax'])) { $entente_fax = $_POST['entente_fax']; } }

	$entete_info_etab_suppl=isset($_POST['entete_info_etab_suppl']) ? $_POST['entete_info_etab_suppl'] : "n";
	$entete_info_etab_suppl_texte=isset($_POST['entete_info_etab_suppl_texte']) ? $_POST['entete_info_etab_suppl_texte'] : "Site web";
	$entete_info_etab_suppl_valeur=isset($_POST['entete_info_etab_suppl_valeur']) ? $_POST['entete_info_etab_suppl_valeur'] : "http://";

	if (empty($_GET['L_max_logo']) and empty($_POST['L_max_logo'])) { $L_max_logo = ''; }
	else { if (isset($_GET['L_max_logo'])) { $L_max_logo = $_GET['L_max_logo']; } if (isset($_POST['L_max_logo'])) { $L_max_logo = $_POST['L_max_logo']; } }
	if (empty($_GET['H_max_logo']) and empty($_POST['H_max_logo'])) { $H_max_logo = ''; }
	else { if (isset($_GET['H_max_logo'])) { $H_max_logo = $_GET['H_max_logo']; } if (isset($_POST['H_max_logo'])) { $H_max_logo = $_POST['H_max_logo']; } }
	if (empty($_GET['toute_moyenne_meme_col']) and empty($_POST['toute_moyenne_meme_col'])) { $toute_moyenne_meme_col = ''; }
	else { if (isset($_GET['toute_moyenne_meme_col'])) { $toute_moyenne_meme_col = $_GET['toute_moyenne_meme_col']; } if (isset($_POST['toute_moyenne_meme_col'])) { $toute_moyenne_meme_col = $_POST['toute_moyenne_meme_col']; } }

	$moyennes_periodes_precedentes=isset($_GET['moyennes_periodes_precedentes']) ? $_GET['moyennes_periodes_precedentes'] : (isset($_POST['moyennes_periodes_precedentes']) ? $_POST['moyennes_periodes_precedentes'] : 'n');

	$evolution_moyenne_periode_precedente=isset($_GET['evolution_moyenne_periode_precedente']) ? $_GET['evolution_moyenne_periode_precedente'] : (isset($_POST['evolution_moyenne_periode_precedente']) ? $_POST['evolution_moyenne_periode_precedente'] : 'n');

	$moyennes_annee=isset($_GET['moyennes_annee']) ? $_GET['moyennes_annee'] : (isset($_POST['moyennes_annee']) ? $_POST['moyennes_annee'] : 'n');

	if (empty($_GET['active_coef_sousmoyene']) and empty($_POST['active_coef_sousmoyene'])) { $active_coef_sousmoyene = ''; }
	else { if (isset($_GET['active_coef_sousmoyene'])) { $active_coef_sousmoyene = $_GET['active_coef_sousmoyene']; } if (isset($_POST['active_coef_sousmoyene'])) { $active_coef_sousmoyene = $_POST['active_coef_sousmoyene']; } }
	if (empty($_GET['arrondie_choix']) and empty($_POST['arrondie_choix'])) { $arrondie_choix = ''; }
	else { if (isset($_GET['arrondie_choix'])) { $arrondie_choix = $_GET['arrondie_choix']; } if (isset($_POST['arrondie_choix'])) { $arrondie_choix = $_POST['arrondie_choix']; } }
	if (empty($_GET['nb_chiffre_virgule']) and empty($_POST['nb_chiffre_virgule'])) { $nb_chiffre_virgule = ''; }
	else { if (isset($_GET['nb_chiffre_virgule'])) { $nb_chiffre_virgule = $_GET['nb_chiffre_virgule']; } if (isset($_POST['nb_chiffre_virgule'])) { $nb_chiffre_virgule = $_POST['nb_chiffre_virgule']; } }
	if (empty($_GET['chiffre_avec_zero']) and empty($_POST['chiffre_avec_zero'])) { $chiffre_avec_zero = ''; }
	else { if (isset($_GET['chiffre_avec_zero'])) { $chiffre_avec_zero = $_GET['chiffre_avec_zero']; } if (isset($_POST['chiffre_avec_zero'])) { $chiffre_avec_zero = $_POST['chiffre_avec_zero']; } }
	if (empty($_GET['autorise_sous_matiere']) and empty($_POST['autorise_sous_matiere'])) { $autorise_sous_matiere = ''; }
	else { if (isset($_GET['autorise_sous_matiere'])) { $autorise_sous_matiere = $_GET['autorise_sous_matiere']; } if (isset($_POST['autorise_sous_matiere'])) { $autorise_sous_matiere = $_POST['autorise_sous_matiere']; } }
	if (empty($_GET['affichage_haut_responsable']) and empty($_POST['affichage_haut_responsable'])) { $affichage_haut_responsable = ''; }
	else { if (isset($_GET['affichage_haut_responsable'])) { $affichage_haut_responsable = $_GET['affichage_haut_responsable']; } if (isset($_POST['affichage_haut_responsable'])) { $affichage_haut_responsable = $_POST['affichage_haut_responsable']; } }
	if (empty($_GET['entete_model_bulletin']) and empty($_POST['entete_model_bulletin'])) {$entete_model_bulletin="";}
		else { if (isset($_GET['entete_model_bulletin'])) {$entete_model_bulletin=$_GET['entete_model_bulletin'];} if (isset($_POST['entete_model_bulletin'])) {$entete_model_bulletin=$_POST['entete_model_bulletin'];} }
	if (empty($_GET['ordre_entete_model_bulletin']) and empty($_POST['ordre_entete_model_bulletin'])) {$ordre_entete_model_bulletin="";}
		else { if (isset($_GET['ordre_entete_model_bulletin'])) {$ordre_entete_model_bulletin=$_GET['ordre_entete_model_bulletin'];} if (isset($_POST['ordre_entete_model_bulletin'])) {$ordre_entete_model_bulletin=$_POST['ordre_entete_model_bulletin'];} }
	if (empty($_GET['affiche_etab_origine']) and empty($_POST['affiche_etab_origine'])) {$affiche_etab_origine="";}
		else { if (isset($_GET['affiche_etab_origine'])) {$affiche_etab_origine=$_GET['affiche_etab_origine'];} if (isset($_POST['affiche_etab_origine'])) {$affiche_etab_origine=$_POST['affiche_etab_origine'];} }
	if (empty($_GET['imprime_pour']) and empty($_POST['imprime_pour'])) {$imprime_pour="";}
		else { if (isset($_GET['imprime_pour'])) {$imprime_pour=$_GET['imprime_pour'];} if (isset($_POST['imprime_pour'])) {$imprime_pour=$_POST['imprime_pour'];} }
	if (empty($_GET['copie_model']) and empty($_POST['copie_model'])) { $copie_model = ''; }
	else { if (isset($_GET['copie_model'])) { $copie_model = $_GET['copie_model']; } if (isset($_POST['copie_model'])) { $copie_model = $_POST['copie_model']; } }
	if (empty($_GET['largeur_matiere']) and empty($_POST['largeur_matiere'])) { $largeur_matiere = ''; }
	else { if (isset($_GET['largeur_matiere'])) { $largeur_matiere = $_GET['largeur_matiere']; } if (isset($_POST['largeur_matiere'])) { $largeur_matiere = $_POST['largeur_matiere']; } }
	if (empty($_GET['taille_texte_date_edition']) and empty($_POST['taille_texte_date_edition'])) { $taille_texte_date_edition = ''; }
	else { if (isset($_GET['taille_texte_date_edition'])) { $taille_texte_date_edition = $_GET['taille_texte_date_edition']; } if (isset($_POST['taille_texte_date_edition'])) { $taille_texte_date_edition = $_POST['taille_texte_date_edition']; } }
	if (empty($_GET['taille_texte_matiere']) and empty($_POST['taille_texte_matiere'])) { $taille_texte_matiere = ''; }
	else { if (isset($_GET['taille_texte_matiere'])) { $taille_texte_matiere = $_GET['taille_texte_matiere']; } if (isset($_POST['taille_texte_matiere'])) { $taille_texte_matiere = $_POST['taille_texte_matiere']; } }

	$presentation_proflist=isset($_POST['presentation_proflist']) ? $_POST['presentation_proflist'] : '1';

	if (empty($_GET['active_moyenne_general']) and empty($_POST['active_moyenne_general'])) { $active_moyenne_general = ''; }
	else { if (isset($_GET['active_moyenne_general'])) { $active_moyenne_general = $_GET['active_moyenne_general']; } if (isset($_POST['active_moyenne_general'])) { $active_moyenne_general = $_POST['active_moyenne_general']; } }
	if (empty($_GET['titre_bloc_avis_conseil']) and empty($_POST['titre_bloc_avis_conseil'])) { $titre_bloc_avis_conseil = ''; }
	else { if (isset($_GET['titre_bloc_avis_conseil'])) { $titre_bloc_avis_conseil = $_GET['titre_bloc_avis_conseil']; } if (isset($_POST['titre_bloc_avis_conseil'])) { $titre_bloc_avis_conseil = $_POST['titre_bloc_avis_conseil']; } }
	if (empty($_GET['taille_titre_bloc_avis_conseil']) and empty($_POST['taille_titre_bloc_avis_conseil'])) { $taille_titre_bloc_avis_conseil = ''; }
	else { if (isset($_GET['taille_titre_bloc_avis_conseil'])) { $taille_titre_bloc_avis_conseil = $_GET['taille_titre_bloc_avis_conseil']; } if (isset($_POST['taille_titre_bloc_avis_conseil'])) { $taille_titre_bloc_avis_conseil = $_POST['taille_titre_bloc_avis_conseil']; } }
	if (empty($_GET['taille_profprincipal_bloc_avis_conseil']) and empty($_POST['taille_profprincipal_bloc_avis_conseil'])) { $taille_profprincipal_bloc_avis_conseil = ''; }
	else { if (isset($_GET['taille_profprincipal_bloc_avis_conseil'])) { $taille_profprincipal_bloc_avis_conseil = $_GET['taille_profprincipal_bloc_avis_conseil']; } if (isset($_POST['taille_profprincipal_bloc_avis_conseil'])) { $taille_profprincipal_bloc_avis_conseil = $_POST['taille_profprincipal_bloc_avis_conseil']; } }


	if (empty($_GET['afficher_tous_profprincipaux']) and empty($_POST['afficher_tous_profprincipaux'])) { $afficher_tous_profprincipaux = ''; }
	else {
		if (isset($_GET['afficher_tous_profprincipaux'])) { $afficher_tous_profprincipaux = $_GET['afficher_tous_profprincipaux']; }
		if (isset($_POST['afficher_tous_profprincipaux'])) { $afficher_tous_profprincipaux = $_POST['afficher_tous_profprincipaux']; }
	}

	if (empty($_GET['affiche_fonction_chef']) and empty($_POST['affiche_fonction_chef'])) { $affiche_fonction_chef = ''; }
	else { if (isset($_GET['affiche_fonction_chef'])) { $affiche_fonction_chef = $_GET['affiche_fonction_chef']; } if (isset($_POST['affiche_fonction_chef'])) { $affiche_fonction_chef = $_POST['affiche_fonction_chef']; } }
	if (empty($_GET['taille_texte_fonction_chef']) and empty($_POST['taille_texte_fonction_chef'])) { $taille_texte_fonction_chef = ''; }
	else { if (isset($_GET['taille_texte_fonction_chef'])) { $taille_texte_fonction_chef = $_GET['taille_texte_fonction_chef']; } if (isset($_POST['taille_texte_fonction_chef'])) { $taille_texte_fonction_chef = $_POST['taille_texte_fonction_chef']; } }
	if (empty($_GET['taille_texte_identitee_chef']) and empty($_POST['taille_texte_identitee_chef'])) { $taille_texte_identitee_chef = ''; }
	else { if (isset($_GET['taille_texte_identitee_chef'])) { $taille_texte_identitee_chef = $_GET['taille_texte_identitee_chef']; } if (isset($_POST['taille_texte_identitee_chef'])) { $taille_texte_identitee_chef = $_POST['taille_texte_identitee_chef']; } }
	if (empty($_GET['tel_image']) and empty($_POST['tel_image'])) { $tel_image = ''; }
	else { if (isset($_GET['tel_image'])) { $tel_image = $_GET['tel_image']; } if (isset($_POST['tel_image'])) { $tel_image = $_POST['tel_image']; } }
	if (empty($_GET['tel_texte']) and empty($_POST['tel_texte'])) { $tel_texte = ''; }
	else { if (isset($_GET['tel_texte'])) { $tel_texte = $_GET['tel_texte']; } if (isset($_POST['tel_texte'])) { $tel_texte = $_POST['tel_texte']; } }

	if (empty($_GET['fax_image']) and empty($_POST['fax_image'])) { $fax_image = ''; }
	else { if (isset($_GET['fax_image'])) { $fax_image = $_GET['fax_image']; } if (isset($_POST['fax_image'])) { $fax_image = $_POST['fax_image']; } }
	if (empty($_GET['fax_texte']) and empty($_POST['fax_texte'])) { $fax_texte = ''; }
	else { if (isset($_GET['fax_texte'])) { $fax_texte = $_GET['fax_texte']; } if (isset($_POST['fax_texte'])) { $fax_texte = $_POST['fax_texte']; } }

	if (empty($_GET['courrier_image']) and empty($_POST['courrier_image'])) { $courrier_image = ''; }
	else { if (isset($_GET['courrier_image'])) { $courrier_image = $_GET['courrier_image']; } if (isset($_POST['courrier_image'])) { $courrier_image = $_POST['courrier_image']; } }
	if (empty($_GET['courrier_texte']) and empty($_POST['courrier_texte'])) { $courrier_texte = ''; }
	else { if (isset($_GET['courrier_texte'])) { $courrier_texte = $_GET['courrier_texte']; } if (isset($_POST['courrier_texte'])) { $courrier_texte = $_POST['courrier_texte']; } }
	if (empty($_GET['largeur_bloc_eleve']) and empty($_POST['largeur_bloc_eleve'])) { $largeur_bloc_eleve = ''; }
	else { if (isset($_GET['largeur_bloc_eleve'])) { $largeur_bloc_eleve = $_GET['largeur_bloc_eleve']; } if (isset($_POST['largeur_bloc_eleve'])) { $largeur_bloc_eleve = $_POST['largeur_bloc_eleve']; } }
	if (empty($_GET['hauteur_bloc_eleve']) and empty($_POST['hauteur_bloc_eleve'])) { $hauteur_bloc_eleve = ''; }
	else { if (isset($_GET['hauteur_bloc_eleve'])) { $hauteur_bloc_eleve = $_GET['hauteur_bloc_eleve']; } if (isset($_POST['hauteur_bloc_eleve'])) { $hauteur_bloc_eleve = $_POST['hauteur_bloc_eleve']; } }
	if (empty($_GET['largeur_bloc_adresse']) and empty($_POST['largeur_bloc_adresse'])) { $largeur_bloc_adresse = ''; }
	else { if (isset($_GET['largeur_bloc_adresse'])) { $largeur_bloc_adresse = $_GET['largeur_bloc_adresse']; } if (isset($_POST['largeur_bloc_adresse'])) { $largeur_bloc_adresse = $_POST['largeur_bloc_adresse']; } }
	if (empty($_GET['hauteur_bloc_adresse']) and empty($_POST['hauteur_bloc_adresse'])) { $hauteur_bloc_adresse = ''; }
	else { if (isset($_GET['hauteur_bloc_adresse'])) { $hauteur_bloc_adresse = $_GET['hauteur_bloc_adresse']; } if (isset($_POST['hauteur_bloc_adresse'])) { $hauteur_bloc_adresse = $_POST['hauteur_bloc_adresse']; } }
	if (empty($_GET['largeur_bloc_datation']) and empty($_POST['largeur_bloc_datation'])) { $largeur_bloc_datation = ''; }
	else { if (isset($_GET['largeur_bloc_datation'])) { $largeur_bloc_datation = $_GET['largeur_bloc_datation']; } if (isset($_POST['largeur_bloc_datation'])) { $largeur_bloc_datation = $_POST['largeur_bloc_datation']; } }
	if (empty($_GET['hauteur_bloc_datation']) and empty($_POST['hauteur_bloc_datation'])) { $hauteur_bloc_datation = ''; }
	else { if (isset($_GET['hauteur_bloc_datation'])) { $hauteur_bloc_datation = $_GET['hauteur_bloc_datation']; } if (isset($_POST['hauteur_bloc_datation'])) { $hauteur_bloc_datation = $_POST['hauteur_bloc_datation']; } }
	if (empty($_GET['taille_texte_classe']) and empty($_POST['taille_texte_classe'])) { $taille_texte_classe = ''; }
	else { if (isset($_GET['taille_texte_classe'])) { $taille_texte_classe = $_GET['taille_texte_classe']; } if (isset($_POST['taille_texte_classe'])) { $taille_texte_classe = $_POST['taille_texte_classe']; } }
	if (empty($_GET['type_texte_classe']) and empty($_POST['type_texte_classe'])) { $type_texte_classe = ''; }
	else { if (isset($_GET['type_texte_classe'])) { $type_texte_classe = $_GET['type_texte_classe']; } if (isset($_POST['type_texte_classe'])) { $type_texte_classe = $_POST['type_texte_classe']; } }
	if (empty($_GET['taille_texte_annee']) and empty($_POST['taille_texte_annee'])) { $taille_texte_annee = ''; }
	else { if (isset($_GET['taille_texte_annee'])) { $taille_texte_annee = $_GET['taille_texte_annee']; } if (isset($_POST['taille_texte_annee'])) { $taille_texte_annee = $_POST['taille_texte_annee']; } }
	if (empty($_GET['type_texte_annee']) and empty($_POST['type_texte_annee'])) { $type_texte_annee = ''; }
	else { if (isset($_GET['type_texte_annee'])) { $type_texte_annee = $_GET['type_texte_annee']; } if (isset($_POST['type_texte_annee'])) { $type_texte_annee = $_POST['type_texte_annee']; } }
	if (empty($_GET['taille_texte_periode']) and empty($_POST['taille_texte_periode'])) { $taille_texte_periode = ''; }
	else { if (isset($_GET['taille_texte_periode'])) { $taille_texte_periode = $_GET['taille_texte_periode']; } if (isset($_POST['taille_texte_periode'])) { $taille_texte_periode = $_POST['taille_texte_periode']; } }
	if (empty($_GET['type_texte_periode']) and empty($_POST['type_texte_periode'])) { $type_texte_periode = ''; }
	else { if (isset($_GET['type_texte_periode'])) { $type_texte_periode = $_GET['type_texte_periode']; } if (isset($_POST['type_texte_periode'])) { $type_texte_periode = $_POST['type_texte_periode']; } }
	if (empty($_GET['taille_texte_categorie_cote']) and empty($_POST['taille_texte_categorie_cote'])) { $taille_texte_categorie_cote = ''; }
	else { if (isset($_GET['taille_texte_categorie_cote'])) { $taille_texte_categorie_cote = $_GET['taille_texte_categorie_cote']; } if (isset($_POST['taille_texte_categorie_cote'])) { $taille_texte_categorie_cote = $_POST['taille_texte_categorie_cote']; } }
	if (empty($_GET['taille_texte_categorie']) and empty($_POST['taille_texte_categorie'])) { $taille_texte_categorie = ''; }
	else { if (isset($_GET['taille_texte_categorie'])) { $taille_texte_categorie = $_GET['taille_texte_categorie']; } if (isset($_POST['taille_texte_categorie'])) { $taille_texte_categorie = $_POST['taille_texte_categorie']; } }
	if (empty($_GET['type_texte_date_datation']) and empty($_POST['type_texte_date_datation'])) { $type_texte_date_datation = ''; }
	else { if (isset($_GET['type_texte_date_datation'])) { $type_texte_date_datation = $_GET['type_texte_date_datation']; } if (isset($_POST['type_texte_date_datation'])) { $type_texte_date_datation = $_POST['type_texte_date_datation']; } }
	if (empty($_GET['cadre_adresse']) and empty($_POST['cadre_adresse'])) { $cadre_adresse = ''; }
	else { if (isset($_GET['cadre_adresse'])) { $cadre_adresse = $_GET['cadre_adresse']; } if (isset($_POST['cadre_adresse'])) { $cadre_adresse = $_POST['cadre_adresse']; } }
	if (empty($_GET['centrage_logo']) and empty($_POST['centrage_logo'])) { $centrage_logo = ''; }
	else { if (isset($_GET['centrage_logo'])) { $centrage_logo = $_GET['centrage_logo']; } if (isset($_POST['centrage_logo'])) { $centrage_logo = $_POST['centrage_logo']; } }
	if (empty($_GET['Y_centre_logo']) and empty($_POST['Y_centre_logo'])) { $Y_centre_logo = ''; }
	else { if (isset($_GET['Y_centre_logo'])) { $Y_centre_logo = $_GET['Y_centre_logo']; } if (isset($_POST['Y_centre_logo'])) { $Y_centre_logo = $_POST['Y_centre_logo']; } }
	if (empty($_GET['ajout_cadre_blanc_photo']) and empty($_POST['ajout_cadre_blanc_photo'])) { $ajout_cadre_blanc_photo = ''; }
	else { if (isset($_GET['ajout_cadre_blanc_photo'])) { $ajout_cadre_blanc_photo = $_GET['ajout_cadre_blanc_photo']; } if (isset($_POST['ajout_cadre_blanc_photo'])) { $ajout_cadre_blanc_photo = $_POST['ajout_cadre_blanc_photo']; } }
	if (empty($_GET['affiche_moyenne_mini_general']) and empty($_POST['affiche_moyenne_mini_general'])) { $affiche_moyenne_mini_general = ''; }
	else { if (isset($_GET['affiche_moyenne_mini_general'])) { $affiche_moyenne_mini_general = $_GET['affiche_moyenne_mini_general']; } if (isset($_POST['affiche_moyenne_mini_general'])) { $affiche_moyenne_mini_general = $_POST['affiche_moyenne_mini_general']; } }

	if (empty($_GET['affiche_moyenne_maxi_general']) and empty($_POST['affiche_moyenne_maxi_general'])) { $affiche_moyenne_maxi_general = ''; }
	else { if (isset($_GET['affiche_moyenne_maxi_general'])) { $affiche_moyenne_maxi_general = $_GET['affiche_moyenne_maxi_general']; } if (isset($_POST['affiche_moyenne_maxi_general'])) { $affiche_moyenne_maxi_general = $_POST['affiche_moyenne_maxi_general']; } }

	if (empty($_GET['affiche_totalpoints_sur_totalcoefs']) and empty($_POST['affiche_totalpoints_sur_totalcoefs'])) { $affiche_totalpoints_sur_totalcoefs=0; }
	else {
		if (isset($_GET['affiche_totalpoints_sur_totalcoefs'])) { $affiche_totalpoints_sur_totalcoefs=$_GET['affiche_totalpoints_sur_totalcoefs']; }
		if (isset($_POST['affiche_totalpoints_sur_totalcoefs'])) { $affiche_totalpoints_sur_totalcoefs=$_POST['affiche_totalpoints_sur_totalcoefs']; }
	}
	//echo "\$affiche_totalpoints_sur_totalcoefs=$affiche_totalpoints_sur_totalcoefs<br />";

	if (empty($_GET['affiche_date_edition']) and empty($_POST['affiche_date_edition'])) { $affiche_date_edition = ''; }
	else { if (isset($_GET['affiche_date_edition'])) { $affiche_date_edition = $_GET['affiche_date_edition']; } if (isset($_POST['affiche_date_edition'])) { $affiche_date_edition = $_POST['affiche_date_edition']; } }
	if (empty($_GET['affiche_ine']) and empty($_POST['affiche_ine'])) { $affiche_ine = ''; }
	else { if (isset($_GET['affiche_ine'])) { $affiche_ine = $_GET['affiche_ine']; } if (isset($_POST['affiche_ine'])) { $affiche_ine = $_POST['affiche_ine']; } }

	if (empty($_GET['affiche_moyenne_general_coef_1']) and empty($_POST['affiche_moyenne_general_coef_1'])) { $affiche_moyenne_general_coef_1 = ''; }
	else { if (isset($_GET['affiche_moyenne_general_coef_1'])) { $affiche_moyenne_general_coef_1 = $_GET['affiche_moyenne_general_coef_1']; } if (isset($_POST['affiche_moyenne_general_coef_1'])) { $affiche_moyenne_general_coef_1 = $_POST['affiche_moyenne_general_coef_1']; } }

	if (empty($_GET['affiche_numero_responsable']) and empty($_POST['affiche_numero_responsable'])) { $affiche_numero_responsable = ''; }
	else { if (isset($_GET['affiche_numero_responsable'])) { $affiche_numero_responsable = $_GET['affiche_numero_responsable']; } if (isset($_POST['affiche_numero_responsable'])) { $affiche_numero_responsable = $_POST['affiche_numero_responsable']; } }

	$signature_img=isset($_POST['signature_img']) ? $_POST['signature_img'] : (isset($_GET['signature_img']) ? $_GET['signature_img'] : "");

	/*
	if (empty($_GET['adresse_resp_fontsize_ligne_1']) and empty($_POST['adresse_resp_fontsize_ligne_1'])) { $adresse_resp_fontsize_ligne_1 = 12; }
	else { if (isset($_GET['adresse_resp_fontsize_ligne_1'])) { $adresse_resp_fontsize_ligne_1 = $_GET['adresse_resp_fontsize_ligne_1']; } if (isset($_POST['adresse_resp_fontsize_ligne_1'])) { $adresse_resp_fontsize_ligne_1 = $_POST['adresse_resp_fontsize_ligne_1']; } }
	if((!preg_match("/^[0-9]*$/", $adresse_resp_fontsize_ligne_1))||($adresse_resp_fontsize_ligne_1<=0)) {
		$adresse_resp_fontsize_ligne_1=12;
	}
	*/
	if (empty($_GET['adresse_resp_fontsize']) and empty($_POST['adresse_resp_fontsize'])) { $adresse_resp_fontsize = 12; }
	else { if (isset($_GET['adresse_resp_fontsize'])) { $adresse_resp_fontsize = $_GET['adresse_resp_fontsize']; } if (isset($_POST['adresse_resp_fontsize'])) { $adresse_resp_fontsize = $_POST['adresse_resp_fontsize']; } }
	if((!preg_match("/^[0-9]*$/", $adresse_resp_fontsize))||($adresse_resp_fontsize<=0)) {
		$adresse_resp_fontsize=10;
	}

// fin Christian
//===================================================

//==============================
// Initialisation d'un tableau des champs de model_bulletin
include('bulletin_pdf.inc.php');
// Pour ajouter un paramètre, il faut ajouter la case à cocher dans la présente page (param_bull_pdf.php), mais il faut aussi déclarer le champ correspondant et sa valeur par défaut dans la page bulletin_pdf.inc.php
//==============================

//===================================================
// début de la validation ajouter/modifier/supprimer des modèles
if(!empty($valide_modif_model))
{
	check_token();
	if($action_model==='ajouter') {
		$id_model_bulletin=get_max_id_model_bulletin();
		$id_model_bulletin++;
		for($i=0;$i<count($champ_bull_pdf);$i++) {
			$nom=$champ_bull_pdf[$i];
			if(isset($$nom)) {
				$valeur=$$nom;
				if($valeur=='') {
					if($type_champ_pdf["$nom"]!="texte") {
						$valeur=0;
					}
				}

				//$sql="INSERT INTO modele_bulletin SET id_model_bulletin='$id_model_bulletin', nom='$nom', valeur='".$$nom."';";
				$sql="INSERT INTO modele_bulletin SET id_model_bulletin='$id_model_bulletin', nom='$nom', valeur='".$valeur."';";
				//echo "$sql<br />\n";
				$insert=mysql_query($sql);
			}
			else {
				// Normalement, cela ne devrait pas arriver si on récupère correctement les valeurs soumises du formulaire.
				// Il faudrait insérer une valeur par défaut.
				// Prendre celle du modèle Standard?
			}
		}
	}

	if($action_model==='modifier') {
		for($i=0;$i<count($champ_bull_pdf);$i++) {
			$nom=$champ_bull_pdf[$i];
			if(isset($$nom)) {
				$valeur=$$nom;
				if($valeur=='') {
					if($type_champ_pdf["$nom"]!="texte") {
						$valeur=0;
					}
				}

				$sql="SELECT 1=1 FROM modele_bulletin WHERE id_model_bulletin='$id_model_bulletin' AND nom='$nom';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0) {
					//$sql="INSERT INTO modele_bulletin SET id_model_bulletin='$id_model_bulletin', nom='$nom', valeur='".$$nom."';";
					$sql="INSERT INTO modele_bulletin SET id_model_bulletin='$id_model_bulletin', nom='$nom', valeur='".$valeur."';";
					//echo "$sql<br />\n";
					$insert=mysql_query($sql);
				}
				else {
					//$sql="UPDATE modele_bulletin SET valeur='".$$nom."' WHERE id_model_bulletin='$id_model_bulletin' AND nom='$nom';";
					$sql="UPDATE modele_bulletin SET valeur='".$valeur."' WHERE id_model_bulletin='$id_model_bulletin' AND nom='$nom';";
					//echo "$sql<br />\n";
					$update=mysql_query($sql);
				}
			}
			/*
			else {
				echo "Pas de valeur pour $nom<br />";
			}
			*/
		}
	}

	if($id_model_bulletin!='1') {
		if($action_model==='supprimer') {
			$requete_model="DELETE FROM ".$prefix_base."modele_bulletin WHERE id_model_bulletin ='".$id_model_bulletin."';";

			//AJOUT ERIC Si on supprime un modèle, s'il est utilisé pour une classe on réinitialise pour la classe la valeur à NULL du champs modele_bulletin_pdf
			$requete_classe="UPDATE classes SET modele_bulletin_pdf=NULL WHERE (modele_bulletin_pdf='$id_model_bulletin')";
			//echo $requete_classe;
			mysql_query($requete_classe) or die('Erreur SQL !'.$requete_classe.'<br>'.mysql_error());

			mysql_query($requete_model) or die('Erreur SQL !'.$requete_model.'<br>'.mysql_error());
		}
	}
	//mysql_query($requete_model) or die('Erreur SQL !'.$requete_model.'<br>'.mysql_error());
}
// fin ajouter/modifier/supprimer des modèles
//===================================================


//===================================================
// DEBUT import de modèle de bulletin pdf par fichier csv
if ( isset($action) and $action === 'importmodelcsv' ) {
	check_token();
	if($_FILES['fichier']['type'] != "")
	{
			$fichiercsv = isset($_FILES["fichier"]) ? $_FILES["fichier"] : NULL;
		if (!isset($fichiercsv['tmp_name']) or ($fichiercsv['tmp_name'] === '')) {
			$msg = "Erreur de téléchargement niveau 1.";
		} else if (!file_exists($fichiercsv['tmp_name'])) {
				$msg = "Erreur de téléchargement niveau 2.";
		} else if ((!preg_match('/csv$/',$fichiercsv['name'])) and $fichiercsv['type'] === "application/x-csv"){
				$msg = "Erreur : seuls les fichiers ayant l'extension .jpg sont autorisés.";
		} else {

			if(!isset($msg)) {$msg="";}

			$fp = fopen($fichiercsv['tmp_name'],"r");

			$ligne = fgets($fp,4096);      // je lis la ligne
			if($ligne!="") {
				// On remplit un tableau des champs
				//$tab_champs_csv[]=explode(";",$ligne);
				$tab_champs_csv=explode(";",$ligne);

				$indice=-1;
				// Recherche de l'indice du champ id_model_bulletin
				for($i=0;$i<count($tab_champs_csv);$i++) {
					//echo "\$tab_champs_csv[$i]=".$tab_champs_csv[$i];
					if($tab_champs_csv[$i]=='id_model_bulletin') {
						$indice=$i;
						//echo " TROUVé: \$indice=$indice";
						break;
					}
					//echo "<br />";
				}

				$indice_nom_modele=-1;
				// Recherche de l'indice du champ nom du modèle
				for($i=0;$i<count($tab_champs_csv);$i++) {
					//echo "\$tab_champs_csv[$i]=".$tab_champs_csv[$i];
					if($tab_champs_csv[$i]=='nom_model_bulletin') {
						$indice_nom_modele=$i;
						//echo " TROUVé: \$indice_nom_modele=$indice_nom_modele";
						break;
					}
					//echo "<br />";
				}

				if($indice!=-1) {
					// On importe ligne par ligne dans un tableau
					while (!feof($fp)) //Jusqu'a la fin du fichier
					{

						$ligne = fgets($fp,4096);      // je lis la ligne
						unset($tab_valeurs_csv);
						//$tab_valeurs_csv[]=explode(";",$ligne);
						$tab_valeurs_csv=explode(";",$ligne);

						// Si $tab_valeurs_csv[$indice] est vide, il faut tester le nom du modèle pour retrouver l'id_model_bulletin ou en affecter un nouveau
						// Normalement, si on repart d'un export fait avec la version modifiée de export_modele_pdf.php, les id_model_bulletin sont bien exportés aussi.

						if($tab_valeurs_csv[$indice]=="") {
							$sql="SELECT DISTINCT id_model_bulletin FROM modele_bulletin WHERE nom='nom_model_bulletin' AND valeur='".$tab_valeurs_csv[$indice_nom_modele]."';";
							//echo "$sql<br />";
							$res_nom_model=mysql_query($sql);

							if(mysql_num_rows($res_nom_model)>0) {
								$tmp_lig_nom_model=mysql_fetch_object($res_nom_model);
								$tab_valeurs_csv[$indice]=$tmp_lig_nom_model->id_model_bulletin;
							}
							else {
								$sql="SELECT MAX(id_model_bulletin) AS max_id_model_bulletin FROM modele_bulletin;";
								//echo "$sql<br />";
								$res_max=mysql_query($sql);

								if(mysql_num_rows($res_max)>0) {
									$tmp_lig_max=mysql_fetch_object($res_max);

									$tab_valeurs_csv[$indice]=$tmp_lig_max->max_id_model_bulletin+1;
								}
								else {
									// Ca ne devrait pas arriver à moins d'avoir supprimé tous les modèles.
									$tab_valeurs_csv[$indice]=1;
								}
							}
						}

						if($tab_valeurs_csv[$indice_nom_modele]!="") {
							$sql="DELETE FROM modele_bulletin WHERE id_model_bulletin='".$tab_valeurs_csv[$indice]."';";
							//echo "$sql<br />";
							$nettoyage=mysql_query($sql);

							for($i=0;$i<count($tab_champs_csv);$i++) {
								if($i!=$indice) {
									$sql="INSERT modele_bulletin SET id_model_bulletin='".$tab_valeurs_csv[$indice]."', nom='".$tab_champs_csv[$i]."', valeur='".$tab_valeurs_csv[$i]."';";
									//echo "$sql<br />";
									$insert=mysql_query($sql);
								}
							}
						}
					}

					/*
					// Parcourir $champ_bull_pdf pour ne pas oublier de champ
					for($i=0;$i<count($tab_champs_csv);$i++) {

					}
					*/
				}
				else {
					$msg="Erreur: Le champ 'id_model_bulletin' n'a pas été trouvé dans le fichier CSV.";
				}
			}
			fclose($fp);
		}
	}
}
// FIN import de modèle de bulletin pdf par fichier csv
//===================================================


if (($reg_ok == 'yes') and (isset($_POST['ok']))) {
	$msg = "Enregistrement réussi !";
}

//**************** EN-TETE *********************
$titre_page = "Paramètres de configuration des bulletins scolaires PDF";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

// Pour afficher les variables transmises en GET/POST/SERVER/SESSION
//debug_var();

?>
<script type="text/javascript">
<!--
function CocheCheckbox() {

	nbParams = CocheCheckbox.arguments.length;

	for (var i=0;i<nbParams-1;i++) {

		theElement = CocheCheckbox.arguments[i];
		formulaire = CocheCheckbox.arguments[nbParams-1];

		if (document.forms[formulaire].elements[theElement])
			document.forms[formulaire].elements[theElement].checked = true;
	}
}

function DecocheCheckbox() {

	nbParams = DecocheCheckbox.arguments.length;

	for (var i=0;i<nbParams-1;i++) {

		theElement = DecocheCheckbox.arguments[i];
		formulaire = DecocheCheckbox.arguments[nbParams-1];

		if (document.forms[formulaire].elements[theElement])
			document.forms[formulaire].elements[theElement].checked = false;
	}
}
//-->
</script>

<?php


	if ((($_SESSION['statut']=='professeur') AND ((getSettingValue("GepiProfImprBul")!='yes') OR ((getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")!='yes')))) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")!='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")!='yes')))
	{
		die("Droits insuffisants pour effectuer cette opération");
	}

	echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>\n";
	//echo " | <a href=\"./index.php?format=pdf\"> Impression des bulletins PDF</a>\n";
	echo " | <a href=\"./bull_index.php\"> Impression des bulletins</a>\n";
	echo " | <a href=\"./param_bull.php\"> Paramètres d'impression des bulletins HTML</a>\n";
	//echo "</p>\n";
	//echo "<br /><br />\n";

	//=========================================================
	// $action_model peut valoir: ajouter/modifier/supprimer
	if((empty($action_model) or !empty($valide_modif_model))) //affiche la liste des modèles
	{

		echo "</p>\n";
		echo "<br /><br />\n";

		$sql="SHOW TABLES LIKE 'modele_bulletin';";
		$test_modele_bulletin=mysql_query($sql);
		if(mysql_num_rows($test_modele_bulletin)==0) {
			echo "<p style='color:red'>La table 'modele_bulletin' n'existe pas.</p>\n";

			if($_SESSION['statut']=='administrateur') {
				echo "<p>Forcez une <a href='../utilitaires/maj.php'>mise à jour de la base</a> et si cela ne suffit pas, <a href='test_modele_bull.php'>testez les tables modèles de bulletins PDF</a>.</p>\n";
			}
			else {
				echo "<p>Contactez l'administrateur pour qu'il effectuer une mise à jour de la base et peut-être un test des tables modèles PDF.</p>\n";
			}

			$titre="La table 'modele_bulletin' n'existe pas : ".strftime("%d/%m/%Y à %H:%M:%S");
			$texte="Forcez une <a href='$gepiPath/utilitaires/maj.php'>mise à jour de la base</a> et si cela ne suffit pas, <a href='$gepiPath/bulletin/test_modele_bull.php'>testez les tables modèles de bulletins PDF</a>.";
			$destinataire="administrateur";
			$mode="statut";
			$id_info=enregistre_infos_actions($titre,$texte,$destinataire,$mode);

			require("../lib/footer.inc.php");
			die();
		}

		echo "<center>
		<form name ='form3' method='post' action='export_modele_pdf.php'>\n";
		echo add_token_field();
		echo "<table style='text-align: left; width: 400px; border: 1px solid #74748F;' border='0' cellpadding='1' cellspacing='1' summary='Tableau des modèles existants'>
		<tbody>
		<tr>
			<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 100%;' colspan='4' rowspan='1'><a href='".$_SERVER['PHP_SELF']."?modele=aff&amp;action_model=ajouter'>Ajouter un nouveau modèle</a></td>
		</tr>
		<tr>
			<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 12px; background: #333333; font: normal 10pt Arial; color: #E0EDF1;'></td>
			<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 50%; background: #333333; font: normal 10pt Arial; color: #E0EDF1;'>Modèle</td>
			<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 25%; background: #333333; font: normal 10pt Arial; color: #E0EDF1;'>Modifier</td>
			<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 25%; background: #333333; font: normal 10pt Arial; color: #E0EDF1;'>Supprimer</td>
		</tr>\n";

		$i = '1';
		$nb_modele = '0'; $varcoche = '';

		//$requete_model = mysql_query('SELECT id_model_bulletin, nom_model_bulletin FROM '.$prefix_base.'model_bulletin');
		$requete_model = mysql_query("SELECT id_model_bulletin, valeur FROM ".$prefix_base."modele_bulletin WHERE nom='nom_model_bulletin' ORDER BY id_model_bulletin;");
        if(mysql_num_rows($requete_model)==0) {
            $message_alerte="<p style='text-align:center; color:red;'>Il semble qu'aucun modèle ne soit défini.<br />Ce n'est pas normal.<br />";
            if($_SESSION['login']=='administrateur') {
                $message_alerte.="Vous devriez effectuer/forcer une <a href='../utilitaires/maj.php'>mise à jour de la base</a> pour corriger.<br />Prenez tout de même soin de vérifier que personne d'autre que vous n'est connecté.\n";
            }
            else {
                $message_alerte.="Contactez l'administrateur pour qu'il effectue une mise à jour de la base.\n";
            }
            $message_alerte.="</p>\n";
        }
        else {
            while($data_model = mysql_fetch_array($requete_model)) {
                if ($i === '1') { $i = '2'; $couleur_cellule = '#CCCCCC'; } else { $couleur_cellule = '#DEDEDE'; $i = '1'; }

                echo "<tr>\n";

                echo "<td style='vertical-align: top; white-space: nowrap; text-align: left; width: 12px; background: $couleur_cellule;'>\n";
                //echo "<input name='selection[$nb_modele]' id='sel$nb_modele' value='1' type='checkbox' />\n";
                echo "<input name='selection[$nb_modele]' id='sel$nb_modele' value='".$data_model['id_model_bulletin']."' type='checkbox' />\n";
                echo "<input name='id_model_bulletin[$nb_modele]' value='".$data_model['id_model_bulletin']."' type='hidden' />\n";

                $varcoche = $varcoche."'sel".$nb_modele."',";

                echo "</td>\n";

                echo "<td style='vertical-align: top; white-space: nowrap; text-align: left; width: 50%; background: $couleur_cellule'>\n";
                //echo ucfirst($data_model['nom_model_bulletin']);
                echo ucfirst($data_model['valeur']);
                echo "</td>\n";

                echo "<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 25%; background: $couleur_cellule'>\n";
                echo "[<a href='".$_SERVER['PHP_SELF']."?modele=aff&amp;action_model=modifier&amp;modele_action=".$data_model['id_model_bulletin']."'>\n";
                echo "Modifier\n";
                echo "</a>]\n";
                echo "</td>\n";

                echo "<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 25%; background: $couleur_cellule;'>\n";
                if($data_model['id_model_bulletin']!='1') {
                    echo "[<a href='".$_SERVER['PHP_SELF']."?modele=aff&amp;action_model=supprimer&amp;modele_action=".$data_model['id_model_bulletin']."'>Supprimer</a>]";
                }
                else {
                    echo "&nbsp;";
                }
                echo "</td>\n";
                echo "</tr>\n";

                $nb_modele = $nb_modele + 1;
            }
        }
		$varcoche = $varcoche."'form3'";

		echo "<tr>\n";
		echo "<td style='vertical-align: center; white-space: nowrap; text-align: center; width: 100%;' colspan='4' rowspan='1'><a href='".$_SERVER['PHP_SELF']."?modele=aff&amp;action_model=ajouter'>Ajouter un nouveau modèle</a><br /></td>\n";
		echo "</tr>\n";
		echo "</tbody>\n";
		echo "</table>\n";

		echo "<a href=\"javascript:CocheCheckbox($varcoche)\">Cocher</a> | <a href=\"javascript:DecocheCheckbox($varcoche)\">Décocher</a>\n";
		echo "<input type='submit' value='Exporter' style='border: 0px; color: #0000AA; text-decoration: none;' />\n";
		echo "<span style='background : #FFFFF1; padding-left: 2px;'><a href='".$_SERVER['PHP_SELF']."?action=import' class='submit'>Importer</a></span>\n";
		echo "</form>\n";

		if ( $action === 'import' ) {
			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='importfichier'>\n";
			echo add_token_field();
			echo "<input name='fichier' type='file' />\n";
			echo "<input type='hidden' name='MAX_FILE_SIZE' value='150000' />\n";
			echo "<input type='hidden' name='action' value='importmodelcsv' />\n";
			echo "<input type='submit' value='Importer' />\n";
			echo "</form>\n";
		}

		echo "</center>\n";

        if(isset($message_alerte)) {
            echo $message_alerte;
        }

		echo "<br />\n";
		echo "<br />\n";
		echo "<hr />\n";


		//ERIC
		$nb_ligne = 1;
		$bgcolor = "#DEDEDE";
		echo "<form name=\"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" style=\"width: 100%\">\n";
		echo add_token_field();
		echo "<h3>Options gestion des modèles d'impression PDF</h3>\n";
		echo "<table cellpadding=\"8\" cellspacing=\"0\" width=\"100%\" border=\"0\" summary=\"Tableau des options d'impression par classe\">\n";

		echo "<tr ";  if ($nb_ligne % 2) echo "bgcolor=".$bgcolor; echo " >\n"; $nb_ligne++;
		echo "<td style=\"font-variant: small-caps;\" width=\"80%\" >\n";
		echo "Interdire la sélection du modèle de bulletin lors de l'impression. Le modèle doit être défini dans les paramètres de chaque classe. <i>(<em>En cas d'absence de modèle, le modèle standard est utilisé.</em>)</i><br />\n";
		echo "</td>\n";
		echo "<td style=\"text-align: center;\">\n";
			echo "<input type=\"radio\" name=\"option_modele_bulletin\" value=\"1\" ";
			if (getSettingValue("option_modele_bulletin") == '1') {echo " checked=\"checked\"";}
			echo " />\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr ";  if ($nb_ligne % 2) echo "bgcolor=".$bgcolor; echo " >\n"; $nb_ligne++;
		echo "<td style=\"font-variant: small-caps;\" width=\"80%\" >\n";
		echo "Le modèle utilisé par défaut est celui défini dans les paramètres de la classe. Un autre modèle pourra être choisi lors de l'impression des bulletins. Il s'appliquera à toutes les classes sélectionnées.<br />\n";
		echo "</td>\n";
		echo "<td style=\"text-align: center;\">\n";
			echo "<input type=\"radio\" name=\"option_modele_bulletin\" value=\"2\" ";
			if (getSettingValue("option_modele_bulletin") == '2') {echo " checked=\"checked\"";}
			echo " />\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr ";  if ($nb_ligne % 2) echo "bgcolor=".$bgcolor; echo " >\n"; $nb_ligne++;
		echo "<td style=\"font-variant: small-caps;\" width=\"80%\" >\n";
		echo "Le modèle devra être choisi au moment de l'impression indépendamment du modèle paramétré dans les paramètres de la classe. Il s'appliquera à toutes les classes sélectionnées.<br />\n";
		echo "</td>\n";
		echo "<td style=\"text-align: center;\">\n";
			echo "<input type=\"radio\" name=\"option_modele_bulletin\" value=\"3\" ";
			if (getSettingValue("option_modele_bulletin") == '3') {echo " checked=\"checked\"";}
			echo " />\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n<hr />\n";

		/*
		// Commenté parce que j'ai un soucis sur le bulletin PDF pour tout positionner.
		echo "<p>";
		echo "<input name='bull_pdf_INE_eleve' style='border: 1px solid #74748F;' type='checkbox' value='y'";
		if(getSettingValue('bull_pdf_INE_eleve')=='y') {
			echo " checked='checked'";
		}
		echo " />&nbsp;Afficher le numéro INE de l'élève sur le bulletin PDF.";
		echo "</p>\n";

		echo "<hr />\n";
		*/

		echo "<center><input type=\"submit\" name=\"ok\" value=\"Enregistrer\" style=\"font-variant: small-caps;\"/></center>";
		echo "</form>";

	}
	//=========================================================



	unset($nom_model_bulletin_ecrased);

	if($modele==='aff' and ($action_model==='ajouter' or $action_model==='modifier' or $action_model==='supprimer') and empty($valide_modif_model)) //affiche la liste des modèles
	{
		// $modele_action contient l'id_model_bulletin du modèle à modifier/supprimer
		if(empty($modele_action)) {
			// On est dans le cas d'un ajout
			$model_bulletin='';
		}
		else {
			$model_bulletin=$modele_action;
		}

		if($action_model==='ajouter' or $action_model==='modifier') {

			// Recherche de l'id_model_bulletin pour lequel on va rechercher les valeurs dans la table MySQL 'modele_bulletin'

			if($action_model==='ajouter') {
				$id_model_courant='';
				if($copie_model==='') {
					// On prend le modèle standard comme modèle pour le nouveau modèle ajouté
					$id_model_bulletin=1;
				}
				else {
					// On prend le modèle id_model_bulletin=$type_bulletin comme modèle pour le nouveau modèle ajouté
					// $type_bulletin est transmis par le formulaire "Copier les paramètres du modèle"
					$id_model_bulletin=$type_bulletin;

					// On récupère le nom du modèle qui avait peut-être déjà été saisi
					$nom_model_bulletin=isset($_POST['nom_model_bulletin']) ? $_POST['nom_model_bulletin'] : "Nouveau";
					// En effet $nom_model_bulletin va être par la suite écrasé par la récupération des valeurs enregistrées pour $id_model_bulletin

					// On met de côté le nom du nouveau modèle:
					$nom_model_bulletin_ecrased=$nom_model_bulletin;
				}
			}

			if($action_model==='modifier') {
				// On conserve le $model_bulletin=$model_action à modifier
				$id_model_courant=$model_bulletin;

				if($copie_model==='') {
					$id_model_bulletin=$model_bulletin;
				}
				else {
					$id_model_bulletin=$type_bulletin;

					$nom_model_bulletin_ecrased=$_POST['nom_model_bulletin'];
				}
			}

			// On récupère les valeurs du modèle $id_model_bulletin (que ce soit le modèle actuellement modifié ou celui qui sert de modèle pour une recopie)
			$sql="SELECT * FROM modele_bulletin WHERE id_model_bulletin='".$id_model_bulletin."';";
			//echo "$sql<br />\n";
			$res_modele=mysql_query($sql);
			while ($lig=mysql_fetch_object($res_modele)) {
				$nom=$lig->nom;
				$valeur=$lig->valeur;

				$$nom=$valeur;
				//echo "\$$nom=$valeur<br />";
				// La valeur de $nom_model_bulletin est écrasée ici par $valeur quand $nom='nom_model_bulletin'
			}


			if($action_model==='ajouter') {
				if($copie_model==='') {
					$nom_model_bulletin="Nouveau";
				}
				else {
					// On restaure le nom de modèle écrasé lors de la recopie
					$nom_model_bulletin=$nom_model_bulletin_ecrased;
				}
			}


			if ( $action_model==='modifier' and $copie_model != '' ) {
				/*
				// id du modèle
				$id_model_bulletin = $modele_action;
				//echo "\$id_model_bulletin=$id_model_bulletin<br />";
				// nom du modèle
				$nom_model_bulletin = $nom_model_bulletin;
				//echo "\$nom_model_bulletin=$nom_model_bulletin<br />";
				*/

				$nom_model_bulletin=$nom_model_bulletin_ecrased;
			}


			//echo " | <a href=\"./".$_SERVER['PHP_SELF']."\"> Paramètres d'impression des bulletins PDF</a>";
			echo " | <a href=\"".$_SERVER['PHP_SELF']."\"> Paramètres d'impression des bulletins PDF</a>";

			echo "</p>\n";
			echo "<br /><br />\n";

			//============================================
			echo "<form method='post' action='".$_SERVER['PHP_SELF']."?modele=aff' name='action_modele_copie_form'>\n";
			echo add_token_field();
			echo "<p>Modèle: <select tabindex='5' name='type_bulletin'>\n";

			// sélection des modèles des bulletins.
			//$requete_model = mysql_query('SELECT id_model_bulletin, nom_model_bulletin FROM '.$prefix_base.'model_bulletin ORDER BY '.$prefix_base.'model_bulletin.nom_model_bulletin ASC');
			$sql="SELECT id_model_bulletin, valeur FROM modele_bulletin WHERE nom='nom_model_bulletin' ORDER BY nom ASC";
			$requete_model = mysql_query($sql);
			while($donner_model = mysql_fetch_array($requete_model))
			{
				echo "<option value='".$donner_model['id_model_bulletin']."'";
				if(!empty($type_bulletin) and $type_bulletin===$donner_model['id_model_bulletin']) {
					echo " selected='selected'";
				}
				echo ">";
				//echo ucfirst($donner_model['nom_model_bulletin']);
				echo ucfirst($donner_model['valeur']);
				echo "</option>\n";
			}

			echo "</select>&nbsp;\n";

			if ( $action_model === 'modifier' ) {
				echo "<input type='hidden' name='modele_action' value='$modele_action' />\n";
				echo "<input type='hidden' name='nom_model_bulletin' value='$nom_model_bulletin' />\n";
			}

			echo "<input type='hidden' name='action_model' value='$action_model' />\n";
			echo "<input type='hidden' name='modele' value='$modele' />\n";
			echo "<input type='hidden' name='format' value='$format' />\n";
			echo "<input type='submit' id='copie_model' name='copie_model' value='Copier les paramètres de ce modèle' onClick=\"return confirm('Attention cette action va écraser votre sélection actuelle')\" />\n";
			echo "</form>\n";
			//============================================

			echo "<form method='post' action='".$_SERVER['PHP_SELF']."?modele=aff' name='action_modele_form'>\n";
			echo add_token_field();

			if(!isset($nom_model_bulletin)) {
				$nom_model_bulletin = 'Nouveau';
			}

			//echo "\$nom_model_bulletin=$nom_model_bulletin<br />";
			/*
			if(isset($nom_model_bulletin_ecrased)) {

				echo "<h2>Mise en page du modèle de bulletin ($nom_model_bulletin_ecrased)</h2>";

				if($id_model_bulletin!='1') {
					//echo "$nom_model_bulletin -";
					echo "Nom du modèle :&nbsp;";
					echo "<input name='nom_model_bulletin' size='22' style='border: 1px solid #74748F;' type='text' ";
					if(!empty($nom_model_bulletin_ecrased)) {
						echo "value=\"$nom_model_bulletin_ecrased\"";
					}
					echo " />";
				}
				else {
					echo "Nom du modèle: ".ucfirst($nom_model_bulletin_ecrased);
				}
			}
			else {
			*/
				echo "<h2>Mise en page du modèle de bulletin ($nom_model_bulletin)</h2>";

				//if($id_model_bulletin!='1') {
				//if(($id_model_bulletin!='1')||($nom_model_bulletin=='Nouveau')) {
				if(($id_model_courant!='1')||($nom_model_bulletin=='Nouveau')) {
					//echo "$nom_model_bulletin -";
					echo "Nom du modèle :&nbsp;";
					echo "<input name='nom_model_bulletin' size='22' style='border: 1px solid #74748F;' type='text' ";
					if(!empty($nom_model_bulletin)) {
						echo "value=\"$nom_model_bulletin\"";
					}
					echo " />";
				}
				else {
					// On devrait avoir ici: Modèle Standard avec id_model_bulletin=1
					echo "Nom du modèle: ".ucfirst($nom_model_bulletin);
					//echo "<input type='hidden' name='id_model_bulletin' value='$id_model_bulletin' />\n";
					echo "<input name='nom_model_bulletin' type='hidden' value=\"$nom_model_bulletin\" />\n";
				}

				echo "<input type='hidden' name='id_model_bulletin' value='$id_model_courant' />\n";
			//}

			echo "<br />\n";

			/*
			if($id_model_bulletin==='1') {
				echo "<input type='hidden' name='id_model_bulletin' value='$id_model_bulletin' />\n";
				echo "<input name='nom_model_bulletin' type='hidden' value=\"$nom_model_bulletin\" />\n";
			}
			*/

			?>
			Nom de la police de caractères&nbsp;<input name="caractere_utilse" size="10" style="border: 1px solid #74748F;" type="text" <?php if(!empty($caractere_utilse)) { ?>value="<?php echo $caractere_utilse; ?>" <?php } ?> />&nbsp;<span style="font-weight: bold; color: rgb(255, 0, 0);">*</span><br /><span style="font-style: italic; color: rgb(255, 0, 0);">* (Attention à ne modifier que si la police existe sur le serveur web voir avec l'administrateur de GEPI)</span><br />
			<table style="text-align: left; width: 100%; border: 1px solid #74748F;" border="0" cellpadding="2" cellspacing="2"  summary="Tableau des paramètres du modèle">
			<tbody>
			<tr>
				<td style="vertical-align: center; white-space: nowrap; text-align: center; width: 100%; background: #B3B7BF;" colspan="2" rowspan="1">
				<input type="submit" id="valide_modif_model" name="valide_modif_model" value="Valider le modèle" />
				</td>
			</tr>
			<tr>
			<td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;">
				<div style="font-weight: bold; background: #CFCFCF;">Cadre information établissement</div>

			<?php
				// AJOUT: boireaus 20081224
				// Afficher le nom de l'établissement
				echo "<input name='affiche_nom_etab' id='affiche_nom_etab' style='border: 1px solid #74748F;' type='checkbox' value='1' ";
				//if(!empty($affiche_nom_etab) and $affiche_nom_etab==='1') {
				if((!isset($affiche_nom_etab))||($affiche_nom_etab!='0')) {
					echo "checked='checked'";
				}
				echo "/>&nbsp;<label for='affiche_nom_etab' style='cursor: pointer;'>Afficher le nom de l'établissement</label><br />\n";

				// Afficher l'adresse de l'établissement
				echo "<input name='affiche_adresse_etab' id='affiche_adresse_etab' style='border: 1px solid #74748F;' type='checkbox' value='1' ";
				//if(!empty($affiche_adresse_etab) and $affiche_adresse_etab==='1') {
				if((!isset($affiche_adresse_etab))||($affiche_adresse_etab!='0')) {
					echo "checked='checked'";
				}
				echo "/>&nbsp;<label for='affiche_adresse_etab' style='cursor: pointer;'>Afficher l'adresse de l'établissement</label><br />\n";

			?>

			<input name="nom_etab_gras" id="nom_etab_gras" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($nom_etab_gras) and $nom_etab_gras==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="nom_etab_gras" style="cursor: pointer;">Nom de l'établissement en gras</label><br />

			<input name="affiche_filigrame" id="filigrame" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_filigrame) and $affiche_filigrame==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="filigrame" style="cursor: pointer;">Filigrane</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;<label for="text_fili" style="cursor: pointer;">texte du filigrane</label>&nbsp;<input name="texte_filigrame" id="text_fili" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($texte_filigrame)) { ?>value="<?php echo $texte_filigrame; ?>" <?php } ?> /><br />

			<input name="entente_tel" id="telephone" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($entente_tel) and $entente_tel==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="telephone" style="cursor: pointer;">Téléphone</label><br />
			&nbsp;&nbsp;&nbsp;Texte&nbsp;<input name="tel_texte" size="4" style="border: 1px solid #74748F;" type="text" <?php if(!empty($tel_texte)) { ?>value="<?php echo $tel_texte; ?>" <?php } ?> /> ou
			<input name="tel_image" id="tel_image_1" value="tel1" type="radio" <?php if(!empty($tel_image) and $tel_image==='tel1') { ?>checked="checked"<?php } ?> /><label for="tel_image_1" style="cursor: pointer;"><img src="../images/imabulle/tel1.jpg" style="width: 6.5px; height: 15.5px; border: 0px" alt="" title="" /></label>
			<input name="tel_image" id="tel_image_2" value="tel2" type="radio" <?php if(!empty($tel_image) and $tel_image==='tel2') { ?>checked="checked"<?php } ?> /><label for="tel_image_2" style="cursor: pointer;"><img src="../images/imabulle/tel2.jpg" style="width: 18.5px; height: 15px; border: 0px" alt="" title="" /></label>
			<input name="tel_image" id="tel_image_3" value="tel3" type="radio" <?php if(!empty($tel_image) and $tel_image==='tel3') { ?>checked="checked"<?php } ?> /><label for="tel_image_3" style="cursor: pointer;"><img src="../images/imabulle/tel3.jpg" style="width: 18px; height: 15px; border: 0px" alt="" title="" /></label>
			<input name="tel_image" id="tel_image_4" value="tel4" type="radio" <?php if(!empty($tel_image) and $tel_image==='tel4') { ?>checked="checked"<?php } ?> /><label for="tel_image_4" style="cursor: pointer;"><img src="../images/imabulle/tel4.jpg" style="width: 16px; height: 16px; border: 0px" alt="" title="" /></label>
			<input name="tel_image" id="tel_image_5" value="" type="radio" <?php if(empty($tel_image) and $tel_image==='') { ?>checked="checked"<?php } ?> /><label for="tel_image_5" style="cursor: pointer;">aucune</label>
			<br />
			<input name="entente_fax" id="fax" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($entente_fax) and $entente_fax==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="fax" style="cursor: pointer;">Fax</label><br />
			&nbsp;&nbsp;&nbsp;Texte&nbsp;<input name="fax_texte" size="4" style="border: 1px solid #74748F;" type="text" <?php if(!empty($fax_texte)) { ?>value="<?php echo $fax_texte; ?>" <?php } ?> /> ou
			<input name="fax_image" id="fax_image_1" value="fax" type="radio" <?php if(!empty($fax_image) and $fax_image==='fax') { ?>checked="checked"<?php } ?> /><label for="fax_image_1" style="cursor: pointer;"><img src="../images/imabulle/fax.jpg" style="width: 20px; height: 20px; border: 0px" alt="" title="" /></label>
			<input name="fax_image" id="fax_image_2" value="" type="radio" <?php if(empty($fax_image) and $fax_image==='') { ?>checked="checked"<?php } ?> /><label for="fax_image_2" style="cursor: pointer;">aucune</label>
			<br />

			<input name="entente_mel" id="courrier" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($entente_mel) and $entente_mel==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="courrier" style="cursor: pointer;">Courriel</label><br />
			&nbsp;&nbsp;&nbsp;Texte&nbsp;<input name="courrier_texte" size="4" style="border: 1px solid #74748F;" type="text" <?php if(!empty($courrier_texte)) { ?>value="<?php echo $courrier_texte; ?>" <?php } ?> /> ou
			<input name="courrier_image" id="courrier_image_1" value="courrier" type="radio" <?php if(!empty($courrier_image) and $courrier_image==='courrier') { ?>checked="checked"<?php } ?> /><label for="courrier_image_1" style="cursor: pointer;"><img src="../images/imabulle/courrier.jpg" style="width: 20px; height: 20px; border: 0px" alt="" title="" /></label>
			<input name="courrier_image" id="courrier_image_2" value="courrier2" type="radio" <?php if(!empty($courrier_image) and $courrier_image==='courrier2') { ?>checked="checked"<?php } ?> /><label for="courrier_image_2" style="cursor: pointer;"><img src="../images/imabulle/courrier2.jpg" style="width: 20px; height: 20px; border: 0px" alt="" title="" /></label>
			<input name="courrier_image" id="courrier_image_3" value="sourismel" type="radio" <?php if(!empty($courrier_image) and $courrier_image==='sourismel') { ?>checked="checked"<?php } ?> /><label for="courrier_image_3" style="cursor: pointer;"><img src="../images/imabulle/sourismel.jpg" style="width: 28px; height: 20px; border: 0px" alt="" title="" /></label>
			<input name="courrier_image" id="courrier_image_4" value="" type="radio" <?php if(empty($courrier_image) and $courrier_image==='') { ?>checked="checked"<?php } ?> /><label for="courrier_image_4" style="cursor: pointer;">aucune</label>
			<br />

			<?php
				// Ligne supplémentaire
				echo "<input type='checkbox' name='entete_info_etab_suppl' id='entete_info_etab_suppl' value='y' ";
				if($entete_info_etab_suppl=='y') {
					echo "checked ";
				}
				echo "/><label for='entete_info_etab_suppl'>Ligne supplémentaire de votre choix</label><br />\n";
				echo "&nbsp;&nbsp;&nbsp;Texte&nbsp;: ";
				echo "<input type='text' name='entete_info_etab_suppl_texte' id='entete_info_etab_suppl_texte' value='$entete_info_etab_suppl_texte' size='6' />\n";
				echo "&nbsp;&nbsp;&nbsp;Valeur&nbsp;: ";
				echo "<input type='text' name='entete_info_etab_suppl_valeur' id='entete_info_etab_suppl_valeur' value='$entete_info_etab_suppl_valeur' size='20' /><br />\n";
			?>

			<br />
			<br />


			Logo de l'établissement<br />
			<input name="affiche_logo_etab" id="aff_logo" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_logo_etab) and $affiche_logo_etab==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="aff_logo" style="cursor: pointer;">Affiche le logo</label><br />
			<label for="larg_logo" style="cursor: pointer;">Largeur</label>&nbsp;<input name="L_max_logo" id="larg_logo" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($L_max_logo)) { ?>value="<?php echo $L_max_logo; ?>" <?php } ?> />mm&nbsp;/&nbsp;<label for="haut_logo" style="cursor: pointer;">Hauteur</label>&nbsp;<input name="H_max_logo" id="haut_logo" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($H_max_logo)) { ?>value="<?php echo $H_max_logo; ?>" <?php } ?> />mm&nbsp;<br />
			<input name="centrage_logo" id="centrage_logo" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($centrage_logo) and $centrage_logo==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="centrage_logo" style="cursor: pointer;">Centrer le logo</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label for="Y_centre_logo" style="cursor: pointer;">Positionnement du centrage (Y)</label>&nbsp;<input name="Y_centre_logo" id="Y_centre_logo" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_centre_logo)) { ?>value="<?php echo $Y_centre_logo; ?>" <?php } ?> />mm<br /><br />

				<div style="font-weight: bold; background: #CFCFCF;">Cadre information identité élève</div>
			<input name="active_bloc_eleve" id="active_bloc_eleve_1" value="1" type="radio" <?php if(!empty($active_bloc_eleve) and $active_bloc_eleve==='1') { ?>checked="checked"<?php } ?> /><label for='active_bloc_eleve_1'>&nbsp;Activer</label> &nbsp;<input name="active_bloc_eleve" id="active_bloc_eleve_0" value="0" type="radio" <?php if(empty($active_bloc_eleve) or (!empty($active_bloc_eleve) and $active_bloc_eleve!='1')) { ?>checked="checked"<?php } ?> /><label for='active_bloc_eleve_0'>&nbsp;Désactiver</label><br />
			
			Positionnement X&nbsp;<input name="X_eleve" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_eleve)) { ?>value="<?php echo $X_eleve; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_eleve" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_eleve)) { ?>value="<?php echo $Y_eleve; ?>" <?php } ?> />mm&nbsp;<br />
			
			Largeur du bloc&nbsp;<input name="largeur_bloc_eleve" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_bloc_eleve)) { ?>value="<?php echo $largeur_bloc_eleve; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_bloc_eleve" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_bloc_eleve)) { ?>value="<?php echo $hauteur_bloc_eleve; ?>" <?php } ?> />mm&nbsp;<br />

			<input name="cadre_eleve" id="cadre_eleve" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_eleve) and $cadre_eleve==='1') { ?>checked="checked"<?php } ?> /><label for='cadre_eleve'>&nbsp;Ajouter un encadrement</label><br />

			<input name="active_photo" id="active_photo" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_photo) and $active_photo==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="active_photo" style="cursor: pointer;">la photo</label> (<input name="ajout_cadre_blanc_photo" id="ajout_cadre_blanc_photo" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($ajout_cadre_blanc_photo) and $ajout_cadre_blanc_photo==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="ajout_cadre_blanc_photo" style="cursor: pointer;">Ajouter un cadre blanc</label> )<br />
			
			<input name="affiche_doublement" id="affiche_doublement" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_doublement) and $affiche_doublement==='1') { ?>checked="checked"<?php } ?> /><label for='affiche_doublement'>&nbsp;si doublement</label><br />
			
			<input name="affiche_date_naissance" id="affiche_date_naissance" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_date_naissance) and $affiche_date_naissance==='1') { ?>checked="checked"<?php } ?> /><label for='affiche_date_naissance'>&nbsp;la date de naissance</label><br />
			
			<input name="affiche_dp" id="affiche_dp" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_dp) and $affiche_dp==='1') { ?>checked="checked"<?php } ?> /><label for='affiche_dp'>&nbsp;le régime</label><br />

			<?php
				/*
				// Il ne faut pas mettre là une variable destinée à arriver dans 'setting' pour toutes les classes et tous les modèles PDF.
				echo "<input name='bull_pdf_INE_eleve' style='border: 1px solid #74748F;' type='checkbox' value='1'";
				if($bull_pdf_INE_eleve=='y') {
					echo " checked='checked'";
				}
				echo " />&nbsp;le régime<br />\n"
				*/
			?>

			<input name="affiche_nom_court" id="affiche_nom_court" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_nom_court) and $affiche_nom_court==='1') { ?>checked="checked"<?php } ?> /><label for='affiche_nom_court'>&nbsp;nom court de la classe</label><br />
			
			<input name="affiche_ine" id="affiche_ine" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_ine) and $affiche_ine==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="affiche_ine" style="cursor: pointer;">numéro INE de l'élève</label><br />
			
			<input name="affiche_effectif_classe" id="affiche_effectif_classe" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_effectif_classe) and $affiche_effectif_classe==='1') { ?>checked="checked"<?php } ?> /><label for='affiche_effectif_classe'>&nbsp;effectif de la classe</label><br />
			
			<input name="affiche_numero_impression" id="affiche_numero_impression" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_numero_impression) and $affiche_numero_impression==='1') { ?>checked="checked"<?php } ?> /><label for='affiche_numero_impression'>&nbsp;numéro d'impression</label><br />
			
			<input name="affiche_etab_origine" id="affiche_etab_origine" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_etab_origine) and $affiche_etab_origine==='1') { ?>checked="checked"<?php } ?> /><label for='affiche_etab_origine'>&nbsp;établissement d'origine</label><br /><br />

			</td>
				<td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;">
				
				<div style="font-weight: bold; background: #CFCFCF;">Cadre datation du bulletin</div>
				
			<input name="active_bloc_datation" id="active_bloc_datation_1" value="1" type="radio" <?php if(!empty($active_bloc_datation) and $active_bloc_datation==='1') { ?>checked="checked"<?php } ?> /><label for='active_bloc_datation_1'>&nbsp;Activer</label> &nbsp;<input name="active_bloc_datation" id="active_bloc_datation_0" value="0" type="radio" <?php if(empty($active_bloc_datation) or (!empty($active_bloc_datation) and $active_bloc_datation!='1')) { ?>checked="checked"<?php } ?> /><label for='active_bloc_datation_0'>&nbsp;Désactiver</label><br />
			
			Positionnement X&nbsp;<input name="X_datation_bul" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_datation_bul)) { ?>value="<?php echo $X_datation_bul; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_datation_bul" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_datation_bul)) { ?>value="<?php echo $Y_datation_bul; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="largeur_bloc_datation" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_bloc_datation)) { ?>value="<?php echo $largeur_bloc_datation; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_bloc_datation" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_bloc_datation)) { ?>value="<?php echo $hauteur_bloc_datation; ?>" <?php } ?> />mm&nbsp;<br />

			<input name="cadre_datation_bul" id="cadre_datation_bul" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_datation_bul) and $cadre_datation_bul==='1') { ?>checked="checked"<?php } ?> /><label for='cadre_datation_bul'>&nbsp;Ajouter un encadrement</label><br /><br />

			Taille du texte "classe"&nbsp;<input name="taille_texte_classe" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_classe)) { ?>value="<?php echo $taille_texte_classe; ?>" <?php } ?> />pixel<br />
			&nbsp;&nbsp;&nbsp;format
				<select name="type_texte_classe">
						<option value="" <?php if ( isset($type_texte_classe) and $type_texte_classe === '' ) { ?>selected="selected"<?php } ?>>défaut</option>
						<option value="N" <?php if ( isset($type_texte_classe) and $type_texte_classe === 'N' ) { ?>selected="selected"<?php } ?>>normal</option>
						<option value="B" <?php if ( isset($type_texte_classe) and $type_texte_classe === 'B' ) { ?>selected="selected"<?php } ?> style="font-weight: bold;">gras</option>
						<option value="I" <?php if ( isset($type_texte_classe) and $type_texte_classe === 'I' ) { ?>selected="selected"<?php } ?> style="font-style: italic;">italique</option>
						<option value="U" <?php if ( isset($type_texte_classe) and $type_texte_classe === 'U' ) { ?>selected="selected"<?php } ?> style="text-decoration: underline;">soulignée</option>
					</select><br />

				Taille du texte "année scolaire"&nbsp;<input name="taille_texte_annee" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_annee)) { ?>value="<?php echo $taille_texte_annee; ?>" <?php } ?> />pixel<br />
			&nbsp;&nbsp;&nbsp;format
				<select name="type_texte_annee">
						<option value="" <?php if ( isset($type_texte_annee) and $type_texte_annee === '' ) { ?>selected="selected"<?php } ?>>défaut</option>
						<option value="N" <?php if ( isset($type_texte_annee) and $type_texte_annee === 'N' ) { ?>selected="selected"<?php } ?>>normal</option>
						<option value="B" <?php if ( isset($type_texte_annee) and $type_texte_annee === 'B' ) { ?>selected="selected"<?php } ?> style="font-weight: bold;">gras</option>
						<option value="I" <?php if ( isset($type_texte_annee) and $type_texte_annee === 'I' ) { ?>selected="selected"<?php } ?> style="font-style: italic;">italique</option>
						<option value="U" <?php if ( isset($type_texte_annee) and $type_texte_annee === 'U' ) { ?>selected="selected"<?php } ?> style="text-decoration: underline;">soulignée</option>
					</select><br />

			Taille du texte "période"&nbsp;<input name="taille_texte_periode" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_periode)) { ?>value="<?php echo $taille_texte_periode; ?>" <?php } ?> />pixel<br />
			&nbsp;&nbsp;&nbsp;format
				<select name="type_texte_periode">
						<option value="" <?php if ( isset($type_texte_periode) and $type_texte_periode === '' ) { ?>selected="selected"<?php } ?>>défaut</option>
						<option value="N" <?php if ( isset($type_texte_periode) and $type_texte_periode === 'N' ) { ?>selected="selected"<?php } ?>>normal</option>
						<option value="B" <?php if ( isset($type_texte_periode) and $type_texte_periode === 'B' ) { ?>selected="selected"<?php } ?> style="font-weight: bold;">gras</option>
						<option value="I" <?php if ( isset($type_texte_periode) and $type_texte_periode === 'I' ) { ?>selected="selected"<?php } ?> style="font-style: italic;">italique</option>
						<option value="U" <?php if ( isset($type_texte_periode) and $type_texte_periode === 'U' ) { ?>selected="selected"<?php } ?> style="text-decoration: underline;">soulignée</option>
					</select><br />

			Taille du texte "date d'edition"&nbsp;<input name="taille_texte_date_edition" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_date_edition)) { ?>value="<?php echo $taille_texte_date_edition; ?>" <?php } ?> />pixel<br />
			&nbsp;&nbsp;&nbsp;format
				<select name="type_texte_date_datation">
						<option value="" <?php if ( isset($type_texte_date_datation) and $type_texte_date_datation === '' ) { ?>selected="selected"<?php } ?>>défaut</option>
						<option value="N" <?php if ( isset($type_texte_date_datation) and $type_texte_date_datation === 'N' ) { ?>selected="selected"<?php } ?>>normal</option>
						<option value="B" <?php if ( isset($type_texte_date_datation) and $type_texte_date_datation === 'B' ) { ?>selected="selected"<?php } ?> style="font-weight: bold;">gras</option>
						<option value="I" <?php if ( isset($type_texte_date_datation) and $type_texte_date_datation === 'I' ) { ?>selected="selected"<?php } ?> style="font-style: italic;">italique</option>
						<option value="U" <?php if ( isset($type_texte_date_datation) and $type_texte_date_datation === 'U' ) { ?>selected="selected"<?php } ?> style="text-decoration: underline;">soulignée</option>
					</select><br />
			&nbsp;&nbsp;&nbsp;<input name="affiche_date_edition" id="affiche_date_edition" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_date_edition) and $affiche_date_edition==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="affiche_date_edition" style="cursor: pointer;">Afficher la date d'édition</label><br /><br /><br />





				<div style="font-weight: bold; background: #CFCFCF;">Cadre adresse des parents</div>
				
			<input name="active_bloc_adresse_parent" id="active_bloc_adresse_parent_1" value="1" type="radio" <?php if(!empty($active_bloc_adresse_parent) and $active_bloc_adresse_parent==='1') { ?>checked="checked"<?php } ?> /><label for='active_bloc_adresse_parent_1'>&nbsp;Activer</label> &nbsp;<input name="active_bloc_adresse_parent" id="active_bloc_adresse_parent_0" value="0" type="radio" <?php if(empty($active_bloc_adresse_parent) or (!empty($active_bloc_adresse_parent) and $active_bloc_adresse_parent!='1')) { ?>checked="checked"<?php } ?> /><label for='active_bloc_adresse_parent_0'>&nbsp;Désactiver</label><br />
			Positionnement X&nbsp;<input name="X_parent" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_parent)) { ?>value="<?php echo $X_parent; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_parent" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_parent)) { ?>value="<?php echo $Y_parent; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="largeur_bloc_adresse" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_bloc_adresse)) { ?>value="<?php echo $largeur_bloc_adresse; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_bloc_adresse" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_bloc_adresse)) { ?>value="<?php echo $hauteur_bloc_adresse; ?>" <?php } ?> />mm&nbsp;<br />

			<input name="cadre_adresse" id="cadre_adresse" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_adresse) and $cadre_adresse==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="cadre_adresse" style="cursor: pointer;">Ajouter un encadrement</label><br /><br />

			<!--
			Taille de la police pour la ligne Civilité_Nom_Prénom&nbsp;: 
			<select name=''>
				<?php
					/*
					$adresse_resp_fontsize_ligne_1=12;
					for($loop=1;$loop<30;$loop++) {
						echo "<option value='$loop'";
						if($loop==$adresse_resp_fontsize_ligne_1) {echo " selected";}
						echo ">$loop</option>\n";
					}
					*/
				?>
			</select>pts<br />
			Taille de la police pour les lignes suivantes du bloc adresse&nbsp;: 
			-->
			Taille de la police pour les lignes du bloc adresse&nbsp;: 
			<select name='adresse_resp_fontsize'>
				<?php
					//$adresse_resp_fontsize=10;
					for($loop=1;$loop<30;$loop++) {
						echo "<option value='$loop'";
						if($loop==$adresse_resp_fontsize) {echo " selected";}
						echo ">$loop</option>\n";
					}
				?>
			</select>pts<br /><br />

			Imprimer les bulletins pour :<br />
			<input name="imprime_pour" id="imprime_pour_1" value="1" type="radio" <?php if( (!empty($imprime_pour) and $imprime_pour==='1') or empty($imprime_pour) ) { ?>checked="checked"<?php } ?> /><label for='imprime_pour_1'>&nbsp;seulement pour le 1er responsable</label><br />
			<input name="imprime_pour" id="imprime_pour_2" value="2" type="radio" <?php if(!empty($imprime_pour) and $imprime_pour==='2') { ?>checked="checked"<?php } ?> /><label for='imprime_pour_2'>&nbsp;le 1er et 2ème responsable s'ils n'ont pas la même adresse</label><br />
			<input name="imprime_pour" id="imprime_pour_3" value="3" type="radio" <?php if(!empty($imprime_pour) and $imprime_pour==='3') { ?>checked="checked"<?php } ?> /><label for='imprime_pour_3'>&nbsp;forcer pour le 1er et 2ème responsable</label><br /><br />
			
			<input name="affiche_numero_responsable" id="affiche_numero_responsable" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_numero_responsable) and $affiche_numero_responsable==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="affiche_numero_responsable" style="cursor: pointer;">Afficher le numéro du responsable</label><br /><br />
			</td>
		</tr>
		<tr>
			<td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;" colspan="2" rowspan="1">




			<div style="font-weight: bold; background: #CFCFCF;">Cadre note et appréciation</div>
			
			<input name="active_bloc_note_appreciation" id="active_bloc_note_appreciation_1" value="1" type="radio" <?php if(!empty($active_bloc_note_appreciation) and $active_bloc_note_appreciation==='1') { ?>checked="checked"<?php } ?> /><label for='active_bloc_note_appreciation_1'>&nbsp;Activer</label> &nbsp;<input name="active_bloc_note_appreciation" id="active_bloc_note_appreciation_0" value="0" type="radio" <?php if(empty($active_bloc_note_appreciation) or (!empty($active_bloc_note_appreciation) and $active_bloc_note_appreciation!='1')) { ?>checked="checked"<?php } ?> /><label for='active_bloc_note_appreciation_0'>&nbsp;Désactiver</label><br />
			Positionnement X&nbsp;<input name="X_note_app" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_note_app)) { ?>value="<?php echo $X_note_app; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_note_app" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_note_app)) { ?>value="<?php echo $Y_note_app; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="longeur_note_app" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($longeur_note_app)) { ?>value="<?php echo $longeur_note_app; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_note_app" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_note_app)) { ?>value="<?php echo $hauteur_note_app; ?>" <?php } ?> />mm&nbsp;<br />
			Entête<br />
			&nbsp;&nbsp;&nbsp;Titre de la colonne matière : <input name="titre_entete_matiere" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_matiere)) { ?>value="<?php echo $titre_entete_matiere; ?>" <?php } ?> /><br />

			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Largeur du bloc matière&nbsp;<input name="largeur_matiere" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_matiere)) { ?>value="<?php echo $largeur_matiere; ?>" <?php } ?> />mm<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taille du texte "matière"&nbsp;<input name="taille_texte_matiere" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_matiere)) { ?>value="<?php echo $taille_texte_matiere; ?>" <?php } ?> />pixel<br />

			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="cell_ajustee_texte_matiere" id="cell_ajustee_texte_matiere" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cell_ajustee_texte_matiere) and $cell_ajustee_texte_matiere=='1') { ?>checked="checked"<?php } ?> /><label for='cell_ajustee_texte_matiere'>&nbsp;Permettre le retour à la ligne dans le nom de matière (<em>avec cell_ajustee()</em>)</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ratio entre la taille maximale et la taille minimale de la police pour le nom de matière si cell_ajustee() est utilisée&nbsp;<input name="cell_ajustee_texte_matiere_ratio_min_max" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($cell_ajustee_texte_matiere_ratio_min_max)) { ?>value="<?php echo $cell_ajustee_texte_matiere_ratio_min_max; ?>" <?php } ?> /><br />


			<?php

				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "Présentation des noms de professeurs&nbsp;:<br />\n";

				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<input type='radio' name='presentation_proflist' id='presentation_proflist_1' value='1' ";
				if($presentation_proflist!="2") {echo "checked ";}
				echo "/><label for='presentation_proflist_1'>en colonne (<i>un par ligne</i>)</label><br />\n";

				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<input type='radio' name='presentation_proflist' id='presentation_proflist_2' value='2' ";
				if($presentation_proflist=="2") {echo "checked ";}
				echo "/><label for='presentation_proflist_2'>en ligne (<i>à la suite</i>)</label><br />\n";
			?>

			&nbsp;&nbsp;&nbsp;Titre de la colonne coefficient : <input name="titre_entete_coef" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_coef)) { ?>value="<?php echo $titre_entete_coef; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;Titre de la colonne nombre de note : <input name="titre_entete_nbnote" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_nbnote)) { ?>value="<?php echo $titre_entete_nbnote; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;Titre de la colonne rang : <input name="titre_entete_rang" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_rang)) { ?>value="<?php echo $titre_entete_rang; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;Titre de la colonne appréciation : <input name="titre_entete_appreciation" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_appreciation)) { ?>value="<?php echo $titre_entete_appreciation; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;Type de l'entête des moyennes&nbsp;
				<select name="entete_model_bulletin">
						<option value="1" <?php if ( isset($entete_model_bulletin) and $entete_model_bulletin === '1' ) { ?>selected="selected"<?php } ?>>1-moyenne</option>
						<option value="2" <?php if ( isset($entete_model_bulletin) and $entete_model_bulletin === '2' ) { ?>selected="selected"<?php } ?>>2-pour la classe</option>
					</select><br />
			&nbsp;&nbsp;&nbsp;Choix de l'ordre&nbsp;
				<select name="ordre_entete_model_bulletin">
						<option value="1" <?php if ( isset($ordre_entete_model_bulletin) and $ordre_entete_model_bulletin === '1' ) { ?>selected="selected"<?php } ?>>1 - eleve | min | classe | max | rang | niveau | appreciation |</option>
					<option value="2" <?php if ( isset($ordre_entete_model_bulletin) and $ordre_entete_model_bulletin === '2' ) { ?>selected="selected"<?php } ?>>2 - min | classe | max | eleve | niveau | rang | appreciation |</option>
						<option value="3" <?php if ( isset($ordre_entete_model_bulletin) and $ordre_entete_model_bulletin === '3' ) { ?>selected="selected"<?php } ?>>3 - eleve | niveau | rang | appreciation | min | classe | max |</option>
						<option value="4" <?php if ( isset($ordre_entete_model_bulletin) and $ordre_entete_model_bulletin === '4' ) { ?>selected="selected"<?php } ?>>4 - eleve | classe | min | max | rang | niveau | appreciation |</option>
						<option value="5" <?php if ( isset($ordre_entete_model_bulletin) and $ordre_entete_model_bulletin === '5' ) { ?>selected="selected"<?php } ?>>5 - eleve | min | classe | max | niveau | rang | appreciation |</option>
						<option value="6" <?php if ( isset($ordre_entete_model_bulletin) and $ordre_entete_model_bulletin === '6' ) { ?>selected="selected"<?php } ?>>6 - min | classe | max | eleve | rang | niveau | appreciation |</option>
						<option value="7" <?php if ( isset($ordre_entete_model_bulletin) and $ordre_entete_model_bulletin === '7' ) { ?>selected="selected"<?php } ?>>7 - appreciation | eleve | niveau | rang | min | classe | max | </option>
					</select><br />






			<!-- Autres -->

			<div style="background: #EFEFEF; font-style:italic;">Autres</div>
			<input name="active_coef_moyenne" id="active_coef_moyenne" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_coef_moyenne) and $active_coef_moyenne==='1') { ?>checked="checked"<?php } ?> /><label for='active_coef_moyenne'>&nbsp;Coefficient de chaque matière</label><br />
			
			&nbsp;&nbsp;&nbsp;- Largeur de la colonne des coefficients&nbsp;<input name="largeur_coef_moyenne" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_coef_moyenne)) { ?>value="<?php echo $largeur_coef_moyenne; ?>" <?php } ?> />mm<br />
			
			&nbsp;&nbsp;&nbsp;<input name="active_coef_sousmoyene" id="active_coef_sousmoyene" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_coef_sousmoyene) and $active_coef_sousmoyene==='1') { ?>checked="checked"<?php } ?> /><label for='active_coef_sousmoyene'>&nbsp;l'afficher sous la moyenne de l'élève</label><br />
			
			<input name="active_nombre_note_case" id="active_nombre_note_case" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_nombre_note_case) and $active_nombre_note_case==='1') { ?>checked="checked"<?php } ?> /><label for='active_nombre_note_case'>&nbsp;Nombre de notes par matière dans une case</label><br />
			
			&nbsp;&nbsp;&nbsp;- Largeur de la colonne du nombre de notes&nbsp;<input name="largeur_nombre_note" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_nombre_note)) { ?>value="<?php echo $largeur_nombre_note; ?>" <?php } ?> />mm<br />
			
			&nbsp;&nbsp;&nbsp;<input name="active_nombre_note" id="active_nombre_note" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_nombre_note) and $active_nombre_note==='1') { ?>checked="checked"<?php } ?> /><label for='active_nombre_note'>&nbsp;l'afficher sous la moyenne de l'élève</label><br />





			<!-- Moyenne -->

			<div style="background: #EFEFEF; font-style:italic;">Moyenne</div>
			<input name="active_moyenne" id="active_moyenne" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne) and $active_moyenne==='1') { ?>checked="checked"<?php } ?> /><label for='active_moyenne'>&nbsp;Les moyennes</label><br />
			&nbsp;&nbsp;&nbsp;- Largeur de la colonne d'une moyenne&nbsp;<input name="largeur_d_une_moyenne" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_d_une_moyenne)) { ?>value="<?php echo $largeur_d_une_moyenne; ?>" <?php } ?> />mm<br />

			<br />

			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_eleve" id="active_moyenne_eleve" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_eleve) and $active_moyenne_eleve==='1') { ?>checked="checked"<?php } ?> /><label for='active_moyenne_eleve'>&nbsp;Moyenne de l'élève</label>
			&nbsp;&nbsp;&nbsp;(<input name="active_reperage_eleve" id="active_reperage_eleve" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_reperage_eleve) and $active_reperage_eleve==='1') { ?>checked="checked"<?php } ?> /><label for='active_reperage_eleve'>&nbsp;Mettre un fond de couleur</label> - R:<input name="couleur_reperage_eleve1" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_reperage_eleve1)) { ?>value="<?php echo $couleur_reperage_eleve1; ?>" <?php } ?> /> G:<input name="couleur_reperage_eleve2" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_reperage_eleve2)) { ?>value="<?php echo $couleur_reperage_eleve2; ?>" <?php } ?> /> B:<input name="couleur_reperage_eleve3" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_reperage_eleve3)) { ?>value="<?php echo $couleur_reperage_eleve3; ?>" <?php } ?> />)<br />


			<?php
				$decalage_gauche="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo $decalage_gauche;
				echo "<input name='toute_moyenne_meme_col' id='toute_moyenne_meme_col' style='border: 1px solid #74748F;' type='checkbox' value='1' ";
				if(!empty($toute_moyenne_meme_col) and $toute_moyenne_meme_col==='1') {
					echo "checked='checked' ";
				}
				echo "onchange='check_coherence_coches_bulletin_pdf();' ";
				echo "/><label for='toute_moyenne_meme_col'>&nbsp;Afficher Moyennes classe/min/max sous la moyenne de l'élève à condition qu'elles soient cochées</label><br />\n";

				echo $decalage_gauche;
				echo "ou<br />\n";
				//===========================================
				echo $decalage_gauche;
				echo "<input name='moyennes_periodes_precedentes' id='moyennes_periodes_precedentes' style='border: 1px solid #74748F;' type='checkbox' value='y' ";
				if(!empty($moyennes_periodes_precedentes) and $moyennes_periodes_precedentes=='y') {
					echo "checked='checked' ";
				}
				echo "onchange='check_coherence_coches_bulletin_pdf();' ";
				echo "/><label for='moyennes_periodes_precedentes'>&nbsp;Pour chaque enseignement, afficher les moyennes de l'élève pour les périodes précédentes</label><br />\n";
				echo $decalage_gauche;
				echo "(<i>incompatible avec le choix \"Moyennes classe/min/max sous la moyenne de l'élève\"</i>)<br />\n";
				//===========================================
				echo $decalage_gauche;
				echo "<input name='moyennes_annee' id='moyennes_annee' style='border: 1px solid #74748F;' type='checkbox' value='y' ";
				if(!empty($moyennes_annee) and $moyennes_annee=='y') {
					echo "checked='checked' ";
				}
				echo "onchange='check_coherence_coches_bulletin_pdf();' ";
				echo "/><label for='moyennes_annee'>&nbsp;Pour chaque enseignement, afficher les moyennes annuelles de l'élève</label><br />\n";
				echo $decalage_gauche;
				echo "(<i>incompatible avec le choix \"Moyennes classe/min/max sous la moyenne de l'élève\"</i>)<br />\n";
				//===========================================
				echo $decalage_gauche;
				echo "<input name='evolution_moyenne_periode_precedente' id='evolution_moyenne_periode_precedente' style='border: 1px solid #74748F;' type='checkbox' value='y' ";
				if(!empty($evolution_moyenne_periode_precedente) and $evolution_moyenne_periode_precedente==='y') {
					echo "checked='checked' ";
				}
				echo "/><label for='evolution_moyenne_periode_precedente'>&nbsp;Pour chaque enseignement, indiquer par un + ou - l'évolution de la moyenne (<i>hausse/stable/baisse</i>) par rapport à la période précédente.</label><br />\n";

			?>
			<br />

			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_classe" id="active_moyenne_classe" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_classe) and $active_moyenne_classe==='1') { ?>checked="checked"<?php } ?> /><label for='active_moyenne_classe'>&nbsp;Moyenne de la classe</label><br />
			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_min" id="active_moyenne_min" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_min) and $active_moyenne_min==='1') { ?>checked="checked"<?php } ?> /><label for='active_moyenne_min'>&nbsp;Moyenne la plus basse</label><br />
			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_max" id="active_moyenne_max" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_max) and $active_moyenne_max==='1') { ?>checked="checked"<?php } ?> /><label for='active_moyenne_max'>&nbsp;Moyenne la plus haute</label><br />

			<br />

			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_general" id="active_moyenne_general" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_general) and $active_moyenne_general === '1') { ?>checked="checked"<?php } ?> /><label for='active_moyenne_general'>&nbsp;Ligne des moyennes générales</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="affiche_moyenne_mini_general" id="affiche_moyenne_mini_general" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_moyenne_mini_general) and $affiche_moyenne_mini_general === '1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="affiche_moyenne_mini_general" style="cursor: pointer;">moyenne générale la plus basse</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="affiche_moyenne_maxi_general" id="affiche_moyenne_maxi_general" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_moyenne_maxi_general) and $affiche_moyenne_maxi_general === '1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="affiche_moyenne_maxi_general" style="cursor: pointer;">moyenne générale la plus haute</label><br />

			<?php
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "Colonne coefficient de la ligne Moyenne générale&nbsp;:<br />\n";

				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<input name='affiche_totalpoints_sur_totalcoefs' id='affiche_totalpoints_sur_totalcoefs_0' style='border: 1px solid #74748F;' type='radio' value='0' ";
				if((empty($affiche_totalpoints_sur_totalcoefs))||($affiche_totalpoints_sur_totalcoefs=='0')) {
					echo "checked='checked' ";
				}
				echo " />&nbsp;<label for='affiche_totalpoints_sur_totalcoefs_0' style='cursor: pointer;'>pas de total affiché</label><br />\n";

				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<input name='affiche_totalpoints_sur_totalcoefs' id='affiche_totalpoints_sur_totalcoefs_1' style='border: 1px solid #74748F;' type='radio' value='1' ";
				if($affiche_totalpoints_sur_totalcoefs=='1') {
					echo "checked='checked' ";
				}
				echo " />&nbsp;<label for='affiche_totalpoints_sur_totalcoefs_1' style='cursor: pointer;'>afficher le total des points sur le total des coefficients dans la case coefficients de la ligne moyenne générale</label><br />\n";

				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<input name='affiche_totalpoints_sur_totalcoefs' id='affiche_totalpoints_sur_totalcoefs_2' style='border: 1px solid #74748F;' type='radio' value='2' ";
				if($affiche_totalpoints_sur_totalcoefs=='2') {
					echo "checked='checked' ";
				}
				echo " />&nbsp;<label for='affiche_totalpoints_sur_totalcoefs_2' style='cursor: pointer;'>afficher le total des coefficients seulement</label><br />\n";

				/*
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name='affiche_totalpoints_sur_totalcoefs' id='affiche_totalpoints_sur_totalcoefs' style='border: 1px solid #74748F;' type='checkbox' value='1' ";
				//if((empty($affiche_totalpoints_sur_totalcoefs))||(!empty($affiche_totalpoints_sur_totalcoefs) and $affiche_totalpoints_sur_totalcoefs=='1')) {
				if(!empty($affiche_totalpoints_sur_totalcoefs) and $affiche_totalpoints_sur_totalcoefs=='1') {
					echo "checked='checked' ";
				}
				echo "/>&nbsp;<label for='affiche_totalpoints_sur_totalcoefs' style='cursor: pointer;'>afficher le total des points sur le total des coefficients dans la case coefficients de la ligne moyenne générale<br />\n";
				*/
			?>

			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="affiche_moyenne_general_coef_1" id="affiche_moyenne_general_coef_1" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_moyenne_general_coef_1) and $affiche_moyenne_general_coef_1 === '1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="affiche_moyenne_general_coef_1" style="cursor: pointer;">moyenne générale avec coefficients à 1 en plus de la moyenne générale<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;avec les coefficients définis dans Gestion des classes/&lt;Classes&gt; Enseignements</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ce choix est sans effet, si tous les coefficients sont à 1,<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ou si on force tous les coefficients à 1,<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ou encore si on n'affiche pas les moyennes générales.<br />

			<br />

			&nbsp;Arrondir les moyennes à : <input name="arrondie_choix" value="0.01" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='0.01') { ?>checked="checked"<?php } ?> />0,01 <input name="arrondie_choix" value="0.1" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='0.1') { ?>checked="checked"<?php } ?> />0,1 <input name="arrondie_choix" value="0.25" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='0.25') { ?>checked="checked"<?php } ?> />0,25 <input name="arrondie_choix" value="0.5" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='0.5') { ?>checked="checked"<?php } ?> />0,5 <input name="arrondie_choix" value="1" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='1') { ?>checked="checked"<?php } ?> />1<br />
			&nbsp;Nombre de zéros après la virgule : <input name="nb_chiffre_virgule" value="2" type="radio" <?php if(!empty($nb_chiffre_virgule) and $nb_chiffre_virgule==='2') { ?>checked="checked"<?php } ?> />2  <input name="nb_chiffre_virgule" value="1" type="radio" <?php if(!empty($nb_chiffre_virgule) and $nb_chiffre_virgule==='1') { ?>checked="checked"<?php } ?> />1 - <input name="chiffre_avec_zero" id="chiffre_avec_zero" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($chiffre_avec_zero) and $chiffre_avec_zero==='1') { ?>checked="checked"<?php } ?> /><label for='chiffre_avec_zero'> ne pas afficher le "0" après la virgule</label><br />

			<!-- Autres -->

			<div style="background: #EFEFEF; font-style:italic;">Autres</div>
			<input name="active_rang" id="active_rang" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_rang) and $active_rang==='1') { ?>checked="checked"<?php } ?> /><label for='active_rang'>&nbsp;Rang de l'élève</label><br />
			&nbsp;&nbsp;&nbsp;- Largeur de la colonne rang&nbsp;<input name="largeur_rang" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_rang)) { ?>value="<?php echo $largeur_rang; ?>" <?php } ?> />mm<br />

			<input name="active_graphique_niveau" id="active_graphique_niveau" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_graphique_niveau) and $active_graphique_niveau==='1') { ?>checked="checked"<?php } ?> /><label for='active_graphique_niveau'>&nbsp;Graphique de niveau</label><br />
			&nbsp;&nbsp;&nbsp;- Largeur de la colonne niveau&nbsp;<input name="largeur_niveau" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_niveau)) { ?>value="<?php echo $largeur_niveau; ?>" <?php } ?> />mm<br />

			<input name="active_appreciation" id="active_appreciation" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_appreciation) and $active_appreciation==='1') { ?>checked="checked"<?php } ?> /><label for='active_appreciation'>&nbsp;Appréciation par matière</label><br />
			&nbsp;&nbsp;&nbsp;<input name="autorise_sous_matiere" id="autorise_sous_matiere" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($autorise_sous_matiere) and $autorise_sous_matiere==='1') { ?>checked="checked"<?php } ?> /><label for='autorise_sous_matiere'>&nbsp;Autoriser l'affichage des <?php echo getSettingValue('gepi_denom_boite')?>s</label><br />

			Hauteur de la moyenne générale&nbsp;<input name="hauteur_entete_moyenne_general" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_entete_moyenne_general)) { ?>value="<?php echo $hauteur_entete_moyenne_general; ?>" <?php } ?> />mm<br />

			<div style="background: #EFEFEF; font-style:italic;">Catégories de matières :</div>
			&nbsp;&nbsp;&nbsp;<input name="active_regroupement_cote" id="active_regroupement_cote" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_regroupement_cote) and $active_regroupement_cote==='1') { ?>checked="checked"<?php } ?> /><label for='active_regroupement_cote'>&nbsp;Nom des catégories de matières sur le coté</label>
			&nbsp;&nbsp;&nbsp;(<input name="couleur_categorie_cote" id="couleur_categorie_cote" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($couleur_categorie_cote) and $couleur_categorie_cote==='1') { ?>checked="checked"<?php } ?> /><label for='couleur_categorie_cote'>&nbsp;Mettre un fond de couleur</label> - R:<input name="couleur_categorie_cote1" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_cote1)) { ?>value="<?php echo $couleur_categorie_cote1; ?>" <?php } ?> /> G:<input name="couleur_categorie_cote2" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_cote2)) { ?>value="<?php echo $couleur_categorie_cote2; ?>" <?php } ?> /> B:<input name="couleur_categorie_cote3" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_cote3)) { ?>value="<?php echo $couleur_categorie_cote3; ?>" <?php } ?> />)<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taille du texte&nbsp;<input name="taille_texte_categorie_cote" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_categorie_cote)) { ?>value="<?php echo $taille_texte_categorie_cote; ?>" <?php } ?> />pixel<br />

			&nbsp;&nbsp;&nbsp;<input name="active_entete_regroupement" id="active_entete_regroupement" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_entete_regroupement) and $active_entete_regroupement==='1') { ?>checked="checked"<?php } ?> /><label for='active_entete_regroupement'>&nbsp;Nom des catégories de matières en entête</label>
			&nbsp;&nbsp;&nbsp;(<input name="couleur_categorie_entete" id="couleur_categorie_entete" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($couleur_categorie_entete) and $couleur_categorie_entete==='1') { ?>checked="checked"<?php } ?> /><label for='couleur_categorie_entete'>&nbsp;Mettre un fond de couleur</label> - R:<input name="couleur_categorie_entete1" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_entete1)) { ?>value="<?php echo $couleur_categorie_entete1; ?>" <?php } ?> /> G:<input name="couleur_categorie_entete2" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_entete2)) { ?>value="<?php echo $couleur_categorie_entete2; ?>" <?php } ?> /> B:<input name="couleur_categorie_entete3" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_entete3)) { ?>value="<?php echo $couleur_categorie_entete3; ?>" <?php } ?> />)<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taille du texte&nbsp;<input name="taille_texte_categorie" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_categorie)) { ?>value="<?php echo $taille_texte_categorie; ?>" <?php } ?> />pixel<br />

			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Hauteur entête des catégories&nbsp;<input name="hauteur_info_categorie" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_info_categorie)) { ?>value="<?php echo $hauteur_info_categorie; ?>" <?php } ?> />mm<br />

			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_regroupement" id="active_moyenne_regroupement" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_regroupement) and $active_moyenne_regroupement==='1') { ?>checked="checked"<?php } ?> /><label for='active_moyenne_regroupement'>&nbsp;Moyenne des catégories de matières</label><br />


			<div style="background: #EFEFEF; font-style:italic;">Moyenne générale</div>

			&nbsp;&nbsp;&nbsp;<input name="couleur_moy_general" id="couleur_moy_general" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($couleur_moy_general) and $couleur_moy_general==='1') { ?>checked="checked"<?php } ?> /><label for='couleur_moy_general'>&nbsp;Mettre un fond de couleur</label> - R:<input name="couleur_moy_general1" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_moy_general1)) { ?>value="<?php echo $couleur_moy_general1; ?>" <?php } ?> /> G:<input name="couleur_moy_general2" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_moy_general2)) { ?>value="<?php echo $couleur_moy_general2; ?>" <?php } ?> /> B:<input name="couleur_moy_general3" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_moy_general3)) { ?>value="<?php echo $couleur_moy_general3; ?>" <?php } ?> /><br /><br />




			<div style="font-weight: bold; background: #CFCFCF;">Cadre Absences/CPE</div>

			<?php
				// A mettre dans 162_to_163
				if((!isset($afficher_abs_tot))||($afficher_abs_tot=="")||(!isset($afficher_abs_nj))||($afficher_abs_nj=="")||(!isset($afficher_abs_ret))||($afficher_abs_ret=="")) {
					if($active_bloc_absence=="1") {
						$afficher_abs_tot='1';
						$afficher_abs_nj='1';
						$afficher_abs_ret='1';
					}
					else {
						$afficher_abs_tot='0';
						$afficher_abs_nj='0';
						$afficher_abs_ret='0';
					}
				}
			?>

			<!-- 20130215 -->
			<table border='0'>
				<tr>
					<td colspan='2'>Affichage du bloc absences/appréciation du CPE&nbsp;:</td>
					<td><input name="active_bloc_absence" id="active_bloc_absence_1" value="1" type="radio" <?php if(!empty($active_bloc_absence) and $active_bloc_absence==='1') { ?>checked="checked"<?php } ?> /><label for='active_bloc_absence_1'>&nbsp;Activer</label> &nbsp;<input name="active_bloc_absence" id="active_bloc_absence_0" value="0" type="radio" <?php if(empty($active_bloc_absence) or (!empty($active_bloc_absence) and $active_bloc_absence!='1')) { ?>checked="checked"<?php } ?> /><label for='active_bloc_absence_0'>&nbsp;Désactiver</label></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td>Affichage des totaux d'absences&nbsp;:</td>
					<td><input name="afficher_abs_tot" id="afficher_abs_tot_1" value="1" type="radio" <?php if(!empty($afficher_abs_tot) and $afficher_abs_tot==='1') { ?>checked="checked"<?php } ?> /><label for='afficher_abs_tot_1'>&nbsp;Activer</label> &nbsp;<input name="afficher_abs_tot" id="afficher_abs_tot_0" value="0" type="radio" <?php if(empty($afficher_abs_tot) or (!empty($afficher_abs_tot) and $afficher_abs_tot!='1')) { ?>checked="checked"<?php } ?> /><label for='afficher_abs_tot_0'>&nbsp;Désactiver</label></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td>Affichage du nombre d'absences non justifiées&nbsp;:</td>
					<td><input name="afficher_abs_nj" id="afficher_abs_nj_1" value="1" type="radio" <?php if(!empty($afficher_abs_nj) and $afficher_abs_nj==='1') { ?>checked="checked"<?php } ?> /><label for='afficher_abs_nj_1'>&nbsp;Activer</label> &nbsp;<input name="afficher_abs_nj" id="afficher_abs_nj_0" value="0" type="radio" <?php if(empty($afficher_abs_nj) or (!empty($afficher_abs_nj) and $afficher_abs_nj!='1')) { ?>checked="checked"<?php } ?> /><label for='afficher_abs_nj_0'>&nbsp;Désactiver</label></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td>Affichage du nombre de retards&nbsp;:</td>
					<td><input name="afficher_abs_ret" id="afficher_abs_ret_1" value="1" type="radio" <?php if(!empty($afficher_abs_ret) and $afficher_abs_ret==='1') { ?>checked="checked"<?php } ?> /><label for='afficher_abs_ret_1'>&nbsp;Activer</label> &nbsp;<input name="afficher_abs_ret" id="afficher_abs_ret_0" value="0" type="radio" <?php if(empty($afficher_abs_ret) or (!empty($afficher_abs_ret) and $afficher_abs_ret!='1')) { ?>checked="checked"<?php } ?> /><label for='afficher_abs_ret_0'>&nbsp;Désactiver</label></td>
				</tr>
			</table>

			Positionnement X&nbsp;<input name="X_absence" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_absence)) { ?>value="<?php echo $X_absence; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_absence" size="3" style="border: 1px solid #74748F;" type="text"  <?php if(!empty($Y_absence)) { ?>value="<?php echo $Y_absence; ?>" <?php } ?> />mm&nbsp;<br />

			Largeur du cadre Absences&nbsp;: 
			<?php
				//$largeur_cadre_absences=200;
				echo "<input  type='text' name='largeur_cadre_absences' size='3' style='border: 1px solid #74748F;' value='$largeur_cadre_absences' />\n";
			?>
			<br /><br />
			</td>
		</tr>
		<tr>
			<td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;">


				<div style="font-weight: bold; background: #CFCFCF;">Cadre Avis conseil de classe</div>

			<input name="active_bloc_avis_conseil" id="active_bloc_avis_conseil_1" value="1" type="radio" <?php if(!empty($active_bloc_avis_conseil) and $active_bloc_avis_conseil==='1') { ?>checked="checked"<?php } ?> /><label for='active_bloc_avis_conseil_1'>&nbsp;Activer</label> &nbsp;<input name="active_bloc_avis_conseil" id="active_bloc_avis_conseil_0" value="0" type="radio" <?php if(empty($active_bloc_avis_conseil) or (!empty($active_bloc_avis_conseil) and $active_bloc_avis_conseil!='1')) { ?>checked="checked"<?php } ?> /><label for='active_bloc_avis_conseil_0'>&nbsp;Désactiver</label><br />
			Positionnement X&nbsp;<input name="X_avis_cons" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_avis_cons)) { ?>value="<?php echo $X_avis_cons; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_avis_cons" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_avis_cons)) { ?>value="<?php echo $Y_avis_cons; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="longeur_avis_cons" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($longeur_avis_cons)) { ?>value="<?php echo $longeur_avis_cons; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_avis_cons" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_avis_cons)) { ?>value="<?php echo $hauteur_avis_cons; ?>" <?php } ?> />mm&nbsp;<br />
			Titre du bloc avis conseil de classe : <input name="titre_bloc_avis_conseil" size="19" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_bloc_avis_conseil)) { ?>value="<?php echo $titre_bloc_avis_conseil; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taille du texte&nbsp;<input name="taille_titre_bloc_avis_conseil" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_titre_bloc_avis_conseil)) { ?>value="<?php echo $taille_titre_bloc_avis_conseil; ?>" <?php } ?> />pixel<br />

			Taille du texte du <?php
				$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
				if($gepi_prof_suivi=='') {
					$info_pp="<p style='color:red'>La variable '<strong>gepi_prof_suivi</strong>' n'est pas renseignée dans <strong>Gestion générale/Configuration générale</strong>.<br />";
					$info_pp.="On y indique habituellement quelque chose comme '<strong>professeur principal</strong>'.";
					if($_SESSION['statut']!='administrateur') {
						$info_pp.="Signalez-le à l'administrateur du Gepi pour qu'il corrige.</p>\n";
					}
					$info_pp.="</p>";
				}
				echo $gepi_prof_suivi;
			?>&nbsp;<input name="taille_profprincipal_bloc_avis_conseil" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_profprincipal_bloc_avis_conseil)) { ?>value="<?php echo $taille_profprincipal_bloc_avis_conseil; ?>" <?php } ?> />pixel<br />

			<?php
				if(isset($info_pp)) {echo $info_pp;}
			?>

			<input name="afficher_tous_profprincipaux" id="afficher_tous_profprincipaux" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($afficher_tous_profprincipaux) and $afficher_tous_profprincipaux==='1') { ?>checked="checked"<?php } ?> /><label for='afficher_tous_profprincipaux'>&nbsp;Afficher les noms de tous les "<?php echo getSettingValue('gepi_prof_suivi');?>"<br />
			&nbsp;&nbsp;&nbsp;&nbsp;au lieu du seul <?php echo getSettingValue('gepi_prof_suivi');?> associé à l'élève<br />
			&nbsp;&nbsp;&nbsp;&nbsp;(<em>dans le cas où il y a plus d'un <?php echo getSettingValue('gepi_prof_suivi');?> associé<br />
			&nbsp;&nbsp;&nbsp;&nbsp;aux différents élèves de la classe</em>).</label><br />

			<input name="cadre_avis_cons" id="cadre_avis_cons" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_avis_cons) and $cadre_avis_cons==='1') { ?>checked="checked"<?php } ?> /><label for='cadre_avis_cons'>&nbsp;Ajouter un encadrement</label><br />

			<?php
				$gepi_denom_mention=getSettingValue("gepi_denom_mention");
				if($gepi_denom_mention=="") {
					$gepi_denom_mention="mention";
				}

				
				echo "<input type='checkbox' name='affich_mentions' id='affich_mentions' value='y' ";
				if($affich_mentions!="n") {echo "checked ";}
				echo "/> \n";
				echo "<label for='affich_mentions'>Faire apparaître les ".$gepi_denom_mention."s sur les bulletins.</label><br />\n";

				echo "&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='affich_coches_mentions' id='affich_coches_mentions' value='y' ";
				if($affich_coches_mentions!="n") {echo "checked ";}
				echo "/> \n";
				echo "<label for='affich_coches_mentions'>Faire apparaître des cases à cocher pour les ".$gepi_denom_mention."s sur les bulletins.</label><br />\n";

				echo "&nbsp;&nbsp;&nbsp;&nbsp;Ou sinon, sans cases à cocher :<br />&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='affich_intitule_mentions' id='affich_intitule_mentions' value='y' ";
				if($affich_intitule_mentions!="n") {echo "checked ";}
				echo "/> \n";
				echo "<label for='affich_intitule_mentions'>Faire apparaître l'intitulé <b>$gepi_denom_mention</b> avant la $gepi_denom_mention choisie pour un élève.</label><br />\n";

			?>
			<br /><br />

			</td>
				<td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;">

				<div style="font-weight: bold; background: #CFCFCF;">Cadre signature du chef</div>
			<input name="active_bloc_chef" id="active_bloc_chef_1" value="1" type="radio" <?php if(!empty($active_bloc_chef) and $active_bloc_chef==='1') { ?>checked="checked"<?php } ?> /><label for='active_bloc_chef_1'>&nbsp;Activer &nbsp;</label><input name="active_bloc_chef" id="active_bloc_chef_0" value="0" type="radio" <?php if(empty($active_bloc_chef) or (!empty($active_bloc_chef) and $active_bloc_chef!='1')) { ?>checked="checked"<?php } ?> /><label for='active_bloc_chef_0'>&nbsp;Désactiver</label><br />
			Positionnement X&nbsp;<input name="X_sign_chef" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_sign_chef)) { ?>value="<?php echo $X_sign_chef; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionnement Y&nbsp;<input name="Y_sign_chef" size="3" style="border: 1px solid #74748F;" type="text"  <?php if(!empty($Y_sign_chef)) { ?>value="<?php echo $Y_sign_chef; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="longeur_sign_chef" size="3" style="border: 1px solid #74748F;" type="text"  <?php if(!empty($longeur_sign_chef)) { ?>value="<?php echo $longeur_sign_chef; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_sign_chef" size="3" style="border: 1px solid #74748F;" type="text"  <?php if(!empty($hauteur_sign_chef)) { ?>value="<?php echo $hauteur_sign_chef; ?>" <?php } ?> />mm&nbsp;<br />
			<input name="affichage_haut_responsable" id="affichage_haut_responsable" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affichage_haut_responsable) and $affichage_haut_responsable==='1') { ?>checked="checked"<?php } ?> /><label for='affichage_haut_responsable'>&nbsp;Afficher l'identité du responsable de direction</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taille du texte&nbsp;<input name="taille_texte_identitee_chef" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_identitee_chef)) { ?>value="<?php echo $taille_texte_identitee_chef; ?>" <?php } ?> />pixel<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="affiche_fonction_chef" id="affiche_fonction_chef" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_fonction_chef) and $affiche_fonction_chef==='1') { ?>checked="checked"<?php } ?> /><label for='affiche_fonction_chef'>&nbsp;Afficher sa fonction</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taille du texte&nbsp;<input name="taille_texte_fonction_chef" size="2" style="border: 1px solid #74748F;" type="text" <?php if(!empty($taille_texte_fonction_chef)) { ?>value="<?php echo $taille_texte_fonction_chef; ?>" <?php } ?> />pixel<br />

			<input name="cadre_sign_chef" id="cadre_sign_chef" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_sign_chef) and $cadre_sign_chef==='1') { ?>checked="checked"<?php } ?> /><label for='cadre_sign_chef'>&nbsp;Ajouter un encadrement</label><br /><br />

			<?php
				echo "<input type='checkbox' name='signature_img' id='signature_img' value='1' ";
				if(isset($signature_img) and $signature_img=='1') { 
					echo "checked='checked'";
				}
				echo "/><label for='signature_img'>Insérer une image de signature</label><br />\n";
				echo "(<em>sous réserve qu'une ";
				if($_SESSION['statut']=='administrateur') {
					echo "<a href='../gestion/gestion_signature.php'>image de signature</a>";
				}
				else {
					echo "image de signature";
				}
				echo " ait été uploadée en administrateur<br />et que vous soyez autorisé à utiliser cette signature</em>)";
			?>

			</td>
		</tr>
		<tr>
			<td style="vertical-align: center; white-space: nowrap; text-align: center; width: 100%; background: #B3B7BF;" colspan="2" rowspan="1">
			<?php
			/*
			// Déjà inséré plus haut
			if($action_model==='modifier') {
				echo "<input type='hidden' name='id_model_bulletin' value='$id_model_bulletin' />\n";
			}
			*/
			?>
			<input type="hidden" name="action_model" value="<?php echo $action_model; ?>" />
			<input type="submit" id="valide_modif_model2" name="valide_modif_model" value="Valider le modèle" />
			</td>
		</tr>
		</tbody>
		</table>
		<input type='hidden' name='is_posted' value='y' />
		</form>
		<?php

		echo "<script type='text/javascript'>
// Diverses vérifications
function check_coherence_coches_bulletin_pdf() {
	if((document.getElementById('toute_moyenne_meme_col'))&&
	(document.getElementById('moyennes_periodes_precedentes'))&&
	(document.getElementById('moyennes_annee'))) {
		if((document.getElementById('toute_moyenne_meme_col').checked==true)&&
			((document.getElementById('moyennes_periodes_precedentes').checked==true)||
			(document.getElementById('moyennes_annee').checked==true))) {
			alert('Le choix \"Afficher Moyennes classe/min/max sous la moyenne de l\'élève\" est incompatible avec les choix \"Afficher les moyennes de l\'élève pour les périodes précédentes\" et \"Afficher les moyennes annuelles de l\'élève\".\\nLes deuxième et troisième choix vont être décochés.');
			document.getElementById('moyennes_periodes_precedentes').checked=false;
			document.getElementById('moyennes_annee').checked=false;
		}
	}
}
</script>\n";

	}

	if($action_model==='supprimer' and empty($valide_modif_model)) {

		echo "<form method='post' action='".$_SERVER['PHP_SELF']."?modele=aff' name='action_modele_form'>\n";
		echo add_token_field();
		echo "<h2>Supprimer un modèle de bulletin</h2>\n";

		$sql="SELECT valeur FROM modele_bulletin WHERE id_model_bulletin='$model_bulletin' AND nom='nom_model_bulletin';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p>Aucun modèle n'a été trouvé pour l'identifiant $model_bulletin</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		else {
			$lig_tmp=mysql_fetch_object($res);
			echo "<p>Vous allez supprimer le modèle <strong>$lig_tmp->valeur</strong></p>\n";
	?>
		<table style="text-align: left; width: 100%; border: 1px solid #74748F;" border="0" cellpadding="2" cellspacing="2" summary="Suppression d'un modèle">
		<tbody>
		<tr>
			<td style="vertical-align: center; white-space: nowrap; text-align: center; width: 100%; background: #B3B7BF;" colspan="2" rowspan="1">
			<br /><span style="font-weight: bold; color: rgb(255, 0, 0);">Souhaitez-vous supprimer ce modèle ?</span><br /><br />
			<input type="hidden" name="id_model_bulletin" value="<?php echo $model_bulletin; ?>" />
			<input type="hidden" name="action_model" value="<?php echo $action_model; ?>" />
			<input type="submit" id="valide_modif_model" name="valide_modif_model" value="Oui supprimer ce modèle" />
			</td>
		</tr>
		</tbody>
		</table>
		</form>
		<?php
		}
	}
}


require("../lib/footer.inc.php");
?>
