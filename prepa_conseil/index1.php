<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

if(!getSettingAOui('active_bulletins')) {
	header("Location: ../accueil.php?msg=Module_inactif");
	die();
}

if (!isset($bord)) {$bord=isset($_SESSION['prepa_conseil_index1_bord']) ? $_SESSION['prepa_conseil_index1_bord'] : NULL;}
if (!isset($larg_tab)) {$larg_tab=isset($_SESSION['prepa_conseil_index1_larg_tab']) ? $_SESSION['prepa_conseil_index1_larg_tab'] : NULL;}

if (!isset($en_tete)) {$en_tete = "yes";}
if (!isset($stat)) {$stat = "no";}
if (!isset($larg_tab)) {$larg_tab = 680;}
if (!isset($bord)) {$bord = 1;}
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "nom");
$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
    $current_group = get_group($id_groupe);
} else {
    $current_group = false;
}

if (count($current_group["classes"]["list"]) > 1) {
    $multiclasses = true;
} else {
    $multiclasses = false;
    $order_by = "nom";
}

$couleur_alterne=isset($_POST['couleur_alterne']) ? $_POST['couleur_alterne'] : (isset($_GET['couleur_alterne']) ? $_GET['couleur_alterne'] : "n");

if((isset($_POST['col_tri']))&&($_POST['col_tri']==1)) {
	$order_by = "nom";
}

//debug_var();
//=====================================================
if ((isset($_POST['mode']))&&($_POST['mode']=='csv')) {

	$now = gmdate('D, d M Y H:i:s') . ' GMT';

	$chaine_titre="export";
	if(isset($current_group)) {
		//$chaine_titre=$current_group['name']."_".$current_group['description'];
		$chaine_titre=$current_group['name']."_".preg_replace("/,/","_",$current_group['classlist_string']);
	}

	//$nom_fic=$chaine_titre."_".$now.".csv";
	$nom_fic=$chaine_titre."_".$now;

	// Filtrer les caractères dans le nom de fichier:
	$nom_fic=preg_replace("/[^a-zA-Z0-9_\.-]/","",remplace_accents($nom_fic,'all'));

	$fd="";
	//$fd.=affiche_tableau_csv2($nb_lignes_tableau, $nb_col, $ligne1_csv, $col);

	$ligne1_csv=$_POST['ligne1_csv'];
	//$col_csv=$_POST['col_csv'];
	$lignes_csv=$_POST['lignes_csv'];

	$fd.=preg_replace("/;/",",",preg_replace("/&#039;/","'",html_entity_decode($ligne1_csv[1])));
	for($i=2;$i<=count($ligne1_csv);$i++) {
		$fd.=";".preg_replace("/;/",",",preg_replace("/&#039;/","'",html_entity_decode($ligne1_csv[$i])));
	}
	$fd.="\n";

	for($j=0;$j<count($lignes_csv);$j++) {
		$fd.=preg_replace("/&#039;/","'",html_entity_decode($lignes_csv[$j])."\n");
	}

	$nom_fic.=".csv";

	send_file_download_headers('text/x-csv',$nom_fic);
	//echo $fd;
	echo echo_csv_encoded($fd);
	die();
}
if ((isset($_POST['mode']))&&($_POST['mode']=='pdf')) {

	$now = gmdate('D, d M Y H:i:s') . ' GMT';

	$chaine_titre="export";
	if(isset($current_group)) {
		$chaine_titre=$current_group['name']."_".preg_replace("/,/","_",$current_group['classlist_string']);
	}

	//$nom_fic=$chaine_titre."_".$now.".pdf";
	$nom_fic=$chaine_titre."_".$now;

	// Filtrer les caractères dans le nom de fichier:
	$nom_fic=preg_replace("/[^a-zA-Z0-9_\.-]/","",remplace_accents($nom_fic,'all'));

	$nom_fic.=".pdf";

	//include("get_param_pdf.php");

	// Extraire les infos générales sur l'établissement

	require_once('../fpdf/fpdf.php');
	require_once("../fpdf/class.multicelltag.php");

	// Fichier d'extension de fpdf pour le bulletin
	require_once("../class_php/gepi_pdf.class.php");

	// Fonctions php des bulletins pdf
	require_once("../bulletin/bulletin_fonctions.php");
	// Ensemble des données communes
	require_once("../bulletin/bulletin_donnees.php");

	
	session_cache_limiter('private');

	$X1 = 0; $Y1 = 0; $X2 = 0; $Y2 = 0;
	$X3 = 0; $Y3 = 0; $X4 = 0; $Y4 = 0;
	$X5 = 0; $Y5 = 0; $X6 = 0; $Y6 = 0;

	$annee_scolaire = $gepiYear;

	$gepiSchoolName=getSettingValue('gepiSchoolName');

	$ligne1_csv=$_POST['ligne1_csv'];
	$lignes_csv=$_POST['lignes_csv'];


	$largeur_page=210;
	$hauteur_page=297;

	$pref_marge=isset($_POST['marge_pdf_mes_moyennes']) ? $_POST['marge_pdf_mes_moyennes'] : getPref($_SESSION['login'],'marge_pdf_mes_moyennes',7);
	if(($pref_marge=="")||(!preg_match("/^[0-9]*$/", $pref_marge))||($pref_marge<5)) {
		$pref_marge=7;
	}
	else {
		savePref($_SESSION['login'], 'marge_pdf_mes_moyennes', $pref_marge);
	}
	//marge_pdf_mes_moyennes
	$marge_gauche=$pref_marge;
	$marge_droite=$pref_marge;
	$marge_haute=$pref_marge;
	$marge_basse=$pref_marge;

	$hauteur_police=10;
	$largeur_col_nom_ele=40;

	// Taille en-dessous de laquelle on passe en format Paysage.
	$largeur_min_app=55;

	// Hauteur des lignes:
	//$h_cell=10;
	$h_cell=isset($_POST['h_cell']) ? $_POST['h_cell'] : getPref($_SESSION['login'], 'hauteur_ligne_pdf_mes_moyennes', 10);
	if((!preg_match("/^[0-9]+$/", $h_cell)||($h_cell<5))) {
		$h_cell=10;
	}
	else {
		savePref($_SESSION['login'], 'hauteur_ligne_pdf_mes_moyennes', $h_cell);
	}


	$h_ligne_titre_tableau=10;

	// Largeur des colonnes
	$largeur_col=array();
	$largeur_col[1]=$largeur_col_nom_ele;
	$indice_col_app=array();

	$taille_max_police=$hauteur_police;
	$taille_min_police=ceil($taille_max_police/3);

	$x0=$marge_gauche;
	$y0=$marge_haute;

	$largeur_nomprenom_classe_et_notes=$marge_gauche+$largeur_col_nom_ele;
	$nb_col_app=0;
	for($i=2;$i<=count($ligne1_csv);$i++) {

		if(preg_match("/^Note/", $ligne1_csv[$i])) {
			$largeur_nomprenom_classe_et_notes+=10;
			$largeur_col[$i]=10;
		}
		elseif(preg_match("/^Classe/", $ligne1_csv[$i])) {
			$largeur_nomprenom_classe_et_notes+=10;
			$largeur_col[$i]=10;
		}
		elseif(preg_match("/^Rang/", $ligne1_csv[$i])) {
			$largeur_nomprenom_classe_et_notes+=10;
			$largeur_col[$i]=10;
		}
		elseif(preg_match("/^Moyenne/", $ligne1_csv[$i])) {
			$largeur_nomprenom_classe_et_notes+=10;
			$largeur_col[$i]=10;
		}
		else {
			$nb_col_app++;
			$indice_col_app[]=$i;
		}
	}

	$format_page="P";
	if($nb_col_app>0) {
		$largeur_col_app=floor(($largeur_page-$marge_droite-$largeur_nomprenom_classe_et_notes)/$nb_col_app);

		if($largeur_col_app<$largeur_min_app) {
			$format_page="L";
			$largeur_page=297;
			$hauteur_page=210;

			$largeur_col_app=floor(($largeur_page-$marge_droite-$largeur_nomprenom_classe_et_notes)/$nb_col_app);
		}
	}

	for($i=0;$i<count($indice_col_app);$i++) {
		$largeur_col[$indice_col_app[$i]]=$largeur_col_app;
	}

	$pdf=new bul_PDF($format_page, 'mm', 'A4');
	$pdf->SetCreator($gepiSchoolName);
	$pdf->SetAuthor($gepiSchoolName);
	$pdf->SetKeywords('');
	$pdf->SetSubject('Mes_moyennes');
	$pdf->SetTitle('Mes_moyennes');
	$pdf->SetDisplayMode('fullwidth', 'single');
	$pdf->SetCompression(TRUE);
	$pdf->SetAutoPageBreak(TRUE, 5);

	$pdf->AddPage();
	$fonte='DejaVu';

	$pdf->SetFont($fonte,'B',8);

	$texte_titre=$current_group['profs']['proflist_string']." - ".$current_group['description']." en ".$current_group['classlist_string'];

	$pdf->SetXY($x0,$y0);

	$texte=$texte_titre;
	$largeur_dispo=$largeur_page-$marge_gauche-$marge_droite;
	$hauteur_caractere=12;
	$h_ligne=$h_ligne_titre_tableau;
	$graisse='B';
	$alignement='C';
	$bordure='';
	cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$hauteur_caractere,$fonte,$graisse,$alignement,$bordure);
	$y2=$y0+$h_ligne_titre_tableau;


	//===========================
	// Ligne d'entête du tableau
	//$pdf->SetXY($x0,$y0);
	$pdf->SetXY($x0,$y2);
	$largeur_dispo=$largeur_col_nom_ele;
	$texte=$ligne1_csv[1];

	$graisse='B';
	//$alignement='L';
	$alignement='C';
	$bordure='LRBT';
	cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$fonte,$graisse,$alignement,$bordure);

	$alignement='C';
	$largeur_dispo=$largeur_col_nom_ele;
	$x2=$x0+$largeur_col_nom_ele;
	for($i=2;$i<=count($ligne1_csv);$i++) {
		$pdf->SetXY($x2, $y2);
		$largeur_dispo=$largeur_col[$i];

		$texte=" ".$ligne1_csv[$i]." ";
		//cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$taille_min_police,'LRBT');
		cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$fonte,$graisse,$alignement,$bordure);

		$x2+=$largeur_dispo;
	}
	//===========================

	$graisse='';
	$alignement='C';
	$bordure='LRBT';

	$y2=$y2+$h_ligne_titre_tableau;
	$k=1;
	for($j=0;$j<count($lignes_csv);$j++) {
		$tab=explode(";", $lignes_csv[$j]);

		//if($y0+$k*$h_cell>$hauteur_page-5-$h_cell) {
		if($y2+$h_ligne>$hauteur_page-$marge_basse) {
			$pdf->AddPage();

			//===========================
			// Ligne d'entête du tableau
			$pdf->SetXY($x0,$y0);
			$largeur_dispo=$largeur_col_nom_ele;
			$texte=$ligne1_csv[1];

			//cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$taille_min_police,'LRBT');
			$graisse='B';
			//$alignement='L';
			$alignement='C';
			$bordure='LRBT';

			//$pdf->SetFont($fonte,$graisse,$hauteur_caractere);

			cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
		
			$alignement='C';
			$largeur_dispo=$largeur_col_nom_ele;
			$x2=$x0+$largeur_col_nom_ele;
			$y2=$y0;
			for($i=2;$i<=count($ligne1_csv);$i++) {
				$pdf->SetXY($x2, $y2);
				$largeur_dispo=$largeur_col[$i];
		
				$texte=" ".$ligne1_csv[$i]." ";
				//$texte=$ligne1_csv[$i];
				//cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$taille_min_police,'LRBT');
				cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
		
				$x2+=$largeur_dispo;
			}
			//===========================

			$y2=$y0+$h_ligne_titre_tableau;

			$k=1;

			$graisse='';
			$alignement='C';

		}
		$x2=$x0;

		if(($tab[0]!='Moyenne')&&($tab[0]!='Min.')&&($tab[0]!='Max.')&&($tab[0]!='Quartile 1')&&($tab[0]!='Médiane')&&($tab[0]!='Quartile 3')) {
			$h_ligne=$h_cell;
			$graisse="";
		}
		else {
			$h_ligne=$h_ligne_titre_tableau;
			$graisse="B";
		}

		for($i=1;$i<=count($ligne1_csv);$i++) {
			$pdf->SetXY($x2, $y2);

			$largeur_dispo=$largeur_col[$i];
			//$texte=$tab[$i-1];
			//$texte=stripslashes(preg_replace("/\\\\r\\\\n/","\r\n",$tab[$i-1]));
			$texte=preg_replace("/\\\\r/","\r",$tab[$i-1]);
			$texte=preg_replace("/\\\\n/","\n",$texte);
			$texte=stripslashes($texte);
			if(preg_match("/^App/", $ligne1_csv[$i])) {
				cell_ajustee(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$taille_min_police,'LRBT');
			}
			else {
				cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
			}
			$x2+=$largeur_dispo;
		}
		$y2+=$h_ligne;

		$k++;
	}

	$pref_output_mode_pdf=get_output_mode_pdf();

	send_file_download_headers('application/pdf',$nom_fic);
	$pdf->Output($nom_fic,$pref_output_mode_pdf);
	die();
}
//=====================================================




include "../lib/periodes.inc.php";
//**************** EN-TETE *****************
if ($en_tete == "yes") $titre_page = "Visualisation des moyennes et appréciations";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

if (isset($_SESSION['chemin_retour'])) {$retour = $_SESSION['chemin_retour'];} else {$retour = "index1.php";}

if ($en_tete!="yes"){
	echo "<script type='text/javascript'>
	document.body.style.backgroundColor='white';
</script>\n";
}

if($_SESSION['statut']=='professeur'){
	//++++++++++++++++++++++++++++++++++++++++++++++++++
	// A FAIRE
	// TEST: Est-ce que le PP a accès aux appréciations ?
	//       Créer un droit dans Droits d'accès ?
	if(getSettingValue("GepiAccesBulletinSimplePP")=='yes') {
		$acces_pp="y";
	}
	//++++++++++++++++++++++++++++++++++++++++++++++++++
}

if (!$current_group) {
    unset($_SESSION['chemin_retour']);
    echo "<p class='bold'><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
    echo "<p>Votre choix :</p>\n";
    //$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
    //$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	if($_SESSION['statut']=='scolarite'){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	elseif($_SESSION['statut']=='professeur'){
		$appel_donnees=mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe");
	}
	elseif($_SESSION['statut']=='cpe'){
		$appel_donnees=mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe");
	}
	elseif($_SESSION['statut']=='secours'){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c ORDER BY classe");
	}

	$lignes = mysql_num_rows($appel_donnees);
	//echo "\$lignes=$lignes<br />";

	if($lignes==0) {
		echo "<p>Aucune classe ne vous est attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else {
		$nb_class_par_colonne=round($lignes/3);
		/*
		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='center'>\n";
			echo "<td>\n";
		*/

		echo "<table border='0'>\n";

		$i = 0;
		while($i < $lignes) {
		/*
		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			echo "<td>\n";
		}
		*/

			$id_classe = mysql_result($appel_donnees, $i, "id");
			$aff_class = 'no';
			$groups = get_groups_for_class($id_classe,"","n");
			//echo "\$id_classe=$id_classe et count(\$groups)=".count($groups)."<br />";

			foreach($groups as $group) {
				$temoin_pp="no";
				$flag2 = "no";
				//if ($_SESSION['statut']!='scolarite') {
				// Seuls les comptes scolarite, professeur et secours ont accès à cette page.
				if ($_SESSION['statut']=='professeur') {
					$test = mysql_query("SELECT count(*) FROM j_groupes_professeurs
					WHERE (id_groupe='" . $group["id"]."' and login = '" . $_SESSION["login"] . "')");
					if (mysql_result($test, 0) == 1) {$flag2 = 'yes';}

					if($acces_pp=="y") {
						$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_classes jec WHERE jec.login=jep.login AND jec.id_classe=jep.id_classe AND jep.professeur='".$_SESSION['login']."' AND jec.id_classe='$id_classe';";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0) {
							$flag2 = 'yes';
							$temoin_pp="yes";
						}
					}
				} else {
					$flag2 = 'yes';
				}

				if ($flag2 == "yes") {
					$display_class = mysql_result($appel_donnees, $i, "classe");
					if ($aff_class == 'no') {
						echo "<tr>\n";
						echo "<td valign='top'>\n";
						echo "<span class='norme'>";
						echo "<b>$display_class</b> : ";
						echo "</span>";
						echo "</td>\n";
						echo "<td>\n";

						$aff_class = 'yes';
					}

					echo "<span class='norme'>";
					echo "<a href='index1.php?id_groupe=" . $group["id"] . "'>" . htmlspecialchars($group["description"]) . " </a>\n";

					// pas de nom si c'est un prof qui demande la page.
					//if ($_SESSION['statut']!='professeur') {
					if(($_SESSION['statut']!='professeur')||($temoin_pp=="yes")) {
						$id_groupe_en_cours = $group["id"];
						//recherche profs du groupe
						$sql_prof_groupe = "SELECT jgp.login,u.nom,u.prenom FROM j_groupes_professeurs jgp,utilisateurs u WHERE jgp.id_groupe='$id_groupe_en_cours' AND u.login=jgp.login";
						$result_prof_groupe=mysql_query($sql_prof_groupe);
						echo "(";
						$cpt=0;
						$nb_profs = mysql_num_rows($result_prof_groupe);
						while($lig_prof=mysql_fetch_object($result_prof_groupe)){
							if (($nb_profs !=1) AND ($cpt<$nb_profs-1)){
							echo "$lig_prof->nom ".ucfirst(mb_strtolower($lig_prof->prenom))." - ";
							} else {
							echo "$lig_prof->nom ".ucfirst(mb_strtolower($lig_prof->prenom));
							}
							$cpt++;
						}
						echo ")";
					}

					echo "</span>\n";
					echo "<br />\n";
				}
			}
			//if ($flag2 == 'yes') {echo "</span><br /><br />\n";}
			//if ($flag2 == 'yes') {echo "<br /><br />\n";}
			if ($flag2 == 'yes') {echo "</td></tr>\n";}
			$i++;
		}
		echo "</table>\n";
	}
        /*
	echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
	*/

} else if (!isset($choix_visu)) {
	echo "<form enctype=\"multipart/form-data\" name= \"form1\" action=\"".$_SERVER['PHP_SELF']."\" method=\"get\">\n";
    echo "<p class='bold'>\n";
	echo "<a href=\"".$retour."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

	if(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='secours')) {
		if($_SESSION['statut']=='professeur') {
			$login_prof_groupe_courant=$_SESSION["login"];
		}
		else {
			$tmp_current_group=get_group($id_groupe);

			$login_prof_groupe_courant=$tmp_current_group["profs"]["list"][0];
		}

		$tab_groups = get_groups_for_prof($login_prof_groupe_courant,"classe puis matière");

		if(!empty($tab_groups)) {

			$chaine_options_classes="";

			$num_groupe=-1;
			$nb_groupes_suivies=count($tab_groups);

			$id_grp_prec=0;
			$id_grp_suiv=0;
			$temoin_tmp=0;
			for($loop=0;$loop<count($tab_groups);$loop++) {
				if($tab_groups[$loop]['id']==$id_groupe){
					$num_groupe=$loop;

					$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";

					$temoin_tmp=1;
					if(isset($tab_groups[$loop+1])){
						$id_grp_suiv=$tab_groups[$loop+1]['id'];
					}
					else{
						$id_grp_suiv=0;
					}
				}
				else {
					$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
				}

				if($temoin_tmp==0){
					$id_grp_prec=$tab_groups[$loop]['id'];
				}
			}
			// =================================
			if(($chaine_options_classes!="")&&($nb_groupes_suivies>1)) {
				echo " | <select name='id_groupe' onchange=\"document.forms['form1'].submit();\">\n";
				echo $chaine_options_classes;
				echo "</select>\n";
			}
		}
		// =================================
	}

	echo "</p>\n";
	echo "</form>\n";

	$test_acces_pp="n";
	if(($_SESSION['statut']=='professeur')&&($acces_pp=="y")) {
		$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_groupes jeg WHERE jeg.login=jep.login AND jep.professeur='".$_SESSION['login']."' AND jeg.id_groupe='$id_groupe';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$test_acces_pp="y";
		}
	}

    if ((!(check_prof_groupe($_SESSION['login'],$id_groupe))) and ($_SESSION['statut']!='scolarite') and ($_SESSION['statut']!='secours') and ($test_acces_pp=="n")) {
        echo "<p>Vous n'êtes pas dans cette classe le professeur de la matière choisie !</p>\n";
        echo "<p><a href='index1.php'>Retour à l'accueil</a></p>\n";
        die();
    }

    echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire\">";
    echo "<p class='bold'>Groupe : " . htmlspecialchars($current_group["description"]) ." " . htmlspecialchars($current_group["classlist_string"]) . " | Matière : " . htmlspecialchars($current_group["matiere"]["nom_complet"]) . "&nbsp;&nbsp;<input type='submit' value='Valider' /></p>\n";
    echo "<p>Choisissez les données à imprimer (<i>vous pouvez cocher plusieurs cases</i>) : </p>\n";
    $i="1";
	$cpt=0;
    while ($i < $nb_periode) {
		$name = "visu_note_".$i;
		echo "<p><input type='checkbox' name='$name' id='$name' value='yes' ";
		if((isset($_SESSION[$name]))&&($_SESSION[$name]=='yes')) {echo "checked "; $temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
		echo "onchange=\"checkbox_change(this.id, $cpt)\" ";
		echo "/><label for='$name' style='cursor: pointer;'><span id='champ_numero_$cpt'$temp_style>".ucfirst($nom_periode[$i])." - Extraire les moyennes</span></label></p>\n";
		$i++;
		$cpt++;
    }
    $i="1";
    while ($i < $nb_periode) {
		$name = "visu_app_".$i;
		echo "<p><input type='checkbox' name='$name' id='$name' value='yes' ";
		if((isset($_SESSION[$name]))&&($_SESSION[$name]=='yes')) {echo "checked "; $temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
		echo "onchange=\"checkbox_change(this.id, $cpt)\" ";
		echo "/><label for='$name' style='cursor: pointer;'><span id='champ_numero_$cpt'$temp_style>".ucfirst($nom_periode[$i])." - Extraire les appréciations</span></label></p>\n";
	    $i++;
		$cpt++;
    }

	//==========================================
	// Le rang doit-il être affiché
	// On autorise le prof à obtenir les rangs même si on ne les met pas sur le bulletin
	// Et le calcul des rangs est effectué après soumission du formulaire si l'option rang est cochée
	$aff_rang="y";
	$affiche_categories="n";
	if($aff_rang=="y") {
		$name="vmm_afficher_rang";
	    echo "<p><input type='checkbox' name='afficher_rang' id='afficher_rang' value='yes' ";
		if((isset($_SESSION[$name]))&&($_SESSION[$name]=='yes')) {echo "checked "; $temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
		echo "onchange=\"checkbox_change(this.id, $cpt)\" ";
		echo "/><label for='afficher_rang' style='cursor: pointer;'><span id='champ_numero_$cpt'$temp_style>Afficher le rang des élèves.</span></label></p>\n";
		$cpt++;
	}
	//==========================================
	$name="vmm_afficher_mediane";
	echo "<p><input type='checkbox' name='afficher_mediane' id='afficher_mediane' value='yes' ";
	if((isset($_SESSION[$name]))&&($_SESSION[$name]=='yes')) {echo "checked "; $temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
	echo "onchange=\"checkbox_change(this.id, $cpt)\" ";
	echo "/><label for='afficher_mediane' style='cursor: pointer;'><span id='champ_numero_$cpt'$temp_style>Afficher les médiane, 1er et 3ème quartiles pour chaque colonne de note.</span></label></p>\n";
	$cpt++;
	//==========================================

	$name="vmm_stat";
    echo "<p><input type='checkbox' name='stat' id='stat' value='yes' ";
	if((isset($_SESSION[$name]))&&($_SESSION[$name]=='yes')) {echo "checked "; $temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
	echo "onchange=\"checkbox_change(this.id, $cpt)\" ";
	echo "/><label for='stat' style='cursor: pointer;'><span id='champ_numero_$cpt'$temp_style>Afficher les statistiques sur les moyennes extraites (<i>moyenne générale, pourcentages,...</i>)</span></label></p>\n";
	$cpt++;

    if ($multiclasses) {
        echo "<p><input type='radio' name='order_by' id='order_by_nom' value='nom' checked /><label for='order_by_nom' style='cursor: pointer;'> Classer les élèves par ordre alphabétique </label>";
        echo "<br/><input type='radio' name='order_by' id='order_by_classe' value='classe' /><label for='order_by_classe' style='cursor: pointer;'> Classer les élèves par classe</label></p>";
    }

	$name="vmm_couleur_alterne";
    echo "<p><input type='checkbox' name='couleur_alterne' id='couleur_alterne' value='y' ";
	if((isset($_SESSION[$name]))&&($_SESSION[$name]=='y')) {echo "checked "; $temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
	echo "onchange=\"checkbox_change(this.id, $cpt)\" ";
	echo "/><label for='couleur_alterne' style='cursor: pointer;'><span id='champ_numero_$cpt'$temp_style>Colorer les lignes (<i>alterner les couleurs de fond</i>).</span></label></p>\n";
	$cpt++;

    echo "<input type='submit' value='Valider' />\n";
    echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
    echo "<input type='hidden' name='choix_visu' value='yes' />\n";
    echo "</form>\n";

	echo "<script type='text/javascript'>
function checkbox_change(champ, cpt) {
	if(document.getElementById(champ)) {
		if(document.getElementById(champ).checked) {
			document.getElementById('champ_numero_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('champ_numero_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";


} else {


	$test_acces_pp="n";
	if(($_SESSION['statut']=='professeur')&&($acces_pp=="y")) {
		$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_groupes jeg WHERE jeg.login=jep.login AND jep.professeur='".$_SESSION['login']."' AND jeg.id_groupe='$id_groupe';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$test_acces_pp="y";
		}
	}

    if ((!(check_prof_groupe($_SESSION['login'],$id_groupe))) and ($_SESSION['statut']!='scolarite') and ($_SESSION['statut']!='secours') and ($test_acces_pp=="n")) {
        echo "<p>Vous n'êtes pas dans cette classe le professeur de la matière choisie !</p>\n";
        echo "<p><a href='index1.php'>Retour à l'accueil</a></p>\n";
        die();
    }


    $nombre_eleves = count($current_group["eleves"]["all"]["list"]);
	//echo "\$nombre_eleves=$nombre_eleves<br />";

    // On commence par mettre la liste dans l'ordre souhaité
    if ($order_by != "classe") {
        $liste_eleves = $current_group["eleves"]["all"]["list"];
    } else {
        // Ici, on trie par classe
        // On va juste créer une liste des élèves pour chaque classe
        $tab_classes = array();
        foreach($current_group["classes"]["list"] as $classe_id) {
            $tab_classes[$classe_id] = array();
        }
        // On passe maintenant élève par élève et on les met dans la bonne liste selon leur classe
        foreach($current_group["eleves"]["all"]["list"] as $eleve_login) {
            $classe = $current_group["eleves"]["all"]["users"][$eleve_login]["classe"];
            $tab_classes[$classe][] = $eleve_login;
			//echo "$eleve_login ";
        }
		//echo "<br />";
        // On met tout ça à la suite
        $liste_eleves = array();
        foreach($current_group["classes"]["list"] as $classe_id) {
            $liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
        }
    }

	//debug_var();

	//==========================================
	// MODIF: boireaus 20080407
	$nb_eleves_avant_include=$nombre_eleves;
	// Le rang doit-il être affiché
	// On autorise le prof à obtenir les rangs même si on ne les met pas sur le bulletin
	$aff_rang="n";
	//if((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes")) {
	if(((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes"))||((isset($_GET['afficher_rang']))&&($_GET['afficher_rang']=="yes"))) {
		$aff_rang="y";
		$affiche_categories="n";
		for($i=0;$i<count($current_group["classes"]["list"]);$i++) {
			/*
			$sql="SELECT display_rang FROM classes WHERE id='".$current_group["classes"]["list"][$i]."';";
			$test_rang=mysql_query($sql);
			$lig_rang=mysql_fetch_object($test_rang);
			if($lig_rang->display_rang=="y") {
			*/
				$id_classe=$current_group["classes"]["list"][$i];

				$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));

				$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe';";
				$res_per=mysql_query($sql);
				while($lig_per=mysql_fetch_object($res_per)) {
					$periode_num=$lig_per->num_periode;
					include("../lib/calcul_rang.inc.php");
				}
			/*
			}
			else {
				$aff_rang="n";
				break;
			}
			*/
		}
	}
	$nombre_eleves=$nb_eleves_avant_include;
	//echo "\$nombre_eleves=$nombre_eleves<br />";

	//==========================================


    $nb_col = 1;
    if ($multiclasses) $nb_col++;

	//==========================================
	// MODIF: boireaus 20080407
	//if ($aff_rang=="y") {$nb_col++;}
	//==========================================

    $total_notes = 0;
    $min_notes = '-';
    $max_notes = '-';
    $pourcent_i8 = 0;
    $pourcent_se12 = 0;
    $pourcent_se8_ie12 = 0;
    $i = "0";
    $eleve_login = null;
    foreach($liste_eleves as $eleve_login) {
		//echo "$eleve_login ";
        // La variable affiche_ligne teste si on affiche une ligne ou non : si l'élève suit la matière pour au moins une période, on affiche la ligne concernant l'élève. Si l'élève ne suit pas la matière pour aucune des périodes, on n'affiche pas la ligne conernant l'élève.
        $affiche_ligne[$i] = 'no';
        $login_eleve[$i] = $eleve_login;
        $k=0;
		//echo "\$nb_periode=$nb_periode<br />";
        while ($k < $nb_periode) {
            $temp1 = "visu_note_".$k;
            $temp2 = "visu_app_".$k;
            if (isset($_POST[$temp1]) or isset($_POST[$temp2]) or isset($_GET[$temp1]) or isset($_GET[$temp2])) {

                if (!in_array($eleve_login, $current_group["eleves"][$k]["list"])) {
                    $option[$i][$k] = "non";
                } else {
                    $option[$i][$k] = "oui";
                    $affiche_ligne[$i] = 'yes';
                }
				//echo "\$option[$i][$k]=".$option[$i][$k]."<br />";
            }
            $k++;
        }
		//echo "$eleve_login : \$affiche_ligne[$i]=$affiche_ligne[$i]<br />";
        $i++;
    }
    //
    // Calcul du nombre de colonnes à afficher et définition de la première ligne à afficher
    //
    $ligne1[1] = "Nom Prénom";
    $ligne1_csv[1] = "Nom Prénom";
    if ($multiclasses) {
		$ligne1[2] = "Classe";
		$ligne1_csv[2] = "Classe";
    }
	$k = 1;
//    if (isset($_POST['stat']) or isset($_GET['stat'])) $only_stats = 'yes'; else $only_stats = 'no';
    while ($k < $nb_periode) {

		//==========================================
		// MODIF: boireaus 20080407
		if ($aff_rang=="y") {
			$temoin_periode=0;

			$temp = "visu_note_".$k;
			if (isset($_POST[$temp]) or isset($_GET[$temp])) {
				$temoin_periode++;
			}

			$temp = "visu_app_".$k;
			if (isset($_POST[$temp]) or isset($_GET[$temp])) {
				$temoin_periode++;
			}

			if($temoin_periode>0) {
				$nb_col++;
				$ligne1[$nb_col] = "Rang P".$k;
				$ligne1_csv[$nb_col] = "Rang P".$k;
			}
		}
		//==========================================

        $temp = "visu_note_".$k;
        if (isset($_POST[$temp]) or isset($_GET[$temp])) {
            $nb_col++;
//            $only_stats = 'no';
            $ligne1[$nb_col] = "Note P".$k;
            $ligne1_csv[$nb_col] = "Note P".$k;
        }
        $temp = "visu_app_".$k;
        if (isset($_POST[$temp]) or isset($_GET[$temp])) {
            $nb_col++;
            $ligne1[$nb_col] = "Appréciation P".$k;
            $ligne1_csv[$nb_col] = "Appréciation P".$k;
        }
        $k++;
    }
    if ($stat == "yes") {
        $nb_col++;
        $ligne1[$nb_col] = "Moyenne";
        $ligne1_csv[$nb_col] = "Moyenne";
		/*
		if($aff_rang=="y") {
			$nb_col++;
			$ligne1[$nb_col] = "Rang";
			$ligne1_csv[$nb_col] = "Rang";
		}
		*/
    }
    $i = 0;
    $nb_lignes = '0';
    $nb_notes = '0';
	$rg=array();
	//echo "\$nombre_eleves=$nombre_eleves<br />\n";
    while($i < $nombre_eleves) {
		//echo "\$login_eleve[$i]=$login_eleve[$i] et \$affiche_ligne[$i]=$affiche_ligne[$i]<br />";
        if ($affiche_ligne[$i] == 'yes') {
            // Calcul de la moyenne
            if ($stat == "yes") {
				$col[$nb_col][$nb_lignes] = '';
				$col_csv[$nb_col][$nb_lignes] = '';
			}
            for ($k=1;$k<$nb_periode;$k++) {
                if (in_array($login_eleve[$i], $current_group["eleves"][$k]["list"])) {
                    $col[1][$nb_lignes] = $current_group["eleves"][$k]["users"][$login_eleve[$i]]["prenom"] . " " . $current_group["eleves"][$k]["users"][$login_eleve[$i]]["nom"];
                    $col_csv[1][$nb_lignes] = $current_group["eleves"][$k]["users"][$login_eleve[$i]]["prenom"] . " " . $current_group["eleves"][$k]["users"][$login_eleve[$i]]["nom"];
                    if ($multiclasses) $col[2][$nb_lignes] = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$login_eleve[$i]]["classe"]]["classe"];
                    if ($multiclasses) $col_csv[2][$nb_lignes] = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$login_eleve[$i]]["classe"]]["classe"];


					//echo "<p>\$col_csv[1][$nb_lignes]=".$col_csv[1][$nb_lignes]."<br />";

                    break;
                }
            }

            $k=1;
            $j=1;
            if ($multiclasses) $j++;
            while ($k < $nb_periode) {

				//==========================================
				// MODIF: boireaus 20080407
				if ($aff_rang=="y") {
					$temoin_periode=0;

					$temp = "visu_note_".$k;
					if (isset($_POST[$temp]) or isset($_GET[$temp])) {
						$temoin_periode++;
					}

					$temp = "visu_app_".$k;
					if (isset($_POST[$temp]) or isset($_GET[$temp])) {
						$temoin_periode++;
					}

					if($temoin_periode>0) {
						$j++;
						$note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$login_eleve[$i]' AND id_groupe = '".$current_group["id"] . "' AND periode='$k')");
						if($note_query) {
							$_statut = @mysql_result($note_query, 0, "statut");
							//$note = @mysql_result($note_query, 0, "note");
							$rang = @mysql_result($note_query, 0, "rang");
							if ($option[$i][$k] == "non") {
								//$col[$j][$nb_lignes] = "-";
								//$col[$j][$nb_lignes] = "<span style='text-align:center;'>-</span>";
								$col[$j][$nb_lignes] = "<center>-</center>";
								$col_csv[$j][$nb_lignes] = "-";
							} else {
								//$col[$j][$nb_lignes] = $rang;
								//$col[$j][$nb_lignes] = "<span style='text-align:center;'>".$rang."</span>";
								if($rang!="") {
									$col[$j][$nb_lignes] = "<center>".$rang."</center>";
									$col_csv[$j][$nb_lignes] = $rang;
								}
								else {
									$col[$j][$nb_lignes] = "<center>-</center>";
									$col_csv[$j][$nb_lignes] = "-";
								}
							}
						}
						else {
							$col[$j][$nb_lignes] = "<center>-</center>";
							$col_csv[$j][$nb_lignes] = "-";
						}
					}
				}
				//==========================================

                $temp = "visu_note_".$k;
                if (isset($_POST[$temp]) or isset($_GET[$temp])) {
                    $j++;
                    $note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$login_eleve[$i]' AND id_groupe = '".$current_group["id"] . "' AND periode='$k')");
                    $_statut = @mysql_result($note_query, 0, "statut");
                    $note = @mysql_result($note_query, 0, "note");
                    if ($option[$i][$k] == "non") {
                        $col[$j][$nb_lignes] = "<center>-</center>";
						$col_csv[$j][$nb_lignes] = "-";
                    } else {
                        if ($_statut != '') {
                            //$col[$j][$nb_lignes] = $_statut;
                            $col[$j][$nb_lignes] = "<center>".$_statut."</center>";
							$col_csv[$j][$nb_lignes] = $_statut;
						} else {
                            if ($note != '') {
                                //$col[$j][$nb_lignes] = number_format($note,1,',','');
								$col[$j][$nb_lignes] = "<center>".number_format($note,1,',','')."</center>";
								$col_csv[$j][$nb_lignes] = number_format($note,1,',','');
                                if ($stat == "yes") {
                                    $col[$nb_col][$nb_lignes] += $note;
                                    if (!isset($nb_note[$nb_lignes])) $nb_note[$nb_lignes]=1; else $nb_note[$nb_lignes]++;
                                }
                            } else {
                                //$col[$j][$nb_lignes] = '-';
								$col[$j][$nb_lignes] = "<center>-</center>";
								$col_csv[$j][$nb_lignes] = "-";
                            }
                        }
                    }

					//echo "\$col_csv[$j][$nb_lignes]=".$col_csv[$j][$nb_lignes]."<br />";

                }
                $temp = "visu_app_".$k;
                if (isset($_POST[$temp]) or isset($_GET[$temp])) {

                    $j++;
                    $app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$login_eleve[$i]' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
                    $app = @mysql_result($app_query, 0, "appreciation");

					//++++++++++++++++++++++++
					// Modif d'après F.Boisson
					// notes dans appreciation
					$sql="SELECT cnd.note, cd.note_sur FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE cnd.login='".$login_eleve[$i]."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group["id"]."' AND ccn.periode='$k' AND cnd.statut='';";
					$result_nbct=mysql_query($sql);
					$string_notes='';
					if ($result_nbct ) {
						while ($snnote =  mysql_fetch_assoc($result_nbct)) {
							if ($string_notes != '') $string_notes .= ", ";
							$string_notes .= $snnote['note'];
							if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $snnote['note_sur']!=getSettingValue("referentiel_note")) {
								$string_notes .= "/".$snnote['note_sur'];
							}
						}
					}
					$app = str_replace('@@Notes', $string_notes,$app);
					//++++++++++++++++++++++++


                    if ($app != '') {
						// =========================================
						// MODIF: boireaus
									//$col[$j][$nb_lignes] = $app;
						if((strstr($app,">"))||(strstr($app,"<"))){
							$col[$j][$nb_lignes] = $app;
						}
						else{
							$col[$j][$nb_lignes] = nl2br($app);
						}
						$col_csv[$j][$nb_lignes] = $app;
						// =========================================
                    } else {
                        $col[$j][$nb_lignes] = '-';
						$col_csv[$j][$nb_lignes] = "-";
                    }
                }
                $k++;
            }
            if ($stat == "yes") {
                if ($col[$nb_col][$nb_lignes] != '') {
                    // moyenne de chaque élève
                    $temp = round($col[$nb_col][$nb_lignes]/$nb_note[$nb_lignes],1);
                    $col[$nb_col][$nb_lignes] = "<center>".number_format($temp,1,',','')."</center>";
					$col_csv[$nb_col][$nb_lignes] = number_format($temp,1,',','');
                    // Total des moyennes de chaque élève
                    $total_notes += $temp;
                    $nb_notes++;
                    if ($min_notes== '-') $min_notes = 20;
                    $min_notes = min($min_notes,$temp);
                    $max_notes = max($max_notes,$temp);
                    if ($temp < 8) $pourcent_i8++;
                    if ($temp >= 12) $pourcent_se12++;
                } else {
                    //$col[$nb_col][$nb_lignes] = '-' ;
                    $col[$nb_col][$nb_lignes] = '<center>-</center>' ;
                    $col_csv[$nb_col][$nb_lignes] = '-' ;
                }
            }
            $nb_lignes++;

			//$rg[]=$i;
        }

		$rg[$i]=$i;

        $i++;
    }

	//==============================================
	// AJOUT: boireaus 20080418
	// Calculs pour la colonne RANG sur la moyenne des périodes
	if(($stat=="yes")&&($aff_rang=="y")) {
		//$tmp_tab=$col[$nb_col];
		//for($loop=0;$loop<count($tmp_tab);$loop++) {
			//echo "\$tmp_tab[$loop]=".$tmp_tab[$loop]."<br />";

		unset($rg);

		for($loop=0;$loop<count($col[$nb_col]);$loop++) {
			//$tmp_tab[$loop]=$col[$nb_col][$loop];
			//$tmp_tab[$loop]=$col_csv[$nb_col][$loop];
			$tmp_tab[$loop]=preg_replace("/,/",".",$col_csv[$nb_col][$loop]);
			//echo "\$tmp_tab[$loop]=".$tmp_tab[$loop]."<br />";

			$rg[$loop]=$loop;
		}

		array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);
		/*
		echo "count(\$rg)=".count($rg)."<br />";
		for($loop=0;$loop<count($rg);$loop++) {
			echo "\$rg[$loop]=".$rg[$loop]."<br />";
		}
		*/

		$i=0;
		$rang_prec = 1;
		$note_prec='';
		//while ($i < $nombre_eleves) {
		while($i < count($col_csv[1])) {
			$ind = $rg[$i];
			if ($tmp_tab[$i] == "-") {
				//$rang_gen = '0';
				$rang_gen = '-';
			}
			else {
				if ($tmp_tab[$i] == $note_prec) {
					$rang_gen = $rang_prec;
				}
				else {
					$rang_gen = $i+1;
				}
				$note_prec = $tmp_tab[$i];
				$rang_prec = $rang_gen;
			}

			//$col[$nb_col+1][$ind]="ind=$ind, i=$i et rang_gen=$rang_gen";
			$col[$nb_col+1][$ind]="<center>".$rang_gen."</center>";
			$col_csv[$nb_col+1][$ind]=$rang_gen;

			$i++;
		}
	}
	//==============================================


	//=========================
	// MODIF: boireaus 20080421
	// Pour permettre de trier autrement...
	if((isset($_POST['col_tri']))&&($_POST['col_tri']!='')) {
		//echo "\$_POST['col_tri']=".$_POST['col_tri']."<br />";
		$col_tri=$_POST['col_tri'];

		$nb_colonnes=$nb_col;
		if(($stat=="yes")&&($aff_rang=="y")) {
			$nb_colonnes=$nb_col+1;
		}

		// Vérifier si $col_tri est bien un entier compris entre 0 et $nb_col ou $nb_col+1
		if((mb_strlen(preg_replace("/[0-9]/","",$col_tri))==0)&&($col_tri>0)&&($col_tri<=$nb_colonnes)) {
			//echo "<table>";
			//echo "<tr><td valign='top'>";
			unset($tmp_tab);
			//for($loop=0;$loop<count($col[$col_tri]);$loop++) {
			//echo "\$_POST['col_tri']=".$_POST['col_tri']."<br />";
			//echo "\$nb_col=".$nb_col."<br />";
			for($loop=0;$loop<count($col_csv[1]);$loop++) {
				// Il faut le POINT au lieu de la VIRGULE pour obtenir un tri correct sur les notes
				$tmp_tab[$loop]=preg_replace("/,/",".",$col_csv[$col_tri][$loop]);

				// La colonne Rang sur la moyenne générale annuelle est ajoutée plus loin dans le code (c'est la seule)
				if(($_POST['col_tri']>$nb_col)||
					(preg_match('/^Rang/',$ligne1_csv[$_POST['col_tri']]))) {
					if($tmp_tab[$loop]=='-') {$tmp_tab[$loop]=1000000;}
				}

				//echo "\$tmp_tab[$loop]=".$tmp_tab[$loop]."<br />";
			}
			/*
			echo "</td>";
			echo "<td valign='top'>";

			$i=0;
			while($i < $nombre_eleves) {
				echo $col_csv[1][$i]."<br />";
				$i++;
			}
			echo "</td>";
			echo "<td valign='top'>";
			*/

			$i=0;
			unset($rg);
			//while($i < $nombre_eleves) {
			while($i < count($col_csv[1])) {
				$rg[$i]=$i;
				$i++;
			}

			//echo "count(\$rg)=".count($rg)."<br />";
			//echo "count(\$tmp_tab)=".count($tmp_tab)."<br />";
			// Tri du tableau avec stockage de l'ordre dans $rg d'après $tmp_tab
			array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);

			/*
			$i=0;
			while($i < $nombre_eleves) {
				echo "\$rg[$i]=".$rg[$i]."<br />";
				$i++;
			}
			echo "</td>";
			echo "<td valign='top'>";
			*/

			// On utilise des tableaux temporaires le temps de la réaffectation dans l'ordre
			$tmp_col=array();
			$tmp_col_csv=array();

			$i=0;
			$rang_prec = 1;
			$note_prec='';
			//while ($i < $nombre_eleves) {
			while ($i < count($col_csv[1])) {
				$ind = $rg[$i];
				if ($tmp_tab[$i] == "-") {
					//$rang_gen = '0';
					$rang_gen = '-';
				}
				else {
					if ($tmp_tab[$i] == $note_prec) {
						$rang_gen = $rang_prec;
					}
					else {
						$rang_gen = $i+1;
					}
					$note_prec = $tmp_tab[$i];
					$rang_prec = $rang_gen;
				}

				//$col[$nb_col+1][$ind]="ind=$ind, i=$i et rang_gen=$rang_gen";
				for($m=1;$m<=$nb_colonnes;$m++) {
					/*
					echo "\$tmp_col[$m][$ind]=\$col[$m][$i]=".$col[$m][$i]."<br />";
					$tmp_col[$m][$ind]="<center>".$col[$m][$i]."</center>";
					$tmp_col_csv[$m][$ind]=$rang_gen;
					*/
					//echo "\$tmp_col[$m][$i]=\$col[$m][$ind]=".$col[$m][$ind]."<br />";
					$tmp_col[$m][$i]=$col[$m][$ind];
					$tmp_col_csv[$m][$ind]=$col_csv[$m][$ind];

				}
				$i++;
			}
			//echo "</td></tr>";
			//echo "</table>";

			// On réaffecte les valeurs dans le tableau initial à l'aide du tableau temporaire
			if((isset($_POST['sens_tri']))&&($_POST['sens_tri']=="inverse")) {
				for($m=1;$m<=$nb_colonnes;$m++) {
					//for($i=0;$i<$nombre_eleves;$i++) {
					for($i=0;$i<count($col_csv[1]);$i++) {
						//$col[$m][$i]=$tmp_col[$m][$nombre_eleves-1-$i];
						//$col_csv[$m][$i]=$tmp_col_csv[$m][$nombre_eleves-1-$i];
						$col[$m][$i]=$tmp_col[$m][count($col_csv[1])-1-$i];
						$col_csv[$m][$i]=$tmp_col_csv[$m][count($col_csv[1])-1-$i];
					}
				}
			}
			else {
				for($m=1;$m<=$nb_colonnes;$m++) {
					$col[$m]=$tmp_col[$m];
					$col_csv[$m]=$tmp_col_csv[$m];
				}
			}
		}
	}
	//=========================


    //
    // On teste s'il y a des moyennes, min et max à calculer :
    //
    $k = 1;
    $test = 0;
    while ($k < $nb_periode) {
        $temp = "visu_note_".$k;
        if (isset($_POST[$temp]) or isset($_GET[$temp])) {$test = 1;}
        $k++;
    }
    //
    // S'il y a des moyennes, min et max à calculer, on le fait :
    //
    if ($test == 1) {
        $k=1;
        $j=1;
        if ($multiclasses) $j++;
        while ($k < $nb_periode) {
			if ($aff_rang=="y") {
				$temoin_periode=0;

				$temp = "visu_note_".$k;
				if (isset($_POST[$temp]) or isset($_GET[$temp])) {
					$temoin_periode++;
				}

				$temp = "visu_app_".$k;
				if (isset($_POST[$temp]) or isset($_GET[$temp])) {
					$temoin_periode++;
				}

				if($temoin_periode>0) {
					$j++;
                    //$col[$j][$nb_lignes] = '-';
					//$col[$j][$nb_lignes+1] = '-';
					//$col[$j][$nb_lignes+2] = '-';
					$col[$j][$nb_lignes] = "<center>-</center>";
					$col[$j][$nb_lignes+1] = "<center>-</center>";
					$col[$j][$nb_lignes+2] = "<center>-</center>";
					//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
					if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
						$col[$j][$nb_lignes+3] = "<center>-</center>";
						$col[$j][$nb_lignes+4] = "<center>-</center>";
						$col[$j][$nb_lignes+5] = "<center>-</center>";
					}
                    $col_csv[$j][$nb_lignes] = '-' ;
                    $col_csv[$j][$nb_lignes+1] = '-' ;
                    $col_csv[$j][$nb_lignes+2] = '-' ;
					//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
					if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
						$col_csv[$j][$nb_lignes+3] = '-' ;
						$col_csv[$j][$nb_lignes+4] = '-' ;
						$col_csv[$j][$nb_lignes+5] = '-' ;
					}
				}
			}

            $temp = "visu_note_".$k;
            if (isset($_POST[$temp]) or isset($_GET[$temp])) {
				$j++;

                $call_moyenne = mysql_query("SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (id_groupe='$id_groupe' AND statut ='' AND periode='$k')");
                $call_max = mysql_query("SELECT max(note) note_max FROM matieres_notes WHERE (id_groupe='$id_groupe' AND statut ='' AND periode='$k')");
                $call_min = mysql_query("SELECT min(note) note_min FROM matieres_notes WHERE (id_groupe='$id_groupe' AND statut ='' AND periode='$k')");
                $temp = @mysql_result($call_moyenne, 0, "moyenne");
                if ($temp != '') {
                    //$col[$j][$nb_lignes] = number_format($temp,1,',','');
					$col[$j][$nb_lignes] = "<center>".number_format($temp,1,',','')."</center>";
                    $col_csv[$j][$nb_lignes] = number_format($temp,1,',','') ;
                } else {
                    //$col[$j][$nb_lignes] = '-';
					$col[$j][$nb_lignes] = "<center>-</center>";
                    $col_csv[$j][$nb_lignes] = '-' ;
                }
                $temp = @mysql_result($call_min, 0, "note_min");
                if ($temp != '') {
                    //$col[$j][$nb_lignes+1] = number_format($temp,1,',','');
					$col[$j][$nb_lignes+1] = "<center>".number_format($temp,1,',','')."</center>";
                    $col_csv[$j][$nb_lignes+1] = number_format($temp,1,',','') ;
                } else {
                    //$col[$j][$nb_lignes+1] = '-';
					$col[$j][$nb_lignes+1] = "<center>-</center>";
                    $col_csv[$j][$nb_lignes+1] = '-' ;
                }
                $temp = @mysql_result($call_max, 0, "note_max");
                if ($temp != '') {
                    //$col[$j][$nb_lignes+2] = number_format($temp,1,',','');
					$col[$j][$nb_lignes+2] = "<center>".number_format($temp,1,',','')."</center>";
                    $col_csv[$j][$nb_lignes+2] = number_format($temp,1,',','') ;
                } else {
                    //$col[$j][$nb_lignes+2] = '-';
					$col[$j][$nb_lignes+2] = "<center>-</center>";
                    $col_csv[$j][$nb_lignes+2] = '-' ;
                }

				//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
				if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
					//echo "\$col[$j][0]=".$col[$j][0]."<br />";
					//echo "\$col[$j][1]=".$col[$j][1]."<br />";
					$tab_notes_ele=array();
					for($loop_ele=0;$loop_ele<$nb_lignes;$loop_ele++) {
						$tab_notes_ele[]=$col_csv[$j][$loop_ele];
					}
					$tab_stat=calcule_moy_mediane_quartiles($tab_notes_ele);
					/*
					echo "<pre>";
					foreach($tab_notes_ele as $key => $value) {
						echo "\$tab_notes_ele[$key]=$value<br />";
					}
					foreach($tab_stat as $key => $value) {
						echo "\$tab_stat[$key]=$value<br />";
					}
					echo "</pre>";
					*/
					//$tab_champs_stat=array('Quartile 1', 'Médiane', 'Quartile 3');
					$tab_code_champs_stat=array('q1', 'mediane', 'q3');
					for($loop_stat=0;$loop_stat<count($tab_code_champs_stat);$loop_stat++) {
						if(($tab_stat[$tab_code_champs_stat[$loop_stat]]!='')&&($tab_stat[$tab_code_champs_stat[$loop_stat]]!='-')) {
							$temp=$tab_stat[$tab_code_champs_stat[$loop_stat]];
							$col[$j][$nb_lignes+2+1+$loop_stat] = "<center>".number_format($temp,1,',','')."</center>";
							$col_csv[$j][$nb_lignes+2+1+$loop_stat] = number_format($temp,1,',','') ;
						}
						else {
							$col[$j][$nb_lignes+2+1+$loop_stat] = "<center>-</center>";
							$col_csv[$j][$nb_lignes+2+1+$loop_stat] = '-' ;
						}
					}
				}
            }
            $temp = "visu_app_".$k;
            if (isset($_POST[$temp]) or isset($_GET[$temp])) {
                $j++;
                $col[$j][$nb_lignes] = '-';
                $col[$j][$nb_lignes+1] = '-';
                $col[$j][$nb_lignes+2] = '-';
				//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
				if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
					$col[$j][$nb_lignes+3] = '-';
					$col[$j][$nb_lignes+4] = '-';
					$col[$j][$nb_lignes+5] = '-';
				}
                $col_csv[$j][$nb_lignes] = '-';
                $col_csv[$j][$nb_lignes+1] = '-';
                $col_csv[$j][$nb_lignes+2] = '-';
				//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
				if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
					$col_csv[$j][$nb_lignes+3] = '-';
					$col_csv[$j][$nb_lignes+4] = '-';
					$col_csv[$j][$nb_lignes+5] = '-';
				}
            }
            $k++;
        }
        if ($stat == "yes") {
            // moyenne générale de la classe
            if ($total_notes != 0) {
				$col[$nb_col][$nb_lignes] ="<center>".number_format(round($total_notes/$nb_notes,1),1,',','')."</center>";
				$col_csv[$nb_col][$nb_lignes] =number_format(round($total_notes/$nb_notes,1),1,',','');

				$col[$nb_col][$nb_lignes+1] = "<center>".number_format($min_notes,1,',','')."</center>";
				$col_csv[$nb_col][$nb_lignes+1] = number_format($min_notes,1,',','');
				$col[$nb_col][$nb_lignes+2] = "<center>".number_format($max_notes,1,',','')."</center>";
				$col_csv[$nb_col][$nb_lignes+2] = number_format($max_notes,1,',','');

				//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
				if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
					$tab_notes_ele=array();
					for($loop_ele=0;$loop_ele<$nb_lignes;$loop_ele++) {
						$tab_notes_ele[]=$col_csv[$nb_col][$loop_ele];
					}
					$tab_stat=calcule_moy_mediane_quartiles($tab_notes_ele);
					/*
					echo "<pre>";
					foreach($tab_notes_ele as $key => $value) {
						echo "\$tab_notes_ele[$key]=$value<br />";
					}
					foreach($tab_stat as $key => $value) {
						echo "\$tab_stat[$key]=$value<br />";
					}
					echo "</pre>";
					*/
					//$tab_champs_stat=array('Quartile 1', 'Médiane', 'Quartile 3');
					$tab_code_champs_stat=array('q1', 'mediane', 'q3');
					for($loop_stat=0;$loop_stat<count($tab_code_champs_stat);$loop_stat++) {
						if(($tab_stat[$tab_code_champs_stat[$loop_stat]]!='')&&($tab_stat[$tab_code_champs_stat[$loop_stat]]!='-')) {
							$temp=$tab_stat[$tab_code_champs_stat[$loop_stat]];
							$col[$nb_col][$nb_lignes+2+1+$loop_stat] = "<center>".number_format($temp,1,',','')."</center>";
							$col_csv[$nb_col][$nb_lignes+2+1+$loop_stat] = number_format($temp,1,',','') ;
						}
						else {
							$col[$nb_col][$nb_lignes+2+1+$loop_stat] = "<center>-</center>";
							$col_csv[$nb_col][$nb_lignes+2+1+$loop_stat] = '-' ;
						}
					}
				}
			}
			else {
				$col[$nb_col][$nb_lignes] = "<center>-</center>";
				$col_csv[$nb_col][$nb_lignes] = "-";

				$col[$nb_col][$nb_lignes+1] = "<center>-</center>";
				$col_csv[$nb_col][$nb_lignes+1] = "-";
				$col[$nb_col][$nb_lignes+2] = "<center>-</center>";
				$col_csv[$nb_col][$nb_lignes+2] = "-";

				//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
				if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
					$col[$nb_col][$nb_lignes+3] = "<center>-</center>";
					$col_csv[$nb_col][$nb_lignes+3] = "-";
					$col[$nb_col][$nb_lignes+4] = "<center>-</center>";
					$col_csv[$nb_col][$nb_lignes+4] = "-";
					$col[$nb_col][$nb_lignes+5] = "<center>-</center>";
					$col_csv[$nb_col][$nb_lignes+5] = "-";
				}
			}
			//$col_csv[$nb_col][$nb_lignes]=$col[$nb_col][$nb_lignes];

            //$moy_gen = $col[$nb_col][$nb_lignes];
            $moy_gen = $col_csv[$nb_col][$nb_lignes];

            //$col[$nb_col][$nb_lignes+1] = "<center>".$min_notes."</center>";
            //$col_csv[$nb_col][$nb_lignes+1] = $min_notes;
            //$col[$nb_col][$nb_lignes+2] = "<center>".$max_notes."</center>";
            //$col_csv[$nb_col][$nb_lignes+2] = $max_notes;

			if($nb_notes!=0){
				$pourcent_se8_ie12 = number_format(($nb_notes-$pourcent_se12-$pourcent_i8)*100/$nb_notes,1,',','');
				if ($pourcent_i8 != '-') $pourcent_i8 = number_format(round($pourcent_i8*100/$nb_notes,1),1,',','');
				if ($pourcent_se12 != '-') $pourcent_se12 = number_format(round($pourcent_se12*100/$nb_notes,1),1,',','');
			}
			else{
				$pourcent_se8_ie12="-";
				$pourcent_i8='-';
				$pourcent_se12='-';
			}
        }
    }
    if ($test == 1) {
        //$col[1][$nb_lignes] = '<center><b>Moyenne</b></center>';
        //$col[1][$nb_lignes+1] = '<center><b>Min.</b></center>';
        //$col[1][$nb_lignes+2] = '<center><b>Max.</b></center>';
        $col[1][$nb_lignes] = '<b>Moyenne</b>';
        $col[1][$nb_lignes+1] = '<b>Min.</b>';
        $col[1][$nb_lignes+2] = '<b>Max.</b>';
		//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
		if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
			$col[1][$nb_lignes+3] = '<b>Quartile 1</b>';
			$col[1][$nb_lignes+4] = '<b>Médiane</b>';
			$col[1][$nb_lignes+5] = '<b>Quartile 3</b>';
		}
        $col_csv[1][$nb_lignes] = 'Moyenne';
        $col_csv[1][$nb_lignes+1] = 'Min.';
        $col_csv[1][$nb_lignes+2] = 'Max.';
		//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
		if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
			$col_csv[1][$nb_lignes+3] = 'Quartile 1';
			$col_csv[1][$nb_lignes+4] = 'Médiane';
			$col_csv[1][$nb_lignes+5] = 'Quartile 3';
		}

        if ($multiclasses) {
            $col[2][$nb_lignes] = '&nbsp;';
            $col[2][$nb_lignes+1] = '&nbsp;';
            $col[2][$nb_lignes+2] = '&nbsp;';
			//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
			if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
				$col[2][$nb_lignes+3] = '&nbsp;';
				$col[2][$nb_lignes+4] = '&nbsp;';
				$col[2][$nb_lignes+5] = '&nbsp;';
			}

            $col_csv[2][$nb_lignes] = '';
            $col_csv[2][$nb_lignes+1] = '';
            $col_csv[2][$nb_lignes+2] = '';
			//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
			if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
				$col_csv[2][$nb_lignes+3] = '';
				$col_csv[2][$nb_lignes+4] = '';
				$col_csv[2][$nb_lignes+5] = '';
			}
        }
		//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
		if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
			$nb_lignes = $nb_lignes + 6;
		}
		else {
			$nb_lignes = $nb_lignes + 3;
		}
    }

	//==============================================
	// AJOUT: boireaus 20080418
	// Affichage de la colonne RANG sur la moyenne des périodes
	if(($stat=="yes")&&($aff_rang=="y")) {

		$nb_col++;
		$ligne1[$nb_col] = "Rang";
		$ligne1_csv[$nb_col] = "Rang";

        $col[$nb_col][$nb_lignes-1] = "<center>-</center>";
        $col[$nb_col][$nb_lignes-2] = "<center>-</center>";
        $col[$nb_col][$nb_lignes-3] = "<center>-</center>";
		//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
		if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
			$col[$nb_col][$nb_lignes-4] = "<center>-</center>";
			$col[$nb_col][$nb_lignes-5] = "<center>-</center>";
			$col[$nb_col][$nb_lignes-6] = "<center>-</center>";
		}
        $col_csv[$nb_col][$nb_lignes-1] = '-';
        $col_csv[$nb_col][$nb_lignes-2] = '-';
        $col_csv[$nb_col][$nb_lignes-3] = '-';
		//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
		if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
			$col_csv[$nb_col][$nb_lignes-4] = '-';
			$col_csv[$nb_col][$nb_lignes-5] = '-';
			$col_csv[$nb_col][$nb_lignes-6] = '-';
		}
	}
	//==============================================

    //
    // Affichage du tableau
    //
    if (!isset($larg_tab)) {$larg_tab = 680;}
    if (!isset($bord)) {$bord = 1;}
	//echo "\n<!-- Formulaire pour l'affichage sans entête -->\n";
    if ($en_tete == "yes") {

		echo "<form enctype=\"multipart/form-data\" name= \"form1\" action=\"".$_SERVER['PHP_SELF']."\" method=\"get\">\n";
		echo "<p class='bold'><a href=\"index1.php?id_groupe=$id_groupe\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

		if(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='secours')) {
			if($_SESSION['statut']=='professeur') {
				$login_prof_groupe_courant=$_SESSION["login"];
			}
			else {
				$tmp_current_group=get_group($id_groupe);

				$login_prof_groupe_courant=$tmp_current_group["profs"]["list"][0];
			}

			$tab_groups = get_groups_for_prof($login_prof_groupe_courant,"classe puis matière");

			if(!empty($tab_groups)) {

				$chaine_options_classes="";

				$num_groupe=-1;
				$nb_groupes_suivies=count($tab_groups);

				$id_grp_prec=0;
				$id_grp_suiv=0;
				$temoin_tmp=0;
				for($loop=0;$loop<count($tab_groups);$loop++) {
					if($tab_groups[$loop]['id']==$id_groupe){
						$num_groupe=$loop;

						$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";

						$temoin_tmp=1;
						if(isset($tab_groups[$loop+1])){
							$id_grp_suiv=$tab_groups[$loop+1]['id'];
						}
						else{
							$id_grp_suiv=0;
						}
					}
					else {
						$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
					}

					if($temoin_tmp==0){
						$id_grp_prec=$tab_groups[$loop]['id'];
					}
				}
				// =================================

				if(($chaine_options_classes!="")&&($nb_groupes_suivies>1)) {
					echo " | <select name='id_groupe' onchange=\"document.forms['form1'].submit();\">\n";
					echo $chaine_options_classes;
					echo "</select> | \n";
				}
			}
			// =================================
		}

		// Insérer les mêmes choix:

		$k=1;
        while ($k < $nb_periode) {
			$temp = "visu_note_".$k;
			if (isset($_POST[$temp]) or isset($_GET[$temp])) {
				echo "<input type='hidden' name='visu_note_$k' value='yes' />\n";
			}

			$temp = "visu_app_".$k;
			if (isset($_POST[$temp]) or isset($_GET[$temp])) {
				echo "<input type='hidden' name='visu_app_$k' value='yes' />\n";
			}

			$k++;
		}

		// Pour conserver les memes choix en changeant de groupe:

		if(((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes"))||((isset($_GET['afficher_rang']))&&($_GET['afficher_rang']=="yes"))) {
			echo "<input type='hidden' name='afficher_rang' value='yes' />\n";
		}

		if(((isset($_POST['stat']))&&($_POST['stat']=="yes"))||((isset($_GET['stat']))&&($_GET['stat']=="yes"))) {
			echo "<input type='hidden' name='stat' value='yes' />\n";
		}

		if(((isset($_POST['choix_visu']))&&($_POST['choix_visu']=="yes"))||((isset($_GET['choix_visu']))&&($_GET['choix_visu']=="yes"))) {
			echo "<input type='hidden' name='choix_visu' value='yes' />\n";
		}

		if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
			echo "<input type='hidden' name='afficher_mediane' value='$afficher_mediane' />\n";
		}

		echo "<input type='hidden' name='couleur_alterne' value='$couleur_alterne' />\n";
		echo "<input type='hidden' name='larg_tab' value='$larg_tab' />\n";
		echo "<input type='hidden' name='bord' value='$bord' />\n";
		echo "<input type='hidden' name='bord' value='$stat' />\n";
		if(isset($order_by)){
			echo "<p><input type='hidden' name='order_by' value='$order_by' />\n";
		}

		echo "</form>\n";

		echo "\n<!-- Formulaire pour l'affichage sans entête -->\n";
		echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire1\"  target=\"_blank\">\n";
		echo "<p><input type=\"submit\" value=\"Visualiser sans l'en-tête\" /></p>\n";
		echo "<input type='hidden' name='couleur_alterne' value='$couleur_alterne' />\n";
	}
	else {
		// On ne place ici cette annonce de formulaire que pour avoir du code HTML valide:
		echo "\n<!-- Formulaire pour l'affichage sans entête -->\n";
		echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire1\"  target=\"_blank\">\n";
	}
	//=========================

    echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
    echo "<input type='hidden' name='choix_visu' value='yes' />\n";
    if ($stat == "yes") echo "<input type='hidden' name='stat' value='yes' />\n";
    $i="1";
    while ($i < $nb_periode) {
        $name1 = "visu_note_".$i;
        //if (isset($_POST[$name1])) {
        if ((isset($_POST[$name1]))||(isset($_GET[$name1]))) {
            //$temp1 = $_POST[$name1];
            $temp1 = isset($_POST[$name1]) ? $_POST[$name1] : $_GET[$name1];
            echo "<input type='hidden' name='$name1' value='$temp1' />\n";
        }
        $name2 = "visu_app_".$i;
        //if (isset($_POST[$name2])) {
        if ((isset($_POST[$name2]))||(isset($_GET[$name2]))) {
            //$temp2 = $_POST[$name2];
            $temp2 = isset($_POST[$name2]) ? $_POST[$name2] : $_GET[$name2];
            echo "<input type='hidden' name='$name2' value='$temp2' />\n";
        }

        $i++;
    }
	//=========================
	// Pour avoir le rang sur le tableau sans entête
	//if((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes")) {
	if(((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes"))||((isset($_GET['afficher_rang']))&&($_GET['afficher_rang']=="yes"))) {
		echo "<input type='hidden' name='afficher_rang' value='yes' />\n";
	}

	if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
		echo "<input type='hidden' name='afficher_mediane' value='$afficher_mediane' />\n";
	}
	//=========================
    echo "<input type='hidden' name='en_tete' value='no' />\n";
    echo "<input type='hidden' name='larg_tab' value='$larg_tab' />\n";
    echo "<input type='hidden' name='bord' value='$bord' />\n";

	if(isset($order_by)){
		echo "<p><input type='hidden' name='order_by' value='$order_by' />\n";
	}

	if(isset($sens_tri)){
		echo "<p><input type='hidden' name='sens_tri' value='$sens_tri' />\n";
	}
	if(isset($col_tri)){
		echo "<p><input type='hidden' name='col_tri' value='$col_tri' />\n";
	}

    echo "</form>\n";


	//=======================================================
	// MODIF: boireaus 20080421
	// Pour permettre de trier autrement...
	echo "\n<!-- Formulaire pour l'affichage avec tri sur la colonne cliquée -->\n";
    echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire_tri\">\n";

    echo "<input type='hidden' name='col_tri' id='col_tri' value='' />\n";
    echo "<input type='hidden' name='sens_tri' id='sens_tri' value='' />\n";

    echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
    echo "<input type='hidden' name='choix_visu' value='yes' />\n";
    if ($stat == "yes") echo "<input type='hidden' name='stat' value='yes' />\n";
    $i="1";
    while ($i < $nb_periode) {
        $name1 = "visu_note_".$i;
        if ((isset($_POST[$name1]))||(isset($_GET[$name1]))) {
            $temp1 = isset($_POST[$name1]) ? $_POST[$name1] : $_GET[$name1];
            echo "<input type='hidden' name='$name1' value='$temp1' />\n";
        }
        $name2 = "visu_app_".$i;
        if ((isset($_POST[$name2]))||(isset($_GET[$name2]))) {
            $temp2 = isset($_POST[$name2]) ? $_POST[$name2] : $_GET[$name2];
            echo "<input type='hidden' name='$name2' value='$temp2' />\n";
        }
        $i++;
    }

	//if((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes")) {
	if(((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes"))||((isset($_GET['afficher_rang']))&&($_GET['afficher_rang']=="yes"))) {
		echo "<input type='hidden' name='afficher_rang' value='yes' />\n";
	}
	//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
	if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
		echo "<input type='hidden' name='afficher_mediane' value='yes' />\n";
	}
    if ($en_tete == "yes") {
		echo "<input type='hidden' name='en_tete' value='yes' />\n";
	}
	else {
		echo "<input type='hidden' name='en_tete' value='no' />\n";
	}
    echo "<input type='hidden' name='larg_tab' value='$larg_tab' />\n";
    echo "<input type='hidden' name='bord' value='$bord' />\n";

	if(isset($order_by)){
		echo "<p><input type='hidden' name='order_by' id='order_by' value='$order_by' />\n";
	}

    echo "<input type='hidden' name='couleur_alterne' value='$couleur_alterne' />\n";
    echo "</form>\n";
	//=======================================================



	//=======================================================
	if((isset($col_csv))&&(count($col_csv)>0)) {
		echo "<div style='width:10em;float:right;'>\n";
		echo "\n<!-- Formulaire pour l'export CSV -->\n";
		echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"form_csv\" target='_blank'>\n";
	
		for($i=1;$i<=count($ligne1_csv);$i++) {
			echo "<input type='hidden' name='ligne1_csv[$i]' value=\"$ligne1_csv[$i]\" />\n";
		}
		echo "<br />\n";
	
		$lignes_csv=array();
		for($i=1;$i<=count($col_csv);$i++) {
			for($j=0;$j<count($col_csv[$i]);$j++) {
				if(!isset($lignes_csv[$j])) {
					$lignes_csv[$j]=$col_csv[$i][$j];
				}
				else {
					$lignes_csv[$j].=";".preg_replace('/"/',"'",preg_replace("/;/",",",preg_replace("/&#039;/","'",html_entity_decode($col_csv[$i][$j]))));
				}
	
				//echo "<input type='hidden' name='col_csv_".$i."[$j]' value='".$col_csv[$i][$j]."' />\n";
			}
			//echo "<br />\n";
		}
	
		for($j=0;$j<count($col_csv[1]);$j++) {
			echo "<input type='hidden' name='lignes_csv[$j]' value=\"".$lignes_csv[$j]."\" />\n";
			//echo "<br />\n";
		}
	
		echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
		echo "<input type='hidden' name='mode' value='csv' />\n";
		// On ne met le bouton que pour l'affichage avec entête
		if ($en_tete == "yes") {echo "<input type='submit' value='Générer un CSV' />\n";}
		echo "</form>\n";

		echo "\n<!-- Formulaire pour l'export PDF -->\n";
		echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"form_pdf\" target='_blank'>\n";
	
		for($i=1;$i<=count($ligne1_csv);$i++) {
			echo "<input type='hidden' name='ligne1_csv[$i]' value=\"$ligne1_csv[$i]\" />\n";
		}
		echo "<br />\n";
	
		$lignes_csv=array();
		for($i=1;$i<=count($col_csv);$i++) {
			for($j=0;$j<count($col_csv[$i]);$j++) {
				if(!isset($lignes_csv[$j])) {
					$lignes_csv[$j]=$col_csv[$i][$j];
				}
				else {
					$lignes_csv[$j].=";".preg_replace('/"/',"'",preg_replace("/;/",",",preg_replace("/&#039;/","'",html_entity_decode($col_csv[$i][$j]))));
				}
	
				//echo "<input type='hidden' name='col_csv_".$i."[$j]' value='".$col_csv[$i][$j]."' />\n";
			}
			//echo "<br />\n";
		}
	
		for($j=0;$j<count($col_csv[1]);$j++) {
			echo "<input type='hidden' name='lignes_csv[$j]' value=\"".$lignes_csv[$j]."\" />\n";
			//echo "<br />\n";
		}
	
		echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
		echo "<input type='hidden' name='mode' value='pdf' />\n";
		// On ne met le bouton que pour l'affichage avec entête
		if ($en_tete == "yes") {
			echo "<input type='submit' value='Générer un PDF' />\n";
			echo "<br />\n";
			echo "Hauteur de ligne&nbsp;: <input type='text' name='h_cell' id='h_cell' value='".getPref($_SESSION['login'],'hauteur_ligne_pdf_mes_moyennes',10)."' size='2' onKeyDown=\"clavier_2(this.id,event,5,50);\" AutoComplete=\"off\" />mm\n";
			echo "<br />\n";
			echo "Marges&nbsp;: <input type='text' name='marge_pdf_mes_moyennes' id='marge_pdf_mes_moyennes' value='".getPref($_SESSION['login'],'marge_pdf_mes_moyennes',7)."' size='2' onKeyDown=\"clavier_2(this.id,event,5,20);\" AutoComplete=\"off\" />mm\n";
		}
		echo "</form>\n";

		echo "</div>\n";
	}
	//=======================================================


	echo "\n<!-- Formulaire pour ... -->\n";
    echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire2\">\n";
    if ($en_tete == "yes") {
        parametres_tableau($larg_tab, $bord);
	}
//    else echo "<p class=small><a href=\"index1.php?id_classe=$id_classe&choix_matiere=$choix_matiere\">Retour</a>";
    echo "<p class='bold'>" . $_SESSION['nom'] . " " . $_SESSION['prenom'] . " | Année : ".getSettingValue("gepiYear")." | Groupe : " . htmlspecialchars($current_group["description"]) . " (" . $current_group["classlist_string"] . ") | Matière : " . htmlspecialchars($current_group["matiere"]["nom_complet"]);
    echo "</p>\n";
    echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
    echo "<input type='hidden' name='choix_visu' value='yes' />\n";
    if ($stat == "yes") {
		echo "<input type='hidden' name='stat' value='yes' />\n";
		$_SESSION['vmm_stat']="yes";
	}
	elseif(isset($_SESSION['vmm_stat'])) {unset($_SESSION['vmm_stat']);}
    $i="1";
    while ($i < $nb_periode) {
        $name1 = "visu_note_".$i;
        if ((isset($_POST[$name1]))||(isset($_GET[$name1]))) {
            $temp1 = isset($_POST[$name1]) ? $_POST[$name1] : $_GET[$name1];
            echo "<input type='hidden' name='$name1' value='$temp1' />\n";
            $_SESSION['visu_note_'.$i]=$temp1;
        }
		elseif(isset($_SESSION['visu_note_'.$i])) {unset($_SESSION['visu_note_'.$i]);}

        $name2 = "visu_app_".$i;
        if ((isset($_POST[$name2]))||(isset($_GET[$name2]))) {
            $temp2 = isset($_POST[$name2]) ? $_POST[$name2] : $_GET[$name2];
            echo "<input type='hidden' name='$name2' value='$temp2' />\n";
            $_SESSION[$name2]=$temp2;
            $_SESSION['visu_app_'.$i]=$temp2;
        }
		elseif(isset($_SESSION['visu_app_'.$i])) {unset($_SESSION['visu_app_'.$i]);}
        $i++;
    }

	if(isset($order_by)) {echo "<input type='hidden' name='order_by' value='$order_by' />\n";}

	//if((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes")) {
	if(((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes"))||((isset($_GET['afficher_rang']))&&($_GET['afficher_rang']=="yes"))) {
		echo "<input type='hidden' name='afficher_rang' value='yes' />\n";
		$_SESSION['vmm_afficher_rang']="yes";
	}
	elseif(isset($_SESSION['vmm_afficher_rang'])) {unset($_SESSION['vmm_afficher_rang']);}

	//if((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes")) {
	if(((isset($_POST['afficher_mediane']))&&($_POST['afficher_mediane']=="yes"))||((isset($_GET['afficher_mediane']))&&($_GET['afficher_mediane']=="yes"))) {
		echo "<input type='hidden' name='afficher_mediane' value='yes' />\n";
		$_SESSION['vmm_afficher_mediane']="yes";
	}
	elseif(isset($_SESSION['vmm_afficher_mediane'])) {unset($_SESSION['vmm_afficher_mediane']);}

    echo "<input type='hidden' name='couleur_alterne' value='$couleur_alterne' />\n";
	if($couleur_alterne=="y") {$_SESSION['vmm_couleur_alterne']="y";} else {unset($_SESSION['vmm_couleur_alterne']);}

    echo "</form>\n";
//    $appel_donnees_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe='$id_classe' AND c.login = e.login) ORDER BY e.nom, e.prenom");
//    $nombre_eleves = mysql_num_rows($appel_donnees_eleves);



    //if (isset($col)) affiche_tableau($nb_lignes, $nb_col, $ligne1, $col, $larg_tab, $bord,0,0,"");

	//function affiche_tableau_index1($nombre_lignes, $nb_col, $ligne1, $col, $larg_tab, $bord, $col1_centre, $col_centre, $couleur_alterne) {

	// On commence en colonne 1: Nom Prénom
	//echo "$ligne1[1]<br />";

    if (isset($col)) {
		//$couleur_alterne="y";
		$col1_centre=0;
		$col_centre=0;
		echo "<table border=\"$bord\" class='boireaus' cellspacing=\"0\" width=\"$larg_tab\" cellpadding=\"1\" summary=''>\n";
		echo "<tr>\n";
		$j = 1;
		while($j < $nb_col+1) {
			echo "<th class='small'>";
			if(!preg_match("/Appréciation/i",$ligne1[$j])) {
				echo "<a href='#' onclick=\"document.getElementById('col_tri').value='$j';";
				if(preg_match("/Rang/i",$ligne1[$j])) {echo "document.getElementById('sens_tri').value='inverse';";}
				if($ligne1[$j]=="Classe") {echo "if(document.getElementById('order_by')) {document.getElementById('order_by').value='classe';}";}
				if($ligne1[$j]=="Nom Prénom") {echo "if(document.getElementById('order_by')) {document.getElementById('order_by').value='nom';}";}
				echo "document.forms['formulaire_tri'].submit();\">";
				echo $ligne1[$j];
				echo "</a>";
			}
			else {
				echo $ligne1[$j];
			}
			echo "</th>\n";
			$j++;
		}
		echo "</tr>\n";
		$i = "0";
		$bg_color = "";
		//$flag = "1";
		$alt=1;
		while($i < $nb_lignes) {
			if ($couleur_alterne=="y") {
				//if ($flag==1) $bg_color = "bgcolor=\"#C0C0C0\""; else $bg_color = "     " ;
				echo "<tr class='lig$alt white_hover'>\n";
			}
			else {
				echo "<tr>\n";
			}

			$j = 1;
			while($j < $nb_col+1) {
				if ((($j == 1) and ($col1_centre == 0)) or (($j != 1) and ($col_centre == 0))){
					//echo "<td class='small' ".$bg_color.">{$col[$j][$i]}</td>\n";
					echo "<td class='small'>{$col[$j][$i]}</td>\n";
				} else {
					//echo "<td align=\"center\" class='small' ".$bg_color.">{$col[$j][$i]}</td>\n";
					echo "<td align=\"center\" class='small'>{$col[$j][$i]}</td>\n";
				}
				$j++;
			}
			echo "</tr>\n";
			//if ($flag == "1") $flag = "0"; else $flag = "1";
			$alt=$alt*(-1);
			$i++;
		}
		echo "</table>\n";
	}

    if ($test == 1 and  $stat == "yes") {
        echo "<br />\n";
		echo "<div style=\"border: ".$bord."px solid black; width: ".$larg_tab."px; margin-bottom: 5px;\">
        <p><b>Moyenne générale de la classe : ".$moy_gen."</b>
        <br /><br /><b>Pourcentage des élèves ayant une moyenne générale : </b>\n";
		echo "<table style='margin-left: 3em;' border='0'>\n";
		echo "<tr>\n";
        echo "<td>inférieure strictement à 8 : </td><td class='bold'>".$pourcent_i8."</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
        echo "<td>entre 8 et 12 : </td><td class='bold'>".$pourcent_se8_ie12."</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
        echo "<td>supérieure ou égale à 12 : </td><td class='bold'>".$pourcent_se12."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";
    }

}

if(isset($bord)) {$_SESSION['prepa_conseil_index1_bord']=$bord;}
if(isset($larg_tab)) {$_SESSION['prepa_conseil_index1_larg_tab']=$larg_tab;}

require("../lib/footer.inc.php");
?>
