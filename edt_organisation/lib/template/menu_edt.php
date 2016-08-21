 
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
            <li><a href="./index_edt.php?salleslibres=ok">Chercher des salles libres</a></li> 
            <li><a href="javascript:window.print()">Imprimer la page</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>
	<?php if ($_SESSION['statut'] == 'administrateur') { ?>
    <ul> 
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
            <li><a href="./edt_calendrier.php">Gestion du calendrier</a></li>
            <?php
                if(getSettingAOui('edt_calendrier_v2')) {
                    echo '
            <li><a href="./index.php?action=calendriermanager">Gestion du calendrier version 2</a></li>';
                }
            ?>
            <li><a href="./admin_config_semaines.php?action=visualiser">Définir les types de semaines</a></li>
            <li><a href="./admin_horaire_ouverture.php?action=visualiser">Définir les horaires d'ouverture</a></li>
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
            <li><a href="./edt_parametrer.php">Personnaliser l'affichage</a></li>
            <li><a href="./edt_param_couleurs.php">Définir les couleurs</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul>
    <ul>
        <li><a href="#">?<!--[if IE 7]><!--></a><!--<![endif]-->
            <ul>
		    <!--[if lte IE 6]><table><tr><td><![endif]-->
            <li><a href="./aide_initialisation.php">Aide à l'initialisation</a></li>
            <li><a href="./aide_maintenance.php">Aide à la maintenance</a></li>
		    <!--[if lte IE 6]></td></tr></table></a><![endif]-->
            </ul>
        </li>
    </ul> 
	<?php } ?>
 </div>
<div style="clear:both;"></div>
 
