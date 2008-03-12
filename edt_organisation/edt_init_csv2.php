<?php

/**
 * Fichier qui permet de faire l'import de l'EdT depuis un logiciel propriétaire
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
 */
$titre_page = "Emploi du temps - Initialisation";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");
require_once("./edt_init_fonctions.php");

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
//==============PROTOTYPE===============
$utilisation_prototype = "ok";
//============fin PROTOTYPE=============
// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php");

//+++++++++++++++++++GESTION DU RETOUR vers absences+++++++++++++++++
$_SESSION["retour"] = "edt_init_csv";
//+++++++++++++++++++FIN GESTION RETOUR vers absences++++++++++++++++

?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php

 // Initialisation des variables
$action = isset($_POST["action"]) ? $_POST["action"] : NULL;
$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
$truncate_cours = isset($_POST["truncate_cours"]) ? $_POST["truncate_cours"] : NULL;
$aff_infos = isset($_POST["aff_infos"]) ? $_POST["aff_infos"] : NULL;
$recommencer = isset($_POST["recommencer"]) ? $_POST["recommencer"] : NULL;
$etape = NULL;
$aff_etape = NULL;

// Si l'utilisateur veut recommencer, on efface toutes les entrées de l'étape qu'il a demandée
if ($recommencer != 'non' AND is_numeric($recommencer)) {

	// On efface toutes les entrées de cette étape (les étapes vont de 0 à 12)
	$supprimer = mysql_query("DELETE FROM edt_init WHERE ident_export >= '".$recommencer."' AND ident_export != 'fichierTexte2'")
						OR error_reporting('Erreur dans le $recommencer : '.mysql_error());
	$modifier = mysql_query("UPDATE edt_init SET nom_export = '".$recommencer."' WHERE ident_export = 'fichierTexte2'")
						OR error_reporting('Erreur dans le $modifier : '.mysql_error());
}

// On teste d'abord pour savoir à quelle étape on est
$query = mysql_query("SELECT nom_export, nom_gepi FROM edt_init WHERE ident_export = 'fichierTexte2'");
// On affiche le numéro de l'étape
if ($query) {
	$etape_effectuee = mysql_fetch_array($query);
	$etape = $etape_effectuee["nom_export"];
	$date_last = explode(" ", $etape_effectuee["nom_gepi"]);
	// Si $etape est null, on crée l'entrée
	if ($etape == '') {
		$insert = mysql_query("INSERT INTO edt_init SET ident_export = 'fichierTexte2', nom_export = '0', nom_gepi = '".date("d-m-Y h:i")."'");
		$etape = '0';
	}
	$aff_etape = '
		<h3 class="gepi" style="font-weight: bold;">Vous êtes actuellement à l\'étape numéro '.$etape.'</h3>
	';
	if ($date_last != '' AND $etape != 0) {
		echo '
		<p>Cette initialisation a été commencée le : '.$date_last[0].' à '.$date_last[1].'</p>
		';
	}
}else{
	// On crée le compteur d'étapes
	$insert = mysql_query("INSERT INTO edt_init SET ident_export = 'fichierTexte2', nom_export = '0', nom_gepi = '".date("d-m-Y h:i")."'");
	$etape = 0;
	$aff_etape = '
	<p class="red">Vous n\'avez pas commencé la concordance.</p>';
}
echo '<div id="divCsv2">';
echo $aff_etape;
// On commence le travail sur le fichier
if ($action == "upload_file") {
    // On vérifie le nom du fichier...
    if(strtolower($csv_file['name']) == "g_edt_2.csv") {
        // Le nom est ok. On ouvre le fichier
        $fp = fopen($csv_file['tmp_name'],"r");

        if(!$fp) {
            // Prob sur l'ouverture du fichier
            echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
            echo "<p><a href=\"./edt_init_csv.php\">Cliquer ici </a> pour recommencer !</center></p>";
        } else {
            // A partir de là, on vide la table edt_cours
            if ($truncate_cours == "oui") {
            	$vider_table = mysql_query("TRUNCATE TABLE edt_cours");
            }

            // On ouvre alors toutes les lignes de tous les champs
			$tableau = array();
			// On affiche le tire pour chaque étape
			$titre = array('Les jours de la semaine',
							'Les créneaux : vous devez faire la concordance sur les créneaux de début de cours <br />&nbsp;&nbsp;&nbsp;-> <span style="font-style: italic;">Précisez le créneau de début pour tous les créneaux</span>.',
							'Les divisions : le nom des classes',
							'Les matières : appelées aussi disciplines',
							'Les professeurs : ',
							'Les salles : si aucune salle n\'existe encore, Gepi va les créer',
							'Les groupes : subdivisions des regroupements',
							'Les regroupements : ce sont les enseignements ou les AID de Gepi',
							'Les effectifs : non utilisés pour l\'EdT',
							'Les Modalités : non utilisées pour l\'EdT',
							'La fréquence : le type de semaine ainsi que les cours qui ne durent pas toute l\'année',
							'L\'aire : non utilisée dans Gepi',
							'Vous allez pouvoir enregistrer les cours dans la base');
			// On détermine quel est le helper appelé
			$helpers = array('select_jours', 'select_creneaux', 'select_classes', 'select_matieres', 'select_professeurs', 'aucun', 'aucun',
							'select_aid_groupes', 'aucun', 'aucun', 'select_frequence', 'aucun');

			echo '<p>'.$titre[$etape].'</p>';
			if ($etape != 12) {
				while($tab = fgetcsv($fp, 1024, ";")) {
					if (in_array($tab[$etape], $tableau) === FALSE) {
						// Puisque la valeur du champ n'est pas encore dans $tableau, on l'insère pour éviter les doublons
						if ($tab[$etape] != '') {
							$tableau[] = $tab[$etape];
						}
					}
				}
				// On commence le traitement des entrées et des sorties
				echo '<form name="edtInitCsv2" action="edt_init_concordance2.php" method="post">';
				$nbre_lignes = count($tableau);

				for($l = 0; $l < $nbre_lignes; $l++){
					echo '
					<p>
					<input type="hidden" name="nom_export_'.$l.'" value="'.$tableau[$l].'" />
					<label for="nomGepi'.$l.'">'.$tableau[$l].'</label>
					';
					// On ne garde que le premier nom de la valeur du champ de l'import pour tester ensuite le selected du select
					if ($etape != 2) {
						$test_selected = explode(" ", $tableau[$l]);
					}else{
						$test_selected[0] = $tableau[$l];
					}

					$nom_select = 'nom_gepi_'.$l; // pour le nom du select
					$nom_selected = $test_selected[0]; // pour le selected du helper
					$nom_id_select = 'nomGepi'.$l; // pour le id du select (en mettre en liaison avec le for du label ci-dessus)
					$style_select = ' style="text-align: center;"';
					// On appelle le bon helper
					if ($helpers[$etape] != 'aucun') {
						include("helpers/".$helpers[$etape].".php");
					}else{
						echo '
						<input type="hidden" name="'.$nom_select.'" value="none" />';
					}

					echo '</p>';
				}
				$aff_enregistrer = 'concordances';
			}elseif($etape == 12){
				echo '
				<form name="edtInitCsv2" action="edt_init_concordance2.php" method="post">';
				$b = 0; // c'est pour le while

				// C'est là qu'on enregistre les cours en se servant des données recueillies auparavant
				while($tab = fgetcsv($fp, 1024, ";")) {
					$nbre_lignes = $b;
					$toutelaligne = NULL;
					// On rentre toutes les cellules de la ligne dans une seule variable
					for($t = 0; $t < 12; $t++){
						// On fait attention au traitement des données envoyées en post
						//if (!get_magic_quotes_gpc()){
						//	$toutelaligne .= addslashes($tab[$t]).'|';
						//}else{
							$toutelaligne .= $tab[$t].'|';
						//}
					}

						echo '
					<input type="hidden" name="ligne_'.$b.'" value="'.$toutelaligne.'" />';
					$b++; // on incrémente le compteur pour le name
				}
				echo 'Votre fichier comporte '.$nbre_lignes.' cours.';
				$aff_enregistrer = 'cours';
			}else{
				// rien pour le moment
			}
			echo '
					<input type="hidden" name="nbre_lignes" value="'.$nbre_lignes.'" />
					<input type="hidden" name="etape" value="'.$etape.'" />
					<input type="hidden" name="aff_infos" value="'.$aff_infos.'" />
					<input type="hidden" name="concord_csv2" value="ok" />
					<input type="submit" name="enregistrer" value="Enregistrer ces '.$aff_enregistrer.'" />
				</form>';
		}
	} else{
		// Ce n'est pas le bon nom de fichier
		echo '<p>Ce n\'est pas le bon nom de fichier !</p>
			<a href="./edt_init_csv2.php">Recommencer avec le bon fichier.</a>
		';
	}
}
echo '</div>'; // fin du div id="DivCsv2"
?>
<h3 class="red">Initialiser l'emploi du temps de Gepi &agrave; partir d'un export csv d'un logiciel propri&eacute;taire.</h3>

<p style="font-weight: bold;">Vous pouvez obtenir un csv valide sur UnDeuxTemps en passant par UDT/admin > Recherche > Emploi du temps > toutes options par défaut.</p>

<p>Pour chaque partie, vous allez devoir faire le lien avec les informations de Gepi. Vous devrez donc faire passer le fichier csv 12 fois et
la derni&egrave;re sera la plus longue. Par contre, les 11 premi&egrave;re &eacute;tapes seront conserv&eacute;es par Gepi et vous pourrez faire la derni&egrave;re
 &eacute;tape (importation des cours eux-m&ecirc;mes autant de fois que vous le d&eacute;sirez (en effa&ccedil;ant les anciens cours ou non).</p>
	<p>Attention, l'&eacute;tape 12 n'efface pas les donn&eacute;es d&eacute;j&agrave; existantes pour les cours sauf si vous cochez le bouton.</p>
	<p><span class="red">Attention</span> de respecter au mieux les heures, jour, nom de mati&egrave;re,... de Gepi que vous avez pr&eacute;cis&eacute;s auparavant,
	l'initialisation de l'emploi du temps en sera simplifi&eacute;e.</p>
	<p>
	Vous devez fournir un fichier csv dont les champs suivants 	 doivent &ecirc;tre pr&eacute;sents, dans l'ordre, <b>s&eacute;par&eacute;s
	par un point-virgule et encadr&eacute;s par des guillemets ""</b> <span style="color: green; font-weight: bold;">(sans ligne d'ent&ecirc;te)</span> :</p>
<ol>
	<li>Jour</li>
	<li>Heure</li>
	<li>Div</li>
	<li>Matière</li>
	<li>Professeur</li>
	<li>Salle</li>
	<li>Groupe</li>
	<li>Regroup</li>
	<li>Eff</li>
	<li>Mo</li>
	<li>Freq</li>
	<li>Aire</li>
</ol>

	<p>Veuillez préciser le nom complet du fichier <b>g_edt_2.csv</b>.</p>
		<form enctype="multipart/form-data" action="edt_init_csv2.php" method="post">

			<input type="hidden" name="action" value="upload_file" />
			<input type="hidden" name="initialiser" value="ok" />

			<p><label for="truncateCours">Effacer les cours d&eacute;j&agrave; cr&eacute;&eacute;s </label>
			<input type="checkbox" id="truncateCours" name="truncate_cours" value="oui" />
			<label for="affInfosEdt">Afficher l'enregistrement de tous les cours</label>
			<input type="checkbox" id="affInfosEdT" name="aff_infos" value="oui" checked="checked" /></p>

			<p><input type="file" size="80" name="csv_file" /></p>

			<p><label for="">Vous pouvez recommencer depuis l'&eacute;tape : </label>
			<select name="recommencer">
				<option value="non">non</option>
				<option value="0">0</option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
			</select></p>

			<p><input type="submit" value="Valider" /></p>
		</form>
	</div>
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>