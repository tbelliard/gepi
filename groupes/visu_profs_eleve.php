<?php
/*
 *
 *  Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$error_login = false;
// Quelques filtrages de départ pour pré-initialiser la variable qui nous importe ici : $login_eleve
$login_eleve = isset($_GET['login_eleve']) ? $_GET['login_eleve'] : (isset($_POST['login_eleve']) ? $_POST["login_eleve"] : null);
if ($_SESSION['statut'] == "responsable") {
	$sql="(SELECT e.login " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$_SESSION['login']."' AND (re.resp_legal='1' OR re.resp_legal='2')))";
	if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
		$sql.=" UNION (SELECT e.login " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$_SESSION['login']."' AND re.resp_legal='0' AND re.acces_sp='y'))";
	}
	$sql.=";";
	$get_eleves = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (mysqli_num_rows($get_eleves) == 1) {
		// Un seul élève associé : on initialise tout de suite la variable $login_eleve
		$login_eleve = mysql_result($get_eleves, 0);
	} elseif (mysqli_num_rows($get_eleves) == 0) {
		$error_login = true;
	}
	// Si le nombre d'élèves associés est supérieur à 1, alors soit $login_eleve a été déjà défini, soit il faut présenter le formulaire.

} else if ($_SESSION['statut'] == "eleve") {
	// Si l'utilisateur identifié est un élève, pas le choix, il ne peut consulter que son équipe pédagogique
	if ($login_eleve != null and (my_strtoupper($login_eleve) != my_strtoupper($_SESSION['login']))) {
		tentative_intrusion(2, "Tentative d'un élève d'accéder à l'équipe pédagogique d'un autre élève.");
	}
	$login_eleve = $_SESSION['login'];
}

//**************** EN-TETE **************************************
$titre_page = "Equipe pédagogique";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

echo "<p class='bold'>";
echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
//echo "</p>\n";

// Quelques vérifications de droits d'accès.
if ($_SESSION['statut'] == "responsable" and $error_login == true) {
	echo "<p>Il semble que vous ne soyez associé à aucun élève. Contactez l'administrateur pour résoudre cette erreur.</p>";
	require "../lib/footer.inc.php";
	die();
}

if (
	($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesEquipePedaParent") != "yes") OR
	($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesEquipePedaEleve") != "yes") OR
	($_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve")
	) {
	tentative_intrusion(1, "Tentative d'accès à l'équipe pédagogique sans y être autorisé.");
	echo "<p>Vous n'êtes pas autorisé à visualiser cette page.</p>";
	require "../lib/footer.inc.php";
	die();
}

// Et une autre vérification de sécurité : est-ce que si on a un statut 'responsable' le $login_eleve est bien un élève dont le responsable a la responsabilité
if ($login_eleve != null and $_SESSION['statut'] == "responsable") {
	$sql="(SELECT e.login " .
			"FROM eleves e, responsables2 re, resp_pers r " .
			"WHERE (" .
			"e.login = '" . $login_eleve . "' AND " .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2')))";
	if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
		$sql.=" UNION (SELECT e.login " .
			"FROM eleves e, responsables2 re, resp_pers r " .
			"WHERE (" .
			"e.login = '" . $login_eleve . "' AND " .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '" . $_SESSION['login'] . "' AND re.resp_legal='0' AND re.acces_sp='y'))";
	}
	$sql.=";";
	$test = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (mysqli_num_rows($test) == 0) {
	    tentative_intrusion(2, "Tentative par un parent d'accéder à l'équipe pédagogique d'un élève dont il n'est pas responsable légal.");
	    echo "Vous ne pouvez visualiser que les relevés de notes des élèves pour lesquels vous êtes responsable légal.\n";
	    require("../lib/footer.inc.php");
		die();
	}
}

// Maintenant on arrive au code en lui-même.
// On commence par traiter le cas où il faut sélectionner un élève (cas d'un responsable de plusieurs élèves)

if ($login_eleve == null and $_SESSION['statut'] == "responsable") {
	echo "</p>\n";
	// Si on est là normalement c'est parce qu'on a un responsable de plusieurs élèves qui n'a pas encore choisi d'élève.
	$sql = "(SELECT e.login, e.nom, e.prenom " .
				"FROM eleves e, responsables2 re, resp_pers r WHERE (" .
				"e.ele_id = re.ele_id AND " .
				"re.pers_id = r.pers_id AND " .
				"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2')))";
	if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
		$sql.=" UNION (SELECT e.login, e.nom, e.prenom " .
				"FROM eleves e, responsables2 re, resp_pers r WHERE (" .
				"e.ele_id = re.ele_id AND " .
				"re.pers_id = r.pers_id AND " .
				"r.login = '" . $_SESSION['login'] . "' AND re.resp_legal='0' AND re.acces_sp='y'))";
	}
	$sql.=";";
	$quels_eleves = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
    echo "<form enctype=\"multipart/form-data\" action=\"visu_profs_eleve.php\" method=\"post\">\n";
	echo "<table summary='Choix'>\n";
	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<span class='bold'>Choisissez l'élève : </span>";
	echo "</td>\n";
	echo "<td valign='top'>\n";
	echo "<select size=\"".mysqli_num_rows($quels_eleves)."\" name=\"login_eleve\">";
	$cpt=0;
	while ($current_eleve = mysqli_fetch_object($quels_eleves)) {
		echo "<option value=".$current_eleve->login;
		if($cpt==0) {echo " selected='selected'";}
		echo ">" . $current_eleve->prenom . " " . $current_eleve->nom . "</option>\n";
		$cpt++;
	}
	echo "</select>\n";
	echo "</td>\n";
	echo "<td valign='top'>\n";
	echo "<input type='submit' value='Valider' />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
    echo "</form>\n";

} else {
	if($_SESSION['statut'] == "responsable") {
		$sql = "(SELECT e.login, e.nom, e.prenom " .
					"FROM eleves e, responsables2 re, resp_pers r WHERE (" .
					"e.ele_id = re.ele_id AND " .
					"re.pers_id = r.pers_id AND " .
					"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2') AND e.login!='".$login_eleve."'))";
		if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
			$sql.=" UNION (SELECT e.login, e.nom, e.prenom " .
					"FROM eleves e, responsables2 re, resp_pers r WHERE (" .
					"e.ele_id = re.ele_id AND " .
					"re.pers_id = r.pers_id AND " .
					"r.login = '" . $_SESSION['login'] . "' AND re.resp_legal='0' AND re.acces_sp='y' AND e.login!='".$login_eleve."'))";
		}
		$sql.=";";
		$quels_eleves = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		while($lig_autres_eleves=mysqli_fetch_object($quels_eleves)) {
			echo " | <a href='".$_SERVER['PHP_SELF']."?login_eleve=".$lig_autres_eleves->login."'>".casse_mot($lig_autres_eleves->nom,'maj')." ".casse_mot($lig_autres_eleves->prenom,'majf2')."</a>";
		}
	}
	echo "</p>\n";

	// On a un élève. On affiche l'équipe pédagogique !
	$eleve = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT e.nom, e.prenom FROM eleves e WHERE e.login = '".$login_eleve."'");
	$nom_eleve = mysql_result($eleve, 0, "nom");
	$prenom_eleve = mysql_result($eleve, 0, "prenom");
	//$id_classe = mysql_result(mysql_query("SELECT id_classe FROM j_eleves_classes WHERE login = '" . $login_eleve ."' LIMIT 1"), 0);

	//$sql="SELECT DISTINCT jec.id_classe, c.* FROM j_eleves_classes jec, classes c WHERE jec.login='".$login_eleve."' AND jec.id_classe=c.id ORDER BY periode DESC LIMIT 1";
	$sql="SELECT DISTINCT jec.id_classe, c.* FROM j_eleves_classes jec, classes c WHERE jec.login='".$login_eleve."' AND jec.id_classe=c.id ORDER BY periode;";
	$res_class=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res_class)==0) {
		echo "<h3>Equipe pédagogique de l'élève : <strong>".$prenom_eleve ." " . $nom_eleve."</strong>";
		echo "</h3>\n";
		echo "<p>L'élève n'est dans aucune classe???</p>\n";
		require "../lib/footer.inc.php";
		die();
	}

	while($lig_clas=mysqli_fetch_object($res_class)) {
		echo "<h3>Equipe pédagogique de l'élève : <strong>".$prenom_eleve ." " . $nom_eleve."</strong>";

		$id_classe=$lig_clas->id_classe;
		echo " de ".$lig_clas->nom_complet." (<i>".$lig_clas->classe."</i>)";
		/*
		$tmp_classes=get_noms_classes_from_ele_login($login_eleve);
		echo " (<i>";
		for($i=0;$i<count($tmp_classes);$i++) {
			if($i>0) {echo ", ";}
			echo $tmp_classes[$i];
		}
		echo "</i>)";
		*/
		echo "</h3>\n";

		echo "<table border='0' class='boireaus boireaus_alt' summary='Equipe'>
		<tr>
			<th>Matière</th>
			<th>Enseignement/groupe</th>
			<th>Professeur</th>
		</tr>\n";

		// On commence par le CPE
		$sql="SELECT DISTINCT u.nom,u.prenom,u.email,u.show_email,jec.cpe_login " .
					"FROM utilisateurs u,j_eleves_cpe jec " .
					"WHERE jec.e_login='".$login_eleve."' AND " .
					"u.login=jec.cpe_login " .
					"ORDER BY jec.cpe_login;";
		//echo "$sql<br />";
		$req = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($req)>0) {
			// Il ne doit y en avoir qu'un...
			$cpe = mysqli_fetch_object($req);
			echo "<tr valign='top'><td>VIE SCOLAIRE</td>\n";
			echo "<td></td>";
			echo "<td>";
			// On affiche l'email s'il est non nul, si le cpe l'a autorisé, et si l'utilisateur est autorisé par les droits d'accès globaux
			if ($cpe->email!="" AND $cpe->show_email == "yes" AND (
				($_SESSION['statut'] == "responsable" AND
						(getSettingValue("GepiAccesEquipePedaEmailParent") == "yes" OR
						getSettingValue("GepiAccesCpePPEmailParent") == "yes"))
				OR
				($_SESSION['statut'] == "eleve" AND
					(getSettingValue("GepiAccesEquipePedaEmailEleve") == "yes" OR
					getSettingValue("GepiAccesEquipePedaEmailEleve") == "yes")
					)
				)){
				echo "<a href='mailto:".$cpe->email."?".urlencode("subject=".getSettingValue('gepiPrefixeSujetMail')."[GEPI] eleve : ".$prenom_eleve . " ".$nom_eleve)."'>".affiche_utilisateur($cpe->cpe_login,$id_classe)."</a>";
			} else {
				echo affiche_utilisateur($cpe->cpe_login,$id_classe);
			}
			echo "</td></tr>\n";
		}

		// On passe maintenant les groupes un par un, sans se préoccuper de la période : on affiche tous les groupes
		// auxquel l'élève appartient ou a appartenu
		$groupes = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT jeg.id_groupe, m.nom_complet, g.* " .
								"FROM j_eleves_groupes jeg, matieres m, j_groupes_matieres jgm, j_groupes_classes jgc, groupes g WHERE " .
								"g.id=jeg.id_groupe AND ".
								"jeg.login = '".$login_eleve."' AND " .
								"m.matiere = jgm.id_matiere AND " .
								"jgm.id_groupe = jeg.id_groupe AND " .
								"jgc.id_groupe = jeg.id_groupe AND " .
								"jgc.id_classe = '".$id_classe . "' " .
								"ORDER BY jgc.priorite, m.matiere");
		while ($groupe = mysqli_fetch_object($groupes)) {
			// On est dans la boucle 'groupes'. On traite les groupes un par un.

		    // Matière correspondant au groupe:
		    echo "<tr valign='top'><td>".htmlspecialchars($groupe->nom_complet)."</td>\n";

			echo "<td>".$groupe->name." <em style='font-size:small'>(".$groupe->description.")</em>"."</td>";

		    // Professeurs
		    echo "<td>";
		    $sql="SELECT jgp.login,u.nom,u.prenom,u.email,u.show_email FROM j_groupes_professeurs jgp,utilisateurs u WHERE jgp.id_groupe='".$groupe->id_groupe."' AND u.login=jgp.login";
		    $result_prof=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		    while($lig_prof=mysqli_fetch_object($result_prof)){

		        // Le prof est-il PP de l'élève ?
		        $sql="SELECT * FROM j_eleves_professeurs WHERE login = '".$login_eleve."' AND professeur='".$lig_prof->login."'";
		        $res_pp=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

				if($lig_prof->email!="" AND $lig_prof->show_email == "yes" AND
					(($_SESSION['statut'] == "responsable" AND
						(getSettingValue("GepiAccesEquipePedaEmailParent") == "yes"
							OR
						 (getSettingValue("GepiAccesCpePPEmailParent") == "yes" AND mysqli_num_rows($res_pp)>0)
						 )
		    		) OR (
					  $_SESSION['statut'] == "eleve" AND
						(getSettingValue("GepiAccesEquipePedaEmailEleve") == "yes"
							OR
						 (getSettingValue("GepiAccesCpePPEmailEleve") == "yes" AND mysqli_num_rows($res_pp)>0)
						 )
					)
					)){
		            echo "<a href='mailto:$lig_prof->email?".urlencode("subject=".getSettingValue('gepiPrefixeSujetMail')."[GEPI] eleve : ".$prenom_eleve . " " . $nom_eleve)."'>".affiche_utilisateur($lig_prof->login,$id_classe)."</a>";
		        }
		        else{
					echo affiche_utilisateur($lig_prof->login,$id_classe);
		        }


		        if(mysqli_num_rows($res_pp)>0){
		             echo " (<i>".getSettingValue('gepi_prof_suivi')."</i>)";
		        }
		        echo "<br />\n";
		    }
		    echo "</td>\n";
		    echo "</tr>\n";
		}
		// On a fini le traitement.
		echo "</table>\n";
	}
}

require "../lib/footer.inc.php";
?>
