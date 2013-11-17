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


if(!isset($msg)){
	$msg="";
}

if(isset($suppr_ad)) {
	check_token();

	$temoin_suppr=0;
	for($i=0;$i<count($suppr_ad);$i++){
		$sql="SELECT pers_id FROM resp_pers WHERE adr_id='$suppr_ad[$i]'";
		$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

		if(mysqli_num_rows($test)==0){
			$sql="DELETE FROM resp_adr WHERE adr_id='$suppr_ad[$i]'";
			$res_suppr=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(!$res_suppr){
				$msg.="Erreur lors de la suppression de l'adresse n°$suppr_ad[$i]. ";
				$temoin_suppr++;
			}
		}
		else{
			$msg.="Suppression impossible de l'adresse n°$suppr_ad[$i] associée ";
			$temoin_suppr++;
			if(mysqli_num_rows($test)==1){
				$lig_resp=mysqli_fetch_object($test);
				$msg.="au responsable n°<a href='modify_resp.php?pers_id=".$lig_resp->pers_id."'>".$lig_resp->pers_id."</a>. ";
			}
			else{
				$msg.="aux responsables n°";
				$lig_resp=mysqli_fetch_object($test);
				$msg.="<a href='modify_resp.php?pers_id=".$lig_resp->pers_id."'>".$lig_resp->pers_id."</a>";
				while($lig_resp=mysqli_fetch_object($test)){
					$msg.=", <a href='modify_resp.php?pers_id=".$lig_resp->pers_id."'>".$lig_resp->pers_id."</a>";
				}
			}
		}
	}
	if($temoin_suppr==0){
		$msg="Suppression(s) réussie(s).";
	}
}


if(isset($_GET['suppr_adresses_non_associees'])) {
	check_token();

	$sql="select 1=1 from resp_adr where adr_id not in (select distinct adr_id from resp_pers);";
	$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$nb_scories=mysqli_num_rows($test);
	if($nb_scories==0) {
		$msg.="Toutes les adresses sont associées à des responsables.<br />\n";
	}
	else {
		$msg.="$nb_scories adresses ne sont pas associées à des responsables&nbsp;: ";

		$sql="delete from resp_adr where adr_id not in (select distinct adr_id from resp_pers);";
		$del=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if($del) {$msg.="<span style='color:green'>Nettoyées</span>";}
		else {$msg.="<span style='color:red'>Echec du nettoyage</span>";}
		$msg.="<br />\n";
	}
}

//**************** EN-TETE *******************************
$titre_page = "Gestion des adresses de responsables";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE ***************************

//debug_var();

if(!getSettingValue('conv_new_resp_table')){
	$sql="SELECT 1=1 FROM responsables";
	$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($test)>0){
		echo "<p>Une conversion des données responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SHOW COLUMNS FROM eleves LIKE 'ele_id'";
	$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($test)==0){
		echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		$sql="SELECT 1=1 FROM eleves WHERE ele_id=''";
		$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($test)>0){
			echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";
			echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
}

?>
<p class='bold'><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href="dedoublonnage_adresses.php">Dédoublonner les adresses</a>
<?php
	$sql="select 1=1 from resp_adr where adr_id not in (select distinct adr_id from resp_pers);";
	$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$nb_scories=mysqli_num_rows($test);
	if($nb_scories>0) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?suppr_adresses_non_associees=y".add_token_in_url()."'>Supprimer les adresses non associées</a>";
	}
?>
</p>

<?php
	//debug_var();
	/*
	$sql="SELECT COUNT(adr_id) AS nb_tot_adr_id FROM resp_adr;";
	$res_tot=mysql_query($sql);
	$lig_tot=mysql_fetch_object($res_tot);
	$nb_tot_adr_id=$lig_tot->nb_tot_adr_id;
	*/

	$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : "";
	$critere_recherche=preg_replace("/[^a-zA-ZÀÄÂÉÈÊËÎÏÔÖÙÛÜ½¼Ççàäâéèêëîïôöùûü0-9_ -]/", "", $critere_recherche);
	$afficher_toutes_les_adr=isset($_POST['afficher_toutes_les_adr']) ? $_POST['afficher_toutes_les_adr'] : "n";

	$champ_rech=isset($_POST['champ_rech']) ? $_POST['champ_rech'] : "commune";
	if(($champ_rech!='commune')&&($champ_rech!='cp')&&($champ_rech!='adrX')&&($champ_rech!='non_assoc')) {$champ_rech="commune";}

	$nb_adr=isset($_POST['nb_adr']) ? $_POST['nb_adr'] : 20;
	if(mb_strlen(preg_replace("/[0-9]/","",$nb_adr))!=0) {
		$nb_adr=20;
	}
	$num_premier_adr_rech=isset($_POST['num_premier_adr_rech']) ? $_POST['num_premier_adr_rech'] : 0;
	if((mb_strlen(preg_replace("/[0-9]/","",$num_premier_adr_rech))!=0)||($num_premier_adr_rech=="")) {
		$num_premier_adr_rech=0;
	}



	$sql_tot="SELECT COUNT(resp_adr.adr_id) AS nb_tot_adr_id FROM resp_adr";

	//$sql="SELECT DISTINCT adr1,adr2,adr3,adr4,cp,commune,pays,adr_id FROM resp_adr ORDER BY commune,cp,adr1,adr2,adr3,adr4";
	$sql="SELECT DISTINCT adr1,adr2,adr3,adr4,cp,commune,pays,resp_adr.adr_id FROM resp_adr";
	if($critere_recherche!=""){
		if($champ_rech=='adrX') {
			$sql.=" WHERE (adr1 like '%".$critere_recherche."%' OR adr2 like '%".$critere_recherche."%' OR adr3 like '%".$critere_recherche."%' OR adr4 like '%".$critere_recherche."%')";
			$sql_tot.=" WHERE (adr1 like '%".$critere_recherche."%' OR adr2 like '%".$critere_recherche."%' OR adr3 like '%".$critere_recherche."%' OR adr4 like '%".$critere_recherche."%')";
		}
		elseif($champ_rech=='cp') {
			$sql.=" WHERE (cp like '%".$critere_recherche."%')";
			$sql_tot.=" WHERE (cp like '%".$critere_recherche."%')";
		}
		elseif($champ_rech=='commune') {
			$sql.=" WHERE (commune like '%".$critere_recherche."%')";
			$sql_tot.=" WHERE (commune like '%".$critere_recherche."%')";
		}
	}
	//elseif($champ_rech=='non_assoc') {
	if($champ_rech=='non_assoc') {
/*
	$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom,rp.adr_id,rp.civilite FROM resp_pers rp
		LEFT JOIN responsables2 r ON r.pers_id=rp.pers_id
		WHERE r.pers_id is NULL";
*/
		$sql.=" LEFT JOIN resp_pers ON resp_pers.adr_id=resp_adr.adr_id WHERE resp_pers.adr_id IS NULL";
		$sql_tot.=" LEFT JOIN resp_pers ON resp_pers.adr_id=resp_adr.adr_id WHERE resp_pers.adr_id IS NULL";
	}
	$sql.=" ORDER BY commune,cp,adr1,adr2,adr3,adr4";
	if($afficher_toutes_les_adr!="y") {
		$sql.=" LIMIT $num_premier_adr_rech,$nb_adr;";
	}
	//echo "$sql<br />\n";


	//if($champ_rech!='non_assoc') {
		$res_tot=mysqli_query($GLOBALS["___mysqli_ston"], $sql_tot);
		$lig_tot=mysqli_fetch_object($res_tot);
		$nb_tot_adr_id=$lig_tot->nb_tot_adr_id;
	//}


	echo "<form enctype='multipart/form-data' name='form_rech' action='gerer_adr.php' method='post'>\n";

	echo "<div align='center' style='border:1px solid black;'>\n";
	echo "<table border='0'>\n";
	echo "<tr>\n";
	echo "<td valign='top' colspan='3'>\n";
	echo "<input type='submit' name='filtrage' value='Afficher' /> les ";
	echo "<input type='text' name='nb_adr' id='nb_adr' value='$nb_adr' size='3' />\n";
	echo " premières adresses ";
	echo " à partir de l'enregistrement ";

	echo "<input type='button' name='prec' value='<<' onclick=\"document.getElementById('num_premier_adr_rech').value=Math.max(0,eval(document.getElementById('num_premier_adr_rech').value)-eval(document.getElementById('nb_adr').value));document.form_rech.submit();\" />\n";
	echo "<input type='text' name='num_premier_adr_rech' id='num_premier_adr_rech' value='$num_premier_adr_rech' size='4' />\n";
	echo "<input type='button' name='suiv' value='>>' onclick=\"document.getElementById('num_premier_adr_rech').value=Math.min($nb_tot_adr_id,eval(document.getElementById('num_premier_adr_rech').value)+eval(document.getElementById('nb_adr').value));document.form_rech.submit();\" />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "dont ";
	echo "</td>\n";

	echo "<td valign='top'>\n";
	echo "<input type='radio' name='champ_rech' id='champ_rech_commune' value='commune' ";
	if($champ_rech=="commune") {echo "checked ";}
	echo "/><label for='champ_rech_commune' style='cursor:pointer;'> le champ <b>commune</b></label><br />\n";
	echo "<input type='radio' name='champ_rech' id='champ_rech_cp' value='cp' ";
	if($champ_rech=="cp") {echo "checked ";}
	echo "/><label for='champ_rech_cp' style='cursor:pointer;'> le champ <b>code postal</b></label><br />\n";
	echo "<input type='radio' name='champ_rech' id='champ_rech_adrX' value='adrX' ";
	if($champ_rech=="adrX") {echo "checked ";}
	echo "/><label for='champ_rech_adrX' style='cursor:pointer;'> l'un des champs <b>adrX</b></label>\n";
	echo "</td>\n";

	echo "<td valign='top'>\n";
	echo " contient: ";
	echo "<input type='text' name='critere_recherche' value='$critere_recherche' />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "ou ";
	echo "</td>\n";
	echo "<td valign='top' colspan='2'>\n";
	echo "<input type='radio' name='champ_rech' id='champ_rech_nonAssoc' value='non_assoc' ";
	if($champ_rech=="non_assoc") {echo "checked ";}
	echo "/><label for='champ_rech_nonAssoc' style='cursor:pointer;'> non associées.</label>";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";

	echo "<input type='hidden' name='afficher_toutes_les_adr' id='afficher_toutes_les_adr' value='n' />\n";
	echo "<p align='center'>ou <input type='button' name='afficher_toutes' value='Afficher toutes les adresses' onClick=\"document.getElementById('afficher_toutes_les_adr').value='y'; document.form_rech.submit();\" /></p>\n";

	echo "</div>\n";
	echo "</form>\n";





/*
echo "<form enctype=\"multipart/form-data\" name=\"choix_adr\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
//echo "<center><input type='button' value='Valider' onClick='reporter_valeur()' /></center>\n";

//$sql="SELECT DISTINCT adr1,adr2,adr3,adr4,cp,commune,pays,adr_id FROM resp_adr ORDER BY commune,cp,adr1,adr2,adr3,adr4";
$sql="SELECT DISTINCT adr1,adr2,adr3,adr4,cp,commune,pays,adr_id FROM resp_adr";
if($critere_recherche!=""){
	if($champ_rech=='adrX') {
		$sql.=" WHERE (adr1 like '%".$critere_recherche."%' OR adr2 like '%".$critere_recherche."%' OR adr3 like '%".$critere_recherche."%' OR adr4 like '%".$critere_recherche."%')";
	}
	elseif($champ_rech=='cp') {
		$sql.=" WHERE (cp like '%".$critere_recherche."%')";
	}
	elseif($champ_rech=='commune') {
		$sql.=" WHERE (commune like '%".$critere_recherche."%')";
	}
}
$sql.=" ORDER BY commune,cp,adr1,adr2,adr3,adr4";
if($afficher_toutes_les_adr!="y") {
	$sql.=" LIMIT $num_premier_adr_rech,$nb_adr;";
}
echo "$sql<br />\n";
*/

$res_adr=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if(mysqli_num_rows($res_adr)>0){
	//echo "<b>ou</b> <input type='checkbox' name='select_ad_existante' id='select_ad_existante' value='y' onchange='modif_div_ad()' /> Sélectionner une adresse existante.";

	echo "<form enctype=\"multipart/form-data\" name=\"choix_adr\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo add_token_field();
	echo "<input type='hidden' name='nb_adr' value='$nb_adr' />\n";
	echo "<input type='hidden' name='num_premier_adr_rech' value='$num_premier_adr_rech' />\n";
	echo "<input type='hidden' name='champ_rech' value='$champ_rech' />\n";
	echo "<input type='hidden' name='critere_recherche' value='$critere_recherche' />\n";
	echo "<input type='hidden' name='afficher_toutes_les_adr' value='$afficher_toutes_les_adr' />\n";

	echo "<div id='div_ad_existante'>\n";

	echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";

	echo "<p align='center'><a href=\"javascript:modif_suppr_adr_id_non_assoc('coche')\">Cocher</a>\n";
	echo " / ";
	echo "<a href=\"javascript:modif_suppr_adr_id_non_assoc('decoche')\">décocher</a>\n";
	echo " les adresses non associées.</p>";


	// Ajouter un lien pour cocher les adresses non associées.
	unset($tab_adr_id_non_assoc);
	$tab_adr_id_non_assoc=array();


	//echo "<table border='1'>\n";
	echo "<table class='boireaus'>\n";

	$ligne_titre="<tr>\n";
	//$ligne_titre.="<td style='text-align:center; font-weight:bold;'>&nbsp;</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Identifiant</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Lignes de l'adresse</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Code postal</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Commune</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Pays</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:#96C8F0;'>Responsable associé</td>\n";
	$ligne_titre.="<td style='text-align:center; font-weight:bold; background-color:red;'>Supprimer adresse(s)<br />\n";
	$ligne_titre.="<a href=\"javascript:modif_case(true)\">";
	$ligne_titre.="<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
	$ligne_titre.=" / ";
	$ligne_titre.="<a href=\"javascript:modif_case(false)\">";
	$ligne_titre.="<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
	$ligne_titre.="</td>\n";
	$ligne_titre.="</tr>\n";

	/*
	echo "<tr>\n";
	echo "<td style='text-align:center;'><input type='radio' name='adr_id_existant' value='' checked /></td>\n";
	echo "<td style='text-align:center; background-color:#FAFABE;' colspan='7'>Ne pas utiliser une adresse existante</td>\n";
	echo "</tr>\n";
	*/


	$cpt=0;
	$alt=1;
	while($lig_adr=mysqli_fetch_object($res_adr)){
		//if(($lig_adr->adr1!="")||($lig_adr->adr2!="")||($lig_adr->adr3!="")||($lig_adr->adr4!="")||($lig_adr->commune!="")){

			if($cpt%10==0){
				echo $ligne_titre;
			}

			/*
			if($cpt%2==0){
				$alt='silver';
			}
			else{
				$alt='white';
			}
			*/
			$alt=$alt*(-1);

			//echo "<tr style='background-color:".$alt.";'>\n";
			echo "<tr class='lig$alt'>\n";

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
			$res_pers=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($res_pers)>0){
				$ligtmp=mysqli_fetch_object($res_pers);
				$chaine="<a href='modify_resp.php?pers_id=$ligtmp->pers_id'>".mb_strtoupper($ligtmp->nom)." ".ucfirst(mb_strtolower($ligtmp->prenom))."</a>";
				while($ligtmp=mysqli_fetch_object($res_pers)){
					$chaine.=",<br />\n<a href='modify_resp.php?pers_id=$ligtmp->pers_id'>".mb_strtoupper($ligtmp->nom)." ".ucfirst(mb_strtolower($ligtmp->prenom))."</a>";
				}
				echo "$chaine";
			}
			else{
				$tab_adr_id_non_assoc[]=$cpt;
			}
			echo "</td>\n";

			echo "<td style='text-align:center;'><input type='checkbox' name='suppr_ad[]' id='suppr_$cpt' value='$lig_adr->adr_id' /></td>\n";
			echo "</tr>\n";
			$cpt++;
		//}
	}

	echo "</table>\n";
	echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";
	//echo "<center><input type='button' value='Valider' onClick='reporter_valeur()' /></center>\n";
	echo "</div>\n";


		echo "<script type='text/javascript'>

	function modif_case(mode) {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=mode;
			}
		}
	}

	tab_non_assoc=new Array();\n";

		for($i=0;$i<count($tab_adr_id_non_assoc);$i++){
			echo "tab_non_assoc[$i]=".$tab_adr_id_non_assoc[$i]."\n";
		}

		echo "	function modif_suppr_adr_id_non_assoc(mode){
		for(i=0;i<tab_non_assoc.length;i++){
			if(document.getElementById('suppr_'+tab_non_assoc[i])){
				if(mode=='coche'){
					document.getElementById('suppr_'+tab_non_assoc[i]).checked=true;
				}
				else{
					document.getElementById('suppr_'+tab_non_assoc[i]).checked=false;
				}
			}
		}
	}
</script>\n";



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
else{
	echo "<p>Aucune adresse dans la table 'resp_adr'";
	if($critere_recherche!="") {echo " pour la recherche";}
	echo ".</p>\n";
}

echo "<input type='hidden' name='is_posted' value='1' />\n";
?>
</form>

<!--font color='red'>A FAIRE: SUPPRESSION d'adresse.</font-->
<?php
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
