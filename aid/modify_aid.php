<?php
/*
 * $Id: modify_aid.php 5907 2010-11-19 20:30:52Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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
//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

// Initialisation des variables
$flag = isset($_GET["flag"]) ? $_GET["flag"] : (isset($_POST["flag"]) ? $_POST["flag"] : NULL);
$aid_id = isset($_GET["aid_id"]) ? $_GET["aid_id"] : (isset($_POST["aid_id"]) ? $_POST["aid_id"] : NULL);
$indice_aid = isset($_GET["indice_aid"]) ? $_GET["indice_aid"] : (isset($_POST["indice_aid"]) ? $_POST["indice_aid"] : NULL);
$add_eleve = isset($_POST["add_eleve"]) ? $_POST["add_eleve"] : NULL;
$add_prof = isset($_POST["add_prof"]) ? $_POST["add_prof"] : NULL;
$add_prof_gest = isset($_POST["add_prof_gest"]) ? $_POST["add_prof_gest"] : NULL;
$reg_prof_login = isset($_POST["reg_prof_login"]) ? $_POST["reg_prof_login"] : NULL;
$reg_add_eleve_login = isset($_POST["reg_add_eleve_login"]) ? $_POST["reg_add_eleve_login"] : NULL;

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

// Vérification du niveau de gestion des AIDs
if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) <= 0) {
    header("Location: ../logout.php?auto=1");
    die();
}

if ((NiveauGestionAid($_SESSION["login"],$indice_aid) >= 5) and (isset($add_prof) and ($add_prof == "yes"))) {
	check_token();

    // On commence par vérifier que le professeur n'est pas déjà présent dans cette liste.
    $test = mysql_query("SELECT * FROM j_aid_utilisateurs WHERE (id_utilisateur = '$reg_prof_login' and id_aid = '$aid_id' and indice_aid='$indice_aid')");
    $test2 = mysql_num_rows($test);
    if ($test2 != "0") {
        $msg = "Le professeur que vous avez tenté d'ajouter appartient déjà à cet AID";
    } else {
        if ($reg_prof_login != '') {
            $reg_data = mysql_query("INSERT INTO j_aid_utilisateurs SET id_utilisateur= '$reg_prof_login', id_aid = '$aid_id', indice_aid='$indice_aid'");
            if (!$reg_data) { $msg = "Erreur lors de l'ajout du professeur !"; } else { $msg = "Le professeur a bien été ajouté !"; }
        }
    }
    $flag = "prof";
}

if ((NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10) and (isset($add_prof_gest) and ($add_prof_gest == "yes"))) {
	check_token();

    // On commence par vérifier que le professeur n'est pas déjà présent dans cette liste.
    $test = mysql_query("SELECT * FROM j_aid_utilisateurs_gest WHERE (id_utilisateur = '$reg_prof_login' and id_aid = '$aid_id' and indice_aid='$indice_aid')");
    $test2 = mysql_num_rows($test);
    if ($test2 != "0") {
        $msg = "L'utilisateur que vous avez tenté d'ajouter appartient déjà à la liste des gestionnaires de cette AID";
    } else {
        if ($reg_prof_login != '') {
            $reg_data = mysql_query("INSERT INTO j_aid_utilisateurs_gest SET id_utilisateur= '$reg_prof_login', id_aid = '$aid_id', indice_aid='$indice_aid'");
            if (!$reg_data) { $msg = "Erreur lors de l'ajout de l'utilisateur !"; } else { $msg = "L'utilisateur a bien été ajouté !"; }
        }
    }
    $flag = "prof_gest";
}

// On appelle les informations de l'aid pour les afficher :
$call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
$nom_aid = @mysql_result($call_data, 0, "nom");
$activer_outils_comp = @mysql_result($call_data, 0, "outils_complementaires");
$autoriser_inscript_multiples = @mysql_result($call_data, 0, "autoriser_inscript_multiples");

if (isset($add_eleve) and ($add_eleve == "yes")) {
	check_token();

    // Les élèves responsable : à chercher parmi les élèves de l'AID
    // On commence par supprimer les élèves responsables
    sql_query("delete from j_aid_eleves_resp where id_aid='$aid_id' and indice_aid='$indice_aid'");
    // Les élèves responsable sont à sélectionner parmi les élèves de l'AID
    $call_eleves = mysql_query("SELECT * FROM j_aid_eleves WHERE (indice_aid='$indice_aid' and id_aid='$aid_id')");
    $nombre = mysql_num_rows($call_eleves);
    $i = "0";
    while ($i < $nombre) {
        $login_eleve = mysql_result($call_eleves, $i, "login");
        if (isset($_POST[$login_eleve."_resp"])) {
            sql_query("insert into j_aid_eleves_resp set id_aid='$aid_id', login='$login_eleve', indice_aid='$indice_aid'");
        }
        $i++;
    }

    // On commence par vérifier que l'élève n'est pas déjà présent dans cette liste, ni dans aucune.
    if ($autoriser_inscript_multiples == 'y')
      $test = mysql_query("SELECT * FROM j_aid_eleves WHERE (login='$reg_add_eleve_login' and id_aid='$aid_id' and indice_aid='$indice_aid')");
    else
      $test = mysql_query("SELECT * FROM j_aid_eleves WHERE (login='$reg_add_eleve_login' and indice_aid='$indice_aid')");
    $test2 = mysql_num_rows($test);
    if ($test2 != "0") {
        $msg = "L'élève que vous avez tenté d'ajouter appartient déjà à une AID";
    } else {
        if ($reg_add_eleve_login != '') {
            $reg_data = mysql_query("INSERT INTO j_aid_eleves SET login='$reg_add_eleve_login', id_aid='$aid_id', indice_aid='$indice_aid'");
            if (!$reg_data) { $msg = "Erreur lors de l'ajout de l'élève"; } else { $msg = "L'élève a bien été ajouté."; }
        }
    }
    $msg .= "<br />Les modifications ont été enregistrées.";
    $flag = "eleve";

    // Cas où la catégorie d'AID est utilisée pour la gestion des accès au trombinoscope,
    // Lister (ou non) uniquement les élèves sans photographie.
    if ((getSettingValue("num_aid_trombinoscopes")==$indice_aid) and (getSettingValue("active_module_trombinoscopes")=='y')) {
        if(isset($_POST['eleves_sans_photos'])){
		    		$_SESSION['eleves_sans_photos']="y";
			  } else unset($_SESSION['eleves_sans_photos']);
    }
}

if (isset($_POST["toutes_aids"]) and ($_POST["toutes_aids"] == "y")) {
	check_token();

    $msg = "";
    // On récupère la liste des profs responsable de cette Aids :
    $sql = "SELECT id_utilisateur FROM j_aid_utilisateurs j WHERE (j.id_aid='$aid_id' and j.indice_aid='$indice_aid')";
    $query = mysql_query($sql) OR DIE('Erreur dans la requête : '.mysql_error());
    while($temp = mysql_fetch_array($query)) {
        $liste_profs[] = $temp["id_utilisateur"];
    }
    // On appelle toutes les aids de la catégorie
    $calldata = mysql_query("SELECT * FROM aid WHERE indice_aid='$indice_aid'");
    $nombreligne = mysql_num_rows($calldata);
    $i = 0;
    while ($i < $nombreligne){
        $aid_id = @mysql_result($calldata, $i, "id");
        // Y-a-il des profs responsables
        $test1 = sql_query1("SELECT count(id_utilisateur) FROM j_aid_utilisateurs WHERE id_aid = '$aid_id' and indice_aid='$indice_aid'");
        if ($test1 == 0) {
          // Pas de profs responsable donc on applique les changements
          foreach($liste_profs as $key){
              $reg_data = mysql_query("INSERT INTO j_aid_utilisateurs SET id_utilisateur= '$key', id_aid = '$aid_id', indice_aid='$indice_aid'");
              if (!$reg_data) { $msg .= "Erreur lors de l'ajout du professeur $key !<br />"; }
          }
        }
        $i++;
    }
    $flag = "prof";
    if ($msg == '') $msg = "Les modifications ont été enregistrées.";
}

if (isset($_POST["selection_aids"]) and ($_POST["selection_aids"] == "y")) {
	check_token();

    $msg = "";
    // On récupère la liste des profs responsable de cette Aids :
    $sql = "SELECT id_utilisateur FROM j_aid_utilisateurs j WHERE (j.id_aid='$aid_id' and j.indice_aid='$indice_aid')";
    $query = mysql_query($sql) OR DIE('Erreur dans la requête : '.mysql_error());
    while($temp = mysql_fetch_array($query)) {
        $liste_profs[] = $temp["id_utilisateur"];
    }
    foreach($_POST["liste_aids"] as $key1) {
      foreach($liste_profs as $key2){
        $test = sql_query1("SELECT id_utilisateur FROM j_aid_utilisateurs WHERE id_aid = '".$key1."' and id_utilisateur = '".$key2."' and indice_aid='$indice_aid'");
        if ($test == -1) {
          $reg_data = mysql_query("INSERT INTO j_aid_utilisateurs SET id_utilisateur= '$key2', id_aid = '$key1', indice_aid='$indice_aid'");
          if (!$reg_data) { $msg .= "Erreur lors de l'ajout du professeur $key2 à l'aid dont l'identifiant est $key1 !<br />"; }
        }
     }
    }
    $flag = "prof";
    if ($msg == '') $msg = "Les modifications ont été enregistrées.";
}


if (isset($_POST["toutes_aids_gest"]) and ($_POST["toutes_aids_gest"] == "y")) {
	check_token();

    $msg = "";
    // On récupère la liste des profs responsable de cette Aids :
    $sql = "SELECT id_utilisateur FROM j_aid_utilisateurs_gest j WHERE (j.id_aid='$aid_id' and j.indice_aid='$indice_aid')";
    $query = mysql_query($sql) OR DIE('Erreur dans la requête : '.mysql_error());
    while($temp = mysql_fetch_array($query)) {
        $liste_profs[] = $temp["id_utilisateur"];
    }
    // On appelle toutes les aids de la catégorie
    $calldata = mysql_query("SELECT * FROM aid WHERE indice_aid='$indice_aid'");
    $nombreligne = mysql_num_rows($calldata);
    $i = 0;
    while ($i < $nombreligne){
        $aid_id = @mysql_result($calldata, $i, "id");
        // Y-a-il des profs responsables
        $test1 = sql_query1("SELECT count(id_utilisateur) FROM j_aid_utilisateurs_gest WHERE id_aid = '$aid_id' and indice_aid='$indice_aid'");
        if ($test1 == 0) {
          // Pas de profs responsable donc on applique les changements
          foreach($liste_profs as $key){
              $reg_data = mysql_query("INSERT INTO j_aid_utilisateurs_gest SET id_utilisateur= '$key', id_aid = '$aid_id', indice_aid='$indice_aid'");
              if (!$reg_data) { $msg .= "Erreur lors de l'ajout de l'utilisateur $key !<br />"; }
          }
        }
        $i++;
    }
    $flag = "prof_gest";
    if ($msg == '') $msg = "Les modifications ont été enregistrées.";
}

$calldata = mysql_query("SELECT nom FROM aid where (id = '$aid_id' and indice_aid='$indice_aid')");
$aid_nom = mysql_result($calldata, 0, "nom");
$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];

if (!isset($_GET["aid_id"]) OR !isset($_GET["indice_aid"]) OR !isset($_GET["flag"])) {
	$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'].'?flag='.$flag.'&aid_id='.$aid_id.'&indice_aid='.$indice_aid;
}
// Ajout d'un style spécifique pour l'AID
$style_specifique = "aid/style_aid";

//**************** EN-TETE *********************
$titre_page = "Gestion des $nom_aid | Modifier les $nom_aid";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************


// On affiche un select avec la liste des aid de cette catégorie
if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 5)
    $sql = "SELECT id, nom FROM aid WHERE indice_aid = '".$indice_aid."' ORDER BY numero, nom";
else if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 1)
    $sql = "SELECT a.id, a.nom FROM aid a, j_aid_utilisateurs_gest j WHERE a.indice_aid = '".$indice_aid."' and j.id_utilisateur = '" . $_SESSION["login"] . "' and j.indice_aid = '".$indice_aid."' and  a.id=j.id_aid ORDER BY a.numero, a.nom";


$query = mysql_query($sql) OR DIE('Erreur dans la requête select * from aid : '.mysql_error());
$nbre = mysql_num_rows($query);

$aff_precedent = '';
$aff_suivant = '';

// On recherche les AID précédente et suivante
for($a = 0; $a < $nbre; $a++){
	$aid_p[$a]["id"] = mysql_result($query, $a, "id");

	// On teste pour savoir quel est le aid_id actuellement affiché
	if ($a != 0) {
		// Alors on propose un lien vers l'AID précédente
		if ($aid_p[$a]["id"] == $aid_id) {
			$aid_precedent = $aid_p[$a-1]["id"];
			$aff_precedent = '
			<a href="modify_aid.php?flag='.$flag.'&amp;indice_aid='.$indice_aid.'&amp;aid_id='.$aid_precedent.'">Aid précédente&nbsp;</a>';
		}
	}

	if ($a < ($nbre - 1)) {
		// alors on propose un lien vers l'AID suivante
		if ($aid_p[$a]["id"] == $aid_id) {
			$aid_suivant = mysql_result($query, $a+1, "id");
			$aff_suivant = '
			<a href="modify_aid.php?flag='.$flag.'&amp;indice_aid='.$indice_aid.'&amp;aid_id='.$aid_suivant.'">&nbsp;Aid suivante</a>';
		}
	}
}

echo '<form action="modify_aid.php" method="post" name="autre_aid">
<p class="bold"><a href="index2.php?indice_aid='.$indice_aid.'">
	<img src="../images/icons/back.png" alt="Retour" class="back_link" /> Retour</a>&nbsp;|&nbsp;'.$aff_precedent.'
		<select name="aid_id" onchange="document.autre_aid.submit();">
	';

// On recommence le query
$query = mysql_query($sql) OR trigger_error('Erreur dans la requête select * from aid : '.mysql_error(), E_USER_ERROR);
while($infos = mysql_fetch_array($query)){
	// On affiche la liste des "<option>"
	if ($aid_id == $infos["id"]) {
		$selected = ' selected="selected"';
	}else{
		$selected = '';
	}
	echo '<option value="'.$infos["id"].'"'.$selected.'>&nbsp;'.$infos["nom"].'&nbsp;</option>'."\n";
}
echo '
		</select>
		<input type="hidden" name="indice_aid" value="'.$indice_aid.'" />
		<input type="hidden" name="flag" value="'.$flag.'" />'.$aff_suivant.'
</p>
	</form>';


if ($flag == "prof") { ?>
   <p class='grand'><?php echo "$nom_aid  $aid_nom";?></p>
   <?php
    $call_liste_data = mysql_query("SELECT u.login, u.prenom, u.nom FROM utilisateurs u, j_aid_utilisateurs j WHERE (j.id_aid='$aid_id' and u.login=j.id_utilisateur and j.indice_aid='$indice_aid')  order by u.nom, u.prenom");
    $nombre = mysql_num_rows($call_liste_data);
    echo "<form enctype=\"multipart/form-data\" action=\"modify_aid.php\" method=\"post\">";

	echo add_token_field();

    if ($nombre !=0) {
    ?>
        <p class='bold'>Liste des professeurs responsables :</p>
        Les noms des professeurs ci-dessous figurent (selon le param&eacute;trage) sur les bulletins officiels et/ou les bulletins simplifi&eacute;s.<br />
        <?php
        if ($activer_outils_comp == "y")
              echo "De plus ces professeurs peuvent modifier les fiches projet (si l'administrateur a activé cette possibilité).";
        echo "<hr /><table class=\"aid_tableau\" border=\"0\" summary=''>\n";
    }
    $i = "0";
    while ($i < $nombre) {
        $login_prof = mysql_result($call_liste_data, $i, "login");
        $nom_prof = mysql_result($call_liste_data, $i, "nom");
        $prenom_prof = @mysql_result($call_liste_data, $i, "prenom");
        echo "<tr><td><b>";
        echo "$nom_prof $prenom_prof</b></td><td><a href='../lib/confirm_query.php?liste_cible=$login_prof&amp;liste_cible2=$aid_id&amp;liste_cible3=$indice_aid&amp;action=del_prof_aid".add_token_in_url()."'>\n<font size=2><img src=\"../images/icons/delete.png\" title=\"Supprimer ce professeur\" alt=\"Supprimer\" /></font></a></td>\n";
        echo "</tr>";
    $i++;
    }

    if ($nombre == 0) {
        echo "<h4 style=\"color: red;\">Il n'y a pas actuellement de professeur responsable !</h4>";
    } else {
        echo "</table>";
    }
    ?>
    <p class='bold'>Ajouter un professeur responsable à la liste de l'AID :</p>
    <select size=1 name="reg_prof_login">
    <!--option value=''><p>(aucun)</p></option-->
    <option value=''>(aucun)</option>
    <?php
    $call_prof = mysql_query("SELECT login, nom, prenom FROM utilisateurs WHERE  etat!='inactif' AND (statut = 'professeur' OR statut = 'autre') order by nom");
    $nombreligne = mysql_num_rows($call_prof);
    $i = "0" ;
    while ($i < $nombreligne) {
        $login_prof = mysql_result($call_prof, $i, 'login');
        $nom_el = mysql_result($call_prof, $i, 'nom');
        $prenom_el = mysql_result($call_prof, $i, 'prenom');

        echo "<option value=\"".$login_prof."\">".strtoupper($nom_el)." ".ucfirst(strtolower($prenom_el))."</option>\n";
    $i++;
    }
    ?>
    </select>
    <input type="hidden" name="add_prof" value="yes" />
    <input type="hidden" name="aid_id" value="<?php echo $aid_id;?>" />
    <input type="hidden" name="indice_aid" value="<?php echo $indice_aid;?>" />
    <input type="submit" value='Enregistrer' />
    </form>

    <?php
    if ($nombre != 0) {
    ?>
      <form enctype="multipart/form-data" action="modify_aid.php" method="post">
      <hr /><H2>Affecter cette liste aux Aids sans professeur responsable</H2>
      Si vous cliquez sur le bouton ci-dessous, les professeurs de la liste ci-dessus seront également affectés à toutes les AIDs de cette catégorie n'ayant pas encore de professeur responsable.
      <?php
		echo add_token_field();

      echo "<input type=\"hidden\" name=\"toutes_aids\" value=\"y\" />\n";
      echo "<input type=\"hidden\" name=\"indice_aid\" value=\"".$indice_aid."\" />\n";
      echo "<input type=\"hidden\" name=\"aid_id\" value=\"".$aid_id."\" />\n";
      echo "<br /><input type=\"submit\" value=\"Affecter la liste aux Aids sans professeur\" />\n";
      echo "</form>";
      ?>
      <form enctype="multipart/form-data" action="modify_aid.php" method="post">
      <hr /><H2>Affecter cette liste aux Aids s&eacute;lectionn&eacute;es</H2>
      <?php
		echo add_token_field();

      echo "<input type=\"hidden\" name=\"selection_aids\" value=\"y\" />\n";
      echo "<input type=\"hidden\" name=\"indice_aid\" value=\"".$indice_aid."\" />\n";
      echo "<input type=\"hidden\" name=\"aid_id\" value=\"".$aid_id."\" />\n";
      // On appelle toutes les aids de la catégorie
      $calldata = mysql_query("SELECT * FROM aid WHERE indice_aid='$indice_aid' ORDER BY numero, nom");
      $nombreligne = mysql_num_rows($calldata);
      $i = 0;
      echo "<select name=\"liste_aids[]\" size=\"6\" multiple>\n";
      while ($i < $nombreligne){
        $aid_id = @mysql_result($calldata, $i, "id");
        $aid_nom = @mysql_result($calldata, $i, "nom");
        echo "<option value=\"$aid_id\">$aid_nom</option>\n";
        $i++;
      }
      echo "</select>";

      echo "<br />Si vous cliquez sur le bouton ci-dessous, les professeurs de la liste de cette AID seront également affectés à toutes les AIDs s&eacute;lectionn&eacute;es ci-dessus.<br />";
      echo "<br /><input type=\"submit\" value=\"Affecter la liste aux Aids sélectionnées ci-dessus\" />\n";
      echo "</form>";

   }

}

if ($flag == "prof_gest") { ?>
   <p class='grand'><?php echo "$nom_aid  $aid_nom";?></p>
   <?php
    echo "<form enctype=\"multipart/form-data\" action=\"modify_aid.php\" method=\"post\">";

	echo add_token_field();

    $call_liste_data = mysql_query("SELECT u.login, u.prenom, u.nom FROM utilisateurs u, j_aid_utilisateurs_gest j WHERE (j.id_aid='$aid_id' and u.login=j.id_utilisateur and j.indice_aid='$indice_aid')  order by u.nom, u.prenom");
    $nombre = mysql_num_rows($call_liste_data);
    if ($nombre !=0) {
    ?>
        Les gestionnaires peuvent ajouter ou supprimer des &eacute;l&egrave;ves dans cette AID.
        <p class='bold'>Liste des utilisateurs gestionnaires :</p>
        <?php
        echo "<hr /><table class=\"aid_tableau\" border=\"0\" summary=''>\n";
    }
    $i = "0";
    while ($i < $nombre) {
        $login_prof = mysql_result($call_liste_data, $i, "login");
        $nom_prof = mysql_result($call_liste_data, $i, "nom");
        $prenom_prof = @mysql_result($call_liste_data, $i, "prenom");
        echo "<tr><td><b>";
        echo "$nom_prof $prenom_prof</b></td><td><a href='../lib/confirm_query.php?liste_cible=$login_prof&amp;liste_cible2=$aid_id&amp;liste_cible3=$indice_aid&amp;action=del_gest_aid".add_token_in_url()."'>\n<font size=2><img src=\"../images/icons/delete.png\" title=\"Supprimer ce professeur\" alt=\"Supprimer\" /></font></a></td>\n";
        echo "</tr>";
    $i++;
    }

    if ($nombre == 0) {
        echo "<h4 style=\"color: red;\">Il n'y a pas actuellement d'utilisateur gestionnaire !</h4>";
    } else {
        echo "</table>";
    }
    ?>
    <p class='bold'>Ajouter un utilisateur à la liste des gestionnaires de l'AID :</p>
    <select size=1 name="reg_prof_login">
    <option value=''>(aucun)</option>
    <?php
    $call_prof = mysql_query("SELECT login, nom, prenom FROM utilisateurs WHERE  etat!='inactif' AND (statut = 'professeur' or statut = 'cpe' or statut = 'scolarite') order by nom, prenom");
    $nombreligne = mysql_num_rows($call_prof);
    $i = "0" ;
    while ($i < $nombreligne) {
        $login_prof = mysql_result($call_prof, $i, 'login');
        $nom_el = mysql_result($call_prof, $i, 'nom');
        $prenom_el = mysql_result($call_prof, $i, 'prenom');
        //echo "<option value=\"".$login_prof."\">".$nom_el." ".$prenom_el."</option>\n";
        echo "<option value=\"".$login_prof."\">".strtoupper($nom_el)." ".ucfirst(strtolower($prenom_el))."</option>\n";
    $i++;
    }
    ?>
    </select>
    <input type="hidden" name="add_prof_gest" value="yes" />
    <input type="hidden" name="aid_id" value="<?php echo $aid_id;?>" />
    <input type="hidden" name="indice_aid" value="<?php echo $indice_aid;?>" />
    <input type="submit" value='Enregistrer' />
    </form>

    <?php
    if ($nombre != 0) {
    ?>
      <form enctype="multipart/form-data" action="modify_aid.php" method="post">
      <hr /><H2>Affecter cette liste aux Aids sans gestionnaire</H2>
      Si vous cliquez sur le bouton ci-dessous, les utilisateurs de la liste ci-dessus seront également affectés à toutes les AIDs de cette catégorie n'ayant pas encore de gestionnaire.
      <?php
		echo add_token_field();

      echo "<input type=\"hidden\" name=\"toutes_aids_gest\" value=\"y\" />\n";
      echo "<input type=\"hidden\" name=\"indice_aid\" value=\"".$indice_aid."\" />\n";
      echo "<input type=\"hidden\" name=\"aid_id\" value=\"".$aid_id."\" />\n";
      echo "<br /><input type=\"submit\" value=\"Affecter la liste aux Aids sans gestionnaire\" />\n";
      echo "</form>";
   }
}

if ($flag == "eleve") {
	// On ajoute le nom des profs et le nombre d'élèves
	$aff_profs = "<font style=\"color: brown; font-size: 12px;\">(";
	$req_profs = mysql_query("SELECT id_utilisateur FROM j_aid_utilisateurs WHERE id_aid = '".$aid_id."'");
	$nbre_profs = mysql_num_rows($req_profs);
	for($a=0; $a<$nbre_profs; $a++) {
		$rep_profs[$a]["id_utilisateur"] = mysql_result($req_profs, $a, "id_utilisateur");
		$rep_profs_a = mysql_fetch_array(mysql_query("SELECT nom, civilite FROM utilisateurs WHERE login = '".$rep_profs[$a]["id_utilisateur"]."'"));
		$aff_profs .= "".$rep_profs_a["civilite"].$rep_profs_a["nom"]." ";
	}
		$aff_profs .= ")</font>";
?>
    <p class='grand'><?php echo "$nom_aid  $aid_nom. $aff_profs"; ?></p>

    <p class = 'bold'>Liste des élèves de l'AID <?php echo $aid_nom ?> :</p>
    <hr />
    <?php
    $vide = 1;
    // Ajout d'un tableau
echo "<form enctype=\"multipart/form-data\" action=\"modify_aid.php\" method=\"post\">\n";

	echo add_token_field();

	echo "<table class=\"aid_tableau\" border=\"0\" summary=''>";
    // appel de la liste des élèves de l'AID :
    $call_liste_data = mysql_query("SELECT DISTINCT e.login, e.nom, e.prenom, e.elenoet
							FROM eleves e, j_aid_eleves j, j_eleves_classes jec, classes c
							WHERE (j.id_aid = '$aid_id' and
							e.login = j.login and
							jec.id_classe = c.id and
							jec.login = j.login AND
							j.indice_aid = '$indice_aid')
							ORDER BY c.classe, e.nom, e.prenom");
    $nombre = mysql_num_rows($call_liste_data);
    // On affiche d'abord le nombre d'élèves
    		$s = "";
		if ($nombre >= 2) {
			$s = "s";
		}
		else {
			$s = "";
		}
    echo "<tr><td>\n";
    echo $nombre." élève".$s.".\n</td><td></td>";
    if ($activer_outils_comp == "y") {
      echo "<td>Elève responsable (*)</td>";
    }
    echo "</tr>\n";
    $i = "0";
    while ($i < $nombre) {
        $vide = 0;
        $login_eleve = mysql_result($call_liste_data, $i, "login");
        $nom_eleve = mysql_result($call_liste_data, $i, "nom");
        $prenom_eleve = @mysql_result($call_liste_data, $i, "prenom");
        $eleve_resp = sql_query1("select login from j_aid_eleves_resp where id_aid='$aid_id' and login ='$login_eleve' and indice_aid='$indice_aid'");
        $call_classe = mysql_query("SELECT c.classe FROM classes c, j_eleves_classes j WHERE (j.login = '$login_eleve' and j.id_classe = c.id) order by j.periode DESC");
        $classe_eleve = @mysql_result($call_classe, '0', "classe");
        $v_elenoet=mysql_result($call_liste_data, $i, 'elenoet');
        echo "<tr><td>\n";
        echo "<b>$nom_eleve $prenom_eleve</b>, $classe_eleve </td>\n<td> <a href='../lib/confirm_query.php?liste_cible=$login_eleve&amp;liste_cible2=$aid_id&amp;liste_cible3=$indice_aid&amp;action=del_eleve_aid".add_token_in_url()."'><img src=\"../images/icons/delete.png\" title=\"Supprimer cet élève\" alt=\"Supprimer\" /></a>\n";

        // Dans le cas où la catégorie d'AID est utilisée pour la gestion des accès au trombinoscope, on ajouter un lien sur la photo de l'élève.
        if ((getSettingValue("num_aid_trombinoscopes")==$indice_aid) and (getSettingValue("active_module_trombinoscopes")=='y')) {
          $info="<div align='center'>\n";
      	  if($v_elenoet!=""){
		        $photo=nom_photo($v_elenoet);
//		        if($photo!=""){
		        if($photo){
			          //$info.="<img src='../photos/eleves/".$photo."' width='150' alt=\"photo\" />";
			          $info.="<img src='".$photo."' width='150' alt=\"photo\" />";
		        }
	        }
      	  $info.="</div>\n";
      	  $tabdiv_infobulle[]=creer_div_infobulle('info_popup_eleve'.$v_elenoet,$titre,"",$info,"",14,0,'y','y','n','n');

		      if($photo!="") {
       	    echo "<a href='#' onmouseover=\"afficher_div('info_popup_eleve".$v_elenoet."','y',30,-200);\"";
	          echo " onmouseout=\"cacher_div('info_popup_eleve".$v_elenoet."');\">";
	          echo "<img src='../images/icons/buddy.png' alt='Photo élève' />";
	          echo "</a>";
	        } else {
	          echo "<img src='../images/icons/buddy_no.png' alt='Pas de photo' />";
          }
        }

        echo "</td>";
        if ($activer_outils_comp == "y") {
            echo "<td><center><input type=\"checkbox\" name=\"".$login_eleve."_resp\" value=\"y\" ";
            if ($eleve_resp!=-1) echo " checked ";
        echo "/></center></td>";
        }
        echo "</tr>\n";
    $i++;
    }

    echo "</table>";

    if ($vide == 1) {
        echo "<br /><p style=\"color: red;\">Il n'y a pas actuellement d'élèves dans cette AID !</p>";
    }
    if ($autoriser_inscript_multiples == 'y')
      $requete = "SELECT distinct e.login, e.nom, e.prenom, e.elenoet
						FROM eleves e LEFT JOIN j_aid_eleves j ON
						(e.login = j.login  and
  						j.id_aid = '$aid_id')  WHERE j.login is null order by e.nom, e.prenom";
    else
        $requete = "SELECT e.login, e.nom, e.prenom, e.elenoet
						FROM eleves e LEFT JOIN j_aid_eleves j ON
						(e.login = j.login  and
						j.indice_aid = '$indice_aid') WHERE j.login is null order by e.nom, e.prenom";
    $call_eleve = mysql_query($requete);
    $nombreligne = mysql_num_rows($call_eleve);
    if ($nombreligne != 0) {

        if (getSettingValue("num_aid_trombinoscopes")==$indice_aid) {
            echo "<br />Ci-dessous, lister uniquement les élèves sans photographie <input type=\"checkbox\" name=\"eleves_sans_photos\" value=\"y\" ";
            if(isset($_SESSION['eleves_sans_photos'])) echo " checked ";
            echo "/>";
        }

        echo "<br />\n<p><span class = 'bold'>Ajouter un élève à la liste de l'AID :</span>\n";
        echo "<a href=\"modify_aid_new.php?id_aid=".$aid_id."&amp;indice_aid=".$indice_aid."\">Lister les élèves par classe</a>\n";
        echo "<br /><select size=\"1\" name=\"reg_add_eleve_login\">";
        //echo "<option value=''><p>(aucun)</p></option>";
        echo "<option value=''>(aucun)</option>\n";
        $i = "0" ;
        while ($i < $nombreligne) {
            $eleve = mysql_result($call_eleve, $i, 'login');
            $nom_el = mysql_result($call_eleve, $i, 'nom');
            $prenom_el = mysql_result($call_eleve, $i, 'prenom');
            $v_elenoet=mysql_result($call_eleve, $i, 'elenoet');
            $photo=nom_photo($v_elenoet);
            if (isset($_SESSION['eleves_sans_photos']) and ($photo!=""))
                $affiche_ligne = "no";
            else
                $affiche_ligne = "yes";
            if ($affiche_ligne == "yes") {
            $call_classe = mysql_query("SELECT c.classe FROM classes c, j_eleves_classes j WHERE (j.login = '$eleve' and j.id_classe = c.id) order by j.periode DESC");
            $classe_eleve = @mysql_result($call_classe, '0', "classe");
            //echo "<option value=\"$eleve\">$nom_el  $prenom_el $classe_eleve</option>\n";
	        echo "<option value=\"".$eleve."\">".strtoupper($nom_el)." ".ucfirst(strtolower($prenom_el))." $classe_eleve</option>\n";
            }
        $i++;
        }
        ?>
        </select>


<?php } else {
        echo "<p>Tous les élèves de la base ont une AID. Impossible d'ajouter un élève à cette AID !</p>";
    }
    ?>
    <input type="hidden" name="add_eleve" value="yes" />
    <input type="hidden" name="indice_aid" value="<?php echo $indice_aid;?>" />
    <input type="hidden" name="aid_id" value="<?php echo $aid_id;?>" />
    <input type="submit" value='Enregistrer' />
    </form>
    <?php if (getSettingValue("num_aid_trombinoscopes")==$indice_aid) echo "<br /><br /><br /><br /><br />"; // Pour que les trombines des derniers élèves s'affichent correctement.
    if ($activer_outils_comp == "y") {?>
    <p><br />(*) Les &eacute;l&egrave;ves responsables peuvent par exemple acc&eacute;der dans certaines conditions &agrave; l'&eacute;dition des fiches AID.
    <?php }

}
require ("../lib/footer.inc.php");
?>
