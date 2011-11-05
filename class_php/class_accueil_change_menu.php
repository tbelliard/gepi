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



class class_accueil_change_menu {

  public $message="";
  private $ordreActuelMenu=array();
  private $ordreFuturMenu=array();
  private $ordreActuelItem=array();
  private $ordreFuturItem=array();

/**
 * 
 * Enregistre les changements d'ordre dans le menu Accueil
 *
 * @author regis
 *
 */
  function __construct($ordreDemande){
	
	if (!$this->chargeNouvelOrdre($ordreDemande)){
	  $this->message="Erreur lors de la récupération de l'ordre des menus" ;
	}else if (!$this->chargeAncienOrdre()){
	  $this->message="Erreur lors de la lecture de la table mn_ordre_accueil" ;
	  }else if (!$this->doublonMenu()){
	  $this->message="Erreur lors de la recherche de doublons" ;
	}else if (!$this->modifieOrdreMenu()){
	  $this->message="Erreur lors de l'enregistrement du nouvel ordre" ;
	}

  }
  
  private function chargeNouvelOrdre($ordreDemande){
 		$nbItem= count($ordreDemande) ;
 		$i=0;
		while ($i<$nbItem) {
			switch (current($ordreDemande)){
				case "menu":
				$this->ordreFuturMenu[]=array('bloc'=>next($ordreDemande), 'statut'=>next($ordreDemande),'ancienNum'=>next($ordreDemande),'nouveauNum'=>next($ordreDemande),'nouveauNom'=>next($ordreDemande));
				$i+=5;
				default:
			}
 		next($ordreDemande);
 		$i++;
		}
		return true;
  }

  private function chargeAncienOrdre(){
	$sql="SELECT * FROM mn_ordre_accueil";
	$resp = mysql_query($sql);
	if ($resp){
	  if (mysql_num_rows($resp)>0){
		while($lig_log=mysql_fetch_array($resp)) {
		  $this->ordreActuelItem[]=$lig_log;
		}
	  }
	  return true;
	}
	  return false;
  }

  private function modifieOrdreMenu(){
		
	foreach ($this->ordreFuturMenu as $nouvelOrdre){
	  $miseAJour=false;
	  if (count($this->ordreActuelItem)){
		foreach ($this->ordreActuelItem as $AncienOrdre){
		  if ($AncienOrdre['num_menu']==$nouvelOrdre['ancienNum']){
			// On a déjà un enregistrement
			$miseAJour=true;
			break;
		  }
		}
	  }
	  if ($miseAJour){
		$sql="UPDATE `mn_ordre_accueil`
		  SET `num_menu` = '".$nouvelOrdre['nouveauNum']."',
			`nouveau_nom`='".$nouvelOrdre['nouveauNom']."'
			WHERE `bloc` ='".$nouvelOrdre['bloc']."'
			AND `statut`= '".$nouvelOrdre['statut']."' ;";
	  }
	  else{
		$sql="INSERT INTO mn_ordre_accueil (`id`, `statut`, `bloc`, `num_menu`, `nouveau_nom`)
			VALUES (NULL, '".$nouvelOrdre['statut']."' ,
			  '".$nouvelOrdre['bloc']."',
			  '".$nouvelOrdre['nouveauNum']."',
				'".$nouvelOrdre['nouveauNom']."');";
	  }

	  if(!mysql_query($sql))
		return false;
	  unset ($AncienOrdre);
	}
	unset ($nouvelOrdre);
	return true;
  }

  private function doublonMenu(){

$ordreDemande=$this->ordreFuturMenu ;
	$recommence=true;
	while ($recommence){
	$recommence=false;
	  foreach ($this->ordreFuturMenu as $nouvelOrdre){
		foreach ($this->ordreFuturMenu as &$doublon){
		  if(($doublon['ancienNum'] == $nouvelOrdre['nouveauNum'])
				  && ($doublon['nouveauNum'] == $nouvelOrdre['nouveauNum'])
				  && ($doublon['statut'] == $nouvelOrdre['statut'])
				  && ($doublon['bloc']!=$nouvelOrdre['bloc'])){
			$doublon['nouveauNum'] = intval($doublon['nouveauNum']) + 1;
			$recommence=true;
			break;
		  }
		}
		unset ($doublon);
		if($recommence)
		  break;
	  }
	}
	unset ($nouvelOrdre);
	return true;
  }

  public function optimiseMenu(){
	/**
	* Supprime les trous dans la numérotation des menus
	 *
	 */
	$numActuel = 0;
	$statut="";
	$sql1 = "SELECT	`bloc`,`statut` FROM `mn_ordre_accueil`
			  ORDER BY `statut` ASC,`num_menu` ASC;";
	
	$resp1 = mysql_query($sql1);
	if ($resp1){
	  while ($nouvelOrdre = mysql_fetch_array($resp1)) {
		if ($statut==$nouvelOrdre['statut'])
		{
		  $numActuel++;
		}else{
		  $numActuel = 0;
		}
		
		$statut=$nouvelOrdre['statut'];
		$sql2="UPDATE `mn_ordre_accueil`
			SET `num_menu` = '".$numActuel."'
			WHERE `bloc` ='".$nouvelOrdre['bloc']."'
			AND `statut`= '".$nouvelOrdre['statut']."' ;";
		 //$resp2 = mysql_query($sql2);
		 if (!mysql_query($sql2))
		   echo 'ça coince <br />';
	  }
	  unset($nouvelOrdre) ;
	}
		
	
  }

}
?>
