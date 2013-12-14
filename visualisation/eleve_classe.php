<?php
/*
 * $Id$
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$datay1 = array();
$datay2 = array();
$etiquette = array();
$graph_title = "";
$v_legend1 = "";
$v_legend2 = "";

//**************** EN-TETE *****************
$titre_page = "Outil de visualisation | Elève vis à vis de la classe";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$periode = isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);
$suiv = isset($_GET['suiv']) ? $_GET['suiv'] : 'no';
$prec = isset($_GET['prec']) ? $_GET['prec'] : 'no';
$v_eleve = isset($_POST['v_eleve']) ? $_POST['v_eleve'] : (isset($_GET['v_eleve']) ? $_GET['v_eleve'] : NULL);

include "../lib/periodes.inc.php";
?>
<!--p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='index.php'>Autre outil de visualisation</a-->
<?php

if (!isset($id_classe)) {
    echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='index.php'>Autre outil de visualisation</a></p>\n";

	echo "<p>Sélectionnez la classe :<br />\n";
    //$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
    //$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");

	if($_SESSION['statut']=='scolarite'){
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
	}
	elseif($_SESSION['statut']=='cpe'){
		/*
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
		*/
		// Les cpe ont accès à tous les bulletins, donc aussi aux courbes
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}

	if(((getSettingValue("GepiAccesReleveProfToutesClasses")=="yes")&&($_SESSION['statut']=='professeur'))||
		((getSettingValue("GepiAccesReleveScol")=='yes')&&($_SESSION['statut']=='scolarite'))) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}
	/*
	if(((getSettingValue("GepiAccesReleveProfToutesClasses")=="yes")&&($_SESSION['statut']=='professeur'))||
		((getSettingValue("GepiAccesReleveScol")=='yes')&&($_SESSION['statut']=='scolarite'))||
		((getSettingValue("GepiAccesReleveCpeTousEleves")=='yes')&&($_SESSION['statut']=='cpe'))) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
	}
	elseif((getSettingValue("GepiAccesReleveCpe")=='yes')&&($_SESSION['statut']=='cpe')) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
	}
	*/

	$call_data=mysqli_query($GLOBALS["mysqli"], $sql);

    $nombre_lignes = mysqli_num_rows($call_data);
    $i = 0;
	$nb_class_par_colonne=round($nombre_lignes/3);
        //echo "<table width='100%' border='1'>\n";
        echo "<table width='100%' summary='Choix de la classe'>\n";
        echo "<tr valign='top' align='center'>\n";
        echo "<td align='left'>\n";
    while ($i < $nombre_lignes){
		$classe = old_mysql_result($call_data, $i, "classe");
		$ide_classe = old_mysql_result($call_data, $i, "id");

		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
		}

		echo "<a href='eleve_classe.php?id_classe=$ide_classe'>$classe</a><br />\n";
		$i++;
    }
    //echo "</p>\n";
        echo "</table>\n";
} else {
	echo "<form action='".$_SERVER['PHP_SELF']."#graph' name='form1' method='post'>\n";

    echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='index.php'>Autre outil de visualisation</a>\n";
    //echo " | <a href=\"eleve_classe.php\">Choisir une autre classe</a>\n";

	if($_SESSION['statut']=='scolarite'){
		//$sql="SELECT id,classe FROM classes ORDER BY classe";
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	if($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc,j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgc.id_groupe=jgp.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
	}
	if($_SESSION['statut']=='cpe'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe=c.id ORDER BY c.classe";
	}
	if($_SESSION['statut']=='administrateur'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	/*
	if(($_SESSION['statut']=='scolarite')&&(getSettingValue("GepiAccesVisuToutesEquipScol") =="yes")){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	if(($_SESSION['statut']=='cpe')&&(getSettingValue("GepiAccesVisuToutesEquipCpe") =="yes")){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	if(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesVisuToutesEquipProf") =="yes")){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	*/
	$chaine_options_classes="";

	$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_class_tmp)>0){
		$id_class_prec=0;
		$id_class_suiv=0;
		$temoin_tmp=0;
		while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
			if($lig_class_tmp->id==$id_classe){
				$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
				$temoin_tmp=1;
				if($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
					$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
					$id_class_suiv=$lig_class_tmp->id;
				}
				else{
					$id_class_suiv=0;
				}
			}
			else {
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
			}
			if($temoin_tmp==0){
				$id_class_prec=$lig_class_tmp->id;
			}
		}
	}
	// =================================

	if($id_class_prec!=0){
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec";
		if(isset($periode)) {echo "&amp;periode=$periode";}
		echo "#graph'>Classe précédente</a>";
	}
	if($chaine_options_classes!="") {
		echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
		echo $chaine_options_classes;
		echo "</select>\n";
	}
	if($id_class_suiv!=0){
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv";
		if(isset($periode)) {echo "&amp;periode=$periode";}
		echo "#graph'>Classe suivante</a>";
	}

	if(isset($periode)) {echo "<input type='hidden' name='periode' value='$periode' />";}
	echo "</p>\n";
	echo "</form>\n";



    if (!$periode) {
        $call_classe = mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM classes WHERE id = '$id_classe'");
        $classe = old_mysql_result($call_classe, "0", "classe");

        ?>
        <p><span class='grand'>Classe : <?php echo $classe; ?></span><br />
        <br />Choisissez quelle période vous souhaitez visualiser :<br />
        <form enctype="multipart/form-data" action="eleve_classe.php?temp=0#graph" method="post">
        <?php
        $i="1";
        while ($i < $nb_periode) {
            echo "<input type='radio' name='periode' id='periode_$i' value='$i' ";
			if ($i == '1') { echo "CHECKED";}
			echo " /> <label for='periode_$i' style='cursor:pointer;'>$nom_periode[$i]</label><br />\n";
        $i++;
        }
        ?>
        <input type='radio' id='annee_complete' name='periode' value='annee' /> <label for='annee_complete' style='cursor:pointer;'>Année complète</label><br />
        <input type='submit' value='Visualiser' />
        <input type='hidden' name='id_classe' value='<?php echo $id_classe; ?>' />
        </form>
        <!--/p-->
        <br />
        <?php
    } else {
        $call_classe = mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM classes WHERE id = '$id_classe'");
        $classe = old_mysql_result($call_classe, "0", "classe");
        $call_eleve = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' and e.login = c.login) order by nom");
        $nombreligne = mysqli_num_rows($call_eleve);

        if (!isset($v_eleve)) {$v_eleve = @old_mysql_result($call_eleve, 0, 'login');}

        if ($suiv == 'yes') {
            $i = "0" ;
            while ($i < $nombreligne) {
                if ($v_eleve == old_mysql_result($call_eleve, $i, 'login') and ($i < $nombreligne-1)) {$v_eleve = old_mysql_result($call_eleve, $i+1, 'login');$i = $nombreligne;}
            $i++;
            }
        }
        if ($prec == 'yes') {
            $i = "0" ;
            while ($i < $nombreligne) {
                if ($v_eleve == old_mysql_result($call_eleve, $i, 'login') and ($i > '0')) {$v_eleve = old_mysql_result($call_eleve, $i-1, 'login');$i = $nombreligne;}
            $i++;
            }
        }
        ?>
    <table border='0' summary='Choix'><tr><td><p class='bold'>|<a href="eleve_classe.php?id_classe=<?php echo $id_classe; ?>">Choisir une autre période</a>|
        <a href="eleve_classe.php?id_classe=<?php echo $id_classe; ?>&amp;v_eleve=<?php echo $v_eleve; ?>&amp;prec=yes&amp;periode=<?php echo $periode; ?>">Elève précédent</a>|
        <a href="eleve_classe.php?id_classe=<?php echo $id_classe; ?>&amp;suiv=yes&amp;periode=<?php echo $periode; ?>&amp;v_eleve=<?php echo $v_eleve; ?>">Elève suivant</a>|
        </p></td>

        <td><form enctype="multipart/form-data" action="eleve_classe.php" method=post>
        <select size='1' name='v_eleve' onchange="this.form.submit()">
        <?php
        $i = "0" ;
        while ($i < $nombreligne) {
            $eleve = old_mysql_result($call_eleve, $i, 'login');
            $nom_el = old_mysql_result($call_eleve, $i, 'nom');
            $prenom_el = old_mysql_result($call_eleve, $i, 'prenom');
            echo "<option value=$eleve";
            if ($v_eleve == $eleve) {echo " selected ";}
            echo ">$nom_el  $prenom_el </option>";
        $i++;
        }
        ?>
        </select>
        <input type='hidden' name='id_classe' value='<?php echo $id_classe; ?>' />
        <input type='hidden' name='periode' value='<?php echo $periode; ?>' />
        </form></td></tr></table>
        <?php
        // On appelle les informations de l'utilisateur pour les afficher :
        $call_eleve_info = mysqli_query($GLOBALS["mysqli"], "SELECT login,nom,prenom FROM eleves WHERE login='$v_eleve'");
        $eleve_nom = old_mysql_result($call_eleve_info, "0", "nom");
        $eleve_prenom = old_mysql_result($call_eleve_info, "0", "prenom");

        if ($periode != 'annee') {
                $temp = my_strtolower($nom_periode[$periode]);
        } else {
                $temp = 'Année complète';
        }
        $graph_title = $eleve_nom." ".$eleve_prenom.", ".$classe.", ".$temp;
        $v_legend1 = "";
        $v_legend2 = "";
        $v_legend1 = $eleve_nom." ".$eleve_prenom;
        $v_legend2 = "Moy. ".$classe ;
        echo "<p>$eleve_nom  $eleve_prenom, classe de $classe   |  $temp</p>";
        echo "<table class='boireaus' border='1' cellspacing='2' cellpadding='5' summary='Matières/Notes/Appréciations'>";
        echo "<tr><th width='100'><p>Matière</p></th><th width='100'><p>Note élève</p></th><th width='100'><p>Moyenne classe</p></th><th width='100'><p>Différence</p></th></tr>";

        $affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
        if ($affiche_categories == "y") {
            $affiche_categories = true;
        } else {
            $affiche_categories = false;
        }

        if ($affiche_categories) {
            // On utilise les valeurs spécifiées pour la classe en question
            $call_groupes = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT jgc.id_groupe ".
            "FROM j_eleves_groupes jeg, j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
            "WHERE ( " .
            "jeg.login = '" . $v_eleve ."' AND " .
            "jgc.id_groupe = jeg.id_groupe AND " .
            "jgc.categorie_id = jmcc.categorie_id AND " .
            "jgc.id_classe = '".$id_classe."' AND " .
            "jgm.id_groupe = jgc.id_groupe AND " .
            "m.matiere = jgm.id_matiere " .
			"AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n')".
            ") " .
            "ORDER BY jmcc.priority,jgc.priorite,m.nom_complet");
        } else {
            $call_groupes = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT jgc.id_groupe, jgc.coef " .
            "FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_eleves_groupes jeg " .
            "WHERE ( " .
            "jeg.login = '" . $v_eleve . "' AND " .
            "jgc.id_groupe = jeg.id_groupe AND " .
            "jgc.id_classe = '".$id_classe."' AND " .
            "jgm.id_groupe = jgc.id_groupe " .
			"AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n')".
            ") " .
            "ORDER BY jgc.priorite,jgm.id_matiere");
        }


        $nombre_lignes = mysqli_num_rows($call_groupes);
        $i = 0;
        $compteur = 0;
        $moyenne_classe = '';
        $prev_cat_id = null;
		$alt=1;
        while ($i < $nombre_lignes) {
            $inserligne="no";
            $group_id = old_mysql_result($call_groupes, $i, "id_groupe");
            $current_group = get_group($group_id);

            if ($periode != 'annee') {
                if (in_array($v_eleve, $current_group["eleves"][$periode]["list"])) {
                    $inserligne="yes";
                    $note_eleve_query=mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_notes WHERE (login='$v_eleve' AND periode='$periode' AND id_groupe='" . $current_group["id"] . "')");
                    $eleve_matiere_statut = @old_mysql_result($note_eleve_query, 0, "statut");
                    $note_eleve = @old_mysql_result($note_eleve_query, 0, "note");
                    if ($eleve_matiere_statut != "") { $note_eleve = $eleve_matiere_statut;}
                    if ($note_eleve == '') {$note_eleve = '-';}
                    $moyenne_classe_query = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(note),1) as moyenne FROM matieres_notes WHERE (periode='$periode' AND id_groupe='" . $current_group["id"] . "' AND statut ='')");
                    $moyenne_classe = old_mysql_result($moyenne_classe_query, 0, "moyenne");
                }
            } else {
                $z = 1;
                $response = "no";
                while ($z < $nb_periode) {
                    if (in_array($v_eleve, $current_group["eleves"][$z]["list"])) $reponse = "yes";
                    $z++;
                }
                if ($reponse == 'yes') {
                    // L'élève suit la matière au moins sur une des périodes de l'année, donc on affiche la matière dans le tableau.
                    $inserligne="yes";
                    $note_eleve_annee_query=mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (login='$v_eleve' AND id_groupe='" . $current_group["id"] ."' AND statut='')");
                    $note_eleve = @old_mysql_result($note_eleve_annee_query, 0, "moyenne");
                    if ($note_eleve == '') {$note_eleve = '-';}
                    $z = 1;
                    while ($z < $nb_periode) {
                        $moyenne_classe_query = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(note),1) as moyenne FROM matieres_notes WHERE (periode='$z' AND id_groupe='" . $current_group["id"] . "' AND statut ='')");
                        $temp = @old_mysql_result($moyenne_classe_query, 0, "moyenne");
                        $moyenne_classe = $moyenne_classe + $temp;
                        $z++;
                    }
                    $moyenne_classe = round($moyenne_classe/($nb_periode-1),1);
                }
            }
            if ($inserligne == "yes") {

                if ($affiche_categories) {
                // On regarde si on change de catégorie de matière
                    if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat_id) {
                        $prev_cat_id = $current_group["classes"]["classes"][$id_classe]["categorie_id"];
                        // On est dans une nouvelle catégorie
                        // On récupère les infos nécessaires, et on affiche une ligne
                        //$cat_name = html_entity_decode(old_mysql_result(mysql_query("SELECT nom_complet FROM matieres_categories WHERE id = '" . $current_group["classes"]["classes"][$id_classe]["categorie_id"] . "'"), 0));
                        $cat_name = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT nom_complet FROM matieres_categories WHERE id = '" . $current_group["classes"]["classes"][$id_classe]["categorie_id"] . "'"), 0);
                        // On détermine le nombre de colonnes pour le colspan
                        $nb_total_cols = 4;

                        // On a toutes les infos. On affiche !
                        echo "<tr>";
                        echo "<td colspan='" . $nb_total_cols . "'>";
                        echo "<p style='padding: 5; margin:0; font-size: 15px;'>".$cat_name."</p></td>";
                        echo "</tr>";
                    }
                }

                $moyenne_classe = old_mysql_result($moyenne_classe_query, 0, "moyenne");
                if ($moyenne_classe == '') {$moyenne_classe = '-';}
                if (($note_eleve == "-") or ($moyenne_classe == "-")) {$difference = '-';} else {$difference = $note_eleve-$moyenne_classe;}
                //echo "<tr><td><p>" . $current_group["description"] . "</p></td><td><p>$note_eleve";
				$alt=$alt*(-1);
                echo "<tr class='lig$alt'><td><p>" . htmlspecialchars($current_group["description"]) . "</p></td><td><p>$note_eleve";
                echo "</p></td><td><p>$moyenne_classe</p></td><td><p>$difference</p></td></tr>";
                (preg_match("/^[0-9\.\,]{1,}$/", $note_eleve)) ? array_push($datay1,"$note_eleve") : array_push($datay1,"0");
                (preg_match("/^[0-9\.\,]{1,}$/", $moyenne_classe)) ? array_push($datay2,"$moyenne_classe") : array_push($datay2,"0");
                //array_push($etiquette,$current_group["matiere"]["nom_complet"]);
                array_push($etiquette,rawurlencode($current_group["matiere"]["nom_complet"]));
                $compteur++;
            }
            $i++;
        }
        echo "</table>";
        ?>
    <br />
    <a name="graph"></a>
    <table border='0' summary='Choix'><tr><td><span class=bold>|<a href="eleve_classe.php?id_classe=<?php echo $id_classe; ?>">Choisir une autre période</a>|
        <a href="eleve_classe.php?id_classe=<?php echo $id_classe; ?>&amp;v_eleve=<?php echo $v_eleve; ?>&amp;prec=yes&amp;periode=<?php echo $periode; ?>#graph">Elève précédent</a>|
        <a href="eleve_classe.php?id_classe=<?php echo $id_classe; ?>&amp;suiv=yes&amp;periode=<?php echo $periode; ?>&amp;v_eleve=<?php echo $v_eleve; ?>#graph">Elève suivant</a>|</span></td>

        <td><form enctype="multipart/form-data" action="eleve_classe.php?temp=0#graph" method="post">
        <select size=1 name=v_eleve onchange="this.form.submit()">
        <?php
        $i = "0" ;
        while ($i < $nombreligne) {
            $eleve = old_mysql_result($call_eleve, $i, 'login');
            $nom_el = old_mysql_result($call_eleve, $i, 'nom');
            $prenom_el = old_mysql_result($call_eleve, $i, 'prenom');
            echo "<option value=$eleve";
            if ($v_eleve == $eleve) {
				echo " selected ";
				// On récupère des infos sur l'élève courant:
				$v_elenoet=old_mysql_result($call_eleve, $i, 'elenoet');
				$v_naissance=old_mysql_result($call_eleve, $i, 'naissance');
				$tmp_tab_naissance=explode("-",$v_naissance);
				$v_naissance=$tmp_tab_naissance[2]."/".$tmp_tab_naissance[1]."/".$tmp_tab_naissance[0];
				$v_sexe=old_mysql_result($call_eleve, $i, 'sexe');
				$v_eleve_nom_prenom="$nom_el  $prenom_el";
			}
            echo ">$nom_el  $prenom_el </option>\n";
	        $i++;
        }
        ?>
        </select>
        <input type='hidden' name='id_classe' value='<?php echo $id_classe; ?>' />
        <input type='hidden' name='periode' value='<?php echo $periode; ?>' />
        </form></td>
        <?php

		echo "<td>\n";
		//echo $v_eleve;

		// ============================================
		// Création de l'infobulle:

		$titre=$v_eleve_nom_prenom;
		//$texte="<table border='0'>\n";
		$texte="<div align='center'>\n";
		//$texte.="<tr>\n";
		if($v_elenoet!=""){
			$photo=nom_photo($v_elenoet);
			//if("$photo"!=""){
			if($photo) {
				$texte.="<img src='".$photo."' width='150' alt=\"$v_eleve_nom_prenom\" />";
				$texte.="<br />\n";
			}
		}
		//$texte.="<td>\n";
		$texte.="Né";
		if($v_sexe=="F"){
			$texte.="e";
		}
		$texte.=" le $v_naissance\n";
		//$texte.="</td>\n";
		//$texte.="</tr>\n";
		//$texte.="</table>\n";
		$texte.="</div>\n";

		//echo creer_div_infobulle('info_popup_eleve',$titre,"",$texte,"",30,0,'y','y','n','n');
		$tabdiv_infobulle[]=creer_div_infobulle('info_popup_eleve',$titre,"",$texte,"",14,0,'y','y','n','n');

		// ============================================

		// Insertion du lien permettant l'affichage de l'infobulle:
		echo "<a href='#' onmouseover=\"afficher_div('info_popup_eleve','y',-100,20);\"";
		//echo " onmouseout=\"cacher_div('info_popup_eleve');\"";
		echo ">";
		echo "<img src='../images/icons/buddy.png' alt='Informations élève' />";
		echo "</a>";

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

        $temp1=implode("|", $datay1);
		if ( empty($temp1) ) { $temp1 = 0; }
        $temp2=implode("|", $datay2);
		if ( empty($temp2) ) { $temp2 = 0; }
        $etiq = implode("|", $etiquette);
        $graph_title = urlencode($graph_title);
        $v_legend1 = urlencode($v_legend1);
        $v_legend2 = urlencode($v_legend2);

        //echo "<img src='draw_artichow1.php?temp1=$temp1&temp2=$temp2&etiquette=$etiq&titre=$graph_title&v_legend1=$v_legend1&v_legend2=$v_legend2&compteur=$compteur&nb_data=3'>";
        //echo "<img src='draw_artichow1.php?temp1=$temp1&amp;temp2=$temp2&amp;etiquette=".rawurlencode("$etiq")."&amp;titre=$graph_title&amp;v_legend1=$v_legend1&amp;v_legend2=$v_legend2&amp;compteur=$compteur&amp;nb_data=3'>\n";
        echo "<img src='draw_artichow1.php?temp1=$temp1&amp;temp2=$temp2&amp;etiquette=$etiq&amp;titre=$graph_title&amp;v_legend1=$v_legend1&amp;v_legend2=$v_legend2&amp;compteur=$compteur&amp;nb_data=3' alt='Graphe de ".urldecode($v_legend1)."' />\n";

        echo "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />";

    }
}

//===========================================================
echo "<p><em>NOTE&nbsp;:</em></p>\n";
require("../lib/textes.inc.php");
echo "<p style='margin-left: 3em;'>$explication_bulletin_ou_graphe_vide</p>\n";
//===========================================================

require("../lib/footer.inc.php");
?>
