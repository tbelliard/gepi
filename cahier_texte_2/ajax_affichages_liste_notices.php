<?php
/*
 * $Id$
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
header('Content-Type: text/html; charset=ISO-8859-1');
// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
include("include_affiche_notices_vignettes.php");
//echo("Debug Locale : ".setLocale(LC_TIME,0));

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=3");
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

//récupération des paramètres
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
$affiche_tout = isset($_POST["affiche_tout"]) ? $_POST["affiche_tout"] :(isset($_GET["affiche_tout"]) ? $_GET["affiche_tout"] :NULL);
//date présente
$aujourdhui = mktime(0,0,0,date("m"),date("d"),date("Y"));

//utile uniquement pour la completion
//$devoir = new CahierTexteTravailAFaire();
//$compte_rendu = new CahierTexteCompteRendu();
//$notice_privee = new CahierTexteNoticePrivee();
//$liste_comptes_rendus = new PropelObjectCollection();

//récupération du groupe courant
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

$current_group = null;
$groups = $utilisateur->getGroupes();
foreach ($groups as $group) {
	if ($id_groupe == $group->getId()) {
		$current_group = $group;
		break;
	}
}
if ($current_group == null) {
	echo("groupe non spécifié ou non trouvé.");
	die();
}

// **********************************************
// Affichage des différents groupes du professeur
//\$A($('id_groupe_colonne_gauche').options).find(function(option) { return option.selected; }).value javascript trick to get selected value.
echo ("<select id=\"id_groupe_colonne_gauche\" onChange=\"javascript:
			updateEditionNoticeChaine();
			selected_group = (\$A($('id_groupe_colonne_gauche').options).find(function(option) { return option.selected; }).value);
			new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + selected_group);
		\">");
echo "<option value='-1'>choisissez un groupe</option>\n";
foreach ($utilisateur->getGroupes() as $group) {
	echo "<option id='colonne_gauche_select_group_option_".$group->getId()."' value='".$group->getId()."'";
	if ($current_group->getId() == $group->getId()) echo " SELECTED ";
	echo ">";
	echo $group->getDescriptionAvecClasses();
	echo "</option>\n";
}
echo "</select>&nbsp;";
echo "<div id=\"div_chaine_liste_notices\" style=\"display:inline;\"><img id=\"chaine_liste_notice\" onLoad=\"updateChaineIcones()\" HEIGHT=\"16\" WIDTH=\"16\" style=\"border: 0px; vertical-align : middle\" src=\"../images/blank.gif\"  alt=\"Lier\" title=\"Lier la liste avec la fenetre edition de notices\" /></div>";
//fin affichage des groupes

echo "<p style='font-size:9pt'>";
if(getSettingValue('cahier_texte_acces_public')!='no'){
	echo "<a href='../public/see_all.php?id_groupe=" . $current_group->getId() ."' target='_blank'>Visualiser l'accès public</a>\n<br>";
} else {
	//$classes_du_groupe = $current_group->getClasses();
	//echo "<a href='./see_all.php?year=". date("Y") ."&month=". date("m") ."&day=". date("d") ."&id_classe=" . $classes_du_groupe[0]->getId() ."&id_groupe=" . $current_group->getId() ."'>Visualiser les cahiers de textes</a>\n<br>";
	echo "<a href='./see_all.php?id_groupe=" . $current_group->getId() ."'>Visualiser les cahiers de textes</a>\n<br>";
}
echo "Export au <a href='./exportcsv.php?id_groupe=".$current_group->getId()."'>format csv</a> / <a href='./export_cdt.php?id_groupe=".$current_group->getId()."'>format html</a><br/>";
//echo "<p style=\"background-color: silver; padding: 2px; border: 1px solid black; font-weight: bold;\">" . $current_group->getDescriptionAvecClasses() . "</p><br/>\n";

if ((getSettingValue("cahiers_texte_login_pub") != '') and (getSettingValue("cahiers_texte_passwd_pub") != ''))
echo "<br />(Identifiant : ".getSettingValue("cahiers_texte_login_pub")." - Mot de passe : ".getSettingValue("cahiers_texte_passwd_pub").")\n";

// recherche des "travaux à faire" futurs, toute matieres confondues, pour afficher le nombre total de devoirs pour une classe
$debutCdt = getSettingValue("begin_bookings");
foreach ($current_group->getClasses() as $classe) {
	$total[$classe->getId()] = null;
	$date[$classe->getId()] = null;
	$groups = $classe->getGroupes();
	foreach ($groups as $group) {
		$req_total =
			"select count(id_ct) total, max(date_ct) date
			from ct_devoirs_entry
			where (id_groupe = '" . $group->getId() . "'
			and date_ct > $aujourdhui)";
		$res_total = mysql_query($req_total);
		$sum = mysql_fetch_object($res_total);
		$total[$classe->getId()] += $sum->total;
		if ($sum->date > $date[$classe->getId()]) $date[$classe->getId()] = $sum->date;
	}
}

// Affichage des travaux à  faire futurs, toutes matières confondues
foreach ($current_group->getClasses() as $classe) {
	if ($total[$classe->getId()] > 0) {
		echo"La classe " . $classe->getNom() . " a  <a href=\"javascript:centrerpopup('liste_tous_devoirs.php?classe=". $classe->getId()."&amp;debut=$aujourdhui',260,320,'scrollbars=yes,statusbar=no,resizable=yes');\">" . $total[$classe->getId()];
		echo (($total[$classe->getId()] == 1) ? " travail personnel" : "travaux personnels");
		echo "</a> jusqu'au " . strftime("%A %d %B %Y", $date[$classe->getId()]) . ".\n<br style='font-size:2px;'/>";
	}
}

echo "</p>";
$compteur_nb_total_notices = 0;

//récupération de $liste_comptes_rendus : comptes rendus pour la matière en cours
$criteria = new Criteria(CahierTexteCompteRenduPeer::DATABASE_NAME);
$criteria->add(CahierTexteCompteRenduPeer::DATE_CT, "0", "!=");
$criteria->add(CahierTexteCompteRenduPeer::DATE_CT, null, Criteria::ISNOTNULL);
$criteria->add(CahierTexteCompteRenduPeer::DATE_CT, $debutCdt, ">=");
$criteria->addDescendingOrderByColumn(CahierTexteCompteRenduPeer::DATE_CT);
$criteria->addAscendingOrderByColumn(CahierTexteCompteRenduPeer::HEURE_ENTRY);
$liste_comptes_rendus = $current_group->getCahierTexteCompteRendus($criteria);
$compteur_nb_total_notices = $compteur_nb_total_notices + $liste_comptes_rendus->count();

//récupération de $liste_devoir : devoirs pour la matière en cours
$criteria = new Criteria(CahierTexteTravailAFairePeer::DATABASE_NAME);
$criteria->add(CahierTexteTravailAFairePeer::DATE_CT, $debutCdt, ">=");
$criteria->addDescendingOrderByColumn(CahierTexteTravailAFairePeer::DATE_CT);
$liste_devoir = $current_group->getCahierTexteTravailAFaires($criteria);
$compteur_nb_total_notices = $compteur_nb_total_notices + $liste_devoir->count();

//récupération de $liste_notice_privee :
$criteria = new Criteria();
$criteria->add(CahierTexteNoticePriveePeer::DATE_CT, $debutCdt, ">=");
$criteria->addDescendingOrderByColumn(CahierTexteNoticePriveePeer::DATE_CT);
$liste_notice_privee = $current_group->getCahierTexteNoticePrivees($criteria);
$compteur_nb_total_notices = $compteur_nb_total_notices + $liste_notice_privee->count();

// Boucle d'affichage des notices dans la colonne de gauche
$compteur_notices_affiches = 0;
$date_ct_old = -1;
while (true) {
	$devoir = $liste_devoir->getCurrent();
	$compte_rendu = $liste_comptes_rendus->getCurrent();
	$notice_privee = $liste_notice_privee->getCurrent();
	if ($affiche_tout != "oui") {
	    if ($liste_devoir->getPosition() > 6) { $devoir = null; }
	    if ($liste_comptes_rendus->getPosition() > 6) { $compte_rendu = null; }
	    if ($liste_notice_privee->getPosition() > 6) { $notice_privee = null; }
	}

	
	//si $devoir n'est pas nul et que la date du devoir est posterieure à celle du compte rendu
	if ($compte_rendu != null && ($devoir == null || $compte_rendu->getDateCt() >= $devoir->getDateCt() ) && ($notice_privee == null || $compte_rendu->getDateCt() >= $notice_privee->getDateCt() )) {

		//si $compte_rendu n'est pas nul et que la date du $compte_rendu est posterieure à celle du devoir
		
		$compteur_notices_affiches = $compteur_notices_affiches + 1;
		affiche_compte_rendu_vignette($compte_rendu, $couleur_bord_tableau_notice, $color_fond_notices);
		$liste_comptes_rendus->getNext();

	} elseif ($notice_privee != null && ($compte_rendu == null || $notice_privee->getDateCt() >= $compte_rendu->getDateCt()) && ($devoir == null || $notice_privee->getDateCt() >= $devoir->getDateCt() )) {

		$compteur_notices_affiches = $compteur_notices_affiches + 1;
		affiche_notice_privee_vignette($notice_privee, $couleur_bord_tableau_notice, $color_fond_notices);
		$liste_notice_privee->getNext();

	} elseif ($devoir != null && ($compte_rendu == null || $devoir->getDateCt() >= $compte_rendu->getDateCt()) && ($notice_privee == null || $devoir->getDateCt() >= $notice_privee->getDateCt() )) {


		$compteur_notices_affiches = $compteur_notices_affiches + 1;
		affiche_devoir_vignette($devoir, $couleur_bord_tableau_notice, $color_fond_notices);
		$liste_devoir->getNext();

	} else {
		//on a tout affiché
		break;
	}
}

// Ajout d'un lien pour aficher plus de notices
if ($compteur_nb_total_notices > 1)
$legend = "Actuellement : ".$compteur_notices_affiches." notices affichées sur un total de ".$compteur_nb_total_notices."<br />";
else if ($compteur_nb_total_notices == 1)
$legend = "Actuellement : 1 notice.<br />";
else
$legend = "";
if ($compteur_nb_total_notices > $compteur_notices_affiches) {
	echo "<fieldset style=\"border: 1px solid grey; font-size: 0.8em; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">";
	echo $legend;
	echo "<a href=\"#\" onclick=\"javascript:
			new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?affiche_tout=oui&id_groupe=".$current_group->getId()."',
					{ onComplete:
						function(transport) {
							updateDivModification();
						}
					}
				);;
			return false;\">";
	echo "Afficher&nbsp;toutes&nbsp;les&nbsp;notices</a>\n";
	echo "</fieldset>";
}

// Affichage des info générales
echo "<br>";
$criteria = new Criteria(CahierTexteCompteRenduPeer::DATABASE_NAME);
$criteria->add(CahierTexteCompteRenduPeer::DATE_CT, '0', '=');
$ctCompteRenduInfoGenerales = $current_group->getCahierTexteCompteRendus($criteria);
if ($ctCompteRenduInfoGenerales->isEmpty()) {
	$ctCompteRenduInfoGenerales->append(new CahierTexteCompteRendu());
}
echo "<b>Informations Générales</b><br>\n";
$i = 1;
foreach ($ctCompteRenduInfoGenerales as $ctCompteRenduInfoGenerale) {
	if (count($ctCompteRenduInfoGenerales) != 1) {
		echo("Notice n° " . $i);
		$i = $i + 1;
	}
	echo "<table style=\"border-style:solid; border-width:0px; background-color: ".$color_fond_notices["i"] ."; padding: 2px; margin: 0px;\" width=\"100%\" cellpadding=\"2\" summary=\"Tableau d'information generale...\">";
	echo "<tr style=\"border-style:solid; border-width:1px; background-color: ".$couleur_cellule["i"]."; padding: 0px; margin: 0px;\">\n<td>\n";

	echo("<div style='display: none; color: red; margin: 0px; float: left;' id='compte_rendu_en_cours_info_".$ctCompteRenduInfoGenerale->getIdCt()."'></div>");
	echo("<div style=\"margin: 0px; float: right;\">");
	echo("<a href=\"#\" onclick=\"javascript:
						getWinEditionNotice().setAjaxContent('ajax_edition_compte_rendu.php?id_ct=".$ctCompteRenduInfoGenerale->getIdCt()."&today=0&id_groupe=".$id_groupe."',
							{ onComplete:
								function(transport) {
									initWysiwyg();
								}
							}
						);
						object_en_cours_edition = 'compte_rendu';
		  \">
				<img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" />
		  </a>");
	echo("<a href=\"#\" onclick=\"suppressionCompteRendu('Information générale',".$ctCompteRenduInfoGenerale->getIdCt().",'".add_token_in_js_func()."'); return false;\">
			<img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" />
		</a></div>\n");

	echo($ctCompteRenduInfoGenerale->getContenu());
	echo(afficheDocuments($ctCompteRenduInfoGenerale->getCahierTexteCompteRenduFichierJoints()));

	echo "</td>\n</tr>\n</table>\n";
}

//
// Export du cahier de texte au format csv ou ods
//
echo "<br />";
echo "<fieldset style=\"border: 1px solid grey; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">";
echo "<legend style=\"border: 1px solid grey; font-variant: small-caps;\">Export</legend>";
echo "<table border='0' width='100%' summary=\"Tableau de...\">\n";
echo "<tr><td>";
echo "<a href='./exportcsv.php?id_groupe=".$current_group->getId()."'>Export au format csv</a> Note : pour ouvrir ce fichier csv avec OpenOffice, garder les réglages par défaut lors de l'ouverture du fichier.";
echo "</td></tr></table></fieldset>";
// fin export
?>
