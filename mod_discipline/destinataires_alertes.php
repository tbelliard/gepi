<?php
/*
 *
 * Copyright 2001, 2018 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
// Modif Eric : Table s_alerte_mail à modifier : ajout champs
// ALTER TABLE `s_alerte_mail` ADD `adresse` VARCHAR( 250 ) NULL 
//INSERT INTO droits VALUES ('/mod_discipline/destinataires_alertes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Parametrage des destinataires de mail d alerte', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if(mb_strtolower(mb_substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

$acces_ok="n";
if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiDiscDefinirDestAlertesCpe')))||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiDiscDefinirDestAlertesScol')))) {
	$acces_ok="y";
}
else {
	$msg="Vous n'avez pas le droit de définir les destinataires d'alertes.";
	header("Location: ./index.php?msg=$msg");
	die();
}

//debug_var();

require('sanctions_func_lib.php');

if (isset($_POST['action']) and ($_POST['action'] == "reg_dest")) {
	check_token();

	$msg = '';
	$notok = false;

	$tab_statut=$_POST['tab_statut'];
	$tab_id_clas=$_POST['tab_id_clas'];

	for($j=0;$j<count($tab_id_clas);$j++){
		for($i=0;$i<count($tab_statut);$i++){
			if($tab_statut[$i]!='login') {
				if(isset($_POST['case_'.$i.'_'.$j])){
					$requete= "SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$tab_id_clas[$j]."' AND destinataire='".$tab_statut[$i]."' AND type='mail';";
					//echo $requete; echo "</br>";
					$test=mysqli_query($GLOBALS["mysqli"], $requete);
					if(mysqli_num_rows($test)==0){
						// Modif Eric Ajout Adresse autre
						if(isset($_POST['adresse_'.$i.'_'.$j]) and isset($_POST['case_'.$i.'_'.$j])) {
							$contenu_adresse = $_POST['adresse_'.$i.'_'.$j];
							if ($contenu_adresse != '') {
							$sql="INSERT INTO s_alerte_mail SET id_classe='".$tab_id_clas[$j]."', destinataire='".$tab_statut[$i]."', adresse='".$contenu_adresse."', type='mail';";
							}
						} else {
							$sql="INSERT INTO s_alerte_mail SET id_classe='".$tab_id_clas[$j]."', destinataire='".$tab_statut[$i]."', type='mail';";
						}
						// Fin modif
						$reg_data=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$reg_data){
							$msg.= "Erreur lors de l'insertion d'un nouvel enregistrement $tab_id_clas[$j] pour $tab_statut[$i].<br />";
							$notok = true;
						}
					}
					// Sinon: l'enregistrement est déjà présent.
				}
				else{
					$test=mysqli_query($GLOBALS["mysqli"], "SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$tab_id_clas[$j]."' AND destinataire='".$tab_statut[$i]."' AND type='mail';");
					if(mysqli_num_rows($test)>0){
						$sql="DELETE FROM s_alerte_mail WHERE id_classe='".$tab_id_clas[$j]."' AND destinataire='".$tab_statut[$i]."' AND type='mail';";
						$reg_data=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$reg_data){
							$msg.= "Erreur lors de la suppression de l'enregistrement $tab_id_clas[$j] pour $tab_statut[$i].<br />";
							$notok = true;
						}
					}
				}
			}
			else {
				$login_associes=isset($_POST['individu_classe_'.$tab_id_clas[$j]]) ? $_POST['individu_classe_'.$tab_id_clas[$j]] : array();

				$sql="SELECT login FROM s_alerte_mail WHERE id_classe='".$tab_id_clas[$j]."' AND login!='' AND type='mail';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					while($lig=mysqli_fetch_object($test)) {
						if(!in_array($lig->login, $login_associes)) {
							$sql="DELETE FROM s_alerte_mail WHERE id_classe='".$tab_id_clas[$j]."' AND login='".$lig->login."' AND type='mail';";
							$del=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$del){
								$msg.= "Erreur lors de la suppression de l'association $tab_id_clas[$j] pour ".$lig->login.".<br />";
								$notok = true;
							}
						}
					}
				}
			}
		}
	}

	// 20180203
	$nb_deja=0;
	$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : NULL;
	$id_classe_user_ajout=isset($_POST['id_classe_user_ajout']) ? $_POST['id_classe_user_ajout'] : NULL;
	if((isset($login_user))&&(isset($id_classe_user_ajout))) {
		for($loop_u=0;$loop_u<count($login_user);$loop_u++) {
			for($loop_c=0;$loop_c<count($id_classe_user_ajout);$loop_c++) {
				$sql="SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$id_classe_user_ajout[$loop_c]."' AND login='".$login_user[$loop_u]."' AND type='mail';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$nb_deja++;
				}
				else {
					$sql="INSERT INTO s_alerte_mail SET id_classe='".$id_classe_user_ajout[$loop_c]."', destinataire='', login='".$login_user[$loop_u]."', type='mail';";
					//echo "$sql<br />";
					$reg_data=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$reg_data){
						$msg.= "Erreur lors de l'insertion d'un nouvel enregistrement ".$id_classe_user_ajout[$loop_c]." pour ".$login_user[$loop_u]."'.<br />";
						$notok = true;
					}
				}
			}
		}
	}

	if ($notok == true) {
		$msg .= "Il y a eu des erreurs lors de l'enregistrement des données.<br />";
	} else {
		$msg .= "L'enregistrement des données s'est bien passé.<br />";
	}

	if($nb_deja>0) {
		$msg.=$nb_deja." personnel(s)/classe(s) étaient déjà associés.<br />";
	}
}

if((getSettingAOui('active_mod_alerte'))&&(isset($_POST['action']))&&($_POST['action'] == "reg_dest_mod_alerte")) {
	check_token();

	$msg = '';
	$notok = false;

	$tab_statut_mod_alerte=$_POST['tab_statut_mod_alerte'];
	$tab_id_clas_mod_alerte=$_POST['tab_id_clas_mod_alerte'];

	for($j=0;$j<count($tab_id_clas_mod_alerte);$j++){
		for($i=0;$i<count($tab_statut_mod_alerte);$i++){
			if($tab_statut_mod_alerte[$i]!='login') {
				if(isset($_POST['case_mod_alerte_'.$i.'_'.$j])){
					$sql= "SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$tab_id_clas_mod_alerte[$j]."' AND destinataire='".$tab_statut_mod_alerte[$i]."' AND type='mod_alerte';";
					//echo "$sql<br />";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)==0){
						$sql="INSERT INTO s_alerte_mail SET id_classe='".$tab_id_clas_mod_alerte[$j]."', destinataire='".$tab_statut_mod_alerte[$i]."', type='mod_alerte';";
						//echo "$sql<br />";
						$reg_data=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$reg_data){
							$msg.= "Erreur lors de l'insertion d'un nouvel enregistrement $tab_id_clas_mod_alerte[$j] pour $tab_statut_mod_alerte[$i].<br />";
							$notok = true;
						}
					}
					// Sinon: l'enregistrement est déjà présent.
				}
				else{
					$sql="SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$tab_id_clas_mod_alerte[$j]."' AND destinataire='".$tab_statut_mod_alerte[$i]."' AND type='mod_alerte';";
					//echo "$sql<br />";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)>0){
						$sql="DELETE FROM s_alerte_mail WHERE id_classe='".$tab_id_clas_mod_alerte[$j]."' AND destinataire='".$tab_statut_mod_alerte[$i]."' AND type='mod_alerte';";
						//echo "$sql<br />";
						$reg_data=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$reg_data){
							$msg.= "Erreur lors de la suppression de l'enregistrement $tab_id_clas_mod_alerte[$j] pour $tab_statut_mod_alerte[$i].<br />";
							$notok = true;
						}
					}
				}
			}
			else {
				$login_associes=isset($_POST['individu_classe_mod_alerte_'.$tab_id_clas_mod_alerte[$j]]) ? $_POST['individu_classe_mod_alerte_'.$tab_id_clas_mod_alerte[$j]] : array();

				$sql="SELECT login FROM s_alerte_mail WHERE id_classe='".$tab_id_clas_mod_alerte[$j]."' AND login!='' AND type='mod_alerte';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					while($lig=mysqli_fetch_object($test)) {
						if(!in_array($lig->login, $login_associes)) {
							$sql="DELETE FROM s_alerte_mail WHERE id_classe='".$tab_id_clas_mod_alerte[$j]."' AND login='".$lig->login."' AND type='mod_alerte';";
							$del=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$del){
								$msg.= "Erreur lors de la suppression de l'association $tab_id_clas_mod_alerte[$j] pour ".$lig->login.".<br />";
								$notok = true;
							}
						}
					}
				}
			}
		}
	}

	// 20180203
	$nb_deja=0;
	$login_user=isset($_POST['login_user_mod_alerte']) ? $_POST['login_user_mod_alerte'] : NULL;
	$id_classe_user_ajout=isset($_POST['id_classe_user_ajout_mod_alerte']) ? $_POST['id_classe_user_ajout_mod_alerte'] : NULL;
	if((isset($login_user))&&(isset($id_classe_user_ajout))) {
		for($loop_u=0;$loop_u<count($login_user);$loop_u++) {
			for($loop_c=0;$loop_c<count($id_classe_user_ajout);$loop_c++) {
				$sql="SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$id_classe_user_ajout[$loop_c]."' AND login='".$login_user[$loop_u]."' AND type='mod_alerte';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$nb_deja++;
				}
				else {
					$sql="INSERT INTO s_alerte_mail SET id_classe='".$id_classe_user_ajout[$loop_c]."', destinataire='', login='".$login_user[$loop_u]."', type='mod_alerte';";
					//echo "$sql<br />";
					$reg_data=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$reg_data){
						$msg.= "Erreur lors de l'insertion d'un nouvel enregistrement ".$id_classe_user_ajout[$loop_c]." pour ".$login_user[$loop_u]."'.<br />";
						$notok = true;
					}
				}
			}
		}
	}

	if ($notok == true) {
		$msg .= "Il y a eu des erreurs lors de l'enregistrement des données";
	} else {
		$msg .= "L'enregistrement des données s'est bien passé.";
	}

	if($nb_deja>0) {
		$msg.=$nb_deja." personnel(s)/classe(s) étaient déjà associés.<br />";
	}
}


$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
$titre_page = "Destinataires des alertes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************
//debug_var();
// Cette page a été ouverte en target='blank' depuis une autre page (par exemple /eleves/modify_eleve.php)
// Après modification éventuelle, il faut quitter cette page.
echo "<p class='bold'>";
echo "<a href='index.php' onClick=\"if(confirm_abandon (this, change, '$themessage')){self.close()};return false;\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";
?>

<?php
	if(getSettingAOui('active_mod_alerte')) {
		echo "<h2>Destinataires des alertes/signalements</h2>
<p>Lors de la saisie d'".$mod_disc_terme_incident."s, il est possible d'alerter des personnels de deux façons&nbsp;:</p>
<ul>
	<li><p>par l'envoi de mail</p></li>
	<li><p>en enregistrant un message dans le module Alertes</p></li>
</ul>
<p style='margin-bottom:1em;'>Les deux formulaires ci-dessous vous permettent de choisir les destinataires.</p>\n";
	}

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>
	<fieldset class='fieldset_opacite50' style='margin-bottom:1em;'>
		<h2>Envoi de mails d'alerte</h2>
		<p>Choisissez les destinataires des mails d'alerte pour des ".$mod_disc_terme_incident."s dont des élèves sont protagonistes.</p>\n";
	echo add_token_field();

	//Ajout Eric
	$contenu_adresse = "";

	$tab_statut=array('cpe', 'tous_cpe', 'scolarite', 'pp', 'professeurs', 'administrateur', 'login', 'mail');

	//echo "<table border='1'>\n";
	echo "<table class='boireaus boireaus_alt'>\n";
	//#96C8F0
	$ligne_statuts="<tr style='background-color:#FAFABE;'>\n";
	//$ligne_comptes_scol.="<td style='text-align:center; font-weight:bold;'>Comptes</td>\n";
	$ligne_statuts.="<th style='text-align:center; font-weight:bold;'>Statuts</th>\n";
	$ligne_statuts.="<th>CPE</th>\n";
	$ligne_statuts.="<th>Tous les CPE</th>\n";
	$ligne_statuts.="<th>Scolarité<br />responsable<br />de la classe</th>\n";
	$gepi_prof_suivi=ucfirst(getSettingValue("gepi_prof_suivi"));
	$ligne_statuts.="<th>".$gepi_prof_suivi."</th>\n";
	$ligne_statuts.="<th>Professeurs<br />de la classe</th>\n";
	$ligne_statuts.="<th>Administrateurs</th>\n";
	$ligne_statuts.="<th title='Liste de personnels spécifiquement choisis (par exemple Assistante Sociale, Infirmière,...)'>Personnels en particulier</th>\n";
	$ligne_statuts.="<th>Autre adresse mail<br/>(Cocher puis saisir directement l'adresse)</th>\n"; 
	$ligne_statuts.="<th>\n";
	$ligne_statuts.="&nbsp;\n";
	$ligne_statuts.="</th>\n";
	$ligne_statuts.="</tr>\n";
	echo $ligne_statuts;

	echo "<tr style='background-color:#FAFABE;'>\n";
	echo "<th style='text-align:center; font-weight:bold;'>Classes</th>\n";
	for($i=0;$i<count($tab_statut);$i++){
		echo "<th style='text-align:center;'>\n";

		if($tab_statut[$i]!='login') {
			echo "<a href=\"javascript:modif_case($i,true,'col');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
			echo "<a href=\"javascript:modif_case($i,false,'col');changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
		}

		echo "<input type='hidden' name='tab_statut[$i]' value='".$tab_statut[$i]."' />";
		//echo "</td>\n";
		echo "</th>\n";
	}
	echo "<th>&nbsp;</th>\n";
	echo "</tr>\n";

	$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes ORDER BY classe");
	$nombre_lignes = mysqli_num_rows($call_data);
	
	if ($nombre_lignes != 0) {
		// Lignes classes...
		$j=0;
		$alt=1;
		$cpt_individu_classe=0;
		while($lig_clas=mysqli_fetch_object($call_data)){
			if(($j%10==0)&&$j>0){echo $ligne_statuts;}

			$alt=$alt*(-1);

			//if($j%2==0){$bgcolor="style='background-color: gray;'";}else{$bgcolor='';}
			//echo "<tr $bgcolor>\n";
			//echo "<tr class='lig$alt white_hover'>\n";
			echo "<tr class='white_hover'>\n";
			echo "<td style='text-align:center;'>";
			echo "<input type='hidden' name='tab_id_clas[$j]' value='$lig_clas->id' />\n";
			echo "$lig_clas->classe";
			echo "</td>\n";
			for($i=0;$i<count($tab_statut);$i++){
				$sql="SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$lig_clas->id."' AND destinataire='".$tab_statut[$i]."' AND type='mail';";
				//echo "$sql<br />";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				//if(mysql_num_rows($test)==0){$checked="";$bgcolor="";}else{$checked="checked ";$bgcolor="background-color: #AAE6AA;";}
				if(mysqli_num_rows($test)==0){$checked="";$bgcolor="";}else{$checked="checked ";$bgcolor="background-color: plum;";}

				if($tab_statut[$i]=='login') {
					echo "<td style='text-align:left;$bgcolor'>\n";
					// 20180203
					// Récupérer la liste des logins et mettre un tableau avec checkbox/Nom_prénom
					$sql="SELECT u.nom, u.prenom, u.login, u.statut FROM utilisateurs u, s_alerte_mail sam WHERE sam.id_classe='".$lig_clas->id."' AND sam.login=u.login AND sam.type='mail' ORDER BY u.nom, u.prenom;";
					//echo "$sql<br />";
					$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_u)>0) {
						//echo "<table class='boireaus boireaus_alt2' style='font-size:small;'>";
						while($lig_u=mysqli_fetch_object($res_u)) {
							/*
							echo "
						<tr>
							<td>
								<input type='checkbox' name='individu_classe[]' id='individu_classe_".$cpt_individu_classe."' value=\"".$lig_u->login."|".$lig_clas->id."\" onchange='changement();' checked />
							</td>
							<td title=\"".$lig_u->nom." ".$lig_u->prenom." (".$lig_u->statut.")\">".$lig_u->nom." ".$lig_u->prenom."</td>
						</tr>";

							echo "
								<input type='checkbox' name='individu_classe[]' id='individu_classe_".$cpt_individu_classe."' value=\"".$lig_u->login."|".$lig_clas->id."\" onchange='changement();checkbox_change(this.id);' checked />&nbsp;<label for='individu_classe_".$cpt_individu_classe."' id='texte_individu_classe_".$cpt_individu_classe."' style='font-weight:bold;' title=\"".$lig_u->nom." ".$lig_u->prenom." (".$lig_u->statut.")\">".$lig_u->nom." ".$lig_u->prenom."</label><br />";
							*/
							echo "
								<input type='checkbox' name='individu_classe_".$lig_clas->id."[]' id='individu_classe_".$cpt_individu_classe."' value=\"".$lig_u->login."\" onchange='changement();checkbox_change(this.id);' checked />&nbsp;<label for='individu_classe_".$cpt_individu_classe."' id='texte_individu_classe_".$cpt_individu_classe."' style='font-weight:bold;' title=\"".$lig_u->nom." ".$lig_u->prenom." (".$lig_u->statut.")\">".$lig_u->nom." ".$lig_u->prenom."</label><br />";
							$cpt_individu_classe++;
						}
						//echo "</table>";
					}
				}
				else {
					echo "<td style='text-align:center;$bgcolor'>\n";
					echo "<input type='checkbox' name='case_".$i."_".$j."' id='case_".$i."_".$j."' value='y' onchange='changement();' $checked/>\n";
					//Ajout Eric traitement autre mail
					$sql="SELECT * FROM s_alerte_mail WHERE id_classe='".$lig_clas->id."' AND destinataire='mail' AND type='mail';";
					//echo $sql;
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)!=0) {
						$contenu_requete=mysqli_fetch_object($test);
						if ($tab_statut[$i]== 'mail') {
						    if ($contenu_requete->adresse != NULL) {
							    $contenu_adresse = $contenu_requete->adresse;
							} else { 
							    $contenu_adresse = '';
							}
							echo "Adresse : <input type='text' name='adresse_".$i."_".$j."' value='$contenu_adresse' onchange='changement();' />\n";    
						} 
					} else if ($tab_statut[$i]== 'mail') echo "Adresse : <input type='text' name='adresse_".$i."_".$j."' value='' onchange='changement();' />\n";
				}
				echo "</td>\n";
			}
			echo "<td>\n";
			echo "<a href=\"javascript:modif_case($j,true,'lig');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
			//echo "<a href='javascript:modif_case($i,false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
			echo "<a href=\"javascript:modif_case($j,false,'lig');changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
			echo "</td>\n";
			echo "</tr>\n";
			$j++;
		}

		echo "</table>\n";

		// 20180203
		echo js_checkbox_change_style('checkbox_change', 'texte_', "y");
		/*
		echo "<div onmouseover=\"document.getElementById('div_liste_user').style.display=''\" onmouseout=\"document.getElementById('div_liste_user').style.display='none'\" class='fieldset_opacite50' style='margin-top:1em;margin-bottom:1em; padding:0.5em;'>
		<p class='bold'>Ajouter un ou des personnels en particulier&nbsp;: <img src='../images/icons/chercher.png' class='icone16' alt='Choisir' /></p>
		<div id='div_liste_user' style='display:none'>
			".liste_checkbox_utilisateurs(array("administrateur", "scolarite", "cpe", "professeur", "autre"))."
			<p style='margin-top:1em' class='bold'>Ajouter les destinataires choisis ci-dessus pour la ou les classes suivantes&nbsp;:</p>
			".liste_checkbox_classes(array(), 'id_classe_user_ajout', 'cocher_decocher_classes')."
			<p><a href=\"javascript:cocher_decocher_classes(true)\">Cocher</a> / <a href=\"javascript:cocher_decocher_classes(false)\">décocher</a> toutes les classes.</p>
		</div>
	</div>";
		*/
		echo "<div class='fieldset_opacite50' style='margin-top:1em;margin-bottom:1em; padding:0.5em;'>
		<p class='bold'>Ajouter un ou des personnels en particulier&nbsp;: <a href='javascript:alterne_affichage_choix_user_classes_mail()'><img src='../images/icons/chercher.png' class='icone16' alt='Choisir' /></a></p>
		<div id='div_liste_user' style='display:none'>
			".liste_checkbox_utilisateurs(array("administrateur", "scolarite", "cpe", "professeur", "autre"))."
			<p style='margin-top:1em' class='bold'>Ajouter les destinataires choisis ci-dessus pour la ou les classes suivantes&nbsp;:</p>
			".liste_checkbox_classes(array(), 'id_classe_user_ajout', 'cocher_decocher_classes')."
			<p><a href=\"javascript:cocher_decocher_classes(true)\">Cocher</a> / <a href=\"javascript:cocher_decocher_classes(false)\">décocher</a> toutes les classes.</p>
		</div>
	</div>

	<script type='text/javascript'>
		function alterne_affichage_choix_user_classes_mail() {
			if(document.getElementById('div_liste_user').style.display=='') {
				document.getElementById('div_liste_user').style.display='none';
			}
			else {
				document.getElementById('div_liste_user').style.display='';
			}
		}
	</script>";

		echo "<input type='hidden' name='action' value='reg_dest' />\n";
		echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";

		//============================================
		echo "
		<p style='text-indent:-4em;margin-left:4em;'><em>NOTES&nbsp;:</em> Les destinataires (<em>sauf 'Adresses autres'</em>) peuvent choisir dans 'Mon compte' pour quelles catégories d'incidents ils souhaitent être informés.<br />
		Un utilisateur, le chef d'établissement par exemple, pourra souhaiter être informé des violences,... mais pas d'incidents plus mineurs.</p>

		</fieldset>
		</form>

		<script type='text/javascript' language='javascript'>
			function modif_case(id,statut,mode){
				// id: numéro de:
				//					. colonne correspondant au login
				//					. ligne
				// statut: true ou false
				// mode: col ou lig
				if(mode=='col'){
					for(k=0;k<$nombre_lignes;k++){
						if(document.getElementById('case_'+id+'_'+k)){
							document.getElementById('case_'+id+'_'+k).checked=statut;
						}
					}
				}
				else{
					for(k=0;k<".count($tab_statut).";k++){
						if(document.getElementById('case_'+k+'_'+id)){
							document.getElementById('case_'+k+'_'+id).checked=statut;
						}
					}
				}
				changement();
			}
		</script>\n";
		//============================================

		if(getSettingAOui('active_mod_alerte')) {

			echo "<form action='".$_SERVER['PHP_SELF']."' name='form2' method='post'>
	<fieldset class='fieldset_opacite50'>
		<h2>Envoi de messages dans le module Alertes</h2>
		<p>Choisissez les destinataires d'alertes dans le module Alertes pour des ".$mod_disc_terme_incident."s dont des élèves sont protagonistes.</p>\n";

			echo add_token_field();

			$tab_statut=array('cpe', 'tous_cpe', 'scolarite', 'pp', 'professeurs', 'administrateur', 'login');

			//echo "<table border='1'>\n";
			echo "<table class='boireaus boireaus_alt'>\n";
			//#96C8F0
			$ligne_statuts="<tr style='background-color:#FAFABE;'>\n";
			$ligne_statuts.="<th style='text-align:center; font-weight:bold;'>Statuts</th>\n";
			$ligne_statuts.="<th title=\"CPE chargé du suivi de l'élève\">CPE</th>\n";
			$ligne_statuts.="<th>Tous les CPE</th>\n";
			$ligne_statuts.="<th>Scolarité<br />responsable<br />de la classe</th>\n";
			$gepi_prof_suivi=ucfirst(getSettingValue("gepi_prof_suivi"));
			$ligne_statuts.="<th>".$gepi_prof_suivi."</th>\n";
			$ligne_statuts.="<th>Professeurs<br />de la classe</th>\n";
			$ligne_statuts.="<th>Administrateurs</th>\n";
			$ligne_statuts.="<th title='Liste de personnels spécifiquement choisis (par exemple Assistante Sociale, Infirmière,...)'>Personnels en particulier</th>\n";
			$ligne_statuts.="<th>\n";
			$ligne_statuts.="&nbsp;\n";
			$ligne_statuts.="</th>\n";
			$ligne_statuts.="</tr>\n";
			echo $ligne_statuts;

			echo "<tr style='background-color:#FAFABE;'>\n";
			echo "<th style='text-align:center; font-weight:bold;'>Classes</th>\n";
			for($i=0;$i<count($tab_statut);$i++){
				echo "<th style='text-align:center;'>\n";

				if($tab_statut[$i]!='login') {
					echo "<a href=\"javascript:modif_case_mod_alerte($i,true,'col');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
					echo "<a href=\"javascript:modif_case_mod_alerte($i,false,'col');changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
				}

				echo "<input type='hidden' name='tab_statut_mod_alerte[$i]' value='".$tab_statut[$i]."' />";
				//echo "</td>\n";
				echo "</th>\n";
			}
			echo "<th>&nbsp;</th>\n";
			echo "</tr>\n";

			$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes ORDER BY classe");
			$nombre_lignes = mysqli_num_rows($call_data);
	
			if ($nombre_lignes != 0) {
				// Lignes classes...
				$j=0;
				$alt=1;
				while($lig_clas=mysqli_fetch_object($call_data)){
					if(($j%10==0)&&$j>0){echo $ligne_statuts;}

					$alt=$alt*(-1);

					//if($j%2==0){$bgcolor="style='background-color: gray;'";}else{$bgcolor='';}
					//echo "<tr $bgcolor>\n";
					//echo "<tr class='lig$alt white_hover'>\n";
					echo "<tr class='white_hover'>\n";
					echo "<td style='text-align:center;'>";
					echo "<input type='hidden' name='tab_id_clas_mod_alerte[$j]' value='$lig_clas->id' />\n";
					echo "$lig_clas->classe";
					echo "</td>\n";
					for($i=0;$i<count($tab_statut);$i++){

						if($tab_statut[$i]=='login') {
							echo "<td style='text-align:left;$bgcolor'>\n";
							// 20180203
							// Récupérer la liste des logins et mettre un tableau avec checkbox/Nom_prénom
							$sql="SELECT u.nom, u.prenom, u.login, u.statut FROM utilisateurs u, s_alerte_mail sam WHERE sam.id_classe='".$lig_clas->id."' AND sam.login=u.login AND sam.type='mod_alerte' ORDER BY u.nom, u.prenom;";
							//echo "$sql<br />";
							$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_u)>0) {
								while($lig_u=mysqli_fetch_object($res_u)) {
									echo "
										<input type='checkbox' name='individu_classe_mod_alerte_".$lig_clas->id."[]' id='individu_classe_mod_alerte_".$cpt_individu_classe."' value=\"".$lig_u->login."\" onchange='changement();checkbox_change(this.id);' checked />&nbsp;<label for='individu_classe_mod_alerte_".$cpt_individu_classe."' id='texte_individu_classe_mod_alerte_".$cpt_individu_classe."' style='font-weight:bold;' title=\"".$lig_u->nom." ".$lig_u->prenom." (".$lig_u->statut.")\">".$lig_u->nom." ".$lig_u->prenom."</label><br />";
									$cpt_individu_classe++;
								}
							}
							echo "</td>\n";
						}
						else {
							$sql="SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$lig_clas->id."' AND destinataire='".$tab_statut[$i]."' AND type='mod_alerte';";
							//echo "$sql<br />";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							//if(mysql_num_rows($test)==0){$checked="";$bgcolor="";}else{$checked="checked ";$bgcolor="background-color: #AAE6AA;";}
							if(mysqli_num_rows($test)==0){$checked="";$bgcolor="";}else{$checked="checked ";$bgcolor="background-color: plum;";}

							echo "<td style='text-align:center;$bgcolor'>\n";
							echo "<input type='checkbox' name='case_mod_alerte_".$i."_".$j."' id='case_mod_alerte_".$i."_".$j."' value='y' onchange='changement();' $checked/>\n";
							echo "</td>\n";
						}
					}
					echo "<td>\n";
					echo "<a href=\"javascript:modif_case_mod_alerte($j,true,'lig');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
					//echo "<a href='javascript:modif_case($i,false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
					echo "<a href=\"javascript:modif_case_mod_alerte($j,false,'lig');changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
					echo "</td>\n";
					echo "</tr>\n";
					$j++;
				}

				echo "</table>\n";

				echo "<div class='fieldset_opacite50' style='margin-top:1em;margin-bottom:1em; padding:0.5em;'>
		<p class='bold'>Ajouter un ou des personnels en particulier&nbsp;: <a href='javascript:alterne_affichage_choix_user_classes_mod_alerte()'><img src='../images/icons/chercher.png' class='icone16' alt='Choisir' /></a></p>
		<div id='div_liste_user_mod_alerte' style='display:none'>
			".liste_checkbox_utilisateurs(array("administrateur", "scolarite", "cpe", "professeur", "autre"), array(), 'login_user_mod_alerte')."
			<p style='margin-top:1em' class='bold'>Ajouter les destinataires choisis ci-dessus pour la ou les classes suivantes&nbsp;:</p>
			".liste_checkbox_classes(array(), 'id_classe_user_ajout_mod_alerte', 'cocher_decocher_classes_mod_alerte')."
			<p><a href=\"javascript:cocher_decocher_classes_mod_alerte(true)\">Cocher</a> / <a href=\"javascript:cocher_decocher_classes_mod_alerte(false)\">décocher</a> toutes les classes.</p>
		</div>
	</div>

	<script type='text/javascript'>
		function alterne_affichage_choix_user_classes_mod_alerte() {
			if(document.getElementById('div_liste_user_mod_alerte').style.display=='') {
				document.getElementById('div_liste_user_mod_alerte').style.display='none';
			}
			else {
				document.getElementById('div_liste_user_mod_alerte').style.display='';
			}
		}
	</script>";

				echo "<input type='hidden' name='action' value='reg_dest_mod_alerte' />\n";
				echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";

				//============================================
				echo "
		<!--p style='text-indent:-4em;margin-left:4em;'><em>NOTES&nbsp;:</em> Les destinataires (<em>sauf 'Adresses autres'</em>) peuvent choisir dans 'Mon compte' pour quelles catégories d'incidents ils souhaitent être informés.<br />
		Un utilisateur, le chef d'établissement par exemple, pourra souhaiter être informé des violences,... mais pas d'incidents plus mineurs.</p-->

		</fieldset>
		</form>

		<script type='text/javascript' language='javascript'>
			function modif_case_mod_alerte(id,statut,mode){
				// id: numéro de:
				//					. colonne correspondant au login
				//					. ligne
				// statut: true ou false
				// mode: col ou lig
				if(mode=='col'){
					for(k=0;k<$nombre_lignes;k++){
						if(document.getElementById('case_mod_alerte_'+id+'_'+k)){
							document.getElementById('case_mod_alerte_'+id+'_'+k).checked=statut;
						}
					}
				}
				else{
					for(k=0;k<".count($tab_statut).";k++){
						if(document.getElementById('case_mod_alerte_'+k+'_'+id)){
							document.getElementById('case_mod_alerte_'+k+'_'+id).checked=statut;
						}
					}
				}
				changement();
			}
		</script>\n";
			}
		}
	} else {
		echo "</table>\n";
		echo "<p class='grand'><b>Attention :</b> aucune classe n'a été définie dans la base GEPI !</p>\n";
	}
?>
<?php require("../lib/footer.inc.php");?>
