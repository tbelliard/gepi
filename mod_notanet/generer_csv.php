<?php
/*
* $Id$
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
	die();
}


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

check_token();

//$nom_fic = "notanet_".date('Y.m.d_H.i.s_').preg_replace("/ /","_",microtime()).".csv";
//send_file_download_headers('text/x-csv',$nom_fic);


// Fin de ligne
$eol="\r\n";

// Récupération des lignes
$lig_notanet=isset($_POST['lig_notanet']) ? $_POST['lig_notanet'] : NULL;

// Initialisation du fichier
$fd = '';

if (isset($lig_notanet)) {
	sort($lig_notanet);

	// Remplissage du fichier
	for($i=0;$i<count($lig_notanet);$i++) {
		$fd.=$lig_notanet[$i].$eol;
	}
}
else {
	// On extrait les infos de la table MySQL
	/*
	$sql="SELECT n.* FROM notanet n,
						notanet_corresp nc,
						notanet_ele_type net
					WHERE n.login=net.login AND
						net.type_brevet=nc.type_brevet
					ORDER BY nc.type_brevet,n.ine,nc.id_mat";
	*/

	$extract_mode=isset($_POST['extract_mode']) ? $_POST['extract_mode'] : (isset($_GET['extract_mode']) ? $_GET['extract_mode'] : NULL);

	$avec_nom_prenom=isset($_GET['avec_nom_prenom']) ? $_GET['avec_nom_prenom'] : "n";
	$total_seul=isset($_GET['total_seul']) ? $_GET['total_seul'] : "n";

	//$fd.="TEMOIN".$eol;

	// Bibliothèque pour Notanet et Fiches brevet
	include("lib_brevets.php");

	/*
	//=========================================================
	unset($tab_mat);
	$sql="SELECT * FROM notanet_corresp ORDER BY type_brevet;";
	$res1=mysql_query($sql);
	while($lig1=mysql_fetch_object($res1)) {
		$sql="SELECT * FROM notanet_corresp WHERE type_brevet='$lig1->type_brevet';";
		$res2=mysql_query($sql);

		unset($id_matiere);
		unset($statut_matiere);

		while($lig2=mysql_fetch_object($res2)) {
			$id_matiere[$lig2->id_mat][]=$lig2->matiere;
			//$statut_matiere[$lig2->id_mat][]=$lig2->statut;
			$statut_matiere[$lig2->id_mat]=$lig2->statut;
		}

		$tab_mat[$lig1->type_brevet]=array();

		//for($j=101;$j<=$indice_max_matieres;$j++) {
		//	$tab_mat[$lig1->type_brevet][$j]=$id_matiere[$j];
		//}

		$tab_mat[$lig1->type_brevet]['id_matiere']=$id_matiere;
		$tab_mat[$lig1->type_brevet]['statut_matiere']=$statut_matiere;
	}
	//=========================================================
	*/

	//$fd.="TEMOIN".$eol;
	//$fd.="\$extract_mode=$extract_mode".$eol;

	unset($lig_notanet);
	if($extract_mode=="tous") {
		//$fd.="\$extract_mode=$extract_mode".$eol;

		$sql="SELECT DISTINCT type_brevet FROM notanet_corresp WHERE $sql_indices_types_brevets ORDER BY type_brevet;";
		$res0=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res0)>0) {
			while($lig0=mysqli_fetch_object($res0)) {

				$type_brevet=$lig0->type_brevet;

				//$tabmatieres=tabmatieres($lig0->type_brevet);
				$tabmatieres=tabmatieres($type_brevet);

				//$sql="SELECT DISTINCT login,type_brevet FROM notanet_ele_type WHERE type_brevet='$lig0->type_brevet';";
				$sql="SELECT DISTINCT login,type_brevet FROM notanet_ele_type WHERE type_brevet='$type_brevet';";
				$res1=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res1)>0) {
					while($lig1=mysqli_fetch_object($res1)) {
						/*
						$sql="SELECT n.ine,n.note_notanet,nc.id_mat FROM notanet n,
										notanet_corresp nc
									WHERE n.login='$lig1->login' AND
										nc.type_brevet='$lig1->type_brevet' AND
										nc.matiere=n.mat
									ORDER BY nc.id_mat;";
						*/
						$sql="SELECT n.ine,n.note_notanet,n.id_mat FROM notanet n
									WHERE n.login='$lig1->login'
									ORDER BY n.id_mat;";
						//$fd.=$sql.$eol;
						$res2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res2)>0) {
							$TOT=0;
							$ine="";
							while($lig2=mysqli_fetch_object($res2)) {
								$ine=$lig2->ine;
								$note=$lig2->note_notanet;
								if (preg_match("/([0-9]{2})\.([0-9]{1})/", $lig2->note_notanet)) {
									if($tabmatieres[$lig2->id_mat][-1]!="NOTNONCA") {
										$TOT+=$lig2->note_notanet;
									}
									$note=formate_note_notanet($lig2->note_notanet);
								}
								// Le formatage est déjà fait lors de l'insertion dans la table: NON... il faut deux chiffres après la virgule
								//$lig_notanet[]="$lig2->ine|$lig2->id_mat|".formate_note_notanet($lig2->note_notanet)."|";
								//$lig_notanet[]="$lig2->ine|$lig2->id_mat|".$lig2->note_notanet."|";

								if($total_seul!="y") {
									$lig_notanet[]="$lig2->ine|".sprintf("%03d",$lig2->id_mat)."|".$note."|";
								}
								elseif(($avec_nom_prenom=="y")&&($total_seul!="y")) {
									// Non traité actuellement
								}
							}

							if($avec_nom_prenom!="y") {
								$lig_notanet[]="$ine|TOT|".formate_note_notanet($TOT)."|";
							}
							else {
								$nom_ele="";
								$prenom_ele="";
								$classe_ele="";

								$sql="SELECT * FROM eleves WHERE login='$lig1->login';";
								$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_ele)>0) {
									$nom_ele=mysql_result($res_ele,0,"nom");
									$prenom_ele=mysql_result($res_ele,0,"prenom");
								}

								$sql="SELECT c.classe FROM classes c, j_eleves_classes jec WHERE jec.id_classe=c.id AND jec.login='$lig1->login' ORDER BY periode DESC LIMIT 1;";
								$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_clas)>0) {
									$classe_ele=mysql_result($res_clas,0,"classe");
								}

								$lig_notanet[]="$ine|$nom_ele|$prenom_ele|$classe_ele|TOT|".formate_note_notanet($TOT)."|";
							}
						}
					}
				}
			}
		}
	}
	elseif((preg_match("/[0-9]/",$extract_mode))&&(mb_strlen(preg_replace("/[0-9]/","",$extract_mode))==0)) {
		$type_brevet=$extract_mode;

		/*
		$sql="SELECT login FROM notanet_ele_type WHERE type_brevet='$extract_mode';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			while($lig=mysql_fetch_object($res)) {
				$sql="SELECT * FROM notanet WHERE login='$lig->login';";
				//$nettoyage=mysql_query($sql);
			}
		}
		*/


		$tabmatieres=tabmatieres($type_brevet);

		//$sql="SELECT DISTINCT login,type_brevet FROM notanet_ele_type WHERE type_brevet='$lig0->type_brevet';";
		$sql="SELECT DISTINCT login,type_brevet FROM notanet_ele_type WHERE type_brevet='$type_brevet';";
		$res1=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res1)>0) {
			while($lig1=mysqli_fetch_object($res1)) {
				/*
				$sql="SELECT n.ine,n.note_notanet,nc.id_mat FROM notanet n,
								notanet_corresp nc
							WHERE n.login='$lig1->login' AND
								nc.type_brevet='$lig1->type_brevet' AND
								nc.matiere=n.mat
							ORDER BY nc.id_mat;";
				*/
				$sql="SELECT n.ine,n.note_notanet,n.id_mat FROM notanet n
							WHERE n.login='$lig1->login'
							ORDER BY n.id_mat;";
				//$fd.=$sql.$eol;
				$res2=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res2)>0) {
					$TOT=0;
					$ine="";
					while($lig2=mysqli_fetch_object($res2)) {
						$ine=$lig2->ine;
						$note=$lig2->note_notanet;
						if (preg_match("/([0-9]{2})\.([0-9]{1})/", $lig2->note_notanet)) {
							if($tabmatieres[$lig2->id_mat][-1]!="NOTNONCA") {
								$TOT+=$lig2->note_notanet;
							}
							$note=formate_note_notanet($lig2->note_notanet);
						}
						// Le formatage est déjà fait lors de l'insertion dans la table
						//$lig_notanet[]="$lig2->ine|$lig2->id_mat|".formate_note_notanet($lig2->note_notanet)."|";
						//$lig_notanet[]="$lig2->ine|$lig2->id_mat|".$note."|";

						if($avec_nom_prenom!="y") {
							$lig_notanet[]="$lig2->ine|".sprintf("%03d",$lig2->id_mat)."|".$note."|";
						}
						elseif(($avec_nom_prenom=="y")&&($total_seul!="y")) {
							// Non traité actuellement
						}
					}

					if($avec_nom_prenom!="y") {
						$lig_notanet[]="$ine|TOT|".formate_note_notanet($TOT)."|";
					}
					else {
						$nom_ele="";
						$prenom_ele="";
						$classe_ele="";

						$sql="SELECT * FROM eleves WHERE login='$lig1->login';";
						$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_ele)>0) {
							$nom_ele=mysql_result($res_ele,0,"nom");
							$prenom_ele=mysql_result($res_ele,0,"prenom");
						}

						$sql="SELECT c.classe FROM classes c, j_eleves_classes jec WHERE jec.id_classe=c.id AND jec.login='$lig1->login' ORDER BY periode DESC LIMIT 1;";
						$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_clas)>0) {
							$classe_ele=mysql_result($res_clas,0,"classe");
						}

						$lig_notanet[]="$ine|$nom_ele|$prenom_ele|$classe_ele|TOT|".formate_note_notanet($TOT)."|";
					}
				}
			}
		}

	}

	if (isset($lig_notanet)) {
		sort($lig_notanet);

		// Remplissage du fichier
		for($i=0;$i<count($lig_notanet);$i++) {
			$fd.=$lig_notanet[$i].$eol;
		}
	}

}

// Génération/envoi au navigateur du fichier
$nom_fic = "notanet_".date('Y.m.d_H.i.s_').preg_replace("/ /","_",microtime()).".csv";
send_file_download_headers('text/x-csv',$nom_fic);

if(getSettingValue('notanet_save_export')=='y') {
	$user_temp_directory=get_user_temp_directory();
	if($user_temp_directory!="") {
		$fich=fopen("../temp/".$user_temp_directory."/".$nom_fic,"w+");
		fwrite($fich, $fd);
		fclose($fich);
	}
}

echo $fd;
?>
