<?php
/*
 * Last modification  : 03/12/2006
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

// Initialisations files
require_once("../lib/initialisations.inc.php");

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


//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
    die("Le module n'est pas activé.");
}

isset($id_retour);
$id_retour = isset($_POST["id_retour"]) ? $_POST["id_retour"] : (isset($_GET["id_retour"]) ? $_GET["id_retour"] : NULL);
isset($id_devoir);
$id_devoir = isset($_POST["id_devoir"]) ? $_POST["id_devoir"] : (isset($_GET["id_devoir"]) ? $_GET["id_devoir"] : NULL);
isset($mode_navig);
$mode_navig = isset($_POST["mode_navig"]) ? $_POST["mode_navig"] : (isset($_GET["mode_navig"]) ? $_GET["mode_navig"] : NULL);


if ($id_devoir)  {
    $query = mysql_query("SELECT id_conteneur, id_racine FROM cn_devoirs WHERE id = '$id_devoir'");
    $id_racine = mysql_result($query, 0, 'id_racine');
    $id_conteneur = mysql_result($query, 0, 'id_conteneur');
} else if ((isset($_POST['id_conteneur'])) or (isset($_GET['id_conteneur']))) {
    $id_conteneur = isset($_POST['id_conteneur']) ? $_POST['id_conteneur'] : (isset($_GET['id_conteneur']) ? $_GET['id_conteneur'] : NULL);
    $query = mysql_query("SELECT id_racine FROM cn_conteneurs WHERE id = '$id_conteneur'");
    $id_racine = mysql_result($query, 0, 'id_racine');
} else {
    header("Location: ../logout.php?auto=1");
    die();
}
//Configuration du calendrier
include("../lib/calendrier/calendrier.class.php");
$cal = new Calendrier("formulaire", "display_date");


// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
    header("Location: index.php?msg=$mess");
    die();
}

$appel_cahier_notes = mysql_query("SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe = mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group = get_group($id_groupe);
$periode_num = mysql_result($appel_cahier_notes, 0, 'periode');
include "../lib/periodes.inc.php";

// On teste si la periode est vérrouillée !
if ($current_group["classe"]["ver_periode"]["all"][$periode_num] <= 1) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes dont la période est bloquée !");
    header("Location: index.php?msg=$mess");
    die();
}

$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];

isset($id_devoir);
$id_devoir = isset($_POST["id_devoir"]) ? $_POST["id_devoir"] : (isset($_GET["id_devoir"]) ? $_GET["id_devoir"] : NULL);

// enregistrement des données
if (isset($_POST['ok'])) {
    $reg_ok = "yes";
    $new='no';
    if ((isset($_POST['new_devoir'])) and ($_POST['new_devoir'] == 'yes')) {
        $reg = mysql_query("insert into cn_devoirs (id_racine,id_conteneur,nom_court) values ('$id_racine','$id_conteneur','nouveau')");
        $id_devoir = mysql_insert_id();
        $new='yes';
        if (!$reg)  $reg_ok = "no";
    }
    if ($_POST['nom_court'])  {
        $nom_court = $_POST['nom_court'];
    } else {
        $nom_court = "Devoir ".$id_devoir;
    }
    $reg = mysql_query("UPDATE cn_devoirs SET nom_court = '".corriger_caracteres($nom_court)."' WHERE id = '$id_devoir'");
    if (!$reg)  $reg_ok = "no";

    if ($_POST['nom_complet'])  {
        $nom_complet = $_POST['nom_complet'];
    } else {
        $nom_complet = $nom_court;
    }

    $reg = mysql_query("UPDATE cn_devoirs SET nom_complet = '".corriger_caracteres($nom_complet)."' WHERE id = '$id_devoir'");
    if (!$reg)  $reg_ok = "no";
    if ($_POST['description'])  {
        $reg = mysql_query("UPDATE cn_devoirs SET description = '".corriger_caracteres($_POST['description'])."' WHERE id = '$id_devoir'");
        if (!$reg)  $reg_ok = "no";
    }
    if ($_POST['id_emplacement'])  {
        $id_emplacement = $_POST['id_emplacement'];
        $reg = mysql_query("UPDATE cn_devoirs SET id_conteneur = '".$id_emplacement."' WHERE id = '$id_devoir'");
        if (!$reg)  $reg_ok = "no";
    }


    if ($_POST['coef']) {
        $reg = mysql_query("UPDATE cn_devoirs SET coef = '".$_POST['coef']."' WHERE id = '$id_devoir'");
        if (!$reg)  $reg_ok = "no";
    } else {
        $reg = mysql_query("UPDATE cn_devoirs SET coef = '0' WHERE id = '$id_devoir'");
        if (!$reg)  $reg_ok = "no";
    }

    if (($_POST['facultatif']) and ereg("^(O|N|B)$", $_POST['facultatif'])) {
        $reg = mysql_query("UPDATE cn_devoirs SET facultatif = '".$_POST['facultatif']."' WHERE id = '$id_devoir'");
        if (!$reg)  $reg_ok = "no";
    }

    if ($_POST['display_date']) {
        if (ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $_POST['display_date'])) {
            $annee = substr($_POST['display_date'],6,4);
            $mois = substr($_POST['display_date'],3,2);
            $jour = substr($_POST['display_date'],0,2);
        } else {
            $annee = strftime("%Y");
            $mois = strftime("%m");
            $jour = strftime("%d");
        }
        $date = $annee."-".$mois."-".$jour." 00:00:00";
        $reg = mysql_query("UPDATE cn_devoirs SET date = '".$date."' WHERE id = '$id_devoir'");
        if (!$reg)  $reg_ok = "no";
    }


/*
    if (isset($_POST['display_parents'])) {
        $display_parents = 1;
    } else {
        $display_parents = 0;
    }
*/
    if (isset($_POST['display_parents'])) {
	if($_POST['display_parents']==1){
		$display_parents=1;
	}
	else{
		$display_parents=0;
	}
    } else {
        $display_parents=1;
    }

    $reg = mysql_query("UPDATE cn_devoirs SET display_parents = '$display_parents' WHERE id = '$id_devoir'");
    if (!$reg)  $reg_ok = "no";

    //==========================================================
    // MODIF: boireaus
    //
    // Mise à jour des moyennes du conteneur et des conteneurs parent, grand-parent, etc...
    //
    $arret = 'no';
    mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur,$arret);
    // La boite courante est mise à jour...
    // ... mais pas la boite destination.
    // Il faudrait rechercher pour $id_racine les derniers descendants et lancer la mise à jour sur chacun de ces descendants.
    function recherche_enfant($id_parent_tmp){
        global $current_group, $periode_num, $id_racine;
    $sql="SELECT * FROM cn_conteneurs WHERE parent='$id_parent_tmp'";
    //echo "<!-- $sql -->\n";
    $res_enfant=mysql_query($sql);
    if(mysql_num_rows($res_enfant)>0){
        while($lig_conteneur_enfant=mysql_fetch_object($res_enfant)){
            /*
            echo "<!-- nom_court=$lig_conteneur_enfant->nom_court -->\n";
            echo "<!-- nom_complet=$lig_conteneur_enfant->nom_complet -->\n";
            echo "<!-- id=$lig_conteneur_enfant->id -->\n";
            echo "<!-- parent=$lig_conteneur_enfant->parent -->\n";
            echo "<!-- recherche_enfant($lig_conteneur_enfant->id); -->\n";
            */
            recherche_enfant($lig_conteneur_enfant->id);
        }
    }
    else{
        $arret = 'no';
        $id_conteneur_enfant=$id_parent_tmp;
        //echo "<!-- mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur_enfant,$arret); -->\n";
        mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur_enfant,$arret);
        //echo "<!-- ========================================== -->\n";
    }
    }
    recherche_enfant($id_racine);
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
    } elseif($mode_navig == 'saisie_devoir'){
	//https://127.0.0.1/steph/gepi-cvs/cahier_notes/saisie_notes.php?id_conteneur=63&id_devoir=79
        header("Location: ./saisie_notes.php?id_conteneur=$id_conteneur&id_devoir=$id_devoir&msg=$msg");
        die();
    }
}

//-----------------------------------------------------------------------------------

if ($id_devoir)  {
    $new_devoir = 'no';
    $appel_devoir = mysql_query("SELECT * FROM cn_devoirs WHERE (id ='$id_devoir' and id_racine='$id_racine')");
    $nom_court = mysql_result($appel_devoir, 0, 'nom_court');
    $nom_complet = mysql_result($appel_devoir, 0, 'nom_complet');
    $description = mysql_result($appel_devoir, 0, 'description');
    $coef = mysql_result($appel_devoir, 0, 'coef');
    $facultatif = mysql_result($appel_devoir, 0, 'facultatif');
    $display_parents = mysql_result($appel_devoir, 0, 'display_parents');
    $date = mysql_result($appel_devoir, 0, 'date');
    $id_conteneur = mysql_result($appel_devoir, 0, 'id_conteneur');

    $annee = substr($date,0,4);
    $mois =  substr($date,5,2);
    $jour =  substr($date,8,2);
    $display_date = $jour."/".$mois."/".$annee;

} else {
    $nom_court = "Nouvelle évaluation";
    $nom_complet = "";
    $description = "";
    $new_devoir = 'yes';
    $coef = "1";
    $display_parents = "1";
    $facultatif = "O";
    $date = "";
    $annee = strftime("%Y");
    $mois = strftime("%m");
    $jour = strftime("%d");
    $display_date = $jour."/".$mois."/".$annee;

}
//**************** EN-TETE *****************
$titre_page = "Carnet de notes - Ajout/modification d'une évaluation";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"add_modif_dev.php\" method=\"post\">\n";
if ($mode_navig == 'retour_saisie') {
    echo "<div class='norme'><p class=bold><a href='./saisie_notes.php?id_conteneur=$id_retour'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
} else {
    echo "<div class='norme'><p class=bold><a href='index.php?id_racine=$id_racine'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
}

/*
if (isset($_POST['ok'])) {
	echo "|<a href='saisie_notes.php?id_conteneur=$id_conteneur&amp;id_devoir=$id_devoir'>Saisir</a>\n";
}
*/



function getPref($login,$item,$default){
	$sql="SELECT value FROM preferences WHERE login='$login' AND name='$item'";
	$res_prefs=mysql_query($sql);

	if(mysql_num_rows($res_prefs)>0){
		$ligne=mysql_fetch_object($res_prefs);
		return $ligne->value;
	}
	else{
		return $default;
	}
}


// Interface simplifiée
//$interface_simplifiee=isset($_POST['interface_simplifiee']) ? $_POST['interface_simplifiee'] : (isset($_GET['interface_simplifiee']) ? $_GET['interface_simplifiee'] : "");

$interface_simplifiee=isset($_POST['interface_simplifiee']) ? $_POST['interface_simplifiee'] : (isset($_GET['interface_simplifiee']) ? $_GET['interface_simplifiee'] : getPref($_SESSION['login'],'add_modif_dev_simpl','n'));


//echo "<a href='".$_SERVER['PHP_SELF']."?id_conteneur=$id_conteneur";
echo " | <a href='add_modif_dev.php?id_conteneur=$id_conteneur";
if(isset($mode_navig)){
	echo "&amp;mode_navig=$mode_navig";
}
if(isset($id_devoir)){
	echo "&amp;id_devoir=$id_devoir";
}
if(isset($id_retour)){
	echo "&amp;id_retour=$id_retour";
}
//if($interface_simplifiee!=""){
if($interface_simplifiee=="y"){
	echo "&amp;interface_simplifiee=n";
	echo "'>Interface complète</a>\n";
}
else{
	echo "&amp;interface_simplifiee=y";
	echo "'>Interface simplifiée</a>\n";
}

echo "\n";



//echo "<p class='bold'> Classe : $nom_classe | Matière : $matiere_nom ($matiere_nom_court)| Période : $nom_periode[$periode_num] <input type=\"submit\" name='ok' value=\"Enregistrer\" style=\"font-variant: small-caps;\" /></p>\n";
echo "<p class='bold'> Classe : $nom_classe | Matière : ".htmlentities("$matiere_nom ($matiere_nom_court)")."| Période : $nom_periode[$periode_num] <input type=\"submit\" name='ok' value=\"Enregistrer\" style=\"font-variant: small-caps;\" /></p>\n";
echo "</div>";


echo "<h2 class='gepi'>Configuration de l'évaluation :</h2>\n";



//if($interface_simplifiee!=""){
if($interface_simplifiee=="y"){
	// Récupérer les paramètres à afficher.
	// Dans un premier temps, un choix pour tous.
	// Dans le futur, permettre un paramétrage par utilisateur

	$aff_nom_court=getPref($_SESSION['login'],'add_modif_dev_nom_court','y');
	$aff_nom_complet=getPref($_SESSION['login'],'add_modif_dev_nom_complet','n');
	$aff_description=getPref($_SESSION['login'],'add_modif_dev_description','n');
	$aff_coef=getPref($_SESSION['login'],'add_modif_dev_coef','y');
	$aff_date=getPref($_SESSION['login'],'add_modif_dev_date','y');
	$aff_boite=getPref($_SESSION['login'],'add_modif_dev_boite','y');


	echo "<div align='center'>\n";
	echo "<table border='1'>\n";

	//#aaaae6
	//#aae6aa

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
		echo "<textarea name='description' rows='2' cols='40' wrap='virtual'>".$description."</textarea>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr style='display:none;'>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Description:</td>\n";
		echo "<td>\n";
		//echo "<textarea name='description' rows='2' cols='40' wrap='virtual'>".$description."</textarea>\n";
		echo "<input type='hidden' name='description' value='$description' />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}


	if($aff_coef=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Coefficient:</td>\n";
		echo "<td>\n";
		echo "<input type='text' name = 'coef' size='4' value = \"".$coef."\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr style='display:none;'>\n";
		echo "<td>Coefficient:</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name = 'coef' size='4' value = \"".$coef."\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}


	if($aff_date=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Date:</td>\n";
		echo "<td>\n";
		echo "<input type='text' name = 'display_date' size='10' value = \"".$display_date."\" />\n";
		echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr style='display:none;'>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Date:</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name = 'display_date' size='10' value = \"".$display_date."\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}


	if($aff_boite=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Emplacement de l'évaluation:</td>\n";
		echo "<td>\n";

		echo "<select size='1' name='id_emplacement'>\n";
		$appel_conteneurs = mysql_query("SELECT * FROM cn_conteneurs WHERE id_racine ='$id_racine' order by nom_court");
		$nb_cont = mysql_num_rows($appel_conteneurs);
		$i = 0;
		while ($i < $nb_cont) {
			$id_cont = mysql_result($appel_conteneurs, $i, 'id');
			$nom_conteneur = mysql_result($appel_conteneurs, $i, 'nom_court');
			echo "<option value='$id_cont' ";
			if ($id_cont == $id_conteneur) echo "selected";
			//echo " >$nom_conteneur</option>\n";
			if($nom_conteneur==""){echo " >---</option>\n";}else{echo " >$nom_conteneur</option>\n";}
			$i++;
		}
		echo "</select>\n";

		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr style='display:none;'>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Emplacement de l'évaluation:</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name='id_emplacement' size='10' value='$id_conteneur' />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}

/*	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "<a href='".$_SERVER['PHP_SELF']."?id_conteneur=$id_conteneur";
	if(isset($id_devoir)){
		echo "&amp;mode_navig=$mode_navig";
	}
	if(isset($id_devoir)){
		echo "&amp;id_devoir=$id_devoir";
	}
	echo "'>Interface complète</a>\n";
	echo "</td>\n";
	echo "</tr>\n";
*/
	echo "</table>\n";
	echo "</div>\n";
	echo "<input type='hidden' name='facultatif' value='$facultatif' />\n";
	echo "<input type='hidden' name='display_parents' value='$display_parents' />\n";
	echo "<input type='hidden' name='interface_simplifiee' value='$interface_simplifiee' />\n";

	//echo "<center><input type=\"submit\" name='ok' value=\"Enregistrer\" style=\"font-variant: small-caps;\" /></center>\n";
	//echo "<br />\n";
}
else{
	//====================================
	// Noms et conteneur
	// =================

	echo "<table>\n";
	//echo "<tr><td>Nom court : </td><td><input type='text' name = 'nom_court' size='40' value = \"".$nom_court."\" /></td></tr>\n";
	echo "<tr><td>Nom court : </td><td><input type='text' name = 'nom_court' size='40' value = \"".$nom_court."\" onfocus=\"javascript:this.select()\" /></td></tr>\n";
	//echo "<tr><td>Nom complet : </td><td><input type='text' name = 'nom_complet' size='40' value = \"".$nom_complet."\" /></td></tr>\n";
	echo "<tr><td>Nom complet : </td><td><input type='text' name = 'nom_complet' size='40' value = \"".$nom_complet."\" onfocus=\"javascript:this.select()\" /></td></tr>\n";
	echo "<tr><td>Description : </td><td><textarea name='description' rows='2' cols='40' wrap='virtual'>".$description."</textarea></td></tr></table>\n";
	echo "<br />\n";
	echo "<table><tr><td><h3 class='gepi'>Emplacement de l'évaluation : </h3></td>\n<td>";
	echo "<select size='1' name='id_emplacement'>\n";
	$appel_conteneurs = mysql_query("SELECT * FROM cn_conteneurs WHERE id_racine ='$id_racine' order by nom_court");
	$nb_cont = mysql_num_rows($appel_conteneurs);
	$i = 0;
	while ($i < $nb_cont) {
	$id_cont = mysql_result($appel_conteneurs, $i, 'id');
	$nom_conteneur = mysql_result($appel_conteneurs, $i, 'nom_court');
	echo "<option value='$id_cont' ";
	if ($id_cont == $id_conteneur) echo "selected";
	//echo " >$nom_conteneur</option>\n";
	if($nom_conteneur==""){echo " >---</option>\n";}else{echo " >$nom_conteneur</option>\n";}
	$i++;
	}
	echo "</select></td></tr></table>\n";

	//====================================
	// Coeff
	// =====

	echo "<h3 class='gepi'>Coefficient de l'évaluation</h3>\n";
	echo "<table><tr><td>Valeur de la pondération dans le calcul de la moyenne (si 0, la note de l'évaluation n'intervient pas dans le calcul de la moyenne) : </td>";
	echo "<td><input type='text' name = 'coef' size='4' value = \"".$coef."\" onfocus=\"javascript:this.select()\" /></td></tr></table>\n";

	//====================================
	// Statut
	// ======

	echo "<h3 class='gepi'>Statut de l'évaluation</h3>\n";
	echo "<table><tr><td><input type='radio' name='facultatif' value='O' "; if ($facultatif=='O') echo "checked"; echo " /></td><td>";
	echo "La note de l'évaluation entre dans le calcul de la moyenne.";
	echo "</td></tr>\n<tr><td><input type='radio' name='facultatif' value='B' "; if ($facultatif=='B') echo "checked"; echo " /></td><td>";
	echo "Seules les notes de l'évaluation supérieures à 10 entrent dans le calcul de la moyenne.";
	echo "</td></tr>\n<tr><td><input type='radio' name='facultatif' value='N' "; if ($facultatif=='N') echo "checked"; echo " /></td><td>";
	echo "La note de l'évaluation n'entre dans le calcul de la moyenne que si elle améliore la moyenne.";
	echo "</td></tr></table>\n";

	//====================================
	// Date
	// ====

	echo "<a name=\"calend\"></a><h3 class='gepi'>Date de l'évaluation (format jj/mm/aaaa) : </h3>
	<b>Remarque</b> : c'est cette date qui est prise en compte pour l'édition des relevés de notes à différentes périodes de l'année.
	<input type='text' name = 'display_date' size='10' value = \"".$display_date."\" />";
	echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";

	//====================================
	// Relevé de notes
	// ===============

	echo "<h3 class='gepi'>Affichage de la note sur le relevé de notes</h3>\n";
	echo "<table><tr><td>";
	echo "Faire apparaître la note de l'évaluation sur le relevé de notes de l'élève ";
	//echo "</td><td><input type='checkbox' name='display_parents' "; if ($display_parents == 1) echo " checked"; echo " /></td></tr></table>\n";
	echo "</td><td><input type='checkbox' name='display_parents' value='1' "; if ($display_parents == 1) echo " checked"; echo " /></td></tr></table>\n";

}

if ($new_devoir=='yes')     echo "<input type='hidden' name='new_devoir' value='yes' />\n";
echo "<input type='hidden' name='id_devoir' value='$id_devoir' />\n";
echo "<input type='hidden' name='id_conteneur' value='$id_conteneur' />\n";
echo "<input type='hidden' name='mode_navig' value='$mode_navig' />\n";
echo "<input type='hidden' name='id_retour' value='$id_retour' />\n";

//echo "<center><input type=\"submit\" name='ok' value=\"Enregistrer\" style=\"font-variant: small-caps;\" /></center>\n";
echo "<div style='display:none'><input type=\"hidden\" name='ok' value=\"Enregistrer\" /></div>\n";
echo "<center><input type=\"submit\" name='ok1' value=\"Enregistrer\" style=\"font-variant: small-caps;\" /></center>\n";
echo "<center><input type=\"button\" name='ok2' value=\"Enregistrer et saisir dans la foulée\" style=\"font-variant: small-caps;\" onClick=\"document.forms['formulaire'].mode_navig.value='saisie_devoir';document.forms['formulaire'].submit();\" /></center>\n";

echo "</form>\n";
echo "<br />\n";
require("../lib/footer.inc.php");
?>