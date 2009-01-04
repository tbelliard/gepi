<?php

require 'gepi/om/BaseCtDevoirDocument.php';


/**
 * Skeleton subclass for representing a row from the 'ct_devoirs_documents' table.
 *
 * Document (fichier joint) appartenant a un travail Ã  faire du cahier de texte
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class CtDevoirDocument extends BaseCtDevoirDocument {

	/**
	 * Initializes internal state of CtDevoirDocument object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

		/**
	 * Sets contents of passed object to values from current object. Copy the associated file (manually added)
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of CtDocument (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		parent::copyInto($copyObj, $deepCopy);
		$emplacement = $copyObj->getEmplacement();
		if (ereg("\.([^.]+)$", $emplacement, $match)) {
			$ext = strtolower($match[1]);
		} else {
			throw new PropelException('File copy failed, bad name string : '.$emplacement);
		}
		$nom_sans_ext = substr($emplacement,0,strlen($emplacement)-(strlen($ext)+1));
		$n = 1;
		$newEmplacement = $nom_sans_ext."-1.".$ext;
		while (file_exists($newEmplacement)) {
			$n++;
			$newEmplacement= $nom_sans_ext."-".$n.".".$ext;
		}

		$ok = @copy($emplacement, $newEmplacement);
		if (!$ok) {
			throw new PropelException('File copy failed : '.$emplacement);
		}
		$copyObj->setEmplacement($newEmplacement);
		return $copyObj;
	}

	/**
	 * Makes a copy of this object that will be inserted as a new row in table when saved.
	 * It creates a new object filling in the simple attributes, but skipping any primary
	 * keys that are defined for the table.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     CtDocument Clone of current object.
	 * @throws     PropelException
	 */
	public function copy($deepCopy = false)
	{
		// we use get_class(), because this might be a subclass
		$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		return $copyObj;
	}

	/**
	 * Remove associated file and Removes this object from datastore and sets delete attribute. Manualy added
	 *
	 * @param      PropelPDO $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete(PropelPDO $con = null)
	{
		//suppression du fichier associé
		$del = @unlink($this->getEmplacement());
		if (file_exists($empl)) {
			throw new PropelException("Erreur : le fichier ".$this->getEmplacement()." n'a pa pu être supprimé. Contactez l'administrateur du site.");
		}
		parent::delete();
	}
	
} // CtDevoirDocument
