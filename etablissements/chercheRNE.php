<?php

/*
 *
 * Copyright 2015 Bouguin Régis
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

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}


//*************** FONCTIONS ***************
function ineSansEtab () {
	global $mysqli;
	$sql= "SELECT DISTINCT id_etablissement "
	   . "FROM j_eleves_etablissements "
	   . "WHERE id_etablissement NOT IN ( "
	   . "SELECT id_etablissement FROM `j_eleves_etablissements` JOIN etablissements ON id_etablissement = id "
	   . ") ORDER BY id_etablissement ASC";
	//echo $sql."<br />";
	$resultchargeDB = $mysqli->query($sql);
	return $resultchargeDB;
}

function enregistreEtab ($etab) {
	global $mysqli;
	$sql= "INSERT INTO `etablissements` (`id`,`nom`,`niveau`,`type`,`cp`,`ville`) "
	   . "VALUES (\"".$etab[0]."\",\"".$etab[1]."\",\"".$etab[2]."\",\"".$etab[3]."\",\"".$etab[4]."\",\"".$etab[5]."\") "
	   . "ON DUPLICATE KEY UPDATE `nom`=\"".$etab[1]."\" , `niveau`=\"".$etab[2]."\" ,"
	   . "`type`=\"".$etab[3]."\" , `cp`=\"".$etab[4]."\" , `ville`=\"".$etab[5]."\" ";
	//echo "<br />".$sql."<br />";
	$resultchargeDB = $mysqli->query($sql);
	
	if ($resultchargeDB) {
		return TRUE;
	} else {
		//printf("Message d'erreur : %s\n", $mysqli->error);
		return FALSE;
	}
	
}


//*************** Fin FONCTIONS ***************

//***** Si on a $_POST['soumet'] on enregistre ou on recherche l'établissement dans un fichier *****
$enregistrer =  isset($_POST['enregistrer']) ? $_POST['enregistrer'] : NULL;
$recherche =  isset($_POST['recherche']) ? $_POST['recherche'] : NULL;

if ($enregistrer) {
	include 'soumetRNE.php';
} elseif ($recherche) {
	include 'import1Etab.php';

}



$etabATraite = ineSansEtab ();

//**************** EN-TETE *****************
$titre_page = "Identifiants sans établissements";
$tbs_librairies[]= "script.js";

// ====== Inclusion des balises head et du bandeau =====
if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la création du fil d'ariane";

require_once("../lib/header.inc.php");
// include_once("../lib/header_template.inc.php");
//**************** FIN EN-TETE *****************
?>
<?php if (isset($_SESSION['msg_etab'])) { ?>
<p class="rouge bold center">
	<?php echo $_SESSION['msg_etab']; ?>
</p>
<?php 
	unset($_SESSION['msg_etab']);
} ?>
<?php if ($etabATraite->num_rows) { ?>
<fieldset>
	<legend>Établissements non trouvés</legend>
	<form method="post" action="chercheRNE.php" id="form_RNE">	
		<table class='boireaus'>
			<caption style="caption-side:bottom">Identifiants non rattachés à un établissement</caption>
			<tr>
				<th>Identifiant</th>
				<th>nom</th>
				<th>niveau</th>
				<th>type</th>
				<th>cp</th>
				<th>ville</th>		
				<th>Rechercher</th>	  	 	
				<th>Sauvegarder</th>	  	  	 	 
			</tr>
			<?php $cpt=-1;
			while($RNE = $etabATraite->fetch_object()){
				if ($RNE->id_etablissement){

				?>
				<tr class="lig<?php echo $cpt; ?>">
					<td>
						<?php echo $RNE->id_etablissement; ?>
						<input type="hidden" name="ine_<?php echo $RNE->id_etablissement; ?>" value="<?php echo $RNE->id_etablissement; ?>" />
					</td>
					<td>
						<input type="text" name="nom_<?php echo $RNE->id_etablissement; ?>" title="Nom de l'établissement" />
					</td>
					<td>
						<input type="text" name="niveau_<?php echo $RNE->id_etablissement; ?>" title="aucun, ecole, college, lycee, lprof …" size="14" />					
					</td>
					<td>
						<input type="text" name="type_<?php echo $RNE->id_etablissement; ?>" title="aucun, prive, public …" size="10" />		
					</td>
					<td>
						<input type="text" name="cp_<?php echo $RNE->id_etablissement; ?>" title="code postal" size="6" />
					</td>
					<td>
						<input type="text" name="ville_<?php echo $RNE->id_etablissement; ?>" title="ville" />
					</td>
					<td>
						<?php include 'importerRNE.php';  ?>
					</td>
					<td class="center">
						<button name="enregistrer"
								id="soumet_<?php echo $RNE->id_etablissement; ?>"
								title="Enregistrer les données de la ligne"
								value="<?php echo $RNE->id_etablissement; ?>" >
							enregistrer
						</button>
					</td>
				</tr>
			<?php 
					$cpt*=-1;
					}
				} ?>
		</table>
	</form>
</fieldset>


<?php } else { ?>
<p class="vert bold center">
	<br />
	Tous les identifiants sont rattachés à un établissement
</p>
		
<?php } ?>

<?php
debug_var();

require("../lib/footer.inc.php");

//$tbs_microtime	="";
//$tbs_pmv="";
//require_once ("../lib/footer_template.inc.php");
