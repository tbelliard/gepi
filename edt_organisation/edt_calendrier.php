<?php

/*
 *
 * @version $Id$
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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
 *
 * Fichier destiné à paramétrer le calendrier de Gepi pour l'Emploi du temps
 */

$titre_page = "Emploi du temps - Calendrier";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die('Vous devez demander à votre administrateur l\'autorisation de voir cette page.');
}
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "edt_organisation/style_edt";
$utilisation_jsdivdrag = "";
//==============PROTOTYPE===============
$utilisation_prototype = "ok";
//============fin PROTOTYPE=============
// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php"); ?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php
	// Initialisation des variables
$calendrier = isset($_GET["calendrier"]) ? $_GET["calendrier"] : (isset($_POST["calendrier"]) ? $_POST["calendrier"] : NULL);
$new_periode = isset($_GET['new_periode']) ? $_GET['new_periode'] : (isset($_POST['new_periode']) ? $_POST['new_periode'] : NULL);
$nom_periode = isset($_POST["nom_periode"]) ? $_POST["nom_periode"] : NULL;
$classes_concernees = isset($_POST["classes_concernees"]) ? $_POST["classes_concernees"] : NULL;
$jour_debut = isset($_POST["jour_debut"]) ? $_POST["jour_debut"] : NULL;
$jour_fin = isset($_POST["jour_fin"]) ? $_POST["jour_fin"] : NULL;
$jour_dperiode = isset($_POST["jour_dperiode"]) ? $_POST["jour_dperiode"] : NULL;
$mois_dperiode = isset($_POST["mois_dperiode"]) ? $_POST["mois_dperiode"] : NULL;
$annee_dperiode = isset($_POST["annee_dperiode"]) ? $_POST["annee_dperiode"] : NULL;
$heure_debut = isset($_POST["heure_deb"]) ? $_POST["heure_deb"] : NULL;
$jour_fperiode = isset($_POST["jour_fperiode"]) ? $_POST["jour_fperiode"] : NULL;
$mois_fperiode = isset($_POST["mois_fperiode"]) ? $_POST["mois_fperiode"] : NULL;
$annee_fperiode = isset($_POST["annee_fperiode"]) ? $_POST["annee_fperiode"] : NULL;
$heure_fin = isset($_POST["heure_fin"]) ? $_POST["heure_fin"] : NULL;
$choix_periode = isset($_POST["choix_periode"]) ? $_POST["choix_periode"] : NULL;
$etabferme = isset($_POST["etabferme"]) ? $_POST["etabferme"] : NULL;
$vacances = isset($_POST["vacances"]) ? $_POST["vacances"] : NULL;
$supprimer = isset($_GET["supprimer"]) ? $_GET["supprimer"] : NULL;
$modifier = isset($_GET["modifier"]) ? $_GET["modifier"] : (isset($_POST["modifier"]) ? $_POST["modifier"] : NULL);
$modif_ok = isset($_POST["modif_ok"]) ? $_POST["modif_ok"] : NULL;
$message_new = NULL;

	// Quelques variables utiles
$annee_actu = date("Y"); // année
$mois_actu = date("m"); // mois sous la forme 01 à 12
$jour_actu = date("d"); // jour sous la forme 01 à 31
$date_jour = date("d/m/Y"); //jour/mois/année

		/*/ Recherche des infos déjà entrées dans Gepi
	$req_heures = mysql_fetch_array(mysql_query("SELECT ouverture_horaire_etablissement, fermeture_horaire_etablissement FROM horaires_etablissement"));
$heure_etab_deb = $req_heures["ouverture_horaire_etablissement"];
$heure_etab_fin = $req_heures["fermeture_horaire_etablissement"];
*/
/* On efface quand c'est demandé */
if (isset($calendrier) AND isset($supprimer)) {
	$req_supp = mysql_query("DELETE FROM edt_calendrier WHERE id_calendrier = '".$supprimer."'") or Die ('Suppression impossible !');
}

//+++++++++++ AIDE pour le calendrier ++++++++
?>
<a href="#" onMouseOver="javascript:changerDisplayDiv('aide_calendar');" onMouseOut="javascript:changerDisplayDiv('aide_calendar');">
	<img src="../images/info.png" alt="Plus d'infos..." Title="Plus d'infos..." />
</a>
	<div style="display: none;" id="aide_calendar">
	<hr />
	<p><span class="red">Attention</span>, ces p&eacute;riodes ne sont pas les m&ecirc;mes que celles d&eacute;finies pour les notes. Si vous voulez faire une
	 lien entre les p&eacute;riodes de notes et celles du calendrier, vous devez pr&eacute;ciser lors de la cr&eacute;ation de ces derni&egrave;res
	 &agrave; quelle p&eacute;riode de notes elles sont rattach&eacute;es en choisissant celle-ci dans le menu <i>P&eacute;riode de notes ?</i></p>
	 <hr />
	</div>
<?php
//+++++++++++ fin de l'aide ++++++++++++++++++


/* On modifie quand c'est demandé */
if (isset($calendrier) AND isset($modifier)) {
	// On affiche la période demandée dans un formulaire
	$rep_modif = mysql_fetch_array(mysql_query("SELECT * FROM edt_calendrier WHERE id_calendrier = '".$modifier."'"));
			// On garde le name de la nouvelle période pour ne pas complexifier le javascript
	echo '
		<form name="nouvelle_periode" action="edt_calendrier.php" method="post">
<fieldset id="modif_periode">
	<legend>Modifier la période pour le calendrier</legend>

			<input type="hidden" name="calendrier" value="ok" />
			<input type="hidden" name="modif_ok" value="'.$rep_modif["id_calendrier"].'" />
		<p>
			<input type="text" id="nomPer" name="nom_periode" maxlenght="100" size="30" value="'.$rep_modif["nom_calendrier"].'" />
			<label for="nomPer">Nom de la période</label>
		</p>
	<div id="div_classes_concernees">
		<p>
			<b>
				<a href="javascript:CocheCase(true)">Tout cocher</a> -
				<a href="javascript:CocheCase(false)">Tout décocher</a>
			</b>
		</p>
		';

	// On affiche la liste des classes
	$tab_select = renvoie_liste("classe");
	// On récupère les classes de la période ("zone de temps") à afficher
	$toutes_classes = explode(";", $rep_modif["classe_concerne_calendrier"]);
		// Fonction checked_calendar
		function checked_calendar($tester_classe, $classes_cochees){
			$cl_coch = explode(";", $classes_cochees);
			$return = "";
			for($t=0; $t<count($cl_coch); $t++) {
				if ($tester_classe == $cl_coch[$t]) {
					$return = " checked='checked'";
				}
			}
			return $return;
		}

	echo '
	<table>
		<tr valign="top" align="right"><td>
			';
	// Choix des classes sur 3 (ou 4) colonnes
		$modulo = count($tab_select) % 3;
			// Calcul du nombre d'entrée par colonne ($ligne)
		if ($modulo !== 0) {
			$calcul = count($tab_select) / 3;
			$expl = explode(".", $calcul);
			$ligne = $expl[0];
		}else {
			$ligne = count($tab_select) / 3;
		}
$aff_checked = ""; // par défaut, le checkbox n'est pas coché
	// On affiche la première colonne
for($i=0; $i<$ligne; $i++) {
	$aff_checked = checked_calendar($tab_select[$i]["id"], $rep_modif["classe_concerne_calendrier"]);
	echo '
		<label for="case_1_'.$tab_select[$i]["id"].'">'.$tab_select[$i]["classe"].'</label>
				<input name="classes_concernees[]" value="'.$tab_select[$i]["id"].'" id="case_1_'.$tab_select[$i]["id"].'"'.$aff_checked.' type="checkbox" />
			<br />
		';
}

echo '
		</td><td>
	';

for($i=$ligne; $i<($ligne*2); $i++) {
	$aff_checked = checked_calendar($tab_select[$i]["id"], $rep_modif["classe_concerne_calendrier"]);
	// On affiche la deuxième colonne
	echo '
		<label for="case_1_'.$tab_select[$i]["id"].'">'.$tab_select[$i]["classe"].'</label>
				<input name="classes_concernees[]" value="'.$tab_select[$i]["id"].'" id="case_1_'.$tab_select[$i]["id"].'"'.$aff_checked.' type="checkbox" />
		<br />
		';
}

echo '
		</td><td>
	';
for($i=($ligne*2); $i<($ligne*3); $i++) {
	$aff_checked = checked_calendar($tab_select[$i]["id"], $rep_modif["classe_concerne_calendrier"]);
	// On affiche la troisième colonne
	echo '
		<label for="case_1_'.$tab_select[$i]["id"].'">'.$tab_select[$i]["classe"].'</label>
				<input name="classes_concernees[]" value="'.$tab_select[$i]["id"].'" id="case_1_'.$tab_select[$i]["id"].'"'.$aff_checked.' type="checkbox" />
		<br />
		';
}
echo '
		</td>
	';
// s'il y a une quatrième colonne, on l'affiche
if ($modulo !== 0) {
	echo '
		<td>
		';
	for($i=($ligne*3); $i<count($tab_select); $i++) {
	$aff_checked = checked_calendar($tab_select[$i]["id"], $rep_modif["classe_concerne_calendrier"]);
		echo '
			<label for="case_1_'.$tab_select[$i]["id"].'">'.$tab_select[$i]["classe"].'</label>
				<input name="classes_concernees[]" value="'.$tab_select[$i]["id"].'" id="case_1_'.$tab_select[$i]["id"].'"'.$aff_checked.' type="checkbox" />
		<br />
		';
	}
	echo '</td>';
	}


	echo '
		</tr>
	</table>
	</div>
		';
// Fin du div pour le choix des classes
	// On retravaille les jours pour utiliser le calendrier
	$exp_jourdeb = explode("-", $rep_modif["jourdebut_calendrier"]);
	$aff_jourdeb = $exp_jourdeb[2]."/".$exp_jourdeb[1]."/".$exp_jourdeb[0];
	$exp_jourfin = explode("-", $rep_modif["jourfin_calendrier"]);
	$aff_jourfin = $exp_jourfin[2]."/".$exp_jourfin[1]."/".$exp_jourfin[0];
		// On enlève les secondes à l'affichage des heures
	$aff_heuredeb = substr($rep_modif["heuredebut_calendrier"], 0, -3);
	$aff_heurefin = substr($rep_modif["heurefin_calendrier"], 0, -3);

	echo '
		<p>
			<input type="text" id="jourDebPer" name="jour_dperiode" maxlenght="10" size="10" value="'.$aff_jourdeb.'" />
				<a href="#calend" onclick="window.open(\'../lib/calendrier/pop.calendrier.php?frm=nouvelle_periode&amp;ch=jour_dperiode\',\'calendrier\',\'width=350,height=170,scrollbars=0\').focus();">
				<img src="../lib/calendrier/petit_calendrier.gif" alt="" border="0" /></a>
			<label for="jourDebPer">Premier jour</label>

			<input type="text" id="heureDeb" name="heure_deb" maxlenght="5" size="5" value="'.$aff_heuredeb.'" />
			<label for="heureDeb">Heure de début</label>
		</p>
		<p>
			<input type="text" id="jourFinPer" name="jour_fperiode" maxlenght="10" size="10" value="'.$aff_jourfin.'" />
				<a href="#calend" onclick="window.open(\'../lib/calendrier/pop.calendrier.php?frm=nouvelle_periode&amp;ch=jour_fperiode\',\'calendrier\',\'width=350,height=170,scrollbars=0\').focus();">
				<img src="../lib/calendrier/petit_calendrier.gif" alt="" border="0" /></a>
			<label for="jourFinPer">Dernier jour</label>

			<input type="text" id="heureFin" name="heure_fin" maxlenght="5" size="5" value="'.$aff_heurefin.'" />
			<label for="heureFin">Heure de fin</label>
		</p>
		<p>
			<select id="choixPer" name="choix_periode">
				<option value="rien">Non</option>'."\n";
	// Proposition de définition des périodes déjà existantes de la table periodes
	$req_periodes = mysql_query("SELECT nom_periode, num_periode FROM periodes WHERE id_classe = '1'");
	$nbre_periodes = mysql_num_rows($req_periodes);
		$rep_periodes[] = array();
		for ($i=0; $i<$nbre_periodes; $i++) {
			$rep_periodes[$i]["num_periode"] = mysql_result($req_periodes, $i, "num_periode");
			$rep_periodes[$i]["nom_periode"] = mysql_result($req_periodes, $i, "nom_periode");
				if ($rep_modif["numero_periode"] == $rep_periodes[$i]["num_periode"]) {
					$selected = " selected='true'";
				}
				else $selected = "";
			echo '<option value="'.$rep_periodes[$i]["num_periode"].'"'.$selected.'>'.$rep_periodes[$i]["nom_periode"].'</option>'."\n";
		}
	echo '
			</select>
			<label for="choixPer">Périodes de notes ?</label>
		</p>
		<p>
			<select id="etabFerm" name="etabferme">
		';
		// On vérifie le ouvert - fermé
		if ($rep_modif["etabferme_calendrier"] == "1") {
			$selected1 = " selected='selected'";
		} else $selected1 = "";
		if ($rep_modif["etabferme_calendrier"] == "2") {
			$selected2 = " selected='selected'";
		} else $selected2 = "";
	echo '
				<option value="1"'.$selected1.'>Ouvert</option>
				<option value="2"'.$selected2.'>Fermé</option>
			</select>
			<label for="etabFerm">Etablissement</label>
		</p>
		<p>
			<select id="selectVac" name="vacances">
		';
		// On vérifie le vacances - cours
		if ($rep_modif["etabvacances_calendrier"] == "0") {
			$selected1v = " selected='selected'";
		} else $selected1v = "";
		if ($rep_modif["etabvacances_calendrier"] == "1") {
			$selected2v = " selected='selected'";
		}else $selected2v = "";
	echo '
				<option value="0"'.$selected1v.'>Cours</option>
				<option value="1"'.$selected2v.'>Vacances</option>
			</select>
			<label for="selectVac">Vacances / Cours</label>
		</p>
			<input type="submit" name="valider" value="enregistrer" />
</fieldset>
		</form>
<br />
	';
}
	// On construit les classes consernées
	if ($classes_concernees[0] == "0") {
			$classes_concernees_insert = "0";
		}
		else {
				$classes_concernees_insert = "";
			for ($c=0; $c<count($classes_concernees); $c++) {
				$classes_concernees_insert .= $classes_concernees[$c].";";
			}
		} // else

	// =========== TRAITEMENT de la modification de la période =============
if (isset($modif_ok) AND isset($nom_periode)) {
	$jourdebut = $jour_dperiode;
	$jourfin = $jour_fperiode;
	// traitement du timestamp Unix GMT ainsi que des dates et heures MySql
	$exp_jourdeb = explode("/", $jourdebut);
	$exp_jourfin = explode("/", $jourfin);
	$exp_heuredeb = explode(":", $heure_debut);
	$exp_heurefin = explode(":", $heure_fin);
	$deb_ts = gmmktime($exp_heuredeb[0], $exp_heuredeb[1], 0, $exp_jourdeb[1], $exp_jourdeb[0], $exp_jourdeb[2]);
	$jourdebut = $exp_jourdeb[2]."-".$exp_jourdeb[1]."-".$exp_jourdeb[0];
	$fin_ts = gmmktime($exp_heurefin[0], $exp_heurefin[1], 0, $exp_jourfin[1], $exp_jourfin[0], $exp_jourfin[2]);
	$jourfin = $exp_jourfin[2]."-".$exp_jourfin[1]."-".$exp_jourfin[0];
	$modif_periode = mysql_query("UPDATE edt_calendrier
				SET nom_calendrier = '".$nom_periode."',
				classe_concerne_calendrier = '".$classes_concernees_insert."',
				debut_calendrier_ts = '".$deb_ts."',
				fin_calendrier_ts = '".$fin_ts."',
				jourdebut_calendrier = '".$jourdebut."',
				heuredebut_calendrier = '".$heure_debut."',
				jourfin_calendrier = '".$jourfin."',
				heurefin_calendrier = '".$heure_fin."',
				numero_periode = '".$choix_periode."',
				etabferme_calendrier = '".$etabferme."',
				etabvacances_calendrier = '".$vacances."'
				WHERE id_calendrier = '".$modif_ok."'")
				OR DIE ('Erreur dans la modification');
}

/* ==================== On traite les nouvelles entrées dans la table ================ */
if (isset($new_periode) AND isset($nom_periode)) {
$detail_jourdeb = explode("/", $jour_debut);
$detail_jourfin = explode("/", $jour_fin);

	$jourdebut = $detail_jourdeb[2]."-".$detail_jourdeb[1]."-".$detail_jourdeb[0];
	$jourfin = $detail_jourfin[2]."-".$detail_jourfin[1]."-".$detail_jourfin[0];
		// On insère les classes qui sont concernées (0 = toutes)
		if ($classes_concernees[0] == "0") {
			$classes_concernees_insert = "0";
		}
		else {
				$classes_concernees_insert = "";
			for ($c=0; $c<count($classes_concernees); $c++) {
				$classes_concernees_insert .= $classes_concernees[$c].";";
			}
		} // else
	// On vérifie que ce nom de période n'existe pas encore
	$req_verif_periode = mysql_fetch_array(mysql_query("SELECT nom_calendrier FROM edt_calendrier WHERE nom_calendrier = '".$nom_periode."'"));
	if ($req_verif_periode[0] == NULL) {
		$heure_debut = $heure_debut.":00";
			$expdeb = explode(":", $heure_debut);
		$heure_fin = $heure_fin.":00";
			$expfin = explode(":", $heure_fin);
			// On insére ces dates en timestamp Unix GMT
		$heuredeb_ts = gmmktime($expdeb[0], $expdeb[1], 0, $detail_jourdeb[1], $detail_jourdeb[0], $detail_jourdeb[2]);
		$heurefin_ts = gmmktime($expfin[0], $expfin[1], 0, $detail_jourfin[1], $detail_jourfin[0], $detail_jourfin[2]);
		$req_insert = mysql_query("INSERT INTO edt_calendrier (`nom_calendrier`, `classe_concerne_calendrier`, `debut_calendrier_ts`, `fin_calendrier_ts`, `jourdebut_calendrier`, `heuredebut_calendrier`, `jourfin_calendrier`, `heurefin_calendrier`, `numero_periode`, `etabferme_calendrier`, `etabvacances_calendrier`)
							VALUES ('$nom_periode', '$classes_concernees_insert', '$heuredeb_ts', '$heurefin_ts', '$jourdebut', '$heure_debut', '$jourfin', '$heure_fin', '$choix_periode', '$etabferme', '$vacances')")
							OR DIE ('Echec dans la requête de création d\'une nouvelle entrée !');
	}
	else echo '<h3 class="red">Ce nom de période existe déjà</h3>';
}

/* ============ On affiche alors toutes les périodes de la table ==============*/

	// Lien qui permet de saisir de nouvelles périodes
if ($modifier == NULL) {
	echo '
	<p>
	<a href="edt_calendrier.php?calendrier=ok&amp;new_periode=ok"><img src="../images/icons/add.png" alt="" class="back_link" /> AJOUTER</a>
	</p>
	';

}

/*+++++++++++++++++++++AFFICHAGE DES PERIODES DEJA DEFINIES +++++++++++++++++++++*/
//================================================================================
	// Toutes les périodes sont visibles par défaut
echo '
<fieldset id="aff_calendar">
	<legend>Liste des périodes</legend>
<table id="edt_calendar" cellspacing="1" cellpadding="1" border="1">
	<tr class="premiere_ligne">
		<td>Nom du calendrier</td>
		<td>Classes</td>
		<td class="bonnelargeur">Premier jour</td>
		<td class="bonnelargeur">à</td>
		<td class="bonnelargeur">Dernier jour</td>
		<td class="bonnelargeur">à</td>
		<td class="bonnelargeur">Etablissement</td>
		<td>Cours<br />Vacances</td>
		<td></td>
		<td></td>
	</tr>
';
	// On affiche toutes les lignes déjà entrées
$req_affcalendar = mysql_query("SELECT * FROM edt_calendrier ORDER BY jourdebut_calendrier") OR die ('Impossible d\'afficher le calendrier.');
$nbre_affcalendar = mysql_num_rows($req_affcalendar);
	// Variable pour le $class_tr
	$a = 1;

	for ($i=0; $i<$nbre_affcalendar; $i++) {
		$rep_affcalendar[$i]["id_calendrier"] = mysql_result($req_affcalendar, $i, "id_calendrier");
		$rep_affcalendar[$i]["classe_concerne_calendrier"] = mysql_result($req_affcalendar, $i, "classe_concerne_calendrier");
		$rep_affcalendar[$i]["nom_calendrier"] = mysql_result($req_affcalendar, $i, "nom_calendrier");
		$rep_affcalendar[$i]["jourdebut_calendrier"] = mysql_result($req_affcalendar, $i, "jourdebut_calendrier");
		$rep_affcalendar[$i]["heuredebut_calendrier"] = mysql_result($req_affcalendar, $i, "heuredebut_calendrier");
		$rep_affcalendar[$i]["jourfin_calendrier"] = mysql_result($req_affcalendar, $i, "jourfin_calendrier");
		$rep_affcalendar[$i]["heurefin_calendrier"] = mysql_result($req_affcalendar, $i, "heurefin_calendrier");
		$rep_affcalendar[$i]["numero_periode"] = mysql_result($req_affcalendar, $i, "numero_periode");
		$rep_affcalendar[$i]["etabferme_calendrier"] = mysql_result($req_affcalendar, $i, "etabferme_calendrier");
		$rep_affcalendar[$i]["etabvacances_calendrier"] = mysql_result($req_affcalendar, $i, "etabvacances_calendrier");
			// établissement ouvert ou fermé ?
			if ($rep_affcalendar[$i]["etabferme_calendrier"] == "1") {
				$ouvert_ferme = "ouvert";
			}
			else $ouvert_ferme = "fermé";
			// Quelles classes sont concernées
			$expl_aff = explode(";", ($rep_affcalendar[$i]["classe_concerne_calendrier"]));
			// Attention, si on compte l'explode, on a une ligne de trop
			if ($expl_aff == "0" OR $rep_affcalendar[$i]["classe_concerne_calendrier"] == "0") {
				$aff_classe_concerne = "<span class=\"legende\">Toutes</span>";
			}
			else {
				$contenu_infobulle = "<font style=\"color: brown;\">".(count($expl_aff) - 1)." classe(s).</font><br />";
				for ($t=0; $t<(count($expl_aff) - 1); $t++) {
					$req_nomclasse = mysql_fetch_array(mysql_query("SELECT nom_complet FROM classes WHERE id = '".$expl_aff[$t]."'"));
					$contenu_infobulle .= $req_nomclasse["nom_complet"].'<br />';
				}
				//$aff_classe_concerne = aff_popup("Voir", "edt", "Classes concernées", $contenu_infobulle);
				$id_div = "periode".$rep_affcalendar[$i]["id_calendrier"];
				$aff_classe_concerne = "<a href=\"#\" onmouseover=\"afficher_div('".$id_div."','Y',10,10);return false;\" onmouseout=\"cacher_div('".$id_div."');\">Liste</a>\n".creer_div_infobulle($id_div, "Liste des classes", "#330033", $contenu_infobulle, "#FFFFFF", 15,0,"n","n","y","n");
			} // else

			// On détermine si c'est une période pédagogique ou une période de vacances
			if ($rep_affcalendar[$i]["etabvacances_calendrier"] == 0) {
				$aff_cours = "Cours";
			} else {
				$aff_cours = "Vac.";
			}

			// On enlève les secondes à l'affichage
			$explode_deb = explode(":", $rep_affcalendar[$i]["heuredebut_calendrier"]);
			$rep_affcalendar[$i]["heuredebut_calendrier"] = $explode_deb[0].":".$explode_deb[1];
			$explode_fin = explode(":", $rep_affcalendar[$i]["heurefin_calendrier"]);
			$rep_affcalendar[$i]["heurefin_calendrier"] = $explode_fin[0].":".$explode_fin[1];
			// On affiche les dates au format français
			$exp_jourdeb = explode("-", $rep_affcalendar[$i]["jourdebut_calendrier"]);
			$aff_jourdeb = $exp_jourdeb[2]."/".$exp_jourdeb[1]."/".$exp_jourdeb[0];
			$exp_jourfin = explode("-", $rep_affcalendar[$i]["jourfin_calendrier"]);
			$aff_jourfin = $exp_jourfin[2]."/".$exp_jourfin[1]."/".$exp_jourfin[0];

		// Afficher de deux couleurs différentes

		if ($a == 1) {
			$class_tr = "ligneimpaire";
			$a ++;
		}
		elseif ($a == 2) {
			$class_tr = "lignepaire";
			$a = 1;
		}
		echo '
	<tr class="'.$class_tr.'">
		<td>'.$rep_affcalendar[$i]["nom_calendrier"].'</td>
		<td>'.$aff_classe_concerne.'</td>
		<td>'.$aff_jourdeb.'</td>
		<td>'.$rep_affcalendar[$i]["heuredebut_calendrier"].'</td>
		<td>'.$aff_jourfin.'</td>
		<td>'.$rep_affcalendar[$i]["heurefin_calendrier"].'</td>
		<!--<td>'.$rep_affcalendar[$i]["numero_periode"].'</td>-->
		<td>'.$ouvert_ferme.'</td>
		<td>'.$aff_cours.'</td>
		<td class="modif_supr"><a href="edt_calendrier.php?calendrier=ok&amp;modifier='.$rep_affcalendar[$i]["id_calendrier"].'"><img src="../images/icons/configure.png" title="Modifier" alt="Modifier" /></a></td>
		<td class="modif_supr"><a href="edt_calendrier.php?calendrier=ok&amp;supprimer='.$rep_affcalendar[$i]["id_calendrier"].'" onClick="return confirm(\'Confirmez-vous cette suppression ?\')"><img src="../images/icons/delete.png" title="Supprimer" alt="Supprimer" /></a></td>
	</tr>
		';
	}
echo '
</table>
</fieldset>
<br />
';
/* ============= fin de l'affichage des périodes déjà présentes dans Gepi
  Début de l'affichage pour enregistrer de nouvelles périodes ================*/

if ($new_periode == "ok") {
	// On affiche le formulaire pour entrer les "new_periode"
	echo '
		<form name="nouvelle_periode" action="edt_calendrier.php" method="post">

<fieldset id="saisie_new_periode">
	<legend>Saisir une nouvelle période pour le calendrier</legend>

			<input type="hidden" name="calendrier" value="ok" />
			<input type="hidden" name="new_periode" value="ok" />

	<div id="div_classes_concernees">
		<p>
			<b>
				<a href="javascript:CocheCase(true)">Tout cocher</a> -
				<a href="javascript:CocheCase(false)">Tout décocher</a>
			</b>
		</p>
		';
	// On affiche la liste des classes
	$tab_select = renvoie_liste("classe");

	echo '
	<table>
		<tr valign="top" align="right"><td>
			';
	// Choix des classes sur 3 (ou 4) colonnes
		$modulo = count($tab_select) % 3;
			// Calcul du nombre d'entrée par colonne ($ligne)
		if ($modulo !== 0) {
			$calcul = count($tab_select) / 3;
			$expl = explode(".", $calcul);
			$ligne = $expl[0];
		}else {
			$ligne = count($tab_select) / 3;
		}

	// Par défaut, tous les checkbox sont cochés
	$aff_checked = " checked='checked'";

	// On affiche la première colonne
for($i=0; $i<$ligne; $i++) {

	echo '
		<label for="case_1_'.$tab_select[$i]["id"].'">'.$tab_select[$i]["classe"].'</label>
			<input name="classes_concernees[]" value="'.$tab_select[$i]["id"].'" id="case_1_'.$tab_select[$i]["id"].'"'.$aff_checked.' type="checkbox" />
		<br />
		';
}

echo '
		</td><td>
	';

for($i=$ligne; $i<($ligne*2); $i++) {
	// On affiche la deuxième colonne
	echo '
		<label for="case_1_'.$tab_select[$i]["id"].'">'.$tab_select[$i]["classe"].'</label>
				<input name="classes_concernees[]" value="'.$tab_select[$i]["id"].'" id="case_1_'.$tab_select[$i]["id"].'"'.$aff_checked.' type="checkbox" />
		<br />
		';
}

echo '
		</td><td>
	';
for($i=($ligne*2); $i<($ligne*3); $i++) {
	// On affiche la troisième colonne
	echo '
		<label for="case_1_'.$tab_select[$i]["id"].'">'.$tab_select[$i]["classe"].'</label>
				<input name="classes_concernees[]" value="'.$tab_select[$i]["id"].'" id="case_1_'.$tab_select[$i]["id"].'"'.$aff_checked.' type="checkbox" />
		<br />
		';
}
echo '
		</td>
	';
// s'il y a une quatrième colonne, on l'affiche
if ($modulo !== 0) {
	echo '
		<td>
		';
	for($i=($ligne*3); $i<count($tab_select); $i++) {
		echo '
			<label for="case_1_'.$tab_select[$i]["id"].'">'.$tab_select[$i]["classe"].'</label>
				<input name="classes_concernees[]" value="'.$tab_select[$i]["id"].'" id="case_1_'.$tab_select[$i]["id"].'"'.$aff_checked.' type="checkbox" />
		<br />
		';
	}
	echo '</td>';
	}


	echo '
		</tr>
	</table>
	</div>
		<p>
			<input type="text" name="nom_periode" maxlenght="100" size="30" value="Nouvelle période" />
			<span class="legende">Nom de la période</span>
		</p>
		<p>

		<input type="text" name="jour_debut" maxlenght="10" size="10" value="'.$date_jour.'" />
		<a href="#calend" onclick="window.open(\'../lib/calendrier/pop.calendrier.php?frm=nouvelle_periode&amp;ch=jour_debut\',\'calendrier\',\'width=350,height=170,scrollbars=0\').focus();">
		<img src="../lib/calendrier/petit_calendrier.gif" alt="" border="0" /></a>
			<span class="legende">Premier jour</span>

			<input type="text" name="heure_deb" maxlenght="5" size="5" value="00:00" />
			<span class="legende">Heure de début</span>
		</p>
		<p>

		<input type="text" name="jour_fin" maxlenght="10" size="10" value="jj/mm/YYYY" />
		<a href="#calend" onclick="window.open(\'../lib/calendrier/pop.calendrier.php?frm=nouvelle_periode&amp;ch=jour_fin\',\'calendrier\',\'width=350,height=170,scrollbars=0\').focus();">
		<img src="../lib/calendrier/petit_calendrier.gif" alt="" border="0" /></a>
			<span class="legende">Dernier jour</span>

			<input type="text" name="heure_fin" maxlenght="5" size="5" value="23:59" />
			<span class="legende">Heure de fin</span>
		</p>
		<p>
			<select name="choix_periode">
				<option value="rien">Non</option>';
	// Proposition de définition des périodes déjà existantes de la table periodes
	$req_periodes = mysql_query("SELECT nom_periode, num_periode FROM periodes WHERE id_classe = '1'");
	$nbre_periodes = mysql_num_rows($req_periodes);
		$rep_periodes[] = array();
		for ($i=0; $i<$nbre_periodes; $i++) {
			$rep_periodes[$i]["num_periode"] = mysql_result($req_periodes, $i, "num_periode");
			$rep_periodes[$i]["nom_periode"] = mysql_result($req_periodes, $i, "nom_periode");
			echo '
				<option value="'.$rep_periodes[$i]["num_periode"].'">'.$rep_periodes[$i]["nom_periode"].'</option>
				';
		}
	echo '
			</select>
			<span class="legende">Période de notes ?</span>
		</p>
		<p>
			<select id="etabFerm" name="etabferme">
				<option value="1">Ouvert</option>
				<option value="2">Fermé</option>
			</select>
			<label for="etabFerm">Etablissement</label>
		</p>
		<p>
			<select name="vacances">
				<option value="0">Cours</option>
				<option value="1">Vacances</option>
			</select>
			<span class="legende">Vacances / Cours</span>
		</p>
			<input type="submit" name="valider" value="enregistrer" />

</fieldset>
		</form>

	';
} // if ($new_periode == "ok")

if (isset($message_new)) {
	echo $message_new;
}


// On vérifie le retour en session pour savoir si il faut l'afficher
if (isset($_SESSION["retour"]) AND $_SESSION["retour"] == "../mod_absences/admin/index.php") {
	echo '<p class=bold><a href="'.$_SESSION["retour"].'"><img src="../images/icons/back.png" alt="Retour" class="back_link"/> Retour vers le module absences</a>';
}
?>

	</div>
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>
