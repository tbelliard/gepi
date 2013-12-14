<?php
/*
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// INSERT INTO `droits` VALUES ('/responsables/dedoublonnage_adresses.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Dédoublonnage des adresses responsables', '');

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


function affiche_debug($texte){
	// Passer à 1 la variable pour générer l'affichage des infos de debug...
	$debug=0;
	if($debug==1){
		echo "<font color='green'>".$texte."</font>";
		flush();
	}
}

function info_debug($texte){
	global $step;
	global $dirname;

	$debug=0;
	if($debug==1) {
		//$fich_debug=fopen("/tmp/debug_maj_import2.txt","a+");
		$fich_debug=fopen("../backup/".$dirname."/debug_maj_import2.txt","a+");
		fwrite($fich_debug,"$step;$texte;".time()."\n");
		fclose($fich_debug);
	}
}


// Etape...
$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

$parcours_diff=isset($_POST['parcours_diff']) ? $_POST['parcours_diff'] : NULL;

$nb_parcours=isset($_POST['nb_parcours']) ? $_POST['nb_parcours'] : NULL;

$stop=isset($_POST['stop']) ? $_POST['stop'] : (isset($_GET['stop']) ? $_GET['stop'] :'n');

//$style_specifique="responsables/maj_import2";

//$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";

//**************** EN-TETE *****************
$titre_page = "Dédoublonnage des adresses responsables";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();


if(isset($step)) {
	check_token(false);

	echo "<div style='float: right; border: 1px solid black; width: 4em;'>
<form name='formstop' action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='checkbox' name='stop' id='stop' value='y' onchange='stop_change()' ";
//if(isset($stop)){
	if($stop=='y'){
		echo "checked ";
	}
	echo "/> <a href='#' onmouseover=\"afficher_div('div_stop','y',10,20);\">Stop</a>
</form>\n";
	echo "</div>\n";

	echo creer_div_infobulle("div_stop","","","Ce bouton permet s'il est coché d'interrompre les passages automatiques à la page suivante","",12,0,"n","n","y","n");

	echo "<script type='text/javascript'>
	temporisation_chargement='ok';
	cacher_div('div_stop');
</script>\n";


	echo "<script type='text/javascript'>
function stop_change(){
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(document.getElementById('id_form_stop')){
		document.getElementById('id_form_stop').value=stop;
	}
}

function test_stop(num){
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	//document.getElementById('id_form_stop').value=stop;
	if(stop=='n'){
		//setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=1')\",2000);
		document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'".add_token_in_url(false)."'";

	// AJOUT A FAIRE VALEUR STOP
	echo "+'&stop='+stop";

	echo ");
	}
}

function test_stop2(){
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	document.getElementById('id_form_stop').value=stop;
	if(stop=='n'){
		//setTimeout(\"document.forms['formulaire'].submit();\",1000);
		document.forms['formulaire'].submit();
	}
}





function test_stop_suite(num){
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}

	document.location.replace('".$_SERVER['PHP_SELF']."?step='+num";
	// AJOUT A FAIRE VALEUR STOP
	echo "+'&stop='+stop+'";

	echo add_token_in_url(false);

	echo "');
}

</script>\n";

}


echo "<p class='bold'>\n";
echo "<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
//echo "</p>\n";


if(!isset($step)) {
	echo "</p>\n";

	echo "<h2>Dédoublonnage des adresses de responsables</h2>\n";

	echo "<p>Cette page est destinée à effectuer le dédoublonnage d'adresses considérées à tort par Sconet comme des adresses différentes alors qu'elles sont identiques.</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

	echo add_token_field();

	//echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo "<input type=hidden name='step' value='0' />\n";
	//==============================
	// AJOUT pour tenir compte de l'automatisation ou non:
	//echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
	echo "<input type='checkbox' name='stop' id='id_form_stop' value='y' /><label for='id_form_stop' style='cursor: pointer;'> Désactiver le mode automatique.</label></p>\n";
	//==============================

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";
}
else{
	echo "</p>\n";

	// Affichage des informations élèves
	echo "<h2>Dédoublonnage des adresses de responsables</h2>\n";

	if(!isset($parcours_diff)){

		echo "<p>Initialisation du processus.</p>\n";

		$sql="TRUNCATE TABLE tempo2;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);

		$sql="INSERT INTO tempo2 SELECT pers_id,adr_id FROM resp_pers;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);

		$sql="SELECT 1=1 FROM tempo2;";
		$res1=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_resp=mysqli_num_rows($res1);
		if($nb_resp==0){
			echo "<p>La table 'tempo2' est vide???<br />Aucun responsable ne serait encore défini?</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<p>Les ".$nb_resp." responsables vont être parcourus par tranches de 20 à la recherche de différences.</p>\n";

		$nb_parcours=ceil($nb_resp/20);

		$parcours_diff=0;
		echo "<p>Parcours de la tranche <b>$parcours_diff</b>.</p>\n";
	}
	else{
		echo "<p>Parcours de la tranche <b>$parcours_diff/$nb_parcours</b>.</p>\n";
	}

	flush();

	echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
	echo add_token_field();
	//==============================
	// AJOUT pour tenir compte de l'automatisation ou non:
	echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
	//==============================

	$sql="SELECT * FROM tempo2 LIMIT 20;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Dédoublonnage achevé.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	//echo "<p>";
	$cpt=0;
	while($lig=mysqli_fetch_object($res)) {
		$pers_id=$lig->col1;
		//$adr_id=$lig->col2;
		//$sql="SELECT adr_id FROM resp_pers WHERE pers_id='$pers_id';";
		$sql="SELECT adr_id, nom, prenom FROM resp_pers WHERE pers_id='$pers_id';";
		$res1=mysqli_query($GLOBALS["mysqli"], $sql);
		$lig1=mysqli_fetch_object($res1);
		$adr_id=$lig1->adr_id;

		//echo "<p>\$pers_id=$pers_id (adr_id=$adr_id) ";

		$sql="SELECT * FROM resp_adr WHERE adr_id='$adr_id';";
		$res2=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res2)>0) {
			$lig2=mysqli_fetch_object($res2);
			if((($lig2->adr1!="")||($lig2->adr2!="")||($lig2->adr3!="")||($lig2->adr4!=""))&&
				($lig2->commune!="")) {
				//$sql="SELECT adr_id FROM resp_adr WHERE adr_id!='$adr_id' AND adr1='$lig2->adr1' AND adr2='$lig2->adr2' AND adr3='$lig2->adr3' AND adr4='$lig2->adr4' AND cp='$lig2->cp' AND commune='$lig2->commune' AND pays='$lig2->pays';";
				$sql="SELECT ra.adr_id, rp.pers_id, rp.nom, rp.prenom FROM resp_adr ra, resp_pers rp
					WHERE ra.adr_id!='$adr_id' AND ra.adr1='".addslashes($lig2->adr1)."' AND ra.adr2='".addslashes($lig2->adr2)."' AND ra.adr3='".addslashes($lig2->adr3)."' AND ra.adr4='".addslashes($lig2->adr4)."' AND ra.cp='".addslashes($lig2->cp)."' AND ra.commune='".addslashes($lig2->commune)."' AND ra.pays='".addslashes($lig2->pays)."' AND ra.adr_id=rp.adr_id;";
				//echo "<br />$sql<br />";
				$res3=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res3)>0) {
					while($lig3=mysqli_fetch_object($res3)) {
						$temoin="n";

						$tab_ele1=array();
						// On vérifie si les deux responsables sont bien liés via responsables2
						$sql="SELECT ele_id FROM responsables2 WHERE pers_id='$pers_id' AND (resp_legal='1' OR resp_legal='2');";
						$res_ele1=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_ele1)>0) {
							while($lig_ele1=mysqli_fetch_object($res_ele1)) {
								$tab_ele1[]=$lig_ele1->ele_id;
							}
						}

						//$tab_ele2=array();
						// On vérifie si les deux responsables sont bien liés via responsables2
						$sql="SELECT ele_id FROM responsables2 WHERE pers_id='$lig3->pers_id' AND (resp_legal='1' OR resp_legal='2');";
						$res_ele2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_ele2)>0) {
							while($lig_ele2=mysqli_fetch_object($res_ele2)) {
								//$tab_ele2[]=$lig_ele2->ele_id;

								if(in_array($lig_ele2->ele_id,$tab_ele1)) {
									$temoin="y";
									break;
								}
							}
						}

						if($temoin=="y") {
							$sql="UPDATE resp_pers SET adr_id='$adr_id' WHERE pers_id='$lig3->pers_id';";
							//echo "<br />$sql<br />";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);

							/*
							$sql="UPDATE tempo2 SET col2='$adr_id' WHERE col1='$lig3->pers_id';";
							//echo "<br />$sql<br />";
							$update=mysql_query($sql);
							*/

							//echo " <span style='color:red'>$lig3->pers_id</span>";
							if($cpt==0) {echo "<p><b>Dédoublonnage pour:</b> ";} else {echo " - ";}

							echo mb_strtoupper($lig1->nom)." ".ucfirst(mb_strtolower($lig1->prenom))." (<i>".mb_strtoupper($lig3->nom)." ".ucfirst(mb_strtolower($lig3->prenom))."</i>)";

							$cpt++;
						}

					}
				}
			}
		}

		//$sql="DELETE FROM tempo2 WHERE col1='$pers_id' AND col2='$adr_id';";
		$sql="DELETE FROM tempo2 WHERE col1='$pers_id';";
		$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);
	}
	if($cpt>0) {echo "</p>\n";}

	echo "<input type='hidden' name='nb_parcours' value='$nb_parcours' />\n";

	//if(!isset($parcours_diff)){$parcours_diff=1;}
	$parcours_diff++;
	//echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";

	//if(count($tab_ele_id)>20){
		echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";

		echo "<input type='hidden' name='step' value='1' />\n";
		echo "<p><input type='submit' value='Suite' /></p>\n";

	echo "</form>\n";

	echo "<script type='text/javascript'>
	//setTimeout(\"test_stop('1')\",3000);
	setTimeout(\"test_stop2()\",3000);
</script>\n";

}


echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
