<?php
/**
 *
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

//fichier qui affiche un menu sous forme d'onglets
//à chaque onglets <li> on teste si c'est l'onglet courant pour le mettre en surimpression
//et on mets dans la session l'url pour revenir sur le même onglet à la prochaine visite d'absence 2 (par défault l'url courante
$basename_serveur=explode("?", basename($_SERVER["REQUEST_URI"]));
$url_end = reset($basename_serveur);
$_SESSION['abs2_onglet'] = $url_end;
// Tests à remplacer par des tests sur les droits attribués aux statuts
if(($_SESSION['statut']=='cpe')||
    ($_SESSION['statut']=='scolarite')) {
  
    echo "<ul class='css-tabs' id='menutabs' style='font-size:85%'>\n";

    echo "<li><a href='tableau_des_appels.php' ";
    if($url_end=='absences_du_jour.php'
	    || $url_end=='tableau_des_appels.php'
	    || $url_end=='bilan_du_jour.php'
	    || $url_end=='extraction_saisies.php'
        || $url_end=='bilan_individuel.php'
        || $url_end=='totaux_du_jour.php'
        || $url_end=='statistiques.php'
        || $url_end=='stat_justifications.php'
        || $url_end=='liste_eleves.php') {echo "class='current' ";}
    echo "title='Bilans'>Bilans</a></li>\n";

    echo "<li><a href='saisir_groupe.php' ";
    if($url_end=='saisir_groupe.php' || $url_end=='enregistrement_saisie_groupe.php') {
        echo "class='current' ";
        $_SESSION['abs2_onglet'] = 'saisir_groupe.php';
    }
    echo "title='Saisir des absences et des retards pour un groupe'>Saisir groupe</a></li>\n";

    echo "<li><a href='saisir_eleve.php' ";
    if($url_end=='saisir_eleve.php' || $url_end=='enregistrement_saisie_eleve.php') {
        echo "class='current' ";
        $_SESSION['abs2_onglet'] = 'saisir_eleve.php';
    }
    echo "title='Saisir pour un eleve'>Saisir élève</a></li>\n";

    echo "<li><a href='liste_saisies_selection_traitement.php' ";
    if($url_end=='liste_saisies_selection_traitement.php') {echo "class='current' style='background-color:#cae7cb; border-bottom:2px solid #cae7cb;' ";}
    else {echo "style='background-color:#e6f8e7;' ";}
    echo "title='Liste des saisies'>Liste saisies</a></li>\n";

    echo "<li><a href='visu_saisie.php' ";
    if($url_end=='visu_saisie.php' || $url_end=='enregistrement_modif_saisie.php') {echo "class='current' style='background-color:#cae7cb; border-bottom:2px solid #cae7cb;' ";}
    else {
        echo "style='background-color:#e6f8e7;' ";
        $_SESSION['abs2_onglet'] = 'visu_saisie.php';
    }
    echo "title='Visualiser une saisie'>Saisie</a></li>\n";

    echo "<li><a href='liste_traitements.php' ";
    if($url_end=='liste_traitements.php') {echo "class='current' style='background-color:#ebedb5; border-bottom:2px solid #ebedb5;' ";}
    else {echo "style='background-color:#f9f9de;' ";}
    echo "title='Traitement'>Liste traitements</a></li>\n";

    echo "<li><a href='visu_traitement.php' ";
    if($url_end=='visu_traitement.php' || $url_end=='enregistrement_modif_traitement.php') {
        echo "class='current' style='background-color:#ebedb5; border-bottom:2px solid #ebedb5;' ";
        $_SESSION['abs2_onglet'] = 'visu_traitement.php';
    } else {echo "style='background-color:#f9f9de;' ";}
    echo "title='Traitement'>Traitement</a></li>\n";

    echo "<li><a href='liste_notifications.php' ";
    if($url_end=='liste_notifications.php') {echo "class='current' style='background-color:#c7e3ec; border-bottom:2px solid #c7e3ec;' ";}
    else {echo "style='background-color:#ecf6f8;' ";}
    echo "title='Notifications'>Liste notifications</a></li>\n";

    echo "<li><a href='visu_notification.php' ";
    if($url_end=='visu_notification.php' || $url_end=='enregistrement_modif_notification.php' || $url_end=='generer_notification.php') {
        echo "class='current' style='background-color:#c7e3ec; border-bottom:2px solid #c7e3ec;' ";
        $_SESSION['abs2_onglet'] = 'visu_notification.php';
    } else {echo "style='background-color:#ecf6f8;' ";}
    echo "title='Notification'>Notification</a></li>\n";

    echo "<li><a href='generer_notifications_par_lot.php' ";
    if($url_end=='generer_notifications_par_lot.php') {echo "class='current' style='background-color:#c7e3ec; border-bottom:2px solid #c7e3ec;' ";}
    else {echo "style='background-color:#ecf6f8;' ";}
    echo "title='Envoi par lot'>Envoi par lot</a></li>\n";

    if($url_end=='saisir_eleve.php' || $url_end=='enregistrement_saisie_eleve.php' || $url_end=='saisir_groupe.php' || $url_end=='enregistrement_saisie_groupe.php') {
	echo '<li style="float :right"><a href="http://www.sylogix.org/projects/gepi/wiki/Saisie_cpe">wiki</a></li>';
    } else if($url_end=='liste_notifications.php') {
	echo '<li style="float :right"><a href="http://www.sylogix.org/projects/gepi/wiki/Suivi">wiki</a></li>';
    } else if($url_end=='tableau_des_appels.php'|| $url_end=='absences_du_jour.php'||$url_end=='bilan_du_jour.php'||$url_end=='totaux_du_jour.php'||$url_end=='extraction_saisies.php'||$url_end=='extraction_demi-journees.php'||$url_end=='bilan_individuel.php'||$url_end=='statistiques.php'||$url_end=='stat_justifications.php') {
	echo '<li style="float :right"><a href="http://www.sylogix.org/projects/gepi/wiki/Bilans">wiki</a></li>';
    } else {
    echo '<li style="float :right"><a href="http://www.sylogix.org/projects/gepi/wiki/Traitement_notification">wiki</a></li>';
    }

    echo "</ul>\n";

} else if ($_SESSION['statut']=='professeur') {

    echo "<ul class='css-tabs' id='menutabs'>\n";

    echo "<li><a href='saisir_groupe.php' ";
    if($url_end=='saisir_groupe.php' || $url_end=='enregistrement_saisie_groupe.php') {
        echo "class='current' ";
        $_SESSION['abs2_onglet'] = 'saisir_groupe.php';
    }
    echo "title='Saisir des absences et des retards pour un groupe'>Saisir un groupe</a></li>\n";

    echo "<li><a href='visu_saisie.php' ";
    if($url_end=='visu_saisie.php' || $url_end=='enregistrement_modif_saisie.php') {
        echo "class='current' style='background-color:#cae7cb; border-bottom:2px solid #cae7cb;' ";
        $_SESSION['abs2_onglet'] = 'visu_saisie.php';
    } else {echo "style='background-color:#e6f8e7;' ";}
    echo "title='Visualiser une saisie'>Saisie</a></li>\n";

    echo "<li><a href='liste_saisies.php' ";
    if($url_end=='liste_saisies.php') {echo "class='current' style='background-color:#cae7cb; border-bottom:2px solid #cae7cb;' ";}
    else {echo "style='background-color:#e6f8e7;' ";}
    echo "title='Liste des saisies'>Liste des saisies</a></li>\n";    

    if(!$utilisateur->getClasses()->isEmpty()){
        echo "<li><a href='bilan_individuel.php' ";
        if($url_end=='bilan_individuel.php') {echo "class='current' border-bottom:2px solid #cae7cb;' ";}
        echo "title='Bilans'>Bilan individuel</a></li>\n";
    }

    echo '<li style="float :right"><a href="http://www.sylogix.org/projects/gepi/wiki/Fond_de_salle">wiki</a></li>';

    echo "</ul>\n";

}else if ($_SESSION['statut']=='autre') {

	echo "<ul class='css-tabs' id='menutabs'>\n";

    if(acces('/mod_abs2/saisir_eleve.php','autre')) {
        echo "<li><a href='saisir_eleve.php' ";
        if($url_end=='saisir_eleve.php') {echo "class='current' ";}
        echo "title='Saisir pour un eleve'>Saisir un élève</a></li>\n";        
    
        echo "<li><a href='visu_saisie.php' ";
        if($url_end=='visu_saisie.php' || $url_end=='enregistrement_modif_saisie.php') {
            echo "class='current' style='background-color:#cae7cb; border-bottom:2px solid #cae7cb;' ";
            $_SESSION['abs2_onglet'] = 'visu_saisie.php';
        } else {echo "style='background-color:#e6f8e7;' ";}
        echo "title='Visualiser une saisie'>Saisie</a></li>\n";

        echo "<li><a href='liste_saisies.php' ";
        if($url_end=='liste_saisies.php') {echo "class='current' style='background-color:#cae7cb; border-bottom:2px solid #cae7cb;' ";}
        else {echo "style='background-color:#e6f8e7;' ";}
        echo "title='Liste des saisies'>Liste des saisies</a></li>\n";
    }
    if(acces('/mod_abs2/bilan_individuel.php','autre')) {
        echo "<li><a href='bilan_individuel.php' ";
        if($url_end=='bilan_individuel.php') {echo "class='current' border-bottom:2px solid #cae7cb;' ";}
        echo "title='Bilan individuel'>Bilan individuel</a></li>\n";
    }
    if(acces('/mod_abs2/totaux_du_jour.php','autre')) {
        echo "<li><a href='totaux_du_jour.php' ";
        if($url_end=='totaux_du_jour.php') {echo "class='current' border-bottom:2px solid #cae7cb;' ";}
        echo "title='Totaux du jour'>Totaux du jour</a></li>\n";
    }

    echo "</ul>\n";
}

?>
