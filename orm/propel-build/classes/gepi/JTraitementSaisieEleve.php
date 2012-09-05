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
	 * Code to be run after deleting the object in database
	 * @param PropelPDO $con
	 */
	public function postDelete(PropelPDO $con = null) {
		$traitement = $this->getAbsenceEleveTraitement();
		if ($traitement != null && !$traitement->getAlreadyInSave()) {
			$traitement->setUpdatedAt('now'); //au lieu d'utiliser un champ supplémentaire pour la date de mise à jours des jointures entre saisies et traitement, on précise la date de mise à jour des jointure dans le traitement directement
			$traitement->save();
		}
		$saisie = $this->getAbsenceEleveSaisie();
		if ($saisie != null && $saisie->getEleve() != null && !$saisie->getAlreadyInSave() && AbsenceEleveSaisiePeer::isAgregationEnabled()) {
			$saisie->getEleve()->clearAbsenceEleveSaisiesParJour();
			$saisie->updateSynchroAbsenceAgregationTable();
			$saisie->checkAndUpdateSynchroAbsenceAgregationTable();
		}
	}
	
	/**
	 * Code to be run after persisting the object
	 * @param PropelPDO $con
	 */
	public function postSave(PropelPDO $con = null) {
		$traitement = $this->getAbsenceEleveTraitement();
		if ($traitement != null && !$traitement->getAlreadyInSave()) {
			$traitement->setUpdatedAt('now'); //au lieu d'utiliser un champ supplémentaire pour la date de mise à jours des jointures entre saisies et traitement, on précise la date de mise à jour des jointure dans le traitement directement
			$traitement->save();
		}
		//le traitement est sauvé ci dessus, ou alors il est en cours de sauvegarde. La table d'agrégation va être recalculé pour les saisies de ce traitement, ce n'est donc pas necessaire de le faire ici
	}
	
	/**
	 * Declares an association between this object and a AbsenceEleveTraitement object.
	 *
	 * @param      AbsenceEleveTraitement $v
	 * @return     JTraitementSaisieEleve The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAbsenceEleveTraitement(AbsenceEleveTraitement $v = null) {
	    if ($this->getATraitementId() != null && $v != null && $this->getATraitementId() != $v->getId()){
	        throw new PropelException('Il ne faut pas modifier une jointure existante jTraitementSaisieEleve car la mise à jour de la table d agrégation non implémentée pour cette méthode');
	    }
	    return parent::setAbsenceEleveTraitement($v);
	}
	
	/**
	 * Declares an association between this object and a AbsenceEleveSaisie object.
	 *
	 * @param      AbsenceEleveSaisie $v
	 * @return     JTraitementSaisieEleve The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAbsenceEleveSaisie(AbsenceEleveSaisie $v = null) {
	    if ($this->getASaisieId() != null && $v != null && $this->getASaisieId() != $v->getId()){
	        throw new PropelException('Il ne faut pas modifier une jointure existante jTraitementSaisieEleve car la mise à jour de la table d agrégation non implémentée pour cette méthode');
	    }
	    return parent::setAbsenceEleveSaisie($v);
	}
	
	/**
	 * Set the value of [a_traitement_id] column.
	 * cle etrangere du traitement de ces absences
	 * @param      int $v new value
	 * @return     JTraitementSaisieEleve The current object (for fluent API support)
	 */
	public function setATraitementId($v)
	{
	    if ($this->getATraitementId() != null && $this->getATraitementId() != $v){
	        throw new PropelException('Il ne faut pas modifier une jointure existante jTraitementSaisieEleve car la mise à jour de la table d agrégation non implémentée pour cette méthode');
	    }
	    return parent::setATraitementId($v);
	}
	
	/**
	 * Set the value of [a_saisie_id] column.
	 * cle etrangere de l'absence saisie
	 * @param      int $v new value
	 * @return     JTraitementSaisieEleve The current object (for fluent API support)
	 */
	public function setASaisieId($v)
	{
	    if ($this->getASaisieId() != null && $this->getASaisieId() != $v){
	        throw new PropelException('Il ne faut pas modifier une jointure existante jTraitementSaisieEleve car la mise à jour de la table d agrégation non implémentée pour cette méthode');
	    }
	    return parent::setASaisieId($v);
	}

} // JTraitementSaisieEleve
