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


abstract class Action
{
    protected $_controller;

    public function __construct($controller)
    {
        $this->_controller = $controller;
    }

    abstract public function launch(Request $request, Response $response);

    public function render($file)
    {
        $this->_controller->render($file);
    }

    public function printOut()
    {
        $this->_controller->getResponse()->printOut();
    }

    protected function _forward($module, $action)
    {
        $this->_controller->forward($module, $action);
    }

    protected function _redirect($url)
    {
        $this->_controller->redirect($url);
    }
}


?>