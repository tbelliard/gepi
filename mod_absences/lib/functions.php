<?php
//fonction permettant de connaître la classe d'un élève par son login
function classe_de($id_classe_eleve) {
    global $prefix_base;
    $requete_classe_eleve ="SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.classe, ".$prefix_base."classes.id, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."eleves.login='".$id_classe_eleve."' AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id";
    $execution_classe_eleve = mysql_query($requete_classe_eleve) or die('Erreur SQL !'.$requete_classe_eleve.'<br />'.mysql_error());
    $data_classe_eleve = mysql_fetch_array($execution_classe_eleve);
    $id_classe_eleve = $data_classe_eleve['nom_complet'];
    return($id_classe_eleve);
}

//fonction permettant de connaître le motif d'une absence
function motif_de($nc_motif) {
    global $prefix_base;
    $requete_motif ="SELECT * FROM ".$prefix_base."absences_motifs WHERE ".$prefix_base."absences_motifs.init_motif_absence='".$nc_motif."'";
    $execution_motif = mysql_query($requete_motif) or die('Erreur SQL !'.$requete_motif.'<br />'.mysql_error());
    $data_motif = mysql_fetch_array($execution_motif);
    $nc_motif = $data_motif['def_motif_absence'];
    return($nc_motif);
}

//fonction permettant de connaître l'action par rapport à un id d'action
function action_de($nc_action) {
    global $prefix_base;
	$requete_action = "SELECT init_absence_action, def_absence_action FROM ".$prefix_base."absences_actions WHERE init_absence_action='".$nc_action."'";
        $resultat_action = mysql_query($requete_action) or die('Erreur SQL !'.$requete_action.'<br />'.mysql_error());
	$data_action = mysql_fetch_array ($resultat_action);
        $nc_action = $data_action['def_absence_action'];
    return($nc_action);
}

function annee_en_cours($date)
{
    $date = explode('-', $date);
    if (empty($annee_d)) {if ($date[1] < 8) {$annee_d = $date[0] - 1;} else {$annee_d = $date[0];}}
    if (empty($annee_f)) {if ($date[1] >= 8){$annee_f = $date[0] + 1;} else {$annee_f = $date[0];}}
    //Annee en cours
    $annee_en_cours = $annee_d."/".$annee_f;
    return($annee_en_cours);
}

function annee_en_cours_t($date)
{
    $date = explode('-', $date);
    if (empty($annee_d)) {if ($date[1] < 8) {$annee_d = $date[0] - 1;} else {$annee_d = $date[0];}}
    if (empty($annee_f)) {if ($date[1] >= 8){$annee_f = $date[0] + 1;} else {$annee_f = $date[0];}}
    //Annee en cours
    $annee_en_cours = $annee_d."-".$annee_f;
    return($annee_en_cours);
}

function dategl($jour, $mois, $annee)
  {
  $date = "$jour,$mois,$annee";
  $tab_mois = array(1=>"Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre");
  $tab_jour = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
  $tab_date = explode(',', $date);
  $jour = date("w", mktime(0, 0, 0, $tab_date[1], $tab_date[0], $tab_date[2]));
  $date = ($tab_jour[$jour]." ".$tab_date[0]." ".$tab_mois[$tab_date[1]]." ".$tab_date[2]);
  echo "$date";
  }

function heure($heure)
  {
  $tab_heure = explode(':', $heure);
  if ($tab_heure[0]==0)
  {
      $heure = "";
  }
  elseif ($tab_heure[1]==0)
  {
      $heure = ($tab_heure[0]."h");
  }
  else
  {
      $heure = ($tab_heure[0]."h".$tab_heure[1]."min");
  }
  return($heure);
  }

function heure_texte_court($heure)
  {
  $tab_heure = explode(':', $heure);
  if ($tab_heure[0]==0)
  {
      $heure = '';
  }
  elseif ($tab_heure[1]==0)
  {
      $heure = ($tab_heure[0].'h');
  }
  else
  {
      $heure = ($tab_heure[0].'h'.$tab_heure[1]);
  }
  return($heure);
  }

function heure_court($heure)
  {
  $tab_heure = explode(':', $heure);

  if ($tab_heure[0]==0)
  {
      $heure = '';
  }
  elseif ($tab_heure[1]==0)
  {
      $heure = ($tab_heure[0].":00");
  }
  else
  {
      $heure = ($tab_heure[0].":".$tab_heure[1]);
  }
  return($heure);
  }

function date_sql($var)
        {
        $var = explode("/",$var);
        $var = $var[2]."-".$var[1]."-".$var[0];
        return($var);
        }

function date_fr($var)
        {
        $var = explode("-",$var);
        $var = $var[2]."/".$var[1]."/".$var[0];
        return($var);
        }

function date_frl($var)
        {
if ($var == "0000-00-00") {} else {
        $var = explode("-",$var);
  $date = "$var[2],$var[1],$var[0]";
  $tab_mois = array('1'=>"Janvier", '2'=>"Fevrier", '3'=>"Mars", '4'=>"Avril", '5'=>"Mai", '6'=>"Juin", '7'=>"Juillet", '8'=>"Aout", '9'=>"Septembre", '01'=>"Janvier", '02'=>"Fevrier", '03'=>"Mars", '04'=>"Avril", '05'=>"Mai", '06'=>"Juin", '07'=>"Juillet", '08'=>"Aout", '09'=>"Septembre", '10'=>"Octobre", '11'=>"Novembre", '12'=>"Decembre");
  $tab_jour = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
  $tab_date = explode(',', $date);
  $jour = date("w", mktime(0, 0, 0, $tab_date[1], $tab_date[0], $tab_date[2]));
  if ($tab_date[0]=='01') {$tab_date[0]='1er';}
  if ($tab_date[0]=='02') {$tab_date[0]='2';}
  if ($tab_date[0]=='03') {$tab_date[0]='3';}
  if ($tab_date[0]=='04') {$tab_date[0]='4';}
  if ($tab_date[0]=='05') {$tab_date[0]='5';}
  if ($tab_date[0]=='06') {$tab_date[0]='6';}
  if ($tab_date[0]=='07') {$tab_date[0]='7';}
  if ($tab_date[0]=='08') {$tab_date[0]='8';}
  if ($tab_date[0]=='09') {$tab_date[0]='9';}
  $date = ($tab_jour[$jour]." ".$tab_date[0]." ".$tab_mois[$tab_date[1]]." ".$tab_date[2]);
   $var = $date;
   return($var);
   }
 }


function date_frc($var)
  {
  $var = explode("-",$var);
  $date = "$var[2],$var[1],$var[0]";
  $tab_mois = array('01'=>"Jan.", '02'=>"Fev.", '03'=>"Mar.", '04'=>"Avr.", '05'=>"Mai", '06'=>"Juin", '07'=>"Juil.", '08'=>"Aout", '09'=>"Sept.", '10'=>"Oct.", '11'=>"Nov.", '12'=>"Dec.");
  $tab_jour = array("Dim.", "Lun.", "Mar.", "Mer.", "Jeu.", "Ven.", "Sam.");
  $tab_date = explode(',', $date);
  $jour = date("w", mktime(0, 0, 0, $tab_date[1], $tab_date[0], $tab_date[2]));
  if ($tab_date[0]=='01') {$tab_date[0]='1er';}
  if ($tab_date[0]=='02') {$tab_date[0]='2';}
  if ($tab_date[0]=='03') {$tab_date[0]='3';}
  if ($tab_date[0]=='04') {$tab_date[0]='4';}
  if ($tab_date[0]=='05') {$tab_date[0]='5';}
  if ($tab_date[0]=='06') {$tab_date[0]='6';}
  if ($tab_date[0]=='07') {$tab_date[0]='7';}
  if ($tab_date[0]=='08') {$tab_date[0]='8';}
  if ($tab_date[0]=='09') {$tab_date[0]='9';}
  $date = ($tab_jour[$jour]." ".$tab_date[0]." ".$tab_mois[$tab_date[1]]." ".$tab_date[2]);
   $var = $date;
   return($var);
 }

function dateglvs($jour, $mois, $annee)
  {
  $date = "$jour,$mois,$annee";
  $tab_mois = array(1=>"Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre");
  $tab_jour = array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
  $tab_date = explode(',', $date);
  $jour = date("w", mktime(0, 0, 0, $tab_date[1], $tab_date[2], $tab_date[0]));
  $date = ($tab_jour[$jour]." ".$tab_date[0]." ".$tab_mois[$tab_date[1]]." ".$tab_date[2]);
  echo "$date";
  }

// fonction pour convertir un nombre de secondes en heure:minute:seconde.
function calcul_hours($temps)
{
  //combien d'heures ?
  $hours = floor($temps / 3600);
  //combien de minutes ?
  $min = floor(($temps - ($hours * 3600)) / 60);
  if ($min < 10)
    $min = "0".$min;
  //combien de secondes
  $sec = $temps - ($hours * 3600) - ($min * 60);
  if ($sec < 10)
    $sec = "0".$sec;
  echo $hours."h".$min."m".$sec."s";
}

// NbJours("2000-10-20", "2000-10-21") retourne 2

function nb_jour($date_debut,$date_fin)
{
    $tab_date_debut = explode('-', $date_debut);
    $tab_date_fin = explode('-', $date_fin);
    $Mois1 = $tab_date_debut[1];
    $Jour1 = $tab_date_debut[2];
    $Annee1 = $tab_date_debut[0];
    $Mois2 = $tab_date_fin[1];
    $Jour2 = $tab_date_fin[2];
    $Annee2 = $tab_date_fin[0];
    $Date1 = mktime( 0, 0, 0, $Mois1, $Jour1, $Annee1 );
    $Date2 = mktime( 0, 0, 0, $Mois2, $Jour2, $Annee2 );
    $nbJour = ($Date2 - $Date1) / (60*60*24);
    return ($nbJour);
}

function PrepSQL($ChangNom)
{
    $str = trim($ChangNom); //trim enleve les espaces avant et après le mot
    if ($str == "")
    {
//      echo "rien";
    }
    else
    {
        $str = str_replace("'", "''", $str);
    }
    return $str;
}

function period_deb($periode)
{
   switch ($periode)
   {
   case "M1": $d_heure_absence_eleve = "08:00";
            break;
   case "M2": $d_heure_absence_eleve = "08:55";
            break;
   case "P1": $d_heure_absence_eleve = "09:50";
            break;
   case "M3": $d_heure_absence_eleve = "10:05";
            break;
   case "M4": $d_heure_absence_eleve = "11:00";
            break;
   case "M5": $d_heure_absence_eleve = "11:55";
            break;
   case "R1": $d_heure_absence_eleve = "11:55";
            break;
   case "R": $d_heure_absence_eleve = "12:30";
            break;
   case "R2": $d_heure_absence_eleve = "13:00";
            break;
   case "S1": $d_heure_absence_eleve = "13:30";
            break;
   case "S2": $d_heure_absence_eleve = "14:25";
            break;
   case "P2": $d_heure_absence_eleve = "15:20";
            break;
   case "S3": $d_heure_absence_eleve = "15:35";
            break;
   case "S4": $d_heure_absence_eleve = "16:30";
            break;
   case "S5": $d_heure_absence_eleve = "17:30";
            break;
    default;
   }
  return $d_heure_absence_eleve;
}
function period_fin($periode)
{
   switch ($periode)
   {
   case "M1": $a_heure_absence_eleve = "08:55";
            break;
   case "M2": $a_heure_absence_eleve = "09:50";
            break;
   case "P1": $a_heure_absence_eleve = "10:05";
            break;
   case "M3": $a_heure_absence_eleve = "11:00";
            break;
   case "M4": $a_heure_absence_eleve = "11:55";
            break;
   case "M5": $a_heure_absence_eleve = "12:30";
            break;
   case "R1": $a_heure_absence_eleve = "12:30";
            break;
   case "R": $a_heure_absence_eleve = "13:00";
            break;
   case "R2": $a_heure_absence_eleve = "13:30";
            break;
   case "S1": $a_heure_absence_eleve = "14:25";
            break;
   case "S2": $a_heure_absence_eleve = "15:20";
            break;
   case "P2": $a_heure_absence_eleve = "15:35";
            break;
   case "S3": $a_heure_absence_eleve = "16:30";
            break;
   case "S4": $a_heure_absence_eleve = "17:30";
            break;
   case "S5": $a_heure_absence_eleve = "18:30";
            break;
    default;
   }
  return $a_heure_absence_eleve;
}

function motab($motab)
{
   switch ($motab)
   {
      case "A": $motab = "aucun motif"; break;
      case "AS": $motab = "accident sport"; break;
      case "AT": $motab = "non pr&eacute;sent en retenue"; break;
      case "C": $motab = "sur la cour"; break;
      case "CF": $motab = "convenances familiales"; break;
      case "CO": $motab = "convocation bureau"; break;
      case "CS": $motab = "competition sportive"; break;
      case "DI": $motab = "dispense d'e.p.s."; break;
      case "ET": $motab = "erreur d'emploie du temps"; break;
      case "EX": $motab = "examen"; break;
      case "H": $motab = "Hospitalis&eacute;(e)"; break;
      case "JP": $motab = "justifie par le principal"; break;
      case "MA": $motab = "Maladie"; break;
      case "OR": $motab = "conseiller"; break;
      case "PR": $motab = "reveil"; break;
      case "RC": $motab = "refus de venir en cours"; break;
      case "RE": $motab = "renvoye du coll&egrave;ge"; break;
      case "RT": $motab = "pr&eacute;sent en retenue"; break;
      case "RV": $motab = "renvoi du cours"; break;
      case "SM": $motab = "refus de justification"; break;
      case "SP": $motab = "sortie p&eacute;dagogique"; break;
      case "ST": $motab = "stage &agrave; l'ext&eacute;rieur du coll&egrave;ge"; break;
      case "T": $motab = "t&eacute;l&eacute;phone"; break;
      case "TR": $motab = "transport"; break;
      case "VM": $motab = "visite m&eacute;dical"; break;
      case "IN": $motab = "infirmerie"; break;
      default;
   }
  return $motab;
}

function motab_c($motab_c)
{
   switch ($motab_c)
   {
      case "A": $motab_c = "aucun"; break;
      case "AS": $motab_c = "accident"; break;
      case "AT": $motab_c = "non en retenue"; break;
      case "C": $motab_c = "sur la cour"; break;
      case "CF": $motab_c = "convenances"; break;
      case "CO": $motab_c = "convocation"; break;
      case "CS": $motab_c = "competition"; break;
      case "DI": $motab_c = "dispense"; break;
      case "ET": $motab_c = "erreur d'edt"; break;
      case "EX": $motab_c = "examen"; break;
      case "H": $motab_c = "hopital"; break;
      case "JP": $motab_c = "justifie"; break;
      case "MA": $motab_c = "Maladie"; break;
      case "OR": $motab_c = "conseiller"; break;
      case "PR": $motab_c = "reveil"; break;
      case "RC": $motab_c = "refus de venir"; break;
      case "RE": $motab_c = "renvoye"; break;
      case "RT": $motab_c = "en retenue"; break;
      case "RV": $motab_c = "renvoi"; break;
      case "SM": $motab_c = "refus de justif"; break;
      case "SP": $motab_c = "sorite peda"; break;
      case "ST": $motab_c = "stage"; break;
      case "T": $motab_c = "t&eacute;l&eacute;phone"; break;
      case "TR": $motab_c = "transport"; break;
      case "VM": $motab_c = "visite med"; break;
      case "IN": $motab_c = "infirmerie"; break;
      default;
   }
  return $motab_c;
}

function nb_jour_mois($mois_select,$annee_select)
 {
  //Dernier jour du mois
    $mois_d = mktime( 0, 0, 0, $mois_select, 1, $annee_select);
    //$date_select_exp[1] = mois
    //$date_select_exp[2] = jour
    $date_select_exp = explode('-', $date_select);

  //Mois précédent
    //calcul des positions des mois
    $mois = $date_select_exp[1];
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
    $date_moins1 = $precedente_annee."-".$precedent_mois."-".$date_select_exp[2];
    $date_plus1 = $prochain_annee."-".$prochain_mois."-".$date_select_exp[2];
    return($date_moins1);
    return($date_plus1);
 }


// fonction qui indique le mois suiviant
function mois_suivant($date_select)
 {

	$prochain_mois = ''; $mois = '';

    $date_select_exp = explode('/', $date_select);
    $mois = $date_select_exp[1]; 
    $annee = $date_select_exp[2];

  //Mois
    //calcul des positions des mois
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

	if($prochain_mois < 10) { $prochain_mois = '0'.$prochain_mois; }
    // $date_plus1 = $prochain_annee."-".$prochain_mois."-".$date_select_exp[2];
    $date_plus1 = '01/'.$prochain_mois.'/'.$prochaine_annee;
    return($date_plus1);
 }

// fonction qui renvoi un tableau de mois d'un moi à un autre mois
function tableau_mois($mois_de, $annee_de, $mois_au, $annee_au)
{
	$jour = '1';
	$i = '0';
	$mois_passe = '';
	$annee_passe = '';
	$sortire_de_la_boucle = 'non';
	while ( $sortire_de_la_boucle != 'oui' )
	{
	  if ( $mois_passe.'-'.$annee_passe === $mois_au.'-'.$annee_au ) { $sortire_de_la_boucle = 'oui'; }
		// si le moi passe n'est pas défini alors on le définie avec
		if ( $mois_passe === '' and $annee_passe === '' )
		{
			$mois_passe = $mois_de;
			$annee_passe = $annee_de;
		}

		// $mois[$i]['mois'] = 'aou. 2006';
		   $tab_mois = array('01'=>"jan.", '02'=>"fev.", '03'=>"mar.", '04'=>"avr.", '05'=>"mai", '06'=>"jui.", '07'=>"juil.", '08'=>"aoû.", '09'=>"sep.", '10'=>"oct.", '11'=>"nov.", '12'=>"déc.");
		$mois[$i]['mois'] = $tab_mois[$mois_passe].' '.$annee_passe;
		$mois[$i]['mois_court'] = $tab_mois[$mois_passe];
		// $mois[$i]['num_mois'] = '08';
		$mois[$i]['num_mois'] = $mois_passe;
		// $mois[$i]['num_mois_simple'] = '8';
		// $mois[$i]['num_annee'] = '2006';
		$mois[$i]['num_annee'] = $annee_passe;
		$mois_suivant = explode('/',mois_suivant('01/'.$mois_passe.'/'.$annee_passe));

		$mois_passe = $mois_suivant[1];
		$annee_passe = $mois_suivant[2]; 
	$i = $i + 1;
	}
	return($mois);
}

function nb_jour_mois_autre($mois_select,$annee_select)
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

function verif_date($date_a_verif)
 {
    if(eregi("[0-9]{4}-[0-9]{2}-[0-9]{2}",$date_a_verif))
     {
       //explotion de la date en jour, mois, année
          $date_a_verif_exp = explode('-', $date_a_verif);
          $resultats = checkdate($date_a_verif_exp[1], $date_a_verif_exp[2], $date_a_verif_exp[0]);

        if( $resultats == true ) { $valide_date = "pass"; } else { $valide_date = "erreur"; }
     } else { $valide_date = "erreur"; }
    return($valide_date);
 }

//fonction redimensionne les photos
function redimensionne_image($photo)
 {
    // prendre les informations sur l'image
    $info_image = getimagesize($photo);
    // largeur et hauteur de l'image d'origine
    $largeur = $info_image[0];
    $hauteur = $info_image[1];
    // largeur et/ou hauteur maximum à afficher
             $taille_max_largeur = 110;
             $taille_max_hauteur = 110;

    // calcule le ratio de redimensionnement
     $ratio_l = $largeur / $taille_max_largeur;
     $ratio_h = $hauteur / $taille_max_hauteur;
     $ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

    // définit largeur et hauteur pour la nouvelle image
     $nouvelle_largeur = $largeur / $ratio;
     $nouvelle_hauteur = $hauteur / $ratio;

   // on renvoit la largeur et la hauteur
    return array($nouvelle_largeur, $nouvelle_hauteur);
 }

//fonction redimensionne les photos petit format
function redimensionne_image_petit($photo)
 {
    // prendre les informations sur l'image
    $info_image = getimagesize($photo);
    // largeur et hauteur de l'image d'origine
    $largeur = $info_image[0];
    $hauteur = $info_image[1];
    // largeur et/ou hauteur maximum à afficher
             $taille_max_largeur = 60;
             $taille_max_hauteur = 60;

    // calcule le ratio de redimensionnement
     $ratio_l = $largeur / $taille_max_largeur;
     $ratio_h = $hauteur / $taille_max_hauteur;
     $ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

    // définit largeur et hauteur pour la nouvelle image
     $nouvelle_largeur = $largeur / $ratio;
     $nouvelle_hauteur = $hauteur / $ratio;

   // on renvoit la largeur et la hauteur
    return array($nouvelle_largeur, $nouvelle_hauteur);
 }

function qui($login_perso)
{
      global $prefix_base;
      $requete_login_perso ="SELECT * FROM ".$prefix_base."utilisateurs WHERE ".$prefix_base."utilisateurs.login = '".$login_perso."'";
      $execution_login_perso = mysql_query($requete_login_perso) or die('Erreur SQL !'.$requete_login_perso.'<br />'.mysql_error());
      $data_login_perso = mysql_fetch_array($execution_login_perso);
      if($login_perso != "") { $qui_est_ce = $data_login_perso['civilite']." ".strtoupper($data_login_perso['nom'])." ".ucfirst($data_login_perso['prenom']); } else { $qui_est_ce=""; }
      return $qui_est_ce;
}

function qui_fonction($login_perso)
{
      global $prefix_base;
      $requete_login_perso ="SELECT * FROM ".$prefix_base."utilisateurs WHERE ".$prefix_base."utilisateurs.login = '".$login_perso."'";
      $execution_login_perso = mysql_query($requete_login_perso) or die('Erreur SQL !'.$requete_login_perso.'<br />'.mysql_error());
      $data_login_perso = mysql_fetch_array($execution_login_perso);
      if($login_perso != "") { $qui_est_ce_status = $data_login_perso['statut']; } else { $qui_est_ce_status=""; }
      return $qui_est_ce_status;
}


function qui_court($login_perso)
{
      global $prefix_base;
      $requete_login_perso ="SELECT * FROM ".$prefix_base."utilisateurs WHERE ".$prefix_base."utilisateurs.login = '".$login_perso."'";
      $execution_login_perso = mysql_query($requete_login_perso) or die('Erreur SQL !'.$requete_login_perso.'<br />'.mysql_error());
      $data_login_perso = mysql_fetch_array($execution_login_perso);
      if($login_perso != "") { $qui_est_ce = $data_login_perso['civilite']." ".strtoupper($data_login_perso['nom'])." ".ucfirst($data_login_perso['prenom']); } else { $qui_est_ce=""; }
      return $qui_est_ce;
}

function qui_eleve($login_perso)
{
      global $prefix_base;
      $requete_login_perso ="SELECT * FROM ".$prefix_base."eleves WHERE ".$prefix_base."eleves.login = '".$login_perso."'";
      $execution_login_perso = mysql_query($requete_login_perso) or die('Erreur SQL !'.$requete_login_perso.'<br />'.mysql_error());
      $data_login_perso = mysql_fetch_array($execution_login_perso);
      if($login_perso != "") { $qui_est_ce = strtoupper($data_login_perso['nom'])." ".ucfirst($data_login_perso['prenom']); } else { $qui_est_ce=""; }
      return $qui_est_ce;
}

function lettre_type($id)
{
      global $prefix_base;
      $requete ="SELECT * FROM ".$prefix_base."lettres_types WHERE id_lettre_type = '".$id."'";
      $execution = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
      $donner = mysql_fetch_array($execution);
      if(!empty($donner['id_lettre_type'])) { $type_de_courrier = $donner['titre_lettre_type']; } else { $type_de_courrier = 'inconnu'; }
      return ($type_de_courrier);
}

function aff_mois($mois) {
   $tab_mois = array('1'=>"Janvier", '2'=>"Fevrier", '3'=>"Mars", '4'=>"Avril", '5'=>"Mai", '6'=>"Juin", '7'=>"Juillet", '8'=>"Aout", '9'=>"Septembre", '10'=>"Octobre", '11'=>"Novembre", '12'=>"Décembre");
   return($tab_mois[$mois]);
}

function aff_jour($jour, $mois, $annee) {
   $tab_jour = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
   $jour = date("w", mktime(0, 0, 0, $mois, $jour, $annee));
   return($tab_jour[$jour]);
}

function date_long_format($jour, $mois, $annee)
 {
    $tab_mois = array('1'=>"Janvier", '2'=>"Fevrier", '3'=>"Mars", '4'=>"Avril", '5'=>"Mai", '6'=>"Juin", '7'=>"Juillet", '8'=>"Aout", '9'=>"Septembre", '10'=>"Octobre", '11'=>"Novembre", '12'=>"Decembre");
    $tab_jour = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
    $jour_semaine = date("w", mktime(0, 0, 0, $mois, $jour, $annee));
    $date = ($tab_jour[$jour_semaine]." ".$jour." ".$tab_mois[$mois]." ".$annee);
    return($date);
 }

// fonction permettant l'affichage des absences sur une année
// tableau_annuel($id_eleve, $mois_debut, $nb_mois, $annee_select, $tableau_info_donnee)
function tableau_annuel($id_eleve, $mois_debut, $nb_mois, $annee_select, $tableau_info_donnee)
 {
	if(empty($mois_debut) or $mois_debut==='0') { $mois_debut = '8'; }
	if(empty($nb_mois) or $nb_mois==='0') { $nb_mois = '12'; }
	if(empty($annee_select) or $annee_select==='0') { $annee_select = '12'; }
	$nb_jour = '31';

	$num_mois = '0';
	$nb_jour_aff = '1';
	$icouleur = '1';

	echo '<table class="table_calendrier">';
	//entête
		echo '<tr>';
		  echo '<td class="td_calendrier_mois" style="background-color: #F0FFCF;">Mois</td>';
		  // boucle des jours
		  while($nb_jour_aff<=$nb_jour) {
			if($icouleur==='1') { $couleur_cellule='#FAFFEF'; $icouleur='2'; } else { $couleur_cellule='#F0FFCF'; $icouleur='1'; } 
			echo '<td class="td_calendrier_jour" style="background-color: '.$couleur_cellule.'">'.$nb_jour_aff.'</td>';
			$nb_jour_aff++;
		  }
		echo '</tr>';

	//donnée
	$nb_jour_aff = '1';
	$nb_mois_aff = '1';
	$icouleur = '1';

	// boucle des mois
	while($nb_mois_aff<=$nb_mois)
	{
		// gestion du numéro du mois
		if($num_mois==='0') 
		{ $num_mois = $mois_debut; } else {
				if($num_mois!='12') { $num_mois = $num_mois + '1'; } else { $num_mois = '1'; $annee_select++; }
				}
		$mois_selectionne = $num_mois;
		if($icouleur==='1') { $couleur_cellule='#FAFFEF'; } else { $couleur_cellule='#F0FFCF'; } 

		echo '<tr>';
	 	  echo '<td class="td_calendrier_mois" style="background-color: '.$couleur_cellule.'">'.aff_mois($mois_selectionne).' <span style="font-size : 0.6em;">'.$annee_select.'</span></td>';

		  // boucle des jours
		  while($nb_jour_aff<=$nb_jour) {
			$jour_selectionne = $nb_jour_aff;
			//s'occupe de connaitre la couleur de la cellule si un jour non existant
			// vérifie l'existance de la date
			if(checkdate($num_mois, $jour_selectionne, $annee_select)) {
				if(aff_jour($jour_selectionne, $num_mois, $annee_select)!='Dimanche') { if($icouleur==='1') { $couleur_cellule='#FAFFEF'; } else { $couleur_cellule='#F0FFCF'; } } else { $couleur_cellule='#C3EF59'; }
			 } else { $couleur_cellule='#5F5F5F'; }
	
			echo '<td class="td_calendrier_jour" style="background-color: '.$couleur_cellule.'" title="'.date_long_format($jour_selectionne, $num_mois, $annee_select).'">';

			$passage = $jour_selectionne.'/'.$num_mois.'/'.$annee_select;
			if(empty($tableau_info_donnee[$passage]['absence'])) { $tableau_info_donnee[$passage]['absence'] = ''; }

			if(empty($tableau_info_donnee[$passage]['retard'])) { $tableau_info_donnee[$passage]['retard'] = ''; }
			if(empty($tableau_info_donnee[$passage]['retard']) and $tableau_info_donnee[$passage]['absence']==='oui')
			{
				?><img src="../images/absence.png" style="width: 10px; height: 10px;" alt="" /><?php
			}
			if(empty($tableau_info_donnee[$passage]['absence']) and $tableau_info_donnee[$passage]['retard']==='oui')
			{
				?><img src="../images/retard.png" style="width: 10px; height: 10px;" alt="" /><?php
			}
			if($tableau_info_donnee[$passage]['absence']==='oui' and $tableau_info_donnee[$passage]['retard']==='oui')
			{
				?><img src="../images/absenceretard.png" style="width: 10px; height: 10px;" alt="" /><?php
			}

			?></td><?php
			$nb_jour_aff++;
		  }
		echo '</tr>';

		$nb_jour_aff ='1';
		if($icouleur==='2') { $icouleur = '1'; } else { $icouleur = '2'; }
		$nb_mois_aff++;
	}
	echo '</table>';
	?><img src="../images/absence.png" style="width: 10px; height: 10px;" alt="" /> - Absences<?php
	?> <img src="../images/retard.png" style="width: 10px; height: 10px;" alt="" /> - Retard<?php
	?> <img src="../images/absenceretard.png" style="width: 10px; height: 10px;" alt="" /> - Absences et retard<?php
}

//fonction qui détermine le nombre de jour entre deux date et renvoie un tableau avec les dates d'absences
// $variable['absences']['date']['login']
/*
function jour_concerne($tableau_selection_eleve, $type)
{
	$type='A';
	// sélecion de tous les élèves de l'école
        if($tableau_selection_eleve === "tous" and empty($tableau_selection_eleve[0])) { $requete = ("SELECT * FROM ".$prefix_base."absences_eleves WHERE type_absence_eleve = '".$type."'"); }
	// sélection de quelles que élèves
        if($tableau_selection_eleve != "tous" and !empty($tableau_selection_eleve[0])) {
		if (!empty($tableau_selection_eleve[0])) {
	 	$o=0; $prepa_requete = "";
	        while(!empty($tableau_selection_eleve[$o]))
		     { 
			if($o == "0") { $prepa_requete = 'eleve_absence_eleve = "'.$tableau_selection_eleve[$o].'"'; }
			if($o != "0") { $prepa_requete = $prepa_requete.' OR eleve_absence_eleve = "'.$tableau_selection_eleve[$o].'" '; }
			$o = $o + 1;
	             }
		}
	        $requete = ("SELECT * FROM ".$prefix_base."absences_eleves WHERE type_absence_eleve = '".$type."' AND ('.$prepa_requete.')");
	}

	// on commence le traitement
	while($donner = mysql_fetch_array($requete))
	{
		// on attribue les variables
		$login = $donner['eleve_absence_eleve'];
		$date_debut = date_fr($donner['d_date_absence_eleve']); 
		$date_fin = date_fr($donner['a_date_absence_eleve']);
		$heure_debut = $donner['d_heure_absence_eleve'];
		$heure_fin = $donner['a_heure_absence_eleve'];
	    // on vas faire une boucle tant que l'élève est absent si la date de début et de fin ne sont pas égale
	      if($date_debut!=$date_fin)
	      {	
 		     $date_debut_tableau = $date_debut;
		     $passage='oui';
		     while($passage==='oui') {
			    $tableau_info_donnee[$date_debut_tableau]['absence'] = 'oui';
			    if($date_debut===$date_fin) { $passage='non'; } else { $passage='oui'; }
			    $date_debut = date("d/m/Y", mktime(0, 0, 0, $dateexplode[1], $dateexplode[0]+1,  $dateexplode[2]));
			}
	      } else {
		        // on effectue l'affection des informations dans un tableau global
		        $liste_information[$type][$date_debut][$login]['heure_debut'] = $heure_debut;
		        $liste_information[$type][$date_debut][$login]['heure_fin'] = $heure_fin;
		     }
	}
} */

//fonction permettant de récupérer les données de l'emploie du temps
function edt_active_prof($login_prof, $heure, $jour, $semaine)
{
	  // requête qui permet de savoir quelle cours à un professeur par rapport à un jour et une heure donnée ainsi que la semaine paire et impaire
          $requete = ('SELECT * FROM edt_classes WHERE prof_edt_classe = "'.$login_prof.'" AND jour_edt_classe = "'.$jour.'" AND semaine_edt_classe = "'.$semaine.'"');
      	  $resultat = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
	  while($donner = mysql_fetch_array($resultat))
	  {
		$id_groupe_edt = $donner['groupe_edt_classe'];
		$heuredebut_edt = $donner['heuredebut_edt_classe'];
	 	$heurefin_edt = $donner['heurefin_edt_classe'];
	  }
return array($id_groupe_edt, $heuredebut_edt, $heurefin_edt);
}

// fonction permettant de savoir dans quelle période nous nous trouvons
// ex: periode_actuel('11:00:00') > période M4
function periode_actuel($heure_choix)
  {
      // fonction permettant de savoir dans quelle période nous nous trouvons
      if($heure_choix=="") { $heure_choix = date('H:i:s'); }
      $num_periode="";
      //on liste dans un tableau les périodes existante
      $requete_periode = ('SELECT * FROM absences_creneaux WHERE heuredebut_definie_periode <= "'.$heure_choix .'" AND heurefin_definie_periode >= "'.$heure_choix.'" ORDER BY heuredebut_definie_periode, nom_definie_periode ASC');
      $resultat_periode = mysql_query($requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.mysql_error());
      while($data_periode = mysql_fetch_array ($resultat_periode)) {
          $debut=$data_periode['heuredebut_definie_periode'];
          $num_periode=$data_periode['id_definie_periode'];
      }
      return($num_periode);
  }

//connaitre l'heure du début soit de la fin d'une période
// ex: perdiode_heure($id_periode) > [0]11:00:00 [1]11:55:00
function periode_heure($periode)
{
	$debut = ''; $fin = '';
      // on recherche les informations sur la périodes sélectionné
      $requete_periode = ('SELECT * FROM absences_creneaux WHERE id_definie_periode = "'.$periode.'"');
      $resultat_periode = mysql_query($requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.mysql_error());
      while($data_periode = mysql_fetch_array ($resultat_periode)) {
          $debut = $data_periode['heuredebut_definie_periode'];
          $fin   = $data_periode['heurefin_definie_periode'];
      }

      return array('debut'=> $debut, 'fin'=>$fin);
}

//fonction pour connaitre le jour de la semaine par rapport à une date
function jour_semaine($date_parametre)
{
   //on explose la date en jour mois annee 
   $date_selection = explode('/', $date_parametre);
	if(!empty($date_selection[1])) { $jour = $date_selection[0]; $mois = $date_selection[1]; $annee = $date_selection[2]; }
	else { $date_selection = explode('-', $date_parametre); $jour = $date_selection[2]; $mois = $date_selection[1]; $annee = $date_selection[0]; }

   if(!empty($date_selection[1])) { 
    $tab_jour = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
	//0=Dimanche, 1=Lundi ... 
    $num_jour_semaine = date("w", mktime(0, 0, 0, $mois, $jour, $annee));
    $jour_semaine = $tab_jour[$num_jour_semaine];
    return array('text'=>$jour_semaine, 'chiffre'=>$num_jour_semaine);
   }
}

// fonction permetant d'exploser une date français ou sql en jour mois annee
// ex: date_explose(date)
function date_explose($date_parametre)
{
   //on explose la date en jour mois annee 
   $date_selection = explode('/', $date_parametre);
	if(!empty($date_selection[1])) { $jour = $date_selection[0]; $mois = $date_selection[1]; $annee = $date_selection[2]; }
	else { $date_selection = explode('-', $date_parametre); $jour = $date_selection[2]; $mois = $date_selection[1]; $annee = $date_selection[0]; }
    return array('jour'=>$jour, 'mois'=>$mois, 'annee'=>$annee);
}

//fonction permetant de connaitre la matière d'une groupe
// ex: matiere_du_groupe(id du groupe)
function matiere_du_groupe($groupe_parametre)
{
      $requete = ('SELECT * FROM j_groupes_matieres, matieres WHERE id_groupe = "'.$groupe_parametre.'" AND matiere=id_matiere');
      $resultat = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
      while($donnee = mysql_fetch_array ($resultat)) {
          $nomcomplet = $donnee['nom_complet'];
          $nomcourt   = $donnee['matiere'];
      }
      return array('nomcomplet'=> $nomcomplet, 'nomcourt'=>$nomcourt);
}

// fonction permetant de connaitre le jour de la semain SQL en numérique
function jour_sem_sql($date)
 {
	date_default_timezone_get();
	$tab_date = explode('-', $date);
	$jour_de_la_semaine = date("w", mktime(0, 0, 0, $tab_date[1], $tab_date[2], $tab_date[0]));
	return($jour_de_la_semaine);
 }

// fonction permetant de connaitre le jour de la semain SQL en format text
function jour_sem_compfr($date)
 {
	date_default_timezone_get();
	$tab_date = explode('-', $date);
	$tab_jour = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
	$jour = date("w", mktime(0, 0, 0, $tab_date[1], $tab_date[2], $tab_date[0]));
	$jour_de_la_semaine = $tab_jour[$jour];
	return($jour_de_la_semaine);
 }


//fonction qui permet de recevoir dans un tableau login|jour|date|heure_debut|heure_fin
// à partir d'un tableau qui contient les données suivant login|date_debut|date_fin|heure_debut|heure_fin
function crer_tableau_jaj($tableau)
 {
	date_default_timezone_get();
	$tableau_de_donnees = '';
	$i = '0'; $i2 = '0'; $heure_de_debut = '07:00:00'; $heure_de_fin = '19:00:00';
	while(!empty($tableau[$i]['login']))
	 { 
		// si la date de debut et de fin son identique on peut saisir les donnes tout de suite
		if($tableau[$i]['date_debut'] === $tableau[$i]['date_fin'])
		 {
			$tableau_de_donnees[$i2]['id'] = $i2;
			$tableau_de_donnees[$i2]['login'] = $tableau[$i]['login'];
			$tableau_de_donnees[$i2]['classe'] = $tableau[$i]['classe'];
			$tableau_de_donnees[$i2]['jour'] = jour_sem_sql($tableau[$i]['date_debut']);
			$tableau_de_donnees[$i2]['date'] = $tableau[$i]['date_debut'];
			$tableau_de_donnees[$i2]['heure_debut'] = $tableau[$i]['heure_debut'];
			$tableau_de_donnees[$i2]['heure_fin'] = $tableau[$i]['heure_fin'];
			$i2 = $i2 + 1;
		 }
		// si la date de debut et de fin ne son pas identique alors on doit faire jour par jour pour la saisi des données
		if($tableau[$i]['date_debut'] != $tableau[$i]['date_fin'])
		 { 
			$jour_select = $tableau[$i]['date_debut'];
			// calcule le nombre de jour entre deux date
			$date1 = str_replace("-", "", $tableau[$i]['date_debut']);
			$date2 = str_replace("-", "", $tableau[$i]['date_fin']);
			$nbjours = floor((strtotime($date2) - strtotime($date1))/(60*60*24));
			$nb_jour_passe = '0'; $jour_passe = $tableau[$i]['date_debut'];
			while($nb_jour_passe<=$nbjours)
			 {
				$tableau_de_donnees[$i2]['id'] = $i2;
				$tableau_de_donnees[$i2]['login'] = $tableau[$i]['login'];
				$tableau_de_donnees[$i2]['classe'] = $tableau[$i]['classe'];
				$tableau_de_donnees[$i2]['jour'] = jour_sem_sql($jour_passe);
				$tableau_de_donnees[$i2]['date'] = $jour_passe;
				if($jour_passe != $tableau[$i]['date_debut'] and $jour_passe != $tableau[$i]['date_fin'])
				{
					$tableau_de_donnees[$i2]['heure_debut'] = $heure_de_debut;
					$tableau_de_donnees[$i2]['heure_fin'] = $heure_de_fin;
				} else {
						if($jour_passe === $tableau[$i]['date_debut']) { $tableau_de_donnees[$i2]['heure_debut'] = $tableau[$i]['heure_debut']; } else { $tableau_de_donnees[$i2]['heure_debut'] = $heure_de_debut; }
						if($jour_passe === $tableau[$i]['date_fin']) { $tableau_de_donnees[$i2]['heure_fin'] = $tableau[$i]['heure_fin']; } else { $tableau_de_donnees[$i2]['heure_fin'] = $heure_de_fin; }
					}
				$tab_date = explode('-', $jour_passe);
				$jour_passe = date("Y-m-d", mktime(0, 0, 0, $tab_date[1], $tab_date[2]+1,  $tab_date[0]));
				$nb_jour_passe = $nb_jour_passe + 1;
				$i2 = $i2 + 1;
			 }
		 }
		$i = $i + 1;
	 }

return $tableau_de_donnees;
 }

function Absence($jourC, $heureD, $heureF, $tab) {

  $abs = 0;
  foreach($tab as $cle => $valeur) {

	list($an, $mois, $jour) = explode("-", $tab[$cle]['date']);
	$newDate = mktime(0,0,0,$mois,$jour,$an);
	$test = date("D", $newDate);

	if ($test === $jourC) {
	  if (($tab[$cle]['heure_debut'] < $heureD and $tab[$cle]['heure_fin'] <= $heureD) or ($tab[$cle]['heure_debut'] >= $heureF and $tab[$cle]['heure_fin'] > $heureF))
	   { /* vide */ } else { $abs = $abs +1; }
	}

  }

return $abs;
}


function absence_fiche($jourC, $heureD, $heureF, $tab) {

  $abs = 0;
  foreach($tab as $cle => $valeur) {
      if ( isset($tab[$cle]['date']) ) {
	$date_select = explode('-',$tab[$cle]['date']);
	if ( checkdate($date_select[1], $date_select[2], $date_select[0]) )
	{
		list($an, $mois, $jour) = explode("-", $tab[$cle]['date']);
		$newDate = mktime(0,0,0,$mois,$jour,$an);
		$test = date("w", $newDate);

		if ($test === $jourC) {
		  if (($tab[$cle]['heure_debut'] < $heureD and $tab[$cle]['heure_fin'] <= $heureD) or ($tab[$cle]['heure_debut'] >= $heureF and $tab[$cle]['heure_fin'] > $heureF))
		   { /* vide */ } else { $abs = $abs + 1; }
		}
	}
      }

  }

return $abs;
}

function retard_fiche($jourC, $heureD, $heureF, $tab) {

  $abs = 0;
  foreach($tab as $cle => $valeur) {

      if ( isset($tab[$cle]['date']) ) {
	$date_select = explode('-',$tab[$cle]['date']);
	if ( checkdate($date_select[1], $date_select[2], $date_select[0]) )
	{
		list($an, $mois, $jour) = explode("-", $tab[$cle]['date']);
		$newDate = mktime(0,0,0,$mois,$jour,$an);
		$test = date("w", $newDate);
		if ($test === $jourC) {
		  if ( $tab[$cle]['heure_debut'] > $heureD and $tab[$cle]['heure_debut'] < $heureF )
		   { $abs = $abs + 1; }
		}
	}
      }
  }

return $abs;
}

// fonction  qui permet de convertir des minutes en heure
function convert_minutes_heures($minutes)
{
	//combien d'heures ?
	$heure = floor($minutes / 60);

	//combien de minutes ?
	$minute = floor(($minutes - ($heure * 60)));
	  if ($minute < 10) { $minute = "0".$minute; }

	$temps = $heure."h".$minute;

  return($temps);
}

// fonction  qui permet de convertir des heure en minutes
function convert_heures_minutes($heures)
{
	// explose les heures pour avoir un tableau heure, minute
	$tab_heure = explode(':', $heures);

	//combien de minute dans une heures ?
	$total_minute = floor($tab_heure[0] * 60);

	//combien de minutes total
	if ( isset($tab_heure[1]) ) {
		$total_minute = $total_minute + $tab_heure[1];
	} else { $total_minute = $total_minute; }
       
  return($total_minute);
}

// fonction de conversion de numéro de mois en mois court
function convert_num_mois_court($numero)
{
  	$tab_mois = array('01'=>"Jan.", '02'=>"Fev.", '03'=>"Mar.", '04'=>"Avr.", '05'=>"Mai", '06'=>"Juin", '07'=>"Juil.", '08'=>"Aout", '09'=>"Sept.", '10'=>"Oct.", '11'=>"Nov.", '12'=>"Dec.");
	$mois_court = $tab_mois[$numero];
  return($mois_court);
}

// fonction permettant d'obtenir le tableau des horaires d'ouverture.
function ouverture() {

    global $prefix_base;

	$requete ="SELECT * FROM ".$prefix_base."horaires_etablissement WHERE date_horaire_etablissement = '0000-00-00'";
	$execution = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
	while ( $donnee = mysql_fetch_array($execution) )
	{
		$jour = $donnee['jour_horaire_etablissement'];
		$ouverture[$jour]['ouverture'] = $donnee['ouverture_horaire_etablissement'];
		$ouverture[$jour]['fermeture'] = $donnee['fermeture_horaire_etablissement'];
		$ouverture[$jour]['pause'] = $donnee['pause_horaire_etablissement'];
	}

    return($ouverture);
}

// fonction permettant de connaitre les jour d'absence ou autre
// en fonction du login, type(A|R), date du, date au)
function repartire($login, $type, $du, $au)
{
	global $prefix_base;

	     $i = '0';
	     date_default_timezone_get();
 	     $tableau_de_donnees = '';
             $requete = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$login."'  AND type_absence_eleve = '".$type."' ORDER BY d_date_absence_eleve ASC, d_heure_absence_eleve DESC";
             $execution = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
              while ( $donnee = mysql_fetch_array($execution))
                { 
		$i = '0'; $i2 = '0'; $heure_de_debut = '07:00:00'; $heure_de_fin = '19:00:00';
		// si la date de debut et de fin son identique on peut saisir les donnes tout de suite
		if($donnee['d_date_absence_eleve'] === $donnee['a_date_absence_eleve'])
		 {
			$tableau_de_donnees[$i2]['id'] = $i2;
			$tableau_de_donnees[$i2]['login'] = $donnee['eleve_absence_eleve'];
			$tableau_de_donnees[$i2]['jour'] = jour_sem_sql($donnee['d_date_absence_eleve']);
			$tableau_de_donnees[$i2]['date'] = $donnee['d_date_absence_eleve'];
			$tableau_de_donnees[$i2]['heure_debut'] = $donnee['d_heure_absence_eleve'];
			$tableau_de_donnees[$i2]['heure_fin'] = $donnee['a_heure_absence_eleve'];
			$i2 = $i2 + 1;
		 }
		// si la date de debut et de fin ne son pas identique alors on doit faire jour par jour pour la saisi des données
		if($donnee['d_date_absence_eleve'] != $donnee['a_date_absence_eleve'])
		 { 
			$jour_select = $donnee['d_date_absence_eleve'];
			// calcule le nombre de jour entre deux date
			$date1 = str_replace("-", "", $donnee['d_date_absence_eleve']);
			$date2 = str_replace("-", "", $donnee['a_date_absence_eleve']);
			$nbjours = floor((strtotime($date2) - strtotime($date1))/(60*60*24));
			$nb_jour_passe = '0'; $jour_passe = $donnee['d_date_absence_eleve'];
			while($nb_jour_passe<=$nbjours)
			 {
				$tableau_de_donnees[$i2]['id'] = $i2;
				$tableau_de_donnees[$i2]['login'] = $donnee['eleve_absence_eleve'];
				$tableau_de_donnees[$i2]['jour'] = jour_sem_sql($jour_passe);
				$tableau_de_donnees[$i2]['date'] = $jour_passe;
				if($jour_passe != $donnee['d_date_absence_eleve'] and $jour_passe != $donnee['a_date_absence_eleve'])
				{
					$tableau_de_donnees[$i2]['heure_debut'] = $heure_de_debut;
					$tableau_de_donnees[$i2]['heure_fin'] = $heure_de_fin;
				} else {
						if($jour_passe === $donnee['d_date_absence_eleve']) { $tableau_de_donnees[$i2]['heure_debut'] = $donnee['d_heure_absence_eleve']; } else { $tableau_de_donnees[$i2]['heure_debut'] = $heure_de_debut; }
						if($jour_passe === $donnee['a_date_absence_eleve']) { $tableau_de_donnees[$i2]['heure_fin'] = $donnee['a_heure_absence_eleve']; } else { $tableau_de_donnees[$i2]['heure_fin'] = $heure_de_fin; }
					}
				$tab_date = explode('-', $jour_passe);
				$jour_passe = date("Y-m-d", mktime(0, 0, 0, $tab_date[1], $tab_date[2]+1,  $tab_date[0]));
				$nb_jour_passe = $nb_jour_passe + 1;
				$i2 = $i2 + 1;
			 }
		 }
		$i = $i + 1;

		} 
return $tableau_de_donnees;
}

// fonction permettant de connaitre les jour d'absence ou autre
// en fonction du login, type(A|R), date du, date au)
function repartire_jour($login, $type, $du, $au)
{
	global $prefix_base;

	     $i = '0';
 	     date_default_timezone_get();
  	     $tableau_de_donnees = '';
             $requete = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$login."'  AND type_absence_eleve = '".$type."' ORDER BY d_date_absence_eleve ASC, d_heure_absence_eleve DESC";
             $execution = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
              while ( $donnee = mysql_fetch_array($execution))
                { 
		$i = '0'; $i2 = '0';
		$horraire = ouverture();
		// si la date de debut et de fin son identique on peut saisir les donnes tout de suite
		if($donnee['d_date_absence_eleve'] === $donnee['a_date_absence_eleve'])
		 {
			$date = $donnee['d_date_absence_eleve'];
			$tableau_de_donnees[$date]['login'] = $donnee['eleve_absence_eleve'];
			$tableau_de_donnees[$date]['jour'] = jour_sem_sql($donnee['d_date_absence_eleve']);
			$tab_jour = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
					$jour_num = $tableau_de_donnees[$date]['jour'];
					$jour = $tab_jour[$jour_num];
					if ( isset($horraire[$jour]) ) {
						$heure_de_debut = $horraire[$jour]['ouverture'];
						$heure_de_fin = $horraire[$jour]['fermeture'];
						$pause = $horraire[$jour]['pause'];
					} else {
							$heure_de_debut = '00:00:00';
							$heure_de_fin = '00:00:00';
							$pause = '00:00:00';
						}
			$tableau_de_donnees[$date]['date'] = $donnee['d_date_absence_eleve'];
			$tableau_de_donnees[$date]['heure_debut'] = $donnee['d_heure_absence_eleve'];
			$tableau_de_donnees[$date]['heure_fin'] = $donnee['a_heure_absence_eleve'];
			if ( $donnee['justify_absence_eleve'] === 'N' ) { $tableau_de_donnees[$date]['justifie'] = 'non'; }
			 else { $tableau_de_donnees[$date]['justifie'] = 'oui'; }

			// statistique par mois
			$tab_date = explode('-',$date);
			$mois = $tab_date[1];
			$annee = $tab_date[0];
			if ( !isset($tableau_de_donnees[$annee.'-'.$mois]) )
			{
				$tableau_de_donnees[$annee.'-'.$mois]['nb'] = 1;
				if ( $donnee['justify_absence_eleve'] === 'N' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_nj'] = 1; } else { $tableau_de_donnees[$annee.'-'.$mois]['nb_nj'] = 0; }
				if ( $donnee['justify_absence_eleve'] != 'N' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_j'] = 1; } else { $tableau_de_donnees[$annee.'-'.$mois]['nb_j'] = 0; }
			      if ( $donnee['type_absence_eleve'] === 'A' ) {
				// en heure
				$minute_debut = convert_heures_minutes($tableau_de_donnees[$date]['heure_debut']);
				$minute_fin = convert_heures_minutes($tableau_de_donnees[$date]['heure_fin']);
				$temp_total_min = ($minute_fin - $minute_debut ) - convert_heures_minutes($pause);
				if ( $tableau_de_donnees[$date]['justifie'] != 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_j'] = $temp_total_min; } else { $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_j'] = '0'; }
				if ( $tableau_de_donnees[$date]['justifie'] === 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_nj'] = $temp_total_min; } else { $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_nj'] = '0'; }
			      }
			} else {
					$tableau_de_donnees[$annee.'-'.$mois]['nb'] = $tableau_de_donnees[$annee.'-'.$mois]['nb'] + 1;
					if ( $tableau_de_donnees[$date]['justifie'] === 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_nj'] = $tableau_de_donnees[$annee.'-'.$mois]['nb_nj'] + 1; }
					if ( $tableau_de_donnees[$date]['justifie'] != 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_j'] = $tableau_de_donnees[$annee.'-'.$mois]['nb_j'] + 1; }
 				      if ( $donnee['type_absence_eleve'] === 'A' ) {
					// en heure
					$minute_debut = convert_heures_minutes($tableau_de_donnees[$date]['heure_debut']);
					$minute_fin = convert_heures_minutes($tableau_de_donnees[$date]['heure_fin']);
					$temp_total_min = ($minute_fin - $minute_debut ) - convert_heures_minutes($pause);
					if ( $tableau_de_donnees[$date]['justifie'] != 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_j'] = $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_j'] + $temp_total_min; }
					if ( $tableau_de_donnees[$date]['justifie'] === 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_nj'] = $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_nj'] + $temp_total_min; }
				      }
				}

				// statistique global
				if ( !isset($tableau_de_donnees['global']) )
				{
					$tableau_de_donnees['global']['nb'] = 1;
	 				      if ( $donnee['type_absence_eleve'] === 'A' ) {
						// en heure				
						$tableau_de_donnees['global']['nb_min'] = $temp_total_min;
					      }
				} else { 
						$tableau_de_donnees['global']['nb'] = $tableau_de_donnees['global']['nb'] + 1;
	 				      if ( $donnee['type_absence_eleve'] === 'A' ) {
						// en heure				
						$tableau_de_donnees['global']['nb_min'] = $tableau_de_donnees['global']['nb_min'] + $temp_total_min;
					      }
					}

			$i2 = $i2 + 1;
		 }
		// si la date de debut et de fin ne son pas identique alors on doit faire jour par jour pour la saisi des données
		if($donnee['d_date_absence_eleve'] != $donnee['a_date_absence_eleve'])
		 { 
			$jour_select = $donnee['d_date_absence_eleve'];
			// calcule le nombre de jour entre deux date
			$date1 = str_replace("-", "", $donnee['d_date_absence_eleve']);
			$date2 = str_replace("-", "", $donnee['a_date_absence_eleve']);
			$nbjours = floor((strtotime($date2) - strtotime($date1))/(60*60*24));
			$nb_jour_passe = '0'; $jour_passe = $donnee['d_date_absence_eleve'];
			while($nb_jour_passe<=$nbjours)
			 {
				$tableau_de_donnees[$jour_passe]['login'] = $donnee['eleve_absence_eleve'];
				$tableau_de_donnees[$jour_passe]['jour'] = jour_sem_sql($jour_passe);
					$tab_jour = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
					$jour_num = $tableau_de_donnees[$jour_passe]['jour'];
					$jour = $tab_jour[$jour_num];
					if ( isset($horraire[$jour]) ) {
						$heure_de_debut = $horraire[$jour]['ouverture'];
						$heure_de_fin = $horraire[$jour]['fermeture'];
						$pause = $horraire[$jour]['pause'];
					} else {
							$heure_de_debut = '00:00:00';
							$heure_de_fin = '00:00:00';
							$pause = '00:00:00';
						}

				$tableau_de_donnees[$jour_passe]['date'] = $jour_passe;
				if($jour_passe != $donnee['d_date_absence_eleve'] and $jour_passe != $donnee['a_date_absence_eleve'])
				{
					$tableau_de_donnees[$jour_passe]['heure_debut'] = $heure_de_debut;
					$tableau_de_donnees[$jour_passe]['heure_fin'] = $heure_de_fin;
				} else {
						if($jour_passe === $donnee['d_date_absence_eleve']) { $tableau_de_donnees[$jour_passe]['heure_debut'] = $donnee['d_heure_absence_eleve']; } else { $tableau_de_donnees[$jour_passe]['heure_debut'] = $heure_de_debut; }
						if($jour_passe === $donnee['a_date_absence_eleve']) { $tableau_de_donnees[$jour_passe]['heure_fin'] = $donnee['a_heure_absence_eleve']; } else { $tableau_de_donnees[$jour_passe]['heure_fin'] = $heure_de_fin; }
					}
				if ( $donnee['justify_absence_eleve'] === 'N' ) { $tableau_de_donnees[$jour_passe]['justifie'] = 'non'; }
				 else { $tableau_de_donnees[$jour_passe]['justifie'] = 'oui'; }

				// statistique par mois
				$tab_date = explode('-',$jour_passe);
				$mois = $tab_date[1];
				$annee = $tab_date[0];
				if ( !isset($tableau_de_donnees[$annee.'-'.$mois]) and isset($tableau_de_donnees[$jour_passe]) )
				{
					$tableau_de_donnees[$annee.'-'.$mois]['nb'] = 1;
					if ( $tableau_de_donnees[$jour_passe]['justifie'] === 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_nj'] = 1; } else { $tableau_de_donnees[$annee.'-'.$mois]['nb_nj'] = 0; }
					if ( $tableau_de_donnees[$jour_passe]['justifie'] != 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_j'] = 1; } else { $tableau_de_donnees[$annee.'-'.$mois]['nb_j'] = 0; }
				      if ( $donnee['type_absence_eleve'] === 'A' ) {
					// en heure
					$minute_debut = convert_heures_minutes($tableau_de_donnees[$jour_passe]['heure_debut']);
					$minute_fin = convert_heures_minutes($tableau_de_donnees[$jour_passe]['heure_fin']);
					$temp_total_min = ( $minute_fin - $minute_debut ) - convert_heures_minutes($pause);
					if ( $tableau_de_donnees[$jour_passe]['justifie'] != 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_j'] = $temp_total_min; } else { $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_j'] = '0'; }
					if ( $tableau_de_donnees[$jour_passe]['justifie'] === 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_nj'] = $temp_total_min; } else { $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_nj'] = '0'; }
				      }
				} else {
						$tableau_de_donnees[$annee.'-'.$mois]['nb'] = $tableau_de_donnees[$annee.'-'.$mois]['nb'] + 1;
						if ( $tableau_de_donnees[$jour_passe]['justifie'] === 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_nj'] = $tableau_de_donnees[$annee.'-'.$mois]['nb_nj'] + 1; }
						if ( $tableau_de_donnees[$jour_passe]['justifie'] != 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_j'] = $tableau_de_donnees[$annee.'-'.$mois]['nb_j'] + 1; }
					      if ( $donnee['type_absence_eleve'] === 'A' ) {
						// en heure
						$minute_debut = convert_heures_minutes($tableau_de_donnees[$jour_passe]['heure_debut']);
						$minute_fin = convert_heures_minutes($tableau_de_donnees[$jour_passe]['heure_fin']);
						$temp_total_min = ( $minute_fin - $minute_debut ) - convert_heures_minutes($pause);
						if ( $tableau_de_donnees[$jour_passe]['justifie'] != 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_j'] = $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_j'] + $temp_total_min; }
						if ( $tableau_de_donnees[$jour_passe]['justifie'] === 'non' ) { $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_nj'] = $tableau_de_donnees[$annee.'-'.$mois]['nb_heure_nj'] + $temp_total_min; }
					      }
					}

					// statistique global
					if ( !isset($tableau_de_donnees['global']) )
					{
						$tableau_de_donnees['global']['nb'] = 1;
 						      if ( $donnee['type_absence_eleve'] === 'A' ) {
							// en heure				
							$tableau_de_donnees['global']['nb_min'] = $temp_total_min;
						      }
					} else { 
						$tableau_de_donnees['global']['nb'] = $tableau_de_donnees['global']['nb'] + 1;
		  				      if ( $donnee['type_absence_eleve'] === 'A' ) {
							// en heure				
							$tableau_de_donnees['global']['nb_min'] = $tableau_de_donnees['global']['nb_min'] + $temp_total_min;
						      }
						}

				$jour_passe = date("Y-m-d", mktime(0, 0, 0, $tab_date[1], $tab_date[2]+1,  $tab_date[0]));
				$nb_jour_passe = $nb_jour_passe + 1;
				$i2 = $i2 + 1;
			 }
		 }
		$i = $i + 1;

		} 
return $tableau_de_donnees;
}

// permet de connaitre le type de semaine
function semaine_type($date)
{
    global $prefix_base;

	list($jour, $mois, $annee) = explode('/',$date);
	if ( empty($mois) ) {
		list($annee, $mois, $jour) = explode('-',$date);
	}
	$temps = mktime(00, 00, 00, $mois, $jour, $annee);
	$numero_de_la_semaine = date('W', $temps);

	$requete ="SELECT * FROM ".$prefix_base."edt_semaines WHERE num_edt_semaine = '".$numero_de_la_semaine."'";
	$execution = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
	$donnee = mysql_fetch_array($execution);
	$type_semaine = $donnee['type_edt_semaine'];

    return($type_semaine);
}

// permet de connaitre le type de motif d'une absences
function motif_type_abs($motif_absence)
{
    global $prefix_base;

	$requete ="SELECT * FROM ".$prefix_base."absences_motifs WHERE init_motif_absence = '".$motif_absence."'";
	$execution = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
	$donnee = mysql_fetch_array($execution);
	$motif_absence_design = $donnee['def_motif_absence'];

    return($motif_absence_design);
}

// fonction pour tronquer un texte
function tronquer_texte($texte, $longeur_max)
{
    if (strlen($texte) > $longeur_max)
    {
    $texte = substr($texte, 0, $longeur_max);
	// on ne coupe pas au dernier espace mais au carcatére pret
	// $dernier_espace = strrpos($texte, "");
	$dernier_espace = $longeur_max;
    $texte = substr($texte, 0, $dernier_espace)."...";
    }

    return $texte;
}

// fonction pour information du cpe d'un élève
function cpe_eleve($login_eleve)
{
    global $prefix_base;

	$requete ="SELECT * FROM ".$prefix_base."j_eleves_cpe jec, ".$prefix_base."utilisateurs u WHERE jec.e_login = '".$login_eleve."' AND jec.cpe_login = u.login";
	$execution = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
	$donnee = mysql_fetch_array($execution);
	$login_cpe = $donnee['login'];
	$civilite = $donnee['civilite'];
	$nom = $donnee['nom'];
	$prenom = $donnee['prenom'];

    return array('login' => $login_cpe, 'civilite' => $civilite, 'nom' => $nom, 'prenom' => $prenom);
}


?>
