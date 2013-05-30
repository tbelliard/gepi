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

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

//INSERT INTO droits SET id='/classes/ajout_eleve_classe.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Enregistrement des inscriptions élève/classe',statut='';
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

check_token();

header('Content-Type: text/html; charset=utf-8');

$login_eleve=isset($_POST['login_ele_ajout_classe']) ? $_POST['login_ele_ajout_classe'] : "";
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : "";
$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : array();

/*
echo "\$login_eleve=$login_eleve<br />";
echo "\$id_classe=$id_classe<br />";
*/

$msg="";

$erreur="n";
if(($login_eleve=="")||($id_classe=="")||(count($num_periode)==0)) {
	$erreur="y";
}
else {
	$nom_prenom=get_nom_prenom_eleve($login_eleve);
	if(preg_match("/\(/", $nom_prenom)) {
		$msg="$nom_prenom n'est pas un élève.";
		$erreur="y";
	}
	else {
		$classe=get_nom_classe($id_classe);
		if(!$classe) {
			$msg="L'identifiant '$id_classe' ne correspond à aucune classe.";
			$erreur="y";
		}
		else {
			$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe';";
			$res_per=mysql_query($sql);
			if(mysql_num_rows($res_per)==0) {
				$msg="Aucune période n'existe pour la classe <a href='../classes/periodes.php?id_classe=$id_classe'>$classe</a>.";
				$erreur="y";
			}
			else {
				$tab_per=array();
				while($lig_per=mysql_fetch_object($res_per)) {
					$tab_per[]=$lig_per->num_periode;
				}

				for($loop=0;$loop<count($num_periode);$loop++) {
					if(!in_array($num_periode[$loop], $tab_per)) {
						$msg.="Numéro de période '$num_periode[$loop]' invalide.<br />";
						unset($num_periode[$loop]);
					}
				}

				foreach($num_periode as $key => $i) {
					// Contrôler que l'élève n'est pas déjà dans une autre classe
					$sql="SELECT id_classe FROM j_eleves_classes WHERE
					(login = '$login_eleve' and
					id_classe!='$id_classe' and
					periode = '$i')";
					$test_clas_per=mysql_query($sql);
					if(mysql_num_rows($test_clas_per)>0) {
						$lig_clas_per=mysql_fetch_object($test_clas_per);
						$msg.=$login_eleve." est déjà dans une autre classe&nbsp;: <a href=../classes/classes_const.php?id_classe=$lig_clas_per->id_classe>".get_class_from_id($lig_clas_per->id_classe)."</a> en période $i.<br />\n";
					}
					else {
						$sql="SELECT login FROM j_eleves_classes WHERE
						(login = '$login_eleve' and
						id_classe = '$id_classe' and
						periode = '$i')";
						$res_clas_per=mysql_query($sql);
						if (mysql_num_rows($res_clas_per)==0) {
							$sql="INSERT INTO j_eleves_classes VALUES('$login_eleve', '$id_classe', $i, '0');";
							$reg_data = mysql_query($sql);
							if (!($reg_data))  {$msg.="Erreur lors de l'inscription de $nom_prenom dans la classe $classe en période $i.<br />";}
							else {
								$msg.="$nom_prenom a été inscrit(e) dans la classe <a href=\"../classes/classes_const.php?id_classe=$id_classe\">$classe</a> en période $i.<br />";

								// Ménage:
								$sql="SELECT id FROM infos_actions WHERE titre LIKE 'Ajout dans une classe % effectuer pour %($login_eleve)';";
								$res_actions=mysql_query($sql);
								if(mysql_num_rows($res_actions)>0) {
									while($lig_action=mysql_fetch_object($res_actions)) {
										$menage=del_info_action($lig_action->id);
										if(!$menage) {$msg.="Erreur lors de la suppression de l'action en attente en page d'accueil à propos de $login_eleve<br />";}
									}
								}
							}
						}

		
						// UPDATE: Ajouter l'élève à tous les groupes pour la période:
						$sql="SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe'";
						$res_liste_grp_classe=mysql_query($sql);
						if(mysql_num_rows($res_liste_grp_classe)>0){
							while($lig_tmp=mysql_fetch_object($res_liste_grp_classe)){
								$sql="SELECT 1=1 FROM j_eleves_groupes WHERE login='$login_eleve' AND id_groupe='$lig_tmp->id_groupe' AND periode='$i'";
								$test=mysql_query($sql);
								if(mysql_num_rows($test)==0){
									$sql="INSERT INTO j_eleves_groupes SET login='$login_eleve',id_groupe='$lig_tmp->id_groupe',periode='$i'";
									$insert_grp=mysql_query($sql);
									if (!($insert_grp))  {$msg.="Erreur lors de l'inscription de $nom_prenom dans le groupe n°<a href='../groupes/edit_eleves.php?id_groupe=$lig_tmp->id_groupe&id_classe=$id_classe' target='_blank'>$lig_tmp->id_groupe</a> en période $i.<br />";}
								}
							}
						}
			
						$sql="SELECT DISTINCT cpe_login FROM j_eleves_cpe jecpe, j_eleves_classes jec
									WHERE (
										jec.id_classe='$id_classe' AND
										jecpe.e_login=jec.login AND
										jec.periode='$i'
									)";
						//echo "$sql<br />";
						$res_cpe=mysql_query($sql);
						if(mysql_num_rows($res_cpe)==1) {
							$sql="DELETE FROM j_eleves_cpe WHERE e_login='$login_eleve';";
							//echo "$sql<br />";
							$nettoyage=mysql_query($sql);
			
							$lig_tmp=mysql_fetch_object($res_cpe);
							$sql="INSERT INTO j_eleves_cpe SET cpe_login='$lig_tmp->cpe_login', e_login='$login_eleve';";
							//echo "$sql<br />";
							$insert_cpe=mysql_query($sql);
						}
						else {
							if(!in_array($login_eleve, $tab_ele_sans_cpe_defini)) {
								$msg.="<br />L'élève $login_eleve n'a pas été <a href='";
								$msg.="classes_const.php?id_classe=$id_classe&amp;quitter_la_page=y";
								$msg.="' target='_blank'>associé</a> à un CPE.";
								$tab_ele_sans_cpe_defini[]=$login_eleve;
							}
						}
			
						$sql="SELECT DISTINCT professeur FROM j_eleves_professeurs jep
									WHERE (
										jep.id_classe='$id_classe'
									)";
						//echo "$sql<br />";
						$res_pp=mysql_query($sql);
						if(mysql_num_rows($res_pp)==1) {
							$sql="DELETE FROM j_eleves_professeurs WHERE login='$login_eleve';";
							//echo "$sql<br />";
							$nettoyage=mysql_query($sql);
			
							$lig_tmp=mysql_fetch_object($res_pp);
							$sql="INSERT INTO j_eleves_professeurs SET professeur='$lig_tmp->professeur', login='$login_eleve', id_classe='$id_classe';";
							//echo "$sql<br />";
							$insert_pp=mysql_query($sql);
						}
						else {
							if(!in_array($login_eleve, $tab_ele_sans_pp_defini)) {
								$msg.="<br />L'élève $login_eleve n'a pas été <a href='";
								if(mysql_num_rows($res_pp)==0) {
									$msg.="prof_suivi.php?id_classe=$id_classe";
								}
								else {
									$msg.="classes_const.php?id_classe=$id_classe&amp;quitter_la_page=y";
								}
								$msg.="' target='_blank'>associé</a> à un ".$gepiProfSuivi.".";
								$tab_ele_sans_pp_defini[]=$login_eleve;
							}
						}
					}
				}
			}
		}
	}
}

if($erreur=="n") {
	header("Location: ../eleves/index.php?quelles_classes=na&msg=".urlencode($msg));
	die();
}

require_once("../lib/header.inc.php");

echo "<p><span style='color:red'>ERREUR&nbsp;:</span> Il s'est produit une erreur&nbsp;:<br />".$msg."</p>";

echo "<p><a href='../eleves/index.php?quelles_classes=na'>Retour à la liste des élèves non inscrits</a></p>";

require("../lib/footer.inc.php");
?>
