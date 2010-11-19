<?php

// =============================================================================
//
//          
//
// =============================================================================
function RecupereNotices(&$tab_data, $entetes) {

	$jour = 0;
	while (isset($entetes['entete'][$jour])) {
		$timestamp = RecupereTimestampJour($jour);
		//$timestamp-=3600;	
		//echo "jour = ".$jour."<br/>";
		//echo strftime("%S %M %H %d %b %Y", 1288825200 	)."<br/>";		
		//echo strftime("%S %M %H %d %b %Y", $timestamp)."<br/>";
		//echo $timestamp."<br/>";
		$index_box = 0;
		while (isset($tab_data[$jour]['type'][$index_box]))
		{
		$tab_data[$jour]['id_ct'][$index_box] = 0;
		if ($tab_data[$jour]['type'][$index_box] == "cours") {
			$id_groupe = $tab_data[$jour]['id_groupe'][$index_box];
			$sql_request = "SELECT id_ct , date_ct FROM ct_entry WHERE id_groupe = '".$id_groupe."' AND 
																date_ct = '".$timestamp."'";
			//echo $sql_request."<br/>";
			$req = mysql_query($sql_request);
			if ($rep = mysql_fetch_array($req)) {
			//echo $rep['id_ct']."  ".$rep['date_ct'];
				$tab_data[$jour]['id_ct'][$index_box] = $rep['id_ct'];
			}
		}
		$index_box++;
		}
		$jour++;
	}
}
// =============================================================================
//
//          Affiche un "+" pour créer un nouveau cours sur un créneau vide
//
// =============================================================================
function AfficheIconePlusNew_CDT($id_groupe, $login_edt, $type_edt, $heuredeb_dec, $jour, $id_ct)
{

    // On envoie le lien si et seulement si c'est un administrateur ou un scolarite ou si l'admin a donné le droit aux professeurs
    if (($_SESSION["statut"] == "administrateur" OR $_SESSION["statut"] == "scolarite" OR ($_SESSION["statut"] == "professeur" AND strtolower($login_edt) == strtolower($_SESSION["login"]))) AND $type_edt == "prof") {
        $deb = "milieu";
        if ($heuredeb_dec == 0) 
        {
            $deb = "debut";
        }
		if ($id_ct == 0) {
			echo ("<span class=\"image\">");
			$MaDate = RecupereTimestampJour($jour);
			echo "<a href=\"#\" style=\"font-size: 11pt;\"  onclick=\"javascript:
					id_groupe = '".$id_groupe."';
					getWinDernieresNotices().hide();
					getWinListeNotices();
					new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$id_groupe."', {encoding: 'ISO-8859-1'});
					getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=".$id_groupe."&today=".$MaDate."', { 
								encoding: 'ISO-8859-1',
								onComplete : 
								function() {
									initWysiwyg();
								}
							}
					);
					return false;
				\">
				<img src=\"../templates/".NameTemplateEDT()."/images/cdt_vide.png\" title=\"Ajouter Compte-rendu\" alt=\"Ajouter Compte-rendu\" /></a>";
			echo ("</span>\n");
		}
		else {
			echo ("<span class=\"image\">");
			$MaDate = RecupereTimestampJour($jour);
			echo "<a href=\"#\" style=\"font-size: 11pt;\"  onclick=\"javascript:
					id_groupe = '".$id_groupe."';
					getWinDernieresNotices().hide();
					getWinListeNotices();
					new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$id_groupe."', {encoding: 'ISO-8859-1'});
					getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_ct=".$id_ct."&id_groupe=".$id_groupe."&today=".$MaDate."', { 
								encoding: 'ISO-8859-1',
								onComplete : 
								function() {
									initWysiwyg();
								}
							}
					);
					return false;
				\">
				<img src=\"../templates/".NameTemplateEDT()."/images/cdt_rempli.png\" title=\"Editer Compte-rendu\" alt=\"Ajouter Compte-rendu\" /></a>";
			echo ("</span>\n");
		}
    }
}
// ======================================================
//
//      Lorsqu'on est en mode "emplois du temps semaines"
//      permet de passer d'une semaine à l'autre
//
// ======================================================
function AfficheBarCommutateurSemaines_CDT($login_edt, $visioedt, $type_edt_2, $week_min, $week_selected)
{
    $range = 8;

    if ($week_min == NULL) {
        if (($week_selected < 33 + $range) AND ($week_selected >= 33)) {
            $week_min = 33;
        }
        else {
            $week_min = $week_selected - $range;
        }
    }
    if ($week_min < 1) {
        $week_min = $week_min + 1 + NumLastWeek();
    }
    if (($week_selected < 28) AND ($week_selected >= 28 - $range)) {
        $week_max = 28;
    }
    else {
        $week_max = $week_min + $range*2;
    }


    echo ("<div id=\"ButtonBarArrows\">");
    echo "<ul style=\"float:left;margin:5px;list-style-type:none;border:0px solid black;\">";
    for ($i = $week_min; $i < $week_max ; $i++) {
        if ($i > NumLastWeek()) {
            $j = $i - NumLastWeek();
        }
        else {
            $j = $i;
        }
        if ($j == $week_selected) {
            echo ("<li class=\"WeekCellYellow\"><a href=\"./index.php?week_selected=".$j."&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\">".$j."</a></li>");        
        }
        else {
            echo ("<li class=\"WeekCellWhite\"><a href=\"./index.php?week_selected=".$j."&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\">".$j."</a></li>");        
        }
    }
    echo "</ul>";
    echo ("</div>");

    echo "<div class=\"spacer\"></div>";

    echo "<div style=\"float:left;width:100%;\";>";
    echo "<p>Semaine sélectionnée : ";
    $tab = RecupereLundisVendredis();
    echo $tab[$week_selected-1]["lundis"]." - ";      
    echo $tab[$week_selected-1]["vendredis"];
    echo "</p>";
    echo "</div>";
    echo "<div class=\"spacer\"></div>";
}
// =============================================================================
//
//                  Permet d'afficher un emploi du temps 
//
// =============================================================================
function AfficherEDT_CDT($tab_data, $entetes, $creneaux, $type_edt, $login_edt, $period) 
{
	$tab_dates = RecupereJoursSemaine();
    echo ("<div class=\"fenetre\">\n");

    echo("<div class=\"contenu\">

		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
		<div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>

        <div class=\"tableau\">\n");


// ===== affichage des colonnes
// ===== Les "display:none" sont utilisés pour l'accessibilité
    $jour = 0;
    $isIconeAddUsable = true;
    while (isset($entetes['entete'][$jour])) {

        echo("<div class=\"colonne".$creneaux['nb_creneaux']."\">\n");
        $jour_sem = $entetes['entete'][$jour];
        echo("<h2 class=\"entete\"><div class=\"cadre\"><strong>".$jour_sem."</strong> ".$tab_dates[$_SESSION['week_selected']-1][$jour_sem]."</div></h2>\n");
        $index_box = 0;
        while (isset($tab_data[$jour]['type'][$index_box]))
        {
            if ($tab_data[$jour]['type'][$index_box] == "vide") {
                
                echo("<div class=\"".$tab_data[$jour]['duree'][$index_box]."\">");
                echo("<div style=\"display:none;\">".$tab_data[$jour]['affiche_creneau'][$index_box]." - durée = ".$tab_data[$jour]['duree_valeur'][$index_box]." heure(s)</div>\n");
                echo ("<div class=\"".$tab_data[$jour]['couleur'][$index_box]."\">\n");
                echo ("<div class=\"ButtonBar\">");
                //AfficheIconePlusNew_CDT($tab_data[$jour]['id_cours'][$index_box], $login_edt, $type_edt, $tab_data[$jour]['heuredeb_dec'][$index_box]);
                echo ("</div>\n");
                echo ("</div></div>\n");  
 
            }
            else if ($tab_data[$jour]['type'][$index_box] == "erreur")
            {
    
                echo("<div class=\"".$tab_data[$jour]['duree'][$index_box]."\">");
                echo("<div style=\"display:none;\">".$tab_data[$jour]['affiche_creneau'][$index_box]." - durée = ".$tab_data[$jour]['duree_valeur'][$index_box]." heure(s)</div>\n");
                echo("<div class=\"cadreRouge\">\n");
                echo $tab_data[$jour]['contenu'][$index_box];
                echo ("<div class=\"ButtonBar\">");
                echo ("</div>\n");
                echo ("</div></div>\n");  
    
            }
            else if ($tab_data[$jour]['type'][$index_box] == "conteneur")
            {
                echo("<div class=\"".$tab_data[$jour]['duree'][$index_box]."\">\n");
                $isIconeAddUsable = false;
        
            }
            else if ($tab_data[$jour]['type'][$index_box] == "cours")
            {
                echo("<div class=\"".$tab_data[$jour]['duree'][$index_box]."\">");
                echo("<div style=\"display:none;\">".$tab_data[$jour]['affiche_creneau'][$index_box]." - durée = ".$tab_data[$jour]['duree_valeur'][$index_box]." heure(s)</div>\n");
                echo ("<div class=\"".$tab_data[$jour]['couleur'][$index_box]."\">");
                echo $tab_data[$jour]['contenu'][$index_box];
                echo ("<div class=\"ButtonBar\">");
				if ($tab_data[$jour]['id_groupe'][$index_box] != 0) {
					AfficheIconePlusNew_CDT($tab_data[$jour]['id_groupe'][$index_box], $login_edt, $type_edt, $tab_data[$jour]['heuredeb_dec'][$index_box], $jour, $tab_data[$jour]['id_ct'][$index_box]);
				}
				else {
					// -------- C'est un AID, non géré par CDT2
				}
                echo ("</div>\n");
                echo ("</div></div>\n");   
   
            }
            else if ($tab_data[$jour]['type'][$index_box] == "fin_conteneur")
            {
                echo("</div>\n");
                $isIconeAddUsable = true;
            }
            else 
            {
                // ========= type de box non implémentée
    
            }


            $index_box++;
        }

        echo("</div>\n");
        $jour++;
    }

// ===== affichage de la colonne créneaux

    echo ("<div class=\"creneaux".$creneaux['nb_creneaux']."\">\n");
    echo ("<div class=\"entete_creneaux\"></div>\n");
    for ($i = 0; $i < $creneaux['nb_creneaux']; $i++)
    {
        echo("<div class=\"horaires\"><div class=\"cadre\"><strong>".$creneaux['creneaux'][$i]."</strong></div></div>\n");
    }

    echo("</div></div><div class=\"spacer\"></div></div></div>");

}

?>