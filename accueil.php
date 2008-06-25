<?php
$starttime = microtime();
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
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ./logout.php?auto=1");
    die();
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
        echo "<td width=\"30%\" align=\"left\" style='border-right: none;'><a href=$temp>$titre_</a>";
        echo "</td>\n";
        echo "<td align=\"left\">$expli_</td>\n";
        echo "</tr>\n";
    }
}


if ($_SESSION['statut'] == "administrateur") {
    echo "<div>\n";

    // Vérification et/ou changement du répertoire de backup
    if (!check_backup_directory()) {
        echo "<font color='red'>Il y a eu un problème avec la mise à jour du répertoire de sauvegarde. \n";
        echo "Veuillez vérifier que le répertoire /backup de Gepi est accessible en écriture par le serveur (le serveur *uniquement* !)<br/>\n";
    }

    // Vérification et/ou changement du répertoire temp
	/*
    if (!check_temp_directory()) {
        echo "<font color='red'>Il y a eu un problème avec la mise à jour du répertoire temp. \n";
        echo "Veuillez vérifier que le répertoire /temp de Gepi est accessible en écriture par le serveur (le serveur *uniquement* !)<br/>\n";
    }
	*/

    if (!check_user_temp_directory()) {
        echo "<font color='red'>Il y a eu un problème avec la mise à jour du répertoire temp. \n";
		//if($_SESSION['statut']=='administrateur'){
			echo "Veuillez vérifier que le répertoire /temp de Gepi est accessible en écriture par le serveur (le serveur *uniquement* !)<br/>\n";
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
		echo "<font color=\"red\"><center>Attention : le site est en cours de maintenance et temporairement inaccessible.</center></font>\n";
	}

    // * affichage du nombre de connecté *
    // compte le nombre d'enregistrement dans la table
    $sql = "select LOGIN from log where END > now()";
    $res = sql_query($sql);
    $nb_connect = sql_count($res);
    echo "Nombre de personnes actuellement connectées : $nb_connect ";
    echo "(<a href = 'gestion/gestion_connect.php?mode_navig=accueil'>Gestion des connexions</a>)\n";

	// Lien vers le panneau de contrôle de sécurité
	$alert_sums = mysql_result(mysql_query("SELECT SUM(niveau) FROM tentatives_intrusion WHERE (statut = 'new')"), 0);
	if (empty($alert_sums)) $alert_sums = "0";
	echo "<br/>Alertes sécurité (niveaux cumulés) : $alert_sums (<a href='gestion/security_panel.php'>Panneau de contrôle</a>)";

// christian : demande d'enregistrement
if ($force_ref) {
    ?><div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: #CFD7FF;  padding: 2px; margin-left: 60px; margin-right: 60px; margin-top: 2px; margin-bottom: 2px;  text-align: center; color: #1C1A8F; font-weight: bold;">Votre établissement n'est pas référencé parmi les utilisateurs de Gepi.<br /><a href="javascript:ouvre_popup_reference('<?php echo($gepiPath); ?>/referencement.php?etape=explication')" title="Pourquoi est-ce utile ?">Pourquoi est-ce utile ?</a> / <a href="javascript:ouvre_popup_reference('<?php echo($gepiPath); ?>/referencement.php?etape=1')" title="Référencer votre établissement">Référencer votre établissement</a>.</div><?php
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
            echo "<br/><font color='red'>Connexion non sécurisée ! Vous *devez* accéder à Gepi en HTTPS (vérifiez la configuration de votre serveur web)</font>\n";
            $test_https = 'n';
    }

    if (ini_get("register_globals") == "1") {
            echo "<br/><font color='red'>PHP potentiellement mal configuré (register_globals=on)! Pour prévenir certaines failles de sécurité, vous *devez* configurer PHP avec le paramètre register_globals à off.</font>\n";
    }

    echo "</div>\n";
}
elseif(($_SESSION['statut']=="professeur")||($_SESSION['statut']=="scolarite")||($_SESSION['statut']=="cpe")||($_SESSION['statut']=="secours")){
    if (!check_user_temp_directory()) {
        echo "<div>\n";
		echo "<font color='red'>Il y a eu un problème avec la mise à jour du répertoire temp. \n";
		/*
		if($_SESSION['statut']=='administrateur'){
			echo "Veuillez vérifier que le répertoire /temp de Gepi est accessible en écriture par le serveur (le serveur *uniquement* !)<br/>\n";
		}
		else{
		*/
			echo "Veuillez contacter l'administrateur pour résoudre ce problème.<br/>\n";
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


echo "<center>\n";

//Affichage des messages
$today=mktime(0,0,0,date("m"),date("d"),date("Y"));
$appel_messages = mysql_query("SELECT id, texte, date_debut, date_fin, auteur, destinataires FROM messages
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
        $texte_messages .= $content;
    }
    $ind++;
}
if ($affiche_messages == 'yes') {
    echo "<table id='messagerie'>\n";
    echo "<tr><td>".$texte_messages;
    echo "</td></tr></table>\n";
}



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
"Pour gérer les bases (établissements, utilisateurs, matières, classes, élèves, responsables, AIDs).",
"Pour gérer les modules (cahiers de texte, carnet de notes, absences, trombinoscope)."
);

$nb_ligne = count($chemin);
//
// Outils d'administration
//
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
    //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
    echo "<table class='menu'>\n";
    echo "<tr>\n";
    echo "<th colspan='2'><img src='./images/icons/configure.png' alt='Admin' class='link'/> - Administration</th>\n";
    echo "</tr>\n";
    // Affichage du bouton pour lancer une sauvegarde
    echo "<tr>";
    echo "<td colspan='2' style='text-align: center; padding: 10px;'>";
    echo "<form enctype=\"multipart/form-data\" action=\"gestion/accueil_sauve.php\" method=\"post\" name=\"formulaire\">\n";
    if (getSettingValue("mode_sauvegarde") == "mysqldump") {
    	echo "<input type='hidden' name='action' value='system_dump' />";
    } else {
    	echo "<input type='hidden' name='action' value='dump' />";
    }
    echo "<input type=\"submit\" value=\"Lancer une sauvegarde de la base de données\" /></form>\n";
	echo "<span class='small'>(le répertoire \"documents\" contenant les documents joints aux cahiers de texte ne sera pas sauvegardé)</span>\n";
    echo "</td></tr>";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}
//
// Outils de gestion
//

// On teste si on l'utilisateur est un prof avec des matières. Si oui, on affiche les lignes relatives au cahier de texte et au carnet de notes
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
{ $chemin[] = "/bulletin/index.php";}

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
{ $titre[] = "Accès des élèves et responsables aux appreciations"; }
//==========================================================

if ((($test_prof_suivi != "0") and ($_SESSION['statut']=='professeur') AND (getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")=='yes')) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")=='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")=='yes')))
{ $titre[] = "Paramètres d'impression des bulletins";}
if ($_SESSION['statut']=='scolarite')
{ $titre[] = "Gestion des fiches responsables élèves";}
if ($_SESSION['statut']=='scolarite')
{ $titre[] = "Gestion des fiches élèves";}
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
{ $expli[] = "Permet de définir quand les comptes élèves et responsables (s'ils existent) peuvent accéder aux appreciations des professeurs sur le bulletin et avis du conseil de classe."; }
//==========================================================

if ((($test_prof_suivi != "0") and ($_SESSION['statut']=='professeur') AND (getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")=='yes')) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")=='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")=='yes')))
{ $expli[] = "Permet de modifier les paramètres de mise en page et d'impression des bulletins.";}
if ($_SESSION['statut']=='scolarite')
{ $expli[] = "Cet outil vous permet de modifier/supprimer/ajouter des fiches responsable élèves.";}
if ($_SESSION['statut']=='scolarite')
{ $expli[] = "Cet outil vous permet de modifier/supprimer/ajouter des fiches élèves.";}
if ((($test_prof_suivi != "0") and (getSettingValue("GepiProfImprBul")=='yes')) or ($_SESSION['statut']!='professeur'))
{ $expli[] = "Cet outil vous permet de visualiser à l'écran et d'imprimer les bulletins, classe par classe.";}

$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
    //else{echo "$chemin[$i] refusé<br />";}
}
if ($affiche=='yes') {
    //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
    echo "<table class='menu'>\n";
    echo "<tr>\n";
    echo "<th colspan='2'><img src='./images/icons/document.png' alt='Bulletins' class='link'/> - Bulletins scolaires</th>\n";
    echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}



//
// Saisie
//
$chemin = array();
$chemin[] = "/absences/index.php";
if ((($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) and (getSettingValue("active_cahiers_texte")=='y')) $chemin[] = "/cahier_texte/index.php";
if ((($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) and (getSettingValue("active_carnets_notes")=='y')) $chemin[] = "/cahier_notes/index.php";
if (($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) $chemin[] = "/saisie/index.php";
if ((($test_prof_suivi != "0") and (getSettingValue("GepiRubConseilProf")=='yes')) or (($_SESSION['statut']!='professeur') and (getSettingValue("GepiRubConseilScol")=='yes') ) or ($_SESSION['statut']=='secours')  ) $chemin[] = "/saisie/saisie_avis.php";


$titre = array();
$titre[] = "Gestion des absences";
if ((($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) and (getSettingValue("active_cahiers_texte")=='y')) $titre[] = "Cahier de texte";
if ((($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) and (getSettingValue("active_carnets_notes")=='y')) $titre[] = "Carnet de notes : saisie des notes";
if (($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) $titre[] = "Bulletin : saisie des moyennes et des appréciations par matière";
if ((($test_prof_suivi != "0") and (getSettingValue("GepiRubConseilProf")=='yes')) or (($_SESSION['statut']!='professeur') and (getSettingValue("GepiRubConseilScol")=='yes') ) or ($_SESSION['statut']=='secours')  ) $titre[] = "Bulletin : saisie des avis du conseil";

$expli = array();
$expli[] = "Cet outil vous permet d'enregistrer les absences des élèves. Elles figureront sur le bulletin scolaire.";
if ((($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) and (getSettingValue("active_cahiers_texte")=='y')) $expli[] = "Cet outil vous permet de constituer un cahier de texte pour chacune de vos classes.";
if ((($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) and (getSettingValue("active_carnets_notes")=='y')) $expli[] = "Cet outil vous permet de constituer un carnet de notes pour chaque période et de saisir les notes de toutes vos évaluations.";
if (($test_prof_matiere != "0") or ($_SESSION['statut']!='professeur')) $expli[] = "Cet outil permet de saisir directement, sans passer par le carnet de notes, les moyennes et les appréciations du bulletin";
if ((($test_prof_suivi != "0") and (getSettingValue("GepiRubConseilProf")=='yes')) or (($_SESSION['statut']!='professeur') and (getSettingValue("GepiRubConseilScol")=='yes') ) or ($_SESSION['statut']=='secours')  ) $expli[] = "Cet outil permet la saisie des avis du conseil de classe.";

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
        $expli[] = "Cet outil permet la saisie des appréciations des élèves pour les $nom_aid.";
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

//==============================


$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
    //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
    echo "<table class='menu'>\n";
    echo "<tr>\n";
    echo "<th colspan='2'><img src='./images/icons/saisie.png' alt='Saisie' class='link'/> - Saisie</th>\n";
    echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}

//
// Outils destinés essentiellement aux parents
// et aux élèves
//

$chemin = array();
$titre = array();
$expli = array();

// Cahier de texte
$condition = (
	getSettingValue("active_cahiers_texte")=='y' AND (
		($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesCahierTexteParent") == 'yes')
		OR ($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesCahierTexteEleve") == 'yes')
	));
if ($condition) {
    $chemin[] = "/cahier_texte/consultation.php";
    $titre[] = "Cahier de texte";
    if ($_SESSION['statut'] == "responsable") {
    	$expli[] = "Permet de consulter les compte-rendus de séance et les devoirs à faire pour le ou les élève(s) dont vous êtes responsable légal.";
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
    $chemin[] = "/cahier_notes/visu_releve_notes.php";
    $titre[] = "Relevés de notes";
    if ($_SESSION['statut'] == "responsable") {
    	$expli[] = "Permet de consulter les relevés de notes du ou des élève(s) dont vous êtes responsable légal.";
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
    	$expli[] = "Permet de consulter l'équipe pédagogique du ou des élève(s) dont vous êtes responsable légal.";
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
    	$expli[] = "Permet de consulter les bulletins simplifiés du ou des élève(s) dont vous êtes responsable légal.";
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
    	$expli[] = "Permet de visualiser sous forme graphique les résultats du ou des élève(s) dont vous êtes responsable légal, par rapport à la classe.";
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
	$expli[] = "Permet de suivre les absences et les retards des &eacute;l&egrave;ves dont je suis responsable";
}

$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
    echo "<table class='menu'>\n";
    echo "<tr>\n";
    echo "<th colspan='2'><img src='./images/icons/vie_privee.png' alt='Consultation' class='link'/> - Consultation</th>\n";
    echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}


//
// Outils de relevé de note
//
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
if ($condition) $chemin[] = "/cahier_notes/visu_releve_notes.php";

$titre = array();
if ($condition) $titre[] = "Visualisation et impression des relevés de notes";

$expli = array();
if ($condition) $expli[] = "Cet outil vous permet de visualiser à l'écran et d'imprimer les relevés de notes, élève par élève, classe par classe.";


if ($condition && $condition2) $chemin[] = "/cahier_notes/index2.php";
if ($condition && $condition2) $titre[] = "Visualisation des moyennes des carnets de notes";
if ($condition && $condition2) $expli[] = "Cet outil vous permet de visualiser à l'écran les moyennes calculées d'après le contenu des carnets de notes, indépendamment de la saisie des moyennes sur les bulletins.";


$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
    //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
    echo "<table class='menu'>\n";
    echo "<tr>\n";
    echo "<th colspan='2'><img src='./images/icons/releve.png' alt='Relevés' class='link'/> - Relevés de notes</th>\n";
    echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}

//
// Outils de gestion des absences : module de Christian Chapel
//

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
    $expli[] = "Cet outil vous permet de gérer les absences, dispenses, retards et autres  bobos à l'infirmerie des élèves.";
    $expli[] = "Vous pouvez visualiser créneau par créneau la saisie des absences.";

    $nb_ligne = count($chemin);
    $affiche = 'no';
    for ($i=0;$i<$nb_ligne;$i++) {
        if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
    }
    if ($affiche=='yes') {
          //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
		  echo "<table class='menu'>\n";
          echo "<tr>\n";
          echo "<th colspan='2'><img src='./images/icons/absences.png' alt='Absences' class='link'/> - Gestion des retards et absences</th>\n";
          echo "</tr>\n";
          for ($i=0;$i<$nb_ligne;$i++) {
            affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
        }
        echo "</table>\n";
    }
}

//
// Outils de gestion des absences par les professeurs : module de Christian Chapel
//

//On vérifie si le module est activé
if (getSettingValue("active_module_absence_professeur")=='y') {
//
// Gestion des ajout d'Absences par les professeurs
//
    $chemin = array();
    $chemin[] = "/mod_absences/professeurs/prof_ajout_abs.php";

    $titre = array();
    $titre[] = "Gestion des Absences par le professeur";

    $expli = array();
    $expli[] = "Cet outil vous permet de gérer les absences durant vos cours.";

    $nb_ligne = count($chemin);
    $affiche = 'no';
    for ($i=0;$i<$nb_ligne;$i++) {
        if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
    }
    if ($affiche=='yes') {
          //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
		  echo "<table class='menu'>\n";
          echo "<tr>\n";
          echo "<th colspan='2'><img src='./images/icons/absences.png' alt='Absences' class='link'/> - Gestion des retards et absences</th>\n";
          echo "</tr>\n";
          for ($i=0;$i<$nb_ligne;$i++) {
            affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
        }
        echo "</table>\n";
    }
}

//
// Outils de gestion des trombinoscopes : module de Christian Chapel
//

//On vérifie si le module est activé
if (getSettingValue("active_module_trombinoscopes")=='y') {
//
// Visualisation des trombinoscopes
//
    $chemin = array();
    $chemin[] = "/mod_trombinoscopes/trombinoscopes.php";

    $titre = array();
    $titre[] = "Trombinoscopes";

    $expli = array();
    $expli[] = "Cet outil vous permet de visualiser les trombinoscopes des classes.";

    // On n'appelle les aid "trombinoscope"
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
            $expli[] = "Cet outil vous permet de visualiser quels élèves ont le droit d'envoyer/modifier leur photo.";
        }
        $i++;
    }


    $nb_ligne = count($chemin);
    $affiche = 'no';
    for ($i=0;$i<$nb_ligne;$i++) {
        if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
    }
    if ($affiche=='yes') {
          //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
    	  echo "<table class='menu'>\n";
          echo "<tr>\n";
          echo "<th colspan='2'><img src='./images/icons/contact.png' alt='Trombi' class='link'/> - Trombinoscope</th>\n";
          echo "</tr>\n";
          for ($i=0;$i<$nb_ligne;$i++) {
            affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
        }
        echo "</table>\n";
    }
}

// Outils complémentaires de gestion des AID
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
if ($nb_aid != 0) {
    $chemin = array();
    $titre = array();
    $expli = array();
    $i = 0;
    while ($i<$nb_aid) {
        $indice_aid = mysql_result($call_data,$i,"indice_aid");
        $_indice_aid[] = mysql_result($call_data,$i,"indice_aid");
        $nom_aid = mysql_result($call_data,$i,"nom");
        $chemin[]="/aid/index_fiches.php?indice_aid=".$indice_aid;
        $titre[] = $nom_aid. " : outils de visualisation et d'édition";
        $expli[] = "Tableau récapitulatif, liste des élèves, ...";
        $i++;
    }
  $nb_ligne = count($chemin);
  $affiche = 'no';
  for ($i=0;$i<$nb_ligne;$i++) {
      if ((acces($chemin[$i],$_SESSION['statut'])==1) and AfficheAid($_SESSION['statut'],$_SESSION['login'],$_indice_aid[$i]))  {$affiche = 'yes';}
  }
  if ($affiche=='yes') {
      echo "<table class='menu'>\n";
      echo "<tr>\n";
      echo "<th colspan='2'><img src='./images/icons/document.png' alt='Outils complémentaires' class='link'/> - Outils de visualisation et d'édition des fiches projets</th>\n";
      echo "</tr>\n";
      for ($i=0;$i<$nb_ligne;$i++) {
          if (AfficheAid($_SESSION['statut'],$_SESSION['login'],$_indice_aid[$i]))
              affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
      }
      echo "</table>";
  }
}




// Accès aux modules propres au LPI
if (file_exists("./lpi/accueil.php")) require("./lpi/accueil.php");

//
// Visualisation / Impression
//

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
$titre[] = "Consultation d'un élève";
$titre[] = "Impression PDF de listes";
if(($_SESSION['statut']=='scolarite')||(($_SESSION['statut']=='professeur') and ($test_prof_suivi != "0"))){
	$titre[] = "Impression PDF des avis du conseil de classe";
}
if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='professeur')||($_SESSION['statut']=='cpe')){
	$titre[] = "Exporter mes listes d'élèves";
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
$expli[] = "Ceci vous permet de connaître tous les enseignants des classes dans lesquelles vous intervenez, ainsi que les compositions des groupes concernés.";
$expli[] = "Ce menu vous permet de consulter dans une même page les informations concernant un élève (<i>enseignements suivis, bulletins, relevés de notes, responsables,...</i>). Certains éléments peuvent n'être accessibles que pour certaines catégories de visiteurs.";
$expli[] = "Ceci vous permet d'imprimer en PDF des listes d'élèves à l'unité ou en série. L'apparence des listes est paramétrable.";
if(($_SESSION['statut']=='scolarite')||(($_SESSION['statut']=='professeur') and ($test_prof_suivi != "0"))){
	$expli[] = "Ceci vous permet d'imprimer en PDF la synthèse des avis du conseil de classe.";
}
if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='professeur')||($_SESSION['statut']=='cpe')){
	$expli[] = "Ce menu permet de télécharger ses listes d'élèves au format CSV avec les champs CLASSE;LOGIN;NOM;PRENOM;SEXE;DATE_NAISS.";
}

//===========================

$expli[] = "Visualisation graphique des résultats des élèves ou des classes, en croisant les données de multiples manières.";
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
        $expli[] = "Cet outil permet la visualisation et l'impression des appréciations des élèves pour les $nom_aid.";
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
		$titre[] = "Gestion des élèves";
		$expli[] = "Cet outil permet d'accéder aux informations des élèves dont vous êtes $gepi_prof_suivi.";
	}
}

$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
    //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
    echo "<table class='menu'>\n";
    echo "<tr>\n";
    echo "<th colspan='2'><img src='./images/icons/print.png' alt='Imprimer' class='link'/> - Visualisation - Impression</th>\n";
    echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}


// **********************************
// Gestion Notanet
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
		//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
		echo "<table class='menu'>\n";
		echo "<tr>\n";
		echo "<th colspan='2'><img src='./images/icons/document.png' alt='Notanet/Fiches Brevet' class='link'/> - Notanet/Fiches Brevet</th>\n";
		echo "</tr>\n";
		for ($i=0;$i<$nb_ligne;$i++) {
			affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
		}
		echo "</table>\n";
	}
}
// **********************************




// **********************************
// Gestion Années antérieures
if (getSettingValue("active_annees_anterieures")=='y') {
	$chemin = array();
	$titre = array();
	$expli = array();

	if($_SESSION['statut']=='administrateur'){
		$chemin[] = "/mod_annees_anterieures/index.php";
		$titre[] = "Années antérieures";
		$expli[] = "Cet outil permet de gérer et de consulter les données d'années antérieures (<i>bulletins simplifiés,...</i>).";
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
				$expli[] = "Cet outil permet de consulter les données d'années antérieures (<i>bulletins simplifiés,...</i>).";
			}
			elseif($AAProfPrinc=="yes"){
				$sql="SELECT 1=1 FROM classes c,
									j_eleves_professeurs jep
							WHERE jep.professeur='".$_SESSION['login']."' AND
									jep.id_classe=c.id
									ORDER BY c.classe";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0){
					$chemin[] = "/mod_annees_anterieures/index.php";
					$titre[] = "Années antérieures";
					$expli[] = "Cet outil permet de consulter les données d'années antérieures (<i>bulletins simplifiés,...</i>).";
				}
			}
		}
		elseif($_SESSION['statut']=='scolarite') {
			$AAScolTout=getSettingValue('AAScolTout');
			$AAScolResp=getSettingValue('AAScolResp');

			if($AAScolTout=="yes"){
				$chemin[] = "/mod_annees_anterieures/consultation_annee_anterieure.php";
				$titre[] = "Années antérieures";
				$expli[] = "Cet outil permet de consulter les données d'années antérieures (<i>bulletins simplifiés,...</i>).";
			}
			elseif($AAScolResp=="yes"){
				$sql="SELECT 1=1 FROM j_scol_classes jsc
								WHERE jsc.login='".$_SESSION['login']."';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0){
					$chemin[] = "/mod_annees_anterieures/consultation_annee_anterieure.php";
					$titre[] = "Années antérieures";
					$expli[] = "Cet outil permet de consulter les données d'années antérieures (<i>bulletins simplifiés,...</i>).";
				}
			}
		}
		elseif($_SESSION['statut']=='cpe') {
			$AACpeTout=getSettingValue('AACpeTout');
			$AACpeResp=getSettingValue('AACpeResp');

			if($AACpeTout=="yes"){
				$chemin[] = "/mod_annees_anterieures/consultation_annee_anterieure.php";
				$titre[] = "Années antérieures";
				$expli[] = "Cet outil permet de consulter les données d'années antérieures (<i>bulletins simplifiés,...</i>).";
			}
			elseif($AACpeResp=="yes"){
				$sql="SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='".$_SESSION['login']."'";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0){
					$chemin[] = "/mod_annees_anterieures/consultation_annee_anterieure.php";
					$titre[] = "Années antérieures";
					$expli[] = "Cet outil permet de consulter les données d'années antérieures (<i>bulletins simplifiés,...</i>).";
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
					$expli[] = "Cet outil permet de consulter les données d'années antérieures (<i>bulletins simplifiés,...</i>).";
				}
			}
		}
		elseif($_SESSION['statut']=='eleve') {
			$AAEleve=getSettingValue('AAEleve');

			if($AAEleve=="yes"){
				$chemin[] = "/mod_annees_anterieures/consultation_annee_anterieure.php";
				$titre[] = "Années antérieures";
				$expli[] = "Cet outil permet de consulter les données d'années antérieures (<i>bulletins simplifiés,...</i>).";
			}
		}
	}



	$nb_ligne = count($chemin);
	$affiche = 'no';
	for ($i=0;$i<$nb_ligne;$i++) {
		if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
	}
	if ($affiche=='yes') {
		//echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
		echo "<table class='menu'>\n";
		echo "<tr>\n";
		echo "<th colspan='2'><img src='./images/icons/document.png' alt='Années antérieures' class='link'/> - Années antérieures</th>\n";
		echo "</tr>\n";
		for ($i=0;$i<$nb_ligne;$i++) {
			affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
		}
		echo "</table>\n";
	}
}
// **********************************



// Gestion des messages

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
    //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
    echo "<table class='menu'>\n";
    echo "<tr>\n";
    //echo "<th colspan='2'><img src='./images/icons/mail.png' alt='Messagerie' class='link'/> - Messagerie</th>\n";
    echo "<th colspan='2'><img src='./images/icons/mail.png' alt='Messagerie' class='link'/> - Panneau d'affichage</th>\n";
    echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}
// **********************************



// EDT

$chemin = array();
$chemin[] = "/edt_organisation/index_edt.php";

$titre = array();
$titre[] = "Emploi du temps";

$expli = array();
$expli[] = "Cet outil permet la consultation/gestion de l'emploi du temps.";

$nb_ligne = count($chemin);
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
	// Ajout d'un test param_edt() pour savoir si l'admin a activé ou non le module EdT - Calendrier
if ($affiche=='yes' AND param_edt($_SESSION["statut"]) == 'yes') {
    //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
    echo "<table class='menu'>\n";
    echo "<tr>\n";
    echo "<th colspan='2'><img src='./images/icons/document.png' alt='Emploi du temps' class='link'/> - Emploi du temps</th>\n";
    echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}

//
// Module Ateliers
//

if (EstAutoriseAteliers($_SESSION["login"])) {
  $chemin = array();
  $titre = array();
  $expli = array();

  $chemin[]="/mod_ateliers/ateliers_accueil_admin.php";
  $titre[] = "Configuration du module Ateliers";
  $expli[] = "Configuration des événements, des disciplines, des professeurs, des salles.";

  $nb_ligne = count($chemin);
  $affiche = 'no';
  for ($i=0;$i<$nb_ligne;$i++) {
      if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
  }
  if ($affiche=='yes') {
      echo "<table class='menu'>\n";
      echo "<tr>\n";
      echo "<th colspan='2'><img src='./images/icons/document.png' alt='Inscription' class='link'/> - Module Ateliers </th>\n";
      echo "</tr>\n";
      for ($i=0;$i<$nb_ligne;$i++) {
          affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
      }
      echo "</table>";
  }
}

//
// Module d'inscription
//
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
      echo "<table class='menu'>\n";
      echo "<tr>\n";
      echo "<th colspan='2'><img src='./images/icons/document.png' alt='Inscription' class='link'/> - ".getSettingValue("mod_inscription_titre")." - Inscription </th>\n";
      echo "</tr>\n";
      for ($i=0;$i<$nb_ligne;$i++) {
          affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
      }
      echo "</table>";
  }
}

// Lien vers les flux rss pour les élèves s'ils sont activés
if (getSettingValue("rss_cdt_eleve") == 'y' AND $_SESSION["statut"] == "eleve") {
	// Les flux rss sont ouverts pour les élèves
	echo "
		<table class='menu'>
			<tr>
				<th colspan='2'>
					<img src='./images/icons/rss.png' alt='flux rss' class='link'/>
					 - Votre flux rss (syndication)
				</th>
			</tr>\n";
	// A vérifier pour les cdt
	if (getSettingValue("rss_acces_ele") == 'direct') {

		$uri_el = retourneUri($_SESSION["login"], $test_https, 'cdt');
		echo '
			<tr>
				<td title="A utiliser avec un lecteur de flux rss" style="cursor: pointer; color: blue;" onclick="changementDisplay(\'divuri\', \'divexpli\');">Votre uri pour les cahiers de textes</td>
				<td>
					<div id="divuri" style="display: none;">
						<a href="'.$uri_el.'" target="_blank">'.$uri_el.'</a></div>
					<div id="divexpli" style="display: block;">En cliquant sur la cellule de gauche, vous pourrez récupérer votre URI.</div>
				</td>
			</tr>
		';
	}elseif(getSettingValue("rss_acces_ele") == 'csv'){
		echo '
			<tr>
				<td>Votre uri pour les cahiers de textes</td>
				<td>Veuillez la demander à l\'administration de votre établissement.</td>
			</tr>
		';
	}

	echo '</table>';
}



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

	echo '<br />
		<table class="menu">
			<tr>
			<th colspan="2"><img src="./images/icons/document.png" alt="Inscription" class="link" />&nbsp;-&nbsp;Navigation</th></tr>';

	//for($a = 1 ; $a < $nbre ; $a++){
	$a = 1;
	while($a < $nbre_a){

		// On récupère le droit sur le fichier
		$sql_f = "SELECT autorisation FROM droits_speciaux WHERE id_statut = '".$_SESSION["special_id"]."' AND nom_fichier = '".$autorise[$a][0]."' ORDER BY id";
		$query_f = mysql_query($sql_f) OR trigger_error('Impossible de trouver le droit : '.mysql_error(), E_USER_WARNING);
		$nbre = mysql_num_rows($query_f);
		if ($nbre >= 1) {
			$rep_f = mysql_result($query_f, "autorisation");
		}else{
			$rep_f = '';
		}

		if ($rep_f == 'V') {
			if ($autorise[$a][0] == "/tous_les_edt") {
				// rien, la vérification se fait dans le module EdT
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

?>
</center>
</div>
</body>
</html>
