<?php
/*
*
* Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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

$sql="SELECT 1=1 FROM droits WHERE id='/saisie/socle_import.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/saisie/socle_import.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Socle: Import',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// A FAIRE : Modifier pour permettre tout de même une consultation sans droits de saisie.

if(!getSettingAOui("SocleSaisieComposantes")) {
	header("Location: ../accueil.php?msg=Accès non autorisé");
	die();
}

if(!getSettingAOui("SocleImportComposantes")) {
	header("Location: ../accueil.php?msg=Import non autorisé");
	die();
}

if(($_SESSION["statut"]!="administrateur")&&(!getSettingAOui("SocleImportComposantes_".$_SESSION["statut"]))) {
	header("Location: ../accueil.php?msg=Accès non autorisé");
	die();
}

$msg="";
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
$cycle=isset($_POST['cycle']) ? $_POST['cycle'] : NULL;
$mode=isset($_POST['mode']) ? $_POST['mode'] : NULL;
$periode=isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);

//$SocleOuvertureSaisieComposantes=getSettingValue("SocleOuvertureSaisieComposantes");

// Etat d'ouverture ou non des saisies
$max_per=0;
$sql="SELECT MAX(num_periode) AS max_per FROM periodes;";
$res_max=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res_max)==0) {
	echo "<p style='color:red'><strong>ANOMALIE&nbsp;:</strong> Aucune classe avec périodes ne semble définie.</p>";
	require("../lib/footer.inc.php");
	die();
}
$lig_max=mysqli_fetch_object($res_max);
$max_per=$lig_max->max_per;

$SocleOuvertureSaisieComposantes=array();
for($i=1;$i<$max_per+1;$i++) {
	$SocleOuvertureSaisieComposantes[$i]=getSettingAOui("SocleOuvertureSaisieComposantesPeriode".$i);
}

$tab_domaine_socle=array();
$tab_domaine_socle["CPD_FRA"]="Comprendre, s'exprimer en utilisant la langue française à l'oral et à l'écrit";
$tab_domaine_socle["CPD_ETR"]="Comprendre, s'exprimer en utilisant une langue étrangère et, le cas échéant, une langue régionale";
$tab_domaine_socle["CPD_SCI"]="Comprendre, s'exprimer en utilisant les langages mathématiques, scientifiques et informatiques";
$tab_domaine_socle["CPD_ART"]="Comprendre, s'exprimer en utilisant les langages des arts et du corps";
$tab_domaine_socle["MET_APP"]="Les méthodes et outils pour apprendre";
$tab_domaine_socle["FRM_CIT"]="La formation de la personne et du citoyen";
$tab_domaine_socle["SYS_NAT"]="Les systèmes naturels et les systèmes techniques";
$tab_domaine_socle["REP_MND"]="Les représentations du monde et l'activité humaine";

$tab_niveau_maitrise_socle=array();
$tab_niveau_maitrise_socle[1]["texte"]="MI";
$tab_niveau_maitrise_socle[1]["texte_couleur"]="<span style='color:red' title='Maitrise insuffisante'>MI</span>";
$tab_niveau_maitrise_socle[2]["texte"]="MF";
$tab_niveau_maitrise_socle[2]["texte_couleur"]="<span style='color:orange' title='Maitrise fragile'>MF</span>";
$tab_niveau_maitrise_socle[3]["texte"]="MS";
$tab_niveau_maitrise_socle[3]["texte_couleur"]="<span style='color:green' title='Maitrise satisfaisante'>MS</span>";
$tab_niveau_maitrise_socle[4]["texte"]="TBM";
$tab_niveau_maitrise_socle[4]["texte_couleur"]="<span style='color:blue' title='Très bonne maitrise'>TBM</span>";

$themessage  = 'Des valeurs ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
//**************** EN-TETE *****************
$titre_page = "Socle: Import";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

//$SocleOuvertureSaisieComposantes=getSettingAOui("SocleOuvertureSaisieComposantes");

echo "<p class='bold'><a href=\"../accueil.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
if((acces("/saisie/saisie_socle.php", $_SESSION["statut"]))&&(getSettingAOui("SocleSaisieComposantes_".$_SESSION["statut"]))) {
	echo " | <a href=\"saisie_socle.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisie des bilans de composantes du socle</a>";
}
echo " | <a href=\"socle_verif.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Vérification du remplissage des bilans de composantes du socle</a>";

if((acces("/saisie/socle_verrouillage.php", $_SESSION["statut"]))&&(
	(getSettingAOui("SocleOuvertureSaisieComposantes_".$_SESSION["statut"]))||
	((getSettingAOui("SocleOuvertureSaisieComposantes_PP"))&&(is_pp($_SESSION["login"])))
)) {
	echo " | <a href=\"socle_verrouillage.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Ouverture/verrouillage des saisies</a>";
}

if((!isset($id_classe))||(!isset($cycle))||(!isset($mode))||(!isset($periode))) {
	echo "</p>";
	// Choix du cycle, des classes et du fichier

	/*
	if(!$SocleOuvertureSaisieComposantes) {
		echo "<p style='color:red'>La saisie/modification des bilans de composantes du socle est fermée.<br />Seule la consultation des saisies est possible.</p>";
	}
	*/

	$sql=retourne_sql_mes_classes();
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red;'>Aucune classe trouvée pour vous.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	echo js_checkbox_change_style('checkbox_change', 'texte_', 'y');

	echo "
<form enctype='multipart/form-data' id='form_envoi' action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p>Sélectionnez le fichier export JSON de SACoche&nbsp;:<br />
		<input type=\"file\" size=\"80\" name=\"sacoche_json_file\" id='sacoche_json_file' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); ' /></p>
		<p style='text-indent:-3em; margin-left:3em; margin-top:1em;'>Séléctionnez le ou les cycles&nbsp;:<br />
		<input type='checkbox' name='cycle[]' id='cycle_3' value='3' onchange=\"checkbox_change(this.id)\" /><label for='cycle_3' id='texte_cycle_3'>Cycle 3</label><br />
		<input type='checkbox' name='cycle[]' id='cycle_4' value='4' onchange=\"checkbox_change(this.id)\" /><label for='cycle_4' id='texte_cycle_4'>Cycle 4</label>
		</p>

		<p style='text-indent:-3em; margin-left:3em; margin-top:1em;'>Dans le cas où une saisie existe déjà pour l'élève/cycle choisi&nbsp;:<br />
		<input type='radio' name='mode' id='mode_remplacer' value='remplacer' onchange=\"checkbox_change('mode_remplacer');checkbox_change('mode_ameliorer');\" checked /><label for='mode_remplacer' id='texte_mode_remplacer' style='font-weight:bold;'>remplacer les valeurs précédemment enregistrées</label><br />
		<input type='radio' name='mode' id='mode_ameliorer' value='ameliorer' onchange=\"checkbox_change('mode_remplacer');checkbox_change('mode_ameliorer');\" /><label for='mode_ameliorer' id='texte_mode_ameliorer'>ne remplacer que si le niveau de maitrise est amélioré</label>.
		</p>";

	$tab_mes_classes=array();
	$tab_mes_classes_txt=array();
	$tab_mes_classes_nom_champ=array();
	$tab_mes_classes_id_champ=array();
	$tab_mes_classes_valeur_champ=array();
	$sql=retourne_sql_mes_classes();
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_mes_classes[]=$lig->id_classe;
			$tab_mes_classes_nom_champ[]="id_classe[]";
			$tab_mes_classes_id_champ[]="id_classe_".$lig->id_classe;
			$tab_mes_classes_valeur_champ[]=$lig->id_classe;
			$tab_mes_classes_txt[]=preg_replace("/ /", "&nbsp;", $lig->classe);
		}
	}

	echo "
		<p style='margin-top:1em; margin-left:3em; text-indent:-3em;'>Choisissez la ou les classes&nbsp;:</p>";

	echo tab_liste_checkbox($tab_mes_classes_txt, $tab_mes_classes_nom_champ, $tab_mes_classes_id_champ, $tab_mes_classes_valeur_champ, "checkbox_change2");
	//echo tab_liste_checkbox($tab_mes_classes_txt, $tab_mes_classes_nom_champ, $tab_mes_classes_id_champ, $tab_mes_classes_valeur_champ);

	echo "
		<p style='margin-left:3em;text-indent:-3em;margin-top:1em;'>Période&nbsp;:<br />";
	for($i=1;$i<$max_per+1;$i++) {
		$checked="";
		$style="";
		if($i==1) {
			$checked=" checked";
			$style=" style='font-weight:bold'";
		}
		echo "
			<input type='radio' name='periode' id='periode_$i' value='$i' onchange=\"change_style_radio()\"".$checked." /><label for='periode_$i' id='texte_periode_$i'".$style.">Période $i</label><br />";
	}
	echo "
		</p>
		<p><input type='submit' value='Valider' /></p>
	</fieldset>
</form>

".js_change_style_radio("change_style_radio", "y")."

<p style='margin-top:1em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li><p style='margin-bottom:1em;'>L'export attendu est un export de <a href='https://sacoche.sesamath.net' target='_blank'>SACoche</a>.<br />
	Voir <a href='https://sacoche.sesamath.net/index.php?page=documentation__referentiels_socle__socle_export_import#toggle_export_gepi' target='_blank'>https://sacoche.sesamath.net/index.php?page=documentation__referentiels_socle__socle_export_import#toggle_export_gepi</a></p></li>
	<li>
		<p>La structure du fichier JSON après extraction par json_decode() est la suivante&nbsp;:<br />
		<pre>
Array
(
    [cycle] => 3
    [eleve] => Array
        (
            [2345] => Array
                (
                    [id_be] => 1234567
                    [nom] => DUGENOU
                    [prenom] => Simone
                    [position] => Array
                        (
                            [CPD_FRA] => 3
                            [CPD_SCI] => 4
                            [CPD_ART] => 1
                            [MET_APP] => 3
                            [FRM_CIT] => 4
                            [SYS_NAT] => 3
                            [REP_MND] => 4
                        )

                )

            [8139] => Array
                (
                    [id_be] => 1762467
                    [nom] => DUPRE
                    [prenom] => Remi
                    [position] => Array
                        (
                            [CPD_FRA] => 4
                            [CPD_SCI] => 4
                            [CPD_ART] => 4
                            [MET_APP] => 4
                            [FRM_CIT] => 2
                            [SYS_NAT] => 3
                            [REP_MND] => 3
                        )

                )

        )

)
		</pre>
		<p><strong>id_be</strong> désigne l'identifiant ELEVE_ID de Sconet/Siècle.</p>
	</li>
</ul>";

	require("../lib/footer.inc.php");
	die();
}

// Saisies (sous réserve que la saisie soit ouverte, sinon affichage)

echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choisir un autre fichier et d'autres classes</a></p>";

/*
if(!$SocleOuvertureSaisieComposantes) {
	echo "\n<p style='color:red'>La saisie/modification des bilans de composantes du socle est fermée.<br />Seule la consultation des saisies est possible.</p>";
}
*/

if(!isset($_POST['confirmer_import'])) {
	// On va uploader le fichier JSON dans le tempdir de l'utilisateur
	$tempdir=get_user_temp_directory();
	if(!$tempdir) {
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$post_max_size=ini_get('post_max_size');
	$upload_max_filesize=ini_get('upload_max_filesize');
	$max_execution_time=ini_get('max_execution_time');
	$memory_limit=ini_get('memory_limit');

	// Affichage des informations
	echo "<h2>Upload et analyse du fichier</h2>\n";

	check_token(false);

	$json_file = isset($_FILES["sacoche_json_file"]) ? $_FILES["sacoche_json_file"] : NULL;

	// DEBUG:
	/*
	echo "<pre>";
	print_r($json_file);
	echo "</pre>";
	*/

	if(!is_uploaded_file($json_file['tmp_name'])) {
		echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

		echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
		echo "post_max_size=$post_max_size<br />\n";
		echo "upload_max_filesize=$upload_max_filesize<br />\n";
		echo "</p>\n";

		echo "<p>Retour à <a href='".$_SERVER['PHP_SELF']."'>l'accueil de la page</a></p>";

		require("../lib/footer.inc.php");
		die();
	}

	if(!file_exists($json_file['tmp_name'])) {
		echo "<p style='color:red;'>Le fichier aurait été uploadé... mais ne serait pas présent/conservé.</p>\n";

		echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
		echo "post_max_size=$post_max_size<br />\n";
		echo "upload_max_filesize=$upload_max_filesize<br />\n";
		echo "et le volume de ".$json_file['name']." serait<br />\n";
		echo "\$json_file['size']=".volume_human($json_file['size'])."<br />\n";
		echo "</p>\n";

		echo "<p>Retour à <a href='".$_SERVER['PHP_SELF']."'>l'accueil de la page</a></p>";

		require("../lib/footer.inc.php");
		die();
	}

	echo "<p>Le fichier a été uploadé.</p>\n";

	$source_file=$json_file['tmp_name'];
	$dest_file="../temp/".$tempdir."/sacoche_bilan_composantes_socle.json";
	if(file_exists($dest_file)) {
		echo "<p><b>NETTOYAGE&nbsp;:</b> Suppression du fichier sacoche_bilan_composantes_socle.json précédent&nbsp;: ";
		if(unlink($dest_file)) {echo "<span style='color:green'>SUCCES</span>";}
		else {echo "<span style='color:red'>ECHEC</span>";}
		echo "</p>\n";
	}
	$res_copy=copy("$source_file" , "$dest_file");

	if(!$res_copy){
		echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier <b>temp/$tempdir</b></p>\n";

		echo "<p>Retour à <a href='".$_SERVER['PHP_SELF']."'>l'accueil de la page</a></p>";

		require("../lib/footer.inc.php");
		die();
	}

	echo "<p style='margin-bottom:1em;'>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

	echo "
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='confirmer_import' value='y' />
		<input type='hidden' name='periode' value='$periode' />
		<input type='hidden' name='mode' value='$mode' />";

	$tab_nom_classe=array();
	$tab_ele_classe=array();
	echo "
		<p style='margin-top:1em'>Les données du fichier ne vont être retenues que pour les élèves des classes suivantes&nbsp;: ";
	for($loop=0;$loop<count($id_classe);$loop++) {
		if($loop>0) {
			echo ", ";
		}
		$tab_nom_classe[$id_classe[$loop]]=get_nom_classe($id_classe[$loop]);
		echo "<strong>".$tab_nom_classe[$id_classe[$loop]]."</strong>";
		echo "<input type='hidden' name='id_classe[]' value='".$id_classe[$loop]."' />";

	$sql="SELECT DISTINCT e.*, jec.id_classe, c.classe FROM eleves e, j_eleves_classes jec, classes c WHERE jec.login=e.login AND jec.id_classe='".$id_classe[$loop]."' AND jec.periode='".$periode."' AND c.id=jec.id_classe ORDER BY c.classe, e.nom, e.prenom;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {

			while($lig=mysqli_fetch_assoc($res)) {

				$mef_code_ele=$lig["mef_code"];
				if(!isset($tab_cycle[$mef_code_ele])) {
					$tmp_tab_cycle_niveau=calcule_cycle_et_niveau($mef_code_ele, "", "");
					$cycle_courant=$tmp_tab_cycle_niveau["mef_cycle"];
					$niveau=$tmp_tab_cycle_niveau["mef_niveau"];
					$tab_cycle[$mef_code_ele]=$cycle;
				}

				if((!isset($tab_cycle[$mef_code_ele]))||($tab_cycle[$mef_code_ele]=="")) {
					echo "
		<p style='color:red'>Le cycle courant pour ".$lig["nom"]." ".$lig["prenom"]." n'a pas pu être identitfié&nbsp;???</p>";
				}
				else {
					$tab_ele_classe["indice_classe"][$id_classe[$loop]][]=$lig;
					$tab_ele_classe["indice_ele_id"][$lig["ele_id"]]=$lig;
				}
			}
		}
	}
	echo "</p>";

	echo "
		<p>Le ou les cycles suivants vont être pris en compte&nbsp;: ";
	for($loop=0;$loop<count($cycle);$loop++) {
		if($loop>0) {
			echo ", ";
		}
		echo "<strong>".$cycle[$loop]."</strong>";
		echo "<input type='hidden' name='cycle[]' value='".$cycle[$loop]."' />";
	}
	echo "</p>";


	$file_data = file_get_contents($dest_file);
	$array_data = json_decode($file_data,TRUE);

	// affichage pour vérification 
	/*
	echo'<pre>';
	print_r($array_data);
	echo'</pre>';
	*/

	echo "<p style='margin-top:1em; margin-bottom:1em; color:red;'>À ce stade, aucun enregistrement n'est effectué.<br />Pour confirmer l'enregistrement, validez en bas de page.</p>\n";

	$cpt_valeur=0;
	for($loop=0;$loop<count($cycle);$loop++) {
		echo "
		<p class='bold' style='margin-top:1em;'>Parcours des données du cycle ".$cycle[$loop]."</p>";

		if((!isset($array_data["cycle"]))||($array_data["cycle"]!=$cycle[$loop])) {
			echo "
		<p style='color:red'>L'export fourni ne concerne pas le cycle ".$cycle[$loop]."</p>";
		}
		else {
			echo "
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th rowspan='2'>Élève Gepi</th>
					<th rowspan='2'>Élève SACoche</th>
					<th rowspan='2'>Classe</th>
					<th colspan='".count($tab_domaine_socle)."'>Domaines</th>
				</tr>
				<tr>";
			foreach($tab_domaine_socle as $code => $intitule) {
				echo "
					<th title=\"".$intitule."\">".$code."</th>";
			}
			echo "
				</tr>
			</thead>
			<tbody>";
			foreach($array_data["eleve"] as $current_id_sacoche => $current_eleve) {
				if(array_key_exists($current_eleve["id_be"], $tab_ele_classe["indice_ele_id"])) {
					echo "
				<tr onmouseover=\"this.style.backgroundColor='white'\" onmouseout=\"this.style.backgroundColor=''\">
					<td>".$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["nom"]." ".$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["prenom"]."</td>
					<td>".$current_eleve["nom"]." ".$current_eleve["prenom"]."</td>
					<td>".$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["classe"]."</td>";
			foreach($tab_domaine_socle as $code => $intitule) {
				$valeur="";
				if(isset($current_eleve["position"][$code])) {
					$valeur=$current_eleve["position"][$code];
					if(isset($tab_niveau_maitrise_socle[$valeur]["texte_couleur"])) {
						$valeur=$tab_niveau_maitrise_socle[$valeur]["texte_couleur"];
						$cpt_valeur++;
					}
				}
				echo "
					<td title=\"".$intitule."\">".$valeur."</td>";
			}
			echo "
				</tr>";
				}
			}
			echo "
			</tbody>
		</table>";
		}
	}

	if($cpt_valeur>0) {
		echo "<p><input type='submit' value='Importer les valeurs' /></p>";
	}
	echo "
	</fieldset>
</form>";

}
else {
	check_token(false);

	// On enregistre les données.

	$tempdir=get_user_temp_directory();
	if(!$tempdir) {
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$dest_file="../temp/".$tempdir."/sacoche_bilan_composantes_socle.json";
	if(!file_exists($dest_file)) {
		echo "<p style='color:red'>Le fichier JSON n'a pas été trouvé.</p>";

		echo "<p>Retour à <a href='".$_SERVER['PHP_SELF']."'>l'accueil de la page</a></p>";

		require("../lib/footer.inc.php");
		die();
	}

	// Récupérer les saisies antérieures
	$tab_saisies=array();

	$tab_nom_classe=array();
	$tab_ele_classe=array();
	echo "
		<p style='margin-top:1em'>Les données du fichier ne vont être retenues/enregistrées que pour les élèves des classes suivantes&nbsp;: ";
	for($loop=0;$loop<count($id_classe);$loop++) {
		if($loop>0) {
			echo ", ";
		}
		$tab_nom_classe[$id_classe[$loop]]=get_nom_classe($id_classe[$loop]);
		echo "<strong>".$tab_nom_classe[$id_classe[$loop]]."</strong>";

		$sql="SELECT DISTINCT e.*, jec.id_classe, c.classe FROM eleves e, j_eleves_classes jec, classes c WHERE jec.login=e.login AND jec.id_classe='".$id_classe[$loop]."' AND periode='".$periode."' AND c.id=jec.id_classe ORDER BY c.classe, e.nom, e.prenom;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$sql="SELECT DISTINCT sec.* FROM socle_eleves_composantes sec, eleves e, j_eleves_classes jec WHERE e.login=jec.login AND sec.ine=e.no_gep AND jec.id_classe='".$id_classe[$loop]."' AND sec.periode=jec.periode AND jec.periode='".$periode."';";
			$res_saisies=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_saisies)>0) {
				while($lig_saisies=mysqli_fetch_object($res_saisies)) {
					$tab_saisies[$lig_saisies->ine][$lig_saisies->cycle][$lig_saisies->code_composante]["niveau_maitrise"]=$lig_saisies->niveau_maitrise;
					/*
					if(!isset($tab_civ_nom_prenom[$lig_saisies->login_saisie])) {
						$tab_civ_nom_prenom[$lig_saisies->login_saisie]=civ_nom_prenom($lig_saisies->login_saisie);
					}
					$tab_saisies[$lig_saisies->ine][$lig_saisies->cycle][$lig_saisies->code_composante]["title"]="Saisi par ".$tab_civ_nom_prenom[$lig_saisies->login_saisie]." le ".formate_date($lig_saisies->date_saisie,"y2");
					*/
				}
			}

			while($lig=mysqli_fetch_assoc($res)) {

				$mef_code_ele=$lig["mef_code"];
				if(!isset($tab_cycle[$mef_code_ele])) {
					$tmp_tab_cycle_niveau=calcule_cycle_et_niveau($mef_code_ele, "", "");
					$cycle_courant=$tmp_tab_cycle_niveau["mef_cycle"];
					$niveau=$tmp_tab_cycle_niveau["mef_niveau"];
					$tab_cycle[$mef_code_ele]=$cycle;
				}

				if((!isset($tab_cycle[$mef_code_ele]))||($tab_cycle[$mef_code_ele]=="")) {
					echo "
		<p style='color:red'>Le cycle courant pour ".$lig["nom"]." ".$lig["prenom"]." n'a pas pu être identitfié&nbsp;???</p>";
				}
				else {
					$tab_ele_classe["indice_classe"][$id_classe[$loop]][]=$lig;
					$tab_ele_classe["indice_ele_id"][$lig["ele_id"]]=$lig;
				}
			}
		}
	}
	echo "</p>";

	echo "
		<p>Le ou les cycles suivants vont être pris en compte&nbsp;: ";
	for($loop=0;$loop<count($cycle);$loop++) {
		if($loop>0) {
			echo ", ";
		}
		echo "<strong>".$cycle[$loop]."</strong>";
	}
	echo "</p>";

	$file_data = file_get_contents($dest_file);
	$array_data = json_decode($file_data,TRUE);

	// affichage pour vérification 
	/*
	echo'<pre>';
	print_r($array_data);
	echo'</pre>';
	*/

	echo "<div id='compte_rendu_import' style='margin-top:1em; margin-bottom:1em; padding:0.5em; text-align:center;' class='fieldset_opacite50'></div>";

	$nb_identique=0;
	$nb_reg=0;
	$nb_err=0;
	$cpt_valeur=0;
	for($loop=0;$loop<count($cycle);$loop++) {
		echo "
		<p class='bold' style='margin-top:1em;'>Parcours des données du cycle ".$cycle[$loop]."</p>";

		if((!isset($array_data["cycle"]))||($array_data["cycle"]!=$cycle[$loop])) {
			echo "
		<p style='color:red'>L'export fourni ne concerne pas le cycle ".$cycle[$loop]."</p>";
		}
		else {
			echo "
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th rowspan='2'>Élève Gepi</th>
					<th rowspan='2'>Élève SACoche</th>
					<th rowspan='2'>Classe</th>
					<th colspan='".count($tab_domaine_socle)."'>Domaines</th>
					<th rowspan='2'>Enregistrement</th>
				</tr>
				<tr>";
			foreach($tab_domaine_socle as $code => $intitule) {
				echo "
					<th title=\"".$intitule."\">".$code."</th>";
			}
			echo "
				</tr>
			</thead>
			<tbody>";
			foreach($array_data["eleve"] as $current_id_sacoche => $current_eleve) {
				if(array_key_exists($current_eleve["id_be"], $tab_ele_classe["indice_ele_id"])) {
					echo "
				<tr onmouseover=\"this.style.backgroundColor='white'\" onmouseout=\"this.style.backgroundColor=''\">
					<td>".$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["nom"]." ".$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["prenom"]."</td>
					<td>".$current_eleve["nom"]." ".$current_eleve["prenom"]."</td>
					<td>".$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["classe"]."</td>";
					$temoin_reg=0;
					$temoin_erreur=0;
					foreach($tab_domaine_socle as $code => $intitule) {
						$valeur="";
						if(isset($current_eleve["position"][$code])) {
							$valeur=$current_eleve["position"][$code];

							if(($mode=="remplacer")||(!isset($tab_saisies[$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["no_gep"]][$cycle[$loop]][$code]["niveau_maitrise"]))) {
								$sql="DELETE FROM socle_eleves_composantes WHERE ine='".$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["no_gep"]."' AND cycle='".$cycle[$loop]."' AND code_composante='".$code."' AND periode='".$periode."';";
								$del=mysqli_query($GLOBALS["mysqli"], $sql);

								$sql="INSERT INTO socle_eleves_composantes SET ine='".$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["no_gep"]."', cycle='".$cycle[$loop]."', code_composante='".$code."', niveau_maitrise='".$valeur."', login_saisie='".$_SESSION['login']."', date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."', periode='".$periode."';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$insert) {
									$temoin_erreur++;
								}
								else {
									$temoin_reg++;
								}


								if(isset($tab_niveau_maitrise_socle[$valeur]["texte_couleur"])) {
									$valeur=$tab_niveau_maitrise_socle[$valeur]["texte_couleur"];

									//$cpt_valeur++;
								}
							}
							elseif(isset($tab_saisies[$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["no_gep"]][$cycle[$loop]][$code]["niveau_maitrise"])) {
								if($valeur>$tab_saisies[$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["no_gep"]][$cycle[$loop]][$code]["niveau_maitrise"]) {
									$sql="DELETE FROM socle_eleves_composantes WHERE ine='".$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["no_gep"]."' AND cycle='".$cycle[$loop]."' AND code_composante='".$code."' AND periode='".$periode."';";
									$del=mysqli_query($GLOBALS["mysqli"], $sql);

									$sql="INSERT INTO socle_eleves_composantes SET ine='".$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["no_gep"]."', cycle='".$cycle[$loop]."', code_composante='".$code."', niveau_maitrise='".$valeur."', login_saisie='".$_SESSION['login']."', date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."', periode='".$periode."';";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$insert) {
										$temoin_erreur++;
									}
									else {
										$temoin_reg++;
									}

									if(isset($tab_niveau_maitrise_socle[$valeur]["texte_couleur"])) {
										$valeur=$tab_niveau_maitrise_socle[$valeur]["texte_couleur"];

										//$cpt_valeur++;
									}

								}
								elseif($valeur<$tab_saisies[$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["no_gep"]][$cycle[$loop]][$code]["niveau_maitrise"]) {

									if(isset($tab_niveau_maitrise_socle[$valeur]["texte_couleur"])) {
										$valeur=$tab_niveau_maitrise_socle[$valeur]["texte_couleur"];
										$valeur="<span title=\"La valeur enregistrée est plus élevée.\nOn la conserve.\">".$valeur."&nbsp;&lt;&nbsp;".$tab_niveau_maitrise_socle[$tab_saisies[$tab_ele_classe["indice_ele_id"][$current_eleve["id_be"]]["no_gep"]][$cycle[$loop]][$code]["niveau_maitrise"]]["texte_couleur"]."</span>";
									}
								}
								else {
									if(isset($tab_niveau_maitrise_socle[$valeur]["texte_couleur"])) {
										$valeur=$tab_niveau_maitrise_socle[$valeur]["texte_couleur"];
										$valeur="<span title=\"La valeur enregistrée est identique.\">".$valeur."</span>";
										$nb_identique++;
									}
								}
							}
						}
						echo "
					<td title=\"".$intitule."\">".$valeur."</td>";
					}
					echo "
					<td>";
					if($temoin_erreur>0) {
						echo "<img src='../images/icons/flag2.gif' class='icone20' alt='Erreur' title=\"$temoin_erreur erreur(s)\" />";
					}
					elseif($temoin_reg>0) {
						echo "<img src='../images/icons/flag_green.png' class='icone20' alt='OK' title=\"$temoin_reg enregistrement(s)\" />";
					}
					echo "</td>";
					echo "
				</tr>";
					$nb_reg+=$temoin_reg;
					$nb_err+=$temoin_erreur;
				}
			}
			echo "
			</tbody>
		</table>";
		}
	}

	$chaine_compte_rendu="<p>";
	if($nb_identique>0) {
		$chaine_compte_rendu.=$nb_identique." valeur(s) identique(s) non ré-enregistrée(s).<br />";
	}
	$chaine_compte_rendu.=$nb_reg." enregistrement(s) effectué(s).<br />";
	$chaine_compte_rendu.=$nb_err." erreur(s) rencontrée(s).</p>";

	echo $chaine_compte_rendu;
	echo "
<script type='text/javascript'>
	document.getElementById('compte_rendu_import').innerHTML='$chaine_compte_rendu';
	document.getElementById('compte_rendu_import').style.display='';
</script>";
}

require("../lib/footer.inc.php");
?>
