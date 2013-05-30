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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$msg="";
$tab_id_classe=isset($_POST['tab_id_classe']) ? $_POST['tab_id_classe'] : NULL;
if(isset($_POST['action_corrections'])) {
	check_token();
	$enregistrement=isset($_POST['enregistrement']) ? $_POST['enregistrement'] : array();
	$action=isset($_POST['action']) ? $_POST['action'] : array();

	$tab_actions_valides=array('en_attente', 'valider', 'supprimer');
	$nb_reg=0;

	$texte_email=array();

	//$reimprimer_bulletins="";
	$reimprimer_bulletins=array();
	$tab_periode_num=array();

	$tab_liste_id_groupe=array();
	$tab_proflist_string=array();

	for($i=0;$i<count($enregistrement);$i++) {
		$tab_tmp=explode("|",$enregistrement[$i]);
		$current_login_ele=$tab_tmp[0];
		$current_id_groupe=$tab_tmp[1];
		$current_periode=$tab_tmp[2];

		$current_group=get_group($current_id_groupe);

		//echo "<pre>".print_r($current_group)."</pre>";
		if(isset($current_group["profs"]['proflist_string'])) {
			$tab_proflist_string[$current_id_groupe]=$current_group["profs"]['proflist_string'];
			//echo "\$tab_proflist_string[$current_id_groupe]=".$tab_proflist_string[$current_id_groupe]."<br />";
		}

		if($current_login_ele=='') {
			// Appréciation de groupe

			if((mb_strlen(preg_replace('/[0-9]/','',$current_id_groupe))==0)&&
			(mb_strlen(preg_replace('/[0-9]/','',$current_periode))==0)) {
	
				if ((isset($action[$i]))&&(in_array($action[$i],$tab_actions_valides))) {
					if (isset($NON_PROTECT["appreciation".$i])) {
						$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["appreciation".$i]));
						// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
						$app=suppression_sauts_de_lignes_surnumeraires($app);

						if($action[$i]=='supprimer') {
							$sql="DELETE FROM matieres_app_corrections WHERE (login='' AND id_groupe='$current_id_groupe' AND periode='$current_periode');";
							$del=mysql_query($sql);
							if($del) {
								$msg.="Suppression de l'enregistrement temporaire $enregistrement[$i].<br />";
								//$nb_reg++;
								// Envoyer un mail... problème... il serait bien de n'envoyer qu'un seul mail par destinataire, plutôt que un mail par correction
								if(!isset($texte_email[$current_id_groupe])) {$texte_email[$current_id_groupe]="";}
								//$texte_email[$current_id_groupe].="Votre proposition de correction pour $enregistrement[$i] a été refusée/supprimée.\n";
								$texte_email[$current_id_groupe].="Votre proposition de correction pour l'appréciation de groupe en ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].") sur la période $current_periode a été refusée/supprimée.\n";
							}
							else {
								$msg.="Erreur lors de la suppression de l'enregistrement temporaire $enregistrement[$i].<br />";
							}
						}
						elseif($action[$i]=='valider') {
							//$sql="UPDATE matieres_appreciations SET appreciation='$app' WHERE (login='$current_login_ele' AND id_groupe='$current_id_groupe' AND periode='$current_periode');";
							$sql="DELETE FROM matieres_appreciations_grp WHERE (id_groupe='$current_id_groupe' AND periode='$current_periode');";
							$menage=mysql_query($sql);
	
							$sql="INSERT INTO matieres_appreciations_grp SET id_groupe='$current_id_groupe', periode='$current_periode', appreciation='$app';";
							$insert=mysql_query($sql);
							if($insert) {
								$sql="DELETE FROM matieres_app_corrections WHERE (login='' AND id_groupe='$current_id_groupe' AND periode='$current_periode');";
								$del=mysql_query($sql);
								if($del) {
									$nb_reg++;
									$msg.="Suppression de l'enregistrement temporaire $enregistrement[$i].<br />";
									// Envoyer un mail... problème... il serait bien de n'envoyer qu'un seul mail par destinataire, plutôt que un mail par correction
									if(!isset($texte_email[$current_id_groupe])) {$texte_email[$current_id_groupe]="";}
									$texte_email[$current_id_groupe].="Votre proposition de correction pour l'appreciation de groupe en ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].") sur la période $current_periode a été validée.\n";
	
									/*
									if(!in_array($current_periode,$tab_periode_num)) {$tab_periode_num[]=$current_periode;}
									//$reimprimer_bulletins.="<input type='hidden' name='preselection_eleves[$current_periode][]' value='$current_login_ele' />\n";
									if(!isset($reimprimer_bulletins[$current_periode])) {$reimprimer_bulletins[$current_periode]="|";}
									$reimprimer_bulletins[$current_periode].="$current_login_ele|";
									*/
								}
								else {
									$msg.="Erreur lors de la suppression de l'enregistrement temporaire $enregistrement[$i].<br />";
								}
							}
							else {
								$msg.="Erreur lors de la mise à jour de l'enregistrement $enregistrement[$i] sur le bulletin.<br />";
							}
						}

						if(!in_array($current_id_groupe,$tab_liste_id_groupe)) {
							$tab_liste_id_groupe[]=$current_id_groupe;
						}
					}
					else {
						$msg.="Action $action[$i] invalide.<br />";
					}
				}
			}
			else {
				$msg.="Des caractères invalides sont proposés pour $enregistrement[$i].<br />";
			}
		}
		else {
			$current_nom_prenom_eleve=get_nom_prenom_eleve($current_login_ele);

			if((mb_strlen(preg_replace('/[A-Za-z0-9._-]/','',$current_login_ele))==0)&&
			(mb_strlen(preg_replace('/[0-9]/','',$current_id_groupe))==0)&&
			(mb_strlen(preg_replace('/[0-9]/','',$current_periode))==0)) {
	
				if ((isset($action[$i]))&&(in_array($action[$i],$tab_actions_valides))) {
					if (isset($NON_PROTECT["appreciation".$i])) {
						$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["appreciation".$i]));
						// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
						$app=suppression_sauts_de_lignes_surnumeraires($app);
	
						if($action[$i]=='supprimer') {
							$sql="DELETE FROM matieres_app_corrections WHERE (login='$current_login_ele' AND id_groupe='$current_id_groupe' AND periode='$current_periode');";
							$del=mysql_query($sql);
							if($del) {
								$msg.="Suppression de l'enregistrement temporaire $enregistrement[$i].<br />";
								//$nb_reg++;
								// Envoyer un mail... problème... il serait bien de n'envoyer qu'un seul mail par destinataire, plutôt que un mail par correction
								if(!isset($texte_email[$current_id_groupe])) {$texte_email[$current_id_groupe]="";}
								//$texte_email[$current_id_groupe].="Votre proposition de correction pour $enregistrement[$i] a été refusée/supprimée.\n";
								$texte_email[$current_id_groupe].="Votre proposition de correction pour ".$current_nom_prenom_eleve." en ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].") sur la période $current_periode a été refusée/supprimée.\n";
							}
							else {
								$msg.="Erreur lors de la suppression de l'enregistrement temporaire $enregistrement[$i].<br />";
							}
						}
						elseif($action[$i]=='valider') {
							//$sql="UPDATE matieres_appreciations SET appreciation='$app' WHERE (login='$current_login_ele' AND id_groupe='$current_id_groupe' AND periode='$current_periode');";
							$sql="DELETE FROM matieres_appreciations WHERE (login='$current_login_ele' AND id_groupe='$current_id_groupe' AND periode='$current_periode');";
							$menage=mysql_query($sql);
	
							$sql="INSERT INTO matieres_appreciations SET login='$current_login_ele', id_groupe='$current_id_groupe', periode='$current_periode', appreciation='$app';";
							$insert=mysql_query($sql);
							if($insert) {
								$sql="DELETE FROM matieres_app_corrections WHERE (login='$current_login_ele' AND id_groupe='$current_id_groupe' AND periode='$current_periode');";
								$del=mysql_query($sql);
								if($del) {
									$nb_reg++;
									$msg.="Suppression de l'enregistrement temporaire $enregistrement[$i].<br />";
									// Envoyer un mail... problème... il serait bien de n'envoyer qu'un seul mail par destinataire, plutôt que un mail par correction
									if(!isset($texte_email[$current_id_groupe])) {$texte_email[$current_id_groupe]="";}
									$texte_email[$current_id_groupe].="Votre proposition de correction pour ".$current_nom_prenom_eleve." en ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].") sur la période $current_periode a été validée.\n";
	
									if(!in_array($current_periode,$tab_periode_num)) {$tab_periode_num[]=$current_periode;}
									//$reimprimer_bulletins.="<input type='hidden' name='preselection_eleves[$current_periode][]' value='$current_login_ele' />\n";
									if(!isset($reimprimer_bulletins[$current_periode])) {$reimprimer_bulletins[$current_periode]="|";}
									$reimprimer_bulletins[$current_periode].="$current_login_ele|";
								}
								else {
									$msg.="Erreur lors de la suppression de l'enregistrement temporaire $enregistrement[$i].<br />";
								}
							}
							else {
								$msg.="Erreur lors de la mise à jour de l'enregistrement $enregistrement[$i] sur le bulletin.<br />";
							}
						}
	
						if(!in_array($current_id_groupe,$tab_liste_id_groupe)) {
							$tab_liste_id_groupe[]=$current_id_groupe;
						}
					}
					else {
						$msg.="Action $action[$i] invalide.<br />";
					}
				}
			}
			else {
				$msg.="Des caractères invalides sont proposés pour $enregistrement[$i].<br />";
			}
		}
	}

	if(($nb_reg>0)||(count($texte_email)>0)) {
		if($nb_reg>0) {$msg.="$nb_reg enregistrement(s) effectué(s).<br />";}

		$envoi_mail_actif=getSettingValue('envoi_mail_actif');
		if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
			$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
		}

		if($envoi_mail_actif=='y') {
			$email_reply="";
			$tab_email_reply=array();
			//$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
			$sql="(select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."')";
			$sql.=" UNION (select nom, prenom, civilite, email from utilisateurs where statut = 'secours' AND etat='actif')";

			for($loop=0;$loop<count($tab_liste_id_groupe);$loop++) {
				$sql.=" UNION (select nom, prenom, civilite, email from utilisateurs u, j_scol_classes jsc, j_groupes_classes jgc where u.statut='scolarite' AND u.email!='' AND etat='actif' AND u.login=jsc.login AND jsc.id_classe=jgc.id_classe AND jgc.id_groupe='".$tab_liste_id_groupe[$loop]."')";
			}
			//echo "$sql<br />";
			$req=mysql_query($sql);
			if(mysql_num_rows($req)>0) {
				while($lig_u=mysql_fetch_object($req)) {
					if(!in_array($lig_u->email, $tab_email_reply)) {
						if($email_reply!="") {$email_reply.=", ";}
						$email_reply.=$lig_u->email;
						$tab_email_reply[]=$lig_u->email;
					}
				}
			}

			foreach($texte_email as $id_groupe => $texte) {
				if($texte!='') {
					$email_destinataires="";

					// Recherche des profs du groupe
					$sql="SELECT DISTINCT u.email FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.id_groupe='$id_groupe' AND jgp.login=u.login AND u.email!='';";
					//echo "$sql<br />";
					$req=mysql_query($sql);
					if(mysql_num_rows($req)>0) {
						$lig_u=mysql_fetch_object($req);
						$email_destinataires.=$lig_u->email;
						while($lig_u=mysql_fetch_object($req)) {$email_destinataires.=",".$lig_u->email;}
					}

					if($email_destinataires!='') {
						$sujet_mail="Groupe n°$id_groupe: Réponse à votre demande de correction";

						$ajout_header="";
						if($email_reply!="") {
							$ajout_header.="Cc: $email_reply\r\n";
							$ajout_header.="Reply-to: $email_reply\r\n";
						}
	
						$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
						if(isset($tab_proflist_string[$id_groupe])) {
							$salutation.=" ".$tab_proflist_string[$id_groupe];
						}
						$texte=$salutation.",\n\n".$texte."\nCordialement.\n-- \n".civ_nom_prenom($_SESSION['login']);

						$envoi = envoi_mail($sujet_mail, $texte, $email_destinataires, $ajout_header);
					}
				}
			}
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Validation des corrections";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();
?>

<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>
<?php

if(!isset($tab_id_classe)) {
	echo "</p>\n";

	if($_SESSION['statut']=='scolarite') {
		$sql="SELECT DISTINCT c.id, c.classe FROM classes c, j_eleves_classes jec, matieres_app_corrections mac, j_scol_classes jsc WHERE c.id=jec.id_classe AND jec.login=mac.login AND jec.periode=mac.periode AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe;";
	}
	else {
		$sql="SELECT DISTINCT c.id, c.classe FROM classes c, j_eleves_classes jec, matieres_app_corrections mac WHERE c.id=jec.id_classe AND jec.login=mac.login AND jec.periode=mac.periode ORDER BY classe;";
	}
	//echo "$sql<br />\n";
	$res=mysql_query($sql);
	$nb_classes=mysql_num_rows($res);
	if($nb_classes==0) {
		echo "<p>Aucune propostion de correction ne requiert votre attention.</p>\n";
		echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	//echo add_token_field();
	echo "<p class='bold'>Veuillez choisir la ou les classes pour lesquelles vous souhaitez consulter/valider les propositions de corrections.</p>\n";

	$cpt=0;
	$nb_class_par_colonne=round($nb_classes/3);
	//echo "<table width='100%' border='1'>\n";
	echo "<table width='100%' summary=\"Choix des classes\">\n";
	echo "<tr valign='top' align='center'>\n";
	echo "<td align='left'>\n";
	while($lig_clas=mysql_fetch_object($res)) {
		if(($cpt>0)&&(round($cpt/$nb_class_par_colonne)==$cpt/$nb_class_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}
		echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='tab_id_classe[]' id='tab_id_classe_$cpt' value='$lig_clas->id' onchange='change_style_classe($cpt)' ";
		if($nb_classes==1) {
			echo "checked ";
		}
		echo "/> $lig_clas->classe</label>";
		echo "<br />\n";
		$cpt++;
	}
	echo "</table>\n";
	echo "<p><a href='#' onClick='ModifCase(true); return false;'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('tab_id_classe_'+k)){
				document.getElementById('tab_id_classe_'+k).checked = mode;
				change_style_classe(k);
			}
		}
	}

	function change_style_classe(num) {
		if(document.getElementById('tab_id_classe_'+num)) {
			if(document.getElementById('tab_id_classe_'+num).checked) {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";

}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a></p>\n";

	//if((isset($reimprimer_bulletins))&&($reimprimer_bulletins!="")) {
	if((isset($reimprimer_bulletins))&&(count($reimprimer_bulletins)>0)) {
		echo "<div class='infobulle_corps' style='border: 1px solid black;'>\n";
		echo "<form enctype='multipart/form-data' action='../bulletin/bull_index.php' method='post' name='form_bulletin' target='_blank'>\n";
		//echo add_token_field();

		echo "<p><span style='font-weight:bold; color:red'>ATTENTION&nbsp;:</span> Des modifications ont été effectuées.<br />Il faut sans doute réimprimer les bulletins correspondants.</p>\n";
	
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<input type='hidden' name='tab_id_classe[]' value='$tab_id_classe[$i]' />\n";
		}
		for($i=0;$i<count($tab_periode_num);$i++) {
			echo "<input type='hidden' name='tab_periode_num[]' value='$tab_periode_num[$i]' />\n";
		}
	
		//echo $reimprimer_bulletins;
		foreach($reimprimer_bulletins as $periode => $liste_ele) {
			echo "<input type='hidden' name='preselection_eleves[$periode]' value='$liste_ele' />\n";
		}

		echo "<input type='hidden' name='choix_periode_num' value='fait' />\n";
	
		echo "<p><input type='submit' value='Réimprimer les bulletins' /></p>\n";
		echo "</form>\n";
		echo "</div>\n";
	}

	$compteur=0;
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action_corrections' value='y' />\n";

	for($i=0;$i<count($tab_id_classe);$i++) {
		$classe=get_class_from_id($tab_id_classe[$i]);
		echo "<p class='bold'>".$classe."</p>\n";
		echo "<input type='hidden' name='tab_id_classe[]' value='$tab_id_classe[$i]' />\n";
		echo "<blockquote>\n";

		// Groupes associés à la classe
		$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, matieres_app_corrections mac WHERE jgc.id_classe='$tab_id_classe[$i]' AND jgc.id_groupe=mac.id_groupe;";
		//echo "$sql<br />\n";
		$res=mysql_query($sql);
		$nb_grp=mysql_num_rows($res);
		if($nb_grp==0) {
			echo "<p>Aucun enseignement associé à cette classe ne présente (plus) de proposition de correction.</p>\n";
		}
		else {
			echo "<table class='boireaus' width='100%' summary=\"Corrections proposées en $classe\">\n";
			echo "<tr>\n";
			echo "<th rowspan='2'>Enseignement</th>\n";
			echo "<th rowspan='2'>Elève</th>\n";
			echo "<th rowspan='2'>Période</th>\n";
			echo "<th rowspan='2'>Proposition</th>\n";
			echo "<th colspan='3'>Action</th>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<th>En attente</th>\n";
			echo "<th>Valider</th>\n";
			echo "<th>Supprimer</th>\n";
			echo "</tr>\n";

			$alt=1;
			while($lig=mysql_fetch_object($res)) {
				$current_group=get_group($lig->id_groupe);

				// Elèves avec correction associés au groupe
				//$sql="SELECT DISTINCT mac.*, ma.appreciation AS old_app FROM matieres_app_corrections mac, matieres_appreciations ma, j_eleves_classes jec WHERE jec.id_classe='$tab_id_classe[$i]' AND jec.periode=mac.periode AND jec.login=mac.login AND mac.id_groupe='$lig->id_groupe' AND mac.periode=ma.periode AND mac.id_groupe=ma.id_groupe AND mac.login=ma.login ORDER BY ma.login;";
				// On ne récupérait pas d'élèves si le prof n'avait pas rempli d'appréciation pour la période (ça ne fonctionnait que pour une correction, pas pour une proposition de première saisie après la date de verrouillage)

				$sql="SELECT DISTINCT mac.* FROM matieres_app_corrections mac WHERE mac.id_groupe='$lig->id_groupe' AND mac.login='';";
				//echo "$sql<br />\n";
				$res_grp=mysql_query($sql);

				if(mysql_num_rows($res_grp)>0) {
					$lig_grp=mysql_fetch_object($res_grp);

					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					$liste_profs="";
					foreach($current_group["profs"]["list"] as $key => $prof_login) {
						if($liste_profs!="") {$liste_profs.=", ";}
						$liste_profs.=civ_nom_prenom($prof_login);
					}

					echo "<td valign='top'>".$current_group['name']."<br /><span style='font-size:small;'>(".$current_group["description"]." en ".$current_group["classlist_string"].")</span><br /><span style='font-size:small;'>".$liste_profs."</span></td>\n";

					echo "<td>Groupe<input type='hidden' name='enregistrement[$compteur]' value='|".$lig_grp->id_groupe."|".$lig_grp->periode."' /></td>\n";
					echo "<td>$lig_grp->periode</td>\n";
					echo "<td>";
					echo "<div style='border: 1px solid black; margin: 2px;'>\n";
					echo "<b>Appréciation enregistrée&nbsp;:</b> ";
					//echo nl2br($lig_ele->old_app);
					$sql="SELECT * FROM matieres_appreciations_grp WHERE periode='$lig_grp->periode' AND id_groupe='$lig_grp->id_groupe';";
					$res_old_app=mysql_query($sql);
					if(mysql_num_rows($res_old_app)>0) {
						$lig_old=mysql_fetch_object($res_old_app);
						echo nl2br($lig_old->appreciation);
					}
					else {
						echo "<span style='color:red'>Aucune appréciation n'a été enregistrée avant la proposition de correction.</span>\n";
					}
					echo "</div>\n";
					echo "<div style='border: 1px solid black; margin: 2px;'>\n";
					echo "<b>Correction proposée&nbsp;:</b> ";
					echo "<textarea id=\"n".$compteur."\" class='wrap' onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_appreciation".$compteur."\" cols='70' rows='2'>".$lig_grp->appreciation."</textarea>\n";
					echo "</div>\n";
					echo "</td>\n";

					echo "<td><input type='radio' name='action[$compteur]' id='action_attente_$compteur' value='en_attente' checked /></td>\n";
					echo "<td><input type='radio' name='action[$compteur]' id='action_valider_$compteur' value='valider' /></td>\n";
					echo "<td><input type='radio' name='action[$compteur]' id='action_supprimer_$compteur' value='supprimer' /></td>\n";

					echo "</tr>\n";

					$compteur++;
				}

				$sql="SELECT DISTINCT mac.* FROM matieres_app_corrections mac, j_eleves_classes jec WHERE jec.id_classe='$tab_id_classe[$i]' AND jec.periode=mac.periode AND jec.login=mac.login AND mac.id_groupe='$lig->id_groupe' ORDER BY mac.login;";
				//echo "$sql<br />\n";
				$res_ele=mysql_query($sql);
				$nb_eleves=mysql_num_rows($res_ele);
				$cpt=0;
				while($lig_ele=mysql_fetch_object($res_ele)) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					if($cpt==0) {
						$liste_profs="";
						foreach($current_group["profs"]["list"] as $key => $prof_login) {
							if($liste_profs!="") {$liste_profs.=", ";}
							$liste_profs.=civ_nom_prenom($prof_login);
						}

						echo "<td valign='top' rowspan='$nb_eleves'>".$current_group['name']."<br /><span style='font-size:small;'>(".$current_group["description"]." en ".$current_group["classlist_string"].")</span><br /><span style='font-size:small;'>".$liste_profs."</span></td>\n";
					}

					echo "<td>".get_nom_prenom_eleve($lig_ele->login)."<input type='hidden' name='enregistrement[$compteur]' value='".$lig_ele->login."|".$lig_ele->id_groupe."|".$lig_ele->periode."' /></td>\n";
					echo "<td>$lig_ele->periode</td>\n";
					echo "<td>";
					echo "<div style='border: 1px solid black; margin: 2px;'>\n";
					echo "<b>Appréciation enregistrée&nbsp;:</b> ";
					//echo nl2br($lig_ele->old_app);
					$sql="SELECT * FROM matieres_appreciations WHERE periode='$lig_ele->periode' AND id_groupe='$lig_ele->id_groupe' AND login='$lig_ele->login';";
					$res_old_app=mysql_query($sql);
					if(mysql_num_rows($res_old_app)>0) {
						$lig_old=mysql_fetch_object($res_old_app);
						echo nl2br($lig_old->appreciation);
					}
					else {
						echo "<span style='color:red'>Aucune appréciation n'a été enregistrée avant la proposition de correction.</span>\n";
					}
					echo "</div>\n";
					echo "<div style='border: 1px solid black; margin: 2px;'>\n";
					echo "<b>Correction proposée&nbsp;:</b> ";
					echo "<textarea id=\"n".$compteur."\" class='wrap' onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_appreciation".$compteur."\" cols='70' rows='2'>".$lig_ele->appreciation."</textarea>\n";
					echo "</div>\n";
					echo "</td>\n";

					echo "<td><input type='radio' name='action[$compteur]' id='action_attente_$compteur' value='en_attente' checked /></td>\n";
					echo "<td><input type='radio' name='action[$compteur]' id='action_valider_$compteur' value='valider' /></td>\n";
					echo "<td><input type='radio' name='action[$compteur]' id='action_supprimer_$compteur' value='supprimer' /></td>\n";

					echo "</tr>\n";
					$cpt++;
					$compteur++;
				}
			}
			echo "</table>\n";
		}
		echo "</blockquote>\n";
	}

	echo "<p><a href='#' onClick=\"ToutCocher('action_valider_'); return false;\">Tout valider</a> / <a href='#' onClick=\"ToutCocher('action_supprimer_'); return false;\">Tout supprimer</a> / <a href='#' onClick=\"ToutCocher('action_attente_'); return false;\">Tout remettre en attente</a></p>\n";
	echo "<p><input type='submit' value='Enregistrer' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	function ToutCocher(prefixe) {
		for (var k=0;k<$compteur;k++) {
			if(document.getElementById(prefixe+k)){
				document.getElementById(prefixe+k).checked=true;
			}
		}
	}
</script>\n";

}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
