<?php

/*
 * $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard + auteur du script original (ac. Orléans-Tours)
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
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// Utilisation de la classe LDAP chargee et configuree
$ldap = new LDAPServerScribe();

echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

if ($_POST['step'] == "5") {
	check_token(false);

    // On se connecte au LDAP
    $ldap->connect();

    // Si on a bien les donnees dans la session, on peut continuer
    /*
     * Recherche de tous les profs de l'établissement (pour ce RNE)
    */
    $matieres = $ldap->get_all_matieres();
    $nb_matieres = $matieres['count'];


   /*
    * Ajout des matières
    */

    for($cpt=0; $cpt<$matieres['count']; $cpt++) {
        
        $code_matiere = $matieres[$cpt]['cn'][0];
        $nom_matiere = $matieres[$cpt]['description'][0];
        
        // On test si on a déjà cette matière
        $nouvelle_matiere = MatierePeer::retrieveByPK($code_matiere);
        if ($nouvelle_matiere != null) {
            echo "La matière "
            .$nouvelle_matiere->getMatiere()." ("
            .$nouvelle_matiere->getNomComplet()
            .") existe d&eacute;ja<br/>";
        }
        else {
            $nouvelle_matiere = new Matiere();
            $nouvelle_matiere->setMatiere($code_matiere);
            $nouvelle_matiere->setNomComplet($nom_matiere);
            $nouvelle_matiere->save();
        }
        
        
        // Maintenant on associe les profs à cette matiere
        $nb_profs = $matieres[$cpt]['memberuid']['count'];
        
        $matiere_profs = $nouvelle_matiere->getProfesseurs();
                
        if ($nb_profs > 0) {
          for ($i=0;$i<$nb_profs;$i++){
            // On vérifie que le prof existe, quand même...
            $prof = UtilisateurProfessionnelPeer::retrieveByPK($matieres[$cpt]['memberuid'][$i]);
            
            // Le prof existe, on créer l'association, si elle n'existe pas encore
            if ($prof != null) {
              
              // L'association n'existe pas, on la créé
              // Pour ça, on doit déterminer l'ordre
              if (!$matiere_profs->contains($prof)) {
                $assoc = JProfesseursMatieresQuery::create()
                  ->filterByProfesseur($prof)
                  ->orderByOrdreMatieres('desc')
                  ->findOne();

                $nouvel_ordre = $assoc == null ? 1 : $assoc->getOrdreMatieres()+1;
                
                $new_assoc = new JProfesseursMatieres();
                $new_assoc->setProfesseur($prof);
                $new_assoc->setMatiere($nouvelle_matiere);
                $new_assoc->setOrdreMatieres($nouvel_ordre);
                $new_assoc->save();
              }
            } else {
              echo "Le prof associé (".$matieres[$cpt]['memberuid'][$i].") n'existe pas dans la base !<br/>";
            }
          }
        }
        
    } // fin parcours des matières
        /*
         * Résumé des matières trouvées :
         */
    echo "<br/><br/>Matières trouvées : $nb_matieres"."<br/><br/>";

    echo "<br/>";

    echo "<form enctype='multipart/form-data' action='etape6.php' method=post>";
	//echo add_token_field();
    echo "<input type=hidden name='step' value='6'>";
    echo "<input type=hidden name='record' value='no'>";

    echo "<p>Passer &agrave; l'&eacute;tape 6 :</p>";
    echo "<input type='submit' value='Etape 6'>";
    echo "</form>";
}

else {
    // Affichage de la page des explications de l'etape 5 (aucune donnee postee)

    echo "<br/><p>L'&eacute;tape 5 vous permet d'importer les matières et de les associés aux professeurs qui vont avoir la charge de les enseigner. Les matières déjà présentes ne seront pas supprimées.</p>";
    echo "<form enctype='multipart/form-data' action='etape5.php' method=post>";
	echo add_token_field();
    echo "<input type=hidden name='step' value='5'>";
    echo "<input type='submit' value='Je suis sûr'>";
    echo "</form>";
    echo "<br>";

    require("../lib/footer.inc.php");

}

?>
