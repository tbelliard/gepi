<?php
/**
 * Ajouter, modifier un conteneur
 * 
*
*  @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 *
 * @package Carnet_de_notes
 * @subpackage Conteneur
 * @license GNU/GPL
 * @see add_token_field()
 * @see checkAccess()
 * @see check_token()
 * @see corriger_caracteres()
 * @see get_group()
 * @see getPref()
 * @see getSettingValue()
 * @see mise_a_jour_moyennes_conteneurs()
 * @see recherche_enfant()
 * @see Session::security_check()
 * @see sous_conteneurs()
 * @see Verif_prof_cahier_notes()
*/

/*
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

/**
 * Fichiers d'initialisation
 */
require_once("../lib/initialisations.inc.php");

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

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
    die("Le module n'est pas activé.");
}

isset($id_conteneur);
$id_conteneur = isset($_POST["id_conteneur"]) ? $_POST["id_conteneur"] : (isset($_GET["id_conteneur"]) ? $_GET["id_conteneur"] : NULL);

isset($mode_navig);
$mode_navig = isset($_POST["mode_navig"]) ? $_POST["mode_navig"] : (isset($_GET["mode_navig"]) ? $_GET["mode_navig"] : NULL);

isset($id_retour);
$id_retour = isset($_POST["id_retour"]) ? $_POST["id_retour"] : (isset($_GET["id_retour"]) ? $_GET["id_retour"] : NULL);


if ($id_conteneur)  {
    $query = mysql_query("SELECT id_racine FROM cn_conteneurs WHERE id = '$id_conteneur'");
    $id_racine = mysql_result($query, 0, 'id_racine');
} else if (isset($_POST['id_racine']) or (isset($_GET['id_racine']))) {
    $id_racine = isset($_POST['id_racine']) ? $_POST['id_racine'] : (isset($_GET['id_racine']) ? $_GET['id_racine'] : NULL);
} else {
    header("Location: ../logout.php?auto=1");
    die();
}
// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
    header("Location: index.php?msg=$mess");
    die();
}



$appel_cahier_notes = mysql_query("SELECT * FROM cn_cahier_notes WHERE id_cahier_notes = '$id_racine'");
$id_groupe = mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group = get_group($id_groupe);

$periode_num = mysql_result($appel_cahier_notes, 0, 'periode');
/**
 * Gestion des périodes
 */
include "../lib/periodes.inc.php";

// On teste si la periode est vérouillée !
if ($current_group["classe"]["ver_periode"]["all"][$periode_num] <= 1) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes dont la période est bloquée !");
    header("Location: index.php?msg=$mess");
    die();
}

$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];



// enregistrement des données
if (isset($_POST['ok'])) {
	check_token();
    $reg_ok = "yes";
    $new='no';
    if (isset($_POST['new_conteneur']) and $_POST['new_conteneur'] == 'yes') {
        $reg = mysql_query("insert into cn_conteneurs (id_racine,nom_court,parent) values ('$id_racine','nouveau','$id_racine')");
        $id_conteneur = mysql_insert_id();
        if (!$reg)  $reg_ok = "no";
        $new='yes';
    }

    if (isset($_POST['mode']) and ($_POST['mode']) and my_ereg("^[12]{1}$", $_POST['mode'])) {
        if ($_POST['mode'] == 1) $_SESSION['affiche_tous'] = 'yes';
        $reg = mysql_query("UPDATE cn_conteneurs SET mode = '".$_POST['mode']."' WHERE id = '$id_conteneur'");
        if (!$reg)  $reg_ok = "no";
    }

    if ($_POST['nom_court'])  {
        $nom_court = $_POST['nom_court'];

    } else {
        $nom_court = "Cont. ".$id_conteneur;
    }
    $reg = mysql_query("UPDATE cn_conteneurs SET nom_court = '".corriger_caracteres($nom_court)."' WHERE id = '$id_conteneur'");
    if (!$reg)  $reg_ok = "no";


    if ($_POST['nom_complet'])  {
        $nom_complet = $_POST['nom_complet'];

    } else {
        $nom_complet = $nom_court;
    }

    $reg = mysql_query("UPDATE cn_conteneurs SET nom_complet = '".corriger_caracteres($nom_complet)."' WHERE id = '$id_conteneur'");
    if (!$reg)  $reg_ok = "no";

    if ($_POST['description'])  {

        $reg = mysql_query("UPDATE cn_conteneurs SET description = '".corriger_caracteres($_POST['description'])."' WHERE id = '$id_conteneur'");
        if (!$reg)  $reg_ok = "no";
    }
    if (isset($_POST['parent']))  {
        $parent = $_POST['parent'];

        $reg = mysql_query("UPDATE cn_conteneurs SET parent = '".$parent."' WHERE id = '$id_conteneur'");
        if (!$reg)  $reg_ok = "no";
    }

    if (isset($_POST['coef'])) {
        $reg = mysql_query("UPDATE cn_conteneurs SET coef = '" . $_POST['coef'] . "' WHERE id = '$id_conteneur'");
        if (!$reg)  $reg_ok = "no";
    } else {
        $reg = mysql_query("UPDATE cn_conteneurs SET coef = '0' WHERE id = '$id_conteneur'");
        if (!$reg)  $reg_ok = "no";
    }

    if ($_POST['ponderation']) {
        $reg = mysql_query("UPDATE cn_conteneurs SET ponderation = '". $_POST['ponderation']."' WHERE id = '$id_conteneur'");
        if (!$reg)  $reg_ok = "no";
    } else {
        $reg = mysql_query("UPDATE cn_conteneurs SET ponderation = '0' WHERE id = '$id_conteneur'");
        if (!$reg)  $reg_ok = "no";
    }

    if (($_POST['precision']) and my_ereg("^(s1|s5|se|p1|p5|pe)$", $_POST['precision'])) {
        $reg = mysql_query("UPDATE cn_conteneurs SET arrondir = '". $_POST['precision']."' WHERE id = '$id_conteneur'");
        if (!$reg)  $reg_ok = "no";
    }

    if (isset($_POST['display_parents'])) {
        $display_parents = 1;
    } else {
        $display_parents = 0;
    }
    $reg = mysql_query("UPDATE cn_conteneurs SET display_parents = '$display_parents' WHERE id = '$id_conteneur'");
    if (!$reg)  $reg_ok = "no";
    if (isset($_POST['display_bulletin'])) {
        $display_bulletin = 1;
    } else {
        $display_bulletin = 0;
    }
    $reg = mysql_query("UPDATE cn_conteneurs SET display_bulletin = '$display_bulletin' WHERE id = '$id_conteneur'");
    if (!$reg)  $reg_ok = "no";

    //==========================================================
    // MODIF: boireaus
    //
    // Mise à jour des moyennes du conteneur et des conteneurs parent, grand-parent, etc...
    //
    $arret = 'no';
    if (!isset($_POST['new_conteneur'])){
        mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur,$arret);
        // La boite courante est mise à jour...
        // ... mais pas la boite destination.
        
        recherche_enfant($id_racine);
    }
    //==========================================================

    if ($reg_ok=='yes') {
        if ($new=='yes') $msg = "Nouvel enregistrement réussi.";
        else $msg="Les modifications ont été effectuées avec succès.";
    } else {
        $msg = "Il y a eu un problème lors de l'enregistrement";
    }

    //
    // retour
    //
    if ($mode_navig == 'retour_saisie') {
        header("Location: ./saisie_notes.php?id_conteneur=$id_retour&msg=$msg");
        die();
    } else if ($mode_navig == 'retour_index') {
        header("Location: ./index.php?id_racine=$id_racine&msg=$msg");
        die();
    }
}

//-----------------------------------------------------------------------------------

if ($id_conteneur)  {
    $new_conteneur = 'no';
    $appel_conteneur = mysql_query("SELECT * FROM cn_conteneurs WHERE id ='$id_conteneur'");
    $id_racine = mysql_result($appel_conteneur, 0, 'id_racine');
    $nom_court = mysql_result($appel_conteneur, 0, 'nom_court');
    $nom_complet = mysql_result($appel_conteneur, 0, 'nom_complet');
    $description = mysql_result($appel_conteneur, 0, 'description');
    $mode = mysql_result($appel_conteneur, 0, 'mode');
    $coef = mysql_result($appel_conteneur, 0, 'coef');
    $arrondir = mysql_result($appel_conteneur, 0, 'arrondir');
    $ponderation = mysql_result($appel_conteneur, 0, 'ponderation');
    $display_parents = mysql_result($appel_conteneur, 0, 'display_parents');
    $display_bulletin = mysql_result($appel_conteneur, 0, 'display_bulletin');
    $parent = mysql_result($appel_conteneur, 0, 'parent');
    // liste des sous_conteneur
    $nom_sous_cont = array();
    $id_sous_cont  = array();
    $coef_sous_cont = array();
    $display_bulletin_sous_cont = array();
    $ponderation_sous_cont=array();

    $nb_sous_cont = 0;
    if ($mode==1) {
        // on s'intéresse à tous les conteneurs fils, petit-fils, ...
        sous_conteneurs($id_conteneur,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'all',$ponderation_sous_cont);
    } else {
        // On s'intéresse uniquement au conteneur fils
        sous_conteneurs($id_conteneur,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'',$ponderation_sous_cont);
    }
    $appel_nom_racine = mysql_query("SELECT * FROM cn_conteneurs WHERE id ='$id_racine'");

    // Nom du conteneur racine
    $nom_racine = mysql_result($appel_nom_racine, 0, 'nom_court');

    // Nom du conteneur racine

} else {
	if(getSettingValue("gepi_denom_boite_genre")=='f'){
		$nom_court = "Nouvelle ".my_strtolower(getSettingValue("gepi_denom_boite"));
	}
	else{
		$nom_court = "Nouveau ".my_strtolower(getSettingValue("gepi_denom_boite"));
	}
	$nom_complet = '';
	$new_conteneur = 'yes';
	$coef = "1";
	$arrondir = "s1";
	$ponderation = "0";
	$display_parents = "1";
	$display_bulletin = "0";
	$parent = '-1';
	$mode = '2';
	$description = "";
	$nb_sous_cont = 0;
}
//**************** EN-TETE *****************
if(getSettingValue("gepi_denom_boite_genre")=='f'){
	$titre_page = "Carnet de notes - Ajout/modification d'une ".my_strtolower(getSettingValue("gepi_denom_boite"));
}
else{
	$titre_page = "Carnet de notes - Ajout/modification d'un ".my_strtolower(getSettingValue("gepi_denom_boite"));
}
/**
 * Entête de la page
 */
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************


echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"add_modif_conteneur.php\" method='post'>\n";
echo add_token_field();
if(($mode_navig == 'retour_saisie')&&(isset($id_retour))) {
    echo "<div class='norme'><p class=bold><a href='./saisie_notes.php?id_conteneur=$id_retour'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
} else {
    echo "<div class='norme'><p class=bold><a href='index.php?id_racine=$id_racine'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}

// Interface simplifiée

$interface_simplifiee=isset($_POST['interface_simplifiee']) ? $_POST['interface_simplifiee'] : (isset($_GET['interface_simplifiee']) ? $_GET['interface_simplifiee'] : getPref($_SESSION['login'],'add_modif_conteneur_simpl','n'));


echo " | <a href='add_modif_conteneur.php?id_racine=$id_racine";
if(isset($id_conteneur)){
	echo "&amp;id_conteneur=$id_conteneur";
}
if(isset($mode_navig)){
	echo "&amp;mode_navig=$mode_navig";
}

if($interface_simplifiee=="y"){
	echo "&amp;interface_simplifiee=n";
	echo "'>Interface complète</a>\n";
}
else{
	echo "&amp;interface_simplifiee=y";
	echo "'>Interface simplifiée</a>\n";
}

echo " | <a href='../gestion/config_prefs.php'>Paramétrer l'interface simplifiée</a>";

echo "</p>\n";
echo "</div>\n";
echo "<p class='bold'> Classe(s) : $nom_classe | Matière : $matiere_nom ($matiere_nom_court)| Période : $nom_periode[$periode_num] <input type=\"submit\" name='ok' value=\"Enregistrer\" style=\"font-variant: small-caps;\" /></p>\n";


if(getSettingValue("gepi_denom_boite_genre")=='f'){
	echo "<h2 class='gepi'>Configuration de la ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court :</h2>\n";
}
else{
	echo "<h2 class='gepi'>Configuration du ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court :</h2>\n";
}


if($interface_simplifiee=="y"){

	// Récupérer les paramètres à afficher.
	// Dans un premier temps, un choix pour tous.
	// Dans le futur, permettre un paramétrage par utilisateur

	$aff_nom_court=getPref($_SESSION['login'],'add_modif_conteneur_nom_court','y');
	$aff_nom_complet=getPref($_SESSION['login'],'add_modif_conteneur_nom_complet','n');
	$aff_description=getPref($_SESSION['login'],'add_modif_conteneur_description','n');
	$aff_coef=getPref($_SESSION['login'],'add_modif_conteneur_coef','y');
	$aff_boite=getPref($_SESSION['login'],'add_modif_conteneur_boite','y');
	$aff_display_releve_notes=getPref($_SESSION['login'],'add_modif_conteneur_aff_display_releve_notes','n');
	$aff_display_bull=getPref($_SESSION['login'],'add_modif_conteneur_aff_display_bull','n');

	echo "<div align='center'>\n";
	echo "<table class='boireaus' border='1'>\n";

	if($aff_nom_court=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom court:</td>\n";
		echo "<td>\n";
		echo "<input type='text' name = 'nom_court' size='40' value = \"".$nom_court."\" onfocus=\"javascript:this.select()\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr style='display:none;'>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom court:</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name = 'nom_court' size='40' value = \"".$nom_court."\" onfocus=\"javascript:this.select()\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}

	if($aff_nom_complet=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom complet:</td>\n";
		echo "<td>\n";
		echo "<input type='text' name = 'nom_complet' size='40' value = \"".$nom_complet."\" onfocus=\"javascript:this.select()\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr style='display:none;'>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom complet:</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name = 'nom_complet' size='40' value = \"".$nom_complet."\" onfocus=\"javascript:this.select()\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}


	if($aff_description=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Description:</td>\n";
		echo "<td>\n";
		echo "<textarea name='description' rows='2' cols='40' wrap='virtual' onfocus=\"javascript:this.select()\">".$description."</textarea>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr style='display:none;'>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Description:</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name='description' value='$description' />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}



	if ($parent != 0) {
		if($aff_boite=='y'){
			echo "<tr>\n";

			echo "<td style='background-color: #aae6aa; font-weight: bold;'>\n";
			if(getSettingValue("gepi_denom_boite_genre")=='f'){
				echo "Emplacement de la ".my_strtolower(getSettingValue("gepi_denom_boite")).":\n";
			}
			else{
				echo "Emplacement du ".my_strtolower(getSettingValue("gepi_denom_boite")).":\n";
			}
			echo "</td>\n";
			echo "<td>\n";
			echo "<select size='1' name='parent'>\n";
			$sql="SELECT * FROM cn_conteneurs WHERE id_racine ='$id_racine' order by nom_court";
			$appel_conteneurs = mysql_query($sql);
			$nb_cont = mysql_num_rows($appel_conteneurs);
			$i = 0;
			while ($i < $nb_cont) {
				//==========================================
				// MODIF: boireaus
				$lig_cont=mysql_fetch_object($appel_conteneurs);
				$id_cont=$lig_cont->id;
				$id_parent=$lig_cont->parent;
				if(($id_cont!=$id_conteneur)&&($id_parent!=$id_conteneur)){
					// On recherche si le conteneur est un descendant du conteneur courant.
					$tmp_parent=$id_parent;
					$temoin_display="oui";
					while($tmp_parent!=0){
						$sql="SELECT * FROM cn_conteneurs WHERE id_racine ='$id_racine' AND id='$tmp_parent'";
						//echo "<!-- $sql -->\n";
						$res_parent=mysql_query($sql);
						$lig_parent=mysql_fetch_object($res_parent);
						$tmp_parent=$lig_parent->parent;
						if($tmp_parent==$id_conteneur){
							$temoin_display="non";
						}
					}

					if($temoin_display=="oui"){
						$nom_conteneur=$lig_cont->nom_court;
						echo "<option value='$id_cont'";
						if ($parent == $id_cont){echo " selected ";}
						if($nom_conteneur==""){echo " >---</option>\n";}else{echo " >$nom_conteneur</option>\n";}
					}
				}
				//==========================================
				$i++;
			}

			echo "</select>\n";

			echo "</td>\n";
			echo "</tr>\n";
		}
		else{
			// Il n'est pas prudent de ne pas laisser choisir/vérifier l'emplacement de la boite...
			// ... ou alors c'est pour imposer des boites avec la racine pour parent uniquement?
			echo "<tr style='display:none;'>\n";
			echo "<td>";
			if(getSettingValue("gepi_denom_boite_genre")=='f'){
				echo "Emplacement de la ".my_strtolower(getSettingValue("gepi_denom_boite")).":\n";
			}
			else{
				echo "Emplacement du ".my_strtolower(getSettingValue("gepi_denom_boite")).":\n";
			}
			echo "</td>\n";
			echo "<td>\n";
			if($parent==-1){
				echo "<input type='hidden' name='parent' size='10' value='$id_racine' />\n";
			}
			else{
				echo "<input type='hidden' name='parent' size='10' value='$parent' />\n";
			}
			echo "</td>\n";
			echo "</tr>\n";
		}



		if($aff_coef=='y'){
			echo "<tr>\n";
			echo "<td style='background-color: #aae6aa; font-weight: bold;'>\n";
			if(getSettingValue("gepi_denom_boite_genre")=='f'){
				echo "Coefficient de la ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court\n";
			}
			else{
				echo "Coefficient du ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court\n";
			}

			echo " (<a href='#' onmouseover=\"document.getElementById('coefinfo').style.display='block';\" onmouseout=\"document.getElementById('coefinfo').style.display='none';\">*</a>)\n";

			echo "</td>\n";
			echo "<td>\n";
			echo "<input type='text' name = 'coef' size='4' value = \"".$coef."\" onfocus=\"javascript:this.select()\" />\n";
			
			echo "</td>\n";
			echo "</tr>\n";
		}
		else{
			echo "<tr style='display:none;'>\n";
			echo "<td style='background-color: #aae6aa; font-weight: bold;'>\n";
			if(getSettingValue("gepi_denom_boite_genre")=='f'){
				echo "Coefficient de la ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court\n";
			}
			else{
				echo "Coefficient du ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court\n";
			}
			echo "</td>\n";
			echo "<td>\n";
			echo "<td><input type='hidden' name = 'coef' size='4' value = \"".$coef."\" onfocus=\"javascript:this.select()\" /></td>\n";
			echo "</tr>\n";
		}
	}

	if ($parent != 0) {
		if($aff_display_releve_notes=='y'){
			echo "<tr>\n";
			echo "<td style='background-color: #aae6aa; font-weight: bold;'>\n";
			echo "Affichage de la moyenne de $nom_court sur le relevé de notes destiné aux parents: \n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<input type='checkbox' name='display_parents' ";
			if ($display_parents == 1) echo " checked";
			echo " /></td>\n";
			echo "</tr>\n";
		}
		else{
			if($display_parents==1){
				echo "<tr style='display:none;'>\n";
				echo "<td style='background-color: #aae6aa; font-weight: bold;'>\n";
				echo "Affichage de la moyenne de $nom_court sur le relevé de notes destiné aux parents: \n";
				echo "</td>\n";
				echo "<td>\n";
				echo "<td><input type='hidden' name='display_parents' value='$display_parents' /></td>\n";
				echo "</tr>\n";
			}
		}

		if($aff_display_bull=='y'){
			echo "<tr>\n";
			echo "<td style='background-color: #aae6aa; font-weight: bold;'>\n";
			echo "Affichage de la moyenne de $nom_court sur le bulletin scolaire en plus de la moyenne générale, à titre d'information: \n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<input type='checkbox' name='display_bulletin' ";
			if ($display_bulletin == 1) echo " checked";
			echo " /></td>\n";
			echo "</tr>\n";
		}
		else{
			if($display_bulletin==1){
				echo "<tr style='display:none;'>\n";
				echo "<td style='background-color: #aae6aa; font-weight: bold;'>\n";
				echo "Affichage de la moyenne de $nom_court sur le bulletin scolaire en plus de la moyenne générale, à titre d'information: \n";
				echo "</td>\n";
				echo "<td><input type='hidden' name='display_bulletin' value='$display_bulletin' /></td>\n";
				echo "</tr>\n";
			}
		}
	} else {
		echo "<tr style='display:none;'><td>&nbsp;</td><td><input type=hidden name=display_bulletin value='yes' /></td></tr>\n";
	}



	echo "</table>\n";

	echo "<input type='hidden' name='interface_simplifiee' value='$interface_simplifiee' />\n";

	// Je laisse (pour le moment?) ceux qui suivent sur l'interface complète...
	echo "<input type='hidden' name='mode' value='$mode' />\n";
	echo "<input type='hidden' name='precision' value='$arrondir' />\n";
	echo "<input type='hidden' name = 'ponderation' value='".$ponderation."' />\n";

	echo "</div>\n";
    
    echo "<div id='coefinfo' style='display:none; text-align:justify; float:right; width:30em; border: 1px solid black; background-color: white;'>\n";
	echo "Si le coefficient est 0, la moyenne de <b>$nom_court</b> n'intervient pas dans le calcul de la moyenne du carnet de note.\n";
	echo "</div>\n";

}
else{
	// AFFICHAGE CLASSIQUE COMPLET:
	echo "<table>\n";
	echo "<tr><td>Nom court : </td><td><input type='text' name = 'nom_court' size='40' value = \"".$nom_court."\" onfocus=\"javascript:this.select()\" /></td></tr>\n";
	echo "<tr><td>Nom complet : </td><td><input type='text' name = 'nom_complet' size='40' value = \"".$nom_complet."\" onfocus=\"javascript:this.select()\" /></td></tr>\n";
	echo "<tr><td>Description : </td><td><input type='text' name = 'description' size='40' value = \"".$description."\" onfocus=\"javascript:this.select()\" /></td></tr></table>\n";

	// $parent=0 pour une boite racine.
	// On ne peut pas la déplacer,... ni modifier son coeff d'ici.
	if ($parent != 0) {
		echo "<br />\n";
		if(getSettingValue("gepi_denom_boite_genre")=='f'){
			echo "<table><tr><td><h3 class='gepi'>Emplacement de la ".my_strtolower(getSettingValue("gepi_denom_boite"))." : </h3></td>\n<td>\n";
		}
		else{
			echo "<table><tr><td><h3 class='gepi'>Emplacement du ".my_strtolower(getSettingValue("gepi_denom_boite"))." : </h3></td>\n<td>\n";
		}
		echo "<select size='1' name='parent'>\n";
		
		$sql="SELECT * FROM cn_conteneurs WHERE id_racine ='$id_racine' order by nom_court";
		$appel_conteneurs = mysql_query($sql);
		$nb_cont = mysql_num_rows($appel_conteneurs);
		$i = 0;
		while ($i < $nb_cont) {
			$lig_cont=mysql_fetch_object($appel_conteneurs);
			$id_cont=$lig_cont->id;
			$id_parent=$lig_cont->parent;
			if(($id_cont!=$id_conteneur)&&($id_parent!=$id_conteneur)){
				// On recherche si le conteneur est un descendant du conteneur courant.
				$tmp_parent=$id_parent;
				$temoin_display="oui";
				while($tmp_parent!=0){
					$sql="SELECT * FROM cn_conteneurs WHERE id_racine ='$id_racine' AND id='$tmp_parent'";
					//echo "<!-- $sql -->\n";
					$res_parent=mysql_query($sql);
					$lig_parent=mysql_fetch_object($res_parent);
					$tmp_parent=$lig_parent->parent;
					if($tmp_parent==$id_conteneur){
						$temoin_display="non";
					}
				}

				if($temoin_display=="oui"){
					$nom_conteneur=$lig_cont->nom_court;
					echo "<option value='$id_cont'";
					if ($parent == $id_cont){echo " selected ";}
					if($nom_conteneur==""){echo " >---</option>\n";}else{echo " >$nom_conteneur</option>\n";}
				}
			}
			//==========================================
			$i++;
		}
		echo "</select></td></tr></table>\n";



		if(getSettingValue("gepi_denom_boite_genre")=='f'){
			echo "<h3 class='gepi'>Coefficient de la ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court</h3>\n";
		}
		else{
			echo "<h3 class='gepi'>Coefficient du ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court</h3>\n";
		}
		echo "<table><tr><td>Valeur de la pondération dans le calcul de la moyenne ";
		if (isset($nom_racine)){
			if($nom_racine==""){
				echo "de <b>$matiere_nom</b>";
			}
			else{
				echo "de <b>$nom_racine</b>";
			}
		}
		echo "<br /><i>(si 0, la moyenne de <b>$nom_court</b> n'intervient pas dans le calcul de la moyenne du carnet de note)</i>.</td>";
		echo "<td><input type='text' name = 'coef' size='4' value = \"".$coef."\" onfocus=\"javascript:this.select()\" /></td></tr></table>\n";
	}



	if ($nb_sous_cont != 0) {
		$chaine_sous_cont="";
		$i=0;
		while ($i < $nb_sous_cont) {
			$chaine_sous_cont.="<b>$nom_sous_cont[$i]</b>, ";
			$i++;
		}

		if(getSettingValue("gepi_denom_boite_genre")=='f'){
			echo "<h3 class='gepi'>Notes prises en comptes dans le calcul de la moyenne de la ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court</h3>\n";
		}
		else{
			echo "<h3 class='gepi'>Notes prises en comptes dans le calcul de la moyenne du ".my_strtolower(getSettingValue("gepi_denom_boite"))." $nom_court</h3>\n";
		}
		if($i>1){
			echo "<table>\n<tr>";
			echo "<td>";
			echo "la moyenne s'effectue sur toutes les notes contenues à la racine de <b>$nom_court</b> et sur les moyennes des ";
			echo my_strtolower(getSettingValue("gepi_denom_boite"))."s ";
			echo " $chaine_sous_cont";
			echo "en tenant compte des options dans ces ";
			echo my_strtolower(getSettingValue("gepi_denom_boite"))."s.";
			echo "</td><td><input type='radio' name='mode' value='2' "; if ($mode=='2') echo "checked"; echo " /></td>";
			echo "</tr>\n";

			echo "<tr>";
			echo "<td>";
			echo "la moyenne s'effectue sur toutes les notes contenues dans <b>$nom_court</b> et dans les ";
			echo my_strtolower(getSettingValue("gepi_denom_boite"))."s";
			echo " $chaine_sous_cont";
			echo "sans tenir compte des options définies dans ces ";
			echo my_strtolower(getSettingValue("gepi_denom_boite"))."s.";
		}
		else{
			if(getSettingValue("gepi_denom_boite_genre")=='f'){
				echo "<table>\n<tr>";
				echo "<td>";
				echo "la moyenne s'effectue sur toutes les notes contenues à la racine de <b>$nom_court</b> et sur les moyennes de la ";
				echo my_strtolower(getSettingValue("gepi_denom_boite"));
				echo " $chaine_sous_cont";
				echo "en tenant compte des options dans cette ";
				echo my_strtolower(getSettingValue("gepi_denom_boite"));
				echo "</td><td><input type='radio' name='mode' value='2' "; if ($mode=='2') echo "checked"; echo " /></td>";
				echo "</tr>\n";

				echo "<tr>";
				echo "<td>";
				echo "la moyenne s'effectue sur toutes les notes contenues dans <b>$nom_court</b> et dans la ";
				echo my_strtolower(getSettingValue("gepi_denom_boite"));
				echo " $chaine_sous_cont";
				echo "sans tenir compte des options définies dans cette ";
				echo my_strtolower(getSettingValue("gepi_denom_boite"));
			}
			else{
				echo "<table>\n<tr>";
				echo "<td>";
				echo "la moyenne s'effectue sur toutes les notes contenues à la racine de <b>$nom_court</b> et sur les moyennes du ".my_strtolower(getSettingValue("gepi_denom_boite"));
				echo " $chaine_sous_cont";
				echo "en tenant compte des options dans ce ".my_strtolower(getSettingValue("gepi_denom_boite")).".";
				echo "</td><td><input type='radio' name='mode' value='2' "; if ($mode=='2') echo "checked"; echo " /></td>";
				echo "</tr>\n";

				echo "<tr>";
				echo "<td>";
				echo "la moyenne s'effectue sur toutes les notes contenues dans <b>$nom_court</b>";
				echo " et dans le ".my_strtolower(getSettingValue("gepi_denom_boite"))."";
				echo " $chaine_sous_cont";
				echo "sans tenir compte des options définies dans ce ".my_strtolower(getSettingValue("gepi_denom_boite")).".";
			}
		}
		echo "</td><td><input type='radio' name='mode' value='1' "; if ($mode=='1') echo "checked"; echo " /></td>";
		echo "</tr>\n</table>\n";
	}




	echo "<h3 class='gepi'>Précision du calcul de la moyenne de $nom_court : </h3>\n";
	echo "<table>\n";
	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='precision' id='precision_s1' value='s1' "; if ($arrondir=='s1') echo "checked"; echo " />\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='precision_s1' style='cursor: pointer;'>";
	echo "Arrondir au dixième de point supérieur";
	echo "</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='precision' id='precision_s5' value='s5' "; if ($arrondir=='s5') echo "checked"; echo " />\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='precision_s5' style='cursor: pointer;'>";
	echo "Arrondir au demi-point supérieur";
	echo "</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='precision' id='precision_se' value='se' "; if ($arrondir=='se') echo "checked"; echo " />\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='precision_se' style='cursor: pointer;'>";
	echo "Arrondir au point entier supérieur";
	echo "</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='precision' id='precision_p1' value='p1' "; if ($arrondir=='p1') echo "checked"; echo " />\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='precision_p1' style='cursor: pointer;'>";
	echo "Arrondir au dixième de point le plus proche";
	echo "</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='precision' id='precision_p5' value='p5' "; if ($arrondir=='p5') echo "checked"; echo " />\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='precision_p5' style='cursor: pointer;'>";
	echo "Arrondir au demi-point le plus proche";
	echo "</label>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<input type='radio' name='precision' id='precision_pe' value='pe' "; if ($arrondir=='pe') echo "checked"; echo " />\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='precision_pe' style='cursor: pointer;'>";
	echo "Arrondir au point entier le plus proche";
	echo "</label>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";




	echo "<h3 class='gepi'>Pondération</h3>\n";
	echo "<table>\n<tr><td>";
	echo "Pour chaque élève, le coefficient de la meilleure note de <b>$nom_court</b> augmente ou diminue de : &nbsp;</td>\n";
	echo "<td><input type='text' name='ponderation' id='ponderation' size='4' value = \"".$ponderation."\" onfocus=\"javascript:this.select()\" onkeydown=\"clavier_2(this.id,event,0,10);\" autocomplete=\"off\" /></td></tr>\n</table>\n";

	if ($parent != 0) {
		//s'il s'agit d'une boite à l'intérieur du conteneur principal, on laisse la possibilité d'afficher la note de la boite sur le bulletin.
		echo "<h3 class='gepi'>Affichage de la moyenne de $nom_court :</h3>\n";
		echo "<table>\n";
		echo "<tr>\n";

		echo "<td valign='top'>\n";
		echo "<input type='checkbox' name='display_parents' id='display_parents' "; if ($display_parents == 1) echo " checked";
		echo " />\n";
		echo "</td>\n";

		echo "<td>\n";
		echo "<label for='display_parents' style='cursor: pointer;'>";
		echo "Faire apparaître la moyenne sur le relevé de notes destiné aux parents";
		echo "</label>";
		echo "</td>\n";

		echo "</tr>\n";

		echo "<tr>\n";

		echo "<td valign='top'>\n";
		echo "<input type='checkbox' name='display_bulletin' id='display_bulletin'";
		if ($display_bulletin == 1) echo " checked"; echo " />\n";
		echo "</td>\n";

		echo "<td>\n";
		echo "<label for='display_bulletin' style='cursor: pointer;'>";
		echo "Faire apparaître la moyenne sur le bulletin scolaire.";
		echo "<br /><i>Si la case ci-contre est cochée, la moyenne de ce";
		if(getSettingValue("gepi_denom_boite_genre")=='f'){echo "tte";}
		echo " ".my_strtolower(getSettingValue("gepi_denom_boite"))." apparaît sur le bulletin scolaire, en plus de la moyenne générale, à titre d'information.</i>";
		echo "</label>";
		echo "</td>\n";

		echo "</tr>\n";
		echo "</table>\n";

	} else {
		echo "<input type='hidden' name='display_bulletin' value='yes' />\n";
	}
}

if ($new_conteneur=='yes')     echo "<input type=hidden name=new_conteneur value='yes' />\n";
if ($new_conteneur != 'no') echo "<input type=hidden name=id_racine value='$id_racine' />\n";
echo "<input type=hidden name=id_conteneur value='$id_conteneur' />\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";
echo "<input type=hidden name=id_retour value='$id_retour' />\n";

echo "<p style='text-align:center;'><input type=\"submit\" name='ok' value=\"Enregistrer\" style=\"font-variant: small-caps;\" /></p>\n";
echo "</form>\n";
/**
 * Pied de page
 */
require("../lib/footer.inc.php");
?>