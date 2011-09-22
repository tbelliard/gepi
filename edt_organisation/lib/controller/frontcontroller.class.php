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
include("./lib/controller/request.class.php");
include("./lib/controller/response.class.php");
include("./lib/view/view.class.php");
class FrontController
{
    private $_defaults = array('action' => 'index');
    private $_request;
    private $_response;
    private static $_instance = null;

    private function __construct()
    {
        $this->_request = new Request();
        $this->_response = new Response();
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function dispatch($defaults = null)
    {
        $parsed = $this->_request->route();
        $parsed = array_merge($this->_defaults, $parsed);
        $this->forward($parsed['action']);
    }

    public function forward($action)
    {
        $command = $this->_getCommand($action);
        $command->launch($this->_request, $this->_response);
    }

    private function _getCommand($action)
    {
		//if ($_SESSION['statut'] == "administrateur") {
			$path = "./lib/actions/$action.php";
			if(!file_exists($path)){
				$action="index";
				$path = "./lib/actions/$action.php";			
			}
		//}
		//else {
		//	$action="forbidden";
		//	$path = "./lib/actions/$action.php";	
		//}
        require($path);
        $class = $action.'Action';
        return new $class($this);
    }

    public function getResponse()
    {
        return $this->_response;
    }

    public function redirect($url)
    {
        $this->_response->redirect($url);
    }

    public function render($file)
    {
        $view = new View();
        $this->_response->setBody($view->render($file,$this->_response->getVars()));
    }
}

?>