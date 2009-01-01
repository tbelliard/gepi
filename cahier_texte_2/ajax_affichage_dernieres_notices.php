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
	die("Le module n'est pas activ�.");
}

$utilisateur = $_SESSION['utilisateur'];
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

$groups = $utilisateur->getGroupes();

echo "<table width='100%' cellspacing='5px'>";
$i = 0;
//pour chaque groupe, on récupère un compte rendu et un travail à faire
foreach ($groups as $group) {
	$i = $i + 1;
	//on affiche sur deux colonne : % est l'operateur pour modulo
	if (($i % 2) == 1) echo "<tr>\n";
	echo "<td valign=\"top\" ><table width='100%' cellspacing='5px'>\n";

	//affichage du groupe
	echo "<tr><td colspan='2' width='50%'>\n";
	echo "<br/>";
	echo "<a href=\"#\" onclick=\"javascript:
			            id_groupe = '".$group->getId()."';
						getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?&id_groupe=".$group->getId()."&today='+getCalendarUnixDate(),
				            { onComplete :
			            		function() {
			            			getWinEditionNotice().updateWidth();
								}
							}
						);
						getWinListeNotices().setAjaxContent('./ajax_affichages_liste_notices.php?id_ct=&id_groupe=".$group->getId()."&today='+getCalendarUnixDate());
						$('groupe_boutons_index').show();
						$('calendar-container').show();
						return false;
			       \">";

	echo "<p style=\"background-color: silver; padding: 2px; border: 1px solid black; font-weight: bold;\">" . $group->getDescriptionAvecClasses() . "</a></p>\n";
	echo "</td></tr>";

	echo "<tr>";
	//récupération et affichage du dernier compte rendu
	$criteria = new Criteria(CtCompteRenduPeer::DATABASE_NAME);
	$criteria->add(CtCompteRenduPeer::DATE_CT, "0", "!=");
	$criteria->add(CtCompteRenduPeer::DATE_CT, null, Criteria::ISNOTNULL);
	$debutCdt = getSettingValue("begin_bookings");
	$criteria->add(CtCompteRenduPeer::DATE_CT, $debutCdt, ">=");
	$criteria->addDescendingOrderByColumn(CtCompteRenduPeer::DATE_CT);
	$criteria->addAscendingOrderByColumn(CtCompteRenduPeer::HEURE_ENTRY);
	$ctCompteRendus = $group->getCtCompteRendus($criteria);
	echo "<td style=\"border-style:solid; border-width:1px;\" valign=\"top\" width='50%' bgcolor=\"".$color_fond_notices["c"]."\">";
	if (!empty($ctCompteRendus)) {
		$compte_rendu = $ctCompteRendus[0];
		//on affiche le compte rendu car il y en a un
		echo("<b>" . strftime("%a %d %b %y", $compte_rendu->getDateCt()) . "</b><br /><br />\n");

		$html_balise = '<div style="margin: 0px; float: right;">';
		if (($compte_rendu->getVise() != 'y') or ($visa_cdt_inter_modif_notices_visees == 'no')) {
			$html_balise .=("<a href=\"#\" onclick=\"javascript:
      							updateCalendarWithUnixDate(".$compte_rendu->getDateCt().");
								id_groupe = '".$group->getId()."';
								object_en_cours_edition = 'compte_rendu';\n
								getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_ct=".$compte_rendu->getIdCt()."',
					            	{ onComplete :
					            		function() {
					            			getWinEditionNotice().updateWidth();
											getWinListeNotices().setAjaxContent('./ajax_affichages_liste_notices.php?id_groupe=".$group->getId()."&today='+getCalendarUnixDate(),
												{ onComplete :
													function() {
														compte_rendu_en_cours_de_modification('compte_rendu_".$compte_rendu->getIdCt()."');
													}
												}
											);
										}
									}
								);
								$('groupe_boutons_index').show();
								$('calendar-container').show();
								return false;
								");
			$html_balise .=("\">");
			$html_balise .=("<img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" /></a>\n");

			$html_balise .=(" ");
			$html_balise .=("<a href=\"#\" onclick=\"javascript:
														if (confirmlink(this,'suppression de la notice du ".strftime("%a %d %b %y", $compte_rendu->getDateCt())." ?','Confirmez vous ')) {
													    	new Ajax.Request('./ajax_suppression_compte_rendu.php?id_ct=".$compte_rendu->getIdCt()."',
													    		{ onComplete:
													    			function(transport) {
							  										 	if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
							      											alert(transport.responseText);
							      										} else {
							      											location.reload();
																		}
																	}
																}
															);
														}
														return false;
								\">
			<img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" /></a>\n");
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
		echo ($compte_rendu->getContenu());

		// Documents joints
		$ctDocuments = $compte_rendu->getCtDocuments();
		echo(afficheDocuments($ctDocuments));
	}
	echo "</td>";

	//récupération et affichage du dernier travail à faire
	$criteria = new Criteria(CtTravailAFairePeer::DATABASE_NAME);
	$criteria->add(CtTravailAFairePeer::DATE_CT, $debutCdt, ">=");
	$criteria->addDescendingOrderByColumn(CtTravailAFairePeer::DATE_CT);
	$ctTravailAFaires = $group->getCtTravailAFaires($criteria);
	echo "<td style=\"border-style:solid; border-width:1px;\" valign=\"top\" width='50%' bgcolor=\"".$color_fond_notices["t"]."\">";
		if (!empty($ctTravailAFaires)) {
		$devoir = $ctTravailAFaires[0];
		//on affiche le devoir car il y en a un
		echo("<strong>A faire pour le :</strong>\n");
		echo("<b>" . strftime("%a %d %b %y", $devoir->getDateCt()) . "</b><br /><br />\n");

		//vise
		$html_balise = '<div style="margin: 0px; float: right;">';
		if (($devoir->getVise() != 'y') or ($visa_cdt_inter_modif_notices_visees == 'no')) {
			$html_balise .=("<a href=\"#\" onclick=\"javascript:\n
      							updateCalendarWithUnixDate(".$devoir->getDateCt().");
								id_groupe = '".$group->getId()."';
								object_en_cours_edition = 'devoir';
								getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_devoir=".$devoir->getIdCt()."',
						            { onComplete :
					            		function() {
					            			new nicEditor({iconsPath : 'nicEdit/nicEditorIcons.gif'}).panelInstance('contenu');
											getWinListeNotices().setAjaxContent('./ajax_affichages_liste_notices.php?id_groupe=".$group->getId()."&today='+getCalendarUnixDate(),
												{ onComplete :
													function() {
														compte_rendu_en_cours_de_modification('devoir_".$devoir->getIdCt()."');
													}
												}
											);
										}
									}
								);
								$('groupe_boutons_index').show();
								$('calendar-container').show();
								return false;
      						");
			$html_balise .=("\">");
			$html_balise .=("<img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" /></a>\n");
			$html_balise .=(" ");
			$html_balise .=("<a href=\"#\" onclick=\"javascript:
								if (confirmlink(this,'suppression de la notice du ".strftime("%a %d %b %y", $devoir->getDateCt())." ?','Confirmez vous ')) {
									new Ajax.Request('./ajax_suppression_devoir.php?id_devoir=".$devoir->getIdCt()."', {
										onComplete: function(transport) {
											if (transport.responseText.match('Erreur') || transport.responseText.match('error')) {
												alert(transport.responseText);
											} else {
												location.reload();
											}
										}
									});
								}
								return false;
								\">
			<img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" /></a>\n");
		} else {
			$html_balise .= "<i><span  class=\"red\">Notice signée</span></i>";
		}
		$html_balise .= '</div>';
		echo($html_balise);

		//affichage contenu
		echo ($devoir->getContenu());

		//Documents joints
		$ctDevoirDocuments = $devoir->getCtDevoirDocuments();
		echo(afficheDocuments($ctDevoirDocuments));
	}
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table></td>\n";

	//on affiche sur deux colonne : % est l'operateur pour modulo
	if (($i % 2) == 0) echo "</tr>\n";
}
echo "</table>";

function afficheDocuments ($documents) {
	$html = '';
	if (($documents) and (count($documents)!=0)) {
		$html = "<span class='petit'>Document(s) joint(s):</span>";
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
?>