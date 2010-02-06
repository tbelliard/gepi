<?php
/*
 * $Id$
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

class ClassFilter {
    private $modele_incidents=Null;   
    private $id_categories_selected;
    private $id_mesures_selected;   

    function  __construct() {
        $this->modele_incidents=new Modele_Incidents();
        $this->roles_selected=isset($_SESSION['filtre']['roles'])?$_SESSION['filtre']['roles']:Null;
        $this->id_categories_selected=isset($_SESSION['filtre']['categories'])?$_SESSION['filtre']['categories']:Null;
        $this->id_mesures_selected=isset($_SESSION['filtre']['mesures'])?$_SESSION['filtre']['mesures']:Null;
        $this->natures_sanctions_selected=isset($_SESSION['filtre']['sanctions'])?$_SESSION['filtre']['sanctions']:Null;        
    }

    public function get_roles_selected(){
        return $this->roles_selected;
    }
    public function get_id_categories_selected(){
       return $this->id_categories_selected;
    }
    public function get_id_mesures_selected(){
        return $this->id_mesures_selected;
    }
    public function get_natures_sanctions_selected(){
       return $this->natures_sanctions_selected;
    }
}
?>
