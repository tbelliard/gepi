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
};

if (!checkAccess()) {
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
//"responsables",
//"etablissements",
//"j_aid_eleves",
"j_aid_utilisateurs",
"j_aid_utilisateurs_gest",
"j_aidcateg_super_gestionnaires",
"j_aidcateg_utilisateurs",
"j_groupes_professeurs",
//"j_eleves_classes",
//"j_eleves_etablissements",
"j_eleves_professeurs",
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

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

// On vérifie si l'extension d_base est active
//verif_active_dbase();

?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php
echo "<center><h3 class='gepi'>Quatrième phase d'initialisation<br />Importation des professeurs</h3></center>";

if (!isset($step1)) {
    $j=0;
    $flag=0;
    while (($j < count($liste_tables_del)) and ($flag==0)) {
        if (mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
            $flag=1;
        }
        $j++;
    }

    $test = mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(*) FROM utilisateurs WHERE statut='professeur'"),0);
    if ($test != 0) {$flag=1;}

    if ($flag != 0){
        echo "<p><b>ATTENTION ...</b><br />";
        echo "Des données concernant les professeurs sont actuellement présentes dans la base GEPI<br /></p>";
        echo "<p>Si vous poursuivez la procédure les données telles que notes, appréciations, ... seront effacées.</p>";
        echo "<ul><li>Seules la table contenant les utilisateurs (professeurs, admin, ...) et la table mettant en relation les matières et les professeurs seront conservées.</li>";
        echo "<li>Les professeurs de l'année passée présents dans la base GEPI et non présents dans la base GEP de cette année ne sont pas effacés de la base GEPI mais simplement déclarés \"inactifs\".</li>";
        echo "</ul>";
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
        if (mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
        $del = @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM $liste_tables_del[$j]");
        }
        $j++;
    }
    $del = @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM tempo2");

    echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method=post>";
    echo "<p>Importation du fichier <b>F_wind.csv</b> contenant les données relatives aux professeurs.";
    echo "<p>Veuillez préciser le nom complet du fichier <b>F_wind.csv</b>.";
    echo "<input type=hidden name='is_posted' value='yes' />";
    echo "<input type=hidden name='step1' value='y' />";
    echo "<p><input type='file' size='80' name='dbf_file' />";
    echo "<br /><br /><p>Quelle formule appliquer pour la génération du login ?</p>";
    echo "<input type='radio' name='login_gen_type' value='name' checked> nom";
    echo "<br /><input type='radio' name='login_gen_type' value='name8' /> nom (tronqué à 8 caractères)";
    echo "<br /><input type='radio' name='login_gen_type' value='fname8' /> pnom (tronqué à 8 caractères)";
    echo "<br /><input type='radio' name='login_gen_type' value='fname19' /> pnom (tronqué à 19 caractères)";
    echo "<br /><input type='radio' name='login_gen_type' value='firstdotname' /> prenom.nom";
    echo "<br /><input type='radio' name='login_gen_type' value='firstdotname19' /> prenom.nom (tronqué à 19 caractères)";
    echo "<br /><input type='radio' name='login_gen_type' value='namef8' /> nomp (tronqué à 8 caractères)";
    echo "<br /><input type='radio' name='login_gen_type' value='lcs' /> pnom (façon LCS)";
    echo "<br /><br /><p>Ces comptes seront-ils utilisés en Single Sign-On avec CAS ou LemonLDAP ? (laissez 'non' si vous ne savez pas de quoi il s'agit)</p>";
    echo "<br /><input type='radio' name='sso' value='no' checked /> Non";
    echo "<br /><input type='radio' name='sso' value='yes' /> Oui (aucun mot de passe ne sera généré)";
    echo "<p><input type='submit' value='Valider' />";
    echo "</form>";

} else {

    $dbf_file = isset($_FILES["dbf_file"]) ? $_FILES["dbf_file"] : NULL;
    // On commence par rendre inactifs tous les professeurs
    $req = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE utilisateurs set etat='inactif' where statut = 'professeur'");

    // on efface la ligne "display_users" dans la table "setting" de façon à afficher tous les utilisateurs dans la page  /utilisateurs/index.php
    $req = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE from setting where NAME = 'display_users'");

    //if(strtoupper($dbf_file['name']) == "F_WIND.DBF") {
    if(strtoupper($dbf_file['name']) == "F_WIND.CSV") {
        //$fp = @dbase_open($dbf_file['tmp_name'], 0);
        $fp=fopen($dbf_file['tmp_name'],"r");
        if(!$fp) {
        echo "<p>Impossible d'ouvrir le fichier dbf !</p>";
        echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
        } else {
        // on constitue le tableau des champs à extraire
        $tabchamps = array("AINOMU","AIPREN","AICIVI","NUMIND","FONCCO","INDNNI" );

        //$nblignes = dbase_numrecords($fp); //number of rows
        //$nbchamps = dbase_numfields($fp); //number of fields

        $nblignes=0;
        while (!feof($fp)) {
            $ligne = fgets($fp, 4096);
            if($nblignes==0){
                /*
                $temp=explode(";",$ligne);
                $nbchamps=sizeof($temp);
                echo "\$nbchamps=$nbchamps<br />\n";
                for($i=0;$i<$nbchamps;$i++){
                    echo "\$temp[$i]=$temp[$i]<br />\n";
                }
                */

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

        echo "<p>Dans le tableau ci-dessous, les identifiants en rouge correspondent à des professeurs nouveaux dans la base GEPI. les identifiants en vert correspondent à des professeurs détectés dans les fichiers GEP mais déjà présents dans la base GEPI.<br /><br />Il est possible que certains professeurs ci-dessous, bien que figurant dans le fichier GEP, ne soient plus en exercice dans votre établissement cette année. C'est pourquoi il vous sera proposé en fin de procédure d'initialsation, un nettoyage de la base afin de supprimer ces données inutiles.</p>";
        echo "<table border=1 cellpadding=2 cellspacing=2>";
        echo "<tr><td><p class=\"small\">Identifiant du professeur</p></td><td><p class=\"small\">Nom</p></td><td><p class=\"small\">Prénom</p></td><td>Mot de passe *</td></tr>";
        srand();
        $nb_reg_no = 0;
        //=========================
        $fp=fopen($dbf_file['tmp_name'],"r");
        // On lit une ligne pour passer la ligne d'entête:
        $ligne = fgets($fp, 4096);
        //=========================
        for($k = 1; ($k < $nblignes+1); $k++){
            //$ligne = dbase_get_record($fp,$k);
            if(!feof($fp)){
                $ligne = fgets($fp, 4096);
                if(trim($ligne)!=""){
                    $tabligne=explode(";",$ligne);
                    for($i = 0; $i < count($tabchamps); $i++) {
                        //$affiche[$i] = dbase_filter(trim($ligne[$tabindice[$i]]));
                        $affiche[$i] = dbase_filter(trim($tabligne[$tabindice[$i]]));
                    }
                    //Civilité
                    $civilite = '';
                    if ($affiche[2] = "ML") $civilite = "Mlle";
                    if ($affiche[2] = "MM") $civilite = "Mme";
                    if ($affiche[2] = "M.") $civilite = "M.";


                    $prenoms = explode(" ",$affiche[1]);
                    $premier_prenom = $prenoms[0];
                    $prenom_compose = '';
                    if (isset($prenoms[1])) $prenom_compose = $prenoms[0]."-".$prenoms[1];

                    $test_exist = mysqli_query($GLOBALS["___mysqli_ston"], "select login from utilisateurs where (
                    nom='".traitement_magic_quotes($affiche[0])."' and
                    prenom = '".traitement_magic_quotes($premier_prenom)."' and
                    statut='professeur'
                    )");
                    $result_test = mysqli_num_rows($test_exist);
                    if ($result_test == 0) {
                        if ($prenom_compose != '') {
                            $test_exist2 = mysqli_query($GLOBALS["___mysqli_ston"], "select login from utilisateurs
                            where (
                            nom='".traitement_magic_quotes($affiche[0])."' and
                            prenom = '".traitement_magic_quotes($prenom_compose)."' and
                            statut='professeur'
                            )");
                            $result_test2 = mysqli_num_rows($test_exist2);
                            if ($result_test2 == 0) {
                                $exist = 'no';
                            } else {
                                $exist = 'yes';
                                $login_prof_gepi = mysql_result($test_exist2,0,'login');
                            }
                        } else {
                            $exist = 'no';
                        }
                    } else {
                        $exist = 'yes';
                        $login_prof_gepi = mysql_result($test_exist,0,'login');
                    }
                    if ($exist == 'no') {


                    // Aucun professeur ne porte le même nom dans la base GEPI. On va donc rentrer ce professeur dans la base

                    $affiche[1] = traitement_magic_quotes(corriger_caracteres($affiche[1]));

                    if ($_POST['login_gen_type'] == "name") {
                        $temp1 = $affiche[0];
                        $temp1 = strtoupper($temp1);
                        $temp1 = my_ereg_replace(" ","", $temp1);
                        $temp1 = my_ereg_replace("-","_", $temp1);
                        $temp1 = my_ereg_replace("'","", $temp1);
                        //$temp1 = mb_substr($temp1,0,8);

                    } elseif ($_POST['login_gen_type'] == "name8") {
                        $temp1 = $affiche[0];
                        $temp1 = strtoupper($temp1);
                        $temp1 = my_ereg_replace(" ","", $temp1);
                        $temp1 = my_ereg_replace("-","_", $temp1);
                        $temp1 = my_ereg_replace("'","", $temp1);
                        $temp1 = mb_substr($temp1,0,8);
                    } elseif ($_POST['login_gen_type'] == "fname8") {
                        $temp1 = $affiche[1]{0} . $affiche[0];
                        $temp1 = strtoupper($temp1);
                        $temp1 = my_ereg_replace(" ","", $temp1);
                        $temp1 = my_ereg_replace("-","_", $temp1);
                        $temp1 = my_ereg_replace("'","", $temp1);
                        $temp1 = mb_substr($temp1,0,8);
                    } elseif ($_POST['login_gen_type'] == "fname19") {
                        $temp1 = $affiche[1]{0} . $affiche[0];
                        $temp1 = strtoupper($temp1);
                        $temp1 = my_ereg_replace(" ","", $temp1);
                        $temp1 = my_ereg_replace("-","_", $temp1);
                        $temp1 = my_ereg_replace("'","", $temp1);
                        $temp1 = mb_substr($temp1,0,19);
                    } elseif ($_POST['login_gen_type'] == "firstdotname") {
                        if ($prenom_compose != '') {
                            $firstname = $prenom_compose;
                        } else {
                            $firstname = $premier_prenom;
                        }

                        $temp1 = $firstname . "." . $affiche[0];
                        $temp1 = strtoupper($temp1);

                        $temp1 = my_ereg_replace(" ","", $temp1);
                        $temp1 = my_ereg_replace("-","_", $temp1);
                        $temp1 = my_ereg_replace("'","", $temp1);
                        //$temp1 = mb_substr($temp1,0,19);
                    } elseif ($_POST['login_gen_type'] == "firstdotname19") {
                        if ($prenom_compose != '') {
                            $firstname = $prenom_compose;
                        } else {
                            $firstname = $premier_prenom;
                        }

                        $temp1 = $firstname . "." . $affiche[0];
                        $temp1 = strtoupper($temp1);
                        $temp1 = my_ereg_replace(" ","", $temp1);
                        $temp1 = my_ereg_replace("-","_", $temp1);
                        $temp1 = my_ereg_replace("'","", $temp1);
                        $temp1 = mb_substr($temp1,0,19);
                    } elseif ($_POST['login_gen_type'] == "namef8") {
                        $temp1 =  mb_substr($affiche[0],0,7) . $affiche[1]{0};
                        $temp1 = strtoupper($temp1);
                        $temp1 = my_ereg_replace(" ","", $temp1);
                        $temp1 = my_ereg_replace("-","_", $temp1);
                        $temp1 = my_ereg_replace("'","", $temp1);
                        //$temp1 = mb_substr($temp1,0,8);
                    } elseif ($_POST['login_gen_type'] == "lcs") {
                        $nom = $affiche[0];
                        $nom = strtolower($nom);
                        if (preg_match("/\s/",$nom)) {
                            $noms = preg_split("/\s/",$nom);
                            $nom1 = $noms[0];
                            if (mb_strlen($noms[0]) < 4) {
                                $nom1 .= "_". $noms[1];
                                $separator = " ";
                            } else {
                                $separator = "-";
                            }
                        } else {
                            $nom1 = $nom;
                            $sn = ucfirst($nom);
                        }
                        $firstletter_nom = $nom1{0};
                        $firstletter_nom = strtoupper($firstletter_nom);
                        $prenom = $affiche[1];
                        $prenom1 = $affiche[1]{0};
                        $temp1 = $prenom1 . $nom1;
                    }
                    $login_prof = $temp1;
                    // On teste l'unicité du login que l'on vient de créer
                    $m = 2;
                    $test_unicite = 'no';
                    $temp = $login_prof;
                    while ($test_unicite != 'yes') {
                        $test_unicite = test_unique_login($login_prof);
                        if ($test_unicite != 'yes') {
                            $login_prof = $temp.$m;
                            $m++;
                        }
                    }
                    $affiche[0] = traitement_magic_quotes(corriger_caracteres($affiche[0]));
                    // Mot de passe
                    //echo "<tr><td colspan='4'>mb_strlen($affiche[5])=".mb_strlen($affiche[5])."<br />\$affiche[4]=$affiche[4]<br />\$_POST['sso']=".$_POST['sso']."</td></tr>";
                    if (mb_strlen($affiche[5])>2 and $affiche[4]=="ENS" and $_POST['sso'] == "no") {
                        //
                        $pwd = md5(trim($affiche[5])); //NUMEN
                        //$mess_mdp = "NUMEN";
                        $mess_mdp = "Mot de passe dans le fichier fourni";
                        //echo "<tr><td colspan='4'>NUMEN: $affiche[5] $pwd</td></tr>";
                    } elseif ($_POST['sso']== "no") {
                        $pwd = md5(rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9));
                        $mess_mdp = $pwd;
                        //echo "<tr><td colspan='4'>Choix 2: $pwd</td></tr>";
            //                       $mess_mdp = "Inconnu (compte bloqué)";
                    } elseif ($_POST['sso'] == "yes") {
                        $pwd = '';
                        $mess_mdp = "aucun (sso)";
                        //echo "<tr><td colspan='4'>sso</td></tr>";
                    }

                    // utilise le prénom composé s'il existe, plutôt que le premier prénom

                    //$res = mysql_query("INSERT INTO utilisateurs VALUES ('".$login_prof."', '".$affiche[0]."', '".$premier_prenom."', '".$civilite."', '".$pwd."', '', 'professeur', 'actif', 'y', '')");
					$sql="INSERT INTO utilisateurs SET login='$login_prof', nom='$affiche[0]', prenom='$premier_prenom', civilite='$civilite', password='$pwd', statut='professeur', etat='actif', change_mdp='y'";
					$res = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					// Pour debug:
					//echo "<tr><td colspan='4'>$sql</td></tr>";

                    if(!$res) $nb_reg_no++;
                        $res = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO tempo2 VALUES ('".$login_prof."', '".$affiche[3]."')");
                        echo "<tr><td><p><font color='red'>".$login_prof."</font></p></td><td><p>".$affiche[0]."</p></td><td><p>".$premier_prenom."</p></td><td>".$mess_mdp."</td></tr>";
                    } else {
                        $res = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE utilisateurs set etat='actif' where login = '".$login_prof_gepi."'");
                        if(!$res) $nb_reg_no++;
                        $res = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO tempo2 VALUES ('".$login_prof_gepi."', '".$affiche[3]."')");
                        echo "<tr><td><p><font color='green'>".$login_prof_gepi."</font></p></td><td><p>".$affiche[0]."</p></td><td><p>".$affiche[1]."</p></td><td>Inchangé</td></tr>";
                    }
                }
            }
        }
        //dbase_close($fp);
        fclose($fp);
        echo "</table>";
        if ($nb_reg_no != 0) {
            echo "<p>Lors de l'enregistrement des données il y a eu $nb_reg_no erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.";
        } else {
            echo "<p>L'importation des professeurs dans la base GEPI a été effectuée avec succès !</p>";

            /*
            echo "<p><b>* Précision sur les mots de passe (en non-SSO) :</b><br />
            (il est conseillé d'imprimer cette page)</p>
            <ul>
            <li>Lorsqu'un nouveau professeur est inséré dans la base GEPI, son mot de passe lors de la première
            connexion à GEPI est son NUMEN.</li>
            <li>Si le NUMEM n'est pas disponible dans le fichier F_wind.csv, GEPI génère aléatoirement
            un mot de passe.</li></ul>";
            */
            echo "<p><b>* Précision sur les mots de passe (en non-SSO) :</b><br />
            (il est conseillé d'imprimer cette page)</p>
            <ul>
            <li>Lorsqu'un nouveau professeur est inséré dans la base GEPI, son mot de passe lors de la première
            connexion à GEPI est celui inscrit dans le F_wind.csv.</li>
            <li>Si le mot de passe n'est pas disponible dans le fichier F_wind.csv, GEPI génère aléatoirement
            un mot de passe.</li></ul>";
            echo "<p><b>Dans tous les cas le nouvel utilisateur est amené à changer son mot de passe lors de sa première connexion.</b></p>";
            echo "<br /><p>Vous pouvez procéder à la cinquième phase d'affectation des matières à chaque professeur, d'affectation des professeurs dans chaque classe et de définition des options suivies par les élèves.</p>";
        }
        //echo "<center><p><b><a href='prof_disc_classe.php'>Procéder à la cinquième phase d'initialisation</a></b></p></center><br /><br />";
        echo "<center><p><b><a href='prof_disc_classe_csv.php'>Procéder à la cinquième phase d'initialisation</a></b></p></center><br /><br />";
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