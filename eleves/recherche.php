<?php
/*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

function extract_utilisateurs($tab_login) {
	global $cpt_pers, $tab_result_recherche, $acces_modify_user, $gepiPath;

	for($loop_tab_login=0;$loop_tab_login<count($tab_login);$loop_tab_login++) {
		$sql="SELECT * FROM utilisateurs WHERE login='".$tab_login[$loop_tab_login]."';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
			$style_ligne="";
			if($lig->etat=='inactif') {
				$style_ligne=" style='background-color:grey;'";
			}

			$tab_result_recherche['personnel'][$cpt_pers]['style_ligne']=$style_ligne;

			// Login
			if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
				$tab_result_recherche['personnel'][$cpt_pers]['login']=$lig->login;

				if($acces_modify_user) {
					$tab_result_recherche['personnel'][$cpt_pers]['td_login']="<a href='$gepiPath/utilisateurs/modify_user.php?login=$lig->login' title=\"Modifier les informations utilisateur\">$lig->login</a>";
				}
				else {
					$tab_result_recherche['personnel'][$cpt_pers]['td_login']=$lig->login;
				}
			}

			$tab_result_recherche['personnel'][$cpt_pers]['nom_prenom']=casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, "majf2");

			$tab_result_recherche['personnel'][$cpt_pers]['email']=$lig->email;
			$tab_result_recherche['personnel'][$cpt_pers]['td_email']="";
			if(check_mail($lig->email)) {
				$tab_result_recherche['personnel'][$cpt_pers]['td_email']="<a href='mailto:".$lig->email."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI' title='Envoyer un mail à $lig->email'><img src='../images/icons/courrier_envoi.png' class='icon16' alt='Mail' /></a><span id='email_personnel_".$cpt_pers."' style='display:none;'>$lig->email</span>";
			}

			// Compte actif ou non
			$tab_result_recherche['personnel'][$cpt_pers]['td_compte']="";
			$tab_result_recherche['personnel'][$cpt_pers]['compte']="";
			if($lig->login!="") {
				if($_SESSION['statut']=='administrateur') {
					$tab_result_recherche['personnel'][$cpt_pers]['td_compte']=lien_image_compte_utilisateur($lig->login, "", "", "y", 'y');
				}
				else {
					$tab_result_recherche['personnel'][$cpt_pers]['td_compte']=lien_image_compte_utilisateur($lig->login, "", "", "n", 'y');
				}

				if(preg_match("/inactif/", $tab_result_recherche['personnel'][$cpt_pers]['td_compte'])) {
					$tab_result_recherche['personnel'][$cpt_pers]['compte']="inactif";
				}
				else {
					$tab_result_recherche['personnel'][$cpt_pers]['compte']="actif";
				}
			}

			$tab_result_recherche['personnel'][$cpt_pers]['statut']=$lig->statut;

			// Matières
			$tab_result_recherche['personnel'][$cpt_pers]['td_matieres']="";
			$tab_result_recherche['personnel'][$cpt_pers]['matieres']="";
			if($lig->statut=='professeur') {
				$tab_matieres_prof=get_matieres_from_prof($lig->login);
				for($loop=0;$loop<count($tab_matieres_prof);$loop++) {
					if($loop>0) {
						$tab_result_recherche['personnel'][$cpt_pers]['matieres'].=", ";
						$tab_result_recherche['personnel'][$cpt_pers]['td_matieres'].=", ";
					}
					$tab_result_recherche['personnel'][$cpt_pers]['matieres'].=$tab_matieres_prof[$loop]['matiere'];

					if($tab_matieres_prof[$loop]['enseignee']=='y') {
						$tab_result_recherche['personnel'][$cpt_pers]['td_matieres'].="<span style='font-weight:bold' title=\"".$tab_matieres_prof[$loop]['nom_complet']."\">".$tab_matieres_prof[$loop]['matiere']."</span>";
					}
					else {
						$tab_result_recherche['personnel'][$cpt_pers]['td_matieres'].="<span style='font-size:xx-small' title=\"".$tab_matieres_prof[$loop]['nom_complet']." (non enseignée cette année)\">".$tab_matieres_prof[$loop]['matiere']."</span>";
					}
				}
			}

			// Classes
			$tab_result_recherche['personnel'][$cpt_pers]['classes']="";
			$tab_result_recherche['personnel'][$cpt_pers]['td_classes']="";
			if($lig->statut=='professeur') {
				$tab_classes_prof=get_classes_from_prof($lig->login);
				if(count($tab_classes_prof)>0) {
					$cpt_classe=0;
					foreach($tab_classes_prof as $id_classe_prof => $classe_prof) {
						if($cpt_classe>0) {
							$tab_result_recherche['personnel'][$cpt_pers]['classes'].=", ";
							$tab_result_recherche['personnel'][$cpt_pers]['td_classes'].=", ";
						}
						$tab_result_recherche['personnel'][$cpt_pers]['classes'].=$classe_prof;

						if($acces_modify_user) {
							$tab_result_recherche['personnel'][$cpt_pers]['td_classes'].="<a href='$gepiPath/groupes/edit_class.php?id_classe=$id_classe_prof' title=\"Modifier les enseignements de la classe $classe_prof\">$classe_prof</a>";
						}
						else {
							$tab_result_recherche['personnel'][$cpt_pers]['td_classes'].=$classe_prof;
						}
						$cpt_classe++;
					}
				}
			}

			//$compteur_personnes_trouvees++;
			$cpt_pers++;
		}
	}
}

$rech_nom=isset($_POST['rech_nom']) ? $_POST['rech_nom'] : "";
$rech_prenom=isset($_POST['rech_prenom']) ? $_POST['rech_prenom'] : "";

if(isset($_POST['is_posted_recherche'])) {
	check_token();

	if($rech_nom=="") {
		unset($_SESSION['rech_nom']);
	}
	else {
		$_SESSION['rech_nom']=$rech_nom;
	}

	if($rech_prenom=="") {
		unset($_SESSION['rech_prenom']);
	}
	else {
		$_SESSION['rech_prenom']=$rech_prenom;
	}

	$tab_result_recherche=array();

	$statut=isset($_POST['statut']) ? $_POST['statut'] : array();
	if(count($statut)>0) {
		$acces_visu_eleve=acces("/eleves/visu_eleve.php", $_SESSION['statut']);

		if($_SESSION['statut']=='professeur') {
			if(!getSettingAOui('GepiAccesGestElevesProf')) {
				$acces_visu_eleve="n";
			}
		}

		//$compteur_personnes_trouvees=0;

		if(in_array("eleve", $statut)) {
			$_SESSION['rech_statut_eleve']="y";

			$tab_result_recherche['eleve']=array();

			$acces_modify_eleve=acces("/eleves/modify_eleve.php", $_SESSION['statut']);
			$acces_class_const=acces("/classes/classes_const.php", $_SESSION['statut']);

			$sql="SELECT * FROM eleves WHERE nom LIKE '%$rech_nom%' AND prenom LIKE '%$rech_prenom%' ORDER BY nom, prenom;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				// Le tableau $tab_result_recherche['eleve'] est vide 
			}
			else {
				$cpt_eleve=0;
				while($lig=mysql_fetch_object($res)) {
					$restriction_acces="n";
					if(($_SESSION['statut']=='professeur')&&
					((!getSettingAOui('GepiAccesGestElevesProf'))||(!is_prof_ele($_SESSION['login'], $lig->login)))) {
						if((getSettingAOui('GepiAccesGestElevesProfP'))&&(is_pp($_SESSION['login'], "", $lig->login))) {
							$restriction_acces="n";
						}
						else {
							$restriction_acces="y";
						}
					}

					$tab_result_recherche['eleve'][$cpt_eleve]['login']=$lig->login;

					if(($acces_modify_eleve)&&($restriction_acces=="n")) {
						$tab_result_recherche['eleve'][$cpt_eleve]['td_login']="<a href='$gepiPath/eleves/modify_eleve.php?eleve_login=$lig->login' title=\"Modifier les informations élève\">$lig->login</a>";
					}
					else {
						$tab_result_recherche['eleve'][$cpt_eleve]['td_login']=$lig->login;
					}

					$tab_result_recherche['eleve'][$cpt_eleve]['compte']="";
					$tab_result_recherche['eleve'][$cpt_eleve]['td_compte']="";
					if($lig->login!="") {
						if($_SESSION['statut']=='administrateur') {
							$tab_result_recherche['eleve'][$cpt_eleve]['td_compte']=lien_image_compte_utilisateur($lig->login, "", "", "y", 'y');
						}
						else {
							$tab_result_recherche['eleve'][$cpt_eleve]['td_compte']=lien_image_compte_utilisateur($lig->login, "", "", "n", 'y');
						}

						if(preg_match("/inactif/", $tab_result_recherche['eleve'][$cpt_eleve]['td_compte'])) {
							$tab_result_recherche['eleve'][$cpt_eleve]['compte']="inactif";
						}
						else {
							$tab_result_recherche['eleve'][$cpt_eleve]['compte']="actif";
						}
					}

					$tab_result_recherche['eleve'][$cpt_eleve]['nom_prenom']=casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, "majf2");
					if($acces_visu_eleve) {
						$tab_result_recherche['eleve'][$cpt_eleve]['td_nom_prenom']="<a href='$gepiPath/eleves/visu_eleve.php?ele_login=$lig->login' title=\"Consulter la fiche élève\">".$tab_result_recherche['eleve'][$cpt_eleve]['nom_prenom']."</a>";
					}
					else {
						$tab_result_recherche['eleve'][$cpt_eleve]['td_nom_prenom']=$tab_result_recherche['eleve'][$cpt_eleve]['nom_prenom'];
					}

					$tab_result_recherche['eleve'][$cpt_eleve]['classe']="";
					$tab_result_recherche['eleve'][$cpt_eleve]['td_classe']="";
					$sql="SELECT DISTINCT id, classe FROM classes c, j_eleves_classes jec WHERE jec.login='$lig->login' AND jec.id_classe=c.id ORDER BY periode;";
					$res_classe=mysql_query($sql);
					if(mysql_num_rows($res_classe)>0) {
						$cpt_classe=0;
						while($lig_classe=mysql_fetch_object($res_classe)) {
							if($cpt_classe>0) {
								$tab_result_recherche['eleve'][$cpt_eleve]['classe'].=", ";
								$tab_result_recherche['eleve'][$cpt_eleve]['td_classe'].=", ";
							}
							if($acces_class_const) {
								$tab_result_recherche['eleve'][$cpt_eleve]['td_classe'].="<a href='$gepiPath/classes/classes_const.php?id_classe=$lig_classe->id' title=\"Accéder à la liste des élèves de la classe.\">$lig_classe->classe</a>";
							}
							else {
								$tab_result_recherche['eleve'][$cpt_eleve]['td_classe'].=$lig_classe->classe;
							}
							$tab_result_recherche['eleve'][$cpt_eleve]['classe'].=$lig_classe->classe;
							$cpt_classe++;
						}
					}
					//$compteur_personnes_trouvees++;
					$cpt_eleve++;
				}
			}
		}
		else {
			$_SESSION['rech_statut_eleve']="n";
		}

		//====================================
		if(in_array("responsable", $statut)) {
			$_SESSION['rech_statut_responsable']="y";

			$tab_result_recherche['responsable']=array();

			$acces_modify_resp=acces("/responsables/modify_resp.php", $_SESSION['statut']);

			$sql="SELECT * FROM resp_pers WHERE nom LIKE '%$rech_nom%' AND prenom LIKE '%$rech_prenom%' ORDER BY nom, prenom;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				// Le tableau $tab_result_recherche['responsable'] est vide 
			}
			else {
				$cpt_resp=0;
				while($lig=mysql_fetch_object($res)) {
					$tab_result_recherche['responsable'][$cpt_resp]['pers_id']=$lig->pers_id;


					if($acces_modify_resp) {
						$tab_result_recherche['responsable'][$cpt_resp]['td_pers_id']="<a href='$gepiPath/responsables/modify_resp.php?pers_id=$lig->pers_id' title=\"Modifier les informations responsable\">$lig->pers_id</a>";
					}
					else {
						$tab_result_recherche['responsable'][$cpt_resp]['td_pers_id']=$lig->pers_id;
					}

					$tab_result_recherche['responsable'][$cpt_resp]['nom_prenom']=casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, "majf2");

					// Le span display none sert dans le tri.
					$tab_result_recherche['responsable'][$cpt_resp]['td_compte']="<span style='display:none;'>Pas de compte</span><img src='../images/disabled.png' class='icon20' title='Pas de compte' alt='Pas de compte' />";
					$tab_result_recherche['responsable'][$cpt_resp]['compte']="";
					$tab_result_recherche['responsable'][$cpt_resp]['login']="";
					if($lig->login!="") {
						$tab_result_recherche['responsable'][$cpt_resp]['login']=$lig->login;
						if($_SESSION['statut']=='administrateur') {
							$tab_result_recherche['responsable'][$cpt_resp]['td_compte']=lien_image_compte_utilisateur($lig->login, "", "", "y", 'y');
						}
						else {
							$tab_result_recherche['responsable'][$cpt_resp]['td_compte']=lien_image_compte_utilisateur($lig->login, "", "", "n", 'y');
						}

						if(preg_match("/inactif/", $tab_result_recherche['responsable'][$cpt_resp]['td_compte'])) {
							$tab_result_recherche['responsable'][$cpt_resp]['compte']="inactif";
						}
						else {
							$tab_result_recherche['responsable'][$cpt_resp]['compte']="actif";
						}
					}

					$tab_result_recherche['responsable'][$cpt_resp]['enfants']="";
					$tab_result_recherche['responsable'][$cpt_resp]['td_enfants']="";
					$tab_enfants=get_enfants_from_pers_id($lig->pers_id, "avec_classe");
					for($loop=0;$loop<count($tab_enfants);$loop+=2) {
						if($loop>0) {
							$tab_result_recherche['responsable'][$cpt_resp]['enfants'].=", ";
						}
						$tab_result_recherche['responsable'][$cpt_resp]['enfants'].=$tab_enfants[$loop+1];

						if($acces_visu_eleve) {
							$tab_result_recherche['responsable'][$cpt_resp]['td_enfants'].="<a href='$gepiPath/eleves/visu_eleve.php?ele_login=".$tab_enfants[$loop]."' title=\"Consulter la fiche élève\">".$tab_enfants[$loop+1]."</a><br />";
						}
						else {
							$tab_result_recherche['responsable'][$cpt_resp]['td_enfants'].=$tab_enfants[$loop+1]."<br />";
						}
					}
					//$compteur_personnes_trouvees++;
					$cpt_resp++;
				}
			}
		}
		else {
			$_SESSION['rech_statut_responsable']="n";
		}

		//====================================
		if(in_array("personnel", $statut)) {
			$_SESSION['rech_statut_personnel']="y";

			$tab_result_recherche['personnel']=array();

			$acces_modify_user=acces("/utilisateurs/modify_user.php", $_SESSION['statut']);
			$acces_edit_class=acces("/groupes/edit_class.php", $_SESSION['statut']);

			$sql="SELECT * FROM utilisateurs WHERE nom LIKE '%$rech_nom%' AND prenom LIKE '%$rech_prenom%' AND statut!='eleve' AND statut!='responsable' ORDER BY nom, prenom;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				// Le tableau $tab_result_recherche['personnel'] est vide 
			}
			else {
				$tab_login=array();
				while($lig=mysql_fetch_object($res)) {
					$tab_login[]=$lig->login;
				}
				/*
				echo "<pre>\$tab_login<br/>";
				print_r($tab_login);
				echo "</pre>";
				*/
				$cpt_pers=0;
				extract_utilisateurs($tab_login);
			}
			/*
			echo "<pre>\$tab_result_recherche<br/>";
			print_r($tab_result_recherche);
			echo "</pre>";
			*/
		}
		else {
			$_SESSION['rech_statut_personnel']="n";
		}
	}
}

if(isset($_POST['is_posted_recherche2'])) {
	check_token();

	$rech_matiere=isset($_POST['rech_matiere']) ? $_POST['rech_matiere'] : array();
	$rech_domaine=isset($_POST['rech_domaine']) ? $_POST['rech_domaine'] : array();

	$tab_login=array();
	$tab_result_recherche['personnel']=array();

	$tab_groupes_non_visibles=array();
	$sql="";
	for($loop=0;$loop<count($tab_domaines);$loop++) {
		if(!in_array($tab_domaines[$loop], $rech_domaine)) {
			if($loop>0) {$sql.=" UNION ";}
			$sql.="(SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='".$tab_domaines[$loop]."' AND visible='n')";
		}
	}
	if($sql!="") {
		$res=mysql_query($sql);
		while($lig=mysql_fetch_object($res)) {
			$tab_groupes_non_visibles[]=$lig->id_groupe;
		}
	}

	for($loop=0;$loop<count($rech_matiere);$loop++) {
		$sql="SELECT DISTINCT jgp.id_groupe, u.login FROM utilisateurs u, j_groupes_matieres jgm, j_groupes_professeurs jgp WHERE jgm.id_matiere='".$rech_matiere[$loop]."'
			AND jgm.id_groupe=jgp.id_groupe
			AND jgp.login=u.login;";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		while($lig=mysql_fetch_object($res)) {
			if(!in_array($lig->login, $tab_login)) {
				if(!in_array($lig->id_groupe, $tab_groupes_non_visibles)) {
					$tab_login[]=$lig->login;
				}
			}
		}
	}

	$cpt_pers=0;
	extract_utilisateurs($tab_login);
}

if(isset($_POST['is_posted_recherche3'])) {
	check_token();

	$rech_classe=isset($_POST['rech_classe']) ? $_POST['rech_classe'] : array();
	$rech_domaine=isset($_POST['rech_domaine']) ? $_POST['rech_domaine'] : array();

	$tab_login=array();
	$tab_result_recherche['personnel']=array();

	$tab_groupes_non_visibles=array();
	$sql="";
	for($loop=0;$loop<count($tab_domaines);$loop++) {
		if(!in_array($tab_domaines[$loop], $rech_domaine)) {
			if($loop>0) {$sql.=" UNION ";}
			$sql.="(SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='".$tab_domaines[$loop]."' AND visible='n')";
		}
	}
	if($sql!="") {
		$res=mysql_query($sql);
		while($lig=mysql_fetch_object($res)) {
			$tab_groupes_non_visibles[]=$lig->id_groupe;
		}
	}

	for($loop=0;$loop<count($rech_classe);$loop++) {
		$sql="SELECT jgp.id_groupe, u.login FROM utilisateurs u, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_classe='".$rech_classe[$loop]."'
			AND jgc.id_groupe=jgp.id_groupe
			AND jgp.login=u.login;";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		while($lig=mysql_fetch_object($res)) {
			if(!in_array($lig->login, $tab_login)) {
				if(!in_array($lig->id_groupe, $tab_groupes_non_visibles)) {
					$tab_login[]=$lig->login;
				}
			}
		}
	}

	$cpt_pers=0;
	extract_utilisateurs($tab_login);
}

if(isset($_POST['export_csv'])) {
	check_token();

	$csv_ligne1="";
	$csv_suite="";
	if($_POST['export_csv']=="eleve") {
		$checkbox_eleve=isset($_POST['checkbox_eleve']) ? $_POST['checkbox_eleve'] : array();
		for($loop=0;$loop<count($tab_result_recherche['eleve']);$loop++) {
			if($loop==0) {
				$csv_ligne1="Login;Etat du compte;Nom prénom;Classe;\r\n";
			}
			if(in_array($tab_result_recherche['eleve'][$loop]["login"] ,$checkbox_eleve)) {
				$csv_suite.=$tab_result_recherche['eleve'][$loop]["login"].";".$tab_result_recherche['eleve'][$loop]["compte"].";".$tab_result_recherche['eleve'][$loop]["nom_prenom"].";".$tab_result_recherche['eleve'][$loop]["classe"].";\r\n";
			}
		}

		$nom_fic="recherche_eleves_".strftime("%Y%m%d_%H%M%S").".csv";
		send_file_download_headers('text/x-csv',$nom_fic);
		echo echo_csv_encoded($csv_ligne1.$csv_suite);
		die();
	}

	if($_POST['export_csv']=="responsable") {
		$checkbox_responsable=isset($_POST['checkbox_responsable']) ? $_POST['checkbox_responsable'] : array();
		for($loop=0;$loop<count($tab_result_recherche['responsable']);$loop++) {
			if($loop==0) {
				$csv_ligne1="Personne_ID;Nom prénom;Etat du compte;Responsable de;\r\n";
			}
			if(in_array($tab_result_recherche['responsable'][$loop]["pers_id"] ,$checkbox_responsable)) {
				$csv_suite.=$tab_result_recherche['responsable'][$loop]["pers_id"].";".$tab_result_recherche['responsable'][$loop]["nom_prenom"].";".$tab_result_recherche['responsable'][$loop]["compte"].";".$tab_result_recherche['responsable'][$loop]["enfants"].";\r\n";
			}
		}

		$nom_fic="recherche_responsables_".strftime("%Y%m%d_%H%M%S").".csv";
		send_file_download_headers('text/x-csv',$nom_fic);
		echo echo_csv_encoded($csv_ligne1.$csv_suite);
		die();
	}

	if($_POST['export_csv']=="personnel") {
		$checkbox_personnel=isset($_POST['checkbox_personnel']) ? $_POST['checkbox_personnel'] : array();
		for($loop=0;$loop<count($tab_result_recherche['personnel']);$loop++) {
			if($loop==0) {
				// En fait, le CSV n'est pas proposé sur les personnels pour les statuts autres que scolarité et administrateur
				//if(isset($tab_result_recherche['personnel'][$loop]['login'])) {
					$csv_ligne1.="Login;";
				//}
				if(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe'))) {
					$csv_ligne1.="Nom prénom;Etat du compte;Mail;Statut;Matières;Classes;\r\n";
				}
				else {
					$csv_ligne1.="Nom prénom;Etat du compte;Statut;Matières;Classes;\r\n";
				}
			}
			if(in_array($tab_result_recherche['personnel'][$loop]["login"] ,$checkbox_personnel)) {
				$csv_suite.=$tab_result_recherche['personnel'][$loop]["login"].";".$tab_result_recherche['personnel'][$loop]["nom_prenom"].";".$tab_result_recherche['personnel'][$loop]["compte"].";";
				if(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe'))) {
					$csv_suite.=$tab_result_recherche['personnel'][$loop]["email"].";";
				}
				$csv_suite.=$tab_result_recherche['personnel'][$loop]["statut"].";".$tab_result_recherche['personnel'][$loop]["matieres"].";".$tab_result_recherche['personnel'][$loop]["classes"].";\r\n";
			}
		}

		$nom_fic="recherche_personnels_".strftime("%Y%m%d_%H%M%S").".csv";
		send_file_download_headers('text/x-csv',$nom_fic);
		echo echo_csv_encoded($csv_ligne1.$csv_suite);
		die();
	}
}

$javascript_specifique[] = "lib/tablekit";
$dojo=true;
$utilisation_tablekit="ok";

//**************** EN-TETE *****************
$titre_page = "Recherche";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE *****************

//debug_var();

if((isset($_POST['is_posted_recherche']))||(isset($_POST['is_posted_recherche2']))||(isset($_POST['is_posted_recherche3']))) {
	echo "<p><a href='".$_SERVER['PHP_SELF']."'>Effectuer une autre recherche</a></p>";

	/*
	echo "<pre>";
	print_r($tab_result_recherche);
	echo "</pre>";
	*/

	if(count($tab_result_recherche)>0) {

		$compteur_max_personnes_trouvees=0;

		if(isset($tab_result_recherche['eleve'])) {
			if(count($tab_result_recherche['eleve'])==0) {
				echo "<p style='color:red'>Aucun élève trouvé.</p>\n";
			}
			else {
				echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='form_csv1'>
	<fieldset style='margin-top:0.5em; border: 1px solid grey; background-image: url(\"$gepiPath/images/background/opacite50.png\"); '>
		".add_token_field()."
		<input type='hidden' name='statut[]' value='eleve' />
		<input type='hidden' name='rech_nom' value='$rech_nom' />
		<input type='hidden' name='rech_prenom' value='$rech_prenom' />
		<input type='hidden' name='is_posted_recherche' value='y' />
		<input type='hidden' name='export_csv' value='eleve' />

		<p class='bold' style='margin-top:1em;'>Élèves trouvés&nbsp;: ".count($tab_result_recherche['eleve'])."</p>
		<table class='sortable resizable boireaus boireaus_alt'>
			<tr>
				<th class='nosort'>
					<input type='submit' value='CSV' title='Exporter en CSV' /><br />
					<a href='javascript:modif_case(\"eleve\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/
					<a href='javascript:modif_case(\"eleve\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>
				</th>
				<th class='text' title=\"Trier sur le login\">Login</th>
				<th class='text' title=\"Trier sur l'état actif ou non du compte\">Compte</th>
				<th class='text' title=\"Trier sur le nom prénom\">Nom prénom</th>
				<th class='text' title=\"Trier sur la classe\">Classe</th>
			</tr>";
				for($loop=0;$loop<count($tab_result_recherche['eleve']);$loop++) {
					echo "
			<tr>
				<td><input type='checkbox' name='checkbox_eleve[]' id='checkbox_eleve_".$loop."' value='".$tab_result_recherche['eleve'][$loop]['login']."' /></td>
				<td>".$tab_result_recherche['eleve'][$loop]['td_login']."</td>
				<td>".$tab_result_recherche['eleve'][$loop]['td_compte']."</td>
				<td>".$tab_result_recherche['eleve'][$loop]['td_nom_prenom']."</td>
				<td>".$tab_result_recherche['eleve'][$loop]['td_classe']."</td>
			</tr>";
				}
				$compteur_max_personnes_trouvees=$loop;
				echo "
		</table>
	</fieldset>
</form>";
			}
		}

		if(isset($tab_result_recherche['responsable'])) {
			if(count($tab_result_recherche['responsable'])==0) {
				echo "<p style='color:red'>Aucun responsable trouvé.</p>\n";
			}
			else {
				echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='form_csv2'>
	<fieldset style='margin-top:0.5em; border: 1px solid grey; background-image: url(\"$gepiPath/images/background/opacite50.png\"); '>
		".add_token_field()."
		<input type='hidden' name='statut[]' value='responsable' />
		<input type='hidden' name='rech_nom' value='$rech_nom' />
		<input type='hidden' name='rech_prenom' value='$rech_prenom' />
		<input type='hidden' name='is_posted_recherche' value='y' />
		<input type='hidden' name='export_csv' value='responsable' />

		<p class='bold' style='margin-top:1em;'>Responsables trouvés&nbsp;:".count($tab_result_recherche['responsable'])."</p>
		<table class='sortable resizable boireaus boireaus_alt'>
			<tr>
				<th class='nosort'>
					<input type='submit' value='CSV' title='Exporter en CSV' /><br />
					<a href='javascript:modif_case(\"responsable\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/
					<a href='javascript:modif_case(\"responsable\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>
				</th>
				<th class='text' title=\"Trier sur le numéro PERSONNE_ID du responsable\">Identifiant</th>
				<th class='text' title=\"Trier sur le nom prénom\">Nom prénom</th>
				<th class='text'>Compte</th>
				<th class='text' title=\"Trier sur le prénom nom de l'élève\">Responsable de</th>
			</tr>";
				for($loop=0;$loop<count($tab_result_recherche['responsable']);$loop++) {
					echo "
			<tr>
				<td><input type='checkbox' name='checkbox_responsable[]' id='checkbox_responsable_".$loop."' value='".$tab_result_recherche['responsable'][$loop]['pers_id']."' /></td>
				<td>".$tab_result_recherche['responsable'][$loop]['td_pers_id']."</td>
				<td>".$tab_result_recherche['responsable'][$loop]['nom_prenom']."</td>
				<td>".$tab_result_recherche['responsable'][$loop]['td_compte']."</td>
				<td>".$tab_result_recherche['responsable'][$loop]['td_enfants']."</td>
			</tr>";
				}
				if($compteur_max_personnes_trouvees<$loop) {$compteur_max_personnes_trouvees=$loop;}
				echo "
		</table>
	</fieldset>
</form>";
			}
		}

		if(isset($tab_result_recherche['personnel'])) {
			if(count($tab_result_recherche['personnel'])==0) {
				echo "<p style='color:red'>Aucun personnel trouvé.</p>\n";
			}
			else {
				echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='form_csv3'>
	<fieldset style='margin-top:0.5em; border: 1px solid grey; background-image: url(\"$gepiPath/images/background/opacite50.png\"); '>
		".add_token_field()."
		<input type='hidden' name='statut[]' value='personnel' />
		<input type='hidden' name='rech_nom' value='$rech_nom' />
		<input type='hidden' name='rech_prenom' value='$rech_prenom' />
		<input type='hidden' name='is_posted_recherche' value='y' />
		<input type='hidden' name='export_csv' value='personnel' />

		<p class='bold' style='margin-top:1em;'>Personnels trouvés&nbsp;:".count($tab_result_recherche['personnel'])."</p>
		<table class='sortable resizable boireaus boireaus_alt'>
			<tr>";
				if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
					echo "
				<th class='nosort'>
					<input type='submit' value='CSV' title='Exporter en CSV' />
					<input type='button' id='button_mail_personnel' value='Mail' title='Envoyer un mail' style='display:none' onclick=\"recherche_envoi_mail('personnel')\" />
					<br />
					<a href='javascript:modif_case(\"personnel\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/
					<a href='javascript:modif_case(\"personnel\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>
				</th>
				<th class='text' title=\"Trier sur le login\">Login</th>";
				}
				echo "
				<th class='text' title=\"Trier sur le nom prénom\">Nom prénom</th>
				<th class='text'>Compte</th>";
				if(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe'))) {
					echo "
				<th class='text'>Mail</th>";
				}
				echo "
				<th class='text' title=\"Trier sur le statut\">Statut</th>
				<th class='text' title=\"Trier sur les matières\">Matières</th>
				<th class='text' title=\"Trier sur les classes\">Classes</th>
			</tr>";
				for($loop=0;$loop<count($tab_result_recherche['personnel']);$loop++) {
					echo "
			<tr".$tab_result_recherche['personnel'][$loop]['style_ligne'].">";
					// Login
					if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
						echo "
				<td><input type='checkbox' name='checkbox_personnel[]' id='checkbox_personnel_".$loop."' value='".$tab_result_recherche['personnel'][$loop]['login']."' /></td>";
					}
					echo "
				<td>".$tab_result_recherche['personnel'][$loop]['td_login']."</td>
				<td>".$tab_result_recherche['personnel'][$loop]['nom_prenom']."</td>
				<td>".$tab_result_recherche['personnel'][$loop]['td_compte']."</td>";
					if(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe'))) {
						echo "
				<td>".$tab_result_recherche['personnel'][$loop]['td_email']."</td>";
					}
					echo "
				<td>".$tab_result_recherche['personnel'][$loop]['statut']."</td>
				<td>".$tab_result_recherche['personnel'][$loop]['td_matieres']."</td>
				<td>".$tab_result_recherche['personnel'][$loop]['td_classes']."</td>
			</tr>";
					if($compteur_max_personnes_trouvees<$loop) {$compteur_max_personnes_trouvees=$loop;}
				}
				echo "
		</table>
	</fieldset>
</form>";
			}
		}
		else {
			$_SESSION['rech_statut_personnel']="n";
		}

		$titre_infobulle="Envoi de mail";
		$texte_infobulle="Envoyer un mail à la liste d'utilisateurs suivante&nbsp;: <span id='span_mail_infobulle'></span>";
		$tabdiv_infobulle[]=creer_div_infobulle("div_envoi_email",$titre_infobulle, "", $texte_infobulle, "",30,0,'y','y','n','n',2);


		echo "<p><br /></p>
<script type='text/javascript'>
	function modif_case(categorie, statut) {
		for(k=0;k<$compteur_max_personnes_trouvees;k++){
			if(document.getElementById('checkbox_'+categorie+'_'+k)){
				document.getElementById('checkbox_'+categorie+'_'+k).checked=statut;
			}
		}
		changement();
	}

	document.getElementById('button_mail_personnel').style.display='';
	function recherche_envoi_mail(categorie) {
		var liste_mail='';
		for(k=0;k<$compteur_max_personnes_trouvees;k++){
			if(document.getElementById('checkbox_'+categorie+'_'+k)){
				if(document.getElementById('checkbox_'+categorie+'_'+k).checked==true) {
					if(document.getElementById('email_'+categorie+'_'+k)) {
						if(document.getElementById('email_'+categorie+'_'+k).innerHTML!='') {
							if(liste_mail!='') {liste_mail=liste_mail+', ';}
							liste_mail=liste_mail+document.getElementById('email_'+categorie+'_'+k).innerHTML;
						}
					}
				}
			}
		}
		//alert(liste_mail);
		document.getElementById('span_mail_infobulle').innerHTML=\"<a href='mailto:\"+liste_mail+\"?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI'>\"+liste_mail+\"</a>\";
		afficher_div('div_envoi_email','y',10,10);
	}
</script>";

		require("../lib/footer.inc.php");
		die();
	}
}

?>

<!-- Formulaire de recherche des élèves, responsables, personnels d'après leur nom/prénom -->

<form action='<?php echo $_SERVER['PHP_SELF'];?>' method='post' name='form_rech' onsubmit="valider_form_recherche()" style='width:40em; float:left;'>
	<fieldset style='margin-top:0.5em; border: 1px solid grey; background-image: url("<?php echo $gepiPath;?>/images/background/opacite50.png"); '>
	<?php
		echo "		".add_token_field();
		if(acces("/eleves/index.php", $_SESSION['statut'])) {
			echo "<div style='float:right; width: 8em; text-align:center;' title=\"Rechercher la liste des élèves de telles classes\"><a href='$gepiPath/eleves/index.php#quelles_classes_certaines'>Élèves de telles classes</a></div>";
		}
	?>
		<p></p>

		<table border='0' summary='Critères de la recherche'>
			<tr>
				<td>
					Le nom contient&nbsp;: 
				</td>
				<td>
					<input type='text' name='rech_nom' id='rech_nom' value='<?php if(isset($_SESSION['rech_nom'])) {echo $_SESSION['rech_nom'];}?>' />
				</td>
			</tr>
			<tr>
				<td>
					Le prénom contient&nbsp;: 
				</td>
				<td>
					<input type='text' name='rech_prenom' id='rech_prenom' value='<?php if(isset($_SESSION['rech_prenom'])) {echo $_SESSION['rech_prenom'];}?>' />
				</td>
			</tr>
			<tr>
				<td style='vertical-align:top'>
					Rechercher parmi&nbsp;: 
				</td>
				<td>
					<input type='checkbox' name='statut[]' id='statut_eleve' value='eleve' <?php if((!isset($_SESSION['rech_statut_eleve']))||($_SESSION['rech_statut_eleve']=="y")) {echo "checked ";}?>/><label for='statut_eleve'> élèves</label><br />
					<input type='checkbox' name='statut[]' id='statut_responsable' value='responsable' <?php if((!isset($_SESSION['rech_statut_responsable']))||($_SESSION['rech_statut_responsable']=="y")) {echo "checked ";}?>/><label for='statut_responsable'> responsables</label><br />
					<input type='checkbox' name='statut[]' id='statut_personnel' value='personnel' <?php if((!isset($_SESSION['rech_statut_personnel']))||($_SESSION['rech_statut_personnel']=="y")) {echo "checked ";}?>/><label for='statut_personnel'> personnels</label>
				</td>
			</tr>
		</table>

		<input type='hidden' name='is_posted_recherche' value='y' />
		<input type='submit' id='submit_chercher' value='Chercher' />
		<input type='button' id='button_chercher' value='Chercher' style='display:none' onclick='valider_form_recherche()' />
	</fieldset>
</form>

<!-- Formulaire de recherche des professeurs d'une ou plusieurs matières -->

<form action='<?php echo $_SERVER['PHP_SELF'];?>' method='post' name='form_rech2' style='width:40em; float:left;'>
	<fieldset style='margin-top:0.5em; border: 1px solid grey; background-image: url("<?php echo $gepiPath;?>/images/background/opacite50.png"); '>
	<?php
		echo "		".add_token_field();
	?>
		<p></p>

		<table border='0' summary='Critères de la recherche'>
			<tr>
				<td valign='top'>
					Professeurs enseignant les matières suivantes
				</td>
				<td>
					<?php
						$tab_matieres=array();
						$sql="SELECT DISTINCT m.* FROM matieres m, j_professeurs_matieres jpm ORDER BY m.matiere, m.nom_complet;";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)>0) {
							$cpt=0;
							while($lig=mysql_fetch_object($res)) {
								$tab_matieres[$cpt]['matiere']=$lig->matiere;
								$tab_matieres[$cpt]['nom_complet']=$lig->nom_complet;
								$cpt++;
							}
						}

						if(count($tab_matieres)==0) {
							echo "<span style='color:red'>Aucune matière n'est associée à un enseignant</span>";
						}
						else {
							echo "
					<select name='rech_matiere[]' multiple='true' size='5' title=\"Pour sélectionner plusieurs matières, utilisez CTRL+clic\">
						<!--option value=''>---</option-->";
							for($loop=0;$loop<count($tab_matieres);$loop++) {
								echo "
						<option value='".$tab_matieres[$loop]['matiere']."'>".$tab_matieres[$loop]['nom_complet']."</option>";
							}
							echo "
					</select>";
						}
					?>
				</td>
			</tr>
		</table>

		<p>Restreindre la recherche aux enseignements visibles sur<br /><?php
			for($loop=0;$loop<count($tab_domaines);$loop++) {
				if($loop>0) {
					echo " - ";
				}
				echo "<input type='checkbox' name='rech_domaine[]' id='rech_matiere_domaine_$loop' value='".$tab_domaines[$loop]."' checked /><label for='rech_matiere_domaine_$loop'> ".$tab_domaines[$loop]."</label>";
			}
		?></p>

		<input type='hidden' name='is_posted_recherche2' value='y' />
		<input type='submit' id='submit_chercher2' value='Chercher' />
	</fieldset>
</form>

<!-- Formulaire de recherche des professeurs d'une ou plusieurs classes -->

<form action='<?php echo $_SERVER['PHP_SELF'];?>' method='post' name='form_rech3' style='width:40em; float:left;'>
	<fieldset style='margin-top:0.5em; border: 1px solid grey; background-image: url("<?php echo $gepiPath;?>/images/background/opacite50.png"); '>
	<?php
		echo "		".add_token_field();
	?>
		<p></p>

		<table border='0' summary='Critères de la recherche'>
			<tr>
				<td valign='top'>
					Professeurs enseignant dans les classes suivantes
				</td>
				<td>
					<?php
						$tab_classes=array();
						$sql="SELECT DISTINCT c.* FROM classes c ORDER BY c.classe, c.nom_complet;";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)>0) {
							$cpt=0;
							while($lig=mysql_fetch_object($res)) {
								$tab_classes[$cpt]['id_classe']=$lig->id;
								$tab_classes[$cpt]['classe']=$lig->classe;
								$tab_classes[$cpt]['nom_complet']=$lig->nom_complet;
								$cpt++;
							}
						}

						if(count($tab_classes)==0) {
							echo "<span style='color:red'>Aucune classe n'a été trouvée</span>";
						}
						else {
							echo "
					<select name='rech_classe[]' multiple='true' size='5' title=\"Pour sélectionner plusieurs classes, utilisez CTRL+clic\">
						<!--option value=''>---</option-->";
							for($loop=0;$loop<count($tab_classes);$loop++) {
								echo "
						<option value='".$tab_classes[$loop]['id_classe']."'>".$tab_classes[$loop]['classe'];
								if($tab_classes[$loop]['classe']!=$tab_classes[$loop]['nom_complet']) {
									echo " (".$tab_classes[$loop]['nom_complet'].")";
								}
								echo "</option>";
							}
							echo "
					</select>";
						}
					?>
				</td>
			</tr>
		</table>

		<p>Restreindre la recherche aux enseignements visibles sur<br /><?php
			for($loop=0;$loop<count($tab_domaines);$loop++) {
				if($loop>0) {
					echo " - ";
				}
				echo "<input type='checkbox' name='rech_domaine[]' id='rech_classe_domaine_$loop' value='".$tab_domaines[$loop]."' checked /><label for='rech_classe_domaine_$loop'> ".$tab_domaines[$loop]."</label>";
			}
		?></p>

		<input type='hidden' name='is_posted_recherche3' value='y' />
		<input type='submit' id='submit_chercher3' value='Chercher' />
	</fieldset>
</form>

<div style='clear:both'></div>
<p style='color:red'>A FAIRE :<br />
- Permettre le fonctionnement en ajax en plaçant une partie de la page en include...<br />
Pb pour tri?<br />
- Pouvoir rechercher parmi les personnels, tels statuts, des profs de telle matière (ou sélection de matières)</p>

<script type='text/javascript'>
	document.getElementById('submit_chercher').style.display='none';
	document.getElementById('button_chercher').style.display='';
	document.getElementById('rech_nom').focus();

	function valider_form_recherche() {
		if((document.getElementById('rech_nom').value=='')&&(document.getElementById('rech_prenom').value=='')) {
			alert('Veuillez saisir une portion de nom ou de prénom.');
		}
		else {
			if((document.getElementById('statut_eleve').checked==false)&&(document.getElementById('statut_responsable').checked==false)&&(document.getElementById('statut_personnel').checked==false)) {
				alert('Veuillez choisir au moins une catégorie.');
			}
			else {
				//alert('OK');
				document.forms['form_rech'].submit();
			}
		}
	}
</script>

<?php
require("../lib/footer.inc.php");
?>
