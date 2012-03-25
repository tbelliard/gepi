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

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/classes/ajouter_periode.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/classes/ajouter_periode.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Classes: Ajouter des périodes',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$nb_ajout_periodes=isset($_POST['nb_ajout_periodes']) ? $_POST['nb_ajout_periodes'] : NULL;
$nb_periodes_initial=isset($_POST['nb_periodes_initial']) ? $_POST['nb_periodes_initial'] : NULL;

if(!isset($id_classe)) {
	header("Location: index.php?msg=Aucun identifiant de classe n'a été proposé");
	die();
}

if((isset($nb_ajout_periodes))&&(!preg_match('/^[1-9]$/',$nb_ajout_periodes))) {
	unset($nb_ajout_periodes);
	$msg="Nombre de périodes à ajouter invalide.";
}

if((isset($nb_periodes_initial))&&(!preg_match('/^[1-9]$/',$nb_periodes_initial))) {
	unset($nb_periodes_initial);
	$msg="Nombre initial de périodes invalide.";
}

$call_data = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
$classe = mysql_result($call_data, 0, "classe");
$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
$test_periode = mysql_num_rows($periode_query) ;
include "../lib/periodes.inc.php";

// =================================
// AJOUT: boireaus
$chaine_options_classes="";
$sql="SELECT id, classe FROM classes ORDER BY classe";
$res_class_tmp=mysql_query($sql);
if(mysql_num_rows($res_class_tmp)>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;

    $cpt_classe=0;
	$num_classe=-1;

	while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
			$temoin_tmp=1;
			if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		else {
			$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
		}

		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}

		$cpt_classe++;
	}
}
// =================================

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Gestion des classes - Ajout de périodes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

echo "<p class='bold'><a href='periodes.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>\n";

if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe précédente</a>\n";}
if($chaine_options_classes!="") {

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
				document.getElementById('id_classe').selectedIndex=$num_classe;
			}
		}
	}
</script>\n";


	echo " | <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
	echo $chaine_options_classes;
	echo "</select>\n";
}
if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suivante</a>\n";}

//=========================
// AJOUT: boireaus 20081224
$titre="Navigation";
$texte="";

//$texte.="<img src='../images/icons/date.png' alt='' /> <a href='periodes.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Périodes</a><br />";
$texte.="<img src='../images/icons/edit_user.png' alt='' /> <a href='classes_const.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Elèves</a><br />";
$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignements</a><br />";
$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class_grp_lot.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">config.simplifiée</a><br />";
$texte.="<img src='../images/icons/configure.png' alt='' /> <a href='modify_nom_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Paramètres</a>";

$ouvrir_infobulle_nav=getSettingValue("ouvrir_infobulle_nav");

if($ouvrir_infobulle_nav=="y") {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/vert.png' width='16' height='16' /></a></div>\n";
}
else {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/rouge.png' width='16' height='16' /></a></div>\n";
}

$texte.="<script type='text/javascript'>
	// <![CDATA[
	function modif_mode_infobulle_nav() {
		new Ajax.Updater($('save_mode_nav'),'classes_ajax_lib.php?mode=ouvrir_infobulle_nav',{method: 'get'});
	}
	//]]>
</script>\n";

$tabdiv_infobulle[]=creer_div_infobulle('navigation_classe',$titre,"",$texte,"",14,0,'y','y','n','n');

echo " | <a href='#' onclick=\"afficher_div('navigation_classe','y',-100,20);\"";
echo ">";
echo "Navigation";
echo "</a>";
//=========================

echo "</p>\n";
echo "</form>\n";

//=========================================================================
function search_liaisons_classes_via_groupes($id_classe) {
	global $tab_liaisons_classes;

	$sql="SELECT jgc.id_groupe FROM j_groupes_classes jgc WHERE jgc.id_classe='$id_classe';";
	//echo "$sql<br />\n";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$sql="SELECT c.classe, jgc.id_classe, g.* FROM j_groupes_classes jgc, groupes g, classes c WHERE jgc.id_classe!='$id_classe' AND g.id=jgc.id_groupe AND c.id=jgc.id_classe AND jgc.id_groupe='$lig->id_groupe' ORDER BY c.classe;";
			//echo "$sql<br />\n";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				while($lig2=mysql_fetch_object($test)) {
					if(!in_array($lig2->id_classe,$tab_liaisons_classes)) {
						$tab_liaisons_classes[]=$lig2->id_classe;
						search_liaisons_classes_via_groupes($lig2->id_classe);
					}
				}
			}
		}
	}
}
//=========================================================================
if(!isset($nb_ajout_periodes)) {

	$sql="SELECT num_periode FROM periodes WHERE id_classe='".$id_classe."' ORDER BY num_periode DESC LIMIT 1;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p style='color:red'>ANOMALIE&nbsp;: La classe ".$classe." n'a actuellement aucune période.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		$lig=mysql_fetch_object($res);
		$max_per=$lig->num_periode;
	}

	echo "<p class='bold'>Recherche des liaisons directes&nbsp;:</p>\n";
	echo "<blockquote>\n";
	echo "<p>";
	
	$tab_liaisons_classes=array();
	$tab_liaisons_classes[]=$id_classe;
	
	$sql="SELECT jgc.id_groupe FROM j_groupes_classes jgc WHERE jgc.id_classe='$id_classe';";
	//echo "$sql<br />\n";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "Aucune liaison n'a été trouvée.<br />L'ajout de période ne présente donc pas de difficulté.</p>\n";
	}
	else {
		while($lig=mysql_fetch_object($res)) {
			$sql="SELECT c.classe, jgc.id_classe, g.* FROM j_groupes_classes jgc, groupes g, classes c WHERE jgc.id_classe!='$id_classe' AND g.id=jgc.id_groupe AND c.id=jgc.id_classe AND jgc.id_groupe='$lig->id_groupe' ORDER BY c.classe;";
			//echo "$sql<br />\n";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				$cpt=0;
				while($lig2=mysql_fetch_object($test)) {
					if($cpt==0) {
						echo "<b>$lig2->name (<i>$lig2->description</i>)&nbsp;:</b> ";
					}
					echo " $lig2->classe";
					$cpt++;
				}
				echo "<br />\n";
			}
		}
	}
	echo "</blockquote>\n";
	
	search_liaisons_classes_via_groupes($id_classe);
	
	if(count($tab_liaisons_classes)>0) {
		echo "<p>La classe <b>$classe</b> est liée (<i>de façon directe ou indirecte (via une autre classe)</i>) aux classes suivantes&nbsp;: ";
		$cpt=0;
		for($i=0;$i<count($tab_liaisons_classes);$i++) {
			if($tab_liaisons_classes[$i]!=$id_classe) {
				if($cpt>0) {echo ", ";}
				echo get_class_from_id($tab_liaisons_classes[$i]);
				$cpt++;
			}
		}

		echo "<p>La classe de <b>$classe</b> a actuellement <b>$max_per</b> périodes.</p>\n";
		echo "<p>Combien de périodes voulez-vous ajouter pour <b>$classe</b> et la ou les classes liées?</p>\n";
	}
	else {
		echo "<p>La classe de <b>$classe</b> a actuellement <b>$max_per</b> périodes.</p>\n";
		echo "<p>Combien de périodes voulez-vous ajouter pour <b>$classe</b>?</p>\n";
	}
	
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	
	echo "<p>Nombre de périodes à ajouter&nbsp;: <select name='nb_ajout_periodes'>\n";
	for($i=1;$i<10;$i++) {
		echo "<option value='$i'>$i</option>\n";
	}
	echo "</select>\n";
	//echo "<br />\n";

	echo " <input type='hidden' name='id_classe' value='$id_classe' />\n";
	echo " <input type='hidden' name='nb_periodes_initial' value='$max_per' />\n";
	echo " <input type='submit' name='Ajouter' value='Ajouter' />\n";
	echo "</p>\n";
	echo "</form>\n";
	
	echo "<p><br /></p>\n";
	
	echo "<p class='bold'>Remarques&nbsp;:</p>\n";
	echo "<div style='margin-left: 3em;'>\n";
		echo "<p>L'ajout de période présente une difficulté lorsqu'il y a des enseignements/groupes à cheval sur plusieurs classes.<br />Deux classes partageant un enseignement doivent avoir le même nombre de périodes.<br />Si vous ajoutez des périodes à la classe ".$classe.", il faudra&nbsp:</p>\n";
		echo "<ul>\n";
			echo "<li>soit ajouter le même nombre de périodes aux classes liées à $classe</li>\n";
			echo "<li>soit rompre les liaisons&nbsp;:<br />Cela signifierait que vous auriez alors deux enseignements distincts pour $classe et une classe partageant l'enseignement.<br />Pour le professeur les conséquences sont les suivantes&nbsp;:<br />\n";
				echo "<ul>\n";
					echo "<li>pour saisir les résultats d'un devoir, il faudra créer un devoir dans chacun des deux enseignements et y saisir les notes</li>\n";
					echo "<li>la moyenne du groupe d'élève ne sera pas calculée; il y aura deux moyennes&nbsp: celles des deux enseignements<br />Même chose pour les moyennes min et max.</li>\n";
					echo "<li>Pour les notes existantes, il faut créer un nouveau groupe, un nouveau carnet de notes, cloner les devoirs et boites pour y transférer les notes et provoquer le recalcul des moyennes de conteneurs.<br />Les saisies de cahier de textes, d'emploi du temps doivent être dupliquées, les saisies antérieures d'absences peuvent-elles être perturbées (?) ou l'association n'est-elle que élève/jour_heures_absence (?),...</li>";
				echo "</ul>\n";
				echo "<span style='color:red'>La deuxième solution n'est pas implémentée pour le moment</span>\n";
			echo "</li>\n";
		echo "</ul>\n";
	echo "</div>\n";
}
//=========================================================================
else {
	check_token(false);

	$tab_liaisons_classes=array();
	$tab_liaisons_classes[]=$id_classe;
	search_liaisons_classes_via_groupes($id_classe);

	for($i=0;$i<count($tab_liaisons_classes);$i++) {
		// Classe par classe
			// Ajouter une période dans la table 'periodes'... à nommer...
			// Insérer des enregistrements pour
				// j_eleves_classes (vérifier qu'un élève n'est pas déjà dans une autre classe pour le même numéro de période... un élève peut-il passer d'une classe à 2 périodes à une classe à 3 périodes... pb de chevauchement pour les absences...)
				// j_eleves_groupes en bouclant sur les groupes de la classe
		$id_classe_courant=$tab_liaisons_classes[$i];
		$classe_courante=get_class_from_id($tab_liaisons_classes[$i]);

		echo "<p class='bold'>Traitement de la classe $classe_courante&nbsp;:</p>\n";
		echo "<blockquote>\n";

		$sql="SELECT num_periode FROM periodes WHERE id_classe='".$id_classe_courant."' ORDER BY num_periode DESC LIMIT 1;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p style='color:red'>ANOMALIE&nbsp;: La classe ".$classe_courante." n'a actuellement aucune période.</p>\n";
		}
		else {
			//$lig=mysql_fetch_object($res);
			//$num_periode=$lig->num_periode;

			$num_periode=$nb_periodes_initial;

			// Récupération de la liste des élèves de la classe pour la dernière période
			$tab_ele=array();
			$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='".$id_classe_courant."' AND periode='$num_periode';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				echo "<p>Aucun élève n'est inscrit dans la classe ".$classe_courante." sur la période $num_periode.</p>\n";
			}
			else {
				while($lig=mysql_fetch_object($res)) {
					$tab_ele[]=$lig->login;
				}
			}

			$tab_group=array();
			$sql="SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe_courant'";
			$res_liste_grp_classe=mysql_query($sql);
			if(mysql_num_rows($res_liste_grp_classe)>0){
				while($lig_tmp=mysql_fetch_object($res_liste_grp_classe)){
					$tab_group[$lig_tmp->id_groupe]=array();
					$sql="SELECT DISTINCT login FROM j_eleves_groupes WHERE id_groupe='$lig_tmp->id_groupe' AND periode='$num_periode'";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0){
						while($lig_tmp2=mysql_fetch_object($test)) {
							$tab_group[$lig_tmp->id_groupe][]=$lig_tmp2->login;
						}
					}
				}
			}

			// Boucle sur le nombre de périodes à ajouter
			for($loop=0;$loop<$nb_ajout_periodes;$loop++) {
				$num_periode++;

				echo "Création de la période $num_periode&nbsp;: ";
				$sql="INSERT INTO periodes SET nom_periode='Période $num_periode', num_periode='$num_periode', verouiller='O', id_classe='".$id_classe_courant."', date_verrouillage='0000-00-00 00:00:00';";
				//echo "$sql<br />\n";
				$res=mysql_query($sql);

				if(!$res) {
					echo "<span style='color:red'>ECHEC</span>";
					echo "<br />\n";
				}
				else {
					echo "<span style='color:green'>SUCCES</span>";
					echo "<br />\n";
					echo "<blockquote>\n";

					if(count($tab_ele)>0) {
						echo "Ajout des élèves dans la classe&nbsp;:";
						for($j=0;$j<count($tab_ele);$j++) {
							if($j>0) {echo ", ";}
							$sql="INSERT INTO j_eleves_classes SET login='$tab_ele[$j]', id_classe='$id_classe_courant', periode='$num_periode';";
							//echo "$sql<br />\n";
							$res=mysql_query($sql);
							if(!$res) {
								echo "<span style='color:red'>$tab_ele[$j]</span> ";
							}
							else {
								echo "<span style='color:green'>$tab_ele[$j]</span> ";
							}
						}
						echo "<br />\n";
					}

					foreach($tab_group as $id_groupe => $tab_ele_groupe) {
						$tab_champs=array();
						$tmp_group=get_group($id_groupe,$tab_champs);

						echo "Inscription dans l'enseignement ".$tmp_group['name']." (<i>".$tmp_group['description']."</i>) (<i>n°$id_groupe</i>)&nbsp;: ";
						$kk=0;
						for($k=0;$k<count($tab_ele_groupe);$k++) {
							$sql="SELECT 1=1 FROM j_eleves_groupes WHERE login='$tab_ele_groupe[$k]' AND id_groupe='$id_groupe' AND periode='$num_periode';";
							//echo "$sql<br />\n";
							$res=mysql_query($sql);
							if(mysql_num_rows($res)>0) {
								echo "<span style='color:blue'>$tab_ele_groupe[$k]</span> ";
							}
							else {
								if($kk>0) {echo ", ";}
								$sql="INSERT INTO j_eleves_groupes SET login='$tab_ele_groupe[$k]', id_groupe='$id_groupe', periode='$num_periode';";
								$res=mysql_query($sql);
								if(!$res) {
									echo "<span style='color:red'>$tab_ele_groupe[$k]</span> ";
								}
								else {
									echo "<span style='color:green'>$tab_ele_groupe[$k]</span> ";
								}
								$kk++;
							}
						}
						echo "<br />\n";
					}
					echo "</blockquote>\n";
				}
			}
		}
		echo "</blockquote>\n";
	}

	echo "<p class='bold'>Terminé.</p>\n";

	if((mb_substr(getSettingValue('autorise_edt_tous'),0,1)=='y')||(mb_substr(getSettingValue('autorise_edt_admin'),0,1)=='y')||(mb_substr(getSettingValue('autorise_edt_eleve'),0,1)=='y')) {
		echo "<p><br /></p>\n";
		echo "<p>Pensez à contrôler que vous avez bien défini les dates de périodes dans le <a href='../edt_organisation/edt_calendrier.php'>calendrier</a>.</p>\n";
		echo "<p><br /></p>\n";
	}
}
//=========================================================================

require("../lib/footer.inc.php");

?>