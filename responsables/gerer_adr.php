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


if(!isset($msg)){
	$msg="";
}

if(isset($suppr_ad)){
	$temoin_suppr=0;
	for($i=0;$i<count($suppr_ad);$i++){
		$sql="SELECT pers_id FROM resp_pers WHERE adr_id='$suppr_ad[$i]'";
		$test=mysql_query($sql);

		if(mysql_num_rows($test)==0){
			$sql="DELETE FROM resp_adr WHERE adr_id='$suppr_ad[$i]'";
			$res_suppr=mysql_query($sql);
			if(!$res_suppr){
				$msg.="Erreur lors de la suppression de l'adresse n°$suppr_ad[$i]. ";
				$temoin_suppr++;
			}
		}
		else{
			$msg.="Suppression impossible de l'adresse n°$suppr_ad[$i] associée ";
			$temoin_suppr++;
			if(mysql_num_rows($test)==1){
				$lig_resp=mysql_fetch_object($test);
				$msg.="au responsable n°<a href='modify_resp.php?pers_id=".$lig_resp->pers_id."'>".$lig_resp->pers_id."</a>. ";
			}
			else{
				$msg.="aux responsables n°";
				$lig_resp=mysql_fetch_object($test);
				$msg.="<a href='modify_resp.php?pers_id=".$lig_resp->pers_id."'>".$lig_resp->pers_id."</a>";
				while($lig_resp=mysql_fetch_object($test)){
					$msg.=", <a href='modify_resp.php?pers_id=".$lig_resp->pers_id."'>".$lig_resp->pers_id."</a>";
				}
			}
		}
	}
	if($temoin_suppr==0){
		$msg="Suppression(s) réussie(s).";
	}
}

//**************** EN-TETE *******************************
$titre_page = "Gestion des adresses de responsables";
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
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<?php
echo "<form enctype=\"multipart/form-data\" name=\"choix_adr\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
//echo "<center><input type='button' value='Valider' onClick='reporter_valeur()' /></center>\n";

$sql="SELECT DISTINCT adr1,adr2,adr3,adr4,cp,commune,pays,adr_id FROM resp_adr ORDER BY commune,cp,adr1,adr2,adr3,adr4";
$res_adr=mysql_query($sql);
if(mysql_num_rows($res_adr)>0){
	//echo "<b>ou</b> <input type='checkbox' name='select_ad_existante' id='select_ad_existante' value='y' onchange='modif_div_ad()' /> Sélectionner une adresse existante.";

	echo "<div id='div_ad_existante'>\n";
	echo "<table border='1'>\n";

	$ligne_titre="<tr>\n";
	//$ligne_titre.="<td style='text-align:center; font-weight:bold;'>&nbsp;</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Identifiant</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Lignes de l'adresse</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Code postal</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Commune</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Pays</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:#96C8F0;'>Responsable associé</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:red;'>Supprimer adresse(s)</td>\n";
	$ligne_titre.="</tr>\n";

	/*
	echo "<tr>\n";
	echo "<td style='text-align:center;'><input type='radio' name='adr_id_existant' value='' checked /></td>\n";
	echo "<td style='text-align:center; background-color:#FAFABE;' colspan='7'>Ne pas utiliser une adresse existante</td>\n";
	echo "</tr>\n";
	*/

	$cpt=0;
	while($lig_adr=mysql_fetch_object($res_adr)){
		if(($lig_adr->adr1!="")||($lig_adr->adr2!="")||($lig_adr->adr3!="")||($lig_adr->adr4!="")||($lig_adr->commune!="")){

			if($cpt%10==0){
				echo $ligne_titre;
			}

			if($cpt%2==0){
				$alt='silver';
			}
			else{
				$alt='white';
			}

			echo "<tr style='background-color:".$alt.";'>\n";

			//echo "<td style='text-align:center;'><input type='radio' name='adr_id_existant' value=\"$lig_adr->adr_id\" /></td>\n";
			echo "<td style='text-align:center;'>$lig_adr->adr_id</td>\n";
			echo "<td style='text-align:center;'>\n";
			$chaine_adr='';
			//$adr1='';
			if($lig_adr->adr1!=""){
				//echo $lig_adr->adr1;
				//$adr1=$lig_adr->adr1;
				$chaine_adr.=$lig_adr->adr1;
			}
			//echo "<input type='hidden' name='adr1_$cpt' id='id_adr1_$cpt' value=\"$adr1\" />\n";

			//$adr2='';
			if($lig_adr->adr2!=""){
				//echo "-".$lig_adr->adr2;
				//$adr2=$lig_adr->adr2;
				$chaine_adr.="-".$lig_adr->adr2;
			}
			//echo "<input type='hidden' name='adr2_$cpt' id='id_adr2_$cpt' value=\"$adr2\" />\n";
			//if($lig_adr->adr3!=""){echo "-".$lig_adr->adr3;$chaine_adr.="-".$lig_adr->adr3;}
			//if($lig_adr->adr4!=""){echo "-".$lig_adr->adr4;$chaine_adr.="-".$lig_adr->adr4;}

			//$adr3='';
			if($lig_adr->adr3!=""){
				//echo "-".$lig_adr->adr3;
				//$adr3=$lig_adr->adr3;
				$chaine_adr.="-".$lig_adr->adr3;
			}
			//echo "<input type='hidden' name='adr3_$cpt' id='id_adr3_$cpt' value=\"$adr3\" />\n";

			//$adr4='';
			if($lig_adr->adr4!=""){
				//echo "-".$lig_adr->adr4;
				//$adr4=$lig_adr->adr4;
				$chaine_adr.="-".$lig_adr->adr4;
			}
			//echo "<input type='hidden' name='adr4_$cpt' id='id_adr4_$cpt' value=\"$adr4\" />\n";

			if($chaine_adr==''){echo "&nbsp;";}else{echo $chaine_adr;}

			//echo "<input type='hidden' name='adr_$cpt' id='id_adr_$cpt' value='$chaine_adr' />\n";
			echo "</td>\n";
			echo "<td style='text-align:center;'>\n";
			if($lig_adr->cp==''){echo "&nbsp;";}else{echo $lig_adr->cp;}
			//echo "<input type='hidden' name='cp_$cpt' id='id_cp_$cpt' value='$lig_adr->cp' />\n";
			echo "</td>\n";
			echo "<td style='text-align:center;'>\n";
			if($lig_adr->commune==''){echo "&nbsp;";}else{echo $lig_adr->commune;}
			//echo "<input type='hidden' name='commune_$cpt' id='id_commune_$cpt' value='$lig_adr->commune' />\n";
			echo "</td>\n";
			echo "<td style='text-align:center;'>\n";
			if($lig_adr->pays==''){echo "&nbsp;";}else{echo $lig_adr->pays;}
			//echo "<input type='hidden' name='pays_$cpt' id='id_pays_$cpt' value='$lig_adr->pays' />\n";
			echo "</td>\n";

			echo "<td style='text-align:center;'>";
			$sql="SELECT nom,prenom,pers_id FROM resp_pers WHERE adr_id='$lig_adr->adr_id'";
			$res_pers=mysql_query($sql);
			if(mysql_num_rows($res_pers)>0){
				$ligtmp=mysql_fetch_object($res_pers);
				//$chaine="<a href='modify_resp.php?pers_id=$pers_id'>".strtoupper($ligtmp->nom)." ".ucfirst(strtolower($ligtmp->prenom))."</a>";
				$chaine="<a href='modify_resp.php?pers_id=$ligtmp->pers_id'>".strtoupper($ligtmp->nom)." ".ucfirst(strtolower($ligtmp->prenom))."</a>";
				while($ligtmp=mysql_fetch_object($res_pers)){
					//$chaine.=",<br />\n<a href='modify_resp.php?pers_id=$pers_id'>".strtoupper($ligtmp->nom)." ".ucfirst(strtolower($ligtmp->prenom))."</a>";
					$chaine.=",<br />\n<a href='modify_resp.php?pers_id=$ligtmp->pers_id'>".strtoupper($ligtmp->nom)." ".ucfirst(strtolower($ligtmp->prenom))."</a>";
				}
				echo "$chaine";
			}
			echo "</td>\n";

			echo "<td style='text-align:center;'><input type='checkbox' name='suppr_ad[]' value='$lig_adr->adr_id' /></td>\n";
			echo "</tr>\n";
			$cpt++;
		}
	}

	echo "</table>\n";
	echo "<center><input type='submit' value='Enregistrer' /></center>\n";
	//echo "<center><input type='button' value='Valider' onClick='reporter_valeur()' /></center>\n";
	echo "</div>\n";

/*
	echo "<script type='text/javascript'>

	//function modif_div_ad(mode){
	//	if(mode=='cacher'){

	function reporter_valeur(){
		for (i=0;i<$cpt;i++) {
			if (document.forms.choix_adr.adr_id_existant[i].checked==true) {
				//alert('C est le choix '+Number(i+1)+' qui est sélectionné');
				adr_id=document.forms.choix_adr.adr_id_existant[i].value;
				break;
			}
		}

		//adresse=eval('document.forms.choix_adr.adr_'+i+'.value');
		//adresse=document.getElementById('adr_'+i).value;
		tmp='id_adr1_'+i;
		//alert('tmp='+tmp);
		adr1=document.getElementById(tmp).value;
		alert(\"adr1=\"+adr1);

		tmp='id_adr2_'+i;
		adr2=document.getElementById(tmp).value;

		tmp='id_adr3_'+i;
		adr3=document.getElementById(tmp).value;

		tmp='id_adr4_'+i;
		adr4=document.getElementById(tmp).value;

		tmp='id_commune_'+i;
		commune=document.getElementById(tmp).value;

		tmp='id_cp_'+i;
		cp=document.getElementById(tmp).value;

		tmp='id_pays_'+i;
		pays=document.getElementById(tmp).value;

		//alert('adr_id='+adr_id+' et i='+i+' et adr1='+adr1);

		//window.opener.document.forms.resp.elements['adr_choisie'].value=v;
		window.opener.document.forms.resp.elements['adr_id_existant'].value=adr_id;
		window.opener.document.forms.resp.elements['adr1'].value=adr1;
		window.opener.document.forms.resp.elements['adr2'].value=adr2;
		window.opener.document.forms.resp.elements['adr3'].value=adr3;
		window.opener.document.forms.resp.elements['adr4'].value=adr4;
		window.opener.document.forms.resp.elements['commune'].value=commune;
		window.opener.document.forms.resp.elements['cp'].value=cp;
		window.opener.document.forms.resp.elements['pays'].value=pays;

		// Je ne parviens pas à modifier l'adr_id affiché dans la page modify_resp.php
		//window.opener.document.getElementById['num_adr_id'].innerHTML='MODIF';
		//tmp=window.opener.document.getElementById['num_adr_id'];
		//tmp.innerHTML='MODIF';

		window.close();
	}

	function modif_div_ad(){
		//alert('Changement');
		//if(document.getElementById('select_ad_existante').checked=='true'){
		if(document.getElementById('select_ad_existante').checked){
			document.getElementById('div_saisie_ad').style.display='none';
			document.getElementById('div_ad_existante').style.display='';
		}
		else{
			document.getElementById('div_saisie_ad').style.display='';
			document.getElementById('div_ad_existante').style.display='none';
		}
	}

	// Initialisation:
	//document.getElementById('div_ad_existante').style.display='none';
</script>\n";
*/
}

echo "<input type='hidden' name='is_posted' value='1' />\n";
?>
</form>

<!--font color='red'>A FAIRE: SUPPRESSION d'adresse.</font-->
<?php require("../lib/footer.inc.php");?>
