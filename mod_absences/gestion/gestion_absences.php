<?php
/*
* $Id$
*
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
//require_once("../../lib/initialisations.inc.php");
require_once("../../lib/initialisations.inc.php");
include("../lib/functions.php");


// Resume session
$resultat_session = $session_gepi->security_check();
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

if (empty($_GET['classe_choix']) and empty($_POST['classe_choix'])) {$classe_choix="";}
    else { if (isset($_GET['classe_choix'])) {$classe_choix=$_GET['classe_choix'];} if (isset($_POST['classe_choix'])) {$classe_choix=$_POST['classe_choix'];} }
if (empty($_GET['action_sql']) and empty($_POST['action_sql'])) {$action_sql="";}
    else { if (isset($_GET['action_sql'])) {$action_sql=$_GET['action_sql'];} if (isset($_POST['action_sql'])) {$action_sql=$_POST['action_sql'];} }
if (empty($_GET['cocher']) and empty($_POST['cocher'])) {$cocher="";}
    else { if (isset($_GET['cocher'])) {$cocher=$_GET['cocher'];} if (isset($_POST['cocher'])) {$cocher=$_POST['cocher'];} }
if (getSettingValue("active_module_trombinoscopes")=='y')
  if (empty($_GET['photo']) and empty($_POST['photo'])) {$photo="";}
    else { if (isset($_GET['photo'])) {$photo=$_GET['photo'];} if (isset($_POST['photo'])) {$photo=$_POST['photo'];} }
if (empty($_GET['type']) and empty($_POST['type'])) {$type="A";}
    else { if (isset($_GET['type'])) {$type=$_GET['type'];} if (isset($_POST['type'])) {$type=$_POST['type'];} }
if (empty($_GET['choix']) and empty($_POST['choix'])) {$choix="sm";}
    else { if (isset($_GET['choix'])) {$choix=$_GET['choix'];} if (isset($_POST['choix'])) {$choix=$_POST['choix'];} }
if (empty($_GET['fiche_eleve']) and empty($_POST['fiche_eleve'])) {$fiche_eleve="";}
    else { if (isset($_GET['fiche_eleve'])) {$fiche_eleve=$_GET['fiche_eleve'];} if (isset($_POST['fiche_eleve'])) {$fiche_eleve=$_POST['fiche_eleve'];} }
if (empty($_GET['select_fiche_eleve']) and empty($_POST['select_fiche_eleve'])) {$select_fiche_eleve="";}
    else { if (isset($_GET['select_fiche_eleve'])) {$select_fiche_eleve=$_GET['select_fiche_eleve'];} if (isset($_POST['select_fiche_eleve'])) {$select_fiche_eleve=$_POST['select_fiche_eleve'];} }
if (empty($_GET['action']) and empty($_POST['action'])) {$action="";}
    else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
if (empty($_GET['support_suivi_eleve_cpe']) and empty($_POST['support_suivi_eleve_cpe'])) { $support_suivi_eleve_cpe = ''; }
   else { if (isset($_GET['support_suivi_eleve_cpe'])) { $support_suivi_eleve_cpe = $_GET['support_suivi_eleve_cpe']; } if (isset($_POST['support_suivi_eleve_cpe'])) { $support_suivi_eleve_cpe = $_POST['support_suivi_eleve_cpe']; } }
if (empty($_GET['lettre_type']) and empty($_POST['lettre_type'])) { $lettre_type = ''; }
   else { if (isset($_GET['lettre_type'])) { $lettre_type = $_GET['lettre_type']; } if (isset($_POST['lettre_type'])) { $lettre_type = $_POST['lettre_type']; } }
if($choix=='sm' and $type=='I') { $choix = "am"; }



if (empty($_GET['aff_fiche']) and empty($_POST['aff_fiche'])) {$aff_fiche="";}
    else { if (isset($_GET['aff_fiche'])) {$aff_fiche=$_GET['aff_fiche'];} if (isset($_POST['aff_fiche'])) {$aff_fiche=$_POST['aff_fiche'];} }
if (empty($_GET['debut_selection_suivi']) and empty($_POST['debut_selection_suivi'])) {$debut_selection_suivi='0';}
    else { if (isset($_GET['debut_selection_suivi'])) {$debut_selection_suivi=$_GET['debut_selection_suivi'];} if (isset($_POST['debut_selection_suivi'])) {$debut_selection_suivi=$_POST['debut_selection_suivi'];} }

if (empty($_GET['id_suivi_eleve_cpe']) and empty($_POST['id_suivi_eleve_cpe'])) { $id_suivi_eleve_cpe = ''; }
   else { if (isset($_GET['id_suivi_eleve_cpe'])) { $id_suivi_eleve_cpe = $_GET['id_suivi_eleve_cpe']; } if (isset($_POST['id_suivi_eleve_cpe'])) { $id_suivi_eleve_cpe = $_POST['id_suivi_eleve_cpe']; } }
if (empty($_GET['id_lettre_suivi']) and empty($_POST['id_lettre_suivi'])) { $id_lettre_suivi = ''; }
   else { if (isset($_GET['id_lettre_suivi'])) { $id_lettre_suivi = $_GET['id_lettre_suivi']; } if (isset($_POST['id_lettre_suivi'])) { $id_lettre_suivi = $_POST['id_lettre_suivi']; } }

// gestion des dates
 if (empty($_GET['date_ce_jour']) and empty($_POST['date_ce_jour'])) { $date_ce_jour = ''; }
   else { if (isset($_GET['date_ce_jour'])) { $date_ce_jour = $_GET['date_ce_jour']; } if (isset($_POST['date_ce_jour'])) { $date_ce_jour = $_POST['date_ce_jour']; } }
 if (empty($_GET['day']) and empty($_POST['day'])) {$day=date("d");}
    else { if (isset($_GET['day'])) {$day=$_GET['day'];} if (isset($_POST['day'])) {$day=$_POST['day'];} }
 if (empty($_GET['month']) and empty($_POST['month'])) {$month=date("m");}
    else { if (isset($_GET['month'])) {$month=$_GET['month'];} if (isset($_POST['month'])) {$month=$_POST['month'];} }
 if (empty($_GET['year']) and empty($_POST['year'])) {$year=date("Y");}
    else { if (isset($_GET['year'])) {$year=$_GET['year'];} if (isset($_POST['year'])) {$year=$_POST['year'];} }
      if ( !empty($date_ce_jour) ) {
	$ou_est_on = explode('-',$date_ce_jour);
	$year = $ou_est_on[0]; $month = $ou_est_on[1]; $day =  $ou_est_on[2];
      } else { $date_ce_jour = $year."-".$month."-".$day; }


// pour le messager permet de choisir une date d'affichage
   if (empty($_GET['du']) and empty($_POST['du'])) {$du='';}
    else { if (isset($_GET['du'])) {$du=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];} }
	if(isset($du) and !empty($du)) { $date_ce_jour = date_sql($du); }
// fin de gestion des dates

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
               $call_prof_classe = mysql_query("SELECT * FROM ".$prefix_base."classes, ".$prefix_base."j_eleves_professeurs, ".$prefix_base."j_eleves_classes WHERE ".$prefix_base."j_eleves_professeurs.login = ".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe = ".$prefix_base."classes.id AND ".$prefix_base."classes.classe = '".$classe_choix."'");
               $data_prof_classe = mysql_fetch_array($call_prof_classe);
               $suivi_par = $data_prof_classe['suivi_par'];
               return($suivi_par);
          }

// On ajoute un paramètre sur les élèves de ce CPE en particulier
$sql_eleves_cpe = "SELECT e_login FROM j_eleves_cpe WHERE cpe_login = '".$_SESSION['login']."'";
$query_eleves_cpe = mysql_query($sql_eleves_cpe) OR die('Erreur SQL ! <br />' . $sql_eleves_cpe . ' <br /> ' . mysql_error());
$test_cpe = array();

$test_nbre_eleves_cpe = mysql_num_rows($query_eleves_cpe);
while($test_eleves_cpe = mysql_fetch_array($query_eleves_cpe)){
	$test_cpe[] = $test_eleves_cpe['e_login'];
}

//ajout des fiche_suivi des eleves
if ($action_sql == "ajouter" or $action_sql == "modifier")
{
     // Vérification des variables
        $date_fiche = date('Y-m-d');
        $heure_fiche = date('H:i:s');
        $data_info_suivi = nl2br(htmlspecialchars(traitement_magic_quotes(rtrim($_POST['data_info_suivi'],"\\r\\n"))));
        $eleve_suivi_eleve_cpe = $_POST['eleve_suivi_eleve_cpe'];
	$niveau_urgent = $_POST['niveau_urgent'];
	$action_suivi = $_POST['action_suivi'];

        if ($action_sql == "modifier") { $id_suivi_eleve_cpe = $_POST['id_suivi_eleve_cpe']; }

            // Vérification de la présence des données
            if($data_info_suivi != "")
            {

		     	$courrier_suivi_eleve_cpe='';

                if($action_sql == "ajouter")
                {
                    // Requete d'insertion MYSQL
                             $requete = "INSERT INTO ".$prefix_base."suivi_eleve_cpe
                             				(eleve_suivi_eleve_cpe,parqui_suivi_eleve_cpe,date_suivi_eleve_cpe,heure_suivi_eleve_cpe,komenti_suivi_eleve_cpe,niveau_message_suivi_eleve_cpe,action_suivi_eleve_cpe,support_suivi_eleve_cpe,courrier_suivi_eleve_cpe)
                             			 VALUES
                             			 	('$eleve_suivi_eleve_cpe','".$_SESSION['login']."','$date_fiche','$heure_fiche','$data_info_suivi','$niveau_urgent','$action_suivi','$support_suivi_eleve_cpe','$courrier_suivi_eleve_cpe')";
                      }
                     if($action_sql == "modifier")
                      {
                            // Requete de mise à jour MYSQL
                              $requete = "UPDATE ".$prefix_base."suivi_eleve_cpe SET parqui_suivi_eleve_cpe='".$_SESSION['login']."', komenti_suivi_eleve_cpe = '$data_info_suivi', niveau_message_suivi_eleve_cpe = '$niveau_urgent', action_suivi_eleve_cpe = '$action_suivi', support_suivi_eleve_cpe = '".$support_suivi_eleve_cpe."' WHERE id_suivi_eleve_cpe = '".$id_suivi_eleve_cpe."'";
                      }
                            // Execution de cette requete dans la base cartouche
                             mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
			     if(!empty($id_suivi_eleve_cpe)) { $id_saisi = $id_suivi_eleve_cpe; } else { $id_saisi = mysql_insert_id(); }
                             $verification = 1;

			     // si le support d'action est le courrier alors on ajout le courrier dans le suivi des courriers
			     if($support_suivi_eleve_cpe === '4' and !empty($lettre_type))
			      {
	                             $requete_courrier = "INSERT INTO ".$prefix_base."lettres_suivis (quirecois_lettre_suivi, partde_lettre_suivi, partdenum_lettre_suivi, quiemet_lettre_suivi, emis_date_lettre_suivi, emis_heure_lettre_suivi, envoye_date_lettre_suivi, envoye_heure_lettre_suivi, type_lettre_suivi, reponse_date_lettre_suivi, statu_lettre_suivi) VALUES ('".$eleve_suivi_eleve_cpe."', 'suivi_eleve_cpe', '".$id_saisi."', '".$_SESSION['login']."', '".$date_fiche."', '".$heure_fiche."', '', '', '".$lettre_type."', '', 'en attente')";
	                             mysql_query($requete_courrier) or die('Erreur SQL !'.$requete_courrier.'<br />'.mysql_error());
				     $courrier_suivi_eleve_cpe = mysql_insert_id();
			      }
            } else {
                     // vérification = 3 - Tous les champs ne sont pas remplis
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

if ($action_sql === 'detacher_courrier')
 {
	$requete = "DELETE FROM ".$prefix_base."lettres_suivis WHERE id_lettre_suivi = '".$id_lettre_suivi."'";
	mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
 }

// requête liste des classes
if ( $classe_choix != "" )
{

	$requete_liste_classe = "SELECT id, classe, nom_complet FROM classes ORDER BY nom_complet DESC";

}
else
{

	$requete_liste_classe = "SELECT id, classe, nom_complet FROM classes ORDER BY nom_complet DESC";

}


/* ************************************************************ */
/* DEBUT - Gére la suppression d'une absence                    */
if ( $action_sql === 'supprimer_selection' )
{

	// librairie de fonction pour le module absence
	include "../lib/function_abs.php";

	// initialise les variables
	if (empty($_GET['id_absence_eleve']) and empty($_POST['id_absence_eleve'])) {$id_absence_eleve='';}
	    else { if (isset($_GET['id_absence_eleve'])) {$id_absence_eleve=$_GET['id_absence_eleve'];} if (isset($_POST['id_absence_eleve'])) {$id_absence_eleve=$_POST['id_absence_eleve'];} }
	if (empty($_GET['selection']) and empty($_POST['selection'])) {$selection='';}
	    else { if (isset($_GET['selection'])) {$selection=$_GET['selection'];} if (isset($_POST['selection'])) {$selection=$_POST['selection'];} }

	// supprime toutes les absences selectionnées, fait le ménage dans les courrier en liaison avec ces absences
	$action_php = supprime_id($id_absence_eleve, $prefix_base, 'absences_eleves', $selection);

}
/* FIN - Gére la suppression d'une absence                      */

/* ************************************************************ */

		$varcoche = ''; //variable des checkbox pour la fonction javascript

//**************** EN-TETE *****************
$titre_page = "Gestion des absences";
require_once("../../lib/header.inc");
//************** FIN EN-TETE ***************

//Configuration du calendrier
include("../../lib/calendrier/calendrier.class.php");
$cal = new Calendrier("form1", "du");

include "../lib/mincals_absences.inc";

?>
<script type="text/javascript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<script type="text/javascript">
<!--
function CocheCheckbox() {

    nbParams = CocheCheckbox.arguments.length;

    for (var i=0;i<nbParams-1;i++) {

        theElement = CocheCheckbox.arguments[i];
        formulaire = CocheCheckbox.arguments[nbParams-1];

        if (document.forms[formulaire].elements[theElement])
            document.forms[formulaire].elements[theElement].checked = true;
    }
}

function DecocheCheckbox() {

    nbParams = DecocheCheckbox.arguments.length;

    for (var i=0;i<nbParams-1;i++) {

        theElement = DecocheCheckbox.arguments[i];
        formulaire = DecocheCheckbox.arguments[nbParams-1];

        if (document.forms[formulaire].elements[theElement])
            document.forms[formulaire].elements[theElement].checked = false;
    }
}

//-->
</script>


<?php
// quelques variables
  $datej = date('Y-m-d');
  $annee_scolaire=annee_en_cours_t($datej);


?>
<?php /* La page gestion global des absences */ ?>
<p class="bold"><a href='<?php if($select_fiche_eleve=='' and $fiche_eleve=='' and $choix!='lemessager') { ?>../../accueil.php<?php } else { ?>gestion_absences.php<?php } ?>'><img src="../../images/icons/back.png" alt="Retour" title="Retour" class="back_link" />&nbsp;Retour</a> |
<a href="./impression_absences.php?type=<?php echo $type; ?>&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Impression</a> |
<a href="statistiques.php?year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Statistiques</a> |
<a href="gestion_absences.php?choix=lemessager&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Le messager</a> |
<a href="alert_suivi.php?choix=alert&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Système d'alerte</a>
</p>

  <form method="post" action="gestion_absences.php?type=<?php echo $type; ?>&amp;choix=<?php echo $choix; ?>" name="choix_type_vs" style="margin: auto; text-align: center;">
   <fieldset class="fieldset_efface">
     <input name="date_ce_jour" type="hidden" value="<?php echo $date_ce_jour; ?>" />
     <select name="type">
        <option value="A" onclick="javascript:document.choix_type_vs.submit()" <?php if ($type=="A") {echo "selected"; } ?>>Absences</option>
        <option value="R" onclick="javascript:document.choix_type_vs.submit()" <?php if ($type=="R") {echo "selected"; } ?>>Retards</option>
        <option value="I" onclick="javascript:document.choix_type_vs.submit()" <?php if ($type=="I") {echo "selected"; } ?>>Infirmerie</option>
        <option value="D" onclick="javascript:document.choix_type_vs.submit()" <?php if ($type=="D") {echo "selected"; } ?>>Dispenses</option>
     </select>
     <!--<input name="submit8" type="image" src="../../images/enabled.png" style="border: 0px;" />-->
		<?php /* <input type="submit" name="submit8" value="&lt;&lt;" />*/ ?>
     &nbsp; <a href="select.php?type=<?php echo $type; ?>&amp;classe_choix=<?php echo $classe_choix; ?>">Ajouter</a> - <a href="../lib/tableau.php?type=<?php echo $type; ?>&amp;pagedarriver=gestion_absences">Tableau</a>
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Voir la fiche élève <input type="text" name="fiche_eleve" value="" /><input name="submit" type="image" src="../../images/enabled.png" style="border: 0px;" />
		<?php /* <input type="submit" name="submit8" value="&lt;&lt;" /> */ ?>
   </fieldset>
  </form>
<div class="centre"><hr style="width: 550px;" /></div>
<div class="centre">
<?php if($type == "A" and $fiche_eleve == "") { ?>
[ <a href="gestion_absences.php?choix=top10&amp;type=<?php echo $type ?>">Top 10</a> | <a href="gestion_absences.php?choix=sm&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Absences sans motif</a> | <a href="gestion_absences.php?choix=am&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Absences avec motif</a> ]
<?php } if($type == "R" and $fiche_eleve == "") { ?>
[ <a href="gestion_absences.php?choix=top10&amp;type=<?php echo $type; ?>">Top 10</a> | <a href="gestion_absences.php?choix=sm&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Retards sans motif</a> | <a href="gestion_absences.php?choix=am&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Retards avec motif</a> ]
<?php } if($type == "I" and $fiche_eleve == "") { ?>
[ <a href="gestion_absences.php?choix=top10&amp;type=<?php echo $type; ?>">Top 10</a> | <a href="gestion_absences.php?choix=am&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Infirmerie avec motif</a> ]
<?php } if($type == "D" and $fiche_eleve == "") { ?>
[ <a href="gestion_absences.php?choix=top10&amp;type=<?php echo $type; ?>">Top 10</a> | <a href="gestion_absences.php?choix=sm&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Dispenses sans motif</a> | <a href="gestion_absences.php?choix=am&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Dispenses avec motif</a> ]
<?php } ?>
</div>

<?php
	// On crée l'affichage du top10 des absences
	if ($choix=="top10" and $fiche_eleve == "" and $select_fiche_eleve == "") {
		$i = 0;
		if ($type == "A" or $type == "I" or $type == "R" or $type == "D") {
			if ($classe_choix != "") {
				// sans choix de classe, c'est le top 10 de l'établissement
				$requete_top10 = "SELECT e.login, e.elenoet, e.nom, e.prenom, e.sexe, COUNT(DISTINCT(ae.id_absence_eleve)) AS count FROM ".$prefix_base."absences_eleves ae, ".$prefix_base."eleves e, ".$prefix_base."j_eleves_classes ec WHERE e.login = ae.eleve_absence_eleve AND ae.type_absence_eleve = '".$type."' AND ec.login = e.login AND ec.id_classe = '".$classe_choix."' GROUP BY e.login ORDER BY count DESC LIMIT 0, 10";
			}elseif ($classe_choix == "") {
				$requete_top10 = "SELECT e.login, e.elenoet, e.nom, e.prenom, e.sexe, COUNT(ae.id_absence_eleve) AS count FROM ".$prefix_base."absences_eleves ae, ".$prefix_base."eleves e WHERE ( e.login = ae.eleve_absence_eleve AND ae.type_absence_eleve = '".$type."' ) GROUP BY e.login ORDER BY count DESC LIMIT 0, 10";
			}
		}
		$execution_top10 = mysql_query($requete_top10)
			or die('Erreur SQL !'.$requete_top10.'<br />'.mysql_error());
		// On définit le margin_top pour la suite
			$margin_top = 50;
		while ( $data_top10 = mysql_fetch_array($execution_top10)) {
			$compte = $data_top10[5];

			echo '
				<div id="d'.$data_top10['login'].'" style="position: absolute; margin-left: 200px; margin-top: '.$margin_top.'px; z-index: 20; display: none; top: 0px; left: 0px;">
			';
			$margin_top = $margin_top + 23;
?>
          <table border="0" cellpadding="2" cellspacing="2" class="tableau_calque_information">
             <tr>
                <td class="texte_fondjaune_calque_information"><?php echo "<b>".strtoupper($data_top10['nom'])."</b> ".ucfirst($data_top10['prenom']); ?> élève de <?php echo "<b>".classe_de($data_top10['login'])."</b>"; $id_classe_eleve = classe_de($data_top10['login']); ?><br />
			<?php if ($type == "A") {?>Absence saisie : <?php } ?><?php if ($type == "R") {?>Retard saisi : <?php } ?><?php if ($type == "I") {?>Passage à l'infirmerie saisi : <?php } ?><?php if ($type == "D") {?>Dispense saisie : <?php } ?><b><?php echo $compte ?></b><br /></td>
                <?php if (getSettingValue("active_module_trombinoscopes")=='y') {
                 echo "<td style=\"width: 60px; vertical-align: top\">";
                 $nom_photo = '';
                 $nom_photo = nom_photo($data_top10['elenoet'],"eleves",2);
                 $photos = "../../photos/eleves/".$nom_photo;
                 //if ( $nom_photo === '' or !file_exists($photo) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                 if ( $nom_photo === '' or !file_exists($photos) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
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
          <td colspan="2" class="titre_tableau_gestion" nowrap><b>TOP 10</b></td>
        </tr>
        <?php
         $i = 0;
         $ic = 1;
         if ($type == "A" or $type == "I" or $type == "R" or $type == "D")
          {
            if ($classe_choix != "") { $requete_top10 = "SELECT e.login, e.elenoet, e.nom, e.prenom, e.sexe, COUNT(DISTINCT(ae.id_absence_eleve)) AS count FROM ".$prefix_base."absences_eleves ae, ".$prefix_base."eleves e, ".$prefix_base."j_eleves_classes ec WHERE e.login = ae.eleve_absence_eleve AND ae.type_absence_eleve = '".$type."' AND ec.login = e.login AND ec.id_classe = '".$classe_choix."' GROUP BY e.login ORDER BY count DESC LIMIT 0, 10"; }
            if ($classe_choix == "") { $requete_top10 = "SELECT e.login, e.elenoet, e.nom, e.prenom, e.sexe, COUNT(ae.id_absence_eleve) AS count FROM ".$prefix_base."absences_eleves ae, ".$prefix_base."eleves e WHERE ( e.login = ae.eleve_absence_eleve AND ae.type_absence_eleve = '".$type."' ) GROUP BY e.login ORDER BY count DESC LIMIT 0, 10"; }
          }
         $execution_top10 = mysql_query($requete_top10) or die('Erreur SQL !'.$requete_top10.'<br />'.mysql_error());
         while ( $data_top10 = mysql_fetch_array($execution_top10))
         {
                     if ($ic === '1') { $ic='2'; $couleur_cellule='td_tableau_absence_1'; } else { $couleur_cellule='td_tableau_absence_2'; $ic='1'; }
         ?>
        <tr>
          <td class="<?php echo $couleur_cellule; ?>" onmouseover="changementDisplay('d<?php echo $data_top10['login']; ?>', ''); return true;" onmouseout="changementDisplay('d<?php echo $data_top10['login']; ?>', ''); return true;"><a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_top10['login']; ?>" title="consulter la fiche de l'élève"><?php echo "<b>".strtoupper($data_top10['nom'])."</b> ".ucfirst($data_top10['prenom']); ?><a/></td>
          <td class="<?php echo $couleur_cellule; ?>">
            <?php if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo")) {
              $nom_photo = '';
              $nom_photo = nom_photo($data_top10['elenoet'],"eleves",2);
              $photos = "../../photos/eleves/".$nom_photo;
              //if ( $nom_photo === '' or !file_exists($photo) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
              if ( $nom_photo === '' or !file_exists($photos) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php
             } ?>
          </td>
        </tr>
    <?php } ?>
        <tr>
          <td class="class55bas">&nbsp;</td>
          <td class="class35bas">&nbsp;</td>
        </tr>
      </table>
    </td>
    <td style="text-align: center">
       <br />
       <form name="form1" method="post" action="gestion_absences.php?type=<?php echo $type; ?>&amp;choix=<?php echo $choix; ?>">
         <fieldset style="background-color: #D1EFE8; background-image: url(../images/2.png); ">
          <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
          Classe &nbsp;
          <select name="classe_choix">
            <option value="" selected="selected" onclick="javascript:document.form1.submit()">Toutes les classes</option>
                <?php
                  $resultat_liste_classe = mysql_query($requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysql_error());
                  while ( $data_liste_classe = mysql_fetch_array ($resultat_liste_classe)) {
                         if ($classe_choix==$data_liste_classe['id']) {$selected = "selected"; } else {$selected = ""; }?>
                        <option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>  onClick="javascript:document.form1.submit()"><?php echo $data_liste_classe['nom_complet']." (".$data_liste_classe['classe'].")"; ?></option>
                <?php } ?>
          </select>
           <?php if (getSettingValue("active_module_trombinoscopes")=='y')  { ?>
               <br />
               <input type="checkbox" name="photo" value="avec_photo" id="avecphoto" onClick="javascript:document.form1.submit()" <?php  if ($photo=="avec_photo") { ?>checked="checked"<?php } ?> /><label for="avecphoto" style="cursor: pointer;">Avec photo</label><br />
           <?php } ?>
               <br />
          TOP 10 des <?php if($type == "A") { ?>absences.<?php } if($type == "R") { ?>retards.<?php } if($type == "I") { ?>passages à l'infirmerie.<?php } if($type == "D") { ?>dispenses.<?php } ?>
	    </fieldset>
          </form>
    </td>
  </tr>
</table>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php
	} //fin du if ($choix=="top10" and $fiche_eleve == "" and $select_fiche_eleve == "")
?>

<?php
if ($choix=="sm" and $fiche_eleve == "" and $select_fiche_eleve == "") {
	$i = 0;
	if ($type == "A" or $type == "I" or $type == "R" or $type == "D") {
		if ($classe_choix != "") {
			$requete_sans_motif = "SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.sexe, ae.id_absence_eleve, ae.saisie_absence_eleve,
										ae.eleve_absence_eleve, ae.justify_absence_eleve, ae.info_justify_absence_eleve, ae.type_absence_eleve,
										ae.motif_absence_eleve, ae.d_date_absence_eleve, ae.a_date_absence_eleve, ae.d_heure_absence_eleve,
										ae.a_heure_absence_eleve, jec.login, jec.id_classe, jec.periode, jer.regime, c.classe, c.id, c.nom_complet
									FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae
									WHERE ( e.login = ae.eleve_absence_eleve AND e.login = jec.login AND e.login = jer.login  AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve != 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve";
		} elseif ($classe_choix == "") {
			$requete_sans_motif = "SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.sexe, ae.id_absence_eleve, ae.saisie_absence_eleve,
										ae.eleve_absence_eleve, ae.justify_absence_eleve, ae.info_justify_absence_eleve, ae.type_absence_eleve,
										ae.motif_absence_eleve, ae.d_date_absence_eleve, ae.a_date_absence_eleve, ae.d_heure_absence_eleve,
										ae.a_heure_absence_eleve, jer.regime FROM ".$prefix_base."eleves e, ".$prefix_base."absences_eleves ae, ".$prefix_base."j_eleves_regime jer
									WHERE ( e.login = ae.eleve_absence_eleve AND e.login = jer.login AND ae.justify_absence_eleve != 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve ORDER BY nom,prenom,d_heure_absence_eleve";
		}
	}
	$execution_sans_motif = mysql_query($requete_sans_motif) or die('Erreur SQL !'.$requete_sans_motif.'<br />'.mysql_error());
	// Pour la position du premier div, on définit le margin-top
	$margin_top = 20;
	while ( $data_sans_motif = mysql_fetch_array($execution_sans_motif))
	{
		if (in_array($data_sans_motif['eleve_absence_eleve'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {
		// DEBUT DE GESTION DU CALQUE D'INFORMATION
		echo '
				<div id="d'.$data_sans_motif['id_absence_eleve'].'" style="position: absolute; margin-left: 200px; margin-top: '.$margin_top.'px; z-index: 20; display: none; top: 0px; left: 0px;">
				';
		$margin_top = $margin_top + 23;
?>
          <table border="0" cellpadding="2" cellspacing="2" class="tableau_calque_information">
             <tr>
                <td class="texte_fondjaune_calque_information"><?php echo "<b>".strtoupper($data_sans_motif['nom'])."</b> ".ucfirst($data_sans_motif['prenom']); ?> élève de <?php echo "<b>".classe_de($data_sans_motif['login'])."</b>";  $id_classe_eleve = classe_de($data_sans_motif['login']); ?><br /><?php if ($data_sans_motif['type_absence_eleve']=="A") { ?> a été absent<?php if ($data_sans_motif['sexe'] == "F") { ?>e<?php } } if  ($data_sans_motif['type_absence_eleve']=="R") { ?> est arrivé<?php if ($data_sans_motif['sexe'] == "F") { ?>e<?php } ?> en retard<?php } ?><?php if ($data_sans_motif['type_absence_eleve']=="I") { ?>est allé à l'infirmerie<?php } ?><br /> le <?php echo date_frl($data_sans_motif['d_date_absence_eleve']); ?><?php if (($data_sans_motif['a_date_absence_eleve'] != $data_sans_motif['d_date_absence_eleve'] and $data_sans_motif['a_date_absence_eleve'] != "") or $data_sans_motif['a_date_absence_eleve'] == "0000-00-00") { ?> au <?php echo date_frl($data_sans_motif['a_date_absence_eleve']); ?><?php } ?><br /><?php if ($data_sans_motif['a_heure_absence_eleve'] == "00:00:00" or $data_sans_motif['a_heure_absence_eleve'] == "") { ?>à <?php } else { ?>de <?php } ?><?php echo heure($data_sans_motif['d_heure_absence_eleve']); ?> <?php if ($data_sans_motif['a_heure_absence_eleve'] == "00:00:00" or $data_sans_motif['a_heure_absence_eleve'] == "") { } else { echo 'à '.heure($data_sans_motif['a_heure_absence_eleve']); } ?></td>
                 <?php if (getSettingValue("active_module_trombinoscopes")=='y') {
                 echo "<td style=\"width: 60px; vertical-align: top\" rowspan=\"4\">";
                 $nom_photo = '';
                 $nom_photo = nom_photo($data_sans_motif['elenoet'],"eleves",2);
                 $photos = "../../photos/eleves/".$nom_photo;
                 //if ( $nom_photo === '' or !file_exists($photo) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                 if ( $nom_photo === '' or !file_exists($photos) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
				$valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php
                 } ?>
             </tr>
             <tr>
                <td class="norme_absence"><?php if($data_sans_motif['saisie_absence_eleve']!="") { ?>Enregistré par : <?php echo qui($data_sans_motif['saisie_absence_eleve']); } ?><br />pour le motif : <?php echo motif_de($data_sans_motif['motif_absence_eleve']); ?></td>
             </tr>
             <tr>
                <td class="norme_absence"><?php if ($data_sans_motif['justify_absence_eleve'] == "O") {?><span class="norme_absence_vert"><b>a donn&eacute; pour justification : </b>
									<?php } elseif ($data_sans_motif['justify_absence_eleve'] == "T") { ?><span class="norme_absence_vert" style="color: orange;"><b>a justifi&eacute; par t&eacute;l&eacute;phone : </b>
																						<?php } else { ?><span class="norme_absence_rouge"><b>n'a pas donn&eacute; de justification</b>
																						<?php } ?></span></td>
             </tr>
             <tr>
                <td class="norme_absence"><?php if(!empty($data_sans_motif['info_justify_absence_eleve'])) { ?><blockquote><?php echo $data_sans_motif['info_justify_absence_eleve']; ?></blockquote><?php } ?></td>
             </tr>
             <tr class="texte_fondjaune_calque_information">
                <td colspan="2">
                <?php

				// gestion de l'affichage des numéro de téléphone
				$info_responsable = tel_responsable($data_sans_motif['ele_id']);

				$telephone = ''; $telephone_pers = ''; $telephone_prof = ''; $telephone_port = '';

				if ( !empty($info_responsable) )
				{

					if ( $info_responsable[0]['tel_pers'] != '' ) { $telephone_pers = '<br />Pers. <strong>'.present_tel($info_responsable[0]['tel_pers']).'</strong> '; }
					if ( $info_responsable[0]['tel_prof'] != ''  ) { $telephone_prof = '<br />Prof. <strong>'.present_tel($info_responsable[0]['tel_prof']).'</strong> '; }
					if ( $info_responsable[0]['tel_port'] != ''  ) { $telephone_port = '<br />Port.<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="14" width="14" /> '.present_tel($info_responsable[0]['tel_port']); }

				}

				if ( $telephone_pers != '' and $telephone_prof === '' ) { $telephone = $telephone_pers; }
				if ( $telephone_pers === '' and $telephone_prof != '' ) { $telephone = $telephone_prof; }
				if ( $telephone_pers != '' and $telephone_prof != '' ) { $telephone = $telephone_pers . ' ' . $telephone_prof; }
				if ( $telephone_pers === '' and $telephone_prof === '' and $telephone_port != '' ) { $telephone = $telephone_port . ' ! surtaxe'; }

				if ( $telephone_pers != '' or $telephone_prof != '' or $telephone_port != '' ) { $telephone = 'Téléphone responsable : '.$telephone; }
				else { $telephone = 'Aucun numéro de téléphone disponible'; }

				echo $telephone;

		  		?>
                </td>
              </tr>
           </table>
     </div>
<?php /* FIN DE GESTION DU CALQUE D'INFORMATION */
		}
	} ?>


<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<table style="margin: auto; width: 800px; border: 0 0 0 0;">
  <tr style="vertical-align: top">
    <td class="td_tableau_gestion">
      <br />
      <form name ="form3" method="post" action="gestion_absences.php?choix=<?php echo $choix; ?>&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>&amp;action_sql=supprimer_selection">
       <fieldset class="fieldset_efface">
        <table class="td_tableau_gestion" style="margin: auto; width: 500px; border: 0 0 0 0;">
          <tr>
            <td colspan="2" class="titre_tableau_gestion"><b><?php if ($type=="A") { ?>Absences sans motif<?php } ?><?php if ($type=="R") { ?>Retards sans motif<?php } ?><?php if ($type=="I") { ?>Infirmerie sans motif<?php } ?><?php if ($type=="D") { ?>Dispenses sans motif<?php } ?></b></td>
          </tr>
          <?php
         $total = 0;
         $i = 0;
         $ic = 1;
           $execution_sans_motif = mysql_query($requete_sans_motif) or die('Erreur SQL !'.$requete_sans_motif.'<br />'.mysql_error());
		while ( $data_sans_motif = mysql_fetch_array($execution_sans_motif))
		{
			if (in_array($data_sans_motif['eleve_absence_eleve'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {

			if ($ic==1)
			{
				$ic=2;
				$couleur_cellule="td_tableau_absence_1";
			} else {
				$couleur_cellule="td_tableau_absence_2";
				$ic=1;
			}
           ?>
          <tr>
            <td class="<?php echo $couleur_cellule; ?>" onmouseover="changementDisplay('d<?php echo $data_sans_motif['id_absence_eleve']; ?>', ''); return true;" onmouseout="changementDisplay('d<?php echo $data_sans_motif['id_absence_eleve']; ?>', ''); return true;"><input name="selection[<?php echo $total; ?>]" id="sel<?php echo $total; ?>" type="checkbox" value="1" <?php $varcoche = $varcoche."'sel".$total."',"; ?><?php /* if((isset($selection[$total]) and $selection[$total] == "1") OR $cocher == 1) { ?>checked="checked"<?php } */ ?> />
            	<input name="id_absence_eleve[<?php echo $total; ?>]" type="hidden" value="<?php echo $data_sans_motif['id_absence_eleve']; ?>" />
            	<?php

            	$cpt_lettre_absence_recus = lettre_absence_envoye($data_sans_motif['id_absence_eleve']);
            	if ( $cpt_lettre_absence_recus != 0 )
            	{

					$info_sup = 'du '.date_fr($data_sans_motif['d_date_absence_eleve']).' au '.date_fr($data_sans_motif['a_date_absence_eleve']);
					?><a href="#" onClick="alert('Pour le supprimer, supprimer la date d\'envoye du courrier.'); return false;"><img src="../../images/icons/delete_imp.png" style="width: 16px; height: 16px;" title="Impossible de supprimer <?php if($data_sans_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_sans_motif['type_absence_eleve']=="D") { ?>la dispence<?php } if ($data_sans_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?> <?php echo $info_sup; ?>" border="0" alt="" /></a><?php

				}
				else
				{

            		?><a href="ajout_<?php if($data_sans_motif['type_absence_eleve']=="A") { ?>abs<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>ret<?php } if($data_sans_motif['type_absence_eleve']=="D") { ?>dip<?php } if($data_sans_motif['type_absence_eleve']=="I") { ?>inf<?php } ?>.php?action=supprimer&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_sans_motif['id_absence_eleve']; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>" <?php $info_sup = 'du '.date_fr($data_sans_motif['d_date_absence_eleve']).' au '.date_fr($data_sans_motif['a_date_absence_eleve']); ?>onClick="return confirm('Etes-vous sur de vouloir le supprimer <?php if($data_avec_motif['type_absence_eleve']=="A") { ?>l\'absence<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_avec_motif['type_absence_eleve']=="D") { ?>la dispense<?php } if ($data_avec_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?> <?php echo $info_sup; ?>.')"><img src="../../images/icons/delete.png" style="width: 16px; height: 16px;" title="supprimer <?php if($data_sans_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_sans_motif['type_absence_eleve']=="D") { ?>la dispense<?php } if ($data_sans_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?> <?php echo $info_sup; ?>" border="0" alt="" /></a><?php


				} ?>
            	<a href="ajout_<?php if($data_sans_motif['type_absence_eleve']=="A") { ?>abs<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>ret<?php } if($data_sans_motif['type_absence_eleve']=="I") { ?>inf<?php } if($data_sans_motif['type_absence_eleve']=="D") { ?>dip<?php } ?>.php?action=modifier&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_sans_motif['id_absence_eleve']; ?>&amp;mode=eleve"><img src="../../images/icons/saisie.png" style="width: 16px; height: 16px;" title="modifier <?php if($data_sans_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_sans_motif['type_absence_eleve']=="D") { ?>la dispence<?php } if ($data_sans_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?>" border="0" alt="" /></a>
            	<a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_sans_motif['login']; ?>" title="consulter la fiche de l'élève"><?php echo "<b>".strtoupper($data_sans_motif['nom'])."</b> ".ucfirst($data_sans_motif['prenom'])." (".$data_sans_motif['regime'].")"; ?></a>
            	</td>
            <td class="<?php echo $couleur_cellule; ?>">

              <?php if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo")) {
              	  $nom_photo = '';
                  $nom_photo = nom_photo($data_sans_motif['elenoet'],"eleves",2);
                  $photos = "../../photos/eleves/".$nom_photo;
                  //if ( $nom_photo === '' or !file_exists($photo) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                  if ( $nom_photo === '' or !file_exists($photos) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 		$valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td>
              <?php } ?>
            </td>
          </tr>
      	<?php $total = $total + 1;
      		}
		} ?>
          <tr>
            <td class="class55bas">&nbsp;</td>
            <td class="class35bas">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">
		<?php /* <a href="gestion_absences.php?choix=<?php echo $choix; ?>&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>&amp;cocher=<?php if($cocher==1) { ?>0<?php } else { ?>1<?php  } ?>"><?php if($cocher==1) { ?>déc<?php } else { ?>C<?php  } ?>ocher toutes les cellules</a> */ ?>
		<?php $varcoche = $varcoche."'form3'"; ?>
		<a href="javascript:CocheCheckbox(<?php echo $varcoche; ?>)">Cocher</a> | <a href="javascript:DecocheCheckbox(<?php echo $varcoche; ?>)">Décocher</a>	<input name="date_ce_jour" type="hidden" value="<?php echo $date_ce_jour; ?>" /><input name="submit2" type="image" src="../../images/delete16.png" title="supprimer la s&eacute;lection rapidement" style="border: 0px;" onClick="return confirm('Etes-vous sur de vouloir le supprimer...')" />
	    </td>
          </tr>
        </table>
        <?php /* <input name="submit2" type="submit" value="supprimer la s&eacute;lection" onClick="return confirm('Etes-vous sur de vouloir le supprimer...')" /> */ ?>
         <input value="<?php echo $year; ?>" name="year" id="year1" type="hidden" />
         <input value="<?php echo $month; ?>" name="month" id="month1" type="hidden" />
         <input value="<?php echo $day; ?>" name="day" id="day1" type="hidden" />
       </fieldset>
      </form>
    </td>
    <td style="text-align: center"><br />
          <form name ="form1" method="post" action="gestion_absences.php?type=<?php echo $type; ?>&amp;choix=<?php echo $choix; ?>">
          <fieldset style="background-color: #D1EFE8; background-image: url(../images/2.png); ">
            <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
          <?php //Affiche le calendrier
                minicals($year, $month, $day, $classe_choix, $type, 'gestion_absences');
          ?>
          Informations données pour la date du<br /><b><?php echo date_frl($date_ce_jour); ?></b><br /><br />
          Classe &nbsp;
          <select name="classe_choix">
            <option value="" selected onclick="javascript:document.form1.submit()">Toutes les classes</option>
                <?php
                  $resultat_liste_classe = mysql_query($requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysql_error());
                  while($data_liste_classe = mysql_fetch_array ($resultat_liste_classe)) {
                         if ($classe_choix==$data_liste_classe['id']) {$selected = "selected"; } else {$selected = ""; }?>
                        <option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>  onClick="javascript:document.form1.submit()"><?php echo $data_liste_classe['nom_complet']." (".$data_liste_classe['classe'].")"; ?></option>
                <?php } ?>
          </select><noscript><input value=">>" name="date" type="submit" /></noscript><br />
          <input value="<?php echo $year; ?>" name="year" id="year2" type="hidden" />
          <input value="<?php echo $month; ?>" name="month" id="month2" type="hidden" />
          <input value="<?php echo $day; ?>" name="day" id="day2" type="hidden" />
          <?php if (getSettingValue("active_module_trombinoscopes")=='y')  { ?>
              <input type="checkbox" name="photo" value="avec_photo" id="avecphoto" onClick="javascript:document.form1.submit()"   <?php  if ($photo=="avec_photo") { ?>checked="checked"<?php } ?> /><label for="avecphoto" style="cursor: pointer;">Avec photo</label><br /><br />
          <?php } ?>
          Pour voir, toutes les <?php if($type == "A") { ?>absences<?php } if($type == "R") { ?>Retards<?php } if($type == "I") { ?>Infirmerie<?php } if($type == "D") { ?>Dispenses<?php } ?> n'ayant pas eu de justificatif, veuillez cocher la case ci-dessous.<br />
            <input type="checkbox" name="choix" id="voirabssansjust" value="sma" onClick="javascript:document.form1.submit()" <?php  if ($choix=="sma") { ?>checked="checked"<?php } ?> />
            <label for="voirabssansjust" style="cursor: pointer;"><?php if($type == "A") { ?>Absence<?php } if($type == "R") { ?>Retard<?php } if($type == "I") { ?>Infirmerie<?php } if($type == "D") { ?>Dispense<?php } ?> sans justification</label>
          </fieldset>
          </form>
    </td>
  </tr>
</table>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php
	}

//
// ABSENCES SANS MOTIF
//

if ($choix=="sma" and $fiche_eleve == "" and $select_fiche_eleve == "") {

       $i = 0;
       if ($type == "A" or $type == "I" or $type == "R" or $type == "D")
         {
           if ($classe_choix != "") { $requete_sans_motif ="SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.sexe, jer.regime, ae.id_absence_eleve, ae.eleve_absence_eleve, ae.justify_absence_eleve, ae.type_absence_eleve, ae.motif_absence_eleve, ae.info_justify_absence_eleve, ae.d_heure_absence_eleve, ae.a_heure_absence_eleve, jec.login, jec.id_classe, jec.periode, c.classe, c.id, c.nom_complet FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae WHERE e.login = ae.eleve_absence_eleve AND e.login = jer.login AND e.login = jec.login AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve != 'O' AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve"; }
           if ($classe_choix == "") { $requete_sans_motif ="SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.sexe, jer.regime, ae.id_absence_eleve, ae.eleve_absence_eleve, ae.justify_absence_eleve, ae.type_absence_eleve, ae.motif_absence_eleve, ae.info_justify_absence_eleve, ae.d_date_absence_eleve, ae.a_date_absence_eleve, ae.d_heure_absence_eleve, ae.a_heure_absence_eleve FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."absences_eleves ae WHERE ( e.login = ae.eleve_absence_eleve AND e.login = jer.login AND ae.justify_absence_eleve!='O' AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve ORDER BY nom,prenom,d_heure_absence_eleve"; }
         }

		$execution_sans_motif = mysql_query($requete_sans_motif)
			or die('Erreur SQL !'.$requete_sans_motif.'<br />'.mysql_error());
		// Pour la position du premier div, on définit le margin-top
			$margin_top = 50;
		while($data_sans_motif = mysql_fetch_array($execution_sans_motif))
		{
			if (in_array($data_sans_motif['eleve_absence_eleve'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {

			if($type == "A" or $type == "I" or $type == "R" or $type == "D") {
				echo '
					<div id="d'.$data_sans_motif['id_absence_eleve'].'" style="position: absolute; margin-left: 200px; margin-top: '.$margin_top.'px; z-index: 20; display: none; top: 0px; left: 0px;">
				';
				$margin_top = $margin_top + 23;
?>
         <table border="0" cellpadding="2" cellspacing="2" class="tableau_calque_information">
            <tr>
               <td class="texte_fondjaune_calque_information"><?php echo "<b>".$data_sans_motif['nom']."</b> ".$data_sans_motif['prenom']; ?> élève de <?php echo "<b>".classe_de($data_sans_motif['login'])."</b>"; $id_classe_eleve = classe_de($data_sans_motif['login']); ?><br /><?php if ($data_sans_motif['type_absence_eleve']=="A") { ?> a été absent<?php if ($data_sans_motif['sexe'] == "F") { ?>e<?php } } if  ($data_sans_motif['type_absence_eleve']=="R") { ?> est arrivé<?php if ($data_sans_motif['sexe'] == "F") { ?>e<?php } ?> en retard<?php } ?><?php if ($data_sans_motif['type_absence_eleve']=="I") { ?>est allé à l'infirmerie<?php } ?><br /> le <?php echo date_frl($data_sans_motif['d_date_absence_eleve']); ?><?php if (($data_sans_motif['a_date_absence_eleve'] != $data_sans_motif['d_date_absence_eleve'] and $data_sans_motif['a_date_absence_eleve'] != "") or $data_sans_motif['a_date_absence_eleve'] == "0000-00-00") { ?> au <?php echo date_frl($data_sans_motif['a_date_absence_eleve']); ?><?php } ?><br /><?php if ($data_sans_motif['a_heure_absence_eleve'] == "" or $data_sans_motif['a_heure_absence_eleve'] == "00:00:00") { ?>à <?php } else { ?>de <?php } ?><?php echo heure($data_sans_motif['d_heure_absence_eleve']); ?> <?php if ($data_sans_motif['a_heure_absence_eleve'] == "00:00:00" or $data_sans_motif['a_heure_absence_eleve'] == "") { } else { echo 'à '.heure($data_sans_motif['a_heure_absence_eleve']); } ?></td>
               <?php if (getSettingValue("active_module_trombinoscopes")=='y') {
                 echo "<td style=\"width: 60px; vertical-align: top\" rowspan=\"4\">";
                 $nom_photo = '';
                 $nom_photo = nom_photo($data_sans_motif['elenoet'],"eleves",2);
                 $photos = "../../photos/eleves/".$nom_photo;
                 //if ( $nom_photo === '' or !file_exists($photo) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                 if ( $nom_photo === '' or !file_exists($photos) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php                 } ?>
            </tr>
            <tr>
               <td class="norme_absence">pour le motif : <?php echo motif_de($data_sans_motif['motif_absence_eleve']); ?></td>
             </tr>
             <tr>
                <td class="norme_absence"><?php if ($data_sans_motif['justify_absence_eleve'] == "O") {?><span class="norme_absence_vert"><b>a donn&eacute; pour justification : </b>
										<? } elseif($data_sans_motif['justify_absence_eleve'] == "T") { ?><span class="norme_absence_vert" style="color: orange;"><b>a justifi&eacute; par t&eacute;l&eacute;phone : </b>
																						<?php } else { ?><span class="norme_absence_rouge"><b>n'a pas donn&eacute; de justification</b>
																							<?php }?></span></td>
             </tr>
             <tr>
                <td class="norme_absence"><?php if(!empty($data_sans_motif['info_justify_absence_eleve'])) { ?><blockquote><?php echo $data_sans_motif['info_justify_absence_eleve']; ?></blockquote><?php } ?></td>
             </tr>
             <tr class="texte_fondjaune_calque_information">
                <td colspan="3">
                <?php

				// affichage des numéro de téléphone
				$info_responsable = tel_responsable($data_sans_motif['ele_id']);

				$telephone = ''; $telephone_pers = ''; $telephone_prof = ''; $telephone_port = '';

				if ( !empty($info_responsable) )
				{

					if ( $info_responsable[0]['tel_pers'] != '' ) { $telephone_pers = '<br />Pers. <strong>'.present_tel($info_responsable[0]['tel_pers']).'</strong> '; }
					if ( $info_responsable[0]['tel_prof'] != ''  ) { $telephone_prof = '<br />Prof. <strong>'.present_tel($info_responsable[0]['tel_prof']).'</strong> '; }
					if ( $info_responsable[0]['tel_port'] != ''  ) { $telephone_port = '<br />Port.<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="14" width="14" /> '.present_tel($info_responsable[0]['tel_port']); }

				}

				if ( $telephone_pers != '' and $telephone_prof === '' ) { $telephone = $telephone_pers; }
				if ( $telephone_pers === '' and $telephone_prof != '' ) { $telephone = $telephone_prof; }
				if ( $telephone_pers != '' and $telephone_prof != '' ) { $telephone = $telephone_pers . ' ' . $telephone_prof; }
				if ( $telephone_pers === '' and $telephone_prof === '' and $telephone_port != '' ) { $telephone = $telephone_port . ' ! surtaxe'; }

				if ( $telephone_pers != '' or $telephone_prof != '' or $telephone_port != '' ) { $telephone = 'Téléphone responsable : '.$telephone; }
				else { $telephone = 'Aucun numéro de téléphone disponible'; }

				echo $telephone;

		  		?>
                </td>
              </tr>
          </table>
       </div>
<?php		}
		}
	} ?>


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
          <td colspan="3" class="titre_tableau_gestion" nowrap><b><?php if ($type=="A") { ?>Absences sans motif<?php } ?><?php if ($type=="R") { ?>Retards sans motif<?php } ?><?php if ($type=="I") { ?>Infirmerie sans motif<?php } ?><?php if ($type=="D") { ?>Dispenses sans motif<?php } ?></b></td>
        </tr>
        <?php
         $total = 0;
         $i = 0;
         $ic = 1;
         $execution_sans_motif = mysql_query($requete_sans_motif) or die('Erreur SQL !'.$requete_sans_motif.'<br />'.mysql_error());
         while ( $data_sans_motif = mysql_fetch_array($execution_sans_motif))
         {
			if (in_array($data_sans_motif['eleve_absence_eleve'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {
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
          <td class="<?php echo $couleur_cellule; ?>" onmouseover="changementDisplay('d<?php echo $data_sans_motif['id_absence_eleve']; ?>', ''); return true;" onmouseout="changementDisplay('d<?php echo $data_sans_motif['id_absence_eleve']; ?>', ''); return true;">
          	<input name="selection[<?php echo $total; ?>]" id="sel<?php echo $total; ?>" type="checkbox" value="1" <?php $varcoche = $varcoche."'sel".$total."',"; ?> <?php /* if((isset($selection[$total]) and $selection[$total]) == "1" OR $cocher == 1) { ?>checked="checked"<?php } */ ?> />
          	<input name="id_absence_eleve[<?php echo $total; ?>]" type="hidden" value="<?php echo $data_sans_motif['id_absence_eleve']; ?>" /><a href="ajout_<?php if($data_sans_motif['type_absence_eleve']=="A") { ?>abs<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>ret<?php } if($data_sans_motif['type_absence_eleve']=="D") { ?>dip<?php } if($data_sans_motif['type_absence_eleve']=="I") { ?>inf<?php } ?>.php?action=supprimer&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_sans_motif['id_absence_eleve']; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>" onClick="return confirm('Etes-vous sur de vouloir le supprimer...')"><img src="../../images/icons/delete.png" style="width: 16px; height: 16px;" title="supprimer l'absence" border="0" alt="" /></a>
          	<a href="ajout_<?php if($data_sans_motif['type_absence_eleve']=="A") { ?>abs<?php } if($data_sans_motif['type_absence_eleve']=="D") { ?>dip<?php } if($data_sans_motif['type_absence_eleve']=="I") { ?>inf<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>ret<?php } ?>.php?action=modifier&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_sans_motif['id_absence_eleve']; ?>&amp;mode=eleve"><img src="../../images/icons/saisie.png" title="modifier l'absence" style="width: 16px; height: 16px;" border="0" alt="" /></a>
          	<a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_sans_motif['login']; ?>" title="consulter la fiche de l'élève"><?php echo "<b>".strtoupper($data_sans_motif['nom'])."</b> ".ucfirst($data_sans_motif['prenom']) . " (" . $data_sans_motif['regime'] . ")"; ?></a>
          </td>
          <td class="<?php echo $couleur_cellule; ?>">
            <?php
				if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo"))
				{
					$id_eleve = $data_sans_motif['id_absence_eleve'];
                    $id_eleve_photo = $data_sans_motif['elenoet'];
					$nom_photo = '';
                    $nom_photo = nom_photo($id_eleve_photo,"eleves",2);
                    $photos = "../../photos/eleves/".$nom_photo;

					if ( $nom_photo === '' or !file_exists($photos) ) {
						$photos = "../../mod_trombinoscopes/images/trombivide.jpg";
					}
		 			$valeur = redimensionne_image($photos);
            ?>
				<img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td>
            <?php } ?>
          </td>
        </tr>
    <?php 	$total = $total + 1;
			}
		} ?>
        <tr>
          <td class="class10">&nbsp;</td>
          <td class="class55bas">&nbsp;</td>
          <td class="class35bas">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2">
		<?php /* <a href="gestion_absences.php?choix=<?php echo $choix; ?>&amp;date_ce_jour=<?php echo date_fr($date_ce_jour); ?>&amp;cocher=<?php if($cocher==1) { ?>0<?php } else { ?>1<?php  } ?>"><?php if($cocher==1) { ?>déc<?php } else { ?>C<?php  } ?>ocher toutes les cellules</a> */ ?>
		<?php $varcoche = $varcoche."'form3'"; ?>
		<a href="javascript:CocheCheckbox(<?php echo $varcoche; ?>)">Cocher</a> | <a href="javascript:DecocheCheckbox(<?php echo $varcoche; ?>)">Décocher</a>	<input name="date_ce_jour" type="hidden" value="<?php echo $date_ce_jour; ?>" /><input name="submit2" type="image" src="../../images/delete16.png" title="supprimer la s&eacute;lection rapide" style="border: 0px;" onClick="return confirm('Etes-vous sur de vouloir le supprimer...')" />
	  </td>
        </tr>
      </table>
        <?php /* <input name="submit2" type="submit" value="supprimer la s&eacute;lection" onClick="return confirm('Etes-vous sur de vouloir le supprimer...')" /> */ ?>
        <input value="<?php echo $year; ?>" name="year" id="year3" type="hidden" />
        <input value="<?php echo $month; ?>" name="month" id="month3" type="hidden" />
        <input value="<?php echo $day; ?>" name="day" id="day3" type="hidden" />
      </fieldset>
     </form>
    </td>
    <td style="text-align: center">
          <br />
          <form name="form1" method="post" action="gestion_absences.php?type=<?php echo $type; ?>">
          <fieldset style="background-color: #D1EFE8; background-image: url(../images/2.png); ">
            <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
          Classe
          <select name="classe_choix">
            <option value="" selected onClick="javascript:document.form1.submit()">Toutes les classes</option>
                <?php
                  $resultat_liste_classe = mysql_query($requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysql_error());
                  while ( $data_liste_classe = mysql_fetch_array ($resultat_liste_classe)) {
                         if ($classe_choix==$data_liste_classe['id']) {$selected = "selected"; } else {$selected = ""; }?>
                        <option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>  onclick="javascript:document.form1.submit()"><?php echo $data_liste_classe['nom_complet']." (".$data_liste_classe['classe'].")"; ?></option>
                <?php } ?>
          </select><br />
          <?php if (getSettingValue("active_module_trombinoscopes")=='y') { ?>
          <input type="checkbox" name="photo" id="avecphoto" value="avec_photo" onClick="javascript:document.form1.submit()" <?php  if ($photo=="avec_photo") { ?>checked="checked"<?php } ?> /><label for="avecphoto" style="cursor: pointer;">Avec photo</label><br /><br />
          <?php } ?>
          Visualiser par date, veuillez décocher la case ci-dessous.<br />
            <input type="checkbox" name="choix" id="voirabssansjust" value="sma" onClick="javascript:document.form1.submit()" <?php  if ($choix=="sma") { ?>checked="checked"<?php } ?> />
            <label for="voirabssansjust" style="cursor: pointer;"><?php if($type == "A") { ?>Absence<?php } if($type == "R") { ?>Retard<?php } if($type == "I") { ?>Infirmerie<?php } if($type == "D") { ?>Dispense<?php } ?> sans justification</label>
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
		if ($type == "A" or $type == "I" or $type == "R" or $type == "D") {
			if ($classe_choix != "") {
				$requete_avec_motif = "SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.elenoet, e.sexe, jer.regime, ae.id_absence_eleve, ae.eleve_absence_eleve, ae.justify_absence_eleve,ae.type_absence_eleve, ae.motif_absence_eleve, ae.info_justify_absence_eleve, ae.d_date_absence_eleve, ae.d_heure_absence_eleve, ae.a_heure_absence_eleve, ae.a_date_absence_eleve, jec.login, jec.id_classe, jec.periode, c.id, c.classe, c.nom_complet FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae WHERE ( e.login = ae.eleve_absence_eleve AND e.login = jer.login AND e.login = jec.login AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve = 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."' ) AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve";
			}
			if ($classe_choix == "") {
				$requete_avec_motif = "SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.elenoet, e.sexe, jer.regime, ae.id_absence_eleve, ae.eleve_absence_eleve, ae.justify_absence_eleve,ae.type_absence_eleve, ae.motif_absence_eleve, ae.info_justify_absence_eleve, ae.d_date_absence_eleve, ae.d_heure_absence_eleve, ae.a_heure_absence_eleve, ae.a_date_absence_eleve FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."absences_eleves ae WHERE ( e.login = ae.eleve_absence_eleve AND e.login = jer.login AND ae.justify_absence_eleve = 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve ORDER BY nom,prenom,d_heure_absence_eleve";
			}
        }
		$execution_avec_motif = mysql_query($requete_avec_motif)
		 	or die('Erreur SQL !'.$requete_avec_motif.'<br />'.mysql_error());
		// On construit alors le div de la fiche élève
		// Pour la position du premier div, on définit le margin-top
			$margin_top = 50;
		while ( $data_avec_motif = mysql_fetch_array($execution_avec_motif)) {

			if($type == "A" or $type == "I" or $type == "R" or $type == "D") {
				echo '
					<div id="d'.$data_avec_motif['id_absence_eleve'].'" style="position: absolute; margin-left: 200px; margin-top: '.$margin_top.'px; z-index: 20; display: none; top: 0px; left: 0px;">
				';
				$margin_top = $margin_top + 23;
	?>
		<table border="0" cellpadding="2" cellspacing="2" class="tableau_calque_information">
			<tr>
				<td class="texte_fondjaune_calque_information">
<?php
			echo "
				<b>".strtoupper($data_avec_motif['nom'])."</b> ".ucfirst($data_avec_motif['prenom']); ?> élève de <?php echo "<b>".classe_de($data_avec_motif['login'])."</b>"; $id_classe_eleve = classe_de($data_avec_motif['login']); ?>
				<br />
				<?php if ($data_avec_motif['type_absence_eleve']=="A") { ?> &agrave; &eacute;t&eacute; absent<?php if ($data_avec_motif['sexe'] == "F") { ?>e<?php } } if  ($data_avec_motif['type_absence_eleve']=="R") { ?> est arrivé<?php if ($data_avec_motif['sexe'] == "F") { ?>e<?php } ?> en retard<?php } ?><?php if ($data_avec_motif['type_absence_eleve']=="I") { ?>est allé à l'infirmerie<?php } ?><br /> le <?php echo date_frl($data_avec_motif['d_date_absence_eleve']); ?><?php if (($data_avec_motif['a_date_absence_eleve'] != $data_avec_motif['d_date_absence_eleve'] and $data_avec_motif['a_date_absence_eleve'] != "") or $data_avec_motif['a_date_absence_eleve'] == "0000-00-00") { ?> au <?php echo date_frl($data_avec_motif['a_date_absence_eleve']); ?><?php } ?>
				<br />

<?php
				if (getSettingValue("active_module_trombinoscopes")=='y') {
					echo "<td style=\"width: 60px; vertical-align: top\" rowspan=\"4\">";
					$nom_photo = '';
					$nom_photo = nom_photo($data_avec_motif['elenoet'],"eleves",2);
          			$photos = "../../photos/eleves/".$nom_photo;
					//if ( $nom_photo === '' or !file_exists($photo) ) {
					if ( $nom_photo === '' or !file_exists($photos) ) {
						$photos = "../../mod_trombinoscopes/images/trombivide.jpg";
					}
					$valeur=redimensionne_image($photos);
?>
				<img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" />
				</td>
<?php
				}
?>
			</tr>
			<tr>
				<td class="norme_absence">pour le motif : <?php echo motif_de($data_avec_motif['motif_absence_eleve']); ?></td>
			</tr>
			<tr>
				<td class="norme_absence"><?php if ($data_avec_motif['justify_absence_eleve'] == "O") {?><span class="norme_absence_vert"><b>a donn&eacute; pour justification : </b>
									<?php } elseif($data_avec_motif['justify_absence_eleve'] == "T") { ?><span class="norme_absence_vert" style="color: orange;"><b>a justifi&eacute; par t&eacute;l&eacute;phone</b>
																							<?php }else { ?><span class="norme_absence_rouge"><b>n'a pas donn&eacute; de justification</b>
																							<?php } ?></span></td>
			</tr>
			<tr>
				<td class="norme_absence"><?php if(!empty($data_avec_motif['info_justify_absence_eleve'])) { ?><blockquote><?php echo $data_avec_motif['info_justify_absence_eleve']; ?></blockquote><?php } ?></td>
			</tr>
			<tr class="texte_fondjaune_calque_information">
				<td colspan="3">
                <?php

				// affichage des numéro de téléphone
				$info_responsable = tel_responsable($data_avec_motif['ele_id']);

				$telephone = ''; $telephone_pers = ''; $telephone_prof = ''; $telephone_port = '';

				if ( !empty($info_responsable) )
				{

					if ( $info_responsable[0]['tel_pers'] != '' ) { $telephone_pers = '<br />Pers. <strong>'.present_tel($info_responsable[0]['tel_pers']).'</strong> '; }
					if ( $info_responsable[0]['tel_prof'] != ''  ) { $telephone_prof = '<br />Prof. <strong>'.present_tel($info_responsable[0]['tel_prof']).'</strong> '; }
					if ( $info_responsable[0]['tel_port'] != ''  ) { $telephone_port = '<br />Port.<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="14" width="14" /> '.present_tel($info_responsable[0]['tel_port']); }

				}

				if ( $telephone_pers != '' and $telephone_prof === '' ) { $telephone = $telephone_pers; }
				if ( $telephone_pers === '' and $telephone_prof != '' ) { $telephone = $telephone_prof; }
				if ( $telephone_pers != '' and $telephone_prof != '' ) { $telephone = $telephone_pers . ' ' . $telephone_prof; }
				if ( $telephone_pers === '' and $telephone_prof === '' and $telephone_port != '' ) { $telephone = $telephone_port . ' ! surtaxe'; }

				if ( $telephone_pers != '' or $telephone_prof != '' or $telephone_port != '' ) { $telephone = 'Téléphone responsable : '.$telephone; }
				else { $telephone = 'Aucun numéro de téléphone disponible'; }

				echo $telephone;

		  		?>
				</td>
			</tr>
		</table>
	</div>
<?php
			} // fin du if($type == "A" or $type == "I" or $type == ...
		}// fin du while
// fin du div de la fiche élève
?>


<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<table style="margin: auto; width: 700px;" border="0" cellspacing="0" cellpadding="1">
  <tr style="vertical-align: top">
    <td class="td_tableau_gestion">
  <br />
  <form name ="form3" method="post" action="gestion_absences.php?choix=<?php echo $choix; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>&amp;type=<?php echo $type; ?>&amp;action_sql=supprimer_selection">
   <fieldset class="fieldset_efface">
    <table class="td_tableau_gestion"  style="width: 500px;">
        <tr>
          <td colspan="2" class="titre_tableau_gestion" nowrap><b><?php if ($type=="A") { ?>Absences avec motif<?php } ?><?php if ($type=="R") { ?>Retards avec motif<?php } ?><?php if ($type=="I") { ?>Infirmerie avec motif<?php } ?><?php if ($type=="D") { ?>Dispenses avec motif<?php } ?></b></td>
        </tr>
<?php
		$total = 0;
		$i = 0;
		$ic = 1;
		$execution_avec_motif = mysql_query($requete_avec_motif)
			or die('Erreur SQL !'.$requete_avec_motif.'<br />'.mysql_error());
		while ( $data_avec_motif = mysql_fetch_array($execution_avec_motif)) {
			if (in_array($data_avec_motif['eleve_absence_eleve'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {

			if ($ic==1) {
				$ic=2;
				$couleur_cellule="td_tableau_absence_1";
			} else {
				$ic=1;
				$couleur_cellule="td_tableau_absence_2";
			}
?>
	<tr>
		<td class="<?php echo $couleur_cellule; ?>" onmouseover="changementDisplay('d<?php echo $data_avec_motif['id_absence_eleve']; ?>', ''); return true;" onmouseout="changementDisplay('d<?php if ($type == "D" ) { echo $data_avec_motif['id_dispense_eleve']; } else  { echo $data_avec_motif['id_absence_eleve']; } ?>', ''); return true;">
		<input name="selection[<?php echo $total; ?>]" id="sel<?php echo $total; ?>" type="checkbox" value="1" <?php $varcoche = $varcoche."'sel".$total."',"; ?> <?php /* if((isset($selection[$total]) and $selection[$total] == "1") OR $cocher == 1) { ?>checked="checked"<?php } */ ?> />
		<input name="id_absence_eleve[<?php echo $total; ?>]" type="hidden" value="<?php echo $data_avec_motif['id_absence_eleve']; ?>" />
		<?php

            	$cpt_lettre_absence_recus = lettre_absence_envoye($data_avec_motif['id_absence_eleve']);
            	if ( $cpt_lettre_absence_recus != 0 )
            	{

					$info_sup = 'du '.date_fr($data_avec_motif['d_date_absence_eleve']).' au '.date_fr($data_avec_motif['a_date_absence_eleve']);
					?><a href="#" onClick="alert('Pour le supprimer, supprimer la date d\'envoye du courrier.'); return false;"><img src="../../images/icons/delete_imp.png" style="width: 16px; height: 16px;" title="Impossible de supprimer <?php if($data_avec_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_avec_motif['type_absence_eleve']=="D") { ?>la dispence<?php } if ($data_avec_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?> <?php echo $info_sup; ?>" border="0" alt="" /></a><?php

				}
				else
				{

            		?><a href="ajout_<?php if($data_avec_motif['type_absence_eleve']=="A") { ?>abs<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>ret<?php } if ($data_avec_motif['type_absence_eleve']=="D") { ?>dip<?php } if ($data_avec_motif['type_absence_eleve']=="I") { ?>inf<?php } ?>.php?action=supprimer&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_avec_motif['id_absence_eleve']; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>" <?php $info_sup = 'du '.date_fr($data_avec_motif['d_date_absence_eleve']).' au '.date_fr($data_avec_motif['a_date_absence_eleve']); ?>onClick="return confirm('Etes-vous sur de vouloir le supprimer <?php if($data_avec_motif['type_absence_eleve']=="A") { ?>l\'absence<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_avec_motif['type_absence_eleve']=="D") { ?>la dispence<?php } if ($data_avec_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?> <?php echo $info_sup; ?>.')"><img src="../../images/icons/delete.png" style="width: 16px; height: 16px;" title="supprimer <?php if($data_avec_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_avec_motif['type_absence_eleve']=="D") { ?>la dispence<?php } if ($data_avec_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?> <?php echo $info_sup; ?>" border="0" alt="" /></a><?php


				} ?>

		<a href="ajout_<?php if($data_avec_motif['type_absence_eleve']=="A") { ?>abs<?php } if($data_avec_motif['type_absence_eleve']=="D") { ?>dip<?php } if($data_avec_motif['type_absence_eleve']=="I") { ?>inf<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>ret<?php } ?>.php?action=modifier&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_avec_motif['id_absence_eleve']; ?>&amp;mode=eleve"><img src="../../images/icons/saisie.png" style="width: 16px; height: 16px;" title="modifier <?php if($data_avec_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_avec_motif['type_absence_eleve']=="D") { ?>la dispence<?php } if ($data_avec_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?>" border="0" alt="" /></a>
		<a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_avec_motif['login']; ?>" title="consulter la fiche de l'élève"><?php echo "<b>".strtoupper($data_avec_motif['nom'])."</b> ".ucfirst($data_avec_motif['prenom']) . " (" . $data_avec_motif['regime'] . ")"; ?></a>
		</td>
		<td class="<?php echo $couleur_cellule; ?>">
<?php
			if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo")) {
				$nom_photo = '';
				$nom_photo = nom_photo($data_avec_motif['elenoet'],"eleves",2);
        		$photos = "../../photos/eleves/".$nom_photo;
				//if ( $nom_photo === '' or !file_exists($photo) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
				if ( $nom_photo === '' or !file_exists($photos) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
				$valeur = redimensionne_image($photos);
?>
			<img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td>
<?php
			}
?>
		</td>
	</tr>
<?php
			$total = $total + 1;
			}
		} // fin du while
?>
        <tr>
          <td class="class55bas">&nbsp;</td>
          <td class="class35bas">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2">
		<?php /* <a href="gestion_absences.php?choix=<?php echo $choix; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>&amp;cocher=<?php if($cocher==1) { ?>0<?php } else { ?>1<?php  } ?>"><?php if($cocher==1) { ?>déc<?php } else { ?>C<?php  } ?>ocher toutes les cellules</a> */ ?>
		<?php $varcoche = $varcoche."'form3'"; ?>
		<a href="javascript:CocheCheckbox(<?php echo $varcoche; ?>)">Cocher</a> | <a href="javascript:DecocheCheckbox(<?php echo $varcoche; ?>)">Décocher</a>	<input name="date_ce_jour" type="hidden" value="<?php echo $date_ce_jour; ?>" /><input name="submit2" type="image" src="../../images/delete16.png" title="supprimer la s&eacute;lection rapidement" style="border: 0px;" onClick="return confirm('Etes-vous sur de vouloir le supprimer...')" />
	  </td>
        </tr>
      </table>
        <?php /* <input name="submit2" type="submit" value="supprimer la s&eacute;lection" onClick="return confirm('Etes-vous sur de vouloir le supprimer...')" /> */ ?>
        <input value="<?php echo $year; ?>" name="year" id="year4" type="hidden" />
        <input value="<?php echo $month; ?>" name="month" id="month4" type="hidden" />
        <input value="<?php echo $day; ?>" name="day" id="day4" type="hidden" />
       </fieldset>
      </form>
    </td>
    <td style="text-align: center"><br />
        <form name ="form1" method="post" action="gestion_absences.php?choix=<?php echo $choix; ?>&amp;type=<?php echo $type; ?>">
          <fieldset style="background-color: #D1EFE8; background-image: url(../images/2.png); ">
            <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <?php //Affiche le calendrier
                minicals($year, $month, $day, $classe_choix, $type, 'gestion_absences');
            ?>
            Informations données pour la date du<br /><b><?php echo date_frl($date_ce_jour); ?></b><br /><br />
            Classe &nbsp;
            <select name="classe_choix">
              <option value="" selected onClick="javascript:document.form1.submit()">Toutes les classes</option>
                  <?php
                    $resultat_liste_classe = mysql_query($requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysql_error());
                    While ( $data_liste_classe = mysql_fetch_array ($resultat_liste_classe)) {
                           if ($classe_choix==$data_liste_classe['id']) {$selected = "selected"; } else {$selected = ""; }?>
                          <option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>  onClick="javascript:document.form1.submit()"><?php echo $data_liste_classe['nom_complet']." (".$data_liste_classe['classe'].")"; ?></option>
                  <?php } ?>
            </select><noscript><input value=">>" name="date" type="submit" /></noscript><br />
            <input value="<?php echo $year; ?>" name="year5" id="year" type="hidden" />
            <input value="<?php echo $month; ?>" name="month5" id="month" type="hidden" />
            <input value="<?php echo $day; ?>" name="day" id="day5" type="hidden" />
            <?php if (getSettingValue("active_module_trombinoscopes")=='y') { ?>
            <input type="checkbox" name="photo" id="avecphoto" value="avec_photo" onClick="javascript:document.form1.submit()" <?php  if ($photo=="avec_photo") { ?>checked="checked"<?php } ?> /><label for="avecphoto" style="cursor: pointer;">Avec photo</label<br />
            <?php } ?>
          </fieldset>
        </form>
    </td>
  </tr>
</table>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>


<?php /* fiche élève sélection */ ?>
<?php if ( $fiche_eleve != '' ) {

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
<div style="text-align:center; margin: auto;">
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


<?php /* fiche élève */ ?>
<?php if ( $select_fiche_eleve != '' ) {

         $requete_liste_fiche = "SELECT * FROM ".$prefix_base."eleves WHERE ".$prefix_base."eleves.login = '".$select_fiche_eleve."'";
         $execution_liste_fiche = mysql_query($requete_liste_fiche) or die('Erreur SQL !'.$requete_liste_fiche.'<br />'.mysql_error());
         while ( $data_liste_fiche = mysql_fetch_array($execution_liste_fiche))
          {

              $login_eleve = $data_liste_fiche['login'];
              $id_eleve_photo = $data_liste_fiche['elenoet'];
              $ele_id_eleve = $data_liste_fiche['ele_id'];
              $nom_eleve = strtoupper($data_liste_fiche['nom']);
              $prenom_eleve = ucfirst($data_liste_fiche['prenom']);
              $naissance_eleve = date_fr(date_sql(affiche_date_naissance($data_liste_fiche['naissance'])));
              $date_de_naissance = $data_liste_fiche['naissance'];
              $sexe_eleve = $data_liste_fiche['sexe'];
			  $responsable_eleve = tel_responsable($ele_id_eleve);

          }
    ?>

    <br />

<?php /* fiche identitée de l'élève */ ?>
<a name="ident"></a>
<div style="background-image: url(../images/haut_tab.png); font-size: 120%; font-weight: bold; color: #E8F1F4; text-align: left;">Identité élève</div>
<div style="border-top: 2px solid #2C7E8F; /* #FF9F2F */ border-bottom: 2px solid #2C7E8F; width: 100%; margin: auto; padding: 0; position: relative;">
	<div style="height: 135px; background: transparent url(../images/grid_10.png)">
		<div style="float: left; margin: 12.5px;">
                <?php if (getSettingValue("active_module_trombinoscopes")=='y') {
                $nom_photo = '';
                $nom_photo = nom_photo($id_eleve_photo,"eleves",2);
                $photos = "../../photos/eleves/".$nom_photo;
                //if ( $nom_photo === '' or !file_exists($photo) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                if ( $nom_photo === '' or !file_exists($photos) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /><?php
                 } ?>
		</div>
		<div style="float: left; margin: 12.5px 0px 0px 0px; width: 36%">
			Nom : <strong><?php echo $nom_eleve; ?></strong><br />
			Prénom : <strong><?php echo $prenom_eleve; ?></strong><br />
			Date de naissance : <?php echo $naissance_eleve; ?><br />
			Age : <strong><?php echo age($date_de_naissance); ?> ans</strong><br />
			<br />
			Classe : <a href="#" class="info"><?php echo classe_de($login_eleve); ?><span style="width: 300px;">(Suivi par : <?php echo pp(classe_court_de($login_eleve)); ?>)</span></a>
		</div>
		<div style="float: left; background-image: url(../images/responsable.png); background-repeat:no-repeat; height: 135px; width: 20px; margin-left: 10px;">&nbsp;</div>
		<div style="float: left; margin: 12.5px; overflow: auto;  width: 40%;">
			<?php
				$cpt_responsable = 0;
				while ( !empty($responsable_eleve[$cpt_responsable]) )
				{
					echo $responsable_eleve[$cpt_responsable]['civilite'].' '.strtoupper($responsable_eleve[$cpt_responsable]['nom']).' '.ucfirst($responsable_eleve[$cpt_responsable]['prenom']).'<br />';
					$telephone = '';
						if ( !empty($responsable_eleve[$cpt_responsable]['tel_pers']) ) { $telephone = $telephone.'Tél. <strong>'.present_tel($responsable_eleve[$cpt_responsable]['tel_pers']).'</strong> '; }
						if ( !empty($responsable_eleve[$cpt_responsable]['tel_prof']) ) { $telephone = $telephone.'Prof. <strong>'.present_tel($responsable_eleve[$cpt_responsable]['tel_prof']).'</strong> '; }
						if ( !empty($responsable_eleve[$cpt_responsable]['tel_port']) ) { $telephone = $telephone.'<br />Port.<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="14" width="14" /> '.present_tel($responsable_eleve[$cpt_responsable]['tel_port']); }
					echo '<div style="margin-left: 0px; font-size: 13px;">'.$telephone.'</div>';
					$cpt_responsable = $cpt_responsable + 1;
				}
			?>
		</div>
	</div>
</div>
<?php /* fin fiche identitée de l'élève */ ?>
<!--
Pour éviter un centrage bizarre:
-->
<div style='clear: both;'>&nbsp;</div>

    <div style="text-align: center;">
	[ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;aff_fiche=suivieleve#suivieleve" title="consulter le suivi de l'élève">Suivi de l'élève</a> | <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;aff_fiche=abseleve#abseleve" title="consulter l'absentéisme non justifié">Absentéisme non justifié</a> | <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;aff_fiche=tableauannuel" title="consulter la fiche de l'élève">Statistique annuelle</a> | <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;aff_fiche=suivieleve#tab_sem_abs" title="Répartissement des absences">Répartition des absences</a> ]
    </div><br />

<?php /* DIV global */ ?>
<div style="margin: auto; position: relative;">

<?php /* DIV coté Gauche */ ?>
	<div style="float: left; width: 370px;">

<?php /* DIV du suivi de l'élève */ ?>
	   <?php if ( $aff_fiche==='suivieleve' or $aff_fiche==='' ) { ?>
		<a name="suivieleve"></a>
		<div style="background-image: url(../images/haut_tab.png); font-size: 120%; font-weight: bold; color: #E8F1F4; text-align: left;">Actualité élève</div>
		<div style="border-top: 2px solid #2C7E8F; border-bottom: 2px solid #2C7E8F;">
			<div style="background: transparent url(../images/grid_10.png); padding-top: 5px;">

			<?php /* formulaire pour l'ajout de l'actualitée de l'élève */ ?>
			<a name="formulaire"></a>
		            <form method="post" action="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>">
		               <fieldset>
		                 <legend>Ajouter un suivi</legend>
			                 <select id="info_suivi" onchange="data_info_suivi.value += info_suivi.options[info_suivi.selectedIndex].text + '\n'" style="width: 210px;">
			                   <option>Sélectionné un texte rapide</option>
					  		   <option>[Exclusion du cours] A été exclus du cours de:   à:</option>
			                   <option>Rencontre avec les parents</option>
			                 </select>
				         <input type="hidden" name="eleve_suivi_eleve_cpe" value="<?php echo $login_eleve; ?>" />
			                 <input type="hidden" name="debut_selection_suivi" value="<?php echo $debut_selection_suivi; ?>" />
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
						<textarea id="data_info_suivi" name="data_info_suivi" rows="3" cols="28" style="height: 70px;"><?php if($action == "modifier") { echo $data_modif_fiche['komenti_suivi_eleve_cpe']; } ?></textarea>
						</td>
						<td>
						<div style="font-family: Arial; font-size: 0.8em; background-color: #FFFFFF; border : 1px solid #0061BD; height: 70px; padding-left: 2px; width: 100px;">
							Niveau de priorité<br />
							<input name="niveau_urgent" id="nur1" value="1" type="radio" <?php if(!empty($data_modif_fiche['niveau_message_suivi_eleve_cpe']) and $data_modif_fiche['niveau_message_suivi_eleve_cpe']==='1') { ?>checked="checked"<?php } else { ?>checked="checked"<?php } ?> /><label for="nur1" style="cursor: pointer;">Information</label><br />
							<input name="niveau_urgent" id="nur2" value="2" type="radio" <?php if(!empty($data_modif_fiche['niveau_message_suivi_eleve_cpe']) and $data_modif_fiche['niveau_message_suivi_eleve_cpe']==='2') { ?>checked="checked"<?php } ?> /><label for="nur2" style="cursor: pointer;">Important</label><br />
							<input name="niveau_urgent" id="nur3" value="3" type="radio" <?php if(!empty($data_modif_fiche['niveau_message_suivi_eleve_cpe']) and $data_modif_fiche['niveau_message_suivi_eleve_cpe']==='3') { ?>checked="checked"<?php } ?> /><label for="nur3" style="cursor: pointer;">Prioritaire</label><br />
						</div>
						</td>
					    </tr>
				        </table>
				  	Entraine une action
					<select name="action_suivi" style="width: 218px;">
 	        		        <?php
					      $requete_liste_action = "SELECT init_absence_action, def_absence_action FROM ".$prefix_base."absences_actions ORDER BY init_absence_action ASC";
		        	              $resultat_liste_action = mysql_query($requete_liste_action) or die('Erreur SQL !'.$requete_liste_action.'<br />'.mysql_error());
			                      while ( $data_liste_action = mysql_fetch_array ($resultat_liste_action)) { ?>
		                                     <option value="<?php echo $data_liste_action['init_absence_action']; ?>" <?php if(!empty($data_modif_fiche['action_suivi_eleve_cpe']) and $data_modif_fiche['action_suivi_eleve_cpe'] === $data_liste_action['init_absence_action']) { ?>selected="selected"<?php } ?>><?php echo $data_liste_action['init_absence_action']." - ".$data_liste_action['def_absence_action']; ?></option>
		                              <?php } ?>
					</select><br />
					Méthode&nbsp;:
						<input name="support_suivi_eleve_cpe" id="ppar1" value="1" type="radio" <?php if(!empty($data_modif_fiche['support_suivi_eleve_cpe']) and $data_modif_fiche['support_suivi_eleve_cpe'] === '1') { ?>checked="checked"<?php } ?> onClick="javascript:aff_lig_type_courrier('cacher')" /><label for="ppar1" style="cursor: pointer;">Oralement</label>
						<input name="support_suivi_eleve_cpe" id="ppar2" value="2" type="radio" <?php if(!empty($data_modif_fiche['support_suivi_eleve_cpe']) and $data_modif_fiche['support_suivi_eleve_cpe'] === '2') { ?>checked="checked"<?php } ?> onClick="javascript:aff_lig_type_courrier('cacher')" /><label for="ppar2" style="cursor: pointer;">Tél.</label>
						<input name="support_suivi_eleve_cpe" id="ppar3" value="3" type="radio" <?php if(!empty($data_modif_fiche['support_suivi_eleve_cpe']) and $data_modif_fiche['support_suivi_eleve_cpe'] === '3') { ?>checked="checked"<?php } ?> onClick="javascript:aff_lig_type_courrier('cacher')" /><label for="ppar3" style="cursor: pointer;">Fax</label>
						<input name="support_suivi_eleve_cpe" id="ppar5" value="5" type="radio" <?php if(!empty($data_modif_fiche['support_suivi_eleve_cpe']) and $data_modif_fiche['support_suivi_eleve_cpe'] === '5') { ?>checked="checked"<?php } ?> onClick="javascript:aff_lig_type_courrier('cacher')" /><label for="ppar5" style="cursor: pointer;">Mel</label>
						<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input name="support_suivi_eleve_cpe" id="ppar4" value="4" type="radio" <?php if(!empty($data_modif_fiche['support_suivi_eleve_cpe']) and $data_modif_fiche['support_suivi_eleve_cpe'] === '4') { ?>checked="checked"<?php } ?> onClick="javascript:aff_lig_type_courrier('afficher')" /><label for="ppar4" style="cursor: pointer;">Courrier</label>
						<input name="support_suivi_eleve_cpe" id="ppar6" value="6" type="radio" <?php if(!empty($data_modif_fiche['support_suivi_eleve_cpe']) and $data_modif_fiche['support_suivi_eleve_cpe'] === '6') { ?>checked="checked"<?php } ?> onClick="javascript:aff_lig_type_courrier('cacher')" /><label for="ppar6" style="cursor: pointer;">Document de liaison</label>


					<div id='ligne_type_courrier'>
					   <select name="lettre_type" size="6" style="width: 350px; border: 1px solid #000000;">
						<optgroup label="Type de lettre">
					        <?php
						$requete_lettre ="SELECT * FROM ".$prefix_base."lettres_types ORDER BY categorie_lettre_type ASC, titre_lettre_type ASC";
					        $execution_lettre = mysql_query($requete_lettre) or die('Erreur SQL !'.$requete_lettre.'<br />'.mysql_error());
				  		while ($donner_lettre = mysql_fetch_array($execution_lettre))
		  				{
						   ?><option value="<?php echo $donner_lettre['id_lettre_type']; ?>" <?php if (isset($lettre_type) and $lettre_type === $donner_lettre['id_lettre_type']) { ?>selected="selected"<?php } ?>><?php echo ucfirst($donner_lettre['titre_lettre_type']); ?></option><?php echo "\n";
						} ?>
						</optgroup>
					  </select>
					</div>

				<script type='text/javascript'>
					test='cacher';
					function aff_lig_type_courrier(mode){
						if(mode=='afficher'){
							document.getElementById('ligne_type_courrier').style.display='';
						} else {
								document.getElementById('ligne_type_courrier').style.display='none';
							}
					  	test=document.getElementById('type').value;
					}

					if(test=='cacher') {
						aff_lig_type_courrier('cacher');
					}
				</script>

              </fieldset>
            </form>



	<?php $cpt_komenti = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."suivi_eleve_cpe WHERE ".$prefix_base."suivi_eleve_cpe.eleve_suivi_eleve_cpe = '".$login_eleve."'"),0); ?>
	<div style="text-align: center;">
	  <?php if($debut_selection_suivi!='0') { ?><a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi-'2'; ?>">Page précédente</a><?php } ?>
	  <?php $debut_selection_suivi_suivant = $debut_selection_suivi+'2'; if($debut_selection_suivi!='0' and $debut_selection_suivi_suivant<=$cpt_komenti) { ?> | <?php } ?>
	  <?php if($debut_selection_suivi_suivant<=$cpt_komenti) { ?><a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi+'2'; ?>">Page suivant</a><?php } ?>
	</div>
           <?php
	     $requete_komenti = "SELECT * FROM ".$prefix_base."suivi_eleve_cpe WHERE ".$prefix_base."suivi_eleve_cpe.eleve_suivi_eleve_cpe = '".$login_eleve."' ORDER BY date_suivi_eleve_cpe DESC, heure_suivi_eleve_cpe DESC LIMIT ".$debut_selection_suivi.", 2";
             $execution_komenti = mysql_query($requete_komenti) or die('Erreur SQL !'.$requete_komenti.'<br />'.mysql_error());
              while ( $data_komenti = mysql_fetch_array($execution_komenti))
                {
			$action_pour_eleve = '';
			if(!empty($data_komenti['action_suivi_eleve_cpe']) and $data_komenti['action_suivi_eleve_cpe'] != 'A') { $action_pour_eleve = ', '.action_de($data_komenti['action_suivi_eleve_cpe']); }
		   if(!empty($data_komenti['niveau_message_suivi_eleve_cpe'])) {
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='1') { $couleur='#FFFFFF'; $couleur2='#280FFF'; $couleur3='#FDFFEF'; $drapeau='[information]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='2') { $couleur='#FFF3DF'; $couleur2='#FF782F'; $couleur3='#FDFFEF'; $drapeau='[important]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='3') { $couleur='#FFDFDF'; $couleur2='#FF0000'; $couleur3='#FDFFEF'; $drapeau='[prioritaire]'; }
			  } else { $couleur='#FFFFFF'; $couleur2='#4DFF2F'; $couleur3='#FDFFEF'; $drapeau=''; } ?>
                    <div class="info_eleve" style="background: <?php echo $couleur; ?>;"><b><?php echo date_frl($data_komenti['date_suivi_eleve_cpe']).' - '.$data_komenti['heure_suivi_eleve_cpe'].' <span style="font-weight: bold; color: '.$couleur2.';">'.$drapeau.'</span>'; ?></b><br /><?php echo $data_komenti['komenti_suivi_eleve_cpe'].$action_pour_eleve; ?><br /><br /><span class="dimi_texte">Ecrit par : <?php echo qui($data_komenti['parqui_suivi_eleve_cpe']); ?><br />
			<?php
				// vérifie si on n'a le droit de supprimer la fiche on ne peut pas s'il y a un courrier attaché
				$autorise_supprimer = 'non';
			        $cpt_lettre_recus = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE partde_lettre_suivi = 'suivi_eleve_cpe' AND partdenum_lettre_suivi = '".$data_komenti['id_suivi_eleve_cpe']."'"),0);
			          if( $cpt_lettre_recus === '0' ) { $autorise_supprimer = 'oui'; }
			?>
				[ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;id_suivi_eleve_cpe=<?php echo $data_komenti['id_suivi_eleve_cpe']; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi; ?>&amp;action=modifier#formulaire">modifier</a> <?php if ( $autorise_supprimer === 'oui' ) { ?>|<a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;id_suivi_eleve_cpe=<?php echo $data_komenti['id_suivi_eleve_cpe']; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi; ?>&amp;action_sql=supprimer">supprimer</a><?php } ?> ] <?php /* [ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;id_suivi_eleve_cpe=<?php echo $data_komenti['id_suivi_eleve_cpe']; ?>">action</a> ] */ ?></span></div>
		<?php // courrier attaché
	        $courrier_existance = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE partdenum_lettre_suivi = '".$data_komenti['id_suivi_eleve_cpe']."' AND partde_lettre_suivi = 'suivi_eleve_cpe'"),0);
	        if ($courrier_existance != '0') { ?>
			<?php
	               $requete_1 ="SELECT * FROM ".$prefix_base."lettres_suivis, ".$prefix_base."lettres_types WHERE partdenum_lettre_suivi = '".$data_komenti['id_suivi_eleve_cpe']."' AND partde_lettre_suivi = 'suivi_eleve_cpe' AND type_lettre_suivi = id_lettre_type";
	               $execution_1 = mysql_query($requete_1) or die('Erreur SQL !'.$requete_1.'<br />'.mysql_error());
	               while ( $data_1 = mysql_fetch_array($execution_1)) {
			       $datation = ''; ?>
			    <div class="info_eleve_courrier" style="background: <?php echo $couleur3; ?>;"><?php if(empty($data_1['envoye_date_lettre_suivi']) or $data_1['envoye_date_lettre_suivi'] === '0000-00-00') { ?><div style="float: right; margin: 0;"><a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;id_suivi_eleve_cpe=<?php echo $data_komenti['id_suivi_eleve_cpe']; ?>&amp;id_lettre_suivi=<?php echo $data_1['id_lettre_suivi']; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi; ?>&amp;action_sql=detacher_courrier">Supprimer</a></div><?php } ?><strong>Courrier attaché:</strong><br />
				Titre: <strong><?php echo $data_1['titre_lettre_type']; ?></strong><?php
				$datation = '<span title="par: '.qui($data_1['quiemet_lettre_suivi']).'">'.date_frl($data_1['emis_date_lettre_suivi']).'<small> à '.heure($data_1['emis_heure_lettre_suivi']).'</small></span>'; ?>
				<br />&nbsp;&nbsp;&nbsp;émis le: <?php echo $datation;
			      if($data_1['statu_lettre_suivi'] != 'annuler') {
				if($data_1['envoye_date_lettre_suivi'] != '0000-00-00') { $datation = '<span title="par: '.qui($data_1['quienvoi_lettre_suivi']).'">'.date_frl($data_1['envoye_date_lettre_suivi']).'<small> à '.heure($data_1['envoye_heure_lettre_suivi']).'</small></span>'; } else { $datation = 'en attente'; } ?>
				<br />&nbsp;&nbsp;&nbsp;expédié le: <?php echo $datation;
				if($data_1['reponse_date_lettre_suivi'] != '0000-00-00') { $datation = '<span title="par: '.qui($data_1['quireception_lettre_suivi']).'">'.date_frl($data_1['reponse_date_lettre_suivi']).'</span>'; } else { $datation = 'en attente'; } ?>
				<br />&nbsp;&nbsp;&nbsp;réponse reçus le: <?php echo $datation;
				       if ( !empty($data_1['reponse_remarque_lettre_suivi']) ) { ?><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Remarque : <?php echo $data_1['reponse_remarque_lettre_suivi']; }
			        } else { ?><br />&nbsp;&nbsp;&nbsp;<span style="color: #EF000A;"><strong>Courrier annulé par <?php echo qui($data_1['quireception_lettre_suivi']); ?></strong></span><?php } ?>
			    </div>
		 <?php } ?>
		<?php } ?>
           <?php } ?>

           	<div style="text-align: center;">
	  <?php if($debut_selection_suivi!='0') { ?><a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi-'2'; ?>">Page précédente</a><?php } ?>
	  <?php $debut_selection_suivi_suivant = $debut_selection_suivi+'2'; if($debut_selection_suivi!='0' and $debut_selection_suivi_suivant<=$cpt_komenti) { ?> | <?php } ?>
	  <?php if($debut_selection_suivi_suivant<=$cpt_komenti) { ?><a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi+'2'; ?>">Page suivant</a><?php } ?>
	</div>
		<br />
			</div>
		</div>
	</div>
	<?php } ?>
<?php /* fin du DIV du suivi de l'élève */ ?>


<?php /* DIV de l'absentéisme de l'élève */ ?>
	<?php if ( $aff_fiche === 'abseleve' ) { ?>
		<a name="abseleve"></a>
		<div style="background-image: url(../images/haut_tab.png); font-size: 120%; font-weight: bold; color: #E8F1F4; text-align: left;">Absentéisme de l'élève</div>
		<div style="border-top: 2px solid #2C7E8F; /* #FF9F2F */ border-bottom: 2px solid #2C7E8F; width: 100%; margin: auto; padding: 0; position: relative;">
			<div style="background: transparent url(../images/grid_10.png); padding-top: 5px;">

	   <?php /* tableau des absences non justifiée */ ?>
           <?php $cpt_absences = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."' AND type_absence_eleve = 'A'"),0);
		 $cpt_absences_nj = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."' AND type_absence_eleve = 'A' AND justify_absence_eleve = 'N '"),0);
           if ( $cpt_absences != 0 ) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=A',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Absences"><?php echo $cpt_absences; ?></a></b> Absence(s)</p>
               <?php if ( $cpt_absences_nj != 0 ) { ?>
           	   Liste des absences non justifiée(s)<br />
		   <ul>
        	   <?php $requete_absences_nr = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='A' AND justify_absence_eleve = 'N' ORDER BY d_date_absence_eleve DESC, d_heure_absence_eleve ASC";
                	 $execution_absences_nr = mysql_query($requete_absences_nr) or die('Erreur SQL !'.$requete_absences_nr.'<br />'.mysql_error());
	                 while ($data_absences_nr = mysql_fetch_array($execution_absences_nr))
        	         {
                	      ?><li><a href="ajout_abs.php?action=modifier&amp;type=A&amp;id=<?php echo $data_absences_nr['id_absence_eleve']; ?>&amp;mode=eleve&amp;select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;fiche=oui">
			      <?php
					if ( $data_absences_nr['d_date_absence_eleve'] != $data_absences_nr['a_date_absence_eleve'] ) { echo 'du '.date_fr($data_absences_nr['d_date_absence_eleve']).' au '.date_fr($data_absences_nr['a_date_absence_eleve']); }
					elseif ( $data_absences_nr['d_date_absence_eleve'] === $data_absences_nr['a_date_absence_eleve'] ) { echo date_fr($data_absences_nr['d_date_absence_eleve'])." de ".$data_absences_nr['d_heure_absence_eleve']." à ".$data_absences_nr['a_heure_absence_eleve']; }
			      ?></a></li><?php
        	         }
	           ?>
		   </ul>
		<?php } ?>
	   <?php } ?>

	   <?php /* tableau des retards non justifiée */ ?>
	   <?php $cpt_retards = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."' AND type_absence_eleve = 'R'"),0);
		 $cpt_retards_nj = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."' AND type_absence_eleve = 'R' AND justify_absence_eleve = 'N '"),0);

           if ( $cpt_retards != 0 ) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=R',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Retards"><?php echo $cpt_retards; ?></a></b> Retards</p>
               <?php if($cpt_retards_nj != 0) { ?>
	           Liste des retards non justifié(s)<br />
		   <ul>
	           <?php $requete_retards_nr = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='R' AND justify_absence_eleve = 'N' ORDER BY d_date_absence_eleve DESC, d_heure_absence_eleve ASC";
        	         $execution_retards_nr = mysql_query($requete_retards_nr) or die('Erreur SQL !'.$requete_retards_nr.'<br />'.mysql_error());
	                 while ($data_retards_nr = mysql_fetch_array($execution_retards_nr))
	                 {
         	             ?><li><a href="ajout_ret.php?action=modifier&amp;type=R&amp;id=<?php echo $data_retards_nr['id_absence_eleve']; ?>&amp;mode=eleve&amp;select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;fiche=oui"><li><?php echo date_fr($data_retards_nr['d_date_absence_eleve'])." ".$data_retards_nr['d_heure_absence_eleve']; ?></a></li><?php
                	 }
	           ?>
		   </ul>
	      <?php } ?>
	   <?php } ?>

           <?php $cpt_dispences = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."' AND type_absence_eleve = 'D'"),0);
		 $cpt_dispences_nj = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."' AND type_absence_eleve = 'D' AND justify_absence_eleve = 'N '"),0);

           if( $cpt_dispences != 0 ) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=D',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Dispences"><?php echo $cpt_dispences; ?></a></b> Dispences</p>
               <?php if($cpt_dispences_nj != 0) { ?>
	           Liste des dispenses non justifiée(s)<br />
		   <ul>
	           <?php $requete_dispences_nr = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='D' AND justify_absence_eleve = 'N' ORDER BY d_date_absence_eleve DESC, d_heure_absence_eleve ASC";
        	         $execution_dispences_nr = mysql_query($requete_dispences_nr) or die('Erreur SQL !'.$requete_dispences_nr.'<br />'.mysql_error());
	                 while ($data_dispences_nr = mysql_fetch_array($execution_dispences_nr))
        	         {
                	      ?><li><a href="ajout_dip.php?action=modifier&amp;type=D&amp;id=<?php echo $data_dispences_nr['id_absence_eleve']; ?>&amp;mode=eleve&amp;select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;fiche=oui"><li><?php echo date_fr($data_dispences_nr['d_date_absence_eleve'])." ".$data_dispences_nr['d_heure_absence_eleve']; ?></a></li><?php
	                 }
        	   ?>
		   </ul>
		<?php } ?>
	   <?php } ?>

           <?php $cpt_infirmeries = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='I'"),0);
           if($cpt_infirmeries != 0) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=I',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Infirmerie"><?php echo $cpt_infirmeries; ?></a></b> Infirmeries</p>
           <br />
           <?php } ?>


		</div>
<?php /* fin du DIV de l'absentéisme de l'élève */ ?>
	</div>
	<?php } ?>

<?php /* fin DIV coté gauche */ ?>
</div>

<?php /* DIV coté droit */ ?>
<div style="float: left; width: 370px; margin-left: 2px;">

<?php /* DIV des statistique de l'élève */ ?>
	<?php if ( $aff_fiche === 'suivieleve' or  $aff_fiche === '' or $aff_fiche==='abseleve' ) { ?>
		<div style="background-image: url(../images/haut_tab.png); font-size: 120%; font-weight: bold; color: #E8F1F4; text-align: left;">Statistique élève</div>
		<div style="border-top: 2px solid #2C7E8F; border-bottom: 2px solid #2C7E8F;">
			<div style="background: transparent url(../images/grid_10.png); padding-top: 5px;">
			<?php
				// graphique
					// hauteur du graphique au maximum
					$h_graphique = '252'; //252
					// largeur du graphique au maximum
					$l_graphique = '370'; //536
// temporaire

$du = '01/09/2008';
$au = '30/06/2009';
$du_explose = explode('/',$du);
	$au_explose = explode('/',$au);
		$jour_du = '1';
		$mois_du = $du_explose[1];
		$annee_du = $du_explose[2];
		$jour_au = '31';
		$mois_au = $au_explose[1];
		$annee_au = $au_explose[2];
		$mois= '';
		$du_sql = $annee_du.'-'.$mois_du.'-'.$du_explose[0];
		$au_sql = $annee_au.'-'.$mois_au.'-'.$au_explose[0];

	if ( $mois === '' ) { $mois = tableau_mois($mois_du, $annee_du, $mois_au, $annee_au); }


$info_absence = repartire_jour($login_eleve, 'A', $du_sql, $au_sql);
$info_retard = repartire_jour($login_eleve, 'R', $du_sql, $au_sql);

// pour test
/*$i = 0;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'aou. 2006'; $mois[$i]['num_mois'] = '08'; $mois[$i]['num_mois_simple'] = '8'; $mois[$i]['num_annee'] = '2006'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'sep. 2006'; $mois[$i]['num_mois'] = '09'; $mois[$i]['num_mois_simple'] = '9'; $mois[$i]['num_annee'] = '2006'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'oct. 2006'; $mois[$i]['num_mois'] = '10'; $mois[$i]['num_mois_simple'] = '10'; $mois[$i]['num_annee'] = '2006'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'nov. 2006'; $mois[$i]['num_mois'] = '11'; $mois[$i]['num_mois_simple'] = '11'; $mois[$i]['num_annee'] = '2006'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'dec. 2006'; $mois[$i]['num_mois'] = '12'; $mois[$i]['num_mois_simple'] = '12'; $mois[$i]['num_annee'] = '2006'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'jan 2007'; $mois[$i]['num_mois'] = '01'; $mois[$i]['num_mois_simple'] = '1'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'fev. 2007'; $mois[$i]['num_mois'] = '02'; $mois[$i]['num_mois_simple'] = '2'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'mar. 2007'; $mois[$i]['num_mois'] = '03'; $mois[$i]['num_mois_simple'] = '3'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'avr. 2007';  $mois[$i]['num_mois'] = '04'; $mois[$i]['num_mois_simple'] = '4'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'mai. 2007'; $mois[$i]['num_mois'] = '05'; $mois[$i]['num_mois_simple'] = '5'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'jui. 2007';  $mois[$i]['num_mois'] = '06'; $mois[$i]['num_mois_simple'] = '6'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'juil. 2007'; $mois[$i]['num_mois'] = '07'; $mois[$i]['num_mois_simple'] = '7'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
*/


	//préparation des valeurs
	// axe x
	if ( !isset($valeur_x) ) {
		$i = '0';
		while ( !empty($mois[$i]) )
		{
			$valeur_x[$i] = $mois[$i]['mois_court'];
		$i = $i + 1;
		}
		$_SESSION['axe_x'] = $valeur_x;
	}
	// axe y des absences et des retards
		$i = '0';
		while ( !empty($mois[$i]) )
		{
			$mois_p = $mois[$i]['num_mois'];
			$annee_p = $mois[$i]['num_annee'];
				$total_abs = '0';
				if ( isset($info_absence[$annee_p.'-'.$mois_p]) and ($info_absence[$annee_p.'-'.$mois_p]['nb_nj'] != '0' or $info_absence[$annee_p.'-'.$mois_p]['nb_j'] != '0') ) {
					// $total_abs = $info_absence[$annee_p.'-'.$mois_p]['nb_j'] + $info_absence[$annee_p.'-'.$mois_p]['nb_nj'];
					$total_abs = $info_absence[$annee_p.'-'.$mois_p]['nb'];
				}
				$total_ret = '0';
				if ( isset($info_retard[$annee_p.'-'.$mois_p]) and ($info_retard[$annee_p.'-'.$mois_p]['nb_nj'] != '0' or $info_retard[$annee_p.'-'.$mois_p]['nb_j'] != '0') ) {
					// $total_ret = $info_retard[$annee_p.'-'.$mois_p]['nb_j'] + $info_absence[$annee_p.'-'.$mois_p]['nb_nj'];
					$total_ret = $info_retard[$annee_p.'-'.$mois_p]['nb'];
				}
			$valeur_y_abs[$i] = $total_abs;
			$valeur_y_ret[$i] = $total_ret;
		$i = $i + 1;
		}
		$_SESSION['axe_y_abs'] = $valeur_y_abs;
		$_SESSION['axe_y_ret'] = $valeur_y_ret;


	// génération du graphique
	?><div style="font-size: 14px; text-align: center; margin: auto;"><strong>Graphique des absences et retard sur l'année</strong></div>
	<img src="../lib/graph_double_ligne_fiche.php" alt="Graphique" style="border: 0px; margin: 0px; padding: 0px;"/><?php


	// tableau des nombre d'absences par jour et par heure (période)
   $i = '0';

//	     $requete_comptage = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."'  AND type_absence_eleve = 'A'"),0);
             $requete_komenti = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."'  AND type_absence_eleve = 'A' ORDER BY d_date_absence_eleve ASC, d_heure_absence_eleve DESC";
             $execution_komenti = mysql_query($requete_komenti) or die('Erreur SQL !'.$requete_komenti.'<br />'.mysql_error());
              while ( $donnee_base = mysql_fetch_array($execution_komenti))
                {
			$tableau[$i]['id'] = $i;
			$tableau[$i]['login'] = $donnee_base['eleve_absence_eleve'];
			$tableau[$i]['classe'] = '';
			$tableau[$i]['date_debut'] = $donnee_base['d_date_absence_eleve'];
			$tableau[$i]['date_fin'] = $donnee_base['a_date_absence_eleve'];
			$tableau[$i]['heure_debut'] = $donnee_base['d_heure_absence_eleve'];
			$tableau[$i]['heure_fin'] = $donnee_base['a_heure_absence_eleve'];
			$i = $i + 1;
		}

	// si aucune absences alors on initialise le tab à vide
	if ( $i > 0 ) {
	$tab = crer_tableau_jaj($tableau);
	} else { $tab = ''; }

	$i = '0';
	$requete_periode = 'SELECT * FROM '.$prefix_base.'absences_creneaux WHERE suivi_definie_periode = "1" ORDER BY heuredebut_definie_periode ASC';
        $execution_periode = mysql_query($requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.mysql_error());
	while ( $donnee_periode = mysql_fetch_array( $execution_periode ) ) {
		$Horaire[$i] = heure_texte_court($donnee_periode['heuredebut_definie_periode']).'-'.heure_texte_court($donnee_periode['heurefin_definie_periode']);
		$HorDeb[$i] = $donnee_periode['heuredebut_definie_periode'];
		$HorFin[$i] = $donnee_periode['heurefin_definie_periode'];
	$i = $i + 1;
	}

	if ( $i === '0' ) {
		$i = '0';
		$Horaire = Array(0 => "8h-9h", "9h-10h", "10h-11h", "11h-12h", "12h-13h", "13h-14h", "14h-15h", "15h-16h", "16h-17h", "17h-18h","18h-19h");
		$HorDeb = Array(0 => "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00","18:00:00");
		$HorFin = Array(0 => "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00", "18:00:00", "19:00:00");
	}

	$semaine = ouverture();


	?><br /><br /><a name="tab_sem_abs"></a>
	<table style="border-style: solid; border-width: 2px; border-color: black; width: 320px; border-collapse: collapse; margin: auto;">
		<caption style="font-size: 14px; text-align: center; margin: auto;"><strong>Nombre d'absences par créneau horaire</strong></caption>
		<tr style="background-color: #F0FFCF;">
			<td class="td_semaine_jour"></td>
			<?php if ( isset($semaine['lundi']['ouverture']) ) { ?><td class="td_semaine_jour">Lun.</td><?php } ?>
			<?php if ( isset($semaine['mardi']['ouverture']) ) { ?><td class="td_semaine_jour">Mar.</td><?php } ?>
			<?php if ( isset($semaine['mercredi']['ouverture']) ) { ?><td class="td_semaine_jour">Mer.</td><?php } ?>
			<?php if ( isset($semaine['jeudi']['ouverture']) ) { ?><td class="td_semaine_jour">Jeu.</td><?php } ?>
			<?php if ( isset($semaine['vendredi']['ouverture']) ) { ?><td class="td_semaine_jour">Ven.</td><?php } ?>
			<?php if ( isset($semaine['samedi']['ouverture']) ) { ?><td class="td_semaine_jour">Sam.</td><?php } ?>
		</tr>
	<?php
	$i = '0';

		// calcul du nombre de période à affiché dans la semaine
		$maxHor = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_creneaux
							WHERE suivi_definie_periode = '1'"),0);

		// si il est égale à 0 alors on l'initialise à 11
		if ( $maxHor === '0' or $maxHor > '11' ) { $maxHor = '11'; }

	$icouleur = '1';
	$aff_chiffre = '0';
	while ($i < $maxHor) {
		if($icouleur === '1') { $couleur_cellule = '#FAFFEF'; } else { $couleur_cellule = '#F0FFCF'; }
	 ?><tr>
		<td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php echo $Horaire[$i]; ?></td>
	 	<?php if ( isset($semaine['lundi']['ouverture']) and $semaine['lundi']['fermeture'] > $HorDeb[$i] ) { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php if ( $tab  != '' ) { $aff_chiffre = Absence("Mon", $HorDeb[$i], $HorFin[$i], $tab); } else { $aff_chiffre = '0'; } if($aff_chiffre != '0') { echo $aff_chiffre; } $aff_chiffre = '0'; ?></td><?php } else { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"></td><?php } ?>
		<?php if ( isset($semaine['mardi']['ouverture']) and $semaine['mardi']['fermeture'] > $HorDeb[$i] ) { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php if ( $tab  != '' ) { $aff_chiffre = Absence("Tue", $HorDeb[$i], $HorFin[$i], $tab); } else { $aff_chiffre = '0'; } if($aff_chiffre != '0') { echo $aff_chiffre; } $aff_chiffre = '0'; ?></td><?php } else { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"></td><?php } ?>
		<?php if ( isset($semaine['mercredi']['ouverture']) and $semaine['mercredi']['fermeture'] > $HorDeb[$i] ) { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php if ( $tab  != '' ) { $aff_chiffre = Absence("Wed", $HorDeb[$i], $HorFin[$i], $tab); } else { $aff_chiffre = '0'; } if($aff_chiffre != '0') { echo $aff_chiffre; } $aff_chiffre = '0'; ?></td><?php } else { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"></td><?php } ?>
		<?php if ( isset($semaine['jeudi']['ouverture']) and $semaine['jeudi']['fermeture'] > $HorDeb[$i] ) { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php if ( $tab  != '' ) { $aff_chiffre = Absence("Thu", $HorDeb[$i], $HorFin[$i], $tab); } else { $aff_chiffre = '0'; } if($aff_chiffre != '0') { echo $aff_chiffre; } $aff_chiffre = '0'; ?></td><?php } else { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"></td><?php } ?>
		<?php if ( isset($semaine['vendredi']['ouverture']) and $semaine['vendredi']['fermeture'] > $HorDeb[$i] ) { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php if ( $tab  != '' ) { $aff_chiffre = Absence("Fri", $HorDeb[$i], $HorFin[$i], $tab); } else { $aff_chiffre = '0'; } if($aff_chiffre != '0') { echo $aff_chiffre; } $aff_chiffre = '0'; ?></td><?php } else { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"></td><?php } ?>
		<?php if ( isset($semaine['samedi']['ouverture']) and $semaine['samedi']['fermeture'] > $HorDeb[$i] ) { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php if ( $tab  != '' ) { $aff_chiffre = Absence("Sat", $HorDeb[$i], $HorFin[$i], $tab); } else { $aff_chiffre = '0'; } if($aff_chiffre != '0') { echo $aff_chiffre; } $aff_chiffre = '0'; ?></td><?php } ?>
	   </tr><?php
	if($icouleur==='2') { $icouleur = '1'; } else { $icouleur = '2'; }
	$i = $i +1;
	} ?>
	</table>
		<div style="text-align: center; margin: auto;"><strong><a href="#ident">remonter</a></strong></div>
		<br /><br />
	</div>
</div>
<?php /* fin du DIV des statistique de l'élève */ ?>
<?php } ?>

<?php /* fin du coté droit */ ?>
</div>

<?php /* fin du DIV global */ ?>
</div>

<?php if ( $aff_fiche === 'tableauannuel' ) { ?>
<div style="margin: auto; position: relative;">
	<div style="float: left; width: 781px">
		<div style="background-image: url(../images/haut_tab.png); font-size: 120%; font-weight: bold; color: #E8F1F4; text-align: left;">L'élève sur l'année</div>
		<div style="border-top: 2px solid #2C7E8F; /* #FF9F2F */ border-bottom: 2px solid #2C7E8F; width: 100%; margin: auto; padding: 0; position: relative;">
			<div style="background: transparent url(../images/grid_10.png)"><br />
		<?php
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
	?><div style="font-size: 14px; text-align: center; margin: auto;"><strong>Statistique sur une année</strong></div>
	<?php
		$gepiYear = getSettingValue('gepiYear');
		$annee_select = explode('-',$gepiYear);
		if ( empty($annee_select[1]) ) { $annee_select = explode('/',$gepiYear); }
		if ( empty($annee_select[1]) ) { $annee_select = explode(' ',$gepiYear); }
		echo @tableau_annuel($select_fiche_eleve, '8', '12', trim($annee_select[0]), $tableau_info_donnee);
	?>
			</div>
		</div>
	</div>
</div>
  <br />
<?php } ?>

<?php } ?>




<?php if ($choix === 'lemessager' and $fiche_eleve === '' and $select_fiche_eleve === '') { ?>
<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<br />
<form method="post" action="gestion_absences.php?choix=lemessager" name="form1">A la date du <input name="du" type="text" size="11" maxlength="11" value="<?php if(isset($du)) { echo $du; } ?>" /><a href="#calend" onclick="<?php echo $cal->get_strPopup('../../lib/calendrier/pop.calendrier.php',350,170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a> <input type="submit" name="Submit" value="&gt;&gt;" /></form>
    <table style="margin: auto; width: 700px;" border="0" cellspacing="1" cellpadding="0">
       <tr class="fond_rouge">
           <td colspan="2" class="titre_tableau_gestion"><b>Le messager</b></td>
       </tr>
       <tr class="td_tableau_absence_1">
           <td class="norme_absence_min" style="text-align: center; width: 50%;">Les prioritaires</td>
           <td class="norme_absence_min" style="text-align: center; width: 50%;">Les messages</td>
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
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='2') { $couleur='#FFF3DF'; $couleur2='#FF782F'; $drapeau='[important]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='3') { $couleur='#FFDFDF'; $couleur2='#FF0000'; $drapeau='[prioritaire]'; }
			  } else { $couleur='#FFFFFF'; $couleur2='#4DFF2F'; $drapeau=''; } ?>
                    <p class="info_eleve" style="background: <?php echo $couleur; ?>;"><b><?php echo date_frl($data_komenti['date_suivi_eleve_cpe']).' - '.$data_komenti['heure_suivi_eleve_cpe']; ?></b><br /><?php echo $data_komenti['komenti_suivi_eleve_cpe']; ?><br /><br /><span class="dimi_texte">Ecrit par : <?php echo qui($data_komenti['parqui_suivi_eleve_cpe']); ?><br />Concerne : <strong><a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_komenti['eleve_suivi_eleve_cpe']; ?>" title="consulter le suivi élève"><?php echo qui_eleve($data_komenti['eleve_suivi_eleve_cpe']); ?></a></strong> de <?php echo classe_de($data_komenti['eleve_suivi_eleve_cpe']) ?><br />[ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_komenti['eleve_suivi_eleve_cpe']; ?>">lire</a> ]</span></p>
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
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='2') { $couleur='#FFF3DF'; $couleur2='#FF782F'; $drapeau='[important]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='3') { $couleur='#FFDFDF'; $couleur2='#FF0000'; $drapeau='[prioritaire]'; }
			  } else { $couleur='#FFFFFF'; $couleur2='#4DFF2F'; $drapeau=''; } ?>
                    <p class="info_eleve" style="background: <?php echo $couleur; ?>;"><b><?php echo date_frl($data_komenti['date_suivi_eleve_cpe']).' - '.$data_komenti['heure_suivi_eleve_cpe'].' <span style="font-weight: bold; color: '.$couleur2.';">'.$drapeau.'</span>'; ?></b><br /><?php echo $data_komenti['komenti_suivi_eleve_cpe']; ?><br /><br /><span class="dimi_texte">Ecrit par: <?php echo qui($data_komenti['parqui_suivi_eleve_cpe']); ?><br />Concerne : <strong><a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_komenti['eleve_suivi_eleve_cpe']; ?>" title="consulter le suivi élève"><?php echo qui_eleve($data_komenti['eleve_suivi_eleve_cpe']); ?></a></strong> de <?php echo classe_de($data_komenti['eleve_suivi_eleve_cpe']) ?><br />[ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_komenti['eleve_suivi_eleve_cpe']; ?>">lire</a> ]</span></p>
           <?php } ?>
	   </td>
       </tr>
    </table>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>


<?php if ($choix === 'alert' and $fiche_eleve === '' and $select_fiche_eleve === '') { ?>
<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<br />
    <table style="margin: auto; width: 700px;" border="0" cellspacing="1" cellpadding="0">
       <tr class="fond_rouge">
           <td colspan="1" class="titre_tableau_alert"><b>Système d'alert</b></td>
       </tr>
       <tr class="td_tableau_absence_1">
           <td class="norme_absence_min" style="text-align: center; width: 50%;">Les alerts</td>
       </tr>
       <tr class="td_tableau_absence_1">
           <td class="norme_absence_min" style="text-align: center; width: 50%;">

<form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>" name="form3">
      <fieldset style="width: 450px; margin: auto;">
         <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Profils des recherchers</div>
            <div class="norme_absence" style="text-align: center; background-color: #E8F1F4">
		<table style="border: 0px; text-align: center; width: 100%;"><tr><td>
                <select name="classe_multiple[]" size="5" multiple="multiple" tabindex="3">
		  <optgroup label="----- Listes des classes -----">
		    <?php
			if ($_SESSION["statut"] === 'cpe') {
	                        $requete_classe = mysql_query('SELECT * FROM '.$prefix_base.'classes, '.$prefix_base.'periodes WHERE '.$prefix_base.'periodes.id_classe = '.$prefix_base.'classes.id  GROUP BY id_classe ORDER BY '.$prefix_base.'classes.classe');
			} else {
		                        $requete_classe = mysql_query('SELECT * FROM '.$prefix_base.'classes, '.$prefix_base.'j_eleves_professeurs, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'periodes WHERE ('.$prefix_base.'j_eleves_professeurs.professeur="'.$_SESSION['login'].'" AND '.$prefix_base.'j_eleves_professeurs.professeur.login = '.$prefix_base.'j_eleves_classes.login AND '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id) AND '.$prefix_base.'periodes.id_classe = '.$prefix_base.'classes.id  GROUP BY id_classe ORDER BY '.$prefix_base.'classes.classe');
			       }
	  		while ($donner_classe = mysql_fetch_array($requete_classe))
		  	 {
				$requete_cpt_nb_eleve_1 =  mysql_query('SELECT count(*) FROM '.$prefix_base.'eleves, '.$prefix_base.'classes, '.$prefix_base.'j_eleves_classes WHERE '.$prefix_base.'classes.id = "'.$donner_classe['id_classe'].'" AND '.$prefix_base.'j_eleves_classes.id_classe='.$prefix_base.'classes.id AND '.$prefix_base.'j_eleves_classes.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login');
				$requete_cpt_nb_eleve = mysql_num_rows($requete_cpt_nb_eleve_1);
			   ?><option value="<?php echo $donner_classe['id_classe']; ?>" <?php if(!empty($classe_multiple) and in_array($donner_classe['id_classe'], $classe_multiple)) { ?>selected="selected"<?php } ?> onclick="javascript:document.form3.submit()"><?php echo $donner_classe['nom_complet']; ?> (eff. <?php echo $requete_cpt_nb_eleve; ?>)</option><?php
			 }
			?>
		  </optgroup>
		  </select></td><td>
		  <select name="eleve_multiple[]" size="5" multiple="multiple" tabindex="4">
		  <optgroup label="----- Listes des &eacute;l&egrave;ves -----">
		    <?php
			// sélection des id eleves sélectionné.
			if(!empty($classe_multiple[0]))
			{
				$cpt_classe_selec = 0; $selection_classe = "";
				while(!empty($classe_multiple[$cpt_classe_selec])) { if($cpt_classe_selec == 0) { $selection_classe = $prefix_base."j_eleves_classes.id_classe = ".$classe_multiple[$cpt_classe_selec]; } else { $selection_classe = $selection_classe." OR ".$prefix_base."j_eleves_classes.id_classe = ".$classe_multiple[$cpt_classe_selec]; } $cpt_classe_selec = $cpt_classe_selec + 1; }
	                        $requete_eleve = mysql_query('SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes WHERE ('.$selection_classe.') AND '.$prefix_base.'j_eleves_classes.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'eleves.nom ASC');
		  		while ($donner_eleve = mysql_fetch_array($requete_eleve))
			  	 {
				   ?><option value="<?php echo $donner_eleve['login']; ?>" <?php if(!empty($eleve_multiple) and in_array($donner_eleve['login'], $eleve_multiple)) { ?> selected="selected"<?php } ?>><?php echo strtoupper($donner_eleve['nom'])." ".ucfirst($donner_eleve['prenom']); ?></option><?php
				 }
			}
			?>
		     <?php if(empty($classe_multiple[0]) and empty($eleve_multiple[0])) { ?><option value="" disabled="disabled">Vide</option><?php } ?>
		  </optgroup>
		  </select></td>
		 </tr>
		 <tr>
		   <td colspan="2">
			<select name="ajout_type_alert" size="1" style="width: 200px;">
				<option>Type action</option>
				<option>Type lettre</option>
				<option>Fiche élève</option>
			</select>
			<input type="submit" name="Submit10" value="Ajouter" />
</td>
		 </tr>
		</table>
                du <input name="du" type="text" size="11" maxlength="11" value="<?php echo $du; ?>" /><a href="#calend" onclick="<?php  echo $cal->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
            </div>
      </fieldset>
    </form>


</td>
       </tr>
    </table>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>

	     <?php
// partie pour du débugage

/*	     $i = '0';
             $requete_komenti = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = 'baba'  AND type_absence_eleve = 'A' ORDER BY d_date_absence_eleve ASC, d_heure_absence_eleve DESC";
             $execution_komenti = mysql_query($requete_komenti) or die('Erreur SQL !'.$requete_komenti.'<br />'.mysql_error());
              while ( $donnee_base = mysql_fetch_array($execution_komenti))
                {
			$tableau[$i]['id'] = $i;
			$tableau[$i]['login'] = $donnee_base['eleve_absence_eleve'];
			$tableau[$i]['date_debut'] = $donnee_base['d_date_absence_eleve'];
			$tableau[$i]['date_fin'] = $donnee_base['a_date_absence_eleve'];
			$tableau[$i]['heure_debut'] = $donnee_base['d_heure_absence_eleve'];
			$tableau[$i]['heure_fin'] = $donnee_base['a_heure_absence_eleve'];
			$i = $i + 1;
		}

		$tab1 = crer_tableau_jaj($tableau);
		$tab2 = rech_tableau_heurepresent($tab1, '08:00:00', '09:00:00');*/
//$tab1 = repartire_jour('baba','A','2007-01-01','2007-06-01');
//echo '<pre>';
//print_r($tab1);
//echo '</pre>';

//$timestamps = timestamps_encode('13/12/2006', '08:00:00');
//echo $timestamps;
//echo $timestamps.'<br />';
//$time = timestamps_decode($timestamps, 'sql');
//echo $timestamps.' '.$time['date'].' '.$time['heure'];

//echo etabouvert('27/09/2006', '08:00:00', '09:00:00', '');

// fin partie pour du débugage

require("../../lib/footer.inc.php");
?>
