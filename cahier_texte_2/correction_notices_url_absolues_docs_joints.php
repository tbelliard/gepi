<?php
/*
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
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
$accessibilite="y";

// Initialisations files
require_once("../lib/initialisations.inc.php");
//require_once("../lib/transform_functions.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/correction_notices_url_absolues_docs_joints.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/correction_notices_url_absolues_docs_joints.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Correction des notices CDT',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (!acces_cdt()) {
	die("Le module n'est pas activé.");
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

/*
$telecharger_et_corriger=isset($_POST['telecharger_et_corriger']) ? $_POST['telecharger_et_corriger'] : (isset($_GET['telecharger_et_corriger']) ? $_GET['telecharger_et_corriger'] : NULL);
*/

if(isset($_POST['is_posted'])) {
	check_token();

	$prefixe_url=isset($_POST['prefixe_url']) ? $_POST['prefixe_url'] : array();
	$suppr_prefixe_url=isset($_POST['suppr_prefixe_url']) ? $_POST['suppr_prefixe_url'] : array();

	$tab_deja=array();
	$url_absolues_gepi="";
	for($loop=0;$loop<count($prefixe_url);$loop++) {
		$prefixe_url[$loop]=preg_replace("|/{1,}$|", "", $prefixe_url[$loop]);
		if(($prefixe_url[$loop]!="")&&(!in_array($prefixe_url[$loop], $suppr_prefixe_url))&&(!in_array($prefixe_url[$loop], $tab_deja))) {
			if($url_absolues_gepi!="") {$url_absolues_gepi.="|";}
			$url_absolues_gepi.=$prefixe_url[$loop];
			$tab_deja[]=$prefixe_url[$loop];
		}
	}

	if(saveSetting("url_absolues_gepi", $url_absolues_gepi)) {
		$msg="Enregistrement effectué.<br />";
	}
	else {
		$msg="ERREUR lors de l'enregistrement.<br />";
	}
}

$eff_parcours=5;

//**************** EN-TETE *****************
$titre_page = "Cahier de textes - Notices avec docs joints en URL absolues";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

echo "<p class='bold'><a href='../cahier_texte_admin/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

$url_absolues_gepi=getSettingValue("url_absolues_gepi");

if(!isset($mode)) {
	if($url_absolues_gepi!="") {
		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=corriger'>Corriger les notices créées avant ce paramétrage</a></p>";
	}

	echo "

<h2>URL absolues des documents joints</h2>

<p>Il peut arriver que des adresses de documents joints aux cahiers de textes soient enregistrées avec un chemin absolu (<em>du type https://NOM_SERVEUR/CHEMIN_GEPI/documents/cl1234/document_XXXX.pdf ou https://IP_SERVEUR/CHEMIN_GEPI/documents/cl1234/document_XXXX.pdf</em>) alors qu'une adresse relative (<em>du type ../documents/cl1234/document_XXXX.pdf</em>) est préférable dans le cas d'un serveur en DMZ publique, mais aussi pour l'archivage et également en cas de déplacement du Gepi sur un autre serveur.</p>

<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">
	<fieldset class='fieldset_opacite50'>
		".add_token_field();

	if($url_absolues_gepi!="") {
		$tab_url=explode("|", $url_absolues_gepi);
		echo "
		<p>Les préfixes suivants sont pris en compte&nbsp;:</p>
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th>Préfixe URL absolue</th>
					<th>Supprimer</th>
				</tr>
			</thead>
			<tbody>";
		for($loop=0;$loop<count($tab_url);$loop++) {
		echo "
				<tr>
					<td>
						".$tab_url[$loop]."
						<input type='hidden' name='prefixe_url[]' value=\"".$tab_url[$loop]."\" />
					</td>
					<td>
						<input type='checkbox' name='suppr_prefixe_url[]' value=\"".$tab_url[$loop]."\" />
					</td>
				</tr>";
		}
		echo "
			</tbody>
		</table>
		<br />";
	}
	else {
		echo "
			<p>Aucun préfixe n'est encore défini.</p>";
	}

	echo "
		<p>Vous pouvez ajouter ici des URL à prendre en compte/corriger lors des prochains enregistrements de notices de compte-rendus, devoirs,...</p>

		<p>Préfixe&nbsp;: <input type='text' name='prefixe_url[]' value='' />
		<input type='hidden' name='is_posted' value='1' />
		<input type='submit' value='Valider' /></p>

		<p style='text-indent:-4em; margin-left:4em; margin-top:3em;'><em>NOTE&nbsp;:</em> Pour corriger des URL du type<br />
		&nbsp;&nbsp;&nbsp;<strong>https://NOM_SERVEUR/CHEMIN_GEPI/documents/cl1234/document_XXXX.pdf</strong><br />
		en<br />
		&nbsp;&nbsp;&nbsp;<strong>../documents/cl1234/document_XXXX.pdf</strong>,<br />
		proposez le préfixe suivant&nbsp;:<br />
		&nbsp;&nbsp;&nbsp;<strong>https://NOM_SERVEUR/CHEMIN_GEPI</strong></p>

	</fieldset>
</form>";
}
elseif($mode=="corriger") {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Paramétrage</a></p>

<h2>URL absolues des documents joints</h2>

<p>Vous allez lancer la correction des URL absolues d'après les paramétrages définis dans la page précédente.<br />
Les notices vont être parcourues par tranches de $eff_parcours.</p>
<p><a href='".$_SERVER['PHP_SELF']."?mode=corriger_confirmed".add_token_in_url()."'>Lancer la correction.</a></p>";
}
elseif($mode=="corriger_confirmed") {
	if(!isset($step)) {
		$step=1;
	}
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Paramétrage</a></p>

<h2>URL absolues des documents joints</h2>

<p class='bold'>Étape $step&nbsp;:</p>";

	check_token(false);

	$chaine_sql_cte="";
	$chaine_sql_ctde="";

	$nb_ct_entry=0;
	$nb_ct_devoirs_entry=0;
	$tab_url=explode("|", $url_absolues_gepi);
	for($loop=0;$loop<count($tab_url);$loop++) {
		//$chaine_tmp.=" OR ";
		$chaine_rech=" contenu LIKE '% href=\"".$tab_url[$loop]."/documents/%' OR contenu LIKE '% src=\"".$tab_url[$loop]."/documents/%' OR  contenu LIKE '% href=\"".$tab_url[$loop]."/cahier_texte_2/visionneur_geogebra.php%' ";

		$sql="SELECT * FROM ct_entry WHERE $chaine_rech";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_ct_entry+=mysqli_num_rows($res);

		if($chaine_sql_cte!="") {
			$chaine_sql_cte.=" UNION (".$sql.")";
		}
		else {
			$chaine_sql_cte="(".$sql.")";
		}

		$sql="SELECT * FROM ct_devoirs_entry WHERE $chaine_rech";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_ct_devoirs_entry+=mysqli_num_rows($res);

		if($chaine_sql_ctde!="") {
			$chaine_sql_ctde.=" UNION (".$sql.")";
		}
		else {
			$chaine_sql_ctde="(".$sql.")";
		}
	}

	function cdt_changer_chemin_absolu_en_relatif_log_modif($texte) {
		$debug="n";
		if($debug=="y") {
			$dirname=getSettingValue("backup_directory");
			$chemin_fichier="../backup/".$dirname."/debug_cdt_changer_chemin_absolu_en_relatif_log_modif_".strftime("%Y%m%d").".txt";
			$f=fopen($chemin_fichier, "a+");
			fwrite($f, $texte);
			fclose($f);
		}
	}

	function cdt_changer_chemin_absolu_en_relatif_sauvegarde_avant_modif($texte) {
		$dirname=getSettingValue("backup_directory");
		$chemin_fichier="../backup/".$dirname."/cdt_changer_chemin_absolu_en_relatif_sauvegarde_avant_modif_".strftime("%Y%m%d").".sql";
		$f=fopen($chemin_fichier, "a+");
		fwrite($f, $texte);
		fclose($f);
	}

	if(($nb_ct_entry==0)&&($nb_ct_devoirs_entry==0)) {
		echo "<p>Toutes les notices ont été corrigées.</p>";
	}
	else {
		echo "<p>Avant ce tour, $nb_ct_entry notices de compte-rendus et $nb_ct_devoirs_entry notices de travaux à faire contiennent des URL absolues à traiter.</p>";

		$nb_reg=0;
		$temoin_erreur="n";
		if($nb_ct_entry>0) {
			$sql=$chaine_sql_cte." LIMIT $eff_parcours;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig=mysqli_fetch_object($res)) {

				//echo "\n\n\n<hr /><hr /><hr /><pre style='color:red'>$lig->contenu</pre><hr />\n";
				$contenu_cor=cdt_changer_chemin_absolu_en_relatif($lig->contenu);
				//echo "<pre style='color:green'>$contenu_cor</pre><hr />\n";
				cdt_changer_chemin_absolu_en_relatif_log_modif("\n=========================================\n".
				"Avant correction:\n".$lig->contenu.
				"\n=========================================\n".
				"Apres correction:\n".
				$contenu_cor."\n".
				"=========================================\n\n");
				cdt_changer_chemin_absolu_en_relatif_sauvegarde_avant_modif("UPDATE ct_entry SET contenu='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->contenu)."' WHERE id_ct='$lig->id_ct';\n");

				$sql="UPDATE ct_entry SET contenu='".mysqli_real_escape_string($GLOBALS["mysqli"], $contenu_cor)."' WHERE id_ct='$lig->id_ct';";
				//echo "$sql<br />\n";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$update) {
					echo "<p style='color:red'>ERREUR&nbsp;:<br />$sql</p>";
					$temoin_erreur="y";
					break;
				}
				else {
					$nb_reg++;
				}
			}
		}

		if($temoin_erreur=="n") {
			if($nb_ct_devoirs_entry>0) {
				$sql=$chaine_sql_ctde." LIMIT $eff_parcours;";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig=mysqli_fetch_object($res)) {

					//echo "\n\n\n<hr /><hr /><hr /><pre style='color:red'>$lig->contenu</pre><hr />\n";
					$contenu_cor=cdt_changer_chemin_absolu_en_relatif($lig->contenu);
					//echo "<pre style='color:green'>$contenu_cor</pre><hr />\n";
					cdt_changer_chemin_absolu_en_relatif_log_modif("\n=========================================\n".
					"Avant correction:\n".$lig->contenu.
					"\n=========================================\n".
					"Apres correction:\n".
					$contenu_cor."\n".
					"=========================================\n\n");

					$sql="UPDATE ct_devoirs_entry SET contenu='".mysqli_real_escape_string($GLOBALS["mysqli"], $contenu_cor)."' WHERE id_ct='$lig->id_ct';";
					//echo "$sql<br />\n";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$update) {
						echo "<p style='color:red'>ERREUR&nbsp;:<br />$sql</p>";
						$temoin_erreur="y";
						break;
					}
					else {
						$nb_reg++;
					}
				}
			}
		}

		$step++;
		echo "<p>".$nb_reg." notice(s) corrigée(s).</p>

	<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" name='form1' method=\"post\">
		".add_token_field()."
		<input type='hidden' name='mode' value='corriger_confirmed' />
		<input type='hidden' name='step' value='$step' />

		<p id='p_submit'><input type='submit' value='Poursuivre' /></p>
	</form>";


		if($temoin_erreur=="n") {
			echo "<script type='text/javascript'>
			document.getElementById('p_submit').style.display='none';

			setTimeout(document.forms['form1'].submit(), 7000);
		</script>\n";
		}

	}
}
require("../lib/footer.inc.php");
die();

?>
