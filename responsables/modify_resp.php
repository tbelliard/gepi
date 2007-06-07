<?php
/*
 * Last modification  : 04/04/2005
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

/*
echo "\$is_posted=$is_posted<br />";
echo "\$_POST[is_posted]=".$_POST['is_posted']."<br />";
echo "\$_GET[is_posted]=".$_GET['is_posted']."<br />";
*/

if (isset($is_posted) and ($is_posted == '1')) {
	$msg="";

	//$adr_id_existant=isset($_POST['adr_id_existant']) ? $_POST['adr_id_existant'] : '';

	//echo "\$choisir_ad_existante=$choisir_ad_existante<br />";

	$choisir_ad_existante=isset($_POST['choisir_ad_existante']) ? $_POST['choisir_ad_existante'] : '';

	//echo "\$choisir_ad_existante=$choisir_ad_existante<br />";


	$ok='';
	if(($nom=='')||($prenom=='')){
		$ok='no';
	}
	else{
		if($choisir_ad_existante=='oui'){
			// On crée la personne si elle n'existe pas et on enchaine avec la page choix_adr_existante.php
			$ok='yes';
		}
		//elseif(($adr1 != '') and ($commune != '') and ($cp != '')){
		elseif(($adr1 != '') and ($commune != '') and (($cp != '')||($pays != ''))){
			$ok='yes';
		}
	}

	if($ok!='yes'){
		$msg = "Un ou plusieurs champs obligatoires sont vides !";
	}
	else{
		if(!isset($nouv_resp)){
			if(isset($pers_id)){
				$sql="UPDATE resp_pers SET nom='$nom',
								prenom='$prenom',
								civilite='$civilite',
								tel_pers='$tel_pers',
								tel_port='$tel_port',
								tel_prof='$tel_prof',
								mel='$mel'";
				/*
				//if($adr_id_existant!=""){
				if((isset($select_ad_existante))&&($adr_id_existant!="")){
					$adr_id=$adr_id_existant;
					$sql.=",adr_id='$adr_id'";
				}
				*/
				$sql.="WHERE pers_id='$pers_id'";
				//echo "$sql<br />\n";
				$res_update=mysql_query($sql);
				if(!$res_update){
					$msg.="Erreur lors de la mise à jour dans 'resp_pers'. ";
				} else {
					// On met également à jour la table utilisateurs si le responsable a un compte
					$test1_login = mysql_result(mysql_query("SELECT login FROM resp_pers WHERE pers_id = '$pers_id'"), 0);
					if ($test1_login != '') {
						$test2_login = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '".$test1_login."'"), 0);
						if ($test2_login == 1) {
							$res = mysql_query("UPDATE utilisateurs SET nom = '".$nom."', prenom = '" . $prenom . "', email = '" . $mel . "' WHERE login ='" . $test1_login ."'");
						}
					}
				}
			}

			// On n'insère pas les saisies des champs adr1, adr2,... si une adresse existante a été sélectionnée:
			//if($adr_id_existant==""){
			if($choisir_ad_existante==""){
				//echo "a<br />";
				if(isset($changement_adresse)){
					//echo "b<br />";
					if($changement_adresse=="desolidariser"){
						//echo "c<br />";
						// Recherche du plus grand adr_id
						$sql="SELECT adr_id FROM resp_adr WHERE adr_id LIKE 'a%' ORDER BY adr_id DESC";
						//echo "$sql<br />\n";
						$res1=mysql_query($sql);
						if(mysql_num_rows($res1)==0){
							//$adr_id="a1";
							$adr_id="a".sprintf("%09d","1");
						}
						else{
							$ligtmp=mysql_fetch_object($res1);
							$nb=substr($ligtmp->adr_id,1);
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
						$res_insert=mysql_query($sql);
						if(!$res_insert){
							$msg.="Erreur lors de l'insertion de la nouvelle adresse. ";
						}
						else{
							$sql="UPDATE resp_pers SET adr_id='$adr_id' ";
							$sql.="WHERE pers_id='$pers_id'";
							//echo "$sql<br />\n";
							$res_update=mysql_query($sql);
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
						$res_update=mysql_query($sql);
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
					$res_update=mysql_query($sql);
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
		}
		else{
			// Nouveau responsable:

			// Recherche du plus grand pers_id
			$sql="SELECT pers_id FROM resp_pers WHERE pers_id LIKE 'p%' ORDER BY pers_id DESC";
			//echo "$sql<br />\n";
			$res1=mysql_query($sql);
			if(mysql_num_rows($res1)==0){
				//$pers_id="p1";
				$pers_id="p".sprintf("%09d","1");
			}
			else{
				$ligtmp=mysql_fetch_object($res1);
				$nb=substr($ligtmp->pers_id,1);
				$nb++;
				//$pers_id="p".$nb;
				$pers_id="p".sprintf("%09d",$nb);
			}



			// Insertion du nouvel utilisateur dans resp_pers:
			$sql="INSERT INTO resp_pers SET pers_id='$pers_id',
								nom='$nom',
								prenom='$prenom',
								civilite='$civilite',
								tel_pers='$tel_pers',
								tel_port='$tel_port',
								tel_prof='$tel_prof',
								mel='$mel'";
			//echo "$sql<br />\n";
			$res_insert=mysql_query($sql);
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
					$res1=mysql_query($sql);
					if(mysql_num_rows($res1)==0){
						//$adr_id="a1";
						$adr_id="a".sprintf("%09d","1");
					}
					else{
						$ligtmp=mysql_fetch_object($res1);
						$nb=substr($ligtmp->adr_id,1);
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
						$res_insert=mysql_query($sql);
						if(!$res_insert){
							$msg.="Erreur lors de l'insertion de l'adresse dans 'resp_adr'. ";
						}
						else{
							$sql="UPDATE resp_pers SET adr_id='$adr_id' WHERE pers_id='$pers_id'";
							$res_update=mysql_query($sql);
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

				/*
				// Insertion du nouvel utilisateur dans resp_pers:
				$sql="INSERT INTO resp_pers SET pers_id='$pers_id',
									nom='$nom',
									prenom='$prenom',
									tel_pers='$tel_pers',
									tel_port='$tel_port',
									tel_prof='$tel_prof',
									mel='$mel',
									adr_id='$adr_id'";
				//echo "$sql<br />\n";
				$res_insert=mysql_query($sql);
				if(!$res_insert){
					$msg.="Erreur lors de l'insertion dans 'resp_pers'. ";
				}
				//$sql="SELECT adr_id";
				*/
			}
		}


		// Partie élèves:
		//if(isset($cpt)){
		if((isset($cpt))&&(isset($pers_id))&&($msg=='')){
			//echo "1<br />";
			for($i=0;$i<$cpt;$i++){
				//echo " $i<br />";
				if(isset($suppr_ele_id[$i])){
					//echo "\$suppr_ele_id[$i]=".$suppr_ele_id[$i]."<br />";
					$sql="DELETE FROM responsables2 WHERE pers_id='$pers_id' AND ele_id='$suppr_ele_id[$i]'";
					//echo "$sql<br />\n";
					$res_suppr=mysql_query($sql);
					if(!$res_suppr){
						$msg.="Erreur lors de la suppression de l'association avec l'élève $suppr_ele_id[$i] dans 'responsables2'. ";
					}
				}
				else{
					if(!isset($resp_erreur[$i])){
						if($resp_legal[$i]==1){$resp_legal2=2;}else{$resp_legal2=1;}

						/*
						//$sql="SELECT pers_id FROM responsables2 WHERE pers_id!='$pers_id' AND ";
						if(isset($_POST['pers_id2_'.$i])){
							$pers_id2=$_POST['pers_id2_'.$i];
							// PB: Est-on sûr de n'avoir que deux responsables?
							for($j=0;$j<count($pers_id2);$j++){
								$sql="UPDATE responsables2 SET resp_legal='$resp_legal2' WHERE pers_id='$pers_id2[$j]' AND ele_id='$ele_id[$i]'";
								//echo "$sql<br />\n";
								$res_update=mysql_query($sql);
							}
						}
						*/

						$temoin_erreur="non";
						if(isset($pers_id2[$i])){
							$sql="UPDATE responsables2 SET resp_legal='$resp_legal2' WHERE pers_id='$pers_id2[$i]' AND ele_id='$ele_id[$i]'";
							//echo "$sql<br />\n";
							$res_update=mysql_query($sql);
							if(!$res_update){
								$msg.="Erreur lors de la mise à jour de 'resp_legal' pour l'autre responsable ($pers_id2[$i]). ";
								$temoin_erreur="oui";
							}
						}

						if($temoin_erreur!="oui"){
							$sql="UPDATE responsables2 SET resp_legal='$resp_legal[$i]' WHERE pers_id='$pers_id' AND ele_id='$ele_id[$i]'";
							//echo "$sql<br />\n";
							$res_update=mysql_query($sql);
							if(!$res_update){
								$msg.="Erreur lors de la mise à jour de 'resp_legal' pour le responsable $pers_id. ";
							}
						}
					}
				}
			}
		}

		//if(isset($add_ele_id)){
		if((isset($add_ele_id))&&(isset($pers_id))&&($msg=='')){
			if($add_ele_id!=''){
				$sql="SELECT 1=1 FROM responsables2 WHERE pers_id!='$pers_id' AND ele_id='$add_ele_id'";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0){
					$resp_legal=1;
				}
				else{
					$sql="SELECT resp_legal FROM responsables2 WHERE ele_id='$add_ele_id'";
					$res_tmp=mysql_query($sql);
					$ligtmp=mysql_fetch_object($res_tmp);
					if($ligtmp->resp_legal==1){$resp_legal=2;}else{$resp_legal=1;}
				}

				$sql="INSERT INTO responsables2 SET pers_id='$pers_id', ele_id='$add_ele_id', resp_legal='$resp_legal'";
				$res_update=mysql_query($sql);
				if(!$res_update){
					$msg.="Erreur lors de l'ajout de l'élève $add_ele_id. ";
				}
			}
		}

		/*
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
		elseif($msg==""){
		*/
		if($msg==""){
			$msg="Enregistrement réussi.";
		}
	}
}



//**************** EN-TETE *******************************
$titre_page = "Ajouter ou modifier un responsable";
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
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href="modify_resp.php">Ajouter un responsable</a></p>

<form enctype="multipart/form-data" name="resp" action="modify_resp.php" method="post">
<?php
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
	$res_resp=mysql_query($sql);

	$lig_pers=mysql_fetch_object($res_resp);

	$nom=$lig_pers->nom;
	$prenom=$lig_pers->prenom;
	$civilite=$lig_pers->civilite;
	$tel_pers=$lig_pers->tel_pers;
	$tel_port=$lig_pers->tel_port;
	$tel_prof=$lig_pers->tel_prof;
	$mel=$lig_pers->mel;

	$sql="SELECT ra.* FROM resp_adr ra WHERE
					ra.adr_id='$lig_pers->adr_id'";
	//echo "$sql<br />\n";
	$res_adr=mysql_query($sql);
	if(mysql_num_rows($res_adr)>0){
		$lig_adr=mysql_fetch_object($res_adr);

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
if (!isset($nom)) $nom='';
if (!isset($prenom)) $prenom='';
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

echo "<table>\n";
echo "<tr>\n";
// Colonne nom, prénom, adresse, tel du responsable:
echo "<td valign='top'>\n";
	// Affichage du tableau de la saisie des nom, prenom, adresse, tel,...
	echo "<p><b>Responsable:</b>\n";
	if(isset($pers_id)){echo " (<i>n°$pers_id</i>)";}
	echo "</p>\n";

	echo "<table>\n";
	echo "<tr><td>Nom * : </td><td><input type=text size=50 name=nom value = \"".$nom."\" /></td></tr>\n";
	echo "<tr><td>Prénom * : </td><td><input type=text size=50 name=prenom value = \"".$prenom."\" /></td></tr>\n";
	echo "<tr><td>Civilité : </td><td>\n";

	echo "<table border='0'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	// AFFICHER AVEC JAVASCRIPT CE QUI EST ENREGISTRé/SAISI...
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='radio' name='civilite' value=\"\" ";
	if($civilite==""){echo "checked ";}
	echo "/> X \n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='radio' name='civilite' value=\"M.\" ";
	if($civilite=="M."){echo "checked ";}
	echo "/> M. \n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='radio' name='civilite' value=\"Mme\" ";
	if($civilite=="Mme"){echo "checked ";}
	echo "/> Mme \n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='radio' name='civilite' value=\"Mlle\" ";
	if($civilite=="Mlle"){echo "checked ";}
	echo "/> Mlle\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "</td></tr>\n";
	echo "<tr><td>Tel.perso : </td><td><input type=text size=15 name=tel_pers value = \"".$tel_pers."\" /></td></tr>\n";
	echo "<tr><td>Tel.portable : </td><td><input type=text size=15 name=tel_port value = \"".$tel_port."\" /></td></tr>\n";
	echo "<tr><td>Tel.professionnel : </td><td><input type=text size=15 name=tel_prof value = \"".$tel_prof."\" /></td></tr>\n";
	echo "<tr><td>Mel : </td><td><input type=text size=50 name=mel value = \"".$mel."\" /></td></tr>\n";
	echo "</table>\n";

echo "</td>\n";
// Colonne élève et conjoint:
echo "<td valign='top'>\n";


if(isset($pers_id)){
	// Enfants/élèves à charge:
	//$sql="SELECT DISTINCT ele_id FROM responsables2 WHERE pers_id='$pers_id'";
	$sql="SELECT e.nom,e.prenom,e.ele_id,r.resp_legal FROM responsables2 r, eleves e WHERE e.ele_id=r.ele_id AND r.pers_id='$pers_id' ORDER BY e.nom,e.prenom";
	//echo "$sql<br />\n";
	$res1=mysql_query($sql);
	if(mysql_num_rows($res1)==0){
		echo "<p>Ce responsable n'est encore rattaché à aucun élève.</p>\n";
	}
	else{
		echo "<p><b>Elève:</b></p>\n";
		echo "<table border='1'>\n";
		echo "<tr>\n";
		echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;' rowspan='2'>Elève</td>\n";
		echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;' colspan='2'>Responsable legal</td>\n";
		echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;' rowspan='2'>Supprimer</td>\n";
		echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;' rowspan='2'>Autre responsable</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>1</td>\n";
		echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>2</td>\n";
		echo "</tr>\n";
		$cpt=0;
		while($lig_ele=mysql_fetch_object($res1)){
			echo "<tr>\n";
			echo "<td style='text-align:center;'><input type='hidden' name='ele_id[$cpt]' value='$lig_ele->ele_id' />".ucfirst(strtolower($lig_ele->prenom))." ".strtoupper($lig_ele->nom)."</td>\n";

			$resp_legal1=$lig_ele->resp_legal;

			// Y a-t-il un deuxième responsable?
			$sql="SELECT rp.nom,rp.prenom,rp.pers_id FROM resp_pers rp, responsables2 r WHERE rp.pers_id!='$pers_id' AND r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' AND (r.resp_legal='1' OR r.resp_legal='2')";
			//echo "$sql<br />\n";
			$res_resp=mysql_query($sql);

			// S'il n'y a pas de deuxième responsable et que le responsable déclaré n'est pas le n°1:
			if((mysql_num_rows($res_resp)==0)&&($resp_legal1!=1)){
				$tmpbg="background-color:red;";
			}
			else{
				$tmpbg="";
			}

			echo "<td style='text-align:center;$tmpbg'><input type='radio' name='resp_legal[$cpt]' value='1'";
			if($resp_legal1==1){echo " checked";}
			echo " /></td>\n";

			echo "<td style='text-align:center;'>";
			if(mysql_num_rows($res_resp)>0){
				echo "<input type='radio' name='resp_legal[$cpt]' value='2'";
				if($resp_legal1!=1){echo " checked";}
				echo " />";
			}
			echo "</td>\n";

			echo "<td style='text-align:center;'><input type='checkbox' name='suppr_ele_id[$cpt]' value='$lig_ele->ele_id' /></td>\n";

			echo "<td style='text-align:center;'>\n";
			//$sql="SELECT rp.nom,rp.prenom,rp.pers_id FROM resp_pers rp, responsables2 r WHERE rp.pers_id!='$pers_id' AND r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' AND (r.resp_legal='1' OR r.resp_legal='2')";
			//echo "$sql<br />\n";
			//$res_resp=mysql_query($sql);
			/*
			if(mysql_num_rows($res_resp)>0){
				while($lig_resp=mysql_fetch_object($res_resp)){
					echo "<a href='modify_resp.php?pers_id=$lig_resp->pers_id'>".strtoupper($lig_resp->nom)." ".ucfirst(strtolower($lig_resp->prenom))."</a>\n";
					//echo "<input type='hidden' name='pers_id[]' value='$lig_resp->pers_id' />\n";
					echo "<input type='hidden' name='pers_id2_".$cpt."[]' value='$lig_resp->pers_id' />\n";
				}
			}
			*/
			if(mysql_num_rows($res_resp)>0){
				if(mysql_num_rows($res_resp)==1){
					$lig_resp=mysql_fetch_object($res_resp);
					echo "<a href='modify_resp.php?pers_id=$lig_resp->pers_id'>".strtoupper($lig_resp->nom)." ".ucfirst(strtolower($lig_resp->prenom))."</a>\n";
					//echo "<input type='hidden' name='pers_id[]' value='$lig_resp->pers_id' />\n";
					echo "<input type='hidden' name='pers_id2[".$cpt."]' value='$lig_resp->pers_id' />\n";
				}
				else{
					echo "<input type='hidden' name='resp_erreur[".$cpt."]' value='y' />\n";
					echo "<font color='red'>L'élève a trop de responsables légaux. Faites le ménage!</font><br />\n";
					while($lig_resp=mysql_fetch_object($res_resp)){
						echo "<a href='modify_resp.php?pers_id=$lig_resp->pers_id'>".strtoupper($lig_resp->nom)." ".ucfirst(strtolower($lig_resp->prenom))."</a><br />\n";
					}
				}
			}
			echo "</td>\n";

			echo "</tr>\n";
			$cpt++;
		}
		echo "</table>\n";
		echo "<input type='hidden' name='cpt' value='$cpt' />\n";
		echo "<hr />\n";
	}
}

// Ajout de l'association avec un élève existant:
//echo "Ajouter un élève: ";
echo "<p align='center'>\n";
echo "<select name='add_ele_id'>\n";
echo "<option value=''>--- Ajouter un élève ---</option>\n";
//$sql="SELECT ele_id,nom,prenom FROM eleves ORDER BY nom,prenom";

/*
// *************************
// A REVOIR: Si aucun responsable n'est encore saisi, on ne récupère pas l'ensemble des élèves...
//           Le mode de "détection/listage" n'est pas valide.
// *************************
$sql="SELECT DISTINCT e.ele_id,e.nom,e.prenom FROM eleves e, responsables2 r WHERE e.ele_id=r.ele_id ORDER BY e.nom,e.prenom";
$res_ele=mysql_query($sql);
$compteur=0;
while($lig_ele=mysql_fetch_object($res_ele)){
	// On ne propose que les élèves n'ayant pas déjà leurs deux responsables légaux
	//$sql="SELECT * FROM responsables2 WHERE ele_id='$lig_ele->ele_id'";
	$sql="SELECT * FROM responsables2 WHERE ele_id='$lig_ele->ele_id' AND (resp_legal='1' OR resp_legal='2')";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)<2){
		echo "<option value='$lig_ele->ele_id'>".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))."</option>\n";
		$compteur++;
	}
}
*/

$sql="SELECT DISTINCT e.ele_id,e.nom,e.prenom FROM eleves e ORDER BY e.nom,e.prenom";
$res_ele=mysql_query($sql);
$compteur=0;
while($lig_ele=mysql_fetch_object($res_ele)){
	// On ne propose que les élèves n'ayant pas déjà leurs deux responsables légaux
	//$sql="SELECT * FROM responsables2 WHERE ele_id='$lig_ele->ele_id'";
	$sql="SELECT * FROM responsables2 WHERE ele_id='$lig_ele->ele_id' AND (resp_legal='1' OR resp_legal='2')";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)<2){
		echo "<option value='$lig_ele->ele_id'>".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))."</option>\n";
		$compteur++;
	}
}

echo "</select>\n";
echo "<br />\n(<i>$compteur élèves n'ont pas leurs deux responsables légaux</i>)\n";
echo "</p>\n";

echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";


//==============================================

echo "<p><b>Adresse:</b>";
if(isset($adr_id)){echo " (<i>n°$adr_id</i>)";}
echo "</p>\n";

echo "<div id='div_saisie_ad'>\n";
if($temoin_adr==1){
	$sql="SELECT * FROM resp_pers WHERE adr_id='$adr_id'";
	//echo "$sql<br />\n";
	$res_adr=mysql_query($sql);
	if(mysql_num_rows($res_adr)==0){
		// Bizarre!
		// Ce n'est pas possible d'après ce qui a été fait auparavant.
	}
	elseif(mysql_num_rows($res_adr)==1){
		// L'adresse n'est associée qu'au responsable courant.
		echo "<p>Corriger/modifier l'adresse:</p>\n";
	}
	else{
		// L'adresse n'est associée à au moins un autre responsable.
		echo "<table><tr><td><b>Attention:</b></td><td>L'adresse indiquée ci-dessous est partagée avec un autre responsable.</td></tr>\n";
		//<br />\nSi vous modifiez l'adresse, elle le sera pour l'autre responsable également.</p>\n";
		echo "<tr><td>&nbsp;</td><td><input type='radio' name='changement_adresse' value='corriger' checked /> Corriger/modifier l'adresse commune aux deux responsables, <b>ou</b></td></tr>\n";
		//echo "<tr><td>&nbsp;</td><td>ou</td></tr>\n";
		echo "<tr><td>&nbsp;</td><td><input type='radio' name='changement_adresse' value='desolidariser' /> Désolidariser l'adresse de celle de l'autre responsable.</td></tr></table>\n";
	}
}
else{
	echo "<p>Saisir une adresse:</p>\n";
}

echo "<table>\n";
//echo "<tr><td colspan='2'>Saisir une adresse</td></tr>\n";
echo "<tr><td>Adresse * : </td><td><input type=text size=50 name=adr1 value = \"".$adr1."\" /></td></tr>\n";
echo "<tr><td>Adresse (<i>suite</i>): </td><td><input type=text size=50 name=adr2 value = \"".$adr2."\" /></td></tr>\n";
echo "<tr><td>Adresse (<i>suite</i>): </td><td><input type=text size=50 name=adr3 value = \"".$adr3."\" /></td></tr>\n";
echo "<tr><td>Adresse (<i>suite</i>): </td><td><input type=text size=50 name=adr4 value = \"".$adr4."\" /></td></tr>\n";
echo "<tr><td>Code postal ** : </td><td><input type=text size=6 name=cp value = \"".$cp."\" />";
echo " ou Pays ** : <input type=text size=20 name=pays value = \"".$pays."\" />\n";
echo "</td></tr>\n";
echo "<tr><td>Commune * : </td><td><input type=text size=50 name=commune value = \"".$commune."\" /></td></tr>\n";
//echo "<tr><td>Pays : </td><td><input type=text size=50 name=pays value = \"".$pays."\" /></td></tr>\n";

/*
$sql="SELECT DISTINCT adr1,adr2,adr3,adr4,cp,commune,adr_id FROM resp_adr ORDER BY commune,cp,adr1,adr2,adr3,adr4";
$res_adr=mysql_query($sql);
if(mysql_num_rows($res_adr)>0){
	echo "<tr><td><b>ou</b></td>";
	echo "<td><select name='adr_id_existant'>\n";
	echo "<option value=''>--- Sélectionner une adresse existante ---</option>\n";
	while($lig_adr=mysql_fetch_object($res_adr)){
		if(($lig_adr->adr1!="")||($lig_adr->adr2!="")||($lig_adr->adr3!="")||($lig_adr->adr4!="")||($lig_adr->commune!="")){
			echo "<option value='$lig_adr->adr_id'>";

			$sql="SELECT nom,prenom FROM resp_pers WHERE adr_id='$lig_adr->adr_id'";
			$res_pers=mysql_query($sql);
			if(mysql_num_rows($res_pers)>0){
				$ligtmp=mysql_fetch_object($res_pers);
				$chaine=strtoupper($ligtmp->nom)." ".ucfirst(strtolower($ligtmp->prenom));
				while($ligtmp=mysql_fetch_object($res_pers)){
					$chaine.=",".strtoupper($ligtmp->nom)." ".ucfirst(strtolower($ligtmp->prenom));
				}
				echo "($chaine)";
			}
			//echo "-adr_id=".$lig_adr->adr_id."-";

			if($lig_adr->adr1!=""){echo $lig_adr->adr1."-";}
			if($lig_adr->adr2!=""){echo $lig_adr->adr2."-";}
			if($lig_adr->adr3!=""){echo $lig_adr->adr3."-";}
			if($lig_adr->adr4!=""){echo $lig_adr->adr4."-";}
			if($lig_adr->cp!=""){echo $lig_adr->cp."-";}
			if($lig_adr->commune!=""){echo $lig_adr->commune;}
			//$lig_adr->adr2."-".$lig_adr->adr3."-".$lig_adr->adr4."-".$lig_adr->cp."-".$lig_adr->commune.
			echo "</option>\n";
		}
	}
	echo "</select>\n";
	echo "</td></tr>\n";
}
*/
echo "</table>\n";

if(isset($pers_id)){
	echo "<p>Ou <a href='choix_adr_existante.php?pers_id=$pers_id";
	if(isset($adr_id)){
		echo "&amp;adr_id_actuel=$adr_id";
	}
	echo "'>Choisir une adresse existante.</a></p>\n";
}
else{
	//echo "<p>Ou <a href='".$_SERVER['PHP_SELF']."?choisir_adr_existante=oui' onClick=''>Choisir une adresse existante.</a></p>";

	echo "<script type='text/javascript'>
	function creer_pers_id_puis_choisir_adr_exist(){
		document.forms.resp.choisir_ad_existante.value='oui';
		//setTimeout('document.forms.resp.submit()',5000);
		document.forms.resp.submit();
	}
</script>\n";

	echo "<p>Ou <a href='".$_SERVER['PHP_SELF']."' onClick='creer_pers_id_puis_choisir_adr_exist();return false;'>Choisir une adresse existante.</a></p>";
	echo "<input type='hidden' name='choisir_ad_existante' value='' />";
}

echo "</div>\n";


echo "<center><input type='submit' value='Enregistrer' /></center>\n";

echo "<p>(*): saisie obligatoire<br />(**): un des deux champs au moins doit être rempli</p>\n";

/*
$sql="SELECT DISTINCT adr1,adr2,adr3,adr4,cp,commune,pays,adr_id FROM resp_adr ORDER BY commune,cp,adr1,adr2,adr3,adr4";
$res_adr=mysql_query($sql);
if(mysql_num_rows($res_adr)>0){
	echo "<b>ou</b> <input type='checkbox' name='select_ad_existante' id='select_ad_existante' value='y' onchange='modif_div_ad()' /> Sélectionner une adresse existante.";

	echo "<div id='div_ad_existante'>\n";
	echo "<table border='1'>\n";
	echo "<tr>\n";
	echo "<td style='text-align:center; font-weight:bold;'>&nbsp;</td>\n";
	echo "<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Identifiant</td>\n";
	echo "<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Lignes de l'adresse</td>\n";
	echo "<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Code postal</td>\n";
	echo "<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>";
	echo "Commune";
*/

/*
	// AJOUTER un champ SELECT avec javascript pour n'afficher que telle ou telle commune
	$sql="SELECT DISTINCT commune FROM resp_adr ORDER BY commune";
	$res_comm=mysql_query($sql);
	if(mysql_num_rows($res_comm)>1){
		echo "<select name='select_commune' onchange='select_commune()'>\n";
		echo "<option value=''>--- Toutes ---</option>\n";
		while($lig_comm=mysql_fetch_object($res_comm)){
			echo "<option value='$lig_comm->commune'>$lig_comm->commune</option>\n";
		}
		echo "</select>\n";
	}
*/
/*
	echo "</td>\n";
	echo "<td style='text-align:center; font-weight:bold; background-color:#AAE6AA;'>Pays</td>\n";
	echo "<td style='text-align:center; font-weight:bold; background-color:#96C8F0;'>Responsable associé</td>\n";
	echo "<td style='text-align:center; font-weight:bold; background-color:red;'>Supprimer adresse(s)</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td style='text-align:center;'><input type='radio' name='adr_id_existant' value='' checked /></td>\n";
	echo "<td style='text-align:center; background-color:#FAFABE;' colspan='7'>Ne pas utiliser une adresse existante</td>\n";
	echo "</tr>\n";

	while($lig_adr=mysql_fetch_object($res_adr)){
		if(($lig_adr->adr1!="")||($lig_adr->adr2!="")||($lig_adr->adr3!="")||($lig_adr->adr4!="")||($lig_adr->commune!="")){
			echo "<tr>\n";
			echo "<td style='text-align:center;'><input type='radio' name='adr_id_existant' value='$lig_adr->adr_id' /></td>\n";
			echo "<td style='text-align:center;'>$lig_adr->adr_id</td>\n";
			echo "<td style='text-align:center;'>\n";
			if($lig_adr->adr1!=""){echo $lig_adr->adr1;}
			if($lig_adr->adr2!=""){echo "-".$lig_adr->adr2;}
			if($lig_adr->adr3!=""){echo "-".$lig_adr->adr3;}
			if($lig_adr->adr4!=""){echo "-".$lig_adr->adr4;}
			echo "</td>\n";
			echo "<td style='text-align:center;'>$lig_adr->cp</td>\n";
			echo "<td style='text-align:center;'>$lig_adr->commune</td>\n";
			echo "<td style='text-align:center;'>$lig_adr->pays</td>\n";

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
		}
	}

	echo "</table>\n";
	echo "<center><input type='submit' value='Enregistrer' /></center>\n";
	echo "</div>\n";


	echo "<script type='text/javascript'>
	//function modif_div_ad(mode){
	//	if(mode=='cacher'){
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
	document.getElementById('div_ad_existante').style.display='none';
</script>\n";

}
*/

echo "<input type='hidden' name='is_posted' value='1' />\n";
?>
</form>

<!--font color='red'>A FAIRE: SUPPRESSION d'adresse.</font-->
<?php require("../lib/footer.inc.php");?>