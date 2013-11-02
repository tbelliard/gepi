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

if($_SESSION['statut']!='administrateur') {
	$user_login=$_SESSION['login'];
}

$affiche_adresse_resp=isset($_POST["affiche_adresse_resp"]) ? $_POST["affiche_adresse_resp"] : "n";

if(!isset($user_login)) {
	$tab_mode=array('personnels', 'responsable', 'eleve');
	if((!isset($mode))||(!in_array($mode,$tab_mode))) {
		//$mode="personnels";
		$mode="";
		$url_retour_index_utilisateurs="index.php";
	}
	elseif($mode=='responsable') {
		$url_retour_index_utilisateurs="edit_responsable.php";
	}
	elseif($mode=='eleve') {
		$url_retour_index_utilisateurs="edit_eleve.php";
	}
	else {
		$url_retour_index_utilisateurs="index.php?mode=$mode";
	}

	if($mode=="personnels") {
		$tab_statut=array('professeur', 'scolarite', 'cpe', 'autre');
		if(!isset($user_statut)) {
			// Imprimer les fiches bienvenue pour une ou des catégories... ou pour une sélection d'utilisateurs, ou pour une classe...

			//**************** EN-TETE *****************************
			$titre_page = "Gestion des utilisateurs | Impression fiches utilisateurs";
			require_once("../lib/header.inc.php");
			//**************** FIN EN-TETE *****************
	
			echo "<p class='bold'>";
			echo "<a href='$url_retour_index_utilisateurs'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
			echo " | <a href='".$_SERVER['PHP_SELF']."'> Fiches bienvenue</a>";
			if(acces("/gestion/modify_impression.php", $_SESSION['statut'])) {
				echo " | <a href='../gestion/modify_impression.php?fiche=personnels'> Modifier les fiches bienvenue</a>";
			}
			echo "</p>\n";
	
			echo "<form action='".$_SERVER['PHP_SELF']."' method='post' target='_blank'>\n";
			echo "<p>Imprimer les fiches bienvenue pour une ou des catégories</p>\n";
			for($i=0;$i<count($tab_statut);$i++) {
				echo "<input type='checkbox' name='user_statut[]' id='user_statut_$tab_statut[$i]' value='$tab_statut[$i]' /><label for='user_statut_$tab_statut[$i]'> $tab_statut[$i]</label><br />\n";
			}
			echo "<input type='hidden' name='mode' value='$mode' />\n";
			echo "<input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";
		
			require("../lib/footer.inc.php");
			die();
		}
	
		$user_login=array();
		for($i=0;$i<count($user_statut);$i++) {
			if(in_array($user_statut[$i],$tab_statut)) {
				$sql="SELECT login FROM utilisateurs WHERE statut='$user_statut[$i]' AND etat='actif' ORDER BY nom, prenom;";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					while($lig=mysql_fetch_object($res)) {$user_login[]=$lig->login;}
				}
			}
		}
	}
	elseif($mode=="responsable") {
		if(!isset($id_classe)) {
			//**************** EN-TETE *****************************
			$titre_page = "Gestion des utilisateurs | Impression fiches responsables";
			require_once("../lib/header.inc.php");
			//**************** FIN EN-TETE *****************
	
			echo "<p class='bold'>";
			echo "<a href='$url_retour_index_utilisateurs'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
			echo " | <a href='".$_SERVER['PHP_SELF']."'> Fiches bienvenue</a>";
			if(acces("/gestion/modify_impression.php", $_SESSION['statut'])) {
				echo " | <a href='../gestion/modify_impression.php?fiche=responsables'> Modifier les fiches bienvenue</a>";
			}
			echo "</p>\n";

			$sql="SELECT DISTINCT c.id,c.classe FROM classes c,
													j_eleves_classes jec,
													eleves e,
													responsables2 r,
													resp_pers rp,
													utilisateurs u
										WHERE jec.login=e.login AND
												e.ele_id=r.ele_id AND
												r.pers_id=rp.pers_id AND
												rp.login=u.login AND
												jec.id_classe=c.id
										ORDER BY classe;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				echo "<p>Aucune compte responsable n'a encore été créé.</p>\n";
			}
			else {
				echo "<form action='".$_SERVER['PHP_SELF']."' method='post' target='_blank'>\n";
				echo "<p>Choisissez les classes pour lesquelles générer les fiches bienvenue&nbsp;:<br />\n";
				while ($lig=mysql_fetch_object($res)) {
					echo "<input type='checkbox' name='id_classe[]' id='id_classe_$lig->id' value='$lig->id' onchange='change_style_classe($lig->id)'><label id='clas_id_classe_$lig->id' for='id_classe_$lig->id'> ".$lig->classe."</label><br />\n";
				}
				echo "<input type='checkbox' name='affiche_adresse_resp' id='affiche_adresse_resp' value='y' /><label for='affiche_adresse_resp'> avec l'adresse du responsable</label><br />\n";
				echo "<input type='submit' value='Valider' /></p>\n";
				echo "<input type='hidden' name='mode' value='$mode' />\n";
				echo "</form>\n";

				echo "<script type='text/javascript'>
	function change_style_classe(num) {
		if(document.getElementById('id_classe_'+num)) {
			if(document.getElementById('id_classe_'+num).checked) {
				document.getElementById('clas_id_classe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('clas_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}
</script>\n";
			}

			require("../lib/footer.inc.php");
			die();
		}
		else {
			$user_login=array();

			if(is_array($id_classe)) {
				for($i=0;$i<count($id_classe);$i++) {
					if(is_numeric($id_classe[$i])) {
						$sql="SELECT u.login FROM j_eleves_classes jec,
																eleves e,
																responsables2 r,
																resp_pers rp,
																utilisateurs u
													WHERE jec.login=e.login AND
															e.ele_id=r.ele_id AND
															r.pers_id=rp.pers_id AND
															rp.login=u.login AND
															jec.id_classe='$id_classe[$i]'
													ORDER BY u.nom, u.prenom;";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)>0) {
							while ($lig=mysql_fetch_object($res)) {
								if(!in_array($lig->login,$user_login)) {
									$user_login[]=$lig->login;
								}
							}
						}
					}
				}
			}
			elseif(is_numeric($id_classe)) {
				$sql="SELECT u.login FROM j_eleves_classes jec,
														eleves e,
														responsables2 r,
														resp_pers rp,
														utilisateurs u
											WHERE jec.login=e.login AND
													e.ele_id=r.ele_id AND
													r.pers_id=rp.pers_id AND
													rp.login=u.login AND
													jec.id_classe='$id_classe[$i]'
											ORDER BY u.nom, u.prenom;";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					while ($lig=mysql_fetch_object($res)) {
						if(!in_array($lig->login,$user_login)) {
							$user_login[]=$lig->login;
						}
					}
				}
			}
			else {
				$msg="L'identifiant de classe est erroné: '$id_classe'.";
			}
		}
	}
	elseif($mode=="eleve") {
		if(!isset($id_classe)) {
			//**************** EN-TETE *****************************
			$titre_page = "Gestion des utilisateurs | Impression fiches élèves";
			require_once("../lib/header.inc.php");
			//**************** FIN EN-TETE *****************
	
			echo "<p class='bold'>";
			echo "<a href='$url_retour_index_utilisateurs'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
			echo " | <a href='".$_SERVER['PHP_SELF']."'> Fiches bienvenue</a>";
			if(acces("/gestion/modify_impression.php", $_SESSION['statut'])) {
				echo " | <a href='../gestion/modify_impression.php?fiche=eleves'> Modifier les fiches bienvenue</a>";
			}
			echo "</p>\n";

			$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_eleves_classes jec, utilisateurs u
									WHERE jec.login=u.login AND
											jec.id_classe=c.id
									ORDER BY classe;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				echo "<p>Aucune compte élève n'a encore été créé.</p>\n";
			}
			else {
				echo "<form action='".$_SERVER['PHP_SELF']."' method='post' target='_blank'>\n";
				echo "<p>Choisissez les classes pour lesquelles générer les fiches bienvenue&nbsp;:<br />\n";
				while ($lig=mysql_fetch_object($res)) {
					echo "<input type='checkbox' name='id_classe[]' id='id_classe_$lig->id' value='$lig->id'><label for='id_classe_$lig->id'> ".$lig->classe."</label><br />\n";
				}
				echo "<input type='submit' value='Valider' /></p>\n";
				echo "<input type='hidden' name='mode' value='$mode' />\n";
				echo "</form>\n";
			}

			require("../lib/footer.inc.php");
			die();
		}
		else {
			$user_login=array();

			if(is_array($id_classe)) {
				for($i=0;$i<count($id_classe);$i++) {
					if(is_numeric($id_classe[$i])) {
						$sql="SELECT DISTINCT u.login FROM j_eleves_classes jec, utilisateurs u
										WHERE jec.login=u.login AND
												jec.id_classe='$id_classe[$i]'
										ORDER BY u.nom, u.prenom;";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)>0) {
							while ($lig=mysql_fetch_object($res)) {
								if(!in_array($lig->login,$user_login)) {
									$user_login[]=$lig->login;
								}
							}
						}
					}
				}
			}
			elseif(is_numeric($id_classe)) {
				$sql="SELECT DISTINCT u.login FROM j_eleves_classes jec, utilisateurs u
								WHERE jec.login=u.login AND
										jec.id_classe='$id_classe'
								ORDER BY u.nom, u.prenom;";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					while ($lig=mysql_fetch_object($res)) {
						if(!in_array($lig->login,$user_login)) {
							$user_login[]=$lig->login;
						}
					}
				}
			}
			else {
				$msg="L'identifiant de classe est erroné: '$id_classe'.";
			}
		}
	}
	else {
		//**************** EN-TETE *****************************
		$titre_page = "Gestion des utilisateurs | Impression fiches utilisateurs";
		require_once("../lib/header.inc.php");
		//**************** FIN EN-TETE *****************

		echo "<p class='bold'>";
		echo "<a href='$url_retour_index_utilisateurs'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour index utilisateurs</a>";
		if(acces("/gestion/modify_impression.php", $_SESSION['statut'])) {
			echo " | <a href='../gestion/modify_impression.php'> Modifier les fiches bienvenue</a>";
		}
		echo "</p>\n";

		echo "<p>Fiches bienvenue&nbsp;:</p>";
		echo "<ul>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=personnels'>personnels</a></li>";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=responsable'>responsables</a></li>";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=eleve'>élèves</a></li>";
		echo "</ul>\n";

		require("../lib/footer.inc.php");
		die();
	}

	if(count($user_login)==0) {
		//**************** EN-TETE *****************************
		$titre_page = "Gestion des utilisateurs | Impression fiches utilisateurs";
		require_once("../lib/header.inc.php");
		//**************** FIN EN-TETE *****************
		echo "<p class='bold'>";
		echo "<a href='$url_retour_index_utilisateurs'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour index utilisateurs</a>";
		echo " | <a href='".$_SERVER['PHP_SELF']."'> Fiches bienvenue</a>";
		if(acces("/gestion/modify_impression.php", $_SESSION['statut'])) {
			echo " | <a href='../gestion/modify_impression.php'> Modifier les fiches bienvenue</a>";
		}
		echo "</p>\n";

		echo "<p>Aucun utilisateur (<i>$mode</i>) n'a été sélectionné.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}
}

//**************** EN-TETE *****************************
//$titre_page = "Gestion des utilisateurs | Impression fiches utilisateurs";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//function fiche_bienvenue($user_login, $mot_de_passe=NULL,$user_statut='personnels') {
function fiche_bienvenue($user_login, $mot_de_passe=NULL, $mode_retour="echo") {
	global $affiche_adresse_resp;

	$lignes_FB="";

	$call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE login='$user_login'");
	
	//$user_login = mysql_result($call_user_info, "0", "login");
	$user_nom = mysql_result($call_user_info, "0", "nom");
	$user_prenom = mysql_result($call_user_info, "0", "prenom");
	$user_statut = mysql_result($call_user_info, "0", "statut");
	$user_email = mysql_result($call_user_info, "0", "email");

	if($user_statut=='professeur') {
		$call_matieres = mysql_query("SELECT * FROM j_professeurs_matieres j WHERE j.id_professeur = '$user_login' ORDER BY ordre_matieres");
		$nb_mat = mysql_num_rows($call_matieres);
		$k = 0;
		while ($k < $nb_mat) {
			$user_matiere[$k] = mysql_result($call_matieres, $k, "id_matiere");
			$k++;
		}
	}

	/*
	$call_data = mysql_query("SELECT * FROM classes");
	$nombre_classes = mysql_num_rows($call_data);
	$i = 0;
	while ($i < $nombre_classes){
		$classe[$i] = mysql_result($call_data, $i, "classe");
		$i++;
	}
	*/

	if($user_statut=='responsable') {
		$impression = getSettingValue("ImpressionFicheParent");
	}
	elseif($user_statut=='eleve') {
		$impression = getSettingValue("ImpressionFicheEleve");
	}
	else {
		$impression = getSettingValue("Impression");
	}

	if($affiche_adresse_resp=='y') {
		// Récupération des variables du bloc adresses:
		// Liste de récupération à extraire de la boucle élèves pour limiter le nombre de requêtes... A FAIRE
		// Il y a d'autres récupération de largeur et de positionnement du bloc adresse à extraire...
		// PROPORTION 30%/70% POUR LE 1er TABLEAU ET ...
		$largeur1=getSettingValue("addressblock_logo_etab_prop") ? getSettingValue("addressblock_logo_etab_prop") : 40;
		$largeur2=100-$largeur1;
	
		// Taille des polices sur le bloc adresse:
		$addressblock_font_size=getSettingValue("addressblock_font_size") ? getSettingValue("addressblock_font_size") : 12;
	
		// Taille de la cellule Classe et Année scolaire sur le bloc adresse:
		$addressblock_classe_annee=getSettingValue("addressblock_classe_annee") ? getSettingValue("addressblock_classe_annee") : 35;
		// Calcul du pourcentage par rapport au tableau contenant le bloc Classe, Année,...
		$addressblock_classe_annee2=round(100*$addressblock_classe_annee/(100-$largeur1));
	
		// Débug sur l'entête pour afficher les cadres
		$addressblock_debug=getSettingValue("addressblock_debug") ? getSettingValue("addressblock_debug") : "n";

		$addressblock_length=getSettingValue("addressblock_length") ? getSettingValue("addressblock_length") : 6;
		$addressblock_padding_top=getSettingValue("addressblock_padding_top") ? getSettingValue("addressblock_padding_top") : 0;
		$addressblock_padding_text=getSettingValue("addressblock_padding_text") ? getSettingValue("addressblock_padding_text") : 0;
		$addressblock_padding_right=getSettingValue("addressblock_padding_right") ? getSettingValue("addressblock_padding_right") : 0;

		//$addressblock_debug="y";

		/*
		$ligne1="NOM PRENOM";
		$ligne2="3 rue de....";
		$ligne3="27300 BERNAY";
		*/

		$sql="SELECT ra.*,rp.nom,rp.prenom,rp.civilite FROM resp_adr ra, resp_pers rp WHERE rp.adr_id=ra.adr_id AND rp.login='$user_login';";
		$res_adr_resp=mysql_query($sql);
		if(mysql_num_rows($res_adr_resp)==0) {
			$ligne1="<font color='red'><b>ADRESSE MANQUANTE</b></font>";
			$ligne2="";
			$ligne3="";
		}
		else {
			$lig_adr_resp=mysql_fetch_object($res_adr_resp);

			$ligne1=$lig_adr_resp->civilite." ".$lig_adr_resp->nom." ".$lig_adr_resp->prenom;
			$ligne2=$lig_adr_resp->adr1;
			$ligne3=$lig_adr_resp->cp." ".$lig_adr_resp->commune;

			if($lig_adr_resp->civilite="") {
				$ligne1=$lig_adr_resp->civilite." ".$lig_adr_resp->nom." ".$lig_adr_resp->prenom;
			}
			else {
				$ligne1="M.".$lig_adr_resp->nom." ".$lig_adr_resp->prenom;
			}

			$ligne2=$lig_adr_resp->adr1;
			if($lig_adr_resp->adr2!=""){
				$ligne2.="<br />\n".$lig_adr_resp->adr2;
			}
			if($lig_adr_resp->adr3!=""){
				$ligne2.="<br />\n".$lig_adr_resp->adr3;
			}
			if($lig_adr_resp->adr4!=""){
				$ligne2.="<br />\n".$lig_adr_resp->adr4;
			}
			$ligne3=$lig_adr_resp->cp." ".$lig_adr_resp->commune;

			if(($lig_adr_resp->pays!="")&&(mb_strtolower($lig_adr_resp->pays)!=mb_strtolower(getSettingValue('gepiSchoolPays')))) {
				if($ligne3!=" "){
					$ligne3.="<br />";
				}
				$ligne3.=$lig_adr_resp->pays;
			}

		}

		$lignes_FB.="<div style='clear: both; font-size: xx-small;'>&nbsp;</div>\n";

		// Cadre adresse du responsable:
		$lignes_FB.="<div style='float:right;
width:".$addressblock_length."mm;
padding-top:".$addressblock_padding_top."mm;
padding-bottom:".$addressblock_padding_text."mm;
padding-right:".$addressblock_padding_right."mm;\n";
		if($addressblock_debug=="y"){$lignes_FB.="border: 1px solid blue;\n";}
		$lignes_FB.="font-size: ".$addressblock_font_size."pt;
'>
<div align='left'>
$ligne1<br />
$ligne2<br />
$ligne3
</div>
</div>\n";



		// Cadre contenant le tableau Logo+Ad_etab et le nom, prénom,... de l'élève:
		$lignes_FB.="<div style='float:left;
left:0px;
top:0px;
width:".$largeur1."%;\n";
		if($addressblock_debug=="y"){$lignes_FB.="border: 1px solid green;\n";}
		$lignes_FB.="'>\n";

	}

	$lignes_FB.="<table border='0' summary='Destinataire fiche bienvenue $user_login'>\n";
	$lignes_FB.="<tr>\n";
	$lignes_FB.="<td>\n";
	$lignes_FB.="A l'attention de&nbsp;: \n";
	$lignes_FB.="</td>\n";
	$lignes_FB.="<td>\n";
	$lignes_FB.="<span class=\"bold\">$user_prenom $user_nom</span>\n";
	$lignes_FB.="</td>\n";
	$lignes_FB.="</tr>\n";

	$lignes_FB.="<tr>\n";
	$lignes_FB.="<td>\n";
	$lignes_FB.="Nom de login&nbsp;: \n";
	$lignes_FB.="</td>\n";
	$lignes_FB.="<td>\n";
	$lignes_FB.="<span class = \"bold\">$user_login</span>";
	$lignes_FB.="</td>\n";
	$lignes_FB.="</tr>\n";

	if (isset($mot_de_passe)) {
		$mot_de_passe = urldecode($mot_de_passe);
		$lignes_FB.="<tr>\n";
		$lignes_FB.="<td>\n";
		$lignes_FB.="Mot de passe&nbsp;: \n";
		$lignes_FB.="</td>\n";
		$lignes_FB.="<td>\n";
		$lignes_FB.="<span class = \"bold\">".stripslashes($mot_de_passe)."</span>";
		$lignes_FB.="</td>\n";
		$lignes_FB.="</tr>\n";
	}
	
	$lignes_FB.="<tr>\n";
	$lignes_FB.="<td>\n";
	$lignes_FB.="Adresse E-mail&nbsp;: ";
	$lignes_FB.="</td>\n";
	$lignes_FB.="<td>\n";
	$lignes_FB.="<span class = \"bold\">$user_email</span>";
	$lignes_FB.="</td>\n";
	$lignes_FB.="</tr>\n";

	if($user_statut=='eleve') {
		$tab_tmp_info_classes=get_noms_classes_from_ele_login($user_login);
		$lignes_FB.="<tr>\n";
		$lignes_FB.="<td>\n";
		$lignes_FB.="Élève de&nbsp;: \n";
		$lignes_FB.="</td>\n";
		$lignes_FB.="<td>\n";
		$lignes_FB.="<span class = \"bold\">".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1]."</span>";
		$lignes_FB.="</td>\n";
		$lignes_FB.="</tr>\n";
	}
	elseif($user_statut=='responsable') {
		$tab_tmp_ele=get_enfants_from_resp_login($user_login);
		$chaine_enfants="";
		if(count($tab_tmp_ele)>0) {
			$chaine_enfants=$tab_tmp_ele[1];
			$tab_tmp_info_classes=get_noms_classes_from_ele_login($tab_tmp_ele[0]);
			if(count($tab_tmp_info_classes)>0) {
				$chaine_enfants.=" (<em>".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1]."</em>)";
			}
			for($i=3;$i<count($tab_tmp_ele);$i+=2) {
				$chaine_enfants.=", ".$tab_tmp_ele[$i];
				unset($tab_tmp_info_classes);
				$tab_tmp_info_classes=get_noms_classes_from_ele_login($tab_tmp_ele[$i-1]);
				if(count($tab_tmp_info_classes)>0) {
					$chaine_enfants.=" (<em>".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1]."</em>)";
				}
			}
		}
		$lignes_FB.="<tr>\n";
		$lignes_FB.="<td>\n";
		$lignes_FB.="Responsable de&nbsp;: \n";
		$lignes_FB.="</td>\n";
		$lignes_FB.="<td>\n";
		$lignes_FB.="<span class = \"bold\">$chaine_enfants</span>";
		$lignes_FB.="</td>\n";
		$lignes_FB.="</tr>\n";
	}

	$lignes_FB.="</table>\n";

	if($affiche_adresse_resp=='y') {
		$lignes_FB.="</div>\n";

		// Pour que le texte de la fiche bienvenue ne remonte pas au delà de l'adresse
		$lignes_FB.="<div style='clear: both; font-size: xx-small;'>&nbsp;</div>\n";
	}

	// La fiche bienvenue:
	$lignes_FB.=$impression;

	if($impression=='') {
		$lignes_FB.="<div class='info_fiche_bienvenue'><div align='center'>Information (<i>non imprimée</i>) : La fiche bienvenue pour <b
	>$user_statut</b> n'est pas renseignée.<br />Vous pouvez paramétrer les fiches bienvenue à la page suivante&nbsp;: <a href='../gestion/modify_impression.php?fiche=";
		if($user_statut=='responsable') {$lignes_FB.='responsables';}
		elseif($user_statut=='eleve') {$lignes_FB.='eleves';}
		else {$lignes_FB.='personnels';}
		$lignes_FB.="' target='_blank'>Clic</a></div></div>\n";

	}

	if($mode_retour=="return") {
		return $lignes_FB;
	}
	else {
		echo $lignes_FB;
	}
}

if(is_array($user_login)) {
	if((isset($mode))&&($mode=='responsable')) {
		$nb_fiches=getSettingValue("ImpressionNombreParent");
	}
	elseif((isset($mode))&&($mode=='eleve')) {
		$nb_fiches=getSettingValue("ImpressionNombreEleve");
	}
	else {
		$nb_fiches=getSettingValue("ImpressionNombre");
	}

	$saut=1;
	for($i=0;$i<count($user_login);$i++) {
		if(isset($mot_de_passe[$i])) {
			fiche_bienvenue($user_login[$i], $mot_de_passe[$i]);
		}
		else {
			fiche_bienvenue($user_login[$i]);
		}

		// Saut de page toutes les $nb_fiches fiches
		if ($saut==$nb_fiches) {
			echo "<p class='saut'>&nbsp;</p>\n";
			$saut=1;
		}
		else {
			$saut++;
		}
	}
}
else {

	//++++++++++++++++++++++++++++++
	$mail_user=get_mail_user($user_login);

	echo "<div id='div_compte_rendu_envoi_mail' style='text-align:center;' class='noprint'></div>\n";

	echo "<div id='lien_mail' style='float:right; width:16px; display:none' class='noprint'><a href=\"javascript:afficher_div('div_envoi_FB_par_mail','y',10,10)\" title=\"Envoyer par mail la Fiche Bienvenue de $user_login.\"><img src='../images/icons/courrier_envoi.png' class='icon16' alt='Mail' /></a></div>
	<script type='text/javascript'>document.getElementById('lien_mail').style.display=''</script>\n";
	//echo "</div>\n";

	$titre_infobulle="Envoi Fiche Bienvenue par mail";
	$texte_infobulle="<form action='".$_SERVER['PHP_SELF']."' name='form_envoi_fb_mail' method='post'>
<p>Fiche bienvenue de <strong>$user_login</strong></p>
<input type='hidden' name='envoi_mail' value='y' />
<input type='hidden' name='user_login' value='$user_login' />";

	if(isset($mot_de_passe)) {
		$texte_infobulle.="
<input type='hidden' name='mot_de_passe' value='$mot_de_passe' />";
	}

	$texte_infobulle.="
<p>Précisez à quelle adresse vous souhaitez envoyer la fiche bienvenue&nbsp;:<br />
Mail&nbsp;:&nbsp;<input type='text' name='mail_dest' value='$mail_user' />
<input type='submit' value='Envoyer' id='button_submit_form_envoi_fb_mail' onclick='afficher_envoi_mail_en_cours()' />
<img src='../images/spinner.gif' class='icon16' title='Envoi en cours' alt='Envoi en cours' style='display:none' id='img_envoi_fb_mail' />
</form>
<script type='text/javascript'>
	function afficher_envoi_mail_en_cours() {
		document.getElementById('button_submit_form_envoi_fb_mail').style.display='none';
		document.getElementById('img_envoi_fb_mail').style.display='';
	}
</script>";
	$tabdiv_infobulle[]=creer_div_infobulle('div_envoi_FB_par_mail',$titre_infobulle,"",$texte_infobulle,"",30,0,'y','y','n','n');
	//++++++++++++++++++++++++++++++
	if(isset($mot_de_passe)) {
		$lignes_FB=fiche_bienvenue($user_login, $mot_de_passe, "return");
	}
	else {
		$lignes_FB=fiche_bienvenue($user_login, NULL, "return");
	}
	echo $lignes_FB;
	//++++++++++++++++++++++++++++++
	$mail_dest=isset($_POST['mail_dest']) ? $_POST['mail_dest'] : NULL;
	$envoi_mail=isset($_POST['envoi_mail']) ? $_POST['envoi_mail'] : "n";

	if($envoi_mail=="y") {
		if(!check_mail($_POST['mail_dest'])) {
			$message="L'adresse mail choisie '".$_POST['mail_dest']."' est invalide.";
			echo "<p style='color:red; text-align:center;' class='noprint'>$message</p>
			<script type='text/javascript'>
				document.getElementById('div_compte_rendu_envoi_mail').innerHTML=\"<span style='color:red'>$message</span>\";
			</script>\n";
		}
		else {
			$sujet="Fiche Bienvenue Gepi";
			$message="Bonjour(soir),\nVoici votre Fiche Bienvenue Gepi :\n".$lignes_FB;
			$destinataire=$_POST['mail_dest'];
			$header_suppl="";
			if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
				$header_suppl.="Bcc:".$_SESSION['email']."\r\n";
			}
			$envoi=envoi_mail($sujet, $message, $destinataire, $header_suppl, "html");
			if($envoi) {
				$message="La Fiche Bienvenue a été expédié à l'adresse mail choisie '".$_POST['mail_dest']."'.";
				echo "<p style='color:green; text-align:center;' class='noprint'>$message</p>
				<script type='text/javascript'>
					document.getElementById('div_compte_rendu_envoi_mail').innerHTML=\"<span style='color:green'>$message</span>\";
				</script>\n";
			}
			else {
				$message="Echec de l'envoi de la Fiche Bienvenue à l'adresse mail choisie '".$_POST['mail_dest']."'.";
				echo "<p style='color:red; text-align:center;' class='noprint'>$message</p>
				<script type='text/javascript'>
					document.getElementById('div_compte_rendu_envoi_mail').innerHTML=\"<span style='color:red'>$message</span>\";
				</script>\n";
			}
		}
	}
	//++++++++++++++++++++++++++++++

}

echo "<style type='text/css'>
@media screen{
	.info_fiche_bienvenue {
			width: 100%;
			height: 50px;
			border:1px solid red;
			background-color: white;
			text-align: center;
	}
}

@media print{
	.info_fiche_bienvenue {
		display:none;
	}

	.noprint {
		display:none;
	}
}
</style>\n";

require("../lib/footer.inc.php");
?>
