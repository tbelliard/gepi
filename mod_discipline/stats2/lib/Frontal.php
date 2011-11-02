<?php
/*
 * $Id: Frontal.php 7799 2011-08-17 08:38:10Z dblanqui $
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

/**
 * Classe gérant les requêtes HTTP et les routant
 * vers le controleur et l'action appropriée
 * Code adapté du controleur de Philippe Rigaux:
 *  http://www.lamsade.dauphine.fr/rigaux/mysqlphp
 */
// On empêche l'accès direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};
class Frontal {
    const NOM_CTRL = "ctrl";
    const NOM_ACTION = "action";
    private static $instance =null;

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance=new self();
        }
        return self::$instance;
    }
    /**
     * Execution d'une requête HTTP
     */

    function execute ()
    {
        // D'abord, on récupère les noms du contrôleur et de l'action
        if (isSet($_GET[Frontal::NOM_CTRL]))
            $controleur = ucfirst($_GET[Frontal::NOM_CTRL]) . "Ctrl";
        else
            $controleur = "SelectCtrl";

        if (isSet($_GET[Frontal::NOM_ACTION]))
            $action = $this->lcfirst($_GET[Frontal::NOM_ACTION]);
        else
            $action = "index";
        // Maintenant chargeons la classe
        $chemin = "controleurs" . DIRECTORY_SEPARATOR . $controleur . ".php";
        if (file_exists("apps" . DIRECTORY_SEPARATOR . $chemin)) {
            require_once($chemin);
        } else {
            throw new Exception ("Le contrôleur <b>$controleur</b> n'existe pas");
        }
        // On instancie un objet
        eval ("\$ctrl = new $controleur();");
        // Il faut vérifier que l'action existe
        if (!method_exists($ctrl, $action)) {
            throw new Exception ("L'action <b>$action</b> n'existe pas");
        }
        // Et pour finir il n'y a plus qu'à exécuter l'action
        call_user_func(array($ctrl, $action));
    }

    /**
     * Parfois la fonction lcfirst n'existe pas dans une distribution PHP...
     */

    function lcfirst($str)
    {
        $str[0] = strtolower($str[0]);
        return (string)$str;
    }
}

?>