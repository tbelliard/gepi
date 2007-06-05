<?php
/*
 * Last modification  : 07/08/2006
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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


// Initialistion
$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
    $current_group = get_group($id_groupe);
} else {
    $current_group = false;
}

if (count($current_group["classes"]["list"]) > 1) {
    $multiclasses = true;
} else {
    $multiclasses = false;
}

$periode_cn = isset($_POST["periode_cn"]) ? $_POST["periode_cn"] :(isset($_GET["periode_cn"]) ? $_GET["periode_cn"] :NULL);
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "classe");

include "../lib/periodes.inc.php";


if ($_SESSION['statut'] != "secours") {
    if (!(check_prof_groupe($_SESSION['login'],$current_group["id"]))) {
        $mess=rawurlencode("Vous n'êtes pas professeur de cet enseignement !");
        header("Location: index.php?msg=$mess");
        die();
    }
}

if (isset($_POST['is_posted'])) {
    foreach ($current_group["eleves"]["all"]["list"] as $reg_eleve_login) {
        $k=1;
        while ($k < $nb_periode) {
            if (in_array($reg_eleve_login, $current_group["eleves"][$k]["list"])) {
                    $eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$reg_eleve_login]["classe"]]["id"];
                    if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] == "N"){
                        $nom_log = $reg_eleve_login."_t".$k;
                        if (isset($NON_PROTECT[$nom_log]))
                           $app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
                        else
                           $app = "";

			// Contrôle des saisies pour supprimer les sauts de lignes surnuméraire.
			$app=ereg_replace('(\\\r\\\n)+',"\r\n",$app);


                        $test_eleve_app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
                        $test = mysql_num_rows($test_eleve_app_query);
                        if ($test != "0") {
                            if ($app != "") {
                                $register = mysql_query("UPDATE matieres_appreciations SET appreciation='" . $app . "' WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
                            } else {
                                $register = mysql_query("DELETE FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
                            }
                            if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour l'élève $reg_eleve_login<br />";}

                        } else {
                            if ($app != "") {
                                $register = mysql_query("INSERT INTO matieres_appreciations SET login='$reg_eleve_login',id_groupe='" . $current_group["id"]."',periode='$k',appreciation='" . $app . "'");
                                if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour l'élève $reg_eleve_login<br />";}
                            }
                        }
                    }
            }
            $k++;
        }
    }
    $affiche_message = 'yes';
}

if (!isset($periode_cn)) $periode_cn = 0;

$themessage = 'Des appréciations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
//**************** EN-TETE *****************
$titre_page = "Saisie des appréciations";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<script type="text/javascript" language="javascript">
change = 'no';
</script>
<?php

$matiere_nom = $current_group["matiere"]["nom_complet"];

echo "<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" method=post>\n";

echo "<p class=bold>";
if ($periode_cn != 0) {
    //echo "|<a href=\"../cahier_notes/index.php?id_groupe=$id_groupe&periode_num=$periode_cn\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Retour</a>";
    echo "<a href=\"../cahier_notes/index.php?id_groupe=$id_groupe&amp;periode_num=$periode_cn\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
} else {
    echo "<a href=\"index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil saisie</a>\n";
}
//echo "|<a href='saisie_notes.php?id_groupe=$id_groupe&periode_cn=$periode_cn' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les moyennes</a>";
echo " | <a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_cn' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les moyennes</a>";
// enregistrement du chemin de retour pour la fonction imprimer
$_SESSION['chemin_retour'] = $_SERVER['PHP_SELF']."?". $_SERVER['QUERY_STRING'];
echo " | <a href='../prepa_conseil/index1.php?id_groupe=$id_groupe'>Imprimer</a>\n";
echo " | <input type='submit' value='Enregistrer' /></p>\n";
echo "<h2 class='gepi'>Bulletin scolaire - Saisie des appréciations</h2>\n";
//echo "<p><b>Groupe : " . $current_group["description"] ." | Matière : $matiere_nom</b></p>\n";
echo "<p><b>Groupe : " . htmlentities($current_group["description"]) ." (".$current_group["classlist_string"].")</b></p>\n";?>

<?php
if ($multiclasses) {
    echo "<p>Affichage :";
    echo "<br/>-> <a href='saisie_appreciations.php?id_groupe=$id_groupe&order_by=classe'>Regrouper les élèves par classe</a>";
    echo "<br/>-> <a href='saisie_appreciations.php?id_groupe=$id_groupe&order_by=nom'>Afficher la liste par ordre alphabétique</a>";
    echo "</p>";
}

// On commence par mettre la liste dans l'ordre souhaité
if ($order_by != "classe") {
    $liste_eleves = $current_group["eleves"]["all"]["list"];
} else {
    // Ici, on tri par classe
    // On va juste créer une liste des élèves pour chaque classe
    $tab_classes = array();
    foreach($current_group["classes"]["list"] as $classe_id) {
        $tab_classes[$classe_id] = array();
    }
    // On passe maintenant élève par élève et on les met dans la bonne liste selon leur classe
    foreach($current_group["eleves"]["all"]["list"] as $eleve_login) {
        $classe = $current_group["eleves"]["all"]["users"][$eleve_login]["classe"];
        $tab_classes[$classe][] = $eleve_login;
    }
    // On met tout ça à la suite
    $liste_eleves = array();
    foreach($current_group["classes"]["list"] as $classe_id) {
        $liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
    }
}

$prev_classe = null;
$num_id = 10;
foreach ($liste_eleves as $eleve_login) {

    $k=1;

    while ($k < $nb_periode) {

        if (in_array($eleve_login, $current_group["eleves"][$k]["list"])) {
            //
            // si l'élève appartient au groupe pour cette période
            //
            $eleve_nom = $current_group["eleves"][$k]["users"][$eleve_login]["nom"];
            $eleve_prenom = $current_group["eleves"][$k]["users"][$eleve_login]["prenom"];
            $eleve_classe = $current_group["classes"]["classes"][$current_group["eleves"]["all"]["users"][$eleve_login]["classe"]]["classe"];
            $eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$eleve_login]["classe"]]["id"];
                $suit_option[$k] = 'yes';
                //
                // si l'élève suit la matière
                //
                // Appel des appréciations
                $app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
                $eleve_app = @mysql_result($app_query, 0, "appreciation");
                // Appel des notes
                $note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
                $eleve_statut = @mysql_result($note_query, 0, "statut");
                $eleve_note = @mysql_result($note_query, 0, "note");
                // Formatage de la note
                $note ="<center>";
                if ($eleve_statut != '') {
                    $note .= $eleve_statut;
                } else {
                    if ($eleve_note != '') {
                       $note .= $eleve_note;
                    } else {
                       $note .= "&nbsp;";
                    }
                }
                $note .= "</center>";

                $eleve_login_t[$k] = $eleve_login."_t".$k;
                if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] != "N") {
                    //
                    // si la période est verrouillée
                    //
                    $mess[$k] = '';
                    $mess[$k] =$mess[$k]."<td>".$note."</td>\n<td>";
                    if ($eleve_app != '') {
                        //$mess[$k] =$mess[$k].$eleve_app;
			if((strstr($eleve_app,">"))||(strstr($eleve_app,"<"))){
				$mess[$k] =$mess[$k].$eleve_app;
			}
			else{
	                        $mess[$k] =$mess[$k].nl2br($eleve_app);
			}
                    } else {
                        $mess[$k] =$mess[$k]."&nbsp;";
                    }
                    $mess[$k] =$mess[$k]."</td>\n";
                } else {

                    // Ajout Eric affichage des notes au dessus de la saisie des appréciations
					$liste_notes ='';
					// Nombre de contrôles
					$sql="SELECT cnd.note FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE cnd.login='".$eleve_login."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group["id"]."' AND ccn.periode='$k' AND cnd.statut='';";
					//echo "\n<!--sql=$sql-->\n";
					$result_nbct=mysql_query($sql);
					$current_eleve_nbct=mysql_num_rows($result_nbct);

					// on prend les notes dans $string_notes
					$liste_notes='';
					if ($result_nbct ) {
						while ($snnote =  mysql_fetch_assoc($result_nbct)) {
							if ($liste_notes != '') $liste_notes .= ", ";
							$liste_notes .= $snnote['note'];
						}
					}

					if ($current_eleve_nbct ==0) {
					  $liste_notes='Pas de note dans le carnet pour cette période.';
					}

                   //$mess[$k] = "<td>".$note."</td>\n<td><textarea id=\"".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_".$eleve_login_t[$k]."\" rows=2 cols=100 wrap='virtual' onchange=\"changement()\">".$eleve_app."</textarea></td>\n";
                   $mess[$k] = "<td>".$note."</td>\n<td>Contenu du carnet de notes : ".$liste_notes."<br /><textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_".$eleve_login_t[$k]."\" rows='2' cols='100' wrap='virtual' onchange=\"changement()\">".$eleve_app."</textarea></td>\n";
                }
        } else {
            //
            // si l'élève n'appartient pas au groupe pour cette période.
            //
            $suit_option[$k] = 'no';
            $mess[$k] = "<td>&nbsp;</td><td><p class='small'>non suivie</p></td>";
        }
        $k++;
    }
    //
    //Affichage de la ligne
    //
    $display_eleve='no';
    $k=1;
    while ($k < $nb_periode) {
        if ($suit_option[$k] != 'no') {$display_eleve='yes';}
        $k++;
    }
    if ($display_eleve=='yes') {

        if ($multiclasses && $prev_classe != $eleve_classe && $order_by == 'classe') {
            if ($prev_classe != null) {
                echo "<hr style='width: 95%;'/>";
            }
            echo "<h3>Classe de " . $eleve_classe . "</h3>";
        }
        $prev_classe = $eleve_classe;
        //echo "<table width=\"750px\" border=\"1\" cellspacing=\"2\" cellpadding=\"5\">\n";
        echo "<table width=\"750\" border=\"1\" cellspacing=\"2\" cellpadding=\"5\">\n";
        echo "<tr>\n";
        echo "<td width=\"200\"><div align=\"center\">&nbsp;</div></td>\n";
        echo "<td width=\"30\"><div align=\"center\"><b>Moy.</b></div></td>\n";
        echo "<td><div align=\"center\"><b>$eleve_nom $eleve_prenom</b></div></td>\n";
        echo "</tr>\n";

        $num_id++;
        $k=1;
        while ($k < $nb_periode) {
            if ($current_group["classe"]["ver_periode"]["all"][$k] == 0) {
                echo "<tr><td><span title=\"$gepiClosedPeriodLabel\">$nom_periode[$k]</span></td>\n";
            } else {
                echo "<tr><td>$nom_periode[$k]</td>\n";
            }
            echo $mess[$k];
            $k++;
        }
        echo "</tr>\n";
        //echo"</table>\n<p></p>";
        echo"</table>\n";
        //echo"<p>&nbsp;</p>\n";
        //echo"<p></p>\n";
        echo"<br />\n";
    }
    $i++;

}
?>

<input type="hidden" name="is_posted" value="yes" />
<input type="hidden" name="id_groupe" value="<?php echo "$id_groupe";?>" />
<input type="hidden" name="periode_cn" value="<?php echo "$periode_cn";?>" />
<center><div id="fixe"><input type="submit" value="Enregistrer" /></div></center>
</form>
<br /><br /><br /><br />
<?php require("../lib/footer.inc.php");?>