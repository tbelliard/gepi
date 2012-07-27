<?php

/**
 *
 * @copyright 2008-2011
 *
 * Fichier qui renvoie un select des classes de l'établissement
 * pour l'intégrer dans un fomulaire
 */
// On récupère les infos utiles pour le fonctionnement des requêtes sql
$niveau_arbo = 1;
require_once("../lib/initialisations.inc.php");
// Sécurité : éviter que quelqu'un appelle ce fichier seul
$serveur_script = $_SERVER["SCRIPT_NAME"];
$analyse = explode("/", $serveur_script);
$analyse[4] = isset($analyse[4]) ? $analyse[4] : null;
if ($analyse[4] == "select_matieres.php") {
    die();
}

$increment = isset($nom_select) ? $nom_select : "liste_matieres";
$matiere_selected = isset($nom_matiere) ? my_strtoupper($nom_matiere) : (isset($nom_selected) ? my_strtoupper($nom_selected) : null);
$options = NULL;

// on recherche la liste des matières
$query = mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY nom_complet");
$nbre = mysql_num_rows($query);

$warning = ' style="background-color: orange;"'; // si il ne trouve pas de correspondance, on modifie le fond

for($i = 0; $i < $nbre; $i++) {
    $matiere[$i] = mysql_result($query, $i, "matiere");
    $nom[$i] = mysql_result($query, $i, "nom_complet");
    if (my_strtoupper(trim(remplace_accents($nom[$i], 'all_nospace'))) == $matiere_selected OR my_strtoupper(trim(remplace_accents($matiere[$i], 'all_nospace'))) == $matiere_selected) {
        $selected = ' selected="selected"';
        $warning = ''; // il a trouvé une correspondance, donc on enlève le fond.
    } else {
        $selected = '';
    }

    $options .= '
		<option value="' . $matiere[$i] . '"' . $selected . '>' . $nom[$i] . '</option>';
}
echo '
	<select name ="' . $increment . '"'.$warning .' onmouseover="if(document.getElementById(\'texte_nomGepi'.$l.'\')) {document.getElementById(\'texte_nomGepi'.$l.'\').style.backgroundColor=\'yellow\'}" onmouseout="if(document.getElementById(\'texte_nomGepi'.$l.'\')) {document.getElementById(\'texte_nomGepi'.$l.'\').style.backgroundColor=\'\'}">
		<option value="aucun">Liste des matières</option>';

echo $options;

echo '</select>';

?>
