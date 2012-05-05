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
	 * Les types de creneaux possibles
	 */
	const MODE_INTERFACE_DEBUT_ABS = 'DEBUT_ABS';
	const MODE_INTERFACE_FIN_ABS = 'FIN_ABS';
	const MODE_INTERFACE_DEBUT_ET_FIN_ABS = 'DEBUT_ET_FIN_ABS';
	const MODE_INTERFACE_NON_PRECISE = 'NON_PRECISE';
	const MODE_INTERFACE_COMMENTAIRE_EXIGE = 'COMMENTAIRE_EXIGE';
	const MODE_INTERFACE_DISCIPLINE = 'DISCIPLINE';
	const MODE_INTERFACE_CHECKBOX_HIDDEN = 'CHECKBOX_HIDDEN';
	const MODE_INTERFACE_CHECKBOX = 'CHECKBOX';

	public static $LISTE_LABEL_TYPE_SAISIE = array(
	    AbsenceEleveType::MODE_INTERFACE_DEBUT_ABS => 'Saisie de l\'heure du debut de l\'absence'
	    , AbsenceEleveType::MODE_INTERFACE_FIN_ABS => 'Saisie de l\'heure de fin debut de l\'absence'
	    , AbsenceEleveType::MODE_INTERFACE_DEBUT_ET_FIN_ABS => 'Saisie de l\'heure du debut et de fin de l\'absence'
	    , AbsenceEleveType::MODE_INTERFACE_NON_PRECISE => 'Type de saisie non précisé'
	    , AbsenceEleveType::MODE_INTERFACE_COMMENTAIRE_EXIGE => 'Saisie d\'un commentaire explicatif'
	    , AbsenceEleveType::MODE_INTERFACE_CHECKBOX => 'Saisie de cases à cocher'
	    , AbsenceEleveType::MODE_INTERFACE_CHECKBOX_HIDDEN => 'Saisie de cases à cocher cachées par défaut'
	    , AbsenceEleveType::MODE_INTERFACE_DISCIPLINE => 'Saisie d\'un incident disciplinaire');

	/**
	 * Les types de RESPONSABILITE_ETABLISSEMENT possibles
	 */
	const SOUS_RESP_ETAB_VRAI = 'VRAI';
	const SOUS_RESP_ETAB_FAUX = 'FAUX';
	const SOUS_RESP_ETAB_NON_PRECISE = 'NON_PRECISE';

	/**
	 * Les types de RESPONSABILITE_ETABLISSEMENT possibles
	 */
	const MANQU_OBLIG_PRESE_VRAI = 'VRAI';
	const MANQU_OBLIG_PRESE_FAUX = 'FAUX';
	const MANQU_OBLIG_PRESE_NON_PRECISE = 'NON_PRECISE';

	/**
	 * Les types de RETARD_BULLETIN possibles
	 */
	const RETARD_BULLETIN_VRAI = 'VRAI';
	const RETARD_BULLETIN_FAUX = 'FAUX';
	const RETARD_BULLETIN_NON_PRECISE = 'NON_PRECISE';

	/**
	 *
	 * Renvoi la description du type de saisie, permet de decrire les code suivant :
	 * DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE
	 *
	 * @return     string description lisible du type de saisie
	 *
	 */
	public function getModeInterfaceDescription() {
	    $type_code = $this->getModeInterface();
	    if ($type_code == "") {
		return "";
	    } else {
		return AbsenceEleveType::$LISTE_LABEL_TYPE_SAISIE[$this->getModeInterface()];
	    }
	}


	/**
	 * renvoi true ou false suivant l'autorisation
	 * M. ou Mlle
	 * @param $statut String
	 * @return     boolean
	 */
	public function isStatutAutorise($statut)
	{
		$criteria = new Criteria();
		$criteria->add(AbsenceEleveTypeStatutAutorisePeer::STATUT, $statut);
		return !($this->getAbsenceEleveTypeStatutAutorises($criteria)->isEmpty());
	}
} // AbsenceEleveType
