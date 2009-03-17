<?php
// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
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

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

$utilisateur = $_SESSION['utilisateurProfessionnel'];
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//récupération des parametres
//id du compte rendu
$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);
$id_info = isset($_POST["id_info"]) ? $_POST["id_info"] :(isset($_GET["id_info"]) ? $_GET["id_info"] :NULL);
$type = isset($_POST["type"]) ? $_POST["type"] :(isset($_GET["type"]) ? $_GET["type"] :NULL);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);

//$ctCompteRendu = CahierTexteCompteRenduPeer::retrieveByPK($id_ct);
//if ($ctCompteRendu == null) {
//	echo "Pas de compte rendu selectionnés.";
//	die();
//}
echo "<form enctype=\"multipart/form-data\" name=\"duplication_notice_form\" id=\"duplication_notice_form\" action=\"ajax_duplication_notice.php\" method=\"post\">\n";
echo "<input type='hidden' name='id_ct' value='".$id_ct."' />";
echo "<input type='hidden' name='type' value='".$type."' />";
echo "<input type='hidden' id='date_duplication' name='date_duplication'/>";
echo "<fieldset style=\"border: 1px solid grey; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">\n";
echo "<legend style=\"border: 1px solid grey; font-variant: small-caps;\"> Duplication de notice</legend> ";
echo "<table style=\"border-style:solid; border-width:0px;\" cellspacing='20px'><tr><td>";
echo "<select name=\"id_groupe\">";
echo "<option value='-1'>(choisissez un groupe de destination)</option>\n";
foreach ($utilisateur->getGroupes() as $group) {
	echo "<option value='".$group->getId()."'>";
	echo $group->getDescriptionAvecClasses();
	echo "</option>\n";
}
echo "</select>\n";
echo "</td><td>";
echo "<div id='calendar-duplication-container'></div>";
echo "</td><td>";
echo "<button onClick=\"javascript:
			if (typeof calendarDuplicationInstanciation != 'undefined' && calendarDuplicationInstanciation != null) {
				//get the unix date
				calendarDuplicationInstanciation.date.setHours(0);
				calendarDuplicationInstanciation.date.setMinutes(0);
				calendarDuplicationInstanciation.date.setSeconds(0);
				calendarDuplicationInstanciation.date.setMilliseconds(0);
			    $('date_duplication').value = Math.round(calendarDuplicationInstanciation.date.getTime()/1000);
			} else {
				$('date_duplication').value = 0;
			}
			$('duplication_notice_form').request({onComplete: function(transport){ alert(transport.responseText) }});
            new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$id_groupe."',
             	{ onComplete:
             		function(transport) {
             			updateDivModification();
             		}
             	}
            );
			return false;\"
			id=\"bouton_dupliquer\" name=\"Dupliquer\" style='font-variant: small-caps;'>Dupliquer</button>";

echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button onClick=\"javascript:
			$('dupplication_notice').hide();
			return false;\"
			style='font-variant: small-caps;'>Cacher</button>";			
echo "</td></tr></table>";
echo "</fieldset>";
echo "</form>";
?>