<?php
/*
* Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/affiche_listes2.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/affiche_listes2.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Genèse des classes: Affichage de listes (2)',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$projet=isset($_POST['projet']) ? $_POST['projet'] : (isset($_GET['projet']) ? $_GET['projet'] : NULL);

$user_temp_directory=get_user_temp_directory();

if((isset($_GET['mode']))&&($_GET['mode']=='affiche_tab_chgt_clas')&&(isset($projet))&&(isset($_GET['login_ele']))) {
	include("gc_func.inc.php");

	$classe_fut=get_classe_fut();
	$tab_opt_exclue=get_tab_opt_exclue();

	include("lib_gc.php");

	$sql="SELECT e.nom, e.prenom, e.elenoet, geo.* FROM gc_eleves_options geo, eleves e WHERE e.login=geo.login AND projet='".$projet."' AND e.login='".$_GET['login_ele']."';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Élève non trouvé.</p>";
	}
	else {
		$lig=mysqli_fetch_object($res);

		$image=nom_photo($lig->elenoet);
		if($image!="") {
			$tab=redim_img($image, 100, 100);
			echo "<div style='float:left; width:100px;'><img src='".$image."' alt='Photo' width='".$tab[0]."' height=".$tab[1]."' /></div>";
		}

		$chaine_profil="";
		if($lig->profil=='GC') {
			$chaine_profil="<span style='color:red' title=\"Gros Cas\"> (GC)</span>";
		}
		elseif($lig->profil=='GC') {
			$chaine_profil="<span style='color:red' title=\"Gros Cas\"> (GC)</span>";
		}
		elseif($lig->profil=='B') {
			$chaine_profil="<span style='color:blue' title=\"Bien (action positive pour la classe)\"> (B)</span>";
		}
		elseif($lig->profil=='TB') {
			$chaine_profil="<span style='color:blue' title=\"Très bien (action très positive pour la classe)\"> (TB)</span>";
		}

		$chaine_moy="";
		if($lig->moy!='') {
			if($lig->moy<7) {
				$chaine_moy.=" <span style='color:red;' title=\"Moyenne générale sur l'année\">";
			}
			elseif($lig->moy<9) {
				$chaine_moy.=" <span style='color:orange;' title=\"Moyenne générale sur l'année\">";
			}
			elseif($lig->moy<12) {
				$chaine_moy.=" <span style='color:gray;' title=\"Moyenne générale sur l'année\">";
			}
			elseif($lig->moy<15) {
				$chaine_moy.=" <span style='color:green;' title=\"Moyenne générale sur l'année\">";
			}
			else {
				$chaine_moy.=" <span style='color:blue;' title=\"Moyenne générale sur l'année\">";
			}
			if($lig->moy!="") {$chaine_moy.="($lig->moy)";} else {$chaine_moy.="&nbsp;\n";}
			$chaine_moy.="</span>";
		}

		// Afficher classe et récapitulatif:
		echo "<p>".$lig->nom." ".$lig->prenom." (".get_chaine_liste_noms_classes_from_ele_login($_GET['login_ele']).") (".trim(preg_replace("/^,/", "",preg_replace("/,$/", "",trim(strtr($lig->liste_opt,"|",", "))))).")".$chaine_profil.$chaine_moy."</p>
			<table class='boireaus'>
				<tr>
					".ligne_entete_classe_future()."
				</tr>
				<tr class='lig1'>
					".ligne_choix_classe_future($_GET['login_ele'])."
				</tr>
			</table>";

	}
	die();
/*
			<table class='boireaus'>
				<tr>
					".ligne_entete_classe_future()."
				</tr>
				<tr class='lig1'>
					".ligne_choix_classe_future($lig->login)."
				</tr>
			</table>
*/
}

if((isset($projet))&&(isset($_POST['chgt_classe']))&&(isset($_POST['login_ele']))&&(isset($_POST['classe_fut']))) {
	$temoin="y";
	if(($_POST['classe_fut']!='')&&($_POST['classe_fut']!='Red')&&($_POST['classe_fut']!='Dep')) {
		$sql="SELECT 1=1 FROM gc_divisions WHERE statut='future' AND classe='".$_POST['classe_fut']."' AND projet='$projet';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			$msg="La classe <b>".$_POST['classe_fut']."</b> n'existe pas.<br />";
			$temoin="n";
		}
	}

	if($temoin=="y") {
		$sql="UPDATE gc_eleves_options SET classe_future='".$_POST['classe_fut']."' WHERE login='".$_POST['login_ele']."' AND projet='$projet';";
		//$msg=$sql;
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$res) {
			$msg="ERREUR lors du changement de classe de ".get_nom_prenom_eleve($_POST['login_ele'])."<br />\n";
		}
		else {
			$msg="Changement de classe de <b>".get_nom_prenom_eleve($_POST['login_ele'])."</b> vers <b>".$_POST['classe_fut']."</b> effectué.<br />\n";
		}
	}
}

if((isset($projet))&&(isset($_POST['valider_enregistrement_nom_aff']))&&(isset($_POST['id_aff']))) {
	check_token();

	$nom_aff=isset($_POST['nom_aff']) ? $_POST['nom_aff'] : NULL;

	$description_aff=isset($_POST['description_aff']) ? $_POST['description_aff'] : NULL;
	$description_aff=isset($NON_PROTECT['description_aff']) ? traitement_magic_quotes(corriger_caracteres($NON_PROTECT['description_aff'])) : NULL;

	if((!isset($nom_aff))||(!isset($description_aff))) {
		$msg="ERREUR : Aucun nom d'affichage ou aucune description proposée.<br />";
	}
	else {
		$sql="SELECT 1=1 FROM gc_noms_affichages WHERE id_aff='".$_POST['id_aff']."' AND projet='$projet';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$sql="INSERT INTO gc_noms_affichages SET projet='$projet', id_aff='".$_POST['id_aff']."', nom='".remplace_accents($nom_aff, "all")."', description='".$description_aff."';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$insert) {
				$msg="Erreur lors du nommage de l'affichage.<br />";
			}
			else {
				$msg="Nommage de l'affichage effectué.<br />";
			}
		}
		else {
			$sql="UPDATE gc_noms_affichages SET nom='".remplace_accents($nom_aff, "all")."', description='".$description_aff."' WHERE id_aff='".$_POST['id_aff']."' AND projet='$projet';";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$update) {
				$msg="Erreur lors du nommage de l'affichage.<br />";
			}
			else {
				$msg="Nommage de l'affichage effectué.<br />";
			}
		}
	}
}

function get_infos_gc_affichage($id_aff) {
	global $projet;
	$tab=array();

	$sql="SELECT * FROM gc_noms_affichages WHERE id_aff='$id_aff' AND projet='$projet';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		//$tab["id"]=$lig->id;
		$tab["id_aff"]=$lig->id_aff;
		$tab["nom"]=$lig->nom;
		$tab["description"]=$lig->description;
		$tab["nomme"]=true;
	}
	else {
		$tab["id_aff"]=$id_aff;
		$tab["nom"]="Affichage n°".$id_aff;
		$tab["description"]="";
		$tab["nomme"]=false;
	}

	return $tab;
}

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$charger_js_dragresize="y";

//**************** EN-TETE *****************
$titre_page = "Genèse classe: affichage de listes";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

function mediane($tab_notes) {
	if(count($tab_notes)>0) {
		$milieu=floor(count($tab_notes)/2);
		if((count($tab_notes))%2==0){
			return ($tab_notes[$milieu]+$tab_notes[$milieu-1])/2;
		}
		else{
			return $tab_notes[$milieu];
		}
	}
	else {
		return "X";
	}
}

function moyenne($tab_notes) {
	if(count($tab_notes)>0) {
		return round(100*(array_sum($tab_notes)/count($tab_notes)))/100;
	}
	else {
		return "X";
	}
}


if((!isset($projet))||($projet=="")) {
	echo "<p style='color:red'>ERREUR: Le projet n'est pas choisi.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='index.php?projet=$projet'>Retour</a>";
//echo "</div>\n";

$id_aff=isset($_POST['id_aff']) ? $_POST['id_aff'] : (isset($_GET['id_aff']) ? $_GET['id_aff'] : NULL);

	// Les requêtes sont choisies, on va procéder à l'affichage des élèves correspondants
	// Affichage des listes pour $projet et $id_aff

	echo " | <a href='".$_SERVER['PHP_SELF']."?projet=$projet'>Autre sélection</a>";

	$id_aff=isset($_POST['id_aff']) ? $_POST['id_aff'] : (isset($_GET['id_aff']) ? $_GET['id_aff'] : NULL);
	if((preg_replace("/[0-9]/","",$id_aff)!="")||($id_aff=="")) {unset($id_aff);}
	if(!isset($id_aff)) {
		echo "<p>ERREUR: La variable 'id_aff' n'est pas affectée.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$tab_aff_courant=get_infos_gc_affichage($id_aff);

	//echo " | <a href='".$_SERVER['PHP_SELF']."?projet=$projet&amp;id_aff=$id_aff'>Modifier la liste de requêtes pour l'".casse_mot($tab_aff_courant['nom'], "min")."</a>";
	echo " | <a href='affiche_listes.php?projet=$projet&amp;id_aff=$id_aff'>Modifier la liste de requêtes pour l'".casse_mot($tab_aff_courant['nom'], "min")."</a>";
	echo "</p>\n";

	echo "<div style='float:right; width:7em; text-align:center;' class='fieldset_opacite50'><a href='javascript:repositionner_infobulle()'>Repositionner</a></div>";

	echo "<h2>Projet $projet : Affichage</h2>\n";

	//=========================================================
	// Liste des affichages précédemment programmés pour ce projet:
	$sql="SELECT DISTINCT id_aff,projet FROM gc_affichages WHERE projet='$projet' ORDER BY id_aff;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "<div id='div_autres_aff' style='width:10em; float: right; text-align:center;'>\n";
		//echo "Autres affichages&nbsp;: ";
		echo "<p>Liste des affichages&nbsp;: ";
		$cpt=0;
		while($lig_tmp=mysqli_fetch_object($res)) {
			if($cpt>0) {echo ", ";}
			$tab_affichages[$lig_tmp->id_aff]=get_infos_gc_affichage($lig_tmp->id_aff);
			if($tab_affichages[$lig_tmp->id_aff]['nomme']) {
				echo "<a href='?projet=$lig_tmp->projet&amp;id_aff=$lig_tmp->id_aff&amp;afficher_listes=y' title=\"Affichage n°".$lig_tmp->id_aff.":\n".$tab_affichages[$lig_tmp->id_aff]['description']."\">".$tab_affichages[$lig_tmp->id_aff]['nom']."</a>\n";
			}
			else {
				echo "<a href='?projet=$lig_tmp->projet&amp;id_aff=$lig_tmp->id_aff&amp;afficher_listes=y'>$lig_tmp->id_aff</a>\n";
			}
			//echo "<a href='?projet=$lig_tmp->projet&amp;id_aff=$lig_tmp->id_aff&amp;afficher_listes=y'>$lig_tmp->id_aff</a>\n";
			$cpt++;
		}
		echo "</p>\n";

		echo "<hr />\n";

		echo "<div id='div_ods' style='text-align:center; border:1px solid black;' class='fieldset_opacite50'>\n";
		echo "</div>\n";
	
		echo "<hr />\n";

		echo "<div id='div_divers' style='text-align:center;'>\n";
		echo "<a href='#' onclick=\"afficher_div('recap_eff','y',-100,20);return false;\">Effectifs des requêtes</a>";
		echo "</div>\n";

		echo "</div>\n";
	}
	//=========================================================


	// Affichage...
	//Construire la requête SQL et l'afficher

	$eff_lv1=-1;
	$eff_lv2=-1;
	$eff_lv3=-1;
	$eff_autre=-1;

	/*
	function echo_debug($texte) {
		$debug=0;
		if($debug==1) {
			echo $texte;
		}
	}
	*/

	//$id_aff=isset($_POST['id_aff']) ? $_POST['id_aff'] : NULL;
	$id_aff=isset($_POST['id_aff']) ? $_POST['id_aff'] : (isset($_GET['id_aff']) ? $_GET['id_aff'] : NULL);
	if((preg_replace("/[0-9]/","",$id_aff)!="")||($id_aff=="")) {unset($id_aff);}
	if(!isset($id_aff)) {
		echo "<p>ERREUR: La variable 'id_aff' n'est pas affectée.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		unset($tab_id_req);
		$tab_id_req=array();
		$sql="SELECT DISTINCT id_req FROM gc_affichages WHERE projet='$projet' AND id_aff='$id_aff' ORDER BY id_req;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig=mysqli_fetch_object($res)) {
			$tab_id_req[]=$lig->id_req;
		}
	}






// 20140623
//necessaire_affichage_infobulle_bull_simpl();


//============================================================
//============================================================
//============================================================
	//On n'effectue qu'une fois ces requêtes communes hors de la boucle sur la liste des requêtes associées à l'affichage choisi

	$classe_fut=array();
	$sql="SELECT DISTINCT classe FROM gc_divisions WHERE projet='$projet' AND statut='future' ORDER BY classe;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucune classe future n'est encore définie pour ce projet.</p>\n";
		// Est-ce que cela doit vraiment bloquer la saisie des options?
		require("../lib/footer.inc.php");
		die();
	}
	else {
		$tab_opt_exclue=array();

		$chaine_classes_fut="tab_classes_fut=new Array(";
		$cpt_tmp=0;
		while($lig=mysqli_fetch_object($res)) {
			$classe_fut[]=$lig->classe;
			if($cpt_tmp>0) {$chaine_classes_fut.=",";}
			$chaine_classes_fut.="'".$lig->classe."'";

			$tab_opt_exclue["$lig->classe"]=array();
			//=========================
			// Options exlues pour la classe
			$sql="SELECT opt_exclue FROM gc_options_classes WHERE projet='$projet' AND classe_future='$lig->classe';";
			$res_opt_exclues=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig_opt_exclue=mysqli_fetch_object($res_opt_exclues)) {
				$tab_opt_exclue["$lig->classe"][]=mb_strtoupper($lig_opt_exclue->opt_exclue);
			}
			//=========================

			$cpt_tmp++;
		}
		$classe_fut[]="Red";
		$classe_fut[]="Dep";
		$classe_fut[]=""; // Vide pour les Non Affectés

		$chaine_classes_fut.=",'Red','Dep','')";
	}
	
	$id_classe_actuelle=array();
	$classe_actuelle=array();
	$sql="SELECT DISTINCT id_classe,classe FROM gc_divisions WHERE projet='$projet' AND statut='actuelle' ORDER BY classe;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucune classe actuelle n'est encore sélectionnée pour ce projet.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		while($lig=mysqli_fetch_object($res)) {
			$id_classe_actuelle[]=$lig->id_classe;
			$classe_actuelle[]=$lig->classe;
		}

		// On ajoute redoublants et arrivants
		$id_classe_actuelle[]='Red';
		$classe_actuelle[]='Red';
	
		$id_classe_actuelle[]='Arriv';
		$classe_actuelle[]='Arriv';
	}

	$chaine_lv1="tab_lv1=new Array(";
	$lv1=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv1' ORDER BY opt;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$cpt_tmp=0;
		while($lig=mysqli_fetch_object($res)) {
			$lv1[]=$lig->opt;
			if($cpt_tmp>0) {$chaine_lv1.=",";}
			$chaine_lv1.="'".$lig->opt."'";
			$cpt_tmp++;
		}
	}
	$chaine_lv1.=")";


	$chaine_lv2="tab_lv2=new Array(";
	$lv2=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv2' ORDER BY opt;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$cpt_tmp=0;
		while($lig=mysqli_fetch_object($res)) {
			$lv2[]=$lig->opt;
			if($cpt_tmp>0) {$chaine_lv2.=",";}
			$chaine_lv2.="'".$lig->opt."'";
			$cpt_tmp++;
		}
	}
	$chaine_lv2.=")";
	
	$chaine_lv3="tab_lv3=new Array(";
	$lv3=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv3' ORDER BY opt;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$cpt_tmp=0;
		while($lig=mysqli_fetch_object($res)) {
			$lv3[]=$lig->opt;
			if($cpt_tmp>0) {$chaine_lv3.=",";}
			$chaine_lv3.="'".$lig->opt."'";
			$cpt_tmp++;
		}
	}
	$chaine_lv3.=")";
	
	$autre_opt=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='autre' ORDER BY opt;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$autre_opt[]=$lig->opt;
		}
	}

	echo "<script type='text/javascript'>
	// Tableau de listes pour assurer la colorisation des lignes
	var $chaine_classes_fut;
	var $chaine_lv1;
	var $chaine_lv2;
	var $chaine_lv3;
</script>\n";

	//=============================
	include("lib_gc.php");
	// On y initialise les couleurs
	// Il faut que le tableaux $classe_fut soit initialisé.
	//=============================

	necessaire_bull_simple();

	//=============================
	$titre="Sélection du profil";
	$texte="<p style='text-align:center;'>";
	for($loop=0;$loop<count($tab_profil);$loop++) {
		if($loop>0) {$texte.=" - ";}
		$texte.="<a href='#' onclick=\"set_profil('".$tab_profil[$loop]."');return false;\">$tab_profil[$loop]</a>";
	}
	$texte.="</p>\n";
	$tabdiv_infobulle[]=creer_div_infobulle('div_set_profil',$titre,"",$texte,"",14,0,'y','y','n','n');

	echo "<script type='text/javascript'>
	var couleur_profil=new Array($chaine_couleur_profil);
	var tab_profil=new Array($chaine_profil);

	function set_profil(profil) {
		var cpt=document.getElementById('profil_courant').value;
		document.getElementById('profil_'+cpt).value=profil;

		for(m=0;m<couleur_profil.length;m++) {
			if(document.getElementById('profil_'+cpt).value==tab_profil[m]) {
				document.getElementById('div_profil_'+cpt).style.color=couleur_profil[m];
			}
		}

		document.getElementById('div_profil_'+cpt).innerHTML=profil;
		cacher_div('div_set_profil');
	}

	function affiche_set_profil(cpt) {
		document.getElementById('profil_courant').value=cpt;
		afficher_div('div_set_profil','y',100,100);
	}
</script>\n";
	//=============================

	// 20150617

	// AJOUTER DES LIENS TRIER PAR
	// classe d'origine : gc_eleves_options.id_classe_actuelle
	// profil : gc_eleves_options.profil
	// niveau : gc_eleves_options.moy
	$order_by=isset($_POST['order_by']) ? $_POST['order_by'] : (isset($_GET['order_by']) ? $_GET['order_by'] : NULL);
	echo "<p>
	<a href='".$_SERVER['PHP_SELF']."?projet=".$projet."&id_aff=$id_aff&order_by=e.nom,e.prenom'>Trier les élèves par ordre alphabétique</a><br />
	<a href='".$_SERVER['PHP_SELF']."?projet=".$projet."&id_aff=$id_aff&order_by=id_classe_actuelle,e.nom,e.prenom'>Trier les élèves par classe d'origine</a><br />
	<a href='".$_SERVER['PHP_SELF']."?projet=".$projet."&id_aff=$id_aff&order_by=profil,e.nom,e.prenom'>Trier les élèves par profil</a><br />
	<a href='".$_SERVER['PHP_SELF']."?projet=".$projet."&id_aff=$id_aff&order_by=moy DESC,e.nom,e.prenom'>Trier les élèves par niveau</a><br />
</p>";
	if(!isset($order_by)) {
		$order_by="e.nom,e.prenom";
	}

	// Pouvoir modifier la largeur
	$largeur_div=10;
	// Pouvoir remplacer les DIV par des drag resizable
	$cpt=0;
	for($loop=0;$loop<count($tab_id_req);$loop++) {
		$id_req=$tab_id_req[$loop];

		$titre_infobulle="Requête n°".$id_req;

		$tab_requete=array();
		$tab_requete_csv=array();

		//echo "<div style='float:left; width:".$largeur_div."em; border:1px solid black;margin:3px;'>";

		$chaine_nom_requete="";
		$sql="SELECT DISTINCT nom_requete FROM gc_affichages WHERE projet='$projet' AND id_aff='$id_aff' AND id_req='".$id_req."' AND nom_requete!='';";
		//$txt_requete.="<br />".$sql."<br />";
		$res_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig_tmp=mysqli_fetch_object($res_tmp)) {
			$chaine_nom_requete=" (<em>".$lig_tmp->nom_requete."</em>)";
		}

		$lien_affect="<p><a name='requete_$id_req'></a><a href='affiche_listes.php?editer_requete=y&amp;projet=$projet&amp;id_aff=$id_aff&amp;id_req=$id_req";
		//$fin_lien_affect="' target='_blank'";
		$fin_lien_affect="' alt='Modifier la requête n°$id_req' title='Modifier la requête n°$id_req'><b>Requête n°$id_req</b>".$chaine_nom_requete."</a>";
		//$fin_lien_affect.=" - <a href='#' onclick=\"afficher_div('div_requete_$id_req','y',100,100); return false;\"><img src='../images/vert.png' width='16' height='16' title='Afficher la requête n°$id_req en infobulle' /></a>";

		$fin_lien_affect.=" - <a href='affect_eleves_classes.php?choix_affich=y&amp;requete_definie=y&amp;projet=$projet&amp;id_aff=$id_aff&amp;id_req=$id_req' title=\"Affecter les élèves de cette requête dans des classes.\"><img src='../images/icons/tableau_couleur.png' class='icone16' alt='Affecter' /></a>";

		$fin_lien_affect.="<br />";

		//=========================
		// Début de la requête à forger pour ne retenir que les élèves souhaités
		$sql_ele="SELECT DISTINCT e.login, e.nom,e.prenom, e.sexe, geo.moy, geo.profil, geo.id_classe_actuelle FROM eleves e, gc_eleves_options geo WHERE e.login=geo.login AND projet='$projet' AND classe_future!='Dep' AND classe_future!='Red'";

		$sql_ele_id_classe_act="";
		$sql_ele_classe_fut="";
		$sql_avec_profil="";
		$sql_sans_profil="";

		/*
		$txt_ele_id_classe_act="";
		$txt_ele_classe_fut="";
		$txt_avec_profil="";
		$txt_sans_profil="";
		*/

		//=========================

		$sql="SELECT * FROM gc_affichages WHERE projet='$projet' AND id_aff='$id_aff' AND id_req='$id_req' ORDER BY type;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig=mysqli_fetch_object($res)) {
			switch($lig->type) {
				case 'id_clas_act':
					$id_clas_act[]=$lig->valeur;
					if(!isset($tab_requete[0])) {$tab_requete[0]="Classe actuelle (<span style='color:black;'>";$tab_requete_csv[0]="Classe actuelle (";} else {$tab_requete[0].=", ";$tab_requete_csv[0].=", ";}
					if(($lig->valeur=='Red')||($lig->valeur=='Arriv')) {
						$tab_requete[0].=$lig->valeur;$tab_requete_csv[0].=$lig->valeur;
					}
					else {
						$tab_requete[0].=get_class_from_id($lig->valeur);$tab_requete_csv[0].=get_class_from_id($lig->valeur);
					}

					$lien_affect.="&amp;id_clas_act[]=$lig->valeur";

					//$sql_ele.=" AND id_classe_actuelle='$lig->valeur'";

					if($sql_ele_id_classe_act!='') {$sql_ele_id_classe_act.=" OR ";}
					$sql_ele_id_classe_act.="id_classe_actuelle='$lig->valeur'";
					break;

				case 'clas_fut':
					$clas_fut[]=$lig->valeur;
					if(!isset($tab_requete[1])) {$tab_requete[1]="Classe future (<span style='color:black;'>";$tab_requete_csv[1]="Classe future (";} else {$tab_requete[1].=", ";$tab_requete_csv[1].=", ";}
					if(($lig->valeur=='Red')||($lig->valeur=='Dep')) {
						$tab_requete[1].=$lig->valeur;$tab_requete_csv[1].=$lig->valeur;
					}
					else {
						//$tab_requete[1].=get_class_from_id($lig->valeur);$tab_requete_csv[1].=get_class_from_id($lig->valeur);
						$tab_requete[1].=$lig->valeur;$tab_requete_csv[1].=$lig->valeur;
					}

					$lien_affect.="&amp;clas_fut[]=$lig->valeur";

					//$sql_ele.=" AND classe_future='$lig->valeur'";

					if($sql_ele_classe_fut!='') {$sql_ele_classe_fut.=" OR ";}
					$sql_ele_classe_fut.="classe_future='$lig->valeur'";
					break;

				case 'avec_lv1':
					$avec_lv1[]=$lig->valeur;
					if(!isset($tab_requete[2])) {$tab_requete[2]="Avec les options (<span style='color:green;'>";$tab_requete_csv[2]="Avec les options (";} else {$tab_requete[2].=", ";$tab_requete_csv[2].=", ";}
					$tab_requete[2].=$lig->valeur;$tab_requete_csv[2].=$lig->valeur;

					$lien_affect.="&amp;avec_lv1[]=$lig->valeur";

					$sql_ele.=" AND liste_opt LIKE '%|$lig->valeur|%'";
					break;
				case 'avec_lv2':
					$avec_lv2[]=$lig->valeur;
					if(!isset($tab_requete[2])) {$tab_requete[2]="Avec les options (<span style='color:green;'>";$tab_requete_csv[2]="Avec les options (";} else {$tab_requete[2].=", ";$tab_requete_csv[2].=", ";}
					$tab_requete[2].=$lig->valeur;$tab_requete_csv[2].=$lig->valeur;

					$lien_affect.="&amp;avec_lv2[]=$lig->valeur";

					$sql_ele.=" AND liste_opt LIKE '%|$lig->valeur|%'";
					break;
				case 'avec_lv3':
					$avec_lv3[]=$lig->valeur;
					if(!isset($tab_requete[2])) {$tab_requete[2]="Avec les options (<span style='color:green;'>";$tab_requete_csv[2]="Avec les options (";} else {$tab_requete[2].=", ";$tab_requete_csv[2].=", ";}
					$tab_requete[2].=$lig->valeur;$tab_requete_csv[2].=$lig->valeur;

					$lien_affect.="&amp;avec_lv3[]=$lig->valeur";

					$sql_ele.=" AND liste_opt LIKE '%|$lig->valeur|%'";
					break;

				case 'avec_autre':
					$avec_autre[]=$lig->valeur;
					if(!isset($tab_requete[2])) {$tab_requete[2]="Avec les options (<span style='color:green;'>";$tab_requete_csv[2]="Avec les options (";} else {$tab_requete[2].=", ";$tab_requete_csv[2].=", ";}
					$tab_requete[2].=$lig->valeur;$tab_requete_csv[2].=$lig->valeur;

					$lien_affect.="&amp;avec_autre[]=$lig->valeur";

					$sql_ele.=" AND liste_opt LIKE '%|$lig->valeur|%'";
					break;

				case 'avec_profil':
					$avec_profil[]=$lig->valeur;
					if(!isset($tab_requete[1])) {$tab_requete[1]="Avec profil (<span style='color:black;'>";$tab_requete_csv[1]="Avec profil (";} else {$tab_requete[1].=", ";$tab_requete_csv[1].=", ";}
					$tab_requete[1].=$lig->valeur;$tab_requete_csv[1].=$lig->valeur;

					$lien_affect.="&amp;avec_profil[]=$lig->valeur";

					if($sql_avec_profil!='') {$sql_avec_profil.=" OR ";}
					$sql_avec_profil.="profil='$lig->valeur'";
					break;

				case 'sans_lv1':
					$sans_lv1[]=$lig->valeur;
					if(!isset($tab_requete[3])) {$tab_requete[3]="Sans les options (<span style='color:red;'>";$tab_requete_csv[3]="Sans les options (";} else {$tab_requete[3].=", ";$tab_requete_csv[3].=", ";}
					$tab_requete[3].=$lig->valeur;$tab_requete_csv[3].=$lig->valeur;

					$lien_affect.="&amp;sans_lv1[]=$lig->valeur";

					$sql_ele.=" AND liste_opt NOT LIKE '%|$lig->valeur|%'";
					break;
				case 'sans_lv2':
					$sans_lv2[]=$lig->valeur;
					if(!isset($tab_requete[3])) {$tab_requete[3]="Sans les options (<span style='color:red;'>";$tab_requete_csv[3]="Sans les options (";} else {$tab_requete[3].=", ";$tab_requete_csv[3].=", ";}
					$tab_requete[3].=$lig->valeur;$tab_requete_csv[3].=$lig->valeur;

					$lien_affect.="&amp;sans_lv2[]=$lig->valeur";

					$sql_ele.=" AND liste_opt NOT LIKE '%|$lig->valeur|%'";
					break;
				case 'sans_lv3':
					$sans_lv3[]=$lig->valeur;
					if(!isset($tab_requete[3])) {$tab_requete[3]="Sans les options (<span style='color:red;'>";$tab_requete_csv[3]="Sans les options (";} else {$tab_requete[3].=", ";$tab_requete_csv[3].=", ";}
					$tab_requete[3].=$lig->valeur;$tab_requete_csv[3].=$lig->valeur;

					$lien_affect.="&amp;sans_lv3[]=$lig->valeur";

					$sql_ele.=" AND liste_opt NOT LIKE '%|$lig->valeur|%'";
					break;
				case 'sans_autre':
					$sans_autre[]=$lig->valeur;
					if(!isset($tab_requete[3])) {$tab_requete[3]="Sans les options (<span style='color:red;'>";$tab_requete_csv[3]="Sans les options (";} else {$tab_requete[3].=", ";$tab_requete_csv[3].=", ";}
					$tab_requete[3].=$lig->valeur;$tab_requete_csv[3].=$lig->valeur;

					$lien_affect.="&amp;sans_autre[]=$lig->valeur";

					$sql_ele.=" AND liste_opt NOT LIKE '%|$lig->valeur|%'";
					break;

				case 'sans_profil':
					$sans_profil[]=$lig->valeur;
					if(!isset($tab_requete[1])) {$tab_requete[1]="Sans profil (<span style='color:black;'>";$tab_requete_csv[1]="Sans profil (";} else {$tab_requete[1].=", ";$tab_requete_csv[1].=", ";}
					$tab_requete[1].=$lig->valeur;$tab_requete_csv[1].=$lig->valeur;

					$lien_affect.="&amp;sans_profil[]=$lig->valeur";

					if($sql_sans_profil!='') {$sql_sans_profil.=" AND ";}
					$sql_sans_profil.="profil='$lig->valeur'";
					break;
			}
		}

		$lien_affect.=$fin_lien_affect;




		// On réinitialise le tableau pour faire table rase des logins de la requête précédente
		//$tab_ele=array();

		if($sql_ele_id_classe_act!='') {$sql_ele.=" AND ($sql_ele_id_classe_act)";}
		if($sql_ele_classe_fut!='') {$sql_ele.=" AND ($sql_ele_classe_fut)";}
		if($sql_avec_profil!='') {$sql_ele.=" AND ($sql_avec_profil)";}
		if($sql_sans_profil!='') {$sql_ele.=" AND ($sql_sans_profil)";}

		$sql_ele.=" ORDER BY ".$order_by.";";

		$texte_infobulle="";
		$texte_infobulle.="<p>$lien_affect<br />";
		foreach($tab_requete as $key=>$value) {
			$texte_infobulle.=$value."</span>)<br />\n";
		}
		$texte_infobulle.="</p>
<div align='center'>
	<table class='boireaus boireaus_alt'>";
		//echo "<table class='boireaus'>";

		$sql_ele.=";";
		//echo "$sql_ele<br />\n";
		$eff_M=0;
		$eff_F=0;
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql_ele);
		while ($lig_ele=mysqli_fetch_object($res_ele)) {
			//$tab_ele[]=$lig_ele->login;
			if($lig_ele->sexe=='F') {
				$eff_F++;
			}
			else {
				$eff_M++;
			}
			if(!isset($nom_classe[$lig_ele->id_classe_actuelle])) {
				$nom_classe[$lig_ele->id_classe_actuelle]=get_nom_classe($lig_ele->id_classe_actuelle);
			}
			//get_nom_prenom_eleve($lig_ele->login)
			$texte_infobulle.="
		<tr>
			<td style='text-align:left;' title=\"".$lig_ele->nom." ".$lig_ele->prenom."
Classe :    ".$nom_classe[$lig_ele->id_classe_actuelle]."
Moy.gen : ".$lig_ele->moy."
Profil :      ".$lig_ele->profil."\"

				onclick=\"afficher_ele('".$lig_ele->login."')\">
				".$lig_ele->nom." ".$lig_ele->prenom."
			</td>
		</tr>";
		}

		$texte_infobulle.="
	</table>
</div>";

		//$texte_infobulle.="</div>";
		$texte_infobulle="<div style='float:right; width:2em; text-align:right;'><span title='Effectif'>".($eff_M+$eff_F)."</span><br /><span style='font-size:x-small'>(<span title='Garçons'>$eff_M</span>/<span title='Filles'>$eff_F</span>)</span></div>".$texte_infobulle;

		$tabdiv_infobulle[]=creer_div_infobulle('div_requete_num_'.$loop,$titre_infobulle,"",$texte_infobulle,"",14,0,'y','y','n','n');
	}
	//echo "<div style='clear:both;'>&nbsp;</div>";

	$titre_chgt_classe="Infos et actions";
	$texte_chgt_classe="<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<div align='center'>
		<div id='div_table_chgt_classe'>
		</div>
		<input type='hidden' id='chgt_classe_login_ele' name='login_ele' value='' />
		<input type='hidden' name='chgt_classe' value='y' />
		<input type='hidden' name='projet' value='$projet' />
		<input type='hidden' name='id_aff' value='$id_aff' />
		<input type='hidden' name='order_by' id='order_by' value='$order_by' />
		<input type='hidden' name='afficher_listes' value='y' />
		<input type='submit' value='Valider' />
	</div>
</form>";
	$tabdiv_infobulle[]=creer_div_infobulle('div_chgt_classe',$titre_chgt_classe,"",$texte_chgt_classe,"",30,0,'y','y','n','n',1000);

	echo "<script type='text/javascript'>
	function afficher_les_infobulles() {
		for(i=0;i<".count($tab_id_req).";i++) {
			afficher_div('div_requete_num_'+i, 'y', eval(10+i*230), 200);

			//document.getElementById('div_requete_num_'+i).style.top=200+'px';
			//document.getElementById('div_requete_num_'+i).style.left=10+i*230+'px';
		}

		//alert('plop');

		for(i=0;i<".count($tab_id_req).";i++) {
			document.getElementById('div_requete_num_'+i).style.top=200+'px';
			document.getElementById('div_requete_num_'+i).style.left=10+i*230+'px';
		}

		setTimeout('repositionner_infobulle()', 500);
	}

	function repositionner_infobulle() {
		//alert('plop');
		for(i=0;i<".count($tab_id_req).";i++) {
			document.getElementById('div_requete_num_'+i).style.top=200+'px';
			document.getElementById('div_requete_num_'+i).style.left=10+i*230+'px';
		}
	}

	function afficher_ele(login_ele) {
		document.getElementById('chgt_classe_login_ele').value=login_ele;

		// Affichage du tableau avec les classes autorisées:
		new Ajax.Updater($('div_table_chgt_classe'),'".$_SERVER['PHP_SELF']."?projet=$projet&mode=affiche_tab_chgt_clas&login_ele='+login_ele,{method: 'get'});

		afficher_div('div_chgt_classe','y',-100,20);
	}

	setTimeout('afficher_les_infobulles()', 2000);
</script>";

require("../lib/footer.inc.php");
?>
