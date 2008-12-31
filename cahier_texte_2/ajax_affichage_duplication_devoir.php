<?php
// On dÃ©samorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
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

//On vÃ©rifie si le module est activÃ©
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

$utilisateur = $_SESSION['utilisateur'];
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//récupération des parametres
//id du compte rendu
$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);

$ctTravailAFaire = CtTravailAFairePeer::retrieveByPK($id_ct);
if ($ctTravailAFaire == null) {
	echo "Pas de devoir selectionné.";
	die();
}
echo "<form enctype=\"multipart/form-data\" name=\"duplication_devoir_form\" id=\"duplication_devoir_form\" action=\"ajax_duplication_devoir.php\" method=\"post\" onsubmit=\"return true\">\n";
echo "<input type='hidden' name='id_ct' value='".$id_ct."' />";
echo "<input type='hidden' id='date_duplication' name='date_duplication'/>";
echo "<fieldset style=\"border: 1px solid grey; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto; background: ".$color_fond_notices[$type_couleur].";\">\n";
echo "<legend style=\"border: 1px solid grey; background: ".$color_fond_notices[$type_couleur]."; font-variant: small-caps;\"> Duplication de devoir</legend> ";
echo "<table style=\"border-style:solid; border-width:0px;\" cellspacing='20px'><tr><td>";
$i = 0;

//liste des groupes
echo "<select name=\"id_groupe\">";
echo "<option value='-1'>(choisissez un groupe de destination)</option>\n";
foreach ($utilisateur->getGroupes() as $group) {
	echo "<option value='".$group->getId()."'>";
	echo $group->getDescription() . "&nbsp;-&nbsp;(";
	$str = null;
	foreach ($group->getClasses() as $classe) {
		$str .= $classe->getClasse() . ", ";
	}
	$str = substr($str, 0, -2);
	echo $str . ")&nbsp;\n";
	echo "</option>\n";
}
echo "</select>\n";
echo "</td><td>";
echo "<div id='calendar-duplication-container'></div>";
echo "</td><td>";
echo "<button onClick=\"javascript:
			//get the unix date
			calendarDuplicationInstanciation.date.setHours(0);
			calendarDuplicationInstanciation.date.setMinutes(0);
			calendarDuplicationInstanciation.date.setSeconds(0);
			calendarDuplicationInstanciation.date.setMilliseconds(0);
			$('date_duplication').value = Math.round(calendarDuplicationInstanciation.date.getTime()/1000);
			
			$('duplication_devoir_form').request({onComplete: function(transport){ alert(transport.responseText) }});

            getWinListesNotices().setAjaxContent( 
             						'./ajax_affichages_liste_notices.php?id_groupe=".$ctTravailAFaire->getIdGroupe()."&today=' + getCalendarUnixDate(),
             						{ method: 'get' ,
             							onComplete: function(transport) {
             																compte_rendu_en_cours_de_modification('devoir_".$ctTravailAFaire->getIdCt()."');
             															}
             						}
              					);
			return false;\"
			id=\"bouton_dupliquer\" name=\"Dupliquer\" style='font-variant: small-caps;'>Dupliquer</button>";

echo "</td></tr></table>";
echo "</fieldset>";
echo "</form>";
?>