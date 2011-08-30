<?php



/**
 * Skeleton subclass for representing a row from the 'j_traitements_saisies' table.
 *
 * Table de jointure entre la saisie et le traitement des absences
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class JTraitementSaisieEleve extends BaseJTraitementSaisieEleve {

	/**
	 * Removes this object from datastore and sets delete attribute. Custom : suppression des notifications et jointures associées et calcul de la table d'agrégation
	 *
	 * @param      PropelPDO $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete(PropelPDO $con = null)
	{
		$saisie = $this->getAbsenceEleveSaisie();
		$traitement = $this->getAbsenceEleveTraitement();
		parent::delete($con);
		if ($traitement != null && !$traitement->getAlreadyInSave()) {
			$traitement->setUpdatedAt('now'); //au lieu d'utiliser un champ supplémentaire pour la date de mise à jours des jointures entre saisies et traitement, on précise la date de mise à jour des jointure dans le traitement directement
			$traitement->save();
		}
	}
	
	/**
	 * Ajout manuel : renseignement automatique de la date de modification du traitement correspondant.
	 * Appel de la mise à jour de la table d'agrégation
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All modified related objects will also be persisted in the doSave()
	 * method.  This method wraps all precipitate database operations in a
	 * single transaction.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save(PropelPDO $con = null) {
		//$this->
		$result = parent::save();
		$saisie = $this->getAbsenceEleveSaisie();
		$traitement = $this->getAbsenceEleveTraitement();
		if ($traitement != null && !$traitement->getAlreadyInSave()) {
			$traitement->setUpdatedAt('now'); //au lieu d'utiliser un champ supplémentaire pour la date de mise à jours des jointures entre saisies et traitement, on précise la date de mise à jour des jointure dans le traitement directement
			$traitement->save();
		}
		//le traitement est sauvé ci dessus, ou alors il est en cours de sauvegarde. La table d'agrégation va être recalculé pour les saisies de ce traitement, ce n'est donc pas necessaire de le faire ici
		return $result;
	}
} // JTraitementSaisieEleve
