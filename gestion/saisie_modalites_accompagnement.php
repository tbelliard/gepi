<?php
/*
*
* Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : (isset($_GET['login_eleve']) ? $_GET['login_eleve'] : NULL);

$tab_modalite_accompagnement=get_tab_modalites_accompagnement();

//debug_var();

if(isset($_POST['is_posted_modalites_classes'])) {
	check_token();

	$nb_maj=0;
	$nb_reg=0;
	$nb_del=0;

	$tab_modalites_ele=array();
	$sql="SELECT DISTINCT jmae.* FROM j_modalite_accompagnement_eleve jmae, 
				eleves e, 
				j_eleves_classes jec 
			WHERE jmae.id_eleve=e.id_eleve AND 
				jec.login=e.login AND 
				jec.id_classe='".$id_classe."' 
			ORDER BY e.nom, e.prenom;";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		if(!isset($_POST['accompagnement_'.$lig->id_eleve."_".$lig->code])) {
			$sql="DELETE FROM j_modalite_accompagnement_eleve WHERE id_eleve='".$lig->id_eleve."' AND code='".$lig->code."';";
			$del=mysqli_query($mysqli, $sql);
			$nb_del++;
		}
		else {
			$tab_modalites_ele[$lig->id_eleve][$lig->code]=$lig->commentaire;
		}
	}
	if($nb_del>0) {
		$msg.=$nb_del." modalité(s) d'accompagnement supprimée(s).<br />";
	}

	$tab_ele=array();
	$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='".$id_classe."' ORDER BY e.nom, e.prenom;";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		//$tab_ele[]=$lig;

		foreach($tab_modalite_accompagnement["code"] as $code => $libelle) {
			if(isset($_POST['accompagnement_'.$lig->id_eleve."_".$code])) {
				if(isset($tab_modalites_ele[$lig->id_eleve][$code])) {
					/*
					if((isset($_POST['textarea_'.$lig->id_eleve."_".$code]))&&($_POST['textarea_'.$lig->id_eleve."_".$code]!=$tab_modalites_ele[$lig->id_eleve][$code])) {
						//$sql="UPDATE j_modalite_accompagnement_eleve SET commentaire='".mysqli_real_escape_string($mysqli, stripslashes($_POST['textarea_'.$lig->id_eleve."_".$code]))."' WHERE id_eleve='".$lig->id_eleve."' AND code='".$code."';";
						$sql="UPDATE j_modalite_accompagnement_eleve SET commentaire='".mysqli_real_escape_string($mysqli, $_POST['textarea_'.$lig->id_eleve."_".$code])."' WHERE id_eleve='".$lig->id_eleve."' AND code='".$code."';";
					*/

					if((isset($NON_PROTECT['textarea_'.$lig->id_eleve."_".$code]))&&($NON_PROTECT['textarea_'.$lig->id_eleve."_".$code]!=$tab_modalites_ele[$lig->id_eleve][$code])) {
						$sql="UPDATE j_modalite_accompagnement_eleve SET commentaire='".mysqli_real_escape_string($mysqli, $NON_PROTECT['textarea_'.$lig->id_eleve."_".$code])."' WHERE id_eleve='".$lig->id_eleve."' AND code='".$code."';";

						$update=mysqli_query($mysqli, $sql);
						if(!$update) {
							$msg.="Erreur lors de la mise à jour de la modalité d'accompagnement $code pour ".$lig->nom." ".$lig->prenom.".<br />";
						}
						else {
							$nb_maj++;
						}
					}
				}
				else {
					//$sql="INSERT INTO j_modalite_accompagnement_eleve SET commentaire='".mysqli_real_escape_string($mysqli, stripslashes($_POST['textarea_'.$lig->id_eleve."_".$code]))."', id_eleve='".$lig->id_eleve."', code='".$code."';";
					//$sql="INSERT INTO j_modalite_accompagnement_eleve SET commentaire='".mysqli_real_escape_string($mysqli, $_POST['textarea_'.$lig->id_eleve."_".$code])."', id_eleve='".$lig->id_eleve."', code='".$code."';";

					if(isset($NON_PROTECT['textarea_'.$lig->id_eleve."_".$code])) {
						$sql="INSERT INTO j_modalite_accompagnement_eleve SET commentaire='".mysqli_real_escape_string($mysqli, $NON_PROTECT['textarea_'.$lig->id_eleve."_".$code])."', id_eleve='".$lig->id_eleve."', code='".$code."';";
					}
					else {
						$sql="INSERT INTO j_modalite_accompagnement_eleve SET commentaire='', id_eleve='".$lig->id_eleve."', code='".$code."';";
					}

					$insert=mysqli_query($mysqli, $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'enregistrement de la modalité d'accompagnement $code pour ".$lig->nom." ".$lig->prenom.".<br />";
					}
					else {
						$nb_reg++;
					}
				}
			}
		}
	}

	if($nb_reg>0) {
		$msg.=$nb_reg." modalité(s) d'accompagnement enregistrée(s).<br />";
	}
	if($nb_maj>0) {
		$msg.=$nb_maj." modalité(s) d'accompagnement mise(s) à jour.<br />";
	}

}

// =================================
$chaine_options_classes="
			<option value=''>---</option>";
$sql="SELECT id, classe FROM classes ORDER BY classe";
$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_class_tmp)>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;

	$cpt_classe=0;
	$num_classe=-1;

	while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
		if((isset($id_classe))&&($lig_class_tmp->id==$id_classe)) {
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="
			<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>";
			$temoin_tmp=1;
			if($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
				$chaine_options_classes.="
				<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>";
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		else {
			$chaine_options_classes.="
			<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>";
		}

		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}

		$cpt_classe++;
	}
}
// =================================

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';

//**************** EN-TETE **************************************
$titre_page = "Modalités d'enseignement";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>
	<p class='bold'>
		<a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";

if($id_class_prec!=0){
	echo "
		 | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe précédente</a>";
 }

if($chaine_options_classes!="") {

	echo "
		<script type='text/javascript'>
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
		</script>
		 | <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">".$chaine_options_classes."
		</select>\n";
}

if($id_class_suiv!=0){
	echo "
		 | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suivante</a>";
 }

echo "
	</p>
</form>\n";

if((!isset($login_eleve))&&(!isset($id_classe))) {
	// A faire: proposer une recherche sur un élève

	echo "<p class='bold'>Choisissez la classe pour laquelle saisir des modalités d'accompagnement&nbsp;:</p>";

	$tab_txt=array();
	$tab_lien=array();
	$sql=retourne_sql_mes_classes();
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tab_txt[]=$lig->classe;
		$tab_lien[]=$_SERVER["PHP_SELF"]."?id_classe=".$lig->id_classe;
	}
	$nbcol=3;
	echo tab_liste($tab_txt,$tab_lien,$nbcol);

}
elseif(isset($login_eleve)) {

	echo "<h2>Modalités d'accompagnement pour ".get_nom_prenom_eleve($login_eleve)."</h2>";

	$tab_ele[0]=get_info_eleve($login_eleve);

	$tab_modalites_ele=array();
	$sql="SELECT DISTINCT jmae.* FROM j_modalite_accompagnement_eleve jmae, 
				eleves e 
			WHERE jmae.id_eleve=e.id_eleve AND 
				e.login='".$login_eleve."';";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tab_modalites_ele[$lig->id_eleve][$lig->code]=$lig->commentaire;
	}

	$rowspan=count($tab_modalite_accompagnement["indice"]);

	echo "
<form action='".$_SERVER["PHP_SELF"]."' method='post' name='form2'>
	<fieldset class='fieldset_opacite50'>
		<p><input type='submit' value='Enregistrer' /></p>
		".add_token_field()."
		<input type='hidden' name='login_eleve' value='$login_eleve' />
		<input type='hidden' name='is_posted_modalites_eleve' value='y' />
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th colspan='2'>Élève</th>
					<th colspan='3'>Accompagnement</th>
					<th>Commentaire</th>
				</tr>
			</thead>
			<tbody>";
	for($loop=0;$loop<count($tab_ele);$loop++) {
			echo "
				<tr>
					<td rowspan='$rowspan'><a href='../eleves/visu_eleve.php?ele_login=".$tab_ele[$loop]['login']."' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/ele_onglets.png' class='icone16' alt='Onglets' /></td>
					<td rowspan='$rowspan'><a href='../eleves/modify_eleve.php?eleve_login=".$tab_ele[$loop]['login']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">".$tab_ele[$loop]["nom"]." ".$tab_ele[$loop]["prenom"]."</td>";
		$cpt_ac=0;
		foreach($tab_modalite_accompagnement["code"] as $code => $libelle) {
			if($cpt_ac>0) {
				echo "
				<tr>";
			}

			$checked="";
			$style='';
			$textarea="";
			if(isset($tab_modalites_ele[$tab_ele[$loop]['id_eleve']][$code])) {
				$checked=" checked";
				$style=" style='font-weight:bold'";
				$textarea=$tab_modalites_ele[$tab_ele[$loop]['id_eleve']][$code];
			}
			echo "
					<td><input type='checkbox' name='accompagnement_".$tab_ele[$loop]['id_eleve']."_".$code."' id='accompagnement_".$tab_ele[$loop]['id_eleve']."_".$cpt_ac."' value='$code' onchange='changement(); checkbox_change(this.id);' ".$checked."/></td>
					<td><label for='accompagnement_".$tab_ele[$loop]['id_eleve']."_".$cpt_ac."' id='texte_accompagnement_".$tab_ele[$loop]['id_eleve']."_".$cpt_ac."'".$style.">$code</label></td>
					<td><label for='accompagnement_".$tab_ele[$loop]['id_eleve']."_".$cpt_ac."'>$libelle</label></td>
					<td>
						<!--textarea name='textarea_".$tab_ele[$loop]['id_eleve']."_".$code."'>".$textarea."</textarea-->
						<textarea name='no_anti_inject_textarea_".$tab_ele[$loop]['id_eleve']."_".$code."'>".$textarea."</textarea>
					</td>
				</tr>";
			$cpt_ac++;
		}
	}
	echo "
			</tbody>
		</table>
		<p><input type='submit' value='Enregistrer' /></p>
	</fieldset>
</form>";

	echo js_checkbox_change_style('checkbox_change', 'texte_', "y");
}
else {
	/*
	echo "<pre>";
	print_r($tab_modalite_accompagnement);
	echo "</pre>";
	*/

	echo "<h2>Modalités d'accompagnement en ".get_nom_classe($id_classe)."</h2>";

	$tab_ele=array();
	$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='".$id_classe."' ORDER BY e.nom, e.prenom;";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_assoc($res)) {
		$tab_ele[]=$lig;
	}

	$tab_modalites_ele=array();
	$sql="SELECT DISTINCT jmae.* FROM j_modalite_accompagnement_eleve jmae, 
				eleves e, 
				j_eleves_classes jec 
			WHERE jmae.id_eleve=e.id_eleve AND 
				jec.login=e.login AND 
				jec.id_classe='".$id_classe."' 
			ORDER BY e.nom, e.prenom;";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tab_modalites_ele[$lig->id_eleve][$lig->code]=$lig->commentaire;
	}

	$rowspan=count($tab_modalite_accompagnement["indice"]);

	echo "
<form action='".$_SERVER["PHP_SELF"]."' method='post' name='form2'>
	<fieldset class='fieldset_opacite50'>
		<p><input type='submit' value='Enregistrer' /></p>
		".add_token_field()."
		<input type='hidden' name='id_classe' value='$id_classe' />
		<input type='hidden' name='is_posted_modalites_classes' value='y' />
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th colspan='2'>Élève</th>
					<th colspan='3'>Accompagnement</th>
					<th>Commentaire</th>
				</tr>
			</thead>
			<tbody>";
	for($loop=0;$loop<count($tab_ele);$loop++) {
			echo "
				<tr>
					<td rowspan='$rowspan'><a href='../eleves/visu_eleve.php?ele_login=".$tab_ele[$loop]['login']."' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/ele_onglets.png' class='icone16' alt='Onglets' /></td>
					<td rowspan='$rowspan'><a href='../eleves/modify_eleve.php?eleve_login=".$tab_ele[$loop]['login']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">".$tab_ele[$loop]["nom"]." ".$tab_ele[$loop]["prenom"]."</td>";
		$cpt_ac=0;
		foreach($tab_modalite_accompagnement["code"] as $code => $libelle) {
			if($cpt_ac>0) {
				echo "
				<tr>";
			}

			$checked="";
			$style='';
			$textarea="";
			if(isset($tab_modalites_ele[$tab_ele[$loop]['id_eleve']][$code])) {
				$checked=" checked";
				$style=" style='font-weight:bold'";
				$textarea=$tab_modalites_ele[$tab_ele[$loop]['id_eleve']][$code];
			}
			echo "
					<td><input type='checkbox' name='accompagnement_".$tab_ele[$loop]['id_eleve']."_".$code."' id='accompagnement_".$tab_ele[$loop]['id_eleve']."_".$cpt_ac."' value='$code' onchange='changement(); checkbox_change(this.id);' ".$checked."/></td>
					<td><label for='accompagnement_".$tab_ele[$loop]['id_eleve']."_".$cpt_ac."' id='texte_accompagnement_".$tab_ele[$loop]['id_eleve']."_".$cpt_ac."'".$style.">$code</label></td>
					<td><label for='accompagnement_".$tab_ele[$loop]['id_eleve']."_".$cpt_ac."'>$libelle</label></td>
					<td>
						<!--textarea name='textarea_".$tab_ele[$loop]['id_eleve']."_".$code."'>".$textarea."</textarea-->
						<textarea name='no_anti_inject_textarea_".$tab_ele[$loop]['id_eleve']."_".$code."'>".$textarea."</textarea>
					</td>
				</tr>";
			$cpt_ac++;
		}
	}
	echo "
			</tbody>
		</table>
		<p><input type='submit' value='Enregistrer' /></p>
	</fieldset>
</form>";

	echo js_checkbox_change_style('checkbox_change', 'texte_', "y");
}



require("../lib/footer.inc.php");

?>
