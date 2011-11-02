<?php
/*
 * $Id: menu_stats.php 7799 2011-08-17 08:38:10Z dblanqui $
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
// On empêche l'accès direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};
?>
<ol id="essaiMenu">
<li><a href='../index.php'><img src='apps/img/back.png' alt='Retour' class='back_link'/> Retour </a></li>
<li><a href='index.php?ctrl=select'><img src='apps/img/cart_add.png' alt='select' class='back_link'/> Selection </a></li>
<li><a href='index.php?ctrl=bilans&action=affiche_bilans'><img src='apps/img/bilans.png' alt='bilans' class='back_link'/> Bilans </a></li>
<li><a href='index.php?ctrl=bilans&action=evolutions'><img src='apps/img/evolution.png' alt='Graphiques' class='back_link'/> Evolutions des incidents </a></li>
<li><a href='index.php?ctrl=bilans&action=top'><img src='apps/img/top10.png' alt='Top10' class='back_link'/> Top 10 </a></li>
<li><a href='index.php?ctrl=categories'><img src='apps/img/categories.png' alt='bilans' class='back_link'/> Catégories </a></li>
<!--<li><a href='index.php?ctrl=switch'><img src='apps/img/switch.png' alt='Absence/Discipline' class='back_link'/> Absences/Discipline </a></li> -->
</ol>
