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
include_once("./lib/model/calendar.php");
include_once("./lib/model/edt_j_calendar_classes.php");
class edtAction extends Action {

    public function launch(Request $request, Response $response)
    {
		$type_edt = null;
		$id = null;
		if ($request->getParam('login_prof')) {
			$id = $request->getParam('login_prof');
			$type_edt = "prof";
		}
		else if ($request->getParam('id_classe')) {
			$id	= $request->getParam('id_classe');
			$type_edt = "classe";
		}
		else if ($request->getParam('id_salle')) {
			$id = $request->getParam('id_salle');
			$type_edt = "salle";
		}

		if ($request->getParam('message') != "") {
			$_SESSION["message"] = "";
		}
		// =================== Gérer la bascule entre emplois du temps périodes et emplois du temps semaines.

		if ($request->getParam('bascule_edt')) {
			$_SESSION['bascule_edt'] = $bascule_edt;
		}
		if (!isset($_SESSION['bascule_edt'])) {
			$_SESSION['bascule_edt'] = 'periode';
		}
		if ($_SESSION['bascule_edt'] == 'periode') {
			if (PeriodesExistent()) {
				if ($period_id != NULL) {
					$_SESSION['period_id'] = $period_id;
				}
				if (!isset($_SESSION['period_id'])) {
					$_SESSION['period_id'] = ReturnIdPeriod(date("U"));
				}
				if (!PeriodExistsInDB($_SESSION['period_id'])) {
					$_SESSION['period_id'] = ReturnFirstIdPeriod();    
				}
				$DisplayPeriodBar = true;
				$DisplayWeekBar = false;
			}
			else {
				$DisplayWeekBar = false;
				$DisplayPeriodBar = false;
				$_SESSION['period_id'] = 0;
			}
		}
		else {
			$DisplayPeriodBar = false;
			$DisplayWeekBar = true;
			if ($week_selected != NULL) {
				$_SESSION['week_selected'] = $week_selected;
			}
			if (!isset($_SESSION['week_selected'])) {
				$_SESSION['week_selected'] = date("W");
			}
		}
		// =================== Forcer l'affichage d'un edt si l'utilisateur est un prof 
		if (!$id) {
			if (($_SESSION['statut'] == "professeur") AND ($type_edt == "prof")) {
				$id = $_SESSION['login'];
				$_GET["login_edt"] = $id;
				$_GET["type_edt_2"] = "prof";
				$type_edt_2 = "prof";
				$visioedt = "prof1";
			}
		}

		// =================== Construire les emplois du temps

		if($id){

			if ($type_edt == "prof")
			{
				$tab_data = ConstruireEDTProf($id, $_SESSION['period_id']);
				$entetes = ConstruireEnteteEDT();
				$creneaux = ConstruireCreneauxEDT();
				FixColumnPositions($tab_data, $entetes);		// en cours de devel
				$DisplayEDT = true;
			}
			else if ($type_edt == "classe")
			{
				$tab_data = ConstruireEDTClasse($id, $_SESSION['period_id']);
				$entetes = ConstruireEnteteEDT();
				$creneaux = ConstruireCreneauxEDT();
				$DisplayEDT = true;

			}
			else if ($type_edt == "salle")
			{
				$tab_data = ConstruireEDTSalle($id , $_SESSION['period_id']);
				$entetes = ConstruireEnteteEDT();
				$creneaux = ConstruireCreneauxEDT();
				//FixColumnPositions($tab_data, $entetes);		// en cours de devel
				$DisplayEDT = true;

			}
			else if ($type_edt == "eleve")
			{
				$tab_data = ConstruireEDTEleve($id , $_SESSION['period_id']);
				$entetes = ConstruireEnteteEDT();
				$creneaux = ConstruireCreneauxEDT();
				$DisplayEDT = true;

			}
			else {
				$DisplayEDT = false;
			}

		}
		else {
			$DisplayEDT = false;
		}
	
	
	
		$response->addVar('message', $message);
		$response->addVar('NomPeriode', calendar::getPeriodName(time()));
		$response->addVar('TypeSemaineCourante', calendar::getTypeCurrentWeek());
		$response->addVar('SemaineCourante', calendar::getCurrentWeek());
        $this->render("./lib/template/edtSuccess.php");
        $this->printOut();
    }
	

}

?>