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
if($mode!="publiposter") {
	$titre_page = "SSO : Publipostage";
}
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

if($mode!='publiposter') {
	echo "<p class='bold'><a href='./index.php?ctrl=cvsent'>Retour</a></p>";
}

if($mode=="upload") {

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

				echo "<form action='publipostage.php' enctype='multipart/form-data' method='post' target='_blank'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<h2>Publipostage</h2>
		<div style='margin-left:3em;'>
		<input type='hidden' name='mode' value='publiposter' />
		<p style='text-indent:-3em;margin-left:3em;'>Paramètres du publipostage&nbsp;:<br />
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
						echo "<br />
			<input type='checkbox' name='classe[]' id='classe_$loop' value=\"".$tab_classe[$loop]."\" onchange=\"checkbox_change(this.id)\" /><label for='classe_$loop' id='texte_classe_$loop'>".$tab_classe[$loop]."</label>";
					}
				echo "
			<br />
			<a href='javascript:cocher_toutes_classes($loop)'>Cocher toutes les classes</a> / 
			<a href='javascript:decocher_toutes_classes($loop)'>Décocher toutes les classes</a>
		</p>";
				}

				echo "
		<p><input type='submit' value='Valider' /></p>
		</div>
	</fieldset>
</form>

<p><em>NOTES&nbsp;:</em></p>
<ul>
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

	".js_checkbox_change_style()."
</script>";

			}
		}
	}
}
elseif($mode=='publiposter') {

	//debug_var();

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
		echo "<p><a href='index.php'>Retour</a></p>";
		require("../lib/footer.inc.php");
		die();
	}

	$fiche_bienvenue=isset($_POST['fiche_bienvenue']) ? $_POST['fiche_bienvenue'] : "n";
	$mot_de_passe_deja_modifie=isset($_POST['mot_de_passe_deja_modifie']) ? $_POST['mot_de_passe_deja_modifie'] : "y";
	$classe=isset($_POST['classe']) ? $_POST['classe'] : NULL;
	$profil=isset($_POST['profil']) ? $_POST['profil'] : NULL;

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

	if((isset($classe))&&(count($classe)>1)) {
		$compteur=0;
		$compteur_pages_imprimees=0;

		for($loop=0;$loop<count($classe);$loop++) {
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

					if($tab[$tabindice['classe']]!=$classe[$loop]) {
						$afficher="n";
					}

					if(($mot_de_passe_deja_modifie!="y")&&(preg_match("/Mot de passe déjà modifié par utilisateur/i", $tab[$tabindice['mot de passe']]))) {
						$afficher="n";
					}

					if(($afficher=="y")&&(isset($tabindice["uid"]))) {
						if($tab[$tabindice['uid']]=="") {
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
						echo "
		<table style='page-break-inside: avoid; width:30em;' class='boireaus'>
			<tr>
				<td style='width:8em;'>À l'attention de </td>
				<td class='bold'>".$tab[$tabindice['nom']]." ".$tab[$tabindice['prenom']]."</td>
			</tr>
			<tr>
				<td>Login </td>
				<td class='bold'>".$tab[$tabindice['login']]."</td>
			</tr>
			<tr>
				<td>Mot de passe </td>
				<td class='bold'>".$tab[$tabindice['mot de passe']]."</td>
			</tr>";

						if((isset($tabindice['nom enfant']))&&($tab[$tabindice['nom enfant']]!='')) {
							echo "
			<tr>
				<td>Responsable de </td>
				<td>".$tab[$tabindice['nom enfant']];
							if(isset($tabindice['prenom enfant'])) {
								echo " ".$tab[$tabindice['prenom enfant']];
							}
							echo "</td>
			</tr>";
						}

						if(isset($tabindice['classe'])) {
							echo "
			<tr>
				<td>Classe de </td>
				<td>".$tab[$tabindice['classe']]."</td>
			</tr>";
						}

				

						echo "
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
				$compteur++;
			}
		}
	}
	else {
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
					echo "
	<table style='page-break-inside: avoid; width:30em;' class='boireaus'>
		<tr>
			<td style='width:8em;'>À l'attention de </td>
			<td class='bold'>".$tab[$tabindice['nom']]." ".$tab[$tabindice['prenom']]."</td>
		</tr>
		<tr>
			<td>Login </td>
			<td class='bold'>".$tab[$tabindice['login']]."</td>
		</tr>
		<tr>
			<td>Mot de passe </td>
			<td class='bold'>".$tab[$tabindice['mot de passe']]."</td>
		</tr>";

					if((isset($tabindice['nom enfant']))&&($tab[$tabindice['nom enfant']]!='')) {
						echo "
		<tr>
			<td>Responsable de </td>
			<td>".$tab[$tabindice['nom enfant']];
						if(isset($tabindice['prenom enfant'])) {
							echo " ".$tab[$tabindice['prenom enfant']];
						}
						echo "</td>
		</tr>";
					}

					if(isset($tabindice['classe'])) {
						echo "
		<tr>
			<td>Classe de </td>
			<td>".$tab[$tabindice['classe']]."</td>
		</tr>";
					}

				

					echo "
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
						echo $page;

						if(($compteur_pages_imprimees+1)%$impression_nombre_global==0) {
							echo "<p class='saut'></p>";
						}
					}

					$compteur_pages_imprimees++;
				}
			}
			$compteur++;
		}
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
