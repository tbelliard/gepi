<?php
/*
 * $Id : $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
    die();};

// INSERT INTO droits VALUES ('/gestion/consult_prefs.php', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'Définition des préférences d utilisateurs', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


/*
function getPref($login,$item,$default){
	$sql="SELECT value FROM preferences WHERE login='$login' AND name='$item'";
	$res_prefs=mysql_query($sql);

	if(mysql_num_rows($res_prefs)>0){
		$ligne=mysql_fetch_object($res_prefs);
		return $ligne->value;
	}
	else{
		return $default;
	}
}
*/
// Ajout de la possibilité d'afficher ou pas le menu en barre horizontale
$afficherMenu = isset($_POST["afficher_menu"]) ? $_POST["afficher_menu"] : NULL;
$modifier_le_menu = isset($_POST["modifier_le_menu"]) ? $_POST["modifier_le_menu"] : NULL;
$modifier_entete_prof = isset($_POST['modifier_entete_prof']) ? $_POST['modifier_entete_prof'] : NULL;
$page = isset($_GET['page']) ? $_GET['page'] : (isset($_POST['page']) ? $_POST['page'] : NULL);
$prof = isset($_POST['prof']) ? $_POST['prof'] : NULL;
$enregistrer=isset($_POST['enregistrer']) ? $_POST['enregistrer'] : NULL;
$msg="";

if($_SESSION['statut']!="administrateur"){
	unset($prof);
	$prof = array($_SESSION['login']);
}
// +++++++++++++++++++++ MENU en barre horizontale ++++++++++++++++++++

	// Petite fonction pour déterminer le checked="checked" des input en tenant compte des deux utilisations (admin et prof)
	function eval_checked($Settings, $yn, $statut, $nom){
		$aff_check = '';
		if ($statut == "professeur") {
			/*
			$req_setting = mysql_fetch_array(mysql_query("SELECT value FROM preferences WHERE login = '".$nom."' AND name = '".$Settings."'"))
								OR DIE ('Erreur requête eval_setting (prof) : '.mysql_error());
			*/
			$test=mysql_query("SELECT value FROM preferences WHERE login = '".$nom."' AND name = '".$Settings."'");
			if(mysql_num_rows($test)>0) {
				$req_setting = mysql_fetch_array($test);
			}
		}
		elseif ($statut == "administrateur") {

			$test=mysql_query("SELECT value FROM setting WHERE name = '".$Settings."'");
			if(mysql_num_rows($test)>0) {
				$req_setting = mysql_fetch_array($test);
			}
		}

		if((isset($req_setting["value"]))&&($req_setting["value"]==$yn)) {
			$aff_check = ' checked="checked"';
		}else {
			$aff_check = '';
		}

		return $aff_check;
	} //function eval_checked()

	// On traite si c'est demandé
			$messageMenu = '';
if ($modifier_le_menu == "ok") {
	// On fait la modif demandée
	// pour l'administrateur général
	if ($_SESSION["statut"] == "administrateur"){
		$sql = "UPDATE setting SET value = '".$afficherMenu."' WHERE name = 'utiliserMenuBarre'";
	// ou pour les professeurs
	}elseif ($_SESSION["statut"] == "professeur") {
		// Pour le prof, on vérifie si ce réglage existe ou pas
		$query = mysql_query("SELECT value FROM preferences WHERE name = 'utiliserMenuBarre' AND login = '".$_SESSION["login"]."'");
		$verif = mysql_num_rows($query);
		if ($verif == 1) {
			// S'il existe, on le modifie
			$sql = "UPDATE preferences SET value = '".$afficherMenu."' WHERE name = 'utiliserMenuBarre' AND login = '".$_SESSION["login"]."'";
		}else {
			// Sinon, on le crée
			$sql = "INSERT INTO preferences SET login = '".$_SESSION["login"]."', name = 'utiliserMenuBarre', value = '".$afficherMenu."'";
		}
	}
		// Dans tous les cas, on envoie la requête et on renvoie le message adéquat.
		$requete = mysql_query($sql);
		if ($requete) {
			$messageMenu = "<p style=\"color: green\">La modification a été enregistrée</p>";
		}else{
			$messageMenu = "<p style=\"color: red\">La modification a échoué, vous devriez mettre à jour votre base
							 avant de poursuivre</p>";
		}
} // fin du if ($modifier_le_menu...
// +++++++++++++++++++++ FIN -- MENU en barre horizontale -- FIN ++++++++++++++++++++

// ====== hauteur du header ======= //
	$message_header_prof = NULL;

if ($modifier_entete_prof == 'ok') {
	// On traite alors la demande
	$reglage = isset($_POST['header_bas']) ? $_POST['header_bas'] : 'n';

	if (saveSetting('impose_petit_entete_prof', $reglage)) {
		$message_header_prof = '<p style="color: green;">Modification enregistrée</p>';
	}else{
		$message_header_prof = '<p style="color: red;">Impossible d\'enregistrer la modification</p>';
	}
}



// Tester les valeurs de $page
// Les valeurs autorisées sont (actuellement): accueil, add_modif_dev, add_modif_conteneur
//if(isset($page)){
if((isset($page))&&($_SESSION['statut']=="administrateur")){
	if(($page!="accueil_simpl")&&($page!="add_modif_dev")&&($page!="add_modif_conteneur")){
		$page=NULL;
		$enregistrer=NULL;
		$msg="La page choisie ne convient pas.";
	}
}

if(isset($enregistrer)){
	for($i=0;$i<count($prof);$i++){
		//if($page=='accueil_simpl'){
		if(($page=='accueil_simpl')||($_SESSION['statut']=='professeur')){
			//$tab=array('accueil_simpl','accueil_ct','accueil_cn','accueil_bull','accueil_visu','accueil_trombino','accueil_liste_pdf','accueil_aff_txt_icon');
			$tab=array('accueil_simpl','accueil_infobulles','accueil_ct','accueil_cn','accueil_bull','accueil_visu','accueil_trombino','accueil_liste_pdf');

			for($j=0;$j<count($tab);$j++){
				unset($valeur);
				//$valeur=isset($_POST[$tab[$j]]) ? $_POST[$tab[$j]] : NULL;
				$tmp_champ=$tab[$j]."_".$i;
				$valeur=isset($_POST[$tmp_champ]) ? $_POST[$tmp_champ] : NULL;

				$sql="DELETE FROM preferences WHERE login='".$prof[$i]."' AND name='".$tab[$j]."'";
				//echo $sql."<br />\n";
				$res_suppr=mysql_query($sql);

				if(isset($valeur)){
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='$valeur'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Erreur lors de l'enregistrement de $tab[$j] pour $prof[$i]<br />\n";
					}
				}
				else{
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='n'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Erreur lors de l'enregistrement de $tab[$j] pour $prof[$i]<br />\n";
					}
				}
			}
		}

		if(($page=='add_modif_dev')||($_SESSION['statut']=='professeur')){
			//$tab=array('add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_note_autre_que_referentiel','add_modif_dev_date','add_modif_dev_boite');
			$tab=array('add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_note_autre_que_referentiel','add_modif_dev_date','add_modif_dev_date_ele_resp','add_modif_dev_boite');
			for($j=0;$j<count($tab);$j++){
				unset($valeur);
				//$valeur=isset($_POST[$tab[$j]]) ? $_POST[$tab[$j]] : NULL;
				$tmp_champ=$tab[$j]."_".$i;
				$valeur=isset($_POST[$tmp_champ]) ? $_POST[$tmp_champ] : NULL;

				$sql="DELETE FROM preferences WHERE login='".$prof[$i]."' AND name='".$tab[$j]."'";
				//echo $sql."<br />\n";
				$res_suppr=mysql_query($sql);

				if(isset($valeur)){
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='$valeur'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Erreur lors de l'enregistrement de $tab[$j] pour $prof[$i]<br />\n";
					}
				}
				else{
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='n'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Erreur lors de l'enregistrement de $tab[$j] pour $prof[$i]<br />\n";
					}
				}
			}
		}

		if(($page=='add_modif_conteneur')||($_SESSION['statut']=='professeur')){
			$tab=array('add_modif_conteneur_simpl','add_modif_conteneur_nom_court','add_modif_conteneur_nom_complet','add_modif_conteneur_description','add_modif_conteneur_coef','add_modif_conteneur_boite','add_modif_conteneur_aff_display_releve_notes','add_modif_conteneur_aff_display_bull');
			for($j=0;$j<count($tab);$j++){
				unset($valeur);
				//$valeur=isset($_POST[$tab[$j]]) ? $_POST[$tab[$j]] : NULL;
				$tmp_champ=$tab[$j]."_".$i;
				$valeur=isset($_POST[$tmp_champ]) ? $_POST[$tmp_champ] : NULL;

				$sql="DELETE FROM preferences WHERE login='".$prof[$i]."' AND name='".$tab[$j]."'";
				//echo $sql."<br />\n";
				$res_suppr=mysql_query($sql);

				if(isset($valeur)){
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='$valeur'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Erreur lors de l'enregistrement de $tab[$j] pour $prof[$i]<br />\n";
					}
				}
				else{
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='n'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Erreur lors de l'enregistrement de $tab[$j] pour $prof[$i]<br />\n";
					}
				}
			}
		}
	}

	if($msg==""){
		$msg="Enregistrement réussi.";
	}

	//unset($page);
}

// Style spécifique pour la page:
$style_specifique="gestion/config_prefs";

// Couleur pour les cases dans lesquelles une modif est faite:
$couleur_modif='orange';

// Message d'alerte pour ne pas quitter par erreur sans valider:
$themessage="Des modifications ont été effectuées. Voulez-vous vraiment quitter sans enregistrer?";


//**************** EN-TETE *****************
$titre_page = "Configuration des interfaces simplifiées";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// Initialisation de la variable utilisée pour noter si des modifications ont été effectuées dans la page.
echo "<script type='text/javascript'>
	change='no';
</script>\n";

/*
- Choisir la page à afficher
- Choisir les profs? ou juste répéter la ligne de titre?
*/

echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
echo "<div class='norme'><p class=bold>";
echo "<a href='";
if($_SESSION['statut']=='administrateur'){
	echo "index.php";
}
else{
	echo "../accueil.php";
}
echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

//if(!isset($page)){
if((!isset($page))&&($_SESSION['statut']=="administrateur")){
	echo "</div>\n";

	echo "<p>Cette page permet de configurer l'interface simplifiée pour:</p>\n";
	echo "<ul>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?page=accueil_simpl'>Page d'accueil simplifiée pour les ".$gepiSettings['denomination_professeurs']."</a></li>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?page=add_modif_dev'>Page de création d'évaluation</a></li>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?page=add_modif_conteneur'>Page de création de ".strtolower(getSettingValue("gepi_denom_boite"))."</a></li>\n";
	echo "</ul>\n";

}
else{
	if($_SESSION['statut']=="administrateur"){
		echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choix de la page</a>";
	}
	echo "</div>\n";


	unset($prof);
	$prof=array();
	if($_SESSION['statut']=="administrateur"){

		//$sql="SELECT DISTINCT nom,prenom,login FROM utilisateurs WHERE statut='professeur' ORDER BY nom, prenom";
		$sql="SELECT DISTINCT nom,prenom,login FROM utilisateurs WHERE statut='professeur' AND etat='actif' ORDER BY nom, prenom";
		$res_prof=mysql_query($sql);
		if(mysql_num_rows($res_prof)==0){
			echo "<p>Aucun ".$gepiSettings['denomination_professeur']." n'est encore défini.<br />Commencez par créer les comptes ".$gepiSettings['denomination_professeurs'].".</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$i=0;
		while($lig_prof=mysql_fetch_object($res_prof)){
			$prof[$i]=array();
			$prof[$i]['login']=$lig_prof->login;
			$prof[$i]['nom']=$lig_prof->nom;
			$prof[$i]['prenom']=$lig_prof->prenom;
			$i++;
		}
	}
	else{
		$i=0;
		$prof[$i]['login']=$_SESSION['login'];
		$prof[$i]['nom']=$_SESSION['nom'];
		$prof[$i]['prenom']=$_SESSION['prenom'];
	}

	$nb_profs=count($prof);


	function cellule_checkbox($prof_login,$item,$num,$special){
		echo "<td align='center'";
		echo " id='td_".$item."_".$num."' ";
		//echo " style='text-align:center; ";
		$checked="";
		$coche="";
		$sql="SELECT * FROM preferences WHERE login='$prof_login' AND name='$item'";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			$lig_test=mysql_fetch_object($test);
			if($lig_test->value=="y"){
				//echo " style='background-color: lightgreen;'";
				//echo "background-color: lightgreen;";
				echo " class='coche'";
				$checked=" checked";
				$coche="y";
			}
			else{
				//echo " style='background-color: lightgray;'";
				//echo "background-color: lightgray;";
				echo " class='decoche'";
				$coche="n";
			}
		}
		//echo "'";
		echo ">";
		echo "<input type='checkbox' name='$item"."_"."$num' id='$item"."_"."$num' value='y'";

		/*
		// Supprimé après avoir permis l'affichage des tableaux sur une seule page pour l'accès prof à ses propres paramétrages
		if($special=="y"){
			echo " onchange=\"modif_ligne($num)\"";
		}
		*/

		echo $checked;
		//echo " onchange='changement();'";
		echo " onchange=\"changement_et_couleur('$item"."_"."$num','";
		//if($special=="y"){
		if($special!=''){
			//echo "td_nomprenom_$num";
			//echo "td_nomprenom_".$num."_".$special;
			$chaine_td="td_nomprenom_".$num."_".$special;
			echo $chaine_td;
		}
		echo "');\"";
		echo " />";

		//if($special=="y"){
		if($special!=''){
			if($coche=="y"){
				echo "<script type='text/javascript'>
	//document.getElementById('td_nomprenom_'+$num).style.backgroundColor='lightgreen';
	document.getElementById('$chaine_td').style.backgroundColor='lightgreen';
</script>\n";
			}
			elseif($coche=="n"){
				echo "<script type='text/javascript'>
	//document.getElementById('td_nomprenom_'+$num).style.backgroundColor='lightgray';
	document.getElementById('$chaine_td').style.backgroundColor='lightgray';
</script>\n";
			}
		}

		echo "</td>\n";
	} // FIN function cellule_checkbox


/*
	echo "<style type='text/css'>
	table.contenu {
		border: 1px solid black;
		border-collapse: collapse;
	}

	.contenu th {
		font-weight:bold;
		text-align: center;
		background-color: white;
		border: 1px solid black;
	}

	.contenu td {
		vertical-align: middle;
		text-align: center;
		border: 1px solid black;
	}

	.contenu tr.entete {
		background-color: white;
	}

	.contenu .coche {
		background-color: lightgreen;
	}

	.contenu .decoche {
		background-color: lightgray;
	}
</style>\n";
*/

	echo "<p align='center'><input type=\"submit\" name='enregistrer' value=\"Valider\" style=\"font-variant: small-caps;\" /></p>\n";

	//if($page=="accueil_simpl"){
	if(($page=="accueil_simpl")||($_SESSION['statut']=='professeur')){
		echo "<p>Paramétrage de la page d'<b>accueil</b> simplifiée pour les ".$gepiSettings['denomination_professeurs'].".</p>\n";

		//$tabchamps=array('accueil_simpl','accueil_ct','accueil_trombino','accueil_cn','accueil_bull','accueil_visu','accueil_liste_pdf');
		//accueil_aff_txt_icon
		$tabchamps=array('accueil_simpl','accueil_infobulles','accueil_ct','accueil_trombino','accueil_cn','accueil_bull','accueil_visu','accueil_liste_pdf');

		//echo "<table border='1'>\n";
		echo "<table class='contenu' border='1' summary='Préférences professeurs'>\n";

		// 1ère ligne
		//$lignes_entete="<tr style='background-color: white;'>\n";
		$lignes_entete="<tr class='entete'>\n";
		if($_SESSION['statut']!='professeur'){
			$lignes_entete.="<th rowspan='3'>".$gepiSettings['denomination_professeur']."</th>\n";
		}
		else{
			$lignes_entete.="<th rowspan='2'>".$gepiSettings['denomination_professeur']."</th>\n";
		}
		$lignes_entete.="<th rowspan='2'>Utiliser l'interface simplifiée</th>\n";
		$lignes_entete.="<th rowspan='2'>Afficher les infobulles</th>\n";
		$lignes_entete.="<th colspan='6'>Afficher les liens pour</th>\n";
		$lignes_entete.="</tr>\n";

		// 2ème ligne
		//$lignes_entete.="<tr style='background-color: white;'>\n";
		$lignes_entete.="<tr class='entete'>\n";
		$lignes_entete.="<th>le Cahier de textes</th>\n";
		$lignes_entete.="<th>le Trombinoscope</th>\n";
		$lignes_entete.="<th>le Carnet de notes</th>\n";
		$lignes_entete.="<th>les notes et appréciations des Bulletins</th>\n";
		$lignes_entete.="<th>la Visualisation des graphes et bulletins simplifiés</th>\n";
		$lignes_entete.="<th>les Listes PDF des élèves</th>\n";
		$lignes_entete.="</tr>\n";

		// 3ème ligne
		if($_SESSION['statut']!='professeur'){
			//$lignes_entete.="<tr style='background-color: white;'>\n";
			$lignes_entete.="<tr class='entete'>\n";
			for($i=0;$i<count($tabchamps);$i++){
				$lignes_entete.="<th>";
				$lignes_entete.="<a href='javascript:modif_coche(\"$tabchamps[$i]\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
				$lignes_entete.="<a href='javascript:modif_coche(\"$tabchamps[$i]\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
				$lignes_entete.="</th>\n";
			}
			$lignes_entete.="</tr>\n";
		}

		//$i=0;
		//while($lig_prof=mysql_fetch_object($res_prof)){
		for($i=0;$i<count($prof);$i++){
			if($i-ceil($i/10)*10==0){
				echo $lignes_entete;
			}

			echo "<tr>\n";

			//echo "<td id='td_nomprenom_".$i."'>";
			echo "<td id='td_nomprenom_".$i."_accueil_simpl'>";
			//echo strtoupper($lig_prof->nom)." ".ucfirst(strtolower($lig_prof->prenom));
			echo strtoupper($prof[$i]['nom'])." ".ucfirst(strtolower($prof[$i]['prenom']));
			//echo "<input type='hidden' name='prof[$i]' value='$lig_prof->login' />";
			echo "<input type='hidden' name='prof[$i]' value='".$prof[$i]['login']."' />";
			echo "</td>\n";

			/*
			cellule_checkbox($prof[$i]['login'],'accueil_simpl',$i,'y');

			cellule_checkbox($prof[$i]['login'],'accueil_ct',$i,'');
			cellule_checkbox($prof[$i]['login'],'accueil_trombino',$i,'');
			cellule_checkbox($prof[$i]['login'],'accueil_cn',$i,'');
			cellule_checkbox($prof[$i]['login'],'accueil_bull',$i,'');
			cellule_checkbox($prof[$i]['login'],'accueil_visu',$i,'');
			cellule_checkbox($prof[$i]['login'],'accueil_liste_pdf',$i,'');
			*/

			$j=0;
			//cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'y');
			cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'accueil_simpl');
			//for($j=0;$j<count($tabchamps);$j++){
			for($j=1;$j<count($tabchamps);$j++){
				cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'');
			}

			echo "</tr>\n";
			//$i++;
		}

		echo "</table>\n";
	}








	if(($page=="add_modif_dev")||($_SESSION['statut']=='professeur')){
		echo "<p>Paramétrage de la page de <b>création d'évaluation</b> pour les ".$gepiSettings['denomination_professeurs']."</p>\n";

		if(getSettingValue("note_autre_que_sur_referentiel")=="V") {
			//$tabchamps=array( 'add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_note_autre_que_referentiel','add_modif_dev_date','add_modif_dev_boite');
			$tabchamps=array( 'add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_note_autre_que_referentiel','add_modif_dev_date','add_modif_dev_date_ele_resp','add_modif_dev_boite');
		} else {
			//$tabchamps=array( 'add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_date','add_modif_dev_boite');	
			$tabchamps=array( 'add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_date','add_modif_dev_date_ele_resp','add_modif_dev_boite');	
		}
		//echo "<table border='1'>\n";
		echo "<table class='contenu' border='1' summary='Préférences professeurs'>\n";

		// 1ère ligne
		//$lignes_entete.="<tr style='background-color: white;'>\n";
		$lignes_entete="<tr class='entete'>\n";
		if($_SESSION['statut']!='professeur'){
			$lignes_entete.="<th rowspan='3'>".$gepiSettings['denomination_professeur']."</th>\n";
		}
		else{
			$lignes_entete.="<th rowspan='2'>".$gepiSettings['denomination_professeur']."</th>\n";
		}
		$lignes_entete.="<th rowspan='2'>Utiliser l'interface simplifiée</th>\n";
		if(getSettingValue("note_autre_que_sur_referentiel")=="V") {
			$lignes_entete.="<th colspan='8'>Afficher les champs</th>\n";
		} else {
			$lignes_entete.="<th colspan='7'>Afficher les champs</th>\n";
		}
		$lignes_entete.="</tr>\n";

		// 2ème ligne
		//$lignes_entete.="<tr style='background-color: white;'>\n";
		$lignes_entete.="<tr class='entete'>\n";
		$lignes_entete.="<th>Nom court</th>\n";
		$lignes_entete.="<th>Nom complet</th>\n";
		$lignes_entete.="<th>Description</th>\n";
		$lignes_entete.="<th>Coefficient</th>\n";
		if(getSettingValue("note_autre_que_sur_referentiel")=="V") {
			$lignes_entete.="<th>Note autre que sur le referentiel</th>\n";
		}
		$lignes_entete.="<th>Date</th>\n";
		$lignes_entete.="<th>Date ele/resp</th>\n";
		$lignes_entete.="<th>".ucfirst(strtolower(getSettingValue("gepi_denom_boite")))."</th>\n";
		$lignes_entete.="</tr>\n";

		// 3ème ligne
		if($_SESSION['statut']!='professeur'){
			//$lignes_entete.="<tr style='background-color: white;'>\n";
			$lignes_entete.="<tr class='entete'>\n";
			for($i=0;$i<count($tabchamps);$i++){
				$lignes_entete.="<th>";
				$lignes_entete.="<a href='javascript:modif_coche(\"$tabchamps[$i]\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
				$lignes_entete.="<a href='javascript:modif_coche(\"$tabchamps[$i]\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
				$lignes_entete.="</th>\n";
			}
			$lignes_entete.="</tr>\n";
		}

		//$i=0;
		//while($lig_prof=mysql_fetch_object($res_prof)){
		for($i=0;$i<count($prof);$i++){
			if($i-ceil($i/10)*10==0){
				echo $lignes_entete;
			}

			echo "<tr>\n";

			//echo "<td>";
			//echo "<td id='td_nomprenom_".$i."'>";
			echo "<td id='td_nomprenom_".$i."_add_modif_dev'>";
			//echo strtoupper($lig_prof->nom)." ".ucfirst(strtolower($lig_prof->prenom));
			echo strtoupper($prof[$i]['nom'])." ".ucfirst(strtolower($prof[$i]['prenom']));
			//echo "<input type='hidden' name='prof[$i]' value='$lig_prof->login' />";
			echo "<input type='hidden' name='prof[$i]' value='".$prof[$i]['login']."' />";
			echo "</td>\n";

			$j=0;
			//cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'y');
			cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'add_modif_dev');
			//for($j=0;$j<count($tabchamps);$j++){
			for($j=1;$j<count($tabchamps);$j++){
				cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'');
			}

			echo "</tr>\n";
			//$i++;
		}

		echo "</table>\n";
	}






	if(($page=="add_modif_conteneur")||($_SESSION['statut']=='professeur')){
		echo "<p>Paramétrage de la page de <b>création de ".ucfirst(strtolower(getSettingValue("gepi_denom_boite")))."</b> pour les ".$gepiSettings['denomination_professeurs']."</p>\n";

		$tabchamps=array('add_modif_conteneur_simpl','add_modif_conteneur_nom_court','add_modif_conteneur_nom_complet','add_modif_conteneur_description','add_modif_conteneur_coef','add_modif_conteneur_boite','add_modif_conteneur_aff_display_releve_notes','add_modif_conteneur_aff_display_bull');

		//echo "<table border='1'>\n";
		echo "<table class='contenu' border='1' summary='Préférences professeurs'>\n";

		// 1ère ligne
		//$lignes_entete.="<tr style='background-color: white;'>\n";
		$lignes_entete="<tr class='entete'>\n";
		if($_SESSION['statut']!='professeur'){
			$lignes_entete.="<th rowspan='3'>".$gepiSettings['denomination_professeur']."</th>\n";
		}
		else{
			$lignes_entete.="<th rowspan='2'>".$gepiSettings['denomination_professeur']."</th>\n";
		}
		$lignes_entete.="<th rowspan='2'>Utiliser l'interface simplifiée</th>\n";
		$lignes_entete.="<th colspan='7'>Afficher les champs</th>\n";
		$lignes_entete.="</tr>\n";

		// 2ème ligne
		//$lignes_entete.="<tr style='background-color: white;'>\n";
		$lignes_entete.="<tr class='entete'>\n";
		$lignes_entete.="<th>Nom court</th>\n";
		$lignes_entete.="<th>Nom complet</th>\n";
		$lignes_entete.="<th>Description</th>\n";
		$lignes_entete.="<th>Coefficient</th>\n";
		$lignes_entete.="<th>".ucfirst(strtolower(getSettingValue("gepi_denom_boite")))."</th>\n";
		$lignes_entete.="<th>Afficher sur le relevé de notes</th>\n";
		$lignes_entete.="<th>Afficher sur le bulletin</th>\n";
		$lignes_entete.="</tr>\n";

		// 3ème ligne
		if($_SESSION['statut']!='professeur'){
			//$lignes_entete.="<tr style='background-color: white;'>\n";
			$lignes_entete.="<tr class='entete'>\n";
			for($i=0;$i<count($tabchamps);$i++){
				$lignes_entete.="<th>";
				$lignes_entete.="<a href='javascript:modif_coche(\"$tabchamps[$i]\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
				$lignes_entete.="<a href='javascript:modif_coche(\"$tabchamps[$i]\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
				$lignes_entete.="</th>\n";
			}
			$lignes_entete.="</tr>\n";
		}

		//$i=0;
		//while($lig_prof=mysql_fetch_object($res_prof)){
		for($i=0;$i<count($prof);$i++){
			if($i-ceil($i/10)*10==0){
				echo $lignes_entete;
			}

			echo "<tr>\n";

			//echo "<td>";
			//echo "<td id='td_nomprenom_".$i."'>";
			echo "<td id='td_nomprenom_".$i."_add_modif_conteneur'>";
			//echo strtoupper($lig_prof->nom)." ".ucfirst(strtolower($lig_prof->prenom));
			echo strtoupper($prof[$i]['nom'])." ".ucfirst(strtolower($prof[$i]['prenom']));
			//echo "<input type='hidden' name='prof[$i]' value='$lig_prof->login' />";
			echo "<input type='hidden' name='prof[$i]' value='".$prof[$i]['login']."' />";
			echo "</td>\n";

			$j=0;
			//cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'y');
			cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'add_modif_conteneur');
			//for($j=0;$j<count($tabchamps);$j++){
			for($j=1;$j<count($tabchamps);$j++){
				cellule_checkbox($prof[$i]['login'],$tabchamps[$j],$i,'');
			}

			echo "</tr>\n";
			//$i++;
		}

		echo "</table>\n";
	}








	// La page n'est considérée que pour l'admin pour réduire la longueur de la liste
	if($_SESSION['statut']=='administrateur'){
		echo "<input type=\"hidden\" name='page' value=\"$page\" />\n";
	}

	echo "<p align='center'><input type=\"submit\" name='enregistrer' value=\"Valider\" style=\"font-variant: small-caps;\" /></p>\n";

	echo "<script type='text/javascript' language='javascript'>
	function modif_coche(item,statut){
		// statut: true ou false
		for(k=0;k<$nb_profs;k++){
			if(document.getElementById(item+'_'+k)){
				document.getElementById(item+'_'+k).checked=statut;

				document.getElementById('td_'+item+'_'+k).style.backgroundColor='$couleur_modif';
			}
		}
		changement();
	}

	function changement_et_couleur(id,special){
		if(document.getElementById(id)){
			document.getElementById('td_'+id).style.backgroundColor='$couleur_modif';
		}

		if(special!=''){
			document.getElementById(special).style.backgroundColor='$couleur_modif';
		}

		changement();
	}
";

	/*
	echo "
	function modif_ligne(num){";

	$liste_champs="";
	for($k=0;$k<count($tabchamps);$k++){
		if($k>0){$liste_champs.=", ";}
		$liste_champs.="'$tabchamps[$k]'";
	}

		echo "
		tabchamps=Array($liste_champs);
		for(k=0;k<tabchamps.length;k++){
			item=tabchamps[k];
			if(document.getElementById('td_'+item+'_'+num)){
				document.getElementById('td_'+item+'_'+num).style.backgroundColor='orange';
			}
		}
		changement();
	}
";
	*/
	echo "</script>\n";


	echo "<p><i>Remarques:</i></p>\n";
	echo "<ul>\n";
	echo "<li>La prise en compte des champs choisis est conditionnée par le fait d'avoir coché ou non la colonne 'Utiliser l'interface simplifiée' pour l'utilisateur considéré.</li>\n";
	echo "<li>Les champs non proposés dans les interfaces simplifiées restent accessibles aux utilisateurs en cliquant sur les liens 'Interface complète' proposés dans les pages d'interfaces simplifiées .</li>\n";
	echo "</ul>\n";
	//}
}

echo "</form>\n";

	// On ajoute le réglage pour le menu en barre horizontale
	$aff = "non";
if ($_SESSION["statut"] == "administrateur") {
	$aff = "oui";
}elseif($_SESSION["statut"] == "professeur" AND getSettingValue("utiliserMenuBarre") == "yes") {
	$aff = "oui";
}else {
	$aff = "non";
}
// On affiche si c'est autorisé
if ($aff == "oui") {
	echo '
		<form name="change_menu" method="post" action="./config_prefs.php">
	<fieldset id="afficherBarreMenu" style="border: 1px solid grey;">
		<legend style="border: 1px solid grey;">Gérer la barre horizontale du menu</legend>
			<input type="hidden" name="modifier_le_menu" value="ok" />
		<p>
			<label for="visibleMenu">Rendre visible la barre de menu horizontale sous l\'en-tête.</label>
			<input type="radio" id="visibleMenu" name="afficher_menu" value="yes"'.eval_checked("utiliserMenuBarre", "yes", $_SESSION["statut"], $_SESSION["login"]).' onclick="document.change_menu.submit();" />
		</p>
		<p>
			<label for="invisibleMenu">Ne pas utiliser la barre de menu horizontale.</label>
			<input type="radio" id="invisibleMenu" name="afficher_menu" value="no"'.eval_checked("utiliserMenuBarre", "no", $_SESSION["statut"], $_SESSION["login"]).' onclick="document.change_menu.submit();" />
		</p>
	</fieldset>
		</form>
		'.$messageMenu
		;
} // fin du if ($aff == "oui")

echo '<br />' . "\n";

if ($_SESSION["statut"] == 'administrateur') {
	// On propose de pouvoir obliger tous les professeurs à avoir un header court
	echo '
		<form name="change_header_prof" method="post" action="config_prefs.php">
			<fieldset style="border: 1px solid grey;">
				<legend style="border: 1px solid grey;">Gérer la hauteur de l\'entête pour les professeurs</legend>
				<input type="hidden" name="modifier_entete_prof" value="ok" />
				<p>
					<label for="headerBas">Imposer une entête basse</label>
					<input type="radio" id="headerBas" name="header_bas" value="y"'.eval_checked("impose_petit_entete_prof", "y", "administrateur", $_SESSION["login"]).' onclick="document.change_header_prof.submit();" />
				</p>
				<p>
					<label for="headerNormal">Ne rien imposer</label>
					<input type="radio" id="headerNormal" name="header_bas" value="n"'.eval_checked("impose_petit_entete_prof", "n", "administrateur", $_SESSION["login"]).' onclick="document.change_header_prof.submit();" />
				</p>
				' . $message_header_prof . '
			</fieldset>
		</form>';
}

echo "<br />\n";
require("../lib/footer.inc.php");
?>
