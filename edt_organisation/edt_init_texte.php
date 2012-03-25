<?php

/**
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau, Julien Jocal
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

// edt_init_textes.php est un fichier qui permet d'initialiser l'EdT par les exports de type "Charlemagne".
// On passe par une table edt_init qui a 4 champs : id_init (auto incrémenté), identifiant, nom_gepi, nom_export

$titre_page = "Emploi du temps - Initialisation EDT";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
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
	Die(ASK_AUTHORIZATION_TO_ADMIN);
}
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";
//==============PROTOTYPE===============
$utilisation_prototype = "ok";
//============fin PROTOTYPE=============
// On insère l'entête de Gepi
require_once("../lib/header.inc.php");

// On ajoute le menu EdT
require_once("./menu.inc.php");

// ======================= traitement du fichier =====================

$action = isset($_POST["action"]) ? $_POST["action"] : NULL;
$txt_file = isset($_FILES["txt_file"]) ? $_FILES["txt_file"] : NULL;
$truncate_cours = isset($_POST["truncate_edt"]) ? $_POST["truncate_edt"] : NULL;
$etape = NULL;
$aff_etape = NULL;

echo 	'<div id="lecorps">';
// On teste d'abord pour savoir à quelle étape on est
$query = mysql_query("SELECT nom_export FROM edt_init WHERE ident_export = 'fichierTexte'");
// On affiche le numéro de l'étape
if ($query) {
	$etape_effectuee = mysql_fetch_array($query);
	if ($etape_effectuee["nom_export"] != '') {
		$aff_etape = '
		<h3 class="gepi">Vous êtes actuellement à l\'étape numéro '.$etape_effectuee["nom_export"].'</h3>';
	}else{
		$aff_etape = '
		<p class="red">Vous n\'avez pas commencé la concordance.</p>';
	}

}else{
	$aff_etape = '
	<p class="red">Vous n\'avez pas commencé la concordance.</p>';
}
echo $aff_etape;
// On va donc afficher le contenu du fichier tel qu'il va être enregistré dans Gepi
// en proposant des champs de saisie pour modifier les données si on le souhaite
if ($action == "upload_file") {


	// On vérifie le nom du fichier...
	if(my_strtolower($txt_file['name']) == "emploidutemps.txt") {
		// Le nom est ok. On ouvre le fichier
        $fp = fopen($txt_file['tmp_name'],"r");

		if(!$fp) {
			// Prob sur l'ouverture du fichier
			echo "<p>Impossible d'ouvrir le fichier texte !</p>\n";
			echo "<p style=\"text-align: center;\"><a href=\"./edt_init_texte.php\">Veuillez recommencer</a></p>\n";
        }else {
			// On vérifie si on demande d'effacer la table en question
			if ($truncate_cours == "oui") {
			$vider_table = mysql_query("TRUNCATE TABLE edt_init");
			} // fin du !fp

			// On peut enfin s'attaquer au travail sur le fichier
			$nbre_rep = mysql_num_rows($query);
			if ($nbre_rep === 0) {
				// C'est qu'on est au tout début, au premier passage et donc
				// on crée le champ fichierTexte
				$insert = mysql_query("INSERT INTO edt_init SET ident_export = 'fichierTexte', nom_export = '1', nom_gepi = '".date("d-m-Y h:i")."'");
				$etape = 1;
			}else{
				// On récupère d'abord le numéro de l'étape actuel
				$etape = mysql_result($query, 0,"nom_export");
				// On incrémentera de 1 si cette nouvelle étape est validée
			}

			$neuf_etapes = array("PROFESSEUR", "CLASSE", "GROUPE", "PARTIE", "MATIERE", "ETABLISSEMENT", "SEMAINE", "CONGES", "COURS");
			$autorise = "stop";
			$neuf_etapes[9] = ''; // on initialise la fin du fichier texte

			// Avant de lancer le while, on met en place le formulaire qui enverra les concordances
			echo '
				<form name="concordance" action="edt_init_concordance.php" method="post">';
			// On ouvre alors le fichier ligne par ligne
				$numero = 0;
			while($tab = fgetcsv($fp, 1024, "	")) {
				$nom_selected = $nom_select = NULL;
				if ($tab[0] == $neuf_etapes[$etape - 1]) {
					// On commence l'étape demandée et on autorise donc à récupérer les données utiles
					$autorise = "continue";
					echo '<p>Vous êtes dans l\'étape '.$etape.'</p>';
					echo '<p>Gestion des "'.$neuf_etapes[$etape - 1].'".</p>';

				}elseif($tab[0] == $neuf_etapes[$etape]){
					// On arrive à l'étape suivante et donc on arrête de récupérer les données du fichier
					$autorise = "stop";
					echo '<p>La lecture du fichier pour cette étape est terminée, vous devez maintenant faire les concordances.</p>';
				}
				// Si $autorise = "continue"; alors on peut utiliser les infos

				if ($autorise == "continue") {
					if ($etape == 1) {
						// On traite les professeurs
						if ($tab[0] == "PROFESSEUR") {
							$nbre_lignes = $tab[1];
							echo 'Il y a '.$tab[1].' professeurs.<br />'."\n";
						}else{
							// On détermine si la première lettre du prénom existe
							$prenom = (isset($tab[3]) AND $tab[3] != '') ? '('.$tab[3].'.)' : NULL;
							// on permet la concordance
							echo 'Numéro : '.$tab[0].' civilité :'.$tab[1].' nom : <b>'.$tab[2].' '.$prenom.'</b>';
							echo '<input type="hidden" name="numero_texte_'.$numero.'" value="'.$tab[0].'" />';
							$nom_select = "nom_gepi_".$numero; // pour le name du select
							$nom_selected = my_strtoupper(remplace_accents($tab[2], 'all_nospace')); // pour le selected
							echo $nom_selected;
							include("helpers/select_professeurs.php");
							echo '<br />'."\n";
						}
					}elseif($etape == 2){
						// On traite des classes
						if($tab[0] == "CLASSE"){
							$nbre_lignes = $tab[1];
							echo 'Il y a '.$tab[1].' classes.<br />'."\n";
						}else{
							// On permet la concordance
							echo 'Numéro : '.$tab[0].' classe :<b>'.$tab[1].'</b>';
							echo '<input type="hidden" name="numero_texte_'.$numero.'" value="'.$tab[0].'" />';
							$nom_select = "nom_gepi_".$numero; // pour le name du select
							$nom_classe = $tab[1]; // pour le selected
							include("helpers/select_classes.php");
							echo '<br />'."\n";
						}
					}elseif($etape == 3){
						// On traite des GROUPES
						if($tab[0] == "GROUPE"){
							$nbre_lignes = $tab[1];
							echo 'Il y a '.$tab[1].' groupes.<br />'."\n";
						}else{
							// On permet la concordance
							echo 'Numéro : '.$tab[0].' groupe :<b>'.$tab[1].'</b>';
							echo '<input type="hidden" name="numero_texte_'.$numero.'" value="'.$tab[0].'" />';
							$nom_select = "nom_gepi_".$numero;
							include("helpers/select_aid_groupes.php");
							echo '<br />'."\n";
						}
					}elseif($etape == 4){
						// On traite des "PARTIES"
						if ($tab[0] == "PARTIE") {
							$nbre_lignes = $tab[1];
							echo 'Il y a '.$tab[1].' "parties".<br />'."\n";
						}else{
							echo '
							<p>A priori, les PARTIES ne servent pas pour l\'emploi du temps car l\'exportation ne donne pas la liste des élèves</p>
							<p>Cliquez sur le bouton ci-dessous pour passer à l\'étape suivante.</p>';
							break;
						}
					}elseif($etape == 5){
						// On traite des "MATIERE"
						if($tab[0] == "MATIERE"){
							$nbre_lignes = $tab[1];
							echo 'Il y a '.$tab[1].' matières.<br />'."\n";
						}else{
							// On permet la concordance
							echo 'Numéro : '.$tab[0].' matière :<b>'.$tab[1].'</b>';
							echo '<input type="hidden" name="numero_texte_'.$numero.'" value="'.$tab[0].'" />';
							$nom_select = "nom_gepi_".$numero; // pour le name du select
							$nom_matiere = $tab[1]; // pour le selected
							include("helpers/select_matieres.php");
							echo '<br />'."\n";
						}
					}elseif($etape == 6){
						// On traite des "ETABLISSEMENT"
						if ($tab[0] == "ETABLISSEMENT") {
							$nbre_lignes = 0;
						}else{
							// Difficile en l'état de faire mieux que ne rien faire.
							echo '
							<p>A priori, l\'établissement '.$tab[1].' est le bon.</p>
							<p>Cliquez sur le bouton ci-dessous pour passer à l\'étape suivante.</p>';
							break;
						}
					}elseif($etape == 7){
						//  On traite des "SEMAINE"
						if($tab[0] == "SEMAINE"){
							$nbre_lignes = 53;
							echo 'Il y a 53 semaines.<br />'."\n";
						}else{
							// on va aller remplir la table edt_semaines $tab[1] est le numéro de la semaine et $tab[2] son type (A/B, 1/2,...)
							// le fichier txt commence par le rne établissement puis le n° de la semaine et sa valeur
							echo '<input type="hidden" name="semaine_'.$tab[1].'" value="'.$tab[2].'" />'."\n";
							$nbre_lignes = 53;
							// voir plus bas le champ checkbox sur le choix de vider ou non la table edt_semaines
						}
					}elseif($etape == 8){
						// On traite des "CONGES"
						if($tab[0] == "CONGES"){
							$nbre_lignes = $tab[1];
							echo 'Il y a '.$tab[1].' congés.<br />'."\n";
						}else{
							// on va aller remplir la table edt_calendrier
							$nbre_lignes = 0;
						}
					}elseif($etape == 9){
						// On traite des "COURS"
						// C'est la partie la plus importante
						if($tab[0] == "COURS"){
							$nbre_lignes = $tab[1];
							echo 'Il y a '.$tab[1].' cours.<br />'."\n";
						}else{
							// On cherche dans la table edt_init les concordances et on crée les cours en question
							// ESSAI : on propose des champs hidden avec toutes les infos et c'est edt_init_concordance.php qui fait le travail
								$cours = '';
							for($a = 0; $a < 11; $a++){
								if (isset($tab[$a])) {
									$cours .= $tab[$a].'|';
								}else{
									$cours .= 'rien|';
								}
							}
							echo '<input type="hidden" name="cours_'.$numero.'" value="'.$cours.'" />'."\n";
						}
					}
					$numero++;
				}
			}
			// Si c'est l'étape 7 (le type des semaines) on propose de vider la table edt_semaines ou pas.
			if ($etape == 7) {
				echo '
				<label for="etapeSemaines">Si vous n\'avez pas encore initialisé le type des semaines de l\'année, cochez : </label>
				<input type="checkbox" id="etapeSemaines" name="effacer_semaines" value="ok" />
				';

			}
			// on ferme le formulaire
			echo '
				<input type="hidden" name="etape" value="'.$etape.'" />
				<input type="hidden" name="nbre_ligne" value="'.$nbre_lignes.'" />
				<input type="submit" name="Enregistrer" value="Enregistrer ces concordances" />
			</form>';
			echo "\n<hr /><br />\n";
		}
	}else{
		// Si on est là c'est que le nom du fichier n'est pas bon.
		echo '<p>Ce n\'est pas le bon nom de fichier, vous devriez regarder et modifier le cas échéant.</p>';
		echo "<p style=\"text-align: center;\"><a href=\"./edt_init_texte.php\">Veuillez recommencer</a></p>\n";
	}
} // fin du if ($action == "upload_file")...

// ======================= fin du traitement du fichier ==============
?>

<h4 class="gepi">Initialisation des l'emploi du temps de Gepi en utilisant les exports texte du type "Charlemagne".</h4>

<p>Certains logiciels propri&eacute;taires de traitement des emplois du temps proposent des exportations en format texte.
Celles-ci doivent avoir 9 parties pour pouvoir les utiliser ici :</p>
<ul>
	<li>PROFESSEUR</li>
	<li>CLASSE</li>
	<li>GROUPE</li>
	<li>PARTIE</li>
	<li>MATIERE</li>
	<li>ETABLISSEMENT</li>
	<li>SEMAINE</li>
	<li>CONGES</li>
	<li>COURS</li>
</ul>

<p>Pour chaque partie, vous allez devoir faire le lien avec les informations de Gepi. Vous devrez donc faire passer le fichier texte 9 fois et
la derni&egrave;re sera la plus longue. Par contre, les 8 premi&egrave;re &eacute;tapes seront conserv&eacute;es par Gepi et vous pourrez effectuer la derni&egrave;re
 &eacute;tape (importation des cours eux-m&ecirc;mes autant de fois que vous le d&eacute;sirez (en effa&ccedil;ant les anciens cours ou non).</p>

	<p>Veuillez préciser le nom complet du fichier <b>emploidutemps.txt</b>.</p>
		<form enctype="multipart/form-data" action="edt_init_texte.php" method="post">
			<input type="hidden" name="action" value="upload_file" />
			<p>
				<label for="truncateEdt">Recommencer en effa&ccedil;ant tous les param&egrave;tres d&eacute;j&agrave; cr&eacute;&eacute;s.</label>
				<input type="checkbox" id="truncateEdt" name="truncate_edt" value="oui" />
			</p>
			<p><input type="file" size="80" name="txt_file" /></p>
			<p><input type="submit" value="Valider" /></p>
		</form>

<?php echo '</div>' ?>
<?php
require_once("../lib/footer.inc.php");
?>