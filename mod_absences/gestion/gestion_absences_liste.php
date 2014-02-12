<?php
$niveau_arbo = 2;
// Initialisations
require_once("../../lib/initialisations.inc.php");
include("../lib/functions.php");
//debug_var();
// Resume session
$resultat_session = $session_gepi->security_check();//fraise
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
 {  if (empty($_GET['photo']) and empty($_POST['photo'])) {$photo="";}
    else { if (isset($_GET['photo'])) {$photo=$_GET['photo'];} if (isset($_POST['photo'])) {$photo=$_POST['photo'];} } }
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

///modification gestion dates pour impression depuis fiche élève et bon fonctionnement messager  didier
	 $date_ce_jour2 = date('d/m/Y');

if (empty($_GET['du']) and empty($_POST['du'])) {$du='';$du2=$date_ce_jour2;}
    else { if (isset($_GET['du'])) {$du=$_GET['du'];$du2=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];$du2=$_POST['du'];} }
	if (empty($_GET['au']) and empty($_POST['au'])) {$au="$date_ce_jour2";}
	 else { if (isset($_GET['au'])) {$au=$_GET['au'];} if (isset($_POST['au'])) {$au=$_POST['au'];} }
	 if(isset($du) and !empty($du)) { $date_ce_jour = date_sql($du); }
// fin de gestion des dates

// Gestion de l'affichage du TOP10 qui peut être autre chose que 10
$test_TOP = getSettingValue("absence_classement_top");
$TOP = isset($test_TOP) ? $test_TOP : '10'; // Par défaut on conserve le top10

function age($date_de_naissance_fr)
          {
            //à partir de la date de naissance, retourne l'âge dans la variable $age

            // date de naissance (partie à modifier)
              $ddn = $date_de_naissance_fr;

            // enregistrement de la date du jour
              $DATEDUJOUR = date("Y-m-d");
              $DATEFRAN = date("d/m/Y");

            // calcul de mon age d'après la date de naissance $ddn
              $annais = mb_substr("$ddn", 0, 4);
              $anjour = mb_substr("$DATEFRAN", 6, 4);
              $moisnais = mb_substr("$ddn", 4, 2);
              $moisjour = mb_substr("$DATEFRAN", 3, 2);
              $journais = mb_substr("$ddn", 6, 2);
              $jourjour = mb_substr("$DATEFRAN", 0, 2);

              $age = $anjour-$annais;
              if ($moisjour<$moisnais){$age=$age-1;}
              if ($jourjour<$journais && $moisjour==$moisnais){$age=$age-1;}
              return($age);
           }

         function pp($classe_choix)
          {
            global $prefix_base;
               $call_prof_classe = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM ".$prefix_base."classes, ".$prefix_base."j_eleves_professeurs, ".$prefix_base."j_eleves_classes WHERE ".$prefix_base."j_eleves_professeurs.login = ".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe = ".$prefix_base."classes.id AND ".$prefix_base."classes.classe = '".$classe_choix."'");
               $data_prof_classe = mysqli_fetch_array($call_prof_classe);
               $suivi_par = $data_prof_classe['suivi_par'];
               return($suivi_par);
          }

// On ajoute un paramètre sur les élèves de ce CPE en particulier
$sql_eleves_cpe = "SELECT e_login FROM j_eleves_cpe WHERE cpe_login = '".$_SESSION['login']."'";
$query_eleves_cpe = mysqli_query($GLOBALS["mysqli"], $sql_eleves_cpe) OR die('Erreur SQL ! <br />' . $sql_eleves_cpe . ' <br /> ' . mysqli_error($GLOBALS["mysqli"]));
$test_cpe = array();

$test_nbre_eleves_cpe = mysqli_num_rows($query_eleves_cpe);
while($test_eleves_cpe = mysqli_fetch_array($query_eleves_cpe)){
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
                             mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
			     if(!empty($id_suivi_eleve_cpe)) { $id_saisi = $id_suivi_eleve_cpe; } else { $id_saisi = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res); }
                             $verification = 1;

			     // si le support d'action est le courrier alors on ajout le courrier dans le suivi des courriers
			     if($support_suivi_eleve_cpe === '4' and !empty($lettre_type))
			      {
	                             $requete_courrier = "INSERT INTO ".$prefix_base."lettres_suivis (quirecois_lettre_suivi, partde_lettre_suivi, partdenum_lettre_suivi, quiemet_lettre_suivi, emis_date_lettre_suivi, emis_heure_lettre_suivi, envoye_date_lettre_suivi, envoye_heure_lettre_suivi, type_lettre_suivi, reponse_date_lettre_suivi, statu_lettre_suivi) VALUES ('".$eleve_suivi_eleve_cpe."', 'suivi_eleve_cpe', '".$id_saisi."', '".$_SESSION['login']."', '".$date_fiche."', '".$heure_fiche."', '', '', '".$lettre_type."', '', 'en attente')";
	                             mysqli_query($GLOBALS["mysqli"], $requete_courrier) or die('Erreur SQL !'.$requete_courrier.'<br />'.mysqli_error($GLOBALS["mysqli"]));
				     $courrier_suivi_eleve_cpe = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
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
            mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
 }

if ($action == "modifier")
 {
      $id_suivi_eleve_cpe = $_GET['id_suivi_eleve_cpe'];
      $requete_modif_fiche = 'SELECT * FROM '.$prefix_base.'suivi_eleve_cpe WHERE id_suivi_eleve_cpe="'.$id_suivi_eleve_cpe.'"';
      $resultat_modif_fiche = mysqli_query($GLOBALS["mysqli"], $requete_modif_fiche) or die('Erreur SQL !'.$requete_modif_fiche.'<br />'.mysqli_error($GLOBALS["mysqli"]));
      $data_modif_fiche = mysqli_fetch_array($resultat_modif_fiche);
 }

if ($action_sql === 'detacher_courrier')
 {
	$requete = "DELETE FROM ".$prefix_base."lettres_suivis WHERE id_lettre_suivi = '".$id_lettre_suivi."'";
	mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
 }

// requête liste des classes en fonction du cpe didier
if ($test_nbre_eleves_cpe === 0){


   	$requete_liste_classe = "SELECT id, classe, nom_complet FROM classes ORDER BY nom_complet ASC";

}
else

   {	$requete_liste_classe = "SELECT  id, classe, nom_complet FROM classes c, j_eleves_cpe jecp ,j_eleves_classes jec
                                 WHERE (jecp.cpe_login = '".$_SESSION['login']."' AND jecp.e_login=jec.login AND jec.id_classe=c.id )
								 GROUP BY id ORDER BY nom_complet ASC";
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

//Configuration du calendrier ajout pour impression didier
include("../../lib/calendrier/calendrier.class.php");
$cal = new Calendrier("form1", "du");
$cal_3 = new Calendrier("form3", "du");
$cal_4 = new Calendrier("form3", "au");

include "../lib/mincals_absences.inc";

?>

<script type="text/javascript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

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
function affichercacher(a) {

   c = a.substr(4);
   var b = document.getElementById(a);

	var f = "img_"+c+"";

       if (b.style.display == "none" || b.style.display == "") {
         b.style.display = "block";
	 document.images[f].src="../../images/fleche_a.gif";
       }
       else
       {
         b.style.display = "none";
	 document.images[f].src="../../images/fleche_na.gif";
       }
 }
function pagin(numpage){
	var laRequete = new Ajax.Updater('absences','gestion_absences_liste.php?classe_choix=<?php echo $classe_choix; ?>&type=<?php echo $type; ?>&choix=<?php echo $choix; ?>&date_ce_jour=<?php echo $date_ce_jour; ?>&photo=<?php echo $photo; ?>&numpage='+numpage,{method: 'get'});
}

//-->
</script>


<?php
// quelques variables
  $datej = date('Y-m-d');
  $annee_scolaire=annee_en_cours_t($datej);

 /* La page gestion global des absences */ ?>
<div id="absences">
<p class="bold"><a href='<?php if($select_fiche_eleve=='' and $fiche_eleve=='' and $choix!='lemessager') { ?>../../accueil.php<?php } else { ?>gestion_absences.php<?php } ?>'><img src="../../images/icons/back.png" alt="Retour" title="Retour" class="back_link" />&nbsp;Retour</a> |
<a href="./impression_absences.php?type=<?php echo $type; ?>&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Impression</a> |
<a href="statistiques.php?year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Statistiques</a> |
<a href="gestion_absences.php?choix=lemessager&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Le messager</a> |
<a href="alert_suivi.php?choix=alert&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Système d'alerte</a>
</p>

  <form method="post" action="gestion_absences.php?type=<?php echo $type; ?>&amp;choix=<?php echo $choix; ?>" name="choix_type_vs" style="margin: auto; text-align: center;">
   <fieldset class="fieldset_efface">
     <input name="date_ce_jour" type="hidden" value="<?php echo $date_ce_jour; ?>" />
     <select name="type" onchange="javascript:document.choix_type_vs.submit()">
        <option value="A" onclick="javascript:document.choix_type_vs.submit()" <?php if ($type=="A") {echo 'selected="selected"'; } ?>>Absences</option>
        <option value="R" onclick="javascript:document.choix_type_vs.submit()" <?php if ($type=="R") {echo 'selected="selected"'; } ?>>Retards</option>
        <option value="I" onclick="javascript:document.choix_type_vs.submit()" <?php if ($type=="I") {echo 'selected="selected"'; } ?>>Infirmerie</option>
        <option value="D" onclick="javascript:document.choix_type_vs.submit()" <?php if ($type=="D") {echo 'selected="selected"'; } ?>>Dispenses</option>
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
<?php //modification de l'affichage didier?>
[ <a href="gestion_absences.php?choix=top10&amp;type=<?php echo $type ?>">Top <?php echo $TOP; ?></a> | <a href="gestion_absences.php?choix=sm&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Absences non justifiées</a> | <a href="gestion_absences.php?choix=am&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Absences justifiées</a> ]
<?php } if($type == "R" and $fiche_eleve == "") { ?>
[ <a href="gestion_absences.php?choix=top10&amp;type=<?php echo $type; ?>">Top <?php echo $TOP; ?></a> | <a href="gestion_absences.php?choix=sm&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Retards non justifiés</a> | <a href="gestion_absences.php?choix=am&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Retards justifiés</a> ]
<?php } if($type == "I" and $fiche_eleve == "") { ?>
[ <a href="gestion_absences.php?choix=top10&amp;type=<?php echo $type; ?>">Top <?php echo $TOP; ?></a> | <a href="gestion_absences.php?choix=am&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Infirmerie avec motif</a> ]
<?php } if($type == "D" and $fiche_eleve == "") { ?>
[ <a href="gestion_absences.php?choix=top10&amp;type=<?php echo $type; ?>">Top <?php echo $TOP; ?></a> | <a href="gestion_absences.php?choix=sm&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Dispenses non justifiées</a> | <a href="gestion_absences.php?choix=am&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>">Dispenses justifiées</a> ]
<?php } ?>
</div>

<?php
	// On crée l'affichage du top10 des absences en tenant compte du réglage pour le TOP

	if ($choix=="top10" and $fiche_eleve == "" and $select_fiche_eleve == "") {
		$i = 0;
		if ($type == "A" or $type == "I" or $type == "R" or $type == "D") {
			if ($classe_choix != "") {
				// sans choix de classe, c'est le top 10 de l'établissement
				$requete_top10 = "SELECT e.login, e.elenoet, e.nom, e.prenom, e.sexe, COUNT(DISTINCT(ae.id_absence_eleve)) AS count
				FROM ".$prefix_base."absences_eleves ae, ".$prefix_base."eleves e, ".$prefix_base."j_eleves_classes ec
				WHERE e.login = ae.eleve_absence_eleve AND ae.type_absence_eleve = '".$type."' AND ec.login = e.login AND ec.id_classe = '".$classe_choix."'
				GROUP BY e.login ORDER BY count DESC LIMIT 0, ".$TOP;
			}elseif ($classe_choix == "") {
				$requete_top10 = "SELECT e.login, e.elenoet, e.nom, e.prenom, e.sexe, COUNT(ae.id_absence_eleve) AS count
				FROM ".$prefix_base."absences_eleves ae, ".$prefix_base."eleves e WHERE ( e.login = ae.eleve_absence_eleve AND ae.type_absence_eleve = '".$type."' )
				GROUP BY e.login ORDER BY count DESC LIMIT 0, ".$TOP;
			}
		}
		$execution_top10 = mysqli_query($GLOBALS["mysqli"], $requete_top10)
			or die('Erreur SQL !'.$requete_top10.'<br />'.mysqli_error($GLOBALS["mysqli"]));
		// On définit le margin_top pour la suite
			$margin_top = 50;
		while ( $data_top10 = mysqli_fetch_array($execution_top10)) {
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
                 $photos = $nom_photo;
                 //if ( $nom_photo === '' or !file_exists($photo) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                 if ( $nom_photo === NULL or !file_exists($photos) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php
                 } ?>
             </tr>
          </table>
       </div>
<?php } ?>

<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<table style="margin: auto; width: 800px; border: 0 0 0 0;">
  <tr style="vertical-align: top">
    <td class="td_tableau_gestion">
    <br />
      <table class="td_tableau_gestion" style="margin: auto; margin-top:15px; width: 600px; border: 0 0 0 0;">
        <tr>
          <td colspan="2" class="titre_tableau_gestion" nowrap><b>TOP <?php echo $TOP; ?></b></td>
        </tr>
        <?php
         $i = 0;
         $ic = 1;
         if ($type == "A" or $type == "I" or $type == "R" or $type == "D")
          {
            if ($classe_choix != "") { $requete_top10 = "SELECT e.login, e.elenoet, e.nom, e.prenom, e.sexe, COUNT(DISTINCT(ae.id_absence_eleve)) AS count
 			                           FROM ".$prefix_base."absences_eleves ae, ".$prefix_base."eleves e, ".$prefix_base."j_eleves_classes ec
									   WHERE e.login = ae.eleve_absence_eleve AND ae.type_absence_eleve = '".$type."' AND ec.login = e.login AND ec.id_classe = '".$classe_choix."'
									   GROUP BY e.login ORDER BY count DESC LIMIT 0, 10"; }
            if ($classe_choix == "") { $requete_top10 = "SELECT e.login, e.elenoet, e.nom, e.prenom, e.sexe, COUNT(ae.id_absence_eleve) AS count
			                           FROM ".$prefix_base."absences_eleves ae, ".$prefix_base."eleves e WHERE ( e.login = ae.eleve_absence_eleve AND ae.type_absence_eleve = '".$type."' )
									   GROUP BY e.login ORDER BY count DESC LIMIT 0, 10"; }
          }
         $execution_top10 = mysqli_query($GLOBALS["mysqli"], $requete_top10) or die('Erreur SQL !'.$requete_top10.'<br />'.mysqli_error($GLOBALS["mysqli"]));
         while ( $data_top10 = mysqli_fetch_array($execution_top10))
         {
                     if ($ic === '1') { $ic='2'; $couleur_cellule='td_tableau_absence_1'; } else { $couleur_cellule='td_tableau_absence_2'; $ic='1'; }
         ?>
        <tr>
		  <?php /* ajout classe eleve didier*/ ?>
          <td class="<?php echo $couleur_cellule; ?>" onmouseover="changementDisplay('d<?php echo $data_top10['login']; ?>', ''); return true;" onmouseout="changementDisplay('d<?php echo $data_top10['login']; ?>', ''); return true;"><a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_top10['login']; ?>" title="consulter la fiche de l'élève"><?php echo "<b>".strtoupper($data_top10['nom'])."</b> ".ucfirst($data_top10['prenom'])." (".classe_de($data_top10['login'])." )"; ?><a/></td>
          <td class="<?php echo $couleur_cellule; ?>">
            <?php if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo")) {
              $nom_photo = '';
              $nom_photo = nom_photo($data_top10['elenoet'],"eleves",2);
              $photos = $nom_photo;
              //if ( $nom_photo === '' or !file_exists($photo) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
              if ( $nom_photo === NULL or !file_exists($photos) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
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
          <select name="classe_choix" onchange="javascript:document.form1.submit()">
            <option value="" selected="selected" onclick="javascript:document.form1.submit()">Toutes les classes</option>
                <?php
				$resultat_liste_classe = mysqli_query($GLOBALS["mysqli"], $requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
				while ( $data_liste_classe = mysqli_fetch_array($resultat_liste_classe)) {
					if ($classe_choix == $data_liste_classe['id']) {
						$selected = "selected";
					} else {
						$selected = "";
					}?>
            <option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>  onclick="javascript:document.form1.submit()">
				<?php echo mb_substr($data_liste_classe['nom_complet'], 0, 50)." (".$data_liste_classe['classe'].")"; ?>
			</option>
                <?php
				} ?>
          </select>
           <?php if (getSettingValue("active_module_trombinoscopes")=='y')  { ?>
               <br />
               <input type="checkbox" name="photo" value="avec_photo" id="avecphoto" onClick="javascript:document.form1.submit()" <?php  if ($photo=="avec_photo") { ?>checked="checked"<?php } ?> /><label for="avecphoto" style="cursor: pointer;">Avec photo</label><br />
           <?php } ?>
               <br />
          TOP <?php echo $TOP; ?> des <?php if($type == "A") { ?>absences.<?php } if($type == "R") { ?>retards.<?php } if($type == "I") { ?>passages à l'infirmerie.<?php } if($type == "D") { ?>dispenses.<?php } ?>
	    </fieldset>
          </form>
    </td>
  </tr>
</table>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php
	} //fin du if ($choix=="top10" and $fiche_eleve == "" and $select_fiche_eleve == "")
?>

<?php


if ($choix=="sm" and $fiche_eleve == "" and $select_fiche_eleve == "") {
	$i = 0;
	if ($type == "A" or $type == "I" or $type == "R" or $type == "D") {

		if ($test_nbre_eleves_cpe === 0){

		if ($classe_choix != "") {
			$compte = "SELECT COUNT(*)/3 AS total
									FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae
									WHERE ( e.login = ae.eleve_absence_eleve AND e.login = jec.login AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve != 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."') ";
		} elseif ($classe_choix == "") {
		    $compte = " SELECT COUNT(*) AS total FROM ".$prefix_base."absences_eleves ae WHERE ( ae.justify_absence_eleve != 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' )";
					}
		}
		else
		{
		if ($classe_choix != "") {
			$compte = "SELECT COUNT(*)/3 AS total
									FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_cpe jecp, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae
									WHERE ( jecp.e_login=e.login AND jecp.cpe_login = '".$_SESSION['login']."' AND e.login = ae.eleve_absence_eleve AND e.login = jec.login AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve != 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."') ";
		} elseif ($classe_choix == "") {
		    $compte = " SELECT COUNT(*) AS total FROM ".$prefix_base."eleves e, ".$prefix_base."absences_eleves ae, ".$prefix_base."j_eleves_cpe jecp
			WHERE ( e.login = ae.eleve_absence_eleve AND jecp.e_login=e.login AND jecp.cpe_login='".$_SESSION['login']."' AND ae.justify_absence_eleve != 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' )";
					}
		}


	//pagination didier

  $retour_total=mysqli_query($GLOBALS["mysqli"], $compte);
	$donnees_total = mysqli_fetch_assoc($retour_total);
	$total = $donnees_total['total'];

  $messageParPage = 25;
  $nombreDePage = ceil($total/$messageParPage);


	if(isset($_GET['numpage'])){
    $pageActuelle = intval($_GET['numpage']);

    if($pageActuelle>$nombreDePage){
		  $pageActuelle = $nombreDePage;
    }
  }else{
    $pageActuelle = 1;
  }

$premiereEntree = ($pageActuelle-1)*$messageParPage;

if ($test_nbre_eleves_cpe === 0){
    if ($classe_choix != "")
    {
			$requete_sans_motif = "SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.sexe, ae.id_absence_eleve, ae.saisie_absence_eleve,
										ae.eleve_absence_eleve, ae.justify_absence_eleve, ae.info_justify_absence_eleve, ae.type_absence_eleve,
										ae.motif_absence_eleve, ae.d_date_absence_eleve, ae.a_date_absence_eleve, ae.d_heure_absence_eleve,
										ae.a_heure_absence_eleve, jec.login, jec.id_classe, jec.periode, jer.regime, c.classe, c.id, c.nom_complet
									FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae
									WHERE ( e.login = ae.eleve_absence_eleve AND e.login = jec.login AND e.login = jer.login  AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve != 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve  LIMIT ".$premiereEntree.",".$messageParPage."";
		}
		elseif ($classe_choix == "")
		{
      $requete_sans_motif = "SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.sexe, ae.id_absence_eleve, ae.saisie_absence_eleve,
										ae.eleve_absence_eleve, ae.justify_absence_eleve, ae.info_justify_absence_eleve, ae.type_absence_eleve,
										ae.motif_absence_eleve, ae.d_date_absence_eleve, ae.a_date_absence_eleve, ae.d_heure_absence_eleve,
										ae.a_heure_absence_eleve, jer.regime FROM ".$prefix_base."eleves e, ".$prefix_base."absences_eleves ae, ".$prefix_base."j_eleves_regime jer
										WHERE ( e.login = ae.eleve_absence_eleve AND e.login = jer.login AND ae.justify_absence_eleve != 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve ORDER BY nom,prenom,d_heure_absence_eleve LIMIT ".$premiereEntree.",".$messageParPage."";
	  }
}else
{
	 if ($classe_choix != "") {
			$requete_sans_motif = "SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.sexe, ae.id_absence_eleve, ae.saisie_absence_eleve,
										ae.eleve_absence_eleve, ae.justify_absence_eleve, ae.info_justify_absence_eleve, ae.type_absence_eleve,
										ae.motif_absence_eleve, ae.d_date_absence_eleve, ae.a_date_absence_eleve, ae.d_heure_absence_eleve,
										ae.a_heure_absence_eleve, jec.login, jec.id_classe, jec.periode, jer.regime, c.classe, c.id, c.nom_complet
									FROM ".$prefix_base."j_eleves_cpe jecp, ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae
									WHERE (jecp.e_login=e.login AND jecp.cpe_login = '".$_SESSION['login']."' AND e.login = ae.eleve_absence_eleve AND e.login = jec.login AND e.login = jer.login  AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve != 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve  LIMIT ".$premiereEntree.",".$messageParPage."";
		} elseif ($classe_choix == "") {
      $requete_sans_motif = "SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.sexe, ae.id_absence_eleve, ae.saisie_absence_eleve,
										ae.eleve_absence_eleve, ae.justify_absence_eleve, ae.info_justify_absence_eleve, ae.type_absence_eleve,
										ae.motif_absence_eleve, ae.d_date_absence_eleve, ae.a_date_absence_eleve, ae.d_heure_absence_eleve,
										ae.a_heure_absence_eleve, jer.regime
										FROM ".$prefix_base."j_eleves_cpe jecp, ".$prefix_base."eleves e, ".$prefix_base."absences_eleves ae, ".$prefix_base."j_eleves_regime jer
										WHERE ( jecp.e_login=e.login AND jecp.cpe_login = '".$_SESSION['login']."'  AND e.login = ae.eleve_absence_eleve AND e.login = jer.login AND ae.justify_absence_eleve != 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve ORDER BY nom,prenom,d_heure_absence_eleve LIMIT ".$premiereEntree.",".$messageParPage."";
	 }
}
	 $execution_sans_motif = mysqli_query($GLOBALS["mysqli"], $requete_sans_motif) or die('Erreur SQL !'.$requete_sans_motif.'<br />'.mysqli_error($GLOBALS["mysqli"]));

	}
	// Pour la position du premier div, on définit le margin-top mise à zero pour eviter clignotement si information rentrée
	$margin_top = 0;
	while ( $data_sans_motif = mysqli_fetch_array($execution_sans_motif))
	{

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
                 $photos = $nom_photo;
                 //if ( $nom_photo === '' or !file_exists($photo) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                 if ( $nom_photo === NULL or !file_exists($photos) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
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
					$nbre = count($info_responsable);
					for ($i = 0 ; $i < $nbre ; $i++){
						if ($info_responsable[$i]['resp_legal'] == '1') {

							$ident_resp = ' <span style="font-size: 0.8em;">(' . $info_responsable[$i]['nom'] . ' ' . $info_responsable[$i]['prenom'] . ' : resp n° ' . $info_responsable[$i]['resp_legal'] . ')</span>';
							if ( $info_responsable[$i]['tel_pers'] != '' ) {
								$telephone_pers = '<br />Pers. <strong>'.present_tel($info_responsable[$i]['tel_pers']).'</strong> ';
							}
							if ( $info_responsable[$i]['tel_prof'] != ''  ) {
								$telephone_prof = '<br />Prof. <strong>'.present_tel($info_responsable[$i]['tel_prof']).'</strong> ';
							}
							if ( $info_responsable[$i]['tel_port'] != ''  ) {
								$telephone_port = '<br />Port.<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="14" width="14" /> '.present_tel($info_responsable[$i]['tel_port']);
							}
						}
					}
				}

				if ( $telephone_pers != '' and $telephone_prof === '' ) { $telephone = $telephone_pers; }
				if ( $telephone_pers === '' and $telephone_prof != '' ) { $telephone = $telephone_prof; }
				if ( $telephone_pers != '' and $telephone_prof != '' ) { $telephone = $telephone_pers . ' ' . $telephone_prof; }
				if ( $telephone_pers === '' and $telephone_prof === '' and $telephone_port != '' ) { $telephone = $telephone_port . ' ! surtaxe'; }

				if ( $telephone_pers != '' or $telephone_prof != '' or $telephone_port != '' ) { $telephone = 'Téléphone responsable : '.$telephone; }
				else { $telephone = 'Aucun numéro de téléphone disponible'; }

				echo $telephone . $ident_resp . $telephone_port;

		  		?>
                </td>
              </tr>
           </table>
     </div>
<?php /* FIN DE GESTION DU CALQUE D'INFORMATION */

	} ?>


<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<table style="margin: auto; width: 800px; border: 0 0 0 0;">
  <tr style="vertical-align: top">
    <td class="td_tableau_gestion">
      <br />
      <form name ="form3" method="post" action="gestion_absences.php?choix=<?php echo $choix; ?>&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>&amp;action_sql=supprimer_selection">
       <fieldset class="fieldset_efface">
	   <?php /* modification affichage didier */ ?>
        <table class="td_tableau_gestion" style="margin: auto; margin-top:15px; width: 600px; border: 0 0 0 0;">
          <tr>
<?php /* modification affichage didier */ ?>
		   <td colspan="2" class="titre_tableau_gestion"><b><?php if ($type=="A") { ?>Absences non justifiées<?php } ?><?php if ($type=="R") { ?>Retards non justifiés<?php } ?><?php if ($type=="I") { ?>Infirmerie sans motif<?php } ?><?php if ($type=="D") { ?>Dispenses non justifiées<?php } ?></b></td>
          </tr>
          <?php
         $total = 0;
         $i = 0;
         $ic = 1;
           $execution_sans_motif = mysqli_query($GLOBALS["mysqli"], $requete_sans_motif) or die('Erreur SQL !'.$requete_sans_motif.'<br />'.mysqli_error($GLOBALS["mysqli"]));
		while ( $data_sans_motif = mysqli_fetch_array($execution_sans_motif))
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
            <td class="<?php echo $couleur_cellule; ?>" onmouseover="changementDisplay('d<?php echo $data_sans_motif['id_absence_eleve']; ?>', ''); return true;" onmouseout="changementDisplay('d<?php echo $data_sans_motif['id_absence_eleve']; ?>', ''); return true;">
         <?php // si pas de lettres envoyés  affiche  case à cocher et affiche icone suppression modif didier
            	$cpt_lettre_absence_recus = lettre_absence_envoye($data_sans_motif['id_absence_eleve']);
            	if ( $cpt_lettre_absence_recus == 0 ) {
				?>
				<input name="selection[<?php echo $total; ?>]" id="sel<?php echo $total; ?>" type="checkbox" value="1" <?php $varcoche = $varcoche."'sel".$total."',"; ?><?php /* if((isset($selection[$total]) and $selection[$total] == "1") OR $cocher == 1) { ?>checked="checked"<?php } */ ?> />
            	<input name="id_absence_eleve[<?php echo $total; ?>]" type="hidden" value="<?php echo $data_sans_motif['id_absence_eleve']; ?>" />
				<a href="ajout_<?php if($data_sans_motif['type_absence_eleve']=="A") { ?>abs<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>ret<?php } if($data_sans_motif['type_absence_eleve']=="D") { ?>dip<?php } if($data_sans_motif['type_absence_eleve']=="I") { ?>inf<?php } ?>.php?action=supprimer&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_sans_motif['id_absence_eleve']; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>" onClick="return confirm('Etes-vous sur de vouloir le supprimer...')"><img src="../images/icons/delete.png" style="width: 16px; height: 16px;" title="supprimer l'absence" border="0" alt="" /></a>
				<?php
				}
				else // sinon si lettre envoyé  n'affiche pas case à cocher et affiche icone delete_imp modif didier
				{$info_sup = 'du '.date_fr($data_sans_motif['d_date_absence_eleve']).' au '.date_fr($data_sans_motif['a_date_absence_eleve']);
				?>
				<img src="../images/icons/coche_imp.png" style="width: 20px; height: 20px;" title="Impossible de supprimer <?php if($data_sans_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_sans_motif['type_absence_eleve']=="D") { ?>la dispense<?php } if ($data_sans_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?> <?php echo $info_sup; ?>" border="0" alt="" />
				<input name="id_absence_eleve[<?php echo $total; ?>]" type="hidden" value="<?php echo $data_sans_motif['id_absence_eleve']; ?>" />
				<a href="#" onClick="alert('Pour le supprimer, supprimer la date d\'envoye du courrier.'); return false;"><img src="../images/icons/delete_imp.png" style="width: 16px; height: 16px;" title="Impossible de supprimer <?php if($data_sans_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_sans_motif['type_absence_eleve']=="D") { ?>la dispense<?php } if ($data_sans_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?> <?php echo $info_sup; ?>" border="0" alt="" /></a>
				<?php
				}
				?>
            	<a href="ajout_<?php if($data_sans_motif['type_absence_eleve']=="A") { ?>abs<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>ret<?php } if($data_sans_motif['type_absence_eleve']=="I") { ?>inf<?php } if($data_sans_motif['type_absence_eleve']=="D") { ?>dip<?php } ?>.php?action=modifier&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_sans_motif['id_absence_eleve']; ?>&amp;mode=eleve"><img src="../../images/icons/saisie.png" style="width: 16px; height: 16px;" title="modifier <?php if($data_sans_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_sans_motif['type_absence_eleve']=="D") { ?>la dispense<?php } if ($data_sans_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?>" border="0" alt="" /></a>
            	<?php /* ajout classe eleve didier*/ ?>
               <a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_sans_motif['login']; ?>" title="consulter la fiche de l'élève"><?php echo "<b>".strtoupper($data_sans_motif['nom'])."</b> ".ucfirst($data_sans_motif['prenom'])." (".$data_sans_motif['regime'].") (".classe_de($data_sans_motif['login'])." )"; ?></a>
            	</td>
            <td class="<?php echo $couleur_cellule; ?>">

              <?php if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo")) {
              	  $nom_photo = '';
                  $nom_photo = nom_photo($data_sans_motif['elenoet'],"eleves",2);
                  $photos = $nom_photo;
                  //if ( $nom_photo === '' or !file_exists($photo) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                  if ( $nom_photo === NULL or !file_exists($photos) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
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
		  <tr><td>
    <?php
    $content = '';
	  for ($i=1; $i <= $nombreDePage;$i++){
      $content .= '<a href="javascript:void(0);" onclick="pagin('.$i.');return false;">';
      if($i == $pageActuelle){
        $content .= '['.$i.']';
      }else{
      	$content .= $i;
      }
      $content .= ' </a>';
    }
    $content .= '</p>';

    echo $content;

		  ?>
		  </td><td></td></tr>
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
          <select name="classe_choix" onchange="javascript:document.form1.submit()">
            <option value="" selected onclick="javascript:document.form1.submit()">Toutes les classes</option>
                <?php
                  $resultat_liste_classe = mysqli_query($GLOBALS["mysqli"], $requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
                  while($data_liste_classe = mysqli_fetch_array($resultat_liste_classe)) {
                         if ($classe_choix==$data_liste_classe['id']) {$selected = "selected"; } else {$selected = ""; }?>
            <option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>  onclick="javascript:document.form1.submit()">
			<?php echo mb_substr($data_liste_classe['nom_complet'], 0, 50)." (".$data_liste_classe['classe'].")"; ?></option>
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

<?php /* fin du div de centrage du tableau pour ie5 */ ?>
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
		 if ($test_nbre_eleves_cpe === 0){
		 	 if ($classe_choix != "") {
			$compte = "SELECT COUNT(*)/3 AS total
									FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae
									WHERE ( e.login = ae.eleve_absence_eleve AND e.login = jec.login AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve != 'O' AND ae.type_absence_eleve = '".$type."' ) ";
		      } elseif ($classe_choix == "") {
		    $compte = " SELECT COUNT(*) AS total FROM ".$prefix_base."absences_eleves ae WHERE ( ae.justify_absence_eleve != 'O' AND ae.type_absence_eleve = '".$type."' )";
					}
			}
			else
			{
			 if ($classe_choix != "") {
			$compte = "SELECT COUNT(*)/3 AS total
									FROM ".$prefix_base."j_eleves_cpe jecp, ".$prefix_base."eleves e, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae
									WHERE ( e.login = ae.eleve_absence_eleve AND jecp.e_login=e.login AND jecp.cpe_login='".$_SESSION['login']."' AND e.login = jec.login AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve != 'O' AND ae.type_absence_eleve = '".$type."' ) ";
		      } elseif ($classe_choix == "") {
		    $compte = " SELECT COUNT(*) AS total
			                       FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_cpe jecp, ".$prefix_base."absences_eleves ae
								   WHERE (e.login = ae.eleve_absence_eleve AND jecp.e_login=e.login AND jecp.cpe_login='".$_SESSION['login']."' AND ae.justify_absence_eleve != 'O' AND ae.type_absence_eleve = '".$type."' )";
					}
			}
		 $retour_total=mysqli_query($GLOBALS["mysqli"], $compte);
	$donnees_total = mysqli_fetch_assoc($retour_total);
	$total = $donnees_total['total'];
	$messageParPage = 50;
  $nombreDePage = ceil($total/$messageParPage);

	if(isset($_GET['numpage'])){
    $pageActuelle = intval($_GET['numpage']);

    if($pageActuelle>$nombreDePage){
      $pageActuelle = $nombreDePage;
    }
  }else{
    $pageActuelle = 1;
  }

$premiereEntree = ($pageActuelle-1)*$messageParPage;
		 if ($test_nbre_eleves_cpe === 0){
           if ($classe_choix != "")
		   {
		   $requete_sans_motif ="SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.sexe, jer.regime, ae.id_absence_eleve, ae.eleve_absence_eleve,
                        		   ae.justify_absence_eleve, ae.type_absence_eleve, ae.motif_absence_eleve,ae.d_date_absence_eleve, ae.a_date_absence_eleve, ae.info_justify_absence_eleve, ae.d_heure_absence_eleve,
								   ae.a_heure_absence_eleve, jec.login, jec.id_classe, jec.periode, c.classe, c.id, c.nom_complet
								   FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae
								   WHERE (e.login = ae.eleve_absence_eleve AND  e.login = jer.login AND e.login = jec.login AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve != 'O' AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve LIMIT ".$premiereEntree.",".$messageParPage.""; }
           if ($classe_choix == "")
		   { $requete_sans_motif ="SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.sexe, jer.regime, ae.id_absence_eleve, ae.eleve_absence_eleve,
                         		   ae.justify_absence_eleve, ae.type_absence_eleve, ae.motif_absence_eleve, ae.info_justify_absence_eleve, ae.d_date_absence_eleve,
								   ae.a_date_absence_eleve, ae.d_heure_absence_eleve, ae.a_heure_absence_eleve
								   FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."absences_eleves ae
								   WHERE ( e.login = ae.eleve_absence_eleve AND e.login = jer.login AND ae.justify_absence_eleve!='O' AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve ORDER BY nom,prenom,d_heure_absence_eleve LIMIT ".$premiereEntree.",".$messageParPage.""; }
         }
		 else
		 {
		 if ($classe_choix != "")
		   {
		   $requete_sans_motif ="SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.sexe, jer.regime, ae.id_absence_eleve, ae.eleve_absence_eleve,
                        		   ae.justify_absence_eleve, ae.type_absence_eleve, ae.motif_absence_eleve,ae.d_date_absence_eleve, ae.a_date_absence_eleve, ae.info_justify_absence_eleve, ae.d_heure_absence_eleve,
								   ae.a_heure_absence_eleve, jec.login, jec.id_classe, jec.periode, c.classe, c.id, c.nom_complet
								   FROM ".$prefix_base."j_eleves_cpe jecp, ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae
								   WHERE (e.login = ae.eleve_absence_eleve AND jecp.e_login=e.login AND jecp.cpe_login='".$_SESSION['login']."' AND e.login = jer.login AND e.login = jec.login AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve != 'O' AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve LIMIT ".$premiereEntree.",".$messageParPage.""; }
           if ($classe_choix == "")
		   { $requete_sans_motif ="SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.sexe, jer.regime, ae.id_absence_eleve, ae.eleve_absence_eleve,
                         		   ae.justify_absence_eleve, ae.type_absence_eleve, ae.motif_absence_eleve, ae.info_justify_absence_eleve, ae.d_date_absence_eleve,
								   ae.a_date_absence_eleve, ae.d_heure_absence_eleve, ae.a_heure_absence_eleve
								   FROM ".$prefix_base."j_eleves_cpe jecp, ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."absences_eleves ae
								   WHERE ( e.login = ae.eleve_absence_eleve AND jecp.e_login=e.login AND jecp.cpe_login='".$_SESSION['login']."' AND e.login = jer.login AND ae.justify_absence_eleve!='O' AND ae.type_absence_eleve = '".$type."' ) GROUP BY id_absence_eleve ORDER BY nom,prenom,d_heure_absence_eleve LIMIT ".$premiereEntree.",".$messageParPage.""; }
		 }
		$execution_sans_motif = mysqli_query($GLOBALS["mysqli"], $requete_sans_motif)
			or die('Erreur SQL !'.$requete_sans_motif.'<br />'.mysqli_error($GLOBALS["mysqli"]));
			}
		// Pour la position du premier div, on définit le margin-top mise à zero pour éviter clignotement si information saisie didier
			$margin_top = 0;

		while($data_sans_motif = mysqli_fetch_array($execution_sans_motif))
		{


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
                 $photos = $nom_photo;
                 //if ( $nom_photo === '' or !file_exists($photo) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                 if ( $nom_photo === NULL or !file_exists($photos) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		      $valeur=redimensionne_image($photos);
                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php
				 } ?>
            </tr>
            <tr>
               <td class="norme_absence">pour le motif : <?php echo motif_de($data_sans_motif['motif_absence_eleve']); ?></td>
             </tr>
             <tr>
                <td class="norme_absence"><?php if ($data_sans_motif['justify_absence_eleve'] == "O") {?><span class="norme_absence_vert"><b>a donn&eacute; pour justification : </b>
										<?php } elseif($data_sans_motif['justify_absence_eleve'] == "T") { ?><span class="norme_absence_vert" style="color: orange;"><b>a justifi&eacute; par t&eacute;l&eacute;phone : </b>
																						<?php } else { ?><span class="norme_absence_rouge"><b>n'a pas donn&eacute; de justification</b>
																							<?php }?></span></td>
             </tr>
             <tr>
                <td class="norme_absence"><?php if(!empty($data_sans_motif['info_justify_absence_eleve'])) { ?><blockquote><?php echo $data_sans_motif['info_justify_absence_eleve']; ?></blockquote><?php } ?></td>
             </tr>
             <tr class="texte_fondjaune_calque_information">
                <td colspan="3">
                <?php

				// affichage des numéros de téléphone
				$info_responsable = tel_responsable($data_sans_motif['ele_id']);

				$telephone = ''; $telephone_pers = ''; $telephone_prof = ''; $telephone_port = '';

				if ( !empty($info_responsable) )
				{
					$nbre = count($info_responsable);
					for ($i = 0 ; $i < $nbre ; $i++){
						if ($info_responsable[$i]['resp_legal'] == '1') {

							$ident_resp = ' <span style="font-size: 0.8em;">(' . $info_responsable[$i]['nom'] . ' ' . $info_responsable[$i]['prenom'] . ' : resp n° ' . $info_responsable[$i]['resp_legal'] . ')</span>';
							if ( $info_responsable[$i]['tel_pers'] != '' ) {
								$telephone_pers = '<br />Pers. <strong>'.present_tel($info_responsable[$i]['tel_pers']).'</strong> ';


							}
							if ( $info_responsable[$i]['tel_prof'] != ''  ) {
								$telephone_prof = '<br />Prof. <strong>'.present_tel($info_responsable[$i]['tel_prof']).'</strong> ';
							}
							if ( $info_responsable[$i]['tel_port'] != ''  ) {
								$telephone_port = '<br />Port.<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="14" width="14" /> '.present_tel($info_responsable[$i]['tel_port']);
							}
						}
					}
				}

				if ( $telephone_pers != '' and $telephone_prof === '' ) { $telephone = $telephone_pers; }
				if ( $telephone_pers === '' and $telephone_prof != '' ) { $telephone = $telephone_prof; }
				if ( $telephone_pers != '' and $telephone_prof != '' ) { $telephone = $telephone_pers . ' ' . $telephone_prof; }
				if ( $telephone_pers === '' and $telephone_prof === '' and $telephone_port != '' ) { $telephone = $telephone_port . ' ! surtaxe'; }

				if ( $telephone_pers != '' or $telephone_prof != '' or $telephone_port != '' ) { $telephone = 'Téléphone responsable : '.$telephone; }
				else { $telephone = 'Aucun numéro de téléphone disponible'; }

				echo $telephone . $ident_resp . $telephone_port;

		  		?>
                </td>
              </tr>
          </table>
       </div>
<?php		}

	} ?>


<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<table style="margin: auto; width: 800px; border: 0 0 0 0;">
  <tr style="vertical-align: top">
    <td class="td_tableau_gestion">
    <br />
     <form name ="form3" method="post" action="gestion_absences.php?choix=<?php echo $choix; ?>&amp;type=<?php echo $type; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>&amp;action_sql=supprimer_selection">
      <fieldset class="fieldset_efface">
        <table class="td_tableau_gestion" style="margin: auto; width: 600px;margin-top:15px; border: 0 0 0 0;">
        <tr>
		<?php /* modification affichage didier */ ?>
          <td colspan="2" class="titre_tableau_gestion" nowrap><b><?php if ($type=="A") { ?>Absences non justifiées <?php } ?><?php if ($type=="R") { ?>Retards non justifiés<?php } ?><?php if ($type=="I") { ?>Infirmerie sans motif<?php } ?><?php if ($type=="D") { ?>Dispenses non justifiées<?php } ?></b></td>
        </tr>
        <?php
         $total = 0;
         $i = 0;
         $ic = 1;
         $execution_sans_motif = mysqli_query($GLOBALS["mysqli"], $requete_sans_motif) or die('Erreur SQL !'.$requete_sans_motif.'<br />'.mysqli_error($GLOBALS["mysqli"]));
         while ( $data_sans_motif = mysqli_fetch_array($execution_sans_motif))
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
          <td class="<?php echo $couleur_cellule; ?>" onmouseover="changementDisplay('d<?php echo $data_sans_motif['id_absence_eleve']; ?>', ''); return true;" onmouseout="changementDisplay('d<?php echo $data_sans_motif['id_absence_eleve']; ?>', ''); return true;">
        <?php // si pas de lettres envoyés  affiche  case à cocher et affiche icone suppression modif didier
            	$cpt_lettre_absence_recus = lettre_absence_envoye($data_sans_motif['id_absence_eleve']);
            	if ( $cpt_lettre_absence_recus == 0 ) {
				?>
          	<input name="selection[<?php echo $total; ?>]" id="sel<?php echo $total; ?>" type="checkbox" value="1" <?php $varcoche = $varcoche."'sel".$total."',"; ?> <?php /* if((isset($selection[$total]) and $selection[$total]) == "1" OR $cocher == 1) { ?>checked="checked"<?php } */ ?> /><a href="ajout_<?php if($data_sans_motif['type_absence_eleve']=="A") { ?>abs<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>ret<?php } if($data_sans_motif['type_absence_eleve']=="D") { ?>dip<?php } if($data_sans_motif['type_absence_eleve']=="I") { ?>inf<?php } ?>.php?action=supprimer&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_sans_motif['id_absence_eleve']; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>" onClick="return confirm('Etes-vous sur de vouloir le supprimer...')"><img src="../images/icons/delete.png" style="width: 16px; height: 16px;" title="supprimer l'absence" border="0" alt="" /></a>
            <?php
				}
				else // sinon impossible de supprimer absence didier
				{
				 $info_sup = 'du '.date_fr($data_sans_motif['d_date_absence_eleve']).' au '.date_fr($data_sans_motif['a_date_absence_eleve']);
				?><img src="../images/icons/coche_imp.png" style="width: 18px; height: 18px;" title="Impossible de supprimer <?php if($data_sans_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_sans_motif['type_absence_eleve']=="D") { ?>la dispense<?php } if ($data_sans_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?> <?php echo $info_sup; ?>" border="0" alt="" />
				<input name="id_absence_eleve[<?php echo $total; ?>]" type="hidden" value="<?php echo $data_sans_motif['id_absence_eleve']; ?>" />
				<a href="#" onClick="alert('Pour le supprimer, supprimer la date d\'envoye du courrier.'); return false;"><img src="../images/icons/delete_imp.png" style="width: 16px; height: 16px;" title="Impossible de supprimer <?php if($data_sans_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_sans_motif['type_absence_eleve']=="D") { ?>la dispense<?php } if ($data_sans_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?> <?php echo $info_sup; ?>" border="0" alt="" /></a>
				<?php
				}
				?>
          	<input name="id_absence_eleve[<?php echo $total; ?>]" type="hidden" value="<?php echo $data_sans_motif['id_absence_eleve']; ?>" />
          	<a href="ajout_<?php if($data_sans_motif['type_absence_eleve']=="A") { ?>abs<?php } if($data_sans_motif['type_absence_eleve']=="D") { ?>dip<?php } if($data_sans_motif['type_absence_eleve']=="I") { ?>inf<?php } if ($data_sans_motif['type_absence_eleve']=="R") { ?>ret<?php } ?>.php?action=modifier&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_sans_motif['id_absence_eleve']; ?>&amp;mode=eleve"><img src="../../images/icons/saisie.png" title="modifier l'absence" style="width: 16px; height: 16px;" border="0" alt="" /></a>
			<a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_sans_motif['login']; ?>" title="consulter la fiche de l'élève"><?php echo "<b>".strtoupper($data_sans_motif['nom'])."</b> ".ucfirst($data_sans_motif['prenom']) . " (" . $data_sans_motif['regime'] . ") (". classe_de($data_sans_motif['login']) ." )"; ?></a>

          </td>
          <td class="<?php echo $couleur_cellule; ?>">
            <?php
				if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo"))
				{
					$id_eleve = $data_sans_motif['id_absence_eleve'];
                    $id_eleve_photo = $data_sans_motif['elenoet'];
					$nom_photo = '';
                    $nom_photo = nom_photo($id_eleve_photo,"eleves",2);
                    $photos = $nom_photo;

					//if ( $nom_photo === '' or !file_exists($photos) ) {
					if ( $nom_photo === NULL or !file_exists($photos) ) {
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

          <td class="class55bas">&nbsp;</td>
          <td class="class35bas">&nbsp;</td>
        </tr>
        <tr>

          <td colspan="2">
		<?php /* <a href="gestion_absences.php?choix=<?php echo $choix; ?>&amp;date_ce_jour=<?php echo date_fr($date_ce_jour); ?>&amp;cocher=<?php if($cocher==1) { ?>0<?php } else { ?>1<?php  } ?>"><?php if($cocher==1) { ?>déc<?php } else { ?>C<?php  } ?>ocher toutes les cellules</a> */ ?>
		<?php $varcoche = $varcoche."'form3'"; ?>
		<a href="javascript:CocheCheckbox(<?php echo $varcoche; ?>)">Cocher</a> | <a href="javascript:DecocheCheckbox(<?php echo $varcoche; ?>)">Décocher</a>	<input name="date_ce_jour" type="hidden" value="<?php echo $date_ce_jour; ?>" /><input name="submit2" type="image" src="../../images/delete16.png" title="supprimer la s&eacute;lection rapide" style="border: 0px;" onClick="return confirm('Etes-vous sur de vouloir le supprimer...')" />
	  </td>
        </tr>
		<tr><td>
		<?php
		$content = '';
		for ($i=1; $i <= $nombreDePage;$i++){
      $content .= '<a href="javascript:void(0);" onclick="pagin('.$i.');return false;">';
      if($i == $pageActuelle){
        $content .= '['.$i.']';
      }else{
        $content .= $i;
      }
      $content .= ' </a>';
    }
    $content .= '</p>';

    echo $content;

		  ?>
		  </td><td></td></tr>
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
          <select name="classe_choix" onChange="javascript:document.form1.submit()">
            <option value="" selected onClick="javascript:document.form1.submit()">Toutes les classes</option>
                <?php
                  $resultat_liste_classe = mysqli_query($GLOBALS["mysqli"], $requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
                  while ( $data_liste_classe = mysqli_fetch_array($resultat_liste_classe)) {
                         if ($classe_choix==$data_liste_classe['id']) {$selected = "selected"; } else {$selected = ""; }?>
            <option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>  onclick="javascript:document.form1.submit()">
			<?php echo mb_substr($data_liste_classe['nom_complet'], 0, 50)." (".$data_liste_classe['classe'].")"; ?></option>
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
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>

<?php if ($choix=="am" and $fiche_eleve == "" and $select_fiche_eleve == "") { ?>

<?php
		$i = 0;
		if ($type == "A" or $type == "I" or $type == "R" or $type == "D") {
		 if ($test_nbre_eleves_cpe === 0){
		 if ($classe_choix != "") {
			$compte = "SELECT COUNT(*)/3 AS total
									FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae
									WHERE ( e.login = ae.eleve_absence_eleve AND e.login = jec.login AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve = 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' ) ";
		} elseif ($classe_choix == "") {
		    $compte = " SELECT COUNT(*) AS total FROM ".$prefix_base."absences_eleves ae WHERE ( ae.justify_absence_eleve = 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' )";
					}
		}
     else
       {
         if ($classe_choix != "") {
			$compte = "SELECT COUNT(*)/3 AS total
									FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_cpe jecp, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae
									WHERE ( jecp.e_login=e.login AND jecp.cpe_login = '".$_SESSION['login']."' AND e.login = ae.eleve_absence_eleve AND e.login = jec.login AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve = 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."') ";
		} elseif ($classe_choix == "") {
		    $compte = " SELECT COUNT(*) AS total FROM ".$prefix_base."eleves e, ".$prefix_base."absences_eleves ae, ".$prefix_base."j_eleves_cpe jecp
			WHERE ( e.login = ae.eleve_absence_eleve AND jecp.e_login=e.login AND jecp.cpe_login='".$_SESSION['login']."' AND ae.justify_absence_eleve = 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' )";
					}
       }
		 $retour_total=mysqli_query($GLOBALS["mysqli"], $compte);
	$donnees_total = mysqli_fetch_assoc($retour_total);
	$total = $donnees_total['total'];
	//echo $total;
    $messageParPage = 25;
    $nombreDePage = ceil($total/$messageParPage);
	//echo $nombreDePage;

	if(isset($_GET['numpage'])){
    $pageActuelle = intval($_GET['numpage']);

    if($pageActuelle>$nombreDePage){
      $pageActuelle = $nombreDePage;
    }
  }else{
    $pageActuelle = 1;
  }

 // echo $pageActuelle;
$premiereEntree = ($pageActuelle-1)*$messageParPage;
    if ($test_nbre_eleves_cpe === 0){
			if ($classe_choix != "") {
				$requete_avec_motif = "SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.elenoet, e.sexe, jer.regime, ae.id_absence_eleve, ae.eleve_absence_eleve,
				ae.justify_absence_eleve,ae.type_absence_eleve, ae.motif_absence_eleve, ae.info_justify_absence_eleve, ae.d_date_absence_eleve, ae.d_heure_absence_eleve,
				ae.a_heure_absence_eleve, ae.a_date_absence_eleve, jec.login, jec.id_classe, jec.periode, c.id, c.classe, c.nom_complet
				FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae
				WHERE ( e.login = ae.eleve_absence_eleve AND e.login = jer.login AND e.login = jec.login AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve = 'O'
				AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."' ) AND ae.type_absence_eleve = '".$type."' )
				GROUP BY id_absence_eleve LIMIT ".$premiereEntree.",".$messageParPage."";
			}
			if ($classe_choix == "") {
				$requete_avec_motif = "SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.elenoet, e.sexe, jer.regime, ae.id_absence_eleve, ae.eleve_absence_eleve,
					ae.justify_absence_eleve,ae.type_absence_eleve, ae.motif_absence_eleve, ae.info_justify_absence_eleve, ae.d_date_absence_eleve, ae.d_heure_absence_eleve,
					ae.a_heure_absence_eleve, ae.a_date_absence_eleve
					FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."absences_eleves ae
					WHERE ( e.login = ae.eleve_absence_eleve AND e.login = jer.login AND ae.justify_absence_eleve = 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' )
				GROUP BY id_absence_eleve
				ORDER BY nom,prenom,d_heure_absence_eleve LIMIT ".$premiereEntree.",".$messageParPage."";
			}
        }
		else
		{
		  if ($classe_choix != "") {
				$requete_avec_motif = "SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.elenoet, e.sexe, jer.regime, ae.id_absence_eleve, ae.eleve_absence_eleve,
				ae.justify_absence_eleve,ae.type_absence_eleve, ae.motif_absence_eleve, ae.info_justify_absence_eleve, ae.d_date_absence_eleve, ae.d_heure_absence_eleve,
				ae.a_heure_absence_eleve, ae.a_date_absence_eleve, jec.login, jec.id_classe, jec.periode, c.id, c.classe, c.nom_complet
				FROM ".$prefix_base."j_eleves_cpe jecp, ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c, ".$prefix_base."absences_eleves ae
				WHERE (jecp.e_login=e.login AND jecp.cpe_login = '".$_SESSION['login']."' AND e.login = ae.eleve_absence_eleve AND e.login = jer.login AND e.login = jec.login AND jec.id_classe = c.id AND c.id = '".$classe_choix."' AND ae.justify_absence_eleve = 'O'
				AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."' ) AND ae.type_absence_eleve = '".$type."' )
				GROUP BY id_absence_eleve LIMIT ".$premiereEntree.",".$messageParPage."";
			}
			if ($classe_choix == "") {
				$requete_avec_motif = "SELECT e.ele_id, e.elenoet, e.login, e.nom, e.prenom, e.elenoet, e.sexe, jer.regime, ae.id_absence_eleve, ae.eleve_absence_eleve,
					ae.justify_absence_eleve,ae.type_absence_eleve, ae.motif_absence_eleve, ae.info_justify_absence_eleve, ae.d_date_absence_eleve, ae.d_heure_absence_eleve,
					ae.a_heure_absence_eleve, ae.a_date_absence_eleve
					FROM ".$prefix_base."j_eleves_cpe jecp, ".$prefix_base."eleves e, ".$prefix_base."j_eleves_regime jer, ".$prefix_base."absences_eleves ae
					WHERE (jecp.e_login=e.login AND jecp.cpe_login = '".$_SESSION['login']."' AND e.login = ae.eleve_absence_eleve AND e.login = jer.login AND ae.justify_absence_eleve = 'O' AND ( ae.d_date_absence_eleve <= '".$date_ce_jour."' AND ae.a_date_absence_eleve >= '".$date_ce_jour."') AND ae.type_absence_eleve = '".$type."' )
				GROUP BY id_absence_eleve
				ORDER BY nom,prenom,d_heure_absence_eleve LIMIT ".$premiereEntree.",".$messageParPage."";
			}
		}
		$execution_avec_motif = mysqli_query($GLOBALS["mysqli"], $requete_avec_motif)
		 	or die('Erreur SQL !'.$requete_avec_motif.'<br />'.mysqli_error($GLOBALS["mysqli"]));
		// On construit alors le div de la fiche élève
		// Pour la position du premier div, on définit le margin-top mise a zero pour eviter clignotement si information saisie didier
			$margin_top = 0;
		while ( $data_avec_motif = mysqli_fetch_array($execution_avec_motif)) {

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
			 echo "<b>".$data_avec_motif['nom']."</b> ".$data_avec_motif['prenom']; ?> élève de <?php echo "<b>".classe_de($data_avec_motif['login'])."</b>";
			 $id_classe_eleve = classe_de($data_avec_motif['login']); ?>
			 <br />
			 <?php //ajout creneau horaire dans div didier ?>
			 <?php if ($data_avec_motif['type_absence_eleve']=="A") { ?> a été absent<?php if ($data_avec_motif['sexe'] == "F") { ?>e<?php } }
      			 if  ($data_avec_motif['type_absence_eleve']=="R") { ?> est arrivé<?php if ($data_avec_motif['sexe'] == "F") { ?>e<?php } ?> en retard<?php } ?>
				 <?php if ($data_avec_motif['type_absence_eleve']=="I") { ?>est allé à l'infirmerie<?php } ?>
				 <br />
				 le <?php echo date_frl($data_avec_motif['d_date_absence_eleve']); ?>
				 <?php if (($data_avec_motif['a_date_absence_eleve'] != $data_avec_motif['d_date_absence_eleve'] and $data_avec_motif['a_date_absence_eleve'] != "") or $data_avec_motif['a_date_absence_eleve'] == "0000-00-00") { ?> au <?php echo date_frl($data_avec_motif['a_date_absence_eleve']); } ?>
				 <br />
				 <?php if ($data_avec_motif['a_heure_absence_eleve'] == "" or $data_avec_motif['a_heure_absence_eleve'] == "00:00:00") { ?>à <?php } else { ?>de <?php } ?><?php echo heure($data_avec_motif['d_heure_absence_eleve']); ?> <?php if ($data_avec_motif['a_heure_absence_eleve'] == "00:00:00" or $data_avec_motif['a_heure_absence_eleve'] == "") { } else { echo 'à '.heure($data_avec_motif['a_heure_absence_eleve']); } ?></td>


<?php
				if (getSettingValue("active_module_trombinoscopes")=='y') {
					echo "<td style=\"width: 60px; vertical-align: top\" rowspan=\"4\">";
					$nom_photo = '';
					$nom_photo = nom_photo($data_avec_motif['elenoet'],"eleves",2);
          			$photos = $nom_photo;
					//if ( $nom_photo === '' or !file_exists($photo) ) {
					if ( $nom_photo === NULL or !file_exists($photos) ) {
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

				// affichage des numéros de téléphone
				$info_responsable = tel_responsable($data_avec_motif['ele_id']);

				$telephone = ''; $telephone_pers = ''; $telephone_prof = ''; $telephone_port = '';

				if ( !empty($info_responsable) )
				{
					$nbre = count($info_responsable);
					for ($i = 0 ; $i < $nbre ; $i++){
						if ($info_responsable[$i]['resp_legal'] == '1') {

							$ident_resp = ' <span style="font-size: 0.8em;">(' . $info_responsable[$i]['nom'] . ' ' . $info_responsable[$i]['prenom'] . ' : resp n° ' . $info_responsable[$i]['resp_legal'] . ')</span>';
							if ( $info_responsable[$i]['tel_pers'] != '' ) {
								$telephone_pers = '<br />Pers. <strong>'.present_tel($info_responsable[$i]['tel_pers']).'</strong> ';


							}
							if ( $info_responsable[$i]['tel_prof'] != ''  ) {
								$telephone_prof = '<br />Prof. <strong>'.present_tel($info_responsable[$i]['tel_prof']).'</strong> ';
							}
							if ( $info_responsable[$i]['tel_port'] != ''  ) {
								$telephone_port = '<br />Port.<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="14" width="14" /> '.present_tel($info_responsable[$i]['tel_port']);
							}
						}
					}
				}

				if ( $telephone_pers != '' and $telephone_prof === '' ) { $telephone = $telephone_pers; }
				if ( $telephone_pers === '' and $telephone_prof != '' ) { $telephone = $telephone_prof; }
				if ( $telephone_pers != '' and $telephone_prof != '' ) { $telephone = $telephone_pers . ' ' . $telephone_prof; }
				if ( $telephone_pers === '' and $telephone_prof === '' and $telephone_port != '' ) { $telephone = $telephone_port . ' ! surtaxe'; }

				if ( $telephone_pers != '' or $telephone_prof != '' or $telephone_port != '' ) { $telephone = 'Téléphone responsable : '.$telephone; }
				else { $telephone = 'Aucun numéro de téléphone disponible'; }

				echo $telephone . $ident_resp . $telephone_port;

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


<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<table style="margin: auto; width: 800px;" border="0" cellspacing="0" cellpadding="1">
  <tr style="vertical-align: top">
    <td class="td_tableau_gestion">
  <br />
  <form name ="form3" method="post" action="gestion_absences.php?choix=<?php echo $choix; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>&amp;type=<?php echo $type; ?>&amp;action_sql=supprimer_selection">
   <fieldset class="fieldset_efface">
    <table class="td_tableau_gestion"  style="width: 600px;margin:auto;margin-top:15px; ">
        <tr>
		<?php /* modification affichage didier */ ?>
          <td colspan="2" class="titre_tableau_gestion" nowrap><b><?php if ($type=="A") { ?>Absences justifiées<?php } ?><?php if ($type=="R") { ?>Retards justifiés<?php } ?><?php if ($type=="I") { ?>Infirmerie avec motif<?php } ?><?php if ($type=="D") { ?>Dispenses justifiées<?php } ?></b></td>
        </tr>
<?php
		$total = 0;
		$i = 0;
		$ic = 1;
		$execution_avec_motif = mysqli_query($GLOBALS["mysqli"], $requete_avec_motif)
			or die('Erreur SQL !'.$requete_avec_motif.'<br />'.mysqli_error($GLOBALS["mysqli"]));
		while ( $data_avec_motif = mysqli_fetch_array($execution_avec_motif)) {

			if ($ic==1) {
				$ic=2;
				$couleur_cellule="td_tableau_absence_1";
			} else {
				$ic=1;
				$couleur_cellule="td_tableau_absence_2";
			}
?>
	<tr>
	      <?php // modif didier pour affichage div dispense ?>
		 <td class="<?php echo $couleur_cellule; ?>" onmouseover="changementDisplay('d<?php echo $data_avec_motif['id_absence_eleve']; ?>', ''); return true;" onmouseout="changementDisplay('d<?php echo $data_avec_motif['id_absence_eleve']; ?>', ''); return true;">
		<input name="selection[<?php echo $total; ?>]" id="sel<?php echo $total; ?>" type="checkbox" value="1" <?php $varcoche = $varcoche."'sel".$total."',"; ?> <?php /* if((isset($selection[$total]) and $selection[$total] == "1") OR $cocher == 1) { ?>checked="checked"<?php } */ ?> />
		<input name="id_absence_eleve[<?php echo $total; ?>]" type="hidden" value="<?php echo $data_avec_motif['id_absence_eleve']; ?>" />
		<?php

            	$cpt_lettre_absence_recus = lettre_absence_envoye($data_avec_motif['id_absence_eleve']);
            	if ( $cpt_lettre_absence_recus != 0 )
            	{

					$info_sup = 'du '.date_fr($data_avec_motif['d_date_absence_eleve']).' au '.date_fr($data_avec_motif['a_date_absence_eleve']);
					?><a href="#" onClick="alert('Pour le supprimer, supprimez la date d\'envoi du courrier.'); return false;"><img src="../images/icons/delete_imp.png" style="width: 16px; height: 16px;" title="Impossible de supprimer <?php if($data_avec_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_avec_motif['type_absence_eleve']=="D") { ?>la dispence<?php } if ($data_avec_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?> <?php echo $info_sup; ?>" border="0" alt="" /></a><?php

				}
				else
				{

            		?><a href="ajout_<?php if($data_avec_motif['type_absence_eleve']=="A") { ?>abs<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>ret<?php } if ($data_avec_motif['type_absence_eleve']=="D") { ?>dip<?php } if ($data_avec_motif['type_absence_eleve']=="I") { ?>inf<?php } ?>.php?action=supprimer&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_avec_motif['id_absence_eleve']; ?>&amp;date_ce_jour=<?php echo $date_ce_jour; ?>" <?php $info_sup = 'du '.date_fr($data_avec_motif['d_date_absence_eleve']).' au '.date_fr($data_avec_motif['a_date_absence_eleve']); ?>onClick="return confirm('Etes-vous sur de vouloir le supprimer <?php if($data_avec_motif['type_absence_eleve']=="A") { ?>l\'absence<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_avec_motif['type_absence_eleve']=="D") { ?>la dispence<?php } if ($data_avec_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?> <?php echo $info_sup; ?>.')"><img src="../../images/icons/delete.png" style="width: 16px; height: 16px;" title="supprimer <?php if($data_avec_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_avec_motif['type_absence_eleve']=="D") { ?>la dispence<?php } if ($data_avec_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?> <?php echo $info_sup; ?>" border="0" alt="" /></a><?php


				} ?>

		<a href="ajout_<?php if($data_avec_motif['type_absence_eleve']=="A") { ?>abs<?php } if($data_avec_motif['type_absence_eleve']=="D") { ?>dip<?php } if($data_avec_motif['type_absence_eleve']=="I") { ?>inf<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>ret<?php } ?>.php?action=modifier&amp;type=<?php echo $type; ?>&amp;id=<?php echo $data_avec_motif['id_absence_eleve']; ?>&amp;mode=eleve"><img src="../../images/icons/saisie.png" style="width: 16px; height: 16px;" title="modifier <?php if($data_avec_motif['type_absence_eleve']=="A") { ?>l'absence<?php } if ($data_avec_motif['type_absence_eleve']=="R") { ?>le retard<?php } if ($data_avec_motif['type_absence_eleve']=="D") { ?>la dispence<?php } if ($data_avec_motif['type_absence_eleve']=="I") { ?>le passage à l'infirmerie<?php } ?>" border="0" alt="" /></a>
		<a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_avec_motif['login']; ?>" title="consulter la fiche de l'élève"><?php echo "<b>".strtoupper($data_avec_motif['nom'])."</b> ".ucfirst($data_avec_motif['prenom']) . " (" . $data_avec_motif['regime'] . ") (" . classe_de($data_avec_motif['login']) . " )"; ?></a>

		</td>
		<td class="<?php echo $couleur_cellule; ?>">
<?php
			if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo")) {
				$nom_photo = '';
				$nom_photo = nom_photo($data_avec_motif['elenoet'],"eleves",2);
        		$photos = $nom_photo;
				//if ( $nom_photo === '' or !file_exists($photo) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
				if ( $nom_photo === NULL or !file_exists($photos) ) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
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
		<tr><td>
		<?php
		$content = '';
		for ($i=1; $i <= $nombreDePage;$i++){
      $content .= '<a href="javascript:void(0);" onclick="pagin('.$i.');return false;">';
      if($i == $pageActuelle){
        $content .= '['.$i.']';
      }else{
        $content .= $i;
      }
      $content .= ' </a>';
    }
    $content .= '</p>';

    echo $content;

		  ?>
		  </td><td></td></tr>
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
            <select name="classe_choix" onChange="javascript:document.form1.submit()">
              <option value="" selected onClick="javascript:document.form1.submit()">Toutes les classes</option>
                  <?php
                    $resultat_liste_classe = mysqli_query($GLOBALS["mysqli"], $requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
                    While ( $data_liste_classe = mysqli_fetch_array($resultat_liste_classe)) {
                           if ($classe_choix==$data_liste_classe['id']) {$selected = "selected"; } else {$selected = ""; }?>
              <option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>  onclick="javascript:document.form1.submit()">
			  <?php echo mb_substr($data_liste_classe['nom_complet'], 0, 50)." (".$data_liste_classe['classe'].")"; ?></option>
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
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php }
} ?>
</div>
