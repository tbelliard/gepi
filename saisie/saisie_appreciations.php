<?php
/*
* $Id$
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


// Initialistion
$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
	$current_group = get_group($id_groupe);
} else {
	$current_group = false;

	// Pourquoi poursuivre si le groupe n'est pas trouvé?
	$mess=rawurlencode("Anomalie: Vous arrivez sur cette page sans que l'enseignement soit sélectionné ! Si vous aviez bien sélectionné un enseignement, il se peut que vous ayez un module php du type 'suhosin' qui limite le nombre de variables pouvant être postées dans un formulaire.");
	header("Location: index.php?msg=$mess");
	die();
}

if (count($current_group["classes"]["list"]) > 1) {
	$multiclasses = true;
} else {
	$multiclasses = false;
}

$periode_cn = isset($_POST["periode_cn"]) ? $_POST["periode_cn"] :(isset($_GET["periode_cn"]) ? $_GET["periode_cn"] :NULL);
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "classe");

include "../lib/periodes.inc.php";


if ($_SESSION['statut'] != "secours") {
	if (!(check_prof_groupe($_SESSION['login'],$current_group["id"]))) {
		$mess=rawurlencode("Vous n'êtes pas professeur de cet enseignement !");
		header("Location: index.php?msg=$mess");
		die();
	}
}

$msg="";

if (isset($_POST['is_posted'])) {

	$indice_max_log_eleve=$_POST['indice_max_log_eleve'];

	$k=1;
	while ($k < $nb_periode) {
		//=========================
		// AJOUT: boireaus 20071003
		unset($log_eleve);
		$log_eleve=isset($_POST['log_eleve_'.$k]) ? $_POST['log_eleve_'.$k] : NULL;
		//=========================

		//=================================================
		// AJOUT: boireaus 20080201
		if(isset($_POST['app_grp_'.$k])){
			//echo "\$current_group[\"classe\"][\"ver_periode\"]['all'][$k]=".$current_group["classe"]["ver_periode"]['all'][$k]."<br />";
			//if($current_group["classe"]["ver_periode"]['all'][$k]!=0){

			//if(($current_group["classe"]["ver_periode"]['all'][$k]!=0)&&($current_group["classe"]["ver_periode"]['all'][$k]!=1)) {
			if(($current_group["classe"]["ver_periode"]['all'][$k]>=2)||
				(($current_group["classe"]["ver_periode"]['all'][$k]!=0)&&($_SESSION['statut']=='secours'))) {

				if (isset($NON_PROTECT["app_grp_".$k])){
					$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["app_grp_".$k]));
				}
				else{
					$app = "";
				}
				//echo "$k: $app<br />";
				// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
				$app=my_ereg_replace('(\\\r\\\n)+',"\r\n",$app);

				$test_grp_app_query = mysql_query("SELECT * FROM matieres_appreciations_grp WHERE (id_groupe='" . $current_group["id"]."' AND periode='$k')");
				$test = mysql_num_rows($test_grp_app_query);
				if ($test != "0") {
					if ($app != "") {
						$register = mysql_query("UPDATE matieres_appreciations_grp SET appreciation='" . $app . "' WHERE (id_groupe='" . $current_group["id"]."' AND periode='$k')");
					} else {
						$register = mysql_query("DELETE FROM matieres_appreciations_grp WHERE (id_groupe='" . $current_group["id"]."' AND periode='$k')");
					}
					if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour le groupe/classe<br />";}

				} else {
					if ($app != "") {
						$register = mysql_query("INSERT INTO matieres_appreciations_grp SET id_groupe='" . $current_group["id"]."',periode='$k',appreciation='" . $app . "'");
						if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour le groupe/classe<br />";}
					}
				}
			}
			else {
				$msg.="Anomalie: Tentative de saisie d'une appréciation de classe alors que la période n'est pas ouverte en saisie.<br />";
			}
		}
		//=================================================

		if(isset($log_eleve)){
			//for($i=0;$i<count($log_eleve);$i++){
			for($i=0;$i<$indice_max_log_eleve;$i++){

				//echo "\$log_eleve[$i]=$log_eleve[$i]<br />\n";
				if(isset($log_eleve[$i])) {
					// On supprime le suffixe indiquant la période:
					$reg_eleve_login=my_ereg_replace("_t".$k."$","",$log_eleve[$i]);

					//echo "\$i=$i<br />";
					//echo "\$reg_eleve_login=$reg_eleve_login<br />";

					// La période est-elle ouverte?
					if (in_array($reg_eleve_login, $current_group["eleves"][$k]["list"])) {
						$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$reg_eleve_login]["classe"]]["id"];
						//if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] == "N"){
						if(($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]=="N")||
							(($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]!="O")&&($_SESSION['statut']=='secours'))) {

							$nom_log = "app_eleve_".$k."_".$i;

							//echo "\$nom_log=$nom_log<br />";

							if (isset($NON_PROTECT[$nom_log])){
								$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
							}
							else{
								$app = "";
							}

							//echo "\$app=$app<br />";

							// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
							$app=my_ereg_replace('(\\\r\\\n)+',"\r\n",$app);


							//=========================
							// 20100604
							// Ménage: pour ne pas laisser une demande de validation de correction alors qu'on a rouvert la période en saisie... on risquerait d'écraser par la suite l'enregistrement fait après la rouverture de période.
							$sql="DELETE FROM matieres_app_corrections WHERE (login='$reg_eleve_login' AND id_groupe='".$current_group["id"]."' AND periode='$k');";
							$del=mysql_query($sql);
							//=========================


							$test_eleve_app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
							$test = mysql_num_rows($test_eleve_app_query);
							if ($test != "0") {
								if ($app != "") {
									$register = mysql_query("UPDATE matieres_appreciations SET appreciation='" . $app . "' WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
								} else {
									$register = mysql_query("DELETE FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
								}
								if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour l'élève $reg_eleve_login<br />";}

							} else {
								if ($app != "") {
									$register = mysql_query("INSERT INTO matieres_appreciations SET login='$reg_eleve_login',id_groupe='" . $current_group["id"]."',periode='$k',appreciation='" . $app . "'");
									if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour l'élève $reg_eleve_login<br />";}
								}
							}
						}
					}
				}
			}
		}
		$k++;
	}

	if($msg=='') {
		// On ne vide que si l'enregistrement s'est bien passé

		// A partir de là, toutes les appréciations ont été sauvegardées proprement, on vide la table tempo
		$effacer = mysql_query("DELETE FROM matieres_appreciations_tempo WHERE id_groupe = '".$id_groupe."'")
		OR die('Erreur dans l\'effacement de la table temporaire (1) :'.mysql_error());
	}

	/*
	foreach ($current_group["eleves"]["all"]["list"] as $reg_eleve_login) {
		$k=1;
		while ($k < $nb_periode) {
			if (in_array($reg_eleve_login, $current_group["eleves"][$k]["list"])) {
					$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$reg_eleve_login]["classe"]]["id"];
					if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] == "N"){
						$nom_log = $reg_eleve_login."_t".$k;
						if (isset($NON_PROTECT[$nom_log]))
						$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
						else
						$app = "";

						// Contrôle des saisies pour supprimer les sauts de lignes surnuméraire.
						$app=my_ereg_replace('(\\\r\\\n)+',"\r\n",$app);


						$test_eleve_app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
						$test = mysql_num_rows($test_eleve_app_query);
						if ($test != "0") {
							if ($app != "") {
								$register = mysql_query("UPDATE matieres_appreciations SET appreciation='" . $app . "' WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
							} else {
								$register = mysql_query("DELETE FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
							}
							if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour l'élève $reg_eleve_login<br />";}

						} else {
							if ($app != "") {
								$register = mysql_query("INSERT INTO matieres_appreciations SET login='$reg_eleve_login',id_groupe='" . $current_group["id"]."',periode='$k',appreciation='" . $app . "'");
								if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour l'élève $reg_eleve_login<br />";}
							}
						}
					}
			}
			$k++;
		}
	}
	*/

	if($msg=="") {
		$affiche_message = 'yes';
	}
}
// 20100604
//elseif((isset($_POST['correction_login_eleve']))&&(isset($_POST['correction_periode']))&&(isset($_POST['correction_app_eleve']))&&(isset($_POST['correction_id_groupe']))) {
//elseif((isset($_POST['correction_login_eleve']))&&(isset($_POST['correction_periode']))&&(isset($_POST['no_anti_inject_correction_app_eleve']))) {
elseif((isset($_POST['correction_login_eleve']))&&(isset($_POST['correction_periode']))&&(isset($_POST['no_anti_inject_correction_app_eleve']))&&(getSettingValue('autoriser_correction_bulletin')=='y')) {
	// Dispositif pour proposer des corrections une fois la période close.
	$correction_login_eleve=$_POST['correction_login_eleve'];
	$correction_periode=$_POST['correction_periode'];
	//$correction_app_eleve=$_POST['correction_app_eleve'];
	//$correction_id_groupe=$_POST['correction_id_groupe'];

	//echo "BLABLA";

	// Un test check_prof_groupe($_SESSION['login'],$current_group["id"]) est fait plus haut pour contrôler que le prof est bien associé à ce groupe

	if (isset($NON_PROTECT["correction_app_eleve"])) {
		$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["correction_app_eleve"]));
		// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
		$app=my_ereg_replace('(\\\r\\\n)+',"\r\n",$app);

		$texte_mail="";

		$correction_nom_prenom_eleve=get_nom_prenom_eleve($correction_login_eleve);

		if((strlen(my_ereg_replace('[A-Za-z0-9._-]','',$correction_login_eleve))!=0)||
		(strlen(my_ereg_replace('[0-9]','',$correction_periode))!=0)) {
			$msg.="Des caractères invalides sont proposés pour le login élève $correction_nom_prenom_eleve ou pour la période $correction_periode.<br />";
		}
		else {

			$sql="SELECT 1=1 FROM j_eleves_groupes WHERE login='$correction_login_eleve' AND periode='$correction_periode' AND id_groupe='$id_groupe';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0) {
				$msg.="L'élève $correction_nom_prenom_eleve n'est pas associé au groupe n°$id_groupe pour la période $correction_periode.<br />";
			}
			else {

				$sql="SELECT * FROM matieres_app_corrections WHERE (login='$correction_login_eleve' AND id_groupe='$id_groupe' AND periode='$correction_periode');";
				$test_correction=mysql_query($sql);
				$test=mysql_num_rows($test_correction);
				if ($test!="0") {
					if ($app!="") {
						$sql="UPDATE matieres_app_corrections SET appreciation='$app' WHERE (login='$correction_login_eleve' AND id_groupe='$id_groupe' AND periode='$correction_periode');";
						$register=mysql_query($sql);
						if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des corrections pour $correction_nom_prenom_eleve sur la période $correction_periode.<br />";} 
						else {
							$msg.="Enregistrement de la proposition de correction pour $correction_nom_prenom_eleve sur la période $correction_periode effectué.<br />";
							$texte_mail.="Une correction proposée a été mise à jour par ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj')."\r\npour l'élève ".$correction_nom_prenom_eleve." sur la période $correction_periode\r\nen ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].").\n";
						}
					} else {
						$sql="DELETE FROM matieres_app_corrections WHERE (login='$correction_login_eleve' AND id_groupe='$id_groupe' AND periode='$correction_periode');";
						$register=mysql_query($sql);
						if (!$register) {$msg = $msg."Erreur lors de la suppression de la proposition de correction pour $correction_nom_prenom_eleve sur la période $correction_periode.<br />";} 
						else {
							$msg.="Suppression de la proposition de correction pour $correction_nom_prenom_eleve sur la période $correction_periode effectuée.<br />";
							$texte_mail.="Suppression de la proposition de correction pour l'élève $correction_nom_prenom_eleve\r\nsur la période $correction_periode en ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].")\r\npar ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj').".\n";
						}
					}
		
				}
				else {
					if ($app != "") {
						$sql="INSERT INTO matieres_app_corrections SET login='$correction_login_eleve', id_groupe='$id_groupe', periode='$correction_periode', appreciation='".$app."';";
						$register=mysql_query($sql);
						if (!$register) {$msg = $msg."Erreur lors de l'enregistrement de la proposition de correction pour $correction_nom_prenom_eleve sur la période $correction_periode.<br />";}
						else {
							$msg.="Enregistrement de la proposition de correction pour $correction_nom_prenom_eleve sur la période $correction_periode effectué.<br />";
							$texte_mail.="Une correction a été proposée par ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj')."\r\npour l'élève $correction_nom_prenom_eleve sur la période $correction_periode\r\nen ".$current_group['name']." (".$current_group["description"]." en ".$current_group["classlist_string"].").\n";
						}
					}
				}
		
				if($texte_mail!="") {
		
					$envoi_mail_actif=getSettingValue('envoi_mail_actif');
					if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
						$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
					}
		
					if($envoi_mail_actif=='y') {
						$email_destinataires="";
						$sql="select email from utilisateurs where statut='secours' AND email!='';";
						$req=mysql_query($sql);
						if(mysql_num_rows($req)>0) {
							$lig_u=mysql_fetch_object($req);
							$email_destinataires=$lig_u->email;
							while($lig_u=mysql_fetch_object($req)) {
								$email_destinataires=", ".$lig_u->email;
							}
		
							$email_declarant="";
							$nom_declarant="";
							$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
							$req=mysql_query($sql);
							if(mysql_num_rows($req)>0) {
								$lig_u=mysql_fetch_object($req);
								$nom_declarant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
								$email_declarant=$lig_u->email;
							}
		
							$email_autres_profs_grp="";
							// Recherche des autres profs du groupe
							$sql="SELECT DISTINCT u.email FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.id_groupe='$id_groupe' AND jgp.login=u.login AND u.login!='".$_SESSION['login']."' AND u.email!='';";
							//echo "$sql<br />";
							$req=mysql_query($sql);
							if(mysql_num_rows($req)>0) {
								$lig_u=mysql_fetch_object($req);
								$email_autres_profs_grp.=$lig_u->email;
								while($lig_u=mysql_fetch_object($req)) {$email_autres_profs_grp.=",".$lig_u->email;}
							}
		
							$sujet_mail="[GEPI] Demande de validation de correction d'appréciation";
			
							$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
							if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}
				
							$ajout_header="";
							if($email_declarant!="") {
								$ajout_header.="Cc: $nom_declarant <".$email_declarant.">";
								if($email_autres_profs_grp!='') {
									$ajout_header.=", $email_autres_profs_grp";
								}
								$ajout_header.="\r\n";
								$ajout_header.="Reply-to: $nom_declarant <".$email_declarant.">\r\n";
		
							}
							elseif($email_autres_profs_grp!='') {
								$ajout_header.="Cc: $email_autres_profs_grp\r\n";
							}

							$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
							$texte_mail=$salutation.",\n\n".$texte_mail."\nCordialement.\n-- \n".$nom_declarant;

							$envoi = mail($email_destinataires,
								$gepiPrefixeSujetMail.$sujet_mail,
								$texte_mail,
								"From: Mail automatique Gepi\r\n".$ajout_header."X-Mailer: PHP/".phpversion());
						}
					}	
				}
			}
		}
	}
}

if (!isset($periode_cn)) $periode_cn = 0;

$themessage = 'Des appréciations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
$utilisation_prototype = "ok";
$javascript_specifique = "saisie/scripts/js_saisie";
//**************** EN-TETE *****************
$titre_page = "Saisie des appréciations";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

$tmp_timeout=(getSettingValue("sessionMaxLength"))*60;

?>
<script type="text/javascript" language="javascript">
change = 'no';

</script>
<?php



$matiere_nom = $current_group["matiere"]["nom_complet"];

echo "<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" name='form1' method=\"post\">\n";

echo "<p class='bold'>\n";
//if ($periode_cn != 0) {
if (($periode_cn != 0)&&($_SESSION['statut']!='secours')) {
	//echo "|<a href=\"../cahier_notes/index.php?id_groupe=$id_groupe&periode_num=$periode_cn\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Retour</a>";
	echo "<a href=\"../cahier_notes/index.php?id_groupe=$id_groupe&amp;periode_num=$periode_cn\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
} else {
	echo "<a href=\"index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil saisie</a>\n";
}
//echo "|<a href='saisie_notes.php?id_groupe=$id_groupe&periode_cn=$periode_cn' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les moyennes</a>";
echo " | <a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_cn' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les moyennes</a>";
// enregistrement du chemin de retour pour la fonction imprimer
if($_SERVER['QUERY_STRING']!='') {
	$_SESSION['chemin_retour'] = $_SERVER['PHP_SELF']."?". $_SERVER['QUERY_STRING'];
}
else {
	$_SESSION['chemin_retour'] = $_SERVER['PHP_SELF']."?id_groupe=$id_groupe";
}
echo " | <a href='../prepa_conseil/index1.php?id_groupe=$id_groupe'>Imprimer</a>\n";

//=========================
// AJOUT: boireaus 20071108
echo " | <a href='index.php?id_groupe=" . $current_group["id"] . "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Import/Export notes et appréciations</a>";
//=========================

/*
// =================================
// AJOUT: boireaus
// Pour proposer de passer à la classe suivante ou à la précédente
//$sql="SELECT id, classe FROM classes ORDER BY classe";
if($_SESSION['statut']=='secours'){
	$sql = "SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
}
else
*/
//if($_SESSION['statut']=='professeur'){
if(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='secours')) {
	//$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";

	if($_SESSION['statut']=='professeur') {
		$login_prof_groupe_courant=$_SESSION["login"];
	}
	else {
		$tmp_current_group=get_group($id_groupe);

		$login_prof_groupe_courant=$tmp_current_group["profs"]["list"][0];
	}

	//$tab_groups = get_groups_for_prof($_SESSION["login"],"classe puis matière");
	$tab_groups = get_groups_for_prof($login_prof_groupe_courant,"classe puis matière");
	//$tab_groups = get_groups_for_prof($_SESSION["login"]);

	if(!empty($tab_groups)) {

		// Pour s'assurer de ne pas avoir deux fois le même groupe...
		$tmp_group=array();

		$chaine_options_classes="";

		$num_groupe=-1;
		$nb_groupes_suivies=count($tab_groups);

		//echo "count(\$tab_groups)=".count($tab_groups)."<br />";

		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		//foreach($tab_groups as $tmp_group) {
		for($loop=0;$loop<count($tab_groups);$loop++) {
			/*
			echo "\$tab_groups[$loop]['id']=".$tab_groups[$loop]['id']."<br />";
			echo "\$tab_groups[$loop]['name']=".$tab_groups[$loop]['name']."<br />";
			echo "\$tab_groups[$loop]['classlist_string']=".$tab_groups[$loop]['classlist_string']."<br />";
			*/
			// Pour s'assurer de ne pas avoir deux fois le même groupe...
			if(!in_array($tab_groups[$loop]['id'],$tmp_group)) {
				$tmp_group[]=$tab_groups[$loop]['id'];

				if($tab_groups[$loop]['id']==$id_groupe){
					$num_groupe=$loop;

					//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
					$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";

					$temoin_tmp=1;
					if(isset($tab_groups[$loop+1])){
						$id_grp_suiv=$tab_groups[$loop+1]['id'];

						//$chaine_options_classes.="<option value='".$tab_groups[$loop+1]['id']."'>".$tab_groups[$loop+1]['name']." (".$tab_groups[$loop+1]['classlist_string'].")</option>\n";
					}
					else{
						$id_grp_suiv=0;
					}
				}
				else {
					//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
					$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
				}

				if($temoin_tmp==0){
					$id_grp_prec=$tab_groups[$loop]['id'];

					//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
				}
			}
		}
		// =================================

		if(isset($id_grp_prec)){
			if($id_grp_prec!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_prec&amp;periode_cn=$periode_cn";
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignement précédent</a>";
			}
		}

		if(($chaine_options_classes!="")&&($nb_groupes_suivies>1)) {

			echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_groupe').selectedIndex=$num_groupe;
			}
		}
	}
</script>\n";

			//echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
			echo " | <select name='id_groupe' id='id_groupe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
			echo $chaine_options_classes;
			echo "</select>\n";
		}

		if(isset($id_grp_suiv)){
			if($id_grp_suiv!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_suiv&amp;periode_cn=$periode_cn";
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignement suivant</a>";
				}
		}
	}
	// =================================
}


echo "</p>\n";
if(isset($periode_cn)) {
	echo "<input type='hidden' name='periode_cn' value='$periode_cn' />\n";
}
echo "</form>\n";
?>
<form enctype="multipart/form-data" action="saisie_appreciations.php" method="post">
<?php

//=========================
// AJOUT: boireaus 20090126
$insert_mass_appreciation_type=getSettingValue("insert_mass_appreciation_type");
if ($insert_mass_appreciation_type=="y") {
	// INSERT INTO setting SET name='insert_mass_appreciation_type', value='y';

	$sql="CREATE TABLE IF NOT EXISTS b_droits_divers (login varchar(50) NOT NULL default '', nom_droit varchar(50) NOT NULL default '', valeur_droit varchar(50) NOT NULL default '');";
	$create_table=mysql_query($sql);

	// Pour tester:
	// INSERT INTO b_droits_divers SET login='toto', nom_droit='insert_mass_appreciation_type', valeur_droit='y';

	$sql="SELECT 1=1 FROM b_droits_divers WHERE login='".$_SESSION['login']."' AND nom_droit='insert_mass_appreciation_type' AND valeur_droit='y';";
	$res_droit=mysql_query($sql);
	if(mysql_num_rows($res_droit)>0) {
		$droit_insert_mass_appreciation_type="y";
	}
	else {
		$droit_insert_mass_appreciation_type="n";
	}

	if($droit_insert_mass_appreciation_type=="y") {
		echo "<div style='float:right; width:150px; border: 1px solid black; background-color: white; font-size: small; text-align:center;'>\n";
		echo "Insérer l'appréciation-type suivante pour toutes les appréciations vides: ";
		echo "<input type='text' name='ajout_a_textarea_vide' id='ajout_a_textarea_vide' value='-' size='10' /><br />\n";
		echo "<input type='button' name='ajouter_a_textarea_vide' value='Ajouter' onclick='ajoute_a_textarea_vide()' /><br />\n";
		echo "</div>\n";
	}
}
//=========================

echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";

//===========================================================
echo "<div id='div_photo_eleve' class='infobulle_corps' style='position: fixed; top: 220px; right: 20px; text-align:center; border:1px solid black; display:none;'></div>\n";
//echo "<div id='div_photo_eleve' class='infobulle_corps' style='position: fixed; top: 220px; right: 20px; text-align:center; background-color:white; border:1px solid black; display:none;'></div>\n";
//echo "<div id='div_photo_eleve' style='position: fixed; top: 220px; right: 200px; text-align:center; border:1px solid black;'>&nbsp;</div>\n";

//===========================================================
echo "<div id='div_bull_simp' style='position: absolute; top: 220px; right: 20px; width: 700px; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>\n";

	echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: 700px; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_bull_simp')\">\n";
		echo "<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>\n";
		echo "<a href='#' onClick=\"cacher_div('div_bull_simp');return false;\">\n";
		echo "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />\n";
		echo "</a>\n";
		echo "</div>\n";

		echo "<div id='titre_entete_bull_simp'></div>\n";
	echo "</div>\n";
	
	echo "<div id='corps_bull_simp' class='infobulle_corps' style='color: #ffffff; cursor: move; font-weight: bold; padding: 0px; height: 15em; width: 700px; overflow: auto;'>";
	echo "</div>\n";

echo "</div>\n";
//===========================================================

echo "<h2 class='gepi'>Bulletin scolaire - Saisie des appréciations</h2>\n";
//echo "<p><b>Groupe : " . $current_group["description"] ." | Matière : $matiere_nom</b></p>\n";
echo "<p><b>Groupe : " . htmlentities($current_group["description"]) ." (".$current_group["classlist_string"].")</b></p>\n";

if ($multiclasses) {
	echo "<p>Affichage :";
	echo "<br/>-> <a href='saisie_appreciations.php?id_groupe=$id_groupe&amp;order_by=classe'>Regrouper les élèves par classe</a>";
	echo "<br/>-> <a href='saisie_appreciations.php?id_groupe=$id_groupe&amp;order_by=nom'>Afficher la liste par ordre alphabétique</a>";
	echo "</p>\n";
}

// On commence par mettre la liste dans l'ordre souhaité
if ($order_by != "classe") {
	$liste_eleves = $current_group["eleves"]["all"]["list"];
} else {
	// Ici, on tri par classe
	// On va juste créer une liste des élèves pour chaque classe
	$tab_classes = array();
	foreach($current_group["classes"]["list"] as $classe_id) {
		$tab_classes[$classe_id] = array();
	}
	// On passe maintenant élève par élève et on les met dans la bonne liste selon leur classe
	foreach($current_group["eleves"]["all"]["list"] as $eleve_login) {
		$classe = $current_group["eleves"]["all"]["users"][$eleve_login]["classe"];
		$tab_classes[$classe][] = $eleve_login;
	}
	// On met tout ça à la suite
	$liste_eleves = array();
	foreach($current_group["classes"]["list"] as $classe_id) {
		$liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
	}
}


// Fonction de renseignement du champ qui doit obtenir le focus après validation
echo "<script type='text/javascript'>

function focus_suivant(num){
	temoin='';
	// La variable 'dernier' peut dépasser de l'effectif de la classe... mais cela n'est pas dramatique
	dernier=num+".count($liste_eleves)."
	// On parcourt les champs à partir de celui de l'élève en cours jusqu'à rencontrer un champ existant
	// (pour réussir à passer un élève qui ne serait plus dans la période)
	// Après validation, c'est ce champ qui obtiendra le focus si on n'était pas à la fin de la liste.
	for(i=num;i<dernier;i++){
		suivant=i+1;
		if(temoin==''){
			if(document.getElementById('n'+suivant)){
				document.getElementById('info_focus').value=suivant;
				temoin=suivant;
			}
		}
	}

	document.getElementById('info_focus').value=temoin;
}

</script>\n";

// ====================== Modif pour la sauvegarde en ajax =================
	$restauration = isset($_GET["restauration"]) ? $_GET["restauration"] : NULL;


	if ($restauration == NULL) {
		// On supprime les appreciation_tempo qui sont identiques aux appreciations enregistrées dans matieres_appreciations
		$sql="SELECT mat.* FROM matieres_appreciations_tempo mat, matieres_appreciations ma WHERE
			(mat.id_groupe='".$current_group["id"]."' AND mat.id_groupe=ma.id_groupe AND mat.periode=ma.periode AND mat.login=ma.login AND mat.appreciation=ma.appreciation);";
		$res_app_identiques=mysql_query($sql);
		if(mysql_num_rows($res_app_identiques)>0) {
			while($lig_app_id=mysql_fetch_object($res_app_identiques)) {
				$sql="DELETE FROM matieres_appreciations_tempo WHERE login='$lig_app_id->login' AND id_groupe='".$current_group["id"]."' AND periode='$lig_app_id->periode';";
				//echo "$sql<br />";
				$menage=mysql_query($sql);
			}
		}
	}

	// On teste s'il existe des données dans la table matieres_appreciations_tempo
	$sql_test = mysql_query("SELECT login FROM matieres_appreciations_tempo WHERE id_groupe = '" . $current_group["id"] . "'");
	$test = mysql_num_rows($sql_test);
	if ($test !== 0 AND $restauration == NULL) {
		// On envoie un message à l'utilisateur
		echo "
		<p class=\"red\">Certaines appréciations n'ont pas été enregistrées lors de votre dernière saisie.<br />
			Elles sont indiquées ci-dessous en rouge. Voulez-vous les restaurer ?
		</p>
		<p class=\"red\">
		<a href=\"./saisie_appreciations.php?id_groupe=".$current_group["id"]."&amp;restauration=oui\">OUI</a>
		(elles remplaceront alors la saisie précédente)
			-
		<a href=\"./saisie_appreciations.php?id_groupe=".$current_group["id"]."&amp;restauration=non\">NON</a>
		(elles seront alors définitivement perdues)
		</p>
		";
	}

	// Dans tous les cas, si $restauration n'est pas NULL, il faut vider la table tempo des appréciations de ce groupe

//=================================
// AJOUT: boireaus 20080201
$k=1;
$num_id = 10;
while ($k < $nb_periode) {

	$app_query = mysql_query("SELECT * FROM matieres_appreciations_grp WHERE (id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
	$app_grp[$k] = @mysql_result($app_query, 0, "appreciation");

	$call_moyenne_t[$k] = mysql_query("SELECT round(avg(n.note),1) moyenne FROM matieres_notes n, j_eleves_groupes j " .
								"WHERE (" .
								"n.id_groupe='" . $current_group["id"] ."' AND " .
								"n.login = j.login AND " .
								"n.statut='' AND " .
								"j.id_groupe = n.id_groupe AND " .
								"n.periode='$k' AND j.periode='$k'" .
								")");
	$moyenne_t[$k] = mysql_result($call_moyenne_t[$k], 0, "moyenne");

	if ($moyenne_t[$k]=='') {
		$moyenne_t[$k]="&nbsp;";
	}

	$mess[$k]="";
	$mess[$k].="<td>".$moyenne_t[$k]."</td>\n";
	$mess[$k].="<td>\n";
	//if ($current_group["classe"]["ver_periode"]['all'][$k] == 0){
	//if(($current_group["classe"]["ver_periode"]['all'][$k] == 0)||($current_group["classe"]["ver_periode"]['all'][$k] == 1)) {
	if(((($current_group["classe"]["ver_periode"]['all'][$k] == 0)||($current_group["classe"]["ver_periode"]['all'][$k] == 1))&&($_SESSION['statut']!='secours'))||
	(($current_group["classe"]["ver_periode"]['all'][$k]==0)&&($_SESSION['statut']=='secours'))) {

		//$mess[$k].=htmlentities(nl2br($app_grp[$k]));
		$mess[$k].=nl2br($app_grp[$k]);

	}
	else {
		if(!isset($id_premier_textarea)) {$id_premier_textarea=$k.$num_id;}

		$mess[$k].="<input type='hidden' name='app_grp_".$k."' value=\"".$app_grp[$k]."\" />\n";
		//$mess[$k].="<textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_grp_".$k."\" rows='2' cols='100' wrap='virtual' onchange=\"changement()\"";
		//$mess[$k].="<textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_grp_".$k."\" rows='2' cols='100' style='white-space: nowrap;' onchange=\"changement()\"";
		$mess[$k].="<textarea id=\"n".$k.$num_id."\" class='wrap' onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_grp_".$k."\" rows='2' cols='100' onchange=\"changement()\"";
		// onBlur=\"ajaxAppreciations('".$eleve_login_t[$k]."', '".$id_groupe."', 'n".$k.$num_id."');\"

		$mess[$k].=" onfocus=\"focus_suivant(".$k.$num_id.");document.getElementById('focus_courant').value='".$k.$num_id."';";
		$mess[$k].="document.getElementById('div_photo_eleve').innerHTML='';";
		$mess[$k].="\"";

		$mess[$k].=">".$app_grp[$k]."</textarea>\n";
	}

	// on affiche si besoin l'appréciation temporaire (en sauvegarde)
	//$mess[$k].=$eleve_app_t;
	$mess[$k].= "</td>\n";
	$k++;
}


echo "<table width=\"750\" class='boireaus' cellspacing=\"2\" cellpadding=\"5\" summary=\"Appréciation sur le groupe/classe\">\n";
echo "<tr>\n";
echo "<th width=\"200\"><div align=\"center\">&nbsp;</div></th>\n";
echo "<th width=\"30\"><div align=\"center\"><b>Moy.</b></div></th>\n";
echo "<th>\n";
echo "<div style='float:right; width:16;'><a href='javascript:affichage_div_photo();'><img src='../images/icons/wizard.png' width='16' height='16' alt='Afficher les quartiles et éventuellement la photo élève' title='Afficher la photo élève pendant la saisie' /></a></div>\n";
echo "<div align=\"center\"><b>Appréciation sur le groupe/classe</b>\n";
echo "</div></th>\n";
echo "</tr>\n";
//=================================================


$num_id++;
$k=1;
$alt=1;
while ($k < $nb_periode) {
	$alt=$alt*(-1);
	if ($current_group["classe"]["ver_periode"]["all"][$k] == 0) {
		echo "<tr class='lig$alt'><td><span title=\"$gepiClosedPeriodLabel\">$nom_periode[$k]</span></td>\n";
	} else {
		echo "<tr class='lig$alt'><td>$nom_periode[$k]</td>\n";
	}
	echo $mess[$k];
	$k++;
}
echo "</tr>\n";
echo "</table>\n";
echo "<br />\n";


//=================================


//=================================
$acces_bull_simp='n';
if(($_SESSION['statut']=="professeur") AND 
((getSettingValue("GepiAccesBulletinSimpleProf")=="yes")||
(getSettingValue("GepiAccesBulletinSimpleProfToutesClasses")=="yes")||
(getSettingValue("GepiAccesBulletinSimpleProfTousEleves")=="yes")
)) {
	$acces_bull_simp='y';
}
/*
if($_SESSION['statut']=="secours") {
	$acces_bull_simp='y';
}
*/
//=================================

//=================================
// 20100604
$chaine_champs_textarea_correction="";
$chaine_champs_input_correction="";
$cpt_correction=0;
//=================================


$prev_classe = null;
//=================================================
// COMMENTé: boireaus 20080201
//$num_id = 10;
//=================================================
//=========================
// AJOUT: boireaus 20071010
// Compteur pour les élèves
$i=0;
//=========================
// Pour permettre le remplacement de la chaine _PRENOM_ par le prénom de l'élève dans les commentaires types (ctp.php)
$chaine_champs_input_prenom="";
//=========================
foreach ($liste_eleves as $eleve_login) {

	$k=1;
	$temoin_photo="";

	while ($k < $nb_periode) {

		if (in_array($eleve_login, $current_group["eleves"][$k]["list"])) {
			//
			// si l'élève appartient au groupe pour cette période
			//
			$eleve_nom = $current_group["eleves"][$k]["users"][$eleve_login]["nom"];
			$eleve_prenom = $current_group["eleves"][$k]["users"][$eleve_login]["prenom"];
			$eleve_classe = $current_group["classes"]["classes"][$current_group["eleves"]["all"]["users"][$eleve_login]["classe"]]["classe"];
			$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$eleve_login]["classe"]]["id"];

			//========================
			// AJOUT boireaus 20071115
			if($k==1){
				$sql="SELECT elenoet FROM eleves WHERE login='$eleve_login';";
				$res_ele=mysql_query($sql);
				$lig_ele=mysql_fetch_object($res_ele);
				$eleve_elenoet=$lig_ele->elenoet;

				// Photo...
				$photo=nom_photo($eleve_elenoet);
				//$temoin_photo="";
				if("$photo"!=""){
					$titre="$eleve_nom $eleve_prenom";

					$texte="<div align='center'>\n";
					$texte.="<img src='../photos/eleves/".$photo."' width='150' alt=\"$eleve_nom $eleve_prenom\" />";
					$texte.="<br />\n";
					$texte.="</div>\n";

					$temoin_photo="y";

					$tabdiv_infobulle[]=creer_div_infobulle('photo_'.$eleve_login,$titre,"",$texte,"",14,0,'y','y','n','n');
				}
			}
			//========================

			$suit_option[$k] = 'yes';
			//
			// si l'élève suit la matière
			//

			$notes_conteneurs="";
			// On contrôle s'il y a des boites avec moyennes à afficher
			$sql="SELECT DISTINCT id_cahier_notes FROM cn_cahier_notes WHERE id_groupe='" . $current_group["id"] . "' AND periode='$k';";
			//if($current_group["id"]==637) {echo "$sql<br />";}
			$test_cn=mysql_query($sql);
			if(mysql_num_rows($test_cn)>0) {
				$lig_cn=mysql_fetch_object($test_cn);
				/*
				$sql="SELECT cc.nom_court, cc.nom_complet, cnc.note, cnc.statut FROM cn_conteneurs cc, cn_notes_conteneurs cnc 
					WHERE cc.id_racine='$lig_cn->id_cahier_notes' AND 
						cc.display_bulletin='1' AND 
						cc.id_racine='$lig_cn->id_cahier_notes' AND 
						cc.parent!='0' AND
						cnc.id_conteneur=cc.id AND 
						cnc.login='$eleve_login';";
				*/
				$sql="SELECT cc.nom_court, cc.nom_complet, cnc.note, cnc.statut FROM cn_conteneurs cc, cn_notes_conteneurs cnc 
					WHERE cc.id_racine='$lig_cn->id_cahier_notes' AND 
						cc.id_racine='$lig_cn->id_cahier_notes' AND 
						cc.parent!='0' AND
						cnc.id_conteneur=cc.id AND 
						cnc.login='$eleve_login';";
				//if($current_group["id"]==637) {echo "$sql<br />";}
				$test_cn_moy=mysql_query($sql);
				if(mysql_num_rows($test_cn_moy)>0) {
					$lig_cnc=mysql_fetch_object($test_cn_moy);
					//$notes_conteneurs.="<center>\n";
					//$notes_conteneurs.="<b>".ucfirst(htmlentities($lig_cnc->nom_complet))."&nbsp;:</b> ";
					$notes_conteneurs.="<b>".ucfirst(htmlentities($lig_cnc->nom_court))."&nbsp;:</b> ";
					if($lig_cnc->statut=='y') {$notes_conteneurs.=$lig_cnc->note;} else {$notes_conteneurs.=$lig_cnc->statut;}

					$cpt_cnc=1;
					while($lig_cnc=mysql_fetch_object($test_cn_moy)) {
						$notes_conteneurs.=", ";
						//$notes_conteneurs.="<b>".ucfirst(htmlentities($lig_cnc->nom_complet))."&nbsp;:</b> ";
						$notes_conteneurs.="<b>".ucfirst(htmlentities($lig_cnc->nom_court))."&nbsp;:</b> ";
						if($lig_cnc->statut=='y') {$notes_conteneurs.=$lig_cnc->note;} else {$notes_conteneurs.=$lig_cnc->statut;}
					}
					//$notes_conteneurs.="</center><br />\n";
					$notes_conteneurs.="<br />\n";
				}
			}


			if ($restauration != "oui" AND $restauration != "non") {
				// On récupère l'appréciation tempo pour la rajouter à $eleve_app
				$app_t_query = mysql_query("SELECT * FROM matieres_appreciations_tempo WHERE
					(login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
				$verif_t = mysql_num_rows($app_t_query);
				if ($verif_t != 0) {
					$eleve_app_t = "\n".'<p>Appréciation non enregistrée : <span style="color: red;">'.@mysql_result($app_t_query, 0, "appreciation").'</span></p>';
				} else {
					$eleve_app_t = '';
				}
			} else {
				$eleve_app_t = '';
			}

			// Appel des appréciations (en vérifiant si une restauration est demandée ou non)
			if ($restauration == "oui") {
				$app_query = mysql_query("SELECT * FROM matieres_appreciations_tempo WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
				// Si la sauvegarde ne donne rien pour cet élève, on va quand même voir dans la table définitive
				// (il se peut qu'il n'y ait pas d'enregistrement tempo pour cet élève)
				$verif = mysql_num_rows($app_query);
				if ($verif == 0){
					$app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
				}
			} else {
				$app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
			}
			$eleve_app = @mysql_result($app_query, 0, "appreciation");
			// Appel des notes
			$note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
			$eleve_statut = @mysql_result($note_query, 0, "statut");
			$eleve_note = @mysql_result($note_query, 0, "note");
			// Formatage de la note
			$note ="<center>";
			if ($eleve_statut != '') {
				$note .= $eleve_statut;
			} else {
				if ($eleve_note != '') {
					$note .= $eleve_note;
				} else {
					$note .= "&nbsp;";
				}
			}
			$note .= "</center>";

			$eleve_login_t[$k] = $eleve_login."_t".$k;
			//if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] != "N") {
			if ((($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]!="N")&&($_SESSION['statut']!='secours'))||
				(($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]=="O")&&($_SESSION['statut']=='secours'))) {

				//
				// si la période est verrouillée
				//
				$mess[$k] = '';
				//$mess[$k] =$mess[$k]."<td>".$note."</td>\n<td>";
				$mess[$k] =$mess[$k]."<td>".$note."</td>\n<td>";

				$mess[$k].=$notes_conteneurs;

				//===============================
				// 20100604
				//if(($_SESSION['statut']=='professeur')&&($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]=="P")) {
				if(($_SESSION['statut']=='professeur')&&($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]=="P")&&(getSettingValue('autoriser_correction_bulletin')=='y')) {

					//$mess[$k].="<div style='float:right; width:2em; height:1em;'><a href='#' onclick=\"document.getElementById('correction_login_eleve').value='$eleve_login';document.getElementById('span_correction_login_eleve').innerHTML='$eleve_login';document.getElementById('correction_periode').value='$k';document.getElementById('span_correction_periode').innerHTML='$k';document.getElementById('correction_app_eleve').value=addslashes('$eleve_app');afficher_div('div_correction','y',-100,20);return false;\" title='Proposer une correction'><img src='../images/edit16.png' width='16' height='16' alt='Proposer une correction' /></a></div>\n";

					// Il y a des pb avec la fonction javascript addslashes()... on utilise des champs de formulaire

					//$mess[$k].="<div style='float:right; width:2em; height:1em;'><a href='#' onclick=\"document.getElementById('correction_login_eleve').value='$eleve_login';document.getElementById('span_correction_login_eleve').innerHTML='$eleve_login';document.getElementById('correction_periode').value='$k';document.getElementById('span_correction_periode').innerHTML='$k';document.getElementById('correction_app_eleve').value=document.getElementById('reserve_correction_app_eleve_$cpt_correction').value;afficher_div('div_correction','y',-100,20);return false;\" title='Proposer une correction'><img src='../images/edit16.png' width='16' height='16' alt='Proposer une correction' /></a>";

					$mess[$k].="<div style='float:right; width:2em; height:1em;'><a href='#' onclick=\"affiche_div_correction('$eleve_login','$k','$cpt_correction');return false;\" alt='Proposer une correction' title='Proposer une correction'><img src='../images/edit16.png' width='16' height='16' alt='Proposer une correction' title='Proposer une correction' /></a>";

					$chaine_champs_textarea_correction.="<textarea name='reserve_correction_app_eleve_$cpt_correction' id='reserve_correction_app_eleve_$cpt_correction'>$eleve_app</textarea>\n";
					$chaine_champs_input_correction.="<input type='hidden' name='nom_prenom_eleve_$cpt_correction' id='nom_prenom_eleve_$cpt_correction' value=\"$eleve_nom $eleve_prenom\" />\n";

					$mess[$k].="</div>\n";
					$cpt_correction++;
				}
				//===============================

				if ($eleve_app != '') {
					//$mess[$k] =$mess[$k].$eleve_app;
					if((strstr($eleve_app,">"))||(strstr($eleve_app,"<"))){
						$mess[$k] =$mess[$k].$eleve_app;
					}
					else{
						$mess[$k] =$mess[$k].nl2br($eleve_app);
					}
				} else {
					$mess[$k] =$mess[$k]."&nbsp;";
				}

				$sql="SELECT * FROM matieres_app_corrections WHERE (login='$eleve_login' AND id_groupe='".$current_group["id"]."' AND periode='$k');";
				$correct_app_query=mysql_query($sql);
				if(mysql_num_rows($correct_app_query)>0) {
					$lig_correct_app=mysql_fetch_object($correct_app_query);
					$mess[$k].="<div style='color:darkgreen; border: 1px solid red;'><b>Proposition de correction en attente&nbsp;:</b><br />".nl2br($lig_correct_app->appreciation)."</div>\n";
				}


				$mess[$k] =$mess[$k]."</td>\n";
			} else {

				// Ajout Eric affichage des notes au dessus de la saisie des appréciations
				$liste_notes ='';
				// Nombre de contrôles
				//$sql="SELECT cnd.note, cd.note_sur FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE cnd.login='".$eleve_login."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group["id"]."' AND ccn.periode='$k' AND cnd.statut='';";

				$sql="SELECT cnd.note, cd.*, cc.nom_court AS nom_court_conteneur FROM 
						cn_notes_devoirs cnd, 
						cn_devoirs cd, 
						cn_cahier_notes ccn, 
						cn_conteneurs cc
					WHERE cnd.login='".$eleve_login."' AND 
						cnd.id_devoir=cd.id AND 
						cd.id_racine=ccn.id_cahier_notes AND 
						ccn.id_groupe='".$current_group["id"]."' AND 
						ccn.periode='$k' AND 
						cnd.statut='' AND
						cc.id=cd.id_conteneur
					ORDER BY cc.nom_court, cd.date;";

				//echo "\n<!--sql=$sql-->\n";
				$result_nbct=mysql_query($sql);
				$current_eleve_nbct=mysql_num_rows($result_nbct);

				// on prend les notes dans $string_notes
				$liste_notes='';
				$liste_notes_detaillees='';
				$conteneur_precedent='';
				if ($result_nbct) {
					while ($snnote=mysql_fetch_assoc($result_nbct)) {
						if ($liste_notes != '') {$liste_notes .= ", ";}
						$liste_notes.=$snnote['note'];
						if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $snnote['note_sur']!=getSettingValue("referentiel_note")) {
							$liste_notes .= "/".$snnote['note_sur'];
						}

						if($conteneur_precedent!=$snnote['nom_court_conteneur']) {
							$liste_notes_detaillees.="<p><b>".$snnote['nom_court_conteneur']."&nbsp;:</b> <br />";
							$conteneur_precedent=$snnote['nom_court_conteneur'];
						}

						//if ($liste_notes_detaillees!='') {$liste_notes_detaillees.=", ";}
						$liste_notes_detaillees.=$snnote['nom_court']."&nbsp;: ";
						$liste_notes_detaillees.=$snnote['note'];
						if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $snnote['note_sur']!=getSettingValue("referentiel_note")) {
							$liste_notes_detaillees.= "/".$snnote['note_sur'];
						}
						$liste_notes_detaillees.=" (coef&nbsp;".$snnote['coef'].")";
						$liste_notes_detaillees.=" (".formate_date($snnote['date']).")<br />";
					}
				}

				if ($current_eleve_nbct ==0) {
					$liste_notes='Pas de note dans le carnet pour cette période.';
				}

				$mess[$k]="<td>".$note."</td>\n";
				//$mess[$k].="<td>".$notes_conteneurs."Contenu du carnet de notes : ";
				$mess[$k].="<td>Contenu du carnet de notes : ";
				//$mess[$k].="<a href='#' onmouseover=\"delais_afficher_div('notes_".$eleve_login."_".$k."','y',-100,20,1000,10,10);\" onclick=\"return false;\">";
				if($liste_notes_detaillees!='') {

					$titre="Notes de $eleve_nom $eleve_prenom sur la période $k";
					$texte="";
					//$texte.="<div align='center'>\n";
					$texte.=$liste_notes_detaillees;
					//$texte.="</div>\n";
					$tabdiv_infobulle[]=creer_div_infobulle('notes_'.$eleve_login.'_'.$k,$titre,"",$texte,"",30,0,'y','y','n','n');

					$mess[$k].="<a name='".$eleve_login."_".$k."'></a>";
					//$mess[$k].="<a href='#".$eleve_login."_".$k."' onclick=\"afficher_div('notes_".$eleve_login."_".$k."','y',-100,20);get_div_size('notes_".$eleve_login."_".$k."');return false;\">";
					$mess[$k].="<a href='#".$eleve_login."_".$k."' onclick=\"afficher_div('notes_".$eleve_login."_".$k."','y',-100,-10);return false;\">";
					$mess[$k].=$liste_notes;
					$mess[$k].="</a>";
				}
				else {
					$mess[$k].=$liste_notes;
				}
				if($notes_conteneurs!='') {
					$mess[$k].="<br />\n";
					$mess[$k].=$notes_conteneurs;
				}
				//$mess[$k].="<br />\n";
				$mess[$k].="<input type='hidden' name='log_eleve_".$k."[$i]' value=\"".$eleve_login_t[$k]."\" />\n";

				//Supprimé le 07/11/2009 pour reduire le nombre de variables transmises (pb suhosin):
				//$mess[$k].="<input type='hidden' name='prenom_eleve_".$k."[$i]' id='prenom_eleve_".$k.$num_id."' value=\"".$eleve_prenom."\" />\n";
				$chaine_champs_input_prenom.="<input type='hidden' name='prenom_eleve_".$k."[$i]' id='prenom_eleve_".$k.$num_id."' value=\"".$eleve_prenom."\" />\n";

				$mess[$k].="<textarea id=\"n".$k.$num_id."\" class='wrap' onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_eleve_".$k."_".$i."\" rows='2' cols='100' onchange=\"changement()\" onBlur=\"ajaxAppreciations('".$eleve_login_t[$k]."', '".$id_groupe."', 'n".$k.$num_id."');\"";

				//==================================
				// Rétablissement: boireaus 20080219
				// Pour revenir au champ suivant après validation/enregistrement:
				// MODIF: boireaus 20080520
				//$mess[$k].=" onfocus=\"focus_suivant(".$k.$num_id.");\"";
				$mess[$k].=" onfocus=\"focus_suivant(".$k.$num_id.");document.getElementById('focus_courant').value='".$k.$num_id."';";
				//================================================
				if(getSettingValue("gepi_pmv")!="n"){
					$sql="SELECT elenoet FROM eleves WHERE login='".$eleve_login."';";
					//echo "$sql<br />";
					$res_ele=mysql_query($sql);
					if(mysql_num_rows($res_ele)>0) {
						$lig_ele=mysql_fetch_object($res_ele);
						$_photo_eleve = (isset ($multisite) AND $multisite == 'y') ? $eleve_login : $lig_ele->elenoet;
						if(file_exists("../photos/eleves/".$_photo_eleve.".jpg")) {
							$mess[$k].=";affiche_photo('".$_photo_eleve.".jpg','".addslashes(strtoupper($eleve_nom)." ".ucfirst(strtolower($eleve_prenom)))."')";
						}
						else {
							$mess[$k].="document.getElementById('div_photo_eleve').innerHTML='';";
						}
					}
					else {
						$mess[$k].="document.getElementById('div_photo_eleve').innerHTML='';";
					}
				}
				//================================================
				$mess[$k].="\"";
				//==================================

				$mess[$k].=">".$eleve_app."</textarea>\n";
				// on affiche si besoin l'appréciation temporaire (en sauvegarde)
				$mess[$k].=$eleve_app_t;
				$mess[$k].= "</td>\n";

				//=========================

			}
		}
		else {
			//
			// si l'élève n'appartient pas au groupe pour cette période.
			//
			$suit_option[$k] = 'no';
			$mess[$k] = "<td>&nbsp;</td><td><p class='small'>non suivie</p></td>\n";
		}
		$k++;
	}
	//
	//Affichage de la ligne
	//
	$display_eleve='no';
	$k=1;
	while ($k < $nb_periode) {
		if ($suit_option[$k] != 'no') {$display_eleve='yes';}
		$k++;
	}
	if ($display_eleve=='yes') {

		if ($multiclasses && $prev_classe != $eleve_classe && $order_by == 'classe') {
			if ($prev_classe != null) {
				echo "<hr style='width: 95%;'/>\n";
			}
			echo "<h3>Classe de " . $eleve_classe . "</h3>\n";
		}
		$prev_classe = $eleve_classe;
		//echo "<table width=\"750px\" border=\"1\" cellspacing=\"2\" cellpadding=\"5\">\n";
		/*
		echo "<table width=\"750\" border=\"1\" cellspacing=\"2\" cellpadding=\"5\">\n";
		echo "<tr>\n";
		echo "<td width=\"200\"><div align=\"center\">&nbsp;</div></td>\n";
		echo "<td width=\"30\"><div align=\"center\"><b>Moy.</b></div></td>\n";
		echo "<td><div align=\"center\"><b>$eleve_nom $eleve_prenom</b></div></td>\n";
		echo "</tr>\n";
		*/

		echo "<table width=\"750\" class='boireaus' cellspacing=\"2\" cellpadding=\"5\" summary=\"Tableau de $eleve_nom $eleve_prenom\">\n";
		echo "<tr>\n";
		//echo "<th width=\"200\">\n";
		echo "<th align='center' width=\"200\">\n";
		//echo "<div align=\"center\">&nbsp;</div>\n";

		$num_per1=0;
		$id_premiere_classe="";
		$current_id_classe=array();
		$sql="SELECT id_classe,periode FROM j_eleves_classes WHERE login='$eleve_login' ORDER BY periode;";
		$res_classe=mysql_query($sql);
		if(mysql_num_rows($res_classe)>0) {
			$lig_classe=mysql_fetch_object($res_classe);
			$id_premiere_classe=$lig_classe->id_classe;
			$current_id_classe[$lig_classe->periode]=$lig_classe->id_classe;
			$num_per1=$lig_classe->periode;
			$num_per2=$num_per1;
			while($lig_classe=mysql_fetch_object($res_classe)) {
				$current_id_classe[$lig_classe->periode]=$lig_classe->id_classe;
				$num_per2=$lig_classe->periode;
			}
		}

		if(($id_premiere_classe!='')&&($acces_bull_simp=='y')) {
			//echo "<div style='float:right; width: 17px; margin-right: 1px;'>\n";
			echo "<a href=\"#\" onclick=\"afficher_div('div_bull_simp','y',-100,40); affiche_bull_simp('$eleve_login','$id_premiere_classe','$num_per1','$num_per2');return false;\">";
			echo "<img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='Bulletin simple toutes périodes en infobulle' title='Bulletin simple toutes périodes en infobulle' />";
			echo "</a>";
			//echo "</div>\n";
		}

		echo "</th>\n";
		echo "<th width=\"30\"><div align=\"center\"><b>Moy.</b></div></th>\n";
		echo "<th>\n";

		echo "<div align=\"center\"><b>$eleve_nom $eleve_prenom</b>\n";

		//==========================
		// AJOUT: boireaus 20071115
		// Lien photo...
		if($temoin_photo=="y"){
			//echo " <a href='#' onmouseover=\"afficher_div('photo_$eleve_login','y',-100,20);\"";
			echo " <a href='#' onmouseover=\"delais_afficher_div('photo_$eleve_login','y',-100,20,1000,10,10);\"";
			echo " onclick=\"afficher_div('photo_$eleve_login','y',-100,20); return false;\"";
			echo ">";
			echo "<img src='../images/icons/buddy.png' alt='$eleve_nom $eleve_prenom' />";
			echo "</a>";
		}
		//==========================

		/*

			$titre=$v_eleve_nom_prenom1;
			$texte="<div align='center'>\n";
			$photo=nom_photo($v_elenoet1);
			if("$photo"!=""){
				$texte.="<img src='../photos/eleves/".$photo."' width='150' alt=\"$v_eleve_nom_prenom1\" />";
				$texte.="<br />\n";
			}
			$texte.="</div>\n";

			$tabdiv_infobulle[]=creer_div_infobulle('info_popup_eleve1',$titre,"",$texte,"",14,0,'y','y','n','n')
		*/

		echo "</div></th>\n";
		echo "</tr>\n";


		$num_id++;
		$k=1;
		$alt=1;
		while ($k < $nb_periode) {

			/*
			$current_id_classe="";
			$sql="SELECT id_classe FROM j_eleves_classes WHERE login='$eleve_login' AND periode='$k';";
			$res_classe=mysql_query($sql);
			if(mysql_num_rows($res_classe)>0) {
				$lig_classe=mysql_fetch_object($res_classe);
				$current_id_classe=$lig_classe->id_classe;
			}
			*/

			$alt=$alt*(-1);
			if ($current_group["classe"]["ver_periode"]["all"][$k] == 0) {
				echo "<tr class='lig$alt'><td><span title=\"$gepiClosedPeriodLabel\">\n";

				echo $nom_periode[$k];

				//if($current_id_classe!='') {
				if((isset($current_id_classe[$k]))&&($acces_bull_simp=='y')) {
					//echo "<a href=\"#\" onclick=\"afficher_div('div_bull_simp','y',-100,20); affiche_bull_simp('$eleve_login','$current_id_classe','$k','$k');return false;\">";
					echo " <a href=\"#\" onclick=\"afficher_div('div_bull_simp','y',-100,20); affiche_bull_simp('$eleve_login','$current_id_classe[$k]','$k','$k');return false;\" alt='Bulletin simple en infobulle' title='Bulletin simple en infobulle'>";
					//echo $nom_periode[$k];
					echo "<img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='Bulletin simple de la période en infobulle' title='Bulletin simple de la période en infobulle' />";
					echo "</a>";
				}
				//else {
				//	echo $nom_periode[$k];
				//}
				echo "</span>\n";
				echo "</td>\n";
			}
			else {
				echo "<tr class='lig$alt'><td>\n";

				echo $nom_periode[$k];

				//if($current_id_classe!='') {
				if((isset($current_id_classe[$k]))&&($acces_bull_simp=='y')) {
					//echo "<a href=\"#\" onclick=\"afficher_div('div_bull_simp','y',-100,40); affiche_bull_simp('$eleve_login','$current_id_classe','$k','$k');return false;\">";
					echo "<a href=\"#\" onclick=\"afficher_div('div_bull_simp','y',-100,40); affiche_bull_simp('$eleve_login','$current_id_classe[$k]','$k','$k');return false;\" alt='Bulletin simple en infobulle' title='Bulletin simple en infobulle'>";
					//echo "<a href=\"../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=".$eleve_login."&id_classe=$current_id_classe&periode1=$k&periode2=$k\" onclick=\"affiche_bull_simp('$eleve_login','$current_id_classe','$k','$k');return false;\" target=\"_blank\">";
					//echo $nom_periode[$k];
					echo " <img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='Bulletin simple de la période en infobulle' title='Bulletin simple de la période en infobulle' />";
					echo "</a>";
				}
				//else {
				//	echo $nom_periode[$k];
				//}

				echo "</td>\n";
			}
			echo $mess[$k];
			$k++;
		}
		echo "</tr>\n";
		//echo"</table>\n<p></p>";
		echo "</table>\n";
		//echo"<p>&nbsp;</p>\n";
		//echo"<p></p>\n";
		echo "<br />\n";
	}
	$i++;

}

echo "<input type='hidden' name='indice_max_log_eleve' value='$i' />\n";
?>
<input type="hidden" name="is_posted" value="yes" />
<input type="hidden" name="id_groupe" value="<?php echo "$id_groupe";?>" />
<input type="hidden" name="periode_cn" value="<?php echo "$periode_cn";?>" />
<center><div id="fixe"><input type="submit" value="Enregistrer" /><br />

<!-- DIV destiné à afficher un décompte du temps restant pour ne pas se faire piéger par la fin de session -->
<div id='decompte'></div>

<?php
	//============================================
	// AJOUT: boireaus 20080520
	// Dispositif spécifique: décommenter la ligne pour l'activer
	if(getSettingValue('appreciations_types_profs')=='y' || getSettingValue('appreciations_types_profs')=='yes') {include('ctp.php');}
	//============================================
?>

<!-- Champ destiné à recevoir la valeur du champ suivant celui qui a le focus pour redonner le focus à ce champ après une validation -->
<input type='hidden' id='info_focus' name='champ_info_focus' value='' />
<input type='hidden' id='focus_courant' name='focus_courant' value='' />
</div></center>
</form>

<?php



echo "<script type='text/javascript'>
	/*
	function get_div_size(id_div) {
		if(document.getElementById(id_div)) {
			alert(document.getElementById(id_div).style.top);
			alert(document.getElementById(id_div).style.height);
		}
	}
	*/

	// <![CDATA[
	function affiche_bull_simp(login_eleve,id_classe,num_per1,num_per2) {
		document.getElementById('titre_entete_bull_simp').innerHTML='Bulletin simplifié de '+login_eleve+' période '+num_per1+' à '+num_per2;
		new Ajax.Updater($('corps_bull_simp'),'ajax_edit_limite.php?choix_edit=2&login_eleve='+login_eleve+'&id_classe='+id_classe+'&periode1='+num_per1+'&periode2='+num_per2,{method: 'get'});
	}
	//]]>
</script>\n";


//===========================================================================

$aff_photo_par_defaut=getPref($_SESSION['login'],'aff_photo_saisie_app',"n");

echo "<script type='text/javascript'>
function affichage_div_photo() {
	if(document.getElementById('div_photo_eleve').style.display=='none') {
		document.getElementById('div_photo_eleve').style.display='';
	}
	else {
		document.getElementById('div_photo_eleve').style.display='none';
	}
}

function affiche_photo(photo,nom_prenom) {
	document.getElementById('div_photo_eleve').innerHTML='<img src=\"../photos/eleves/'+photo+'\" width=\"150\" alt=\"Photo\" /><br />'+nom_prenom;
}
";
if($aff_photo_par_defaut=='y') {
	echo "affichage_div_photo();\n";
}
//echo "affichage_div_photo();\n";
echo "</script>\n";


// =======================
// 20100604
if(($_SESSION['statut']=='professeur')&&(getSettingValue('autoriser_correction_bulletin')=='y')) {
	$titre="Correction d'une appréciation";
	$texte="<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" name='form_correction' method=\"post\">\n";
	$texte.="Vous pouvez proposer une correction pour <span id='span_correction_login_eleve' class='bold'>...</span> sur la période <span id='span_correction_periode' class='bold'>...</span>&nbsp;: ";
	$texte.="<input type='hidden' name='correction_login_eleve' id='correction_login_eleve' value='' />\n";
	$texte.="<input type='hidden' name='correction_periode' id='correction_periode' value='' />\n";
	$texte.="<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
	$texte.="<textarea class='wrap' name=\"no_anti_inject_correction_app_eleve\" id='correction_app_eleve' rows='2' cols='70'></textarea><br />";
	$texte.="<input type='submit' name='soumettre_correction' value='Soumettre la correction' />\n";
	$texte.="</form>\n";
	$tabdiv_infobulle[]=creer_div_infobulle('div_correction',$titre,"",$texte,"",40,0,'y','y','n','n');
	echo "<script type='text/javascript'>

function affiche_div_correction(eleve_login,num_periode,num_eleve) {
	document.getElementById('correction_login_eleve').value=eleve_login;
	document.getElementById('span_correction_login_eleve').innerHTML=document.getElementById('nom_prenom_eleve_'+num_eleve).value;
	document.getElementById('correction_periode').value=num_periode;
	document.getElementById('span_correction_periode').innerHTML=num_periode;
	document.getElementById('correction_app_eleve').value=document.getElementById('reserve_correction_app_eleve_'+num_eleve).value;
	afficher_div('div_correction','y',-100,20)
}

/*
// Ca ne fonctionne pas correctement.
function addslashes (str) {
    // Escapes single quote, double quotes and backslash characters in a string with backslashes  
    // 
    // version: 1004.2314
    // discuss at: http://phpjs.org/functions/addslashes    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Ates Goral (http://magnetiq.com)
    // +   improved by: marrtins
    // +   improved by: Nate
    // +   improved by: Onno Marsman    // +   input by: Denny Wardhana
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Oskar Larsson Högfeldt (http://oskar-lh.name/)
    // *     example 1: addslashes(\"kevin's birthday\");
    // *     returns 1: 'kevin\'s birthday' 
    return (str+'').replace(/[\\\\\"']/g, '\\\\$&').replace(/\\u0000/g, '\\\\0');
}
*/
</script>\n";

	// Formulaire caché destiné à faire des copies vers no_anti_inject_correction_app_eleve
	echo "<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" name='form_correction_reserve' method=\"post\" style='display:none'>\n";
	echo $chaine_champs_textarea_correction;
	echo $chaine_champs_input_correction;
	echo "</form>\n";
}
// =======================

// ====================== DISPOSITIF CTP ========================
// Pour permettre le remplacement de la chaine _PRENOM_ par le prénom de l'élève dans les commentaires types (ctp.php)
// Les champs INPUT des prénoms sont insérés hors du formulaire principal pour éviter d'envoyer trop de champs lors du submit (problèmes avec suhosin qui limite le nombre de champs pouvant être POSTés)
echo "<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" name='form2' method=\"post\">\n";
echo $chaine_champs_input_prenom;
echo "</form>\n";
// ==============================================================

// ====================== SYSTEME  DE SAUVEGARDE ========================
// Dans tous les cas, suite à une demande de restauration, et quelle que soit la réponse, les sauvegardes doivent être effacées
if ($restauration == "oui" OR $restauration == "non") {
	$effacer = mysql_query("DELETE FROM matieres_appreciations_tempo WHERE id_groupe = '".$id_groupe."'")
	OR DIE('Erreur dans l\'effacement de la table temporaire (2) : '.mysql_error());
}
// Il faudra permettre de n'afficher ce décompte que si l'administrateur le souhaite.

echo "<script type='text/javascript'>
cpt=".$tmp_timeout.";
compte_a_rebours='y';

function decompte(cpt){
	if(compte_a_rebours=='y'){
		document.getElementById('decompte').innerHTML=cpt;
		if(cpt>0){
			cpt--;
		}

		setTimeout(\"decompte(\"+cpt+\")\",1000);
	}
	else{
		document.getElementById('decompte').style.display='none';
	}
}

decompte(cpt);

";

// Après validation, on donne le focus au champ qui suivait celui qui vient d'être rempli
if(isset($_POST['champ_info_focus'])){
	if($_POST['champ_info_focus']!=""){
		echo "// On positionne le focus...
	document.getElementById('n".$_POST['champ_info_focus']."').focus();
\n";
	}
}
elseif(isset($id_premier_textarea)) {
	echo "if(document.getElementById('n".$id_premier_textarea."')) {document.getElementById('n".$id_premier_textarea."').focus();}
if(document.getElementById('focus_courant')) {document.getElementById('focus_courant').value='$id_premier_textarea';}";
}

echo "</script>\n";

//=========================
// AJOUT: boireaus 20090126
if (($insert_mass_appreciation_type=="y")&&($droit_insert_mass_appreciation_type=="y")) {
	echo "<script type='text/javascript'>
	function ajoute_a_textarea_vide() {
		champs_textarea=document.getElementsByTagName('textarea');
		alert('champs_textarea.length='+champs_textarea.length);
		for(i=0;i<champs_textarea.length;i++){
			if(champs_textarea[i].value=='') {
				champs_textarea[i].value=document.getElementById('ajout_a_textarea_vide').value;
			}
		}
	}
</script>\n";
}
//=========================

?>
<p><br /></p>
<?php require("../lib/footer.inc.php");?>
