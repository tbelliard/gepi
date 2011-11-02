<?php
/*
 * @version $Id: modele_page_garde.php 1725 2008-04-19 19:21:49Z crob $
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
// Initialisations files
require_once("../lib/initialisations.inc.php");

//**************** EN-TETE *****************
require_once("../lib/header.inc");
//**************** FIN EN-TETE *************

$info_eleve_page_garde="Elève: Durand Camille, 3 A2";

$ligne1 = "Durand Albert";
$ligne2 = "1, Avenue de l'Europe";
$ligne3 = "75000 Paris";
include "./page_garde.php";
require("../lib/footer.inc.php");
?>