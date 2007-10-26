<?php
/*
 *
 * $Id$
 *
 * Last modification  : 19/03/2007
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

header('Content-Type: application/pdf');

// Global configuration file
// Quand on est en SSL, IE n'arrive pas à ouvrir le PDF.
//Le problème peut être résolu en ajoutant la ligne suivante :
Header('Pragma: public');

// Lorsque qu'on utilise une session PHP, parfois, IE n'affiche pas le PDF
// C'est un problème qui affecte certaines versions d'IE.
// Pour le contourner, on ajoutez la ligne suivante avant session_start() :
session_cache_limiter('private');

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
die();
}

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
die();
}

// fonction de redimensionnement d'image
function redimensionne_logo($photo, $L_max, $H_max)
 {
	// prendre les informations sur l'image
	$info_image = getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur = $info_image[0];
	$hauteur = $info_image[1];
	// largeur et/ou hauteur maximum à afficher en pixel
	 $taille_max_largeur = $L_max;
	 $taille_max_hauteur = $H_max;

	// calcule le ratio de redimensionnement
	 $ratio_l = $largeur / $taille_max_largeur;
	 $ratio_h = $hauteur / $taille_max_hauteur;
	 $ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

	// définit largeur et hauteur pour la nouvelle image
	 $nouvelle_largeur = $largeur / $ratio;
	 $nouvelle_hauteur = $hauteur / $ratio;

	// des Pixels vers Millimetres
	 $nouvelle_largeur = $nouvelle_largeur / 2.8346;
	 $nouvelle_hauteur = $nouvelle_hauteur / 2.8346;

	return array($nouvelle_largeur, $nouvelle_hauteur);
 }

//permet de transformer les caractère html
 function unhtmlentities($chaineHtml) {
         $tmp = get_html_translation_table(HTML_ENTITIES);
         $tmp = array_flip ($tmp);
         $chaineTmp = strtr ($chaineHtml, $tmp);

         return $chaineTmp;
 }

  $date_ce_jour = date('d/m/Y');
    if (empty($_GET['lettre_type']) and empty($_POST['lettre_type'])) { $lettre_type = ''; }
     else { if (isset($_GET['lettre_type'])) { $lettre_type=$_GET['lettre_type']; } if (isset($_POST['lettre_type'])) { $lettre_type=$_POST['lettre_type']; } }
    if (empty($_GET['lettre_action']) and empty($_POST['lettre_action'])) { $lettre_action = ''; }
     else { if (isset($_GET['lettre_action'])) { $lettre_action=$_GET['lettre_action']; } if (isset($_POST['lettre_action'])) { $lettre_action=$_POST['lettre_action']; } }
    if (empty($_GET['id_lettre_suivi']) and empty($_POST['id_lettre_suivi'])) { $id_lettre_suivi = ''; }
     else { if (isset($_GET['id_lettre_suivi'])) { $id_lettre_suivi=$_GET['id_lettre_suivi']; } if (isset($_POST['id_lettre_suivi'])) { $id_lettre_suivi=$_POST['id_lettre_suivi']; } }

// si session on cherche
  if(!empty($_SESSION['id_lettre_suivi'][0]) and $_SESSION['id_lettre_suivi'] != "")
   { $id_lettre_suivi = $_SESSION['id_lettre_suivi']; } else { unset($_SESSION["id_lettre_suivi"]); }
  if(!empty($_SESSION['lettre_action'][0]) and $_SESSION['lettre_action'] != "")
   { $lettre_action = $_SESSION['lettre_action']; }



if ( $lettre_action === 'test' ) {
//importation des informations de présentation de la lettre type
	   $i_cadre = '0';
           $requete_structure ="SELECT * FROM ".$prefix_base."lettres_types, ".$prefix_base."lettres_cadres, ".$prefix_base."lettres_tcs WHERE id_lettre_type = '".$lettre_type."' AND id_lettre_type = type_lettre_tc AND id_lettre_cadre = cadre_lettre_tc ORDER BY y_lettre_tc ASC, x_lettre_tc ASC";
           $execution_structure = mysql_query($requete_structure) or die('Erreur SQL !'.$requete_structure.'<br />'.mysql_error());
           while ( $donne_structure = mysql_fetch_array($execution_structure))
	    {
		$x_cadre[$i_cadre] = $donne_structure['x_lettre_tc'];
		$y_cadre[$i_cadre] = $donne_structure['y_lettre_tc'];
		$l_cadre[$i_cadre] = $donne_structure['l_lettre_tc'];
		$h_cadre[$i_cadre] = $donne_structure['h_lettre_tc'];
		$encadre_cadre[$i_cadre] = $donne_structure['encadre_lettre_tc'];
		$text_cadre[$i_cadre] = $donne_structure['texte_lettre_cadre'];
		$i_cadre = $i_cadre + 1;
	    }
}

if ( $lettre_action === 'originaux' ) {
// on sélectionne les informations

	//construction de la requete
	$i = '0'; $requete_command = '';

	while(!empty($id_lettre_suivi[$i]))
	 {
		if ( $i === '0' ) { $requete_command = 'id_lettre_suivi = '.$id_lettre_suivi[$i]; }
		if ( $i != '0' ) { $requete_command = $requete_command.' OR id_lettre_suivi = '.$id_lettre_suivi[$i]; }
		$i = $i + 1;
	 }

	$i = '0';
        $requete_persone ="SELECT * FROM ".$prefix_base."lettres_suivis, ".$prefix_base."eleves e WHERE ( (".$requete_command.") AND login = quirecois_lettre_suivi )";
        $execution_persone = mysql_query($requete_persone) or die('Erreur SQL !'.$requete_persone.'<br />'.mysql_error());
        while ( $donne_persone = mysql_fetch_array($execution_persone))
	 {
		$id_lettre_suivi = $donne_persone['id_lettre_suivi'];
		// information sur l'élève
		$id_eleve[$i] = $donne_persone['login']; // id de l'élève
		$ele_id_eleve[$i] = $donne_persone['ele_id'];
		$classe_eleve[$i] = classe_de($id_eleve[$i]);
		$sexe_eleve[$i] = $donne_persone['sexe']; // M ou F
		$nom_eleve[$i] = $donne_persone['nom']; // nom de l'élève
		$prenom_eleve[$i] = $donne_persone['prenom']; // prénom de l'élève
		$naissance_eleve[$i] = $donne_persone['naissance']; // date de naissance de l'élève au format SQL AAAA-MM-JJ
		// information sur les parents
		$nombre_de_responsable = 0;
		$nombre_de_responsable =  mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id )"),0);

		if($nombre_de_responsable != 0)
		{
			$cpt_parents = 0;
			$requete_parents = mysql_query("SELECT * FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id ) ORDER BY resp_legal ASC");
			while ($donner_parents = mysql_fetch_array($requete_parents))
			{
				$civilitee_responsable[$cpt_parents][$i] = $donner_parents['civilite']; // civilité du responsable
			        $nom_responsable[$cpt_parents][$i] = $donner_parents['nom']; // nom du responsable
				$prenom_responsable[$cpt_parents][$i] = $donner_parents['prenom']; // prénom du responsable
				$adresse_responsable[$cpt_parents][$i] = $donner_parents['adr1']; // adresse du responsable
				$adressecomp_responsable[$cpt_parents][$i] = $donner_parents['adr2']; // adresse du responsable suite
				$commune_responsable[$cpt_parents][$i] = $donner_parents['commune']; // ville du responsable
				$cp_responsable[$cpt_parents][$i] = $donner_parents['cp']; // code postal du responsable
				$cpt_parents = $cpt_parents + 1;
			}
		} else {
				$civilitee_responsable[0][$i] = ''; // civilité du responsable
			        $nom_responsable[0][$i] = ''; // nom du responsable
				$prenom_responsable[0][$i] = ''; // prénom du responsable
				$adresse_responsable[0][$i] = ''; // adresse du responsable
				$adressecomp_responsable[0][$i] = ''; // adresse du responsable suite
				$commune_responsable[0][$i] = ''; // ville du responsable
				$cp_responsable[0][$i] = ''; // code postal du responsable
			}


		// information sur la personne qui expédie la lettre
		$signature_status[$i] = $donne_persone['quienvoi_lettre_suivi'];
		$t1 = ''; $t1 = $signature_status[$i];
			if(!isset($signature_qui_status[$t1])) { $signature_status[$i] = strtoupper(qui_fonction($donne_persone['quienvoi_lettre_suivi'])); }
		$signature[$i] = $donne_persone['quienvoi_lettre_suivi'];
		$t2 = ''; $t2 = $signature[$i];
			if(!isset($signature_qui[$t2])) { $signature[$i] = qui($donne_persone['quienvoi_lettre_suivi']); }

		// information sur le/la cpe qui suit l'élève
		$cpe_de_l_eleve[$i] = cpe_eleve($id_eleve[$i]);		

		//information complémentaire pour la lettre
			$remarque[$i] = ''; $date_debut[$i] = ''; $heure_debut[$i] = ''; $date_fin[$i] = ''; $heure_fin[$i] = '';
			$ouestce = $donne_persone['partde_lettre_suivi'];
			$idouestce =  $donne_persone['partdenum_lettre_suivi'];
			//if suivi_eleve_cpe on connait les remarques
			if ( $ouestce === 'suivi_eleve_cpe') {
			        $requete_plusdinfo ="SELECT * FROM ".$prefix_base."suivi_eleve_cpe WHERE id_suivi_eleve_cpe = '".$idouestce."'";
			        $execution_plusdinfo = mysql_query($requete_plusdinfo) or die('Erreur SQL !'.$requete_plusdinfo.'<br />'.mysql_error());
			        while ( $donne_plusdinfo = mysql_fetch_array($execution_plusdinfo))
				 {
					$remarque[$i] = $donne_plusdinfo['komenti_suivi_eleve_cpe'];
				 }
			}
			//if absences_eleves on connait heure de début de fin et date début et fin
			$liste_abs[$i] = '';
			$remarque[$i] = '';
			$date_debut[$i] = '';
			$heure_debut[$i] = '';
			$date_fin[$i] = '';
			$heure_fin[$i] = '';

			if ( $ouestce === 'absences_eleves') {

				$icom = '1'; $requete_command = '';
				$idouestce_explode = explode(',',$idouestce);
				while(!empty($idouestce_explode[$icom]))
				 {
				    if($idouestce_explode[$icom]!='') {
					if ( $icom === '1' ) { $requete_command = '(id_absence_eleve = '.$idouestce_explode[$icom]; }
					if ( $icom != '1' ) { $requete_command = $requete_command.' OR id_absence_eleve = '.$idouestce_explode[$icom]; }
				    }
 				 $icom = $icom + 1;
				 }
			        $requete_plusdinfo ="SELECT * FROM ".$prefix_base."absences_eleves WHERE ".$requete_command.")";
			        $execution_plusdinfo = mysql_query($requete_plusdinfo) or die('Erreur SQL !'.$requete_plusdinfo.'<br />'.mysql_error());
				$o = 0;
			        while ( $donne_plusdinfo = mysql_fetch_array($execution_plusdinfo))
				 {
					$remarque[$i] = $donne_plusdinfo['info_justify_absence_eleve'];
					$date_debut[$i] = date_frl($donne_plusdinfo['d_date_absence_eleve']);
					$heure_debut[$i] = heure($donne_plusdinfo['d_heure_absence_eleve']);
					$date_fin[$i] = date_frl($donne_plusdinfo['a_date_absence_eleve']);
					$heure_fin[$i] = heure($donne_plusdinfo['a_heure_absence_eleve']);
					if( $date_debut[$i] === $date_fin[$i] ) { $liste_abs[$i] = $liste_abs[$i]."<hh size='60' > </hh>- le <b>".$date_debut[$i]."</b> à partir de <b>".$heure_debut[$i]."</b> jusqu'à <b>".$heure_fin[$i]."</b>"; $liste_abs[$i] = $liste_abs[$i].'
'; }
					if( $date_debut[$i] != $date_fin[$i] ) { $liste_abs[$i] = $liste_abs[$i]."<hh size='60' > </hh>- du <b>".$date_debut[$i]."</b> à partir de <b>".$heure_debut[$i]."</b> au <b>".$date_fin[$i]."</b> jusqu'à <b>".$heure_fin[$i]."</b>"; $liste_abs[$i] = $liste_abs[$i].'
'; }
				 $o = $o + 1;
				 }
			}


			if ( $ouestce === 'suivi_eleve_cpe') {

				$icom = '1'; $requete_command = '';
			        $requete_plusdinfo ="SELECT * FROM ".$prefix_base."suivi_eleve_cpe WHERE id_suivi_eleve_cpe = '".$donne_persone['partdenum_lettre_suivi']."'";
			        $execution_plusdinfo = mysql_query($requete_plusdinfo) or die('Erreur SQL !'.$requete_plusdinfo.'<br />'.mysql_error());
				$o = 0;
			        while ( $donne_plusdinfo = mysql_fetch_array($execution_plusdinfo))
				 {
					$remarque[$i] = '   '.$donne_plusdinfo['komenti_suivi_eleve_cpe'];
					$date_debut[$i] = date_frl($donne_plusdinfo['date_suivi_eleve_cpe']);
					$heure_debut[$i] = heure($donne_plusdinfo['heure_suivi_eleve_cpe']);
				 $o = $o + 1;
				 }
			}

		// information sur la structure de la lettre choisi
		$lettre_type_selectionne[$i] = $donne_persone['type_lettre_suivi'];
		$type_lettre = $lettre_type_selectionne[$i];

	   $i_cadre = '0';
           $requete_structure ="SELECT * FROM ".$prefix_base."lettres_types, ".$prefix_base."lettres_cadres, ".$prefix_base."lettres_tcs WHERE id_lettre_type = '".$type_lettre."' AND id_lettre_type = type_lettre_tc AND id_lettre_cadre = cadre_lettre_tc ORDER BY y_lettre_tc ASC, x_lettre_tc ASC";
           $execution_structure = mysql_query($requete_structure) or die('Erreur SQL !'.$requete_structure.'<br />'.mysql_error());
           while ( $donne_structure = mysql_fetch_array($execution_structure))
	    {
		$x_cadre[$type_lettre][$i_cadre] = $donne_structure['x_lettre_tc'];
		$y_cadre[$type_lettre][$i_cadre] = $donne_structure['y_lettre_tc'];
		$l_cadre[$type_lettre][$i_cadre] = $donne_structure['l_lettre_tc'];
		$h_cadre[$type_lettre][$i_cadre] = $donne_structure['h_lettre_tc'];
		$encadre_cadre[$type_lettre][$i_cadre] = $donne_structure['encadre_lettre_tc'];
		$type_lettre = $lettre_type_selectionne[$i];$text_cadre[$type_lettre][$i_cadre] = $donne_structure['texte_lettre_cadre'];
		$i_cadre = $i_cadre + 1;
	    }

		// mise à jour du suivi des lettres
        	$date_envoi = date('Y-m-d');
	        $heure_envoi = date('H:i:s');
                $requete = "UPDATE ".$prefix_base."lettres_suivis SET quienvoi_lettre_suivi = '".$_SESSION['login']."', envoye_date_lettre_suivi = '".$date_envoi."', envoye_heure_lettre_suivi = '".$heure_envoi."' WHERE id_lettre_suivi = '".$id_lettre_suivi."'";
	        mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());

		$i = $i + 1;
	 }
}


define('PARAGRAPH_STRING', '~~~');
define('FPDF_FONTPATH','../../fpdf/font/');
require_once("../../fpdf/class.multicelltag.php");


// mode paysage, a4, etc.
$pdf=new FPDF_MULTICELLTAG('P','mm','A4');
$pdf->SetMargins(10,10,10,10);
$pdf->Open();
$pdf->SetAutoPageBreak(true);

$i = '0';
while(!empty($id_eleve[$i])) {

$pdf->AddPage();

$pdf->SetFont('arial','',11);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);

	// gestion des styles
	$pdf->SetStyle("p","times","",11,"0,0,0");
	$pdf->SetStyle("g","times","B",11,"0,0,0");
	$pdf->SetStyle("b","times","B",11,"0,0,0");
	$pdf->SetStyle("i","times","I",11,"0,0,0");
	$pdf->SetStyle("u","times","U",11,"0,0,0");
	$pdf->SetStyle("decal","times","",11,"0,0,120");
	$pdf->SetStyle("pb","times","B",11,"0,0,0");
	$pdf->SetStyle("t1","arial","",11,"254,252,222");
	$pdf->SetStyle("t1","arial","",11,"0,151,200");
	$pdf->SetStyle("t2","arial","",11,"0,151,200");
	$pdf->SetStyle("t3","times","B",14,"203,0,48");
	$pdf->SetStyle("t4","arial","BI",11,"0,151,200");
	$pdf->SetStyle("hh","times","B",11,"255,189,12");
	$pdf->SetStyle("ss","arial","",7,"203,0,48");
	$pdf->SetStyle("font","helvetica","",10,"0,0,255");
	$pdf->SetStyle("style","helvetica","BI",10,"0,0,220");
	$pdf->SetStyle("size","times","BI",13,"0,0,120");
	$pdf->SetStyle("color","times","BI",13,"0,255,255");

$cpt_i_cadre = '0';

$type_lettre = $lettre_type_selectionne[$i];

// BOLC IDENTITE DE L'ETABLISSEMENT
		$X_entete_etab='5';
		$Y_entete_etab='5';
		$affiche_logo_etab = '1';
		$L_max_logo='75'; // Longeur maxi du logo
		$H_max_logo='75'; // hauteur maxi du logo
		$logo = '../../images/'.getSettingValue('logo_etab');
		$format_du_logo = str_replace('.','',strstr(getSettingValue('logo_etab'), '.')); 
		if($affiche_logo_etab==='1' and file_exists($logo) and getSettingValue('logo_etab') != '' and ($format_du_logo==='jpg' or $format_du_logo==='png'))
		{
		 $valeur=redimensionne_logo($logo, $L_max_logo, $H_max_logo);

		 //$X_logo et $Y_logo; placement du bloc identite de l'établissement
		 $X_logo=$X_entete_etab; $Y_logo=$Y_entete_etab; $L_logo=$valeur[0]; $H_logo=$valeur[1];
		 $X_etab=$X_logo+$L_logo; $Y_etab=$Y_logo;
		 //logo
	         $pdf->Image($logo, $X_logo, $Y_logo, $L_logo, $H_logo);
		} else { 
			  $X_etab = $X_entete_etab; $Y_etab = $Y_entete_etab;
		       }

// BLOC ADRESSE ETABLISSEMENT
		$caractere_utilse='arial';
		$affiche_logo_etab='1'; // affiché le logo de l'établissement
		$entente_mel='0'; // afficher dans l'entête le mel de l'établissement
		$entente_tel='0'; // afficher dans l'entête le téléphone de l'établissement
		$entente_fax='0'; // afficher dans l'entête le fax de l'établissement
	 	 $pdf->SetXY($X_etab,$Y_etab);
	 	 $pdf->SetFont($caractere_utilse,'',14);
		  $gepiSchoolName = getSettingValue('gepiSchoolName');
		 $pdf->Cell(90,7, $gepiSchoolName,0,2,''); 
		 $pdf->SetFont($caractere_utilse,'',10);
	   	  $gepiSchoolAdress1 = getSettingValue('gepiSchoolAdress1');
		 $pdf->Cell(90,5, $gepiSchoolAdress1,0,2,'');
		  $gepiSchoolAdress2 = getSettingValue('gepiSchoolAdress2');
		 $pdf->Cell(90,5, $gepiSchoolAdress2,0,2,''); 
		  $gepiSchoolZipCode = getSettingValue('gepiSchoolZipCode');
		  $gepiSchoolCity = getSettingValue('gepiSchoolCity');
		 $pdf->Cell(90,5, $gepiSchoolZipCode." ".$gepiSchoolCity,0,2,''); 
		  $gepiSchoolTel = getSettingValue('gepiSchoolTel');
		  $gepiSchoolFax = getSettingValue('gepiSchoolFax');
		if($entente_tel==='1' and $entente_fax==='1') { $entete_communic = 'Tél: '.$gepiSchoolTel.' / Fax: '.$gepiSchoolFax; }
		if($entente_tel==='1' and empty($entete_communic)) { $entete_communic = 'Tél: '.$gepiSchoolTel; }
		if($entente_fax==='1' and empty($entete_communic)) { $entete_communic = 'Fax: '.$gepiSchoolFax; }
		if(isset($entete_communic) and $entete_communic!='') {
		 $pdf->Cell(90,5, $entete_communic,0,2,''); 
		}
		if($entente_mel==='1') {
		  $gepiSchoolEmail = getSettingValue('gepiSchoolEmail');
		 $pdf->Cell(90,5, $gepiSchoolEmail,0,2,''); 
		}

while($cpt_i_cadre<$i_cadre)
 {
	$pdf->SetXY($x_cadre[$type_lettre][$cpt_i_cadre],$y_cadre[$type_lettre][$cpt_i_cadre]);
	$text = '<p>'.$text_cadre[$type_lettre][$cpt_i_cadre].'</p>';



	$variable = array("<sexe>", "<nom_eleve>", "<prenom_eleve>", "<date_naissance>", "<classe_eleve>", "<civilitee_court_responsable>", "<civilitee_long_responsable>", "<nom_responsable>", "<prenom_responsable>", "<adresse_responsable>", "<cp_responsable>", "<commune_responsable>", "<remarque_eleve>", "<date_debut>", "<heure_debut>", "<date_fin>", "<heure_fin>", "<liste>", "<courrier_signe_par_fonction>", "<courrier_signe_par>", "<civilitee_court_cpe>", "<civilitee_long_cpe>", "<nom_cpe>", "<prenom_cpe>");
		$civilitee_long_responsable = 'Madame, Monsieur';
	if( !isset($adresse_responsable[1][$i]) or $adresse_responsable[0][$i] != $adresse_responsable[1][$i]) {
		if($civilitee_responsable[0][$i] === 'M.') { $civilitee_long_responsable = 'Monsieur'; }
		if($civilitee_responsable[0][$i] === 'Mme') { $civilitee_long_responsable = 'Madame'; }
	}

		$civilite_long_cpe = '';
		if($cpe_de_l_eleve[$i]['civilite'] === 'M.') { $civilite_long_cpe = 'Monsieur'; }
		if($cpe_de_l_eleve[$i]['civilite'] === 'Mme') { $civilite_long_cpe = 'Madame'; }


	$remplacer_par = array($sexe_eleve[$i], strtoupper($nom_eleve[$i]), ucfirst($prenom_eleve[$i]), $naissance_eleve[$i], $classe_eleve[$i], $civilitee_responsable[0][$i], $civilitee_long_responsable, $nom_responsable[0][$i], $prenom_responsable[0][$i], $adresse_responsable[0][$i], $cp_responsable[0][$i], $commune_responsable[0][$i], $remarque[$i], $date_debut[$i], $heure_debut[$i], $date_fin[$i], $heure_fin[$i], $liste_abs[$i], $signature_status[$i], $signature[$i], $cpe_de_l_eleve[$i]['civilite'], $civilite_long_cpe, $cpe_de_l_eleve[$i]['nom'], $cpe_de_l_eleve[$i]['prenom']);
	$text = str_replace($variable, $remplacer_par, $text);

	$pdf->MultiCellTag($l_cadre[$type_lettre][$cpt_i_cadre], $h_cadre[$type_lettre][$cpt_i_cadre], $text, $encadre_cadre[$type_lettre][$cpt_i_cadre], "J", '');
	$cpt_i_cadre = $cpt_i_cadre + 1;
 }

$i = $i + 1;
}

$pdf->Output('Lettre_'.date("Ymd_Hi").'.pdf','I');
?>
