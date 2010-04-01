<?php


/**
 * Skeleton subclass for representing a row from the 'a_types' table.
 *
 * Liste des types d'absences possibles dans l'etablissement
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceEleveType extends BaseAbsenceEleveType {

	/**
	 *
	 * Renvoi la description du type de saisie, permet de decrire les code suivant :
	 * DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE
	 *
	 * @return     string description lisible du type de saisie
	 *
	 */
	public function getTypeSaisieDescription() {
	    $type_code = $this->getTypeSaisie();
	    if ($type_code == "") {
		return "";
	    } elseif ($type_code == "DEBUT_ABS") {
		return "Sasie imperative de l'heure du debut de l'absence";
	    } elseif ($type_code == "FIN_ABS") {
		return "Sasie imperative de l'heure de fin debut de l'absence";
	    } elseif ($type_code == "DEBUT_ET_FIN_ABS") {
		return "Sasie imperative de l'heure du debut et de fin de l'absence";
	    } elseif ($type_code == "NON_PRECISE") {
		return "Type de saisie non précisé";
	    } elseif ($type_code == "COMMENTAIRE_EXIGE") {
		return "Sasie imperative d'un commentaire explicatif";
	    }
	}

} // AbsenceEleveType
