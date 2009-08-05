<?php

@set_time_limit(0);
/*
* Last modification  : 26/08/2006
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


/*
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
//"j_eleves_professeurs",
//"j_eleves_regime",
//"j_professeurs_matieres",
//"log",
//"matieres",
"matieres_appreciations",
"matieres_notes",
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
*/



//INSERT INTO `droits` ( `id` , `administrateur` , `professeur` , `cpe` , `scolarite` , `eleve` , `secours` , `description` , `statut` ) VALUES ('/matieres/matieres_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Importation des matières depuis un fichier CSV', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières depuis un CSV";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil matières</a></p>

<?php

echo "<center><h3 class='gepi'>Importation des matières</h3></center>\n";

/*
if (!isset($step1)) {
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
    echo "Des données concernant les matières sont actuellement présentes dans la base GEPI<br /></p>";
    echo "<p>Si vous poursuivez la procédure les données telles que notes, appréciations, ... seront effacées.</p>";
    echo "<p>Seules la table contenant les matières et la table mettant en relation les matières et les professeurs seront conservées.</p>";

    echo "<form enctype='multipart/form-data' action='disciplines.php' method=post>";
    echo "<input type=hidden name='step1' value='y'>";
    echo "<input type='submit' name='confirm' value='Poursuivre la procédure'>";
    echo "</form>";
    die();
}
}
*/

if (!isset($is_posted)) {
    /*
    $j=0;
    while ($j < count($liste_tables_del)) {
        if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
        $del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
        }
        $j++;
    }
    */

    //echo "<p><b>ATTENTION ...</b><br />Vous ne devez procéder à cette opération uniquement si la constitution des classes a été effectuée !</p>\n";
    echo "<p>Importation d'un fichier CSV où chaque ligne est de la forme: <code>nom_court;nom_long</code><br /><i>Par exemple:</i><br />\n";
    echo "<pre>MATHS;MATHEMATIQUES
FRANC;FRANCAIS
...</pre>\n";
    //echo "</p>\n";
    echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
    echo "<input type='hidden' name='is_posted' value='yes' />\n";
    //echo "<input type='hidden' name='step1' value='y'>";
    echo "<p><input type='file' size='80' name='csv_file' /><br />\n";
    echo "<input type='submit' value='Valider' /></p>\n";
    echo "</form>\n";

}
else{
    $csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
    //if(strtoupper($csv_file['name']) == "F_TMT.DBF") {
        //$fp = dbase_open($csv_file['tmp_name'], 0);
        $fp=fopen($csv_file['tmp_name'],"r");
        if(!$fp) {
            echo "<p>Impossible d'ouvrir le fichier CSV</p>\n";
            echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</p>\n";
        }
        else{
            $msg="";
            /*
            // on constitue le tableau des champs à extraire
            $tabchamps = array("MATIMN","MATILC");

            $nblignes = dbase_numrecords($fp); //number of rows
            $nbchamps = dbase_numfields($fp); //number of fields

            if (@dbase_get_record_with_names($fp,1)) {
                $temp = @dbase_get_record_with_names($fp,1);
            } else {
                echo "<p>Le fichier sélectionné n'est pas valide !<br />";
                echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</p>";
                die();
            }

            $nb = 0;
            foreach($temp as $key => $val){
                $en_tete[$nb] = "$key";
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
            */

            echo "<p>Dans le tableau ci-dessous, les identifiants en rouge correspondent à des nouvelles matières dans la base GEPI. les identifiants en vert correspondent à des identifiants de matières détectés dans le fichier CSV mais déjà présents dans la base GEPI.</p>\n";
            //Il est possible que certaines matières ci-dessous, bien que figurant dans le fichier GEP, ne soient pas utilisées dans votre établissement cette année. C'est pourquoi il vous sera proposé en fin de procédure d'initialsation, un nettoyage de la base afin de supprimer ces données inutiles.</p>";
            echo "<table border=1 cellpadding=2 cellspacing=2>\n";
            echo "<tr><td><p class=\"small\">Identifiant de la matière</p></td><td><p class=\"small\">Nom complet</p></td></tr>\n";

            $nb_reg_no = 0;
            //for($k = 1; ($k < $nblignes+1); $k++){
                //$ligne = dbase_get_record($fp,$k);
            while(!feof($fp)){
                $temoin_erreur="non";
                //$ligne=explode(";",fgets($fp,4096));
                $tmp_lig=fgets($fp,4096);
				if(trim($tmp_lig)!=""){
					$ligne=explode(";",$tmp_lig);
					//$affiche[0]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($ligne[0]))));
					//$affiche[1]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($ligne[1]))));

					/*
					for($i=0;$i<2; $i++) {
						$affiche[$i]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($ligne[$i]))));
						if((strlen(my_ereg_replace("[A-Za-z0-9_ &]","",strtr($affiche[$i],"-","_")))!=0)&&($affiche[$i]!="")){
							$temoin_erreur="oui";
							//echo "<!--  -->\n";
							$msg.="Le nom <font color='red'>$affiche[$i]</font> ne convient pas.<br />\n";
							$nb_reg_no++;
						}
					}
					*/

					$affiche[0]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($ligne[0]))));
					if((strlen(my_ereg_replace("[A-Za-z0-9_ &]","",strtr($affiche[0],"-","_")))!=0)&&($affiche[0]!="")){
					//if((strlen(my_ereg_replace("[A-Za-zÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü0-9_ &]","",strtr($affiche[$i],"-","_")))!=0)&&($affiche[$i]!="")){
						$temoin_erreur="oui";
						//echo "<!--  -->\n";
						$msg.="Le nom <font color='red'>$affiche[0]</font> ne convient pas.<br />\n";
						$nb_reg_no++;
					}

					$affiche[1]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($ligne[1]))));
					//if((strlen(my_ereg_replace("[A-Za-z0-9_ &]","",strtr($affiche[$i],"-","_")))!=0)&&($affiche[$i]!="")){
					if((strlen(my_ereg_replace("[A-Za-zÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü0-9_ &]","",strtr($affiche[1],"-","_")))!=0)&&($affiche[1]!="")){
						$temoin_erreur="oui";
						//echo "<!--  -->\n";
						$msg.="Le nom <font color='red'>$affiche[1]</font> ne convient pas.<br />\n";
						$nb_reg_no++;
					}

					/*
					for($i = 0; $i < count($tabchamps); $i++) {
						$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($ligne[$tabindice[$i]]))));
					}
					*/
					//if(($affiche[0]!="")&&($affiche[1]!="")){
					if(($affiche[0]!="")&&($affiche[1]!="")&&($temoin_erreur!="oui")){
						$verif = mysql_query("select matiere, nom_complet from matieres where matiere='$affiche[0]'");
						$resverif = mysql_num_rows($verif);
						if($resverif == 0) {
							$req = mysql_query("insert into matieres set matiere='$affiche[0]', nom_complet='$affiche[1]', priority='0',matiere_aid='n',matiere_atelier='n'");
							if(!$req) {
								$nb_reg_no++;
								//echo mysql_error();
								echo "<tr><td colspan='2'><font color='red'>".mysql_error()."</font></td></tr>\n";
							} else {
								echo "<tr><td><p><font color='red'>".htmlentities($affiche[0])."</font></p></td><td><p>".htmlentities($affiche[1])."</p></td></tr>\n";
							}
						} else {
							$nom_complet = mysql_result($verif,0,'nom_complet');
							echo "<tr><td><p><font color='green'>".htmlentities($affiche[0])."</font></p></td><td><p>".htmlentities($nom_complet)."</p></td></tr>\n";
						}
					}
				}
            }
            echo "</table>\n";
            //dbase_close($fp);
            fclose($fp);
            if ($nb_reg_no != 0) {
                echo "<p>Lors de l'enregistrement des données il y a eu <b>$nb_reg_no erreur(s)</b>.<br />Essayez de trouver la cause de l'erreur et recommencez la procédure.</p>\n";
                if($msg!=""){
                    echo "<p>Voici la liste des chaines non acceptées:</p>\n";
                    echo "<blockquote>\n";
                    echo "$msg";
                    echo "</blockquote>\n";
                }
            } else {
                echo "<p>L'importation des matières dans la base GEPI a été effectuée avec succès !<br />";
                //Vous pouvez procéder à la quatrième phase d'importation des professeurs.</p>\n";
            }
            //echo "<center><p><a href='professeurs.php'>Importation des professeurs</a></p></center>";
        }
    /*
    } else if (trim($csv_file['name'])=='') {
        echo "<p>Aucun fichier n'a été sélectionné !<br />";
        echo "<a href='".."'>Cliquer ici </a> pour recommencer !</center></p>";

    } else {
        echo "<p>Le fichier sélectionné n'est pas valide !<br />";
        echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
    }
    */
}
require("../lib/footer.inc.php");
?>