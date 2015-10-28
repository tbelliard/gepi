<?php

/*
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

@set_time_limit(0);

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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

//INSERT INTO droits SET id='/lib/ajax_corriger_app.php',administrateur='F',professeur='V',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='V',description='Correction appreciation',statut='';
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

check_token();

header('Content-Type: text/html; charset=utf-8');

//debug_var();

$corriger_app_login_eleve=isset($_POST['corriger_app_login_eleve']) ? $_POST['corriger_app_login_eleve'] : "";
$corriger_app_id_groupe=isset($_POST['corriger_app_id_groupe']) ? $_POST['corriger_app_id_groupe'] : "";
$corriger_app_id_classe=isset($_POST['corriger_app_id_classe']) ? $_POST['corriger_app_id_classe'] : "";
$corriger_app_num_periode=isset($_POST['corriger_app_num_periode']) ? $_POST['corriger_app_num_periode'] : "";

$app=isset($NON_PROTECT['app']) ? traitement_magic_quotes($NON_PROTECT['app']) : "";

/*
$f=fopen("/tmp/debug_mail_corriger_app_faute.txt","a+");
fwrite($f,"========================================"."\n");
fwrite($f,"++++++++++++++++++++++++++++++++++++++++"."\n");
fwrite($f,"========================================"."\n");
fwrite($f,strftime("%Y%m%d à %H%M%S")."\n");
fwrite($f,$corriger_app_message."\n");

fwrite($f,"========================================"."\n");
fwrite($f,strftime("%Y%m%d à %H%M%S")."\n");
fwrite($f,$corriger_app_message."\n");
*/

//$app=preg_replace("/\\\\n/","\n",$app);
//$app=stripslashes($app);

/*
fwrite($f,"========================================"."\n");
fwrite($f,$corriger_app_message."\n");
fwrite($f,"========================================"."\n");
fclose($f);
*/

//if(!is_numeric($corriger_app_id_groupe)) {
//}

if(($corriger_app_login_eleve=='')||($corriger_app_id_groupe=='')||(!is_numeric($corriger_app_id_groupe))||($corriger_app_id_groupe==0)||($app=='')) {
	echo "<span style='color:red'> KO</span>";
	return false;
	die();
}

// Témoin destiné à repérer s'il s'agit d'une correction pour autrui... auquel cas on envoie un mail
$envoi_mail_correction_autrui="n";

$pp_avec_droit_modif="n";

if($_SESSION['statut']=='professeur') {
	$poursuivre="n";
	if(check_prof_groupe($_SESSION['login'],$corriger_app_id_groupe)) {
		$envoi_mail_correction_autrui="n";

		if($current_group["classe"]["ver_periode"][$corriger_app_id_classe][$corriger_app_num_periode]=="P") {
			// On vérifie quand même si le prof est PP avec droit de correction
			if((getSettingAOui('PeutAutoriserPPaCorrigerSesApp'))&&(acces_correction_app_pp($corriger_app_id_groupe))) {
				$pp_avec_droit_modif="y";
			}
		}
		$poursuivre="y";
	}
	else {
		$envoi_mail_correction_autrui="y";

		if($current_group["classe"]["ver_periode"][$corriger_app_id_classe][$corriger_app_num_periode]!="O") {
			if((getSettingAOui('PeutAutoriserPPaCorrigerSesApp'))&&(acces_correction_app_pp($corriger_app_id_groupe))) {
				$pp_avec_droit_modif="y";
				$poursuivre="y";
			}
		}
	}

	if($poursuivre!="y") {
		echo "<span style='color:red'> KO</span>";
		return false;
		die();
	}
}
elseif (($_SESSION['statut']=='scolarite')&&(!getSettingAOui('AccesModifAppreciationScol'))) {
	echo "<span style='color:red'> KO</span>";
	return false;
	die();
}
elseif ($_SESSION['statut']=='scolarite') {
	$envoi_mail_correction_autrui="y";
}
/*
else {
	// On devrait même déconnecter et mettre une tentative_intrusion()
	echo "<span style='color:red'> KO</span>";
	return false;
	die();
}
*/

$prefixe_debug=strftime("%Y%m%d %H%M%S")." : ".$_SESSION['login'];

$current_group=get_group($corriger_app_id_groupe);

// La période est-elle ouverte?
if (in_array($corriger_app_login_eleve, $current_group["eleves"][$corriger_app_num_periode]["list"])) {
	$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$corriger_app_num_periode]["users"][$corriger_app_login_eleve]["classe"]]["id"];

	$valider_modif="n";
	$proposer_modif="n";

	if($current_group["classe"]["ver_periode"][$eleve_id_classe][$corriger_app_num_periode]=="N") {
		$valider_modif="y";
	}
	elseif($current_group["classe"]["ver_periode"][$eleve_id_classe][$corriger_app_num_periode]=="P") {
		// Tester s'il y a un accès exceptionnel

		if($_SESSION['statut']=='scolarite') {
			$valider_modif="y";
			// L'envoi ou non de mail est défini plus haut pour ce cas
			//$envoi_mail_correction_autrui="y";
		}
		elseif($pp_avec_droit_modif=="y") {
			$valider_modif="y";
			// L'envoi ou non de mail est défini plus haut pour ce cas
		}
		else {
			if(getSettingAOui('autoriser_correction_bulletin')) {
				$proposer_modif="y";
			}

			$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite, mode FROM matieres_app_delais WHERE id_groupe='".$corriger_app_id_groupe."' AND periode='$corriger_app_num_periode';";
			$res_mad=mysqli_query($mysqli, $sql);
			if($res_mad->num_rows>0) {
				$lig_mad=$res_mad->fetch_object();
				$date_limite=$lig_mad->date_limite;
				if($date_courante<$date_limite) {
					$proposer_modif="y";
					if($lig_mad->mode=='acces_complet') {
						$valider_modif="y";
					}
				}
			}
		}
	}

	if($valider_modif=="y") {
		// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
		$app=suppression_sauts_de_lignes_surnumeraires($app);

		//==================================================
		if($envoi_mail_correction_autrui=="y") {
			$ancienne_app="";
			$sql="SELECT appreciation FROM matieres_appreciations WHERE (login='$corriger_app_login_eleve' AND id_groupe='".$current_group["id"]."' AND periode='$corriger_app_num_periode');";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);
				$ancienne_app=$lig->appreciation;
				//echo "<span style='color:plum'>ancienne_app=$ancienne_app</span>";
			}

			$tab_param_mail=array();

			$texte_mail="Je viens d'effectuer (".strftime("%d/%m/%Y à %H:%M:%S").") une correction de votre appréciation pour ".get_nom_prenom_eleve($corriger_app_login_eleve)." en ".$current_group["name"]." (".$current_group["description"]." en ".$current_group["classlist_string"].") en période ".$corriger_app_num_periode.".

L'ancienne appréciation était:
====================================================
$ancienne_app
====================================================

et la nouvelle:
====================================================
$app
====================================================


Cordialement.
-- 
".civ_nom_prenom($_SESSION['login']);

			$envoi_mail_actif=getSettingValue('envoi_mail_actif');
			if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
				$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
			}

			//if($envoi_mail_actif=='y') {
				$email_destinataires="";

				$sql="SELECT DISTINCT u.login, u.email FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.login=u.login AND jgp.id_groupe='$corriger_app_id_groupe';";
				$req=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($req)>0) {
					//$tab_email_destinataire=array();
					$tab_login_destinataire=array();

					$cpt_dest=0;
					while($lig_u=mysqli_fetch_object($req)) {
						if($cpt_dest>0) {
							$email_destinataires.=", ";
						}

						if(check_mail($lig_u->email)) {
							$email_destinataires=$lig_u->email;
							$tab_param_mail['destinataire'][]=$lig_u->email;
							$cpt_dest++;
						}

						$tab_login_destinataire[]=$lig_u->login;
					}
				}

				//if($email_destinataires=="") {
					// Au cas où l'envoi de mail foirerait, on met toujours un message:
					// On met un message en page d'accueil
					for($loop=0;$loop<count($tab_login_destinataire);$loop++) {
						$r_sql = "INSERT INTO messages
								SET texte = '".mysqli_real_escape_string($GLOBALS["mysqli"], nl2br($texte_mail))."',
								date_debut = '".time()."',
								date_fin = '".(time()+7*24*3600)."',
								date_decompte = '',
								auteur='".$_SESSION['login']."',
								statuts_destinataires = '_',
								login_destinataire='".$tab_login_destinataire[$loop]."';";
						//echo "$r_sql<br />";
						$retour=mysqli_query($GLOBALS["mysqli"], $r_sql);
						if ($retour) {
							$id_message=mysqli_insert_id($GLOBALS["mysqli"]);

							$contenu_cor='
							<form method="POST" action="accueil.php" name="f_suppression_message">
							<input type="hidden" name="csrf_alea" value="_CSRF_ALEA_">
							<input type="hidden" name="supprimer_message" value="'.$id_message.'">
							<button type="submit" title=" Supprimer ce message " style="border: none; background: none; float: right;"><img style="vertical-align: bottom;" src="images/icons/delete.png"></button>
							</form>'.mysqli_real_escape_string($GLOBALS["mysqli"], nl2br($texte_mail));
							$r_sql="UPDATE messages SET texte='".$contenu_cor."' WHERE id='".$id_message."';";
							//echo "<span style='color:green'>$r_sql</span><br />";
							$retour=mysqli_query($GLOBALS["mysqli"], $r_sql);
						}
						/*
						else {
							echo "<span style='color:red'>ERREUR</span>";
						}
						*/
					}
				//}
				//else {
			if($envoi_mail_actif=='y') {
				if($email_destinataires!="") {
					$sql="SELECT id_classe FROM j_eleves_classes WHERE (login='$corriger_app_login_eleve' AND periode='$corriger_app_num_periode');";
					$req=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($req)>0) {
						//$correction_id_classe=mysql_result($req,0,"id_classe");
						$obj_classe=$req->fetch_object();
						$correction_id_classe=$obj_classe->id_classe;
						$sql="SELECT DISTINCT email FROM utilisateurs u, j_scol_classes jsc WHERE u.login=jsc.login AND id_classe='$correction_id_classe';";
					}
					else {
						$sql="SELECT DISTINCT email FROM utilisateurs WHERE statut='scolarite' AND email!='';";
					}
					//echo "$sql<br />";
					$req=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($req)>0) {
						$email_cc="";

						$cpt_cc=0;
						while($lig_u=mysqli_fetch_object($req)) {
							if($cpt_cc>0) {
								$email_cc.=", ";
							}

							if(check_mail($lig_u->email)) {
								$email_cc=$lig_u->email;
								$tab_param_mail['cc'][]=$lig_u->email;
								$cpt_cc++;
							}
						}

						$email_declarant="";
						$nom_declarant="";
						$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
						$req=mysqli_query($mysqli, $sql);
						if(mysqli_num_rows($req)>0) {
							$lig_u=mysqli_fetch_object($req);
							$nom_declarant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
							$email_declarant=$lig_u->email;
							$tab_param_mail['from']=$lig_u->email;
							$tab_param_mail['from_name']=$nom_declarant;
						}

						$sujet_mail="Correction d'appréciation";
	
						$ajout_header="";
						if($email_declarant!="") {
							$ajout_header.="Cc: $nom_declarant <".$email_declarant.">";
							$tab_param_mail['cc'][$cpt_cc]=$email_declarant;
							$tab_param_mail['cc_name'][$cpt_cc]=$nom_declarant;
							if($email_cc!='') {
								$ajout_header.=", $email_cc";
							}
							$ajout_header.="\r\n";
							$ajout_header.="Reply-to: $nom_declarant <".$email_declarant.">\r\n";
							$tab_param_mail['replyto']=$email_declarant;
							$tab_param_mail['replyto_name']=$nom_declarant;

						}
						elseif($email_cc!='') {
							$ajout_header.="Cc: $email_cc\r\n";
						}

						$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
						//$texte_mail=$salutation.",\n\n".$texte_mail."\nCordialement.\n-- \n".$nom_declarant;
						$texte_mail=$salutation.",\n\n".$texte_mail;

						$envoi = envoi_mail($sujet_mail, $texte_mail, $email_destinataires, $ajout_header,"plain",$tab_param_mail);
					}

				}
			}
		}
		//==================================================

		//=========================
		// Ménage: pour ne pas laisser une demande de validation de correction alors qu'on a rouvert la période en saisie... on risquerait d'écraser par la suite l'enregistrement fait après la rouverture de période.
		$sql="DELETE FROM matieres_app_corrections WHERE (login='$corriger_app_login_eleve' AND id_groupe='".$current_group["id"]."' AND periode='$corriger_app_num_periode');";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		//=========================

		$test_eleve_app_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_appreciations WHERE (login='$corriger_app_login_eleve' AND id_groupe='" . $current_group["id"]."' AND periode='$corriger_app_num_periode')");
		$test = mysqli_num_rows($test_eleve_app_query);
		if ($test != "0") {
			if ($app != "") {
				$register = mysqli_query($GLOBALS["mysqli"], "UPDATE matieres_appreciations SET appreciation='" . $app . "' WHERE (login='$corriger_app_login_eleve' AND id_groupe='" . $current_group["id"]."' AND periode='$corriger_app_num_periode')");
			} else {
				$register = mysqli_query($GLOBALS["mysqli"], "DELETE FROM matieres_appreciations WHERE (login='$corriger_app_login_eleve' AND id_groupe='" . $current_group["id"]."' AND periode='$corriger_app_num_periode')");
			}

			if (!$register) {
				echo "<span style='color:red'> KO</span>";
				return false;
				die();
			}
			else {
				echo stripslashes(nl2br($app));
				die();
			}

		} else {
			if ($app != "") {
				$register = mysqli_query($GLOBALS["mysqli"], "INSERT INTO matieres_appreciations SET login='$corriger_app_login_eleve',id_groupe='" . $current_group["id"]."',periode='$corriger_app_num_periode',appreciation='" . $app . "'");

				if (!$register) {
					echo "<span style='color:red'> KO</span>";
					return false;
					die();
				}
				else {
					echo stripslashes(nl2br($app));
					die();
				}
			}
		}
	}
	elseif($proposer_modif=="y") {
		// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
		$app=suppression_sauts_de_lignes_surnumeraires($app);

		$sql="SELECT * FROM matieres_app_corrections WHERE (login='$corriger_app_login_eleve' AND id_groupe='" . $current_group["id"]."' AND periode='$corriger_app_num_periode');";
		fich_debug_proposition_correction_app($prefixe_debug." : $sql\n");
		$test_eleve_app_query = mysqli_query($mysqli, $sql);
		$test = mysqli_num_rows($test_eleve_app_query);
		if ($test != "0") {
			if ($app != "") {
				$sql="UPDATE matieres_app_corrections SET appreciation='" . $app . "' WHERE (login='$corriger_app_login_eleve' AND id_groupe='" . $current_group["id"]."' AND periode='$corriger_app_num_periode');";
			} else {
				$sql="DELETE FROM matieres_app_corrections WHERE (login='$corriger_app_login_eleve' AND id_groupe='" . $current_group["id"]."' AND periode='$corriger_app_num_periode');";
			}
			fich_debug_proposition_correction_app($prefixe_debug." : $sql\n");
			$register = mysqli_query($mysqli, $sql);

			if (!$register) {
				fich_debug_proposition_correction_app($prefixe_debug." : Echec de l'enregistrement de la proposition de correction.\n");
				echo "<span style='color:red' title=\"Echec de l'enregistrement de la proposition de correction\"> KO</span>";
				return false;
				die();
			}
			else {
				echo "<div style='border:1px solid red; color: green' title=\"Proposition de correction soumise.\nElle doit encore être validée.\"><strong>Proposition de correction en attente&nbsp;:</strong><br />".stripslashes(nl2br($app))."</div>";
				fich_debug_proposition_correction_app($prefixe_debug." : Proposition de correction soumise.\n");

				if ($test != "0") {
					$texte_mail="Une correction a été proposée par ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj')."\r\npour l'élève ".civ_nom_prenom($corriger_app_login_eleve)." sur la période $corriger_app_num_periode\r\nen ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].").\r\n\r\nVous pouvez valider ou rejeter la proposition en vous connectant avec un compte de statut scolarité ou secours.\r\nVous trouverez en page d'accueil, dans la rubrique Saisie, un message en rouge concernant la Correction de bulletins.\r\n";
				}
				else {
					$texte_mail="Suppression de la proposition de correction pour l'élève ".civ_nom_prenom($corriger_app_login_eleve)."\r\nsur la période $corriger_app_num_periode en ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].")\r\npar ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj').".\n";
				}
				fich_debug_proposition_correction_app($prefixe_debug." : Texte du mail:\n$texte_mail\n");
				envoi_mail_proposition_correction($corriger_app_login_eleve, $corriger_app_id_groupe, $corriger_app_num_periode, $texte_mail);
				die();
			}

		} else {
			if ($app != "") {
				$sql="INSERT INTO matieres_app_corrections SET login='$corriger_app_login_eleve',id_groupe='" . $current_group["id"]."',periode='$corriger_app_num_periode',appreciation='" . $app . "';";
				fich_debug_proposition_correction_app($prefixe_debug." : $sql\n");
				$register = mysqli_query($mysqli, $sql);

				if (!$register) {
					fich_debug_proposition_correction_app($prefixe_debug." : Echec de l'enregistrement de la proposition de correction.\n");
					echo "<span style='color:red' title=\"Echec de l'enregistrement de la proposition de correction\"> KO</span>";
					return false;
					die();
				}
				else {
					echo "<div style='border:1px solid red; color: green' title=\"Proposition de correction soumise.\nElle doit encore être validée.\"><strong>Proposition de correction en attente&nbsp;:</strong><br />".stripslashes(nl2br($app))."</div>";
					fich_debug_proposition_correction_app($prefixe_debug." : Proposition de correction soumise.\n");

					$texte_mail="Une correction proposée a été mise à jour par ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj')."\r\npour l'élève ".civ_nom_prenom($corriger_app_login_eleve)." sur la période $corriger_app_num_periode\r\nen ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].").\r\n\r\nVous pouvez valider ou rejeter la proposition en vous connectant avec un compte de statut scolarité ou secours.\r\nVous trouverez en page d'accueil, dans la rubrique Saisie, un message en rouge concernant la Correction de bulletins.\r\n";
					fich_debug_proposition_correction_app($prefixe_debug." : Texte du mail:\n$texte_mail\n");
					envoi_mail_proposition_correction($corriger_app_login_eleve, $corriger_app_id_groupe, $corriger_app_num_periode, $texte_mail);
					die();
				}
			}
		}
	}
}
?>
