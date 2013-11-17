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

	public $sem = 0; // permet de récupérer un numéro de semaine autre que l'actuel $sem incrémente ou décrémente par rapport à la semaine actuelle

	public function __construct($id = NULL){

		if (isset($id) AND is_numeric($id)) {
			$this->id = $id;
		}else{
			$this->id = NULL;
		}
	}

	public function infos(){

		/**
		* Si le cours est connu, on peut afficher toutes ses caractéristiques
		* On définit tous les attributs de l'objet
		*/

		$sql = "SELECT edt_cours.*, numero_salle FROM edt_cours, salle_cours
												WHERE id_cours = '".$this->id."'
												AND edt_cours.id_salle = salle_cours.id_salle
												LIMIT 1";
		$query = mysqli_query($GLOBALS["___mysqli_ston"], $sql) OR trigger_error('Impossible de récupérer les infos du cours.', E_USER_ERROR);
		$rep = mysqli_fetch_array($query);

		// on charge les variables de classe
		$this->edt_gr = $rep["id_groupe"];
		$this->edt_aid = $rep["id_aid"];
		$this->edt_jour = $rep["jour_semaine"];
		$this->edt_creneau = $rep["id_definie_periode"];
		$this->edt_debut = $rep["heuredeb_dec"];
		$this->edt_duree = $rep["duree"];
		$this->edt_salle = $rep["numero_salle"];
		$this->edt_semaine = $rep["id_semaine"];
		$this->edt_calend = $rep["id_calendrier"];
		$this->edt_modif = $rep["modif_edt"];
		$this->edt_prof = $rep["login_prof"];
	}

	public function semaine_actu(){

		/**
		* On cherche à déterminer à quel type de semaine se rattache la semaine actuelle
		* Il y a deux possibilités : soit l'établissement utilise les semaines classiques ISO soit il a défini
		* des numéros spéciaux.
 		*/

		//
		$rep = array();

		$sem = date("W") + ($this->sem);

		$query_s = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT type_edt_semaine FROM edt_semaines WHERE id_edt_semaine = '".$sem."' LIMIT 1");
		$rep["type"] = mysql_result($query_s, 0,"type_edt_semaine");

		$query_se = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT type_edt_semaine FROM edt_semaines WHERE num_semaines_etab = '".$sem."' LIMIT 1");
		$compter = mysqli_num_rows($query_se);
		if ($compter >= 1) {
			$rep["etab"] = mysql_result($query_se, 0, "type_edt_semaine");
		}
		$rep["etab"] = '';

		return $rep;
	}

	public function jours_de_la_semaine(){
		/**
		* Affiche les dates de la semaine demandée
		* */
		return 'Il faut que j\'ajoute cette méthode publique edt::jours_de_la_semaine() pour afficher les dates de la semaine vue ;)';
	}

	public function creneau($cren){
		// On cherche le créneau de début du cours
		$sql_c = "SELECT * FROM edt_creneaux WHERE type_creneau != 'pause' AND id_definie_periode = '".$cren."' LIMIT 1";
		$query_c = mysqli_query($GLOBALS["___mysqli_ston"], $sql_c);
		$verif = mysqli_num_rows($query_c);

		if ($verif >= 1) {
			$rep = mysql_result($query_c, 0,"heuredebut_definie_periode");
		}

		return $rep;
	}

	public function joursOuverts(){
		// Liste des jours ouverts
		$sql = "SELECT jour_horaire_etablissement FROM horaires_etablissement WHERE ouvert_horaire_etablissement = 1 LIMIT 7";
		$query = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		$retour = array();
		$i = 0;
		while($rep = mysqli_fetch_array($query)){
			$retour[] = $rep["jour_horaire_etablissement"];
			$i++;
		}
		$retour["nbre"] = $i;

		return $retour;
	}

	public function debut(){
		// On cherche si ce cours commence au début ou au milieu d'un cours $this->edt_debut
		// n veut dire qu'il commence au milieu du cours et y veut dir qu'il commence au début du cours
		if ($this->edt_debut == '0.5') {
			$debut = 'n';
		}elseif($this->edt_debut == '0'){
			$debut = 'y';
		}

		return $debut;
	}

	public function duree(){
		// La durée doit être connu en nombre de demi-créneaux et en nombre de créneaux
		if (isset($this->edt_duree)) {
			$duree["demis"] = $this->edt_duree;
			$test_duree = $this->edt_duree / 2;
			if (is_int($test_duree)) {
				$duree["creneaux"] = $test_duree;
			}else{
				$duree["creneaux"] = $test_duree.'.5';
			}
		}

		return $duree;

	}

	public function calend(){
		// Pour tout savoir sur la période du cours si = 0, pas de période rattachée
		if (isset($this->edt_calend)) {
			$calend = $this->edt_calend;
			if ($calend != 0) {
				// On recherche les infos sur la période existante
				$query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT nom_calendrier, debut_calendrier_ts, fin_calendrier_ts
															FROM edt_calendrier
															WHERE id_calendrier = '".$calend."'");
				$retour = mysqli_fetch_array($query);
			}else{
				$retour = 'n';
			}
		}else{
			return 'erreur';
		}
		return $retour;
	}

	public function prof(){
		// Pour savoir qui est le professeur qui anime le cours $this->edt_prof;
		if (isset($this->edt_prof)) {
			$sql = "SELECT nom, prenom, civilite FROM utilisateurs WHERE login = '".$this->edt_prof."'";
			$query = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			$retour = mysqli_fetch_array($query);
			//$retour = $this->edt_prof;
		}else{
			return 'erreur';
		}
		return $retour;
	}

	public function matiere(){
		$matiere = NULL;
		if ($this->edt_gr != NULL) {
			// C'est donc un 'groupe'
			$sql = "SELECT * FROM groupes WHERE id = '".$this->edt_gr."'";
			$query = mysqli_query($GLOBALS["___mysqli_ston"], $sql);

			$matiere = mysqli_fetch_array($query);

		}elseif($this->edt_aid != NULL){
			// C'est donc une AID
		}else{
			return 'erreur_M';
		}
		return $matiere;
	}

	public function eleves(){
		$eleves = NULL; // en attendant d'implémenter cette méthode
		// pour connaitre la liste des élèves concernés par ce cours
		if ($this->edt_gr != NULL) {
			// C'est donc un 'groupe'
			// $sql = "SELECT .....";
			// A TERMINER ICI
		}
		return $eleves;
	}

	public function couleur_cours(){
		// On récupère la matière
		//$test_grp = $this->type_gr($this->edt_gr);
		$sql = '';
		if ($this->edt_gr != NULL) {
			// on peut alors récupérer la matière et la couleur rattachées à cette matière
			$sql = "SELECT matiere FROM matieres m, j_groupes_matieres jgm
									WHERE jgm.id_matiere = m.matiere
									AND jgm.id_groupe = '".$this->edt_gr."' LIMIT 1";

		}
		if ($sql != '') {
			$query = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			$matiere = mysqli_fetch_array($query);

			// on cherche la couleur rattachée
			$sql2 = "SELECT valeur FROM edt_setting WHERE reglage = 'M_".$matiere["matiere"]."' LIMIT 1";
			$query2 = mysqli_query($GLOBALS["___mysqli_ston"], $sql2);
			$verif = mysqli_num_rows($query2);

			if ($verif == 1) {
				$couleur = mysql_result($query2, 0,"valeur");
			}else{
				$couleur = 'silver';
			}

		}else{
			$couleur = '';
		}

		return $couleur;
	}

}

/**
 * classe de mise en page de l'edt
 *
 */
class edtAfficher{

	public $largeur_creneau = 90;
	public $largeur_jour = 60;
	public $hauteur_entete = 60;
	public $hauteur_creneau = 100;
	public $aff_jour = 'gauche'; // peut être modifiée pour enlever le jour à gauche du div
	public $type_edt = 'prof'; // peut être modifiée pour un élève
	/**
	 * Constructor
	 * @access protected
	 */
	function __construct(){
		// vide pour le moment
	}

	public function liste_creneaux(){
		// Renvoie la liste des créneaux d'une journée
		$sql = "SELECT id_definie_periode, nom_definie_periode, heuredebut_definie_periode FROM edt_creneaux
							WHERE type_creneaux != 'pause'
							AND type_creneaux != 'repas'
							ORDER BY heuredebut_definie_periode";

		$query = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		$rep["nbre"] = mysqli_num_rows($query);

		if ($query AND $rep["nbre"] > 0) {

			for($a = 0 ; $a < $rep["nbre"] ; $a++){
				$rep[$a]["id"] = mysql_result($query, $a, "id_definie_periode");
				$rep[$a]["nom"] = mysql_result($query, $a, "nom_definie_periode");
				$rep[$a]["horaire"] = mb_substr(mysql_result($query, $a, "heuredebut_definie_periode"), 0, 5);
			}

		}
		return $rep;
	}

	public function entete_creneaux($reglage = NULL){

		$rep = '';

		$liste_creneaux = $this->liste_creneaux();

		$rep .= "\n".'
			<div style="width: '.(($this->largeur_creneau * $liste_creneaux["nbre"]) + $this->largeur_jour).'px; height: '.$this->hauteur_entete.'px; border-top: 2px dotted silver;">
				<div class="creneau prem" style="width: '.$this->largeur_jour.'px;">&nbsp;</div>';

		for($a = 0 ; $a < $liste_creneaux["nbre"] ; $a++){

			if ($reglage == 'heures') {
				$cren = $liste_creneaux[$a]["horaire"];
			}elseif($reglage == 'noms'){
				$cren = $liste_creneaux[$a]["nom"];
			}else{
				// Par défaut, le réglage est 'heures'
				$cren = $liste_creneaux[$a]["horaire"];
			}

			if ($a < ($liste_creneaux["nbre"] - 1)) {
				$class = 'creneau';
			}else{
				$class = 'creneau_d';
			}

			if ($a == 0) {
				$margin_left = $this->largeur_jour;
			}else{
				$margin_left = ($this->largeur_creneau * ($a + 1)) - ($this->largeur_creneau - $this->largeur_jour);
			}

			$rep .= "\n".'<div class="'.$class.'" style="margin-left: '.$margin_left.'px; width: '.$this->largeur_creneau.'px;">'.$cren.'</div>';

		}

		$rep .= "\n".'</div>';

		$entete = $rep;

		return $entete;
	}

	public function afficher_cours_jour($jour, $prof){

		$retour = '';

		$liste_cours = $this->edt_jour($jour, $prof);
		// petite verif sur le contenu
		if (!is_array($liste_cours) AND mb_substr($liste_cours, 0, 7) == 'Ce_mode') {
			return $liste_cours;
			exit;
		}

		$liste_creneaux = $this->liste_creneaux();

		if ($this->aff_jour == 'gauche') {

			$largeur = $this->largeur_creneau + 1;
			$aff_jour_gauche = 'oui';

		}elseif($this->aff_jour == 'cache'){

			$largeur = $this->largeur_creneau;
			$aff_jour_gauche = 'non';

		}else{
			echo 'le mode '.$this->aff_jour.' n\'est pas implémenté => ERREUR.';
			echo '<br />Vous avez le choix entre "gauche" et "cache" (par défaut, c\'est gauche).';
			exit();
		}

		$retour .= '<div style="width: '.($largeur) * $liste_creneaux["nbre"].'px; height: '.$this->hauteur_creneau.'px; border-bottom: 2px dotted silver;">';

		if ($aff_jour_gauche == 'oui') {
			$retour .= '<div style="width: '.$this->largeur_jour.'px; height: '.($this->hauteur_creneau - 1).'px; font-size: 12px; text-align: center; border-right: 2px solid grey; position: absolute;"><br />
			'.$jour.'</div>';
		}

		for($a = 0 ; $a < $liste_cours["nbre"] ; $a++){

			$cours = new edt($liste_cours[$a]["id_cours"]);
			$cours_i = $cours->infos();

			$ou = $this->placer_cours($cours);

			$retour .= '
			<div class="affedtcours" style="'.$ou["margin"].' '.$ou["width"].' height: '.($this->hauteur_creneau - 1).'px; background: '.$cours->couleur_cours().';">';

			$retour .= $this->contenu_cours($cours);

			$retour .= '</div>
			';
		}
		$retour .=('</div>'."\n");

		return $retour;
	}

	protected function ordre_creneau(edt $cours){

		$creneaux = $this->liste_creneaux();
		// On cherche le numéro du créneau en question (premier, deuxièmre, troisième, ...)
		$test = 'n';
		for($o = 0 ; $o < $creneaux['nbre'] ; $o++){
			if ($creneaux[$o]["id"] == $cours->edt_creneau) {
				$test = $o + 1;
			}
		}
		return $test;
	}

	protected function placer_cours(edt $cours){

		// $cours doit être une instance de la classe edt... Il faudra peut-être vérifier cela
		$test = $this->ordre_creneau($cours);

		if ($test == 1) {
			$rep["margin"] = 'margin-left: '.$this->largeur_jour.'px;';
		}else{

			if ($cours->edt_debut == '0') {
				$rep["margin"] = 'margin-left: '.(((($test - 1) * $this->largeur_creneau) + $this->largeur_creneau) - ($this->largeur_creneau - $this->largeur_jour)).'px;';
			}elseif($cours->edt_debut == '0.5'){
				// C'est le même calcul que sur le précédent mais on y ajoute un demi-créneau
				$rep["margin"] = 'margin-left: '.(((($test - 1) * $this->largeur_creneau) + $this->largeur_creneau) - ($this->largeur_creneau - $this->largeur_jour) + ($this->largeur_creneau / 2)).'px;';
			}else{
				$rep["margin"] = 'Il manque une info.';
			}

		}

		$rep["width"] = 'width: '.(($cours->edt_duree * ($this->largeur_creneau / 2)) - 1).'px;';

		return $rep;
	}

	protected function edt_jour($jour, $user_login){
		/**
		* méthode qui renvoie l'edt d'un prof
		* sur un jour donné Il faudra ajouter les aid
		*
		*/

		$rep = array();
		$sem = edt::semaine_actu();

		if ($this->type_edt == 'prof') {

			$sql = "SELECT id_cours FROM edt_cours WHERE
								login_prof = '".$user_login."'
								AND jour_semaine = '".$jour."'
								AND (id_semaine = '".$sem["type"]."' OR id_semaine = '0')
							ORDER BY id_definie_periode";

		}elseif($this->type_edt == 'eleve'){

			$sql = "SELECT id_cours FROM edt_cours, j_eleves_groupes WHERE
								edt_cours.jour_semaine = '".$jour."'
								AND edt_cours.id_groupe = j_eleves_groupes.id_groupe
								AND login = '".$user_login."'
								AND (id_semaine = '".$sem["type"]."' OR id_semaine = '0')
							ORDER BY edt_cours.id_semaine";

		}elseif($this->type_edt == 'classe'){

			$sql = "SELECT * FROM edt_cours, j_groupes_classes, classes WHERE
								edt_cours.jour_semaine = '".$jour."'
								AND edt_cours.id_groupe = j_groupes_classes.id_groupe
								AND j_groupes_classes.id_classe = classes.id
								AND classes.classe = '".$user_login."'
								AND (id_semaine = '".$sem["type"]."' OR id_semaine = '0')
							ORDER BY edt_cours.id_groupe";

		}else{
			return 'Ce_mode '.$this->type_edt.' n\'est pas encore disponible';
		}

		$query = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		$rep["nbre"] = mysqli_num_rows($query);

		for($a = 0 ; $a < $rep["nbre"] ; $a++){
			$reponse = mysqli_fetch_array($query);

			$rep[$a]["id_cours"] = $reponse["id_cours"];

		}

		return $rep;
	}

	protected function contenu_cours(edt $cours){
		/**
		* méthode qui gère l'affichage d'un cours dans le div
		*
		*/
		// $cours doit être une instance de la classe edt... Il faudra peut-être vérifier cela
		// Le professeur
		$contenu = '';

		$prof = $cours->prof();
		$matiere = $cours->matiere();

		$contenu .= '<p style="text-align: center;">'.
			$prof["civilite"].$prof["nom"].' '.mb_substr($prof["prenom"], 0, 1).'.<br />'.
			$matiere["name"].'<br /><i>salle&nbsp;'.$cours->edt_salle.'</i>
			</p>';

		return $contenu;
	}

	public function aujourdhui(){
		/**
		* méthode qui donne le jour d'aujourd'hui en toute lettre et en Français
		*
		*/
		$jour_num = date("N") - 1;

		$jours_semaine = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');

		return $jours_semaine[$jour_num];
	}

}