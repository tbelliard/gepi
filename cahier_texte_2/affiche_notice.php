<?php
/*
*
*  Copyright 2001, 2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/affiche_notice.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/affiche_notice.php',
administrateur='F',
professeur='V',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Cahier de texte 2 : Affichage notice',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// Récupérer id_notice et type notice.
$id_ct=isset($_GET['id_ct']) ? $_GET['id_ct'] : NULL;
$type_notice=isset($_GET['type_notice']) ? $_GET['type_notice'] : NULL;

if((!isset($id_ct))||
(!isset($type_notice))||
(!preg_match('/^[0-9]{1,}$/', $id_ct))||
(($type_notice!='c')&&($type_notice!='t')&&($type_notice!='p'))) {
	header("Location: ../accueil.php?msg=Notice invalide");
	die();
}

if($type_notice=='c') {
	$table_ct='ct_entry';
}
elseif($type_notice=='t') {
	$table_ct='ct_devoirs_entry';
}
else {
	$table_ct='ct_private_entry';
}
$sql="SELECT * FROM ".$table_ct." WHERE id_ct='".$id_ct."';";
$res=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res)>0) {
	$lig_ct=mysqli_fetch_object($res);

	// Contrôler que la personne est propriétaire du CDT.
	$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='".$lig_ct->id_groupe."' AND login='".$_SESSION['login']."';";
	$test=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)>0) {
		$titre_page_title2=get_info_grp($lig_ct->id_groupe, array('classes'), '');
	}
}

// Pas d'entête
//**************** EN-TETE **************************************
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

// Afficher les infos groupe, date, type notice
// Lien notice précédente/suivante ?

// Afficher la notice

//debug_var();

$couleur_fond=$color_fond_notices[$type_notice];
echo "<div style='margin:0.5em; padding:0.5em; background-color:".$couleur_fond."; border: 1px solid black;'>";
// Mettre en float right un retour à la page d'accueil.
echo "
	<div style='float:right; width:16px; margin:0.5em;'>
		<a href='../accueil.php'><img src='../images/icons/home.png' class='icone16' alt='Accueil' /></a>
	</div>";

if($type_notice=='c') {
	$table_ct='ct_entry';
}
elseif($type_notice=='t') {
	$table_ct='ct_devoirs_entry';
}
else {
	$table_ct='ct_private_entry';
}
$sql="SELECT * FROM ".$table_ct." WHERE id_ct='".$id_ct."';";
$res=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p style='color:red'>La notice n'existe pas.</p>";
	echo "</div>";

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();
}

$lig_ct=mysqli_fetch_object($res);

// Contrôler que la personne est propriétaire du CDT.
$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='".$lig_ct->id_groupe."' AND login='".$_SESSION['login']."';";
$test=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p style='color:red'>Vous n'êtes pas propriétaire de ce CDT.</p>";
	echo "</div>";

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();
}

// Et un autre vers visu CDT
$ancre_see_all='';
if($type_notice=='c') {
	$ancre_see_all='#compte_rendu_'.$id_ct;
}
elseif($type_notice=='t') {
	$ancre_see_all='#travail_'.$id_ct;
}
echo "
	<div style='float:right; width:16px; margin:0.5em;'>
		<a href='../cahier_texte_2/see_all.php?id_groupe=".$lig_ct->id_groupe.$ancre_see_all."'><img src='../images/icons/cahier_textes.png' class='icone16' alt='CDT' /></a>
	</div>";

echo "
	<h2>".get_info_grp($lig_ct->id_groupe)."</h2>";

//echo "\$type_notice=$type_notice<br />";

if($type_notice!='c') {
	$sql="SELECT * FROM ct_entry WHERE id_groupe='".$lig_ct->id_groupe."' AND date_ct='".$lig_ct->date_ct."';";
	$test_autre_notice=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($test_autre_notice)>0) {
		while($lig_autre_notice=mysqli_fetch_object($test_autre_notice)) {
			echo "
	<div style='float:right; width:16px; margin:0.2em;'>
		<a href='affiche_notice.php?id_ct=".$lig_autre_notice->id_ct."&amp;type_notice=c' title=\"Consulter le compte-rendu de séance n°".$lig_autre_notice->id_ct." pour le même jour.\"><img src='../images/icons/notices_CDT_compte_rendu.png' class='icone16' alt='CDT' /></a>
	</div>";
		}
	}
}

if($type_notice!='t') {
	$sql="SELECT * FROM ct_devoirs_entry WHERE id_groupe='".$lig_ct->id_groupe."' AND date_ct='".$lig_ct->date_ct."';";
	$test_autre_notice=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($test_autre_notice)>0) {
		while($lig_autre_notice=mysqli_fetch_object($test_autre_notice)) {
			echo "
	<div style='float:right; width:16px; margin:0.2em;'>
		<a href='affiche_notice.php?id_ct=".$lig_autre_notice->id_ct."&amp;type_notice=t' title=\"Consulter la notice de travail à faire à la maison n°".$lig_autre_notice->id_ct." pour le même jour.\"><img src='../images/icons/notices_CDT_travail.png' class='icone16' alt='CDT' /></a>
	</div>";
		}
	}
}

if($type_notice!='p') {
	$sql="SELECT * FROM ct_private_entry WHERE id_groupe='".$lig_ct->id_groupe."' AND date_ct='".$lig_ct->date_ct."';";
	$test_autre_notice=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($test_autre_notice)>0) {
		while($lig_autre_notice=mysqli_fetch_object($test_autre_notice)) {
			echo "
	<div style='float:right; width:16px; margin:0.2em;'>
		<a href='affiche_notice.php?id_ct=".$lig_autre_notice->id_ct."&amp;type_notice=p' title=\"Consulter la notice privée n°".$lig_autre_notice->id_ct." pour le même jour.\"><img src='../images/icons/notices_CDT_privee.png' class='icone16' alt='CDT' /></a>
	</div>";
		}
	}
}

echo "
	<div style='float:left; width:18.5em; font-weight:bold;'>
		Séance du ".french_strftime("%A %d/%m/%Y", $lig_ct->date_ct)."
	</div>

	<div style='float:left; width:20px; text-align:center; '>";

// Lien séance précédente/suivante
// Problème lorsqu'on a deux heures dans la même journée (ajouter un test)
$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$lig_ct->id_groupe."' AND id_ct<'".$id_ct."' AND date_ct='".$lig_ct->date_ct."' ORDER BY id_ct DESC;";
$res_mult=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res_mult)>0) {
	$lig_prec=mysqli_fetch_object($res_mult);
	echo "
		 <a href='".$_SERVER['PHP_SELF']."?id_ct=".$lig_prec->id_ct."&type_notice=".$type_notice."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_prec->date_ct)."\"><img src='../images/icons/back.png' class='icone16' alt='Séance précédente' /></a>";
}
else {
	$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$lig_ct->id_groupe."' AND id_ct!='".$id_ct."' AND date_ct<'".$lig_ct->date_ct."' ORDER BY date_ct DESC limit 1;";
	$res_prec=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_prec)>0) {
		$lig_prec=mysqli_fetch_object($res_prec);
		echo "
		 <a href='".$_SERVER['PHP_SELF']."?id_ct=".$lig_prec->id_ct."&type_notice=".$type_notice."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_prec->date_ct)."\"><img src='../images/icons/back.png' class='icone16' alt='Séance précédente' /></a>";
	}
}
echo "
	</div>

	<div style='float:left; width:20px; text-align:center; '>";

if($type_notice=='c') {
	$type_notice_2='cr';
}
elseif($type_notice=='t') {
	$type_notice_2='dev';
}
else {
	$type_notice_2='priv';
}

echo "
		 <a href='index.php?id_groupe=".$lig_ct->id_groupe."&id_ct=".$id_ct."&type_notice=".$type_notice_2."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_ct->date_ct)."\"><img src='../images/edit16.png' class='icone16' alt='Séance' /></a> ";

echo "
	</div>

	<div style='float:left; width:20px; text-align:center; '>";

$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$lig_ct->id_groupe."' AND id_ct>'".$id_ct."' AND date_ct='".$lig_ct->date_ct."' ORDER BY id_ct ASC;";
$res_mult=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res_mult)>0) {
	$lig_suiv=mysqli_fetch_object($res_mult);
	echo "
		 <a href='".$_SERVER['PHP_SELF']."?id_ct=".$lig_suiv->id_ct."&type_notice=".$type_notice."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_suiv->date_ct)."\"><img src='../images/icons/forward.png' class='icone16' alt='Séance suivante' /></a>";
}
else {
	$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$lig_ct->id_groupe."' AND id_ct!='".$id_ct."' AND date_ct>'".$lig_ct->date_ct."' ORDER BY date_ct ASC limit 1;";
	$res_suiv=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_suiv)>0) {
		$lig_suiv=mysqli_fetch_object($res_suiv);
		echo "
		 <a href='".$_SERVER['PHP_SELF']."?id_ct=".$lig_suiv->id_ct."&type_notice=".$type_notice."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_suiv->date_ct)."\"><img src='../images/icons/forward.png' class='icone16' alt='Séance suivante' /></a>";
	}
}
	echo "
	</div>
	<div style='clear:both;'></div>";

echo "
	<div class='fieldset_opacite50' style='padding:0.5em'>
		<div id='div_ouverture_toutes_images' style='float:right; width:16px; margin:0.3em;'></div>";

$tab_tag_type=get_tab_tag_cdt();
$tab_tag_notice=get_tab_tag_notice($lig_ct->id_ct, $type_notice);
if(isset($tab_tag_notice["indice"])) {
	echo "
		<div style='float:right; width:16px; margin:0.5em;'>";
	for($loop_tag=0;$loop_tag<count($tab_tag_notice["indice"]);$loop_tag++) {
		echo "
			<img src='$gepiPath/".$tab_tag_notice["indice"][$loop_tag]['drapeau']."' class='icone16' alt=\"".$tab_tag_notice["indice"][$loop_tag]['nom_tag']."\" title=\"Un ".$tab_tag_notice["indice"][$loop_tag]['nom_tag']." est indiqué.\" /> ";
	}
	echo "
		</div>";
}

//echo $lig_ct->contenu;
//echo preg_replace("#<img #i", "<img onclick=\"affiche_div_img(this.src)\" ", $lig_ct->contenu);
//echo preg_replace("#<img #i", "<img onclick=\"affiche_div_img(this.src, this.width, this.height)\" ", $lig_ct->contenu);

// Ajout via PHP des onclick sur les images
//echo preg_replace("#<img #i", "<img onclick=\"window.open(this.src, '_blank', 'toolbar=no,location=no,scrollbars=yes,resizable=yes,top=10,left=10,width=400,height=400');\" ", $lig_ct->contenu);


echo $lig_ct->contenu;

$adj=affiche_docs_joints($lig_ct->id_ct, $type_notice);
if($adj!='') {
	echo "
		<div style='border: 1px dashed black; margin-top:1em;'>
			".$adj.
		"</div>\n";
}

echo "
	</div>
</div>

<script type='text/javascript'>
	var temoin_images=0;

	// Ajout via Javascript des onclick sur les images

	img=document.getElementsByTagName('img');
	for(i=0;i<img.length;i++) {
		//id=img[i].getAttribute('id');
		src_img=img[i].getAttribute('src');
		if(src_img.substring(0, 10)!='../images/') {
			//width_img=img[i].getAttribute('width');
			//height_img=img[i].getAttribute('height');

			var att = document.createAttribute('onclick');

			//att.value = \"window.open(this.src, '_blank', 'toolbar=no,location=no,scrollbars=yes,resizable=yes,top=10,left=10,width='+this.width+',height='+this.height+'');\";
			//att.value = \"window.open(this.src, '_blank', 'toolbar=no,location=no,scrollbars=yes,resizable=yes,top=10,left=10,width='+Math.min(screen.availWidth, Math.max(this.width, 400))+',height='+Math.min(screen.availHeight, Math.max(this.height,400))+'');\";
			//att.value = \"window.open(this.src, '_blank', 'toolbar=no,location=no,scrollbars=yes,resizable=yes,top=10,left=10,width='+Math.min(screen.availWidth, this.width)+',height='+Math.min(screen.availHeight, this.height)+'');\";


			att.value = \"window.open(this.src, '_blank', 'toolbar=no,location=no,scrollbars=yes,resizable=yes,top=10,left=10,width='+Math.min(screen.availWidth, Math.max(this.width, 600))+',height='+Math.min(screen.availHeight, this.height)+'');\";

			//att.value = \"ouvre_et_positionne_fenetre(i, this.src, this.width, this.height);\";

			img[i].setAttributeNode(att);

			//alert(i);
			temoin_images++;
		}
	}

	var fen=new Array();
	function ouvre_et_positionne_fenetre(indice, img_src, img_width, img_height) {
		var x0=10;
		var y0=10;

		alert(indice+' '+img_src);

		var img=document.getElementsByTagName('img');
		for(i=0;i<img.length;i++) {
			//if(fen[i]!='NaN') {
			if(fen[i]) {
				var y=fen[i].scrollTop();
				alert('Fenetre '+i+' ordonnee '+y);
			}
		}

		fen[indice]=\"window.open(img_src, '_blank', 'toolbar=no,location=no,scrollbars=yes,resizable=yes,top='+y0+',left='+x0+',width='+Math.min(screen.availWidth, Math.max(img_width, 600))+',height='+Math.min(screen.availHeight, img_height)+'');\";
		eval(fen[indice]);
	}

	function ouvre_fenetres_toutes_images() {
		var x0=0;
		var x=x0;
		var maxwidth=0;
		var y=10;
		var img=document.getElementsByTagName('img');
		for(i=0;i<img.length;i++) {
		//for(i=0;i<6;i++) {
			img_src=img[i].src;

			//alert(img_src.substring(0, 10));
			// On récupère https://ww
			//if(img_src.substring(0, 10)!='../images/') {

			if((img_src.search('images/icons/home.png')==-1)&&
			(img_src.search('images/icons/cahier_textes.png')==-1)&&
			(img_src.search('images/icons/notices_CDT_compte_rendu.png')==-1)&&
			(img_src.search('images/icons/notices_CDT_travail.png')==-1)&&
			(img_src.search('images/icons/notices_CDT_privee.png')==-1)&&
			(img_src.search('images/icons/back.png')==-1)&&
			(img_src.search('images/edit16.png')==-1)&&
			(img_src.search('images/icons/forward.png')==-1)&&
			(img_src.search('images/icons/images.png')==-1)&&
			(img_src.search('images/alerte_entete.png')==-1)&&
			(img_src.search('images/bouton_continue.png')==-1)) {
				img_width=img[i].width;
				img_height=img[i].height;

				largeur=Math.min(screen.availWidth, Math.max(img_width, 600));
				hauteur=Math.min(screen.availHeight, img_height);

				if(y+hauteur+60>screen.availHeight) {
					if(x+maxwidth<screen.availHeight) {
						x=x+maxwidth;
						y=0;
						maxwidth=0;
					}
					else {
						break;
					}
				}

				if(x+largeur>maxwidth) {
					maxwidth=x+largeur;
				}

				//window.open(img_src, '_blank', 'toolbar=no,location=no,scrollbars=yes,resizable=yes,top='+y+',left='+x0+',width='+largeur+',height='+hauteur);
				window.open(img_src, '_blank', 'toolbar=no,location=no,scrollbars=yes,resizable=yes,top='+y+',left='+x+',width='+largeur+',height='+hauteur);

				//document.getElementById('div_ouverture_toutes_images').innerHTML=document.getElementById('div_ouverture_toutes_images').innerHTML+' '+img_src+'->'+y;

				//y+=hauteur;
				y=y+(hauteur+60);
				/*
				if(y>screen.availHeight) {
					if(x+maxwidth<screen.availHeight) {
						x=x+maxwidth;
						y=0;
					}
					else {
						break;
					}
				}
				*/
			}
		}
	}

	if(temoin_images>0) {
		document.getElementById('div_ouverture_toutes_images').innerHTML=\"<a href='#' onclick='ouvre_fenetres_toutes_images(); return false;' title='Ouvrir toutes (*) les images en une mosaïque de nouvelles fenêtres (* celles qui tiendront à l écran).'><img src='../images/icons/images.png' class='icone16' /></a>\";
	}

// A FAIRE: Permettre d'ouvrir des lots de fenêtres (les N premières, les suivantes,...)

//https://www.sitepoint.com/css3-background-size-property/

//https://javascript.info/popup-windows#accessing-a-popup

/*
// Ouvrir / provoque une déconnexion
let newWindow = open('/', 'example', 'width=300,height=300')
newWindow.focus();

newWindow.onload = function() {
	let html = '<div style=\"font-size:30px\">Welcome!</div>';
	newWindow.document.body.insertAdjacentHTML('afterbegin', html);
};
*/

</script>

<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
