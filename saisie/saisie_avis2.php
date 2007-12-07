<?php
/*
 * Last modification  : 11/05/2005
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

$gepiYear = getSettingValue("gepiYear");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

include "../lib/bulletin_simple.inc.php";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// On teste si un professeur peut saisir les avis
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiRubConseilProf")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

// On teste si le service scolarité peut saisir les avis
if (($_SESSION['statut'] == 'scolarite') and getSettingValue("GepiRubConseilScol")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

// initialisation
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :NULL);
$periode_num = isset($_POST["periode_num"]) ? $_POST["periode_num"] :(isset($_GET["periode_num"]) ? $_GET["periode_num"] :NULL);
$fiche = isset($_POST["fiche"]) ? $_POST["fiche"] :(isset($_GET["fiche"]) ? $_GET["fiche"] :NULL);
$current_eleve_login = isset($_POST["current_eleve_login"]) ? $_POST["current_eleve_login"] :(isset($_GET["current_eleve_login"]) ? $_GET["current_eleve_login"] :NULL);
$ind_eleve_login_suiv = isset($_POST["ind_eleve_login_suiv"]) ? $_POST["ind_eleve_login_suiv"] :(isset($_GET["ind_eleve_login_suiv"]) ? $_GET["ind_eleve_login_suiv"] :NULL);
$current_eleve_login_ap = isset($NON_PROTECT["current_eleve_login_ap"]) ? traitement_magic_quotes(corriger_caracteres($NON_PROTECT["current_eleve_login_ap"])) :NULL;
//================================
// AJOUT: boireaus 20070713
//$current_eleve_login_ap=nl2br($current_eleve_login_ap);
//================================
$affiche_message = isset($_GET["affiche_message"]) ? $_GET["affiche_message"] :NULL;

include "../lib/periodes.inc.php";

//*******************************************************************************************************
$msg = '';
if (isset($_POST['is_posted'])) {
    if (($periode_num < $nb_periode) and ($periode_num > 0) and ($ver_periode[$periode_num] != "O"))  {
        $reg = 'yes';
        // si l'utilisateur n'a pas le statut scolarité, on vérifie qu'il est prof principal de l'élève
        if (($_SESSION['statut'] != 'scolarite') and ($_SESSION['statut'] != 'secours')) {
             $test_prof_suivi = sql_query1("select professeur from j_eleves_professeurs
             where login = '$current_eleve_login' and
             professeur = '".$_SESSION['login']."' and
             id_classe = '".$id_classe."'
             ");
             if ($test_prof_suivi == '-1') {
                 $msg = "Vous n'êtes pas professeur de suivi de cet élève.";
                 $reg = 'no';
             }
         }
         if ($reg == 'yes') {
             $test_eleve_avis_query = mysql_query("SELECT * FROM avis_conseil_classe WHERE (login='$current_eleve_login' AND periode='$periode_num')");
             $test = mysql_num_rows($test_eleve_avis_query);
             if ($test != "0") {
                 $register = mysql_query("UPDATE avis_conseil_classe SET avis='$current_eleve_login_ap',statut='' WHERE (login='$current_eleve_login' AND periode='$periode_num')");
             } else {
                 $register = mysql_query("INSERT INTO avis_conseil_classe SET login='$current_eleve_login',periode='$periode_num',avis='$current_eleve_login_ap',statut=''");
             }
             if (!$register) {
                 $msg = "Erreur lors de l'enregistrement des données.";
             } else {
                 $affiche_message = 'yes';             }
             }
    } else {
        $msg = "La période sur laquelle vous voulez enregistrer est verrouillée";
    }
    if (isset($_POST['ok1']))  {
        if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {
            $appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
            WHERE (
            c.id_classe='$id_classe' AND
            c.login = e.login AND
            c.periode = '".$periode_num."'

            ) ORDER BY 'nom'");
        } else {
            $appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c, j_eleves_professeurs p
            WHERE (c.id_classe='$id_classe' AND
            c.login = e.login AND
            p.login = c.login AND
            p.professeur = '".$_SESSION['login']."' AND
            c.periode = '".$periode_num."'
            ) ORDER BY 'nom'");
        }
        $nb_eleve = mysql_num_rows($appel_donnees_eleves);
        $current_eleve_login = @mysql_result($appel_donnees_eleves, $ind_eleve_login_suiv, "login");
        $ind_eleve_login_suiv++;
        if ($ind_eleve_login_suiv >= $nb_eleve)  $ind_eleve_login_suiv = 0;
        //header("Location: saisie_avis2.php?periode_num=$periode_num&amp;id_classe=$id_classe&amp;current_eleve_login=$current_eleve_login&amp;ind_eleve_login_suiv=$ind_eleve_login_suiv&amp;fiche=y&amp;msg=$msg&amp;affiche_message=$affiche_message#app");
        header("Location: saisie_avis2.php?periode_num=$periode_num&id_classe=$id_classe&current_eleve_login=$current_eleve_login&ind_eleve_login_suiv=$ind_eleve_login_suiv&fiche=y&msg=$msg&affiche_message=$affiche_message#app");
    }
}
//*******************************************************************************************************
$message_enregistrement = "Les modifications ont été enregistrées !";
$themessage = 'Des appréciations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Saisie des avis | Saisie";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// Première étape : la classe est définie, on definit la période
if (isset($id_classe) and (!isset($periode_num))) {
    $classe_suivi = sql_query1("SELECT nom_complet FROM classes WHERE id = '".$id_classe."'");
    echo "<p class=bold><a href=\"saisie_avis.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Mes classes</a></p>\n";
    echo "<p><b>".$classe_suivi.", choisissez la période : </b></p>\n";
    include "../lib/periodes.inc.php";
    $i="1";
    echo "<ul>\n";
    while ($i < $nb_periode) {
        if ($ver_periode[$i] != "O") {
            echo "<li><a href='saisie_avis2.php?id_classe=".$id_classe."&amp;periode_num=".$i."'>".ucfirst($nom_periode[$i])."</a></li>\n";
        } else {
            echo "<li>".ucfirst($nom_periode[$i])." (".$gepiClosedPeriodLabel.", édition impossible).</li>\n";
        }
    $i++;
    }
    echo "</ul>\n";
}

// Deuxième étape : la classe est définie, la période est définie, on affiche la liste des élèves
if (isset($id_classe) and (isset($periode_num)) and (!isset($fiche))) {
    $classe_suivi = sql_query1("SELECT nom_complet FROM classes WHERE id = '".$id_classe."'");
    ?>
    <p class=bold><a href="saisie_avis2.php?id_classe=<?php echo $id_classe; ?>"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Choisir une autre période</a></p>
    <p class='grand'>Classe : <?php echo $classe_suivi; ?></p>

    <p>Cliquez sur le nom de l'élève pour lequel vous voulez entrer ou modifier l'appréciation.</p>
    <table border="1" cellspacing="2" cellpadding="5" width="100%">
    <tr>
        <td width="20%"><b>Nom Prénom</b></td>
        <td><b><?php echo ucfirst($nom_periode[$periode_num]) ; ?> : avis du conseil de classe</b></td>
    </tr>
    <?php
    if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {
        $sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
        WHERE (c.id_classe='$id_classe' AND
           c.login = e.login AND
           c.periode = '".$periode_num."'
           ) ORDER BY nom";
    } else {
        $sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c, j_eleves_professeurs p
        WHERE (c.id_classe='$id_classe' AND
           c.login = e.login AND
           p.login = c.login AND
           p.professeur = '".$_SESSION['login']."' AND
           c.periode = '".$periode_num."'
           ) ORDER BY nom";
    }
	//echo "<tr><td colspan='2'>$sql</td></tr>";
	$appel_donnees_eleves = mysql_query($sql);
    $nombre_lignes = mysql_num_rows($appel_donnees_eleves);
    $i = "0";
    while($i < $nombre_lignes) {
        $current_eleve_login = mysql_result($appel_donnees_eleves, $i, "login");
        $ind_eleve_login_suiv = 0;
        if ($i < $nombre_lignes-1) $ind_eleve_login_suiv = $i+1;
        $current_eleve_nom = mysql_result($appel_donnees_eleves, $i, "nom");
        $current_eleve_prenom = mysql_result($appel_donnees_eleves, $i, "prenom");
        $current_eleve_avis_query = mysql_query("SELECT avis FROM avis_conseil_classe WHERE (login='$current_eleve_login' AND periode='$periode_num')");
        $current_eleve_avis = @mysql_result($current_eleve_avis_query, 0, "avis");
        echo "<tr>\n<td>\n<a href = 'saisie_avis2.php?periode_num=$periode_num&amp;id_classe=$id_classe&amp;fiche=y&amp;current_eleve_login=$current_eleve_login&amp;ind_eleve_login_suiv=$ind_eleve_login_suiv#app'>$current_eleve_nom $current_eleve_prenom</a></td>\n";
        echo "<td><span class=\"medium\">$current_eleve_avis&nbsp;</span></td>\n";
        echo "</tr>\n";
        $i++;
    }
    echo "</table>\n";
}


if (isset($fiche)) {
	// On teste la présence d'au moins un coeff pour afficher la colonne des coef
	$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));

	// On remonte $affiche_categories au-dessus de include "../lib/calcul_rang.inc.php"; sans quoi il se produit des erreurs.
	$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
	if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}

	// on teste si le rang doit être affiché
	$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");

	// Ajout: boireaus (sans cela le rang total n'est pas affiché.)
	if ($affiche_rang == 'y'){
		//include "../lib/calcul_rang.inc.php";}

		$periode_courante=$periode_num;
		$periode_num=1;
		while ($periode_num <= $periode_courante) {
			include "../lib/calcul_rang.inc.php";
			$periode_num++;
		}
		$periode_num=$periode_courante;
	}

	bulletin($current_eleve_login,'',0,1,$periode_num,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$test_coef,$affiche_categories);
	$current_eleve_avis_query = mysql_query("SELECT * FROM avis_conseil_classe WHERE (login='$current_eleve_login' AND periode='$periode_num')");
	$current_eleve_avis = @mysql_result($current_eleve_avis_query, 0, "avis");
	echo "<form enctype=\"multipart/form-data\" action=\"saisie_avis2.php\" method=\"post\">\n";
	echo "<table border='0'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<a name=\"app\"></a><textarea name='no_anti_inject_current_eleve_login_ap' id='no_anti_inject_current_eleve_login_ap' rows='5' cols='80' wrap='virtual' onchange=\"changement()\">";
	echo "$current_eleve_avis";
	echo "</textarea>\n";
	echo "</td>\n";


	//==========================
	// AJOUT boireaus 20071115
	$sql="SELECT elenoet, nom, prenom FROM eleves WHERE login='$current_eleve_login';";
	$res_ele=mysql_query($sql);
	$lig_ele=mysql_fetch_object($res_ele);
	$current_eleve_elenoet=$lig_ele->elenoet;
	$current_eleve_nom=$lig_ele->nom;
	$current_eleve_prenom=$lig_ele->prenom;

	// Photo...
	$photo=nom_photo($current_eleve_elenoet);
	$temoin_photo="";
	if("$photo"!=""){
		$titre="$current_eleve_nom $current_eleve_prenom";

		$texte="<div align='center'>\n";
		$texte.="<img src='../photos/eleves/".$photo."' width='150' alt=\"$current_eleve_nom $current_eleve_prenom\" title=\"$current_eleve_nom $current_eleve_prenom\" />";
		$texte.="<br />\n";
		$texte.="</div>\n";

		$temoin_photo="y";

		$tabdiv_infobulle[]=creer_div_infobulle('photo_'.$current_eleve_login,$titre,"",$texte,"",14,0,'y','y','n','n');

		echo "<td valign='top'>\n";
		echo " <a href='#' onmouseover=\"afficher_div('photo_$current_eleve_login','y',-100,20);\"";
		echo ">";
		echo "<img src='../images/icons/buddy.png' alt='$current_eleve_nom $current_eleve_prenom' />";
		echo "</a>";
		echo "</td>\n";
	}
	//==========================


	//============================
	// Pour permettre la saisie de commentaires-type, renseigner la variable $commentaires_types dans /lib/global.inc
	// Et récupérer le paquet commentaires_types sur... ADRESSE A DEFINIR:
	//if((file_exists('saisie_commentaires_types.php'))&&($commentaires_types=='y')){
	if((file_exists('saisie_commentaires_types.php'))
		&&(($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiRubConseilProf")=='yes')&&(getSettingValue('CommentairesTypesPP')=='yes'))
		||(($_SESSION['statut'] == 'scolarite')&&(getSettingValue("GepiRubConseilScol")=='yes')&&(getSettingValue('CommentairesTypesScol')=='yes'))) {
		//include('saisie_commentaires_types.php');
		echo "<td align='center'>\n";
		include('saisie_commentaires_types2.php');
		echo "</td>\n";
	}
	//============================
	echo "</tr>\n";
	echo "</table>\n";
    ?>

    <input type=hidden name=id_classe value=<?php echo "$id_classe";?> />
    <input type=hidden name=is_posted value="yes" />
    <input type=hidden name=periode_num value="<?php echo "$periode_num";?>" />
    <input type=hidden name=current_eleve_login value="<?php echo "$current_eleve_login";?>" />
    <input type=hidden name=ind_eleve_login_suiv value="<?php echo "$ind_eleve_login_suiv";?>" />
    <!--br /-->
	<input type="submit" NAME="ok1" value="Enregistrer et passer à l'élève suivant" />
    <input type="submit" NAME="ok2" value="Enregistrer et revenir à la liste" /><br /><br />&nbsp;
    </form>
    <?php

}

//**********************************************************************************************************
require("../lib/footer.inc.php");
?>
