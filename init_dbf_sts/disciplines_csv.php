<?php

@set_time_limit(0);
/*
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
require_once("../lib/initialisations.inc.php");
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}


$liste_tables_del = array(
//"absences",
//"aid",
//"aid_appreciations",
//"aid_config",
//"avis_conseil_classe",
//"classes",
//"droits",
//"eleves",
//"responsables",
//"etablissements",
"groupes",
//"j_aid_eleves",
//"j_aid_utilisateurs",
//"j_eleves_classes",
//"j_eleves_etablissements",
"j_eleves_groupes",
"j_groupes_matieres",
"j_groupes_professeurs",
"j_groupes_classes",
"j_signalement",
//"j_eleves_professeurs",
//"j_eleves_regime",
//"j_professeurs_matieres",
//"log",
//"matieres",
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
//"periodes",
"tempo2",
//"temp_gep_import",
"tempo",
//"utilisateurs",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
//"setting"
);

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// Page bourrinée... la gestion du token n'est pas faite... et ne sera faite que si quelqu'un utilise encore ce mode d'initialisation et le manifeste sur la liste de diffusion gepi-users
check_token();

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>

<?php

// On vérifie si l'extension d_base est active
//verif_active_dbase();

echo "<center><h3 class='gepi'>Troisième phase d'initialisation<br />Importation des matières</h3></center>";

if (!isset($step1)) {
    $j=0;
    $flag=0;
    while (($j < count($liste_tables_del)) and ($flag==0)) {
        if (old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
            $flag=1;
        }
        $j++;
    }
    if ($flag != 0){
        echo "<p><b>ATTENTION ...</b><br />";
        echo "Des données concernant les matières sont actuellement présentes dans la base GEPI<br /></p>";
        echo "<p>Si vous poursuivez la procédure les données telles que notes, appréciations, ... seront effacées.</p>";
        echo "<p>Seules la table contenant les matières et la table mettant en relation les matières et les professeurs seront conservées.</p>";

        echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>";
        echo "<input type=hidden name='step1' value='y' />";
        echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />";
        echo "</form>";
        die();
    }
}

if (!isset($is_posted)) {
    $j=0;
    while ($j < count($liste_tables_del)) {
        if (old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
            $del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM $liste_tables_del[$j]");
        }
        $j++;
    }

    echo "<p><b>ATTENTION ...</b><br />Vous ne devez procéder à cette opération uniquement si la constitution des classes a été effectuée !</p>";
    echo "<p>Importation du fichier <b>F_tmt.csv</b> contenant les données relatives aux matières : veuillez préciser le nom complet du fichier <b>F_tmt.csv</b>.";
    echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>";
    echo "<input type=hidden name='is_posted' value='yes' />";
    echo "<input type=hidden name='step1' value='y' />";
    echo "<p><input type='file' size='80' name='dbf_file' />";
    echo "<p><input type=submit value='Valider' />";
    echo "</form>";

} else {
    $dbf_file = isset($_FILES["dbf_file"]) ? $_FILES["dbf_file"] : NULL;
    //if(strtoupper($dbf_file['name']) == "F_TMT.DBF") {
    if(strtoupper($dbf_file['name']) == "F_TMT.CSV") {
        //$fp = dbase_open($dbf_file['tmp_name'], 0);
        $fp = fopen($dbf_file['tmp_name'],"r");
        if(!$fp) {
            echo "<p>Impossible d'ouvrir le fichier dbf</p>";
            echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
        } else {
            // on constitue le tableau des champs à extraire
            $tabchamps = array("MATIMN","MATILC");

            //$nblignes = dbase_numrecords($fp); //number of rows
            //$nbchamps = dbase_numfields($fp); //number of fields

            $nblignes=0;
            while (!feof($fp)) {
                $ligne = fgets($fp, 4096);
                if($nblignes==0){
                    // Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
                    // On ne retient pas ces ajouts pour $en_tete
                    $temp=explode(";",$ligne);
                    for($i=0;$i<sizeof($temp);$i++){
                        $temp2=explode(",",$temp[$i]);
                        $en_tete[$i]=$temp2[0];
                    }

                    //$en_tete=explode(";",$ligne);
                    $nbchamps=sizeof($en_tete);
                }
                $nblignes++;
            }
            fclose ($fp);
/*
            if (@dbase_get_record_with_names($fp,1)) {
                $temp = @dbase_get_record_with_names($fp,1);
            } else {
                echo "<p>Le fichier sélectionné n'est pas valide !<br />";
                echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
                die();
            }

            $nb = 0;
            foreach($temp as $key => $val){
                $en_tete[$nb] = "$key";
                $nb++;
            }
*/
            // On range dans tabindice les indices des champs retenus
            for ($k = 0; $k < count($tabchamps); $k++) {
                for ($i = 0; $i < count($en_tete); $i++) {
                    //if ($en_tete[$i] == $tabchamps[$k]) {
                    if (trim($en_tete[$i]) == $tabchamps[$k]) {
                        $tabindice[] = $i;
                    }
                }
            }
            echo "<p>Dans le tableau ci-dessous, les identifiants en rouge correspondent à des nouvelles matières dans la base GEPI. les identifiants en vert correspondent à des identifiants de matières détectés dans le fichier GEP mais déjà présents dans la base GEPI.<br /><br />Il est possible que certaines matières ci-dessous, bien que figurant dans le fichier GEP, ne soient pas utilisées dans votre établissement cette année. C'est pourquoi il vous sera proposé en fin de procédure d'initialsation, un nettoyage de la base afin de supprimer ces données inutiles.</p>";
            echo "<table border=1 cellpadding=2 cellspacing=2>";
            echo "<tr><td><p class=\"small\">Identifiant de la matière</p></td><td><p class=\"small\">Nom complet</p></td></tr>";


            //=========================
            $fp=fopen($dbf_file['tmp_name'],"r");
            // On lit une ligne pour passer la ligne d'entête:
            $ligne = fgets($fp, 4096);
            //=========================
            $nb_reg_no = 0;
            for($k = 1; ($k < $nblignes+1); $k++){
                //$ligne = dbase_get_record($fp,$k);
                if(!feof($fp)){
                    $ligne = fgets($fp, 4096);
                    if(trim($ligne)!=""){
                        $tabligne=explode(";",$ligne);
                        for($i = 0; $i < count($tabchamps); $i++) {
                            //$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($ligne[$tabindice[$i]]))));
                            $affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
                        }
                        $verif = mysqli_query($GLOBALS["mysqli"], "select matiere, nom_complet from matieres where matiere='$affiche[0]'");
                        $resverif = mysqli_num_rows($verif);
                        if($resverif == 0) {
                            $req = mysqli_query($GLOBALS["mysqli"], "insert into matieres set matiere='$affiche[0]', nom_complet='$affiche[1]', priority='0',matiere_aid='n',matiere_atelier='n'");
                            if(!$req) {
                                $nb_reg_no++; echo ((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
                            } else {
                                echo "<tr><td><p><font color='red'>$affiche[0]</font></p></td><td><p>".htmlspecialchars($affiche[1])."</p></td></tr>";
                            }
                        } else {
                            $nom_complet = old_mysql_result($verif,0,'nom_complet');
                            echo "<tr><td><p><font color='green'>$affiche[0]</font></p></td><td><p>".htmlspecialchars($nom_complet)."</p></td></tr>";
                        }
                    }
                }
            }
            echo "</table>";
            //dbase_close($fp);
            fclose($fp);
            if ($nb_reg_no != 0) {
                echo "<p>Lors de l'enregistrement des données il y a eu $nb_reg_no erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.";
            } else {
                echo "<p>L'importation des matières dans la base GEPI a été effectuée avec succès !<br />Vous pouvez procéder à la quatrième phase d'importation des professeurs.</p>";
            }
            echo "<center><p><a href='prof_csv.php'>Importation des professeurs</a></p></center>";
        }
    } else if (trim($dbf_file['name'])=='') {
        echo "<p>Aucun fichier n'a été sélectionné !<br />";
        echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";

    } else {
        echo "<p>Le fichier sélectionné n'est pas valide !<br />";
        echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
    }
}
require("../lib/footer.inc.php");
?>