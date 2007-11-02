<?php
/* fichier pour visionner l'EdT d'un élève */

$titre_page = "Emploi du temps";
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

unset ($_SESSION['order_by']);

// CSS particulier à l'EdT
$style_specifique = "edt_organisation/style_edt";

// End standart header
require_once("../lib/header.inc");


	echo '<br />';
	echo '<p class="bold"><a href="../accueil.php"><img src=\'../images/icons/back.png\' alt=\'Retour\' class=\'back_link\'/> Retour accueil</a>';
	echo '<center>';

if (isset($_SESSION["login"])) {

	$aff_nom_edt = renvoie_nom_long(($_SESSION["login"]), "eleve");
	echo 'L\'emploi du temps de '.$aff_nom_edt;
}
	echo '<br /><br />';


		premiere_ligne_tab_edt();


	$tab_creneaux = retourne_creneaux();
		$i=0;
	while($i<count($tab_creneaux)){

	$tab_id_creneaux = retourne_id_creneaux();
		$c=0;
		while($c<count($tab_id_creneaux)){

		echo("<tr><th rowspan=\"2\"><br />".$tab_creneaux[$i]."<br /><br /></th>".(construction_tab_edt($tab_id_creneaux[$c], "0"))."\n");
		echo("<tr>".(construction_tab_edt($tab_id_creneaux[$c], "0.5"))."\n");
		$i ++;
		$c ++;
		}
	}

	echo '</tbody></table>';
	echo '</center>';
?>