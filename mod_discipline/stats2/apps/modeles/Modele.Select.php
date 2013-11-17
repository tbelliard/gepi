<?php
/*
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
// On empêche l'accès direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};

require_once("Class.Date.php");
require_once('Class.Modele.php');
Class modele_select extends Modele {


  public function get_eleves_classe($id) {
    return($this->get_db_eleves_classe($id));
  }
  private function get_db_eleves_classe($id) {
    $this->sql='SELECT DISTINCT login from j_eleves_classes
                   WHERE id_classe='.$id;
    $this->res=mysqli_query($GLOBALS["mysqli"], $this->sql);
    if (isset($this->liste)) unset ($this->liste);
    while($this->row=mysqli_fetch_array($this->res)) {
      $this->liste[].=$this->row[0];
    }
    return($this->liste);
  }

  public function test_edt() {
    $this->sql = " SELECT 1=1 FROM setting WHERE name ='autorise_edt_admin' AND value='y'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->sql);
    if(mysqli_num_rows($this->res)>0) return true;
  }
  public function get_db_periodes_calendrier() {
    $this->sql = "SELECT id_calendrier,classe_concerne_calendrier,nom_calendrier,jourdebut_calendrier,jourfin_calendrier
		        FROM edt_calendrier WHERE etabvacances_calendrier=0 AND etabferme_calendrier=1
                        ORDER BY jourdebut_calendrier ASC ";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->sql);
    return($this->periodes_calendrier=parent::set_array('assoc',$this->res));
  }

  public function get_classes_periode() {
        $liste = false;
        if (isset($_SESSION['stats_periodes']['periode'])) {
            foreach ($this->periodes_calendrier as $value) {
                if ($value['id_calendrier'] == $_SESSION['stats_periodes']['periode']) {
                    if ($value['classe_concerne_calendrier'] != '') {
                        $liste = trim($value['classe_concerne_calendrier'], ";");
                        $liste = explode(';', $liste);
                    }
                }
            }
        }
        if ($liste) {
            $this->sql = "SELECT id, classe, nom_complet FROM classes WHERE id IN (" . implode(',', $liste) . " ) ORDER BY classe ASC ";
            $this->res = mysqli_query($GLOBALS["mysqli"], $this->sql);
            $this->classes = parent::set_array('object', $this->res);
        } else {
            $this->sql = "SELECT id, classe, nom_complet FROM classes ORDER BY classe ASC ";
            $this->res = mysqli_query($GLOBALS["mysqli"], $this->sql);
            $this->classes = parent::set_array('object', $this->res);
        }
        return($this->classes);
    }
  public function get_infos_classe($id) {
    $this->sql = 'SELECT id, classe, nom_complet FROM classes where id='.$id;
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->sql);    
    return ($this->noms_classe=parent::set_array('array',$this->res));
  }


  public function get_db_individu_identite($ident,$statut) {
    switch ($statut) {
      case'eleves': {
          $this->sql = "SELECT e.login,e.nom,e.prenom,c.classe  FROM eleves e,classes c,j_eleves_classes jec
				WHERE e.login='$ident'
				AND e.login=jec.login AND jec.id_classe=c.id
				GROUP BY e.login";
          $this->res = mysqli_query($GLOBALS["mysqli"], $this->sql);
          if (mysqli_num_rows($this->res) > 0) {
                        while ($this->row = mysqli_fetch_assoc($this->res)) {
                            $this->individu_identite = $this->row;
                        }
                    } else {
                        $this->individu_identite = Array("login" => $ident, "nom" => "eleve inconnu(login:$ident)", "prenom" => "inconnu", "classe" => "classe inconnue");
                    }
          break;
        }
      case'personnels': {
          $this->sql = "SELECT login,nom,prenom,statut FROM utilisateurs
				WHERE login='$ident'
				AND (statut='professeur' OR statut='CPE' OR statut='AUTRE'
                                OR statut='SCOLARITE' OR statut='Administrateur')";
          $this->res = mysqli_query($GLOBALS["mysqli"], $this->sql);
          if (mysqli_num_rows($this->res) > 0) {
                        while ($this->row = mysqli_fetch_assoc($this->res)) {
                            $this->individu_identite = $this->row;
                        }
                    } else {
                        $this->individu_identite = Array("login" => $ident, "nom" => "personnel inconnu(login:$ident)", "prenom" => "inconnu", "statut" => "statut inconnu");
                    }

                    break;
        }
    }
    return($this->individu_identite) ;
  }

  public function get_id_from_classe($classe){
    $this->sql = "SELECT id FROM classes where classe ='".$classe."'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->sql);
    $id=parent::set_array('array',$this->res);
    return($id[0]['id']);
  }
}
?>
