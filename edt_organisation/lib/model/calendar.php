<?php
/*
 *
 * Copyright 2011 Pascal Fautrero
 *
 * This file is part of GEPi.
 *
 * GEPi is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPi is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPi; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
include_once("./lib/model/edt_calendrier.php");
class calendar {

/*******************************************************************
 *
 *
 *******************************************************************/
	public function GenerateCalendar()
	{
		$result = '';
		$TableSemaines = calendar::getDaysTable();
		$TableDaysInPeriods = calendar::getDaysFromPeriods();
		$i = 1;
		foreach ($TableSemaines as $semaine) {
			if (in_array($i, $TableDaysInPeriods)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_period_".$semaine['lundi-mois']."\" >
						<div style=\"margin:3px;\">".$semaine['lundi']."</div>
					</div>
				";			
			}
			else {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_".$semaine['lundi-mois']."\">
						<div style=\"margin:3px;\">".$semaine['lundi']."</div>
					</div>
				";
			}
			$i++;
			if (in_array($i, $TableDaysInPeriods)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_period_".$semaine['mardi-mois']."\" >
						<div style=\"margin:3px;\">".$semaine['mardi']."</div>
					</div>
				";			
			}
			else {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_".$semaine['mardi-mois']."\">
						<div style=\"margin:3px;\">".$semaine['mardi']."</div>
					</div>
				";
			}
			$i++;
			if (in_array($i, $TableDaysInPeriods)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_period_".$semaine['mercredi-mois']."\" >
						<div style=\"margin:3px;\">".$semaine['mercredi']."</div>
					</div>
				";			
			}
			else {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_".$semaine['mercredi-mois']."\">
						<div style=\"margin:3px;\">".$semaine['mercredi']."</div>
					</div>
				";
			}
			$i++;
			if (in_array($i, $TableDaysInPeriods)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_period_".$semaine['jeudi-mois']."\" >
						<div style=\"margin:3px;\">".$semaine['jeudi']."</div>
					</div>
				";			
			}
			else {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_".$semaine['jeudi-mois']."\">
						<div style=\"margin:3px;\">".$semaine['jeudi']."</div>
					</div>
				";
			}
			$i++;
			if (in_array($i, $TableDaysInPeriods)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_period_".$semaine['vendredi-mois']."\" >
						<div style=\"margin:3px;\">".$semaine['vendredi']."</div>
					</div>
				";			
			}
			else {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_".$semaine['vendredi-mois']."\">
						<div style=\"margin:3px;\">".$semaine['vendredi']."</div>
					</div>
				";
			}
			$i++;
			if (in_array($i, $TableDaysInPeriods)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_period_".$semaine['samedi-mois']."\" >
						<div style=\"margin:3px;\">".$semaine['samedi']."</div>
					</div>
				";			
			}
			else {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_".$semaine['samedi-mois']."\">
						<div style=\"margin:3px;\">".$semaine['samedi']."</div>
					</div>
				";
			}
			$result.="	<div style=\"clear:both;\"></div>";
			$i++;
			
		}
		return $result;
	}
/*******************************************************************
 *
 *
 *******************************************************************/
	public static function getDayNumber($num_jour) {
		$result = array();
		setlocale (LC_TIME, 'fr_FR','fra');
		
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

		$semaine = calendar::getNumLastWeek();
		$ts_ref = $ts;
		while ($semaine >=33) {
			$ts-=86400*7;
			$semaine--;
		}
		$i = 1;
		while ($i < $num_jour) {
			$i++;
			$ts+=86400;
			if ($i%7 == 0) {
				$ts+=86400;			
			}
		}
		$i = 0;
		$result['timestamp'] = $ts;
		$result['day'] = strftime("%Y-%m-%d", $ts); 
		return $result;
	}
	
/*******************************************************************
 *
 *		echo $calendar::getCurrentWeek();
 *		result = string
 *		renvoie les dates du lundi et du samedi de la semaine courante
 *
 *******************************************************************/
	public static function getCurrentWeek() {
		$result = '';
        $ts = time();
        while (date("D", $ts) != "Mon") {
        $ts-=86400;
        }
        setlocale (LC_TIME, 'fr_FR','fra');
        $result .= strftime("%d %b ", $ts);
        $ts+=86400*5;
        $result.= " - ";
        $result .=strftime("%d %b %Y", $ts);
		return $result;
	}
	
/*******************************************************************
 *
 *		echo $calendar::getTypeCurrentWeek();
 *		result = string
 *		renvoie le type de la semaine courante (Semaine A ou Semaine B par exemple)
 *
 *******************************************************************/
	public static function getTypeCurrentWeek(){
		$retour = '';
		$numero_sem_actu = date("W");
		$query = mysql_query("SELECT type_edt_semaine FROM edt_semaines WHERE num_edt_semaine = '".$numero_sem_actu."'");
		if (count($query) == 1) {
			$type = mysql_result($query, 0);
			$retour = $type;
		}
		return $retour;
	}

/*******************************************************************
 *
 *		echo $calendar::getPeriodName(time());
 *		result = string
 *		renvoie le nom des périodes contenant le timestamp spécifié (si définies dans les edt)
 *
 *******************************************************************/

 	public static function getPeriodName($date_ts)
	{
		$req_periode = mysql_query("SELECT * FROM edt_calendrier");
		$endprocess = false;
		$result = '';
		while (($rep_periode = mysql_fetch_array($req_periode)) AND (!$endprocess)) {
			if (($rep_periode['debut_calendrier_ts'] <= $date_ts) AND ($rep_periode['fin_calendrier_ts'] >= $date_ts)) { 
				$result.= "<p>".$rep_periode['nom_calendrier']."</p>";
				//$endprocess = true;
			}
		}	
		return $result;
	}
/*******************************************************************
 *
 *		echo $calendar::getNumLastWeek();
 *		result = integer
 *		Renvoie le numéro de la dernière semaine de l'année civile (52 ou 53)
 *
 *******************************************************************/	

	public static function getNumLastWeek() {

		if (date("m") >= 8) {
			$derniere_semaine=date("W",mktime(0, 0, 0, 12, 28, date("Y")));
		}else{
			$derniere_semaine=date("W",mktime(0, 0, 0, 12, 28, (date("Y")-1)));
		}
		return $derniere_semaine;
	} 

/*******************************************************************
 *
 *		result = array
 *      Récupère les dates des lundis et vendredis de toutes les semaines de l'année scolaire courante
 *      Usage : 
 *      $tab = $calendar::getDaysTable();
 *      echo $tab[0]["lundis"];         // renvoie la date du lundi de la semaine 01     
 *      echo $tab[5]["vendredis"];      // renvoie la date du vendredi de la semaine 06 
 *
 *******************************************************************/

	public static function getDaysTable () {

    $tab_select_semaine = array();
    setlocale (LC_TIME, 'fr_FR','fra');
    
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

    $semaine = calendar::getNumLastWeek();
    $ts_ref = $ts;
	while ($semaine >=33) {
		$ts-=86400*7;
		$semaine--;
	}
	$i = 0;
	$tab_select_semaine[$i]["lundi"] = strftime("%d", $ts);
    $tab_select_semaine[$i]["mardi"] = strftime("%d", $ts+86400*1);
    $tab_select_semaine[$i]["mercredi"] = strftime("%d", $ts+86400*2);
	$tab_select_semaine[$i]["jeudi"] = strftime("%d", $ts+86400*3);
    $tab_select_semaine[$i]["vendredi"] = strftime("%d", $ts+86400*4);
    $tab_select_semaine[$i]["samedi"] = strftime("%d", $ts+86400*5);
    $tab_select_semaine[$i]["dimanche"] = strftime("%d", $ts+86400*6);

    $tab_select_semaine[$i]["lundi-mois"] = strftime("%m", $ts);
    $tab_select_semaine[$i]["mardi-mois"] = strftime("%m", $ts+86400*1);
    $tab_select_semaine[$i]["mercredi-mois"] = strftime("%m", $ts+86400*2);
	$tab_select_semaine[$i]["jeudi-mois"] = strftime("%m", $ts+86400*3);
    $tab_select_semaine[$i]["vendredi-mois"] = strftime("%m", $ts+86400*4);
    $tab_select_semaine[$i]["samedi-mois"] = strftime("%m", $ts+86400*5);
    $tab_select_semaine[$i]["dimanche-mois"] = strftime("%m", $ts+86400*6);
    while ($semaine <=calendar::getNumLastWeek()) {
	    $ts+=86400*7;
	    $semaine++;
		$i++;
	$tab_select_semaine[$i]["lundi"] = strftime("%d", $ts);
    $tab_select_semaine[$i]["mardi"] = strftime("%d", $ts+86400*1);
    $tab_select_semaine[$i]["mercredi"] = strftime("%d", $ts+86400*2);
	$tab_select_semaine[$i]["jeudi"] = strftime("%d", $ts+86400*3);
    $tab_select_semaine[$i]["vendredi"] = strftime("%d", $ts+86400*4);
    $tab_select_semaine[$i]["samedi"] = strftime("%d", $ts+86400*5);
    $tab_select_semaine[$i]["dimanche"] = strftime("%d", $ts+86400*6);

    $tab_select_semaine[$i]["lundi-mois"] = strftime("%m", $ts);
    $tab_select_semaine[$i]["mardi-mois"] = strftime("%m", $ts+86400*1);
    $tab_select_semaine[$i]["mercredi-mois"] = strftime("%m", $ts+86400*2);
	$tab_select_semaine[$i]["jeudi-mois"] = strftime("%m", $ts+86400*3);
    $tab_select_semaine[$i]["vendredi-mois"] = strftime("%m", $ts+86400*4);
    $tab_select_semaine[$i]["samedi-mois"] = strftime("%m", $ts+86400*5);
    $tab_select_semaine[$i]["dimanche-mois"] = strftime("%m", $ts+86400*6);
    }
	
    $semaine = 1;
    $ts_ref = $ts;
	$i++;
	$tab_select_semaine[$i]["lundi"] = strftime("%d", $ts);
    $tab_select_semaine[$i]["mardi"] = strftime("%d", $ts+86400*1);
    $tab_select_semaine[$i]["mercredi"] = strftime("%d", $ts+86400*2);
	$tab_select_semaine[$i]["jeudi"] = strftime("%d", $ts+86400*3);
    $tab_select_semaine[$i]["vendredi"] = strftime("%d", $ts+86400*4);
    $tab_select_semaine[$i]["samedi"] = strftime("%d", $ts+86400*5);
    $tab_select_semaine[$i]["dimanche"] = strftime("%d", $ts+86400*6);

    $tab_select_semaine[$i]["lundi-mois"] = strftime("%m", $ts);
    $tab_select_semaine[$i]["mardi-mois"] = strftime("%m", $ts+86400*1);
    $tab_select_semaine[$i]["mercredi-mois"] = strftime("%m", $ts+86400*2);
	$tab_select_semaine[$i]["jeudi-mois"] = strftime("%m", $ts+86400*3);
    $tab_select_semaine[$i]["vendredi-mois"] = strftime("%m", $ts+86400*4);
    $tab_select_semaine[$i]["samedi-mois"] = strftime("%m", $ts+86400*5);
    $tab_select_semaine[$i]["dimanche-mois"] = strftime("%m", $ts+86400*6);
    while ($semaine <=30) {
	    $ts+=86400*7;
	    $semaine++;
		$i++;
	$tab_select_semaine[$i]["lundi"] = strftime("%d", $ts);
    $tab_select_semaine[$i]["mardi"] = strftime("%d", $ts+86400*1);
    $tab_select_semaine[$i]["mercredi"] = strftime("%d", $ts+86400*2);
	$tab_select_semaine[$i]["jeudi"] = strftime("%d", $ts+86400*3);
    $tab_select_semaine[$i]["vendredi"] = strftime("%d", $ts+86400*4);
    $tab_select_semaine[$i]["samedi"] = strftime("%d", $ts+86400*5);
    $tab_select_semaine[$i]["dimanche"] = strftime("%d", $ts+86400*6);

    $tab_select_semaine[$i]["lundi-mois"] = strftime("%m", $ts);
    $tab_select_semaine[$i]["mardi-mois"] = strftime("%m", $ts+86400*1);
    $tab_select_semaine[$i]["mercredi-mois"] = strftime("%m", $ts+86400*2);
	$tab_select_semaine[$i]["jeudi-mois"] = strftime("%m", $ts+86400*3);
    $tab_select_semaine[$i]["vendredi-mois"] = strftime("%m", $ts+86400*4);
    $tab_select_semaine[$i]["samedi-mois"] = strftime("%m", $ts+86400*5);
    $tab_select_semaine[$i]["dimanche-mois"] = strftime("%m", $ts+86400*6);
    }

    return $tab_select_semaine;
}	
	
/*******************************************************************
 *
 *
 *******************************************************************/

	public static function getDaysFromPeriods () {

		$period = PeriodeCalendaire::getPeriods();
		$tab_period = array();
		
		setlocale (LC_TIME, 'fr_FR','fra');
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
		$ts_max = $ts + 86400*7*36;
		$semaine = calendar::getNumLastWeek();
		$ts_ref = $ts;
		while ($semaine >=33) {
			$ts-=86400*7;
			$semaine--;
		}
		// =====================================================
		$i = 1;$j = 0;$stop = false;
		while (($ts <= $ts_max) && (!$stop)) {

			$ts_sup = $ts + 86400 - 1;
			if (($ts <= $period['debut'][$j]) && ($period['debut'][$j] <= $ts_sup)) {
				//echo $ts." ".$period['debut'][$j]." ".$i."<br/>";
				$tab_period[] = $i;
				$ts+=86400;
				$i++;
				if ($i%7 == 0) {
					$ts+=86400;						
				}
				while ($period['fin'][$j] >= $ts) {
					//echo $ts." ".$period['debut'][$j]." ".$i."<br/>";
					$tab_period[] = $i;
					$ts+=86400;
					$i++;	
					if ($i%7 == 0) {
						$ts+=86400;						
					}
				}
				$j++;
				if (!isset($period['debut'][$j])) {
					$stop = true;
				}
			
			}
			else {
				$ts+=86400;
				$i++;
				if ($i%7 == 0) {
					$ts+=86400;						
				}
			}
		}
		return $tab_period;
	}		
}
?>