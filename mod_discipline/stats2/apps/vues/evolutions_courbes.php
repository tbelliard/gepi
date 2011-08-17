<?php
// On empêche l'accès direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};

require_once "../../../../artichow/LinePlot.class.php";
if (get_magic_quotes_gpc()) {
  $data = @unserialize(stripslashes($_GET['values']));     
} else {
  $data = @unserialize($_GET['values']);      
}
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