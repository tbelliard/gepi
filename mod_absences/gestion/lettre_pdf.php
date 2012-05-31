<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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
$resultat_session = $session_gepi->security_check();
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
  $date_ce_jour_sql = date('Y-m-d');
    if (empty($_GET['lettre_type']) and empty($_POST['lettre_type'])) { $lettre_type = ''; }
     else { if (isset($_GET['lettre_type'])) { $lettre_type=$_GET['lettre_type']; } if (isset($_POST['lettre_type'])) { $lettre_type=$_POST['lettre_type']; } }
    if (empty($_GET['lettre_action']) and empty($_POST['lettre_action'])) { $lettre_action = ''; }
     else { if (isset($_GET['lettre_action'])) { $lettre_action=$_GET['lettre_action']; } if (isset($_POST['lettre_action'])) { $lettre_action=$_POST['lettre_action']; } }
    if (empty($_GET['id_lettre_suivi']) and empty($_POST['id_lettre_suivi'])) { $id_lettre_suivi = ''; }
     else { if (isset($_GET['id_lettre_suivi'])) { $id_lettre_suivi=$_GET['id_lettre_suivi']; } if (isset($_POST['id_lettre_suivi'])) { $id_lettre_suivi=$_POST['id_lettre_suivi']; } }
    if (empty($_GET['mode']) and empty($_POST['mode'])) { $mode = ''; }
     else { if (isset($_GET['mode'])) { $mode=$_GET['mode']; } if (isset($_POST['mode'])) { $mode=$_POST['mode']; } }

/*
// si session on cherche
  if(!empty($_SESSION['id_lettre_suivi'][0]) and $_SESSION['id_lettre_suivi'] != "")
   { $id_lettre_suivi = $_SESSION['id_lettre_suivi']; } else { unset($_SESSION["id_lettre_suivi"]); }
  if(!empty($_SESSION['lettre_action'][0]) and $_SESSION['lettre_action'] != "")
   { $lettre_action = $_SESSION['lettre_action']; }
*/

$id_lettre_suivi = unserialize(stripslashes($id_lettre_suivi));

//if ( $lettre_action === 'test' ) {
if ( $mode === 'apercus' )
{

	// on considère qu'il y a 1 élève de sélectionné
	$id_eleve[0] = 'Test';
	$lettre_type_selectionne[0] = $lettre_type;
	$civilite_responsable[0][0] = 'M.';
	$cpe_de_l_eleve[0] = 'Mme';
	$sexe_eleve[0] = 'Mlle';
	$nom_eleve[0] = 'test';
	$prenom_eleve[0] = 'test';
	$naissance_eleve[0] = '12/12/2000';
	$classe_eleve[0] = '6ème 1';
	$nom_responsable[0][0] = 'Tartampion';
	$prenom_responsable[0][0] = 'mozard';
	$adresse_responsable[0][0] = 'rue alber';

	$adressecomp_responsable[0][0] = ''; // adresse du responsable suite
	$adressecomp2_responsable[0][0] = ''; // adresse du responsable suite
	$adressecomp3_responsable[0][0] = ''; // adresse du responsable suite

	$cp_responsable[0][0] = '54385';
	$commune_responsable[0][0] = 'lacommune';
	$remarque[0] = 'aucun';
	$date_debut[0] = '12/12/2000';
	$heure_debut[0] = '12:00';
	$date_fin[0] = '12/12/2000';
	$heure_fin[0] = '12:00';
	$liste_abs[0] = '';
	$signature_status[0] = 'Madame';
	$signature[0] = '';
	$cpe_de_l_eleve[0]['civilite'] = 'Mme';
	$cpe_de_l_eleve[0]['nom'] = 'test';
	$cpe_de_l_eleve[0]['prenom'] = 'test';

	//importation des informations de présentation de la lettre type
	$i_cadre = '0';
        $requete_structure ="SELECT *
                               FROM ".$prefix_base."lettres_types, ".$prefix_base."lettres_cadres, ".$prefix_base."lettres_tcs
                              WHERE id_lettre_type = '".$lettre_type."'
                                AND id_lettre_type = type_lettre_tc
                                AND id_lettre_cadre = cadre_lettre_tc
                           ORDER BY y_lettre_tc ASC, x_lettre_tc ASC";
        //echo "\$requete_structure=$requete_structure<br />";
        $execution_structure = mysql_query($requete_structure) or die('Erreur SQL !'.$requete_structure.'<br />'.mysql_error());

        while ( $donne_structure = mysql_fetch_array($execution_structure))
	    {

			$x_cadre[$lettre_type][$i_cadre] = $donne_structure['x_lettre_tc'];
			$y_cadre[$lettre_type][$i_cadre] = $donne_structure['y_lettre_tc'];
			$l_cadre[$lettre_type][$i_cadre] = $donne_structure['l_lettre_tc'];
			$h_cadre[$lettre_type][$i_cadre] = $donne_structure['h_lettre_tc'];
			$encadre_cadre[$lettre_type][$i_cadre] = $donne_structure['encadre_lettre_tc'];
			$text_cadre[$lettre_type][$i_cadre] = $donne_structure['texte_lettre_cadre'];

			$i_cadre = $i_cadre + 1;

	    }

}

if ( $lettre_action === 'originaux' ) {
// on sélectionne les informations

	//construction de la requete
	$i = '0'; $requete_command = '';

	while(!empty($id_lettre_suivi[$i]))
	{
		if ( $i === '0' ) {
			$requete_command = 'id_lettre_suivi = '.$id_lettre_suivi[$i];
		}elseif ( $i !== '0' ) {
			$requete_command = $requete_command.' OR id_lettre_suivi = '.$id_lettre_suivi[$i];
		}
		$i = $i + 1;
	}

	$i = '0';

	$requete_persone = "SELECT DISTINCT lettres_suivis.*, eleves.* FROM lettres_suivis, eleves, j_eleves_classes
		  									WHERE (".$requete_command.")
											AND eleves.login = quirecois_lettre_suivi
		  									AND eleves.login = j_eleves_classes.login
										ORDER BY j_eleves_classes.id_classe ASC, nom ASC, prenom ASC";
	//echo "\$requete_persone=$requete_persone<br />";

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
		$sql="SELECT count(*) FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id AND r.resp_legal!='0' )";
		//echo "\$sql=$sql<br />";
		$nombre_de_responsable =  mysql_result(mysql_query($sql),0);

		if($nombre_de_responsable != 0)
		{
			$cpt_parents = 0;
			$sql="SELECT * FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id AND r.resp_legal!='0' ) ORDER BY resp_legal ASC";
			//echo "\$sql=$sql<br />";
			$requete_parents = mysql_query($sql);
			while ($donner_parents = mysql_fetch_array($requete_parents))
			{
				$civilite_responsable[$cpt_parents][$i] = $donner_parents['civilite']; // civilité du responsable
			        $nom_responsable[$cpt_parents][$i] = $donner_parents['nom']; // nom du responsable
				$prenom_responsable[$cpt_parents][$i] = $donner_parents['prenom']; // prénom du responsable
				$adresse_responsable[$cpt_parents][$i] = $donner_parents['adr1']; // adresse du responsable
				$adressecomp_responsable[$cpt_parents][$i] = $donner_parents['adr2']; // adresse du responsable suite
				$adressecomp2_responsable[$cpt_parents][$i] = $donner_parents['adr3']; // adresse du responsable suite
				$adressecomp3_responsable[$cpt_parents][$i] = $donner_parents['adr4']; // adresse du responsable suite
				$commune_responsable[$cpt_parents][$i] = $donner_parents['commune']; // ville du responsable
				$cp_responsable[$cpt_parents][$i] = $donner_parents['cp']; // code postal du responsable
				$cpt_parents = $cpt_parents + 1;
			}
		} else {
				$civilite_responsable[0][$i] = ''; // civilité du responsable
				$nom_responsable[0][$i] = ''; // nom du responsable
				$prenom_responsable[0][$i] = ''; // prénom du responsable
				$adresse_responsable[0][$i] = ''; // adresse du responsable
				$adressecomp_responsable[0][$i] = ''; // adresse du responsable suite
				$adressecomp2_responsable[0][$i] = ''; // adresse du responsable suite
				$adressecomp3_responsable[0][$i] = ''; // adresse du responsable suite
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
					//echo "\$requete_plusdinfo=$requete_plusdinfo<br />";
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
					//echo "\$requete_plusdinfo=$requete_plusdinfo<br />";
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
					//echo "\$requete_plusdinfo=$requete_plusdinfo<br />";
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
			//echo "\$requete_structure=$requete_structure<br />";
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
				//echo "\$requete=$requete<br />";
	        mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());

		$i = $i + 1;
	 }
}

function debug_lettre_pdf($text, $mode="a+") {
	$debug="n";
	if($debug=="y") {
		$f=fopen("/tmp/lettre_pdf.txt", $mode);
		fwrite($f,$text);
		fclose($f);
	}
}
debug_lettre_pdf(strftime("%Y%m%d à %H:%M:%S")."\n", "w+");

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

	$pdf->SetFont('DejaVu','',11);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);


	// gestion des styles
	$pdf->SetStyle2("p","times","",11,"0,0,0");
	$pdf->SetStyle2("g","times","B",11,"0,0,0");
	$pdf->SetStyle2("b","times","B",11,"0,0,0");
	$pdf->SetStyle2("i","times","I",11,"0,0,0");
	$pdf->SetStyle2("u","times","U",11,"0,0,0");
	$pdf->SetStyle2("decal","times","",11,"0,0,120");
	$pdf->SetStyle2("pb","times","B",11,"0,0,0");
	$pdf->SetStyle2("t1","DejaVu","",11,"254,252,222");
	$pdf->SetStyle2("t1","DejaVu","",11,"0,151,200");
	$pdf->SetStyle2("t2","DejaVu","",11,"0,151,200");
	$pdf->SetStyle2("t3","times","B",14,"203,0,48");
	$pdf->SetStyle2("t4","DejaVu","BI",11,"0,151,200");
	$pdf->SetStyle2("hh","times","B",11,"255,189,12");
	$pdf->SetStyle2("ss","DejaVu","",7,"203,0,48");
	$pdf->SetStyle2("font","helvetica","",10,"0,0,255");
	$pdf->SetStyle2("style","helvetica","BI",10,"0,0,220");
	$pdf->SetStyle2("size","times","BI",13,"0,0,120");
	$pdf->SetStyle2("color","times","BI",13,"0,255,255");

	$cpt_i_cadre = '0';
	//$cpt_i_cadre = 0;
	//$cpt_i_cadre = 1;

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
	$caractere_utilse='DejaVu';
	$affiche_logo_etab='1'; // affiché le logo de l'établissement
	$entente_mel='1'; // afficher dans l'entête le mel de l'établissement
	$entente_tel='1'; // afficher dans l'entête le téléphone de l'établissement
	$entente_fax='1'; // afficher dans l'entête le fax de l'établissement
	$pdf->SetXY($X_etab,$Y_etab);
	$pdf->SetFont('DejaVu','',14);
	$gepiSchoolName = getSettingValue('gepiSchoolName');
	$pdf->Cell(90,7, ($gepiSchoolName),0,2,'');
	$pdf->SetFont('DejaVu','',10);
	$gepiSchoolAdress1 = getSettingValue('gepiSchoolAdress1');
	$pdf->Cell(90,5, ($gepiSchoolAdress1),0,2,'');
	$gepiSchoolAdress2 = getSettingValue('gepiSchoolAdress2');
	$pdf->Cell(90,5, ($gepiSchoolAdress2),0,2,'');
	$gepiSchoolZipCode = getSettingValue('gepiSchoolZipCode');
	$gepiSchoolCity = getSettingValue('gepiSchoolCity');
	$pdf->Cell(90,5, ($gepiSchoolZipCode." ".$gepiSchoolCity),0,2,'');
	$gepiSchoolTel = getSettingValue('gepiSchoolTel');
	$gepiSchoolFax = getSettingValue('gepiSchoolFax');
	if($entente_tel==='1' and $entente_fax==='1') { $entete_communic = 'Tél: '.$gepiSchoolTel.' / Fax: '.$gepiSchoolFax; }
	if($entente_tel==='1' and empty($entete_communic)) { $entete_communic = 'Tél: '.$gepiSchoolTel; }
	if($entente_fax==='1' and empty($entete_communic)) { $entete_communic = 'Fax: '.$gepiSchoolFax; }
	if(isset($entete_communic) and $entete_communic!='') {
		$pdf->Cell(90,5, ($entete_communic),0,2,'');
	}
	if($entente_mel==='1') {
		$gepiSchoolEmail = getSettingValue('gepiSchoolEmail');
		$pdf->Cell(90,5, $gepiSchoolEmail,0,2,'');
	}

	while($cpt_i_cadre<$i_cadre)
	{
		debug_lettre_pdf("\$cpt_i_cadre=$cpt_i_cadre"."\n");

		$pdf->SetXY($x_cadre[$type_lettre][$cpt_i_cadre],$y_cadre[$type_lettre][$cpt_i_cadre]);

		debug_lettre_pdf("\$x_cadre[$type_lettre][$cpt_i_cadre]=".$x_cadre[$type_lettre][$cpt_i_cadre]."\n");
		debug_lettre_pdf("\$y_cadre[$type_lettre][$cpt_i_cadre]=".$y_cadre[$type_lettre][$cpt_i_cadre]."\n");

		//$text = '<p>'.$text_cadre[$type_lettre][$cpt_i_cadre].'</p>';
		$text = $text_cadre[$type_lettre][$cpt_i_cadre];

		// ajout des autres lignes pour l'adresse des responsables  didier
		$variable = array("<sexe>", "<nom_eleve>", "<prenom_eleve>", "<date_naissance>", "<classe_eleve>", "<civilitee_court_responsable>", "<civilitee_long_responsable>", "<nom_responsable>", "<prenom_responsable>", "<adresse_responsable>","<adressecomp_responsable>","<adressecomp2_responsable>","<adressecomp3_responsable>","<cp_responsable>", "<commune_responsable>", "<remarque_eleve>", "<date_debut>", "<heure_debut>", "<date_fin>", "<heure_fin>", "<liste>", "<courrier_signe_par_fonction>", "<courrier_signe_par>", "<civilitee_court_cpe>", "<civilitee_long_cpe>", "<nom_cpe>", "<prenom_cpe>", "<date_court>", "<date_long>");
		$civilite_long_responsable = 'Madame, Monsieur';
		if( !isset($adresse_responsable[1][$i]) or $adresse_responsable[0][$i] != $adresse_responsable[1][$i]) {
			if($civilite_responsable[0][$i] == 'M.') {
				$civilite_long_responsable = 'Monsieur';
			}elseif($civilite_responsable[0][$i] == 'Mme') {
				$civilite_long_responsable = 'Madame';
			}elseif($civilite_responsable[0][$i] == 'Mlle') {
				$civilite_long_responsable = 'Mademoiselle';
			}else{
				$civilite_responsable[0][$i] = 'M.Mme';
				$civilite_long_responsable = 'Madame, Monsieur';
			}
		}

		$civilite_long_cpe = '';
		if($cpe_de_l_eleve[$i]['civilite'] == 'M.') {
			$civilite_long_cpe = 'Monsieur';
		}elseif($cpe_de_l_eleve[$i]['civilite'] == 'Mme') {
			$civilite_long_cpe = 'Madame';
		}elseif($cpe_de_l_eleve[$i]['civilite'] == 'Mlle') {
			$civilite_long_cpe = 'Mademoiselle';
		}else{
			$civilite_long_cpe = 'M.';
		}

		$civilite_responsable[0][$i] = (isset($civilite_responsable[0][$i]) AND $civilite_responsable[0][$i] != '') ? $civilite_responsable[0][$i] : 'M.Mme';
		$civilite_long_responsable = ($civilite_long_responsable != '') ? $civilite_long_responsable : 'Madame, Monsieur';
		$civilite_long_cpe = (isset($cpe_de_l_eleve[$i]['civilite']) AND $cpe_de_l_eleve[$i]['civilite'] != '') ? $cpe_de_l_eleve[$i]['civilite'] : 'M.';
		// ajout des autres lignes pour l'adresse des responsables  didier
		$remplacer_par = array($sexe_eleve[$i], casse_mot($nom_eleve[$i],'maj'), casse_mot($prenom_eleve[$i],'majf2'), $naissance_eleve[$i], $classe_eleve[$i], $civilite_responsable[0][$i], $civilite_long_responsable, $nom_responsable[0][$i], $prenom_responsable[0][$i], $adresse_responsable[0][$i],$adressecomp_responsable[0][$i],$adressecomp2_responsable[0][$i],$adressecomp3_responsable[0][$i],$cp_responsable[0][$i], $commune_responsable[0][$i], $remarque[$i], $date_debut[$i], $heure_debut[$i], $date_fin[$i], $heure_fin[$i], $liste_abs[$i], $signature_status[$i], $signature[$i], $cpe_de_l_eleve[$i]['civilite'], $civilite_long_cpe, $cpe_de_l_eleve[$i]['nom'], $cpe_de_l_eleve[$i]['prenom'], $date_ce_jour, date_frl($date_ce_jour_sql));
		//print_r($remplacer_par);print_r($variable);

		$text = str_replace($variable, $remplacer_par, $text);

		$text=preg_replace("/\r\n/","\n",$text);

		// Nettoyage des balises HTML non prises en compte dans cell_ajustee()
		$text = preg_replace("|<g>|i","<b>",$text);
		$text = preg_replace("|</g>|i","</b>",$text);

		// On abandonne ext_MultiCellTag()
		//$pdf->ext_MultiCellTag($l_cadre[$type_lettre][$cpt_i_cadre], $h_cadre[$type_lettre][$cpt_i_cadre], ensure_ascii($text), $encadre_cadre[$type_lettre][$cpt_i_cadre], "J", '');

		$taille_max_police=11;
		//$taille_min_police=ceil($taille_max_police/3);
		$taille_min_police=$taille_max_police;
		$largeur_dispo=$l_cadre[$type_lettre][$cpt_i_cadre];

		debug_lettre_pdf("\$l_cadre[$type_lettre][$cpt_i_cadre]=".$l_cadre[$type_lettre][$cpt_i_cadre]."\n");

		// La hauteur est en mm
		if((is_numeric($l_cadre[$type_lettre][$cpt_i_cadre]))&&($l_cadre[$type_lettre][$cpt_i_cadre]>0)) {
			$largeur_dispo=$l_cadre[$type_lettre][$cpt_i_cadre];
		}
		else {
			$largeur_dispo=210-$pdf->GetX()-10;
		}

		debug_lettre_pdf("\$h_cadre[$type_lettre][$cpt_i_cadre]=".$h_cadre[$type_lettre][$cpt_i_cadre]."\n");

		$compte_retours_ligne=strlen(preg_replace("/[^\n]/","",$text));

		// La hauteur est en cm
		if((is_numeric($h_cadre[$type_lettre][$cpt_i_cadre]))&&($h_cadre[$type_lettre][$cpt_i_cadre]>0)) {
			$h_cell=$h_cadre[$type_lettre][$cpt_i_cadre]*10;
		}
		else {
			//$h_cell=50;
			// On essaye de calculer une hauteur fonction du nombre de retours à la ligne
			// en controlant que cela tient
			$h_cell=min($compte_retours_ligne*7, 297-$pdf->getY()-10);
		}

		//$largeur_underscore = $pdf->GetStringWidth("__________")/10;
		//$text=preg_replace("/______________________________/","_____________________________ ",$text);
		//$text=preg_replace("/__/","_ ",$text);

		// Les retours à la ligne ne sont pas pris en compte sans ça:
		$text=nl2br($text);
		//$text.=" ".$compte_retours_ligne;

		//cell_ajustee($text,$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,'LRBT');
		cell_ajustee($text,$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_cell,$taille_max_police,$taille_min_police,$encadre_cadre[$type_lettre][$cpt_i_cadre]);
		//$pdf->Cell($largeur_dispo, $h_cell, $text,'TLRB',0,'L', '');

		$cpt_i_cadre = $cpt_i_cadre + 1;
	 }

	debug_lettre_pdf("==========================================="."\n");

	$i = $i + 1;
}

$pdf->Output('Lettre_'.date("Ymd_Hi").'.pdf','I');
?>
