<?php

/* Fichier destiné à paramétrer le calendrier de Gepi pour l'Emploi du temps */

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
	// Initialisation des variables
$new_periode = isset($_GET['new_periode']) ? $_GET['new_periode'] : (isset($_POST['new_periode']) ? $_POST['new_periode'] : NULL);
$nom_periode = isset($_POST["nom_periode"]) ? $_POST["nom_periode"] : NULL;
$classes_concernees = isset($_POST["classes_concernees"]) ? $_POST["classes_concernees"] : NULL;
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
		// Recherche des infos déjà entrées dans Gepi
	$req_heures = mysql_fetch_array(mysql_query("SELECT ouverture_horaire_etablissement, fermeture_horaire_etablissement FROM horaires_etablissement"));
$heure_etab_deb = $req_heures["ouverture_horaire_etablissement"];
$heure_etab_fin = $req_heures["fermeture_horaire_etablissement"];

/* On efface quand c'est demandé */
if (isset($calendrier) AND isset($supprimer)) {
	$req_supp = mysql_query("DELETE FROM edt_calendrier WHERE id_calendrier = '".$supprimer."'") or Die ('Suppression impossible !');
}

/* On modifie quand c'est demandé */
if (isset($calendrier) AND isset($modifier)) {
	// On affiche la période demandée dans un formulaire
	$rep_modif = mysql_fetch_array(mysql_query("SELECT * FROM edt_calendrier WHERE id_calendrier = '".$modifier."'"));
	echo '
	</center>
<fieldset id="modif_periode">
	<legend>Modifier la période pour le calendrier</legend>
		<form name="modifier_periode" action="index_edt.php" method="POST">
			<input type="hidden" name="calendrier" value="ok" />
			<input type="hidden" name="modif_ok" value="'.$rep_modif["id_calendrier"].'" />
		<p>
			<input type="text" name="nom_periode" maxlenght="100" size="30" value="'.$rep_modif["nom_calendrier"].'" />
			<span class="legende">Nom de la période</span>
		</p>
	<div id="div_classes_concernees">
		<p>
			<span class="legende">Classes concernées</span><br />
			<select name="classes_concernees[]" multiple="multiple">
		';

	// On détermine à quel endroit il faut afficher le selected="true"
	$toutes_classes = explode(";", $rep_modif["classe_concerne_calendrier"]);
			if ($toutes_classes[0] == "0") {
					$selected_c = "<option value=\"0\" selected=\"selected\">Toutes</option>";
			}
			else $selected_c = "<option value=\"0\">Toutes</option>";
		echo $selected_c;
	// On affiche la liste des classes
	$tab_select = renvoie_liste("classe");

for($i=0; $i<count($tab_select); $i++) {
		// Il faudra voir comment retrouver les selected car ça ne marche pas... ?
		$aff_selected = "";
			for ($e=0; $e<count($toutes_classes); $e++) {
				if ($tab_select[$i]["id"] == "$toutes_classes[$e]") {
					$aff_selected = " selected=\"selected\"";
				}
				else $aff_selected = "";
			}
	echo ("			<option value=\"".$tab_select[$i]["id"]."\"".$aff_selected."> ".$tab_select[$i]["classe"]."</option>\n");
	}

	echo '
			</select>
		</p>
	</div>

		<p>
			<input type="text" name="jour_dperiode" maxlenght="10" size="10" value="'.$rep_modif["jourdebut_calendrier"].'" />
			<span class="legende">Premier jour</span>

			<input type="text" name="heure_deb" maxlenght="8" size="8" value="'.$rep_modif["heuredebut_calendrier"].'" />
			<span class="legende">Heure de début</span>
		</p>
		<p>
			<input type="text" name="jour_fperiode" maxlenght="10" size="10" value="'.$rep_modif["jourfin_calendrier"].'" />
			<span class="legende">Dernier jour</span>

			<input type="text" name="heure_fin" maxlenght="8" size="8" value="'.$rep_modif["heurefin_calendrier"].'" />
			<span class="legende">Heure de fin</span>
		</p>
		<p>
			<SELECT name="choix_periode">
				<OPTION value="rien">Nouvelle</OPTION>'."\n";
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
			echo '<OPTION value="'.$rep_periodes[$i]["num_periode"].'"'.$selected.'>'.$rep_periodes[$i]["nom_periode"].'</OPTION>'."\n";
		}
	echo '
			</SELECT>
			<span class="legende">Périodes</span>
		</p>
		<p>
			<SELECT name="etabferme" />
		';
		// On vérifie le ouvert - fermé
		if ($rep_modif["etabferme_calendrier"] == "1") {
			$selected1 = " selected='true'";
		} else $selected1 = "";
		if ($rep_modif["etabferme_calendrier"] == "2") {
			$selected2 = " selected='true'";
		} else $selected2 = "";
	echo '
				<option value="1"'.$selected1.'>Ouvert</option>
				<option value="2"'.$selected2.'>Fermé</option>
			</select>
			<span class="legende">Etablissement</span>
		</p>
		<p>
			<SELECT name="vacances">
		';
		// On vérifie le vacances - cours
		if ($rep_modif["etabvacances_calendrier"] == "0") {
			$selected1v = " selected='true'";
		} else $selected1v = "";
		if ($rep_modif["etabvacances_calendrier"] == "1") {
			$selected2v = " selected='true'";
		}else $selected2v = "";
	echo '
				<option value="0"'.$selected1v.'>Cours</option>
				<option value="1"'.$selected2v.'>Vacances</option>
			</SELECT>
			<span class="legende">Vacances / Cours</span>
		</p>
			<input type="submit" name="valider" value="enregistrer" />
		</form>
</fieldset>
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
	// Puis on modifie la période
if (isset($modif_ok) AND isset($nom_periode)) {
	$jourdebut = $jour_dperiode;
	$jourfin = $jour_fperiode;
	$modif_periode = mysql_query("UPDATE edt_calendrier SET nom_calendrier = '".$nom_periode."', classe_concerne_calendrier = '".$classes_concernees_insert."', jourdebut_calendrier = '".$jourdebut."', heuredebut_calendrier = '".$heure_debut."', jourfin_calendrier = '".$jourfin."', heurefin_calendrier = '".$heure_fin."', numero_periode = '".$choix_periode."', etabferme_calendrier = '".$etabferme."', etabvacances_calendrier = '".$vacances."' WHERE id_calendrier = '".$modif_ok."'") OR DIE ('Erreur dans la modification');
}

/* On traite les nouvelles entrées dans la table */
if (isset($new_periode) AND isset($nom_periode)) {
	$jourdebut = $annee_dperiode."-".$mois_dperiode."-".$jour_dperiode;
	$jourfin = $annee_fperiode."-".$mois_fperiode."-".$jour_fperiode;
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
		$req_insert = mysql_query("INSERT INTO edt_calendrier (`nom_calendrier`, `classe_concerne_calendrier`, `jourdebut_calendrier`, `heuredebut_calendrier`, `jourfin_calendrier`, `heurefin_calendrier`, `numero_periode`, `etabferme_calendrier`, `etabvacances_calendrier`) VALUES ('$nom_periode', '$classes_concernees_insert', '$jourdebut', '$heure_debut', '$jourfin', '$heure_fin', '$choix_periode', '$etabferme', '$vacances')") OR DIE ('Echec dans la requête de création d\'une nouvelle entrée !');
	}
	else echo '<p><h3 class="red">Ce nom de période existe déjà</h3></p>';
}

/* On affiche alors toutes les périodes de la table */

	// Lien qui permet de saisir de nouvelles périodes
if ($modifier == NULL) {
	echo '
</center>
	<p>
	<a href="index_edt.php?calendrier=ok&new_periode=ok"><img src="../images/icons/add.png" alt="" class="back_link" /> AJOUTER</a>
	</p>
	';

}


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
		<!--<td>Trimestre</td>-->
		<td class="bonnelargeur">Etablissement</td>
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
			if ($expl_aff == "0" OR $rep_affcalendar[$i]["classe_concerne_calendrier"] == "0") {
				$aff_classe_concerne = "<span class=\"legende\">Toutes</span>";
			}
			else {
				$contenu_infobulle = "";
				for ($t=0; $t<count($expl_aff); $t++) {
					$req_nomclasse = mysql_fetch_array(mysql_query("SELECT nom_complet FROM classes WHERE id = '".$expl_aff[$t]."'"));
					$contenu_infobulle .= $req_nomclasse["nom_complet"].'<br />';
				}
				$aff_classe_concerne = aff_popup("Voir", "edt", "Classes concernées", $contenu_infobulle);
			} // else
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
		<td>'.$rep_affcalendar[$i]["jourdebut_calendrier"].'</td>
		<td>'.$rep_affcalendar[$i]["heuredebut_calendrier"].'</td>
		<td>'.$rep_affcalendar[$i]["jourfin_calendrier"].'</td>
		<td>'.$rep_affcalendar[$i]["heurefin_calendrier"].'</td>
		<!--<td>'.$rep_affcalendar[$i]["numero_periode"].'</td>-->
		<td>'.$ouvert_ferme.'</td>
		<td class="modif_supr"><a href="index_edt.php?calendrier=ok&modifier='.$rep_affcalendar[$i]["id_calendrier"].'"><img src="../images/icons/configure.png" title="Modifier" alt="Modifier" /></a></td>
		<td class="modif_supr"><a href="index_edt.php?calendrier=ok&supprimer='.$rep_affcalendar[$i]["id_calendrier"].'" onClick="return confirm(\'Confirmez-vous cette suppression ?\')"><img src="../images/icons/delete.png" title="Supprimer" alt="Supprimer" /></a></td>
	</tr>
		';
	}
echo '
</table>
</fieldset>
<br />
';
/* fin de l'affichage des périodes déjà présentes dans Gepi
  Début de l'affichage pour enregistrer de nouvelles périodes */
if ($new_periode == "ok") {
	// On affiche le formulaire pour entrer les "new_periode"
	echo '
<fieldset id="saisie_new_periode">
	<legend>Saisir une nouvelle période pour le calendrier</legend>
		<form name="nouvelle_periode" action="index_edt.php" method="POST">
			<input type="hidden" name="calendrier" value="ok" />
			<input type="hidden" name="new_periode" value="ok" />
	<div id="div_classes_concernees">
		<p>
			<span class="legende">Classes concernées</span><br />
			<select name="classes_concernees[]" multiple="multiple">
				<option value="0" selected="true">Toutes</option>
		';
	// On affiche la liste des classes
	$tab_select = renvoie_liste("classe");

for($i=0; $i<count($tab_select); $i++) {
	echo ("			<option value=\"".$tab_select[$i]["id"]."\"> ".$tab_select[$i]["classe"]."</option>\n");
	}

	echo '
			</select>
		</p>
	</div>
		<p>
			<input type="text" name="nom_periode" maxlenght="100" size="30" value="Nouvelle période" />
			<span class="legende">Nom de la période</span>
		</p>
		<p>
			<input type="text" name="jour_dperiode" maxlenght="2" size="2" value="01" />
			<input type="text" name="mois_dperiode" maxlenght="2" size="2" value="01" />
			<input type="text" name="annee_dperiode" maxlenght="4" size="4" value="'.$annee_actu.'" />
			<span class="legende">Premier jour</span>

			<input type="text" name="heure_deb" maxlenght="8" size="8" value="'.$heure_etab_deb.'" />
			<span class="legende">Heure de début</span>
		</p>
		<p>
			<input type="text" name="jour_fperiode" maxlenght="2" size="2" value="31" />
			<input type="text" name="mois_fperiode" maxlenght="2" size="2" value="12" />
			<input type="text" name="annee_fperiode" maxlenght="4" size="4" value="'.$annee_actu.'" />
			<span class="legende">Dernier jour</span>

			<input type="text" name="heure_fin" maxlenght="8" size="8" value="'.$heure_etab_fin.'" />
			<span class="legende">Heure de fin</span>
		</p>
		<p>
			<SELECT name="choix_periode">
				<OPTION value="rien">Nouvelle</OPTION>';
	// Proposition de définition des périodes déjà existantes de la table periodes
	$req_periodes = mysql_query("SELECT nom_periode, num_periode FROM periodes WHERE id_classe = '1'");
	$nbre_periodes = mysql_num_rows($req_periodes);
		$rep_periodes[] = array();
		for ($i=0; $i<$nbre_periodes; $i++) {
			$rep_periodes[$i]["num_periode"] = mysql_result($req_periodes, $i, "num_periode");
			$rep_periodes[$i]["nom_periode"] = mysql_result($req_periodes, $i, "nom_periode");
			echo '<OPTION value="'.$rep_periodes[$i]["num_periode"].'">'.$rep_periodes[$i]["nom_periode"].'</OPTION>';
		}
	echo '
			</SELECT>
			<span class="legende">Périodes</span>
		</p>
		<p>
			<SELECT name="etabferme" />
				<option value="1">Ouvert</option>
				<option value="2">Fermé</option>
			</select>
			<span class="legende">Etablissement</span>
		</p>
		<p>
			<SELECT name="vacances">
				<option value="0">Cours</option>
				<option value="1">Vacances</option>
			</SELECT>
			<span class="legende">Vacances / Cours</span>
		</p>
			<input type="submit" name="valider" value="enregistrer" />
		</form>
</fieldset>

	';
} // if ($new_periode == "ok")

if (isset($message_new)) {
	echo $message_new;
}
?>