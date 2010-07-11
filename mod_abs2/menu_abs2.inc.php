<?php
/*
$Id$
 */

echo "<ul class='css-tabs' id='menutabs'>\n";

$onglet_abs = reset(explode("?", basename($_SERVER["REQUEST_URI"])));
$_SESSION['abs2_onglet'] = $onglet_abs;
// Tests à remplacer par des tests sur les droits attribués aux statuts
if(($_SESSION['statut']=='cpe')||
    ($_SESSION['statut']=='scolarite')) {

    echo "<li><a href='absences_du_jour.php' ";
    if($onglet_abs=='absences_du_jour.php') {echo "class='current' ";}
    echo "title='Absences du jour'>Absences du jour</a></li>\n";

    echo "<li><a href='saisir_groupe.php' ";
    if($onglet_abs=='saisir_groupe.php') {echo "class='current' ";}
    echo "title='Saisir des absences et des retards pour un groupe'>Saisir un groupe</a></li>\n";

    echo "<li><a href='saisir_eleve.php' ";
    if($onglet_abs=='saisir_eleve.php') {echo "class='current' ";}
    echo "title='Saisir pour un eleve'>Saisir un eleve</a></li>\n";

    echo "<li><a href='liste_saisies_selection_traitement.php' ";
    if($onglet_abs=='liste_saisies_selection_traitement.php') {echo "class='current' ";}
    echo "title='Liste des saisies'>Liste des saisies</a></li>\n";

    echo "<li><a href='visu_saisie.php' ";
    if($onglet_abs=='visu_saisie.php' || $onglet_abs=='enregistrement_modif_saisie.php') {echo "class='current' ";}
    echo "title='Visualiser une saisie'>Saisie</a></li>\n";

    echo "<li><a href='liste_traitements.php' ";
    if($onglet_abs=='liste_traitements.php') {echo "class='current' ";}
    echo "title='Traitement'>Liste des traitements</a></li>\n";

    echo "<li><a href='visu_traitement.php' ";
    if($onglet_abs=='visu_traitement.php' || $onglet_abs=='enregistrement_modif_traitement.php') {echo "class='current' ";}
    echo "title='Traitement'>Traitement</a></li>\n";

    echo "<li><a href='liste_notifications.php' ";
    if($onglet_abs=='liste_notifications.php') {echo "class='current' ";}
    echo "title='Notifications'>Liste des notifications</a></li>\n";

    echo "<li><a href='visu_notification.php' ";
    if($onglet_abs=='visu_notification.php' || $onglet_abs=='enregistrement_modif_notification.php' || $onglet_abs=='generer_notification.php') {echo "class='current' ";}
    echo "title='Notification'>Notification</a></li>\n";

    if ($_SESSION['statut']=='scolarite') {
	echo "<li><a href='stats.php' ";
	if($onglet_abs=='stats.php') {echo "class='current' ";}
	echo "title='Stats'>Stats</a></li>\n";
    }
} else if ($_SESSION['statut']=='professeur') {
    echo "<li><a href='saisir_groupe.php' ";
    if($onglet_abs=='saisir_groupe.php') {echo "class='current' ";}
    echo "title='Saisir des absences et des retards pour un groupe'>Saisir un groupe</a></li>\n";

    echo "<li><a href='visu_saisie.php' ";
    if($onglet_abs=='visu_saisie.php' || $onglet_abs=='enregistrement_modif_saisie.php') {echo "class='current' ";}
    echo "title='Visualiser une saisie'>Saisie</a></li>\n";

    echo "<li><a href='liste_saisies.php' ";
    if($onglet_abs=='liste_saisies.php') {echo "class='current' ";}
    echo "title='Liste des saisies'>Liste des saisies</a></li>\n";
}else if ($_SESSION['statut']=='autre') {
    echo "<li><a href='saisir_eleve.php' ";
    if($onglet_abs=='saisir_eleve.php') {echo "class='current' ";}
    echo "title='Saisir pour un eleve'>Saisir un eleve</a></li>\n";

    echo "<li><a href='visu_saisie.php' ";
    if($onglet_abs=='visu_saisie.php' || $onglet_abs=='enregistrement_modif_saisie.php') {echo "class='current' ";}
    echo "title='Visualiser une saisie'>Saisie</a></li>\n";

    echo "<li><a href='liste_saisies.php' ";
    if($onglet_abs=='liste_saisies.php') {echo "class='current' ";}
    echo "title='Liste des saisies'>Liste des saisies</a></li>\n";
}
echo "</ul>\n";

?>
