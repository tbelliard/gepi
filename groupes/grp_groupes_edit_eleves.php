<?php
/*
 *
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

$sql="SELECT 1=1 FROM droits WHERE id='/groupes/grp_groupes_edit_eleves.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/groupes/grp_groupes_edit_eleves.php',
administrateur='F',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Edition des élèves des groupes de groupes',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!acces_modif_liste_eleves_grp_groupes()) {
	header("Location: ../accueil.php?msg=Accès non autorisé");
	die();
}

$msg="";

$groupe_de_groupes=getSettingValue('denom_groupe_de_groupes');
if($groupe_de_groupes=="") {
	$groupe_de_groupes="groupe de groupes";
}

$groupes_de_groupes=getSettingValue('denom_groupes_de_groupes');
if($groupes_de_groupes=="") {
	$groupes_de_groupes="groupes de groupes";
}

$id_grp_groupe = isset($_POST['id_grp_groupe']) ? $_POST['id_grp_groupe'] : (isset($_GET['id_grp_groupe']) ? $_GET['id_grp_groupe'] : null);

if((isset($id_grp_groupe))&&(!acces_modif_liste_eleves_grp_groupes("", $id_grp_groupe))) {
	$msg="Vous n'administrez pas le $groupe_de_groupes n°$id_grp_groupe<br />";
	unset($id_grp_groupe);
}


$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "classe");

if(isset($id_groupe)) {
	if(!acces_modif_liste_eleves_grp_groupes($id_groupe)) {
		$msg="Vous n'administrez pas le groupe n°$id_groupe<br />";
		unset($id_groupe);
	}
	elseif(!isset($id_grp_groupe)) {
		$sql="SELECT ggg.id_grp_groupe FROM grp_groupes_groupes ggg, grp_groupes_admin gga WHERE gga.id_grp_groupe=ggg.id_grp_groupe AND gga.login='".$_SESSION['login']."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$id_grp_groupe=$lig->id_grp_groupe;
		}
	}
}

if((isset($id_groupe))&&(isset($id_grp_groupe))&&(isset($_POST['modifier_liste_eleves']))) {
	check_token();

	// A FAIRE : RELEVER LES MODIFICATIONS ET ENVOYER UN MAIL AUX ADMINISTRATEURS ET A getSettingValue('email_dest_info_erreur_affect_grp')

	$current_group = get_group($id_groupe);

	$reg_nom_groupe = $current_group["name"];
	$reg_nom_complet = $current_group["description"];
	$reg_matiere = $current_group["matiere"]["matiere"];
	$reg_clazz = $current_group["classes"]["list"];
	$reg_professeurs = (array)$current_group["profs"]["list"];

	$old_reg_eleves=(array)$current_group["eleves"];

	// On vide les signalements par un prof lors de l'enregistrement
	$sql="DELETE FROM j_signalement WHERE id_groupe='$id_groupe' AND nature='erreur_affect';";
	//echo "$sql<br />";
	$del=mysqli_query($GLOBALS["mysqli"], $sql);

	$login_eleve=$_POST['login_eleve'];

	$reg_eleves = array();
	$ajout_eleves = array();
	$suppr_eleves = array();

	// On travaille période par période
	foreach($current_group["periodes"] as $period) {
		$reg_eleves[$period["num_periode"]]=array();

		for($i=0;$i<count($login_eleve);$i++) {
			if(isset($_POST['eleve_'.$period["num_periode"].'_'.$i])) {
				$id=$login_eleve[$i];
				$reg_eleves[$period["num_periode"]][] = $id;

				if(!in_array($id, $old_reg_eleves[$period["num_periode"]])) {
					$ajout_eleves[$period["num_periode"]][]=$id;
				}
			}
		}

		for($i=0;$i<count($old_reg_eleves[$period["num_periode"]]['list']);$i++) {
			$current_eleve_login=$old_reg_eleves[$period["num_periode"]]['list'][$i];
			if(!in_array($current_eleve_login, $reg_eleves[$period["num_periode"]])) {
				if(!test_before_eleve_removal($current_eleve_login, $id_groupe, $period["num_periode"])) {
					$reg_eleves[$period["num_periode"]][]=$current_eleve_login;
					$msg.="Désinscription impossible&nbsp;: ".get_nom_prenom_eleve($current_eleve_login)." a des notes/appréciations sur le bulletin.<br />";
				}
				else {
					$suppr_eleves[$period["num_periode"]][]=$current_eleve_login;
				}
			}
		}
	}

	// Test à revoir... on ne va pas pouvoir vider un groupe...
	if(count($reg_eleves)!=0) {
		// ==================================================
		if((count($ajout_eleves)>0)||(count($suppr_eleves)>0)) {
			$envoi_mail_actif=getSettingValue('envoi_mail_actif');
			if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
				$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
			}

			if(($envoi_mail_actif=='y')&&
			((getSettingValue('gepiAdminAdress')!="")||(getSettingValue('email_dest_info_erreur_affect_grp')!=""))) {
				$texte_mail="Bonjour,

L'enseignement ".$current_group['name']." (".$current_group['description'].") en classe de ".$current_group['classlist_string']." (".$current_group['proflist_string'].") a été modifié par ".$_SESSION['prenom']." ".$_SESSION['nom']."\n\n";

				if(count($ajout_eleves)>0) {
					foreach($ajout_eleves as $current_periode => $tab_ajout) {
						$texte_mail.="Ajout d'élèves en période $current_periode:\n";
						for($loop=0;$loop<count($tab_ajout);$loop++) {
							if($loop>0) {$texte_mail.=", ";}
							$texte_mail.=get_nom_prenom_eleve($tab_ajout[$loop]);
						}
						$texte_mail.="\n\n";
					}
				}

				if(count($suppr_eleves)>0) {
					foreach($suppr_eleves as $current_periode => $tab_suppr) {
						$texte_mail.="Suppression d'élèves en période $current_periode:\n";
						for($loop=0;$loop<count($tab_suppr);$loop++) {
							if($loop>0) {$texte_mail.=", ";}
							$texte_mail.=get_nom_prenom_eleve($tab_suppr[$loop]);
						}
						$texte_mail.="\n\n";
					}
				}

				if(getSettingValue('url_racine_gepi')!="") {
					$texte_mail.="Voir la liste des élèves de l'enseignement : ".getSettingValue('url_racine_gepi')."/groupes/edit_eleves.php?id_groupe=".$current_group["id"]." \n(vous devez être connecté(e) dans GEPI avant de cliquer sur ce lien).\n";
				}

				$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
				if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}

				$ajout_header="";
				$email_declarant=retourne_email($_SESSION['login']);
				if(($email_declarant!="")&&(check_mail($email_declarant))) {
					$ajout_header.="Cc: $nom_declarant <".$email_declarant.">\r\n";
					$tab_param_mail['cc'][]=$email_declarant;
					$tab_param_mail['cc_name'][]=$nom_declarant;
				}

				$destinataire_mail=getSettingValue("gepiAdminAdress");
				if(check_mail($destinataire_mail)) {
					$tab_param_mail['destinataire'][]=$destinataire_mail;
				}
				$email_dest_info_erreur_affect_grp=getSettingValue('email_dest_info_erreur_affect_grp');
				if(($email_dest_info_erreur_affect_grp!="")&&(check_mail($email_dest_info_erreur_affect_grp))&&($email_dest_info_erreur_affect_grp!=getSettingValue("gepiAdminAdress"))) {
					if($destinataire_mail!="") {$destinataire_mail.=",";}
					$destinataire_mail.=getSettingValue("email_dest_info_erreur_affect_grp");
					$tab_param_mail['destinataire'][]=getSettingValue("email_dest_info_erreur_affect_grp");
				}

				$sujet_mail=$gepiPrefixeSujetMail."[GEPI]: Modification de la liste des élèves d un groupe";
				$envoi = envoi_mail($sujet_mail, $texte_mail, $destinataire_mail, $ajout_header, "plain", $tab_param_mail);
			}
		}
		// ==================================================





		$create = update_group($id_groupe, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves);
		if (!$create) {
			$msg .= "Erreur lors de la mise à jour du groupe.";
		} else {
			$msg .= "Le groupe a bien été mis à jour.";
		}
	}

}

//$avec_js_et_css_edt="y";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
$titre_page = "Élèves des groupes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

//debug_var();

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' name='form_passage_a_un_autre_groupe' method='post'>
<p class='bold'><a href='accueil.php' title=\"Retour à l'accueil\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if(!isset($id_grp_groupe)) {
	echo "</p>
</form>

<h2>Choix du $groupe_de_groupes</h2>

<p>Vous êtes autorisé à modifier la liste des élèves des enseignements des $groupes_de_groupes suivants&nbsp;:</p>";

	$sql="SELECT DISTINCT id_grp_groupe FROM grp_groupes_admin WHERE login='".$_SESSION['login']."';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		// Les tests sont faits plus haut... on ne devrait pas arriver là.

		echo "
<p> style='color:red'>Aucun $groupe_de_groupes trouvé.</p>";

		require("../lib/footer.inc.php");
		die();
	}

	echo "
<ul>";
	while($lig=mysqli_fetch_object($res)) {
		$current_grp_groupes=get_tab_grp_groupes($lig->id_grp_groupe, array('classes', 'profs'));
		echo "
	<li style='margin-bottom:1em;'>
		<p>
			<span title=\"Modifier le élèves inscrits dans des enseignements du $groupe_de_groupes n°$lig->id_grp_groupe

".$current_grp_groupes['description']."\">".$current_grp_groupes['nom_court']." (<em style='font-size:small'>".$current_grp_groupes['nom_complet']."</em>)</span>
			<br />
			<a href='repartition_ele_grp.php?id_grp_groupe=$lig->id_grp_groupe'>Répartir les élèves entre les groupes</a><br />
			ou modifier les inscriptions pour un des groupes suivants&nbsp;:
		</p>
		<ul>";
		foreach($current_grp_groupes['groupes'] as $cpt => $current_group) {
			echo "
			<li>
				<a href='".$_SERVER['PHP_SELF']."?id_grp_groupe=$lig->id_grp_groupe&amp;id_groupe=".$current_group['id']."' title=\"Modifier la liste des élèves de ".$current_group['name']." (".$current_group['description'].")
Classes     : ".$current_group['classlist_string']."
Professeurs : ".$current_group['profs']['proflist_string']."\">".$current_group['name']." (<em style='font-size:small;'>".$current_group['description']." en ".$current_group['classlist_string']." avec ".$current_group['profs']['proflist_string']."</em>)</a>
			</li>";
		}
		echo "
		</ul>
	</li>";
	}
	echo "
</ul>";

	require("../lib/footer.inc.php");
	die();

}

// Le groupe de groupes et le groupe sont choisis
$current_grp_groupes=get_tab_grp_groupes($id_grp_groupe);
$current_group=get_group($id_groupe);

/*
echo "<pre>";
print_r($current_grp_groupes);
echo "</pre>";
*/

if (count($current_group["classes"]["list"]) > 1) {
	$multiclasses = true;
} else {
	$multiclasses = false;
}

$nb_periode=$current_group['nb_periode'];

$reg_eleves = array();
foreach ($current_group["periodes"] as $period) {
	if($period["num_periode"]!=""){
		$reg_eleves[$period["num_periode"]] = $current_group["eleves"][$period["num_periode"]]["list"];
	}
}

$tab_sig=array();
$sql="SELECT * FROM j_signalement WHERE id_groupe='$id_groupe' AND nature='erreur_affect' ORDER BY periode, login;";
//echo "$sql<br />";
$res_sig=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_sig)>0) {
	while($lig_sig=mysqli_fetch_object($res_sig)) {
		$tab_sig[$lig_sig->periode][$lig_sig->login]=my_ereg_replace("_"," ",$lig_sig->valeur)." selon ".civ_nom_prenom($lig_sig->declarant);
	}
}


$cpt=0;
$chaine_periode_ouverte="<p>La période courante est";
foreach($current_group['classes']['list'] as $current_id_classe) {
	if($cpt>0) {
		$chaine_periode_ouverte.=", ";
	}
	$current_periode=cherche_periode_courante($current_id_classe);
	if($current_periode=="") {
		$chaine_periode_ouverte.=" <span style='color:red' title=\"Période non trouvée ???\">???</span> (<em>".get_nom_classe($current_id_classe)."</em>)";
	}
	else {
		$chaine_periode_ouverte.=" la <strong>période $current_periode</strong> (<em>".get_nom_classe($current_id_classe)."</em>)";
	}
	$cpt++;
}
$chaine_periode_ouverte.="</p>";

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre groupe/enseignement</a> | 
<select name='id_groupe' id='id_groupe' onchange=\"confirm_changement_grp(change, '$themessage');\">";

	$indice_grp_courant=0;
	$cpt_grp=0;
	foreach($current_grp_groupes['groupes'] as $tmp_current_group) {
		echo "<option value='".$tmp_current_group['id_groupe']."'";
		if($tmp_current_group['id_groupe']==$id_groupe) {
			echo " selected";
			$indice_grp_courant=$cpt_grp;
			echo " title=\"Groupe courant.\" style='color:blue;'";
		}
		echo ">".$tmp_current_group['description']." (".$tmp_current_group['name']." en ".$tmp_current_group["classlist_string"].")</option>\n";
		$cpt_grp++;
	}
	echo "
</select>
<!--input type='hidden' name='id_groupe' value='$id_groupe' /-->
<input type='hidden' name='id_grp_groupe' value='$id_grp_groupe' />

 | <a href='repartition_ele_grp.php?id_grp_groupe=$id_grp_groupe'>Répartir les élèves entre les groupes du $groupe_de_groupes</a>

</p>
</form>

<script type='text/javascript'>
	var change='no';
</script>";

	// Effectifs des classes associées au groupe:
	$tab_eff_clas_grp=array();
	for($loop=0;$loop<count($current_group["classes"]["list"]);$loop++) {
		$tab_eff_clas_grp[$current_group["classes"]["list"][$loop]]=array();

		for($loop_per=1;$loop_per<$current_group["nb_periode"];$loop_per++) {
			$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='".$current_group["classes"]["list"][$loop]."' AND periode='".$loop_per."';";
			//echo "$sql<br />";
			$res_compte=mysqli_query($GLOBALS["mysqli"], $sql);
			$tab_eff_clas_grp[$current_group["classes"]["list"][$loop]][$loop_per]=mysqli_num_rows($res_compte);
		}
	}

	if(count($current_grp_groupes['groupes']>1)) {
		echo "<div style='float:right; text-align:center; width:40em;margin-top:3em;'>\n";
		echo "
	<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' name='form_copie_ele' method='post'>
		<fieldset class='fieldset_opacite50'>
			<p>
				<select name='choix_modele_copie' id='choix_modele_copie'>";
		$cpt_ele_grp=0;
		$chaine_js=array();
		$id_groupe_js=array();
		foreach($current_grp_groupes['groupes'] as $tmp_current_group) {
			//if($tmp_current_group['id_groupe']!=$id_groupe) {
				$tmp_grp=get_group($tmp_current_group['id_groupe']);

				$temoin_classe_entiere="y";
				for($loop=0;$loop<count($tmp_grp["classes"]["list"]);$loop++) {
					for($loop_per=1;$loop_per<$current_group["nb_periode"];$loop_per++) {
						if((isset($tab_eff_clas_grp[$current_group["classes"]["list"][$loop]][$loop_per]))&&(count($tmp_grp["eleves"][$loop_per]["telle_classe"][$tmp_grp["classes"]["list"][$loop]])!=$tab_eff_clas_grp[$current_group["classes"]["list"][$loop]][$loop_per])) {
							$temoin_classe_entiere="n";
							break;
						}
					}
				}

				$chaine_js[$cpt_ele_grp]="";
				for($loop=0;$loop<count($tmp_grp["eleves"]["all"]["list"]);$loop++) {
					$chaine_js[$cpt_ele_grp].=",\"".$tmp_grp["eleves"]["all"]["list"][$loop]."\"";
				}
				$chaine_js[$cpt_ele_grp]=mb_substr($chaine_js[$cpt_ele_grp],1);

				$id_groupe_js[$cpt_ele_grp]=$tmp_current_group['id_groupe'];

				echo "<option value='$cpt_ele_grp'";
				if($tmp_current_group['id_groupe']==$id_groupe) {
					echo " style='color:blue;'";
					if($temoin_classe_entiere=="y") {
						echo " title=\"Groupe courant, en classe(s) entière(s).
		Enseignement dispensé par ".$tmp_grp["profs"]["proflist_string"]."\"";
					}
					else {
						echo " title=\"Groupe courant: Sous-groupe.
		Enseignement dispensé par ".$tmp_grp["profs"]["proflist_string"]."\"";
					}
				}
				elseif($temoin_classe_entiere=="y") {
					echo " style='color:grey;' title=\"Classe(s) entière(s).
	Enseignement dispensé par ".$tmp_grp["profs"]["proflist_string"]."\"";
				}
				else {
					echo " title=\"Sous-groupe.
	Enseignement dispensé par ".$tmp_grp["profs"]["proflist_string"]."\"";
				}
				//if((isset($_SESSION['id_groupe_reference_copie_assoc']))&&($_SESSION['id_groupe_reference_copie_assoc']==$tmp_current_group['id_groupe'])) {echo " selected='true'";}
				if($tmp_current_group['id_groupe']==$id_groupe) {echo " selected='true'";}
				echo ">".$tmp_grp['description']." (".$tmp_grp['name']." en ".$tmp_grp["classlist_string"].")</option>\n";

				$cpt_ele_grp++;
			//}
		}
		echo "</select>\n";
		echo "<br />\n";
		echo "<input type='button' name='Copie' value='Recopie des élèves associés' onclick=\"recopie_grp_ele(document.getElementById('choix_modele_copie').selectedIndex);changement();\" />\n";
		echo "<br />\n";
		echo "<input type='button' name='Copie' value='Copie INVERSE des élèves associés' onclick=\"recopie_inverse_grp_ele(document.getElementById('choix_modele_copie').selectedIndex);changement();\" />\n";
		echo "</p>\n";

		echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
		echo "<input type='hidden' name='id_grp_groupe' value='$id_grp_groupe' />\n";

		echo "</fieldset>\n";
		echo "</form>\n";

		echo "<script type='text/javascript'>\n";
		for($loop=0;$loop<count($chaine_js);$loop++) {
			echo "tab_grp_ele_".$loop."=new Array(".$chaine_js[$loop].");\n";
			echo "id_groupe_js_".$loop."=".$id_groupe_js[$loop].";\n";
		}
		echo "</script>\n";

		echo "<br />\n";
		echo "</div>\n";
	}

	echo "
<h2>".ucfirst($groupe_de_groupes)." n°$id_grp_groupe</h2>
<div style='margin-left:2em;'>
	<h3>".$current_group['name']." (<em style='font-size:small;'>".$current_group['description']." en ".$current_group['classlist_string']."</em>) (<em>".$current_group['profs']['proflist_string']."</em>)</h3>

	<p>Vous pouvez modifier la liste des élèves inscrits dans le groupe&nbsp;:</p>
	$chaine_periode_ouverte
	<div style='margin-left:2em;'>";

	// Effectifs des classes associées au groupe:
	$tab_eff_clas_grp=array();
	for($loop=0;$loop<count($current_group["classes"]["list"]);$loop++) {
		$tab_eff_clas_grp[$current_group["classes"]["list"][$loop]]=array();

		for($loop_per=1;$loop_per<$current_group["nb_periode"];$loop_per++) {
			$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='".$current_group["classes"]["list"][$loop]."' AND periode='".$loop_per."';";
			//echo "$sql<br />";
			$res_compte=mysqli_query($GLOBALS["mysqli"], $sql);
			$tab_eff_clas_grp[$current_group["classes"]["list"][$loop]][$loop_per]=mysqli_num_rows($res_compte);
		}
	}

	/*
	echo "<div style='float:right; text-align:center;'>\n";
	if(count($current_grp_groupes['groupes']>1)) {
		echo "
	<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' name='form_copie_ele' method='post'>
		<fieldset class='fieldset_opacite50'>
			<p>
				<select name='choix_modele_copie' id='choix_modele_copie'>";
		$cpt_ele_grp=0;
		$chaine_js=array();
		$id_groupe_js=array();
		foreach($current_grp_groupes['groupes'] as $tmp_current_group) {
			//if($tmp_current_group['id_groupe']!=$id_groupe) {
				$tmp_grp=get_group($tmp_current_group['id_groupe']);

				$temoin_classe_entiere="y";
				for($loop=0;$loop<count($tmp_grp["classes"]["list"]);$loop++) {
					for($loop_per=1;$loop_per<$current_group["nb_periode"];$loop_per++) {
						if((isset($tab_eff_clas_grp[$current_group["classes"]["list"][$loop]][$loop_per]))&&(count($tmp_grp["eleves"][$loop_per]["telle_classe"][$tmp_grp["classes"]["list"][$loop]])!=$tab_eff_clas_grp[$current_group["classes"]["list"][$loop]][$loop_per])) {
							$temoin_classe_entiere="n";
							break;
						}
					}
				}

				$chaine_js[$cpt_ele_grp]="";
				for($loop=0;$loop<count($tmp_grp["eleves"]["all"]["list"]);$loop++) {
					$chaine_js[$cpt_ele_grp].=",\"".$tmp_grp["eleves"]["all"]["list"][$loop]."\"";
				}
				$chaine_js[$cpt_ele_grp]=mb_substr($chaine_js[$cpt_ele_grp],1);

				$id_groupe_js[$cpt_ele_grp]=$tmp_current_group['id_groupe'];

				echo "<option value='$cpt_ele_grp'";
				if($tmp_current_group['id_groupe']==$id_groupe) {
					echo " style='color:blue;'";
					if($temoin_classe_entiere=="y") {
						echo " title=\"Groupe courant, en classe(s) entière(s).
		Enseignement dispensé par ".$tmp_grp["profs"]["proflist_string"]."\"";
					}
					else {
						echo " title=\"Groupe courant: Sous-groupe.
		Enseignement dispensé par ".$tmp_grp["profs"]["proflist_string"]."\"";
					}
				}
				elseif($temoin_classe_entiere=="y") {
					echo " style='color:grey;' title=\"Classe(s) entière(s).
	Enseignement dispensé par ".$tmp_grp["profs"]["proflist_string"]."\"";
				}
				else {
					echo " title=\"Sous-groupe.
	Enseignement dispensé par ".$tmp_grp["profs"]["proflist_string"]."\"";
				}
				//if((isset($_SESSION['id_groupe_reference_copie_assoc']))&&($_SESSION['id_groupe_reference_copie_assoc']==$tmp_current_group['id_groupe'])) {echo " selected='true'";}
				if($tmp_current_group['id_groupe']==$id_groupe) {echo " selected='true'";}
				echo ">".$tmp_grp['description']." (".$tmp_grp['name']." en ".$tmp_grp["classlist_string"].")</option>\n";

				$cpt_ele_grp++;
			//}
		}
		echo "</select>\n";
		echo "<br />\n";
		echo "<input type='button' name='Copie' value='Recopie des élèves associés' onclick=\"recopie_grp_ele(document.getElementById('choix_modele_copie').selectedIndex);changement();\" />\n";
		echo "<br />\n";
		echo "<input type='button' name='Copie' value='Copie INVERSE des élèves associés' onclick=\"recopie_inverse_grp_ele(document.getElementById('choix_modele_copie').selectedIndex);changement();\" />\n";
		echo "</p>\n";

		echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
		echo "<input type='hidden' name='id_grp_groupe' value='$id_grp_groupe' />\n";

		echo "</fieldset>\n";
		echo "</form>\n";

		echo "<script type='text/javascript'>\n";
		for($loop=0;$loop<count($chaine_js);$loop++) {
			echo "tab_grp_ele_".$loop."=new Array(".$chaine_js[$loop].");\n";
			echo "id_groupe_js_".$loop."=".$id_groupe_js[$loop].";\n";
		}
		echo "</script>\n";

		echo "<br />\n";
	}
	*/
?>

<p>
<b>
<a href="javascript:CocheCase(true);changement();">Tout cocher</a> - 
<a href="javascript:CocheCase(false);changement();">Tout décocher</a></b> - <br />

<a href="javascript:CocheFrac(true, 1);changement();">Cocher la première moitié</a> - 
<a href="javascript:CocheFrac(false, 1);changement();">Décocher la première moitié</a> - <br />
<a href="javascript:CocheFrac(true, 2);changement();">Cocher la seconde moitié</a> - 
<a href="javascript:CocheFrac(false, 2);changement();">Décocher la seconde moitié</a> - <br />

<a href="javascript:griser_degriser('griser');changement();">Griser</a> - 
<a href="javascript:griser_degriser('degriser');changement();">Dégriser</a>
</p>

<?php
	echo "
</div>

	<div style='float:left;'>
		<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>
			<fieldset class='fieldset_opacite50'>
				".add_token_field()."
				<input type='hidden' name='id_groupe' value='$id_groupe' />
				<input type='hidden' name='id_grp_groupe' value='$id_grp_groupe' />

				<div style='float:right;'><input type='submit' value='Valider' /></div>

				<table border='1' class='boireaus boireaus_alt' summary='Suivi de cet enseignement par les élèves en fonction des périodes'>
					<tr>
						<th>
							<a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;id_grp_groupe=$id_grp_groupe&amp;order_by=nom' onclick=\"return confirm_abandon (this, change, '$themessage')\">Nom/Prénom</a>
						</th>";
	if ($multiclasses) {
		echo "
						<th>
							<a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;id_grp_groupe=$id_grp_groupe&amp;order_by=classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe</a>
						</th>";
	}

	$acces_mes_listes="y";
	if(!acces('/groupes/mes_listes.php', $_SESSION['statut'])) {
		$acces_mes_listes="n";
	}

	foreach ($current_group["periodes"] as $period) {
		if($period["num_periode"]!=""){
			echo "
						<th>" . $period["nom_periode"];
			if($acces_mes_listes=="y") {
				echo " <a href='mes_listes.php?id_groupe=$id_groupe&amp;periode_num=".$period["num_periode"]."' onclick=\"return confirm_abandon (this, change, '$themessage')\" title='Exporter la liste des élèves de cet enseignement pour la période ".$period["nom_periode"]." au format '><img src='../images/icons/csv.png' width='16' height='16' /></a>";
			}
			echo "</th>";
		}
	}
	echo "
						<th>&nbsp;</th>
					</tr>";


	$conditions = "e.login = j.login and (";
	foreach ($current_group["classes"]["list"] as $query_id_classe) {
		$conditions .= "j.id_classe = '" . $query_id_classe . "' or ";
	}
	$conditions = mb_substr($conditions, 0, -4);
	$conditions .= ") and c.id = j.id_classe";

	// Définition de l'ordre de la liste
	if ($order_by == "classe") {
		// Classement par classe puis nom puis prénom
		//$order_conditions = "j.id_classe, e.nom, e.prenom";
		$order_conditions = "c.classe, j.id_classe, e.nom, e.prenom";
	} elseif ($order_by == "nom") {
		$order_conditions = "e.nom, e.prenom";
	}

	unset($login_eleve);

	echo "
					<tr>
						<th>";

	$calldata = mysqli_query($GLOBALS["mysqli"], "SELECT distinct(j.login), j.id_classe, c.classe, e.nom, e.prenom FROM eleves e, j_eleves_classes j, classes c WHERE (" . $conditions . ") ORDER BY ".$order_conditions);
	$nb = mysqli_num_rows($calldata);
	$eleves_list = array();
	$eleves_list["list"]=array();
	$i=0;
	while($lig=mysqli_fetch_object($calldata)) {
		$e_login=$lig->login;
		echo "
							<input type='hidden' name='login_eleve[$i]' id='login_eleve_$i' value='$e_login' />";
		$login_eleve[$i]=$e_login;
		$e_nom = $lig->nom;
		$e_prenom = $lig->prenom;
		$e_id_classe=$lig->id_classe;
		$classe = $lig->classe;
		$eleves_list["list"][] = $e_login;
		$eleves_list["users"][$e_login] = array("login" => $e_login, "nom" => $e_nom, "prenom" => $e_prenom, "classe" => $classe, "id_classe" => $e_id_classe);
		$i++;
	}
	$total_eleves = $eleves_list["list"];

	foreach ($current_group["periodes"] as $period) {
		if($period["num_periode"]!=""){
			if(count($reg_eleves[$period["num_periode"]])>0) {$total_eleves = array_merge($total_eleves, (array)$reg_eleves[$period["num_periode"]]);}
		}
	}
	$total_eleves = array_unique($total_eleves);

	$elements = array();
	foreach ($current_group["periodes"] as $period) {
		$elements[$period["num_periode"]] = null;
		foreach($total_eleves as $e_login) {
			$elements[$period["num_periode"]] .= "'eleve_" . $period["num_periode"] . "_"  . $e_login  . "',";
		}
		$elements[$period["num_periode"]] = mb_substr($elements[$period["num_periode"]], 0, -1);
	}

	echo "&nbsp;</th>";

	if ($multiclasses) {
		echo "
						<th>&nbsp;</th>";
	}

	foreach ($current_group["periodes"] as $period) {
		if($period["num_periode"]!="") {
			echo "
						<th>";
			if(count($total_eleves)>0) {
				echo "<a href=\"javascript:CocheColonne(".$period["num_periode"].");changement();griser_degriser(etat_grisage);\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne(".$period["num_periode"].");changement();griser_degriser(etat_grisage);\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher' /></a>";

				if($period["num_periode"]>1) {
					echo " / <a href=\"javascript:copieElevesPeriode1(".$period["num_periode"].");changement();griser_degriser(etat_grisage);\"><img src='../images/icons/copy-16.png' width='16' height='16' alt='Copier les affectations de la première période' title='Copier les affectations de la première période' /></a>";
				}
			}
			//=========================
			echo "<br />Inscrits : " . count($current_group["eleves"][$period["num_periode"]]["list"]);
			echo "</th>";
		}
	}
	echo "
						<th>";
	if((isset($tab_sig))&&(count($tab_sig)>0)) {
		echo "<span id='prise_en_compte_signalement_toutes_periodes'>&nbsp;&nbsp;<a href=\"javascript:prise_en_compte_signalement('prise_en_compte_signalement_toutes_periodes');changement();griser_degriser(etat_grisage);\"><img src='../images/icons/flag2.gif' width='16' height='16' alt='Prendre en compte tous les signalements d erreurs pour toutes les périodes.' title='Prendre en compte tous les signalements d erreurs pour toutes les périodes.' /></a></span>";
	}
	else {
		echo "&nbsp;";
	}
	echo "
						</th>
					</tr>";



	// Marqueurs pour identifier quand on change de classe dans la liste
	$prev_classe = 0;
	$new_classe = 0;
	$empty_td = false;

	//=====================================
	$chaine_sql_classe="(";
	for($i=0;$i<count($current_group["classes"]["list"]);$i++) {
		if($i>0) {$chaine_sql_classe.=" OR ";}
		$chaine_sql_classe.="id_classe='".$current_group["classes"]["list"][$i]."'";
	}
	$chaine_sql_classe.=")";
	//=====================================

	$acces_eleve_options="y";
	if(!acces('/classes/eleve_options.php', $_SESSION['statut'])) {
		$acces_eleve_options="n";
	}

	$acces_prepa_conseil_edit_limite="y";
	if(!acces('/prepa_conseil/edit_limite.php', $_SESSION['statut'])) {
		$acces_prepa_conseil_edit_limite="n";
	}

	if(count($total_eleves)>0) {
		$alt=1;
		foreach($total_eleves as $e_login) {

			//=========================
			// Récupération du numéro de l'élève:
			$num_eleve=-1;
			for($i=0;$i<count($login_eleve);$i++){
				if($e_login==$login_eleve[$i]){
					$num_eleve=$i;
					break;
				}
			}
			if($num_eleve!=-1) {

				//=========================
				// AJOUT: boireaus 20080229
				// Test de l'appartenance à plusieurs classes
				$sql="SELECT DISTINCT id_classe FROM j_eleves_classes WHERE login='$e_login';";
				$test_plusieurs_classes=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test_plusieurs_classes)==1) {
					$temoin_eleve_changeant_de_classe="n";
				}
				else {
					$temoin_eleve_changeant_de_classe="y";
				}
				//=========================

				//=========================
				//$new_classe = $eleves_list["users"][$e_login]["id_classe"];
				if(isset($eleves_list["users"][$e_login])) {
					$new_classe = $eleves_list["users"][$e_login]["id_classe"];
				}
				else {
					$new_classe="BIZARRE";
				}
				//echo "$e_login -&gt; $new_classe<br />";

				if ($new_classe != $prev_classe and $order_by == "classe" and $multiclasses) {
					echo "<tr style='background-color: #CCCCCC;'>\n";
					echo "<td colspan='3' style='padding: 5px; font-weight: bold;'>";
					echo "Classe de : " . $eleves_list["users"][$e_login]["classe"];
					echo "</td>\n";
					foreach ($current_group["periodes"] as $period) {
						echo "<td>&nbsp;</td>\n";
					}
					echo "<td>&nbsp;</td>\n";
					echo "</tr>\n";
					$prev_classe = $new_classe;
				}

				$alt=$alt*(-1);
				echo "<tr id='tr_$num_eleve' class='lig$alt white_hover'>\n";
				if (array_key_exists($e_login, $eleves_list["users"])) {
					/*
					echo "<td>" . $eleves_list["users"][$e_login]["prenom"] . " " .
						$eleves_list["users"][$e_login]["nom"] .
						"</td>";
					*/
					echo "<td>";
					if($acces_eleve_options=="y") {
						echo "<a href='../classes/eleve_options.php?login_eleve=$e_login&id_classe=".$eleves_list["users"][$e_login]['id_classe']."' title=\"Consulter les matières suivies par ".$eleves_list["users"][$e_login]["nom"]." ".$eleves_list["users"][$e_login]["prenom"]." en classe de ".$eleves_list["users"][$e_login]["classe"]."\"";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo ">";
						echo $eleves_list["users"][$e_login]["nom"];
						echo " ";
						echo $eleves_list["users"][$e_login]["prenom"];
						echo "</a>\n";
					}
					else {
						echo $eleves_list["users"][$e_login]["nom"];
						echo " ";
						echo $eleves_list["users"][$e_login]["prenom"];
					}
					//echo "<pre>".print_r($eleves_list["users"][$e_login])."</pre>";
					echo "</td>\n";

					if ($multiclasses) {echo "<td>" . $eleves_list["users"][$e_login]["classe"] . "</td>\n";}
					echo "\n";
				}
				else {
					/*
					echo "<td>" . $e_login . "</td>" .
						"<td>" . $current_group["eleves"]["users"][$e_login]["prenom"] . " " .
						$current_group["eleves"]["users"][$e_login]["nom"] .
						"</td>";
					*/
					echo "<td>";
					if($new_classe=="BIZARRE"){
						echo "<font color='red'>$e_login</font>";
					}
					else{
						echo "$e_login";
					}
					echo "</td>\n";
					if ($multiclasses) {echo "<td>" . $current_group["eleves"]["users"][$e_login]["classe"] . "</td>\n";}
					echo "\n";
				}
	
	
				foreach ($current_group["periodes"] as $period) {
					if($period["num_periode"]!="") {
						echo "<td align='center'>";
	
						//=========================
						// MODIF: boireaus 20080229
						//$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$e_login' AND id_classe='".$new_classe."' AND periode='".$period["num_periode"]."'";
						$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$e_login' AND $chaine_sql_classe AND periode='".$period["num_periode"]."'";
						//=========================
						$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_test)>0){
							//=========================
							// MODIF: boireaus 20071010
							//echo "<input type='checkbox' name='eleve_".$period["num_periode"] . "_" . $e_login."' ";
							echo "<input type='checkbox' name='eleve_".$period["num_periode"]."_".$num_eleve."' id='case_".$period["num_periode"]."_".$num_eleve."' ";
							//=========================
							echo " onchange='modif_grisage_case(".$period["num_periode"].", $num_eleve), changement();'";
							if (in_array($e_login, (array)$current_group["eleves"][$period["num_periode"]]["list"])) {
								echo " checked />";
							} else {
								echo " />";
							}


							// Test sur la présence de notes dans cn ou de notes/app sur bulletin
							if (!test_before_eleve_removal($e_login, $current_group['id'], $period["num_periode"])) {
								if($acces_prepa_conseil_edit_limite=="y") {
									echo "<a href='../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=".$e_login."&id_classe=".$eleves_list["users"][$e_login]["id_classe"]."&periode1=".$period["num_periode"]."&periode2=".$period["num_periode"]."' target='_blank'>";
									echo "<img id='img_bull_non_vide_".$period["num_periode"]."_".$num_eleve."' src='../images/icons/bulletin_16.png' width='16' height='16' title='Bulletin non vide' alt='Bulletin non vide' />";
									echo "</a>";
								}
								else {
									echo "<img id='img_bull_non_vide_".$period["num_periode"]."_".$num_eleve."' src='../images/icons/bulletin_16.png' width='16' height='16' title='Bulletin non vide' alt='Bulletin non vide' />";
								}
							}

							/*$sql="SELECT DISTINCT id_devoir FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE (cnd.login = '".$e_login."' AND cnd.statut='' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe = '".$current_group['id']."' AND ccn.periode = '".$period["num_periode"]."')";
							$test_cn=mysqli_query($GLOBALS["mysqli"], $sql);
							$nb_notes_cn=mysqli_num_rows($test_cn);
							*/
							$nb_notes_cn=nb_notes_ele_dans_tel_enseignement($e_login, $current_group['id'], $period["num_periode"]);
							if($nb_notes_cn>0) {
								echo "<img id='img_cn_non_vide_".$period["num_periode"]."_".$num_eleve."' src='../images/icons/cn_16.png' width='16' height='16' title='Carnet de notes non vide: $nb_notes_cn notes' alt='Carnet de notes non vide: $nb_notes_cn notes' />";
								//echo "$sql<br />";
							}

							if((isset($tab_sig[$period["num_periode"]]))&&(isset($tab_sig[$period["num_periode"]][$e_login]))) {
								$info_erreur=$tab_sig[$period["num_periode"]][$e_login];
								echo "<img id='img_erreur_affect_".$period["num_periode"]."_".$num_eleve."' src='../images/icons/flag2.gif' width='17' height='18' title='".$info_erreur."' alt='".$info_erreur."' />";

								//$chaine_sig.=",'case_".$period["num_periode"]."_".$num_eleve."'";
							}

							//=========================
							// AJOUT: boireaus 20080229
							if($temoin_eleve_changeant_de_classe=="y") {
								$sql="SELECT c.classe FROM classes c, j_eleves_classes jec WHERE jec.login='$e_login' AND jec.id_classe=c.id AND jec.periode='".$period["num_periode"]."';";
								$res_classe_ele=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_classe_ele)>0){
									$lig_tmp=mysqli_fetch_object($res_classe_ele);
									echo " $lig_tmp->classe";
								}
							}
							//=========================
						}
						else{
							echo "&nbsp;\n";
							//echo "<input type='hidden' name='eleve_".$period["num_periode"] . "_" . $e_login."' />\n";
						}
						echo "</td>\n";
					}
				}
	
				$elementlist = null;
				foreach ($current_group["periodes"] as $period) {
					if($period["num_periode"]!="") {
						$elementlist .= "'eleve_" . $period["num_periode"] . "_" . $e_login . "',";
					}
				}
				$elementlist = mb_substr($elementlist, 0, -1);
	
				echo "<td><a href=\"javascript:CocheLigne($num_eleve);changement();griser_degriser(etat_grisage);\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne($num_eleve);changement();griser_degriser(etat_grisage);\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a></td>\n";
				echo "</tr>\n";
			}
		}

		echo "<tr>\n";
		echo "<th>\n";
		echo "&nbsp;\n";
		echo "</th>\n";
		if ($multiclasses) {
			echo "<th>&nbsp;</th>\n";
		}
		echo "\n";
		foreach ($current_group["periodes"] as $period) {
			if($period["num_periode"]!="") {
				echo "<th>";
				if(count($total_eleves)>0) {
					echo "<a href=\"javascript:DecocheColonne_si_bull_et_cn_vide(".$period["num_periode"].");changement();griser_degriser(etat_grisage);\"><img src='../images/icons/wizard.png' width='16' height='16' alt='Décocher les élèves sans note/app sur les bulletin et carnet de notes' title='Décocher les élèves sans note/app sur les bulletin et carnet de notes' /></a>";

					if((isset($tab_sig))&&(count($tab_sig)>0)) {
						echo "<span id='prise_en_compte_signalement_".$period["num_periode"]."'>&nbsp;&nbsp;<a href=\"javascript:prise_en_compte_signalement(".$period["num_periode"].");changement();griser_degriser(etat_grisage);\"><img src='../images/icons/flag2.gif' width='16' height='16' alt='Prendre en compte tous les signalements d erreurs pour la période ".$period["num_periode"]."' title='Prendre en compte tous les signalements d erreurs pour la période ".$period["num_periode"]."' /></a></span>";
					}
				}
				echo "</th>\n";
			}
		}
		echo "<th>";
		if((isset($tab_sig))&&(count($tab_sig)>0)) {
			echo "<span id='prise_en_compte_signalement_toutes_periodes'>&nbsp;&nbsp;<a href=\"javascript:prise_en_compte_signalement('prise_en_compte_signalement_toutes_periodes');changement();griser_degriser(etat_grisage);\"><img src='../images/icons/flag2.gif' width='16' height='16' alt='Prendre en compte tous les signalements d erreurs pour toutes les périodes.' title='Prendre en compte tous les signalements d erreurs pour toutes les périodes.' /></a></span>";
		}
		else {
			echo "&nbsp;";
		}
		echo "</th>\n";
		echo "</tr>\n";

	}

	$nb_eleves=count($total_eleves);

	echo "
				</table>
				".add_token_field()."
				<input type='hidden' name='modifier_liste_eleves' value='y' />
				<input type='hidden' name='id_groupe_reference' id='id_groupe_reference' value='' />
				<p><input type='submit' value='Valider' /></p>
			</fieldset>
		</form>

		<script type='text/javascript' language='javascript'>

			function CocheCase(boul) {

				nbelements = document.formulaire.elements.length;
				for (i = 0 ; i < nbelements ; i++) {
					if (document.formulaire.elements[i].type =='checkbox') {
						document.formulaire.elements[i].checked = boul ;
					}
				}

			}

			function CocheLigne(ki) {
				for (var i=1;i<$nb_periode;i++) {
					if(document.getElementById('case_'+i+'_'+ki)){
						document.getElementById('case_'+i+'_'+ki).checked = true;
					}
				}
			}

			function DecocheLigne(ki) {
				for (var i=1;i<$nb_periode;i++) {
					if(document.getElementById('case_'+i+'_'+ki)){
						document.getElementById('case_'+i+'_'+ki).checked = false;
					}
				}
			}

			var etat_grisage='griser';

			function griser_degriser(mode) {
				if(mode=='griser') {
					griser_degriser('degriser');

					for (var ki=0;ki<$nb_eleves;ki++) {
						temoin='n';
						for(i=0;i<=".count($current_group["periodes"]).";i++) {
							if(document.getElementById('case_'+i+'_'+ki)){
								if(document.getElementById('case_'+i+'_'+ki).checked == true) {
									temoin='y';
								}
							}
						}

						if(temoin=='n') {
							if(document.getElementById('tr_'+ki)) {
								document.getElementById('tr_'+ki).style.backgroundColor='grey';
							}
						}
					}
				}
				else {
					for (var ki=0;ki<$nb_eleves;ki++) {
						if(document.getElementById('tr_'+ki)) {
							document.getElementById('tr_'+ki).style.backgroundColor='';
						}
					}
				}
				etat_grisage=mode;
			}

			griser_degriser('griser');

			function modif_grisage_case(num_per, num_ligne) {
				temoin='n';
				if(document.getElementById('case_'+num_per+'_'+num_ligne)) {
					for(i=0;i<=".count($current_group["periodes"]).";i++) {
						if(document.getElementById('case_'+i+'_'+num_ligne)){
							if(document.getElementById('case_'+i+'_'+num_ligne).checked == true) {
								temoin='y';
							}
						}
					}

					if(temoin=='y') {
						if(document.getElementById('tr_'+num_ligne)) {
							document.getElementById('tr_'+num_ligne).style.backgroundColor='';
						}
					}
					else {
						if(document.getElementById('tr_'+num_ligne)) {
							document.getElementById('tr_'+num_ligne).style.backgroundColor='grey';
						}
					}
				}
			}

			function CocheColonne(i) {
				for (var ki=0;ki<$nb_eleves;ki++) {
					if(document.getElementById('case_'+i+'_'+ki)){
						document.getElementById('case_'+i+'_'+ki).checked = true;
					}
				}
			}

			function DecocheColonne(i) {
				for (var ki=0;ki<$nb_eleves;ki++) {
					if(document.getElementById('case_'+i+'_'+ki)){
						document.getElementById('case_'+i+'_'+ki).checked = false;
					}
				}
			}

			function DecocheColonne_si_bull_et_cn_vide(i) {
				for (var ki=0;ki<$nb_eleves;ki++) {
					if((document.getElementById('case_'+i+'_'+ki))&&(!document.getElementById('img_bull_non_vide_'+i+'_'+ki))&&(!document.getElementById('img_cn_non_vide_'+i+'_'+ki))) {
						document.getElementById('case_'+i+'_'+ki).checked = false;
					}
				}
			}

			function copieElevesPeriode1(num_periode) {
				for (var ki=0;ki<$nb_eleves;ki++) {
					if((document.getElementById('case_1_'+ki))&&(document.getElementById('case_'+num_periode+'_'+ki))) {
						document.getElementById('case_'+num_periode+'_'+ki).checked=document.getElementById('case_1_'+ki).checked;
					}
				}
			}

			function recopie_grp_ele(num) {
				tab=eval('tab_grp_ele_'+num);
				//alert('tab[0]='+tab[0]);

				document.getElementById('id_groupe_reference').value=eval('id_groupe_js_'+num);

				for(j=0;j<$nb_eleves;j++) {
					DecocheLigne(j);
				}
	
				for(i=0;i<tab.length;i++) {
					//if(i<3) {alert('tab['+i+']='+tab[i])}
					for(j=0;j<$nb_eleves;j++) {
						//if(j<3) {alert('document.getElementById(login_eleve_'+j+').value='+document.getElementById('login_eleve_'+j).value)}
						if(document.getElementById('login_eleve_'+j).value==tab[i]) {
							CocheLigne(j);
						}
					}
				}

				griser_degriser('griser');
			}

			function recopie_inverse_grp_ele(num) {
				tab=eval('tab_grp_ele_'+num);
				//alert('tab[0]='+tab[0]);

				document.getElementById('id_groupe_reference').value=eval('id_groupe_js_'+num);

				for(j=0;j<$nb_eleves;j++) {
					CocheLigne(j);
				}

				for(i=0;i<tab.length;i++) {
					for(j=0;j<$nb_eleves;j++) {
						if(document.getElementById('login_eleve_'+j).value==tab[i]) {
							DecocheLigne(j);
						}
					}
				}

				griser_degriser('griser');
			}";

	if((isset($tab_sig))&&(count($tab_sig)>0)) {
		echo "
			function prise_en_compte_signalement(num_periode) {
				if(num_periode=='prise_en_compte_signalement_toutes_periodes') {
					for(num_periode=0;num_periode<=".count($current_group["periodes"]).";num_periode++) {
						for(j=0;j<$nb_eleves;j++) {
							if(document.getElementById('img_erreur_affect_'+num_periode+'_'+j)) {
								if(document.getElementById('case_'+num_periode+'_'+j)) {
									if(document.getElementById('case_'+num_periode+'_'+j).checked) {
										document.getElementById('case_'+num_periode+'_'+j).checked=false;
									}
									else {
										document.getElementById('case_'+num_periode+'_'+j).checked=true;
									}
								}
							}
						}
						if(document.getElementById('prise_en_compte_signalement_'+num_periode)) {
							document.getElementById('prise_en_compte_signalement_'+num_periode).style.display='none';
						}
						document.getElementById('prise_en_compte_signalement_toutes_periodes').style.display='none';
					}
				}
				else {
					for(j=0;j<$nb_eleves;j++) {
						if(document.getElementById('img_erreur_affect_'+num_periode+'_'+j)) {
							if(document.getElementById('case_'+num_periode+'_'+j)) {
								if(document.getElementById('case_'+num_periode+'_'+j).checked) {
									document.getElementById('case_'+num_periode+'_'+j).checked=false;
								}
								else {
									document.getElementById('case_'+num_periode+'_'+j).checked=true;
								}
							}
						}
					}
					document.getElementById('prise_en_compte_signalement_'+num_periode).style.display='none';
				}
			}";
	}

	echo "
			function CocheFrac(mode, part) {
				for(i=0;i<$nb_eleves;i++) {
					for(num_periode=0;num_periode<=".count($current_group["periodes"]).";num_periode++) {
						if(document.getElementById('case_'+num_periode+'_'+i)) {
							if(part==1) {
								if(i<$nb_eleves/2) {
									document.getElementById('case_'+num_periode+'_'+i).checked=mode;
								}
							}
							else {
								if(i>=$nb_eleves/2) {
									document.getElementById('case_'+num_periode+'_'+i).checked=mode;
								}
							}
						}
					}
				}
				griser_degriser(etat_grisage);
			}

			function confirm_changement_grp(thechange, themessage)
			{
				if (!(thechange)) thechange='no';
				if (thechange != 'yes') {
					document.forms['form_passage_a_un_autre_groupe'].submit();
				}
				else{
					var is_confirmed = confirm(themessage);
					if(is_confirmed){
						document.forms['form_passage_a_un_autre_groupe'].submit();
					}
					else{
						document.getElementById('id_groupe_a_passage_autre_grp').selectedIndex=$indice_grp_courant;
					}
				}
			}

		</script>

		<p style='margin-top:1em; margin-left:4.5em; text-indent:-4.5em;'><em>NOTES&nbsp;:</em> Il n'est pas possible de désinscrire un élève d'un groupe pour une période sur laquelle il a des notes, appréciation ou avis du conseil de classe dans le bulletin.</p>

	</div>
	</div>
</div>";

require("../lib/footer.inc.php");
?>
