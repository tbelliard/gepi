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
      $sql  = "SELECT DISTINCT nom, prenom, id_eleve, classe FROM eleves e, j_eleves_classes jec, classes c";
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
      $sql  = "SELECT DISTINCT nom, prenom, id_eleve, classe FROM eleves e, j_eleves_classes jec, classes c, j_eleves_cpe jep";
      $sql .= " WHERE jec.login = e.login AND jec.id_classe = c.id";
      $sql .= " AND jep.e_login = e.login AND jep.cpe_login = '" . $_SESSION["login"] . "'";
      $sql .= " ORDER BY e.nom, e.prenom";

    }elseif(is_numeric($reglage["classes"])){
      // On affiche que la liste des élèves de ce groupe

      $sql  = "SELECT DISTINCT nom, prenom, id_eleve FROM eleves e, j_eleves_groupes jeg";
      $sql .= " WHERE jeg.login = e.login AND jeg.id_groupe = '" . $reglage["classes"] . "'";
      $sql .= " ORDER BY e.nom, e.prenom";

    }elseif(substr($reglage["classes"], 0, 3) == 'AID'){
      // On affiche que la liste des élèves de cet AID
      $test = explode("|", $reglage["classes"]);
      // On a affaire à une AID, il faut donc appeler la liste des élèves de celle-ci
      $sql  = "SELECT DISTINCT nom, prenom, id_eleve FROM eleves e, j_aid_eleves jae";
      $sql .= " WHERE jae.login = e.login AND jae.id_aid = '" . $test[1] . "'";
      $sql .= " ORDER BY e.nom, e.prenom";

    }elseif(substr($reglage["classes"], 0, 3) == 'CLA'){
      $test = explode("|", $reglage["classes"]);
      $sql  = "SELECT DISTINCT nom, prenom, id_eleve FROM eleves e, j_eleves_classes jac";
      $sql .= " WHERE jac.login = e.login AND jac.id_classe = '" . $test[1] . "'";
      $sql .= " ORDER BY e.nom, e.prenom";

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

function infosResponsables($ele_id) {

	$sql = "SELECT * FROM responsables2 r, resp_pers rp
													LEFT JOIN  resp_adr ra ON rp.adr_id=ra.adr_id
													WHERE ( r.ele_id = '".$ele_id."' AND r.pers_id = rp.pers_id )
													ORDER BY resp_legal DESC, nom, prenom";
	if ($query = $GLOBALS["cnx"]->query($sql)) {
    $responsables = $query->fetchAll(PDO::FETCH_OBJ);
  }else{
    throw new Exception('Une erreur dans la requête empêche de voir les informations des reponsables de cet élève.||' . $sql);
  }

    return $responsables;
}

function infosEleve($_id_eleve){
  // On récupère les informations de cet élève
  $eleve = array();
  $sql  = "SELECT DISTINCT nom, prenom, id_eleve, sexe, naissance, classe, ele_id FROM eleves e, j_eleves_classes jec, classes c";
  $sql .= " WHERE e.login = jec.login AND jec.id_classe = c.id AND e.id_eleve = '".$_id_eleve."'";

  if ($query = $GLOBALS["cnx"]->query($sql)) {
    $eleve = $query->fetchAll(PDO::FETCH_OBJ);
  }else{
    throw new Exception('Une erreur dans la requête empêche de pouvoir lister les élèves.||' . $sql);
  }
  $eleve[0]->fiche_eleve = 'ok'; // pour pouvoir afficher la fiche de l'élève

  // Les responsables
  $responsables = infosResponsables($eleve[0]->ele_id);
  $eleve[0]->responsables = $responsables;

  return $eleve;
}

function listeCreneaux($options = NULL){
  $sql = "SELECT ";
}

function AffSelectParametres($options){
  /**
  * Permet de lister les motifs, les types, les justifications et les actions
  * $options["_type"] définit lequel des quatre on renvoie :
  * ATTENTION : doit être types / motifs / justifications / actions et c'est tout
  * A besoin du fichier /classes/abs_gestion.class.php pour charger la classe abs_gestion
  */
  if(!isset($options["_type"])){return false;}

  $aff_name = isset($options["name"]) ? $options["name"] : 'liste' . $options["_type"];

  $param = new abs_gestion();
  $param->setTable('abs_' . $options["_type"]); // On définit la bonne table
  $donnees = $param->voirTout();
  $champ = 'type_' . substr($options["_type"], 0, (strlen($options["_type"]) - 1)); // On définit le champ de cette table

  $retour = '
    <select name="' . $aff_name . '">
  ';
  foreach($donnees as $aff_param):

   $retour .= '
    <option value="' . $aff_param->id . '">' . $aff_param->$champ . '</option>
   ';

   endforeach;

  $retour .= '
    </select>
  ';

  return $retour;
}

function affSelectEleves($liste_eleves, $options = NULL){
  if (!is_array($liste_eleves)) {
    die('<p style="color: red;">Il manque des informations pour afficher la liste des élèves.</p>');
  }else{
    // On peut décider du lieu d'affichage de la classe dans le select
    $aff_classe = isset($options["classe"]) ? $options["classe"] : 'fin';
    $_id = isset($options["id"]) ? ' id="' . $options["id"] . '"' : 'listeIdEleve';
    $aff_label = isset($options["label"]) ? '<label for="' . $_id . '">'.$options["label"].'</label>' : '';
    $_url = isset($options["url"]) ? $options["url"] : NULL;
    $method_event = isset($options["method_event"]) ? $options["method_event"]."('aff_result', '".$_id."', '".$_url."')" : '';
    $aff_event = isset($options["event"]) ? ' on'.$options["event"].'="'.$method_event.'"' : '';
    $aff_multiple = (isset($options["multiple"]) AND $options["multiple"] == 'on') ? ' multiple="multiple"' : NULL;
    $aff_multiple_name = (isset($options["multiple"]) AND $options["multiple"] == 'on') ? '[]' : NULL;
    $aff_size = isset($options["size"]) ? ' size="'.$options["size"].'"' : NULL;


    $retour =
    $aff_label . '
    <select name="choix_eleve'.$aff_multiple_name.'" id="' . $_id . '"' . $aff_event . $aff_multiple . $aff_size . '>
      <option value="r">-- -- -- --</option>';

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

function affSelectClasses($options = NULL){
  if (!isset($options["classes"])) {
    // On affiche alors par défaut toutes les classes
    $sql = "SELECT id, classe, nom_complet FROM classes ORDER BY nom_complet";

  }elseif($options["classes"] == 'cpe'){
    // Il faut tirer la bonne requête ici pour ne garder que les classes de ce cpe
  }
  if ($query = $GLOBALS["cnx"]->query($sql)) {
    $donnees = $query->fetchAll(PDO::FETCH_OBJ);
  }else{
    throw new Exception('Aucune classe n\'est disponible.||' . $sql);
  }
  $nbre = count($donnees);
  $_id = isset($options["id"]) ? ' id="' . $options["id"] . '"' : 'listeIdClasses';
  $aff_label = isset($options["label"]) ? '<label for="' . $_id . '">' . $options["label"] . '</label>' : '';
  $_width = isset($options["width"]) ? ' style="width: ' . $options["width"] . ';"' : '';
  $_url = isset($options["url"]) ? $options["url"] : NULL;
  $method_event = isset($options["method_event"]) ? $options["method_event"]."('aff_result', '".$_id."', '".$_url."')" : '';
  $aff_event = isset($options["event"]) ? ' on'.$options["event"].'="'.$method_event.'"' : '';

  $retour =
  $aff_label . '
  <select name="choix_classe" id="' . $_id . '"' . $_width . $aff_event .'>
    <option value="r">-- -- -- --</option>
  ';
  for ($a = 0 ; $a < $nbre ; $a++){
    $retour .= '
    <option value="' . $donnees[$a]->id . '">' . $donnees[$a]->classe . '</option>';
  }
  $retour .= '
  </select>
  ';
  return $retour;
}

function affSelectAid($options = NULL){

  if (!isset($options["aid"])) {
    // On affiche alors tous les AID
    $sql = "SELECT id, nom FROM aid ORDER BY indice_aid, nom";
  }
  if ($query = $GLOBALS["cnx"]->query($sql)) {
    $donnees = $query->fetchAll(PDO::FETCH_OBJ);
  }else{
    throw new Exception('Aucune classe n\'est disponible.||' . $sql);
  }
  $nbre = count($donnees);
  $_id = isset($options["id"]) ? ' id="' . $options["id"] . '"' : 'listeIdAid';
  $aff_label = isset($options["label"]) ? '<label for="' . $_id . '">' . $options["label"] . '</label>' : '';
  $_width = isset($options["width"]) ? ' style="width: ' . $options["width"] . ';"' : '';
  $_url = isset($options["url"]) ? $options["url"] : NULL;
  $method_event = isset($options["method_event"]) ? $options["method_event"]."('aff_result', '".$_id."', '".$_url."')" : '';
  $aff_event = isset($options["event"]) ? ' on'.$options["event"].'="'.$method_event.'"' : '';

  $retour =
  $aff_label . '
  <select name="choix_classe" id="' . $_id . '"' . $_width . $aff_event .'>
    <option value="r">-- -- -- --</option>
  ';
  for ($a = 0 ; $a < $nbre ; $a++){
    $retour .= '
    <option value="' . $donnees[$a]->id . '">' . $donnees[$a]->nom . '</option>';
  }
  $retour .= '
  </select>
  ';

  return $retour;
}

function affSelectEnseignements($options = NULL){

  if (!isset($options["groupes"])) {
    // On affiche alors tous les groupes
/* ======== Il faut ajouter le nom de la classe à côté =====================*/
    $sql = "SELECT g.id, g.description, c.classe FROM groupes g, j_groupes_classes jgc, classes c
                                                WHERE g.id = jgc.id_groupe
                                                AND jgc.id_classe = c.id
                                                ORDER BY name";
  }

  if ($query = $GLOBALS["cnx"]->query($sql)) {
    $donnees = $query->fetchAll(PDO::FETCH_OBJ);
  }else{
    throw new Exception('Impossible de lister les enseignements.||' . $sql);
  }
  $nbre = count($donnees);
  $_id = isset($options["id"]) ? ' id="' . $options["id"] . '"' : 'listeIdGroupes';
  $aff_label = isset($options["label"]) ? '<label for="' . $_id . '">' . $options["label"] . '</label>' : '';
  $_width = isset($options["width"]) ? ' style="width: ' . $options["width"] . ';"' : '';
  $_url = isset($options["url"]) ? $options["url"] : NULL;
  $method_event = isset($options["method_event"]) ? $options["method_event"]."('aff_result', '".$_id."', '".$_url."')" : '';
  $aff_event = isset($options["event"]) ? ' on'.$options["event"].'="'.$method_event.'"' : '';

  $retour =
  $aff_label . '
  <select name="choix_groupe" id="' . $_id . '"' . $_width . $aff_event .'>
    <option value="r">-- -- -- --</option>
  ';

  for($a = 0 ; $a < $nbre ; $a++){
    $retour .= '
    <option value="' . $donnees[$a]->id . '">' . $donnees[$a]->description . ' ( ' . $donnees[$a]->classe . ') </option>';
  }

  $retour .= '
  </select>
  ';

  return $retour;
}
?>