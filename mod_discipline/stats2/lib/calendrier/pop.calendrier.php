<?php
/*
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Didier Blanqui
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

             
/**
 * Intérieur du fichier php contenant le seul calendrier. On récupère en GET les valeurs
 * représentant le nom du formulaire et le nom du champ de la date.
 */
$frm = $_GET['frm'];
$chm = $_GET['ch'];

include("calendrier.class.php");

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