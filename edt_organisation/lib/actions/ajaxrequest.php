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


    public function launch(Request $request, Response $response)
    {
		$content = "";
		if ($request->getParam('asker')) {
			if ($request->getParam('asker') == "calendrier") {
				$this->insertPeriod($content, $request);
			}
		}
		$response->addVar('content', $content);
        $this->render("./lib/template/ajaxrequestSuccess.php");
        $this->printOut();
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

					$PeriodeCalendaire->etabferme = 1
					;
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