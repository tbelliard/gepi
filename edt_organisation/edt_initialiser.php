<?php

/**
 * Fichier d'initialisation de l'EdT
 * Pour effacer la table avant une nouvelle initialisation il faut faire un TRUNCATE TABLE nom_table;
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
 */
$titre_page = "Emploi du temps - Initialisation";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");
require_once("./fonctions_cours.php");

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

// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php"); ?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">
<?php
	// Initialisation des variables de la page
$initialiser = isset($_POST["initialiser"]) ? $_POST["initialiser"] : NULL;
$choix_prof = isset($_POST["prof"]) ? $_POST["prof"] : NULL;
$enseignement = isset($_POST["enseignement"]) ?$_POST["enseignement"] : NULL;
$ch_heure = isset($_POST["ch_heure"]) ? $_POST["ch_heure"] : NULL;
$ch_jour_semaine = isset($_POST["ch_jour_semaine"]) ? $_POST["ch_jour_semaine"] : NULL;
$duree = isset($_POST["duree"]) ? $_POST["duree"] : NULL;
$heure_debut = isset($_POST["heure_debut"]) ? $_POST["heure_debut"] : NULL;
$choix_semaine = isset($_POST["choix_semaine"]) ? $_POST["choix_semaine"] : NULL;
$login_salle = isset($_POST["login_salle"]) ? $_POST["login_salle"] : NULL;
$init = isset($_GET["init"]) ? $_GET["init"] : NULL;

	// On affiche ou non les infos de base
$aff_reglages = GetSettingEdt("edt_aff_init_infos");

if ($aff_reglages == "oui") {
	echo "
	<p>
	Pour entrer des informations dans l'emploi du temps de Gepi, il y a plusieurs possibilit&eacute;s.
	<br />
Pour les entr&eacute;es simples, la saisie manuelle est possible.
Elle vous permettra de rentrer heure par heure des informations
en v&eacute;rifiant si deux cours ne se chevauchent pas.</p>
<p>Nous vous conseillons pour cela de saisir les emplois du temps des professeurs directement sur leur affichage.
 Pour cela, cliquez sur [Visionner], [Professeur], puis vous choisissez le professeur dans la liste déroulante.
  En cliquant ensuite sur (-+-), une fenêtre apparait où vous pouvez saisir les cours. Un module de vérification
  des cours est présent mais ne peut se substituer à un véritable logiciel de fabrication des emplois du temps.</p>

	<h5 class=\"red\">Attention ! seuls les enseignements
d&eacute;finis dans Gepi peuvent appara&icirc;tre dans l'emploi du temps.</h5>
<br />";
}
else {

}
	// Saisie manuelle de l'emploi du temps
echo '
	<span class="legend">Vous ne devriez utiliser ce menu que pour de rares occasions car il n\'est pas aussi
	performant que la méthode décrite plus haut.</span>
		<form action="edt_initialiser.php" name="choix_prof" method="post">
	<fieldset id="init_edt1">
		<legend>Saisie manuelle</legend>

		<select name="prof" onchange=\'document.choix_prof.submit();\'>
			<option value="rien">Choix du professeur</option>
	';

	$tab_select = renvoie_liste("prof");

	echo "	\n";
for($i=0;$i<count($tab_select);$i++) {
	if(isset($choix_prof)){
		if($choix_prof==$tab_select[$i]["login"]){
			$selected=" selected='selected'";
		}
		else{
			$selected="";
		}
	}
	else{
		$selected="";
	}
	echo "
			<option value='".$tab_select[$i]["login"]."'".$selected.">".$tab_select[$i]["nom"].' '.$tab_select[$i]["prenom"]."</option>\n";
}

echo '
 		</select>
			<input type="hidden" name="initialiser" value="ok" />
	</fieldset>
		</form>';

	// Ensuite, on propose la liste des enseignements de ce professeur associés à la matière
if (isset($choix_prof)) {
	echo '
			<form action="edt_initialiser.php" name="choix_enseignement" method="post">
	<fieldset id="init_edt2">
		<legend>Choix du cours</legend>

<table border="0" cellspacing="4" cellpadding="0">
	<tr>
		<td>

			<select name="enseignement">';
	echo "\n";

		$tab_enseignements = get_groups_for_prof($choix_prof);
	echo "
				<option value=\"rien\">Choix de l'enseignement</option>\n";

	// On détermine le selected
	for($i=0; $i<count($tab_enseignements); $i++) {
		if(isset($enseignement)){
			if($enseignement==$tab_enseignements[$i]["id"]){
				$selected=" selected='selected'";
			}else{
				$selected="";
			}
		}else{
			$selected="";
		}
			echo "
				<option value=\"".$tab_enseignements[$i]["id"]."\"".$selected.">".$tab_enseignements[$i]["classlist_string"]." : ".$tab_enseignements[$i]["description"]."</option>\n";
	}

	// On ajoute sa liste des aid
		$tab_aid = renvoieAid("prof", $choix_prof);
	for($i = 0; $i < count($tab_aid); $i++) {
		$nom_aid = mysql_fetch_array(mysql_query("SELECT nom FROM aid WHERE id = '".$tab_aid[$i]["id_aid"]."'"));
		echo '
				<option value="AID|'.$tab_aid[$i]["id_aid"].'">'.$nom_aid["nom"].'</option>
		';
	}

	echo '
			</select>

			<input type="hidden" name="initialiser" value="ok" />
			<input type="hidden" name="prof" value="'.$choix_prof.'" />
		</td>
		<td>
			<select name="ch_jour_semaine">
				<option value="rien">Jour</option>';
	echo "\n";

	// On propose aussi le choix du jour

	$req_jour = mysql_query("SELECT id_horaire_etablissement, jour_horaire_etablissement FROM horaires_etablissement");
	$rep_jour = mysql_fetch_array($req_jour);

	$tab_select_jour = array();

	for($a=0; $a<=count($rep_jour); $a++) {
		$tab_select_jour[$a]["id"] = mysql_result($req_jour, $a, "id_horaire_etablissement");
		$tab_select_jour[$a]["jour_sem"] = mysql_result($req_jour, $a, "jour_horaire_etablissement");
		if(isset($ch_jour_semaine)){
			if($ch_jour_semaine==$tab_select_jour[$a]["jour_sem"]){
				$selected=" selected='selected'";
			}else{
				$selected="";
			}
		}else{
			$selected="";
		}
		echo "
		<option value='".$tab_select_jour[$a]["jour_sem"]."'".$selected.">".$tab_select_jour[$a]["jour_sem"]."</option>\n";
	}
	echo '
			</select>
		</td>

		<td>
			<select name="ch_heure">
				<option value="rien">Horaire</option>';
	echo "\n";
	// On propose aussi le choix de l'horaire

	$req_heure = mysql_query("SELECT id_definie_periode, nom_definie_periode, heuredebut_definie_periode, heurefin_definie_periode FROM absences_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
	$rep_heure = mysql_num_rows($req_heure);
	$tab_select_heure = array();

	for($b=0; $b<$rep_heure; $b++) {

		$tab_select_heure[$b]["id_heure"] = mysql_result($req_heure, $b, "id_definie_periode");
		$tab_select_heure[$b]["creneaux"] = mysql_result($req_heure, $b, "nom_definie_periode");
		$tab_select_heure[$b]["heure_debut"] = mysql_result($req_heure, $b, "heuredebut_definie_periode");
		$tab_select_heure[$b]["heure_fin"] = mysql_result($req_heure, $b, "heurefin_definie_periode");
		if(isset($ch_heure)){
			if($ch_heure==$tab_select_heure[$b]["id_heure"]){
				$selected=" selected='selected'";
			}else{
				$selected="";
			}
		}else{
			$selected="";
		}
		echo "
		<option value='".$tab_select_heure[$b]["id_heure"]."'".$selected.">".$tab_select_heure[$b]["creneaux"]." : ".$tab_select_heure[$b]["heure_debut"]." - ".$tab_select_heure[$b]["heure_fin"]."</option>\n";

	}
	echo '
			</select>
		</td>
		<td>
		</td>
	</tr>
	<tr>
		<td>
			<select name="heure_debut">
				<option value="0">Le cours commence au début d\'un créneau</option>
				<option value="0.5">Le cours commence au milieu d\'un créneau</option>
			</select>
		</td>';

	// On propose aussi le choix du type de semaine et l'heure de début du cours

	echo '
		<td>
			<select name="duree">
				<option value="2">1 heure</option>
				<option value="3">1.5 heure</option>
				<option value="4">2 heures</option>
				<option value="5">2.5 heures</option>
				<option value="6">3 heures</option>
				<option value="7">3.5 heures</option>
				<option value="8">4 heures</option>
				<option value="0.5">1/2 heure</option>
			</select>
		</td>
		<td>
			<select name="choix_semaine">
				<option value="0">Toutes les semaines</option>
		';
	// on récupère les types de semaines
				//<option value="2">Semaines paires</option>
				//<option value="1">Semaines impaires</option>
	$req_semaines = mysql_query('SELECT SQL_SMALL_RESULT DISTINCT type_edt_semaine FROM edt_semaines LIMIT 10');
	//mysql_query("SELECT type_edt_semaine FROM edt_semaines");
	//mysql_query('SELECT SQL_SMALL_RESULT DISTINCT type_edt_semaine FROM edt_semaines LIMIT 10');
	$nbre_semaines = mysql_num_rows($req_semaines);

	for ($s=0; $s<$nbre_semaines; $s++) {
			$rep_semaines[$s]["type_edt_semaine"] = mysql_result($req_semaines, $s, "type_edt_semaine");
		echo '
				<option value="'.$rep_semaines[$s]["type_edt_semaine"].'">Semaine '.$rep_semaines[$s]["type_edt_semaine"].'</option>
		';
	}

	echo '
			</select>
		</td>
		<td>
		</td>
	</tr>
	<tr>
		<td>
			<select  name="login_salle">
				<option value="rien">Salle</option>
	';
	// Choix de la salle
	$tab_select_salle = renvoie_liste("salle");

	for($c=0;$c<count($tab_select_salle);$c++) {
		if(isset($login_salle)){
			if($login_salle==$tab_select_salle[$c]["id_salle"]){
				$selected=" selected='selected'";
			}else{
				$selected="";
			}
		}else{
			$selected="";
		}
		echo "
				<option value='".$tab_select_salle[$c]["id_salle"]."'".$selected.">".$tab_select_salle[$c]["nom_salle"]."</option>\n";
	}
	echo '
			</select>
		</td>
		<td>
			<select name="periode_calendrier">
				<option value="rien">Année entière</option>
	';
	// Choix de la période définie dans le calendrier
	$req_calendrier = mysql_query("SELECT * FROM edt_calendrier WHERE etabferme_calendrier = '1' AND etabvacances_calendrier = '0'");
	$nbre_calendrier = mysql_num_rows($req_calendrier);
		for ($a=0; $a<$nbre_calendrier; $a++) {
			$rep_calendrier[$a]["id_calendrier"] = mysql_result($req_calendrier, $a, "id_calendrier");
			$rep_calendrier[$a]["nom_calendrier"] = mysql_result($req_calendrier, $a, "nom_calendrier");
			echo '
				<option value="'.$rep_calendrier[$a]["id_calendrier"].'">'.$rep_calendrier[$a]["nom_calendrier"].'</option>
			'."\n";
		}

	echo '
			</select>
		</td>
		<td>
			<input type="submit" name="Valider" value="Valider" />
		</td>
		<td>
		</td>
	</tr>
	</table>
	</fieldset>
	</form>';

	// Traitement et enregistrement des entrées manuelles de l'EdT

	if (isset($choix_prof) AND ($enseignement == "rien" OR $login_salle == "rien" OR $ch_heure == "rien" OR $ch_jour_semaine == "rien")) {
		echo '
	<p class="refus">Vous devez renseigner tous les champs !</p><br />';
	}else {
		if (isset($choix_prof) AND $enseignement != NULL) {
			//echo "<font color=\"green\">OK Tout Est OK !</font>";


			// Vérification que la salle est libre à ce jour cette heure
			$verif_salle = mysql_query("SELECT id_cours FROM edt_cours WHERE
						id_salle ='".$login_salle."' AND
						jour_semaine = '".$ch_jour_semaine."' AND
						id_definie_periode = '".$ch_heure."' AND
						(id_semaine = '0' OR id_semaine = '".$choix_semaine."')")
							 or die ('Erreur dans la verif salle');
			$rep_verif_s = mysql_fetch_array($verif_salle);

			$nbre_verif_s = mysql_num_rows($verif_salle);
			if ($nbre_verif_s != 0) {
				$req_present_s = mysql_query("SELECT id_groupe FROM edt_cours WHERE id_cours = '".$rep_verif_s['id_cours']."'");
				$rep_present_s = mysql_fetch_array($req_present_s);
				// On vérifie si ce n'est pas une AID
				if (retourneAid($rep_present_s["id_groupe"]) != "non") {
					$aid = retourneAid($rep_present_s["id_groupe"]);
					echo "<p class=\"refus\">Cette salle est déjà occupée par un groupe AID( ".$aid." ).</p>";
				}else{
					$tab_present_s = get_group($rep_present_s["id_groupe"]);
					echo "<p class=\"refus\">Cette salle est déjà occupée par les ".$tab_present_s["classlist_string"]." en ".$tab_present_s["description"]."</p><br />";
				}

			}

			// Vérification que ce prof n'a pas déjà cours à ce moment là
			$verif_prof = mysql_query("SELECT * FROM edt_cours, j_groupes_professeurs WHERE edt_cours.jour_semaine='".$ch_jour_semaine."' AND edt_cours.id_definie_periode='".$ch_heure."' AND edt_cours.id_semaine = '".$choix_semaine."' AND edt_cours.id_groupe=j_groupes_professeurs.id_groupe AND login='".$choix_prof."' AND edt_cours.heuredeb_dec = '".$heure_debut."'") or die('erreur verif prof !');
			$rep_verif_prof = mysql_fetch_array($verif_prof);
			$nbre_verif_prof = mysql_num_rows($verif_prof);
			if ($nbre_verif_prof != 0) {
				$tab_present_p = get_group($rep_verif_prof["id_groupe"]);
				echo "<p class=\"refus\">Ce professeur a déjà cours avec les ".$tab_present_p["classlist_string"]." en ".$tab_present_p["description"]."</p><br />";
			}

			// Si c'est bon, on enregistre le cours dans l'EdT
			if ($nbre_verif_prof === 0 AND $nbre_verif_s === 0) {
				$insert_edt = mysql_query("INSERT INTO edt_cours (id_cours, id_groupe, id_salle, jour_semaine, id_definie_periode, duree, heuredeb_dec, id_semaine, modif_edt)
					VALUE ('', '".$enseignement."', '".$login_salle."', '".$ch_jour_semaine."', '".$ch_heure."', '".$duree."', '".$heure_debut."', '".$choix_semaine."', '0')") or die('Erreur dans l\'enregistrement, il faut recommencer !');

				// et on affiche les infos sur le cours enregistré
				$tab_infos = get_group($enseignement);
				$contenu="";
				foreach ($tab_infos["eleves"][1]["users"] as $eleve_login) {
					$contenu .=$eleve_login['nom']." ".$eleve_login['prenom']."<br />";
				}
				$titre_listeleve = "Liste des élèves";

				$classe_js = "<a href=\"#\" onmouseover=\"afficher_div('nouveau_cours','Y',10,10);return false;\">Liste</a>
					".creer_div_infobulle("nouveau_cours", $titre_listeleve, "#330033", $contenu, "#FFFFFF", 15,0,"n","n","y","n");
				echo "<p>Ce cours est enregistré :<font color=\"green\" size=\"1\">
					Les ".$tab_infos["classlist_string"]." en ".$tab_infos["description"]." avec ".$choix_prof." (".$classe_js.").</font></p>";
			}
		}
	}
}else {
//echo '
//		</fieldset>';
}



	// une fois initialisé, la partie suivante peut être verrouillée
			// Pour déverrouiller, le traitement se fait ici là

if (isset($init) AND $init == "ok") {
	$req_reprendre_init = mysql_query("UPDATE edt_setting SET valeur = 'oui' WHERE reglage = 'edt_aff_init_infos2'");
}
else if (isset($init) AND $init == "ko") {
	$req_reprendre_init = mysql_query("UPDATE edt_setting SET valeur = 'non' WHERE reglage = 'edt_aff_init_infos2'");
}

$aff_reglages2 = GetSettingEdt("edt_aff_init_infos2");

if ($aff_reglages2 == "oui") {
	echo '
	<span class="refus">Le module EdT n\'est pas initialis&eacute;.
	<a href="./edt_initialiser.php?init=ko">Cliquer ici quand vous avez termin&eacute; l\'initialisation</a></span>

	<h5>Pour l\'initialisation du d&eacute;but d\'ann&eacute;e, les diff&eacute;rents logiciels de conception des emplois
 	du temps ne permettent pas d\'avoir une seule proc&eacute;dure. Il convient donc de bien d&eacute;terminer ce
	 qui est possible. Avant de vous lancer dans cette initialisation, vous devez vous assurer d\'avoir param&eacute;tr&eacute;
 	l\'ensemble des informations relatives aux horaires de l\'&eacute;tablissement. En suivant les instructions suivantes
 	tout devrait bien se passer.</h5>
 	<h4 class="red">Toutes les proc&eacute;dures d\'initialisation par csv ou xml effacent l\'emploi du temps déjà créé,
	 sauf si vous leur demandez de ne pas le faire.</h4>

 	<div id="lien"><a href="./edt_init_csv.php">Cliquer ici pour une initialisation par fichiers csv</a></div>
 		';

 //<div id=\"lien\"><a href=\"./index_edt.php?initialiser=ok&xml=ok\">Cliquer ici pour une initialisation par fichiers xml (type export STSWeb)</a></div>
}
else if ($aff_reglages2 == "non") {
	echo '
	<span class="accept">Le module EdT est initialis&eacute;.
	<a href="./edt_initialiser.php?init=ok">Cliquer ici pour reprendre cette initialisation</a></span>
		';
}
else {
	echo '
	Il y a un probl&egrave;me de r&eacute;glage dans votre base de donn&eacute;es, il faut peut-&ecirc;tre la mettre &agrave; jour.
		';

}

	/* A enlever après vérification de la fonction

echo "<br />\n<br />\n<table CELLSPACING=\"1\" BORDER=\"1\"><tr>\n";
$aff_essai = mysql_query("SELECT jgp.id_groupe FROM j_groupes_professeurs jgp, j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgp.login = 'FDAUCOUR' AND jgc.id_classe = '6' AND jgm.id_matiere = 'HG' AND jgp.id_groupe = jgc.id_groupe AND jgp.id_groupe = jgm.id_groupe") OR die ('saucisse');
$nbre_tab_essai = count($aff_essai);
echo "<td HEIGHT=\"50\" WIDTH=\"150\">tab_essai : ".$nbre_tab_essai."<br /></td>\n";
echo "<td WIDTH=\"250\">".$aff_essai."<br /><!--coucou --></td>\n</tr>\n";
$aff_tab_essai = array();
		while($rep_toutes = mysql_fetch_array($aff_essai))
		{
		$aff_tab_essai[]=$rep_toutes["id_groupe"];
		}
		echo "<tr>\n<td HEIGHT=\"50\">rep_toutes : ".$rep_toutes["id_groupe"]."<br /></td>\n";
		echo "<td>";
	for($i=0; $i<count($aff_essai); ) {

			echo "aff_tab_essai : ".$aff_tab_essai[$i]."<br />";
		$i++;
	}

	$current_group=get_group($aff_tab_essai[0]);

		$contenu="";
		foreach ($current_group["eleves"][1]["users"] as $eleve_login) {
			$contenu .=$eleve_login['nom']." ".$eleve_login['prenom']."<br />";
		}
		$titre_listeleve = "Liste des élèves";

	$classe_js = aff_popup("LISTE", "edt", $titre_listeleve, $contenu);

		echo "".$current_group["matiere"]["matiere"]." ".$current_group["description"]." ";

		echo " ".$classe_js."</td>\n</tr>\n</table>\n";

		========FIN DU TRAVAIL A ENLEVER==================
		*/

?>

	</div>
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>