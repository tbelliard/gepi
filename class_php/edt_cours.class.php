<?php

/**
 *
 * @version $Id$
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
 *
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

if (!$_SESSION["login"]) {
	DIE();
}

// ============================= classe php de construction des emplois du temps =================================== //

/**
 * la classe edt implémente tous les paramètres indispensables sur les informations utiles
 * à l'organisation des emplois du temps.
 */

class edt{

	public $id; // permet de préciser l'id du cours en question
	public $edt_gr; // les élèves {enseignements AID edt_gr}
	public $type_grpe; // le type de groupe d'élèves {ENS AID EDT}
	public $id_grpe; // l'identifiant de l'id_groupe après traitement par type_gr();
	public $edt_jour; // voir table horaires_etablissement
	public $edt_creneau; // voir table
	public $edt_debut; // 0 = début du créneau 0.5 = milieu d'un créneau
	public $edt_duree; //  en nbre de demis-créneaux
	public $edt_salle; // voir table salle_cours
	public $edt_semaine; // type de semaine comme défini dans la table edt_semaines
	public $edt_calend; // cours rattaché à une période précise définie dans le calendrier
	public $edt_modif; // pour savoir s'il s'agit d'un cours temporaire sur une semaine précise =0 si ce n'est pas le cas.
	public $edt_prof; // qui est le professeur qui anime le cours (login)
	public $type; // permet de définir s'il s'agit d'un type {prof, eleve, classe, salle)

	public function __construct($id){

		if (isset($id) AND is_numeric($id)) {
			$this->id = $id;
		}
	}

	public function infos($id){

		/**
		* Si le cours est connu, on peut afficher toutes ses caractéristiques
		* On définit tous les attributs de l'objet
		*/

		if (!isset($this->id)) {
			$this->id = $id;
		}

		$sql = "SELECT * FROM edt_cours WHERE id_cours = '".$this->id."' LIMIT 1";
		$query = mysql_query($sql) OR trigger_error('Impossible de récupérer les infos du cours.', E_USER_ERROR);
		$rep = mysql_fetch_array($query);

		// on charge les variables de classe
		$this->edt_gr = $rep["id_groupe"];
		$this->edt_jour = $rep["jour_semaine"];
		$this->edt_creneau = $rep["id_definie_periode"];
		$this->edt_debut = $rep["heuredeb_dec"];
		$this->edt_duree = $rep["duree"];
		$this->edt_salle = $rep["id_salle"];
		$this->edt_semaine = $rep["id_semaine"];
		$this->edt_calend = $rep["id_calendrier"];
		$this->edt_modif = $rep["modif_edt"];
		$this->edt_prof = $rep["login_prof"];
	}

	public function semaine_actu(){

		/**
		* On cherche à déterminer à quel type de semaine se rattache la semaine actuelle
		* Il y a deuxpossibilités : soit l'établissement utilise les semaines classiques ISO soit il a défini
		* des numéros spéciaux.
 		*/

		//
		$rep = array();

		$sem = date("W");

		$query_s = mysql_query("SELECT type_edt_semaine FROM edt_semaines WHERE id_edt_semaine = '".$sem."' LIMIT 1");
		$rep["type"] = mysql_result($query_s, "type_edt_semaine");

		$query_se = mysql_query("SELECT type_edt_semaine FROM edt_semaines WHERE num_semaines_etab = '".$sem."' LIMIT 1");
		$compter = mysql_num_rows($query_se);
		if ($compter >= 1) {
			$rep["etab"] = mysql_result($query_se, "type_edt_semaine");
		}
		$rep["etab"] = '';

		return $rep;
	}

	public function creneau($cren){
		// On cherche le créneau de début du cours
		$sql_c = "SELECT * FROM absences_creneaux WHERE type_creneau != 'pause' AND id_definie_periode = '".$cren."' LIMIT 1";
		$query_c = mysql_query($sql_c);
	}

	public function debut(){
		// On cherche si ce cours commence au début ou au milieu d'un cours
	}

	public function duree(){
		// La durée doit être connu en nombre de demi-créneaux et en nombre de créneaux

	}

	public function calend(){
		// Pour tout savoir sur la période du cours si = 0, pas de période rattachée
	}

	public function prof(){
		// Pour savoir qui est le professeur qui anime le cours
	}

	public function eleves(){
		// pour connaitre la liste des élèves concernés par ce cours
	}

	protected function type_gr($gr){
		/**
		* méthode qui permet de définir le type de id_groupe
		* soit de type ENS (un entier seul)
		* soit de type AID (AID|...) -> voir les tables liées aux aid_...
		* soit de type EDT (EDT|...) -> voir les tables liés aux edt_gr_...
		*/
		$test = explode("|", $gr);
		if ($test[0] == 'AID') {
			$rep = 'AID';
			$grpe = $test[1];
		}elseif($test[0] == 'EDT'){
			$rep = 'EDT';
			$grpe = $test[1];
		}else{
			$rep = 'ENS';
			$grpe = $gr;
		}

		$this->type_grpe = $rep;
		$this->id_grpe = $grpe;

		return $rep;
	}

	protected function couleur_cours(){
		// On récupère la matière
	}

}

/**
 *
 *
 */
class edtAfficher extends edt{
	/**
	 * Constructor
	 * @access protected
	 */
	function __construct(){
		// vide pour le moment
	}

	public function afficher_cours($jour, $prof){

		$cours = $this->edt_jour($jour, $prof);

		echo '<div style="width: 80px; height: 99px; text-align: center; border-right: 2px solid grey; position: absolute;"><br />
			'.$jour.'</div>';

		for($a = 0 ; $a < $cours["nbre"] ; $a++){

			$ou = $this->placer_cours($cours[$a]["id_definie_periode"], $cours[$a]["heuredeb_dec"], $cours[$a]["duree"]);

			echo '
			<div style="'.$ou["margin"].' '.$ou["width"].' height: 99px; background: silver; position: absolute; border: 1px solid black;">';
			//echo $cours[$a]["id_cours"];
			$this->contenu_cours($cours[$a]);

			echo '</div>
			';
		}

	}

	protected function placer_cours($id_creneau, $debut, $duree){

		if ($debut == '0') {
			$rep["margin"] = 'margin-left: '.((($id_creneau - 1) * 2 * 40) + 81).'px;';
		}elseif($debut == '0.5'){
			$rep["margin"] = 'margin-left: '.((((($id_creneau - 1) * 2) - 1) * 40) + 81).'px;';
		}else{
			$rep["margin"] = 'Il manque une info.';
		}

		$rep["width"] = 'width: '.($duree * 40).'px;';

		return $rep;
	}

	protected function edt_jour($jour, $prof){
		/**
		* méthode qui renvoie l'edt d'un prof
		* sur un jour donné
		*
		*/

		$rep = array();
		$sem = $this->semaine_actu();

		$sql = "SELECT * FROM edt_cours
						WHERE login_prof = '".$prof."'
						AND jour_semaine = '".$jour."'
						AND (id_semaine = '".$sem["type"]."' OR id_semaine = '0')
						ORDER BY id_definie_periode";
		$query = mysql_query($sql);

		$rep["nbre"] = mysql_num_rows($query);

		//while($reponse = mysql_fetch_array($query)){
		for($a = 0 ; $a < $rep["nbre"] ; $a++){
			$reponse = mysql_fetch_array($query);

			$rep[$a]["id_cours"] = $reponse["id_cours"];
			$rep[$a]["id_groupe"] = $reponse["id_groupe"];
			$rep[$a]["type_groupe"] = $this->type_gr($reponse["id_groupe"]);
			$rep[$a]["id_salle"] = $reponse["id_salle"];
			$rep[$a]["jour_semaine"] = $reponse["jour_semaine"];
			$rep[$a]["id_definie_periode"] = $reponse["id_definie_periode"];
			$rep[$a]["duree"] = $reponse["duree"];
			$rep[$a]["heuredeb_dec"] = $reponse["heuredeb_dec"];
			$rep[$a]["id_semaine"] = $reponse["id_semaine"];
			$rep[$a]["id_calendrier"] = $reponse["id_calendrier"];
			$rep[$a]["modif_edt"] = $reponse["modif_edt"];
			$rep[$a]["login_prof"] = $reponse["login_prof"];

		}

		return $rep;
	}

	protected function contenu_cours($cours){
		/**
		* méthode qui gère l'affichage d'un cours dans le div
		*
		*/
		//print_r($cours);
		echo $cours["id_cours"].'<br />'.$cours["type_groupe"];
	}


}