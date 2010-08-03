<?php



/**
 * Skeleton subclass for representing a row from the 'a_notifications' table.
 *
 * Notification (a la famille) des absences
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceEleveNotification extends BaseAbsenceEleveNotification {

    public static $STATUT_INITIAL = 0;
    public static $STATUT_EN_COURS = 1;
    public static $STATUT_ECHEC = 2;
    public static $STATUT_SUCCES = 3;
    public static $STATUT_SUCCES_AR = 4;

    public static $LISTE_LABEL_STATUT = array(0 => "initial", 1 => "en cours", 2 => "échec", 3 => "succès", 4 => "succes avec A/R");

    public static $TYPE_COURRIER = 0;
    public static $TYPE_EMAIL = 1;
    public static $TYPE_SMS = 2;
    public static $TYPE_TELEPHONIQUE = 3;

    public static $LISTE_LABEL_TYPE = array(0 => "courrier", 1 => "email", 2 => "sms", 3 => "communication téléphonique");


    /**
     *
     * Renvoi true / false suivant que la notification est modifiable ou pas
     *
     * @return     String description
     *
     */
    public function getModifiable() {
	//modifiable uniquement si le statut est initial
	return $this->getStatutEnvoi() == AbsenceEleveNotification::$STATUT_INITIAL;
    }

    /**
     *
     * Renvoi une description intelligible de la notification
     *
     * @return     String description
     *
     */
    public function getDescription() {
	$desc = '';
	if (isset(AbsenceEleveNotification::$LISTE_LABEL_TYPE[$this->getTypeNotification()])) {
	    $desc .= "type ".AbsenceEleveNotification::$LISTE_LABEL_TYPE[$this->getTypeNotification()]."; ";
	}
	if (isset(AbsenceEleveNotification::$LISTE_LABEL_STATUT[$this->getStatutEnvoi()])) {
	    $desc .= "statut : ".AbsenceEleveNotification::$LISTE_LABEL_STATUT[$this->getStatutEnvoi()]."; ";
	}
	if ($this->getDateEnvoi() != null) {
	    $desc .= strftime("%a %d/%m/%Y %H:%M", $this->getDateEnvoi('U'));
	}
	return $desc;
    }

} // AbsenceEleveNotification
