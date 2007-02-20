<?php
/*
 * Last modification  : 04/10/2006
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//if(isset($suppr_resp)){
if((isset($suppr_resp1))||(isset($suppr_resp2))){
	$msg="";

	if(isset($suppr_resp1)){
		$suppr_resp=$suppr_resp1;
		for($i=0;$i<count($suppr_resp);$i++){
			$sql="DELETE FROM responsables2 WHERE pers_id='$suppr_resp[$i]' AND resp_legal='1'";
			//echo "$sql<br />\n";
			$res0=mysql_query($sql);
			if($res0){
				// Est-ce que ce responsable est encore responsable de quelqu'un?
				$sql="SELECT 1=1 FROM responsables2 WHERE pers_id='$suppr_resp[$i]'";
				//echo "$sql<br />\n";
				$res_test=mysql_query($sql);
				if(mysql_num_rows($res_test)==0){
					// On vérifie que la personne existe et on en récupère l'identifiant d'adresse (éventuellement vide)
					$sql="SELECT adr_id FROM resp_pers WHERE pers_id='$suppr_resp[$i]'";
					//echo "$sql<br />\n";
					$res1=mysql_query($sql);
					if(mysql_num_rows($res1)>0){
						$lig1=mysql_fetch_object($res1);
						$sql="DELETE FROM resp_pers WHERE pers_id='$suppr_resp[$i]'";
						//echo "$sql<br />\n";
						$res2=mysql_query($sql);
						if(!$res2){
							$msg.="Erreur lors de la suppression du responsable $suppr_resp[$i] de la table 'resp_pers'.<br />\n";
						}
						else{
							$sql="SELECT 1=1 FROM resp_pers WHERE adr_id='$lig1->adr_id'";
							//echo "$sql<br />\n";
							$res3=mysql_query($sql);
							if(mysql_num_rows($res3)==0){
								$sql="DELETE FROM resp_adr WHERE adr_id='$lig1->adr_id'";
								//echo "$sql<br />\n";
								$res4=mysql_query($sql);
								if(!$res4){
									$msg.="Erreur lors de la suppression de l'adresse $lig1->adr_id de la table 'resp_adr'.<br />\n";
								}
							}
						}
					}
				}
			}
			else{
				$msg.="Erreur lors de la suppression du responsable $suppr_resp[$i] de la table 'responsables2'.<br />";
			}
		}
	}

	if(isset($suppr_resp)){
		unset($suppr_resp);
	}

	if(isset($suppr_resp2)){
		$suppr_resp=$suppr_resp2;
		for($i=0;$i<count($suppr_resp);$i++){
			$sql="DELETE FROM responsables2 WHERE pers_id='$suppr_resp[$i]' AND resp_legal='2'";
			//echo "$sql<br />\n";
			$res0=mysql_query($sql);
			if($res0){
				// Est-ce que ce responsable est encore responsable de quelqu'un?
				$sql="SELECT 1=1 FROM responsables2 WHERE pers_id='$suppr_resp[$i]'";
				//echo "$sql<br />\n";
				$res_test=mysql_query($sql);
				if(mysql_num_rows($res_test)==0){
					// On vérifie que la personne existe et on en récupère l'identifiant d'adresse (éventuellement vide)
					$sql="SELECT adr_id FROM resp_pers WHERE pers_id='$suppr_resp[$i]'";
					//echo "$sql<br />\n";
					$res1=mysql_query($sql);
					if(mysql_num_rows($res1)>0){
						$lig1=mysql_fetch_object($res1);
						$sql="DELETE FROM resp_pers WHERE pers_id='$suppr_resp[$i]'";
						//echo "$sql<br />\n";
						$res2=mysql_query($sql);
						if(!$res2){
							$msg.="Erreur lors de la suppression du responsable $suppr_resp[$i] de la table 'resp_pers'.<br />\n";
						}
						else{
							$sql="SELECT 1=1 FROM resp_pers WHERE adr_id='$lig1->adr_id'";
							//echo "$sql<br />\n";
							$res3=mysql_query($sql);
							if(mysql_num_rows($res3)==0){
								$sql="DELETE FROM resp_adr WHERE adr_id='$lig1->adr_id'";
								//echo "$sql<br />\n";
								$res4=mysql_query($sql);
								if(!$res4){
									$msg.="Erreur lors de la suppression de l'adresse $lig1->adr_id de la table 'resp_adr'.<br />\n";
								}
							}
						}
					}
				}
			}
			else{
				$msg.="Erreur lors de la suppression du responsable $suppr_resp[$i] de la table 'responsables2'.<br />";
			}
		}
	}


	if($msg==''){
		$msg="Suppression(s) réussie(s).";
	}
}

//**************** EN-TETE *****************
$titre_page = "Gestion des responsables élèves";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

if(!getSettingValue('conv_new_resp_table')){
	echo "<p>Une conversion des données responsables est requise.</p>\n";
	echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
	echo "</body>\n";
	echo "</html>\n";
	die();
}

echo "<p class=bold>";
if ($_SESSION['statut'] == 'administrateur'){
	echo "|<a href=\"../accueil_admin.php\">Retour</a>";
	echo "|<a href=\"modify_resp.php\">Ajouter un responsable</a>\n";
	if(getSettingValue("import_maj_xml_sconet")==1){
		echo "|<a href=\"maj_import.php\">Mettre à jour depuis Sconet</a>\n";
	}
}
else{
	echo "|<a href=\"../accueil.php\">Retour</a>";
}
echo "|</p>\n";

$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];

//if (!isset($order_by)) {$order_by = "nom1,prenom1";}
if(!isset($order_by)) {$order_by = "nom,prenom";$num_resp=1;}

$num_resp=isset($_POST['num_resp']) ? $_POST['num_resp'] : (isset($_GET['num_resp']) ? $_GET['num_resp'] : 1);

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
echo "<center><input type='submit' value='Valider' /></center>\n";
echo "<table border='1'>\n";

$ligne_titre="";
$ligne_titre.="<tr>\n";
//$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;' colspan='2'>Responsable légal 1</td>\n";
$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;' colspan='3'>Responsable légal 1</td>\n";
//$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#FAFABE;' rowspan='2'><a href='index.php?order_by=nom,prenom&amp;tri=ele'>Elève(s)</a></td>\n";
$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#FAFABE;' rowspan='2'><a href='index.php?order_by=nom,prenom&amp;num_resp=ele'>Elève(s)</a></td>\n";
//$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#96C8F0;' colspan='2'>Responsable légal 2</td>\n";
$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#96C8F0;' colspan='3'>Responsable légal 2</td>\n";
$ligne_titre.="</tr>\n";
$ligne_titre.="<tr>\n";
$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'><a href='index.php?order_by=nom,prenom&amp;num_resp=1'>Nom prénom</a></td>\n";
$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>Adresse</td>\n";
$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>Supprimer</td>\n";
//$ligne_titre.="<td>Elève</td>\n";
$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'><a href='index.php?order_by=nom,prenom&amp;num_resp=2'>Nom prénom</a></td>\n";
$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'>Adresse</td>\n";
$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'>Supprimer</td>\n";
$ligne_titre.="</tr>\n";

if(($order_by=="nom,prenom")&&($num_resp==1)){
	// Pour ne récupérer qu'une seule occurence de pers_id:
	$sql="SELECT DISTINCT r.pers_id FROM resp_pers rp, responsables2 r WHERE
			rp.pers_id=r.pers_id AND
			r.resp_legal='$num_resp'
		ORDER BY $order_by";
	$res1=mysql_query($sql);

	if(mysql_num_rows($res1)){
		$cpt=0;
		while($lig1=mysql_fetch_object($res1)){

			if($cpt%10==0){
				echo $ligne_titre;
			}

			if($cpt%2==0){
				$alt='silver';
			}
			else{
				$alt='white';
			}

			if($num_resp==1){$autre_resp=2;}else{$autre_resp=1;}

			$sql="SELECT rp.nom,rp.prenom,ra.* FROM resp_pers rp, resp_adr ra WHERE
									rp.adr_id=ra.adr_id AND
									rp.pers_id='$lig1->pers_id'
								 ORDER BY $order_by";
			$res2=mysql_query($sql);
			if(mysql_num_rows($res2)>0){
				while($lig2=mysql_fetch_object($res2)){
					$sql="SELECT DISTINCT e.ele_id,e.login,e.nom,e.prenom FROM responsables2 r, eleves e WHERE r.pers_id='$lig1->pers_id' AND r.resp_legal='$num_resp' AND r.ele_id=e.ele_id";
					$res3=mysql_query($sql);
					//if(mysql_num_rows($res3)>0){
						echo "<tr style='background-color:".$alt.";'>\n";
						echo "<td style='text-align:center;'";
						if(mysql_num_rows($res3)>1){
							echo " rowspan='".mysql_num_rows($res3)."'";
						}
						echo ">\n";
						echo "<a href='modify_resp.php?pers_id=$lig1->pers_id'>$lig2->nom $lig2->prenom</a>\n";
						echo "</td>\n";

						echo "<td style='text-align:center;'";
						if(mysql_num_rows($res3)>1){
							echo " rowspan='".mysql_num_rows($res3)."'";
						}
						echo ">\n";
						if($lig2->adr1!=''){echo "$lig2->adr1\n";}
						if($lig2->adr2!=''){echo "<br />\n$lig2->adr2\n";}
						if($lig2->adr3!=''){echo "<br />\n$lig2->adr3\n";}
						if($lig2->adr4!=''){echo "<br />\n$lig2->adr4\n";}
						if(($lig2->commune!='')||($lig2->cp!='')){echo "<br />\n$lig2->cp $lig2->commune\n";}
						if($lig2->pays!=''){echo "<br />\n$lig2->pays\n";}
						echo "</td>\n";


						echo "<td style='text-align:center;'";
						if(mysql_num_rows($res3)>1){
							echo " rowspan='".mysql_num_rows($res3)."'";
						}
						echo ">\n";
						echo "<input type='checkbox' name='suppr_resp1[]' value='$lig1->pers_id' />";
						echo "</td>\n";


						if(mysql_num_rows($res3)>0){
							$cpt_temoin=0;
							while($lig3=mysql_fetch_object($res3)){
								if($cpt_temoin>0){
									echo "<tr style='background-color:".$alt.";'>\n";
								}
								echo "<td style='text-align:center;'><a href='../eleves/modify_eleve.php?eleve_login=$lig3->login&amp;quelles_classes=toutes&amp;order_type=nom,prenom'>$lig3->nom $lig3->prenom</a></td>\n";

								$sql="SELECT rp.nom,rp.prenom,r.*,ra.* FROM resp_pers rp, responsables2 r, resp_adr ra WHERE
									rp.pers_id=r.pers_id AND
									rp.adr_id=ra.adr_id AND
									r.ele_id='$lig3->ele_id' AND
									r.resp_legal=$autre_resp";
								$res4=mysql_query($sql);
								if(mysql_num_rows($res4)>0){
									while($lig4=mysql_fetch_object($res4)){
										echo "<td style='text-align:center;'>\n";
										echo "<a href='modify_resp.php?pers_id=$lig4->pers_id'>$lig4->nom $lig4->prenom</a>\n";
										echo "</td>\n";

										echo "<td style='text-align:center;'>\n";
										if($lig4->adr1!=''){echo "$lig4->adr1\n";}
										if($lig4->adr2!=''){echo "<br />\n$lig4->adr2\n";}
										if($lig4->adr3!=''){echo "<br />\n$lig4->adr3\n";}
										if($lig4->adr4!=''){echo "<br />\n$lig4->adr4\n";}
										if(($lig4->commune!='')||($lig4->cp!='')){echo "<br />\n$lig4->cp $lig4->commune\n";}
										if($lig4->pays!=''){echo "<br />\n$lig4->pays\n";}
										echo "</td>\n";


										echo "<td style='text-align:center;'><input type='checkbox' name='suppr_resp2[]' value='$lig4->pers_id' /></td>\n";
									}
								}
								else{
									echo "<td>&nbsp;</td>\n";
									echo "<td>&nbsp;</td>\n";
									echo "<td>&nbsp;</td>\n";
								}
								echo "</tr>\n";
								$cpt_temoin++;
							}
						}
						else{
							echo "<td>&nbsp;</td>\n";
							echo "<td>&nbsp;</td>\n";
							echo "<td>&nbsp;</td>\n";
							echo "<td>&nbsp;</td>\n";
							echo "</tr>\n";
						}
					//}
				}
			}
			$cpt++;
		}
	}
}
elseif(($order_by=="nom,prenom")&&($num_resp==2)){
	// Pour ne récupérer qu'une seule occurence de pers_id:
	$sql="SELECT DISTINCT r.pers_id FROM resp_pers rp, responsables2 r WHERE
			rp.pers_id=r.pers_id AND
			r.resp_legal='$num_resp'
		ORDER BY $order_by";
	$res1=mysql_query($sql);

	if(mysql_num_rows($res1)){
		$cpt=0;
		while($lig1=mysql_fetch_object($res1)){

			if($cpt%10==0){
				echo $ligne_titre;
			}

			if($cpt%2==0){
				$alt='silver';
			}
			else{
				$alt='white';
			}

			if($num_resp==1){$autre_resp=2;}else{$autre_resp=1;}

			$sql="SELECT rp.nom,rp.prenom,ra.* FROM resp_pers rp, resp_adr ra WHERE
									rp.adr_id=ra.adr_id AND
									rp.pers_id='$lig1->pers_id'
								 ORDER BY $order_by";
			$res2=mysql_query($sql);
			if(mysql_num_rows($res2)>0){
				while($lig2=mysql_fetch_object($res2)){
					$sql="SELECT DISTINCT e.ele_id,e.login,e.nom,e.prenom FROM responsables2 r, eleves e WHERE r.pers_id='$lig1->pers_id' AND r.resp_legal='$num_resp' AND r.ele_id=e.ele_id";
					$res3=mysql_query($sql);
					//if(mysql_num_rows($res3)>0){
						echo "<tr style='background-color:".$alt.";'>\n";



						if(mysql_num_rows($res3)>0){
							$cpt_temoin=0;
							while($lig3=mysql_fetch_object($res3)){
								if($cpt_temoin>0){
									echo "<tr style='background-color:".$alt.";'>\n";
								}


								$sql="SELECT rp.nom,rp.prenom,r.*,ra.* FROM resp_pers rp, responsables2 r, resp_adr ra WHERE
									rp.pers_id=r.pers_id AND
									rp.adr_id=ra.adr_id AND
									r.ele_id='$lig3->ele_id' AND
									r.resp_legal=$autre_resp";
								$res4=mysql_query($sql);
								if(mysql_num_rows($res4)>0){
									while($lig4=mysql_fetch_object($res4)){
										echo "<td style='text-align:center;'>\n";
										echo "<a href='modify_resp.php?pers_id=$lig4->pers_id'>$lig4->nom $lig4->prenom</a>\n";
										echo "</td>\n";

										echo "<td style='text-align:center;'>\n";
										if($lig4->adr1!=''){echo "$lig4->adr1\n";}
										if($lig4->adr2!=''){echo "<br />\n$lig4->adr2\n";}
										if($lig4->adr3!=''){echo "<br />\n$lig4->adr3\n";}
										if($lig4->adr4!=''){echo "<br />\n$lig4->adr4\n";}
										if(($lig4->commune!='')||($lig4->cp!='')){echo "<br />\n$lig4->cp $lig4->commune\n";}
										if($lig4->pays!=''){echo "<br />\n$lig4->pays\n";}
										echo "</td>\n";
									}

									echo "<td style='text-align:center;'>\n";
									echo "<input type='checkbox' name='suppr_resp1[]' value='$lig4->pers_id' />";
									echo "</td>\n";


								}
								else{
									echo "<td>&nbsp;</td>\n";
									echo "<td>&nbsp;</td>\n";
									echo "<td>&nbsp;</td>\n";
								}

								echo "<td style='text-align:center;'><a href='../eleves/modify_eleve.php?eleve_login=$lig3->login&amp;quelles_classes=toutes&amp;order_type=nom,prenom'>$lig3->nom $lig3->prenom</a></td>\n";



								if($cpt_temoin==0){
									echo "<td style='text-align:center;'";
									if(mysql_num_rows($res3)>1){
										echo " rowspan='".mysql_num_rows($res3)."'";
									}
									echo ">\n";
									echo "<a href='modify_resp.php?pers_id=$lig1->pers_id'>$lig2->nom $lig2->prenom</a>\n";
									echo "</td>\n";

									echo "<td style='text-align:center;'";
									if(mysql_num_rows($res3)>1){
										echo " rowspan='".mysql_num_rows($res3)."'";
									}
									echo ">\n";
									if($lig2->adr1!=''){echo "$lig2->adr1\n";}
									if($lig2->adr2!=''){echo "<br />\n$lig2->adr2\n";}
									if($lig2->adr3!=''){echo "<br />\n$lig2->adr3\n";}
									if($lig2->adr4!=''){echo "<br />\n$lig2->adr4\n";}
									if(($lig2->commune!='')||($lig2->cp!='')){echo "<br />\n$lig2->cp $lig2->commune\n";}
									if($lig2->pays!=''){echo "<br />\n$lig2->pays\n";}
									echo "</td>\n";

									echo "<td style='text-align:center;'";
									if(mysql_num_rows($res3)>1){
										echo " rowspan='".mysql_num_rows($res3)."'";
									}
									echo ">\n";
									echo "<input type='checkbox' name='suppr_resp2[]' value='$lig1->pers_id' />";
									echo "</td>\n";
								}

								echo "</tr>\n";
								$cpt_temoin++;
							}
						}
						else{
							echo "<td>&nbsp;</td>\n";
							echo "<td>&nbsp;</td>\n";
							echo "<td>&nbsp;</td>\n";

							echo "<td>&nbsp;</td>\n";

							echo "<td style='text-align:center;'";
							if(mysql_num_rows($res3)>1){
								echo " rowspan='".mysql_num_rows($res3)."'";
							}
							echo ">\n";
							echo "<a href='modify_resp.php?pers_id=$lig1->pers_id'>$lig2->nom $lig2->prenom</a>\n";
							echo "</td>\n";

							echo "<td style='text-align:center;'";
							if(mysql_num_rows($res3)>1){
								echo " rowspan='".mysql_num_rows($res3)."'";
							}
							echo ">\n";
							if($lig2->adr1!=''){echo "$lig2->adr1\n";}
							if($lig2->adr2!=''){echo "<br />\n$lig2->adr2\n";}
							if($lig2->adr3!=''){echo "<br />\n$lig2->adr3\n";}
							if($lig2->adr4!=''){echo "<br />\n$lig2->adr4\n";}
							if(($lig2->commune!='')||($lig2->cp!='')){echo "<br />\n$lig2->cp $lig2->commune\n";}
							if($lig2->pays!=''){echo "<br />\n$lig2->pays\n";}
							echo "</td>\n";

							echo "<td style='text-align:center;'";
							if(mysql_num_rows($res3)>1){
								echo " rowspan='".mysql_num_rows($res3)."'";
							}
							echo ">\n";
							echo "<input type='checkbox' name='suppr_resp2[]' value='$lig1->pers_id' />";
							echo "</td>\n";

							echo "</tr>\n";
						}






					//}
				}
			}
			$cpt++;
		}
	}



}
/*
elseif(($order_by=="commune,adr1,adr2,adr3,adr4")&&($num_resp==1)){
}
elseif(($order_by=="commune,adr1,adr2,adr3,adr4")&&($num_resp==2)){
}
*/
//elseif(($order_by=="nom,prenom")&&($_GET['tri']=="ele")){
elseif(($order_by=="nom,prenom")&&($num_resp=="ele")){
	$sql="SELECT DISTINCT r.ele_id,e.nom,e.prenom,e.login FROM responsables2 r, eleves e WHERE e.ele_id=r.ele_id ORDER BY e.nom,e.prenom";
	$res1=mysql_query($sql);

	//echo "<tr><td colspan='5'>AAA</td></tr>\n";
	if(mysql_num_rows($res1)>0){
		$cpt=0;
		while($lig1=mysql_fetch_object($res1)){

			if($cpt%10==0){
				echo $ligne_titre;
			}

			if($cpt%2==0){
				$alt='silver';
			}
			else{
				$alt='white';
			}

			$sql="SELECT rp.nom,rp.prenom,rp.pers_id,ra.* FROM resp_pers rp, resp_adr ra, responsables2 r WHERE
					r.pers_id=rp.pers_id AND
					rp.adr_id=ra.adr_id AND
					r.resp_legal='1' AND
					r.ele_id='$lig1->ele_id'";
			$res2=mysql_query($sql);

			//echo "<tr>\n";
			echo "<tr style='background-color:".$alt.";'>\n";

			if(mysql_num_rows($res2)>0){
				//while($lig2=mysql_fetch_object($res2)){
					$lig2=mysql_fetch_object($res2);
					echo "<td style='text-align:center;'>\n";
					echo "<a href='modify_resp.php?pers_id=$lig2->pers_id'>$lig2->nom $lig2->prenom</a>\n";
					echo "</td>\n";

					echo "<td style='text-align:center;'>\n";
					if($lig2->adr1!=''){echo "$lig2->adr1\n";}
					if($lig2->adr2!=''){echo "<br />\n$lig2->adr2\n";}
					if($lig2->adr3!=''){echo "<br />\n$lig2->adr3\n";}
					if($lig2->adr4!=''){echo "<br />\n$lig2->adr4\n";}
					if(($lig2->commune!='')||($lig2->cp!='')){echo "<br />\n$lig2->cp $lig2->commune\n";}
					if($lig2->pays!=''){echo "<br />\n$lig2->pays\n";}
					echo "</td>\n";

					echo "<td style='text-align:center;'>\n";
					echo "<input type='checkbox' name='suppr_resp1[]' value='$lig2->pers_id' />";
					echo "</td>\n";
				//}
			}
			else{
				echo "<td>&nbsp;</td>\n";
				echo "<td>&nbsp;</td>\n";
				echo "<td>&nbsp;</td>\n";
			}

			echo "<td style='text-align:center;'><a href='../eleves/modify_eleve.php?eleve_login=$lig1->login&amp;quelles_classes=toutes&amp;order_type=nom,prenom'>$lig1->nom $lig1->prenom</a></td>\n";


			$sql="SELECT rp.nom,rp.prenom,rp.pers_id,ra.* FROM resp_pers rp, resp_adr ra, responsables2 r WHERE
					r.pers_id=rp.pers_id AND
					rp.adr_id=ra.adr_id AND
					r.resp_legal='2' AND
					r.ele_id='$lig1->ele_id'";
			$res3=mysql_query($sql);
			if(mysql_num_rows($res3)>0){
				$lig3=mysql_fetch_object($res3);
				echo "<td style='text-align:center;'>\n";
				echo "<a href='modify_resp.php?pers_id=$lig3->pers_id'>$lig3->nom $lig3->prenom</a>\n";
				echo "</td>\n";

				echo "<td style='text-align:center;'>\n";
				if($lig3->adr1!=''){echo "$lig3->adr1\n";}
				if($lig3->adr2!=''){echo "<br />\n$lig3->adr2\n";}
				if($lig3->adr3!=''){echo "<br />\n$lig3->adr3\n";}
				if($lig3->adr4!=''){echo "<br />\n$lig3->adr4\n";}
				if(($lig3->commune!='')||($lig3->cp!='')){echo "<br />\n$lig3->cp $lig3->commune\n";}
				if($lig3->pays!=''){echo "<br />\n$lig3->pays\n";}
				echo "</td>\n";

				echo "<td style='text-align:center;'>\n";
				echo "<input type='checkbox' name='suppr_resp2[]' value='$lig3->pers_id' />";
				echo "</td>\n";
			}
			else{
				echo "<td>&nbsp;</td>\n";
				echo "<td>&nbsp;</td>\n";
				echo "<td>&nbsp;</td>\n";
			}

			echo "</tr>\n";
			$cpt++;
		}
	}
}
echo "</table>\n";
echo "<center><input type='submit' value='Valider' /></center>\n";
echo "</form>\n";
echo "</body></html>\n";
?>
