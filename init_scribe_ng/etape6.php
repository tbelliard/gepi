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

if ($_POST['step'] == "6") {

    /*
     * Vidage des tables necessaires
     */
    if (!is_table_vide("groupes")) { vider_table_seule("groupes"); }
    if (!is_table_vide("j_groupes_classes")) { vider_table_seule("j_groupes_classes"); }
    if (!is_table_vide("j_groupes_professeurs")) { vider_table_seule("j_groupes_professeurs"); }
    if (!is_table_vide("j_eleves_groupes")) { vider_table_seule("j_eleves_groupes"); }
    if (!is_table_vide("j_groupes_matieres")) { vider_table_seule("j_groupes_matieres"); }
    if (!is_table_vide("j_signalement")) { vider_table_seule("j_signalement"); }

    // On se connecte au LDAP
    $ldap->connect();

    $equipes = $ldap->get_all_equipes();
    $nb_equipes = $equipes['count'];

    $nombre_enseignements = 0;
    
    # On initialise un tableau avec juste les données nécessaires
    $donnees_equipes = array();
    
    for($cpt=0; $cpt<$equipes['count']; $cpt++) {
        $code_classe = str_replace('profs-','',$equipes[$cpt]['cn'][0]);
        $donnees_equipes[$code_classe] = array();
        for($i=0;$i<$equipes[$cpt]['memberuid']['count'];$i++) {
          $donnees_equipes[$code_classe][] = $equipes[$cpt]['memberuid'][$i];
        }
    }
    
    $classes = ClasseQuery::create()
                  ->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
                  ->find();
    
    // On boucle sur chaque équipe, ce qui revient à boucler sur les classes
    foreach($classes as $classe_courante) {
        # On a une classe, on poursuit
        if (array_key_exists($classe_courante->getNom(), $donnees_equipes)) {
          
          # On initialisation la liste des élèves de la classe
          
          $students_query = mysql_query("SELECT login FROM j_eleves_classes WHERE 
                                        id_classe = '".$classe_courante->getId()."' AND
                                        periode = '1'");
          
          unset($students);
          $students = array();
          while ($row = mysql_fetch_object($students_query)) {
            $students[] = $row->login;
          }
          
          
          # On passe tous les profs de l'équipe
          foreach($donnees_equipes[$classe_courante->getNom()] as $login_prof) {
            
            $prof = UtilisateurProfessionnelPeer::retrieveByPK($login_prof);
            if ($prof) {
              # On a un prof. On créé un enseignement pour chacune des matières qui lui sont associées.
              $matieres = $prof->getMatieres();
              
              foreach($matieres as $matiere) {
                
                  $rec_groupe = mysql_query("INSERT INTO groupes SET
                      name = '".$matiere->getNomComplet()."',
                      description = '".$matiere->getNomComplet()."'");
                  $id_groupe = mysql_insert_id();
                  
                  $rec_mat = mysql_query("INSERT INTO j_groupes_matieres SET
                      id_matiere = '".$matiere->getMatiere()."',
                      id_groupe = '".$id_groupe."'");
                  
                  $rec_prof = mysql_query("INSERT INTO j_groupes_professeurs SET
                      login = '".$prof->getLogin()."',
                      id_groupe = '".$id_groupe."'");
                      
                  # Maintenant il faut mettre les élèves
                  foreach ($students as $student) {
                    
                    foreach ($classe_courante->getPeriodeNotes() as $periode) {
                      $rec = mysql_query("INSERT INTO j_eleves_groupes SET
                                  login = '".$student."',
                                  id_groupe = '".$id_groupe."',
                                  periode = '".$periode->getNumPeriode()."'");
                    } # Fin boucle périodes
                  } # Fin boucle élèves
                  
                  
                  # Association à la classe
                  $rec = mysql_query("INSERT INTO j_groupes_classes SET
                                  id_groupe = '".$id_groupe."',
                                  id_classe = '".$classe_courante->getId()."'");
                  
                  $nombre_enseignements++;                  
                  
                #} # Fin test si enseignement existe déjà
                
              } # Fin boucle matières du prof

              
            } # Fin test prof existant
          } # Fin boucle profs
          
          
        } else {
          # Pas de classe, on renvoie un message d'erreur.
          echo "<p>La classe $code_classe n'a pas été trouvée. Les cours ne seront pas créés.</p>";          
        }
                
    } // fin parcours des classes
    
    echo "<br/><br/>Groupes créés : $nombre_enseignements"."<br/><br/>";

    echo "<br/>";

    echo "<br/>";
    echo "<form enctype='multipart/form-data' action='etape7.php' method=post>";
    echo "<input type=hidden name='step' value='7'>";
    echo "<input type=hidden name='record' value='no'>";

    echo "<p>Passer &agrave; l'&eacute;tape 7 :</p>";
    echo "<input type='submit' value='Etape 7'>";
    echo "</form>";
}

else {
    // Affichage de la page des explications de l'etape 6 (aucune donnee postee)

    echo "<br/><p>L'&eacute;tape 6 vous permet de créer les enseignements dans les classes.</p>";
    echo "<p><b>Attention !</b> En raison de l'absence de données détaillées dans l'annuaire LDAP, la procédure va extrapoler les enseignements à partir des équipes pédagogiques uniquement. En conséquences seront créés des enseignements pour toutes les matières associées à tous les enseignants de l'équipe pédagogique d'une classe donnée. Ensuite, tous les élèves de la classe seront associés à ces enseignements, pour toutes les périodes. Il sera donc indispensable de refaire une vérification complète des enseignements ainsi créés. Notez en outre que les enseignements multi-classes (groupes de langue, par exemple) seront créés pour chaque classe séparémment, et qu'il vous faudra donc les 'fusionner' manuellement.</p>";
    echo "<p>Cette étape reste facultative, si vous préférez créer à la main les enseignements à travers les interfaces dédiées de Gepi.</p>";
    echo "<p>Tous les groupes existants dans la base actuelle seront supprimés !</p>";
    echo "<form enctype='multipart/form-data' action='etape6.php' method=post>";
    echo "<input type=hidden name='step' value='6'>";
    echo "<input type='submit' value='Je suis sûr'>";
    echo "</form>";
    echo "<br>";

}

require("../lib/footer.inc.php");
?>
