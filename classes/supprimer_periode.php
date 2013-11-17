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

$sql="SELECT 1=1 FROM droits WHERE id='/classes/supprimer_periode.php';";
$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/classes/supprimer_periode.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Classes: Supprimer des périodes',
statut='';";
$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$suppr_periode=isset($_POST['suppr_periode']) ? $_POST['suppr_periode'] : NULL;

if(!isset($id_classe)) {
	header("Location: index.php?msg=Aucun identifiant de classe n'a été proposé");
	die();
}

$call_data = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT classe FROM classes WHERE id = '$id_classe'");
$classe = mysql_result($call_data, 0, "classe");
$periode_query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM periodes WHERE id_classe = '$id_classe'");
$test_periode = mysqli_num_rows($periode_query) ;
include "../lib/periodes.inc.php";

// =================================
// AJOUT: boireaus
$chaine_options_classes="";
$sql="SELECT id, classe FROM classes ORDER BY classe";
$res_class_tmp=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if(mysqli_num_rows($res_class_tmp)>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;

    $cpt_classe=0;
	$num_classe=-1;

	while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
			$temoin_tmp=1;
			if($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
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
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$sql="SELECT c.classe, jgc.id_classe, g.* FROM j_groupes_classes jgc, groupes g, classes c WHERE jgc.id_classe!='$id_classe' AND g.id=jgc.id_groupe AND c.id=jgc.id_classe AND jgc.id_groupe='$lig->id_groupe' ORDER BY c.classe;";
			//echo "$sql<br />\n";
			$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($test)>0) {
				while($lig2=mysqli_fetch_object($test)) {
					if(!in_array($lig2->id_classe,$tab_liaisons_classes)) {
						$tab_liaisons_classes[]=$lig2->id_classe;
						search_liaisons_classes_via_groupes($lig2->id_classe);
					}
				}
			}
		}
	}
}

function search_periodes_non_vides($id_classe) {
	global $tab_periode_non_supprimable;

	// Recherche des périodes non vides
	$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe';";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if(!in_array($lig->num_periode, $tab_periode_non_supprimable)) {
				/*
				$sql="SELECT login FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='$lig->num_periode';";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0) {
					while($lig2=mysql_fetch_object($res2)) {

							$tab_periode_non_supprimable[]=$lig->num_periode;
							break;

					}
				}
				*/
				// Contrôle de la présence de notes sur les bulletins
				$sql="SELECT jec.login FROM j_eleves_classes jec, matieres_notes mn WHERE jec.id_classe='$id_classe' AND jec.periode='$lig->num_periode' AND jec.periode=mn.periode AND jec.login=mn.login;";
				//echo "$sql<br />\n";
				$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($test)>0) {
					$tab_periode_non_supprimable[]=$lig->num_periode;
				}
				else {
					// Contrôle de la présence d'appréciations sur les bulletins
					$sql="SELECT jec.login FROM j_eleves_classes jec, matieres_appreciations ma WHERE jec.id_classe='$id_classe' AND jec.periode='$lig->num_periode' AND jec.periode=ma.periode AND jec.login=ma.login;";
					//echo "$sql<br />\n";
					$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($test)>0) {
						$tab_periode_non_supprimable[]=$lig->num_periode;
					}
					else {
						// Contrôle de la présence de notes dans les carnets de notes
						$sql="SELECT 1=1 FROM j_groupes_classes jgc, cn_cahier_notes ccn, cn_devoirs cd, cn_conteneurs cc, cn_notes_devoirs cnd WHERE jgc.id_groupe=ccn.id_groupe AND cc.id=cd.id_conteneur AND cc.id_racine=ccn.id_cahier_notes AND cnd.id_devoir=cd.id AND cnd.statut!='v' AND jgc.id_classe='$id_classe' AND ccn.periode='$lig->num_periode';";
						//echo "$sql<br />\n";
						$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($test)>0) {
							$tab_periode_non_supprimable[]=$lig->num_periode;
						}
					}
				}
			}
		}
	}
}
//=========================================================================
if(!isset($suppr_periode)) {

	$sql="SELECT num_periode FROM periodes WHERE id_classe='".$id_classe."' ORDER BY num_periode DESC LIMIT 1;";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>ANOMALIE&nbsp;: La classe ".$classe." n'a actuellement aucune période.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		$lig=mysqli_fetch_object($res);
		$max_per=$lig->num_periode;
	}

	echo "<p><b>ATTENTION&nbsp;:</b> Il est recommandé de faire une <a href='../gestion/accueil_sauve.php?action=";
	if(getSettingValue('mode_sauvegarde')=='mysqldump') {
		echo "system_dump";
	}
	else {
		echo "dump";
	}
	echo add_token_in_url()."'>sauvegarde de la base</a> avant de supprimer une ou des périodes.</p>\n";
	echo "<br />\n";

	echo "<p class='bold'>Recherche des liaisons directes&nbsp;:</p>\n";
	echo "<blockquote>\n";
	echo "<p>";
	
	$tab_liaisons_classes=array();
	$tab_liaisons_classes[]=$id_classe;
	
	$sql="SELECT jgc.id_groupe FROM j_groupes_classes jgc WHERE jgc.id_classe='$id_classe';";
	//echo "$sql<br />\n";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "Aucune liaison n'a été trouvée.<br />La suppression de période ne présente donc pas de difficulté.</p>\n";
	}
	else {
		while($lig=mysqli_fetch_object($res)) {
			$sql="SELECT c.classe, jgc.id_classe, g.* FROM j_groupes_classes jgc, groupes g, classes c WHERE jgc.id_classe!='$id_classe' AND g.id=jgc.id_groupe AND c.id=jgc.id_classe AND jgc.id_groupe='$lig->id_groupe' ORDER BY c.classe;";
			//echo "$sql<br />\n";
			$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($test)>0) {
				$cpt=0;
				while($lig2=mysqli_fetch_object($test)) {
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

	$tab_periode_non_supprimable=array();
	if(count($tab_liaisons_classes)>0) {
		echo "<p>La classe <b>$classe</b> est liée (<i>de façon directe ou indirecte (via une autre classe)</i>) aux classes suivantes&nbsp;: ";
		$cpt=0;
		for($i=0;$i<count($tab_liaisons_classes);$i++) {
			if($tab_liaisons_classes[$i]!=$id_classe) {
				if($cpt>0) {echo ", ";}
				echo get_class_from_id($tab_liaisons_classes[$i]);

				search_periodes_non_vides($tab_liaisons_classes[$i]);

				$cpt++;
			}
		}

		if(count($tab_periode_non_supprimable)>0) {
			echo "<p>Une ou des périodes ne peuvent être supprimées parce qu'il y a des notes ou appréciations sur les bulletins ou dans des carnets de notes.<br />\n";
			sort($tab_periode_non_supprimable);
			echo "En voici la liste&nbsp;:";
			for($i=0;$i<count($tab_periode_non_supprimable);$i++) {
				if($i>0) {echo ", ";}
				echo "période $tab_periode_non_supprimable[$i]";
			}
		}

		echo "<p>Quelles périodes voulez-vous supprimer pour <b>$classe</b> et la ou les classes liées?</p>\n";
	}
	else {
		$tab_periode_non_supprimable=array();
		search_periodes_non_vides($id_classe);

		if(count($tab_periode_non_supprimable)>0) {
			echo "<p>Une ou des périodes ne peuvent être supprimées parce qu'il y a des notes ou appréciations sur les bulletins ou dans des carnets de notes.<br />\n";
			sort($tab_periode_non_supprimable);
			echo "En voici la liste&nbsp;:";
			for($i=0;$i<count($tab_periode_non_supprimable);$i++) {
				if($i>0) {echo ", ";}
				echo "période $tab_periode_non_supprimable[$i]";
			}
		}

		echo "<p>Quelles périodes voulez-vous supprimer pour <b>$classe</b>?</p>\n";
	}

	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();

	echo "<table class='boireaus' summary='Tableau des périodes'>\n";
	echo "<tr>\n";
	echo "<th>Numéro de période</th>\n";
	echo "<th>Nom de période</th>\n";
	echo "<th>Supprimer</th>\n";
	echo "</tr>\n";
	$alt=1;
	for($i=1;$i<$nb_periode;$i++) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td>$i</td>\n";
		echo "<td>$nom_periode[$i]</td>\n";
		echo "<td>\n";
		if(in_array($i,$tab_periode_non_supprimable)) {
			echo "&nbsp;";
		}
		else {
			echo " <input type='checkbox' name='suppr_periode[]' id='suppr_periode_$i' onchange='check_suppr($i)' value='$i' />\n";
		}
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";

	echo " <input type='hidden' name='id_classe' value='$id_classe' />\n";
	echo " <input type='submit' name='Supprimer' value='Supprimer' />\n";
	echo "</p>\n";
	echo "</form>\n";
	

	echo "<script type='text/javascript'>
	function check_suppr(num) {
		temoin='n';
		for(i=1;i<$nb_periode;i++) {
			if(temoin=='n') {
				if(document.getElementById('suppr_periode_'+i)) {
					if(document.getElementById('suppr_periode_'+i).checked==true) {
						temoin='y';
					}
				}
			}
			else {
				document.getElementById('suppr_periode_'+i).checked=true;
			}
		}
	}
</script>\n";


	echo "<p><br /></p>\n";
	
	echo "<p class='bold'>Remarques&nbsp;:</p>\n";
	//echo "<div style='margin-left: 3em;'>\n";
	echo "<ul>\n";
		echo "<li>\n";
			echo "<p>On ne peut pas supprimer une période n°5 et conserver la période n°6.</p>\n";
		echo "</li>\n";
		echo "<li>\n";
			echo "<p>La suppression de période présente une difficulté lorsqu'il y a des enseignements/groupes à cheval sur plusieurs classes.<br />Deux classes partageant un enseignement doivent avoir le même nombre de périodes.<br />Si vous supprimez des périodes à la classe ".$classe.", il faudra&nbsp:</p>\n";
			echo "<ul>\n";
				echo "<li>soit supprimer les mêmes périodes des classes liées à $classe</li>\n";
				echo "<li>soit rompre les liaisons&nbsp;:<br />Cela signifierait que vous auriez alors deux enseignements distincts pour $classe et une classe partageant l'enseignement.<br />Pour le professeur les conséquences sont les suivantes&nbsp;:<br />\n";
					echo "<ul>\n";
						echo "<li>pour saisir les résultats d'un devoir, il faudra créer un devoir dans chacun des deux enseignements et y saisir les notes</li>\n";
						echo "<li>la moyenne du groupe d'élève ne sera pas calculée; il y aura deux moyennes&nbsp: celles des deux enseignements<br />Même chose pour les moyennes min et max.</li>\n";
						echo "<li>Pour les notes existantes, il faut créer un nouveau groupe, un nouveau carnet de notes, cloner les devoirs et boites pour y transférer les notes et provoquer le recalcul des moyennes de conteneurs.<br />Les saisies de cahier de textes, d'emploi du temps doivent être dupliquées, les saisies antérieures d'absences peuvent-elles être perturbées (?) ou l'association n'est-elle que élève/jour_heures_absence (?),...</li>";
					echo "</ul>\n";
					echo "<span style='color:red'>La deuxième solution n'est pas implémentée pour le moment</span>\n";
				echo "</li>\n";
			echo "</ul>\n";
		echo "</li>\n";
	echo "</ul>\n";
	//echo "</div>\n";
}
//=========================================================================
else {
	check_token(false);

	$tab_liaisons_classes=array();
	$tab_liaisons_classes[]=$id_classe;
	search_liaisons_classes_via_groupes($id_classe);

	$tab_periode_non_supprimable=array();
	for($i=0;$i<count($tab_liaisons_classes);$i++) {
		search_periodes_non_vides($tab_liaisons_classes[$i]);
	}

	// Il faut supprimer toutes les périodes après le plus petit des num_periode
	// Il ne faut pas se retrouver avec une classe qui aurait des périodes 1, 2, 3 puis passerait à 5 sans période 4.
	
	$sql="SELECT num_periode FROM periodes WHERE id_classe='".$id_classe."' ORDER BY num_periode DESC LIMIT 1;";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>ANOMALIE&nbsp;: La classe ".$classe." n'a actuellement aucune période.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		$lig=mysqli_fetch_object($res);
		$max_per=$lig->num_periode;
	}

	sort($suppr_periode);

	for($i=0;$i<count($tab_liaisons_classes);$i++) {
		$id_classe_courant=$tab_liaisons_classes[$i];
		$classe_courante=get_class_from_id($tab_liaisons_classes[$i]);

		echo "<p class='bold'>Traitement de la classe $classe_courante&nbsp;:</p>\n";
		echo "<blockquote>\n";

		$sql="SELECT num_periode FROM periodes WHERE id_classe='".$id_classe_courant."' ORDER BY num_periode DESC LIMIT 1;";
		$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p style='color:red'>ANOMALIE&nbsp;: La classe ".$classe_courante." n'a actuellement aucune période.</p>\n";
		}
		else {
			// Boucle sur la liste des périodes en contrôlant qu'elles ne sont pas dans $tab_periode_non_supprimable

			//for($j=0;$j<count($suppr_periode);$j++) {
			for($j=$suppr_periode[0];$j<=$max_per;$j++) {
				//if(!in_array($suppr_periode[$j],$tab_periode_non_supprimable)) {
				if(!in_array($j,$tab_periode_non_supprimable)) {
					// Nettoyer j_eleves_groupes
					//echo "Nettoyage des inscriptions d'élèves dans des groupes/enseignements pour la période $suppr_periode[$j]&nbsp;: ";
					//$sql="DELETE FROM j_eleves_groupes WHERE periode='$suppr_periode[$j]' AND id_groupe IN (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe_courant');";
					echo "Nettoyage des inscriptions d'élèves dans des groupes/enseignements pour la période $j&nbsp;: ";
					$sql="DELETE FROM j_eleves_groupes WHERE periode='$j' AND id_groupe IN (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe_courant');";
					$del=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(!$del) {
						echo "<span style='color:red'>ECHEC</span>";
						echo "<br />\n";
					}
					else {
						echo "<span style='color:green'>SUCCES</span>";
						echo "<br />\n";
	
						// Nettoyer j_eleves_classes
						//echo "Nettoyage des inscriptions d'élèves dans la classe $classe_courante pour la période $suppr_periode[$j]&nbsp;: ";
						//$sql="DELETE FROM j_eleves_classes WHERE periode='$suppr_periode[$j]' AND id_classe='$id_classe_courant';";
						echo "Nettoyage des inscriptions d'élèves dans la classe $classe_courante pour la période $j&nbsp;: ";
						$sql="DELETE FROM j_eleves_classes WHERE periode='$j' AND id_classe='$id_classe_courant';";
						$del=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(!$del) {
							echo "<span style='color:red'>ECHEC</span>";
							echo "<br />\n";
						}
						else {
							echo "<span style='color:green'>SUCCES</span>";
							echo "<br />\n";

							// Nettoyer edt_calendrier
							$poursuivre="y";
							$sql="SELECT * FROM edt_calendrier WHERE numero_periode='$j' AND (classe_concerne_calendrier LIKE '$id_classe_courant;%' OR classe_concerne_calendrier LIKE '%;$id_classe_courant;%');";
							$res_edt_calendrier=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
							if(mysqli_num_rows($res_edt_calendrier)>0) {
								echo "Nettoyage de edt_calendrier pour la classe $classe_courante sur la période $j&nbsp;: ";
								// Normalement, on ne fait qu'un tour dans la boucle
								while($lig_edt_cal=mysqli_fetch_object($res_edt_calendrier)) {
									$tab_edt=explode(";",$lig_edt_cal->classe_concerne_calendrier);
									$chaine_classe="";
									for($k=0;$k<count($tab_edt);$k++) {
										if($tab_edt[$k]!=$id_classe_courant) {
											$chaine_classe.=";".$tab_edt[$k];
										}
									}
									$chaine_classe=preg_replace("/^;/","",$chaine_classe);

									$sql="UPDATE edt_calendrier SET classe_concerne_calendrier='$chaine_classe' WHERE id_calendrier='$lig_edt_cal->id_calendrier';";
									$update=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									if(!$del) {
										echo "<span style='color:red'>ECHEC</span>";
										echo "<br />\n";
										$poursuivre="n";
									}
									else {
										echo "<span style='color:green'>SUCCES</span>";
										echo "<br />\n";
									}
								}
							}

							if($poursuivre=='y') {
								// Nettoyer periodes
								//echo "Suppression de la période $suppr_periode[$j] pour la classe $classe_courante&nbsp;: ";
								//$sql="DELETE FROM periodes WHERE id_classe='$id_classe_courant' AND num_periode='$suppr_periode[$j]';";
								echo "Suppression de la période $j pour la classe $classe_courante&nbsp;: ";
								$sql="DELETE FROM periodes WHERE id_classe='$id_classe_courant' AND num_periode='$j';";
								$del=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
								if(!$del) {
									echo "<span style='color:red'>ECHEC</span>";
									echo "<br />\n";
								}
								else {
									echo "<span style='color:green'>SUCCES</span>";
									echo "<br />\n";
								}
							}
						}
					}

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