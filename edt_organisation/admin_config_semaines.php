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
//include("../lib/functions.php");
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




	if (empty($_GET['action_sql']) and empty($_POST['action_sql'])) {$action_sql="";}
	   else { if (isset($_GET['action_sql'])) {$action_sql=$_GET['action_sql'];} if (isset($_POST['action_sql'])) {$action_sql=$_POST['action_sql'];} }
	if (empty($_GET['action']) and empty($_POST['action'])) {exit();}
	   else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
	if (empty($_GET['num_semaine']) and empty($_POST['num_semaine'])) { $num_semaine = ''; }
	   else { if (isset($_GET['num_semaine'])) { $num_semaine = $_GET['num_semaine']; } if (isset($_POST['num_semaine'])) { $num_semaine = $_POST['num_semaine']; } }
	if (empty($_GET['type_semaine']) and empty($_POST['type_semaine'])) { $type_semaine = ''; }
	   else { if (isset($_GET['type_semaine'])) { $type_semaine = $_GET['type_semaine']; } if (isset($_POST['type_semaine'])) { $type_semaine = $_POST['type_semaine']; } }
// ajout du champ num_semaines_etab
$num_interne = isset($_GET["num_interne"]) ? $_GET["num_interne"] : (isset($_POST["num_interne"]) ? $_POST["num_interne"] : NULL);

// ======================================================
//
//		fonctions utilisées par config_semaines
//
// ======================================================
	
function trouverDates($numero_semaine){
	// fonction qui permet de déterminer la date de début de la semaine (lundi)
	/*
	$ts_depart = 1186358400;
	$ts_depart = 1217887200; // 5 aout 2008 à 00:00:00
	$ts_depart = 1249336800;
	$ts_depart = 1280771449; // 2 août 2010 à 00:00:00
*/
	
	// On recherche l'année
	$maintenant = date("n");
	if ($maintenant >= 8) {
		$annee = date("Y");
	} else {
		$annee = date("Y") - 1;
	}
	
	// On recherche le premier lundi du mois d'Août
	$lundi1=1;
	while (date("N",mktime(0, 0, 0, 8, $lundi1, $annee))!=1) {
		$lundi1++;
	}
	
	// On recherche le lundi de la semaine 32
	while (date("W",mktime(0, 0, 0, 8, $lundi1, $annee))<32) {
		$lundi1 = $lundi1 + 7;
	}
	while (date("W",mktime(0, 0, 0, 8, $lundi1, $annee))>32) {
		$lundi1 = $lundi1 - 7;
	}
	
	$ts_depart = mktime(1, 0, 0, 8, $lundi1, $annee);

    $fin_temp = NumLastWeek();

	if ($numero_semaine == 32) {
		$ts = $ts_depart;
	}elseif ($numero_semaine > 32 AND $numero_semaine <= $fin_temp) {
		$coef_multi = $numero_semaine - 32;
		$ts = $ts_depart + ($coef_multi * 604800);
	}elseif ($numero_semaine < 32 AND $numero_semaine >= 1) {
		$coef_multi = ($fin_temp - 32) + $numero_semaine;
		$ts = $ts_depart + ($coef_multi * 604800);
	}else {
		$ts = "";
	}
	return $ts;
}

// ======================================================
//
//				préparation de la page
//
// ======================================================

// ajout et mise à jour de la base
if ( $action_sql === 'ajouter' or $action_sql === 'modifier' )
{
	$i = '0';
	$fin = NumLastWeek();
	
	while ( $i < $fin )
	{
		if( isset($num_semaine[$i]) and !empty($num_semaine[$i]) )
		{
			$test_num_semaine = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."edt_semaines WHERE num_edt_semaine = '".$num_semaine[$i]."'"),0);
			$num_edt_semaine = $num_semaine[$i];
			$type_edt_semaine = $type_semaine[$i];
	
			if ( $test_num_semaine === '0' ) { $requete = "INSERT INTO ".$prefix_base."edt_semaines (num_edt_semaine, type_edt_semaine) VALUES ('".$num_edt_semaine."', '".$type_edt_semaine."')"; }
			if ( $test_num_semaine != '0' ) { $requete = "UPDATE ".$prefix_base."edt_semaines SET type_edt_semaine = '".$type_edt_semaine."', num_semaines_etab = '".$num_interne[$i]."' WHERE num_edt_semaine = '".$num_edt_semaine."'"; }
			//echo "$requete<br />";
			mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		}
	
	$i = $i + 1;
	}

	//$action='visualiser';
}


// prendre les donnees de la base
if ( $action === 'visualiser' )
{
        $i = '0';
        $requete = "SELECT * FROM ".$prefix_base."edt_semaines";
        $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
        while ( $donnee = mysqli_fetch_array($resultat))
	{
		$num_semaine[$i] = $donnee['num_edt_semaine'];
		//$num_interne[$i] = $donnee['id_edt_semaine'];
		$num_interne[$i] = $donnee['num_semaines_etab'];
		$type_semaine[$i] = $donnee['type_edt_semaine'];
		$i = $i + 1;
        }
}


// ======================================================
//
//		On traite l'affichage du tableau récapitulatif
//
// ======================================================	


if ($action === "visualiser") {
	
	//====================================
	// header
	$titre_page = "Définition des types de semaine de l'établissement";
	$style_specifique = "templates/".NameTemplateEdt()."/css/style_edt";
	require_once("../lib/header.inc.php");
	//====================================
	
	//debug_var();
	
	/* gestion des jours de chaque semaine */
	// On considère que la 32e semaine commence le 6 août 2007
	// En timestamp Unix GMT, cette date vaut 1186358400 secondes
	// RAPPEL : une journée a 86400 secondes et une semaine en a 604800

	require_once("./menu.inc.php");
	?>
	<br/>
	<!-- la page du corps de l'EdT -->

	<div id="lecorps">

		<?php 
		require_once("./menu.inc.new.php"); ?>
		
		<h2>Définition des types de semaines</h2>

		<div style="text-align: center;">			
			<form method="post" action="admin_config_semaines.php?action=<?php echo $action; ?>" name="form1">
					<input type="submit" name="submit" value="Enregistrer" />

				<br /><br />			

				<table cellpadding="0" cellspacing="1" class="tab_table" summary="Semaines">
				<tbody>
					<tr>
						<th class="tab_th" style="width: 100px;">Semaine n°<br /> (officiel)</th>
						<th class="tab_th" style="width: 100px;">Num&eacute;ro<br />interne
						<br />
						<a href="javascript: numerotation_auto()" title="Numérotation automatique; Laisser vides les champs à ne pas traiter"><img src="../images/icons/wizard.png" width="16" height="16" alt="Numérotation automatique d'après le premier champ" /></a>
						</th>
						<th class="tab_th" style="width: 100px;">Type
						<br />
						<a href="javascript: alterne_auto_semaines_AB()" title="Alterner les semaines A et B; Laisser vides les champs à ne pas traiter"><img src="../images/icons/wizard.png" width="16" height="16" alt="Alterner les semaines A/B" /></a>
						</th>
						<th class="tab_th" style="width: 200px;">Du</th>
						<th class="tab_th" style="width: 200px;">au</th>
					</tr>
					<?php
					// On permet l'affichage en commençant par la 32ème semaine et en terminant par la 31 ème de l'année suivante
					// attention, on part du lundi à 00:00:00, le samedi matin à la même heure est donc 86400*5 fois plus loin (et pas 6*86400 fois).
					$i = '31';
					$i_initial=$i;
					$ic = '1';
					$fin = NumLastWeek();
					$fin_annee = $fin;
					$j=0;
					while ( $i < $fin ) {
						if ($ic === '1') {
							$ic = '2';
							$couleur_cellule = 'couleur_ligne_1';
						} else {
							$couleur_cellule = 'couleur_ligne_2';
							$ic = '1';
						}
					?>
					<tr class="<?php echo $couleur_cellule; ?>">
						<td><input type="hidden" name="num_semaine[<?php echo $i; ?>]" value="<?php echo $num_semaine[$i]; ?>" /><strong><?php echo $num_semaine[$i]; ?></strong></td>
						<td><input type="text" id="num_interne_<?php echo $j; ?>" name="num_interne[<?php echo $i; ?>]" size="3" value="<?php echo $num_interne[$i]; ?>" class="input_sans_bord" /></td>
						<td><input type="text" id="type_semaine_<?php echo $j; ?>" name="type_semaine[<?php echo $i; ?>]" size="3" maxlength="10"  value="<?php if ( isset($type_semaine[$i]) and !empty($type_semaine[$i]) ) { echo $type_semaine[$i]; } ?>" class="input_sans_bord" /></td>
						<td> lundi <?php echo date("d-m-Y", (int) trouverDates($i+1)); ?> </td>
						<td> samedi <?php echo date("d-m-Y", (trouverDates($i+1) + 5*86400)); ?> </td>


					</tr>
					<?php
						if ($i == $fin_annee-1) {
							$i = '0';
							$fin = '31';
						} else {
							$i = $i + 1;
						}
						$j++;
					} // fin du while ( $i < '52'...
					?>
				</tbody>
				</table>
				<br />
				<input type="hidden" name="action_sql" value="modifier" />
				<input type="submit" name="submit" value="Enregistrer" />
			</form>
			<br /><br />

			<script type="text/javascript">
				function numerotation_auto() {
					if(document.getElementById('num_interne_0')) {
						v=document.getElementById('num_interne_0').value;
						if(v=='') {v=0;}
						for(j=1;j<=51;j++) {
							//if((document.getElementById('num_interne_'+j))&&(document.getElementById('num_interne_'+j).value!='')) {
							//if(j==12) {}
							if(document.getElementById('num_interne_'+j)) {
								if(document.getElementById('num_interne_'+j).value!='') {
									v=eval(v)+1;
									document.getElementById('num_interne_'+j).value=v;
								}
							}
						}
					}
				}

				function alterne_auto_semaines_AB() {
					if(document.getElementById('type_semaine_0')) {
						v=document.getElementById('type_semaine_0').value;
						for(j=1;j<=51;j++) {
							if(document.getElementById('type_semaine_'+j)) {
								// Test pour permettre de fixer les vacances avant et alterner ce qui reste
								if((document.getElementById('type_semaine_'+j).value=="A")||(document.getElementById('type_semaine_'+j).value=="B")) {
									if(v=='A') {v='B';} else {v='A';}
									document.getElementById('type_semaine_'+j).value=v;
								}
							}
						}
					}
				}
			</script>

		</div>
	</div>

<?php
/* fin de gestion des horaire d'ouverture */
/* fin du div de centrage du tableau pour ie5 */

//mysql_close();

} // if ($action === "visualiser")

require("../lib/footer.inc.php");

?>

