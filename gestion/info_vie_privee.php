<?php

/*
 *
 * Copyright 2001-2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


//**************** EN-TETE *****************
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

?>
<H1 class='gepi'>GEPI - Vie Privée</H1>
<?php
echo "<h2>Cadre légal</h2>";
echo "<p>Gepi est un logiciel de traitement de données entrant dans le cadre des Environnements Numériques de Travail (ENT).";
echo "<br/>A ce titre, il est soumis à un encadrement légal particulier. Nous vous invitons à consulter <a href='https://www.cnil.fr/fr/declaration/ru-003-espaces-numeriques-de-travail'>l'Arrêté du 30 novembre 2006</a> relatif aux dispositifs de traitement de données au sein du ministère de l'éducation nationale.</p>";

if (getSettingValue("num_enregistrement_cnil") != '')  {

echo "<h2>Déclaration à la CNIL</h2>";

echo "Conformément à l'article 16 de la loi 78-17 du 6 janvier 1978, dite loi informatique et liberté, nous vous informons

 que le présent site a fait l'objet d'une déclaration de traitement automatisé d'informations nominatives auprès de la CNIL

  : le site est enregistré sous le n° ".getSettingValue("num_enregistrement_cnil");

}

echo "<a name='cookies'></a><H2>1/ Cookies</H2>";

echo "A chacune de vos visites GEPI tente de générer un cookie de session. L'acceptation de ce cookie par votre navigateur est obligatoire pour accéder au site. Ce cookie de session est un cookie temporaire exigé pour des raisons de sécurité. Ce type de cookie n'enregistre pas d'information sur votre ordinateur, il vous attribue un numéro de session qu'il communique au serveur pour pouvoir suivre votre session en toute sécurité. Il est mis temporairement dans la mémoire de votre ordinateur et est exploitable uniquement durant le temps de connexion. Il est ensuite détruit lorsque vous vous déconnectez ou lorsque vous fermez toutes les fenêtres de votre navigateur.";

echo "<div class='fieldset_opacite50' style='margin:1em; margin-left:3em; width:70em;'>";
if((isset($_COOKIE))&&(count($_COOKIE)>0)) {
	echo "<p style='margin-left:3em; text-indent:-3em;'>Le ou les cookies actuellement définis sont&nbsp;:<br />";
	foreach($_COOKIE as $key => $value) {
		if(is_array($value)) {
			echo "<strong>$key&nbsp;:</strong><br />";
			echo "<pre>";
			print_r($value);
			echo "</pre>";
		}
		else {
			echo "<strong>$key&nbsp;:</strong> $value";
			if($key=="GEPI_start_session") {
				echo " <em>(soit le ".strftime("%d/%m/%Y à %H:%M:%S").")</em>";
			}
			elseif($key=="GEPI") {
				echo " <em>(identifiant aléatoire de session, sans signification)</em>";
			}
			elseif($key=="RNE") {
				echo " <em>(identifiant RNE/UAJ de l'établissement utilisé dans le cas d'une installation multisite de Gepi)</em>";
			}
			elseif($key=="displayCookieConsent") {
				echo " <em>(indique que vous avez accepté les COOKIES du site)</em>";
			}
			echo "<br />";
		}
	}
	echo "</p>";
}
else {
	echo "Aucun cookie n'est défini.";
}
echo "</div>
<p>Lorsqu'il en est déclaré, les cookies propres à Gepi sont GEPI, GEPI_start_session et RNE.<br />
Si d'autres cookies apparaissent, ils correspondent à des services supplémentaires, éventuellement mis en place par l'administrateur du site.</p>";
//debug_var();

echo "<H2>2/ Informations transmises</H2>";



echo "Lors de l'ouverture d'une session certaines informations sont transmises au serveur :

<ul>

<li>le numéro de votre session (voir ci-dessus),</li>

<li>votre identifiant,</li>

<li>l'adresse IP de votre machine,</li>

<li>le type de votre navigateur,

<li>l'origine de la connexion au présent site,</li>

<li>les heures et dates de début et fin de la session.</li>

</ul>";

switch (getSettingValue("duree_conservation_logs")) {

case 30:

$duree="un mois";

break;

case 60:

$duree="deux mois";

break;

case 183:

$duree="six mois";

break;

case 365:

$duree="un an";

break;

}

echo "Pour des raisons de sécurité, ces informations sont conservées pendant <b>".$duree."</b> à partir de leur enregistrement.<br />
<em>(la durée de conservation peut être modifiée en administrateur dans la rubrique Gestion générale/Options de connexion)</em>";



echo "<H2>3/ Sécurité</H2>";

echo "<b>Par mesure de sécurité, pensez à vous déconnecter à la fin de votre visite sur le site (lien en haut à droite).</b>";

require("../lib/footer.inc.php");
?>
