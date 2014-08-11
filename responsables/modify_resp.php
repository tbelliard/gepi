<?php
/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

/*
echo "\$is_posted=$is_posted<br />";
echo "\$_POST[is_posted]=".$_POST['is_posted']."<br />";
echo "\$_GET[is_posted]=".$_GET['is_posted']."<br />";
*/

if((isset($_GET['acces_resp_legal_0']))&&(($_GET['acces_resp_legal_0']=='y')||($_GET['acces_resp_legal_0']=='n'))) {
	check_token();

	$sql="UPDATE responsables2 SET acces_sp='".$_GET['acces_resp_legal_0']."' WHERE pers_id='".$_GET['pers_id']."' AND ele_id='".$_GET['ele_id']."';";
	$update=mysqli_query($GLOBALS["mysqli"], $sql);
	if($update) {
		$msg="Modification de l'accès aux données pour pers_id=".$_GET['pers_id']." et ele_id=".$_GET['ele_id']." effectuée.<br />";
	}
	else {
		$msg="Erreur lors de la modification de l'accès aux données pour pers_id=".$_GET['pers_id']." et ele_id=".$_GET['ele_id']."<br />";
	}
}

if((isset($_GET['envoi_bulletin_resp_legal_0']))&&(($_GET['envoi_bulletin_resp_legal_0']=='y')||($_GET['envoi_bulletin_resp_legal_0']=='n'))) {
	check_token();

	$sql="UPDATE responsables2 SET envoi_bulletin='".$_GET['envoi_bulletin_resp_legal_0']."' WHERE pers_id='".$_GET['pers_id']."' AND ele_id='".$_GET['ele_id']."';";
	$update=mysqli_query($GLOBALS["mysqli"], $sql);
	if($update) {
		$msg="Modification de la génération ou non des bulletins pour pers_id=".$_GET['pers_id']." et ele_id=".$_GET['ele_id']." effectuée.<br />";
	}
	else {
		$msg="Erreur lors de la modification de la génération ou non des bulletins pour pers_id=".$_GET['pers_id']." et ele_id=".$_GET['ele_id']."<br />";
	}
}

if (isset($is_posted) and ($is_posted == '1')) {
	check_token();

	$msg="";

	//$adr_id_existant=isset($_POST['adr_id_existant']) ? $_POST['adr_id_existant'] : '';

	//echo "\$choisir_ad_existante=$choisir_ad_existante<br />";

	$choisir_ad_existante=isset($_POST['choisir_ad_existante']) ? $_POST['choisir_ad_existante'] : '';
	//echo "\$choisir_ad_existante=$choisir_ad_existante<br />";

	$tab_nom_prenom_resp=isset($_POST['tab_nom_prenom_resp']) ? $_POST['tab_nom_prenom_resp'] : NULL;

	$ok='';
	if((isset($add_ele_id))&&(isset($pers_id))) {
		$ok='yes';
	}
	elseif((isset($tab_nom_prenom_resp))&&(($resp_nom=='')||($resp_prenom==''))) {
		$ok='no';
	}
	else {
		if($choisir_ad_existante=='oui') {
			// On crée la personne si elle n'existe pas et on enchaine avec la page choix_adr_existante.php
			$ok='yes';
		}
		else {
			if(!isset($_POST['resp_legal'])) {
				$tester_validite_adresse="n";
				$ok='yes';
			}
			else {
				$tester_validite_adresse="y";
				if(is_array($_POST['resp_legal'])) {
					$tmp_resp_legal=$_POST['resp_legal'];
					$tester_validite_adresse="n";
					$ok='yes';
					for($loop=0;$loop<count($tmp_resp_legal);$loop++) {
						if(($tmp_resp_legal[$loop]==1)||($tmp_resp_legal[$loop]==2)) {
							$tester_validite_adresse="y";
							$ok='no';
							break;
						}
					}
				}
			}

			if($tester_validite_adresse=='y') {
				//elseif(($adr1 != '') and ($commune != '') and ($cp != '')){
				if(($adr1 != '') and ($commune != '') and (($cp != '')||($pays != ''))) {
					$ok='yes';
				}
				else {
					$ok='no';
					$msg.="Un responsable légal 1 ou 2 doit avoir une adresse non vide.<br />";
				}
			}
		}
	}

	if($ok!='yes'){
		$msg.= "Un ou plusieurs champs obligatoires sont vides !";
	}
	else{
		if(!isset($nouv_resp)){
			//if(isset($pers_id)){
			if((isset($pers_id))&&(isset($tab_nom_prenom_resp))) {
				$compte_resp_existe="n";
				$test1_login=mysqli_query($GLOBALS["mysqli"], "SELECT login FROM resp_pers WHERE pers_id = '$pers_id'");
				if(mysqli_num_rows($test1_login)>0) {$compte_resp_existe="y";}

				$sql="UPDATE resp_pers SET nom='$resp_nom',
								prenom='$resp_prenom',
								civilite='$civilite',
								tel_pers='$tel_pers',
								tel_port='$tel_port',
								tel_prof='$tel_prof'";
				// On permet de modifier l'adresse mail même si on est en mode mon_compte (quand on modifie, c'est alors pour dépanner)
				//if((getSettingValue('mode_email_resp')!='mon_compte')&&($compte_resp_existe=='y')&&(isset($mel))) {
				if(($compte_resp_existe=='y')&&(isset($mel))) {
					$sql.=",mel='$mel'";
				}
				/*
				//if($adr_id_existant!=""){
				if((isset($select_ad_existante))&&($adr_id_existant!="")){
					$adr_id=$adr_id_existant;
					$sql.=",adr_id='$adr_id'";
				}
				*/
				$sql.=" WHERE pers_id='$pers_id'";
				//echo "$sql<br />\n";
				$res_update=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$res_update){
					$msg.="Erreur lors de la mise à jour dans 'resp_pers'. ";
				} else {
					// On met également à jour la table utilisateurs si le responsable a un compte
					$test1_login = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT login FROM resp_pers WHERE pers_id = '$pers_id'"), 0);
					//echo "\$test1_login=$test1_login<br />\n";
					if ($test1_login != '') {
						$sql="SELECT count(login) FROM utilisateurs WHERE login = '".$test1_login."'";
						//echo "$sql<br />\n";
						$test2_login = old_mysql_result(mysqli_query($GLOBALS["mysqli"], $sql), 0);
						if ($test2_login == 1) {
							$sql="UPDATE utilisateurs SET nom = '".$resp_nom."', prenom = '" . $resp_prenom . "', civilite='$civilite'";
							//if((getSettingValue('mode_email_resp')!='mon_compte')&&(isset($mel))) {
							if(isset($mel)) {
								$sql.=", email = '" . $mel . "'";
							}
							$sql.=" WHERE login ='" . $test1_login ."'";
							//echo "$sql<br />\n";
							$res = mysqli_query($GLOBALS["mysqli"], $sql);
						}
					}
				}
			}

			// On n'insère pas les saisies des champs adr1, adr2,... si une adresse existante a été sélectionnée:
			//if($adr_id_existant==""){
			if($choisir_ad_existante==""){
				//echo "a<br />";
				//if(isset($changement_adresse)){
				if((isset($changement_adresse))&&(isset($tab_nom_prenom_resp))) {
					//echo "b<br />";
					if($changement_adresse=="desolidariser"){
						//echo "c<br />";
						// Recherche du plus grand adr_id
						$sql="SELECT adr_id FROM resp_adr WHERE adr_id LIKE 'a%' ORDER BY adr_id DESC";
						//echo "$sql<br />\n";
						$res1=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res1)==0){
							//$adr_id="a1";
							$adr_id="a".sprintf("%09d","1");
						}
						else{
							$ligtmp=mysqli_fetch_object($res1);
							$nb=mb_substr($ligtmp->adr_id,1);
							$nb++;
							//$adr_id="a".$nb;
							$adr_id="a".sprintf("%09d",$nb);
						}
						$sql="INSERT INTO resp_adr SET adr1='$adr1',
										adr2='$adr2',
										adr3='$adr3',
										adr4='$adr4',
										cp='$cp',
										commune='$commune',
										pays='$pays',
										adr_id='$adr_id'";
						//echo "$sql<br />\n";
						$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res_insert){
							$msg.="Erreur lors de l'insertion de la nouvelle adresse. ";
						}
						else{
							$sql="UPDATE resp_pers SET adr_id='$adr_id' ";
							$sql.="WHERE pers_id='$pers_id'";
							//echo "$sql<br />\n";
							$res_update=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res_update){
								$msg.="Erreur lors de la mise à jour de l'identifiant d'adresse dans 'resp_pers'. ";
							}
						}
					}
					elseif(isset($adr_id)){
						$sql="UPDATE resp_adr SET adr1='$adr1',
										adr2='$adr2',
										adr3='$adr3',
										adr4='$adr4',
										cp='$cp',
										commune='$commune',
										pays='$pays'
									WHERE adr_id='$adr_id'";
						//echo "$sql<br />\n";
						$res_update=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res_update){
							$msg.="Erreur lors de la mise à jour de l'adresse dans 'resp_adr'. ";
						}
					}
				}
				elseif(isset($adr_id)){
					$sql="UPDATE resp_adr SET adr1='$adr1',
									adr2='$adr2',
									adr3='$adr3',
									adr4='$adr4',
									cp='$cp',
									commune='$commune',
									pays='$pays'
								WHERE adr_id='$adr_id'";
					//echo "$sql<br />\n";
					$res_update=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res_update){
						$msg.="Erreur lors de la mise à jour de l'adresse dans 'resp_adr'. ";
					}
				}
			}
			else{
				// On redirige vers choix_adr_existante.php
				header("Location: choix_adr_existante.php?pers_id=$pers_id");
				die();
			}


			// Partie élèves:
			//if(isset($cpt)){
			//if((isset($cpt))&&(isset($pers_id))&&($msg=='')){
			if((isset($cpt))&&(isset($pers_id))&&($msg=='')&&(isset($tab_nom_prenom_resp))) {
				//echo "1<br />";
				for($i=0;$i<$cpt;$i++){
					//echo " $i<br />";
					if(isset($suppr_ele_id[$i])){
						//echo "\$suppr_ele_id[$i]=".$suppr_ele_id[$i]."<br />";
						$sql="DELETE FROM responsables2 WHERE pers_id='$pers_id' AND ele_id='$suppr_ele_id[$i]'";
						//echo "$sql<br />\n";
						$res_suppr=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res_suppr){
							$msg.="Erreur lors de la suppression de l'association avec l'élève $suppr_ele_id[$i] dans 'responsables2'. ";
						}
					}
					else {
						//if(!isset($resp_erreur[$i])){
						// On ne cherche pas à modifier les resp_legal
						if($resp_legal[$i]==0) {
							$sql="UPDATE responsables2 SET resp_legal='$resp_legal[$i]' WHERE pers_id='$pers_id' AND ele_id='$ele_id[$i]'";
							//echo "$sql<br />\n";
							$res_update=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res_update){
								$msg.="Erreur lors de la mise à jour de 'resp_legal' pour le responsable $pers_id. ";
							}
						}
						else {
							// Pour le responsable affiché, on vient de soumettre $resp_legal[$i] pour l'élève $i
							if($resp_legal[$i]==1){$resp_legal2=2;}else{$resp_legal2=1;}

							$temoin_erreur="non";
							//if(isset($pers_id2[$i])){
							if(isset($_POST['pers_id2_'.$i])){

								$tmp_pers_id2=$_POST['pers_id2_'.$i];

								for($loop=0;$loop<count($tmp_pers_id2);$loop++) {
									if($loop==0) {
										$sql="UPDATE responsables2 SET resp_legal='$resp_legal2' WHERE pers_id='".$tmp_pers_id2[$loop]."' AND ele_id='$ele_id[$i]'";
										//echo "$sql<br />\n";
										$res_update=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$res_update){
											$msg.="Erreur lors de la mise à jour de 'resp_legal' pour l'autre responsable (".$tmp_pers_id2[$loop]."). ";
											$temoin_erreur="oui";
										}
									}
									else {
										$sql="UPDATE responsables2 SET resp_legal='0' WHERE pers_id='".$tmp_pers_id2[$loop]."' AND ele_id='$ele_id[$i]'";
										//echo "$sql<br />\n";
										$res_update=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$res_update){
											$msg.="Erreur lors de la mise à jour de 'resp_legal' pour l'autre responsable (".$tmp_pers_id2[$loop]."). ";
											$temoin_erreur="oui";
										}
										else {
											$msg.="Il y avait trop de responsables légaux.<br />Le responsable n°".$tmp_pers_id2[$loop]." est rendu responsable non légal. ";
										}
									}
								}
							}

							if($temoin_erreur!="oui"){
								$sql="UPDATE responsables2 SET resp_legal='$resp_legal[$i]' WHERE pers_id='$pers_id' AND ele_id='$ele_id[$i]'";
								//echo "$sql<br />\n";
								$res_update=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$res_update){
									$msg.="Erreur lors de la mise à jour de 'resp_legal' pour le responsable $pers_id. ";
								}
							}
						}
					}
				}
			}

			if((isset($add_ele_id))&&(isset($pers_id))&&($msg=='')){
				if($add_ele_id!=''){
					$sql="SELECT 1=1 FROM responsables2 WHERE pers_id!='$pers_id' AND ele_id='$add_ele_id'";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)==0){
						$resp_legal=1;
					}
					else{
						$sql="SELECT resp_legal FROM responsables2 WHERE ele_id='$add_ele_id'";
						$res_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
						$ligtmp=mysqli_fetch_object($res_tmp);
						if($ligtmp->resp_legal==1){$resp_legal=2;}else{$resp_legal=1;}
					}

					$sql="INSERT INTO responsables2 SET pers_id='$pers_id', ele_id='$add_ele_id', resp_legal='$resp_legal'";
					$res_update=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res_update){
						$msg.="Erreur lors de l'ajout de l'élève $add_ele_id. ";
					}
				}
			}

		}
		else{
			// Nouveau responsable:

			// Recherche du plus grand pers_id
			$sql="SELECT pers_id FROM resp_pers WHERE pers_id LIKE 'p%' ORDER BY pers_id DESC";
			//echo "$sql<br />\n";
			$res1=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res1)==0){
				//$pers_id="p1";
				$pers_id="p".sprintf("%09d","1");
			}
			else{
				$ligtmp=mysqli_fetch_object($res1);
				$nb=mb_substr($ligtmp->pers_id,1);
				$nb++;
				//$pers_id="p".$nb;
				$pers_id="p".sprintf("%09d",$nb);
			}



			// Insertion du nouvel utilisateur dans resp_pers:
			$sql="INSERT INTO resp_pers SET pers_id='$pers_id',
								nom='$resp_nom',
								prenom='$resp_prenom',
								civilite='$civilite',
								tel_pers='$tel_pers',
								tel_port='$tel_port',
								tel_prof='$tel_prof',
								mel='$mel'";
			//echo "$sql<br />\n";
			$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$res_insert){
				$msg.="Erreur lors de l'insertion dans 'resp_pers'. ";
			}
			else{
				//if($adr_id_existant==""){
				//if((!isset($select_ad_existante))||($adr_id_existant=="")){
				if($choisir_ad_existante==""){
					//echo "<p>1</p>";

					// Recherche du plus grand adr_id
					$sql="SELECT adr_id FROM resp_adr WHERE adr_id LIKE 'a%' ORDER BY adr_id DESC";
					//echo "$sql<br />\n";
					$res1=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res1)==0){
						//$adr_id="a1";
						$adr_id="a".sprintf("%09d","1");
					}
					else{
						$ligtmp=mysqli_fetch_object($res1);
						$nb=mb_substr($ligtmp->adr_id,1);
						$nb++;
						//$adr_id="a".$nb;
						$adr_id="a".sprintf("%09d",$nb);
					}

					if(isset($adr_id)){
						$sql="INSERT INTO resp_adr SET adr1='$adr1',
										adr2='$adr2',
										adr3='$adr3',
										adr4='$adr4',
										cp='$cp',
										commune='$commune',
										pays='$pays',
										adr_id='$adr_id'";
						//echo "$sql<br />\n";
						$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res_insert){
							$msg.="Erreur lors de l'insertion de l'adresse dans 'resp_adr'. ";
						}
						else{
							$sql="UPDATE resp_pers SET adr_id='$adr_id' WHERE pers_id='$pers_id'";
							$res_update=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res_update){
								$msg.="Erreur lors de la mise à jour de l'association de la personne avec son adresse. ";
							}
						}
					}
				}
				else{
					//$adr_id=$adr_id_existant;
					//echo "<p>2</p>";

					// On redirige vers choix_adr_existante.php
					header("Location: choix_adr_existante.php?pers_id=$pers_id");
					die();
				}
			}
		}

		if($msg==""){
			$msg="Enregistrement réussi.";
		}
	}
}


$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *******************************
$titre_page = "Ajouter ou modifier un responsable";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE ***************************

//debug_var();

if(!getSettingValue('conv_new_resp_table')){
	$sql="SELECT 1=1 FROM responsables";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0){
		echo "<p>Une conversion des données responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SHOW COLUMNS FROM eleves LIKE 'ele_id'";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0){
		echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		$sql="SELECT 1=1 FROM eleves WHERE ele_id=''";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0){
			echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";
			echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
}

// &amp;quitter_la_page=y


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


if(isset($associer_eleve)) {

	if(!isset($quitter_la_page)){
		if (!isset($pers_id)) {
			echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
			echo "<p><b>ERREUR</b>: Aucun identifiant de responsable n'a été fourni.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<p class='bold'><a href='modify_resp.php?pers_id=$pers_id'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
		echo "</p>\n";
	}
	else {
		if (!isset($pers_id)) {
			//echo "<p class=bold><a href=\"#\" onclick=\"self.close();\">Refermer la page</a></p>\n";

		echo "<script type='text/javascript'>
	function refresh_opener() {
		ad=window.opener.location.href;
		window.opener.location.href=ad;
	}
</script>\n";
			echo "<p class=bold><a href=\"#\" onclick=\"refresh_opener();confirm_close (this, change, '$themessage');\">Refermer la page</a>\n";

			echo "<p><b>ERREUR</b>: Aucun identifiant de responsable n'a été fourni.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		//if($_SESSION['statut']=="administrateur"){
			//echo "<p class=bold><a href=\"#\" onclick=\"confirm_close (this, change, '$themessage');\">Refermer la page</a></p>\n";
			// window.opener.location.href='../eleves/modify_eleve.php?var=rien&v
			echo "<p class=bold><a href=\"#\" onclick=\"confirm_close (this, change, '$themessage');\">Refermer la page</a></p>\n";
		/*
		}
		else{
			echo "<p class=bold><a href=\"#\" onclick=\"self.close();\">Refermer la page</a></p>\n";
		}
		*/
	}

	// AFFICHER LE RESPONSABLE COURANT

	$sql="SELECT rp.* FROM resp_pers rp WHERE
					rp.pers_id='$pers_id'";
	//echo "$sql<br />\n";
	$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);

	$lig_pers=mysqli_fetch_object($res_resp);

	$sql="SELECT DISTINCT e.ele_id,e.nom,e.prenom,e.login FROM eleves e ORDER BY e.nom,e.prenom";
	$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);

	if(mysqli_num_rows($res_ele)==0){
		echo "<p>Il semblerait qu'aucun élève ne soit encore dans la base.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	$tab_anomalie_ele_id=array();
	$compteur=0;
	while($lig_ele=mysqli_fetch_object($res_ele)){
		// On ne propose que les élèves n'ayant pas déjà leurs deux responsables légaux
		//$sql="SELECT * FROM responsables2 WHERE ele_id='$lig_ele->ele_id'";
		$sql="SELECT * FROM responsables2 WHERE ele_id='$lig_ele->ele_id' AND (resp_legal='1' OR resp_legal='2')";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)<2){

			if($compteur==0){
				echo "<form enctype='multipart/form-data' name='resp' action='modify_resp.php' method='post'>\n";
				echo add_token_field();
				echo "<input type='hidden' name='pers_id' value='$pers_id' />\n";

				if(isset($quitter_la_page)) {
					echo "<input type='hidden' name='quitter_la_page' value='$quitter_la_page' />\n";
				}

				echo "<p>Sélectionner l'élève à associer à ".casse_mot($lig_pers->prenom,'majf2')." ".my_strtoupper($lig_pers->nom)."<br />\n";

				//echo "<p align='center'>\n";
				echo "<select name='add_ele_id'";
				echo " onchange='changement();'";
				echo ">\n";
				echo "<option value=''>--- Ajouter un élève ---</option>\n";
			}

			if($lig_ele->ele_id!='') {
				echo "<option value='$lig_ele->ele_id'>".my_strtoupper($lig_ele->nom)." ".casse_mot($lig_ele->prenom,'majf2')."</option>\n";
			}
			else {
				$tab_anomalie_ele_id[]=$lig_ele->login;
			}
			$compteur++;
		}
	}

	if($compteur>0){
		echo "</select>\n";
		echo "<br />\n(<i>$compteur élèves n'ont pas leurs deux responsables légaux</i>)\n";
		echo "</p>\n";

		echo "<center><input type='submit' value='Enregistrer' /></center>\n";
		echo "<input type='hidden' name='is_posted' value='1' />\n";
		echo "</form>\n";
	}
	else{
		echo "<p>Tous les élèves ont leur deux responsables légaux.</p>\n";
	}

	if(count($tab_anomalie_ele_id)>0) {
		echo "<p><span style='color:red'>ANOMALIE&nbsp;:</span> Un ou des élèves n'ont pas d'ELE_ID.<br />Comment avez-vous initialisé/importé/créé ces élèves&nbsp;?<br />En voici la liste&nbsp;:</p>";
		echo "<ul>\n";
		for($i=0;$i<count($tab_anomalie_ele_id);$i++) {
			echo "<li><a href='../eleves/modify_eleve.php?eleve_login=".$tab_anomalie_ele_id[$i]."'>".get_nom_prenom_eleve($tab_anomalie_ele_id[$i],'avec_classe')."</li>\n";
		}
		echo "</ul>\n";
	}

	require("../lib/footer.inc.php");
	die();
}


if(!isset($quitter_la_page)){
	echo "<p class='bold'><a href='index.php'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo "><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	echo " | <a href='modify_resp.php'>Ajouter un responsable</a>";

	echo "<span id='lien_saisie_engagements'></span>";
}
else {
	//if($_SESSION['statut']=="administrateur"){
		//echo "<p class=bold><a href=\"#\" onclick=\"confirm_close (this, change, '$themessage');\">Refermer la page</a>\n";
		echo "<script type='text/javascript'>
	function refresh_opener() {
		ad=window.opener.location.href;
		var verif = /modify_eleve.php/
		if (verif.exec(ad) != null) {
			window.opener.location.href=ad;
		}
	}
</script>\n";
		echo "<p class=bold><a href=\"#\" onclick=\"refresh_opener();confirm_close (this, change, '$themessage');\">Refermer la page</a>\n";

	/*
	}
	else{
		echo "<p class=bold><a href=\"#\" onclick=\"self.close();\">Refermer la page</a>\n";
	}
	*/
	echo " | <a href='modify_resp.php?quitter_la_page=y'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">Ajouter un responsable</a>";
}
echo "</p>\n";

echo "<form enctype='multipart/form-data' name='resp' action='modify_resp.php' method='post'>\n";
echo add_token_field();

if(isset($quitter_la_page)) {
	echo "<input type='hidden' name='quitter_la_page' value='$quitter_la_page' />\n";
}

if ((!isset($pers_id))&&(isset($login_resp))&&($login_resp!="")) {
	$sql="SELECT pers_id FROM resp_pers WHERE login='$login_resp';";
	$res_pers_id=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_pers_id)>0) {
		$pers_id=old_mysql_result($res_pers_id, 0, "pers_id");
	}
}

//$temoin_compte_utilisateur="n";
$temoin_adr=0;
//if (isset($ereno)) {
if (isset($pers_id)) {

	echo "<input type='hidden' name='pers_id' value='$pers_id' />\n";
	// Recherche des infos sur le responsable:
	/*
	$sql="SELECT ra.*,rp.nom,rp.prenom,rp.tel_pers,rp.tel_port,rp.tel_prof,rp.mel FROM resp_pers rp, resp_adr ra WHERE
					rp.adr_id=ra.adr_id AND
					rp.pers_id='$pers_id'";
	*/
	$sql="SELECT rp.* FROM resp_pers rp WHERE
					rp.pers_id='$pers_id'";
	//echo "$sql<br />\n";
	$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);

	if(mysqli_num_rows($res_resp)==0) {
		echo "<p style='color:red'><strong>Erreur&nbsp;:</strong> Aucun responsable n'est (<em>plus</em>) associé au n°$pers_id dans la table 'resp_pers'.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$lig_pers=mysqli_fetch_object($res_resp);

	$resp_login_tmp=$lig_pers->login;
	$resp_nom=$lig_pers->nom;
	$resp_prenom=$lig_pers->prenom;
	$civilite=$lig_pers->civilite;
	$tel_pers=$lig_pers->tel_pers;
	$tel_port=$lig_pers->tel_port;
	$tel_prof=$lig_pers->tel_prof;
	$mel=$lig_pers->mel;
	$mel_resp_pers=$lig_pers->mel;

	if(getSettingValue('mode_email_resp')=='mon_compte') {
		$sql="SELECT email FROM utilisateurs WHERE login='$resp_login_tmp' and statut='responsable';";
		$res_email_utilisateur_resp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_email_utilisateur_resp)>0) {
			$lig_email_utilisateur_resp=mysqli_fetch_object($res_email_utilisateur_resp);
			if($lig_email_utilisateur_resp->email!=$mel) {
				$sql="UPDATE resp_pers SET mel='$lig_email_utilisateur_resp->email' WHERE login='$resp_login_tmp' and statut='responsable';";
				$update_email=mysqli_query($GLOBALS["mysqli"], $sql);
				if($update_email) {echo "<span style='color:red;'>Adresse mail mise à jour d'après celle du compte d'utilisateur.</span><br />";}
			}
			$mel=$lig_email_utilisateur_resp->email;
			//$temoin_compte_utilisateur="y";
		}
	}

	$sql="SELECT ra.* FROM resp_adr ra WHERE
					ra.adr_id='$lig_pers->adr_id'";
	//echo "$sql<br />\n";
	$res_adr=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_adr)>0){
		$lig_adr=mysqli_fetch_object($res_adr);

		//echo "adr_id=";
		echo "<input type='hidden' name='adr_id' value='$lig_adr->adr_id' />\n";
		$adr_id=$lig_adr->adr_id;
		$adr1=$lig_adr->adr1;
		$adr2=$lig_adr->adr2;
		$adr3=$lig_adr->adr3;
		$adr4=$lig_adr->adr4;
		$cp=$lig_adr->cp;
		$pays=$lig_adr->pays;
		$commune=$lig_adr->commune;

		$temoin_adr=1;
	}
}
else{
	echo "<input type='hidden' name='nouv_resp' value='yes' />\n";
}

// Initialisation des variables, si nécessaire:
if (!isset($resp_nom)) $resp_nom='';
if (!isset($resp_prenom)) $resp_prenom='';
if (!isset($civilite)) $civilite='';
if (!isset($adr1)) $adr1='';
if (!isset($adr2)) $adr2='';
if (!isset($adr3)) $adr3='';
if (!isset($adr4)) $adr4='';
if (!isset($commune)) $commune='';
if (!isset($cp)) $cp='';
if (!isset($pays)) $pays='';
if (!isset($tel_pers)) $tel_pers='';
if (!isset($tel_port)) $tel_port='';
if (!isset($tel_prof)) $tel_prof='';
if (!isset($mel)) $mel='';

$AccesDetailConnexionResp=false;

echo "<table>\n";
echo "<tr>\n";
// Colonne nom, prénom, adresse, tel du responsable:
echo "<td valign='top'>\n";

	// Témoin pour faire le distingo entre l'ajout/modif de responsable et l'association avec un élève
	echo "<input type='hidden' name='tab_nom_prenom_resp' value='y' />\n";

	// Affichage du tableau de la saisie des nom, prenom, adresse, tel,...
	echo "<p><b>Responsable&nbsp;:</b>\n";
	if(isset($pers_id)){
		echo " (<i>n°$pers_id</i>)";

		$sql="SELECT u.login, u.email, u.auth_mode FROM utilisateurs u, resp_pers rp WHERE rp.login=u.login AND rp.pers_id='$pers_id' AND u.login!='';";
		$test_compte=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test_compte)>0) {
			$compte_resp_existe="y";
			$lig_resp_login=mysqli_fetch_object($test_compte);

			$resp_login=$lig_resp_login->login;
			$resp_u_email=$lig_resp_login->email;
			$resp_auth_mode=$lig_resp_login->auth_mode;

			$AccesDetailConnexionResp=AccesInfoResp('AccesDetailConnexionResp', $resp_login);

			if($_SESSION['statut']=='administrateur') {$avec_lien="y";}
			else {$avec_lien="n";}
			$lien_image_compte_utilisateur=lien_image_compte_utilisateur($resp_login, "responsable", "_blank", $avec_lien);

			if($_SESSION['statut']=='administrateur') {
				echo " (<em title=\"Compte d'utilisateur\"><a href='../utilisateurs/edit_responsable.php?critere_recherche_login=$resp_login'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				echo ">$resp_login</a>";
				if($lien_image_compte_utilisateur!="") {echo " ".$lien_image_compte_utilisateur;}
				echo "</em>)";
			}
			else {
				echo " (<em title=\"Compte d'utilisateur\">$resp_login";
				if($lien_image_compte_utilisateur!="") {echo " ".$lien_image_compte_utilisateur;}
				echo "</em>)";
			}
			echo temoin_compte_sso($resp_login);
		}
		else {
			$compte_resp_existe="n";

			if($_SESSION['statut']=="administrateur") {
				$tmp_tab=get_enfants_from_pers_id($pers_id, 'simple', "n");
				if(count($tmp_tab)>0) {
					echo " <a href='../utilisateurs/create_responsable.php?filtrage=Afficher&amp;critere_recherche=".preg_replace("/[^A-Za-z]/", "%", $resp_nom)."'";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo " title=\"Ajouter un compte d'utilisateur pour ce responsable.\"><img src='../images/icons/buddy_plus.png' class='icone16' /></a>";
				}
				elseif(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
					// Il ne faut pas "yy" parce que le droit spécial ne peut être donné qu'une fois le compte créé.
					$tmp_tab=get_enfants_from_pers_id($pers_id, 'simple', "y");
					/*
					echo "<pre>";
					print_r($tmp_tab);
					echo "</pre>";
					*/
					if(count($tmp_tab)>0) {
						echo " <a href='../utilisateurs/create_responsable.php?filtrage_rl0=Afficher&amp;critere_recherche_rl0=".preg_replace("/[^A-Za-z]/", "%", $resp_nom)."'";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo " title=\"Ajouter un compte d'utilisateur pour ce responsable.\"><img src='../images/icons/buddy_plus.png' class='icone16' /></a>";
					}
				}
			}
		}

		if(($compte_resp_existe=="y")&&
				($AccesDetailConnexionResp)
			) {
			$journal_connexions=isset($_POST['journal_connexions']) ? $_POST['journal_connexions'] : (isset($_GET['journal_connexions']) ? $_GET['journal_connexions'] : 'n');
			$duree=isset($_POST['duree']) ? $_POST['duree'] : NULL;
		
			echo " <a href='".$_SERVER['PHP_SELF']."?pers_id=$pers_id&amp;journal_connexions=y#connexion' title='Journal des connexions'><img src='../images/icons/document.png' width='16' height='16' alt='Journal des connexions' /></a>\n";
		}

	}
	echo "</p>\n";

	echo "<table>\n";
	echo "<tr><td>Nom * : </td><td><input type=text size=50 name=resp_nom value = \"".$resp_nom."\" onchange='changement();' /></td></tr>\n";
	echo "<tr><td>Prénom * : </td><td><input type=text size=50 name=resp_prenom value = \"".$resp_prenom."\" onchange='changement();' /></td></tr>\n";
	echo "<tr><td>Civilité : </td><td>\n";

	echo "<table border='0'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	// AFFICHER AVEC JAVASCRIPT CE QUI EST ENREGISTRé/SAISI...
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='civilite' style='cursor: pointer;'>\n";
	echo "<input type='radio' name='civilite' id='civilite' value=\"\" onchange='changement();' ";
	if($civilite==""){echo "checked ";}
	echo "/> X \n";
	echo "</label>\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='civiliteM' style='cursor: pointer;'>\n";
	echo "<input type='radio' name='civilite' id='civiliteM' value=\"M.\" onchange='changement();' ";
	if($civilite=="M."){echo "checked ";}
	echo "/> M. \n";
	echo "</label>\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='civiliteMme' style='cursor: pointer;'>\n";
	echo "<input type='radio' name='civilite' id='civiliteMme' value=\"Mme\" onchange='changement();' ";
	if($civilite=="Mme"){echo "checked ";}
	echo "/> Mme \n";
	echo "</label>\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<label for='civiliteMlle' style='cursor: pointer;'>\n";
	echo "<input type='radio' name='civilite' id='civiliteMlle' value=\"Mlle\" onchange='changement();' ";
	if($civilite=="Mlle"){echo "checked ";}
	echo "/> Mlle\n";
	echo "</label>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "</td></tr>\n";
	echo "<tr><td>Tel.perso : </td><td><input type='text' size='15' name='tel_pers' value=\"".$tel_pers."\" onchange='changement();' /></td></tr>\n";
	echo "<tr><td>Tel.portable : </td><td><input type='text' size='15' name='tel_port' value=\"".$tel_port."\" onchange='changement();' /></td></tr>\n";
	echo "<tr><td>Tel.professionnel : </td><td><input type='text' size='15' name='tel_prof' value=\"".$tel_prof."\" onchange='changement();' /></td></tr>\n";

	echo "<tr>\n";
	echo "<td>Mel : </td><td>\n";
	if(isset($compte_resp_existe)&&($compte_resp_existe=="y")&&(getSettingValue('mode_email_resp')=='mon_compte')) {
		// Faudrait-il quand même permettre la saisie en mode mon_compte si le mail est vide?
		// Pour permettre une récupération de mot de passe?

		echo "<input type='text' size='46' name='mel' value=\"".$resp_u_email."\" onchange='changement();' />";

		if((isset($mel_resp_pers))&&($mel_resp_pers!=$resp_u_email)&&($mel_resp_pers!="")) {
			$precision_sur_mails="<br />(<em>'$resp_u_email' saisi par le responsable et '$mel_resp_pers' dans la table resp_pers</em>)";
		}
	}
	else {
		echo "<input type='text' size='46' name='mel' value=\"".$mel."\" onchange='changement();' />";

		if((isset($resp_u_email))&&($mel!=$resp_u_email)&&($resp_u_email!="")) {
			$precision_sur_mails="<br />(<em>'$resp_u_email' saisi par le responsable et '$mel' dans la table resp_pers</em>)";
		}
	}
	if($mel!='') {
		$tmp_date=getdate();
		echo " <a href='mailto:".$mel."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI&amp;body=";
		if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
		echo ",%0d%0aCordialement.'>";
		echo "<img src='../images/imabulle/courrier.jpg' width='20' height='15' alt='Envoyer un courriel' border='0' />";
		echo "</a>";
	}
	if(isset($precision_sur_mails)) {echo $precision_sur_mails;}

	echo "</td></tr>\n";
	echo "</table>\n";

echo "</td>\n";
// Colonne élève et conjoint:
echo "<td valign='top'>\n";


if(isset($pers_id)){
	// Enfants/élèves à charge:
	//$sql="SELECT DISTINCT ele_id FROM responsables2 WHERE pers_id='$pers_id'";
	//$sql="SELECT e.nom,e.prenom,e.ele_id,r.resp_legal FROM responsables2 r, eleves e WHERE e.ele_id=r.ele_id AND r.pers_id='$pers_id' ORDER BY e.nom,e.prenom;";
	//$sql="SELECT e.nom,e.prenom,e.login,e.ele_id,r.resp_legal FROM responsables2 r, eleves e WHERE (e.ele_id=r.ele_id AND r.pers_id='$pers_id' AND (r.resp_legal='1' OR r.resp_legal='2')) ORDER BY e.nom,e.prenom;";
	$sql="SELECT e.nom,e.prenom,e.login,e.ele_id,r.resp_legal, r.acces_sp, r.envoi_bulletin FROM responsables2 r, eleves e WHERE (e.ele_id=r.ele_id AND r.pers_id='$pers_id') ORDER BY e.nom,e.prenom;";
	//echo "$sql<br />\n";
	$res1=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res1)==0){
		echo "<p>Ce responsable n'est encore rattaché à aucun élève.</p>\n";
	}
	else{
		echo "<p><b>Elève:</b></p>\n";
		//echo "<table border='1'>\n";
		echo "<table class='boireaus'>\n";
		echo "<tr>\n";
		echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;' rowspan='2'>Elève</td>\n";
		echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;' colspan='3'>Responsable legal</td>\n";
		echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;' rowspan='2'>Supprimer</td>\n";
		echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;' rowspan='2'>Autre responsable</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>1</td>\n";
		echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>2</td>\n";
		echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>0</td>\n";
		echo "</tr>\n";
		$cpt=0;
		$alt=-1;
		while($lig_ele=mysqli_fetch_object($res1)){
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td style='text-align:center;'><input type='hidden' name='ele_id[$cpt]' value='$lig_ele->ele_id' /><a href='../eleves/modify_eleve.php?eleve_login=".$lig_ele->login."' title=\"Éditer/Modifier la fiche élève.\">".ucfirst(mb_strtolower($lig_ele->prenom))." ".mb_strtoupper($lig_ele->nom);
			$tmp_clas=get_class_from_ele_login($lig_ele->login);
			if(isset($tmp_clas['liste_nbsp'])) {
				echo " <span style='font-size:small'>(<em>".$tmp_clas['liste_nbsp']."</em>)</span>";
			}
			echo "</a></td>\n";

			$resp_legal1=$lig_ele->resp_legal;

			// Y a-t-il un deuxième responsable?
			//$sql="SELECT rp.nom,rp.prenom,rp.pers_id FROM resp_pers rp, responsables2 r WHERE (rp.pers_id!='$pers_id' AND r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' AND (r.resp_legal='1' OR r.resp_legal='2'));";
			$sql="SELECT rp.nom,rp.prenom,rp.pers_id, r.resp_legal FROM resp_pers rp, responsables2 r WHERE (rp.pers_id!='$pers_id' AND r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id') ORDER BY r.resp_legal='1' DESC;";
			//echo "$sql<br />\n";
			$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);

			// S'il n'y a pas de deuxième responsable et que le responsable déclaré n'est pas le n°1:
			if((mysqli_num_rows($res_resp)==0)&&($resp_legal1!=1)){
				$tmpbg="background-color:red;";
			}
			else{
				$tmpbg="";
			}

			echo "<td style='text-align:center;$tmpbg'><input type='radio' name='resp_legal[$cpt]' value='1' onchange='changement();'";
			if($resp_legal1==1){echo " checked";}
			echo " /></td>\n";

			echo "<td style='text-align:center;'>";
			if(mysqli_num_rows($res_resp)>0){
				echo "<input type='radio' name='resp_legal[$cpt]' value='2' onchange='changement();'";
				//if($resp_legal1!=1){echo " checked";}
				if($resp_legal1==2){echo " checked";}
				echo " />";
			}
			echo "</td>\n";

			echo "<td style='text-align:center;'>";
			if(mysqli_num_rows($res_resp)>0){
				echo "<input type='radio' name='resp_legal[$cpt]' value='0' onchange='changement();'";
				if($resp_legal1==0){echo " checked";}
				echo " />";

				if($resp_legal1==0){
					// 20121213
					if(isset($compte_resp_existe)&&($compte_resp_existe=="y")) {
						if($lig_ele->acces_sp=='y') {
							echo " <a href='".$_SERVER['PHP_SELF']."?pers_id=$pers_id&amp;ele_id=".$lig_ele->ele_id."&amp;acces_resp_legal_0=n".add_token_in_url()."'";
							echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
							echo "><img src='../images/vert.png' width='16' height='16' title=\"Le responsable non légal $resp_prenom $resp_nom a accès aux données notes, CDT,... de l'élève (si ces modules sont actifs).

Cliquez pour supprimer l'accès.\" /></a>";
						}
						else {
							echo " <a href='".$_SERVER['PHP_SELF']."?pers_id=$pers_id&amp;ele_id=".$lig_ele->ele_id."&amp;acces_resp_legal_0=y".add_token_in_url()."'";
							echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
							echo "><img src='../images/rouge.png' width='16' height='16' title=\"Le responsable non légal $resp_prenom $resp_nom n'a pas accès aux données notes, CDT,... de l'élève (si ces modules sont actifs)

Cliquez pour donner l'accès.\" /></a>";
						}
					}

					if($lig_ele->envoi_bulletin=='y') {
						echo " <a href='".$_SERVER['PHP_SELF']."?pers_id=$pers_id&amp;ele_id=".$lig_ele->ele_id."&amp;envoi_bulletin_resp_legal_0=n".add_token_in_url()."'";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo "><img src='../images/icons/bulletin.png' width='16' height='16' title=\"Le responsable non légal $resp_prenom $resp_nom est destinataire des bulletins générés dans Gepi.

Cliquez pour supprimer la génération de bulletins à destination de ce responsable.\" /></a>";
					}
					else {
						echo " <a href='".$_SERVER['PHP_SELF']."?pers_id=$pers_id&amp;ele_id=".$lig_ele->ele_id."&amp;envoi_bulletin_resp_legal_0=y".add_token_in_url()."'";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo "><img src='../images/icons/bulletin_barre.png' width='16' height='16' title=\"Le responsable non légal $resp_prenom $resp_nom n'est pas destinataire des bulletins générés dans Gepi.

Cliquez pour activer la génération des bulletins à destination de ce responsable.\" /></a>";
					}
				}
			}
			echo "</td>\n";

			echo "<td style='text-align:center;'><input type='checkbox' name='suppr_ele_id[$cpt]' value='$lig_ele->ele_id' onchange='changement();' /></td>\n";

			echo "<td style='text-align:center;'>\n";

			if(mysqli_num_rows($res_resp)>0){
				$nb_resp_legaux_1=0;
				if($resp_legal1==1) {$nb_resp_legaux_1++;}
				$nb_resp_legaux_2=0;
				if($resp_legal1==2) {$nb_resp_legaux_2++;}
				$affichage_message_erreur_resp_legaux=0;
				while($lig_resp=mysqli_fetch_object($res_resp)){
					if($lig_resp->resp_legal==2) {
						$nb_resp_legaux_2++;
					}
					if($lig_resp->resp_legal==1) {
						$nb_resp_legaux_1++;
					}
					if(($affichage_message_erreur_resp_legaux==0)&&(($nb_resp_legaux_1>1)||($nb_resp_legaux_2>1))) {
						//echo "<input type='hidden' name='resp_erreur[".$cpt."]' value='y' />\n";
						// 20121213 : A FAIRE: CA BUGGUE AVEC L'AFFICHAGE DES RESP_LEGAL=0
						echo "<font color='red'>L'élève a trop de responsables légaux. Faites le ménage!</font><br />\n";
						$affichage_message_erreur_resp_legaux++;
					}

					echo "<span title='Responsable légal $lig_resp->resp_legal
Éditer/Modifier la fiche de ce responsable.'><a href='modify_resp.php?pers_id=$lig_resp->pers_id' onclick=\"return confirm_abandon (this, change, '$themessage')\">".mb_strtoupper($lig_resp->nom)." ".ucfirst(mb_strtolower($lig_resp->prenom))."</a>($lig_resp->resp_legal)</span>\n";
					//if(($lig_resp->resp_legal==2)&&($nb_resp_legaux_2<=1)) {
					//if(($nb_resp_legaux_1<=1)&&($nb_resp_legaux_2<=1)) {
					if(($lig_resp->resp_legal==1)||($lig_resp->resp_legal==2)) {
						//echo "<input type='hidden' name='pers_id2[".$cpt."]' value='$lig_resp->pers_id' />\n";
						echo "<input type='hidden' name='pers_id2_".$cpt."[]' value='$lig_resp->pers_id' />\n";
					}
					echo "<br />";
				}
			}
			echo "</td>\n";

			echo "</tr>\n";
			$cpt++;
		}
		echo "</table>\n";
		echo "<input type='hidden' name='cpt' value='$cpt' />\n";

		//$sql="SELECT * FROM resp_pers rp, responsables2 r WHERE rp.pers_id=r.pers_id AND r.resp_legal!='1' AND r.resp_legal!='2' AND r.ele_id=";

		echo "<hr />\n";
	}
}

if(isset($pers_id)) {
	// Ajout de l'association avec un élève existant:
	echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?pers_id=$pers_id&amp;associer_eleve=y";
	if(isset($quitter_la_page)){
		echo "&amp;quitter_la_page=$quitter_la_page";
	}
	echo "'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">Ajouter l'association avec un élève</a></p>\n";
}


echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

//==============================================
// Infos compte utilisateur
if((isset($compte_resp_existe))&&($compte_resp_existe=="y")&&(isset($resp_login))&&(isset($resp_auth_mode))&&
	(
		($_SESSION['statut']=="administrateur")||
		(($_SESSION['statut']=='scolarite')&&(getSettingAOui('ScolResetPassResp')))||
		(($_SESSION['statut']=='cpe')&&(getSettingAOui('CpeResetPassResp')))
	)
) {
	echo "<div style='float: right; width:15 em; text-align: center; border: 1px solid black; margin:0.2em; background-image: url(\"../images/background/opacite50.png\");'>\n";
	if($_SESSION['statut']=="administrateur") {
		echo affiche_actions_compte($resp_login);
		echo "<br />\n";
	}

	if((($resp_auth_mode=='gepi')||
	(($resp_auth_mode=='ldap')&&($gepiSettings['ldap_write_access'] == "yes")))&&
	(acces('/utilisateurs/reset_passwords.php', $_SESSION['statut']))) {
		echo affiche_reinit_password($resp_login);
	}
	echo "</div>\n";
}
//==============================================
// Engagements
if((isset($resp_login))&&($resp_login!="")&&(getSettingAOui('active_mod_engagements'))) {
	if(acces('/mod_engagements/saisie_engagements_user.php', $_SESSION['statut'])) {
		echo "<script type='text/javascript'>
	if(document.getElementById('lien_saisie_engagements')) {
		document.getElementById('lien_saisie_engagements').innerHTML=\" | <a href='../mod_engagements/saisie_engagements_user.php?login_user=$resp_login&amp;retour=modify_resp'>Saisir des engagements</a>\";
	}
</script>";
	}

	$tab_engagements_user=get_tab_engagements_user($resp_login);
	if(count($tab_engagements_user['indice'])>0) {
		echo "<div style='float: right; width:15em; text-align: center; margin:0.5em; margin:0.2em;' class='fieldset_opacite50' title=\"Engagements du responsable\">";
		if(acces("/mod_engagements/saisie_engagements_user.php", $_SESSION['statut'])) {
			/*
			echo "
	<div style='float: right; width:20px; height:20px;' title=\"Saisir/Modifier les engagements\"><a href='../mod_engagements/saisie_engagements_user.php?login_user=$resp_login' onclick=\"if(confirm_abandon (this, change, '".$themessage."')) {afficher_div_saisie_engagements('$login_resp')}; return false;\"><img src='../images/icons/plus_moins.png' class='icone16' alt='Ajouter/Enlever'/></a></div>";

			echo "
	<div style='float: right; width:20px; height:20px;' title=\"Saisir/Modifier les engagements\"><a href='../mod_engagements/saisie_engagements_user.php?login_user=$resp_login&amp;retour=modify_resp' onclick=\"afficher_div_saisie_engagements('$login_resp'); return false;\"><img src='../images/icons/plus_moins.png' class='icone16' alt='Ajouter/Enlever'/></a></div>";
			*/
			echo "
	<div style='float: right; width:20px; height:20px;' title=\"Saisir/Modifier les engagements\"><a href='../mod_engagements/saisie_engagements_user.php?login_user=$resp_login&amp;retour=modify_resp'><img src='../images/icons/plus_moins.png' class='icone16' alt='Ajouter/Enlever'/></a></div>";
		}

		/*
		echo "<pre>";
		print_r($tab_engagements_user['indice']);
		echo "</pre>";
		*/
		echo "<div id='div_engagements_responsable'>";
		for($loop=0;$loop<count($tab_engagements_user['indice']);$loop++) {
			$detail_eng="";
			if(($tab_engagements_user['indice'][$loop]['type']=='id_classe')&&($tab_engagements_user['indice'][$loop]['id_type']=='id_classe')) {
				$detail_eng=" en ".get_nom_classe($tab_engagements_user['indice'][$loop]['valeur']);
			}
			echo "<span title=\"".$tab_engagements_user['indice'][$loop]['nom_engagement'].$detail_eng."\n(".$tab_engagements_user['indice'][$loop]['engagement_description'].")\">".$tab_engagements_user['indice'][$loop]['nom_engagement'].$detail_eng."</span><br />";
		}
		echo "</div>\n";

		/*
		echo "<script type='text/javascript'>
	var change='no';

	function afficher_div_saisie_engagements(login_user) {
		//alert('plip');
		document.getElementById('valider_proposition').value=chaine;

		document.getElementById('valider_remplacement_nom_user').innerHTML=nom_user[tab[4]];

		afficher_div('div_saisie_engagements','y',10,-40);
	}
</script>";
		*/

		echo "</div>\n";
	}
}
//==============================================

echo "<a name='adresse'></a>\n";
echo "<p><b>Adresse:</b>";
if(isset($adr_id)){echo " (<i>n°$adr_id</i>)";}
echo "</p>\n";

echo "<div id='div_saisie_ad'>\n";
if($temoin_adr==1){
	$sql="SELECT * FROM resp_pers WHERE adr_id='$adr_id'";
	//echo "$sql<br />\n";
	$res_adr=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_adr)==0){
		// Bizarre!
		// Ce n'est pas possible d'après ce qui a été fait auparavant.
	}
	elseif(mysqli_num_rows($res_adr)==1){
		// L'adresse n'est associée qu'au responsable courant.
		echo "<p>Corriger/modifier l'adresse:</p>\n";
	}
	else{
		// L'adresse n'est associée à au moins un autre responsable.
		echo "<table><tr><td><b>Attention:</b></td><td>L'adresse indiquée ci-dessous est partagée avec un autre responsable.</td></tr>\n";
		//<br />\nSi vous modifiez l'adresse, elle le sera pour l'autre responsable également.</p>\n";
		echo "<tr><td>&nbsp;</td><td><label for='changement_adresse_corriger' style='cursor: pointer;'><input type='radio' name='changement_adresse' id='changement_adresse_corriger' value='corriger' checked onchange='changement();' /> Corriger/modifier l'adresse commune aux deux responsables,</label> <b>ou</b></td></tr>\n";
		//echo "<tr><td>&nbsp;</td><td>ou</td></tr>\n";
		echo "<tr><td>&nbsp;</td><td><label for='changement_adresse_desolidariser' style='cursor: pointer;'><input type='radio' name='changement_adresse' id='changement_adresse_desolidariser' value='desolidariser' onchange='changement();' /> Désolidariser l'adresse de celle de l'autre responsable.</label></td></tr></table>\n";
	}
}
else{
	echo "<p>Saisir une adresse:</p>\n";
}

echo "<table>\n";
//echo "<tr><td colspan='2'>Saisir une adresse</td></tr>\n";
echo "<tr><td>Adresse * : </td><td><input type=text size=50 name=adr1 value = \"".$adr1."\" onchange='changement();' /></td></tr>\n";
echo "<tr><td>Adresse (<i>suite</i>): </td><td><input type=text size=50 name=adr2 value = \"".$adr2."\" onchange='changement();' /></td></tr>\n";
echo "<tr><td>Adresse (<i>suite</i>): </td><td><input type=text size=50 name=adr3 value = \"".$adr3."\" onchange='changement();' /></td></tr>\n";
echo "<tr><td>Adresse (<i>suite</i>): </td><td><input type=text size=50 name=adr4 value = \"".$adr4."\" onchange='changement();' /></td></tr>\n";
echo "<tr><td>Code postal ** : </td><td><input type=text size=6 name=cp value = \"".$cp."\" onchange='changement();' />";
echo " ou Pays ** : <input type=text size=20 name=pays value = \"".$pays."\" onchange='changement();' />\n";
echo "</td></tr>\n";
echo "<tr><td>Commune * : </td><td><input type=text size=50 name=commune value = \"".$commune."\" onchange='changement();' /></td></tr>\n";

echo "</table>\n";

if(isset($pers_id)){
	echo "<p>Ou <a href='choix_adr_existante.php?pers_id=$pers_id";
	if(isset($adr_id)){
		echo "&amp;adr_id_actuel=$adr_id";
	}
	if(isset($quitter_la_page)) {
		echo "&amp;quitter_la_page=$quitter_la_page";
	}
	echo "'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">Choisir une adresse existante.</a></p>\n";
}
else{
	echo "<script type='text/javascript'>
	function creer_pers_id_puis_choisir_adr_exist(theLink, thechange, themessage){
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.forms.resp.choisir_ad_existante.value='oui';
			//setTimeout('document.forms.resp.submit()',5000);
			document.forms.resp.submit();
			return false;
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.forms.resp.choisir_ad_existante.value='oui';
				//setTimeout('document.forms.resp.submit()',5000);
				document.forms.resp.submit();
				return false;
			}
			else{
				return false;
			}
		}
	}
</script>\n";

	echo "<p>Ou <a href='".$_SERVER['PHP_SELF']."' onClick=\"creer_pers_id_puis_choisir_adr_exist(this, change, '$themessage');return false;\">Choisir une adresse existante.</a></p>";
	echo "<input type='hidden' name='choisir_ad_existante' value='' />";
}

echo "</div>\n";


echo "<center><input type='submit' value='Enregistrer' /></center>\n";

echo "<p>(*): saisie obligatoire<br />(**): un des deux champs au moins doit être rempli</p>\n";

echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "</form>\n";

if((isset($pers_id))&&($compte_resp_existe=="y")&&(isset($journal_connexions))&&($journal_connexions=='n')&&
		($AccesDetailConnexionResp)
	) {
	echo "<hr />\n";
	echo "<p><a href='".$_SERVER['PHP_SELF']."?pers_id=$pers_id&amp;journal_connexions=y#connexion' title='Journal des connexions'>Journal des connexions</a></p>\n";
}

if((isset($pers_id))&&($compte_resp_existe=="y")&&(isset($journal_connexions))&&($journal_connexions=='y')&&
		($AccesDetailConnexionResp)
	) {
	echo "<hr />\n";
	// Journal des connexions
	echo "<a name=\"connexion\"></a>\n";
	if (isset($_POST['duree'])) {
		$duree = $_POST['duree'];
	} else {
		$duree = '7';
	}
	
	journal_connexions($resp_login,$duree,'modify_resp',$pers_id);
	echo "<p><br /></p>\n";
}
?>

<!--font color='red'>A FAIRE: SUPPRESSION d'adresse.</font-->
<?php require("../lib/footer.inc.php");?>
