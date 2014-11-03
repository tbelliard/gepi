<?php
/*
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


unset($retour_cn);
$retour_cn = isset($_POST["retour_cn"]) ? $_POST["retour_cn"] : (isset($_GET["retour_cn"]) ? $_GET["retour_cn"] : NULL);

$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
	$current_group = get_group($id_groupe);
} else {
	$current_group = false;
}
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "classe");
if (count($current_group["classes"]["list"]) > 1) {
	$multiclasses = true;
} else {
	$multiclasses = false;
	$order_by = "nom";
}


include "../lib/periodes.inc.php";

if ($_SESSION['statut'] != "secours") {
	if (!(check_prof_groupe($_SESSION['login'],$current_group["id"]))) {
		$mess=rawurlencode("Vous n'êtes pas professeur de cet enseignement !");
		header("Location: index.php?msg=$mess");
		die();
	}
}


if (isset($is_posted) and ($is_posted == 'yes')) {
	check_token();

	$k=$periode_cn;

	$acces_exceptionnel_saisie=false;
	if($_SESSION['statut']=='professeur') {
		$acces_exceptionnel_saisie=acces_exceptionnel_saisie_bull_note_groupe_periode($id_groupe, $periode_cn);
	}

	//=========================
	// AJOUT: boireaus 20071010
	$log_eleve=$_POST['log_eleve_'.$k];
	$note_eleve=$_POST['note_eleve_'.$k];
	//=========================

	$indice_max_log_eleve=$_POST['indice_max_log_eleve'];

	//for($i=0;$i<count($log_eleve);$i++){
	for($i=0;$i<$indice_max_log_eleve;$i++){

		if(isset($log_eleve[$i])) {
			// La période est-elle ouverte?
			$reg_eleve_login=$log_eleve[$i];

			if (in_array($reg_eleve_login, $current_group["eleves"][$k]["list"])) {
				$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$reg_eleve_login]["classe"]]["id"];
				//if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] == "N") {
				if (($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] == "N")||
					($acces_exceptionnel_saisie)||
					(($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]!="O")&&($_SESSION['statut']=='secours'))) {

					$loguer_modif=false;
					if(($_SESSION['statut']=='professeur')&&($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] != "N")) {
						$loguer_modif=true;
					}

					$note=$note_eleve[$i];

					$elev_statut = '';
					if (($note == 'disp')) {
						$note = '0';
						$elev_statut = 'disp';
					}
					else if (($note == 'abs')) {
						$note = '0';
						$elev_statut = 'abs';
					}
					else if (($note == '-')) {
						$note = '0';
						$elev_statut = '-';
					}
					else if (preg_match("/^[0-9\.\,]{1,}$/", $note)) {
						$note = str_replace(",", ".", "$note");
						if (($note < 0) or ($note > 20)) {
							$note = '';
							$elev_statut = '';
						}
					}
					else {
						$note = '';
						$elev_statut = '';
					}

					if (($note != '') or ($elev_statut != '')) {
						$test_eleve_note_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_notes WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"] . "' AND periode='$k')");
						$test = mysqli_num_rows($test_eleve_note_query);
						if ($test != "0") {
							if($loguer_modif) {
								// On récupère la note précédente de l'élève
								$lig_old_note_ele=mysqli_fetch_object($test_eleve_note_query);

								if(($lig_old_note_ele->note!=$note)||($lig_old_note_ele->statut!=$elev_statut)) {
									$texte="Modification de note du bulletin en période $k pour ".get_nom_prenom_eleve($reg_eleve_login, 'avec_classe')." : ";
									if(($lig_old_note_ele->statut!="")) {
										$texte.=$lig_old_note_ele->statut." -> ";
									}
									else {
										$texte.=$lig_old_note_ele->note." -> ";
									}
									if($elev_statut!="") {
										if($elev_statut=="v") {
											$texte.="(vide)";
										}
										else {
											$texte.=$elev_statut;
										}
									}
									else {
										$texte.=$note;
									}
									$texte.=".";
									$retour=log_modifs_acces_exceptionnel_saisie_bull_note_groupe_periode($id_groupe, $periode_cn, $texte);
								}
							}

							$register = mysqli_query($GLOBALS["mysqli"], "UPDATE matieres_notes SET note='$note',statut='$elev_statut', rang='0' WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"] . "' AND periode='$k')");
							$modif[$k] = 'yes';
						} else {

							if($loguer_modif) {
								$texte="Saisie de note sur le bulletin en période $k pour ".get_nom_prenom_eleve($reg_eleve_login, 'avec_classe')." : ";
								if(($elev_statut!="")) {
									if($elev_statut=="v") {
										$texte.="(vide)";
									}
									else {
										$texte.=$elev_statut;
									}
								}
								else {
									$texte.=$note;
								}
								$texte.=".\n";
								$retour=log_modifs_acces_exceptionnel_saisie_bull_note_groupe_periode($id_groupe, $periode_cn, $texte);
							}

							$register = mysqli_query($GLOBALS["mysqli"], "INSERT INTO matieres_notes SET login='$reg_eleve_login', id_groupe='" . $current_group["id"] . "',periode='$k',note='$note',statut='$elev_statut', rang='0'");
							$modif[$k] = 'yes';
						}
					} else {
						if($loguer_modif) {
							$test_eleve_note_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_notes WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"] . "' AND periode='$k')");
							$test = mysqli_num_rows($test_eleve_note_query);
							if ($test != "0") {
								$texte="Suppression de note sur le bulletin en période $k pour ".get_nom_prenom_eleve($reg_eleve_login, 'avec_classe')." : ";
								if(($elev_statut!="")) {
									if($elev_statut=="v") {
										$texte.="(vide)";
									}
									else {
										$texte.=$elev_statut;
									}
								}
								else {
									$texte.=$note;
								}
								$texte.=".\n";
								$retour=log_modifs_acces_exceptionnel_saisie_bull_note_groupe_periode($id_groupe, $periode_cn, $texte);
							}
						}

						$register = mysqli_query($GLOBALS["mysqli"], "DELETE FROM matieres_notes WHERE (login='$reg_eleve_login' and id_groupe='" . $current_group["id"] . "' and periode='$k')");
						$modif[$k] = 'yes';
					}
				}
			}
		}
	}

	/*
	foreach ($current_group["eleves"]["all"]["list"] as $reg_eleve_login) {
		// MODIFICATION: boireaus
		// On n'enregistre que pour la période correspondant à $periode_cn
		//$k=1;
		$k=$periode_cn;
		//while ($k < $nb_periode) {
			if (in_array($reg_eleve_login, $current_group["eleves"][$k]["list"])) {
				$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$reg_eleve_login]["classe"]]["id"];
				if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] == "N"){
					$nom_log = $reg_eleve_login."_t".$k;
					$note = $$nom_log;
					$elev_statut = '';
					if (($note == 'disp')) { $note = '0'; $elev_statut = 'disp';
					} else if (($note == 'abs')) { $note = '0'; $elev_statut = 'abs';
					} else if (($note == '-')) { $note = '0'; $elev_statut = '-';
					} else if (ereg ("^[0-9\.\,]{1,}$", $note)) {
						$note = str_replace(",", ".", "$note");
						if (($note < 0) or ($note > 20)) { $note = ''; $elev_statut = '';}
					} else {
						$note = ''; $elev_statut = '';
					}
					if (($note != '') or ($elev_statut != '')) {
						$test_eleve_note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"] . "' AND periode='$k')");
						$test = mysql_num_rows($test_eleve_note_query);
						if ($test != "0") {
							$register = mysql_query("UPDATE matieres_notes SET note='$note',statut='$elev_statut', rang='0' WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"] . "' AND periode='$k')");
							$modif[$k] = 'yes';
						} else {
							$register = mysql_query("INSERT INTO matieres_notes SET login='$reg_eleve_login', id_groupe='" . $current_group["id"] . "',periode='$k',note='$note',statut='$elev_statut', rang='0'");
							$modif[$k] = 'yes';
						}
					} else {
						$register = mysql_query("DELETE FROM matieres_notes WHERE (login='$reg_eleve_login' and id_groupe='" . $current_group["id"] . "' and periode='$k')");
						$modif[$k] = 'yes';
					}
				}
			}

			//$k++;
		//}
	}
	*/

	// on indique qu'il faut le cas échéant procéder à un recalcul du rang des élèves
	//$k=1;
	$k=$periode_cn;
	//while ($k < $nb_periode) {
		if (isset($modif[$k]) and ($modif[$k] == 'yes')) {
			$recalcul_rang = sql_query1("select recalcul_rang from groupes
			where id='".$current_group["id"]."' limit 1");
			$long = mb_strlen($recalcul_rang);
			if ($long >= $k) {
				$recalcul_rang = substr_replace ( $recalcul_rang, "y", $k-1, $k);
			} else {
				for ($l = $long; $l<$k; $l++) {
					$recalcul_rang = $recalcul_rang.'y';
				}
			}
			$req = mysqli_query($GLOBALS["mysqli"], "update groupes set recalcul_rang = '".$recalcul_rang."'
			where id='".$current_group["id"]."'");
		}
		//$k++;
	//}


	$affiche_message = 'yes';
}
if (!isset($is_posted)) {$is_posted = '';}
$themessage  = 'Des notes ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
//**************** EN-TETE *****************
$titre_page = "Saisie des moyennes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

// Couleurs utilisées
$couleur_devoirs = '#AAE6AA';
$couleur_fond = '#AAE6AA';
$couleur_moy_cn = '#96C8F0';

$acces_visu_eleve=acces('/eleves/visu_eleve.php', $_SESSION['statut']);

if (!isset($periode_cn)) {$periode_cn = 0;}

//echo "\$periode_cn=$periode_cn<br />\n";

if($periode_cn==0){
	//echo "A";
	foreach($current_group["classes"]["classes"] as $classe){
		//echo "B";
		if($periode_cn==0){
			//echo "C";
			for($i=1;$i<=count($current_group["classe"]["ver_periode"][$classe['id']]);$i++){
				//echo "$i";
				//if($current_group["classe"]["ver_periode"][$classe['id']][$i]=="N"){
				if (($current_group["classe"]["ver_periode"][$classe['id']][$i]=="N")||
					(($current_group["classe"]["ver_periode"][$classe['id']][$i]!="O")&&($_SESSION['statut']=='secours'))) {
					$periode_cn=$i;
					//echo "\$periode_cn=$i<br />";
					break;
				}
			}
		}
		else{
			break;
		}
	}

	// Si jamais aucune période n'est ouverte:
	if($periode_cn==0){
		$periode_cn=1;
	}
}
/*
// appel du carnet de notes
if ($periode_cn != 0) {
	$login_prof = $_SESSION['login'];
	$appel_cahier_notes = mysql_query("SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe = '" . $current_group["id"] . "' and periode='$periode_cn')");
	$id_racine = @old_mysql_result($appel_cahier_notes, 0, 'id_cahier_notes');

}
*/
// appel du carnet de notes
if ($periode_cn != 0) {
	$login_prof = $_SESSION['login'];

	// On teste si la première classe du groupe a bien la période $periode_cn (on ne peut pas associer un groupe a des classes qui n'ont pas le même nombre de périodes)
	$sql="SELECT 1=1 FROM periodes WHERE (id_classe='".$current_group["classes"]["list"][0]."' and num_periode='$periode_cn');";
	//echo "$sql<br />";
	$test_periode_premiere_classe_du_groupe=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_periode_premiere_classe_du_groupe)==0) {
		// En passant à enseignement suivant, il peut arriver que l'on passe d'un enseignement à trois périodes à un enseignement à 2 périodes.
		// Si on arrive sur l'enseignement à deux périodes avec un periode_cn=3, on obtient des erreurs

		$sql="SELECT num_periode FROM periodes p, j_groupes_classes jgc WHERE p.verouiller='N' AND jgc.id_classe=p.id_classe AND jgc.id_groupe='".$current_group["id"]."' ORDER BY num_periode LIMIT 1;";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0) {
			$lig_tmp=mysqli_fetch_object($test);
			$periode_cn=$lig_tmp->num_periode;
		}
		else {
			$periode_cn=1;
		}
	}

	// On récupére, si le cahier de notes est initialisé l'identifiant du cahier de notes.
	$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe = '" . $current_group["id"] . "' and periode='$periode_cn');";
	//echo "$sql<br />";
	$appel_cahier_notes = mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($appel_cahier_notes)>0) {
		$id_racine = old_mysql_result($appel_cahier_notes, 0, 'id_cahier_notes');
	}

	//$id_racine = @old_mysql_result($appel_cahier_notes, 0, 'id_cahier_notes');
}



$matiere_nom = $current_group["matiere"]["nom_complet"];

$acces_exceptionnel_saisie=array();
$i = 1;
while ($i < $nb_periode) {
	$acces_exceptionnel_saisie[$i]=false;
	$i++;
}
	
if($_SESSION['statut']=='professeur') {
	$acces_exceptionnel_saisie[$periode_cn]=acces_exceptionnel_saisie_bull_note_groupe_periode($id_groupe, $periode_cn);
}

$affiche_bascule = 'no';
$i = 1;
/*
while ($i < $nb_periode) {
	if (($current_group["classe"]["ver_periode"]["all"][$i] >= 2) and ($periode_cn == $i)) $affiche_bascule = 'yes';
	$i++;
}
*/
//if ($current_group["classe"]["ver_periode"]["all"][$periode_cn]!=0) {
//if ($current_group["classe"]["ver_periode"]["all"][$periode_cn]>=2) {
if (($current_group["classe"]["ver_periode"]["all"][$periode_cn]>=2)||
	($acces_exceptionnel_saisie[$periode_cn])||
	(($current_group["classe"]["ver_periode"]["all"][$periode_cn]!=0)&&($_SESSION['statut']=='secours'))) {
	$affiche_bascule = 'yes';
}

echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" name='form1' method=\"post\">\n";

echo "<p class='bold'>\n";
if (isset($retour_cn)) {
	echo "<a href=\"../cahier_notes/index.php?id_groupe=" . $current_group["id"] . "&amp;periode_num=$periode_cn\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour vers mes évaluations</a>";
} else {
	echo "<a href=\"index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil saisie</a>";
}
echo " | <a href='saisie_appreciations.php?id_groupe=" . $current_group["id"] . "&amp;periode_cn=$periode_cn' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les appréciations</a>";
// enregistrement du chemin de retour pour la fonction imprimer
$_SESSION['chemin_retour'] = $_SERVER['PHP_SELF']."?". $_SERVER['QUERY_STRING'];
echo " | <a href='../prepa_conseil/index1.php?id_groupe=$id_groupe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Imprimer</a>";

//=========================
echo " | <a href='index.php?id_groupe=" . $current_group["id"] . "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Import/Export notes et appréciations</a> | ";
//=========================

if(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='secours')) {

	$login_prof_groupe_courant="";
	$tab_groups=array();
	if($_SESSION['statut']=='professeur') {
		$login_prof_groupe_courant=$_SESSION["login"];
	}
	else {
		$tmp_current_group=get_group($id_groupe);

		if(isset($tmp_current_group["profs"]["list"][0])) {
			$login_prof_groupe_courant=$tmp_current_group["profs"]["list"][0];
		}
	}

	if($login_prof_groupe_courant!='') {
		$tab_groups = get_groups_for_prof($login_prof_groupe_courant,"classe puis matière");
	}

	if(!empty($tab_groups)) {

		$chaine_options_classes="";

		$tmp_groups=array();
		for($loop=0;$loop<count($tab_groups);$loop++) {
			if((!isset($tab_groups[$loop]["visibilite"]["bulletins"]))||($tab_groups[$loop]["visibilite"]["bulletins"]=='y')) {
				$tmp_groups[]=$tab_groups[$loop];
			}
		}

		$num_groupe=-1;
		$nb_groupes_suivies=count($tmp_groups);

		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		for($loop=0;$loop<count($tmp_groups);$loop++) {
			if((!isset($tmp_groups[$loop]["visibilite"]["bulletins"]))||($tmp_groups[$loop]["visibilite"]["bulletins"]=='y')) {
				if($tmp_groups[$loop]['id']==$id_groupe){
					$num_groupe=$loop;

					$chaine_options_classes.="<option value='".$tmp_groups[$loop]['id']."' selected='true'>".$tmp_groups[$loop]['description']." (".$tmp_groups[$loop]['classlist_string'].")</option>\n";
	
					$temoin_tmp=1;
					if(isset($tmp_groups[$loop+1])){
						$id_grp_suiv=$tmp_groups[$loop+1]['id'];
					}
					else{
						$id_grp_suiv=0;
					}
				}
				else {
					$chaine_options_classes.="<option value='".$tmp_groups[$loop]['id']."'>".$tmp_groups[$loop]['description']." (".$tmp_groups[$loop]['classlist_string'].")</option>\n";
				}
	
				if($temoin_tmp==0){
					$id_grp_prec=$tmp_groups[$loop]['id'];
				}
			}
		}
		// =================================

		if(isset($id_grp_prec)){
			if($id_grp_prec!=0){
				echo "<a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_prec&amp;periode_cn=$periode_cn";
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\" title='Enseignement précédent'><img src='../images/icons/back.png' class='icone16' alt='Enseignement précédent' /></a>\n";
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

			echo " <select name='id_groupe' id='id_groupe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
			echo $chaine_options_classes;
			echo "</select>\n";
		}

		if(isset($id_grp_suiv)){
			if($id_grp_suiv!=0){
				echo " <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_suiv&amp;periode_cn=$periode_cn";
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\" title='Enseignement suivant'><img src='../images/icons/forward.png' class='icone16' alt='Enseignement suivant' /></a>";
			}
		}
	}
	// =================================
}

if($_SESSION['statut']=='professeur') {
	echo " | <a href=\"../groupes/signalement_eleves.php?id_groupe=$id_groupe&amp;chemin_retour=../cahier_notes/index.php?id_groupe=$id_groupe\" title=\"Si certains élèves sont affectés à tort dans cet enseignement, ou si il vous manque certains élèves, vous pouvez dans cette page signaler l'erreur à l'administrateur Gepi.\"> Signaler des erreurs d'affectation <img src='../images/icons/ico_attention.png' class='icone16' alt='Erreur' /></a>";
}

echo "</p>\n";
if(isset($periode_cn)) {
	echo "<input type='hidden' name='periode_cn' value='$periode_cn' />\n";
}
echo "</form>\n";


echo "<h2 class='gepi'>Bulletin scolaire - Saisie des moyennes</h2>\n";

echo "<script type=\"text/javascript\" language=\"javascript\">\n";
if (($affiche_bascule == 'yes') and ($is_posted == 'bascule')) {echo "change = 'yes';";} else {echo "change = 'no';";}
echo "</script>\n";


if((isset($current_group["visibilite"]["bulletins"]))&&($current_group["visibilite"]["bulletins"]!='y')) {
	echo "<p style='color:red; text-indent:-7em;margin-left:7em;'><strong>ANOMALIE&nbsp;:</strong> Vous ne devriez pas saisir de notes pour les bulletins.<br />L'enseignement courant est marqué comme n'apparaissant pas sur les bulletins.<br />Si vous y saisissez des notes, elles seront inexploitables.</p>";
}

//echo "<table  border=\"0\">\n";
if ($affiche_bascule == 'yes') {

	echo "<div id='div_bascule'>\n";

	//if ($id_racine == '') echo "<tr><td></td><td><font color=\"#FF0000\">Actuellement, vous n'utilisez pas le cahier de notes. Il n'y a donc aucune note à importer.</font></td></tr>\n";
	if ($id_racine == '') {echo "<font color=\"#FF0000\">Actuellement, vous n'utilisez pas le cahier de notes. Il n'y a donc aucune note à importer.</font>\n";}

	echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=\"post\">\n";
	echo add_token_field();
	if ($is_posted != 'bascule') {
		//echo "<tr><td><input type=\"submit\" value=\"Recopier\"></td><td> : Recopier la colonne \"carnet de notes\" dans la colonne \"bulletin\"</td></tr>\n";
		echo "<input type=\"submit\" value=\"Recopier\" /> : Recopier la colonne \"carnet de notes\" dans la colonne \"bulletin\"\n";
		echo "<input type=\"hidden\" name=\"is_posted\" value=\"bascule\" />\n";
	} 
	else {
		// Si une Recopie a été effectuée ou provoquée, le token doit être correct.
		check_token();

		//echo "<tr><td><input type=\"submit\" value=\"Annuler recopie\"></td><td> : Afficher dans la colonne \"bulletin\" les moyennes actuellement enregistrées</td></tr>\n";
		echo "<input type=\"submit\" value=\"Annuler recopie\" /> : Afficher dans la colonne \"bulletin\" les moyennes actuellement enregistrées\n";
	}
	echo "<input type=\"hidden\" name=\"id_groupe\" value= \"".$id_groupe."\" />\n";
	echo "<input type=\"hidden\" name=\"periode_cn\" value=\"".$periode_cn."\" />\n";
	if (isset($retour_cn)) {echo "<input type=\"hidden\" name=\"retour_cn\" value=\"".$retour_cn."\" />\n";}
	echo "</form>\n";

	echo "</div>\n";
}




//=============================================================
// MODIF: boireaus
echo "
<script type='text/javascript' language='JavaScript'>

function verifcol(num_id){
	document.getElementById('n'+num_id).value=document.getElementById('n'+num_id).value.toLowerCase();
	if(document.getElementById('n'+num_id).value=='a'){
		document.getElementById('n'+num_id).value='abs';
	}
	if(document.getElementById('n'+num_id).value=='d'){
		document.getElementById('n'+num_id).value='disp';
	}
	if(document.getElementById('n'+num_id).value=='n'){
		document.getElementById('n'+num_id).value='-';
	}
	note=document.getElementById('n'+num_id).value;

	if((note!='-')&&(note!='disp')&&(note!='abs')&&(note!='')){
		//if((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0))){
		if(((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0)))||
	((note.search(/^[0-9,]+$/)!=-1)&&(note.lastIndexOf(',')==note.indexOf(',',0)))){
			if((note>20)||(note<0)){
				couleur='red';
			}
			else{
				couleur='$couleur_devoirs';
			}
		}
		else{
			couleur='red';
		}
	}
	else{
		couleur='$couleur_devoirs';
	}
	eval('document.getElementById(\'td_'+num_id+'\').style.background=couleur');
}
</script>
";
//=============================================================


// Tableau des notes pour chaque période
$tab_per_notes=array();

echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=\"post\" name=\"saisie\">\n";
echo add_token_field();
?>

<!--tr><td><input type=submit value=Enregistrer></td><td> : Enregistrer les moyennes dans le bulletin</td></tr></table-->

<?php
	$temoin_notes=0;

	// Il ne faudrait afficher le bouton d'enregistrement que si la période choisie est ouverte ou seulement partiellement close.
	//if ($current_group["classe"]["ver_periode"]["all"][$periode_cn]!=0) {
	//if ($current_group["classe"]["ver_periode"]["all"][$periode_cn]>=2) {
	if (($current_group["classe"]["ver_periode"]["all"][$periode_cn]>=2)||
		($acces_exceptionnel_saisie[$periode_cn])||
		(($current_group["classe"]["ver_periode"]["all"][$periode_cn]!=0)&&($_SESSION['statut']=='secours'))) {
		echo "<p><input type='submit' value='Enregistrer' /> : Enregistrer les moyennes dans le bulletin</p>\n";

		echo "<p><i>Taper une note de 0 à 20 pour chaque élève, ou à défaut le code 'a' pour 'absent', le code 'd' pour 'dispensé', le code 'n' ou '-' pour absence de note.</i></p>\n";
	}

	echo "<p><b>Moyennes (sur 20) de : ".htmlspecialchars($current_group["description"])." (" . $current_group["classlist_string"] . ")</b></p>\n";

	echo "<div id='info_recopie' class='infobulle_corps' style='float:right; width:20em; border: 1px solid black; display:none;'></div>\n";
	//echo "<div style='clear:both;'></div>\n";

	echo "<table border='1' cellspacing='2' cellpadding='1' class='boireaus' summary='Saisie'>\n";
	//echo "<table border='1' cellspacing='2' cellpadding='1'>\n";
	echo "<tr>\n";
	echo "<th><b><a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_cn&amp;order_by=nom' onclick=\"return confirm_abandon (this, change, '$themessage')\">Nom Prénom</a></b></th>\n";

	if ($multiclasses) {
		echo "<th><b><a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_cn&amp;order_by=classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe</a></b></th>";
	}
	$i = 1;
	while ($i < $nb_periode) {
		/*
		if (($periode_cn == $i) and ($current_group["classe"]["ver_periode"]["all"][$i] >= 2)) {
		//if ($current_group["classe"]["ver_periode"]["all"][$i] >= 2) {
			echo "<th bgcolor=\"$couleur_fond\" colspan=\"2\"><b>".ucfirst($nom_periode[$i])."</b></th>\n";
		} else {
			echo "<td><b>".ucfirst($nom_periode[$i])."</b></td>\n";
		}
		*/

		$statut_verrouillage=$current_group["classe"]["ver_periode"]["all"][$i];

		echo "<th ";
		if ($periode_cn == $i) {
			echo "bgcolor=\"$couleur_fond\" ";
			//echo "colspan=\"2\"><b>".ucfirst($nom_periode[$i])."<br />";
			echo "colspan=\"2\"><b>".ucfirst($nom_periode[$i]);
			echo "</b>\n";
			if($statut_verrouillage!=0){echo "<br />\n"."en saisie";}
		}
		else{
			echo "colspan=\"2\"><b><a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$i";
			if(isset($retour_cn)){echo "&amp;retour_cn=yes";}
			echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">".ucfirst($nom_periode[$i])."</a></b>";
		}
		/*
		//echo "colspan=\"2\"><b>".ucfirst($nom_periode[$i])."</b></th>\n";
		if(isset($retour_cn)){
			echo "colspan=\"2\"><b><a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$i&amp;retour_cn=yes'>".ucfirst($nom_periode[$i])."</a></b>";
		}
		else{
			echo "colspan=\"2\"><b><a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$i'>".ucfirst($nom_periode[$i])."</a></b>";
		}
		*/

		//echo "<br />\$statut_verrouillage=$statut_verrouillage";
		echo "</th>\n";

		$i++;
	}
	echo "</tr>\n";

	echo "<tr>\n<th>&nbsp;</th>\n";
	if ($multiclasses) {echo "<th>&nbsp;</th>\n";}

	$i = 1;
	while ($i < $nb_periode) {
		if (($current_group["classe"]["ver_periode"]["all"][$i]>=2)||
			($acces_exceptionnel_saisie[$i])||
			(($current_group["classe"]["ver_periode"]["all"][$i]!=0)&&($_SESSION['statut']=='secours'))) {
			if ($periode_cn == $i) {
				echo "<td bgcolor=\"$couleur_moy_cn\" style='text-align:center;'>Carnet<br />de notes";
				if($_SESSION['statut']=='professeur') {
					$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE id_groupe='$id_groupe' AND periode='$periode_cn';";
					$res_cn=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_cn)>0) {
						$lig_cn=mysqli_fetch_object($res_cn);
						echo "<br /><a href='../cahier_notes/saisie_notes.php?id_conteneur=".$lig_cn->id_cahier_notes."&amp;recalculer=y".add_token_in_url()."' style='font-size:x-small' onclick=\"return confirm_abandon (this, change, '$themessage')\">Recalculer</a>";
					}
				}
				echo "</td>\n";
				echo "<td bgcolor=\"$couleur_fond\" style='text-align:center;'>Bulletin<span id='span_bull_per_$i'></span><br /><a href='#' onclick=\"vider_bulletins_periode($i);return false;\" title=\"Si vous souhaitez vider les moyennes saisies sur les bulletins (*), cliquez ici.\n\n(*) par exemple parce que vous avez fait une recopie des moyennes trop tôt avant la fin de période et qu'elles donne l'illusion que vous avez fini les saisies de notes pour cette période de notes.\n\nN'oubliez pas d'enregistrer après avoir vidé.\"><img src='../images/icons/trash.png' class='icone16' alt='Vider' /></a></td>\n";
			} else {
				//echo "<td>&nbsp;</td>\n";
				echo "<th style='text-align:center;'>Carnet<br />de notes</th><th style='text-align:center;'>Bulletin<span id='span_bull_per_$i'></span></th>\n";
			}
		} else {
			if ($periode_cn == $i) {
				echo "<td colspan='2' style='text-align:center;";
				echo " background-color:$couleur_fond;";
				echo "'><b>".ucfirst($gepiClosedPeriodLabel)."</b><span id='span_bull_per_$i'></span></td>\n";
			}
			else {
				echo "<th colspan='2'><b>".ucfirst($gepiClosedPeriodLabel)."</b><span id='span_bull_per_$i'></span></th>\n";
			}
		}
		$i++;
	}
	?>
</tr>

<?php
// On commence par mettre la liste dans l'ordre souhaité
if ($order_by != "classe") {
	$liste_eleves = $current_group["eleves"]["all"]["list"];
} else {
	// Ici, on trie par classe
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

//$tmp_tab_test=array();

$eleve_login = null;
$num_id = 10;
$prev_classe = null;
//=========================
// Compteur pour les élèves
$i=0;
//=========================
$alt=1;
unset($tab_recopie_vide);
$tab_recopie_vide=array();
foreach ($liste_eleves as $eleve_login) {
	$alt=$alt*(-1);

	//==================
	$temoin_num_id="n";
	//==================

	$k=1;
	while ($k < $nb_periode) {

		$appel_cahier_notes_periode = mysqli_query($GLOBALS["mysqli"], "SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe = '" . $current_group["id"] . "' and periode='$k')");
		$id_racine_periode = @old_mysql_result($appel_cahier_notes_periode, 0, 'id_cahier_notes');


		if (in_array($eleve_login, $current_group["eleves"][$k]["list"])) {
			//
			// si l'élève appartient au groupe pour cette période
			//
			$eleve_nom = $current_group["eleves"][$k]["users"][$eleve_login]["nom"];
			$eleve_prenom = $current_group["eleves"][$k]["users"][$eleve_login]["prenom"];
			$eleve_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$eleve_login]["classe"]]["classe"];
			$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$eleve_login]["classe"]]["id"];
			$suit_option[$k] = 'yes';
			//
			// si l'élève suit la matière
			//
			$note_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres_notes WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
			$eleve_statut = @old_mysql_result($note_query, 0, "statut");
			$eleve_note = @old_mysql_result($note_query, 0, "note");
			$eleve_login_t[$k] = $eleve_login."_t".$k;

			if (($eleve_statut == '')&&($eleve_note!="")) {
				$tab_per_notes[$k][]=$eleve_note;
			}

			//if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] != "N") {
			if ((!$acces_exceptionnel_saisie[$k])&&
				((($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] != "N")&&($_SESSION['statut']!='secours'))||
				(($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]=="O")&&($_SESSION['statut']=='secours')))) {
			//if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] == "O") {
				//
				// si la période est verrouillée pour l'élève
				//

				//$moyenne_query = mysql_query("SELECT * FROM cn_notes_conteneurs WHERE (login='$eleve_login' AND id_conteneur='$id_racine')");
				$moyenne_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_notes_conteneurs WHERE (login='$eleve_login' AND id_conteneur='$id_racine_periode')");
				$statut_moy = @old_mysql_result($moyenne_query, 0, "statut");
				if ($statut_moy == 'y') {
					$moy = @old_mysql_result($moyenne_query, 0, "note");
				} else {
					$moy = '&nbsp;';
				}

				if (($current_group["classe"]["ver_periode"]["all"][$k]>=2)||
					($acces_exceptionnel_saisie[$k])||
					(($current_group["classe"]["ver_periode"]["all"][$k]!=0)&&($_SESSION['statut']=='secours'))) {
					// La période n'est pas complètement verrouillée pour tous.

					if ($periode_cn == $k) {
						// Affichage de la colonne du carnet de notes
						$mess[$k] = "<td bgcolor=\"$couleur_moy_cn\"><center>$moy</center></td>\n";
						$temp = " bgcolor='$couleur_fond'";

						// Affichage de la colonne 'note'
						$mess[$k] =$mess[$k]."<td$temp><center><b>";
					}
					else{
						// Affichage de la colonne du carnet de notes
						$mess[$k] = "<td><center>$moy</center></td>\n";
						// Affichage de la colonne 'note' du bulletin
						$mess[$k] =$mess[$k]."<td><center><b>";
					}
				} else {
					$mess[$k] = '';
					$temp = "";

					// Affichage de la colonne 'note'
					$mess[$k] =$mess[$k]."<td colspan='2'";
					if ($periode_cn == $k) {$mess[$k].=" bgcolor='$couleur_fond'";}
					$mess[$k].="><center><b>";
				}

				// Affichage de la colonne 'note' -> REMONTé
				//$mess[$k] =$mess[$k]."<td><center><b>";
				if ($eleve_statut != '') {
					$mess[$k] = $mess[$k].$eleve_statut;
				} else {
					if ($eleve_note != '') {
						$mess[$k] =$mess[$k]."$eleve_note";
					} else {
						$mess[$k] =$mess[$k]."&nbsp;";
					}
				}
				//$mess[$k] =$mess[$k]."</center></b></td>\n";
				$mess[$k] =$mess[$k]."</b></center></td>\n";
			} else {
				//
				// si la période n'est pas verrouillée pour l'élève
				// PAS COMPLETEMENT...
				//

				//$moyenne_query = mysql_query("SELECT * FROM cn_notes_conteneurs WHERE (login='$eleve_login' AND id_conteneur='$id_racine')");
				$moyenne_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_notes_conteneurs WHERE (login='$eleve_login' AND id_conteneur='$id_racine_periode')");
				$statut_moy = @old_mysql_result($moyenne_query, 0, "statut");
				if ($statut_moy == 'y') {
					$moy = @old_mysql_result($moyenne_query, 0, "note");
					$temoin_notes++;
				} else {
					$moy = '&nbsp;';
				}

				if ($periode_cn == $k) {
					// Affichage de la colonne du carnet de notes
					$mess[$k] = "<td bgcolor=\"$couleur_moy_cn\"><center>$moy</center></td>\n";
					$temp = "bgcolor=$couleur_fond";
				} else {
					//$mess[$k] = '';
					$mess[$k] = "<td><center>$moy</center></td>\n";
					$temp = "";
				}

				// Affichage de la colonne 'note'
				if ($periode_cn == $k){

					//==================
					// AJOUT boireaus 20080523
					$temoin_num_id="y";
					//==================

					// ========================
					$mess[$k].="<td id=\"td_".$k.$num_id."\" ".$temp."><center>\n";
					$mess[$k].="<input type='hidden' name=\"log_eleve_".$k."[$i]\" value=\"$eleve_login\" />\n";

					//$mess[$k].="<input id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" type=\"text\" size=\"4\" name=\"note_eleve_".$k."[$i]\" value=";
					$mess[$k].="<input id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" type=\"text\" size=\"4\" ";
					$mess[$k].="autocomplete=\"off\" ";
					$mess[$k].="name=\"note_eleve_".$k."[$i]\" value=";
					// ========================

					if (($periode_cn == $k) and ($is_posted=='bascule')) {
						//$mess[$k] = $mess[$k]."<td id=\"td_".$k.$num_id."\" ".$temp."><center><input id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" type=\"text\" size=\"4\" name=\"".$eleve_login_t[$k]."\" value=";
						if ($statut_moy == 'y') {
							$mess[$k] = $mess[$k]."\"".@old_mysql_result($moyenne_query, 0, "note")."\"";
						} else {
							$mess[$k] = $mess[$k]."\"\"";
							$tab_recopie_vide[]="$eleve_nom $eleve_prenom";
						}
						//$mess[$k] = $mess[$k]." onfocus=\"javascript:this.select()\" onchange=\"verifcol(".$k.$num_id.");changement()\" /></td>\n";
						$mess[$k] = $mess[$k]." onfocus=\"javascript:this.select()\" onchange=\"verifcol(".$k.$num_id.");changement()\" />\n";
					} else {
						//$mess[$k] = $mess[$k]."<td id=\"td_".$k.$num_id."\" ".$temp."><center><input id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" type=\"text\" size=\"4\" name=\"".$eleve_login_t[$k]."\" value=";
						if ($eleve_statut != '') {
							$mess[$k] = $mess[$k]."\"".$eleve_statut."\"";
							//$tmp_tab_test[]=$eleve_statut;
						} else {
							$mess[$k] = $mess[$k]."\"".$eleve_note."\"";
							//$tmp_tab_test[]=$eleve_note;
						}
						$mess[$k] = $mess[$k]." onfocus=\"javascript:this.select()\" onchange=\"verifcol(".$k.$num_id.");changement()\" />\n";
					}

					$mess[$k].="</center></td>\n";
				}
				else{
					$mess[$k] = $mess[$k]."<td><center><b>";
					if ($eleve_statut != '') {
						$mess[$k] = $mess[$k].$eleve_statut;
					} else {
						$mess[$k] = $mess[$k].$eleve_note;
					}
					$mess[$k] = $mess[$k]."</b></center></td>\n";
				}
			}

		} else {
			//
			// si l'élève n'est pas dans le groupe pour la période
			//
			$suit_option[$k] = 'no';

			if (($current_group["classe"]["ver_periode"]["all"][$k]>=2)||
				($acces_exceptionnel_saisie[$k])||
				(($current_group["classe"]["ver_periode"]["all"][$k]!=0)&&($_SESSION['statut']=='secours'))) {
				if ($periode_cn == $k) {
					$mess[$k]="<td bgcolor=\"$couleur_moy_cn\"><center>-</center></td><td bgcolor=\"$couleur_fond\"><center>-</center></td>\n";
				} else {
					$mess[$k]="<td><center>-</center></td><td><center>-</center></td>\n";
				}
			} else {
				//$mess[$k]="<td colspan='2' tric='1'";
				$mess[$k]="<td colspan='2'";
				//$mess[$k]="<td tric='1'";
				if($periode_cn == $k){$mess[$k].=" bgcolor='$couleur_fond'";}
				$mess[$k].="><center>-</center></td>\n";
			}

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
		//==================
		if($temoin_num_id=='y') {
			$num_id++;
		}
		//==================

		if ($order_by == "nom" OR $prev_classe == $eleve_classe OR $prev_classe == null) {
			//echo "<tr><td>$eleve_nom $eleve_prenom</td>";
			echo "<tr class='lig$alt'><td style='text-align:left;'>";
			if($acces_visu_eleve) {
				echo "<a href='../eleves/visu_eleve.php?ele_login=$eleve_login' title=\"Voir (dans un nouvel onglet) la fiche élève avec les onglets Élève, Enseignements, Bulletins, CDT, Absences,...\" target='_blank' style='text-decoration:none; color:black;'>$eleve_nom $eleve_prenom</a>";
			}
			else {
				echo "$eleve_nom $eleve_prenom";
			}
			echo "</td>";
			if ($multiclasses) echo "<td style='text-align:center;'>$eleve_classe</td>";
			echo "\n";
			$prev_classe = $eleve_classe;
		} else {
			//echo "<tr><td style='border-top: 2px solid blue;'>$eleve_nom $eleve_prenom</td>";
			echo "<tr class='lig$alt'><td style='border-top: 2px solid blue; text-align:left;'>";
			if($acces_visu_eleve) {
				echo "<a href='../eleves/visu_eleve.php?ele_login=$eleve_login' title=\"Voir (dans un nouvel onglet) la fiche élève avec les onglets Élève, Enseignements, Bulletins, CDT, Absences,...\" target='_blank' style='text-decoration:none; color:black;'>$eleve_nom $eleve_prenom</a>";
			}
			else {
				echo "$eleve_nom $eleve_prenom";
			}
			echo "</td>";
			if ($multiclasses) echo "<td style='border-top: 2px solid blue;'>$eleve_classe</td>";
			echo "\n";
			$prev_classe = $eleve_classe;
		}
		$k=1;
		while ($k < $nb_periode) {
			echo $mess[$k];
			$k++;
		}
		echo "</tr>\n";
	}

	$i++;
}
$nb_eleves=$i;

echo "<tr>\n";
if ($multiclasses) {
	echo "<th colspan='2'>";
} else {
	echo "<th>";
}

echo "<input type='hidden' name='indice_max_log_eleve' value='$i' />\n";

echo "Moyennes :</th>\n";

$k='1';
$temp = '';
while ($k < $nb_periode) {
	if (($current_group["classe"]["ver_periode"]["all"][$k]>=2)||
		($acces_exceptionnel_saisie[$k])||
		(($current_group["classe"]["ver_periode"]["all"][$k]!=0)&&($_SESSION['statut']=='secours'))) {

		$appel_cahier_notes_periode = mysqli_query($GLOBALS["mysqli"], "SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe = '" . $current_group["id"] . "' and periode='$k')");
		$id_racine_periode = @old_mysql_result($appel_cahier_notes_periode, 0, 'id_cahier_notes');

		$call_moy_moy = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(n.note),1) moyenne FROM cn_notes_conteneurs n, j_eleves_groupes j WHERE
		(
		j.id_groupe='" . $current_group["id"] ."' AND
		j.periode = '$periode_cn' AND
		n.login = j.login AND
		n.statut='y' AND
		n.id_conteneur='$id_racine_periode'
		)");

		$moy_moy = old_mysql_result($call_moy_moy, 0, "moyenne");
		if ($moy_moy != '') {
			$affiche_moy = $moy_moy;
		} else {
			$affiche_moy = "&nbsp;";
		}

		if ($periode_cn == $k) {
			echo "<td bgcolor=\"$couleur_moy_cn\"><center>$affiche_moy</center></td>\n";
			$temp = "bgcolor=\"$couleur_fond\"";
		}
		else{
			echo "<td><center>$affiche_moy</center></td>\n";
			$temp = "";
		}
	} else {
		//$temp = '';
		$temp = " colspan='2'";
		if($periode_cn == $k){
			$temp.=" bgcolor='$couleur_fond'";
		}
	}

	if (($is_posted=='bascule') and (($periode_cn == $k) and
		(($current_group["classe"]["ver_periode"]["all"][$k]>=2)||($acces_exceptionnel_saisie[$k])||(($current_group["classe"]["ver_periode"]["all"][$k]!=0)&&($_SESSION['statut']=='secours'))))) {
		echo "<td><center><b>$affiche_moy</b></center></td>\n";
	} else {
		$call_moyenne_t[$k] = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(n.note),1) moyenne FROM matieres_notes n, j_eleves_groupes j " .
									"WHERE (" .
									"n.id_groupe='" . $current_group["id"] ."' AND " .
									"n.login = j.login AND " .
									"n.statut='' AND " .
									"j.id_groupe = n.id_groupe AND " .
									"n.periode='$k' AND j.periode='$k'" .
									")");
		$moyenne_t[$k] = old_mysql_result($call_moyenne_t[$k], 0, "moyenne");
		if ($moyenne_t[$k] != '') {
			echo "<td ".$temp."><center><b>$moyenne_t[$k]</b></center></td>\n";
		} else {
			echo "<td ".$temp.">&nbsp;</td>\n";
		}
	}

	$k++;
}
?>
</tr>
<?php

	if(count($tab_per_notes)>0) {
		echo "<tr>\n";
		if ($multiclasses) {
			echo "<th colspan='2'>";
		} else {
			echo "<th>";
		}
		echo "Min. :</th>\n";
		for($loop=1;$loop<$nb_periode;$loop++) {
			/*
			if((isset($tab_per_notes[$loop]))&&(count($tab_per_notes[$loop])>0)) {
				$tab_m[$loop]=calcule_moy_mediane_quartiles($tab_per_note[$loop]);
			}
			*/

			// Colonne CN
			if (($current_group["classe"]["ver_periode"]["all"][$loop]>=2)||
				($acces_exceptionnel_saisie[$loop])||
				(($current_group["classe"]["ver_periode"]["all"][$loop]!=0)&&($_SESSION['statut']=='secours'))) {
				if ($periode_cn == $loop) {
					echo "<td bgcolor=\"$couleur_moy_cn\"></td>\n";
					$temp = "bgcolor=\"$couleur_fond\"";
				}
				else{
					echo "<td></td>\n";
					$temp = "";
				}
			} else {
				$temp = " colspan='2'";
				if($periode_cn == $loop){
					$temp.=" bgcolor='$couleur_fond'";
				}
			}

			// Colonne Bull
			echo "<td";
			if (($is_posted=='bascule') and (($periode_cn == $loop) and
				(($current_group["classe"]["ver_periode"]["all"][$loop]>=2)||($acces_exceptionnel_saisie[$loop])||(($current_group["classe"]["ver_periode"]["all"][$loop]!=0)&&($_SESSION['statut']=='secours'))))) {
				echo ">";
			} else {
				echo " ".$temp.">";
			}
			if((isset($tab_per_notes[$loop]))&&(count($tab_per_notes[$loop])>0)) {
				echo min($tab_per_notes[$loop]);
			}
			echo "</td>\n";
		}
		echo "</tr>\n";

		echo "<tr>\n";
		if ($multiclasses) {
			echo "<th colspan='2'>";
		} else {
			echo "<th>";
		}
		echo "Max. :</th>\n";
		for($loop=1;$loop<$nb_periode;$loop++) {
			/*
			if((isset($tab_per_notes[$loop]))&&(count($tab_per_notes[$loop])>0)) {
				$tab_m[$loop]=calcule_moy_mediane_quartiles($tab_per_note[$loop]);
			}
			*/

			// Colonne CN
			if (($current_group["classe"]["ver_periode"]["all"][$loop]>=2)||
				($acces_exceptionnel_saisie[$loop])||
				(($current_group["classe"]["ver_periode"]["all"][$loop]!=0)&&($_SESSION['statut']=='secours'))) {
				if ($periode_cn == $loop) {
					echo "<td bgcolor=\"$couleur_moy_cn\"></td>\n";
					$temp = "bgcolor=\"$couleur_fond\"";
				}
				else{
					echo "<td></td>\n";
					$temp = "";
				}
			} else {
				$temp = " colspan='2'";
				if($periode_cn == $loop){
					$temp.=" bgcolor='$couleur_fond'";
				}
			}

			// Colonne Bull
			echo "<td";
			if (($is_posted=='bascule') and (($periode_cn == $loop) and
				(($current_group["classe"]["ver_periode"]["all"][$loop]>=2)||($acces_exceptionnel_saisie[$loop])||(($current_group["classe"]["ver_periode"]["all"][$loop]!=0)&&($_SESSION['statut']=='secours'))))) {
				echo ">";
			} else {
				echo " ".$temp.">";
			}
			if((isset($tab_per_notes[$loop]))&&(count($tab_per_notes[$loop])>0)) {
				echo max($tab_per_notes[$loop]);
			}
			echo "</td>\n";
		}
		echo "</tr>\n";

		echo "<tr>\n";
		if ($multiclasses) {
			echo "<th colspan='2'>";
		} else {
			echo "<th>";
		}
		echo "Répartition&nbsp;:</th>\n";
		for($loop=1;$loop<$nb_periode;$loop++) {
			$histogramme="";
			if((isset($tab_per_notes[$loop]))&&(count($tab_per_notes[$loop])>0)) {
				$histogramme=retourne_html_histogramme_svg($tab_per_notes[$loop], "Repartition P$loop", "repartition_p$loop");
			}

			// Colonne CN
			if (($current_group["classe"]["ver_periode"]["all"][$loop]>=2)||
				($acces_exceptionnel_saisie[$loop])||
				(($current_group["classe"]["ver_periode"]["all"][$loop]!=0)&&($_SESSION['statut']=='secours'))) {
				if ($periode_cn == $loop) {
					echo "<td bgcolor=\"$couleur_moy_cn\"></td>\n";
					$temp = "bgcolor=\"$couleur_fond\"";
				}
				else{
					echo "<td></td>\n";
					$temp = "";
				}
			} else {
				$temp = " colspan='2'";
				if($periode_cn == $loop){
					$temp.=" bgcolor='$couleur_fond'";
				}
			}

			// Colonne Bull
			echo "<td";
			if (($is_posted=='bascule') and (($periode_cn == $loop) and
				(($current_group["classe"]["ver_periode"]["all"][$loop]>=2)||($acces_exceptionnel_saisie[$loop])||(($current_group["classe"]["ver_periode"]["all"][$loop]!=0)&&($_SESSION['statut']=='secours'))))) {
				echo ">";
			} else {
				echo " ".$temp.">";
			}

			if($histogramme!="") {
				echo $histogramme;
				echo "<script type='text/javascript'>
	if(document.getElementById('span_bull_per_$loop')) {document.getElementById('span_bull_per_$loop').innerHTML='<br />".addslashes($histogramme)."';}
</script>\n";
			}
			echo "</td>\n";
		}
		echo "<tr>\n";
	}
?>
</table>
<?php

if($temoin_notes==0) {
	echo "<script type='text/javascript'>
	document.getElementById('div_bascule').style.display='none';
</script>\n";
}

if(count($tab_recopie_vide)>0) {
	$chaine_js="<p style='text-align:center'>Pas de moyenne recopiée pour:<br />";
	for($i=0;$i<count($tab_recopie_vide);$i++) {
		$chaine_js.="<b>".$tab_recopie_vide[$i]."</b><br />";
	}
	$chaine_js.="Il faudra saisir manuellement Absent (<b>a</b>), Dispensé (<b>d</b>) ou Non noté (<b>-</b>).</p>";

	echo "<script type='text/javascript'>
	document.getElementById('info_recopie').innerHTML=\"$chaine_js\";
	document.getElementById('info_recopie').style.display='';
</script>\n";
}

if ($is_posted == 'bascule') {
?>
	<script type="text/javascript" language="javascript">
	<!--
	alert("Attention, les notes importées ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton \"enregistrer\") !");
	//-->
	</script>
<?php
}
?>
<input type="hidden" name="is_posted" value="yes" />
<input type="hidden" name="id_groupe" value="<?php echo "$id_groupe";?>" />
<input type="hidden" name="periode_cn" value="<?php echo "$periode_cn";?>" />
<?php
if (isset($retour_cn)) echo "<input type=\"hidden\" name=\"retour_cn\" value=\"".$retour_cn."\" />\n";

if (($current_group["classe"]["ver_periode"]["all"][$periode_cn]>=2)||
($acces_exceptionnel_saisie[$periode_cn])||
(($current_group["classe"]["ver_periode"]["all"][$periode_cn]!=0)&&($_SESSION['statut']=='secours'))
) {

	echo "<center>\n";
	echo "<div id='fixe'>\n";
	echo "<input type='submit' value='Enregistrer' />\n";
	echo "</div>\n";
	echo "</center>\n";
}
?>

</form>

<script language='javascript' type='text/javascript'>
	// On donne le focus à la première cellule lors du chargement de la page:

	for(k=1;k<<?php echo $nb_periode;?>;k++) {
		if(document.getElementById('n'+k+'10')){
			document.getElementById('n'+k+'10').focus();
		}
	}

	function vider_bulletins_periode(num) {
		for(i=0;i<<?php echo $nb_eleves;?>;i++) {
			j=eval(i+10);
			if(document.getElementById('n'+num+j)){
				document.getElementById('n'+num+j).value='';
			}
		}
	}
</script>
<p><br /></p>
<?php require("../lib/footer.inc.php");?>
