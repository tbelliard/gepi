<?php
/*
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

require_once ("Controleur.php");
require_once("ImportModele.php");
class MajCtrl extends Controleur {
  function index () {

    $this->vue->LoadTemplate('maj.php');
    
    $sql="SELECT nom , prenom , statut , login FROM `utilisateurs` u  WHERE NOT login IN
        (SELECT login_gepi FROM sso_table_correspondance)
        ORDER BY nom , prenom ";
    
    $resp=mysqli_query($GLOBALS["mysqli"], $sql);
    if(mysqli_num_rows($resp)==0) {
        $this->var[]=Array('nom'=>'','statut'=>'','login_gepi'=>'','login_sso'=>'','ligne'=>'');
    } else {
        $lig=-1;
        while($lig_correspond=mysqli_fetch_object($resp)) {
            $nomPrenom = $lig_correspond->nom." ".$lig_correspond->prenom;
          $this->var[]=Array('nom'=>"$nomPrenom",'statut'=>"$lig_correspond->statut" , 'login_gepi'=>"$lig_correspond->login",'ligne'=>$lig);
           $lig *= -1; 
        }
        $this->vue->MergeBlock('sso1',$this->var) ;
    } 
    
    $this->vue->show();
  }
  function search () {         
    try {
      $this->nom=$_POST['nom'];
      $data=new ImportModele();
      $this->search_result=$data->search($this->nom);
      if(!$this->search_result)throw new exception("Aucun utilisateur correspondant à vos critères de recherche n'existe dans Gépi avec son compte paramétré en sso");
      $this->vue->LoadTemplate('result_search.php');
      $this->vue->MergeBlock('b1',$this->search_result) ;
        $this->vue->MergeBlock('sso1',array()) ;
      $this->vue->show();

    }catch (Exception $e) {
      $this->vue->LoadTemplate('exceptions.php');
      $this->mess[]=Array('mess'=>$e->getMessage());
      $this->vue->MergeBlock('b1',$this->mess) ;
        $this->vue->MergeBlock('sso1',array()) ;
      $this->vue->Show() ;

    }
  }
  function update() {
    try {
      $this->login_gepi=$_GET['login_gepi'];
      $data=new ImportModele();
      $this->login_sso=$data->get_login_sso_table_sso($this->login_gepi);
      $this->vue->LoadTemplate('update.php');
      $this->var[]=Array('login_gepi'=>$this->login_gepi,'login_sso'=>$this->login_sso);
      $this->vue->MergeBlock('b1',$this->var) ;
      $this->vue->show();
    }catch (Exception $e) {
      $this->vue->LoadTemplate('exceptions.php');
      $this->mess[]=Array('mess'=>$e->getMessage());
      $this->vue->MergeBlock('b1',$this->mess) ;
      $this->vue->Show() ;

    }

  }
  function updated() {
    try {
      $this->login_gepi=$_POST['login_gepi'];
      $this->login_sso=$_POST['login_sso'];
      if($this->login_sso=="")throw new Exception ('Vous devez entrer une valeur pour la correspondance');
      $data=new ImportModele();
      if($data->verif_exist_login_sso($this->login_sso)) throw new Exception ('Une entrée existe déja avec ce login sso; mise à jour impossible');
      if($data->get_login_sso_table_sso($this->login_gepi)) $this->mode='update';
      else $this->mode='insert';
      $data->maj_sso_table($this->login_gepi,$this->login_sso,$this->mode);
      $this->vue->LoadTemplate('updated.php');
      $this->var[]=Array('login_gepi'=>$this->login_gepi,'login_sso'=>$this->login_sso);
      $this->vue->MergeBlock('b1',$this->var) ;
      $this->vue->show();
    }catch (Exception $e) {
      $this->vue->LoadTemplate('exceptions.php');
      $this->mess[]=Array('mess'=>$e->getMessage());
      $this->vue->MergeBlock('b1',$this->mess) ;
      $this->vue->Show() ;

    }

  }
}
?>