<?php
/*
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




/* ---------Variables envoyées au gabarit
*	$tbs_CdT_public_titre						cahier de textes public activé
*	$tbs_multisite									rne si on est en multisite
*	$tbs_gepiSchoolName							nom de l'établissement
*	$tbs_gepiYear										année scolaire en cours
*	$tbs_password_recovery					adresse page de récupération de mot de passe oublié
* $tbs_SSO_lien										adresse page de login SSO
*	$tbs_admin_java									nom du script pour contacter l'administrateur
*	$tbsStyleScreenAjout						chemin du fichier Style_Screen_Ajout.css
*	
*	----- tableaux -----
*	$tbs_Site_ferme									message de fermeture									tbs_blk1
* $tbs_message										message sous l'entête									tbs_message
*				-> classe									classe CSS ("" ou "txt_rouge")
*				-> texte									le texte à afficher
*	$tbs_admin_adr									adresse courriel administrateur				tbs_blk2
*				-> nom
*				-> fai
* $tbs_dossier_gabarit						liste des gabarits disponibles				tbs_blk3
*				-> texte									texte à afficher dans la liste de choix
*				-> value									nom du dossier
*				-> selection							`y` si gabarit par défaut, `n` ou rien sinon
*/


/*
table à ajouter pour pouvoir utiliser plusieurs gabarits et données du gabarit d'origine 

CREATE TABLE `gabarits` (
`index` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`texte` VARCHAR( 32 ) NOT NULL ,
`repertoire` VARCHAR( 16 ) NOT NULL ,
`pardefaut` CHAR( 1 ) NOT NULL DEFAULT 'n'
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

INSERT INTO `gabarits` (
`index` ,
`texte` ,
`repertoire` ,
`pardefaut`
)
VALUES (
NULL , 'Interface de GEPI', 'origine', 'n'
);


*/

// On utilise mysqli
$useMysqli = TRUE;


//test version de php
if (version_compare(PHP_VERSION, '5') < 0) {
    die('GEPI nécessite PHP5 pour fonctionner');
}



// Pour le tbs_multisite
if (isset($_GET["rne"])) {
	if (!preg_match("/^[0-9A-Za-z]*$/", $_GET['rne'])) {
		die('RNE invalide 0.');
	}

	/*
	include_once(dirname(__FILE__).'/lib/HTMLPurifier.standalone.php');
	$config = HTMLPurifier_Config::createDefault();
	$config->set('Core.Encoding', 'utf-8'); // replace with your encoding
	$config->set('HTML.Doctype', 'XHTML 1.0 Strict'); // replace with your doctype
	$purifier = new HTMLPurifier($config);

	if($purifier->purify($_GET['rne'])!=$_GET['rne']) {
		die('RNE invalide.');
	}

	if (preg_match("/^[0-9A-Za-z]*$/", $_GET['rne'])) {
	*/
		setcookie('RNE', $_GET['rne'], null, '/');
	//}
}

// Vérification de la bonne installation de GEPI
require_once("./utilitaires/verif_install.php");

$niveau_arbo = 0;

// On indique qu'il faut créer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("./lib/initialisations.inc.php");

// Si on est sur LCS, on récupère l'identité de connexion:
//if ($is_lcs_plugin=='yes') {list ($idpers,$login) = isauth();}
// Inutile, c'est déjà fait dans lib/initialisations.inc.php

# On redirige vers le login SSO si le login local ou ldap n'est pas activé.
//if ($session_gepi->auth_sso && !$session_gepi->auth_locale && !$session_gepi->auth_ldap) {
if (($session_gepi->auth_sso && !$session_gepi->auth_locale && ! $session_gepi->auth_ldap) ||
(($is_lcs_plugin=='yes')&&($login!=""))) {
	header("Location:login_sso.php");
	exit();
}

if ($session_gepi->auth_simpleSAML == 'yes') {
	//l'authentification est faite pour chaque page par simpleSAML, pas besoin de page d'authentification
	header("Location: ./accueil.php");
	die();
}

// Test de mise à jour : si on détecte que la base n'est à jour avec les nouveaux
// paramètres utilisés pour l'authentification, on redirige vers maj.php pour
// une mise à jour, normale ou forcée.
if (!isset($gepiSettings['auth_sso'])) {
	header("Location:utilitaires/maj.php");
	exit();
}

// Authentification Classique et Ldap
//-----------------------------------


if ($session_gepi->auth_locale && isset($_POST['login']) && isset($_POST['no_anti_inject_password'])) {

	$auth = $session_gepi->authenticate($_POST['login'], $NON_PROTECT['password']);

	if ($auth == "1") {
		// On renvoie à la page d'accueil
		session_write_close();
		header("Location: ./accueil.php");
		die();

	} else {
		header("Location: ./login_failure.php?error=".$auth);
		die();
	}
}
?>



<?php

$test = 'templates/accueil_externe.php' ;


//==================================
//Site en maintenance
	$tbs_Site_ferme = array();
	if ((getSettingValue("disable_login"))!='no'){
		// Fermeture du site à afficher en rouge et plus grand
		$tbs_Site_ferme[0] = "Le site est en cours de maintenance et temporairement inaccessible.";
		$tbs_Site_ferme[1] = "Veuillez nous excuser de ce dérangement et réessayer de vous connecter ultérieurement.";
	}


//==================================
//On vérifie si le module cahiers de textes public est activé
	$tbs_CdT_public_titre =  "" ;
	if (getSettingValue("active_cahiers_texte")=='y' and getSettingValue("cahier_texte_acces_public") == "yes" and getSettingValue("disable_login")!='yes') {
		$tbs_CdT_public_titre = "Consulter les cahiers de textes (accès public)";
	}
//==================================
//Utilisation tbs_multisite
	$tbs_multisite = "";
	if ($multisite == "y" AND isset($_GET["rne"]) AND $_GET["rne"] != '' AND preg_match("/^[0-9A-Za-z]*$/", $_GET["rne"])) {
		$tbs_multisite = $_GET["rne"];
	}

//==================================
//      Cadre identification
//==================================

//==================================
//Nom année
	$tbs_gepiSchoolName = getSettingValue("gepiSchoolName");
	$tbs_gepiYear = getSettingValue("gepiYear");
	
//==================================
//Message
	if (isset($message)) {
		$tbs_message[] =array("classe"=>"txt_rouge","texte" => $message);
	} else {
		//$tbs_message_class = "message";
		$tbs_message[] =array("classe"=>"","texte" => "Afin d'utiliser Gepi, vous devez vous identifier.");
	}

	$tbs_input_password_to_text= input_password_to_text('no_anti_inject_password');

//==================================
//	Mode d'alerte CapsLock
	$tbs_mode_alerte_capslock = "capsDetect";
	if (getSettingValue("login_mode_alerte_capslock")==2) {
		$tbs_mode_alerte_capslock = "capsDetect2";
	}

//==================================
//	Mot de passe oublié
	$tbs_password_recovery = "";
	if (getSettingValue("enable_password_recovery") == "yes") {
		$tbs_password_recovery = "recover_password.php";
	}


//==================================
//	Demande de compte/mot de passe
	$tbs_demande_compte_mdp = "";
	if (getSettingAOui("GepiResp_obtenir_compte_et_motdepasse")) {
		$tbs_demande_compte_mdp = "obtenir_compte_et_motdepasse.php";
	}

//==================================
//	authentification unique
	$tbs_SSO_lien = "";
	if ($session_gepi->auth_sso) {
		$tbs_SSO_lien = 'login_sso.php';
	// ajouter un test sur plugin_sso_table
		if (mb_strlen(getSettingValue('login_sso_url'))>0) {
			$tbs_SSO_lien = getSettingValue('login_sso_url');
		}
	}

	
//==================================
//	Feuille de style style_screen_ajout.css
if (isset($style_screen_ajout))  {

	// Styles paramétrables depuis l'interface:
	if($style_screen_ajout=='y') {
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			if (@file_exists('./style_screen_ajout_'.getSettingValue("gepiSchoolRne").'.css')) {
				$tbsStyleScreenAjout=$gepiPath."/style_screen_ajout_".getSettingValue("gepiSchoolRne").".css";	
			}else {
				$tbsStyleScreenAjout="";	
			}
		} else {
			if (@file_exists('./style_screen_ajout.css')) {
				$tbsStyleScreenAjout=$gepiPath."/style_screen_ajout.css";	
			}else {
				$tbsStyleScreenAjout="";	
			}
		}
	} else {
		$tbsStyleScreenAjout="";	
	}
} else {
	$tbsStyleScreenAjout="";	
}
	


//==================================
//	administrateurs
	$tbs_admin_adr=array();
	$tbs_admin_titre="";
	if(getSettingValue("gepiAdminAdressPageLogin")!='n'){
		$gepiAdminAdress=getSettingValue("gepiAdminAdress");
		//$tmp_adr=explode("@",$gepiAdminAdress);
		//echo("<a href=\"javascript:pigeon('$tmp_adr[0]','$tmp_adr[1]');\">[Contacter l'administrateur]</a> \n");
		//echo "$gepiAdminAdress<br />";
		//$compteur=0;
		$tab_adr=array();
		$tmp_adr1=explode(",",$gepiAdminAdress);
		for($i=0;$i<count($tmp_adr1);$i++){
			//echo "\$tmp_adr1[$i]=$tmp_adr1[$i]<br />";
			$tmp_adr2=explode("@",$tmp_adr1[$i]);
			//echo "\$tmp_adr2[0]=$tmp_adr2[0]<br />";
			//echo "\$tmp_adr2[1]=$tmp_adr2[1]<br />";
			if((isset($tmp_adr2[0]))&&(isset($tmp_adr2[1]))) {
				$tbs_admin_adr[]=array("nom"=>$tmp_adr2[0] , "fai"=>$tmp_adr2[1]);
				/*
				$tab_adr[$compteur]=$tmp_adr2[0];
				$compteur++;
				$tab_adr[$compteur]=$tmp_adr2[1];
				$compteur++;
				*/
			}
		}

		//echo "<script type='text/javascript'>\n";
		//echo "adm_adr=new Array();\n";
		/*
			for($i=0;$i<count($tab_adr);$i++){
				echo "adm_adr[$i]='$tab_adr[$i]';\n";
			}
		//echo "</script>\n";
		if(count($tab_adr)>0){
			//echo("<a href=\"javascript:pigeon2(adm_adr);\">[Contacter l'administrateur]</a> \n");
			//echo("<p><a href=\"javascript:pigeon2();\">[Contacter l'administrateur]</a></p>\n");
		}
		*/
	}
	

//==================================

$msg_page_login="";

// 20140301
$auth_sso_secours=isset($_GET['auth_sso_secours']) ? $_GET['auth_sso_secours'] : "n";
if((isset($auth_sso_secours))&&
	($auth_sso_secours=="y")&&
	(getSettingAOui('autoriser_sso_password_auth'))) {
		$msg_page_login=getSettingValue('auth_sso_secours_msg');
}
else {
	$test = mysqli_query($GLOBALS["mysqli"], "SHOW TABLES LIKE 'message_login'");
	if(mysqli_num_rows($test)>0) {
		$sql="SELECT ml.texte FROM message_login ml, setting s WHERE s.value=ml.id AND s.name='message_login';";
		//echo "$sql <br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);

		if(mysqli_num_rows($res)>0) {
			$lig_page_login=mysqli_fetch_object($res);
			$msg_page_login=$lig_page_login->texte;
		}
	}
}

//==================================
//	gabarits dynamiques




//==================================
//	switcher de gabarits

	$tbs_dossier_gabarit=array();


$test = mysqli_query($GLOBALS["mysqli"], "SHOW TABLES LIKE 'gabarits'");

		$sql="SELECT texte, repertoire, pardefaut FROM gabarits ;";
		$res_gab=mysqli_query($GLOBALS["mysqli"], $sql);
	if($res_gab){
	
		if(mysqli_num_rows($res_gab)>0) {
			while($lig_gab=mysqli_fetch_object($res_gab)) {
				$texte_gab=$lig_gab->texte;
				$repertoire_gab=$lig_gab->repertoire;
				$defaut_gab=$lig_gab->pardefaut;
				if($defaut_gab=="y"){
					$value_gab="selected='selected'";
					$gabarit=$lig_gab->repertoire;
				}else{
					$value_gab="";
				}
			$tbs_dossier_gabarit[]=array("texte"=>$texte_gab, "selection"=>$value_gab, "value"=>$repertoire_gab);	
			}
		}
		
	}else{
		$gabarit="origine";
	}

	if ((isset($_GET['template'])) or (isset($_POST['template'])) or (isset($gabarit))) {
		$gabarit = isset($_POST['template']) ? unslashes($_POST['template']) : (isset($_GET['template']) ? unslashes($_GET['template']) : $gabarit);
	}
	else{
		$gabarit="origine";
	}


	// Pour repérer les onglets lors du développement:
	//insert into setting set value='y', name='afficher_version_en_title';
	$tbs_prefixe_title="";
	if(getSettingAOui("afficher_version_en_title")) {
		$tbs_prefixe_title="(".getSettingValue("version").") ";
	}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
//debug_var();

// appel des bibliothèques tinyButStrong


$_SESSION['tbs_class'] = 'tbs/tbs_class.php';
include_once($_SESSION['tbs_class']);
			
		

	$_SESSION['rep_gabarits'] = $gabarit;

//==================================
// Appel de script externe
	
	$entete_externe = "templates/".$_SESSION['rep_gabarits']."/login_entete_externe.php" ;
	$corps_externe = "templates/".$_SESSION['rep_gabarits']."/login_corps_externe.php" ;
	$pied_externe = "templates/".$_SESSION['rep_gabarits']."/login_pied_externe.php" ;

	$fichier_gabarits='templates/'.$_SESSION['rep_gabarits'].'/login_template.html' ;
		
	$TBS = new clsTinyButStrong ;
	$TBS->LoadTemplate($fichier_gabarits) ;
	$TBS->MergeBlock('tbs_blk1',$tbs_Site_ferme);
	$TBS->MergeBlock('tbs_blk2',$tbs_admin_adr);
	$TBS->MergeBlock('tbs_blk3',$tbs_dossier_gabarit);
	$TBS->MergeBlock("tbs_message",$tbs_message);
	/*
	if(isset($lig_page_login)) {
		$TBS->MergeBlock("msg_page_login",$lig_page_login);
	}
	if(isset($msg_page_login)) {
		$TBS->MergeBlock("msg_page_login",$msg_page_login);
	}
	*/

	$TBS->Show() ;

// ------ on vide les tableaux -----
	unset($tbs_Site_ferme,$tbs_admin_adr,$tbs_dossier_gabarit,$tbs_message);

?> 
