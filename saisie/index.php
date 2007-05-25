<?php
/*
 * Last modification  : 15/03/2005
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

// Initialisation
$saisie_matiere = isset($_POST['saisie_matiere']) ? $_POST['saisie_matiere'] : (isset($_GET['saisie_matiere']) ? $_GET['saisie_matiere'] : NULL);

$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
    $current_group = get_group($id_groupe);
} else {
    $current_group = false;
}

include "../lib/periodes.inc.php";
//**************** EN-TETE *****************
$titre_page = "Saisie des moyennes et appréciations";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>

<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>
<?php
if ($current_group) {

    $matiere_nom = $current_group["matiere"]["nom_complet"];
    $classes = $current_group["classlist_string"];

    echo " | <a href='index.php'>Mes enseignements</a></p>\n";
    //echo "<p class='grand'> Groupe : " . $current_group["description"] . " ($classes) | Matière : $matiere_nom</p>";
    echo "<p class='grand'> Groupe : " . htmlentities($current_group["description"]) . " ($classes) | Matière : ".htmlentities($matiere_nom)."</p>\n";
    echo "<p class='bold'>Saisie manuelle (tous trimestres) :</p>\n<ul>\n";

    echo "<li><a href='saisie_notes.php?id_groupe=$id_groupe'>Saisir les moyennes manuellement</a></li>\n";
    echo "<li><a href='saisie_appreciations.php?id_groupe=$id_groupe'>Saisir les appréciations manuellement</a></li>\n</ul>\n";
    $i="1";
    // Avec affichage de la colonne "carnet de note"
    while ($i < $nb_periode) {
        if ($current_group["classe"]["ver_periode"]["all"][$i] >= 2) {
            echo "<p class='bold'>".ucfirst($nom_periode[$i])." - Importation à partir du carnet de notes :</p>\n";
            echo "<ul><li><a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$i'>Importer les notes à partir du carnet de notes</a></li></ul>\n";
        }
        $i++;
    }
    $i="1";

    // importation par csv
    while ($i < $nb_periode) {
        if ($current_group["classe"]["ver_periode"]["all"][$i] >= 2) {
            echo "<p class='bold'>".ucfirst($nom_periode[$i])." - Importation d'un fichier de moyennes/appréciations (format csv) :</p>\n";
            echo "<ul>\n<li><a href='import_note_app.php?id_groupe=$id_groupe&amp;periode_num=$i'>Procéder à l'importation</a> et consulter l'aide.</li>\n";

            /*echo "<li>Préparation du fichier d'importation :
            <br />-> <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=".$i."&amp;champs=3'>Télécharger le fichier des noms, prénoms et identifiants GEPI de cette classe</a>, ou bien,";
            */
            echo "<li>Préparation du fichier d'importation :";
            //echo "<br />-> <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=".$i."&amp;champs=1'>Télécharger le fichier des identifiants GEPI seuls de cette classe.</a></ul>";
            //echo "<br />-> <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=".$i."&amp;champs=1'>Télécharger le fichier des identifiants GEPI seuls de cette classe</a>, ou encore";
            echo "<br />-> <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=".$i."&amp;champs=3&amp;ligne_entete=y&amp;mode=Id_Note_App'>Télécharger le fichier des identifiants GEPI avec les colonnes Moyennes et Appréciations de cette classe, avec ligne d'entête.</a>";
            echo "<br />\n";
			if(getSettingValue("export_cn_ods")=='y') {
				if($_SESSION['user_temp_directory']=='y'){
					echo "-> <a href='export_class_ods.php?id_groupe=$id_groupe&amp;periode_num=$i'>Télécharger un fichier tableur OpenOffice (<i>ODS</i>) avec les identifiants GEPI, les colonnes Moyennes et Appréciations de cette classe.</a>\n";
				}
				else{
					echo "-> <font color='red'>L'export tableur ODS n'est pas possible.</font>";
				}
				echo "<br />\n";
			}
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<i>les champs vides ne sont pas importés</i>)\n";
            echo "</li>\n";
            echo "</ul>\n";
        }
        $i++;
    }


} else {

    // On commence par gérer simplement la liste des groupes pour les professeurs

    if ($_SESSION["statut"] == "professeur") {
        echo "<p>Saisir les moyennes ou appréciations par classe :</p>\n";

        $groups = get_groups_for_prof($_SESSION["login"]);
        foreach ($groups as $group) {
		//echo "<p><a href='index.php?id_groupe=" . $group["id"] . "'>" . $group["description"] . "</a> (" . $group["classlist_string"] . ")</p>\n";
		//echo "<p><a href='index.php?id_groupe=" . $group["id"] . "'>" . htmlentities($group["description"]) . "</a> (" . $group["classlist_string"] . ")</p>\n";
		//echo "<p><a href='index.php?id_groupe=" . $group["id"] . "'>" . htmlentities($group["description"]) . "</a> (" . $group["classlist_string"] . ")</p>\n";
		echo "<p><span class='norme'><b>" . $group["classlist_string"] . "</b> : ";
		echo "<a href='index.php?id_groupe=" . $group["id"] ."'>" . htmlentities($group["description"]) . "</a>";
		echo "</span></p>\n";
        }
    } elseif ($_SESSION["statut"] == "secours") {
        echo "<p>Saisir les moyennes ou appréciations par classe :</p>\n";
        $appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
        $lignes = mysql_num_rows($appel_donnees);
        $i = 0;
        while($i < $lignes){
            $id_classe = mysql_result($appel_donnees, $i, "id");
            $nom_classe = mysql_result($appel_donnees, $i, "classe");
            echo "<p><span class='norme'><b>$nom_classe</b> : ";
            $groups = get_groups_for_class($id_classe);
            foreach ($groups as $group) {

		$sql="SELECT u.nom,u.prenom FROM j_groupes_professeurs jgp, utilisateurs u WHERE
				jgp.login=u.login AND
				jgp.id_groupe='".$group["id"]."'
				ORDER BY u.nom,u.prenom";
		$res_prof=mysql_query($sql);
		$texte_alternatif="Pas de prof???";
		if(mysql_num_rows($res_prof)>0){
			$texte_alternatif="";
			while($ligne=mysql_fetch_object($res_prof)){
				$texte_alternatif.=", ".ucfirst(strtolower($ligne->prenom))." ".strtoupper($ligne->nom);
			}
			$texte_alternatif=substr($texte_alternatif,2);
		}

		//echo "<a href='index.php?id_groupe=" . $group["id"] . "'>" . $group["description"] . "</a> - \n";
		echo "<a href='index.php?id_groupe=" . $group["id"] . "' title='$texte_alternatif'>" . htmlentities($group["description"]) . "</a> - \n";
            }
            $i++;
            echo "</span>\n";
            echo "</p>\n";
        }
    }
}
require("../lib/footer.inc.php");
?>