<?php
/**
 *
 *
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

/**
 * Classe de helpers sur les types, motifs, justifications et actions des absences
 */
class AbsencesParametresHelper {

  /**
   * Afficher un tag HTML pour la liste des types d'absence (select)
   * Les options possibles : id pour l'identifiant du select, name pour son name et selected pour vérifier le quel doit apparaitre en selected
   *
   * @param array $options id, name, selected
   * @return string Liste deroulante des types d'absences
   */
  public static function AfficherListeDeroulanteTypes($options){
    $criteria = new Criteria();
    $criteria->addAscendingOrderByColumn(AbsenceTypePeer::ORDRE);
    $liste = AbsenceTypePeer::doSelect($criteria);

    $_id        = isset($options["id"]) ? $options["id"] : 'idTypes';
    $_name      = isset($options["name"]) ? $options["name"] : 'nametypes';
    $_selected  = isset($options["selected"]) ? $options["selected"] : '';
    $_class     = isset($options["class"]) ? ' class="'.$options["class"].'"' : '';

    $retour = '<select id="'.$_id.'" name="'.$_name.'"'.$_class.'>'."\n";
    $retour .= '<option value="0">---</option>'."\n";
    foreach($liste as $type){
      $aff_selected = '';
      if ($type->getId() == $_selected){
        $aff_selected = ' selected="selected"';
      }
      $retour .= '<option value="'.$type->getId().'"'.$aff_selected.'>'.$type->getNom().'</option>'."\n";
    }
    $retour .= '</select>'."\n";

    return $retour;
  }

  /**
   * Afficher un tag HTML pour la liste des motifs d'absence (select)
   * Les options possibles : id pour l'identifiant du select, name pour son name et selected pour vérifier le quel doit apparaitre en selected
   *
   * @param array $options id, name, selected
   * @return string Liste deroulante des motifs d'absences
   */
  public static function AfficherListeDeroulanteMotifs($options){
    $criteria = new Criteria();
    $criteria->addAscendingOrderByColumn(AbsenceMotifPeer::ORDRE);
    $liste = AbsenceMotifPeer::doSelect($criteria);

    $_id        = isset($options["id"]) ? $options["id"] : 'idMotifs';
    $_name      = isset($options["name"]) ? $options["name"] : 'namemotifs';
    $_selected  = isset($options["selected"]) ? $options["selected"] : '';
    $_class     = isset($options["class"]) ? ' class="'.$options["class"].'"' : '';

    $retour = '<select id="'.$_id.'" name="'.$_name.'"'.$_class.'>'."\n";
    $retour .= '<option value="0">---</option>'."\n";
    foreach($liste as $type){
      $aff_selected = '';
      if ($type->getId() == $_selected){
        $aff_selected = ' selected="selected"';
      }
      $retour .= '<option value="'.$type->getId().'"'.$aff_selected.'>'.$type->getNom().'</option>'."\n";
    }
    $retour .= '</select>'."\n";

    return $retour;
  }

  /**
   * Afficher un tag HTML pour la liste des justifications des absences (select)
   * Les options possibles : id pour l'identifiant du select, name pour son name et selected pour vérifier le quel doit apparaitre en selected
   *
   * @param array $options id, name, selected
   * @return string Liste deroulante des justifications d'absences
   */
  public static function AfficherListeDeroulanteJustifications($options){
    $criteria = new Criteria();
    $criteria->addAscendingOrderByColumn(AbsenceJustificationPeer::ORDRE);
    $liste = AbsenceJustificationPeer::doSelect($criteria);

    $_id        = isset($options["id"]) ? $options["id"] : 'idJustif';
    $_name      = isset($options["name"]) ? $options["name"] : 'namejustif';
    $_selected  = isset($options["selected"]) ? $options["selected"] : '';
    $_class     = isset($options["class"]) ? ' class="'.$options["class"].'"' : '';

    $retour = '<select id="'.$_id.'" name="'.$_name.'"'.$_class.'>'."\n";
    $retour .= '<option value="0">---</option>'."\n";
    foreach($liste as $type){
      $aff_selected = '';
      if ($type->getId() == $_selected){
        $aff_selected = ' selected="selected"';
      }
      $retour .= '<option value="'.$type->getId().'"'.$aff_selected.'>'.$type->getNom().'</option>'."\n";
    }
    $retour .= '</select>'."\n";

    return $retour;
  }

  /**
   * Afficher un tag HTML pour la liste des actions des absences (select)
   * Les options possibles : id pour l'identifiant du select, name pour son name et selected pour vérifier le quel doit apparaitre en selected
   *
   * @param array $options id, name, selected
   * @return string Liste deroulante des justifications d'absences
   */
  public static function AfficherListeDeroulanteActions($options){
    $criteria = new Criteria();
    $criteria->addAscendingOrderByColumn(AbsenceActionPeer::ORDRE);
    $liste = AbsenceActionPeer::doSelect($criteria);

    $_id        = isset($options["id"]) ? $options["id"] : 'idAction';
    $_name      = isset($options["name"]) ? $options["name"] : 'nameaction';
    $_selected  = isset($options["selected"]) ? $options["selected"] : '';
    $_class     = isset($options["class"]) ? ' class="'.$options["class"].'"' : '';

    $retour = '<select id="'.$_id.'" name="'.$_name.'"'.$_class.'>'."\n";
    $retour .= '<option value="0">---</option>'."\n";
    foreach($liste as $type){
      $aff_selected = '';
      if ($type->getId() == $_selected){
        $aff_selected = ' selected="selected"';
      }
      $retour .= '<option value="'.$type->getId().'"'.$aff_selected.'>'.$type->getNom().'</option>'."\n";
    }
    $retour .= '</select>'."\n";

    return $retour;
  }
}
?>
