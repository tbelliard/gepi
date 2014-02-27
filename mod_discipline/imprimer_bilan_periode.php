<?php
/*
 * Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

//$variables_non_protegees = 'yes';

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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

require('sanctions_func_lib.php');

$msg="";

// recupération des parametres
$periode=isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$eleve=isset($_POST['eleve']) ? $_POST['eleve'] : (isset($_GET['eleve']) ? $_GET['eleve'] : NULL);

if (isset($eleve)) {
	$tab_eleves_OOo=array();
	$nb_eleve=0;

	$tab_type_avertissement_fin_periode=get_tab_type_avertissement();

	for($loop=0;$loop<count($eleve);$loop++) {
		$tab=explode("|", $eleve[$loop]);
		if(isset($tab[2])) {
			$tab_eleves_OOo[$nb_eleve]=array();

			$current_id_classe=$tab[0];
			$current_periode=$tab[1];
			$current_eleve_login=$tab[2];

			$classe=get_nom_classe($current_id_classe);

			$tab_current_ele=get_info_eleve($current_eleve_login, $current_periode);

			$tab_eleves_OOo[$nb_eleve]['nom']=$tab_current_ele['nom'];
			$tab_eleves_OOo[$nb_eleve]['prenom']=$tab_current_ele['prenom'];
			$tab_eleves_OOo[$nb_eleve]['classe']=$classe;

			$tab_eleves_OOo[$nb_eleve]['periode']=array();
			$sql="SELECT * FROM periodes WHERE id_classe='$current_id_classe' ORDER BY num_periode;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig=mysqli_fetch_object($res)) {
					if($lig->num_periode==$current_periode) {
						$tab_eleves_OOo[$nb_eleve]['per'][$lig->num_periode]="X";
						// Nom de la période
						$tab_eleves_OOo[$nb_eleve]['nomper']=$lig->nom_periode;
					}
					else {
						$tab_eleves_OOo[$nb_eleve]['per'][$lig->num_periode]="";
					}
				}
			}

			$tab_eleves_OOo[$nb_eleve]['annee']=getSettingValue('gepiYear');

			$tab_eleves_OOo[$nb_eleve]['etab']=getSettingValue('gepiSchoolName');
			$tab_eleves_OOo[$nb_eleve]['adr1']=getSettingValue('gepiSchoolAdress1');
			$tab_eleves_OOo[$nb_eleve]['adr2']=getSettingValue('gepiSchoolAdress2');
			$tab_eleves_OOo[$nb_eleve]['cp']=getSettingValue('gepiSchoolZipCode');
			$tab_eleves_OOo[$nb_eleve]['ville']=getSettingValue('gepiSchoolCity');
			$tab_eleves_OOo[$nb_eleve]['tel']=getSettingValue('gepiSchoolTel');
			$tab_eleves_OOo[$nb_eleve]['fax']=getSettingValue('gepiSchoolFax');

			$tab_eleves_OOo[$nb_eleve]['acad']=getSettingValue('gepiSchoolAcademie');

			// id_type_avertissement
			$tab_eleves_OOo[$nb_eleve]['ita']=array();

			$tmp_tab=array();
			$sql="SELECT * FROM s_avertissements WHERE login_ele='".$current_eleve_login."' AND periode='".$current_periode."';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig=mysqli_fetch_object($res)) {
					$tmp_tab[]=$lig->id_type_avertissement;
				}
			}

			foreach($tab_type_avertissement_fin_periode['id_type_avertissement'] as $key => $value) {
				if(in_array($key, $tmp_tab)) {
					$tab_eleves_OOo[$nb_eleve]['ita'][$key]="X";
				}
				else {
					$tab_eleves_OOo[$nb_eleve]['ita'][$key]="";
				}
			}

			// [eleves.sc.116A; if [val]='MS'; then X ; else '']

			$nb_eleve++;
		}
	}

	$mode_ooo="imprime";

	include_once('../mod_ooo/lib/tinyButStrong.class.php');
	include_once('../mod_ooo/lib/tinyDoc.class.php');

	$tempdir=get_user_temp_directory();
	
	$fb_dezip_ooo=getSettingValue("fb_dezip_ooo");

	if($fb_dezip_ooo==2) {
		$msg="Mode \$fb_dezip_ooo=$fb_dezip_ooo non traité pour le moment... désolé.<br />";
	}
	else {
		$tempdirOOo="../temp/".$tempdir;

		$nom_dossier_temporaire = $tempdirOOo;
		//par defaut content.xml
		$nom_fichier_xml_a_traiter ='content.xml';

		// Création d'une classe tinyDoc
		$OOo = new tinyDoc();
		
		// Choix du module de dézippage
		$dezippeur=getSettingValue("fb_dezip_ooo");
		if ($dezippeur==1){
			$OOo->setZipMethod('shell');
			$OOo->setZipBinary('zip');
			$OOo->setUnzipBinary('unzip');
		}
		else{
			$OOo->setZipMethod('ziparchive');
		}

		$prefixe_generation_hors_dossier_mod_ooo="../mod_ooo/";
		$nom_fichier_modele_ooo="avertissement_fin_periode.odt";

		//Procédure du traitement à effectuer
		//les chemins contenant les données
		include_once("../mod_ooo/lib/chemin.inc.php");

		//echo "\$nom_dossier_modele_a_utiliser=$nom_dossier_modele_a_utiliser<br />";

		// setting the object
		$OOo->SetProcessDir($nom_dossier_temporaire ); //dossier où se fait le traitement (décompression / traitement / compression)
		// create a new openoffice document from the template with an unique id
		$OOo->createFrom($nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo); // le chemin du fichier est indiqué à partir de l'emplacement de ce fichier
		// merge data with openoffice file named 'content.xml'
		$OOo->loadXml($nom_fichier_xml_a_traiter); //Le fichier qui contient les variables et doit être parsé (il sera extrait)
		
		
		// Traitement des tableaux
		// On insère ici les lignes concernant la gestion des tableaux
		
		// $OOo->mergeXmlBlock('eleves',$tab_eleves_OOo);
		
		$OOo->mergeXml(
			array(
				'name'      => 'eleves',
				'type'      => 'block',
				'data_type' => 'array',
				'charset'   => 'UTF-8'
			),$tab_eleves_OOo);
		
		$OOo->SaveXml(); //traitement du fichier extrait
		
		$OOo->sendResponse(); //envoi du fichier traité
		$OOo->remove(); //suppression des fichiers de travail
		// Fin de traitement des tableaux
		$OOo->close();
		
		die();
	}
}

//**************** EN-TETE *******************************
// End standart header
$titre_page = "Impression des $mod_disc_terme_avertissement_fin_periode";
require_once("../lib/header.inc.php");
//********************************************************

//debug_var();

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if(!isset($periode)) {
	// Choix de la (des?) période(s)

	echo "</p>\n

<h2>Impression des $mod_disc_terme_avertissement_fin_periode</h2>\n

<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>
	<fieldset class='fieldset_opacite50'>
		<p class='bold'>Choix de la (ou des) période(s)&nbsp;:</p>";

	//$sql="SELECT DISTINCT sa.periode FROM s_avertissements sa, j_eleves_classes jec WHERE sa.login_ele=jec.login AND jec.periode=sa.periode ORDER BY sa.periode;";
	// Pour éviter des problèmes de ré-impression avec des élèves changeant de classe
	$sql="SELECT DISTINCT sa.periode FROM s_avertissements sa, j_eleves_classes jec WHERE sa.login_ele=jec.login ORDER BY sa.periode;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "
		<p style='color:red'>Aucun $mod_disc_terme_avertissement_fin_periode ne semble encore attribué.</p>\n";
	}
	else {
		echo "
		<p>";
		while($lig=mysqli_fetch_object($res)) {
			echo "
			<input type='checkbox' name='periode[]' id='periode_".$lig->periode."' value='$lig->periode' /><label for='periode_".$lig->periode."'>Période ".$lig->periode."</label><br />\n";
		}
		echo "
		</p>";
	}

	echo "
		<p><input type='submit' name='Valider' value='Valider' /></p>
	</fieldset>
</form>

<p><br /></p>
<p style='margin-left:4em;text-indent:-4em;'><em>NOTES&nbsp;:</em> Le fichier modèle OOo utilisé utilise une police particulière pour les cases à cocher (<a href='http://www.sylogix.org/attachments/download/243/gepi.ttf'>gepi.ttf</a>)<br />
Il faut mettre en place cette police sur votre machine pour obtenir les cases correctement cochées.<br />
A défaut, vous pouvez opter pour un autre mode d'affichage en créant votre propre modèle.<br />
Quelques ressources&nbsp;:
<ul style='margin-left:4em;'>
	<li><a href=''>http://www.sylogix.org/projects/gepi/wiki/GepiDoc_fbOooCalc)</a></li>
	<li>
		Les champs tests sur les cases cochées pour les avertissements nécessitent que vous sachiez quel est l'identifiant de chaque sanction.<br />
		Pour information, sur votre Gepi, la liste est la suivante&nbsp;:
		".affiche_tab_type_avertissement()."
		Pour le type dont l'identifiant est <span style='color:red'>1</span> (<em>si cet identifiant existe</em>), le test serait<br />
		<span style='color:blue'>[eleves.ita.<span style='color:red'>1</span>; if [val]='X'; then X ; else 0]</span><br />
		Le X est traduit avec la police gepi.ttf par une case cochée, et le 0 par une case non cochée.
	</li>
</ul>";

	require("../lib/footer.inc.php");
	die();
}


if(!isset($id_classe)) {
	// Choix des classes

	$liste_periodes=implode(",", $periode);

	$input_periode="";
	$chaine_periode="";
	for($loop=0;$loop<count($periode);$loop++) {
		if($loop>0) {$chaine_periode.=" OR ";}
		$chaine_periode.="sa.periode='".$periode[$loop]."'";
		$input_periode.="<input type='hidden' name='periode[]' value='".$periode[$loop]."' />\n";
	}

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres périodes</a></p>

<h2>Impression des $mod_disc_terme_avertissement_fin_periode en période(s) $liste_periodes</h2>\n

<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>
	<fieldset class='fieldset_opacite50'>
		<p class='bold'>Choix de la (ou des) classe(s)&nbsp;:</p>
		$input_periode";

	$sql="SELECT DISTINCT c.classe, jec.id_classe FROM s_avertissements sa, 
									j_eleves_classes jec, 
									classes c 
								WHERE sa.login_ele=jec.login AND
									($chaine_periode) AND
									c.id=jec.id_classe
								ORDER BY c.classe;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "
		<p style='color:red'>Aucune classe n'a été trouvée???</p>\n";
	}
	else {
		echo "
		<p>";
		while($lig=mysqli_fetch_object($res)) {
			echo "
			<input type='checkbox' name='id_classe[]' id='id_classe_".$lig->id_classe."' value='$lig->id_classe' /><label for='id_classe_".$lig->id_classe."'> ".$lig->classe."</label><br />\n";
		}
		echo "
		</p>";
	}

	echo "
		<p><input type='submit' name='Valider' value='Valider' /></p>
	</fieldset>
</form>\n";

	require("../lib/footer.inc.php");
	die();
}

//==============================================================================
// Choix des élèves

$chaine_retour_periode="";
for($loop=0;$loop<count($periode);$loop++) {
	if($loop>0) {$chaine_retour_periode.="&amp;";}
	$chaine_retour_periode.="periode[]=".$periode[$loop];
}

echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres périodes</a> | <a href='".$_SERVER['PHP_SELF']."?$chaine_retour_periode'>Choisir d'autres classes</a></p>

<h2>Impression des $mod_disc_terme_avertissement_fin_periode</h2>

<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire' target='_blank'>
	<fieldset class='fieldset_opacite50'>
		<p class='bold'>Choix des élèves&nbsp;:</p>";
for($loop=0;$loop<count($id_classe);$loop++) {
	echo "
		<div style='margin-left:3em; width:25em; float:left;'>
			<p>Classe de ".get_nom_classe($id_classe[$loop])."</p>
			<input type='hidden' name='id_classe[]' value='".$id_classe[$loop]."' />";

	for($loop2=0;$loop2<count($periode);$loop2++) {
		echo "
				<p style='margin-left:2em;'>Période ".$periode[$loop2]."</p>";

		if($loop==0) {
			echo "
				<input type='hidden' name='periode[]' value='".$periode[$loop2]."' />";
		}

		$sql="SELECT DISTINCT e.nom, e.prenom, e.login FROM eleves e, 
								j_eleves_classes jec, 
								s_avertissements sa
							WHERE e.login=jec.login AND 
								jec.id_classe='".$id_classe[$loop]."' AND 
								e.login=sa.login_ele AND
								sa.periode='".$periode[$loop2]."'
							ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "
				<p style='margin-left:4em; color:red;'>Aucun élève ???</p>";
		}
		else {
			echo "
				<p style='margin-left:4em;'>";
			$cpt=0;
			while($lig=mysqli_fetch_object($res)) {
				echo "
					<input type='checkbox' name='eleve[]' id='eleve_".$id_classe[$loop]."_".$periode[$loop2]."_".$cpt."' value=\"".$id_classe[$loop]."|".$periode[$loop2]."|".$lig->login."\" /><label for='eleve_".$id_classe[$loop]."_".$periode[$loop2]."_".$cpt."'> ".$lig->prenom." ".$lig->nom."</label><br />\n";
				$cpt++;
			}
			echo "
				</p>";
		}
	}

	echo "
		</div>";
}

echo "
		<p><input type='submit' name='Valider' value='Valider' /></p>

		<div style='clear:both;'></div>
		<p><input type='submit' name='Valider' value='Valider' /></p>
	</fieldset>
</form>\n";


require("../lib/footer.inc.php");
?>
