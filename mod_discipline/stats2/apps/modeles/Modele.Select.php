<?php
/*
 * $Id$
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
require_once("Class.Date.php");
require_once('Class.Modele.php');
Class modele_select extends Modele {

    public function test_edt(){
      $this->sql = " SELECT 1=1 FROM setting WHERE name ='autorise_edt_admin' AND value='y'";
      $this->res = mysql_query($this->sql);
      if(mysql_num_rows($this->res)>0) return true;
    }
    public function get_db_periodes_calendrier() {
        $this->sql = "SELECT id_calendrier,classe_concerne_calendrier,nom_calendrier,jourdebut_calendrier,jourfin_calendrier
		        FROM edt_calendrier WHERE etabvacances_calendrier=0 AND etabferme_calendrier=1
                        ORDER BY jourdebut_calendrier ASC ";
        $this->res = mysql_query($this->sql);
        return($this->periodes_calendrier=parent::set_array('assoc',$this->res));
    }

    public function get_classes_periode() {

        if (isset($_SESSION['stats_periodes']['periode'])) {
            foreach ($this->periodes_calendrier as $value) {
                if ($value['id_calendrier']==$_SESSION['stats_periodes']['periode']) {
                    $liste=trim($value['classe_concerne_calendrier'],";");
                    $liste=explode(';',$liste);
                }
            }
            $this->sql = "SELECT id, classe, nom_complet FROM classes WHERE id IN (".implode(',', $liste)." ) ORDER BY classe ASC ";
            $this->res = mysql_query($this->sql);
            $this->classes=parent::set_array('object',$this->res);

        }else {
            $this->sql = "SELECT id, classe, nom_complet FROM classes ORDER BY classe ASC ";
            $this->res = mysql_query($this->sql);
            $this->classes=parent::set_array('object',$this->res);

        }
        return($this->classes);
    }
    public function get_infos_classe($id) {
        $this->sql = 'SELECT id, classe, nom_complet FROM classes where id='.$id;
        $this->res = mysql_query($this->sql);

        return ($this->noms_classe=parent::set_array('array',$this->res));
    }


    public function get_db_individu_identite($ident,$statut) {
        switch ($statut) {
            case'eleves': {
                    $this->sql = "SELECT e.login,e.nom,e.prenom,c.classe  FROM eleves e,classes c,j_eleves_classes jec
				WHERE e.login='$ident'
				AND e.login=jec.login AND jec.id_classe=c.id
				GROUP BY e.login";
                    $this->res = mysql_query($this->sql);
                    while($this->row=mysql_fetch_assoc($this->res)){
                        $this->individu_identite=$this->row;
                    }
                    break;
                }
            case'personnels': {
                    $this->sql = "SELECT login,nom,prenom,statut FROM utilisateurs
				WHERE login='$ident'
				AND (statut='professeur' OR statut='CPE' OR statut='AUTRE'
                                OR statut='SCOLARITE' OR statut='Administrateur')";
                    $this->res = mysql_query($this->sql);
                    while($this->row=mysql_fetch_assoc($this->res)){
                        $this->individu_identite=$this->row;
                    }       
                    break;
                }
        }
        return($this->individu_identite) ;
    }
}
?>
