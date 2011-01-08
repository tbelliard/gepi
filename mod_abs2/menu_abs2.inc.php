<?php
/**
 *
 * @version $Id$
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

//echo "<ul class='css-tabs' id='menutabs'>\n";

// $onglet_abs = reset(explode("?", basename($_SERVER["REQUEST_URI"])));
$basename_serveur=explode("?", basename($_SERVER["REQUEST_URI"]));
$onglet_abs = reset($basename_serveur);

$_SESSION['abs2_onglet'] = $onglet_abs;
// Tests à remplacer par des tests sur les droits attribués aux statuts
if(($_SESSION['statut']=='cpe')||
    ($_SESSION['statut']=='scolarite')) {
  
    echo "<ul class='css-tabs' id='menutabs' style='font-size:85%'>\n";

    echo "<li><a href='tableau_des_appels.php' ";
    if($onglet_abs=='absences_du_jour.php'
	    || $onglet_abs=='tableau_des_appels.php'
	    || $onglet_abs=='bilan_du_jour.php'
	    || $onglet_abs=='extraction_saisies.php'
        || $onglet_abs=='bilan_individuel.php') {echo "class='current' ";}
    echo "title='Bilans'>Bilans</a></li>\n";

    echo "<li><a href='saisir_groupe.php' ";
    if($onglet_abs=='saisir_groupe.php' || $onglet_abs=='enregistrement_saisie_groupe.php') {echo "class='current' ";}
    echo "title='Saisir des absences et des retards pour un groupe'>Saisir un groupe</a></li>\n";

    echo "<li><a href='saisir_eleve.php' ";
    if($onglet_abs=='saisir_eleve.php' || $onglet_abs=='enregistrement_saisie_eleve.php') {echo "class='current' ";}
    echo "title='Saisir pour un eleve'>Saisir un élève</a></li>\n";

    echo "<li><a href='liste_saisies_selection_traitement.php' ";
    if($onglet_abs=='liste_saisies_selection_traitement.php') {echo "class='current' style='background-color:#cae7cb; border-bottom:2px solid #cae7cb;' ";}
    else {echo "style='background-color:#e6f8e7;' ";}
    echo "title='Liste des saisies'>Liste des saisies</a></li>\n";

    echo "<li><a href='visu_saisie.php' ";
    if($onglet_abs=='visu_saisie.php' || $onglet_abs=='enregistrement_modif_saisie.php') {echo "class='current' style='background-color:#cae7cb; border-bottom:2px solid #cae7cb;' ";}
    else {echo "style='background-color:#e6f8e7;' ";}
    echo "title='Visualiser une saisie'>Saisie</a></li>\n";

    echo "<li><a href='liste_traitements.php' ";
    if($onglet_abs=='liste_traitements.php') {echo "class='current' style='background-color:#ebedb5; border-bottom:2px solid #ebedb5;' ";}
    else {echo "style='background-color:#f9f9de;' ";}
    echo "title='Traitement'>Liste des traitements</a></li>\n";

    echo "<li><a href='visu_traitement.php' ";
    if($onglet_abs=='visu_traitement.php' || $onglet_abs=='enregistrement_modif_traitement.php') {echo "class='current' style='background-color:#ebedb5; border-bottom:2px solid #ebedb5;' ";}
    else {echo "style='background-color:#f9f9de;' ";}
    echo "title='Traitement'>Traitement</a></li>\n";

    echo "<li><a href='liste_notifications.php' ";
    if($onglet_abs=='liste_notifications.php') {echo "class='current' style='background-color:#c7e3ec; border-bottom:2px solid #c7e3ec;' ";}
    else {echo "style='background-color:#ecf6f8;' ";}
    echo "title='Notifications'>Liste des notifications</a></li>\n";

    echo "<li><a href='visu_notification.php' ";
    if($onglet_abs=='visu_notification.php' || $onglet_abs=='enregistrement_modif_notification.php' || $onglet_abs=='generer_notification.php') {echo "class='current' style='background-color:#c7e3ec; border-bottom:2px solid #c7e3ec;' ";}
    else {echo "style='background-color:#ecf6f8;' ";}
    echo "title='Notification'>Notification</a></li>\n";

    echo "<li><a href='generer_notifications_par_lot.php' ";
    if($onglet_abs=='generer_notifications_par_lot.php') {echo "class='current' style='background-color:#c7e3ec; border-bottom:2px solid #c7e3ec;' ";}
    else {echo "style='background-color:#ecf6f8;' ";}
    echo "title='Envoi par lot'>Envoi par lot</a></li>\n";

    if($onglet_abs=='saisir_eleve.php' || $onglet_abs=='enregistrement_saisie_eleve.php' || $onglet_abs=='saisir_groupe.php' || $onglet_abs=='enregistrement_saisie_groupe.php') {
	echo '<div style="float :right"><a href="http://www.sylogix.org/projects/gepi/wiki/Saisie_cpe">wiki</a></div>';
    } else if($onglet_abs=='liste_notifications.php') {
	echo '<div style="float :right"><a href="http://www.sylogix.org/projects/gepi/wiki/Suivi">wiki</a></div>';
    } else {
	echo '<div style="float :right"><a href="http://www.sylogix.org/projects/gepi/wiki/Traitement_notification">wiki</a></div>';
    }

    echo "</ul>\n";

} else if ($_SESSION['statut']=='professeur') {

    echo "<ul class='css-tabs' id='menutabs'>\n";

    echo "<li><a href='saisir_groupe.php' ";
    if($onglet_abs=='saisir_groupe.php' || $onglet_abs=='enregistrement_saisie_groupe.php') {echo "class='current' ";}
    echo "title='Saisir des absences et des retards pour un groupe'>Saisir un groupe</a></li>\n";

    echo "<li><a href='visu_saisie.php' ";
    if($onglet_abs=='visu_saisie.php' || $onglet_abs=='enregistrement_modif_saisie.php') {echo "class='current' style='background-color:#cae7cb; border-bottom:2px solid #cae7cb;' ";}
    else {echo "style='background-color:#e6f8e7;' ";}
    echo "title='Visualiser une saisie'>Saisie</a></li>\n";

    echo "<li><a href='liste_saisies.php' ";
    if($onglet_abs=='liste_saisies.php') {echo "class='current' style='background-color:#cae7cb; border-bottom:2px solid #cae7cb;' ";}
    else {echo "style='background-color:#e6f8e7;' ";}
    echo "title='Liste des saisies'>Liste des saisies</a></li>\n";    

    if(!$utilisateur->getClasses()->isEmpty()){
    echo "<li><a href='bilan_individuel.php' ";
    if($onglet_abs=='bilan_individuel.php') {echo "class='current' border-bottom:2px solid #cae7cb;' ";}
    echo "title='Bilans'>Bilan individuel</a></li>\n";
    }

    echo '<div style="float :right"><a href="http://www.sylogix.org/projects/gepi/wiki/Fond_de_salle">wiki</a></div>';

    echo "</ul>\n";

}else if ($_SESSION['statut']=='autre') {

	echo "<ul class='css-tabs' id='menutabs'>\n";

    if(acces('/mod_abs2/saisir_eleve.php','autre')) {
        echo "<li><a href='saisir_eleve.php' ";
        if($onglet_abs=='saisir_eleve.php') {echo "class='current' ";}
        echo "title='Saisir pour un eleve'>Saisir un élève</a></li>\n";        
    
        echo "<li><a href='visu_saisie.php' ";
        if($onglet_abs=='visu_saisie.php' || $onglet_abs=='enregistrement_modif_saisie.php') {echo "class='current' style='background-color:#cae7cb; border-bottom:2px solid #cae7cb;' ";}
        else {echo "style='background-color:#e6f8e7;' ";}
        echo "title='Visualiser une saisie'>Saisie</a></li>\n";

        echo "<li><a href='liste_saisies.php' ";
        if($onglet_abs=='liste_saisies.php') {echo "class='current' style='background-color:#cae7cb; border-bottom:2px solid #cae7cb;' ";}
        else {echo "style='background-color:#e6f8e7;' ";}
        echo "title='Liste des saisies'>Liste des saisies</a></li>\n";
    }
    if(acces('/mod_abs2/bilan_individuel.php','autre')) {
        echo "<li><a href='bilan_individuel.php' ";
        if($onglet_abs=='bilan_individuel.php') {echo "class='current' border-bottom:2px solid #cae7cb;' ";}
        echo "title='Bilan individuel'>Bilan individuel</a></li>\n";
    }
    if(acces('/mod_abs2/totaux_du_jour.php','autre')) {
        echo "<li><a href='totaux_du_jour.php' ";
        if($onglet_abs=='totaux_du_jour.php') {echo "class='current' border-bottom:2px solid #cae7cb;' ";}
        echo "title='Totaux du jour'>Totaux du jour</a></li>\n";
    }

    echo "</ul>\n";
}

?>
