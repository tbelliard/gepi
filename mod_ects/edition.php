<?php
/*
 * $Id: saisie_avis.php 2147 2008-07-23 09:01:04Z tbelliard $
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
//**************** EN-TETE *****************
$titre_page = "Edition des documents ECTS";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// On teste si un professeur principal peut effectuer l'édition
if (($_SESSION['statut'] == 'professeur') and $gepiSettings["GepiAccesEditionDocsEctsPP"] !='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

// On teste si le service scolarité peut effectuer la saisie
if (($_SESSION['statut'] == 'scolarite') and $gepiSettings["GepiAccesEditionDocsEctsScolarite"] !='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

// Les autres statuts n'ont de toute façon pas accès à ce fichier (cf. table 'droits').

$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] : false);

// Si aucune classe n'a été choisie, on affiche la liste des classes accessibles
if (!$id_classe) {

    echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

    if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {

        // On ne sélectionne que les classes qui ont au moins un enseignement ouvrant à crédits ECTS
        if($_SESSION['statut']=='scolarite'){
            $call_classe = mysql_query("SELECT DISTINCT c.*
                                        FROM classes c, periodes p, j_scol_classes jsc, j_groupes_classes jgc
                                        WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' AND c.id=jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
        } else {
            $call_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc WHERE p.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
        }

        $nombre_classe = mysql_num_rows($call_classe);
        if($nombre_classe==0){
            echo "<p>Aucune classe avec paramétrage ECTS ne vous est attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
        }
    } else {
        $call_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc, j_groupes_classes jgc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE)");
        $nombre_classe = mysql_num_rows($call_classe);
        if ($nombre_classe == "0") {
            echo "Vous n'êtes pas ".$gepiSettings['gepi_prof_suivi']." dans des classes ayant des enseignements ouvrant droits à des ECTS.";
        }
    }

    echo "<p>Cliquez sur la classe pour laquelle vous souhaitez éditer les documents ECTS :</p>\n";
    echo "<br/><p><a href='edition.php?id_classe=all'>Toutes les classes</a></p>";

    $i = 0;
    unset($tab_lien);
    unset($tab_txt);
    $nombreligne = mysql_num_rows($call_classe);
    while ($i < $nombreligne){
        $tab_lien[$i] = "edition.php?id_classe=".mysql_result($call_classe, $i, "id");
        $tab_txt[$i] = mysql_result($call_classe, $i, "classe");
        $i++;

    }
    tab_liste($tab_txt,$tab_lien,3);
    echo "<p><br /></p>\n";

} else {
    // Si on arrive là, c'est qu'une classe a été choisie (ou bien toutes les classes).
    // On affiche le formulaire d'édition des options
    echo "<p class=bold><a href=\"edition.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

    echo "<form method='post' action='../mod_ooo/documents_ects.php' id='generer_documents' class='centre_texte'>\n";
    echo '<input type="hidden" name="id_classe" value="'.$id_classe.'"/>';
    
    if ($id_classe == 'all') {
        // On a sélectionné la totalité des classes.
        if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {
            // On ne sélectionne que les classes qui ont au moins un enseignement ouvrant à crédits ECTS
            if($_SESSION['statut']=='scolarite'){
                $call_classes = mysql_query("SELECT DISTINCT c.*
                                            FROM classes c, periodes p, j_scol_classes jsc, j_groupes_classes jgc
                                            WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' AND c.id=jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
            } else {
                $call_classes = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc WHERE p.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
            }
        } else {
            $call_classes = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc, j_groupes_classes jgc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE)");
        }
        $noms_classes = '';
        $nb_classes = mysql_num_rows($call_classes);
        for($i=0;$i<$nb_classes;$i++) {
            $noms_classes .= mysql_result($call_classes, $i, 'classe');
            if ($i != $nb_classes-1) {
                $noms_classes .= ', ';
            }
        }

        echo '<h3>Classes de '.$noms_classes.'</h3>';

        // On récupère les années archivées, pour sélectionner celle qui correspond à l'année dernière :
        $annees = mysql_query('SELECT DISTINCT annee FROM archivage_ects');
        $nb_annees = mysql_num_rows($annees);
        if ($nb_annees == 0) {
            echo "<p>Attention ! Aucun crédit ECTS n'est actuellement présent dans les tables d'archivage. Les informations
                    des semestres 1 et 2 seront donc absentes.</p>";
        } else {
            echo "<select size=\"1\" id=\"select_annee_derniere\" name=\"annee_derniere\">\n";
            for ($a=0;$a<$nb_annees;$a++) {
                $annee = mysql_result($annees, $a);
              echo "<option value='".$annee."'>".$annee."</option>\n";
            }
            echo "</select>\n";
        }

    } else {
        // On appelle une seule classe. On vérifie que l'utilisateur a bien le droit sur cette classe.
        if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {
            // On ne sélectionne que les classes qui ont au moins un enseignement ouvrant à crédits ECTS
            if($_SESSION['statut']=='scolarite'){
                $call_classe = mysql_query("SELECT c.*
                                            FROM classes c, j_scol_classes jsc
                                            WHERE c.id = jsc.id_classe AND jsc.login='".$_SESSION['login']."' AND jsc.id_classe = '".$id_classe."'");
            } else {
                $call_classe = mysql_query("SELECT c.* FROM classes c WHERE c.id = '".$id_classe."'");
            }
        } else {
            $call_classe = mysql_query("SELECT c.* FROM classes c, j_eleves_professeurs jep, j_groupes_classes jgc
                                        WHERE
                                            c.id = jep.id_classe AND
                                            c.id = jgc.id_classe AND
                                            c.id = '".$id_classe."' AND
                                            jgc.saisie_ects = TRUE AND
                                            jep.professeur='" . $_SESSION['login'] . "'");
        }
        if (mysql_num_rows($call_classe) == 0) {
            echo 'Erreur avec la sélection de la classe. Avez-vous bien les droits sur cette classe ?';
            die();
        } else {
            $Classe = ClassePeer::retrieveByPK($id_classe);

            echo '<h3>Classe de '.$Classe->getClasse().'</h3>';

            // On propose de générer les documents pour toutes la classe, ou bien pour un seul élève
            echo "<div>\n";
                echo "<input type=\"radio\" name=\"choix_edit\" id='choix_edit_1' value=\"1\" checked='checked' />\n";
                echo "<label for='choix_edit_1' class='curseur_pointeur'>\n";
                    echo "Générer les documents ECTS de tous les élèves de la classe\n";
                echo "</label>\n";
            echo "</div>\n";

            echo "<div>\n";
                echo "<input type=\"radio\" name=\"choix_edit\" id='choix_edit_2' value=\"2\" />\n";
                echo "<label for='choix_edit_2' class='curseur_pointeur'>\n";
                    echo "Générer uniquement les documents ECTS de l'élève sélectionné ci-contre : \n";
                echo "</label>\n";
                echo "<select size=\"1\" id=\"select_login_eleve\" name=\"login_eleve\" onchange=\"document.getElementById('choix_edit_2').checked=true;\">\n";

                // On récupère la liste des élèves s'il s'agit d'un professeur principal
                if ($_SESSION['statut'] == 'professeur') {
                    $Eleves = $Classe->getElevesByProfesseurPrincipal($_SESSION['login']);
                } else {
                    $Eleves = $Classe->getEleves('1'); // On prend la première période, qui sert de référence.
                }
                foreach ($Eleves as $Eleve) {
                    echo "<option value='".$Eleve->getLogin()."'>".$Eleve->getNom()." ".$Eleve->getPrenom()."</option>\n";
                }
            echo "</select>\n";
        echo "</div>\n";


        echo "<p>Document fait à ";
        echo "<input type='text' name='lieu_edition' value='".$gepiSettings['gepiSchoolCity']."'/> ";
        echo "le ";
        echo "<input type='text' name='date_edition' value='".date('d/m/Y')."' /></p>";
        }

    }

    // On propose maintenant de choisir les documents qui doivent être générés pour chaque élève
    echo "<h3>Documents à générer</h3>";
    echo "<p>Cochez les documents que vous souhaitez générer :</p>";
    echo "<div>\n";
        echo "<input type=\"checkbox\" name=\"page_garde\" id='choix_page_garde' value=\"page_garde\" checked='checked' />\n";
        echo "<label for='choix_page_garde' class='curseur_pointeur'>\n";
            echo "La page de garde\n";
        echo "</label>\n";
    echo "</div>\n";
    echo "<div>\n";
        echo "<input type=\"checkbox\" name=\"releve\" id='choix_releve' value=\"releve\" checked='checked' />\n";
        echo "<label for='choix_releve' class='curseur_pointeur'>\n";
            echo "Le relevé de crédits\n";
        echo "</label>\n";
    echo "</div>\n";
    echo "<div>\n";
        echo "<input type=\"checkbox\" name=\"attestation\" id='choix_attestation' value=\"attestation\" checked='checked' />\n";
        echo "<label for='choix_attestation' class='curseur_pointeur'>\n";
            echo "L'attestation de parcours suivi\n";
        echo "</label>\n";
    echo "</div>\n";
    echo "<div>\n";
        echo "<input type=\"checkbox\" name=\"description\" id='choix_description' value=\"description\" checked='checked' />\n";
        echo "<label for='choix_description' class='curseur_pointeur'>\n";
            echo "L'annexe explicative\n";
        echo "</label>\n";
    echo "</div>\n";

    echo "<br/>";
    echo '<input type="submit" name="Valider" value="Générer les documents" style="margin-left: 50px;"/>';
    echo "</form>";
}
require("../lib/footer.inc.php");
?>