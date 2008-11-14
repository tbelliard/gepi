<?php
/**
 *
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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


function ListeEleves($reglage){
  if (!is_array($reglage)) {
    die('<p style="color: red;">Il manque des informations pour afficher la liste des élèves.</p>');
  }else{
    if ($reglage["classes"] == 'toutes') {
      // On affiche la liste de tous les élèves de toutes les classes
      $sql = "SELECT DISTINCT nom, prenom, id_eleve, classe FROM eleves e, j_eleves_classes jec, classes c";
      $sql .= " WHERE jec.login = e.login AND jec.id_classe = c.id";
      if ($reglage["eleves"] == 'alpha') {
        // On les range par ordre alphabétique général
        $sql .= " ORDER BY e.nom, e.prenom";

      }elseif($reglage["eleves"] == 'classe'){
        // On les range par classe et par ordre alpha à l'intérieur de celles-ci
        $sql .= " ORDER BY c.classe, e.nom, e.prenom";

      }

    }elseif($reglage["classes"] == 'cpe'){
      // On n'affiche que les classes du cpe en question
      $sql = "";
    }elseif(is_numeric($reglage["classes"]) OR strpos($reglage["classes"], "AID")){
      // On affiche que la liste des élèves de ce groupe ou de cet AID
      $test = explode("|", $reglage["classes"]);
      if ($test[0] == 'AID') {
        // On a affaire à une AID, il faut donc appeler la liste des élèves de celle-ci
        $sql = "SELECT";

      }else{
        // Il s'agit donc d'un groupe
        $sql  = "SELECT DISTINCT nom, prenom, id_eleve FROM eleves e, j_eleves_groupes jeg";
        $sql .= " WHERE jeg.login = e.login AND jeg.id_groupe = '" . $reglage["classes"] . "'";
        $sql .= " ORDER BY e.nom, e.prenom";

      }

    }else{
      throw new Exception('Un mauvais réglage dans la requête empêche de pouvoir lister les élèves.||' . $sql);
    }
  }

    if ($query = $GLOBALS["cnx"]->query($sql)) {
      $eleves = $query->fetchAll(PDO::FETCH_OBJ);
    }else{
      throw new Exception('Une erreur dans la requête empêche de pouvoir lister les élèves.||' . $sql);
    }

    return $eleves;

}

function affSelectEleves($liste_eleves, $options = NULL){
  if (!is_array($liste_eleves)) {
    die('<p style="color: red;">Il manque des informations pour afficher la liste des élèves.</p>');
  }else{
    // On peut décider du lieu d'affichage de la classe dans le select
    $id = 'listeIdEleve';
    $aff_classe = isset($options["classe"]) ? $options["classe"] : 'fin';
    $aff_label = isset($options["label"]) ? '<label for="listeIdEleve">'.$options["label"].'</label>' : '';
    $method_event = isset($options["method_event"]) ? $options["method_event"]."('aff_result', '')" : '';
    $aff_event = isset($options["event"]) ? ' on'.$options["event"].'="'.$method_event.'"' : '';
    $retour =
    $aff_label . '
    <select name="choix_eleve" id="listeIdEleve"' . $aff_event . '>';

    $nbre = count($liste_eleves);
    if ($nbre === 0) {
      $retour .= '
      <option value="r">Pas d\'élève dans la base</option>';
    }else{

      for($a = 0 ; $a < $nbre ; $a++){
        $classe_fin = (isset($liste_eleves[$a]->classe) AND $aff_classe == 'fin') ? '  '.$liste_eleves[$a]->classe : '';
        $classe_debut = (isset($liste_eleves[$a]->classe) AND $aff_classe == 'debut') ? $liste_eleves[$a]->classe.'&nbsp;&nbsp;' : '';
        $retour .= '
        <option value="' . $liste_eleves[$a]->id_eleve . '">' . $classe_debut . $liste_eleves[$a]->nom . ' ' . $liste_eleves[$a]->prenom . $classe_fin . '</option>
        ';
      }

    }

    $retour .= '
    </select>';

  }
  return $retour;
}

function donneesFicheEleve($_eleves_id){

    if (is_integer($_eleves_id)) {
      // on utilise le eleves_id de la table eleves pour retouver les infos sur ses responsables
      $sql_eleve = "SELECT ele_id FROM eleves WHERE id_eleve = " . $_eleves_id . "";
      if ($query = $GLOBALS["cnx"]->query($sql_eleve)) {
        $eleve = $query->fetchAll(PDO::FETCH_OBJ);
      }else{
        throw new Exception('Cet élève n\'a pas d\'ele_id pour retrouver ses responsables dans la base.||' . $sql_eleve);
      }
      $nbre_rep = count($eleve);
      if ($nbre_rep == 1) {
        $sql = "SELECT * FROM resp_pers rp, responsables2 r
                        WHERE r.ele_id = '".$eleve[0]->ele_id."' AND r.pers_id = rp.pers_id
                        ORDER BY resp_legal ASC";
      }elseif($nbre_rep > 1){
        throw new Exception('Cet élève a plusieurs ele_id et donc plusieurs entrées dans la table élèves.||aucune');
      }else{
        throw new Exception('Cet élève n\'a pas d\'ele_id dans la table eleves.||' . $nbre_rep . ' + ' . $sql_eleve);
      }
      if ($query2 = $GLOBALS["cnx"]->query($sql)) {
        $donnees  = $query2->fetchAll(PDO::FETCH_OBJ);
      }else{
        throw new Exception('Cet élève n\'est rattaché à aucun responsable.||' . $sql);
      }

    }elseif(is_string($_eleves_id)){
      // On utilise le login de la table eleves pour retrouver les infos sur ses responsables
    }
    return $donnees;
  }

function affSelectClasses(){

}

function affSelectAid(){

}

function affSelectEnseignements(){

}
?>