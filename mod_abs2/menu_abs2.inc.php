<?php
/*
$Id$
 */

echo "<div id='aidmenu' style='display: none;'>$aff_aide</div>\n";

echo "<ul class='css-tabs' id='menutabs'>\n";

echo "<li><a href='index2.php' ";
if($onglet_abs=='index2') {echo "class='current' ";}
echo "title='Accueil du module'>Index</a></li>\n";

echo "<li><a href='saisie_abs2b.php' ";
if($onglet_abs=='saisie') {echo "class='current' ";}
echo "title='Saisie des absences et des retards'>Saisie</a></li>\n";

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
echo "<li><a href='fiche_eleve.php' ";
if($onglet_abs=='fiche_eleve') {echo "class='current' ";}
echo "title='Informations sur les élèves'>Fiches élève</a></li>\n";

if (getSettingValue("active_mod_discipline") == "y") {
    echo "<li><a href='../mod_discipline/index.php' ";
    if($onglet_abs=='discipline') {echo "class='current' ";}
    echo "title='Module discipline'>Discipline</a></li>";
}

echo "</ul>\n";

?>
