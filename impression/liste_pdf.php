<?php
/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

// Global configuration file

// Quand on est en SSL, IE n'arrive pas à ouvrir le PDF.
//Le problème peut être résolu en ajoutant la ligne suivante :
//Header('Pragma: public');
//header('Content-Type: application/pdf');

//=============================
// REMONTé:
// Initialisations files
require_once("../lib/initialisations.inc.php");
//=============================
if (!defined('FPDF_VERSION')) {
	require_once(dirname(__FILE__).'/../fpdf/fpdf.php');
}


define('LargeurPage','210');
define('HauteurPage','297');

//debug_var();

/*
// Initialisations files
require_once("../lib/initialisations.inc.php");
*/

require_once("./class_pdf.php");
require_once ("./liste.inc.php");

// Lorsque qu'on utilise une session PHP, parfois, IE n'affiche pas le PDF
// C'est un problème qui affecte certaines versions d'IE.
// Pour le contourner, on ajoutez la ligne suivante avant session_start() :
session_cache_limiter('private');

// Resume session
$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

//INSERT INTO droits VALUES ('/impression/liste_pdf.php', 'V', 'V', 'V', 'V', 'V', 'V', 'Impression des listes (PDF)', '');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// LES OPTIONS DEBUT


if (!isset($_SESSION['marge_haut'])) { $marge_haut = 10 ; } else {$marge_haut =  $_SESSION['marge_haut'];}
if (!isset($_SESSION['marge_droite'])) { $marge_droite = 10 ; } else {$marge_droite =  $_SESSION['marge_droite'];}
if (!isset($_SESSION['marge_gauche'])) { $marge_gauche = 10 ; } else {$marge_gauche =  $_SESSION['marge_gauche'];}
if (!isset($_SESSION['marge_bas'])) { $marge_bas = 10 ; } else {$marge_bas =  $_SESSION['marge_bas'];}
if (!isset($_SESSION['marge_reliure'])) { $marge_reliure = 1 ; } else {$marge_reliure =  $_SESSION['marge_reliure'];}
if (!isset($_SESSION['avec_emplacement_trous'])) { $avec_emplacement_trous = 1 ; } else {$avec_emplacement_trous =  $_SESSION['avec_emplacement_trous'];}


//Gestion de la marge à gauche pour une reliure éventuelle ou des feuilles perforées.
if ($marge_reliure==1) {
  if ($marge_gauche < 18) {$marge_gauche = 18;}
}


//Calcul de la Zone disponible
$EspaceX = LargeurPage - $marge_droite - $marge_gauche ;
$EspaceY = HauteurPage - $marge_haut - $marge_bas;

$X_tableau = $marge_gauche;


//entête classe et année scolaire
$L_entete_classe = 65;
$H_entete_classe = 14;
$X_entete_classe = $EspaceX - $L_entete_classe + $marge_gauche;
$Y_entete_classe = $marge_haut;

$X_entete_matiere = $marge_gauche;
$Y_entete_matiere = $marge_haut;
$L_entete_discipline = 65;
$H_entete_discipline = 14;

// LES OPTIONS suite


if (!isset($_SESSION['h_ligne'])) { $h_ligne = 8 ; } else {$h_ligne =  $_SESSION['h_ligne'];} //hauteur d'une ligne du tableau
if (!isset($_SESSION['l_colonne'])) { $l_colonne = 8 ; } else {$l_colonne =  $_SESSION['l_colonne'];} // largeur d'une colonne
if (!isset($_SESSION['l_nomprenom'])) { $l_nomprenom = 40 ; } else {$l_nomprenom =  $_SESSION['l_nomprenom'];} // la largeur de la colonne nom - prénom

if (!isset($_SESSION['nb_ligne_avant'])) { $nb_ligne_avant = 2 ; } else {$nb_ligne_avant =  $_SESSION['nb_ligne_avant'];}
if (!isset($_SESSION['h_ligne1_avant'])) { $h_ligne1_avant = 25 ; } else {$h_ligne1_avant =  $_SESSION['h_ligne1_avant'];} //Hauteur de la 1ère ligne avant
if (!isset($_SESSION['nb_ligne_apres'])) { $nb_ligne_apres = 1 ; } else {$nb_ligne_apres =  $_SESSION['nb_ligne_apres'];}

if (!isset($_SESSION['affiche_pp'])) { $affiche_pp = 1 ; } else {$affiche_pp =  $_SESSION['affiche_pp'];}// 0 On n'affiche pas le PP 1 on l'affiche

if (!isset($_SESSION['avec_ligne_texte'])) { $avec_ligne_texte = 1 ; } else {$avec_ligne_texte =  $_SESSION['avec_ligne_texte'];} //Permet d'inscrire une ligne de texte sous les entête, avant la tableau
if (!isset($_SESSION['ligne_texte'])) { $ligne_texte = " " ; } else {$ligne_texte =  $_SESSION['ligne_texte'];} // La ligne de texte

if (!isset($_SESSION['afficher_effectif'])) { $afficher_effectif = 1 ; } else {$afficher_effectif =  $_SESSION['afficher_effectif'];}// Indique l'effectif dans le tableau

if (!isset($_SESSION['une_seule_page'])) { $une_seule_page = 1 ; } else {$une_seule_page =  $_SESSION['une_seule_page'];} // Faire tenir sur une seule page la classe 0 nom - 1 oui


if (!isset($_SESSION['encadrement_total_cellules'])) { $encadrement_total_cellules = 1 ; } else {$encadrement_total_cellules =  $_SESSION['encadrement_total_cellules'];}// 1 quagrillage de toute la ligne. 0 quadrillage partiel déterminé par $nb_cellules_quadrillees
if (!isset($_SESSION['nb_cellules_quadrillees'])) { $nb_cellules_quadrillees = 5 ; } else {$nb_cellules_quadrillees =  $_SESSION['nb_cellules_quadrillees'];}//nombre de ligne au debut du quadrillage avant un bloc vide
if (!isset($_SESSION['zone_vide'])) { $zone_vide = 1 ; } else {$zone_vide =  $_SESSION['zone_vide'];}//0 non 1 OUI
if (!isset($_SESSION['hauteur_zone_finale'])) { $hauteur_zone_finale = 20 ; } else {$hauteur_zone_finale =  $_SESSION['hauteur_zone_finale'];}//si 0 == > on prend ce qui reste

if(isset($_POST['id_modele'])) {
	if($_POST['id_modele']!='') {
		$sql="SELECT * FROM modeles_grilles_pdf_valeurs WHERE id_modele='".$_POST['id_modele']."';";
		//echo "$sql<br >\n";
		$res_modele=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_modele)) {
			while($lig_modele=mysqli_fetch_object($res_modele)) {
				$nom_champ=$lig_modele->nom;
				$$nom_champ=$lig_modele->valeur;
				/*
				echo "1<br />";
				echo "\$nom_champ=$lig_modele->nom<br >\n";
				echo "\$$nom_champ=$lig_modele->valeur<br >\n";
				echo "\$$nom_champ=".$$nom_champ."<br >\n\n";
				*/
			}
			$_SESSION['id_modele']=$_POST['id_modele'];
		}
		else {
			unset($_SESSION['id_modele']);
		}
	}
	else {
		unset($_SESSION['id_modele']);
	}
}
elseif(isset($_SESSION['id_modele'])) {
	// On verifie que le modele existe et on recupere les valeurs
	$sql="SELECT * FROM modeles_grilles_pdf_valeurs WHERE id_modele='".$_SESSION['id_modele']."';";
	//echo "$sql<br >\n";
	$res_modele=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_modele)) {
		while($lig_modele=mysqli_fetch_object($res_modele)) {
			$nom_champ=$lig_modele->nom;
			$$nom_champ=$lig_modele->valeur;
			/*
			echo "2<br />";
			echo "\$nom_champ=$lig_modele->nom<br >\n";
			echo "\$$nom_champ=$lig_modele->valeur<br >\n";
			echo "\$$nom_champ=".$$nom_champ."<br >\n\n";
			*/
		}
	}
	else {
		unset($_SESSION['id_modele']);
	}
}

//debug_var();
//die();

$nb_ligne_avant_initial = $nb_ligne_avant; //pour l'enchainemenet de PDF !

$texte = '';

$nb_colonne = 0;
//Calcul du nombre de colonnes en fonction des marges et de la largeur de la colonne.
$nb_colonne = ($EspaceX - $l_nomprenom) / $l_colonne;
$nb_colonne = intval(abs($nb_colonne)); //partie entière


// Cas d'un quadrillage total
if ($encadrement_total_cellules ==1) {
  $nb_cellules_quadrillees = $nb_colonne; //nb ce cellule après le nom
} // Sinon avec le calcul.


// Définition de la page
$pdf=new rel_PDF("P","mm","A4");
$pdf->SetTopMargin($marge_haut);
$pdf->SetRightMargin($marge_droite);
$pdf->SetLeftMargin($marge_gauche);
$pdf->SetAutoPageBreak(true, $marge_bas);


//On recupère les variables pour l'affichage et on traite leur existance.
// DE   IMPRIME.PHP
$id_classe=isset($_GET['id_classe']) ? $_GET["id_classe"] : NULL;
$id_groupe=isset($_GET['id_groupe']) ? $_GET["id_groupe"] : NULL;
$id_periode=isset($_GET['periode_num']) ? $_GET["periode_num"] : NULL;

//On recupère les variables pour l'affichage
// DE  IMPRIME_SERIE.PHP
// les tableaux contienent la liste des id.
$id_liste_classes=isset($_POST['id_liste_classes']) ? $_POST["id_liste_classes"] : NULL;
$id_liste_groupes=isset($_POST['id_liste_groupes']) ? $_POST["id_liste_groupes"] : NULL;
//echo "count(\$id_liste_groupes)=".count($id_liste_groupes)."<br />";
if ($id_periode==NULL){$id_periode=isset($_POST['id_periode']) ? $_POST["id_periode"] : NULL;}
if (!(is_numeric($id_periode))) {
	$id_periode=1;
}

$tri=isset($_POST['tri']) ? $_POST['tri'] : (isset($_GET['tri']) ? $_GET['tri'] : '');

$nb_pages = 0;
$nb_eleves = 0;
$flag_groupe = FALSE;

// DEFINIR LE NOMBRE DE BOUCLES A FAIRE
// Impressions RAPIDES
if ($id_groupe!=NULL) { // C'est un groupe
    $nb_pages=1;
	$flag_groupe = true;
}

if ($id_classe!=NULL) { // C'est une classe
    $nb_pages=1;
} //fin c'est une classe

//IMPRESSION A LA CHAINE
if ($id_liste_classes!=NULL) {
    $nb_pages = sizeof($id_liste_classes);
//echo $nb_pages;
}

//IMPRESSION A LA CHAINE
if ($id_liste_groupes!=NULL) {
    $nb_pages = sizeof($id_liste_groupes);
	$flag_groupe = true;
//echo $nb_pages;
}


if ($id_groupe != NULL) {
	$current_group = get_group($id_groupe);
}
elseif((isset($id_liste_groupes))&&(count($id_liste_groupes)==1)) {
	$current_group = get_group($id_liste_groupes[0]);
}

$flag_groupe_plusieurs_classes = "n";
if(count($current_group['classes']['list'])>1) {
	$flag_groupe_plusieurs_classes = "y";
}
//$info_tmp=count($current_group['classes']['list']);

//echo $nb_pages;

	// Cette boucle crée les différentes pages du PDF
	for ($i_pdf=0; $i_pdf<$nb_pages ; $i_pdf++) {
		// Impressions RAPIDES
		if ($id_groupe!=NULL) { // C'est un groupe
			$donnees_eleves = traite_donnees_groupe($id_groupe,$id_periode,$nb_eleves,$tri);
			if(count($donnees_eleves)>0) {
				$id_classe=$donnees_eleves[0]['id_classe'];
			}
		} elseif ($id_classe!=NULL) { // C'est une classe
			$donnees_eleves = traite_donnees_classe($id_classe,$id_periode,$nb_eleves);
		} //fin c'est une classe

		//IMPRESSION A LA CHAINE
		if ($id_liste_groupes!=NULL) {
			$donnees_eleves = traite_donnees_groupe($id_liste_groupes[$i_pdf],$id_periode,$nb_eleves,$tri);
			$id_groupe=$id_liste_groupes[$i_pdf];
			if(count($donnees_eleves)>0) {
				$id_classe=$donnees_eleves[0]['id_classe'];
			}
		}
/*
echo "\$i_pdf=$i_pdf<br />
\$donnees_eleves
<pre>";
print_r($donnees_eleves);
echo "</pre>";
*/
		if ($id_liste_classes!=NULL) {
			$donnees_eleves = traite_donnees_classe($id_liste_classes[$i_pdf],$id_periode,$nb_eleves);
			$id_classe=$id_liste_classes[$i_pdf];
		}

		//echo "count(\$donnees_eleves)=".count($donnees_eleves)."<br />";
		if(count($donnees_eleves)==0) {
			$pdf->AddPage("P"); //ajout d'une page au document
			$pdf->SetDrawColor(0,0,0);
			$pdf->SetFont('DejaVu');
			$pdf->SetXY(20,20);
			$pdf->SetFontSize(14);
			$pdf->Cell(90,7, "ERREUR",0,2,'');

			$pdf->SetXY(20,40);
			$pdf->SetFontSize(10);
			$pdf->Cell(150,7, "Aucun élève n'est affecté dans cette classe ou enseignement.",0,2,'');

			$nom_releve=date("Ymd_Hi");
			$nom_releve = 'Liste_'.$nom_releve.'.pdf';
			//header('Content-Type: application/pdf');
			send_file_download_headers('application/pdf',$nom_releve);
			$pdf->Output($nom_releve,'I');
			die();
		}


		// CALCUL de VARIABLES
		//Calcul de la hauteur de la ligne dans le cas de l'option tout sur une ligne
		if ($une_seule_page == 1) {
			  $hauteur_disponible = HauteurPage - $marge_haut - $marge_bas - $H_entete_classe - 5 - 2.5 - $hauteur_zone_finale; //2.5 ==> avant le pied de page

			  if ($avec_ligne_texte ==1) {
				$hauteur_disponible = $hauteur_disponible - 12.5;
			  }

			  // le nombre de lignes demandées.
			  $nb_ligne_demande = $nb_eleves + $nb_ligne_avant + $nb_ligne_apres;

			  // cas 1ère ligne plus haute
			  if (($h_ligne1_avant !=0)){
				$hauteur_disponible = $hauteur_disponible - $h_ligne1_avant;
				$nb_ligne_demande--;
			  }

			  $h_ligne = $hauteur_disponible / $nb_ligne_demande ;
			  //echo $h_ligne;

			  //Cas particulier ou après calcul, h_ligne > $h_ligne1_avant
			  if ($h_ligne>$h_ligne1_avant) {
				  $hauteur_disponible = HauteurPage - $marge_haut - $marge_bas - $H_entete_classe - 5 - 2.5 - $hauteur_zone_finale; //2.5 ==> avant le pied de page

				  if ($avec_ligne_texte ==1) {
					$hauteur_disponible = $hauteur_disponible - 12.5;
				  }
				  // le nombre de lignes demandées.
				  $nb_ligne_demande = $nb_eleves + $nb_ligne_avant + $nb_ligne_apres;

				  $h_ligne = ($hauteur_disponible / $nb_ligne_demande );
			  }
		}

		$pdf->AddPage("P");
		// Couleur des traits
		$pdf->SetDrawColor(0,0,0);

		// caractère utilisé dans le document
		$caractere_utilise = 'DejaVu';

		// on appelle une nouvelle page pdf
		$nb_eleves_i = 0;

		//Entête du PDF
			$pdf->SetLineWidth(0.7);
			$pdf->SetFont('DejaVu','',14);
			$pdf->Setxy($X_entete_classe,$Y_entete_classe);

			if ($id_classe != NULL) {
			  $calldata = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes WHERE id = '$id_classe'");
			  $current_classe = old_mysql_result($calldata, 0, "classe");
			} else {
			   $sql = "SELECT * FROM classes WHERE id = '$id_classe'";
			   $calldata = mysqli_query($GLOBALS["mysqli"], $sql);
			   $current_classe = old_mysql_result($calldata, 0, "classe");
			}

			if (($affiche_pp==1)) {
			   if ($id_groupe == NULL) {
				   $pdf->CellFitScale($L_entete_classe,$H_entete_classe / 2,'Classe de '.$current_classe,'LTR',2,'C');
				   $pdf->SetFont('DejaVu','I',8.5);
			   }
			  //PP de la classe
			  if ($id_groupe != NULL) {
				$current_group = get_group($id_groupe);
			  // On n'affiche pas le PP (il peut y en avoir plusieurs) ==> on affiche la période
			    $sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$id_classe' AND num_periode='$id_periode' ORDER BY num_periode";
				$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_per)==0){
					die("Problème avec les infos de la classe $id_classe</body></html>");
				}
				else{
					$lig_tmp=mysqli_fetch_object($res_per);
					$periode=$lig_tmp->nom_periode;
					//Affichage  de la période
					//$pdf->CellFitScale($L_entete_discipline,$H_entete_classe ,$periode,'TLBR',2,'C');
					if(isset($current_group)) {
						$texte_per=$current_group['classlist_string']." - ".$periode;
						//$texte_per=$current_group['classlist_string'];
					}
					else {
						$texte_per=$periode;
					}
					$pdf->CellFitScale($L_entete_discipline,$H_entete_classe ,$texte_per,'TLBR',2,'C');
				}
			   } else {
				   $sql = "SELECT professeur FROM j_eleves_professeurs WHERE (login = '".$donnees_eleves[0]['login']."' and id_classe='$id_classe')";
				   $call_profsuivi_eleve = mysqli_query($GLOBALS["mysqli"], $sql);
				   $current_eleve_profsuivi_login = @old_mysql_result($call_profsuivi_eleve, '0', 'professeur');

				   $gepi_prof_suivi=getParamClasse($id_classe, 'gepi_prof_suivi', getSettingValue('gepi_prof_suivi'));
				   $pdf->CellFitScale($L_entete_classe,$H_entete_classe / 2,ucfirst($gepi_prof_suivi).' : '.affiche_utilisateur($current_eleve_profsuivi_login,$id_classe),'LRB',0,'L');//'Année scolaire '.getSettingValue('gepiYear')
			   }
			}

			else {
              // On n'affiche pas le PP (il peut y en avoir plusieurs) ==> on affiche la période
			  if ($id_groupe != NULL) {
				$current_group = get_group($id_groupe);
				$sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$id_classe' AND num_periode='$id_periode' ORDER BY num_periode";
				$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_per)==0){
					die("Problème avec les infos de la classe $id_classe</body></html>");
				}
				else{
					$lig_tmp=mysqli_fetch_object($res_per);
					$periode=$lig_tmp->nom_periode;
					if(isset($current_group)) {
						$texte_per=$current_group['classlist_string']." - ".$periode;
					}
					else {
						$texte_per=$periode;
					}
					//Affichage  de la période
					//$pdf->CellFitScale($L_entete_discipline,$H_entete_classe ,$periode,'TLBR',2,'C');
					$pdf->CellFitScale($L_entete_discipline,$H_entete_classe ,$texte_per,'TLBR',2,'C');
				}
			  }
			  //$pdf->CellFitScale($L_entete_classe,$H_entete_classe,' '.$current_classe,'LTRB',2,'C');
			}


			$pdf->Setxy($X_entete_matiere,$Y_entete_matiere);
			$pdf->SetFont('DejaVu','',14);


			//Si on peut connaître le nom de la matière (id_groupe existe !)
			if ($id_groupe != NULL) {
				$current_group = get_group($id_groupe);
				$matiere = $current_group["description"];
                 //echo $matiere."<br/>";
				$pdf->CellFitScale($L_entete_discipline,$H_entete_discipline /2 ,$matiere,'LTR',2,'C');
				$pdf->SetFont('DejaVu','I',11);
				$pdf->Cell($L_entete_classe,$H_entete_classe / 2,'Année scolaire '.getSettingValue('gepiYear'),'LRB',2,'C');
			} else {
			// On demande une classe ==> on ajoute la période.
			        $pdf->SetFont('DejaVu','I',11);

				$sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$id_classe' AND num_periode=$id_periode ORDER BY num_periode";
				$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_per)==0){
					die("Problème avec les infos de la classe $id_classe</body></html>");
				}
				else{
					$lig_tmp=mysqli_fetch_object($res_per);
					$periode=$lig_tmp->nom_periode;
					$pdf->Cell($L_entete_classe,$H_entete_classe / 2,'Année scolaire '.getSettingValue('gepiYear'),'TLR',2,'C');
					$pdf->CellFitScale($L_entete_discipline,$H_entete_classe / 2 ,$periode,'LBR',2,'C');
				}
			}

			$Y_courant=$pdf->GetY()+2.5;
			$pdf->Setxy($marge_gauche,$Y_courant);

		//La ligne de texte après les entêtes
			if ($avec_ligne_texte==1) {
			    if ($ligne_texte == '') {$ligne_texte=' ';}
				$pdf->CellFitScale(0,10,$ligne_texte,'',2,'C');
				$Y_courant=$pdf->GetY()+2.5;
			}

		//debut tableau;
			$pdf->SetLineWidth(0.3);
			$pdf->SetFont('DejaVu','',9);
			$y_tmp = $Y_courant;
			//Nb de ligne AVANT dans le tableau
			//echo "\$nb_ligne_avant=$nb_ligne_avant<br />";die();
			if ($nb_ligne_avant > 0) {

				//echo "\$h_ligne1_avant=$h_ligne1_avant<br />\n";echo "\$h_ligne=$h_ligne<br />\n";die();

				//la première ligne peut être plus haute
				if ($h_ligne1_avant > $h_ligne) {
					//Une première ligne qui peut être plus haute (date ou nom du devoir par ex)
					$pdf->Setxy($X_tableau,$y_tmp);

					// Option effectif
					if (($afficher_effectif == 1) and ($nb_ligne_avant == 1)) {
					  //on indique ici le nombre d'élève dans la classe (option)
					  $texte = 'Effectif : '.$nb_eleves;
					  //$pdf->Cell($l_nomprenom,$h_ligne1_avant,$texte,'R',2,'C',2);
					  $pdf->Cell($l_nomprenom,$h_ligne1_avant,$texte,'R',2,'C',0);
					} else {
						//$pdf->Cell($l_nomprenom,$h_ligne1_avant,' ','R',2,'C',2);
						// Avec le 'R' pas de bordure
						$pdf->Cell($l_nomprenom,$h_ligne1_avant,' ','R',2,'L',0);
						//$pdf->Cell($l_nomprenom,$h_ligne1_avant,' Plop ','R',2,'C',2);
					}

					//la fin du quadrillage
					for($i=0; $i < $nb_colonne ; $i++) {
						$pdf->Setxy($X_tableau+$l_nomprenom + $i*$l_colonne,$y_tmp);
						if ($i<$nb_cellules_quadrillees) {
							//$pdf->Cell($l_colonne,$h_ligne1_avant,'',1,2,'C',2); //le quadrillage
							// Cell(largeur,hauteur,texte,epaisseur_bord,???,alignement,remplissage)
							$pdf->Cell($l_colonne,$h_ligne1_avant,'',1,2,'C',0); //le quadrillage
						} else {
							if ($i < ($nb_colonne-1)) {
								//$pdf->Cell($l_colonne,$h_ligne1_avant,'','TB',2,'C',2); //suivant le type : plus de quadrillage
								$pdf->Cell($l_colonne,$h_ligne1_avant,'','TB',2,'C',0);
							} else {
								//$pdf->Cell($l_colonne,$h_ligne1_avant,'','TBR',2,'C',2); // pour le dernier, on clos le tableau
								$pdf->Cell($l_colonne,$h_ligne1_avant,'','TBR',2,'C',0); // pour le dernier, on clos le tableau
							}
						}
					}

					$y_tmp = $pdf->GetY();
					$nb_ligne_avant--;

				}

				//La suite des lignes avant
				for($j=0; $j < $nb_ligne_avant ; $j++) {
					$pdf->Setxy($X_tableau,$y_tmp);
					// Option effectif
					//echo $j."<br>";
					//echo $nb_ligne_avant."<br>";;

					if (($afficher_effectif == 1) and ($nb_ligne_avant == 1)) { // Attention possibilite de cas non traité
						//on indique ici le nombre d'élève dans la classe (option)
						//on indique ici le nombre d'élève dans la classe (option)
						$texte = 'Effectif : '.$nb_eleves;
					}
					if (($afficher_effectif == 1) and ($j == $nb_ligne_avant-2)) {
						$pdf->Cell($l_nomprenom,$h_ligne1_avant,$texte,'R',0,'C',0);
					} else {
						if (($nb_ligne_avant-2 <=0) and ($nb_ligne_avant < 2)) {
							$pdf->Cell($l_nomprenom,$h_ligne,$texte,'R',0,'C',0);
						} else {
							$pdf->Cell($l_nomprenom,$h_ligne,'','R',0,'C',0);
						}
					}
	
					for($i=0; $i < $nb_colonne ; $i++) {
						$pdf->Setxy($X_tableau+$l_nomprenom + $i*$l_colonne,$y_tmp);
						if ($i<$nb_cellules_quadrillees) {
							$pdf->Cell($l_colonne,$h_ligne,'',1,0,'C',0); //le quadrillage
						} else {
							if ($i < ($nb_colonne-1)) {
								$pdf->Cell($l_colonne,$h_ligne,'','TB',0,'C',0); //suivant le type : plus de quadrillage
							} else {
								$pdf->Cell($l_colonne,$h_ligne,'','TBR',0,'C',0); // pour le dernier, on clos le tableau
							}
						}
					}
					$y_tmp = $y_tmp + $h_ligne;
					$pdf->ln();
				}

				$y_tmp = $pdf->GetY();

				$nb_ligne_avant = $nb_ligne_avant_initial;
			}


			// Ordonnee premier eleve pour la premiere page de la classe/groupe
			$y_top_tableau=$y_tmp;

			// Le tableau
			$compteur_eleves_page=0;
			while($nb_eleves_i < $nb_eleves) {

				if(strtr($y_tmp,",",".")+strtr($h_ligne,",",".")>297-$marge_haut-$marge_bas-$h_ligne-5) {
					// Haut du tableau pour la deuxieme, troisieme,... page de la classe
					// Pour la deuxieme, troisieme,... page d'une classe, on n'a pas d'entete:
					$y_top_tableau=$marge_haut;

					$pdf->AddPage("P");
					$pdf->Setxy($X_tableau,$y_top_tableau);
					$compteur_eleves_page=0;
				}

				//$y_tmp = $pdf->GetY();
				$y_tmp = $y_top_tableau+$compteur_eleves_page*$h_ligne;

				$pdf->Setxy($X_tableau,$y_tmp);
				$pdf->SetFont('DejaVu','',9);
				//if ($flag_groupe==true) {
				if ($flag_groupe_plusieurs_classes=="y") {
					$texte = (($donnees_eleves[$nb_eleves_i]['nom'])." ".($donnees_eleves[$nb_eleves_i]['prenom']." (".$donnees_eleves[$nb_eleves_i]['nom_court'].")"));
				} else {
					$texte = (($donnees_eleves[$nb_eleves_i]['nom'])." ".($donnees_eleves[$nb_eleves_i]['prenom']));
				}

				//$pdf->CellFitScale($l_nomprenom,$h_ligne,$texte,1,0,'L',0); //$l_nomprenom.' - '.$h_ligne.' / '.$X_tableau.' - '.$y_tmp

				$taille_max_police=9;
				$taille_min_police=ceil($taille_max_police/3);
				cell_ajustee($texte,$pdf->GetX(),$y_tmp,$l_nomprenom,$h_ligne,$taille_max_police,$taille_min_police,'LRBT');

				for($i=0; $i < $nb_colonne ; $i++) {
					//$y_tmp = $pdf->GetY();
					$pdf->Setxy($X_tableau+$l_nomprenom + $i*$l_colonne,$y_tmp);
					if ($i<$nb_cellules_quadrillees) {
					   $pdf->Cell($l_colonne,$h_ligne,'',1,0,'C',0); //le quadrillage
					} else {
					   if ($i < ($nb_colonne-1)) {
						 $pdf->Cell($l_colonne,$h_ligne,'','TB',0,'C',0); //suivant le type : plus de quadrillage
					   } else {
						   $pdf->Cell($l_colonne,$h_ligne,'','TBR',0,'C',0); // pour le dernier, on clos le tableau
					   }
					}
				}

				$pdf->ln();
				$nb_eleves_i = $nb_eleves_i + 1;
				//$y_tmp = $y_tmp + $h_ligne;

				$compteur_eleves_page++;
			}
			$y_tmp = $pdf->GetY();


			if ($nb_ligne_apres > 0) {
			//Nb de ligne APRES
				for($j=0; $j < $nb_ligne_apres ; $j++) {
					$y_tmp = $pdf->GetY();
					$pdf->Setxy($X_tableau,$y_tmp);
					$pdf->Cell($l_nomprenom,$h_ligne,' ',1,0,'C',0);

					for($i=0; $i < $nb_colonne ; $i++) {
						$pdf->Setxy($X_tableau+$l_nomprenom + $i*$l_colonne,$y_tmp);
						if ($i<$nb_cellules_quadrillees) {
						   $pdf->Cell($l_colonne,$h_ligne,'',1,0,'C',0); //le quadrillage
						} else {
						   if ($i < ($nb_colonne-1)) {
							 $pdf->Cell($l_colonne,$h_ligne,'','TB',0,'C',0); //suivant le type : plus de quadrillage
						   } else {
							   $pdf->Cell($l_colonne,$h_ligne,'','TBR',0,'C',0); // pour le dernier, on clos le tableau
						   }
						}
					}
					$pdf->ln();
					$y_tmp = $pdf->GetY();
				}
			}

			//le bloc du bas encadré.
			if ($zone_vide==1) {
			  $y_tmp = $pdf->GetY()+2.5;
			  $pdf->Setxy($X_tableau,$y_tmp);
			  if ($hauteur_zone_finale ==0) {
				// on prend tout ce qui reste
				$espace_restant = $EspaceY - $y_tmp + $marge_bas -2.5;
				if ($espace_restant >= 10) { // on ne met le bloc que si lespace est > à 10
				  $pdf->Cell(0,$espace_restant,' ',1,0,'C',0);
				}
			  } else {
				$pdf->Cell(0,$hauteur_zone_finale -2.5,' ',1,0,'C',0);
			  }
			}
		} // FOR

	// sortie PDF sur écran
	$nom_releve="";
	if(isset($current_group)) {
		$nom_releve=remplace_accents($current_group['name']."_".$current_group['description']."_-_".$current_group['classlist_string']."_", "all");
	}

	$pref_output_mode_pdf=get_output_mode_pdf();

	$nom_releve.=date("Ymd_Hi");
	$nom_releve = 'Liste_'.$nom_releve.'.pdf';
	//header('Content-Type: application/pdf');
	send_file_download_headers('application/pdf',$nom_releve);
	$pdf->Output($nom_releve,$pref_output_mode_pdf);
?>
