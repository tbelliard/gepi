<?php
/*
 *
 * Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$sql="SELECT 1=1 FROM droits WHERE id='/groupes/modify_grp_group.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/groupes/modify_grp_group.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Gestion des ensembles de groupes',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$groupe_de_groupes=getSettingValue('denom_groupe_de_groupes');
if($groupe_de_groupes=="") {
	$groupe_de_groupes="ensemble de groupes";
}

$groupes_de_groupes=getSettingValue('denom_groupes_de_groupes');
if($groupes_de_groupes=="") {
	$groupes_de_groupes="ensembles de groupes";
}

// Initialisation des variables utilisées dans le formulaire

$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST['id_classe'] : null);
if(!isset($id_classe)) {
	if(isset($id_grp_groupe)) {
		$sql="SELECT id_classe FROM j_groupes_classes jgc, grp_groupes_groupes ggg WHERE ggg.id_grp_groupe='$id_grp_groupe' AND ggg.id_groupe=jgc.id_groupe LIMIT 1";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$lig=mysqli_fetch_object($res);
		$id_classe=$lig->id_classe;
	}

	if(!isset($id_classe)) {
		// On récupère la première classe avec période
		$sql="SELECT c.id FROM classes c, periodes p WHERE c.id=p.id_classe ORDER BY c.classe LIMIT 1;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$lig=mysqli_fetch_object($res);
		$id_classe=$lig->id;
	}
}

$classe=get_nom_classe($id_classe);

$sql="CREATE TABLE IF NOT EXISTS grp_groupes (
id int(11) NOT NULL AUTO_INCREMENT,
nom_court varchar(20) NOT NULL,
nom_complet varchar(100) NOT NULL,
description text NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS grp_groupes_admin (
id int(11) NOT NULL AUTO_INCREMENT,
id_grp_groupe int(11) NOT NULL,
login varchar(50) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS grp_groupes_groupes (
id int(11) NOT NULL AUTO_INCREMENT,
id_grp_groupe int(11) NOT NULL,
id_groupe int(11) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);


$mode = isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : null);
$id_grp_groupe = isset($_POST['id_grp_groupe']) ? $_POST['id_grp_groupe'] : (isset($_GET['id_grp_groupe']) ? $_GET['id_grp_groupe'] : null);
$tab_id_classe = isset($_POST['tab_id_classe']) ? $_POST['tab_id_classe'] : (isset($_GET['tab_id_classe']) ? $_GET['tab_id_classe'] : null);
$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : array());

$msg="";

$nom_court=isset($_POST['nom_court']) ? $_POST['nom_court'] : "";
$nom_complet=isset($_POST['nom_complet']) ? $_POST['nom_complet'] : "";
$description=isset($_POST['description']) ? $_POST['description'] : "";

if((isset($mode))&&($mode=='valider_creation_grp_groupes')) {
	check_token();

	$nom_court=remplace_accents($nom_court, "all");

	if(($nom_court=="")||(!preg_match("/^[A-Za-z]/", $nom_court))) {
		$msg.="Le nom court est invalide.<br />";
		$mode="creer_grp_groupes";
	}
	else {
		if($nom_complet=="") {
			$nom_complet=$nom_court;
		}

		if (isset($NON_PROTECT["description"])){
			$description=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["description"]));
		}
		else {
			$description="";
		}

		$sql="INSERT INTO grp_groupes SET nom_court='$nom_court', 
								nom_complet='$nom_complet', 
								description='$description';";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$insert) {
			$msg.="Erreur lors de la création du $groupe_de_groupes.<br />";
			$mode="creer_grp_groupes";
		}
		else {
			$id_grp_groupe=mysqli_insert_id($GLOBALS["mysqli"]);
			$mode="modifier_grp_groupes";
		}
	}
}

if(isset($id_grp_groupe)) {
	$sql="SELECT * FROM grp_groupes WHERE id='$id_grp_groupe';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$nom_court=$lig->nom_court;
		$nom_complet=$lig->nom_complet;
		$description=$lig->description;
	}
}


if((isset($mode))&&($mode=='supprimer_grp_groupes')&&(isset($id_grp_groupe))) {
	check_token();

	$sql="DELETE FROM grp_groupes_groupes WHERE id_grp_groupe='$id_grp_groupe';";
	$del=mysqli_query($GLOBALS["mysqli"], $sql);
	if(!$del) {
		$msg.="Erreur lors de la suppression des associations avec des groupes pour le $groupe_de_groupes n°$id_grp_groupe.<br />";
	}
	else {
		$sql="DELETE FROM grp_groupes_admin WHERE id_grp_groupe='$id_grp_groupe';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$del) {
			$msg.="Erreur lors de la suppression des associations avec des utilisateurs pour le $groupe_de_groupes n°$id_grp_groupe.<br />";
		}
		else {

			$sql="DELETE FROM grp_groupes WHERE id='$id_grp_groupe';";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$del) {
				$msg.="Erreur lors de la suppression du $groupe_de_groupes n°$id_grp_groupe.<br />";
			}
			else {
				$msg.="$groupe_de_groupes n°$id_grp_groupe supprimé.<br />";
			}
		}
	}

	unset($mode);
}


if((isset($mode))&&($mode=='valider_ajout_user')&&(isset($id_grp_groupe))) {
	check_token();

	$mode="modifier_grp_groupes";

	$sql="DELETE FROM grp_groupes_admin WHERE id_grp_groupe='$id_grp_groupe';";
	$del=mysqli_query($GLOBALS["mysqli"], $sql);
	if(!$del) {
		$msg.="Erreur lors du nettoyage préalable des utilisateurs adminstrateurs du $groupe_de_groupes n°$id_grp_groupe.<br />";
	}
	else {
		$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : "";

		$cpt=0;
		for($loop=0;$loop<count($login_user);$loop++) {
			$sql="INSERT INTO grp_groupes_admin SET login='".$login_user[$loop]."', 
									id_grp_groupe='$id_grp_groupe';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$insert) {
				$msg.="Erreur lors de l'association avec ".civ_nom_prenom($login_user[$loop]).".<br />";
			}
			else {
				$cpt++;
			}
		}

		$msg.="$cpt utilisateur(s) est(sont) autorisé(s) à modifier la liste des élèves des groupes du $groupe_de_groupes n°$id_grp_groupe.<br />";
	}

}


if((isset($mode))&&($mode=='valider_ajout_groupe')&&(isset($id_grp_groupe))) {
	check_token();

	$mode="modifier_grp_groupes";

	$tab_groupes_deja=array();
	$sql="SELECT DISTINCT id_groupe FROM grp_groupes_groupes WHERE id_grp_groupe='$id_grp_groupe';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tab_groupes_deja[]=$lig->id_groupe;
	}

	$cpt=0;
	for($loop=0;$loop<count($id_groupe);$loop++) {
		if(!in_array($id_groupe[$loop], $tab_groupes_deja)) {
			$sql="INSERT INTO grp_groupes_groupes SET id_groupe='".$id_groupe[$loop]."', 
									id_grp_groupe='$id_grp_groupe';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$insert) {
				$msg.="Erreur lors de l'association avec le groupe n°".$id_groupe[$loop].".<br />";
			}
			else {
				$cpt++;
			}
			// Pour ne pas ajouter plusieurs fois les mêmes
			$tab_groupes_deja[]=$id_groupe[$loop];
		}
	}
	$msg.="$cpt groupe(s) ajouté(s).<br />";

	$cpt=0;
	for($loop=0;$loop<count($tab_groupes_deja);$loop++) {
		if(!in_array($tab_groupes_deja[$loop], $id_groupe)) {
			$sql="DELETE FROM grp_groupes_groupes WHERE id_groupe='".$tab_groupes_deja[$loop]."' AND 
									id_grp_groupe='$id_grp_groupe';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$insert) {
				$msg.="Erreur lors de la suppression de l'association avec le groupe n°".$tab_groupes_deja[$loop].".<br />";
			}
			else {
				$cpt++;
			}
		}
	}
	$msg.="$cpt groupe(s) retiré(s).<br />";

}


$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
$titre_page = ucfirst($groupes_de_groupes);
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

//echo "id_classe=$id_classe<br />";

echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
?>
<p class="bold">
<a href="edit_class.php?id_classe=<?php echo $id_classe;?>" title="Retour aux enseignements de <?php echo $classe;?>"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
<?php

// =================================
$chaine_options_classes="";
$sql="SELECT id, classe FROM classes ORDER BY classe";
$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_class_tmp)>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;
	$cpt_classe=0;
	$num_classe=-1;
	while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
			$temoin_tmp=1;
			if($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		else {
			$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
		}

		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}
		$cpt_classe++;
	}
}
// =================================

if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe préc.</a>";}
if($chaine_options_classes!="") {

	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_classe').selectedIndex=$num_classe;
			}
		}
	}
</script>\n";


	echo " | <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
	echo $chaine_options_classes;
	echo "</select>\n";
}
if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suiv.</a>";}

	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	if(!isset($mode)) {

		echo "</p>
</form>

<p class='bold'>Choisissez&nbsp;:</p>
<ul>
	<li><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;mode=creer_grp_groupes'>Créer un nouveau $groupe_de_groupes</a></li>";

		$sql="SELECT DISTINCT gg.* FROM grp_groupes gg, 
					grp_groupes_groupes ggg,
					j_groupes_classes jgc 
				WHERE gg.id=ggg.id_grp_groupe AND 
					jgc.id_groupe=ggg.id_groupe AND 
					jgc.id_classe='$id_classe'
				ORDER BY gg.nom_court, gg.nom_complet;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0){
			echo "
	<li>
		<p>Modifier un $groupe_de_groupes (<em>associé à $classe</em>) existant&nbsp;:</p>
		<ul>";
			while($lig=mysqli_fetch_object($res)){
				$current_grp_groupes=get_tab_grp_groupes($lig->id);
				echo "
			<li>
				<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;mode=modifier_grp_groupes&amp;id_grp_groupe=$lig->id' title=\"Modifier le $groupe_de_groupes n°$lig->id

".$current_grp_groupes['description']."\">".$current_grp_groupes['nom_court']." (<em style='font-size:small'>".$current_grp_groupes['nom_complet']."</em>)</a> 
				<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;mode=supprimer_grp_groupes&amp;id_grp_groupe=$lig->id".add_token_in_url()."' title=\"Supprimer ce $groupe_de_groupes\" onclick=\"confirm('Êtes-vous sûr de vouloir supprimer ce ".addslashes($groupe_de_groupes)."')\"><img src='../images/icons/delete.png' class='icone16' alt='Supprimer' /></a>
			</li>";
			}
			echo "
		</ul>
	</li>";

		}

		$sql="SELECT DISTINCT gg.* FROM grp_groupes gg, 
					grp_groupes_groupes ggg,
					j_groupes_classes jgc 
				WHERE gg.id=ggg.id_grp_groupe AND 
					jgc.id_groupe=ggg.id_groupe AND 
					jgc.id_classe!='$id_classe'
				ORDER BY gg.nom_court, gg.nom_complet;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0){
			echo "
	<li>
		<p>Modifier un $groupe_de_groupes (<em>associé à d'autres classes</em>) existant&nbsp;:</p>
		<ul>";
			while($lig=mysqli_fetch_object($res)){
				$current_grp_groupes=get_tab_grp_groupes($lig->id);
				echo "
			<li>
				<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;mode=modifier_grp_groupes&amp;id_grp_groupe=$lig->id' title=\"Modifier le $groupe_de_groupes n°$lig->id

".$current_grp_groupes['description']."\">".$current_grp_groupes['nom_court']." (<em style='font-size:small'>".$current_grp_groupes['nom_complet']."</em>)</a> 
				<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;mode=supprimer_grp_groupes&amp;id_grp_groupe=$lig->id".add_token_in_url()."' title=\"Supprimer ce $groupe_de_groupes\" onclick=\"confirm('Êtes-vous sûr de vouloir supprimer ce ".addslashes($groupe_de_groupes)."')\"><img src='../images/icons/delete.png' class='icone16' alt='Supprimer' /></a>
			</li>";
			}
			echo "
		</ul>
	</li>";

		}

		$sql="SELECT gg.* FROM grp_groupes gg
				WHERE gg.id NOT IN (SELECT DISTINCT id_grp_groupe FROM grp_groupes_groupes)
				ORDER BY gg.nom_court, gg.nom_complet;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0){
			echo "
	<li>
		<p>Modifier un $groupe_de_groupes existant sans groupe/enseignement associé&nbsp;:</p>
		<ul>";
			while($lig=mysqli_fetch_object($res)){
				$current_grp_groupes=get_tab_grp_groupes($lig->id);
				echo "
			<li>
				<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;mode=modifier_grp_groupes&amp;id_grp_groupe=$lig->id' title=\"Modifier le $groupe_de_groupes n°$lig->id

".$current_grp_groupes['description']."\">".$current_grp_groupes['nom_court']." (<em style='font-size:small'>".$current_grp_groupes['nom_complet']."</em>)</a> 
				<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;mode=supprimer_grp_groupes&amp;id_grp_groupe=$lig->id".add_token_in_url()."' title=\"Supprimer ce $groupe_de_groupes\" onclick=\"confirm('Êtes-vous sûr de vouloir supprimer ce ".addslashes($groupe_de_groupes)."')\"><img src='../images/icons/delete.png' class='icone16' alt='Supprimer' /></a>
			</li>";
			}
			echo "
		</ul>
	</li>";

		}

		echo "
</ul>";

	}
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	elseif($mode=="creer_grp_groupes") {
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Index $groupes_de_groupes</a></p>
</form>

<form action='".$_SERVER['PHP_SELF']."' name='form_creation' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='id_classe' value='$id_classe' />

		<p class='bold'>Créer un $groupe_de_groupes&nbsp;:</p>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th>Nom court&nbsp;:</th>
				<td><input type='text' name='nom_court' value=\"$nom_court\" onchange='changement()' /></td>
			</tr>
			<tr>
				<th>Nom complet&nbsp;:</th>
				<td><input type='text' name='nom_complet' value=\"$nom_complet\" onchange='changement()' /></td>
			</tr>
			<tr>
				<th>Description&nbsp;:</th>
				<td><textarea name='no_anti_inject_description' rows='4' cols='40' onchange='changement()'></textarea></td>
			</tr>
		</table>
		<input type='hidden' name='mode' value='valider_creation_grp_groupes' />
		<p><input type='submit' value='Créer' /></p>
	</fieldset>
</form>";
	}
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	elseif(($mode=="modifier_grp_groupes")&&(isset($id_grp_groupe))) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Index $groupes_de_groupes</a></p>
</form>";

		// Récupérer les infos sur le groupe de groupes courant
		$tab_grp_groupes=get_tab_grp_groupes($id_grp_groupe, array('classes', 'matieres', 'profs'));

		echo "
<form action='".$_SERVER['PHP_SELF']."' name='form_creation' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='id_grp_groupe' value='$id_grp_groupe' />
		<input type='hidden' name='id_classe' value='$id_classe' />

		<p class='bold'>Modifier le $groupe_de_groupes n°$id_grp_groupe&nbsp;: ".$tab_grp_groupes['nom_court']."</p>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th>Nom court&nbsp;:</th>
				<td><input type='text' name='nom_court' value=\"$nom_court\" onchange='changement()' /></td>
			</tr>
			<tr>
				<th>Nom complet&nbsp;:</th>
				<td><input type='text' name='nom_complet' value=\"$nom_complet\" onchange='changement()' /></td>
			</tr>
			<tr>
				<th>Description&nbsp;:</th>
				<td><textarea name='no_anti_inject_description' rows='4' cols='40' onchange='changement()'>".$description."</textarea></td>
			</tr>
		</table>
		<input type='hidden' name='mode' value='valider_modification_grp_groupes' />
		<p><input type='submit' value='Valider' /></p>
	</fieldset>
</form>";

		// Lister les groupes associés
		if(count($tab_grp_groupes['groupes'])>0) {
			echo "
<form action='".$_SERVER['PHP_SELF']."' name='form_liste_groupes' method='post' style='margin-top:1em;'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='id_grp_groupe' value='$id_grp_groupe' />
		<input type='hidden' name='id_classe' value='$id_classe' />

		<p>Les groupes/enseignements suivants sont associés au $groupe_de_groupes&nbsp;:</p>
		<ul>";
			foreach($tab_grp_groupes['groupes'] as $cpt => $current_group) {
				echo "
			<li><input type='checkbox' name='id_groupe[]' id='group_$cpt' value='".$current_group['id']."' checked onchange=\"checkbox_change('group_$cpt')\" /><label for='group_$cpt' id='texte_group_$cpt' style='font-weight:bold;' title=\"".$current_group['name']." (".$current_group['description'].")
Classes     : ".$current_group['classlist_string']."
Enseignants : ".$current_group['profs']['proflist_string']."\">".$current_group['name']." (<em style='font-size:small;'>".$current_group['description']." en ".$current_group['classlist_string']." avec ".$current_group['profs']['proflist_string']."</em>)"."</label></li>";
			}
			echo "
		</ul>
		<input type='hidden' name='mode' value='valider_ajout_groupe' />
		<p><input type='submit' value='Valider' /></p>
	</fieldset>
</form>";
		}
		else {
			echo "<p style='margin-top:1em;'>Aucun enseignement/groupe n'est encore associé au $groupe_de_groupes.</p>";
		}

		// Proposer d'associer de nouveaux groupes
		echo "
<p><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;mode=ajouter_groupes&amp;id_grp_groupe=$id_grp_groupe' title=\"Ajouter des groupes au $groupe_de_groupes.\">Ajouter des enseignements.</a></p>";

		/*
		echo "<pre>";
		print_r($tab_grp_groupes);
		echo "</pre>";
		*/

		// Lister les profs et cpe administrateurs
		if(count($tab_grp_groupes['admin'])>0) {
			echo "
<form action='".$_SERVER['PHP_SELF']."' name='form_liste_admin' method='post' style='margin-top:1em;'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='id_grp_groupe' value='$id_grp_groupe' />
		<input type='hidden' name='id_classe' value='$id_classe' />

		<p>Les utilisateurs suivants administrent le $groupe_de_groupes&nbsp;:</p>
		<ul>";
			foreach($tab_grp_groupes['admin'] as $cpt => $current_user) {
				echo "
			<li><input type='checkbox' name='login_user[]' id='user_$cpt' value='".$current_user['login']."' checked onchange=\"checkbox_change('user_$cpt')\" /><label for='user_$cpt' id='texte_user_$cpt' style='font-weight:bold;'>".$current_user['denomination']." (<em>".$current_user['statut']."</em>)"."</label></li>";
			}
			echo "
		</ul>
		<input type='hidden' name='mode' value='valider_ajout_user' />
		<p><input type='submit' value='Valider' /></p>

		".js_checkbox_change_style('checkbox_change', 'texte_', "y")."
	</fieldset>
</form>";
		}
		else {
			echo "<p style='margin-top:1em;'>Aucun utilisateur n'administre encore le $groupe_de_groupes.</p>";
		}

		// Proposer d'associer de nouveaux utilisateurs
		echo "
<p><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;mode=ajouter_user&amp;id_grp_groupe=$id_grp_groupe' title=\"Ajouter des groupes au $groupe_de_groupes.\">Ajouter des utilisateurs autorisés à modifier la liste des élèves des groupes de ce $groupe_de_groupes.</a></p>";

		echo "<p style='color:red; margin-top:1em;'>ATTENTION à ne pas modifier simultanément plusieurs formulaires.</p>";

	}
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	elseif(($mode=="ajouter_user")&&(isset($id_grp_groupe))) {
		// Récupérer les infos sur le groupe de groupes courant
		$tab_grp_groupes=get_tab_grp_groupes($id_grp_groupe, array('classes', 'matieres', 'profs'));

		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Index $groupes_de_groupes</a> | <a href='".$_SERVER['PHP_SELF']."?mode=modifier_grp_groupes&amp;id_grp_groupe=$id_grp_groupe&amp;id_classe=$id_classe'>".$tab_grp_groupes['nom_court']."</a>
	</p>
</form>";

		$tab_user_preselectionnes=array();
		if(count($tab_grp_groupes['admin'])>0) {
			foreach($tab_grp_groupes['admin'] as $cpt => $current_user) {
				$tab_user_preselectionnes[]=$current_user['login'];
			}
		}

		echo "
<form action='".$_SERVER['PHP_SELF']."' name='form_ajout_user' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='mode' value='valider_ajout_user' />
		<input type='hidden' name='id_grp_groupe' value='$id_grp_groupe' />
		<input type='hidden' name='id_classe' value='$id_classe' />

		<p class='bold'>Ajouter des administrateurs du $groupe_de_groupes n°$id_grp_groupe&nbsp;: ".$tab_grp_groupes['nom_court']."</p>

		".liste_checkbox_utilisateurs(array("scolarite", "cpe", "professeur"), $tab_user_preselectionnes)."

		<p><input type='submit' value='Valider' /></p>

		".js_checkbox_change_style('checkbox_change', 'texte_', "y")."
	</fieldset>
</form>";

		if((isset($tab_grp_groupes['groupes']))&&(count($tab_grp_groupes['groupes'])>0)) {
			echo "
<p style='text-indent:-3em;margin-left:3em;margin-top:1em;'><strong>Enseignements inscrits dans le $groupe_de_groupes&nbsp;:</strong><br />";
			foreach($tab_grp_groupes['groupes'] as $key => $current_group) {
				echo "
".$current_group['name']." (<em>".$current_group['classlist_string']."</em>) (<em>".$current_group['profs']['proflist_string']."</em>)<br />";
			}
			echo "</p>";
		}
		/*
		echo "<pre>";
		print_r($tab_grp_groupes);
		echo "</pre>";
		*/
	}
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	elseif(($mode=="ajouter_groupes")&&(isset($id_grp_groupe))) {
		// Récupérer les infos sur le groupe de groupes courant
		$tab_grp_groupes=get_tab_grp_groupes($id_grp_groupe, array('classes', 'matieres', 'profs'));

		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Index $groupes_de_groupes</a> | <a href='".$_SERVER['PHP_SELF']."?mode=modifier_grp_groupes&amp;id_grp_groupe=$id_grp_groupe&amp;id_classe=$id_classe'>".$tab_grp_groupes['nom_court']."</a>";

		// Choix des classes dans lesquelles sélectionner des groupes
		if(!isset($tab_id_classe)) {
			echo "
	</p>
</form>

	<p class='bold'>Ajout de groupes au $groupe_de_groupes n°$id_grp_groupe&nbsp;: ".$tab_grp_groupes['nom_court']."</p>
	<p>Choisissez les classes dans lesquelles sélectionner les groupes/enseignements.</p>

<form action='".$_SERVER['PHP_SELF']."' name='form_ajout_groupe' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='mode' value='ajouter_groupes' />
		<input type='hidden' name='id_grp_groupe' value='$id_grp_groupe' />
		<input type='hidden' name='id_classe' value='$id_classe' />";

			$tmp_tab_classe=array();
			$sql="SELECT DISTINCT p.id_classe, c.classe FROM periodes p, classes c WHERE p.id_classe=c.id ORDER BY c.classe";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			$cpt=0;
			while($lig=mysqli_fetch_object($res)) {
				$tmp_tab_classe['texte'][$cpt]=$lig->classe;
				$tmp_tab_classe['nom_champ'][$cpt]="tab_id_classe[]";
				$tmp_tab_classe['id_champ'][$cpt]="tab_id_classe_".$lig->id_classe;
				$tmp_tab_classe['valeur'][$cpt]=$lig->id_classe;
				$cpt++;
			}

			$tab_valeurs_preselectionnees=array();
			if(count($tab_grp_groupes['groupes'])>0) {
				foreach($tab_grp_groupes['groupes'] as $cpt => $current_group) {
					for($loop=0;$loop<count($current_group['classes']['list']);$loop++) {
						$tab_valeurs_preselectionnees[]=$current_group['classes']['list'][$loop];
					}
				}
			}

			if(!in_array($id_classe, $tab_valeurs_preselectionnees)) {
				$tab_valeurs_preselectionnees[]=$id_classe;
			}

			tab_liste_checkbox($tmp_tab_classe['texte'], $tmp_tab_classe['nom_champ'], $tmp_tab_classe['id_champ'], $tmp_tab_classe['valeur'], "checkbox_change_classe", "modif_coche", 3, $tab_valeurs_preselectionnees);

		echo "
		<p><input type='submit' value='Valider' /></p>
	</fieldset>
</form>";
			require("../lib/footer.inc.php");
			die();

		}

		//========================================================
		// Choix des groupes

		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=ajouter_groupes&amp;id_grp_groupe=$id_grp_groupe&amp;id_classe=$id_classe'>Ajouter des groupes d'autres classes</a>
	</p>
</form>

<p class='bold'>Ajout de groupes au $groupe_de_groupes n°$id_grp_groupe&nbsp;: ".$tab_grp_groupes['nom_court']."</p>
<p>Choisissez les groupes/enseignements.</p>

<form action='".$_SERVER['PHP_SELF']."' name='form_ajout_groupe' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='mode' value='valider_ajout_groupe' />
		<input type='hidden' name='id_grp_groupe' value='$id_grp_groupe' />
		<input type='hidden' name='id_classe' value='$id_classe' />";

		$tab_valeurs_preselectionnees=array();
		if(count($tab_grp_groupes['groupes'])>0) {
			foreach($tab_grp_groupes['groupes'] as $cpt => $current_group) {
				$tab_valeurs_preselectionnees[]=$current_group['id'];
			}
		}

		$cpt=0;
		$get_groups_for_class_avec_proflist="y";
		for($loop=0;$loop<count($tab_id_classe);$loop++) {
			echo "
		<div style='float:left; width:30em; margin:0.5em;' class='fieldset_opacite50'>
			<p class='bold'>Classe de ".get_nom_classe($tab_id_classe[$loop])."</p>";
			$tmp_tab_grp=get_groups_for_class($tab_id_classe[$loop]);
			foreach($tmp_tab_grp as $current_group) {
				echo "
			<input type='hidden' name='tab_id_classe[]' value='$tab_id_classe[$loop]' />
			<input type='checkbox' name='id_groupe[]' id='id_groupe_$cpt' value='".$current_group['id']."' onchange=\"checkbox_change('id_groupe_$cpt'); controle_doublons($cpt);\" ";
				if(in_array($current_group['id'], $tab_valeurs_preselectionnees)) {
					echo "checked ";
				}
				echo "/><label for='id_groupe_$cpt' id='texte_id_groupe_$cpt' title=\"".$current_group['name']." (".$current_group['description'].")
Classes     : ".$current_group['classlist_string']."
Enseignants : ".$current_group['proflist_string']."\"";
				if(in_array($current_group['id'], $tab_valeurs_preselectionnees)) {
					echo " style='font-weight:bold;'";
				}
				echo ">".$current_group['name']." (<em style='font-size:small'>".$current_group['description']."</em>)</label><br />";

				$cpt++;
			}
			echo "
		</div>";
		}

		echo "
		<p><input type='submit' value='Valider' /></p>

		<div style='clear:both;'></div>
		<p><input type='submit' value='Valider' /></p>

		".js_checkbox_change_style('checkbox_change', 'texte_', "y")."

		<script type='text/javascript'>
			function change_style_grp(num) {
				checkbox_change('id_groupe_'+num);
			}

			function controle_doublons(num) {
				if(document.getElementById('id_groupe_'+num)) {
					valeur_id_groupe=document.getElementById('id_groupe_'+num).value;
					//alert('valeur_id_groupe='+valeur_id_groupe)
					if(document.getElementById('id_groupe_'+num).checked) {
						for(i=0;i<$cpt;i++) {
							if(i!=num) {
								if(document.getElementById('id_groupe_'+i)) {
									//alert('document.getElementById(id_groupe_'+i+').value='+document.getElementById('id_groupe_'+i).value)
									if(document.getElementById('id_groupe_'+i).value==valeur_id_groupe) {
										document.getElementById('id_groupe_'+i).checked=true;
										change_style_grp(i);
									}
								}
							}
						}
					}
					else {
						for(i=0;i<$cpt;i++) {
							if(i!=num) {
								if(document.getElementById('id_groupe_'+i)) {
									//alert('document.getElementById(id_groupe_'+i+').value='+document.getElementById('id_groupe_'+i).value)
									if(document.getElementById('id_groupe_'+i).value==valeur_id_groupe) {
										document.getElementById('id_groupe_'+i).checked=false;
										change_style_grp(i);
									}
								}
							}
						}
					}
				}
			}
		</script>
	</fieldset>
</form>";
	}
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	else {
		echo " | <a href='".$_SERVER['PHP_SELF']."'>Index $groupes_de_groupes</a></p>
</form>

<p style='color:red'>Mode inconnu???</p>";
	}
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


	echo "
<p style='margin-left:4em;text-indent:-4em; margin-top:1em;'><em>NOTES&nbsp;:</em> Les \"$groupes_de_groupes\" sont destinés à permettre à des professeurs de gérer la composition de certains groupes.<br />
Typiquement, des groupes dont la composition change toutes les 3 semaines, comme par exemple des groupes d'aide aux devoirs.<br />
Mettre à jour ces groupes permet d'effectuer les saisies d'absences avec des groupes à jour.<br />
Si les professeurs et AED font la répartition des élèves lors d'une réunion, il peut être commode de leur laisser effectuer la mise à jour directement.</p>

<p style='color:red; margin-top:1em;'>
A FAIRE :<br />
- Donner l'accès aux utilisateurs administrateurs des $groupes_de_groupes à des pages de sélection des élèves type groupes/edit_eleves.php et groupes/repartition_ele_grp.php<br />
- Pouvoir éditer les listes produites en CSV et PDF.<br />
</p>";

	require("../lib/footer.inc.php");
?>
