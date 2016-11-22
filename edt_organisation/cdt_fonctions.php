<?php

// =============================================================================
//
//          
//
// =============================================================================
function RecupereNotices(&$tab_data, $entetes) {

	$jour = 0;
	while (isset($entetes['entete'][$jour])) {
		//$timestamp = RecupereTimestampJour($jour);
		$timestamp = RecupereTimestampJour_CDT2($jour);
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
				/*
				$sql_request = "SELECT id_ct , date_ct FROM ct_entry WHERE id_groupe = '".$id_groupe."' AND 
																	date_ct = '".$timestamp."'";
				*/
				// Il faut impérativement que le $timestamp corresponde au début de la journée à 0h00min
				$sql_request = "SELECT id_ct , date_ct FROM ct_entry WHERE id_groupe = '".$id_groupe."' AND 
																	date_ct >= '".$timestamp."' AND 
																	date_ct < '".($timestamp+24*3600)."' ORDER BY date_ct;";
				/*
				if($id_groupe==3203) {
					echo "\$jour=$jour<br />";
					echo "\$timestamp=$timestamp (".strftime("%a %d/%m/%Y à %H:%M:%S", $timestamp).")<br />";
					echo $sql_request."<br/>";
				}
				*/
				$req = mysqli_query($GLOBALS["mysqli"], $sql_request);
				if ($rep = mysqli_fetch_array($req)) {
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
    if (($_SESSION["statut"] == "administrateur" OR $_SESSION["statut"] == "scolarite" OR ($_SESSION["statut"] == "professeur" AND my_strtolower($login_edt) == my_strtolower($_SESSION["login"]))) AND $type_edt == "prof") {
        $deb = "milieu";
        if ($heuredeb_dec == 0) 
        {
            $deb = "debut";
        }

		/*
		//=========================
		// DEBUG
		echo "<span style='color:green'>$jour</span> ";
		echo "<span style='color:plum'>$id_ct</span>";
		//=========================
		*/

		$afficher_icone_et_lien_cdt2="y";
		$sql="SELECT 1=1 FROM j_groupes_visibilite WHERE id_groupe='".$id_groupe."' AND domaine='cahier_texte' AND visible='n';";
		//echo "$sql<br />\n";
		$test_grp_visib=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test_grp_visib)!=0) {
			// C'est un groupe non affiché sur CDT.
			$afficher_icone_et_lien_cdt2="n";

			if($_SESSION['statut']=="professeur") {
				// Trouver si le groupe est un sous-groupe d'un groupe-classe.
				$sql="SELECT DISTINCT id_classe FROM j_groupes_classes jgc WHERE id_groupe='".$id_groupe."';";
				//echo "$sql<br />\n";
				$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_clas)==1) {
					// Il faudrait gérer les regroupements aussi, mais on verra plus tard.
					$lig_clas=mysqli_fetch_object($res_clas);
					$sql="SELECT * FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' AND jgc.id_classe='".$lig_clas->id_classe."' AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='cahier_texte' and visible='n');";
					//echo "$sql<br />\n";
					$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_clas)==1) {
						$afficher_icone_et_lien_cdt2="y";
						$lig_clas=mysqli_fetch_object($res_clas);
						$id_groupe=$lig_clas->id_groupe;
					}
				}
			}
		}

		if($afficher_icone_et_lien_cdt2=="y") {
			if ($id_ct == 0) {
				echo ("<span class=\"image\">");
				$MaDate = RecupereTimestampJour_CDT2($jour);
				/*
				// 20141007
				echo "MaDate=$MaDate<br />";
				echo strftime("%d/%m/%Y %H:%M:%S", $MaDate)."<br />";
				*/
				echo "<a href=\"#\" style=\"font-size: 11pt;\"  onclick=\"javascript:
						id_groupe = '".$id_groupe."';
						getWinDernieresNotices().hide();
						getWinListeNotices();
						new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$id_groupe."', {encoding: 'utf-8'});
						getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=".$id_groupe."&today=".$MaDate."', { 
									encoding: 'utf-8',
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
				$MaDate = RecupereTimestampJour_CDT2($jour);
				/*
				// 20141007
				echo "MaDate=$MaDate<br />";
				echo strftime("%d/%m/%Y %H:%M:%S", $MaDate)."<br />";
				*/
				echo "<a href=\"#\" style=\"font-size: 11pt;\"  onclick=\"javascript:
						id_groupe = '".$id_groupe."';
						getWinDernieresNotices().hide();
						getWinListeNotices();
						new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$id_groupe."', {encoding: 'utf-8'});
						getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_ct=".$id_ct."&id_groupe=".$id_groupe."&today=".$MaDate."', { 
									encoding: 'utf-8',
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
}
// ======================================================
//
//      Lorsqu'on est en mode "emplois du temps semaines"
//      permet de passer d'une semaine à l'autre
//
// ======================================================
function AfficheBarCommutateurSemaines_CDT($login_edt, $visioedt, $type_edt_2, $week_min, $week_selected, $avec_semAB='n')
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
    $tab = RecupereLundisVendredis();	
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
            echo ("<li class=\"WeekCellWhite\"><a title=\"Semaine du ".$tab[$j-1]["lundis"]." au ".$tab[$j-1]["vendredis"]."\" href=\"./index.php?week_selected=".$j."&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\">".$j."</a></li>");        
        }
    }
    echo "</ul>";
    echo ("</div>");

    echo "<div class=\"spacer\"></div>";

    echo "<div style=\"float:left;width:100%;\";>";
    echo "<p class='bold'>Semaine sélectionnée : ";

    echo $tab[$week_selected-1]["lundis"]." - ";      
    echo $tab[$week_selected-1]["vendredis"];
    $avec_semAB="y";
    if($avec_semAB=='y') {
    	$sql="SELECT type_edt_semaine FROM edt_semaines WHERE id_edt_semaine='$week_selected' AND type_edt_semaine!='';";
    	$res=mysqli_query($GLOBALS["mysqli"], $sql);
    	if(mysqli_num_rows($res)>0) {
    		$lig=mysqli_fetch_object($res);
    		echo " ($lig->type_edt_semaine)";
    	}
    }
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

/*
// 20141007
echo "Tableau des créneaux<pre>";
print_r($creneaux);
echo "</pre>";
*/
}

?>
