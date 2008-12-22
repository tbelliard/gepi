<?php

/**
 * Description of abs_informationsclass
 * `abs_informations`
 * `id` int(11) NOT NULL auto_increment
 * `utilisateurs_id` int(4) NOT NULL
 * `eleves_id` varchar(100) NOT NULL
 * `date_saisie` int(13) NOT NULL
 * `debut_abs` int(12) NOT NULL
 * `fin_abs` int(12) NOT NULL
 * PRIMARY KEY  (`id`)
 *
 * @author jjocal
 */
class Abs_information extends activeRecordGepi {

  /**
   * Constructeur qui appelle le constructeur de la classe mère
   *
   * @access public
   */
  public function  __construct() {
    parent::__construct(__CLASS__);
  }
}
?>
