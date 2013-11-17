<?php
/*
 *
 * Copyright 2001, 2009 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] : false);

// On teste si un professeur peut effectuer l'édition
if (($_SESSION['statut'] == 'professeur') and $gepiSettings["GepiAccesRecapitulatifEctsProf"] !='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

// On teste si le service scolarité peut effectuer la saisie
if (($_SESSION['statut'] == 'scolarite') and $gepiSettings["GepiAccesRecapitulatifEctsScolarite"] !='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}



// Si aucune classe n'a été choisie, on affiche la liste des classes accessibles
if (!$id_classe) {

// On n'affiche le header que dans la partie de sélection de la classe,
// car le tableau va s'ouvrir dans une nouvelle fenêtre pour pouvoir être imprimé

//**************** EN-TETE *****************
$titre_page = "Récapitulatif ECTS";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

    echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

    if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {

        // On ne sélectionne que les classes qui ont au moins un enseignement ouvrant à crédits ECTS
        if($_SESSION['statut']=='scolarite'){
            $call_classe = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT c.*
                                        FROM classes c, periodes p, j_scol_classes jsc, j_groupes_classes jgc
                                        WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' AND c.id=jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
        } else {
            $call_classe = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc WHERE p.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
        }

        $nombre_classe = mysqli_num_rows($call_classe);
        if($nombre_classe==0){
            echo "<p>Aucune classe avec paramétrage ECTS ne vous est attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
        }
    } else {
        $call_classe = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT c.* FROM classes c, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (c.id = jgc.id_classe AND jgc.saisie_ects = TRUE AND jgc.id_groupe = jgp.id_groupe AND jgp.login = '".$_SESSION['login']."')");
        $nombre_classe = mysqli_num_rows($call_classe);
        if ($nombre_classe == "0") {
            echo "Vous n'êtes pas enseignant dans une classe ayant des enseignements ouvrant droits à des ECTS.";
        }
    }

    echo "<p>Cliquez sur la classe pour laquelle vous souhaitez éditer les documents ECTS :</p>\n";
    //echo "<br/><p><a href='recapitulatif.php?id_classe=all' target='_new'>Toutes les classes</a></p>";

    $i = 0;
    unset($tab_lien);
    unset($tab_txt);
    $nombreligne = mysqli_num_rows($call_classe);
    while ($i < $nombreligne){
        $tab_lien[$i] = "recapitulatif.php?id_classe=".mysql_result($call_classe, $i, "id");
        $tab_txt[$i] = mysql_result($call_classe, $i, "classe");
        $i++;

    }
    tab_liste($tab_txt,$tab_lien,3,"target='_new'");
    echo "<p><br /></p>\n";

} else {
  // Ici, on affiche le tableau

  // On load les les périodes de l'année en cours, notamment pour connaître
  // la période actuelle
  include "../lib/periodes.inc.php";


  // Initialisation des tableaux
  
  $annees = array(); // Contient années->périodes->matieres.
                     // C'est le tableau global de référence pour les colonnes.
                     
  $resultats = array(); // Contient les résultats, organisés par
                        // élève->année->période->matiere. C'est le tableau de stockage des donnnées
                        
  $derniere_annee_archivee = false;
  $ignore_annees = array(); // Contient, pour chaque élève, les années
                              // qui doivent être grisées car redoublées
  
  $gepiYear = $gepiSettings['gepiYear']; // L'année courante


  // On vérifie que l'utilisateur a bien le droit de visualiser les résultats de la classe sélectionnée
  
    if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {
        if($_SESSION['statut']=='scolarite'){
            $call_classe = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT c.*
                                        FROM classes c, periodes p, j_scol_classes jsc, j_groupes_classes jgc
                                        WHERE c.id = '".$id_classe."' AND p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' AND c.id=jgc.id_classe AND jgc.saisie_ects = TRUE");
        } else {
            $call_classe = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc WHERE c.id = '".$id_classe."' AND p.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE");
        }

        $nombre_classe = mysqli_num_rows($call_classe);
        if($nombre_classe==0){
            echo "<p>Aucune classe avec paramétrage ECTS ne vous est attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
            die();
        }
    } else {
        $call_classe = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT c.* FROM classes c, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (c.id = '".$id_classe."' AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE AND jgc.id_groupe = jgp.id_groupe AND jgp.login = '".$_SESSION['login']."')");
        $nombre_classe = mysqli_num_rows($call_classe);
        if ($nombre_classe == "0") {
            echo "Soit vous n'êtes pas enseignant dans cette classe, soit cette classe n'ouvre pas droits à des ECTS.";
            die();
        }
    }

  // On passe élève par élève. Pour chaque élève, on va extraire les ECTS
  // archivés, puis les ECTS courant, et au fur et à mesure on stocke
  // tout ça dans le tableau récapitulatif général, en mettant bien à jour
  
  // Appel des élèves
  $Classe = ClassePeer::retrieveByPK($id_classe);
  $Eleves = $Classe->getEleves('1');
  

  // Boucle de remplissage des données
  foreach ($Eleves as $Eleve) {
    if (!array_key_exists($Eleve->getId(), $ignore_annees)){
      $ignore_annees[$Eleve->getId()] = array();
    }
    // On commence par les archives
    $annees_precedentes = $Eleve->getEctsAnneesPrecedentes();
    if (!array_key_exists($Eleve->getLogin(), $resultats)) {
      $resultats[$Eleve->getLogin()] = array();
    }
    
    
    // On alimente le tableau de référence, si nécessaire
    foreach($annees_precedentes as $a) {
      // L'année
      if (!array_key_exists($a['annee'], $annees)) {
        $annees[$a['annee']] = array();
        // Les périodes
        foreach($a['periodes'] as $num => $periode) {
          if (!array_key_exists($num, $annees[$a['annee']])) {
            $annees[$a['annee']][$num] = array('nom_periode' => $periode, 'matieres' => array());
          }
        }
      }
    }
    
    // On va chercher les résultats
    foreach($annees_precedentes as $a) {
      // On initialise le tableau de l'année, si besoin
      if (!array_key_exists($a['annee'], $resultats[$Eleve->getLogin()])) {
        $resultats[$Eleve->getLogin()][$a['annee']] = array();
      }
      
      // On passe chaque période et on récupère le crédit
      foreach($a['periodes'] as $p_num => $p) {
        // On initialise le tableau de la période, si besoin
        if (!array_key_exists($p_num, $resultats[$Eleve->getLogin()][$a['annee']])) {
          $resultats[$Eleve->getLogin()][$a['annee']][$p_num] = array(); // C'est le tableau qui va ensuite contenir les crédits par matière
        }
        $credits = $Eleve->getArchivedEctsCredits($a['annee'], $p_num);
        foreach($credits as $credit) {
          if (!array_key_exists($credit->getMatiere(), $annees[$a['annee']][$p_num]['matieres'])) {
            $annees[$a['annee']][$p_num]['matieres'][$credit->getMatiere()] = $credit->getMatiere();
          }
          $resultats[$Eleve->getLogin()][$a['annee']][$p_num][$credit->getMatiere()] = $credit;
        }
      }
      // On regarde si l'élève est redoublant
      $redoublant = sql_count(sql_query("SELECT * FROM archivage_eleves2 WHERE ine = '".$Eleve->getNoGep()."' AND doublant = 'R'")) != "0" ? true : false;
      if ($redoublant && $derniere_annee_archivee) {
        $ignore_annees[$Eleve->getId()][$derniere_annee_archivee] = true;
      }
      $derniere_annee_archivee = $a['annee'];      
    }
        
    // On continue avec l'année courante, même principe
    $redoublant = sql_count(sql_query("SELECT * FROM j_eleves_regime WHERE login = '".$Eleve->getLogin()."' AND doublant = 'R'")) != "0" ? true : false;
    if ($redoublant) {
      $ignore_annees[$Eleve->getId()][$derniere_annee_archivee] = true;
    }
    if (!array_key_exists($gepiYear, $annees)) {
      $annees[$gepiYear] = array();
    }
    if (!array_key_exists($gepiYear, $resultats[$Eleve->getLogin()])) {
      $resultats[$Eleve->getLogin()][$gepiYear] = array();
    }
    
    // On regarde quelle est la période maxi pour laquelle l'élève a des notes
    $periode_num = mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT MAX(num_periode) FROM ects_credits WHERE id_eleve = '".$Eleve->getId()."'"),0);
    
    for($i=1;$i<=$periode_num;$i++) {
      if (!array_key_exists($i, $annees[$gepiYear])) {
        $annees[$gepiYear][$i] = array('nom_periode' => $nom_periode[$i], 'matieres' => array());
      }
      if (!array_key_exists($i, $resultats[$Eleve->getLogin()][$gepiYear])) {
        $resultats[$Eleve->getLogin()][$gepiYear][$i] = array();
      }
      // Maintenant on récupère pour chaque période les crédits pour chaque matière
      $categories = $Eleve->getEctsGroupesByCategories($i);
      foreach ($categories as $categorie) {
        foreach($categorie[1] as $group) {
          $CreditEcts = $Eleve->getEctsCredit($i,$group->getId());
          $matiere = mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT m.nom_complet FROM matieres m, j_groupes_matieres jgm, groupes g
            WHERE
              m.matiere = jgm.id_matiere AND
              jgm.id_groupe = '".$group->getId()."'"), 0);
          
          // On enregistre quoi qu'il arrive la matière dans le tableau de référence,
          // car il s'agit de l'année en cours. Donc on doit pouvoir utiliser le tableau
          // comme document de travail.
          if (!array_key_exists($matiere, $annees[$gepiYear][$i]['matieres'])) {
              $annees[$gepiYear][$i]['matieres'][$matiere] = $matiere;
          }
          if ($CreditEcts) {
            $resultats[$Eleve->getLogin()][$gepiYear][$i][$matiere] = $CreditEcts;
          }
        }
      }
    }
  }
  
  function column_classname($annee) {
    return preg_replace('/[^0-9a-zA-Z]/i','',$annee);
  }
  
  // Affichage des en-têtes du tableau
  require('../lib/header.inc.php');
  ?>
  <style>
        .cell, .central_cell, .first_cell, .last_cell, .lone_cell
        {
          border-top: 1px solid black;
          border-bottom: 1px solid black;
          padding: 5 5 5 5;
          text-align: center;
        }
                
        .debut_annee {
          border-left: 4px double black;
        }
        
        .fin_annee {
          border-right: 4px double black;
        }
        
        .lone_cell
        {
          border-left: 1px solid black;
          border-right: 1px solid black;
        }
        
        .central_cell
        {
          border-left: 1px solid grey;
          border-right: 1px solid grey;
        }
        
        .first_cell
        {
          border-left: 2px solid black;
          border-right: 1px solid grey;
        }
        
        .last_cell
        {
          border-left: 1px solid grey;
          border-right: 2px solid black;
        }
        
        .result, .nom
        {
          font-size: 0.7em;
        }
        
        .result_ignore
        {
          color: grey;
        }
        
        .nom
        {
          text-align: left;
          padding-left: 5px;
          padding-right: 5px;
        }
        
        .ligne_paire {
          background-color: #C0C0C0;
        }
        
        .ligne_impaire {
          background-color: inherit;
        }
  </style>
  <script language="javascript">

    function toggleCells(theClass) {

      //Create Array of All Cells
      var allCells=document.getElementsByTagName("td");

      //Loop through all tags using a for loop
      for (i=0; i<allCells.length; i++) {
        for (j=0; j<allCells[i].classList.length;j++) {
          if (allCells[i].classList[j] == theClass) {
            if (allCells[i].style.display == 'none') {
              allCells[i].style.display = 'table-cell'
            } else {
              allCells[i].style.display = 'none'
            }
          }
        }
      }
    }  
  </script>
  
  
  <?php
  
  echo "<h1 style='margin-top: 0px; margin-bottom: 0px;margin-left: 30px;'>Crédits ECTS acquis - Classe de ".$Classe->getNom()."</h1>";
  
  echo "<p style='margin-left: 30px;'>Afficher/masquer par année : ";
  

  $a = 1;
  $nb_a = count($annees);
  foreach($annees as $annee => $periodes) {
    $column_classname = column_classname($annee);
    echo "<a href=\"javascript:toggleCells('".column_classname($annee)."')\">".$annee."</a>";
    if ($a != $nb_a) echo " - ";
    $a++;
  }
  echo "</p>";
  
  echo "<table style='border: 1px solid black;border-collapse: collapse; margin: 20px;'>";
  echo "<tr>";
  echo "<td style='padding-left: 150px;'>&nbsp;</td>\n"; // Nom et prénom
  foreach($annees as $annee => $periodes) {
    $colspan_annee = 0;
    $column_classname = column_classname($annee);
    foreach($periodes as $periode) { 
      $colspan_annee = $colspan_annee + count($periode['matieres']);
    }
    echo "<td class='debut_annee fin_annee cell $column_classname' colspan='$colspan_annee'>";
    echo $annee;
    echo "</td>\n";
  }
  // La colonne pour le crédit global :
  echo "<td class='debut_annee'></td>";
  echo "</tr>";
  
  // Maintenant on affiche les périodes
  echo "<tr>\n";
  echo "<td></td>\n";
  foreach($annees as $annee => $periodes) {
    $column_classname = column_classname($annee);
    $m = 1;
    $nb = count($periodes);
    foreach($periodes as $periode) {
      $colspan_periode = count($periode['matieres']);
      if ($m == 1) { $styles = 'cell debut_annee';}else if($m == $nb){ $styles = 'cell fin_annee';} else {$styles = 'cell';}
      echo "<td class='$styles $column_classname' colspan='$colspan_periode'>";
      echo $periode['nom_periode'];
      echo "</td>\n";
      $m++;
    }
  }
  // La colonne pour le crédit global
  echo "<td class='debut_annee'></td>";
  echo "</tr>\n";
  
  // Et enfin on affiche les matières
  echo "<tr>\n";
  echo "<td class='lone_cell'>";
  echo "Classe :<br/><br/>";
  echo "<span style='font-weight: bold;'>";
  echo $Classe->getNom();
  echo "</span>";
  echo "</td>\n";
  $a = 1;
  $nb_a = count($annees);
  foreach($annees as $annee => $periodes) {
    $column_classname = column_classname($annee);
    $p = 1;
    $nb_p = count($periodes);
    foreach($periodes as $periode) {
      $m = 1;
      $nb_m = count($periode['matieres']);
      foreach($periode['matieres'] as $matiere) {
        if ($m == 1 && $p == 1) {
          $cellstyle = 'debut_annee cell';
        } else if ($m == 1 && $p != 1) {
          $cellstyle = 'first_cell';
        } else if ($p == $nb_p && $m == $nb_m) {
          $cellstyle = 'fin_annee cell';
        } else if ($p < $nb_p && $m == $nb_m) {
          $cellstyle = 'last_cell';
        } else {
          $cellstyle = 'central_cell';
        }
        echo "<td class='$cellstyle $column_classname' style='vertical-align: bottom;'>\n";
        $nom_complet_coupe = (mb_strlen($matiere) > 20)? urlencode(mb_substr($matiere,0,20)."...") : urlencode($matiere);
        echo "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("$nom_complet_coupe")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"$nom_complet_coupe\" />";
        echo "</td>\n";
        $m++;
      }
      $p++;
    }
    $a++;
  }
  
  echo "<td class='lone_cell' style='vertical-align: bottom;'>";
  echo "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Mention globale")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"Mention globale\" />";
  echo "</td>";
  echo "</tr>\n";
  
  
  // Boucle d'affichage du tableau
  $classe_bg = 'ligne_paire';
  foreach ($Eleves as $Eleve) {
    $total_ects = 0;
    $classe_bg = $classe_bg == 'ligne_paire' ? 'ligne_impaire' : 'ligne_paire';
    echo "<tr class='$classe_bg'>";
    
    // Nom Prénom
    echo "<td class='lone_cell nom'>";
    echo $Eleve->getNom().' '.$Eleve->getPrenom();
    echo "</td>";
    
    // Les résultats
    $a = 1;
    $nb_a = count($annees);
    foreach($annees as $annee => $periodes) {
      $column_classname = column_classname($annee);
      $p = 1;
      $nb_p = count($periodes);
      foreach($periodes as $num => $periode) {
        $m = 1;
        $nb_m = count($periode['matieres']);
        foreach($periode['matieres'] as $matiere) {
        if ($m == 1 && $p == 1) {
          $cellstyle = 'debut_annee cell';
        } else if ($m == 1 && $p != 1) {
          $cellstyle = 'first_cell';
        } else if ($p == $nb_p && $m == $nb_m) {
          $cellstyle = 'fin_annee cell';
        } else if ($p < $nb_p && $m == $nb_m) {
          $cellstyle = 'last_cell';
        } else {
          $cellstyle = 'central_cell';
        }
          
          if (array_key_exists($annee, $ignore_annees[$Eleve->getId()])) {
            $cellstyle = $cellstyle.' result_ignore';
            $ignore_ects = true;
          } else {
            $ignore_ects = false;
          }
          
          echo "<td class='$cellstyle result $column_classname'>";
          if (array_key_exists($annee, $resultats[$Eleve->getLogin()])
            and array_key_exists($num, $resultats[$Eleve->getLogin()][$annee])
            and array_key_exists($matiere, $resultats[$Eleve->getLogin()][$annee][$num])) {
          
            
            $valeur = $resultats[$Eleve->getLogin()][$annee][$num][$matiere]->getValeur();
            $mention = $resultats[$Eleve->getLogin()][$annee][$num][$matiere]->getMention();
            if ($annee == $gepiYear) {
              $mention_prof = $resultats[$Eleve->getLogin()][$annee][$num][$matiere]->getMentionProf();
            } else {
              $mention_prof = '';
            }
            if (is_numeric($valeur) && !$ignore_ects) $total_ects = $total_ects + $valeur;
            echo $valeur;
            if (($mention == null or $mention == '') and ($mention_prof != null or $mention_prof != '')) {
              echo '('.$mention_prof.')';
            } else {
              echo $mention;
            }
          } else {
            echo "&nbsp;";
          }
          echo "</td>";
          $m++;
        }
        $p++;
      }
      $a++;
    }
    // Le crédit global
    echo "<td class='lone_cell result'>";
    echo $total_ects;
    $credit_global = $Eleve->getCreditEctsGlobal();
    if ($credit_global) {
      echo $credit_global->getMention();
    } else {
      echo "&nbsp;";
    }
    echo "</td>";
    
    echo "</tr>";
    
    
  }
  
  echo "</table>";
  
}
require("../lib/footer.inc.php");
?>
