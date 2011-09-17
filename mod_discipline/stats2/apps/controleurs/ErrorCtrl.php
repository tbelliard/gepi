<?php
/*
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Didier Blanqui
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
// On empêche l'accès direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};

$niveau_arbo = 2;
require_once("../../lib/initialisations.inc.php");
require_once ("Controleur.php");


class ErrorCtrl extends Controleur {

 private $errors=Null;

  function  __construct() {
    parent::__construct();
  }

  function index(){

    }

    function select(){
        $this->errors[]="Vous n'avez sélectionné aucune donnée à traîter";
        $this->errors[].="Sélectionnez des individus ou des classes dans l'onglet sélection..";
        $this->vue->setVar('errors', $this->errors);
        $this->vue->afficheVue("error.php",$this->vue->getVars());
    }
}
?>
