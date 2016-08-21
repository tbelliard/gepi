<?php
/*
 *
 * Copyright 2011 Pascal Fautrero
 *
 * This file is part of GEPi.
 *
 * GEPi is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPi is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPi; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
class Response
{
    private $_vars = array();
    private $_headers = array();
    private $_body;
	

    public function addVar($key, $value)
    {

		if (array_key_exists($key, $this->_vars)) {
			$this->_vars[$key] .= $value;
		}
		else {
			$this->_vars[$key] = $value;		
		}
    }

    public function getVar($key)
    {
        return $this->_vars[$key];
    }

    public function getVars()
    {
        return $this->_vars;
    }

    public function setBody($value)
    {
        $this->_body = $value;
    }
    
    public function redirect($url, $permanent = false)
    {
        if ($permanent){
            $this->_headers['Status'] = '301 Moved Permanently';
        }else{
            $this->_headers['Status'] = '302 Found';
        }
        $this->_headers['location'] = $url;
    }

    public function printOut()
    {
        foreach ($this->_headers as $key => $value) {
            header($key. ':' . $value);
        }
        echo $this->_body;
    }
}





?>