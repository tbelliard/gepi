<?php
/*
*
* Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

if(!getSettingAOui('active_bulletins')) {
	header("Location: ../accueil.php?msg=Module_inactif");
	die();
}

//Initialisation
unset($id_classe);
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : (isset($_GET["id_classe"]) ? $_GET["id_classe"] : NULL);
unset($login_eleve);
$login_eleve = isset($_POST["login_eleve"]) ? $_POST["login_eleve"] : (isset($_GET["login_eleve"]) ? $_GET["login_eleve"] : NULL);

$error_login = false;
// Quelques filtrages de départ pour pré-initialiser la variable qui nous importe ici : $login_eleve
if ($_SESSION['statut'] == "responsable") {
	$sql="(SELECT e.login FROM eleves e, resp_pers r, responsables2 re 
						WHERE (e.ele_id = re.ele_id AND 
							re.pers_id = r.pers_id AND 
							r.login = '".$_SESSION['login']."' AND 
							(re.resp_legal='1' OR re.resp_legal='2')))";
	if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
		$sql.=" UNION (SELECT e.login FROM eleves e, resp_pers r, responsables2 re 
						WHERE (e.ele_id = re.ele_id AND 
							re.pers_id = r.pers_id AND 
							r.login = '".$_SESSION['login']."' AND 
							re.resp_legal='0' AND
							re.acces_sp='y'))";
	}
	$sql.=";";
	//echo "$sql<br />";
	$get_eleves = mysqli_query($GLOBALS["mysqli"], $sql);

	if (mysqli_num_rows($get_eleves) == 1) {
		// Un seul élève associé : on initialise tout de suite la variable $login_eleve
		$login_eleve = old_mysql_result($get_eleves, 0);
		//echo "$login_eleve<br />";
	} elseif (mysqli_num_rows($get_eleves) == 0) {
		$error_login = true;
	}
	// Si le nombre d'élèves associés est supérieur à 1, alors soit $login_eleve a été déjà défini, soit il faut présenter un choix.

} else if ($_SESSION['statut'] == "eleve") {
	if ($login_eleve != null and (mb_strtoupper($login_eleve) != mb_strtoupper($_SESSION['login']))) {
		tentative_intrusion(2, "Tentative d'un ".$gepiSettings['denomination_eleve']." de visualiser le bulletin simplifié d'un autre ".$gepiSettings['denomination_eleve'].".");
	}
	// Si l'utilisateur identifié est un élève, pas le choix, il ne peut consulter que son équipe pédagogique
	$login_eleve = $_SESSION['login'];
}

if ($login_eleve and $login_eleve != null) {
	// On récupère la classe de l'élève, pour déterminer automatiquement le nombre de périodes
	// On part du postulat que même si l'élève change de classe en cours d'année, c'est pour aller
	// dans une classe qui a le même nombre de périodes...
	$id_classe = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT id_classe FROM j_eleves_classes jec WHERE login = '".$login_eleve."' LIMIT 1"), 0);
}

if (isset($id_classe)) {
	// On regarde si le type est correct :
	if (!is_numeric($id_classe)) {
		tentative_intrusion("2", "Changement de la valeur de id_classe pour un type non numérique.");
		echo "Erreur.";
		require ("../lib/footer.inc.php");
		die();
	}
	// On teste si un professeur a le droit d'accéder à cette classe
	//if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {

	//echo "\$_SESSION['statut']=".$_SESSION['statut']."<br />";
	//echo "\getSettingValue(\"GepiAccesBulletinSimpleProfToutesClasses\")=".getSettingValue("GepiAccesBulletinSimpleProfToutesClasses")."<br />";

	if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes") {

		//echo "SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')<br />";

		if ((getSettingValue("GepiAccesBulletinSimpleProf") == "yes")||(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes")) {
			$test = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')"));
			if ($test == "0") {
				tentative_intrusion("2", "Tentative d'accès par un prof à une classe dans laquelle il n'enseigne pas, sans en avoir l'autorisation.");
				echo "Vous ne pouvez pas accéder à cette classe car vous n'y êtes pas professeur !";
				require ("../lib/footer.inc.php");
				die();
			}
		}
		else {
			//$gepi_prof_suivi=getSettingValue("gepi_prof_suivi");
			$gepi_prof_suivi=retourne_denomination_pp($id_classe);
			//echo "\$gepi_prof_suivi=$gepi_prof_suivi<br/>";

			$test = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT 1=1 FROM j_eleves_classes jec, j_eleves_professeurs jep WHERE (jep.professeur='".$_SESSION['login']."' AND jep.login=jec.login AND jec.id_classe = '".$id_classe."')"));
			if ($test == "0") {
				tentative_intrusion("2", "Tentative d'accès par un prof à une classe dans laquelle il n'est pas $gepi_prof_suivi, sans en avoir l'autorisation.");
				echo "Vous ne pouvez pas accéder à cette classe car vous n'y êtes pas $gepi_prof_suivi!";
				require ("../lib/footer.inc.php");
				die();
			}
		}
	}
}


//**************** EN-TETE *******************************
$titre_page = "Edition simplifiée des bulletins";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE ****************************
?>
<script type='text/javascript' language='javascript'>
function active(num) {
document.form_choix_edit.choix_edit[num].checked=true;
}

function change_periode(){
var indi=document.form_choix_edit.periode1.selectedIndex;
document.form_choix_edit.periode2.value=indi+1;
}
</script>
<?php
//echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

// Si on a eu une erreur sur l'association responsable->élève
if ($_SESSION['statut'] == "responsable" and $error_login == true) {
	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

	echo "<p>Il semble que vous ne soyez associé à aucun ".$gepiSettings['denomination_eleve'].". Contactez l'administrateur pour résoudre cette erreur.</p>";
	require "../lib/footer.inc.php";
	die();
}

// Vérifications de sécurité
if (
	($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesBulletinSimpleParent") != "yes") OR
	($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesBulletinSimpleEleve") != "yes")
	) {
	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

	tentative_intrusion(1, "Tentative d'accès aux bulletins simplifiés sans autorisation.");
	echo "<p>Vous n'êtes pas autorisé à visualiser cette page.</p>";
	require "../lib/footer.inc.php";
	die();
}


if (!isset($id_classe) and $_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve") {
	// Le choix de la classe n'est pas encore fait et l'on n'est ni responsable, ni élève

	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

	//$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	//$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");

	if($_SESSION['statut'] == 'scolarite'){
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	//elseif(($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiAccesReleveProf")=='yes')){
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes"){

		// C'est un prof et l'accès "a accès aux bulletins simples des élèves de toutes les classes" n'est pas donné
		//$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";

		if ((getSettingValue("GepiAccesBulletinSimpleProf") == "yes")||(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes")) {
			$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
		}
		elseif(getSettingValue("GepiAccesBulletinSimplePP") == "yes") {
			$sql="SELECT DISTINCT c.* FROM classes c,
											j_eleves_classes jec,
											j_eleves_professeurs jep
									WHERE jec.id_classe=c.id AND
											jep.login=jec.login AND
											jep.professeur='".$_SESSION['login']."'
									ORDER BY c.classe;";
		}
		else {
			tentative_intrusion(1, "Tentative d'accès aux bulletins simplifiés sans autorisation.");
			echo "<p>Vous n'êtes pas autorisé à visualiser cette page.</p>";
			require "../lib/footer.inc.php";
			die();
		}
	}
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") == "yes") {
		// C'est un prof et l'accès "a accès aux bulletins simples des élèves de toutes les classes" est donné
		$sql="SELECT DISTINCT c.* FROM classes c ORDER BY c.classe";
	}
	//elseif(($_SESSION['statut'] == 'cpe')&&(getSettingValue("GepiAccesReleveCpe")=='yes')){
	elseif($_SESSION['statut'] == 'cpe' OR $_SESSION['statut'] == 'autre'){
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe";
	}
	//echo "$sql<br />\n";
	$calldata = mysqli_query($GLOBALS["mysqli"], $sql);
	$nombreligne = mysqli_num_rows($calldata);
	echo " | Total : $nombreligne classes </p>\n";

	if($nombreligne==0){
		echo "<p>Aucune classe ne vous est attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else{
		echo "<p>Cliquez sur la classe pour laquelle vous souhaitez extraire les bulletins</p>\n";
		//echo "<table border=0>\n";
		$nb_class_par_colonne=round($nombreligne/3);
			//echo "<table width='100%' border='1'>\n";
			echo "<table width='100%' summary='Choix de la classe'>\n";
			echo "<tr valign='top' align='center'>\n";
			echo "<td align='left'>\n";
		$i = 0;
		while ($i < $nombreligne){
			$id_classe = old_mysql_result($calldata, $i, "id");
			$classe_liste = old_mysql_result($calldata, $i, "classe");
			//echo "<tr><td><a href='index3.php?id_classe=$id_classe'>$classe_liste</a></td></tr>\n";
			if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
				echo "</td>\n";
				//echo "<td style='padding: 0 10px 0 10px'>\n";
				echo "<td align='left'>\n";
			}
			echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>$classe_liste</a><br />\n";
			$i++;
		}
		echo "</table>\n";
	}
} else if ($_SESSION['statut'] == "responsable" AND $login_eleve == null) {
	// Si on est là, c'est que le responsable est responsable de plusieurs élèves. Il doit donc
	// choisir celui pour lequel il souhaite visualiser le bulletin simplifié

	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

	$sql="(SELECT e.login, e.nom, e.prenom " .
				"FROM eleves e, responsables2 re, resp_pers r WHERE (" .
				"e.ele_id = re.ele_id AND " .
				"re.pers_id = r.pers_id AND " .
				"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2')))";
	if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
		$sql.=" UNION (SELECT e.login, e.nom, e.prenom FROM eleves e, resp_pers r, responsables2 re 
						WHERE (e.ele_id = re.ele_id AND 
							re.pers_id = r.pers_id AND 
							r.login = '".$_SESSION['login']."' AND 
							re.resp_legal='0' AND
							re.acces_sp='y'))";
	}
	$sql.=";";
	$quels_eleves = mysqli_query($GLOBALS["mysqli"], $sql);


	echo "<p>Cliquez sur le nom d'un ".$gepiSettings['denomination_eleve']." pour visualiser son bulletin simplifié :</p>";
	while ($current_eleve = mysqli_fetch_object($quels_eleves)) {
		echo "<p><a href='".$_SERVER['PHP_SELF']."?login_eleve=".$current_eleve->login."'>".$current_eleve->prenom." ".$current_eleve->nom."</a></p>";
	}
} else if (!isset($choix_edit)) {
	// ====================
	// Je ne saisis pas bien comment $choix_edit peut être affecté sans register_globals=on
	// Nulle part la variable n'a l'air récupérée en POST ou autre...
	// ====================

	if ($_SESSION['statut'] != "responsable" and $_SESSION['statut'] != "eleve") {
		//echo " | <a href = \"index3.php\">Choisir une autre classe</a> ";

		// =================================
		// Formulaire de choix de la classe précédente/suivante
		echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

		echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

		// Ajout lien classe précédente / classe suivante
		if($_SESSION['statut']=='scolarite'){
			$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
		}
		elseif($_SESSION['statut']=='professeur'){
			if ((getSettingValue("GepiAccesBulletinSimpleProf") == "yes")||(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes")) {
				$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
			}
			elseif(getSettingValue("GepiAccesBulletinSimplePP") == "yes") {
				$sql="SELECT DISTINCT c.id,c.classe FROM classes c,
												j_eleves_classes jec,
												j_eleves_professeurs jep
										WHERE jec.id_classe=c.id AND
												jep.login=jec.login AND
												jep.professeur='".$_SESSION['login']."'
										ORDER BY c.classe;";
			}
			else {
				tentative_intrusion(1, "Tentative d'accès aux bulletins simplifiés sans autorisation.");
				echo "<p>Vous n'êtes pas autorisé à visualiser cette page.</p>";
				require "../lib/footer.inc.php";
				die();
			}

		}
		elseif($_SESSION['statut']=='cpe'){
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
				p.id_classe = c.id AND
				jec.id_classe=c.id AND
				jec.periode=p.num_periode AND
				jecpe.e_login=jec.login AND
				jecpe.cpe_login='".$_SESSION['login']."'
				ORDER BY classe";
		}
		elseif($_SESSION['statut'] == 'autre'){

			// On recherche toutes les classes pour ce statut qui n'est accessible que si l'admin a donné les bons droits
			$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe";

		}

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
		if(isset($id_class_prec)){
			if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec'>Classe précédente</a>";}
		}

		if($chaine_options_classes!="") {
			echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
			echo $chaine_options_classes;
			echo "</select>\n";
		}

		if(isset($id_class_suiv)){
			if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv'>Classe suivante</a></p>";}
		}
		//fin ajout lien classe précédente / classe suivante

		echo "</form>\n";
		// =================================


		$gepi_prof_suivi=retourne_denomination_pp($id_classe);

		$classe_eleve = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes WHERE id='$id_classe'");
		$nom_classe = old_mysql_result($classe_eleve, 0, "classe");
		echo "<p class='grand'>Classe de $nom_classe</p>\n";
		echo "<p>Afficher&nbsp;:</p>\n";
		echo "<form enctype=\"multipart/form-data\" action=\"edit_limite.php\" method=\"post\" name=\"form_choix_edit\" target=\"_blank\">\n";
		echo "<table summary='Choix des élèves'>\n";
		echo "<tr>\n";
		echo "<td><input type=\"radio\" name=\"choix_edit\" id='choix_edit_1' value=\"1\" onchange=\"change_style_radio()\" ";
		if((!isset($_SESSION['choix_edit']))||($_SESSION['choix_edit']==1)) {
			echo "checked ";
		}
		echo "/></td>\n";
		echo "<td><label for='choix_edit_1' id='texte_choix_edit_1' style='cursor: pointer;'>Les bulletins simplifiés de tous les ".$gepiSettings['denomination_eleves']." de la classe";
		if((getSettingAOui('GepiAccesPPTousElevesDeLaClasse'))&&(is_pp($_SESSION['login'], $id_classe))) {
			// Tous les élèves vont être affichés
		}
		elseif ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesBulletinSimpleProfTousEleves") != "yes" AND getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes") {
			echo " (<em>uniquement les ".$gepiSettings['denomination_eleves']." que j'ai en cours ou dont je suis ".$gepi_prof_suivi."</em>)";
		}
		echo "</label></td></tr>\n";

		$call_suivi = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT professeur FROM j_eleves_professeurs WHERE id_classe='$id_classe' ORDER BY professeur");
		$nb_lignes = mysqli_num_rows($call_suivi);
		$indice = 1;
		if ($nb_lignes > 1) {
			echo "<tr>\n";
			echo "<td><input type=\"radio\" name=\"choix_edit\" id='choix_edit_3' value=\"3\" onchange=\"change_style_radio()\" ";
			if((isset($_SESSION['choix_edit']))&&($_SESSION['choix_edit']==3)) {
				echo "checked ";
			}
			echo "/></td>\n";
			echo "<td><label for='choix_edit_3' id='texte_choix_edit_3' style='cursor: pointer;'>Uniquement les bulletins simplifiés des ".$gepiSettings['denomination_eleves']." dont le ".$gepi_prof_suivi." est :</label>\n";
			echo "<select size=\"1\" name=\"login_prof\" onclick=\"active(1)\">\n";
			$i=0;
			while ($i < $nb_lignes) {
				$login_pr = old_mysql_result($call_suivi,$i,"professeur");
				$call_prof = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM utilisateurs WHERE login='$login_pr'");
				$nom_prof = old_mysql_result($call_prof,0,"nom");
				$prenom_prof = old_mysql_result($call_prof,0,"prenom");
				echo "<option value=".$login_pr."";
				if((isset($_SESSION['login_prof']))&&($_SESSION['login_prof']==$login_pr)) {
					echo " selected='true'";
				}
				echo ">".$nom_prof." ".$prenom_prof."</option>\n";
				$i++;
			}
			echo "</select></td></tr>\n";
			$indice = 2;
		}


		echo "<tr>\n";
		echo "<td><input type=\"radio\" id='choix_edit_2' name=\"choix_edit\" value=\"2\" onchange=\"change_style_radio()\" ";
		if((isset($_SESSION['choix_edit']))&&($_SESSION['choix_edit']==2)) {
			echo "checked ";
		}
		echo "/></td>\n";
		echo "<td><label for='choix_edit_2' id='texte_choix_edit_2' style='cursor: pointer;'>Uniquement le bulletin simplifié de l'".$gepiSettings['denomination_eleve']." sélectionné ci-contre : </label>\n";
		echo "<select size=\"1\" name=\"login_eleve\" onclick=\"active(".$indice.")\">\n";

		//if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
		if((getSettingAOui('GepiAccesPPTousElevesDeLaClasse'))&&(is_pp($_SESSION['login'], $id_classe))) {
			// Tous les élèves vont être affichés
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe = '$id_classe' and j.login=e.login) order by nom, prenom";
		}
		elseif ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesBulletinSimpleProfTousEleves") != "yes" AND getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes") {
			/*
			$sql="SELECT DISTINCT e.* " .
				"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
				"WHERE (" .
				"jec.id_classe='$id_classe' AND " .
				"e.login = jeg.login AND " .
				"jeg.login = jec.login AND " .
				"jeg.id_groupe = jgp.id_groupe AND " .
				"jgp.login = '".$_SESSION['login']."') " .
				"ORDER BY e.nom,e.prenom";
			*/
			if(is_pp($_SESSION['login'], $id_classe)) {
				if(getSettingAOui('GepiAccesBulletinSimpleProf')) {
					$sql="(SELECT DISTINCT e.* " .
						"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
						"WHERE (" .
						"jec.id_classe='$id_classe' AND " .
						"e.login = jeg.login AND " .
						"jeg.login = jec.login AND " .
						"jeg.id_groupe = jgp.id_groupe AND " .
						"jgp.login = '".$_SESSION['login']."')) " .
						"UNION (SELECT DISTINCT e.* " .
						"FROM eleves e, j_eleves_classes jec, j_eleves_professeurs jep " .
						"WHERE (" .
						"jec.id_classe='$id_classe' AND " .
						"e.login = jep.login AND " .
						"jep.login = jec.login AND " .
						"jep.professeur = '".$_SESSION['login']."')) ORDER BY nom,prenom;";
					$appel_liste_eleves = mysqli_query($GLOBALS["mysqli"], $sql);
				}
				else {
					$sql="SELECT DISTINCT e.* " .
						"FROM eleves e, j_eleves_classes jec, j_eleves_professeurs jep " .
						"WHERE (" .
						"jec.id_classe='$id_classe' AND " .
						"e.login = jep.login AND " .
						"jep.login = jec.login AND " .
						"jep.professeur = '".$_SESSION['login']."') ORDER BY e.nom,e.prenom;";
					$appel_liste_eleves = mysqli_query($GLOBALS["mysqli"], $sql);
				}
			}
			else {
			    $sql="SELECT DISTINCT e.* " .
					"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
					"WHERE (" .
					"jec.id_classe='$id_classe' AND " .
					"e.login = jeg.login AND " .
					"jeg.login = jec.login AND " .
					"jeg.id_groupe = jgp.id_groupe AND " .
					"jgp.login = '".$_SESSION['login']."') " .
					"ORDER BY e.nom,e.prenom";
				$appel_liste_eleves = mysqli_query($GLOBALS["mysqli"], $sql);
			}

		} else {
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe = '$id_classe' and j.login=e.login) order by nom, prenom";
		}
		//echo "$sql<br />\n";
		$call_eleve = mysqli_query($GLOBALS["mysqli"], $sql);
		$nombreligne = mysqli_num_rows($call_eleve);
		$i = "0" ;
		while ($i < $nombreligne) {
			$eleve = old_mysql_result($call_eleve, $i, 'login');
			$nom_el = old_mysql_result($call_eleve, $i, 'nom');
			$prenom_el = old_mysql_result($call_eleve, $i, 'prenom');
			echo "<option value=$eleve>$nom_el  $prenom_el </option>\n";
			$i++;
		}
		echo "</select></td></tr>\n";

		echo "<tr>\n";
		echo "<td><input type=\"radio\" name=\"choix_edit\" id='choix_edit_4' value=\"4\" onchange=\"change_style_radio()\" ";
		if((isset($_SESSION['choix_edit']))&&($_SESSION['choix_edit']==4)) {
			echo "checked ";
		}
		echo "/></td>\n";
		echo "<td><label for='choix_edit_4' id='texte_choix_edit_4' style='cursor: pointer;'>Le bulletin simplifié des appréciations sur le groupe-classe";
		echo "</label></td></tr>\n";

		echo "</table>\n";

	} else {
		// Accès parent ou élève
		echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

		if($_SESSION['statut']=='responsable') {
			$quels_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT e.login, e.nom, e.prenom " .
						"FROM eleves e, responsables2 re, resp_pers r WHERE (" .
						"e.ele_id = re.ele_id AND " .
						"re.pers_id = r.pers_id AND " .
						"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2') AND e.login!='$login_eleve')");
			while ($current_eleve = mysqli_fetch_object($quels_eleves)) {
				echo " | <a href='".$_SERVER['PHP_SELF']."?login_eleve=".$current_eleve->login."' title=\"Accéder au bulletin simplifié de ".$current_eleve->prenom." ".$current_eleve->nom."\">".$current_eleve->prenom." ".$current_eleve->nom."</a>";
			}
		}
		echo "</p>\n";

		$eleve = mysqli_query($GLOBALS["mysqli"], "SELECT e.nom, e.prenom FROM eleves e WHERE e.login = '".$login_eleve."'");
		$prenom_eleve = old_mysql_result($eleve, 0, "prenom");
		$nom_eleve = old_mysql_result($eleve, 0, "nom");

		echo "<p class='grand'>".casse_mot($gepiSettings['denomination_eleve'],'majf')." : ".$prenom_eleve." ".$nom_eleve."</p>\n";
		echo "<form enctype=\"multipart/form-data\" action=\"edit_limite.php\" method=\"post\" name=\"form_choix_edit\" target=\"_blank\">\n";

		echo "<input type=\"hidden\" name=\"login_eleve\" value=\"".$login_eleve."\" />\n";

		// 20121101: Si le droit est donné, permettre d'accéder au bulletin de la classe
		if((($_SESSION['statut']=='responsable')&&(getSettingAOui('GepiAccesBulletinSimpleClasseResp')))||
			(($_SESSION['statut']=='eleve')&&(getSettingAOui('GepiAccesBulletinSimpleClasseEleve')))) {
			echo "<p>Afficher<br />\n";
			echo "<input type=\"radio\" name=\"choix_edit\" id=\"choix_edit_2\" value=\"2\" onchange=\"change_style_radio()\" checked /><label for='choix_edit_2'>Le bulletin simplifié de ".$prenom_eleve." ".$nom_eleve."</label><br />\n";
			echo "<input type=\"radio\" name=\"choix_edit\" id=\"choix_edit_4\" value=\"4\" onchange=\"change_style_radio()\" /><label for='choix_edit_4'>Le bulletin simplifié des appréciations sur le groupe-classe</label><br /><br />\n";
		}
		else {
			echo "<input type=\"hidden\" name=\"choix_edit\" value=\"2\" />\n";
		}
	}
	echo "<p>Choisissez la(les) période(s) : </p>\n";
	include "../lib/periodes.inc.php";

	$periode1_par_defaut=1;
	if(isset($_SESSION['periode1'])) {
		$periode1_par_defaut=$_SESSION['periode1'];
	}

	echo "De la période : <select onchange=\"change_periode()\" size=1 name=\"periode1\">\n";
	$i = "1" ;
	while ($i < $nb_periode) {
	echo "<option value='$i'";
	if($i==$periode1_par_defaut) {echo " selected='true'";}
	echo ">$nom_periode[$i] </option>\n";
	$i++;
	}
	echo "</select>\n";


	if(isset($_SESSION['periode2'])) {
		$max_per=$_SESSION['periode2'];
	}
	else {
		$max_per=1;
		//$sql="SELECT max(num_periode) AS max_per FROM periodes WHERE id_classe='$id_classe' AND verouiller='N';";
		// Bizarre: Si tout est clos, on obtient
		/*
			mysql> SELECT max(num_periode) AS max_per FROM periodes WHERE id_classe='3' AND verouiller='N';
			+---------+
			| max_per |
			+---------+
			|    NULL |
			+---------+
			1 row in set (0.00 sec)
			
			mysql> 
		*/
		$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' AND verouiller='N' ORDER BY num_periode DESC LIMIT 1;";
		//echo "$sql<br />";
		$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_per)) {
			$lig_per=mysqli_fetch_object($res_per);
			//$max_per=$lig_per->max_per;
			$max_per=$lig_per->num_periode;
		}
		else {
			// La solution ci-dessous n'est pas fiable: si un groupe est à cheval sur plusieurs classes et que les périodes ouvertes sur les déifférentes classes ne sont pas les mêmes
			$sql="SELECT max(periode) AS max_per FROM matieres_notes mn, j_groupes_classes jgc WHERE mn.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe';";
			//echo "$sql<br />";
			$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_per)) {
				$lig_per=mysqli_fetch_object($res_per);
				$max_per=$lig_per->max_per;
			}
		}
	}
	echo "&nbsp;à la période : <select size='1' name=\"periode2\">\n";
	$i = "1" ;
	while ($i < $nb_periode) {
	echo "<option value='$i'";
	if($i==$max_per) {echo " selected='true'";}
	echo ">$nom_periode[$i] </option>\n";
	$i++;
	}
	echo "</select>\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";

	echo "<br />\n";
	echo "<br />\n";

	// 20141127
	// Largeurs de colonnes:
	// Valeurs par défaut
	$bull_simp_larg_tab_defaut = 680;
	$bull_simp_larg_col1_defaut = 120;
	$bull_simp_larg_col2_defaut = 38;
	$bull_simp_larg_col3_defaut = 38;
	$bull_simp_larg_col4_defaut = 20;

	$bull_simp_larg_tab = $bull_simp_larg_tab_defaut;
	$bull_simp_larg_col1 = $bull_simp_larg_col1_defaut;
	$bull_simp_larg_col2 = $bull_simp_larg_col2_defaut;
	$bull_simp_larg_col3 = $bull_simp_larg_col3_defaut;
	$bull_simp_larg_col4 = $bull_simp_larg_col4_defaut;

	$pref_bull_simp_larg_tab=getPref($_SESSION['login'], 'bull_simp_larg_tab', $bull_simp_larg_tab);
	$pref_bull_simp_larg_col1=getPref($_SESSION['login'], 'bull_simp_larg_col1', $bull_simp_larg_col1);
	$pref_bull_simp_larg_col2=getPref($_SESSION['login'], 'bull_simp_larg_col2', $bull_simp_larg_col2);
	$pref_bull_simp_larg_col3=getPref($_SESSION['login'], 'bull_simp_larg_col3', $bull_simp_larg_col3);
	$pref_bull_simp_larg_col4=getPref($_SESSION['login'], 'bull_simp_larg_col4', $bull_simp_larg_col4);
	if(preg_match("/^[0-9]{1,}$/", $pref_bull_simp_larg_tab)) {
		$bull_simp_larg_tab=$pref_bull_simp_larg_tab;
	}
	if(preg_match("/^[0-9]{1,}$/", $pref_bull_simp_larg_col1)) {
		$bull_simp_larg_col1=$pref_bull_simp_larg_col1;
	}
	if(preg_match("/^[0-9]{1,}$/", $pref_bull_simp_larg_col2)) {
		$bull_simp_larg_col2=$pref_bull_simp_larg_col2;
	}
	if(preg_match("/^[0-9]{1,}$/", $pref_bull_simp_larg_col3)) {
		$bull_simp_larg_col3=$pref_bull_simp_larg_col3;
	}
	if(preg_match("/^[0-9]{1,}$/", $pref_bull_simp_larg_col4)) {
		$bull_simp_larg_col4=$pref_bull_simp_larg_col4;
	}
	if($bull_simp_larg_tab<$bull_simp_larg_col1+$bull_simp_larg_col2+$bull_simp_larg_col3+$bull_simp_larg_col4) {
		$bull_simp_larg_tab = $bull_simp_larg_tab_defaut;
		$bull_simp_larg_col1 = $bull_simp_larg_col1_defaut;
		$bull_simp_larg_col2 = $bull_simp_larg_col2_defaut;
		$bull_simp_larg_col3 = $bull_simp_larg_col3_defaut;
		$bull_simp_larg_col4 = $bull_simp_larg_col4_defaut;
	}

	$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
	$colspan=4;
	if($affiche_rang!="y") {
		$colspan--;
	}
	echo "<p>Largeur des colonnes&nbsp;:</p>
<table class='boireaus boireaus_alt'>
	<tr>
		<th rowspan='2'>Largeur totale du tableau</th>
		<th colspan='4'>
			Largeur des colonnes
			 <a href=\"#\" onclick=\"document.getElementById('bull_simp_larg_tab').value=$bull_simp_larg_tab_defaut;
							document.getElementById('bull_simp_larg_col1').value=$bull_simp_larg_col1_defaut;
							document.getElementById('bull_simp_larg_col2').value=$bull_simp_larg_col2_defaut;
							document.getElementById('bull_simp_larg_col3').value=$bull_simp_larg_col3_defaut;
							document.getElementById('bull_simp_larg_col4').value=$bull_simp_larg_col4_defaut;
							return false;\" title=\"Reprendre les valeurs par défaut.\"><img src='../images/icons/wizard.png' class='icone16' alt='Valeurs par défaut' /></a>
		</th>
	</tr>
	<tr>
		<th>Enseignements</th>
		<th>Coefficients</th>
		<th>Moyennes (classe et élève)</th>".(($affiche_rang=='y') ? "
		<th>Rang de l'élève</th>" : "")."
	</tr>
	<tr>
		<td><input type='text' name='bull_simp_larg_tab' id='bull_simp_larg_tab' value='$bull_simp_larg_tab' size='3' onKeyDown=\"clavier_2(this.id, event, 10,3000);\" AutoComplete=\"off\" /></td>
		<td><input type='text' name='bull_simp_larg_col1' id='bull_simp_larg_col1' value='$bull_simp_larg_col1' size='3' onKeyDown=\"clavier_2(this.id, event, 10,3000);\" AutoComplete=\"off\" /></td>
		<td><input type='text' name='bull_simp_larg_col2' id='bull_simp_larg_col2' value='$bull_simp_larg_col2' size='3' onKeyDown=\"clavier_2(this.id, event, 10,3000);\" AutoComplete=\"off\" /></td>
		<td><input type='text' name='bull_simp_larg_col3' id='bull_simp_larg_col3' value='$bull_simp_larg_col3' size='3' onKeyDown=\"clavier_2(this.id, event, 10,3000);\" AutoComplete=\"off\" /></td>".(($affiche_rang=='y') ? "
		<td><input type='text' name='bull_simp_larg_col4' id='bull_simp_larg_col4' value='$bull_simp_larg_col4' size='3' onKeyDown=\"clavier_2(this.id, event, 10,3000);\" AutoComplete=\"off\" /></td>" : "")."
	</tr>
</table>";

	echo "<label for='bull_simp_pref_marges' style='cursor:pointer;'>\n";
	echo "Ajouter une marge&nbsp;: \n";
	echo "</label>\n";
	$bull_simp_pref_marges=getPref($_SESSION['login'],'bull_simp_pref_marges','');
	echo "<input type=\"text\" size=\"2\" name=\"bull_simp_pref_marges\" id=\"bull_simp_pref_marges\" ";
	echo "value='";
	if(isset($_SESSION['bull_simp_pref_marges'])) {
		$bull_simp_pref_marges=preg_replace('/[^0-9]/','',$_SESSION['bull_simp_pref_marges']);
		// Pour permettre de ne pas inserer de margin et memoriser ce choix, on accepte le champ vide:
		echo $bull_simp_pref_marges;
	}
	elseif($bull_simp_pref_marges!='') {
		echo $bull_simp_pref_marges;
	}
	echo "' ";
	echo "onkeydown=\"clavier_2(this.id,event,0,100);\" autocomplete=\"off\" ";
	echo " />px\n";

	echo "<br />\n";

	$checked_couleurs_alt="";
	$style_couleurs_alt="";
	if(isset($_SESSION['bull_simp_pref_couleur_alterne'])) {
		if($_SESSION['bull_simp_pref_couleur_alterne']=='y') {
			$checked_couleurs_alt=" checked";
			$style_couleurs_alt="font-weight:bold;";
		}
	}
	else {
		$couleur_alterne=getPref($_SESSION['login'], 'bull_simp_pref_couleur_alterne', 'n');
		if($couleur_alterne=='y') {
			$checked_couleurs_alt=" checked";
			$style_couleurs_alt="font-weight:bold;";
		}
	}

	echo "<label for='couleur_alterne' id='texte_couleur_alterne' style='cursor:pointer;$style_couleurs_alt'>\n";
	echo "Couleurs de fond des lignes alternées&nbsp;: \n";
	echo "</label>\n";
	echo "<input type=\"checkbox\" name=\"couleur_alterne\" id=\"couleur_alterne\" value='y' onchange=\"changement();checkbox_change(this.id);\"".$checked_couleurs_alt;
	echo " />\n";

	if(($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
		echo "<br />\n";
		echo "<label for='pas_de_colonne_moy_classe' id='texte_pas_de_colonne_moy_classe' style='cursor:pointer;'>\n";
		echo "Ne pas afficher la colonne Moyenne de la classe&nbsp;: \n";
		echo "</label>\n";
		echo "<input type=\"checkbox\" name=\"pas_de_colonne_moy_classe\" id=\"pas_de_colonne_moy_classe\" value='y' onchange=\"changement();checkbox_change(this.id);\"";
		echo " />\n";
	}

	if(($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
		echo "<br />\n";
		echo "<label for='pas_de_moy_gen' id='texte_pas_de_moy_gen' style='cursor:pointer;";
		if(!getSettingAOui('bullNoMoyGenParDefaut')) {
			echo "font-weight:bold;";
		}
		echo "'>\n";
		echo "Ne pas afficher la ligne Moyenne générale&nbsp;: \n";
		echo "</label>\n";
		echo "<input type=\"checkbox\" name=\"pas_de_moy_gen\" id=\"pas_de_moy_gen\" value='y' onchange=\"changement();checkbox_change(this.id);\" ";
		if(!getSettingAOui('bullNoMoyGenParDefaut')) {
			echo "checked ";
		}
		echo " />\n";

		echo "<br />\n";
		echo "<label for='pas_de_moy_cat' id='texte_pas_de_moy_cat' style='cursor:pointer;";
		if(!getSettingAOui('bullNoMoyCatParDefaut')) {
			echo "font-weight:bold;";
		}
		echo "'>\n";
		echo "Ne pas afficher les moyennes de catégories&nbsp;: \n";
		echo "</label>\n";
		echo "<input type=\"checkbox\" name=\"pas_de_moy_cat\" id=\"pas_de_moy_cat\" value='y' onchange=\"changement();checkbox_change(this.id);\" ";
		if(!getSettingAOui('bullNoMoyCatParDefaut')) {
			echo "checked ";
		}
		echo " />\n";
	}

	echo "<br /><br /><center><input type=submit value=Valider /></center>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	".js_change_style_radio("change_style_radio", "n", "n")."
	".js_checkbox_change_style('checkbox_change', 'texte_', "n")."
	change_style_radio();
</script>";

	//=================================
	// 20121118
	if(($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')&&
	((getSettingAOui('GepiAccesBulletinSimpleParent'))||
	(getSettingAOui('GepiAccesGraphParent'))||
	(getSettingAOui('GepiAccesBulletinSimpleEleve'))||
	(getSettingAOui('GepiAccesGraphEleve')))) {
		echo "<p><em>Note&nbsp;:</em> ";

		$date_du_jour=strftime("%d/%m/%Y");
		// Si les parents ont accès aux bulletins ou graphes,... on va afficher un témoin
		$tab_acces_app_classe=array();
		// L'accès est donné à la même date pour parents et responsables.
		// On teste seulement pour les parents
		$date_ouverture_acces_app_classe=array();
		$tab_acces_app_classe[$id_classe]=acces_appreciations(1, $nb_periode-1, $id_classe, 'responsable');

		$acces_app_ele_resp=getSettingValue('acces_app_ele_resp');
		if($acces_app_ele_resp=='manuel') {
			$msg_acces_app_ele_resp="Les appréciations seront visibles après une intervention manuelle d'un compte de statut 'scolarité'.";
		}
		elseif($acces_app_ele_resp=='manuel_individuel') {
			$msg_acces_app_ele_resp="Les appréciations seront visibles après une intervention manuelle d'un compte de statut 'scolarité'.";
		}
		elseif($acces_app_ele_resp=='date') {
			$chaine_date_ouverture_acces_app_classe="";
			for($loop=0;$loop<count($date_ouverture_acces_app_classe);$loop++) {
				if($loop>0) {
					$chaine_date_ouverture_acces_app_classe.=", ";
				}
				$chaine_date_ouverture_acces_app_classe.=$date_ouverture_acces_app_classe[$loop];
			}
			if($chaine_date_ouverture_acces_app_classe=="") {$chaine_date_ouverture_acces_app_classe="Aucune date n'est encore précisée.
		Peut-être devriez-vous en poser la question à l'administration de l'établissement.";}
			$msg_acces_app_ele_resp="Les appréciations seront visibles soit à une date donnée (".$chaine_date_ouverture_acces_app_classe.").";
		}
		elseif($acces_app_ele_resp=='periode_close') {
			$delais_apres_cloture=getSettingValue('delais_apres_cloture');
			$msg_acces_app_ele_resp="Les appréciations seront visibles ".$delais_apres_cloture." jour(s) après la clôture de la période.";
		}
		else{
			$msg_acces_app_ele_resp="???";
		}

		/*
		echo "<pre>";
		print_r($tab_acces_app_classe);
		echo "</pre>";
		*/

		echo "A la date du jour (".$date_du_jour.")&nbsp;:</p>\n";
		echo "<ul>\n";
		foreach($tab_acces_app_classe[$id_classe] as $periode_num => $value) {
			echo "<li> les appréciations de la période ".$periode_num." ";
			if($value=="y") {
				echo "sont visibles des parents/élèves.";
			}
			elseif($value=="n") {
				echo "ne sont pas encore visibles des parents/élèves.<br />";
				echo $msg_acces_app_ele_resp;
			}
			else {
				echo "ne sont visibles que pour ".$value." élèves.<br />";
				echo $msg_acces_app_ele_resp;
			}
			echo "</li>\n";
		}
		echo "</ul>\n";
	}
	//=================================

}
require("../lib/footer.inc.php");
?>
