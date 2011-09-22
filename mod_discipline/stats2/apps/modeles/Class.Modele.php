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

class Modele {
    private $row=Null;
    private $variable=Null;
    private $liste=Null;
    protected  function set_array($type,$res) {        
        unset($this->variable);
        if(mysql_num_rows($res)==0) {
            $this->variable['error']='pas de résultats';
        }else {
            switch ($type) {
                case 'array': while($this->row=mysql_fetch_array($res)) {
                        $this->variable[]=$this->row;
                    }
                    break;
                case 'assoc': while($this->row=mysql_fetch_assoc($res)) {
                        $this->variable[]=$this->row;
                    }
                    break;
                case 'object': while($this->row=mysql_fetch_object($res)) {
                        $this->variable[]=$this->row;
                    }
                    break;
            }
        }
        return $this->variable;
    }

    public function make_list_for_request_in($array) {
        $this->liste=implode(',',$array);
        return($this->liste=str_replace(",","','",$this->liste));
    }
}
?>
