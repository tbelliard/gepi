<?php
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

if (isset($_POST['action']) and ($_POST['action'] == "reg_cperesp")) {
	check_token();
    $msg = '';
    $notok = false;
    $call_data = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM classes ORDER BY classe");
    $nombre_lignes = mysqli_num_rows($call_data);

    for($i=0;$i<$nombre_lignes;$i++){

        $id_classe = mysql_result($call_data, $i, "id");
        if (isset($_POST[$id_classe]) and ($_POST[$id_classe] == "yes")) {
            // On récupère tous les élèves de la classe
            $call_eleves = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT login FROM j_eleves_classes WHERE (id_classe='$id_classe' AND periode='1')");
            $nb_eleves = mysqli_num_rows($call_eleves);
            for ($j=0;$j<$nb_eleves;$j++) {
                // Pour chaque élève, on regarde si un enregistrement existe déjà
                $eleve_login = mysql_result($call_eleves, $j, "login");
                $test = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM j_eleves_cpe WHERE e_login='$eleve_login'");
                $nbtest = mysqli_num_rows($test);
                if ($nbtest == "0") { // Si aucun enregistrement, on en créé un nouveau
                    $reg_data = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO j_eleves_cpe SET e_login='$eleve_login', cpe_login='" . $_POST['reg_cpelogin'] . "'");
                    if (!$reg_data) { $msg .= "Erreur lors lors de l'insertion d'un nouvel enregistrement."; $notok = true;}
                } else { // Si un enregistrement existe, on le met à jour si nécessaire
                    $test_cpelogin = mysql_result($test, "0", "cpe_login");
                    if ($test_cpelogin != $_POST['reg_cpelogin']) {
                        $reg_data = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE j_eleves_cpe SET cpe_login='". $_POST['reg_cpelogin'] . "' WHERE e_login='$eleve_login'");
                        if (!$reg_data) { $msg .= "Erreur lors de la mise à jour d'un enregistrement."; $notok = true;}
                    }
                }
            }
        }
    }
    if ($notok == true) {
        $msg .= "Il y a eu des erreurs lors de l'enregistrement des données";
        } else {
        $msg .= "L'enregistrement des données s'est bien passé.";
    }
}



$disp_filter = null;
if (isset($_GET['disp_filter'])) {
	$disp_filter = $_GET['disp_filter'];
} else {
	$disp_filter = "only_undefined";
}

//**************** EN-TETE **************************************
$titre_page = "Gestion des classes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************
$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
?>
<p class="bold"><a href="./index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<p>Sélectionnez un CPE, et cochez les classes pour lesquelles vous souhaitez définir ce CPE comme responsable du suivi vie scolaire.</p>
<p>ATTENTION ! Pour les élèves des classes sélectionnées, le paramétrage effectué ici écrase d'éventuels paramétrages précédents. Les élèves appartenant à des classes non cochées conservent leur paramétrage actuel.</p>
<p>Remarque : la classe d'appartenance de l'élève prise en compte est celle de la première période de l'année.</p>
<p><a href="cpe_resp.php?disp_filter=all">Afficher toutes les classes</a> || <a href="cpe_resp.php?disp_filter=only_undefined">Afficher les classes non-paramétrées</a></p>
<?php

	echo "<script type='text/javascript'>

  function checkAll(){
    champs_input=document.getElementsByTagName('input');
    for(i=0;i<champs_input.length;i++){
      type=champs_input[i].getAttribute('type');
      if(type=='checkbox'){
        champs_input[i].checked=true;
      }
    }
  }
  function UncheckAll(){
    champs_input=document.getElementsByTagName('input');
    for(i=0;i<champs_input.length;i++){
      type=champs_input[i].getAttribute('type');
      if(type=='checkbox'){
        champs_input[i].checked=false;
      }
    }
  }
</script>
";
	echo "<p><a href='javascript:checkAll()'>Tout cocher</a> - <a href='javascript:UncheckAll()'>Tout décocher</a></p>\n";

	echo "<form name='setCpeResp' action='cpe_resp.php?disp_filter=" . $disp_filter . "' method='post'>";
	echo add_token_field();

	echo "<p><select size = 1 name='reg_cpelogin'>";
	$cperesp = "vide";
	$call_cpe = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT login,nom,prenom FROM utilisateurs WHERE (statut='cpe' AND etat='actif')");
	$nb = mysqli_num_rows($call_cpe);
	for ($i="0";$i<$nb;$i++) {
		$cperesp = mysql_result($call_cpe, $i, "login");
		$cperesp_nom = mysql_result($call_cpe, $i, "nom");
		$cperesp_prenom = mysql_result($call_cpe, $i, "prenom");
		echo "<option value='$cperesp'>" . $cperesp_prenom . " " . $cperesp_nom ;
		echo "</option>";
	}
	echo "</select>";
	// On va chercher les classes déjà existantes, et on les affiche.

	$call_data = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM classes ORDER BY classe");
	$nombre_lignes = mysqli_num_rows($call_data);

	if ($nombre_lignes != 0) {
		$flag = 1;
		echo "<table style='margin-left: 50px;' cellpadding=3 cellspacing=0 border=0>";
		$i = 0;
		while ($i < $nombre_lignes){
			$id_classe = mysql_result($call_data, $i, "id");
			$classe = mysql_result($call_data, $i, "classe");
			$nb_per = mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select id_classe from periodes where id_classe = '$id_classe'"));

			$test_existing = mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "select count(*) total" .
										" from j_eleves_cpe e, j_eleves_classes c" .
										" where (" .
										"e.e_login = c.login" .
										" and " .
										"c.id_classe = '" . $id_classe . "'" .
										")"), "0", "total");
			if ($disp_filter == "all" OR ($disp_filter == "only_undefined" AND $test_existing == "0")) {

				if ($nb_per != "0") {
					echo "<tr";
					if ($flag=="1") {
						echo " class='fond_sombre'";
						$flag = "0";
					} else {
						$flag=1;
					}

					echo ">\n";
					echo "<td><input type='checkbox' name='".$id_classe."' id='id".$id_classe."' value='yes' /></td>\n";
					echo "<td><label for='id".$id_classe."' style='cursor: pointer;'><b>$classe</b></label></td>\n";
				}
				echo "</tr>\n";
			}
			$i++;
		}
		echo "</table>\n";
		echo "<input type='hidden' name='action' value='reg_cperesp' />\n";
		echo "<p><input type='submit' value='Enregistrer' /></p>\n";

	} else {
		echo "<p class='grand'>Attention : aucune classe n'a été définie dans la base GEPI !</p>\n";
	}
?>
</form>
<p><br /></p>
<?php require("../lib/footer.inc.php");?>