<?php

/**
 * /edt_organisation/ajax_edtcouleurs.php
 * Fichier qui gère une requête ajax et qui renvoie la bonne couleur pour la matière
 *
 * @version $Id$
 * @copyright 2007
 */
// Initialisations
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
// Initialisation des variables
$M_couleur = isset($_GET["var1"]) ? $_GET["var1"] : NULL;
$nouvelle_couleur = isset($_GET["var2"]) ? $_GET["var2"] : "non";
$matiere = isset($M_couleur) ? substr($M_couleur, 2) : NULL; // pour récupérer le nom court de la matière
$couleur = "";

// on récupère les éléments sur la matière en question
$sql = mysql_query("SELECT nom_complet FROM matieres WHERE matiere = '".$matiere."'");
$matiere_long = mysql_fetch_array($sql);
// les requêtes AJAX se font en utf8, il faut donc encoder utf8 pour être tranquille
$aff_matiere_long = utf8_encode($matiere_long["nom_complet"]);
// On récupère la couleur de la matière en question
$verif_couleur = GetSettingEdt($M_couleur);
	if ($verif_couleur == "") {
		$couleur = "";
	} else {
		$couleur = $verif_couleur;
	}

if ($nouvelle_couleur == "non") {
	echo '
<td>'.$aff_matiere_long.'</td>
<td>'.$matiere.'</td>
<td style="background-color: '.$couleur.';">
	<form id="choixCouleur" method="get" action="">
		<select id="selectColeur" name="couleur">
			<option value="rien">Couleurs...</option>
			<option value="blue" onClick="couleursEdtAjax(\''.$M_couleur.'\', \'blue\');">Bleu</option>
			<option value="fuchsia" onClick="couleursEdtAjax(\''.$M_couleur.'\', \'fuchsia\');">Fushia</option>
			<option value="lime" onClick="couleursEdtAjax(\''.$M_couleur.'\', \'lime\');">Vert citron</option>
			<option value="maroon" onClick="couleursEdtAjax(\''.$M_couleur.'\', \'maroon\');">Brun</option>
			<option value="purple" onClick="couleursEdtAjax(\''.$M_couleur.'\', \'purple\');">Mauve</option>
			<option value="red" onClick="couleursEdtAjax(\''.$M_couleur.'\', \'red\');">Rouge</option>
			<option value="white" onClick="couleursEdtAjax(\''.$M_couleur.'\', \'white\');">Blanc</option>
			<option value="yellow" onClick="couleursEdtAjax(\''.$M_couleur.'\', \'yellow\');">Jaune</option>
			<option value="aqua" onClick="couleursEdtAjax(\''.$M_couleur.'\', \'aqua\');">Bleu clair</option>
			<option value="grey" onClick="couleursEdtAjax(\''.$M_couleur.'\', \'grey\');">Gris</option>
			<option value="green" onClick="couleursEdtAjax(\''.$M_couleur.'\', \'green\');">Vert</option>
			<option value="olive" onClick="couleursEdtAjax(\''.$M_couleur.'\', \'olive\');">Vert olive</option>
			<option value="teal" onClick="couleursEdtAjax(\''.$M_couleur.'\', \'teal\');">Turquoise</option>
		</select>
	</form>
</td>
	';
} else {
// On vérifie si le réglage existe et on le met à jour, sinon on le crée
$sql = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = '".$M_couleur."'");
$nbre_rep = mysql_num_rows($sql);
	if ($nbre_rep !== 0) {
		$miseajour = mysql_query("UPDATE edt_setting SET valeur = '".$nouvelle_couleur."' WHERE reglage = '".$M_couleur."'") OR DIE ('Impossible de mettre à jour la table edt_setting');
	} else {
		$create = mysql_query("INSERT INTO edt_setting (`id`, `reglage`, `valeur`) VALUES ('', '$M_couleur', '$nouvelle_couleur')");
	}
	echo '
<td>'.$aff_matiere_long.'</td>
<td>'.$matiere.'</td>
<td style="background-color: '.$nouvelle_couleur.';">
	<p onclick="couleursEdtAjax(\''.$M_couleur.'\', \'non\');">Modifier</p>
</td>
	';
}

?>