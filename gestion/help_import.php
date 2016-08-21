<?php
/*
 * Last modification  : 14/03/2005
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


//**************** EN-TETE *****************
$titre_page = "Aide en ligne";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>

<p class=bold>Aide à l'importation</p>
<p>Le fichier d'importation doit être au format csv (séparateur : point-virgule)
<br />Le fichier doit contenir les différents champs suivants, tous obligatoires :<br />
--> <B>IDENTIFIANT</B> : l'identifiant de l'élève<br />
--> <B>Nom</B><br />
--> <B>Prénom</B><br />
--> <B>Sexe</B>  : F ou M<br />
--> <B>Date de naissance</B> : jj/mm/aaaa<br />
--> <B>Classe (fac.)</B> : le nom court d'une classe déjà définie dans la base GEPI ou bien le caractère - si l'élève n'est pas affecté à une classe.<br />
--> <B>Régime</B> : d/p (demi-pensionnaire) ext. (externe) int. (interne) ou i-e (interne externé(e))<br />
--> <B>Doublant</B> : R (pour un doublant)  - (pour un non-doublant)<br />
--> <B><?php echo ucfirst(getSettingValue("gepi_prof_suivi")); ?></B> : l'identifiant d'un <?php echo getSettingValue("gepi_prof_suivi"); ?> déjà défini dans la base GEPI ou bien le caractère - si l'élève n'a pas de <?php echo getSettingValue("gepi_prof_suivi"); ?>.<br />
--> <B>Identifiant de l'établissement d'origine </B> : le code RNE identifiant chaque établissement scolaire et déjà défini dans la base GEPI, ou bien le caractère - si l'établissement n'est pas connu.<br /></p>

<p class='bold'>IDENTIFIANT</p>
<p>Identifiant de l'élève : il peut s'agir de n'importe quelle suite de caractères et/ou de chiffres sans espace. Si ce format n'est pas respecté, la suite de caractères ??? apparaît à la place de l'identifiant. Les identifiants qui apparaissent en rouge correspondent à des noms d'utilisateur déjà existants dans la base GEPI. Les données existantes seront alors écrasées par les données présentes dans le fichier à importer !</p>
<p class='bold'>Nom</p>
<p>Nom de l'élève. Il peut s'agir de n'importe quelle suite de caractères et/ou de chiffres avec éventuellement des espaces</p>
<p class='bold'>Prénom</p>
<p>Prénom de l'élève. Même remarque que pour le nom. Les noms et prénoms qui apparaissent en bleu correspondent à des élèves existant dans la base GEPI et portant les mêmes noms et prénoms.</p>
<p class='bold'>Sexe</p>
<p>Les seuls caractères acceptés sont F pour féminin et M pour masculin (respectez les majuscules). Si ce format n'est pas respecté, la suite de caractères ??? apparaît.</p>
<p class='bold'>Date de naissance</p>
<p>Il s'agit de la date de naissance de l'élève. Le seul format autorisé est jj/mm/aaaa. Par exemple, pour un élève né le 15 avril 1985, on tapera 15/04/1985. Si ce format n'est pas respecté, la suite de caractères ??? apparaît.</p>
<p class='bold'>Classe</p>
<p>Classe dans laquelle l'élève est affecté. Les seuls données acceptées sont :
<br />--> le nom court d'une classe déjà définie dans la base GEPI
<br />--> ou bien le caractère - si l'élève n'est pas affecté à une classe.
<br />Si la classe n'est pas définie dans la base GEPI, celle-ci sera considérée comme erronée.
<br />La procédure d'importation ne permet pas de changer un élève de classe.
<br />En revanche, il est possible d'affecter à une classe, un élève existant de la base qui n'est pas déjà affecté à une classe.<br /></p>
<p class='bold'>Régime</p>
<p>Les seules suites de caractères acceptées sont "d/p", "ext.", "int." et "i-e" (respectez les minuscules). Dans tous les autres cas, la suite de caractères ??? apparaît.
<br />--> d/p pour demi-pensionnaire,
<br />--> ext. pour externe,
<br />--> int. pour interne,
<br />--> i-e  pour interne externé(e).</p>
<p class='bold'>Doublant</p>
<p>Les seuls caractères acceptés sont "R" et "-". Dans tous les autres cas, la suite de caractère ??? apparaît.
<br />--> R pour un doublant,
<br />--> - pour un non-doublant.</p>
<p class='bold'><?php echo ucfirst(getSettingValue("gepi_prof_suivi")); ?></p>
<p>L'identifiant d'un <?php echo getSettingValue("gepi_prof_suivi"); ?> déjà défini dans la base GEPI ou bien le caractère - si l'élève n'a pas de <?php echo getSettingValue("gepi_prof_suivi"); ?>.
<br />Il s'agit obligatoirement d'un professeur de la classe de l'élève. Dans le cas contraire, la suite de caractères ??? apparaît. Il en est de même si la classe n'est pas définie.</p>
<p class='bold'>Identifiant de l'établissement d'origine </p>
<p>Le code RNE identifiant chaque établissement scolaire et déjà défini dans la base GEPI, ou bien le caractère - si l'établissement n'est pas connu.<br /></p>
<?php require("../lib/footer.inc.php");?>