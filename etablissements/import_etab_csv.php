<?php
/*
 * Last modification  : 20/08/2006
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

//**************** EN-TETE *****************
$titre_page = "Etablissements | Importation d'un fichier csv";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
// $long_max : doit être plus grand que la plus grande ligne trouvée dans le fichier CSV
$long_max = 8000;

echo "<p class='bold'>|<a href='index.php'>Retour</a>|</p>";

if (!isset($is_posted)) {
    echo "<p><span class = 'grand'>Première phase d'importation des établissements </span></p>";
    echo "<hr />";
    echo "<p>Choisir un fichier csv parmi ceux disponibles actuellement dans la distribution GEPI : <br />";
    echo "<form enctype=\"multipart/form-data\" action=\"import_etab_csv.php\" method=post name=\"formulaire\">";
    $handle=opendir('./bases');
    echo "<select name=\"csv_file\" size=\"1\">";
    $file_tab = array();
    while ($file = readdir($handle)) {
      if (($file != '.') and ($file != '..'))
		// On met le fichier dans un tableau, histoire de pouvoir classer tout ça
		$files_tab[] = $file;
    }
    sort($files_tab);
    foreach ($files_tab as $file) {
      echo "<option>".$file."</option>\n";
    }
    echo "</select>";
    closedir($handle);
    echo "<input type='submit' value='Valider' />";
    echo "<input type='hidden' name='is_posted' value='1' />";
    echo "<input type='hidden' name='choix' value=\"gepi\" />";
    echo "</form>";

    echo "<br /><br /><hr />";
/*
    echo "<p>Choisir un autre fichier de votre choix :<br />
    <form enctype=\"multipart/form-data\" action=\"import_etab_csv.php\" method=\"post\" name=\"formulaire\">";
*/
    echo "<p>Choisir un autre fichier de votre choix :<br />
    <form enctype=\"multipart/form-data\" action=\"import_etab_csv.php\" method=\"post\" name=\"formulaire2\">";
    $csv_file = "";
    echo "<input type='file' name=\"csv_file\" />";
    echo "<input type='submit' value='Valider' />";
    ?>
    <p>Si le fichier à importer comporte une première ligne d'en-tête (non vide) à ignorer, cocher la case ci-contre&nbsp;
    <input type='checkbox' NAME="en_tete" VALUE="yes" /></p>
    <input type='hidden' name='is_posted' value='1' />
    <input type='hidden' name='choix' value="autre" />

    </FORM>
    <?php
    echo "<p>Le fichier d'importation peut-être constitué à l'aide d'un tableur à partir des informations contenues dans le fichier \"NMETABC.TXT\" qui se trouve dans GEP.";
    echo "<br />Il doit être au format csv (séparateur : point-virgule) et doit contenir les six champs suivants :<br />";
    echo "--> <B>Le N° RNE de l'établissement</B><br />";
    echo "--> <B>Le nom de l'établissement</B><br />";
    echo "--> <B>Le type :</B><ul>";
    foreach ($type_etablissement as $type_etab => $nom_etablissement) {
        if ($nom_etablissement != "") echo "<li>\"<b>".$type_etab."</b>\" (pour les établissements de type \"".$nom_etablissement."\")</li>";
    }
    echo "</ul>Seules ces possibilités sont autorisées (attention à respecter la casse).<br /><br />";
    echo "--> <B>Le type  \"public\" ou \"prive\". Seules ces deux possibilités sont autorisées.</B><br />";
    echo "--> <B>Le code postal de la ville.</B><br />";
    echo "--> <B>La ville.</B>";

} else if (isset($is_posted ) and ($is_posted==1 )) {
    echo "<p><span class = 'grand'>Deuxième phase d'importation des établissements </span></p>";
    $table_etab=array();
    if ($_POST['choix'] == 'gepi') {
        $fp = @fopen("./bases/".$_POST['csv_file'], "r");
    } else {
       $csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
       if($csv_file['tmp_name'] == "") {
           echo "<p>Aucun fichier n'a été sélectionné !</p></body></html>";
           die();
       }
       $fp = @fopen($csv_file['tmp_name'], "r");
    }

    echo "<form enctype='multipart/form-data' action='import_etab_csv.php' method='post'>";
    echo "<p><b>Attention</b>, les données ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton en bas de la page) !</p>";
    if(!$fp) {
        echo "Impossible d'ouvrir le fichier CSV";
    } else {
        // Nombre total de lignes lues
        $row = 0;
        // Nombre total de lignes insérées dans la base
        $ind = 0;
        echo "<table border=1><tr>
        <td><p class='bold'>N° RNE</p></td>
        <td><p class='bold'>Nom de l'établissement</p></td>
        <td><p class='bold'>Type lycée/collège/école/...</p></td>
        <td><p class='bold'>Type public/privé</p></td>
        <td><p class='bold'>Code postal</p></td>
        <td><p class='bold'>Ville</p></td></tr>";
        while(!feof($fp)) {
            if (isset($en_tete)) {
                $data = fgetcsv ($fp, $long_max, ";");
                unset($en_tete);
            }
            $data = fgetcsv ($fp, $long_max, ";");
            $num = count ($data);
            if ($num == 6)  {
                $reg_rne = '';
                $reg_nom = '';
                $reg_type2 = '';
                $reg_type1 = '';
                $reg_cp = '';
                $reg_ville = '';
                $row++;
                echo "<tr>";
                for ($c=0; $c<$num; $c++) {
                    switch ($c) {
                    case 0:
                        //RNE
                        $call_rne = mysql_query("SELECT * FROM etablissements WHERE id='$data[$c]'");
                        $test = @mysql_num_rows($call_rne);
                        $couleur = 'black';
                        if ($test != 0) {
                            $couleur = 'red';
                            $reg_ligne='no';
                        }
                        //echo "<td><p><b><font color = ".$couleur.">".$data[$c]."</font></p></b></td>";
                        echo "<td><p><b><font color = ".$couleur.">".$data[$c]."</font></b></p></td>";
                        $reg_rne=$data[$c];
                        break;
                    case 1:
                        // Nom
                        if ($data[$c] == "") {
                           $col = "<b><font color='red'>Non défini</font></b>";
                            $reg_ligne='no';
                        } else {
                            $reg_nom = traitement_magic_quotes(corriger_caracteres($data[$c]));
                            $col = $data[$c];
                        }
                        echo "<td>$col</td>";
                        break;
                    case 2:
                        // Type lycée/collège
                        $tempo = $data[$c];
                        $valid='no';
                        foreach ($type_etablissement as $type_etabli => $nom_etablissement) {
                            if ($tempo == $type_etabli) {
                                $tempo = $nom_etablissement;
                                $reg_type1 = $type_etabli;
                                $valid='yes';

                            }
                        }
                        if ($valid=='yes') {
                            echo "<td><p>$tempo</p></td>";
                        } else {
                            echo "<td><b><font color='red'>Non défini</font></b></td>";
                            $reg_ligne='no';
                        }
                        break;
                    case 3:
                        // Type public/privé
                        $tempo = strtolower($data[$c]);
                        $valid='yes';
                        switch($tempo) {
                            case "public":
                            $reg_type2 = "public";
                            break;
                            case "prive":
                            $reg_type2 = "prive";
                            break;
                            $valid = 'no';
                        }
                        if ($valid=='yes') {
                            echo "<td><p>$tempo</p></td>";
                        } else {
                            echo "<td><b><font color='red'>Non défini</font></b></td>";
                            $reg_ligne='no';
                        }
                        break;
                    case 4:
                        // Code postal
                        if (ereg ("^[0-9]{1,5}$", $data[$c])) {
                            echo "<td><p>$data[$c]</p></td>";
                            $reg_cp=$data[$c];
                        } else {
                            echo "<td><b><font color='red'>Non défini</font></b></td>";
                            $reg_ligne='no';
                        }
                        break;
                     case 5:
                        // Ville
                       if ($data[$c] == "") {
                            $col = "<b><font color='red'>Non défini</font></b>";
                            $reg_ligne='no';
                            $reg_ville = '';
                        } else {
                            $col = $data[$c];
                            $reg_ville = traitement_magic_quotes(corriger_caracteres($data[$c]))    ;
                        }
                        echo "<td>$col</td></tr>";
                        break;
                      }
                }
                if (isset($reg_ligne)) {
                    unset($reg_ligne);
                } else {
                    $table_etab[$ind][] = $reg_rne;
                    $table_etab[$ind][] = $reg_nom;
                    $table_etab[$ind][] = $reg_type1;
                    $table_etab[$ind][] = $reg_type2;
                    $table_etab[$ind][] = $reg_cp;
                    $table_etab[$ind][] = $reg_ville;
                    $ind++;
                }
            }
            // fin de la boucle "while(!feof($fp))"
        }
        fclose($fp);
        echo "</table>";
        echo "<p>Première phase de l'importation : <b>$row entrées détectées</b> !</p>";
        if ($row > 0) {
             $table_etab=serialize($table_etab);
            $_SESSION['table_etab']=$table_etab;
            echo "<p class='bold'>AVERTISSEMENT : </p>
            <ul><li>Les N° RNE qui apparaissent en rouge correspondent à des établissements déjà présents dans la base.
            Les lignes correspondantes seront ignorées lors de la phase finale d'importation.</li>
            <li>Les intitulés \"<font color=red>Non défini</font>\" signifient que le champ en question n'est pas valide.
            la ligne correspondante sera ignorée lors de la phase finale d'importation.</li>
            </ul>";
            if ($ind != 0) {
                echo "<center><p><b>".$ind." lignes sont prêtes à être enregistrer.</b></p>";
                echo "<input type='submit' value='Enregistrer les données' /></center>";
                echo "<input type='hidden' name='is_posted' value='2' />";
            } else {
                echo "<center><p><b>Il n'y a aucun établissement à entrer dans la base.</p></center>";
            }
            echo "</FORM>";
        } else {
            echo "<p>L'importation a échoué !</p>";
        }
    }
} else {
    echo "<p><span class = 'grand'>Troisième phase d'importation des établissements </span></p>";
    if (!isset($_SESSION['table_etab'])) {
        echo "<center><p class='grand'>Opération non conforme.</p></center></body></html>";
        die();
    }

    $table_etab=unserialize($_SESSION['table_etab']);
    $pb = 'no';
    for ($c=0; $c<count ($table_etab); $c++) {
        $couleur[$c] = '';
        $sql = mysql_query("INSERT INTO etablissements SET
        id='".$table_etab[$c][0]."',
        nom='".$table_etab[$c][1]."',
        niveau='".$table_etab[$c][2]."',
        type='".$table_etab[$c][3]."',
        cp='".$table_etab[$c][4]."',
        ville='".$table_etab[$c][5]."'
        ");
        if (!$sql) {
            $couleur[$c] = 'red';
            $pb = 'yes';
        }
    }
    If ($pb == 'yes') {
        echo "<p>Il y a eu un ou plusieurs problèmes lors de l'enregistrement.
        Les lignes en rouge indiquent les enregistrements défectueux.</p>";
    } else {
        echo "<p class='bold'>".count ($table_etab)." établissements ont été insérés avec succès dans la base.</p>";
    }

     echo "<table border=\"1\" cellpadding=\"2\" cellspacing=\"2\">";
     for ($c=0; $c<count ($table_etab); $c++) {
        echo "<tr bgColor=\"".$couleur[$c]."\">";
        for ($j=0; $j<count($table_etab[$c]); $j++) {
            // Pour l'affichage final, on enlève les caractère \ qu'on a rajouté avec traitement_magic_quotes plus haut
            echo "<td>".StripSlashes($table_etab[$c][$j])."</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    unset($_SESSION['table_etab']);

}
?>
</body>
</html>