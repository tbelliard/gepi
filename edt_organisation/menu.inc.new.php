<?php
/**
 * EdT Gepi : le menu pour les includes require_once().
 *
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Pascal Fautrero
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

// Sécurité : éviter que quelqu'un appelle ce fichier seul
$serveur_script = $_SERVER["SCRIPT_NAME"];
$analyse = explode("/", $serveur_script);
	if ($analyse[3] == "menu.inc.new.php") {
		die();
	}

// ========================= Récupérer le bon fichier de langue

require_once('./choix_langue.php');

// ================= Désactivation de ce type de menu pour IE6 - pb de z-index insoluble !

$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
	echo '

<div class="menu_deroulant">
    <ul>
        <li><a href="#">Affichage<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            <li><a href="./index_edt.php?visioedt=prof1">Emplois du temps professeurs</a></li>
            <li><a href="./index_edt.php?visioedt=classe1">Emplois du temps classes</a></li>
            <li><a href="./index_edt.php?visioedt=salle1">Emplois du temps salles</a></li>
            <li><a href="./index_edt.php?visioedt=eleve1">Emplois du temps élèves</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>

        </li>
    </ul>
    
    <ul>
        <li><a href="#">Outils<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            ';
	        // =====================La fonction chercher_salle est paramétrable
            $aff_cherche_salle = GetSettingEdt("aff_cherche_salle");
	        if ($aff_cherche_salle == "tous") {
		        $aff_ok = "oui";
	        }
	        else if ($aff_cherche_salle == "admin") {
		        $aff_ok = "administrateur";
	        }
	        else {
	            $aff_ok = "non";
            }
	        if ($aff_ok == "oui" OR $_SESSION["statut"] == $aff_ok) {
		        echo '
            <li><a href="./index_edt.php?salleslibres=ok">Chercher des salles libres</a></li>
                ';
            }
            // ================================================================
            echo '
            <li><a href="javascript:window.print()">Imprimer la page</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>
    <ul>
        ';
        if ($_SESSION['statut'] == "administrateur") {
        echo '
        <li><a href="#">Maintenance<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            <li><a href="./verifier_edt.php">Vérifier/Corriger la base</a></li>
            <li><a href="./voir_base.php">Voir la base</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>

    <ul>
        <li><a href="#">Gestion<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            <li><a href="./edt.php" >Gestion des accès</a></li>
            <li><a href="./index.php?action=propagation" >Gestion des propagations</a></li>
            <li><a href="./transferer_edt.php" >Gestion des remplacements</a></li>
            <li><a href="./ajouter_salle.php">Gestion des salles</a></li>
            <li><a href="./edt_calendrier.php">Gestion du calendrier</a></li>';
                if(getSettingAOui('edt_calendrier_v2')) {
                    echo '
            <li><a href="./index.php?action=calendriermanager">Gestion du calendrier version 2</a></li>';
                }
        echo '
            <li><a href="./admin_config_semaines.php?action=visualiser">Définir les types de semaines</a></li>
            <li><a href="./admin_horaire_ouverture.php?action=visualiser">Définir les horaires d\'ouverture</a></li>
            <li><a href="./admin_periodes_absences.php?action=visualiser">Définir la journée type</a></li>
            <li><a href="./edt_initialiser.php">Initialisation automatique</a></li>
            <li><a href="./index_edt.php?visioedt=prof1">Initialisation manuelle</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>
    <ul>
        <li><a href="#">Options<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            <li><a href="./edt_parametrer.php">Personnaliser l\'affichage</a></li>
            <li><a href="./edt_param_couleurs.php">Définir les couleurs</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>
    <ul>
        <li><a href="#">?<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            <li><a href="./aide_initialisation.php">Aide à l\'initialisation</a></li>
            <li><a href="./aide_maintenance.php">Aide à la maintenance</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>
        ';
        } 
        echo '
</div>
<div style="clear:both;"></div>
	';
	}
?>
