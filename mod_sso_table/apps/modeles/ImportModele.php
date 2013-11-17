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

class ImportModele {

  function get_error($login_gepi, $login_sso, $ecriture) {
    if ($login_gepi == "" || $login_sso == "") {
      return 5;
    }
    if ($this->verif_exist_login_gepi($login_gepi)) {
      return 0;
    }
    if ($this->verif_exist_login_sso($login_sso)) {
      return 1;
    }
    if (!$this->test_utilisateur($login_gepi)) {
      return 2;
    }
    if (!$this->test_auth_mode($login_gepi)) {
      if ($ecriture) {
        $this->insert_table_sso($login_gepi, $login_sso);
      }
      return 3;
    } else {
      if ($ecriture) {
        $this->insert_table_sso($login_gepi, $login_sso);
      }
      return 4;
    }
  }

  function search($nom) {
    $this->table = '';
    $this->req = "SELECT nom,prenom,login,statut FROM utilisateurs
                WHERE nom LIKE '%" . $_POST['nom'] . "%'
                AND auth_mode='sso'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    while ($this->row = mysqli_fetch_array($this->res,  MYSQLI_NUM)) {
      $this->table[] = array('nom' => $this->row[0], 'prenom' => $this->row[1], 'login_gepi' => $this->row[2], 'statut' => $this->row[3]);
    }
    return($this->table);
  }

  function get_login_sso_table_sso($login) {
    $this->req = "SELECT login_sso FROM sso_table_correspondance WHERE login_gepi='" . $login . "'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    $this->q = mysqli_fetch_array($this->res);
    if ($this->q)
      return $this->q[0];
  }

  function maj_sso_table($login_gepi, $login_sso, $mode) {
    switch ($mode) {
      case 'insert':
        $this->insert_table_sso($login_gepi, $login_sso);
        break;
      case 'update':
        $this->update_table_sso($login_gepi, $login_sso);
        break;
    }
  }

  function get_nbre_entrees() {
    $this->req = "SELECT COUNT(*) FROM sso_table_correspondance ";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    $resultat = (mysqli_fetch_row($this->res));
    return $resultat[0];
  }

  function vide_table() {
    $this->req = "DELETE FROM sso_table_correspondance WHERE 1>0";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return mysqli_affected_rows($GLOBALS["mysqli"]);
  }

  function get_anciens_comptes() {
    if ($this->get_logins_gepi_table()) {
      $logins_gepi_table = $this->get_logins_gepi_table();
      $logins_gepi_utilisateurs = $this->get_logins_gepi_utilisateurs();
      return($logins_a_supprimer = array_diff($logins_gepi_table, $logins_gepi_utilisateurs));
    } else {
      return false;
    }
  }

  function get_logins_gepi_table() {
    $this->req = "SELECT login_gepi FROM sso_table_correspondance";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    while ($this->q = mysqli_fetch_assoc($this->res)) {
      $logins[] = $this->q['login_gepi'];
    }
    if (isset($logins)) {
      return($logins);
    } else {
      return false;
    }
  }

  function get_nbre_by_profil($profil) {
    $this->req = "SELECT COUNT(*) FROM utilisateurs u,sso_table_correspondance p WHERE p.login_gepi=u.login AND statut='" . $profil . "'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return(mysql_result($this->res, 0));
  }

  function del_by_profil($profil) {
    $this->req = "DELETE p FROM sso_table_correspondance p  INNER JOIN utilisateurs u ON p.login_gepi=u.login WHERE statut='" . $profil . "'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return(mysqli_affected_rows($GLOBALS["mysqli"]));
  }

  function get_nbre_by_classe_profil($classe, $profil) {
    if ($profil == "Eleve") {
      $this->req = "SELECT COUNT(DISTINCT login_gepi) FROM sso_table_correspondance p
                   INNER JOIN utilisateurs u ON p.login_gepi=u.login
                   INNER JOIN j_eleves_classes jec ON (u.login=jec.login AND jec.id_classe='" . $classe . "')
                   WHERE u.statut='" . $profil . "'";
    } else {
      $this->req = "SELECT COUNT(DISTINCT login_gepi) FROM sso_table_correspondance p
                   INNER JOIN utilisateurs u ON p.login_gepi=u.login
                   INNER JOIN resp_pers rp ON u.login=rp.login
                   INNER JOIN responsables2 rp2 ON rp.pers_id=rp2.pers_id
                   INNER JOIN eleves e ON e.ele_id=rp2.ele_id
                   INNER JOIN j_eleves_classes jec ON (e.login=jec.login AND jec.id_classe='" . $classe . "')
                   WHERE u.statut='" . $profil . "'";
    }
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return(mysql_result($this->res, 0));
  }

  function del_by_classe_profil($classe, $profil) {
    if ($profil == "Eleve") {
      $this->req = "DELETE p FROM sso_table_correspondance p
                   INNER JOIN utilisateurs u ON p.login_gepi=u.login
                   INNER JOIN j_eleves_classes jec ON (u.login=jec.login AND jec.id_classe='" . $classe . "')
                   WHERE u.statut='" . $profil . "'";
    } else {
      $this->req = "DELETE p FROM sso_table_correspondance p
                   INNER JOIN utilisateurs u ON p.login_gepi=u.login
                   INNER JOIN resp_pers rp ON u.login=rp.login
                   INNER JOIN responsables2 rp2 ON rp.pers_id=rp2.pers_id
                   INNER JOIN eleves e ON e.ele_id=rp2.ele_id
                   INNER JOIN j_eleves_classes jec ON (e.login=jec.login AND jec.id_classe='" . $classe . "')
                   WHERE u.statut='" . $profil . "'";
    }
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return(mysqli_affected_rows($GLOBALS["mysqli"]));
  }

  function is_profil($login_gepi, $profil) {
    $this->req = "SELECT login FROM utilisateurs WHERE STATUT='" . $profil . "' AND LOGIN='" . $login_gepi . "'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    if (mysqli_affected_rows($GLOBALS["mysqli"]) == 1) {
      return true;
    } else {
      return false;
    }
  }

  private function get_logins_gepi_utilisateurs() {
    $this->req = "SELECT login FROM utilisateurs";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    while ($this->q = mysqli_fetch_assoc($this->res)) {
      $logins[] = $this->q['login'];
    }
    return($logins);
  }

  private function verif_exist_login_gepi($login) {
    $this->req = "SELECT login_gepi FROM sso_table_correspondance WHERE login_gepi='" . $login . "'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    $this->q = mysqli_fetch_array($this->res);
    if ($this->q)
      return true;
  }

  function verif_exist_login_sso($login) {
    $this->req = "SELECT login_sso FROM sso_table_correspondance WHERE login_sso='" . $login . "'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    $this->q = mysqli_fetch_array($this->res);
    if ($this->q)
      return true;
  }

  private function test_utilisateur($login) {
    $this->req = "SELECT login FROM utilisateurs WHERE login='" . $login . "'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    $this->q = mysqli_fetch_array($this->res);
    if ($this->q)
      return true;
  }

  private function test_auth_mode($login) {
    $this->req = "SELECT auth_mode FROM utilisateurs WHERE login='" . $login . "'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    $this->q = mysqli_fetch_array($this->res);
    if ($this->q[0] == 'sso')
      return true;
  }

  private function insert_table_sso($login_gepi, $login_sso) {
    $this->req = "INSERT INTO sso_table_correspondance (login_gepi,login_sso) values ('" . $login_gepi . "','" . $login_sso . "')";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
  }

  private function update_table_sso($login_gepi, $login_sso) {
    $this->req = "UPDATE sso_table_correspondance SET login_sso='" . $login_sso . "' WHERE login_gepi='" . $login_gepi . "'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
  }

  function delete_from_table_sso($login_gepi) {
    $this->req = "DELETE FROM sso_table_correspondance WHERE login_gepi='" . $login_gepi . "'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return(mysqli_affected_rows($GLOBALS["mysqli"]));
  }

  /*
    public function cherche_nom_prenom2($nom, $prenom) {
    $this->req = "SELECT `login` FROM `utilisateurs` WHERE `nom`=\"" . $nom . "\" AND `prenom`=\"" . $prenom . "\"";
    $this->res = mysql_query($this->req);
    return $this->res;
    }
   *
   */

  /**
   * Création de la table sso_table_import
   */
  public function cree_table_import() {
    $this->req = "DROP TABLE IF EXISTS `sso_table_import`;";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req);
    $this->req = " CREATE TABLE `sso_table_import` (
					`id` int(11) NOT NULL auto_increment COMMENT 'cle primaire auto-incrementee',
					`uid` varchar(100) NOT NULL COMMENT 'UID de ENT',
					`classe` varchar(100) NOT NULL COMMENT 'classe de ENT',
					`statut` varchar(100) COMMENT 'statut dans ENT',
					`prenom` varchar(50) COMMENT 'prenom dans ENT',
					`nom` varchar(50) COMMENT 'nom dans ENT',
					`login` varchar(50) COMMENT 'login de Gépi',
					`jointure` varchar(50) COMMENT 'jointure ENT annuaire',
					`pere` varchar(50) COMMENT 'uid pere dans ENT',
					`mere` varchar(50) COMMENT 'uid mere',
					`tuteur1` varchar(50) COMMENT 'uid tuteur1 dans ENT',
					`tuteur2` varchar(50) COMMENT 'uid tuteur2',
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=4
					CHARACTER SET utf8 COLLATE utf8_general_ci
					COMMENT='Liste des utilisateurs présents dans l''ENT';";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
  }

  /**
   * Suppression de la table sso_table_import
   */
  public function supprime_table_import() {
    $this->req = "DROP TABLE IF EXISTS `sso_table_import`;";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
  }

  /**
   * Insertion des lignes issues du CSV dans la table sso_table_import
   * @param array $importENT Ligne du fichier CSV
   * @param text $login_gepi Login de l'utilisateur
   */
  public function ligne_table_import($importENT, $login_gepi) {
    $this->req = "INSERT INTO `sso_table_import` (`id`, `uid`, `classe`, `statut`, `prenom`, `nom`, `login`, `jointure`, `pere`, `mere`, `tuteur1`, `tuteur2`)
	  VALUES (NULL, '" . $importENT[1] . "', '" . $importENT[2] . "', '" . $importENT[3] . "', '" . traitement_magic_quotes($this->traite_prenom($importENT)) . "', '" . traitement_magic_quotes($importENT[5]) . "', '" . $login_gepi . "', '" . $importENT[8] . "', '" . $importENT[9] . "', '" . $importENT[10] . "', '" . $importENT[11] . "', '" . $importENT[12] . "');";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
  }

  /**
   * Recherche un login dans utilisateurs à partir d'une ligne du CSV
   * @param array $importENT Ligne du fichier CSV
   * @param text $statut statut à prendre en compte (tous par défaut)
   * @param bool $recherche recherche ou rejette le statut (par défaut : recherche)
   */
  public function cherche_login($importENT, $statut = '%', $recherche = TRUE) {
    if ($recherche == TRUE) {
      $this->req = "SELECT `login` FROM `utilisateurs`
		WHERE `nom` = \"" . $importENT[5] . "\" AND `prenom` = \"" . $importENT[4] . "\" AND `statut` LIKE \"" . $statut . "\"";
    } ELSE {
      $this->req = "SELECT `login` FROM `utilisateurs`
		WHERE `nom` = \"" . $importENT[5] . "\" AND `prenom` = \"" . $importENT[4] . "\" AND `statut` NOT LIKE \"" . $statut . "\"";
    }
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  /**
   * Supprime les enregistrement sans classe en fonction d'un statut
   * @param text $statut statut à prendre en compte
   */
  public function supprime_sans_classe($statut) {
    $this->req = "DELETE FROM `sso_table_import` WHERE `statut` = '" . $statut . "' AND `classe`=''";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
  }

  /**
   * Recherche les enregistrements dont le login est vides dans sso_table_import
   */
  public function login_vide() {
    $this->req = "SELECT * FROM `sso_table_import` WHERE `login`='';";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  /**
   * Recherche si un utilisateur dans sso_table_import a le statut recherché
   * @param text $uid uid de l'utilisateur testé
   * @param text $statut statut à prendre en compte
   */
  public function a_statut($uid, $statut) {
    $this->req = "SELECT 1=1 FROM `sso_table_import` WHERE `uid` = $uid `statut` = '" . $statut . "'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    $row = mysqli_num_rows($this->res);
    if ($row == 1) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  /**
   * Recherche si un utilisateur dans sso_table_import est responsable en cherchant son enfant
   * @param array $ligne enregistrement de l'utilisateur cherché
   * @return ressource mysql_query($this->req) nom, prenom, login, uid des élèves liés à l'utilisateur
   */
  public function est_responsable($ligne) {
    $this->req = "SELECT `nom` nom, `prenom` prenom, `login` login, `uid` uid FROM `sso_table_import`
					  WHERE `pere`= \"" . $ligne['uid'] . "\"
						OR `mere`= \"" . $ligne['uid'] . "\"
						  OR `tuteur1` = \"" . $ligne['uid'] . "\"
						OR `tuteur2` = \"" . $ligne['uid'] . "\" ";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  /**
   * Recherche si un utilisateur dans sso_table_import est uniquement tuteur
   * @return ressource mysql_query($this->req) nom, prenom, login, uid des responsables uniquement tuteurs
   */
  public function est_que_tuteur() {
    $this->req = "SELECT r.`nom` , r.`prenom` , r.`login` , r.`uid` FROM `sso_table_import` r,`sso_table_import` e
					  WHERE (e.`tuteur1` = r.`uid`
						OR e.`tuteur2` = r.`uid` )
						  AND (e.`pere` != r.`uid`
						OR e.`mere` != r.`uid` ) ";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  /**
   * Recherche le login d'un utilisateur responsable en utilisant son enfant pour le retrouver dans Gépi (traitement des doublons)
   * @param array $eleve issus de public function est_responsable($ligne)
   * @param array $ligne enregistrement de l'utilisateur cherché
   * @return ressource mysql_query($this->req) login du responsable
   */
  public function cherche_responsable($eleve, $ligne) {
    $this->req = "SELECT p.login FROM `resp_pers` p , `eleves` e, responsables2 r
					WHERE \"" . $eleve['login'] . "\" = e.login
						AND e.ele_id = r.ele_id
						AND r.pers_id = p.pers_id
						AND p.nom =\"" . $ligne['nom'] . "\"
						AND p.prenom = \"" . $ligne['prenom'] . "\"";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  /**
   * Recherche si un utilisateur dans sso_table_import est eleve
   * @param array $ligne enregistrement de l'utilisateur cherché
   */
  public function est_eleve($ligne) {
    $this->req = "SELECT `nom` nom , `prenom` prenom , `login` login , `uid` uid FROM `sso_table_import`
					  WHERE `uid` = \"" . $ligne['pere'] . "\"
						OR `uid` = \"" . $ligne['mere'] . "\"
						OR `uid` = \"" . $ligne['tuteur1'] . "\"
						OR `uid` = \"" . $ligne['tuteur2'] . "\"";
    //$this->reselv = mysql_query($this->reqelv);
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  /**
   * Recherche le login d'un eleve a partir de son nom, de son prénom et du login de son responsable
   * @param array $ligne enregistrement de l'utilisateur cherché
   * @param array $responsable issu de public function est_eleve($ligne)
   */
  public function cherche_eleve($responsable, $ligne) {
    $this->req = "SELECT e.login FROM `resp_pers` p , `eleves` e, responsables2 r
					WHERE \"" . $responsable['login'] . "\" = p.login
						AND r.pers_id = p.pers_id
						AND e.ele_id = r.ele_id
						AND e.nom = \"" . $ligne['nom'] . "\"
						AND e.prenom = \"" . $ligne['prenom'] . "\"";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  # phpdoc

  public function doublon_pro($ligne, $statutEleve, $statutResponsable) {
    $this->req = "SELECT `login` FROM `utilisateurs`
				WHERE `nom` = \"" . $ligne['nom'] . "\"
				  AND `prenom` = \"" . $ligne['prenom'] . "\"
				  AND `statut` != 'eleve'
				  AND `statut` != 'responsable' ";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  # phpdoc

  public function met_a_jour_ent($login, $uid) {
    $this->req = "UPDATE `sso_table_import` SET `login`='" . $login . "'
					WHERE `uid`='" . $uid . "'";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
  }

  # phpdoc

  public function get_gepi_ent() {
    $this->req = "SELECT `login`,`uid` FROM `sso_table_import` WHERE `login` !=''";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  # phpdoc

  public function get_sans_login() {
    $this->req = "SELECT * FROM `sso_table_import` WHERE `login` = ''";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  # phpdoc

  public function cree_index_uid() {
    $this->req = "ALTER IGNORE TABLE `sso_table_import` ADD UNIQUE INDEX(`uid`)";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
  }

  # phpdoc

  public function trouve_statut_eleves() {
    $this->req = "SELECT DISTINCT `statut` FROM `sso_table_import`
				WHERE (`pere` != '' OR `mere` != '' OR `tuteur1` != '' OR `tuteur2` != '') AND `login` != '' ";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  # phpdoc

  public function trouve_statut_responsables() {
    $this->req = "SELECT DISTINCT a.statut FROM `sso_table_import` a,`sso_table_import` b
				WHERE (a.uid = b.pere OR a.uid = b.mere OR a.uid = b.tuteur1 OR a.uid = b.tuteur2) AND a.login != '' ";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  # phpdoc

  public function doublon_2ent_1gepi() {
    $this->req = "SELECT `uid`, `nom`, `prenom`, `login` FROM `sso_table_import` GROUP BY `login`,`nom`,`prenom` HAVING COUNT( * ) >1";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  /**
   * Efface un responsable en fonction de son nom, son prenom, son login
   * @param array $ligne enregistrement de l'utilisateur cherché
   * @param texte $lib_responsable libellé du statut dans l'ENT
   */
  public function efface_2ent_1gepi($ligne, $lib_responsable) {
    $this->req = "DELETE FROM `sso_table_import` WHERE `login` = '" . $ligne['login'] . "'
					AND `nom` = \"" . $ligne['nom'] . "\"
					AND `statut` != \"" . $lib_responsable . "\"";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req);
  }

  public function get_infos_classes() {
    $this->sql = "SELECT id, classe, nom_complet FROM classes ORDER BY classe ASC ";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->sql) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

  /**
   * Tronque les prénoms au premier espace
   * @param array $ligne enregistrement de l'utilisateur cherché
   * @return texte  1er prénom des élèves en ayant plusieurs, $ligne[4] pour les autres
   */
  private function traite_prenom($ligne) {
    if (($ligne[9] || $ligne[10] || $ligne[11] || $ligne[12] ) || (!$ligne[9] && !$ligne[10] && !$ligne[11] && !$ligne[12] && !$ligne[2] )) {
      $prenom_tab = explode(" ", $ligne[4]);
      return($prenom_tab[0]);
    } else {
      return($ligne[4]);
    }
  }

  /**
   * Recherche le login d'un eleve a partir de son nom, de son prénom et du login de son responsable
   * @param texte $uid enregistrement de l'utilisateur cherché
   */
  public function del_by_uid($uid) {
    $this->req = "DELETE FROM `sso_table_import` WHERE `uid` = '" . $uid . "' ";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
  }

  /**
   * On récupère les utilisateurs de Gépi sans correspondance dans la table
   */
  public function get_homonymes_sans_correspondance($nom) {
    $this->req = "SELECT * FROM `utilisateurs`  WHERE  nom='" . traitement_magic_quotes($nom) . "' AND NOT EXISTS
      (SELECT * from `sso_table_import` WHERE sso_table_import.login=utilisateurs.login)";
    $this->res = mysqli_query($GLOBALS["mysqli"], $this->req) or die(((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    return $this->res;
  }

}

?>
