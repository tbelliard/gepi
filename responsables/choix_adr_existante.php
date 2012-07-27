<?php
/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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


if(!isset($pers_id)){
    header("Location: index.php");
    die();
}

if(isset($is_posted)) {
	check_token();

	if(!isset($msg)){
		$msg="";
	}

	if($is_posted=="choix_adr_existante"){
		if($adr_id_existant==''){
			if(!isset($quitter_la_page)) {
				header("Location: modify_resp.php?pers_id=$pers_id");
			}
			else{
				header("Location: modify_resp.php?pers_id=$pers_id&quitter_la_page=$quitter_la_page");
			}
			die();
		}
		else{
			$sql="SELECT 1=1 FROM resp_adr WHERE adr_id='$adr_id_existant'";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0){
				$sql="UPDATE resp_pers SET adr_id='$adr_id_existant' WHERE pers_id='$pers_id'";
				$res_update=mysql_query($sql);
				if(!$res_update){
					$msg.="Erreur lors de l'insertion de l'association personne/adresse. ";
				}
				else{
					if(!isset($quitter_la_page)) {
						header("Location: modify_resp.php?pers_id=$pers_id");
					}
					else{
						header("Location: modify_resp.php?pers_id=$pers_id&quitter_la_page=$quitter_la_page");
					}
					die();
				}
			}
		}
	}
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *******************************
$titre_page = "Choisir une adresse responsable";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE ***************************

if(!getSettingValue('conv_new_resp_table')){
	$sql="SELECT 1=1 FROM responsables";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0){
		echo "<p>Une conversion des données responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SHOW COLUMNS FROM eleves LIKE 'ele_id'";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0){
		echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		$sql="SELECT 1=1 FROM eleves WHERE ele_id=''";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";
			echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
}

echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_close(theLink, thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			self.close();
			return false;
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				self.close();
				return false;
			}
			else{
				return false;
			}
		}
	}
</script>\n";

if(!isset($quitter_la_page)){
	echo "<p class='bold'><a href='modify_resp.php?pers_id=$pers_id'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo "><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
}
else {
	echo "<p class=bold><a href=\"#\"";
	echo " onclick=\"return confirm_close (this, change, '$themessage')\"";
	echo ">Refermer la page</a></p>\n";
}

//$sql="SELECT DISTINCT adr1,adr2,adr3,adr4,cp,commune,pays,adr_id FROM resp_adr ORDER BY commune,cp,adr1,adr2,adr3,adr4";
//$res_adr=mysql_query($sql);
$sql="SELECT COUNT(adr_id) nb_adr FROM resp_adr";
$res_nb=mysql_query($sql);
$tmp_nb=mysql_fetch_object($res_nb);
$nb_adr=$tmp_nb->nb_adr;
//if(mysql_num_rows($res_adr)==0){
if($nb_adr==0){
	echo "<p>Aucune adresse n'est encore définie.<p>\n";
}
else{
	$sql="SELECT nom,prenom FROM resp_pers WHERE pers_id='$pers_id'";
	$res_info_pers=mysql_query($sql);
	$lig_pers=mysql_fetch_object($res_info_pers);


	echo "<p>Choix d'une adresse pour $lig_pers->nom $lig_pers->prenom (<i>$pers_id</i>)</p>\n";

	echo "<form enctype=\"multipart/form-data\" name=\"param_liste\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

	if(isset($quitter_la_page)) {
		echo "<input type='hidden' name='quitter_la_page' value='$quitter_la_page' />\n";
	}

	echo "<p align='center'>";
	if(!isset($debut)){
		$debut=0;
	}
	else{
		if(mb_strlen(my_ereg_replace("[0-9]","",$debut))){
			$debut=0;
		}
	}

	if($debut > 0){
		echo "<input type='button' value='<<' onClick='precedent()' /> \n";
	}

	if(!isset($limit)){
		$limit=100;
	}

	echo "Afficher <select name='limit'>\n";
	if($limit==20){$selected=" selected='true'";}else{$selected="";}
	echo "<option value='20'$selected>20</option>\n";
	if($limit==50){$selected=" selected='true'";}else{$selected="";}
	echo "<option value='50'$selected>50</option>\n";
	//for($i=100;$i<=500;$i+=100){
	for($i=100;$i<=$nb_adr;$i+=100){
		if($limit==$i){$selected=" selected='true'";}else{$selected="";}
		echo "<option value='$i'$selected>$i</option>\n";
	}
	echo "<option value='TOUS'>TOUS ($nb_adr)</option>\n";
	echo "</select> enregistrements à partir de l'enregistrement n°\n";
	echo "<input type='text' name='debut' value='$debut' size='5' /> \n";

	//$cpt=mysql_num_rows($res_adr);
	$cpt=$nb_adr;

	if(isset($cpt)){
		//echo "<p>limit=$limit debut=$debut cpt=$cpt</p>";
		if($limit+$debut<$cpt){
			echo "<input type='button' value='>>' onClick='suivant()' /> \n";
		}
	}
	else{
		echo "<input type='button' value='>>' onClick='suivant()' /> \n";
	}

	echo "</p>\n";

	echo "<script type='text/javascript'>
	function precedent(){
		debut=document.forms.param_liste.debut.value;
		limit=document.forms.param_liste.limit.value;
		//alert('debut='+debut+' et limit='+limit);

		if(limit=='TOUS'){
			document.forms.param_liste.debut.value=0;
		}
		else{
			document.forms.param_liste.debut.value=Math.max(debut-limit,0);
		}
		document.forms.param_liste.submit();
	}

	function suivant(){
		debut=document.forms.param_liste.debut.value;
		limit=document.forms.param_liste.limit.value;
		//alert('debut='+debut+' et limit='+limit);

		if(limit=='TOUS'){
			document.forms.param_liste.debut.value=0;
		}
		else{
			// Il faudrait récupérer le nombre de lignes du tableau...\n";

	if(isset($cpt)){
		echo "			document.forms.param_liste.debut.value=Math.min(eval(debut)+eval(limit),eval($cpt)-eval(limit));\n";
	}
	else{
		echo "			document.forms.param_liste.debut.value=eval(debut)+eval(limit);\n";
	}

	echo "		}
		document.forms.param_liste.submit();
	}
</script>\n";

	echo "<input type='hidden' name='pers_id' value='$pers_id' />\n";
	//echo "<center><input type='submit' value='Valider' /></center>\n";


	if(isset($adr_id_actuel)){
		echo "<input type='hidden' name='adr_id_actuel' value='$adr_id_actuel' />\n";
	}


	echo "<div style='text-align:center;'>\n";
	echo "ou <a href='#' onClick=\"document.getElementById('div_rech').style.display='';return false;\">Filtrer:</a>\n";
	echo "<div id='div_rech' style='display:none;' align='center'>\n";
	echo "<table border='0'>\n";
	echo "<tr>\n";
	echo "<td>les adresses dont \n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='crit_rech_adr' style='cursor: pointer;'>\n";
	echo "<input type='radio' name='crit_rech' id='crit_rech_adr' value='adr' checked /> une ligne 'adrX'\n";
	echo "</label>\n";
	echo "<br />\n";
	echo "<label for='crit_rech_cp' style='cursor: pointer;'>\n";
	echo "<input type='radio' name='crit_rech' id='crit_rech_cp' value='cp' /> le code postal\n";
	echo "</label>\n";
	echo "<br />\n";
	echo "<label for='crit_rech_commune' style='cursor: pointer;'>\n";
	echo "<input type='radio' name='crit_rech' id='crit_rech_commune' value='commune' /> le nom de commune\n";
	echo "</label>\n";
	echo "</td>\n";
	echo "<td>\n";

	echo "<label for='mode_rech_contient' style='cursor: pointer;'>\n";
	echo "<input type='radio' name='mode_rech' id='mode_rech_contient' value='contient' checked /> contient \n";
	echo "</label>\n";
	echo "<br />\n";
	echo "<label for='mode_rech_commence' style='cursor: pointer;'>\n";
	echo "<input type='radio' name='mode_rech' id='mode_rech_commence' value='commence par' /> commence par \n";
	echo "</label>\n";
	echo "<br />\n";
	echo "<label for='mode_rech_termine' style='cursor: pointer;'>\n";
	echo "<input type='radio' name='mode_rech' id='mode_rech_termine' value='se termine par' /> se termine par \n";
	echo "</label>\n";
	/*
	echo "<input type='radio' name='mode_rech' value='contient' checked /> contient \n";
	echo "<br />\n";
	echo "<input type='radio' name='mode_rech' value='commence par' /> commence par \n";
	echo "<br />\n";
	echo "<input type='radio' name='mode_rech' value='se termine par' /> se termine par \n";
	*/
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='text' name='val_rech' value='' />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<center><input type='submit' value='Afficher' /></center>\n";

	echo "</div>\n";
	echo "</div>\n";




	echo "</form>\n";


	echo "<hr width='20%' align='center' />\n";


	echo "<form enctype=\"multipart/form-data\" name=\"choix_adr\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo add_token_field();

	if(isset($quitter_la_page)) {
		echo "<input type='hidden' name='quitter_la_page' value='$quitter_la_page' />\n";
	}

	echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";

	echo "<input type='hidden' name='pers_id' value='$pers_id' />\n";

	echo "<div id='div_ad_existante'>\n";
	//echo "<table border='1'>\n";
	echo "<table class='boireaus'>\n";
	echo "<tr>\n";
	echo "<td style='text-align:center; font-weight:bold;'>&nbsp;</td>\n";
	echo "<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Identifiant</td>\n";
	echo "<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Lignes de l'adresse</td>\n";
	echo "<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Code postal</td>\n";
	echo "<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>";
	echo "Commune";
	echo "</td>\n";
	echo "<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Pays</td>\n";
	echo "<td style='text-align:center; font-weight:bold; background-color:#96C8F0;'>Responsable associé</td>\n";
	//echo "<td style='text-align:center; font-weight:bold; background-color:red;'>Supprimer adresse(s)</td>\n";
	echo "</tr>\n";




	unset($chaine_recherche);
	if(isset($val_rech)){
		//$order_by=="nom,prenom";
		// On ne mixe pas les modes de recherche
		$limit="TOUS";
		if($val_rech!=""){
			// FILTRER LES CARACTERES DE $val_rech?
			switch($mode_rech){
				case "contient":
						$valeur_cherchee="%$val_rech%";
				break;
				case "commence par":
						$valeur_cherchee="$val_rech%";
					break;
				case "se termine par":
						$valeur_cherchee="%$val_rech";
					break;
			}

			switch($crit_rech){
				case "adr":
						$chaine_recherche="((adr1 LIKE '$valeur_cherchee') OR (adr2 LIKE '$valeur_cherchee') OR (adr3 LIKE '$valeur_cherchee') OR (adr4 LIKE '$valeur_cherchee'))";
						$num_resp=1;
					break;
				case "cp":
						$chaine_recherche="cp LIKE '$valeur_cherchee'";
						$num_resp=2;
					break;
				case "commune":
						$chaine_recherche="commune LIKE '$valeur_cherchee'";
					break;
			}
		}
	}




	$sql="SELECT DISTINCT adr1,adr2,adr3,adr4,cp,commune,pays,adr_id FROM resp_adr";
	if(isset($chaine_recherche)){
		$sql.=" WHERE $chaine_recherche";
	}
	//echo " ORDER BY commune,cp,adr1,adr2,adr3,adr4";
	$sql.=" ORDER BY commune,cp,adr1,adr2,adr3,adr4";
	if($limit!='TOUS'){
		$sql.=" LIMIT $debut,$limit";
	}
	//echo "<tr><td colspan='7'>$sql</td></tr>";
	$res_adr=mysql_query($sql);


	$cpt=0;
	unset($tab_adr);
	$tab_adr=array();
	$temoin_adr_actuelle_dans_la_page="non";
	while($lig_adr=mysql_fetch_object($res_adr)){
		$tab_adr[$cpt]=array();
		$tab_adr[$cpt]["adr_id"]=$lig_adr->adr_id;
		$tab_adr[$cpt]["adr1"]=$lig_adr->adr1;
		$tab_adr[$cpt]["adr2"]=$lig_adr->adr2;
		$tab_adr[$cpt]["adr3"]=$lig_adr->adr3;
		$tab_adr[$cpt]["adr4"]=$lig_adr->adr4;
		$tab_adr[$cpt]["cp"]=$lig_adr->cp;
		$tab_adr[$cpt]["commune"]=$lig_adr->commune;
		$tab_adr[$cpt]["pays"]=$lig_adr->pays;

		if(isset($adr_id_actuel)){
			if($adr_id_actuel==$lig_adr->adr_id){
				// L'adresse actuel est dans la partie de la table qui va être affichée
				$temoin_adr_actuelle_dans_la_page="oui";
			}
		}
		$cpt++;
	}


	if(!isset($adr_id_actuel)){
		echo "<tr>\n";
		echo "<td style='text-align:center;'><input type='radio' name='adr_id_existant' value='' ";
		//if(!isset($adr_id_actuel)){
			echo "checked ";
		//}
		echo "onchange='changement();' ";
		echo "/></td>\n";
		echo "<td style='text-align:center; background-color:#FAFABE;' colspan='7'>Ne pas utiliser une adresse existante (<i>ne pas modifier</i>)</td>\n";
		echo "</tr>\n";
	}

	if((isset($adr_id_actuel))&&($temoin_adr_actuelle_dans_la_page=="non")) {
		$sql="SELECT * FROM resp_adr WHERE adr_id='$adr_id_actuel'";
		$res_adr_actuelle=mysql_query($sql);

		if(mysql_num_rows($res_adr_actuelle)!=0){
			$lig_adr_actuelle=mysql_fetch_object($res_adr_actuelle);
			echo "<tr style='background-color:orange;'>\n";
			echo "<td style='text-align:center;'><input type='radio' name='adr_id_existant' value=\"$lig_adr_actuelle->adr_id\" checked ";
			echo "onchange='changement();' ";
			echo "/></td>\n";
			echo "<td style='text-align:center;'>$lig_adr_actuelle->adr_id</td>\n";
			echo "<td style='text-align:center;'>\n";
			if($lig_adr_actuelle->adr1!=""){
				echo $lig_adr_actuelle->adr1;
			}
			if($lig_adr_actuelle->adr2!=""){
				echo "-".$lig_adr_actuelle->adr2;
			}
			if($lig_adr_actuelle->adr3!=""){
				echo "-".$lig_adr_actuelle->adr3;
			}
			if($lig_adr_actuelle->adr4!=""){
				echo "-".$lig_adr_actuelle->adr4;
			}
			echo "</td>\n";
			echo "<td style='text-align:center;'>$lig_adr_actuelle->cp\n";
			echo "</td>\n";
			echo "<td style='text-align:center;'>$lig_adr_actuelle->commune\n";
			echo "</td>\n";
			echo "<td style='text-align:center;'>$lig_adr_actuelle->pays\n";
			echo "</td>\n";

			echo "<td style='text-align:center;'>";
			$sql="SELECT nom,prenom,pers_id FROM resp_pers WHERE adr_id='$lig_adr_actuelle->adr_id'";
			$res_pers=mysql_query($sql);
			if(mysql_num_rows($res_pers)>0){
				$ligtmp=mysql_fetch_object($res_pers);
				$chaine="<a href='modify_resp.php?pers_id=$ligtmp->pers_id' target='_blank'>".mb_strtoupper($ligtmp->nom)." ".ucfirst(mb_strtolower($ligtmp->prenom))."</a>";
				while($ligtmp=mysql_fetch_object($res_pers)){
					$chaine.=",<br />\n<a href='modify_resp.php?pers_id=$ligtmp->pers_id' target='_blank'>".mb_strtoupper($ligtmp->nom)." ".ucfirst(mb_strtolower($ligtmp->prenom))."</a>";
				}
				echo "$chaine";
			}
			echo "</td>\n";
			echo "</tr>\n";
		}
	}

	$alt=1;
	for($i=0;$i<count($tab_adr);$i++){
		//if(($tab_adr[$i]["adr1"]!="")||($tab_adr[$i]["adr2"]!="")||($tab_adr[$i]["adr3"]!="")||($tab_adr[$i]["adr4"]!="")||($tab_adr[$i]["commune"]!="")){
			//echo "<tr>\n";
			//echo "<td style='text-align:center;'><input type='radio' name='adr_id_existant' value=\"".$tab_adr[$i]["adr_id"]."\" ";
			$alt=$alt*(-1);
			//if($i%2==0){$couleur="silver";}else{$couleur="white";}
			if((isset($adr_id_actuel))&&($temoin_adr_actuelle_dans_la_page=="oui")) {
				if($tab_adr[$i]["adr_id"]==$adr_id_actuel){
					echo "<tr style='background-color:orange;'>\n";
					echo "<td style='text-align:center;'><input type='radio' name='adr_id_existant' value=\"".$tab_adr[$i]["adr_id"]."\" ";
					echo "checked ";
					echo "onchange='changement();' ";
					echo "/></td>\n";
				}
				else{
					//echo "<tr style='background-color:$couleur;'>\n";
					echo "<tr class='lig$alt'>\n";
					echo "<td style='text-align:center;'><input type='radio' name='adr_id_existant' value=\"".$tab_adr[$i]["adr_id"]."\" ";
					echo "onchange='changement();' ";
					echo "/></td>\n";
				}
			}
			else{
				//echo "<tr style='background-color:$couleur;'>\n";
				echo "<tr class='lig$alt'>\n";
				echo "<td style='text-align:center;'><input type='radio' name='adr_id_existant' value=\"".$tab_adr[$i]["adr_id"]."\" ";
				echo "onchange='changement();' ";
				echo "/></td>\n";
			}
			//echo "/></td>\n";


			echo "<td style='text-align:center;'>".$tab_adr[$i]["adr_id"]."</td>\n";
			echo "<td style='text-align:center;'>\n";
			if($tab_adr[$i]["adr1"]!=""){
				echo $tab_adr[$i]["adr1"];
			}
			if($tab_adr[$i]["adr2"]!=""){
				echo "-".$tab_adr[$i]["adr2"];
			}
			if($tab_adr[$i]["adr3"]!=""){
				echo "-".$tab_adr[$i]["adr3"];
			}
			if($tab_adr[$i]["adr4"]!=""){
				echo "-".$tab_adr[$i]["adr4"];
			}

			echo "</td>\n";
			echo "<td style='text-align:center;'>".$tab_adr[$i]["cp"]."\n";
			echo "</td>\n";
			echo "<td style='text-align:center;'>".$tab_adr[$i]["commune"]."\n";
			echo "</td>\n";
			echo "<td style='text-align:center;'>".$tab_adr[$i]["pays"]."\n";
			echo "</td>\n";

			echo "<td style='text-align:center;'>";
			$sql="SELECT nom,prenom,pers_id FROM resp_pers WHERE adr_id='".$tab_adr[$i]["adr_id"]."'";
			$res_pers=mysql_query($sql);
			if(mysql_num_rows($res_pers)>0){
				$ligtmp=mysql_fetch_object($res_pers);
				$chaine="<a href='modify_resp.php?pers_id=$ligtmp->pers_id' target='_blank'>".mb_strtoupper($ligtmp->nom)." ".ucfirst(mb_strtolower($ligtmp->prenom))."</a>";
				while($ligtmp=mysql_fetch_object($res_pers)){
					$chaine.=",<br />\n<a href='modify_resp.php?pers_id=$ligtmp->pers_id' target='_blank'>".mb_strtoupper($ligtmp->nom)." ".ucfirst(mb_strtolower($ligtmp->prenom))."</a>";
				}
				echo "$chaine";
			}
			echo "</td>\n";
			echo "</tr>\n";
		//}
	}


	echo "</table>\n";
	echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";
	echo "</div>\n";

	echo "<input type='hidden' name='is_posted' value='choix_adr_existante' />\n";

	echo "</form>\n";
}

?>

<!--font color='red'>A FAIRE: SUPPRESSION d'adresse.</font-->
<?php require("../lib/footer.inc.php");?>
