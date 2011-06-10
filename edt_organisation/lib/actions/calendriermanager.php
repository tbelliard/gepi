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
class calendriermanagerAction extends Action {

    public function launch(Request $request, Response $response)
    {

		$message = null;
		if ($request->getParam('operation')) {
			if ($request->getParam('operation') == "delete") {
				if ($request->getParam('id_calendrier')) {
					$calendrier = new Calendrier;
					$calendrier->id = $request->getParam('id_calendrier');
					if (!$calendrier->delete()) {
						$message = "Impossible de supprimer le calendrier";
					}
				}
			}
			else if ($request->getParam('operation') == "new") {
				if ($request->getParam('nom_calendrier')) {
					$calendrier = new Calendrier;
					$calendrier->nom = $request->getParam('nom_calendrier');
					if (!$calendrier->save()) {
						$message = "Impossible de créer le calendrier";
					}
				}
			}
		}
		if ($_SESSION['statut'] == "administrateur") {
			calendar::updateTables();
		}
		$response->addVar('message', $message);
		$response->addVar('NomPeriode', calendar::getPeriodName(time()));
		$response->addVar('TypeSemaineCourante', calendar::getTypeCurrentWeek());
		$response->addVar('SemaineCourante', calendar::getCurrentWeek());
		$response->addVar('calendrier', calendar::GenerateCalendarList());
        $this->render("./lib/template/calendriermanagerSuccess.php");
        $this->printOut();
    }
	

}

?>