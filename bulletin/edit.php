<?php
/*
 * @version: $Id$
 */

@set_time_limit(0);
$starttime = microtime();
/*
*  $Id$
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Sandrine Dangreville
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


// Eric : Inclusion fichier contenant la bibliothèque pour les AID
include ("aid_lib.inc");
// Eric : Ajout : La variable pour sélectionner le bulletin
// 1 : initial
// 2 : initial avec au  dessus de min / max / moy "Pour la calsse"
// 3 : le précédent, mais le bloc Min Max Moy "Pour la classe" passe après la colonne  "appréciations"
if(getSettingValue("choix_bulletin")){
		$option_affichage_bulletin = getSettingValue("choix_bulletin");
	}
	else{
		$option_affichage_bulletin = 2;
}

switch ($option_affichage_bulletin) {
case 1:
    $fichier_bulletin = "edit_0.inc";
    break;
case 2:
    $fichier_bulletin = "edit_1.inc";
    break;
case 3:
    $fichier_bulletin = "edit_2.inc";
    break;
default:
    $fichier_bulletin = "edit_1.inc";
}
//
// Pour afficher les trois colonnes en une seule, on transmet '1':

if(getSettingValue("min_max_moyclas")){
		$min_max_moyclas = getSettingValue("min_max_moyclas");
	}
	else{
		$min_max_moyclas = 0;
}


$RneEtablissement=getSettingValue("gepiSchoolRne");


if (isset($_POST['id_classe'])) {
    settype($_POST['id_classe'],"integer");
    $id_classe = $_POST['id_classe'];
} else die();

if (isset($_POST['periode_num'])) {
    settype($_POST['periode_num'],"integer");
    $periode_num = $_POST['periode_num'];
} else die();


// Resume session
$resultat_session = $session_gepi->security_check();
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

if(!getSettingAOui('active_bulletins')) {
	header("Location: ../accueil.php?msg=Module_inactif");
	die();
}

if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiProfImprBul")!='yes') {
    die("Droits insuffisants pour effectuer cette opération");
}

include "../lib/periodes.inc.php";
// On vérifie si la période est close
if ($ver_periode[$periode_num] == "N") {
    echo "<p>Edition Impossible : la période n'est pas close.";
    die();
}


// Stockage de la période par défaut pour les bulletins pour les impressions suivantes dans la même session
$_SESSION['periode_par_defaut_bulletin']=$periode_num;


// Récupération des variables concernant la liste des élèves et le bulletin unique par famille:
//$liste_login_ele=isset($_POST['liste_login_ele']) ? $_POST['liste_login_ele'] : "_CLASSE_ENTIERE_";
$selection=isset($_POST['selection']) ? $_POST['selection'] : "_CLASSE_ENTIERE_";
$liste_login_ele=isset($_POST['liste_login_ele']) ? $_POST['liste_login_ele'] : NULL;
$un_seul_bull_par_famille=isset($_POST['un_seul_bull_par_famille']) ? $_POST['un_seul_bull_par_famille'] : "non";


$coefficients_a_1=isset($_POST['coefficients_a_1']) ? $_POST['coefficients_a_1'] : "non";

// Marge de gauche pour le bulletin
if(getSettingValue("bull_body_marginleft")){
	$bull_body_marginleft=getSettingValue("bull_body_marginleft");
}
else{
	$bull_body_marginleft=1;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <meta HTTP-EQUIV="Content-Type" content="text/html; charset=utf-8" />
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
    <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
    <META HTTP-EQUIV="Expires" CONTENT="0" />
    <title><?php echo getSettingValue("gepiSchoolName"); ?> : Bulletin | Edition des bulletins</title>
    <link rel=stylesheet type="text/css" href="../style.css" />
    <style type="text/css">
		body {
			margin-left: <?php echo $bull_body_marginleft;?>px;
		}

        .bgrand {
            color: #000000;
            font-size: <?php echo getSettingValue("titlesize");?>pt;
            font-style: normal;
        }

        /*p.bulletin {*/
	<?php
	$p_bulletin_margin=getSettingValue("p_bulletin_margin");
	$textsize=getSettingValue("textsize");

	echo ".bulletin {
color: #000000;
font-size: ".$textsize."pt;
font-style: normal;";
            if($p_bulletin_margin!=""){
                echo "margin-top: ".$p_bulletin_margin."pt;\n";
                echo "margin-bottom: ".$p_bulletin_margin."pt;\n";
            }
	echo "}\n";

	$textminclasmax=$textsize-2;
	echo ".bullminclasmax{
color: #000000;
font-size: ".$textminclasmax."pt;
font-style: normal;\n";
            if($p_bulletin_margin!=""){
			echo "margin-top: ".$p_bulletin_margin."pt;\n";
			echo "margin-bottom: ".$p_bulletin_margin."pt;\n";
            }
	echo "}\n";

//$tab_styles_avis=Array("Normal","Gras","Italique","Gras et Italique");

$bull_categ_font_size_avis=	getSettingValue("bull_categ_font_size_avis");
$bull_police_avis=getSettingValue("bull_police_avis");
$bull_font_style_avis=getSettingValue("bull_font_style_avis");
echo ".avis_bulletin {
color: #000000;
font-size: ".$bull_categ_font_size_avis."pt\n;
font-family:'".$bull_police_avis."';\n";
//font-style: ".$bull_font_style_avis.";";
switch ($bull_font_style_avis) {
case "Normal":
    echo "font-style: normal;\n";
    break;
case "Gras":
    echo "font-weight:bold;\n";
    break;
case "Italique":
    echo "font-style: italic;\n";
    break;
case "Gras et Italique":
    echo "font-style: italic;\n";
	echo "font-weight: bold;\n";
    break;
default :
    echo "font-style: normal;";
}
echo "}\n";
	?>

        @media print  {
            .noprint{
                display: none;
            }
        }

    <?php
        echo "td.adresse{
        font-size: 1em;
        color: black;
        width:".getSettingValue("addressblock_length")."mm;
        padding-top:".getSettingValue("addressblock_padding_top")."mm;
        padding-bottom:".getSettingValue("addressblock_padding_text")."mm;
        padding-right:".getSettingValue("addressblock_padding_right")."mm;
        text-align:left;
        }\n";
        echo "td.empty{
        width:auto;
        padding-right: 20%;
        }\n";

	// Récupération des variables du bloc adresses:
	// Liste de récupération à extraire de la boucle élèves pour limiter le nombre de requêtes... A FAIRE
	// Il y a d'autres récupération de largeur et de positionnement du bloc adresse à extraire...
	// PROPORTION 30%/70% POUR LE 1er TABLEAU ET ...
	if(!getSettingValue("addressblock_logo_etab_prop")){
		$largeur1=40;
	}
	else{
		$largeur1=getSettingValue("addressblock_logo_etab_prop");
	}
	$largeur2=100-$largeur1;


	// Taille des polices sur le bloc adresse:
	if(!getSettingValue("addressblock_font_size")){
		$addressblock_font_size=12;
	}
	else{
		$addressblock_font_size=getSettingValue("addressblock_font_size");
	}


	// Taille de la cellule Classe et Année scolaire sur le bloc adresse:
	if(!getSettingValue("addressblock_classe_annee")){
		$addressblock_classe_annee=35;
	}
	else{
		$addressblock_classe_annee=getSettingValue("addressblock_classe_annee");
	}
	// Calcul du pourcentage par rapport au tableau contenant le bloc Classe, Année,...
	$addressblock_classe_annee2=round(100*$addressblock_classe_annee/(100-$largeur1));


	// Débug sur l'entête pour afficher les cadres
	if(!getSettingValue("addressblock_debug")){
		$addressblock_debug="n";
	}
	else{
		$addressblock_debug=getSettingValue("addressblock_debug");
	}


	// Nombre de sauts de lignes entre le tableau logo+etab et le nom, prénom,... de l'élève
	if(!getSettingValue("bull_ecart_bloc_nom")){
		$bull_ecart_bloc_nom=0;
	}
	else{
		$bull_ecart_bloc_nom=getSettingValue("bull_ecart_bloc_nom");
	}


	// Afficher l'établissement d'origine de l'élève:
	if(!getSettingValue("bull_affiche_etab")){
		$bull_affiche_etab="n";
	}
	else{
		$bull_affiche_etab=getSettingValue("bull_affiche_etab");
	}


	// Bordure classique ou trait-noir:
	if(!getSettingValue("bull_bordure_classique")){
		$bull_bordure_classique="y";
	}
	else{
		$bull_bordure_classique=getSettingValue("bull_bordure_classique");
	}
	if($bull_bordure_classique!="y"){
		$class_bordure=" class='uneligne' ";
	}
	else{
		$class_bordure="";
	}





	$addressblock_length=getSettingValue("addressblock_length");
	$addressblock_padding_top=getSettingValue("addressblock_padding_top");
	$addressblock_padding_text=getSettingValue("addressblock_padding_text");
	$addressblock_padding_right=getSettingValue("addressblock_padding_right");




	$gepiSchoolName=getSettingValue("gepiSchoolName");
	$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1");
	$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2");
	$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode");
	$gepiSchoolCity=getSettingValue("gepiSchoolCity");
	$gepiSchoolPays=getSettingValue("gepiSchoolPays");

	// Affichage ou non du nom et de l'adresse de l'établissement
	$bull_affich_nom_etab=getSettingValue("bull_affich_nom_etab");
	$bull_affich_adr_etab=getSettingValue("bull_affich_adr_etab");
	if(($bull_affich_nom_etab!="n")&&($bull_affich_nom_etab!="y")) {$bull_affich_nom_etab="y";}
	if(($bull_affich_adr_etab!="n")&&($bull_affich_adr_etab!="y")) {$bull_affich_adr_etab="y";}

	$bull_ecart_entete=getSettingValue("bull_ecart_entete");

	$page_garde_imprime=getSettingValue("page_garde_imprime");

	$logo_etab=getSettingValue("logo_etab");

	$bull_mention_doublant=getSettingValue("bull_mention_doublant");


	$cellspacing=getSettingValue("cellspacing");
	$cellpadding=getSettingValue("cellpadding");

	$gepiYear=getSettingValue("gepiYear");

	$bull_affiche_numero=getSettingValue("bull_affiche_numero");


        $bull_affiche_avis=getSettingValue("bull_affiche_avis");
	$bull_affiche_signature=getSettingValue("bull_affiche_signature");
	$bull_affiche_appreciations=getSettingValue("bull_affiche_appreciations");

        $bull_formule_bas=getSettingValue("bull_formule_bas");
	$bull_affiche_formule=getSettingValue("bull_affiche_formule");

	$bull_affiche_absences=getSettingValue("bull_affiche_absences");
	$bull_affiche_aid=getSettingValue("bull_affiche_aid");

	$col_hauteur=getSettingValue("col_hauteur");

	$gepi_prof_suivi=getSettingValue("gepi_prof_suivi");

	$bull_espace_avis=getSettingValue("bull_espace_avis");

	$bull_affiche_eleve_une_ligne=getSettingValue("bull_affiche_eleve_une_ligne");
	$bull_mention_nom_court=getSettingValue("bull_mention_nom_court");


	if(!getSettingValue("bull_photo_largeur_max")){
		$bull_photo_largeur_max=100;
	}
	else{
		$bull_photo_largeur_max=getSettingValue("bull_photo_largeur_max");
	}

	if(!getSettingValue("bull_photo_hauteur_max")){
		$bull_photo_hauteur_max=100;
	}
	else{
		$bull_photo_hauteur_max=getSettingValue("bull_photo_hauteur_max");
	}

	if(!getSettingValue("bull_categ_font_size")){
		$bull_categ_font_size=10;
	}
	else{
		$bull_categ_font_size=getSettingValue("bull_categ_font_size");
	}

	if(!getSettingValue("bull_categ_bgcolor")){
		$bull_categ_bgcolor="";
	}
	else{
		$bull_categ_bgcolor=getSettingValue("bull_categ_bgcolor");
	}

	if(!getSettingValue("bull_intitule_app")){
		$bull_intitule_app="Appréciations/Conseils";
	}
	else{
		$bull_intitule_app=getSettingValue("bull_intitule_app");
	}

	if(!getSettingValue("bull_affiche_tel")){
		$bull_affiche_tel="n";
	}
	else{
		$bull_affiche_tel=getSettingValue("bull_affiche_tel");
	}

	if(!getSettingValue("bull_affiche_fax")){
		$bull_affiche_fax="n";
	}
	else{
		$bull_affiche_fax=getSettingValue("bull_affiche_fax");
	}

	if($bull_affiche_fax=="y"){
		$gepiSchoolFax=getSettingValue("gepiSchoolFax");
	}

	if($bull_affiche_tel=="y"){
		$gepiSchoolTel=getSettingValue("gepiSchoolTel");
	}




	if(!getSettingValue("bull_affiche_INE_eleve")){
		$bull_affiche_INE_eleve="n";
	}
	else{
		$bull_affiche_INE_eleve=getSettingValue("bull_affiche_INE_eleve");
	}


	if(getSettingValue("genre_periode")){
		$genre_periode=getSettingValue("genre_periode");
	}
	else{
		$genre_periode="M";
	}


	function redimensionne_image($photo){
		global $bull_photo_largeur_max, $bull_photo_hauteur_max;

		// prendre les informations sur l'image
		$info_image=getimagesize($photo);
		// largeur et hauteur de l'image d'origine
		$largeur=$info_image[0];
		$hauteur=$info_image[1];

		// calcule le ratio de redimensionnement
		$ratio_l=$largeur/$bull_photo_largeur_max;
		$ratio_h=$hauteur/$bull_photo_hauteur_max;
		$ratio=($ratio_l>$ratio_h)?$ratio_l:$ratio_h;

		// définit largeur et hauteur pour la nouvelle image
		$nouvelle_largeur=round($largeur/$ratio);
		$nouvelle_hauteur=round($hauteur/$ratio);

		return array($nouvelle_largeur, $nouvelle_hauteur);
	}


    ?>

    </style>
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/ico" href="../favicon.ico" />

	<?php
		if(isset($style_screen_ajout)){
			// Styles paramétrables depuis l'interface:
			if($style_screen_ajout=='y'){
				// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
				// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
				echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
			}
		}
	?>
</head>
<body>
<?php
	//echo "\$ne_pas_afficher_moy_gen=$ne_pas_afficher_moy_gen <br />\n";

	//echo "\$addressblock_classe_annee2=round(100*$addressblock_classe_annee/(100-$largeur1))=".$addressblock_classe_annee2."<br />";


// On teste la présence d'au moins un coeff pour afficher la colonne des coef
$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
//echo "\$test_coef=$test_coef<br />\n";

// Afficher la moyenne générale? (également conditionné par la présence d'un coef non nul au moins)
$display_moy_gen = sql_query1("SELECT display_moy_gen FROM classes WHERE id='".$id_classe."'");


// On teste si on affiche une colonne "rang"
$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");

// On teste si on affiche les graphiques
if (getSettingValue("bull_affiche_graphiques") == 'yes'){$affiche_graph = 'y';}else{$affiche_graph = 'n';}

//************************************************************
$affiche_adresse = sql_query1("SELECT display_address FROM classes WHERE id='".$id_classe."'");

$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
if ($affiche_categories == "y") {
    $affiche_categories = true;
} else {
    $affiche_categories = false;
}

//Afficher les coefficients des matières (uniquement si au moins un coef différent de 0)
if($test_coef>0){
	$affiche_coef = sql_query1("SELECT display_coef FROM classes WHERE id='".$id_classe."'");
}
else{
	$affiche_coef = "n";
}

$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='".$id_classe."' AND periode='$periode_num';";
$res_test_nb_ele=mysql_query($sql);
if(mysql_num_rows($res_test_nb_ele)==0) {
	echo "<p>La classe ne compte aucun élève sur la période choisie.</p>\n";
	require_once("../lib/footer.inc.php");
	die();
}

// Si le rang des élèves est demandé, on met à jour le champ rang de la table matieres_notes
if ($affiche_rang == 'y'){include "../lib/calcul_rang.inc.php";}


//============================================
// AJOUT: boireaus
// Affichage ou non du nombre de devoirs
//$affiche_nbdev=isset($_GET['affiche_nbdev']) ? $_GET['affiche_nbdev'] : "y";
$affiche_nbdev=sql_query1("SELECT display_nbdev FROM classes WHERE id='".$id_classe."'");
//============================================


// Le cas échéant, calcul des moyennes générales
include "../lib/calcul_moy_gen.inc.php";

//Initialisation des tableaux d'affichage
$largeurtableau = getSettingValue("largeurtableau");
$col_matiere_largeur = getSettingValue("col_matiere_largeur");
$col_note_largeur = getSettingValue("col_note_largeur");
$col_boite_largeur = getSettingValue("col_boite_largeur");

// Initialisations diverses
unset($call_data_aid_b);
unset($call_data_aid_e);

// Données nom de la classe et nom complet de la classe
$calldata = mysql_query("SELECT * FROM classes WHERE id = '$id_classe'");
$current_classe = mysql_result($calldata, 0, "classe");
$current_classe_nom_complet = mysql_result($calldata, 0, "nom_complet");

// On appelle la liste des groupes si ça n'a pas été fait

$appel_liste_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c
WHERE (
e.login = c.login and
c.id_classe = '".$id_classe."' and
c.periode='".$periode_num."'
)
ORDER BY e.nom, e.prenom");
$nombre_eleves = mysql_num_rows($appel_liste_eleves);
if (($affiche_rang != 'y') and ($test_coef==0)) {
    if ($affiche_categories) {
		// On utilise les valeurs spécifiées pour la classe en question
		$sql="SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id ".
		"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
		"WHERE ( " .
		"jgc.categorie_id = jmcc.categorie_id AND " .
		"jgc.id_classe='".$id_classe."' AND " .
		"jgm.id_groupe=jgc.id_groupe AND " .
		"m.matiere = jgm.id_matiere" .
		") " .
		"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet";
    } else {
        $sql="SELECT DISTINCT jgc.id_groupe, jgc.coef
        FROM j_groupes_classes jgc, j_groupes_matieres jgm
        WHERE (
        jgc.id_classe='".$id_classe."' AND
        jgm.id_groupe=jgc.id_groupe
        )
        ORDER BY jgc.priorite,jgm.id_matiere";
    }
	//echo "$sql<br />";
	$appel_liste_groupes = mysql_query($sql);
    $nombre_groupes = mysql_num_rows($appel_liste_groupes);
}

// Préparation des données
$j=0;
while ($j < $nombre_groupes) {
    // si cela n'a pas été fait dans calcul_rang_inc.php ou dans calcul_moy_gen, on prépare le tableau $current_matiere
    if (($affiche_rang != 'y') and ($test_coef==0)) {
        $group_id = mysql_result($appel_liste_groupes, $j, "id_groupe");
        $current_group[$j] = get_group($group_id);
    }

    // on prépare les données relatives aux professeurs

    $nombre_profs[$j] = count($current_group[$j]["profs"]["list"]);
    $k=0;
    foreach ($current_group[$j]["profs"]["list"] as $prof_login) {
        $current_matiere_professeur_login[$j][$k] = $prof_login;
        $k++;
    }
    //==========================================
    // MODIF: boireaus
    // noms complets des matières
    //$current_matiere_nom_complet[$j] = $current_group[$j]["description"];
    // C'était le nom du groupe... voilà le nom de la matière:
    $current_matiere_nom_complet[$j] = $current_group[$j]["matiere"]["nom_complet"];
    //==========================================

    // Si ça n'a pas été fait dans calcul_moy_inc.php, on prépare les :
    // tableaux $current_eleve_note, $current_eleve_statut et $current_classe_matiere_moyenne
    if ($test_coef == 0) {
        // Moyenne de la classe dans la matière $current_matiere[$j]
        $current_classe_matiere_moyenne_query = mysql_query("SELECT round(avg(note),1) moyenne
        FROM matieres_notes
        WHERE (
        statut ='' AND
        id_groupe='".$current_group[$j]["id"]."' AND
        periode='$periode_num'
        )
        ");
        $current_classe_matiere_moyenne[$j] = mysql_result($current_classe_matiere_moyenne_query, 0, "moyenne");

        $i=0;
        while ($i < $nombre_eleves) {
            $current_eleve_login[$i] = mysql_result($appel_liste_eleves, $i, "login");
            // Maintenant on regarde si l'élève suit bien cette matière ou pas
            if (in_array($current_eleve_login[$i], $current_group[$j]["eleves"][$periode_num]["list"])) {
                // $count[$j][$i] == "0"
                $current_eleve_note_query = mysql_query("SELECT distinct * FROM matieres_notes
                WHERE (
                login='".$current_eleve_login[$i]."' AND
                periode='$periode_num' AND
                id_groupe='".$current_group[$j]["id"]."'
                )");
                $current_eleve_note[$j][$i] = @mysql_result($current_eleve_note_query, 0, "note");
                $current_eleve_statut[$j][$i] = @mysql_result($current_eleve_note_query, 0, "statut");

				// On détermine le coefficient pour cette matière
				if((isset($coefficients_a_1))&&($coefficients_a_1=="oui")){
					$current_eleve_coef[$j][$i]=1;
				}
				else{
					// On teste si l'élève a un coef spécifique pour cette matière
					$test_coef_eleve = mysql_query("SELECT value FROM eleves_groupes_settings WHERE (" .
							"login = '".$current_eleve_login[$i]."' AND " .
							"id_groupe = '".$current_group[$j]["id"]."' AND " .
							"name = 'coef')");
					if (mysql_num_rows($test_coef_eleve) > 0) {
						$current_eleve_coef[$j][$i] = mysql_result($test_coef_eleve, 0);
					} else {
						$current_eleve_coef[$j][$i] = $current_coef[$j];
					}
				}
				$current_eleve_coef[$j][$i]=number_format($current_eleve_coef[$j][$i],1, ',', ' ');

            }
            $i++;
        }
    }
	else{
        $i=0;
        while ($i < $nombre_eleves) {

			//echo "$current_eleve_login[$i] - ";

			// EST-CE QU'ON NE VA PAS AVOIR UNE BLAGUE A REAFFECTER LE TABLEAU $current_eleve_login???
			// IL FAUDRAIT VOIR CE QUE DONNE calcul_moy_inc.php
			// PROVOQUER L'AFFICHAGE...
            //$current_eleve_login[$i] = mysql_result($appel_liste_eleves, $i, "login");

			//echo "$current_eleve_login[$i]<br />";
			// L'ordre a l'air d'être le même...
			// Pas de modif de $current_eleve_login[$i], on récupère celui rempli par calcul_moy_inc.php

            // Maintenant on regarde si l'élève suit bien cette matière ou pas
            if (in_array($current_eleve_login[$i], $current_group[$j]["eleves"][$periode_num]["list"])) {
				// On détermine le coefficient pour cette matière
				if((isset($coefficients_a_1))&&($coefficients_a_1=="oui")){
					$current_eleve_coef[$j][$i]=1;
				}
				else{
					// On teste si l'élève a un coef spécifique pour cette matière
					$test_coef_eleve = mysql_query("SELECT value FROM eleves_groupes_settings WHERE (" .
							"login = '".$current_eleve_login[$i]."' AND " .
							"id_groupe = '".$current_group[$j]["id"]."' AND " .
							"name = 'coef')");
					if (mysql_num_rows($test_coef_eleve) > 0) {
						$current_eleve_coef[$j][$i] = mysql_result($test_coef_eleve, 0);
					} else {
						$current_eleve_coef[$j][$i] = $current_coef[$j];
					}
				}
				$current_eleve_coef[$j][$i]=number_format($current_eleve_coef[$j][$i],1, ',', ' ');
            }
            $i++;
        }
	}

    $j++;
}


// Fin de l'initialisation

if ($current_classe_nom_complet == '') {
    $current_classe_nom_complet = "<font color='red'>* NOM A PRECISER *</font>";
}

$current_classe_suivi_par = @mysql_result($calldata, 0, "suivi_par");

$current_classe_formule = mysql_result($calldata, 0, "formule");
if ($current_classe_formule == '') {
    $current_classe_formule = "<font color='red'>* FORMULE A PRECISER *</font>";
}

// On prépare l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en tête des bulletins :
if (!isset($call_data_aid_b)){
    $call_data_aid_b = mysql_query("SELECT * FROM aid_config WHERE (order_display1 ='b' and display_bulletin!='n') ORDER BY order_display2");
    $nb_aid_b = mysql_num_rows($call_data_aid_b);
}

// On prépare l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en fin des bulletins :
if (!isset($call_data_aid_e)){
    $call_data_aid_e = mysql_query("SELECT * FROM aid_config WHERE (order_display1 ='e' and display_bulletin!='n') ORDER BY order_display2");
    $nb_aid_e = mysql_num_rows($call_data_aid_e);
}



// On contrôle si on ne demande à imprimer qu'un seul bulletin ou si on veut imprimer la classe entière.
// Dans le cas du bulletin unique on re-remplit les tableaux $current_eleve_login, $current_eleve_note, $current_eleve_statut

// On initialise $nombre_eleves2 à $nombre_eleves pour le cas où on aurait à traiter la classe entière et la boucle sur la liste d'élèves se fait sur $nombre_eleves2
// $nombre_eleves est conservé pour Elève n°.../total
// Et $nombre_eleves2 prend pour valeur 1 si on ne veut qu'un élève.
$nombre_eleves2=$nombre_eleves;
//echo "\$liste_login_ele=$liste_login_ele<br />";
//if($liste_login_ele!="_CLASSE_ENTIERE_"){


if(($selection!="_CLASSE_ENTIERE_")&&(isset($liste_login_ele))){

	// On vérifie que les élèves choisis sont bien tous dans la classe sur la période choisie:
	unset($liste_login_ele2);
	$liste_login_ele2=array();
	for($i=0;$i<count($liste_login_ele);$i++){
		$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$liste_login_ele[$i]' AND periode='$periode_num' AND id_classe='$id_classe'";
		$res_test=mysql_query($sql);
		if(mysql_num_rows($res_test)!=0){
			$liste_login_ele2[]=$liste_login_ele[$i];
		}
	}

	if(count($liste_login_ele2)>0){
		$nombre_eleves2=count($liste_login_ele2);
		//echo "\$nombre_eleves2=$nombre_eleves2<br />";


		unset($indice_ele);
		$indice_ele=array();
		for($i=0;$i<count($liste_login_ele2);$i++){
			// On recherche l'indice du $login_ele dans l'appel précédent comprenant la liste de tous les élèves de la classe:
			for($k=0;$k<count($current_eleve_login);$k++){
				if($current_eleve_login[$k]==$liste_login_ele2[$i]){
					$indice_ele[$i]=$k;
				}
			}
			//echo "$liste_login_ele2[$i] indice $i anciennement à l'indice $indice_ele[$i]<br />";
		}

		$chaine_eleves="";
		for($i=0;$i<count($liste_login_ele2);$i++){
			//$login_ele=$liste_login_ele;
			$login_ele=$liste_login_ele2[$i];

			if($i==0){
				$chaine_eleves="e.login='$login_ele'";
			}
			else{
				$chaine_eleves.=" OR e.login='$login_ele'";
			}
			//echo "$chaine_eleves<br />";

			// On va re-remplir des tableaux pour tenir compte de la liste des élèves souhaités...
			// mais des calculs de moyennes ont été faits auparavant.

			//$appel_liste_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c WHERE (e.login = c.login and c.id_classe = '".$id_classe."' and c.periode='".$periode_num."' AND e.login='".$login_ele."') ORDER BY e.nom, e.prenom");
			//$nombre_eleves2 = mysql_num_rows($appel_liste_eleves);
			//echo "\$nombre_eleves2=$nombre_eleves2<br />";

			// DEBUG:
			//echo "DEBUG: \$nombre_groupes=$nombre_groupes<br />";

			$j=0;
			while ($j < $nombre_groupes) {
				//$i=0;
				//$current_eleve_login[$i]=mysql_result($appel_liste_eleves, $i, "login");
				$current_eleve_login[$i]=$login_ele;
				// Maintenant on regarde si l'élève suit bien cette matière ou pas
				if (in_array($current_eleve_login[$i], $current_group[$j]["eleves"][$periode_num]["list"])) {
					$current_eleve_note_query = mysql_query("SELECT distinct * FROM matieres_notes
					WHERE (
					login='".$current_eleve_login[$i]."' AND
					periode='$periode_num' AND
					id_groupe='".$current_group[$j]["id"]."'
					)");
					$current_eleve_note[$j][$i] = @mysql_result($current_eleve_note_query, 0, "note");
					$current_eleve_statut[$j][$i] = @mysql_result($current_eleve_note_query, 0, "statut");

					// On détermine le coefficient pour cette matière

					// On teste si l'élève a un coef spécifique pour cette matière
					if((isset($coefficients_a_1))&&($coefficients_a_1=="oui")){
						$current_eleve_coef[$j][$i]=1;
					}
					else{
						$test_coef_eleve = mysql_query("SELECT value FROM eleves_groupes_settings WHERE (" .
								"login = '".$current_eleve_login[$i]."' AND " .
								"id_groupe = '".$current_group[$j]["id"]."' AND " .
								"name = 'coef')");
						if (mysql_num_rows($test_coef_eleve) > 0) {
							$current_eleve_coef[$j][$i] = mysql_result($test_coef_eleve, 0);
						} else {
							$current_eleve_coef[$j][$i] = $current_coef[$j];
						}
					}
					$current_eleve_coef[$j][$i]=number_format($current_eleve_coef[$j][$i],1, ',', ' ');
				}
				//flush();
				$j++;
			}

			// Le calcul des moyennes générales a été effectué auparavant avec:
			// include "../lib/calcul_moy_gen.inc.php";
			// ... mais les indices ne coïncident pas.
			// Idem sur les catégories
			if(isset($indice_ele[$i])){
				// Si l'élève n'est pas présent dans la classe sur toutes les périodes,
				// il se peut que l'indice ne soit pas trouvé dans la liste des élèves récupérés par le premier $appel_liste_eleves

				// Avec le re-remplissage de $liste_login_ele2, le test ci-dessus ne devrait plus servir.

				$moy_gen_eleve[$i]=$moy_gen_eleve[$indice_ele[$i]];
				$moy_gen_classe[$i]=$moy_gen_classe[$indice_ele[$i]];
				//echo "\$moy_gen_eleve[$i]=\$moy_gen_eleve[".$indice_ele[$i]."]=".$moy_gen_eleve[$indice_ele[$i]]."<br />\n";
				//echo "\$moy_gen_classe[$i]=\$moy_gen_classe[".$indice_ele[$i]."]=".$moy_gen_classe[$indice_ele[$i]]."<br />\n";

				if ($affiche_categories) {
					foreach($categories as $cat) {
						$moy_cat_eleve[$i][$cat]=$moy_cat_eleve[$indice_ele[$i]][$cat];
						$moy_cat_classe[$i][$cat]=$moy_cat_classe[$indice_ele[$i]][$cat];
					}
				}
			}
		}

		//echo "$chaine_eleves<br />";
		//flush();

		// On regénère la requête utilisée par la suite pour récupérer l'ERENO,...
		$appel_liste_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c
		WHERE (
		e.login = c.login and
		c.id_classe = '".$id_classe."' and
		c.periode='".$periode_num."' AND
		($chaine_eleves)
		)
		ORDER BY e.nom, e.prenom");
	}
}

$b_adr_pg=isset($_POST['b_adr_pg']) ? $_POST['b_adr_pg'] : 'xx';
if($b_adr_pg=='nn') {
	$affiche_adresse="n";
	$page_garde_imprime="n";
}
elseif($b_adr_pg=='yn') {
	$affiche_adresse="y";
	$page_garde_imprime="n";
}
elseif($b_adr_pg=='ny') {
	$affiche_adresse="n";
	$page_garde_imprime="yes";
}
elseif($b_adr_pg=='yy') {
	$affiche_adresse="y";
	$page_garde_imprime="yes";
}
$_SESSION['b_adr_pg']=$b_adr_pg;


// On lance la première boucle : boucle 'élève'
// ---------------------------------------------
$i=0;
//while ($i < $nombre_eleves) {
while ($i < $nombre_eleves2) {

	// On est dans la première boucle. On appelle les données complètes de l'élève :
	//-------------------------------
	$current_eleve_nom = mysql_result($appel_liste_eleves, $i, "nom");
	$current_eleve_prenom = mysql_result($appel_liste_eleves, $i, "prenom");
	$current_eleve_sexe = mysql_result($appel_liste_eleves, $i, "sexe");
	$call_profsuivi_eleve = mysql_query("SELECT professeur FROM j_eleves_professeurs WHERE (login = '".$current_eleve_login[$i]."' and id_classe='$id_classe')");
	$current_eleve_profsuivi_login = @mysql_result($call_profsuivi_eleve, '0', 'professeur');
	$current_eleve_naissance = mysql_result($appel_liste_eleves, $i, "naissance");
	$current_eleve_naissance = affiche_date_naissance($current_eleve_naissance);
	$regime_doublant_eleve = mysql_query("SELECT * FROM j_eleves_regime WHERE login = '".$current_eleve_login[$i]."'");
	$current_eleve_regime = mysql_result($regime_doublant_eleve, 0, "regime");
	$current_eleve_doublant = mysql_result($regime_doublant_eleve, 0, "doublant");
	$current_eleve_absences_query = mysql_query("SELECT * FROM absences WHERE (login='".$current_eleve_login[$i]."' AND periode='$periode_num')");
	$current_eleve_absences = @mysql_result($current_eleve_absences_query, 0, "nb_absences");
	$current_eleve_nj = @mysql_result($current_eleve_absences_query, 0, "non_justifie");
	$current_eleve_retards = @mysql_result($current_eleve_absences_query, 0, "nb_retards");
	$current_eleve_appreciation_absences = @mysql_result($current_eleve_absences_query, 0, "appreciation");
	if ($current_eleve_absences == '') { $current_eleve_absences = "?"; }
	if ($current_eleve_nj == '') { $current_eleve_nj = "?"; }
	if ($current_eleve_retards=='') { $current_eleve_retards = "?"; }
	$query = mysql_query("SELECT u.login login FROM utilisateurs u, j_eleves_cpe j WHERE (u.login = j.cpe_login AND j.e_login = '" . $current_eleve_login[$i] . "')");
	$current_eleve_cperesp_login = @mysql_result($query, "0", "login");

	// Numéro INE de l'élève:
	$current_eleve_INE = mysql_result($appel_liste_eleves, $i, "no_gep");


    //determination du nombre de bulletins à imprimer
    $nb_bulletins = 1;

    // Impression d'une page de garde
    $affiche_page_garde = $page_garde_imprime;
    if ( $affiche_page_garde == 'yes' OR $affiche_adresse == 'y') {

        $ele_id='';
        $ele_id = @mysql_result($appel_liste_eleves, $i, "ele_id");
        if ($ele_id!='') {
		$sql="SELECT rp.nom, rp.prenom, rp.civilite, ra.* FROM responsables2 r, resp_pers rp, resp_adr ra
					WHERE r.ele_id='$ele_id' AND
						rp.adr_id=ra.adr_id AND
						r.pers_id=rp.pers_id AND
						(r.resp_legal='1' OR r.resp_legal='2')
					ORDER BY r.resp_legal";
		//echo "$sql<br />";
		$call_resp=@mysql_query($sql);

		// VIDER LES TABLEAUX AVANT ?

		$nom_resp=array();
		$prenom_resp=array();
		$civilite_resp=array();
		$adr1_resp=array();
		$adr2_resp=array();
		$adr3_resp=array();
		$adr4_resp=array();
		$cp_resp=array();
		$commune_resp=array();
		$pays_resp=array();
		$cpt=1;
		while($lig_resp=mysql_fetch_object($call_resp)){
			$nom_resp[$cpt]=$lig_resp->nom;
			$prenom_resp[$cpt]=$lig_resp->prenom;
			$civilite_resp[$cpt]=$lig_resp->civilite;
			$adr1_resp[$cpt]=$lig_resp->adr1;
			$adr2_resp[$cpt]=$lig_resp->adr2;
			$adr3_resp[$cpt]=$lig_resp->adr3;
			$adr4_resp[$cpt]=$lig_resp->adr4;
			$cp_resp[$cpt]=$lig_resp->cp;
			$commune_resp[$cpt]=$lig_resp->commune;
			$pays_resp[$cpt]=$lig_resp->pays;

			echo "<!--\n";
			echo "\$nom_resp[$cpt]=$nom_resp[$cpt]\n";
			echo "\$civilite_resp[$cpt]=$civilite_resp[$cpt]\n";
			echo "\n-->\n";

			$cpt++;
		}
		if ($nom_resp[1]=='') {
			$ligne1="<font color='red'><b>ADRESSE MANQUANTE</b></font>";
			$ligne2="";
			$ligne3="";
		}
		else{

			if((isset($adr1_resp[2]))&&(isset($adr2_resp[2]))&&(isset($adr3_resp[2]))&&(isset($cp_resp[2]))&&(isset($commune_resp[2]))) {
				if((
				(mb_substr($adr1_resp[1],0,mb_strlen($adr1_resp[1])-1)==mb_substr($adr1_resp[2],0,mb_strlen($adr1_resp[2])-1))
				and (mb_substr($adr2_resp[1],0,mb_strlen($adr2_resp[1])-1)==mb_substr($adr2_resp[2],0,mb_strlen($adr2_resp[2])-1))
				and (mb_substr($adr3_resp[1],0,mb_strlen($adr3_resp[1])-1)==mb_substr($adr3_resp[2],0,mb_strlen($adr3_resp[2])-1))
				and (mb_substr($adr4_resp[1],0,mb_strlen($adr4_resp[1])-1)==mb_substr($adr4_resp[2],0,mb_strlen($adr4_resp[2])-1))

				and ($cp_resp[1]==$cp_resp[2])
				and ($commune_resp[1]==$commune_resp[2])
				and ($pays_resp[1]==$pays_resp[2])
				)
				and ($adr1_resp[2]!='')) {

					//echo "\$nom_resp[1]=$nom_resp[1] \$nom_resp[2]=$nom_resp[2] ";
					//if($nom_resp[1]!=$nom_resp[2]){
					if(($nom_resp[1]!=$nom_resp[2])&&($nom_resp[2]!="")) {
						//$ligne1=$civilite_resp[1]." ".$nom_resp[1]." ".$prenom_resp[1]." et ".$civilite_resp[2]." ".$nom_resp[2]." ".$prenom_resp[2];
						$ligne1=$civilite_resp[1]." ".$nom_resp[1]." ".$prenom_resp[1];
						//$ligne1.=" et ";
						$ligne1.="<br />\n";
						$ligne1.="et ";
						$ligne1.=$civilite_resp[2]." ".$nom_resp[2]." ".$prenom_resp[2];
					}
					else{
						if(($civilite_resp[1]!="")&&($civilite_resp[2]!="")) {
							$ligne1=$civilite_resp[1]." et ".$civilite_resp[2]." ".$nom_resp[1]." ".$prenom_resp[1];
						}
						else {
							$ligne1="M. et Mme ".$nom_resp[1]." ".$prenom_resp[1];
						}
					}

				}
				elseif($civilite_resp[1]!=""){
					$ligne1=$civilite_resp[1]." ".$nom_resp[1]." ".$prenom_resp[1];
				}
				else{
					$ligne1=$nom_resp[1]." ".$prenom_resp[1];
				}
			}
			else {
				if($civilite_resp[1]!=""){
					$ligne1=$civilite_resp[1]." ".$nom_resp[1]." ".$prenom_resp[1];
				}
				else{
					$ligne1=$nom_resp[1]." ".$prenom_resp[1];
				}
			}

			//echo "\$ligne1=$ligne1 <br />\n";
			echo "<!-- \$ligne1=$ligne1 -->\n";
			$ligne2=$adr1_resp[1];
			if($adr2_resp[1]!=""){
				$ligne2.="<br />\n".$adr2_resp[1];
			}
			if($adr3_resp[1]!=""){
				$ligne2.="<br />\n".$adr3_resp[1];
			}
			if($adr4_resp[1]!=""){
				$ligne2.="<br />\n".$adr4_resp[1];
			}
			$ligne3=$cp_resp[1]." ".$commune_resp[1];

			//if($pays_resp[1]!="") {
			if(($pays_resp[1]!="")&&(my_strtolower($pays_resp[1])!=my_strtolower($gepiSchoolPays))) {
				//$ligne3.="<br />$pays_resp[1]";
				if($ligne3!=" "){
					$ligne3.="<br />";
				}
				$ligne3.="$pays_resp[1]";
			}

		}
	}
	else {
		$ligne1 = "<font color='red'><b>ADRESSE MANQUANTE</b></font>";
		$ligne2 = "";
		$ligne3 = "";
	}

	$info_eleve_page_garde="Elève: $current_eleve_nom $current_eleve_prenom, $current_classe";


	if ($affiche_page_garde == "yes") {
		include "./page_garde.php";
		// Saut de page
		echo "<p class='saut'>&nbsp;</p>\n";

	}
	//determination du nombre de bulletins à imprimer
	if((isset($adr1_resp[2]))&&(isset($adr2_resp[2]))&&(isset($adr3_resp[2]))&&(isset($cp_resp[2]))&&(isset($commune_resp[2]))) {
		if((
		(mb_substr($adr1_resp[1],0,mb_strlen($adr1_resp[1])-1)!=mb_substr($adr1_resp[2],0,mb_strlen($adr1_resp[2])-1))
		or (mb_substr($adr2_resp[1],0,mb_strlen($adr2_resp[1])-1)!=mb_substr($adr2_resp[2],0,mb_strlen($adr2_resp[2])-1))
		or (mb_substr($adr3_resp[1],0,mb_strlen($adr3_resp[1])-1)!=mb_substr($adr3_resp[2],0,mb_strlen($adr3_resp[2])-1))
		or (mb_substr($adr4_resp[1],0,mb_strlen($adr4_resp[1])-1)!=mb_substr($adr4_resp[2],0,mb_strlen($adr4_resp[2])-1))
		or ($cp_resp[1]!=$cp_resp[2])
		or ($commune_resp[1]!=$commune_resp[2])
		or ($pays_resp[1]!=$pays_resp[2])
		)
		and ($adr1_resp[2]!='')) {
			$nb_bulletins=2;
		}
	}



	// On passe outre si il a été expressement demandé un seul bulletin.
	if($un_seul_bull_par_famille=="oui"){
		$nb_bulletins=1;
	}

    }
    $k=$i+1;


    for ($bulletin=0; $bulletin<$nb_bulletins; $bulletin++) {

        //====================================================================
        // AJOUT: boireaus
        // On imprime la deuxième page de garde si nécessaire entre les deux bulletins pour permettre un recto-verso
        if ($affiche_page_garde == "yes") {
            // Pour le deuxième bulletin, $bulletin vaut 1:
            if($bulletin==1) {
                // Impression d'une deuxième page de garde s'il y a un deuxième responsable

				if (((mb_substr($adr1_resp[1],0,mb_strlen($adr1_resp[1])-1)!=mb_substr($adr1_resp[2],0,mb_strlen($adr1_resp[2])-1))
				or (mb_substr($adr2_resp[1],0,mb_strlen($adr2_resp[1])-1)!=mb_substr($adr2_resp[2],0,mb_strlen($adr2_resp[2])-1))
				or (mb_substr($adr3_resp[1],0,mb_strlen($adr3_resp[1])-1)!=mb_substr($adr3_resp[2],0,mb_strlen($adr3_resp[2])-1))
				or (mb_substr($adr4_resp[1],0,mb_strlen($adr4_resp[1])-1)!=mb_substr($adr4_resp[2],0,mb_strlen($adr4_resp[2])-1))
				or ($cp_resp[1]!=$cp_resp[2])
				or ($commune_resp[1]!=$commune_resp[2])
				or ($pays_resp[1]!=$pays_resp[2])
				)
				and ($adr1_resp[2]!='')) {
					//$ligne1=$nom_resp[2]." ".$prenom_resp[2];
					if($civilite_resp[2]!=""){
						$ligne1=$civilite_resp[2]." ".$nom_resp[2]." ".$prenom_resp[2];
					}
					else{
						$ligne1=$nom_resp[2]." ".$prenom_resp[2];
					}

					$ligne2=$adr1_resp[2];
					if($adr2_resp[2]!=""){
						$ligne2.="<br />\n".$adr2_resp[2];
					}
					if($adr3_resp[2]!=""){
						$ligne2.="<br />\n".$adr3_resp[2];
					}
					if($adr4_resp[2]!=""){
						$ligne2.="<br />\n".$adr4_resp[2];
					}
					$ligne3=$cp_resp[2]." ".$commune_resp[2];

					if(($pays_resp[2]!="")&&(my_strtolower($pays_resp[2])!=my_strtolower($gepiSchoolPays))) {
						//$ligne3.="<br />$pays_resp[2]";
						if($ligne3!=" "){
							$ligne3.="<br />";
						}
						$ligne3.="$pays_resp[2]";
					}

					include "./page_garde.php";
					// Saut de page
					echo "<p class='saut'>&nbsp;</p>";
				}

            }
        }
        //====================================================================
		//echo "<span style='color:green;'>$ligne1</span>";
/*
        // On est dans la première boucle. On appelle les données complètes de l'élève :
        //-------------------------------
        $current_eleve_nom = mysql_result($appel_liste_eleves, $i, "nom");
        $current_eleve_prenom = mysql_result($appel_liste_eleves, $i, "prenom");
        $current_eleve_sexe = mysql_result($appel_liste_eleves, $i, "sexe");
        $call_profsuivi_eleve = mysql_query("SELECT professeur FROM j_eleves_professeurs WHERE (login = '".$current_eleve_login[$i]."' and id_classe='$id_classe')");
        $current_eleve_profsuivi_login = @mysql_result($call_profsuivi_eleve, '0', 'professeur');
        $current_eleve_naissance = mysql_result($appel_liste_eleves, $i, "naissance");
        $current_eleve_naissance = affiche_date_naissance($current_eleve_naissance);
        $regime_doublant_eleve = mysql_query("SELECT * FROM j_eleves_regime WHERE login = '".$current_eleve_login[$i]."'");
        $current_eleve_regime = mysql_result($regime_doublant_eleve, 0, "regime");
        $current_eleve_doublant = mysql_result($regime_doublant_eleve, 0, "doublant");
        $current_eleve_absences_query = mysql_query("SELECT * FROM absences WHERE (login='".$current_eleve_login[$i]."' AND periode='$periode_num')");
        $current_eleve_absences = @mysql_result($current_eleve_absences_query, 0, "nb_absences");
        $current_eleve_nj = @mysql_result($current_eleve_absences_query, 0, "non_justifie");
        $current_eleve_retards = @mysql_result($current_eleve_absences_query, 0, "nb_retards");
        $current_eleve_appreciation_absences = @mysql_result($current_eleve_absences_query, 0, "appreciation");
        if ($current_eleve_absences == '') { $current_eleve_absences = "?"; }
        if ($current_eleve_nj == '') { $current_eleve_nj = "?"; }
        if ($current_eleve_retards=='') { $current_eleve_retards = "?"; }
        $query = mysql_query("SELECT u.login login FROM utilisateurs u, j_eleves_cpe j WHERE (u.login = j.cpe_login AND j.e_login = '" . $current_eleve_login[$i] . "')");
        $current_eleve_cperesp_login = @mysql_result($query, "0", "login");
*/


        // début de l'affichage du bulletin

        if ($affiche_adresse == "y") {
		// CADRE AU-DESSUS DU TABLEAU DES APPRECIATIONS:
		echo "<div";
		if($addressblock_debug=="y"){echo " style='border:1px solid red;'";}
		echo ">\n";
		// Pour éviter que le bloc-adresse ne remonte au-delà du saut de page:
		echo "<div style='clear: both; font-size: xx-small;'>&nbsp;</div>\n";


		// Affichage du bloc adresse sur le bulletin

		//adresse du premier bulletin
		if ($bulletin==0) {
/*
			$ligne1 = $nom1." ".$prenom1;
			$ligne2 = $adr1;
			if ($adr1_comp != '') $ligne2 .= "<br />".$adr1_comp;
			$ligne3 = $cp1." ".$commune1;
*/
			if(isset($nom_resp[1])){
				//$ligne1=$nom_resp[1]." ".$prenom_resp[1];
				/*
				if($civilite_resp[1]!=""){
					$ligne1=$civilite_resp[1]." ".$nom_resp[1]." ".$prenom_resp[1];
				}
				else{
					$ligne1=$nom_resp[1]." ".$prenom_resp[1];
				}
				*/

				if((isset($adr1_resp[2]))&&(isset($adr2_resp[2]))&&(isset($adr3_resp[2]))&&(isset($cp_resp[2]))&&(isset($commune_resp[2]))) {
					if((
					(mb_substr($adr1_resp[1],0,mb_strlen($adr1_resp[1])-1)==mb_substr($adr1_resp[2],0,mb_strlen($adr1_resp[2])-1))
					and (mb_substr($adr2_resp[1],0,mb_strlen($adr2_resp[1])-1)==mb_substr($adr2_resp[2],0,mb_strlen($adr2_resp[2])-1))
					and (mb_substr($adr3_resp[1],0,mb_strlen($adr3_resp[1])-1)==mb_substr($adr3_resp[2],0,mb_strlen($adr3_resp[2])-1))
					and (mb_substr($adr4_resp[1],0,mb_strlen($adr4_resp[1])-1)==mb_substr($adr4_resp[2],0,mb_strlen($adr4_resp[2])-1))
					and ($cp_resp[1]==$cp_resp[2])
					and ($commune_resp[1]==$commune_resp[2])
					and ($pays_resp[1]==$pays_resp[2])
					)
					and ($adr1_resp[2]!='')) {

						//echo "\$nom_resp[1]=$nom_resp[1] \$nom_resp[2]=$nom_resp[2] ";
						//if($nom_resp[1]!=$nom_resp[2]){
						if(($nom_resp[1]!=$nom_resp[2])&&($nom_resp[2]!="")) {
							//$ligne1=$civilite_resp[1]." ".$nom_resp[1]." ".$prenom_resp[1]." et ".$civilite_resp[2]." ".$nom_resp[2]." ".$prenom_resp[2];
							$ligne1=$civilite_resp[1]." ".$nom_resp[1]." ".$prenom_resp[1];
							//$ligne1.=" et ";
							$ligne1.="<br />\n";
							$ligne1.="et ";
							$ligne1.=$civilite_resp[2]." ".$nom_resp[2]." ".$prenom_resp[2];
						}
						else{
							//$ligne1="M. et Mme ".$nom_resp[1]." ".$prenom_resp[1];
							if(($civilite_resp[1]!="")&&($civilite_resp[2]!="")) {
								$ligne1=$civilite_resp[1]." et ".$civilite_resp[2]." ".$nom_resp[1]." ".$prenom_resp[1];
							}
							else {
								$ligne1="M. et Mme ".$nom_resp[1]." ".$prenom_resp[1];
							}
						}

					}
					elseif($civilite_resp[1]!=""){
						$ligne1=$civilite_resp[1]." ".$nom_resp[1]." ".$prenom_resp[1];
					}
					else{
						$ligne1=$nom_resp[1]." ".$prenom_resp[1];
					}
				}
				else {
					if($civilite_resp[1]!=""){
						$ligne1=$civilite_resp[1]." ".$nom_resp[1]." ".$prenom_resp[1];
					}
					else{
						$ligne1=$nom_resp[1]." ".$prenom_resp[1];
					}
				}




				$ligne2=$adr1_resp[1];
				if($adr2_resp[1]!=""){
					$ligne2.="<br />\n".$adr2_resp[1];
				}
				if($adr3_resp[1]!=""){
					$ligne2.="<br />\n".$adr3_resp[1];
				}
				if($adr4_resp[1]!=""){
					$ligne2.="<br />\n".$adr4_resp[1];
				}
				$ligne3=$cp_resp[1]." ".$commune_resp[1];

				if(($pays_resp[1]!="")&&(my_strtolower($pays_resp[1])!=my_strtolower($gepiSchoolPays))) {
					//$ligne3.="<br />$pays_resp[1]";
					if($ligne3!=" "){
						$ligne3.="<br />";
					}
					$ligne3.="$pays_resp[1]";
				}

			}
			else{
				$ligne1 = "<font color='red'><b>ADRESSE MANQUANTE</b></font>";
				$ligne2="";
				$ligne3="";
			}
		}


		//adresse du second bulletin
		if ($bulletin==1) {
/*
			$ligne1 = $nom2." ".$prenom2;
			$ligne2 = $adr2;
			if ($adr2_comp != '') $ligne2 .= "<br />".$adr2_comp;
			$ligne3 = $cp2." ".$commune2;
*/

			if(isset($nom_resp[2])){
				//$ligne1=$nom_resp[2]." ".$prenom_resp[2];
				if($civilite_resp[2]!=""){
					$ligne1=$civilite_resp[2]." ".$nom_resp[2]." ".$prenom_resp[2];
				}
				else{
					$ligne1=$nom_resp[2]." ".$prenom_resp[2];
				}
				$ligne2=$adr1_resp[2];
				if($adr2_resp[2]!=""){
					$ligne2.="<br />\n".$adr2_resp[2];
				}
				if($adr3_resp[2]!=""){
					$ligne2.="<br />\n".$adr3_resp[2];
				}
				if($adr4_resp[2]!=""){
					$ligne2.="<br />\n".$adr4_resp[2];
				}
				$ligne3=$cp_resp[2]." ".$commune_resp[2];

				if(($pays_resp[2]!="")&&(my_strtolower($pays_resp[2])!=my_strtolower($gepiSchoolPays))) {
					if($ligne3!=" "){
						$ligne3.="<br />";
					}
					$ligne3.="$pays_resp[2]";
				}

			}
			else{
				$ligne1 = "<font color='red'><b>ADRESSE MANQUANTE</b></font>";
				$ligne2="";
				$ligne3="";
			}
		}

/*
		if($nom1==""){
			$ligne1 = "<font color='red'><b>ADRESSE MANQUANTE</b></font>";
		}
*/

		// Cadre adresse du responsable:
		echo "<div style='float:right;
width:".$addressblock_length."mm;
padding-top:".$addressblock_padding_top."mm;
padding-bottom:".$addressblock_padding_text."mm;
padding-right:".$addressblock_padding_right."mm;\n";
if($addressblock_debug=="y"){echo "border: 1px solid blue;\n";}
echo "font-size: ".$addressblock_font_size."pt;
'>
<div align='left'>
$ligne1<br />
$ligne2<br />
$ligne3
</div>
</div>\n";


		// Cadre contenant le tableau Logo+Ad_etab et le nom, prénom,... de l'élève:
		echo "<div style='float:left;
left:0px;
top:0px;
width:".$largeur1."%;\n";
if($addressblock_debug=="y"){echo "border: 1px solid green;\n";}
echo "'>\n";

		echo "<table summary='Tableau du logo et infos établissement'";
		if($addressblock_debug=="y"){echo " border='1'";}
		echo ">\n";
		echo "<tr>\n";

		$nom_fic_logo = $logo_etab;
		$nom_fic_logo_c = "../images/".$nom_fic_logo;

		if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
			echo "<td style=\"text-align: left;\"><img src=\"".$nom_fic_logo_c."\" border=\"0\" alt=\"Logo\" /></td>\n";
		}
		/*
		else {
			echo "<td>&nbsp;</td>\n";
		}
		*/
		//echo "<td style='width: 20%;text-align: center;'>";
		echo "<td style='text-align: center;'>";

		//echo "<p class='bulletin'><span class=\"bgrand\">".getSettingValue("gepiSchoolName")."</span><br />\n".getSettingValue("gepiSchoolAdress1")."<br />\n".getSettingValue("gepiSchoolAdress2")."<br />\n".getSettingValue("gepiSchoolZipCode")." ".getSettingValue("gepiSchoolCity")."</p>\n";

		echo "<p class='bulletin'>";
		if($bull_affich_nom_etab=="y"){
			echo "<span class=\"bgrand\">".$gepiSchoolName."</span>";
		}
		if($bull_affich_adr_etab=="y"){
			echo "<br />\n".$gepiSchoolAdress1."<br />\n".$gepiSchoolAdress2."<br />\n".$gepiSchoolZipCode." ".$gepiSchoolCity;
			if($bull_affiche_tel=="y"){echo "<br />\nTel: ".$gepiSchoolTel;}
			if($bull_affiche_fax=="y"){echo "<br />\nFax: ".$gepiSchoolFax;}
		}
		echo "</p>\n";

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<br />";


		// On rajoute des lignes vides
		$n = 0;
		while ($n < $bull_ecart_bloc_nom) {
			echo "<br />";
			$n++;
		}

		//echo "DEBUG<br />";
		//echo 'getSettingValue("active_module_trombinoscopes")='.getSettingValue("active_module_trombinoscopes")."<br />";
		//echo 'getSettingValue("activer_photo_bulletin")='.getSettingValue("activer_photo_bulletin")."<br />";

		//echo "getSettingValue(\"activer_photo_bulletin\")=".getSettingValue("activer_photo_bulletin")."<br />";
		//echo "getSettingValue(\"active_module_trombinoscopes\")=".getSettingValue("active_module_trombinoscopes")."<br />";

		$current_eleve_elenoet=mysql_result($appel_liste_eleves, $i, "elenoet");
		if (getSettingValue("activer_photo_bulletin")=='y' and getSettingValue("active_module_trombinoscopes")=='y') {
			//$current_eleve_idphoto=mysql_result($appel_liste_eleves, $i, "elenoet");
			$current_eleve_idphoto=$current_eleve_elenoet;
			$photo=nom_photo($current_eleve_idphoto);
			if($photo){
				if(file_exists($photo)){
					$dimphoto=redimensionne_image($photo);
					echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />'."\n";
				}
			}
		}


        	        //affichage des données sur une seule ligne ou plusieurs
        if  ($bull_affiche_eleve_une_ligne == 'no') { // sur plusieurs lignes
			echo "<p class='bulletin'>\n";
			echo "<b><span class=\"bgrand\">$current_eleve_nom $current_eleve_prenom</span></b><br />";
			if ($current_eleve_sexe == "M") {
				echo "Né&nbsp;le&nbsp;$current_eleve_naissance";
			} else {
				echo "Née&nbsp;le&nbsp;$current_eleve_naissance";
			}
			//Eric Ajout
			echo "<br />";
			if ($current_eleve_regime == "d/p") {echo "Demi-pensionnaire";}
			if ($current_eleve_regime == "ext.") {echo "Externe";}
			if ($current_eleve_regime == "int.") {echo "Interne";}
			if ($current_eleve_regime == "i-e"){
			   if ($current_eleve_sexe == "M"){echo "Interne&nbsp;externé";}else{echo "Interne&nbsp;externée";}
			}
			//Eric Ajout
			if ($bull_mention_doublant == 'yes'){
				if ($current_eleve_doublant == 'R'){
				echo "<br />";
				if ($current_eleve_sexe == "M"){echo "Redoublant";}else{echo "Redoublante";}
				}
			}

			if ($bull_mention_nom_court == 'no') {
				//Eric Ajout et supp
				//echo "<br />";
				//echo ", $current_classe";
			} else {
			    echo "<br />";
				echo "$current_classe";
			}

        } else { //sur une ligne
			echo "<p class='bulletin'>\n";
			echo "<b><span class=\"bgrand\">$current_eleve_nom $current_eleve_prenom</span></b><br />";
			if ($current_eleve_sexe == "M") {
				echo "Né&nbsp;le&nbsp;$current_eleve_naissance";
			} else {
				echo "Née&nbsp;le&nbsp;$current_eleve_naissance";
			}

			if ($current_eleve_regime == "d/p") {echo ", Demi-pensionnaire";}
			if ($current_eleve_regime == "ext.") {echo ", Externe";}
			if ($current_eleve_regime == "int.") {echo ", Interne";}
			if ($current_eleve_regime == "i-e"){
				if ($current_eleve_sexe == "M"){echo ", Interne&nbsp;externé";}else{echo ", Interne&nbsp;externée";}
			}
			if ($bull_mention_doublant == 'yes'){
				if ($current_eleve_doublant == 'R'){
				if ($current_eleve_sexe == "M"){echo ", Redoublant";}else{echo ", Redoublante";}
				}
			}
			if ($bull_mention_nom_court == 'yes') {
				echo ", $current_classe";
			}
		}

		if($bull_affiche_INE_eleve=="y"){
			echo "<br />\n";
			echo "Numéro INE: $current_eleve_INE";
		}

		if($bull_affiche_etab=="y"){
			//$data_etab = mysql_query("SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='".$current_eleve_login[$i]."' AND e.id = j.id_etablissement) ");
			$data_etab = mysql_query("SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='".$current_eleve_elenoet."' AND e.id = j.id_etablissement) ");
			$current_eleve_etab_id = @mysql_result($data_etab, 0, "id");
			$current_eleve_etab_nom = @mysql_result($data_etab, 0, "nom");
			$current_eleve_etab_niveau = @mysql_result($data_etab, 0, "niveau");
			$current_eleve_etab_type = @mysql_result($data_etab, 0, "type");
			$current_eleve_etab_cp = @mysql_result($data_etab, 0, "cp");
			$current_eleve_etab_ville = @mysql_result($data_etab, 0, "ville");

			if ($current_eleve_etab_niveau!='') {
			foreach ($type_etablissement as $type_etab => $nom_etablissement) {
				if ($current_eleve_etab_niveau == $type_etab) {$current_eleve_etab_niveau_nom = $nom_etablissement;}
			}
			if ($current_eleve_etab_cp == 0) {$current_eleve_etab_cp = '';}
			if ($current_eleve_etab_type == 'aucun')
				$current_eleve_etab_type = '';
			else
				$current_eleve_etab_type = $type_etablissement2[$current_eleve_etab_type][$current_eleve_etab_niveau];
			}
			if ($current_eleve_etab_nom != '') {
				echo "<br />\n";
				if ($current_eleve_etab_id != '990') {
				    //echo "$current_eleve_etab_niveau_nom $current_eleve_etab_type $current_eleve_etab_nom ($current_eleve_etab_cp $current_eleve_etab_ville)\n";
					if ($RneEtablissement != $current_eleve_etab_id) {
				      echo "Etablissement d'origine : ";
					  echo "$current_eleve_etab_niveau_nom $current_eleve_etab_type $current_eleve_etab_nom ($current_eleve_etab_cp $current_eleve_etab_ville)\n";
					}
				} else {
				    echo "Etablissement d'origine : ";
					echo "hors de France\n";
				}
			}
		}

		echo "</p>";

		echo "</div>\n";

		//echo "<spacer type='vertical' size='10'>";


		// Tableau contenant le nom de la classe, l'année et la période.
		echo "<table width='".$largeur2."%' summary='Tableau des nom de classe, année et période' ";
		if($addressblock_debug=="y"){echo "border='1' ";}
		echo "cellspacing='".$cellspacing."' cellpadding='".$cellpadding."'>\n";
		echo "<tr>\n";
		echo "<td class='empty'>\n";
		echo "&nbsp;\n";
		echo "</td>\n";
		echo "<td style='width:".$addressblock_classe_annee2."%;'>\n";
		echo "<p class='bulletin' align='center'><span class=\"bgrand\">Classe de $current_classe_nom_complet<br />Année scolaire ".$gepiYear."</span><br />\n";
		$temp = my_strtolower($nom_periode[$periode_num]);
		echo "Bulletin&nbsp;";
		if($genre_periode=="M"){
			echo "du ";
		}
		else{
			echo "de la ";
		}
		echo "$temp</p>";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		// Pour que le tableau des appréciations ne vienne pas s'encastrer dans les DIV float:
		echo "<div style='clear: both; font-size: xx-small;'>&nbsp;</div>\n";

		// Fin du cadre entête:
		echo "</div>\n";

	}
	else{
		//-------------------------------
		// Maintenant, on affiche l'en-tête : Les données de l'élève, et l'adresse du lycée.
		//-------------------------------

		echo "<table width='$largeurtableau' border='0' cellspacing='".$cellspacing."' cellpadding='".$cellpadding."' summary='Tableau des données élève et établissement'>\n";
		//echo "<table width='$largeurtableau' border='1' cellspacing='".getSettingValue("cellspacing")."' cellpadding='".getSettingValue("cellpadding")."'>\n";

		echo "<tr>\n";
		echo "<td style=\"width: 30%;\">\n";
		//echo "getSettingValue(\"activer_photo_bulletin\")=".getSettingValue("activer_photo_bulletin")."<br />";
		//echo "getSettingValue(\"active_module_trombinoscopes\")=".getSettingValue("active_module_trombinoscopes")."<br />";
		if (getSettingValue("activer_photo_bulletin")=='y' and getSettingValue("active_module_trombinoscopes")=='y') {
			$current_eleve_idphoto=mysql_result($appel_liste_eleves, $i, "elenoet");
			$photo=nom_photo($current_eleve_idphoto);
			if($photo){
				if(file_exists($photo)){
					echo '<img src="'.$photo.'" style="width: 60px; height: 80px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />'."\n";
				}
			}
		}

	        //affichage des données sur une seule ligne ou plusieurs
        if  ($bull_affiche_eleve_une_ligne == 'no') { // sur plusieurs lignes
			echo "<p class='bulletin'>\n";
			echo "<b><span class=\"bgrand\">$current_eleve_nom $current_eleve_prenom</span></b><br />";
			if ($current_eleve_sexe == "M") {
				echo "Né&nbsp;le&nbsp;$current_eleve_naissance";
			} else {
				echo "Née&nbsp;le&nbsp;$current_eleve_naissance";
			}
			//Eric Ajout
			echo "<br />";
			if ($current_eleve_regime == "d/p") {echo "Demi-pensionnaire";}
			if ($current_eleve_regime == "ext.") {echo "Externe";}
			if ($current_eleve_regime == "int.") {echo "Interne";}
			if ($current_eleve_regime == "i-e"){
			   if ($current_eleve_sexe == "M"){echo "Interne&nbsp;externé";}else{echo "Interne&nbsp;externée";}
			}
			//Eric Ajout
			if ($bull_mention_doublant == 'yes'){
				if ($current_eleve_doublant == 'R'){
				echo "<br />";
				if ($current_eleve_sexe == "M"){echo "Redoublant";}else{echo "Redoublante";}
				}
			}

			if ($bull_mention_nom_court == 'no') {
				//Eric Ajout et supp
				//echo "<br />";
				//echo ", $current_classe";
			} else {
			    echo "<br />";
				echo "$current_classe";
			}

        } else { //sur une ligne
			echo "<p class='bulletin'>\n";
			echo "<b><span class=\"bgrand\">$current_eleve_nom $current_eleve_prenom</span></b><br />";
			if ($current_eleve_sexe == "M") {
				echo "Né&nbsp;le&nbsp;$current_eleve_naissance";
			} else {
				echo "Née&nbsp;le&nbsp;$current_eleve_naissance";
			}

			if ($current_eleve_regime == "d/p") {echo ", Demi-pensionnaire";}
			if ($current_eleve_regime == "ext.") {echo ", Externe";}
			if ($current_eleve_regime == "int.") {echo ", Interne";}
			if ($current_eleve_regime == "i-e"){
				if ($current_eleve_sexe == "M"){echo ", Interne&nbsp;externé";}else{echo ", Interne&nbsp;externée";}
			}
			if ($bull_mention_doublant == 'yes'){
				if ($current_eleve_doublant == 'R'){
				if ($current_eleve_sexe == "M"){echo ", Redoublant";}else{echo ", Redoublante";}
				}
			}
			if ($bull_mention_nom_court == 'yes') {
				echo ", $current_classe";
			}
		}


		if($bull_affiche_etab=="y"){
			$data_etab = mysql_query("SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='".$current_eleve_login[$i]."' AND e.id = j.id_etablissement) ");
			$current_eleve_etab_id = @mysql_result($data_etab, 0, "id");
			$current_eleve_etab_nom = @mysql_result($data_etab, 0, "nom");
			$current_eleve_etab_niveau = @mysql_result($data_etab, 0, "niveau");
			$current_eleve_etab_type = @mysql_result($data_etab, 0, "type");
			$current_eleve_etab_cp = @mysql_result($data_etab, 0, "cp");
			$current_eleve_etab_ville = @mysql_result($data_etab, 0, "ville");

			if ($current_eleve_etab_niveau!='') {
			foreach ($type_etablissement as $type_etab => $nom_etablissement) {
				if ($current_eleve_etab_niveau == $type_etab) {$current_eleve_etab_niveau_nom = $nom_etablissement;}
			}
			if ($current_eleve_etab_cp == 0) {$current_eleve_etab_cp = '';}
			if ($current_eleve_etab_type == 'aucun')
				$current_eleve_etab_type = '';
			else
				$current_eleve_etab_type = $type_etablissement2[$current_eleve_etab_type][$current_eleve_etab_niveau];
			}
			if ($current_eleve_etab_nom != '') {
				echo "<br />\n";
				echo "Etablissement d'origine : ";
				if ($current_eleve_etab_id != '990') {
					echo "$current_eleve_etab_niveau_nom $current_eleve_etab_type $current_eleve_etab_nom ($current_eleve_etab_cp $current_eleve_etab_ville)\n";
				} else {
					echo "hors de France\n";
				}
			}
		}

		echo "</p></td>\n<td style=\"width: 40%;text-align: center;\">\n";

		if ($affiche_adresse != "y") {
			echo "<p class='bulletin'><span class=\"bgrand\">Classe de $current_classe_nom_complet<br />Année scolaire ".$gepiYear."</span><br />";
			$temp = my_strtolower($nom_periode[$periode_num]);
			echo "Bulletin&nbsp;";
			if($genre_periode=="M"){
				echo "du ";
			}
			else{
				echo "de la ";
			}
			echo "$temp</p>";
		} else {
			echo "&nbsp;";
		}

		$nom_fic_logo = $logo_etab;
		$nom_fic_logo_c = "../images/".$nom_fic_logo;
		if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
		echo "</td>\n<td style=\"text-align: right;\"><IMG SRC=\"".$nom_fic_logo_c."\" BORDER=\"0\" ALT=\"Logo\" />";
		} else {
		echo "</td>\n<td>&nbsp;";
		}
	echo "</td>\n";
	echo "<td style=\"width: 20%;text-align: center;\">";
	echo "<p class='bulletin'>";
	if($bull_affich_nom_etab=="y"){
		echo "<span class=\"bgrand\">".$gepiSchoolName."</span>";
	}
	if($bull_affich_adr_etab=="y"){
		//echo "<span class=\"bgrand\">".$gepiSchoolName."</span>";
		if($bull_affich_nom_etab=="y"){echo "<br />\n";}
		echo $gepiSchoolAdress1."<br />\n";
		echo $gepiSchoolAdress2."<br />\n";
		echo $gepiSchoolZipCode." ".$gepiSchoolCity;

		if($bull_affiche_tel=="y"){echo "<br />\nTel: ".$gepiSchoolTel;}
		if($bull_affiche_fax=="y"){echo "<br />\nFax: ".$gepiSchoolFax;}
	}
	echo "</p>";

	echo "</td>\n</tr>\n</table>\n";
		//-------------------------------
		// Fin de l'en-tête
	}



        // On rajoute des lignes vides
        $n = 0;
        while ($n < $bull_ecart_entete) {
            echo "<br />";
            $n++;
        }

        //=============================================

		// Tableau des matières/notes/appréciations

		// Eric
		include ($fichier_bulletin);
		// Eric

        //=============================================

		// Absences

        // Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
        if ($bull_affiche_absences == 'y') {
            //
            // Tableau des absences
            //
            //echo "<table style='margin-left:5px; margin-right:5px;' width='$largeurtableau' border='0' cellspacing='".$cellspacing."' cellpadding='".$cellpadding."'>\n";
			// style='margin-left:5px; margin-right:5px;'
			// class='uneligne'
            echo "<table width='$largeurtableau' border='0' cellspacing='".$cellspacing."' cellpadding='".$cellpadding."' summary='Tableau des absences et retards'>\n";
            echo "<tr>\n<td style=\"vertical-align: top;\"><p class='bulletin'>";
            if ($current_eleve_absences == '0') {
                echo "<i>Aucune demi-journée d'absence</i>.";
            } else {
                echo "<i>Nombre de demi-journées d'absence ";
                if ($current_eleve_nj == '0') {echo "justifiées ";}
                echo ": </i><b>$current_eleve_absences</b>";
                if ($current_eleve_nj != '0') {
                    echo " (dont <b>$current_eleve_nj</b> non justifiée"; if ($current_eleve_nj != '1') {echo "s";}
                    echo ")";
                }
                echo ".";
            }
            if ($current_eleve_retards != '0') {
                echo "<i> Nombre de retards : </i><b>$current_eleve_retards</b>";
            }
            //echo "  (C.P.E. chargé du suivi : ". affiche_utilisateur($current_eleve_cperesp_login,$id_classe) . ")";
        echo "  (C.P.E. chargé";
        $sql="SELECT civilite FROM utilisateurs WHERE login='$current_eleve_cperesp_login'";
        $res_civi=mysql_query($sql);
        if(mysql_num_rows($res_civi)>0){
            $lig_civi=mysql_fetch_object($res_civi);
            if($lig_civi->civilite!="M."){
                echo "e";
            }
        }
        echo " du suivi : ". affiche_utilisateur($current_eleve_cperesp_login,$id_classe) . ")";
            if ($current_eleve_appreciation_absences != ""){echo "<br />$current_eleve_appreciation_absences";}
            echo "</p></td>\n</tr>\n</table>\n";
        }



        //=============================================

		// Avis du conseil de classe

        // MODIF: boireaus
        // Si la variable 'bull_affiche_avis' est à 'n',  mais que 'bull_affiche_signature' est à 'y', il faut quand même le tableau
        if (($bull_affiche_avis == 'y')||($bull_affiche_signature == 'y')) {
            // Tableau de l'avis des conseil de classe
            //echo "<table $class_bordure style='margin-left:5px; margin-right:5px;' width='$largeurtableau' border='1' cellspacing='".$cellspacing."' cellpadding='".$cellpadding."'>\n";
			// style='margin-left:5px; margin-right:5px;'
            echo "<table $class_bordure width='$largeurtableau' border='1' cellspacing='".$cellspacing."' cellpadding='".$cellpadding."' summary=\"Tableau de l'avis du conseil de classe\">\n";
            echo "<tr>\n";
        }
        //=============================================

        if ($bull_affiche_avis == 'y') {
            //
            // Avis du conseil de classe :
            //
            // Appel des données :
            $current_eleve_avis_query = mysql_query("SELECT * FROM avis_conseil_classe WHERE (login='".$current_eleve_login[$i]."' AND periode='$periode_num')");
            $current_eleve_avis = @mysql_result($current_eleve_avis_query, 0, "avis");


            // Tableau de l'avis des conseil de classe
            //echo "<table width='$largeurtableau' border='1' cellspacing='".getSettingValue("cellspacing")."' cellpadding='".getSettingValue("cellpadding")."'>\n";
            //echo "<tr>\n";
            //
            // Case de gauche : avis des conseils de classe
            //
            echo "<td style=\"vertical-align: top;\">";
            // 1) l'avis
            echo "<span class='bulletin'><i>Avis du Conseil de classe : </i><br /></span>";
			if($current_eleve_avis!=""){
				echo "<span class='avis_bulletin'>";
				//$current_eleve_avis
				if((strstr($current_eleve_avis,">"))||(strstr($current_eleve_avis,"<"))){
					echo $current_eleve_avis;
				}
				else{
					echo nl2br($current_eleve_avis);
				}
				echo "</span>";
			}
			else{
				echo "<span class='avis_bulletin'>&nbsp;</span>";
			}
            if ($current_eleve_avis == '') {
                // Si il n'y a pas d'avis, on rajoute des lignes vides selon les paramètres d'impression
                $n = 0;
                if ($bull_espace_avis >0){
                    while ($n < $bull_espace_avis) {
                        echo "<br />";
                        $n++;
                    }
                }
            }
//=======================
	    elseif($bull_affiche_signature == 'y'){
		echo "<br />";
	    }
//=======================
        }
        elseif ($bull_affiche_signature == 'y') {
            echo "<td style=\"vertical-align: top;\">";
        }


        if ($bull_affiche_signature == 'y') {
            // 2) Le nom du professeur principal
            if ($current_eleve_profsuivi_login){
                echo "<span class='bulletin'><b>".ucfirst($gepi_prof_suivi)." : </b><i>".affiche_utilisateur($current_eleve_profsuivi_login,$id_classe)."</i></span>\n";
            }
            echo "</td>\n";
            //
            // Case de droite : paraphe du proviseur
            //
            echo "<td width=\"30%\" valign=\"top\">\n";
			if($current_classe_formule!='') {
	            echo "<span class='bulletin'><b>$current_classe_formule&nbsp;</b></span><br />\n";
			}
			echo "<span class='bulletin'><i>$current_classe_suivi_par</i></span>";
            //echo "<p class='bulletin'><b>$current_classe_formule&nbsp;</b><br /><i>$current_classe_suivi_par</i></p>";
            //echo "</td>\n";
            // Fin du tableau
            //echo "</tr>\n</table>\n";
        }

        //=============================================
        // MODIF: boireaus
        // Si une des deux variables 'bull_affiche_avis' ou 'bull_affiche_signature' est à 'y', il faut fermer le tableau
        if (($bull_affiche_avis == 'y')||($bull_affiche_signature == 'y')) {
            echo "</td>\n";
            // Fin du tableau
            echo "</tr>\n</table>\n";
        }
        //=============================================




        // Affichage de la formule de bas de page

        if (($bull_formule_bas != '') and ($bull_affiche_formule == 'y')) {
            // Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
            echo "<table width='$largeurtableau' style='margin-left:5px; margin-right:5px;' border='0' cellspacing='".$cellspacing."' cellpadding='".$cellpadding."' summary='Tableau de la formule de bas de page'>\n";
            echo "<tr>";
            echo "<td><p align='center' class='bulletin'>".$bull_formule_bas."</p></td>\n";
            echo "</tr></table>";
        }
        //if ($i != $nombre_eleves-1) {echo "<p class='saut'>&nbsp;</p>\n";}
    //echo "<p>\$i=$i</p>\n";
        if ($i < $nombre_eleves-1) {
        echo "<p class='saut'>&nbsp;</p>\n";
    }
    else{
        if(($bulletin==0)&&($nb_bulletins==2)){
            echo "<p class='saut'>&nbsp;</p>\n";
        }
    }
    }  //fin de la boucle pour 1 ou 2 bulletins

    $i++; // Repère de la boucle 'élève';
}
require_once("../lib/microtime.php");
?>
</body></html>
