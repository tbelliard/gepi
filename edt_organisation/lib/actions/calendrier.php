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
class calendrierAction extends Action {



    public function launch(Request $request, Response $response)
    {
		$response->addVar('NomPeriode', calendar::getPeriodName(time()));
		$response->addVar('TypeSemaineCourante', calendar::getTypeCurrentWeek());
		$response->addVar('SemaineCourante', calendar::getCurrentWeek());
		$response->addVar('Calendrier', $this->GenerateCalendar());
        $this->render("./lib/template/calendrierSuccess.php");
        $this->printOut();
    }
	
	public function GenerateCalendar()
	{
		$result = '';
		$TableSemaines = calendar::getDaysTable();
		$i = 1;
		foreach ($TableSemaines as $semaine) {
			$result.= "
				<div id=\"div".$i."\" class=\"calendar_cell_".$semaine['lundi-mois']."\">
					<div style=\"margin:3px;\">".$semaine['lundi']."</div>
				</div>
			";
			$i++;
			$result.= "
				<div id=\"div".$i."\" class=\"calendar_cell_".$semaine['mardi-mois']."\">
					<div style=\"margin:3px;\">".$semaine['mardi']."</div>
				</div>
			";
			$i++;
			$result.= "
				<div id=\"div".$i."\" class=\"calendar_cell_".$semaine['mercredi-mois']."\">
					<div style=\"margin:3px;\">".$semaine['mercredi']."</div>
				</div>
			";
			$i++;
			$result.= "
				<div id=\"div".$i."\" class=\"calendar_cell_".$semaine['jeudi-mois']."\">
					<div style=\"margin:3px;\">".$semaine['jeudi']."</div>
				</div>
			";
			$i++;
			$result.= "
				<div id=\"div".$i."\" class=\"calendar_cell_".$semaine['vendredi-mois']."\">
					<div style=\"margin:3px;\">".$semaine['vendredi']."</div>
				</div>
			";
			$i++;
			$result.= "
				<div id=\"div".$i."\" class=\"calendar_cell_".$semaine['samedi-mois']."\">
					<div style=\"margin:3px;\">".$semaine['samedi']."</div>
				</div>
			";
			$result.="	<div style=\"clear:both;\"></div>";
			$i++;
			
		}
		return $result;
	}
}

?>