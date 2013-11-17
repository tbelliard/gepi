<?php
/*
* $Id$
*
 * Copyright 2001, 2002 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");
/*debug_var();*/

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
die();
};
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
die();
}

	if (empty($_GET['id_lettre_suivi']) and empty($_POST['id_lettre_suivi'])) {$id_lettre_suivi[0]="";}
	   else { if (isset($_GET['id_lettre_suivi'])) {$id_lettre_suivi=$_GET['id_lettre_suivi'];} if (isset($_POST['id_lettre_suivi'])) {$id_lettre_suivi=$_POST['id_lettre_suivi'];} }
	


	if (empty($_GET['lettre_action']) and empty($_POST['lettre_action'])) { $lettre_action = ''; }
     	   else { if (isset($_GET['lettre_action'])) { $lettre_action=$_GET['lettre_action']; } if (isset($_POST['lettre_action'])) { $lettre_action=$_POST['lettre_action']; } }
	
	

	if (empty($_GET['classe_multiple']) and empty($_POST['classe_multiple'])) { $classe_multiple = ''; }
	   else { if (isset($_GET['classe_multiple'])) { $classe_multiple = $_GET['classe_multiple']; } if (isset($_POST['classe_multiple'])) { $classe_multiple = $_POST['classe_multiple']; } }
	if (empty($_GET['eleve_multiple']) and empty($_POST['eleve_multiple'])) { $eleve_multiple = ''; }
	   else { if (isset($_GET['eleve_multiple'])) { $eleve_multiple = $_GET['eleve_multiple']; } if (isset($_POST['eleve_multiple'])) { $eleve_multiple = $_POST['eleve_multiple']; } }
		$_SESSION['classe_multiple'] = $classe_multiple;
		$_SESSION['eleve_multiple'] = $eleve_multiple;


	$form_pdf_lettre = 'cache';
// redirection vers la création des courrier en pdf
// si au moins 1 lettre a été cochée et lettre_action = originaux ( c'est a dire passage préalable par le form2 de selection de
if($id_lettre_suivi[0] != '' and $lettre_action === 'originaux')
{
//	$_SESSION['id_lettre_suivi'] = $id_lettre_suivi;
//	$_SESSION['lettre_action'] = $lettre_action;
//	header("Location: lettre_pdf.php");

	// ajout du form de validation du PDF
	$form_pdf_lettre = 'affiche';
}


//**************** EN-TETE *****************
$titre_page = "Gestion des absences";
require_once("../../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<script type="text/javascript" language="javascript">
<!--
function getDate(input_pass,form_choix){
 var date_select=new Date();
 var jour=date_select.getDate(); if(jour<10){jour="0"+jour;}
 var mois=date_select.getMonth()+1; if(mois<10){mois="0"+mois;}
 var annee=date_select.getFullYear();
 var date_jour = jour+"/"+mois+"/"+annee;
// nom du formulaire
  var form_action = form_choix;
// id des élèments
  var input_pass_id = input_pass.id;
  var input_pass_value = input_pass.value;
// modifie le contenue de l'élèment
if(document.forms[form_action].elements[input_pass_id].value=='JJ/MM/AAAA' || document.forms[form_action].elements[input_pass_id].value=='') { document.forms[form_action].elements[input_pass_id].value=date_jour; }
}
 // -->
</script>
<?php
//Configuration du calendrier
include("../../lib/calendrier/calendrier.class.php");
$cal = new Calendrier("form1", "du");
$cal_3 = new Calendrier("form3", "du");
$cal_4 = new Calendrier("form3", "au");
$cal_5 = new Calendrier("form5", "du");
$cal_6 = new Calendrier("form5", "au");
$cal_7 = new Calendrier("form6", "du");

    $date_ce_jour = date('d/m/Y'); $erreur = '';

   if (empty($_GET['type']) and empty($_POST['type'])) {$type="A";}
    else { if (isset($_GET['type'])) {$type=$_GET['type'];} if (isset($_POST['type'])) {$type=$_POST['type'];} }
   if (empty($_GET['type_impr']) and empty($_POST['type_impr'])) {$type_impr="laf";}
    else { if (isset($_GET['type_impr'])) {$type_impr=$_GET['type_impr'];} if (isset($_POST['type_impr'])) {$type_impr=$_POST['type_impr'];} }
   if (empty($_GET['choix']) and empty($_POST['choix'])) {$choix="nonjustifie";}
    else { if (isset($_GET['choix'])) {$a_imprimer=$_GET['choix'];} if (isset($_POST['choix'])) {$choix=$_POST['choix'];} }
   if (empty($_GET['a_imprimer']) and empty($_POST['a_imprimer'])) {$a_imprimer="";}
    else { if (isset($_GET['a_imprimer'])) {$a_imprimer=$_GET['a_imprimer'];} if (isset($_POST['a_imprimer'])) {$a_imprimer=$_POST['a_imprimer'];} }
   if (empty($_GET['classe']) and empty($_POST['classe'])) {$classe="tous";}
    else { if (isset($_GET['classe'])) {$classe=$_GET['classe'];} if (isset($_POST['classe'])) {$classe=$_POST['classe'];} }
   if (empty($_GET['eleve']) and empty($_POST['eleve'])) {$eleve="";}
    else { if (isset($_GET['eleve'])) {$eleve=$_GET['eleve'];} if (isset($_POST['eleve'])) {$eleve=$_POST['eleve'];} }
   if (empty($_GET['id_eleve']) and empty($_POST['id_eleve'])) {$id_eleve="";}
    else { if (isset($_GET['id_eleve'])) {$id_eleve=$_GET['id_eleve'];} if (isset($_POST['id_eleve'])) {$id_eleve=$_POST['id_eleve'];} }
   if (empty($_GET['id_classe']) and empty($_POST['id_classe'])) {$id_classe="";}
    else { if (isset($_GET['id_classe'])) {$id_classe=$_GET['id_classe'];} if (isset($_POST['id_classe'])) {$id_classe=$_POST['id_classe'];} }
   if (empty($_GET['du']) and empty($_POST['du'])) {$du="$date_ce_jour";}
    else { if (isset($_GET['du'])) {$du=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];} }

	// gestion des dates
	if (empty($_GET['du']) and empty($_POST['du'])) {$du = '';}
	 else { if (isset($_GET['du'])) {$du=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];} }
	if (empty($_GET['au']) and empty($_POST['au'])) {$au="JJ/MM/AAAA";}
	 else { if (isset($_GET['au'])) {$au=$_GET['au'];} if (isset($_POST['au'])) {$au=$_POST['au'];} }

		if (empty($_GET['day']) and empty($_POST['day'])) {$day=date("d");}
	    	 else { if (isset($_GET['day'])) {$day=$_GET['day'];} if (isset($_POST['day'])) {$day=$_POST['day'];} }
		if (empty($_GET['month']) and empty($_POST['month'])) {$month=date("m");}
		 else { if (isset($_GET['month'])) {$month=$_GET['month'];} if (isset($_POST['month'])) {$month=$_POST['month'];} }
		if (empty($_GET['year']) and empty($_POST['year'])) {$year=date("Y");}
		 else { if (isset($_GET['year'])) {$year=$_GET['year'];} if (isset($_POST['year'])) {$year=$_POST['year'];} }
	      	if ( !empty($du) ) {
		  $ou_est_on = explode('/',$du);
		  $year = $ou_est_on[2]; $month = $ou_est_on[1]; $day =  $ou_est_on[0];
	        } else { $du = $day."/".$month.'/'.$year; }

   if (empty($_GET['composer']) and empty($_POST['composer'])) {$composer="";}
    else { if (isset($_GET['composer'])) {$composer=$_GET['composer'];} if (isset($_POST['composer'])) {$composer=$_POST['composer'];} }
   if (empty($_GET['id_absence']) and empty($_POST['id_absence'])) {$id_absence="";}
    else { if (isset($_GET['id_absence'])) {$id_absence=$_GET['id_absence'];} if (isset($_POST['id_absence'])) {$id_absence=$_POST['id_absence'];} }
   if (empty($_GET['cocher']) and empty($_POST['cocher'])) {$cocher="";}
    else { if (isset($_GET['cocher'])) {$cocher=$_GET['cocher'];} if (isset($_POST['cocher'])) {$cocher=$_POST['cocher'];} }
   if (empty($_GET['sous_rubrique']) and empty($_POST['sous_rubrique'])) { $sous_rubrique = ''; }
    else { if (isset($_GET['sous_rubrique'])) { $sous_rubrique = $_GET['sous_rubrique']; } if (isset($_POST['sous_rubrique'])) { $sous_rubrique = $_POST['sous_rubrique']; } }
   if (empty($_GET['action_lettre']) and empty($_POST['action_lettre'])) { $action_lettre = ''; }
    else { if (isset($_GET['action_lettre'])) { $action_lettre = $_GET['action_lettre']; } if (isset($_POST['action_lettre'])) { $action_lettre = $_POST['action_lettre']; } }

	if (empty($_GET['absencenj']) and empty($_POST['absencenj'])) { $absencenj = ''; }
	   else { if (isset($_GET['absencenj'])) { $absencenj = $_GET['absencenj']; } if (isset($_POST['absencenj'])) { $absencenj = $_POST['absencenj']; } }
	if (empty($_GET['retardnj']) and empty($_POST['retardnj'])) { $retardnj = ''; }
	   else { if (isset($_GET['retardnj'])) { $retardnj = $_GET['retardnj']; } if (isset($_POST['retardnj'])) { $retardnj = $_POST['retardnj']; } }

//lettre
   if (empty($_GET['lettre_type']) and empty($_POST['lettre_type'])) {$lettre_type="";}
    else { if (isset($_GET['lettre_type'])) {$lettre_type=$_GET['lettre_type'];} if (isset($_POST['lettre_type'])) {$lettre_type=$_POST['lettre_type'];} }
   if (empty($_GET['action_lettre']) and empty($_POST['action_lettre'])) {$action_lettre="";}
    else { if (isset($_GET['action_lettre'])) {$action_lettre=$_GET['action_lettre'];} if (isset($_POST['action_lettre'])) {$action_lettre=$_POST['action_lettre'];} }
   if (empty($_GET['cadre_selection']) and empty($_POST['cadre_selection'])) {$cadre_selection="";}
    else { if (isset($_GET['cadre_selection'])) {$cadre_selection=$_GET['cadre_selection'];} if (isset($_POST['cadre_selection'])) {$cadre_selection=$_POST['cadre_selection'];} }
   if (empty($_GET['id']) and empty($_POST['id'])) {$id="";}
    else { if (isset($_GET['id'])) {$id=$_GET['id'];} if (isset($_POST['id'])) {$id=$_POST['id'];} }
   if (empty($_GET['x_lettre_tc']) and empty($_POST['x_lettre_tc'])) { $x_lettre_tc = ''; }
    else { if (isset($_GET['x_lettre_tc'])) { $x_lettre_tc = $_GET['x_lettre_tc']; } if (isset($_POST['x_lettre_tc'])) { $x_lettre_tc = $_POST['x_lettre_tc']; } }
   if (empty($_GET['y_lettre_tc']) and empty($_POST['y_lettre_tc'])) { $y_lettre_tc = ''; }
    else { if (isset($_GET['y_lettre_tc'])) { $y_lettre_tc = $_GET['y_lettre_tc']; } if (isset($_POST['y_lettre_tc'])) { $y_lettre_tc = $_POST['y_lettre_tc']; } }
   if (empty($_GET['l_lettre_tc']) and empty($_POST['l_lettre_tc'])) { $l_lettre_tc = ''; }
    else { if (isset($_GET['l_lettre_tc'])) { $l_lettre_tc = $_GET['l_lettre_tc']; } if (isset($_POST['l_lettre_tc'])) { $l_lettre_tc = $_POST['l_lettre_tc']; } }
   if (empty($_GET['h_lettre_tc']) and empty($_POST['h_lettre_tc'])) { $h_lettre_tc = ''; }
    else { if (isset($_GET['h_lettre_tc'])) { $h_lettre_tc = $_GET['h_lettre_tc']; } if (isset($_POST['h_lettre_tc'])) { $h_lettre_tc = $_POST['h_lettre_tc']; } }
   if (empty($_GET['encadre_lettre_tc']) and empty($_POST['encadre_lettre_tc'])) { $encadre_lettre_tc = ''; }
    else { if (isset($_GET['encadre_lettre_tc'])) { $encadre_lettre_tc = $_GET['encadre_lettre_tc']; } if (isset($_POST['encadre_lettre_tc'])) { $encadre_lettre_tc = $_POST['encadre_lettre_tc']; } }
   if (empty($_GET['lettre_type_nouv']) and empty($_POST['lettre_type_nouv'])) { $lettre_type_nouv = ''; }
    else { if (isset($_GET['lettre_type_nouv'])) { $lettre_type_nouv = $_GET['lettre_type_nouv']; } if (isset($_POST['lettre_type_nouv'])) { $lettre_type_nouv = $_POST['lettre_type_nouv']; } }
   if (empty($_GET['action_choix_lettre']) and empty($_POST['action_choix_lettre'])) { $action_choix_lettre = ''; }
    else { if (isset($_GET['action_choix_lettre'])) { $action_choix_lettre = $_GET['action_choix_lettre']; } if (isset($_POST['action_choix_lettre'])) { $action_choix_lettre = $_POST['action_choix_lettre']; } }
// cadre des lettre
   if (empty($_GET['action_cadre']) and empty($_POST['action_cadre'])) {$action_cadre="";}
    else { if (isset($_GET['action_cadre'])) {$action_cadre=$_GET['action_cadre'];} if (isset($_POST['action_cadre'])) {$action_cadre=$_POST['action_cadre'];} }
   if (empty($_GET['nom_lettre_cadre']) and empty($_POST['nom_lettre_cadre'])) { $nom_lettre_cadre = ''; }
    else { if (isset($_GET['nom_lettre_cadre'])) { $nom_lettre_cadre = $_GET['nom_lettre_cadre']; } if (isset($_POST['nom_lettre_cadre'])) { $nom_lettre_cadre = $_POST['nom_lettre_cadre']; } }
   if (empty($_GET['x_lettre_cadre']) and empty($_POST['x_lettre_cadre'])) { $x_lettre_cadre = ''; }
    else { if (isset($_GET['x_lettre_cadre'])) { $x_lettre_cadre = $_GET['x_lettre_cadre']; } if (isset($_POST['x_lettre_cadre'])) { $x_lettre_cadre = $_POST['x_lettre_cadre']; } }
   if (empty($_GET['y_lettre_cadre']) and empty($_POST['y_lettre_cadre'])) { $y_lettre_cadre = ''; }
    else { if (isset($_GET['y_lettre_cadre'])) { $y_lettre_cadre = $_GET['y_lettre_cadre']; } if (isset($_POST['y_lettre_cadre'])) { $y_lettre_cadre = $_POST['y_lettre_cadre']; } }
   if (empty($_GET['l_lettre_cadre']) and empty($_POST['l_lettre_cadre'])) { $l_lettre_cadre = ''; }
    else { if (isset($_GET['l_lettre_cadre'])) { $l_lettre_cadre = $_GET['l_lettre_cadre']; } if (isset($_POST['l_lettre_cadre'])) { $l_lettre_cadre = $_POST['l_lettre_cadre']; } }
   if (empty($_GET['h_lettre_cadre']) and empty($_POST['h_lettre_cadre'])) { $h_lettre_cadre = ''; }
    else { if (isset($_GET['h_lettre_cadre'])) { $h_lettre_cadre = $_GET['h_lettre_cadre']; } if (isset($_POST['h_lettre_cadre'])) { $h_lettre_cadre = $_POST['h_lettre_cadre']; } }
   if (empty($_GET['texte_lettre_cadre']) and empty($_POST['texte_lettre_cadre'])) { $texte_lettre_cadre = ''; }
    else { if (isset($_GET['texte_lettre_cadre'])) { $texte_lettre_cadre = $_GET['texte_lettre_cadre']; } if (isset($_POST['texte_lettre_cadre'])) { $texte_lettre_cadre = $_POST['texte_lettre_cadre']; } }
   if (empty($_GET['encadre_lettre_cadre']) and empty($_POST['encadre_lettre_cadre'])) { $encadre_lettre_cadre = ''; }
    else { if (isset($_GET['encadre_lettre_cadre'])) { $encadre_lettre_cadre = $_GET['encadre_lettre_cadre']; } if (isset($_POST['encadre_lettre_cadre'])) { $encadre_lettre_cadre = $_POST['encadre_lettre_cadre']; } }
   if (empty($_GET['r_couleurdefond_lettre_cadre']) and empty($_POST['r_couleurdefond_lettre_cadre'])) { $r_couleurdefond_lettre_cadre = ''; }
    else { if (isset($_GET['r_couleurdefond_lettre_cadre'])) { $r_couleurdefond_lettre_cadre = $_GET['r_couleurdefond_lettre_cadre']; } if (isset($_POST['r_couleurdefond_lettre_cadre'])) { $r_couleurdefond_lettre_cadre = $_POST['r_couleurdefond_lettre_cadre']; } }
   if (empty($_GET['v_couleurdefond_lettre_cadre']) and empty($_POST['v_couleurdefond_lettre_cadre'])) { $v_couleurdefond_lettre_cadre = ''; }
    else { if (isset($_GET['v_couleurdefond_lettre_cadre'])) { $v_couleurdefond_lettre_cadre = $_GET['v_couleurdefond_lettre_cadre']; } if (isset($_POST['v_couleurdefond_lettre_cadre'])) { $v_couleurdefond_lettre_cadre = $_POST['v_couleurdefond_lettre_cadre']; } }
   if (empty($_GET['b_couleurdefond_lettre_cadre']) and empty($_POST['b_couleurdefond_lettre_cadre'])) { $b_couleurdefond_lettre_cadre = ''; }
    else { if (isset($_GET['b_couleurdefond_lettre_cadre'])) { $b_couleurdefond_lettre_cadre = $_GET['b_couleurdefond_lettre_cadre']; } if (isset($_POST['b_couleurdefond_lettre_cadre'])) { $b_couleurdefond_lettre_cadre = $_POST['b_couleurdefond_lettre_cadre']; } }

   if (empty($_GET['action_laf']) and empty($_POST['action_laf'])) { $action_laf = ''; }
    else { if (isset($_GET['action_laf'])) { $action_laf = $_GET['action_laf']; } if (isset($_POST['action_laf'])) { $action_laf = $_POST['action_laf']; } }
   if (empty($_GET['statu_lettre']) and empty($_POST['statu_lettre'])) { $statu_lettre = ''; }
    else { if (isset($_GET['statu_lettre'])) { $statu_lettre = $_GET['statu_lettre']; } if (isset($_POST['statu_lettre'])) { $statu_lettre = $_POST['statu_lettre']; } }
   if (empty($_GET['remarque_lettre_suivi']) and empty($_POST['remarque_lettre_suivi'])) { $remarque_lettre_suivi = ''; }
    else { if (isset($_GET['remarque_lettre_suivi'])) { $remarque_lettre_suivi = $_GET['remarque_lettre_suivi']; } if (isset($_POST['remarque_lettre_suivi'])) { $remarque_lettre_suivi = $_POST['remarque_lettre_suivi']; } }
   if (empty($_GET['reponse_lettre_type']) and empty($_POST['reponse_lettre_type'])) { $reponse_lettre_type = ''; }
    else { if (isset($_GET['reponse_lettre_type'])) { $reponse_lettre_type = $_GET['reponse_lettre_type']; } if (isset($_POST['reponse_lettre_type'])) { $reponse_lettre_type = $_POST['reponse_lettre_type']; } }

   if (empty($_GET['x_c']) and empty($_POST['x_c'])) { $x_c = ''; }
    else { if (isset($_GET['x_c'])) { $x_c = $_GET['x_c']; } if (isset($_POST['x_c'])) { $x_c = $_POST['x_c']; } }
   if (empty($_GET['y_c']) and empty($_POST['y_c'])) { $y_c = ''; }
    else { if (isset($_GET['y_c'])) { $y_c = $_GET['y_c']; } if (isset($_POST['y_c'])) { $y_c = $_POST['y_c']; } }
   if (empty($_GET['l_c']) and empty($_POST['l_c'])) { $l_c = ''; }
    else { if (isset($_GET['l_c'])) { $l_c = $_GET['l_c']; } if (isset($_POST['l_c'])) { $l_c = $_POST['l_c']; } }
   if (empty($_GET['h_c']) and empty($_POST['h_c'])) { $h_c = ''; }
    else { if (isset($_GET['h_c'])) { $h_c = $_GET['h_c']; } if (isset($_POST['h_c'])) { $h_c = $_POST['h_c']; } }
   if (empty($_GET['encad_c']) and empty($_POST['encad_c'])) { $encad_c = ''; }
    else { if (isset($_GET['encad_c'])) { $encad_c = $_GET['encad_c']; } if (isset($_POST['encad_c'])) { $encad_c = $_POST['encad_c']; } }

   if (empty($_GET['action_etiquette']) and empty($_POST['action_etiquette'])) { $action_etiquette = ''; }
    else { if (isset($_GET['action_etiquette'])) { $action_etiquette = $_GET['action_etiquette']; } if (isset($_POST['action_etiquette'])) { $action_etiquette = $_POST['action_etiquette']; } }


if($type_impr === 'eti') {
   if (empty($_GET['trie_par']) and empty($_POST['trie_par'])) { $trie_par = ''; }
    else { if (isset($_GET['trie_par'])) { $trie_par = $_GET['trie_par']; } if (isset($_POST['trie_par'])) { $trie_par = $_POST['trie_par']; } }
   if (empty($_GET['id_etiquette_format']) and empty($_POST['id_etiquette_format'])) { $id_etiquette_format = ''; }
    else { if (isset($_GET['id_etiquette_format'])) { $id_etiquette_format = $_GET['id_etiquette_format']; } if (isset($_POST['id_etiquette_format'])) { $id_etiquette_format = $_POST['id_etiquette_format']; } }
   if (empty($_GET['nom_etiquette_format']) and empty($_POST['nom_etiquette_format'])) { $nom_etiquette_format = ''; }
    else { if (isset($_GET['nom_etiquette_format'])) { $nom_etiquette_format = $_GET['nom_etiquette_format']; } if (isset($_POST['nom_etiquette_format'])) { $nom_etiquette_format = $_POST['nom_etiquette_format']; } }
   if (empty($_GET['xcote_etiquette_format']) and empty($_POST['xcote_etiquette_format'])) { $xcote_etiquette_format = ''; }
    else { if (isset($_GET['xcote_etiquette_format'])) { $xcote_etiquette_format = $_GET['xcote_etiquette_format']; } if (isset($_POST['xcote_etiquette_format'])) { $xcote_etiquette_format = $_POST['xcote_etiquette_format']; } }
   if (empty($_GET['ycote_etiquette_format']) and empty($_POST['ycote_etiquette_format'])) { $ycote_etiquette_format = ''; }
    else { if (isset($_GET['ycote_etiquette_format'])) { $ycote_etiquette_format = $_GET['ycote_etiquette_format']; } if (isset($_POST['ycote_etiquette_format'])) { $ycote_etiquette_format = $_POST['ycote_etiquette_format']; } }
   if (empty($_GET['espacementx_etiquette_format']) and empty($_POST['espacementx_etiquette_format'])) { $espacementx_etiquette_format = ''; }
    else { if (isset($_GET['espacementx_etiquette_format'])) { $espacementx_etiquette_format = $_GET['espacementx_etiquette_format']; } if (isset($_POST['espacementx_etiquette_format'])) { $espacementx_etiquette_format = $_POST['espacementx_etiquette_format']; } }
   if (empty($_GET['espacementy_etiquette_format']) and empty($_POST['espacementy_etiquette_format'])) { $espacementy_etiquette_format = ''; }
    else { if (isset($_GET['espacementy_etiquette_format'])) { $espacementy_etiquette_format = $_GET['espacementy_etiquette_format']; } if (isset($_POST['espacementy_etiquette_format'])) { $espacementy_etiquette_format = $_POST['espacementy_etiquette_format']; } }
   if (empty($_GET['largeur_etiquette_format']) and empty($_POST['largeur_etiquette_format'])) { $largeur_etiquette_format = ''; }
    else { if (isset($_GET['largeur_etiquette_format'])) { $largeur_etiquette_format = $_GET['largeur_etiquette_format']; } if (isset($_POST['largeur_etiquette_format'])) { $largeur_etiquette_format = $_POST['largeur_etiquette_format']; } }
   if (empty($_GET['hauteur_etiquette_format']) and empty($_POST['hauteur_etiquette_format'])) { $hauteur_etiquette_format = ''; }
    else { if (isset($_GET['hauteur_etiquette_format'])) { $hauteur_etiquette_format = $_GET['hauteur_etiquette_format']; } if (isset($_POST['hauteur_etiquette_format'])) { $hauteur_etiquette_format = $_POST['hauteur_etiquette_format']; } }
   if (empty($_GET['nbl_etiquette_format']) and empty($_POST['nbl_etiquette_format'])) { $nbl_etiquette_format = ''; }
    else { if (isset($_GET['nbl_etiquette_format'])) { $nbl_etiquette_format = $_GET['nbl_etiquette_format']; } if (isset($_POST['nbl_etiquette_format'])) { $nbl_etiquette_format = $_POST['nbl_etiquette_format']; } }
   if (empty($_GET['nbh_etiquette_format']) and empty($_POST['nbh_etiquette_format'])) { $nbh_etiquette_format = ''; }
    else { if (isset($_GET['nbh_etiquette_format'])) { $nbh_etiquette_format = $_GET['nbh_etiquette_format']; } if (isset($_POST['nbh_etiquette_format'])) { $nbh_etiquette_format = $_POST['nbh_etiquette_format']; } }
   if (empty($_GET['etiquette_aff']) and empty($_POST['etiquette_aff'])) { $etiquette_aff = ''; }
    else { if (isset($_GET['etiquette_aff'])) { $etiquette_aff = $_GET['etiquette_aff']; } if (isset($_POST['etiquette_aff'])) { $etiquette_aff = $_POST['etiquette_aff']; } }
}

// fonction de sécuritée
// uid de pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post===$_SESSION['uid_prime']
 if(empty($_SESSION['uid_prime'])) { $_SESSION['uid_prime']=''; }
 if (empty($_GET['uid_post']) and empty($_POST['uid_post'])) {$uid_post='';}
    else { if (isset($_GET['uid_post'])) {$uid_post=$_GET['uid_post'];} if (isset($_POST['uid_post'])) {$uid_post=$_POST['uid_post'];} }
	$uid = md5(uniqid(microtime(), 1));
	$valide_form='';
	   // on remplace les %20 par des espaces
	    $uid_post = my_eregi_replace('%20',' ',$uid_post);
	if($uid_post===$_SESSION['uid_prime']) { $valide_form = 'yes'; } else { $valide_form = 'no'; }
	$_SESSION['uid_prime'] = $uid;
// fin de la fonction de sécuritée

function titre_lettre_type($id)
 {
	global $prefix_base;
 	$requete_lettre ="SELECT * FROM ".$prefix_base."lettres_types WHERE id_lettre_type = '".$id."' LIMIT 0, 1";
	$execution_lettre = mysqli_query($GLOBALS["mysqli"], $requete_lettre) or die('Erreur SQL !'.$requete_lettre.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$donner_lettre = mysqli_fetch_array($execution_lettre);
	$titre_lettre = $donner_lettre['titre_lettre_type'];
	$reponse_lettre_type = $donner_lettre['reponse_lettre_type'];
	return array($titre_lettre, $reponse_lettre_type);
 }

// pour ajouter un nouveau type de lettre
if(!empty($lettre_type_nouv) and $action_choix_lettre === 'editer' and $valide_form === 'yes')
 {
	// on vérifie s'il y a des lettres de ce type qui on était envoyé si oui on ne peut pas supprimer ce type de lettre
        $test_existance = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_types WHERE titre_lettre_type = '".$lettre_type_nouv."'"),0);
        if ($test_existance === '0')
	{
 	      if(empty($reponse_lettre_type)) { $reponse_lettre_type = 'non'; }
              $requete="INSERT INTO ".$prefix_base."lettres_types (titre_lettre_type, categorie_lettre_type, reponse_lettre_type) VALUES ('".$lettre_type_nouv."', 'Type de lettre', '".$reponse_lettre_type."')";
              $execution_requete = mysqli_query($GLOBALS["mysqli"], $requete);
	      $lettre_type = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
	} else { $erreur = 'Il existe déjas un type de lettre de même nom'; }
 }

//si action_lettre = ajout_cadre
if($action_lettre === 'ajout_cadre' and $valide_form === 'yes')
 {
	$cpt_cs = '0';
	while(!empty($cadre_selection[$cpt_cs]))
	 {
	      // nous prenons les informations sur le cadre choisi
               $requete_1 ="SELECT * FROM ".$prefix_base."lettres_cadres WHERE id_lettre_cadre = '".$cadre_selection[$cpt_cs]."'";
               $execution_1 = mysqli_query($GLOBALS["mysqli"], $requete_1) or die('Erreur SQL !'.$requete_1.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
               while ( $data_1 = mysqli_fetch_array($execution_1))
                {
			$x_c = $data_1['x_lettre_cadre'];
			$y_c = $data_1['y_lettre_cadre'];
			$l_c = $data_1['l_lettre_cadre'];
			$h_c = $data_1['h_lettre_cadre'];
			$encad_c = $data_1['encadre_lettre_cadre'];
	        }
              $requete="INSERT INTO ".$prefix_base."lettres_tcs (type_lettre_tc,cadre_lettre_tc,x_lettre_tc,y_lettre_tc,l_lettre_tc,h_lettre_tc,encadre_lettre_tc) VALUES ('$lettre_type','$cadre_selection[$cpt_cs]','$x_c','$y_c','$l_c','$h_c','$encad_c')";
              $execution_requete = mysqli_query($GLOBALS["mysqli"], $requete);
	      $cpt_cs = $cpt_cs + 1;
	 }
 }

//si action_lettre = supprimer_cadre
if($action_lettre === 'supprimer_cadre' and $valide_form === 'yes')
 {
	if(!empty($id) and !empty($cadre_selection))
	{
       		$req_delete = "DELETE FROM ".$prefix_base."lettres_tcs WHERE id_lettre_tc = '".$id."' AND cadre_lettre_tc = '".$cadre_selection."'";
       		$req_sql2 = mysqli_query($GLOBALS["mysqli"], $req_delete);
	}
 }

//si action_lettre = modifier_cadre
if($action_lettre === 'modifier_cadre' and $valide_form === 'yes')
 {
	if(!empty($id) and !empty($cadre_selection))
	{
		$requete="UPDATE ".$prefix_base."lettres_tcs SET x_lettre_tc = '".$x_lettre_tc."', y_lettre_tc = '".$y_lettre_tc."', l_lettre_tc = '".$l_lettre_tc."', h_lettre_tc = '".$h_lettre_tc."', encadre_lettre_tc = '".$encadre_lettre_tc."' WHERE  id_lettre_tc = '".$id."' AND cadre_lettre_tc = '".$cadre_selection."'";
	        $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	}
 }

//si action_cadre = ajout_cadre
if($action_cadre === 'ajout_cadre' and $valide_form === 'yes')
 {
              $requete="INSERT INTO ".$prefix_base."lettres_cadres (nom_lettre_cadre, x_lettre_cadre, y_lettre_cadre, l_lettre_cadre, h_lettre_cadre, texte_lettre_cadre, encadre_lettre_cadre, couleurdefond_lettre_cadre) VALUES ('".$nom_lettre_cadre."', '".$x_lettre_cadre."', '".$y_lettre_cadre."', '".$l_lettre_cadre."', '".$h_lettre_cadre."', '".$texte_lettre_cadre."', '".$encadre_lettre_cadre."', '".$couleurdefond_lettre_cadre."')";
              $execution_requete = mysqli_query($GLOBALS["mysqli"], $requete);
 }

//si action_cadre = supprimer_cadre
if($action_cadre === 'supprimer_cadre' and $valide_form === 'yes')
 {
	if(!empty($id))
	{
		// on vérifie s'il y a des lettres de ce type qui on était envoyé si oui on ne peut pas supprimer ce type de lettre
                $test_existance = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_tcs WHERE cadre_lettre_tc = '".$id."'"),0);
                if ($test_existance === '0')
		{
	     		$req_delete = "DELETE FROM ".$prefix_base."lettres_cadres WHERE id_lettre_cadre = '".$id."'";
	       		$req_sql2 = mysqli_query($GLOBALS["mysqli"], $req_delete);
		}
	}
 }

//si action_cadre = modifier_cadre
if($action_cadre === 'modifier_cadre' and $valide_form === 'yes')
 {
	if(!empty($id))
	{
		$texte_lettre_cadre = my_ereg_replace('\[','<',$texte_lettre_cadre);
		$texte_lettre_cadre = my_ereg_replace('\]','>',$texte_lettre_cadre);
		$couleurdefond_lettre_cadre = $r_couleurdefond_lettre_cadre.'|'.$v_couleurdefond_lettre_cadre.'|'.$b_couleurdefond_lettre_cadre;
		$requete="UPDATE ".$prefix_base."lettres_cadres SET nom_lettre_cadre = '".$nom_lettre_cadre."', x_lettre_cadre = '".$x_lettre_cadre."', y_lettre_cadre = '".$y_lettre_cadre."', l_lettre_cadre = '".$l_lettre_cadre."', h_lettre_cadre = '".$h_lettre_cadre."', texte_lettre_cadre = '".$texte_lettre_cadre."', encadre_lettre_cadre = '".$encadre_lettre_cadre."', couleurdefond_lettre_cadre = '".$couleurdefond_lettre_cadre."' WHERE  id_lettre_cadre = '".$id."'";
	        $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	}
 }

//si $action_choix_lettre = supprimer alors on supprime le type de lettre
if($action_choix_lettre === 'supprimer' and $valide_form === 'yes')
 {
	if(!empty($lettre_type))
	{
		// on vérifie s'il y a des lettres de ce type qui on était envoyé si oui on ne peut pas supprimer ce type de lettre
                $test_existance = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE type_lettre_suivi = '".$lettre_type."'"),0);
                if ($test_existance === '0')
		{
	       		$req_delete = "DELETE FROM ".$prefix_base."lettres_tcs WHERE type_lettre_tc = '".$lettre_type."'";
	       		$req_sql2 = mysqli_query($GLOBALS["mysqli"], $req_delete);
	       		$req_delete = "DELETE FROM ".$prefix_base."lettres_types WHERE id_lettre_type = '".$lettre_type."'";
	       		$req_sql2 = mysqli_query($GLOBALS["mysqli"], $req_delete);
		} else { $erreur = 'Impossible de le supprimer car il existe des envoies de ce type'; }
	}
 }

//si $action_choix_lettre = renommer
if($action_choix_lettre === 'renommer' and $lettre_type_nouv != '' and $valide_form === 'yes')
 {
	if(!empty($lettre_type_nouv))
	{
		// on vérifie s'il y a des lettres de ce type qui on était envoyé si oui on ne peut pas supprimer ce type de lettre
                $test_existance = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_types WHERE titre_lettre_type = '".$lettre_type_nouv."' AND id_lettre_type != '".$lettre_type."'"),0);
                if ($test_existance === '0')
		{
			if(empty($reponse_lettre_type)) { $reponse_lettre_type = 'non'; }
			$requete="UPDATE ".$prefix_base."lettres_types SET titre_lettre_type = '".$lettre_type_nouv."', reponse_lettre_type = '".$reponse_lettre_type."' WHERE id_lettre_type = '".$lettre_type."'";
		        $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			$action_choix_lettre = '';
		} else { $erreur = 'Il existe déjas un type de lettre de même nom'; }
	}
 }

//si action_cadre = ajout_cadre
if(!empty($nom_lettre_cadre) and empty($id) and $valide_form === 'yes')
 {
              $requete="INSERT INTO ".$prefix_base."lettres_cadres (nom_lettre_cadre) VALUES ('".$nom_lettre_cadre."')";
              $execution_requete = mysqli_query($GLOBALS["mysqli"], $requete);
	      $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
	      $type_impr = 'crea_lettre';
	      $action_cadre = 'aff_modifier_cadre';
	      $sous_rubrique = 'gb';
 }

//supprimer un type de lettre
/*if($action_lettre === 'supprimer_cadre' and $valide_form === 'yes')
 {
       $req_delete = "DELETE FROM ".$prefix_base."lettres_tcs WHERE id_lettre_tc ='".$id_cadre."'";
       $req_sql2 = mysql_query($req_delete);
 }*/

//IMPRESSIONS LETTRES AUX PARENTS

// pour réinitialiser la date d'envoi d'un document
if($action_laf === 'reinit_envoi' and $valide_form === 'yes')
 {
	if(!empty($id))
	{
		// on vérifie s'il n'y a pas eu de réponse pour cette envoi si oui on ne peut pas réinitialiser
                $test_existance = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE id_lettre_suivi = '".$id."' AND (reponse_date_lettre_suivi = '' OR reponse_date_lettre_suivi != '0000-00-00') AND statu_lettre_suivi = 'recus'"),0);
                if ($test_existance === '0')
		{
			$requete="UPDATE ".$prefix_base."lettres_suivis SET envoye_date_lettre_suivi = '', envoye_heure_lettre_suivi = '', quienvoi_lettre_suivi = '' WHERE id_lettre_suivi = '".$id."'";
		        $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		} else { $erreur = 'Il existe une réponse pour ce courrier donc vous ne pouvez pas réinitialiser l\'envoi'; }
	}
 }

// pour modifier le statut d'un courrier
if($action_laf === 'modif_status' and $valide_form === 'yes')
 {
	if(!empty($id))
	{
        	if( $statu_lettre[0] === 'recus' ) { $date_fiche = date('Y-m-d'); } else { $date_fiche = ''; }

		$requete="UPDATE ".$prefix_base."lettres_suivis SET quireception_lettre_suivi = '".$_SESSION['login']."', reponse_date_lettre_suivi = '".$date_fiche."', reponse_remarque_lettre_suivi = '".$remarque_lettre_suivi[0]."', statu_lettre_suivi = '".$statu_lettre[0]."' WHERE id_lettre_suivi = '".$id."'";
	        $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	}
 }


//si action_etiquette = ajout_etiquette_format
if(!empty($nom_etiquette_format) and empty($id) and $valide_form === 'yes')
 {
              $requete="INSERT INTO ".$prefix_base."etiquettes_formats (nom_etiquette_format) VALUES ('".$nom_etiquette_format."')";
              $execution_requete = mysqli_query($GLOBALS["mysqli"], $requete);
	      $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
 }

//si action_lettre = modifier_cadre
if($action_etiquette === 'modifier_etiquette_format' and $valide_form === 'yes')
 {
	if(!empty($id))
	{
		$requete="UPDATE ".$prefix_base."etiquettes_formats SET nom_etiquette_format = '".$nom_etiquette_format."', xcote_etiquette_format = '".$xcote_etiquette_format."', ycote_etiquette_format = '".$ycote_etiquette_format."', espacementx_etiquette_format = '".$espacementx_etiquette_format."', espacementy_etiquette_format = '".$espacementy_etiquette_format."', largeur_etiquette_format = '".$largeur_etiquette_format."', hauteur_etiquette_format = '".$hauteur_etiquette_format."', nbl_etiquette_format = '".$nbl_etiquette_format."', nbh_etiquette_format = '".$nbh_etiquette_format."' WHERE  id_etiquette_format = '".$id."'";
	        $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	}
 }


//si action_etiquette = supprimer_etiquette_format
if($action_etiquette === 'supprimer_etiquette_format' and $valide_form === 'yes')
 {
	if(!empty($id))
	{
     		$req_delete = "DELETE FROM ".$prefix_base."etiquettes_formats WHERE id_etiquette_format = '".$id."'";
       		$req_sql2 = mysqli_query($GLOBALS["mysqli"], $req_delete);
	}
 }


//requête :
$sql_cpe = 'SELECT '.$prefix_base.'utilisateurs.login, '.$prefix_base.'utilisateurs.nom, '.$prefix_base.'utilisateurs.prenom, '.$prefix_base.'utilisateurs.civilite FROM '.$prefix_base.'utilisateurs WHERE '.$prefix_base.'utilisateurs.statut="cpe" ORDER BY '.$prefix_base.'utilisateurs.nom, '.$prefix_base.'utilisateurs.prenom ASC';
?>

<script type='text/javascript' language='javascript'>

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

function affichercacher(a) {

   c = a.substr(4);
   var b = document.getElementById(a);

	var f = "img_"+c+"";

       if (b.style.display == "none" || b.style.display == "") {
         b.style.display = "block";
	 document.images[f].src="../../images/fleche_a.gif";
       }
       else
       {
         b.style.display = "none";
	 document.images[f].src="../../images/fleche_na.gif";
       }
 }

function activedesactive(mavar,devar)
{

mavar = mavar.split('-');

	for (var i in mavar)
	{
		if (document.getElementById(devar).checked == false)
		{
			//document.getElementById(mavar[i]).disabled=false;
			if (document.getElementById(mavar[i]).checked) { document.getElementById(mavar[i]).checked=false; }
		} else {
			  //document.getElementById(mavar[i]).disabled=true;
			  if (document.getElementById(mavar[i]).checked) { document.getElementById(mavar[i]).checked=false; }
		       }
	}
}


affichercacher('div_1');
// si JavaScript est disponible, cache le contenu dès le
// chargement de la page. Sans JavaScript, le contenu sera
// affiché.
</script>

<!-- ENTETE PRINCIPAL -->
<p class=bold><a href='gestion_absences.php?type=<?php echo $type; ?>&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>'><img src="../../images/icons/back.png" alt="Retour" title="Retour" class="back_link" />&nbsp;Retour</a>|
<a href="impression_absences.php?year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Impression</a> |
<a href="statistiques.php?year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Statistiques</a> |
<a href="gestion_absences.php?choix=lemessager&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Le messager</a> |
<a href="alert_suivi.php?choix=alert&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Système d'alerte</a>
</p>
<!-- ENTETE PROPOSANT LES CHOIX -->
<div class="norme_absence centre">
	[ <a href="impression_absences.php?type_impr=laf">Lettres aux familles</a> |
	 <a href="impression_absences.php?type_impr=bda">Bilan des absences</a> |
	 <a href="impression_absences.php?type_impr=bpc">Bilan conseils</a> |
	 <a href="impression_absences.php?type_impr=bj">Bilan journalier</a> |
	 <a href="impression_absences.php?type_impr=fic">Fiches récapitulatives</a> |
	 <a href="impression_absences.php?type_impr=eti">Etiquettes</a> |
	 <a href='impression_absences.php?type_impr=crea_lettre'>Gestion de la création des types de lettre</a> ]
	 </div><br />



<?php if($type_impr == "laf") { ?>
<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center;">

	<?php

	/* ******************************************************************* */
	/* DEBUT - Bloc form pour la génération du PDF des lettres aux parents */
	if ( $form_pdf_lettre === 'affiche' and $_POST['Submit3']==='Sélectionner pour envoi' )
	{

		?>
		<form method="post" action="lettre_pdf.php" name="imprime_pdf_ok" target="_blank">
	  		<fieldset style="width: 90%; margin: auto; border: 1px solid;"><legend>Votre sélection</legend>
	  		 	<input type="hidden" name="id_lettre_suivi" value='<?php echo serialize($id_lettre_suivi); ?>' />
	  		 	<input type="hidden" name="lettre_action" value="<?php echo $lettre_action; ?>" />
				En cliquant sur le bouton ci-après, vous imprimez les lettres sélectionnées et passer leur état à Envoyé
	  		 	<center>
				<input type="submit" id="valider_pdf" name="creer_pdf" value="Générer le fichier PDF" />
				</center>
			</fieldset>
		</form>


	<?php

	}
	/* FIN - Bloc form pour la génération du PDF des lettres aux parents */
	/* ***************************************************************** */


	/* ******************************************************************* */
	/* DEBUT - Bloc form pour l'effacement des lettres aux parents */
	if ( $form_pdf_lettre === 'affiche' and $_POST['Submit3']==='Effacer les lettres selectionnées' and $_POST['id_lettre_suivi'][0]!='')
	{
	$rq_efface_courriers='';
	$succes_efface_courriers=1;
	foreach ($_POST['id_lettre_suivi'] as $i => $value) 
		{
		$rq_efface_courriers='DELETE FROM `lettres_suivis` WHERE `id_lettre_suivi`='.$_POST['id_lettre_suivi'][$i].';';
		$resultat_efface_courriers = mysqli_query($GLOBALS["mysqli"], $rq_efface_courriers) or die('Erreur SQL !'.$rq_efface_courriers.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		if ($resultat_efface_courriers!=1){$succes_efface_courriers=0;}
		}
	if($succes_efface_courriers==1){echo('<table class="table_erreur" border="0"><tr><td class="erreur">Les lettres ont été effacées !</td></tr></table>');}
	}
	/* FIN - Bloc form pour l'effacement des lettres aux parents  */
	/* ***************************************************************** */

	/* ******************************************************************* */
	/* DEBUT - Bloc form pour l'assemblage des courriers */
	if ( $form_pdf_lettre === 'affiche' and $_POST['Submit3']==='Assembler les courriers' and $_POST['id_lettre_suivi'][0]!='')
	{
		/*On filtre la liste sur les courriers EN ATTENTE*/
		$liste='';
		foreach ($_POST['id_lettre_suivi'] as $i => $value) 
			{
				if ($i!=0){$liste=$liste.','.$value;}else{$liste=$value;}
			}
		/*echo($liste);*/
		$rq_liste='SELECT `id_lettre_suivi`,`quirecois_lettre_suivi`,`partdenum_lettre_suivi` FROM `lettres_suivis` WHERE `id_lettre_suivi` IN ('.$liste.') AND `envoye_date_lettre_suivi` = "0000-00-00" AND `statu_lettre_suivi`="en attente" ORDER BY `quirecois_lettre_suivi`,`emis_date_lettre_suivi`;';
		$resultat_rq_liste = mysqli_query($GLOBALS["mysqli"], $rq_liste) or die('Erreur SQL !'.$rq_liste.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		/* boucle de rassemblement des absences concernées par un courrier*/
		$precedent=Array("","","");
		while($courrier=mysqli_fetch_array($resultat_rq_liste))
			{
			/*echo(" ".$courrier[0]." ".$courrier[1]." ".$courrier[2]." <br>");*/
			if ($precedent[1]==$courrier[1])
				{
					/*Même login, donc ajouter courier2 à listeabsences */
					$liste_absences=ereg_replace(",{2,}", ",",$liste_absences.','.$courrier[2]);
					/*echo($liste_absences.'<BR>');*/
					/*et ecrire  le sql du courier n°courier[0] avec liste absences*/
					$rq_ajout_courrier='UPDATE `lettres_suivis` SET `envoye_date_lettre_suivi`=0000-00-00 ,`quirecois_lettre_suivi`="'.$courrier[1].'",`quiemet_lettre_suivi`="'.$_SESSION['login'].'" ,`partdenum_lettre_suivi`="'.$liste_absences.'",`statu_lettre_suivi`="en attente", `reponse_date_lettre_suivi`=0000-00-00 ,`envoye_heure_lettre_suivi`=0  WHERE ( `id_lettre_suivi`="'.$courrier[0].'");';
					/*echo($rq_ajout_courrier);*/
					mysqli_query($GLOBALS["mysqli"], $rq_ajout_courrier) or die('Erreur SQL !'.$rq_liste.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					/*on doit effacer l enregistrement précédent[0] dans sql*/
					$rq_suppr_prece='DELETE FROM `lettres_suivis` WHERE `id_lettre_suivi`='.$precedent[0].';';
					/*echo($rq_suppr_prece.'<br>');*/
					mysqli_query($GLOBALS["mysqli"], $rq_suppr_prece) or die('Erreur SQL !'.$rq_suppr_prece.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					$precedent=$courrier;
				}
			else 
				{			
					/*login différent donc on réinitialise precedent*/
					$precedent=$courrier;
					$liste_absences=ereg_replace(",{2,}", ",",$courrier[2]);
					/*$liste_absences=ereg_replace("^,", "", $courrier[2]);*/
					/*echo($liste_absences.'<BR>');*/
				}
			}
		echo('<table class="table_erreur" border="0"><tr><td class="erreur">Les courriers sélectionnés ont été fusionnés !</td></tr></table>');
	}
	/* FIN - Bloc form pour l'assemblage des courriers  */
	/* ***************************************************************** */



	/* ******************************************************************* */
	/* DEBUT - Bloc form pour repasser les courriers au statut EN ATTENTE */
	if ( $form_pdf_lettre === 'affiche' and $_POST['Submit3']==='Réinitialiser les envois de courriers' and $_POST['id_lettre_suivi'][0]!='')
	{
	$rq_repasse_courriers_attente='';
	$succes_repasse_courriers_attente=1;
	foreach ($_POST['id_lettre_suivi'] as $i => $value) 
		{
		$rq_repasse_courriers_attente=' UPDATE `lettres_suivis` SET `envoye_date_lettre_suivi`=0000-00-00 ,`quireception_lettre_suivi`="",`quienvoi_lettre_suivi`="" ,`statu_lettre_suivi`="en attente", `reponse_date_lettre_suivi`=0000-00-00 ,`envoye_heure_lettre_suivi`=0  WHERE ( `id_lettre_suivi`='.$_POST['id_lettre_suivi'][$i].' AND `reponse_date_lettre_suivi`=0000-00-00 );';
		$resultat_repasse_courriers_attente = mysqli_query($GLOBALS["mysqli"], $rq_repasse_courriers_attente) or die('Erreur SQL !'.$rq_repasse_courriers_attente.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		if ($resultat_repasse_courriers_attente!=1){$succes_repasse_courriers_attente=0;}
		/*echo($rq_repasse_courriers_attente);*/
		}
	if($succes_repasse_courriers_attente==1){echo('<table class="table_erreur" border="0"><tr><td class="erreur">Les lettres ont été réinitialisées !</td></tr></table>');}
	}
	/* FIN - Bloc form pour repasser les courriers au statut EN ATTENTE  */
	/* ***************************************************************** */



	/* ******************************************************************* */
	/* DEBUT - Bloc form pour réception de reponse */
	if ( $form_pdf_lettre === 'affiche' and $_POST['Submit3']==='Considérer les réponses comme recues' and $_POST['id_lettre_suivi'][0]!='')
	{
	$rq_reponse_recue='';
	$succes_reponse_recue=1;
	foreach ($_POST['id_lettre_suivi'] as $i => $value) 
		{
		$today = date("Y-m-d");
		/*echo($today);
		echo($_SESSION['login']);*/
		$rq_reponse_recue='UPDATE `lettres_suivis` SET `quireception_lettre_suivi`="'.$_SESSION['login'].'", `reponse_date_lettre_suivi`="'.$today.'", `statu_lettre_suivi`="recus" WHERE ( `id_lettre_suivi`='.$_POST['id_lettre_suivi'][$i].' AND `envoye_date_lettre_suivi`!=0000-00-00 );';
		$resultat_rq_reponse_recue = mysqli_query($GLOBALS["mysqli"], $rq_reponse_recue) or die('Erreur SQL !'.$rq_reponse_recue.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		if ($resultat_rq_reponse_recue!=1){$succes_reponse_recue=0;}
		/*echo($rq_reponse_recue);*/
		}
	if($succes_reponse_recue==1){echo('<table class="table_erreur" border="0"><tr><td class="erreur">Les lettres sélectionnées ont été reçues !</td></tr></table>');}
	}
	/* FIN - Bloc form pour réception de reponse */
	/* ***************************************************************** */


	?>

<!-- DEBUT DU FORMULAIRE DE CHOIX D'AFFICHAGE DES LETTRES  FORM1 -->
  
  <form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>" name="form1">
      <fieldset style="width: 460px; margin: auto; padding: 4px" class="couleur_ligne_3">
         <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Lettres aux familles</div>
            <div class="norme_absence" style="text-align: center;">

<?php
		$crearequete = '';
		if($lettre_type != '') {
			if($choix != 'tous') { $crearequete = 'AND type_lettre_suivi = \''.$lettre_type.'\''; }
		}
		if($choix != '') {
			if($choix === '2') { $crearequete = $crearequete.' AND envoye_date_lettre_suivi <> "0000-00-00"'; }
			if($choix === '3') { $crearequete = $crearequete.' AND envoye_date_lettre_suivi = "0000-00-00"'; }
			if($choix === '4') { $crearequete = $crearequete.' AND reponse_date_lettre_suivi = "0000-00-00"'; }
			if($choix === '5') { $crearequete = $crearequete.' AND reponse_date_lettre_suivi <> "0000-00-00"'; }
		}

			if($action_lettre === '1' or $action_lettre === '') { $crearequete = $crearequete.' AND emis_date_lettre_suivi = \''.date_sql($du).'\''; }
			if($action_lettre === '2') { $crearequete = $crearequete.' AND envoye_date_lettre_suivi = \''.date_sql($du).'\''; }
			if($action_lettre === '3') { $crearequete = $crearequete.' AND reponse_date_lettre_suivi = \''.date_sql($du).'\''; }
			if($action_lettre === '4') { $crearequete = $crearequete.' AND emis_date_lettre_suivi >= \''.date_sql($du).'\''; }
			if($action_lettre === '5') { $crearequete = $crearequete; }


?>


<table style="border: 0px;" cellspacing="0" cellpadding="0">
<tr>
<td>
		 <select name="lettre_type" size="4" style="width: 250px; border: 0px solid #000000;">
		    <option value="" <?php if(empty($lettre_type) or $lettre_type === '') { ?>selected="selected"<?php } ?>>Tous</option>
		    <?php
			  $categorie_pass = '';
			  $requete_lettre ="SELECT * FROM ".$prefix_base."lettres_types, ".$prefix_base."lettres_suivis WHERE id_lettre_type = type_lettre_suivi ".$crearequete."GROUP BY id_lettre_type ORDER BY titre_lettre_type ASC";
		          $execution_lettre = mysqli_query($GLOBALS["mysqli"], $requete_lettre) or die('Erreur SQL !'.$requete_lettre.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	  		while ($donner_lettre = mysqli_fetch_array($execution_lettre))
		  	 {
			   ?><option value="<?php echo $donner_lettre['id_lettre_type']; ?>" <?php if (isset($lettre_type) and $lettre_type === $donner_lettre['id_lettre_type']) { ?>selected="selected"<?php } ?>><?php echo ucfirst($donner_lettre['titre_lettre_type']); ?></option><?php echo "\n";
			 }
			?>
		  </select>
</td>
<td style="text-align: right;">
<select name="choix" style="width: 198px; border: 1px solid #000000; cursor: pointer; margin-bottom: 2px;">
<option value="1" <?php if(empty($choix) or (!empty($choix) and $choix === '1')) { ?>selected="selected"<?php } ?>>Tous les courriers</option>
<option value="2" <?php if(!empty($choix) and $choix === '2') { ?>selected="selected"<?php } ?>>Courriers expédiés</option>
<option value="3" <?php if(!empty($choix) and $choix === '3') { ?>selected="selected"<?php } ?>>Courriers non expédiés</option>
<option value="4" <?php if(!empty($choix) and $choix === '4') { ?>selected="selected"<?php } ?>>Courriers sans réponse</option>
<option value="5" <?php if(!empty($choix) and $choix === '5') { ?>selected="selected"<?php } ?>>Courriers avec réponse</option>
</select><br />
<select name="action_lettre" style="width: 90px; border: 1px solid #000000; cursor: pointer;">
<option value="1" <?php if(empty($action_lettre) or (!empty($action_lettre) and $action_lettre === '1')) { ?>selected="selected"<?php } ?>>&eacute;mis le</option>
<option value="2" <?php if(!empty($action_lettre) and $action_lettre === '2') { ?>selected="selected"<?php } ?>>postés le</option>
<option value="3" <?php if(!empty($action_lettre) and $action_lettre === '3') { ?>selected="selected"<?php } ?>>r&eacute;ponse re&ccedil;ue le</option>
<option value="4" <?php if(!empty($action_lettre) and $action_lettre === '4') { ?>selected="selected"<?php } ?>>émis depuis le</option>
<option value="5" <?php if(!empty($action_lettre) and $action_lettre === '5') { ?>selected="selected"<?php } ?>>ne pas tenir compte de la date</option>
</select>

<!--INSERER ICI UNE POSSIBILITE DE CHOIX DE CLASSE -->

<input name="du" type="text" size="9" maxlength="10" style="width: 80px; border: 1px solid #000000;" value="<?php if(isset($du)) { echo $du; } ?>" /><a href="#calend" onClick="<?php echo $cal->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a><br />
<input type="submit" name="Submit" value="Lister la sélection" style="cursor: pointer;" /></td>
</tr>
</table>

</div>

      </fieldset>
    </form>
<!-- Fin du FORMULAIRE de Choix du type de lettre-->

<br />

<!-- TABLEAU DES LETTRES -- COCHABLE !   --- FORM2 -->
<?php /*   <form method="post" action="lettre_pdf.php?type_impr=<?php echo $type_impr; ?>&amp;choix=<?php echo $choix; ?>&amp;lettre_action=originaux" name="form2"> */ ?>
   <form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>&amp;lettre_action=originaux" name="form2">
      <?php if($type_impr == "laf" and $choix != '') { ?>

	<table style="margin: auto; text-align: left; width: 750px; border: 2px solid #000000; background-color: #FFFFFF;" cellpadding="0" cellspacing="1">	  <tbody>
	    <tr class="fond_vert">
	      <td align="center" nowrap="nowrap" valign="middle" style="width: 20px; font-weight:bold;"></td>
	      <td align="center" nowrap="nowrap" valign="middle" class="norme_absence_blanc" style="width: 280px; font-weight:bold;">personnes concern&eacute;es</td>
	      <td align="center" nowrap="nowrap" valign="middle" class="norme_absence_blanc" style="width: 120px; font-weight:bold;">courrier type</td>
	      <td align="center" nowrap="nowrap" valign="middle" class="norme_absence_blanc" style="width: 90px; font-weight:bold;">&eacute;mis</td>
	      <td align="center" nowrap="nowrap" valign="middle" class="norme_absence_blanc" style="width: 90px; font-weight:bold;">envoy&eacute;</td>
	      <td align="center" nowrap="nowrap" valign="middle" class="norme_absence_blanc" style="width: 90px; font-weight:bold;">r&eacute;ponse</td>
	      <td align="center" nowrap="nowrap" valign="middle" class="norme_absence_blanc" style="width: 80px; font-weight:bold;">statut</td>
		</tr>
	    <?php $i = '0'; $ic = '1';
		//while ( $i < 5)

		//$requete_liste_courrier = "SELECT * FROM ".$prefix_base."lettres_suivis, ".$prefix_base."eleves WHERE login = quirecois_lettre_suivi ".$crearequete." ORDER BY nom ASC, prenom ASC";
		$requete_liste_courrier = "SELECT DISTINCT lettres_suivis.*, eleves.* FROM lettres_suivis, eleves, j_eleves_classes
		  									WHERE eleves.login = quirecois_lettre_suivi ".$crearequete."
		  									AND eleves.login = j_eleves_classes.login
											ORDER BY j_eleves_classes.id_classe ASC, nom ASC, prenom ASC";
		// On ajoute un paramètre sur les élèves de ce CPE en particulier
		$sql_eleves_cpe = "SELECT e_login FROM j_eleves_cpe WHERE cpe_login = '".$_SESSION['login']."'";
		$query_eleves_cpe = mysqli_query($GLOBALS["mysqli"], $sql_eleves_cpe) OR die('Erreur SQL ! <br />' . $sql_eleves_cpe . ' <br /> ' . ((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$test = array();

		$test_nbre_eleves_cpe = mysqli_num_rows($query_eleves_cpe);
		while($test_cpe = mysqli_fetch_array($query_eleves_cpe)){
			$test[] = $test_cpe['e_login'];
		}

		$varcoche = ''; //variable des checkbox pour la fonction javascript
		$resultat_liste_courrier = mysqli_query($GLOBALS["mysqli"], $requete_liste_courrier) or die('Erreur SQL !'.$requete_liste_courrier.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$nombre_d_entre = mysqli_num_rows($resultat_liste_courrier);
	while ( $donner_liste_courrier = mysqli_fetch_array($resultat_liste_courrier))
	{
		if (in_array($donner_liste_courrier['login'], $test) OR $test_nbre_eleves_cpe === 0) {

			if ($ic === '1') {
				$ic = '2';
				$couleur_cellule = 'td_tableau_absence_1';
			} else {
				$couleur_cellule = 'td_tableau_absence_2';
				$ic = '1';
			}

			?>
		<tr id="tr<?php echo $i; ?>" class="<?php echo $couleur_cellule; ?>" onmouseover="document.getElementById('tr<?php echo $i; ?>').className='td_tableau_sel';" onmouseout="document.getElementById('tr<?php echo $i; ?>').className='<?php echo $couleur_cellule; ?>';">
			<td align="center" nowrap="nowrap" valign="middle">
	      		<a name="n<?php echo $donner_liste_courrier['id_lettre_suivi']; ?>"></a>
			<?php
			if ( $donner_liste_courrier['statu_lettre_suivi'] != 'recus' )
			{ ?>
				<input type="checkbox" name="id_lettre_suivi[]" id="sel<?php echo $i; ?>" value="<?php echo $donner_liste_courrier['id_lettre_suivi']; ?>" onclick="document.getElementById('tr<?php echo $i; ?>').className='td_tableau_sel';" />
	    	<?php $varcoche = $varcoche."'sel".$i."',";
			}?>

	      	</td>
	      	<td align="center" nowrap="nowrap" valign="middle" style="text-align: left;"><label for="sel<?php echo $i; ?>"><?php echo '<strong>'.$donner_liste_courrier['nom'].' '.$donner_liste_courrier['prenom'].'</strong> ('.classe_de($donner_liste_courrier['login']).')'; ?></label></td>
	      	<td align="center" nowrap="nowrap" valign="middle"><small><?php echo lettre_type($donner_liste_courrier['type_lettre_suivi']); ?></small></td>
	      	<td align="center" nowrap="nowrap" valign="middle"><?php $datation = date_frl($donner_liste_courrier['emis_date_lettre_suivi']).' à '.heure_texte_court($donner_liste_courrier['emis_heure_lettre_suivi']).' par: '.qui_court($donner_liste_courrier['quiemet_lettre_suivi']); echo '<span title="'.$datation.'">'.date_fr($donner_liste_courrier['emis_date_lettre_suivi']).'</span>'; ?></td>
	      	<td align="center" nowrap="nowrap" valign="middle">
							<?php if($donner_liste_courrier['envoye_date_lettre_suivi'] != '0000-00-00') { $datation = date_frl($donner_liste_courrier['envoye_date_lettre_suivi']).' à '.heure_texte_court($donner_liste_courrier['envoye_heure_lettre_suivi']).' par: '.qui_court($donner_liste_courrier['quienvoi_lettre_suivi']); ?>
							<span title="<?php echo $datation; ?>"><?php if ( $donner_liste_courrier['statu_lettre_suivi'] === 'recus' ) { echo date_fr($donner_liste_courrier['envoye_date_lettre_suivi']); } else { ?><a href="impression_absences.php?type_impr=<?php echo $type_impr; ?>&amp;choix=<?php echo $choix; ?>&amp;action_lettre=<?php echo $action_lettre; ?>&amp;du=<?php echo $du; ?>&amp;lettre_type=<?php echo $lettre_type; ?>&amp;id=<?php echo $donner_liste_courrier['id_lettre_suivi']; ?>&amp;action_laf=reinit_envoi&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>" 							onClick="return confirm('Etes-vous sur de vouloir réinitialiser la date d\'envoi ?')"><?php echo date_fr($donner_liste_courrier['envoye_date_lettre_suivi']); ?></a><?php } ?></span><?php } else { if($donner_liste_courrier['statu_lettre_suivi'] === 'annuler') { ?>annuler<?php } else { ?>en attente<?php } } ?></td>	      	<td align="center" nowrap="nowrap" valign="middle"><?php if ( $donner_liste_courrier['statu_lettre_suivi'] === 'envoyer' ) { ?><?php } elseif($donner_liste_courrier['reponse_date_lettre_suivi'] != '0000-00-00') { $datation = date_frl($donner_liste_courrier['reponse_date_lettre_suivi']); echo '<span title="'.$datation.'">'.date_fr($donner_liste_courrier['reponse_date_lettre_suivi']).'</span>';  } else { if($donner_liste_courrier['statu_lettre_suivi'] === 'annuler') { ?>annuler<?php } else { ?>en attente<?php } } ?></td>
	      	<td align="center" nowrap="nowrap" valign="middle"><a href="impression_absences.php?type_impr=<?php echo $type_impr; ?>&amp;choix=<?php echo $choix; ?>&amp;action_lettre=<?php echo $action_lettre; ?>&amp;du=<?php echo $du; ?>&amp;lettre_type=<?php echo $lettre_type; ?>&amp;id=<?php echo $donner_liste_courrier['id_lettre_suivi']; ?>&amp;action_laf=aff_status&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>#n<?php echo $donner_liste_courrier['id_lettre_suivi']; ?>" title="modifier l'état de réception"><?php echo $donner_liste_courrier['statu_lettre_suivi']; ?></a></td>
	    </tr>
			<?php
			if( $action_laf === 'aff_status' and $id === $donner_liste_courrier['id_lettre_suivi'])
			{
	    	?>
		<tr id="tra<?php echo $i; ?>" class="<?php echo $couleur_cellule; ?>" onmouseover="document.getElementById('tr<?php echo $i; ?>').className='td_tableau_sel'; document.getElementById('tra<?php echo $i; ?>').className='td_tableau_sel';" onmouseout="document.getElementById('tr<?php echo $i; ?>').className='<?php echo $couleur_cellule; ?>'; document.getElementById('tra<?php echo $i; ?>').className='<?php echo $couleur_cellule; ?>';">
	      	<td align="center" nowrap="nowrap" valign="middle"></td>
	      	<td align="center" nowrap="nowrap" valign="middle" style="text-align: left;" colspan="7" >Statut
				<select name="statu_lettre[0]" style="width: 130px; border: 1px solid #000000;">
					<option value="envoyer" <?php if($donner_liste_courrier['statu_lettre_suivi'] === 'envoyer') { ?>selected="selected"<?php } ?>>envoyé</option>
					<option value="en attente" <?php if($donner_liste_courrier['statu_lettre_suivi'] === 'en attente') { ?>selected="selected"<?php } ?>>en attente</option>
					<option value="recus" <?php if($donner_liste_courrier['statu_lettre_suivi'] === 'recus') { ?>selected="selected"<?php } ?>>réponses reçue</option>
					<option value="annuler" <?php if($donner_liste_courrier['statu_lettre_suivi'] === 'annuler') { ?>selected="selected"<?php } ?>>courriers annulé</option>
				</select>
				Remarque
				<input type="texte" name="remarque_lettre_suivi[0]" style="width: 150px; border: 1px solid #000000;" />
				<input type="hidden" name="action_laf" value="modif_status" />
				<input type="hidden" name="id" value="<?php echo $id; ?>" />
				<input type="submit" name="Submit" value="<<" style="cursor: pointer;" /></td>
	      	</td>
	    </tr>
			<?php
			}
			$i = $i + 1;
		}
	} // fin du while ?>
		<tr class="fond_vert">
			<td colspan="7" class="norme_absence_blanc"><?php if($nombre_d_entre!='' and $nombre_d_entre!='0') { ?>Nombre de lettres affichées <strong><?php echo $nombre_d_entre.'</strong>'; } else { ?>Aucune sélection<?php } ?></td>
	    </tr>
        <tr>
            <td colspan="2" class="norme_absence">
				<?php $varcoche = $varcoche."'form2'"; ?>
				<a href="javascript:CocheCheckbox(<?php echo $varcoche; ?>)">Cocher</a> | <a href="javascript:DecocheCheckbox(<?php echo $varcoche; ?>)">Décocher</a>
			</td>
            <td colspan="7" class="centre">
                <input type="hidden" name="du" value="<?php echo $du; ?>" />
                <input type="hidden" name="nbi" value="<?php echo $i; ?>" />

				<input type="hidden" name="type_impr" value="<?php echo $type_impr; ?>" />
				<input type="hidden" name="choix" value="<?php echo $choix; ?>" />
				<input type="hidden" name="action_lettre" value="<?php echo $action_lettre; ?>" />
				<input type="hidden" name="lettre_type" value="<?php echo $lettre_type; ?>" />
				<input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
		
		<input type="submit" name="Submit3" value="Effacer les lettres selectionnées"  onClick="return confirm('Etes-vous sur de vouloir supprimer les courriers cochés ?')" />
                <br>
		<input type="submit" name="Submit3" value="Assembler les courriers" onClick="return confirm('Si plusieurs courriers  sont en attente pour un élève, ils seront rassemblés en un seul courrier demandant justification pour toutes les absences. Etes-vous sûr de vouloir procéder à ce changement ?')" />
		<br>
		<input type="submit" name="Submit3" value="Réinitialiser les envois de courriers" onClick="return confirm('Le statut des courriers sélectionnés redeviendra EN ATTENTE et pourront être réexpédiés. Etes-vous sûr de vouloir procéder à ce changement ?')"/>
		<br>
		<input type="submit" name="Submit3" onClick="return confirm('Ceci va créer un fichier PDF des courriers aux parents. Les courriers changeront alors de statut et la date d'envoi sera enregistrée. Etes-vous sûr de vouloir envoyer les courriers sélectionnés ?')"  value="Sélectionner pour envoi" />
		<br>
		<input type="submit" name="Submit3" value="Considérer les réponses comme recues" onClick="return confirm('Les courriers qui ont été envoyés passeront à REPONSE RECUE. Etes-vous sur de vouloir procéder à ce changement ?')"/>		
            </td>
        </tr>
	</tbody>
</table>
      <?php } ?>
      </form>
<!-- FIN DU TABLEAU DES LETTRES -->



<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>

<!-- DEBUT DU FORMULAIRE POUR CHOIX DU BILAN DES ABSENCES -->
<?php if($type_impr == "bda") { ?>
<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center;">
   <form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>" name="form3">
      <fieldset style="width: 450px; margin: auto;" class="couleur_ligne_3">
         <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Bilan général des absences</div>
            <div class="norme_absence" style="text-align: left;">
            Classe
                <select name="classe">
                    <option value="tous">toutes</option>
                    <?php
                          $sql_classe = 'SELECT id, classe, nom_complet FROM '.$prefix_base.'classes ORDER BY nom_complet DESC';
                          $req_classe = mysqli_query($GLOBALS["mysqli"], $sql_classe) or die('Erreur SQL ! '.$sql_classe.' '.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

                          while($data_classe = mysqli_fetch_array($req_classe))
                          {
                            echo "<option value=\"".$data_classe['id']."\"";
                            if ($classe == $data_classe['id']) {echo " selected";}
                            echo " onClick=\"javascript:document.form3.submit()\">".$data_classe['nom_complet']." (".$data_classe['classe'].")</option>";
                          }
                    ?>
                </select><br />
                Elève
                <select name="eleve">
                    <option value="tous">tous</option>
                    <?php
                         if( $classe == "tous" ) { $sql_eleve = 'SELECT '.$prefix_base.'eleves.login, nom, prenom FROM '.$prefix_base.'eleves ORDER BY nom, prenom ASC'; }
                         if( $classe != "tous" ) { $sql_eleve = 'SELECT DISTINCT '.$prefix_base.'eleves.login, nom, prenom, '.$prefix_base.'j_eleves_classes.login, id_classe FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes WHERE '.$prefix_base.'j_eleves_classes.login='.$prefix_base.'eleves.login AND id_classe="'.$classe.'" ORDER BY nom, prenom ASC'; }

                          $req_eleve = mysqli_query($GLOBALS["mysqli"], $sql_eleve) or die('Erreur SQL ! '.$sql_eleve.' '.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

                          while($data_eleve = mysqli_fetch_array($req_eleve))
                          {
                            echo "<option value=\"".$data_eleve['login']."\"";
                            if ($eleve == $data_eleve['login']) {echo " selected";}
                            echo " >".strtoupper($data_eleve['nom'])." ".ucfirst($data_eleve['prenom'])."</option>";
                          }
                    ?>
                </select>
                <br />
                du <input name="du" type="text" size="11" maxlength="11" value="<?php echo $du; ?>" /><a href="#calend" onClick="<?php  echo $cal_3->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a> au <input name="au" id="au" type="text" size="11" maxlength="11" value="<?php echo $au; ?>" onClick="getDate(au,'form3')" /><a href="#calend" onClick="<?php  echo $cal_4->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
		<br /><a href="#ao" onclick="affichercacher('div_1')" style="cursor: pointer;"><img style="border: 0px solid ; width: 13px; height: 13px; border: none; padding:2px; margin:2px; float: left;" name="img_1" alt="" title="Information" src="../../images/fleche_na.gif" align="middle" />Autres options</a>
		<div id="div_1" style="display: <?php if( $absencenj != '' or $retardnj != '' ) { ?>block<?php } else { ?>none<?php } ?>; border-top: solid 1px; border-bottom: solid 1px; padding: 10px; background-color: #E0EEEF"><a name="ao"></a>
		  <span style="font-family: Arial;">
			<input name="absencenj" id="absencenj" value="1" type="checkbox" onclick="activedesactive('retardnj','absencenj');" <?php if ( $absencenj === '1' ) { ?>checked="checked"<?php } ?> /><label for="absencenj" style="cursor: pointer;">Lister seulement les absences non justifi&eacute;es</label>
		  	<br /><input name="retardnj" id="retardnj" value="1" type="checkbox" onclick="activedesactive('absencenj','retardnj');" <?php if ( $retardnj === '1' ) { ?>checked="checked"<?php } ?> /><label for="retardnj" style="cursor: pointer;">Lister seulement les retards non justifi&eacute;s</label>
		  </span>
		</div>
		<br /><div style="text-align: right;"><input type="submit" name="Submit2" value="Valider la sélection" /></div>
            </div>
      </fieldset>
    </form>

     <form method="post" action="bilan_absence.php?type_impr=<?php echo $type_impr; ?>&amp;choix=<?php echo $choix; ?>" name="form4">
        <fieldset style="width: 400px; margin: auto;" class="couleur_ligne_3">
            <legend class="legend_texte">&nbsp;Action&nbsp;</legend>
                <input type="hidden" name="classe" value="<?php echo $classe; ?>" />
                <input type="hidden" name="eleve" value="<?php echo $eleve; ?>" />
                <input type="hidden" name="du" value="<?php echo $du; ?>" />
                <input type="hidden" name="au" value="<?php echo $au; ?>" />
                <input type="hidden" name="absencenj" value="<?php echo $absencenj; ?>" />
                <input type="hidden" name="retardnj" value="<?php echo $retardnj; ?>" />
                <input type="submit" name="Submit32" value="Composer" />
        </fieldset>
     </form>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>


<!-- FORMULAIRE BILAN POUR CONSEIL -->
<?php if($type_impr == "bpc") { ?>
<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center;">
   <form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>" name="form5">
      <fieldset style="width: 450px; margin: auto;" class="couleur_ligne_3">
        <legend style="clear: both" class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Bilan des absences pour les conseils de classe</div>
            <div class="norme_absence" style="text-align: center;">du <input name="du" type="text" size="11" maxlength="11" value="<?php echo $du; ?>" /><a href="#calend" onClick="<?php  echo $cal_5->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a> au <input name="au" id="au" type="text" size="11" maxlength="11" value="<?php echo $au; ?>" onClick="getDate(au,'form5')" /><a href="#calend" onClick="<?php  echo $cal_6->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a><input type="submit" name="Submit2" value="&gt;&gt;" /></div>
      </fieldset>
   </form>
   <br />

       <form method="post" action="bilan.php?type_impr=<?php echo $type_impr; ?>&amp;choix=<?php echo $choix; ?>" name="form6">
        <table style="width: 600px; margin: auto;" border="0" cellpadding="0" cellspacing="1">
          <tr class="fond_vert">
            <td class="norme_absence_blanc" style="width: 80px"><strong>&agrave; imprimer</strong></td>
            <td class="norme_absence_blanc"><strong>Classe</strong></td>
            <td class="norme_absence_blanc"><strong>Expéditeur</strong></td>
          </tr>
        <?php
           $ic = '1';
           $i = '0';
           $niveau = "";
           $niveau_v = "";
	   $varcoche = '';
           $requete_1 ="SELECT id, classe, nom_complet FROM ".$prefix_base."classes ORDER BY nom_complet DESC";
           $execution_1 = mysqli_query($GLOBALS["mysqli"], $requete_1) or die('Erreur SQL !'.$requete_1.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
           while ( $data_1 = mysqli_fetch_array($execution_1))
               {
           if ($niveau == "") { if (mb_substr($data_1['nom_complet'],0,1) != mb_substr($niveau_v,0,1)) { ?><tr bgcolor="#5E938C"><td colspan="3"><div class="norme_absence_blanc"><strong><?php echo mb_substr($data_1['nom_complet'],0,-1); $ic='2'; ?></strong></div></td></tr><?php $niveau_v =$data_1['nom_complet'];} }
           if ($niveau != "") { if (mb_substr($niveau,0,1) != mb_substr($niveau_v,0,1)) { ?><tr bgcolor="#5E938C"><td colspan="3"><div class="norme_absence_blanc"><strong><?php echo $niveau; ?></strong></div></td></tr><?php $niveau_v =$data_1['nom_complet'];} }
              if ($ic === '1') { $ic = '2'; $couleur_cellule = 'td_tableau_absence_1'; } else { $couleur_cellule = 'td_tableau_absence_2'; $ic = '1'; }
                ?>
                  <tr class="<?php echo $couleur_cellule; ?>">
                    <td class="centre"><input type="checkbox" name="a_imprimer[<?php echo $i; ?>]" id="sel<?php echo $i; ?>" value="1"  <?php if(isset($a_imprimer[$i]) and $a_imprimer[$i] == "1" OR $cocher == '1') {?> checked<?php } ?> /><input type="hidden" name="id_classe[<?php echo $i; ?>]" value="<?php echo $data_1['id']; ?>" /><?php $varcoche = $varcoche."'sel".$i."',"; ?></td>
                    <td class="norme_absence"><?php echo $data_1['nom_complet']; ?></td>
                    <td>
                        <select name="cpe[<?php echo $i; ?>]">
                        <?php if($i!=0) { ?><option value="idem">idem</option><?php } ?>
                        <?php
                             $req_cpe = mysqli_query($GLOBALS["mysqli"], $sql_cpe) or die('Erreur SQL ! '.$sql_cpe.' '.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

                              while($data_cpe = mysqli_fetch_array($req_cpe))
                              {
                                echo "<option value=\"".$data_cpe['login']."\"";
                                if ($eleve == $data_cpe['login']) {echo " selected";}
                                echo " >".strtoupper($data_cpe['nom'])." ".ucfirst($data_cpe['prenom'])."</option>";
                              }
                        ?>
                        </select>
                    </td>
                  </tr>
         <?php $i = $i + 1; } ?>
           <tr>
            <td colspan="2" class="norme_absence">
		<?php $varcoche = $varcoche."'form6'"; ?>
		<a href="javascript:CocheCheckbox(<?php echo $varcoche; ?>)">Cocher</a> | <a href="javascript:DecocheCheckbox(<?php echo $varcoche; ?>)">Décocher</a>
	    </td>
            <td class="centre">
                <input type="hidden" name="du" value="<?php echo $du; ?>" />
                <input type="hidden" name="au" value="<?php echo $au; ?>" />
                <input type="submit" name="Submit33" value="Composer" />
            </td>
          </tr>
        </table>
    </form>

<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>


<!-- FORMULAIRE DE SELECTION DE LA DATE POUR LE BILAN JOURNALIER -->
<?php
if ( $type_impr == "bj" ) { ?>
<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center;">
   <form method="post" action="bilan_absences_quotidien_pdf.php" name="form6">
      <fieldset style="width: 450px; margin: auto;" class="couleur_ligne_3">
         <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Bilan journalier général</div>
            <div class="norme_absence" style="text-align: left;">
            <br />
            du <input name="du" type="text" size="11" maxlength="11" value="<?php echo $du; ?>" /><a href="#calend" onClick="<?php  echo $cal_7->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
			&nbsp;
			<input type="submit" name="Submit2" value="Générer le PDF" /></div>
			<br />
            </div>
      </fieldset>
    </form>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>


<!-- FORMULAIRE DE SELECTION POUR LA FICHE RECAPITULATIVE ELEVE, CLASSE, DATES -->
<?php // fiche récapitulative des absences
 if($type_impr === 'fic') { ?>
<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center;">
   <form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>" name="form3">
      <fieldset style="width: 450px; margin: auto;" class="couleur_ligne_3">
         <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Fiche récapitulative</div>
            <div class="norme_absence" style="text-align: center;">
		<table style="border: 0px; text-align: center; width: 100%;"><tr><td>
                <select name="classe_multiple[]" size="5" multiple="multiple" tabindex="3" style="width: 220px;">
		  <optgroup label="----- Listes des classes -----">
		    <?php
			if ($_SESSION["statut"] === 'cpe') {
	                        $requete_classe = mysqli_query($GLOBALS["mysqli"], 'SELECT * FROM '.$prefix_base.'classes, '.$prefix_base.'periodes WHERE '.$prefix_base.'periodes.id_classe = '.$prefix_base.'classes.id  GROUP BY id_classe ORDER BY '.$prefix_base.'classes.classe');
			} else {
		                        $requete_classe = mysqli_query($GLOBALS["mysqli"], 'SELECT * FROM '.$prefix_base.'classes, '.$prefix_base.'j_eleves_professeurs, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'periodes WHERE ('.$prefix_base.'j_eleves_professeurs.professeur="'.$_SESSION['login'].'" AND '.$prefix_base.'j_eleves_professeurs.professeur.login = '.$prefix_base.'j_eleves_classes.login AND '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id) AND '.$prefix_base.'periodes.id_classe = '.$prefix_base.'classes.id  GROUP BY id_classe ORDER BY '.$prefix_base.'classes.classe');
			       }
	  		while ($donner_classe = mysqli_fetch_array($requete_classe))
		  	 {
				$requete_cpt_nb_eleve_1 =  mysqli_query($GLOBALS["mysqli"], 'SELECT count(*) FROM '.$prefix_base.'eleves, '.$prefix_base.'classes, '.$prefix_base.'j_eleves_classes WHERE '.$prefix_base.'classes.id = "'.$donner_classe['id_classe'].'" AND '.$prefix_base.'j_eleves_classes.id_classe='.$prefix_base.'classes.id AND '.$prefix_base.'j_eleves_classes.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login');
				$requete_cpt_nb_eleve = mysqli_num_rows($requete_cpt_nb_eleve_1);
			   ?><option value="<?php echo $donner_classe['id_classe']; ?>" <?php if(!empty($classe_multiple) and in_array($donner_classe['id_classe'], $classe_multiple)) { ?>selected="selected"<?php } ?> onClick="javascript:document.form3.submit()"><?php echo $donner_classe['nom_complet']." -".$donner_classe['classe']."- "; ?> (eff. <?php echo $requete_cpt_nb_eleve; ?>)</option><?php
			 }
			?>
		  </optgroup>
		  </select></td><td>
		  <select name="eleve_multiple[]" size="5" multiple="multiple" tabindex="4" style="width: 220px;">
		  <optgroup label="----- Listes des &eacute;l&egrave;ves -----">
		    <?php
			// sélection des id eleves sélectionné.
			if(!empty($classe_multiple[0]))
			{
				$cpt_classe_selec = 0; $selection_classe = "";
				while(!empty($classe_multiple[$cpt_classe_selec])) { if($cpt_classe_selec == 0) { $selection_classe = $prefix_base."j_eleves_classes.id_classe = ".$classe_multiple[$cpt_classe_selec]; } else { $selection_classe = $selection_classe." OR ".$prefix_base."j_eleves_classes.id_classe = ".$classe_multiple[$cpt_classe_selec]; } $cpt_classe_selec = $cpt_classe_selec + 1; }
	                        $requete_eleve = mysqli_query($GLOBALS["mysqli"], 'SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes WHERE ('.$selection_classe.') AND '.$prefix_base.'j_eleves_classes.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'eleves.nom ASC');
		  		while ($donner_eleve = mysqli_fetch_array($requete_eleve))
			  	 {
				   ?><option value="<?php echo $donner_eleve['login']; ?>" <?php if(!empty($eleve_multiple) and in_array($donner_eleve['login'], $eleve_multiple)) { ?> selected="selected"<?php } ?>><?php echo strtoupper($donner_eleve['nom'])." ".ucfirst($donner_eleve['prenom']); ?></option><?php
				 }
			}
			?>
		     <?php if(empty($classe_multiple[0]) and empty($eleve_multiple[0])) { ?><option value="" disabled="disabled">Vide</option><?php } ?>
		  </optgroup>
		  </select></td></tr></table>
                du <input name="du" type="text" size="11" maxlength="11" value="<?php echo $du; ?>" />
		<a href="#calend" onClick="<?php  echo $cal_3->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>">
		<img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a> au <input name="au" id="au" type="text" size="11" maxlength="11" value="<?php echo $au; ?>" onClick="getDate(au,'form3')" />
		<a href="#calend" onClick="<?php  echo $cal_4->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>">
		<img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>&nbsp;
		<input type="submit" name="Submit2" value="Valider" />
            </div>
      </fieldset>
    </form>

<!-- UNE FOIS LA SELECTION ELEVE, CLASSE, DATES FAITE, FORMULAIRE PERMETTANT DE COMPOSER  LE PDF -->
     <?php if ( !empty($classe_multiple[0]) ) { ?>
    <?php /* <form method="post" action="fiche_pdf.php?type_impr=<?php echo $type_impr; ?>&amp;choix=<?php echo $choix; ?>" name="form4"> */ ?>
    <form method="post" action="fiche_pdf.php" name="form4">
        <fieldset style="width: 400px; margin: auto;" class="couleur_ligne_3">
            <legend class="legend_texte">&nbsp;Action&nbsp;</legend>
                <input type="hidden" name="classe" value="<?php echo $classe; ?>" />
                <input type="hidden" name="eleve" value="<?php echo $eleve; ?>" />
                <input type="hidden" name="du" value="<?php echo $du; ?>" />
                <input type="hidden" name="au" value="<?php echo $au; ?>" />
                <input type="submit" name="Submit32" value="Composer" />
        </fieldset>
     </form>
     <?php } ?>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>



<!-- FORMULAIRE ETIQUETTES -->
<?php if($type_impr == "eti") { ?>
<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center;">
   <form method="post" action="etiquette_pdf.php?type_impr=<?php echo $type_impr; ?>&amp;choix=<?php echo $choix; ?>&amp;etiquette_action=originaux" name="form3">
      <fieldset style="width: 450px; margin: auto;" class="couleur_ligne_3">
         <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Impression d'étiquettes</div>
            <div class="norme_absence" style="text-align: left;">
            Classe
                <select name="classe">
                    <option value="tous">toutes</option>
                    <?php
                          $sql_classe = 'SELECT id, classe, nom_complet FROM '.$prefix_base.'classes ORDER BY nom_complet DESC';
                          $req_classe = mysqli_query($GLOBALS["mysqli"], $sql_classe) or die('Erreur SQL ! '.$sql_classe.' '.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

                          while($data_classe = mysqli_fetch_array($req_classe))
                          {
                            ?><option value=" <?php echo $data_classe['id']; ?>"> <?php echo $data_classe['nom_complet']." (".$data_classe['classe'].")"; ?></option><?php
                          }
                    ?>
                </select><br />
                Trié par
                <select name="trie_par">
                    <option value="1">Classe, Nom, Prénom</option>
                    <option value="2">Nom, Prénom</option>
                </select><br />
		Types d'étiquettes
                <select name="etiquette_type">
                    <option value="1">NOM Prénom, Classe</option>
                    <option value="2">NOM Prénom, Classe, Numéro élève</option>
                    <option value="3">NOM Prénom, Numéro élève</option>
                    <option value="4">Adresse responsable</option>
                    <option value="5">Adresse responsable + élève</option>
                </select><br />
		<a href="impression_absences.php?type_impr=eti&amp;etiquette_aff=<?php if($etiquette_aff === 'bibliotheque') { ?><?php } else { ?>bibliotheque<?php } ?>#crea_etiquette">Format d'étiquette</a>
		<select name="etiquette_format" style="width: 200px;">
                    <?php
                          $sql_etiquette = 'SELECT * FROM '.$prefix_base.'etiquettes_formats ORDER BY nom_etiquette_format ASC';
                          $req_etiquette = mysqli_query($GLOBALS["mysqli"], $sql_etiquette) or die('Erreur SQL ! '.$sql_etiquette.' '.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
                          while($donner_etiquette = mysqli_fetch_array($req_etiquette))
                          {
                            ?><option value="<?php echo $donner_etiquette['id_etiquette_format']; ?>"><?php echo $donner_etiquette['nom_etiquette_format']; ?></option><?php
                          }
                    ?>
                </select><input type="checkbox" name="cadre" value="1" title="Encadrer" />
                <input type="submit" name="Submit32" value="Sélectionner" />
            </div>
      </fieldset>
    </form>


	<?php if($etiquette_aff === 'bibliotheque') { ?>
	<br /><a name="crea_etiquette"></a>
	<form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>&amp;action_etiquette=bibliotheque" name="form10">
          <table style="width: 650px; margin: auto; border: 1px solid #000000;" cellpadding="0" cellspacing="0">
	    <tr class="fond_vert">
	     <td class="titre_tableau_gestion" colspan="3">Bibliothèque des formats d'étiquettes</td>
	    </tr>
            <tr class="fond_vert">
	      <td style="width: 17px;"></td>
	      <td style="width: 17px;"></td>
              <td class="norme_absence_blanc"><strong>Etiquettes</strong> - Nouveau <input name="nom_etiquette_format" value="" style="border: 1px solid #B3BFB8;" /><input type="hidden" name="type_impr" value="<?php echo $type_impr; ?>" /><input type="hidden" name="etiquette_aff" value="<?php echo $etiquette_aff; ?>" /><input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" /><input type="submit" name="Submit10" value="Créer" /></td>
            </tr>
	  <?php
           $ic = '1';  $i = '0';
           $requete_1 ="SELECT * FROM ".$prefix_base."etiquettes_formats ORDER BY nom_etiquette_format ASC";
           $execution_1 = mysqli_query($GLOBALS["mysqli"], $requete_1) or die('Erreur SQL !'.$requete_1.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
           while ( $data_1 = mysqli_fetch_array($execution_1))
               { if ($ic === '1') { $ic = '2'; $couleur_cellule = 'td_tableau_absence_1'; } else { $couleur_cellule = 'td_tableau_absence_2'; $ic = '1'; }
                ?>
                  <tr class="<?php echo $couleur_cellule; ?>">
	            <td style="text-align: center;"><a name="crea_etiquette<?php echo $data_1['id_etiquette_format']; ?>"></a><a href="impression_absences.php?type_impr=<?php echo $type_impr; ?>&amp;etiquette_aff=bibliotheque&amp;id=<?php echo $data_1['id_etiquette_format']; ?>&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>&amp;action_etiquette=aff_modifier_etiquette_format&amp;sous_rubrique=gb#crea_etiquette<?php echo $data_1['id_etiquette_format']; ?>"><img src="../../images/edit16.png" title="modifier le format d'étiquette" border="0" alt="" /></a></td>
	            <td style="text-align: center;"><a href="impression_absences.php?type_impr=<?php echo $type_impr; ?>&amp;etiquette_aff=<?php echo $etiquette_aff; ?>&amp;id=<?php echo $data_1['id_etiquette_format']; ?>&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>&amp;action_etiquette=supprimer_etiquette_format&amp;sous_rubrique=gb#crea_etiquette" onClick="return confirm('Etes-vous sur de vouloire le supprimer...')"><img src="../../images/delete16.png" title="supprimer le format d'étiquette" border="0" alt="" /></a></td>
                    <td class="norme_absence"><?php echo $data_1['nom_etiquette_format']; if($action_etiquette === 'aff_modifier_etiquette_format' and $id === $data_1['id_etiquette_format']) { ?>

			<input name="nom_etiquette_format" value="<?php echo $data_1['nom_etiquette_format']; ?>" style="border: 1px solid #B3BFB8;" /><br />
			Pos. X <input maxlength="6" size="4" name="xcote_etiquette_format" value="<?php echo $data_1['xcote_etiquette_format']; ?>" style="border: 1px solid #B3BFB8;" />
			Pos. Y <input maxlength="6" size="4" name="ycote_etiquette_format" value="<?php echo $data_1['ycote_etiquette_format']; ?>" style="border: 1px solid #B3BFB8;" />
			Largeur <input maxlength="6" size="4" name="largeur_etiquette_format" value="<?php echo $data_1['largeur_etiquette_format']; ?>" style="border: 1px solid #B3BFB8;" />
			Hauteur <input maxlength="6" size="4" name="hauteur_etiquette_format" value="<?php echo $data_1['hauteur_etiquette_format']; ?>" style="border: 1px solid #B3BFB8;" /><br />
			Espacement X <input maxlength="6" size="4" name="espacementx_etiquette_format" value="<?php echo $data_1['espacementy_etiquette_format']; ?>" style="border: 1px solid #B3BFB8;" />
			Espacement Y <input maxlength="6" size="4" name="espacementy_etiquette_format" value="<?php echo $data_1['espacementy_etiquette_format']; ?>" style="border: 1px solid #B3BFB8;" /><br />
			Nb. Etiq. Lar. <input maxlength="6" size="4" name="nbl_etiquette_format" value="<?php echo $data_1['nbl_etiquette_format']; ?>" style="border: 1px solid #B3BFB8;" />
			Nb. Etiq. Haut. <input maxlength="6" size="4" name="nbh_etiquette_format" value="<?php echo $data_1['nbh_etiquette_format']; ?>" style="border: 1px solid #B3BFB8;" />
			<input type="hidden" name="type_impr" value="<?php echo $type_impr; ?>" />
			<input type="hidden" name="etiquette_aff" value="<?php echo $action_etiquette; ?>" />
			<input type="hidden" name="id" value="<?php echo $data_1['id_etiquette_format']; ?>" />
			<input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
			<input type="hidden" name="action_etiquette" value="modifier_etiquette_format" />
			<input type="submit" value="valider" title="Valider les informations" alt="Valider les informations" />
		    <?php } ?></td>
                  </tr>
         <?php $i = $i + 1; } ?>
	</table>
	</form>

	<?php } ?>

<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>




<!-- FORMULAIRE GERER LES TYPES DE LETTRES -->
<?php if($type_impr === 'crea_lettre') { ?>
<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center;">

      <form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>" name="form6">
      <fieldset style="width: 450px; margin: auto;" class="couleur_ligne_3">
        <legend style="clear: both" class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Gestion des types de lettre</div>
            <div class="norme_absence" style="text-align: center;">
	    <select name="lettre_type" size="6" style="width: 448px; border: 1px solid #000000;">
		    <?php
			  $categorie_pass = '';
			  $requete_lettre ="SELECT * FROM ".$prefix_base."lettres_types ORDER BY categorie_lettre_type ASC, titre_lettre_type ASC";
		          $execution_lettre = mysqli_query($GLOBALS["mysqli"], $requete_lettre) or die('Erreur SQL !'.$requete_lettre.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	  		while ($donner_lettre = mysqli_fetch_array($execution_lettre))
		  	 {
			   if($categorie_pass != $donner_lettre['categorie_lettre_type']) {
				if($categorie_pass != '') { ?></optgroup><?php echo "\n"; }
			   	?><optgroup label="<?php echo ucfirst($donner_lettre['categorie_lettre_type']); ?>"><?php echo "\n";
			   }
			   $categorie_pass = $donner_lettre['categorie_lettre_type'];
			   ?><option value="<?php echo $donner_lettre['id_lettre_type']; ?>" <?php if (isset($lettre_type) and $lettre_type === $donner_lettre['id_lettre_type']) { ?>selected="selected"<?php } ?>><?php echo ucfirst($donner_lettre['titre_lettre_type']); ?></option><?php echo "\n";
			 }
			?>
		  </optgroup>
		  </select><br />
		  Si vous désirez en créer, saisissez le titre ici: <input name="lettre_type_nouv" style="width: 150px; border: 1px solid #000000;" value="<?php if ($action_choix_lettre === 'renommer') { $titre = titre_lettre_type($lettre_type); echo $titre[0]; } ?>" /><input name="reponse_lettre_type" value="oui" type="checkbox" title="désirez-vous une réponse à ce type de lettre" <?php if( ($action_choix_lettre === 'renommer' and $titre[1] === 'oui') or $action_choix_lettre != 'renommer' ) { ?>checked="checked"<?php } ?> />

  		  <input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
		  <?php if (!empty($erreur)) { ?><span class="erreur_rouge_jaune"><?php echo $erreur.' (<strong>'.$lettre_type_nouv.'</strong>)'; ?></span><?php } ?>
		  <br /><input name="action_choix_lettre" id="acl1" value="editer" type="radio" <?php if(empty($action_choix_lettre) or $action_choix_lettre != 'renommer') { ?>checked="checked"<?php } ?> /><label for="acl1" style="cursor: pointer;">Editer</label> <input name="action_choix_lettre" id="acl2" value="renommer" type="radio" <?php if(!empty($action_choix_lettre) and $action_choix_lettre === 'renommer') { ?>checked="checked"<?php } ?> /><label for="acl2" style="cursor: pointer;">Renommer</label> <input name="action_choix_lettre" id="acl3" value="supprimer" type="radio" /><label for="acl3" style="cursor: pointer;">Supprimer</label>
		  <?php if ($action_choix_lettre === 'modifier') { ?><input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" /><?php } ?>
		  &nbsp;<input type="submit" name="Submit6" value="Valider" />
		</div>
      </fieldset>
   </form>

   <?php // afficher les informations sur la lettre
	if($type_impr === 'crea_lettre' and !empty($lettre_type) and $action_choix_lettre != 'supprimer' and $action_choix_lettre != 'modifier') { ?>
     <br />
[ <a href='../gestion/impression_absences.php?type_impr=crea_lettre&amp;lettre_type=<?php echo $lettre_type; ?>'>Contenu de la lettre sélectionnée</a> | <a href='../gestion/impression_absences.php?type_impr=crea_lettre&amp;lettre_type=<?php echo $lettre_type; ?>&amp;sous_rubrique=gb'>Gestion de la bibliothèque des cadres</a> ]
<br />
<br />
<?php if(empty($sous_rubrique)) { ?>
<div style="float: right; margin-right: 20px; border: 1px solid #000000; background-color: #C2CFC5; height: 150px; width: 210px; background-image: url('../images/1.png'); background-repeat: repeat-x;">
      <form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>#crea_lettre" name="form7">
	    <strong style="color: #FFFFFF;">Cadres disponibles</strong><br />
	    <select name="cadre_selection[]" size="6" style="width: 200px; border: 0px solid #000000; filter:alpha(opacity=75); -moz-opacity:0.75; -khtml-opacity: 0.75; opacity: 0.75;" multiple="multiple">
		    <?php
			  $categorie_pass = '';
			  $requete_lettre ="SELECT * FROM ".$prefix_base."lettres_cadres ORDER BY nom_lettre_cadre ASC";
		          $execution_lettre = mysqli_query($GLOBALS["mysqli"], $requete_lettre) or die('Erreur SQL !'.$requete_lettre.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	  		while ($donner_lettre = mysqli_fetch_array($execution_lettre))
		  	 {
			   ?><option value="<?php echo $donner_lettre['id_lettre_cadre']; ?>"><?php echo ucfirst($donner_lettre['nom_lettre_cadre']); ?></option><?php echo "\n";
			 }
			?>
		  </select><br />
		  <input type="hidden" name="action_lettre" value="ajout_cadre" />
		  <input type="hidden" name="lettre_type" value="<?php echo $lettre_type; ?>" />
		  <input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
		  <input type="submit" name="Submit6" value="<< Ajouter la sélection" />
	</form>
</div>


     <a name="crea_lettre"></a>
     <form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>#crea_lettre" name="form8">
     <table style="width: 550px; margin: auto; border: 1px solid #000000;" cellpadding="0" cellspacing="0">
	  <tr class="fond_vert">
	   <td class="titre_tableau_gestion" colspan="8">Contenu de la lettre - <?php echo titre_lettre_type($lettre_type) ?></td>
	  </tr>
          <tr class="fond_vert">
	    <td style="width: 17px;"></td>
	    <td style="width: 17px;"></td>
            <td class="norme_absence_blanc"><strong>Nom du cadre</strong></td>
            <td class="norme_absence_blanc"><strong>Pos. X</strong></td>
            <td class="norme_absence_blanc"><strong>Pos. Y</strong></td>
            <td class="norme_absence_blanc"><strong>Largeur</strong></td>
            <td class="norme_absence_blanc"><strong>Hauteur</strong></td>
            <td class="norme_absence_blanc"><strong>Encadré</strong></td>
          </tr>
	  <?php
           $ic = '1';  $i = '0';
           $requete_1 ="SELECT * FROM ".$prefix_base."lettres_types, ".$prefix_base."lettres_cadres, ".$prefix_base."lettres_tcs WHERE id_lettre_type = '".$lettre_type."' AND id_lettre_type = type_lettre_tc AND id_lettre_cadre = cadre_lettre_tc ORDER BY y_lettre_tc ASC, x_lettre_tc ASC";
           $execution_1 = mysqli_query($GLOBALS["mysqli"], $requete_1) or die('Erreur SQL !'.$requete_1.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
           while ( $data_1 = mysqli_fetch_array($execution_1))
               { if ($ic === '1') { $ic = '2'; $couleur_cellule = 'td_tableau_absence_1'; } else { $couleur_cellule = 'td_tableau_absence_2'; $ic = '1'; }
                ?>
                  <tr class="<?php echo $couleur_cellule; ?>">
	            <td style="text-align: center;"><a name="crea_lettre<?php echo $data_1['cadre_lettre_tc']; ?>"></a><a href="impression_absences.php?type_impr=crea_lettre&amp;lettre_type=<?php echo $lettre_type; ?>&amp;id=<?php echo $data_1['id_lettre_tc']; ?>&amp;cadre_selection=<?php echo $data_1['cadre_lettre_tc']; ?>&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>&amp;action_lettre=aff_modifier_cadre#crea_lettre<?php echo $data_1['cadre_lettre_tc']; ?>"><img src="../../images/edit16.png" title="modifier le cadre" border="0" alt="" /></a></td>
	            <td style="text-align: center;"><a href="impression_absences.php?type_impr=crea_lettre&amp;lettre_type=<?php echo $lettre_type; ?>&amp;id=<?php echo $data_1['id_lettre_tc']; ?>&amp;cadre_selection=<?php echo $data_1['cadre_lettre_tc']; ?>&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>&amp;action_lettre=supprimer_cadre#crea_lettre" onClick="return confirm('Etes-vous sûr de vouloir le supprimer...')"><img src="../../images/delete16.png" title="supprimer le cadre" border="0" alt="" /></a></td>
                    <td class="norme_absence"><?php echo $data_1['nom_lettre_cadre']; ?></td>
                    <td class="norme_absence" style="text-align: center;"><?php if($action_lettre === 'aff_modifier_cadre' and $cadre_selection === $data_1['cadre_lettre_tc']) { ?><input maxlength="6" size="4" name="x_lettre_tc" value="<?php echo $data_1['x_lettre_tc']; ?>" style="border: 1px solid #B3BFB8;" /><?php } else { echo $data_1['x_lettre_tc']; } ?></td>
                    <td class="norme_absence" style="text-align: center;"><?php if($action_lettre === 'aff_modifier_cadre' and $cadre_selection === $data_1['cadre_lettre_tc']) { ?><input maxlength="6" size="4" name="y_lettre_tc" value="<?php echo $data_1['y_lettre_tc']; ?>" style="border: 1px solid #B3BFB8;" /><?php } else { echo $data_1['y_lettre_tc']; } ?></td>
                    <td class="norme_absence" style="text-align: center;"><?php if($action_lettre === 'aff_modifier_cadre' and $cadre_selection === $data_1['cadre_lettre_tc']) { ?><input maxlength="6" size="4" name="l_lettre_tc" value="<?php echo $data_1['l_lettre_tc']; ?>" style="border: 1px solid #B3BFB8;" /><?php } else { echo $data_1['l_lettre_tc']; } ?></td>
                    <td class="norme_absence" style="text-align: center;"><?php if($action_lettre === 'aff_modifier_cadre' and $cadre_selection === $data_1['cadre_lettre_tc']) { ?><input maxlength="6" size="4" name="h_lettre_tc" value="<?php echo $data_1['h_lettre_tc']; ?>" style="border: 1px solid #B3BFB8;" /><?php } else { echo $data_1['h_lettre_tc']; } ?></td>
                    <td class="norme_absence" style="text-align: center;"><?php if($action_lettre === 'aff_modifier_cadre' and $cadre_selection === $data_1['cadre_lettre_tc']) { ?><select name="encadre_lettre_tc" style="border: 1px solid #B3BFB8;"><option value="0" <?php if($data_1['encadre_lettre_tc'] === '0') { ?>selected="selected"<?php } ?>>non</option><option value="1" <?php if($data_1['encadre_lettre_tc'] === '1') { ?>selected="selected"<?php } ?>>oui</option></select>
			<input type="hidden" name="type_impr" value="crea_lettre" />
			<input type="hidden" name="lettre_type" value="<?php echo $lettre_type; ?>" />
			<input type="hidden" name="id" value="<?php echo $data_1['id_lettre_tc']; ?>" />
			<input type="hidden" name="cadre_selection" value="<?php echo $data_1['cadre_lettre_tc']; ?>" />
			<input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
			<input type="hidden" name="action_lettre" value="modifier_cadre" />
			<input type="image" src="../../images/enabled.png" style="border: 0px; width: 15px; height: 15px;" title="Valider les informations" alt="Valider les informations" /><?php } else { if($data_1['encadre_lettre_tc'] === '1') { ?>oui<?php } else { ?>non<?php }; } ?></td>
                  </tr>
         <?php $i = $i + 1; } ?>
	</table>
	</form>
<a href="lettre_pdf.php?lettre_type=<?php echo $lettre_type; ?>&amp;mode=apercus">Lancer un aperçu</a>

<?php }

if($sous_rubrique === 'gb') { ?>
     <a name="crea_lettre"></a>
     <form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>#crea_lettre" name="form8">
     <table style="width: 650px; margin: auto; border: 1px solid #000000;" cellpadding="0" cellspacing="0">
	  <tr class="fond_vert">
	   <td class="titre_tableau_gestion" colspan="3">Bibliothèque des cadres</td>
	  </tr>
          <tr class="fond_vert">
	    <td style="width: 17px;"></td>
	    <td style="width: 17px;"></td>
            <td class="norme_absence_blanc"><strong>Nom du cadre</strong> - Nouveau <input name="nom_lettre_cadre" value="" style="border: 1px solid #B3BFB8;" /><input type="hidden" name="type_impr" value="crea_lettre" /><input type="hidden" name="lettre_type" value="<?php echo $lettre_type; ?>" /><input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" /><input type="submit" name="Submit6" value="Créer" /></td>
          </tr>
	  <?php
           $ic = '1';  $i = '0';
           $requete_1 ="SELECT * FROM ".$prefix_base."lettres_cadres ORDER BY nom_lettre_cadre ASC";
           $execution_1 = mysqli_query($GLOBALS["mysqli"], $requete_1) or die('Erreur SQL !'.$requete_1.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
           while ( $data_1 = mysqli_fetch_array($execution_1))
               { if ($ic === '1') { $ic = '2'; $couleur_cellule = 'td_tableau_absence_1'; } else { $couleur_cellule = 'td_tableau_absence_2'; $ic = '1'; }
                ?>
                  <tr class="<?php echo $couleur_cellule; ?>">
	            <td style="text-align: center;"><a name="crea_lettre<?php echo $data_1['id_lettre_cadre']; ?>"></a><a href="impression_absences.php?type_impr=crea_lettre&amp;lettre_type=<?php echo $lettre_type; ?>&amp;id=<?php echo $data_1['id_lettre_cadre']; ?>&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>&amp;action_cadre=aff_modifier_cadre&amp;sous_rubrique=gb#crea_lettre<?php echo $data_1['id_lettre_cadre']; ?>"><img src="../../images/edit16.png" title="modifier le cadre" border="0" alt="" /></a></td>
	            <td style="text-align: center;"><a href="impression_absences.php?type_impr=crea_lettre&amp;lettre_type=<?php echo $lettre_type; ?>&amp;id=<?php echo $data_1['id_lettre_cadre']; ?>&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>&amp;action_cadre=supprimer_cadre&amp;sous_rubrique=gb#crea_lettre" onClick="return confirm('Etes-vous sur de vouloire le supprimer...')"><img src="../../images/delete16.png" title="supprimer le cadre" border="0" alt="" /></a></td>
                    <td class="norme_absence"><?php echo $data_1['nom_lettre_cadre']; if($action_cadre === 'aff_modifier_cadre' and $id === $data_1['id_lettre_cadre']) { ?>

			<input name="nom_lettre_cadre" value="<?php echo $data_1['nom_lettre_cadre']; ?>" style="border: 1px solid #B3BFB8;" /><br />
			Pos. X <input maxlength="6" size="4" name="x_lettre_cadre" value="<?php echo $data_1['x_lettre_cadre']; ?>" style="border: 1px solid #B3BFB8;" />
			Pos. Y <input maxlength="6" size="4" name="y_lettre_cadre" value="<?php echo $data_1['y_lettre_cadre']; ?>" style="border: 1px solid #B3BFB8;" />
			Largeur du cadre <input maxlength="6" size="4" name="l_lettre_cadre" value="<?php echo $data_1['l_lettre_cadre']; ?>" style="border: 1px solid #B3BFB8;" />
			Hauteur d'une ligne <input maxlength="6" size="4" name="h_lettre_cadre" value="<?php echo $data_1['h_lettre_cadre']; ?>" style="border: 1px solid #B3BFB8;" /><br />
			Encadrement <select name="encadre_lettre_cadre" style="border: 1px solid #B3BFB8;"><option value="0" <?php if($data_1['encadre_lettre_cadre'] === '0') { ?>selected="selected"<?php } ?>>non</option><option value="1" <?php if($data_1['encadre_lettre_cadre'] === '1') { ?>selected="selected"<?php } ?>>oui</option></select>
			Couleur de fond (
			<?php $couleur_rvb = explode('|',$data_1['couleurdefond_lettre_cadre']); if(trim($couleur_rvb[0]) === '0' or trim($couleur_rvb[0]) === '') { $couleur_rvb['1'] = ''; $couleur_rvb['2'] = ''; } ?>
			R: <input maxlength="3" size="3" name="r_couleurdefond_lettre_cadre" value="<?php echo $couleur_rvb[0]; ?>" style="border: 1px solid #B3BFB8;" />
			V: <input maxlength="3" size="3" name="v_couleurdefond_lettre_cadre" value="<?php echo $couleur_rvb[1]; ?>" style="border: 1px solid #B3BFB8;" />
			B: <input maxlength="3" size="3" name="b_couleurdefond_lettre_cadre" value="<?php echo $couleur_rvb[2]; ?>" style="border: 1px solid #B3BFB8;" /> )<br />
			<label for="texte_lettre_cadre"><strong>Texte du cadre</strong></label><br />

	<div style="text-align: center; float: right; margin-right: 20px; border: 1px solid #000000; background-color: #C2CFC5; height: 167px; width: 210px; background-image: url('../images/1.png'); background-repeat: repeat-x;">
	    <strong style="color: #FFFFFF;">Champ disponible</strong><br />
	    <select name="cadre_selection" id="cadre_selection" size="8" style="width: 200px; border: 0px solid #000000; filter:alpha(opacity=75); -moz-opacity:0.75; -khtml-opacity: 0.75; opacity: 0.75;" onchange="texte_lettre_cadre.value += cadre_selection.options[cadre_selection.selectedIndex].value + '\n'">
		<optgroup label="Style">
		   <option value="[g][/g]">Gras</option><?php echo "\n"; ?>
		   <option value="[i][/i]">Italique</option><?php echo "\n"; ?>
		   <option value="[u][/u]">Souligné</option><?php echo "\n"; ?>
		   <option value="[t3][/t3]">Titre</option><?php echo "\n"; ?>
		</optgroup>
		<optgroup label="Date">
		   <option value="[date_court]">date court (00/00/0000)</option><?php echo "\n"; ?>
		   <option value="[date_long]">date long (Vendredi 00 0000)</option><?php echo "\n"; ?>
		</optgroup>
		<optgroup label="Courrier">
		   <option value="[courrier_demande_par]">courrier demandé par</option><?php echo "\n"; ?>
		   <option value="[remarque_eleve]">raison du courrier</option><?php echo "\n"; ?>
		   <option value="[courrier_signe_par_fonction]">courrier signé par fonct.</option><?php echo "\n"; ?>
		   <option value="[courrier_signe_par]">courrier signé par</option><?php echo "\n"; ?>
		</optgroup>
		<optgroup label="Elève">
		   <option value="[nom_eleve]">Nom</option><?php echo "\n"; ?>
		   <option value="[prenom_eleve]">Prénom</option><?php echo "\n"; ?>
		   <option value="[classe_eleve]">Classe de</option><?php echo "\n"; ?>
		   <option value="[sujet_eleve]">Sujet de</option><?php echo "\n"; ?>
		   <option value="[remarque_eleve]">Remarque de</option><?php echo "\n"; ?>
		   <option value="[liste]">Liste abs. et ret.</option><?php echo "\n"; ?>
		   <option value="[liste_abs]">Liste des absences</option><?php echo "\n"; ?>
		   <option value="[liste_ret]">Liste des retards</option><?php echo "\n"; ?>
		</optgroup>
		<optgroup label="Responsable">
		   <option value="[civilite_court_responsable]">Civilité court</option><?php echo "\n"; ?>
		   <option value="[civilite_long_responsable]">Civilité long</option><?php echo "\n"; ?>
		   <option value="[nom_responsable]">Nom</option><?php echo "\n"; ?>
		   <option value="[prenom_responsable]">Prénom</option><?php echo "\n"; ?>
		   <option value="[adresse_responsable]">Adresse</option><?php echo "\n"; ?>
		   <option value="[adressecomp_responsable]">Adresse ligne 2</option><?php echo "\n"; ?>
		   <option value="[adressecomp2_responsable]">Adresse ligne 3</option><?php echo "\n"; ?>
		   <option value="[adressecomp3_responsable]">Adresse ligne 4</option><?php echo "\n"; ?>
		   <option value="[cp_responsable]">Code postal</option><?php echo "\n"; ?>
		   <option value="[ville_responsable]">Ville</option><?php echo "\n"; ?>
		</optgroup>
		<optgroup label="CPE en charge de l'élève">
		   <option value="[civilite_court_cpe]">Civilité court</option><?php echo "\n"; ?>
		   <option value="[civilite_long_cpe]">Civilité long</option><?php echo "\n"; ?>
		   <option value="[nom_cpe]">Nom</option><?php echo "\n"; ?>
		   <option value="[prenom_cpe]">Prénom</option><?php echo "\n"; ?>
		</optgroup>
 	    </select><br />
		  <input type="hidden" name="sous_rubrique" value="<?php echo $sous_rubrique; ?>" />
		  <input type="hidden" name="action_lettre" value="ajout_cadre" />
		  <input type="hidden" name="lettre_type" value="<?php echo $lettre_type; ?>" />
		  <input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
		 <?php /* <input type="submit" name="Submit6" value="<< Ajouter la sélection" /> */ ?>
	</div>

		<textarea name="texte_lettre_cadre" id="texte_lettre_cadre" cols="42" rows="10"><?php
		// modifie les < en [ et inversement
		$texte_lettre_cadre = my_ereg_replace('<','[',$data_1['texte_lettre_cadre']);
		$texte_lettre_cadre = my_ereg_replace('>',']',$texte_lettre_cadre);
		echo $texte_lettre_cadre; ?></textarea>

			<input type="hidden" name="type_impr" value="crea_lettre" />
			<input type="hidden" name="lettre_type" value="<?php echo $lettre_type; ?>" />
			<input type="hidden" name="id" value="<?php echo $data_1['id_lettre_cadre']; ?>" />
			<input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
			<input type="hidden" name="action_cadre" value="modifier_cadre" />
			<input type="submit" value="valider" title="Valider les informations" alt="Valider les informations" />
		    <?php } ?></td>
                  </tr>
         <?php $i = $i + 1; } ?>
	</table>
	</form>
<?php } ?>

   <?php } ?>


<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>




<?php
// système de test
	/* $test = repartire_jour('baba', 'R', '2007-01-01', '2007-05-01');
	//if ( in_array('login', $test) ) { echo 'aaaa'; } else { echo 'bbbb'; }
	if ( !empty($test['2005-02-22']) ) { echo 'rrrr'; }
	echo '<pre>';
	print_r($test);
	echo '</pre>';
*/
//debug_var();

require("../../lib/footer.inc.php");
?>
<?php ((is_null($___mysqli_res = mysqli_close($GLOBALS["mysqli"]))) ? false : $___mysqli_res); ?>
