<?php
/*
* Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_sso_table/publipostage.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_sso_table/publipostage.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='SSO table : Publipostage',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$mode=isset($_POST['mode']) ? $_POST['mode'] : NULL;

if(!isset($mode)) {
	header("Location: ./index.php?ctrl=cvsent&msg=Mode non choisi");
	die();
}
/*
elseif($mode=="enregistrer_correspondances") {
	check_token();

	//debug_var();

	$msg="";
	$assoc=isset($_POST['assoc']) ? $_POST['assoc'] : array();
	if(count($assoc)==0) {
		$msg="Aucune association n'a été cochée.<br />";
	}
	else {
		$compteur=isset($_POST['compteur']) ? $_POST['compteur'] : 0;

		$nb_reg=0;
		$nb_err=0;
		//for($loop=0;$loop<count($assoc);$loop++) {
		for($loop=0;$loop<$compteur;$loop++) {
			if(isset($assoc[$loop])) {
				$tab=explode("|", $assoc[$loop]);
				if((isset($tab[1]))&&($tab[0]!="")&&($tab[1]!="")) {
					$sql="DELETE FROM sso_table_correspondance WHERE login_gepi='".$tab[1]."';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);

					$sql="DELETE FROM sso_table_correspondance WHERE login_sso='".$tab[0]."';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);

					$sql="INSERT INTO sso_table_correspondance SET login_sso='".$tab[0]."', login_gepi='".$tab[1]."';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'enregistrement de l'association ".$tab[0]." &lt;-&gt; ".$tab[1]."<br />";
						$nb_err++;
					}
					else {
						$nb_reg++;
					}
				}
			}
		}

		if($nb_reg>0) {
			$msg.="<span style='color:green'>".$nb_reg." association(s) enregistrée(s).</span><br />";
		}
		if($nb_err>0) {
			$msg.="<span style='color:red'>".$nb_err." erreur(s) lors de l'enregistrement des associations.</span><br />";
		}
	}
}
*/

//$javascript_specifique[] = "lib/tablekit";
//$utilisation_tablekit="ok";

//**************** EN-TETE *****************
if(($mode!="publiposter")&&($mode!="publiposter2")) {
	$titre_page = "SSO : Publipostage";
}
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

if($mode!='publiposter') {
	echo "<p class='bold'><a href='./index.php?ctrl=cvsent#publipostage'>Retour</a></p>";
}

if($mode=="upload") {

	//echo "<p class='bold'><a href='./index.php?ctrl=cvsent'>Retour</a></p>";

	echo "<h2>Réception puis analyse du fichier CSV</h2>";

	if(!isset($_FILES['fichier'])) {
		echo "<p style='color:red'>Echec de l'envoi du fichier.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	if(!isset($_FILES['fichier']['tmp_name'])) {
		echo "<p style='color:red'>Echec de l'envoi du fichier ou fichier non conservé.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		$file_tmp=$_FILES['fichier']['tmp_name'];
		if ($file_tmp=='') {
			echo "<p style='color:red'>Le nom du fichier temporaire est vide.</p>";
			require("../lib/footer.inc.php");
			die();
		}

		$tempdir=get_user_temp_directory();
		if($tempdir=="") {
			echo "<p style='color:red'>Dossier temporaire de l'utilisateur non valide.</p>";
			require("../lib/footer.inc.php");
			die();
		}

		$file_name=$_FILES['fichier']['name'];
		$file_size=$_FILES['fichier']['size'];
		$file_type=$_FILES['fichier']['type'];

		if(is_uploaded_file($file_tmp)) {
			$dest_file = "../temp/".$tempdir."/fichier_export_ent.csv";

			$res_copy=copy("$file_tmp" , "$dest_file");
			if(!$res_copy) {
				echo "<p style='color:red'>Echec de la copie du fichier dans le dossier temporaire de l'utilisateur.</p>";
				require("../lib/footer.inc.php");
				die();
			}
			else {

				$fp=fopen($dest_file,"r");

				if(!$fp){
					echo "<p style='color:red'>Impossible d'ouvrir le fichier CSV !</p>";
					require("../lib/footer.inc.php");
					die();
				}

				// Civilité;Nom;Prénom;Profil;Login;Identifiant ENT;Etablissement;
				// Lire la ligne d'entête pour repérer les indices des colonnes recherchées
				//$tabchamps = array("Civilité", "Nom", "Prénom", "Prenom", "Profil", "Login", "Identifiant ENT", "Etablissement");
				//$tabchamps = array("Nom", "Prénom", "Profil", "Login", "Identifiant ENT");

				//rne;uid;classe;profil;prenom;nom;login;mot de passe;cle de jointure;uid pere;uid mere;uid tuteur1;uid tuteur2;prenom enfant;nom enfant;adresse;code postal;ville;pays
				$tabchamps = array("profil", "classe", "nom", "prenom", "login", "mot de passe", "prenom enfant", "nom enfant", "adresse", "code postal", "ville", "pays");


				// Lecture de la ligne 1 et la mettre dans $temp
				$ligne_entete=trim(fgets($fp,4096));
				if((substr($ligne_entete,0,3) == "\xEF\xBB\xBF")) {
					$ligne_entete=substr($ligne_entete,3);
				}
				//echo "$ligne_entete<br />";
				$en_tete=explode(";", $ligne_entete);

				$tabindice=array();

				// On range dans tabindice les indices des champs retenus
				for ($k = 0; $k < count($tabchamps); $k++) {
					//echo "<br /><p style='text-indent:-4em;margin-left:4em'>Recherche du champ ".$tabchamps[$k]."<br />";
					for ($i = 0; $i < count($en_tete); $i++) {
						//echo "\$en_tete[$i]=$en_tete[$i]<br />";
						//echo casse_mot(remplace_accents($en_tete[$i]),'min')."<br />";
						//echo casse_mot(remplace_accents($tabchamps[$k]), 'min')."<br />";
						if (casse_mot(remplace_accents($en_tete[$i]),'min') == casse_mot(remplace_accents($tabchamps[$k]), 'min')) {
							$tabindice[$tabchamps[$k]] = $i;
							//echo "\$tabindice[$tabchamps[$k]]=$i<br />";
						}
					}
				}
				if((!isset($tabindice['nom']))||(!isset($tabindice['prenom']))||(!isset($tabindice['login']))) {
					echo "<p style='color:red'>La ligne d'entête ne comporte pas un des champs indispensables (<em>nom, prenom, login</em>).</p>";
					require("../lib/footer.inc.php");
					die();
				}

				$cpt=0;
				for($loop=0;$loop<count($tabchamps);$loop++) {
					if(isset($tabindice[$tabchamps[$loop]])) {
						$cpt++;
					}
				}

				if(!isset($tabindice['classe'])) {
					echo "<p>Le fichier CSV fourni ne comporte pas de colonne 'classe'.<br />Le publipostage va être effectué sur l'ensemble des lignes (<em>toutes classes confondues</em>).</p>";
				}

				if(!isset($tabindice['profil'])) {
					echo "<p>Le fichier CSV fourni ne comporte pas de colonne 'profil'.<br />Le publipostage va être effectué sur l'ensemble des lignes (<em>tous profils confondus</em>).</p>";
				}

				// On fait la liste des classes
				$tab_classe=array();
				$tab_profil=array();
				$compteur=0;
				while (!feof($fp)) {
					$ligne = trim(fgets($fp, 4096));
					if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
						$ligne=substr($ligne,3);
					}

					if($ligne!='') {
						$infos="";
						$tab=explode(";", ensure_utf8($ligne));

						if(isset($tabindice['classe'])) {

							if((isset($tabindice['nom enfant']))&&(isset($tabindice['prenom enfant']))&&($tab[$tabindice['classe']]=="")) {
								$sql="SELECT DISTINCT classe FROM eleves e, j_eleves_classes jec, classes c WHERE e.login=jec.login AND jec.id_classe=c.id AND e.nom='".mysqli_real_escape_string($GLOBALS['mysqli'], $tab[$tabindice['nom enfant']])."' AND e.prenom='".mysqli_real_escape_string($GLOBALS['mysqli'], $tab[$tabindice['prenom enfant']])."';";
								$res_classe=mysqli_query($GLOBALS['mysqli'], $sql);
								if(mysqli_num_rows($res_classe)==1) {
									$lig_classe=mysqli_fetch_object($res_classe);
									$tab[$tabindice['classe']]=$lig_classe->classe;
								}
							}

							if(!in_array($tab[$tabindice['classe']], $tab_classe)) {
								$tab_classe[]=$tab[$tabindice['classe']];
							}
						}

						if(isset($tabindice['profil'])) {
							if(!in_array($tab[$tabindice['profil']], $tab_profil)) {
								$tab_profil[]=$tab[$tabindice['profil']];
							}
						}

					}
					$compteur++;
				}

				sort($tab_classe);
				sort($tab_profil);

				$url_connexion_ent=getSettingValue("url_connexion_ent");
				if(getSettingAOui("inclure_url_connexion_ent")) {
					$checked_inclure_url_connexion_ent=" checked";
					$style_inclure_url_connexion_ent=" style='font-weight:bold;'";
				}
				else {
					$checked_inclure_url_connexion_ent="";
					$style_inclure_url_connexion_ent="";
				}
				echo "<form action='publipostage.php' enctype='multipart/form-data' method='post' target='_blank'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<h2>Publipostage</h2>
		<div style='margin-left:3em;'>
		<input type='hidden' name='mode' value='publiposter' />
		<p style='text-indent:-3em;margin-left:3em;'>Paramètres du publipostage&nbsp;:<br />
		<input type='checkbox' name='inclure_url_connexion_ent' id='inclure_url_connexion_ent' value='y' onchange=\"checkbox_change(this.id)\"$checked_inclure_url_connexion_ent /><label for='inclure_url_connexion_ent' id='texte_inclure_url_connexion_ent'$style_inclure_url_connexion_ent> Inclure l'URL de connexion <input type='text' name='url_connexion_ent' value='".$url_connexion_ent."' /> dans le tableau des informations Login, mot de passe.</label><br />
		<input type='checkbox' name='fiche_bienvenue' id='fiche_bienvenue' value='y' onchange=\"checkbox_change(this.id)\" /><label for='fiche_bienvenue' id='texte_fiche_bienvenue'> Inclure la fiche bienvenue sous les informations Login, mot de passe.</label><br />
		<input type='checkbox' name='mot_de_passe_deja_modifie' id='mot_de_passe_deja_modifie' value='n' onchange=\"checkbox_change(this.id)\" /><label for='mot_de_passe_deja_modifie' id='texte_mot_de_passe_deja_modifie'> Ne pas imprimer les fiches pour lesquelles le mot de passe a déjà été modifié.</label><br />
		<em style='font-size:x-small'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(la chaine testée est '<span style='color:green'>Mot de passe déjà modifié par utilisateur</span>')</em><br />
		<br />

		<input type='checkbox' name='envoi_mail' id='envoi_mail' value='y' onchange=\"checkbox_change(this.id)\" /><label for='envoi_mail' id='texte_envoi_mail'> Envoyer la fiche par mail aux utilisateurs concernés <em>(sous réserve que l'adresse mail de l'utilisateur associé au compte soit renseigné)</em>.</label><br />
		<br />

		<label for='envoi_mail_adresse_specifiee' id='texte_envoi_mail_adresse_specifiee'> Envoyer les fiches par mail à l'adresse suivante&nbsp;: <input type='text' name='envoi_mail_adresse_specifiee' id='envoi_mail_adresse_specifiee' value='' /></label> <em>(typiquement, l'adresse mail du CPE s'il est chargé de distribuer les fiches)</em><br /><em>(et ceci que l'envoi aux responsables/élèves conservés soit demandé/coché ou non)</em><br />
		&nbsp;&nbsp;&nbsp;<input type='radio' name='envoi_mail_autre_adresse' id='envoi_mail_autre_adresse_toujours' value='toujours' onchange=\"checkbox_change('envoi_mail_autre_adresse_sinon');checkbox_change('envoi_mail_autre_adresse_toujours')\" /><label for='envoi_mail_autre_adresse_toujours' id='texte_envoi_mail_autre_adresse_toujours'>qu'il y ait ou non une adresse mail de l'utilisateur parent/élève concerné,</label><br />
		&nbsp;&nbsp;&nbsp;<input type='radio' name='envoi_mail_autre_adresse' id='envoi_mail_autre_adresse_sinon' value='sinon' onchange=\"checkbox_change('envoi_mail_autre_adresse_sinon');checkbox_change('envoi_mail_autre_adresse_toujours')\" checked /><label for='envoi_mail_autre_adresse_sinon' id='texte_envoi_mail_autre_adresse_sinon'>seulement s'il n'y a pas d'adresse mail pour l'utilisateur parent/élève concerné.</label><br />
		<span style='color:red'>Attention&nbsp;:</span> Si vous générez plusieurs fiches de connexion, l'envoi à une adresse commune ne présente d'intérêt que pour envoyer au CPE qui se chargera d'imprimer et distribuer les fiches aux élèves <em>(ne mettez pas une adresse mail parent/élève dans le champ adresse ci-dessus sauf si vous ne générez <strong>que</strong> la fiche bienvenue correspondante)</em>.
		<br />
		</p>";

				if(count($tab_profil)>0) {
					echo "
		<p style='text-indent:-3em;margin-left:3em;margin-top:1em;'>Profils&nbsp;:";
					for($loop=0;$loop<count($tab_profil);$loop++) {
						echo "<br />
			<input type='checkbox' name='profil[]' id='profil_$loop' value=\"".$tab_profil[$loop]."\" onchange=\"checkbox_change(this.id)\" /><label for='profil_$loop' id='texte_profil_$loop'>".$tab_profil[$loop]."</label>";
					}
				echo "
		</p>";
				}

				if(count($tab_classe)>0) {
					echo "
		<p style='text-indent:-3em;margin-left:3em;margin-top:1em;'>Classes&nbsp;:";
					for($loop=0;$loop<count($tab_classe);$loop++) {
						$info_classe=$tab_classe[$loop];
						if($info_classe=="") {
							$info_classe="<span style='color:red'>Aucune classe (<em>parents ou élèves de l'année passée?</em>)</span>";
						}
						echo "<br />
			<input type='checkbox' name='classe[]' id='classe_$loop' value=\"".$tab_classe[$loop]."\" onchange=\"checkbox_change(this.id)\" /><label for='classe_$loop' id='texte_classe_$loop'>".$info_classe."</label>";
					}
				echo "
			<br />
			<a href='javascript:cocher_toutes_classes($loop)'>Cocher toutes les classes</a> / 
			<a href='javascript:decocher_toutes_classes($loop)'>Décocher toutes les classes</a>
		</p>";
				}

				echo "
		<p><input type='submit' value='Valider' /></p>

		<p style='margin-top:1em;'>
			<a href='javascript:cocher_tous_checkbox()'>Tout cocher</a> / 
			<a href='javascript:decocher_tous_checkbox()'>Tout décocher</a>
		</p>

		</div>
	</fieldset>
</form>

<p><em>NOTES&nbsp;:</em></p>
<ul>
	<li>
		<p>Si vous sélectionnez des classes, les fiches seront triées par classe, puis par ordre alphabétique des noms et prénoms.<br />
		Sinon, toutes les lignes du fichier fourni seront parcourues et traitées/affichées dans l'ordre du fichier.</p>
	</li>
	<li>
		<p>Si vous effectuez le publipostage pour plusieurs profils différents sans fiche bienvenue insérée, la découpe risque de ne pas être pratique.<br />
		Pour les élèves, il y a en effet 4 lignes, contre 5 pour les responsables.<br />
		Il vaut mieux alors imprimer les fiches pour les élèves dans un premier temps et pour les responsables dans un deuxième temps.</p>
		<p>Avec fiche bienvenue en revanche, un saut de page est inséré si bien que le problème ci-dessus ne se présente plus.</p>
	</li>";

				if(acces("/gestion/modify_impression.php", $_SESSION['statut'])) {
					echo "
	<li>
		<p><a href='../gestion/modify_impression.php' target='_blank'>Contrôler/Modifier les fiches bienvenue</a>.</p>
	</li>";
				}

				echo "
</ul>

<script type='text/javascript'>
	function cocher_toutes_classes(num) {
		for(i=0;i<num;i++) {
			if(document.getElementById('classe_'+i)) {
				document.getElementById('classe_'+i).checked=true;
				checkbox_change('classe_'+i);
			}
		}
	}

	function decocher_toutes_classes(num) {
		for(i=0;i<num;i++) {
			if(document.getElementById('classe_'+i)) {
				document.getElementById('classe_'+i).checked=false;
				checkbox_change('classe_'+i);
			}
		}
	}

	function cocher_tous_checkbox() {
		champs_input=document.getElementsByTagName('input');
		for(i=0;i<champs_input.length;i++){
			id_champ=champs_input[i].getAttribute('id');
			type=champs_input[i].getAttribute('type');
			if(type==\"checkbox\"){
				champs_input[i].checked=true;
				checkbox_change(id_champ);
			}
		}
	}

	function decocher_tous_checkbox() {
		champs_input=document.getElementsByTagName('input');
		for(i=0;i<champs_input.length;i++){
			id_champ=champs_input[i].getAttribute('id');
			type=champs_input[i].getAttribute('type');
			if(type==\"checkbox\"){
				champs_input[i].checked=false;
				checkbox_change(id_champ);
			}
		}
	}

	".js_checkbox_change_style()."

	checkbox_change('envoi_mail_autre_adresse_sinon');
	checkbox_change('envoi_mail_autre_adresse_toujours');
</script>";

			}
		}
	}
}
elseif($mode=='publiposter') {

	//debug_var();
	// Désactiver le mode deflate afin que les ob_flush() et flush() fonctionnent
	if (function_exists('apache_setenv')) apache_setenv("no-gzip","1");

	if(isset($_POST['inclure_url_connexion_ent'])) {
		saveSetting('inclure_url_connexion_ent', 'y');
		$inclure_url_connexion_ent="y";
	}
	else {
		saveSetting('inclure_url_connexion_ent', 'n');
		$inclure_url_connexion_ent="n";
	}

	if(isset($_POST['url_connexion_ent'])) {
		saveSetting('url_connexion_ent', $_POST['url_connexion_ent']);
	}
	$url_connexion_ent=getSettingValue('url_connexion_ent');

	if(isset($_POST['envoi_mail'])) {
		$envoi_mail="y";
	}
	else {
		$envoi_mail="n";
	}
	$nb_mail=0;
	$chaine_mail_envoye="";

	$tempdir=get_user_temp_directory();
	if($tempdir=="") {
		echo "<p style='color:red'>Dossier temporaire de l'utilisateur non valide.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$dest_file = "../temp/".$tempdir."/fichier_export_ent.csv";

	$fp=fopen($dest_file,"r");

	if(!$fp){
		echo "<p style='color:red'>Impossible d'ouvrir le fichier CSV !</p>";
		echo "<p><a href='index.php?ctrl=cvsent#publipostage'>Retour</a></p>";
		require("../lib/footer.inc.php");
		die();
	}

	$fiche_bienvenue=isset($_POST['fiche_bienvenue']) ? $_POST['fiche_bienvenue'] : "n";
	$mot_de_passe_deja_modifie=isset($_POST['mot_de_passe_deja_modifie']) ? $_POST['mot_de_passe_deja_modifie'] : "y";
	$classe=isset($_POST['classe']) ? $_POST['classe'] : NULL;
	$profil=isset($_POST['profil']) ? $_POST['profil'] : NULL;
	$envoi_mail_adresse_specifiee=isset($_POST['envoi_mail_adresse_specifiee']) ? $_POST['envoi_mail_adresse_specifiee'] : "";
	$envoi_mail_autre_adresse=isset($_POST['envoi_mail_autre_adresse']) ? $_POST['envoi_mail_autre_adresse'] : "sinon";
	$compilation_fb="";

	// Ajout de l'UID pour croiser avec sso_table_correspondance et s'assurer que l'élève est encore dans l'établissement
	$tabchamps = array("uid", "profil", "classe", "nom", "prenom", "login", "mot de passe", "prenom enfant", "nom enfant", "adresse", "code postal", "ville", "pays");

	// Lecture de la ligne 1 et la mettre dans $temp
	$ligne_entete=trim(fgets($fp,4096));
	if((substr($ligne_entete,0,3) == "\xEF\xBB\xBF")) {
		$ligne_entete=substr($ligne_entete,3);
	}
	//echo "$ligne_entete<br />";
	$en_tete=explode(";", $ligne_entete);

	$tabindice=array();

	// On range dans tabindice les indices des champs retenus
	for ($k = 0; $k < count($tabchamps); $k++) {
		//echo "<br /><p style='text-indent:-4em;margin-left:4em'>Recherche du champ ".$tabchamps[$k]."<br />";
		for ($i = 0; $i < count($en_tete); $i++) {
			//echo "\$en_tete[$i]=$en_tete[$i]<br />";
			//echo casse_mot(remplace_accents($en_tete[$i]),'min')."<br />";
			//echo casse_mot(remplace_accents($tabchamps[$k]), 'min')."<br />";
			if (casse_mot(remplace_accents($en_tete[$i]),'min') == casse_mot(remplace_accents($tabchamps[$k]), 'min')) {
				$tabindice[$tabchamps[$k]] = $i;
				//echo "\$tabindice[$tabchamps[$k]]=$i<br />";
			}
		}
	}
	if((!isset($tabindice['nom']))||(!isset($tabindice['prenom']))||(!isset($tabindice['login']))) {
		echo "<p style='color:red'>La ligne d'entête ne comporte pas un des champs indispensables (<em>nom, prenom, login</em>).</p>";
		require("../lib/footer.inc.php");
		die();
	}

	/*
	echo "<pre>";
	print_r($tabindice);
	echo "</pre>";
	*/

	$cpt=0;
	for($loop=0;$loop<count($tabchamps);$loop++) {
		if(isset($tabindice[$tabchamps[$loop]])) {
			$cpt++;
		}
	}

	$Impression=getSettingValue('Impression');
	$ImpressionFicheParent=getSettingValue('ImpressionFicheParent');
	$ImpressionFicheEleve=getSettingValue('ImpressionFicheEleve');

	$ImpressionNombre=getSettingValue('ImpressionNombre');
	$ImpressionNombreParent=getSettingValue('ImpressionNombreParent');
	$ImpressionNombreEleve=getSettingValue('ImpressionNombreEleve');
	//echo "\$ImpressionNombreEleve=$ImpressionNombreEleve<br />";

	$impression_nombre_global=100;
	//if((isset($profil))&&(isset($tabindice['profil']))&&(count($tabindice['profil'])>1)) {
	if(isset($profil)) {
		for($loop=0;$loop<count($profil);$loop++) {
			//echo "\$profil[$loop]=$profil[$loop]<br />";
			if(((mb_strtolower($profil[$loop])=="parent")||(mb_strtolower($profil[$loop])=="tuteur")||(mb_strtolower($profil[$loop])=="responsable"))&&($ImpressionNombreParent<$impression_nombre_global)) {
				$impression_nombre_global=$ImpressionNombreParent;
			}
			elseif((mb_strtolower($profil[$loop])=="eleve")&&($ImpressionNombreEleve<$impression_nombre_global)) {
				$impression_nombre_global=$ImpressionNombreEleve;
			}
			//echo "\$impression_nombre_global=$impression_nombre_global<br />";
		}
	}

	if($impression_nombre_global==100) {
		$impression_nombre_global=1;
	}
	elseif($impression_nombre_global<=0) {
		$impression_nombre_global=1;
	}

	/*
	echo "<pre>";
	print_r($tabindice);
	echo "</pre>";
	*/

	$compteur_pages_imprimees=0;
	// Pour ne pas imprimer deux fiches pour un parent de 2 enfants.
	$tab_deja=array();
	if((isset($classe))&&(count($classe)>1)) {
		$compteur=0;
		$compteur_pages_imprimees=0;

		for($loop=0;$loop<count($classe);$loop++) {
			$tab_cle_courante=array();
			$tab_courant=array();

			$fp=fopen($dest_file,"r");
			$ligne_entete=trim(fgets($fp,4096));

			while (!feof($fp)) {
				$ligne = trim(fgets($fp, 4096));
				if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
					$ligne=substr($ligne,3);
				}

				if($ligne!='') {
					$infos="";
					$tab=explode(";", ensure_utf8($ligne));

					/*
					echo "<pre>";
					print_r($tab);
					echo "</pre>";
					*/

					$afficher="y";
					if((isset($profil))&&(isset($tabindice['profil']))&&(!in_array($tab[$tabindice['profil']], $profil))) {
						$afficher="n";
					}

					if((isset($tabindice['nom enfant']))&&(isset($tabindice['prenom enfant']))&&($tab[$tabindice['classe']]=="")) {
						$sql="SELECT DISTINCT classe FROM eleves e, j_eleves_classes jec, classes c WHERE e.login=jec.login AND jec.id_classe=c.id AND e.nom='".mysqli_real_escape_string($GLOBALS['mysqli'], $tab[$tabindice['nom enfant']])."' AND e.prenom='".mysqli_real_escape_string($GLOBALS['mysqli'], $tab[$tabindice['prenom enfant']])."';";
						$res_classe=mysqli_query($GLOBALS['mysqli'], $sql);
						if(mysqli_num_rows($res_classe)==1) {
							$lig_classe=mysqli_fetch_object($res_classe);
							$tab[$tabindice['classe']]=$lig_classe->classe;
						}
					}

					if($tab[$tabindice['classe']]!=$classe[$loop]) {
						$afficher="n";
					}

					if(($mot_de_passe_deja_modifie!="y")&&(preg_match("/Mot de passe déjà modifié par utilisateur/i", $tab[$tabindice['mot de passe']]))) {
						$afficher="n";
					}

					/*
					echo "afficher=$afficher<pre>";
					print_r($tab);
					echo "</pre>";
					*/

					if(($afficher=="y")&&(isset($tabindice["uid"]))) {
						if($tab[$tabindice['uid']]=="") {
							$afficher="n";
						}
						elseif(in_array($tab[$tabindice['uid']], $tab_deja)) {
							$afficher="n";
						}
						else {
							$sql="SELECT 1=1 FROM sso_table_correspondance WHERE login_sso='".$tab[$tabindice['uid']]."';";
							//echo "$sql<br />";
							$test_sso=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test_sso)==0) {
								$afficher="n";
							}
						}
					}

					if($afficher=="y") {
						$tab_deja[]=$tab[$tabindice['uid']];

						$tab_cle_courante[]=$tab[$tabindice['nom']]." ".$tab[$tabindice['prenom']];
						$tab_courant[$tab[$tabindice['nom']]." ".$tab[$tabindice['prenom']]]=$tab;

					}
				}
				$compteur++;
			}

			sort($tab_cle_courante);
			for($loop_cle=0;$loop_cle<count($tab_cle_courante);$loop_cle++) {
				$tab=$tab_courant[$tab_cle_courante[$loop_cle]];

				$fb_courante="
		<table style='page-break-inside: avoid; width:30em; font-size:9pt;' class='boireaus'>
			<tr>
				<td style='width:8em;'>À l'attention de </td>
				<td class='bold'>";
				//$fb_courante.=$tab[$tabindice['nom']]." ".$tab[$tabindice['prenom']];
				//$fb_courante.="<a href='../eleves/recherche.php?rech_nom=".preg_replace("/[^A-Za-z]/","%",$tab[$tabindice['nom']])."&statut[0]=eleve&statut[1]=responsable&is_posted_recherche=y' style='text-decoration:none; color:black;' target='_blank'>".$tab[$tabindice['nom']]." ".$tab[$tabindice['prenom']]."</a>";
				$fb_courante.="<a href=\"#\" onclick=\"cherche_ele_resp('".preg_replace("/[^A-Za-z]/","%",$tab[$tabindice['nom']])."');return false;\" style='text-decoration:none; color:black;' target='_blank'>".$tab[$tabindice['nom']]." ".$tab[$tabindice['prenom']]."</a>";
				$fb_courante.="</td>
			</tr>";
				if($inclure_url_connexion_ent=="y") {
					$fb_courante.="
			<tr>
				<td>URL </td>
				<td class='bold'>".$url_connexion_ent."</td>
			</tr>";
				}
				$fb_courante.="
			<tr>
				<td>Login </td>
				<td class='bold'>".$tab[$tabindice['login']]."</td>
			</tr>
			<tr>
				<td>Mot de passe </td>
				<td class='bold'>".$tab[$tabindice['mot de passe']]."</td>
			</tr>";

				if((isset($tabindice['nom enfant']))&&($tab[$tabindice['nom enfant']]!='')) {
					$fb_courante.="
			<tr>
				<td>Responsable de </td>
				<td>".$tab[$tabindice['nom enfant']];
					if(isset($tabindice['prenom enfant'])) {
						$fb_courante.=" ".$tab[$tabindice['prenom enfant']];
					}
					$fb_courante.="</td>
			</tr>";
				}

				if(isset($tabindice['classe'])) {
					$fb_courante.="
			<tr>
				<td>Classe de </td>
				<td>".$tab[$tabindice['classe']]."</td>
			</tr>";
						}

				

				$fb_courante.="
		</table>";

				if($fiche_bienvenue=="y") {
					if(isset($tabindice['profil'])) {
						if((mb_strtolower($tab[$tabindice['profil']])=='tuteur')||
						(mb_strtolower($tab[$tabindice['profil']])=='parent')||
						(mb_strtolower($tab[$tabindice['profil']])=='responsable')) {
							$page=$ImpressionFicheParent;
						}
						elseif((mb_strtolower($tab[$tabindice['profil']])=='eleve')||(mb_strtolower($tab[$tabindice['profil']])=='élève')) {
							$page=$ImpressionFicheEleve;
						}
						else {
							$page=$Impression;
						}
					}
					else {
						$page=$Impression;
					}
					$fb_courante.=$page;

					echo $fb_courante;

					/*
					if($envoi_mail_adresse_specifiee!="") {
						$compilation_fb.=$fb_courante;
					}
					*/

					if(($compteur_pages_imprimees+1)%$impression_nombre_global==0) {
						echo "<p class='saut'></p>";

						/*
						if($envoi_mail_adresse_specifiee!="") {
							$compilation_fb.="<p class='saut'></p>";
						}
						*/
					}
				}
				else {
					echo $fb_courante;
					echo "<p>&nbsp;</p>";

					/*
					if($envoi_mail_adresse_specifiee!="") {
						$compilation_fb.=$fb_courante."<p>&nbsp;</p>";
					}
					*/
				}

				// Mail 20160905
				if($envoi_mail=="y") {
					$tab_param_mail=array();
					$tab_param_mail['destinataire']=array();
					// Récupérer le login_gepi associé à l'uid.
					// Pour un élève, rechercher l'email dans eleves et les email des parents dans utilisateurs et resp_pers
					// Pour un parent, rechercher les email des parents dans utilisateurs et resp_pers

					$sql="SELECT u.statut, u.login, u.email FROM utilisateurs u, sso_table_correspondance stc WHERE u.login=stc.login_gepi AND stc.login_sso='".$tab[$tabindice['uid']]."';";
					//echo "$sql<br />";
					$res_mail=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res_mail)>0) {
						$lig_mail=mysqli_fetch_object($res_mail);

						if(check_mail($lig_mail->email)) {
							$tab_param_mail['destinataire'][]=$lig_mail->email;
						}

						if($lig_mail->statut=="eleve") {
							$sql="SELECT email FROM eleves WHERE login='".$lig_mail->login."' AND email LIKE '%@%';";
							//echo "$sql<br />";
							$res_mail2=mysqli_query($mysqli, $sql);
							if(mysqli_num_rows($res_mail2)>0) {
								$lig_mail2=mysqli_fetch_object($res_mail2);

								if((check_mail($lig_mail2->email))&&(!in_array($lig_mail2->email, $tab_param_mail['destinataire']))) {
									$tab_param_mail['destinataire'][]=$lig_mail2->email;
								}
							}
							// RECHERCHE DES ADRESSES MAIL DES RESPONSABLES... et envoyer un mail par reponsable? pb des responsables séparés ne voulant pas divulguer leur mail au conjoint?
							$tab_resp=get_resp_from_ele_login($lig_mail->login);
							for($loop_resp=0;$loop_resp<count($tab_resp);$loop_resp++) {
								if((isset($tab_resp[$loop_resp]["mel"]))&&(check_mail($tab_resp[$loop_resp]["mel"]))) {
									$tab_param_mail['destinataire'][]=$tab_resp[$loop_resp]["mel"];
								}
								if((isset($tab_resp[$loop_resp]["email"]))&&(check_mail($tab_resp[$loop_resp]["email"]))) {
									$tab_param_mail['destinataire'][]=$tab_resp[$loop_resp]["email"];
								}
							}
						}
						elseif($lig_mail->statut=="responsable") {
							$sql="SELECT mel AS email FROM resp_pers WHERE login='".$lig_mail->login."' AND mel LIKE '%@%';";
							//echo "$sql<br />";
							$res_mail2=mysqli_query($mysqli, $sql);
							if(mysqli_num_rows($res_mail2)>0) {
								$lig_mail2=mysqli_fetch_object($res_mail2);

								if((check_mail($lig_mail2->email))&&(!in_array($lig_mail2->email, $tab_param_mail['destinataire']))) {
									$tab_param_mail['destinataire'][]=$lig_mail2->email;
								}
							}
						}

						if(count($tab_param_mail['destinataire'])>0) {
							/*$destinataire=$tab_param_mail['destinataire'][0];
							for($loop_mail=1;$loop_mail<count($tab_param_mail['destinataire']);$loop_mail++) {
								$destinataire.=",".$tab_param_mail['destinataire'][$loop_mail];
							}
							if(envoi_mail("Compte Gepi", "Bonjour(soir),\n".$fb_courante, $destinataire, "", "html", $tab_param_mail)) {
								$nb_mail++;
							}
							*/
							// Pour éviter les problèmes d'envoi à plusieurs adresses mail, avec des parents séparés
							for($loop_mail=0;$loop_mail<count($tab_param_mail['destinataire']);$loop_mail++) {
								if($chaine_mail_envoye!="") {
									$chaine_mail_envoye.=", ";
								}

								$destinataire=$tab_param_mail['destinataire'][$loop_mail];
								if(envoi_mail("Compte Gepi", "Bonjour(soir),\n\n".$fb_courante, $destinataire, "", "html", $tab_param_mail)) {
									$chaine_mail_envoye.="<span style='color:green' title=\"Informations de connexion du compte ".$tab[$tabindice['login']]." envoyé avec succès.\">".$destinataire."</span>";
									$nb_mail++;
								}
								else {
									$chaine_mail_envoye.="<span style='color:red' title=\"Échec de l'envoi des informations de connexion du compte ".$tab[$tabindice['login']].".\">".$destinataire."</span>";
								}
							}

							if(($envoi_mail_adresse_specifiee!="")&&($envoi_mail_autre_adresse=="toujours")) {
								//$compilation_fb.="DEBUG: Envoi à l'adresse autre saisie<br />";
								$compilation_fb.=$fb_courante;

								if(($compteur_pages_imprimees+1)%$impression_nombre_global==0) {
									$compilation_fb.="<p class='saut'></p>";
								}
							}
						}
						//elseif(($envoi_mail_adresse_specifiee!="")&&($envoi_mail_autre_adresse=="sinon")) {
						elseif($envoi_mail_adresse_specifiee!="") {
							//$compilation_fb.="DEBUG: Envoi à l'adresse autre saisie<br />";
							$compilation_fb.=$fb_courante;

							if(($compteur_pages_imprimees+1)%$impression_nombre_global==0) {
								$compilation_fb.="<p class='saut'></p>";
							}
						}
					}
					elseif($envoi_mail_adresse_specifiee!="") {
						//$compilation_fb.="DEBUG: Envoi à l'adresse autre saisie<br />";
						$compilation_fb.=$fb_courante;

						if(($compteur_pages_imprimees+1)%$impression_nombre_global==0) {
							$compilation_fb.="<p class='saut'></p>";
						}
					}
				}
				elseif($envoi_mail_adresse_specifiee!="") {
					//$compilation_fb.="DEBUG: Envoi à l'adresse autre saisie<br />";
					$compilation_fb.=$fb_courante;

					if(($compteur_pages_imprimees+1)%$impression_nombre_global==0) {
						$compilation_fb.="<p class='saut'></p>";
					}
				}

				flush();
				$compteur_pages_imprimees++;
				//echo "\$compteur_pages_imprimees=$compteur_pages_imprimees<br />\n";
			}
		} // Fin de la boucle sur les classes

		if(($compilation_fb!="")&&($envoi_mail_adresse_specifiee!="")&&(check_mail($envoi_mail_adresse_specifiee))) {
			$tab_param_mail=array();
			$tab_param_mail['destinataire']=array();
			$tab_param_mail['destinataire'][]=$envoi_mail_adresse_specifiee;

			$destinataire=$envoi_mail_adresse_specifiee;

			if($chaine_mail_envoye!="") {
				$chaine_mail_envoye.=", ";
			}
			if(envoi_mail("Compte(s) Gepi", "Bonjour(soir),\n\n".$compilation_fb, $destinataire, "", "html", $tab_param_mail)) {
				$chaine_mail_envoye.="<span style='color:green' title=\"Informations de connexion envoyées avec succès.\">".$destinataire."</span>";
			}
			else {
				$chaine_mail_envoye.="<span style='color:red' title=\"Échec de l'envoi des informations de connexion.\">".$destinataire."</span>";
			}
		}

		if($chaine_mail_envoye!="") {
			echo "<div class='noprint'><p><strong>Liste des envois de mail&nbsp;:</strong> $chaine_mail_envoye</p></div>";
		}
	}
	else { 
		// Il n'y a qu'une classe ou aucune classe cochée
		$compteur_pages_imprimees=0;
		$compteur=0;
		while (!feof($fp)) {
			$ligne = trim(fgets($fp, 4096));
			if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
				$ligne=substr($ligne,3);
			}

			if($ligne!='') {
				$infos="";
				$tab=explode(";", ensure_utf8($ligne));

				/*
				echo "<pre>";
				print_r($tab);
				echo "</pre>";
				*/

				$afficher="y";
				if((isset($profil))&&(isset($tabindice['profil']))&&(!in_array($tab[$tabindice['profil']], $profil))) {
					$afficher="n";
				}

				if((isset($tabindice['nom enfant']))&&(isset($tabindice['prenom enfant']))&&($tab[$tabindice['classe']]=="")) {
					$sql="SELECT DISTINCT classe FROM eleves e, j_eleves_classes jec, classes c WHERE e.login=jec.login AND jec.id_classe=c.id AND e.nom='".mysqli_real_escape_string($GLOBALS['mysqli'], $tab[$tabindice['nom enfant']])."' AND e.prenom='".mysqli_real_escape_string($GLOBALS['mysqli'], $tab[$tabindice['prenom enfant']])."';";
					$res_classe=mysqli_query($GLOBALS['mysqli'], $sql);
					if(mysqli_num_rows($res_classe)==1) {
						$lig_classe=mysqli_fetch_object($res_classe);
						$tab[$tabindice['classe']]=$lig_classe->classe;
					}
				}

				if((isset($classe))&&(isset($tabindice['classe']))&&(!in_array($tab[$tabindice['classe']], $classe))) {
					$afficher="n";
				}

				if(($mot_de_passe_deja_modifie!="y")&&(preg_match("/Mot de passe déjà modifié par utilisateur/", $tab[$tabindice['mot de passe']]))) {
					$afficher="n";
				}

				if(($afficher=="y")&&(isset($tabindice["uid"]))) {
					if($tab[$tabindice['uid']]=="") {
						$afficher="n";
					}
					elseif(in_array($tab[$tabindice['uid']], $tab_deja)) {
						$afficher="n";
					}
					else {
						$sql="SELECT 1=1 FROM sso_table_correspondance WHERE login_sso='".$tab[$tabindice['uid']]."';";
						//echo "$sql<br />";
						$test_sso=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test_sso)==0) {
							$afficher="n";
						}
					}
				}

				if($afficher=="y") {
					$tab_deja[]=$tab[$tabindice['uid']];
					$fb_courante="
	<table style='page-break-inside: avoid; width:30em; font-size:9pt;' class='boireaus'>
		<tr>
			<td style='width:8em;'>À l'attention de </td>
			<td class='bold'>";
				//$fb_courante.=$tab[$tabindice['nom']]." ".$tab[$tabindice['prenom']];
					$fb_courante.="<a href=\"#\" onclick=\"cherche_ele_resp('".preg_replace("/[^A-Za-z]/","%",$tab[$tabindice['nom']])."');return false;\" style='text-decoration:none; color:black;' target='_blank'>".$tab[$tabindice['nom']]." ".$tab[$tabindice['prenom']]."</a>";
					$fb_courante.="</td>
		</tr>";
					if($inclure_url_connexion_ent=="y") {
						$fb_courante.="
			<tr>
				<td>URL </td>
				<td class='bold'>".$url_connexion_ent."</td>
			</tr>";
					}
					$fb_courante.="
		<tr>
			<td>Login </td>
			<td class='bold'>".$tab[$tabindice['login']]."</td>
		</tr>
		<tr>
			<td>Mot de passe </td>
			<td class='bold'>".$tab[$tabindice['mot de passe']]."</td>
		</tr>";

					if((isset($tabindice['nom enfant']))&&($tab[$tabindice['nom enfant']]!='')) {
						$fb_courante.="
		<tr>
			<td>Responsable de </td>
			<td>".$tab[$tabindice['nom enfant']];
						if(isset($tabindice['prenom enfant'])) {
							$fb_courante.=" ".$tab[$tabindice['prenom enfant']];
						}
						$fb_courante.="</td>
		</tr>";
					}

					if(isset($tabindice['classe'])) {
						$fb_courante.="
		<tr>
			<td>Classe de </td>
			<td>".$tab[$tabindice['classe']]."</td>
		</tr>";
					}

				

					$fb_courante.="
	</table>";

					if($fiche_bienvenue=="y") {
						if(isset($tabindice['profil'])) {
							if((mb_strtolower($tab[$tabindice['profil']])=='tuteur')||
							(mb_strtolower($tab[$tabindice['profil']])=='parent')||
							(mb_strtolower($tab[$tabindice['profil']])=='responsable')) {
								$page=$ImpressionFicheParent;
							}
							elseif((mb_strtolower($tab[$tabindice['profil']])=='eleve')||(mb_strtolower($tab[$tabindice['profil']])=='élève')) {
								$page=$ImpressionFicheEleve;
							}
							else {
								$page=$Impression;
							}
						}
						else {
							$page=$Impression;
						}
						$fb_courante.=$page;

						echo $fb_courante;

						if(($compteur_pages_imprimees+1)%$impression_nombre_global==0) {
							echo "<p class='saut'></p>";
						}
					}


					// Mail 20160905
					if($envoi_mail=="y") {
						$tab_param_mail=array();
						$tab_param_mail['destinataire']=array();
						// Récupérer le login_gepi associé à l'uid.
						// Pour un élève, rechercher l'email dans eleves et les email des parents dans utilisateurs et resp_pers
						// Pour un parent, rechercher les email des parents dans utilisateurs et resp_pers

						$sql="SELECT u.statut, u.login, u.email FROM utilisateurs u, sso_table_correspondance stc WHERE u.login=stc.login_gepi AND stc.login_sso='".$tab[$tabindice['uid']]."';";
						//echo "$sql<br />";
						$res_mail=mysqli_query($mysqli, $sql);
						if(mysqli_num_rows($res_mail)>0) {
							$lig_mail=mysqli_fetch_object($res_mail);

							if(check_mail($lig_mail->email)) {
								$tab_param_mail['destinataire'][]=$lig_mail->email;
							}
						
							if($lig_mail->statut=="eleve") {
								$sql="SELECT email FROM eleves WHERE login='".$lig_mail->login."' AND email LIKE '%@%';";
								//echo "$sql<br />";
								$res_mail2=mysqli_query($mysqli, $sql);
								if(mysqli_num_rows($res_mail2)>0) {
									$lig_mail2=mysqli_fetch_object($res_mail2);

									if((check_mail($lig_mail2->email))&&(!in_array($lig_mail2->email, $tab_param_mail['destinataire']))) {
										$tab_param_mail['destinataire'][]=$lig_mail2->email;
									}
								}
								// RECHERCHE DES ADRESSES MAIL DES RESPONSABLES... et envoyer un mail par reponsable? pb des responsables séparés ne voulant pas divulguer leur mail au conjoint?
								$tab_resp=get_resp_from_ele_login($lig_mail->login);
								for($loop_resp=0;$loop_resp<count($tab_resp);$loop_resp++) {
									if((isset($tab_resp[$loop_resp]["mel"]))&&(check_mail($tab_resp[$loop_resp]["mel"]))) {
										$tab_param_mail['destinataire'][]=$tab_resp[$loop_resp]["mel"];
									}
									if((isset($tab_resp[$loop_resp]["email"]))&&(check_mail($tab_resp[$loop_resp]["email"]))) {
										$tab_param_mail['destinataire'][]=$tab_resp[$loop_resp]["email"];
									}
								}
							}
							elseif($lig_mail->statut=="responsable") {
								$sql="SELECT mel AS email FROM resp_pers WHERE login='".$lig_mail->login."' AND mel LIKE '%@%';";
								//echo "$sql<br />";
								$res_mail2=mysqli_query($mysqli, $sql);
								if(mysqli_num_rows($res_mail2)>0) {
									$lig_mail2=mysqli_fetch_object($res_mail2);

									if((check_mail($lig_mail2->email))&&(!in_array($lig_mail2->email, $tab_param_mail['destinataire']))) {
										$tab_param_mail['destinataire'][]=$lig_mail2->email;
									}
								}
							}

							/*$destinataire=$tab_param_mail['destinataire'][0];
							for($loop_mail=1;$loop_mail<count($tab_param_mail['destinataire']);$loop_mail++) {
								$destinataire.=",".$tab_param_mail['destinataire'][$loop_mail];
							}
							if(envoi_mail("Compte Gepi", "Bonjour(soir),\n".$fb_courante, $destinataire, "", "html", $tab_param_mail)) {
								$nb_mail++;
							}
							*/
							// Pour éviter les problèmes d'envoi à plusieurs adresses mail, avec des parents séparés
							if(count($tab_param_mail['destinataire'])>0) {
								for($loop_mail=0;$loop_mail<count($tab_param_mail['destinataire']);$loop_mail++) {
									if($chaine_mail_envoye!="") {
										$chaine_mail_envoye.=", ";
									}

									$destinataire=$tab_param_mail['destinataire'][$loop_mail];
									if(envoi_mail("Compte Gepi", "Bonjour(soir),\n\n".$fb_courante, $destinataire, "", "html", $tab_param_mail)) {
										$chaine_mail_envoye.="<span style='color:green' title=\"Informations de connexion du compte ".$tab[$tabindice['login']]." envoyé avec succès.\">".$destinataire."</span>";
										$nb_mail++;
									}
									else {
										$chaine_mail_envoye.="<span style='color:red' title=\"Échec de l'envoi des informations de connexion du compte ".$tab[$tabindice['login']].".\">".$destinataire."</span>";
									}
								}

								if(($envoi_mail_adresse_specifiee!="")&&($envoi_mail_autre_adresse=="toujours")) {
									//$compilation_fb.="DEBUG: Envoi à l'adresse autre saisie<br />";
									$compilation_fb.=$fb_courante;

									if(($compteur_pages_imprimees+1)%$impression_nombre_global==0) {
										$compilation_fb.="<p class='saut'></p>";
									}
								}
							}
							//elseif(($envoi_mail_adresse_specifiee!="")&&($envoi_mail_autre_adresse=="sinon")) {
							elseif($envoi_mail_adresse_specifiee!="") {
								//$compilation_fb.="DEBUG: Envoi à l'adresse autre saisie<br />";
								$compilation_fb.=$fb_courante;

								if(($compteur_pages_imprimees+1)%$impression_nombre_global==0) {
									$compilation_fb.="<p class='saut'></p>";
								}
							}
						}
						elseif($envoi_mail_adresse_specifiee!="") {
							//$compilation_fb.="DEBUG: Envoi à l'adresse autre saisie<br />";
							$compilation_fb.=$fb_courante;

							if(($compteur_pages_imprimees+1)%$impression_nombre_global==0) {
								$compilation_fb.="<p class='saut'></p>";
							}
						}
					}
					elseif($envoi_mail_adresse_specifiee!="") {
						//$compilation_fb.="DEBUG: Envoi à l'adresse autre saisie<br />";
						$compilation_fb.=$fb_courante;

						if(($compteur_pages_imprimees+1)%$impression_nombre_global==0) {
							$compilation_fb.="<p class='saut'></p>";
						}
					}

					flush();

					$compteur_pages_imprimees++;
					//echo "\$compteur_pages_imprimees=$compteur_pages_imprimees<br />\n";
				}
			}
			$compteur++;
			//echo "\$compteur=$compteur<br />\n";
		}

		if(($compilation_fb!="")&&($envoi_mail_adresse_specifiee!="")&&(check_mail($envoi_mail_adresse_specifiee))) {
			$tab_param_mail=array();
			$tab_param_mail['destinataire']=array();
			$tab_param_mail['destinataire'][]=$envoi_mail_adresse_specifiee;

			$destinataire=$envoi_mail_adresse_specifiee;

			if($chaine_mail_envoye!="") {
				$chaine_mail_envoye.=", ";
			}
			if(envoi_mail("Compte(s) Gepi", "Bonjour(soir),\n\n".$compilation_fb, $destinataire, "", "html", $tab_param_mail)) {
				$chaine_mail_envoye.="<span style='color:green' title=\"Informations de connexion envoyées avec succès.\">".$destinataire."</span>";
			}
			else {
				$chaine_mail_envoye.="<span style='color:red' title=\"Échec de l'envoi des informations de connexion.\">".$destinataire."</span>";
			}
		}

		if($chaine_mail_envoye!="") {
			echo "<div class='noprint'><p><strong>Liste des envois de mail&nbsp;:</strong> $chaine_mail_envoye</p></div>";
		}
	}

	echo "<form action='../eleves/recherche.php' method='post' id='form_rech_ele' target='_blank'>
	".add_token_field()."
	<input type='hidden' name='is_posted_recherche' id='is_posted_recherche' value='y' />
	<input type='hidden' name='statut[0]' id='statut_0' value='eleve' />
	<input type='hidden' name='statut[1]' id='statut_1' value='responsable' />
	<input type='hidden' name='rech_nom' id='rech_nom' value='' />
	<input type='hidden' name='rech_prenom' id='rech_prenom' value='' />
</form>
<script type='text/javascript'>
	function cherche_ele_resp(rech_nom) {
		document.getElementById('rech_nom').value=rech_nom;
		document.getElementById('form_rech_ele').submit();
	}
</script>";

	if($compteur_pages_imprimees==0) {
		echo "<p style='color:red'>Aucune fiche n'a été imprimée.<br />Revoyez les critères ou contrôlez votre fichier.</p>";
	}
}
elseif($mode=="derniers_parents_et_eleves_inscrits") {

	echo "<h2>Dernières correspondances inscrites</h2>";

	$sql="SELECT * FROM tempo2_sso ts, sso_table_import sti WHERE ts.col2=sti.uid;";
	//echo "$sql<br />";
	$res_ts=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ts)==0) {
		echo "<p style='color:red'>Aucune correspondance n'est conservée dans la table 'tempo2_sso'.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$tab_classe=array();
	$tab_profil=array();
	$compteur=0;
	while($lig_ts=mysqli_fetch_object($res_ts)) {
		if(!in_array($lig_ts->classe, $tab_classe)) {
			$tab_classe[]=$lig_ts->classe;
		}

		if(!in_array($lig_ts->statut, $tab_profil)) {
			$tab_profil[]=$lig_ts->statut;
		}
	}


	sort($tab_classe);
	sort($tab_profil);

	$url_connexion_ent=getSettingValue("url_connexion_ent");
	if(getSettingAOui("inclure_url_connexion_ent")) {
		$checked_inclure_url_connexion_ent=" checked";
		$style_inclure_url_connexion_ent=" style='font-weight:bold;'";
	}
	else {
		$checked_inclure_url_connexion_ent="";
		$style_inclure_url_connexion_ent="";
	}
	echo "<form action='publipostage.php' enctype='multipart/form-data' method='post' target='_blank'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<h2>Publipostage</h2>
		<div style='margin-left:3em;'>
		<input type='hidden' name='mode' value='publiposter2' />
		<p style='text-indent:-3em;margin-left:3em;'>Paramètres du publipostage&nbsp;:<br />
		<input type='checkbox' name='inclure_url_connexion_ent' id='inclure_url_connexion_ent' value='y' onchange=\"checkbox_change(this.id)\"$checked_inclure_url_connexion_ent /><label for='inclure_url_connexion_ent' id='texte_inclure_url_connexion_ent'$style_inclure_url_connexion_ent> Inclure l'URL de connexion <input type='text' name='url_connexion_ent' value='".$url_connexion_ent."' /> dans le tableau des informations Login, mot de passe.</label><br />
		<input type='checkbox' name='fiche_bienvenue' id='fiche_bienvenue' value='y' onchange=\"checkbox_change(this.id)\" /><label for='fiche_bienvenue' id='texte_fiche_bienvenue'> Inclure la fiche bienvenue sous les informations Login, mot de passe.</label><br />
		<input type='checkbox' name='mot_de_passe_deja_modifie' id='mot_de_passe_deja_modifie' value='n' onchange=\"checkbox_change(this.id)\" /><label for='mot_de_passe_deja_modifie' id='texte_mot_de_passe_deja_modifie'> Ne pas imprimer les fiches pour lesquelles le mot de passe a déjà été modifié.</label><br />
		<em style='font-size:x-small'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(la chaine testée est '<span style='color:green'>Mot de passe déjà modifié par utilisateur</span>')</em></p>";

	if(count($tab_profil)>0) {
		echo "
		<p style='text-indent:-3em;margin-left:3em;'>Profils&nbsp;:";
		for($loop=0;$loop<count($tab_profil);$loop++) {
			echo "<br />
			<input type='checkbox' name='profil[]' id='profil_$loop' value=\"".$tab_profil[$loop]."\" onchange=\"checkbox_change(this.id)\" /><label for='profil_$loop' id='texte_profil_$loop'>".$tab_profil[$loop]."</label>";
		}
		echo "
		</p>";
	}

	if(count($tab_classe)>0) {
		echo "
		<p style='text-indent:-3em;margin-left:3em;'>Classes&nbsp;:";
		for($loop=0;$loop<count($tab_classe);$loop++) {
			$info_classe=$tab_classe[$loop];
			if($info_classe=="") {
				$info_classe="<span style='color:red'>Aucune classe (<em>parents ou élèves de l'année passée?</em>)</span>";
			}
			echo "<br />
			<input type='checkbox' name='classe[]' id='classe_$loop' value=\"".$tab_classe[$loop]."\" onchange=\"checkbox_change(this.id)\" /><label for='classe_$loop' id='texte_classe_$loop'>".$info_classe."</label>";
		}
	echo "
			<br />
			<a href='javascript:cocher_toutes_classes($loop)'>Cocher toutes les classes</a> / 
			<a href='javascript:decocher_toutes_classes($loop)'>Décocher toutes les classes</a>
		</p>";
	}

	echo "
		<p><input type='submit' value='Valider' /></p>

		<p style='margin-top:1em;'>
			<a href='javascript:cocher_tous_checkbox()'>Tout cocher</a> / 
			<a href='javascript:decocher_tous_checkbox()'>Tout décocher</a>
		</p>

		</div>
	</fieldset>
</form>

<p><em>NOTES&nbsp;:</em></p>
<ul>
	<li>
		<p>Si vous sélectionnez des classes, les fiches seront triées par classe, puis par ordre alphabétique des noms et prénoms.<br />
		Sinon, toutes les lignes du fichier fourni seront parcourues et traitées/affichées dans l'ordre du fichier.</p>
	</li>
	<li>
		<p>Si vous effectuez le publipostage pour plusieurs profils différents sans fiche bienvenue insérée, la découpe risque de ne pas être pratique.<br />
		Pour les élèves, il y a en effet 4 lignes, contre 5 pour les responsables.<br />
		Il vaut mieux alors imprimer les fiches pour les élèves dans un premier temps et pour les responsables dans un deuxième temps.</p>
		<p>Avec fiche bienvenue en revanche, un saut de page est inséré si bien que le problème ci-dessus ne se présente plus.</p>
	</li>";

	if(acces("/gestion/modify_impression.php", $_SESSION['statut'])) {
		echo "
	<li>
		<p><a href='../gestion/modify_impression.php' target='_blank'>Contrôler/Modifier les fiches bienvenue</a>.</p>
	</li>";
	}

	echo "
</ul>

<script type='text/javascript'>
	function cocher_toutes_classes(num) {
		for(i=0;i<num;i++) {
			if(document.getElementById('classe_'+i)) {
				document.getElementById('classe_'+i).checked=true;
				checkbox_change('classe_'+i);
			}
		}
	}

	function decocher_toutes_classes(num) {
		for(i=0;i<num;i++) {
			if(document.getElementById('classe_'+i)) {
				document.getElementById('classe_'+i).checked=false;
				checkbox_change('classe_'+i);
			}
		}
	}

	function cocher_tous_checkbox() {
		champs_input=document.getElementsByTagName('input');
		for(i=0;i<champs_input.length;i++){
			id_champ=champs_input[i].getAttribute('id');
			type=champs_input[i].getAttribute('type');
			if(type==\"checkbox\"){
				champs_input[i].checked=true;
				checkbox_change(id_champ);
			}
		}
	}

	function decocher_tous_checkbox() {
		champs_input=document.getElementsByTagName('input');
		for(i=0;i<champs_input.length;i++){
			id_champ=champs_input[i].getAttribute('id');
			type=champs_input[i].getAttribute('type');
			if(type==\"checkbox\"){
				champs_input[i].checked=false;
				checkbox_change(id_champ);
			}
		}
	}

	".js_checkbox_change_style()."
</script>";

}
elseif($mode=="publiposter2") {

	//debug_var();

	if(isset($_POST['inclure_url_connexion_ent'])) {
		saveSetting('inclure_url_connexion_ent', 'y');
		$inclure_url_connexion_ent="y";
	}
	else {
		saveSetting('inclure_url_connexion_ent', 'n');
		$inclure_url_connexion_ent="n";
	}

	if(isset($_POST['url_connexion_ent'])) {
		saveSetting('url_connexion_ent', $_POST['url_connexion_ent']);
	}
	$url_connexion_ent=getSettingValue('url_connexion_ent');

	$fiche_bienvenue=isset($_POST['fiche_bienvenue']) ? $_POST['fiche_bienvenue'] : "n";
	$mot_de_passe_deja_modifie=isset($_POST['mot_de_passe_deja_modifie']) ? $_POST['mot_de_passe_deja_modifie'] : "y";
	$classe=isset($_POST['classe']) ? $_POST['classe'] : NULL;
	$profil=isset($_POST['profil']) ? $_POST['profil'] : NULL;

	$Impression=getSettingValue('Impression');
	$ImpressionFicheParent=getSettingValue('ImpressionFicheParent');
	$ImpressionFicheEleve=getSettingValue('ImpressionFicheEleve');

	$ImpressionNombre=getSettingValue('ImpressionNombre');
	$ImpressionNombreParent=getSettingValue('ImpressionNombreParent');
	$ImpressionNombreEleve=getSettingValue('ImpressionNombreEleve');
	//echo "\$ImpressionNombreEleve=$ImpressionNombreEleve<br />";

	$impression_nombre_global=100;
	if(isset($profil)) {
		for($loop=0;$loop<count($profil);$loop++) {
			//echo "\$profil[$loop]=$profil[$loop]<br />";
			if(((mb_strtolower($profil[$loop])=="parent")||(mb_strtolower($profil[$loop])=="tuteur")||(mb_strtolower($profil[$loop])=="responsable"))&&($ImpressionNombreParent<$impression_nombre_global)) {
				$impression_nombre_global=$ImpressionNombreParent;
			}
			elseif((mb_strtolower($profil[$loop])=="eleve")&&($ImpressionNombreEleve<$impression_nombre_global)) {
				$impression_nombre_global=$ImpressionNombreEleve;
			}
			//echo "\$impression_nombre_global=$impression_nombre_global<br />";
		}
	}

	if($impression_nombre_global==100) {
		$impression_nombre_global=1;
	}
	elseif($impression_nombre_global<=0) {
		$impression_nombre_global=1;
	}

	$compteur_pages_imprimees=0;
	// Pour ne pas imprimer deux fiches pour un parent de 2 enfants.
	$tab_deja=array();
	if((isset($classe))&&(count($classe)>1)) {
		$compteur=0;
		$compteur_pages_imprimees=0;

		for($loop=0;$loop<count($classe);$loop++) {
			$tab_cle_courante=array();
			$tab_courant=array();

			$sql="SELECT * FROM tempo2_sso ts, sso_table_import sti WHERE ts.col2=sti.uid AND sti.classe='".$classe[$loop]."';";
			$res_ts=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_ts)>0) {
				while($lig_ts=mysqli_fetch_assoc($res_ts)) {
					$afficher="y";
					if((isset($profil))&&(!in_array($lig_ts['statut'], $profil))) {
						$afficher="n";
					}
					elseif(in_array($lig_ts['uid'], $tab_deja)) {
						$afficher="n";
					}
					else {
						$sql="SELECT 1=1 FROM sso_table_correspondance WHERE login_sso='".$lig_ts['uid']."';";
						//echo "$sql<br />";
						$test_sso=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test_sso)==0) {
							$afficher="n";
						}
						else {
							$tmp_tab=explode("|", $lig_ts['col1']);
							if(($mot_de_passe_deja_modifie!="y")&&(preg_match("/Mot de passe déjà modifié par utilisateur/", $tmp_tab[2]))) {
								$afficher="n";
							}
						}
					}

					if($afficher=="y") {
						$tab_deja[]=$lig_ts['uid'];

						$tab_cle_courante[]=$lig_ts['nom']." ".$lig_ts['prenom'];
						$tab_courant[$lig_ts['nom']." ".$lig_ts['prenom']]=$lig_ts;

						$tmp_tab=explode("|", $lig_ts['col1']);
						$tab_courant[$lig_ts['nom']." ".$lig_ts['prenom']]['login_ent']=$tmp_tab[1];
						$tab_courant[$lig_ts['nom']." ".$lig_ts['prenom']]['pass_ent']=$tmp_tab[2];

						if((mb_strtolower($lig_ts['statut'])=='tuteur')||
						(mb_strtolower($lig_ts['statut'])=='parent')||
						(mb_strtolower($lig_ts['statut'])=='responsable')) {
							$sql="SELECT * FROM sso_table_import WHERE (pere='".$lig_ts['uid']."' OR mere='".$lig_ts['uid']."' OR  tuteur1='".$lig_ts['uid']."' OR tuteur2='".$lig_ts['uid']."') AND classe='".$classe[$loop]."';";
							$test_enfant=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test_enfant)>0) {
								$lig_enfant=mysqli_fetch_object($test_enfant);
								$chaine_tmp=$lig_enfant->nom." ".$lig_enfant->prenom;
								while($lig_enfant=mysqli_fetch_object($test_enfant)) {
									$chaine_tmp.=", ".$lig_enfant->nom." ".$lig_enfant->prenom;
								}
								$tab_courant[$lig_ts['nom']." ".$lig_ts['prenom']]['enfant']=$chaine_tmp;
							}
						}

					}
					$compteur++;
				}
			}

			sort($tab_cle_courante);
			for($loop_cle=0;$loop_cle<count($tab_cle_courante);$loop_cle++) {
				$tab=$tab_courant[$tab_cle_courante[$loop_cle]];
				echo "
		<table style='page-break-inside: avoid; width:30em;' class='boireaus'>
			<tr>
				<td style='width:8em;'>À l'attention de </td>
				<td class='bold'>";
				echo "<a href=\"#\" onclick=\"cherche_ele_resp('".preg_replace("/[^A-Za-z]/","%",$tab['nom'])."');return false;\" style='text-decoration:none; color:black;' target='_blank'>".$tab['nom']." ".$tab['prenom']."</a>";
				echo "</td>
			</tr>";
				if($inclure_url_connexion_ent=="y") {
					echo "
			<tr>
				<td>URL </td>
				<td class='bold'>".$url_connexion_ent."</td>
			</tr>";
				}
				echo "
			<tr>
				<td>Login </td>
				<td class='bold'>".$tab['login_ent']."</td>
			</tr>
			<tr>
				<td>Mot de passe </td>
				<td class='bold'>".$tab['pass_ent']."</td>
			</tr>";

				if((isset($tab['enfant']))&&($tab['enfant']!='')) {
					echo "
			<tr>
				<td>Responsable de </td>
				<td>".$tab['enfant']."</td>
			</tr>";
				}

				if(isset($tab['classe'])) {
					echo "
			<tr>
				<td>Classe de </td>
				<td>".$tab['classe']."</td>
			</tr>";
				}

				echo "
		</table>";

				if($fiche_bienvenue=="y") {
					if((mb_strtolower($tab['statut'])=='tuteur')||
					(mb_strtolower($tab['statut'])=='parent')||
					(mb_strtolower($tab['statut'])=='responsable')) {
						$page=$ImpressionFicheParent;
					}
					elseif((mb_strtolower($tab['statut'])=='eleve')||(mb_strtolower($tab['statut'])=='élève')) {
						$page=$ImpressionFicheEleve;
					}
					else {
						$page=$Impression;
					}

					echo $page;

					if(($compteur_pages_imprimees+1)%$impression_nombre_global==0) {
						echo "<p class='saut'></p>";
					}
				}
				else {
					echo "<p>&nbsp;</p>";
				}
				$compteur_pages_imprimees++;
			}
		}
	}
	else {
		$compteur_pages_imprimees=0;
		$compteur=0;

		$sql="SELECT * FROM tempo2_sso ts, sso_table_import sti WHERE ts.col2=sti.uid ORDER BY sti.classe, sti.nom, sti.prenom;";
		$res_ts=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ts)>0) {
			while($lig_ts=mysqli_fetch_assoc($res_ts)) {
				$afficher="y";
				if((isset($profil))&&(!in_array($lig_ts['statut'], $profil))) {
					$afficher="n";
				}
				elseif(in_array($lig_ts['uid'], $tab_deja)) {
					$afficher="n";
				}
				else {
					$sql="SELECT 1=1 FROM sso_table_correspondance WHERE login_sso='".$lig_ts['uid']."';";
					//echo "$sql<br />";
					$test_sso=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test_sso)==0) {
						$afficher="n";
					}
					else {
						$tmp_tab=explode("|", $lig_ts['col1']);
						if(($mot_de_passe_deja_modifie!="y")&&(preg_match("/Mot de passe déjà modifié par utilisateur/", $tmp_tab[2]))) {
							$afficher="n";
						}
					}
				}

				if($afficher=="y") {
					$tab_deja[]=$lig_ts['uid'];

					$tab_cle_courante[]=$lig_ts['nom']." ".$lig_ts['prenom'];
					$tab_courant[$lig_ts['nom']." ".$lig_ts['prenom']]=$lig_ts;

					$tmp_tab=explode("|", $lig_ts['col1']);
					$tab_courant[$lig_ts['nom']." ".$lig_ts['prenom']]['login_ent']=$tmp_tab[1];
					$tab_courant[$lig_ts['nom']." ".$lig_ts['prenom']]['pass_ent']=$tmp_tab[2];

					if((mb_strtolower($lig_ts['statut'])=='tuteur')||
					(mb_strtolower($lig_ts['statut'])=='parent')||
					(mb_strtolower($lig_ts['statut'])=='responsable')) {
						$sql="SELECT * FROM sso_table_import WHERE (pere='".$lig_ts['uid']."' OR mere='".$lig_ts['uid']."' OR  tuteur1='".$lig_ts['uid']."' OR tuteur2='".$lig_ts['uid']."');";
						//echo "$sql<br />";
						$test_enfant=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test_enfant)>0) {
							$lig_enfant=mysqli_fetch_object($test_enfant);
							$chaine_tmp=$lig_enfant->nom." ".$lig_enfant->prenom;
							while($lig_enfant=mysqli_fetch_object($test_enfant)) {
								$chaine_tmp.=", ".$lig_enfant->nom." ".$lig_enfant->prenom;
							}
							$tab_courant[$lig_ts['nom']." ".$lig_ts['prenom']]['enfant']=$chaine_tmp;
						}
					}
				}
				$compteur++;
			}

			for($loop_cle=0;$loop_cle<count($tab_cle_courante);$loop_cle++) {
				$tab=$tab_courant[$tab_cle_courante[$loop_cle]];
				echo "
		<table style='page-break-inside: avoid; width:30em;' class='boireaus'>
			<tr>
				<td style='width:8em;'>À l'attention de </td>
				<td class='bold'>";
				echo "<a href=\"#\" onclick=\"cherche_ele_resp('".preg_replace("/[^A-Za-z]/","%",$tab['nom'])."');return false;\" style='text-decoration:none; color:black;' target='_blank'>".$tab['nom']." ".$tab['prenom']."</a>";
				echo "</td>
			</tr>";
				if($inclure_url_connexion_ent=="y") {
					echo "
			<tr>
				<td>URL </td>
				<td class='bold'>".$url_connexion_ent."</td>
			</tr>";
				}
				echo "
			<tr>
				<td>Login </td>
				<td class='bold'>".$tab['login_ent']."</td>
			</tr>
			<tr>
				<td>Mot de passe </td>
				<td class='bold'>".$tab['pass_ent']."</td>
			</tr>";

				if((isset($tab['enfant']))&&($tab['enfant']!='')) {
					echo "
			<tr>
				<td>Responsable de </td>
				<td>".$tab['enfant']."</td>
			</tr>";
				}

				if(isset($tab['classe'])) {
					echo "
			<tr>
				<td>Classe de </td>
				<td>".$tab['classe']."</td>
			</tr>";
				}

				echo "
		</table>";

				if($fiche_bienvenue=="y") {
					if((mb_strtolower($tab['statut'])=='tuteur')||
					(mb_strtolower($tab['statut'])=='parent')||
					(mb_strtolower($tab['statut'])=='responsable')) {
						$page=$ImpressionFicheParent;
					}
					elseif((mb_strtolower($tab['statut'])=='eleve')||(mb_strtolower($tab['statut'])=='élève')) {
						$page=$ImpressionFicheEleve;
					}
					else {
						$page=$Impression;
					}

					echo $page;

					if(($compteur_pages_imprimees+1)%$impression_nombre_global==0) {
						echo "<p class='saut'></p>";
					}
				}
				else {
					echo "<p>&nbsp;</p>";
				}
				$compteur_pages_imprimees++;
			}

		}

	}

	echo "<form action='../eleves/recherche.php' method='post' id='form_rech_ele' target='_blank'>
	".add_token_field()."
	<input type='hidden' name='is_posted_recherche' id='is_posted_recherche' value='y' />
	<input type='hidden' name='statut[0]' id='statut_0' value='eleve' />
	<input type='hidden' name='statut[1]' id='statut_1' value='responsable' />
	<input type='hidden' name='rech_nom' id='rech_nom' value='' />
	<input type='hidden' name='rech_prenom' id='rech_prenom' value='' />
</form>
<script type='text/javascript'>
	function cherche_ele_resp(rech_nom) {
		document.getElementById('rech_nom').value=rech_nom;
		document.getElementById('form_rech_ele').submit();
	}
</script>";

	if($compteur_pages_imprimees==0) {
		echo "<p style='color:red'>Aucune fiche n'a été imprimée.<br />Revoyez les critères ou contrôlez votre fichier.</p>";
	}

}
elseif($mode=='enregistrer_correspondances') {
	echo "<h2>Fin du traitement demandé</h2>
<p>Bonne continuation.</p>";
}
else {
	echo "<p style='color:red'>Mode invalide.</p>";
}

require("../lib/footer.inc.php");
?>
