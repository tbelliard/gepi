<?php

/**
 * Fichier de fonctions destinées au calendrier
 * 
 *
 * @copyright Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Pascal Fautrero
 * 
 * @package Emploi_du_temps
 * @subpackage fonctions
 */
/*
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


/**
 * Renvoie le numéro de la dernière semaine de l'année civile (52 ou 53)
 * @return int numéro de la dernière semaine
 */
function NumLastWeek() {
/* On regarde si on est entre Aout ou décembre auquel cas on est en année scolaire AA - AA+1
ou si on est avant auquel cas on est en année scolaire AA-1 - AA
*/
 if (date("m") >= 8) {
     $derniere_semaine=date("W",mktime(0, 0, 0, 12, 28, date("Y")));
 }else{
     $derniere_semaine=date("W",mktime(0, 0, 0, 12, 28, (date("Y")-1)));
 }
 return $derniere_semaine;
} 

/**
 *Affiche le nom de la période courante (si définie dans les edt)
 * @param type $date_ts 
 */
function AffichePeriode($date_ts) {
	$req_periode = mysql_query("SELECT * FROM edt_calendrier");
	$endprocess = false;
	while (($rep_periode = mysql_fetch_array($req_periode)) AND (!$endprocess)) {
		if (($rep_periode['debut_calendrier_ts'] <= $date_ts) AND ($rep_periode['fin_calendrier_ts'] >= $date_ts)) { 
			echo $rep_periode['nom_calendrier'];
			$endprocess = true;
		}
	}	
    
}

/**
 * Affiche les dates du lundi et du samedi de la semaine courante
 */
function AfficheDatesDebutFinSemaine() {

        $ts = time();
        while (date("D", $ts) != "Mon") {
        $ts-=86400;
        }
        echo strftime("%d %b ", $ts);
        $ts+=86400*5;
        echo " - ";
        echo strftime("%d %b %Y", $ts);
}

/**
 * Calcule le timestamp d'un jour
 * 
 * Calcule à partir du N° de la semaine stocké dans $_SESSION['week_selected'] 
 * et du N° du jour dans la semaine
 * @param type $jour
 * @return type
 * @TODO à quoi sert le premier test ? il ne manquerait pas un + 1 ?
 */
function RecupereTimestampJour ($jour) {
    if ((1<=$_SESSION['week_selected']) AND ($_SESSION['week_selected'] <= 28)) {
	    $annee = date("Y");
    }
    else {
	    $annee = date("Y");
    }
    $ts = mktime(0,0,0,1,4,$annee); // définition ISO de la semaine 01 : semaine du 4 janvier.
    while (date("D", $ts) != "Mon") {
	    $ts-=86400;
    }
    $semaine = 1;
    
    while ($semaine != $_SESSION['week_selected']) {
	    $ts+=86400*7;
	    $semaine++;
    }
	$timestamp = $ts+86400*($jour+0);
    return $timestamp;
}

/**
 * Calcule le timestamp d'un jour
 * 
 * Calcule à partir du N° de la semaine stocké dans $_SESSION['week_selected'] 
 * et du N° du jour dans la semaine
 * @param type $jour
 * @return type
 */
function RecupereTimestampJour_CDT2 ($jour) {
	//echo "U=".strftime("%U")."<br />";
	if(strftime("%U")<=28) {
		if ((1<=$_SESSION['week_selected']) AND ($_SESSION['week_selected'] <= 28)) {
			$annee = date("Y");
		}
		else {
			$annee = date("Y")-1;
		}
	}
	else {
		if ((1<=$_SESSION['week_selected']) AND ($_SESSION['week_selected'] <= 28)) {
			$annee = date("Y")+1;
		}
		else {
			$annee = date("Y");
		}
	}
	//echo "A=".$annee."<br />";

	$ts = mktime(0,0,0,1,4,$annee); // définition ISO de la semaine 01 : semaine du 4 janvier.
	while (date("D", $ts) != "Mon") {
		$ts-=86400;
	}
	$semaine = 1;

	while ($semaine != $_SESSION['week_selected']) {
		$ts+=86400*7;
		$semaine++;
	}
	$timestamp = $ts+86400*($jour+0);
	return $timestamp;
}

/**
 * Récupère les dates des lundis et vendredis de toutes les semaines de l'année scolaire courante
 * 
 * Usage : 
 * - $tab = RecupereLundisVendredis();
 * - echo $tab[0]["lundis"];         // renvoie la date du lundi de la semaine 01
 * - echo $tab[5]["vendredis"];      // renvoie la date du vendredi de la semaine 06 
 * @return array 
 */
function RecupereLundisVendredis () {

    $tab_select_semaine = array();
    
    if ((1<=date("n")) AND (date("n") <=8)) {
	    $annee = date("Y");
    }
    else {
	    $annee = date("Y")+1;
    }
    $ts = mktime(0,0,0,1,4,$annee); // définition ISO de la semaine 01 : semaine du 4 janvier.
    while (date("D", $ts) != "Mon") {
	    $ts-=86400;
    }
    $semaine = 1;
    $ts_ref = $ts;
    $tab_select_semaine[$semaine-1]["lundis"] = strftime("%d %b %Y", $ts);
    $tab_select_semaine[$semaine-1]["vendredis"] = strftime("%d %b %Y", $ts+86400*4);
    
    while ($semaine <=30) {
	    $ts+=86400*7;
	    $semaine++;
	    $tab_select_semaine[$semaine-1]["lundis"] = strftime("%d %b %Y", $ts);
	    $tab_select_semaine[$semaine-1]["vendredis"] = strftime("%d %b %Y", $ts+86400*4);
    }
    $semaine = NumLastWeek();
    $ts = $ts_ref;
    $ts-=86400*7;
	$tab_select_semaine[$semaine-1]["lundis"] = strftime("%d %b %Y", $ts);
	$tab_select_semaine[$semaine-1]["vendredis"] = strftime("%d %b %Y", $ts+86400*4);
    while ($semaine >=33) {
	    $ts-=86400*7;
	    $semaine--;
	    $tab_select_semaine[$semaine-1]["lundis"] = strftime("%d %b %Y", $ts);
	    $tab_select_semaine[$semaine-1]["vendredis"] = strftime("%d %b %Y", $ts+86400*4);
    }
    return $tab_select_semaine;
}

/** 
 * Récupère les dates des lundis et vendredis de toutes les semaines de l'année scolaire courante
 * 
 * Usage : 
 * - $tab = RecupereJoursSemaine();
 * - echo $tab[0]["lundis"];         // renvoie la date du lundi de la semaine 01
 * - echo $tab[5]["vendredis"];      // renvoie la date du vendredi de la semaine 06 
 * @return array 
 * @todo Quelles différence avec RecupereLundisVendredis ()
 * @see RecupereLundisVendredis()
 */
function RecupereJoursSemaine () {

    $tab_select_semaine = array();
    
    if ((1<=date("n")) AND (date("n") <=8)) {
	    $annee = date("Y");
    }
    else {
	    $annee = date("Y")+1;
    }
    $ts = mktime(0,0,0,1,4,$annee); // définition ISO de la semaine 01 : semaine du 4 janvier.
    while (date("D", $ts) != "Mon") {
	    $ts-=86400;
    }
    $semaine = 1;
    $ts_ref = $ts;
    $tab_select_semaine[$semaine-1]["lundi"] = strftime("%d", $ts);
    $tab_select_semaine[$semaine-1]["mardi"] = strftime("%d", $ts+86400*1);
    $tab_select_semaine[$semaine-1]["mercredi"] = strftime("%d", $ts+86400*2);
	$tab_select_semaine[$semaine-1]["jeudi"] = strftime("%d", $ts+86400*3);
    $tab_select_semaine[$semaine-1]["vendredi"] = strftime("%d", $ts+86400*4);
    $tab_select_semaine[$semaine-1]["samedi"] = strftime("%d", $ts+86400*5);
    
    while ($semaine <=30) {
	    $ts+=86400*7;
	    $semaine++;
    $tab_select_semaine[$semaine-1]["lundi"] = strftime("%d", $ts);
    $tab_select_semaine[$semaine-1]["mardi"] = strftime("%d", $ts+86400*1);
    $tab_select_semaine[$semaine-1]["mercredi"] = strftime("%d", $ts+86400*2);
	$tab_select_semaine[$semaine-1]["jeudi"] = strftime("%d", $ts+86400*3);
    $tab_select_semaine[$semaine-1]["vendredi"] = strftime("%d", $ts+86400*4);
    $tab_select_semaine[$semaine-1]["samedi"] = strftime("%d", $ts+86400*5);
    }
    $semaine = NumLastWeek();
    $ts = $ts_ref;
    $ts-=86400*7;
    $tab_select_semaine[$semaine-1]["lundi"] = strftime("%d", $ts);
    $tab_select_semaine[$semaine-1]["mardi"] = strftime("%d", $ts+86400*1);
    $tab_select_semaine[$semaine-1]["mercredi"] = strftime("%d", $ts+86400*2);
	$tab_select_semaine[$semaine-1]["jeudi"] = strftime("%d", $ts+86400*3);
    $tab_select_semaine[$semaine-1]["vendredi"] = strftime("%d", $ts+86400*4);
    $tab_select_semaine[$semaine-1]["samedi"] = strftime("%d", $ts+86400*5);
    while ($semaine >=33) {
	    $ts-=86400*7;
	    $semaine--;
    $tab_select_semaine[$semaine-1]["lundi"] = strftime("%d", $ts);
    $tab_select_semaine[$semaine-1]["mardi"] = strftime("%d", $ts+86400*1);
    $tab_select_semaine[$semaine-1]["mercredi"] = strftime("%d", $ts+86400*2);
	$tab_select_semaine[$semaine-1]["jeudi"] = strftime("%d", $ts+86400*3);
    $tab_select_semaine[$semaine-1]["vendredi"] = strftime("%d", $ts+86400*4);
    $tab_select_semaine[$semaine-1]["samedi"] = strftime("%d", $ts+86400*5);
    }
    return $tab_select_semaine;
}

/**
 * Renvoie "true" si des périodes sont définies
 * @return boolean 
 */
function PeriodesExistent() {
	$req_periode = mysql_query("SELECT * FROM edt_calendrier");
    if (mysql_num_rows($req_periode) > 0) {
        $retour = true;
    }
    else {
        $retour = false;
    }
    return $retour;
}

/**
 *Renvoie "true" si la période spécifiée existe
 * @param type $period
 * @return boolean 
 */
function PeriodExistsInDB($period) {
	$req_periode = mysql_query("SELECT id_calendrier FROM edt_calendrier WHERE id_calendrier='".$period."' ");
    if (mysql_num_rows($req_periode) > 0) {
        $retour = true;
    }
    else {
        $retour = false;
    }
    return $retour;
}

/**
 * Renvoie l'id de la première période de edt_calendrier
 * @return int 
 */
function ReturnFirstIdPeriod() {
	$req_periode = mysql_query("SELECT id_calendrier FROM edt_calendrier");
    $retour = 0;
	if ($rep_periode = mysql_fetch_array($req_periode)) {
    	$retour = $rep_periode['id_calendrier'];
	}
    return $retour;    
}

/**
 * Renvoie l'id de la période courante
 * @param timestamp $date_ts
 * @return int 
 */
function ReturnIdPeriod($date_ts) {
	$req_periode = mysql_query("SELECT * FROM edt_calendrier");
	$endprocess = false;
    $retour = 0;
	while (($rep_periode = mysql_fetch_array($req_periode)) AND (!$endprocess)) {
		if (($rep_periode['debut_calendrier_ts'] < $date_ts) AND ($rep_periode['fin_calendrier_ts'] > $date_ts)) { 
			$retour = $rep_periode['id_calendrier'];
			$endprocess = true;
		}
	}
    return $retour;    
}

/**
 * Renvoie l'id de la période suivant celle passée en argument
 * @param int $current_id_period
 * @return int
 * @see ReturnIdPeriod()
 */
function ReturnNextIdPeriod($current_id_period) {
	$req_periode = mysql_query("SELECT * FROM edt_calendrier ORDER BY debut_calendrier_ts ASC");
	$endprocess = false;
    $retour = ReturnIdPeriod(date("U"));
	while (($rep_periode = mysql_fetch_array($req_periode)) AND (!$endprocess)) {
		if ($rep_periode['id_calendrier'] == $current_id_period) { 
			$endprocess = true;
            if ($rep_periode = mysql_fetch_array($req_periode)) {
                $retour = $rep_periode['id_calendrier'];
            }
            else {
                mysql_data_seek($req_periode,0);
                $rep_periode = mysql_fetch_array($req_periode);
                $retour = $rep_periode['id_calendrier'];
            }
		}
	}
    return $retour;    
}

/**
 * Renvoie l'id de la période précédant celle passée en argument
 * @param int $current_id_period
 * @return int
 * @see ReturnIdPeriod()
 */
function ReturnPreviousIdPeriod($current_id_period) {
	$req_periode = mysql_query("SELECT * FROM edt_calendrier ORDER BY debut_calendrier_ts DESC");
	$endprocess = false;
    $retour = ReturnIdPeriod(date("U"));
	while (($rep_periode = mysql_fetch_array($req_periode)) AND (!$endprocess)) {
		if ($rep_periode['id_calendrier'] == $current_id_period) { 
			$endprocess = true;
            if ($rep_periode = mysql_fetch_array($req_periode)) {
                $retour = $rep_periode['id_calendrier'];
            }
            else {
                mysql_data_seek($req_periode,0);
                $rep_periode = mysql_fetch_array($req_periode);
                $retour = $rep_periode['id_calendrier'];
            }
		}
	}
    return $retour;    
}

/**
 * Retourne le type de la semaine en cours
 * @return string 
 */
function typeSemaineActu(){
		$retour = '0';
	$numero_sem_actu = date("W");
	$query = mysql_query("SELECT type_edt_semaine FROM edt_semaines WHERE num_edt_semaine = '".$numero_sem_actu."'");

	if (count($query) != 1) {
		$retour = '0';
	}else{
		$type = mysql_result($query, 0);
		$retour = $type;
	}
	return $retour;
}

/**
 * Retourne le jour en français et en toutes lettres
 * @param type $jour
 * @return string
 * @todo Pourquoi faire une boucle ?
 */
function retourneJour($jour){
	if ($jour === "") {
		$jour = date("w");
	}
	// On traduit le nom du jour
	$semaine = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
			$jour_semaine = '';
	for($a = 0; $a < 7; $a++) {
		if ($jour == $a) {
			$jour_semaine = $semaine[$a];
		}
	}
	return $jour_semaine;
}

/**
 * Fonction qui retourne l'id du créneau actuel
 * @return int 
 */
function retourneCreneau(){
		$retour = 'non';
	$heure = date("H:i:s");
	// On vérifie si on est dans un jour différent ou pas
	if (date("w") == getSettingValue("creneau_different")) {
		$table = 'edt_creneaux_bis';
	}else{
		$table = 'edt_creneaux';
	}
	$query = mysql_query("SELECT id_definie_periode FROM ".$table." WHERE
			heuredebut_definie_periode <= '".$heure."' AND
			heurefin_definie_periode > '".$heure."'")
				OR DIE('Le creneau n\'est pas trouvé : '.mysql_error());
	if ($query) {
		$reponse = mysql_fetch_array($query);
		$retour = $reponse["id_definie_periode"];
	}else {
		$retour = "non";
	}
	return $retour;
}

/**
 * Fonction qui retourne si on est dans la première ou la seconde partie d'un créneau
 * @return float 0 ou 0.5
 */
function heureDeb(){
		$retour = '0';
	// On compare des minutes car c'est plus simple
	$heureMn = (date("H") * 60) + date("i");
	$creneauId = retourneCreneau();
	// On vérifie si il existe un jour différent et si c'est aujourd'hui
	if (date("w") == getSettingValue("creneau_different")) {
		$table = "edt_creneaux_bis";
	}else {
		$table = "edt_creneaux";
	}
	// On récupère l'heure de début et celle de fin du créneau
	$query = mysql_query("SELECT heuredebut_definie_periode, heurefin_definie_periode FROM ".$table." WHERE id_definie_periode = '".$creneauId."'");
	if ($query) {
		$reponse = mysql_fetch_array($query);
		// On enlève les secondes
		$explodeDeb = explode(":", $reponse["heuredebut_definie_periode"]);
		$explodeFin = explode(":", $reponse["heurefin_definie_periode"]);
		$dureeCreneau = (($explodeFin[0] - $explodeDeb[0]) * 60) + ($explodeFin[1] - $explodeDeb[1]);
		$miCreneau = $dureeCreneau / 2;
		$heureMilieu = ($explodeDeb[0] * 60) + $explodeDeb[1] + $miCreneau;
		// et on compare
		if ($heureMn > $heureMilieu) {
			$retour = '0.5';
		}elseif($heureMn < $heureMilieu){
			$retour = '0';
		}else{
			$retour = '0';
		}
	}
	return $retour;
}

/**
 * Fonction qui retourne l'id du cours d'un prof à un créneau, jour et type_semaine donnés
 * @param string $prof Login de l'enseignant
 * @return int l'id du cours
 */
function retourneCours($prof){
		$retour = 'non';
	$query = mysql_query("SELECT id_cours FROM edt_cours, j_groupes_professeurs WHERE
			edt_cours.jour_semaine='".retourneJour('')."' AND
			edt_cours.id_definie_periode='".retourneCreneau()."' AND
			edt_cours.id_groupe=j_groupes_professeurs.id_groupe AND
			login='".$prof."' AND
			edt_cours.heuredeb_dec = '0' AND
			(edt_cours.id_semaine = '".typeSemaineActu()."' OR edt_cours.id_semaine = '0')
			ORDER BY edt_cours.id_semaine")
				or die('Erreur : retourneCours(prof) !'.mysql_error());
	$nbreCours = mysql_num_rows($query);
	if ($nbreCours >= 1) {
		$reponse = mysql_fetch_array($query);
		$retour = $reponse["id_cours"];
	}else{
		// On teste les AID
		$query_aid = mysql_query("SELECT id_cours FROM edt_cours WHERE
			jour_semaine = '".retourneJour('')."' AND
			id_definie_periode = '".retourneCreneau()."' AND
			id_aid != NULL AND
            id_aid != '' AND
			login_prof = '".$prof."' AND
			heuredeb_dec = '0' AND
			(id_semaine = '".typeSemaineActu()."' OR id_semaine = '0')
			ORDER BY id_semaine")
				or die('Erreur : retourneCours(prof) !'.mysql_error());
			$nbreCours = mysql_num_rows($query_aid);
		if ($nbreCours >= 1) {
			$reponse = mysql_fetch_array($query_aid);
			$retour = $reponse["id_cours"];
		}
	}
	return $retour;
}

?>
