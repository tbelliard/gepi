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
class Request 
{
    public function getParam($key)
    {
        //return filter_var($this->getTaintedParam($key), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		return $this->getTaintedParam($key);
    }
	
    public function getTaintedParam($key)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST[$key])){
				return $_POST[$key];
			}
			else {
				return null;
			}
        }else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			
			if (isset($_GET[$key])){
				return $_GET[$key];
			}
			else {
				return null;
			}
        }
		else {
			return null;
		}
    }
	
    public function route()
    {
		$matches = array();
        $args = explode('&', parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY));
		foreach ($args as $arg) {
			$pos = strpos($arg, "action=");		
			if ($pos !== false) {
				$matches['action'] = mb_substr($arg, $pos+7);
			}		
		}
        return $matches;
    }
}
?>