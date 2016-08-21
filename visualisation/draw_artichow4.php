<?php
/*
 $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard, Stephane Boireau, Julien Jocal
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
require_once("../secure/connect.inc.php");
require_once("../lib/global.inc.php");
require_once("../lib/share.inc.php");
require_once("../lib/mysql.inc");
require_once "../artichow/BarPlot.class.php";

$place_eleve= $_GET['place_eleve'];
$colors = array();
$all_values=array();
$nb_data = $_GET['nb_data'];

$i=1;
$etiquettex=array(
' A B C+C-D E'
);

while ($i < $nb_data) {
    $values[$i]=array();
    $values[$i][0]=$_GET['temp'.$i];
    array_push($all_values,$values[$i][0]);
    $colors[$i] = new Color(180, 180, 180, 10);
    $i++;
}
$max=max($all_values);
if ($place_eleve != "") $colors[$place_eleve]= new Color(0, 0, 0, 0);
$graph = new Graph(150, 150);
//$graph->SetSize();
$graph->setAntiAliasing(TRUE);
$group = new PlotGroup();
$k = 1;
while ($k < $nb_data) {
    $plot = new BarPlot($values[$k], $k, $nb_data-1);
    $plot->setBarColor($colors[$k]);
    $plot->setBarSize(1);
    $plot->setYMax($max);
    $group->add($plot);
    $k++;
}

$group->axis->left->hide(TRUE);
$group->axis->bottom->hide(TRUE);
//$group->axis->bottom->setNumberByTick('minor','major', 0);
//$group->axis->bottom->setLabelNumber(count($etiquettex));
//$group->axis->bottom->setLabelText($etiquettex);
//$group->axis->bottom->setAlign(RIGHT);
$graph->add($group);
$graph->draw();
?>