<?php
/*
* $Id$
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Laurent Viénot-Hauger
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_notanet/verif_saisies.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_notanet/verif_saisies.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Notanet: Verification avant impression des fiches brevet',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


$type_brevet=isset($_POST["type_brevet"]) ? $_POST["type_brevet"] :(isset($_GET["type_brevet"]) ? $_GET["type_brevet"] : NULL);
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :NULL);

//$check_notes=isset($_POST["check_notes"]) ? $_POST["check_notes"] : "n";
$check_app=isset($_POST["check_app"]) ? $_POST["check_app"] : "n";
$check_avis=isset($_POST["check_avis"]) ? $_POST["check_avis"] : "n";


$style_specifique="mod_notanet/mod_notanet";
//$themessage = 'Des appréciations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//$message_enregistrement = "Les modifications ont été enregistrées !";
//**************** EN-TETE *****************
$titre_page = "Fiches brevet | Vérification des saisies";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

$tmp_timeout=(getSettingValue("sessionMaxLength"))*60;

// Bibliothèque pour Notanet et Fiches brevet
include("lib_brevets.php");

echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='../accueil.php'".insert_confirm_abandon().">Accueil</a> | <a href='index.php'".insert_confirm_abandon().">Retour à l'accueil Notanet</a>";

$sql="SELECT DISTINCT type_brevet FROM notanet_ele_type ORDER BY type_brevet";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association élève/type de brevet n'a encore été réalisée.<br />Commencez par <a href='select_eleves.php'>sélectionner les élèves</a></p>\n";

	require("../lib/footer.inc.php");
	die();
}

$sql="SELECT DISTINCT type_brevet FROM notanet_corresp ORDER BY type_brevet";
$res=mysql_query($sql);
$nb_types_brevets=mysql_num_rows($res);
if($nb_types_brevets==0) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association matières/type de brevet n'a encore été réalisée.<br />Commencez par <a href='select_matieres.php'>sélectionner les matières</a></p>\n";

	require("../lib/footer.inc.php");
	die();
}

if($nb_types_brevets==1) {
	$type_brevet=mysql_result($res, 0, 'type_brevet');
}

if(!isset($type_brevet)) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Pour quel brevet souhaitez-vous contrôler les saisies&nbsp;?";
	echo "</p>\n";
	echo "<ul>\n";
	while($lig=mysql_fetch_object($res)) {
		echo "<li><a href='".$_SERVER['PHP_SELF']."?type_brevet=".$lig->type_brevet."'>Contrôler les saisies pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
	}
	echo "</ul>\n";

	require("../lib/footer.inc.php");
	die();
}

if($nb_types_brevets>1) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre type de brevet</a>";
}

//if ((!isset($id_classe))||(count($id_classe)==0)||(($check_notes=='n')&&($check_app=='n')&&($check_avis=='n'))) {
if ((!isset($id_classe))||(count($id_classe)==0)||(($check_app=='n')&&($check_avis=='n'))) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<h3>Choix des classes à contrôler avant impression des fiches pour le brevet $tab_type_brevet[$type_brevet]</h3>\n";

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form_choix_classe' method='post'>\n";

	//echo "<input type='hidden' name='choix1' value='export' />\n";
	echo "<input type='hidden' name='type_brevet' value='$type_brevet' />\n";
	echo "<p>Sélectionnez les classes&nbsp;: </p>\n";
	echo "<blockquote>\n";
	$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	$nombre_lignes = mysql_num_rows($call_data);
	//echo "<select name='id_classe[]' multiple='true' size='10'>\n";

	$nb_class_par_colonne=round($nombre_lignes/3);
	echo "<table width='100%'>\n";
	echo "<tr valign='top' align='center'>\n";

	$i = '0';

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	$i = 0;
	while ($i < $nombre_lignes){
		$classe = mysql_result($call_data, $i, "classe");
		$ide_classe = mysql_result($call_data, $i, "id");
		//echo "<a href='eleve_classe.php?id_classe=$ide_classe'>$classe</a><br />\n";
		//echo "<option value='$ide_classe'>$classe</option>\n";

		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
		}

		echo "<label for='classe_$ide_classe' style='cursor: pointer;'>\n";
		echo "<input type='checkbox' name='id_classe[]' id='classe_$ide_classe' value='$ide_classe' ";

		$sql="SELECT 1=1 FROM eleves e, j_eleves_classes jec, notanet_ele_type n
				WHERE jec.login=e.login AND
						e.login=n.login AND
						jec.id_classe='$ide_classe' AND
						n.type_brevet='$type_brevet'
				ORDER BY e.nom,e.prenom";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)!=0) {
			echo "checked ";
		}

		echo "/>\n";
		echo " $classe<br />";
		echo "</label>\n";

		$i++;
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<p>Contrôler le bon remplissage&nbsp;:";
	//echo "<br />\n<input type='checkbox' name='check_notes' id='check_notes' value='y' checked /><label for='check_notes'>les notes</label>\n";
	echo "<br />\n<input type='checkbox' name='check_app' id='check_app' value='y' checked /><label for='check_app'>des appréciations des professeurs</label>\n";
	echo "<br />\n<input type='checkbox' name='check_avis' id='check_avis' value='y' checked /><label for='check_avis'>de l'avis du chef d'établissement</label>\n";
	echo "</p>\n";

	//echo "</select><br />\n";
	echo "<p align='center'><input type='submit' name='choix_classe' value='Envoyer' /></p>\n";
	echo "</blockquote>\n";
	//echo "</p>\n";
	echo "</form>\n";

	require("../lib/footer.inc.php");
	die();
}

echo " | <a href='".$_SERVER['PHP_SELF']."?type_brevet=$type_brevet'>Choisir d'autres classes</a>";
echo "</p>\n";
echo "</div>\n";

//=========================================================
unset($tab_mat);
//$sql="SELECT * FROM notanet_corresp ORDER BY type_brevet;";
$sql="SELECT DISTINCT type_brevet FROM notanet_corresp ORDER BY type_brevet;";
$res1=mysql_query($sql);
while($lig1=mysql_fetch_object($res1)) {
	$sql="SELECT * FROM notanet_corresp WHERE type_brevet='$lig1->type_brevet';";
	//echo "$sql<br />";
	$res2=mysql_query($sql);

	unset($id_matiere);
	unset($statut_matiere);

	while($lig2=mysql_fetch_object($res2)) {
		$id_matiere[$lig2->id_mat][]=$lig2->matiere;
		//$statut_matiere[$lig2->id_mat][]=$lig2->statut;
		$statut_matiere[$lig2->id_mat]=$lig2->statut;
	}

	$tab_mat[$lig1->type_brevet]=array();
	$tab_mat[$lig1->type_brevet]['id_matiere']=$id_matiere;
	$tab_mat[$lig1->type_brevet]['statut_matiere']=$statut_matiere;
}
/*
echo "<pre>";
echo print_r($tab_mat);
echo "</pre>";

echo "<hr />";
*/
//=========================================================

$tabmatieres=tabmatieres($type_brevet);
//echo "count(\$tabmatieres)=".count($tabmatieres)."<br />";
/*
echo "<pre>";
echo print_r($tabmatieres);
echo "</pre>";

echo "<hr />";
*/
$id_matiere=$tab_mat[$type_brevet]['id_matiere'];
$statut_matiere=$tab_mat[$type_brevet]['statut_matiere'];

for($i=0;$i<count($id_classe);$i++) {
	$current_classe=get_class_from_id($id_classe[$i]);
	echo "<p class='bold'>".$current_classe."</p>\n";
	echo "<div style='margin-left:2em;'>\n";

	$tab_ele_manques=array();
	$tab_prof_manques=array();

	$sql="SELECT DISTINCT jec.login, e.nom, e.prenom FROM j_eleves_classes jec, notanet_ele_type net, eleves e WHERE net.login=jec.login AND e.login=jec.login AND jec.id_classe='".$id_classe[$i]."' ORDER BY e.nom, e.prenom;";
	$res_ele=mysql_query($sql);
	while($lig_ele=mysql_fetch_object($res_ele)) {
		//$cpt_notes=0;
		$cpt_app=0;
		$cpt_avis=0;

		for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++) {
			if($tabmatieres[$j][0]!="") {
				/*
				// Test moyenne
				if($check_notes=='y') {
					//$sql="SELECT round(avg(mn.note),1) as moyenne FROM matieres_notes mn, j_groupes_matieres jgm WHERE (jgm.id_matiere='".$id_matiere[$j][$k]."' AND mn.login='".$tab_ele['login']."' AND mn.statut ='' AND mn.id_groupe=jgm.id_groupe)";
					$sql="SELECT * FROM notanet WHERE login='".$lig_ele->login."' AND id_mat='$j' AND id_classe='".$id_classe[$i]."';";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)==0) {
						$tab_ele_manques[$lig_ele->login]['notes'][$cpt_notes]=$tab_mat[$type_brevet]['id_matiere'][$j][0];
						for($loop=1;$loop<count($tab_mat[$type_brevet]['id_matiere'][$j]);$loop++) {
							$tab_ele_manques[$lig_ele->login]['notes'][$cpt_notes].=", ".$tab_mat[$type_brevet]['id_matiere'][$j][$loop];
						}
						$cpt_notes++;
					}
				}
				*/

				$sql="SELECT matiere FROM notanet WHERE login='".$lig_ele->login."' AND id_mat='$j' AND id_classe='".$id_classe[$i]."';";
				//echo "$sql<br />";
				$res_notanet=mysql_query($sql);
				if(mysql_num_rows($res_notanet)>0) {
					// Test appréciation
					if($check_app=='y') {
						$matiere=mysql_result($res_notanet, 0, 'matiere');
						//$sql="SELECT * FROM notanet_app WHERE login='".$lig_ele->login."' AND id_mat='$j' AND matiere='".$matiere."' AND appreciation!='';";
						// id_mat n'est pas rempli dans notanet_app
						// Est-ce qu'on pourrait associer deux fois une matière Gepi à des matières notanet différentes?
						// N'est-ce pas le cas dans HIGEO/EDCIV
						$sql="SELECT * FROM notanet_app WHERE login='".$lig_ele->login."' AND matiere='".$matiere."' AND appreciation!='';";
						//echo "$sql<br />";
						$res_na=mysql_query($sql);
						if(mysql_num_rows($res_na)==0) {
							/*
							$tab_ele_manques[$lig_ele->login]['nom_prenom']=casse_mot($lig_ele->nom,'maj')." ".casse_mot($lig_ele->prenom,'majf2');
							$tab_ele_manques[$lig_ele->login]['app'][$cpt_app]=$tab_mat[$type_brevet]['id_matiere'][$j][0];
							for($loop=1;$loop<count($tab_mat[$type_brevet]['id_matiere'][$j]);$loop++) {
								$tab_ele_manques[$lig_ele->login]['app'][$cpt_app].=", ".$tab_mat[$type_brevet]['id_matiere'][$j][$loop];
							}
							*/

							$sql="SELECT DISTINCT g.* FROM groupes g,
																	j_groupes_classes jgc,
																	j_groupes_matieres jgm,
																	j_eleves_groupes jeg,
																	notanet n
																WHERE g.id=jgc.id_groupe AND
																	jgc.id_classe=n.id_classe AND
																	jgc.id_classe='".$id_classe[$i]."' AND
																	jgc.id_groupe=jgm.id_groupe AND
																	jgm.id_matiere=n.matiere AND
																	n.matiere='$matiere' AND
																	jeg.id_groupe=jgc.id_groupe AND
																	jeg.login='$lig_ele->login'
																ORDER BY g.name;";
							//echo "$sql<br />";
							$res_grp=mysql_query($sql);
							if(mysql_num_rows($res_grp)>0) {
								$tab_ele_manques[$lig_ele->login]['app'][$cpt_app]['matiere']=$matiere;

								$cpt_grp=0;
								$tab_ele_manques[$lig_ele->login]['nom_prenom']=casse_mot($lig_ele->nom,'maj')." ".casse_mot($lig_ele->prenom,'majf2');
								while($lig_grp=mysql_fetch_object($res_grp)) {
									$tab_ele_manques[$lig_ele->login]['app'][$cpt_app]['groupe'][$cpt_grp]['id_groupe']=$lig_grp->id;
									$tab_ele_manques[$lig_ele->login]['app'][$cpt_app]['groupe'][$cpt_grp]['name']=$lig_grp->name;
									$tab_ele_manques[$lig_ele->login]['app'][$cpt_app]['groupe'][$cpt_grp]['description']=$lig_grp->description;

									$cpt_prof=0;
									$sql="SELECT u.login, u.nom, u.prenom, u.civilite, u.email FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.login=u.login AND jgp.id_groupe='$lig_grp->id' ORDER BY u.nom, u.prenom;";
									//echo "$sql<br />";
									$res_prof=mysql_query($sql);
									while($lig_prof=mysql_fetch_object($res_prof)) {
										$tab_ele_manques[$lig_ele->login]['app'][$cpt_app]['groupe'][$cpt_grp]['prof'][$cpt_prof]['login']=$lig_prof->login;
										$tab_ele_manques[$lig_ele->login]['app'][$cpt_app]['groupe'][$cpt_grp]['prof'][$cpt_prof]['nom']=$lig_prof->nom;
										$tab_ele_manques[$lig_ele->login]['app'][$cpt_app]['groupe'][$cpt_grp]['prof'][$cpt_prof]['prenom']=$lig_prof->prenom;
										$tab_ele_manques[$lig_ele->login]['app'][$cpt_app]['groupe'][$cpt_grp]['prof'][$cpt_prof]['civilite']=$lig_prof->civilite;
										$tab_ele_manques[$lig_ele->login]['app'][$cpt_app]['groupe'][$cpt_grp]['prof'][$cpt_prof]['email']=$lig_prof->email;
										if($cpt_prof==0) {
											$tab_ele_manques[$lig_ele->login]['app'][$cpt_app]['groupe'][$cpt_grp]['proflist_string']="";
										}
										else {
											$tab_ele_manques[$lig_ele->login]['app'][$cpt_app]['groupe'][$cpt_grp]['proflist_string'].=", ";
										}
										$tab_ele_manques[$lig_ele->login]['app'][$cpt_app]['groupe'][$cpt_grp]['proflist_string'].=$lig_prof->civilite." ".casse_mot($lig_prof->nom,'maj')." ".casse_mot($lig_prof->prenom,'majf2');

										if(!isset($tab_prof_manques[$lig_prof->login])) {
											$tab_prof_manques[$lig_prof->login]=array();
											$tab_prof_manques[$lig_prof->login]['nom']=$lig_prof->nom;
											$tab_prof_manques[$lig_prof->login]['prenom']=$lig_prof->prenom;
											$tab_prof_manques[$lig_prof->login]['civilite']=$lig_prof->civilite;
											$tab_prof_manques[$lig_prof->login]['email']=$lig_prof->email;
										}
										$tab_prof_manques[$lig_prof->login]['groupe'][$lig_grp->id]['name']=$lig_grp->name;
										$tab_prof_manques[$lig_prof->login]['groupe'][$lig_grp->id]['description']=$lig_grp->description;
										$tab_prof_manques[$lig_prof->login]['groupe'][$lig_grp->id]['name']=$lig_grp->name;

										$tab_prof_manques[$lig_prof->login]['groupe'][$lig_grp->id]['eleve'][]=$tab_ele_manques[$lig_ele->login]['nom_prenom'];
										$tab_prof_manques[$lig_prof->login]['groupe'][$lig_grp->id]['eleve_classe'][]=$current_classe;

										$cpt_prof++;
									}
									$cpt_grp++;
								}
								$cpt_app++;
							}
						}
					}
				}
			}
		}

		// Test avis
		$sql="SELECT 1=1 FROM notanet_avis WHERE login='".$lig_ele->login."' AND (favorable!='' OR avis!='');";
		$test_avis=mysql_query($sql);
		if(mysql_num_rows($test_avis)==0) {
			if(!isset($tab_ele_manques[$lig_ele->login]['nom_prenom'])) {
				$tab_ele_manques[$lig_ele->login]['nom_prenom']=casse_mot($lig_ele->nom,'maj')." ".casse_mot($lig_ele->prenom,'majf2');
			}
			$tab_ele_manques[$lig_ele->login]['avis']='';
		}
	}

	if(count($tab_ele_manques)>0) {
		echo "<p>Il manque des saisies&nbsp;:</p>\n";
		echo "<ul>\n";
		foreach($tab_ele_manques as $login_eleve => $tab_ele_m) {
			echo "<li>\n";
			echo "<p>".$tab_ele_manques[$login_eleve]['nom_prenom']."&nbsp;: </p>\n";
				echo "<ul>\n";
					if(isset($tab_ele_manques[$login_eleve]['app']	)) {
						echo "<li>\n";
						echo "<strong>Appréciations&nbsp;:</strong> ";
						for($k=0;$k<count($tab_ele_manques[$login_eleve]['app']);$k++) {
							if($k>0) {echo ", ";}
							//echo $tab_ele_manques[$login_eleve]['app'][$k]['matiere'];
							//echo " (<em>";
							for($kk=0;$kk<count($tab_ele_manques[$login_eleve]['app'][$k]['groupe']);$kk++) {
								echo "<span title='".$tab_ele_manques[$login_eleve]['app'][$k]['groupe'][$kk]['description']." (".$tab_ele_manques[$login_eleve]['app'][$k]['groupe'][$kk]['proflist_string'].")'>".$tab_ele_manques[$login_eleve]['app'][$k]['groupe'][$kk]['name']."</span>";
							}
							//echo " </em>)";
						}
						echo "</li>\n";
					}
					if(isset($tab_ele_manques[$login_eleve]['avis'])) {
						echo "<li>\n";
						echo "<strong>Avis du chef d'établissement</strong>";
						echo "</li>\n";
					}
				echo "</ul>\n";
			echo "</li>\n";
		}
		echo "</ul>\n";
/*
echo "<pre>";
echo print_r($tab_ele_manques);
echo "</pre>";
*/
	}

/*
echo "<pre>";
echo print_r($tab_prof_manques);
echo "</pre>";
*/
	if($check_app=='y') {
		if(count($tab_prof_manques)==0) {
			echo "<p>Tous les professeurs ont rempli les appréciations.</p>\n";
		}
		else {
			echo "<p>Voulez-vous envoyer un mail au(x) professeur(s) qui n'ont pas encore rempli leurs appréciations&nbsp;?</p>\n";

			$tab_num_mail=array();

			$num=0;

			//echo "<div style='border: 1px solid black'>";
			echo "<p class='bold'>Récapitulatif&nbsp;:</p>\n";
			echo "<table class='boireaus' summary=\"Courriels\">\n";
			$alt=1;

			//$tab_alerte_prof[$login_prof]['groupe'][$group_id]['app_manquante'][]
			foreach($tab_prof_manques as $login_prof => $tab_prof) {
				$alt=$alt*(-1);

				$info_prof=$tab_prof_manques[$login_prof]['civilite']." ".casse_mot($tab_prof_manques[$login_prof]['nom'],'maj')." ".casse_mot($tab_prof_manques[$login_prof]['prenom'],'majf2');

				$message="Bonjour(soir) ".$info_prof.",\n\nDes appréciations de Fiches Brevet ne sont pas remplies:\n";
				foreach($tab_prof['groupe'] as $group_id => $tab_group) {
					if(isset($tab_group['name'])) {
						$message.="Appréciation(s) manquante(s) en ".$tab_prof_manques[$login_prof]['groupe'][$group_id]['name']." (".$tab_prof_manques[$login_prof]['groupe'][$group_id]['description'].") pour ";
						for($loop=0;$loop<count($tab_prof_manques[$login_prof]['groupe'][$group_id]['eleve']);$loop++) {
							if($loop>0) {$message.=", ";}
							$message.=$tab_prof_manques[$login_prof]['groupe'][$group_id]['eleve'][$loop];
						}
						$message.=".\n";
					}
				}

				$message.="\nSi ce signalement vous parait être une erreur, merci d'en alerter l'administrateur Gepi.\n";

				$message.="\nDans le cas contraire, je vous serais reconnaissant(e) de bien vouloir les remplir rapidement.\n\nD'avance merci.\n-- \n".civ_nom_prenom($_SESSION['login']);

				echo "<tr class='lig$alt'>\n";
				echo "<td>\n";
				if($tab_prof_manques[$login_prof]['email']!="") {
					if(check_mail($tab_prof_manques[$login_prof]['email'])) {
						$tab_num_mail[]=$num;
						$sujet_mail="[Gepi]: Appreciations des Fiches Brevet manquantes";
						echo "<a href='mailto:".$tab_prof_manques[$login_prof]['email']."?subject=$sujet_mail&amp;body=".rawurlencode($message)."'>".$info_prof."</a>";
						echo "<input type='hidden' name='sujet_$num' id='sujet_$num' value=\"$sujet_mail\" />\n";
						echo "<input type='hidden' name='mail_$num' id='mail_$num' value=\"".$tab_prof_manques[$login_prof]['email']."\" />\n";
					}
				}
				else {
					echo $info_prof;
				}
				echo "</td>\n";
				echo "<td rowspan='2'>\n";
				echo "<textarea name='message_$num' id='message_$num' cols='50' rows='5'>$message</textarea>\n";

				echo "</td>\n";
				echo "</tr>\n";

				echo "<tr class='lig$alt'>\n";
				echo "<td>\n";
				if(!in_array($num, $tab_num_mail)) {
					echo "<span style='color: red;'>Pas de mail</span>";
				}
				else {
					echo "<span id='mail_envoye_$num'><a href='#' onclick=\"envoi_mail($num);return false;\">Envoyer</a></span>";
				}
				echo "</td>\n";
				echo "</tr>\n";

				//echo "<a href='#' onclick=\"envoi_mail($num);return false;\">Envoyer</a>";
				//echo "<br />\n";
				$num++;
			}
			echo "</table>\n";

			echo add_token_field(true);

			echo "<script type='text/javascript'>
	// <![CDATA[
	function envoi_mail(num) {
		csrf_alea=document.getElementById('csrf_alea').value;
		destinataire=document.getElementById('mail_'+num).value;
		sujet_mail=document.getElementById('sujet_'+num).value;
		message=document.getElementById('message_'+num).value;

		//alert(message);
		//alert(encodeURIComponent(message));

		document.getElementById('mail_envoye_'+num).innerHTML=\"<img src='../images/spinner.gif' width='20' height='20' alt='Action en cours d\'exécution' title='Action en cours d\'exécution' />\";

		//new Ajax.Updater($('mail_envoye_'+num),'../bulletin/envoi_mail.php?destinataire='+destinataire+'&sujet_mail='+sujet_mail+'&message='+encodeURIComponent(message)+'&csrf_alea='+csrf_alea,{method: 'get'});

		//message=encodeURIComponent(message);

		new Ajax.Updater($('mail_envoye_'+num),'../bulletin/envoi_mail.php',{method: 'post',
		parameters: {
			destinataire: destinataire,
			sujet_mail: sujet_mail,
			message: message,
			csrf_alea: csrf_alea
		}});

	}
	//]]>
</script>\n";



		}
	}

	echo "</div>\n";
	echo "<br />\n";
}

echo "<p style='color:red'>A FAIRE: SIGNALER SI LE VERROUILLAGE EST FAIT...</p>";
echo "<p style='color:red'>A FAIRE: Récupérer la liste des personnes auxquelles envoyer des mails.</p>";
require("../lib/footer.inc.php");
die();
?>
