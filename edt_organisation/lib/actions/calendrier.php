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
include("./lib/model/calendar.php");
include_once("./lib/model/edt_calendrier_manager.php");
class calendrierAction extends Action {



    public function launch(Request $request, Response $response)
    {
		
		$message = null;
		$PeriodesNotesAutorisees = true;
		$liste_periodes = null;
		$response->addVar('NomPeriode', calendar::getPeriodName(time()));
		$response->addVar('TypeSemaineCourante', calendar::getTypeCurrentWeek());
		$response->addVar('SemaineCourante', calendar::getCurrentWeek());
		if ($request->getParam('id_calendrier')) {
			$calendrier = new Calendrier;
			$calendrier->id = $request->getParam('id_calendrier');
			if ($calendrier->exists()) {
				$jointure = new jointure_calendar_classes;
				$jointure->id_calendar = $request->getParam('id_calendrier');
				if (!$jointure->PeriodsCompatible()) {
					$PeriodesNotesAutorisees = false;
					$message = "Certaines classes n'ont pas les mêmes périodes de notes ! Vous ne pourrez donc pas associer une période de notes à une période calendaire.";
				}
				else {
					$liste_periodes = $jointure->getPeriodesNotesFromCalendar();
				}
				$response->addVar('nom_calendrier', Calendrier::getNom($request->getParam('id_calendrier')));
				$response->addVar('Calendrier', calendar::GenerateCalendar($request->getParam('id_calendrier')));
			}
			else {
				$response->addVar('nom_calendrier', "Erreur - calendrier inexistant");
				$response->addVar('Calendrier', "");			
			}
		}
		else {
			$response->addVar('nom_calendrier', "Erreur - aucun calendrier demandé");
			$response->addVar('Calendrier', "");
		}
		$response->addVar('liste_periodes', $liste_periodes);
		$response->addVar('periodes_notes_autorisees', $PeriodesNotesAutorisees);
		$response->addVar('message', $message);
        $this->render("./lib/template/calendrierSuccess.php");
        $this->printOut();
    }
	

}

?>