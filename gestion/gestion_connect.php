<?php
/*
 *
 * Copyright 2001-2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Begin standart header

$titre_page = "Gestion des connexions";



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

// Load settings

if (!loadSettings()) {
    die("Erreur chargement settings");
}

//
// Protection contre les attaques.
//
if (isset($_POST['valid_param_mdp'])) {
	check_token();

    settype($_POST['nombre_tentatives_connexion'],"integer");
    settype($_POST['temps_compte_verrouille'],"integer");
    if ($_POST['nombre_tentatives_connexion'] < 1) $_POST['nombre_tentatives_connexion'] = 1;
    if ($_POST['temps_compte_verrouille'] < 0) $_POST['temps_compte_verrouille'] = 0;
    if (!saveSetting("nombre_tentatives_connexion", $_POST['nombre_tentatives_connexion'])) {
        $msg1 = "Il y a eu un problème lors de l'enregistrement du paramètre nombre_tentatives_connexion.";
    } else {
        $msg1 = "";
    }
    if (!saveSetting("temps_compte_verrouille", $_POST['temps_compte_verrouille'])) {
        $msg2 = "Il y a eu un problème lors de l'enregistrement du paramètre temps_compte_verrouille.";
    } else {
        $msg2 = "";
    }
    if (($msg1 == "") and ($msg2 == ""))
        $msg = "Les paramètres ont été correctement enregistrées";
    else
        $msg = $msg1." ".$msg2;
}



//Activation / désactivation du login
if (isset($_POST['disable_login'])) {
	check_token();

    if (!saveSetting("disable_login", $_POST['disable_login'])) {
        $msg = "Il y a eu un problème lors de l'enregistrement du paramètre d'activation/désactivation des connexions.";
    } else {
        $msg = "l'enregistrement du paramètre d'activation/désactivation des connexions a été effectué avec succès.";
    }
}

//Activation / désactivation de la procédure de réinitialisation du mot de passe par email
if (isset($_POST['enable_password_recovery'])) {
	check_token();

    if (!saveSetting("enable_password_recovery", $_POST['enable_password_recovery'])) {
        $msg = "Il y a eu un problème lors de l'enregistrement du paramètre d'activation/désactivation des connexions.";
    } else {
        $msg = "l'enregistrement du paramètre d'activation/désactivation des connexions a été effectué avec succès.";
    }
}


//EXPORT CSV
if(isset($_GET['mode'])){
	if($_GET['mode']=="csv"){

	if (!isset($_SESSION['donnees_export_csv_log'])) { $ligne_csv = false ; } else {$ligne_csv =  $_SESSION['donnees_export_csv_log'];}

		$chaine_titre="Export_log_Annee_scolaire_".getSettingValue("gepiYear");
		$now = gmdate('D, d M Y H:i:s') . ' GMT';
		$nom_fic=$chaine_titre."_".$now.".csv";

		send_file_download_headers('text/x-csv',$nom_fic);

		$nb_ligne = count($ligne_csv);

		$fd="";
		for ($i=0;$i<$nb_ligne;$i++) {
		  $fd.=$ligne_csv[$i];
		}

		echo echo_csv_encoded($fd);
		die();
	}
}
//FIN EXPORT CSV


if(isset($_POST['valid_envoi_mail_connexion'])){
	check_token();

	$envoi_mail_connexion=isset($_POST['envoi_mail_connexion']) ? $_POST['envoi_mail_connexion'] : "n";
	if($envoi_mail_connexion!="y") {
		$envoi_mail_connexion="n";
	}
	if (!saveSetting("envoi_mail_connexion", $envoi_mail_connexion)) {
		$msg = "Il y a eu un problème lors de l'enregistrement du paramètre d'envoi ou non de mail lors des connexions.";
	} else {
		$msg = "l'enregistrement du paramètre d'envoi ou non de mail lors des connexions a été effectué avec succès.";
	}
}

if(isset($_POST['valid_message'])){
	check_token();

	$message_login=isset($_POST['message_login']) ? $_POST['message_login'] : 0;
	//$sql="UPDATE setting SET value='$message_login' WHERE name='message_login'";
	saveSetting('message_login',$message_login);
}

//================================
// End standart header
require_once("../lib/header.inc.php");
//================================

//debug_var();

isset($mode_navig);
$mode_navig = isset($_POST["mode_navig"]) ? $_POST["mode_navig"] : (isset($_GET["mode_navig"]) ? $_GET["mode_navig"] : NULL);
if ($mode_navig == 'accueil') {
    $retour = "../accueil.php";
} else {
    $retour = "index.php#gestion_connect";
}
?>
<p class='bold'>
	<a href="<?php echo $retour; ?>">
		<img src='../images/icons/back.png' alt='Retour' class='back_link'/>
		Retour
	</a>
</p>

<?php
//
// Affichage des personnes connectées
//
?>
<h2>Utilisateurs connectés</h2>
<?php
// compte le nombre d'enregistrement dans la table
//$sql = "select u.login, concat(u.prenom, ' ', u.nom) utilisa, u.email from log l, utilisateurs u where (l.LOGIN = u.login and l.END > now())";
$sql = "select u.login, concat(u.prenom, ' ', u.nom) utilisa, u.email, u.auth_mode, u.statut, l.END from log l, utilisateurs u where (l.LOGIN = u.login and l.END > now()) ORDER BY statut";

$res = sql_query($sql);
if ($res) {
?>
	<table class='boireaus center'>
		<caption>Utilisateurs connectés en ce moment</caption>
		<tr>
			<th>Utilisateur</th>
			<th>Statut</th>
			<th>Envoyer un mail</th>
			<th>Déconnecter en changeant le mot de passe</th>
			<th title="Si l'utilisateur n'agit pas, ne change pas de page, n'enregistre pas,... la session se terminera à la date et à l'heure indiquée">
				Fin théorique de session
			</th>
		</tr>
<?php

	$alt=1;
    for ($i = 0; ($row = sql_row($res, $i)); $i++) {
		$alt=$alt*(-1);

?>
		<tr class='lig<?php echo $alt; ?> white_hover'>
			<td>
<?php
		if ($row[4]=="eleve") {
?>
				<a href="../eleves/modify_eleve.php?eleve_login=<?php echo $row[0]; ?>" ><?php echo $row[1]; ?></a>
<?php
			$sql= " SELECT id_classe FROM j_eleves_classes WHERE login='$row[0]'";
			$res1 = sql_query($sql);
			$id = mysql_fetch_array($res1);
			$sql= " SELECT classe FROM classes WHERE id='$id[id_classe]'";
			$res2 = sql_query($sql);
			$classe_eleve = mysql_fetch_array($res2);
		}
		elseif ($row[4]=="responsable") {
			$sql= " SELECT pers_id FROM resp_pers where login='$row[0]'";
			$res3 = sql_query($sql);
			$id = mysql_fetch_array($res3);
			echo "<a href=\"../responsables/modify_resp.php?pers_id=" .$id['pers_id']. "\" />".$row[1]."</a>";
		}
		else {
?>
				<a href="../utilisateurs/modify_user.php?user_login=<?php echo $row[0]; ?>" ><?php echo $row[1]; ?></a>
<?php
		}
?>
			</td>
			
			<td>
				<?php
				if ($row[4] == "eleve") {
				echo $row[4]. " " .$classe_eleve['classe'];
				}
				else {
				echo $row[4];
				}
				?>
			</td>
			
			<td>
<?php if(check_mail($row[2])) { ?>
				<a href="mailto:<?php echo $row[2]; ?>">
					<img src='../images/icons/mail.png' 
						 width='16' 
						 height='16' 
						 alt="" 
						 title="Envoyer un mail à <?php echo $row[2]; ?>" />
				</a>
<?php } elseif($row[2]=='') { ?>
				<img src='../images/disabled.png' 
					 width='16' 
					 height='16' 
					 alt="" 
					 title="Pas d'adresse mail renseignée" />
<?php } else { ?>
				<a href="mailto:<?php echo $row[2]; ?>">
				   <img src='../images/icons/mail.png' 
						width='16' 
						height='16' 
						alt="" 
						title="Envoyer un mail à <?php echo $row[2]; ?>" />
				</a>
				<span style='color:red' 
					  title="L'adresse mail <?php echo $row[2]; ?> n'a pas l'air correcte"> (*) </span>
<?php } ?>
			</td>
			
			<td>
<?php
		$afficher_deconnecter_et_changer_mdp="n";
		if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon"  and getSettingValue("auth_sso") != "lcs" and getSettingValue("use_sso") != "ldap_scribe")) {
			$afficher_deconnecter_et_changer_mdp="y";
		}
		elseif((getSettingValue("auth_sso") == "lcs")&&($row[3]=='gepi')) {
			$afficher_deconnecter_et_changer_mdp="y";
		}

		if($afficher_deconnecter_et_changer_mdp=="y") { 
?>
				<a href="../utilisateurs/change_pwd.php?user_login=<?php echo $row[0].add_token_in_url(); ?>">
				   <img src='../images/icons/quit_16.png' 
						width='16' 
						height='16' 
						alt="" 
						title="Déconnecter en changeant le mot de passe" />
				</a>
<?php } ?>
			</td>
			
			<td>
				<?php echo strftime("%d/%m/%Y à %H:%M", mysql_date_to_unix_timestamp($row[5])); ?>
			</td>
		</tr>
<?php }
}
?>
	</table>

<hr />
<?php
//
// Activation/désactivation des connexions
//
?>
<h2>Activation/désactivation des connexions</h2>


<?php
$disable_login=getSettingValue("disable_login");

if($disable_login=="yes"){
?>
<p>Les connexions sont actuellement <span style='font-weight:bold'>désactivées</span>.</p>
<?php
}
elseif($disable_login=="no"){
?>
<p>Les connexions sont actuellement <span style='font-weight:bold'>activées</span>.</p>
<?php } else { ?>
<p>
	Les connexions <span style='font-weight:bold'>futures</span> sont actuellement 
	<span style='font-weight:bold'>désactivées</span>.<br />Aucune nouvelle connexion n'est acceptée.
</p>
<?php } ?>
<p>
	En désactivant les connexions, vous rendez impossible la connexion au site pour les utilisateurs, 
	hormis les administrateurs.
</p>

<form action="gestion_connect.php" id="form_acti_connect" method="post">
<fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png"); '>
	<p>
<?php echo add_token_field(); ?>
		<input type='radio' 
			   name='disable_login' 
			   value='yes' 
			   id='label_1a'
			   <?php if ($disable_login=='yes'){ echo " checked='checked'";} ?> />
		<label for='label_1a'>Désactiver les connexions</label>
		<br />
		(<em>
			<span class='rouge'>
				Attention, les utilisateurs actuellement connectés sont automatiquement déconnectés.
			</span>
		</em>)
	</p>
	
	<p>
		<input type='radio' 
			   name='disable_login' 
			   value='soft' 
			   id='label_3a'
			   <?php if ($disable_login=='soft'){ echo " checked='checked'";} ?> />
		<label for='label_3a'>Désactiver les futures connexions</label>
		<br />
		(<em>
			et attendre la fin des connexions actuelles pour pouvoir désactiver les connexions et 
			procéder à une opération de maintenance, par exemple
		</em>)
	</p>
	
	<p>
		<input type='radio' 
			   name='disable_login' 
			   value='no' 
			   id='label_2a'
			   <?php if ($disable_login=='no'){ echo " checked='checked'";} ?> />
		<label for='label_2a'>Activer les connexions</label>
	</p>

	<p class="center">
		<input type="submit" name="valid_acti_mdp" value="Valider" />
	</p>
</fieldset>
</form>

<hr />

<?php 
//
// Message sur la page de login
// ?>
<a name='message_login'></a>
<h2>Faire apparaitre un message sur la page de login</h2>

<?php 
$message_login=getSettingValue("message_login");
if($message_login=='') {$message_login=0; saveSetting('message_login',$message_login);}

$sql="SELECT * FROM message_login ORDER BY texte;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
?>
<p>Aucun message n'a encore été saisi.</p>
<p><a href='saisie_message_connexion.php'>Saisir de nouveaux messages.</a></p>
<?php } else { ?>

<form action="gestion_connect.php" id="form_message_login" method="post">
<fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png"); '>
	<div style='border:1px dashed black; margin:1em;'>
		<p>
		<?php echo add_token_field(); ?>
			<input type='radio' 
				   name='message_login' 
				   id='message_login0' 
				   value='0'
				   <?php if($message_login==0) {echo " checked='checked'";} ?> />
			<label for='message_login0'> Aucun message</label>
		</p>
	</div>
<?php 
	while($lig=mysql_fetch_object($res)) { ?>
	<div style='border:1px dashed black; margin:1em;'>
		<p>
			<input type='radio' 
				   name='message_login' 
				   id='message_login<?php echo $lig->id; ?>' 
				   value='<?php echo $lig->id; ?>'
				   <?php if($message_login==$lig->id) {echo " checked='checked'";} ?> />
			<label for='message_login<?php echo $lig->id; ?>'> <?php echo nl2br($lig->texte); ?></label>
		</p>
	</div>
<?php } ?>
	<p class="center"><input type="submit" name="valid_message" value="Valider" /></p>
</fieldset>
</form>

<p><a href='saisie_message_connexion.php'>Saisir de nouveaux messages ou modifier des messages existants.</a></p>

<?php } ?>

<hr />


<?php 
//
// Protection contre les attaques.
// ?>
<h2>Protection contre les attaques forces brutes.</h2>
<p>
	Configuration de GEPI de manière à bloquer temporairement le compte d'un utilisateur après un certain nombre de tentatives 
	de connexion infructueuses.
	<br />
	En contrepartie, un pirate peut se servir de ce mécanisme d'auto-défense pour bloquer en permanence des comptes utilisateur 
	ou administrateur.
	<br />
	Si vous ête un jour confronté à cette situation d'urgence, vous pourrez dans le fichier "config.inc.php", forcer le 
	débloquage des comptes administrateur et/ou mettre en liste noire, la ou les adresses IP incriminées.
</p>

<form action="gestion_connect.php" id="form_param_mdp" method="post">
<fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png"); '>
	<table>
	<tr style="text-align:left;">
	<td>
	<?php echo add_token_field(); ?>
		<label for="nombre_tentatives_connexion">
			Nombre maximum de tentatives de connexion infructueuses : 
		</label>
	</td>
	<td>
		<input type="text" 
			   name="nombre_tentatives_connexion" 
			   id="nombre_tentatives_connexion"
			   value="<?php echo getSettingValue('nombre_tentatives_connexion'); ?>"
			   size="10" />
	</td>
	</tr>
	<tr style="text-align:left;">
	<td>
		<label for="temps_compte_verrouille">
			Temps en minutes pendant lequel un compte est temporairement verrouillé suite à un trop grand nombre d'essais infructueux :
		</label>
	</td>
	<td>
		<input type="text" 
			   name="temps_compte_verrouille"
			   id= "temps_compte_verrouille"
			   value="<?php echo getSettingValue('temps_compte_verrouille'); ?>" 
			   size="10" />
	</td>
	</tr>
	</table>
	
	<p class="center">
		<input type="submit" name="valid_param_mdp" value="Valider" />
	</p>
</fieldset>
</form>

<hr />

<?php 
//
// Avertissement des utilisateurs lors des connexions
// ?>
<h2>Avertissement lors des connexions</h2>
<p>
	Il est possible d'avertir les utilisateurs par mail lors de leur connexion, 
	sous réserve que leur adresse mail soit renseignée dans Gepi (<em>information modifiable par le lien 'Gérer mon compte'</em>).
	<br />
	Si l'adresse n'est pas renseignée aucun mail ne peut parvenir à l'utilisateur qui se connecte.
	<br />
	Si l'adresse est correctement renseignée, en cas d'usurpation comme de connexion légitime, l'utilisateur recevra un mail.
	<br />
	S'il ne réagit pas en changeant de mot de passe et en avertissant l'administrateur lors d'une usurpation, 
	des intrusions ultérieures pourront être opérées sans que l'utilisateur soit averti si l'intrus prend soin de 
	supprimer/modifier l'adresse mail dans 'Gérer mon compte'.
</p>

<form action="gestion_connect.php" id="form_mail_connexion" method="post">
<fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png"); '>
	<p>
	<?php echo add_token_field(); ?>
			Activer l'envoi de mail lors de la connexion :
		<input type="radio" 
			   name="envoi_mail_connexion" 
			   id="envoi_mail_connexion_y" 
			   value='y'
			   <?php if(getSettingValue("envoi_mail_connexion")=="y") { echo " checked='checked' "; } ?> />
		<label for='envoi_mail_connexion_y'>
			Oui
		</label>
		<input type="radio" 
			   name="envoi_mail_connexion" 
			   id="envoi_mail_connexion_n" 
			   value='n'
			   <?php if(getSettingValue("envoi_mail_connexion")!="y") {	echo "checked='checked' ";} ?> />
		<label for='envoi_mail_connexion_n'>
			Non
		</label>
	</p>
	
	<p class="center">
		<input type="submit" name="valid_envoi_mail_connexion" value="Valider" />
	</p>
</fieldset>
</form>

<hr />

<?php 
/*
//
// Changement du mot de passe obligatoire
//
if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon"  and getSettingValue("use_sso") != "lcs" and getSettingValue("use_sso") != "ldap_scribe")) {
echo "<h3 class='gepi'>Changement du mot de passe obligatoire lors de la prochaine connexion</h3>";
echo "<p><span style='font-weight:bold'>ATTENTION : </span>En validant le bouton ci-dessous, <span style='font-weight:bold'>tous les utilisateurs</span> seront amenés à changer leur mot de passe lors de leur prochaine connexion.</p>";
echo "<form action=\"gestion_connect.php\" name=\"form_chgt_mdp\" method=\"post\">";
echo "<center><input type=\"submit\" name=\"valid_chgt_mdp\" value=\"Valider\" onclick=\"return confirmlink(this, 'Êtes-vous sûr de vouloir forcer le changement de mot de passe de tous les utilisateurs ?', 'Confirmation')\" /></center>";
echo "<input type=hidden name=mode_navig value='$mode_navig' />";
echo "</form><hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>";
}
//
// Paramétrage du Single Sign-On
//

echo "<h3 class='gepi'>Mode d'authentification</h3>";
echo "<p><span style='font-weight:bold'>ATTENTION :</span> Dans le cas d'une authentification en Single Sign-On avec CAS, LemonLDAP ou LCS, seuls les utilisateurs pour lesquels aucun mot de passe n'est présent dans la base de données pourront se connecter. Toutefois, il est recommandé de conserver un compte administrateur avec un mot de passe afin de pouvoir vous connecter en bloquant le SSO par le biais de la variable 'block_sso' du fichier /lib/global.inc.</p>";
echo "<p>Si vous utilisez CAS, vous devez entrer les coordonnées du serveur CAS dans le fichier /secure/config_cas.inc.php.</p>";
echo "<p>Si vous utilisez l'authentification sur serveur LDAP, vous devez renseigner le fichier /secure/config_ldap.inc.php avec les informations nécessaires pour se connecter au serveur.</p>";
echo "<form action=\"gestion_connect.php\" name=\"form_auth\" method=\"post\">";

echo "<input type='radio' name='use_sso' value='no' id='label_1'";
if (getSettingValue("use_sso")=='no' OR !getSettingValue("use_sso")) echo " checked ";
echo " /> <label for='label_1'>Authentification autonome (sur la base de données de Gepi) [défaut]</label>";

echo "<br/><input type='radio' name='use_sso' value='lcs' id='lcs'";
if (getSettingValue("use_sso")=='lcs') echo " checked ";
echo " /> <label for='lcs'>Authentification sur serveur LCS</label>";

echo "<br/><input type='radio' name='use_sso' value='ldap_scribe' id='label_ldap_scribe'";
if (getSettingValue("use_sso")=='ldap_scribe') echo " checked ";
echo " /> <label for='label_ldap_scribe'>Authentification sur serveur Eole SCRIBE (LDAP)</label>";

echo "<br /><input type='radio' name='use_sso' value='cas' id='label_2'";
if (getSettingValue("use_sso")=='cas') echo " checked ";
echo " /> <label for='label_2'>Authentification SSO par un serveur CAS</label>";

echo "<br /><input type='radio' name='use_sso' value='lemon' id='label_3'";
if (getSettingValue("use_sso")=='lemon') echo " checked ";
echo " /> <label for='label_3'>Authentification SSO par LemonLDAP</label>";

echo "<p>Remarque : les changements n'affectent pas les sessions en cours.";

echo "<center><input type=\"submit\" name=\"auth_mode_submit\" value=\"Valider\" onclick=\"return confirmlink(this, 'Êtes-vous sûr de vouloir changer le mode d\' authentification ?', 'Confirmation')\" /></center>";

echo "<input type=hidden name=mode_navig value='$mode_navig' />";

echo "</form>

<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\" />";



//
// Durée de conservation des logs
//
echo "<h3 class='gepi'>Durée de conservation des connexions</h3>";
echo "<p>Conformément à la loi loi informatique et liberté 78-17 du 6 janvier 1978, la durée de conservation de ces données doit être déterminée et proportionnée aux finalités de leur traitement.
Cependant par sécurité, il est conseillé de conserver une trace des connexions sur un laps de temps suffisamment long.
</p>";
echo "<form action=\"gestion_connect.php\" name=\"form_chgt_duree\" method=\"post\">";
echo "Durée de conservation des informations sur les connexions : <select name=\"duree\" size=\"1\">";
echo "<option ";
$duree = getSettingValue("duree_conservation_logs");
if ($duree == 30) echo "selected";
echo " value=30>Un mois</option>";
echo "<option ";
if ($duree == 60) echo "selected";
echo " value=60>Deux mois</option>";
echo "<option ";
if ($duree == 183) echo "selected";
echo " value=183>Six mois</option>";
echo "<option ";
if ($duree == 365) echo "selected";
echo " value=365>Un an</option>";
echo "</select>";
echo "<input type=\"submit\" name=\"Valider\" value=\"Enregistrer\" />";
echo "<input type=hidden name=mode_navig value='$mode_navig' />";
echo "</form>";
//
// Nettoyage du journal
//
?>
<hr class="header" style="margin-top: 32px; margin-bottom: 24px;"/>
<h3 class='gepi'>Suppression de toutes les entrées du journal de connexion</h3>
<?php
$sql = "select START from log order by END";
$res = sql_query($sql);
$logs_number = sql_count($res);
$row = sql_row($res, 0);
$annee = mb_substr($row[0],0,4);
$mois =  mb_substr($row[0],5,2);
$jour =  mb_substr($row[0],8,2);
echo "<p>Nombre d'entrées actuellement présentes dans le journal de connexion : <span style='font-weight:bold'>".$logs_number."</span><br />";
echo "Actuellement, le journal contient l'historique des connexions depuis le <span style='font-weight:bold'>".$jour."/".$mois."/".$annee."</span></p>";
echo "<p><span style='font-weight:bold'>ATTENTION : </span>En validant le bouton ci-dessous, <span style='font-weight:bold'>toutes les entrées du journal de connexion (hormis les connexions en cours) seront supprimées</span>.</p>";
echo "<form action=\"gestion_connect.php\" name=\"form_sup_logs\" method=\"post\">";
echo "<center><input type=\"submit\" name=\"valid_sup_logs\" value=\"Valider\" onclick=\"return confirmlink(this, 'Êtes-vous sûr de vouloir supprimer tout l\'historique du journal de connexion ?', 'Confirmation')\" /></center>";
echo "<input type=hidden name=mode_navig value='$mode_navig' />";
echo "</form>";
*/
//
// Journal des connections
//
?>
<!--<hr class="header" style="margin-top: 32px; margin-bottom: 24px;"/>-->
<?php
if (isset($_POST['duree2'])) {
   $duree2 = $_POST['duree2'];
} else {
	if (isset($_GET['duree2'])) {
		$duree2 = $_GET['duree2'];
	} else {
		$duree2 = '20dernieres';
	}
}

if(($duree2!="20dernieres")&&
	($duree2!="2")&&
	($duree2!="7")&&
	($duree2!="15")&&
	($duree2!="30")&&
	($duree2!="60")&&
	($duree2!="183")&&
	($duree2!="365")&&
	($duree2!="all")
	) {
		$duree2="20dernieres";
}

switch( $duree2 ) {
   case '20dernieres' :
   $display_duree="les 20 dernières";
   break;
   case 2:
   $display_duree="depuis deux jours";
   break;
   case 7:
   $display_duree="depuis une semaine";
   break;
   case 15:
   $display_duree="depuis quinze jours";
   break;
   case 30:
   $display_duree="depuis un mois";
   break;
   case 60:
   $display_duree="depuis deux mois";
   break;
   case 183:
   $display_duree="depuis six mois";
   break;
   case 365:
   $display_duree="depuis un an";
   break;
   case 'all':
   $display_duree="depuis le début";
   break;
}

?>
<h2>Journal des connexions <span style='font-weight:bold'><?php echo $display_duree; ?></span></h2>

<div title="Journal des connections" style="width: 100%;">
<ul>
	<li>Les lignes en rouge signalent une tentative de connexion avec un mot de passe erroné.</li>
	<li>Les lignes en orange signalent une session close pour laquelle l'utilisateur ne s'est pas déconnecté correctement.</li>
	<li>Les lignes en noir signalent une session close normalement.</li>
	<li>Les lignes en vert indiquent les sessions en cours (cela peut correspondre à une connexion actuellement close mais pour laquelle l'utilisateur ne s'est pas déconnecté correctement).</li>
</ul>
	
<form action="gestion_connect.php#tab_connexions" id="form_affiche_log" method="post">
<fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png"); '>
	<p>
	Afficher le journal des connexions : 
	<select name="duree2" size="1">
		<option <?php if ($duree2 == '20dernieres') echo "selected = 'selected'"; ?> value='20dernieres'>
			les 20 dernières
		</option>
		<option <?php if ($duree2 == 2) echo "selected = 'selected'"; ?> value='2'>
			depuis Deux jours
		</option>
		<option <?php if ($duree2 == 7) echo "selected = 'selected'"; ?> value='7'>
			depuis Une semaine
		</option>
		<option <?php if ($duree2 == 15) echo "selected = 'selected'"; ?> value='15'>
			depuis Quinze jours
		</option>
		<option <?php if ($duree2 == 30) echo "selected = 'selected'"; ?> value='30'>
			depuis Un mois
		</option>
		<option <?php if ($duree2 == 60) echo "selected = 'selected'"; ?> value='60'>
			depuis Deux mois
		</option>
		<option <?php if ($duree2 == 183) echo "selected = 'selected'"; ?> value='183'>
			depuis Six mois
		</option>
		<option <?php if ($duree2 == 365) echo "selected = 'selected'"; ?> value='365'>
			depuis Un an
		</option>
		<option <?php if ($duree2 == 'all') echo "selected = 'selected'"; ?> value='all'>
			depuis Le début
		</option>
	</select>
	</p>
	<p>
		<input type="submit" name="Valider" value="Valider" />
		<input type="hidden" name="mode_navig" value='$mode_navig' />
	</p>
</fieldset>
</form>
	
<div class='noprint' 
	 style='float: right; border: 1px solid black; background-color: white; width: 3em; padding: .2em; text-align: center;'>
	<a href='<?php echo $_SERVER['PHP_SELF']; ?>?mode=csv'>CSV</a>
</div>

<a name='tab_connexions'></a>
	<table class='boireaus center'>
         <th class="col">Statut</th>
		<th class="col">Identifiant</th>
        <th class="col">Début session</th>
        <th class="col">Fin session</th>
        <th class="col"><a href='gestion_connect.php?order_by=ip<?php if(isset($duree2)){echo "&amp;duree2=$duree2";}?>#tab_connexions'>Adresse IP et nom de la machine cliente</a></th>
        <th class="col">Navigateur</th>
        <th class="col">Provenance</th>
    </tr>

<?php
$requete = '';
$requete1 = '';
if ($duree2 != 'all') {$requete = "where l.START > now() - interval " . $duree2 . " day";}
if ($duree2 == '20dernieres') {$requete1 = "LIMIT 0,20"; $requete='';}

$sql = "select l.LOGIN, concat(prenom, ' ', nom) utili, l.START, l.SESSION_ID, l.REMOTE_ADDR, l.USER_AGENT, l.REFERER,
 l.AUTOCLOSE, l.END, u.email, u.statut
from log l LEFT JOIN utilisateurs u ON l.LOGIN = u.login ".$requete;

$sql.=" order by ";
if(isset($_GET['order_by'])) {
	$order_by=$_GET['order_by'];
	
	if($order_by=='ip') {
		$sql.="l.REMOTE_ADDR, ";
	}
	else {
		unset($order_by);
	}
}
$sql.="START desc ".$requete1;

$day_now   = date("d");
$month_now = date("m");
$year_now  = date("Y");
$hour_now  = date("H");
$minute_now = date("i");
$now = mktime($hour_now, $minute_now, 0, $month_now, $day_now, $year_now);
$res = sql_query($sql);

$ligne_csv[0] = "statut;login;debut_session;fin_session;adresse_ip;navigateur;provenance\n";
$nb_ligne = 1;

if ($res) {
    $alt=1;
    for ($i = 0; ($row = sql_row($res, $i)); $i++) {
        $alt=$alt*(-1);
        $annee_f = mb_substr($row[8],0,4);
        $mois_f =  mb_substr($row[8],5,2);
        $jour_f =  mb_substr($row[8],8,2);
        $heures_f = mb_substr($row[8],11,2);
        $minutes_f = mb_substr($row[8],14,2);
        $secondes_f = mb_substr($row[8],17,2);
        $date_fin_f = $jour_f."/".$mois_f."/".$annee_f." à ".$heures_f."&nbsp;h&nbsp;".$minutes_f;
        $end_time = mktime($heures_f, $minutes_f, $secondes_f, $mois_f, $jour_f, $annee_f);
        $annee_b = mb_substr($row[2],0,4);
        $mois_b =  mb_substr($row[2],5,2);
        $jour_b =  mb_substr($row[2],8,2);
        $heures_b = mb_substr($row[2],11,2);
        $minutes_b = mb_substr($row[2],14,2);
        $secondes_b = mb_substr($row[2],17,2);
        //$date_debut = $jour_b."/".$mois_b."/".$annee_b." à ".$heures_b." h ".$minutes_b;
        $date_debut = $jour_b."/".$mois_b."/".$annee_b." à ".$heures_b."&nbsp;h&nbsp;".$minutes_b;
        $temp1 = '';
        $temp2 = '';
        if ($end_time > $now) {
            $temp1 = "<span style='color:green'>";
            $temp2 = "</span>";
        }
        if ($row[1] == '') {$row[1] = "<span style='color:red;font-weight:bold'>Utilisateur inconnu</span>";}

        echo "<tr class='lig$alt white_hover'>\n";
		 echo "<td class=\"col\"><span class='small'>".$temp1.$row[10].$temp2."</span></td>\n";
        echo "<td class=\"col\"><span class='small'>".$temp1.$row[0]."<br />";
		if($row[9]!='') {
			echo "<a href=\"mailto:" .$row[9]. "\">".$row[1]."</a>";
		}
		else {
			echo $row[1];
		}
		echo $temp2."</span></td>\n";
        echo "<td class=\"col\"><span class='small'>".$temp1.$date_debut.$temp2."</span></td>\n";

		//$ligne_csv[$nb_ligne] = "$row[10];$row[0];$date_debut;";
		$ligne_csv[$nb_ligne] = my_ereg_replace("&nbsp;"," ","$row[10];$row[0];$date_debut;");

        if ($row[7] == 4) {
           echo "<td class=\"col\" style=\"color: red;\"><span class='small'><span style='font-weight:bold'>Tentative de connexion<br />avec mot de passe erroné.</span></span></td>\n";
        } else if ($end_time > $now) {
            echo "<td class=\"col\" style=\"color: green;\"><span class='small'>" .$date_fin_f. "</span></td>\n";
        } else if (($row[7] == 1) or ($row[7] == 2) or ($row[7] == 3)) {
            echo "<td class=\"col\" style=\"color: orange;\"><span class='small'>" .$date_fin_f. "</span></td>\n";
        } else {
            echo "<td class=\"col\"><span class='small'>" .$date_fin_f. "</span></td>";
        }
        if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
            $result_hostbyaddr = " - ".@gethostbyaddr($row[4]);
		}
        else if($active_hostbyaddr == "no_local") {
            if ((mb_substr($row[4],0,3) == 127) or (mb_substr($row[4],0,3) == 10.) or (mb_substr($row[4],0,7) == 192.168)) {
                $result_hostbyaddr = "";
            }
			else{
				$tabip=explode(".",$row[4]);
				if(($tabip[0]==172)&&($tabip[1]>=16)&&($tabip[1]<=31)) {
					$result_hostbyaddr = "";
				}
				else{
	                $result_hostbyaddr = " - ".@gethostbyaddr($row[4]);
				}
			}
		}
		else{
            $result_hostbyaddr = "";
		}
        echo "<td class=\"col\"><span class='small'>".$temp1.$row[4].$result_hostbyaddr.$temp2. "</span></td>\n";
        echo "<td class=\"col\"><span class='small'>".$temp1. detect_browser($row[5]) .$temp2. "</span></td>\n";
        //echo "<td class=\"col\"><span class='small'>".$temp1. $row[6] .$temp2. "</span></td>\n";
        echo "<td class=\"col\"><span class='small'>";
		if($row[6]=="") {
			echo "&nbsp;";
		}
		else {
			echo $temp1. $row[6] .$temp2;
		}
		echo "</span></td>\n";

		//$ligne_csv[$nb_ligne] .= "$date_fin_f;$result_hostbyaddr;".detect_browser($row[5]).";$row[6]\n";
		$ligne_csv[$nb_ligne] .= my_ereg_replace("&nbsp;"," ","$date_fin_f;$result_hostbyaddr;".detect_browser($row[5]).";$row[6]\n");

        echo "</tr>\n";

		$nb_ligne++;

		flush();
    }
}

$_SESSION['donnees_export_csv_log']=$ligne_csv;
?>
</table>
<p>
	<em>NOTES :</em>
</p>
<p>
	La résolution d'adresse IP en nom DNS peut ralentir l'affichage de cette page.
	<br />
	Dans le cas d'un serveur situé sur un réseau local, il se peut qu'aucun serveur DNS ne soit en mesure d'assurer 
	la résolution IP/NOM.
	<br />
	Si l'attente vous pèse, vous pouvez modifier le paramétrage de la variable 
	<span style='font-weight:bold'>$active_hostbyaddr</span> 
	dans le fichier <span style='font-weight:bold'>lib/global.inc.php</span>
</p>

<table class='boireaus'>
	<caption>Valeurs possibles pour la variable :</caption>
	<tr>
		<th>Valeur</th>
		<th>Signification</th>
	</tr>
	<tr class='lig1'>
		<td>all</td>
		<td>la résolution inverse de toutes les adresses IP est activée.<br />
		Cela peut se traduire par des lenteurs à l'affichage de la présente page.
		</td>
	</tr>
	<tr class='lig-1'>
		<td>no</td>
		<td>la résolution inverse des adresses IP est désactivée.<br />
		Radical, mais toutes les adresses fournies sont en IP.</td>
	</tr>
	<tr class='lig1'>
		<td>no_local</td>
		<td>
			la résolution inverse des adresses IP locales (<em>privées</em>) est désactivée.
			<br />
			Seules les adresses IP de 
			<a href='#' onmouseover="afficher_div('ip_priv','y',20,20);" onclick="return false;">réseaux non-privés</a>
			sont traduites en noms DNS.

<?php	
$texte="<p style='text-align:justify;margin: .5em;'>
	L'organisme gérant l'espace d'adressage public (adresses IP routables) est l'Internet Assigned Number Authority (IANA). 
	La RFC 1918 définit un espace d'adressage privé permettant à toute organisation d'attribuer des adresses IP 
	aux machines de son réseau interne sans risque d'entrer en conflit avec une adresse IP publique allouée par l'IANA. 
	Ces adresses dites non-routables correspondent aux plages d'adresses suivantes :</p>
<ul>
<li>Classe A : plage de 10.0.0.0 à 10.255.255.255 ;</li>
<li>Classe B : plage de 172.16.0.0 à 172.31.255.255 ;</li>
<li>Classe C : plage de 192.168.0.0 à 192.168.255.55 ;</li>
</ul>

<p style='text-align:justify;margin: .5em;'>Toutes les machines d'un réseau interne, connectées à internet par l'intermédiaire d'un routeur et ne possédant pas d'adresse IP publique doivent utiliser une adresse contenue dans l'une de ces plages. Pour les petits réseaux domestiques, la plage d'adresses de 192.168.0.1 à 192.168.0.255 est généralement utilisée.</p>";
$tabdiv_infobulle[]=creer_div_infobulle('ip_priv',"Espaces d'adressage","",$texte,"",30,0,'y','y','n','n');

?>
			
		</td>
	</tr>
</table>
<p>
	La valeur actuelle de la variable <span style='font-weight:bold'>$active_hostbyaddr</span> 
	sur votre GEPI est: <span style='font-weight:bold'><?php echo $active_hostbyaddr; ?></span></p>
</div>

	<?php	
require("../lib/footer.inc.php");
?>
