<?php
/*
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard + auteur initial du script (ac. Orléans-Tours)
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
$titre_page = "Outil d'initialisation de l'année : Importation des élèves";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// Utilisation de la classe LDAP chargee et configuree
$ldap = new LDAPServerScribe();

echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

if (isset($_POST['step'])) {

    // Compteurs pour les insertions
    $classes_inserees = 0;
    $eleves_inseres = 0;

    // Compteur pour les erreurs
    $eleves_erreurs = 0;
    $resp_erreurs = 0;
    $classes_deja_existantes = 0;

    // Logins des eleves en erreur
    $eleves_erreurs_logins = array();
    // Logins des responsables en erreur
    $resp_erreurs_logins = array();


    // Données à importer
    $nb_eleves=0;
    $nb_responsables=0;
    $nb_profs=0;
    $siren = 0;
    // L'admin a validé la procédure, on procède donc...

    // On se connecte au LDAP
    $ldap->connect();

    //----***** STEP 1 *****-----//
    /*
    * L'étape 1 consiste à
     *  - récupérer le SIREN de l'établissement
     *  - Recuperer tous les eleves pour ce RNE et les creer
     *  - Recuperer les classes de chaque eleve pour avoir toutes les classes de l'établissement et les créer
     * (l'association eleve-classe se fera à l'étape suivante)
    */

    if ($_POST['step'] == "1") {

        /*
         * Vidage des tables qui le necessitent
         */
        vider_tables_avant_import();

        #UtilisateurProfessionnelQuery::create()
        #  ->filterByStatut('eleve')
        #  ->delete();
        $del = mysql_query("DELETE FROM utilisateurs WHERE statut = 'eleve'");

        /*
         * Recherche de tous les profs de l'établissement
         */
        $profs = $ldap->get_all_profs();
        $nb_profs = $profs['count'];

        /*
         * Recherche de tous les eleves de l'établissement
         */
        $eleves = $ldap->get_all_eleves();

        // Le premier element (indice 'count') du tableau n'est pas un eleve mais le 'count' du nombre
        // d'éléments présents.
        $nb_eleves = $eleves['count'];

        // On parcours tous les eleves
        for($nb=0; $nb<$nb_eleves; $nb++) {
            /*
             * On créé l'eleve en base (avec les classes ORM)
             */
            $eleve = EleveQuery::create()
              ->filterByLogin($eleves[$nb][$ldap->champ_login][0])
              ->findOne();

            if ($eleve == NULL) {
              $nouvel_eleve = new Eleve();
              $nouvel_eleve->setLogin($eleves[$nb][$ldap->champ_login][0]);
            } else {
              $nouvel_eleve = $eleve;
            }
            $nouvel_eleve->setNom($eleves[$nb][$ldap->champ_nom][0]);
            $nouvel_eleve->setPrenom($eleves[$nb][$ldap->champ_prenom][0]);
            $nouvel_eleve->setSexe($eleves[$nb]['entpersonsexe'][0]);
            $nouvel_eleve->setNaissance(formater_date_pour_mysql($eleves[$nb]['entpersondatenaissance'][0]));
            $nouvel_eleve->setLieuNaissance('');
            $ele_no_et = (array_key_exists('employeenumber', $eleves[$nb])) ? $eleves[$nb]['employeenumber'][0] : '';
            $nouvel_eleve->setElenoet($ele_no_et);
            $nouvel_eleve->setEreno('');
            
            $ele_id = (array_key_exists('intid', $eleves[$nb])) ? $eleves[$nb]['intid'][0] : false;
            // L'ele_id est très important dans Gepi pour le lien eleve/responsable, mais dans Scribe il ne peut pas
            // etre spécifié manuellement (seulement à l'import depuis Sconet). En conséquence, s'il est absent,
            // on le remplace par le noet, en espérant qu'il n'y ait pas de conflit ! (en principe non)
            if (!$ele_id and $ele_no_et != '') $ele_id = $ele_no_et;
            
            $nouvel_eleve->setEleid($ele_id);
            $nouvel_eleve->setNoGep($eleves[$nb]['ine'][0]);
            $nouvel_eleve->setEmail($eleves[$nb][$ldap->champ_email][0]);
            
            // On ne peut créer l'élève que s'il a un ele_id. Sinon, ça ne va pas marcher correctement !
            if ($ele_id) {
            
              /*
               * Récupération des CLASSES de l'eleve :
               * Pour chaque eleve, on parcours ses classes, et on ne prend que celles
               * qui correspondent à la branche de l'établissement courant, et on les stocke
               */
              $nb_classes = $eleves[$nb]['enteleveclasses']['count'];

              // Pour chaque classe trouvée..
              $classe_from_ldap = array();
              for ($cpt=0; $cpt<$nb_classes; $cpt++) {
                  $classe_from_ldap = explode("$", $eleves[$nb]['enteleveclasses'][$cpt]);
                  // $classe_from_ldap[0] contient le DN de l'établissement
                  // $classe_from_ldap[1] contient l'id de la classe
                  $code_classe = $classe_from_ldap[1];

                  // Si le SIREN de la classe trouvée correspond bien au SIREN de l'établissement courant,
                  // on crée une entrée correspondante dans le tableau des classes disponibles
                  // Sinon c'est une classe d'un autre établissement, on ne doit donc pas en tenir compte
                  if (strcmp($classe_from_ldap[0], $ldap->get_base_branch()) == 0) {

                      /*
                       * On test si la classe que l'on souhaite ajouter existe déjà
                       * en la cherchant dans la base (
                       */
                      $crit = new Criteria();
                      $crit->add(ClassePeer::CLASSE, $code_classe);
                      $classe_select = ClassePeer::doSelect($crit);
                      $classe_courante = null;

                      // Si elle n'existe pas
                      if (count($classe_select) == 0) {
                          /*
                          * Creation de la classe correspondante
                          */
                          $nouvelle_classe = new Classe();
                          $nouvelle_classe->setNom($code_classe);

                          $nouvelle_classe->save();
                          $classes_inserees++;
                          $classe_courante = $nouvelle_classe;
                          // On crééra les périodes associées a la classe par la suite
                      }
                      else if (count($classe_select) == 1){
                          $classe_courante = $classe_select[0];
                      }
                      // Si plus d'une classe trouvee, erreur...
                      else {
                          die ("erreur dans la base : plusieurs classes ont le meme nom.<br>");
                      }

                      // Comme on n'a pas encore de périodes, on va tricher un peu
                      // pour la définition de l'association élève-classe
                      $nouvelle_assoc_classe_eleve = new JEleveClasse();
                      $nouvelle_assoc_classe_eleve->setClasse($classe_courante);
                      $nouvelle_assoc_classe_eleve->setEleve($nouvel_eleve);
                      // Pour le moment on met 0 dans l'id de periode, car on les créera plus tard.
                      // On veut simplement garder l'association eleve/classe pour ne pas avoir
                      // a refaire une connexion au LDAP a l'etape suivante
                      $nouvelle_assoc_classe_eleve->setPeriode(0);
                      $nouvelle_assoc_classe_eleve->save();
                      $nouvel_eleve->addJEleveClasse($nouvelle_assoc_classe_eleve);

                  } //Fin du if classe appartient a l'etablissement courant
              } //Fin du parcours des classes de l'eleve

              $nouvel_eleve->save();
              
              // On créé maintenant son compte d'accès à Gepi
              // On test si l'uid est deja connu de GEPI
              $compte_utilisateur_eleve = UtilisateurProfessionnelPeer::retrieveByPK($nouvel_eleve->getLogin());
              if ($compte_utilisateur_eleve != null) {
                  // Un compte d'accès avec le même identifiant existe déjà. On ne touche à rien.
                  echo "Un compte existe déjà pour l'identifiant ".$nouvel_eleve->getLogin().".<br/>";
              }
              else {
                  $new_compte_utilisateur = new UtilisateurProfessionnel();
                  $new_compte_utilisateur->setAuthMode('sso');
                  $new_compte_utilisateur->setCivilite($eleves[$nb]['personaltitle'][0]);
                  $new_compte_utilisateur->setEmail($nouvel_eleve->getEmail());
                  $new_compte_utilisateur->setEtat('actif');
                  $new_compte_utilisateur->setLogin($nouvel_eleve->getLogin());
                  $new_compte_utilisateur->setNom($nouvel_eleve->getNom());
                  $new_compte_utilisateur->setPrenom($nouvel_eleve->getPrenom());
                  $new_compte_utilisateur->setShowEmail('no');
                  $new_compte_utilisateur->setStatut('eleve');
                  $new_compte_utilisateur->save();
              }
              $eleves_inseres++;
            
          }
            
            
        }
    }

    /*
     * Affichage du résumé de l'étape 1
     */
    echo "<h3> Résumé de l'étape 1 </h3>";

    echo "<b>$eleves_inseres</b> &eacute;l&egrave;ves ins&eacute;res en base<br>";

    echo "<b>$classes_inserees</b> classes ins&eacute;r&eacute;es en base<br>";

    // Les indices sont les id des classes de l'établissement
    // On a pris que les classes correspondant au SIREN de l'établissement
    echo "<br>";
    echo "<form enctype='multipart/form-data' action='etape2.php' method=post>";
    echo "<input type=hidden name='step' value='1'>";
    echo "<input type=hidden name='record' value='no'>";

    echo "<p>Passer &agrave; l'&eacute;tape 2 :</p>";
    echo "<input type='submit' value='Etape 2'>";
    echo "</form>";
}

else {

    // La première étape consiste à importer les classes

    echo "<br><p>L'opération d'importation des élèves depuis le LDAP va effectuer les opérations suivantes :</p>";
    echo "<ul>";
    echo "<li>Importation des &eacute;l&egrave;ves.</li>";
    echo "<li>Tentative d'ajout de chaque élèves présent dans l'annuaire.</li>";
    echo "<li>Si l'élève n'existe pas, il est créé.</li>";
    echo "<li>Si l'élève existe déjà, ses informations de base sont mises à jour.</li>";
    echo "<li>Ajout des classes présentes dans l'annuaire.</li>";
    echo "</ul>";

    echo "<form enctype='multipart/form-data' action='etape1.php' method=post>";
    echo "<input type=hidden name='step' value='1'>";
    echo "<input type=hidden name='record' value='no'>";

    echo "<p>Etes-vous sûr de vouloir importer tous les élèves depuis l'annuaire vers Gepi ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='Je suis sûr'>";
    echo "</form>";

    require("../lib/footer.inc.php");
}

?>
