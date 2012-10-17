<?php
/*
 *
 * Copyright 2001-2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_ent/index_itop.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_ent/index_itop.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Rapprochement des comptes ENT/GEPI : ENT ITOP',
statut='';";
$insert=mysql_query($sql);
}
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$msg="";
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

function formate_date2($chaine, $from, $to) {
	$retour=$chaine;

	if($from=="jj/mm/aaaa") {
		$tab=explode("/", $chaine);

		if(isset($tab[2])) {
			if($to=="aaaammjj") {
				if($tab[2]>100) {
					$retour=$tab[2].sprintf($tab[1], "%1$02d").sprintf($tab[0], "%1$02d");
				}
			}
		}
	}

	return $retour;
}

// Menage:
$sql="DELETE FROM sso_table_correspondance WHERE login_gepi='' OR login_sso='';";
$menage=mysql_query($sql);

if(isset($_POST['enregistrement_eleves'])) {
	check_token();

	$ligne=isset($_POST['ligne']) ? $_POST['ligne'] : NULL;

	$nb_reg=0;

	if(!isset($ligne)) {
		$msg="Aucun enregistrement d'association n'a été demandée.<br />";
	}
	else {
		$ligne_tempo2=array();
		$sql="SELECT * FROM tempo2;";
		$res=mysql_query($sql);
		while($lig=mysql_fetch_object($res)) {
			$ligne_tempo2[$lig->col1]=$lig->col2;
		}

		for($loop=0;$loop<count($ligne);$loop++) {
			// On a eu un choix de login
			if((isset($_POST['login_'.$ligne[$loop]]))&&($_POST['login_'.$ligne[$loop]]!='')) {
				if(isset($ligne_tempo2[$ligne[$loop]])) {
					$tab=explode(";", $ligne_tempo2[$ligne[$loop]]);

					$sql="SELECT login FROM eleves WHERE login='".mysql_real_escape_string($_POST['login_'.$ligne[$loop]])."';";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)==0) {
						$msg.="Le login élève Gepi ".$_POST['login_'.$ligne[$loop]]." proposé n'existe pas.<br />\n";
					}
					else {
						$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".mysql_real_escape_string($tab[0])."';";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)==0) {
							$sql="SELECT * FROM sso_table_correspondance WHERE login_gepi='".$_POST['login_'.$ligne[$loop]]."';";
							$test=mysql_query($sql);
							if(mysql_num_rows($test)==0) {
								$sql="INSERT INTO sso_table_correspondance SET login_gepi='".$_POST['login_'.$ligne[$loop]]."', login_sso='".mysql_real_escape_string($tab[0])."';";
								$insert=mysql_query($sql);
								if(!$insert) {
									$msg.="Erreur lors de l'insertion de l'association ".$tab[0]." &gt; ".$_POST['login_'.$ligne[$loop]]."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
							else {
								$sql="UPDATE sso_table_correspondance SET login_sso='".mysql_real_escape_string($tab[0])."' WHERE login_gepi='".$_POST['login_'.$ligne[$loop]]."';";
								$update=mysql_query($sql);
								if(!$update) {
									$msg.="Erreur lors de la mise à jour de l'association ".$tab[0]." &gt; ".$_POST['login_'.$ligne[$loop]]."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
						}
						else {
							$lig=mysql_fetch_object($test);
							$msg.="Le GUID $tab[0] est déjà associé à $lig->login_gepi<br />\n";
						}
					}
				}
				else {
					$msg.="Aucun enregistrement pour la ligne $loop<br />";
				}
			}
			else {
				if(isset($ligne_tempo2[$ligne[$loop]])) {
					$tab=explode(";", $ligne_tempo2[$ligne[$loop]]);

					$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".mysql_real_escape_string($tab[0])."';";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)==0) {
						$naissance=(isset($tab[5])) ? $tab[5] : "";
						if(!preg_match("#[0-9]{2}/[0-9]{2}/[0-9]{4}#", $naissance)) {$naissance="";}

						if($naissance!="") {
							$sql="SELECT * FROM eleves WHERE nom='".mysql_real_escape_string($tab[1])."' AND prenom='".mysql_real_escape_string($tab[2])."' AND naissance='".formate_date2($naissance, "jj/mm/aaaa", "aaaammjj")."'";
						}
						else {
							$sql="SELECT * FROM eleves WHERE nom='".mysql_real_escape_string($tab[1])."' AND prenom='".mysql_real_escape_string($tab[2])."' ORDER BY naissance;";
						}
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==1) {
							// Un seul élève correspond
							$lig=mysql_fetch_object($res);

							$sql="SELECT 1=1 FROM sso_table_correspondance WHERE login_gepi='$lig->login';";
							$test=mysql_query($sql);
							if(mysql_num_rows($test)==0) {
								$sql="INSERT INTO sso_table_correspondance SET login_gepi='$lig->login', login_sso='".mysql_real_escape_string($tab[0])."';";
								$insert=mysql_query($sql);
								if(!$insert) {
									$msg.="Erreur lors de l'insertion de l'association ".$tab[0]." &gt; ".$lig->login."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
							else {
								$sql="UPDATE sso_table_correspondance SET login_sso='".mysql_real_escape_string($tab[0])."' WHERE login_gepi='$lig->login';";
								$update=mysql_query($sql);
								if(!$update) {
									$msg.="Erreur lors de la mise à jour de l'association ".$tab[0]." &gt; ".$lig->login."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
						}
						else {
							// On ne doit pas arriver là
							$msg.="Plusieurs enregistrements dans la table 'eleves' pour ".$tab[1]." ".$tab[2]." !<br />\n";
						}
					}
					else {
						$lig=mysql_fetch_object($test);
						$msg.="Le GUID $tab[0] est déjà associé à $lig->login_gepi<br />\n";
					}
				}
				else {
					$msg.="Aucun enregistrement pour la ligne $loop<br />";
				}
			}
		}
	}

	if($nb_reg>0) {
		$msg.="$nb_reg enregistrement(s) effectué(s).<br />\n";
	}

	$mode="consult_eleves";
}


if(isset($_POST['enregistrement_responsables'])) {
	check_token();

	$ligne=isset($_POST['ligne']) ? $_POST['ligne'] : NULL;

	$nb_reg=0;

	if(!isset($ligne)) {
		$msg="Aucun enregistrement d'association n'a été demandée.<br />";
	}
	else {
		$ligne_tempo2=array();
		$sql="SELECT * FROM tempo2;";
		$res=mysql_query($sql);
		while($lig=mysql_fetch_object($res)) {
			$ligne_tempo2[$lig->col1]=$lig->col2;
		}

		for($loop=0;$loop<count($ligne);$loop++) {
			// On a eu un choix de login
			if((isset($_POST['login_'.$ligne[$loop]]))&&($_POST['login_'.$ligne[$loop]]!='')) {
				if(isset($ligne_tempo2[$ligne[$loop]])) {
					$tab=explode(";", $ligne_tempo2[$ligne[$loop]]);

					$sql="SELECT login FROM resp_pers WHERE login='".mysql_real_escape_string($_POST['login_'.$ligne[$loop]])."';";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)==0) {
						$msg.="Le login responsable Gepi ".$_POST['login_'.$ligne[$loop]]." proposé n'existe pas.<br />\n";
					}
					else {
						$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".mysql_real_escape_string($tab[0])."';";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)==0) {
							$sql="SELECT * FROM sso_table_correspondance WHERE login_gepi='".$_POST['login_'.$ligne[$loop]]."';";
							$test=mysql_query($sql);
							if(mysql_num_rows($test)==0) {
								$sql="INSERT INTO sso_table_correspondance SET login_gepi='".$_POST['login_'.$ligne[$loop]]."', login_sso='".mysql_real_escape_string($tab[0])."';";
								$insert=mysql_query($sql);
								if(!$insert) {
									$msg.="Erreur lors de l'insertion de l'association ".$tab[0]." &gt; ".$_POST['login_'.$ligne[$loop]]."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
							else {
								$sql="UPDATE sso_table_correspondance SET login_sso='".mysql_real_escape_string($tab[0])."' WHERE login_gepi='".$_POST['login_'.$ligne[$loop]]."';";
								$update=mysql_query($sql);
								if(!$update) {
									$msg.="Erreur lors de la mise à jour de l'association ".$tab[0]." &gt; ".$_POST['login_'.$ligne[$loop]]."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
						}
						else {
							$lig=mysql_fetch_object($test);
							$msg.="Le GUID $tab[0] est déjà associé à $lig->login_gepi<br />\n";
						}
					}
				}
				else {
					$msg.="Aucun enregistrement pour la ligne $loop<br />";
				}
			}
			else {
				if(isset($ligne_tempo2[$ligne[$loop]])) {
					$tab=explode(";", $ligne_tempo2[$ligne[$loop]]);

					$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".mysql_real_escape_string($tab[0])."';";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)==0) {

						$chaine="";
						if((isset($tab[5]))&&($tab[5]!="")) {
							$chaine.="s.login_sso='".mysql_real_escape_string($tab[5])."'";
						}
						if((isset($tab[6]))&&($tab[6]!="")) {
							if($chaine!="") {$chaine.=" OR ";}
							$chaine.="s.login_sso='".mysql_real_escape_string($tab[6])."'";
						}
						if((isset($tab[7]))&&($tab[7]!="")) {
							if($chaine!="") {$chaine.=" OR ";}
							$chaine.="s.login_sso='".mysql_real_escape_string($tab[7])."'";
						}

						$cpt_resp=0;
						$tab_resp=array();
						$tab_resp_login=array();
						if($chaine!="") {
							$sql="SELECT e.* FROM eleves e, sso_table_correspondance s WHERE ($chaine) AND e.login=s.login_gepi ORDER BY e.nom, e.prenom;";
							//echo "$sql<br />";
							$res_ele=mysql_query($sql);
							if(mysql_num_rows($res_ele)>0) {
								$cpt_ele=0;
								while($lig_ele=mysql_fetch_object($res_ele)) {
									$sql="SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r WHERE r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' AND rp.nom='".mysql_real_escape_string($tab[1])."' AND rp.prenom='".mysql_real_escape_string($tab[2])."' AND rp.login!='';";
									//echo "$sql<br />";
									$res_resp=mysql_query($sql);
									if(mysql_num_rows($res_resp)>0) {
										while($lig_resp=mysql_fetch_object($res_resp)) {
											if(!in_array($lig_resp->login, $tab_resp_login)) {
												$tab_resp_login[]=$lig_resp->login;
												$tab_resp[$cpt_resp]['login']=$lig_resp->login;
												$tab_resp[$cpt_resp]['civilite']=$lig_resp->civilite;
												$tab_resp[$cpt_resp]['nom']=$lig_resp->nom;
												$tab_resp[$cpt_resp]['prenom']=$lig_resp->prenom;
												$tab_resp[$cpt_resp]['resp_legal']=$lig_resp->resp_legal;
												$tab_resp[$cpt_resp]['info']=$lig_resp->civilite." ".casse_mot($lig_resp->nom,'maj')." ".casse_mot($lig_resp->prenom,'majf2');
												$tab_resp[$cpt_resp]['info'].=" (N° ".$lig_resp->pers_id.")";

												$tab_resp[$cpt_resp]['info'].=" (Légal ".$lig_resp->resp_legal.")";
												$cpt_resp++;
											}
										}
									}
									else {
										$sql="SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r WHERE r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' AND rp.login!='';";
										$res_resp=mysql_query($sql);
										if(mysql_num_rows($res_resp)>0) {
											while($lig_resp=mysql_fetch_object($res_resp)) {
												if(!in_array($lig_resp->login, $tab_resp_login)) {
													$tab_resp_login[]=$lig_resp->login;
													$tab_resp[$cpt_resp]['login']=$lig_resp->login;
													$tab_resp[$cpt_resp]['civilite']=$lig_resp->civilite;
													$tab_resp[$cpt_resp]['nom']=$lig_resp->nom;
													$tab_resp[$cpt_resp]['prenom']=$lig_resp->prenom;
													$tab_resp[$cpt_resp]['resp_legal']=$lig_resp->resp_legal;
													$tab_resp[$cpt_resp]['info']=$lig_resp->civilite." ".casse_mot($lig_resp->nom,'maj')." ".casse_mot($lig_resp->prenom,'majf2');
													$tab_resp[$cpt_resp]['info'].=" (N° ".$lig_resp->pers_id.")";
													$tab_resp[$cpt_resp]['info'].=" (Légal ".$lig_resp->resp_legal.")";
													$cpt_resp++;
												}
											}
										}
									}
								}
							}

							if(count($tab_resp)==1) {
								// Un seul responsable correspond
								$lig=mysql_fetch_object($res);

								$sql="SELECT 1=1 FROM sso_table_correspondance WHERE login_gepi='".$tab_resp_login[0]."';";
								$test=mysql_query($sql);
								if(mysql_num_rows($test)==0) {
									$sql="INSERT INTO sso_table_correspondance SET login_gepi='".$tab_resp_login[0]."', login_sso='".mysql_real_escape_string($tab[0])."';";
									$insert=mysql_query($sql);
									if(!$insert) {
										$msg.="Erreur lors de l'insertion de l'association ".$tab[0]." &gt; ".$tab_resp_login[0]."<br />\n";
									}
									else {
										$nb_reg++;
									}
								}
								else {
									$sql="UPDATE sso_table_correspondance SET login_sso='".mysql_real_escape_string($tab[0])."' WHERE login_gepi='".$tab_resp_login[0]."';";
									$update=mysql_query($sql);
									if(!$update) {
										$msg.="Erreur lors de la mise à jour de l'association ".$tab[0]." &gt; ".$tab_resp_login[0]."<br />\n";
									}
									else {
										$nb_reg++;
									}
								}
							}
							else {
								$msg.="Responsable ".$tab[1]." ".$tab[2]." non identifié.<br />\n";
							}
						}
						else {
							$msg.="Aucun élève associé n'a été trouvé.<br />\n";
						}
					}
					else {
						$lig=mysql_fetch_object($test);
						$msg.="Le GUID $tab[0] est déjà associé à $lig->login_gepi<br />\n";
					}
				}
				else {
					$msg.="Aucun enregistrement pour la ligne $loop<br />";
				}
			}
		}
	}

	if($nb_reg>0) {
		$msg.="$nb_reg enregistrement(s) effectué(s).<br />\n";
	}

	$mode="consult_responsables";
}


if(isset($_POST['enregistrement_personnels'])) {
	check_token();

	$ligne=isset($_POST['ligne']) ? $_POST['ligne'] : NULL;

	$nb_reg=0;

	if(!isset($ligne)) {
		$msg="Aucun enregistrement d'association n'a été demandée.<br />";
	}
	else {
		$ligne_tempo2=array();
		$sql="SELECT * FROM tempo2;";
		$res=mysql_query($sql);
		while($lig=mysql_fetch_object($res)) {
			$ligne_tempo2[$lig->col1]=$lig->col2;
		}

		for($loop=0;$loop<count($ligne);$loop++) {
			// On a eu un choix de login
			if((isset($_POST['login_'.$ligne[$loop]]))&&($_POST['login_'.$ligne[$loop]]!='')) {
				if(isset($ligne_tempo2[$ligne[$loop]])) {
					$tab=explode(";", $ligne_tempo2[$ligne[$loop]]);

					$sql="SELECT login FROM utilisateurs WHERE login='".mysql_real_escape_string($_POST['login_'.$ligne[$loop]])."' AND statut!='eleve' AND statut!='responsable';";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)==0) {
						$msg.="Le login de personnel Gepi ".$_POST['login_'.$ligne[$loop]]." proposé n'existe pas.<br />\n";
					}
					else {
						$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".mysql_real_escape_string($tab[0])."';";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)==0) {
							$sql="SELECT * FROM sso_table_correspondance WHERE login_gepi='".$_POST['login_'.$ligne[$loop]]."';";
							$test=mysql_query($sql);
							if(mysql_num_rows($test)==0) {
								$sql="INSERT INTO sso_table_correspondance SET login_gepi='".$_POST['login_'.$ligne[$loop]]."', login_sso='".mysql_real_escape_string($tab[0])."';";
								$insert=mysql_query($sql);
								if(!$insert) {
									$msg.="Erreur lors de l'insertion de l'association ".$tab[0]." &gt; ".$_POST['login_'.$ligne[$loop]]."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
							else {
								$sql="UPDATE sso_table_correspondance SET login_sso='".mysql_real_escape_string($tab[0])."' WHERE login_gepi='".$_POST['login_'.$ligne[$loop]]."';";
								$update=mysql_query($sql);
								if(!$update) {
									$msg.="Erreur lors de la mise à jour de l'association ".$tab[0]." &gt; ".$_POST['login_'.$ligne[$loop]]."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
						}
						else {
							$lig=mysql_fetch_object($test);
							$msg.="Le GUID $tab[0] est déjà associé à $lig->login_gepi<br />\n";
						}
					}
				}
				else {
					$msg.="Aucun enregistrement pour la ligne $loop<br />";
				}
			}
			else {
				if(isset($ligne_tempo2[$ligne[$loop]])) {
					$tab=explode(";", $ligne_tempo2[$ligne[$loop]]);

					$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".mysql_real_escape_string($tab[0])."';";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)==0) {

						$sql="SELECT * FROM utilisateurs WHERE nom='".mysql_real_escape_string($tab[1])."' AND prenom='".mysql_real_escape_string($tab[2])."' AND statut!='eleve' AND statut!='responsable';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==1) {
							// Un seul élève correspond
							$lig=mysql_fetch_object($res);

							$sql="SELECT 1=1 FROM sso_table_correspondance WHERE login_gepi='$lig->login';";
							$test=mysql_query($sql);
							if(mysql_num_rows($test)==0) {
								$sql="INSERT INTO sso_table_correspondance SET login_gepi='$lig->login', login_sso='".mysql_real_escape_string($tab[0])."';";
								$insert=mysql_query($sql);
								if(!$insert) {
									$msg.="Erreur lors de l'insertion de l'association ".$tab[0]." &gt; ".$lig->login."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
							else {
								$sql="UPDATE sso_table_correspondance SET login_sso='".mysql_real_escape_string($tab[0])."' WHERE login_gepi='$lig->login';";
								$update=mysql_query($sql);
								if(!$update) {
									$msg.="Erreur lors de la mise à jour de l'association ".$tab[0]." &gt; ".$lig->login."<br />\n";
								}
								else {
									$nb_reg++;
								}
							}
						}
						else {
							// On ne doit pas arriver là
							$msg.="Plusieurs enregistrements dans la table 'utilisateurs' pour ".$tab[1]." ".$tab[2]." !<br />\n";
						}
					}
					else {
						$lig=mysql_fetch_object($test);
						$msg.="Le GUID $tab[0] est déjà associé à $lig->login_gepi<br />\n";
					}
				}
				else {
					$msg.="Aucun enregistrement pour la ligne $loop<br />";
				}
			}
		}
	}

	if($nb_reg>0) {
		$msg.="$nb_reg enregistrement(s) effectué(s).<br />\n";
	}

	$mode="consult_personnels";
}

if(isset($_POST['enregistrement_saisie_manuelle'])) {
	check_token();

	$login_gepi=isset($_POST["login_gepi"]) ? $_POST["login_gepi"] : NULL;
	$login_sso=isset($_POST["login_sso"]) ? $_POST["login_sso"] : NULL;

	$nb_reg=0;
	if((!isset($login_gepi))||(!isset($login_gepi))||($login_sso=='')||($login_gepi=='')) {
		$msg.="Un des champs login_gepi ou guid n'a pas été transmis.<br />\n";
	}
	else {
		$sql="SELECT * FROM sso_table_correspondance WHERE login_sso='".$login_sso."';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$sql="SELECT statut FROM utilisateurs WHERE login='$login_gepi';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==1) {
				$statut_compte=mysql_result($res, 0, "statut");

				$sql="SELECT 1=1 FROM sso_table_correspondance WHERE login_gepi='$login_gepi';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0) {
					$sql="INSERT INTO sso_table_correspondance SET login_gepi='$login_gepi', login_sso='$login_sso';";
					$insert=mysql_query($sql);
					if(!$insert) {
						$msg.="Erreur lors de l'insertion de l'association ".$login_sso." &gt; ".$login_gepi."<br />\n";
					}
					else {
						$nb_reg++;
					}
				}
				else {
					$sql="UPDATE sso_table_correspondance SET login_sso='".$login_sso."' WHERE login_gepi='$login_gepi';";
					$update=mysql_query($sql);
					if(!$update) {
						$msg.="Erreur lors de la mise à jour de l'association ".$login_sso." &gt; ".$login_gepi."<br />\n";
					}
					else {
						$nb_reg++;
					}
				}
			}
			elseif(mysql_num_rows($res)==0) {
				$msg.="Aucun enregistrement dans la table 'eleves' pour $login_gepi<br />\n";
			}
			else {
				$msg.="Plusieurs enregistrements dans la table 'eleves' pour $login_gepi<br />\n";
			}
		}
		else {
			$lig=mysql_fetch_object($test);
			$msg.="Le GUID $login_sso est déjà associé à $lig->login_gepi<br />\n";
		}
	}

	if($nb_reg>0) {
		$msg.="$nb_reg enregistrement effectué.<br />\n";

		if($statut_compte=='eleve') {
			$mode="consult_eleves";
		}
		elseif($statut_compte=='responsable') {
			$mode="consult_eleves";
		}
		else {
			$mode="consult_personnels";
		}
	}
}

if($mode=='vider') {
	check_token();

	$sql="TRUNCATE sso_table_correspondance;";
	$menage=mysql_query($sql);
	if($menage) {
		$msg.="Table sso_table_correspondance vidée.<br />";
	}
	else {
		$msg.="Erreur lors du 'vidage' de sso_table_correspondance.<br />";
	}
}

//**************** EN-TETE *****************
$titre_page = "ENT ITOP : Rapprochement";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//echo "<p style='color:red'>REVOIR la page lien de Retour. Il faudrait que mod_ent/index.php soit une page commune pour pointer vers les divers rappochements selon les ENT (enregistrer un paramètre quelque part pour ne pas changer accidentellement de type d'ENT).</p>";

echo "<p class=bold>
<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";


if(!isset($mode)) {
	echo "
</p>

<h2>Rapprochement des comptes ENT ITOP/GEPI</h2>

<p><a href='".$_SERVER['PHP_SELF']."?mode=saisie_manuelle'>Saisir manuellement une association</a></p>
<p>Ou importer un CSV&nbsp;:</p>
<ul>
	<li><a href='".$_SERVER['PHP_SELF']."?mode=import_eleves'>Importer un CSV élèves</a></li>
	<li><a href='".$_SERVER['PHP_SELF']."?mode=import_responsables'>Importer un CSV responsables</a></li>
	<li><a href='".$_SERVER['PHP_SELF']."?mode=import_personnels'>Importer un CSV personnels de l'établissement</a> <span style='color:red'>A TESTER</span></li>
</ul>
<p> ou consulter&nbsp;:</p>
<ul>";



	$sql="SELECT e.*, s.* FROM eleves e, sso_table_correspondance s WHERE s.login_gepi=e.login ORDER BY e.nom, e.prenom LIMIT 1;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "
	<li>Aucune association élève n'est encore enregistrée.</li>";
	}
	else {
		echo "
	<li><a href='".$_SERVER['PHP_SELF']."?mode=consult_eleves'>Consulter les associations élèves</a></li>";
	}

	$sql="SELECT rp.*, s.* FROM resp_pers rp, sso_table_correspondance s WHERE s.login_gepi=rp.login ORDER BY rp.nom, rp.prenom LIMIT 1;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "
	<li>Aucune association responsable n'est encore enregistrée.</li>";
	}
	else {
		echo "
	<li><a href='".$_SERVER['PHP_SELF']."?mode=consult_responsables'>Consulter les associations responsables</a></li>";
	}

	$sql="SELECT u.*, s.* FROM utilisateurs u, sso_table_correspondance s WHERE s.login_gepi=u.login AND u.statut!='eleve' AND u.statut!='responsable' LIMIT 1;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "
	<li>Aucune association n'est encore enregistrée pour les personnels de l'établissement.</li>";
	}
	else {
		echo "
	<li><a href='".$_SERVER['PHP_SELF']."?mode=consult_personnels'>Consulter les associations personnels</a> <span style='color:red'>A TESTER</span></li>";
	}

	echo "
</ul>
<p>ou <a href='".$_SERVER['PHP_SELF']."?mode=vider".add_token_in_url()."' onclick=\"return confirmlink(this, 'ATTENTION !!! Êtes-vous vraiment sûr de vouloir vider la table sso_table_correspondance ?', 'Confirmation du vidage')\">vider la table des correspondances</a></p>

<br />
<p style='color:red'>A FAIRE : Détecter et permettre de supprimer des associations pour des élèves,... qui ne sont plus dans le CSV.</p>
\n";
	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="saisie_manuelle") {
		echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
</p>

<h2>Rapprochement des comptes ENT ITOP/GEPI</h2>

<p>Saisir manuellement une association Login_gepi / Guid&nbsp;:</p>
<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	".add_token_field()."
	<input type='hidden' name='mode' value='saisie_manuelle' />
	<input type='hidden' name='enregistrement_saisie_manuelle' value='y' />
	Login Gepi&nbsp;: <input type=\"text\" name=\"login_gepi\" value=\"\" /><br />
	Guid&nbsp;: <input type=\"text\" name=\"login_sso\" value=\"\" /><br />
	<input type='submit' value='Valider' />
</form>

<p style='color:red'>A FAIRE : Ajouter un dispositf de recherche de login contenant telle chaine,...</p>
";
	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="import_eleves") {
	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	if(!isset($csv_file)) {
		echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
</p>

<h2>Rapprochement des comptes ENT ITOP/GEPI</h2>

<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	".add_token_field()."
	<input type='hidden' name='mode' value='import_eleves' />
	<input type=\"file\" size=\"65\" name=\"csv_file\" /><br />
	<input type='submit' value='Envoyer' />
</form>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Le fichier CSV attendu doit avoir le format suivant&nbsp;:<br />
Guid;Nom;Prénom;Profil;Classes;Groupe;Naissance<br />
f73d0f72-0958-4b8f-85f7-a58a96d95220;DISSOIR;Alain;National_1;0310000Z$1L1;16/06/1987<br />
...
</p>\n";
	}
	else {
		check_token(false);
		$fp=fopen($csv_file['tmp_name'],"r");

		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$sql="TRUNCATE tempo2;";
		$menage=mysql_query($sql);

		echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
</p>

<h2>Rapprochement des comptes élèves ENT ITOP/GEPI</h2>

<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
".add_token_field()."
<input type='hidden' name='mode' value='import_eleves' />
<input type='hidden' name='enregistrement_eleves' value='y' />

<table class='boireaus'>
	<tr>
		<th rowspan='2'>
			Enregistrer
			<span id='tout_cocher_decocher' style='display:none;'>
				<br />
				<a href=\"javascript:tout_cocher()\" title='Tout cocher'><img src='../images/enabled.png' width='20' height='20' /></a>
				/
				<a href=\"javascript:tout_decocher()\" title='Tout décocher'><img src='../images/disabled.png' width='20' height='20' /></a>
			</span>
		</th>
		<th colspan='6'>Informations ENT</th>
		<th rowspan='2'>Login Gepi</th>
	</tr>
	<tr>
		<th>Guid</th>
		<th>Nom</th>
		<th>Prénom</th>
		<th>Profil</th>
		<th>Classe</th>
		<th>Naissance</th>
	</tr>
";
		$alt=1;
		$cpt=0;
		$cpt_deja_enregistres=0;
		while (!feof($fp)) {
			$ligne = trim(fgets($fp, 4096));
			if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
				$ligne=substr($ligne,3);
			}

			if($ligne!='') {
				$tab=explode(";", ensure_utf8($ligne));
				if(!preg_match("/^Guid;/i", trim($ligne))) {

					// Si un élève a déjà une association enregistrée, ne pas proposer de refaire l'association.
					// Juste stocker ce qui est déjà associé et l'afficher dans un 2è tableau.
					// Pouvoir supprimer une association du 2è tableau (ou voir dans mode=consult_eleves)

					$sql="SELECT login_gepi FROM sso_table_correspondance WHERE login_sso='".mysql_real_escape_string($tab[0])."';";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0) {
						$cpt_deja_enregistres++;
					}
					else {
						$naissance=(isset($tab[5])) ? $tab[5] : "";
						if(!preg_match("#[0-9]{2}/[0-9]{2}/[0-9]{4}#", $naissance)) {$naissance="";}

						$alt=$alt*(-1);
						echo "
	<tr class='lig$alt white_hover'>
		<td><input type='checkbox' name='ligne[]' id='ligne_$cpt' value='$cpt' /></td>
		<td><label for='ligne_$cpt'>".$tab[0]."</label></td>
		<td><label for='ligne_$cpt'>".$tab[1]."</label></td>
		<td><label for='ligne_$cpt'>".$tab[2]."</label></td>
		<td><label for='ligne_$cpt'>".$tab[3]."</label></td>
		<td><label for='ligne_$cpt'>".preg_replace("/".getSettingValue('gepiSchoolRne')."\\$/", "", $tab[4])."</label></td>
		<td><label for='ligne_$cpt'>".$naissance."</label></td>";

						if($naissance!="") {
							$sql="SELECT * FROM eleves WHERE nom='".mysql_real_escape_string($tab[1])."' AND prenom='".mysql_real_escape_string($tab[2])."' AND naissance='".formate_date2($naissance, "jj/mm/aaaa", "aaaammjj")."'";
						}
						else {
							$sql="SELECT * FROM eleves WHERE nom='".mysql_real_escape_string($tab[1])."' AND prenom='".mysql_real_escape_string($tab[2])."' ORDER BY naissance;";
						}
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==1) {
							// Un seul élève correspond
							$lig=mysql_fetch_object($res);

							echo "
		<td title=\"$lig->nom $lig->prenom ".formate_date($lig->naissance)."\">
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>
			$lig->login";

							// On vérifie quand même la classe?
							$tab_classe=get_class_from_ele_login($lig->login);
							if(isset($tab_classe['liste_nbsp'])) {
								echo " (<em>".$tab_classe['liste_nbsp']."</em>)";
							}
							else {
								echo " (<em style='color:red'>Aucune classe???</em>)";
							}

							echo "
			<span id='saisie_$cpt'></span>
		</td>
	</tr>
";
						}
						else {
							// Plusieurs élèves correspondent
							// Il va falloir choisir
							$chaine_options="";
							while($lig=mysql_fetch_object($res)) {
								$chaine_options.="				<option value=\"$lig->login\">$lig->nom $lig->prenom (".formate_date($lig->naissance).")";
								$tab_classe=get_class_from_ele_login($lig->login);
								if(isset($tab_classe['liste'])) {
									$chaine_options.=" en ".$tab_classe['liste'];
								}
								$chaine_options.="</option>\n";
							}

							if($chaine_options!="") {
								echo "
		<td>
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>

			<span id='saisie_$cpt'>
			<select name='login_$cpt' id='login_$cpt' onchange=\"if(document.getElementById('login_$cpt').options[if(document.getElementById('login_$cpt').selectedIndex].value!='') {document.getElementById('ligne_$cpt').checked=true;} else{document.getElementById('ligne_$cpt').checked=false;}\">
				<option value=''>---</option>
				$chaine_options
			</select>
			</span>
		</td>
	</tr>
";
							}
							else {
								echo "
		<td>
			<input type='text' name='login_$cpt' id='login_$cpt' value=\"\" onchange=\"if(document.getElementById('login_$cpt').value!='') {document.getElementById('ligne_$cpt').checked=true;} else{document.getElementById('ligne_$cpt').checked=false;}\" />
		</td>
	</tr>
";
								// A FAIRE: Mettre un dispositif de recherche comme dans mod_annees_anterieures
							}
						}

						$sql="INSERT INTO tempo2 SET col1='$cpt', col2='".mysql_real_escape_string($ligne)."';";
						$insert=mysql_query($sql);
						if(!$insert) {
							echo "<span style='color:red'>ERREUR</span>";
						}
						$cpt++;
					}
				}
			}
		}

		echo "
</table>
<input type='submit' value='Enregistrer' />
</form>
";
		if($cpt_deja_enregistres>0) {
			echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=consult_eleves'>$cpt_deja_enregistres association(s) élève(s) déjà enregistrée(s)</a>.<br />\n";
		}
		else {
			echo "<p>Aucune association élève n'est encore enregistrée.<br />\n";
		}

		echo "
<p style='color:red'>A FAIRE:<br />
Pouvoir trier par classe<br />
Ajouter une variable en fin de formulaire pour détecter les pb de transmission de trop de variables avec suhosin.</p>

<script type='text/javascript'>
	document.getElementById('tout_cocher_decocher').style.display='';

	function tout_cocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				document.getElementById('ligne_'+i).checked=true;
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				document.getElementById('ligne_'+i).checked=false;
			}
		}
	}

	function ajout_champ_saisie_login(num) {
		if(document.getElementById('saisie_'+num)) {
			document.getElementById('saisie_'+num).innerHTML='<input type=\"text\" name=\"login_'+num+'\" id=\"login_'+num+'\" value=\"\" onchange=\"if(document.getElementById(\'login_'+num+'\').value!=\'\') {document.getElementById(\'ligne_'+num+'\').checked=true;} else {document.getElementById(\'ligne_'+num+'\').checked=false;}\" />';
		}
	}
</script>
";
		// En fin d'enregistrement, renvoyer vers consult_eleves pour afficher les associations
		// Compter/afficher ce total des associations avant... et après dans consult_eleves
	}
	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="consult_eleves") {
	echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
 | <a href='".$_SERVER['PHP_SELF']."?mode=import_eleves'>Importer un CSV élève</a>
</p>

<h2>Rapprochement actuels des comptes élèves ENT ITOP/GEPI</h2>
";

	$sql="SELECT e.*, s.* FROM eleves e, sso_table_correspondance s WHERE s.login_gepi=e.login ORDER BY e.nom, e.prenom";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>Aucun rapprochement élève n'est enregistré.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo "
<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<p>Les rapprochements enregistrés sont les suivants&nbsp;:</p>
	".add_token_field()."
	<input type='hidden' name='mode' value='consult_eleves' />

	<table class='boireaus'>
		<tr>
			<th>
				<input type='submit' value='Supprimer' />
				<span id='tout_cocher_decocher' style='display:none;'>
					<br />
					<a href=\"javascript:tout_cocher()\" title='Tout cocher'><img src='../images/enabled.png' width='20' height='20' /></a>
					/
					<a href=\"javascript:tout_decocher()\" title='Tout décocher'><img src='../images/disabled.png' width='20' height='20' /></a>
				</span>
			</th>
			<th>Guid</th>
			<th>Login</th>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Classe</th>
			<th>Naissance</th>
		</tr>
";

	$cpt=0;
	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$tab_classe=get_class_from_ele_login($lig->login);
		$classe=isset($tab_classe['liste_nbsp']) ? $tab_classe['liste_nbsp'] : "";

		$alt=$alt*(-1);
		echo "
		<tr class='lig$alt white_hover'>
			<td><input type='checkbox' name='suppr[]' id='suppr_$cpt' value=\"$lig->login\" /></td>
			<td><label for='suppr_$cpt'>$lig->login_sso</label></td>
			<td><label for='suppr_$cpt'>$lig->login</label></td>
			<td><label for='suppr_$cpt'>$lig->nom</label></td>
			<td><label for='suppr_$cpt'>$lig->prenom</label></td>
			<td><label for='suppr_$cpt'>$classe</label></td>
			<td><label for='suppr_$cpt'>".formate_date($lig->naissance)."</label></td>
		</tr>
";
		$cpt++;
	}

	echo "
	</table>

</form>


<script type='text/javascript'>
	document.getElementById('tout_cocher_decocher').style.display='';

	function tout_cocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=true;
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=false;
			}
		}
	}
</script>
";

	require("../lib/footer.inc.php");
	die();
}
//==================================================================================

if($mode=="import_responsables") {
	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	if(!isset($csv_file)) {
		echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
</p>

<h2>Rapprochement des comptes responsables ENT ITOP/GEPI</h2>

<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	".add_token_field()."
	<input type='hidden' name='mode' value='import_responsables' />
	<input type=\"file\" size=\"65\" name=\"csv_file\" /><br />
	<input type='submit' value='Envoyer' />
</form>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Le fichier CSV attendu doit avoir le format suivant&nbsp;:<br />
﻿Guid;Nom;Prénom;Profil;Classes;Groupe;Guid_Enfant1;Guid_Enfant2;Guid_Enfant3<br />
f7ebe441-14e0-4c48-b9ec-53e603829fb3;DISSOIR;Amar;National_2;;;f73d0f72-0958-4b8f-85f7-a58a96d95220<br />
...
</p>\n";
	}
	else {
		check_token(false);
		$fp=fopen($csv_file['tmp_name'],"r");

		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$sql="TRUNCATE tempo2;";
		$menage=mysql_query($sql);

		echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
</p>

<h2>Rapprochement des comptes responsables ENT ITOP/GEPI</h2>

<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
".add_token_field()."
<input type='hidden' name='mode' value='import_responsables' />
<input type='hidden' name='enregistrement_responsables' value='y' />

<table class='boireaus'>
	<tr>
		<th rowspan='2'>
			Enregistrer
			<span id='tout_cocher_decocher' style='display:none;'>
				<br />
				<a href=\"javascript:tout_cocher()\" title='Tout cocher'><img src='../images/enabled.png' width='20' height='20' /></a>
				/
				<a href=\"javascript:tout_decocher()\" title='Tout décocher'><img src='../images/disabled.png' width='20' height='20' /></a>
			</span>
		</th>
		<th colspan='6'>Informations ENT</th>
		<th rowspan='2'>Login Gepi</th>
	</tr>
	<tr>
		<th>Guid</th>
		<th>Nom</th>
		<th>Prénom</th>
		<th>Profil</th>
		<th>Groupe</th>
		<th>Enfants</th>
	</tr>
";
		$alt=1;
		$cpt=0;
		$cpt_deja_enregistres=0;
		while (!feof($fp)) {
			$ligne = trim(fgets($fp, 4096));
			if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
				$ligne=substr($ligne,3);
			}

			if($ligne!='') {
				$tab=explode(";", ensure_utf8($ligne));
				if(!preg_match("/^Guid;/i", trim($ligne))) {

					// Si un élève a déjà une association enregistrée, ne pas proposer de refaire l'association.
					// Juste stocker ce qui est déjà associé et l'afficher dans un 2è tableau.
					// Pouvoir supprimer une association du 2è tableau (ou voir dans mode=consult_eleves)

					$sql="SELECT login_gepi FROM sso_table_correspondance WHERE login_sso='".mysql_real_escape_string($tab[0])."';";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0) {
						$cpt_deja_enregistres++;
					}
					else {

						$alt=$alt*(-1);
						echo "
	<tr class='lig$alt white_hover'>
		<td><input type='checkbox' name='ligne[]' id='ligne_$cpt' value='$cpt' /></td>
		<td><label for='ligne_$cpt'>".$tab[0]."</label></td>
		<td><label for='ligne_$cpt'>".$tab[1]."</label></td>
		<td><label for='ligne_$cpt'>".$tab[2]."</label></td>
		<td><label for='ligne_$cpt'>".$tab[3]."</label></td>
		<td><label for='ligne_$cpt'>".(isset($tab[4])?$tab[4]:"")."</label></td>
		<td><label for='ligne_$cpt'>";

						$chaine="";
						if((isset($tab[5]))&&($tab[5]!="")) {
							$chaine.="s.login_sso='".mysql_real_escape_string($tab[5])."'";
						}
						if((isset($tab[6]))&&($tab[6]!="")) {
							if($chaine!="") {$chaine.=" OR ";}
							$chaine.="s.login_sso='".mysql_real_escape_string($tab[6])."'";
						}
						if((isset($tab[7]))&&($tab[7]!="")) {
							if($chaine!="") {$chaine.=" OR ";}
							$chaine.="s.login_sso='".mysql_real_escape_string($tab[7])."'";
						}

						$tab_resp=array();
						$tab_resp_login=array();
						$cpt_resp=0;
						// Si la chaine est vide, proposer un champ TEXT
						if($chaine!="") {
							$sql="SELECT e.* FROM eleves e, sso_table_correspondance s WHERE ($chaine) AND e.login=s.login_gepi ORDER BY e.nom, e.prenom;";
							//echo "$sql<br />";
							$res_ele=mysql_query($sql);
							if(mysql_num_rows($res_ele)>0) {
								$cpt_ele=0;
								while($lig_ele=mysql_fetch_object($res_ele)) {
									if($cpt_ele>0) {echo "<br />";}
									echo $lig_ele->nom." ".$lig_ele->prenom;
									$tab_classe=get_class_from_ele_login($lig_ele->login);
									if(isset($tab_classe['liste_nbsp'])) {echo " (<em>".$tab_classe['liste_nbsp']."</em>)";}

									$sql="SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r WHERE r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' AND rp.nom='".mysql_real_escape_string($tab[1])."' AND rp.prenom='".mysql_real_escape_string($tab[2])."' AND rp.login!='';";
									//echo "$sql<br />";
									$res_resp=mysql_query($sql);
									if(mysql_num_rows($res_resp)>0) {
										while($lig_resp=mysql_fetch_object($res_resp)) {
											if(!in_array($lig_resp->login, $tab_resp_login)) {
												$tab_resp_login[]=$lig_resp->login;
												$tab_resp[$cpt_resp]['login']=$lig_resp->login;
												$tab_resp[$cpt_resp]['civilite']=$lig_resp->civilite;
												$tab_resp[$cpt_resp]['nom']=$lig_resp->nom;
												$tab_resp[$cpt_resp]['prenom']=$lig_resp->prenom;
												$tab_resp[$cpt_resp]['resp_legal']=$lig_resp->resp_legal;
												$tab_resp[$cpt_resp]['info']=$lig_resp->civilite." ".casse_mot($lig_resp->nom,'maj')." ".casse_mot($lig_resp->prenom,'majf2');
												$tab_resp[$cpt_resp]['info'].=" (N° ".$lig_resp->pers_id.")";

												$tab_resp[$cpt_resp]['info'].=" (Légal ".$lig_resp->resp_legal.")";
												$cpt_resp++;
											}
										}
									}
									else {
										$sql="SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r WHERE r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' AND rp.login!='';";
										$res_resp=mysql_query($sql);
										if(mysql_num_rows($res_resp)>0) {
											while($lig_resp=mysql_fetch_object($res_resp)) {
												if(!in_array($lig_resp->login, $tab_resp_login)) {
													$tab_resp_login[]=$lig_resp->login;
													$tab_resp[$cpt_resp]['login']=$lig_resp->login;
													$tab_resp[$cpt_resp]['civilite']=$lig_resp->civilite;
													$tab_resp[$cpt_resp]['nom']=$lig_resp->nom;
													$tab_resp[$cpt_resp]['prenom']=$lig_resp->prenom;
													$tab_resp[$cpt_resp]['resp_legal']=$lig_resp->resp_legal;
													$tab_resp[$cpt_resp]['info']=$lig_resp->civilite." ".casse_mot($lig_resp->nom,'maj')." ".casse_mot($lig_resp->prenom,'majf2');
													$tab_resp[$cpt_resp]['info'].=" (N° ".$lig_resp->pers_id.")";
													$tab_resp[$cpt_resp]['info'].=" (Légal ".$lig_resp->resp_legal.")";
													$cpt_resp++;
												}
											}
										}
									}
									$cpt_ele++;
								}
							}
						}
						echo "</label></td>\n";

						if(count($tab_resp)==0) {
							echo "
		<td>
			<input type='text' name='login_$cpt' id='login_$cpt' value=\"\" onchange=\"if(document.getElementById('login_$cpt').value!='') {document.getElementById('ligne_$cpt').checked=true;} else{document.getElementById('ligne_$cpt').checked=false;}\" />
		</td>
	</tr>
";
						}
						elseif(count($tab_resp)==1) {
							echo "
		<td title=\"".$tab_resp[0]['info']."\">
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>
			".$tab_resp[0]['login']."
			<span id='saisie_$cpt'></span>
		</td>
	</tr>
";
						}
						else {
							$chaine_options="";
							for($loop=0;$loop<count($tab_resp);$loop++) {
								$chaine_options.="				<option value=\"".$tab_resp[$loop]['login']."\">".$tab_resp[$loop]['info']."</option>\n";
							}
							echo "
		<td>
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>

			<span id='saisie_$cpt'>
			<select name='login_$cpt' id='login_$cpt' onchange=\"if(document.getElementById('login_$cpt').options[if(document.getElementById('login_$cpt').selectedIndex].value!='') {document.getElementById('ligne_$cpt').checked=true;} else{document.getElementById('ligne_$cpt').checked=false;}\">
				<option value=''>---</option>
				$chaine_options
			</select>
			</span>
		</td>
	</tr>
";
						}

						$sql="INSERT INTO tempo2 SET col1='$cpt', col2='".mysql_real_escape_string($ligne)."';";
						$insert=mysql_query($sql);
						if(!$insert) {
							echo "<span style='color:red'>ERREUR</span>";
						}
						$cpt++;
					}
				}
			}
		}

		echo "
</table>
<input type='submit' value='Enregistrer' />
</form>
";
		if($cpt_deja_enregistres>0) {
			echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=consult_responsables'>$cpt_deja_enregistres association(s) responsable(s) déjà enregistrée(s)</a>.<br />\n";
		}
		else {
			echo "<p>Aucune association responsable n'est encore enregistrée.<br />\n";
		}

		echo "
<p style='color:red'>A FAIRE:<br />
Pouvoir trier par classe<br />
Ajouter une variable en fin de formulaire pour détecter les pb de transmission de trop de variables avec suhosin.</p>

<script type='text/javascript'>
	document.getElementById('tout_cocher_decocher').style.display='';

	function tout_cocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				document.getElementById('ligne_'+i).checked=true;
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				document.getElementById('ligne_'+i).checked=false;
			}
		}
	}

	function ajout_champ_saisie_login(num) {
		if(document.getElementById('saisie_'+num)) {
			document.getElementById('saisie_'+num).innerHTML='<input type=\"text\" name=\"login_'+num+'\" id=\"login_'+num+'\" value=\"\" onchange=\"if(document.getElementById(\'login_'+num+'\').value!=\'\') {document.getElementById(\'ligne_'+num+'\').checked=true;} else {document.getElementById(\'ligne_'+num+'\').checked=false;}\" />';
		}
	}
</script>
";
		// En fin d'enregistrement, renvoyer vers consult_eleves pour afficher les associations
		// Compter/afficher ce total des associations avant... et après dans consult_eleves
	}
	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="consult_responsables") {
	echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
 | <a href='".$_SERVER['PHP_SELF']."?mode=import_responsables'>Importer un CSV élève</a>
</p>

<h2>Rapprochement actuels des comptes responsables ENT ITOP/GEPI</h2>
";

	$sql="SELECT rp.*, s.* FROM resp_pers rp, sso_table_correspondance s WHERE s.login_gepi=rp.login ORDER BY rp.nom, rp.prenom";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>Aucun rapprochement élève n'est enregistré.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo "
<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<p>Les rapprochements enregistrés sont les suivants&nbsp;:</p>
	".add_token_field()."
	<input type='hidden' name='mode' value='consult_responsables' />

	<table class='boireaus'>
		<tr>
			<th>
				<input type='submit' value='Supprimer' />
				<span id='tout_cocher_decocher' style='display:none;'>
					<br />
					<a href=\"javascript:tout_cocher()\" title='Tout cocher'><img src='../images/enabled.png' width='20' height='20' /></a>
					/
					<a href=\"javascript:tout_decocher()\" title='Tout décocher'><img src='../images/disabled.png' width='20' height='20' /></a>
				</span>
			</th>
			<th>Guid</th>
			<th>Login</th>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Responsable de</th>
		</tr>
";

	$cpt=0;
	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$tab_classe=get_class_from_ele_login($lig->login);
		$classe=isset($tab_classe['liste_nbsp']) ? $tab_classe['liste_nbsp'] : "";

		$alt=$alt*(-1);
		echo "
		<tr class='lig$alt white_hover'>
			<td><input type='checkbox' name='suppr[]' id='suppr_$cpt' value=\"$lig->login\" /></td>
			<td><label for='suppr_$cpt'>$lig->login_sso</label></td>
			<td><label for='suppr_$cpt'>$lig->login</label></td>
			<td><label for='suppr_$cpt'>$lig->nom</label></td>
			<td><label for='suppr_$cpt'>$lig->prenom</label></td>
			<td><label for='suppr_$cpt'></label></td>
		</tr>
";
		$cpt++;
	}

	echo "
	</table>

</form>


<script type='text/javascript'>
	document.getElementById('tout_cocher_decocher').style.display='';

	function tout_cocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=true;
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=false;
			}
		}
	}
</script>
";

	require("../lib/footer.inc.php");
	die();
}
//==================================================================================

if($mode=="import_personnels") {
	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	if(!isset($csv_file)) {
		echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
</p>

<h2>Rapprochement des comptes ENT ITOP/GEPI</h2>

<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	".add_token_field()."
	<input type='hidden' name='mode' value='import_personnels' />
	<input type=\"file\" size=\"65\" name=\"csv_file\" /><br />
	<input type='submit' value='Envoyer' />
</form>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Le fichier CSV attendu doit avoir le format suivant&nbsp;:<br />
Guid;Nom;Prénom;Profil;Classes;Groupe<br />
f73d0f72-0958-4b8f-85f7-a58a96d95220;BACQUET;Michel;National_3;;<br />
...
</p>\n";
	}
	else {
		check_token(false);
		$fp=fopen($csv_file['tmp_name'],"r");

		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$sql="TRUNCATE tempo2;";
		$menage=mysql_query($sql);

		echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
</p>

<h2>Rapprochement des comptes de personnels ENT ITOP/GEPI</h2>

<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
".add_token_field()."
<input type='hidden' name='mode' value='import_personnels' />
<input type='hidden' name='enregistrement_personnels' value='y' />

<table class='boireaus'>
	<tr>
		<th rowspan='2'>
			Enregistrer
			<span id='tout_cocher_decocher' style='display:none;'>
				<br />
				<a href=\"javascript:tout_cocher()\" title='Tout cocher'><img src='../images/enabled.png' width='20' height='20' /></a>
				/
				<a href=\"javascript:tout_decocher()\" title='Tout décocher'><img src='../images/disabled.png' width='20' height='20' /></a>
			</span>
		</th>
		<th colspan='4'>Informations ENT</th>
		<th rowspan='2'>Login Gepi</th>
	</tr>
	<tr>
		<th>Guid</th>
		<th>Nom</th>
		<th>Prénom</th>
		<th>Profil</th>
	</tr>
";
		$alt=1;
		$cpt=0;
		$cpt_deja_enregistres=0;
		while (!feof($fp)) {
			$ligne = trim(fgets($fp, 4096));
			if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
				$ligne=substr($ligne,3);
			}

			if($ligne!='') {
				$tab=explode(";", ensure_utf8($ligne));
				if(!preg_match("/^Guid;/i", trim($ligne))) {

					$sql="SELECT login_gepi FROM sso_table_correspondance WHERE login_sso='".mysql_real_escape_string($tab[0])."';";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0) {
						$cpt_deja_enregistres++;
					}
					else {
						$alt=$alt*(-1);
						echo "
	<tr class='lig$alt white_hover'>
		<td><input type='checkbox' name='ligne[]' id='ligne_$cpt' value='$cpt' /></td>
		<td><label for='ligne_$cpt'>".$tab[0]."</label></td>
		<td><label for='ligne_$cpt'>".$tab[1]."</label></td>
		<td><label for='ligne_$cpt'>".$tab[2]."</label></td>
		<td><label for='ligne_$cpt'>".$tab[3]."</label></td>";

						$sql="SELECT * FROM utilisateurs WHERE nom='".mysql_real_escape_string($tab[1])."' AND prenom='".mysql_real_escape_string($tab[2])."' AND statut!='eleve' AND statut!='responsable' ORDER BY statut;";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==1) {
							// Un seul personnel correspond
							$lig=mysql_fetch_object($res);

							echo "
		<td title=\"$lig->nom $lig->prenom ($lig->statut)\">
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>
			$lig->login
			<span id='saisie_$cpt'></span>
		</td>
	</tr>
";
						}
						else {
							// Plusieurs personnels correspondent
							// Il va falloir choisir
							$chaine_options="";
							while($lig=mysql_fetch_object($res)) {
								$chaine_options.="				<option value=\"$lig->login\">$lig->nom $lig->prenom (".$lig->statut.")";
								$chaine_options.="</option>\n";
							}

							if($chaine_options!="") {
								echo "
		<td>
			<div style='float:right; width:16px'><a href='javascript:ajout_champ_saisie_login($cpt)' title='Ajouter un champ de saisie de login'><img src='../images/icons/wizard.png' /></a></div>

			<span id='saisie_$cpt'>
			<select name='login_$cpt' id='login_$cpt' onchange=\"if(document.getElementById('login_$cpt').options[if(document.getElementById('login_$cpt').selectedIndex].value!='') {document.getElementById('ligne_$cpt').checked=true;} else{document.getElementById('ligne_$cpt').checked=false;}\">
				<option value=''>---</option>
				$chaine_options
			</select>
			</span>
		</td>
	</tr>
";
							}
							else {
								echo "
		<td>
			<input type='text' name='login_$cpt' id='login_$cpt' value=\"\" onchange=\"if(document.getElementById('login_$cpt').value!='') {document.getElementById('ligne_$cpt').checked=true;} else{document.getElementById('ligne_$cpt').checked=false;}\" />
		</td>
	</tr>
";
								// A FAIRE: Mettre un dispositif de recherche comme dans mod_annees_anterieures
							}
						}

						$sql="INSERT INTO tempo2 SET col1='$cpt', col2='".mysql_real_escape_string($ligne)."';";
						$insert=mysql_query($sql);
						if(!$insert) {
							echo "<span style='color:red'>ERREUR</span>";
						}
						$cpt++;
					}
				}
			}
		}

		echo "
</table>
<input type='submit' value='Enregistrer' />
</form>
";
		if($cpt_deja_enregistres>0) {
			echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=consult_eleves'>$cpt_deja_enregistres association(s) personnel(s) déjà enregistrée(s)</a>.<br />\n";
		}
		else {
			echo "<p>Aucune association de compte personnel n'est encore enregistrée.<br />\n";
		}

		echo "
<p style='color:red'>A FAIRE:<br />
Ajouter une variable en fin de formulaire pour détecter les pb de transmission de trop de variables avec suhosin.</p>

<script type='text/javascript'>
	document.getElementById('tout_cocher_decocher').style.display='';

	function tout_cocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				document.getElementById('ligne_'+i).checked=true;
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('ligne_'+i)) {
				document.getElementById('ligne_'+i).checked=false;
			}
		}
	}

	function ajout_champ_saisie_login(num) {
		if(document.getElementById('saisie_'+num)) {
			document.getElementById('saisie_'+num).innerHTML='<input type=\"text\" name=\"login_'+num+'\" id=\"login_'+num+'\" value=\"\" onchange=\"if(document.getElementById(\'login_'+num+'\').value!=\'\') {document.getElementById(\'ligne_'+num+'\').checked=true;} else {document.getElementById(\'ligne_'+num+'\').checked=false;}\" />';
		}
	}
</script>
";
		// En fin d'enregistrement, renvoyer vers consult_eleves pour afficher les associations
		// Compter/afficher ce total des associations avant... et après dans consult_eleves
	}
	require("../lib/footer.inc.php");
	die();
}

//==================================================================================
if($mode=="consult_personnels") {
	echo "
 | <a href='".$_SERVER['PHP_SELF']."'>Index rapprochement ENT ITOP</a>
 | <a href='".$_SERVER['PHP_SELF']."?mode=import_personnels'>Importer un CSV élève</a>
</p>

<h2>Rapprochement actuels des comptes de personnels ENT ITOP/GEPI</h2>
";

	$sql="SELECT u.*, s.* FROM utilisateurs u, sso_table_correspondance s WHERE s.login_gepi=u.login AND u.statut!='eleve' AND u.statut!='responsable' ORDER BY u.nom, u.prenom";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>Aucun rapprochement de personnel n'est enregistré.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo "
<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
	<p>Les rapprochements enregistrés sont les suivants&nbsp;:</p>
	".add_token_field()."
	<input type='hidden' name='mode' value='consult_personnels' />

	<table class='boireaus'>
		<tr>
			<th>
				<input type='submit' value='Supprimer' />
				<span id='tout_cocher_decocher' style='display:none;'>
					<br />
					<a href=\"javascript:tout_cocher()\" title='Tout cocher'><img src='../images/enabled.png' width='20' height='20' /></a>
					/
					<a href=\"javascript:tout_decocher()\" title='Tout décocher'><img src='../images/disabled.png' width='20' height='20' /></a>
				</span>
			</th>
			<th>Guid</th>
			<th>Login</th>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Statut</th>
		</tr>
";

	$cpt=0;
	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "
		<tr class='lig$alt white_hover'>
			<td><input type='checkbox' name='suppr[]' id='suppr_$cpt' value=\"$lig->login\" /></td>
			<td><label for='suppr_$cpt'>$lig->login_sso</label></td>
			<td><label for='suppr_$cpt'>$lig->login</label></td>
			<td><label for='suppr_$cpt'>$lig->nom</label></td>
			<td><label for='suppr_$cpt'>$lig->prenom</label></td>
			<td><label for='suppr_$cpt'>$lig->statut</label></td>
		</tr>
";
		$cpt++;
	}

	echo "
	</table>

</form>


<script type='text/javascript'>
	document.getElementById('tout_cocher_decocher').style.display='';

	function tout_cocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=true;
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=false;
			}
		}
	}
</script>
";

	require("../lib/footer.inc.php");
	die();
}
//==================================================================================

echo "<p style='color:red'>Mode non encore développé</p>\n";
echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
