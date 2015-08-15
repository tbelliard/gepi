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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_discipline/param_pointages.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_discipline/param_pointages.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Discipline: Definir les types de pointages de petits incidents',
statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_mod_discipline')) {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

$sql="CREATE TABLE IF NOT EXISTS sp_types_saisies (
id_type int(11) NOT NULL AUTO_INCREMENT,
nom VARCHAR(255) NOT NULL default '',
description TEXT NOT NULL,
rang int(11) NOT NULL,
PRIMARY KEY (id_type)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create=mysqli_query($GLOBALS["mysqli"], $sql);

require('sanctions_func_lib.php');

$suppr_pointage=isset($_POST['suppr_pointage']) ? $_POST['suppr_pointage'] : NULL;

$nom=isset($_POST['nom']) ? $_POST['nom'] : NULL;
$cpt=isset($_POST['cpt']) ? $_POST['cpt'] : 0;

//$rang=isset($_POST['rang']) ? $_POST['rang'] : NULL;
//$description=isset($_POST['description']) ? $_POST['description'] : NULL;
$id_type=isset($_GET['id_type']) ? $_GET['id_type'] : NULL;
$move=isset($_GET['move']) ? $_GET['move'] : NULL;

$msg="";

function corrige_rangs_types_saisies() {
	global $msg;

	$sql="SELECT * FROM sp_types_saisies ORDER BY rang,nom;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$rang=1;
		while($lig=mysqli_fetch_object($res)) {
			$sql="UPDATE sp_types_saisies SET rang='".$rang."' WHERE id_type='".$lig->id_type."';";
			//echo "$sql<br />\n";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$update) {
				$msg.="ERREUR lors de la mise à jour du rang de ".$lig->nom."<br />\n";
			}
			else {
				$rang++;
			}
		}
	}
}

if(isset($suppr_pointage)) {
	check_token();

	for($i=0;$i<$cpt;$i++) {
		if(isset($suppr_pointage[$i])) {
			$sql="SELECT 1=1 FROM sp_saisies WHERE id_type='".$suppr_pointage[$i]."';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				$msg.="Suppression du type n°".$suppr_pointage[$i]." impossible car associé à ".mysqli_num_rows($test)." pointages.<br />\n";
			}
			else {
				$sql="DELETE FROM sp_types_saisies WHERE id_type='".$suppr_pointage[$i]."';";
				$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$suppr) {
					$msg.="ERREUR lors de la suppression du type n°".$suppr_pointage[$i].".<br />\n";
				}
			}
		}
	}

	corrige_rangs_types_saisies();
}

if(isset($nom)) {
	$a_enregistrer='y';

	check_token();

	$tab_pointage=array();
	$sql="SELECT * FROM sp_types_saisies ORDER BY rang, nom;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_pointage[]=$lig->nom;

			//echo "Id_pointage: $lig->id_type<br />";
			if(isset($NON_PROTECT["description_".$lig->id_type])) {
				$description=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["description_".$lig->id_type]));
				$description=suppression_sauts_de_lignes_surnumeraires($description);

				$sql="UPDATE sp_types_saisies SET description='".$description."' WHERE id_type='".$lig->id_type."';";
				//echo "$sql<br />\n";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$update) {
					$msg.="ERREUR lors de la mise à jour de la description de ".$lig->nom."<br />\n";
				}
			}
		}

		if($msg=="") {
			$msg.="Mise à jour des descriptions des pointages précédemment saisis effectuée.<br />";
		}
		//if(in_array($pointage,$tab_pointage)) {$a_enregistrer='n';}
	}


	if((isset($nom))&&($nom!='')) {

		if(in_array($nom,$tab_pointage)) {$a_enregistrer='n';}

		if($a_enregistrer=='y') {
			if(isset($NON_PROTECT["description"])) {
				$description=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["description"]));
			}
			else {
				$description="";
			}
			$description=suppression_sauts_de_lignes_surnumeraires($description);

			$sql="SELECT 1=1 FROM sp_types_saisies;";
			//echo "$sql<br />\n";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			$rang=mysqli_num_rows($res)+1;

			$sql="INSERT INTO sp_types_saisies SET nom='".$nom."', description='$description', rang='$rang';";
			//echo "$sql<br />\n";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$res) {
				$msg.="ERREUR lors de l'enregistrement de ".$nom."<br />\n";
			}
			else {
				$msg.="Enregistrement de ".$nom." effectué.<br />\n";
			}
		}
	}

}

if((isset($id_type))&&(preg_match("/^[0-9]{1,}$/", $id_type))&&(isset($move))&&(($move=="up")||($move=="down"))) {
	check_token();

	$sql="SELECT * FROM sp_types_saisies WHERE id_type='$id_type';";
	//echo "$sql<br />\n";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		$msg.="Type n°$id_type inconnu.<br />";
	}
	else {
		if($move=="up") {
			$lig=mysqli_fetch_object($res);

			$sql="SELECT * FROM sp_types_saisies WHERE rang<'$lig->rang' ORDER BY rang DESC LIMIT 1;";
			//echo "$sql<br />\n";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res2)==0) {
				$msg.="Aucun type trouvé avant le type n°$id_type.<br />";
			}
			else {
				$lig2=mysqli_fetch_object($res2);

				$sql="UPDATE sp_types_saisies SET rang='".$lig->rang."' WHERE id_type='".$lig2->id_type."';";
				//echo "$sql<br />\n";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$update) {
					$msg.="ERREUR lors de la mise à jour du rang de ".$lig->nom."<br />\n";
				}
				else {
					$sql="UPDATE sp_types_saisies SET rang='".$lig2->rang."' WHERE id_type='".$lig->id_type."';";
					//echo "$sql<br />\n";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$update) {
						$msg.="ERREUR lors de la mise à jour du rang de ".$lig2->nom."<br />\n";
					}
				}
			}
		}
		else {
			$lig=mysqli_fetch_object($res);

			$sql="SELECT * FROM sp_types_saisies WHERE rang>'$lig->rang' ORDER BY rang ASC LIMIT 1;";
			//echo "$sql<br />\n";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res2)==0) {
				$msg.="Aucun type trouvé après le type n°$id_type.<br />";
			}
			else {
				$lig2=mysqli_fetch_object($res2);

				$sql="UPDATE sp_types_saisies SET rang='".$lig->rang."' WHERE id_type='".$lig2->id_type."';";
				//echo "$sql<br />\n";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$update) {
					$msg.="ERREUR lors de la mise à jour du rang de ".$lig->nom."<br />\n";
				}
				else {
					$sql="UPDATE sp_types_saisies SET rang='".$lig2->rang."' WHERE id_type='".$lig->id_type."';";
					//echo "$sql<br />\n";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$update) {
						$msg.="ERREUR lors de la mise à jour du rang de ".$lig2->nom."<br />\n";
					}
				}
			}
		}

		corrige_rangs_types_saisies();
	}
}

if(isset($_POST['save_params'])) {
	check_token();

	if(isset($_POST['active_mod_disc_pointage'])) {
		$valeur="y";
	}
	else {
		$valeur="n";
	}
	if(!saveSetting('active_mod_disc_pointage', $valeur)) {
		$msg.="Erreur lors de l'enregistrement de 'active_mod_disc_pointage'<br />";
	}

	if(isset($_POST['disc_pointage_aff_totaux_visu_ele'])) {
		$valeur="y";
	}
	else {
		$valeur="n";
	}
	if(!saveSetting('disc_pointage_aff_totaux_visu_ele', $valeur)) {
		$msg.="Erreur lors de l'enregistrement de 'disc_pointage_aff_totaux_visu_ele'<br />";
	}

	if(isset($_POST['disc_pointage_aff_totaux_ele'])) {
		$valeur="y";
	}
	else {
		$valeur="n";
	}
	if(!saveSetting('disc_pointage_aff_totaux_ele', $valeur)) {
		$msg.="Erreur lors de l'enregistrement de 'disc_pointage_aff_totaux_ele'<br />";
	}

	if(isset($_POST['disc_pointage_aff_totaux_resp'])) {
		$valeur="y";
	}
	else {
		$valeur="n";
	}
	if(!saveSetting('disc_pointage_aff_totaux_resp', $valeur)) {
		$msg.="Erreur lors de l'enregistrement de 'disc_pointage_aff_totaux_resp'<br />";
	}

	if(isset($_POST['mod_disc_terme_menus_incidents'])) {
		if($_POST['mod_disc_terme_menus_incidents']=="") {
			$msg.="Erreur: Le nom des 'menus incidents' ne peut pas être vide.<br />";
		}
		elseif(!saveSetting('mod_disc_terme_menus_incidents', $_POST['mod_disc_terme_menus_incidents'])) {
			$msg.="Erreur lors de l'enregistrement du nom des 'menus incidents'.<br />";
		}
	}

	if($msg=="") {
		$msg="Enregistrement effectué : ".strftime("%d/%m/%Y à %H:%M:%S")."<br />";
	}
}

$mod_disc_terme_menus_incidents=getSettingValue("mod_disc_terme_menus_incidents");
if($mod_disc_terme_menus_incidents=="") {
	$mod_disc_terme_menus_incidents="menus incidents";
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: Définition des pointages";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
".((getSettingAOui('active_mod_disc_pointage')) ? " | <a href='saisie_pointages.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Pointer de ".$mod_disc_terme_menus_incidents."</a>" : "")."
</p>\n";

//=============================================
echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire2'>
<fieldset class='fieldset_opacite50'>\n";
echo add_token_field();

echo "<p class='bold'>Paramètres&nbsp;:</p>\n";
echo "<blockquote>\n";

$checked="";
if(getSettingAOui('active_mod_disc_pointage')) {
	$checked=" checked";
}
echo "<p><input type='checkbox' name='active_mod_disc_pointage' id='active_mod_disc_pointage' value=\"y\" onchange='checkbox_change(this.id); changement();'$checked /><label for='active_mod_disc_pointage' id='texte_active_mod_disc_pointage'> Activer le dispositif de saisie/pointages disciplinaires</label><br /><br />\n";


echo "Nom à donner aux <strong>menus incidents</strong>&nbsp;: <input type='text' name='mod_disc_terme_menus_incidents' id='mod_disc_terme_menus_incidents' value=\"".$mod_disc_terme_menus_incidents."\" onchange='changement();' /><br />
<em>(suggestions&nbsp;: 'manquements', 'menus manquements', 'menus incidents')</em>
<br /><br />\n";


$checked="";
if(getSettingAOui('disc_pointage_aff_totaux_visu_ele')) {
	$checked=" checked";
}
echo "<input type='checkbox' name='disc_pointage_aff_totaux_visu_ele' id='disc_pointage_aff_totaux_visu_ele' value=\"y\" onchange='checkbox_change(this.id); changement();'$checked /><label for='disc_pointage_aff_totaux_visu_ele' id='texte_disc_pointage_aff_totaux_visu_ele'> Faire apparaître les totaux par types dans la page de Consultation des onglets élève <img src='../images/icons/ele_onglets.png' class='icone16' alt='Consultation élève' /></label><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>(page accessible des personnels de l'établissement)</em><br /><br />\n";

$checked="";
if(getSettingAOui('disc_pointage_acces_totaux_ele')) {
	$checked=" checked";
}
echo "<input type='checkbox' name='disc_pointage_acces_totaux_ele' id='disc_pointage_acces_totaux_ele' value=\"y\" onchange='checkbox_change(this.id); changement();'$checked /><label for='disc_pointage_acces_totaux_ele' id='texte_disc_pointage_acces_totaux_ele'> Donner l'accès aux totaux par types dans la page Discipline pour les utilisateurs élèves</label><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>(sous réserve que l'accès aux ".$mod_disc_terme_incident."s soit donné aux élèves)</em>
<br />\n";

$checked="";
if(getSettingAOui('disc_pointage_aff_totaux_ele')) {
	$checked=" checked";
}
echo "<input type='checkbox' name='disc_pointage_aff_totaux_ele' id='disc_pointage_aff_totaux_ele' value=\"y\" onchange='checkbox_change(this.id); changement();'$checked /><label for='disc_pointage_aff_totaux_ele' id='texte_disc_pointage_aff_totaux_ele'> Faire apparaître les totaux par types en page d'accueil pour les utilisateurs élèves</label><br /><br />\n";

$checked="";
if(getSettingAOui('disc_pointage_acces_totaux_resp')) {
	$checked=" checked";
}
echo "<input type='checkbox' name='disc_pointage_acces_totaux_resp' id='disc_pointage_acces_totaux_resp' value=\"y\" onchange='checkbox_change(this.id); changement();'$checked /><label for='disc_pointage_acces_totaux_resp' id='texte_disc_pointage_acces_totaux_resp'> Donner l'accès aux totaux par types dans la page Discipline pour les utilisateurs responsables élèves</label><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>(sous réserve que l'accès aux ".$mod_disc_terme_incident."s soit donné aux responsables élèves)</em><br />\n";

$checked="";
if(getSettingAOui('disc_pointage_aff_totaux_resp')) {
	$checked=" checked";
}
echo "<input type='checkbox' name='disc_pointage_aff_totaux_resp' id='disc_pointage_aff_totaux_resp' value=\"y\" onchange='checkbox_change(this.id); changement();'$checked /><label for='disc_pointage_aff_totaux_resp' id='texte_disc_pointage_aff_totaux_resp'> Faire apparaître les totaux par types en page d'accueil pour les responsables élèves</label><br />\n";

echo "<input type='hidden' name='save_params' value='y' />\n";
echo "<p class='center'><input type='submit' name='valider' value='Valider' /></p>\n";

echo "<p><em>NOTES&nbsp;:</em></p>\n";
echo "<ul>\n";
echo "<li><p>Si vous n'utilisez le dispositif de pointage que pour noter ce que vous n'avez pas pu inscrire dans le carnet de correspondance \"oublié\" par un élève <br />
(<em>si vous n'utilisez le dispositif que pour ne pas oublier de reporter ce pointage sur le carnet lorsque vous pourrez mettre la main dessus</em>),<br />
il ne sera sans doute pas judicieux de faire apparaître les totaux pour les utilisateurs élèves et responsables.</p></li>\n";
echo "</ul>\n";

echo "<script type='text/javascript'>
".js_checkbox_change_style()."

checkbox_change('active_mod_disc_pointage');
checkbox_change('disc_pointage_aff_totaux_visu_ele');
checkbox_change('disc_pointage_aff_totaux_ele');
checkbox_change('disc_pointage_aff_totaux_resp');
</script>\n";

echo "</blockquote>\n";

echo "</fieldset>\n";
echo "</form>\n";

echo "<p><br /></p>\n";
//=============================================

echo "<a name='saisie_types'></a>
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."#saisie_types' method='post' name='formulaire'>
<fieldset class='fieldset_opacite50'>\n";
echo add_token_field();

echo "<p class='bold'>Saisie des types de pointages de menus ".$mod_disc_terme_incident."s&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt=0;
$sql="SELECT * FROM sp_types_saisies ORDER BY rang, nom;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>Aucun type de pointage n'est encore défini.</p>\n";
}
else {
	echo "<p>Types de pointages existants&nbsp;:</p>\n";
	echo "<table class='boireaus' border='1' summary='Tableau des pointages existants'>\n";
	echo "<tr>\n";
	echo "<th colspan='3'>Rang</th>\n";
	echo "<th>Nom</th>\n";
	echo "<th>Description</th>\n";
	echo "<th>Supprimer</th>\n";
	echo "</tr>\n";
	$alt=1;
	while($lig=mysqli_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";

		echo "<td>\n";
		if($cpt>0) {
			echo "<a href='".$_SERVER['PHP_SELF']."?id_type=$lig->id_type&amp;move=up".add_token_in_url()."#saisie_types' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/up.png' class='icone16' alt='Haut' /></a> ";
		}
		echo "</td>\n";
		echo "<td>\n";
		echo $lig->rang;
		echo "</td>\n";
		echo "<td>\n";
		if($cpt<mysqli_num_rows($res)-1) {
			echo "<a href='".$_SERVER['PHP_SELF']."?id_type=$lig->id_type&amp;move=down".add_token_in_url()."#saisie_types' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/down.png' class='icone16' alt='Bas' /></a> ";
		}
		echo "</td>\n";

		echo "<td>\n";
		echo "<label for='suppr_pointage_$cpt' style='cursor:pointer;'>";
		echo $lig->nom;
		echo "</label>";
		echo "</td>\n";

		echo "<td>\n";
		echo "<textarea class='wrap' name=\"no_anti_inject_description_".$lig->id_type."\" rows='2' cols='60' onchange=\"changement()\">$lig->description</textarea>\n";
		echo "</td>\n";

		//echo "<td><input type='checkbox' name='suppr_pointage[]' id='suppr_pointage_$cpt' value=\"$lig->pointage\" onchange='changement();' /></td>\n";
		echo "<td><input type='checkbox' name='suppr_pointage[]' id='suppr_pointage_$cpt' value=\"$lig->id_type\" onchange='changement();' /></td>\n";
		echo "</tr>\n";

		$cpt++;
	}

	echo "</table>\n";
}

echo "<p style='margin-top:1em;'>Nouveau type de pointage&nbsp;:</p>\n";

echo "<table class='boireaus' border='1' summary='Nouvelle pointage'>\n";
echo "<tr class='lig1'>\n";
echo "<td>Nom&nbsp;</td>\n";
echo "<td><input type='text' name='nom' value='' onchange='changement();' /></td>\n";
echo "</tr>\n";
echo "<tr class='lig1'>\n";
echo "<td>Description&nbsp;</td>\n";
echo "<td>\n";
//echo "<input type='text' name='description' value='' onchange='changement();' />\n";

echo "<textarea class='wrap' name=\"no_anti_inject_description\" rows='2' cols='60' onchange=\"changement()\"></textarea>\n";

echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "<p><br /><input type='hidden' name='cpt' value='$cpt' /></p>\n";

echo "<p class='center'><input type='submit' name='valider' value='Valider' /></p>\n";

echo "</blockquote>\n";

echo "</fieldset>\n";
echo "</form>\n";

echo "<p style='color:red; margin-top:1em;'><em>A FAIRE&nbsp;:</em></p>
<ul>
	<li><p>Faire une page pour récapituler les totaux (<em>par classe/élève, mais aussi classement</em>).</p></li>
	<li><p>Pouvoir paramétrer des seuils provoquant un envoi de mail, un rappel en page d'accueil pour tel ou tel statut.<br />
	Pouvoir pointer qu'un traitement a été fait pour tel élève au passage du seuil.</p></li>
</ul>
<p><br /></p>";

require("../lib/footer.inc.php");
?>
