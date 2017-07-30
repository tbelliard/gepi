<?php

/*
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

if ($resultat_session == '0') {

   header("Location: ../logout.php?auto=1");

   die();

};

//**************** EN-TETE *****************

require_once("../lib/header.inc.php");

//**************** FIN EN-TETE *************



?>

<h1 class='gepi'>GEPI - Informations générales</h1>

<?php

$duree_max_session=getSettingValue("sessionMaxLength");
$session_gc_maxlifetime=ini_get("session.gc_maxlifetime");
$session_gc_maxlifetime_minutes=$session_gc_maxlifetime/60;
if((getSettingValue("sessionMaxLength")!="")&&($session_gc_maxlifetime_minutes<getSettingValue("sessionMaxLength"))) {
	$duree_max_session=$session_gc_maxlifetime_minutes;
}

echo "Vous êtes actuellement connecté sur l'application <b>GEPI (".getSettingValue("gepiSchoolName").")</b>.

<br />Par sécurité, si vous n'envoyez aucune information au serveur <em>(activation d'un lien ou soumission d'un formulaire)</em> pendant plus de <b>".$duree_max_session." minutes</b>, vous serez automatiquement déconnecté de l'application.";

echo "<h2>Administration de l'application GEPI</h2>\n";

echo "<table cellpadding='5' summary='Infos'>\n";

echo "<tr><td>Nom et prénom de l'administrateur : </td><td><b>".getSettingValue("gepiAdminNom")." ".getSettingValue("gepiAdminPrenom")."</b></td></tr>\n";

echo "<tr><td>Fonction de l'administrateur : </td><td><b>".getSettingValue("gepiAdminFonction")."</b></td></tr>\n";

echo "<tr><td>Email de l'administrateur : </td><td><b><a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">".getSettingValue("gepiAdminAdress")."</a></b></td></tr>\n";

echo "<tr><td>Nom de l'établissement : </td><td><b>".getSettingValue("gepiSchoolName")."</b></td></tr>\n";

echo "<tr><td Valign='top'>Adresse : </td><td><b>".getSettingValue("gepiSchoolAdress1")."<br />".getSettingValue("gepiSchoolAdress2")."<br />".getSettingValue("gepiSchoolZipCode")." ".getSettingValue("gepiSchoolCity")."</b></td></tr>\n";

echo "</table>\n";



echo "<h2>Objectifs de l'application GEPI</h2>\n";

echo "L'objectif de GEPI est la <b>gestion pédagogique des élèves et de leur scolarité</b>.

Dans ce but, des données sont collectées et stockées dans une base unique de type MySql.";



echo "<h2>Obligations de l'utilisateur</h2>\n";

echo "Les membres de l'équipe pédagogique sont tenus de remplir les rubriques qui leur ont été affectées par l'administrateur

lors du paramétrage de l'application.";

echo "<br />Il est possible de modifier le contenu d'une rubrique tant que la période concernée n'a pas été close par l'administrateur.";



echo "<h2>Destinataires des données relatives au bulletin scolaire</h2>\n";

echo "Concernant le bulletin scolaire, les données suivantes sont récoltées auprès des membres de l'équipe pédagogique :

<ul><li>absences (pour chaque période : nombre de demi-journées d'absence, nombre d'absences non justifiées, nombre de retards, observations)</li>

<li>moyennes et appréciations par matière,</li>

<li>moyennes et appréciations par projet inter-disciplinaire,</li>

<li>avis du conseil de classe.</li>

</ul>

Toutes ces informations sont intégralement reproduites sur un bulletin à la fin de chaque période <em>(voir ci-dessous)</em>.

<br /><br />

Ces données servent à :

<ul>

<li>l'élaboration d'un bulletin à la fin de chaque période, édité par le service scolarité et communiqué à l'élève

et à ses responsables légaux : notes obtenues, absences, moyennes, appréciations des enseignants, avis du conseil de classe.</li>

<li>l'élaboration d'un document de travail reprenant les informations du bulletin officiel et disponible pour les membres de l'équipe pédagogique de la classe concernée.</li>

</ul>\n";



//On vérifie si le module cahiers de texte est activé
if (getSettingValue("active_cahiers_texte")=='y') {

    echo "<h2>Destinataires des données relatives au cahier de texte</h2>\n";

    echo "Conformément aux directives de l'Education Nationale, chaque professeur dispose dans GEPI d'un cahier de texte pour chacune de ses classes qu'il peut tenir à jour

    en étant connecté.

    <br />

    Le cahier de texte relate le travail réalisé en classe :

    <ul>

    <li>projet de l'équipe pédagogique,</li>

    <li>contenu pédagogique de chaque séance, chronologie, objectif visé, travail à faire ...</li>

    <li>documents divers,</li>

    <li>évaluations, ...</li>

    </ul>

    Il constitue un outil de communication pour l'élève, les équipes disciplinaires

    et pluridisciplinaires, l'administration, le chef d'établissement, les corps d'inspection et les familles.

    <br /> Les cahiers de texte sont accessibles en ligne.";

    if (getSettingAOui("cahier_texte_acces_public")) {
	    if ((getSettingValue("cahiers_texte_login_pub") != '')&&(getSettingValue("cahiers_texte_passwd_pub") != '')) {

		 echo " <b>En raison du caractère personnel du contenu, l'accès à l'interface de consultation publique est restreint</b>. Pour accéder aux cahiers de texte, il est nécessaire de demander auprès de l'administrateur,
		 le nom d'utilisateur et le mot de passe valides.";

	    } else {

		 echo " <b>L'accès à l'interface de consultation publique est entièrement libre et n'est soumise à aucune restriction.</b>\n";

	    }
	}
	else {
		 echo " <b>Il est nécessaire de se connecter avec son compte/mot de passe pour accéder à son cahier de textes.</b>\n";
	}
}

//On vérifie si le module carnet de notes est activé
if (getSettingValue("active_carnets_notes")=='y') {

    echo "<h2>Destinataires des données relatives au carnet de notes</h2>\n";

    echo "Chaque professeur dispose dans GEPI d'un carnet de notes pour chacune de ses classes, qu'il peut tenir à jour

    en étant connecté.

    <br />

    Le carnet de note permet la saisie des notes et/ou des commentaires de tout type d'évaluation <em>(formatives, sommatives, oral, TP, TD, ...)</em>.

    <br /><b>Le professeur s'engage à ne faire figurer dans le carnet de notes que des notes et commentaires portés à la connaissance de l'élève <em>(note et commentaire portés sur la copie, ...)</em>.</b>

    Ces données stockées dans GEPI n'ont pas d'autre destinataire que le professeur lui-même et le ou les professeurs principaux de la classe.

    <br />Les notes peuvent servir à l'élaboration d'une moyenne qui figurera dans le bulletin officiel à la fin de chaque période.";

}

//On vérifie si le plugin suivi_eleves est activé
$test_plugin = sql_query1("select ouvert from plugins where nom='suivi_eleves'");
if ($test_plugin=='y') {
    echo "<h2>Destinataires des données relatives au module de suivi des élèves</h2>\n";

    echo "Chaque professeur dispose dans GEPI d'un outil de suivi des élèves (\"observatoire\") pour chacune de ses classes, qu'il peut tenir à jour

    en étant connecté.

    <br />

    Dans l'observatoire, le professeur a la possibilité d'attribuer à chacun de ses élèves un code pour chaque période.

    Ces codes et leur signification sont paramétrables par les administrateurs de l'observatoire désignés par l'administrateur général de GEPI.

    <br />.

    Le professeur dispose également de la possibilité de saisir un commentaire pour chacun de ses élèves

    dans le respect de la loi et dans le cadre strict de l'Education Nationale.

    <br /><br />L'observatoire et les données qui y figurent sont accessibles à l'ensemble de l'équipe pédagogique de l'établissement.

    <br /><br />Dans le respect de la loi informatique et liberté 78-17 du 6 janvier 1978, chaque élève a également accès dans son espace GEPI aux données qui le concernent";

}
require("../lib/footer.inc.php");
?>
