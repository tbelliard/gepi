<?php
header('Content-Type: text/html; charset=ISO-8859-1');
// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
include("include_affiche_notices_vignettes.php");
//echo("Debug Locale : ".setLocale(LC_TIME,0));

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

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

$utilisateur = $_SESSION['utilisateurProfessionnel'];
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

$groups = $utilisateur->getGroupes();

echo "<table width='100%' cellspacing='5px'>";
$i = 0;
//pour chaque groupe, on récupère un compte rendu et un travail à  faire
foreach ($groups as $group) {
	$i = $i + 1;
	//on affiche sur deux colonne : % est l'operateur pour modulo
	if (($i % 2) == 1) echo "<tr>\n";
	echo "<td valign=\"top\" width=\"50%\"><table width='100%' cellspacing='5px'>\n";

	//affichage du groupe
	echo "<tr><td  style=\"background-color: silver; padding: 2px; border: 1px solid black; font-weight: bold;\" colspan='2'>\n";
	echo "<a href=\"#\" onclick=\"javascript:
			            id_groupe = '".$group->getId()."';
						getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?&id_groupe=".$group->getId()."&today='+getCalendarUnixDate(),
				            {onComplete : function() { initWysiwyg();}});
						getWinListeNotices();
						new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$group->getId()."');
						return false;
			       \">";
	echo "<img style=\"border: 0px;\" src=\"../images/ico_edit16plus.png\" alt=\"modifier\" title=\"modifier\" />&nbsp;";
	echo $group->getDescriptionAvecClasses();
	echo "</a></td></tr>";

	//récupération des derniers compte rendu
	$criteria = new Criteria(CahierTexteCompteRenduPeer::DATABASE_NAME);
	$criteria->add(CahierTexteCompteRenduPeer::DATE_CT, "0", "!=");
	$criteria->add(CahierTexteCompteRenduPeer::DATE_CT, null, Criteria::ISNOTNULL);
	$debutCdt = getSettingValue("begin_bookings");
	$criteria->add(CahierTexteCompteRenduPeer::DATE_CT, $debutCdt, ">=");
	$criteria->addDescendingOrderByColumn(CahierTexteCompteRenduPeer::DATE_CT);
	$criteria->addAscendingOrderByColumn(CahierTexteCompteRenduPeer::HEURE_ENTRY);
	$criteria->setLimit(2);
	$ctCompteRendus = $group->getCahierTexteCompteRendus($criteria);

	echo "<tr>";
	//afichage du dernier compte rendu
	echo "<td style=\"width:50%;\" valign=\"top\">";
	if (isset($ctCompteRendus[0]) && $ctCompteRendus[0] != null) {
		$compte_rendu = $ctCompteRendus[0];
		affiche_compte_rendu_vignette($compte_rendu, $couleur_bord_tableau_notice, $color_fond_notices);
	}
	echo "</td>";

	//récupération et affichage du dernier travail à faire
	$criteria = new Criteria(CahierTexteTravailAFairePeer::DATABASE_NAME);
	$criteria->add(CahierTexteTravailAFairePeer::DATE_CT, $debutCdt, ">=");
	$criteria->addDescendingOrderByColumn(CahierTexteTravailAFairePeer::DATE_CT);
	$criteria->setLimit(1);
	$ctTravailAFaires = $group->getCahierTexteTravailAFaires($criteria);
	echo "<td style=\"width:50%;\" valign=\"top\">";
	if (!empty($ctTravailAFaires)) {
		$devoir = $ctTravailAFaires[0];
		//on affiche le devoir car il y en a un
		affiche_devoir_vignette($devoir, $couleur_bord_tableau_notice, $color_fond_notices);
	}
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>";
	//récupération et affichage du deuxieme compte rendu
	echo "<td style=\"width:50%;\" valign=\"top\">";
	if (isset($ctCompteRendus[1]) && $ctCompteRendus[0] != null) {
		$compte_rendu = $ctCompteRendus[1];
		affiche_compte_rendu_vignette($compte_rendu, $couleur_bord_tableau_notice, $color_fond_notices);
	}
	echo "</td>";

	//récupération et affichage de la derniere notice privee
	$criteria = new Criteria();
	$criteria->add(CahierTexteNoticePriveePeer::DATE_CT, $debutCdt, ">=");
	$criteria->addDescendingOrderByColumn(CahierTexteNoticePriveePeer::DATE_CT);
	$criteria->setLimit(1);
	$noticePrivees = $group->getCahierTexteNoticePrivees($criteria);
	echo "<td style=\"width:50%;\" valign=\"top\">";
	if (isset($noticePrivees[0]) && $noticePrivees[0] != null) {
		$noticePrivee = $noticePrivees[0];
		//on affiche le devoir car il y en a un
		affiche_notice_privee_vignette($noticePrivee, $couleur_bord_tableau_notice, $color_fond_notices);
	}
	echo "</td>\n";
	echo "</tr>\n";


	echo "</table></td>\n";

	//on affiche sur deux colonne : % est l'operateur pour modulo
	if (($i % 2) == 0) echo "</tr>\n";
}
echo "</table>";
?>