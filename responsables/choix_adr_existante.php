<?php
/*
 * Last modification  : 14/04/2006
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


if(!isset($pers_id)){
    header("Location: index.php");
    die();
}

if(isset($is_posted)){
	if(!isset($msg)){
		$msg="";
	}

	if($is_posted=="choix_adr_existante"){
		if($adr_id_existant==''){
			header("Location: modify_resp.php?pers_id=$pers_id");
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
					header("Location: modify_resp.php?pers_id=$pers_id");
					die();
				}
			}
		}
	}
}

//**************** EN-TETE *******************************
$titre_page = "Choisir une adresse responsable";
require_once("../lib/header.inc");
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

?>
<p class='bold'><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<?php
$sql="SELECT DISTINCT adr1,adr2,adr3,adr4,cp,commune,pays,adr_id FROM resp_adr ORDER BY commune,cp,adr1,adr2,adr3,adr4";
$res_adr=mysql_query($sql);
if(mysql_num_rows($res_adr)==0){
	echo "<p>Aucune adresse n'est encore définie.<p>\n";
}
else{
	$sql="SELECT nom,prenom FROM resp_pers WHERE pers_id='$pers_id'";
	$res_info_pers=mysql_query($sql);
	$lig_pers=mysql_fetch_object($res_info_pers);

	echo "<p>Choix d'une adresse pour $lig_pers->nom $lig_pers->prenom (<i>$pers_id</i>)</p>\n";

	echo "<form enctype=\"multipart/form-data\" name=\"param_liste\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

	echo "<p align='center'>";
	if(!isset($debut)){
		$debut=0;
	}
	else{
		if(strlen(ereg_replace("[0-9]","",$debut))){
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
	for($i=100;$i<=500;$i+=100){
		if($limit==$i){$selected=" selected='true'";}else{$selected="";}
		echo "<option value='$i'$selected>$i</option>\n";
	}
	echo "<option value='TOUS'>TOUS</option>\n";
	echo "</select> enregistrements à partir de l'enregistrement n°\n";
	echo "<input type='text' name='debut' value='$debut' size='5' /> \n";

	$cpt=mysql_num_rows($res_adr);

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
	echo "</form>\n";





	echo "<form enctype=\"multipart/form-data\" name=\"choix_adr\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

	echo "<center><input type='submit' value='Valider' /></center>\n";

	echo "<input type='hidden' name='pers_id' value='$pers_id' />\n";

	echo "<div id='div_ad_existante'>\n";
	echo "<table border='1'>\n";
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

	echo "<tr>\n";
	echo "<td style='text-align:center;'><input type='radio' name='adr_id_existant' value='' checked /></td>\n";
	echo "<td style='text-align:center; background-color:#FAFABE;' colspan='7'>Ne pas utiliser une adresse existante</td>\n";
	echo "</tr>\n";


	$sql="SELECT DISTINCT adr1,adr2,adr3,adr4,cp,commune,pays,adr_id FROM resp_adr ORDER BY commune,cp,adr1,adr2,adr3,adr4";
	if($limit!='TOUS'){
		$sql.=" LIMIT $debut,$limit";
	}
	echo "<tr><td colspan='7'>$sql</td></tr>";
	$res_adr=mysql_query($sql);


	$cpt=0;
	while($lig_adr=mysql_fetch_object($res_adr)){
		if(($lig_adr->adr1!="")||($lig_adr->adr2!="")||($lig_adr->adr3!="")||($lig_adr->adr4!="")||($lig_adr->commune!="")){
			echo "<tr>\n";
			echo "<td style='text-align:center;'><input type='radio' name='adr_id_existant' value=\"$lig_adr->adr_id\" /></td>\n";
			echo "<td style='text-align:center;'>$lig_adr->adr_id</td>\n";
			echo "<td style='text-align:center;'>\n";
			//$chaine_adr='';
			//$adr1='';
			if($lig_adr->adr1!=""){
				echo $lig_adr->adr1;
				//$adr1=$lig_adr->adr1;
			}
			//echo "<input type='hidden' name='adr1_$cpt' id='id_adr1_$cpt' value=\"$adr1\" />\n";

			//$adr2='';
			if($lig_adr->adr2!=""){
				echo "-".$lig_adr->adr2;
				//$adr2=$lig_adr->adr2;
			}
			//echo "<input type='hidden' name='adr2_$cpt' id='id_adr2_$cpt' value=\"$adr2\" />\n";

			//$adr3='';
			if($lig_adr->adr3!=""){
				echo "-".$lig_adr->adr3;
				//$adr3=$lig_adr->adr3;
			}
			//echo "<input type='hidden' name='adr3_$cpt' id='id_adr3_$cpt' value=\"$adr3\" />\n";

			//$adr4='';
			if($lig_adr->adr4!=""){
				echo "-".$lig_adr->adr4;
				//$adr4=$lig_adr->adr4;
			}
			//echo "<input type='hidden' name='adr4_$cpt' id='id_adr4_$cpt' value=\"$adr4\" />\n";

			echo "</td>\n";
			echo "<td style='text-align:center;'>$lig_adr->cp\n";
			//echo "<input type='hidden' name='cp_$cpt' id='id_cp_$cpt' value='$lig_adr->cp' />\n";
			echo "</td>\n";
			echo "<td style='text-align:center;'>$lig_adr->commune\n";
			//echo "<input type='hidden' name='commune_$cpt' id='id_commune_$cpt' value='$lig_adr->commune' />\n";
			echo "</td>\n";
			echo "<td style='text-align:center;'>$lig_adr->pays\n";
			//echo "<input type='hidden' name='pays_$cpt' id='id_pays_$cpt' value='$lig_adr->pays' />\n";
			echo "</td>\n";

			echo "<td style='text-align:center;'>";
			$sql="SELECT nom,prenom,pers_id FROM resp_pers WHERE adr_id='$lig_adr->adr_id'";
			$res_pers=mysql_query($sql);
			if(mysql_num_rows($res_pers)>0){
				$ligtmp=mysql_fetch_object($res_pers);
				//$chaine="<a href='modify_resp.php?pers_id=$pers_id'>".strtoupper($ligtmp->nom)." ".ucfirst(strtolower($ligtmp->prenom))."</a>";
				$chaine="<a href='modify_resp.php?pers_id=$ligtmp->pers_id' target='_blank'>".strtoupper($ligtmp->nom)." ".ucfirst(strtolower($ligtmp->prenom))."</a>";
				while($ligtmp=mysql_fetch_object($res_pers)){
					//$chaine.=",<br />\n<a href='modify_resp.php?pers_id=$pers_id'>".strtoupper($ligtmp->nom)." ".ucfirst(strtolower($ligtmp->prenom))."</a>";
					$chaine.=",<br />\n<a href='modify_resp.php?pers_id=$ligtmp->pers_id' target='_blank'>".strtoupper($ligtmp->nom)." ".ucfirst(strtolower($ligtmp->prenom))."</a>";
				}
				echo "$chaine";
			}
			echo "</td>\n";

			//echo "<td style='text-align:center;'><input type='checkbox' name='suppr_ad[]' value='$lig_adr->adr_id' /></td>\n";
			echo "</tr>\n";
			$cpt++;
		}
	}

	echo "</table>\n";
	//echo "<center><input type='submit' value='Enregistrer' /></center>\n";
	//echo "<center><input type='button' value='Valider' onClick='reporter_valeur()' /></center>\n";
	echo "<center><input type='submit' value='Valider' /></center>\n";
	echo "</div>\n";

	echo "<input type='hidden' name='is_posted' value='choix_adr_existante' />\n";

	echo "</form>\n";
}

?>

<!--font color='red'>A FAIRE: SUPPRESSION d'adresse.</font-->
<?php require("../lib/footer.inc.php");?>
