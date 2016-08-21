<?php
/*
 *
 * Copyright 2009-2011 Josselin Jacquard
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
header('Content-Type: text/html; charset=utf-8');

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
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
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
echo add_token_field();
echo "<input type='hidden' id='id_ct' name='id_ct' value='".$id_ct."' />";
echo "<input type='hidden' id='type' name='type' value='".$type."' />";
echo "<input type='hidden' id='date_duplication' name='date_duplication'/>";
echo "<fieldset style=\"border: 1px solid grey; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">\n";
echo "<legend style=\"border: 1px solid grey; font-variant: small-caps;\"> Duplication de notice</legend> ";
echo "<table style=\"border-style:solid; border-width:0px;\" cellspacing='20px'><tr><td>";
echo "<select id=\"id_groupe_duplication\" name=\"id_groupe_duplication\">";
echo "<option value='-1'>(choisissez un groupe de destination)</option>\n";
foreach ($utilisateur->getGroupes() as $group) {
	echo "<option value='".$group->getId()."'";
	if ($group->getId() == $id_groupe) {
		echo " selected='true' ";
	}

	echo " title=\"".$group->getName()." - ".$group->getDescriptionAvecClasses()." (";
	$cpt_prof=0;
	foreach($group->getProfesseurs() as $prof) {
		if($cpt_prof>0) {echo ", ";}
		echo casse_mot($prof->getNom(),"maj")." ".casse_mot($prof->getPrenom(),"majf2");
		$cpt_prof++;
	}
	echo ").\"";

	echo ">";
	echo $group->getDescriptionAvecClasses();
	echo "</option>\n";
}
echo "</select>\n";
echo "</td><td>";
echo "<div id='calendar-duplication-container'></div>";
echo "</td><td>";
echo "<button onClick=\"javascript:
			//d'abord on enregistre la notice pour prendre en compte des modifications de contenu eventuelles
			//la duplication est faite dans completeDuplicationNoticeCallback
			AIM.submit($('modification_compte_rendu_form'), {'onComplete' : completeDuplicationNoticeCallback});
			$('modification_compte_rendu_form').submit();
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
