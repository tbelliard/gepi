<?php
/*
 *
 *
 * Copyright 2001, 2025 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Stephane Boireau
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

$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions complémentaires et/ou librairies utiles

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
	header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
	die();
} else if ($resultat_session == "0") {
	// Nouvel essai pour essayer d'ouvrir une session SSO CAS:
	require_once("../lib/auth_sso.inc.php");

	if (($resultat_session == '0')||($resultat_session == 'c')) {
		//header("Location: ../logout.php?auto=1");
		header("Location: ../logout.php?auto=1");
		die();
	}
}

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/voir_pj.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/voir_pj.php',
administrateur='F',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='F',
autre='F',
description='Consulter les PJ à une notice de CDT',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=2");
	die();
}

if((getSettingANon('active_cahiers_texte'))&&(getSettingANon('acces_cdt_prof'))) {
	header("Location: ../accueil.php?msg=Accès non autorisé.");
	die();
}

$id_ct=isset($_GET['id_ct']) ? $_GET['id_ct'] : NULL;
$type_notice=isset($_GET['type_notice']) ? $_GET['type_notice'] : NULL;

if((!isset($id_ct))||(!preg_match('/^[0-9]{1,}$/', $id_ct))) {
	header("Location: ../accueil.php?msg=Numéro de notice invalide.");
	die();
}

if((!isset($type_notice))||(!is_string($type_notice))||
(($type_notice!='c')&&($type_notice!='t'))) {
	header("Location: ../accueil.php?msg=Type notice invalide.");
	die();
}

if($type_notice=='c') {
	$sql="SELECT id_groupe, date_ct FROM ct_entry WHERE id_ct='".$id_ct."';";
	$titre_notice="Documents joints au compte-rendu de séance du ";
	$table_ct='ct_entry';
}
else {
	$sql="SELECT id_groupe, date_ct FROM ct_devoirs_entry WHERE id_ct='".$id_ct."';";
	$titre_notice="Documents joints aux travaux à faire pour le ";
	$table_ct='ct_devoirs_entry';
}
$res_grp=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res_grp)==0) {
	header("Location: ../accueil.php?msg=Numéro de notice invalide.");
	die();
}

$lig_grp=mysqli_fetch_object($res_grp);
$id_groupe=$lig_grp->id_groupe;
$titre_notice.=strftime("%A %d/%m/%Y", $lig_grp->date_ct);

// Tester si la personne connectée a accès au groupe correspondant.
if($_SESSION['statut']=='eleve') {
	$sql="SELECT 1=1 FROM j_eleves_groupes WHERE id_groupe='".$id_groupe."' AND login='".$_SESSION['login']."';";
	$test=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($test)==0) {
		header("Location: ../accueil.php?msg=Accès à cet enseignement non autorisé.");
		die();
	}

	// Récupérer la liste des documents accessibles
	$tab_doc=array();
	if($type_notice=='c') {
		$sql="SELECT * FROM ct_documents WHERE id_ct='".$id_ct."' AND visible_eleve_parent='1' ORDER BY CONCAT('a', titre) ASC;";
	}
	else {
		$sql="SELECT * FROM ct_devoirs_documents WHERE id_ct_devoir='".$id_ct."' AND visible_eleve_parent='1' ORDER BY CONCAT('a', titre) ASC;";
	}
	$res_doc=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_doc)>0) {
		while($lig_doc=mysqli_fetch_assoc($res_doc)) {
			$tab_doc[]=$lig_doc;
		}
	}
}
elseif($_SESSION['statut']=='responsable') {








}
elseif(in_array($_SESSION['statut'], array('professeur', 'scolarite', 'cpe'))) {
	$restriction=" AND visible_eleve_parent='1'";
	if($_SESSION['statut']=='professeur') {
		$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='".$id_groupe."' AND login='".$_SESSION['login']."';";
		$test=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($test)>0) {
			$restriction="";
		}
	}

	// Récupérer la liste des documents accessibles
	$tab_doc=array();
	if($type_notice=='c') {
		$sql="SELECT * FROM ct_documents WHERE id_ct='".$id_ct."'".$restriction." ORDER BY CONCAT('a', titre) ASC;";
	}
	else {
		$sql="SELECT * FROM ct_devoirs_documents WHERE id_ct_devoir='".$id_ct."'".$restriction." ORDER BY CONCAT('a', titre) ASC;";
	}
	//echo "$sql<br />";
	$res_doc=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_doc)>0) {
		while($lig_doc=mysqli_fetch_assoc($res_doc)) {
			$tab_doc[]=$lig_doc;
		}
	}
}
else {
	header("Location: ../accueil.php?msg=Accès non autorisé.");
	die();
}
// Remplir un tableau des documents

// ===================== entete Gepi ======================================//
//$titre_page = "CDT : Pièces jointes";
require_once("../lib/header.inc.php");
// ===================== fin entete =======================================//

//debug_var();

$precedent_suivant='';

if($_SESSION['statut']=='professeur') {
	if((getSettingAOui('active_cahiers_texte'))||
	(getSettingAOui('acces_cdt_prof'))) {
		$retour='../cahier_texte_2/index.php';

		//=======================================
		echo "<p class='bold'><a href='".$retour."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

		//Mettre des liens vers d'autres enseignements, d'autres dates du même enseignement...

		/*
		$precedent_suivant.="
		<div style='float:left; width:20px; text-align:center; '>";
		*/

		// Lien séance précédente/suivante
		// Problème lorsqu'on a deux heures dans la même journée (ajouter un test)
		$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$lig_grp->id_groupe."' AND id_ct<'".$id_ct."' AND date_ct='".$lig_grp->date_ct."' ORDER BY id_ct DESC;";
		$res_mult=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_mult)>0) {
			$lig_prec=mysqli_fetch_object($res_mult);
			$precedent_suivant.="
				 <a href='".$_SERVER['PHP_SELF']."?id_ct=".$lig_prec->id_ct."&type_notice=".$type_notice."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_prec->date_ct)."\"><img src='../images/icons/back.png' class='icone16' alt='Séance précédente' /></a>";
		}
		else {
			$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$lig_grp->id_groupe."' AND id_ct!='".$id_ct."' AND date_ct<'".$lig_grp->date_ct."' ORDER BY date_ct DESC limit 1;";
			$res_prec=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_prec)>0) {
				$lig_prec=mysqli_fetch_object($res_prec);
				$precedent_suivant.="
				 <a href='".$_SERVER['PHP_SELF']."?id_ct=".$lig_prec->id_ct."&type_notice=".$type_notice."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_prec->date_ct)."\"><img src='../images/icons/back.png' class='icone16' alt='Séance précédente' /></a>";
			}
		}
		/*
		$precedent_suivant.="
			</div>

			<div style='float:left; width:20px; text-align:center; '>";
		*/

		if($type_notice=='c') {
			$type_notice_2='cr';
		}
		elseif($type_notice=='t') {
			$type_notice_2='dev';
		}
		else {
			$type_notice_2='priv';
		}

		$precedent_suivant.="
				 <a href='index.php?id_groupe=".$lig_grp->id_groupe."&id_ct=".$id_ct."&type_notice=".$type_notice_2."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_grp->date_ct)."\"><img src='../images/edit16.png' class='icone16' alt='Séance' /></a> ";

		/*
		$precedent_suivant.="
		</div>

		<div style='float:left; width:20px; text-align:center; '>";
		*/

		$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$lig_grp->id_groupe."' AND id_ct>'".$id_ct."' AND date_ct='".$lig_grp->date_ct."' ORDER BY id_ct ASC;";
		$res_mult=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_mult)>0) {
			$lig_suiv=mysqli_fetch_object($res_mult);
			$precedent_suivant.="
				 <a href='".$_SERVER['PHP_SELF']."?id_ct=".$lig_suiv->id_ct."&type_notice=".$type_notice."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_suiv->date_ct)."\"><img src='../images/icons/forward.png' class='icone16' alt='Séance suivante' /></a>";
		}
		else {
			$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$lig_grp->id_groupe."' AND id_ct!='".$id_ct."' AND date_ct>'".$lig_grp->date_ct."' ORDER BY date_ct ASC limit 1;";
			$res_suiv=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_suiv)>0) {
				$lig_suiv=mysqli_fetch_object($res_suiv);
				$precedent_suivant.="
				 <a href='".$_SERVER['PHP_SELF']."?id_ct=".$lig_suiv->id_ct."&type_notice=".$type_notice."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_suiv->date_ct)."\"><img src='../images/icons/forward.png' class='icone16' alt='Séance suivante' /></a>";
			}
		}

		/*
		$precedent_suivant.="
		</div>
		<div style='clear:both;'></div>";
		*/
	//=======================================
	}
	else {
		$retour='../accueil.php';
	}
}
else {
	$retour='../accueil.php';
}

echo "
<h2 align='center'>".$titre_notice." ".$precedent_suivant."</h2>";

if(count($tab_doc)==0) {
	echo "<p style='color:red'>Aucun document n'est joint à cette notice.</p>";
}
else {
	// Permettre de parcourir les images,... sans recharger la page, via JS.
	//echo "count(\$tab_doc)=".count($tab_doc)."<br />";
	$tab_img=array();
	$tab_autre=array();
	foreach($tab_doc as $key => $document) {
		/*
		echo "==========================<br />Document $key<br />
		<pre>";
		print_r($document);
		echo "</pre>";
		*/

		$emplacement_min=strtolower($document['emplacement']);
		if((preg_match('/.png$/', $emplacement_min))||
		(preg_match('/.gif$/', $emplacement_min))||
		(preg_match('/.jpg$/', $emplacement_min))||
		(preg_match('/.jpeg$/', $emplacement_min))) {
			$tab_img[]=$document;
		}
		else {
			$tab_autre[]=$document;
		}
	}

	echo "";

	// 20251102
	echo "
	<div align='center'>
		<p class='bold'>Images jointes&nbsp;: 
		<a href='#' onclick=\"image_precedente(); return false;\" title='Précédent'>
			<img src='../images/icons/arrow-left.png' class='icone20' />
		</a>
		&nbsp;
		<a href='#' onclick=\"toutes_les_images(); return false;\" title=\"Afficher toutes les images\" id='a_toutes_images'>
			<img src='../images/icons/images.png' class='icone20' />
		</a>
		<a href='#' onclick=\"une_seule_image(); return false;\" title=\"N'afficher qu'une seule image\" id='a_une_seule_image' style='display:none'>
			<img src='../images/icons/image.png' class='icone20' />
		</a>
		&nbsp;
		<a href='#' onclick=\"image_suivante(); return false;\" title='Précédent'>
			<img src='../images/icons/arrow-right.png' class='icone20' />
		</a>
		<br />";
	for($loop=0;$loop<count($tab_img);$loop++) {
		echo "<img src='".$tab_img[$loop]['emplacement']."' id='image_".$loop."' ";
		if($loop>0) {
			echo "style='display:none;' ";
		}
		echo "/>";
	}

	echo "
		</p>
	</div>
	
		<script type='text/javascript'>
			var indice=0;
			function image_suivante() {
				if(indice<".(count($tab_img)-1).") {
					indice++;
					for(i=0;i<".count($tab_img).";i++) {
						if(i==indice) {
							document.getElementById('image_'+i).style.display='';
						}
						else {
							document.getElementById('image_'+i).style.display='none';
						}
					}
				}
			}

			function image_precedente() {
				if(indice>0) {
					indice--;
					for(i=0;i<".count($tab_img).";i++) {
						if(i==indice) {
							document.getElementById('image_'+i).style.display='';
						}
						else {
							document.getElementById('image_'+i).style.display='none';
						}
					}
				}
			}

			function une_seule_image() {
				indice=0;
				document.getElementById('image_0').style.display='';

				for(i=1;i<".count($tab_img).";i++) {
					document.getElementById('image_'+i).style.display='none';
				}
				document.getElementById('a_toutes_images').style.display='';
				document.getElementById('a_une_seule_image').style.display='none';
			}

			function toutes_les_images() {
				for(i=0;i<".count($tab_img).";i++) {
					document.getElementById('image_'+i).style.display='block';
				}
				document.getElementById('a_toutes_images').style.display='none';
				document.getElementById('a_une_seule_image').style.display='';
			}

		</script>";


	/*
	echo "<img src='".$tab_img[0]['emplacement']."' id='image_affichee' />
	<script type='text/javascript'>
	</script>";
	*/



	echo "<p class='bold'>Document(s) joint(s)&nbsp;:</p>
	<ul>";
	foreach($tab_doc as $key => $document) {
		echo "<li><a href='".$document['emplacement']."'>".$document['titre']."</a></li>";
	}
	echo "</ul>";
}

// https://127.0.0.1/steph/gepi_git_trunk/cahier_texte_2/affiche_notice.php?id_ct=40930&type_notice=c
// https://127.0.0.1/steph/gepi_git_trunk/cahier_texte_2/voir_pj.php?id_ct=40930&type_notice=c

require_once("../lib/footer.inc.php");
?>
