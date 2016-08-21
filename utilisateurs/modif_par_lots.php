<?php
/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//**************** EN-TETE *****************************
$titre_page = "Personnels : Traitements par lots";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************************

//debug_var();

?>
<p class="bold">
<a href="index.php?mode=personnels"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>

<?php

if(isset($chaine_form_confirm)) {
	echo $chaine_form_confirm;
}

echo "<form action='".$_SERVER['PHP_SELF']."' method='post' id='form_aff'>
	<fieldset style='border:1px solid white; background-image: url(\"../images/background/opacite50.png\");'>
		<ul>
			<li style='list-style-type:none; display:inline; margin-right:2em;'>Afficher les comptes</li>
			<li style='list-style-type:none; display:inline; margin-right:2em;'><input type='checkbox' name='aff_etat[]' id='aff_statut_actif' value='actif' ".(in_array("actif", $aff_etat) ? "checked" : "")."/><label for='aff_statut_actif'>actifs</label></li>
			<li style='list-style-type:none; display:inline; margin-right:2em;'><input type='checkbox' name='aff_etat[]' id='aff_statut_inactif' value='inactif' ".(in_array("inactif", $aff_etat) ? "checked" : "")."/><label for='aff_statut_inactif'>inactifs</label></li>
		</ul>

		<ul>
			<li style='list-style-type:none; display:inline; margin-right:2em;'>Afficher les comptes de statut</li>";

for($loop=0;$loop<count($tab_statuts);$loop++) {
	echo "
			<li style='list-style-type:none; display:inline; margin-right:2em;'><input type='checkbox' name='aff_statut[]' id='aff_statut_$loop' value='".$tab_statuts[$loop]."' ".(in_array($tab_statuts[$loop], $aff_statut) ? "checked" : "")."/><label for='aff_statut_$loop'>".$tab_statuts[$loop]."</label></li>";
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

	<input type='radio' name='action' id='action_etat' value='etat' /><label for='action_etat'>Modifier l'état vers&nbsp;:</label>
		<select name='etat' onchange=\"document.getElementById('action_etat').checked=true\">";
for($loop=0;$loop<count($tab_etat);$loop++) {
	echo "
			<option value='".$tab_etat[$loop]."'>".$tab_etat[$loop]."</option>";
}
echo "
		</select>
		<br />

	<input type='radio' name='action' id='action_auth_mode' value='auth_mode' /><label for='action_auth_mode'>Modifier le mode d'authentification vers&nbsp;:</label>
		<select name='auth_mode' onchange=\"document.getElementById('action_auth_mode').checked=true\">";
for($loop=0;$loop<count($tab_auth_mode);$loop++) {
echo "
			<option value='".$tab_auth_mode[$loop]."'>".$tab_auth_mode_texte[$loop]."</option>";
}
echo "
		</select>
		<br />

	<input type='radio' name='action' id='action_reset_passwords' value='reset_passwords' /><label for='action_reset_passwords'>Réinitialiser le mot de passe</label><!--span style='color:red'> Non encore implémenté</span--><br />

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
			<th colspan='6'>".$lig->statut."</th>
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
require("../lib/footer.inc.php");
?>
