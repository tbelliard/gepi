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
	const TYPE_SAISIE_DEBUT_ABS = 'DEBUT_ABS';
	const TYPE_SAISIE_FIN_ABS = 'FIN_ABS';
	const TYPE_SAISIE_DEBUT_ET_FIN_ABS = 'DEBUT_ET_FIN_ABS';
	const TYPE_SAISIE_NON_PRECISE = 'NON_PRECISE';
	const TYPE_SAISIE_COMMENTAIRE_EXIGE = 'COMMENTAIRE_EXIGE';
	const TYPE_SAISIE_DISCIPLINE = 'DISCIPLINE';

	public static $LISTE_LABEL_TYPE_SAISIE = array(
	    AbsenceEleveType::TYPE_SAISIE_DEBUT_ABS => 'Saisie de l\'heure du debut de l\'absence'
	    , AbsenceEleveType::TYPE_SAISIE_FIN_ABS => 'Saisie de l\'heure de fin debut de l\'absence'
	    , AbsenceEleveType::TYPE_SAISIE_DEBUT_ET_FIN_ABS => 'Saisie de l\'heure du debut et de fin de l\'absence'
	    , AbsenceEleveType::TYPE_SAISIE_NON_PRECISE => 'Type de saisie non précisé'
	    , AbsenceEleveType::TYPE_SAISIE_COMMENTAIRE_EXIGE => 'Saisie d\'un commentaire explicatif'
	    , AbsenceEleveType::TYPE_SAISIE_DISCIPLINE => 'Saisie d\'un incident disciplinaire');

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
	public function getTypeSaisieDescription() {
	    $type_code = $this->getTypeSaisie();
	    if ($type_code == "") {
		return "";
	    } else {
		return AbsenceEleveType::$LISTE_LABEL_TYPE_SAISIE[$this->getTypeSaisie()];
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
