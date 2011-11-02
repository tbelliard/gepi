<?php
@set_time_limit(0);
/*
* $Id: professeurs.php 8578 2011-10-30 10:53:59Z crob $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
extract($_POST, EXTR_OVERWRITE);

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

//================================================
// Fonction de génération de mot de passe récupérée sur TotallyPHP
// Aucune mention de licence pour ce script...

/*
 * The letter l (lowercase L) and the number 1
 * have been removed, as they can be mistaken
 * for each other.
*/

function createRandomPassword() {
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;

    //while ($i <= 7) {
    while ($i <= 5) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }

    return $pass;
}
//================================================

function affiche_debug($texte){
	// Passer à 1 la variable pour générer l'affichage des infos de debug...
	$debug=0;
	if($debug==1){
		echo "<font color='green'>".$texte."</font>";
		flush();
	}
}


include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_professeurs;

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des professeurs";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

require_once("init_xml_lib.php");

// On vérifie si l'extension d_base est active
//verif_active_dbase();

?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php
echo "<center><h3 class='gepi'>Quatrième phase d'initialisation<br />Importation des professeurs</h3></center>\n";

if (!isset($step1)) {
	$j=0;
	$flag=0;
	$chaine_tables="";
	while (($j < count($liste_tables_del)) and ($flag==0)) {
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE '$liste_tables_del[$j]'"));
		if($test==1) {
			if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$flag=1;
			}
		}
		$j++;
	}

	for($loop=0;$loop<count($liste_tables_del);$loop++) {
		if($chaine_tables!="") {$chaine_tables.=", ";}
		$chaine_tables.="'".$liste_tables_del[$loop]."'";
	}

	$test = mysql_result(mysql_query("SELECT count(*) FROM utilisateurs WHERE statut='professeur'"),0);
	if ($test != 0) {$flag=1;}

	if ($flag != 0){
		echo "<p><b>ATTENTION ...</b><br />\n";
		echo "Des données concernant les professeurs sont actuellement présentes dans la base GEPI<br /></p>\n";
		echo "<p>Si vous poursuivez la procédure les données telles que notes, appréciations, ... seront effacées.</p>\n";

		echo "<p>Les tables vidées seront&nbsp;: $chaine_tables</p>\n";

		echo "<ul><li>Seule la table contenant les utilisateurs (professeurs, admin, ...) et la table mettant en relation les matières et les professeurs seront conservées.</li>\n";
		echo "<li>Les professeurs de l'année passée présents dans la base GEPI et non présents dans le fichier XML de cette année ne sont pas effacés de la base GEPI mais simplement déclarés \"inactifs\".</li>\n";
		echo "</ul>\n";

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo add_token_field();
		echo "<input type=hidden name='step1' value='y' />\n";
		echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />\n";
		echo "</form>\n";
        echo "<p><br /></p>\n";
		echo "</div>\n";
		require("../lib/footer.inc.php");
		die();
	}
}

if (!isset($is_posted)) {
	if(isset($step1)) {
		$dirname=get_user_temp_directory();

		$sql="SELECT * FROM j_professeurs_matieres WHERE ordre_matieres='1';";
		$res_matiere_principale=mysql_query($sql);
		if(mysql_num_rows($res_matiere_principale)>0) {
			$fich_mp=fopen("../temp/".$dirname."/matiere_principale.csv","w+");
			if($fich_mp) {
				echo "<p>Création d'un fichier de sauvegarde de la matière principale de chaque professeur.</p>\n";
				while($lig_mp=mysql_fetch_object($res_matiere_principale)) {
					fwrite($fich_mp,"$lig_mp->id_professeur;$lig_mp->id_matiere\n");
				}
				fclose($fich_mp);
			}
			else {
				echo "<p style='color:red'>Echec de la création d'un fichier de sauvegarde de la matière principale de chaque professeur.</p>\n";
			}
		}

		$sql="SELECT * FROM j_professeurs_matieres ORDER BY ordre_matieres;";
		$res_matieres_profs=mysql_query($sql);
		if(mysql_num_rows($res_matieres_profs)>0) {
			$fich_mp=fopen("../temp/".$dirname."/matieres_profs_an_dernier.csv","w+");
			if($fich_mp) {
				echo "<p>Création d'un fichier de sauvegarde des matières (<i>de l'an dernier</i>) de chaque professeur.</p>\n";
				while($lig_mp=mysql_fetch_object($res_matieres_profs)) {
					fwrite($fich_mp,"$lig_mp->id_professeur;$lig_mp->id_matiere\n");
				}
				fclose($fich_mp);
			}
			else {
				echo "<p style='color:red'>Echec de la création d'un fichier de sauvegarde des matières (<i>de l'an dernier</i>) de chaque professeur.</p>\n";
			}
		}

		check_token(false);
		$j=0;
		while ($j < count($liste_tables_del)) {
			$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE '$liste_tables_del[$j]'"));
			if($test==1){
				if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
					$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
				}
			}
			$j++;
		}
	}
	$del = @mysql_query("DELETE FROM tempo2");

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	//echo "<p>Importation du fichier <b>F_wind.csv</b> contenant les données relatives aux professeurs.";

	echo "<p>Importation du fichier <b>sts.xml</b> contenant les données relatives aux professeurs.\n";
	//echo "<p>Veuillez préciser le nom complet du fichier <b>F_wind.csv</b>.";
	echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo "<input type=hidden name='step1' value='y' />\n";
	//echo "<p><input type='file' size='80' name='dbf_file' />";
	echo "<br /><br /><p>Quelle formule appliquer pour la génération du login ?</p>\n";

	if(getSettingValue("use_ent")!='y') {
		$default_login_gen_type=getSettingValue('login_gen_type');
		if($default_login_gen_type=='') {$default_login_gen_type='name';}
	}
	else {
		$default_login_gen_type="";
	}

	if(getSettingValue('auth_sso')=="lcs") {
		echo "<span style='color:red'>Votre Gepi utilise une authentification LCS; Le format de login ci-dessous ne sera pas pris en compte. Les comptes doivent avoir été importés dans l'annuaire LDAP du LCS avant d'effectuer l'import dans GEPI.</span><br />\n";
	}

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_name' value='name' ";
	if($default_login_gen_type=='name') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_name'  style='cursor: pointer;'>nom</label>\n";
	echo "<br />\n";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_name8' value='name8' ";
	if($default_login_gen_type=='name8') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_name8'  style='cursor: pointer;'>nom (<em>tronqué à 8 caractères</em>)</label>\n";
	echo "<br />";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_fname8' value='fname8' ";
	if($default_login_gen_type=='fname8') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_fname8'  style='cursor: pointer;'>pnom (<em>tronqué à 8 caractères</em>)</label>\n";
	echo "<br />\n";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_fname19' value='fname19' ";
	if($default_login_gen_type=='fname19') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_fname19'  style='cursor: pointer;'>pnom (<em>tronqué à 19 caractères</em>)</label>\n";
	echo "<br />\n";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_firstdotname' value='firstdotname' ";
	if($default_login_gen_type=='firstdotname') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_firstdotname'  style='cursor: pointer;'>prenom.nom</label>\n";
	echo "<br />\n";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_firstdotname19' value='firstdotname19' ";
	if($default_login_gen_type=='firstdotname19') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_firstdotname19'  style='cursor: pointer;'>prenom.nom (<em>tronqué à 19 caractères</em>)</label>\n";
	echo "<br />\n";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_namef8' value='namef8' ";
	if($default_login_gen_type=='namef8') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_namef8'  style='cursor: pointer;'>nomp (<em>tronqué à 8 caractères</em>)</label>\n";
	echo "<br />\n";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_lcs' value='lcs' ";
	if($default_login_gen_type=='lcs') {
		echo "checked ";
	}
	echo "/> <label for='login_gen_type_lcs'  style='cursor: pointer;'>pnom (<em>façon LCS</em>)</label>\n";
	echo "<br />\n";

	if (getSettingValue("use_ent") == "y") {
		echo "<input type='radio' name='login_gen_type' id='login_gen_type_ent' value='ent' checked=\"checked\" />\n";
		echo "<label for='login_gen_type_ent'  style='cursor: pointer;'>
			Les logins sont produits par un ENT (<span title=\"Vous devez adapter le code du fichier ci-dessus vers la ligne 710.\">Attention !</span>)</label>\n";
		echo "<br />\n";
	}
	echo "<br />\n";

	// Modifications jjocal dans le cas où c'est un serveur CAS qui s'occupe de tout
	if((getSettingValue("use_sso") == "cas")||(getSettingValue('auth_sso')=="lcs")) {
		$checked1 = ' checked="checked"';
		$checked0 = '';
	}else{
		$checked1 = '';
		$checked0 = ' checked="checked"';
	}

	echo "<p>Ces comptes seront-ils utilisés en Single Sign-On avec CAS ou LemonLDAP ? (<i>laissez 'non' si vous ne savez pas de quoi il s'agit</i>)</p>\n";
	echo "<input type='radio' name='sso' id='sso_n' value='no'".$checked0." /> <label for='sso_n' style='cursor: pointer;'>Non</label>\n";
	echo "<br /><input type='radio' name='sso' id='sso_y' value='yes'".$checked1." /> <label for='sso_y' style='cursor: pointer;'>Oui (<em>aucun mot de passe ne sera généré</em>)</label>\n";
	echo "<br />\n";
	echo "<br />\n";


	echo "<p>Dans le cas où la réponse à la question précédente est Non, voulez-vous:</p>\n";
	echo "<p><input type=\"radio\" name=\"mode_mdp\" id='mode_mdp_alea' value=\"alea\" checked /> <label for='mode_mdp_alea' style='cursor: pointer;'>Générer un mot de passe aléatoire pour chaque professeur</label>.<br />\n";
	echo "<input type=\"radio\" name=\"mode_mdp\" id='mode_mdp_date' value=\"date\" /> <label for='mode_mdp_date' style='cursor: pointer;'>Utiliser plutôt la date de naissance au format 'aaaammjj' comme mot de passe initial (<i>il devra être modifié au premier login</i>)</label>.</p>\n";
	echo "<br />\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";
	echo "<p><br /></p>\n";

}
else {
	check_token();

	if(isset($_POST['login_gen_type'])) {
		saveSetting('login_gen_type',$_POST['login_gen_type']);
	}

	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
	}

	$dest_file="../temp/".$tempdir."/sts.xml";
	/*
	$fp=fopen($dest_file,"r");
	if(!$fp){
		echo "<p>Le XML STS Emploi du temps n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	*/

	$sts_xml=simplexml_load_file($dest_file);
	if(!$sts_xml) {
		echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$nom_racine=$sts_xml->getName();
	if(strtoupper($nom_racine)!='STS_EDT') {
		echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML STS_EMP_&lt;RNE&gt;_&lt;ANNEE&gt;.<br />Sa racine devrait être 'STS_EDT'.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<p>";
	echo "Analyse du fichier pour extraire les informations de la section INDIVIDUS...<br />\n";

	$prof=array();
	$i=0;

	$tab_champs_personnels=array("NOM_USAGE",
	"NOM_PATRONYMIQUE",
	"PRENOM",
	"SEXE",
	"CIVILITE",
	"DATE_NAISSANCE",
	"GRADE",
	"FONCTION");

	$prof=array();
	$i=0;

	foreach($sts_xml->DONNEES->INDIVIDUS->children() as $individu) {
		$prof[$i]=array();

		//echo "<span style='color:orange'>\$individu->NOM_USAGE=".$individu->NOM_USAGE."</span><br />";

		foreach($individu->attributes() as $key => $value) {
			// <INDIVIDU ID="4189" TYPE="epp">
			$prof[$i][strtolower($key)]=trim(traite_utf8($value));
		}

		// Champs de l'individu
		foreach($individu->children() as $key => $value) {
			if(in_array(strtoupper($key),$tab_champs_personnels)) {
				if(strtoupper($key)=='SEXE') {
					$prof[$i]["sexe"]=trim(preg_replace("/[^1-2]/","",$value));
				}
				elseif(strtoupper($key)=='CIVILITE') {
					$prof[$i]["civilite"]=trim(preg_replace("/[^1-3]/","",$value));
				}
				elseif((strtoupper($key)=='NOM_USAGE')||
				(strtoupper($key)=='NOM_PATRONYMIQUE')||
				(strtoupper($key)=='NOM_USAGE')) {
					$prof[$i][strtolower($key)]=trim(preg_replace("/[^A-Za-z -]/","",traite_utf8($value)));
				}
				elseif(strtoupper($key)=='PRENOM') {
					$prof[$i][strtolower($key)]=trim(preg_replace("/[^A-Za-zÆæ¼½".$liste_caracteres_accentues." -]/","",traite_utf8($value)));
				}
				elseif(strtoupper($key)=='DATE_NAISSANCE') {
					$prof[$i][strtolower($key)]=trim(preg_replace("/[^0-9-]/","",traite_utf8($value)));
				}
				elseif((strtoupper($key)=='GRADE')||
					(strtoupper($key)=='FONCTION')) {
					$prof[$i][strtolower($key)]=trim(preg_replace('/"/','',traite_utf8($value)));
				}
				else {
					$prof[$i][strtolower($key)]=trim(traite_utf8($value));
				}
				//echo "\$prof[$i][".strtolower($key)."]=".$prof[$i][strtolower($key)]."<br />";
			}
		}

		if(isset($individu->PROFS_PRINC)) {
		//if($temoin_prof_princ>0) {
			$j=0;
			foreach($individu->PROFS_PRINC->children() as $prof_princ) {
				//$prof[$i]["prof_princ"]=array();
				foreach($prof_princ->children() as $key => $value) {
					$prof[$i]["prof_princ"][$j][strtolower($key)]=trim(traite_utf8(preg_replace('/"/',"",$value)));
					$temoin_au_moins_un_prof_princ="oui";
				}
				$j++;
			}
		}

		//if($temoin_discipline>0) {
		if(isset($individu->DISCIPLINES)) {
			$j=0;
			foreach($individu->DISCIPLINES->children() as $discipline) {
				foreach($discipline->attributes() as $key => $value) {
					if(strtoupper($key)=='CODE') {
						$prof[$i]["disciplines"][$j]["code"]=trim(traite_utf8(preg_replace('/"/',"",$value)));
						break;
					}
				}

				foreach($discipline->children() as $key => $value) {
					$prof[$i]["disciplines"][$j][strtolower($key)]=trim(traite_utf8(preg_replace('/"/',"",$value)));
				}
				$j++;
			}
		}

		if($debug_import=='y') {
			echo "<pre style='color:green;'><b>Tableau \$prof[$i]&nbsp;:</b>";
			print_r($prof[$i]);
			echo "</pre>";
		}

		$i++;
	}

	// Les $prof[$i]["disciplines"] ne sont pas utilisées sauf à titre informatif à l'affichage...
	// Les $prof[$i]["prof_princ"][$j]["code_structure"] peuvent être exploitées à ce niveau pour désigner les profs principaux.

	//========================================================

	// On commence par rendre inactifs tous les professeurs
	$req = mysql_query("UPDATE utilisateurs set etat='inactif' where statut = 'professeur'");

	// on efface la ligne "display_users" dans la table "setting" de façon à afficher tous les utilisateurs dans la page  /utilisateurs/index.php
	$req = mysql_query("DELETE from setting where NAME = 'display_users'");


	if(getSettingValue('auth_sso')=='lcs') {
		require_once("../lib/lcs.inc.php");
		$ds = connect_ldap($lcs_ldap_host,$lcs_ldap_port,"","");
	}

	echo "<p>Dans le tableau ci-dessous, les identifiants en rouge correspondent à des professeurs nouveaux dans la base GEPI. les identifiants en vert correspondent à des professeurs détectés dans les fichiers CSV mais déjà présents dans la base GEPI.<br /><br />Il est possible que certains professeurs ci-dessous, bien que figurant dans le fichier CSV, ne soient plus en exercice dans votre établissement cette année. C'est pourquoi il vous sera proposé en fin de procédure d'initialsation, un nettoyage de la base afin de supprimer ces données inutiles.</p>\n";
	echo "<table border='1' class='boireaus' cellpadding='2' cellspacing='2' summary='Tableau des professeurs'>\n";
	echo "<tr><th><p class=\"small\">Identifiant du professeur</p></th><th><p class=\"small\">Nom</p></th><th><p class=\"small\">Prénom</p></th><th>Mot de passe *</th></tr>\n";


	srand();

	$nb_reg_no = 0;

	$tab_nouveaux_profs=array();

	$info_pb_mdp="";

	$alt=1;
	for($k=0;$k<count($prof);$k++){
		//if(isset($prof[$k]["fonction"])) {
		//	if($prof[$k]["fonction"]=="ENS"){

		if(((isset($prof[$k]["fonction"]))&&($prof[$k]["fonction"]=="ENS"))||
			((!isset($prof[$k]["fonction"]))&&(isset($prof[$k]["nom_usage"]))&&(isset($prof[$k]["prenom"])))) {

				$civilite="M.";
				if(isset($prof[$k]["sexe"])) {
					if($prof[$k]["sexe"]=="2"){
						$civilite="Mme";
					}
					else{
						$civilite="M.";
					}
				}

				if(isset($prof[$k]["civilite"])) {
					switch($prof[$k]["civilite"]){
						case 1:
							$civilite="M.";
							break;
						case 2:
							$civilite="Mme";
							break;
						case 3:
							$civilite="Mlle";
							break;
					}
				}

				if($_POST['mode_mdp']=="alea") {
					$mdp=createRandomPassword();
				}
				elseif(!isset($prof[$k]["date_naissance"])) {
					// Cela peut arriver avec des personnes ajoutées dans STS par le principal
					// Elles peuvent apparaitre avec
					/*
						<INDIVIDU ID="3506" TYPE="local">
							<SEXE/>
							<CIVILITE>3</CIVILITE>
							<NOM_USAGE>ZETOFREY</NOM_USAGE>
							<NOM_PATRONYMIQUE/>
							<PRENOM>MELANIE</PRENOM>
						</INDIVIDU>
					*/
					$mdp=createRandomPassword();
					$info_pb_mdp.="<p style='color:red'>".$prof[$k]["nom_usage"]." ".casse_mot($prof[$k]["prenom"],'majf2')." n'a pas de date de naissance renseignée.<br />Son mot de passe est généré aléatoirement.</p>\n";
				}
				else{
					$date=str_replace("-","",$prof[$k]["date_naissance"]);
					$mdp=$date;
				}

				//echo $prof[$k]["nom_usage"].";".$prof[$k]["prenom"].";".$civi.";"."P".$prof[$k]["id"].";"."ENS".";".$date."<br />\n";
				//$chaine=$prof[$k]["nom_usage"].";".$prof[$k]["prenom"].";".$civi.";"."P".$prof[$k]["id"].";"."ENS".";".$mdp;


				$prenoms = explode(" ",$prof[$k]["prenom"]);
				$premier_prenom = $prenoms[0];
				$prenom_compose = '';
				if (isset($prenoms[1])) $prenom_compose = $prenoms[0]."-".$prenoms[1];

				$lcs_prof_en_erreur="n";
				if(getSettingValue('auth_sso')=='lcs') {
					$lcs_prof_en_erreur="y";
					$exist = 'no';
					if($prof[$k]["id"]!='') {
						$login_prof_gepi=get_lcs_login($prof[$k]["id"], 'professeur');
						//echo "get_lcs_login(".$prof[$k]["id"].", 'professeur')=".$login_prof_gepi."<br />";
						if($login_prof_gepi!='') {
							$lcs_prof_en_erreur="n";
							$sql="SELECT 1=1 FROM utilisateurs WHERE login='$login_prof_gepi';";
							$test_exist_prof=mysql_query($sql);
							if(mysql_num_rows($test_exist_prof)>0) {
								$exist = 'yes';
							}
							else {
								$exist = 'no';
							}
						}
						else {
							$lcs_prof_en_erreur="y";
						}
					}
				}
				else {
					// On effectue d'abord un test sur le NUMIND
					$sql="select login from utilisateurs where (
					numind='P".$prof[$k]["id"]."' and
					numind!='' and
					statut='professeur')";
					//echo "<tr><td>$sql</td></tr>";
					$test_exist = mysql_query($sql);
					$result_test = mysql_num_rows($test_exist);
					if ($result_test == 0) {
						// On tente ensuite une reconnaissance sur nom/prénom, si le test NUMIND a échoué
						$sql="select login from utilisateurs where (
						nom='".traitement_magic_quotes($prof[$k]["nom_usage"])."' and
						prenom = '".traitement_magic_quotes($premier_prenom)."' and
						statut='professeur')";
	
						// Pour debug:
						//echo "$sql<br />";
						$test_exist = mysql_query($sql);
						$result_test = mysql_num_rows($test_exist);
						if ($result_test == 0) {
							if ($prenom_compose != '') {
								$test_exist2 = mysql_query("select login from utilisateurs
								where (
								nom='".traitement_magic_quotes($prof[$k]["nom_usage"])."' and
								prenom = '".traitement_magic_quotes($prenom_compose)."' and
								statut='professeur'
								)");
								$result_test2 = mysql_num_rows($test_exist2);
								if ($result_test2 == 0) {
									$exist = 'no';
								} else {
									$exist = 'yes';
									$login_prof_gepi = mysql_result($test_exist2,0,'login');
								}
							} else {
								$exist = 'no';
							}
						} else {
							$exist = 'yes';
							$login_prof_gepi = mysql_result($test_exist,0,'login');
						}
					} else {
						$exist = 'yes';
						$login_prof_gepi = mysql_result($test_exist,0,'login');
					}
				}

				if($lcs_prof_en_erreur=="y") {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td><p><font color='red'>Non trouvé dans l'annuaire LDAP</font></p></td><td><p>".$prof[$k]["nom_usage"]."</p></td><td><p>".$premier_prenom."</p></td><td>&nbsp;</td></tr>\n";
				}
				/*
				elseif(getSettingValue('auth_sso')=='lcs') {
					if ($exist == 'no') {
						// On devrait récupérer nom, prénom,... du LDAP du LCS...
					}
					else {
					}
				}
				*/
				else {
					if ($exist == 'no') {
	
						// Aucun professeur ne porte le même nom dans la base GEPI. On va donc rentrer ce professeur dans la base
	
						$prof[$k]["prenom"]=traitement_magic_quotes(corriger_caracteres($prof[$k]["prenom"]));

						/*
						if ($_POST['login_gen_type'] == "name") {
							$temp1 = $prof[$k]["nom_usage"];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							//$temp1 = substr($temp1,0,8);
	
						} elseif ($_POST['login_gen_type'] == "name8") {
							$temp1 = $prof[$k]["nom_usage"];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							$temp1 = substr($temp1,0,8);
						} elseif ($_POST['login_gen_type'] == "fname8") {
							$temp1 = $prof[$k]["prenom"]{0} . $prof[$k]["nom_usage"];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							$temp1 = substr($temp1,0,8);
						} elseif ($_POST['login_gen_type'] == "fname19") {
							$temp1 = $prof[$k]["prenom"]{0} . $prof[$k]["nom_usage"];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							$temp1 = substr($temp1,0,19);
						} elseif ($_POST['login_gen_type'] == "firstdotname") {
							if ($prenom_compose != '') {
								$firstname = $prenom_compose;
							} else {
								$firstname = $premier_prenom;
							}
	
							$temp1 = $firstname . "." . $prof[$k]["nom_usage"];
							$temp1 = strtoupper($temp1);
	
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							//$temp1 = substr($temp1,0,19);
						} elseif ($_POST['login_gen_type'] == "firstdotname19") {
							if ($prenom_compose != '') {
								$firstname = $prenom_compose;
							} else {
								$firstname = $premier_prenom;
							}
	
							$temp1 = $firstname . "." . $prof[$k]["nom_usage"];
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							$temp1 = substr($temp1,0,19);
						} elseif ($_POST['login_gen_type'] == "namef8") {
							$temp1 =  substr($prof[$k]["nom_usage"],0,7) . $prof[$k]["prenom"]{0};
							$temp1 = strtoupper($temp1);
							$temp1 = preg_replace("/ /","", $temp1);
							$temp1 = preg_replace("/-/","_", $temp1);
							$temp1 = preg_replace("/'/","", $temp1);
							$temp1 = strtoupper(remplace_accents($temp1,"all"));
							//$temp1 = substr($temp1,0,8);
						} elseif ($_POST['login_gen_type'] == "lcs") {
							$nom = $prof[$k]["nom_usage"];
							$nom = strtolower($nom);
							if (preg_match("/\s/",$nom)) {
								$noms = preg_split("/\s/",$nom);
								$nom1 = $noms[0];
								if (strlen($noms[0]) < 4) {
									$nom1 .= "_". $noms[1];
									$separator = " ";
								} else {
									$separator = "-";
								}
							} else {
								$nom1 = $nom;
								$sn = ucfirst($nom);
							}
							$firstletter_nom = $nom1{0};
							$firstletter_nom = strtoupper($firstletter_nom);
							$prenom = $prof[$k]["prenom"];
							$prenom1 = $prof[$k]["prenom"]{0};
							$temp1 = $prenom1 . $nom1;
							$temp1 = remplace_accents($temp1,"all");
						}
						else
						*/
						if($_POST['login_gen_type'] == 'ent'){
	
							if (getSettingValue("use_ent") == "y") {
								// Charge à l'organisme utilisateur de pourvoir à cette fonctionnalité
								// le code suivant n'est qu'une méthode proposée pour relier Gepi à un ENT
								$bx = 'oui';
								if (isset($bx) AND $bx == 'oui') {
									// On va chercher le login de l'utilisateur dans la table créée
									$sql_p = "SELECT login_u FROM ldap_bx
												WHERE nom_u = '".strtoupper($prof[$k]["nom_usage"])."'
												AND prenom_u = '".strtoupper($prof[$k]["prenom"])."'
												AND statut_u = 'teacher'";
									$query_p = mysql_query($sql_p);
									$nbre = mysql_num_rows($query_p);
									if ($nbre >= 1 AND $nbre < 2) {
										$temp1 = mysql_result($query_p, 0,"login_u");
									}else{
										// Il faudrait alors proposer une alternative à ce cas
										$temp1 = "erreur_".$k;
									}
								}
							}
							else{
								die('Vous n\'avez pas autorisé Gepi à utiliser un ENT');
							}
						}
						else {
							$temp1=generate_unique_login($prof[$k]["nom_usage"], $prof[$k]["prenom"], $_POST['login_gen_type']);
						}

						if(getSettingValue('auth_sso')=='lcs') {
							// On ne devrait jamais arriver là.
							$login_prof=$login_prof_gepi;
						}
						else {
							$login_prof = $temp1;
							//$login_prof = remplace_accents($temp1,"all");
							// On teste l'unicité du login que l'on vient de créer
							$m = 2;
							$test_unicite = 'no';
							$temp = $login_prof;
							while ($test_unicite != 'yes') {
								$test_unicite = test_unique_login($login_prof);
		
								if ($test_unicite != 'yes') {
									$login_prof = $temp.$m;
									$m++;
								}
							}
						}
						$prof[$k]["nom_usage"] = traitement_magic_quotes(corriger_caracteres($prof[$k]["nom_usage"]));
						// Mot de passe et change_mdp
	
						$changemdp = 'y';
	
						//echo "<tr><td colspan='4'>strlen($affiche[5])=".strlen($affiche[5])."<br />\$affiche[4]=$affiche[4]<br />\$_POST['sso']=".$_POST['sso']."</td></tr>";
						if(getSettingValue('auth_sso')=="lcs") {
							$pwd = '';
							$mess_mdp = "aucun (sso)";
							$changemdp = 'n';
						}
						elseif (strlen($mdp)>2 and (!isset($prof[$k]["fonction"]) or $prof[$k]["fonction"]=="ENS") and $_POST['sso'] == "no") {
							//
							$pwd = md5(trim($mdp));
							//$mess_mdp = "NUMEN";
							if($_POST['mode_mdp']=='alea'){
								$mess_mdp = "$mdp";
							}
							elseif(!isset($prof[$k]["date_naissance"])) {
								$mess_mdp = "$mdp";
							}
							else{
								$mess_mdp = "Mot de passe d'après la date de naissance";
							}
							//echo "<tr><td colspan='4'>NUMEN: $affiche[5] $pwd</td></tr>";
						} elseif ($_POST['sso']== "no") {
							$pwd = md5(rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9));
							$mess_mdp = $pwd;
							//echo "<tr><td colspan='4'>Choix 2: $pwd</td></tr>";
							// $mess_mdp = "Inconnu (compte bloqué)";
						} elseif ($_POST['sso'] == "yes") {
							$pwd = '';
							$mess_mdp = "aucun (sso)";
							$changemdp = 'n';
							//echo "<tr><td colspan='4'>sso</td></tr>";
						}
	
						// utilise le prénom composé s'il existe, plutôt que le premier prénom
	
						$sql="INSERT INTO utilisateurs SET login='$login_prof', nom='".$prof[$k]["nom_usage"]."', prenom='$premier_prenom', civilite='$civilite', password='$pwd', statut='professeur', etat='actif', change_mdp='".$changemdp."', numind='P".$prof[$k]["id"]."'";
						if(getSettingValue('auth_sso')=='lcs') {
							$sql.=", auth_mode='sso'";
						}
						$res = mysql_query($sql);
						// Pour debug:
						//echo "<tr><td colspan='4'>$sql</td></tr>";
	
						$tab_nouveaux_profs[]="$login_prof|$mess_mdp";
	
						if(!$res){$nb_reg_no++;}
						$res = mysql_query("INSERT INTO tempo2 VALUES ('".$login_prof."', '"."P".$prof[$k]["id"]."')");
	
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td><p><font color='red'>".$login_prof."</font></p></td><td><p>".$prof[$k]["nom_usage"]."</p></td><td><p>".$premier_prenom."</p></td><td>".$mess_mdp."</td></tr>\n";
					} else {
						// On corrige aussi les nom/prénom/civilité et numind parce que la reconnaissance a aussi pu se faire sur le nom/prénom
						$sql="UPDATE utilisateurs set etat='actif', nom='".$prof[$k]["nom_usage"]."', prenom='$premier_prenom', civilite='$civilite', numind='P".$prof[$k]["id"]."'";
						if(getSettingValue('auth_sso')=='lcs') {
							$sql.=", auth_mode='sso'";
						}
						$sql.=" where login = '".$login_prof_gepi."';";
						$res = mysql_query($sql);

						if(!$res) $nb_reg_no++;
						$res = mysql_query("INSERT INTO tempo2 VALUES ('".$login_prof_gepi."', '"."P".$prof[$k]["id"]."')");
	
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td><p><font color='green'>".$login_prof_gepi."</font></p></td><td><p>".$prof[$k]["nom_usage"]."</p></td><td><p>".$prof[$k]["prenom"]."</p></td><td>Inchangé</td></tr>\n";
					}
				}
			//}
		}
	}
	echo "</table>\n";


	if((isset($tab_nouveaux_profs))&&(count($tab_nouveaux_profs)>0)) {
		echo "<form action='../utilisateurs/impression_bienvenue.php' method='post' target='_blank'>\n";
		echo "<p>Imprimer les fiches bienvenue pour les nouveaux professeurs&nbsp;: \n";
		for($i=0;$i<count($tab_nouveaux_profs);$i++) {
			$tmp_tab=explode('|',$tab_nouveaux_profs[$i]);
			echo "<input type='hidden' name='user_login[]' value='$tmp_tab[0]' />\n";
			echo "<input type='hidden' name='mot_de_passe[]' value='$tmp_tab[1]' />\n";
		}
		echo "<input type='submit' value='Imprimer' /></p>\n";
		echo "</form>\n";
	}

	if((isset($info_pb_mdp))&&($info_pb_mdp!="")) {
		echo $info_pb_mdp;
	}

	if ($nb_reg_no != 0) {
		echo "<p>Lors de l'enregistrement des données il y a eu <span style='color:red;'>$nb_reg_no erreurs</span>. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.\n";
	}
	else {
		echo "<p>L'importation des professeurs dans la base GEPI a été effectuée avec succès !</p>\n";

		echo "<p><b>* Précision sur les mots de passe (<em>en non-SSO</em>) :</b></p>\n";
		echo "<ul>
		<li>Lorsqu'un nouveau professeur est inséré dans la base GEPI, son mot de passe lors de la première
		connexion à GEPI est celui choisi à l'étape précédente:<br />
			<ul>
			<li>Mot de passe daprès la date de naissance au format '<em>aaaammjj</em>', ou</li>
			<li>un mot de passe génèré aléatoirement par GEPI.<br />(<em>il est alors conseillé d'imprimer cette page</em>)</li>
			</ul>
		</ul>\n";
		if ($_POST['sso'] != "yes") {
			echo "<p><b>Dans tous les cas le nouvel utilisateur est amené à changer son mot de passe lors de sa première connexion.</b></p>\n";
		}
		echo "<br />\n<p>Vous pouvez procéder à la cinquième phase d'affectation des matières à chaque professeur, d'affectation des professeurs dans chaque classe et de définition des options suivies par les élèves.</p>\n";
	}


	// Création du f_div.csv pour l'import des profs principaux plus loin
	affiche_debug("Création du f_div.csv pour l'import des profs principaux lors d'une autre étape.<br />\n");
	$fich=fopen("../temp/$tempdir/f_div.csv","w+");
	$chaine="DIVCOD;NUMIND";
	if($fich){
		fwrite($fich,html_entity_decode_all_version($chaine)."\n");
	}
	affiche_debug($chaine."<br />\n");

	$tabchaine=array();
	for($m=0;$m<count($prof);$m++){
		if(isset($prof[$m]["prof_princ"])){
			for($n=0;$n<count($prof[$m]["prof_princ"]);$n++){
				$tabchaine[]=$prof[$m]["prof_princ"][$n]["code_structure"].";"."P".$prof[$m]["id"];
				//$chaine=$prof[$m]["prof_princ"][$n]["code_structure"].";"."P".$prof[$m]["id"];
				//if($fich){
				//	fwrite($fich,html_entity_decode_all_version($chaine)."\n");
				//}
				affiche_debug($chaine."<br />\n");
			}
		}
	}
	sort($tabchaine);
	for($i=0;$i<count($tabchaine);$i++){
		if($fich){
			fwrite($fich,html_entity_decode_all_version($tabchaine[$i])."\n");
		}
	}
	fclose($fich);


	if (getSettingValue("use_ent") == "y"){

		echo '<p style="text-align: center; font-weight: bold;"><a href="../mod_ent/gestion_ent_profs.php">Vérifier les logins avant de poursuivre</a></p>'."\n";

	} else {

		echo "<p>La création des enseignements peut se faire de trois façons différentes (<i>par ordre de préférence</i>)&nbsp;:</p>\n";

		echo "<ul>\n";
		echo "<li>\n";
		//  style="text-align: center; font-weight: bold;"
		echo "<p>";
		echo "Si votre emploi du temps est remonté vers STS, vous disposez d'un fichier <b>sts_emp_RNE_ANNEE.xml</b>&nbsp;:";
		echo "<br />";
		echo "<a href='prof_disc_classe_csv.php?a=a".add_token_in_url()."'>Procéder à la cinquième phase d'initialisation</a></p>\n";
		echo "</li>\n";

		echo "<li>\n";
		echo "<p>Si la remontée vers STS n'a pas encore été effectuée, vous pouvez effectuer l'initialisation des enseignements à partir d'un export CSV de UnDeuxTemps&nbsp;: <br /><a href='traite_csv_udt.php?a=a".add_token_in_url()."'>Procéder à la cinquième phase d'initialisation</a><br />(<i>procédure encore expérimentale... il se peut que vous ayez des groupes en trop</i>)</p>\n";
		echo "</li>\n";

		echo "<li>\n";
		echo "<p>Si vous n'avez pas non plus d'export CSV d'UnDeuxTemps&nbsp;: <br /><a href='init_alternatif.php?'>Initialisation alternative des enseignements</a><br />(<i>le mode le plus fastidieux</i>)</p>\n";
		echo "</li>\n";

		echo "</ul>\n";

	}

	echo "<p><br /></p>\n";
}

require("../lib/footer.inc.php");
?>