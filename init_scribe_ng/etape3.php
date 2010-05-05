<?php

/*
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard + auteur du script original (ac. Orléans-Tours)
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

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
require_once("../lib/LDAPServerScribe.class.php");
require_once("eleves_fonctions.php");
include("config_init_annuaire.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des responsables";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// Utilisation de la classe LDAP chargee et configuree
$ldap = new LDAPServerScribe();

echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";


if ($_POST['step'] == "3") {

    // On se connecte au LDAP
    $ldap->connect();

    /*
     * Recherche de tous les responsables d'eleves de l'établissement
     */
    $responsables = $ldap->get_all_responsables();
    $nb_responsables = $responsables['count'];

    /*
     * Vidage des tables necessaires
     */
    if (!is_table_vide("responsables2")) { vider_table_seule("responsables2"); }
    if (!is_table_vide("resp_pers")) { vider_table_seule("resp_pers"); }
    if (!is_table_vide("resp_adr")) { vider_table_seule("resp_adr"); }
    
    // On supprime tous les comptes d'accès de type responsable (vu qu'on a de toute façon supprimé tous les responsables...
/*
    UtilisateurProfessionelQuery::create()
      ->filterByStatut('responsable')
      ->delete();
*/
  $del = mysql_query("DELETE FROM utilisateurs WHERE statut = 'responsable'");
    
    // On parcours tous les responsables

    $resp_inseres = 0;
    // avertissement si un eleve a plus de 2 responsables legaux
    $avertissement_trop_de_responsables = 0;
    for($nb=0; $nb<$nb_responsables; $nb++) {
        // On créé les responsables en base (avec les classes ORM)
        // Table resp_pers

        $resp = new ResponsableEleve();
        $resp->setLogin($responsables[$nb][$ldap->champ_login][0]);
        $resp->setNom($responsables[$nb][$ldap->champ_nom][0]);
        $resp->setPrenom($responsables[$nb][$ldap->champ_prenom][0]);
        $resp->setCivilite($responsables[$nb]['personaltitle'][0]);
        
        $homephone = array_key_exists('homephone', $responsables[$nb]) ? $responsables[$nb]['homephone'][0] : '';
        $resp->setTelPers($homephone);
        
        $profphone = array_key_exists('telephonenumber', $responsables[$nb]) ? $responsables[$nb]['telephonenumber'][0] : '';
        $resp->setTelProf($profphone);
        
        $mobilephone = array_key_exists('mobile', $responsables[$nb]) ? $responsables[$nb]['mobile'][0] : '';
        $resp->setTelPort($mobilephone);
        
        $respemail = array_key_exists($ldap->champ_email, $responsables[$nb]) ? $responsables[$nb][$ldap->champ_email][0] : '';
        $resp->setMel($respemail);
        
        $pers_id = array_key_exists('intid', $responsables[$nb]) ? $responsables[$nb]['intid'][0] : '';
        $resp->setPersId($pers_id);
        
        
        // On créé l'adresse associée
        
        $resp_addr = array_key_exists('entpersonadresse', $responsables[$nb]) ? $responsables[$nb]['entpersonadresse'][0] : null;
        $resp_ville = array_key_exists('entpersonville', $responsables[$nb]) ? $responsables[$nb]['entpersonville'][0] : '';
        $resp_cp = array_key_exists('entpersoncodepostal', $responsables[$nb]) ? $responsables[$nb]['entpersoncodepostal'][0] : '';
        $resp_pays = array_key_exists('entpersonpays', $responsables[$nb]) ? $responsables[$nb]['entpersonpays'][0] : '';
        
        if ($resp_addr) {
          $adr = new ResponsableEleveAdresse();
          $adr->setAdrId($pers_id);
          $adr->setAdr1($resp_addr);
          $adr->setAdr2('');
          $adr->setAdr3('');
          $adr->setAdr4('');
          $adr->setCommune($resp_ville);
          $adr->setCp($resp_cp);
          $adr->setPays($resp_pays);
        
          $resp->setResponsableEleveAdresse($adr);
        }
        
        $resp->save();
        $resp_inseres++;

        // Pour chaque responsable, on cherche le ou les eleves associes
        // attribut de lien : ENTAuxPersRelEleveEleve


        $nb_eleves_a_charge = $responsables[$nb]['entauxpersreleleveeleve']['count'];
        $valid_associations = 0;
        //pour chaque dn d'eleve
        for ($i=0;$i<$nb_eleves_a_charge;$i++) {
            $eleve_uid = explode(",",$responsables[$nb]['entauxpersreleleveeleve'][$i]);

            $eleve_associe_login = substr($eleve_uid[0], 4);
            
            $req_eleid = mysql_query("SELECT ele_id FROM eleves WHERE login = '$eleve_associe_login'");
            
            // On s'assure qu'on a bien un élève correspondant !
            if (mysql_num_rows($req_eleid) == 1) {
              $eleve_associe_ele_id = mysql_result($req_eleid, 0);
              /*
               * Il faut ensuite effectuer le lien entre Responsable et Eleve
               */

              // Gepi donne un ordre aux responsables, il faut donc verifier combien de responsables sont deja enregistres pour l'eleve
              // On initialise le numero de responsable
              $numero_responsable = 1;
              $req_nb_resp_deja_presents = "SELECT count(*) FROM responsables2 WHERE ele_id = '$eleve_associe_ele_id'";
              $res_nb_resp = mysql_query($req_nb_resp_deja_presents);
              if (mysql_errno() != 0) {
                  error_log("Erreur : ".mysql_error());
                  die("Une erreur s'est produite lors la r&eacute;cup&eacute;ration des responsables d&eacute;j&agrave; pr&eacute;sents.");
              }
              $nb_resp = mysql_fetch_array($res_nb_resp);
              if ($nb_resp[0] > 0) {
                  // Si deja 1 ou plusieurs responsables legaux pour cet eleve,on ajoute le nouveau responsable en incrementant son numero
                  $numero_responsable += $nb_resp[0];
                  if ($numero_responsable > 2) {
                      // On affichera un avertissement disant que l'eleve a plus de 2 responsables legaux, et que ce n'est pas normal.
                      // gepi les affichera dans la fiche de l'eleve mais ils ne seront pas reconnus parfaitement (recherches impossibles)
                      $avertissement_trop_de_responsables = 1;
                  }

                  //--
                  // TODO: tester si on a des adresses identiques, et n'utiliser qu'un seul objet adresse dans ce cas.
                  //--
              }

              // Ajout de la relation entre Responsable et Eleve dans la table "responsables2" pour chaque eleve
              $req_ajout_lien_eleve_resp = "INSERT INTO responsables2 VALUES('$eleve_associe_ele_id','".$resp->getPersId()."','$numero_responsable','')";
              mysql_query($req_ajout_lien_eleve_resp);
              if (mysql_errno() != 0) {
                  die("Une erreur s'est produite lors de l'affectation d'un &eacute;l&egrave;ve &agrave; son responsable l&eacute;gal.");
              }
              $valid_associations++;
            }
        }
        
        if ($nb_eleves_a_charge > 0 && $valid_associations > 0) {
            // On créé maintenant son compte d'accès à Gepi
            // On test si l'uid est deja connu de GEPI
            $compte_utilisateur_resp = UtilisateurProfessionnelPeer::retrieveByPK($resp->getLogin());
            if ($compte_utilisateur_resp != null) {
                // Un compte d'accès avec le même identifiant existe déjà. On ne touche à rien.
                echo "Un compte existe déjà pour l'identifiant ".$resp->getLogin().".<br/>";
            }
            else {
                $new_compte_utilisateur = new UtilisateurProfessionnel();
                $new_compte_utilisateur->setAuthMode('sso');
                $new_compte_utilisateur->setCivilite($resp->getCivilite());
                $new_compte_utilisateur->setEmail($resp->getMel());
                $new_compte_utilisateur->setEtat('actif');
                $new_compte_utilisateur->setLogin($resp->getLogin());
                $new_compte_utilisateur->setNom($resp->getNom());
                $new_compte_utilisateur->setPrenom($resp->getPrenom());
                $new_compte_utilisateur->setShowEmail('no');
                $new_compte_utilisateur->setStatut('responsable');
                $new_compte_utilisateur->save();
            }          
          
        }
        
        
        
        
    }

    /*
     * Affichage du résumé de l'étape
     */
    echo "<h3> Résumé de l'étape 3 </h3>";

    echo "<p><b>$resp_inseres</b> responsables d'&eacute;l&egrave;ves ins&eacute;r&eacute;s en base<br> (sur $nb_responsables trouv&eacute;(s))</p>";
    if ($avertissement_trop_de_responsables) {
        echo "<br><p><font color=red>Avertissement : certains &eacute;l&egrave;ves ont plus de 2 responsables legaux, ce qui n'est pas normal. Bien que gepi n'en g&egrave;re que 2, ils ont tous &eacute;t&eacute; import&eacute;s.</font></p><br>";
    }
    echo "<br>";
    echo "<form enctype='multipart/form-data' action='etape4.php' method=post>";
    echo "<input type=hidden name='step' value='4'>";
    echo "<input type=hidden name='record' value='no'>";

    echo "<p>Passer &agrave; l'&eacute;tape 4 :</p>";
    echo "<input type='submit' value='Etape 4'>";
    echo "</form>";
}

else {
    // Affichage de la page des explications de l'etape 3 (aucune donnee postee)
    // La troisieme étape consiste a importer les responsables d'eleves, les associer a leur(s) eleve(s)
    echo "<br><p>L'&eacute;tape 3 vous permet d'importer les reponsables d'&eacute;l&egrave;ve et de les associer &agrave; leur(s) &eacute;l&egrave;ve(s).</p>";
    echo "<br><p>Les donn&eacute;es concernant les reponsables d'&eacute;l&egrave;ves actuellement en base seront remplac&eacute;es par ces nouvelles donn&eacute;es</p>";

    // On test si les tables dans lesquelles on va importer sont vides
    if ((!is_table_vide("responsables2")) || (!is_table_vide("resp_pers")) || (!is_table_vide("resp_adr"))) {
        // Si au moins une n'est pas vide, on affiche un message d'avertissement
        echo "<br><p><font color=red>Attention, les tables concernant les responsables ne sont pas vides, vous n'&ecirc;tes pas pass&eacute; par la premi&egrave;re &eacute;tape.</font></p><br>";
    }

    echo "<form enctype='multipart/form-data' action='etape3.php' method=post>";
    echo "<input type=hidden name='step' value='3'>";
    echo "<input type='submit' value='Je suis sûr'>";
    echo "</form>";
    echo "<br>";
    require("../lib/footer.inc.php");

}

?>
