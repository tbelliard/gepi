<?php
/*
 *
 * Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_notes/utilisateurs/modif_par_lots.php';";
$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/utilisateurs/modif_par_lots.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F','F', 'Personnels : Traitements par lots', '1');";
	$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";

// Tableau des valeurs prises en compte:
$tab_auth_mode=array('gepi', 'ldap', 'sso');
$tab_auth_mode_texte=array('Locale (base Gepi)', 'LDAP', 'SSO (Cas, LCS, LemonLDAP)');

$tab_statuts=array('professeur', 'scolarite', 'cpe', 'secours');

$tab_etat=array('actif', 'inactif');

// Types à afficher:
$aff_etat=isset($_POST['aff_etat']) ? $_POST['aff_etat'] : array("actif");
$aff_statut=isset($_POST['aff_statut']) ? $_POST['aff_statut'] : $tab_statuts;


if(isset($_POST['action'])) {
	check_token();

	$u_login=isset($_POST['u_login']) ? $_POST['u_login'] : array();

	$cpt_reg=0;
	$cpt_err=0;
	if(($_POST['action']=="etat")&&(isset($_POST['etat']))&&(in_array($_POST['etat'], $tab_etat))) {
		for($loop=0;$loop<count($u_login);$loop++) {
			$sql="UPDATE utilisateurs SET etat='".$_POST['etat']."' WHERE login='".$u_login[$loop]."';";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if($update) {
				$cpt_reg++;
			}
			else {
				$msg.="Erreur lors du passage à l'état ".$_POST['etat']." de ".$u_login[$loop]."<br />\n";
				$cpt_err++;
			}
		}
		if($cpt_reg>0) {$msg.=$cpt_reg." compte(s) rendus ".$_POST['etat']."<br />";}
	}
	elseif(($_POST['action']=="auth_mode")&&(isset($_POST['auth_mode']))&&(in_array($_POST['auth_mode'], $tab_auth_mode))) {
		for($loop=0;$loop<count($u_login);$loop++) {
			$chaine_vidage_mdp="";
			if((($_POST['auth_mode']=="ldap")||($_POST['auth_mode']=="sso"))&&
			(!getSettingAOui('auth_sso_ne_pas_vider_MDP_gepi'))) {
				$sql="SELECT auth_mode FROM utilisateurs WHERE login='".$u_login[$loop]."';";
				$res_old_auth_mode=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_old_auth_mode)>0) {
					$lig_old_auth_mode=mysqli_fetch_object($res_old_auth_mode);
					if($lig_old_auth_mode->auth_mode=="gepi") {
						$chaine_vidage_mdp=", password='', salt='', change_mdp='n' ";
					}
				}
			}
			$sql="UPDATE utilisateurs SET auth_mode='".$_POST['auth_mode']."' $chaine_vidage_mdp WHERE login='".$u_login[$loop]."';";
			//echo "$sql<br />";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if($update) {
				$cpt_reg++;
			}
			else {
				$msg.="Erreur lors du passage à l'auth_mode ".$_POST['auth_mode']." de ".$u_login[$loop]."<br />\n";
				$cpt_err++;
			}
		}
		if($cpt_reg>0) {$msg.=$cpt_reg." compte(s) passés au mode d'authentification ".$_POST['auth_mode']."<br />";}
	}
	elseif($_POST['action']=="reset_passwords") {

		//header("Location:reset_passwords.php?u_login=$u_login&mode_impression=html".add_token_in_url(false));
		//die();

		// Remplir une chaine avec formulaire pour provoquer un POST dans un autre onglet en présentant ça comme une demande de confirmation
		// Faire pareil pour la suppression de comptes
		$chaine_form_confirm="
<form action='reset_passwords.php' method='post' id='form_aff' target='_blank'>
	<fieldset style='border:1px solid white; background-image: url(\"../images/background/opacite50.png\");'>
		<p style='color:red'>Confirmation requise&nbsp;:</p>
		<p>Vous avez demandé à réinitialiser les mots de passe pour&nbsp;:<br />
		<input type='hidden' name='csrf_alea' value='".$_POST['csrf_alea']."' />
		<input type='hidden' name='mode_impression' value='html' />
";
		for($loop=0;$loop<count($u_login);$loop++) {
			if($loop>0) {$chaine_form_confirm.=", ";}
			$chaine_form_confirm.="		<input type='hidden' name='u_login[]' value='".$u_login[$loop]."' />\n";
			$chaine_form_confirm.=civ_nom_prenom($u_login[$loop]);
		}
		$chaine_form_confirm.="
		</p>

		<p><input type='checkbox' name='envoi_mail' id='envoi_mail' value='y' /><label for='envoi_mail'> Envoyer la fiche bienvenue par mail si le mail est renseigné</label></p>

		<p><input type='submit' value='Confirmer cette opération' /></p>
	</fieldset>
</form>
<br />";
	}

	// https://127.0.0.1/steph/gepi_git_trunk/lib/confirm_query.php?liste_cible=autretc&action=del_utilisateur&chemin_retour=%2Fsteph%2Fgepi_git_trunk%2Futilisateurs%2Findex.php%3Fmode%3Dpersonnels&csrf_alea=NDt97L9MTpx603Ntq1Zxq611eSNNEz6pA9ZU9cX5

}

if(isset($_POST['enregistrer_modifs_xml'])) {
	check_token();

	$cpt=isset($_POST['cpt']) ? $_POST['cpt'] : NULL;
	$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : array();
	$numind=isset($_POST['numind']) ? $_POST['numind'] : array();
	$type=isset($_POST['type']) ? $_POST['type'] : array();

	$cpt_reg=0;
	$cpt_err=0;
	if((isset($cpt))&&(preg_match("/^[0-9]{1,}$/", $cpt))) {
		for($loop=0;$loop<count($login_user);$loop++) {
			$sql="SELECT * FROM utilisateurs WHERE login='".mysqli_real_escape_string($mysqli, $login_user[$loop])."';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				$msg.="Le compte ".$login_user[$loop]." n'existe pas.<br />";
			}
			else {
				$lig=mysqli_fetch_object($res);
				if(!in_array($lig->statut, array("professeur", "cpe", "scolarite"))) {
					$msg.="Le compte ".$login_user[$loop]." n'est pas un compte professeur, cpe ou scolarité.<br />";
				}
				else {
					if(($lig->statut=="professeur")&&($numind[$loop]!="")&&(mb_substr($numind[$loop], 0, 1)!="P")) {
						$numind[$loop]="P".$numind[$loop];
					}
					if(($numind[$loop]!=$lig->numind)||($type[$loop]!=$lig->type)) {
						$sql="UPDATE utilisateurs SET numind='".mysqli_real_escape_string($mysqli, $numind[$loop])."', type='".mysqli_real_escape_string($mysqli, $type[$loop])."' WHERE login='".mysqli_real_escape_string($mysqli, $login_user[$loop])."'";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if($update) {
							$cpt_reg++;
						}
						else {
							$msg.="Erreur lors de la mise à jour des informations STS pour ".civ_nom_prenom($login_user[$loop])."<br />\n";
							$cpt_err++;
						}
					}
				}
			}
		}
	}
	$msg.=$cpt_reg." compte(s) mis à jour.<br />";
	$msg.=$cpt_err." erreur(s).<br />";
}

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************************
$titre_page = "Personnels : Traitements par lots";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************************

//debug_var();

echo "<p class=\"bold\">
<a href='index.php?mode=personnels' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if(isset($_POST['upload_sts_file'])) {
	echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Modifications/traitements par lots</a></p>";
	$debug_import="n";

	$post_max_size=ini_get('post_max_size');
	$upload_max_filesize=ini_get('upload_max_filesize');
	$max_execution_time=ini_get('max_execution_time');
	$memory_limit=ini_get('memory_limit');

	$xml_file=isset($_FILES["xml_file"]) ? $_FILES["xml_file"] : NULL;

	if(!is_uploaded_file($xml_file['tmp_name'])) {
		echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

		echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
		echo "post_max_size=$post_max_size<br />\n";
		echo "upload_max_filesize=$upload_max_filesize<br />\n";
		echo "</p>\n";

		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
		require("../lib/footer.inc.php");
		die();
	}
	else{
		if(!file_exists($xml_file['tmp_name'])){
			echo "<p style='color:red;'>Le fichier aurait été uploadé... mais ne serait pas présent/conservé.</p>\n";

			echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
			echo "post_max_size=$post_max_size<br />\n";
			echo "upload_max_filesize=$upload_max_filesize<br />\n";
			echo "et le volume de ".$xml_file['name']." serait<br />\n";
			echo "\$xml_file['size']=".volume_human($xml_file['size'])."<br />\n";
			echo "</p>\n";

			echo "<p>Il semblerait que l'absence d'extension .XML puisse aussi provoquer ce genre de symptômes.<br />Dans ce cas, ajoutez l'extension et ré-essayez.</p>\n";

			// Il ne faut pas aller plus loin...
			// SITUATION A GERER
			require("../lib/footer.inc.php");
			die();
		}

		echo "<p>Le fichier a été uploadé.</p>\n";

		$tempdir=get_user_temp_directory();
		if(!$tempdir){
			echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
		}

		//$source_file=stripslashes($xml_file['tmp_name']);
		$source_file=$xml_file['tmp_name'];
		$dest_file="../temp/".$tempdir."/sts.xml";
		$res_copy=copy("$source_file" , "$dest_file");

		if(!$res_copy){
			echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
			// Il ne faut pas aller plus loin...
			// SITUATION A GERER
			require("../lib/footer.inc.php");
			die();
		}
		else{
			echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

			$sts_xml=simplexml_load_file($dest_file);
			if(!$sts_xml) {
				echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			$nom_racine=$sts_xml->getName();
			if(my_strtoupper($nom_racine)!='STS_EDT') {
				echo "<p style='color:red;'><b>ERREUR&nbsp;:</b> Le fichier XML fourni n'a pas l'air d'être un fichier XML STS_EMP_&lt;RNE&gt;_&lt;ANNEE&gt;.<br />Sa racine devrait être 'STS_EDT'.</p>\n";

				if(my_strtoupper($nom_racine)=='EDT_STS') {
					echo "<p style='color:red;'>Vous vous êtes trompé d'export.<br />Vous avez probablement utilisé un export de votre logiciel EDT d'Index Education, au lieu de l'export XML provenant de STS.</p>\n";
				}

				require("../lib/footer.inc.php");
				die();
			}

			echo "Analyse du fichier pour extraire les informations...<br />\n";

			$tab_champs_personnels=array("NOM_USAGE",
			"NOM_PATRONYMIQUE",
			"PRENOM",
			"SEXE",
			"CIVILITE",
			"DATE_NAISSANCE",
			"GRADE",
			"FONCTION");

			$prof=array();
			$prof2=array();
			$i=0;

			foreach($sts_xml->DONNEES->INDIVIDUS->children() as $individu) {
				$prof[$i]=array();

				//echo "<span style='color:orange'>\$individu->NOM_USAGE=".$individu->NOM_USAGE."</span><br />";

				foreach($individu->attributes() as $key => $value) {
					// <INDIVIDU ID="4189" TYPE="epp">
					$prof[$i][my_strtolower($key)]=trim($value);
				}

				// Champs de l'individu
				foreach($individu->children() as $key => $value) {
					if(in_array(my_strtoupper($key),$tab_champs_personnels)) {
						if(my_strtoupper($key)=='SEXE') {
							$prof[$i]["sexe"]=trim(preg_replace("/[^1-2]/","",$value));
						}
						elseif(my_strtoupper($key)=='CIVILITE') {
							$prof[$i]["civilite"]=trim(preg_replace("/[^1-3]/","",$value));
						}
						elseif((my_strtoupper($key)=='NOM_USAGE')||
						(my_strtoupper($key)=='NOM_PATRONYMIQUE')||
						(my_strtoupper($key)=='NOM_USAGE')) {
							$prof[$i][my_strtolower($key)]=trim(preg_replace("/[^A-Za-z -]/","",remplace_accents($value)));
						}
						elseif(my_strtoupper($key)=='PRENOM') {
							$prof[$i][my_strtolower($key)]=trim(preg_replace('/"/','',preg_replace("/'/","",nettoyer_caracteres_nom($value,"a"," -",""))));
						}
						elseif(my_strtoupper($key)=='DATE_NAISSANCE') {
							$prof[$i][my_strtolower($key)]=trim(preg_replace("/[^0-9-]/","",$value));
						}
						elseif((my_strtoupper($key)=='GRADE')||
							(my_strtoupper($key)=='FONCTION')) {
							$prof[$i][my_strtolower($key)]=trim(preg_replace('/"/','',preg_replace("/'/"," ",$value)));
						}
						else {
							$prof[$i][my_strtolower($key)]=trim($value);
						}
						//echo "\$prof[$i][".strtolower($key)."]=".$prof[$i][strtolower($key)]."<br />";
					}
				}

				$indice=$prof[$i]["nom_usage"]." ".$prof[$i]["prenom"];
				if(isset($prof[$i]["date_naissance"])) {
					$indice.=" ".$prof[$i]["date_naissance"];
				}
				$prof2[$indice]=$i;

				if(isset($individu->PROFS_PRINC)) {
					$j=0;
					foreach($individu->PROFS_PRINC->children() as $prof_princ) {
						//$prof[$i]["prof_princ"]=array();
						foreach($prof_princ->children() as $key => $value) {
							$prof[$i]["prof_princ"][$j][my_strtolower($key)]=trim(preg_replace('/"/',"",$value));
							$temoin_au_moins_un_prof_princ="oui";
						}
						$j++;
					}
				}

				if(isset($individu->DISCIPLINES)) {
					$j=0;
					foreach($individu->DISCIPLINES->children() as $discipline) {
						foreach($discipline->attributes() as $key => $value) {
							if(my_strtoupper($key)=='CODE') {
								$prof[$i]["disciplines"][$j]["code"]=trim(preg_replace('/"/',"",$value));
								break;
							}
						}

						foreach($discipline->children() as $key => $value) {
							$prof[$i]["disciplines"][$j][my_strtolower($key)]=trim(preg_replace('/"/',"",$value));
						}
						$j++;
					}
				}

				if($debug_import=='y') {
					echo "<pre style='color:green;'><b>Tableau \$prof[$i]&nbsp;:</b>";
					print_r($prof[$i]);
					echo "</pre>";
				}

				$i++;
			}

			$tmp_prof=$prof;
			$prof=array();
			$tmp_prof2=array_keys($prof2);
			sort($tmp_prof2);
			for($k=0;$k<count($tmp_prof2);$k++){
				$prof[]=$tmp_prof[$prof2[$tmp_prof2[$k]]];
			}
			/*
			echo "<div style='float:left;width:30em;background-color:plum;'><pre>";
			print_r($tmp_prof);
			echo "</pre></div>";
			echo "<div style='float:left;width:30em;background-color:silver';><pre>";
			print_r($prof);
			echo "</pre></div>";
			*/

			$sql="SELECT * FROM utilisateurs WHERE (statut='professeur' OR statut='cpe' OR statut='scolarite') AND etat='actif';";
			$res_u=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_u)==0) {
				echo "<p style='color:red'>Aucun professeur, cpe ou compte scolarité n'est actif dans la base.</p>";
				require("../lib/footer.inc.php");
				die();
			}

			echo "
<form action='".$_SERVER['PHP_SELF']."' enctype='multipart/form-data' method='post' id='form_xml'>
	<fieldset style='border:1px solid white; background-image: url(\"../images/background/opacite50.png\");'>
		".add_token_field()."
		<table class='boireaus boireaus_alt resizable sortable'>
			<thead>
				<tr>
					<th colspan='7'>Base Gepi</th>
					<th>XML</th>
				</tr>
				<tr>
					<th>Login</th>
					<th>Nom</th>
					<th>Prénom</th>
					<th>Statut</th>
					<th colspan='2'>Identifiant STS</th>
					<th>Type STS</th>
					<th></th>
				</tr>
			</thead>
			<tbody>";
			$cpt=0;
			while($lig_u=mysqli_fetch_object($res_u)) {
				echo "
				<tr>
					<td>".$lig_u->login."</td>
					<td>".casse_mot($lig_u->nom,"maj")."</td>
					<td>".casse_mot($lig_u->prenom,"majf2")."</td>
					<td>".$lig_u->statut."</td>
					<td>
						<input type='hidden' name='login_user[$cpt]' id='login_user_$cpt' value='".$lig_u->login."' />
						<input type='text' name='numind[$cpt]' id='numind_$cpt' value='".$lig_u->numind."' onchange='changement()' />
					</td>
					<td>".$lig_u->type."</td>
					<td title=\"Sélectionnez ici le type pour une mise à jour manuelle du type STS.\">
						<select name='type[$cpt]' id='type_$cpt' onchange='changement()'>
							<option value=''>---</option>
							<option value='epp'".($lig_u->type=="epp" ? " selected='true'" : "").">Emploi Poste Personnel</option>
							<option value='local'".($lig_u->type=="local" ? " selected='true'" : "").">Local</option>
						</select>
					</td>
					<td title=\"Effectuez ici le rapprochement pour mettre à jour l'identifiant et le type STS d'après le contenu du fichier XML.\">
						<select name='select_xml[$cpt]' id='select_xml_$cpt' onchange=\"maj_info($cpt);changement()\">
							<option value='|'>---</option>";
				for($k=0;$k<count($prof);$k++){
					$numind=$prof[$k]["id"];
					if((isset($prof[$k]["fonction"]))&&($prof[$k]["fonction"]=="ENS")) {
						$numind="P".$prof[$k]["id"];
					}
					echo "
							<option value='".$numind."|".$prof[$k]["type"]."'>".casse_mot($prof[$k]["nom_usage"],"maj")." ".casse_mot($prof[$k]["prenom"],"majf2")." (".$numind.") (".$prof[$k]["type"].")</option>";
				}
				echo "
						</select>
					</td>
				</tr>";
				$cpt++;
			}
			echo "
			</tbody>
		</table>

		<input type='hidden' name='cpt' value='".$cpt."' />
		<input type='hidden' name='enregistrer_modifs_xml' value='y' />
		<p><input type='submit' id='input_submit' value='Valider' /></p>
	</fieldset>
</form>

<script type='text/javascript'>
	function maj_info(cpt) {
		index=document.getElementById('select_xml_'+cpt).selectedIndex;
		valeur=document.getElementById('select_xml_'+cpt).options[index].value;
		tab=valeur.split('|');
		//alert(valeur+' tab[0]='+tab[0]+' tab[1]='+tab[1]);
		document.getElementById('numind_'+cpt).value=tab[0];

		if(tab[1]=='') {
			document.getElementById('type_'+cpt).selectedIndex=0;
		}
		if(tab[1]=='epp') {
			document.getElementById('type_'+cpt).selectedIndex=1;
		}
		if(tab[1]=='local') {
			document.getElementById('type_'+cpt).selectedIndex=2;
		}
	}
</script>";


		}
	}

	require("../lib/footer.inc.php");
	die();
}


echo "
</p>";

if(isset($chaine_form_confirm)) {
	echo $chaine_form_confirm;
}

echo "
<form action='".$_SERVER['PHP_SELF']."' method='post' id='form_aff'>
	<fieldset style='border:1px solid white; background-image: url(\"../images/background/opacite50.png\");'>
		<ul>
			<li style='list-style-type:none; display:inline; margin-right:2em;'>Afficher les comptes</li>
			<li style='list-style-type:none; display:inline; margin-right:2em;'><input type='checkbox' name='aff_etat[]' id='aff_statut_actif' value='actif' ".(in_array("actif", $aff_etat) ? "checked" : "")." onchange='changement()' /><label for='aff_statut_actif'>actifs</label></li>
			<li style='list-style-type:none; display:inline; margin-right:2em;'><input type='checkbox' name='aff_etat[]' id='aff_statut_inactif' value='inactif' ".(in_array("inactif", $aff_etat) ? "checked" : "")." onchange='changement()' /><label for='aff_statut_inactif'>inactifs</label></li>
		</ul>

		<ul>
			<li style='list-style-type:none; display:inline; margin-right:2em;'>Afficher les comptes de statut</li>";

for($loop=0;$loop<count($tab_statuts);$loop++) {
	echo "
			<li style='list-style-type:none; display:inline; margin-right:2em;'><input type='checkbox' name='aff_statut[]' id='aff_statut_$loop' value='".$tab_statuts[$loop]."' ".(in_array($tab_statuts[$loop], $aff_statut) ? "checked" : "")." onchange='changement()' /><label for='aff_statut_$loop'>".$tab_statuts[$loop]."</label></li>";
}

echo "
		</ul>

		<p><input type='submit' value='Afficher' /></p>
	</fieldset>
</form>

<br />

<form action='".$_SERVER['PHP_SELF']."' method='post' id='formulaire'>
<fieldset style='border:1px solid white; background-image: url(\"../images/background/opacite50.png\");'>
	".add_token_field()."
	<p><span class='bold'>Action&nbsp;:</span> Pour les utilisateurs cochés ci-dessous, appliquer l'action suivante&nbsp;:<br />

	<input type='radio' name='action' id='action_etat' value='etat' onchange='changement()' /><label for='action_etat'>Modifier l'état vers&nbsp;:</label>
		<select name='etat' onchange=\"document.getElementById('action_etat').checked=true;changement();\">";
for($loop=0;$loop<count($tab_etat);$loop++) {
	echo "
			<option value='".$tab_etat[$loop]."'>".$tab_etat[$loop]."</option>";
}
echo "
		</select>
		<br />

	<input type='radio' name='action' id='action_auth_mode' value='auth_mode' onchange='changement()' /><label for='action_auth_mode'>Modifier le mode d'authentification vers&nbsp;:</label>
		<select name='auth_mode' onchange=\"document.getElementById('action_auth_mode').checked=true;changement();\">";
for($loop=0;$loop<count($tab_auth_mode);$loop++) {
echo "
			<option value='".$tab_auth_mode[$loop]."'>".$tab_auth_mode_texte[$loop]."</option>";
}
echo "
		</select>
		<br />

	<input type='radio' name='action' id='action_reset_passwords' value='reset_passwords' onchange='changement()' /><label for='action_reset_passwords'>Réinitialiser le mot de passe</label><!--span style='color:red'> Non encore implémenté</span--><br />

	<!--input type='radio' name='action' id='action_supprimer' value='supprimer' /><label for='action_supprimer'>Supprimer le compte<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<em>ATTENTION&nbsp;: Opération irréversible&nbsp;!</em>)</label><span style='color:red'> Non encore implémenté</span><br /-->

	</p>\n";

//statut='administrateur' OR
$sql="SELECT * FROM utilisateurs WHERE statut='scolarite' OR statut='professeur' OR statut='cpe' OR statut='secours' ORDER BY statut, nom, prenom;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
echo "
	<p><input type='submit' value='Valider' /></p>
	<table class='boireaus' summary='Tableau des comptes'>
		<tr>
			<th>
				<a href=\"javascript:CocheColonne();changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne();changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher' /></a>
			</th>
			<th>Login</th>
			<th>Nom</th>
			<th>Statut</th>
			<th>Etat</th>
			<th>Auth.</th>
			<th>Id.STS</th>
			<th>Type STS</th>
		</tr>\n";
$statut_prec="";
$alt=1;
$cpt=0;
while($lig=mysqli_fetch_object($res)) {
	if((in_array($lig->statut, $aff_statut))&&(in_array($lig->etat, $aff_etat))) {
		$alt=$alt*(-1);
		if($statut_prec!=$lig->statut) {
			echo "
		<tr>
			<th colspan='8'>".$lig->statut."</th>
		</tr>";
		}
		if($lig->etat=='actif') {$style_ajout="";} else {$style_ajout=" style='background-color:grey;'";}
		echo "
		<tr class='lig$alt white_hover'$style_ajout>
			<td><input type='checkbox' name='u_login[]' id='case_$cpt' value='".$lig->login."' onchange=\"checkbox_change('case_$cpt');changement()\" /></td>
			<td><label for='case_$cpt'>".$lig->login."</label></td>
			<td><label for='case_$cpt' id='texte_case_$cpt'>".casse_mot($lig->nom, 'maj')." ".casse_mot($lig->prenom, 'majf2')."</label></td>
			<td><label for='case_$cpt'>".$lig->statut."</label></td>
			<td><label for='case_$cpt'>".$lig->etat."</label></td>
			<td><label for='case_$cpt'>".$lig->auth_mode."</label></td>
			<td><label for='case_$cpt'>".$lig->numind."</label></td>
			<td><label for='case_$cpt'>".$lig->type."</label></td>
		</tr>";
		$statut_prec=$lig->statut;
		$cpt++;
	}
}
echo "
	</table>
	<p><input type='submit' value='Valider' /></p>
</fieldset>
</form>

<script type='text/javascript'>
	".js_checkbox_change_style('checkbox_change', 'texte_', 'n').";

	for(i=0;i<$cpt;i++) {
		checkbox_change('case_'+i);
	}

	function CocheColonne() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('case_'+i)) {
				document.getElementById('case_'+i).checked=true;
				checkbox_change('case_'+i);
			}
		}
	}

	function DecocheColonne() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('case_'+i)) {
				document.getElementById('case_'+i).checked=false;
				checkbox_change('case_'+i);
			}
		}
	}
</script>

<br />
<p><em>NOTES&nbsp;:</em></p>
<ul>
	<li><p>Le statut 'administrateur' n'est pas proposé pour éviter des accidents.</p></li>
	<li><p>Si vous réinitialisez des mots de passe, il vous sera proposé, après validation, d'envoyer ces nouveaux mot de passe par mail pour les utilisateurs dont le mail est renseigné.<br />
	Par sécurité, à la première connexion, les comptes concernés devront changer de mot de passe.</p></li>
</ul>
<p><br /></p>\n";

echo "
<a name='update_xml_sts'></a>
<form action='".$_SERVER['PHP_SELF']."' enctype='multipart/form-data' method='post' id='form_envoi_xml'>
	<fieldset style='border:1px solid white; background-image: url(\"../images/background/opacite50.png\");'>
		".add_token_field()."
		<p><span class='bold'>Mise à jour des informations utilisateurs d'après STS&nbsp;:</span><br />
		Il s'agit de mettre à jour les identifiants STS et types STS.</p>

		<input type='hidden' name='upload_sts_file' value='y' />

		<p>Veuillez fournir le fichier XML <b>sts_emp_<i>RNE</i>_<i>ANNEE</i>.xml</b>&nbsp;:</p>
		<p><input type=\"file\" size=\"65\" name=\"xml_file\" id='input_xml_file' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' onchange='changement()' />

		<p><input type='submit' id='input_submit' value='Valider' />
	<input type='button' id='input_button' value='Valider' style='display:none;' onclick=\"check_champ_file()\" /></p>
	</fieldset>

	<script type='text/javascript'>
		document.getElementById('input_submit').style.display='none';
		document.getElementById('input_button').style.display='';

		function check_champ_file() {
			fichier=document.getElementById('input_xml_file').value;
			//alert(fichier);
			if(fichier=='') {
				alert('Vous n\'avez pas sélectionné de fichier XML à envoyer.');
			}
			else {
				document.getElementById('form_envoi_xml').submit();
			}
		}
	</script>
</form>";

require("../lib/footer.inc.php");
?>
