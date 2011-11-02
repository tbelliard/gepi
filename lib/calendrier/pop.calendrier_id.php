<?php
/*
 * @version $Id: pop.calendrier_id.php 2281 2008-08-17 09:22:44Z crob $
 */

             
/**
 * Intérieur du fichier php contenant le seul calendrier. On récupère en GET les valeurs
 * représentant le nom du formulaire et le nom du champ de la date.
 */
$frm = $_GET['frm'];
$chm = $_GET['ch'];

include("calendrier_id.class.php");

/**
 * On créé un nouveau calendrier, on récupère la date à afficher (par défaut, le calendrier
 * affiche le mois en cours de l'année en cours). Les valeurs de POST sont transmises au
 * moment où on change le SELECT des mois ou celui des années. Finalement, on affiche le
 * calendrier.
 */
$cal = new Calendrier($frm, $chm);
$cal->auto_set_date($_POST);
$cal->affiche();

?>
