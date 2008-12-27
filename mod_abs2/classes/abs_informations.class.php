<?php
/**
 *
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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

/**
 *
 * Description of abs_informationsclass
 *
 * Struture de la table
 *
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
