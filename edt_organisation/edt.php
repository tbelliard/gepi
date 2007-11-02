<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2007
 */
$titre_page = "Emploi du temps";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = resumeSession();
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
$style_specifique = "edt_organisation/style_edt";
// ====== Entête de Gepi =========================
require_once("../lib/header.inc");
// ===============================================

// ++++++++++ Initialisation des variables +++++++
$autorise_acces_admin = isset($_POST["activ_ad"]) ? $_POST["activ_ad"] : NULL;
$autorise_acces_eleve = isset($_POST["activ_ele"]) ? $_POST["activ_ele"] : NULL;
$autorise_acces_tous = isset($_POST["activ_tous"]) ? $_POST["activ_tous"] : NULL;
$modif_setting = "";
$message = "";

	// Modification du setting autorise_edt_tous
	if (isset($autorise_acces_tous)) {
		$requete = "UPDATE setting SET value = '".$autorise_acces_tous."' WHERE name = 'autorise_edt_tous'";
		$modif_setting = "ok";
	}

	// Modification du setting autorise_edt_admin
	if (isset($autorise_acces_admin)) {
		$requete = "UPDATE setting SET value = '".$autorise_acces_admin."' WHERE name = 'autorise_edt_admin'";
		$modif_setting = "ok";
	}

	// Modification du setting autorise_edt_eleve
	if (isset($autorise_acces_eleve)) {
		$requete = "UPDATE setting SET value = '".$autorise_acces_eleve."' WHERE name = 'autorise_edt_eleve'";
		$modif_setting = "ok";
	}

	// On effectue la requête
	if ($modif_setting == "ok") {
		$modif = mysql_query($requete) OR DIE('La modification n\'a pas été enregistrée : '.mysql_error());
		$message .= "<p class=\"red\">La modification a bien été enregistrée !</p>";
	}

	// Petite fonction pour déterminer le checked="checked"
	function eval_checked($Settings, $yn){
	$req_setting = mysql_fetch_array(mysql_query("SELECT value FROM setting WHERE name = '".$Settings."'")) OR DIE ('Erreur requête eval_setting () : '.mysql_error());
		if ($req_setting["value"] == $yn) {
			$aff_check = ' checked="checked"';
		}else {
			$aff_check = '';
		}
		return $aff_check;
	}

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

<i>La désactivation des emplois du temps n'entraîne aucune suppression des données.
 Lorsque le module est désactivé, personne n'a accès au module et
 la consultation des emplois du temps est impossible.</i><br />
<br />
		<input name="activ_tous" value="y" type="radio"<?php echo eval_checked("autorise_edt_tous", "y"); ?> onclick='document.autorise_edt.submit();' />
&nbsp;Activer les emplois du temps pour tous les utilisateurs<br />
		<input name="activ_tous" value="n" type="radio"<?php echo eval_checked("autorise_edt_tous", "n"); ?> onclick='document.autorise_edt.submit();' />
&nbsp;Désactiver les emplois du temps pour tous les utilisateurs<br />

	</form>

<br /><br />

	<form action="edt.php" method="post" name="autorise_admin">

<i>Si vous avez d&eacute;sactiver l'acc&egrave;s g&eacute;n&eacute;ral aux emplois du temps,
 vous pouvez autoriser les comptes administrateurs &agrave; y avoir acc&egrave;s.</i><br />
<br />
		<input name="activ_ad" value="y" type="radio"<?php echo eval_checked("autorise_edt_admin", "y"); ?> onclick='document.autorise_admin.submit();' />
&nbsp;Activer les emplois du temps pour les administrateurs<br />
		<input name="activ_ad" value="n" type="radio"<?php echo eval_checked("autorise_edt_admin", "n"); ?> onclick='document.autorise_admin.submit();' />
&nbsp;D&eacute;sactiver les emplois du temps pour les administrateurs<br />

	</form>
<br /><hr />

<h3>Gestion de l'acc&egrave;s pour les &eacute;l&egrave;ves et leurs responsables</h3>

	<form action="edt.php" method="post" name="autorise_ele">

<i>Si vous souhaitez rendre accessible leur emploi du temps aux &eacute;l&egrave;ves et
&agrave; leurs responsables, il faut imp&eacute;rativement l'autoriser ici.</i><br />
<br />
		<input name="activ_ele" value="y" type="radio"<?php echo eval_checked("autorise_edt_eleve", "y"); ?> onclick='document.autorise_ele.submit();' />
&nbsp;Activer les emplois du temps pour les &eacute;l&egrave;ves et leurs responsables<br />
		<input name="activ_ele" value="n" type="radio"<?php echo eval_checked("autorise_edt_eleve", "n"); ?> onclick='document.autorise_ele.submit();' />
&nbsp;D&eacute;sactiver les emplois du temps pour les &eacute;l&egrave;ves et leurs responsables<br />
	</form>
<br /><br />
	</div>

<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>