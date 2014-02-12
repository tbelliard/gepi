<?php
/*
 *
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel, Pascal Fautrero
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

$niveau_arbo = 1;
// Initialisations files
require_once("../lib/initialisations.inc.php");
//mes fonctions
include("./fonctions_edt.php");
include("./fonctions_calendrier.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// fonction  qui permet de convertir des heures en minutes
function convert_heures_minutes($heures)
{
	// explose les heures pour avoir un tableau heure, minute
	$tab_heure = explode(':', $heures);

	//combien de minute dans une heures ?
	$total_minute = floor($tab_heure[0] * 60);

	//combien de minutes total
	if ( isset($tab_heure[1]) ) {
		$total_minute = $total_minute + $tab_heure[1];
	} else { $total_minute = $total_minute; }

  return($total_minute);
}

// fonction  qui permet de convertir des minutes en heures
function convert_minutes_heures($minutes)
{
	//combien d'heures ?
	$heure = floor($minutes / 60);

	//combien de minutes ?
	$minute = floor(($minutes - ($heure * 60)));
	  if ($minute < 10) { $minute = "0".$minute; }

	$temps = $heure."h".$minute;

  return($temps);
}

if (empty($_GET['action_sql']) and empty($_POST['action_sql'])) {$action_sql="";}
   else { if (isset($_GET['action_sql'])) {$action_sql=$_GET['action_sql'];} if (isset($_POST['action_sql'])) {$action_sql=$_POST['action_sql'];} }
if (empty($_GET['action']) and empty($_POST['action'])) {exit();}
   else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
if (empty($_GET['ouvert']) and empty($_POST['ouvert'])) { $ouvert = ''; }
   else { if (isset($_GET['ouvert'])) { $ouvert = $_GET['ouvert']; } if (isset($_POST['ouvert'])) { $ouvert = $_POST['ouvert']; } }
if (empty($_GET['ouverture']) and empty($_POST['ouverture'])) { $ouverture = ''; }
   else { if (isset($_GET['ouverture'])) { $ouverture = $_GET['ouverture']; } if (isset($_POST['ouverture'])) { $ouverture = $_POST['ouverture']; } }
if (empty($_GET['fermeture']) and empty($_POST['fermeture'])) { $fermeture = ''; }
   else { if (isset($_GET['fermeture'])) { $fermeture = $_GET['fermeture']; } if (isset($_POST['fermeture'])) { $fermeture = $_POST['fermeture']; } }
if (empty($_GET['pause']) and empty($_POST['pause'])) { $pause = ''; }
   else { if (isset($_GET['pause'])) { $pause = $_GET['pause']; } if (isset($_POST['pause'])) { $pause = $_POST['pause']; } }


// tableau semaine
	$tab_sem[0] = 'lundi';
	$tab_sem[1] = 'mardi';
	$tab_sem[2] = 'mercredi';
	$tab_sem[3] = 'jeudi';
	$tab_sem[4] = 'vendredi';
	$tab_sem[5] = 'samedi';
	$tab_sem[6] = 'dimanche';

// tableau semaine inverse
	$tab_sem_inv['lundi'] = '0';
	$tab_sem_inv['mardi'] = '1';
	$tab_sem_inv['mercredi'] = '2';
	$tab_sem_inv['jeudi'] = '3';
	$tab_sem_inv['vendredi'] = '4';
	$tab_sem_inv['samedi'] = '5';
	$tab_sem_inv['dimanche'] = '6';
	
// ajout et mise à jour de la base
if ( $action_sql === 'ajouter' or $action_sql === 'modifier' )
{
	$chaine_jours_ouverts="";
	$i = '0';
	while ( $i < '7' )
	{
		if( isset($ouvert[$i]) and !empty($ouvert[$i]) )
		{
        	$test_jour = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."horaires_etablissement
												WHERE jour_horaire_etablissement = '".$tab_sem[$i]."' AND
												date_horaire_etablissement = '0000-00-00'"),0);
			$date_horaire_etablissement = '';
			$jour_horaire_etablissement = $tab_sem[$i];
			$ouverture_horaire_etablissement = $ouverture[$i];
			$fermeture_horaire_etablissement = $fermeture[$i];
			$pause_horaire_etablissement = $pause[$i];
			$ouvert_horaire_etablissement = $ouvert[$i];

			if ( $test_jour === '0' ) {
				$requete = "INSERT INTO ".$prefix_base."horaires_etablissement
									(date_horaire_etablissement, jour_horaire_etablissement, ouverture_horaire_etablissement, fermeture_horaire_etablissement, pause_horaire_etablissement, ouvert_horaire_etablissement)
									VALUES ('".$date_horaire_etablissement."', '".$jour_horaire_etablissement."', '".$ouverture_horaire_etablissement."', '".$fermeture_horaire_etablissement."', '".$pause_horaire_etablissement."', '".$ouvert_horaire_etablissement."')";
			}
			if ( $test_jour != '0' ) {
				$requete = "UPDATE ".$prefix_base."horaires_etablissement SET date_horaire_etablissement = '".$date_horaire_etablissement."', jour_horaire_etablissement = '".$jour_horaire_etablissement."', ouverture_horaire_etablissement = '".$ouverture_horaire_etablissement."', fermeture_horaire_etablissement = '".$fermeture_horaire_etablissement."', pause_horaire_etablissement = '".$pause_horaire_etablissement."', ouvert_horaire_etablissement = '".$ouvert_horaire_etablissement."' WHERE jour_horaire_etablissement = '".$tab_sem[$i]."' AND date_horaire_etablissement = '0000-00-00'";
			}
	        mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));

			if($chaine_jours_ouverts!="") {$chaine_jours_ouverts.=",";}
			if($ouverture_horaire_etablissement!=$fermeture_horaire_etablissement) {
				$num_jour=$i+1;
				$chaine_jours_ouverts.="'$num_jour'";
			}

		} else{
			// On teste si le jour en question existe et on passe à 0 le dernier champ
			$test = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM horaires_etablissement
										WHERE jour_horaire_etablissement = '".$tab_sem[$i]."' LIMIT 1");
			$test_c = mysqli_num_rows($test);

			if ($test_c == 1) {
				$rep = mysqli_fetch_array($test);
				$requete = "UPDATE ".$prefix_base."horaires_etablissement
									SET date_horaire_etablissement = '".$rep["date_horaire_etablissement"]."',
									ouverture_horaire_etablissement = '".$ouverture[$i]."',
									fermeture_horaire_etablissement = '".$fermeture[$i]."',
									pause_horaire_etablissement = '".$pause[$i]."',
									ouvert_horaire_etablissement = '0'
								WHERE jour_horaire_etablissement = '".$tab_sem[$i]."'
								AND date_horaire_etablissement = '0000-00-00'";
				mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
			}
		}

	$i = $i + 1;
	}

	$f=fopen("../temp/info_jours.js","w+");
	fwrite($f,"// Tableau des jours ouverts
// 0 pour dimanche,
// 1 pour lundi,...
var tab_jours_ouverture=new Array($chaine_jours_ouverts);");
	fclose($f);
}


// prendre les donnees de la base
if ( $action === 'visualiser' )
{
        $i = '';
        $requete = "SELECT * FROM ".$prefix_base."horaires_etablissement WHERE date_horaire_etablissement = '0000-00-00'";
        $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
        while ( $donnee = mysqli_fetch_array($resultat))
		{
		$jour = $donnee['jour_horaire_etablissement'];
		$i = $tab_sem_inv[$jour];
		if( $donnee['ouverture_horaire_etablissement'] != '00:00:00' ) { $ouverture[$i] = $donnee['ouverture_horaire_etablissement']; } else { $ouverture[$i] = ''; }
		if( $donnee['fermeture_horaire_etablissement'] != '00:00:00' ) { $fermeture[$i] = $donnee['fermeture_horaire_etablissement']; } else { $fermeture[$i] = ''; }
		if( $donnee['pause_horaire_etablissement'] != '00:00:00' ) { $pause[$i] = $donnee['pause_horaire_etablissement']; } else { $pause[$i] = ''; }
		$ouvert[$i] = $donnee['ouvert_horaire_etablissement'];
		if( $fermeture[$i] != '00:00:00' and $ouverture[$i] != '00:00:00' and $pause[$i] != '00:00:00' and $fermeture[$i] != '' and $ouverture[$i] != '' and $pause[$i] != '') {
			$calcul = (convert_heures_minutes($fermeture[$i]) - convert_heures_minutes($ouverture[$i])) - convert_heures_minutes($pause[$i]);
			$temps_total_ouverture[$i] = convert_minutes_heures($calcul);
		} elseif ( $fermeture[$i] != '00:00:00' and $ouverture[$i] != '00:00:00' and $fermeture[$i] != '' and $ouverture[$i] != '') {
				$calcul = convert_heures_minutes($fermeture[$i]) - convert_heures_minutes($ouverture[$i]);
				$temps_total_ouverture[$i] = convert_minutes_heures($calcul);
			 }
		$i = '';
        }
}

// ===================================================================
//
//						Affichage de la page
//
// ===================================================================
// header
$titre_page = "Horaires d'ouverture de l'établissement";
$style_specifique[] = "templates/".NameTemplateEdt()."/css/style_edt";
$ua = getenv("HTTP_USER_AGENT");
if (strstr($ua, "MSIE 6.0")) {
	$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_ie6_param";
}
else if (strstr($ua, "MSIE 7")) {
	$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_ie7_param";
}
$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_param";

require_once("../lib/header.inc.php");

require_once("./menu.inc.php");
echo "<br/>\n";
echo "<div id=\"lecorps\">\n";
require_once("./menu.inc.new.php"); 

//debug_var();
?>

<?php if ($action === "visualiser") { ?>
<?php /* div de centrage du tableau pour ie5 */ ?>

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
												Définition des horaires d'ouverture de l'établissement
											</h2>
										</div>
										<div class="art-postcontent">
											<!-- article-content -->
<div style="text-align: center;">

<?php /* gestion des horaire d'ouverture */ ?>

  <form method="post" action="admin_horaire_ouverture.php?action=<?php echo $action; ?>" name="form1">
	<table cellpadding="0" cellspacing="1" class="tab_table" summary="Horaires d'ouverture">
	  <tbody>
	    <tr>
	      <th style="width: 60px;"></th>
	      <?php $i = 0;
		while ( $i < 7 ) { ?>
		      <th class="tab_th" style="width: 70px;"><?php echo $tab_sem[$i]; $i = $i + 1; ?></th>
		<?php } 

		?>

	    </tr>
	    <tr class="couleur_ligne_1">
	      <td class="tab_td_donnee_cote">Ouvert</td>
	      <?php $i = '0';
		while ( $i < '7' ) { ?>
		      <td><input name="ouvert[<?php echo $i; ?>]" value="1" type="checkbox" <?php if ( isset($ouvert[$i]) and $ouvert[$i] === '1' ) { ?>checked="checked"<?php } ?> /><?php $i = $i + 1; ?></td>
		<?php } 

		?>
	    </tr>
	    <tr class="couleur_ligne_2">
	      <td class="tab_td_donnee_cote">Ouverture &agrave;</td>
	      <?php $i = '0';
		while ( $i < '7' ) { ?>
		      <td><input name="ouverture[<?php echo $i; ?>]" size="5" maxlength="5"  value="<?php if ( isset($ouverture[$i]) and !empty($ouverture[$i]) ) { echo $ouverture[$i]; } ?>" class="input_sans_bord" /><?php $i = $i + 1; ?></td>
		<?php } ?>
	    </tr>
	    <tr class="couleur_ligne_1">
	      <td class="tab_td_donnee_cote">Fermeture à</td>
	      <?php $i = '0';
		while ( $i < '7' ) { ?>
		      <td><input name="fermeture[<?php echo $i; ?>]" size="5" maxlength="5"  value="<?php if ( isset($fermeture[$i]) and !empty($fermeture[$i]) ) { echo $fermeture[$i]; } ?>" class="input_sans_bord" /><?php $i = $i + 1; ?></td>
		<?php } ?>
	    </tr>
	    <tr class="couleur_ligne_2">
	      <td class="tab_td_donnee_cote">Temps de pause</td>
	      <?php $i = '0';
		while ( $i < '7' ) { ?>
		      <td><input name="pause[<?php echo $i; ?>]" size="5" maxlength="5"  value="<?php if ( isset($pause[$i]) and !empty($pause[$i]) ) { echo $pause[$i]; } ?>" class="input_sans_bord" /><?php $i = $i + 1; ?></td>
		<?php } ?>
	    </tr>
	    <tr class="couleur_ligne_1">
	      <td class="tab_td_donnee_cote">Temps Jour</td>
	      <?php $i = '0';
		while ( $i < '7' ) { ?>
		      <td><?php if ( isset($temps_total_ouverture[$i]) and !empty($temps_total_ouverture[$i]) ) { echo $temps_total_ouverture[$i]; } $i = $i + 1; ?></td>
		<?php } 

		
		?>
	    </tr>
	  </tbody>
	</table>
	<input type="hidden" name="action_sql" value="modifier" />
	<br/>
	<center><input type="submit" name="submit" value="Enregistrer" /></center>
  </form>
<?php /* fin de gestion des horaire d'ouverture */ 	?>

<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
											
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


<?php //mysql_close(); 

}

require("../lib/footer.inc.php");

 ?>

