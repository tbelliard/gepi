<?php
/*
 * Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Régis Bouguin, Stephane Boireau
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

if((!isset($indice_aid))||(!preg_match("/^[0-9]{1,}$/", $indice_aid))) {
	//header("Location: ../logout.php?auto=1");
	header("Location: ../accueil.php?msg=Indice AID non défini.");
	die();
}

// Vérification du niveau de gestion des AIDs
if (NiveauGestionAid($_SESSION["login"],$indice_aid) < 5) {
	//header("Location: ../logout.php?auto=1");
	header("Location: ../accueil.php?msg=NiveauGestion AID insuffisant.");
	die();
}

//=======================================
$sql="SELECT * FROM aid_config WHERE indice_aid='$indice_aid';";
$res_famille_aid=mysqli_query($GLOBALS['mysqli'], $sql);
if(mysqli_num_rows($res_famille_aid)==0) {
	header("Location: ../accueil.php?msg=Indice AID $indice_aid inconnu.");
	die();
}
$lig_famille_aid=mysqli_fetch_object($res_famille_aid);
$nom_famille_aid=$lig_famille_aid->nom;
$nom_complet_famille_aid=$lig_famille_aid->nom_complet;
$autoriser_inscript_multiples=$lig_famille_aid->autoriser_inscript_multiples;
//=======================================

include_once 'fonctions_aid.php';
$mysqli = $GLOBALS["mysqli"];
$javascript_specifique = "aid/aid_ajax";

if(!isset($mess)) {$mess="";}

// $is_posted = isset($_POST['is_posted']) ? $_POST['is_posted'] : (isset($is_posted) ? $is_posted : NULL);

$aid_id = isset($aid_id) ? $aid_id : "";
$mode = isset($mode) ? $mode : "";
$action = isset($action) ? $action : "";
$sous_groupe = isset($sous_groupe) ? $sous_groupe : "n";
$parent = isset($parent) ? $parent : "";
$sous_groupe_de =isset($sous_groupe_de) ? $sous_groupe_de : NULL;
$inscrit_direct =isset($inscrit_direct) ? $inscrit_direct : NULL;

// Si is_posted==1, c'est un nouveau AID.
// Si is_posted==2, c'est une modification d'AID.
if (isset($is_posted) && $is_posted) {
	//debug_var();

	$msg = "";

	if(($aid_id!="")&&(preg_match("/^[0-9]{1,}$/", $aid_id))&&(isset($_POST['indice_aid_modif']))&&(preg_match("/^[0-9]{1,}$/", $_POST['indice_aid_modif']))&&($_POST['indice_aid_modif']!=$indice_aid)) {
		$sql="UPDATE aid SET indice_aid='".$_POST['indice_aid_modif']."' WHERE id='".$aid_id."';";
		$update=mysqli_query($mysqli, $sql);
		if(!$update) {
			$msg.="Erreur lors de la modification de la catégorie AID.<br />";
		}
		else {
			$sql="UPDATE aid_appreciations SET indice_aid='".$_POST['indice_aid_modif']."' WHERE id_aid='".$aid_id."';";
			$update=mysqli_query($mysqli, $sql);

			$sql="UPDATE j_aid_eleves SET indice_aid='".$_POST['indice_aid_modif']."' WHERE id_aid='".$aid_id."';";
			$update=mysqli_query($mysqli, $sql);

			$sql="UPDATE j_aid_utilisateurs SET indice_aid='".$_POST['indice_aid_modif']."' WHERE id_aid='".$aid_id."';";
			$update=mysqli_query($mysqli, $sql);

			$sql="UPDATE j_aid_eleves_resp SET indice_aid='".$_POST['indice_aid_modif']."' WHERE id_aid='".$aid_id."';";
			$update=mysqli_query($mysqli, $sql);

			$sql="UPDATE j_aid_utilisateurs_gest SET indice_aid='".$_POST['indice_aid_modif']."' WHERE id_aid='".$aid_id."';";
			$update=mysqli_query($mysqli, $sql);

			$sql="UPDATE aid_appreciations_grp SET indice_aid='".$_POST['indice_aid_modif']."' WHERE id_aid='".$aid_id."';";
			$update=mysqli_query($mysqli, $sql);

			$sql="UPDATE j_groupes_aid SET indice_aid='".$_POST['indice_aid_modif']."' WHERE id_aid='".$aid_id."';";
			$update=mysqli_query($mysqli, $sql);

			$indice_aid=$_POST['indice_aid_modif'];
		}
	}

	if(($is_posted==1)&&(isset($_POST['creer_un_aid_par_classe']))&&($_POST['creer_un_aid_par_classe']=="y")&&(count($id_classe)>1)) {

		$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : array();
		$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : array();
		$prof_matiere=isset($_POST['prof_matiere']) ? $_POST['prof_matiere'] : array();

		$aid_nom_depart=$aid_nom;

		for($loop=0;$loop<count($id_classe);$loop++) {

			$aid_nom=$aid_nom_depart." (".get_nom_classe($id_classe[$loop]).")";
			//$msg.="aid_nom=$aid_nom<br />";

			//$msg.="aid_id=$aid_id<br />";
			// Pour un nouveau AID, on a aid_id=""
			if ("n" == $sous_groupe) {
				Efface_sous_groupe($aid_id);
				//die($aid_id);
			}
			if ("y" == $sous_groupe || $sous_groupe_de != NULL) {
				$reg_parent = Sauve_sous_groupe($aid_id, $parent);
				if (!$reg_parent) {
					$mess = rawurlencode("Erreur lors de l'enregistrement des données pour $aid_nom.");
					header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
					die();
				}
			}
			//$msg.="aid_id=$aid_id<br />";


			//  On regarde si une aid porte déjà le même nom
			$count = mysqli_num_rows(Extrait_aid_sur_nom($aid_nom , $indice_aid));
			check_token();
			if (isset($is_posted) and ($is_posted =="1")) { // nouveau
				// On calcule le nouveau id pour l'aid à insérer → Plus gros id + 1
				$aid_id = Dernier_id ($ordre = "DESC") + 1;
			} else {
				$count--;
			}
			//if ($inscrit_direct) die ($inscrit_direct);


			$reg_data = Sauve_definition_aid ($aid_id , $aid_nom , $aid_num , $indice_aid , $sous_groupe , $inscrit_direct);
			if (!$reg_data) {
				$mess = rawurlencode("Erreur lors de l'enregistrement des données pour $aid_nom.");
				header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
				die();
			}
			else {
				//$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : array();

				//$msg.="aid_id=$aid_id<br />";

				$nb_ele_inscrits=0;
				//for($loop=0;$loop<count($id_classe);$loop++) {
					$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='".$id_classe[$loop]."';";
					//echo "$sql<br />";
					$res_ele_clas=mysqli_query($GLOBALS['mysqli'], $sql);
					if(mysqli_num_rows($res_ele_clas)>0) {
						while($lig_ele=mysqli_fetch_object($res_ele_clas)) {
							// On commence par vérifier que l'élève n'est pas déjà présent dans cette liste, ni dans aucune.
							if ($autoriser_inscript_multiples == 'y') {
								$filtre =  " AND id_aid='".$aid_id."' ";
							}
							else {
								$filtre =  "";
							}
							$sql = "SELECT * FROM j_aid_eleves WHERE (login='".$lig_ele->login."' AND indice_aid='".$indice_aid."'".$filtre.")";
							//echo $sql;
							$test = mysqli_query($GLOBALS["mysqli"], $sql);
							$test2 = mysqli_num_rows($test);
							//$msg = "";
							if ($test2=="0") {
								if($lig_ele->login!='') {
									$reg_data = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_aid_eleves SET login='".$lig_ele->login."', id_aid='$aid_id', indice_aid='$indice_aid'");
									if (!$reg_data) {
										$msg.="Erreur lors de l'ajout de l'élève ".$lig_ele->login."<br />";
									}
									else {
										$nb_ele_inscrits++;
									}
								}
							}
						}
					}
				//}

				//$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : array();

				$nb_profs_inscrits=0;
				for($loop2=0;$loop2<count($login_prof);$loop2++) {
					$test2=Prof_deja_membre($login_prof[$loop2], $aid_id, $indice_aid)->num_rows;
					if ($test2 != "0") {
						$msg.="Le professeur ".$login_prof[$loop2]." que vous avez tenté d'ajouter appartient déjà à cet AID.<br />";
					} else {
						if ($login_prof[$loop2] != '') {
							$reg_data=Sauve_prof_membre($login_prof[$loop2], $aid_id, $indice_aid);
							if (!$reg_data) {
								$msg.="Erreur lors de l'ajout du professeur ".$login_prof[$loop2]." !<br />";
							}
							else {
								$nb_profs_inscrits++;
							}
						}
					}
				}

				if((count($prof_matiere)>0)&&(isset($_POST['restreindre_aux_profs_de_la_classe']))) {
					if($_POST['restreindre_aux_profs_de_la_classe']=="y") {
						for($loop_mat=0;$loop_mat<count($prof_matiere);$loop_mat++) {
							$sql="SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, 
																j_groupes_professeurs jgp, 
																j_groupes_matieres jgm, 
																j_groupes_classes jgc 
															WHERE jgp.login=u.login AND 
																jgp.id_groupe=jgm.id_groupe AND 
																jgp.id_groupe=jgc.id_groupe AND 
																jgc.id_classe='".$id_classe[$loop]."' AND 
																jgm.id_matiere='".$prof_matiere[$loop_mat]."';";
							//echo "$sql<br />";
							$res_prof_mat=mysqli_query($mysqli, $sql);
							if(mysqli_num_rows($res_prof_mat)>0) {
								while($lig_prof_mat=mysqli_fetch_object($res_prof_mat)) {
									$test2=Prof_deja_membre($lig_prof_mat->login, $aid_id, $indice_aid)->num_rows;
									if ($test2 != "0") {
										$msg.="Le professeur ".$lig_prof_mat->login." que vous avez tenté d'ajouter appartient déjà à cet AID.<br />";
									} else {
										$reg_data=Sauve_prof_membre($lig_prof_mat->login, $aid_id, $indice_aid);
										if (!$reg_data) {
											$msg.="Erreur lors de l'ajout du professeur ".$lig_prof_mat->nom." ".$lig_prof_mat->prenom." !<br />";
										}
										else {
											$nb_profs_inscrits++;
										}
									}
								}
							}
						}
					}
					else {
						for($loop_mat=0;$loop_mat<count($prof_matiere);$loop_mat++) {
							$sql="SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, j_professeurs_matieres jpm WHERE jpm.id_professeur=u.login AND jpm.id_matiere='".$prof_matiere[$loop_mat]."' AND u.etat='actif' ORDER BY u.nom, u.prenom;";
							//echo "$sql<br />";
							$res_prof_mat=mysqli_query($mysqli, $sql);
							if(mysqli_num_rows($res_prof_mat)>0) {
								while($lig_prof_mat=mysqli_fetch_object($res_prof_mat)) {
									$test2=Prof_deja_membre($lig_prof_mat->login, $aid_id, $indice_aid)->num_rows;
									if ($test2 != "0") {
										$msg.="Le professeur ".$lig_prof_mat->login." que vous avez tenté d'ajouter appartient déjà à cet AID.<br />";
									} else {
										$reg_data=Sauve_prof_membre($lig_prof_mat->login, $aid_id, $indice_aid);
										if (!$reg_data) {
											$msg.="Erreur lors de l'ajout du professeur ".$lig_prof_mat->nom." ".$lig_prof_mat->prenom." !<br />";
										}
										else {
											$nb_profs_inscrits++;
										}
									}
								}
							}
						}
					}
				}
			}

			if ($count == "1") {
				$msg=$msg." Attention, une AID ($nom_famille_aid) portant le même nom ($aid_nom) existait déja !<br />";
			} else if ($count > 1) {
				$msg=$msg." Attention, plusieurs AID ($nom_famille_aid) portant le même nom ($aid_nom) existaient déja !<br />";
			}
			if ($mode == "multiple") {
				$msg .= "AID ($nom_famille_aid) $aid_nom enregistrée !<br />" ;

				if((isset($nb_ele_inscrits))&&($nb_ele_inscrits>0)) {
					$msg.=$nb_ele_inscrits." élève(s) inscrit(s).<br />";
				}

				if((isset($nb_profs_inscrits))&&($nb_profs_inscrits>0)) {
					$msg.=$nb_profs_inscrits." professeur(s) inscrit(s).<br />";
				}

				$mess = rawurlencode($msg);
				header("Location: add_aid.php?action=add_aid&mode=multiple&msg=$mess&indice_aid=$indice_aid");
				die();
			} else{
				$msg .= "AID ($nom_famille_aid) $aid_nom enregistrée !<br />";

				if((isset($nb_ele_inscrits))&&($nb_ele_inscrits>0)) {
					$msg.=$nb_ele_inscrits." élève(s) inscrit(s).<br />";
				}

				if((isset($nb_profs_inscrits))&&($nb_profs_inscrits>0)) {
					$msg.=$nb_profs_inscrits." professeur(s) inscrit(s).<br />";
				}
			}
		}


		$mess = rawurlencode($msg);
		header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
		die();
	}
	else {
		if ("n" == $sous_groupe) {
			Efface_sous_groupe($aid_id);
			//die($aid_id);
		}
		if ("y" == $sous_groupe || $sous_groupe_de != NULL) {
			$reg_parent = Sauve_sous_groupe($aid_id, $parent);
			if (!$reg_parent) {
				$mess = rawurlencode("Erreur lors de l'enregistrement des données.");
				header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
				die();
			}
		}
	
		//  On regarde si une aid porte déjà le même nom
		$count = mysqli_num_rows(Extrait_aid_sur_nom($aid_nom , $indice_aid));
		check_token();
		if (isset($is_posted) and ($is_posted =="1")) { // nouveau
			// On calcule le nouveau id pour l'aid à insérer → Plus gros id + 1
			$aid_id = Dernier_id ($ordre = "DESC") + 1;
		} else {
			$count--;
		}
	//if ($inscrit_direct) die ($inscrit_direct);

		$reg_data = Sauve_definition_aid ($aid_id , $aid_nom , $aid_num , $indice_aid , $sous_groupe , $inscrit_direct);
		if (!$reg_data) {
			$mess = rawurlencode("Erreur lors de l'enregistrement des données.");
			header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
			die();
		}
		else {
			$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : array();

			$nb_ele_inscrits=0;
			for($loop=0;$loop<count($id_classe);$loop++) {
				$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='".$id_classe[$loop]."';";
				$res_ele_clas=mysqli_query($GLOBALS['mysqli'], $sql);
				if(mysqli_num_rows($res_ele_clas)>0) {
					while($lig_ele=mysqli_fetch_object($res_ele_clas)) {
						// On commence par vérifier que l'élève n'est pas déjà présent dans cette liste, ni dans aucune.
						if ($autoriser_inscript_multiples == 'y') {
							$filtre =  " AND id_aid='".$aid_id."' ";
						}
						else {
							$filtre =  "";
						}
						$sql = "SELECT * FROM j_aid_eleves WHERE (login='".$lig_ele->login."' AND indice_aid='".$indice_aid."'".$filtre.")";
						//echo $sql;
						$test = mysqli_query($GLOBALS["mysqli"], $sql);
						$test2 = mysqli_num_rows($test);
						$msg = "";
						if ($test2=="0") {
							if($lig_ele->login!='') {
								$reg_data = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_aid_eleves SET login='".$lig_ele->login."', id_aid='$aid_id', indice_aid='$indice_aid'");
								if (!$reg_data) {
									$msg.="Erreur lors de l'ajout de l'élève ".$lig_ele->login."<br />";
								}
								else {
									$nb_ele_inscrits++;
								}
							}
						}
					}
				}
			}

			$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : array();

			$nb_profs_inscrits=0;
			for($loop=0;$loop<count($login_prof);$loop++) {
				$test2=Prof_deja_membre($login_prof[$loop], $aid_id, $indice_aid)->num_rows;
				if ($test2 != "0") {
					$msg.="Le professeur ".$login_prof[$loop]." que vous avez tenté d'ajouter appartient déjà à cet AID.<br />";
				} else {
					if ($login_prof[$loop] != '') {
						$reg_data=Sauve_prof_membre($login_prof[$loop], $aid_id, $indice_aid);
						if (!$reg_data) {
							$msg.="Erreur lors de l'ajout du professeur ".$login_prof[$loop]." !<br />";
						}
						else {
							$nb_profs_inscrits++;
						}
					}
				}
			}

			if((count($prof_matiere)>0)&&(isset($_POST['restreindre_aux_profs_de_la_classe']))) {
				if($_POST['restreindre_aux_profs_de_la_classe']=="y") {
					$chaine_classes="";
					for($loop=0;$loop<count($id_classe);$loop++) {
						if($chaine_classes=="") {
							$chaine_classes.=" AND (";
						}
						else {
							$chaine_classes.=" OR ";
						}

						$chaine_classes.="jgc.id_classe='".$id_classe[$loop]."'";
					}
					if($chaine_classes!="") {
						$chaine_classes.=")";
					}

					for($loop_mat=0;$loop_mat<count($prof_matiere);$loop_mat++) {
						$sql="SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, 
															j_groupes_professeurs jgp, 
															j_groupes_matieres jgm, 
															j_groupes_classes jgc 
														WHERE jgp.login=u.login AND 
															jgp.id_groupe=jgm.id_groupe AND 
															jgp.id_groupe=jgc.id_groupe AND 
															jgm.id_matiere='".$prof_matiere[$loop_mat]."' 
															$chaine_classes;";
						//echo "$sql<br />";
						$res_prof_mat=mysqli_query($mysqli, $sql);
						if(mysqli_num_rows($res_prof_mat)>0) {
							while($lig_prof_mat=mysqli_fetch_object($res_prof_mat)) {
								$test2=Prof_deja_membre($lig_prof_mat->login, $aid_id, $indice_aid)->num_rows;
								if ($test2 != "0") {
									$msg.="Le professeur ".$lig_prof_mat->login." que vous avez tenté d'ajouter appartient déjà à cet AID.<br />";
								} else {
									$reg_data=Sauve_prof_membre($lig_prof_mat->login, $aid_id, $indice_aid);
									if (!$reg_data) {
										$msg.="Erreur lors de l'ajout du professeur ".$lig_prof_mat->nom." ".$lig_prof_mat->prenom." !<br />";
									}
									else {
										$nb_profs_inscrits++;
									}
								}
							}
						}
					}
				}
				else {
					for($loop_mat=0;$loop_mat<count($prof_matiere);$loop_mat++) {
						$sql="SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, j_professeurs_matieres jpm WHERE jpm.id_professeur=u.login AND jpm.id_matiere='".$prof_matiere[$loop_mat]."' AND u.etat='actif' ORDER BY u.nom, u.prenom;";
						//echo "$sql<br />";
						$res_prof_mat=mysqli_query($mysqli, $sql);
						if(mysqli_num_rows($res_prof_mat)>0) {
							while($lig_prof_mat=mysqli_fetch_object($res_prof_mat)) {
								$test2=Prof_deja_membre($lig_prof_mat->login, $aid_id, $indice_aid)->num_rows;
								if ($test2 != "0") {
									$msg.="Le professeur ".$lig_prof_mat->login." que vous avez tenté d'ajouter appartient déjà à cet AID.<br />";
								} else {
									$reg_data=Sauve_prof_membre($lig_prof_mat->login, $aid_id, $indice_aid);
									if (!$reg_data) {
										$msg.="Erreur lors de l'ajout du professeur ".$lig_prof_mat->nom." ".$lig_prof_mat->prenom." !<br />";
									}
									else {
										$nb_profs_inscrits++;
									}
								}
							}
						}
					}
				}
			}
		}

		if ($count == "1") {
			$msg=$msg." Attention, une AID ($nom_famille_aid) portant le même nom ($aid_nom) existait déja !<br />";
		} else if ($count > 1) {
			$msg=$msg." Attention, plusieurs AID ($nom_famille_aid) portant le même nom ($aid_nom) existaient déja !<br />";
		}
		if ($mode == "multiple") {
			$msg .= "AID ($nom_famille_aid) $aid_nom enregistrée !<br />" ;

			if((isset($nb_ele_inscrits))&&($nb_ele_inscrits>0)) {
				$msg.=$nb_ele_inscrits." élève(s) inscrit(s).<br />";
			}

			if((isset($nb_profs_inscrits))&&($nb_profs_inscrits>0)) {
				$msg.=$nb_profs_inscrits." professeur(s) inscrit(s).<br />";
			}

			$mess = rawurlencode($msg);
			header("Location: add_aid.php?action=add_aid&mode=multiple&msg=$mess&indice_aid=$indice_aid");
			die();
		} else {
			$msg .= "AID ($nom_famille_aid) $aid_nom enregistrée !<br />";

			if((isset($nb_ele_inscrits))&&($nb_ele_inscrits>0)) {
				$msg.=$nb_ele_inscrits." élève(s) inscrit(s).<br />";
			}

			if((isset($nb_profs_inscrits))&&($nb_profs_inscrits>0)) {
				$msg.=$nb_profs_inscrits." professeur(s) inscrit(s).<br />";
			}

			$mess = rawurlencode($msg);
			header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
			die();
		}
	}
} else {
	// on remplit tous les champs pour n'avoir qu'un affichage
	
	$id_aid_prec=-1;
	$id_aid_suiv=-1;
	$temoin_tmp=0;
	$aid_nom = "";
	$aid_num = "";
	$nouveau = "Entrez un nom : ";
	$is_posted = (isset($action) && $action == "modif_aid") ? 2 : ((isset($action) && $action == "add_aid") ? 1 : "" );

	if ("modif_aid" == $action) {
		$res_aid_tmp = Extrait_aid_sur_indice_aid ($indice_aid);
		if(mysqli_num_rows($res_aid_tmp)>0) {
			while($lig_aid_tmp=mysqli_fetch_object($res_aid_tmp)){
				if($lig_aid_tmp->id==$aid_id) {
					$temoin_tmp=1;
					if($lig_aid_tmp=mysqli_fetch_object($res_aid_tmp)){
						$id_aid_suiv=$lig_aid_tmp->id;
					}
					else{
						$id_aid_suiv=-1;
					}
				}
				if($temoin_tmp==0) {
					$id_aid_prec=$lig_aid_tmp->id;
				}
			}
		}
	}
	$res_parents=Extrait_aid_sur_indice_aid ($indice_aid);
	
	if ($action == "modif_aid") {
		$calldata = Extrait_aid_sur_id ($aid_id, $indice_aid)->fetch_object();
		$aid_nom = $calldata->nom;
		$aid_num = $calldata->numero;
		$sous_groupe = $calldata->sous_groupe;
		$nouveau = "Entrez le nouveau nom à la place de l'ancien : ";
		if ('y' == $sous_groupe) {
			$res_groupe_de=Extrait_parent ($aid_id);
			if ($res_groupe_de->num_rows) {
				$sous_groupe_de = $res_groupe_de->fetch_object()->parent;
			}
		}
	}
}

//**************** EN-TETE *********************
if ($action == "modif_aid") {
	$titre_page = "Gestion des AID | Modifier Une AID ($nom_famille_aid)";
}
else {
	$titre_page = "Gestion des AID | Ajouter Une AID ($nom_famille_aid)";
}
require_once("../lib/header.inc.php");


// debug_var();
//**************** FIN EN-TETE *****************
$NiveauGestionAid_categorie=NiveauGestionAid($_SESSION["login"],$indice_aid);
$NiveauGestionAid_AID_courant=NiveauGestionAid($_SESSION["login"],$indice_aid, $aid_id);

if ($_SESSION['statut'] == 'professeur') {
	$retour = 'index2.php';
} else {
	$retour = 'index.php';
}

?>
<p class="bold">
	<a href="<?php echo $retour; ?>?indice_aid=<?php echo $indice_aid; ?>">
		<img src='../images/icons/back.png' alt='Retour' class='back_link'/>
		Retour
	</a>

<?php
	if ($action == "modif_aid") {


		if($id_aid_prec!=-1) {
?>
	|
	<a href='<?php echo $_SERVER['PHP_SELF']; ?>?action=modif_aid&amp;aid_id=<?php echo $id_aid_prec; ?>&amp;indice_aid=<?php echo $indice_aid; ?>' 
	   onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
		AID précédent
	</a>
<?php
		}
		if($id_aid_suiv!=-1) {
?>
	|
	<a href='<?php echo $_SERVER['PHP_SELF']; ?>?action=modif_aid&amp;aid_id=<?php echo $id_aid_suiv; ?>&amp;indice_aid=<?php echo $indice_aid; ?>'
	   onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
		AID suivant
	</a>
<?php
		}
	}

	if($NiveauGestionAid_AID_courant>=1) {
		echo "
	| <a href='modify_aid.php?flag=eleve&aid_id=".$aid_id."&indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Élèves de l'AID</a>";
	}
	if($NiveauGestionAid_AID_courant>=2) {
		echo "
	| <a href='modify_aid.php?flag=prof&aid_id=".$aid_id."&indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Professeurs de l'AID</a>";
	}
	if($NiveauGestionAid_AID_courant>=5) {
		echo "
	| <a href='modify_aid.php?flag=prof_gest&aid_id=".$aid_id."&indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestionnaires de l'AID</a>";
	}
	if($NiveauGestionAid_categorie==10) {
		echo "
	| <a href='config_aid.php?indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Catégorie AID</a>";
	}
?>

</p>

<form enctype="multipart/form-data" action="add_aid.php" method="post">

	<h2><?php echo $nom_famille_aid; ?>
		<input type="submit" value="Enregistrer" />
	</h2>

    <p><?php echo $nouveau; ?></p>

	<?php
		echo add_token_field();
	?>

	<p>
		<label for="aidRegNom">
			Nom : 
		</label>
		<input type="text" 
			   id="aidRegNom" 
			   name="aid_nom" 
			   size="100" 
				<?php echo " value=\"".$aid_nom."\"";?>/>
		<a href='#' onclick="document.getElementById('aidRegNom').value='<?php echo remplace_accents($nom_famille_aid, "all");?>'" title="Prendre pour nom d'AID, le nom de la catégorie d'AID."><img src='../images/icons/wizard.png' class='icone16' alt='Magic' /></a>
	</p>
	<p>
		<label for="aidRegNum">
			Numéro (fac.) : 
		</label>
		<input type="text" id="aidRegNum" name="aid_num" size="4" <?php echo " value=\"".$aid_num."\""; ?> />
	</p>

	<div <?php if (!Multiples_possible ($indice_aid)) {echo " style='display:none;'";} ?> >
		<p title="Cochez pour affecter un parent puis choisissez le parent">
			<label for="sous_groupe">
				Sous-groupe d'une autre AID
			</label>
			<input type="checkbox"
				   name='sous_groupe'
				   id='sous_groupe'
				   value="y"
					<?php if ($sous_groupe=='y') {echo " checked='checked' ";} ?>  
				   onchange="afficher_cacher_parent();"
				   />
		</p>

		<div id="aidParent" >
		
<?php if((isset($res_parents) && $res_parents->num_rows)){ ?>
			<select name="parent" id="choix_parent">
				<option value="" 
						<?php if (!$sous_groupe_de) {echo " selected='selected' ";} ?>
						>
					Aucun parent

				<?php while ($parent = $res_parents->fetch_object()){ ?>
				<option value="<?php echo $parent->id; ?>" 
						<?php if ($parent->id == $sous_groupe_de) {echo " selected='selected' ";} ?>
						>
					<?php echo $parent->nom; ?>
				</option>
				<?php } ?>
			</select>
<?php } ?>
		</div>

		<h3>Élèves</h3>
		<div style='margin-left:3em;'>
		<p>
			<label for="inscrit_direct">
				Un élève peut s'inscrire directement
			</label>
			<input type="checkbox"
				   name='inscrit_direct'
				   id='inscrit_direct'
				   value="y"
					<?php if (eleve_inscrit_direct($aid_id, $indice_aid)) {echo " checked='checked' ";} ?>
				   />
		</p>
		</div>
	</div>

	<?php

		//echo "aid_id=$aid_id<br />";
		if((isset($aid_id))&&($aid_id!="")) {
			echo "
		<br />
		<h3>Catégorie</h3>
		<div style='margin-left:3em;'>
			<p>Catégorie à laquelle l'AID est rattaché&nbsp;: 
				<select name='indice_aid_modif'>";
			$sql="SELECT * FROM aid_config ORDER BY nom, nom_complet;";
			$res_cat_aid=mysqli_query($mysqli, $sql);
			while($lig_cat_aid=mysqli_fetch_object($res_cat_aid)) {
				echo "
					<option value='".$lig_cat_aid->indice_aid."'".(($lig_cat_aid->indice_aid==$indice_aid) ? " selected='true'" : "").">".$lig_cat_aid->nom." (".$lig_cat_aid->nom_complet.")</option>";
			}
			echo "
				</select>
			</p>
		</div>";
		}

		//echo "action=$action<br />";
		if ($action=="add_aid") {
			$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_classes jec WHERE c.id=jec.id_classe ORDER BY c.classe, c.nom_complet;";
			$res_classes=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res_classes)>0) {
				if (!Multiples_possible ($indice_aid)) {
					echo "<h3>Élèves</h3>";
				}
				echo "<div style='margin-left:3em;'>";
				$tab_txt=array();
				$tab_nom_champ=array();
				$tab_id_champ=array();
				$tab_valeur_champ=array();
				echo "<p style='margin-top:1em;'>Inscrire dans l'AID tous les élèves des classes cochées&nbsp;:</p>";
				while($lig_clas=mysqli_fetch_object($res_classes)) {
					$tab_txt[]=$lig_clas->classe;
					$tab_nom_champ[]="id_classe[]";
					$tab_id_champ[]="id_classe_".$lig_clas->id;
					$tab_valeur_champ[]=$lig_clas->id;
				}
				tab_liste_checkbox($tab_txt, $tab_nom_champ, $tab_id_champ, $tab_valeur_champ, "checkbox_change", "modif_coche", 5);
				echo "<p>Si vous préférez ne pas affecter tous les élèves de telle(s) ou telle(s) classe(s) dans le $nom_famille_aid, vous pourrez gérer plus finement l'inscription par la suite.</p>";
				echo "</div>";


				echo "<div style='margin-left:3em;'>";
				echo "<p style='text-indent:-2em;margin-left:2em;'><input type='checkbox' id='creer_un_aid_par_classe' name='creer_un_aid_par_classe' value='y' /><label for='creer_un_aid_par_classe'>Ajouter un $nom_famille_aid par classe cochée <em>(un suffixe au nom de la classe sera ajouté)</em>.</label><br />Sinon, on ne crée qu'un $nom_famille_aid avec tous les élèves des classes cochés dans cet unique $nom_famille_aid.</p>";
				echo "</div>";
			}

			$sql="SELECT DISTINCT u.login, u.nom, u.prenom FROM utilisateurs u WHERE u.statut='professeur' AND u.etat='actif' ORDER BY u.nom, u.prenom;";
			$res_prof=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res_prof)>0) {
				echo "<h3>Professeurs</h3>";
				echo "<div style='margin-left:3em;'>";
				$tab_txt=array();
				$tab_nom_champ=array();
				$tab_id_champ=array();
				$tab_valeur_champ=array();
				echo "<p style='margin-top:1em;'>Inscrire comme professeur(s) responsable(s) de cet AID les professeurs cochés&nbsp;:</p>";
				$cpt_prof=0;
				while($lig_prof=mysqli_fetch_object($res_prof)) {
					$tab_txt[]=casse_mot($lig_prof->nom, "maj")." ".casse_mot($lig_prof->prenom, "majf2");
					$tab_nom_champ[]="login_prof[]";
					$tab_id_champ[]="login_prof_".$cpt_prof;
					$tab_valeur_champ[]=$lig_prof->login;
					$cpt_prof++;
				}
				tab_liste_checkbox($tab_txt, $tab_nom_champ, $tab_id_champ, $tab_valeur_champ, "checkbox_change_prof", "modif_coche_prof", 5);
				echo "<p>Si vous préférez ne pas affecter les professeurs maintenant, vous pourrez le faire plus tard.</p>";
				echo "</div>";


				echo "<div style='margin-left:3em; margin-top:1em;'>";
				echo "<p style='text-indent:-3em;margin-left:3em;'>
					Vous pouvez également ou alternativement, affecter des profs avec les contraintes suivantes&nbsp;:<br />
					<input type='radio' name='restreindre_aux_profs_de_la_classe' id='restreindre_aux_profs_de_la_classe_y' value='y' checked /><label for='restreindre_aux_profs_de_la_classe_y'>Inscrire le(s) professeur(s) de la (des) matière(s) suivante(s) enseignant par ailleurs dans la(les) classe(s) sélectionnée(s)</label><br />
					<input type='radio' name='restreindre_aux_profs_de_la_classe' id='restreindre_aux_profs_de_la_classe_n' value='n' /><label for='restreindre_aux_profs_de_la_classe_n'>Inscrire le(s) professeur(s) de la (des) matière(s) suivante(s) sans se restreindre aux professeurs enseignant par ailleurs dans la(les) classe(s) sélectionnée(s)</label>.<br />
					Si vous ne cochez aucune matière, ce paramètre ne sera pas pris en compte.
				</p>";
				echo liste_checkbox_matieres(array(), 'prof_matiere', 'cocher_decocher', "y", "m.matiere, m.nom_complet");
				echo "</div>";

			}

		}
	?>

	<p style='margin-top:1em;' class="center">
		<input type="hidden" name="indice_aid" value="<?php echo $indice_aid; ?>" />
		<input type="hidden" name="aid_id" value="<?php echo $aid_id; ?>" />
		<input type="hidden" name="mode" value="<?php echo $mode; ?>" />
		<input type="hidden" name="is_posted" value="<?php echo $is_posted; ?>" />
		<input type="submit" value="Enregistrer" />
	</p>
	
</form>

<script type='text/javascript'>
if(document.getElementById('aidRegNom')) {
	document.getElementById('aidRegNom').focus();
}
</script>

<script type='text/javascript'>
	function afficher_cacher(id)
{
    if(document.getElementById(id).style.visibility=="hidden")
    {
        document.getElementById(id).style.visibility="visible";
    }
    else
    {
        document.getElementById(id).style.visibility="hidden";
    }
    return true;
}

	function afficher_cacher_parent()
{
    if(document.getElementById('sous_groupe').checked)
    {
        document.getElementById('choix_parent').style.visibility="visible";
    }
    else
    {
        document.getElementById('choix_parent').style.visibility="hidden";
    }
    return true;
}

afficher_cacher_parent();
</script>

<?php 
require("../lib/footer.inc.php");
