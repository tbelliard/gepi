<?php

/**
 * Fichiers qui permet de paramétrer les couleurs de chaque matière des emplois du temps
 *
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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
require_once("./choix_langue.php");

$titre_page = TITLE_EDT_PARAM_COLORS;
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");
//require_once("./fonctions_edt_2.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

/*/ Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}*/
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_edt";
$utilisation_jsdivdrag = "";
$ua = getenv("HTTP_USER_AGENT");
if (strstr($ua, "MSIE 6.0")) {
	$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_ie6_param";
}
else if (strstr($ua, "MSIE 7")) {
	$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_ie7_param";
}
$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_param";
//==============PROTOTYPE===============
$utilisation_prototype = "ok";
//============fin PROTOTYPE=============
// On insère l'entête de Gepi
require_once("../lib/header.inc.php");

// On ajoute le menu EdT
require_once("./menu.inc.php");
?>


<br />
<!-- la page du corps de l'EdT -->

<div id="lecorps">
    <?php require_once("./menu.inc.new.php"); ?>
	
	
	<div id="art-main">
        <div class="art-sheet">
            <div class="art-sheet-tl"></div>
            <div class="art-sheet-tr"></div>
            <div class="art-sheet-bl"></div>
            <div class="art-sheet-br"></div>
            <div class="art-sheet-tc"></div>
            <div class="art-sheet-bc"></div>
            <div class="art-sheet-cl"></div>
            <div class="art-sheet-cr"></div>
            <div class="art-sheet-cc"></div>
            <div class="art-sheet-body">
                <div class="art-nav">
                	<div class="l"></div>
                	<div class="r"></div>
                </div>
                        <div class="art-layout-cell art-sidebar1">
                        </div>
                        <div class="art-layout-cell art-content">
						
<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <div class="art-post">
                                <div class="art-post-tl"></div>
                                <div class="art-post-tr"></div>
                                <div class="art-post-bl"></div>
                                <div class="art-post-br"></div>
                                <div class="art-post-tc"></div>
                                <div class="art-post-bc"></div>
                                <div class="art-post-cl"></div>
                                <div class="art-post-cr"></div>
                                <div class="art-post-cc"></div>
                                <div class="art-post-body">
									<div class="art-post-inner art-article">
										<div class="art-postmetadataheader">
											<h2 class="art-postheader">
												<?php echo CLICK_ON_COLOR ?>
											</h2>
										</div>
										<div class="art-postcontent">
											<!-- article-content -->
	<p><?php echo "Pensez à activer l'affichage des couleurs pour qu'elles soient visibles sur les emplois du temps."; ?></p>

	<table id="edt_table_couleurs" style="width:100%">
		<thead>
		<tr><th><?php echo FIELD ?></th><th><?php echo SHORT_NAME ?></th><th><?php echo COLOR ?></th></tr>
		</thead>

		<tbody>

<?php
// On affiche la liste des matières
$req_sql = mysqli_query($GLOBALS["mysqli"], "SELECT matiere, nom_complet FROM matieres ORDER BY nom_complet");
$nbre_matieres = mysqli_num_rows($req_sql);

	for($i=0; $i < $nbre_matieres; $i++){
	$aff_matiere[$i]["court"] = mysql_result($req_sql, $i, "matiere");
	$aff_matiere[$i]["long"] = mysql_result($req_sql, $i, "nom_complet");
	// On détermine la couleur choisie
	$recher_couleur = "M_".$aff_matiere[$i]["court"];
	$color = GetSettingEdt($recher_couleur);
		if ($color == "") {
			$color = "none";
		}
		// On construit le tableau
		echo '
		<tr id="M_'.$aff_matiere[$i]["court"].'">
			<td>'.$aff_matiere[$i]["long"].'</td>
			<td>'.$aff_matiere[$i]["court"].'</td>
			<td class="cadreCouleur'.$color.'">
				<p onclick="couleursEdtAjax(\'M_'.$aff_matiere[$i]["court"].'\', \'non\');">'.MODIFY_COLOR.'</p>
			</td>
		</tr>
		';

	}
?>
		</tbody>

	</table>
											
											<div class="cleared"></div>
											<!-- /article-content -->
										</div>
										<div class="cleared"></div>
									</div>
								</div>
							</div>

							



	
						</div>
			</div>
		</div>
	</div>
	<br /><br />
</div>
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>