<?php
/*
 * Copyright 2001, 2002 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
include("../lib/functions.php");


// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='y') {
    die("Le module n'est pas activé.");
}

if (empty($_GET['classe_choix']) AND empty($_POST['classe_choix'])) {$classe_choix="";}
    else { if (isset($_GET['classe_choix'])) {$classe_choix=$_GET['classe_choix'];} if (isset($_POST['classe_choix'])) {$classe_choix=$_POST['classe_choix'];} }
if (empty($_GET['action_sql']) AND empty($_POST['action_sql'])) {$action_sql="";}
    else { if (isset($_GET['action_sql'])) {$action_sql=$_GET['action_sql'];} if (isset($_POST['action_sql'])) {$action_sql=$_POST['action_sql'];} }
if (empty($_GET['cocher']) AND empty($_POST['cocher'])) {$cocher="";}
    else { if (isset($_GET['cocher'])) {$cocher=$_GET['cocher'];} if (isset($_POST['cocher'])) {$cocher=$_POST['cocher'];} }
if (getSettingValue("active_module_trombinoscopes")=='y')
  if (empty($_GET['photo']) AND empty($_POST['photo'])) {$photo="";}
    else { if (isset($_GET['photo'])) {$photo=$_GET['photo'];} if (isset($_POST['photo'])) {$photo=$_POST['photo'];} }
if (empty($_GET['type']) AND empty($_POST['type'])) {$type="A";}
    else { if (isset($_GET['type'])) {$type=$_GET['type'];} if (isset($_POST['type'])) {$type=$_POST['type'];} }
if (empty($_GET['choix']) AND empty($_POST['choix'])) {$choix="sm";}
    else { if (isset($_GET['choix'])) {$choix=$_GET['choix'];} if (isset($_POST['choix'])) {$choix=$_POST['choix'];} }
if (empty($_GET['fiche_eleve']) AND empty($_POST['fiche_eleve'])) {$fiche_eleve="";}
    else { if (isset($_GET['fiche_eleve'])) {$fiche_eleve=$_GET['fiche_eleve'];} if (isset($_POST['fiche_eleve'])) {$fiche_eleve=$_POST['fiche_eleve'];} }
if (empty($_GET['select_fiche_eleve']) AND empty($_POST['select_fiche_eleve'])) {$select_fiche_eleve="";}
    else { if (isset($_GET['select_fiche_eleve'])) {$select_fiche_eleve=$_GET['select_fiche_eleve'];} if (isset($_POST['select_fiche_eleve'])) {$select_fiche_eleve=$_POST['select_fiche_eleve'];} }
if (empty($_GET['action']) AND empty($_POST['action'])) {$action="";}
    else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
if($choix=='sm' AND $type=='I') { $choix = "am"; }

if (empty($_GET['aff_fiche']) AND empty($_POST['aff_fiche'])) {$aff_fiche="";}
    else { if (isset($_GET['aff_fiche'])) {$aff_fiche=$_GET['aff_fiche'];} if (isset($_POST['aff_fiche'])) {$aff_fiche=$_POST['aff_fiche'];} }
if (empty($_GET['debut_selection_suivi']) AND empty($_POST['debut_selection_suivi'])) {$debut_selection_suivi='0';}
    else { if (isset($_GET['debut_selection_suivi'])) {$debut_selection_suivi=$_GET['debut_selection_suivi'];} if (isset($_POST['debut_selection_suivi'])) {$debut_selection_suivi=$_POST['debut_selection_suivi'];} }


// gestion des dates
 if (empty($_GET['day']) and empty($_POST['day'])) {$day=date("d");}
    else { if (isset($_GET['day'])) {$day=$_GET['day'];} if (isset($_POST['day'])) {$day=$_POST['day'];} }
 if (empty($_GET['month']) and empty($_POST['month'])) {$month=date("m");}
    else { if (isset($_GET['month'])) {$month=$_GET['month'];} if (isset($_POST['month'])) {$month=$_POST['month'];} }
 if (empty($_GET['year']) and empty($_POST['year'])) {$year=date("Y");}
    else { if (isset($_GET['year'])) {$year=$_GET['year'];} if (isset($_POST['year'])) {$year=$_POST['year'];} }
 $date_ce_jour = $year."-".$month."-".$day;

// pour le messager permet de choisir une date d'affichage
   if (empty($_GET['du']) AND empty($_POST['du'])) {$du='';}
    else { if (isset($_GET['du'])) {$du=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];} }
	if(isset($du) and !empty($du)) { $date_ce_jour = date_sql($du); }

/*unset($id_classe);
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : (isset($_GET["id_classe"]) ? $_GET["id_classe"] : NULL);
if(isset($_GET['id_classe'])) { $classe_choix = $id_classe; }
unset($day);
$day = isset($_POST["day"]) ? $_POST["day"] : (isset($_GET["day"]) ? $_GET["day"] : date("d"));
unset($month);
$month = isset($_POST["month"]) ? $_POST["month"] : (isset($_GET["month"]) ? $_GET["month"] : date("m"));
unset($year);
$year = isset($_POST["year"]) ? $_POST["year"] : (isset($_GET["year"]) ? $_GET["year"] : date("Y"));
unset($id_classe);
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : (isset($_GET["id_classe"]) ? $_GET["id_classe"] : -1);
unset($id_matiere);
$id_matiere = isset($_POST["id_matiere"]) ? $_POST["id_matiere"] : (isset($_GET["id_matiere"]) ? $_GET["id_matiere"] :-1);
if(isset($_GET["day"]) OR isset($_POST["day"])) { $date_ce_jour = $year."-".$month."-".$day; } else { if(isset($_GET["date_ce_jour"]) OR isset($_POST["date_ce_jour"])) { $date_explose = explode('-', $date_ce_jour); $day = $date_explose[2]; $month = $date_explose[1]; $year = $date_explose[0];} else { $date_ce_jour = date('Y-m-d'); } }
*/
// fin de gestion des dates

//ajout des fiche_suivi des eleve
if ($action_sql == "ajouter" or $action_sql == "modifier")
{
     // Vérifcation des variable
        $date_fiche = date('Y-m-d');
        $heure_fiche = date('H:i:s');
        $data_info_suivi = nl2br(htmlspecialchars(traitement_magic_quotes($_POST['data_info_suivi'])));
        $eleve_suivi_eleve_cpe = $_POST['eleve_suivi_eleve_cpe'];
	$niveau_urgent = $_POST['niveau_urgent'];
	$action_suivi = $_POST['action_suivi'];

        if ($action_sql == "modifier") { $id_suivi_eleve_cpe = $_POST['id_suivi_eleve_cpe']; }

            // Vérification des champs nom et prenom (si il ne sont pas vides ?)
            if($data_info_suivi != "")
            {
                 $test = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."suivi_eleve_cpe WHERE eleve_suivi_eleve_cpe = '".$eleve_suivi_eleve_cpe."' AND date_suivi_eleve_cpe = '".$date_fiche."' AND komenti_suivi_eleve_cpe = '".$data_info_suivi."'"),0);
                 if ($test == "0")
                  {
                     if($action_sql == "ajouter")
                      {
                            // Requete d'insertion MYSQL
                             $requete = "INSERT INTO ".$prefix_base."suivi_eleve_cpe (eleve_suivi_eleve_cpe,parqui_suivi_eleve_cpe,date_suivi_eleve_cpe,heure_suivi_eleve_cpe,komenti_suivi_eleve_cpe,niveau_message_suivi_eleve_cpe,action_suivi_eleve_cpe) VALUES ('$eleve_suivi_eleve_cpe','".$_SESSION['login']."','$date_fiche','$heure_fiche','$data_info_suivi','$niveau_urgent','$action_suivi')";
                      }
                     if($action_sql == "modifier")
                      {
                            // Requete de mise à jour MYSQL
                              $requete = "UPDATE ".$prefix_base."suivi_eleve_cpe SET parqui_suivi_eleve_cpe='".$_SESSION['login']."', komenti_suivi_eleve_cpe = '$data_info_suivi', niveau_message_suivi_eleve_cpe = '$niveau_urgent', action_suivi_eleve_cpe = '$action_suivi' WHERE id_suivi_eleve_cpe = '".$id_suivi_eleve_cpe."'";
                      }
                            // Execution de cette requete dans la base cartouche
                             mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
                             $verification = 1;
                    } else {
                               // vérification = 2 - C'est initiale pour les motif existe déjas
                                 $verification = 2;
                                 $erreur = 1;
                            }
            } else {
                     // vérification = 3 - Tous les champs ne sont pas remplie
                     $verification = 3;
                     $erreur = 1;
                   }

}

if ($action_sql === "supprimer")
 {
//	include "../lib/function_abs.php";

//	if (empty($_GET['id_suivi_eleve_cpe']) and empty($_POST['id_suivi_eleve_cpe'])) {$id_suivi_eleve_cpe="";}
//	 else { if (isset($_GET['id_suivi_eleve_cpe'])) {$id_suivi_eleve_cpe=$_GET['id_suivi_eleve_cpe'];} if (isset($_POST['id_suivi_eleve_cpe'])) {$id_suivi_eleve_cpe=$_POST['id_suivi_eleve_cpe'];} }

//	$action_php = fait_le_menage($id_suivi_eleve_cpe, 'suivi_eleve_cpe')
         $id_suivi_eleve_cpe = $_GET['id_suivi_eleve_cpe'];
         //Requete de suppresion MYSQL
            $requete = "DELETE FROM ".$prefix_base."suivi_eleve_cpe WHERE id_suivi_eleve_cpe ='$id_suivi_eleve_cpe'";
         // Execution de cette requete
            mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
 }

if ($action == "modifier")
 {
      $id_suivi_eleve_cpe = $_GET['id_suivi_eleve_cpe'];
      $requete_modif_fiche = 'SELECT * FROM '.$prefix_base.'suivi_eleve_cpe WHERE id_suivi_eleve_cpe="'.$id_suivi_eleve_cpe.'"';
      $resultat_modif_fiche = mysql_query($requete_modif_fiche) or die('Erreur SQL !'.$requete_modif_fiche.'<br />'.mysql_error());
      $data_modif_fiche = mysql_fetch_array($resultat_modif_fiche);
 }

//requête liste des classes
if ($classe_choix != "")
 { $requete_liste_classe = "SELECT id, classe, nom_complet FROM classes ORDER BY nom_complet DESC"; }
 else
 { $requete_liste_classe = "SELECT id, classe, nom_complet FROM classes ORDER BY nom_complet DESC"; }

if ($action_sql == "supprimer_selection")
 {

	include "../lib/function_abs.php";

	// initialise les variables
	if (empty($_GET['id_absence_eleve']) and empty($_POST['id_absence_eleve'])) {$id_absence_eleve='';}
	    else { if (isset($_GET['id_absence_eleve'])) {$id_absence_eleve=$_GET['id_absence_eleve'];} if (isset($_POST['id_absence_eleve'])) {$id_absence_eleve=$_POST['id_absence_eleve'];} }
	if (empty($_GET['selection']) and empty($_POST['selection'])) {$selection='';}
	    else { if (isset($_GET['selection'])) {$selection=$_GET['selection'];} if (isset($_POST['selection'])) {$selection=$_POST['selection'];} }
	// action
	$action_php = supprime_id($id_absence_eleve, $prefix_base, 'absences_eleves', $selection);
 }

//Configuration du calendrier
include("../../lib/calendrier/calendrier.class.php");
$cal = new Calendrier("form1", "du");

include "../lib/mincals_absences.inc";
?>
<script type="text/javascript" language="javascript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<script type="text/javascript" language="javascript">
<!--
// Scrolling bug fixing by Pierre Gardenat
	NS4 = (document.layers) ? 1 : 0;
	IE4 = (document.all) ? 1 : 0;
	W3C = (document.getElementById) ? 1 : 0;
// W3C stands for the W3C standard, implemented in Mozilla (and Netscape 6) and IE5

// Function show(evt, name)
function show ( evt, name ) {
  if (IE4) {
    evt = window.event;
  }

  var currentX,
      currentY,
      x,
      y,
      docWidth,
      docHeight,
      layerWidth,
      layerHeight,
      ele;


  if ( W3C ) {
    ele = document.getElementById(name);
    currentX = evt.clientX,
    currentY = evt.clientY+document.body.scrollTop+10;
    docWidth = document.width;
    docHeight = document.height;
    layerWidth = ele.style.width;
    layerHeight = ele.style.height;

  } else if ( NS4 ) {
    ele = document.layers[name];
    currentX = evt.pageX,
    currentY = evt.pageY;
    docWidth = document.width;
    docHeight = document.height;
    layerWidth = ele.clip.width;
    layerHeight = ele.clip.height;

  } else {  // meant for IE4
    ele = document.all[name];
    currentX = evt.clientX,
    currentY = evt.clientY+document.body.scrollTop+10;
    docHeight = document.body.offsetHeight;
    docWidth = document.body.offsetWidth;
    //var layerWidth = document.all[name].offsetWidth;
    // for some reason, this doesnt seem to work... so set it to 200
    layerWidth = 200;
    layerHeight = ele.offsetHeight;
  }


  if ( ( currentX + layerWidth ) > docWidth ) {
    x = ( currentX - layerWidth );
  }
  else {
    x = currentX;
  }
  if ( ( currentY + layerHeight ) >= docHeight ) {
     y = ( currentY - layerHeight - 20 );
  }
  else {
    y = currentY + 20;
  }
// (for debugging purpose) alert("docWidth " + docWidth + ", docHeight " + docHeight + "\nlayerWidth " + layerWidth + ", layerHeight " + layerHeight + "\ncurrentX " + currentX + ", currentY " + currentY + "\nx " + x + ", y " + y);


  if ( NS4 ) {
    //ele.xpos = parseInt ( x );
    ele.left = parseInt ( x );
    //ele.ypos = parseInt ( y );
    ele.top = parseInt ( y );
    ele.visibility = "show";
  } else {  // IE4 & W3C
    ele.style.left = parseInt ( x );
    ele.style.top = parseInt ( y );
    ele.style.visibility = "visible";
  }
}

function hide ( name ) {
  if (W3C) {
    document.getElementById(name).style.visibility = "hidden";
  } else if (NS4) {
    document.layers[name].visibility = "hide";
  } else {

      document.all[name].style.visibility = "hidden";

  }
}
//-->
</script>

<?php
//**************** EN-TETE *****************
$titre_page = "Gestion des absences";
require_once("../../lib/header.inc");
//**************** FIN EN-TETE *****************

//Quelle que variabale
  $datej = date('Y-m-d');
  $annee_scolaire=annee_en_cours_t($datej);
?>
<?php /* La page gestion global des absences */ ?>
<p class="bold">|<a href='<?php if($select_fiche_eleve=='' and $fiche_eleve=='' and $choix!='lemessager') { ?>../../accueil.php<?php } else { ?>gestion_absences.php<?php } ?>'>Retour</a>|
<a href="./impression_absences.php?type=<?php echo $type; ?>">Impression</a>|
<a href="../lib/graphiques.php">Graphiques</a>|
<a href="gestion_absences.php?choix=lemessager&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Le messager</a>|
</p>

  <form method="post" action="gestion_absences.php?type=<?php echo $type; ?>&amp;choix=<?php echo $choix; ?>" name="choix_type_vs">
   <fieldset class="fieldset_efface">
     <select name="type">
        <option value="A" onClick="javascript:document.choix_type_vs.submit()" <?php if ($type=="A") {echo "selected"; } ?>>Absence</option>
        <option value="R" onClick="javascript:document.choix_type_vs.submit()" <?php if ($type=="R") {echo "selected"; } ?>>Retard</option>
        <option value="I" onClick="javascript:document.choix_type_vs.submit()" <?php if ($type=="I") {echo "selected"; } ?>>Infirmerie</option>
        <option value="D" onClick="javascript:document.choix_type_vs.submit()" <?php if ($type=="D") {echo "selected"; } ?>>Dispense</option>
     </select><input type="submit" name="submit8" value="&lt;&lt;" />
     &nbsp; <a href="select.php?type=<?php echo $type; ?>">Ajouter</a> - <a href="../lib/tableau.php?type=<?php echo $type; ?>">Tableau</a>
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Voir la fiche élève <input type="text" name="fiche_eleve" value="" /><input type="submit" name="submit8" value="&lt;&lt;" />
   </fieldset>
  </form>
<div class="centre"><hr width="550" size="1"/></div>
<div class="centre">
<?php if($type == "A" and $fiche_eleve == "") { ?>
[ <a href="gestion_absences.php?choix=top10&amp;type=<?php echo $type ?>">Top 10</a> | <a href="gestion_absences.php?choix=sm&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Absence sans motif</a> | <a href="gestion_absences.php?choix=am&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Absence avec motif</a> ]
<?php } if($type == "R" and $fiche_eleve == "") { ?>
[ <a href="gestion_absences.php?choix=top10&amp;type=<?php echo $type; ?>">Top 10</a> | <a href="gestion_absences.php?choix=sm&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Retard sans motif</a> | <a href="gestion_absences.php?choix=am&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Retard avec motif</a> ]
<?php } if($type == "I" and $fiche_eleve == "") { ?>
[ <a href="gestion_absences.php?choix=top10&amp;type=<?php echo $type; ?>">Top 10</a> | <a href="gestion_absences.php?choix=am&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">infirmerie avec motif</a> ]
<?php } if($type == "D" and $fiche_eleve == "") { ?>
[ <a href="gestion_absences.php?choix=top10&amp;type=<?php echo $type; ?>">Top 10</a> | <a href="gestion_absences.php?choix=sm&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Dispense sans motif</a> | <a href="gestion_absences.php?choix=am&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Dispense avec motif</a> ]
<?php } ?>
</div>

<?php if ($choix=="top10" and $fiche_eleve == "" and $select_fiche_eleve == "") { ?>
<?php
       $i = 0;
       if ($type == "A" OR $type == "I" OR $type == "R" OR $type == "D")
         {
            if ($classe_choix != "") { $requete_top10 ="SELECT COUNT(*) FROM ".$prefix."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."classes WHERE type_absence_eleve='".$type."' AND eleve_absence_eleve=id_eleve AND classe_eleve=id_classe AND id_classe='".$classe_choix."' GROUP BY nom_eleve LIMIT 0, 10";  }
            if ($classe_choix == "") { $requete_top10 ="SELECT ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."eleves.sexe, ".$prefix_base."absences_eleves.id_absence_eleve, ".$prefix_base."absences_eleves.eleve_absence_eleve, ".$prefix_base."absences_eleves.justify_absence_eleve, ".$prefix_base."absences_eleves.type_absence_eleve FROM ".$prefix_base."eleves, ".$prefix_base."absences_eleves WHERE ".$prefix_base."eleves.login=".$prefix_base."absences_eleves.eleve_absence_eleve AND ".$prefix_base."absences_eleves.type_absence_eleve='".$type."' GROUP BY nom LIMIT 0, 10";  }
         }

         $execution_top10 =mysql_query($requete_top10) or die('Erreur SQL !'.$requete_top10.'<br />'.mysql_error());
         while ( $data_top10 = mysql_fetch_array($execution_top10))
         {
             $compte = mysql_result(mysql_query("SELECT COUNT(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$data_top10['login']."' AND type_absence_eleve = '".$type."'"),0);
?>
      <div id="d<?php echo $data_top10['id_absence_eleve']; ?>" style="position: absolute; z-index: 20; visibility: hidden; top: 0px; left: 0px;">
          <table border="0" cellpadding="2" cellspacing="2" class="tableau_calque_information">
             <tr>
                <td class="texte_fondjaune_calque_information"><?php echo "<b>".strtoupper($data_top10['nom'])."</b> ".ucfirst($data_top10['prenom']); ?> élève de <?php echo "<b>".classe_de($data_top10['login'])."</b>"; $id_classe_eleve = classe_de($data_top10['login']); ?><br /><?php if ($type == "A") {?>a été absent<?php } ?><?php if ($type == "R") {?>est arrivé en retard<?php } ?><?php if ($type == "I") {?>est allé à l'infirmerie<?php } ?><?php if ($type == "D") {?>a été dispensé<?php } ?><?php if ($data_top10['sexe'] == "F") { ?>e<?php } ?> <b><?php echo $compte ?></b> fois<br /></td>
                <?php if (getSettingValue("active_module_trombinoscopes")=='y') {
                 echo "<td style=\"width: 60px; vertical-align: top\">";
                 $photos = "../../photos/eleves/".$data_top10['elenoet'].".jpg";
                 if (!(file_exists($photos))) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php
                 } ?>
             </tr>
          </table>
       </div>
<?php } ?>

<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<table style="margin: auto; width: 600px;" border="0" cellspacing="1" cellpadding="1">
  <tr style="vertical-align: top">
    <td class="td_tableau_gestion">
    <br />
      <table class="td_tableau_gestion" border="0" cellspacing="1" cellpadding="2">
        <tr>
          <td colspan="3" class="titre_tableau_gestion" nowrap><b>TOP 10</b></td>
        </tr>
        <?php
         $i = 0;
         $ic = 1;
         if ($type == "A" OR $type == "I" OR $type == "R" OR $type == "D")
          {
            if ($classe_choix != "") { $requete_top10 ="SELECT ".$prefix_base."eleves.elenoet, id_absence_eleve, id_eleve, civilite_eleve, nl_classe, num_classe, type_absence_eleve, justify_absence_eleve, info_justify_absence_eleve, motif_absence_eleve, d_date_absence_eleve, a_date_absence_eleve, d_heure_absence_eleve, a_heure_absence_eleve, eleve_absence_eleve, nom_eleve, prenom_eleve, COUNT(*) FROM ".$prefix."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."classes WHERE type_absence_eleve='".$type."' AND eleve_absence_eleve=id_eleve AND classe_eleve=id_classe AND id_classe='".$classe_choix."' GROUP BY nom_eleve LIMIT 0, 10";  }
            if ($classe_choix == "") { $requete_top10 ="SELECT ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."absences_eleves.id_absence_eleve, ".$prefix_base."absences_eleves.eleve_absence_eleve, ".$prefix_base."absences_eleves.justify_absence_eleve, ".$prefix_base."absences_eleves.type_absence_eleve FROM ".$prefix_base."eleves, ".$prefix_base."absences_eleves WHERE ".$prefix_base."eleves.login=".$prefix_base."absences_eleves.eleve_absence_eleve AND ".$prefix_base."absences_eleves.type_absence_eleve='".$type."' GROUP BY nom LIMIT 0, 10";  }
          }
         $execution_top10 = mysql_query($requete_top10) or die('Erreur SQL !'.$requete_top10.'<br />'.mysql_error());
         while ( $data_top10 = mysql_fetch_array($execution_top10))
         {
                     if ($ic === '1') { $ic='2'; $couleur_cellule='td_tableau_absence_1'; } else { $couleur_cellule='td_tableau_absence_2'; $ic='1'; }
         ?>
        <tr>
          <td>&nbsp;</td>
          <td class="<?php echo $couleur_cellule; ?>" onmouseover="show(event, 'd<?php echo $data_top10['id_absence_eleve']; ?>'); return true;" onmouseout="hide('d<?php echo $data_top10['id_absence_eleve']; ?>'); return true;"><a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_top10['login']; ?>" title="consulter la fiche de l'élève"><?php echo "<b>".strtoupper($data_top10['nom'])."</b> ".ucfirst($data_top10['prenom']); ?><a/></td>
          <td class="<?php echo $couleur_cellule; ?>">
            <?php if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo")) {
             $photos = "../../photos/eleves/".$data_top10['elenoet'].".jpg";
                 if (!(file_exists($photos))) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php
             } ?>
          </td>
        </tr>
    <?php } ?>
        <tr>
          <td class="class10">&nbsp;</td>
          <td class="class55bas">&nbsp;</td>
          <td class="class35bas">&nbsp;</td>
        </tr>
      </table>
    </td>
    <td style="text-align: center">
       <br />
       <form name ="form1" method="post" action="gestion_absences.php?type=<?php echo $type; ?>&amp;choix=<?php echo $choix; ?>">
         <fieldset>
          <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
          Classe &nbsp;
          <select name="classe_choix">
            <option value="" selected onClick="javascript:document.form1.submit()">Toutes les classes</option>
                <?php
                  $resultat_liste_classe = mysql_query($requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysql_error());
                  While ( $data_liste_classe = mysql_fetch_array ($resultat_liste_classe)) {
                         if ($classe_choix==$data_liste_classe['id']) {$selected = "selected"; } else {$selected = ""; }?>
                        <option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>  onClick="javascript:document.form1.submit()"><?php echo $data_liste_classe['nom_complet']; ?></option>
                <?php } ?>
          </select>
           <?php if (getSettingValue("active_module_trombinoscopes")=='y')  { ?>
               <br />
               <input type="checkbox" name="photo" value="avec_photo" onClick="javascript:document.form1.submit()" <?php  if ($photo=="avec_photo") { ?>checked="checked"<?php } ?> />Avec photo<br />
           <?php } ?>
               <br />
          Voici le TOP10 des <?php if($type == "A") { ?>absences.<?php } if($type == "R") { ?>retards.<?php } if($type == "I") { ?>passages à l'infirmerie.<?php } if($type == "D") { ?>dispenses.<?php } ?>
	    </fieldset>
          </form>
    </td>
  </tr>
</table>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>

<?php if ($choix=="sm" and $fiche_eleve == "" and $select_fiche_eleve == "") { ?>
  <?php
       $i = 0;
       if ($type == "A" OR $type == "I" OR $type == "R" OR $type == "D")
         {
           if ($classe_choix != "") { $requete_sans_motif ="SELECT ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.sexe, ".$prefix_base."absences_eleves.id_absence_eleve, ".$prefix_base."absences_eleves.saisie_absence_eleve, ".$prefix_base."absences_eleves.eleve_absence_eleve, ".$prefix_base."absences_eleves.justify_absence_eleve, ".$prefix_base."absences_eleves.info_justify_absence_eleve, ".$prefix_base."absences_eleves.type_absence_eleve, ".$prefix_base."absences_eleves.motif_absence_eleve, ".$prefix_base."absences_eleves.d_date_absence_eleve, ".$prefix_base."absences_eleves.a_date_absence_eleve, ".$prefix_base."absences_eleves.d_heure_absence_eleve, ".$prefix_base."absences_eleves.a_heure_absence_eleve, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.classe, ".$prefix_base."classes.id, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes, ".$prefix_base."absences_eleves WHERE ".$prefix_base."eleves.login=".$prefix_base."absences_eleves.eleve_absence_eleve AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' AND ".$prefix_base."absences_eleves.justify_absence_eleve!='O' AND (".$prefix_base."absences_eleves.d_date_absence_eleve <= '".$date_ce_jour."' AND ".$prefix_base."absences_eleves.a_date_absence_eleve >= '".$date_ce_jour."') AND ".$prefix_base."absences_eleves.type_absence_eleve= '".$type."'"; }
           if ($classe_choix == "") { $requete_sans_motif ="SELECT ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.sexe, ".$prefix_base."absences_eleves.id_absence_eleve, ".$prefix_base."absences_eleves.saisie_absence_eleve, ".$prefix_base."absences_eleves.eleve_absence_eleve, ".$prefix_base."absences_eleves.justify_absence_eleve, ".$prefix_base."absences_eleves.info_justify_absence_eleve, ".$prefix_base."absences_eleves.type_absence_eleve, ".$prefix_base."absences_eleves.motif_absence_eleve, ".$prefix_base."absences_eleves.d_date_absence_eleve, ".$prefix_base."absences_eleves.a_date_absence_eleve, ".$prefix_base."absences_eleves.d_heure_absence_eleve, ".$prefix_base."absences_eleves.a_heure_absence_eleve FROM ".$prefix_base."eleves, ".$prefix_base."absences_eleves WHERE ".$prefix_base."eleves.login=".$prefix_base."absences_eleves.eleve_absence_eleve AND ".$prefix_base."absences_eleves.justify_absence_eleve!='O' AND (".$prefix_base."absences_eleves.d_date_absence_eleve <= '".$date_ce_jour."' AND ".$prefix_base."absences_eleves.a_date_absence_eleve >= '".$date_ce_jour."') AND ".$prefix_base."absences_eleves.type_absence_eleve= '".$type."' ORDER BY nom,prenom,d_heure_absence_eleve"; }
         }
         $execution_sans_motif = mysql_query($requete_sans_motif) or die('Erreur SQL !'.$requete_sans_motif.'<br />'.mysql_error());
         while ( $data_sans_motif = mysql_fetch_array($execution_sans_motif))
         {
  ?>

  <?php /* DEBUT DE GESTION DU CALQUE D'INFORMATION */ ?>
     <div id="d<?php echo $data_sans_motif['id_absence_eleve']; ?>" style="position: absolute; z-index: 20; visibility: hidden; top: 0px; left: 0px;">
          <table border="0" cellpadding="2" cellspacing="2" class="tableau_calque_information">
             <tr>
                <td class="texte_fondjaune_calque_information"><?php echo "<b>".strtoupper($data_sans_motif['nom'])."</b> ".ucfirst($data_sans_motif['prenom']); ?> élève de <?php echo "<b>".classe_de($data_sans_motif['login'])."</b>";  $id_classe_eleve = classe_de($data_sans_motif['login']); ?><br /><?php if ($data_sans_motif['type_absence_eleve']=="A") { ?> a été absent<?php if ($data_sans_motif['sexe'] == "F") { ?>e<?php } } if  ($data_sans_motif['type_absence_eleve']=="R") { ?> est arrivé<?php if ($data_sans_motif['sexe'] == "F") { ?>e<?php } ?> en retard<?php } ?><?php if ($data_sans_motif['type_absence_eleve']=="I") { ?>est allé à l'infirmerie<?php } ?><br /> le <?php echo date_frl($data_sans_motif['d_date_absence_eleve']); ?><?php if (($data_sans_motif['a_date_absence_eleve'] != $data_sans_motif['d_date_absence_eleve'] and $data_sans_motif['a_date_absence_eleve'] != "") or $data_sans_motif['a_date_absence_eleve'] == "0000-00-00") { ?> au <?php echo date_frl($data_sans_motif['a_date_absence_eleve']); ?><?php } ?><br /><?php if ($data_sans_motif['a_heure_absence_eleve'] == "00:00:00" or $data_sans_motif['a_heure_absence_eleve'] == "") { ?>à <?php } else { ?>de <?php } ?><?php echo heure($data_sans_motif['d_heure_absence_eleve']); ?> <?php if ($data_sans_motif['a_heure_absence_eleve'] == "00:00:00" or $data_sans_motif['a_heure_absence_eleve'] == "") { } else { ?> à <?php } ?> <?php echo heure($data_sans_motif['a_heure_absence_eleve']); ?></td>
                 <?php if (getSettingValue("active_module_trombinoscopes")=='y') {
                 echo "<td style=\"width: 60px; vertical-align: top\" rowspan=\"4\">";
                 $photos = "../../photos/eleves/".$data_sans_motif['elenoet'].".jpg";
                 if (!(file_exists($photos))) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php
                 } ?>
             </tr>
             <tr>
                <td class="norme_absence"><?php if($data_sans_motif['saisie_absence_eleve']!="") { ?>Enregistré par : <?php echo qui($data_sans_motif['saisie_absence_eleve']); } ?><br />pour le motif : <?php echo motif_de($data_sans_motif['motif_absence_eleve']); ?></td>
             </tr>
             <tr>
                <td class="norme_absence"><?php if ($data_sans_motif['justify_absence_eleve'] != "O") {?><span class="norme_absence_rouge"><b>n'a pas donn&eacute; de justification</b><? } else { ?><span class="norme_absence_vert"><b>a donn&eacute; pour justification : </b><?php } ?></span></td>
             </tr>
             <tr>
                <td class="norme_absence"><?php if(!empty($data_sans_motif['info_justify_absence_eleve'])) { ?><blockquote><?php echo $data_sans_motif['info_justify_absence_eleve']; ?></blockquote><?php } ?></td>
             </tr>
             <tr class="texte_fondjaune_calque_information">
                <td colspan="2">Téléphone responsable :
                  <?php /*
                     $requete_responsable1 = ("SELECT * FROM ".$prefix_base."responsables, ".$prefix_base."eleves WHERE ".$prefix_base."responsables.ereno='".$data_sans_motif['ereno']."'");
                     $execution_responsable1 = mysql_query($requete_responsable1) or die('Erreur SQL !'.$requete_responsable1.''.mysql_error());
                     while ($data_responsable1 = mysql_fetch_array($execution_responsable1))
                      {
                        if ($data_responsable1['tel1_responsable'] != "") { echo "<b>".$data_responsable1['tel1_responsable']."</b> - ".$data_responsable1['civilite_responsable']." ".strtoupper($data_responsable1['nom_responsable'])." ".ucfirst($data_responsable1['prenom_responsable']); }
                        else {?>Conctacter l'administration<?php }
                      } */ ?>
                </td>
              </tr>
           </table>
     </div>
<?php /* FIN DE GESTION DU CALQUE D'INFORMATION */ ?>
<?php } ?>


<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<table style="margin: auto; width: 600px;" border="0" cellspacing="0" cellpadding="1">
  <tr style="vertical-align: top">
    <td class="td_tableau_gestion">
      <br />
      <form name ="form3" method="post" action="gestion_absences.php?choix=<?php echo $choix; ?>&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>&amp;action_sql=supprimer_selection">
       <fieldset class="fieldset_efface">
        <table class="td_tableau_gestion" border="0" cellspacing="1" cellpadding="2">
          <tr>
            <td colspan="3" class="titre_tableau_gestion" nowrap><b><?php if ($type=="A") { ?>Absence sans motif<?php } ?><?php if ($type=="R") { ?>Retard sans motif<?php } ?><?php if ($type=="I") { ?>Infirmerie sans motif<?php } ?><?php if ($type=="D") { ?>Dispense sans motif<?php } ?></b></td>
          </tr>
          <?php
         $total = 0;
         $i = 0;
         $ic = 1;
           $execution_sans_motif = mysql_query($requete_sans_motif) or die('Erreur SQL !'.$requete_sans_motif.'<br />'.mysql_error());
           while ( $data_sans_motif = mysql_fetch_array($execution_sans_motif))
           {
              if ($ic==1) {
                            $ic=2;
                            $couleur_cellule="td_tableau_absence_1";
                          } else {
                                   $couleur_cellule="td_tableau_absence_2";
                                   $ic=1;
                                }
           ?>
          <tr>
            <td>&nbsp;</td>
            <td class="<?php echo $couleur_cellule; ?>" onmouseover="show(event, 'd<?php echo $data_sans_motif['id_absence_eleve']; ?>'); return true;" onmouseout="hide('d<?php echo $data_sans_motif['id_absence_eleve']; ?>'); return true;"><input name="selection[<?php echo $total; ?>]" type="checkbox" value="1" <?php if((isset($selection[$total]) and $selection[$total] == "1") OR $cocher == 1) { ?>checked="checked"<?php } ?> /><input name="id_absence_eleve[<?php echo $total; ?>]" type="hidden" value="<?php echo $data_sans_motif['id_absence_eleve']; ?>" /><a href="ajout_<?php if($data_sans_motif['type_absence_eleve']=="A") { ?>abs<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>ret<?php } if($data_sans_motif['type_absence_eleve']=="D") { ?>dip<?php } if($data_sans_motif['type_absence_eleve']=="I") { ?>inf<?php } ?>.php?action=supprimer&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_sans_motif['id_absence_eleve']; ?>" onClick="return confirm('Etes-vous sur de vouloire le supprimer...')"><img src="../images/x2.png" title="supprimer l'absence" border="0" alt="" /></a><a href="ajout_<?php if($data_sans_motif['type_absence_eleve']=="A") { ?>abs<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>ret<?php } if($data_sans_motif['type_absence_eleve']=="I") { ?>inf<?php } if($data_sans_motif['type_absence_eleve']=="D") { ?>dip<?php } ?>.php?action=modifier&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_sans_motif['id_absence_eleve']; ?>&amp;mode=eleve"><img src="../images/fichier.png" title="modifier l'absence" border="0" alt="" /></a><a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_sans_motif['login']; ?>" title="consulter la fiche de l'élève"><?php echo "<b>".strtoupper($data_sans_motif['nom'])."</b> ".ucfirst($data_sans_motif['prenom']); ?></a></td>
            <td class="<?php echo $couleur_cellule; ?>">

              <?php if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo")) {
                  $photos = "../../photos/eleves/".$data_sans_motif['elenoet'].".jpg";
                 if (!(file_exists($photos))) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td>
              <?php } ?>
            </td>
          </tr>
      <?php $total = $total + 1; } ?>
          <tr>
            <td class="class10">&nbsp;</td>
            <td class="class55bas">&nbsp;</td>
            <td class="class35bas">&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2"><a href="gestion_absences.php?choix=<?php echo $choix; ?>&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>&amp;cocher=<?php if($cocher==1) { ?>0<?php } else { ?>1<?php  } ?>"><?php if($cocher==1) { ?>déc<?php } else { ?>C<?php  } ?>ocher toutes les cellules</a></td>
          </tr>
        </table>
         <input name="submit2" type="submit" value="supprimer la s&eacute;lection" onClick="return confirm('Etes-vous sur de vouloire le supprimer...')" />
         <input value="<?php echo $year; ?>" name="year" id="year1" type="hidden" />
         <input value="<?php echo $month; ?>" name="month" id="month1" type="hidden" />
         <input value="<?php echo $day; ?>" name="day" id="day1" type="hidden" />
       </fieldset>
      </form>
    </td>
    <td style="text-align: center"><br />
          <form name ="form1" method="post" action="gestion_absences.php?type=<?php echo $type; ?>&amp;choix=<?php echo $choix; ?>">
          <fieldset>
            <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
          <?php //Affiche le calendrier
                minicals($year, $month, $day, $classe_choix, $type, 'gestion_absences');
          ?>
          Informations donnée pour la date du<br /><b><?php echo date_frl($date_ce_jour); ?></b><br /><br />
          Classe &nbsp;
          <select name="classe_choix">
            <option value="" selected onClick="javascript:document.form1.submit()">Toutes les classes</option>
                <?php
                  $resultat_liste_classe = mysql_query($requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysql_error());
                  while($data_liste_classe = mysql_fetch_array ($resultat_liste_classe)) {
                         if ($classe_choix==$data_liste_classe['id']) {$selected = "selected"; } else {$selected = ""; }?>
                        <option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>  onClick="javascript:document.form1.submit()"><?php echo $data_liste_classe['nom_complet']; ?></option>
                <?php } ?>
          </select><noscript><input value=">>" name="date" type="submit" /></noscript><br />
          <input value="<?php echo $year; ?>" name="year" id="year2" type="hidden" />
          <input value="<?php echo $month; ?>" name="month" id="month2" type="hidden" />
          <input value="<?php echo $day; ?>" name="day" id="day2" type="hidden" />
          <?php if (getSettingValue("active_module_trombinoscopes")=='y')  { ?>
              <input type="checkbox" name="photo" value="avec_photo" onClick="javascript:document.form1.submit()"   <?php  if ($photo=="avec_photo") { ?>checked="checked"<?php } ?> />Avec photo<br /><br />
          <?php } ?>
          Pour voir, toutes les <?php if($type == "A") { ?>Absence<?php } if($type == "R") { ?>Retard<?php } if($type == "I") { ?>Infirmerie<?php } if($type == "D") { ?>Dispense<?php } ?> n'ayant pas eu de justificatif, veuillez cocher la case ci-dessous.<br />
            <input type="checkbox" name="choix" value="sma" onClick="javascript:document.form1.submit()" <?php  if ($choix=="sma") { ?>checked="checked"<?php } ?> />
            <?php if($type == "A") { ?>Absence<?php } if($type == "R") { ?>Retard<?php } if($type == "I") { ?>Infirmerie<?php } if($type == "D") { ?>Dispense<?php } ?> sans justification
          </fieldset>
          </form>
    </td>
  </tr>
</table>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>


<?php if ($choix=="sma" and $fiche_eleve == "" and $select_fiche_eleve == "") { ?>
<?php
       $i = 0;
       if ($type == "A" OR $type == "I" OR $type == "R" OR $type == "D")
         {
           if ($classe_choix != "") { $requete_sans_motif ="SELECT ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.sexe, ".$prefix_base."absences_eleves.id_absence_eleve, ".$prefix_base."absences_eleves.eleve_absence_eleve, ".$prefix_base."absences_eleves.justify_absence_eleve, ".$prefix_base."absences_eleves.type_absence_eleve, ".$prefix_base."absences_eleves.motif_absence_eleve, ".$prefix_base."absences_eleves.info_justify_absence_eleve, ".$prefix_base."absences_eleves.d_heure_absence_eleve, ".$prefix_base."absences_eleves.a_heure_absence_eleve, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.classe, ".$prefix_base."classes.id, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes, ".$prefix_base."absences_eleves WHERE ".$prefix_base."eleves.login=".$prefix_base."absences_eleves.eleve_absence_eleve AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' AND ".$prefix_base."absences_eleves.justify_absence_eleve!='O' AND ".$prefix_base."absences_eleves.type_absence_eleve= '".$type."'"; }
           if ($classe_choix == "") { $requete_sans_motif ="SELECT ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.sexe, ".$prefix_base."absences_eleves.id_absence_eleve, ".$prefix_base."absences_eleves.eleve_absence_eleve, ".$prefix_base."absences_eleves.justify_absence_eleve, ".$prefix_base."absences_eleves.type_absence_eleve, ".$prefix_base."absences_eleves.motif_absence_eleve, ".$prefix_base."absences_eleves.info_justify_absence_eleve, ".$prefix_base."absences_eleves.d_date_absence_eleve, ".$prefix_base."absences_eleves.a_date_absence_eleve, ".$prefix_base."absences_eleves.d_heure_absence_eleve, ".$prefix_base."absences_eleves.a_heure_absence_eleve FROM ".$prefix_base."eleves, ".$prefix_base."absences_eleves WHERE ".$prefix_base."eleves.login=".$prefix_base."absences_eleves.eleve_absence_eleve AND ".$prefix_base."absences_eleves.justify_absence_eleve!='O' AND ".$prefix_base."absences_eleves.type_absence_eleve= '".$type."' ORDER BY nom,prenom,d_heure_absence_eleve"; }
         }

         $execution_sans_motif = mysql_query($requete_sans_motif) or die('Erreur SQL !'.$requete_sans_motif.'<br />'.mysql_error());
         while($data_sans_motif = mysql_fetch_array($execution_sans_motif))
         {
 ?>
<?php if($type == "A" OR $type == "I" OR $type == "R" OR $type == "D") {?>
    <div id="d<?php echo $data_sans_motif['id_absence_eleve']; ?>" style="position: absolute; z-index: 20; visibility: hidden; top: 0px; left: 0px;">
         <table border="0" cellpadding="2" cellspacing="2" class="tableau_calque_information">
            <tr>
               <td class="texte_fondjaune_calque_information"><?php echo "<b>".$data_sans_motif['nom']."</b> ".$data_sans_motif['prenom']; ?> élève de <?php echo "<b>".classe_de($data_sans_motif['login'])."</b>"; $id_classe_eleve = classe_de($data_sans_motif['login']); ?><br /><?php if ($data_sans_motif['type_absence_eleve']=="A") { ?> a été absent<?php if ($data_sans_motif['sexe'] == "F") { ?>e<?php } } if  ($data_sans_motif['type_absence_eleve']=="R") { ?> est arrivé<?php if ($data_sans_motif['sexe'] == "F") { ?>e<?php } ?> en retard<?php } ?><?php if ($data_sans_motif['type_absence_eleve']=="I") { ?>est allé à l'infirmerie<?php } ?><br /> le <?php echo date_frl($data_sans_motif['d_date_absence_eleve']); ?><?php if (($data_sans_motif['a_date_absence_eleve'] != $data_sans_motif['d_date_absence_eleve'] and $data_sans_motif['a_date_absence_eleve'] != "") or $data_sans_motif['a_date_absence_eleve'] == "0000-00-00") { ?> au <?php echo date_frl($data_sans_motif['a_date_absence_eleve']); ?><?php } ?><br /><?php if ($data_sans_motif['a_heure_absence_eleve'] == "" or $data_sans_motif['a_heure_absence_eleve'] == "00:00:00") { ?>à <?php } else { ?>de <?php } ?><?php echo heure($data_sans_motif['d_heure_absence_eleve']); ?> <?php if ($data_sans_motif['a_heure_absence_eleve'] == "00:00:00" or $data_sans_motif['a_heure_absence_eleve'] == "") { } else { ?> à <?php } ?> <?php echo heure($data_sans_motif['a_heure_absence_eleve']); ?></td>
               <?php if (getSettingValue("active_module_trombinoscopes")=='y') {
                 echo "<td style=\"width: 60px; vertical-align: top\" rowspan=\"4\">";
                 $photos = "../../photos/eleves/".$data_sans_motif['elenoet'].".jpg";
                 if (!(file_exists($photos))) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php                 } ?>
            </tr>
            <tr>
               <td class="norme_absence">pour le motif : <?php echo motif_de($data_sans_motif['motif_absence_eleve']); ?></td>
             </tr>
             <tr>
                <td class="norme_absence"><?php if ($data_sans_motif['justify_absence_eleve'] != "O") {?><span class="norme_absence_rouge"><b>n'a pas donn&eacute; de justification</b><? } else { ?><span class="norme_absence_vert"><b>a donn&eacute; pour justification : </b><?php } ?></span></td>
             </tr>
             <tr>
                <td class="norme_absence"><?php if(!empty($data_sans_motif['info_justify_absence_eleve'])) { ?><blockquote><?php echo $data_sans_motif['info_justify_absence_eleve']; ?></blockquote><?php } ?></td>
             </tr>
             <tr class="texte_fondjaune_calque_information">
                <td colspan="3">Téléphone responsable :
                  <?php /*
                     $requete_responsable1 = ("SELECT * FROM ".$prefix_base."responsables, ".$prefix_base."eleves WHERE ".$prefix_base."responsables.ereno='".$data_sans_motif['ereno']."'");
                     echo $requete_responsable1;
                     $execution_responsable1 = mysql_query($requete_responsable1) or die('Erreur SQL !'.$requete_responsable1.''.mysql_error());
                     while ($data_responsable1 = mysql_fetch_array($execution_responsable1))
                      {
                         if ($data_responsable1['tel1'] != "") { echo "<b>".$data_responsable1['tel1']."</b> - ".$data_responsable1['civilite_responsable']." ".strtoupper($data_responsable1['nom1'])." ".ucfirst($data_responsable1['prenom1']); }
                         else {?>Conctacter l'administration<?php }
                      } */ ?>
                </td>
              </tr>
          </table>
       </div>
<?php } ?>
<?php } ?>


<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<table style="margin: auto; width: 600px;" border="0" cellspacing="0" cellpadding="1">
  <tr style="vertical-align: top">
    <td class="td_tableau_gestion">
    <br />
     <form name ="form3" method="post" action="gestion_absences.php?choix=<?php echo $choix; ?>&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>&amp;action_sql=supprimer_selection">
      <fieldset class="fieldset_efface">
       <table class="td_tableau_gestion" border="0" cellspacing="1" cellpadding="2">
        <tr>
          <td colspan="3" class="titre_tableau_gestion" nowrap><b><?php if ($type=="A") { ?>Absence sans motif<?php } ?><?php if ($type=="R") { ?>Retard sans motif<?php } ?><?php if ($type=="I") { ?>Infirmerie sans motif<?php } ?><?php if ($type=="D") { ?>Dispense sans motif<?php } ?></b></td>
        </tr>
        <?php
         $total = 0;
         $i = 0;
         $ic = 1;
         $execution_sans_motif = mysql_query($requete_sans_motif) or die('Erreur SQL !'.$requete_sans_motif.'<br />'.mysql_error());
         while ( $data_sans_motif = mysql_fetch_array($execution_sans_motif))
         {
                     if ($ic==1) {
                          $ic=2;
                          $couleur_cellule="td_tableau_absence_1";
                       } else {
                                 $couleur_cellule="td_tableau_absence_2";
                                 $ic=1;
                              }
         ?>
        <tr>
          <td>&nbsp;</td>
          <td class="<?php echo $couleur_cellule; ?>" onmouseover="show(event, 'd<?php echo $data_sans_motif['id_absence_eleve']; ?>'); return true;" onmouseout="hide('d<?php echo $data_sans_motif['id_absence_eleve']; ?>'); return true;"><input name="selection[<?php echo $total; ?>]" type="checkbox" value="1" <?php if((isset($selection[$total]) and $selection[$total]) == "1" OR $cocher == 1) { ?>checked="checked"<?php } ?> /><input name="id_absence_eleve[<?php echo $total; ?>]" type="hidden" value="<?php echo $data_sans_motif['id_absence_eleve']; ?>" /><a href="ajout_<?php if($data_sans_motif['type_absence_eleve']=="A") { ?>abs<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>ret<?php } if($data_sans_motif['type_absence_eleve']=="D") { ?>dip<?php } if($data_sans_motif['type_absence_eleve']=="I") { ?>inf<?php } ?>.php?action=supprimer&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_sans_motif['id_absence_eleve']; ?>" onClick="return confirm('Etes-vous sur de vouloire le supprimer...')"><img src="../images/x2.png" title="supprimer l'absence" border="0" alt="" /></a><a href="ajout_<?php if($data_sans_motif['type_absence_eleve']=="A") { ?>abs<?php } if($data_sans_motif['type_absence_eleve']=="D") { ?>dip<?php } if($data_sans_motif['type_absence_eleve']=="I") { ?>inf<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>ret<?php } ?>.php?action=modifier&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_sans_motif['id_absence_eleve']; ?>&amp;mode=eleve"><img src="../images/fichier.png" title="modifier l'absence" border="0" alt="" /></a><a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_sans_motif['login']; ?>" title="consulter la fiche de l'élève"><?php echo "<b>".strtoupper($data_sans_motif['nom'])."</b> ".ucfirst($data_sans_motif['prenom']); ?></a></td>
          <td class="<?php echo $couleur_cellule; ?>">
            <?php if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo")) {
                                      $id_eleve = $data_sans_motif['id_absence_eleve'];
                                      $id_eleve_photo = $data_sans_motif['elenoet'];
                                      $photos = "../../photos/eleves/".$id_eleve_photo.".jpg";            
                 if (!(file_exists($photos))) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; } 
		 $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td>
            <?php } ?>
          </td>
        </tr>
    <?php $total = $total + 1; } ?>
        <tr>
          <td class="class10">&nbsp;</td>
          <td class="class55bas">&nbsp;</td>
          <td class="class35bas">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2"><a href="gestion_absences.php?choix=<?php echo $choix; ?>&amp;date_ce_jour=<?php echo date_fr($date_ce_jour); ?>&amp;cocher=<?php if($cocher==1) { ?>0<?php } else { ?>1<?php  } ?>"><?php if($cocher==1) { ?>déc<?php } else { ?>C<?php  } ?>ocher toutes les cellules</a></td>
        </tr>
      </table>
        <input name="submit2" type="submit" value="supprimer la s&eacute;lection" onClick="return confirm('Etes-vous sur de vouloire le supprimer...')" />
        <input value="<?php echo $year; ?>" name="year" id="year3" type="hidden" />
        <input value="<?php echo $month; ?>" name="month" id="month3" type="hidden" />
        <input value="<?php echo $day; ?>" name="day" id="day3" type="hidden" />
      </fieldset>
     </form>
    </td>
    <td style="text-align: center">
          <br />
          <form name="form1" method="post" action="gestion_absences.php?type=<?php echo $type; ?>">
          <fieldset>
            <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
          Classe
          <select name="classe_choix">
            <option value="" selected onClick="javascript:document.form1.submit()">Toutes les classes</option>
                <?php
                  $resultat_liste_classe = mysql_query($requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysql_error());
                  While ( $data_liste_classe = mysql_fetch_array ($resultat_liste_classe)) {
                         if ($classe_choix==$data_liste_classe['id']) {$selected = "selected"; } else {$selected = ""; }?>
                        <option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>  onClick="javascript:document.form1.submit()"><?php echo $data_liste_classe['nom_complet']; ?></option>
                <?php } ?>
          </select><br />
          <?php if (getSettingValue("active_module_trombinoscopes")=='y') { ?>
          <input type="checkbox" name="photo" value="avec_photo" onClick="javascript:document.form1.submit()" <?php  if ($photo=="avec_photo") { ?>checked="checked"<?php } ?> />Avec photo<br /><br />
          <?php } ?>
          Les informations ci-contre vous donne toutes les fiches n'ayant pas eu de justificatif. <br /><br />
          Visualiser par date, veuillez décocher la case ci-dessous.<br />
            <input type="checkbox" name="choix" value="sma" onClick="javascript:document.form1.submit()" <?php  if ($choix=="sma") { ?>checked="checked"<?php } ?> />
            <?php if($type == "A") { ?>Absence<?php } if($type == "R") { ?>Retard<?php } if($type == "I") { ?>Infirmerie<?php } if($type == "D") { ?>Dispense<?php } ?> sans justification
           </fieldset>
          </form>
    </td>
  </tr>
</table>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>


<?php if ($choix=="am" and $fiche_eleve == "" and $select_fiche_eleve == "") { ?>
<?php
       $i = 0;
       if ($type == "A" OR $type == "I" OR $type == "R" OR $type == "D")
         {
           if ($classe_choix != "") { $requete_avec_motif ="SELECT ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.sexe, ".$prefix_base."absences_eleves.id_absence_eleve, ".$prefix_base."absences_eleves.eleve_absence_eleve, ".$prefix_base."absences_eleves.justify_absence_eleve,".$prefix_base."absences_eleves.type_absence_eleve, ".$prefix_base."absences_eleves.motif_absence_eleve, ".$prefix_base."absences_eleves.info_justify_absence_eleve, ".$prefix_base."absences_eleves.d_date_absence_eleve, ".$prefix_base."absences_eleves.d_heure_absence_eleve, ".$prefix_base."absences_eleves.a_heure_absence_eleve, ".$prefix_base."absences_eleves.a_date_absence_eleve, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.id, ".$prefix_base."classes.classe, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes, ".$prefix_base."absences_eleves WHERE ".$prefix_base."eleves.login=".$prefix_base."absences_eleves.eleve_absence_eleve AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."'  AND  ".$prefix_base."absences_eleves.justify_absence_eleve='O' AND (".$prefix_base."absences_eleves.d_date_absence_eleve <= '".$date_ce_jour."' AND ".$prefix_base."absences_eleves.a_date_absence_eleve >= '".$date_ce_jour."') AND ".$prefix_base."absences_eleves.type_absence_eleve='".$type."'"; }
           if ($classe_choix == "") { $requete_avec_motif ="SELECT ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."eleves.elenoet, ".$prefix_base."eleves.sexe, ".$prefix_base."absences_eleves.id_absence_eleve, ".$prefix_base."absences_eleves.eleve_absence_eleve, ".$prefix_base."absences_eleves.justify_absence_eleve,".$prefix_base."absences_eleves.type_absence_eleve, ".$prefix_base."absences_eleves.motif_absence_eleve, ".$prefix_base."absences_eleves.info_justify_absence_eleve, ".$prefix_base."absences_eleves.d_date_absence_eleve, ".$prefix_base."absences_eleves.d_heure_absence_eleve, ".$prefix_base."absences_eleves.a_heure_absence_eleve, ".$prefix_base."absences_eleves.a_date_absence_eleve FROM ".$prefix_base."eleves, ".$prefix_base."absences_eleves WHERE ".$prefix_base."eleves.login=".$prefix_base."absences_eleves.eleve_absence_eleve AND ".$prefix_base."absences_eleves.justify_absence_eleve='O' AND (".$prefix_base."absences_eleves.d_date_absence_eleve <= '".$date_ce_jour."' AND ".$prefix_base."absences_eleves.a_date_absence_eleve >= '".$date_ce_jour."') AND ".$prefix_base."absences_eleves.type_absence_eleve= '".$type."' ORDER BY nom,prenom,d_heure_absence_eleve";  }
         }
         $execution_avec_motif = mysql_query($requete_avec_motif) or die('Erreur SQL !'.$requete_avec_motif.'<br />'.mysql_error());
         while ( $data_avec_motif = mysql_fetch_array($execution_avec_motif))
         {
 ?>
<?php if($type == "A" OR $type == "I" OR $type == "R" OR $type == "D") {?>
     <div id="d<?php echo $data_avec_motif['id_absence_eleve']; ?>" style="position: absolute; z-index: 20; visibility: hidden; top: 0px; left: 0px;">
          <table border="0" cellpadding="2" cellspacing="2" class="tableau_calque_information">
              <tr>
                <td class="texte_fondjaune_calque_information"><?php echo "<b>".strtoupper($data_avec_motif['nom'])."</b> ".ucfirst($data_avec_motif['prenom']); ?> élève de <?php echo "<b>".classe_de($data_avec_motif['login'])."</b>"; $id_classe_eleve = classe_de($data_avec_motif['login']); ?><br /><?php if ($data_avec_motif['type_absence_eleve']=="A") { ?> &agrave; &eacute;t&eacute; absent<?php if ($data_avec_motif['sexe'] == "F") { ?>e<?php } } if  ($data_avec_motif['type_absence_eleve']=="R") { ?> est arrivé<?php if ($data_avec_motif['sexe'] == "F") { ?>e<?php } ?> en retard<?php } ?><?php if ($data_avec_motif['type_absence_eleve']=="I") { ?>est allé à l'infirmerie<?php } ?><br /> le <?php echo date_frl($data_avec_motif['d_date_absence_eleve']); ?><?php if (($data_avec_motif['a_date_absence_eleve'] != $data_avec_motif['d_date_absence_eleve'] and $data_avec_motif['a_date_absence_eleve'] != "") or $data_avec_motif['a_date_absence_eleve'] == "0000-00-00") { ?> au <?php echo date_frl($data_avec_motif['a_date_absence_eleve']); ?><?php } ?><br />
<?php /*
<?php if ($data_avec_motif['heure_retard_eleve'] == "" or $data_avec_motif['heure_retard_eleve'] != "00:00:00") { ?>à 
<?php } else { ?>de <?php } ?><?php echo heure($data_avec_motif['d_heure_absence_eleve']); ?> 
<?php if ($data_avec_motif['heure_retard_eleve'] != "00:00:00" or $data_avec_motif['heure_retard_eleve'] == "") { } 
else { ?> à <?php } ?> <?php echo heure($data_avec_motif['a_heure_absence_eleve']); ?></td>
*/?>

                 <?php if (getSettingValue("active_module_trombinoscopes")=='y') {
                 echo "<td style=\"width: 60px; vertical-align: top\" rowspan=\"4\">";
                 $photos = "../../photos/eleves/".$data_avec_motif['elenoet'].".jpg";
                 if (!(file_exists($photos))) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php
                 } ?>
              </tr>
              <tr>
                <td class="norme_absence">pour le motif : <?php echo motif_de($data_avec_motif['motif_absence_eleve']); ?></td>
              </tr>
              <tr>
                <td class="norme_absence"><?php if ($data_avec_motif['justify_absence_eleve'] != "O") {?><span class="norme_absence_rouge"><b>n'a pas donn&eacute; de justification</b><? } else { ?><span class="norme_absence_vert"><b>a donn&eacute; pour justification : </b><?php } ?></span></td>
              </tr>
              <tr>
                <td class="norme_absence"><?php if(!empty($data_avec_motif['info_justify_absence_eleve'])) { ?><blockquote><?php echo $data_avec_motif['info_justify_absence_eleve']; ?></blockquote><?php } ?></td>
              </tr>
              <tr class="texte_fondjaune_calque_information">
                <td colspan="3">Téléphone responsable :
                  <?php     /*
                     $requete_responsable1 = ("SELECT * FROM ".$prefix_base."responsables, ".$prefix_base."eleves WHERE ".$prefix_base."responsables.ereno='".$data_sans_motif['ereno']."'");
                     $execution_responsable1 = mysql_query($requete_responsable1) or die('Erreur SQL !'.$requete_responsable1.''.mysql_error());
                     while ($data_responsable1 = mysql_fetch_array($execution_responsable1))
                       {
                          if ($data_responsable1['tel1_responsable'] != "") { echo "<b>".$data_responsable1['tel1_responsable']."</b> - ".$data_responsable1['civilite_responsable']." ".strtoupper($data_responsable1['nom_responsable'])." ".ucfirst($data_responsable1['prenom_responsable']); }
                          else {?>Conctacter l'administration<?php }
                       } */ ?>
                </td>
              </tr>
         </table>
     </div>
<?php } ?>
<?php } ?>


<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<table style="margin: auto; width: 600px;" border="0" cellspacing="0" cellpadding="1">
  <tr style="vertical-align: top">
    <td class="td_tableau_gestion">
  <br />
  <form name ="form3" method="post" action="gestion_absences.php?choix=<?php echo $choix; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>&amp;type=<?php echo $type; ?>&amp;action_sql=supprimer_selection">
   <fieldset class="fieldset_efface">
    <table class="td_tableau_gestion" border="0" cellspacing="1" cellpadding="2">
        <tr>
          <td colspan="3" class="titre_tableau_gestion" nowrap><b><?php if ($type=="A") { ?>Absence avec motif<?php } ?><?php if ($type=="R") { ?>Retard avec motif<?php } ?><?php if ($type=="I") { ?>Infirmerie avec motif<?php } ?><?php if ($type=="D") { ?>Dispense avec motif<?php } ?></b></td>
        </tr>
        <?php
         $total = 0;
         $i = 0;
         $ic = 1;
         $execution_avec_motif = mysql_query($requete_avec_motif) or die('Erreur SQL !'.$requete_avec_motif.'<br />'.mysql_error());
         while ( $data_avec_motif = mysql_fetch_array($execution_avec_motif))
         {
                     if ($ic==1) {
                          $ic=2;
                          $couleur_cellule="td_tableau_absence_1";
                       } else {
                                 $couleur_cellule="td_tableau_absence_2";
                                 $ic=1;
                              }
         ?>
        <tr>
          <td>&nbsp;</td>
          <td class="<?php echo $couleur_cellule; ?>" onmouseover="show(event, 'd<?php echo $data_avec_motif['id_absence_eleve']; ?>'); return true;" onmouseout="hide('d<?php if ($type == "D" ) { echo $data_avec_motif['id_dispense_eleve']; } else  { echo $data_avec_motif['id_absence_eleve']; } ?>'); return true;"><input name="selection[<?php echo $total; ?>]" type="checkbox" value="1" <?php if((isset($selection[$total]) and $selection[$total] == "1") OR $cocher == 1) { ?>checked="checked"<?php } ?> /><input name="id_absence_eleve[<?php echo $total; ?>]" type="hidden" value="<?php echo $data_avec_motif['id_absence_eleve']; ?>" /><a href="ajout_<?php if($data_avec_motif['type_absence_eleve']=="A") { ?>abs<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>ret<?php } if ($data_avec_motif['type_absence_eleve']=="D") { ?>dip<?php } if ($data_avec_motif['type_absence_eleve']=="I") { ?>inf<?php } ?>.php?action=supprimer&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_avec_motif['id_absence_eleve']; ?>" onClick="return confirm('Etes-vous sur de vouloire le supprimer...')"><img src="../images/x2.png" title="supprimer l'absence" border="0" alt="" /></a><a href="ajout_<?php if($data_avec_motif['type_absence_eleve']=="A") { ?>abs<?php } if($data_avec_motif['type_absence_eleve']=="D") { ?>dip<?php } if($data_avec_motif['type_absence_eleve']=="I") { ?>inf<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>ret<?php } ?>.php?action=modifier&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_avec_motif['id_absence_eleve']; ?>&amp;mode=eleve"><img src="../images/fichier.png" title="modifier l'absence" border="0" alt="" /></a><a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_avec_motif['login']; ?>" title="consulter la fiche de l'élève"><?php echo "<b>".strtoupper($data_avec_motif['nom'])."</b> ".ucfirst($data_avec_motif['prenom']); ?></a></td>
          <td class="<?php echo $couleur_cellule; ?>">
          <?php if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo")) {
                                      $photos = "../../photos/eleves/".$data_avec_motif['elenoet'].".jpg";
                 if (!(file_exists($photos))) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td>
            <?php } ?>
          </td>
        </tr>
    <?php $total = $total + 1; } ?>
        <tr>
          <td class="class10">&nbsp;</td>
          <td class="class55bas">&nbsp;</td>
          <td class="class35bas">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2"><a href="gestion_absences.php?choix=<?php echo $choix; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>&amp;cocher=<?php if($cocher==1) { ?>0<?php } else { ?>1<?php  } ?>"><?php if($cocher==1) { ?>déc<?php } else { ?>C<?php  } ?>ocher toutes les cellules</a></td>
        </tr>
      </table>
        <input name="submit2" type="submit" value="supprimer la s&eacute;lection" onClick="return confirm('Etes-vous sur de vouloire le supprimer...')" />
        <input value="<?php echo $year; ?>" name="year" id="year4" type="hidden" />
        <input value="<?php echo $month; ?>" name="month" id="month4" type="hidden" />
        <input value="<?php echo $day; ?>" name="day" id="day4" type="hidden" />
       </fieldset>
      </form>
    </td>
    <td style="text-align: center"><br />
        <form name ="form1" method="post" action="gestion_absences.php?choix=<?php echo $choix; ?>&amp;type=<?php echo $type; ?>">
          <fieldset>
            <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <?php //Affiche le calendrier
                minicals($year, $month, $day, $classe_choix, $type, 'gestion_absences');
            ?>
            Informations donnée pour la date du<br /><b><?php echo date_frl($date_ce_jour); ?></b><br /><br />
            Classe &nbsp;
            <select name="classe_choix">
              <option value="" selected onClick="javascript:document.form1.submit()">Toutes les classes</option>
                  <?php
                    $resultat_liste_classe = mysql_query($requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysql_error());
                    While ( $data_liste_classe = mysql_fetch_array ($resultat_liste_classe)) {
                           if ($classe_choix==$data_liste_classe['id']) {$selected = "selected"; } else {$selected = ""; }?>
                          <option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>  onClick="javascript:document.form1.submit()"><?php echo $data_liste_classe['nom_complet']; ?></option>
                  <?php } ?>
            </select><noscript><input value=">>" name="date" type="submit" /></noscript><br />
            <input value="<?php echo $year; ?>" name="year5" id="year" type="hidden" />
            <input value="<?php echo $month; ?>" name="month5" id="month" type="hidden" />
            <input value="<?php echo $day; ?>" name="day" id="day5" type="hidden" />
            <?php if (getSettingValue("active_module_trombinoscopes")=='y') { ?>
            <input type="checkbox" name="photo" value="avec_photo" onClick="javascript:document.form1.submit()" <?php  if ($photo=="avec_photo") { ?>checked="checked"<?php } ?> />Avec photo<br />
            <?php } ?>
          </fieldset>
        </form>
    </td>
  </tr>
</table>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>


<?php if ($fiche_eleve!="") { ?>
<?php
         $cpt_liste = 0;
         $requete_liste_fiche = "SELECT * FROM ".$prefix_base."eleves WHERE ".$prefix_base."eleves.nom  LIKE '".$fiche_eleve."%' GROUP BY login ORDER BY nom, prenom";
         $execution_liste_fiche = mysql_query($requete_liste_fiche) or die('Erreur SQL !'.$requete_liste_fiche.'<br />'.mysql_error());
         while ( $data_liste_fiche = mysql_fetch_array($execution_liste_fiche))
          {
              $login_liste[$cpt_liste] = $data_liste_fiche['login'];
              $nom_liste[$cpt_liste] = strtoupper($data_liste_fiche['nom']);
              $prenom_liste[$cpt_liste] = ucfirst($data_liste_fiche['prenom']);
              $cpt_liste = $cpt_liste + 1;
          }
?>
<br />
<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
    <table style="margin: auto; width: 500px;" border="0" cellspacing="2" cellpadding="0">
       <tr class="fond_rouge">
           <td class="titre_tableau_gestion"><b>Liste des élèves</b></td>
       </tr>
       <?php $cpt_aff_liste = 0; $ic = 1;
             while ($cpt_aff_liste < $cpt_liste)
              { if ($ic==1) { $ic=2; $couleur_cellule="td_tableau_absence_1"; } else { $couleur_cellule="td_tableau_absence_2"; $ic=1; }
              ?>
                  <tr class="<?php echo $couleur_cellule; ?>">
                      <td class="norme_absence_min"><a href="gestion_absences.php?select_fiche_eleve=<?php echo $login_liste[$cpt_aff_liste]; ?>"><?php echo $nom_liste[$cpt_aff_liste]." ".$prenom_liste[$cpt_aff_liste]." - ".classe_de($login_liste[$cpt_aff_liste]); ?></a></td>
                  </tr>
             <?php $cpt_aff_liste = $cpt_aff_liste + 1; } ?>
    </table>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>


<?php if ($select_fiche_eleve!="") { ?>
    <?php
         $requete_liste_fiche = "SELECT * FROM ".$prefix_base."eleves WHERE ".$prefix_base."eleves.login = '".$select_fiche_eleve."'";
         $execution_liste_fiche = mysql_query($requete_liste_fiche) or die('Erreur SQL !'.$requete_liste_fiche.'<br />'.mysql_error());
         while ( $data_liste_fiche = mysql_fetch_array($execution_liste_fiche))
          {
              $login_eleve = $data_liste_fiche['login'];
              $id_eleve_photo = $data_liste_fiche['elenoet'];
              $nom_eleve = strtoupper($data_liste_fiche['nom']);
              $prenom_eleve = ucfirst($data_liste_fiche['prenom']);
              $naissance_eleve = date_frl(date_sql(affiche_date_naissance($data_liste_fiche['naissance'])));
              $date_de_naissance = $data_liste_fiche['naissance'];
              $sexe_eleve = $data_liste_fiche['sexe'];
          }

        function age($date_de_naissance_fr)
          {
            //à partir de la date de naissance, retourne l'âge dans la variable $age

            // date de naissance (partie à modifier)
              $ddn = $date_de_naissance_fr;

            // enregistrement de la date du jour
              $DATEDUJOUR = date("Y-m-d");
              $DATEFRAN = date("d/m/Y");

            // calcul de mon age d'après la date de naissance $ddn
              $annais = substr("$ddn", 0, 4);
              $anjour = substr("$DATEFRAN", 6, 4);
              $moisnais = substr("$ddn", 4, 2);
              $moisjour = substr("$DATEFRAN", 3, 2);
              $journais = substr("$ddn", 6, 2);
              $jourjour = substr("$DATEFRAN", 0, 2);

              $age = $anjour-$annais;
              if ($moisjour<$moisnais){$age=$age-1;}
              if ($jourjour<$journais && $moisjour==$moisnais){$age=$age-1;}
              return($age);
           }

         function pp($classe_choix)
          {
            global $prefix_base;
               $call_prof_classe = mysql_query("SELECT * FROM ".$prefix_base."classes, ".$prefix_base."j_eleves_professeurs, ".$prefix_base."j_eleves_classes WHERE ".$prefix_base."j_eleves_professeurs.login = ".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe = ".$prefix_base."classes.id AND ".$prefix_base."classes.nom_complet = '".$classe_choix."'");
               $data_prof_classe = mysql_fetch_array($call_prof_classe);
               $suivi_par = $data_prof_classe['suivi_par'];
               return($suivi_par);
          }
    ?>
    <br />
    <? /* div de centrage du tableau pour ie5 */ ?>
    <div style="text-align:center">
    <table class="entete_tableau_selection" border="0" cellspacing="0" cellpadding="2">
       <tr>
           <td class="titre_tableau_gestion" colspan="2"><b>Identitée élève</b></td>
       </tr>
       <tr>
           <td class="td_tableau_fiche" style="width: 440px; vertical-align: top">Nom : <?php echo $nom_eleve; ?><br />Prénom : <?php echo $prenom_eleve; ?><br />Date de naissance : <?php echo $naissance_eleve; ?><br />Age : <?php echo age($date_de_naissance); ?> ans <br /><br />Classe : <?php echo classe_de($login_eleve); ?> (Suivi par : <?php echo pp(classe_de($login_eleve)); ?>)</td>
                <?php if (getSettingValue("active_module_trombinoscopes")=='y') {
                echo "<td class=\"td_tableau_fiche\" style=\"width: 60px; vertical-align: top\">";
                $photos = "../../photos/eleves/".$id_eleve_photo.".jpg";
                 if (!(file_exists($photos))) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php
                 } ?>
       </tr>
    </table>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
  <br />

    <? /* div de centrage du tableau pour ie5 */ ?>
    <div style="text-align:center">
	[ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;aff_fiche=infoeleve" title="consulter la fiche de l'élève">Information élève</a> | <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;aff_fiche=tableauannuel" title="consulter la fiche de l'élève">Statistique annuel</a> <?php /*| <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;aff_fiche=courrierfamille" title="Dialoguer avec la famille">Contact famille</a> */ ?> ]
    </div>

    <? /* div de centrage du tableau pour ie5 */ ?>
    <div style="text-align:center">
<?php if($aff_fiche==='infoeleve' or $aff_fiche==='') { ?>
    <table class="entete_tableau_selection" border="0" cellspacing="0" cellpadding="2">
       <tr>
           <td class="titre_tableau_gestion" colspan="2"><b>Information élève</b></td>
       </tr>
       <tr>
           <td class="td_tableau_fiche" style="width: 400px; vertical-align: top">
           <?php
	     $cpt_komenti = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."suivi_eleve_cpe WHERE ".$prefix_base."suivi_eleve_cpe.eleve_suivi_eleve_cpe = '".$login_eleve."'"),0);        
	     $requete_komenti = "SELECT * FROM ".$prefix_base."suivi_eleve_cpe WHERE ".$prefix_base."suivi_eleve_cpe.eleve_suivi_eleve_cpe = '".$login_eleve."' ORDER BY date_suivi_eleve_cpe DESC, heure_suivi_eleve_cpe DESC LIMIT ".$debut_selection_suivi.", 2";
             $execution_komenti = mysql_query($requete_komenti) or die('Erreur SQL !'.$requete_komenti.'<br />'.mysql_error());
              while ( $data_komenti = mysql_fetch_array($execution_komenti))
                {
			$action_pour_eleve = '';
			if(!empty($data_komenti['action_suivi_eleve_cpe']) and $data_komenti['action_suivi_eleve_cpe'] != 'A') { $action_pour_eleve = ', '.action_de($data_komenti['action_suivi_eleve_cpe']); }
		   if(!empty($data_komenti['niveau_message_suivi_eleve_cpe'])) {
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='1') { $couleur='#FFFFFF'; $couleur2='#280FFF'; $drapeau='[information]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='2') { $couleur='#FFF3DF'; $couleur2='#FF782F'; $drapeau='[urgent]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='3') { $couleur='#FFDFDF'; $couleur2='#FF0000'; $drapeau='[prioritaire]'; }
			  } else { $couleur='#FFFFFF'; $couleur2='#4DFF2F'; $drapeau=''; } ?>
                    <p class="info_eleve" style="background: <?php echo $couleur; ?>;"><b><?php echo date_frl($data_komenti['date_suivi_eleve_cpe']).' - '.$data_komenti['heure_suivi_eleve_cpe'].' <span style="font-weight: bold; color: '.$couleur2.';">'.$drapeau.'</span>'; ?></b><br /><?php echo $data_komenti['komenti_suivi_eleve_cpe'].$action_pour_eleve; ?><br /><br /><span class="dimi_texte">écrit par: <?php echo qui($data_komenti['parqui_suivi_eleve_cpe']); ?><br />[ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;id_suivi_eleve_cpe=<?php echo $data_komenti['id_suivi_eleve_cpe']; ?>&amp;action=modifier#formulaire">modifier</a> | <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;id_suivi_eleve_cpe=<?php echo $data_komenti['id_suivi_eleve_cpe']; ?>&amp;action_sql=supprimer">supprimer</a> ] <?php /* [ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;id_suivi_eleve_cpe=<?php echo $data_komenti['id_suivi_eleve_cpe']; ?>">action</a> ] */ ?></span></p>
           <?php } ?>

	<div style="text-align: center;">
	  <?php if($debut_selection_suivi!='0') { ?><a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi-'2'; ?>">Page précédente</a><?php } ?>
	  <?php $debut_selection_suivi_suivant = $debut_selection_suivi+'2'; if($debut_selection_suivi!='0' and $debut_selection_suivi_suivant<=$cpt_komenti) { ?> | <?php } ?>
	  <?php if($debut_selection_suivi_suivant<=$cpt_komenti) { ?><a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi+'2'; ?>">Page suivant</a><?php } ?>
	</div>

            <a name="formulaire"></a>
            <form method="post" action="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>">
               <fieldset>
                 <legend>Ajouter un suivi</legend>
                 <select id="info_suivi" onchange="data_info_suivi.value += info_suivi.options[info_suivi.selectedIndex].text + '\n'" style="width: 210px;">
                   <option>Sélectionné un texte rapide</option>
		   <option>[Exclusion du cours] A été exclus du cours de:   à:</option>
                   <option>Rencontre avec les parents</option>
                   <option>Avertissement</option>
                 </select>
                   <input type="hidden" name="eleve_suivi_eleve_cpe" value="<?php echo $login_eleve; ?>" />
                   <input type="hidden" name="action_sql" value="<?php if($action == "modifier") { ?>modifier<?php } else { ?>ajouter<?php } ?>" />
                   <?php if($action === 'modifier') { ?>
                      <input type="hidden" name="id_suivi_eleve_cpe" value="<?php echo $id_suivi_eleve_cpe; ?>" />
                   <?php } ?>
<?php /* 		   <input type="hidden" name="uid_post" value="<?php echo ereg_replace(' ','%20',$uid); ?>" /> */ ?>
                   <input type="submit" name="submit8" value="Valider la saisie" />
                   <br />
		   <table style="border: 0px" cellspacing="1" cellpadding="1">
		   	<tr>
				<td>
				<textarea id="data_info_suivi" name="data_info_suivi" rows="3" cols="30" style="height: 70px;"><?php if($action == "modifier") { echo $data_modif_fiche['komenti_suivi_eleve_cpe']; } ?></textarea>
				</td>
				<td>
				<div style="font-family: Arial; font-size: 0.8em; background-color: #FFFFFF; border : 1px solid #0061BD; height: 70px; padding: 0px;">
				Niveau de priorité<br />
				<input name="niveau_urgent" value="1" type="radio" <?php if(!empty($data_modif_fiche['niveau_message_suivi_eleve_cpe']) and $data_modif_fiche['niveau_message_suivi_eleve_cpe']==='1') { ?>checked="checked"<?php } else { ?>checked="checked"<?php } ?> />Information<br />
				<input name="niveau_urgent" value="2" type="radio" <?php if(!empty($data_modif_fiche['niveau_message_suivi_eleve_cpe']) and $data_modif_fiche['niveau_message_suivi_eleve_cpe']==='2') { ?>checked="checked"<?php } ?> />Urgent<br />
				<input name="niveau_urgent" value="3" type="radio" <?php if(!empty($data_modif_fiche['niveau_message_suivi_eleve_cpe']) and $data_modif_fiche['niveau_message_suivi_eleve_cpe']==='3') { ?>checked="checked"<?php } ?> />Prioritaire<br />
				</div>
				</td>
			</tr>
		   </table>
		   Entraine une action
			<select name="action_suivi" style="width: 250px;">
 	                <?php
			      $requete_liste_action = "SELECT init_absence_action, def_absence_action FROM ".$prefix_base."absences_actions ORDER BY init_absence_action ASC";
        	              $resultat_liste_action = mysql_query($requete_liste_action) or die('Erreur SQL !'.$requete_liste_action.'<br />'.mysql_error());
	                      while ( $data_liste_action = mysql_fetch_array ($resultat_liste_action)) { ?>
                                     <option value="<?php echo $data_liste_action['init_absence_action']; ?>" <?php if(!empty($data_modif_fiche['action_suivi_eleve_cpe']) and $data_modif_fiche['action_suivi_eleve_cpe'] === $data_liste_action['init_absence_action']) { ?>selected="selected"<?php } ?>><?php echo $data_liste_action['init_absence_action']." - ".$data_liste_action['def_absence_action']; ?></option>
                              <?php } ?>
			</select>
		   <br />
              </fieldset>
            </form>
           </td>
           <td class="td_tableau_fiche bordure_sous_menu" style="width: 100px; vertical-align: top;">
           <?php $cpt_absences = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='A'"),0);
           if($cpt_absences != 0) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=A',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Absences"><?php echo $cpt_absences; ?></a></b> Absence(s)</p>
           non justifiée(s)<br />
           <?php $requete_absences_nr = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='A' AND justify_absence_eleve = 'N' ORDER BY d_date_absence_eleve DESC, d_heure_absence_eleve ASC";
                 $execution_absences_nr = mysql_query($requete_absences_nr) or die('Erreur SQL !'.$requete_absences_nr.'<br />'.mysql_error());
                 while ($data_absences_nr = mysql_fetch_array($execution_absences_nr))
                   {
                      ?><a href="ajout_abs.php?action=modifier&amp;type=A&amp;id=<?php echo $data_absences_nr['id_absence_eleve']; ?>&amp;mode=eleve&amp;select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;fiche=oui"><?php
                      echo date_fr($data_absences_nr['d_date_absence_eleve'])."<br />";
                      echo $data_absences_nr['d_heure_absence_eleve']."</a><br /><br />";
                      ?><?php
                   }
           ?>
           <?php } $cpt_retards = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='R'"),0);
           if($cpt_retards != 0) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=R',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Retards"><?php echo $cpt_retards; ?></a></b> Retards</p>
           non justifié(s)<br />
           <?php $requete_retards_nr = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='R' AND justify_absence_eleve = 'N' ORDER BY d_date_absence_eleve DESC, d_heure_absence_eleve ASC";
                 $execution_retards_nr = mysql_query($requete_retards_nr) or die('Erreur SQL !'.$requete_retards_nr.'<br />'.mysql_error());
                 while ($data_retards_nr = mysql_fetch_array($execution_retards_nr))
                   {
                      ?><a href="ajout_ret.php?action=modifier&amp;type=R&amp;id=<?php echo $data_retards_nr['id_absence_eleve']; ?>&amp;mode=eleve&amp;select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;fiche=oui"><?php
                      echo date_fr($data_retards_nr['d_date_absence_eleve'])."<br />";
                      echo $data_retards_nr['d_heure_absence_eleve']."<br /><br />";
                      ?></a><?php
                   }
           ?>
           <?php } $cpt_dispences = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='D'"),0);
           if($cpt_dispences != 0) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=D',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Dispences"><?php echo $cpt_dispences; ?></a></b> Dispences</p>
           non justifiée(s)<br />
           <?php $requete_dispences_nr = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='D' AND justify_absence_eleve = 'N' ORDER BY d_date_absence_eleve DESC, d_heure_absence_eleve ASC";
                 $execution_dispences_nr = mysql_query($requete_dispences_nr) or die('Erreur SQL !'.$requete_dispences_nr.'<br />'.mysql_error());
                 while ($data_dispences_nr = mysql_fetch_array($execution_dispences_nr))
                   {
                      ?><a href="ajout_dip.php?action=modifier&amp;type=D&amp;id=<?php echo $data_dispences_nr['id_absence_eleve']; ?>&amp;mode=eleve&amp;select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;fiche=oui"><?php
                      echo date_fr($data_dispences_nr['d_date_absence_eleve'])."<br />";
                      echo $data_dispences_nr['d_heure_absence_eleve']."<br /><br />";
                      ?></a><?php
                   }
           ?>
           <?php } $cpt_infirmeries = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='I'"),0);
           if($cpt_infirmeries != 0) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=I',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Infirmerie"><?php echo $cpt_infirmeries; ?></a></b> Infirmeries</p>
           <br />
           <?php } ?>
           </td>
       </tr>
    </table>
<?php }

	if($aff_fiche==="tableauannuel") {

		 $requete_absences_nr = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='A' ORDER BY d_date_absence_eleve DESC";
                 $execution_absences_nr = mysql_query($requete_absences_nr) or die('Erreur SQL !'.$requete_absences_nr.'<br />'.mysql_error());
                 while ($data_absences_nr = mysql_fetch_array($execution_absences_nr))
                   {
			$date_debut = date_fr($data_absences_nr['d_date_absence_eleve']);
			$date_fin = date_fr($data_absences_nr['a_date_absence_eleve']);
			  $passage='oui';
			while($passage==='oui') {
		          $dateexplode = explode('/', $date_debut);
			    $date_debut_tableau_jour = eregi_replace('^0','',$dateexplode[0]);
			    $date_debut_tableau_mois = eregi_replace('^0','',$dateexplode[1]);
			    $date_debut_tableau= $date_debut_tableau_jour.'/'.$date_debut_tableau_mois.'/'.$dateexplode[2];
			    if(empty($tableau_info_donnee[$date_debut_tableau])) { $tableau_info_donnee[$date_debut_tableau]=''; }
			    $tableau_info_donnee[$date_debut_tableau]['absence'] = 'oui';
			    if($date_debut===$date_fin) { $passage='non'; } else { $passage='oui'; }
			    $date_debut = date("d/m/Y", mktime(0, 0, 0, $dateexplode[1], $dateexplode[0]+1,  $dateexplode[2]));
			}
                   }
		 $requete_absences_nr = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='R' ORDER BY d_date_absence_eleve DESC";
                 $execution_absences_nr = mysql_query($requete_absences_nr) or die('Erreur SQL !'.$requete_absences_nr.'<br />'.mysql_error());
                 while ($data_absences_nr = mysql_fetch_array($execution_absences_nr))
                   {
			$date_debut = date_fr($data_absences_nr['d_date_absence_eleve']);
			    $date_debut = eregi_replace('^0','',$date_debut);
			$date_fin = date_fr($data_absences_nr['a_date_absence_eleve']);
			    $date_fin = eregi_replace('^0','',$date_fin);
			if(empty($tableau_info_donnee[$date_debut])) { $tableau_info_donnee[$date_debut]=''; }
			$tableau_info_donnee[$date_debut]['retard'] = 'oui';
			if(empty($tableau_info_donnee[$date_fin])) { $tableau_info_donnee[$date_fin]=''; }
			$tableau_info_donnee[$date_fin]['retard'] = 'oui';
                   }

echo '<h2>Statistique sur une année</h2>'; echo @tableau_annuel($select_fiche_eleve, '8', '12', '2006', $tableau_info_donnee); 
}

	if($aff_fiche==="courrierfamille") { ?>

	<?php } ?>

<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>

<?php if ($choix === 'lemessager' and $fiche_eleve === '' and $select_fiche_eleve === '') { ?>
<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<br />
<form method="post" action="gestion_absences.php?choix=lemessager" name="form1">A la date du <input name="du" type="text" size="11" maxlength="11" value="<?php if(isset($du)) { echo $du; } ?>" /><a href="#calend" onClick="<?php echo $cal->get_strPopup('../../lib/calendrier/pop.calendrier.php',350,170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a> <input type="submit" name="Submit" value="&gt;&gt;" /></form>
    <table style="margin: auto; width: 700px;" border="0" cellspacing="1" cellpadding="0">
       <tr class="fond_rouge">
           <td colspan="2" class="titre_tableau_gestion"><b>Le messager</b></td>
       </tr>
       <tr class="td_tableau_absence_1">
           <td class="norme_absence_min" style="text-align: center; width: 50%;">Les prioritaire</td>
           <td class="norme_absence_min" style="text-align: center; width: 50%;">Les message</td>
       </tr>
       <tr class="td_tableau_absence_2">
           <td class="norme_absence_min" valign="top">
	   <?php
             $requete_komenti = "SELECT * FROM ".$prefix_base."suivi_eleve_cpe WHERE ".$prefix_base."suivi_eleve_cpe.date_suivi_eleve_cpe = '".$date_ce_jour."' AND niveau_message_suivi_eleve_cpe='3' ORDER BY date_suivi_eleve_cpe DESC, heure_suivi_eleve_cpe DESC";
             $execution_komenti = mysql_query($requete_komenti) or die('Erreur SQL !'.$requete_komenti.'<br />'.mysql_error());
              while ( $data_komenti = mysql_fetch_array($execution_komenti))
                { 
		   if(!empty($data_komenti['niveau_message_suivi_eleve_cpe'])) {
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='1') { $couleur='#FFFFFF'; $couleur2='#280FFF'; $drapeau='[information]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='2') { $couleur='#FFF3DF'; $couleur2='#FF782F'; $drapeau='[urgent]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='3') { $couleur='#FFDFDF'; $couleur2='#FF0000'; $drapeau='[prioritaire]'; }
			  } else { $couleur='#FFFFFF'; $couleur2='#4DFF2F'; $drapeau=''; } ?>
                    <p class="info_eleve" style="background: <?php echo $couleur; ?>;"><b><?php echo date_frl($data_komenti['date_suivi_eleve_cpe']).' - '.$data_komenti['heure_suivi_eleve_cpe']; ?></b><br /><?php echo $data_komenti['komenti_suivi_eleve_cpe']; ?><br /><br /><span class="dimi_texte">écrit par: <?php echo qui($data_komenti['parqui_suivi_eleve_cpe']); ?><br />[ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_komenti['eleve_suivi_eleve_cpe']; ?>">lire</a> ]</span></p>
           <?php } ?>
	   </td>
           <td class="norme_absence_min" valign="top">
           <?php
             $requete_komenti = "SELECT * FROM ".$prefix_base."suivi_eleve_cpe WHERE ".$prefix_base."suivi_eleve_cpe.date_suivi_eleve_cpe = '".$date_ce_jour."'  AND niveau_message_suivi_eleve_cpe!='3' ORDER BY date_suivi_eleve_cpe DESC, heure_suivi_eleve_cpe DESC";
             $execution_komenti = mysql_query($requete_komenti) or die('Erreur SQL !'.$requete_komenti.'<br />'.mysql_error());
              while ( $data_komenti = mysql_fetch_array($execution_komenti))
                { 
		   if(!empty($data_komenti['niveau_message_suivi_eleve_cpe'])) {
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='1') { $couleur='#FFFFFF'; $couleur2='#280FFF'; $drapeau='[information]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='2') { $couleur='#FFF3DF'; $couleur2='#FF782F'; $drapeau='[urgent]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='3') { $couleur='#FFDFDF'; $couleur2='#FF0000'; $drapeau='[prioritaire]'; }
			  } else { $couleur='#FFFFFF'; $couleur2='#4DFF2F'; $drapeau=''; } ?>
                    <p class="info_eleve" style="background: <?php echo $couleur; ?>;"><b><?php echo date_frl($data_komenti['date_suivi_eleve_cpe']).' - '.$data_komenti['heure_suivi_eleve_cpe'].' <span style="font-weight: bold; color: '.$couleur2.';">'.$drapeau.'</span>'; ?></b><br /><?php echo $data_komenti['komenti_suivi_eleve_cpe']; ?><br /><br /><span class="dimi_texte">écrit par: <?php echo qui($data_komenti['parqui_suivi_eleve_cpe']); ?><br />pour: <strong><?php echo qui_eleve($data_komenti['eleve_suivi_eleve_cpe']); ?></strong> de <?php echo classe_de($data_komenti['eleve_suivi_eleve_cpe']) ?><br />[ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_komenti['eleve_suivi_eleve_cpe']; ?>">lire</a> ]</span></p>
           <?php } ?>
	   </td>
       </tr>
    </table>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>
<?php mysql_close(); ?>
