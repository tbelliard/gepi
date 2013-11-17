<?php
//fonction permettant de connaître la classe d'un élève par son login
function classe_de($id_classe_eleve) {
    global $prefix_base;
    $requete_classe_eleve ="SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.classe, ".$prefix_base."classes.id, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."eleves.login='".$id_classe_eleve."' AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id";
    $execution_classe_eleve = mysqli_query($GLOBALS["___mysqli_ston"], $requete_classe_eleve) or die('Erreur SQL !'.$requete_classe_eleve.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    $data_classe_eleve = mysqli_fetch_array($execution_classe_eleve);
    $id_classe_eleve = $data_classe_eleve['nom_complet'];
    return($id_classe_eleve);
}

//fonction permettant de connaître le motif d'une absence
function motif_de($nc_motif) {
    global $prefix_base;
    $requete_motif ="SELECT * FROM ".$prefix_base."absences_motifs WHERE ".$prefix_base."absences_motifs.init_motif_absence='".$nc_motif."'";
    $execution_motif = mysqli_query($GLOBALS["___mysqli_ston"], $requete_motif) or die('Erreur SQL !'.$requete_motif.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    $data_motif = mysqli_fetch_array($execution_motif);
    $nc_motif = $data_motif['def_motif_absence'];
    return($nc_motif);
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
  $heure = "$heure";
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
      $heure = ($tab_heure[0]."h ".$tab_heure[1]."min");
  }
  return($heure);
  }

function heure_court($heure)
  {
  $heure = "$heure";
  $tab_heure = explode(':', $heure);

  if ($tab_heure[0]==0)
  {
      $heure = "";
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
  $tab_mois = array('01'=>"Janvier", '02'=>"Fevrier", '03'=>"Mars", '04'=>"Avril", '05'=>"Mai", '06'=>"Juin", '07'=>"Juillet", '08'=>"Aout", '09'=>"Septembre", '10'=>"Octobre", '11'=>"Novembre", '12'=>"Decembre");
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

function verif_date($date_a_verif)
 {
    if(my_eregi("[0-9]{4}-[0-9]{2}-[0-9]{2}",$date_a_verif))
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
      $execution_login_perso = mysqli_query($GLOBALS["___mysqli_ston"], $requete_login_perso) or die('Erreur SQL !'.$requete_login_perso.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
      $data_login_perso = mysqli_fetch_array($execution_login_perso);
      if($login_perso != "") { $qui_est_ce = $data_login_perso['civilite']." ".strtoupper($data_login_perso['nom'])." ".ucfirst($data_login_perso['prenom']); } else { $qui_est_ce=""; }
      return $qui_est_ce;
}

function qui_eleve($login_perso)
{
      global $prefix_base;
      $requete_login_perso ="SELECT * FROM ".$prefix_base."eleves WHERE ".$prefix_base."eleves.login = '".$login_perso."'";
      $execution_login_perso = mysqli_query($GLOBALS["___mysqli_ston"], $requete_login_perso) or die('Erreur SQL !'.$requete_login_perso.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
      $data_login_perso = mysqli_fetch_array($execution_login_perso);
      if($login_perso != "") { $qui_est_ce = strtoupper($data_login_perso['nom'])." ".ucfirst($data_login_perso['prenom']); } else { $qui_est_ce=""; }
      return $qui_est_ce;
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
      	  $resultat = mysqli_query($GLOBALS["___mysqli_ston"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	  while($donner = mysqli_fetch_array($resultat))
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
      $requete_periode = ('SELECT * FROM edt_creneaux WHERE heuredebut_definie_periode <= "'.$heure_choix .'" AND heurefin_definie_periode >= "'.$heure_choix.'" ORDER BY heuredebut_definie_periode, nom_definie_periode ASC');
      $resultat_periode = mysqli_query($GLOBALS["___mysqli_ston"], $requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
      while($data_periode = mysqli_fetch_array($resultat_periode)) {
          $debut=$data_periode['heuredebut_definie_periode'];
          $num_periode=$data_periode['id_definie_periode'];
      }
      return($num_periode);
  }

//connaitre l'heure du début soit de la fin d'une période
// ex: perdiode_heure($id_periode) > [0]11:00:00 [1]11:55:00
function periode_heure($periode)
{
      // on recherche les informations sur la périodes sélectionné
      $requete_periode = ('SELECT * FROM edt_creneaux WHERE id_definie_periode = "'.$periode.'"');
      $resultat_periode = mysqli_query($GLOBALS["___mysqli_ston"], $requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
      while($data_periode = mysqli_fetch_array($resultat_periode)) {
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
      $resultat = mysqli_query($GLOBALS["___mysqli_ston"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
      while($donnee = mysqli_fetch_array($resultat)) {
          $nomcomplet = $donnee['nom_complet'];
          $nomcourt   = $donnee['matiere'];
      }
      return array('nomcomplet'=> $nomcomplet, 'nomcourt'=>$nomcourt);
}
?>
