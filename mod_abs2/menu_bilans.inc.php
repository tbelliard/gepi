<?php
/*
$Id$
 */

//echo "<ul class='css-tabs' id='menutabs'>\n";

// $onglet_abs = reset(explode("?", basename($_SERVER["REQUEST_URI"])));
$basename_serveur=explode("?", basename($_SERVER["REQUEST_URI"]));
$onglet_abs = reset($basename_serveur);

$_SESSION['abs2_onglet'] = $onglet_abs;
// Tests à remplacer par des tests sur les droits attribués aux statuts
if(($_SESSION['statut']=='cpe')||
    ($_SESSION['statut']=='scolarite')) {

    echo "<ul class='css-tabs' id='menutabs' style='font-size:85%'>\n";

    echo "<li><a href='tableau_des_saisies.php' ";
    if($onglet_abs=='tableau_des_saisies.php') {echo "class='current' ";}
    echo "title='Tableau des saisies'>Tableau des saisies</a></li>\n";

    echo "<li><a href='absences_du_jour.php' ";
    if($onglet_abs=='absences_du_jour.php') {echo "class='current' ";}
    echo "title='Absences du jours'>Absences du jours</a></li>\n";

    echo "</ul>\n";

}

?>
