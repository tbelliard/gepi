<?php
$eleve_1 = $_GET['eleve_1'];
$classe_1 = $_GET['classe_1'];
$type_1 = $_GET['type_1'];
if($type_1 == "") { $type_1='A'; }

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
require_once "../../artichow/LinePlot.class.php";

function nb_jour_mois($mois_select,$annee_select)
 {
  //Dernier jour du mois
    $mois_d = mktime( 0, 0, 0, $mois_select, 1, $annee_select);
    $nombreDeJours = intval(date("t",$mois_select));
    return($nombreDeJours);
 }

function mois_precedent($date_select)
 {
    if(empty($date_select)) { $date_select = date('Y-m-d'); } { $date_select = $date_select; }

    $date_select_exp = explode('-', $date_select);

    $mois = $date_select_exp[1];
    $annee = $date_select_exp[0];
        if($mois == 12)
            {
            $prochain_mois  = 1 ;
            $prochaine_annee = $annee + 1 ;
            $precedent_mois = $mois - 1 ;
            $precedente_annee = $annee ;
            }
            else if($mois == 1)
                    {
                    $prochain_mois  = $mois + 1  ;
                    $prochaine_annee = $annee ;
                    $precedent_mois = 12 ;
                    $precedente_annee = $annee - 1 ;
                    }
                    else
                    {
                    $prochain_mois  = $mois + 1  ;
                    $prochaine_annee = $annee ;
                    $precedent_mois = $mois - 1 ;
                    $precedente_annee = $annee ;
                    }

          if ($precedent_mois<10) { $precedent_mois = "0".$precedent_mois; }
    $date_moins1 = $precedent_mois;
    return($date_moins1);
 }

function annee_precedent($date_select)
 {
    if(empty($date_select)) { $date_select = date('Y-m-d'); } { $date_select = $date_select; }

    $date_select_exp = explode('-', $date_select);

    $mois = $date_select_exp[1];
    $annee = $date_select_exp[0];
        if($mois == 12)
            {
            $prochain_mois  = 1 ;
            $prochaine_annee = $annee + 1 ;
            $precedent_mois = $mois - 1 ;
            $precedente_annee = $annee ;
            }
            else if($mois == 1)
                    {
                    $prochain_mois  = $mois + 1  ;
                    $prochaine_annee = $annee ;
                    $precedent_mois = 12 ;
                    $precedente_annee = $annee - 1 ;
                    }
                    else
                    {
                    $prochain_mois  = $mois + 1  ;
                    $prochaine_annee = $annee ;
                    $precedent_mois = $mois - 1 ;
                    $precedente_annee = $annee ;
                    }
    $date_moins1 = $precedente_annee;
    return($date_moins1);
 }

$i = 0;
date_default_timezone_set('Europe/Paris');
$date_act = date('Y-m-d');
$date_act_exp = explode('-', $date_act);
$mois = $date_act_exp[1];
$annee_select = $date_act_exp[0];
$nb_mois = "";

while($i<12)
  {
         if($i != 0) { $date_select = $annee_select."-".$mois."-01"; $mois = mois_precedent($date_select); $annee_select = annee_precedent($date_select);  }
         $date_debut = $annee_select."-".$mois."-01";
         if($mois == "01") { $mois_aff[$i] = "janvier ".$annee_select; }
         if($mois == "02") { $mois_aff[$i] = "février ".$annee_select; }
         if($mois == "03") { $mois_aff[$i] = "mars ".$annee_select; }
         if($mois == "04") { $mois_aff[$i] = "avril ".$annee_select; }
         if($mois == "05") { $mois_aff[$i] = "mai ".$annee_select; }
         if($mois == "06") { $mois_aff[$i] = "juin ".$annee_select; }
         if($mois == "07") { $mois_aff[$i] = "juillet ".$annee_select; }
         if($mois == "08") { $mois_aff[$i] = "août ".$annee_select; }
         if($mois == "09") { $mois_aff[$i] = "septembre ".$annee_select; }
         if($mois == "10") { $mois_aff[$i] = "octobre ".$annee_select; }
         if($mois == "11") { $mois_aff[$i] = "novembre ".$annee_select; }
         if($mois == "12") { $mois_aff[$i] = "décembre ".$annee_select; }
         $date_fin = $annee_select."-".$mois."-".nb_jour_mois($mois,$annee_select);

         if($eleve_1 == "tous" and $classe_1 == "tous") { $requete_compt = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE type_absence_eleve = '".$type_1."' AND d_date_absence_eleve >= '".$date_debut."' AND a_date_absence_eleve <= '".$date_fin."'"),0); }
//echo "SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE type_absence_eleve = '".$type_1."' AND d_date_absence_eleve >= '".$date_debut."' AND a_date_absence_eleve <= '".$date_fin."'";
         if($eleve_1 == "tous" and $classe_1 != "tous") { $requete_compt = mysql_result(mysql_query("SELECT count(DISTINCT (id_absence_eleve)) FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes, ".$prefix_base."absences_eleves WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."absences_eleves.type_absence_eleve = '".$type_1."' AND ".$prefix_base."absences_eleves.d_date_absence_eleve >= '".$date_debut."' AND ".$prefix_base."absences_eleves.a_date_absence_eleve <= '".$date_fin."' AND ".$prefix_base."absences_eleves.eleve_absence_eleve = ".$prefix_base."eleves.login AND ".$prefix_base."classes.id = '".$classe_1."' LIMIT 0,1"),0); }
//         if($eleve_1 == "tous" and $classe_1 != "tous") { $requete_compt = mysql_result(mysql_query("SELECT count(".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.classe, ".$prefix_base."classes.id, ".$prefix_base."classes.nom_complet, ".$prefix_base."absences_eleves.type_absence_eleve, ".$prefix_base."absences_eleves.d_date_absence_eleve, ".$prefix_base."absences_eleves.a_date_absence_eleve, ".$prefix_base."absences_eleves.eleve_absence_eleve) FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes, ".$prefix_base."absences_eleves WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."absences_eleves.type_absence_eleve = '".$type_1."' AND ".$prefix_base."absences_eleves.d_date_absence_eleve >= '".$date_debut."' AND ".$prefix_base."absences_eleves.a_date_absence_eleve <= '".$date_fin."' AND ".$prefix_base."absences_eleves.eleve_absence_eleve = ".$prefix_base."eleves.login AND ".$prefix_base."classes.id = '".$classe_1."' LIMIT 0,1"),0); }

         if($eleve_1 != "tous" and $classe_1 != "tous") { $requete_compt = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE type_absence_eleve = '".$type_1."' AND d_date_absence_eleve >= '".$date_debut."' AND a_date_absence_eleve <= '".$date_fin."' AND eleve_absence_eleve = '".$eleve_1."'"),0); }
         $nb_mois[$i] = $requete_compt;
         $i = $i + 1;
  }

if($type_1=="A") { $tire_graph = "Absences sur les 12 mois précédents"; }
if($type_1=="D") { $tire_graph = "Dispenses sur les 12 mois précédents"; }
if($type_1=="I") { $tire_graph = "Passages à l'infirmerie sur les 12 mois précédents"; }
if($type_1=="R") { $tire_graph = "Retards sur les 12 mois précédents"; }

$y = array($nb_mois[11],$nb_mois[10],$nb_mois[9],$nb_mois[8],$nb_mois[7],$nb_mois[6],$nb_mois[5],$nb_mois[4],$nb_mois[3],$nb_mois[2],$nb_mois[1],$nb_mois[0]);
$x = array($mois_aff[11],$mois_aff[10],$mois_aff[9],$mois_aff[8],$mois_aff[7],$mois_aff[6],$mois_aff[5],$mois_aff[4],$mois_aff[3],$mois_aff[2],$mois_aff[1],$mois_aff[0]);

// Creation du graphique
$graph = new Graph(400, 300);
$graph->setAntiAliasing(TRUE);

$plot = new LinePlot($y);

$plot->grid->setNoBackground();

$plot->title->set($tire_graph);
$plot->title->setFont(new Tuffy(10));
$plot->title->setBackgroundColor(new Color(255, 255, 255, 25));
$plot->title->border->show();
$plot->title->setPadding(3, 3, 3, 3);
$plot->title->move(-20, 25);

// Change la couleur de fond de la grille
//$plot->grid->setBackgroundColor(new Color(235, 235, 180, 60));
$plot->grid->setBackgroundColor(new Color(255, 255, 255, 30));

$plot->setSpace(4, 4, 10, 0);
$plot->setPadding(50, 15, 10, 60);

$plot->setBackgroundGradient(
    new LinearGradient(
        new Color(210, 210, 210),
        new Color(255, 255, 255),
        0
    )
);

$plot->setColor(new Color(0, 0, 150, 20));

$plot->setFillGradient(
    new LinearGradient(
        new Color(150, 150, 210),
        new Color(245, 245, 245),
        90
    )
);

$plot->mark->setType(MARK_CIRCLE);
$plot->mark->border->show();

$plot->yAxis->title->set("Nombre d'absences");
$plot->yAxis->title->setFont(new Tuffy(10));

$plot->xAxis->setLabelText($x);
//$plot->xAxis->SetAngle(50);
$plot->xAxis->label->setFont(new Tuffy(8));
$plot->xAxis->label->setAngle("30");
$plot->xAxis->label->move(10, 0);
$plot->xAxis->label->setAlign(LABEL_RIGHT, LABEL_BOTTOM);
$plot->xAxis->label->setPadding(0, 0, 0, 0);
//$plot->xAxis->left->title->move(-10, 0); // Déplace de 10 pixels vers la gauche

$graph->add($plot);
$graph->draw();
?>
