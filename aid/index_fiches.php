<?php
/*
 * $Id$
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$indice_aid = $_GET["indice_aid"];
// Vérification de la validité de $indice_aid et $aid_id
if (!VerifAidIsAcive($indice_aid,"")) {
    echo "<p>Vous tentez d'accéder à des outils qui ne sont pas activés. veuillez contacter l'administrateur.</p></body></html>";
    die();
}


$nom_projet = sql_query1("select nom from aid_config where indice_aid='".$indice_aid."'");
$feuille_presence = sql_query1("select feuille_presence from aid_config where indice_aid='".$indice_aid."'");

//**************** EN-TETE *********************
if ((isset($_GET['action'])) and ($_GET['action'] == "liste_presence"))
    unset ($titre_page);
else
    $titre_page = "Outils de visualisation ".$nom_projet;
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// Accueil
if (!isset($_GET['action'])) {
    echo "<p class=bold>";
    echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
    echo "</p>";
    echo "<b>Votre choix : </b>\n";
    echo "<ul>";
    echo "<li><a href=\"index_fiches.php?action=liste_projet&amp;indice_aid=".$indice_aid."\"><b>Tableau de toutes les fiches ".$nom_projet."</b> (professeurs responsables, élèves responsables";
    if ($test_salle != 0)
        echo ", salles";
    if (($feuille_presence == 'y') and ($_SESSION["statut"] != "eleve") and ($_SESSION["statut"] != "responsable"))
        echo ", feuilles de présence";
    echo ")</a></li>\n";
    echo "<li><a href=\"visu_fiches.php?indice_aid=".$indice_aid."\"><b>Tableau de toutes les fiches ".$nom_projet."</b> (résumé, productions attendues, ...)</a></li>\n";
    echo "<li><a href=\"index_fiches.php?action=liste_eleves&amp;indice_aid=".$indice_aid."\"><b>Liste de tous les élèves</b> (classements possibles par nom, prénom, classe, projet)</a></li>\n";
    if (($_SESSION["statut"] == "administrateur") or ($_SESSION["statut"] == "cpe"))
        echo "<li><a href=\"index_fiches.php?action=liste_eleves_sans_projet&amp;indice_aid=".$indice_aid."\"><b>Liste des élèves non affectés</b></a></li>\n";
    if (($feuille_presence == 'y') and ($_SESSION["statut"] != "eleve") and ($_SESSION["statut"] != "responsable"))
        echo "<li><a href=\"index_fiches.php?action=liste_presence&amp;indice_aid=".$indice_aid."\"  target=\"_blank\"><b>Edition de toutes les feuilles de présence</b></a></li>\n";

    echo "</ul>\n";
}

// Affichage de la liste des projets
if ((isset($_GET['action'])) and ($_GET['action']=="liste_projet")) {
    echo "<p class=bold>";
    echo "<a href=\"./index_fiches.php?indice_aid=".$indice_aid."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
    echo "</p>";

    echo "<h2>Liste des ".$nom_projet."</h2>";
    $call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
    $nom_aid = @mysql_result($call_data, 0, "nom");
    $order_by = isset($_POST["order_by"]) ? $_POST["order_by"] : (isset($_GET["order_by"]) ? $_GET["order_by"] : 'nom');
    $calldata = mysql_query("SELECT * FROM aid WHERE indice_aid='$indice_aid' ORDER BY $order_by");
    $nombreligne = mysql_num_rows($calldata);
    echo "<table width=\"100%\" cellpadding=\"3\" border=\"1\">\n";
    echo "<tr>\n";
    echo "<td>N°</td>\n";
    echo "<td>Nom du projet ".$nom_projet."</td>\n";
    echo "<td>Professeur(s) responsable(s)</td>\n";
    echo "<td>Elève(s) responsable(s)</td>\n";
    // On n'affiche la colonne "salle" uniquement si ce champ est utilisé.
    If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso1',"R")) {
        echo "<td>".LibelleChampAid("perso1")."</td>\n";
    }
    If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso2',"R")) {
        echo "<td>".LibelleChampAid("perso2")."</td>\n";
    }
    If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso3',"R")) {
        echo "<td>".LibelleChampAid("perso3")."</td>\n";
    }
    echo "</tr>\n";
    $i = 0;
    while ($i < $nombreligne){
        $aid_id = @mysql_result($calldata,$i,"id");
        $aid_nom = @mysql_result($calldata,$i,"nom");
        $aid_num = @mysql_result($calldata,$i,"numero");
        $perso1 = @mysql_result($calldata,$i,"perso1");
        $perso2 = @mysql_result($calldata,$i,"perso2");
        $perso3 = @mysql_result($calldata,$i,"perso3");

        if ($aid_num =='') {$aid_num='&nbsp;';}
        if ($perso1 == "") $perso1 = "-";
        if ($perso2 == "") $perso2 = "-";
        if ($perso3 == "") $perso3 = "-";

        // Profs responsables
        $liste_profs = "";
        $call_liste_data = mysql_query("SELECT u.login, u.prenom, u.nom, u.email, u.show_email
        FROM utilisateurs u, j_aid_utilisateurs j
        WHERE (j.id_aid='".$aid_id."' and u.login=j.id_utilisateur and j.indice_aid='$indice_aid')
        order by u.nom, u.prenom");
        $nombre_prof = mysql_num_rows($call_liste_data);
        $j = "0";
        while ($j < $nombre_prof) {
            if ($liste_profs != "") $liste_profs .= "<br />";
            $nom_prof = @mysql_result($call_liste_data, $j, "nom");
            $prenom_prof = @mysql_result($call_liste_data, $j, "prenom");
            $email_prof = @mysql_result($call_liste_data, $j, "email");
            $show_email_prof = @mysql_result($call_liste_data, $j, "show_email");
          if(($email_prof!="") AND ($show_email_prof == "yes") AND
		    	(($_SESSION['statut'] == "responsable" AND
		    		(getSettingValue("GepiAccesEquipePedaEmailParent") == "yes"
		    			OR
		    		 (getSettingValue("GepiAccesCpePPEmailParent") == "yes" AND mysql_num_rows($res_pp)>0)
		    		 )
        		) OR (
				  $_SESSION['statut'] == "eleve" AND
		    		(getSettingValue("GepiAccesEquipePedaEmailEleve") == "yes"
		    			OR
		    		 (getSettingValue("GepiAccesCpePPEmailEleve") == "yes" AND mysql_num_rows($res_pp)>0)
		    		 )
		    	)
		    	))
                $nom_prenom = "<a href='mailto:$email_prof?".urlencode("subject=[".$nom_projet."]")."'>".$nom_prof." ".$prenom_prof."</a>";
            else
                $nom_prenom = $nom_prof." ".$prenom_prof;

            $liste_profs .= "<b>".$nom_prenom."</b>";
            $j++;
        }
        if ($liste_profs == "") $liste_profs = "-";

        // Eleves responsables
        $call_liste_data = mysql_query("SELECT e.login, e.nom, e.prenom
        FROM eleves e, j_aid_eleves_resp j
        WHERE (j.id_aid='$aid_id' and e.login=j.login and j.indice_aid='$indice_aid')
        ORDER BY nom, prenom");
        $liste_eleves = "";
        $nombre = mysql_num_rows($call_liste_data);
        $j = "0";
        while ($j < $nombre) {
            $login_eleve = mysql_result($call_liste_data, $j, "login");
            $nom_eleve = mysql_result($call_liste_data, $j, "nom");
            $prenom_eleve = @mysql_result($call_liste_data, $j, "prenom");
            $call_classe = mysql_query("SELECT c.classe, c.id FROM classes c, j_eleves_classes j WHERE (j.login = '$login_eleve' and j.id_classe = c.id) order by j.periode DESC");
            $classe_eleve = @mysql_result($call_classe, '0', "classe");
            $id_classe_eleve = @mysql_result($call_classe, '0', "id");
            if ($liste_eleves != "") $liste_eleves .= "<br />";
            if (($_SESSION["statut"] != "eleve") and ($_SESSION["statut"] != "responsable"))
                $liste_eleves .="<b>".$nom_eleve." ".$prenom_eleve."</b>, <a href='../groupes/visu_profs_class.php?id_classe=".$id_classe_eleve."' target='_blank'>".$classe_eleve."</a>";
            else
                $liste_eleves .="<b>".$nom_eleve." ".$prenom_eleve."</b>, ".$classe_eleve;
            $j++;
        }
        if ($liste_eleves == "") $liste_eleves = "-";

        echo "<tr>\n<td><span class='medium'><b>$aid_num</b></span></td>\n";
        echo "<td><span class='medium'><b>$aid_nom</b></span>";

    if (($feuille_presence == 'y') and ($_SESSION["statut"] != "eleve") and ($_SESSION["statut"] != "responsable"))
            echo "<br /><span class='medium'><a href='index_fiches.php?action=liste_presence&amp;aid_id=".$aid_id."&amp;indice_aid=".$indice_aid."' target=\"_blank\" title=\"La feuille de présence s'ouvre dans une nouvelle fenêtre.\"  ><i>Feuille de présence</i></a></span>";
        echo "<br /><span class='medium'><i>Fiche complète : </i>";
        echo "<a href='modif_fiches.php?aid_id=$aid_id&amp;indice_aid=$indice_aid&amp;retour=index_fiches.php' >Visualiser</a></span>";
        if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'','')) {
            echo " - <a href='modif_fiches.php?aid_id=$aid_id&amp;indice_aid=$indice_aid&amp;action=modif&amp;retour=index_fiches.php'>Modifier</a></span>";
        }
        echo "</td>\n";
        echo "<td><span class='medium'>".$liste_profs."</span></td>\n";
        echo "<td><span class='medium'>".$liste_eleves."</span></td>\n";
        if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso1',''))
            echo "<td><span class='medium'>".$perso1."</span></td>\n";
        if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso2',''))
            echo "<td><span class='medium'>".$perso2."</span></td>\n";
        if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso3',''))
            echo "<td><span class='medium'>".$perso3."</span></td>\n";

        echo "</tr>";
    $i++;
    }
    echo "</table>\n";
}

// Affichage de la feuille de présence
if ((isset($_GET['action'])) and ($_GET['action']=="liste_presence")) {
    $nom_aid = sql_query1("SELECT nom FROM aid_config WHERE indice_aid = '$indice_aid'");
    if (!isset($_GET['aid_id']))
        $calldata = mysql_query("SELECT * FROM aid WHERE indice_aid='$indice_aid' ORDER BY nom");
    else
        $calldata = mysql_query("SELECT * FROM aid WHERE indice_aid='".$indice_aid."' and id='".$_GET['aid_id']."' ORDER BY nom ");
    $nombreligne = mysql_num_rows($calldata);
    $i = 0;
    while ($i < $nombreligne){
        $aid_id = @mysql_result($calldata, $i, "id");
        $aid_nom = @mysql_result($calldata, $i, 'nom');
        $perso1 = @mysql_result($calldata, $i, 'perso1');
        $perso2 = @mysql_result($calldata, $i, 'perso2');
        $perso3 = @mysql_result($calldata, $i, 'perso3');
        if ($perso1 == "") $perso1 = "-";
        if ($perso2 == "") $perso2 = "-";
        if ($perso3 == "") $perso3 = "-";
        // Profs responsables
        $call_liste_data = mysql_query("SELECT u.civilite, u.nom
            FROM utilisateurs u, j_aid_utilisateurs j
            WHERE (j.id_aid='".$aid_id."' and u.login=j.id_utilisateur and j.indice_aid='$indice_aid')
            order by u.nom, u.prenom");
        $nombre_prof = mysql_num_rows($call_liste_data);
        if ($nombre_prof == 1)
            $liste_profs = "Professeur responsable : ";
        else if ($nombre_prof > 1)
            $liste_profs = "Professeurs responsables : ";
        else
            $liste_profs = "Professeurs responsables : Aucun";
        $j = "0";
        while ($j < $nombre_prof) {
            $nom_prof = @mysql_result($call_liste_data, $j, "nom");
            $civilite = @mysql_result($call_liste_data, $j, "civilite");
            $liste_profs .= "<b>".$civilite." ".$nom_prof."</b>";
            if ($j < $nombre_prof-1) $liste_profs .= ", ";
            $j++;
        }

        // Eleves responsables
        $call_liste_data = mysql_query("SELECT e.login, e.nom, e.prenom
            FROM eleves e, j_aid_eleves_resp j
            WHERE (j.id_aid='".$aid_id."' and e.login=j.login and j.indice_aid='".$indice_aid."')
            ORDER BY nom, prenom");
        $nombre_eleves = mysql_num_rows($call_liste_data);
        if ($nombre_eleves == 1)
            $liste_eleves = "Eleve responsable : ";
        else if ($nombre_eleves > 1)
            $liste_eleves = "Eleves responsables : ";
        else
            $liste_eleves = "Eleves responsables : Aucun";
        $j = "0";
        while ($j < $nombre_eleves) {
            $login_eleve = mysql_result($call_liste_data, $j, "login");
            $nom_eleve = mysql_result($call_liste_data, $j, "nom");
            $prenom_eleve = @mysql_result($call_liste_data, $j, "prenom");
            $call_classe = mysql_query("SELECT c.classe FROM classes c, j_eleves_classes j WHERE (j.login = '$login_eleve' and j.id_classe = c.id) order by j.periode DESC");
            $classe_eleve = @mysql_result($call_classe, '0', "classe");
            $liste_eleves .="<b>".$nom_eleve." ".$prenom_eleve."</b> (".$classe_eleve.")";
            if ($j < $nombre_eleves-1) $liste_eleves .= ", ";
            $j++;
        }
        echo "<p class='grand'>".$nom_projet." : ".$aid_nom."</p>\n";
        if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso1','W'))
            echo LibelleChampAid("perso1")." : ".$perso1."<br />";
        if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso2','W'))
            echo LibelleChampAid("perso2")." : ".$perso2."<br />";
        if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso3','W'))
            echo LibelleChampAid("perso3")." : ".$perso1."<br />";
        echo $liste_profs."<br />";
        echo $liste_eleves;
        echo "<p><span class = 'bold'>Séance ".$nom_projet." du .....................................................................</span>\n";

        // appel de la liste des élèves de l'AID :
        $call_liste_data = mysql_query("SELECT e.login, e.nom, e.prenom
        FROM eleves e, j_aid_eleves j
        WHERE (j.id_aid='".$aid_id."' and e.login=j.login and j.indice_aid='$indice_aid') ORDER BY nom, prenom");
        echo "<table width=\"95%\" border=\"1\" cellpadding=\"8\">\n";
        echo "<tr><td width=\"50%\"><b>Nom Prénom</b></td><td><b>Absences / Retard (début de séance)</b></td><td><b>Absences / Retard (fin de séance)</b></td></tr>";
        $nombre = mysql_num_rows($call_liste_data);
        $j = "0";
        while ($j < $nombre) {
            $vide = 0;
            $login_eleve = mysql_result($call_liste_data, $j, "login");
            $nom_eleve = mysql_result($call_liste_data, $j, "nom");
            $prenom_eleve = @mysql_result($call_liste_data, $j, "prenom");
            $call_classe = mysql_query("SELECT c.classe FROM classes c, j_eleves_classes j WHERE (j.login = '$login_eleve' and j.id_classe = c.id) order by j.periode DESC");
            $classe_eleve = @mysql_result($call_classe, '0', "classe");
            echo "<tr><td>".$nom_eleve." ".$prenom_eleve." (".$classe_eleve.")</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
            $j++;
        }
        echo "</table>\n";
        if ($vide == 1) {
            echo "<br /><font color = red>Il n'y a pas actuellement d'élèves dans cette AID !</font>\n";
        } else {
            echo "<table width=\"90%\" border=\"0\" cellpadding=\"5\">\n";
            echo "<tr><td width=\"50%\">&nbsp;</td><td><b>Signature du (des) professeur(s) responsable(s)</b></td></tr>\n";
            echo "</table>";
        }
        if ($nombreligne > 1)  echo "<p class='saut'>&nbsp;</p>\n";
        $i++;
    }

}


// Affichage de la liste des élèves
if ((isset($_GET['action'])) and ($_GET['action']=="liste_eleves")) {
    $order_by2 = isset($_POST["order_by2"]) ? $_POST["order_by2"] : (isset($_GET["order_by2"]) ? $_GET["order_by2"] : 'nom');
    if ($order_by2 == "nom") $order_by2 = "e.nom, e.prenom";
    if ($order_by2 == "classe") $order_by2 = "c.classe, e.nom, e.prenom";
    if ($order_by2 == "projet") $order_by2 = "a.nom, e.nom, e.prenom";
    echo "<p class=bold>";
    echo "<a href=\"./index_fiches.php?indice_aid=".$indice_aid."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
    echo "</p>";
    echo "<h2>Liste des élèves</h2>";
    echo "Cliquez sur l'en-tête de la première ligne pour classer les élèves par nom et prénom, classe ou projet<br /><br />";

    $call_liste_data = mysql_query("SELECT distinct e.nom, e.prenom, c.classe, c.id, a.nom, j.id_aid, e.login
    FROM eleves e, j_aid_eleves j, classes c, j_eleves_classes jec, aid a
    WHERE (
    e.login=j.login and
    j.indice_aid='".$indice_aid."' and
    jec.login = j.login and
    jec.id_classe = c.id and
    a.id = j.id_aid
    ) ORDER BY ".$order_by2);

    echo "<table width=\"90%\" border=\"1\" cellpadding=\"3\">\n";
    echo "<tr>
    <td width=\"50%\"><b><a href='index_fiches.php?order_by2=nom&amp;action=liste_eleves&amp;indice_aid=".$indice_aid."'>Nom Prénom</a></b></td>\n
    <td width=\"50%\"><b>Identifiant</b></td>\n
    <td><b><a href='index_fiches.php?order_by2=classe&amp;action=liste_eleves&amp;indice_aid=".$indice_aid."'>Classe</a></b></td>\n
    <td><b><a href='index_fiches.php?order_by2=projet&amp;action=liste_eleves&amp;indice_aid=".$indice_aid."'>".$nom_projet."</a></b></td>\n";
    if ($test_salle != 0)
        echo "<td><b>Salle</b></td>\n";
    echo "</tr>\n";
    $nombre = mysql_num_rows($call_liste_data);
    $i = "0";
    $vide = 1;
    while ($i < $nombre) {
        $vide = 0;
        $login_eleve =  mysql_result($call_liste_data, $i, "e.login");
        $nom_eleve = mysql_result($call_liste_data, $i, "e.nom");
        $prenom_eleve = @mysql_result($call_liste_data, $i, "e.prenom");
        $classe_eleve = @mysql_result($call_liste_data, $i, "c.classe");
        $id_classe_eleve = @mysql_result($call_liste_data, $i, "c.id");
        $aid_eleve = @mysql_result($call_liste_data, $i, "a.nom");
        $id_aid = @mysql_result($call_liste_data, $i, "j.id_aid");
        $salle = sql_query1("select salle from aid where (id_aid = '$id_aid' and indice_aid='$indice_aid')");
        if ($salle == -1) $salle = "";

        echo "<tr><td>".$nom_eleve." ".$prenom_eleve."</td>\n";
        echo "<td>".$login_eleve."</td>\n";
        echo "<td>";
        if (($_SESSION["statut"] != "eleve") and ($_SESSION["statut"] != "responsable"))
            echo "<a href='../groupes/visu_profs_class.php?id_classe=".$id_classe_eleve."' target='_blank'>".$classe_eleve."</a>";
        else
            echo $classe_eleve;
        echo "&nbsp;</td>\n";
        echo "<td>".$aid_eleve."&nbsp;</td>\n";
        if ($test_salle != 0)
            echo "<td>".$salle."&nbsp;</td>";
        echo "</tr>\n";

        $i++;
    }
    echo "</table>\n";
    if ($vide == 1) {
        echo "<br /><font color = red>Il n'y a pas actuellement d'élèves inscrits dans les ".$nom_projet." !</font>\n";
    }

}

// Affichage de la liste des élèves sans projet
if ((isset($_GET['action'])) and ($_GET['action']=="liste_eleves_sans_projet")) {
    $order_by2 = isset($_POST["order_by2"]) ? $_POST["order_by2"] : (isset($_GET["order_by2"]) ? $_GET["order_by2"] : 'nom');
    if ($order_by2 == "nom") $order_by2 = "e.nom, e.prenom";
    if ($order_by2 == "classe") $order_by2 = "c.classe, e.nom, e.prenom";
    echo "<p class=bold>";
    echo "<a href=\"./index_fiches.php?indice_aid=".$indice_aid."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
    echo "</p>";
    echo "<h2>Liste des élèves non affectés</h2>";
    echo "Cliquez sur l'en-tête de la première ligne pour classer les élèves par nom et prénom ou classe<br /><br />";

    $call_liste_data = mysql_query("SELECT distinct e.nom, e.prenom, c.classe, c.id, e.login
    FROM eleves e, classes c, j_eleves_classes jec
    WHERE (
    jec.login = e.login and
    jec.id_classe = c.id and
    c.classe != 'CIRA-APP1' and
    c.classe != 'CIRA-APP2' and
    c.classe != 'CIRA1' and
    c.classe != 'CIRA2' and
    c.classe != 'IRIS1' and
    c.classe != 'IRIS2'

    ) ORDER BY ".$order_by2);

    echo "<table width=\"90%\" border=\"1\" cellpadding=\"3\">\n";
    echo "<tr>
    <td width=\"50%\"><b><a href='index_fiches.php?order_by2=nom&amp;action=liste_eleves_sans_projet&amp;indice_aid=".$indice_aid."'>Nom Prénom</a></b></td>\n
    <td width=\"50%\"><b>Identifiant</b></td>\n
    <td><b><a href='index_fiches.php?order_by2=classe&amp;action=liste_eleves_sans_projet&amp;indice_aid=".$indice_aid."'>Classe</a></b></td>\n
    </tr>\n";
    $nombre = mysql_num_rows($call_liste_data);
    $i = "0";
    $vide = 1;
    while ($i < $nombre) {
        $login_eleve =  mysql_result($call_liste_data, $i, "e.login");
        $test = sql_query1("SELECT login FROM j_aid_eleves j
        WHERE j.login = '".$login_eleve."' and j.indice_aid='".$indice_aid."'");
        if ($test == "-1") {
            $vide = 0;
            $nom_eleve = mysql_result($call_liste_data, $i, "e.nom");
            $prenom_eleve = @mysql_result($call_liste_data, $i, "e.prenom");
            $classe_eleve = @mysql_result($call_liste_data, $i, "c.classe");
            $id_classe_eleve = @mysql_result($call_liste_data, $i, "c.id");
            echo "<tr><td>".$nom_eleve." ".$prenom_eleve."</td>\n";
            echo "<td>".$login_eleve."</td>\n";
            echo "<td><a href='../groupes/visu_profs_class.php?id_classe=".$id_classe_eleve."' target='_blank'>".$classe_eleve."</a>&nbsp;</td>\n";
        }
        $i++;
    }
    echo "</table>\n";
    if ($vide == 1) {
        echo "<br /><font color = red>Actuellement tous les élèves sont inscrits dans un projet !</font>\n";
    }

}
include "../lib/footer.inc.php";
?>