<?php
/*
* Copyright 2001, 2021 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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



$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/attribuer_copies.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/attribuer_copies.php',
administrateur='V',
professeur='V',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: Attribuer les copies aux professeurs',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

$sql="UPDATE droits SET professeur='V' WHERE id='/mod_epreuve_blanche/attribuer_copies.php';";
$maj=mysqli_query($GLOBALS["mysqli"], $sql);

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$id_epreuve=isset($_POST['id_epreuve']) ? $_POST['id_epreuve'] : (isset($_GET['id_epreuve']) ? $_GET['id_epreuve'] : NULL);

$definition_salles=isset($_POST['definition_salles']) ? $_POST['definition_salles'] : (isset($_GET['definition_salles']) ? $_GET['definition_salles'] : NULL);

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

// 20210407
if((!isset($id_epreuve))||(!preg_match("/^[0-9]{1,}$/", $id_epreuve))) {
	header("Location: ../accueil.php?msg=".rawurlencode("Numéro d'épreuve invalide."));
	die();
}

if((($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite'))&&(isset($_POST['valider_param_choix_numeros']))) {
	check_token();

	$autoriser_choix_numeros=isset($_POST['autoriser_choix_numeros']) ? '1' : '0';

	$sql="SELECT * FROM eb_param WHERE type='autoriser_choix_numeros' AND nom='".$id_epreuve."';";
	$test=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($test)==0) {
		$sql="INSERT INTO eb_param SET type='autoriser_choix_numeros', nom='".$id_epreuve."', valeur='".$autoriser_choix_numeros."';";
		$reg=mysqli_query($mysqli, $sql);
	}
	else {
		$sql="UPDATE eb_param SET valeur='".$autoriser_choix_numeros."' WHERE type='autoriser_choix_numeros' AND nom='".$id_epreuve."';";
		$reg=mysqli_query($mysqli, $sql);
	}
	if(!$reg) {
		$msg="Erreur lors de l'enregistrement du paramètre autorisant ou non les professeurs à sélectionner leurs numéros de copies dans la liste.<br />";
	}
	else {
		$msg="Enregistrement du paramètre autorisant ou non les professeurs à sélectionner leurs numéros de copies dans la liste effectué.<br />";
	}
}

if($_SESSION['statut']=='professeur') {
	$sql="SELECT * FROM eb_profs WHERE id_epreuve='".$id_epreuve."' AND login_prof='".$_SESSION['login']."';";
	//echo "$sql<br />";
	$test=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($test)==0) {
		header("Location: ../accueil.php?msg=".rawurlencode("Vous n'êtes pas associé à l'épreuve n°".$id_epreuve."."));
		die();
	}
}

$autoriser_choix_numeros=false;
$sql="SELECT * FROM eb_param WHERE type='autoriser_choix_numeros' AND nom='".$id_epreuve."';";
//echo "$sql<br />";
$test=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($test)>0) {
	$lig=mysqli_fetch_object($test);
	/*
	echo "<pre>";
	print_r($lig);
	echo "</pre>";
	*/
	if($lig->valeur==1) {
		$autoriser_choix_numeros=true;
	}
}
//echo "\$autoriser_choix_numeros=$autoriser_choix_numeros<br />";
if(($_SESSION['statut']=='professeur')&&(!$autoriser_choix_numeros)) {
	//header("Location: ../accueil.php?msg=".rawurlencode("Accès non autorisé."));
	die();
}

if(($_SESSION['statut']=='professeur')&&(isset($_POST['valider_choix_copies_prof']))) {
	check_token();

	$copie=isset($_POST['copie']) ? $_POST['copie'] : array();
	$nb_copies=0;
	$nb_desattribution=0;

	$msg='';

	$tab_deja_a_moi=array();
	$tab_deja_attribuee=array();
	$sql="SELECT ec.login_prof, ec.n_anonymat FROM eb_copies ec WHERE ec.id_epreuve='$id_epreuve' ORDER BY ec.n_anonymat;";
	//echo "$sql<br />";
	$res2=mysqli_query($GLOBALS["mysqli"], $sql);
	$cpt=0;
	while($lig2=mysqli_fetch_object($res2)) {
		if($lig2->login_prof==$_SESSION['login']) {
			$tab_deja_a_moi[$lig2->n_anonymat]=$lig2->login_prof;
		}
		elseif($lig2->login_prof!='') {
			$tab_deja_attribuee[$lig2->n_anonymat]=$lig2->login_prof;
		}
	}


	foreach($copie as $key => $value) {

		$tmp_tab=explode("|", $value);
		$n_anonymat=$tmp_tab[0];

		if($tmp_tab[1]=='y') {
			if((!isset($tab_deja_a_moi[$n_anonymat]))&&(!isset($tab_deja_attribuee[$n_anonymat]))) {
				$sql="UPDATE eb_copies SET login_prof='".$_SESSION['login']."' WHERE id_epreuve='".$id_epreuve."' AND n_anonymat='".$n_anonymat."';";
				$update=mysqli_query($mysqli, $sql);
				if(!$update) {
					$msg.="Erreur lors de l'attribution de la copie n°".$n_anonymat."<br />";
				}
				else {
					$nb_copies++;
				}
			}
		}
		else {
			if(isset($tab_deja_a_moi[$n_anonymat])) {
				$sql="UPDATE eb_copies SET login_prof='' WHERE id_epreuve='".$id_epreuve."' AND n_anonymat='".$n_anonymat."';";
				$update=mysqli_query($mysqli, $sql);
				if(!$update) {
					$msg.="Erreur lors de la désattribution de la copie n°".$n_anonymat."<br />";
				}
				else {
					$nb_desattribution++;
				}
			}
		}
	}

	if($nb_copies>0) {
		$msg.=$nb_copies." copies prises en charge.<br />";
	}
	if($nb_desattribution>0) {
		$msg.=$nb_desattribution." copies désattribuées.<br />";
	}
}

if((($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite'))&&(isset($_POST['valide_affect_eleves']))) {
	check_token();

	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		$msg="L'épreuve choisie (<i>$id_epreuve</i>) n'existe pas.\n";
	}
	else {
		$lig=mysqli_fetch_object($res);
		$etat=$lig->etat;
	
		if($etat!='clos') {

			$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : (isset($_GET['login_ele']) ? $_GET['login_ele'] : array());
			$id_prof_ele=isset($_POST['id_prof_ele']) ? $_POST['id_prof_ele'] : (isset($_GET['id_prof_ele']) ? $_GET['id_prof_ele'] : array());
		
			$msg="";
			for($i=0;$i<count($login_ele);$i++) {
				$sql="UPDATE eb_copies SET login_prof='$id_prof_ele[$i]' WHERE id_epreuve='$id_epreuve' AND login_ele='$login_ele[$i]'";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$update) {$msg.="Erreur lors de l'attribution de la copie de '$login_ele[$i]' à '$login_prof[$i]'.<br />";}
			}
			if((count($login_ele)>0)&&($msg=="")) {$msg="Attribution des copies enregistrée.";}
		}
		else {
			$msg="L'épreuve choisie (<i>$id_epreuve</i>) est close.\n";
		}
	}
}

include('lib_eb.php');

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Epreuve blanche: Attribution des copies";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

//echo "<div class='noprint'>\n";
//echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
echo "<p class='bold'>
	<a href='index.php?id_epreuve=$id_epreuve&amp;mode=modif_epreuve'
			 onclick=\"return confirm_abandon (this, change, '$themessage')\"
			 ><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
	 | 
	<a href='".$_SERVER["PHP_SELF"]."?id_epreuve=$id_epreuve&amp;mode=modif_epreuve'
			 onclick=\"return confirm_abandon (this, change, '$themessage')\"
			 >Rafraichir sans prendre en compte les modifications <img src='../images/icons/actualiser.png' alt='Retour' class='icone16'/></a>";
if($_SESSION['statut']=='professeur') {
	echo "
	 | 
	<a href='saisie_notes.php?id_epreuve=$id_epreuve'
			 onclick=\"return confirm_abandon (this, change, '$themessage')\"
			 >Saisir les notes <img src='../images/edit16.png' alt='Retour' class='icone16'/></a>";
}
echo "
</p>\n";
//echo "</div>\n";

//==================================================================

echo "<p class='bold'>Epreuve n°$id_epreuve</p>\n";
$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>L'épreuve choisie (<i>$id_epreuve</i>) n'existe pas.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

$lig=mysqli_fetch_object($res);
$etat=$lig->etat;

$note_sur=$lig->note_sur;
$mysql_date_epreuve=$lig->date;
$date_epreuve=formate_date($lig->date);

echo "<blockquote>\n";
echo "<p><b>".$lig->intitule."</b> (<i>".$date_epreuve."</i>)<br />\n";
if($lig->description!='') {
	echo nl2br(trim($lig->description))."<br />\n";
}
else {
	echo "Pas de description saisie.<br />\n";
}
echo "</blockquote>\n";


// 20210407
if((($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite'))&&($etat!='clos')) {
	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">
	<fieldset class='fieldset_opacite50' style='margin-bottom:1em;'>
		".add_token_field()."
		<p><strong>Paramètre&nbsp;:</strong><br />
		Dans le cas où vous avez distribué les copies anonymées sans relever à quel professeur vous les avez attribuées, vous pouvez autoriser les professeurs à sélectionner eux-mêmes leur liste de numéros dans cette page.</p>
		<input type='hidden' name='id_epreuve' value='$id_epreuve' />
		<input type='hidden' name='valider_param_choix_numeros' value='y' />
		<p>
			<input type='checkbox' name='autoriser_choix_numeros' id='autoriser_choix_numeros' value='y' onchange=\"checkbox_change(this.id)\" ".($autoriser_choix_numeros ? "checked " : "")."/><label for='autoriser_choix_numeros' id='texte_autoriser_choix_numeros'".($autoriser_choix_numeros ? " style='font-weight:bold' " : "")."> Autoriser les professeurs à sélectionner leurs numéros de copies d'après les copies qu'ils ont effectivement reçues à corriger.</label>
		</p>
		<p>
			<input type='submit' value=\"Valider\" />
		</p>
	</fieldset>
</form>\n";
	echo js_checkbox_change_style('checkbox_change', 'texte_', 'y');
}


$sql="SELECT u.login,u.nom,u.prenom,u.civilite FROM eb_profs ep, utilisateurs u WHERE ep.id_epreuve='$id_epreuve' AND u.login=ep.login_prof ORDER BY u.nom,u.prenom;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>Aucun professeur n'est encore choisi.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

// 20210407
if($_SESSION['statut']=='professeur') {
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>
	".add_token_field()."
	<input type='hidden' name='valider_choix_copies_prof' value='y' />
	<input type='hidden' name='id_epreuve' value='".$id_epreuve."' />

	<p>Liste des professeurs&nbsp;: ";
	$info_prof=array();
	$cpt_prof=0;
	while($lig=mysqli_fetch_object($res)) {
		$info_prof[$lig->login]=$lig->civilite." ".$lig->nom." ".mb_substr($lig->prenom,0,1);
		if($cpt_prof>0) {
			echo ", ";
		}
		echo $info_prof[$lig->login];
		$cpt_prof++;
	}
	echo ".</p>";

	echo "<p style='margin-top:1em'>Sélectionnez vos copies et <input type='submit' value='Validez' />&nbsp;:</p>";
	$sql="SELECT ec.login_prof, ec.n_anonymat FROM eb_copies ec WHERE ec.id_epreuve='$id_epreuve' ORDER BY ec.n_anonymat;";
	//echo "$sql<br />";
	$res2=mysqli_query($GLOBALS["mysqli"], $sql);

	echo "<table class='boireaus boireaus_alt' summary='Choix des copies'>
	<tr>
		<th>Numéro</th>
		<th>\n";
		if($etat!='clos') {
			echo "<a href=\"javascript:coche_colonne_attribuer_copie('y')\" title=\"Sélectionner toutes les copies\">\n";
			echo civ_nom_prenom($_SESSION["login"]);
			echo "</a>\n";
		}
		else {
			echo civ_nom_prenom($_SESSION["login"]);
		}
		echo "
		</th>
		<th>Déjà attribuée</th>
		<th>
			Non attribuée";
		if($etat!='clos') {
			echo "<br /><a href=\"javascript:coche_colonne_attribuer_copie('n')\" title=\"Désélectionner toutes les copies\"><img src='../images/disabled.png' class='icone20' /></a>\n";
		}
		echo "
		</th>
	</tr>";
	$cpt=0;
	while($lig2=mysqli_fetch_object($res2)) {
		$colonne2='';
		$colonne3='';
		$colonne4='';

		if($lig2->login_prof==$_SESSION['login']) {
			if($etat!='clos') {
				$colonne2="<input type='radio' name='copie[$cpt]' id='copie_y_".$cpt."' value='".$lig2->n_anonymat."|y' checked />";
				$colonne4="<input type='radio' name='copie[$cpt]' id='copie_n_".$cpt."' value='".$lig2->n_anonymat."|n' />";
				$cpt++;
			}
			else {
				$colonne2="<img src='../images/enabled.png' class='icone20' />";
			}
		}
		elseif(($lig2->login_prof!='')&&(isset($info_prof[$lig2->login_prof]))) {
			$colonne3=$info_prof[$lig2->login_prof];
		}
		else {
			if($etat!='clos') {
				$colonne2="<input type='radio' name='copie[$cpt]' id='copie_y_".$cpt."' value='".$lig2->n_anonymat."|y' />";
				$colonne4="<input type='radio' name='copie[$cpt]' id='copie_n_".$cpt."' value='".$lig2->n_anonymat."|n' checked />";
				$cpt++;
			}
			else {
				$colonne4="<img src='../images/enabled.png' class='icone20' />";
			}
		}

		echo "
	<tr>
		<td>".$lig2->n_anonymat."</td>
		<td>".$colonne2."</td>
		<td>".$colonne3."</td>
		<td>".$colonne4."</td>
	</tr>";
	}
	echo "
</table>
<p><input type='submit' value='Valider' /></p>
</form>

<script type='text/javascript'>
	function coche_colonne_attribuer_copie(col) {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('copie_'+col+'_'+i)) {
				document.getElementById('copie_'+col+'_'+i).checked=true;
			}
		}
	}
</script>";



	require("../lib/footer.inc.php");
	die();
}

//=======================================
$tmp_verif=array();
$ordre_prof=isset($_POST['ordre_prof']) ? $_POST['ordre_prof'] : NULL;
if(isset($ordre_prof)) {
	for($loop=0;$loop<count($ordre_prof);$loop++) {
		if(in_array($ordre_prof[$loop], $tmp_verif)) {
			echo "<p style='margin-top:1em;margin-bottom:1em;color:red'>L'ordre choisi pour les correcteurs comporte un doublon.<br />Ce choix n'est pas valide.</p>";
			unset($ordre_prof);
			break;
		}
		$tmp_verif[]=$ordre_prof[$loop];
	}
}

// Tableau avant ré-ordonnement
$indice_prof_0=array();
$login_prof_0=array();
$info_prof_0=array();
$cpt_prof=0;
while($lig=mysqli_fetch_object($res)) {
	$login_prof_0[$cpt_prof]=$lig->login;
	$info_prof_0[$cpt_prof]=$lig->civilite." ".$lig->nom." ".mb_substr($lig->prenom,0,1);
	$indice_prof_0[$lig->login]=$cpt_prof;
	$cpt_prof++;
}

$login_prof=array();
$info_prof=array();
if(!isset($ordre_prof)) {
	for($loop=0;$loop<count($login_prof_0);$loop++) {
		$login_prof[$loop]=$login_prof_0[$loop];
		$info_prof[$loop]=$info_prof_0[$loop];
		$ordre_prof[$loop]=$login_prof_0[$loop];
	}
}
else {
	for($loop=0;$loop<count($ordre_prof);$loop++) {
		$tmp_login=$ordre_prof[$loop];
		$indice_tmp=$indice_prof_0[$tmp_login];
		$login_prof[$loop]=$login_prof_0[$indice_tmp];
		// En fait, $login_prof c'est alors $ordre_prof
		$info_prof[$loop]=$info_prof_0[$indice_tmp];
	}
}

$eff_habituel_prof=array();
$js_chaine_effectif_habituel_prof="";
for($loop=0;$loop<count($login_prof);$loop++) {
	$sql="SELECT DISTINCT jeg.login FROM j_eleves_groupes jeg, j_groupes_professeurs jgp, eb_groupes eg, groupes g WHERE id_epreuve='$id_epreuve' AND eg.id_groupe=g.id AND jgp.id_groupe=jeg.id_groupe AND jeg.id_groupe=g.id AND jgp.login='".$login_prof[$loop]."';";
	$res_eff_prof=mysqli_query($GLOBALS["mysqli"], $sql);
	$eff_habituel_prof[$loop]=mysqli_num_rows($res_eff_prof);

	if($js_chaine_effectif_habituel_prof!="") {
		$js_chaine_effectif_habituel_prof.=",";
	}
	$js_chaine_effectif_habituel_prof.=mysqli_num_rows($res_eff_prof);
}
/*
echo "\$login_prof<pre>";
print_r($login_prof);
echo "</pre>";
*/

//$tri=isset($_POST['tri']) ? $_POST['tri'] : (isset($_GET['tri']) ? $_GET['tri'] : "groupe");
$tri=isset($_POST['tri']) ? $_POST['tri'] : (isset($_GET['tri']) ? $_GET['tri'] : "salle");
$pas_de_salle="n";
$sql="SELECT DISTINCT es.* FROM eb_salles es WHERE id_epreuve='$id_epreuve' ORDER BY es.salle;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	$pas_de_salle="y";
}

if(($tri=='salle')&&($pas_de_salle=="y")) {
	$tri='groupe';
}

echo "<p class='bold'>Trier les élèves par&nbsp;:</p>\n";
echo "<ul>\n";
if($pas_de_salle=="y") {
	echo "<li>Aucune salle n'est encore choisie</li>\n";
}
else {
	echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;tri=salle'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">salle</a></li>\n";
}
echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;tri=groupe'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">groupe/enseignement</a></li>\n";
echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;tri=n_anonymat'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">numéro anonymat</a></li>\n";
echo "</ul>\n";

if($etat!='clos') {
	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">
	<fieldset class='fieldset_opacite50' style='margin-bottom:1em;'>
		".add_token_field()."
		<p><strong>Modifier l'ordre des professeurs</strong><br />
		<em>(pour pouvoir utiliser les répartitions automatiques ci-dessous, sans affecter toujours les mêmes élèves aux mêmes correcteurs si vous programmez plusieurs épreuves blanches dans l'année)</em></p>
		<input type='hidden' name='id_epreuve' value='$id_epreuve' />
		<input type='hidden' name='tri' value='$tri' />";

	for($loop=0;$loop<count($login_prof_0);$loop++) {
		echo "<strong>".($loop+1)."-</strong> 
			<select name='ordre_prof[]'>";
		for($loop2=0;$loop2<count($login_prof_0);$loop2++) {
			$selected="";
			if($login_prof_0[$loop2]==$ordre_prof[$loop]) {
				$selected=" selected='true'";
			}
			echo "
				<option value='".$login_prof_0[$loop2]."'".$selected.">".$info_prof_0[$loop2]."</option>";
		}
		echo "
			</select><br />";
	}
	/*
	for($loop=0;$loop<count($login_prof_0);$loop++) {
		echo "<strong>".($loop+1)."-</strong> 
			<select name='ordre_prof[]'>";
		for($loop2=0;$loop2<count($login_prof_0);$loop2++) {

			$selected="";
			if($loop2==$ordre_prof[$loop]) {
				$selected=" selected='true'";
			}
			echo "
				<option value='".$loop2."'".$selected.">".$info_prof_0[$loop2]." $loop $loop2</option>";
		}
		echo "
			</select><br />";
	}
	*/
	echo "
		<input type='submit' value=\"Valider l'ordre choisi pour les correcteurs\" />
	</fieldset>
</form>\n";


	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='tri' value='$tri' />\n";

	if(isset($ordre_prof)) {
		for($loop=0;$loop<count($ordre_prof);$loop++) {
			echo "
		<input type='hidden' name='ordre_prof[]' value=\"".$ordre_prof[$loop]."\" />";
		}
	}
}

//echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves1' value='Valider' /></p>\n";

if($tri=='groupe') {
	$tab_eleves_deja_affiches=array();

	$sql="SELECT DISTINCT g.* FROM eb_groupes eg, groupes g WHERE id_epreuve='$id_epreuve' AND eg.id_groupe=g.id ORDER BY g.name, g.description;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucune groupe n'est encore associé à l'épreuve.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$tab_cpt_eleve=array();
	$tab_groupes=array();
	$cpt=0;
	$compteur_groupe=-1;
	if($etat!='clos') {

		echo "<p style='margin-top:1em;margin-bottom:1em;'><a href='javascript:repartir_automatiquement_entre_les_profs(1)'>Répartir les copies entre les ".count($login_prof)." professeur(s) en nombre égal pour tous</a>.<br />
<a href='javascript:repartir_automatiquement_entre_les_profs(2)'>Attribuer à chaque professeur un nombre de copies égal au nombre d'élèves qu'il a en classe</a>.</p>";

		echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves$cpt' value='Valider' /></p>\n";
	}

	while($lig=mysqli_fetch_object($res)) {
		$tab_cpt_eleve[]=$cpt;

		$compteur_groupe++;

		$tab_groupes[]=$lig->id;
	
		$current_group=get_group($lig->id);

		echo "<p>"."<b>".$current_group['classlist_string']."</b> ".htmlspecialchars($lig->name)." (<i>".htmlspecialchars($lig->description)."</i>) (<i>";
		for($k=0;$k<count($current_group["profs"]["list"]);$k++) {
			if($k>0) {echo ", ";}
			echo get_denomination_prof($current_group["profs"]["list"][$k]);
		}
		echo "</i>)</p>\n";
		echo "<blockquote>\n";
	
		//$sql="SELECT * FROM eb_copies ec, eb_groupes eg WHERE id_epreuve='$id_epreuve' AND...;";
	
		$sql="SELECT ec.login_ele,ec.login_prof, ec.n_anonymat FROM eb_copies ec, eb_groupes eg WHERE eg.id_epreuve='$id_epreuve' AND ec.id_epreuve=eg.id_epreuve AND eg.id_groupe='$lig->id';";
		//echo "$sql<br />";
		$res2=mysqli_query($GLOBALS["mysqli"], $sql);

		$tab_ele_prof=array();
		$tab_ele_anonymat=array();
		while($lig2=mysqli_fetch_object($res2)) {
			$tab_ele_prof[$lig2->login_ele]=$lig2->login_prof;
			$tab_ele_anonymat[$lig2->login_ele]=$lig2->n_anonymat;
		}

		echo "<table class='boireaus' summary='Choix des élèves du groupe $lig->id'>\n";
		echo "<tr>\n";
		echo "<th>Numéro</th>\n";
		echo "<th>Elèves</th>\n";
		echo "<th>Classes</th>\n";
		for($i=0;$i<count($info_prof);$i++) {
			echo "<th>\n";
			if($etat!='clos') {
				echo "<a href='javascript:coche($i,$compteur_groupe,true)' title=\"Affecter les copies de cette salle à ce professeur\">\n";
				echo "$info_prof[$i]\n";
				echo "</a>\n";
			}
			else {
				echo "$info_prof[$i]\n";
			}
			//echo "<input type='hidden' name='salle[$i]' value='$salle[$i]' />\n";
			// A FAIRE: Afficher effectif
			// style='color:red;'
			//echo "<br />(<span id='eff_prof_".$lig->id."_$i'>Effectif</span>)";

			echo "<br />\n";
			// coche(colonne,rang_groupe,mode)
			//echo "<a href='javascript:coche($i,$compteur_groupe,true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
			//echo "<a href='javascript:coche($i,$compteur_groupe,false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";

			echo "</th>\n";
		}
		echo "<th>\n";
		if($etat!='clos') {
			echo "<a href='javascript:coche($i,$compteur_groupe,true)'>\n";
			echo "Non affecté";
			echo "</a>\n";
		}
		else {
			echo "Non affecté";
		}
		echo "<br />\n";
		// coche(colonne,rang_groupe,mode)
		//echo "<a href='javascript:coche($i,$compteur_groupe,true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
		//echo "<a href='javascript:coche($i,$compteur_groupe,false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
		echo "</th>\n";
		echo "</tr>\n";


		if($etat!='clos') {
			echo "<tr>\n";
			echo "<th>Effectifs</th>\n";
			echo "<th>&nbsp;</th>\n";
			echo "<th>&nbsp;</th>\n";
			for($i=0;$i<count($info_prof);$i++) {
				echo "<th title=\"Nombre de copies attribuées à ce professeur par rapport au nombre d'élèves qu'il a en cours.\">\n";
				echo "<span id='eff_prof_".$lig->id."_$i'>Effectif</span>";
				echo "/".$eff_habituel_prof[$i]."\n";
				echo "</th>\n";
			}
			echo "<th>\n";
			//$i++;
			echo "<span id='eff_prof_".$lig->id."_$i'>Effectif</span>";
			echo "</th>\n";
			echo "</tr>\n";
		}
	
		$alt=1;
		for($j=0;$j<count($current_group["eleves"]["all"]["list"]);$j++) {
			if(!in_array($current_group["eleves"]["all"]["list"][$j],$tab_eleves_deja_affiches)) {
				$login_ele=$current_group["eleves"]["all"]["list"][$j];

				$tab_eleves_deja_affiches[]=$login_ele;

				$alt=$alt*(-1);
				if((isset($current_group["eleves"]["all"]["users"][$login_ele]['date_sortie']))&&(!is_null($current_group["eleves"]["all"]["users"][$login_ele]['date_sortie']))&&($current_group["eleves"]["all"]["users"][$login_ele]['date_sortie']!='0000-00-00 00:00:00')&&($current_group["eleves"]["all"]["users"][$login_ele]['date_sortie']<$mysql_date_epreuve)) {
					echo "<tr class='white_hover' style='background-color:grey' title=\"Élève sorti de l'établissement (".formate_date($current_group["eleves"]["all"]["users"][$login_ele]['date_sortie']).") avant l'épreuve ($date_epreuve).\">\n";
				}
				elseif((isset($current_group["eleves"]["all"]["users"][$login_ele]['date_entree']))&&(!is_null($current_group["eleves"]["all"]["users"][$login_ele]['date_entree']))&&($current_group["eleves"]["all"]["users"][$login_ele]['date_entree']!='0000-00-00 00:00:00')&&($current_group["eleves"]["all"]["users"][$login_ele]['date_entree']>$mysql_date_epreuve)) {
					echo "<tr class='white_hover' style='background-color:grey' title=\"Élève entré dans l'établissement (".formate_date($current_group["eleves"]["all"]["users"][$login_ele]['date_entree']).") après l'épreuve ($date_epreuve).\">\n";
				}
				else {
					echo "<tr class='lig$alt white_hover'>\n";
				}

				echo "<td>\n";
				$numero_anonymat='';
				if(isset($tab_ele_anonymat[$login_ele])) {
					$numero_anonymat=$tab_ele_anonymat[$login_ele];
				}
				echo $numero_anonymat;
				echo "</td>\n";

				echo "<td style='text-align:left;'>\n";
				echo "<input type='hidden' name='login_ele[$cpt]' value='$login_ele' />\n";
				echo "<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."' title=\"Voir la fiche élève dans un nouvel onglet.\" target='_blank'>".get_nom_prenom_eleve($login_ele)."</a>";
				echo "</td>\n";
	
				echo "<td>\n";
				$tmp_tab_classe=get_class_from_ele_login($login_ele);
				echo $tmp_tab_classe['liste'];
				echo "</td>\n";
	
				$affect="n";
				for($i=0;$i<count($info_prof);$i++) {
					echo "<td>\n";
					if($etat=='clos') {
						if((isset($tab_ele_prof[$login_ele]))&&($tab_ele_prof[$login_ele]==$login_prof[$i])) {echo "X";$affect="y";}
					}
					else {
						echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='$login_prof[$i]' ";
						echo "onchange='calcule_effectif();changement();' ";
						echo "title=\"Attribuer la copie n°".$numero_anonymat." à ".$info_prof[$i]."\" ";
						// On risque une blague si pour une raison ou une autre, on n'a pas une copie dans eb_copies pour tous les élèves du groupe (toutes périodes confondues)... à améliorer
						if((isset($tab_ele_prof[$login_ele]))&&($tab_ele_prof[$login_ele]==$login_prof[$i])) {echo "checked ";$affect="y";}
						echo "/>\n";
					}
					echo "</td>\n";
				}
				echo "<td>\n";
				if($etat=='clos') {
					if($affect=="n") {
						echo "X";
					}
				}
				else {
					echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='' ";
					echo "title=\"Supprimer l'attribution de la copie n°".$numero_anonymat." à tel ou tel professeur.\" ";
					echo "onchange='calcule_effectif();changement();' ";
					if($affect=="n") {echo "checked ";}
					echo "/>\n";
				}
				echo "</td>\n";
				echo "</tr>\n";
				$cpt++;
			}
		}
		echo "</table>\n";
		//$tab_cpt_eleve[]=$cpt;

		echo "</blockquote>\n";

		if($etat!='clos') {
			echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves$cpt' value='Valider' /></p>\n";
		}
	}

	if($etat!='clos') {
		echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
		echo "<input type='hidden' name='mode' value='affect_eleves' />\n";
		echo "<input type='hidden' name='valide_affect_eleves' value='y' />\n";
		//echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves2' value='Valider' /></p>\n";
		echo "	</fieldset>
</form>\n";
		
	
		$chaine_groupes="";
		for($i=0;$i<count($tab_groupes);$i++) {
			if($i>0) {$chaine_groupes.=",";}
			$chaine_groupes.="'$tab_groupes[$i]'";
		}
	
		/*
		$chaine_cpt0_eleves="";
		$chaine_cpt1_eleves="";
		for($i=0;$i<count($tab_cpt_eleve);$i++) {
			if($i>0) {$chaine_cpt0_eleves.=",";$chaine_cpt1_eleves.=",";}
			$j=2*$i;
			$chaine_cpt0_eleves.="'$tab_cpt_eleve[$j]'";
			$j=2*$i+1;
			$chaine_cpt1_eleves.="'$tab_cpt_eleve[$j]'";
		}
		*/

		/*
		echo "<pre>";
		print_r($tab_cpt_eleve);
		echo "</pre>";
		*/

		$chaine_cpt0_eleves="";
		$chaine_cpt1_eleves="";
		for($i=0;$i<count($tab_cpt_eleve);$i++) {
			//if($i>1) {$chaine_cpt1_eleves.=",";}
			if($chaine_cpt1_eleves!="") {$chaine_cpt1_eleves.=",";}
			if($i>0) {
				$chaine_cpt0_eleves.=",";
				$chaine_cpt1_eleves.="'$tab_cpt_eleve[$i]'";
			}
			$chaine_cpt0_eleves.="'$tab_cpt_eleve[$i]'";
		}
		if($chaine_cpt1_eleves!="") {
			$chaine_cpt1_eleves.=",";
		}
		$chaine_cpt1_eleves.="'$cpt'";

		echo "<script type='text/javascript'>

function calcule_effectif() {
	var tab_groupes=new Array($chaine_groupes);
	var eff;

	for(i=0;i<".count($login_prof)."+1;i++) {
		eff=0;

		for(j=0;j<$cpt;j++) {
			if(document.getElementById('id_prof_ele_'+i+'_'+j)) {
				if(document.getElementById('id_prof_ele_'+i+'_'+j).checked) {
					eff++;
				}
			}
		}

		//alert('Salle i='+i+' eff='+eff)
		for(j=0;j<tab_groupes.length;j++) {
			if(document.getElementById('eff_prof_'+tab_groupes[j]+'_'+i)) {
				document.getElementById('eff_prof_'+tab_groupes[j]+'_'+i).innerHTML=eff;
				//alert('eff_prof_'+tab_groupes[j]+'_'+i+' eff='+eff);
			}
		}
	}
}

calcule_effectif();

function coche(colonne,rang_groupe,mode) {
	var tab_cpt0_ele=new Array($chaine_cpt0_eleves);
	var tab_cpt1_ele=new Array($chaine_cpt1_eleves);

	//for(k=tab_cpt0_ele[rang_groupe];k<tab_cpt1_ele[rang_groupe];k++) {
	for(k=eval(tab_cpt0_ele[rang_groupe]);k<eval(tab_cpt1_ele[rang_groupe]);k++) {
		if(document.getElementById('id_prof_ele_'+colonne+'_'+k)) {
			document.getElementById('id_prof_ele_'+colonne+'_'+k).checked=mode;
		}
	}

	calcule_effectif();

	changement();
}

function repartir_automatiquement_entre_les_profs(mode) {
	if(mode==1) {
		var effectif_par_prof=Math.ceil($cpt/".count($login_prof).");

		for(j=0;j<$cpt;j++) {
			indice_prof=Math.floor(j/effectif_par_prof);
			if(document.getElementById('id_prof_ele_'+indice_prof+'_'+j)) {
				document.getElementById('id_prof_ele_'+indice_prof+'_'+j).checked=true;
			}
		}
	}
	else {
		var tab_eff_prof=new Array($js_chaine_effectif_habituel_prof);

		indice_prof=0;
		limite=tab_eff_prof[indice_prof];
		for(j=0;j<$cpt;j++) {
			if(j>=limite) {
				indice_prof++;
				limite+=tab_eff_prof[indice_prof];
			}
			if(document.getElementById('id_prof_ele_'+indice_prof+'_'+j)) {
				document.getElementById('id_prof_ele_'+indice_prof+'_'+j).checked=true;
			}
		}

	}

	calcule_effectif();
	changement();
}

</script>\n";
	}
}
elseif($tri=='n_anonymat') {

	$tab_ele_prof_habituel=array();
	for($i=0;$i<count($login_prof);$i++) {
	
		$sql="SELECT DISTINCT jeg.login FROM j_eleves_groupes jeg, j_groupes_professeurs jgp, eb_groupes eg, groupes g WHERE id_epreuve='$id_epreuve' AND eg.id_groupe=g.id AND jgp.id_groupe=jeg.id_groupe AND jeg.id_groupe=g.id AND jgp.login='".$login_prof[$i]."';";
		$res_ele_prof=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele_prof)>0) {
			while($lig=mysqli_fetch_object($res_ele_prof)) {
				$tab_ele_prof_habituel[$lig->login]=$login_prof[$i];
			}
		}
	}

	$sql="SELECT ec.*, e.nom, e.prenom, e.date_sortie, e.date_entree FROM eb_copies ec,eleves e WHERE ec.id_epreuve='$id_epreuve' AND ec.login_ele=e.login ORDER BY ec.n_anonymat;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucun élève n'est encore associé à l'épreuve.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$cpt=0;
	$tab_eleves=array();
	while($lig=mysqli_fetch_object($res)) {
		$tab_eleves[$cpt]['login_prof']=$lig->login_prof;
		$tab_eleves[$cpt]['login_ele']=$lig->login_ele;
		$tab_eleves[$cpt]['nom']=$lig->nom;
		$tab_eleves[$cpt]['prenom']=$lig->prenom;
		$tab_eleves[$cpt]['date_entree']=$lig->date_entree;
		$tab_eleves[$cpt]['date_sortie']=$lig->date_sortie;
		$tab_eleves[$cpt]['n_anonymat']=$lig->n_anonymat;

		$tab_eleves[$cpt]['note']=$lig->note;
		$tab_eleves[$cpt]['statut']=$lig->statut;

		$tab_eleves[$cpt]['note_ou_statut']="";
		if($lig->statut!="v") {
			if($lig->statut!="") {
				$tab_eleves[$cpt]['note_ou_statut']=$lig->statut;
			}
			else {
				$tab_eleves[$cpt]['note_ou_statut']=$lig->note."/".$note_sur;
			}
		}

		$cpt++;
	}

	$largeur_tranche=10;
	$nb_tranches=ceil(count($tab_eleves)/$largeur_tranche);

	$cpt_tranche=0;
	$compteur_eleves_du_prof=array();
	$cpt=0;
	$compteur_tranche=0;

	
	if($etat!='clos') {

		echo "<p style='margin-top:1em;margin-bottom:1em;'><a href='javascript:repartir_automatiquement_entre_les_profs(1)'>Répartir les copies entre les ".count($login_prof)." professeur(s) en nombre égal pour tous</a>.<br />
<a href='javascript:repartir_automatiquement_entre_les_profs(2)'>Attribuer à chaque professeur un nombre de copies égal au nombre d'élèves qu'il a en classe</a>.</p>";

		echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves$cpt_tranche' value='Valider' /></p>\n";
	}

	for($loop=0;$loop<count($tab_eleves);$loop++) {

		if(($loop==0)||($loop%$largeur_tranche==0)) {
			if($loop>0) {
				echo "</blockquote>\n";
			}

			$tab_cpt_eleve[]=$loop;

			$cpt_tranche++;
			$compteur_tranche++;

			$compteur_eleves_dans_la_tranche=1;

			echo "<p class='bold' style='margin-top:1em;'>Tranche $cpt_tranche/$nb_tranches&nbsp;:</p>\n";
			echo "<blockquote>\n";
			echo "<table class='boireaus boireaus_alt' summary='Elèves de la tranche $cpt_tranche'>\n";
			echo "<tr>\n";
			echo "<th title=\"Numéro anonymat\">Numéro</th>\n";
			echo "<th>Elèves</th>\n";
			echo "<th>Classes</th>\n";
			echo "<th title=\"La copie est-elle corrigée ou non\">État</th>\n";
			for($i=0;$i<count($info_prof);$i++) {
				$compteur_eleves_du_prof[$i]=0;
				echo "<th>\n";
				if($etat!='clos') {
					echo "<a href='javascript:coche($i,$compteur_tranche,true)' title=\"Affecter les copies de cette tranche à ce professeur\">\n";
					echo "$info_prof[$i]\n";
					echo "</a>\n";
				}
				else {
					echo "$info_prof[$i]\n";
				}
				echo "</th>\n";
			}
			echo "<th>\n";
			if($etat!='clos') {
				echo "<a href='javascript:coche($i,$compteur_tranche,true)'>\n";
				echo "Non affecté";
				echo "</a>\n";
			}
			else {
				echo "Non affecté";
			}
			echo "</th>\n";
			echo "</tr>\n";
	
			if($etat!='clos') {
				echo "<tr>\n";
				echo "<th></th>\n";
				echo "<th></th>\n";
				echo "<th>Effectifs</th>\n";
				echo "<th>&nbsp;</th>\n";
				for($i=0;$i<count($info_prof);$i++) {
					echo "<th title=\"Nombre de copies attribuées à ce professeur par rapport au nombre d'élèves qu'il a en cours.\">\n";
					//echo "<span id='eff_prof_".$lig->id."_$i'>Effectif</span>";
					echo "<span id='eff_prof_".$compteur_tranche."_$i'>Effectif</span>";
					echo "/".$eff_habituel_prof[$i]."\n";
					echo "</th>\n";
				}
				echo "<th>\n";
				//$i++;
				echo "<span id='eff_prof_".$compteur_tranche."_$i'>Effectif</span>";
				echo "</th>\n";
				echo "</tr>\n";
			}
		}

		if((isset($tab_eleves[$loop]['date_sortie']))&&(!is_null($tab_eleves[$loop]['date_sortie']))&&($tab_eleves[$loop]['date_sortie']!='0000-00-00 00:00:00')&&($tab_eleves[$loop]['date_sortie']<$mysql_date_epreuve)) {
			echo "<tr class='white_hover' style='background-color:grey' title=\"Élève sorti de l'établissement (".formate_date($tab_eleves[$loop]['date_sortie']).") avant l'épreuve ($date_epreuve).\">\n";
		}
		elseif((isset($tab_eleves[$loop]['date_entree']))&&(!is_null($tab_eleves[$loop]['date_entree']))&&($tab_eleves[$loop]['date_entree']!='0000-00-00 00:00:00')&&($tab_eleves[$loop]['date_entree']>$mysql_date_epreuve)) {
			echo "<tr class='white_hover' style='background-color:grey' title=\"Élève entré dans l'établissement (".formate_date($tab_eleves[$loop]['date_entree']).") après l'épreuve ($date_epreuve).\">\n";
		}
		else {
			echo "<tr class='white_hover'>\n";
		}
		echo "<td>";
		//echo $loop." "; //DEBUG
		$numero_anonymat='';
		if(isset($tab_eleves[$loop]['n_anonymat'])) {
			$numero_anonymat=$tab_eleves[$loop]['n_anonymat'];
		}
		echo $numero_anonymat;
		echo "</td>\n";

		echo "<td style='text-align:left;'>\n";
		$login_ele=$tab_eleves[$loop]['login_ele'];
		echo "<input type='hidden' name='login_ele[$cpt]' value='$login_ele' />\n";
		//echo get_nom_prenom_eleve($login_ele);
		echo "<a href='../eleves/visu_eleve.php?ele_login=".$tab_eleves[$loop]['login_ele']."' title=\"Voir la fiche élève dans un nouvel onglet.\" target='_blank'>".casse_mot($tab_eleves[$loop]['nom'])." ".casse_mot($tab_eleves[$loop]['prenom'],'majf2')."</a>";
		echo "</td>\n";

		echo "<td>\n";
		$tmp_tab_classe=get_class_from_ele_login($login_ele);
		echo $tmp_tab_classe['liste'];
		echo "</td>\n";

		if($tab_eleves[$loop]['statut']=="v") {
			echo "<td title=\"La copie n'est pas encore corrigée.\">\n";
			echo "</td>\n";
		}
		else {
			echo "<td title=\"La copie est corrigée : ".$tab_eleves[$loop]['note_ou_statut']."\">\n";
			echo "<img src='../images/edit16b.png' class='icone16' />\n";
			echo "</td>\n";
		}

		$affect="n";
		for($i=0;$i<count($info_prof);$i++) {
			echo "<td>\n";

			if((isset($tab_ele_prof_habituel[$login_ele]))&&($tab_ele_prof_habituel[$login_ele]==$login_prof[$i])) {
				echo "<div style='float:right; width:17px;'><img src='../images/icons/flag.png' width='17' height='18' title='Professeur habituel de cet élève' alt='Professeur habituel de cet élève' /></div>\n";
				$compteur_eleves_du_prof[$i]++;
			}

			if($etat!='clos') {
				echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='$login_prof[$i]' ";
				echo "title=\"Attribuer la copie n°".$numero_anonymat." à ".$info_prof[$i]."\" ";
				echo "onchange='calcule_effectif();changement();' ";
				// On risque une blague si pour une raison ou une autre, on n'a pas une copie dans eb_copies pour tous les élèves du groupe (toutes périodes confondues)... à améliorer
				if($tab_eleves[$loop]['login_prof']==$login_prof[$i]) {echo "checked ";$affect="y";}
				echo "/>\n";
			}
			else {
				if($tab_eleves[$loop]['login_prof']==$login_prof[$i]) {echo "X";$affect="y";}
			}

			echo "</td>\n";
		}
		echo "<td>\n";
		if($etat!='clos') {
			echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='' ";
			echo "title=\"Supprimer l'attribution de la copie n°".$numero_anonymat." à tel ou tel professeur.\" ";
			echo "onchange='calcule_effectif();changement();' ";
			if($affect=="n") {echo "checked ";}
			echo "/>\n";
		}
		else {
			if($affect=="n") {echo "X";}
		}
		echo "</td>\n";
		echo "</tr>\n";

		if((($loop>0)&&(($loop+1)%$largeur_tranche==0))||($loop==count($tab_eleves)-1)) {
			echo "<tr>\n";
			echo "<th></th>\n";
			echo "<th></th>\n";
			echo "<th></th>\n";
			echo "<th></th>\n";
			for($i=0;$i<count($info_prof);$i++) {
				echo "<th title=\"Le professeur a ".$compteur_eleves_du_prof[$i]." élève(s) en cours parmi les $compteur_eleves_dans_la_tranche de cette tranche\">".$compteur_eleves_du_prof[$i]."/".$compteur_eleves_dans_la_tranche."</th>\n";
			}
			echo "<th></th>\n";
			echo "</tr>\n";
			echo "</table>\n";
		}

		$cpt++;

		$compteur_eleves_dans_la_tranche++;

	}
	echo "</blockquote>\n";



	if($etat!='clos') {
		echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
		echo "<input type='hidden' name='mode' value='affect_eleves' />\n";
		echo "<input type='hidden' name='valide_affect_eleves' value='y' />\n";
		echo "	</fieldset>
</form>\n";

		/*
		echo "<pre>";
		print_r($tab_cpt_eleve);
		echo "</pre>";
		*/

		$chaine_cpt0_eleves="";
		$chaine_cpt1_eleves="";
		for($i=0;$i<count($tab_cpt_eleve);$i++) {
			//if($i>1) {$chaine_cpt1_eleves.=",";}
			if($chaine_cpt1_eleves!="") {$chaine_cpt1_eleves.=",";}
			if($i>0) {
				$chaine_cpt0_eleves.=",";
				$chaine_cpt1_eleves.="'$tab_cpt_eleve[$i]'";
			}
			$chaine_cpt0_eleves.="'$tab_cpt_eleve[$i]'";
		}
		if($chaine_cpt1_eleves!="") {
			$chaine_cpt1_eleves.=",";
		}
		$chaine_cpt1_eleves.="'$cpt'";

		//echo "\$chaine_cpt0_eleves=$chaine_cpt0_eleves<br />";
		//echo "\$chaine_cpt1_eleves=$chaine_cpt1_eleves<br />";

		echo "<script type='text/javascript'>

function calcule_effectif() {
	var eff;

	for(i=0;i<".count($login_prof)."+1;i++) {
		eff=0;

		for(j=0;j<$cpt;j++) {
			if(document.getElementById('id_prof_ele_'+i+'_'+j)) {
				if(document.getElementById('id_prof_ele_'+i+'_'+j).checked) {
					eff++;
				}
			}
		}

		//alert('Salle i='+i+' eff='+eff)
		for(j=0;j<=$cpt_tranche;j++) {
			if(document.getElementById('eff_prof_'+j+'_'+i)) {
				document.getElementById('eff_prof_'+j+'_'+i).innerHTML=eff;
				//alert('eff_prof_'+j+'_'+i+' eff='+eff);
			}
		}
	}
}

calcule_effectif();

function coche(colonne,rang_groupe,mode) {
	var tab_cpt0_ele=new Array($chaine_cpt0_eleves);
	var tab_cpt1_ele=new Array($chaine_cpt1_eleves);

	//for(k=tab_cpt0_ele[rang_groupe];k<tab_cpt1_ele[rang_groupe];k++) {
	//alert(tab_cpt0_ele[rang_groupe]+' '+tab_cpt1_ele[rang_groupe]);
	//for(k=eval(tab_cpt0_ele[rang_groupe]);k<eval(tab_cpt1_ele[rang_groupe]);k++) {
	for(k=tab_cpt0_ele[rang_groupe-1];k<tab_cpt1_ele[rang_groupe-1];k++) {
		if(document.getElementById('id_prof_ele_'+colonne+'_'+k)) {
			//alert('id_prof_ele_'+colonne+'_'+k);
			document.getElementById('id_prof_ele_'+colonne+'_'+k).checked=mode;
		}
	}

	calcule_effectif();

	changement();
}

function repartir_automatiquement_entre_les_profs(mode) {
	if(mode==1) {
		var effectif_par_prof=Math.ceil($cpt/".count($login_prof).");

		for(j=0;j<$cpt;j++) {
			indice_prof=Math.floor(j/effectif_par_prof);
			if(document.getElementById('id_prof_ele_'+indice_prof+'_'+j)) {
				document.getElementById('id_prof_ele_'+indice_prof+'_'+j).checked=true;
			}
		}
	}
	else {
		var tab_eff_prof=new Array($js_chaine_effectif_habituel_prof);

		indice_prof=0;
		limite=tab_eff_prof[indice_prof];
		for(j=0;j<$cpt;j++) {
			if(j>=limite) {
				indice_prof++;
				limite+=tab_eff_prof[indice_prof];
			}
			if(document.getElementById('id_prof_ele_'+indice_prof+'_'+j)) {
				document.getElementById('id_prof_ele_'+indice_prof+'_'+j).checked=true;
			}
		}

	}

	calcule_effectif();
	changement();
}

</script>\n";
	}
}
elseif($tri=='salle') {

	$tab_ele_prof_habituel=array();
	for($i=0;$i<count($login_prof);$i++) {
	
		$sql="SELECT DISTINCT jeg.login FROM j_eleves_groupes jeg, j_groupes_professeurs jgp, eb_groupes eg, groupes g WHERE id_epreuve='$id_epreuve' AND eg.id_groupe=g.id AND jgp.id_groupe=jeg.id_groupe AND jeg.id_groupe=g.id AND jgp.login='".$login_prof[$i]."';";
		$res_ele_prof=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele_prof)>0) {
			while($lig=mysqli_fetch_object($res_ele_prof)) {
				$tab_ele_prof_habituel[$lig->login]=$login_prof[$i];
			}
		}
	}

	$sql="SELECT DISTINCT es.* FROM eb_salles es WHERE id_epreuve='$id_epreuve' ORDER BY es.salle;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucune salle n'est encore associée à l'épreuve.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	// 20190329
	echo "<div id='div_debug' style='float:right;width:20em;'></div>";

	if($etat!='clos') {
		echo "<p style='margin-top:1em;margin-bottom:1em;'><a href='javascript:repartir_automatiquement_entre_les_profs(1)'>Répartir les copies entre les ".count($login_prof)." professeur(s) en nombre égal pour tous</a>.<br />
<a href='javascript:repartir_automatiquement_entre_les_profs(4)'>Répartir les copies entre les ".count($login_prof)." professeur(s) en nombre égal pour tous, sans modifier les copies déjà attribuées manuellement</a>.<br />

<a href='javascript:repartir_automatiquement_entre_les_profs(2)'>Attribuer à chaque professeur un nombre de copies égal au nombre d'élèves qu'il a en classe</a>.<br />
<a href='javascript:repartir_automatiquement_entre_les_profs(3)'>Attribuer à chaque professeur un nombre de copies égal au nombre d'élèves qu'il a en classe, sans modifier les copies déjà attribuées manuellement</a>.<br />
</p>";
	}

	$tab_cpt_eleve=array();
	$tab_id_salle=array();
	$tab_salle=array();
	$cpt=0;
	$compteur_salle=-1;
	$compteur_eleves_du_prof=array();
	// Boucle sur les salles
	while($lig=mysqli_fetch_object($res)) {
		$tab_cpt_eleve[]=$cpt;

		$compteur_salle++;

		$tab_salle[]=$lig->salle;
		$tab_id_salle[]=$lig->id;

		$sql="select * from eb_copies WHERE id_epreuve='".$id_epreuve."' AND id_salle='".$lig->id."';";
		$res_eff=mysqli_query($GLOBALS["mysqli"], $sql);
		$effectif_salle_courante=mysqli_num_rows($res_eff);

		if($etat!='clos') {

			echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves$cpt' value='Valider' /></p>\n";
		}
		echo "<p>Salle <b>$lig->salle</b>&nbsp;: <span title=\"Effectif dans la salle : ".$effectif_salle_courante."\">(".$effectif_salle_courante." élève(s))</span></p>\n";
		echo "<blockquote>\n";

		//echo "\$cpt=$cpt<br />";
		echo "<table class='boireaus' summary='Elèves de la salle $lig->id'>\n";
		echo "<tr>\n";
		echo "<th>Numéro</th>\n";
		echo "<th>Elèves</th>\n";
		echo "<th>Classes</th>\n";
		for($i=0;$i<count($info_prof);$i++) {
			$compteur_eleves_du_prof[$i]=0;
			echo "<th>\n";
			if($etat!='clos') {
				echo "<a href='javascript:coche($i,$compteur_salle,true)' title=\"Affecter les copies de cette salle à ce professeur\">\n";
				echo "$info_prof[$i]\n";
				echo "</a>\n";
			}
			else {
				echo "$info_prof[$i]\n";
			}
			//echo "<input type='hidden' name='salle[$i]' value='$salle[$i]' />\n";
			// A FAIRE: Afficher effectif
			// style='color:red;'
			//echo "<br />(<span id='eff_prof_".$lig->id."_$i'>Effectif</span>)";
			echo "</th>\n";
		}
		echo "<th>\n";
		if($etat!='clos') {
			echo "<a href='javascript:coche($i,$compteur_salle,true)'>\n";
			echo "Non affecté";
			echo "</a>\n";
		}
		else {
			echo "Non affecté";
		}
		echo "</th>\n";
		echo "</tr>\n";
	
		if($etat!='clos') {
			echo "<tr>\n";
			echo "<th>Effectifs</th>\n";
			echo "<th>&nbsp;</th>\n";
			echo "<th>&nbsp;</th>\n";
			for($i=0;$i<count($info_prof);$i++) {
				echo "<th title=\"Nombre de copies attribuées à ce professeur par rapport au nombre d'élèves qu'il a en cours.\">\n";
				echo "<span id='eff_prof_".$lig->id."_$i'>Effectif</span>";
				echo "/".$eff_habituel_prof[$i]."\n";
				echo "</th>\n";
			}
			echo "<th>\n";
			//$i++;
			echo "<span id='eff_prof_".$lig->id."_$i'>Effectif</span>";
			echo "</th>\n";
			echo "</tr>\n";
		}

		$sql="SELECT ec.*, e.nom, e.prenom, e.date_entree, e.date_sortie FROM eb_copies ec,eleves e WHERE ec.id_epreuve='$id_epreuve' AND ec.login_ele=e.login AND ec.id_salle='$lig->id' ORDER BY e.nom,e.prenom;";
		//echo "$sql<br />";
		$res2=mysqli_query($GLOBALS["mysqli"], $sql);
	
		$alt=1;
		//$tab_ele_prof=array();
		$compteur_eleves_dans_la_salle=0;
		while($lig2=mysqli_fetch_object($res2)) {
			$alt=$alt*(-1);
			//echo "<tr class='lig$alt white_hover'>\n";
			if((isset($lig2->date_sortie))&&(!is_null($lig2->date_sortie))&&($lig2->date_sortie!='0000-00-00 00:00:00')&&($lig2->date_sortie<$mysql_date_epreuve)) {
				echo "<tr class='white_hover' style='background-color:grey' title=\"Élève sorti de l'établissement (".formate_date($lig2->date_sortie).") avant l'épreuve ($date_epreuve).\">\n";
			}
			elseif((isset($lig2->date_entree))&&(!is_null($lig2->date_entree))&&($lig2->date_entree!='0000-00-00 00:00:00')&&($lig2->date_entree>$mysql_date_epreuve)) {
				echo "<tr class='white_hover' style='background-color:grey' title=\"Élève entré dans l'établissement (".formate_date($lig2->date_entree).") après l'épreuve ($date_epreuve).\">\n";
			}
			else {
				echo "<tr class='lig$alt white_hover'>\n";
			}

			echo "<td>\n";
			$numero_anonymat=$lig2->n_anonymat;
			echo $numero_anonymat;
			echo "</td>\n";

			echo "<td style='text-align:left;'>\n";
			$login_ele=$lig2->login_ele;
			echo "<input type='hidden' name='login_ele[$cpt]' value='$login_ele' />\n";
			//echo get_nom_prenom_eleve($login_ele);
			echo "<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."' title=\"Voir la fiche élève dans un nouvel onglet.\" target='_blank'>".casse_mot($lig2->nom)." ".casse_mot($lig2->prenom,'majf2')."</a>";
			echo "</td>\n";

			echo "<td>\n";
			$tmp_tab_classe=get_class_from_ele_login($login_ele);
			echo $tmp_tab_classe['liste'];
			echo "</td>\n";
	
			$affect="n";
			for($i=0;$i<count($info_prof);$i++) {
				echo "<td>\n";

				if((isset($tab_ele_prof_habituel[$login_ele]))&&($tab_ele_prof_habituel[$login_ele]==$login_prof[$i])) {
					echo "<div style='float:right; width:17px;'><img src='../images/icons/flag.png' width='17' height='18' title='Professeur habituel de cet élève' alt='Professeur habituel de cet élève' /></div>\n";
					$compteur_eleves_du_prof[$i]++;
				}

				if($etat!='clos') {
					echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='$login_prof[$i]' ";
					echo "title=\"Attribuer la copie n°".$numero_anonymat." à ".$info_prof[$i]."\" ";
					echo "onchange='calcule_effectif();changement();' ";
					// On risque une blague si pour une raison ou une autre, on n'a pas une copie dans eb_copies pour tous les élèves du groupe (toutes périodes confondues)... à améliorer
					if($lig2->login_prof==$login_prof[$i]) {echo "checked ";$affect="y";}
					echo "/>\n";
				}
				else {
					if($lig2->login_prof==$login_prof[$i]) {echo "X";$affect="y";}
				}

				echo "</td>\n";
			}
			echo "<td>\n";
			if($etat!='clos') {
				echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='' ";
				echo "title=\"Supprimer l'attribution de la copie n°".$numero_anonymat." à tel ou tel professeur.\" ";
				echo "onchange='calcule_effectif();changement();' ";
				if($affect=="n") {echo "checked ";}
				echo "/>\n";
			}
			else {
				if($affect=="n") {echo "X";}
			}
			echo "</td>\n";
			echo "</tr>\n";
			$cpt++;

		$compteur_eleves_dans_la_salle++;

		}
		echo "<tr>\n";
		echo "<th></th>\n";
		echo "<th></th>\n";
		echo "<th></th>\n";
		for($i=0;$i<count($info_prof);$i++) {
			echo "<th title=\"Le professeur a ".$compteur_eleves_du_prof[$i]." élève(s) en cours parmi les $compteur_eleves_dans_la_salle de cette salle\">".$compteur_eleves_du_prof[$i]."/".$compteur_eleves_dans_la_salle."</th>\n";
		}
		echo "<th></th>\n";
		echo "</table>\n";
		echo "</tr>\n";
		//echo "\$cpt=$cpt<br />";

		echo "</blockquote>\n";
	}
	if($etat!='clos') {
		echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves$cpt' value='Valider' /></p>\n";
	}

	$compteur_salle++;
	$tab_salle[]="Non affecté";
	$tab_id_salle[]='na';

	$sql="SELECT ec.*, e.nom, e.prenom, e.date_entree, e.date_sortie FROM eb_copies ec,eleves e WHERE ec.id_epreuve='$id_epreuve' AND ec.login_ele=e.login AND ec.id_salle='-1' ORDER BY e.nom,e.prenom;";
	//echo "$sql<br />";
	$res2=mysqli_query($GLOBALS["mysqli"], $sql);

	if(mysqli_num_rows($res2)==0) {
		echo "<p>Tous les élèves sont affectés dans des salles.</p>\n";
	}
	else {
		echo "<p>Elèves <b>non affectés</b> dans une salle&nbsp;:</p>\n";
		echo "<blockquote>\n";
	
		//echo "\$cpt=$cpt<br />";
		echo "<table class='boireaus' summary='Elèves non affectés'>\n";
		echo "<tr>\n";
		echo "<th>Elèves</th>\n";
		echo "<th>Classes</th>\n";
		for($i=0;$i<count($info_prof);$i++) {
			echo "<th>\n";
			if($etat!='clos') {
				echo "<a href='javascript:coche($i,$compteur_salle,true)'>\n";
				echo "$info_prof[$i]\n";
				echo "</a>\n";
			}
			else {
				echo "$info_prof[$i]\n";
			}
			echo "</th>\n";
		}
		echo "<th>\n";
		if($etat!='clos') {
			echo "<a href='javascript:coche($i,$compteur_salle,true)'>\n";
			echo "Non affecté";
			echo "</a>\n";
		}
		else {
			echo "Non affecté";
		}
		echo "</th>\n";
		echo "</tr>\n";
	
		if($etat!='clos') {
			echo "<tr>\n";
			echo "<th>Effectifs</th>\n";
			echo "<th>&nbsp;</th>\n";
			for($i=0;$i<count($info_prof);$i++) {
				echo "<th>\n";
				echo "<span id='eff_prof_na_$i'>Effectif</span>";
				echo "</th>\n";
			}
			echo "<th>\n";
			//$i++;
			echo "<span id='eff_prof_na_$i'>Effectif</span>";
			echo "</th>\n";
			echo "</tr>\n";
		}
	
		$tab_cpt_eleve[]=$cpt;
		$alt=1;
		//$tab_ele_prof=array();
		while($lig2=mysqli_fetch_object($res2)) {
			$alt=$alt*(-1);
			//echo "<tr class='lig$alt'>\n";
			if((isset($lig2->date_sortie))&&(!is_null($lig2->date_sortie))&&($lig2->date_sortie!='0000-00-00 00:00:00')&&($lig2->date_sortie<$mysql_date_epreuve)) {
				echo "<tr class='white_hover' style='background-color:grey' title=\"Élève sorti de l'établissement (".formate_date($lig2->date_sortie).") avant l'épreuve ($date_epreuve).\">\n";
			}
			elseif((isset($lig2->date_entree))&&(!is_null($lig2->date_entree))&&($lig2->date_entree!='0000-00-00 00:00:00')&&($lig2->date_entree>$mysql_date_epreuve)) {
				echo "<tr class='white_hover' style='background-color:grey' title=\"Élève entré dans l'établissement (".formate_date($lig2->date_entree).") après l'épreuve ($date_epreuve).\">\n";
			}
			else {
				echo "<tr class='lig$alt white_hover'>\n";
			}

			echo "<td style='text-align:left;'>\n";
			$login_ele=$lig2->login_ele;
			echo "<input type='hidden' name='login_ele[$cpt]' value='$login_ele' />\n";
			echo "<a href='../eleves/visu_eleve.php?ele_login=".$login_ele."' title=\"Voir la fiche élève dans un nouvel onglet.\" target='_blank'>".casse_mot($lig2->nom)." ".casse_mot($lig2->prenom,'majf2')."</a>";
			echo "</td>\n";
	
			echo "<td>\n";
			$tmp_tab_classe=get_class_from_ele_login($login_ele);
			echo $tmp_tab_classe['liste'];
			echo "</td>\n";
	
			$affect="n";
			for($i=0;$i<count($info_prof);$i++) {
				echo "<td>\n";
				if($etat!='clos') {
					echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='$login_prof[$i]' ";
					echo "onchange='calcule_effectif();changement();' ";
					// On risque une blague si pour une raison ou une autre, on n'a pas une copie dans eb_copies pour tous les élèves du groupe (toutes périodes confondues)... à améliorer
					if($lig2->login_prof==$login_prof[$i]) {echo "checked ";$affect="y";}
					echo "/>\n";
				}
				else {
					if($lig2->login_prof==$login_prof[$i]) {echo "X";$affect="y";}
				}
				echo "</td>\n";
			}
			echo "<td>\n";
			if($etat!='clos') {
				echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='' ";
				echo "onchange='calcule_effectif();changement();' ";
				if($affect=="n") {echo "checked ";}
				echo "/>\n";
			}
			else {
				if($affect=="n") {echo "X";}
			}
			echo "</td>\n";
			echo "</tr>\n";
			$cpt++;
		}
		echo "</table>\n";
		//echo "\$cpt=$cpt<br />";
		$tab_cpt_eleve[]=$cpt;
	
		echo "</blockquote>\n";
		if($etat!='clos') {
			echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves$cpt' value='Valider' /></p>\n";
		}
	}

	if($etat!='clos') {
		echo "<input type='hidden' name='tri' value='$tri' />\n";
		echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
		echo "<input type='hidden' name='mode' value='affect_eleves' />\n";
		echo "<input type='hidden' name='valide_affect_eleves' value='y' />\n";
		//echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves2' value='Valider' /></p>\n";
		echo "	</fieldset>
</form>\n";
		
		$chaine_salles="";
		for($i=0;$i<count($tab_id_salle);$i++) {
			if($i>0) {$chaine_salles.=",";}
			$chaine_salles.="'$tab_id_salle[$i]'";
		}
	
		/*
		$chaine_cpt0_eleves="";
		$chaine_cpt1_eleves="";
		for($i=0;$i<count($tab_cpt_eleve);$i+=2) {
			if($i>0) {$chaine_cpt0_eleves.=",";$chaine_cpt1_eleves.=",";}
			//$j=2*$i;
			$chaine_cpt0_eleves.="'$tab_cpt_eleve[$i]'";
			//$j=2*$i+1;
			$j=$i+1;
			$chaine_cpt1_eleves.="'$tab_cpt_eleve[$j]'";
		}
		*/

		/*
		echo "<pre>";
		print_r($tab_cpt_eleve);
		echo "</pre>";
		*/

		$chaine_cpt0_eleves="";
		$chaine_cpt1_eleves="";
		for($i=0;$i<count($tab_cpt_eleve);$i++) {
			//if($i>1) {$chaine_cpt1_eleves.=",";}
			if($chaine_cpt1_eleves!="") {$chaine_cpt1_eleves.=",";}
			if($i>0) {
				$chaine_cpt0_eleves.=",";
				$chaine_cpt1_eleves.="'$tab_cpt_eleve[$i]'";
			}
			$chaine_cpt0_eleves.="'$tab_cpt_eleve[$i]'";
		}
		if($chaine_cpt1_eleves!="") {
			$chaine_cpt1_eleves.=",";
		}
		$chaine_cpt1_eleves.="'$cpt'";

		//echo "\$chaine_cpt0_eleves=$chaine_cpt0_eleves<br />";
		//echo "\$chaine_cpt1_eleves=$chaine_cpt1_eleves<br />";
	

		echo "<script type='text/javascript'>

function calcule_effectif() {
	var tab_salles=new Array($chaine_salles);
	var eff;

	for(i=0;i<".count($login_prof)."+1;i++) {
		eff=0;

		for(j=0;j<$cpt;j++) {
			if(document.getElementById('id_prof_ele_'+i+'_'+j)) {
				if(document.getElementById('id_prof_ele_'+i+'_'+j).checked) {
					eff++;
				}
			}
		}

		for(j=0;j<tab_salles.length;j++) {
			if(document.getElementById('eff_prof_'+tab_salles[j]+'_'+i)) {
				document.getElementById('eff_prof_'+tab_salles[j]+'_'+i).innerHTML=eff;
			}
		}
	}
}

calcule_effectif();

function coche(colonne,rang_groupe,mode) {
	var tab_cpt0_ele=new Array($chaine_cpt0_eleves);
	var tab_cpt1_ele=new Array($chaine_cpt1_eleves);

	//for(k=tab_cpt0_ele[rang_groupe];k<tab_cpt1_ele[rang_groupe];k++) {
	for(k=eval(tab_cpt0_ele[rang_groupe]);k<eval(tab_cpt1_ele[rang_groupe]);k++) {
		if(document.getElementById('id_prof_ele_'+colonne+'_'+k)) {
			document.getElementById('id_prof_ele_'+colonne+'_'+k).checked=mode;
		}
	}

	calcule_effectif();

	changement();
}


function repartir_automatiquement_entre_les_profs(mode) {
	if(mode==1) {
		var effectif_par_prof=Math.ceil($cpt/".count($login_prof).");

		for(j=0;j<$cpt;j++) {
			indice_prof=Math.floor(j/effectif_par_prof);
			if(document.getElementById('id_prof_ele_'+indice_prof+'_'+j)) {
				document.getElementById('id_prof_ele_'+indice_prof+'_'+j).checked=true;
			}
		}
	}
	else if(mode==2) {
		var tab_eff_prof=new Array($js_chaine_effectif_habituel_prof);

		indice_prof=0;
		limite=tab_eff_prof[indice_prof];
		for(j=0;j<$cpt;j++) {
			if(j>=limite) {
				indice_prof++;
				limite+=tab_eff_prof[indice_prof];
			}
			if(document.getElementById('id_prof_ele_'+indice_prof+'_'+j)) {
				document.getElementById('id_prof_ele_'+indice_prof+'_'+j).checked=true;
			}
		}

	}
	else if(mode==3) {
		var tab_eff_prof=new Array($js_chaine_effectif_habituel_prof);

		var chaine_debug='';
		var tab_deja=new Array();
		for(indice_prof=0;indice_prof<tab_eff_prof.length;indice_prof++) {
			tab_deja[indice_prof]=0;
			for(j=0;j<$cpt;j++) {
				if(document.getElementById('id_prof_ele_'+indice_prof+'_'+j)) {
					if(document.getElementById('id_prof_ele_'+indice_prof+'_'+j).checked==true) {
						tab_deja[indice_prof]++;
					}
				}
			}
			chaine_debug+=' tab_eff_prof['+indice_prof+']='+tab_eff_prof[indice_prof];
			chaine_debug+=' tab_deja['+indice_prof+']='+tab_deja[indice_prof];
		}
		//alert(chaine_debug);
		//document.getElementById('div_debug').innerHTML=chaine_debug;

		indice_prof=0;
		limite=tab_eff_prof[indice_prof];
		for(j=0;j<$cpt;j++) {
			if(tab_deja[indice_prof]>=limite) {
				indice_prof++;
				limite=tab_eff_prof[indice_prof];
			}
			if(document.getElementById('id_prof_ele_'+indice_prof+'_'+j)) {
				if(document.getElementById('id_prof_ele_'+tab_eff_prof.length+'_'+j).checked==true) {
					document.getElementById('id_prof_ele_'+indice_prof+'_'+j).checked=true;
					tab_deja[indice_prof]++;

					//document.getElementById('div_debug').innerHTML+='<br />'+j+'-&gt; prof'+indice_prof+' ('+tab_deja[indice_prof]+')';
				}
			}
		}

	}
	else {
		var effectif_par_prof=Math.ceil($cpt/".count($login_prof).");
		var tab_eff_prof=new Array($js_chaine_effectif_habituel_prof);

		var chaine_debug='';
		var tab_deja=new Array();
		for(indice_prof=0;indice_prof<tab_eff_prof.length;indice_prof++) {
			tab_deja[indice_prof]=0;
			for(j=0;j<$cpt;j++) {
				if(document.getElementById('id_prof_ele_'+indice_prof+'_'+j)) {
					if(document.getElementById('id_prof_ele_'+indice_prof+'_'+j).checked==true) {
						tab_deja[indice_prof]++;
					}
				}
			}
			chaine_debug+=' tab_eff_prof['+indice_prof+']='+tab_eff_prof[indice_prof];
			chaine_debug+=' tab_deja['+indice_prof+']='+tab_deja[indice_prof];
		}
		//alert(chaine_debug);
		//document.getElementById('div_debug').innerHTML=chaine_debug;

		indice_prof=0;
		limite=effectif_par_prof;
		for(j=0;j<$cpt;j++) {
			if(tab_deja[indice_prof]>=limite) {
				indice_prof++;
			}
			if(document.getElementById('id_prof_ele_'+indice_prof+'_'+j)) {
				if(document.getElementById('id_prof_ele_'+tab_eff_prof.length+'_'+j).checked==true) {
					document.getElementById('id_prof_ele_'+indice_prof+'_'+j).checked=true;
					tab_deja[indice_prof]++;

					//document.getElementById('div_debug').innerHTML+='<br />'+j+'-&gt; prof'+indice_prof+' ('+tab_deja[indice_prof]+')';
				}
			}
		}

	}

	calcule_effectif();
	changement();
}
</script>\n";
	}
}
else {
	echo "<p>Le mode de tri choisi ne convient pas.</p>\n";
}

//echo "<p style='color:red;'>Ajouter des confirm_abandon() sur les liens.</p>\n";

echo "<p style='color:red;'>A FAIRE: Permettre de ne pas attribuer les copies... pouvoir saisir en piochant dans la liste.</p>\n";

require("../lib/footer.inc.php");
?>
