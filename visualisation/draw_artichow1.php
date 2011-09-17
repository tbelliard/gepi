<?php
/*
 $Id$
*/

// On précise de ne pas traiter les données avec la fonction anti_inject
$traite_anti_inject = 'no';
// Initialisations files
require_once("../lib/initialisations.inc.php");
require_once "../artichow/BarPlot.class.php";

$nb_data = $_GET['nb_data'];

// Définition des couleurs
$colors = array(
    new Color(173, 216, 230, 12),
    new Color(255, 165, 0, 12),
    new Color(165, 42, 42, 12)
);

$k = 3;
while ($k < $nb_data) {
    $colors[$k] = new Color(180, 100, 154, 12);
    $k++;
}

$etiquettex = array();
$k="1";
while ($k < $nb_data) {
 $datay[$k] = array();
 $temp[$k] =  array();
 $temp[$k]=explode("|", $_GET['temp'.$k]);
 $k++;
}

$legendy = array();
$temp3=explode("|", $_GET['etiquette']);
$titre = unslashes($_GET['titre']);
$k = 1;
while ($k < $nb_data) {
    if (isset($_GET['v_legend'.$k])) {
        $legendy[$k] = unslashes(urldecode($_GET['v_legend'.$k]));
    } else {
        $legendy[$k]='' ;
    }
    $k++;
}

$i=0;
while ($i < count($temp[1])) {
    $k="1";
    while ($k < $nb_data) {
        $datay[$k][$i] = $temp[$k][$i];
        $k++;
    }
/*
    // On ne transmet plus l'id_matiere dans 'etiquette', mais le nom_complet.
    $call_matiere = mysql_query("SELECT nom_complet FROM matieres WHERE matiere = '".$temp3[$i]."'");
    $etiquettex[$i] = mysql_result($call_matiere, "0", "nom_complet");
*/
    $etiquettex[$i] = $temp3[$i];
    $i++;
}

        $graph = new Graph(600, 400);
        $graph->setAntiAliasing(TRUE);
        $blue = new Color(0, 0, 200);
        $red = new Color(200, 0, 0);
        $group = new PlotGroup();
        $group->setPadding(40, 40,60,100);
        $group->setBackgroundColor(
            new Color(240, 240, 240)
        );

        $k = 1;
        while ($k < $nb_data) {
            $plot = new BarPlot($datay[$k], $k, $nb_data-1);

            $plot->barBorder->setColor(new Color(0, 0, 0, 30));

            $plot->setBarPadding(0.1, 0.1);
            $plot->setBarSpace(0);

            $plot->barShadow->setSize(4);
            $plot->barShadow->setPosition(SHADOW_RIGHT_TOP);
            $plot->barShadow->setColor(new Color(180, 180, 180, 10));
            $plot->barShadow->smooth(TRUE);

            $plot->label->move(0, -6);
            $plot->label->setFont(new Tuffy(7));
            $plot->label->setAngle(90);
            $plot->label->setAlign(NULL, LABEL_TOP);
            $plot->label->setPadding(3, 1, 0, 6);

            $plot->setBarColor($colors[$k], 50);
            $plot->setBarSize(0.60);

            $plot->setYAxis(PLOT_LEFT);
            $plot->setYMax("20");

            $group->add($plot);
            $group->legend->add($plot, $legendy[$k], LEGEND_BACKGROUND);
            $k++;
        }

        $group->axis->left->setColor($blue);
        $group->axis->left->setNumberByTick('minor','major', 1);
        $group->axis->left->title->set("Note");
        $group->axis->left->setLabelNumber("11");

        $group->axis->bottom->setNumberByTick('minor','major', 0);
        //$group->axis->bottom->setLabelNumber(count($etiquettex));
        $group->axis->bottom->setLabelText($etiquettex);
        $group->axis->bottom->label->setFont(new TuffyBold(9));
        $group->axis->bottom->label->setAngle("30");
        $group->axis->bottom->label->move(10, 0);
        $group->axis->bottom->label->setAlign(LABEL_RIGHT, LABEL_BOTTOM);
        $group->axis->bottom->label->setPadding(0, 0, 0, 0);
        $group->axis->bottom->reverseTickStyle();


        $group->legend->shadow->setSize(0);
        $group->legend->setAlign(LEGEND_CENTER);
        $group->legend->setSpace(6);
        $group->legend->setTextFont(new Tuffy(8));
        $group->legend->setPosition(0.85, 0.12);
        $group->legend->setBackgroundColor($colors[0]);
        $group->legend->setColumns(1);

        $group->title->set($titre);
        $group->title->move(-70, -20);

        $graph->border->hide();
        $group->grid->hideVertical();
        $group->grid->setInterval("1","1");


       $group->add($plot);
       $graph->add($group);
       $graph->draw();

?>