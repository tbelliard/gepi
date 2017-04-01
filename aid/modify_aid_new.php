<?php

/**
 * Document destiné à constituer les AID (élèves) en partant d'un lot de classes.
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

// Initialisation
require_once("../lib/initialisations.inc.php");

// Les fonctions de Gepi
require_once("../lib/share.inc.php");

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

include_once 'fonctions_aid.php';
global $mysqli;

//Initialisation des variables
$id_aid = \filter_input(\INPUT_POST, 'id_aid') !== NULL ? \filter_input(\INPUT_POST, 'id_aid') : (\filter_input(\INPUT_GET, 'id_aid') !== NULL ? \filter_input(\INPUT_GET, 'id_aid') : NULL);
$indice_aid = \filter_input(\INPUT_POST, 'indice_aid') !== NULL ? \filter_input(\INPUT_POST, 'indice_aid') : (\filter_input(\INPUT_GET, 'indice_aid') !== NULL ? \filter_input(\INPUT_GET, 'indice_aid') : NULL);
$aff_liste_m = \filter_input(\INPUT_POST, 'classe') ? \filter_input(\INPUT_POST, 'classe') : (\filter_input(\INPUT_GET, 'classe') ? \filter_input(\INPUT_GET, 'classe') : NULL);
$choix_aid = \filter_input(\INPUT_POST, 'choix_aid') ? \filter_input(\INPUT_POST, 'choix_aid') : (\filter_input(\INPUT_GET, 'choix_aid') ? \filter_input(\INPUT_GET, 'choix_aid') : NULL);
$id_eleve = \filter_input(\INPUT_POST, 'id_eleve') ? \filter_input(\INPUT_POST, 'id_eleve') : (\filter_input(\INPUT_GET, 'id_eleve') ? \filter_input(\INPUT_GET, 'id_eleve') : NULL);
$eleve = \filter_input(\INPUT_POST, 'eleve') ? \filter_input(\INPUT_POST, 'eleve') : (\filter_input(\INPUT_GET, 'eleve') ? \filter_input(\INPUT_GET, 'eleve') : NULL);
$action = \filter_input(\INPUT_POST, 'action') ? \filter_input(\INPUT_POST, 'action') : (\filter_input(\INPUT_GET, 'action') ? \filter_input(\INPUT_GET, 'action') : NULL);

$aff_infos_g = "";
$aff_classes_g = "";
$aff_aid_d = "";
$aff_classes_m = "";

$autoriser_inscript_multiples = Multiples_possible ($indice_aid);
$sous_groupe = a_parent ($id_aid, $indice_aid) ? Extrait_parent ($id_aid)->fetch_object()->parent : NULL;
$msg = '';
//+++++++++++++++++ CSS AID++++++++
$style_specifique = "aid/style_aid";
//+++++++++++++++++ AJAX AID ++++++
	// En attente de fonctionnement
$utilisation_prototype = "ok";
$javascript_specifique = "aid/aid_ajax";

// Vérification du niveau de gestion des AIDs
if (NiveauGestionAid($_SESSION["login"],$indice_aid,$id_aid) <= 0) {
    header("Location: ../logout.php?auto=1");
    die();
}

	//================ TRAITEMENT des entrées ===================
	if (isset($aff_liste_m) AND isset($id_aid) AND isset($id_eleve) AND isset($indice_aid)) {
		check_token(false);

		// Cas de la classe entière
		if ($id_eleve == "tous") {
			// On récupère tous les login de cette classe
			
			if (isset($sous_groupe) && $sous_groupe !== null) {
				$filtre = " INNER JOIN j_aid_eleves j2 ON ( e.login = j2.login "
				   . "AND j2.id_aid = '".$sous_groupe."' ) ";
			} else {
				$filtre ="";
			}
			
			$sql_login = "SELECT DISTINCT e.login FROM j_eleves_classes e ".$filtre;
			$sql_login .= "WHERE e.id_classe = '".$aff_liste_m."' ORDER BY e.login";
			$req_login = mysqli_query($GLOBALS["mysqli"], $sql_login);
			$nbre_login = mysqli_num_rows($req_login);
			for($i=0; $i<$nbre_login; $i++){
				$rep_log_eleve[$i]["login"] = old_mysql_result($req_login, $i, "login");
				// On teste si cet élève n'est pas déjà membre de l'AID
				if (!$autoriser_inscript_multiples) {
					$req_verif = Eleve_est_deja_membre ($rep_log_eleve[$i]["login"], $indice_aid);
        		} else {
					$req_verif = Eleve_est_deja_membre ($rep_log_eleve[$i]["login"], $indice_aid, $id_aid);
				} 
				$verif = $req_verif->num_rows;
				if ($verif === 0) {
					$req_ajout = Sauve_eleve_membre($id_aid, $indice_aid, $rep_log_eleve[$i]["login"]);
				}else {
					$msg .= get_nom_prenom_eleve($rep_log_eleve[$i]["login"])." est déjà dans la table.".'<br />';
				}
			}
		} else {
			// On intègre cet élève dans la base s'il n'y est pas déjà
			// Pour l'instant on récupère son login à partir de id_eleve
			$rep_log_eleve = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT login FROM eleves WHERE id_eleve = '".$id_eleve."'"));
			// On vérifie s'il n'est pas déjà membre de cet aid
			// Par cette méthode, on ne peut enregistrer deux fois le même
			$req_ajout = Sauve_eleve_membre($id_aid, $indice_aid, $rep_log_eleve["login"]);
		}// fin du else
	}

	//================= TRAITEMENT des sorties =======================
	// Attention de penser à sortir les lignes des notes et appréciations si elles existent
	if (isset($action) AND $action == "del_eleve_aid") {
		check_token(false);

		// On supprime l'élève de l'AID
		$req_suppr1 = mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_aid_eleves WHERE login='".$eleve."' and id_aid = '".$id_aid."' and indice_aid='".$indice_aid."'");
		$req_suppr2 = mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_aid_eleves_resp WHERE login='".$eleve."' and id_aid = '".$id_aid."' and indice_aid='".$indice_aid."'");
		//On teste ensuite si cet élève avait des appréciations / notes
		$req_test_notes = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_appreciations WHERE login='".$eleve."' and id_aid = '".$id_aid."' and indice_aid='".$indice_aid."'");
		$test_notes = mysqli_num_rows($req_test_notes);
		if ($test_notes !== 0) {
			$suppr_notes = mysqli_query($GLOBALS["mysqli"], "DELETE FROM aid_appreciations WHERE login='".$eleve."' and id_aid = '".$id_aid."' and indice_aid='".$indice_aid."'");
		}
	} //if isset($action...

	

//**************** EN-TETE **************************************
$titre_page = "Gestion des élèves dans les AID";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

// Affichage du retour
/*
	// On récupère l'indice de l'aid en question
	$aff_infos_g .= "<p class=\"aid_a\">"
	   . "<a href=\"modify_aid.php?flag=eleve&amp;aid_id=".$id_aid."&amp;indice_aid=".$indice_aid.add_token_in_url()."\">"
	   . "<img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour"
	   . "</a>"
	   . "</p>";
*/
$NiveauGestionAid_categorie=NiveauGestionAid($_SESSION["login"],$indice_aid);
$NiveauGestionAid_AID_courant=NiveauGestionAid($_SESSION["login"],$indice_aid, $id_aid);

// On affiche un select avec la liste des aid de cette catégorie
if ($NiveauGestionAid_AID_courant >= 5) {
    $sql = "SELECT id, nom FROM aid WHERE indice_aid = '".$indice_aid."' ORDER BY numero, nom";
}
else if ($NiveauGestionAid_AID_courant >= 1) {
    $sql = "SELECT a.id, a.nom FROM aid a, j_aid_utilisateurs_gest j WHERE a.indice_aid = '".$indice_aid."' and j.id_utilisateur = '" . $_SESSION["login"] . "' and j.indice_aid = '".$indice_aid."' and  a.id=j.id_aid ORDER BY a.numero, a.nom";
}

$query = mysqli_query($GLOBALS["mysqli"], $sql) OR DIE('Erreur dans la requête select * from aid : '.mysqli_error($GLOBALS["mysqli"]));
$nbre = mysqli_num_rows($query);

$aff_precedent = '';
$aff_suivant = '';

// On recherche les AID précédente et suivante
for($a = 0; $a < $nbre; $a++){
	$aid_p[$a]["id"] = old_mysql_result($query, $a, "id");

	// On teste pour savoir quel est le aid_id actuellement affiché
	if ($a != 0) {
		// Alors on propose un lien vers l'AID précédente
		if ($aid_p[$a]["id"] == $id_aid) {
			$aid_precedent = $aid_p[$a-1]["id"];
			$aff_precedent = '
			<a href="modify_aid_new.php?indice_aid='.$indice_aid.'&amp;id_aid='.$aid_precedent.'" onclick="return confirm_abandon (this, change, \''.$themessage.'\')">Aid précédente&nbsp;</a>';
		}
	}

	if ($a < ($nbre - 1)) {
		// alors on propose un lien vers l'AID suivante
		if ($aid_p[$a]["id"] == $id_aid) {
			$aid_suivant = old_mysql_result($query, $a+1, "id");
			$aff_suivant = '
			<a href="modify_aid_new.php?indice_aid='.$indice_aid.'&amp;id_aid='.$aid_suivant.'" onclick="return confirm_abandon (this, change, \''.$themessage.'\')">&nbsp;Aid suivante</a>';
		}
	}
}
?>
<form action="modify_aid_new.php" method="post" name="autre_aid" style='margin-bottom:1em;'>
	<p class="bold">
		<!--a href="index2.php?indice_aid=<?php echo $indice_aid; ?>" onclick="return confirm_abandon (this, change, '<?php echo $themessage;?>')"-->
		<a href="modify_aid.php?flag=eleve&aid_id=<?php echo $id_aid; ?>&indice_aid=<?php echo $indice_aid; ?>" onclick="return confirm_abandon (this, change, '<?php echo $themessage;?>')">
			<img src="../images/icons/back.png" alt="Retour" class="back_link" />
			Retour
		</a>&nbsp;|&nbsp;<?php echo $aff_precedent; ?>
		<select name="id_aid" id='aid_id_autre_aid' onchange="confirm_changement_aid(change, '<?php echo $themessage;?>');">
<?php
$indice_aid_champ_select=-1;
$compteur_aid=0;
// On recommence le query
$query = mysqli_query($GLOBALS["mysqli"], $sql) OR trigger_error('Erreur dans la requête select * from aid : '.mysqli_error($GLOBALS["mysqli"]), E_USER_ERROR);
while($infos = mysqli_fetch_array($query)){
	// On affiche la liste des "<option>"
	if ($id_aid == $infos["id"]) {
		$selected = ' selected="selected" ';
		$indice_aid_champ_select=$compteur_aid;
	}else{
		$selected = '';
	}
?>
			<option value="<?php echo $infos["id"]; ?>"<?php echo $selected; ?>>
				&nbsp;<?php echo $infos["nom"]; ?>&nbsp;
			</option>
<?php
	$compteur_aid++;
}
?>
		</select>
		
		<input type="hidden" name="indice_aid" value="<?php echo $indice_aid; ?>" />
		<input type="hidden" name="flag" value="<?php echo $flag; ?>" /><?php echo $aff_suivant; ?>

<?php
	if(acces("/groupes/mes_listes.php", $_SESSION['statut'])) {
		echo "
		| <a href='../groupes/mes_listes.php#aid' onclick=\"return confirm_abandon (this, change, '$themessage')\">Export CSV</a>";
	}
	if((getSettingAOui('active_module_trombinoscopes'))&&(acces("/mod_trombinoscopes/trombinoscopes.php", $_SESSION['statut']))) {
		echo "
		| <a href='../mod_trombinoscopes/trombinoscopes.php?aid=$id_aid&etape=2' onclick=\"return confirm_abandon (this, change, '$themessage')\">Trombinoscope</a>";
	}
	if(((!isset($flag))||($flag!="prof"))&&(($NiveauGestionAid_AID_courant>=2))) {
		echo "
		| <a href='".$_SERVER['PHP_SELF']."?flag=prof&aid_id=".$id_aid."&indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Professeurs de l'AID</a>";
	}
	if(((!isset($flag))||($flag!="prof_gest"))&&(($NiveauGestionAid_AID_courant>=5))) {
		echo "
		| <a href='".$_SERVER['PHP_SELF']."?flag=prof_gest&aid_id=".$id_aid."&indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestionnaires de l'AID</a>";
	}
	if($NiveauGestionAid_categorie==10) {
		echo "
		| <a href='config_aid.php?indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Catégorie AID</a>";
	}
?>
	</p>

	<script type='text/javascript'>
		//onchange="document.autre_aid.submit();"

		// Initialisation
		change='no';

		function confirm_changement_aid(thechange, themessage)
		{
			if (!(thechange)) thechange='no';
			if (thechange != 'yes') {
				document.autre_aid.submit();
			}
			else{
				var is_confirmed = confirm(themessage);
				if(is_confirmed){
					document.autre_aid.submit();
				}
				else{
					document.getElementById('aid_id_autre_aid').selectedIndex=<?php echo $indice_aid_champ_select;?>;
				}
			}
		}

	</script>
</form>
<?php


//Affichage du nom et des précisions sur l'AID en question
	$req_aid = mysqli_query($GLOBALS["mysqli"], "SELECT nom FROM aid WHERE id = '".$id_aid."'");
	$rep_aid = mysqli_fetch_array($req_aid);
	$aff_infos_g .= "<p class=\"bold\">Liste des classes</p>\n";

// Affichage de la liste des classes par $aff_classes_g

	$req_liste_classe = mysqli_query($GLOBALS["mysqli"], "SELECT id, classe FROM classes ORDER BY classe");
	$nbre_classe = mysqli_num_rows($req_liste_classe);

	for($a=0; $a<$nbre_classe; $a++) {
		$liste_classe[$a]["id"] = old_mysql_result($req_liste_classe, $a, "id");
		$liste_classe[$a]["classe"] = old_mysql_result($req_liste_classe, $a, "classe");

		$aff_classes_g .= "<tr><td style=\"width: 196px;\"><a href=\"./modify_aid_new.php?id_aid=".$id_aid."&amp;classe=".$liste_classe[$a]["id"]."&amp;indice_aid=".$indice_aid.add_token_in_url()."\">Elèves de la ".$liste_classe[$a]["classe"]."</a></td></tr>\n";
	}

// Affichage de la liste des élèves de la classe choisie (au milieu) par $aff_classes_m

if (isset($aff_liste_m)) {

	$aff_nom_classe = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM classes WHERE id = '".$aff_liste_m."'"));

	// Récupérer la liste des élèves de la classe en question
	if (isset($sous_groupe) && $sous_groupe !== null) {
		$filtre = " INNER JOIN j_aid_eleves j2 ON ( e.login = j2.login "
		   . "AND j2.id_aid = '".$sous_groupe."' ) ";
	} else {
		$filtre ="";
	}
	
	$sql="SELECT DISTINCT e.login, e.id_eleve, nom, prenom, sexe "
	   . "FROM j_eleves_classes jec, eleves e ".$filtre;
	$sql .="WHERE id_classe = '".$aff_liste_m."' "
	   . "AND jec.login = e.login ORDER BY nom, prenom";
	$req_ele = mysqli_query($GLOBALS["mysqli"], $sql) OR die('Erreur dans la requête $req_ele : '.mysqli_error($GLOBALS["mysqli"]));
	$nbre_ele_m = mysqli_num_rows($req_ele);

	$aff_classes_m .= "
		<p class=\"red\">Classe de ".$aff_nom_classe["classe"]." : </p>

	<table class=\"aid_tableau\" summary=\"Liste des élèves\">
	";
		// Ligne paire, ligne impaire (inutile dans un premier temps), on s'en sert pour faire ladifférence avec une ligne vide.
			$aff_tr_css = "aid_lignepaire";
		// On ajoute un lien qui permet d'intégrer toute la classe d'un coup
		$aff_classes_m .= "
		<tr class=\"".$aff_tr_css."\">
			<td>
				<a href=\"modify_aid_new.php?classe=".$aff_liste_m."&amp;id_eleve=tous&amp;id_aid=".$id_aid."&amp;indice_aid=".$indice_aid.add_token_in_url()."\">
				<img src=\"../images/icons/add_user.png\" alt=\"Ajouter\" title=\"Ajouter\" /> Toute la classe
				</a>
			</td>
		</tr>
		<tr>
			<td>Liste des élèves
			</td>
		</tr>
						";

	for($b=0; $b<$nbre_ele_m; $b++) {
		$aff_ele_m[$b]["login"] = old_mysql_result($req_ele, $b, "login") OR DIE('Erreur requête liste_eleves : '.mysqli_error($GLOBALS["mysqli"]));

			$aff_ele_m[$b]["id_eleve"] = old_mysql_result($req_ele, $b, "id_eleve");
			$aff_ele_m[$b]["nom"] = old_mysql_result($req_ele, $b, "nom");
			$aff_ele_m[$b]["prenom"] = old_mysql_result($req_ele, $b, "prenom");
			$aff_ele_m[$b]["sexe"] = old_mysql_result($req_ele, $b, "sexe");

			// On vérifie que cet élève n'est pas déjà membre de l'AID
			if ($autoriser_inscript_multiples)
  			$req_verif = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_aid_eleves WHERE login = '".$aff_ele_m[$b]["login"]."' and id_aid='$id_aid' AND indice_aid = '".$indice_aid."'");
  		else
  			$req_verif = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_aid_eleves WHERE login = '".$aff_ele_m[$b]["login"]."' AND indice_aid = '".$indice_aid."'");
			$nbre_verif = mysqli_num_rows($req_verif);
				if ($nbre_verif >> 0) {
					$aff_classes_m .= "
					<tr class=\"aid_ligneimpaire\">
					<td></td></tr>
					";
				}
				else {
					$aff_classes_m .= "
					<tr class=\"".$aff_tr_css."\">
					<td><a href=\"modify_aid_new.php?classe=".$aff_liste_m."&amp;id_eleve=".$aff_ele_m[$b]["id_eleve"]."&amp;id_aid=".$id_aid."&amp;indice_aid=".$indice_aid.add_token_in_url()."\">
							<img src=\"../images/icons/add_user.png\" alt=\"Ajouter\" title=\"Ajouter\" /> ".$aff_ele_m[$b]["nom"]." ".$aff_ele_m[$b]["prenom"]."
							</a></td></tr>
					";
				}
	}// for $b


	$aff_classes_m .= "</table>\n";
}// if isset...

// Dans le div de droite, on affiche la liste des élèves de l'AID
		$aff_aid_d .= "<p style=\"color: brown; border: 1px solid brown; padding: 2px;\">".$rep_aid["nom"]." :</p>\n";
		// mais aussi le nom des profs de l'AID
		$req_prof = mysqli_query($GLOBALS["mysqli"], "SELECT id_utilisateur FROM j_aid_utilisateurs WHERE id_aid = '".$id_aid."' ORDER BY id_utilisateur");
		$nbre_prof = mysqli_num_rows($req_prof);
		for($p=0; $p<$nbre_prof; $p++) {
			$prof[$p]["id_utilisateur"] = old_mysql_result($req_prof, $p, "id_utilisateur");
			// On récupère le nom et la civilité de tous les profs
			$rep_nom = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT nom, civilite FROM utilisateurs WHERE login = '".$prof[$p]["id_utilisateur"]."'"));
			$aff_aid_d .= "".$rep_nom["civilite"].$rep_nom["nom"]." ";
		}

	$req_ele_aid = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT j.login, e.nom, e.prenom, c.id, c.classe
										FROM j_aid_eleves j, eleves e, j_eleves_classes jec, classes c
										WHERE j.id_aid = '".$id_aid."' AND
										j.login = e.login AND
										jec.login = j.login AND
										jec.id_classe = c.id
										ORDER BY c.classe, e.nom, e.prenom")
									OR trigger_error('Erreur sur la liste d\'élèves : '.mysqli_error($GLOBALS["mysqli"]), E_USER_ERROR);
	$nbre = mysqli_num_rows($req_ele_aid);
		$s = "";
		if ($nbre >= 2) {
			$s = "s";
		}
		else {
			$s = "";
		}
		$aff_aid_d .= "\n<br />".$nbre." élève".$s.".<br />";

	for($d=0; $d<$nbre; $d++){
		// On récupère ses noms et prénoms, puis la classe
		$rep_ele_aid[$d]["login"] = old_mysql_result($req_ele_aid, $d, "login");
		$rep_ele_aid[$d]["nom"] = old_mysql_result($req_ele_aid, $d, "nom");
		$rep_ele_aid[$d]["prenom"] = old_mysql_result($req_ele_aid, $d, "prenom");
		$rep_ele_aid[$d]["classe"] = old_mysql_result($req_ele_aid, $d, "classe");
		$rep_ele_aid[$d]["id_classe"] = old_mysql_result($req_ele_aid, $d, "id");

		$aff_aid_d .= "<br />
			<a href='./modify_aid_new.php?classe=".$rep_ele_aid[$d]["id_classe"]."&amp;eleve=".$rep_ele_aid[$d]["login"]."&amp;id_aid=".$id_aid."&amp;indice_aid=".$indice_aid."&amp;action=del_eleve_aid".add_token_in_url()."'>
				<img src=\"../images/icons/delete.png\" title=\"Supprimer cet élève\" alt=\"Supprimer\" />
			</a>".$rep_ele_aid[$d]["nom"]." ".$rep_ele_aid[$d]["prenom"]." ".$rep_ele_aid[$d]["classe"]."\n";
	}

?>
<a href="#" onMouseOver="javascript:changerDisplayDiv('aid_aide');" onMouseOut="javascript:changerDisplayDiv('aid_aide');">
	<img src="../images/info.png" alt="Plus d'infos..." Title="Plus d'infos..." />
</a>
	<div id="aid_aide" style="display: none;">
	Pour accélérer la procédure, en cliquant sur une classe,
	vous avez accès à la liste de ses élèves.<br />
	Vous pouvez intégrer tous ces élèves en cliquant sur [Toute la classe].<br />
	<hr />
	</div>

	<div id="aid_gauche">

<?php // Affichage des infos sur la partie gauche
	echo $aff_infos_g;
?>

		<table class="aid_tableau" summary="Liste des classes">
<?php // Afichage de la liste des classes à gauche
	echo $aff_classes_g;
?>
		</table>
	</div>

	<div id="aid_droite">

<?php // Affichage à droite
	echo $aff_aid_d;
?>

	</div>

	<div id="aid_centre">

<?php // Affichage au centre
	echo $aff_classes_m;
?>
	</div>


<?php
//require_once("../lib/footer.inc.php");
echo "</div></body></html>";
?>
