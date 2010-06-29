<?php
/*
 * $Id: class_menu_general.php $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


class itemGeneral
{
	// déclaration des propriétés
	
	public $indexMenu = 0;
	public $indexItem = 0;
	public $icone = array('chemin'=>'','titre'=>'','alt'=>'');	//données de l'icône
	public $key_setting = '' ;																	//test dans setting pour choisir l'icône
	public $chemin="" ;																					//chemin du lien
	public $titre="" ;																					//titre court
	public $expli="" ;																					//explications

	
	// constructeur
	function __construct() 
	{
	}
	
  function __destruct() 
  {
  }

	// déclaration des méthodes
	function acces($id,$statut) 
	{
		$tab_id = explode("?",$id);
    $query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
    $droit = @mysql_result($query_droits, 0, $statut);
    if ($droit == "V") {
        return "1";
    } else {
        return "0";
    }
	}

	function choix_icone($key_setting) 
	{
		if($key_setting!='')
		{
			$sql="SELECT 1=1 FROM setting WHERE name LIKE '$key_setting' AND (value='y' OR value='yes');";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0)
			{
				$this->icone['chemin'] = "images/enabled.png";
				$this->icone['titre'] = "Module actif";
				$this->icone['alt'] = "Module actif";
			}
			else 
			{
				$this->icone['chemin'] = "images/disabled.png";
				$this->icone['titre'] = "Module inactif";
				$this->icone['alt'] = "Module inactif";
			}
		}
		else
		{
			$this->icone['chemin'] = "images/icons/ico_question.png";
			$this->icone['titre'] = "Etat inconnu";
			$this->icone['alt'] = "Etat inconnu";
		}
	}

}

class menuGeneral
{
	// déclaration des propriétés
	
	public $indexMenu = 0;
	public $classe='accueil';
	public $icone= array('chemin'=>'./images/icons/control-center.png','titre'=>'', 'alt'=>"");
	public $texte='';
	
	// constructeur
	function __construct() 
	{
	}
	
  function __destruct() 
  {
  }
	// déclaration des méthodes

}

?>
