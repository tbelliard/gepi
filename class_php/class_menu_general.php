<?php
/*
 * $Id$
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


class itemGeneral {
 
	// déclaration des propriétés
	
	public $indexMenu = 0;
	public $indexItem = 0;
	public $icone = array('chemin'=>'','titre'=>'','alt'=>'');	//données de l'icône
	public $key_setting = '' ;																	//test dans setting pour choisir l'icône
	public $chemin="" ;																						//chemin du lien
	public $titre="" ;																					//titre court
	public $expli="" ;																				//explications

	
	// constructeur
/** * @class: itemGeneral :
 *
 * Regroupe les données nécessaires au remplissage d'un item de menu du type accueil.php ou accueil_modules.php
 */
	function __construct() 
	{
	}
	
  function __destruct() 
  {
  }

	// déclaration des méthodes

/**
 *
 * Vérifie qu'un utilisateur à le droit de voir la page en lien
 *
 * @var string $id l'adresse de la page
 * telle qu'enregistrée dans la base droits
 * @var string $statut le statut de l'utilisateur
 *
 * @return entier 1 si l'utilisateur a le droit de voir la page
 * 0 sinon
 *
 *
 */
	function acces($id,$statut) 
	{ 
		if ($_SESSION['statut']!='autre') {
			$tab_id = explode("?",$id);
			$query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
			$droit = @mysql_result($query_droits, 0, $statut);
			if ($droit == "V") {
				return "1";
			} else {
				return "0";
			}
	  } else {
	  
			$sql="SELECT ds.autorisation FROM `droits_speciaux` ds,  `droits_utilisateurs` du
						WHERE (ds.nom_fichier='".$id."'
							AND ds.id_statut=du.id_statut
							AND du.login_user='".$_SESSION['login']."');" ;
			$result=mysql_query($sql);
			if (!$result) {
				return FALSE;
			} else {
				$row = mysql_fetch_row($result) ;
				if ($row[0]=='V' || $row[0]=='v'){
				return TRUE;
				} else {
				return FALSE;
				}
			}
	
	  }
	}

/**
 * 
 * Met à jour l'icone à afficher avant un item de menu
 * en interrogeant la table setting
 *
 * @var string $key_setting
 * @return
 * images/icons/ico_question.png si vide
 * coche selon les enregistrements dans la table setting
 * images/enabled.png ou images/disabled.png
 *
 */
	function choix_icone($key_setting, $special="") 
	{
		if($key_setting!='')
		{
			if($special=="mod_abs2") {
				$sql="SELECT 1=1 FROM setting WHERE name LIKE '$key_setting' AND value='2';";
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
			elseif($special=="mod_absences") {
				$sql="SELECT 1=1 FROM setting WHERE name LIKE '$key_setting' AND (value='y' OR value='yes');";
				$test=mysql_query($sql);

				$sql="SELECT 1=1 FROM setting WHERE name='active_module_absence' AND value='2';";
				$test2=mysql_query($sql);

				if((mysql_num_rows($test)>0)&&(mysql_num_rows($test2)==0))
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
			else {
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
	public $bloc='';
	public $nouveauNom='';

	// constructeur
/** * @class: menuGeneral :
 *
 * Regroupe les données nécessaires au remplissage des entêtes de menu du type accueil.php ou accueil_modules.php
 */
	function __construct()
	{
	}

  function __destruct()
  {
  }
	// déclaration des méthodes

}

class changeMenuGeneral extends menuGeneral
{


}

class changeItemGeneral extends itemGeneral {


}

?>
