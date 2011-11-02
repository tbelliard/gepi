<?php
/*
* $Id: NettoyageCtrl.php 7744 2011-08-14 13:07:15Z dblanqui $
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

class NettoyageCtrl extends Controleur {

  private $choix = Null;
  private $nbre_entrees = Null;
  private $data = Null;
  private $table = Null;

  function __construct() {
    parent::__construct();
    $this->data = new ImportModele();
  }

  function index() {

    $this->vue->LoadTemplate('choix_nettoyage.php');
    $this->vue->show();
  }

  function choix() {

    $this->choix = $_POST['choix'];
    switch ($this->choix) {
      case "vidage_complet":
        $this->nbre_entrees = $this->data->get_nbre_entrees();
        $this->setVarGlobal('choix_info', 'avertissement_vidage_complet');
        $this->vue->LoadTemplate('nettoyage.php');
        $this->vue->MergeField('nbre_entrees', $this->nbre_entrees);
        $this->vue->show();
        break;
      case "anciens_comptes":
        if (is_array($this->data->get_anciens_comptes())) {
          foreach ($this->data->get_anciens_comptes() as $login) {
            $this->table[] = Array("login_gepi" => $login);
          }
          if (!is_null($this->table)) {
            $this->setVarGlobal('choix_info', 'avertissement_anciens_comptes');
            $this->vue->LoadTemplate('nettoyage.php');
            $this->vue->MergeBlock('b1', $this->table);
            $this->vue->show();
          } else {
            $this->setVarGlobal('choix_info', 'aucun_anciens_comptes');
            $this->vue->LoadTemplate('nettoyage.php');
            $this->vue->show();
          }
        } else {
          $this->setVarGlobal('choix_info', 'table_vide');
          $this->vue->LoadTemplate('nettoyage.php');
          $this->vue->show();
        }
        break;
      case "profil":
        $this->setVarGlobal('choix_info', 'choix_profil');
        $this->setVarGlobal('message', '');
        $this->vue->LoadTemplate('nettoyage.php');
        $this->vue->show();
        break;
      case "classe":
        $this->setVarGlobal('choix_info', 'choix_classe');
        $this->setVarGlobal('message', '');
        $this->get_classes();
        $this->vue->LoadTemplate('nettoyage.php');
        $this->vue->MergeBlock('b1', $this->table);
        $this->vue->show();
        break;
    }
  }

  function vidage_complet() {
    $this->setVarGlobal('choix_info', 'vidage_complet');
    $this->nbre_entrees_nettoyees = $this->data->vide_table();
    $this->vue->LoadTemplate('nettoyage.php');
    $this->vue->MergeField('nbre_entrees_nettoyees', $this->nbre_entrees_nettoyees);
    $this->vue->show();
  }

  function supp_anciens_comptes() {

    if ($this->data->get_anciens_comptes()) {
      $this->setVarGlobal('choix_info', 'supp_anciens_comptes');
      foreach ($this->data->get_anciens_comptes() as $login) {
        if ($this->data->delete_from_table_sso($login) == 1) {
          $this->messages = $this->get_message('1');
        } else {
          $this->messages = $this->get_message('0');
        }
        $this->table[] = Array("login_gepi" => $login, 'couleur' => $this->messages[0], 'message' => $this->messages[1]);
      }
      $this->vue->LoadTemplate('nettoyage.php');
      $this->vue->MergeBlock('b1', $this->table);
      $this->vue->show();
    } else { //la table est déja vide; normalement on n'arrivera jamais ici car le test a été fait avant
      $this->setVarGlobal('choix_info', 'table_vide');
      $this->vue->LoadTemplate('nettoyage.php');
      $this->vue->show();
    }
  }

  function choix_profil() {
    if (isset($_SESSION['choix_profil']))
      unset($_SESSION['choix_profil']);
    $this->setVarGlobal('choix_info', 'avertissement_profil');
    if (isset($_POST['choix_profil'])) {
      $_SESSION['choix_profil'] = $_POST['choix_profil'];
      foreach ($_SESSION['choix_profil'] as $profil) {
        $nombre = $this->data->get_nbre_by_profil($profil);
        $this->table[] = Array("profil" => $profil, "nombre" => $nombre);
      }
      $this->vue->LoadTemplate('nettoyage.php');
      $this->vue->MergeBlock('b1', $this->table);
      $this->vue->show();
    } else {
      $this->setVarGlobal('choix_info', 'choix_profil');
      $this->setVarGlobal('message', 'Vous devez choisir au moins un profil');
      $this->vue->LoadTemplate('nettoyage.php');
      $this->vue->show();
    }
  }

  function supp_profil() {
    $this->setVarGlobal('choix_info', 'resultat_profil');
    foreach ($_SESSION['choix_profil'] as $profil) {
      $nombre = $this->data->del_by_profil($profil);
      $this->table[] = Array("profil" => $profil, "nombre" => $nombre);
    }
    $this->vue->LoadTemplate('nettoyage.php');
    $this->vue->MergeBlock('b1', $this->table);
    $this->vue->show();
  }

  function choix_classe() {
    $this->setVarGlobal('choix_info', 'avertissement_classe');
    $_SESSION['choix_profil'] = isset($_POST['choix_profil']) ? $_POST['choix_profil'] : Null;
    $_SESSION['choix_classe'] = isset($_POST['choix_classe']) ? $_POST['choix_classe'] : Null;
    if ($_SESSION['choix_profil'] && $_SESSION['choix_classe']) {
      foreach ($_SESSION['choix_classe'] as $classe) {
        foreach ($_SESSION['choix_profil'] as $profil) {
          $nombre = $this->data->get_nbre_by_classe_profil($classe, $profil);
          $this->table[] = Array("classe" => get_class_from_id($classe), "profil" => $profil, "nombre" => $nombre);
        }
      }
      $this->vue->LoadTemplate('nettoyage.php');
      $this->vue->MergeBlock('b1', $this->table);
      $this->vue->show();
    } else {
      if (!$_SESSION['choix_profil'] && !$_SESSION['choix_classe']) {
        $message = "Vous devez choisir au moins une classe et un profil.";
      } elseif (!isset($_SESSION['choix_profil'])) {
        $message = "Vous devez choisir au moins un profil en plus des classes.";
      } else {
        $message = "Vous devez choisir au moins une classe en plus des profils.";
      }
      $this->setVarGlobal('choix_info', 'choix_classe');
      $this->setVarGlobal('message', $message);
      $this->get_classes();
      $this->vue->LoadTemplate('nettoyage.php');
      $this->vue->MergeBlock('b1', $this->table);
      $this->vue->show();
    }
  }

  function supp_classe() {
    $this->setVarGlobal('choix_info', 'resultat_classe');
    foreach ($_SESSION['choix_classe'] as $classe) {
      foreach ($_SESSION['choix_profil'] as $profil) {
        $nombre = $this->data->del_by_classe_profil($classe, $profil);
        $this->table[] = Array("classe" => get_class_from_id($classe), "profil" => $profil, "nombre" => $nombre);
      }
    }
    $this->vue->LoadTemplate('nettoyage.php');
    $this->vue->MergeBlock('b1', $this->table);
    $this->vue->show();
  }

  function get_message($code) {
    //$NomBloc   : nom du bloc qui appel la fonction (lecture seule)
    //$CurrRec   : tableau contenant les champs de l'enregistrement en cours (lecture/écriture)
    //$RecNum    : numéro de l'enregsitrement en cours (lecture seule)
    switch ($code) {
      case 0:
        $this->class = "message_red";
        $this->message = 'La suppression ne semble pas avoir réussie';
        break;
      case 1:
        $this->class = "message_green";
        $this->message = 'La suppression a réussie';
        break;
    }
    return array($this->class, $this->message);
  }

  private function get_classes() {
    $res = $this->data->get_infos_classes();
    while ($this->row = mysql_fetch_array($res)) {
      $this->table[] = Array('id' => $this->row['id'], 'classe' => $this->row['classe'], 'nom_complet' => $this->row['nom_complet']);
    }
  }

}

?>