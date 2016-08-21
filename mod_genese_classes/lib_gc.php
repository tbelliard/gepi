<?php

$tabcouleur=Array("aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen");


//=====================================
/*
$liste=array('palegoldenrod',
'mistyrose',
'palegreen',
'moccasin',
'lightsteelblue',
'darkseagreen',
'olive',
'mintcream',
'lightgray',
'gray');
*/
$liste=array('palegoldenrod',
'mistyrose',
'palegreen',
'moccasin',
'lightskyblue',
'darkseagreen',
'olive',
'mintcream',
'lightgray',
'gray');

if(isset($classe_fut)) {
	$chaine_couleur_classe_fut="'$liste[0]'";
	for($i=1;$i<count($classe_fut)-3;$i++) {
		if(isset($liste[$i])) {
			$chaine_couleur_classe_fut.=",'$liste[$i]'";
		}
		else {
			$chaine_couleur_classe_fut.=",'$tabcouleur[$i]'";
		}
	}
	$chaine_couleur_classe_fut.=",'lightgray','gray','white'";

	//echo "\$chaine_couleur_classe_fut=$chaine_couleur_classe_fut<br />";
	$tab_couleur_classe_fut=explode(",", preg_replace("/[^A-Za-z0-9,]/", "", $chaine_couleur_classe_fut));
}
//=====================================

//=====================================
$chaine_couleur_lv1="'palegoldenrod',
'mintcream',
'mistyrose',
'palegreen',
'moccasin',
'lightsteelblue',
'darkseagreen',
'olive',
'lightgray',
'gray'";
//=====================================

$chaine_couleur_lv2="'lightgreen','lightpink','lightblue','gold','lightgray','gray','olive'";
$chaine_couleur_lv3="'purple','greenyellow','violet','chartreuse','lightgray','gray','olive'";

// Les variables $chaine_couleur_* sont utilisées pour initialiser des tableaux javascript.

$tab_sexe=array('M','F');

$tab_profil=array('GC','C','RAS','B','TB');
$tab_profil_traduction=array('Gros Cas','Cas','Rien à signaler','Bien','Très Bien');
// Pour le moment les valeurs testées dans les scripts javascript et les couleurs associées sont en dur dans les pages.
// A modifier...
//$chaine_couleur_profil="'red','orangered','gray','green','blue'";
//$chaine_profil="'GC','C','RAS','B','TB'";
$chaine_couleur_profil="";
$chaine_profil="";

//$tab_profil=array($chaine_profil);
//$tab_couleur_profil=array($chaine_couleur_profil);
$tab_couleur_profil=array('red','orangered','gray','green','blue');
$tab_couleur_profil_assoc=array();
for($loop=0;$loop<count($tab_profil);$loop++) {
	$tab_couleur_profil_assoc[$tab_profil[$loop]]=$tab_couleur_profil[$loop];

	if($chaine_couleur_profil!="") {
		$chaine_couleur_profil.=",";
	}
	$chaine_couleur_profil.="'".$tab_couleur_profil[$loop]."'";

	if($chaine_profil!="") {
		$chaine_profil.=",";
	}
	$chaine_profil.="'".$tab_profil[$loop]."'";
}


function colorise_abs($abs,$nj,$ret,$mode="echo") {
	$retour="";

	if($abs<=10) {
		$retour.="<span style='color:green;'>";
	}
	elseif(($abs>10)&&($abs<=30)) {
		$retour.="<span style='color:orange;'>";
	}
	elseif(($abs>30)&&($abs<=50)) {
		$retour.="<span style='color:orangered;'>";
	}
	else {
		$retour.="<span style='color:red;'>";
	}
	$retour.=$abs;
	$retour.="</span>";

	$retour.="/";

	if(($nj==0)||($abs==0)) {
		$retour.="<span style='color:green;'>";
	}
	else{
		$p=100*$nj/$abs;
		if($p<=20) {
			$retour.="<span style='color:orange;'>";
		}
		elseif(($p>20)&&($p<=50)) {
			$retour.="<span style='color:orangered;'>";
		}
		else {
			$retour.="<span style='color:red;'>";
		}
	}
	$retour.=$nj;
	$retour.="</span>";

	$retour.="/";

	if($ret<=10) {
		$retour.="<span style='color:green;'>";
	}
	elseif(($ret>10)&&($ret<=30)) {
		$retour.="<span style='color:orange;'>";
	}
	elseif(($ret>30)&&($ret<=50)) {
		$retour.="<span style='color:orangered;'>";
	}
	else {
		$retour.="<span style='color:red;'>";
	}
	$retour.=$ret;
	$retour.="</span>";

	if($mode=='echo') {
		echo $retour;
	}
	else {
		return $retour;
	}
}

function image_sexe($sexe) {
	if(mb_strtolower($sexe)=='f') {
		return "<img src='../images/symbole_femme16.png' width='16' height='16' title='F' />";
	}
	else {
		return "<img src='../images/symbole_homme16.png' width='16' height='16' title='M' />";
	}
}



function afficher_contraintes($tab_clas_fut) {
	global $projet;

	$retour="";

	$sql="SELECT * FROM gc_options_classes WHERE projet='$projet' ORDER BY classe_future,opt_exclue;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$cpt=0;
		$classe_prec="";
		$alt=1;
		$retour.="<table class='boireaus' border='1' summary='Contraintes saisies'>\n";
		while($lig=mysqli_fetch_object($res)) {
			if(in_array($lig->classe_future,$tab_clas_fut)) {
				if($lig->classe_future!=$classe_prec) {
					if($cpt>0) {$retour.="</td></tr>\n";}
					$alt=$alt*(-1);
					$retour.="<tr class='lig$alt'><td style='text-align:left;'>\n";
				}
				$retour.="<b>$lig->classe_future</b>&nbsp;: Pas de $lig->opt_exclue<br />\n";
				$classe_prec=$lig->classe_future;
				$cpt++;
			}
		}
		$retour.="</td></tr>\n";
		$retour.="</table>\n";
	}

	return $retour;
}

/*
function tableau_eleves_req($id_aff, $id_req) {
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

	//$tab_ele=array();
	$sql_ele.=";";
	//echo "$sql_ele<br />\n";
	$cpt=0;
	$res_ele=mysql_query($sql_ele);
	while ($lig_ele=mysql_fetch_object($res_ele)) {
		//$tab_ele[]=$lig_ele->login;

		$retour.=get_nom_prenom_eleve($login_ele,'avec_classe')."<br />";

		$cpt++;
	}
}
*/

function ligne_entete_classe_future() {
	global $projet, $classe_fut;

	$retour="";

	for($i=0;$i<count($classe_fut);$i++) {
		$retour.="<th>\n";
		$retour.="$classe_fut[$i]";
		$retour.="</th>\n";
	}

	return $retour;
}

function ligne_choix_classe_future($ele_login) {
	global $projet, $classe_fut, $tab_opt_exclue;

	$retour="";

	$fut_classe="";
	$tab_ele_opt=array();
	$sql="SELECT * FROM gc_eleves_options WHERE projet='$projet' AND login='$ele_login';";
	$res_opt=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_opt)>0) {
		$lig_opt=mysqli_fetch_object($res_opt);

		$fut_classe=$lig_opt->classe_future;

		$tmp_tab=explode("|",$lig_opt->liste_opt);
		for($loop=0;$loop<count($tmp_tab);$loop++) {
			if($tmp_tab[$loop]!="") {
				$tab_ele_opt[]=mb_strtoupper($tmp_tab[$loop]);
			}
		}
	}
	else {
		// On récupère les options de l'année écoulée
		$sql="SELECT * FROM j_eleves_groupes jeg, j_groupes_matieres jgm WHERE jeg.id_groupe=jgm.id_groupe AND jeg.login='ele_';";
		$res_opt=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_opt)>0) {
			while($lig_opt=mysqli_fetch_object($res_opt)) {
				$tab_ele_opt[]=mb_strtoupper($lig_opt->id_matiere);
			}
		}
	}

	for($i=0;$i<count($classe_fut);$i++) {
		$retour.="<td>\n";

		$coche_possible='y';
		if(($classe_fut[$i]!='Red')&&($classe_fut[$i]!='Dep')&&($classe_fut[$i]!='')) {
			for($loop=0;$loop<count($tab_ele_opt);$loop++) {
				if(in_array($tab_ele_opt[$loop],$tab_opt_exclue["$classe_fut[$i]"])) {
					$coche_possible='n';
					break;
				}
			}
		}

		if($coche_possible=='y') {
			//$retour.="<input type='radio' name='classe_fut[$cpt]' id='classe_fut_".$i."_".$cpt."' value='$classe_fut[$i]' ";
			$retour.="<input type='radio' name='classe_fut' id='classe_fut_choisie' value='$classe_fut[$i]' ";
			if(mb_strtoupper($fut_classe)==mb_strtoupper($classe_fut[$i])) {$retour.="checked ";}
			//alert('bip');
			//$retour.="onchange=\"calcule_effectif('classe_fut',".count($classe_fut).");colorise_ligne('classe_fut',$cpt,$i);changement();\" ";
			//$retour.="title=\"$lig->login/$classe_fut[$i]\" ";
			//$retour.="onmouseover=\"test_aff_classe3('".$lig->login."','".$classe_fut[$i]."');\" onmouseout=\"cacher_div('div_test_aff_classe2');\" ";
			$retour.="/>\n";
		}
		else {
			$retour.="_";
		}

		$retour.="</td>\n";
	}

	return $retour;
}
?>
