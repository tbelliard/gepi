<?php
/*
 * $Id$
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

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
		if ( !empty($classe[0]) and empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oublier pas de sélectioner la ou les période(s) !'; }
		if ( empty($classe[0]) and !empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oublier pas de sélectioner la ou les classe(s) !'; }
		if ( empty($classe[0]) and empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oublier pas de sélectioner la ou les classe(s) et la ou les période(s) !'; }

	if(empty($_SESSION['classe']) or !empty($_POST['classe'])) { $_SESSION['classe'] = $classe; }
	if(empty($_SESSION['eleve']) or !empty($_POST['eleve'])) { $_SESSION['eleve'] = $eleve; }
	$_SESSION['periode'] = $periode;
	$_SESSION['periode_ferme'] = $periode_ferme;
	$_SESSION['type_bulletin'] = $type_bulletin;

	if(!empty($creer_pdf) and !empty($periode[0]) and !empty($classe[0]) and !empty($type_bulletin) and empty($selection_eleve) )
	{  header("Location: buletin_pdf.php"); }
// FIN Christian renvoye vers le fichier PDF bulletin

// Modif Christian pour les variable PDF
	$selection = isset($_POST["selection"]) ? $_POST["selection"] :NULL;
	$selection_eleve = isset($_POST["selection_eleve"]) ? $_POST["selection_eleve"] :NULL;
	$bt_select_periode = isset($_POST["bt_select_periode"]) ? $_POST["bt_select_periode"] :NULL;
	$valide_modif_model = isset($_POST["valide_modif_model"]) ? $_POST["valide_modif_model"] :NULL;


	if (empty($_GET['format']) and empty($_POST['format'])) {$format="";}
	    else { if (isset($_GET['format'])) {$format=$_GET['format'];} if (isset($_POST['format'])) {$format=$_POST['format'];} }
	if (empty($_GET['modele']) and empty($_POST['modele'])) {$modele="";}
	    else { if (isset($_GET['modele'])) {$modele=$_GET['modele'];} if (isset($_POST['modele'])) {$modele=$_POST['modele'];} }
	if (empty($_GET['action_model']) and empty($_POST['action_model'])) {$action_model="";}
	    else { if (isset($_GET['action_model'])) {$action_model=$_GET['action_model'];} if (isset($_POST['action_model'])) {$action_model=$_POST['action_model'];} }
	if (empty($_GET['modele_action']) and empty($_POST['modele_action'])) {$modele_action='';}
	    else { if (isset($_GET['modele_action'])) {$modele_action=$_GET['modele_action'];} if (isset($_POST['modele_action'])) {$modele_action=$_POST['modele_action'];} }

	if (empty($_GET['id_model_bulletin']) and empty($_POST['id_model_bulletin'])) {$id_model_bulletin="";}
	    else { if (isset($_GET['id_model_bulletin'])) {$id_model_bulletin=$_GET['id_model_bulletin'];} if (isset($_POST['id_model_bulletin'])) {$id_model_bulletin=$_POST['id_model_bulletin'];} }

	if (empty($_GET['active_bloc_datation']) and empty($_POST['active_bloc_datation'])) { $active_bloc_datation = ''; }
	   else { if (isset($_GET['active_bloc_datation'])) { $active_bloc_datation = $_GET['active_bloc_datation']; } if (isset($_POST['active_bloc_datation'])) { $active_bloc_datation = $_POST['active_bloc_datation']; } }
	if (empty($_GET['active_bloc_eleve']) and empty($_POST['active_bloc_eleve'])) { $active_bloc_eleve = ''; }
	   else { if (isset($_GET['active_bloc_eleve'])) { $active_bloc_eleve = $_GET['active_bloc_eleve']; } if (isset($_POST['active_bloc_eleve'])) { $active_bloc_eleve = $_POST['active_bloc_eleve']; } }
	if (empty($_GET['active_bloc_adresse_parent']) and empty($_POST['active_bloc_adresse_parent'])) { $active_bloc_adresse_parent = ''; }
	   else { if (isset($_GET['active_bloc_adresse_parent'])) { $active_bloc_adresse_parent = $_GET['active_bloc_adresse_parent']; } if (isset($_POST['active_bloc_adresse_parent'])) { $active_bloc_adresse_parent = $_POST['active_bloc_adresse_parent']; } }
	if (empty($_GET['active_bloc_absence']) and empty($_POST['active_bloc_absence'])) { $active_bloc_absence = ''; }
	   else { if (isset($_GET['active_bloc_absence'])) { $active_bloc_absence = $_GET['active_bloc_absence']; } if (isset($_POST['active_bloc_absence'])) { $active_bloc_absence = $_POST['active_bloc_absence']; } }
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
	if (empty($_GET['hauteur_entete_moyenne_general']) and empty($_POST['hauteur_entete_moyenne_general'])) { $hauteur_entete_moyenne_general = ''; }
	   else { if (isset($_GET['hauteur_entete_moyenne_general'])) { $hauteur_entete_moyenne_general = $_GET['hauteur_entete_moyenne_general']; } if (isset($_POST['hauteur_entete_moyenne_general'])) { $hauteur_entete_moyenne_general = $_POST['hauteur_entete_moyenne_general']; } }
	if (empty($_GET['X_avis_cons']) and empty($_POST['X_avis_cons'])) { $X_avis_cons = ''; }
	   else { if (isset($_GET['X_avis_cons'])) { $X_avis_cons = $_GET['X_avis_cons']; } if (isset($_POST['X_avis_cons'])) { $X_avis_cons = $_POST['X_avis_cons']; } }
	if (empty($_GET['cadre_avis_cons']) and empty($_POST['cadre_avis_cons'])) { $cadre_avis_cons = ''; }
	   else { if (isset($_GET['cadre_avis_cons'])) { $cadre_avis_cons = $_GET['cadre_avis_cons']; } if (isset($_POST['cadre_avis_cons'])) { $cadre_avis_cons = $_POST['cadre_avis_cons']; } }
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
	if (empty($_GET['entente_mel']) and empty($_POST['entente_mel'])) { $entente_mel = ''; }
	   else { if (isset($_GET['entente_mel'])) { $entente_mel = $_GET['entente_mel']; } if (isset($_POST['entente_mel'])) { $entente_mel = $_POST['entente_mel']; } }
	if (empty($_GET['entente_tel']) and empty($_POST['entente_tel'])) { $entente_tel = ''; }
	   else { if (isset($_GET['entente_tel'])) { $entente_tel = $_GET['entente_tel']; } if (isset($_POST['entente_tel'])) { $entente_tel = $_POST['entente_tel']; } }
	if (empty($_GET['entente_fax']) and empty($_POST['entente_fax'])) { $entente_fax = ''; }
	   else { if (isset($_GET['entente_fax'])) { $entente_fax = $_GET['entente_fax']; } if (isset($_POST['entente_fax'])) { $entente_fax = $_POST['entente_fax']; } }
	if (empty($_GET['L_max_logo']) and empty($_POST['L_max_logo'])) { $L_max_logo = ''; }
	   else { if (isset($_GET['L_max_logo'])) { $L_max_logo = $_GET['L_max_logo']; } if (isset($_POST['L_max_logo'])) { $L_max_logo = $_POST['L_max_logo']; } }
	if (empty($_GET['H_max_logo']) and empty($_POST['H_max_logo'])) { $H_max_logo = ''; }
	   else { if (isset($_GET['H_max_logo'])) { $H_max_logo = $_GET['H_max_logo']; } if (isset($_POST['H_max_logo'])) { $H_max_logo = $_POST['H_max_logo']; } }
	if (empty($_GET['toute_moyenne_meme_col']) and empty($_POST['toute_moyenne_meme_col'])) { $toute_moyenne_meme_col = ''; }
	   else { if (isset($_GET['toute_moyenne_meme_col'])) { $toute_moyenne_meme_col = $_GET['toute_moyenne_meme_col']; } if (isset($_POST['toute_moyenne_meme_col'])) { $toute_moyenne_meme_col = $_POST['toute_moyenne_meme_col']; } }
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

// fin Christian

// début ajouter/modifier/supprimer des modèles
if(!empty($valide_modif_model))
 {
	if($action_model==='ajouter') { $requete_model='INSERT INTO '.$prefix_base.'model_bulletin (nom_model_bulletin,active_bloc_datation,active_bloc_eleve,active_bloc_adresse_parent,active_bloc_absence,active_bloc_note_appreciation,active_bloc_avis_conseil,active_bloc_chef,active_photo,active_coef_moyenne,active_nombre_note,active_nombre_note_case,active_moyenne,active_moyenne_eleve,active_moyenne_classe,active_moyenne_min,active_moyenne_max,active_regroupement_cote,active_entete_regroupement,active_moyenne_regroupement,active_rang,active_graphique_niveau,active_appreciation,affiche_doublement,affiche_date_naissance,affiche_dp,affiche_nom_court,affiche_effectif_classe,affiche_numero_impression,caractere_utilse,X_parent,Y_parent,X_eleve,Y_eleve,cadre_eleve,X_datation_bul,Y_datation_bul,cadre_datation_bul,hauteur_info_categorie,X_note_app,Y_note_app,longeur_note_app,hauteur_note_app,largeur_coef_moyenne,largeur_nombre_note,largeur_d_une_moyenne,largeur_niveau,largeur_rang,X_absence,Y_absence,hauteur_entete_moyenne_general,X_avis_cons,Y_avis_cons,longeur_avis_cons,hauteur_avis_cons,cadre_avis_cons,X_sign_chef,Y_sign_chef,longeur_sign_chef,hauteur_sign_chef,cadre_sign_chef,affiche_filigrame,texte_filigrame,affiche_logo_etab,entente_mel,entente_tel,entente_fax,L_max_logo,H_max_logo, toute_moyenne_meme_col,active_reperage_eleve,couleur_reperage_eleve1,couleur_reperage_eleve2,couleur_reperage_eleve3,couleur_categorie_entete,couleur_categorie_entete1,couleur_categorie_entete2,couleur_categorie_entete3,couleur_categorie_cote,couleur_categorie_cote1,couleur_categorie_cote2,couleur_categorie_cote3,couleur_moy_general,couleur_moy_general1,couleur_moy_general2,couleur_moy_general3,titre_entete_matiere,titre_entete_coef,titre_entete_nbnote,titre_entete_rang,titre_entete_appreciation,active_coef_sousmoyene,arrondie_choix,nb_chiffre_virgule,chiffre_avec_zero,autorise_sous_matiere,affichage_haut_responsable,entete_model_bulletin,ordre_entete_model_bulletin,affiche_etab_origine,imprime_pour)
					VALUES ("'.$nom_model_bulletin.'", "'.$active_bloc_datation.'", "'.$active_bloc_eleve.'", "'.$active_bloc_adresse_parent.'", "'.$active_bloc_absence.'", "'.$active_bloc_note_appreciation.'", "'.$active_bloc_avis_conseil.'", "'.$active_bloc_chef.'", "'.$active_photo.'", "'.$active_coef_moyenne.'", "'.$active_nombre_note.'", "'.$active_nombre_note_case.'", "'.$active_moyenne.'", "'.$active_moyenne_eleve.'", "'.$active_moyenne_classe.'", "'.$active_moyenne_min.'", "'.$active_moyenne_max.'", "'.$active_regroupement_cote.'", "'.$active_entete_regroupement.'", "'.$active_moyenne_regroupement.'", "'.$active_rang.'", "'.$active_graphique_niveau.'", "'.$active_appreciation.'", "'.$affiche_doublement.'", "'.$affiche_date_naissance.'", "'.$affiche_dp.'", "'.$affiche_nom_court.'", "'.$affiche_effectif_classe.'", "'.$affiche_numero_impression.'", "'.$caractere_utilse.'", "'.$X_parent.'", "'.$Y_parent.'", "'.$X_eleve.'", "'.$Y_eleve.'", "'.$cadre_eleve.'", "'.$X_datation_bul.'", "'.$Y_datation_bul.'", "'.$cadre_datation_bul.'", "'.$hauteur_info_categorie.'", "'.$X_note_app.'", "'.$Y_note_app.'", "'.$longeur_note_app.'", "'.$hauteur_note_app.'", "'.$largeur_coef_moyenne.'", "'.$largeur_nombre_note.'", "'.$largeur_d_une_moyenne.'", "'.$largeur_niveau.'", "'.$largeur_rang.'", "'.$X_absence.'", "'.$Y_absence.'", "'.$hauteur_entete_moyenne_general.'", "'.$X_avis_cons.'", "'.$Y_avis_cons.'", "'.$longeur_avis_cons.'", "'.$hauteur_avis_cons.'", "'.$cadre_avis_cons.'", "'.$X_sign_chef.'", "'.$Y_sign_chef.'", "'.$longeur_sign_chef.'", "'.$hauteur_sign_chef.'", "'.$cadre_sign_chef.'", "'.$affiche_filigrame.'","'.$texte_filigrame.'","'.$affiche_logo_etab.'","'.$entente_mel.'","'.$entente_tel.'","'.$entente_fax.'","'.$L_max_logo.'","'.$H_max_logo.'","'.$toute_moyenne_meme_col.'","'.$active_reperage_eleve.'", "'.$couleur_reperage_eleve1.'", "'.$couleur_reperage_eleve2.'", "'.$couleur_reperage_eleve3.'", "'.$couleur_categorie_entete.'", "'.$couleur_categorie_entete1.'", "'.$couleur_categorie_entete2.'", "'.$couleur_categorie_entete3.'", "'.$couleur_categorie_cote.'", "'.$couleur_categorie_cote1.'", "'.$couleur_categorie_cote2.'", "'.$couleur_categorie_cote3.'", "'.$couleur_moy_general.'", "'.$couleur_moy_general1.'", "'.$couleur_moy_general2.'", "'.$couleur_moy_general3.'", "'.$titre_entete_matiere.'", "'.$titre_entete_coef.'", "'.$titre_entete_nbnote.'", "'.$titre_entete_rang.'", "'.$titre_entete_appreciation.'", "'.$active_coef_sousmoyene.'", "'.$arrondie_choix.'", "'.$nb_chiffre_virgule.'", "'.$chiffre_avec_zero.'", "'.$autorise_sous_matiere.'", "'.$affichage_haut_responsable.'", "'.$entete_model_bulletin.'", "'.$ordre_entete_model_bulletin.'", "'.$affiche_etab_origine.'", "'.$imprime_pour.'")'; }
	if($action_model==='modifier') { $requete_model='UPDATE '.$prefix_base.'model_bulletin SET nom_model_bulletin="'.$nom_model_bulletin.'", active_bloc_datation="'.$active_bloc_datation.'", active_bloc_eleve="'.$active_bloc_eleve.'", active_bloc_adresse_parent="'.$active_bloc_adresse_parent.'", active_bloc_absence="'.$active_bloc_absence.'", active_bloc_note_appreciation="'.$active_bloc_note_appreciation.'", active_bloc_avis_conseil="'.$active_bloc_avis_conseil.'", active_bloc_chef="'.$active_bloc_chef.'", active_photo="'.$active_photo.'", active_coef_moyenne="'.$active_coef_moyenne.'", active_nombre_note="'.$active_nombre_note.'", active_nombre_note_case="'.$active_nombre_note_case.'", active_moyenne="'.$active_moyenne.'", active_moyenne_eleve="'.$active_moyenne_eleve.'", active_moyenne_classe="'.$active_moyenne_classe.'", active_moyenne_min="'.$active_moyenne_min.'", active_moyenne_max="'.$active_moyenne_max.'", active_regroupement_cote="'.$active_regroupement_cote.'", active_entete_regroupement="'.$active_entete_regroupement.'", active_moyenne_regroupement="'.$active_moyenne_regroupement.'", active_rang="'.$active_rang.'", active_graphique_niveau="'.$active_graphique_niveau.'", active_appreciation="'.$active_appreciation.'", affiche_doublement="'.$affiche_doublement.'", affiche_date_naissance="'.$affiche_date_naissance.'", affiche_dp="'.$affiche_dp.'", affiche_nom_court="'.$affiche_nom_court.'", affiche_effectif_classe="'.$affiche_effectif_classe.'", affiche_numero_impression="'.$affiche_numero_impression.'", caractere_utilse="'.$caractere_utilse.'", X_parent="'.$X_parent.'", Y_parent="'.$Y_parent.'", X_eleve="'.$X_eleve.'", Y_eleve="'.$Y_eleve.'", cadre_eleve="'.$cadre_eleve.'", X_datation_bul="'.$X_datation_bul.'", Y_datation_bul="'.$Y_datation_bul.'", cadre_datation_bul="'.$cadre_datation_bul.'", hauteur_info_categorie="'.$hauteur_info_categorie.'", X_note_app="'.$X_note_app.'", Y_note_app="'.$Y_note_app.'", longeur_note_app="'.$longeur_note_app.'", hauteur_note_app="'.$hauteur_note_app.'", largeur_coef_moyenne="'.$largeur_coef_moyenne.'", largeur_nombre_note="'.$largeur_nombre_note.'", largeur_d_une_moyenne="'.$largeur_d_une_moyenne.'", largeur_niveau="'.$largeur_niveau.'", largeur_rang="'.$largeur_rang.'", X_absence="'.$X_absence.'", Y_absence="'.$Y_absence.'", hauteur_entete_moyenne_general="'.$hauteur_entete_moyenne_general.'", X_avis_cons="'.$X_avis_cons.'", Y_avis_cons="'.$Y_avis_cons.'", longeur_avis_cons="'.$longeur_avis_cons.'", hauteur_avis_cons="'.$hauteur_avis_cons.'", cadre_avis_cons="'.$cadre_avis_cons.'",
					 X_sign_chef="'.$X_sign_chef.'", Y_sign_chef="'.$Y_sign_chef.'", longeur_sign_chef="'.$longeur_sign_chef.'", hauteur_sign_chef="'.$hauteur_sign_chef.'", cadre_sign_chef="'.$cadre_sign_chef.'", affiche_filigrame="'.$affiche_filigrame.'", texte_filigrame="'.$texte_filigrame.'", affiche_logo_etab="'.$affiche_logo_etab.'", entente_mel="'.$entente_mel.'", entente_tel="'.$entente_tel.'", entente_fax="'.$entente_fax.'", L_max_logo="'.$L_max_logo.'", H_max_logo="'.$H_max_logo.'", toute_moyenne_meme_col="'.$toute_moyenne_meme_col.'", active_reperage_eleve="'.$active_reperage_eleve.'", couleur_reperage_eleve1="'.$couleur_reperage_eleve1.'", couleur_reperage_eleve2="'.$couleur_reperage_eleve2.'", couleur_reperage_eleve3="'.$couleur_reperage_eleve3.'", couleur_categorie_entete="'.$couleur_categorie_entete.'", couleur_categorie_entete1="'.$couleur_categorie_entete1.'", couleur_categorie_entete2="'.$couleur_categorie_entete2.'", couleur_categorie_entete3="'.$couleur_categorie_entete3.'", couleur_categorie_cote="'.$couleur_categorie_cote.'", couleur_categorie_cote1="'.$couleur_categorie_cote1.'", couleur_categorie_cote2="'.$couleur_categorie_cote2.'", couleur_categorie_cote3="'.$couleur_categorie_cote3.'", couleur_moy_general="'.$couleur_moy_general.'", couleur_moy_general1="'.$couleur_moy_general1.'", couleur_moy_general2="'.$couleur_moy_general2.'", couleur_moy_general3="'.$couleur_moy_general3.'", titre_entete_matiere="'.$titre_entete_matiere.'", titre_entete_coef="'.$titre_entete_coef.'", titre_entete_nbnote="'.$titre_entete_nbnote.'", titre_entete_rang="'.$titre_entete_rang.'", titre_entete_appreciation="'.$titre_entete_appreciation.'", active_coef_sousmoyene="'.$active_coef_sousmoyene.'", arrondie_choix="'.$arrondie_choix.'", nb_chiffre_virgule="'.$nb_chiffre_virgule.'", chiffre_avec_zero="'.$chiffre_avec_zero.'", autorise_sous_matiere="'.$autorise_sous_matiere.'", affichage_haut_responsable="'.$affichage_haut_responsable.'", entete_model_bulletin="'.$entete_model_bulletin.'", ordre_entete_model_bulletin="'.$ordre_entete_model_bulletin.'", affiche_etab_origine="'.$affiche_etab_origine.'", imprime_pour = "'.$imprime_pour.'" WHERE id_model_bulletin="'.$id_model_bulletin.'" LIMIT 1'; }
	if($id_model_bulletin!='1') { if($action_model==='supprimer') { $requete_model='DELETE FROM '.$prefix_base.'model_bulletin WHERE id_model_bulletin ="'.$id_model_bulletin.'"  LIMIT 1'; } }
        mysql_query($requete_model) or die('Erreur SQL !'.$requete_model.'<br>'.mysql_error());
 }
// fin ajouter/modifier/supprimer des modèles

//**************** EN-TETE *********************
$titre_page = "Edition des bulletins";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiProfImprBul")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";
//if (!isset($id_classe)) {

	//modification christian pour le choix des bulletins au format PDF
	?> | <?php if(empty($format) or $format != 'pdf') { ?><a href='index.php?format=pdf'>Impression au format PDF</a><?php } else { ?><a href='index.php?format='>Impression au format HTML</a><?php } ?> | <?php
	//fin de modification

       //modification Christian CHAPEL
	if($format === 'pdf' and ( empty($bt_select_periode)) or !empty($creer_pdf) and $modele ==='' and !isset($classe[0]))
	{
		?>
		<form method="post" action="index.php" name="imprime_pdf">
		  <fieldset style="width: 90%; margin: auto;"><legend>S&eacute;lection</legend>
		  <center>
			<table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">
			  <tbody>
			    <tr>
			      <td align="right" nowrap="nowrap" valign="middle" colspan="1" rowspan="2" >
				<select name="classe[]" size="6" multiple="multiple" tabindex="3">
				  <optgroup label="----- Listes des classes -----">
				    <?php
					if( $_SESSION['statut'] === 'scolarite' ){ //n'affiche que les classes du profil scolarité
						$login_scolarite = $_SESSION['login'];
						$requete_classe = mysql_query("SELECT c.classe, c.nom_complet, c.id, jsc.login, jsc.id_classe, p.id_classe FROM ".$prefix_base."classes c, ".$prefix_base."j_scol_classes jsc, ".$prefix_base."periodes p WHERE ( jsc.login = '".$login_scolarite."' AND jsc.id_classe = c.id AND p.id_classe = c.id) GROUP BY p.id_classe ORDER BY nom_complet ASC");
					} else {
				                        $requete_classe = mysql_query('SELECT * FROM '.$prefix_base.'classes, '.$prefix_base.'j_eleves_professeurs, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'periodes WHERE ('.$prefix_base.'j_eleves_professeurs.professeur="'.$_SESSION['login'].'" AND '.$prefix_base.'j_eleves_professeurs.professeur.login = '.$prefix_base.'j_eleves_classes.login AND '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id) AND '.$prefix_base.'periodes.id_classe = '.$prefix_base.'classes.id  GROUP BY id_classe ORDER BY '.$prefix_base.'classes.classe');
						} 
			  		while ($donner_classe = mysql_fetch_array($requete_classe))
				  	 {
						$requete_cpt_nb_eleve_1 =  mysql_query('SELECT count(*) FROM '.$prefix_base.'eleves, '.$prefix_base.'classes, '.$prefix_base.'j_eleves_classes WHERE '.$prefix_base.'classes.id = "'.$donner_classe['id_classe'].'" AND '.$prefix_base.'j_eleves_classes.id_classe='.$prefix_base.'classes.id AND '.$prefix_base.'j_eleves_classes.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login');
						$requete_cpt_nb_eleve = mysql_num_rows($requete_cpt_nb_eleve_1);
					   ?><option value="<?php echo $donner_classe['id_classe']; ?>" <?php if(!empty($classe) and in_array($donner_classe['id_classe'], $classe)) { ?>selected="selected"<?php } ?>><?php echo $donner_classe['nom_complet']; ?> (eff. <?php echo $requete_cpt_nb_eleve; ?>)</option><?php
					 }
					?>
				  </optgroup>
				  </select>
			      </td>
			      <td align="center" nowrap="nowrap" valign="middle">
				<select tabindex="5" name="periode[]" size="4" multiple="multiple">
				  <?php
					// sélection des période disponible
			                        $requete_periode = mysql_query('SELECT nom_periode FROM '.$prefix_base.'periodes GROUP BY '.$prefix_base.'periodes.nom_periode ORDER BY '.$prefix_base.'periodes.nom_periode ASC');
				  		while($donner_periode = mysql_fetch_array($requete_periode))
					  	 {
						   ?><option value="<?php echo $donner_periode['nom_periode']; ?>" <?php if(!empty($periode) and in_array($donner_periode['nom_periode'], $periode)) { ?> selected="selected"<?php } ?>><?php echo ucfirst($donner_periode['nom_periode']); ?></option><?php
						 }
				  ?>
				  </select><input type="checkbox" name="periode_ferme" id="periode_ferme" value="1" <?php if( isset($periode_ferme) and $periode_ferme === '1' ) { ?>checked="checked"<?php } ?> title="Imprimer seulement les période fermée" alt="Imprimer seulement les période fermée" />
			      </td>
			      <td align="left" nowrap="nowrap" valign="middle" colspan="1" rowspan="2" >
				<select name="eleve[]" size="6" multiple="multiple" tabindex="4">
				  <optgroup label="----- Listes des &eacute;l&egrave;ves -----">
				    <?php
					// sélection des id eleves sélectionné.
					if(!empty($classe[0]))
					{
						$cpt_classe_selec = 0; $selection_classe = "";
						while(!empty($classe[$cpt_classe_selec])) { if($cpt_classe_selec == 0) { $selection_classe = $prefix_base."j_eleves_classes.id_classe = ".$classe[$cpt_classe_selec]; } else { $selection_classe = $selection_classe." OR ".$prefix_base."j_eleves_classes.id_classe = ".$classe[$cpt_classe_selec]; } $cpt_classe_selec = $cpt_classe_selec + 1; }
			                        $requete_eleve = mysql_query('SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes WHERE ('.$selection_classe.') AND '.$prefix_base.'j_eleves_classes.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'eleves.nom ASC');
				  		while ($donner_eleve = mysql_fetch_array($requete_eleve))
					  	 {
						   ?><option value="<?php echo $donner_eleve['login']; ?>" <?php if(!empty($eleve) and in_array($donner_eleve['login'], $eleve)) { ?> selected="selected"<?php } ?>><?php echo strtoupper($donner_eleve['nom'])." ".ucfirst($donner_eleve['prenom']); ?></option><?php
						 }
					}
					?>
				     <?php if(empty($classe[0]) and empty($eleve[0])) { ?><option value="" disabled="disabled">Vide</option><?php } ?>
				  </optgroup>
				  </select>
				</td>
			    </tr>
			    <tr>
			      <td align="center" nowrap="nowrap" valign="middle"><input name="selection_eleve" id="selection_eleve" value="Liste élève >" onclick="this.form.submit();this.disabled=true;this.value='En cours'" type="submit" title="Transfère les élèves des classe sélectionné" alt="Transfère les élèves des classe sélectionné" /></td>
			    </tr>
			  </tbody>
		</table>
			<?php if ( $message_erreur != '' ) { ?><span style="color: #FF0000; font-weight: bold;"><?php echo $message_erreur; ?></span><?php } ?>
		  <br />Modèle
		  <select tabindex="5" name="type_bulletin">
		  <?php
			// sélection des modèle des bulletins.
	                        $requete_model = mysql_query('SELECT id_model_bulletin, nom_model_bulletin FROM '.$prefix_base.'model_bulletin ORDER BY '.$prefix_base.'model_bulletin.nom_model_bulletin ASC');
		  		while($donner_model = mysql_fetch_array($requete_model))
			  	 {
				   ?><option value="<?php echo $donner_model['id_model_bulletin']; ?>" <?php if(!empty($type_bulletin) and $type_bulletin===$donner_model['id_model_bulletin']) { ?> selected="selected"<?php } ?>><?php echo ucfirst($donner_model['nom_model_bulletin']); ?></option><?php
				 }
		  ?>
		  </select>&nbsp;<a href="index.php?modele=aff">Utiliser le gestionnaire de modèle</a>
		  <br /><br />
	 	  <input type="hidden" name="format" value="<?php echo $format; ?>" />
		  <input type="submit" id="creer_pdf" name="creer_pdf" value="Créer le PDF" />
		  </center>
		  </fieldset>
		</form>

		<?php
	}
	// fin de modification de la sélection pour le PDF Christian

	//modification christian gestion des modèles
	if($modele==='aff')
	 {
		if($modele==='aff' and (empty($action_model) or !empty($valide_modif_model))) //affiche la liste des modèles
		 {
		     ?>
		     <center>
		     <h2>Gestion des modèle de Bulletin</h2>
		     <table style="text-align: left; width: 400px; border: 1px solid #74748F;" border="0" cellpadding="1" cellspacing="1">
		      <tbody>
		       <tr>
		         <td style="vertical-align: center; white-space: nowrap; text-align: center; width: 100%;" colspan="3" rowspan="1"><a href="index.php?modele=aff&amp;action_model=ajouter">Ajouter un nouveau modèle</a></td>
		       </tr>
		       <tr>
		         <td style="vertical-align: center; white-space: nowrap; text-align: center; width: 50%; background: #0069C8; font: normal 10pt Arial; color: #E0EDF1;">Modèle</td>
		         <td style="vertical-align: center; white-space: nowrap; text-align: center; width: 25%; background: #0069C8; font: normal 10pt Arial; color: #E0EDF1;">Modifier</td>
		         <td style="vertical-align: center; white-space: nowrap; text-align: center; width: 25%; background: #0069C8; font: normal 10pt Arial; color: #E0EDF1;">Supprimer</td>
		       </tr>
		       <?php
			$i = '1';
			$requete_model = mysql_query('SELECT id_model_bulletin, nom_model_bulletin FROM '.$prefix_base.'model_bulletin');
    			while($data_model = mysql_fetch_array($requete_model)) { 
		       		if ($i === '1') { $i = '2'; $couleur_cellule = '#88C7FF'; } else { $couleur_cellule = '#B7DDFF'; $i = '1'; } ?>
		       <tr>
		         <td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%; background: <?php echo $couleur_cellule; ?>;"><?php echo ucfirst($data_model['nom_model_bulletin']); ?></td>
		         <td style="vertical-align: center; white-space: nowrap; text-align: center; width: 25%; background: <?php echo $couleur_cellule; ?>;">[<a href="index.php?modele=aff&amp;action_model=modifier&amp;modele_action=<?php echo $data_model['id_model_bulletin']; ?>">Modifier</a>]</td>
		         <td style="vertical-align: center; white-space: nowrap; text-align: center; width: 25%; background: <?php echo $couleur_cellule; ?>;"><?php if($data_model['id_model_bulletin']!='1') { ?>[<a href="index.php?modele=aff&amp;action_model=supprimer&amp;modele_action=<?php echo $data_model['id_model_bulletin']; ?>">Supprimer</a>]<?php } ?></td>
		       </tr>
		       <?php } ?>
		       <tr>
		         <td style="vertical-align: center; white-space: nowrap; text-align: center; width: 100%;" colspan="3" rowspan="1"><a href="index.php?modele=aff&amp;action_model=ajouter">Ajouter un nouveau modèle</a></td>
		       </tr>
		      </tbody>
		     </table>
		     </center>
		     <?php
		 }
		if($modele==='aff' and ($action_model==='ajouter' or $action_model==='modifier' or $action_model==='supprimer') and empty($valide_modif_model)) //affiche la liste des modèles
		 { 
			if(empty($modele_action)) { $model_bulletin=''; } else { $model_bulletin=$modele_action; }
			if($action_model==='ajouter' or $action_model==='modifier') {
			if($action_model==='ajouter') { $requete_model = mysql_query('SELECT * FROM '.$prefix_base.'model_bulletin WHERE id_model_bulletin="1"'); }
			if($action_model==='modifier') { $requete_model = mysql_query('SELECT * FROM '.$prefix_base.'model_bulletin WHERE id_model_bulletin="'.$model_bulletin.'"'); }
			while($donner_model = mysql_fetch_array($requete_model))
			 {
				if($action_model==='modifier') { $id_model_bulletin = $donner_model['id_model_bulletin']; } // id du modèle
				if($action_model==='modifier') { $nom_model_bulletin = $donner_model['nom_model_bulletin']; } // nom du modèle
				$active_bloc_datation = $donner_model['active_bloc_datation']; // afficher le cadre les informations datation du bulletin
				$active_bloc_eleve = $donner_model['active_bloc_eleve']; // afficher le cadre sur les informations élève
				$active_bloc_adresse_parent = $donner_model['active_bloc_adresse_parent']; // afficher le cadre adresse des parents
				$active_bloc_absence = $donner_model['active_bloc_absence']; // afficher le cadre absences de l'élève
				$active_bloc_note_appreciation = $donner_model['active_bloc_note_appreciation']; // afficher les notes et appréciations
				$active_bloc_avis_conseil = $donner_model['active_bloc_avis_conseil']; // afficher les avis du conseil de classe
				$active_bloc_chef = $donner_model['active_bloc_chef']; // fait - afficher la signature du chef
				$active_photo = $donner_model['active_photo']; // fait - afficher la photo de l'élève
				$active_coef_moyenne = $donner_model['active_coef_moyenne']; // fait - afficher le coéficient des moyenne par matière
				$active_nombre_note = $donner_model['active_nombre_note']; // fait - afficher le nombre de note par matière sous la moyenne de l'élève
				$active_nombre_note_case = $donner_model['active_nombre_note_case']; // fait - afficher le nombre de note par matière
				$active_moyenne = $donner_model['active_moyenne']; // fait - afficher les moyennes
				$active_moyenne_eleve = $donner_model['active_moyenne_eleve']; // fait - afficher la moyenne de l'élève
				$active_moyenne_classe = $donner_model['active_moyenne_classe']; // fait - afficher les moyennes de la classe
				$active_moyenne_min = $donner_model['active_moyenne_min']; // fait - afficher les moyennes minimum
				$active_moyenne_max = $donner_model['active_moyenne_max']; // fait - afficher les moyennes maximum
				$active_regroupement_cote = $donner_model['active_regroupement_cote']; // fait - afficher le nom des regroupement sur le coté
				$active_entete_regroupement = $donner_model['active_entete_regroupement']; // fait - afficher les entête des regroupement
				$active_moyenne_regroupement = $donner_model['active_moyenne_regroupement']; // fait - afficher les moyennes des regroupement
				$active_rang = $donner_model['active_rang']; // fait - afficher le rang de l'élève
				$active_graphique_niveau = $donner_model['active_graphique_niveau']; // fait - afficher le graphique des niveaux
				$active_appreciation = $donner_model['active_appreciation']; // fait - afficher les appréciations des professeurs
				$affiche_doublement = $donner_model['affiche_doublement']; // affiche si l'élève à doubler
				$affiche_date_naissance = $donner_model['affiche_date_naissance']; // affiche la date de naissance de l'élève
				$affiche_dp = $donner_model['affiche_dp']; // affiche l'état de demi pension ou extern
				$affiche_nom_court = $donner_model['affiche_nom_court']; // affiche le nom court de la classe
				$affiche_effectif_classe = $donner_model['affiche_effectif_classe']; // affiche l'effectif de la classe
				$affiche_numero_impression = $donner_model['affiche_numero_impression']; // affiche le numéro d'impression des bulletins
				$affiche_etab_origine = $donner_model['affiche_etab_origine']; // affiche l'établissement d'orignine
			 	$active_reperage_eleve = $donner_model['active_reperage_eleve']; // activé la couleur de réparage des moyenne de l'élève
				$couleur_reperage_eleve1 = $donner_model['couleur_reperage_eleve1']; // couleur 1 du repérage ci-dessus
				$couleur_reperage_eleve2 = $donner_model['couleur_reperage_eleve2']; // couleur 2 du repérage ci-dessus
				$couleur_reperage_eleve3 = $donner_model['couleur_reperage_eleve3']; // couleur 3 du repérage ci-dessus
				$couleur_categorie_entete = $donner_model['couleur_categorie_entete']; // activé la couleur de fond des catégorie entête
				$couleur_categorie_entete1 = $donner_model['couleur_categorie_entete1']; // couleur 1 du repérage ci-dessus
				$couleur_categorie_entete2 = $donner_model['couleur_categorie_entete2']; // couleur 2 du repérage ci-dessus
				$couleur_categorie_entete3 = $donner_model['couleur_categorie_entete3']; // couleur 3 du repérage ci-dessus
				$couleur_categorie_cote = $donner_model['couleur_categorie_cote']; // activé la couleur de fond des catégorie sur le coté
				$couleur_categorie_cote1 = $donner_model['couleur_categorie_cote1']; // couleur 1 du repérage ci-dessus
				$couleur_categorie_cote2 = $donner_model['couleur_categorie_cote2']; // couleur 2 du repérage ci-dessus
				$couleur_categorie_cote3 = $donner_model['couleur_categorie_cote3']; // couleur 3 du repérage ci-dessus
				$couleur_moy_general = $donner_model['couleur_moy_general']; // activer la couleur moyenne général
				$couleur_moy_general1 = $donner_model['couleur_moy_general1']; // couleur 1 de la moyenne général
				$couleur_moy_general2 = $donner_model['couleur_moy_general2']; // couleur 2 de la moyenne général
				$couleur_moy_general3 = $donner_model['couleur_moy_general3']; // couleur 3 de la moyenne général
				$titre_entete_matiere = $donner_model['titre_entete_matiere']; // texte de la colone matière
				$titre_entete_coef = $donner_model['titre_entete_coef']; // texte de la colone coéfficiant
				$titre_entete_nbnote = $donner_model['titre_entete_nbnote']; // texte de la colone nombre de note
				$titre_entete_rang = $donner_model['titre_entete_rang']; // texte de la colone rang
				$titre_entete_appreciation = $donner_model['titre_entete_appreciation']; //texte de la colone appréciation
				$entete_model_bulletin = $donner_model['entete_model_bulletin']; //choix du type d'entete des moyennes
				$ordre_entete_model_bulletin = $donner_model['ordre_entete_model_bulletin']; // ordre des entêtes tableau du bulletin
				// information paramétrage
				$caractere_utilse = $donner_model['caractere_utilse'];
				// cadre identitée parents
				$X_parent=$donner_model['X_parent']; $Y_parent=$donner_model['Y_parent'];
				$imprime_pour=$donner_model['imprime_pour'];
				// cadre identitée eleve
				$X_eleve=$donner_model['X_eleve']; $Y_eleve=$donner_model['Y_eleve'];
				$cadre_eleve=$donner_model['cadre_eleve'];
				// cadre de datation du bulletin
				$X_datation_bul=$donner_model['X_datation_bul']; $Y_datation_bul=$donner_model['Y_datation_bul'];
				$cadre_datation_bul=$donner_model['cadre_datation_bul'];
				// si les catégorie son affiché avec moyenne
				$hauteur_info_categorie=$donner_model['hauteur_info_categorie'];
				// cadre des notes et app
				$X_note_app=$donner_model['X_note_app']; $Y_note_app=$donner_model['Y_note_app']; $longeur_note_app=$donner_model['longeur_note_app']; $hauteur_note_app=$donner_model['hauteur_note_app'];
				//coef des matiere
				$largeur_coef_moyenne = $donner_model['largeur_coef_moyenne'];
				//nombre de note par matière
				$largeur_nombre_note = $donner_model['largeur_nombre_note'];
				//champ des moyennes
				$largeur_d_une_moyenne = $donner_model['largeur_d_une_moyenne'];
				//graphique de niveau
				$largeur_niveau = $donner_model['largeur_niveau'];
				//rang de l'élève
				$largeur_rang = $donner_model['largeur_rang'];
				// cadre absence
				$X_absence=$donner_model['X_absence']; $Y_absence=$donner_model['Y_absence'];
				// entete du bas contient les moyennes gérnéral
				$hauteur_entete_moyenne_general = $donner_model['hauteur_entete_moyenne_general'];
				// cadre des Avis du conseil de classe
				$X_avis_cons=$donner_model['X_avis_cons']; $Y_avis_cons=$donner_model['Y_avis_cons']; $longeur_avis_cons=$donner_model['longeur_avis_cons']; $hauteur_avis_cons=$donner_model['hauteur_avis_cons'];
				$cadre_avis_cons=$donner_model['cadre_avis_cons'];
				// cadre signature du chef
				$X_sign_chef=$donner_model['X_sign_chef']; $Y_sign_chef=$donner_model['Y_sign_chef']; $longeur_sign_chef=$donner_model['longeur_sign_chef']; $hauteur_sign_chef=$donner_model['hauteur_sign_chef'];
				$cadre_sign_chef=$donner_model['cadre_sign_chef'];
				$affiche_filigrame=$donner_model['affiche_filigrame'];
				$texte_filigrame=$donner_model['texte_filigrame'];
				$affiche_logo_etab=$donner_model['affiche_logo_etab'];
				$entente_mel=$donner_model['entente_mel'];
				$entente_tel=$donner_model['entente_tel'];
				$entente_fax=$donner_model['entente_fax'];
				$L_max_logo=$donner_model['L_max_logo'];
				$H_max_logo=$donner_model['H_max_logo'];
				$toute_moyenne_meme_col=$donner_model['toute_moyenne_meme_col'];
				$active_coef_sousmoyene=$donner_model['active_coef_sousmoyene'];
				$arrondie_choix=$donner_model['arrondie_choix'];
				$nb_chiffre_virgule=$donner_model['nb_chiffre_virgule'];
				$chiffre_avec_zero=$donner_model['chiffre_avec_zero'];
				$autorise_sous_matiere=$donner_model['autorise_sous_matiere'];
				$affichage_haut_responsable=$donner_model['affichage_haut_responsable'];
			}
		?>
		<form method="post" action="index.php?modele=aff" name="action_modele_form">
		<?php if(!isset($nom_model_bulletin)) { $nom_model_bulletin = 'Nouveau'; } ?>
		<h2>Mise en page du modèle de bulletin (<?php echo $nom_model_bulletin; ?>)</h2>
		<?php if($id_model_bulletin!='1') { ?>Nom du modèle:&nbsp;<input name="nom_model_bulletin" size="22" style="border: 1px solid #74748F;" type="text" <?php if(!empty($nom_model_bulletin)) { ?>value="<?php echo $nom_model_bulletin; ?>" <?php } ?> /><?php } else { ?>Nom du modèle: <?php echo ucfirst($nom_model_bulletin); } ?><br />
		<?php if($id_model_bulletin==='1') { ?><input type="hidden" name="id_model_bulletin" value="<?php echo $id_model_bulletin; ?>" /><input name="nom_model_bulletin" type="hidden" value="<?php echo $nom_model_bulletin; ?>" /><?php } ?>
		Nom de la police de caractère&nbsp;<input name="caractere_utilse" size="10" style="border: 1px solid #74748F;" type="text" <?php if(!empty($caractere_utilse)) { ?>value="<?php echo $caractere_utilse; ?>" <?php } ?> />&nbsp;<span style="font-weight: bold; color: rgb(255, 0, 0);">*</span><br /><span style="font-style: italic; color: rgb(255, 0, 0);">* (Attention à ne modifier seulement si la police existe sur le serveur web voir avec votre administrateur web)</span><br />
		<table style="text-align: left; width: 100%; border: 1px solid #74748F;" border="0" cellpadding="2" cellspacing="2">
		  <tbody>
		   <tr>
		      <td style="vertical-align: center; white-space: nowrap; text-align: center; width: 100%; background: #B3B7BF;" colspan="2" rowspan="1">
			  <input type="submit" id="valide_modif_model" name="valide_modif_model" value="Valider le modèle" />
		      </td>
		   </tr>
		    <tr>
		      <td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;">
	        	<div style="font-weight: bold; background: #CFCFCF;">Cadre information établissement</div>
			<input name="affiche_filigrame" id="filigrame" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_filigrame) and $affiche_filigrame==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="filigrame" style="cursor: pointer;">Filigramme</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;<label for="text_fili" style="cursor: pointer;">texte du filigrame</label>&nbsp;<input name="texte_filigrame" id="text_fili" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($texte_filigrame)) { ?>value="<?php echo $texte_filigrame; ?>" <?php } ?> /><br />
			<input name="entente_tel" id="telephone" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($entente_tel) and $entente_tel==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="telephone" style="cursor: pointer;">Téléphone</label><br />
			<input name="entente_fax" id="fax" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($entente_fax) and $entente_fax==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="fax" style="cursor: pointer;">Fax</label><br />
			<input name="entente_mel" id="courrier" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($entente_mel) and $entente_mel==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="courrier" style="cursor: pointer;">Courriel</label><br />
			Logo de l'établissement<br />
			<input name="affiche_logo_etab" id="aff_logo" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_logo_etab) and $affiche_logo_etab==='1') { ?>checked="checked"<?php } ?> />&nbsp;<label for="aff_logo" style="cursor: pointer;">Affiche le logo</label><br />
			<label for="larg_logo" style="cursor: pointer;">Largeur</label>&nbsp;<input name="L_max_logo" id="larg_logo" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($L_max_logo)) { ?>value="<?php echo $L_max_logo; ?>" <?php } ?> />mm&nbsp;/&nbsp;<label for="haut_logo" style="cursor: pointer;">Hauteur</label>&nbsp;<input name="H_max_logo" id="haut_logo" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($H_max_logo)) { ?>value="<?php echo $H_max_logo; ?>" <?php } ?> />mm&nbsp;<br /><br />
	        	<div style="font-weight: bold; background: #CFCFCF;">Cadre information identité élève</div>
			<input name="active_bloc_eleve" value="1" type="radio" <?php if(!empty($active_bloc_eleve) and $active_bloc_eleve==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activé &nbsp;<input name="active_bloc_eleve" value="0" type="radio" <?php if(empty($active_bloc_eleve) or (!empty($active_bloc_eleve) and $active_bloc_eleve!='1')) { ?>checked="checked"<?php } ?> />&nbsp;Désactiver<br />
			Positionement X&nbsp;<input name="X_eleve" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_eleve)) { ?>value="<?php echo $X_eleve; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionement Y&nbsp;<input name="Y_eleve" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_eleve)) { ?>value="<?php echo $Y_eleve; ?>" <?php } ?> />mm&nbsp;<br />
			<input name="cadre_eleve" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_eleve) and $cadre_eleve==='1') { ?>checked="checked"<?php } ?> />&nbsp;Ajouter un encadrement<br />
			<input name="active_photo" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_photo) and $active_photo==='1') { ?>checked="checked"<?php } ?> />&nbsp;la photo<br />
			<input name="affiche_doublement" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_doublement) and $affiche_doublement==='1') { ?>checked="checked"<?php } ?> />&nbsp;si doublement<br />
			<input name="affiche_date_naissance" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_date_naissance) and $affiche_date_naissance==='1') { ?>checked="checked"<?php } ?> />&nbsp;la date de naissance<br />
			<input name="affiche_dp" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_dp) and $affiche_dp==='1') { ?>checked="checked"<?php } ?> />&nbsp;le régime<br />
			<input name="affiche_nom_court" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_nom_court) and $affiche_nom_court==='1') { ?>checked="checked"<?php } ?> />&nbsp;nom court de la classe<br />
			<input name="affiche_effectif_classe" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_effectif_classe) and $affiche_effectif_classe==='1') { ?>checked="checked"<?php } ?> />&nbsp;effectif de la classe<br />
			<input name="affiche_numero_impression" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_numero_impression) and $affiche_numero_impression==='1') { ?>checked="checked"<?php } ?> />&nbsp;numéro d'impression<br />
			<input name="affiche_etab_origine" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affiche_etab_origine) and $affiche_etab_origine==='1') { ?>checked="checked"<?php } ?> />&nbsp;établissement d'origine<br /><br />

		      </td>
	              <td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;">
	        	<div style="font-weight: bold; background: #CFCFCF;">Cadre datation du bulletin</div>
			<input name="active_bloc_datation" value="1" type="radio" <?php if(!empty($active_bloc_datation) and $active_bloc_datation==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activé &nbsp;<input name="active_bloc_datation" value="0" type="radio" <?php if(empty($active_bloc_datation) or (!empty($active_bloc_datation) and $active_bloc_datation!='1')) { ?>checked="checked"<?php } ?> />&nbsp;Désactiver<br />
			Positionement X&nbsp;<input name="X_datation_bul" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_datation_bul)) { ?>value="<?php echo $X_datation_bul; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionement Y&nbsp;<input name="Y_datation_bul" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_datation_bul)) { ?>value="<?php echo $Y_datation_bul; ?>" <?php } ?> />mm&nbsp;<br />
			<input name="cadre_datation_bul" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_datation_bul) and $cadre_datation_bul==='1') { ?>checked="checked"<?php } ?> />&nbsp;Ajouter un encadrement<br /><br /><br /><br /><br /><br /><br />
	        	<div style="font-weight: bold; background: #CFCFCF;">Cadre adresse des parents</div>
			<input name="active_bloc_adresse_parent" value="1" type="radio" <?php if(!empty($active_bloc_adresse_parent) and $active_bloc_adresse_parent==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activé &nbsp;<input name="active_bloc_adresse_parent" value="0" type="radio" <?php if(empty($active_bloc_adresse_parent) or (!empty($active_bloc_adresse_parent) and $active_bloc_adresse_parent!='1')) { ?>checked="checked"<?php } ?> />&nbsp;Désactiver<br />
			Positionement X&nbsp;<input name="X_parent" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_parent)) { ?>value="<?php echo $X_parent; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionement Y&nbsp;<input name="Y_parent" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_parent)) { ?>value="<?php echo $Y_parent; ?>" <?php } ?> />mm&nbsp;<br />
			Imprimer les bulletin pour :<br />
			<input name="imprime_pour" value="1" type="radio" <?php if(!empty($imprime_pour) and $imprime_pour==='1') { ?>checked="checked"<?php } ?> />&nbsp;seulement pour le 1er responsable<br />
			<input name="imprime_pour" value="2" type="radio" <?php if(!empty($imprime_pour) and $imprime_pour==='2') { ?>checked="checked"<?php } ?> />&nbsp;le 1er et 2ème responsable s'il n'ont pas la même adresse<br />
			<input name="imprime_pour" value="3" type="radio" <?php if(!empty($imprime_pour) and $imprime_pour==='3') { ?>checked="checked"<?php } ?> />&nbsp;forcer pour le 1er et 2ème responsable<br /><br />
		      </td>
		   </tr>
		   <tr>
		      <td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;" colspan="2" rowspan="1">
			<div style="font-weight: bold; background: #CFCFCF;">Cadre note et appréciation</div>
			<input name="active_bloc_note_appreciation" value="1" type="radio" <?php if(!empty($active_bloc_note_appreciation) and $active_bloc_note_appreciation==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activé &nbsp;<input name="active_bloc_note_appreciation" value="0" type="radio" <?php if(empty($active_bloc_note_appreciation) or (!empty($active_bloc_note_appreciation) and $active_bloc_note_appreciation!='1')) { ?>checked="checked"<?php } ?> />&nbsp;Désactiver<br />
			Positionement X&nbsp;<input name="X_note_app" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_note_app)) { ?>value="<?php echo $X_note_app; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionement Y&nbsp;<input name="Y_note_app" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_note_app)) { ?>value="<?php echo $Y_note_app; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="longeur_note_app" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($longeur_note_app)) { ?>value="<?php echo $longeur_note_app; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_note_app" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_note_app)) { ?>value="<?php echo $hauteur_note_app; ?>" <?php } ?> />mm&nbsp;<br />
			Entête<br />
			&nbsp;&nbsp;&nbsp;Titre de la colone matière: <input name="titre_entete_matiere" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_matiere)) { ?>value="<?php echo $titre_entete_matiere; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;Titre de la colone coéficient: <input name="titre_entete_coef" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_coef)) { ?>value="<?php echo $titre_entete_coef; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;Titre de la colone nombre de note: <input name="titre_entete_nbnote" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_nbnote)) { ?>value="<?php echo $titre_entete_nbnote; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;Titre de la colone rang: <input name="titre_entete_rang" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_rang)) { ?>value="<?php echo $titre_entete_rang; ?>" <?php } ?> /><br />
			&nbsp;&nbsp;&nbsp;Titre de la colone apréciation: <input name="titre_entete_appreciation" size="20" style="border: 1px solid #74748F;" type="text" <?php if(!empty($titre_entete_appreciation)) { ?>value="<?php echo $titre_entete_appreciation; ?>" <?php } ?> /><br />
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
		            </select><br />
			<div style="background: #EFEFEF; font-style:italic;">Autres</div>
			<input name="active_coef_moyenne" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_coef_moyenne) and $active_coef_moyenne==='1') { ?>checked="checked"<?php } ?> />&nbsp;Coéfficient de chaque matière<br />
			&nbsp;&nbsp;&nbsp;- Largeur de la colone des coefficients&nbsp;<input name="largeur_coef_moyenne" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_coef_moyenne)) { ?>value="<?php echo $largeur_coef_moyenne; ?>" <?php } ?> />mm<br />
			&nbsp;&nbsp;&nbsp;<input name="active_coef_sousmoyene" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_coef_sousmoyene) and $active_coef_sousmoyene==='1') { ?>checked="checked"<?php } ?> />&nbsp;l'afficher sous la moyenne de l'élève<br />
			<input name="active_nombre_note_case" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_nombre_note_case) and $active_nombre_note_case==='1') { ?>checked="checked"<?php } ?> />&nbsp;Nombre de note par matière dans une case<br />
			&nbsp;&nbsp;&nbsp;- Largeur de la colone des nombre de note&nbsp;<input name="largeur_nombre_note" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_nombre_note)) { ?>value="<?php echo $largeur_nombre_note; ?>" <?php } ?> />mm<br />
			&nbsp;&nbsp;&nbsp;<input name="active_nombre_note" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_nombre_note) and $active_nombre_note==='1') { ?>checked="checked"<?php } ?> />&nbsp;l'afficher sous la moyenne de l'élève<br />
			<div style="background: #EFEFEF; font-style:italic;">Moyenne</div>
			<input name="active_moyenne" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne) and $active_moyenne==='1') { ?>checked="checked"<?php } ?> />&nbsp;Les moyennes<br />
			&nbsp;&nbsp;&nbsp;- Largeur de la colone d'une moyenne&nbsp;<input name="largeur_d_une_moyenne" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_d_une_moyenne)) { ?>value="<?php echo $largeur_d_une_moyenne; ?>" <?php } ?> />mm<br />
			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_eleve" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_eleve) and $active_moyenne_eleve==='1') { ?>checked="checked"<?php } ?> />&nbsp;Moyenne de l'élève&nbsp;&nbsp;&nbsp;(<input name="active_reperage_eleve" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_reperage_eleve) and $active_reperage_eleve==='1') { ?>checked="checked"<?php } ?> />&nbsp;Mettre un fond de couleur - R:<input name="couleur_reperage_eleve1" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_reperage_eleve1)) { ?>value="<?php echo $couleur_reperage_eleve1; ?>" <?php } ?> /> G:<input name="couleur_reperage_eleve2" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_reperage_eleve2)) { ?>value="<?php echo $couleur_reperage_eleve2; ?>" <?php } ?> /> B:<input name="couleur_reperage_eleve3" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_reperage_eleve3)) { ?>value="<?php echo $couleur_reperage_eleve3; ?>" <?php } ?> />)<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="toute_moyenne_meme_col" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($toute_moyenne_meme_col) and $toute_moyenne_meme_col==='1') { ?>checked="checked"<?php } ?> />&nbsp;Afficher Moyenne classe/min/max sous la moyenne de l'élève à condition qu'il soit cocher<br />
			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_classe" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_classe) and $active_moyenne_classe==='1') { ?>checked="checked"<?php } ?> />&nbsp;Moyenne de la classe<br />
			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_min" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_min) and $active_moyenne_min==='1') { ?>checked="checked"<?php } ?> />&nbsp;Moyenne la plus basse<br />
			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_max" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_max) and $active_moyenne_max==='1') { ?>checked="checked"<?php } ?> />&nbsp;Moyenne la plus haute<br />
			&nbsp;Arrondire les moyennes à: <input name="arrondie_choix" value="0.01" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='0.01') { ?>checked="checked"<?php } ?> />0,01 <input name="arrondie_choix" value="0.1" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='0.1') { ?>checked="checked"<?php } ?> />0,1 <input name="arrondie_choix" value="0.25" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='0.25') { ?>checked="checked"<?php } ?> />0,25 <input name="arrondie_choix" value="0.5" type="radio" <?php if(!empty($arrondie_choix) and $arrondie_choix==='0.5') { ?>checked="checked"<?php } ?> />0,5<br />
			&nbsp;Nombre de zéro après la virgule: <input name="nb_chiffre_virgule" value="2" type="radio" <?php if(!empty($nb_chiffre_virgule) and $nb_chiffre_virgule==='2') { ?>checked="checked"<?php } ?> />2  <input name="nb_chiffre_virgule" value="1" type="radio" <?php if(!empty($nb_chiffre_virgule) and $nb_chiffre_virgule==='1') { ?>checked="checked"<?php } ?> />1 - <input name="chiffre_avec_zero" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($chiffre_avec_zero) and $chiffre_avec_zero==='1') { ?>checked="checked"<?php } ?> /> ne pas affiché le "0" après la virgule<br />
			<div style="background: #EFEFEF; font-style:italic;">Autres</div>
			<input name="active_rang" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_rang) and $active_rang==='1') { ?>checked="checked"<?php } ?> />&nbsp;Rang de l'élève<br />
			&nbsp;&nbsp;&nbsp;- Largeur de la colone rang&nbsp;<input name="largeur_rang" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_rang)) { ?>value="<?php echo $largeur_rang; ?>" <?php } ?> />mm<br />
			<input name="active_graphique_niveau" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_graphique_niveau) and $active_graphique_niveau==='1') { ?>checked="checked"<?php } ?> />&nbsp;Graphique de niveau<br />
			&nbsp;&nbsp;&nbsp;- Largeur de la colone niveau&nbsp;<input name="largeur_niveau" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($largeur_niveau)) { ?>value="<?php echo $largeur_niveau; ?>" <?php } ?> />mm<br />
			<input name="active_appreciation" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_appreciation) and $active_appreciation==='1') { ?>checked="checked"<?php } ?> />&nbsp;Appréciation par matière<br />
			&nbsp;&nbsp;&nbsp;<input name="autorise_sous_matiere" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($autorise_sous_matiere) and $autorise_sous_matiere==='1') { ?>checked="checked"<?php } ?> />&nbsp;Autoriser l'affichage des sous matières<br />
			Hauteur de la moyenne général&nbsp;<input name="hauteur_entete_moyenne_general" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_entete_moyenne_general)) { ?>value="<?php echo $hauteur_entete_moyenne_general; ?>" <?php } ?> />mm<br />
			<div style="background: #EFEFEF; font-style:italic;">Regroupement:</div>
			&nbsp;&nbsp;&nbsp;<input name="active_regroupement_cote" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_regroupement_cote) and $active_regroupement_cote==='1') { ?>checked="checked"<?php } ?> />&nbsp;Nom des regroupement sur le coté&nbsp;&nbsp;&nbsp;(<input name="couleur_categorie_cote" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($couleur_categorie_cote) and $couleur_categorie_cote==='1') { ?>checked="checked"<?php } ?> />&nbsp;Mettre un fond de couleur - R:<input name="couleur_categorie_cote1" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_cote1)) { ?>value="<?php echo $couleur_categorie_cote1; ?>" <?php } ?> /> G:<input name="couleur_categorie_cote2" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_cote2)) { ?>value="<?php echo $couleur_categorie_cote2; ?>" <?php } ?> /> B:<input name="couleur_categorie_cote3" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_cote3)) { ?>value="<?php echo $couleur_categorie_cote3; ?>" <?php } ?> />)<br />
			&nbsp;&nbsp;&nbsp;<input name="active_entete_regroupement" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_entete_regroupement) and $active_entete_regroupement==='1') { ?>checked="checked"<?php } ?> />&nbsp;Nom des regroupement en entête&nbsp;&nbsp;&nbsp;(<input name="couleur_categorie_entete" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($couleur_categorie_entete) and $couleur_categorie_entete==='1') { ?>checked="checked"<?php } ?> />&nbsp;Mettre un fond de couleur - R:<input name="couleur_categorie_entete1" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_entete1)) { ?>value="<?php echo $couleur_categorie_entete1; ?>" <?php } ?> /> G:<input name="couleur_categorie_entete2" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_entete2)) { ?>value="<?php echo $couleur_categorie_entete2; ?>" <?php } ?> /> B:<input name="couleur_categorie_entete3" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_categorie_entete3)) { ?>value="<?php echo $couleur_categorie_entete3; ?>" <?php } ?> />)<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Hauteur entête des catégories&nbsp;<input name="hauteur_info_categorie" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_info_categorie)) { ?>value="<?php echo $hauteur_info_categorie; ?>" <?php } ?> />mm<br />
			&nbsp;&nbsp;&nbsp;<input name="active_moyenne_regroupement" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($active_moyenne_regroupement) and $active_moyenne_regroupement==='1') { ?>checked="checked"<?php } ?> />&nbsp;Moyenne des regroupement<br />
			<div style="background: #EFEFEF; font-style:italic;">Moyenne général</div>
			&nbsp;&nbsp;&nbsp;<input name="couleur_moy_general" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($couleur_moy_general) and $couleur_moy_general==='1') { ?>checked="checked"<?php } ?> />&nbsp;Mettre un fond de couleur - R:<input name="couleur_moy_general1" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_moy_general1)) { ?>value="<?php echo $couleur_moy_general1; ?>" <?php } ?> /> G:<input name="couleur_moy_general2" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_moy_general2)) { ?>value="<?php echo $couleur_moy_general2; ?>" <?php } ?> /> B:<input name="couleur_moy_general3" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($couleur_moy_general3)) { ?>value="<?php echo $couleur_moy_general3; ?>" <?php } ?> /><br /><br />
			<div style="font-weight: bold; background: #CFCFCF;">Cadre Absence/CPE</div>
			<input name="active_bloc_absence" value="1" type="radio" <?php if(!empty($active_bloc_eleve) and $active_bloc_eleve==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activé &nbsp;<input name="active_bloc_absence" value="0" type="radio" <?php if(empty($active_bloc_absence) or (!empty($active_bloc_absence) and $active_bloc_absence!='1')) { ?>checked="checked"<?php } ?> />&nbsp;Désactiver<br />
			Positionement X&nbsp;<input name="X_absence" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_absence)) { ?>value="<?php echo $X_absence; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionement Y&nbsp;<input name="Y_absence" size="3" style="border: 1px solid #74748F;" type="text"  <?php if(!empty($Y_absence)) { ?>value="<?php echo $Y_absence; ?>" <?php } ?> />mm&nbsp;<br /><br />
		      </td>
		   </tr>
		   <tr>
		      <td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;">
	        	<div style="font-weight: bold; background: #CFCFCF;">Cadre Avis conseil de classe</div>
			<input name="active_bloc_avis_conseil" value="1" type="radio" <?php if(!empty($active_bloc_avis_conseil) and $active_bloc_avis_conseil==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activé &nbsp;<input name="active_bloc_avis_conseil" value="0" type="radio" <?php if(empty($active_bloc_avis_conseil) or (!empty($active_bloc_avis_conseil) and $active_bloc_avis_conseil!='1')) { ?>checked="checked"<?php } ?> />&nbsp;Désactiver<br />
			Positionement X&nbsp;<input name="X_avis_cons" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_avis_cons)) { ?>value="<?php echo $X_avis_cons; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionement Y&nbsp;<input name="Y_avis_cons" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($Y_avis_cons)) { ?>value="<?php echo $Y_avis_cons; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="longeur_avis_cons" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($longeur_avis_cons)) { ?>value="<?php echo $longeur_avis_cons; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_avis_cons" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($hauteur_avis_cons)) { ?>value="<?php echo $hauteur_avis_cons; ?>" <?php } ?> />mm&nbsp;<br />
			<input name="cadre_avis_cons" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_avis_cons) and $cadre_avis_cons==='1') { ?>checked="checked"<?php } ?> />&nbsp;Ajouter un encadrement<br /><br />
		      </td>
	              <td style="vertical-align: top; white-space: nowrap; text-align: left; width: 50%;">
	        	<div style="font-weight: bold; background: #CFCFCF;">Cadre signature du chef</div>
			<input name="active_bloc_chef" value="1" type="radio" <?php if(!empty($active_bloc_chef) and $active_bloc_chef==='1') { ?>checked="checked"<?php } ?> />&nbsp;Activé &nbsp;<input name="active_bloc_chef" value="0" type="radio" <?php if(empty($active_bloc_chef) or (!empty($active_bloc_chef) and $active_bloc_chef!='1')) { ?>checked="checked"<?php } ?> />&nbsp;Désactiver<br />
			Positionement X&nbsp;<input name="X_sign_chef" size="3" style="border: 1px solid #74748F;" type="text" <?php if(!empty($X_sign_chef)) { ?>value="<?php echo $X_sign_chef; ?>" <?php } ?> />mm&nbsp;/&nbsp;Positionement Y&nbsp;<input name="Y_sign_chef" size="3" style="border: 1px solid #74748F;" type="text"  <?php if(!empty($Y_sign_chef)) { ?>value="<?php echo $Y_sign_chef; ?>" <?php } ?> />mm&nbsp;<br />
			Largeur du bloc&nbsp;<input name="longeur_sign_chef" size="3" style="border: 1px solid #74748F;" type="text"  <?php if(!empty($longeur_sign_chef)) { ?>value="<?php echo $longeur_sign_chef; ?>" <?php } ?> />mm&nbsp;/&nbsp;Hauteur du bloc&nbsp;<input name="hauteur_sign_chef" size="3" style="border: 1px solid #74748F;" type="text"  <?php if(!empty($hauteur_sign_chef)) { ?>value="<?php echo $hauteur_sign_chef; ?>" <?php } ?> />mm&nbsp;<br />
			<input name="affichage_haut_responsable" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($affichage_haut_responsable) and $affichage_haut_responsable==='1') { ?>checked="checked"<?php } ?> />&nbsp;Afficher l'identité du responsable de direction<br />
			<input name="cadre_sign_chef" style="border: 1px solid #74748F;" type="checkbox" value="1" <?php if(!empty($cadre_sign_chef) and $cadre_sign_chef==='1') { ?>checked="checked"<?php } ?> />&nbsp;Ajouter un encadrement<br /><br />
		      </td>
		   </tr>
		   <tr>
		      <td style="vertical-align: center; white-space: nowrap; text-align: center; width: 100%; background: #B3B7BF;" colspan="2" rowspan="1">
		 	  <?php if($action_model==='modifier') { ?><input type="hidden" name="id_model_bulletin" value="<?php echo $id_model_bulletin; ?>" /><?php } ?>
		 	  <input type="hidden" name="action_model" value="<?php echo $action_model; ?>" />
			  <input type="submit" id="valide_modif_model2" name="valide_modif_model" value="Valider le modèle" />
		      </td>
		   </tr>
		 </tbody>
		</table>
		</form>
		<?php }
		if($action_model==='supprimer' and empty($valide_modif_model)) { ?>
		<form method="post" action="index.php?modele=aff" name="action_modele_form">
		<h2>Supprimer un modèle de bulletin</h2>
		<table style="text-align: left; width: 100%; border: 1px solid #74748F;" border="0" cellpadding="2" cellspacing="2">
		  <tbody>
		   <tr>
		      <td style="vertical-align: center; white-space: nowrap; text-align: center; width: 100%; background: #B3B7BF;" colspan="2" rowspan="1">
			  <br /><span style="font-weight: bold; color: rgb(255, 0, 0);">Souhaiter vous supprimer ce modèle</span><br /><br />
		 	  <input type="hidden" name="id_model_bulletin" value="<?php echo $model_bulletin; ?>" />
		 	  <input type="hidden" name="action_model" value="<?php echo $action_model; ?>" />
			  <input type="submit" id="valide_modif_model" name="valide_modif_model" value="Oui supprimer ce modèle" />
		      </td>
		   </tr>
		 </tbody>
		</table>
		</form>
		<?php }
	 }
}


if (!isset($id_classe) and $format != 'pdf' and $modele === '') {
    if ($_SESSION["statut"] == "scolarite") {
        //$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
        $calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
    } else {
        $calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
    }

    $nombreligne = mysql_num_rows($calldata);
    if ($nombreligne > "1") {
	  echo " | Total : $nombreligne ";
	  echo "classes";
	} else {
	  echo " | Total : $nombreligne ";
	  echo "classe";
    }
    echo "</p>\n";
    echo "<p>Cliquez sur la classe pour laquelle vous souhaitez extraire les bulletins.<br />\n";

	$nb_class_par_colonne=round($nombreligne/3);
        //echo "<table width='100%' border='1'>\n";
        echo "<table width='100%'>\n";
        echo "<tr valign='top' align='center'>\n";

    $i = 0;

        echo "<td align='left'>\n";

    while ($i < $nombreligne){

		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
		}

        $ide_classe = mysql_result($calldata, $i, "id");
        $classe_liste = mysql_result($calldata, $i, "classe");
        echo "<br /><a href='index.php?id_classe=$ide_classe'>$classe_liste</a>\n";
        $i++;
    }
        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
}
if (isset($id_classe) and $format != 'pdf' and $modele === '') {
	echo "<a href=\"index.php\">Choisir une autre classe</a>|";
/*
	// On choisit le periode :
	echo "<p><b>Choisissez la période : </b></p>\n";
	include "../lib/periodes.inc.php";
	$i="1";
	while ($i < $nb_periode) {
		if ($ver_periode[$i] == "N") {
			echo "<p><b>".ucfirst($nom_periode[$i])."</b> : édition Impossible ";
			echo " ($gepiOpenPeriodLabel)";
		} else {
			echo "<p><a href='edit.php?id_classe=$id_classe&amp;periode_num=$i' target='bull'><b>".ucfirst($nom_periode[$i])."</b></a>";
			if ($ver_periode[$i] == "P")  echo " (Période partiellement close, seule la saisie des avis du conseil de classe est possible)";
			if ($ver_periode[$i] == "O")  echo " (Période entièrement close, plus aucune saisie/modification n'est possible)";
		}
		echo "</p>\n";
		$i++;
	}
*/
	echo "<p><b>Choisissez la période : </b></p>\n";
	include "../lib/periodes.inc.php";
	$i="1";
	//echo "<form name='choix' action='edit.php' target='bull' method='post'>\n";
	echo "<form name='choix' action='edit.php' target='_blank' method='post'>\n";
	//echo "<form name='choix' action='edit.php' target='bull' method='get' >\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' /> \n";
	echo "<table border='0'>\n";
	$num_per_close=0;
	$nb_per_close=0;
	while ($i < $nb_periode) {
		echo "<tr>\n";
		if ($ver_periode[$i] == "N") {
			//echo "<td style='text-align:center; color:red;'>X</td>\n";
			//echo "<td style='text-align:center; color:red;'><img src='../images/disabled.png' alt='impossible' /></td>\n";
			echo "<td style='text-align:center; color:red;'>&nbsp;</td>\n";
			echo "<td><b>".ucfirst($nom_periode[$i])."</b> : édition impossible ";
			echo " (<i>$gepiOpenPeriodLabel</i>)</td>\n";
		} else {
			//echo "<td align='center'><input type='radio' name='periode_num' id='id_periode_num' value='$i' /> </td>\n";
			//echo "<td align='center'><input type='radio' name='periode_num' id='id_periode_num' value='$i'";
			echo "<td align='center'><input type='radio' name='periode_num' value='$i'";
			if($nb_per_close==0){
				echo " checked";
			}
			echo " /> </td>\n";
			echo "<td><b>".ucfirst($nom_periode[$i])."</b>";
			if ($ver_periode[$i] == "P"){echo " (<i>Période partiellement close, seule la saisie des avis du conseil de classe est possible</i>)";}
			if ($ver_periode[$i] == "O"){echo " (<i>Période entièrement close, plus aucune saisie/modification n'est possible</i>)";}
			echo "</td>\n";
			$num_per_close=$i;
			$nb_per_close++;
		}
		echo "</tr>\n";
		$i++;
	}
	echo "</table>\n";

/*
// Je ne parviens pas à cocher la dernière période close (si elle existe)...
	if($nb_per_close>0){
		echo "<script type='text/javascript' language='javascript'>
//document.getElementById('id_periode_num').element[$num_per_close].checked=true;
//document.forms[0].periode_num[$num_per_close].checked=true;
//document.elements['choix'].elements['periode_num'][$num_per_close].checked=true;
//document.forms[0].periode_num[$num_per_close].checked=true;
radio=document.getElementById('id_periode_num');
for(i=0;i<$nb_per_close;i++){
	alert('radio['+i+']='+radio[i].value);
}
</script>\n";
	}
*/

	// AJOUTER LES AUTRES PARAMETRES
	echo "<p><b>Et les bulletins à imprimer: </b></p>\n";
	$sql="SELECT DISTINCT e.login,e.nom,e.prenom FROM j_eleves_classes jec, eleves e WHERE jec.login=e.login AND jec.id_classe='$id_classe' ORDER BY e.nom,e.prenom";
	$res_ele=mysql_query($sql);
	if(mysql_num_rows($res_ele)==0){
		echo "<p style='color:red;'>ERREUR: La classe choisie ne compterait aucun élève?</p>\n";
		echo "</form>\n";
		echo "</body>\n";
		echo "</html>\n";
		die();
	}
	else{
		/*
		echo "<p>\n";
		echo "<select name='liste_login_ele'>\n";
		echo "<option value='_CLASSE_ENTIERE_' selected>Classe entière</option>\n";
		while($lig_ele=mysql_fetch_object($res_ele)){
			echo "<option value='$lig_ele->login'>".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))."</option>\n";
		}
		echo "</select>\n";
		//echo "<br />\n";
		echo "</p>\n";
		*/

		echo "<table border='0'>\n";
		echo "<tr>\n";
		echo "<td valign='top'><input type='radio' name='selection' value='_CLASSE_ENTIERE_' onchange=\"affiche_nb_ele_select();\" checked /></td>\n";
		echo "<td valign='top'>Classe entière</td>\n";
		echo "<td valign='top'> ou </td>\n";
		//echo "</tr>\n";
		//echo "<tr>\n";
		echo "<td valign='top'><input type='radio' name='selection' id='selection_ele' value='_SELECTION_' onchange=\"affiche_nb_ele_select();\" /></td>\n";
		echo "<td valign='top'>Sélection<br />\n";
		echo "<select id='liste_login_ele' name='liste_login_ele[]' multiple='yes' size='5' onchange=\"document.getElementById('selection_ele').checked=true;affiche_nb_ele_select();\">\n";
		//echo "<option value='_CLASSE_ENTIERE_' selected>Classe entière</option>\n";

		while($lig_ele=mysql_fetch_object($res_ele)){
			echo "<option value='$lig_ele->login'>".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))."</option>\n";
		}
		echo "</select>\n";
		echo "</td>\n";
		echo "<td valign='bottom'>\n";
		echo "<div id='nb_ele_select'>\n";
		echo "&nbsp;\n";
		echo "</div>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}

	echo "<script type='text/javascript'>
	function affiche_nb_ele_select(){
		if(document.getElementById('selection_ele').checked==true){
			num=0;
			//for(i=0;i<document.forms['choix'].selection.options.length;i++){
			//	if(document.forms['choix'].selection.options[i].selected){
			for(i=0;i<document.getElementById('liste_login_ele').options.length;i++){
				if(document.getElementById('liste_login_ele').options[i].selected){
					num++;
				}
			}
		}
		else{
			num=".mysql_num_rows($res_ele).";
		}

		if(num>=2){
			document.getElementById('nb_ele_select').innerHTML=num+' élèves sélectionnés.';
		}
		else{
			document.getElementById('nb_ele_select').innerHTML=num+' élève sélectionné.';
		}
	}
</script>\n";

	echo "<table border='0'>\n";
	echo "<tr><td valign='top'><input type='checkbox' name='un_seul_bull_par_famille' value='oui' /></td><td>Ne pas imprimer de bulletin pour le deuxième parent<br />(<i>même dans le cas de parents séparés</i>).</td></tr>\n";


    if(!getSettingValue("bull_intitule_app")){
		$bull_intitule_app="Appréciations/Conseils";
	}
	else{
		$bull_intitule_app=getSettingValue("bull_intitule_app");
	}


/*
	echo "<tr><td valign='top'><input type='checkbox' name='ne_pas_afficher_moy_gen' value='oui' /><td><td>Ne pas afficher la moyenne générale (<i>même si l'affichage des coefficients de matières est activé</i>).<br /><i>Rappel:</i> La moyenne générale ne peut apparaitre que si un des coefficients de matière au moins est non nul.</td></tr>\n";
	echo "<tr><td valign='top'><input type='checkbox' name='min_max_moyclas' value='1' /></td><td>Afficher les moyennes minimale, classe et maximale dans une seule colonne pour gagner de la place pour l'appréciation.</td></tr>\n";
*/
	// A FAIRE:
	// Tester et ne pas afficher:
	// - si tous les coeff sont à 1
	$test_coef=mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef!='1.0')"));
	if($test_coef>0){
		echo "<tr>\n";
		echo "<td colspan=\"2\"><br /><br /><b>Calcul des moyennes générales";
		// Ne pas afficher la mention de catégorie, si on n'affiche pas les catégories dans cette classe.
		$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
		if ($affiche_categories == "y") {
			echo " et par catégorie";
		}
		echo ".<br /></b></td>\n";
		echo "</tr>\n";
		echo "<tr><td valign='top'><input type='checkbox' name='coefficients_a_1' value='oui' /></td><td>Forcer les coefficients des matières à 1, indépendamment des coefficients saisis dans les paramètres de la classe.</td></tr>\n";
	}
	echo "</table>\n";

	echo "<p style='text-align:center;'><input type='submit' name='Valider' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<br />\n<center><table border=\"1\" cellpadding=\"10\" width=\"80%\"><tr><td>";
	echo "<center><b>Avertissement</b></center><br /><br />La mise en page des bulletins est très différente à l'écran et à l'impression.
	Avant d'imprimer les bulletins :
	<ul>
	<li>Veillez à utiliser la fonction \"aperçu avant impression\" disponible sur la plupart des navigateurs.</li>
	<li>Veillez à régler les paramètres de marges, d'en-tête et de pied de page.</li>
	</ul>
	</td></tr></table></center>\n";
}

require("../lib/footer.inc.php");
?>
