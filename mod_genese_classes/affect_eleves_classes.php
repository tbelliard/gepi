<?php
/*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/affect_eleves_classes.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/affect_eleves_classes.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Genèse des classes: Affectation des élèves',
statut='';";
$insert=mysql_query($sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$projet=isset($_POST['projet']) ? $_POST['projet'] : (isset($_GET['projet']) ? $_GET['projet'] : NULL);


//if((isset($_POST['is_posted']))&&(isset($_POST['valide_aff_classe_fut']))) {
if(isset($_POST['is_posted'])) {
	//echo "GRRRRRRR";
	$eleve=isset($_POST['eleve']) ? $_POST['eleve'] : array();
	$classe_fut=isset($_POST['classe_fut']) ? $_POST['classe_fut'] : array();

	//echo "count(\$eleve)=".count($eleve)."<br />";

	$nb_reg=0;
	$nb_err=0;

	$complement_msg="";
	if(count($eleve)>0) {
		//$sql="DELETE FROM gc_eleve_fut_classe WHERE projet='$projet';";
		//$del=mysql_query($sql);

		$nom_requete=isset($_POST['nom_requete']) ? $_POST['nom_requete'] : '';
		$sql="UPDATE gc_affichages SET nom_requete='".addslashes($_POST['nom_requete'])."' WHERE id_aff='".$_POST['id_aff']."' AND id_req='".$_POST['id_req']."' AND projet='$projet';";
		//echo "$sql<br />";
		$res_nom_req=mysql_query($sql);

		$profil=isset($_POST['profil']) ? $_POST['profil'] : array();

		for($i=0;$i<count($eleve);$i++) {
			//echo "plop<br />";
			/*
			$sql="DELETE FROM gc_eleve_fut_classe WHERE projet='$projet' AND login='$eleve[$i]';";
			//echo "$sql<br />";
			$del=mysql_query($sql);

			$sql="INSERT INTO gc_eleve_fut_classe SET login='$eleve[$i]', classe='$classe_fut[$i]', projet='$projet';";
			//echo "$sql<br />";
			if($insert=mysql_query($sql)) {$nb_reg++;} else {$nb_err++;}
			*/

			if(!isset($classe_fut[$i])) {
				$complement_msg.="<br />Erreur sur la classe de $eleve[$i]";
			}
			else {
				$sql="UPDATE gc_eleves_options SET classe_future='$classe_fut[$i]', profil='$profil[$i]' WHERE login='$eleve[$i]' AND projet='$projet';";
				if($update=mysql_query($sql)) {$nb_reg++;} else {$nb_err++;}
			}
		}
	}

	if($nb_err==0) {
		$msg="$nb_reg enregistrements effectués (".strftime("%d/%m/%Y à %H:%M:%S").").";
	}
	else {
		$msg="ERREUR: $nb_err erreurs lors de l'enregistrement des classes futures,... (".strftime("%d/%m/%Y à %H:%M:%S").")";
	}
	$msg.=$complement_msg;
}

$style_specifique[]="mod_genese_classes/mod_genese_classes";
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Genèse classe: affectation des élèves";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

if((!isset($projet))||($projet=="")) {
	echo "<p style='color:red'>ERREUR: Le projet n'est pas choisi.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//echo "<div class='noprint'>\n";
//echo "<p class='bold'><a href='index.php?projet=$projet'>Retour</a>";
//echo "</div>\n";

$choix_affich=isset($_POST['choix_affich']) ? $_POST['choix_affich'] : (isset($_GET['choix_affich']) ? $_GET['choix_affich'] : NULL);

$id_clas_act=isset($_POST['id_clas_act']) ? $_POST['id_clas_act'] : (isset($_GET['id_clas_act']) ? $_GET['id_clas_act'] : array());
$clas_fut=isset($_POST['clas_fut']) ? $_POST['clas_fut'] : (isset($_GET['clas_fut']) ? $_GET['clas_fut'] : array());
$avec_lv1=isset($_POST['avec_lv1']) ? $_POST['avec_lv1'] : (isset($_GET['avec_lv1']) ? $_GET['avec_lv1'] : array());
$sans_lv1=isset($_POST['sans_lv1']) ? $_POST['sans_lv1'] : (isset($_GET['sans_lv1']) ? $_GET['sans_lv1'] : array());
$avec_lv2=isset($_POST['avec_lv2']) ? $_POST['avec_lv2'] : (isset($_GET['avec_lv2']) ? $_GET['avec_lv2'] : array());
$sans_lv2=isset($_POST['sans_lv2']) ? $_POST['sans_lv2'] : (isset($_GET['sans_lv2']) ? $_GET['sans_lv2'] : array());
$avec_lv3=isset($_POST['avec_lv3']) ? $_POST['avec_lv3'] : (isset($_GET['avec_lv3']) ? $_GET['avec_lv3'] : array());
$sans_lv3=isset($_POST['sans_lv3']) ? $_POST['sans_lv3'] : (isset($_GET['sans_lv3']) ? $_GET['sans_lv3'] : array());
$avec_autre=isset($_POST['avec_autre']) ? $_POST['avec_autre'] : (isset($_GET['avec_autre']) ? $_GET['avec_autre'] : array());
$sans_autre=isset($_POST['sans_autre']) ? $_POST['sans_autre'] : (isset($_GET['sans_autre']) ? $_GET['sans_autre'] : array());

$avec_profil=isset($_POST['avec_profil']) ? $_POST['avec_profil'] : (isset($_GET['avec_profil']) ? $_GET['avec_profil'] : array());
$sans_profil=isset($_POST['sans_profil']) ? $_POST['sans_profil'] : (isset($_GET['sans_profil']) ? $_GET['sans_profil'] : array());

// Choix des élèves à afficher:
//if(!isset($_POST['choix_affich'])) {
if(!isset($choix_affich)) {
	echo "<p class='bold'><a href='index.php?projet=$projet'>Retour</a>";
	echo "</p>\n";

	echo "<h2>Projet $projet</h2>\n";

	$sql="SELECT DISTINCT id_classe, classe FROM gc_divisions WHERE projet='$projet' AND statut='actuelle' ORDER BY classe;";
	$res_clas_act=mysql_query($sql);
	$nb_clas_act=mysql_num_rows($res_clas_act);
	if($nb_clas_act==0) {
		echo "<p>Aucune classe actuelle n'est encore choisie pour ce projet.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT DISTINCT classe FROM gc_divisions WHERE projet='$projet' AND statut='future' ORDER BY classe;";
	$res_clas_fut=mysql_query($sql);
	$nb_clas_fut=mysql_num_rows($res_clas_fut);
	if($nb_clas_fut==0) {
		echo "<p>Aucune classe future n'est encore définie pour ce projet.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	//include("lib_gc.php");


	function tableau_eleves_req($id_aff, $id_req) {
		global $projet;

		$id_clas_act=array();
		$clas_fut=array();
		$avec_lv1=array();
		$sans_lv1=array();
		$avec_lv2=array();
		$sans_lv2=array();
		$avec_lv3=array();
		$sans_lv3=array();
		$avec_autre=array();
		$sans_autre=array();
		
		$avec_profil=array();
		$sans_profil=array();
	
		// Pour utiliser des listes d'affichage
		//$requete_definie=isset($_POST['requete_definie']) ? $_POST['requete_definie'] : (isset($_GET['requete_definie']) ? $_GET['requete_definie'] : 'n');
		//$id_aff=isset($_POST['id_aff']) ? $_POST['id_aff'] : (isset($_GET['id_aff']) ? $_GET['id_aff'] : NULL);
		//$id_req=isset($_POST['id_req']) ? $_POST['id_req'] : (isset($_GET['id_req']) ? $_GET['id_req'] : NULL);
		//if(($requete_definie=='y')&&(isset($id_aff))&&(isset($id_req))) {
			$sql="SELECT * FROM gc_affichages WHERE projet='$projet' AND id_aff='$id_aff' AND id_req='$id_req' ORDER BY type;";
			$res_tmp=mysql_query($sql);
			while($lig_tmp=mysql_fetch_object($res_tmp)) {
				switch($lig_tmp->type) {
					case 'id_clas_act':
						if(!in_array($lig_tmp->valeur,$id_clas_act)) {$id_clas_act[]=$lig_tmp->valeur;}
						break;
					case 'clas_fut':
						if(!in_array($lig_tmp->valeur,$clas_fut)) {$clas_fut[]=$lig_tmp->valeur;}
						break;
	
					case 'avec_lv1':
						if(!in_array($lig_tmp->valeur,$avec_lv1)) {$avec_lv1[]=$lig_tmp->valeur;}
						break;
					case 'avec_lv2':
						if(!in_array($lig_tmp->valeur,$avec_lv2)) {$avec_lv2[]=$lig_tmp->valeur;}
						break;
					case 'avec_lv3':
						if(!in_array($lig_tmp->valeur,$avec_lv3)) {$avec_lv3[]=$lig_tmp->valeur;}
						break;
					case 'avec_autre':
						if(!in_array($lig_tmp->valeur,$avec_autre)) {$avec_autre[]=$lig_tmp->valeur;}
						break;
					case 'avec_profil':
						if(!in_array($lig_tmp->valeur,$avec_profil)) {$avec_profil[]=$lig_tmp->valeur;}
						break;
	
					case 'sans_lv1':
						if(!in_array($lig_tmp->valeur,$sans_lv1)) {$sans_lv1[]=$lig_tmp->valeur;}
						break;
					case 'sans_lv2':
						if(!in_array($lig_tmp->valeur,$sans_lv2)) {$sans_lv2[]=$lig_tmp->valeur;}
						break;
					case 'sans_lv3':
						if(!in_array($lig_tmp->valeur,$sans_lv3)) {$sans_lv3[]=$lig_tmp->valeur;}
						break;
					case 'sans_autre':
						if(!in_array($lig_tmp->valeur,$sans_autre)) {$sans_autre[]=$lig_tmp->valeur;}
						break;
					case 'sans_profil':
						if(!in_array($lig_tmp->valeur,$sans_profil)) {$sans_profil[]=$lig_tmp->valeur;}
						break;
				}
			}
		//}
	
		//=========================
		// Début de la requête à forger pour ne retenir que les élèves souhaités
		$sql_ele="SELECT DISTINCT login FROM gc_eleves_options WHERE projet='$projet' AND classe_future!='Dep' AND classe_future!='Red'";
	
		$sql_ele_id_classe_act="";
		$sql_ele_classe_fut="";
		//=========================
	
		//$chaine_lien_modif_requete="projet=$projet";
	
		$chaine_classes_actuelles="";
		if(count($id_clas_act)>0) {
			for($i=0;$i<count($id_clas_act);$i++) {
				if($i>0) {$sql_ele_id_classe_act.=" OR ";}
				$sql_ele_id_classe_act.="id_classe_actuelle='$id_clas_act[$i]'";
	
				if($i>0) {$chaine_classes_actuelles.=", ";}
				$chaine_classes_actuelles.=get_class_from_id($id_clas_act[$i]);
	
				//$chaine_lien_modif_requete.="&amp;id_clas_act[$i]=".$id_clas_act[$i];
			}
			$sql_ele.=" AND ($sql_ele_id_classe_act)";
		}
	
		$chaine_classes_futures="";
		if(count($clas_fut)>0) {
			for($i=0;$i<count($clas_fut);$i++) {
				if($i>0) {$sql_ele_classe_fut.=" OR ";}
				$sql_ele_classe_fut.="classe_future='$clas_fut[$i]'";
	
				if($i>0) {$chaine_classes_futures.=", ";}
				if($clas_fut[$i]=='') {$chaine_classes_futures.='Non.aff';} else {$chaine_classes_futures.=$clas_fut[$i];}
	
				//$chaine_lien_modif_requete.="&amp;clas_fut[$i]=".$clas_fut[$i];
			}
			$sql_ele.=" AND ($sql_ele_classe_fut)";
		}
	
		$chaine_avec_opt="";
		for($i=0;$i<count($avec_lv1);$i++) {
			$sql_ele.=" AND liste_opt LIKE '%|$avec_lv1[$i]|%'";
	
			if($chaine_avec_opt!="") {$chaine_avec_opt.=", ";}
			$chaine_avec_opt.="<span style='color:green;'>".$avec_lv1[$i]."</span>";
	
			//$chaine_lien_modif_requete.="&amp;avec_lv1[$i]=".$avec_lv1[$i];
		}
	
		for($i=0;$i<count($avec_lv2);$i++) {
			$sql_ele.=" AND liste_opt LIKE '%|$avec_lv2[$i]|%'";
	
			if($chaine_avec_opt!="") {$chaine_avec_opt.=", ";}
			$chaine_avec_opt.="<span style='color:green;'>".$avec_lv2[$i]."</span>";
	
			//$chaine_lien_modif_requete.="&amp;avec_lv2[$i]=".$avec_lv2[$i];
		}
	
		for($i=0;$i<count($avec_lv3);$i++) {
			$sql_ele.=" AND liste_opt LIKE '%|$avec_lv3[$i]|%'";
	
			if($chaine_avec_opt!="") {$chaine_avec_opt.=", ";}
			$chaine_avec_opt.="<span style='color:green;'>".$avec_lv3[$i]."</span>";
	
			//$chaine_lien_modif_requete.="&amp;avec_lv3[$i]=".$avec_lv3[$i];
		}
	
		for($i=0;$i<count($avec_autre);$i++) {
			$sql_ele.=" AND liste_opt LIKE '%|$avec_autre[$i]|%'";
	
			if($chaine_avec_opt!="") {$chaine_avec_opt.=", ";}
			$chaine_avec_opt.="<span style='color:green;'>".$avec_autre[$i]."</span>";
	
			//$chaine_lien_modif_requete.="&amp;avec_autre[$i]=".$avec_autre[$i];
		}
	
		$chaine_sans_opt="";
		for($i=0;$i<count($sans_lv1);$i++) {
			$sql_ele.=" AND liste_opt NOT LIKE '%|$sans_lv1[$i]|%'";
	
			if($chaine_sans_opt!="") {$chaine_sans_opt.=", ";}
			$chaine_sans_opt.="<span style='color:red;'>".$sans_lv1[$i]."</span>";
	
			//$chaine_lien_modif_requete.="&amp;sans_lv1[$i]=".$sans_lv1[$i];
		}
	
		for($i=0;$i<count($sans_lv2);$i++) {
			$sql_ele.=" AND liste_opt NOT LIKE '%|$sans_lv2[$i]|%'";
	
			if($chaine_sans_opt!="") {$chaine_sans_opt.=", ";}
			$chaine_sans_opt.="<span style='color:red;'>".$sans_lv2[$i]."</span>";
	
			//$chaine_lien_modif_requete.="&amp;sans_lv2[$i]=".$sans_lv2[$i];
		}
	
		for($i=0;$i<count($sans_lv3);$i++) {
			$sql_ele.=" AND liste_opt NOT LIKE '%|$sans_lv3[$i]|%'";
	
			if($chaine_sans_opt!="") {$chaine_sans_opt.=", ";}
			$chaine_sans_opt.="<span style='color:red;'>".$sans_lv3[$i]."</span>";
	
			//$chaine_lien_modif_requete.="&amp;sans_lv3[$i]=".$sans_lv3[$i];
		}
	
		for($i=0;$i<count($sans_autre);$i++) {
			$sql_ele.=" AND liste_opt NOT LIKE '%|$sans_autre[$i]|%'";
	
			if($chaine_sans_opt!="") {$chaine_sans_opt.=", ";}
			$chaine_sans_opt.="<span style='color:red;'>".$sans_autre[$i]."</span>";
	
			//$chaine_lien_modif_requete.="&amp;sans_autre[$i]=".$sans_autre[$i];
		}
	
	
		$chaine_avec_profil="";
		if(count($avec_profil)>0) {
			$sql_ele_profil="";
			for($i=0;$i<count($avec_profil);$i++) {
				if($i>0) {$sql_ele_profil.=" OR ";}
				$sql_ele_profil.="profil='$avec_profil[$i]'";
	
				if($chaine_avec_profil!="") {$chaine_avec_profil.=", ";}
				$chaine_avec_profil.="<span style='color:red;'>".$avec_profil[$i]."</span>";
	
				//$chaine_lien_modif_requete.="&amp;avec_profil[$i]=".$avec_profil[$i];
			}
			$sql_ele.=" AND ($sql_ele_profil)";
		}
	
		$chaine_sans_profil="";
		if(count($sans_profil)>0) {
			$sql_ele_profil="";
			for($i=0;$i<count($sans_profil);$i++) {
				if($i>0) {$sql_ele_profil.=" AND ";}
				$sql_ele_profil.="profil!='$sans_profil[$i]'";
	
				if($chaine_sans_profil!="") {$chaine_sans_profil.=", ";}
				$chaine_sans_profil.="<span style='color:red;'>".$sans_profil[$i]."</span>";
	
				//$chaine_lien_modif_requete.="&amp;sans_profil[$i]=".$sans_profil[$i];
			}
			$sql_ele.=" AND ($sql_ele_profil)";
		}
	
	
		$retour="";
		$tab_retour=array();

		//$tab_ele=array();
		$sql_ele.=";";
		//echo "$sql_ele<br />\n";
		$cpt=0;
		$res_ele=mysql_query($sql_ele);
		while ($lig_ele=mysql_fetch_object($res_ele)) {
			//$tab_ele[]=$lig_ele->login;
	
			//$retour.=get_nom_prenom_eleve($lig_ele->login,'avec_classe')."<br />";
			$tab_retour[]=get_nom_prenom_eleve($lig_ele->login,'avec_classe')."<br />";

			$cpt++;
		}

		sort($tab_retour);
		for($i=0;$i<count($tab_retour);$i++) {$retour.=$tab_retour[$i];}

		return $retour;
	}




	//=========================================
	// Pouvoir utiliser des requêtes déjà définies dans l'affichage des listes:
	$sql="SELECT DISTINCT id_aff FROM gc_affichages WHERE projet='$projet' ORDER BY id_aff;";
	$res_req_aff=mysql_query($sql);
	if(mysql_num_rows($res_req_aff)>0) {
		echo "<script type='text/javascript'>
function change_display(id) {
	if(document.getElementById(id)) {
		if(document.getElementById(id).style.display=='none') {document.getElementById(id).style.display='block'} else {document.getElementById(id).style.display='none'}
	}
}
</script>\n";

		echo "<div style='float:right;'>\n";
		echo "<p class='bold'>Listes des affichages définis</p>\n";
		while($lig_req_aff=mysql_fetch_object($res_req_aff)) {
			echo "<p><a href='#' onclick=\"change_display('id_aff_$lig_req_aff->id_aff')\">Affichage n°$lig_req_aff->id_aff</a>";
			echo "</p>\n";

			echo "<div id='id_aff_$lig_req_aff->id_aff' style='display:none;'>\n";
			//++++++++++++++++++++++++++++++++++++++++++++++
			//$sql="SELECT DISTINCT id_req FROM gc_affichages WHERE projet='$projet'AND id_aff='$lig_req_aff->id_aff' ORDER BY id_req;";
			$sql="SELECT DISTINCT id_req, nom_requete FROM gc_affichages WHERE projet='$projet'AND id_aff='$lig_req_aff->id_aff' ORDER BY id_req;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$txt_requete="<ul>\n";
				while($lig=mysql_fetch_object($res)) {
					$txt_requete.="<li>\n";
					$txt_requete.="<b><a href='".$_SERVER['PHP_SELF']."?choix_affich=y&amp;requete_definie=y&amp;id_aff=$lig_req_aff->id_aff&amp;id_req=$lig->id_req&amp;projet=$projet'>";
					if($lig->nom_requete!="") {
						$txt_requete.="$lig->nom_requete (<em>Req.n°$lig->id_req</em>)";
					}
					else {
						$txt_requete.="Requête n°$lig->id_req";
					}
					$txt_requete.="</a></b>";
	
					//===========================================
					$id_req=$lig->id_req;
	
					$sql_ele="SELECT DISTINCT login FROM gc_eleves_options WHERE projet='$projet' AND classe_future!='Dep' AND classe_future!='Red'";
					$sql_ele_id_classe_act="";
					$sql_ele_classe_fut="";
	
					$sql="SELECT * FROM gc_affichages WHERE projet='$projet' AND id_aff='$lig_req_aff->id_aff' AND id_req='$id_req' ORDER BY type;";
					$res_tmp=mysql_query($sql);
					while($lig_tmp=mysql_fetch_object($res_tmp)) {
						switch($lig_tmp->type) {
							case 'id_clas_act':
								if($sql_ele_id_classe_act!='') {$sql_ele_id_classe_act.=" OR ";}
								$sql_ele_id_classe_act.="id_classe_actuelle='$lig_tmp->valeur'";
								break;
			
							case 'clas_fut':
								if($sql_ele_classe_fut!='') {$sql_ele_classe_fut.=" OR ";}
								$sql_ele_classe_fut.="classe_future='$lig_tmp->valeur'";
								break;
			
							case 'avec_lv1':
								$sql_ele.=" AND liste_opt LIKE '%|$lig_tmp->valeur|%'";
								break;
							case 'avec_lv2':
								$sql_ele.=" AND liste_opt LIKE '%|$lig_tmp->valeur|%'";
								break;
							case 'avec_lv3':
								$sql_ele.=" AND liste_opt LIKE '%|$lig_tmp->valeur|%'";
								break;
			
							case 'avec_autre':
								$sql_ele.=" AND liste_opt LIKE '%|$lig_tmp->valeur|%'";
								break;
			
							case 'sans_lv1':
								$sql_ele.=" AND liste_opt NOT LIKE '%|$lig_tmp->valeur|%'";
								break;
							case 'sans_lv2':
								$sql_ele.=" AND liste_opt NOT LIKE '%|$lig_tmp->valeur|%'";
								break;
							case 'sans_lv3':
								$sql_ele.=" AND liste_opt NOT LIKE '%|$lig_tmp->valeur|%'";
								break;
							case 'sans_autre':
								$sql_ele.=" AND liste_opt NOT LIKE '%|$lig_tmp->valeur|%'";
								break;
						}
					}
			
					//$tab_ele=array();
			
					if($sql_ele_id_classe_act!='') {$sql_ele.=" AND ($sql_ele_id_classe_act)";}
					if($sql_ele_classe_fut!='') {$sql_ele.=" AND ($sql_ele_classe_fut)";}
			
					$sql_ele.=";";
					//echo "$sql_ele<br />\n";
					$res_ele=mysql_query($sql_ele);
	
					$txt_requete.=" <span style='font-size:small;font-style:italic;'>(".mysql_num_rows($res_ele).")</span>";
					//tableau_eleves_req($id_aff, $id_req)
					//$txt_requete.=" - <a href='#' onclick=\"afficher_div('div_id_aff_".$lig_req_aff->id_aff."_id_req_".$lig->id_req."','y',100,100); return false;\"><img src='../images/vert.png' width='16' height='16' title='Afficher les élèves de la requête n°$id_req en infobulle' /></a>";
					//$txt_requete.=" - <a href='#' onmouseover=\"afficher_div('div_id_aff_".$lig_req_aff->id_aff."_id_req_".$lig->id_req."','y',100,100);\" onmouseout=\"cacher_div('div_id_aff_".$lig_req_aff->id_aff."_id_req_".$lig->id_req."')\"><img src='../images/vert.png' width='16' height='16' title='Afficher les élèves de la requête n°$id_req en infobulle' /></a>";
					$txt_requete.=" - <a href='#' onclick=\"afficher_div('div_id_aff_".$lig_req_aff->id_aff."_id_req_".$lig->id_req."','y',100,100);\"><img src='../images/vert.png' width='16' height='16' title='Afficher les élèves de la requête n°$id_req en infobulle' /></a>";
					$txt_requete.="<br />";

					$titre_i="Affichage n°$lig_req_aff->id_aff - Requête n°$lig->id_req";
					$texte_i=tableau_eleves_req($lig_req_aff->id_aff, $lig->id_req);
					//$tabdiv_infobulle[]=creer_div_infobulle("div_id_aff_".$lig_req_aff->id_aff."_id_req_".$lig->id_req,$titre_i,"",$texte_i,"",18,0,'y','y','n','n');
					$tabdiv_infobulle[]=creer_div_infobulle("div_id_aff_".$lig_req_aff->id_aff."_id_req_".$lig->id_req,$titre_i,"",$texte_i,"",18,0,'y','y','n','n');

					//===========================================
	
	
					$sql="SELECT * FROM gc_affichages WHERE projet='$projet'AND id_aff='$lig_req_aff->id_aff' AND type='id_clas_act' AND id_req='$lig->id_req';";
					$res2=mysql_query($sql);
					if(mysql_num_rows($res2)>0) {
						$txt_requete.="Classe actuelle (";
						$cpt=0;
						while($lig2=mysql_fetch_object($res2)) {
							if($cpt>0) {$txt_requete.=", ";}
							if(($lig2->valeur=='Red')||($lig2->valeur=='Arriv')) {
								$txt_requete.=$lig2->valeur;
							}
							else {
								$txt_requete.=get_class_from_id($lig2->valeur);
							}
							$cpt++;
						}
						$txt_requete.=")<br />";
					}
	
					$sql="SELECT * FROM gc_affichages WHERE projet='$projet'AND id_aff='$lig_req_aff->id_aff' AND type='clas_fut' AND id_req='$lig->id_req';";
					$res2=mysql_query($sql);
					if(mysql_num_rows($res2)>0) {
						$txt_requete.="Classe future (";
						$cpt=0;
						while($lig2=mysql_fetch_object($res2)) {
							if($cpt>0) {$txt_requete.=", ";}
							$txt_requete.=$lig2->valeur;
							$cpt++;
						}
						$txt_requete.=")<br />";
					}
	
					$sql="SELECT * FROM gc_affichages WHERE projet='$projet'AND id_aff='$lig_req_aff->id_aff' AND type LIKE 'avec_%' AND id_req='$lig->id_req';";
					$res2=mysql_query($sql);
					if(mysql_num_rows($res2)>0) {
						$txt_requete.="Avec les options (<span style='color:green;'>";
						$cpt=0;
						while($lig2=mysql_fetch_object($res2)) {
							if($cpt>0) {$txt_requete.=", ";}
							$txt_requete.=$lig2->valeur;
							$cpt++;
						}
						$txt_requete.="</span>)<br />";
					}
	
					$sql="SELECT * FROM gc_affichages WHERE projet='$projet'AND id_aff='$lig_req_aff->id_aff' AND type LIKE 'sans_%' AND id_req='$lig->id_req';";
					$res2=mysql_query($sql);
					if(mysql_num_rows($res2)>0) {
						$txt_requete.="Sans les options (<span style='color:red;'>";
						$cpt=0;
						while($lig2=mysql_fetch_object($res2)) {
							if($cpt>0) {$txt_requete.=", ";}
							$txt_requete.=$lig2->valeur;
							$cpt++;
						}
						$txt_requete.="</span>)<br />";
					}
	
					$txt_requete.="</li>\n";
				}
				$txt_requete.="</ul>\n";
				echo $txt_requete;

			}
			//++++++++++++++++++++++++++++++++++++++++++++++
			echo "</div>\n";




		}
		echo "</div>\n";
	}
	//=========================================

	$sql="SELECT DISTINCT opt FROM gc_options WHERE projet='$projet' AND type='lv1' ORDER BY opt;";
	$res_lv1=mysql_query($sql);
	$nb_lv1=mysql_num_rows($res_lv1);
	
	$sql="SELECT DISTINCT opt FROM gc_options WHERE projet='$projet' AND type='lv2' ORDER BY opt;";
	$res_lv2=mysql_query($sql);
	$nb_lv2=mysql_num_rows($res_lv2);
	
	$sql="SELECT DISTINCT opt FROM gc_options WHERE projet='$projet' AND type='lv3' ORDER BY opt;";
	$res_lv3=mysql_query($sql);
	$nb_lv3=mysql_num_rows($res_lv3);
	
	$sql="SELECT DISTINCT opt FROM gc_options WHERE projet='$projet' AND type='autre' ORDER BY opt;";
	$res_autre=mysql_query($sql);
	$nb_autre=mysql_num_rows($res_autre);
	
	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";

	echo "<table class='boireaus' border='1' summary='Choix des paramètres'>\n";
	echo "<tr>\n";
	echo "<th>Classe actuelle</th>\n";
	echo "<th>Classe future</th>\n";
	if($nb_lv1>0) {echo "<th>LV1</th>\n";}
	if($nb_lv2>0) {echo "<th>LV2</th>\n";}
	if($nb_lv3>0) {echo "<th>LV3</th>\n";}
	if($nb_autre>0) {echo "<th>Autre option</th>\n";}
	echo "<th>Profil</th>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td style='vertical-align:top; padding:2px;' class='lig-1'>\n";
	$cpt=0;
	while($lig=mysql_fetch_object($res_clas_act)) {
		echo "<input type='checkbox' name='id_clas_act[]' id='id_clas_act_$cpt' value='$lig->id_classe' ";
		if(in_array($lig->id_classe,$id_clas_act)) {echo "checked ";}
		echo "/><label for='id_clas_act_$cpt'>$lig->classe</label><br />\n";
		$cpt++;
	}
	echo "<input type='checkbox' name='id_clas_act[]' id='id_clas_act_$cpt' value='Red' ";
	if(in_array('Red',$id_clas_act)) {echo "checked ";}
	echo "/><label for='id_clas_act_$cpt'>Redoublants</label><br />\n";
	$cpt++;
	echo "<input type='checkbox' name='id_clas_act[]' id='id_clas_act_$cpt' value='Arriv' ";
	if(in_array('Arriv',$id_clas_act)) {echo "checked ";}
	echo "/><label for='id_clas_act_$cpt'>Arrivants</label><br />\n";
	$cpt++;
	echo "</td>\n";

	$classe_fut=array();
	echo "<td style='vertical-align:top; padding:2px;' class='lig-1'>\n";
	$cpt=0;
	while($lig=mysql_fetch_object($res_clas_fut)) {
		//$sql="SELECT 1=1 FROM gc_eleve_fut_classe WHERE projet='$projet' AND classe='$lig->classe';";
		$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND classe_future='$lig->classe';";
		$res_test=mysql_query($sql);
		if(mysql_num_rows($res_test)>0) {
			echo "<input type='checkbox' name='clas_fut[]' id='clas_fut_$cpt' value='$lig->classe' ";
			if(in_array($lig->classe,$clas_fut)) {echo "checked ";}
			echo "/><label for='clas_fut_$cpt'>$lig->classe <span style='font-size:x-small'>(<em>".mysql_num_rows($res_test)."</em>)</span></label><br />\n";
		}
		else {
			echo "_ $lig->classe<br />\n";
		}

		$classe_fut[]=$lig->classe;

		$cpt++;
	}
	$classe_fut[]="Red";
	$classe_fut[]="Dep";
	$classe_fut[]=""; // Vide pour les Non Affectés


	echo "<input type='checkbox' name='clas_fut[]' id='clas_fut_$cpt' value='' /><label for='clas_fut_$cpt'>Non encore affecté</label><br />\n";
	$cpt++;
	echo "</td>\n";

	if($nb_lv1>0) {
		echo "<td style='vertical-align:top; padding:2px;' class='lig1'>\n";
			echo "<table class='boireaus' border='1' summary='LV1'>\n";
			echo "<tr>\n";
			echo "<th>Avec</th>\n";
			echo "<th>Sans</th>\n";
			echo "<th>LV</th>\n";
			echo "</tr>\n";
			while($lig=mysql_fetch_object($res_lv1)) {
				echo "<tr>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='avec_lv1[]' value='$lig->opt' ";
				if(in_array($lig->opt,$avec_lv1)) {echo "checked ";}
				echo "/>\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='sans_lv1[]' value='$lig->opt' ";
				if(in_array($lig->opt,$sans_lv1)) {echo "checked ";}
				echo "/>\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "$lig->opt\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		echo "</td>\n";
	}

	if($nb_lv2>0) {
		echo "<td style='vertical-align:top; padding:2px;' class='lig1'>\n";
			echo "<table class='boireaus' border='2' summary='LV2'>\n";
			echo "<tr>\n";
			echo "<th>Avec</th>\n";
			echo "<th>Sans</th>\n";
			echo "<th>LV</th>\n";
			echo "</tr>\n";
			while($lig=mysql_fetch_object($res_lv2)) {
				echo "<tr>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='avec_lv2[]' value='$lig->opt' ";
				if(in_array($lig->opt,$avec_lv2)) {echo "checked ";}
				echo "/>\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='sans_lv2[]' value='$lig->opt' ";
				if(in_array($lig->opt,$sans_lv2)) {echo "checked ";}
				echo "/>\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "$lig->opt\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		echo "</td>\n";
	}

	if($nb_lv3>0) {
		echo "<td style='vertical-align:top; padding:2px;' class='lig1'>\n";
			echo "<table class='boireaus' border='3' summary='LV3'>\n";
			echo "<tr>\n";
			echo "<th>Avec</th>\n";
			echo "<th>Sans</th>\n";
			echo "<th>LV</th>\n";
			echo "</tr>\n";
			while($lig=mysql_fetch_object($res_lv3)) {
				echo "<tr>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='avec_lv3[]' value='$lig->opt' ";
				if(in_array($lig->opt,$avec_lv3)) {echo "checked ";}
				echo "/>\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='sans_lv3[]' value='$lig->opt' ";
				if(in_array($lig->opt,$sans_lv3)) {echo "checked ";}
				echo "/>\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "$lig->opt\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		echo "</td>\n";
	}

	if($nb_autre>0) {
		echo "<td style='vertical-align:top; padding:2px;' class='lig1'>\n";
			echo "<table class='boireaus' border='1' summary='Option'>\n";
			echo "<tr>\n";
			echo "<th>Avec</th>\n";
			echo "<th>Sans</th>\n";
			echo "<th>Option</th>\n";
			echo "</tr>\n";
			while($lig=mysql_fetch_object($res_autre)) {
				echo "<tr>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='avec_autre[]' value='$lig->opt' ";
				if(in_array($lig->opt,$avec_autre)) {echo "checked ";}
				echo "/>\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "<input type='checkbox' name='sans_autre[]' value='$lig->opt' ";
				if(in_array($lig->opt,$sans_autre)) {echo "checked ";}
				echo "/>\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "$lig->opt\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		echo "</td>\n";
	}


	//=============================
	include("lib_gc.php");
	// On y initialise le tableau des profils
	//=============================

	echo "<td style='vertical-align:top; padding:2px;' class='lig-1'>\n";
		echo "<table class='boireaus' border='1' summary='Profil'>\n";
		echo "<tr>\n";
		echo "<th>Avec</th>\n";
		echo "<th>Sans</th>\n";
		echo "<th>Profil</th>\n";
		echo "</tr>\n";

		for($loop=0;$loop<count($tab_profil);$loop++) {
			echo "<tr>\n";
			echo "<td>\n";
			echo "<input type='checkbox' name='avec_profil[]' value='$tab_profil[$loop]' ";
			if(in_array($tab_profil[$loop],$avec_profil)) {echo "checked ";}
			echo "/>\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<input type='checkbox' name='sans_profil[]' value='$tab_profil[$loop]' ";
			if(in_array($tab_profil[$loop],$sans_profil)) {echo "checked ";}
			echo "/>\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "$tab_profil[$loop]\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
	echo "</td>\n";
	// Pouvoir faire une recherche par niveau aussi?


	echo "</tr>\n";
	echo "</table>\n";

	echo "<input type='hidden' name='projet' value='$projet' />\n";
	//echo "<input type='hidden' name='is_posted' value='y' />\n";
	echo "<p align='center'><input type='submit' name='choix_affich' value='Valider' /></p>\n";

	echo "</form>\n";

	echo "<p><i>NOTES&nbsp;:</i></p>\n";
	echo "<ul>\n";
	echo "<li>En sélectionnant toutes les classes futures et les Non affectés, on obtient une liste avec les effectifs utiles dans les options.<br />
	En haut de tableau, on a les effectifs totaux et en bas de tableau, on a les effectifs de la sélection.</li>";
	echo "<li>Les colonnes Classe actuelle et Classe future sont traitées suivant le mode OU<br />
	Si vous cochez deux classes, les élèves pris en compte seront '<i>membre de Classe 1 OU membre de Classe 2</i>'</li>\n";
	echo "<li>Les colonnes d'options sont traitées suivant le mode ET.<br />
	Ce sera par exemple '<i>Avec AGL1 ET Avec ESP2 ET Avec LATIN ET Sans DECP3</i>'</li>\n";
	echo "<li>Les lignes de la colonne avec profil sont traitées suivant le mode OU.<br />
	Les lignes de la colonne sans profil sont traitées suivant le mode ET.<br />
	Ce sera par exemple '<i>Avec profil RAS OU profil B</i>'
	</li>\n";
	echo "</ul>\n";
}
else {

	// Pour utiliser des listes d'affichage
	$requete_definie=isset($_POST['requete_definie']) ? $_POST['requete_definie'] : (isset($_GET['requete_definie']) ? $_GET['requete_definie'] : 'n');
	$id_aff=isset($_POST['id_aff']) ? $_POST['id_aff'] : (isset($_GET['id_aff']) ? $_GET['id_aff'] : NULL);
	$id_req=isset($_POST['id_req']) ? $_POST['id_req'] : (isset($_GET['id_req']) ? $_GET['id_req'] : NULL);

	echo "<div class='noprint'>\n"; // Debut de l'entête à ne pas imprimer

	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name=\"form_autre_requete\">\n";

	echo "<p class='bold'><a href='index.php?projet=$projet'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">Retour</a>";

	echo " | <a href='".$_SERVER['PHP_SELF']."?projet=$projet'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">Autre sélection</a>";

	$num_requete=0;
	$indice_requete=0;
	if(isset($id_aff)) {
		$sql="SELECT DISTINCT id_req, nom_requete FROM gc_affichages WHERE projet='$projet' AND id_aff='$id_aff' AND nom_requete!='' ORDER BY nom_requete;";
		$res_req_nommees=mysql_query($sql);
		if(mysql_num_rows($res_req_nommees)>0) {
			echo " | <select name='id_req' id='id_req_chg_requete'";
			//echo " onchange=\"document.forms['form_autre_requete'].submit();\"";
			echo " onchange=\"confirm_changement_requete(change, '$themessage');\"";
			echo ">\n";
			while($lig_req_nommee=mysql_fetch_object($res_req_nommees)) {
				echo "<option value='".$lig_req_nommee->id_req."'";
				if((isset($id_req))&&($lig_req_nommee->id_req==$id_req)) {
					echo " selected='selected'";
					$indice_requete=$num_requete;
				}
				echo ">".$lig_req_nommee->nom_requete."</option>\n";
				$num_requete++;
			}
			echo "</select>\n";

			echo "<input type='hidden' name='projet' value='$projet' />\n";
			echo "<input type='hidden' name='id_aff' value='$id_aff' />\n";
			echo "<input type='hidden' name='requete_definie' value='y' />\n";
			echo "<input type='hidden' name='choix_affich' value='y' />\n";
			echo "<input type='submit' name='changer_affect_eleves_classes' id='changer_affect_eleves_classes' value='Valider' />\n";
		}
	}

	$sql="SELECT DISTINCT login FROM gc_eleves_options WHERE projet='$projet' AND classe_future!='Dep' AND classe_future!='Red' AND (classe_future='');";
	//echo "$sql<br />";
	$res_na=mysql_query($sql);
	if(mysql_num_rows($res_na)>0) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?projet=$projet&amp;choix_affich=Valider&amp;clas_fut[0]='>Non affectés (<em>".mysql_num_rows($res_na)."</em>)</a>";
	}

	echo "</p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	if(document.getElementById('changer_affect_eleves_classes')) {
		document.getElementById('changer_affect_eleves_classes').style.display='none';
	}

	// Initialisation
	change='no';

	function confirm_changement_requete(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.forms['form_autre_requete'].submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.forms['form_autre_requete'].submit();
			}
			else{
				document.getElementById('id_req_chg_requete').selectedIndex=$indice_requete;
			}
		}
	}
</script>\n";

	echo "<h2>Projet $projet</h2>\n";

	if(($requete_definie=='y')&&(isset($id_aff))&&(isset($id_req))) {
		$sql="SELECT * FROM gc_affichages WHERE projet='$projet' AND id_aff='$id_aff' AND id_req='$id_req' ORDER BY type;";
		//echo "$sql<br />";
		$res_tmp=mysql_query($sql);
		while($lig_tmp=mysql_fetch_object($res_tmp)) {
			$nom_requete=$lig_tmp->nom_requete;
			switch($lig_tmp->type) {
				case 'id_clas_act':
					if(!in_array($lig_tmp->valeur,$id_clas_act)) {$id_clas_act[]=$lig_tmp->valeur;}
					break;
				case 'clas_fut':
					if(!in_array($lig_tmp->valeur,$clas_fut)) {$clas_fut[]=$lig_tmp->valeur;}
					break;

				case 'avec_lv1':
					if(!in_array($lig_tmp->valeur,$avec_lv1)) {$avec_lv1[]=$lig_tmp->valeur;}
					break;
				case 'avec_lv2':
					if(!in_array($lig_tmp->valeur,$avec_lv2)) {$avec_lv2[]=$lig_tmp->valeur;}
					break;
				case 'avec_lv3':
					if(!in_array($lig_tmp->valeur,$avec_lv3)) {$avec_lv3[]=$lig_tmp->valeur;}
					break;
				case 'avec_autre':
					if(!in_array($lig_tmp->valeur,$avec_autre)) {$avec_autre[]=$lig_tmp->valeur;}
					break;
				case 'avec_profil':
					if(!in_array($lig_tmp->valeur,$avec_profil)) {$avec_profil[]=$lig_tmp->valeur;}
					break;

				case 'sans_lv1':
					if(!in_array($lig_tmp->valeur,$sans_lv1)) {$sans_lv1[]=$lig_tmp->valeur;}
					break;
				case 'sans_lv2':
					if(!in_array($lig_tmp->valeur,$sans_lv2)) {$sans_lv2[]=$lig_tmp->valeur;}
					break;
				case 'sans_lv3':
					if(!in_array($lig_tmp->valeur,$sans_lv3)) {$sans_lv3[]=$lig_tmp->valeur;}
					break;
				case 'sans_autre':
					if(!in_array($lig_tmp->valeur,$sans_autre)) {$sans_autre[]=$lig_tmp->valeur;}
					break;
				case 'sans_profil':
					if(!in_array($lig_tmp->valeur,$sans_profil)) {$sans_profil[]=$lig_tmp->valeur;}
					break;
			}
		}
	}

	//=========================
	// Début de la requête à forger pour ne retenir que les élèves souhaités
	$sql_ele="SELECT DISTINCT login FROM gc_eleves_options WHERE projet='$projet' AND classe_future!='Dep' AND classe_future!='Red'";

	$sql_ele_id_classe_act="";
	$sql_ele_classe_fut="";
	//=========================

	$chaine_lien_modif_requete="projet=$projet";

	$chaine_classes_actuelles="";
	if(count($id_clas_act)>0) {
		for($i=0;$i<count($id_clas_act);$i++) {
			if($i>0) {$sql_ele_id_classe_act.=" OR ";}
			$sql_ele_id_classe_act.="id_classe_actuelle='$id_clas_act[$i]'";

			if($i>0) {$chaine_classes_actuelles.=", ";}
			$chaine_classes_actuelles.=get_class_from_id($id_clas_act[$i]);

			$chaine_lien_modif_requete.="&amp;id_clas_act[$i]=".$id_clas_act[$i];
		}
		$sql_ele.=" AND ($sql_ele_id_classe_act)";
	}

	$chaine_classes_futures="";
	if(count($clas_fut)>0) {
		for($i=0;$i<count($clas_fut);$i++) {
			if($i>0) {$sql_ele_classe_fut.=" OR ";}
			$sql_ele_classe_fut.="classe_future='$clas_fut[$i]'";

			if($i>0) {$chaine_classes_futures.=", ";}
			if($clas_fut[$i]=='') {$chaine_classes_futures.='Non.aff';} else {$chaine_classes_futures.=$clas_fut[$i];}

			$chaine_lien_modif_requete.="&amp;clas_fut[$i]=".$clas_fut[$i];
		}
		$sql_ele.=" AND ($sql_ele_classe_fut)";
	}

	$chaine_avec_opt="";
	for($i=0;$i<count($avec_lv1);$i++) {
		$sql_ele.=" AND liste_opt LIKE '%|$avec_lv1[$i]|%'";

		if($chaine_avec_opt!="") {$chaine_avec_opt.=", ";}
		$chaine_avec_opt.="<span style='color:green;'>".$avec_lv1[$i]."</span>";

		$chaine_lien_modif_requete.="&amp;avec_lv1[$i]=".$avec_lv1[$i];
	}

	for($i=0;$i<count($avec_lv2);$i++) {
		$sql_ele.=" AND liste_opt LIKE '%|$avec_lv2[$i]|%'";

		if($chaine_avec_opt!="") {$chaine_avec_opt.=", ";}
		$chaine_avec_opt.="<span style='color:green;'>".$avec_lv2[$i]."</span>";

		$chaine_lien_modif_requete.="&amp;avec_lv2[$i]=".$avec_lv2[$i];
	}

	for($i=0;$i<count($avec_lv3);$i++) {
		$sql_ele.=" AND liste_opt LIKE '%|$avec_lv3[$i]|%'";

		if($chaine_avec_opt!="") {$chaine_avec_opt.=", ";}
		$chaine_avec_opt.="<span style='color:green;'>".$avec_lv3[$i]."</span>";

		$chaine_lien_modif_requete.="&amp;avec_lv3[$i]=".$avec_lv3[$i];
	}

	for($i=0;$i<count($avec_autre);$i++) {
		$sql_ele.=" AND liste_opt LIKE '%|$avec_autre[$i]|%'";

		if($chaine_avec_opt!="") {$chaine_avec_opt.=", ";}
		$chaine_avec_opt.="<span style='color:green;'>".$avec_autre[$i]."</span>";

		$chaine_lien_modif_requete.="&amp;avec_autre[$i]=".$avec_autre[$i];
	}

	$chaine_sans_opt="";
	for($i=0;$i<count($sans_lv1);$i++) {
		$sql_ele.=" AND liste_opt NOT LIKE '%|$sans_lv1[$i]|%'";

		if($chaine_sans_opt!="") {$chaine_sans_opt.=", ";}
		$chaine_sans_opt.="<span style='color:red;'>".$sans_lv1[$i]."</span>";

		$chaine_lien_modif_requete.="&amp;sans_lv1[$i]=".$sans_lv1[$i];
	}

	for($i=0;$i<count($sans_lv2);$i++) {
		$sql_ele.=" AND liste_opt NOT LIKE '%|$sans_lv2[$i]|%'";

		if($chaine_sans_opt!="") {$chaine_sans_opt.=", ";}
		$chaine_sans_opt.="<span style='color:red;'>".$sans_lv2[$i]."</span>";

		$chaine_lien_modif_requete.="&amp;sans_lv2[$i]=".$sans_lv2[$i];
	}

	for($i=0;$i<count($sans_lv3);$i++) {
		$sql_ele.=" AND liste_opt NOT LIKE '%|$sans_lv3[$i]|%'";

		if($chaine_sans_opt!="") {$chaine_sans_opt.=", ";}
		$chaine_sans_opt.="<span style='color:red;'>".$sans_lv3[$i]."</span>";

		$chaine_lien_modif_requete.="&amp;sans_lv3[$i]=".$sans_lv3[$i];
	}

	for($i=0;$i<count($sans_autre);$i++) {
		$sql_ele.=" AND liste_opt NOT LIKE '%|$sans_autre[$i]|%'";

		if($chaine_sans_opt!="") {$chaine_sans_opt.=", ";}
		$chaine_sans_opt.="<span style='color:red;'>".$sans_autre[$i]."</span>";

		$chaine_lien_modif_requete.="&amp;sans_autre[$i]=".$sans_autre[$i];
	}


	$chaine_avec_profil="";
	if(count($avec_profil)>0) {
		$sql_ele_profil="";
		for($i=0;$i<count($avec_profil);$i++) {
			if($i>0) {$sql_ele_profil.=" OR ";}
			$sql_ele_profil.="profil='$avec_profil[$i]'";

			if($chaine_avec_profil!="") {$chaine_avec_profil.=", ";}
			$chaine_avec_profil.="<span style='color:red;'>".$avec_profil[$i]."</span>";

			$chaine_lien_modif_requete.="&amp;avec_profil[$i]=".$avec_profil[$i];
		}
		$sql_ele.=" AND ($sql_ele_profil)";
	}

	$chaine_sans_profil="";
	if(count($sans_profil)>0) {
		$sql_ele_profil="";
		for($i=0;$i<count($sans_profil);$i++) {
			if($i>0) {$sql_ele_profil.=" AND ";}
			$sql_ele_profil.="profil!='$sans_profil[$i]'";

			if($chaine_sans_profil!="") {$chaine_sans_profil.=", ";}
			$chaine_sans_profil.="<span style='color:red;'>".$sans_profil[$i]."</span>";

			$chaine_lien_modif_requete.="&amp;sans_profil[$i]=".$sans_profil[$i];
		}
		$sql_ele.=" AND ($sql_ele_profil)";
	}



	$tab_ele=array();
	$sql_ele.=";";
	//echo "$sql_ele<br />\n";
	$res_ele=mysql_query($sql_ele);
	while ($lig_ele=mysql_fetch_object($res_ele)) {
		$tab_ele[]=$lig_ele->login;
	}

	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form_affect_eleves_classes'>\n";

	if(!isset($nom_requete)) {$nom_requete="";}
	echo "<p>Nom de la requête&nbsp;: <input type='text' name='nom_requete' value=\"$nom_requete\" ></p>\n";
	echo "<input type='hidden' name='id_aff' value=\"$id_aff\" >\n";
	echo "<input type='hidden' name='id_req' value=\"$id_req\" >\n";

	// Rappel de la requête:

	echo "<p>";
	echo "<a href='".$_SERVER['PHP_SELF']."?$chaine_lien_modif_requete'>";
	if($chaine_classes_actuelles!="") {echo "Classes actuelles $chaine_classes_actuelles<br />\n";}
	if($chaine_classes_futures!="") {echo "Classes futures $chaine_classes_futures<br />\n";}
	if($chaine_avec_opt!="") {echo "Avec $chaine_avec_opt<br />\n";}
	if($chaine_sans_opt!="") {echo "Sans $chaine_sans_opt<br />\n";}
	if($chaine_avec_profil!="") {echo "Avec profil $chaine_avec_profil<br />\n";}
	if($chaine_sans_profil!="") {echo "Sans profil $chaine_sans_profil<br />\n";}
	echo "</a>\n";
	echo "&nbsp;</p>\n";



/*
$_POST['id_clas_act']=	Array (*)
$_POST['id_clas_act'][0]=	23
$_POST['id_clas_act'][1]=	24
$_POST['avec_lv1']=	Array (*)
$_POST['avec_lv1'][0]=	AGL1
$_POST['avec_lv2']=	Array (*)
$_POST['avec_lv2'][0]=	ESP2
$_POST['avec_autre']=	Array (*)
$_POST['avec_autre'][0]=	LATIN
$_POST['projet']=	4eme_vers_3eme
*/


	$classe_fut=array();
	$sql="SELECT DISTINCT classe FROM gc_divisions WHERE projet='$projet' AND statut='future' ORDER BY classe;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>Aucune classe future n'est encore définie pour ce projet.</p>\n";
		// Est-ce que cela doit vraiment bloquer la saisie des options?
		require("../lib/footer.inc.php");
		die();
	}
	else {
		$tab_opt_exclue=array();
		while($lig=mysql_fetch_object($res)) {
			$classe_fut[]=$lig->classe;

			$tab_opt_exclue["$lig->classe"]=array();
			//=========================
			// Options exlues pour la classe
			$sql="SELECT opt_exclue FROM gc_options_classes WHERE projet='$projet' AND classe_future='$lig->classe';";
			$res_opt_exclues=mysql_query($sql);
			while($lig_opt_exclue=mysql_fetch_object($res_opt_exclues)) {
				$tab_opt_exclue["$lig->classe"][]=mb_strtoupper($lig_opt_exclue->opt_exclue);
			}
			//=========================

		}
		$classe_fut[]="Red";
		$classe_fut[]="Dep";
		$classe_fut[]=""; // Vide pour les Non Affectés
	}
	
	$id_classe_actuelle=array();
	$classe_actuelle=array();
	$sql="SELECT DISTINCT id_classe,classe FROM gc_divisions WHERE projet='$projet' AND statut='actuelle' ORDER BY classe;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>Aucune classe actuelle n'est encore sélectionnée pour ce projet.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		while($lig=mysql_fetch_object($res)) {
			$id_classe_actuelle[]=$lig->id_classe;
			$classe_actuelle[]=$lig->classe;
		}

		// On ajoute redoublants et arrivants
		$id_classe_actuelle[]='Red';
		$classe_actuelle[]='Red';
	
		$id_classe_actuelle[]='Arriv';
		$classe_actuelle[]='Arriv';
	}
	
	$lv1=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv1' ORDER BY opt;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$lv1[]=$lig->opt;
		}
	}
	
	
	$lv2=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv2' ORDER BY opt;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$lv2[]=$lig->opt;
		}
	}
	
	$lv3=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='lv3' ORDER BY opt;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$lv3[]=$lig->opt;
		}
	}
	
	$autre_opt=array();
	$sql="SELECT * FROM gc_options WHERE projet='$projet' AND type='autre' ORDER BY opt;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$autre_opt[]=$lig->opt;
		}
	}
	
	//=============================
	include("lib_gc.php");
	// On y initialise les couleurs
	// Il faut que le tableaux $classe_fut soit initialisé.
	//=============================

	//=========================================
	necessaire_bull_simple();
	//=========================================

	//echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";

	for($i=0;$i<count($avec_lv1);$i++) {
		echo "<input type='hidden' name='avec_lv1[$i]' value='$avec_lv1[$i]' />\n";
	}
	for($i=0;$i<count($avec_lv2);$i++) {
		echo "<input type='hidden' name='avec_lv2[$i]' value='$avec_lv2[$i]' />\n";
	}
	for($i=0;$i<count($avec_lv3);$i++) {
		echo "<input type='hidden' name='avec_lv3[$i]' value='$avec_lv3[$i]' />\n";
	}
	for($i=0;$i<count($avec_autre);$i++) {
		echo "<input type='hidden' name='avec_autre[$i]' value='$avec_autre[$i]' />\n";
	}

	for($i=0;$i<count($sans_lv1);$i++) {
		echo "<input type='hidden' name='sans_lv1[$i]' value='$sans_lv1[$i]' />\n";
	}
	for($i=0;$i<count($sans_lv2);$i++) {
		echo "<input type='hidden' name='sans_lv2[$i]' value='$sans_lv2[$i]' />\n";
	}
	for($i=0;$i<count($sans_lv3);$i++) {
		echo "<input type='hidden' name='sans_lv3[$i]' value='$sans_lv3[$i]' />\n";
	}
	for($i=0;$i<count($sans_autre);$i++) {
		echo "<input type='hidden' name='sans_autre[$i]' value='$sans_autre[$i]' />\n";
	}

	for($i=0;$i<count($id_clas_act);$i++) {
		echo "<input type='hidden' name='id_clas_act[$i]' value='$id_clas_act[$i]' />\n";
	}

	for($i=0;$i<count($clas_fut);$i++) {
		echo "<input type='hidden' name='clas_fut[$i]' value='$clas_fut[$i]' />\n";
	}

	for($i=0;$i<count($avec_profil);$i++) {
		echo "<input type='hidden' name='avec_profil[$i]' value='$avec_profil[$i]' />\n";
	}
	for($i=0;$i<count($sans_profil);$i++) {
		echo "<input type='hidden' name='sans_profil[$i]' value='$sans_profil[$i]' />\n";
	}

	// Colorisation
	echo "<p>Colorisation&nbsp;: ";
	echo "<select name='colorisation' onchange='lance_colorisation()'>
	<option value='classe_fut' selected>Classe future</option>
	<option value='lv1'>LV1</option>
	<option value='lv2'>LV2</option>
	<option value='profil'>Profil</option>
	</select>\n";
	
	echo "</p>\n";



	$eff_fut_classe_hors_selection=array();
	$eff_fut_classe_hors_selection_F=array();
	$eff_fut_classe_hors_selection_M=array();

	echo "<p align='center'><input type='submit' name='valide_aff_classe_fut0' value='Valider' /></p>\n";

	echo "</div>\n"; // Fin de l'entête à ne pas imprimer

	echo "<table class='boireaus' border='1' summary='Tableau des options'>\n";

	//==========================================
	echo "<thead>\n";
	echo "<tr>\n";
	echo "<th rowspan='2'>Elève</th>\n";
	echo "<th rowspan='2'>Sexe</th>\n";
	echo "<th rowspan='2'>Classe<br />actuelle</th>\n";
	echo "<th rowspan='2'>Profil</th>\n";
	echo "<th rowspan='2'>Niveau</th>\n";
	echo "<th rowspan='2'>Absences Non.Just Retards</th>\n";

	//if(count($classe_fut)>0) {echo "<th colspan='".(count($classe_fut)+2)."'>Classes futures</th>\n";}
	if(count($classe_fut)>0) {echo "<th colspan='".count($classe_fut)."'>Classes futures</th>\n";}
	if(count($lv1)>0) {echo "<th colspan='".count($lv1)."'>LV1</th>\n";}
	if(count($lv2)>0) {echo "<th colspan='".count($lv2)."'>LV2</th>\n";}
	if(count($lv3)>0) {echo "<th colspan='".count($lv3)."'>LV3</th>\n";}
	if(count($autre_opt)>0) {echo "<th colspan='".count($autre_opt)."'>Autres options</th>\n";}
	echo "</tr>\n";
	//==========================================
	echo "<tr>\n";
	for($i=0;$i<count($classe_fut);$i++) {
		echo "<th>$classe_fut[$i]</th>\n";

		// Initialisation
		$eff_fut_classe_hors_selection[$i]=0;
		$eff_fut_classe_hors_selection_F[$i]=0;
		$eff_fut_classe_hors_selection_M[$i]=0;
	}
	for($i=0;$i<count($lv1);$i++) {
		echo "<th>$lv1[$i]</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		echo "<th>$lv2[$i]</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		echo "<th>$lv3[$i]</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		echo "<th>$autre_opt[$i]</th>\n";
	}
	echo "</tr>\n";
	//==========================================



	$eff_tot=0;
	$eff_tot_M=0;
	$eff_tot_F=0;

	$eff_tot_classe_M=0;
	$eff_tot_classe_F=0;
	$eff_tot_classe=0;

	$j=-1;
	//$id_classe_actuelle[$j]=-1;
	$i=-1;
	echo "<tr>\n";
	//echo "<th>Effectifs&nbsp;: <span id='eff_tot'>&nbsp;</span></th>\n";
	echo "<th>Eff.tot&nbsp;:</th>\n";
	echo "<th id='eff_tot'>...</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	for($i=0;$i<count($classe_fut);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleve_fut_classe WHERE projet='$projet' AND classe='$classe_fut[$i]';";
		$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND classe_future='$classe_fut[$i]';";
		$res=mysql_query($sql);
		$effectif_classe_fut[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_classe_fut_".$i."'>".$effectif_classe_fut[$i]."</th>\n";
	}
	for($i=0;$i<count($lv1);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND opt='$lv1[$i]';";
		$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND liste_opt LIKE '%|$lv1[$i]|%';";
		$res=mysql_query($sql);
		$effectif_lv1[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_lv1_".$i."'>".$effectif_lv1[$i]."</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND opt='$lv2[$i]';";
		$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND liste_opt LIKE '%|$lv2[$i]|%';";
		$res=mysql_query($sql);
		$effectif_lv2[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_lv2_".$i."'>".$effectif_lv2[$i]."</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND opt='$lv3[$i]';";
		$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND liste_opt LIKE '%|$lv3[$i]|%';";
		$res=mysql_query($sql);
		$effectif_lv3[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_lv3_".$i."'>".$effectif_lv3[$i]."</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND opt='$autre_opt[$i]';";
		$sql="SELECT 1=1 FROM gc_eleves_options WHERE projet='$projet' AND liste_opt LIKE '%|$autre_opt[$i]|%';";
		$res=mysql_query($sql);
		$effectif_autre_opt[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_autre_opt_".$i."'>".$effectif_autre_opt[$i]."</th>\n";
	}
	echo "</tr>\n";
	//==========================================
	echo "<tr>\n";
	//echo "<th>Effectifs&nbsp;: <span id='eff_tot_selection'>&nbsp;</span></th>\n";
	echo "<th>Eff.tot.sexe&nbsp;:</th>\n";
	echo "<th id='eff_tot_sexe'>...</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	for($i=0;$i<count($classe_fut);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleve_fut_classe g, eleves e WHERE g.projet='$projet' AND g.classe='$classe_fut[$i]' AND e.login=g.login AND e.sexe='M';";
		$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.classe_future='$classe_fut[$i]' AND e.login=g.login AND e.sexe='M';";
		$res=mysql_query($sql);
		$effectif_classe_fut_M[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_sexe_classe_fut_".$i."'>".$effectif_classe_fut_M[$i]."/".($effectif_classe_fut[$i]-$effectif_classe_fut_M[$i])."</th>\n";
	}
	for($i=0;$i<count($lv1);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.opt='$lv1[$i]' AND e.login=g.login AND e.sexe='M';";
		$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.liste_opt LIKE '%|$lv1[$i]|%' AND e.login=g.login AND e.sexe='M';";
		$res=mysql_query($sql);
		$effectif_lv1_M[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_sexe_lv1_".$i."'>".$effectif_lv1_M[$i]."/".($effectif_lv1[$i]-$effectif_lv1_M[$i])."</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.opt='$lv2[$i]' AND e.login=g.login AND e.sexe='M';";
		$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.liste_opt LIKE '%|$lv2[$i]|%' AND e.login=g.login AND e.sexe='M';";
		$res=mysql_query($sql);
		$effectif_lv2_M[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_sexe_lv2_".$i."'>".$effectif_lv2_M[$i]."/".($effectif_lv2[$i]-$effectif_lv2_M[$i])."</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.opt='$lv3[$i]' AND e.login=g.login AND e.sexe='M';";
		$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.liste_opt LIKE '%|$lv3[$i]|%' AND e.login=g.login AND e.sexe='M';";
		$res=mysql_query($sql);
		$effectif_lv3_M[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_sexe_lv3_".$i."'>".$effectif_lv3_M[$i]."/".($effectif_lv3[$i]-$effectif_lv3_M[$i])."</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		//$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.opt='$autre_opt[$i]' AND e.login=g.login AND e.sexe='M';";
		$sql="SELECT 1=1 FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.liste_opt LIKE '%|$autre_opt[$i]|%' AND e.login=g.login AND e.sexe='M';";
		$res=mysql_query($sql);
		$effectif_autre_opt_M[$i]=mysql_num_rows($res);
		echo "<th id='eff_col_sexe_autre_opt_".$i."'>".$effectif_autre_opt_M[$i]."/".($effectif_autre_opt[$i]-$effectif_autre_opt_M[$i])."</th>\n";
	}
	echo "</tr>\n";
	//==========================================
	echo "<tr>\n";
	echo "<th>Coches</th>\n";

	// Mettre là les effectifs de la sélection
	echo "<th id='eff_selection'>&nbsp;</th>\n";

	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	for($i=0;$i<count($classe_fut);$i++) {
		echo "<th>\n";
		echo "<a href=\"javascript:modif_colonne('classe_fut_$i',true);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
		$eff_selection_classe_fut[$i]=0;
		$eff_selection_classe_fut_M[$i]=0;
		$eff_selection_classe_fut_F[$i]=0;
		echo "</th>\n";
	}
	for($i=0;$i<count($lv1);$i++) {
		echo "<th id='eff_select_lv1_$i'>\n";
		//echo "<a href=\"javascript:modif_colonne('lv1_$i',true)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
		$eff_selection_lv1[$i]=0;
		$eff_selection_lv1_M[$i]=0;
		$eff_selection_lv1_F[$i]=0;
		echo "&nbsp;";
		echo "</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		echo "<th id='eff_select_lv2_$i'>\n";
		//echo "<a href=\"javascript:modif_colonne('lv2_$i',true)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
		$eff_selection_lv2[$i]=0;
		$eff_selection_lv2_M[$i]=0;
		$eff_selection_lv2_F[$i]=0;
		echo "&nbsp;";
		echo "</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		echo "<th id='eff_select_lv3_$i'>\n";
		//echo "<a href=\"javascript:modif_colonne('lv3_$i',true)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
		$eff_selection_lv3[$i]=0;
		$eff_selection_lv3_M[$i]=0;
		$eff_selection_lv3_F[$i]=0;
		echo "&nbsp;";
		echo "</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		echo "<th id='eff_select_autre_opt_$i'>\n";
		//echo "<a href=\"javascript:modif_colonne('autre_opt_$i',true)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
		//echo " / <a href=\"javascript:modif_colonne('autre_opt_$i',false)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
		echo "&nbsp;";
		$eff_selection_autre_opt[$i]=0;
		$eff_selection_autre_opt_M[$i]=0;
		$eff_selection_autre_opt_F[$i]=0;
		echo "</th>\n";
	}
	echo "</tr>\n";
	//==========================================
	echo "</thead>\n";
	echo "<tbody>\n";



	$chaine_id_classe="";
	$cpt=0;
	// Boucle sur toutes les classes actuelles
	for($j=0;$j<count($id_classe_actuelle);$j++) {
		//$num_eleve1_id_classe_actuelle[$j]=$cpt;
		$eff_tot_classe_M=0;
		$eff_tot_classe_F=0;
	
		if($chaine_id_classe!="") {$chaine_id_classe.=",";}
		$chaine_id_classe.="'$id_classe_actuelle[$j]'";

		//==========================================
		//$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe_actuelle[$j]' ORDER BY e.nom,e.prenom;";
		$num_per2=-1;
		if(($id_classe_actuelle[$j]!='Red')&&($id_classe_actuelle[$j]!='Arriv')) {
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe_actuelle[$j]' ORDER BY e.nom,e.prenom;";
			//$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe_actuelle[$j]' AND (e.date_sortie IS NULL OR e.date_sortie NOT LIKE '20%') ORDER BY e.nom,e.prenom;";

			$sql_per="SELECT num_periode FROM periodes WHERE id_classe='$id_classe_actuelle[$j]' ORDER BY num_periode DESC LIMIT 1;";
			$res_per=mysql_query($sql_per);
			if(mysql_num_rows($res_per)>0) {
				$lig_per=mysql_fetch_object($res_per);
				$num_per2=$lig_per->num_periode;
			}
		}
		else {
			$sql="SELECT DISTINCT e.* FROM eleves e, gc_ele_arriv_red gc WHERE gc.login=e.login AND gc.statut='$id_classe_actuelle[$j]' AND gc.projet='$projet' ORDER BY e.nom,e.prenom;";
		}
		$res=mysql_query($sql);
		$eff_tot_classe=mysql_num_rows($res);
		$eff_tot+=$eff_tot_classe;
		//==========================================

		if(mysql_num_rows($res)>0) {
			while($lig=mysql_fetch_object($res)) {

				if(mb_strtoupper($lig->sexe)=='F') {$eff_tot_classe_F++;$eff_tot_F++;} else {$eff_tot_classe_M++;$eff_tot_M++;}

				if(!in_array($lig->login,$tab_ele)) {

					$fut_classe="";
					//$sql="SELECT * FROM gc_eleve_fut_classe WHERE projet='$projet' AND login='$lig->login';";
					$sql="SELECT classe_future FROM gc_eleves_options WHERE projet='$projet' AND login='$lig->login';";
					$res_clas=mysql_query($sql);
					if(mysql_num_rows($res_clas)>0) {
						// On récupère la classe future s'il y a déjà un enregistrement
						while($lig_clas=mysql_fetch_object($res_clas)) {
							// On ne devrait faire qu'un tour dans la boucle
							$fut_classe=mb_strtoupper($lig_clas->classe_future);
						}
					}

					for($i=0;$i<count($classe_fut);$i++) {
						if($fut_classe==mb_strtoupper($classe_fut[$i])) {
							$eff_fut_classe_hors_selection[$i]++;
							if($lig->sexe=='F') {
								$eff_fut_classe_hors_selection_F[$i]++;
							}
							else {
								$eff_fut_classe_hors_selection_M[$i]++;
							}
							break;
						}
					}

				}
				else {

					//echo "<tr id='tr_eleve_$cpt' class='white_hover'>\n";
					echo "<tr id='tr_eleve_$cpt' class='white_hover white_survol' onmouseover=\"this.style.backgroundColor='white';\" onmouseout=\"this.style.backgroundColor='';\">\n";
					//echo "<tr id='tr_eleve_$cpt' class='white_hover' onmouseover=\"document.getElementById('nom_prenom_eleve_numero_$cpt').style.color='red';\" onmouseout=\"document.getElementById('nom_prenom_eleve_numero_$cpt').style.color='';\">\n";
					echo "<td>\n";
					echo "<a name='eleve$cpt'></a>\n";
					if(nom_photo($lig->elenoet)) {
						echo "<a href='#eleve$cpt' onclick=\"affiche_photo('".nom_photo($lig->elenoet)."','".addslashes(mb_strtoupper($lig->nom)." ".ucfirst(mb_strtolower($lig->prenom)))."');afficher_div('div_photo','y',100,100);return false;\">";
						echo "<span id='nom_prenom_eleve_numero_$cpt' class='col_nom_eleve'>";
						echo mb_strtoupper($lig->nom)." ".ucfirst(mb_strtolower($lig->prenom));
						echo "</span>";
						echo "</a>\n";
					}
					else {
						echo "<span id='nom_prenom_eleve_numero_$cpt' class='col_nom_eleve'>";
						echo mb_strtoupper($lig->nom)." ".ucfirst(mb_strtolower($lig->prenom));
						echo "</span>";
					}
					echo "<input type='hidden' name='eleve[$cpt]' value='$lig->login' />\n";
					echo "</td>\n";
					echo "<td>";
					echo "<span style='display:none' id='eleve_sexe_$cpt'>".$lig->sexe."</span>";
					//echo image_sexe($lig->sexe);
					echo "<div id='div_sexe_$cpt' onclick=\"affiche_set_sexe($cpt, '$lig->login');changement();return false;\">".image_sexe($lig->sexe)."</div>\n";
					echo "</td>\n";
					echo "<td>$classe_actuelle[$j]</td>\n";


					//===================================
					// Initialisations
					$profil='RAS';
					$moy="-";
					$nb_absences="-";
					$non_justifie="-";
					$nb_retards="-";

					// On récupère les classe future, lv1, lv2, lv3 et autres options de l'élève $lig->login
					$fut_classe="";
					$tab_ele_opt=array();
					$sql="SELECT * FROM gc_eleves_options WHERE projet='$projet' AND login='$lig->login';";
					$res_opt=mysql_query($sql);
					if(mysql_num_rows($res_opt)>0) {
						$lig_opt=mysql_fetch_object($res_opt);

						$fut_classe=$lig_opt->classe_future;

						$profil=$lig_opt->profil;
						$moy=$lig_opt->moy;
						$nb_absences=$lig_opt->nb_absences;
						$non_justifie=$lig_opt->non_justifie;
						$nb_retards=$lig_opt->nb_retards;
		
						$tmp_tab=explode("|",$lig_opt->liste_opt);
						for($loop=0;$loop<count($tmp_tab);$loop++) {
							if($tmp_tab[$loop]!="") {
								$tab_ele_opt[]=mb_strtoupper($tmp_tab[$loop]);
							}
						}
					}
					else {
						// On récupère les options de l'année écoulée (année qui se termine)
						// ON NE DEVRAIT PAS VENIR SUR CETTE PAGE SANS ETRE PASSE D'ABORD PAR select_eleves_options.php
						$sql="SELECT * FROM j_eleves_groupes jeg, j_groupes_matieres jgm WHERE jeg.id_groupe=jgm.id_groupe AND jeg.login='$lig->login';";
						$res_opt=mysql_query($sql);
						if(mysql_num_rows($res_opt)>0) {
							while($lig_opt=mysql_fetch_object($res_opt)) {
								$tab_ele_opt[]=mb_strtoupper($lig_opt->id_matiere);
							}
						}
					}
					//===================================


					//echo "<td>Profil</td>\n";
					echo "<td>\n";
					echo "<input type='hidden' name='profil[$cpt]' id='profil_$cpt' value='$profil' />\n";
					echo "<div id='div_profil_$cpt' onclick=\"affiche_set_profil($cpt);changement();return false;\">$profil</div>\n";
					echo "</td>\n";



					//===================================
					echo "<td>\n";
					if(($moy!="")&&(mb_strlen(my_ereg_replace("[0-9.,]","",$moy))==0)) {
						if($num_per2>0) {
							echo "<a href=\"#\" onclick=\"afficher_div('div_bull_simp','y',-100,40); affiche_bull_simp('$lig->login','".$id_classe_actuelle[$j]."','1','$num_per2');return false;\" style='text-decoration:none;'>";
						}
						if($moy<7) {
							echo "<span style='color:red;'>";
						}
						elseif($moy<9) {
							echo "<span style='color:orange;'>";
						}
						elseif($moy<12) {
							echo "<span style='color:gray;'>";
						}
						elseif($moy<15) {
							echo "<span style='color:green;'>";
						}
						else {
							echo "<span style='color:blue;'>";
						}
						echo "$moy";
						if($num_per2>0) {
							echo "</a>\n";
						}
						echo "</span>\n";
					}
					else {
						echo "-\n";
					}
					echo "</td>\n";
					//===================================

					//===================================
					echo "<td>\n";
					echo colorise_abs($nb_absences,$non_justifie,$nb_retards);
					echo "</td>\n";
					//===================================

					//===================================
					for($i=0;$i<count($classe_fut);$i++) {
						echo "<td";

						$coche_possible='y';
						if(($classe_fut[$i]!='Red')&&($classe_fut[$i]!='Dep')&&($classe_fut[$i]!='')) {
							for($loop=0;$loop<count($tab_ele_opt);$loop++) {
								if(in_array(mb_strtoupper($tab_ele_opt[$loop]),$tab_opt_exclue["$classe_fut[$i]"])) {
									$coche_possible='n';
									break;
								}
							}
						}

						if($coche_possible=='y') {
							echo " onclick=\"document.getElementById('classe_fut_".$i."_".$cpt."').checked=true;calcule_effectif('classe_fut',".count($classe_fut).");colorise_ligne('classe_fut',$cpt,$i);changement();\"";
							echo ">\n";

							echo "<input type='radio' name='classe_fut[$cpt]' id='classe_fut_".$i."_".$cpt."' value='$classe_fut[$i]' ";
							if($fut_classe==mb_strtoupper($classe_fut[$i])) {
								echo "checked ";
	
								$eff_selection_classe_fut[$i]++;
								if($lig->sexe=='F') {
									$eff_selection_classe_fut_F[$i]++;
								}
								else {
									$eff_selection_classe_fut_M[$i]++;
								}
							}
							//alert('bip');
							echo "onmouseover=\"test_aff_classe3('".$lig->login."','".$classe_fut[$i]."');\" onmouseout=\"cacher_div('div_test_aff_classe2');\" ";
							echo "onchange=\"calcule_effectif('classe_fut',".count($classe_fut).");colorise_ligne('classe_fut',$cpt,$i);changement();\" ";
							//echo "title=\"$lig->login/$classe_fut[$i]\" ";
							echo "/>\n";
						}
						else {
							echo ">\n";
							echo "_";
						}

						echo "</td>\n";
					}
		
					for($i=0;$i<count($lv1);$i++) {
						echo "<td title='$lv1[$i]'>\n";
						if(in_array(mb_strtoupper($lv1[$i]),$tab_ele_opt)) {
							echo "<div style='display:none;'><input type='checkbox' name='lv1[$cpt]' id='lv1_".$i."_".$cpt."' value='$lv1[$i]' checked /></div>\n";
							echo "<span title='$lv1[$i]'>X</span>";
							$eff_selection_lv1[$i]++;
							if($lig->sexe=='F') {
								$eff_selection_lv1_F[$i]++;
							}
							else {
								$eff_selection_lv1_M[$i]++;
							}
						}
						else {
							echo "&nbsp;";
						}
						// Compter les effectifs...
						echo "</td>\n";
					}
		
		
					for($i=0;$i<count($lv2);$i++) {
						echo "<td title='$lv2[$i]'>\n";
						if(in_array(mb_strtoupper($lv2[$i]),$tab_ele_opt)) {
							echo "<div style='display:none;'><input type='checkbox' name='lv2[$cpt]' id='lv2_".$i."_".$cpt."' value='$lv2[$i]' checked /></div>\n";
							echo "<span title='$lv2[$i]'>X</span>";
							$eff_selection_lv2[$i]++;
							if($lig->sexe=='F') {
								$eff_selection_lv2_F[$i]++;
							}
							else {
								$eff_selection_lv2_M[$i]++;
							}
						}
						else {
							echo "&nbsp;";
						}
						echo "</td>\n";
					}
		
		
					for($i=0;$i<count($lv3);$i++) {
						echo "<td title='$lv3[$i]'>\n";
						if(in_array(mb_strtoupper($lv3[$i]),$tab_ele_opt)) {
							echo "<div style='display:none;'><input type='checkbox' name='lv3[$cpt]' id='lv3_".$i."_".$cpt."' value='$lv3[$i]' checked /></div>\n";
							echo "<span title='$lv3[$i]'>X</span>";
							$eff_selection_lv3[$i]++;
							if($lig->sexe=='F') {
								$eff_selection_lv3_F[$i]++;
							}
							else {
								$eff_selection_lv3_M[$i]++;
							}
						}
						else {
							echo "&nbsp;";
						}
						echo "</td>\n";
					}
		
					for($i=0;$i<count($autre_opt);$i++) {
						echo "<td title='$autre_opt[$i]'>\n";
						if(in_array(mb_strtoupper($autre_opt[$i]),$tab_ele_opt)) {
							echo "<div style='display:none;'><input type='checkbox' name='autre_opt[$cpt]' id='autre_opt_".$i."_".$cpt."' value='$autre_opt[$i]' checked /></div>\n";
							echo "<span title='$autre_opt[$i]'>X</span>";
							$eff_selection_autre_opt[$i]++;
							if($lig->sexe=='F') {
								$eff_selection_autre_opt_F[$i]++;
							}
							else {
								$eff_selection_autre_opt_M[$i]++;
							}
						}
						else {
							echo "&nbsp;";
						}
						echo "</td>\n";
					}
					echo "</tr>\n";
					$cpt++;
				}
			}
		}
	}
	echo "</tbody>\n";
	echo "<tfoot>\n";

	//==========================================
	echo "<tr>\n";
	//echo "<th>Effectifs&nbsp;: <span id='eff_tot'>&nbsp;</span></th>\n";
	echo "<th rowspan='2'>Eff.select&nbsp;:</th>\n";
	echo "<th rowspan='2' id='eff_select'>$cpt</th>\n";
	echo "<th rowspan='2' id='eff_select_sexe'>&nbsp;</th>\n";
	echo "<th rowspan='2'>&nbsp;</th>\n";
	echo "<th rowspan='2'>&nbsp;</th>\n";
	echo "<th rowspan='2'>&nbsp;</th>\n";

	for($i=0;$i<count($classe_fut);$i++) {
		echo "<th id='eff_col_classe_fut_select_".$i."'>".$eff_selection_classe_fut[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($lv1);$i++) {
		echo "<th id='eff_col_lv1_select_".$i."'>".$eff_selection_lv1[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		echo "<th id='eff_col_lv2_select_".$i."'>".$eff_selection_lv2[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		echo "<th id='eff_col_lv3_select_".$i."'>".$eff_selection_lv3[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		echo "<th id='eff_col_autre_opt_select_".$i."'>".$eff_selection_autre_opt[$i];
		echo "</th>\n";
	}
	echo "</tr>\n";


	echo "<tr>\n";
	for($i=0;$i<count($classe_fut);$i++) {
		echo "<th id='eff_col_sexe_classe_fut_select_".$i."'>\n";
		echo $eff_selection_classe_fut_M[$i]."/".$eff_selection_classe_fut_F[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($lv1);$i++) {
		echo "<th id='eff_col_sexe_lv1_select_".$i."'>\n";
		echo $eff_selection_lv1_M[$i]."/".$eff_selection_lv1_F[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		echo "<th id='eff_col_sexe_lv2_select_".$i."'>\n";
		echo $eff_selection_lv2_M[$i]."/".$eff_selection_lv2_F[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		echo "<th id='eff_col_sexe_lv3_select_".$i."'>\n";
		echo $eff_selection_lv3_M[$i]."/".$eff_selection_lv3_F[$i];
		echo "</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		echo "<th id='eff_col_sexe_autre_opt_select_".$i."'>\n";
		echo $eff_selection_autre_opt_M[$i]."/".$eff_selection_autre_opt_F[$i];
		echo "</th>\n";
	}
	echo "</tr>\n";

	//==========================================
	echo "<tr>\n";
	echo "<th>Elève</th>\n";
	echo "<th>Sexe</th>\n";
	echo "<th>Classe<br />actuelle</th>\n";
	echo "<th>Profil</th>\n";
	echo "<th>Niveau</th>\n";
	echo "<th>Absences Non.Just Retards</th>\n";

	for($i=0;$i<count($classe_fut);$i++) {
		echo "<th>$classe_fut[$i]</th>\n";
	}
	for($i=0;$i<count($lv1);$i++) {
		echo "<th>$lv1[$i]</th>\n";
	}
	for($i=0;$i<count($lv2);$i++) {
		echo "<th>$lv2[$i]</th>\n";
	}
	for($i=0;$i<count($lv3);$i++) {
		echo "<th>$lv3[$i]</th>\n";
	}
	for($i=0;$i<count($autre_opt);$i++) {
		echo "<th>$autre_opt[$i]</th>\n";
	}
	echo "</tr>\n";
	//==========================================
	echo "</tfoot>\n";

	echo "</table>\n";
	
	echo "<input type='hidden' name='projet' value='$projet' />\n";
	echo "<input type='hidden' name='is_posted' value='y' />\n";
	echo "<input type='hidden' name='choix_affich' value='done' />\n";
	echo "<p align='center'><input type='submit' name='valide_aff_classe_fut' value='Valider' /></p>\n";
	echo "<hr width='200'/>\n";

	echo "</form>\n";


	$titre="<span id='entete_div_photo_eleve'>Elève</span>";
	$texte="<div id='corps_div_photo_eleve' align='center'>\n";
	$texte.="<br />\n";
	$texte.="</div>\n";
	
	$tabdiv_infobulle[]=creer_div_infobulle('div_photo',$titre,"",$texte,"",14,0,'y','y','n','n');
	

	//===============================================
	// Paramètres concernant le délais avant affichage d'une infobulle via delais_afficher_div()
	// Hauteur de la bande testée pour la position de la souris:
	$hauteur_survol_infobulle=20;
	// Largeur de la bande testée pour la position de la souris:
	$largeur_survol_infobulle=100;
	// Délais en ms avant affichage:
	$delais_affichage_infobulle=2000;

	echo "<script type='text/javascript'>
	/*
	function test_aff_classe(classe_fut) {
		//new Ajax.Updater($('div_test_aff_classe'),'liste_classe_fut.php?projet='+$projet+'&amp;classe_fut='+classe_fut,{method: 'get'});
		//new Ajax.Updater($('div_test_aff_classe'),'liste_classe_fut.php?classe_fut='+classe_fut,{method: 'get'});
		new Ajax.Updater($('div_test_aff_classe'),'liste_classe_fut.php?classe_fut='+classe_fut+'&projet=$projet',{method: 'get'});
	}
	*/

	function test_aff_classe2(classe_fut) {
		new Ajax.Updater($('div_test_aff_classe2'),'liste_classe_fut.php?classe_fut='+classe_fut+'&projet=$projet',{method: 'get'});
		delais_afficher_div('div_test_aff_classe2','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);
	}

	function test_aff_classe3(login,classe_fut) {
		//new Ajax.Updater($('div_test_aff_classe2'),'liste_classe_fut.php?ele_login='+login+'&classe_fut='+classe_fut+'&projet=$projet',{method: 'get'});
		new Ajax.Updater($('div_test_aff_classe2'),'liste_classe_fut.php?ele_login='+login+'&classe_fut='+classe_fut+'&projet=$projet&avec_classe_origine=y',{method: 'get'});
		delais_afficher_div('div_test_aff_classe2','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);
	}
</script>\n";

	echo "<div id='div_test_aff_classe2' class='infobulle_corps' style='position:absolute; border:1px solid black;'>Classes futures</div>\n";

	//===============================================

	$titre="Sélection du profil";
	$texte="<p style='text-align:center;'>";
	for($loop=0;$loop<count($tab_profil);$loop++) {
		if($loop>0) {$texte.=" - ";}
		$texte.="<a href='#' onclick=\"set_profil('".$tab_profil[$loop]."');return false;\">$tab_profil[$loop]</a>";
	}
	$texte.="</p>\n";
	$tabdiv_infobulle[]=creer_div_infobulle('div_set_profil',$titre,"",$texte,"",14,0,'y','y','n','n');

	$titre="Sélection du sexe";
	$texte="<p style='text-align:center;'>";
	for($loop=0;$loop<count($tab_sexe);$loop++) {
		if($loop>0) {$texte.=" - ";}
		$texte.="<a href='#' onclick=\"set_sexe('".$tab_sexe[$loop]."');return false;\">$tab_sexe[$loop]</a>";
	}
	$texte.="</p>\n";
	$tabdiv_infobulle[]=creer_div_infobulle('div_set_sexe',$titre,"",$texte,"",14,0,'y','y','n','n');

	echo "<input type='hidden' name='profil_courant' id='profil_courant' value='-1' />\n";
	echo "<input type='hidden' name='sexe_courant' id='sexe_courant' value='' />\n";
	echo "<input type='hidden' name='login_eleve_courant' id='login_eleve_courant' value='' />\n";

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

	function set_sexe(sexe) {
		var cpt=document.getElementById('sexe_courant').value;
		var login_eleve_courant=document.getElementById('login_eleve_courant').value;
		//document.getElementById('sexe_'+cpt).value=sexe;

		new Ajax.Updater($('div_sexe_'+cpt),'../eleves/modif_sexe.php?login_eleve='+login_eleve_courant+'&sexe='+sexe+'&mode_retour=image".add_token_in_url(false)."',{method: 'get'});

		document.getElementById('eleve_sexe_'+cpt).innerHTML=sexe;

		calcule_effectif('classe_fut',".count($classe_fut).");
		cacher_div('div_set_sexe');
	}

	function affiche_set_sexe(cpt, login) {
		document.getElementById('sexe_courant').value=cpt;
		document.getElementById('login_eleve_courant').value=login;
		afficher_div('div_set_sexe','y',100,100);
	}

	for(i=0;i<$cpt;i++) {
		if(document.getElementById('profil_'+i)) {
			profil=document.getElementById('profil_'+i).value;

			for(m=0;m<couleur_profil.length;m++) {
				if(document.getElementById('profil_'+i).value==tab_profil[m]) {
					document.getElementById('div_profil_'+i).style.color=couleur_profil[m];
				}
			}
		}
	}

</script>
\n";

	//===============================================

	echo "<script type='text/javascript'>
	document.getElementById('div_test_aff_classe2').style.display='none';

	function affiche_photo(photo,nom_prenom) {
		document.getElementById('entete_div_photo_eleve').innerHTML=nom_prenom;
		document.getElementById('corps_div_photo_eleve').innerHTML='<img src=\"'+photo+'\" width=\"150\" alt=\"Photo\" /><br />';
	}
";


echo "var eff_fut_classe_hors_selection=new Array(";
for($i=0;$i<count($classe_fut);$i++) {
	if($i>0) {echo ",";}
	echo "$eff_fut_classe_hors_selection[$i]";
}
echo ");\n";

echo "var eff_fut_classe_hors_selection_F=new Array(";
for($i=0;$i<count($classe_fut);$i++) {
	if($i>0) {echo ",";}
	echo "$eff_fut_classe_hors_selection_F[$i]";
}
echo ");\n";

echo "var eff_fut_classe_hors_selection_M=new Array(";
for($i=0;$i<count($classe_fut);$i++) {
	if($i>0) {echo ",";}
	echo "$eff_fut_classe_hors_selection_M[$i]";
}
echo ");\n";


echo "
	function calcule_effectif(champ,n) {
		for(k=0;k<n;k++) {
			eff=0;
			eff_M=0;
			eff_F=0;
			for(i=0;i<$cpt;i++) {
				//alert('document.getElementById('+champ+'_'+i+')')
				if(document.getElementById(champ+'_'+k+'_'+i)) {
					if(document.getElementById(champ+'_'+k+'_'+i).checked) {
						eff++;
						if(document.getElementById('eleve_sexe_'+i).innerHTML=='M') {eff_M++;} else {eff_F++;}
					}
				}
			}

			document.getElementById('eff_col_'+champ+'_select_'+k).innerHTML=eff;
			document.getElementById('eff_col_sexe_'+champ+'_select_'+k).innerHTML=eff_M+'/'+eff_F;

			eff=eff+eff_fut_classe_hors_selection[k];
			eff_M=eff_M+eff_fut_classe_hors_selection_M[k];
			eff_F=eff_F+eff_fut_classe_hors_selection_F[k];

			document.getElementById('eff_col_'+champ+'_'+k).innerHTML=eff;
			document.getElementById('eff_col_sexe_'+champ+'_'+k).innerHTML=eff_M+'/'+eff_F;

			//alert('eff='+eff);
		}
	}
	
	calcule_effectif('classe_fut',".count($classe_fut).");

	var couleur_classe_fut=new Array($chaine_couleur_classe_fut);
	var couleur_lv1=new Array($chaine_couleur_lv1);
	var couleur_lv2=new Array($chaine_couleur_lv2);
	var couleur_lv3=new Array($chaine_couleur_lv3);
	
	function colorise(mode,n) {
		var k;
		var i;

		for(k=0;k<n;k++) {
			for(i=0;i<$cpt;i++) {
				if(mode!='profil') {
					if(document.getElementById(mode+'_'+k+'_'+i)) {
						if(document.getElementById(mode+'_'+k+'_'+i).checked) {
							if(mode=='classe_fut') {
								document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_classe_fut[k];
							}
							if(mode=='lv1') {
								document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_lv1[k];
							}
							if(mode=='lv2') {
								document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_lv2[k];
							}
							if(mode=='lv3') {
								document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_lv3[k];
							}
						}
					}
				}
				else {
					for(m=0;m<couleur_profil.length;m++) {
						if(document.getElementById('profil_'+i).value==tab_profil[m]) {
							document.getElementById('tr_eleve_'+i).style.backgroundColor=couleur_profil[m];
						}
					}
				}
			}
		}
	}
	
	colorise('classe_fut',".count($classe_fut).");
	
	function colorise_ligne(cat,cpt,i) {
		// On ne traite qu'une ligne contrairement à colorise()
		//alert('couleur_classe_fut[0]='+couleur_classe_fut[0]);
		//alert(document.forms['form_affect_eleves_classes'].elements['colorisation'].options[document.forms['form_affect_eleves_classes'].elements['colorisation'].selectedIndex].value);
		if(document.forms['form_affect_eleves_classes'].elements['colorisation'].options[document.forms['form_affect_eleves_classes'].elements['colorisation'].selectedIndex].value==cat) {
			if(cat=='classe_fut') {
				//alert(cat);
				//alert(i);
				//alert(couleur_classe_fut[i]);
				document.getElementById('tr_eleve_'+cpt).style.backgroundColor=couleur_classe_fut[i];
			}
			if(cat=='lv1') {
				document.getElementById('tr_eleve_'+cpt).style.backgroundColor=couleur_lv1[i];
			}
			if(cat=='lv2') {
				document.getElementById('tr_eleve_'+cpt).style.backgroundColor=couleur_lv2[i];
			}
			if(cat=='lv3') {
				document.getElementById('tr_eleve_'+cpt).style.backgroundColor=couleur_lv3[i];
			}
			if(cat=='profil') {
				document.getElementById('tr_eleve_'+cpt).style.backgroundColor=couleur_profil[i];
			}
		}
	}
	
	function lance_colorisation() {
		cat=document.forms['form_affect_eleves_classes'].elements['colorisation'].options[document.forms['form_affect_eleves_classes'].elements['colorisation'].selectedIndex].value;
		//alert(cat);
		if(cat=='classe_fut') {
			colorise(cat,".count($classe_fut).");
		}
		if(cat=='lv1') {
			colorise(cat,".count($lv1).");
		}
		if(cat=='lv2') {
			colorise(cat,".count($lv2).");
		}
		if(cat=='lv3') {
			colorise(cat,".count($lv3).");
		}
		if(cat=='profil') {
			colorise(cat,".count($tab_profil).");
		}
	}

	function modif_colonne(col,mode) {
		for(i=0;i<=$cpt;i++) {
			if(document.getElementById(col+'_'+i)) {
				document.getElementById(col+'_'+i).checked=mode;
			}
		}
	
		cat=document.forms['form_affect_eleves_classes'].elements['colorisation'].options[document.forms['form_affect_eleves_classes'].elements['colorisation'].selectedIndex].value;
		if(col.substr(0,cat.length)==cat) {lance_colorisation();}

		// Lancer un recalcul des effectifs
		calcule_effectif('classe_fut',".count($classe_fut).");
	}

</script>\n";

	echo "<p><i>NOTES&nbsp;:</i></p>\n";
	echo "<ul>\n";
	//echo "<li></li>\n";
	echo "<li>Les Redoublants (<i>Red</i>) et Partants (<i>Dep</i>) sont exclus de la sélection puisque déjà affectés.</li>\n";
	echo "</ul>\n";

	echo "<script type='text/javascript'>
	document.getElementById('bandeau').className+=' noprint';
</script>\n";
}

require("../lib/footer.inc.php");
?>
