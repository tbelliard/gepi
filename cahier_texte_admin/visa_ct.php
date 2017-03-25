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


// INSERT INTO `droits`  VALUES ('/cahier_texte_admin/visa_ct.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Page de signature des cahiers de texte', '');
// ALTER TABLE `ct_devoirs_entry` ADD `vise` CHAR( 1 ) NOT NULL DEFAULT 'n' AFTER `contenu` ;
// ALTER TABLE `ct_entry` ADD `vise` VARCHAR( 1 ) NOT NULL DEFAULT 'n' AFTER `contenu` ;
// ALTER TABLE `ct_entry` ADD `visa` VARCHAR( 1 ) NOT NULL DEFAULT 'n' AFTER `vise` ;


// Initialisations files
require_once("../lib/initialisations.inc.php");
//debug_var();
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}
// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(isset($_SESSION['retour_cdt'])) {unset($_SESSION['retour_cdt']);}

$definir_visa_par_defaut=isset($_POST['definir_visa_par_defaut']) ? $_POST['definir_visa_par_defaut'] : (isset($_GET['definir_visa_par_defaut']) ? $_GET['definir_visa_par_defaut'] : NULL);

if (isset($_POST['ok_enr_visa'])) {
	check_token();

	$error = false;
	if(isset($_POST['texte_visa_FCK'])) {
			$txt = html_entity_decode($_POST['texte_visa_FCK']);
			if (!saveSetting("texte_visa_cdt", $txt)) {
				$msg .= "Erreur lors de l'enregistrement du texte du visa !";
				$erreur = true;
			}
	}
	if (!$error) {
		$msg = "Le visa a bien été enregistré.";
	}
}

$texte_visa_cdt = preg_replace('/\\\r\\\n/','',getSettingValue("texte_visa_cdt"));

if (isset($_POST['begin_day']) and isset($_POST['begin_month']) and isset($_POST['begin_year'])) {
	check_token();

	$date_signature = mktime(0,0,0,$_POST['begin_month'],$_POST['begin_day'],$_POST['begin_year']);
	if (!saveSetting("date_signature", $date_signature)) $msg .= "Erreur lors de l'enregistrement de date de signature des cahiers de textes !";
}

//on récupère la date butoir pour la signture des CDT
$date_signature = getSettingValue("date_signature");

// visa d'un ou plusieurs cahiers de texte
if (isset($_POST['visa_ct'])) {
	check_token();

	$nb_cdt_signes=0;

	$msg = '';

	if(isset($_POST['texte_visa_FCK'])) {
		$texte_visa_cdt = preg_replace('/\\\r\\\n/','',html_entity_decode($_POST['texte_visa_FCK']));
	}

	// les entrées
	// on vise les notices (le champs vise de la table ct_entry est mis à 'y')
	$query = sql_query("SELECT DISTINCT id_groupe, id_login FROM ct_entry ORDER BY id_groupe");

	$iterateur = 0;
	for ($i=0; ($row=sql_row($query,$i)); $i++) {
		$id_groupe = $row[0];
		$id_prop = $row[1];
		$temp = "visa_".$iterateur;
		if (isset($_POST[$temp])) {
			$error = 'no';
			$id_groupe = isset($_POST["groupe_".$iterateur]) ? $_POST["groupe_".$iterateur] : NULL;
			$id_prop = isset($_POST["prof_".$iterateur]) ? $_POST["prof_".$iterateur] : NULL;
		
			$sql_visa_ct = "UPDATE `ct_entry` SET `vise`='y' WHERE ((id_groupe='".$id_groupe."' and id_login = '".$id_prop."') and (date_ct<$date_signature))";
			//echo "$sql_visa_ct<br />\n";
			$visa_ct = sql_query($sql_visa_ct);
			if(!$visa_ct) {
				$msg.="Erreur lors de l'enregistrement du visa des comptes-rendus pour le groupe n°$id_groupe et le professeur $id_professeur<br />\n";
			}
		
			// On ajoute une notice montrant la signature du cahier de texte
			//$aujourdhui = mktime(0,0,0,date("m"),date("d"),date("Y"));
			$aujourdhui = date("U");
		
			$id_sequence="0";
			$sql_insertion_visa = "INSERT INTO `ct_entry` VALUES (NULL, '00:00:00', '".$id_groupe."', '".$aujourdhui."', '".$id_prop."', '".$id_sequence."', '".$texte_visa_cdt."', 'y', 'y')";
			//echo "$sql_insertion_visa<br />\n";
			$insertion_visa = sql_query($sql_insertion_visa);
			if ($error == 'no') {
				//$msg .= "Cahier(s) de textes signé(s).<br />\n";
				$nb_cdt_signes++;
			} else {
				$msg .= "Il y a eu un problème lors de la signature du cahier de textes.<br />\n";
			}
		}
		$iterateur++;
	}
	
	$query = sql_query("SELECT DISTINCT id_groupe, id_login FROM ct_devoirs_entry ORDER BY id_groupe");
	//les devoirs
	// on vise les notices devoirs (le champs vise de la table ct_devoirs_entry est mis à 'y')
	$itera = 0;
	for ($i=0; ($row=sql_row($query,$i)); $i++) {
		$id_groupe = $row[0];
		$id_prop = $row[1];
		$temp = "visa_".$itera;
	
		if (isset($_POST[$temp])) {
			$error = 'no';
			$id_professeur = isset($_POST["prof_".$itera]) ? $_POST["prof_".$itera] : NULL;
			$id_groupe = isset($_POST["groupe_".$itera]) ? $_POST["groupe_".$itera] : NULL;
	
			$sql_visa_ct = "UPDATE `ct_devoirs_entry` SET `vise` = 'y' WHERE ((id_groupe='".$id_groupe."' and id_login = '".$id_professeur."') and (date_ct<$date_signature))";
			//echo "$sql_visa_ct<br />\n";
			$visa_ct = sql_query($sql_visa_ct);
			if(!$visa_ct) {
				$msg.="Erreur lors de l'enregistrement du visa des notices de devoirs pour le groupe n°$id_groupe et le professeur $id_professeur<br />\n";
			}
		}
		$itera++;
	}

	if($nb_cdt_signes>0) {
		$msg .= "$nb_cdt_signes cahier(s) de textes signé(s).<br />\n";
	}
}


//=============================================
// header
$titre_page = "Signature des cahiers de textes";
require_once("../lib/header.inc.php");
//=============================================

//debug_var();

if (!(isset($_GET['action']))) {
// Affichage du tableau complet

if ($_SESSION['statut'] == "autre"||$_SESSION['statut']== "scolarite") {
	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
} else {
	echo "<p class=\"bold\"><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
}

if(isset($definir_visa_par_defaut)) {
	echo " | <a href='visa_ct.php'>Viser les CDT</a>";
}
else {
	echo " | <a href='visa_ct.php?definir_visa_par_defaut=y'>Définir le texte du visa par défaut</a>";
}
echo "</p>\n";

echo "<h2>Signature des cahiers de textes</h2>\n";

if(isset($definir_visa_par_defaut)) {

	echo "<div style='width: 750px;'>\n";
	echo "<fieldset style=\"border: 1px solid grey; font-size: 0.8em; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">\n";
	
	echo "<form enctype=\"multipart/form-data\" action=\"visa_ct.php\" method=\"post\">\n";
	echo add_token_field();
	echo "<h2 class='gepi' style=\"text-align: center;\">Texte du visa par défaut</h2>\n";
	echo "<p><em>Mise en forme du visa :</em></p><p>\n";
	

?>

	<script src="../ckeditor/ckeditor.js"></script>  
	<textarea name="texte_visa_FCK" id ="texte_visa_FCK" style="border: 1px solid gray; width: 600px; height: 250px;"><?php echo $texte_visa_cdt; ?></textarea>
	<script type='text/javascript'>
	// Configuration via JavaScript
	CKEDITOR.replace('texte_visa_FCK',{
		customConfig: '../lib/ckeditor_gepi_config_mini.js'
	});
	</script>

<?php
	echo "<input type='submit' name=\"ok_enr_visa\" value='Enregistrer le visa' /></p>\n";
	echo "</form>\n";
	echo "<br /><br />";
	
	echo "</fieldset>";
	echo "<br /><br />\n";
	echo "</div>\n";


	require ("../lib/footer.inc.php");
	die();
}

echo "<p>Le tableau ci-dessous présente l'ensemble des cahiers de textes actuellement en ligne.</p>\n";
echo "<ul>\n";
echo "<li>&nbsp;&nbsp;Vous pouvez trier le tableau par le groupe ou le propriétaire d'un cahier de textes en cliquant sur le lien correspondant.</li>\n";
echo "<li>&nbsp;&nbsp;Vous pouvez visualiser un cahier de textes.</li>\n";
echo "<li>&nbsp;&nbsp;Vous pouvez également signer un ou plusieurs cahiers de textes avec le texte ci-dessous.<br />Le texte par défaut peut être défini <a href='visa_ct.php?definir_visa_par_defaut=y'>ici</a></li>\n";
echo "</ul>\n";

//echo "<br /><br />\n";


?>

<a name='tableau_des_enseignants'></a>
<form action="visa_ct.php" method="post">
<?php
	echo add_token_field();

	echo "<div style='width: 820px;'>\n";
	//echo "<fieldset style=\"border: 1px solid grey; font-size: 0.8em; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">\n";
	
	//echo "<form enctype=\"multipart/form-data\" action=\"visa_ct.php\" method=\"post\">\n";
	//echo add_token_field();
	echo "<h2 class='gepi' style=\"text-align: center;\">Texte du visa à apposer sur les cahiers de textes</h2>\n";
	echo "<p><em>Mise en forme du visa :</em> <a href='visa_ct.php'>Réinitialiser au visa par défaut</a></p><p>\n";


?>

	<script src="../ckeditor/ckeditor.js"></script>  
	<textarea name="texte_visa_FCK" id ="texte_visa_FCK" style="border: 1px solid gray; width: 600px; height: 250px;"><?php echo $texte_visa_cdt; ?></textarea>
	<script type='text/javascript'>
	// Configuration via JavaScript
	CKEDITOR.replace('texte_visa_FCK',{
		customConfig: '../lib/ckeditor_gepi_config_mini.js'
	});
	</script>

<?php
	
	//echo "<input type='submit' name=\"ok_enr_visa\" value='Enregistrer le visa' /></p>\n";
	//echo "</form>\n";
	//echo "<br /><br />";
	
	//echo "</fieldset>";
	echo "<br /><br />\n";
	echo "</div>\n";

	if(getSettingAOui('cdt_afficher_volume_docs_joints')) {
		$affichage_volume_docs_joints=isset($_GET['affichage_volume_docs_joints']) ? "y" : NULL;
		if(!isset($affichage_volume_docs_joints)) {
			echo "<p><a href='".$_SERVER['PHP_SELF']."?affichage_volume_docs_joints=y'>Afficher le volume des documents joints aux CDT</a></p>\n";
		}
	}
?>
<table class='boireaus' border="1"><tr valign='middle' align='center'>
<th><b><a href='visa_ct.php?order_by=jc.id_classe,jm.id_matiere'>Classe(s)</a></b></th>
<th><b><a href='visa_ct.php?order_by=jm.id_matiere,jc.id_classe'>Groupe</a></b></th>
<th><b><a href='visa_ct.php?order_by=ct.id_login,jc.id_classe,jm.id_matiere'>Propriétaire</a></b></th>
<th><b>Nombre<br />de notices</b></th>
<th><b>Nombre<br />de notices<br />"devoirs"</b></th>
<th>
<b>Action</b></th>
<th><b><input type="submit" name="visa_ct" value="Signer les cahiers" onclick="return confirmlink(this, 'La signature d\'un cahier de texte est définitive. Etes-vous sûr de vouloir continuer ?', 'Confirmation de la signature')" /></b>
<p><b>dont la date est inférieure au</b></p>
<?php
		$bday = strftime("%d", getSettingValue("date_signature"));
		$bmonth = strftime("%m", getSettingValue("date_signature"));
		$byear = strftime("%Y", getSettingValue("date_signature"));
		genDateSelector("begin_", $bday, $bmonth, $byear,"more_years") ?>
</th>

<th><b>Nombre de visa</b></th>
<?php
	if(isset($affichage_volume_docs_joints)) {
		$total_volumes_docs_joints=0;
?>
<th title="Volume des documents joints"><b>Volume</b></th>
<?php
	}
?>
</tr>

<?php
if (!isset($_GET['order_by'])) {
	$order_by = "jc.id_classe,jm.id_matiere";
} else {
	$order_by = $_GET['order_by'];
}

$iter = 0; // itérateur

	$alt=1;
$query = sql_query("SELECT DISTINCT ct.id_groupe, ct.id_login FROM ct_entry ct, j_groupes_classes jc, j_groupes_matieres jm WHERE (jc.id_groupe = ct.id_groupe AND jm.id_groupe = ct.id_groupe) ORDER BY ".$order_by);
for ($i=0; ($row=sql_row($query,$i)); $i++) {
	$alt=$alt*(-1);
	$id_groupe = $row[0];
	$id_prop = $row[1];
	$nom_groupe = sql_query1("select name from groupes where id = '".$id_groupe."'");
	$nom_matiere = sql_query1("select m.nom_complet from matieres m, j_groupes_matieres jm where (jm.id_groupe = '".$id_groupe."' AND m.matiere = jm.id_matiere)");
	$get_classes = mysqli_query($GLOBALS["mysqli"], "SELECT c.id, c.classe FROM classes c, j_groupes_classes jc WHERE (c.id = jc.id_classe and jc.id_groupe = '" . $id_groupe . "')");
	$nb_classes = mysqli_num_rows($get_classes);
	$id_classe = old_mysql_result($get_classes, 0, "id"); // On ne garde qu'un id pour ne pas perturber le GET ensuite
	$classes = null;
	for ($c=0;$c<$nb_classes;$c++) {
		$current_classe = old_mysql_result($get_classes, $c, "classe");
		$classes .= $current_classe;
		if ($c+1<$nb_classes) $classes .= ", ";
	}

	if ($nom_groupe == "-1") $nom_groupe = "<font color='red'>Groupe inexistant</font>";
	$sql_prof = sql_query("select nom, prenom from utilisateurs where login = '".$id_prop."'");
	if (!($sql_prof)) {
		$nom_prof = "<font color='red'>".$id_prop." : utilisateur inexistant</font>";
	} else {
		$row_prof=sql_row($sql_prof,0);
		$nom_prof = $row_prof[1]." ".$row_prof[0];
		$test_groupe_prof = sql_query("select login from j_groupes_professeurs WHERE (id_groupe='".$id_groupe."' and login = '".$id_prop."')");
		if (sql_count($test_groupe_prof) == 0) $nom_prof = "<font color='red'>".$nom_prof." : <br />Ce professeur n'enseigne pas dans ce groupe</font>";
	}
	// Nombre de notices de chaque utilisateurs
	$nb_ct = sql_count(sql_query("select 1=1 FROM ct_entry WHERE (id_groupe='".$id_groupe."' and id_login='".$id_prop."' AND visa != 'y') "));

	// Nombre de notices devoirs de haque utilisateurs
	$nb_ct_devoirs = sql_count(sql_query("select 1=1 FROM ct_devoirs_entry WHERE (id_groupe='".$id_groupe."' and id_login='".$id_prop."') "));

	//Nombre de visa sur un cahier de texte
	$sql="select 1=1 FROM ct_entry WHERE (id_groupe='".$id_groupe."' and id_login='".$id_prop."' and visa ='y');";
	$nb_ct_visa = sql_count(sql_query($sql));

	// Affichage des lignes
	echo "<tr class='lig$alt white_hover'><td>".$classes."</td>";
	echo "<td>".$nom_groupe."</td>";
	echo "<td>".$nom_prof."</td>";
	echo "<td>".$nb_ct."</td>";
	echo "<td>".$nb_ct_devoirs."</td>";
	// Modif pour le statut 'autre'
	if ($_SESSION["statut"] == 'autre' OR $_SESSION["statut"] == 'administrateur' OR $_SESSION["statut"] == 'scolarite') {
		//echo '<td><a href="../cahier_texte/see_all.php?id_groupe='.$id_groupe.'&amp;id_classe='.$id_classe.'">Voir</a></td>';
		echo '<td><a href="../cahier_texte/see_all.php?id_groupe='.$id_groupe.'&amp;id_classe='.$id_classe.'&amp;retour_cdt=visa_cdt">Voir</a></td>';
	}else{
		echo "<td><a href='../public/index.php?id_groupe=".$id_groupe."' target='_blank'>Voir</a></td>";
	}
	echo "<td><center><input type=\"checkbox\" name=\"visa_".$iter."\" />
						<input type=\"hidden\" name=\"prof_".$iter."\" value=\"".$id_prop."\" />
						<input type=\"hidden\" name=\"groupe_".$iter."\" value=\"".$id_groupe."\" />
			</center></td>";
	echo "<td>".$nb_ct_visa;
	//echo "$sql<br />";
	//echo "\$nb_ct_visa=$nb_ct_visa\<br />";
	echo "</td>";

	if(isset($affichage_volume_docs_joints)) {
		echo "<td>";
		$volume_cdt_groupe=volume_docs_joints($id_groupe);
		if($volume_cdt_groupe!=0) {
			$total_volumes_docs_joints+=$volume_cdt_groupe;
			echo volume_human($volume_cdt_groupe);
		}
		else {
			echo "0";
		}
		echo "</td>";
	}

	echo "</tr>";
	$iter++;
}
echo "</table></form>";

if((isset($affichage_volume_docs_joints))&&($total_volumes_docs_joints!=0)) {
	echo "<p>Volume total des documents joints&nbsp;: ".volume_human($total_volumes_docs_joints)."</p>\n";
}

	echo "<br />";
}
require ("../lib/footer.inc.php");
?>
