<?php

/*
 * $Id: eleves.php 2366 2008-09-10 12:26:23Z delineau $
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
$titre_page = "Outil d'initialisation de l'année : Importation des élèves";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// Utilisation de la classe LDAP chargee et configuree
$ldap = new LDAPServerScribe();

echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

//----***** STEP 2 *****-----//
    /*
    * L'étape 2 consiste à
     *  - Creer les periodes pour les classes recemment importees
     *  - Associer les eleves a leurs classes
    */
if ($_POST['step'] == "2") {
	check_token(false);

    // On se connecte au LDAP
    $ldap->connect();

    if (!isset($_SESSION['classesamodifier'])) {
        echo "Erreur : aucune p&eacute;riodes &agrave; cr&eacute;er";
        require("../lib/footer.inc.php");
    }
    else {
        // Si on a bien les donnees dans la session, on peut continuer

        /*
        * Vidage des tables qui le necessitent
        */

        /*
        * Affichage du résumé de l'étape 2
        */
        echo "<h3> Résumé de l'étape 2 </h3>";

        /*
         * Recuperation de toutes les classes pour lesquelles l'utilisateur a choisi de creer des periodes
         * et Creation du nombre de periodes souhaite
         */
        $classes_concernees = $_SESSION['classesamodifier'];
        unset($_SESSION['classesamodifier']);
        foreach($classes_concernees as $key => $val) {
            $indice = "classe".$key;
            // $key contient l'id de la classe, et $val son "nom" (ou numéro publique..)
            // Si un nombre de periodes a ete selectionne pour cette classe, on cree les periodes
            // Pour chaque periode, jusqu'au nombre souhaite (REVOIR pour choix non faits...)
            for ($i=1; $i<=$_POST[$indice]; $i++) {
                $req_insertion_periode = "INSERT INTO periodes VALUES ('P$i','$i','T', '$key',NULL,NULL)";
                mysql_query($req_insertion_periode);
                // Si tout s'est bien deroule
                if (mysql_errno() != 0) {
                    die("Une erreur s'est produite lors de la creation des p&eacute;riodes");
                }
            }

            if (isset($_POST[$indice])) {
                echo "<p>Creation de ".$_POST[$indice]." p&eacute;riode(s) pour la classe $val</p>";
            }
        }

        /*
         * Association des eleves a leur classes (pour chaque classe qui possede des periodes)
         * car on ne peut pas associer d'eleve a une classe sans periode
         */
        //Il faut reprendre les liens dans j_eleves_classes pour savoir dans quelle classe est chaque eleve
        // Et creer une ligne par periode de la classe
        // Peut-etre faire choisir à l'admin d'ajouter les les eleves pour toutes les periodes, ou juste pour la premiere

        // On cree un critere pour ramener tous les liens eleve_classe/periode qui sont temporaires
        // (c'est a dire qui ont un leur periode a 0 = pas de periode associe)
        echo "<br>";
        $crit = new Criteria();
        $crit->add(JEleveClassePeer::PERIODE, 0);
        // Construction d'un tableau contenant les id des classes pour lesquelles l'utilisateur
        // a defini un nombre de periodes (on ne va affecter les eleves QUE pour ces classes la)
        //$classes_avec_periodes_definies =
        //$crit->add(JEleveClassePeer::ID_CLASSE, );
        $relations_eleves_classes = JEleveClassePeer::doSelect($crit);
        // Pour chaque relation eleve_classe/periode
        foreach($relations_eleves_classes as $relation_ec) {
            // Recuperation des periodes correspondantes a la classe de l'eleve
            echo "recuperation des periodes pour la classe ".$relation_ec->getIdClasse()." <br>";
            $req_periodes_classe = "SELECT * FROM periodes WHERE id_classe = ".$relation_ec->getIdClasse();
            $periodes_de_la_classe = mysql_query($req_periodes_classe);
            // Si on trouve des periodes,
            echo mysql_num_rows($periodes_de_la_classe)." periodes trouvees<br>";
            if (mysql_num_rows($periodes_de_la_classe) > 0) {
                // On met d'abord a jour la relation temporaire eleve_classe deja presente
                // (en lui affectant le numero de premiere periode (normalement 1...))
                // Pour cela on recupere separement la premiere periode
                $periodes_de_la_classe_row = mysql_fetch_object($periodes_de_la_classe);
                $relation_ec_a_modifier = JEleveClassePeer::retrieveByPK($relation_ec->getLogin(), $periodes_de_la_classe_row->id_classe, 0);
                //echo "relation a modifier : ".$relation_ec_a_modifier->getLogin()
                //." (Periode numero ".$relation_ec_a_modifier->getPeriode()
                //."  Classe : ".$relation_ec_a_modifier->getClasse()->getId()."(".$relation_ec_a_modifier->getClasse()->getClasse().") )<br>";
                //echo "Mise de la periode a : ".$periodes_de_la_classe_row->num_periode."<br>";
                //$relation_ec_a_modifier->setPeriode($periodes_de_la_classe_row->num_periode);
                //$relation_ec_a_modifier->save();
                $new_relation = new JEleveClasse();
                $new_relation->setClasse($relation_ec->getClasse());
                $new_relation->setLogin($relation_ec->getLogin());
                $new_relation->setPeriode(1);
                $new_relation->save();

                // Suppression de la relation temporaire (voir pourquoi modif impossible !)
                $relation_ec_a_modifier->delete();

                //$relation_ec->setPeriode($periodes_de_la_classe_row->num_periode);
                //$relation_ec->save();
                //echo "mise a jour du lien temporaire effectuee pour ".$relation_ec->getLogin()
                //        ." (Periode numero ".$periodes_de_la_classe_row->num_periode
                //        ."  Classe : ".$periodes_de_la_classe_row->id_classe.")<br>";

                // Ensuite, pour chaque periode restante, on ajoute une relation eleve / periode de la classe
                while ($periodes_de_la_classe_row = mysql_fetch_object($periodes_de_la_classe)) {
                    $nouvelle_relation_ep = new JEleveClasse();
                    $eleve_concerne = ElevePeer::retrieveByLOGIN($relation_ec->getLogin());
                    $nouvelle_relation_ep->setEleve($eleve_concerne); // On passe l'objet eleve
                    $classe_concerne = ClassePeer::retrieveByPK($relation_ec->getIdClasse());
                    $nouvelle_relation_ep->setClasse($classe_concerne); // On passe l'objet classe
                    $nouvelle_relation_ep->setPeriode($periodes_de_la_classe_row->num_periode); // On passe le numero de periode
                    $nouvelle_relation_ep->save();
                }
            }
            // Si on ne trouve pas de periodes pour cette association temporaire, on ne fait rien
        }

        // Eleves associees a leurs classes ? pas d'erreur ?

        echo "<br>";
        echo "<form enctype='multipart/form-data' action='etape3.php' method=post>";
		//echo add_token_field();
        echo "<input type=hidden name='step' value='2'>";
        echo "<input type=hidden name='record' value='no'>";

        echo "<p>Passer &agrave; l'&eacute;tape 3 :</p>";
        echo "<input type='submit' value='Etape 3'>";
        echo "</form>";
    }
}

else {
    // Affichage de la page des explications de l'etape 2 (aucune donnee postee)
    // La deuxieme étape consiste a creer les periodes pour les classes et a affecter les eleves aux classes

    echo "<br><p>L'&eacute;tape 2 vous permet de cr&eacute;er les p&eacute;riodes pour les classes.</p>";
    echo "<p>Une fois celles-ci cr&eacute;&eacute;es, les &eacute;l&egrave;ves seront affect&eacute;s &agrave; toutes les p&eacute;riodes de leur(s) classe(s)</p>";

    $req_classes_sans_periodes = "select id, classe from classes WHERE id NOT IN(Select id_classe from periodes) ORDER BY classe ASC";
    $res = mysql_query($req_classes_sans_periodes);
    if (mysql_errno() != 0) {
        echo "<br>Impossible de r&eacute;cup&eacute;rer les classes dans la base GEPI.<br>";
        require("../lib/footer.inc.php");
        die();
    }
    else {
        // Si des classes virtuelles sont trouvees (= classes sans periodes)
        if (mysql_num_rows($res) != 0) {
            echo "<p>Voici la liste des classes présentes dans GEPI pour lesquelles aucune p&eacute;riode n'a &eacute;t&eacute; d&eacute;finie,<br>";
            echo "<br><p><b>Choisissez pour chaque classe le nombre de p&eacute;riodes : </b></p>";
            echo "<form enctype='multipart/form-data' action='etape2.php' method=post>";
			echo add_token_field();
            echo "<input type=hidden name='step' value='2'><br>";
            $classes_concernees = array();
            while($row = mysql_fetch_object($res)) {
                // On stocke l'identifiant technique (auto_inc mysql) en indice, et le nom de la classe en valeur
                $classes_concernees[$row->id] = $row->classe;
                echo "<p>Classe ". $row->classe." : ";
                echo "<input type=\"radio\" name=\"classe".$row->id."\" value=\"1\"> 1&nbsp;&nbsp;\n";
                echo "<input type=\"radio\" name=\"classe".$row->id."\" value=\"2\"> 2&nbsp;&nbsp;\n";
                echo "<input type=\"radio\" name=\"classe".$row->id."\" value=\"3\"> 3&nbsp;&nbsp;\n";
                echo "<input type=\"radio\" name=\"classe".$row->id."\" value=\"4\"> 4</p>\n";
            }
            // On sauvegarde dans la session les classes qui étaient affichées à l'utilisateur,
            $_SESSION['classesamodifier'] = $classes_concernees;

            echo "<br><p>Validation du choix des p&eacute;riodes :</p>";
            echo "<input type='submit' value='Je suis sûr'>";
            echo "</form>";
            echo "<br>";

            require("../lib/footer.inc.php");
        }
        else {
            // Si aucune classe virtuelle trouvee : rien a faire
            echo "<br><p>Aucune classe sans p&eacute;riode n'a &eacute;t&eacute; trouv&eacute;e.</p>";
            echo "<p>Aucune action &agrave; effectuer.</p>";
            require("../lib/footer.inc.php");
        }
    }

}

?>
