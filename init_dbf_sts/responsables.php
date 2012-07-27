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

// Page bourrinée... la gestion du token n'est pas faite... et ne sera faite que si quelqu'un utilise encore ce mode d'initialisation et le manifeste sur la liste de diffusion gepi-users
check_token();


$liste_tables_del = array(
//"absences",
//"aid",
//"aid_appreciations",
//"aid_config",
//"avis_conseil_classe",
//"classes",
//"droits",
//"eleves",
"responsables",
"responsables2",
"resp_pers",
"resp_adr",
//"etablissements",
//"j_aid_eleves",
//"j_aid_utilisateurs",
//"j_eleves_classes",
//"j_eleves_etablissements",
//"j_eleves_professeurs",
//"j_eleves_regime",
//"j_professeurs_matieres",
//"log",
//"matieres",
//"matieres_appreciations",
//"matieres_notes",
//"periodes",
"tempo2",
//"temp_gep_import",
"tempo",
//"utilisateurs",
//"cn_cahier_notes",
//"cn_conteneurs",
//"cn_devoirs",
//"cn_notes_conteneurs",
//"cn_notes_devoirs",
//"setting"
);



if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des responsables des élèves";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>

<?php

// On vérifie si l'extension d_base est active
verif_active_dbase();

echo "<h3 class='gepi' align='center'>Deuxième phase d'initialisation<br />Importation des responsables</h3>\n";

if (isset($step1)) {
    $j=0;
    $flag=0;
    while (($j < count($liste_tables_del)) and ($flag==0)) {
        if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
            $flag=1;
        }
        $j++;
    }
    if ($flag != 0){
        echo "<p><b>ATTENTION ...</b><br />";
        echo "Des données concernant les responsables sont actuellement présentes dans la base GEPI<br /></p>";
        echo "<p>Si vous poursuivez la procédure ces données seront effacées.</p>";
        echo "<form enctype='multipart/form-data' action='responsables.php' method='post'>";
        echo "<input type=hidden name='step1' value='y' />";
        echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />";
        echo "</form>";
        die();
    }
}

if (!isset($is_posted)) {
    $j=0;
    while ($j < count($liste_tables_del)) {
        if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
            $del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
        }
        $j++;
    }

    echo "<p><b>ATTENTION ...</b><br />Vous ne devez procéder à cette opération uniquement si la constitution des classes a été effectuée !</p>";
    echo "<p>Importation du fichier <b>F_ere.dbf</b> contenant les données relatives aux responsables : veuillez préciser le nom complet du fichier <b>F_ere.dbf</b>.";
    echo "<form enctype='multipart/form-data' action='responsables.php' method='post'>";
    echo "<input type=hidden name='is_posted' value='yes' />";
    echo "<input type=hidden name='step1' value='y' />";
    echo "<p><input type='file' size='80' name='dbf_file' />";
    echo "<p><input type=submit value='Valider' />";
    echo "</form>";

} else {
    $dbf_file = isset($_FILES["dbf_file"]) ? $_FILES["dbf_file"] : NULL;
    if(strtoupper($dbf_file['name']) == "F_ERE.DBF") {
        $fp = dbase_open($dbf_file['tmp_name'], 0);
        if(!$fp) {
            echo "<p>Impossible d'ouvrir le fichier dbf</p>";
            echo "<p><a href='responsables.php'>Cliquer ici </a> pour recommencer !</p>";
        } else {
            // on constitue le tableau des champs à extraire
            $tabchamps = array("ERENO", "ERENOM", "EREPRE", "EREADR", "EREADRS", "ERECLD", "ERELCOM", "EREANOM", "EREAPRE", "EREAADR", "EREACLD", "EREALCOM");

            // ERENO          numéro desresponsables (en liaison avec F_ELE.DBF)
            // ERENOM         nom  du premier responsable
            // EREPRE         prénom(s)  du premier responsable
            // EREADR         n° + rue   du premier responsable
            // ERECLD         code postal   du premier responsable
            // ERELCOM        nom de la commune  du premier responsable
            // EREANOM        nom du deuxième responsable
            // EREAPRE        prénom(s) du deuxième responsable
            // EREAADR        n° + rue  du deuxième responsable
            // EREADRS        complément adresse
            // EREACLD        code postal  du deuxième responsable
            // EREALCOM       nom de la commune  du deuxième responsable


            $nblignes = dbase_numrecords($fp); //number of rows
            $nbchamps = dbase_numfields($fp); //number of fields

            if (@dbase_get_record_with_names($fp,1)) {
                $temp = @dbase_get_record_with_names($fp,1);
            } else {
                echo "<p>Le fichier sélectionné n'est pas valide !<br />";
                echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</p>";
                die();
            }

            $nb = 0;
            foreach($temp as $key => $val){
                $en_tete[$nb] = $key;
                $nb++;
            }

            // On range dans tabindice les indices des champs retenus
            for ($k = 0; $k < count($tabchamps); $k++) {
                for ($i = 0; $i < count($en_tete); $i++) {
                    if ($en_tete[$i] == $tabchamps[$k]) {
                        $tabindice[] = $i;
                    }
                }
            }

            $nb_reg_no = 0;
            $nb_record = 0;
            for($k = 1; ($k < $nblignes+1); $k++){
                $ligne = dbase_get_record($fp,$k);
                for($i = 0; $i < count($tabchamps); $i++) {
                    $affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($ligne[$tabindice[$i]]))));
                }
                $req = mysql_query("insert into responsables set
                ereno = '$affiche[0]',
                nom1 = '$affiche[1]',
                prenom1 = '$affiche[2]',
                adr1 = '$affiche[3]',
                adr1_comp = '$affiche[4]',
                cp1 = '$affiche[5]',
                commune1 = '$affiche[6]',
                nom2 = '$affiche[7]',
                prenom2 = '$affiche[8]',
                adr2 = '$affiche[9]',
                adr2_comp = '',
                cp2 = '$affiche[10]',
                commune2 = '$affiche[11]'
                ");
                if(!$req) {
                    $nb_reg_no++; echo mysql_error();
                } else {
                    $nb_record++;
                }
            }
            dbase_close($fp);
            if ($nb_reg_no != 0) {
                echo "<p>Lors de l'enregistrement des données il y a eu $nb_reg_no erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.";
            } else {
                echo "<p>L'importation des responsables dans la base GEPI a été effectuée avec succès (".$nb_record." enregistrements au total).
                <br />Vous pouvez à présent retourner à l'accueil et effectuer toutes les autres opérations d'initialisation manuellement ou bien procéder à la troixième phase d'importation des matières et de définition des options suivies par les élèves.</p>";
                echo "<center><p><a href='../accueil.php'>Retourner à l'accueil</a></p></center>";
                echo "<center><p><a href='disciplines_csv.php'>Procéder à la troisième phase</a>.</p></center>";
            }

			// On sauvegarde le témoin du fait qu'il va falloir convertir pour remplir les nouvelles tables responsables:
			saveSetting("conv_new_resp_table", 0);

        }
    } else if (trim($dbf_file['name'])=='') {
        echo "<p>Aucun fichier n'a été sélectionné !<br />";
        //echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
        echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</p>";

    } else {
        echo "<p>Le fichier sélectionné n'est pas valide !<br />";
        //echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
        echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</p>";
    }
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>