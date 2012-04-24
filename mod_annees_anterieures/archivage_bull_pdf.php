<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
require_once("../lib/initialisations.inc.php");
//include("../lib/initialisationsPropel.inc.php");
//require_once("./fonctions_annees_anterieures.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();};

// INSERT INTO droits VALUES ('/mod_annees_anterieures/archivage_bull_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génération archives bulletins PDF', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$generer_fichiers_pdf_archivage=isset($_POST['generer_fichiers_pdf_archivage']) ? $_POST['generer_fichiers_pdf_archivage'] : (isset($_GET['generer_fichiers_pdf_archivage']) ? $_GET['generer_fichiers_pdf_archivage'] : NULL);

$archivage_fichiers_bull_pdf_auto=isset($_POST['archivage_fichiers_bull_pdf_auto']) ? $_POST['archivage_fichiers_bull_pdf_auto'] : (isset($_GET['archivage_fichiers_bull_pdf_auto']) ? $_GET['archivage_fichiers_bull_pdf_auto'] : "n");

// Si le module n'est pas activé...
if($gepiSettings['active_annees_anterieures'] !="y"){
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";

// Suppression des données archivées pour une année donnée.
if (isset($_GET['action']) and ($_GET['action']=="supp_annee")) {
	check_token();

	$sql="DELETE FROM archivage_disciplines WHERE annee='".$_GET["annee_supp"]."';";
	$res_suppr1=mysql_query($sql);

	// Maintenant, on regarde si l'année est encore utilisée dans archivage_types_aid
	// Sinon, on supprime les entrées correspondantes à l'année dans archivage_eleves2 car elles ne servent plus à rien.
	$test = sql_query1("select count(annee) from archivage_types_aid where annee='".$_GET['annee_supp']."'");
	if ($test == 0) {
		$sql="DELETE FROM archivage_eleves2 WHERE annee='".$_GET["annee_supp"]."';";
		$res_suppr2=mysql_query($sql);
	} else {
		$res_suppr2 = 1;
	}

	$sql="DELETE FROM archivage_ects WHERE annee='".$_GET["annee_supp"]."';";
	$res_suppr3=mysql_query($sql);

	// Maintenant, il faut supprimer les données élèves qui ne servent plus à rien
	suppression_donnees_eleves_inutiles();

	if (($res_suppr1) and ($res_suppr2) and ($res_suppr3)) {
		$msg = "La suppression des données a été correctement effectuée.";
	} else {
		$msg = "Un ou plusieurs problèmes ont été rencontrés lors de la suppression.";
	}

}

if(isset($_GET['chgt_annee'])) {$_SESSION['chgt_annee']="y";}

$themessage  = 'Etes-vous sûr de vouloir supprimer toutes les données concerant cette année ?';

//**************** EN-TETE *****************
$titre_page = "Générer les bulletins PDF par élève";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************


if(!isset($generer_fichiers_pdf_archivage)){
	echo "<div class='norme'><p class=bold><a href='";
	if(isset($_SESSION['chgt_annee'])) {
		echo "../gestion/changement_d_annee.php";
	}
	else {
		echo "./index.php";
	}
	echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";
	echo "</p></div>\n";

	echo "<p>Un fichier PDF par élève pour les N périodes va être généré dans un dossier temporaire.<br />Un zip sera généré pour permettre le téléchargement d'un coup de l'ensemble.<br />...</p>\n";

	echo "<p><a href='".$_SERVER['PHP_SELF']."?generer_fichiers_pdf_archivage=y".add_token_in_url()."'>Générer les PDF par élève</a></p>\n";

	/*
	$sql="SELECT DISTINCT annee FROM archivage_disciplines ORDER BY annee";
	$res_annee=mysql_query($sql);
	//if(){
	if(mysql_num_rows($res_annee)==0){
		echo "<p>Concernant les données autres que les AIDs, aucune année n'est encore sauvegardée.</p>\n";
	}
	else{
		echo "<p>Voici la liste des années sauvegardées:</p>\n";
		echo "<ul>\n";
		while($lig_annee=mysql_fetch_object($res_annee)){
			$annee_scolaire=$lig_annee->annee;
			echo "<li><b>Année $annee_scolaire (<a href='".$_SERVER['PHP_SELF']."?action=supp_annee&amp;annee_supp=".$annee_scolaire.add_token_in_url()."'   onclick=\"return confirm_abandon (this, 'yes', '$themessage')\">Supprimer toutes les données archivées pour cette année</a>) :<br /></b> ";
			$sql="SELECT DISTINCT classe FROM archivage_disciplines WHERE annee='$annee_scolaire' ORDER BY classe;";
			$res_classes=mysql_query($sql);
			if(mysql_num_rows($res_classes)==0){
				echo "Aucune classe???";
			}
			else{
				$lig_classe=mysql_fetch_object($res_classes);
				echo $lig_classe->classe;

				while($lig_classe=mysql_fetch_object($res_classes)){
					echo ", ".$lig_classe->classe;
				}
			}
			echo "</li>\n";
		}
		echo "</ul>\n";
		echo "<p><br /></p>\n";

	}
	echo "<p>Sous quel nom d'année voulez-vous sauvegarder l'année?</p>\n";
	$default_annee=getSettingValue('gepiYear');

	if($default_annee==""){
		$instant=getdate();
		$annee=$instant['year'];
		$mois=$instant['mon'];

		$annee2=$annee+1;
		$default_annee=$annee."-".$annee2;
	}

	echo "<p>Année&nbsp;: <input type='text' name='annee_scolaire' value='$default_annee' /></p>\n";

	echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";
	*/
}
else {
	echo "<div class='norme'><p class=bold><a href='";
	if(isset($_SESSION['chgt_annee'])) {
		echo "../gestion/changement_d_annee.php";
	}
	else {
		echo "./index.php";
	}
	echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";

	check_token(false);

	$sql="SELECT * FROM classes ORDER BY classe;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>Aucune classe trouvée.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$cpt=0;
	$tab_classe=array();
	while($lig=mysql_fetch_object($res)) {
		$tab_classe[$cpt]['id_classe']=$lig->id;
		$tab_classe[$cpt]['classe']=$lig->classe;
		//echo "<p>\$tab_classe[$cpt]['id_classe']=".$tab_classe[$cpt]['id_classe']."<br />";
		//echo "\$tab_classe[$cpt]['classe']=".$tab_classe[$cpt]['classe']."</p>";
		$cpt++;
	}

	if(isset($id_classe)) {
		$dossier_archivage_pdf=getPref($_SESSION['login'], 'dossier_archivage_pdf', 'bulletins_pdf_individuels_eleves_'.strftime('%Y%m%d'));

		$trouve="n";
		for($loop=0;$loop<$cpt;$loop++) {
			if((isset($tab_classe[$loop-1]['id_classe']))&&($tab_classe[$loop-1]['id_classe']==$id_classe)) {
				//echo "\$tab_classe[$loop-1]['id_classe']=".$tab_classe[$loop-1]['id_classe']."<br />";
				$id_classe=$tab_classe[$loop]['id_classe'];
				$classe=$tab_classe[$loop]['classe'];
				$trouve="y";
				break;
			}
		}

		if($trouve=='n') {
			echo "<p>Toutes les classes ont été parcourues.<br />Il ne reste que les élèves ayant changé de classe à traiter (<span style='color:red'>A FAIRE</span>).<br />Et enfin, il reste le ZIP à générer (<span style='color:red'>A FAIRE</span>).</p>\n";
			echo "<p>Dossier temporaire d'archivage&nbsp;: <a href='../temp/".get_user_temp_directory()."/".$dossier_archivage_pdf."/' target='_blank'>$dossier_archivage_pdf</a></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
	else {
		$dossier_archivage_pdf=savePref($_SESSION['login'], 'dossier_archivage_pdf', 'bulletins_pdf_individuels_eleves_'.strftime('%Y%m%d'));
		@mkdir("../temp/".get_user_temp_directory()."/".$dossier_archivage_pdf);

		$id_classe=$tab_classe[0]['id_classe'];
		$classe=$tab_classe[0]['classe'];
	}

	//echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"../bulletin/bull_index.php\" method=\"post\">\n";

	// Boucler sur les classes
	echo "<p>Archiver la classe de $classe&nbsp;: ";
	echo "<input type='hidden' name='mode_bulletin' value='pdf' />\n";
	echo "<input type='hidden' name='type_bulletin' value='-1' />\n";

	echo "<input type='hidden' name='bull_pdf_debug' value='n' />\n";
	echo "<input type='hidden' name='generer_fichiers_pdf_archivage' value='y' />\n";

	echo "<input type='hidden' name='choix_periode_num' value='fait' />\n";

	echo "<input type='hidden' name='un_seul_bull_par_famille' value='oui' />\n";

	echo "<input type='hidden' name='b_adr_pg' value='xx' />\n";

	echo "<input type='hidden' name='bouton_valide_select_eleves1' value='Valider' />\n";
	echo "<input type='hidden' name='valide_select_eleves' value='y' />\n";

	echo "<input type='hidden' name='tab_id_classe[]' value='$id_classe' />\n";

	// Pour ne pas avoir à faire la liste des périodes à ce stade:
	echo "<input type='hidden' name='tab_periode_num[]' value='1' />\n";
	echo "<input type='hidden' name='toutes_les_periodes' value='y' />\n";

	// Pour ne pas avoir à poster la liste des élèves: (sauf pour la dernière étape avec les élèves qui ont changé de classe)
	echo "<input type='hidden' name='tous_les_eleves' value='y' />\n";

	echo "<br />\n";
	echo "<input type='checkbox' name='archivage_fichiers_bull_pdf_auto' id='archivage_fichiers_bull_pdf_auto' value='y' ";
	if($archivage_fichiers_bull_pdf_auto=='y') {echo "checked ";}
	echo "/><label for='archivage_fichiers_bull_pdf_auto'> Boucler automatiquement sur la liste des classes</label>\n";

	echo "<br />\n";
	echo "<span id='bouton_validation'><input type='submit' name='valider' value='Valider' /></span>\n";
	echo "</p>\n";

	echo "</form>\n";

	if($archivage_fichiers_bull_pdf_auto=='y') {
		echo "<script type='text/javascript'>
	document.getElementById('bouton_validation').innerHTML='Dans un instant... ou un peu plus;)';
	setTimeout('document.formulaire.submit()', 2000);
</script>\n";
	}


}

echo "<br />\n";
require("../lib/footer.inc.php");
?>
