<?php
/*
 *
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

// Initialisations files
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

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href="../gestion/index.php#init_csv"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='export_tables.php'>Exporter au format CSV le contenu actuel des tables</a></p>

<p><strong>Vous allez effectuer l'initialisation de l'année scolaire qui vient de débuter.</strong><br />
(<em>c'est une opération que vous ne devez effectuer qu'<span style='color:red'>une seule fois par an</span>.<br />
<?php

	if(getSettingValue("import_maj_xml_sconet")==1) {
		echo "Pour mettre à jour la base avec les informations saisies en cours d'année dans Sconet pour les changements d'adresses, arrivées d'èlèves,...<br />il faut effectuer une <a href='../responsables/maj_import.php'>Mise à jour d'après Sconet</a></em>)<br />";
	}
	else {
		echo "L'initialisation d'année ne convient pas pour prendre en compte les changements d'adresses, arrivées d'èlèves,...</em>)<br />";
	}

	if((getSettingValue('use_sso')=="lcs")||(getSettingValue('use_sso')=="ldap_scribe")) {
		echo "<p style='color:red;'><b>ATTENTION&nbsp;:</b> Vous utilisez un serveur LCS ou SCRIBE.<br />
		Il existe un mode d'initialisation de l'année propre à <a href='../init_lcs/index.php'>LCS</a> d'une part et à SCRIBE d'autre part (<i><a href='../init_scribe/index.php'>Scribe</a> et <a href='../init_scribe_ng/index.php'>Scribe_ng</a></i>).<br />
		Si vous initialisez l'année avec le mode XML, vous ne pourrez pas utiliser les comptes de votre serveur LCS/SCRIBE par la suite pour accéder à GEPI.<br />Réfléchissez-y à deux fois avant de poursuivre.</p>\n";
		echo "<br />\n";
	}

	echo "<br />\n";

	echo "<p>Avez-vous pensé à effectuer les différentes opérations de fin d'année et préparation de nouvelle année à la page <a href='../gestion/changement_d_annee.php' style='font-weight:bold;'>Changement d'année</a>&nbsp?</p>\n";

	//===========================================================
	// Sauvegarde temporaire:
	$sql="CREATE TABLE IF NOT EXISTS tempo_utilisateurs
(login VARCHAR( 50 ) NOT NULL PRIMARY KEY,
password VARCHAR(128) NOT NULL,
salt VARCHAR(128) NOT NULL,
email VARCHAR(50) NOT NULL,
identifiant1 VARCHAR( 10 ) NOT NULL ,
identifiant2 VARCHAR( 50 ) NOT NULL ,
nom VARCHAR( 50 ) NOT NULL ,
prenom VARCHAR( 50 ) NOT NULL ,
statut VARCHAR( 20 ) NOT NULL ,
auth_mode ENUM('gepi','ldap','sso') NOT NULL default 'gepi',
date_reserve DATE DEFAULT '1970-01-01',
temoin VARCHAR( 50 ) NOT NULL
);";
	$creation_table=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="SELECT 1=1 FROM tempo_utilisateurs WHERE statut='responsable';";
	//if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		echo "<p style='margin:1em;padding:1em;' class='fieldset_opacite50'><strong style='color:red'>ATTENTION&nbsp;:</strong> ".mysqli_num_rows($test)." comptes responsables sont actuellement mis en réserve.<br />
		L'initialisation CSV ne permet par d'imposer le même login à des utilisateurs responsables.<br />
		Vous devriez supprimer maintenant les comptes mis en réserve pour éviter par exemple l'attribution du login de M.MARTIN Jean à Mme.DUBOIS Martine lorsque vous recréerez les compes responsables.<br />
		<a href='../gestion/changement_d_annee.php?suppr_reserve_resp=y".add_token_in_url()."' title=\"Cela supprime de la table 'tempo_utilisateurs', les comptes responsables.\" target='_blank'>Supprimer les comptes responsables mis en réserve</a></p>\n";
	}

/*


echo "<p>Pour pouvoir imposer les mêmes comptes parents et/ou élèves d'une année sur l'autre (<em>pour se connecter dans Gepi, consulter les cahiers de textes, les notes,...</em>), il convient avant d'initialiser la nouvelle année (<em>opération qui vide/nettoye un certain nombre de tables</em>) de mettre en réserve dans une table temporaire les login, mot de passe, email et statut des parents/élèves de façon à leur redonner le même login et restaurer l'accès lors de l'initialisation.</p>\n";

echo "<p>";
$sql="SELECT 1=1 FROM utilisateurs WHERE statut='eleve';";
if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)>0) {
	echo "Il existe actuellement ".mysqli_num_rows($test)." comptes élèves.<br />";
	$temoin_compte_ele="y";
}
else {
	echo "Il n'existe actuellement aucun compte élève.<br />";
	$temoin_compte_ele="n";
}
$sql="SELECT 1=1 FROM tempo_utilisateurs WHERE statut='eleve';";
if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)>0) {
	echo mysqli_num_rows($test)." comptes élèves sont actuellement mis en réserve";
	$sql="SELECT DISTINCT date_reserve FROM tempo_utilisateurs WHERE statut='eleve' ORDER BY date_reserve;";
	if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		echo " (<em>date de mise en réserve&nbsp;: ";
		$cpt=0;
		while($lig_res=mysqli_fetch_object($test)) {
			if($cpt>0) {echo ", ";}
			echo formate_date($lig_res->date_reserve);
			$cpt++;
		}
		echo "</em>)";
	}
	echo " - <a href='".$_SERVER['PHP_SELF']."?suppr_reserve_eleve=y".add_token_in_url()."' title=\"Cela supprime de la table 'tempo_utilisateurs', les comptes élèves. Cela ne supprime pas les comptes élèves actuellement enregistrés dans la table 'utilisateurs'. Vous pourrez donc refaire une mise en réserve des actuels comptes élèves tant que vous n'aurez pas lancé l'initialisation de la nouvelle année.\">Supprimer les comptes élèves mis en réserve</a>";
	$temoin_reserve_compte_ele="faite";
}
else {
	echo "Aucun compte élève n'est actuellement mis en réserve.<br />";
	$temoin_reserve_compte_ele="non_faite";
}
echo "</p>\n";

echo "<p>";
$sql="SELECT 1=1 FROM utilisateurs WHERE statut='responsable';";
if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)>0) {
	echo "Il existe actuellement ".mysqli_num_rows($test)." comptes responsables.<br />";
	$temoin_compte_resp="y";
}
else {
	echo "Il n'existe actuellement aucun compte responsable.<br />";
	$temoin_compte_resp="n";
}
$sql="SELECT 1=1 FROM tempo_utilisateurs WHERE statut='responsable';";
if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)>0) {
	echo mysqli_num_rows($test)." comptes responsables sont actuellement mis en réserve";
	$sql="SELECT DISTINCT date_reserve FROM tempo_utilisateurs WHERE statut='responsable' ORDER BY date_reserve;";
	if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		echo " (<em>date de mise en réserve&nbsp;: ";
		$cpt=0;
		while($lig_res=mysqli_fetch_object($test)) {
			if($cpt>0) {echo ", ";}
			echo formate_date($lig_res->date_reserve);
			$cpt++;
		}
		echo "</em>)";
	}
	echo " - <a href='".$_SERVER['PHP_SELF']."?suppr_reserve_resp=y".add_token_in_url()."' title=\"Cela supprime de la table 'tempo_utilisateurs', les comptes responsables. Cela ne supprime pas les comptes responsables actuellement enregistrés dans la table 'utilisateurs'. Vous pourrez donc refaire une mise en réserve des actuels comptes responsables tant que vous n'aurez pas lancé l'initialisation de la nouvelle année.\">Supprimer les comptes responsables mis en réserve</a>";
	$temoin_reserve_compte_resp="faite";
}
else {
	echo "Aucun compte responsable n'est actuellement mis en réserve.<br />";
	$temoin_reserve_compte_resp="non_faite";
}
echo "</p>\n";

*/



	/*
	$sql="SELECT 1=1 FROM matieres_notes LIMIT 1;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo "Avez-vous pensé à <a href='#' onmouseover=\"afficher_div('archivage','y',20,20);\" onclick=\"return false;\">archiver</a> l'année qui se termine ?</p>\n";
		$texte="<p>L'archivage de l'année en cours vous permettra, une fois passé à l'année suivante, de consulter les bulletins antérieurs de chacun de vos élèves, pour peu qu'ils aient été scolarisés dans votre établissement.</p><p>Cela nécessite l'activation du <a href='../mod_annees_anterieures/admin.php'>module 'Années antérieures'</a>.</p>";
		$tabdiv_infobulle[]=creer_div_infobulle('archivage',"Archivage d'une année","",$texte,"",30,0,'y','y','n','n');
	}
	else {
		echo "</p>\n";
	}

	// CDT
	$sql="SELECT 1=1 FROM ct_entry LIMIT 1;";
	$test1=mysql_query($sql);
	$sql="SELECT 1=1 FROM ct_devoirs_entry LIMIT 1;";
	$test2=mysql_query($sql);
	if((mysql_num_rows($test1)>0)||(mysql_num_rows($test2)>0)) {
		echo "<p>Les cahiers de textes ne sont pas vides.<br />Vous devriez <a href='../cahier_texte_admin/admin_ct.php'>vider les cahiers de textes de l'an dernier</a> avant de procéder à l'initialisation.</p>\n";
	}
	*/
?>
<!--/p-->
<ul>
<li><p>Au cours de la procédure, le cas échéant, certaines données de l'année passée seront définitivement effacées de la base GEPI (<em>élèves, notes, appréciations, ...</em>) . Seules seront conservées les données suivantes, qui seront seulement mises à jour si nécessaire :<br /><br />
- les données relatives aux établissements,<br />
- les données relatives aux classes : intitulés courts, intitulés longs, nombre de périodes et noms des périodes,<br />
- les données relatives aux matières : identifiants et intitulés complets,<br />
- les données relatives aux utilisateurs (<em>professeurs, administrateurs,...</em>). Concernant les professeurs, les matières enseignées par les professeurs sont conservées,<br />
- Les données relatives aux différents types d'AID.<br />&nbsp;</p></li>

<li>
	<?php
	//==================================
	// RNE de l'établissement pour comparer avec le RNE de l'établissement de l'année précédente
	$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
	//==================================
	if($gepiSchoolRne=="") {
		echo "<p><b style='color:red;'>Attention&nbsp;:</b> Le RNE de l'établissement n'est pas renseigné dans 'Gestion générale/<a href='../gestion/param_gen.php' target='_blank'>Configuration générale</a>'<br />Cela peut perturber l'import de l'établissement d'origine des élèves.<br />Vous devriez corriger avant de poursuivre.</p>\n";
	}
	?>

	<p>L'initialisation s'effectue en plusieurs phases successives, chacune nécessitant un fichier CSV spécifique, que vous devrez fournir au bon format :</p>
    <ul>
    <li><p><a name="eleves"></a><a href='eleves.php'>Procéder à la première phase</a> d'importation des élèves. <b>g_eleves.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre les champs suivants :
    	<br/>Nom ; Prénom ; Date de naissance ; n° identifiant interne (étab) ; n° identifiant national ; Code établissement précédent ; Doublement (OUI | NON) ; Régime (INTERN | EXTERN | IN.EX. | DP DAN) ; Sexe (F ou M)<br />&nbsp;</p></li>

    <li><p><a name="responsables"></a><a href='responsables.php'>Procéder à la deuxième phase</a> d'importation des responsables des élèves : le fichier <b>g_responsables.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>n° d'identifiant élève interne à l'établissement ; Nom du responsable ; Prénom du responsable ; Civilité ;  Ligne 1 Adresse ; Ligne 2 Adresse ; Code postal ; Commune<br />&nbsp;</p></li>

    <li><p><a name="disciplines"></a><a href='disciplines.php'>Procéder à la troisième phase</a> d'importation des matières : le fichier <b>g_disciplines.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>Nom court matière ; Nom long matière<br />&nbsp;</p></li>

    <li><p><a name="professeurs"></a><a href='professeurs.php'>Procéder à la quatrième phase</a> d'importation des professeurs : le fichier <b>g_professeurs.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>Nom ; Prénom ; Civilité ; Adresse e-mail<br />&nbsp;</p></li>

    <li><p><a name="eleves_classes"></a><a href='eleves_classes.php'>Procéder à la cinquième phase</a> d'affectation des élèves aux classes  : le fichier <b>g_eleves_classes.csv</b> requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>n° d'identifiant élève interne à l'établissement ; Identifiant court de la classe
    	<br/><em>Remarque&nbsp;:</em> cette opération créé automatiquement les classes dans Gepi, mais ne leur attribue qu'un nom court (identifiant). Vous devrez ajouter le nom long par l'interface de gestion des classes.<br />&nbsp;</p></li>


    <li><p><a name="prof_disc_classes"></a><a href='prof_disc_classes.php'>Procéder à la sixième phase</a> d'affectation des matières à chaque professeur et d'affectation des professeurs dans chaque classe : le fichier <b>g_prof_disc_classes.csv</b> requis. Cette importation va définir les compétences des professeurs et créer les groupes d'enseignement dans chaque classe.
    	<br />Il doit contenir, dans l'ordre, les champs suivants :
    	<br />Login du professeur ; Nom court de la matière ; Le ou les identifiants de classe (séparés par des !) ; Le type de cours (CG (= cours général) | OPT (= option))
    	<br /><em>Remarques&nbsp;:</em>
    	<br />Si le dernier champ est vide et qu'une seule classe est présente dans le troisième champ, le type sera défini comme "général". S'il est vide et que plusieurs classes ont été définies, alors le type sera défini comme "option".
    	<br />Lorsque l'enseignement est général, tous les élèves de la classe sont automatiquement associés à cet enseignement.
    	<br />Lorsque l'enseignement est une option, aucun élève n'y est associé, l'association se faisant à la septième étape.
    	<br /><b>Attention&nbsp;!</b> Ne mettez plusieurs classes pour une même matière que s'il s'agit d'un seul enseignement ! Si un professeur enseigne la même matière dans deux classes différentes, il faut alors deux lignes distinctes dans le fichier CSV, avec une seule classe définie pour chaque ligne.<br />&nbsp;</p></li>

    <li><p><a name="eleves_options"></a><a href='eleves_options.php'>Procéder à la septième phase</a> d'affectation des élèves à chaque groupe d'option : le fichier <b>g_eleves_options.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>n° d'identifiant élève interne à l'établissement ; Identifiants des matières suivies en option, séparés par des !
    	<br/>Remarque : si plusieurs groupes avec la même matière sont trouvés dans la classe de l'élève, alors l'élève sera associé à tous ces différents groupes.<br />&nbsp;</p></li>
    </ul>
	<br />
</li>
<li><p>Une fois toute la procédure d'initialisation des données terminée, il conviendra d'effectuer une opération de conversion des informations élèves et responsables en consultant la page <a href='../eleves/index.php'>Gestion des élèves</a>.</p>
<p>Il vous sera par ailleurs possible d'effectuer toutes les modifications nécessaires au cas par cas par le biais des outils de gestion inclus dans <b>GEPI</b>.</p></li>
</ul>
<p><br /></p>

<p><b>ATTENTION&nbsp;:</b> Le <em>n° d'identifiant élève interne à l'établissement</em> ne doit être constitué que de chiffres.</p>
<p><br /></p>

<?php require("../lib/footer.inc.php");?>
