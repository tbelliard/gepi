<?php
/*
 *
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
require_once("../../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

// variable définie
	$message_erreur = '';

// variable de sélection
	if (empty($_GET['id_alert_groupe']) and empty($_POST['id_alert_groupe'])) { $id_alert_groupe = ''; }
	   else { if (isset($_GET['id_alert_groupe'])) { $id_alert_groupe = $_GET['id_alert_groupe']; } if (isset($_POST['id_alert_groupe'])) { $id_alert_groupe = $_POST['id_alert_groupe']; } }
	if (empty($_GET['nom_alert_groupe']) and empty($_POST['nom_alert_groupe'])) { $nom_alert_groupe = ''; }
	   else { if (isset($_GET['nom_alert_groupe'])) { $nom_alert_groupe = $_GET['nom_alert_groupe']; } if (isset($_POST['nom_alert_groupe'])) { $nom_alert_groupe = $_POST['nom_alert_groupe']; } }
	if (empty($_GET['creerpar_alert_groupe']) and empty($_POST['creerpar_alert_groupe'])) { $creerpar_alert_groupe = ''; }
	   else { if (isset($_GET['creerpar_alert_groupe'])) { $creerpar_alert_groupe = $_GET['creerpar_alert_groupe']; } if (isset($_POST['creerpar_alert_groupe'])) { $creerpar_alert_groupe = $_POST['creerpar_alert_groupe']; } }

	if (empty($_GET['id_alert_type']) and empty($_POST['id_alert_type'])) { $id_alert_type = ''; }
	   else { if (isset($_GET['id_alert_type'])) { $id_alert_type = $_GET['id_alert_type']; } if (isset($_POST['id_alert_type'])) { $id_alert_type = $_POST['id_alert_type']; } }
	if (empty($_GET['groupe_alert_type']) and empty($_POST['groupe_alert_type'])) { $groupe_alert_type = ''; }
	   else { if (isset($_GET['groupe_alert_type'])) { $groupe_alert_type = $_GET['groupe_alert_type']; } if (isset($_POST['groupe_alert_type'])) { $groupe_alert_type = $_POST['groupe_alert_type']; } }
	if (empty($_GET['type_alert_type']) and empty($_POST['type_alert_type'])) { $type_alert_type = ''; }
	   else { if (isset($_GET['type_alert_type'])) { $type_alert_type = $_GET['type_alert_type']; } if (isset($_POST['type_alert_type'])) { $type_alert_type = $_POST['type_alert_type']; } }
	if (empty($_GET['specifisite_alert_type']) and empty($_POST['specifisite_alert_type'])) { $specifisite_alert_type = ''; }
	   else { if (isset($_GET['specifisite_alert_type'])) { $specifisite_alert_type = $_GET['specifisite_alert_type']; } if (isset($_POST['specifisite_alert_type'])) { $specifisite_alert_type = $_POST['specifisite_alert_type']; } }
		if (empty($_GET['specifisite_alert_type_p']) and empty($_POST['specifisite_alert_type_p'])) { $specifisite_alert_type_p = ''; }
		   else { if (isset($_GET['specifisite_alert_type_p'])) { $specifisite_alert_type_p = $_GET['specifisite_alert_type_p']; } if (isset($_POST['specifisite_alert_type_p'])) { $specifisite_alert_type_p = $_POST['specifisite_alert_type_p']; } }
		if (empty($_GET['specifisite_alert_type_c']) and empty($_POST['specifisite_alert_type_c'])) { $specifisite_alert_type_c = ''; }
		   else { if (isset($_GET['specifisite_alert_type_c'])) { $specifisite_alert_type_c = $_GET['specifisite_alert_type_c']; } if (isset($_POST['specifisite_alert_type_c'])) { $specifisite_alert_type_c = $_POST['specifisite_alert_type_c']; } }
		if (empty($_GET['specifisite_alert_type_f']) and empty($_POST['specifisite_alert_type_f'])) { $specifisite_alert_type_f = ''; }
		   else { if (isset($_GET['specifisite_alert_type_f'])) { $specifisite_alert_type_f = $_GET['specifisite_alert_type_f']; } if (isset($_POST['specifisite_alert_type_f'])) { $specifisite_alert_type_f = $_POST['specifisite_alert_type_f']; } }
		if ( $type_alert_type === 'P' ) { $specifisite_alert_type = $specifisite_alert_type_p; }
		if ( $type_alert_type === 'C' ) { $specifisite_alert_type = $specifisite_alert_type_c; }
		if ( $type_alert_type === 'F' ) { $specifisite_alert_type = $specifisite_alert_type_f; }
	if (empty($_GET['eleve_concerne']) and empty($_POST['eleve_concerne'])) { $eleve_concerne = ''; }
	   else { if (isset($_GET['eleve_concerne'])) { $eleve_concerne = $_GET['eleve_concerne']; } if (isset($_POST['eleve_concerne'])) { $eleve_concerne = $_POST['eleve_concerne']; } }
	if (empty($_GET['date_debut_comptage']) and empty($_POST['date_debut_comptage'])) { $date_debut_comptage = ''; }
	   else { if (isset($_GET['date_debut_comptage'])) { $date_debut_comptage = $_GET['date_debut_comptage']; } if (isset($_POST['date_debut_comptage'])) { $date_debut_comptage = $_POST['date_debut_comptage']; } }
	if (empty($_GET['nb_comptage_limit']) and empty($_POST['nb_comptage_limit'])) { $nb_comptage_limit = ''; }
	   else { if (isset($_GET['nb_comptage_limit'])) { $nb_comptage_limit = $_GET['nb_comptage_limit']; } if (isset($_POST['nb_comptage_limit'])) { $nb_comptage_limit = $_POST['nb_comptage_limit']; } }

	if (empty($_GET['etat_alert_eleve']) and empty($_POST['etat_alert_eleve'])) { $etat_alert_eleve = ''; }
	   else { if (isset($_GET['etat_alert_eleve'])) { $etat_alert_eleve = $_GET['etat_alert_eleve']; } if (isset($_POST['etat_alert_eleve'])) { $etat_alert_eleve = $_POST['etat_alert_eleve']; } }

	if (empty($_GET['action']) and empty($_POST['action'])) { $action = ''; }
	   else { if (isset($_GET['action'])) { $action = $_GET['action']; } if (isset($_POST['action'])) { $action = $_POST['action']; } }
	if (empty($_GET['action_sql']) and empty($_POST['action_sql'])) { $action_sql = ''; }
	   else { if (isset($_GET['action_sql'])) { $action_sql = $_GET['action_sql']; } if (isset($_POST['action_sql'])) { $action_sql = $_POST['action_sql']; } }
	if (empty($_GET['action_page']) and empty($_POST['action_page'])) { $action_page = ''; }
	   else { if (isset($_GET['action_page'])) { $action_page = $_GET['action_page']; } if (isset($_POST['action_page'])) { $action_page = $_POST['action_page']; } }

	// gestion des dates
	if (empty($_GET['du']) and empty($_POST['du'])) {$du = '';}
	 else { if (isset($_GET['du'])) {$du=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];} }
	if (empty($_GET['au']) and empty($_POST['au'])) {$au="JJ/MM/AAAA";}
	 else { if (isset($_GET['au'])) {$au=$_GET['au'];} if (isset($_POST['au'])) {$au=$_POST['au'];} }

		if (empty($_GET['day']) and empty($_POST['day'])) {$day=date("d");}
	    	 else { if (isset($_GET['day'])) {$day=$_GET['day'];} if (isset($_POST['day'])) {$day=$_POST['day'];} }
		if (empty($_GET['month']) and empty($_POST['month'])) {$month=date("m");}
		 else { if (isset($_GET['month'])) {$month=$_GET['month'];} if (isset($_POST['month'])) {$month=$_POST['month'];} }
		if (empty($_GET['year']) and empty($_POST['year'])) {$year=date("Y");}
		 else { if (isset($_GET['year'])) {$year=$_GET['year'];} if (isset($_POST['year'])) {$year=$_POST['year'];} }
	      	if ( !empty($du) ) {
		  $ou_est_on = explode('/',$du);
		  $year = $ou_est_on[2]; $month = $ou_est_on[1]; $day =  $ou_est_on[0];
	        } else { $du = $day."/".$month.'/'.$year; }

        //Configuration du calendrier
        include("../../lib/calendrier/calendrier.class.php");
        //$cal_1 = new Calendrier("form3", "date_debut_comptage[0]");
        $cal_1 = new Calendrier("form3", "date_debut_comptage");
        //include("../../lib/calendrier/calendrier_id.class.php");
        //$cal_1 = new Calendrier("form3", "date_debut_comptage_0");

	// Variable prédéfinit
	$date_ce_jour = date('d/m/Y');
	$date_ce_jour_sql = date('Y-m-d');


// Mes fonctions
	include("../lib/functions.php");

// fonction de sécuritée
// uid de pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post===$_SESSION['uid_prime']
 if(empty($_SESSION['uid_prime'])) { $_SESSION['uid_prime']=''; }
 if (empty($_GET['uid_post']) and empty($_POST['uid_post'])) {$uid_post='';}
    else { if (isset($_GET['uid_post'])) {$uid_post=$_GET['uid_post'];} if (isset($_POST['uid_post'])) {$uid_post=$_POST['uid_post'];} }
	$uid = md5(uniqid(microtime(), 1));
	$valide_form='';
	   // on remplace les %20 par des espaces
	    $uid_post = my_eregi_replace('%20',' ',$uid_post);
	if($uid_post===$_SESSION['uid_prime']) { $valide_form = 'yes'; } else { $valide_form = 'no'; }
	$_SESSION['uid_prime'] = $uid;
// fin de la fonction de sécuritée


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


//  SELECT departement, COUNT(membre) FROM la_table WHERE pays='france' GROUP BY departement
//  avec eventuellement un ORDER BY departement

// on recherche tout les élèves ayant 5 absence depuis le 01/01/2007
$type = 'suivi';
	// si absences, retard, dispense, infirmerie
if ( $type === 'absences' and $action_page != 'gestion_ag') {
		$table_de_comptage = 'absences_eleves';
		$identifiant_de_comptage = 'eleve_absence_eleve';
		$objet_de_comptage = 'type_absence_eleve';
		$type_comptage = 'A';
		$type_date_comptage = 'd_date_absence_eleve';
		$date_debut_comptage = '2007-01-01';
		$nb_comptage_limit = '1';
}
	// lettres
if ( $type === 'lettres' and $action_page != 'gestion_ag' ) {
		$table_de_comptage = 'lettres_suivis';
		$identifiant_de_comptage = 'quirecois_lettre_suivi';
		$objet_de_comptage = 'type_lettre_suivi';
		$type_comptage = '6';
		$type_date_comptage = 'emis_date_lettre_suivi';
		$date_debut_comptage = '2007-01-01';
		$nb_comptage_limit = '1';
}
	// fiche élève
if ( $type === 'suivi' and $action_page != 'gestion_ag' ) {
		$table_de_comptage = 'suivi_eleve_cpe';
		$identifiant_de_comptage = 'eleve_suivi_eleve_cpe';
		$objet_de_comptage = 'niveau_message_suivi_eleve_cpe';
		$type_comptage = '3';
		$type_date_comptage = 'date_suivi_eleve_cpe';
		$date_debut_comptage = '2007-01-01';
		$nb_comptage_limit = '1';
}

if ( $action_page != 'gestion_ag' ) {

	$i_cpt = 0;
	$nombre_de_type_passe = 0;
	$requete_alert_type = "SELECT * FROM ".$prefix_base."vs_alerts_types WHERE groupe_alert_type = '".$id_alert_groupe."'";
      	$resultat_alert_type = mysqli_query($GLOBALS["mysqli"], $requete_alert_type) or die('Erreur SQL !'.$requete_alert_type.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	// requete par requete on passe en boucle requete 1 requete 2 ex : 5 abs, 5 ret...
       	while ( $donnee_alert_type = mysqli_fetch_array($resultat_alert_type))
	{
		// les donnees
			$id_alert_type = $donnee_alert_type['id_alert_type'];
			$date_debut_comptage = $donnee_alert_type['date_debut_comptage'];
			$specifisite_alert_type = $donnee_alert_type['specifisite_alert_type'];
			$nb_comptage_limit = $donnee_alert_type['nb_comptage_limit'];
				$type_alert_type = $donnee_alert_type['type_alert_type'];
				if ( $type_alert_type === 'P' ) { $table_de_comptage = 'absences_eleves'; $identifiant_de_comptage = 'eleve_absence_eleve'; $type_date_comptage = 'd_date_absence_eleve'; $objet_de_comptage = 'type_absence_eleve'; }
				if ( $type_alert_type === 'C' ) { $table_de_comptage = 'lettres_suivis'; $identifiant_de_comptage = 'quirecois_lettre_suivi'; $type_date_comptage = 'emis_date_lettre_suivi'; $objet_de_comptage = 'type_lettre_suivi'; }
				if ( $type_alert_type === 'F' ) { $table_de_comptage = 'suivi_eleve_cpe'; $identifiant_de_comptage = 'eleve_suivi_eleve_cpe'; $type_date_comptage = 'date_suivi_eleve_cpe'; $objet_de_comptage = 'niveau_message_suivi_eleve_cpe'; }
				$nombre_de_type_passe = $nombre_de_type_passe + 1;

		// la recherche
		$requete = "SELECT ".$identifiant_de_comptage.", COUNT(".$objet_de_comptage.") AS count FROM ".$table_de_comptage." WHERE ( ".$objet_de_comptage."='".$specifisite_alert_type."' AND ".$type_date_comptage." >= '".$date_debut_comptage."' ) GROUP BY ".$identifiant_de_comptage." ORDER BY count DESC";
		$execution = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	       	while ( $donnee = mysqli_fetch_array($execution))
		{
			if ($donnee[1] < $nb_comptage_limit) { break; }
				$eleve[$i_cpt] = $donnee[0];
				$nb_fois[$i_cpt] = $donnee[1];
				$type_alert[$i_cpt] = $id_alert_type;
			$login = $donnee[0];


// si on trouve on regarde dans la base des alerts_eleves s'il n'existe pas une alerte déjà enregistré pour cette élève
	      		$test_existance = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."vs_alerts_eleves WHERE eleve_alert_eleve = '".$donnee[0]."' AND groupe_alert_eleve = '".$id_alert_groupe."'"),0);
			if ( $test_existance === '0' )
			{
				// si on rencontre un erreur on incrément le nombre d'erreur
				if ( isset($alert[$login]) ) { $alert[$login] = $alert[$login] + 1; } else { $alert[$login] = 1; }
			} else {
					$total_compteur = 0;
					$total_compteur_enregistrement = 0;
					$dernier_enregistrement = 0;
					// si oui on lit le total des trucs
			      		$requete_alert_eleve = "SELECT * FROM ".$prefix_base."vs_alerts_eleves WHERE ( eleve_alert_eleve = '".$donnee[0]."' AND groupe_alert_eleve = '".$id_alert_groupe."' ) ";
			               	$resultat_alert_eleve = mysqli_query($GLOBALS["mysqli"], $requete_alert_eleve) or die('Erreur SQL !'.$requete_alert_eleve.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					while ( $donnee_alert_eleve = mysqli_fetch_array($resultat_alert_eleve))
					{
						$total_compteur = $total_compteur + $donnee_alert_eleve['nb_trouve'];
						$dernier_enregistrement = $donnee_alert_eleve['nb_trouve'];
						$total_compteur_enregistrement = $total_compteur_enregistrement + 1;
					}
						// total de ce que l'on cherche à ne pas dépaser si on ajout la limit
						$total_anepas_depasser = $nb_comptage_limit + $dernier_enregistrement;
						// si le total a ne pas dépaser et plus petit que le total trouvé
						if ( $total_anepas_depasser <= $nb_fois[$i_cpt] )
						{
							// on ne prend que les plus par rapport au total des enregistrements
							$nb_trouve_parrapportautotal = $donnee[1] - $total_compteur;
							// si on rencontre un erreur on incrément le nombre d'erreur
							if ( isset($alert[$login]) ) { $alert[$login] = $alert[$login] + 1; } else { $alert[$login] = 1; }
						}
						// si non on passe
			         }

			$i_cpt = $i_cpt + 1;
		}
		((mysqli_free_result($execution) || (is_object($execution) && (get_class($execution) == "mysqli_result"))) ? true : false);
	}

	$i_cpt = 0;
	if ( isset($eleve[$i_cpt]) )
	{

		// création du code unique d'insertion
	        $date_t1 = date('Y-m-d');
	        $heure_t1 = date('H:i:s');
		$date_t1 = explode('-', $date_t1);
		$heure_t1 = explode(':', $heure_t1);

		while ( !empty($eleve[$i_cpt]) )
		{
			$login = $eleve[$i_cpt];
			if ( isset($alert[$login]) )
			{
				if ( $alert[$login] === $nombre_de_type_passe)
				{
					$temps_insert = $date_t1[0].$date_t1[1].$date_t1[2].$heure_t1[0].$heure_t1[1].$heure_t1[2].$login;
					// si il y a une alert sur tout les type alons on l'ajout
					$requete_alert_eleve = "INSERT INTO ".$prefix_base."vs_alerts_eleves (eleve_alert_eleve, date_alert_eleve, groupe_alert_eleve, type_alert_eleve, nb_trouve, temp_insert) VALUES ('".$login."', '".$date_ce_jour_sql."', '".$id_alert_groupe."', '".$type_alert[$i_cpt]."', '".$nb_fois[$i_cpt]."', '".$temps_insert."')";
				        $execution_requete_alert_eleve = mysqli_query($GLOBALS["mysqli"], $requete_alert_eleve);
				}
			}
			$i_cpt = $i_cpt + 1;
		}
	}

// si suivi
//$requete = "SELECT eleve_absence_eleve, COUNT(type_absence_eleve) FROM absences_eleves WHERE ( type_absence_eleve='A' AND d_date_absence_eleve >= '2007-01-01' ) GROUP BY eleve_absence_eleve ORDER BY type_absence_eleve";
//$requete = "SELECT statut, COUNT(login) FROM utilisateurs GROUP BY statut ORDER BY statut";
//$requete = "SELECT ".$identifiant_de_comptage.", COUNT(".$objet_de_comptage.") AS count FROM ".$table_de_comptage." WHERE ( ".$objet_de_comptage."='".$type_comptage."' AND ".$type_date_comptage." >= '".$date_debut_comptage."' ) GROUP BY ".$identifiant_de_comptage." ORDER BY count DESC";
//$execution = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
/*
while($donnee = mysql_fetch_array($execution))
{
	// on quite la boucle s'il n'y a plus de sélection correspondant au nombre demandé
	if ($donnee[1] < $nb_comptage_limit) { break; }

	echo $donnee[0];
	echo $donnee[1];

}*/

	// on vide la requete SQL
//	mysql_free_result($execution);
}

if ( ( $action_sql === 'nouveau_alert_groupe' or $action_sql === 'modifier_alert_groupe' ) and $valide_form === 'yes' ) {

	if ( $action_sql === 'nouveau_alert_groupe' ) { $requete = "INSERT INTO ".$prefix_base."vs_alerts_groupes (nom_alert_groupe, creerpar_alert_groupe) VALUES ('".$nom_alert_groupe."','".$_SESSION['login']."')"; }
	if ( $action_sql === 'modifier_alert_groupe' ) { $requete = "UPDATE ".$prefix_base."vs_alerts_groupes SET nom_alert_groupe = '".$nom_alert_groupe."', creerpar_alert_groupe = '".$_SESSION['login']."' WHERE  id_alert_groupe = '".$id_alert_groupe."'"; }
        $execution_requete = mysqli_query($GLOBALS["mysqli"], $requete);
	// connaitre le nouvelle id
	if ( $action_sql === 'nouveau_alert_groupe' ) { $id_alert_groupe = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res); }
	// vider les variables
	unset($nom_alert_groupe);
}

if ( $action_sql === 'supprimer_alert_groupe' and $valide_form === 'yes' ) {

	// on vérifie s'il existe une alerte
	      $test_existance = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."vs_alerts_groupes WHERE id_alert_groupe = '".$id_alert_groupe."'"),0);
		if ( $test_existance != '0' )
		{
	              $requete = "DELETE FROM ".$prefix_base."vs_alerts_groupes WHERE id_alert_groupe = '".$id_alert_groupe."'";
	              mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		      // on vérifie s'il existe des type définie pour ce groupe si oui on les supprimes
		      $test_existance = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."vs_alerts_types WHERE groupe_alert_type = '".$id_alert_groupe."'"),0);
			if ( $test_existance != '0' )
			{
				$requete = "DELETE FROM ".$prefix_base."vs_alerts_types WHERE groupe_alert_type = '".$id_alert_groupe."'";
		              	mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			}
		      // on vérifie s'il existe des alert eleve définie pour ce groupe si oui on les supprimes
		      $test_existance = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."vs_alerts_eleves WHERE groupe_alert_eleve = '".$id_alert_groupe."'"),0);
			if ( $test_existance != '0' )
			{
				$requete = "DELETE FROM ".$prefix_base."vs_alerts_eleves WHERE groupe_alert_eleve = '".$id_alert_groupe."'";
		              	mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			}
		} else { $message_erreur = 'Cette id n\'exite pas.'; }

}

if ( $action === 'modifier_alert_groupe' and $valide_form === 'yes' ) {

	// on vérifie s'il n'existe pas une alerte de même nom
	      $test_existance = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."vs_alerts_groupes WHERE id_alert_groupe = '".$id_alert_groupe."'"),0);
		if ( $test_existance != '0' )
		{
			$requete_alert_groupe = "SELECT * FROM ".$prefix_base."vs_alerts_groupes WHERE id_alert_groupe = '".$id_alert_groupe."'";
	               	$resultat_alert_groupe = mysqli_query($GLOBALS["mysqli"], $requete_alert_groupe) or die('Erreur SQL !'.$requete_alert_groupe.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	               	while ( $donnee_alert_groupe = mysqli_fetch_array($resultat_alert_groupe))
			{
				$nom_alert_groupe = $donnee_alert_groupe['nom_alert_groupe'];
			}
		} else { $message_erreur = 'Cette id n\'exite pas.'; }
}

if ( ( $action_sql === 'creer_alert_type' or $action_sql === 'modifier_alert_type' ) and $valide_form === 'yes' ) {

        if ( $action_sql === 'creer_alert_type' ) { $requete = "INSERT INTO ".$prefix_base."vs_alerts_types (groupe_alert_type, type_alert_type, specifisite_alert_type, eleve_concerne, date_debut_comptage, nb_comptage_limit) VALUES ('".$id_alert_groupe."','".$type_alert_type."', '".$specifisite_alert_type."', '', '".date_sql($date_debut_comptage)."', '".$nb_comptage_limit."')"; }
	if ( $action_sql === 'modifier_alert_type' ) { $requete = "UPDATE ".$prefix_base."vs_alerts_types SET type_alert_type = '".$type_alert_type."', specifisite_alert_type = '".$specifisite_alert_type."', eleve_concerne = '', date_debut_comptage = '".date_sql($date_debut_comptage)."', nb_comptage_limit = '".$nb_comptage_limit."' WHERE  id_alert_type = '".$id_alert_type."'"; }
        $execution_requete = mysqli_query($GLOBALS["mysqli"], $requete);
	$action_sql = '';
	$action = 'editer_alert_groupe';
}

if ( $action_sql === 'supprimer_alert_type' and $valide_form === 'yes' ) {

	// on vérifie s'il n'existe pas une alerte de même nom
	      $test_existance = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."vs_alerts_types WHERE id_alert_type = '".$id_alert_type."'"),0);
		if ( $test_existance != '0' )
		{
	              $requete = "DELETE FROM ".$prefix_base."vs_alerts_types WHERE id_alert_type = '".$id_alert_type."'";
	              mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		} else { $message_erreur = 'Cette id n\'exite pas.'; }
}

if ( $action === 'modifier_alert_type' and $valide_form === 'yes' ) {

	// on vérifie s'il n'existe pas une alerte de même nom
	      $test_existance = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."vs_alerts_types WHERE id_alert_type = '".$id_alert_type."'"),0);
		if ( $test_existance != '0' )
		{
			$requete_alert_type = "SELECT * FROM ".$prefix_base."vs_alerts_types WHERE id_alert_type = '".$id_alert_type."'";
	               	$resultat_alert_type = mysqli_query($GLOBALS["mysqli"], $requete_alert_type) or die('Erreur SQL !'.$requete_alert_type.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	               	while ( $donnee_alert_type = mysqli_fetch_array($resultat_alert_type))
			{
				$type_alert_type = $donnee_alert_type['type_alert_type'];
				$specifisite_alert_type = $donnee_alert_type['specifisite_alert_type'];
				$eleve_concerne = $donnee_alert_type['eleve_concerne'];
				$date_debut_comptage = date_fr($donnee_alert_type['date_debut_comptage']);
				$nb_comptage_limit = $donnee_alert_type['nb_comptage_limit'];
			}
		} else { $message_erreur = 'Cette id n\'exite pas.'; }
}

if ( $action_sql === 'modif_etat_ae' and $valide_form === 'yes' ) {
	if ( $etat_alert_eleve === '0' ) { $personnelle_active = ''; } else { $personnelle_active = $_SESSION['login']; }
	$requete = "UPDATE ".$prefix_base."vs_alerts_eleves SET etat_alert_eleve = '".$etat_alert_eleve."', etatpar_alert_eleve = '".$personnelle_active."' WHERE  id_alert_eleve = '".$id_alert_eleve."'";
        $execution_requete = mysqli_query($GLOBALS["mysqli"], $requete);
}

//**************** EN-TETE *****************
$titre_page = "Gestion des absences";
require_once("../../lib/header.inc.php");
//**************** FIN EN-TETE *****************

        // voir numero d'erreur = 2047 toutes les erreurs
        //echo(error_reporting());

?>
<script type="text/javascript" language="javascript">
function twAfficheCache(nObjet,nEtat) {
	document.getElementById(nObjet).style.visibility = (nEtat==0?'hidden':'visible');
}
</script>
<p class=bold><a href='gestion_absences.php?year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>'><img src="../../images/icons/back.png" alt="Retour" title="Retour" class="back_link" />&nbsp;Retour</a> |

<a href="impression_absences.php?year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Impression</a> |
<a href="statistiques.php?year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Statistiques</a> |
<a href="gestion_absences.php?choix=lemessager&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Le messager</a> |
<a href="alert_suivi.php?choix=alert&amp;year=<?php echo $year; ?>&amp;month=<?php echo $month; ?>&amp;day=<?php echo $day; ?>">Système d'alerte</a>
</p>

<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center; margin: auto; width: 95%;">

  <div style="border: 2px solid #BF0000; width: 25%; height: 150px; float: left; margin-top: 17px;">
	<div class="entete_alert"><b>Système d'alerte</b></div>
	<div>
		<form name="form1" method="post" action="alert_suivi.php">
			Choix du groupe d'alerte
	               <select name="id_alert_groupe" id="id_alert_groupe" tabindex="1" style="width: 98%; border : 1px solid #000000; margin-top: 5px;">
                 		<?php
				$requete_alert_groupe = "SELECT * FROM ".$prefix_base."vs_alerts_groupes ORDER BY nom_alert_groupe ASC";
	                    	$resultat_alert_groupe = mysqli_query($GLOBALS["mysqli"], $requete_alert_groupe) or die('Erreur SQL !'.$requete_alert_groupe.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	                    	while ( $donnee_alert_groupe = mysqli_fetch_array($resultat_alert_groupe))
				{ ?>
	                          <option value="<?php echo $donnee_alert_groupe['id_alert_groupe']; ?>" <?php if ( !empty($id_alert_groupe) and $id_alert_groupe === $donnee_alert_groupe['id_alert_groupe'] ) { ?>selected="selected"<?php } ?>><?php echo $donnee_alert_groupe['nom_alert_groupe']; ?></option>
        	          <?php } ?>
	               </select><br /><a href="alert_suivi.php?action_page=gestion_ag&amp;id_alert_groupe=<?php echo $id_alert_groupe; ?>&amp;action=editer_alert_groupe&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>">gérer les groupes d'alerte</a><br /><br />
		       <input type="hidden" name="action_page" value="alert" />
		       <input type="submit" name="submit1" value="Valider" tabindex="2" /><br />
		</form>
	</div>
  </div>

<?php if ( $action_page === '' or $action_page === 'alert' ) { ?>
  <div style="margin-left: 25%; width: 74%;">
	<div class="entete_alert_message">ALERTE EN COURS</div>
	<div class="scroll" style="background-color: #EFEFEF; border-left: 4px solid #BF0000;">
		<table style="width: 98%; margin: 5px;" cellspacing="1" cellpadding="0">
		  <tr class="entete_alert_message">
			<td style="padding: 2px; text-align: center; font-weight: bold; color: #E8F1F4;">Elève</td>
			<td style="width: 50px; padding: 2px; text-align: center; font-weight: bold; color: #E8F1F4;">Etat</td>
		  </tr>
		  <?php $i_cpt = 0; $i_couleur = '1';
	      		$requete_alert_eleve = "SELECT * FROM ".$prefix_base."vs_alerts_eleves ae, ".$prefix_base."vs_alerts_groupes ag, ".$prefix_base."eleves e WHERE ( ae.groupe_alert_eleve= '".$id_alert_groupe."' AND ae.eleve_alert_eleve = e.login AND ag.id_alert_groupe = ae.groupe_alert_eleve) GROUP BY ae.temp_insert";
	               	$resultat_alert_eleve = mysqli_query($GLOBALS["mysqli"], $requete_alert_eleve) or die('Erreur SQL !'.$requete_alert_eleve.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			while ( $donnee_alert_eleve = mysqli_fetch_array($resultat_alert_eleve))
			{
			  if ($i_couleur === '1') { $couleur_cellule = 'couleur_ligne_5'; $i_couleur = '2'; } else { $couleur_cellule = 'couleur_ligne_6'; $i_couleur = '1'; } ?>
		  <tr class="<?php echo $couleur_cellule; ?>">
			<td style="text-align: left; padding: 2px;"><a href="alert_suivi.php?id_alert_groupe=<?php echo $id_alert_groupe; ?>&amp;id_alert_eleve=<?php echo $donnee_alert_eleve['id_alert_eleve']; ?>#ea"><?php echo $donnee_alert_eleve['nom'].' '.$donnee_alert_eleve['prenom']; ?></a></td>
			<td style="width: 200px; padding: 2px;">
				<?php if ( $donnee_alert_eleve['etat_alert_eleve'] === '0' ) { ?>Non classé<?php } ?>
				<?php if ( $donnee_alert_eleve['etat_alert_eleve'] === '1' ) { ?>Convocation élève<?php } ?>
				<?php if ( $donnee_alert_eleve['etat_alert_eleve'] === '2' ) { ?>Déclassé<?php } ?>
			</td>
		  </tr>
		  <?php $i_cpt = $i_cpt + 1; } ?>
		</table>
	</div>
	<?php if ( isset($id_alert_eleve) and $id_alert_eleve != '' ) { ?>
		<div style="background-color: #EFEFEF; border-left: 4px solid #BF0000; width: 98.5%; margin-left: 4px; text-align: left;">
			<?php
				$requete_alert_eleve = "SELECT * FROM ".$prefix_base."vs_alerts_eleves vsae, ".$prefix_base."eleves e WHERE vsae.id_alert_eleve = '".$id_alert_eleve."' AND vsae.eleve_alert_eleve = e.login ORDER BY vsae.date_alert_eleve ASC, e.nom ASC, e.prenom ASC";
		               	$resultat_alert_eleve = mysqli_query($GLOBALS["mysqli"], $requete_alert_eleve) or die('Erreur SQL !'.$requete_alert_eleve.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	        	       	while ( $donnee_alert_eleve = mysqli_fetch_array($resultat_alert_eleve))
				{
					$login_eleve = $donnee_alert_eleve['eleve_alert_eleve'];
					$elenoet_eleve = $donnee_alert_eleve['elenoet'];
					$ele_id_eleve = $donnee_alert_eleve['ele_id'];
					$sexe_eleve = $donnee_alert_eleve['sexe'];
					switch ($sexe_eleve) {
						case 'F':
							$civilite_eleve = 'Mlle';
							break;
						case 'M':
							$civilite_eleve = 'M.';
							break;
						default:
							$civilite_eleve = '';
							break;
					}
					$nom_eleve = strtoupper($donnee_alert_eleve['nom']);
					$prenom_eleve = ucfirst($donnee_alert_eleve['prenom']);
					$naissance_eleve = date_frl(date_sql(affiche_date_naissance($donnee_alert_eleve['naissance'])));
				        $date_de_naissance = $donnee_alert_eleve['naissance'];
					$classe_eleve = classe_de($login_eleve);
					$responsable_eleve = tel_responsable($ele_id_eleve);

					// l'alert
					$date_debut_alert = $donnee_alert_eleve['date_alert_eleve'];
					$groupe_alert = $donnee_alert_eleve['groupe_alert_eleve'];
					$nb_compte = $donnee_alert_eleve['nb_trouve'];
					$etat_alert_eleve = $donnee_alert_eleve['etat_alert_eleve'];
					$etatpar_alert_eleve = $donnee_alert_eleve['etatpar_alert_eleve'];
				}
			?>
		<a name="ea"></a>

			<div style="width: 90px; float: right; padding: 2px; text-align: center;">
			<?php
			if ( getSettingValue("active_module_trombinoscopes")=='y' ) {
			    $nom_photo = nom_photo($elenoet_eleve,"eleves",2);
			    if ($nom_photo != NULL) $photos = $nom_photo;
			    //if ((!(file_exists($photos))) or ($nom_photo == "")) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
			    if ((!(file_exists($photos))) or ($nom_photo == NULL)) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
			    $valeur=redimensionne_image($photos);
          ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /><?php
		  }
			?>
			</div>
				<div style="font-size: 150%; background: #555555; color: #FFFFFF;"><?php echo $civilite_eleve.' '.$nom_eleve.' '.$prenom_eleve; ?></div>
				<div style="border-bottom: 2px solid #1B2BDF; width: 70%"><?php echo 'né(e) le '.$naissance_eleve.' - agé(e) de '.age($date_de_naissance).'ans<br />Classe de : <strong>'.$classe_eleve.'</strong>'; ?></div><br />
				<div style="margin-left: 10px; padding-left: 10px;"><strong>Les responsables :</strong><br />
					<?php
					$cpt_responsable = 0;
					while ( !empty($responsable_eleve[$cpt_responsable]) )
					{
						echo $responsable_eleve[$cpt_responsable]['civilite'].' '.strtoupper($responsable_eleve[$cpt_responsable]['nom']).' '.ucfirst($responsable_eleve[$cpt_responsable]['prenom']).'<br />';
						$telephone = '';
							if ( !empty($responsable_eleve[$cpt_responsable]['tel_pers']) ) { $telephone = $telephone.'Tél. <strong>'.$responsable_eleve[$cpt_responsable]['tel_pers'].'</strong> '; }
							if ( !empty($responsable_eleve[$cpt_responsable]['tel_prof']) ) { $telephone = $telephone.'Prof. <strong>'.$responsable_eleve[$cpt_responsable]['tel_prof'].'</strong> '; }
							if ( !empty($responsable_eleve[$cpt_responsable]['tel_port']) ) { $telephone = $telephone.'Port. '.$responsable_eleve[$cpt_responsable]['tel_port'].'<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="13" width="13" />'; }
						echo $telephone;
						$cpt_responsable = $cpt_responsable + 1;
					}
					?>
				</div><br />
				<div style="font-size: 150%; background: #555555; color: #FFFFFF;">Filtre</div>
				<div style="margin-left: 10px; padding-left: 10px;">
<?php

?>L'alerte que vous venez de sélectionner contient le(s) filtre(s) suiviant(s) :<br /><ul><?php
				$requete_alert_type = "SELECT * FROM ".$prefix_base."vs_alerts_groupes vsag, ".$prefix_base."vs_alerts_types vsat WHERE vsag.id_alert_groupe = '".$id_alert_groupe."' AND vsat.groupe_alert_type = vsag.id_alert_groupe";
		               	$resultat_alert_type = mysqli_query($GLOBALS["mysqli"], $requete_alert_type) or die('Erreur SQL !'.$requete_alert_type.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	        	       	while ( $donnee_alert_type = mysqli_fetch_array($resultat_alert_type))
				{
?><li>correspond à une limite de <strong><?php echo $donnee_alert_type['nb_comptage_limit']; ?></strong> de type <strong>
<?php
	$type_alert = '';
	if ( $donnee_alert_type['type_alert_type'] === 'C' ) { $type_alert = 'courrier'; }
	if ( $donnee_alert_type['type_alert_type'] === 'P' ) { $type_alert = 'Présence de l\'élève'; }
	if ( $donnee_alert_type['type_alert_type'] === 'F' ) { $type_alert = 'Action sur la fiche élève'; }

	$spec_type_alert = '';
	if ( $donnee_alert_type['type_alert_type'] === 'C' )
	{
		$spec_type_alert = lettre_type($donnee_alert_type['specifisite_alert_type']);
	}
	if ( $donnee_alert_type['type_alert_type'] === 'P' )
	{
		if ( $donnee_alert_type['specifisite_alert_type'] === 'A' ) { $spec_type_alert = 'absence'; }
		if ( $donnee_alert_type['specifisite_alert_type'] === 'R' ) { $spec_type_alert = 'retard'; }
		if ( $donnee_alert_type['specifisite_alert_type'] === 'D' ) { $spec_type_alert = 'dispense'; }
		if ( $donnee_alert_type['specifisite_alert_type'] === 'I' ) { $spec_type_alert = 'passage à l\'infirmerie'; }
	}
	if ( $donnee_alert_type['type_alert_type'] === 'F' )
	{
		$spec_type_alert = fiche_action_type($donnee_alert_type['specifisite_alert_type']);
	}
?>

<?php echo $type_alert.' ('.$spec_type_alert.')'; ?></strong> depuis le <strong><?php echo date_fr($donnee_alert_type['date_debut_comptage']); ?></strong>.</li><?php
				}
?></ul><br /><?php


/*
L'alerte que vous venez de sélectionner correspond à une limite de <?php echo $nb_compt_limit; ?> <?php echo $specification_alert; ?> depuis le <?php echo date_fr($date_debut_comptage); ?>.<br />
				<strong><?php echo $prenom_eleve; ?></strong> à franchit ce niveau le : <strong><?php echo date_fr($date_debut_alert); ?></strong> avec un total de <strong><?php echo $nb_compte; ?></strong>.
*/ ?>
				</div>



				<div style="font-size: 150%; background: #555555; color: #FFFFFF;">Etat de l'alerte</div>
				<div style="margin-left: 10px; padding-left: 10px;">
					<div class="information_etat_ae">
						<?php if ( $etat_alert_eleve === '0' ) { ?>Cette alerte n'a pas d'action sélectionnée pour l'instant <?php } ?>
						<?php if ( $etat_alert_eleve === '1' ) { ?>L'élève a été convoqué par <?php echo qui($etatpar_alert_eleve); ?> pour cette alerte<?php } ?>
						<?php if ( $etat_alert_eleve === '2' ) { echo qui($etatpar_alert_eleve); ?> ne tient pas compte de cette alerte<?php } ?>
					</div>

					<?php
					// seul la personne qui avait saisi peut modifier
					if ( strtoupper($etatpar_alert_eleve) === strtoupper($_SESSION['login']) or $etatpar_alert_eleve === '' ) { ?>
					<form name="form3" method="post" action="alert_suivi.php#ea">
				               	<input type="radio" name="etat_alert_eleve" id="eae1" value="1" onClick="javascript:document.form3.submit()" <?php  if ( $etat_alert_eleve === '1' ) { ?>checked="checked"<?php } ?> /><label for="eae1" style="cursor: pointer;">Convocation de l'élève.</label><br />
				               	<input type="radio" name="etat_alert_eleve" id="eae2" value="2" onClick="javascript:document.form3.submit()" <?php  if ( $etat_alert_eleve === '2' ) { ?>checked="checked"<?php } ?> /><label for="eae2" style="cursor: pointer;">Ne pas tenir compte de cette alerte.</label><br />
				               	<input type="radio" name="etat_alert_eleve" id="eae0" value="0" onClick="javascript:document.form3.submit()" <?php  if ( $etat_alert_eleve === '0' ) { ?>checked="checked"<?php } ?> /><label for="eae0" style="cursor: pointer;">Pas de sélection pour le moment.</label><br />
						<input type="hidden" name="id_alert_groupe" value="<?php echo $id_alert_groupe; ?>" />
						<input type="hidden" name="id_alert_eleve" value="<?php echo $id_alert_eleve; ?>" />
						<input type="hidden" name="action_sql" value="modif_etat_ae" />
						<input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
						<input type="submit" name="submit3" value="<?php if ( $etat_alert_eleve === '0' ) { ?>Valider<?php } else { ?>Modifier<?php } ?>" />
					</form>
					<?php } ?>
				</div>



		</div>
	<?php } ?>
  </div>
<?php } ?>

<?php if ( $action_page === 'gestion_ag' ) { ?>
  <div style="margin-left: 25%; width: 74%;">
	<div class="entete_alert_message">Gestion des groupes d'alerte</div>
	<div class="scroll" style="background-color: #EFEFEF; border-left: 4px solid #BF0000;">
		<form name="form2" method="post" action="alert_suivi.php">
		<label for="nom_alert_groupe" style="cursor: pointer;">Nouveau</label> <input name="nom_alert_groupe" id="nom_alert_groupe" value="<?php if ( isset($nom_alert_groupe) and !empty($nom_alert_groupe) ) { echo $nom_alert_groupe; } ?>" style="border: 1px solid #B3BFB8;" />
			<?php if ( $action === 'modifier_alert_groupe' ) { ?>
				<input type="hidden" name="id_alert_groupe" value="<?php echo $id_alert_groupe; ?>" />
			<?php } ?>
				<input type="hidden" name="action_sql" value="<?php if ( $action != 'modifier_alert_groupe' ) { ?>nouveau_alert_groupe<?php } else { ?>modifier_alert_groupe<?php } ?>" />
				<input type="hidden" name="action_page" value="<?php echo $action_page; ?>" />
				<input type="hidden" name="action" value="editer_alert_groupe" />
				<input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
				<input type="submit" name="submit2" value="<?php if ( $action != 'modifier_alert_groupe' ) { ?>Créer<?php } else { ?>Modifier<?php } ?>" />
		<?php if ( $message_erreur != '' ) { ?><span style="color: #FF0000; font-weight: bold;"><?php echo $message_erreur; ?></span><?php } ?>
		</form>
		<table style="width: 98%; margin: 5px;" cellspacing="1" cellpadding="0">
		  <tr class="entete_alert_message">
			<td style="padding: 2px; text-align: center; font-weight: bold; color: #E8F1F4;">Désignation du groupe d'alerte</td>
			<td style="width: 25px; padding: 2px; text-align: center; font-weight: bold; color: #E8F1F4;"></td>
			<td style="width: 25px; padding: 2px; text-align: center; font-weight: bold; color: #E8F1F4;"></td>
		  </tr>
		  <?php
			$i_couleur = '1';
			$requete_alert_groupe = "SELECT * FROM ".$prefix_base."vs_alerts_groupes ORDER BY nom_alert_groupe ASC";
	               	$resultat_alert_groupe = mysqli_query($GLOBALS["mysqli"], $requete_alert_groupe) or die('Erreur SQL !'.$requete_alert_groupe.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	               	while ( $donnee_alert_groupe = mysqli_fetch_array($resultat_alert_groupe))
			{
			  if ($i_couleur === '1') { $couleur_cellule = 'couleur_ligne_5'; $i_couleur = '2'; } else { $couleur_cellule = 'couleur_ligne_6'; $i_couleur = '1'; } ?>
			  <tr class="<?php echo $couleur_cellule; ?>">
				<td style="text-align: left; padding: 2px;"><a href="alert_suivi.php?action_page=<?php echo $action_page; ?>&amp;action=editer_alert_groupe&amp;id_alert_groupe=<?php echo $donnee_alert_groupe['id_alert_groupe']; ?>&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>#eg" title="editer le groupe"><?php echo $donnee_alert_groupe['nom_alert_groupe']; ?></a></td>
			        <td style="text-align: center;"><a href="alert_suivi.php?action_page=<?php echo $action_page; ?>&amp;action=modifier_alert_groupe&amp;id_alert_groupe=<?php echo $donnee_alert_groupe['id_alert_groupe']; ?>&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>"><img src="../images/modification.png" width="18" height="22" title="Modifier" border="0" alt="" /></a></td>
			        <td style="text-align: center;"><a href="alert_suivi.php?action_sql=supprimer_alert_groupe&amp;action_page=<?php echo $action_page; ?>&amp;id_alert_groupe=<?php echo $donnee_alert_groupe['id_alert_groupe']; ?>&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>" onClick="return confirm('Attention cela va supprimer toutes les alerts élève pour ce groupe...')"><img src="../images/x2.png" width="22" height="22" title="Supprimer" border="0" alt="" /></a></td>
			  </tr>
		<?php } ?>
		</table>
	</div>

	<?php if ( $id_alert_groupe != '' and ( $action === 'editer_alert_groupe' or $action === 'modifier_alert_type' ) ) { ?>
			<?php
			// on vérifie s'il existe des alert eleve définie si oui on ne peut plus ajouter ou modifier les type pour ce groupe
		      	$test_existance = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."vs_alerts_eleves WHERE groupe_alert_eleve = '".$id_alert_groupe."'"),0);
			if ( $test_existance != '0' ) { $editer_ce_groupe = 'non'; } else {  $editer_ce_groupe = 'oui'; } ?>
		<div style="background-color: #EFEFEF; border-left: 4px solid #BF0000; width: 98.5%; margin-left: 4px; text-align: left;">
		<a name="eg"></a>
	<?php if ( $editer_ce_groupe != 'non' ) { ?>
		<form name="form3" method="post" action="alert_suivi.php#eg">
		       Type
	               <select name="type_alert_type" id="type_alert_type" tabindex="1" style="border : 1px solid #000000; margin-top: 5px;">
	                          <option value="P" <?php if ( !empty($type_alert_type) and $type_alert_type === 'P' ) { ?>selected="selected"<?php } ?> onclick="twAfficheCache('monForm-A',1);twAfficheCache('monForm-B',0);twAfficheCache('monForm-C',0);">Présence</option>
	                          <option value="C" <?php if ( !empty($type_alert_type) and $type_alert_type === 'C' ) { ?>selected="selected"<?php } ?> onclick="twAfficheCache('monForm-B',1);twAfficheCache('monForm-A',0);twAfficheCache('monForm-C',0);">Courrier</option>
	                          <option value="F" <?php if ( !empty($type_alert_type) and $type_alert_type === 'F' ) { ?>selected="selected"<?php } ?> onclick="twAfficheCache('monForm-B',0);twAfficheCache('monForm-A',0);twAfficheCache('monForm-C',1);">Fiche élève</option>
	               </select>
		       <span id="monForm-A" style="position:absolute;visibility:hidden;">
	               <select name="specifisite_alert_type_p" id="specifisite_alert_type_p" tabindex="1" style="border: 1px solid #000000; margin-top: 5px;">
	                          <option value="A" <?php if ( !empty($specifisite_alert_type_p) and $specifisite_alert_type_p === 'A' ) { ?>selected="selected"<?php } ?>>Absences</option>
	                          <option value="R" <?php if ( !empty($specifisite_alert_type_p) and $specifisite_alert_type_p === 'R' ) { ?>selected="selected"<?php } ?>>Retard</option>
	                          <option value="D" <?php if ( !empty($specifisite_alert_type_p) and $specifisite_alert_type_p === 'D' ) { ?>selected="selected"<?php } ?>>Dispenses</option>
	                          <option value="I" <?php if ( !empty($specifisite_alert_type_p) and $specifisite_alert_type_p === 'I' ) { ?>selected="selected"<?php } ?>>Infirmerie</option>
	               </select></span>
			<span id="monForm-B" style="position:absolute;visibility:hidden;">
	                <select name="specifisite_alert_type_c" style="border: 1px solid #000000; margin-top: 5px;">
		<optgroup label="Type de lettre">
		    <?php
			$requete_lettre ="SELECT * FROM ".$prefix_base."lettres_types ORDER BY categorie_lettre_type ASC, titre_lettre_type ASC";
		        $execution_lettre = mysqli_query($GLOBALS["mysqli"], $requete_lettre) or die('Erreur SQL !'.$requete_lettre.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	  		while ($donner_lettre = mysqli_fetch_array($execution_lettre))
		  	 {
			   ?><option value="<?php echo $donner_lettre['id_lettre_type']; ?>" <?php if (isset($specifisite_alert_type_c) and $specifisite_alert_type_c === $donner_lettre['id_lettre_type']) { ?>selected="selected"<?php } ?>><?php echo ucfirst($donner_lettre['titre_lettre_type']); ?></option><?php echo "\n";
			 }
			?>
		</optgroup>
		  </select></span>
			<span id="monForm-C" style="position:absolute;visibility:hidden;">
	                <select name="specifisite_alert_type_f" style="border: 1px solid #000000; margin-top: 5px;">
		<optgroup label="Type action">
		    <?php
			$requete_action ="SELECT * FROM ".$prefix_base."absences_actions ORDER BY init_absence_action ASC";
		        $execution_action = mysqli_query($GLOBALS["mysqli"], $requete_action) or die('Erreur SQL !'.$requete_action.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	  		while ($donner_action = mysqli_fetch_array($execution_action))
		  	 {
			   ?><option value="<?php echo $donner_action['id_absence_action']; ?>" <?php if (isset($specifisite_alert_type_f) and $specifisite_alert_type_f === $donner_action['id_absence_action']) { ?>selected="selected"<?php } ?>><?php echo ucfirst($donner_action['def_absence_action']); ?></option><?php echo "\n";
			 }
			?>
		</optgroup>
		  </select></span>
			<br />
			à partir du&nbsp;<input name="date_debut_comptage" id="date_debut_comptage" onfocus="javascript:this.select()" type="text" value="<?php if ( isset($date_debut_comptage) and !empty($date_debut_comptage) ) { echo $date_debut_comptage; } else { echo $date_ce_jour; } ?>" size="10" maxlength="10" /><a href="#calend" onClick="<?php echo $cal_1->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
			au bout d'<input name="nb_comptage_limit" type="text" value="<?php if(isset($nb_comptage_limit) and !empty($nb_comptage_limit)) { echo $nb_comptage_limit; } else { ?>1<?php } ?>" size="2" maxlength="10" />fois
				<?php if ( $action === 'modifier_alert_type' ) { ?>
					<input type="hidden" name="id_alert_type" value="<?php echo $id_alert_type; ?>" />
				<?php } ?>
				<input type="hidden" name="id_alert_groupe" value="<?php echo $id_alert_groupe; ?>" />
				<input type="hidden" name="action" value="<?php echo $action; ?>" />
				<input type="hidden" name="action_sql" value="<?php if ( $action != 'modifier_alert_type' ) { ?>creer_alert_type<?php } else { ?>modifier_alert_type<?php } ?>" />
				<input type="hidden" name="action_page" value="<?php echo $action_page; ?>" />
				<input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
				<input type="submit" name="submit3" value="<?php if ( $action != 'modifier_alert_type' ) { ?>Ajouter<?php } else { ?>Modifier<?php } ?>" />
		</form>
		<?php } else { echo '* Modification du groupe impossible. Il y a des alerts d\'élèves relevé.'; } ?>
		<table style="width: 98%; margin: 5px;" cellspacing="1" cellpadding="0">
		  <tr class="entete_alert_message">
			<td style="padding: 2px; text-align: center; font-weight: bold; color: #E8F1F4;">Type</td>
			<td style="padding: 2px; text-align: center; font-weight: bold; color: #E8F1F4;">Spécificiter</td>
			<td style="padding: 2px; text-align: center; font-weight: bold; color: #E8F1F4;">Depuis le</td>
			<td style="padding: 2px; text-align: center; font-weight: bold; color: #E8F1F4;">Au bout de</td>
		      <?php if ( $editer_ce_groupe != 'non' ) { ?>
			<td style="width: 25px; padding: 2px; text-align: center; font-weight: bold; color: #E8F1F4;"></td>
			<td style="width: 25px; padding: 2px; text-align: center; font-weight: bold; color: #E8F1F4;"></td>
		      <?php } ?>
		  </tr>
		  <?php
			$i_couleur = '1';
			$requete_alert_type = "SELECT * FROM ".$prefix_base."vs_alerts_types WHERE ( groupe_alert_type = '".$id_alert_groupe."' ) ORDER BY date_debut_comptage ASC";
	               	$resultat_alert_type = mysqli_query($GLOBALS["mysqli"], $requete_alert_type) or die('Erreur SQL !'.$requete_alert_type.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	               	while ( $donnee_alert_type = mysqli_fetch_array($resultat_alert_type))
			{
			  if ($i_couleur === '1') { $couleur_cellule = 'couleur_ligne_5'; $i_couleur = '2'; } else { $couleur_cellule = 'couleur_ligne_6'; $i_couleur = '1'; } ?>
			  <tr class="<?php echo $couleur_cellule; ?>">
				<td style="text-align: left; padding: 2px;">
					<?php switch ( $donnee_alert_type['type_alert_type'] ) {
							case 'P':
								echo 'Présence';
								break;
							case 'C':
								echo 'Courrier';
								break;
							case 'F':
								echo 'Fiche élève';
								break;
							default:
								echo '';
								break;
						}
					?>
				</td>
				<td style="text-align: center; padding: 2px;"><?php echo $donnee_alert_type['specifisite_alert_type']; ?></td>
				<td style="text-align: center; padding: 2px;"><?php echo date_fr($donnee_alert_type['date_debut_comptage']); ?></td>
				<td style="text-align: center; padding: 2px;"><?php echo $donnee_alert_type['nb_comptage_limit']; ?> fois</td>
			      <?php if ( $editer_ce_groupe != 'non' ) { ?>
			        <td style="text-align: center;"><a href="alert_suivi.php?action_page=<?php echo $action_page; ?>&amp;id_alert_groupe=<?php echo $id_alert_groupe; ?>&amp;action=modifier_alert_type&amp;id_alert_type=<?php echo $donnee_alert_type['id_alert_type']; ?>&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>#eg"><img src="../images/modification.png" width="18" height="22" title="Modifier" border="0" alt="" /></a></td>
			        <td style="text-align: center;"><a href="alert_suivi.php?action_sql=supprimer_alert_type&amp;action_page=<?php echo $action_page; ?>&amp;action=<?php echo $action; ?>&amp;id_alert_groupe=<?php echo $id_alert_groupe; ?>&amp;id_alert_type=<?php echo $donnee_alert_type['id_alert_type']; ?>&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>#eg" onClick="return confirm('Etes-vous sur de vouloire le supprimer...')"><img src="../images/x2.png" width="22" height="22" title="Supprimer" border="0" alt="" /></a></td>
			      <?php } ?>
			  </tr>
		<?php } ?>
		</table>
		</div>
	<?php } ?>

  </div>
<?php } ?>

<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>

<?php require("../../lib/footer.inc.php"); ?>
