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
      if ($reglage["eleves"] == 'alpha') {
        // On les range par ordre alphabétique général
        $sql = "SELECT nom, prenom, id_eleve FROM eleves ORDER BY nom, prenom";
      }elseif($reglage["eleves"] == 'classe'){
        // On les range par classe et par ordre alpha à l'intérieur de celles-ci
        $sql = "SELECT DISTINCT nom, prenom, id_eleve, classe FROM eleves e, j_eleves_classes jec, classes c";
        $sql .= " WHERE jec.login = e.login AND jec.id_classe = c.id";
        $sql .= " ORDER BY c.classe, e.nom, e.prenom";
      }
    }elseif($reglage["classes"] == 'cpe'){
      // On n'affiche que les classes du cpe en question
      $sql = "";
    }elseif(is_numeric($reglage["classes"])){
      // On affiche que la liste des élèves de cette classe


    }else{
      Die('<p>Une erreur dans la requête empêche de pouvoir lister les élèves</p>' . $sql);
    }
  }

    $query = $GLOBALS["cnx"]->query($sql);
    $eleves = $query->fetchAll(PDO::FETCH_OBJ);

    return $eleves;

}

function affListeEleves($liste_eleves){
  if (!is_array($liste_eleves)) {
    die('<p style="color: red;">Il manque des informations pour afficher la liste des élèves.</p>');
  }else{

  }
}

function affSelectEleves($liste_eleves){
  if (!is_array($liste_eleves)) {
    die('<p style="color: red;">Il manque des informations pour afficher la liste des élèves.</p>');
  }else{
    $retour = '
    <select name="choix_eleve">';

    $nbre = count($liste_eleves);
    if ($nbre === 0) {
      $retour = '
      <option value="r">Pas d\'élève dans la base</option>';
    }else{

      for($a = 0 ; $a < $nbre ; $a++){
        $classe = isset($liste_eleves[$a]->classe) ? $liste_eleves[$a]->classe : '';
        $retour .= '
        <option value="' . $liste_eleves[$a]->id_eleve . '">' . $liste_eleves[$a]->nom . ' ' . $liste_eleves[$a]->prenom . ' ' . $classe . '</option>
        ';
      }

    }

    $retour .= '
    </select>';

  }
  return $retour;
}

?>