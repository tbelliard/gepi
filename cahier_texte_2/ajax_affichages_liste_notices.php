<?php
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
};

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

//récupération du groupe courant
$utilisateur = $_SESSION['utilisateur'];
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
			selected_group = (\$A($('id_groupe_colonne_gauche').options).find(function(option) { return option.selected; }).value);
			new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=' + selected_group);
		\">");
echo "<option value='-1'>(choisissez un groupe pour changer la liste)</option>\n";
foreach ($utilisateur->getGroupes() as $group) {
	echo "<option id='colonne_gauche_select_group_option_".$group->getId()."' value='".$group->getId()."'";
	if ($current_group->getId() == $group->getId()) echo " SELECTED ";
	echo ">";
	echo $group->getDescription() . "&nbsp;-&nbsp;(";
	$str = null;
	foreach ($group->getClasses() as $classe) {
		$str .= $classe->getClasse() . ", ";
	}
	$str = substr($str, 0, -2);
	echo $str . ")&nbsp;\n";
	echo "</option>\n";
}
echo "</select><br><br>";
//fin affichage des groupes

if(getSettingValue('cahier_texte_acces_public')!='no'){
    echo "<a href='../public/index.php?id_groupe=" . $current_group->getId() ."' target='_blank'>Visualiser le cahier de textes en accès public</a>\n<br><br>";
} else {
	echo "<a href='./see_all.php'>Visualiser les cahiers de textes (accès restreint)</a>\n<br><br>";
}
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
		echo"<p>La classe " . $classe->getClasse() . " a  <a href=\"javascript:centrerpopup('liste_tous_devoirs.php?classe=". $classe->getId()."&amp;debut=$aujourdhui',260,320,'scrollbars=yes,statusbar=no,resizable=yes');\"><strong>" . $total[$classe->getId()] . "</strong> ";
		echo (($total[$classe->getId()] == 1) ? "travail personnel" : "travaux personnels");
		echo "</a> jusqu'au <strong>" . iconv('ISO-8859-1', 'UTF-8', strftime("%a %d %b %y", $date[$classe->getId()])) . "</strong>.</p>\n";
	}
}

$compteur_nb_total_notices = 0;

//récupération de $liste_comptes_rendus : comptes rendus pour la matière en cours
$criteria = new Criteria(CtCompteRenduPeer::DATABASE_NAME);
$criteria->add(CtCompteRenduPeer::DATE_CT, "0", "!=");
$criteria->add(CtCompteRenduPeer::DATE_CT, null, Criteria::ISNOTNULL);
$criteria->add(CtCompteRenduPeer::DATE_CT, $debutCdt, ">=");
$criteria->addDescendingOrderByColumn(CtCompteRenduPeer::DATE_CT);
$criteria->addAscendingOrderByColumn(CtCompteRenduPeer::HEURE_ENTRY);
$liste_comptes_rendus = $current_group->getCtCompteRendus($criteria);
$compteur_nb_total_notices = $compteur_nb_total_notices + count($liste_comptes_rendus);
if ($affiche_tout != "oui") {
	//limit à 7 devoirs
	$liste_comptes_rendus = array_slice($liste_comptes_rendus, 0 , 7);
}

//récupération de $liste_devoir : devoirs pour la matière en cours
$criteria = new Criteria(CtTravailAFairePeer::DATABASE_NAME);
$criteria->add(CtTravailAFairePeer::DATE_CT, $debutCdt, ">=");
$criteria->addDescendingOrderByColumn(CtTravailAFairePeer::DATE_CT);
$liste_devoir = $current_group->getCtTravailAFaires($criteria);
$compteur_nb_total_notices = $compteur_nb_total_notices + count($liste_devoir);
if ($affiche_tout != "oui") {
	//limit à 7 devoirs
	$liste_devoir = array_slice($liste_devoir, 0 , 7);
}

// Boucle d'affichage des notices dans la colonne de gauche
$compteur_notices_affiches = 0;
$date_ct_old = -1;
while (true) {
	$devoir = isset($liste_devoir[0]) ? $liste_devoir[0] : NULL;
	$compte_rendu = isset($liste_comptes_rendus[0]) ? $liste_comptes_rendus[0] : NULL;

	//si $devoir n'est pas nul et que la date du devoir est posterieure à celle du compte rendu
	if ($devoir != null && ($compte_rendu == null || $compte_rendu->getDateCt() < $devoir->getDateCt() )) {

		$liste_devoir = array_slice($liste_devoir, 1);
		$compteur_notices_affiches = $compteur_notices_affiches + 1;
		//on affiche le devoir car il y en a un
		echo("<table style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice.";\" width=\"100%\" cellpadding=\"1\" bgcolor=\"".$color_fond_notices["t"]."\" summary=\"Tableau de...\">\n<tr>\n<td>\n");

		echo("<strong>A faire pour le :</strong>\n");
		echo("<b>" . strftime("%a %d %b %y", $devoir->getDateCt()) . "</b>\n");
		echo("&nbsp;&nbsp;&nbsp;&nbsp;");

		//vise
		$html_balise =("<div style='display: none; color: red; margin: 0px; float: right;' id='compte_rendu_en_cours_devoir_".$devoir->getIdCt()."'></div>");
		$html_balise .= '<div style="margin: 0px; float: right;">';
		if (($devoir->getVise() != 'y') or (isset($visa_cdt_inter_modif_notices_visees) AND $visa_cdt_inter_modif_notices_visees == 'no')) {
			$html_balise .=("<a href=\"#\" onclick=\"javascript:
								getWinEditionNotice().setAjaxContent('ajax_edition_devoir.php?id_devoir=".$devoir->getIdCt()."',
		    						{ onComplete: function(transport) {
										new nicEditor({iconsPath : 'nicEdit/nicEditorIcons.gif'}).panelInstance('contenu');}
      								}
      							);
      							updateCalendarWithUnixDate(".$devoir->getDateCt().");
      							object_en_cours_edition = 'devoir';
								compte_rendu_en_cours_de_modification('devoir_".$devoir->getIdCt()."');
      							");
			$html_balise .=("\">");
			$html_balise .=("<img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" /></a>\n");
			$html_balise .=(" ");
			$html_balise .=("<a href=\"#\" onclick=\"javascript:
								suppressionDevoir('".strftime("%a %d %b %y", $devoir->getDateCt())."','".$devoir->getIdCt()."', '".$current_group->getId()."');
								return false;
							\"><img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" /></a>\n");
		} else {
			$html_balise .= "<i><span  class=\"red\">Notice signée</span></i>";
		}
		$html_balise .= '</div>';
		echo($html_balise);
		echo "<br/>";
		//affichage contenu
		echo ($devoir->getContenu());

		//Documents joints
		$ctDevoirDocuments = $devoir->getCtDevoirDocuments();
		echo(afficheDocuments($ctDevoirDocuments));

		echo("</td>\n</tr>\n</table>\n<br/>\n");

	} elseif ($compte_rendu != null && ($devoir == null || $compte_rendu->getDateCt() >= $devoir->getDateCt() )) {
		//si $compte_rendu n'est pas nul et que la date du $compte_rendu est posterieure à celle du devoir

		$liste_comptes_rendus = array_slice($liste_comptes_rendus, 1);
		$compteur_notices_affiches = $compteur_notices_affiches + 1;
		//on affiche le compte rendu car il y en a un
		echo("<table style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."\" width=\"100%\" cellpadding=\"1\" bgcolor=\"".$color_fond_notices["c"]."\" summary=\"Tableau de...\">\n<tr>\n<td>\n");
		echo("<b>" . strftime("%a %d %b %y", $compte_rendu->getDateCt()) . "</b>\n");

		// Numerotation des notices si plusieurs notice sur la mÃªme journée
		if ($date_ct_old == $compte_rendu->getDateCt()) {
			$num_notice++;
			echo " <b><i>(notice N° ".$num_notice.")</i></b>";
		} else {
			// on affiche "(notice N° 1)" uniquement s'il y a plusieurs notices dans la même journée
			if (!empty($liste_comptes_rendus) && $liste_comptes_rendus[0]->getDateCt() == $compte_rendu->getDateCt()) {
				echo " <b><i>(notice N° 1)</i></b>";
			}
			// On reinitialise le compteur
			$num_notice = 1;
		}
		$date_ct_old = $compte_rendu->getDateCt();

		$html_balise =("<div style='display: none; color: red; margin: 0px; float: right;' id='compte_rendu_en_cours_compte_rendu_".$compte_rendu->getIdCt()."'></div>");
		$html_balise .= '<div style="margin: 0px; float: right;">';
		if (($compte_rendu->getVise() != 'y') or (isset($visa_cdt_inter_modif_notices_visees) AND $visa_cdt_inter_modif_notices_visees == 'no')) {
			$html_balise .=("<a href=\"#\" onclick=\"javascript:
								getWinEditionNotice().setAjaxContent('ajax_edition_compte_rendu.php?id_ct=".$compte_rendu->getIdCt()."',
		    						{ onComplete: function(transport) {
											getWinEditionNotice().uptdateWidth();
										}
									});
								updateCalendarWithUnixDate(".$compte_rendu->getDateCt().");
								object_en_cours_edition = 'compte_rendu';
								compte_rendu_en_cours_de_modification('compte_rendu_".$compte_rendu->getIdCt()."');
							");
			$html_balise .=("\">");
			$html_balise .=("<img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" /></a>\n");

			$html_balise .=(" ");
			$html_balise .=("<a href=\"#\" onclick=\"javascript:
							suppressionCompteRendu('".strftime("%a %d %b %y", $compte_rendu->getDateCt())."',".$compte_rendu->getIdCt().");
							return false;
						\"><img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" /></a>\n");
		}
		// cas d'un visa, on n'affiche rien
		if ($compte_rendu->getVisa() == 'y') {
			$html_balise = " ";
		} else {
			if ($compte_rendu->getVise() == 'y') {
				$html_balise .= "<i><span  class=\"red\">Notice signée</span></i>";
			}
		}
		$html_balise .= '</div>';
		echo($html_balise);

		//affichage contenu
		echo "<br/>";
		echo ($compte_rendu->getContenu());

		// Documents joints
		$ctDocuments = $compte_rendu->getCtDocuments();
		echo(afficheDocuments($ctDocuments));

		echo("</td>\n</tr>\n</table>\n<br/>\n");
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
	echo "<legend style=\"font-variant: small-caps; border: 1px solid grey;\">".$legend."</legend>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#\" onclick=\"javascript:
			new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?affiche_tout=oui&id_groupe=".$current_group->getId()."');
			return false;\">";
	echo "Afficher&nbsp;toutes&nbsp;les&nbsp;notices</a>\n";
	echo "</fieldset>";
}

// Affichage des info générales
echo "<br>";
$criteria = new Criteria(CtCompteRenduPeer::DATABASE_NAME);
$criteria->add(CtCompteRenduPeer::DATE_CT, '0', '=');
$ctCompteRenduInfoGenerales = $current_group->getCtCompteRendus($criteria);
$ctCompteRenduInfoGenerale = isset($ctCompteRenduInfoGenerales[0]) ? $ctCompteRenduInfoGenerales[0] : '';
if (empty($ctCompteRenduInfoGenerales)) {
	$ctCompteRenduInfoGenerales[0] = new CtCompteRendu();
}
foreach ($ctCompteRenduInfoGenerales as $ctCompteRenduInfoGenerale) {
$html =$ctCompteRenduInfoGenerale->getContenu();
$html .=afficheDocuments($ctCompteRenduInfoGenerale->getCtDocuments());

echo "<b>Informations Générales</b>\n";
$html_balise =("<div style='display: none; color: red; margin: 0px; float: right;' id='compte_rendu_en_cours_info_".$ctCompteRenduInfoGenerale->getIdCt()."'></div>");
$html_balise .= "<div style=\"margin: 0px; float: right;\">";
$html_balise .=("<a href=\"#\" onclick=\"javascript:
					getWinEditionNotice().setAjaxContent('ajax_edition_compte_rendu.php?id_ct=".$ctCompteRenduInfoGenerale->getIdCt()."&today=0&id_groupe=".$id_groupe."',
						{ onComplete:
							function(transport) {
								getWinEditionNotice().uptdateWidth();
							}
      					}
      				);
					object_en_cours_edition = 'compte_rendu';
					compte_rendu_en_cours_de_modification('info_".$ctCompteRenduInfoGenerale->getIdCt()."');
      				");
$html_balise .=("\">");
$html_balise .= "<img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" /></a>";

$html_balise .= "<a href=\"#\" onclick=\"suppressionCompteRendu('Information générale',".$ctCompteRenduInfoGenerale->getIdCt()."); return false;\"><img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" /></a></div>\n";
echo "<table style=\"border-style:solid; border-width:0px; background-color: ".$color_fond_notices["i"] ."; padding: 2px; margin: 0px;\" width=\"100%\" cellpadding=\"2\" summary=\"Tableau de...\">\n<tr style=\"border-style:solid; border-width:1px; background-color: ".$couleur_cellule["i"]."; padding: 0px; margin: 0px;\">\n<td>\n".$html_balise.$html."</td>\n</tr>\n</table>\n";
}
function afficheDocuments ($documents) {
	$html = '';
	if (($documents) and (count($documents)!=0)) {
		$html = "<br><span class='petit'>Document(s) joint(s):</span>";
		//$html .= "<ul type=\"disc\" style=\"padding-left: 15px;\">";
		$html .= "<ul style=\"padding-left: 15px;\">";
		foreach ($documents as $document) {
			// Ouverture dans une autre fenêtre conservée parce que si le fichier est un PDF, un TXT, un HTML ou tout autre document susceptible de s'ouvrir dans le navigateur, on risque de refermer sa session en croyant juste refermer le document.
			// alternative, utiliser un javascript
			$html .= "<li style=\"padding: 0px; margin: 0px; font-family: arial, sans-serif; font-size: 80%;\"><a onclick=\"window.open(this.href, '_blank'); return false;\" href=\"".$document->getEmplacement()."\">".$document->getTitre()."</a></li>";

		}
		$html .= "</ul>";
	}
	return $html;
}

//
// Export du cahier de texte au format csv ou ods
//
echo "<br />";
echo "<fieldset style=\"border: 1px solid grey; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">";
echo "<legend style=\"border: 1px solid grey; font-variant: small-caps;\">Export</legend>";
echo "<table border='0' width='100%' summary=\"Tableau de...\">\n";
echo "<tr><td>";
echo "<a href='./exportcsv.php?id_groupe=".$current_group->getId()."'>Export au format csv</a> Note : pour ouvrir ce fichier csv avec oppenoffice, garder les réglages par défaut lors de l'ouverture du fichier.";
echo "</td></tr></table></fieldset>";
// fin export

?>
