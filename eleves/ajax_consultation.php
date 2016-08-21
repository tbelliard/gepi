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

// On donne l'accès à tous les personnels,
// donc on ne doit faire par la suite que des consultations,
// en vérifiant les droits d'accès à telle ou telle info.
// Pas de modifications, sauf à contrôler les droits là aussi.
// Pour le moment la page ne permet de récupérer que les membres élèves des différentes classes.
$sql="SELECT 1=1 FROM droits WHERE id='/eleves/ajax_consultation.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/eleves/ajax_consultation.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='V',
autre='V',
description='Recherches/consultations classes/élèves via ajax',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : "");
$mode2=isset($_POST['mode2']) ? $_POST['mode2'] : (isset($_GET['mode2']) ? $_GET['mode2'] : "");
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$id_champ=isset($_POST['id_champ']) ? $_POST['id_champ'] : (isset($_GET['id_champ']) ? $_GET['id_champ'] : NULL);

if((isset($id_classe))&&(isset($id_champ))&&(preg_match("/^[0-9]+$/", $id_classe))&&(preg_match("/^[0-9A-Za-z_]+$/", $id_champ))) {
	$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='".$id_classe."' ORDER BY e.nom, e.prenom;";

	echo "<p class='bold'>Classe de ".get_nom_classe($id_classe)."</p><div style='width:48%; float:left;'>";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$eff=mysqli_num_rows($res);
	$cpt=0;
	$moitie_passee=0;
	while($lig=mysqli_fetch_object($res)) {
		if(($moitie_passee==0)&&($cpt>=ceil($eff/2))) {
			echo "</div>";
			echo "<div style='width:48%; float:left;'>";
			$moitie_passee++;
		}

		$chaine=casse_mot($lig->prenom, "majf2")." ".casse_mot($lig->nom, "maj");
		/*
		// Le remplacement du contenu du champ risque d'être source de fausses manips... il sera plus simple de sélectionner à la souris/supprimer puisque toute l'opération se fait déjà avec la souris en main.
		echo "<a href='#' onclick=\"remplace_contenu_champ_ajax_ele('$id_champ', '".addslashes($chaine)."');return false;\" title=\"Remplacer le contenu du champ\">$lig->nom $lig->prenom</a>";
		echo "<a href='#' onclick=\"complete_contenu_champ_ajax_ele('$id_champ', '".addslashes($chaine)."');return false;\" title=\"Compléter le champ\"><img src='$gepiPath/images/icons/add.png' class='icone16' alt='Compléter' /></a><br />";
		*/
		echo "<a href='#' onclick=\"complete_contenu_champ_ajax_ele('$id_champ', '".addslashes($chaine)."');return false;\" title=\"Compléter le champ '$id_champ'\">$lig->nom $lig->prenom&nbsp;<img src='$gepiPath/images/icons/add.png' class='icone16' alt='Compléter' /></a><br />";

		$cpt++;
	}
	echo "</div>";

	die();
}
elseif(($mode=="get_classes")&&(isset($id_champ))&&(preg_match("/^[0-9A-Za-z_]+$/", $id_champ))) {
	$chaine_classes="";
	if($_SESSION['statut']=='professeur') {
		if($mode2=="toutes") {
			$sql="SELECT id, classe FROM classes ORDER BY classe;";
			$chaine_classes="Affichage de <strong>toutes les classes</strong> ou <a href='#' onclick=\"recherche_classes_ajax_ele('$id_champ', '')\">seulement mes classes</a><br />";
		}
		else {
			$sql="SELECT DISTINCT c.id, c.classe FROM classes c, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE c.id=jgc.id_classe AND jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe;";
			$chaine_classes="Affichage de <a href='#' onclick=\"recherche_classes_ajax_ele('$id_champ', 'toutes')\">toutes les classes</a> ou <strong> seulement mes classes</strong><br />";
		}
	}
	else {
		$sql="SELECT id, classe FROM classes ORDER BY classe;";
	}
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$cpt=0;
	while($lig=mysqli_fetch_object($res)) {
		if($cpt>0) {
			$chaine_classes.=", &nbsp; &nbsp; ";
		}

		$chaine_classes.="<a href=\"#\" onclick=\"recherche_ele_clas_ajax_ele('$id_champ', $lig->id);return false;\" title=\"Afficher la liste des élèves de la classe de $lig->classe\">$lig->classe</a><a href='#' onclick=\"complete_contenu_champ_ajax_ele('$id_champ', '".addslashes($lig->classe)."');return false;\" title=\"Compléter le champ '$id_champ' avec le nom de la classe\"><img src='$gepiPath/images/icons/add.png' class='icone16' alt='Compléter' /></a>";

		$cpt++;
	}

	echo $chaine_classes;

	die();
}
// Commenter la section else{} ci-dessous pour tester la page sans utiliser les fonctions de share-html.inc.php
// insere_lien_recherche_ajax_ele($id_champ), insere_fonctions_js_recherche_ajax_ele(), insere_infobulle_recherche_ajax_ele(), insere_tout_le_necessaire_recherche_ajax_ele($id_champ)
else {
	echo "<p>Aucune valeur n'a été transmise.</p>";
	die();
}

//**************** EN-TETE *****************
$titre_page = "Recherche élève";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE *****************

//debug_var();
echo "<p class='bold'><a href='../accueil.php'>Accueil</a></p>

<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		<p>Formulaire bidon, sans action effective... juste là pour tester la recherche.</p>
		<input type='text' name='chaine_ele' id='chaine_ele' value='' />
		<input type='submit' value='Valider' />
	</fieldset>
</form>";

echo insere_tout_le_necessaire_recherche_ajax_ele('chaine_ele', " Recherche");
/*
echo "<p><a href=\"#\" onclick=\"recherche_classes_ajax_ele('chaine_ele', ''); afficher_div('div_recherche_ajax_ele','y',10,10); return false;\">Recherche <img src='$gepiPath/images/icons/chercher_eleve.png' width='24' height='16' alt='Recherche' /></a></p>

<script type='text/javascript'>
	function recherche_ele_clas_ajax_ele(id_champ, id_classe) {
		new Ajax.Updater($('div_liste_ele_ajax_ele'),'".$_SERVER['PHP_SELF']."?id_classe='+id_classe+'&id_champ='+id_champ,{method: 'get'});
	}

	function remplace_contenu_champ_ajax_ele(id_champ, texte) {
		document.getElementById(id_champ).value=texte;
	}

	function complete_contenu_champ_ajax_ele(id_champ, texte) {
		document.getElementById(id_champ).value+=texte;
	}

	function recherche_classes_ajax_ele(id_champ, mode2) {
		//alert('plop');

		// S'il y a plusieurs liens de recherche pour plusieurs champs,
		// il faut vider la liste des élèves précédente pour ne pas conserver
		// des liens élèves pointant vers le champ associé à la dernière recherche.
		$('div_liste_ele_ajax_ele').innerHTML='';

		if(mode2=='') {
			new Ajax.Updater($('p_div_recherche_ajax_ele'),'".$_SERVER['PHP_SELF']."?mode=get_classes&id_champ='+id_champ,{method: 'get'});
		}
		else {
			new Ajax.Updater($('p_div_recherche_ajax_ele'),'".$_SERVER['PHP_SELF']."?mode=get_classes&mode2='+mode2+'&id_champ='+id_champ,{method: 'get'});
		}
	}
</script>";

$chaine_classes="";
$titre_infobulle="Recherche élève";
$texte_infobulle="<p>Recherche élève parmi les classes&nbsp;:</p>
<p id='p_div_recherche_ajax_ele'>$chaine_classes</p>
<div id='div_liste_ele_ajax_ele'></div>";
$tabdiv_infobulle[]=creer_div_infobulle("div_recherche_ajax_ele",$titre_infobulle, "", $texte_infobulle, "",35,0,'y','y','n','n',2);
*/
require("../lib/footer.inc.php");
?>
