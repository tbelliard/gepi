<?php
/**
 *
 *
 * @version $Id: parametrage_ajax.php 2708 2008-11-28 21:37:48Z jjocal $
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
 * Classe qui permet d'utiliser les créneaux de Gepi
 *
 * @author jjocal
 */
class Abs_creneau extends activeRecordGepi {


  public function  __construct() {
    parent::__construct(__CLASS__);
  }

  /**
   * Méthode qui transforme les secondes de l'horaire en heure fançaise hh:mm:ss
   *
   * @access public
   * @return void
   */
  public function heureFr($var){
    $heures = floor($var / 3600);
    $reste = $var % 3600;
    $minutes = floor($reste / 60);

    return $heures . ':' . $minutes;
  }
}
?>
