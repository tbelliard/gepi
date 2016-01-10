<?php
/*
*
* Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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


$sql="SELECT 1=1 FROM droits WHERE id='/aid/popup.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/aid/popup.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Visualisation des membres d un AID',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$id_aid=isset($_GET['id_aid']) ? $_GET['id_aid'] : NULL;
$periode_num=isset($_GET['periode_num']) ? $_GET['periode_num'] : NULL;

$avec_details=isset($_GET['avec_details']) ? $_GET['avec_details'] : "n";

if(isset($_GET['orderby'])){
	if($_GET['orderby']=='nom'){
		$orderby=" ORDER BY e.nom,e.prenom";
	}
	else{
		$orderby=" ORDER BY c.classe,e.nom,e.prenom";
	}
}
else{
	$orderby=" ORDER BY e.nom,e.prenom";
}

$msg="";

if(!isset($id_aid)) {
	header("Location: ../accueil.php?msg=Aucun_AID_choisi");
	die();
}
elseif(!preg_match("/^[0-9]{1,}$/", $id_aid)) {
	header("Location: ../accueil.php?msg=Identifiant AID invalide");
	die();
}

if(isset($periode_num)) {
	$periode_num=preg_replace('/[^0-9]/','',$periode_num);
	if($periode_num=='') {
		unset($periode_num);
		//$msg.="Numéro de période invalide.<br />\n";
	}
}

$tab_aid=get_tab_aid($id_aid, $orderby);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php
	echo "<title>".$tab_aid['nom_aid']." (".$tab_aid['nom_general_complet'].")</title>\n";
?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link type="text/css" rel="stylesheet" href="../style.css" />
<?php
	if(isset($style_screen_ajout)){
		if($style_screen_ajout=='y'){
			echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
		}
	}

	$posDiv_infobulle=0;
	// $posDiv_infobulle permet de fixer la position horizontale initiale du Div.

	$tabdiv_infobulle=array();
	$tabid_infobulle=array();

	// Choix de l'unité pour les dimensions des DIV: em, px,...
	$unite_div_infobulle="em";
	// Pour l'overflow dans les DIV d'aide, il vaut mieux laisser 'em'.

	// Variable passée à 'ok' en fin de page via le /lib/footer.inc.php
	echo "<script type='text/javascript'>
		var temporisation_chargement='n';
	</script>\n";

	echo "<script type='text/javascript' src='$gepiPath/lib/brainjar_drag.js'></script>\n";
	echo "<script type='text/javascript' src='$gepiPath/lib/position.js'></script>\n";


?>
</head>
<body style='margin-left:2px;'>

<?php

	$lien_visu_eleve="n";
	if(acces('/eleves/visu_eleve.php',$_SESSION['statut'])) {
		$lien_visu_eleve="y";
	}

	if($msg!=""){
		echo "<p style='color:red; text-align:center;'>".$msg."</p>\n";
	}

	echo "<h2>".$tab_aid['nom_aid']." <span style='font-size:small; font-style:italic;'>(".$tab_aid['nom_general_complet'].")</span>";

	$nb_periode=$tab_aid['maxper']+1;
	if(isset($periode_num)) {
		echo " <em title='Période $periode_num'>(";
		if(($periode_num>1)&&(isset($tab_aid['eleves'][$periode_num-1]))&&(count($tab_aid['eleves'][$periode_num-1]['list'])>0)) {
			echo "<a href='".$_SERVER['PHP_SELF']."?id_aid=$id_aid&amp;periode_num=".($periode_num-1)."' title='Voir la période précédente'><img src='../images/icons/arrow-left.png' class='icone16' /></a>";
		}
		echo "P.$periode_num";
		if(($periode_num<$nb_periode-1)&&(isset($tab_aid['eleves'][$periode_num+1]))&&(count($tab_aid['eleves'][$periode_num+1]['list'])>0)) {
			echo "<a href='".$_SERVER['PHP_SELF']."?id_aid=$id_aid&amp;periode_num=".($periode_num+1)."' title='Voir la période suivante'><img src='../images/icons/arrow-right.png' class='icone16' /></a>";
		}
		echo ")</em>";
	}

	echo "</h2>\n";

	echo "<div class='noprint' style='float:right; width: 20px; height: 20px'><a href='";
	echo $_SERVER['PHP_SELF']."?id_aid=".$id_aid;
	if($avec_details=='y') {echo "&amp;avec_details=n";} else {echo "&amp;avec_details=y";}
	if(isset($periode_num)) {echo "&amp;periode_num=$periode_num";}
	echo "' title='Afficher/masquer les détails'>";
	if($avec_details=='y') {echo "<img src='../images/icons/remove.png' width='16' height='16' alt='Sans détails' />";} else {echo "<img src='../images/icons/add.png' width='16' height='16' alt='Avec détails' />";}
	echo "</a></div>";

	$titre="Photo";
	$texte="";
	$tabdiv_infobulle[]=creer_div_infobulle('div_photo_eleve',$titre,"",$texte,"",10,0,'y','y','n','n');

	if(acces("/groupes/get_csv.php", $_SESSION['statut'])) {
		echo "<div class='noprint' style='float:right; width: 20px; height: 20px'><a href='../groupes/get_csv.php?id_aid=$id_aid";
		if(isset($periode_num)) {echo "&amp;periode_num=$periode_num";}
		echo "' title=\"Exporter la liste des élèves au format CSV (tableur)\"><img src='../images/icons/csv.png' class='icone16' alt='CSV' /></a></div>\n";
	}

	$tabmail=array();

	echo "<table class='boireaus' border='1'>\n";
	echo "<tr valign='top'><th>Professeur";
	if((isset($tab_aid['profs']['list']))&&(count($tab_aid['profs']['list'])>1)) {echo "s";}
	echo ":</th>\n";
	echo "<td class='lig-1'>";
	foreach($tab_aid['profs']['users'] as $current_login_prof => $current_prof){
		echo $current_prof['civilite']." ".$current_prof['nom']." ".$current_prof['prenom'];
		echo "<br />\n";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";


	if(isset($periode_num)) {
		if(isset($tab_aid['eleves'][$periode_num]['users'])) {
			$current_aid_ele_periode=$tab_aid['eleves'][$periode_num]['users'];
		}
		else {
			$current_aid_ele_periode=array();
		}
	}
	else {
		if(isset($tab_aid['eleves']['all']['users'])) {
			$current_aid_ele_periode=$tab_aid['eleves']['all']['users'];
		}
		else {
			$current_aid_ele_periode=array();
		}
	}
	$nb_eleves=count($current_aid_ele_periode);
	echo "<p>Effectif: $nb_eleves</p>\n";
	if($nb_eleves>0){
		echo "<table class='boireaus boireaus_alt' border='1'>\n";
		echo "<tr><th><a href='".$_SERVER['PHP_SELF']."?id_aid=$id_aid&amp;orderby=nom";
		if(isset($periode_num)) {echo "&amp;periode_num=$periode_num";}
		echo "'>Elève</a></th>\n";
		echo "<th><a href='".$_SERVER['PHP_SELF']."?id_aid=$id_aid&amp;orderby=classe";
		if(isset($periode_num)) {echo "&amp;periode_num=$periode_num";}
		echo "'>Classe</a></th>\n";
		if($avec_details=='y') {
			// Ajouter un test sur le trombino actif ou non
			if(getSettingValue('active_module_trombinoscopes')=='y') {echo "<th>Photo</th>\n";}
			echo "<th>Naissance</th>\n";
		}
		echo "</tr>\n";
		foreach($current_aid_ele_periode as $current_login_ele => $current_ele){
			echo "<tr class='white_hover'><td>";
			if($current_ele['email']!=""){
				echo "
<div style='float:right; width:16px' class='noprint'>
	<a href='mailto:".$current_ele['email']."?".urlencode("subject=".getSettingValue('gepiPrefixeSujetMail')."[GEPI]")."' title='Envoyer un mail à cet élève'><img src='../images/icons/mail.png' class='icone16' alt='Mail' /></a>
</div>";
				$tabmail[]=$current_ele['email'];
			}

			if($lien_visu_eleve=="y") {
				echo "<a href='../eleves/visu_eleve.php?ele_login=$current_login_ele&amp;cacher_header=y' title='Accéder à la consultation élève.' style='text-decoration:none; color:black;'>";
				echo $current_ele['nom']." ".$current_ele['prenom'];
				echo "</a>";
			}
			else {
				echo $current_ele['nom']." ".$current_ele['prenom'];
			}
			//echo "<br />\n";

			echo "</td>\n";
			echo "<td>".$current_ele['nom_classe']."</td>\n";
/*

			echo "</td>\n";
*/
			if($avec_details=='y') {
				if(getSettingValue('active_module_trombinoscopes')=='y') {
					echo "<td>\n";
					$_photo_eleve = nom_photo($current_ele['elenoet']);
					if($_photo_eleve!='') {
						echo "<a href='#' onclick=\"document.getElementById('div_photo_eleve_contenu_corps').innerHTML='<div align=\'center\'><img src=\'$_photo_eleve\' width=\'150\' /></div>';afficher_div('div_photo_eleve','y',-100,20); return false;\"><img src='../images/icons/buddy.png' alt=\"".$current_ele['nom']." ".$current_ele['prenom']."\"></a>\n";
					}
					else {
						echo "&nbsp;";
					}
					echo "</td>\n";
				}
				echo "<td>\n";
				if($lien_visu_eleve=="y") {
					echo "<a href='../eleves/visu_eleve.php?ele_login=$current_login_ele&amp;cacher_header=y' title='Accéder à la consultation élève' style='text-decoration:none; color:black;'>".affiche_date_naissance($current_ele['naissance'])."</a>";
				}
				else {
					echo affiche_date_naissance($current_ele['naissance']);
				}
				echo "</td>\n";
			}

			echo "</tr>\n";
		}
		echo "</table>\n";
	}



if(getSettingValue('envoi_mail_liste')=='y') {
	$chaine_mail="";
	if(count($tabmail)>0){
		unset($tabmail2);
		$tabmail2=array();
		//$tabmail=array_unique($tabmail);
		//sort($tabmail);
		$chaine_mail=$tabmail[0];
		for ($i=1;$i<count($tabmail);$i++) {
			if((isset($tabmail[$i]))&&(!in_array($tabmail[$i],$tabmail2))) {
				$chaine_mail.=",".$tabmail[$i];
				$tabmail2[]=$tabmail[$i];
			}
		}
		//echo "<p>Envoyer un <a href='mailto:$chaine_mail?".rawurlencode("subject=[GEPI]")."'>mail à tous les élèves de l'enseignement</a>.</p>\n";
		echo "<p>Envoyer un <a href='mailto:$chaine_mail?".rawurlencode("subject=".getSettingValue('gepiPrefixeSujetMail')."[GEPI]")."'>mail à tous les élèves</a>.</p>\n";
	}
}
?>
<script language="JavaScript" type="text/javascript">
	window.focus();
</script>
<!--/body>
</html-->
<?php
	require("../lib/footer.inc.php");
?>
