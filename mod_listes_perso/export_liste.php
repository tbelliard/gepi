<?php
/**
 *
 *
 * Copyright 2015-2017 Régis Bouguin, Stephane Boireau
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité

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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_listes_perso/export_liste.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_listes_perso/export_liste.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Listes perso: Export',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
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


//On vérifie si le module est activé
//if (getSettingValue("active_module_liste_perso")!='y') {
if (getSettingAOui("active_module_liste_perso")) {
    //die("Le module n'est pas activé.");
}

//include_once 'lib/fonction_listes.php';

$id_def=isset($_GET['id_def']) ? $_GET['id_def'] : NULL;

if(isset($id_def)) {
	$sql="SELECT * FROM mod_listes_perso_definition WHERE id='".$id_def."' AND proprietaire='".$_SESSION["login"]."';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);

	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$titre_liste=$lig->nom;

		$csv="Nom;Prenom;";
		if($lig->sexe=="1") {
			$csv.="Sexe;";
		}
		if($lig->classe=="1") {
			$csv.="Classe;";
		}

		$tab_col=array();
		$sql="SELECT * FROM mod_listes_perso_colonnes WHERE id_def='".$id_def."' ORDER BY placement;";
		//echo "$sql<br />";
		$res2=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig2=mysqli_fetch_object($res2)) {
			$csv.=preg_replace("/;/", ",", $lig2->titre).";";
			$tab_col[]=$lig2->placement;
		}
		$csv.="\n";

		/*
		echo "<pre>";
		print_r($tab_col);
		echo "</pre>";
		*/

		$sql="SELECT e.* FROM mod_listes_perso_eleves m, eleves e WHERE m.id_def='".$id_def."' AND e.login=m.login ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig_ele=mysqli_fetch_object($res_ele)) {
			$csv.=$lig_ele->nom.";";
			$csv.=$lig_ele->prenom.";";
			if($lig->sexe=="1") {
				$csv.=$lig_ele->sexe.";";
			}
			if($lig->classe=="1") {
				$csv.=get_chaine_liste_noms_classes_from_ele_login($lig_ele->login).";";
			}

			for($loop=0;$loop<count($tab_col);$loop++) {
				$valeur="";
				$sql="SELECT * FROM mod_listes_perso_contenus WHERE id_def='".$id_def."' AND login='".$lig_ele->login."' AND colonne='".$tab_col[$loop]."';";
				//echo "$sql<br />";
				$res_col=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_col)>0) {
					$lig_col=mysqli_fetch_object($res_col);
					$valeur=preg_replace("/;/", ",", $lig_col->contenu);
				}
				$csv.=$valeur.";";
			}
			$csv.="\n";
		}

		//echo "<pre>$csv</pre>";

		$nom_fic="export_liste_perso_".remplace_accents($titre_liste, "all")."_".strftime("%Y%m%d_%H%M%S").".csv";
		send_file_download_headers('text/x-csv',$nom_fic);
		echo $csv;
		die();
	}
}
/*
select * from mod_listes_perso_eleves limit 5;
select * from mod_listes_perso_colonnes limit 5;
select * from mod_listes_perso_contenus limit 5;
select * from mod_listes_perso_definition limit 5;
*/

//**************** EN-TETE *****************
$titre_page = "Exports Listes perso";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<p style='color:red'>Identifiant non trouvé.</p>";

require("../lib/footer.inc.php");
?>


