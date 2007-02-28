<?php
@set_time_limit(0);
/*
 * Last modification  : 09/03/2005
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
$resultat_session = resumeSession();

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

if (isset($_POST['confirm']) and ($_POST['confirm']=='Non')) {
    header("Location: index.php");
}

//**************** EN-TETE *****************
$titre_page = "Outil de gestion | Effacement des données élèves";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?><p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<H2>Effacement de la base</H2>
<?php
if (isset($_POST['is_posted']) and ($_POST['is_posted'] == 1)) {
    if ($_POST['confirm']=='Oui') {
        ?>
        <center><p class='grand'><font color='red'><b>ATTENTION, la suppression des données est irréversible !</b></font></p>
        <form action="efface_base.php" method="post" name="formulaire">
        <!--table border = 10 bordercolor='red'-->
        <!--table style='border:10px solid red'-->
        <table class='bordercolor10'>
        <tr><td><INPUT TYPE=SUBMIT value ="EFFACER LES DONNEES ELEVES" /></td></tr></table>
        <INPUT TYPE=HIDDEN name=is_posted value = 2 />
        </FORM></center>
        <?php
    }
}

if (!isset($_POST['is_posted'])) {
    echo "<p><b>ATTENTION : Cette procédure efface tout le contenu de la base de données concernant les élèves (données personnelles, notes, appréciations, ...)</b>
    <br />Si vous souhaitez initialiser l'année à l'aide de fichiers GEP, inutile d'utiliser cette procédure, l'effacement des données vous sera proposé au cours de la procédure d'initialisation.

    <br /><br />Les données suivantes sont conservées :
    <ul>
    <li>Les classes (noms, périodes, ...)</li>
    <li>Les catégories d'AID</li>
    <li>La base établissement</li>
    <li>Les logs de connexion</li>
    <li>La base des matières</li>
    <li>La base des utilisateurs</li>
    <li>Le paramétrage général.</li>
    <li>Les cahiers de texte</li>
    </ul>";



    echo "<p><b>Etes-vous sûr de vouloir continuer ?</b></p>";
    echo "<form action=\"efface_base.php\" method=\"post\" name=\"formulaire\">";
    echo "<INPUT TYPE=HIDDEN name=is_posted value = '1' /> ";
    echo "<INPUT TYPE=SUBMIT name='confirm' value = 'Oui' />";
    echo "<INPUT TYPE=SUBMIT name='confirm' value = 'Non' />";
    echo "</FORM>";

}


if (isset($_POST['is_posted']) and ($_POST['is_posted'] == 2)) {
   $liste_tables_del = array(
   "absences",
   "absences_gep",
   "aid",
   "aid_appreciations",
   //"aid_config",
   "avis_conseil_classe",
   //"classes",
   //"droits",
   "eleves",
   "responsables",
   //"etablissements",

  "groupes",
   "j_aid_eleves",
   "j_aid_utilisateurs",
   "j_eleves_classes",
   "j_eleves_etablissements",
   "j_eleves_professeurs",
   "j_eleves_regime",
   "j_eleves_groupes",
   "j_groupes_classes",
   "j_groupes_matieres",
   "j_groupes_professeurs",
   //"j_professeurs_matieres",
  //"log",
   //"matieres",
   "matieres_appreciations",
   "matieres_notes",
   //"periodes",
   "tempo2",
   "temp_gep_import",
   "tempo",
   //"utilisateurs",
   "cn_cahier_notes",
   "cn_conteneurs",
   "cn_devoirs",
   "cn_notes_conteneurs",
   "cn_notes_devoirs",
   //"setting"
   );
   $j=0;
   while ($j < count($liste_tables_del)) {
       $del = mysql_query("DELETE FROM $liste_tables_del[$j]");
       $j++;
   }
   echo "<p class='grand'>Suppression des données réussie.</p>";
}
require("../lib/footer.inc.php");
?>