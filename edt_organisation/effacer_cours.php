<?php
// PAS BON DU TOUT A REVOIR
/**
 * Fichier destiné à effacer une entrée dans l'emploi du temps
 * Il reçoit par GET un id de cours à supprimer
 * @version $Id$
 * @copyright 2007
 */

$titre_page = "Emploi du temps - Effacer un cours";
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

// INSERT INTO `gepi`.`droits` (`id` ,`administrateur` ,`professeur` ,`cpe` ,`scolarite` ,`eleve` ,`responsable` ,`secours` ,`description` ,`statut`) VALUES ('/edt_organisation/effacer_cours.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Effacer un cours des EdT', '');
// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// CSS particulier à l'EdT
$style_specifique = "edt_organisation/style_edt";

// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php"); ?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php
// Initialisation des variables
$supprimer_cours = isset($_GET["supprimer_cours"]) ? $_GET["supprimer_cours"] : NULL;
$type_edt = isset($_GET["type_edt"]) ? $_GET["type_edt"] : NULL;
$identite = isset($_GET["identite"]) ? $_GET["identite"] : NULL;

$effacer_cours = mysql_query("DELETE FROM edt_cours WHERE id_cours = '".$supprimer_cours."'") OR die ('Impossible d\'effacer ce cours');

if (!$effacer_cours) {
	echo '<span class="refus">Revenez en arrière avec la flèche de votre navigateur et recommencez.</span>';
}
else {
	/*echo '
		<span class="accept">Ce cours est effacé !</span>
		<form name="retour" method="post" action="index_edt.php">
		<input type="hidden" name="visioedt" value="'.$type_edt.'1" />
		<input type="hidden" name="login_edt" value="'.$identite.'" />
		<input type="hidden" name="type_edt_2" value="'.$type_edt.'" />
		<input type="image" src="../images/icons/back.png" border="0" name="submit" alt="Revenir" title="Revenir" /> Revenir
		</form>
		';*/

}
?>

	</div>
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>