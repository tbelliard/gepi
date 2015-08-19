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

$sql="CREATE TABLE IF NOT EXISTS sp_seuils (
id_seuil int(11) NOT NULL AUTO_INCREMENT,
seuil int(11) NOT NULL,
periode CHAR(1) NOT NULL default 'y',
type VARCHAR(255) NOT NULL default '',
administrateur CHAR(1) NOT NULL default '',
scolarite CHAR(1) NOT NULL default '',
cpe CHAR(1) NOT NULL default '',
eleve CHAR(1) NOT NULL default '',
responsable CHAR(1) NOT NULL default '',
professeur_principal CHAR(1) NOT NULL default '',
PRIMARY KEY (id_seuil)
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

if(isset($_POST['modif_seuils'])) {
	check_token();

	$msg="";

	// Suppr
	$nb_suppr=0;
	if(isset($_POST['suppr_seuil'])) {
		$suppr_seuil=$_POST['suppr_seuil'];
		for($loop=0;$loop<count($suppr_seuil);$loop++) {
			$sql="DELETE FROM sp_seuils WHERE id_seuil='".$suppr_seuil[$loop]."';";
			//echo "$sql<br />";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
			if($del) {
				$nb_suppr++;
			}
			else {
				$msg.="Erreur lors de la suppression du seuil n°".$suppr_seuil[$loop].".<br />";
			}
		}
	}
	if($nb_suppr>0) {
		$msg.=$nb_suppr." seuil(s) supprimé(s).<br />";
	}

	// Modif
	$nb_modif=0;
	$sql="SELECT * FROM sp_seuils ORDER BY seuil, type;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if((isset($_POST['seuil_'.$lig->id_seuil]))&&
			(isset($_POST['periode_'.$lig->id_seuil]))&&
			(isset($_POST['type_'.$lig->id_seuil]))&&
			(isset($_POST['destinataire_'.$lig->id_seuil]))) {
				$destinataire_courant=$_POST['destinataire_'.$lig->id_seuil];
				$modif="n";
				if(($_POST['seuil_'.$lig->id_seuil]!=$lig->seuil)||
				($_POST['seuil_'.$lig->id_seuil]!=$lig->type)||
				($_POST['seuil_'.$lig->id_seuil]!=$lig->periode)) {
					$modif="y";
				}
				else {
					if((($lig->administrateur=="y")&&(!in_array('administrateur', $destinataire_courant)))||
					(($lig->administrateur=="n")&&(in_array('administrateur', $destinataire_courant)))) {
						$modif="y";
					}
					if((($lig->scolarite=="y")&&(!in_array('scolarite', $destinataire_courant)))||
					(($lig->scolarite=="n")&&(in_array('scolarite', $destinataire_courant)))) {
						$modif="y";
					}
					if((($lig->cpe=="y")&&(!in_array('cpe', $destinataire_courant)))||
					(($lig->cpe=="n")&&(in_array('cpe', $destinataire_courant)))) {
						$modif="y";
					}
					if((($lig->professeur_principal=="y")&&(!in_array('professeur_principal', $destinataire_courant)))||
					(($lig->professeur_principal=="n")&&(in_array('professeur_principal', $destinataire_courant)))) {
						$modif="y";
					}
					if((($lig->eleve=="y")&&(!in_array('eleve', $destinataire_courant)))||
					(($lig->eleve=="n")&&(in_array('eleve', $destinataire_courant)))) {
						$modif="y";
					}
					if((($lig->responsable=="y")&&(!in_array('responsable', $destinataire_courant)))||
					(($lig->responsable=="n")&&(in_array('responsable', $destinataire_courant)))) {
						$modif="y";
					}
					/*
					for($loop=0;$loop<count($destinataire_courant);$loop++) {
						
					}
					*/
				}

				if($modif=="y") {
					$sql="UPDATE sp_seuils SET seuil='".$_POST['seuil_'.$lig->id_seuil]."', 
									type='".$_POST['type_'.$lig->id_seuil]."', 
									periode='".$_POST['periode_'.$lig->id_seuil]."', 
									administrateur='".((in_array('administrateur', $destinataire_courant)) ? "y" : "n")."',
									scolarite='".((in_array('scolarite', $destinataire_courant)) ? "y" : "n")."',
									cpe='".((in_array('cpe', $destinataire_courant)) ? "y" : "n")."',
									professeur_principal='".((in_array('professeur_principal', $destinataire_courant)) ? "y" : "n")."',
									eleve='".((in_array('eleve', $destinataire_courant)) ? "y" : "n")."',
									responsable='".((in_array('responsable', $destinataire_courant)) ? "y" : "n")."' 
									WHERE id_seuil='".$lig->id_seuil."';";
					//echo "$sql<br />";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if($update) {
						$nb_modif++;
					}
					else {
						$msg.="Erreur lors de la modification du seuil n°".$suppr_seuil[$loop].".<br />";
					}
				}
			}
		}
	}
	if($nb_modif>0) {
		$msg.=$nb_modif." seuil(s) modifié(s).<br />";
	}

	// Ajout
	if((isset($_POST['seuil']))&&
	($_POST['seuil']>0)&&
	(isset($_POST['periode']))&&
	(isset($_POST['type']))&&
	(isset($_POST['destinataire']))) {
		$destinataire_courant=$_POST['destinataire'];

		$sql="INSERT INTO sp_seuils SET seuil='".$_POST['seuil']."', 
							type='".$_POST['type']."', 
							periode='".$_POST['periode']."', 
							administrateur='".((in_array('administrateur', $destinataire_courant)) ? "y" : "n")."',
							scolarite='".((in_array('scolarite', $destinataire_courant)) ? "y" : "n")."',
							cpe='".((in_array('cpe', $destinataire_courant)) ? "y" : "n")."',
							professeur_principal='".((in_array('professeur_principal', $destinataire_courant)) ? "y" : "n")."',
							eleve='".((in_array('eleve', $destinataire_courant)) ? "y" : "n")."',
							responsable='".((in_array('responsable', $destinataire_courant)) ? "y" : "n")."';";
		//echo "$sql<br />";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		if($insert) {
			$msg.="Nouveau seuil enregistré.<br />";
		}
		else {
			$msg.="Erreur lors de la modification du seuil n°".$suppr_seuil[$loop].".<br />";
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

debug_var();

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
	echo "<p class='bold'>Types de pointages existants&nbsp;:</p>\n";
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

echo "<p style='margin-top:1em;' class='bold'>Nouveau type de pointage&nbsp;:</p>\n";

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

echo "<p><br /></p>\n";
//=============================================

echo "<a name='seuils'></a>
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."#saisie_types' method='post' name='formulaire3'>
<fieldset class='fieldset_opacite50'>\n";
echo add_token_field();

echo "<p class='bold'>Seuils et actions</p>
<blockquote>
	<p>Vous pouvez programmer l'envoi de mail et/ou l'affichage d'un message en page d'accueil pour certaines personnes/statuts lorsqu'un certain nombre de pointages de menus ".$mod_disc_terme_incident."s est atteint par tel ou tel élève.</p>\n";

$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');

$cpt=0;
$sql="SELECT * FROM sp_seuils ORDER BY seuil, type;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>Aucun seuil n'est encore défini.</p>\n";
}
else {
	echo "
	<p class='bold'>Seuils existants&nbsp;:</p>
	<table class='boireaus boireaus_alt' border='1' summary='Tableau des seuils existants'>
		<tr>
			<th>Seuil</th>
			<th>Période</th>
			<th>Type</th>
			<th>Destinataires</th>
			<th>Supprimer</th>
		</tr>\n";
	while($lig=mysqli_fetch_object($res)) {
		$checked_radio_periode="";
		$style_periode_y="";
		$checked_radio_annee="";
		$style_periode_n="";
		if($lig->periode=="y") {
			$checked_radio_periode="checked ";
			$style_periode_y=" style='font-weight:bold'";
		}
		else {
			$checked_radio_annee="checked ";
			$style_periode_n=" style='font-weight:bold'";
		}

		$checked_radio_mail="";
		$checked_radio_message="";
		$style_type_mail="";
		$style_type_message="";
		if($lig->type=="mail") {
			$checked_radio_mail="checked ";
			$style_type_mail=" style='font-weight:bold'";
		}
		else {
			$checked_radio_message="checked ";
			$style_type_message=" style='font-weight:bold'";
		}

		$checked_administrateur="";
		$style_administrateur="";
		$checked_scolarite="";
		$style_scolarite="";
		$checked_cpe="";
		$style_cpe="";
		$checked_professeur_principal="";
		$style_professeur_principal="";
		$checked_eleve="";
		$style_eleve="";
		$checked_responsable="";
		$style_responsable="";
		if($lig->administrateur=="y") {
			$checked_administrateur="checked ";
			$style_administrateur=" style='font-weight:bold'";
		}
		if($lig->scolarite=="y") {
			$checked_scolarite="checked ";
			$style_scolarite=" style='font-weight:bold'";
		}
		if($lig->cpe=="y") {
			$checked_cpe="checked ";
			$style_cpe=" style='font-weight:bold'";
		}
		if($lig->professeur_principal=="y") {
			$checked_professeur_principal="checked ";
			$style_professeur_principal=" style='font-weight:bold'";
		}
		if($lig->eleve=="y") {
			$checked_eleve="checked ";
			$style_eleve=" style='font-weight:bold'";
		}
		if($lig->responsable=="y") {
			$checked_responsable="checked ";
			$style_responsable=" style='font-weight:bold'";
		}

		echo "
		<tr>
			<td>
				<select name='seuil_".$lig->id_seuil."' onchange='changement()'>";
		for($loop=1;$loop<200;$loop++) {
			$selected="";
			if($lig->seuil==$loop) {
				$selected=" selected='true'";
			}
			echo "
					<option value='$loop'$selected>$loop</option>";
		}
		echo "
				</select>
			</td>
			<td style='text-align:left;'>
				<input type='radio' name='periode_".$lig->id_seuil."' id='periode_y_".$lig->id_seuil."' value='y' onchange=\"changement();checkbox_change(this.id);checkbox_change('periode_n_".$lig->id_seuil."');\" $checked_radio_periode/><label for='periode_y_".$lig->id_seuil."' id='texte_periode_y_".$lig->id_seuil."'$style_periode_y>Période</label><br />
				<input type='radio' name='periode_".$lig->id_seuil."' id='periode_n_".$lig->id_seuil."' value='n' onchange=\"changement();checkbox_change(this.id);checkbox_change('periode_y_".$lig->id_seuil."');\" $checked_radio_annee/><label for='periode_n_".$lig->id_seuil."' id='texte_periode_n_".$lig->id_seuil."'$style_periode_n>Année</label>
			</td>
			<td style='text-align:left;'>
				<input type='radio' name='type_".$lig->id_seuil."' id='type_mail_".$lig->id_seuil."' value='mail' onchange=\"changement();checkbox_change(this.id);checkbox_change('type_message_".$lig->id_seuil."');\" $checked_radio_mail/><label for='type_mail_".$lig->id_seuil."' id='texte_type_mail_".$lig->id_seuil."'$style_type_mail>Mail</label><br />
				<input type='radio' name='type_".$lig->id_seuil."' id='type_message_".$lig->id_seuil."' value='message' onchange=\"changement();checkbox_change(this.id);checkbox_change('type_mail_".$lig->id_seuil."');\" $checked_radio_message/><label for='type_message_".$lig->id_seuil."' id='texte_type_message_".$lig->id_seuil."'$style_type_message>Message en page d'accueil</label>
			</td>
			<td style='text-align:left;'>
				<input type='checkbox' name='destinataire_".$lig->id_seuil."[]' id='destinataire_administrateur_".$lig->id_seuil."' value='administrateur' onchange=\"changement();checkbox_change(this.id);\" $checked_administrateur/><label for='destinataire_administrateur_".$lig->id_seuil."' id='texte_destinataire_administrateur_".$lig->id_seuil."'$style_administrateur>Administrateur</label><br />
				<input type='checkbox' name='destinataire_".$lig->id_seuil."[]' id='destinataire_scolarite_".$lig->id_seuil."' value='scolarite' onchange=\"changement();checkbox_change(this.id);\" $checked_scolarite/><label for='destinataire_scolarite_".$lig->id_seuil."' id='texte_destinataire_scolarite_".$lig->id_seuil."'$style_scolarite>Scolarité</label><br />
				<input type='checkbox' name='destinataire_".$lig->id_seuil."[]' id='destinataire_cpe_".$lig->id_seuil."' value='cpe' onchange=\"changement();checkbox_change(this.id);\" $checked_cpe/><label for='destinataire_cpe_".$lig->id_seuil."' id='texte_destinataire_cpe_".$lig->id_seuil."'$style_cpe>Cpe</label><br />
				<input type='checkbox' name='destinataire_".$lig->id_seuil."[]' id='destinataire_professeur_principal_".$lig->id_seuil."' value='professeur_principal' onchange=\"changement();checkbox_change(this.id);\" $checked_professeur_principal/><label for='destinataire_professeur_principal_".$lig->id_seuil."' id='texte_destinataire_professeur_principal_".$lig->id_seuil."'$style_professeur_principal>".$gepi_prof_suivi."</label><br />
				<input type='checkbox' name='destinataire_".$lig->id_seuil."[]' id='destinataire_eleve_".$lig->id_seuil."' value='eleve' onchange=\"changement();checkbox_change(this.id);\" $checked_eleve/><label for='destinataire_eleve_".$lig->id_seuil."' id='texte_destinataire_eleve_".$lig->id_seuil."'$style_eleve>Élève</label><br />
				<input type='checkbox' name='destinataire_".$lig->id_seuil."[]' id='destinataire_responsable_".$lig->id_seuil."' value='responsable' onchange=\"changement();checkbox_change(this.id);\" $checked_responsable/><label for='destinataire_responsable_".$lig->id_seuil."' id='texte_destinataire_responsable_".$lig->id_seuil."'$style_responsable>Responsable</label><br />
			</td>
			<td>
				<input type='checkbox' name='suppr_seuil[]' id='suppr_seuil_".$cpt."' value='".$lig->id_seuil."' onchange='changement()' />
			</td>
		</tr>";
		$cpt++;

	}

	echo "</table>\n";
}

echo "<p style='margin-top:1em; font-weight:bold;'>Nouveau seuil&nbsp;:</p>\n";

echo "<table class='boireaus boireaus_alt' border='1' summary='Nouveau seuil'>
		<tr>
			<th>Seuil</th>
			<th>Période</th>
			<th>Type</th>
			<th>Destinataires</th>
		</tr>
		<tr>
		<td>
			<select name='seuil' onchange='changement()'>
				<option value=''>---</option>";
		for($loop=1;$loop<200;$loop++) {
			echo "
				<option value='$loop'>$loop</option>";
		}
		echo "
			</select>
		</td>
		<td style='text-align:left;'>
			<input type='radio' name='periode' id='periode_y' value='y' onchange=\"changement();checkbox_change(this.id);checkbox_change('periode_n');\" checked /><label for='periode_y' id='texte_periode_y' style='font-weight:bold'>Période</label><br />
			<input type='radio' name='periode' id='periode_n' value='n' onchange=\"changement();checkbox_change(this.id);checkbox_change('periode_y');\" /><label for='periode_n' id='texte_periode_n'$style_periode_n>Année</label>
		</td>
		<td style='text-align:left;'>
			<input type='radio' name='type' id='type_mail' value='mail' onchange=\"changement();checkbox_change(this.id);checkbox_change('type_message');\" checked /><label for='type_mail' id='texte_type_mail' style='font-weight:bold'>Mail</label><br />
			<input type='radio' name='type' id='type_message' value='message' onchange=\"changement();checkbox_change(this.id);;checkbox_change('type_mail')\" /><label for='type_message' id='texte_type_message'>Message en page d'accueil</label>
		</td>
		<td style='text-align:left;'>
			<input type='checkbox' name='destinataire[]' id='destinataire_administrateur' value='administrateur' onchange=\"changement();checkbox_change(this.id);\" /><label for='destinataire_administrateur' id='texte_destinataire_administrateur'>Administrateur</label><br />
			<input type='checkbox' name='destinataire[]' id='destinataire_scolarite' value='scolarite' onchange=\"changement();checkbox_change(this.id);\" /><label for='destinataire_scolarite' id='texte_destinataire_scolarite'>Scolarité</label><br />
			<input type='checkbox' name='destinataire[]' id='destinataire_cpe' value='cpe' onchange=\"changement();checkbox_change(this.id);\" /><label for='destinataire_cpe' id='texte_destinataire_cpe'>Cpe</label><br />
			<input type='checkbox' name='destinataire[]' id='destinataire_professeur_principal' value='professeur_principal' onchange=\"changement();checkbox_change(this.id);\" /><label for='destinataire_professeur_principal' id='texte_destinataire_professeur_principal'>".$gepi_prof_suivi."</label><br />
			<input type='checkbox' name='destinataire[]' id='destinataire_eleve' value='eleve' onchange=\"changement();checkbox_change(this.id);\" /><label for='destinataire_eleve' id='texte_destinataire_eleve'>Élève</label><br />
			<input type='checkbox' name='destinataire[]' id='destinataire_responsable' value='responsable' onchange=\"changement();checkbox_change(this.id);\" /><label for='destinataire_responsable' id='texte_destinataire_responsable'>Responsable</label><br />
		</td>
	</tr>
</table>
<input type='hidden' name='modif_seuils' value='y' /></p>
<p class='center'><input type='submit' name='valider' value='Valider' /></p>
<p style='margin-top:1em; margin-left:4em; text-indent:-4em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li><p>Les seuils peuvent être calculés sur l'année, ou par période (<em>en fin de période un compteur est alors considéré comme réinitialisé pour les seuils</em>).</p></li>
	<li><p>Pour que les actions (<em>mails, message</em>) par période fonctionnent, ils faut que les dates de périodes soient renseignées dans <a href='../edt_organisation/edt_calendrier.php' target='_blank'>Emploi du temps/Gestion/Gestion du calendrier</a><br />
	Ils faut également que les classes y soient associées.</p></li>
</ul>
</blockquote>
</fieldset>
</form>\n";


echo "<p style='color:red; margin-top:1em;'><em>A FAIRE&nbsp;:</em></p>
<ul>
	<li><p>Faire une page pour récapituler les totaux (<em>par classe/élève, mais aussi classement</em>).</p></li>
	<li><p>Pouvoir paramétrer des seuils provoquant un envoi de mail, un rappel en page d'accueil pour tel ou tel statut.<br />
	Pouvoir insérer par lots des seuils (tous les 5 pointages (5, 10, 15,...))<br />
	Pouvoir pointer qu'un traitement a été fait pour tel élève au passage du seuil.</p></li>
</ul>
<p><br /></p>";

require("../lib/footer.inc.php");
?>
