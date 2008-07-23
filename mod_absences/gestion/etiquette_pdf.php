<?php
/*
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


    if (empty($_GET['etiquette_format']) and empty($_POST['etiquette_format'])) { $etiquette_format = ''; }
     else { if (isset($_GET['etiquette_format'])) { $etiquette_format=$_GET['etiquette_format']; } if (isset($_POST['etiquette_format'])) { $etiquette_format=$_POST['etiquette_format']; } }
    if (empty($_GET['etiquette_type']) and empty($_POST['etiquette_type'])) { $etiquette_type = ''; }
     else { if (isset($_GET['etiquette_type'])) { $etiquette_type=$_GET['etiquette_type']; } if (isset($_POST['etiquette_type'])) { $etiquette_type=$_POST['etiquette_type']; } }
    if (empty($_GET['etiquette_action']) and empty($_POST['etiquette_action'])) { $etiquette_action = ''; }
     else { if (isset($_GET['etiquette_action'])) { $etiquette_action=$_GET['etiquette_action']; } if (isset($_POST['etiquette_action'])) { $etiquette_action=$_POST['etiquette_action']; } }
    if (empty($_GET['classe']) and empty($_POST['classe'])) { $classe = ''; }
     else { if (isset($_GET['classe'])) { $classe=$_GET['classe']; } if (isset($_POST['classe'])) { $classe=$_POST['classe']; } }
    if (empty($_GET['cadre']) and empty($_POST['cadre'])) { $cadre = '0'; }
     else { if (isset($_GET['cadre'])) { $cadre=$_GET['cadre']; } if (isset($_POST['cadre'])) { $cadre=$_POST['cadre']; } }


    if (empty($_GET['trie_par']) and empty($_POST['trie_par'])) { $trie_par = ''; }
     else { if (isset($_GET['trie_par'])) { $trie_par = $_GET['trie_par']; } if (isset($_POST['trie_par'])) { $trie_par = $_POST['trie_par']; } }


// si session on cherche
  if(!empty($_SESSION['id_lettre_suivi'][0]) and $_SESSION['id_lettre_suivi'] != "")
   { $id_lettre_suivi = $_SESSION['id_lettre_suivi']; } else { unset($_SESSION["id_lettre_suivi"]); }
  if(!empty($_SESSION['etiquette_action'][0]) and $_SESSION['etiquette_action'] != "")
   { $etiquette_action = $_SESSION['etiquette_action']; }



if ( $etiquette_action === 'test' ) {
//importation des informations de présentation de la lettre type
           $requete_structure ="SELECT * FROM ".$prefix_base."etiquettes_formats WHERE id_etiquette_format = '".$etiquette_format."'";
           $execution_structure = mysql_query($requete_structure) or die('Erreur SQL !'.$requete_structure.'<br />'.mysql_error());
           while ( $donne_structure = mysql_fetch_array($execution_structure))
	    {
		$xcote = $donne_structure['xcote_etiquette_format'];
		$ycote = $donne_structure['ycote_etiquette_format'];
		$espacementx = $donne_structure['espacementx_etiquette_format'];
		$espacementy = $donne_structure['espacementy_etiquette_format'];
		$largeur = $donne_structure['largeur_etiquette_format'];
		$hauteur = $donne_structure['hauteur_etiquette_format'];
		$nbl = $donne_structure['nbl_etiquette_format'];
		$nbh = $donne_structure['nbh_etiquette_format'];
	    }
}

if ( $etiquette_action === 'originaux' ) {
// on sélectionne les informations
$etiquette_agencement = '3';

	// méthode de trie
	$trie = '';
	if($trie_par === '1') { $trie = 'ORDER BY c.id ASC, e.nom ASC, e.prenom ASC'; }
	if($trie_par === '2') { $trie = 'ORDER BY e.nom ASC, e.prenom ASC'; }

	$i = '0';
	if($classe === 'tous') { $requete_persone ="SELECT * FROM ".$prefix_base."classes c, ".$prefix_base."j_eleves_classes ec, ".$prefix_base."eleves e WHERE ec.id_classe = c.id AND ec.login = e.login GROUP BY e.login ".$trie.""; }
	if($classe != 'tous') { $requete_persone ="SELECT * FROM ".$prefix_base."classes c, ".$prefix_base."j_eleves_classes ec, ".$prefix_base."eleves e WHERE c.id = '".$classe."' AND ec.id_classe = c.id AND ec.login = e.login GROUP BY e.login ".$trie.""; }
        
        $execution_persone = mysql_query($requete_persone) or die('Erreur SQL !'.$requete_persone.'<br />'.mysql_error());
        while ( $donne_persone = mysql_fetch_array($execution_persone))
	 {

		// information sur l'élève
		$id_eleve[$i] = $donne_persone['login']; // id de l'élève
		$eleve_numid[$i] = $donne_persone['ele_id']; // numéro id de l'élève

		$classe_eleve[$i] = classe_de($id_eleve[$i]);
		$sexe_eleve[$i] = $donne_persone['sexe']; // M ou F
		$nom_eleve[$i] = strtoupper($donne_persone['nom']); // nom de l'élève
		$prenom_eleve[$i] = ucfirst($donne_persone['prenom']); // prénom de l'élève
		$naissance_eleve[$i] = $donne_persone['naissance']; // date de naissance de l'élève au format SQL AAAA-MM-JJ
		$code_eleve[$i] = $donne_persone['no_gep']; // date de naissance de l'élève au format SQL AAAA-MM-JJ

		// information sur les responsables
		$nombre_de_responsable = 0;
		$nombre_de_responsable =  mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$eleve_numid[$i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id )"),0);
		if($nombre_de_responsable != 0)
		{
			$cpt_parents = '1';
			$requete_parents = mysql_query("SELECT * FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$eleve_numid[$i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id ) ORDER BY resp_legal ASC");
			while ($donner_parents = mysql_fetch_array($requete_parents))
		  	{
				$civilitee_responsable[$cpt_parents][$i] = $donner_parents['civilite'];
			        $nom_responsable[$cpt_parents][$i] = $donner_parents['nom'];
	        		$prenom_responsable[$cpt_parents][$i] = $donner_parents['prenom'];
			        $adresse_responsable[$cpt_parents][$i] = $donner_parents['adr1'];
			        $adressecomp_responsable[$cpt_parents][$i] = $donner_parents['adr2'];
			        $commune_responsable[$cpt_parents][$i] = $donner_parents['commune'];
			        $cp_responsable[$cpt_parents][$i] = $donner_parents['cp'];
				$cpt_parents = $cpt_parents + 1;
			}
		} else {
				$civilitee_responsable['1'][$i] = '';
			        $nom_responsable['1'][$i] = '';
	        		$prenom_responsable['1'][$i] = '';
			        $adresse_responsable['1'][$i] = '';
			        $adressecomp_responsable['1'][$i] = '';
			        $commune_responsable['1'][$i] = '';
			        $cp_responsable['1'][$i] = '';
			}

		// nom et prénom, classe de l'élève
		if($etiquette_type === '1') { $ligne[$i]['1'] = ' '.$nom_eleve[$i].' '.$prenom_eleve[$i]; $ligne[$i]['2'] = ' Classe: '.$classe_eleve[$i]; $ligne[$i]['3'] = ''; $ligne[$i]['4'] = ''; $ligne[$i]['5'] = ''; $cpt_ligne[$i] = '5'; }
		// nom et prénom de l'élève avec sont code élève
		if($etiquette_type === '2') { $ligne[$i]['1'] = ' '.$nom_eleve[$i].' '.$prenom_eleve[$i]; $ligne[$i]['2'] = ' Classe: '.$classe_eleve[$i]; $ligne[$i]['3'] = ' '.$code_eleve[$i]; $ligne[$i]['4'] = ''; $ligne[$i]['5'] = ''; $cpt_ligne[$i] = '5'; }
		// nom et prénom de l'élève avec sont code élève
		if($etiquette_type === '3') { $ligne[$i]['1'] = ' '; $ligne[$i]['2'] = ' '.$nom_eleve[$i].' '.$prenom_eleve[$i]; $ligne[$i]['3'] = ' '.$code_eleve[$i]; $ligne[$i]['4'] = ''; $ligne[$i]['5'] = ''; $cpt_ligne[$i] = '5'; }
		// adresse des responsables
		if($etiquette_type === '4') { $ligne[$i]['1'] = ' '.$civilitee_responsable['1'][$i].' '.$prenom_responsable['1'][$i].' '.$nom_responsable['1'][$i]; $ligne[$i]['2'] = ' '.$adresse_responsable['1'][$i]; $ligne[$i]['3'] = ' '.$cp_responsable['1'][$i].' '.$commune_responsable['1'][$i]; $ligne[$i]['4'] = ''; $ligne[$i]['5'] = ''; $cpt_ligne[$i] = '5'; }
		// adresse des responsables et nom de l'élève
		if($etiquette_type === '5') { $ligne[$i]['1'] = ' '.$civilitee_responsable['1'][$i].' '.$prenom_responsable['1'][$i].' '.$nom_responsable['1'][$i]; $ligne[$i]['2'] = '    --- '.$nom_eleve[$i].' '.$prenom_eleve[$i].'('.$classe_eleve[$i].')'; $ligne[$i]['3'] = ' '.$adresse_responsable['1'][$i]; $ligne[$i]['4'] = ' '.$cp_responsable['1'][$i].' '.$commune_responsable['1'][$i]; $ligne[$i]['5'] = ''; $ligne[$i]['6'] = ''; $cpt_ligne[$i] = '6'; }


           $requete_structure ="SELECT * FROM ".$prefix_base."etiquettes_formats WHERE id_etiquette_format = '".$etiquette_format."'";
           $execution_structure = mysql_query($requete_structure) or die('Erreur SQL !'.$requete_structure.'<br />'.mysql_error());
           while ( $donne_structure = mysql_fetch_array($execution_structure))
	    {
		$xcote = $donne_structure['xcote_etiquette_format'];
		$ycote = $donne_structure['ycote_etiquette_format'];
		$espacementx = $donne_structure['espacementx_etiquette_format'];
		$espacementy = $donne_structure['espacementy_etiquette_format'];
		$largeur = $donne_structure['largeur_etiquette_format'];
		$hauteur = $donne_structure['hauteur_etiquette_format'];
		$nbl = $donne_structure['nbl_etiquette_format'];
		$nbh = $donne_structure['nbh_etiquette_format'];
	    }
 	 $i = $i + 1;
	 }
}


define('PARAGRAPH_STRING', '~~~');
define('FPDF_FONTPATH','../../fpdf/font/');
require_once("../../fpdf/class.multicelltag.php");


// mode paysage, a4, etc.
$pdf=new FPDF_MULTICELLTAG('P','mm','A4');

//$pdf->Open();
$pdf->SetAutoPageBreak(false);

	$caractere_utilse = 'Arial';

//calcul du nombre d'étiquette
$nombre_eleve = count($id_eleve);
$nombre_etiquette_par_page = $nbl * $nbh;
$nombre_de_page = ceil($nombre_eleve / $nombre_etiquette_par_page);
//echo $nombre_etiquette_par_page.' '.$nombre_eleve.' '.$nombre_de_page.'<br/>';
$i = '0';

// initialisation du compteur d'élèves
$cpt_eleve = '0';
// initialisation du compteur de page
$cpt_page = 0;
while ( $cpt_page < $nombre_de_page )
{
	// Ajout d'une page
	$pdf->AddPage();
	$pdf->SetFont('arial','',11);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);

if ( !isset($xcote_origine) ) { $xcote_origine = $xcote; }
if ( !isset($ycote_origine) ) { $ycote_origine = $ycote; }

		// placement des premières étiquette
		$pdf->SetY($ycote_origine);
		$ycote_passe = $ycote_origine;
		$ycote_passeh = $ycote_origine;

	$cpt_etiquette = 0;
	while ( $cpt_etiquette < $nombre_etiquette_par_page )
	{

		// boucle hauteur
		$cpt_i_h = '0';
		while ( $cpt_i_h < $nbh )
		{

			$xcote_passe = $xcote;

			// boucle largeur
			$cpt_i_l = '1';
			while($cpt_i_l<=$nbl)
			{

				$pdf->SetFont($caractere_utilse,'',11);
//echo $nombre_etiquette_par_page.' '.$cpt_etiquette.' '.$cpt_eleve.' '.$cpt_page.' '.$i.'<br/>';


				if(!empty($ligne[$cpt_eleve]['1'])) {

				// hauteur de chaque ligne
				$hauteur_select = $hauteur / $cpt_ligne[$i];

					$cpt_ligne_aff = '1';
					while($cpt_ligne_aff <= $cpt_ligne[$i])
					{


						if( $cpt_ligne_aff != '1') { $ycote_passe = $ycote_passe + $hauteur_select; }
	
						$pdf->SetXY($xcote_passe, $ycote_passe);
						// gestion des encadrements
						$code_cadre = '0';
						if($cadre === '1')
						{
							if($cpt_ligne_aff === '1' and $cpt_ligne_aff != $cpt_ligne[$i]) { $code_cadre = 'TRL'; }
							if($cpt_ligne_aff != '1' and $cpt_ligne_aff != $cpt_ligne[$i]) { $code_cadre = 'RL'; }
							if($cpt_ligne_aff != '1' and $cpt_ligne_aff == $cpt_ligne[$i]) { $code_cadre = 'RLB'; }
						}
						// calcule de la taille du texte des appréciation
			 			$hauteur_caractere = '11';
						if($etiquette_type === '5' and $cpt_ligne_aff == '2') { $hauteur_caractere = '6'; }
	
					 	$pdf->SetFont($caractere_utilse,'',$hauteur_caractere);
						$val = $pdf->GetStringWidth($ligne[$cpt_eleve][$cpt_ligne_aff]);
					 	$taille_texte = $largeur-2;
					 	$grandeur_texte='test';
					 	while($grandeur_texte!='ok') {
						 if($taille_texte<$val) 
						  {
						     $hauteur_caractere = $hauteur_caractere-0.3;
						     $pdf->SetFont($caractere_utilse,'',$hauteur_caractere);
						     $val = $pdf->GetStringWidth($ligne[$cpt_eleve][$cpt_ligne_aff]);
						  } else { $grandeur_texte='ok'; }
		                		}
						$grandeur_texte='test';
						$pdf->Cell($largeur, $hauteur_select, $ligne[$cpt_eleve][$cpt_ligne_aff],$code_cadre,0,'L');

					$cpt_ligne_aff = $cpt_ligne_aff + 1;
					}

				// compteur élève
				$cpt_eleve = $cpt_eleve + 1;
			$i = $i + 1;

				// passer à la ligne
				$xcote_passe = $xcote_passe + $largeur + $espacementx;

				// placement de X et Y leur nouveau point
				$ycote_passe = $ycote_passeh; 
				if($cpt_i_l == $nbl) { $ycote_passe = $ycote_passe + $hauteur + $espacementy; $ycote_passeh = $ycote_passe; $xcote_passe = $xcote; }

				}
		$cpt_etiquette = $cpt_etiquette + 1;
			$cpt_i_l = $cpt_i_l + 1;
			}

	 	$cpt_i_h = $cpt_i_h + 1;
		}

	// compteur du nombre d'étiquette affiché sur la page et fin de la boucle des etiquettes par page
//	$cpt_etiquette = $cpt_etiquette + 1;
	}

// compteur de page et fin de la boucle des pages
$cpt_page = $cpt_page + 1;
}

$pdf->Output('Etiquette_'.date("Ymd_Hi").'.pdf','I');
?>
