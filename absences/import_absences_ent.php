<?php
@set_time_limit(0);
/*
* $Id$
*
* Copyright 2001, 2020 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

// INSERT INTO droits VALUES ('/absences/import_absences_csv.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', '');
$sql="SELECT 1=1 FROM droits WHERE id='/absences/import_absences_ent.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits VALUES ('/absences/import_absences_ent.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', '');";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


if(isset($_POST['enregistrer_absences'])) {
	check_token();
	$msg='';

	if(!isset($_POST['periode'])) {
		$msg="Vous n'avez pas choisi la période.<br />";
	}
	elseif(!isset($_POST['eleve_nb_abs'])) {
		$msg="Vous n'avez pas choisi de ligne à importer.<br />";
	}
	else {
		// On ne va écraser que ce qui est transmis.
		// Si on n'a pas coché une ligne, mais qu'il existe des enregistrements, ils ne seront pas supprimés

		$num_periode=$_POST['periode'];

		$eleve_nb_abs=$_POST['eleve_nb_abs'];

		$recapitulatif='';
		$nb_ok=0;
		$nb_err=0;

		$tab_verrouiller=array();
		foreach($eleve_nb_abs as $key => $value) {
			$tab=explode('|', $value);
			$ele_login=$tab[0];
			$nb_abs=$tab[1];
			$nb_nj=$tab[2];

			// En attendant l'extraction
			$nb_ret=0;

			$sql="SELECT id_classe FROM j_eleves_classes WHERE periode='".$num_periode."' AND login='".$ele_login."';";
			$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_clas)>0) {
				$lig=mysqli_fetch_object($res_clas);
				$id_classe=$lig->id_classe;

				if(!isset($tab_verrouiller[$id_classe])) {
					$sql="SELECT verouiller FROM periodes WHERE num_periode='".$num_periode."' AND id_classe='".$id_classe."';";
					$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_clas)>0) {
						$lig=mysqli_fetch_object($res_clas);
						$tab_verrouiller[$id_classe]=$lig->verouiller;
						if($lig->verouiller=='O') {
							$msg.="La période ".$num_periode." est close pour la classe ".get_nom_classe($id_classe).".<br />";
						}
					}
					else {
						$msg.="Bizarre&nbsp;: Aucun enregistrement n'a été trouvé pour la classe n°".get_nom_classe($id_classe)." en période ".$num_periode." dans la table 'periodes'.<br />";
						$tab_verrouiller[$id_classe]='O';
					}
				}

				if((isset($tab_verrouiller[$id_classe]))&&($tab_verrouiller[$id_classe]!='O')) {

					$sql="SELECT 1=1 FROM absences WHERE periode='$num_periode' AND login='".$ele_login."';";
					$test1=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test1)==0){
						$sql="INSERT INTO absences SET periode='$num_periode',
														login='".$ele_login."',
														nb_absences='".$nb_abs."',
														nb_retards='".$nb_ret."',
														non_justifie='".$nb_nj."';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if($insert){
							$nb_ok++;
							$recapitulatif.="<span style='color:green;'>".$ele_login."</span> ";
						}
						else{
							$nb_err++;
							$recapitulatif.="<span style='color:red;'>".$ele_login."</span> ";
						}
					}
					else{
						// En attendant l'extraction
						/*
						$sql="UPDATE absences SET nb_absences='".$nb_abs."',
													nb_retards='".$nb_ret."',
													non_justifie='".$nb_nj."'
												WHERE periode='$num_periode' AND
														login='".$ele_login."';";
						*/
						$sql="UPDATE absences SET nb_absences='".$nb_abs."',
													non_justifie='".$nb_nj."'
												WHERE periode='$num_periode' AND
														login='".$ele_login."';";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if($update){
							$nb_ok++;
							$recapitulatif.="<span style='color:green;'>".$ele_login."</span> ";
						}
						else{
							$nb_err++;
							$recapitulatif.="<span style='color:red;'>".$ele_login."</span> ";
						}
					}

				}

			}
			else {
				$msg.='Classe non trouvée pour <a href="../eleves/visu_eleve.php?ele_login='.$ele_login.'" target="_blank">'.get_nom_prenom_eleve($ele_login).'</a> en période '.$num_periode.'.<br />';
			}
		}

		if($nb_ok>0) {
			$msg.=$nb_ok." enregistrement(s) effectué(s).<br />";
		}

		if($nb_err>0) {
			$msg.=$nb_err." erreur(s).<br />";
		}

		if($nb_err>0) {
			$msg.="Récapitulatif&nbsp;: ".$recapitulatif."<br />";
		}
	}
}



//**************** EN-TETE *****************
$titre_page = "Import absences ENT";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE ***************

//debug_var();

if(isset($_GET['ad_retour'])){
	$_SESSION['ad_retour']=$_GET['ad_retour'];
}

// Initialisation du répertoire actuel de sauvegarde
//$dirname = getSettingValue("backup_directory");

echo "<h2 align='center'>Import des absences depuis un fichier CSV ENT</h2>\n";

//echo "<p><a href='index.php'>Retour</a>|\n";
echo "<p class=bold><a href='";
if(isset($_SESSION['ad_retour'])){
	echo $_SESSION['ad_retour'];
}
else{
	echo "index.php";
}
echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";

// Uploader le fichier
// Identifier les élèves
// Enregistrer

$is_uploaded_file=isset($_POST['is_uploaded_file']) ? $_POST['is_uploaded_file'] : NULL;

if(!isset($is_uploaded_file)) {

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi_csv' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='is_uploaded_file' value='y' />
		<input type='hidden' name='mode' value='1' />

		<p>L'import des absences depuis un fichier CSV ENT se déroule comme suit&nbsp;:</p>
		<ol>
			<li>Envoi du fichier;</li>
			<li>Rapprochements et choix de la période;</li>
			<li>Enregistrement.</li>
		</ol>

		<p>
			Veuillez fournir le fichier CSV&nbsp;:<br />
			<input type=\"file\" size=\"65\" name=\"csv_file\" id='input_csv_file' class='fieldset_opacite50' style='padding:5px; margin:5px;' /><br />
			<input type='hidden' name='action' value='upload' />
		</p>

		<p>
			<input type='submit' id='input_submit' value='Valider' />
			<input type='button' id='input_button' value='Valider' style='display:none;' onclick=\"check_champ_file()\" />
		</p>

		<!--p style='text-indent:-4em; margin-left:4em; margin-top:1em;'-->
		<p style='margin-top:1em;'>
			<em>NOTES&nbsp;:</em>
		</p>
		<ul>
			<li>
				<p>La page est conçue pour traiter un export de l'ENT Arsène de Kosmos.<br />
				C'est aussi l'ENT27 utilisé dans l'Eure.</p>
				<p>Le CSV attendu correspond à l'export <strong>Absentéisme.xls</strong> généré par l'ENT doit comporter les champs suivants&nbsp;:<br />
					Classe;Élève;½ j. d'absences comptabilisées;½ j. d'absences non justifiées comptabilisées;
				</p>
				<p>
					Il manque les retards dans cet export.
				</p>
			</li>
			<li>
				<p>La période d'import devra être choisie dans la 2è phase, après l'envoi du fichier CSV.</p>
			</li>
	</fieldset>

	<script type='text/javascript'>
		document.getElementById('input_submit').style.display='none';
		document.getElementById('input_button').style.display='';

		function check_champ_file() {
			fichier=document.getElementById('input_csv_file').value;
			//alert(fichier);
			if(fichier=='') {
				alert('Vous n\'avez pas sélectionné de fichier CSV à envoyer.');
			}
			else {
				document.getElementById('form_envoi_csv').submit();
			}
		}
	</script>
</form>";

	echo "<p><br /></p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi_csv2' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='is_uploaded_file' value='y' />
		<input type='hidden' name='mode' value='2' />

		<p style='color:red'>Mode en cours de développement <em>(non achevé).</em></p>
		<p>Utilisation d'un export alternatif correspondant au feuillet Données de l'export <strong>STATS_Nième trimestre_XXX_absence_retard.xlsx</strong>.</p>

		<p style='color:red'>L'import des absences depuis un fichier CSV ENT se déroule comme suit&nbsp;:</p>
		<ol>
			<li>Envoi du fichier;</li>
			<li>Rapprochements et choix de la période;</li>
			<li>Enregistrement.</li>
		</ol>

		<p>
			Veuillez fournir le fichier CSV&nbsp;:<br />
			<input type=\"file\" size=\"65\" name=\"csv_file\" id='input_csv_file2' class='fieldset_opacite50' style='padding:5px; margin:5px;' /><br />
			<input type='hidden' name='action' value='upload' />
		</p>

		<p>
			<input type='submit' id='input_submit2' value='Valider' />
			<input type='button' id='input_button2' value='Valider' style='display:none;' onclick=\"check_champ_file2()\" />
		</p>

		<!--p style='text-indent:-4em; margin-left:4em; margin-top:1em;'-->
		<p style='margin-top:1em;'>
			<em>NOTES&nbsp;:</em>
		</p>
		<ul>
			<li>
				<p>La page est conçue pour traiter un export de l'ENT Arsène de Kosmos.<br />
				C'est aussi l'ENT27 utilisé dans l'Eure.</p>
				<p>Le CSV attendu correspond à l'export <strong>STATS_Nième trimestre_XXX_absence_retard.xlsx</strong> généré par l'ENT doit comporter les champs suivants&nbsp;:<br />
					<strong style='color:red'>A DETAILLER</strong><br />
					Avec le point-virgule comme séparateur de champs.
				</p>
			</li>
			<li>
				<p>La période d'import devra être choisie dans la 2è phase, après l'envoi du fichier CSV.</p>
			</li>
	</fieldset>

	<script type='text/javascript'>
		document.getElementById('input_submit2').style.display='none';
		document.getElementById('input_button2').style.display='';

		function check_champ_file2() {
			fichier=document.getElementById('input_csv_file2').value;
			//alert(fichier);
			if(fichier=='') {
				alert('Vous n\'avez pas sélectionné de fichier CSV à envoyer.');
			}
			else {
				document.getElementById('form_envoi_csv2').submit();
			}
		}
	</script>
</form>";


	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();
}


check_token(false);

$tempdir=get_user_temp_directory();
if(!$tempdir){
	echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
	require("../lib/footer.inc.php");
	die();
}


$post_max_size=ini_get('post_max_size');
$upload_max_filesize=ini_get('upload_max_filesize');
$max_execution_time=ini_get('max_execution_time');
$memory_limit=ini_get('memory_limit');

$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

if(!is_uploaded_file($csv_file['tmp_name'])) {
	echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

	echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
	echo "post_max_size=$post_max_size<br />\n";
	echo "upload_max_filesize=$upload_max_filesize<br />\n";
	echo "</p>\n";

	require("../lib/footer.inc.php");
	die();
}

if(!file_exists($csv_file['tmp_name'])){
	echo "<p style='color:red;'>Le fichier aurait été uploadé... mais ne serait pas présent/conservé.</p>\n";

	echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
	echo "post_max_size=$post_max_size<br />\n";
	echo "upload_max_filesize=$upload_max_filesize<br />\n";
	echo "et le volume de ".$csv_file['name']." serait<br />\n";
	echo "\$csv_file['size']=".volume_human($csv_file['size'])."<br />\n";
	echo "</p>\n";

	//echo "<p>Il semblerait que l'absence d'extension .XML puisse aussi provoquer ce genre de symptômes.<br />Dans ce cas, ajoutez l'extension et ré-essayez.</p>\n";

	require("../lib/footer.inc.php");
	die();
}

echo "<p>Le fichier a été uploadé.</p>\n";



$dest_file="../temp/".$tempdir."/absences_ent_".strftime('%Y%m%d_%H%M%S').".csv";
$source_file=$csv_file['tmp_name'];
$res_copy=copy("$source_file" , "$dest_file");

if(!$res_copy){
	echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
	require("../lib/footer.inc.php");
	die();
}

echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

if($_POST['mode']=='1') {

	$fp=fopen($dest_file,"r");

	// Lire la ligne d'entête pour repérer les indices des colonnes recherchées
	$tabchamps = array("rne", "uid", "classe", "profil", "prenom", "nom", "login", "mot de passe", "cle de jointure", "uid pere", "uid mere", "uid tuteur1", "uid tuteur2", "prenom enfant", "nom enfant", "adresse", "code postal", "ville", "pays");

	$tabchamps = array("Classe", "Élève", "½ j. d'absences comptabilisées", "½ j. d'absences non justifiées comptabilisées");
	$tabchamps2 = array("classe", "eleve", "nb_abs", "nb_nj");

	// Lecture de la ligne 1 et la mettre dans $temp
	//$ligne_entete=trim(fgets($fp,4096));
	// Au cas où on aurait des champs entourés de guillemets
	$ligne_entete=trim(preg_replace('/"/', '', (preg_replace('/,/', ';', (fgets($fp,4096))))));
	//echo "$ligne_entete<br />";
	$en_tete=explode(";", $ligne_entete);

	$tabindice=array();

	// On range dans tabindice les indices des champs retenus
	for ($k = 0; $k < count($tabchamps); $k++) {
		for ($i = 0; $i < count($en_tete); $i++) {
			if (casse_mot(remplace_accents($en_tete[$i]),'min') == casse_mot(remplace_accents($tabchamps[$k]), 'min')) {
				$tabindice[$tabchamps2[$k]] = $i;
				echo "<span style='color:green'>Champ  \$tabchamps2[$k]=".$tabchamps[$k]." (".$tabchamps2[$k].") trouvé à l'indice $i (\$en_tete[$i]=".$en_tete[$i].")</span><br />";
			}
		}
	}

	if((!isset($tabindice['classe']))||(!isset($tabindice['eleve']))||(!isset($tabindice['nb_abs']))||(!isset($tabindice['nb_nj']))) {
		echo "<p style='color:red'>La ligne d'entête ne comporte pas un des champs indispensables (<em>classe, eleve, nb_abs, nb_nj</em>).</p>";
		require("../lib/footer.inc.php");
		die();
	}


	$sql="SELECT MAX(num_periode) AS maxper FROM periodes;";
	$res_maxper=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_maxper)==0) {
		echo "<p style='color:red'>Le nombre max de périodes pour une classe n'a pas été trouvé.<br />Y a-t-il des classes avec périodes associées.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	$lig=mysqli_fetch_object($res_maxper);
	$maxper=$lig->maxper;

	// Faut-il faire une première passe pour identifier les classes?

	echo "
	<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_rapprochements' method='post'>
		<fieldset class='fieldset_opacite50'>
			".add_token_field()."

			<p>Lecture du fichier et tentative de rapprochement&nbsp;: </p>
			<table class='boireaus boireaus_alt'>
				<thead>
					<tr>
						<th colspan='4' style='background-color:azure'>Fichier CSV</th>
						<th style='background-color:plum'>Gepi</th>
						<th rowspan='2'>
							Coche
							<br />
							<a href=\"javascript:import_abs_ent_tout_cocher_decocher(true)\">
								<img src='../images/enabled.png' class='icone20' />
							</a>
							/
							<a href=\"javascript:import_abs_ent_tout_cocher_decocher(false)\">
								<img src='../images/disabled.png' class='icone20' />
							</a>
						</th>
					</tr>
					<tr>
						<th style='background-color:azure'>Classe</th>
						<th style='background-color:azure'>Élève</th>
						<th style='background-color:azure'>Nombre<br />d'absences</th>
						<th style='background-color:azure'>Nombre<br />d'absences<br />non justifiées</th>

						<th style='background-color:plum'>Élève</th>
					</tr>
				</thead>
				<tbody>";

	$tab_ele_clas=array();
	$cpt=0;
	while (!feof($fp)) {
		$ligne = trim(preg_replace('/,/', ';', fgets($fp, 4096)));
		if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
			$ligne=substr($ligne,3);
		}

		if($ligne!='') {
			//$tab=explode(";", ensure_utf8($ligne));
			$tab=explode(";", preg_replace('/"/', '', ensure_utf8($ligne)));

			$classe=$tab[$tabindice['classe']];
			$eleve=$tab[$tabindice['eleve']];
			$nb_abs=$tab[$tabindice['nb_abs']];
			$nb_nj=$tab[$tabindice['nb_nj']];

			$eleve_gepi='';

			if(!isset($tab_ele_clas[$classe])) {
				$tab_ele_clas[$classe]=array();
				$sql="SELECT DISTINCT e.login, e.nom, e.prenom, e.no_gep, e.ele_id, e.elenoet, e.sexe, e.naissance FROM eleves e, 
						j_eleves_classes jec, 
						classes c 
					WHERE e.login=jec.login AND 
						c.id=jec.id_classe AND 
						c.classe LIKE '".preg_replace("/[^A-Za-z0-9]/", "%", $classe)."' 
					ORDER BY e.nom, e.prenom;";
				$res_clas=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_clas)>0) {
					$cpt_ele=0;
					while($lig=mysqli_fetch_assoc($res_clas)) {
						$tab_ele_clas[$classe][$cpt_ele]=$lig;
						$tab_ele_clas[$classe][$cpt_ele]['eleve']=$lig['nom'].' '.$lig['prenom'];
						$cpt_ele++;
					}
				}
				/*
				echo "<pre>";
				print_r($tab_ele_clas);
				echo "</pre>";
				*/
			}

			$options_ele=array();
			for($loop=0;$loop<count($tab_ele_clas[$classe]);$loop++) {
				if(casse_mot($tab_ele_clas[$classe][$loop]['eleve'], 'min')==casse_mot($eleve, 'min')) {
					$eleve_gepi="<span style='color:green' title=\"Correspondance trouvée sur le nom et le prénom dans la classe.\">".$tab_ele_clas[$classe][$loop]['eleve']."</span>
					<input type='hidden' name='eleve_nb_abs[".$cpt."]' value='".$tab_ele_clas[$classe][$loop]['login']."|".$nb_abs."|".$nb_nj."' />";

					// IL FAUT AUSSI VERIFIER QU ON NE TROUVE QU UN ELEVE
					$options_ele[]=$loop;


				}
			}

			if($eleve_gepi=='') {
				for($loop=0;$loop<count($tab_ele_clas[$classe]);$loop++) {
					// Faire une recherche en remplaçant les accents par % et en mettant des % en fin de nom/prénom

					if(casse_mot(preg_replace("/[^A-Z-a-z]/", '%', $tab_ele_clas[$classe][$loop]['eleve']), 'min')==casse_mot(preg_replace("/[^A-Z-a-z]/", '%', $eleve), 'min')) {
						$eleve_gepi="<span style='color:blue' title=\"Correspondance trouvée sur le nom et le prénom dans la classe en jouant sur les accents,...\">".$tab_ele_clas[$classe][$loop]['eleve']."</span>
					<input type='hidden' name='eleve_nb_abs[".$cpt."]' value='".$tab_ele_clas[$classe][$loop]['login']."|".$nb_abs."|".$nb_nj."' />";

						// IL FAUT AUSSI VERIFIER QU ON NE TROUVE QU UN ELEVE
						//$options_ele[]=$tab_ele_clas[$classe][$loop]['login'];
						$options_ele[]=$loop;
						
					}
				}
			}

			if($eleve_gepi=='') {
				for($loop=0;$loop<count($tab_ele_clas[$classe]);$loop++) {
					$tmp_tab=explode(' ', $eleve);
					$tmp_tab2=explode('-', $tmp_tab[0]);

					$tmp_tab3=explode(' ', $tab_ele_clas[$classe][$loop]['eleve']);
					$tmp_tab4=explode('-', $tmp_tab3[0]);

					if(casse_mot(preg_replace("/[^A-Z-a-z]/", '%', $tmp_tab4[0]), 'min')==casse_mot(preg_replace("/[^A-Z-a-z]/", '%', $tmp_tab2[0]), 'min')) {
						$eleve_gepi="<span style='color:red' title=\"Correspondance trouvée sur le nom seul en tronquant,... Veuillez vérifier que l'identification est correcte.\">".$tab_ele_clas[$classe][$loop]['eleve']."</span>
						<input type='hidden' name='eleve_nb_abs[".$cpt."]' value='".$tab_ele_clas[$classe][$loop]['login']."|".$nb_abs."|".$nb_nj."' />";

						// IL FAUT AUSSI VERIFIER QU ON NE TROUVE QU UN ELEVE
						//$options_ele[]=$tab_ele_clas[$classe][$loop]['login'];
						$options_ele[]=$loop;
					}
				}
			}

			// Et si on n'a toujours pas trouvé?
			// Chercher dans une autre classe.?
			// Et dans le pire des cas, proposer un champ de sélection parmi les élèves de la classe
			// Ou inviter à une saisie manuelle pour ceux qui n'ont pas été trouvés
			// Faire un récap des non trouvés.

			if(count($options_ele)>1) {
				// Mettre un champ SELECT
				$eleve_gepi="
						<select name='eleve_nb_abs[$cpt]'>
							<option value=''>--- Choisissez ---</option>";
				for($loop=0;$loop<count($options_ele);$loop++) {
					$eleve_gepi.="
								<option value='".$tab_ele_clas[$classe][$options_ele[$loop]]['login']."|".$nb_abs."|".$nb_nj."'>".$tab_ele_clas[$classe][$options_ele[$loop]]['eleve']."</option>";
				}
				$eleve_gepi.="
							</select>";
			}

			$checkbox_ou_pas='';
			if($eleve_gepi!='') {
				$checkbox_ou_pas="<input type='checkbox' name='coche[]' id='coche_$cpt' value='$cpt' onchange=\"checkbox_change(this.id)\" />";
			}

			echo "
					<tr>
						<td>".$classe."</td>
						<td><label for='coche_".$cpt."' id='texte_coche_".$cpt."'>".$eleve."</label></td>
						<td>".$nb_abs."</td>
						<td>".$nb_nj."</td>

						<td>".$eleve_gepi."</td>
						<td>".$checkbox_ou_pas."</td>
					</tr>";
			$cpt++;
		}
	}
	echo "
				</tbody>
			</table>

			<input type='hidden' name='enregistrer_absences' value='y' />


			<p style='margin-top:1em; margin-bottom:1em;'>
			Choix de la période pour laquelle enregistrer ces données&nbsp;:<br />";

	for($i=1;$i<=$maxper;$i++) {
		$sql="SELECT verouiller FROM periodes WHERE num_periode='".$i."' AND verouiller!='O';";
		$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_clas)>0) {
			echo "<input type='radio' name='periode' id='periode_".$i."' value='".$i."' onchange=\"radio_change_style()\" /><label for='periode_".$i."' id='texte_periode_".$i."'>Période $i</label><br />";
		}
		else {
			echo "<img src='../images/disabled.png' class='icone20' />&nbsp;<span style='color:grey' title=\"La période est totalement close.\">Période $i</span><br />";
		}
	}
	echo "
			</p>

			<p>
				<input type='submit' id='input_submit' value='Valider' />
				<input type='button' id='input_button' value='Valider' style='display:none;' onclick=\"check_champ_periode()\" />

			</p>

			<!--
			<p style='text-indent:-4em; margin-left:4em; margin-top:1em;'>
				<em>NOTE&nbsp;:</em>
			</p>
			-->
		</fieldset>
	</form>

	<script type='text/javascript'>

		//================================

		".js_checkbox_change_style('checkbox_change', 'texte_')."

		item=document.getElementsByTagName('input');
		for(i=0;i<item.length;i++) {
			if(item[i].getAttribute('type')=='checkbox') {
				checkbox_change(item[i].getAttribute('id'));
			}
		}

		function import_abs_ent_tout_cocher_decocher(mode) {
			for(i=0;i<$cpt;i++) {
				if(document.getElementById('coche_'+i)) {
					document.getElementById('coche_'+i).checked=mode;
					checkbox_change('coche_'+i);
				}
			}
		}

		//================================

		function radio_change_style() {
			for (var k=0;k<=$maxper;k++) {
				if(document.getElementById('periode_'+k)){
					if(document.getElementById('periode_'+k).checked==true) {
						document.getElementById('texte_periode_'+k).style.fontWeight='bold';
					}
					else {
						document.getElementById('texte_periode_'+k).style.fontWeight='normal';
					}
				}
			}
		}

		radio_change_style();

		//================================

		// Tests sur les choix faits avant de valider le formulaire

		document.getElementById('input_submit').style.display='none';
		document.getElementById('input_button').style.display='';

		function check_champ_periode() {
			periode_choisie=false;
			for (var k=0;k<=$maxper;k++) {
				if(document.getElementById('periode_'+k)){
					if(document.getElementById('periode_'+k).checked==true) {
						periode_choisie=true;
						break;
					}
				}
			}

			if(periode_choisie) {
				au_moins_une_ligne_cochee=false;
				for(i=0;i<$cpt;i++) {
					if(document.getElementById('coche_'+i)) {
						if(document.getElementById('coche_'+i).checked==true) {
							au_moins_une_ligne_cochee=true;
							break;
						}
					}
				}

				if(au_moins_une_ligne_cochee) {
					document.getElementById('form_rapprochements').submit();
				}
				else {
					alert('Vous n\'avez pas coché de ligne.');
				}
			}
			else {
				alert('Vous n\'avez pas choisi la période.');
			}
		}

	</script>";
}
elseif($_POST['mode']==2) {

	//Élève;Établissement;Niveau;Classe;Matière;Période officielle;Date séance;Jour séance;Mois séance;Heure de début;Durée (minutes);Durée (heures);Décompte (½ journée);Enseignants séance;Type de dossier;Justifié;Valable;État;Motif;Identifiant du dossier;Identifiant de la séance

	$sql="CREATE TABLE IF NOT EXISTS temp_absences_ent (id int(11) NOT NULL auto_increment, 
		login varchar(50) NOT NULL DEFAULT '', 
		eleve varchar(255) NOT NULL DEFAULT '', 
		classe varchar(20) NOT NULL DEFAULT '', 
		matiere varchar(255) NOT NULL DEFAULT '', 
		date_mysql DATETIME DEFAULT '1970-01-01 00:00:01', 
		date_seance varchar(20) NOT NULL DEFAULT '', 
		heure_debut varchar(10) NOT NULL DEFAULT '',
		duree_min INT(11) NOT NULL DEFAULT '0',
		duree_h VARCHAR(20) NOT NULL DEFAULT '',
		prof varchar(255) NOT NULL DEFAULT '',
		type_dossier varchar(50) NOT NULL DEFAULT '',
		justifie varchar(50) NOT NULL DEFAULT '',
		valable varchar(50) NOT NULL DEFAULT '',
		motif varchar(50) NOT NULL DEFAULT '',
		PRIMARY KEY (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	//echo "$sql<br />";
	$create_table=mysqli_query($mysqli, $sql);

	if(!$create_table) {
		echo "<p style='color:red'>Erreur lors de la création de la table temporaire.</p>";
		echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="TRUNCATE TABLE temp_absences_ent;";
	$menage=mysqli_query($mysqli, $sql);


	$fp=fopen($dest_file,"r");


	//Élève;Établissement;Niveau;Classe;Matière;Période officielle;Date séance;Jour séance;Mois séance;Heure de début;Durée (minutes);Durée (heures);Décompte (½ journée);Enseignants séance;Type de dossier;Justifié;Valable;État;Motif;Identifiant du dossier;Identifiant de la séance

	// Lire la ligne d'entête pour repérer les indices des colonnes recherchées
	$tabchamps = array("Élève", "Classe", "Matière", "Date séance", "Heure de début", "Durée (minutes)", "Durée (heures)", "Enseignants séance", "Type de dossier", "Justifié", "Valable", "Motif");
	$tabchamps2 = array("eleve", "classe", "matiere", "date_seance", "heure_debut", "duree_min", "duree_h", "prof", "type_dossier", "justifie", "valable", "motif");

	// Lecture de la ligne 1 et la mettre dans $temp
	//$ligne_entete=trim(fgets($fp,4096));
	// Au cas où on aurait des champs entourés de guillemets
	$ligne_entete=trim(preg_replace('/"/', '', fgets($fp,4096)));
	//echo "$ligne_entete<br />";
	$en_tete=explode(";", $ligne_entete);

	$tabindice=array();

	// On range dans tabindice les indices des champs retenus
	for ($k = 0; $k < count($tabchamps); $k++) {
		for ($i = 0; $i < count($en_tete); $i++) {
			if (casse_mot(remplace_accents($en_tete[$i]),'min') == casse_mot(remplace_accents($tabchamps[$k]), 'min')) {
				$tabindice[$tabchamps2[$k]] = $i;
				echo "<span style='color:green'>Champ  \$tabchamps2[$k]=".$tabchamps[$k]." (".$tabchamps2[$k].") trouvé à l'indice $i (\$en_tete[$i]=".$en_tete[$i].")</span><br />";
			}
		}
	}

	if((!isset($tabindice['classe']))||
	(!isset($tabindice['eleve']))||
	(!isset($tabindice['date_seance']))||
	(!isset($tabindice['heure_debut']))||
	(!isset($tabindice['duree_min']))||
	(!isset($tabindice['duree_h']))||
	(!isset($tabindice['prof']))||
	(!isset($tabindice['type_dossier']))||
	(!isset($tabindice['justifie']))||
	(!isset($tabindice['valable']))) {
		echo "<p style='color:red'>La ligne d'entête ne comporte pas un des champs indispensables (<em>\"Élève\", \"Classe\", \"Matière\", \"Date séance\", \"Heure de début\", \"Durée (minutes)\", \"Durée (heures)\", \"Enseignants séance\", \"Type de dossier\", \"Justifié\", \"Valable\", \"Motif\"</em>).</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$cpt=0;
	$nb_reg=0;
	while (!feof($fp)) {
		$ligne = trim(fgets($fp, 4096));
		if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
			$ligne=substr($ligne,3);
		}

		if($ligne!='') {
			//$tab=explode(";", ensure_utf8($ligne));
			$tab=explode(";", ensure_utf8($ligne));

			$classe=$tab[$tabindice['classe']];
			$eleve=$tab[$tabindice['eleve']];
			$matiere=$tab[$tabindice['matiere']];

			$date_seance=$tab[$tabindice['date_seance']];
			$heure_debut=$tab[$tabindice['heure_debut']];
			$duree_min=$tab[$tabindice['duree_min']];
			$duree_h=$tab[$tabindice['duree_h']];
			$prof=$tab[$tabindice['prof']];
			$type_dossier=$tab[$tabindice['type_dossier']];
			$justifie=$tab[$tabindice['justifie']];
			$valable=$tab[$tabindice['valable']];

			$tmp_tab=explode("/", $date_seance);
			$date_mysql=$date_seance[2]."-".$date_seance[1]."-".$date_seance[0]." ".$heure_debut.":00";

			$sql="INSERT INTO temp_absences_ent SET login='', 
										eleve='".mysqli_real_escape_string($mysqli, $eleve)."', 
										classe='".mysqli_real_escape_string($mysqli, $classe)."', 
										matiere='".mysqli_real_escape_string($mysqli, $matiere)."', 
										date_mysql='".mysqli_real_escape_string($mysqli, $date_mysql)."',
										date_seance='".mysqli_real_escape_string($mysqli, $date_seance)."',
										heure_debut='".mysqli_real_escape_string($mysqli, $heure_debut)."',
										duree_min='".mysqli_real_escape_string($mysqli, $duree_min)."',
										duree_h='".mysqli_real_escape_string($mysqli, $duree_h)."',
										prof='".mysqli_real_escape_string($mysqli, $prof)."',
										type_dossier='".mysqli_real_escape_string($mysqli, $type_dossier)."',
										justifie='".mysqli_real_escape_string($mysqli, $justifie)."',
										valable='".mysqli_real_escape_string($mysqli, $valable)."';";
			$insert=mysqli_query($mysqli, $sql);
			if(!$insert) {
				echo "<p style='color:red'>Erreur lors de l'enregistrement $sql.</p>";
			}
			else {
				$nb_reg++;
			}

		}
	}

	echo "<p>".$nb_reg." enregistrement(s) effectué(s).</p>";


	$sql="SELECT DISTINCT eleve, classe FROM temp_absences_ent ORDER BY classe, eleve;";
	// Faire les rapprochements et renseigner une colonne login.


	require("../lib/footer.inc.php");
	die();


	//++++++++++++++++++++++++++++++++++++++++++++
	//++++++++++++++++++++++++++++++++++++++++++++
	//++++++++++++++++++++++++++++++++++++++++++++
	//++++++++++++++++++++++++++++++++++++++++++++




	$sql="SELECT MAX(num_periode) AS maxper FROM periodes;";
	$res_maxper=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_maxper)==0) {
		echo "<p style='color:red'>Le nombre max de périodes pour une classe n'a pas été trouvé.<br />Y a-t-il des classes avec périodes associées.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	$lig=mysqli_fetch_object($res_maxper);
	$maxper=$lig->maxper;

	// Faut-il faire une première passe pour identifier les classes?

	echo "
	<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_rapprochements' method='post'>
		<fieldset class='fieldset_opacite50'>
			".add_token_field()."

			<p>Lecture du fichier et tentative de rapprochement&nbsp;: </p>
			<table class='boireaus boireaus_alt'>
				<thead>
					<tr>
						<th colspan='4' style='background-color:azure'>Fichier CSV</th>
						<th style='background-color:plum'>Gepi</th>
						<th rowspan='2'>
							Coche
							<br />
							<a href=\"javascript:import_abs_ent_tout_cocher_decocher(true)\">
								<img src='../images/enabled.png' class='icone20' />
							</a>
							/
							<a href=\"javascript:import_abs_ent_tout_cocher_decocher(false)\">
								<img src='../images/disabled.png' class='icone20' />
							</a>
						</th>
					</tr>
					<tr>
						<th style='background-color:azure'>Classe</th>
						<th style='background-color:azure'>Élève</th>
						<th style='background-color:azure'>Nombre<br />d'absences</th>
						<th style='background-color:azure'>Nombre<br />d'absences<br />non justifiées</th>

						<th style='background-color:plum'>Élève</th>
					</tr>
				</thead>
				<tbody>";

	$tab_ele_clas=array();




}
else {
	echo "<p style='color:red'>Mode inconnu</p>";
}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
