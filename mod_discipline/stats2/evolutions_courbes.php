<?php
$niveau_arbo = 2;
require_once("../../lib/initialisations.inc.php");
require_once "../../artichow/LinePlot.class.php";
$titre = stripslashes($_GET['titre']);
$data=$_SESSION['evolution'][$titre];
$graph = new Graph(800, 500);
$graph->setAntiAliasing(TRUE);


// On définit les mois
$months = array(
        'Septembre',
        'Octobre',
        'Novembre',
        'Décembre',
        'Janvier',
        'Février',
        'Mars',
        'Avril',
        'Mai',
        'Juin'
);

$group = new PlotGroup;
$group->setPadding(40, 40);
$group->setBackgroundColor(
        new Color(240, 240, 240)
);

$nbre_courbes=count($data);

$i=0;
$values=Array();
foreach($data as $key=>$type) {
  foreach($type as $value) {
    $values[$i][]=$value;
  }
  $plot = new LinePlot($values[$i]);
  $color=new color(rand(0,200),rand(0,200),rand(0,200));
  $plot->setColor($color);
  $plot->setThickness(2);

  $group->legend->add($plot, $key, Legend::LINE);
  $group->legend->setPosition(1, 0.25);
  $group->add($plot);
  $i++;
}

$group->axis->bottom->setLabelText($months);

$graph->add($group);
$graph->draw();
?>