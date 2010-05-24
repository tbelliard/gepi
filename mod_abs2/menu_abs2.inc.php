<?php
/*
$Id$
 */

echo "<ul class='css-tabs' id='menutabs'>\n";

$onglet_abs = reset(explode("?", basename($_SERVER["REQUEST_URI"])));
$_SESSION['abs2_onglet'] = $onglet_abs;
echo "<li><a href='saisie_absences.php' ";
if($onglet_abs=='saisie_absences.php') {echo "class='current' ";}
echo "title='Saisir des absences et des retards'>Saisir un groupe</a></li>\n";

// Tests à remplacer par des tests sur les droits attribués aux statuts
if(($_SESSION['statut']=='cpe')||
    ($_SESSION['statut']=='scolarite')) {
    echo "<li><a href='suivi_absences.php' ";
    if($onglet_abs=='suivi') {echo "class='current' ";}
    echo "title='Traitement et suivi des absences et des retards'>Suivi</a></li>\n";

    echo "<li><a href='#' ";
    if($onglet_abs=='bilans') {echo "class='current' ";}
    echo "title='Bilans'>Bilans</a></li>\n";

    echo "<li><a href='#' ";
    if($onglet_abs=='stat') {echo "class='current' ";}
    echo "title='Statistiques'>Statistiques</a></li>\n";

    echo "<li><a href='#' ";
    if($onglet_abs=='courrier') {echo "class='current' ";}
    echo "title='Gestion du courrier'>Courrier</a></li>\n";

    echo "<li><a href='#' ";
    if($onglet_abs=='parametrage') {echo "class='current' ";}
    echo "title='Paramètres : types, actions, motifs, justifications, créneaux'>Paramètres</a></li>\n";
}

if($_SESSION['statut']=='cpe') {
    echo "<li><a href='liste_saisies_selection_traitement.php' ";
    if($onglet_abs=='liste_saisies_selection_traitement.php') {echo "class='current' ";}
    echo "title='Liste des saisies'>Liste des saisies</a></li>\n";
} else {
    echo "<li><a href='liste_saisies.php' ";
    if($onglet_abs=='liste_saisies.php') {echo "class='current' ";}
    echo "title='Liste des saisies'>Liste des saisies</a></li>\n";
}
echo "<li><a href='visu_saisie.php' ";
if($onglet_abs=='visu_saisie.php') {echo "class='current' ";}
echo "title='Visualiser une saisie'>Visualiser une saisie</a></li>\n";

echo "</ul>\n";

?>
