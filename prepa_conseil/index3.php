<?php
/*
 * Last modification  : 02/03/2005
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

//Initialisation
unset($id_classe);
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : (isset($_GET["id_classe"]) ? $_GET["id_classe"] : NULL);
unset($login_eleve);
$login_eleve = isset($_POST["login_eleve"]) ? $_POST["login_eleve"] : (isset($_GET["login_eleve"]) ? $_GET["login_eleve"] : NULL);

// Quelques filtrages de départ pour pré-initialiser la variable qui nous importe ici : $login_eleve
if ($_SESSION['statut'] == "responsable") {
	$get_eleves = mysql_query("SELECT e.login " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$_SESSION['login']."')");
			
	if (mysql_num_rows($get_eleves) == 1) {
		// Un seul élève associé : on initialise tout de suite la variable $login_eleve
		$login_eleve = mysql_result($get_eleves, 0);
	} elseif (mysql_num_rows($get_eleves) == 0) {
		$login_eleve = false;
	}
	// Si le nombre d'élèves associés est supérieur à 1, alors soit $login_eleve a été déjà défini, soit il faut présenter un choix.
	
} else if ($_SESSION['statut'] == "eleve") {
	// Si l'utilisateur identifié est un élève, pas le choix, il ne peut consulter que son équipe pédagogique
	$login_eleve = $_SESSION['login'];
}

if ($login_eleve and $login_eleve != null) {
	// On récupère la classe de l'élève, pour déterminer automatiquement le nombre de périodes
	// On part du postulat que même si l'élève change de classe en cours d'année, c'est pour aller
	// dans une classe qui a le même nombre de périodes...
	$id_classe = mysql_result(mysql_query("SELECT id_classe FROM j_eleves_classes jec WHERE login = '".$login_eleve."' LIMIT 1"), 0);
}

//**************** EN-TETE *******************************
$titre_page = "Edition simplifiée des bulletins";
require_once("../lib/header.inc");
//**************** FIN EN-TETE ****************************
?>
<script type='text/javascript' language='javascript'>
function active(num) {
 document.form_choix_edit.choix_edit[num].checked=true;
}
</script>
<?php
echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

// Si on a eu une erreur sur l'association responsable->élève
if ($_SESSION['statut'] == "responsable" and $login_eleve == false) {
	echo "<p>Il semble que vous ne soyez associé à aucun élève. Contactez l'administrateur pour résoudre cette erreur.</p>";
	require "../lib/footer.inc.php";
	die();
}

// Vérifications de sécurité
if (
	($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesBulletinSimpleParent") != "yes") OR
	($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesBulletinSimpleEleve") != "yes")
	) {
	echo "<p>Vous n'êtes pas autorisé à visualiser cette page.</p>";
	require "../lib/footer.inc.php";
	die();
}


if (!isset($id_classe) and $_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve") {
    //$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
    //$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	if($_SESSION['statut'] == 'scolarite'){
		$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	//elseif(($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiAccesReleveProf")=='yes')){
	elseif($_SESSION['statut'] == 'professeur'){
		$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe");
	}
	//elseif(($_SESSION['statut'] == 'cpe')&&(getSettingValue("GepiAccesReleveCpe")=='yes')){
	elseif($_SESSION['statut'] == 'cpe'){
		$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	}
    $nombreligne = mysql_num_rows($calldata);
    echo "Total : $nombreligne classes|</p>\n";
    echo "<p>Cliquez sur la classe pour laquelle vous souhaitez extraire les bulletins</p>\n";
    //echo "<table border=0>\n";
	$nb_class_par_colonne=round($nombreligne/3);
        //echo "<table width='100%' border='1'>\n";
        echo "<table width='100%'>\n";
        echo "<tr valign='top' align='center'>\n";
        echo "<td align='left'>\n";
    $i = 0;
    while ($i < $nombreligne){
        $id_classe = mysql_result($calldata, $i, "id");
        $classe_liste = mysql_result($calldata, $i, "classe");
        //echo "<tr><td><a href='index3.php?id_classe=$id_classe'>$classe_liste</a></td></tr>\n";
	if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
		echo "</td>\n";
		//echo "<td style='padding: 0 10px 0 10px'>\n";
		echo "<td align='left'>\n";
	}
        echo "<a href='index3.php?id_classe=$id_classe'>$classe_liste</a><br />\n";
        $i++;
    }
    echo "</table>\n";
    
} else if ($_SESSION['statut'] == "responsable" AND !isset($login_eleve)) {
	// Si on est là, c'est que le responsable est responsable de plusieurs élèves. Il doit donc
	// choisir celui pour lequel il souhaite visualiser le bulletin simplifié
	
	$quels_eleves = mysql_query("SELECT e.login, e.nom, e.prenom " .
				"FROM eleves e, responsables2 re, resp_pers r WHERE (" .
				"e.ele_id = re.ele_id AND " .
				"re.pers_id = r.pers_id AND " .
				"r.login = '" . $_SESSION['login'] . "')");

	echo "<p>Cliquez sur le nom de l'élève pour lequel vous souhaitez visualiser un bulletin simplifié :</p>";	
	while ($current_eleve = mysql_fetch_object($quels_eleves)) {
		echo "<p><a href='index3.php?login_eleve=".$current_eleve->login."'>".$current_eleve->prenom." ".$current_eleve->prenom."</a></p>";
	}	
} else if (!isset($choix_edit)) {
    if ($_SESSION['statut'] != "responsable" and $_SESSION['statut'] != "eleve") {
	    echo " | <a href = \"index3.php\">Choisir une autre classe</a></p>";
	    $classe_eleve = mysql_query("SELECT * FROM classes WHERE id='$id_classe'");
	    $nom_classe = mysql_result($classe_eleve, 0, "classe");
	    echo "<p class='grand'>Classe de $nom_classe</p>\n";
	    echo "<form enctype=\"multipart/form-data\" action=\"edit_limite.php\" method=\"post\" name=\"form_choix_edit\" target=\"_blank\">\n";
	    echo "<table><tr>\n";
	    echo "<td><input type=\"radio\" name=\"choix_edit\" value=\"1\" checked /></td>\n";
	    echo "<td>Les bulletins simplifiés de tous les élèves de la classe</td></tr>\n";
	
	    $call_suivi = mysql_query("SELECT DISTINCT professeur FROM j_eleves_professeurs WHERE id_classe='$id_classe' ORDER BY professeur");
	    $nb_lignes = mysql_num_rows($call_suivi);
	    $indice = 1;
	    if ($nb_lignes > 1) {
	        echo "<tr>\n";
	        echo "<td><input type=\"radio\" name=\"choix_edit\" value=\"3\" /></td>\n";
	        echo "<td>Uniquement les bulletins simplifiés des élèves dont le ".getSettingValue("gepi_prof_suivi")." est :\n";
	        echo "<select size=\"1\" name=\"login_prof\" onclick=\"active(1)\">\n";
	        $i=0;
	        while ($i < $nb_lignes) {
	            $login_pr = mysql_result($call_suivi,$i,"professeur");
	            $call_prof = mysql_query("SELECT * FROM utilisateurs WHERE login='$login_pr'");
	            $nom_prof = mysql_result($call_prof,0,"nom");
	            $prenom_prof = mysql_result($call_prof,0,"prenom");
	            echo "<option value=".$login_pr.">".$nom_prof." ".$prenom_prof."</option>\n";
	            $i++;
	        }
	        echo "</select></td></tr>\n";
	        $indice = 2;
	    }
	
	
	    echo "<tr>\n";
	    echo "<td><input type=\"radio\" name=\"choix_edit\" value=\"2\" /></td>\n";
	    echo "<td>Uniquement le bulletin simplifié de l'élève sélectionné ci-contre : \n";
	    echo "<select size=\"1\" name=\"login_eleve\" onclick=\"active(".$indice.")\">\n";
	    $call_eleve = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe = '$id_classe' and j.login=e.login) order by nom");
	    $nombreligne = mysql_num_rows($call_eleve);
	    $i = "0" ;
	    while ($i < $nombreligne) {
	        $eleve = mysql_result($call_eleve, $i, 'login');
	        $nom_el = mysql_result($call_eleve, $i, 'nom');
	        $prenom_el = mysql_result($call_eleve, $i, 'prenom');
	        echo "<option value=$eleve>$nom_el  $prenom_el </option>\n";
	        $i++;
	    }
	    echo "</select></td></tr></table>\n";
    } else {
    	$eleve = mysql_query("SELECT e.nom, e.prenom FROM eleves e WHERE e.login = '".$login_eleve."'");
    	$prenom_eleve = mysql_result($eleve, 0, "prenom");
    	$nom_eleve = mysql_result($eleve, 0, "nom");
    	
	    echo "<p class='grand'>Elève : ".$prenom_eleve." ".$nom_eleve."</p>\n";
	    echo "<form enctype=\"multipart/form-data\" action=\"edit_limite.php\" method=\"post\" name=\"form_choix_edit\" target=\"_blank\">\n";
	    echo "<input type=\"hidden\" name=\"choix_edit\" value=\"2\" />\n";
	    echo "<input type=\"hidden\" name=\"login_eleve\" value=\"".$login_eleve."\" />\n";
    }
    echo "<p>Choisissez la(les) période(s) : </p><br />\n";
    include "../lib/periodes.inc.php";
    echo "De la période : <select size=1 name=periode1>\n";
    $i = "1" ;
    while ($i < $nb_periode) {
       echo "<option value=$i>$nom_periode[$i] </option>\n";
       $i++;
    }
    echo "</select>\n";
    echo "&nbsp;à la période : <select size=1 name=periode2>\n";
    $i = "1" ;
    while ($i < $nb_periode) {
       echo "<option value=$i>$nom_periode[$i] </option>\n";
       $i++;
    }
    echo "</select>\n";
    echo "<input type=hidden name=id_classe value=$id_classe />\n";
    echo "<br /><br /><center><input type=submit value=Valider /></center>\n";
    echo "</form>\n";
}
require("../lib/footer.inc.php");
?>