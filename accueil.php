<?php
/*
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Begin standart header
$titre_page = "Accueil GEPI";
$affiche_connexion = 'yes';
$niveau_arbo = 0;

// Initialisations files
require_once("./lib/initialisations.inc.php");


// On teste s'il y a une mise à jour de la base de données à effectuer
if (test_maj()) {
    header("Location: ./utilitaires/maj.php");
}

// Resume session
$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ./logout.php?auto=1");
    die();
}

// On vérifie si l'utilisation du cdt n'est pas unique
if (getSettingValue("use_only_cdt") == 'y' AND $_SESSION["statut"] == 'professeur'){
  $cdt = (getSettingValue("GepiCahierTexteVersion") == '2') ? '_2' : '';
  header("Location:cahier_texte".$cdt."/index.php");
}

// Sécurité
if (!checkAccess()) {
    header("Location: ./logout.php?auto=2");
    die();
}

unset ($_SESSION['order_by']);
$test_https = 'y'; // pour ne pas avoir à refaire le test si on a besoin de l'URL complète (rss)
if (!isset($_SERVER['HTTPS'])
    OR (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != "on")
    OR (isset($_SERVER['X-Forwaded-Proto']) AND $_SERVER['X-Forwaded-Proto'] != "https"))
{
	$test_https = 'n';
}


if($_SESSION['statut']=='professeur'){
	$accueil_simpl=isset($_GET['accueil_simpl']) ? $_GET['accueil_simpl'] : NULL;
	if(!isset($accueil_simpl)){
		$pref_accueil_simpl=getPref($_SESSION['login'],'accueil_simpl',"n");
		$accueil_simpl=$pref_accueil_simpl;
	}

	if($accueil_simpl=="y"){
		header("Location: ./accueil_simpl_prof.php");
	}
}
else{
	$accueil_simpl=NULL;
}

// End standart header
require_once("./lib/header.inc");

/*
$tmp_timeout=(getSettingValue("sessionMaxLength"))*60;

echo "<div id='decompte' style='float: right; border: 1px solid black;'></div>

<script type='text/javascript'>
cpt=".$tmp_timeout.";
compte_a_rebours='y';

function decompte(cpt){
	if(compte_a_rebours=='y'){
		document.getElementById('decompte').innerHTML=cpt;
		if(cpt>0){
			cpt--;
		}

		setTimeout(\"decompte(\"+cpt+\")\",1000);
	}
	else{
		document.getElementById('decompte').style.display='none';
	}
}

decompte(cpt);

</script>\n";
*/

	// Initialisation de $_SESSION["retour"]
$_SESSION["retour"] = "";

$tab[0] = "administrateur";
$tab[1] = "professeur";
$tab[2] = "cpe";
$tab[3] = "scolarite";
$tab[4] = "eleve";
$tab[5] = "secours";
$tab[6] = "responsable";

function acces($id,$statut) {
    $tab_id = explode("?",$id);
    $query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
    $droit = @mysql_result($query_droits, 0, $statut);
    if ($droit == "V") {
        return "1";
    } else {
        return "0";
    }
}


function affiche_ligne($chemin_,$titre_,$expli_,$tab,$statut_) {
    if (acces($chemin_,$statut_)==1)  {
        $temp = substr($chemin_,1);
        echo "<tr>\n";
	// Correction Regis ajout de \" pour encadrer $temp
        //echo "<td width=\"30%\" align=\"left\" style='border-right: none;'><a href=\"$temp\">$titre_</a>";
        echo "<td class='menu_gauche'><a href=\"$temp\">$titre_</a>";
        echo "</td>\n";
        //echo "<td align=\"left\">$expli_</td>\n";
        echo "<td class='menu_droit'>$expli_</td>\n";
        echo "</tr>\n";
    }
}


if ($_SESSION['statut'] == "administrateur") {
    echo "<div>\n";

    // Vérification et/ou changement du répertoire de backup
    if (!check_backup_directory()) {
        echo "<p style=\"color: red;\">Il y a eu un problème avec la mise à jour du répertoire de sauvegarde.<br/>\n";
        echo "Veuillez vérifier que le répertoire /backup de Gepi est accessible en écriture par le serveur (le serveur *uniquement* !)</p>\n";
    }

    // Vérification et/ou changement du répertoire temp
	/*
    if (!check_temp_directory()) {
        echo "<font color='red'>Il y a eu un problème avec la mise à jour du répertoire temp. \n";
        echo "Veuillez vérifier que le répertoire /temp de Gepi est accessible en écriture par le serveur (le serveur *uniquement* !)<br/>\n";
    }
	*/

    if (!check_user_temp_directory()) {
        echo "<p style=\"color: red;\">Il y a eu un problème avec la mise à jour du répertoire temp.<br/>\n";
		//if($_SESSION['statut']=='administrateur'){
			echo "Veuillez vérifier que le répertoire /temp de Gepi est accessible en écriture par le serveur (le serveur *uniquement* !)</p> \n";
		/*
		}
		else{
			echo "Veuillez contacter l'administrateur pour résoudre ce problème.<br/>\n";
			$_SESSION['user_temp_directory']='n';
		}
		*/
    }
	else{
		$_SESSION['user_temp_directory']='y';
	}

    if ((getSettingValue("disable_login"))!='no'){
		//echo "<br /><br />\n<font color=\"red\" size=\"+1\">Le site est en cours de maintenance et temporairement inaccessible.<br />Veuillez nous excuser de ce dérangement et réessayer de vous connecter ultérieurement.</font><br />\n";
		// correction Régis balise <center> invalide
		echo "<p style=\"text-align: center; color: red;\">Attention : le site est en cours de maintenance et temporairement inaccessible.</p>\n";
	}

    // * affichage du nombre de connecté *
    // compte le nombre d'enregistrement dans la table
    $sql = "select LOGIN from log where END > now()";
    $res = sql_query($sql);
    $nb_connect = sql_count($res);
	if(mysql_num_rows($res)>1) {
		$titre="Personnes connectées";

		$texte="<div align='center'>\n";
		$texte.="<table class='boireaus'>\n";
		$texte.="<tr>\n";
		$texte.="<th>Personne</th>\n";
		$texte.="<th>Statut</th>\n";
		$texte.="</tr>\n";
		$alt=1;
		while($lig_log=mysql_fetch_object($res)) {
			$sql="SELECT nom,prenom,statut,email FROM utilisateurs WHERE login='$lig_log->LOGIN';";
			$res_pers=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				$texte.="<tr><td style='color:red;'>$lig_log->LOGIN</td><td style='color:red;'>???</td></tr>\n";
			}
			else {
				$lig_pers=mysql_fetch_object($res_pers);
				$alt=$alt*(-1);
				$texte.="<tr class='lig$alt'><td>";
				if($lig_pers->email!="") {$texte.="<a href='mailto:$lig_pers->email'>";}
				$texte.=strtoupper($lig_pers->nom)." ".ucfirst(strtolower($lig_pers->prenom));
				if($lig_pers->email!="") {$texte.="</a>";}
				$texte.="</td><td>".$lig_pers->statut."</td></tr>\n";
			}
		}
		$texte.="</table>\n";
		$texte.="</div>\n";

		if($nb_connect>10) {
			$tabdiv_infobulle[]=creer_div_infobulle('personnes_connectees',$titre,"",$texte,"",20,10,'y','y','n','y');
		}
		else {
			$tabdiv_infobulle[]=creer_div_infobulle('personnes_connectees',$titre,"",$texte,"",20,0,'y','y','n','n');
		}

		echo "<p>\nNombre de personnes actuellement connectées :<a href='#' onClick='return false;' onMouseover=\"delais_afficher_div('personnes_connectees','y',-10,20,500,20,20);\"> $nb_connect </a>";
	}
	else {
		echo "<p>\nNombre de personnes actuellement connectées : $nb_connect ";
	}
    echo "(<a href = 'gestion/gestion_connect.php?mode_navig=accueil'>Gestion des connexions</a>)\n</p>\n";

	// Lien vers le panneau de contrôle de sécurité
	$alert_sums = mysql_result(mysql_query("SELECT SUM(niveau) FROM tentatives_intrusion WHERE (statut = 'new')"), 0);
	if (empty($alert_sums)) $alert_sums = "0";
	echo "<p>\nAlertes sécurité (niveaux cumulés) : $alert_sums (<a href='gestion/security_panel.php'>Panneau de contrôle</a>)</p>\n";

// christian : demande d'enregistrement
if ($force_ref) {
    ?><div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: #CFD7FF;  padding: 2px; margin-left: 60px; margin-right: 60px; margin-top: 2px; margin-bottom: 2px;  text-align: center; color: #1C1A8F; font-weight: bold;"><p>Votre établissement n'est pas référencé parmi les utilisateurs de Gepi.<br /><a href="javascript:ouvre_popup_reference('<?php echo($gepiPath); ?>/referencement.php?etape=explication')" title="Pourquoi est-ce utile ?">Pourquoi est-ce utile ?</a> / <a href="javascript:ouvre_popup_reference('<?php echo($gepiPath); ?>/referencement.php?etape=1')" title="Référencer votre établissement">Référencer votre établissement</a>.</p></div><?php
}
// fin christian demande d'enregistrement

    // Test du mode de connexion (http ou https) :
    // FIXME: Les deux lignes ci-dessous ne sont-elles pas inutiles ?
    $uri = $_SERVER['PHP_SELF'];
    $parsed_uri = parse_url($uri);

    if (
    	!isset($_SERVER['HTTPS'])
    	OR (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != "on")
    	OR (isset($_SERVER['X-Forwaded-Proto']) AND $_SERVER['X-Forwaded-Proto'] != "https")
    	) {
            echo "<p style=\"color: red;\">Connexion non sécurisée ! Vous *devez* accéder à Gepi en HTTPS (vérifiez la configuration de votre serveur web)</p>\n";
            $test_https = 'n';
    }

    if (ini_get("register_globals") == "1") {
            echo "<p style=\"color: red;\">PHP potentiellement mal configuré (register_globals=on)! Pour prévenir certaines failles de sécurité, vous *devez* configurer PHP avec le paramètre register_globals à off.</p>\n";
    }

    echo "</div>\n";
}
elseif(($_SESSION['statut']=="professeur")||($_SESSION['statut']=="scolarite")||($_SESSION['statut']=="cpe")||($_SESSION['statut']=="secours")){
    if (!check_user_temp_directory()) {
        echo "<div>\n";
		echo "<p style=\"color: red;\">Il y a eu un problème avec la mise à jour du répertoire temp.</p> \n";
		/*
		if($_SESSION['statut']=='administrateur'){
			echo "Veuillez vérifier que le répertoire /temp de Gepi est accessible en écriture par le serveur (le serveur *uniquement* !)<br/>\n";
		}
		else{
		*/
			echo "<p>Veuillez contacter l'administrateur pour résoudre ce problème.</p>\n";
			$_SESSION['user_temp_directory']='n';
		//}
        echo "</div>\n";
    }
	else{
		$_SESSION['user_temp_directory']='y';
	}
}

if($_SESSION['statut']=="professeur"){
	echo "<p class='bold'>\n";
	//echo "<a href='accueil_simpl_prof.php'>Interface simplifiée</a>";
	echo "<a href='accueil_simpl_prof.php'>Interface graphique</a>";
	//echo " | \n";
	echo "</p>\n";
}

// correction Régis balise <center> invalide, remplacée par des données dans table.menu dans style.css
//echo "<center>\n";

//Affichage des messages
$today=mktime(0,0,0,date("m"),date("d"),date("Y"));
$appel_messages = mysql_query("SELECT id, texte, date_debut, date_fin, date_decompte, auteur, destinataires FROM messages
    WHERE (
    texte != '' and
    date_debut <= '".$today."' and
    date_fin >= '".$today."'
    )
    order by id DESC");
$nb_messages = mysql_num_rows($appel_messages);
//echo "\$nb_messages=$nb_messages<br />";
$ind = 0;
$texte_messages = '';
$affiche_messages = 'no';
while ($ind < $nb_messages) {
    $destinataires1 = mysql_result($appel_messages, $ind, 'destinataires');
	/*
	echo "\$destinataires1=$destinataires1<br />";
	echo "\$_SESSION['statut']=".$_SESSION['statut']."<br />";
	echo "\substr(\$_SESSION['statut'], 0, 1)=".substr($_SESSION['statut'], 0, 1)."<br />";
	echo "strpos($destinataires1, substr(\$_SESSION['statut'], 0, 1))=".strpos($destinataires1, substr($_SESSION['statut'], 0, 1))."<br />";
	*/
    if (strpos($destinataires1, substr($_SESSION['statut'], 0, 1))) {
        if ($affiche_messages == 'yes') $texte_messages .= "<hr />";
        $affiche_messages = 'yes';
        $content = mysql_result($appel_messages, $ind, 'texte');
        // Mise en forme du texte
//        $auteur1 = mysql_result($appel_messages, $ind, 'auteur');
//        $nom_auteur = sql_query1("SELECT nom from utilisateurs where login = '".$auteur1."'");
//        $prenom_auteur = sql_query1("SELECT prenom from utilisateurs where login = '".$auteur1."'");
//        $texte_messages .= "<span class='small'>Message de </span>: ".$prenom_auteur." ".$nom_auteur;
		if(strstr($content, '_DECOMPTE_')) {
			$nb_sec=mysql_result($appel_messages, $ind, 'date_decompte')-mktime();
			if($nb_sec>0) {
				$decompte_remplace="";
			}
			elseif($nb_sec==0) {
				$decompte_remplace=" <span style='color:red'>Vous êtes à l'instant T</span> ";
			}
			else {
				$nb_sec=$nb_sec*(-1);
				$decompte_remplace=" <span style='color:red'>date dépassée de</span> ";
			}

			$decompte_j=floor($nb_sec/(24*3600));
			$decompte_h=floor(($nb_sec-$decompte_j*24*3600)/3600);
			$decompte_m=floor(($nb_sec-$decompte_j*24*3600-$decompte_h*3600)/60);

			if($decompte_j==1) {$decompte_remplace.=$decompte_j." jour ";}
			elseif($decompte_j>1) {$decompte_remplace.=$decompte_j." jours ";}

			if($decompte_h==1) {$decompte_remplace.=$decompte_h." heure ";}
			elseif($decompte_h>1) {$decompte_remplace.=$decompte_h." heures ";}

			if($decompte_m==1) {$decompte_remplace.=$decompte_m." minute";}
			elseif($decompte_m>1) {$decompte_remplace.=$decompte_m." minutes";}

			$content=my_ereg_replace("_DECOMPTE_",$decompte_remplace,$content);
		}
        $texte_messages .= $content;

    }
    $ind++;
}
// modification Régis : utilisation de div plutôt que de table pour la mise en page
if ($affiche_messages == 'yes') {
    echo "<div id='messagerie'>\n";
    echo "$texte_messages";
    echo "</div>\n";
}

/****************************
	Outils d'administration
****************************/

$chemin = array(
"/gestion/index.php",
"/accueil_admin.php",
"/accueil_modules.php"
);

$titre = array(
"Gestion générale",
"Gestion des bases",
"Gestion des modules"
);

$expli = array(
"Pour accéder aux outils de gestion (sécurité, configuration générale, bases de données, initialisation de GEPI).",
"Pour gérer les bases (établissements, utilisateurs, matières, classes, ".$gepiSettings['denomination_eleves'].", ".$gepiSettings['denomination_responsables'].", AIDs).",
"Pour gérer les modules (cahiers de textes, carnet de notes, absences, trombinoscope)."
);
if (getSettingValue('use_ent') == 'y') {
	// On ajoute les pages du module
	$chemin[]	= '/mod_ent/index.php';
	$titre[]	= 'Liaison ENT';
	$expli[]	= 'Entrer en liaison avec l\'ENT pour gérer les utilisateurs et récupérer les logins pour le sso';
}

$nb_ligne = count($chemin);



$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
	if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/configure.png' alt=''/> - Administration</h2>\n";
	//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
	echo "<table class='menu' summary=\"Outils d'administration. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
	//echo "<tr>\n";
	//echo "<th colspan='2'><img src='./images/icons/configure.png' alt='Admin' class='link'/> - Administration</th>\n";
	//echo "</tr>\n";
	// Affichage du bouton pour lancer une sauvegarde
	echo "<tr>";
	echo "<td colspan='2' style='text-align: center; padding: 10px;'>";
	echo "<form enctype=\"multipart/form-data\" action=\"gestion/accueil_sauve.php\" method=\"post\" id=\"formulaire\" >\n";
	if (getSettingValue("mode_sauvegarde") == "mysqldump") {
		echo "<p>\n<input type='hidden' name='action' value='system_dump' />";
	} else {
		echo "<input type='hidden' name='action' value='dump' />";
	}
	echo "<input type=\"submit\" value=\"Lancer une sauvegarde de la base de données\" /></p></form>\n";
	echo "<p class='small'>Les répertoires \"documents\" (<em>contenant les documents joints aux cahiers de textes</em>) et \"photos\" (<em>contenant les photos du trombinoscope</em>) ne seront pas sauvegardés.\n";
	echo "Un outil de sauvegarde spécifique se trouve en bas de la page <a href='./gestion/accueil_sauve.php#zip'>gestion des sauvegardes</a></p>\n";
	echo "</td></tr>";
	for ($i=0;$i<$nb_ligne;$i++) {
		affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
	}
	echo "</table>\n";
}

/********************************
	Fin outils d'administration
********************************/


/****************************************************************
	Outils de gestion des absences : module de Christian Chapel
*****************************************************************/

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")=='y') {
//
// Gestion Absences, dispenses, retards
//
    $chemin = array();
    $chemin[] = "/mod_absences/gestion/gestion_absences.php";
    $chemin[] = "/mod_absences/gestion/voir_absences_viescolaire.php";

    $titre = array();
    $titre[] = "Gestion Absences, dispenses, retards et infirmeries";
    $titre[] = "Visualiser les absences";

    $expli = array();
    $expli[] = "Cet outil vous permet de gérer les absences, dispenses, retards et autres bobos à l'infirmerie des ".$gepiSettings['denomination_eleves'].".";
    $expli[] = "Vous pouvez visualiser créneau par créneau la saisie des absences.";

    $nb_ligne = count($chemin);
    $affiche = 'no';
    for ($i=0;$i<$nb_ligne;$i++) {
        if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
    }
    if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/absences.png' alt=''/> - Gestion des retards et absences</h2>\n";
	//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
	echo "<table class='menu' summary=\"Outils de gestion des absences. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
		 // echo "<table class='menu' summary=\"Outils de gestion des absences\">\n";
         // echo "<tr>\n";
         // echo "<th colspan='2'><img src='./images/icons/absences.png' alt='Absences' class='link'/> - Gestion des retards et absences</th>\n";
         // echo "</tr>\n";
          for ($i=0;$i<$nb_ligne;$i++) {
            affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
        }
        echo "</table>\n";
    }
}

/********************************************************************
	Fin outils de gestion des absences : module de Christian Chapel
********************************************************************/


/************************************************************************************
	Outils de gestion des absences par les professeurs : module de Christian Chapel
************************************************************************************/

//On vérifie si le module est activé
if (getSettingValue("active_module_absence_professeur")=='y') {
//
// Gestion des ajout d'Absences par les professeurs
//
    $chemin = array();
    $chemin[] = "/mod_absences/professeurs/prof_ajout_abs.php";

    $titre = array();
    $titre[] = "Gestion des Absences par le ".$gepiSettings['denomination_professeur'];

    $expli = array();
    $expli[] = "Cet outil vous permet de gérer les absences durant vos cours.";

    $nb_ligne = count($chemin);
    $affiche = 'no';
    for ($i=0;$i<$nb_ligne;$i++) {
        if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
    }
    if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/absences.png' alt=''/> - Gestion des retards et absences</h2>\n";
	//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
	echo "<table class='menu' summary=\"Outils de gestion des absences. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
          //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
		  //echo "<table class='menu' summary=\"Outils de gestion des absences par les professeurs\">\n";
          //echo "<tr>\n";
          //echo "<th colspan='2'><img src='./images/icons/absences.png' alt='Absences' class='link'/> - Gestion des retards et absences</th>\n";
          //echo "</tr>\n";
          for ($i=0;$i<$nb_ligne;$i++) {
            affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
        }
        echo "</table>\n";
    }
}

/****************************************************************************************
	Fin outils de gestion des absences par les professeurs : module de Christian Chapel
****************************************************************************************/


/***********
	Saisie
***********/
// On teste si on l'utilisateur est un prof avec des matières. Si oui, on affiche les lignes relatives au cahier de textes et au carnet de notes
$test_prof_matiere = sql_count(sql_query("SELECT login FROM j_groupes_professeurs WHERE login = '".$_SESSION['login']."'"));
// On teste si le l'utilisateur est prof de suivi. Si oui on affiche la ligne relative remplissage de l'avis du conseil de classe
$test_prof_suivi = sql_count(sql_query("SELECT professeur FROM j_eleves_professeurs  WHERE professeur = '".$_SESSION['login']."'"));

$chemin = array();
$chemin[] = "/absences/index.php";
if ((($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) and (getSettingValue("active_cahiers_texte")=='y')) $chemin[] = "/cahier_texte/index.php";
if ((($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) and (getSettingValue("active_carnets_notes")=='y')) $chemin[] = "/cahier_notes/index.php";
if (($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) $chemin[] = "/saisie/index.php";
if ((($test_prof_suivi != "0") and (getSettingValue("GepiRubConseilProf")=='yes')) or (($_SESSION['statut']!='professeur') and (getSettingValue("GepiRubConseilScol")=='yes') ) or ($_SESSION['statut']=='secours')  ) $chemin[] = "/saisie/saisie_avis.php";
// Saisie ECTS - ne doit être affichée que si l'utilisateur a bien des classes ouvrant droit à ECTS
if ($_SESSION['statut'] == 'professeur') {
    $test_prof_ects = sql_count(sql_query("SELECT jgc.saisie_ects FROM j_groupes_classes jgc, j_eleves_professeurs jep, j_eleves_groupes jeg WHERE (jgc.saisie_ects = TRUE AND jgc.id_groupe = jeg.id_groupe AND jeg.login = jep.login AND jep.professeur = '".$_SESSION['login']."')"));
} else {
    $test_scol_ects = sql_count(sql_query("SELECT jgc.saisie_ects FROM j_groupes_classes jgc, j_scol_classes jsc WHERE (jgc.saisie_ects = TRUE AND jgc.id_classe = jsc.id_classe AND jsc.login = '".$_SESSION['login']."')"));
}
$conditions_ects = ($gepiSettings['active_mod_ects'] == 'y' and (($test_prof_suivi != "0" and $gepiSettings['GepiAccesSaisieEctsPP'] =='yes' and $test_prof_ects != "0")
      or ($_SESSION['statut']=='scolarite' and $gepiSettings['GepiAccesSaisieEctsScolarite'] =='yes' and $test_scol_ects != "0")
      or ($_SESSION['statut']=='secours')));
if ($conditions_ects) $chemin[] = "/mod_ects/index_saisie.php";


$titre = array();
$titre[] = "Gestion des absences";
if ((($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) and (getSettingValue("active_cahiers_texte")=='y')) $titre[] = "Cahier de textes";
if ((($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) and (getSettingValue("active_carnets_notes")=='y')) $titre[] = "Carnet de notes : saisie des notes";
if (($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) $titre[] = "Bulletin : saisie des moyennes et des appréciations par matière";
if ((($test_prof_suivi != "0") and (getSettingValue("GepiRubConseilProf")=='yes')) or (($_SESSION['statut']!='professeur') and (getSettingValue("GepiRubConseilScol")=='yes') ) or ($_SESSION['statut']=='secours')  ) $titre[] = "Bulletin : saisie des avis du conseil";
if ($conditions_ects) $titre[] = "Crédits ECTS";

$expli = array();
$expli[] = "Cet outil vous permet d'enregistrer les absences des ".$gepiSettings['denomination_eleves'].". Elles figureront sur le bulletin scolaire.";
if ((($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) and (getSettingValue("active_cahiers_texte")=='y')) $expli[] = "Cet outil vous permet de constituer un cahier de textes pour chacune de vos classes.";
if ((($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) and (getSettingValue("active_carnets_notes")=='y')) $expli[] = "Cet outil vous permet de constituer un carnet de notes pour chaque période et de saisir les notes de toutes vos évaluations.";
if (($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) $expli[] = "Cet outil permet de saisir directement, sans passer par le carnet de notes, les moyennes et les appréciations du bulletin";
if ((($test_prof_suivi != "0") and (getSettingValue("GepiRubConseilProf")=='yes')) or (($_SESSION['statut']!='professeur') and (getSettingValue("GepiRubConseilScol")=='yes') ) or ($_SESSION['statut']=='secours')  ) $expli[] = "Cet outil permet la saisie des avis du conseil de classe.";
if ($conditions_ects) $expli[] = "Saisie des crédits ECTS";

// Pour un professeur, on n'appelle que les aid qui sont sur un bulletin
$call_data = mysql_query("SELECT * FROM aid_config WHERE display_bulletin = 'y' OR bull_simplifie = 'y' ORDER BY nom");
$nb_aid = mysql_num_rows($call_data);
$i=0;
while ($i < $nb_aid) {
    $indice_aid = @mysql_result($call_data, $i, "indice_aid");
    $call_prof = mysql_query("SELECT * FROM j_aid_utilisateurs WHERE (id_utilisateur = '" . $_SESSION['login'] . "' and indice_aid = '$indice_aid')");
    $nb_result = mysql_num_rows($call_prof);
    if (($nb_result != 0) or ($_SESSION['statut'] == 'secours')) {
        $nom_aid = @mysql_result($call_data, $i, "nom");
        $chemin[] = "/saisie/saisie_aid.php?indice_aid=".$indice_aid;
        $titre[] = "Bulletin : saisie des appréciations $nom_aid";
        $expli[] = "Cet outil permet la saisie des appréciations des ".$gepiSettings['denomination_eleves']." pour les $nom_aid.";
    }
    $i++;
}


//==============================
// Pour permettre la saisie de commentaires-type, renseigner la variable $commentaires_types dans /lib/global.inc
// Et récupérer le paquet commentaires_types sur... ADRESSE A DEFINIR:
//if((file_exists('saisie/commentaires_types.php'))&&($commentaires_types=='y')){
if(file_exists('saisie/commentaires_types.php')) {
	/*
    //echo "AAAAAAAAAAAAAAA";
    if ((($_SESSION['statut']=='professeur') AND ((getSettingValue("GepiProfImprBul")!='yes') OR ((getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")!='yes')))) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")!='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")!='yes')))
    {
        // Pas d'accès au module;
    }
    else{
	*/
	//echo "SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['statut']."'<br />";
	//echo mysql_num_rows(mysql_query("SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."'"))."<br />";
    if ((($_SESSION['statut']=='professeur') AND (getSettingValue("CommentairesTypesPP")=='yes') AND (mysql_num_rows(mysql_query("SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."'"))>0))
		OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("CommentairesTypesScol")=='yes')))
    {
        //echo "BBBBBBBBBBB";
        $chemin[] = "/saisie/commentaires_types.php";
        $titre[] = "Saisie de commentaires-types";
        $expli[] = "Permet de définir des commentaires-types pour l'avis du conseil de classe.";
    }
}
/*
//==============================
if($_SESSION['statut']=='professeur') {
	// Le prof est-il PP
	$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_classes jec WHERE jep.professeur='".$_SESSION['login']."' AND jec.login=jep.login;";
	$res_classe=mysql_query($sql);
	if(mysql_num_rows($res_classe)>0) {
		$chemin[] = "/saisie/saisie_synthese_app_classe.php";
		$titre[] = "Saisie des synthèses de classes";
		$expli[] = "Permet de saisir une synthèse des appréciations effectuées par les professeurs sur le groupe classe.";
	}
}
elseif($_SESSION['statut']=='scolarite') {
	$sql="SELECT 1=1 FROM j_scol_classes WHERE login='".$_SESSION['login']."';";
	//echo "$sql<br />";
	$res_classe=mysql_query($sql);
	if(mysql_num_rows($res_classe)>0) {
		$chemin[] = "/saisie/saisie_synthese_app_classe.php";
		$titre[] = "Saisie des synthèses de classes";
		$expli[] = "Permet de saisir une synthèse des appréciations effectuées par les professeurs sur le groupe classe.";
	}
}
//==============================
*/
$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/configure.png' alt=''/> - Saisie</h2>\n";
	//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
	echo "<table class='menu' summary=\"Outils de Saisie. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
    //echo "<table class='menu' summary=\"Saisie\">\n";
    //echo "<tr>\n";
    //echo "<th colspan='2'><img src='./images/icons/saisie.png' alt='Saisie' class='link'/> - Saisie</th>\n";
    //echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}

/******************
	Fin de saisie
******************/


/**********************************************************************
	Outils de gestion des trombinoscopes : module de Christian Chapel
**********************************************************************/

//On vérifie si le module est activé
//if (getSettingValue("active_module_trombinoscopes")=='y') {
$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes");
$active_module_trombino_pers=getSettingValue("active_module_trombino_pers");
if (($active_module_trombinoscopes=='y')||($active_module_trombino_pers=='y')) {
//
// Visualisation des trombinoscopes
//
    $chemin = array();
    $chemin[] = "/mod_trombinoscopes/trombinoscopes.php";

    $titre = array();
    $titre[] = "Trombinoscopes";

    $expli = array();
    $expli[] = "Cet outil vous permet de visualiser les trombinoscopes des classes.";

    // On appelle les aid "trombinoscope"
    $call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid= '".getSettingValue("num_aid_trombinoscopes")."' ORDER BY nom");
    $nb_aid = mysql_num_rows($call_data);
    $i=0;
    while ($i < $nb_aid) {
        $indice_aid = @mysql_result($call_data, $i, "indice_aid");
        $call_prof = mysql_query("SELECT * FROM j_aid_utilisateurs_gest WHERE (id_utilisateur = '" . $_SESSION['login'] . "' and indice_aid = '$indice_aid')");
        $nb_result = mysql_num_rows($call_prof);
        if (($nb_result != 0) or ($_SESSION['statut'] == 'secours')) {
            $nom_aid = @mysql_result($call_data, $i, "nom");
            $chemin[] = "/aid/index2.php?indice_aid=".$indice_aid;
            $titre[] = $nom_aid;
            $expli[] = "Cet outil vous permet de visualiser quels ".$gepiSettings['denomination_eleves']." ont le droit d'envoyer/modifier leur photo.";
        }
        $i++;
    }

    $nb_ligne = count($chemin);
    $affiche = 'no';
    for ($i=0;$i<$nb_ligne;$i++) {
        if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
    }

	if(($_SESSION['statut']=='eleve')&&($affiche=="yes")) {
		$GepiAccesEleTrombiTousEleves=getSettingValue("GepiAccesEleTrombiTousEleves");
		$GepiAccesEleTrombiElevesClasse=getSettingValue("GepiAccesEleTrombiElevesClasse");
		$GepiAccesEleTrombiPersonnels=getSettingValue("GepiAccesEleTrombiPersonnels");
		$GepiAccesEleTrombiProfsClasse=getSettingValue("GepiAccesEleTrombiProfsClasse");

		if(($GepiAccesEleTrombiTousEleves!="yes")&&
		($GepiAccesEleTrombiElevesClasse!="yes")&&
		($GepiAccesEleTrombiPersonnels!="yes")&&
		($GepiAccesEleTrombiProfsClasse!="yes")) {
			$affiche = 'no';
		}
		else {
			// Au moins un des droits est donné aux élèves.
			$affiche = 'yes';

			if (($active_module_trombinoscopes!='y')&&($GepiAccesEleTrombiPersonnels!="yes")&&($GepiAccesEleTrombiProfsClasse!="yes")) {
				$affiche = 'no';
			}

			if (($active_module_trombino_pers!='y')&&($GepiAccesEleTrombiTousEleves!="yes")&&($GepiAccesEleTrombiElevesClasse!="yes")) {
				$affiche = 'no';
			}
		}
	}


    if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/configure.png' alt=''/> - Trombinoscope</h2>\n";
		//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
		echo "<table class='menu' summary=\"Outils de gestion des trombinoscopes. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
    	 // echo "<table class='menu' summary=\"Outils de gestion des trombinoscopes\">\n";
          //echo "<tr>\n";
          //echo "<th colspan='2'><img src='./images/icons/contact.png' alt='Trombi' class='link'/> - Trombinoscope</th>\n";
          //echo "</tr>\n";
          for ($i=0;$i<$nb_ligne;$i++) {
            affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
        }
        echo "</table>\n";
    }
}

/**********************************************************************
	Fin de gestion des trombinoscopes : module de Christian Chapel
**********************************************************************/


/******************************
	Outils de relevé de notes
******************************/
/* $test_prof_suivi doit être défini avant */

$condition = (
    (getSettingValue("active_carnets_notes")=='y')
    AND
        ((($_SESSION['statut'] == "scolarite") AND (getSettingValue("GepiAccesReleveScol") == "yes"))
        OR
        (
        ($_SESSION['statut'] == "professeur") AND
            (
            (getSettingValue("GepiAccesReleveProf") == "yes") OR
            (getSettingValue("GepiAccesReleveProfTousEleves") == "yes") OR
            (getSettingValue("GepiAccesReleveProfToutesClasses") == "yes") OR
            ((getSettingValue("GepiAccesReleveProfP") == "yes") AND ($test_prof_suivi != "0"))
            )
        )
        OR
        (($_SESSION['statut'] == "cpe") AND getSettingValue("GepiAccesReleveCpe") == "yes")));

$condition2 = ($_SESSION['statut'] != "professeur" OR
				(
				$_SESSION['statut'] == "professeur" AND
				(
	            	(getSettingValue("GepiAccesMoyennesProf") == "yes") OR
	            	(getSettingValue("GepiAccesMoyennesProfTousEleves") == "yes") OR
	            	(getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes")
				)
				)
			);

$chemin = array();
//if ($condition) $chemin[] = "/cahier_notes/visu_releve_notes.php";
if ($condition) $chemin[] = "/cahier_notes/visu_releve_notes_bis.php";

$titre = array();
if ($condition) $titre[] = "Visualisation et impression des relevés de notes";

$expli = array();
if ($condition) $expli[] = "Cet outil vous permet de visualiser à l'écran et d'imprimer les relevés de notes, ".$gepiSettings['denomination_eleve']." par ".$gepiSettings['denomination_eleve'].", classe par classe.";


if ($condition && $condition2) $chemin[] = "/cahier_notes/index2.php";
if ($condition && $condition2) $titre[] = "Visualisation des moyennes des carnets de notes";
if ($condition && $condition2) $expli[] = "Cet outil vous permet de visualiser à l'écran les moyennes calculées d'après le contenu des carnets de notes, indépendamment de la saisie des moyennes sur les bulletins.";


$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/releve.png' alt=''/> - Relevés de notes</h2>\n";
	//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
	echo "<table class='menu' summary=\"Outils de relevés de notes. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
    //echo "<table class='menu' summary=\"Outils de relevés de notes\">\n";
    //echo "<tr>\n";
    //echo "<th colspan='2'><img src='./images/icons/releve.png' alt='Relevés' class='link'/> - Relevés de notes</th>\n";
    //echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}

/**********************************
	Fin outils de relevé de note$
**********************************/


/******************************
	Outils de relevé ECTS
******************************/
/* $test_prof_suivi et $test_prof_ects doivent être défini avant */

$condition = ($gepiSettings['active_mod_ects'] == 'y' and ((($test_prof_suivi != "0") and ($gepiSettings['GepiAccesEditionDocsEctsPP'] =='yes') and $test_prof_ects != "0")
                or (($_SESSION['statut']=='scolarite') and ($gepiSettings['GepiAccesEditionDocsEctsScolarite'] =='yes') and $test_scol_ects != "0")
                or ($_SESSION['statut']=='secours')  ));

$chemin = array();
if ($condition) $chemin[] = '/mod_ects/edition.php';

$titre = array();
if ($condition) $titre[] = 'Génération des documents ECTS';

$expli = array();
if ($condition) $expli[] = 'Cet outil vous permet de générer les documents ECTS (relevé, attestation, annexe) pour les classes concernées.';


$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/releve.png' alt=''/> - Documents ECTS</h2>\n";
	echo "<table class='menu' summary=\"Outils de relevés ECTS. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}

/**********************************
	Fin outils de relevé ECTS
**********************************/



/********************
	Emploi du temps
********************/

$chemin = array();
$chemin[] = "/edt_organisation/index_edt.php";
if ($_SESSION["statut"] == 'responsable') {
	// on propose l'edt d'un élève, les autres enfants seront disponibles dans la page de l'edt.
	$tab_tmp_ele = get_enfants_from_resp_login($_SESSION['login']);
	$chemin[] = "/edt_organisation/edt_eleve.php?login_edt=" . $tab_tmp_ele[0];
}else{
	$chemin[] = "/edt_organisation/edt_eleve.php";
}

$titre = array();
$titre[] = "Emploi du temps";
$titre[] = "Emploi du temps";

$expli = array();
$expli[] = "Cet outil permet la consultation/gestion de l'emploi du temps.";
$expli[] = "Cet outil permet la consultation de votre emploi du temps.";

$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
	// Ajout d'un test param_edt() pour savoir si l'admin a activé ou non le module EdT - Calendrier
if ($affiche=='yes' AND param_edt($_SESSION["statut"]) == 'yes') {

	echo "<h2 class='accueil'><img src='./images/icons/document.png' alt=''/> - Emploi du temps</h2>\n";
	echo "<table class='menu' summary=\"Module Emploi du temps. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";

    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}

/************************
	fin emploi du temps
************************/


/**************************************************************
	Outils destinés essentiellement aux parents et aux élèves
**************************************************************/

$chemin = array();
$titre = array();
$expli = array();

// Cahier de textes
$condition = (
	getSettingValue("active_cahiers_texte")=='y' AND (
		($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesCahierTexteParent") == 'yes')
		OR ($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesCahierTexteEleve") == 'yes')
	));
if ($condition) {
    $chemin[] = "/cahier_texte/consultation.php";
    $titre[] = "Cahier de textes";
    if ($_SESSION['statut'] == "responsable") {
    	$expli[] = "Permet de consulter les compte-rendus de séance et les devoirs à faire pour les ".$gepiSettings['denomination_eleves']." dont vous êtes le ".$gepiSettings['denomination_responsable'].".";
    } else {
    	$expli[] = "Permet de consulter les compte-rendus de séance et les devoirs à faire pour les enseignements que vous suivez.";
    }
}

// Relevés de notes
$condition = (
		getSettingValue("active_carnets_notes")=='y' AND (
			($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesReleveParent") == 'yes')
			OR ($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesReleveEleve") == 'yes')
			));
if ($condition) {
    //$chemin[] = "/cahier_notes/visu_releve_notes.php";
    $chemin[] = "/cahier_notes/visu_releve_notes_bis.php";
    $titre[] = "Relevés de notes";
    if ($_SESSION['statut'] == "responsable") {
    	$expli[] = "Permet de consulter les relevés de notes des ".$gepiSettings['denomination_eleves']." dont vous êtes le ".$gepiSettings['denomination_responsable'].".";
    } else {
    	$expli[] = "Permet de consulter vos relevés de notes détaillés.";
    }
}

// Equipes pédagogiques
$condition = (
			($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesEquipePedaParent") == 'yes')
			OR ($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesEquipePedaEleve") == 'yes')
			);
if ($condition) {
    $chemin[] = "/groupes/visu_profs_eleve.php";
    $titre[] = "Equipe pédagogique";
    if ($_SESSION['statut'] == "responsable") {
    	$expli[] = "Permet de consulter l'équipe pédagogique des ".$gepiSettings['denomination_eleves']." dont vous êtes ".$gepiSettings['denomination_responsable'].".";
    } else {
    	$expli[] = "Permet de consulter l'équipe pédagogique qui vous concerne.";
    }
}

// Bulletins simplifiés
$condition = (
			($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesBulletinSimpleParent") == 'yes')
			OR ($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesBulletinSimpleEleve") == 'yes')
			);
if ($condition) {
    $chemin[] = "/prepa_conseil/index3.php";
    $titre[] = "Bulletins simplifiés";
    if ($_SESSION['statut'] == "responsable") {
    	$expli[] = "Permet de consulter les bulletins simplifiés des ".$gepiSettings['denomination_eleves']." dont vous êtes ".$gepiSettings['denomination_responsable'].".";
    } else {
    	$expli[] = "Permet de consulter vos bulletins sous forme simplifiée.";
    }
}

// Graphiques
$condition = (
			($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesGraphParent") == 'yes')
			OR ($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesGraphEleve") == 'yes')
			);
if ($condition) {
    $chemin[] = "/visualisation/affiche_eleve.php";
    $titre[] = "Visualisation graphique";
    if ($_SESSION['statut'] == "responsable") {
    	$expli[] = "Permet de visualiser sous forme graphique les résultats des ".$gepiSettings['denomination_eleves']." dont vous êtes ".$gepiSettings['denomination_responsable'].", par rapport à la classe.";
    } else {
    	$expli[] = "Permet de consulter vos résultats sous forme graphique, comparés à la classe.";
    }
}

// les absences
$conditions3 = ($_SESSION['statut'] == "responsable" AND
				getSettingValue("active_module_absence") == 'y' AND
				getSettingValue("active_absences_parents") == 'y');
if ($conditions3) {
	$chemin[] = "/mod_absences/absences.php";
	$titre[] = "Absences";
	$expli[] = "Permet de suivre les absences et les retards des &eacute;l&egrave;ves dont je suis ".$gepiSettings['denomination_responsable'];
}

$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/vie_privee.png' alt='' /> - Consultation</h2>\n";
	echo "<table class='menu' summary=\"Outils de consultation. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";

    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}

/******************************************************************
	Fin outils destinés essentiellement aux parents et aux élèves
******************************************************************/


/**********************************************
// Outils complémentaires de gestion des AID
**********************************************/

// Y-a-t-il des AIDs pour lesquelles les outils complémetaires sont activés ?

// Dans le cas des élèves, on n'affiche que les AID dans lesquelles ils sont inscrits
function AfficheAid($_statut,$_login,$indice_aid){
    if ($_statut == "eleve") {
        $test = sql_query1("select count(login) from j_aid_eleves where login='".$_login."' and indice_aid='".$indice_aid."' ");
        if ($test == 0)
            return false;
        else
            return true;
    } else
        return true;
}

$call_data = sql_query("select indice_aid, nom from aid_config WHERE outils_complementaires = 'y' order by nom_complet");
$nb_aid = mysql_num_rows($call_data);
$call_data2 = sql_query("select id from archivage_types_aid WHERE outils_complementaires = 'y'");
$nb_aid_annees_anterieures = mysql_num_rows($call_data2);
$nb_total=$nb_aid+$nb_aid_annees_anterieures;

if ($nb_total != 0) {
    $chemin = array();
    $titre = array();
    $expli = array();
    $i = 0;
    while ($i<$nb_aid) {
        $indice_aid = mysql_result($call_data,$i,"indice_aid");
        $_indice_aid[] = mysql_result($call_data,$i,"indice_aid");
        $nom_aid = mysql_result($call_data,$i,"nom");
        $chemin[]="/aid/index_fiches.php?indice_aid=".$indice_aid;
        $titre[] = $nom_aid;
        $expli[] = "Tableau récapitulatif, liste des ".$gepiSettings['denomination_eleves'].", ...";
        $i++;
    }
  $nb_ligne = count($chemin);
  $affiche = 'no';
  for ($i=0;$i<$nb_ligne;$i++) {
      if ((acces($chemin[$i],$_SESSION['statut'])==1) and AfficheAid($_SESSION['statut'],$_SESSION['login'],$_indice_aid[$i]))  {$affiche = 'yes';}
  }
  if (($nb_aid_annees_anterieures > 0) and (acces("/aid/annees_anterieures_accueil.php",$_SESSION['statut'])==1))
    $affiche = 'yes';

  if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/document.png' alt=''/> - Outils de visualisation et d'édition des fiches projets</h2>\n";
	//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
	echo "<table class='menu' summary=\"Outils complémentaires de gestion des AID. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
      //echo "<table class='menu' summary=\"Outils complémentaires de gestion des AID\">\n";
      //echo "<tr>\n";
      //echo "<th colspan='2'><img src='./images/icons/document.png' alt='Outils complémentaires' class='link'/> - Outils de visualisation et d'édition des fiches projets</th>\n";
      //echo "</tr>\n";
      for ($i=0;$i<$nb_ligne;$i++) {
          if (AfficheAid($_SESSION['statut'],$_SESSION['login'],$_indice_aid[$i]))
              affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
      }
      if (($nb_aid_annees_anterieures > 0) and (acces("/aid/annees_anterieures_accueil.php",$_SESSION['statut'])==1)) {
        $chemin_="/aid/annees_anterieures_accueil.php";
        $titre_ = "Fiches projets des années antérieures";
        $expli_ = "Accès administrateur aux fiches projets des années antérieures";
        affiche_ligne($chemin_,$titre_,$expli_,$tab,$_SESSION['statut']);
      }
      echo "</table>";
  }
}

/**************************************************
	Fin outils complémentaires de gestion des AID
**************************************************/


/**********************************************
	Outils de gestion des Bulletins scolaires
**********************************************/

// On teste si on l'utilisateur est un prof avec des matières. Si oui, on affiche les lignes relatives au cahier de textes et au carnet de notes
$test_prof_matiere = sql_count(sql_query("SELECT login FROM j_groupes_professeurs WHERE login = '".$_SESSION['login']."'"));
// On teste si le l'utilisateur est prof de suivi. Si oui on affiche la ligne relative remplissage de l'avis du conseil de classe
$test_prof_suivi = sql_count(sql_query("SELECT professeur FROM j_eleves_professeurs  WHERE professeur = '".$_SESSION['login']."'"));


$chemin = array();
if ((($test_prof_suivi != "0") and (getSettingValue("GepiProfImprBul")=='yes')) or ($_SESSION['statut']!='professeur'))
{$chemin[] = "/bulletin/verif_bulletins.php"; }
if ($_SESSION['statut']!='professeur')
{$chemin[] = "/bulletin/verrouillage.php"; }

//==========================================================
// AJOUT: boireaus 20080219
//        Dispositif de restriction des accès aux appréciations pour les comptes responsables/eleves

//        Sur quel droit s'appuyer pour donner l'accès?
//            GepiAccesRestrAccesAppProfP : peut saisir les avis du conseil de classe pour sa classe
if ((($test_prof_suivi != "0") and ($_SESSION['statut']=='professeur') AND (getSettingValue("GepiAccesRestrAccesAppProfP")=='yes')) OR ($_SESSION['statut']=='scolarite') OR ($_SESSION['statut']=='administrateur'))
{ $chemin[] = "/classes/acces_appreciations.php"; }
//==========================================================

if ((($test_prof_suivi != "0") and ($_SESSION['statut']=='professeur') AND (getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")=='yes')) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")=='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")=='yes')))
{ $chemin[] = "/bulletin/param_bull.php"; }
if ($_SESSION['statut']=='scolarite')
{ $chemin[] = "/responsables/index.php"; }
if ($_SESSION['statut']=='scolarite')
{ $chemin[] = "/eleves/index.php"; }
if ((($test_prof_suivi != "0") and (getSettingValue("GepiProfImprBul")=='yes')) or ($_SESSION['statut']!='professeur'))
{ $chemin[] = "/bulletin/bull_index.php";}

$titre = array();
if ((($test_prof_suivi != "0") and (getSettingValue("GepiProfImprBul")=='yes')) or ($_SESSION['statut']!='professeur'))
{ $titre[] = "Outil de vérification";}
if ($_SESSION['statut']!='professeur')
{ $titre[] = "Verrouillage/Déverrouillage des périodes";}

//==========================================================
// AJOUT: boireaus 20080219
//        Dispositif de restriction des accès aux appréciations pour les comptes responsables/eleves

//        Sur quel droit s'appuyer pour donner l'accès?
//            GepiAccesRestrAccesAppProfP : peut saisir les avis du conseil de classe pour sa classe
if ((($test_prof_suivi != "0") and ($_SESSION['statut']=='professeur') AND (getSettingValue("GepiAccesRestrAccesAppProfP")=='yes')) OR ($_SESSION['statut']=='scolarite') OR ($_SESSION['statut']=='administrateur'))
{ $titre[] = "Accès des ".$gepiSettings['denomination_eleves']." et ".$gepiSettings['denomination_responsables']." aux appreciations"; }
//==========================================================

if ((($test_prof_suivi != "0") and ($_SESSION['statut']=='professeur') AND (getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")=='yes')) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")=='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")=='yes')))
{ $titre[] = "Paramètres d'impression des bulletins";}
if ($_SESSION['statut']=='scolarite')
{ $titre[] = "Gestion des fiches ".$gepiSettings['denomination_responsables'];}
if ($_SESSION['statut']=='scolarite')
{ $titre[] = "Gestion des fiches ".$gepiSettings['denomination_eleves'];}
if ((($test_prof_suivi != "0") and (getSettingValue("GepiProfImprBul")=='yes')) or ($_SESSION['statut']!='professeur'))
{ $titre[] = "Visualisation et impression des bulletins";}

$expli = array();
if ((($test_prof_suivi != "0") and (getSettingValue("GepiProfImprBul")=='yes')) or ($_SESSION['statut']!='professeur'))
{$expli[] = "Permet de vérifier si toutes les rubriques des bulletins sont remplies.";}
if ($_SESSION['statut']!='professeur')
{ $expli[] = "Permet de verrouiller ou déverrouiller une période pour une ou plusieurs classes.";}

//==========================================================
// AJOUT: boireaus 20080219
//        Dispositif de restriction des accès aux appréciations pour les comptes responsables/eleves

//        Sur quel droit s'appuyer pour donner l'accès?
//            GepiAccesRestrAccesAppProfP : peut saisir les avis du conseil de classe pour sa classe
if ((($test_prof_suivi != "0") and ($_SESSION['statut']=='professeur') AND (getSettingValue("GepiAccesRestrAccesAppProfP")=='yes')) OR ($_SESSION['statut']=='scolarite') OR ($_SESSION['statut']=='administrateur'))
{ $expli[] = "Permet de définir quand les comptes ".$gepiSettings['denomination_eleves']." et ".$gepiSettings['denomination_responsables']." (s'ils existent) peuvent accéder aux appreciations des ".$gepiSettings['denomination_professeurs']." sur le bulletin et avis du conseil de classe."; }
//==========================================================

if ((($test_prof_suivi != "0") and ($_SESSION['statut']=='professeur') AND (getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")=='yes')) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")=='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")=='yes')))
{ $expli[] = "Permet de modifier les paramètres de mise en page et d'impression des bulletins.";}
if ($_SESSION['statut']=='scolarite')
{ $expli[] = "Cet outil vous permet de modifier/supprimer/ajouter des fiches de ".$gepiSettings['denomination_responsables']." des ".$gepiSettings['denomination_eleves'].".";}
if ($_SESSION['statut']=='scolarite')
{ $expli[] = "Cet outil vous permet de modifier/supprimer/ajouter des fiches ".$gepiSettings['denomination_eleves'].".";}
if ((($test_prof_suivi != "0") and (getSettingValue("GepiProfImprBul")=='yes')) or ($_SESSION['statut']!='professeur'))
{ $expli[] = "Cet outil vous permet de visualiser à l'écran et d'imprimer les bulletins, classe par classe.";}

$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
    //else{echo "$chemin[$i] refusé<br />";}
}
if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/document.png' alt=''/> - Bulletins scolaires</h2>\n";
	//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
	echo "<table class='menu' summary=\"Bulletins scolaires. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
    //echo "<table class='menu' summary=\"Outils de gestion\">\n";
    //echo "<tr>\n";
    //echo "<th colspan='2'><img src='./images/icons/document.png' alt='Bulletins' class='link'/> - Bulletins scolaires</th>\n";
    //echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}

/*******************************************
	Fin de gestion des Bulletins scolaires
*******************************************/


/*************************************
// Accès aux modules propres au LPI
*************************************/
if (file_exists("./lpi/accueil.php")) require("./lpi/accueil.php");

/*************************************
	Fin accès aux modules propres au LPI
*************************************/


/*******************************
	Visualisation / Impression
*******************************/
/*$test_prof_suivi et $test_prof_matiere  doivent être définis avant*/

$conditions_moyennes = (
        ($_SESSION['statut'] != "professeur")
        OR
        (
        ($_SESSION['statut'] == "professeur") AND
            (
            (getSettingValue("GepiAccesMoyennesProf") == "yes") OR
            (getSettingValue("GepiAccesMoyennesProfTousEleves") == "yes") OR
            (getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes")
            )
        )
        );
$conditions_bulsimples = (
        	(
	        ($_SESSION['statut'] != "eleve") AND ($_SESSION['statut'] != "responsable")
        	)
        AND
        (
        ($_SESSION['statut'] != "professeur") OR
        (
	    	($_SESSION['statut'] == "professeur") AND
	            (
	            (getSettingValue("GepiAccesBulletinSimpleProf") == "yes") OR
	            (getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes") OR
	            (getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") == "yes")
	            )
        	)
        )
        );
$chemin = array();
//===========================
// AJOUT:boireaus
$chemin[] = "/groupes/visu_profs_class.php";
$chemin[] = "/eleves/visu_eleve.php";
$chemin[] = "/impression/impression_serie.php";
if(($_SESSION['statut']=='scolarite')||(($_SESSION['statut']=='professeur') and ($test_prof_suivi != "0"))){
	$chemin[] = "/saisie/impression_avis.php";
}
if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='professeur')||($_SESSION['statut']=='cpe')){
	$chemin[] = "/groupes/mes_listes.php";
}
//===========================
$chemin[] = "/visualisation/index.php";
if (($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) $chemin[] = "/prepa_conseil/index1.php";
if ($conditions_moyennes) $chemin[] = "/prepa_conseil/index2.php";
if ($conditions_bulsimples) {
	$chemin[] = "/prepa_conseil/index3.php";
}
elseif(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesBulletinSimplePP")=="yes")) {
	$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."';";
	$test_pp=mysql_num_rows(mysql_query($sql));
	if($test_pp>0) {
		$chemin[] = "/prepa_conseil/index3.php";
	}
}

$titre = array();
//===========================
// AJOUT:boireaus
$titre[] = "Visualisation des équipes pédagogiques";
$titre[] = "Consultation d'un ".$gepiSettings['denomination_eleve'];
$titre[] = "Impression PDF de listes";
if(($_SESSION['statut']=='scolarite')||(($_SESSION['statut']=='professeur') and ($test_prof_suivi != "0"))){
	$titre[] = "Impression PDF des avis du conseil de classe";
}
if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='professeur')||($_SESSION['statut']=='cpe')){
	$titre[] = "Exporter mes listes";
}
//===========================
$titre[] = "Outils graphiques de visualisation";
if (($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur'))
    if ($_SESSION['statut']!='scolarite')
        $titre[] =  "Visualiser mes moyennes et appréciations des bulletins ";
    else
        $titre[] =  "Visualiser les moyennes et appréciations des bulletins ";
if ($conditions_moyennes) $titre[] = "Visualiser toutes les moyennes d'une classe";
if ($conditions_bulsimples) {
	$titre[] = "Visualiser les bulletins simplifiés";
}
elseif(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesBulletinSimplePP")=="yes")) {
	if($test_pp>0) {
		$titre[] = "Visualiser les bulletins simplifiés";
	}
}

$expli = array();
//===========================
// AJOUT:boireaus
$expli[] = "Ceci vous permet de connaître tous les ".$gepiSettings['denomination_professeurs']." des classes dans lesquelles vous intervenez, ainsi que les compositions des groupes concernés.";
$expli[] = "Ce menu vous permet de consulter dans une même page les informations concernant un ".$gepiSettings['denomination_eleve']." (<em>enseignements suivis, bulletins, relevés de notes, ".$gepiSettings['denomination_responsables'].",...</em>). Certains éléments peuvent n'être accessibles que pour certaines catégories de visiteurs.";
$expli[] = "Ceci vous permet d'imprimer en PDF des listes avec les ".$gepiSettings['denomination_eleves'].", à l'unité ou en série. L'apparence des listes est paramétrable.";
if(($_SESSION['statut']=='scolarite')||(($_SESSION['statut']=='professeur') and ($test_prof_suivi != "0"))){
	$expli[] = "Ceci vous permet d'imprimer en PDF la synthèse des avis du conseil de classe.";
}
if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='professeur')||($_SESSION['statut']=='cpe')){
	$expli[] = "Ce menu permet de télécharger ses listes avec tous les ".$gepiSettings['denomination_eleves']." au format CSV avec les champs CLASSE;LOGIN;NOM;PRENOM;SEXE;DATE_NAISS.";
}

//===========================

$expli[] = "Visualisation graphique des résultats des ".$gepiSettings['denomination_eleves']." ou des classes, en croisant les données de multiples manières.";
if (($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur'))
    if ($_SESSION['statut']!='scolarite')
        $expli[] = "Tableau récapitulatif de vos moyennes et/ou appréciations figurant dans les bulletins avec affichage de statistiques utiles pour le remplissage des livrets scolaires.";
    else
        $expli[] = "Tableau récapitulatif des moyennes et/ou appréciations figurant dans les bulletins avec affichage de statistiques utiles pour le remplissage des livrets scolaires.";
if ($conditions_moyennes) $expli[] = "Tableau récapitulatif des moyennes d'une classe.";
if ($conditions_bulsimples) {
	$expli[] = "Bulletins simplifiés d'une classe.";
}
elseif(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesBulletinSimplePP")=="yes")) {
	$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."';";
	$test_pp=mysql_num_rows(mysql_query($sql));
	if($test_pp>0) {
		$expli[] = "Bulletins simplifiés d'une classe.";
	}
}


$call_data = mysql_query("SELECT * FROM aid_config WHERE display_bulletin = 'y' OR bull_simplifie = 'y' ORDER BY nom");
$nb_aid = mysql_num_rows($call_data);
$i=0;
while ($i < $nb_aid) {
    $indice_aid = @mysql_result($call_data, $i, "indice_aid");
    $call_prof = mysql_query("SELECT * FROM j_aid_utilisateurs WHERE (id_utilisateur = '" . $_SESSION['login'] . "' and indice_aid = '$indice_aid')");
    $nb_result = mysql_num_rows($call_prof);
    if ($nb_result != 0) {
        $nom_aid = @mysql_result($call_data, $i, "nom");
        $chemin[] = "/prepa_conseil/visu_aid.php?indice_aid=".$indice_aid;
        $titre[] = "Visualiser des appréciations $nom_aid";
        $expli[] = "Cet outil permet la visualisation et l'impression des appréciations des ".$gepiSettings['denomination_eleves']." pour les $nom_aid.";
    }
    $i++;
}

if(($_SESSION['statut']=='professeur')&&(getSettingValue('GepiAccesGestElevesProfP')=='yes')) {
	// Le professeur est-il professeur principal dans une classe au moins.
	$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."';";
	$test=mysql_query($sql);
	if (mysql_num_rows($test)>0) {
		$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
		$chemin[] = "/eleves/index.php";
		$titre[] = "Gestion des ".$gepiSettings['denomination_eleves'];
		$expli[] = "Cet outil permet d'accéder aux informations des ".$gepiSettings['denomination_eleves']." dont vous êtes $gepi_prof_suivi.";
	}
}

$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/print.png' alt=''/> - Visualisation - Impression</h2>\n";
	//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
	echo "<table class='menu' summary=\"Visualisation - Impression. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
    //echo "<table class='menu' summary=\"Visualisation/Impression\">\n";
    //echo "<tr>\n";
    //echo "<th colspan='2'><img src='./images/icons/print.png' alt='Imprimer' class='link'/> - Visualisation - Impression</th>\n";
    //echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}
/***********************************
	Fin visualisation / Impression
***********************************/


/********************
	Gestion Notanet
********************/
if (getSettingValue("active_notanet")=='y') {
	$chemin = array();
	//$chemin[] = "/mod_notanet/notanet.php";
	$chemin[] = "/mod_notanet/index.php";
	//$chemin[] = "/mod_notanet/fiches_brevet.php";

	$titre = array();
	//$titre[] = "Notanet";
	//$titre[] = "Fiches Brevet";
	$titre[] = "Notanet/Fiches Brevet";

	$expli = array();
	//$expli[] = "Cet outil permet d'effectuer les calculs et la génération du fichier CSV requis pour Notanet.<br />L'opération renseigne également les tables nécessaires pour générer les Fiches brevet.";
	//$expli[] = "Cet outil permet de générer les fiches brevet.";
	if($_SESSION['statut']=='professeur') {
		$expli[] = "Cet outil permet de saisir les appréciations pour les Fiches Brevet.";
	}
	else {
		$expli[] = "Cet outil permet<br /><ul><li>d'effectuer les calculs et la génération du fichier CSV requis pour Notanet.<br />L'opération renseigne également les tables nécessaires pour générer les Fiches brevet.</li><li>de générer les fiches brevet</li></ul>";
	}

	$nb_ligne = count($chemin);
	$affiche = 'no';
	for ($i=0;$i<$nb_ligne;$i++) {
		if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
	}

	if($_SESSION['statut']=='professeur') {
		$sql="SELECT DISTINCT g.*,c.classe FROM groupes g,
							j_groupes_classes jgc,
							j_groupes_professeurs jgp,
							j_groupes_matieres jgm,
							classes c,
							notanet n
						WHERE g.id=jgc.id_groupe AND
							jgc.id_classe=n.id_classe AND
							jgc.id_classe=c.id AND
							jgc.id_groupe=jgp.id_groupe AND
							jgp.login='".$_SESSION['login']."' AND
							jgm.id_groupe=g.id AND
							jgm.id_matiere=n.matiere
						ORDER BY jgc.id_classe;";
		//echo "$sql<br />";
		$res_grp=mysql_query($sql);
		if(mysql_num_rows($res_grp)==0) {
			$affiche='no';
		}
	}

	if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
		echo "<h2 class='accueil'><img src='./images/icons/document.png' alt=''/> - Notanet/Fiches Brevet</h2>\n";
		//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
		echo "<table class='menu' summary=\"Gestion Notanet. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
		//echo "<table class='menu' summary=\"Gestion Notanet\">\n";
		//echo "<tr>\n";
		//echo "<th colspan='2'><img src='./images/icons/document.png' alt='Notanet/Fiches Brevet' class='link'/> - Notanet/Fiches Brevet</th>\n";
		//echo "</tr>\n";
		for ($i=0;$i<$nb_ligne;$i++) {
			affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
		}
		echo "</table>\n";
	}
}

/************************
	Fin gestion Notanet
************************/


/*******************************
// Gestion années antérieures
*******************************/

if (getSettingValue("active_annees_anterieures")=='y') {
	$chemin = array();
	$titre = array();
	$expli = array();

	if($_SESSION['statut']=='administrateur'){
		$chemin[] = "/mod_annees_anterieures/index.php";
		$titre[] = "Années antérieures";
		$expli[] = "Cet outil permet de gérer et de consulter les données d'années antérieures (<em>bulletins simplifiés,...</em>).";
	}
	else{
		if($_SESSION['statut']=='professeur') {
			$AAProfTout=getSettingValue('AAProfTout');
			$AAProfPrinc=getSettingValue('AAProfPrinc');
			$AAProfClasses=getSettingValue('AAProfClasses');
			$AAProfGroupes=getSettingValue('AAProfGroupes');

			if(($AAProfTout=="yes")||($AAProfClasses=="yes")||($AAProfGroupes=="yes")){
				$chemin[] = "/mod_annees_anterieures/consultation_annee_anterieure.php";
				$titre[] = "Années antérieures";
				$expli[] = "Cet outil permet de consulter les données d'années antérieures (<em>bulletins simplifiés,...</em>).";
			}
			elseif($AAProfPrinc=="yes"){
				$sql="SELECT 1=1 FROM classes c,
									j_eleves_professeurs jep
							WHERE jep.professeur='".$_SESSION['login']."' AND
									jep.id_classe=c.id
									ORDER BY c.classe";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0){
					//$chemin[] = "/mod_annees_anterieures/index.php";
					$chemin[] = "/mod_annees_anterieures/consultation_annee_anterieure.php";
					$titre[] = "Années antérieures";
					$expli[] = "Cet outil permet de consulter les données d'années antérieures (<em>bulletins simplifiés,...</em>).";
				}
			}
		}
		elseif($_SESSION['statut']=='scolarite') {
			$AAScolTout=getSettingValue('AAScolTout');
			$AAScolResp=getSettingValue('AAScolResp');

			if($AAScolTout=="yes"){
				$chemin[] = "/mod_annees_anterieures/consultation_annee_anterieure.php";
				$titre[] = "Années antérieures";
				$expli[] = "Cet outil permet de consulter les données d'années antérieures (<em>bulletins simplifiés,...</em>).";
			}
			elseif($AAScolResp=="yes"){
				$sql="SELECT 1=1 FROM j_scol_classes jsc
								WHERE jsc.login='".$_SESSION['login']."';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0){
					$chemin[] = "/mod_annees_anterieures/consultation_annee_anterieure.php";
					$titre[] = "Années antérieures";
					$expli[] = "Cet outil permet de consulter les données d'années antérieures (<em>bulletins simplifiés,...</em>).";
				}
			}
		}
		elseif($_SESSION['statut']=='cpe') {
			$AACpeTout=getSettingValue('AACpeTout');
			$AACpeResp=getSettingValue('AACpeResp');

			if($AACpeTout=="yes"){
				$chemin[] = "/mod_annees_anterieures/consultation_annee_anterieure.php";
				$titre[] = "Années antérieures";
				$expli[] = "Cet outil permet de consulter les données d'années antérieures (<em>bulletins simplifiés,...</em>).";
			}
			elseif($AACpeResp=="yes"){
				$sql="SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='".$_SESSION['login']."'";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0){
					$chemin[] = "/mod_annees_anterieures/consultation_annee_anterieure.php";
					$titre[] = "Années antérieures";
					$expli[] = "Cet outil permet de consulter les données d'années antérieures (<em>bulletins simplifiés,...</em>).";
				}
			}
		}
		elseif($_SESSION['statut']=='responsable') {
			$AAResponsable=getSettingValue('AAResponsable');

			if($AAResponsable=="yes"){
				// Est-ce que le responsable est bien associé à un élève?
				$sql="SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e WHERE rp.pers_id=r.pers_id AND
																					r.ele_id=e.ele_id AND
																					rp.login='".$_SESSION['login']."'";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0){
					$chemin[] = "/mod_annees_anterieures/consultation_annee_anterieure.php";
					$titre[] = "Années antérieures";
					$expli[] = "Cet outil permet de consulter les données d'années antérieures (<em>bulletins simplifiés,...</em>).";
				}
			}
		}
		elseif($_SESSION['statut']=='eleve') {
			$AAEleve=getSettingValue('AAEleve');

			if($AAEleve=="yes"){
				$chemin[] = "/mod_annees_anterieures/consultation_annee_anterieure.php";
				$titre[] = "Années antérieures";
				$expli[] = "Cet outil permet de consulter les données d'années antérieures (<em>bulletins simplifiés,...</em>).";
			}
		}
	}

	$nb_ligne = count($chemin);
	$affiche = 'no';
	for ($i=0;$i<$nb_ligne;$i++) {
		if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
	}
	if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
		echo "<h2 class='accueil'><img src='./images/icons/document.png' alt=''/> - Années antérieures</h2>\n";
		//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
		echo "<table class='menu' summary=\"Gestion des Années antérieures. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
		//echo "<table class='menu' summary=\"Gestion des Années antérieures\">\n";
		//echo "<tr>\n";
		//echo "<th colspan='2'><img src='./images/icons/document.png' alt='Années antérieures' class='link'/> - Années antérieures</th>\n";
		//echo "</tr>\n";
		for ($i=0;$i<$nb_ligne;$i++) {
			affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
		}
		echo "</table>\n";
	}
}

/***********************************
	Fin gestion Années antérieures
***********************************/


/*************************
// Gestion des messages
*************************/

$chemin = array();
$chemin[] = "/messagerie/index.php";

$titre = array();
//$titre[] = "Messagerie interne";
$titre[] = "Panneau d'affichage";

$expli = array();
$expli[] = "Cet outil permet la gestion des messages à afficher sur la page d'accueil des utilisateurs.";

$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/mail.png' alt=''/> - Panneau d'affichage</h2>\n";
	//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
	echo "<table class='menu' summary=\"Gestion du panneau d'affichage. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
    //echo "<table class='menu' summary=\"Gestion du panneau d'affichage\">\n";
    //echo "<tr>\n";
    //echo "<th colspan='2'><img src='./images/icons/mail.png' alt='Messagerie' class='link'/> - Messagerie</th>\n";
   // echo "<th colspan='2'><img src='./images/icons/mail.png' alt='Messagerie' class='link'/> - Panneau d'affichage</th>\n";
    //echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}

/*************************
	Fin gestion des messages
*************************/


/********************
	Module Ateliers
********************/

if (EstAutoriseAteliers($_SESSION["login"])) {
  $chemin = array();
  $titre = array();
  $expli = array();

  $chemin[]="/mod_ateliers/ateliers_accueil_admin.php";
  $titre[] = "Configuration du module Ateliers";
  $expli[] = "Configuration des événements, des disciplines, des ".$gepiSettings['denomination_professeurs'].", des salles.";

  $nb_ligne = count($chemin);
  $affiche = 'no';
  for ($i=0;$i<$nb_ligne;$i++) {
      if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
  }
  if ($affiche=='yes') {
		// modification Régis : créer des <h2> pour faciliter la navigation
		echo "<h2 class='accueil'><img src='./images/icons/document.png' alt=''/> - Module Ateliers</h2>\n";
		//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
		echo "<table class='menu' summary=\"Module Ateliers. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
      //echo "<table class='menu' summary=\"Module Ateliers\">\n";
      //echo "<tr>\n";
      //echo "<th colspan='2'><img src='./images/icons/document.png' alt='Inscription' class='link'/> - Module Ateliers </th>\n";
     // echo "</tr>\n";
      for ($i=0;$i<$nb_ligne;$i++) {
          affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
      }
      echo "</table>";
  }
}

/************************
	Fin module Ateliers
************************/


/*************************
	Module inscription
*************************/

if (getSettingValue("active_inscription")=='y') {
  $chemin = array();
  if (getSettingValue("active_inscription_utilisateurs")=='y') $chemin[]="/mod_inscription/inscription_index.php";
  $chemin[]="/mod_inscription/inscription_config.php";

  $titre = array();
  if (getSettingValue("active_inscription_utilisateurs")=='y') $titre[] = "Accès au module d'inscription/visualisation";
  $titre[] = "Configuration du module d'inscription/visualisation";

  $expli = array();
  if (getSettingValue("active_inscription_utilisateurs")=='y') $expli[] = "S'inscrire ou se désinscrire - Consulter les inscriptions";
  $expli[] = "Configuration des différents paramètres du module";

  $nb_ligne = count($chemin);
  $affiche = 'no';
  for ($i=0;$i<$nb_ligne;$i++) {
      if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
  }
  if ($affiche=='yes') {
		// modification Régis : créer des <h2> pour faciliter la navigation
		echo "<h2 class='accueil'><img src='./images/icons/document.png' alt=''/> - Inscription</h2>\n";
		//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
		echo "<table class='menu' summary=\"Module d'inscriptions. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
     // echo "<table class='menu' summary=\"Module d'inscriptions\">\n";
     // echo "<tr>\n";
     // echo "<th colspan='2'><img src='./images/icons/document.png' alt='Inscription' class='link'/> - ".getSettingValue("mod_inscription_titre")." - Inscription </th>\n";
     // echo "</tr>\n";
      for ($i=0;$i<$nb_ligne;$i++) {
          affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
      }
      echo "</table>";
  }
}

/*****************************
	Fin module inscription
*****************************/


/*************************
	Module discipline
*************************/

if (getSettingValue("active_mod_discipline")=='y') {

	$chemin = array();
	$chemin[]="/mod_discipline/index.php";
	$titre = array();
	$titre[] = "Discipline";

	$expli = array();
	$expli[] = "Signaler des incidents, prendre des mesures, des sanctions.";

	$nb_ligne = count($chemin);
	$affiche = 'no';
	for ($i=0;$i<$nb_ligne;$i++) {
		if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
	}
	if ($affiche=='yes') {
			echo "<h2 class='accueil'><img src='./images/icons/document.png' alt=''/> - Discipline</h2>\n";
			echo "<table class='menu' summary=\"Module discipline. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
			for ($i=0;$i<$nb_ligne;$i++) {
				affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
			}
			echo "</table>";
	}
}

/*****************************
	Fin module discipline
*****************************/

/*************************
	Module Modèle Open Office
*************************/

if (getSettingValue("active_mod_ooo")=='y') {

	$chemin = array();
	$chemin[]="/mod_ooo/index.php";
	$titre = array();
	$titre[] = "Modèle Open Office";

	$expli = array();
	$expli[] = "Gérer les modèles Open Office dans Gepi et Utiliser les formulaires de saisie";

	$nb_ligne = count($chemin);
	$affiche = 'no';
	for ($i=0;$i<$nb_ligne;$i++) {
		if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
	}
	if ($affiche=='yes') {
			echo "<h2 class='accueil'><img src='./mod_ooo/images/ico_gene_ooo.png' alt=''/> - Modèles Open Office</h2>\n";
			echo "<table class='menu' summary=\"Module Modèle Open Office. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
			for ($i=0;$i<$nb_ligne;$i++) {
				affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
			}
			echo "</table>";
	}
}

/*****************************
	Fin module modèle Open Office
*****************************/

/*****************************
 * Module plugins : affichage des menus des plugins en fonction des droits
*****************************/

$query = mysql_query('SELECT * FROM plugins WHERE ouvert = "y"');
while ($plugin = mysql_fetch_object($query)){

  $chemin = array();
  $titre = array();
  $expli = array();

  $querymenu = mysql_query('SELECT * FROM plugins_menus WHERE plugin_id = "'.$plugin->id.'"');
  while ($menuItem = mysql_fetch_object($querymenu)){
    if ($menuItem->user_statut == $_SESSION['statut']){
      $chemin[] = $menuItem->lien_item;

      $titre[]  = $menuItem->titre_item;

      $expli[]  = $menuItem->description_item;
    }
  }

      $nb_ligne = count($chemin);

    if ($nb_ligne >= 1){
      echo "<h2 class='accueil'><img src='./images/icons/package.png' alt='#' /> - ".str_replace("_", " ", $plugin->nom)." (plugin)</h2>
    <table class='menu' summary=\"Plugins de Gepi. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
			for ($i=0;$i<$nb_ligne;$i++) {

        echo "
      <tr>
        <td class='menu_gauche'><a href=\"$chemin[$i]\">$titre[$i]</a></td>
        <td class='menu_droit'>$expli[$i]</td>
      </tr>\n";

			}
			echo "</table>";
    }

}



/*****************************
 * Fin module plugins
*****************************/

/*************************
	Module Genese des classes
*************************/

if (getSettingValue("active_mod_genese_classes")=='y') {

	$chemin = array();
	$chemin[]="/mod_genese_classes/index.php";
	$titre = array();
	$titre[] = "Génèse des classes";

	$expli = array();
	$expli[] = "Effectuer la répartition des élèves par classes en tenant comptes des options,...";

	$nb_ligne = count($chemin);
	$affiche = 'no';
	for ($i=0;$i<$nb_ligne;$i++) {
		if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
	}
	if ($affiche=='yes') {
			echo "<h2 class='accueil'><img src='./images/icons/document.png' alt=''/> - Génèse des classes</h2>\n";
			echo "<table class='menu' summary=\"Module Génèse des classes. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
			for ($i=0;$i<$nb_ligne;$i++) {
				affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
			}
			echo "</table>";
	}
}

/*****************************
	Fin module Genese des classes
*****************************/

/**************************************************************
	Lien vers les flux rss pour les élèves s'ils sont activés
**************************************************************/

if (getSettingValue("rss_cdt_eleve") == 'y' AND $_SESSION["statut"] == "eleve") {
	// Les flux rss sont ouverts pour les élèves
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/rss.png' alt=''/> - Votre flux rss</h2>\n";
	echo "<table class='menu' summary=\"Tableau des flux RSS. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
	/* echo "
		<table class='menu' summary=\"Tableau des flux RSS\">
			<tr>
				<th colspan='2'>
					<img src='./images/icons/rss.png' alt='flux rss' class='link'/>
					 - Votre flux rss (syndication)
				</th>
			</tr>\n";*/
	// A vérifier pour les cdt
	if (getSettingValue("rss_acces_ele") == 'direct') {

		$uri_el = retourneUri($_SESSION["login"], $test_https, 'cdt');
		echo '
			<tr>
				<td class="menu_gauche" title="A utiliser avec un lecteur de flux rss" style="cursor: pointer; color: blue;" onclick="changementDisplay(\'divuri\', \'divexpli\');">
					Votre uri pour les cahiers de textes
				</td>
				<td class="menu_droit">
					<div id="divuri" style="display: none;">
						<a onclick="window.open(this.href, \'_blank\'); return false;" href="'.$uri_el["uri"].'">'.$uri_el["text"].'</a>
					</div>
					<div id="divexpli" style="display: block;">En cliquant sur la cellule de gauche, vous pourrez récupérer votre URI <em>(si vous avez activé le javascript sur votre navigateur)</em>.</div>
				</td>
			</tr>
		';
	}elseif(getSettingValue("rss_acces_ele") == 'csv'){
		echo '
			<tr>
				<td class="menu_gauche">Votre uri pour les cahiers de textes</td>
				<td class="menu_droit">Veuillez la demander à l\'administration de votre établissement.</td>
			</tr>
		';
	}

	echo '</table>';
}

/******************************************************************
	Fin lien vers les flux rss pour les élèves s'ils sont activés
******************************************************************/



//=================================
// AJOUT: boireaus 20071127
//        Ajout pour un module spécial.
//        Il suffit de décommenter la ligne pour charger le module (s'il existe)
// include('inc_special.php');
//=================================

// ========================== Statut AUTRE =============================
if ($_SESSION["statut"] == 'autre') {
	// On récupère la liste des fichiers à autoriser
	require_once("utilisateurs/creer_statut_autorisation.php");
	$nbre_a = count($autorise);
	// modification Régis : créer des <h2> pour faciliter la navigation
	echo "<h2 class='accueil'><img src='./images/icons/document.png' alt=''/>&nbsp;-&nbsp;Navigation</h2>\n";
	echo "<table class='menu' summary=\"Tableau du statut Autre. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";

	/*echo '<br />
		<table class="menu" summary="Tableau du statut Autre">
			<tr>
			<th colspan="2"><img src="./images/icons/document.png" alt="Inscription" class="link" />&nbsp;-&nbsp;Navigation</th></tr>';*/

	//for($a = 1 ; $a < $nbre ; $a++){
	$a = 1;
	while($a < $nbre_a){

		// On récupère le droit sur le fichier
		$sql_f = "SELECT autorisation FROM droits_speciaux WHERE id_statut = '".$_SESSION["statut_special_id"]."' AND nom_fichier = '".$autorise[$a][0]."' ORDER BY id";
		$query_f = mysql_query($sql_f) OR trigger_error('Impossible de trouver le droit : '.mysql_error(), E_USER_WARNING);
		$nbre = mysql_num_rows($query_f);
		if ($nbre >= 1) {
			$rep_f = mysql_result($query_f, "autorisation");
		}else{
			$rep_f = '';
		}

		if ($rep_f == 'V') {

			$test = explode(".", $autorise[$a][0]); // On teste pour voir s'il y a un .php à la fin de la chaîne

			if (!isset($test[1])) {
				// rien, la vérification se fait dans le module EdT
				// ou alors dans les autres modules spécifiés
			}else{
				if($a == 4){
					// Dans le cas de la saisie des absences, il faut ajouter une variable pour le GET
					$var = '?type=A';
				}else{
					$var = '';
				}
			echo '
				<tr>
					<td><a href="'.$gepiPath.$autorise[$a][0].$var.'">'.$menu_accueil[$a][0].'</a></td>
					<td>'.$menu_accueil[$a][1].'</td>
				</tr>
			';
			}
		}
		$a++;
	}

	echo '</table>';
}
// ========================== fin Statut AUTRE =============================


/*************************
        Module Epreuves blanches
*************************/
//insert into setting set name='active_mod_epreuve_blanche', value='y';
if (getSettingValue("active_mod_epreuve_blanche")=='y') {
$chemin = array();
$chemin[]="/mod_epreuve_blanche/index.php";
$titre = array();
$titre[] = "Epreuves blanches";

$expli = array();
$expli[] = "Organisation d'épreuves blanches,...";

$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
echo "<h2 class='accueil'><img src='./images/icons/document.png' alt=''/> - Epreuves blanches</h2>\n";
echo "<table class='menu' summary=\"Module Epreuves blanches. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
for ($i=0;$i<$nb_ligne;$i++) {
affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
}
echo "</table>";
}
}

/*****************************
        Fin module Epreuves blanches
*****************************/

/*************************
        Module Examens blancs
*************************/
//insert into setting set name='active_mod_epreuve_blanche', value='y';
if (getSettingValue("active_mod_examen_blanc")=='y') {
$chemin = array();
$chemin[]="/mod_examen_blanc/index.php";
$titre = array();
$titre[] = "Examens blancs";

$expli = array();
$expli[] = "Organisation d'examens blancs,...";

$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
echo "<h2 class='accueil'><img src='./images/icons/document.png' alt=''/> - Examens blancs</h2>\n";
echo "<table class='menu' summary=\"Module Examens blancs. Colonne de gauche : lien vers les pages, colonne de droite : rapide description\">\n";
for ($i=0;$i<$nb_ligne;$i++) {
affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
}
echo "</table>";
}
}

/*****************************
        Fin module Examens blancs
*****************************/

require_once ("./lib/footer.inc.php");
?>
