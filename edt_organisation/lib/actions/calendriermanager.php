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
class calendriermanagerAction extends Action {

    public function launch(Request $request, Response $response)
    {

		$message = null;
		$new_name = null;
		$delete_confirmation = null;
		if ($_SESSION['statut'] == "administrateur") {
			if ($request->getParam('operation')) {
				check_token(false);
				if ($request->getParam('operation') == "delete") {
					if ($request->getParam('confirm_delete')) {
						if ($request->getParam('id_calendrier')) {
							$calendrier = new Calendrier;
							$calendrier->id = $request->getParam('id_calendrier');
							if (!$calendrier->delete()) {
								$message = "Impossible de supprimer le calendrier";
							}
						}
					}
					else {
						if ($request->getParam('id_calendrier')) {
							$delete_confirmation = "<form action=\"index.php?action=calendriermanager\" method=\"post\">".
											add_token_field(false)."
											<input name=\"operation\" type=\"hidden\" value=\"delete\">
											<input name=\"id_calendrier\" type=\"hidden\" value=\"".$request->getParam('id_calendrier')."\">
											<p>La suppression d'un calendrier entraîne la suppression de toutes les périodes calendaires qui en dépendent !</p>
											<input name=\"confirm_delete\" type=\"submit\" style=\"width:200px;\" value=\"Confirmer la suppression\">
										</form>";
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
				else if ($request->getParam('operation') == "modify_name") {
					if ($request->getParam('new_name')) {
						$calendrier = new Calendrier;
						$calendrier->nom = $request->getParam('new_name');
						$calendrier->id = $request->getParam('id_calendrier');
						if (!$calendrier->update()) {
							$message = "Impossible de modifier le nom du calendrier";
						}
					}
					else {
						if ($request->getParam('id_calendrier')) {
							$new_name = "<form action=\"index.php?action=calendriermanager\" method=\"post\">".
											add_token_field(false)."
											<input name=\"operation\" type=\"hidden\" value=\"modify_name\">
											<input name=\"id_calendrier\" type=\"hidden\" value=\"".$request->getParam('id_calendrier')."\">
											<input name=\"new_name\" type=\"text\" style=\"width:200px;\" value=\"".Calendrier::getNom($request->getParam('id_calendrier'))."\">
											<input name=\"bouton_valider_new_name\" type=\"submit\" style=\"width:200px;\" value=\"Modifier le nom du calendrier\">
										</form>";
						}
					}
				}
				else if ($request->getParam('operation') == "edit_classes") {
					if ($request->getParam('id_calendrier')) {
						$id_calendrier = $request->getParam('id_calendrier');
						$jointure = new jointure_calendar_classes;
						$periodes = new PeriodeCalendaire;
						$classe = new Classe;
						$jointure->id_calendar = $id_calendrier;
						$jointure->delete_classes();
						if ($request->getParam('classes_'.$id_calendrier)) {
							$liste_classes = null;
							foreach ($request->getParam('classes_'.$id_calendrier) as $id_classe) {
								$classe->id = $id_classe;
								$liste_classes.= $classe->getShortName().";";
								$jointure->id_classe = $id_classe;
								if (!$jointure->save_classe()) {
									$message .= "Une classe est déjà affectée dans un autre calendrier<br/>";
								}
							}
							// ================ Compatibilité pour les autres modules GEPi
							$periodes->id_calendar = $id_calendrier;
							$periodes->classes_concernees = $liste_classes;
							$periodes->update_classes();

						}
					}
				}				
			}
			calendar::updateTables();
		}
		$response->addVar('delete_confirmation', $delete_confirmation);
		$response->addVar('new_name', $new_name);
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