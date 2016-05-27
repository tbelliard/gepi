<?php

/**
 *
 * Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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


/**
 * Gestion des cahiers de textes
 * 
 * @param $_POST['activer'] activation/désactivation
 * @param $_POST['export_cn_ods'] autorisation de l'export au format OD
 * @param $_POST['referentiel_note'] referentiel de note
 * @param $_POST['note_autre_que_sur_referentiel'] note autre que sur referentiel
 * @param $_POST['is_posted']
 *
 */

$accessibilite="y";
$titre_page = "Emploi du temps";
$niveau_arbo = 1;
$gepiPathJava="./..";
$msg = '';
$post_reussi=FALSE;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

// ====== CSS particulier à l'EdT ================
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";
// ====== Entête de Gepi =========================
// ===============================================

// ++++++++++ Initialisation des variables +++++++
$autorise_acces_admin = isset($_POST["activ_ad"]) ? $_POST["activ_ad"] : NULL;
$autorise_acces_eleve = isset($_POST["activ_ele"]) ? $_POST["activ_ele"] : NULL;
$autorise_acces_tous  = isset($_POST["activ_tous"]) ? $_POST["activ_tous"] : NULL;
$autorise_saisir_prof = isset($_POST["autorise_saisir_prof"]) ? $_POST["autorise_saisir_prof"] : NULL;
$affiche_vacances_eleresp = isset($_POST["affiche_vacances_eleresp"]) ? $_POST["affiche_vacances_eleresp"] : NULL;
$affiche_vacances_prof = isset($_POST["affiche_vacances_prof"]) ? $_POST["affiche_vacances_prof"] : NULL;
$modif_setting = "";
$message = "";

	// Modification du setting autorise_edt_tous
	if (isset($autorise_acces_tous)) {
		check_token();
		$requete = "UPDATE setting SET value = '".$autorise_acces_tous."' WHERE name = 'autorise_edt_tous'";
		$modif_setting = "ok";
	}

	// Modification du setting autorise_edt_admin
	if (isset($autorise_acces_admin)) {
		check_token();
		$requete = "UPDATE setting SET value = '".$autorise_acces_admin."' WHERE name = 'autorise_edt_admin'";
		$modif_setting = "ok";
	}

	// Modification du setting autorise_edt_eleve
	if (isset($autorise_acces_eleve)) {
		check_token();
		$requete = "UPDATE setting SET value = '".$autorise_acces_eleve."' WHERE name = 'autorise_edt_eleve'";
		$modif_setting = "ok";
	}

	// On effectue la requête (un seul des trois formulaires précédents a pu être posté à la fois)
	if ($modif_setting == "ok") {
		check_token();
		$modif = mysqli_query($GLOBALS["mysqli"], $requete) OR DIE('La modification n\'a pas été enregistrée : '.mysqli_error($GLOBALS["mysqli"]));
		//$message .= "<p class=\"red\">La modification a bien été enregistrée !</p>";
		$msg .= "La modification a bien été enregistrée !";
		$post_reussi=TRUE;
	}

	// L'autorisation pour les professeurs de saisir leur edt
	if (isset ($autorise_saisir_prof)){
		check_token();
		if (saveSetting("edt_remplir_prof", $autorise_saisir_prof)){
			$message .= "<p class=\"red\">La modification a bien été enregistrée !</p>";;
			$msg .= " La modification a bien été enregistrée !";
		}
	}


	// Modification du setting affiche_vacances_eleresp
	if (isset($affiche_vacances_eleresp)) {
		check_token();
		if (saveSetting("affiche_vacances_eleresp", $affiche_vacances_eleresp)){
			$message .= "<p class=\"red\">La modification de 'affiche_vacances_eleresp' a bien été enregistrée !</p>";;
			$msg .= " La modification de 'affiche_vacances_eleresp' a bien été enregistrée !";
		}
	}

	// Modification du setting affiche_vacances_prof
	if (isset($affiche_vacances_prof)) {
		check_token();
		if (saveSetting("affiche_vacances_prof", $affiche_vacances_prof)){
			$message .= "<p class=\"red\">La modification de 'affiche_vacances_prof' a bien été enregistrée !</p>";;
			$msg .= " La modification de 'affiche_vacances_prof' a bien été enregistrée !";
		}
	}

	// Petite fonction pour déterminer le checked="checked"
	function eval_checked($Settings, $yn){
		$aff_check = '';
		$sql="SELECT value FROM setting WHERE name = '".$Settings."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql) OR DIE ('Erreur requête eval_setting () : '.mysqli_error($GLOBALS["mysqli"]));
		if(mysqli_num_rows($res)>0) {
			$req_setting=mysqli_fetch_array($res);
			if ($req_setting["value"] == $yn) {
				$aff_check = ' checked="checked"';
			}
		}
		return $aff_check;
	}



/****************************************************************
                     HAUT DE PAGE
****************************************************************/

// ====== Inclusion des balises head et du bandeau =====
include_once("../lib/header_template.inc.php");

/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la création du fil d'ariane";
/****************************************************************/

/*
?>
	<div>

<p class=bold>
	<a href="../accueil_modules.php">
		<img src="../images/icons/back.png" alt="Retour" class="back_link" /> Retour
	</a>
</p>
<?php echo $message; ?>
<h2>Gestion des acc&egrave;s &agrave; l'emploi du temps</h2> (Tous les comptes sauf &eacute;l&egrave;ve et responsable)
<hr />
	<form action="edt.php" method="post" name="autorise_edt">

<i>La d&eacute;sactivation des emplois du temps n'entra&icirc;ne aucune suppression des donn&eacute;es.
 Lorsque le module est d&eacute;sactiv&eacute;, personne n'a acc&egrave;s au module et
 la consultation des emplois du temps est impossible.</i><br />
<p>
		<input name="activ_tous" id="activTous" value="y" type="radio"<?php echo eval_checked("autorise_edt_tous", "y"); ?> onclick='document.autorise_edt.submit();' />
<label for="activTous">&nbsp;Activer les emplois du temps pour tous les utilisateurs</label>
</p>
<p>
		<input name="activ_tous" id="activPas" value="n" type="radio"<?php echo eval_checked("autorise_edt_tous", "n"); ?> onclick='document.autorise_edt.submit();' />
<label for="activPas">&nbsp;Désactiver les emplois du temps pour tous les utilisateurs</label>
</p>

	</form>

<br /><br />
<form action="edt.php" method="post" name="autorise_prof">
<p>
  <input type="radio" name="autorise_saisir_prof" id="autoProf" value="y"<?php echo eval_checked("edt_remplir_prof", "y"); ?> onclick="document.autorise_prof.submit();">
  <label for="autoProf">Autoriser le professeur &agrave; saisir son emploi du temps</label>
</p>
<p>
  <input type="radio" name="autorise_saisir_prof" id="autoProfNon" value="n"<?php echo eval_checked("edt_remplir_prof", "n"); ?> onclick="document.autorise_prof.submit();">
  <label for="autoProfNon">Interdire au professeur de saisir son emploi du temps</label>
</p>
</form>

<br /><br />

	<form action="edt.php" method="post" name="autorise_admin">

<i>Les comptes </i>administrateur<i> ont acc&egrave;s aux emplois du temps si celui-ci est activ&eacute; pour eux. Si vous
avez d&eacute;sactiv&eacute; l'acc&egrave;s pour tous,
 vous pouvez quand m&ecirc;me autoriser les comptes </i>administrateur<i> &agrave; y avoir acc&egrave;s.</i><br />
<p>
		<input name="activ_ad" id="activAdY" value="y" type="radio"<?php echo eval_checked("autorise_edt_admin", "y"); ?> onclick='document.autorise_admin.submit();' />
<label for="activAdY">&nbsp;Activer les emplois du temps pour les administrateurs</label>
</p>
<p>
		<input name="activ_ad" id="activAdN" value="n" type="radio"<?php echo eval_checked("autorise_edt_admin", "n"); ?> onclick='document.autorise_admin.submit();' />
<label for="activAdN">D&eacute;sactiver les emplois du temps pour les administrateurs</label>
</p>

	</form>
<br /><hr />

<h3>Gestion de l'acc&egrave;s pour les &eacute;l&egrave;ves et leurs responsables</h3>

	<form action="edt.php" method="post" name="autorise_ele">

<i>Si vous souhaitez rendre accessible leur emploi du temps aux &eacute;l&egrave;ves et
&agrave; leurs responsables, il faut imp&eacute;rativement l'autoriser ici.</i><br />
<p>
		<input name="activ_ele" id="activEleY" value="y" type="radio"<?php echo eval_checked("autorise_edt_eleve", "y"); ?> onclick='document.autorise_ele.submit();' />
<label for="activEleY"> &nbsp;Activer les emplois du temps pour les &eacute;l&egrave;ves et leurs responsables</label>
</p>
<p>
		<input name="activ_ele" id="activEleN" value="n" type="radio"<?php echo eval_checked("autorise_edt_eleve", "n"); ?> onclick='document.autorise_ele.submit();' />
<label for="activEleN">&nbsp;D&eacute;sactiver les emplois du temps pour les &eacute;l&egrave;ves et leurs responsables</label>
</p>
	</form>
<br /><br />
	</div>

<?php
// inclusion du footer
require("../lib/footer.inc.php");
*/
/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime	="";
$tbs_pmv="";
require_once ("../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var();


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/edt_organisation/edt_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($menuAffiche);




?>
