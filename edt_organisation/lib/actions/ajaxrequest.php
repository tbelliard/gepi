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
include("./lib/actions/action.class.php");
include("./lib/model/edt_calendrier.php");
include("./lib/model/calendar.php");
class ajaxrequestAction extends Action {

/*******************************************************************
 *
 *							Dispatcher
 *
 *******************************************************************/
    public function launch(Request $request, Response $response)
    {
		$content = "";
		if ($request->getParam('asker')) {
			if ($_SESSION['statut'] == "administrateur") {
				check_token();
				if ($request->getParam('asker') == "calendrier") {
					$this->insertPeriod($content, $request);
				}
				else if ($request->getParam('asker') == "edit_period") {
					$this->editPeriod($content, $request);
				}
				else if ($request->getParam('asker') == "delete_period") {
					$this->deletePeriod($content, $request);
				}
				else if ($request->getParam('asker') == "validate_period") {
					$this->validatePeriod($content, $request);
				}
			}
		}
		$response->addVar('content', $content);
        $this->render("./lib/template/ajaxrequestSuccess.php");
        $this->printOut();
    }
/*******************************************************************
 *
 *					Validation de la modification d'une période
 *
 *******************************************************************/		
	
	public function validatePeriod(&$content, Request $request) {	
		if ($request->getParam('id_period')) {

			if ($request->getParam('start_period') AND $request->getParam('end_period')) {
				$detail_jourdeb = explode("/", $request->getParam('start_period'));
				$detail_jourfin = explode("/", $request->getParam('end_period'));
				$formatdatevalid = false;
				if (isset($detail_jourdeb[0]) AND isset($detail_jourdeb[1]) AND isset($detail_jourdeb[2])) {
					if (isset($detail_jourfin[0]) AND isset($detail_jourfin[1]) AND isset($detail_jourfin[2])) {
						if (is_numeric($detail_jourfin[0]) AND is_numeric($detail_jourfin[1]) AND is_numeric($detail_jourfin[2])) {
							if (is_numeric($detail_jourdeb[0]) AND is_numeric($detail_jourdeb[1]) AND is_numeric($detail_jourdeb[2])) {
								$formatdatevalid = true;
							}
						}
					}
				}
				if ($formatdatevalid) {
					$jourdebut = $detail_jourdeb[2]."-".$detail_jourdeb[1]."-".$detail_jourdeb[0];
					$jourfin = $detail_jourfin[2]."-".$detail_jourfin[1]."-".$detail_jourfin[0];
					$heuredebut = "00:00:00";
					$heurefin = "23:59:00";
					$expdeb = explode(":", $heuredebut);
					$expfin = explode(":", $heurefin);
					// On insére ces dates en timestamp Unix GMT
					$debut_ts = gmmktime($expdeb[0], $expdeb[1], 0, $detail_jourdeb[1], $detail_jourdeb[0], $detail_jourdeb[2]);
					$fin_ts = gmmktime($expfin[0], $expfin[1], 0, $detail_jourfin[1], $detail_jourfin[0], $detail_jourfin[2]);
					
					$PeriodeCalendaire = new PeriodeCalendaire();
					$PeriodeCalendaire->id = $request->getParam('id_period');
					$PeriodeCalendaire->id_calendar = $PeriodeCalendaire->getCalendarID();
					$PeriodeCalendaire->nom = $request->getParam('name_period');
					$PeriodeCalendaire->debut_ts = $debut_ts;
					$PeriodeCalendaire->jourdebut = $jourdebut;		
					$PeriodeCalendaire->heuredebut = $heuredebut;	
					$PeriodeCalendaire->fin_ts = $fin_ts;
					$PeriodeCalendaire->jourfin = $jourfin;		
					$PeriodeCalendaire->heurefin = $heurefin;
					$PeriodeCalendaire->periode_note = $request->getParam('periode_notes');
					$PeriodeCalendaire->etabvacances = $request->getParam('type');
					$PeriodeCalendaire->etabferme = $request->getParam('ouvert');
					if ($PeriodeCalendaire->insertable()) {
						$success = $PeriodeCalendaire->update();

						if ($success) {
							$debut = array();$fin = array();
							calendar::getFrontiersPeriodID($debut, $fin, $PeriodeCalendaire->id);
							$debut_periode = $debut[0];
							$fin_periode = $fin[0];
							$num_initial = $request->getParam('num_jour_initial');
							$num_final = $request->getParam('num_jour_final');
							header('Content-type: text/html;charset=iso-8859-1;');
							$content = '[{
									"code": "success",
									"message": "mise à jour de la période effectuée",
									"new_start": "'.$debut_periode.'",
									"new_end": "'.$fin_periode.'",
									"old_start": "'.$num_initial.'",
									"old_end": "'.$num_final.'"
									}]';
						}
						else {
							header('Content-type: text/html;charset=iso-8859-1;');
							$code = "error";
							$message = "impossible de mettre à jour la période";
							$content = '[{
									"code": "'.$code.'",
									"message": "'.$message.'"
									}]';
						}
					}
					else {
						header('Content-type: text/html;charset=iso-8859-1;');
						$code = "error";
						$message = "Le chevauchement des périodes n'est pas possible";
						$content = '[{
								"code": "'.$code.'",
								"message": "'.$message.'"
								}]';					
					
					
					
					}

				}
				else {
					header('Content-type: text/html;charset=iso-8859-1;');
					$content = '[{
							"code": "error",
							"message": "Le format des dates est non valide" 
							}]';				
				}
			}
		}
		else {
			header('Content-type: text/html;charset=iso-8859-1;');
			$content = '[{
					"code": "error",
					"message": "Aucune période transmise" 
					}]';		
		
		}	
	
	
	
	}
/*******************************************************************
 *
 *					Suppression d'une période
 *
 *******************************************************************/		
	
	public function deletePeriod(&$content, Request $request) {
		if ($request->getParam('periodid')) {
			$PeriodeCalendaire = new PeriodeCalendaire();
			$PeriodeCalendaire->id = $request->getParam('periodid');
			$success = $PeriodeCalendaire->delete();
			header('Content-type: text/html;charset=iso-8859-1;');
			if ($success) {
				
				$code = "success";
				$message = "suppression de la période effectuée";
			}
			else {
				$code = "error";
				$message = "impossible de supprimer la période";
			}
			$content = '[{
					"code": "'.$code.'",
					"message": "'.$message.'" 
					}]';

		}
		else {
			$content = '[{
					"code": "error",
					"message": "erreur" 
					}]';		
		
		}
	}
/*******************************************************************
 *
 *			Edition d'une période - renvoie au format JSON
 *
 *******************************************************************/		
	
	public function editPeriod(&$content, Request $request) {
		if ($request->getParam('id_calendar')) {
			$id_calendar = $request->getParam('id_calendar');
			if ($request->getParam('day')) {
				$debut = array();
				$fin = array();
				$day  = $request->getParam('day');
				$period = calendar::getPeriodFromDay($day, $id_calendar);
				calendar::getFrontiersPeriods($debut, $fin, $id_calendar);
				$success = false;$debut_periode = 0;$fin_periode = 0;$stop = false;$i=0;
				while ((!$success) && (!$stop)) {
					if (($day >= $debut[$i]) && ($day <=$fin[$i])) {
						$success = true;
						$debut_periode = $debut[$i];
						$fin_periode = $fin[$i];
					}
					$i++;
					if (!isset($debut[$i])) {
						$stop = true;
					}
				}
				header('Content-type: text/html;charset=iso-8859-1;');
				//header('Content-type: application/x-json');
				$content = '[{
						"id": "'.$period['id'].'",
						"name": "'.$period['nom'].'", 
						"start_date": "'.strftime("%d/%m/%Y", $period['debut']).'",
						"end_date": "'.strftime("%d/%m/%Y", $period['fin']).'",
						"periode_notes": "'.$period['periode_notes'].'",
						"ouvert": "'.$period['ouvert'].'",
						"type": "'.$period['type'].'",
						"num_jour_initial" : "'.$debut_periode.'",
						"num_jour_final" : "'.$fin_periode.'"
						}]';

			}
		}
	}
/*******************************************************************
 *
 *			Insertion d'une période
 *
 *******************************************************************/		
	
	public function insertPeriod(&$content, Request $request) {
	
		if ($request->getParam('periodname')) {
			$content .= "nom de la periode :".$request->getParam('periodname');
			if ($request->getParam('firstday')) {
				if ($request->getParam('lastday')) {
					if ($request->getParam('lastday') > $request->getParam('firstday')) {
						$lastday = $request->getParam('lastday');
						$firstday = $request->getParam('firstday');
					}
					else {
						$lastday = $request->getParam('firstday');
						$firstday = $request->getParam('lastday');							
					}
					$PeriodeCalendaire = new PeriodeCalendaire();
					$PeriodeCalendaire->nom = $request->getParam('periodname');
					
					$result = calendar::getDayNumber($firstday);
					$PeriodeCalendaire->debut_ts = $result['timestamp'];
					$PeriodeCalendaire->jourdebut = $result['day'];		
					$PeriodeCalendaire->heuredebut = "00:00:00";	
					
					$result = calendar::getDayNumber($lastday);
					$PeriodeCalendaire->fin_ts = $result['timestamp'];
					$PeriodeCalendaire->jourfin = $result['day'];		
					$PeriodeCalendaire->heurefin = "23:59:00";

					$PeriodeCalendaire->periode_note = $request->getParam('periode_notes');
					$PeriodeCalendaire->etabvacances = $request->getParam('type');
					$PeriodeCalendaire->etabferme = $request->getParam('ouvert');

					$PeriodeCalendaire->id_calendar = $request->getParam('id_calendar');
					if ($PeriodeCalendaire->insertable()) {
						if (!$PeriodeCalendaire->save()) {
							$content = "error Impossible d'enregistrer la période";
						}
						else {
							$content = "success";
						}
					}
					else {
						$content = "error Les p&eacute;riodes ne peuvent pas se chevaucher";					
					}
				}
			}
		}
		else {
			$content = "error Veuillez entrer un nom de période";
		}	
	}
}

?>