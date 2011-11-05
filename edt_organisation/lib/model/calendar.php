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
 *
 * updateTables
 * GenerateCalendar
 * getDayNumber
 * getCurrentWeek
 * getTypeCurrentWeek
 * getPeriodName
 * getSinglePeriod
 * getNumLastWeek
 * getDaysTable
 * getTimestampFromDay
 * getPeriodFromDay
 * getDaysFromPeriods
 * getFrontiersPeriods
 *
 *
 *
 *
 *
 */
include_once("./lib/model/classes.php");
include_once("./lib/model/edt_calendrier.php");
include_once("./lib/model/edt_calendrier_manager.php");
include_once("./lib/model/edt_j_calendar_classes.php");
class calendar {


/*******************************************************************
 *
 *
 *******************************************************************/	
    public static function updateTables() {	
	
		// ===============================================================
		$sql = "CREATE TABLE IF NOT EXISTS edt_j_calendar_classes (
					id_calendar INT,
					id_classe INT
					) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
		$req_creation = mysql_query($sql);
		$sql = "CREATE TABLE IF NOT EXISTS edt_calendrier_manager (
					id INT AUTO_INCREMENT,
					nom_calendrier TEXT,
					PRIMARY KEY (id)
					) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
		$req_creation = mysql_query($sql);
		$sql = "SELECT id FROM edt_calendrier_manager";
		$req_calendar = mysql_query($sql);
		$id_primary = 0;
		if ($req_calendar) {
			if (mysql_num_rows($req_calendar) == 0) {
				$sql = "INSERT INTO edt_calendrier_manager SET
					nom_calendrier = 'calendrier 1' ";
				$req = mysql_query($sql);
				if ($req) {
					$sql = "SELECT id FROM edt_calendrier_manager";
					$req = mysql_query($sql);
					if ($req) {
						$rep = mysql_fetch_array($req);
						$id_primary = $rep['id'];
					}
				
				}
			}
		}

		

		// ===============================================================
		$sql = "SHOW COLUMNS FROM edt_calendrier ";
        $req_colonne = mysql_query($sql);
		$nomsChamps = array();
		if ($req_colonne) {
			while ($rep = mysql_fetch_array($req_colonne)) {
				$nomsChamps[] = $rep[0];
			}
			if (!in_array("id_calendar",$nomsChamps)) {
				$sql = "ALTER TABLE edt_calendrier ADD id_calendar INT";
				$add_column = mysql_query($sql);
				$sql = "UPDATE edt_calendrier SET id_calendar = '".$id_primary."' ";
				$req = mysql_query($sql);
			}
		}
		
	}

/*******************************************************************
 *
 *
 *******************************************************************/
	public function GenerateCalendar($id_calendrier)
	{
		$result = '';$debut = array();$fin = array();
		$TableSemaines = calendar::getDaysTable();
		$TableDaysInPeriods = calendar::getDaysFromPeriods($id_calendrier);
		calendar::getFrontiersPeriods($debut, $fin, $id_calendrier);
		$i = 1;
		$result.="<div><input id=\"id_calendar\" type=\"hidden\" value=\"".$id_calendrier."\"></div>";
		foreach ($TableSemaines as $semaine) {
			if (in_array($i, $debut)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_first_cell_period\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['lundi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['lundi']."</div>
						</div>
					</div>
				";			
			}
			else if (in_array($i, $TableDaysInPeriods)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_period\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['lundi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['lundi']."</div>
						</div>
					</div>
				";			
			}
			else {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['lundi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['lundi']."</div>
						</div>
					</div>
				";
			}
			$i++;
			if (in_array($i, $debut)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_first_cell_period\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['mardi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['mardi']."</div>
						</div>
					</div>
				";			
			}
			else if (in_array($i, $TableDaysInPeriods)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_period\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['mardi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['mardi']."</div>
						</div>
					</div>
				";			
			}
			else {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['mardi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['mardi']."</div>
						</div>
					</div>
				";
			}
			$i++;
			if (in_array($i, $debut)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_first_cell_period\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['mercredi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['mercredi']."</div>
						</div>
					</div>
				";			
			}
			else if (in_array($i, $TableDaysInPeriods)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_period\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['mercredi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['mercredi']."</div>
						</div>
					</div>
				";			
			}
			else {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['mercredi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['mercredi']."</div>
						</div>
					</div>
				";
			}
			$i++;
			if (in_array($i, $debut)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_first_cell_period\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['jeudi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['jeudi']."</div>
						</div>
					</div>
				";			
			}
			else if (in_array($i, $TableDaysInPeriods)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_period\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['jeudi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['jeudi']."</div>
						</div>
					</div>
				";			
			}
			else {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['jeudi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['jeudi']."</div>
						</div>
					</div>
				";
			}
			$i++;
			if (in_array($i, $debut)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_first_cell_period\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['vendredi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['vendredi']."</div>
						</div>
					</div>
				";			
			}
			else if (in_array($i, $TableDaysInPeriods)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_period\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['vendredi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['vendredi']."</div>
						</div>
					</div>
				";			
			}
			else {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['vendredi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['vendredi']."</div>
						</div>
					</div>
				";
			}
			$i++;
			if (in_array($i, $debut)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_first_cell_period\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['samedi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['samedi']."</div>
						</div>
					</div>
				";			
			}
			else if (in_array($i, $TableDaysInPeriods)) {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell_period\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['samedi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['samedi']."</div>
						</div>
					</div>
				";			
			}
			else {
				$result.= "
					<div id=\"div".$i."\" class=\"calendar_cell\">
						<div id=\"div_".$i."\" class=\"month_".$semaine['samedi-mois']."\" >
							<div style=\"margin:3px;\">".$semaine['samedi']."</div>
						</div>
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
 *			Générer la liste des calendriers existants
 *
 *******************************************************************/
	public static function GenerateCalendarList()
	{
		$result = '';
		$calendriers = Calendrier::getCalendriers();
		$classes = Classe::getClasses();
		if ($calendriers) {
			$i = 0;
			$jointure = new jointure_calendar_classes;
			while (isset($calendriers['id'][$i])) {
				$result.="<div id=\"calendrier_".$calendriers['id'][$i]."\" class=\"cadre_calendrier\">";
				$result.="<div style=\"float:left;width:30px;\"><img style=\"border:0px;padding:0px;margin:0;\" src=\"./lib/template/images/calendar.png\" alt=\"\" \></div>";
				$result.= "<div style=\"padding-top:3px;float:left;width:60%;top:0px;padding-left:10px;\">".$calendriers['nom'][$i]."</div>";
				$result.="<div class=\"bouton_loupe\">
							<a href=\"index.php?action=calendrier&id_calendrier=".$calendriers['id'][$i]."\">
							<img src=\"./lib/template/images/loupe.png\" alt=\"voir les périodes calendaires\" title=\"voir les périodes calendaires\"/>
							</a>
							</div>";
				$result.="<div class=\"bouton_supprimer\">
							<a href=\"index.php?action=calendriermanager&operation=delete&id_calendrier=".$calendriers['id'][$i].add_token_in_url()."\">
							<img src=\"./lib/template/images/erase.png\" alt=\"supprimer\" title=\"supprimer le calendrier\"/>
							</a>
							</div>";
				$result.="<div class=\"bouton_modifier\">
							<a href=\"index.php?action=calendriermanager&operation=modify_name&id_calendrier=".$calendriers['id'][$i].add_token_in_url()."\">
							<img src=\"./lib/template/images/modif.png\" alt=\"modifier le nom\" title=\"modifier le nom du calendrier\"/>
							</a>
							</div>";				
				$result .= "</div>";

				$result .= "<div style=\"background-color:white;
										width:73%;
										margin:0 auto;
										border-top:3px solid #aaaaaa;
										border-right:1px solid #bbbbbb;
										border-left:1px solid #bbbbbb;
										border-bottom:1px solid #bbbbbb;
										position:relative;
										padding:20px;\">";
				$result.="<form action=\"index.php?action=calendriermanager\" method=\"post\">";
				$result.= add_token_field();
				$result.="<input name=\"operation\" type=\"hidden\" value=\"edit_classes\">";
				$result.="<input name=\"id_calendrier\" type=\"hidden\" value=\"".$calendriers['id'][$i]."\">";
				$j = 0;

				$jointure->id_calendar = $calendriers['id'][$i];
				while (isset($classes['id'][$j])) {	
					$jointure->id_classe = $classes['id'][$j];
					if ($jointure->exists()) {
						$result.="	<p style=\"width:70px;font-size:12px;float:left;\">
									<input style=\"border:0px\" type=\"checkbox\" checked name=\"classes_".$calendriers['id'][$i]."[]\" value=\"".$classes['id'][$j]."\">".$classes['nom'][$j]."
									</p>";
					}
					else if ($jointure->bad_calendar()) {
						$result.="	<p style=\"width:70px;font-size:12px;float:left;\">
									<input style=\"border:0px\" type=\"checkbox\" disabled name=\"classes_".$calendriers['id'][$i]."[]\" value=\"".$classes['id'][$j]."\">".$classes['nom'][$j]."
									</p>";					
					
					}
					else {
						$result.="	<p style=\"width:70px;font-size:12px;float:left;\">
									<input style=\"border:0px\" type=\"checkbox\" name=\"classes_".$calendriers['id'][$i]."[]\" value=\"".$classes['id'][$j]."\">".$classes['nom'][$j]."
									</p>";					
					}
					$j++;
				}
				$result .= "<div style=\"clear:both;\">";
				$result .="<input type=\"submit\" value=\"Valider les classes\">";
				$result .="</form>";
				$result.="</div></div>";
				$i++;
			}
		}
		return $result;
	}
/*******************************************************************
 *
 *
 *******************************************************************/
	public static function getDayNumber($num_jour) {
		$result = array();
		
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
			if (($i-1)%6 == 0) {
				$ts+=86400;			
			}
		}

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
 *		echo $calendar::getSinglePeriodName($date_ts, $calendar_id);
 *		result = string
 *		renvoie le nom des périodes contenant le timestamp spécifié (si définies dans les edt)
 *
 *******************************************************************/

 	public static function getSinglePeriod($date_ts, $id_calendar)
	{
		if ($id_calendar) {
			$req_periode = mysql_query("SELECT * FROM edt_calendrier");
		}
		else {
			$req_periode = mysql_query("SELECT * FROM edt_calendrier WHERE id_calendar='".$id_calendar."'");		
		}
		$endprocess = false;
		$result = array();
		while (($rep_periode = mysql_fetch_array($req_periode)) AND (!$endprocess)) {
			if (($rep_periode['debut_calendrier_ts'] <= $date_ts) AND ($rep_periode['fin_calendrier_ts'] >= $date_ts)) { 
				$result['id'] = $rep_periode['id_calendrier'];
				$result['nom'] = $rep_periode['nom_calendrier'];
				$result['debut'] = $rep_periode['debut_calendrier_ts'];				
				$result['fin'] = $rep_periode['fin_calendrier_ts'];
				$result['periode_notes'] = $rep_periode['numero_periode'];
				$result['ouvert'] = $rep_periode['etabferme_calendrier'];
				$result['type'] = $rep_periode['etabvacances_calendrier'];
				$endprocess = true;
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
    while ($semaine <calendar::getNumLastWeek()) {
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
    $ts = $ts_ref + 86400*7;
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

	public static function getTimestampFromDay ($day) {

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
		while ($semaine >=33) {
			$ts-=86400*7;
			$semaine--;
		}
		$i = 1;
		while ($i != $day) {
			$ts+=86400;
			$i++;
			if (($i-1)%6==0) $ts+=86400;
		}
		return $ts;
	}	
/*******************************************************************
 *
 *
 *******************************************************************/

	public static function getPeriodFromDay ($num_day, $calendar) {
		$ts = calendar::getTimestampFromDay($num_day);
		$period = calendar::getSinglePeriod($ts, $calendar);
		return $period;
	}
/*******************************************************************
 *
 *
 *******************************************************************/

	public static function getDaysFromPeriods ($id_calendar) {

		$period = PeriodeCalendaire::getPeriods($id_calendar);
		$tab_period = array();
		
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
				if (($i-1)%6 == 0) {
					$ts+=86400;						
				}
				while ($period['fin'][$j] >= $ts) {
					//echo $ts." ".$period['debut'][$j]." ".$i."<br/>";
					$tab_period[] = $i;
					$ts+=86400;
					$i++;	
					if (($i-1)%6 == 0) {
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
				if (($i-1)%6 == 0) {
					$ts+=86400;						
				}
			}
		}
		return $tab_period;
	}	
/*******************************************************************
 *
 *
 *******************************************************************/

	public static function getFrontiersPeriods (&$debut, &$fin, $id_calendar) {

		$period = PeriodeCalendaire::getPeriods($id_calendar);
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
			if ($ts == $period['debut'][$j]) {
				$debut[] = $i;
				while ($period['fin'][$j] > $ts) {
					$ts+=86400;
					$i++;	
					if (($i-1)%6 == 0) {
						$ts+=86400;						
					}
				}
				$fin[] = $i;
				$j++;
				if (!isset($period['debut'][$j])) {
					$stop = true;
				}
			
			}
			else {
				$ts+=86400;
				$i++;
				if (($i-1)%6 == 0) {
					$ts+=86400;						
				}
			}
		}
	}
/*******************************************************************
 *
 *
 *******************************************************************/

	public static function getFrontiersPeriodID (&$debut, &$fin, $id) {

		$period = PeriodeCalendaire::getPeriods(null);
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
		while ($period['id'][$j] != $id) {
			$j++;
		}
		while (($ts <= $ts_max) && (!$stop)) {

			$ts_sup = $ts + 86400 - 1;

			if ($ts == $period['debut'][$j]) {
				$debut[] = $i;
				while ($period['fin'][$j] > $ts) {
					$ts+=86400;
					$i++;	
					if (($i-1)%6 == 0) {
						$ts+=86400;						
					}
				}
				$fin[] = $i;
				$stop = true;
			}
			else {
				$ts+=86400;
				$i++;
				if (($i-1)%6 == 0) {
					$ts+=86400;						
				}
			}
		}
	}	
}
?>
