<?php

/**
 * Fonctions pour l' affichage des EdT
 *
 * @package		GEPI
 * @subpackage	EmploisDuTemps
 * @copyright	Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Pascal Fautrero
 * @license		GNU/GPL, see COPYING.txt
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

// =============================================================================
//
//                                  PROTOS
//
// void     function AfficherEDT($tab_data, $type_edt, $login_edt, $period)
// void     function AfficheBarCommutateurSemaines($login_edt, $visioedt, $type_edt_2, $week_min, $week_selected)
// void     function AfficheBarCommutateurPeriodes($login_edt, $visioedt, $type_edt_2)
// void     function AfficheImprimante($display_print)
// void     function AfficheIconePlusNew($type_edt,$heuredeb_dec,$login_edt,$jour_sem,$id_creneaux, $period)
// void     function AfficheIconePlusAdd($type_edt,$heuredeb_dec,$login_edt,$jour_sem,$id_creneaux, $period)
// void     function AfficheModifierIcone($type_edt,$login_edt,$id_cours, $period)
// void     function AfficheEffacerIcone($type_edt,$login_edt,$id_cours, $period)

// =============================================================================
//
//                  Constantes utilisées par les edt
//
// =============================================================================
	// Affiche le contenu des créneaux directement
	define('NO_INFOBULLE', 0);
	// Affiche le contenu des créneaux dans l'infobulle d'une image
	define('INFOBULLE', 1);
	
	// Affiche l'edt verticalement
	define('VERTICAL', 0);	
	// Affiche l'edt horizontalement
	define('HORIZONTAL', 2);

	// Affiche les intitulés des créneaux (M1, M2...)
	define('CRENEAUX_VISIBLES', 0);
	// Masque les intitulés des créneaux
	define('CRENEAUX_INVISIBLES', 4);

// =============================================================================
//
//                  Permet d'afficher l'edt du jour
//
// =============================================================================
function EdtDuJour($tab_data, $jour, $flags) 
{
	$result = "";
	if ($flags & HORIZONTAL) {
		$result = EdtDuJourHorizontal($tab_data, $jour, $flags);
	}
	else {
		$result = EdtDuJourVertical($tab_data, $jour, $flags);
	}
	return $result;
}
// =============================================================================
//
//                  Permet d'afficher l'edt du jour verticalement
//
// =============================================================================
function EdtDuJourVertical($tab_data, $jour, $flags) 
{
    $result = "";
    $entetes = ConstruireEnteteEDT();
    $creneaux = ConstruireCreneauxEDT();
    $hauteur_demicreneaux = 20;
	$hauteur_creneaux = $hauteur_demicreneaux * 2;
	$nb_creneaux = $creneaux['nb_creneaux'];
    if (($nb_creneaux == 0) OR ($nb_creneaux == 1)) {
        $height = "94px";
    }
    else {
        $height = $nb_creneaux*2*$hauteur_demicreneaux + $hauteur_demicreneaux+1;
        $height = $height."px;";
    }   
    while (!isset($entetes['entete'][$jour])) {
        $jour--;
    }
    $jour_sem = $entetes['entete'][$jour];

    $result .= " <div class=\"fond\">\n";
    $result .= "<div class=\"colonne\" style=\"height : ".$height."\">\n";
    $jour_sem = $entetes['entete'][$jour];
    $result .= "<div class=\"entete\" style=\"height : ".$hauteur_demicreneaux."px;\"><div class=\"cadre\">".$jour_sem."</div></div>\n";
    $index_box = 0;
    while (isset($tab_data[$jour]['type'][$index_box]))
    {
        if ($tab_data[$jour]['type'][$index_box] == "vide") {
            $hauteur = $tab_data[$jour]['duree_valeur'][$index_box] * $hauteur_demicreneaux * 2;
            $hauteur = $hauteur."px;";
            if (strpos($tab_data[$jour]['duree'][$index_box], "demi") !== FALSE) {
                $result .= "<div class=\"demicellule\" style=\"height : ".$hauteur.";\">";
            }
            elseif (strpos($tab_data[$jour]['duree'][$index_box], "tiers") !== FALSE) {
                $result .= "<div class=\"tierscellule\" style=\"height : ".$hauteur.";\">";
            }
            else {
                $result .= "<div class=\"cellule\" style=\"height : ".$hauteur.";\">";
            }
            $result .= "<div style=\"display:none;\">".$tab_data[$jour]['affiche_creneau'][$index_box]." - durée = ".$tab_data[$jour]['duree_valeur'][$index_box]." heure(s)</div>\n";
            
            if (strpos($tab_data[$jour]['couleur'][$index_box], "Repas") !== FALSE) {
                $result .= "<div class=\"cadreRepas\">\n";
            }
            else {
                $result .= "<div class=\"cadre\">\n";
            }
            if (isset($tab_data[$jour]['extras'][$index_box])) {
                $result .= "Hello".$tab_data[$jour]['extras'][$index_box];
            }
            $result .= "</div></div>\n";  

        }
        else if ($tab_data[$jour]['type'][$index_box] == "erreur")
        {
            $hauteur = $tab_data[$jour]['duree_valeur'][$index_box] * $hauteur_demicreneaux * 2;
            $hauteur = $hauteur."px;";
            if (strpos($tab_data[$jour]['duree'][$index_box], "demi") !== FALSE) {
                $result .= "<div class=\"demicellule\" style=\"height : ".$hauteur.";\">";
            }
            elseif (strpos($tab_data[$jour]['duree'][$index_box], "tiers") !== FALSE) {
                $result .= "<div class=\"tierscellule\" style=\"height : ".$hauteur.";\">";
            }
            else {
                $result .= "<div class=\"cellule\" style = \"height : ".$hauteur.";\">";
            }
            $result .= "<div style=\"display:none;\">".$tab_data[$jour]['affiche_creneau'][$index_box]." - durée = ".$tab_data[$jour]['duree_valeur'][$index_box]." heure(s)</div>\n";
            $result .= "<div class=\"cadreRouge\">\n";
            $result .= $tab_data[$jour]['contenu'][$index_box];
            $result .= "</div></div>\n";  

        }
        else if ($tab_data[$jour]['type'][$index_box] == "conteneur")
        {
            $hauteur = $tab_data[$jour]['duree_valeur'][$index_box] * $hauteur_demicreneaux * 2;
            $hauteur = $hauteur."px;";
            if (strpos($tab_data[$jour]['duree'][$index_box], "demi") !== FALSE) {
                $result .= "<div class=\"demicellule\" style =\"height : ".$hauteur.";\">";
            }
            elseif (strpos($tab_data[$jour]['duree'][$index_box], "tiers") !== FALSE) {
                $result .= "<div class=\"tierscellule\" style = \"height : ".$hauteur.";\">";
            }
            else {
                $result .= "<div class=\"cellule\" style=\"height : ".$hauteur.";\">";
            }
    
        }
        else if ($tab_data[$jour]['type'][$index_box] == "cours")
        {
            $hauteur = $tab_data[$jour]['duree_valeur'][$index_box] * $hauteur_demicreneaux * 2;
            $hauteur = $hauteur."px;";
            if (strpos($tab_data[$jour]['duree'][$index_box], "demi") !== FALSE) {
                $result .= "<div class=\"demicellule\" style=\"height : ".$hauteur.";\">";
            }
            elseif (strpos($tab_data[$jour]['duree'][$index_box], "tiers") !== FALSE) {
                $result .= "<div class=\"tierscellule\" style=\"height : ".$hauteur.";\">";
            }
            else {
                $result .= "<div class=\"cellule\" style=\"height : ".$hauteur.";\">";
            }
            $result .= "<div style=\"display:none;\">".$tab_data[$jour]['affiche_creneau'][$index_box]." - durée = ".$tab_data[$jour]['duree_valeur'][$index_box]." heure(s)</div>\n";
            if (strpos($tab_data[$jour]['couleur'][$index_box], "Couleur") !== FALSE) {
                $result .= "<div class=\"cadreCouleur\">\n";
            }
            else {
                $result .= "<div class=\"cadre\">\n";
            }
            if (isset($tab_data[$jour]['extras'][$index_box])) {
                $result .= $tab_data[$jour]['extras'][$index_box];
            }
            if ($flags & INFOBULLE) {
                    $lesson_content_1 = str_replace("<br />", " - ", $tab_data[$jour]['contenu'][$index_box]);
                    $lesson_content_2 = str_replace("<i>", " ", $lesson_content_1);
                    $lesson_content = str_replace("</i>", " ", $lesson_content_2);
                    $result .="<div class=\"ButtonBar\"><div class=\"image\"><img src=\"../../templates/DefaultEDT/images/info.png\" title=\"".$lesson_content."\"  /></div></div>";
                    $result .= "</div></div>\n";

            }
            else {
                    $result .= $tab_data[$jour]['contenu'][$index_box];
                    $result .= "</div></div>\n";
            }

        }
        else if ($tab_data[$jour]['type'][$index_box] == "fin_conteneur")
        {
            $result .= "</div>\n";
        }
        else 
        {
            // ========= type de box non implémentée

        }


        $index_box++;
    }

    $result .= "</div>\n";

	if ($flags & CRENEAUX_INVISIBLES) {
		$result .= '</div>';
	}
	else {
		// ===== affichage de la colonne créneaux

		$result .= "<div class=\"colonne_creneaux\">\n";
		$result .= "<div class=\"entete_creneaux\" style=\"height : ".$hauteur_demicreneaux."px;\">";
		if (isset($tab_data['entete_creneaux'])) {
			$result .= $tab_data['entete_creneaux'];
		}
		$result .= "</div>\n";

		for ($i = 0; $i < $creneaux['nb_creneaux']; $i++)
		{
			$hauteur = 2 * $hauteur_demicreneaux;
			$hauteur = $hauteur."px;";
			$result .= "<div class=\"cellule\" style=\"height : ".$hauteur.";\">";
			$result .= "<div class=\"cellule_creneaux\"><div class=\"cadre\">".$creneaux['creneaux'][$i]."</div></div>\n";
			$result .= "</div>";
		}
	}
		$result .= "</div></div><div class=\"spacer\"></div>";
    return $result;
}

// =============================================================================
//
//                  Permet d'afficher l'edt du jour horizontalement
//
// =============================================================================
function EdtDuJourHorizontal($tab_data, $jour, $flags) 
{
    $result = "";
    $entetes = ConstruireEnteteEDT();
    $creneaux = ConstruireCreneauxEDT();
    $hauteur_demicreneaux = 30;
	$hauteur_creneaux = $hauteur_demicreneaux * 2;
	$nb_creneaux = $creneaux['nb_creneaux'];
    if (($nb_creneaux == 0) OR ($nb_creneaux == 1)) {
        $width = "94px";
    }
    else {
        $width = $nb_creneaux*2*$hauteur_demicreneaux + $hauteur_creneaux+4;
        $width = $width."px;";
    }   
    while (!isset($entetes['entete'][$jour])) {
        $jour--;
    }
    $jour_sem = $entetes['entete'][$jour];

    $result .= " <div class=\"fond_h\">\n";
    $result .= "<div class=\"ligne\" style=\"width : ".$width."\">\n";
    $jour_sem = $entetes['entete'][$jour];
    $result .= "<div class=\"entete_h\" style=\"width : ".$hauteur_creneaux."px;\"><div class=\"cadre\">".$jour_sem."</div></div>\n";
    $index_box = 0;
	$AlreadyInContainer = false;
    while (isset($tab_data[$jour]['type'][$index_box]))
    {
        if ($tab_data[$jour]['type'][$index_box] == "vide") {
            $hauteur = $tab_data[$jour]['duree_valeur'][$index_box] * $hauteur_demicreneaux * 2;
            $hauteur = $hauteur."px;";
            if (strpos($tab_data[$jour]['duree'][$index_box], "demi") !== FALSE) {
                $result .= "<div class=\"demicellule_h\" style=\"width : ".$hauteur.";\">";
            }
            elseif (strpos($tab_data[$jour]['duree'][$index_box], "tiers") !== FALSE) {
                $result .= "<div class=\"tierscellule_h\" style=\"width : ".$hauteur.";\">";
            }
            else {
                $result .= "<div class=\"cellule_h\" style=\"width : ".$hauteur.";\">";
            }
            $result .= "<div style=\"display:none;\">".$tab_data[$jour]['affiche_creneau'][$index_box]." - durée = ".$tab_data[$jour]['duree_valeur'][$index_box]." heure(s)</div>\n";
            
            if (strpos($tab_data[$jour]['couleur'][$index_box], "Repas") !== FALSE) {
                $result .= "<div class=\"cadreRepas\">\n";
            }
            else {
                $result .= "<div class=\"cadre\">\n";
            }
            if (isset($tab_data[$jour]['extras'][$index_box])) {
                $result .= "Hello".$tab_data[$jour]['extras'][$index_box];
            }
            $result .= "</div></div>\n";  

        }
        else if ($tab_data[$jour]['type'][$index_box] == "erreur")
        {
            $hauteur = $tab_data[$jour]['duree_valeur'][$index_box] * $hauteur_demicreneaux * 2;
            $hauteur = $hauteur."px;";
            if (strpos($tab_data[$jour]['duree'][$index_box], "demi") !== FALSE) {
                $result .= "<div class=\"demicellule_h\" style=\"width : ".$hauteur.";\">";
            }
            elseif (strpos($tab_data[$jour]['duree'][$index_box], "tiers") !== FALSE) {
                $result .= "<div class=\"tierscellule_h\" style=\"width : ".$hauteur.";\">";
            }
            else {
                $result .= "<div class=\"cellule_h\" style = \"width : ".$hauteur.";\">";
            }
            $result .= "<div style=\"display:none;\">".$tab_data[$jour]['affiche_creneau'][$index_box]." - durée = ".$tab_data[$jour]['duree_valeur'][$index_box]." heure(s)</div>\n";
            $result .= "<div class=\"cadreRouge\">\n";
            $result .= $tab_data[$jour]['contenu'][$index_box];
            $result .= "</div></div>\n";  

        }
        else if ($tab_data[$jour]['type'][$index_box] == "conteneur")
        {
            $hauteur = $tab_data[$jour]['duree_valeur'][$index_box] * $hauteur_demicreneaux * 2;
            $hauteur = $hauteur."px;";
			if (!$AlreadyInContainer) {
				$result .= "<div class=\"cellule_h\" style=\"width : ".$hauteur.";\">";
			}
            if (strpos($tab_data[$jour]['duree'][$index_box], "demi") !== FALSE) {
                $result .= "<div class=\"demicellule_h\" style =\"width : ".$hauteur.";display:block;\">";
				if (!$AlreadyInContainer) {
					$CountBeforeOutOfContainer = 2;
				}
				$AlreadyInContainer = true;
            }
            elseif (strpos($tab_data[$jour]['duree'][$index_box], "tiers") !== FALSE) {
                $result .= "<div class=\"tierscellule_h\" style = \"width : ".$hauteur.";display:block;\">";
				if (!$AlreadyInContainer) {
					$CountBeforeOutOfContainer = 3;
				}
				$AlreadyInContainer = true;				
            }
            else {
                $result .= "<div class=\"cellule_h\" style=\"width : ".$hauteur.";\">";
				if (!$AlreadyInContainer) {
					$CountBeforeOutOfContainer = 1;
				}
				$AlreadyInContainer = true;				
            }
    
        }
        else if ($tab_data[$jour]['type'][$index_box] == "cours")
        {
            $hauteur = $tab_data[$jour]['duree_valeur'][$index_box] * $hauteur_demicreneaux * 2;
            $hauteur = $hauteur."px;";
            if (strpos($tab_data[$jour]['duree'][$index_box], "demi") !== FALSE) {
                $result .= "<div class=\"demicellule_h\" style=\"width : ".$hauteur.";\">";
            }
            elseif (strpos($tab_data[$jour]['duree'][$index_box], "tiers") !== FALSE) {
                $result .= "<div class=\"tierscellule_h\" style=\"width : ".$hauteur.";\">";
            }
            else {
                $result .= "<div class=\"cellule_h\" style=\"width : ".$hauteur.";\">";
            }
            $result .= "<div style=\"display:none;\">".$tab_data[$jour]['affiche_creneau'][$index_box]." - durée = ".$tab_data[$jour]['duree_valeur'][$index_box]." heure(s)</div>\n";
            if (strpos($tab_data[$jour]['couleur'][$index_box], "Couleur") !== FALSE) {
                $result .= "<div class=\"cadreCouleur\">\n";
            }
            else {
                $result .= "<div class=\"cadre\">\n";
            }
            if (isset($tab_data[$jour]['extras'][$index_box])) {
                $result .= "Hello".$tab_data[$jour]['extras'][$index_box];
            }
            if ($flags & INFOBULLE) {
                    $lesson_content_1 = str_replace("<br />", " - ", $tab_data[$jour]['contenu'][$index_box]);
                    $lesson_content_2 = str_replace("<i>", " ", $lesson_content_1);
                    $lesson_content = str_replace("</i>", " ", $lesson_content_2);
                    $result .="<div class=\"ButtonBar\"><div class=\"image\"><img src=\"../../templates/DefaultEDT/images/info.png\" title=\"".$lesson_content."\"  /></div></div>";
                    $result .= "</div></div>\n";

            }
            else {
                    $result .= $tab_data[$jour]['contenu'][$index_box];
                    $result .= "</div></div>\n";
            }

        }
        else if ($tab_data[$jour]['type'][$index_box] == "fin_conteneur")
        {
            $result .= "</div>\n";
			$CountBeforeOutOfContainer--;
			if ($CountBeforeOutOfContainer == 0) {
				$result .= "</div>\n";
				$AlreadyInContainer = false;
			}
        }
        else 
        {
            // ========= type de box non implémentée

        }


        $index_box++;
    }

    $result .= "</div><div style=\"clear:both\"></div>\n";

	if ($flags & CRENEAUX_INVISIBLES) {
		$result .= '</div>';
	
	}
	else {
		// ===== affichage de la colonne créneaux

		$result .= "<div class=\"ligne_creneaux\" style=\"width : ".$width."\">\n";
		$result .= "<div class=\"entete_creneaux_h\" style=\"width : ".$hauteur_creneaux."px;\"><div class=\"cadre\" style=\"width : ".$hauteur_creneaux."px;\">";
		if (isset($tab_data['entete_creneaux'])) {
			$result .= $tab_data['entete_creneaux'];
		}
		$result .= "</div></div>\n";
		for ($i = 0; $i < $creneaux['nb_creneaux']; $i++)
		{
			$hauteur = 2 * $hauteur_demicreneaux;
			$hauteur = $hauteur."px;";
			$result .= "<div class=\"cellule_h\" style=\"width : ".$hauteur.";\">";
			$result .= "<div class=\"cellule_creneaux\"><div class=\"cadre\">".$creneaux['creneaux'][$i]."</div></div>\n";
			$result .= "</div>";
		}

		$result .= "</div></div><div style=\"clear:both\"></div>";
	}
    return $result;
}
// =============================================================================
//
//                  Permet d'afficher un emploi du temps 
//
// =============================================================================
function AfficherEDT($tab_data, $entetes, $creneaux, $type_edt, $login_edt, $period) 
{
	$peut_poster_message=peut_poster_message($_SESSION['statut']);
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
        echo("<h2 class=\"entete\"><div class=\"cadre\"><strong>".$jour_sem."</strong></div></h2>\n");
        $index_box = 0;
        while (isset($tab_data[$jour]['type'][$index_box]))
        {
            if ($tab_data[$jour]['type'][$index_box] == "vide") {
                
                echo("<div class=\"".$tab_data[$jour]['duree'][$index_box]."\">");
                echo("<div style=\"display:none;\">".$tab_data[$jour]['affiche_creneau'][$index_box]." - durée = ".$tab_data[$jour]['duree_valeur'][$index_box]." heure(s)</div>\n");
                echo ("<div class=\"".$tab_data[$jour]['couleur'][$index_box]."\">\n");
                echo ("<div class=\"ButtonBar\">");
                AfficheIconePlusNew($type_edt,$tab_data[$jour]['heuredeb_dec'][$index_box],$login_edt,$jour_sem,$tab_data[$jour]['id_creneau'][$index_box], $period);
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
                AfficheEffacerIcone($type_edt,$login_edt,$tab_data[$jour]['id_cours'][$index_box], $period);
                AfficheModifierIcone($type_edt,$login_edt,$tab_data[$jour]['id_cours'][$index_box], $period);
                if ($isIconeAddUsable)
                {
                    AfficheIconePlusAdd($type_edt,0,$login_edt,$jour_sem,$tab_data[$jour]['id_creneau'][$index_box], $period);
                }

				if($peut_poster_message) {
					if((isset($_GET['appel_depuis_form_message']))&&($_GET['appel_depuis_form_message']=="y")) {
						$tmp_jour_suivant=get_next_tel_jour($jour+1);
						if(($tmp_jour_suivant!="")&&(is_numeric($tmp_jour_suivant))) {
							$tmp_chaine_date=strftime("%d/%m/%Y", time()+24*3600*$tmp_jour_suivant);
						}
						else {
							$tmp_chaine_date=strftime("%d/%m/%Y");
						}

						$chaine_heure_visibilite="";
						if((isset($tab_data[$jour]['heuredebut'][$index_box]))&&($tab_data[$jour]['heuredebut'][$index_box]!='')) {
							$chaine_heure_visibilite="document.getElementById('heure_visibilite').value='".$tab_data[$jour]['heuredebut'][$index_box]."';";
						}

						echo "<div style='float:right;width:10px'><a href='#' onclick=\"document.getElementById('date_visibilite').value='$tmp_chaine_date';".$chaine_heure_visibilite."return false;\" target='_blank' title=\"Fixer la date et l'heure du message\ndans le module Alertes/Informations de Gepi\"><img src='../images/icons/sound.png' width='10' height='10' /></a></div>";
					}
					elseif(isset($tab_data[$jour]['login_prof'][$index_box])) {
						// Récupérer le jour suivant
						echo "<div style='float:right;width:10px'><a href='../mod_alerte/form_message.php?message_envoye=y&amp;login_dest=".$tab_data[$jour]['login_prof'][$index_box];
						$tmp_jour_suivant=get_next_tel_jour($jour+1);
						if(($tmp_jour_suivant!="")&&(is_numeric($tmp_jour_suivant))) {
							echo "&date_visibilite=".strftime("%d/%m/%Y", time()+24*3600*$tmp_jour_suivant);
						}
						else {
							echo "&date_visibilite=".strftime("%d/%m/%Y");
						}
						if((isset($tab_data[$jour]['heuredebut'][$index_box]))&&($tab_data[$jour]['heuredebut'][$index_box]!='')) {
							echo "&amp;heure_visibilite=".$tab_data[$jour]['heuredebut'][$index_box];
						}
						// Icone du module Alertes
						//$icone_deposer_alerte="no_mail.png";
						$icone_deposer_alerte="module_alerte32.png";
						echo add_token_in_url()."' target='_blank' title=\"Déposer pour ce professeur un message\ndans le module Alertes/Informations de Gepi\"><img src='../images/icons/$icone_deposer_alerte' width='10' height='10' /></a></div>";
					}
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
		/*
		echo "<hr /><pre>";
		print_r($tab_data[$jour]);
		echo "<pre>";
		*/
        $jour++;
    }

// ===== affichage de la colonne créneaux

    echo ("<div class=\"creneaux".$creneaux['nb_creneaux']."\">\n");
    echo ("<div class=\"entete_creneaux\"></div>\n");
    for ($i = 0; $i < $creneaux['nb_creneaux']; $i++)
    {
/*
        if(in_array($creneaux['creneaux'][$i], array('R0', 'R1', 'R2'))) {
            echo("<div class=\"demihoraires\"><div class=\"cadre\"><strong>".$creneaux['creneaux'][$i]."</strong></div></div>\n");
        }
        else {
*/
            echo("<div class=\"horaires\"><div class=\"cadre\"><strong>".$creneaux['creneaux'][$i]."</strong></div></div>\n");
//        }
    }

    echo("</div></div><div class=\"spacer\"></div></div></div>");

/*
//20141007
echo "Tableau des créneaux<pre>";
print_r($creneaux);
echo "</pre>";
*/
}

// ======================================================
//
//      Lorsqu'on est en mode "emplois du temps semaines"
//      permet de passer d'une semaine à l'autre
//
// ======================================================
function AfficheBarCommutateurSemaines($login_edt, $visioedt, $type_edt_2, $week_min, $week_selected)
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
            echo ("<li class=\"WeekCellYellow\"><a href=\"./index_edt.php?week_selected=".$j."&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\">".$j."</a></li>");        
        }
        else {
            echo ("<li class=\"WeekCellWhite\"><a href=\"./index_edt.php?week_selected=".$j."&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\" >".$j."</a></li>");        
        }
    }
    echo "</ul>";
    echo ("</div>");

    echo "<div class=\"spacer\"></div>";

    echo "<div style=\"float:left;width:100%;\";>";
    echo "<p>Semaine sélectionnée : ";
    echo $tab[$week_selected-1]["lundis"]." - ";      
    echo $tab[$week_selected-1]["vendredis"];
    echo "</p>";
    echo "</div>";
    echo "<div class=\"spacer\"></div>";
}



// ======================================================
//
//      Lorsqu'on est en mode "emplois du temps périodes"
//      permet de passer d'une période à l'autre
//
// ======================================================
function AfficheBarCommutateurPeriodes($login_edt, $visioedt, $type_edt_2)
{
    if (isset($_SESSION['period_id'])) {
        $period_next = ReturnNextIdPeriod($_SESSION['period_id']);
        $period_previous = ReturnPreviousIdPeriod($_SESSION['period_id']);
    }
    else {
        $period_next = ReturnNextIdPeriod(ReturnIdPeriod(date("U")));
        $period_previous = ReturnPreviousIdPeriod(ReturnIdPeriod(date("U")));

    }
    echo ("<div id=\"ButtonBarArrows\">");

    echo "<ul class=\"ButtonBarArrowLeft\">";
    echo "<li class=\"ButtonBarArrowLeft1\">";
    echo ("<a href=\"./index_edt.php?period_id=".$period_previous."&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\"></a>");
    echo "</li>";
    echo "</ul>";


    $req_periode = mysqli_query($GLOBALS["mysqli"], "SELECT nom_calendrier FROM edt_calendrier WHERE id_calendrier='".$_SESSION['period_id']."'");
    $rep_periode = mysqli_fetch_array($req_periode);

    echo "<ul class=\"Period\">";
    echo "<li>Période visualisée : ".$rep_periode['nom_calendrier']."</li>";
    echo "</ul>";


    echo "<ul class=\"ButtonBarArrowRight\">";
    echo "<li class=\"ButtonBarArrowRight1\">";
    echo ("<a href=\"./index_edt.php?period_id=".$period_next."&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\"></a>");
    echo "</li>";
    echo "</ul>";


    echo ("</div>");
    echo "<div class=\"spacer\"></div>";
}


// ======================================================
//
//      Lorsqu'on est en mode "emploi du temps"
//      permet de passer d'une période à l'autre
//      fonction associée à gepi/edt_organisation/edt_eleve.php
//
// ======================================================
function AfficheBarCommutateurPeriodesEleve()
{
    if (isset($_SESSION['period_id'])) {
        $period_next = ReturnNextIdPeriod($_SESSION['period_id']);
        $period_previous = ReturnPreviousIdPeriod($_SESSION['period_id']);
    }
    else {
        $period_next = ReturnNextIdPeriod(ReturnIdPeriod(date("U")));
        $period_previous = ReturnPreviousIdPeriod(ReturnIdPeriod(date("U")));

    }
    echo ("<div id=\"ButtonBarArrows\">");

    echo "<ul class=\"ButtonBarArrowLeft\">";
    echo "<li class=\"ButtonBarArrowLeft1\">";
    echo ("<a href=\"./edt_eleve.php?period_id=".$period_previous."\"></a>");
    echo "</li>";
    echo "</ul>";


    $req_periode = mysqli_query($GLOBALS["mysqli"], "SELECT nom_calendrier FROM edt_calendrier WHERE id_calendrier='".$_SESSION['period_id']."'");
    $rep_periode = mysqli_fetch_array($req_periode);

    echo "<ul class=\"Period\">";
    echo "Période visualisée : ".$rep_periode['nom_calendrier'];
    echo "</ul>";


    echo "<ul class=\"ButtonBarArrowRight\">";
    echo "<li class=\"ButtonBarArrowRight1\">";
    echo ("<a href=\"./edt_eleve.php?period_id=".$period_next."\"></a>");
    echo "</li>";
    echo "</ul>";


    echo ("</div>");
    echo "<div class=\"spacer\"></div>";
}
// =============================================================================
//
//          Affiche une petite imprimante
//
// =============================================================================
function AfficheImprimante($display_print)
{

    if ($display_print) {

        echo "<ul id=\"ButtonBarPrint\">";
        echo "<li id=\"ButtonBarPrint1\">";
	    echo "<a href='javascript:window.print()'></a>";
        echo "</li>";
        echo "</ul>";

    }
}

// =============================================================================
//
//          Affiche la bascule pour passer des emplois du temps périodes aux
//          emplois du temps semaines
//
// =============================================================================
function AfficheBascule($display_commutator, $login_edt, $visioedt, $type_edt_2)
{

    if ($display_commutator) {
        if (!isset($_SESSION['bascule_edt'])) {
            echo "<div class=\"ButtonBarCommutator\">";
            echo "<a href=\"./index_edt.php?bascule_edt=semaine&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\"><img src=\"../templates/".NameTemplateEDT()."/images/bascule_periode2.png\" title=\"Bascule vers emploi du temps semaine\" alt=\"Bascule vers emploi du temps semaine\" /></a>";
            echo "</div>";
        }
        else if ($_SESSION['bascule_edt'] == 'periode') {
            echo "<div class=\"ButtonBarCommutator\">";
            echo "<a href=\"./index_edt.php?bascule_edt=semaine&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\"><img src=\"../templates/".NameTemplateEDT()."/images/bascule_periode2.png\" title=\"Bascule vers emploi du temps semaine\" alt=\"Bascule vers emploi du temps semaine\" /></a>";
            echo "</div>";
        }
        else if ($_SESSION['bascule_edt'] == 'semaine') {
            echo "<div class=\"ButtonBarCommutator\">";
            echo "<a href=\"./index_edt.php?bascule_edt=periode&amp;visioedt=".$visioedt."&amp;login_edt=".$login_edt."&amp;type_edt_2=".$type_edt_2."\"><img src=\"../templates/".NameTemplateEDT()."/images/bascule_semaine2.png\" title=\"Bascule vers emploi du temps periode\" alt=\"Bascule vers emploi du temps periode\" /></a>";
            echo "</div>";
        }
    }
}

// =============================================================================
//
//          Affiche un "+" pour créer un nouveau cours sur un créneau vide
//
// =============================================================================
function AfficheIconePlusNew($type_edt,$heuredeb_dec,$login_edt,$jour_sem,$id_creneaux, $period)
{
    global $gepiPath;

    // On envoie le lien si et seulement si c'est un administrateur ou un scolarite ou si l'admin a donné le droit aux professeurs
    if (($_SESSION["statut"] == "administrateur" OR $_SESSION["statut"] == "scolarite" OR ($_SESSION["statut"] == "professeur" AND getSettingValue("edt_remplir_prof") == 'y' AND my_strtolower($login_edt) == my_strtolower($_SESSION["login"]))) AND $type_edt == "prof") {
        $deb = "milieu";
        if ($heuredeb_dec == 0) 
        {
            $deb = "debut";
        }
        echo ("<span class=\"image\">");
	    //echo "<a href='javascript:centrerpopup(\"modifier_cours_popup.php?period_id=".$period."&amp;cours=aucun&amp;identite=".$login_edt."&amp;horaire=".$jour_sem."|".$id_creneaux."|".$deb."\",700,205,\"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no\")'>
        //<img src=\"../templates/".NameTemplateEDT()."/images/ico_plus2.png\" title=\"Cr&eacute;er un cours\" alt=\"Cr&eacute;er un cours\" /></a>";
	    echo "<a href='$gepiPath/edt_organisation/modifier_cours.php?period_id=".$period."&amp;cours=aucun&amp;identite=".$login_edt."&amp;horaire=".$jour_sem."|".$id_creneaux."|".$deb."' onClick='centrerpopup(\"$gepiPath/edt_organisation/modifier_cours_popup.php?period_id=".$period."&amp;cours=aucun&amp;identite=".$login_edt."&amp;horaire=".$jour_sem."|".$id_creneaux."|".$deb."\",700,205,\"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no\");return false;'>
        <img src=\"../templates/".NameTemplateEDT()."/images/ico_plus2.png\" title=\"Cr&eacute;er un cours\" alt=\"Cr&eacute;er un cours\" /></a>";
        echo ("</span>\n");
    }
}

// =============================================================================
//
//              Affiche un "+" pour ajouter un cours sur un créneau contenant déjà quelque chose
//
// =============================================================================
function AfficheIconePlusAdd($type_edt,$heuredeb_dec,$login_edt,$jour_sem,$id_creneaux, $period)
{
    global $gepiPath;

    // On envoie le lien si et seulement si c'est un administrateur ou un scolarite ou si l'admin a donné le droit aux professeurs
    if (($_SESSION["statut"] == "administrateur" OR $_SESSION["statut"] == "scolarite" OR ($_SESSION["statut"] == "professeur" AND getSettingValue("edt_remplir_prof") == 'y' AND my_strtolower($login_edt) == my_strtolower($_SESSION["login"]))) AND $type_edt == "prof") {
        $deb = "milieu";
        if ($heuredeb_dec == 0) 
        {
            $deb = "debut";
        }
        echo ("<span class=\"image\">");
	    //echo "<a href='javascript:centrerpopup(\"modifier_cours_popup.php?period_id=".$period."&amp;cours=aucun&amp;identite=".$login_edt."&amp;horaire=".$jour_sem."|".$id_creneaux."|".$deb."\",700,205,\"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no\")'>
	    echo "<a href='$gepiPath/edt_organisation/modifier_cours.php?period_id=".$period."&amp;cours=aucun&amp;identite=".$login_edt."&amp;horaire=".$jour_sem."|".$id_creneaux."|".$deb."' onClick='centrerpopup(\"$gepiPath/edt_organisation/modifier_cours_popup.php?period_id=".$period."&amp;cours=aucun&amp;identite=".$login_edt."&amp;horaire=".$jour_sem."|".$id_creneaux."|".$deb."\",700,205,\"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no\");return false;'>
        <img src=\"../templates/".NameTemplateEDT()."/images/ico_plus2.png\" title=\"Ajouter un cours\" alt=\"Ajouter un cours\" /></a>";
        echo ("</span>\n");
    }
}
// =============================================================================
//
//          Affiche un petit crayon pour éditer le cours
//
// =============================================================================
function AfficheModifierIcone($type_edt,$login_edt,$id_cours, $period)
{
    global $gepiPath;

    // On envoie le lien si et seulement si c'est un administrateur ou un scolarite ou si l'admin a donné le droit aux professeurs
    if (($_SESSION["statut"] == "administrateur" OR $_SESSION["statut"] == "scolarite" OR ($_SESSION["statut"] == "professeur" AND getSettingValue("edt_remplir_prof") == 'y' AND my_strtolower($login_edt) == my_strtolower($_SESSION["login"]))) AND $type_edt == "prof") {
        echo ("<span class=\"image\">");
	    //echo "<a href='javascript:centrerpopup(\"modifier_cours_popup.php?period_id=".$period."&amp;id_cours=".$id_cours."&amp;type_edt=".$type_edt."&amp;identite=".$login_edt."\",700,205,\"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no\")'>
	    echo "<a href='$gepiPath/edt_organisation/modifier_cours.php?period_id=".$period."&amp;id_cours=".$id_cours."&amp;type_edt=".$type_edt."&amp;identite=".$login_edt."' onClick='centrerpopup(\"$gepiPath/edt_organisation/modifier_cours_popup.php?period_id=".$period."&amp;id_cours=".$id_cours."&amp;type_edt=".$type_edt."&amp;identite=".$login_edt."\",700,205,\"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no\");return false;'>
        <img src=\"../templates/".NameTemplateEDT()."/images/edit16.png\" title=\"Modifier\" alt=\"Modifier\" /></a>";
        echo ("</span>\n");
    }
}


// =============================================================================
//
//          Affiche un "X" pour supprimer le cours
//
// =============================================================================
function AfficheEffacerIcone($type_edt,$login_edt,$id_cours, $period)
{
    global $gepiPath;

    // On envoie le lien si et seulement si c'est un administrateur ou un scolarite ou si l'admin a donné le droit aux professeurs
    if (($_SESSION["statut"] == "administrateur" OR $_SESSION["statut"] == "scolarite" OR ($_SESSION["statut"] == "professeur" AND getSettingValue("edt_remplir_prof") == 'y' AND my_strtolower($login_edt) == my_strtolower($_SESSION["login"]))) AND $type_edt == "prof") {
        echo ("<span class=\"image\">");
	    //echo "<a href=\"./index_edt.php?visioedt=prof1&amp;login_edt=".$login_edt."&amp;type_edt_2=prof&amp;supprimer_cours=".$id_cours."&amp;type_edt=".$type_edt."&amp;identite=".$login_edt."\"onclick=\"return confirm('Confirmez-vous cette suppression ?')\")'>
        echo "<a href='javascript:centrerpopup(\"$gepiPath/edt_organisation/effacer_cours.php?period_id=".$period."&amp;supprimer_cours=".$id_cours."&amp;type_edt=".$type_edt."&amp;identite=".$login_edt."\",600,55,\"scrollbars=0,statusbar=0,resizable=0,menubar=no,toolbar=no,status=no\")'>        
        <img src=\"../templates/".NameTemplateEDT()."/images/delete2.png\" title=\"Supprimer\" alt=\"Supprimer\" /></a>";
        echo ("</span>\n");
    }
}








?>
