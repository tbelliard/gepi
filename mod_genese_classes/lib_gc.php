<?php
/* $Id: lib_gc.php 7357 2011-07-01 16:35:26Z crob $ */

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


$tab_profil=array('GC','C','RAS','B','TB');
// Pour le moment les valeurs testées dans les scripts javascript et les couleurs associées sont en dur dans les pages.
// A modifier...
$chaine_couleur_profil="'red','orangered','gray','green','blue'";
$chaine_profil="'GC','C','RAS','B','TB'";

//$tab_profil=array($chaine_profil);
//$tab_couleur_profil=array($chaine_couleur_profil);
$tab_couleur_profil=array('red','orangered','gray','green','blue');


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

function necessaire_bull_simple() {
	echo "<div id='div_bull_simp' style='position: absolute; top: 220px; right: 20px; width: 700px; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>\n";
	
		echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: 700px; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_bull_simp')\">\n";
			echo "<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>\n";
			echo "<a href='#' onClick=\"cacher_div('div_bull_simp');return false;\">\n";
			echo "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />\n";
			echo "</a>\n";
			echo "</div>\n";
	
			echo "<div id='titre_entete_bull_simp'></div>\n";
		echo "</div>\n";
		
		echo "<div id='corps_bull_simp' class='infobulle_corps' style='color: #ffffff; cursor: move; font-weight: bold; padding: 0px; height: 15em; width: 700px; overflow: auto;'>";
		echo "</div>\n";
	
	echo "</div>\n";

	echo "<script type='text/javascript'>
	// <![CDATA[
	function affiche_bull_simp(login_eleve,id_classe,num_per1,num_per2) {
		document.getElementById('titre_entete_bull_simp').innerHTML='Bulletin simplifié de '+login_eleve+' période '+num_per1+' à '+num_per2;
		new Ajax.Updater($('corps_bull_simp'),'../saisie/ajax_edit_limite.php?choix_edit=2&login_eleve='+login_eleve+'&id_classe='+id_classe+'&periode1='+num_per1+'&periode2='+num_per2,{method: 'get'});
	}
	//]]>
</script>\n";
}

function image_sexe($sexe) {
	if(strtolower($sexe)=='f') {
		return "<img src='../images/symbole_femme16.png' width='16' height='16' title='F' />";
	}
	else {
		return "<img src='../images/symbole_homme16.png' width='16' height='16' title='M' />";
	}
}

/*
function tableau_eleves_req($id_req) {

	echo "<table class='boireaus' border='1' summary='Requête n°$id_req'>\n";

	//==========================================
	echo "<tr>\n";

	echo "<th>Elève</th>\n";

	echo "<th>Profil</th>\n";
	echo "<th>Niveau</th>\n";
	echo "<th>Absences</th>\n";
	echo "<th>Classe<br />actuelle</th>\n";
	//$fich_csv.="Elève;Classe actuelle;";
	$fich_csv.="Elève;Clas.act;";

	if(count($lv1)>0) {echo "<th>LV1</th>\n";$fich_csv.="LV1;";}
	if(count($lv2)>0) {echo "<th>LV2</th>\n";$fich_csv.="LV2;";}
	if(count($lv3)>0) {echo "<th>LV3</th>\n";$fich_csv.="LV3;";}
	if(count($autre_opt)>0) {echo "<th>Options</th>\n";$fich_csv.="Options;";}

	echo "<th rowspan='2'>Observations</th>\n";

	echo "</tr>\n";
	$fich_csv.="\n";

	//==========================================

	echo "<tr>\n";
	//echo "<th>Effectifs&nbsp;: <span id='eff_tot'>&nbsp;</span></th>\n";
	echo "<th>Eff.select&nbsp;: <span id='eff_select$loop'>...</span></th>\n";
	echo "<th id='eff_select_sexe$loop'>...</th>\n";

	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "<th>&nbsp;</th>\n";

	if(count($lv1)>0) {echo "<th>&nbsp;</th>\n";}
	if(count($lv2)>0) {echo "<th>&nbsp;</th>\n";}
	if(count($lv3)>0) {echo "<th>&nbsp;</th>\n";}
	if(count($autre_opt)>0) {echo "<th>&nbsp;</th>\n";}
	echo "</tr>\n";

	//==========================================
	$lignes_tab="";
	//==========================================

	$eff_tot_select=0;
	$eff_tot_select_M=0;
	$eff_tot_select_F=0;

	// Pour effectuer des moyennes, médiane,...
	$tab_moy_eleves=array();

	$chaine_id_classe="";
	//$cpt=0;
	// Boucle sur toutes les classes actuelles
	for($j=0;$j<count($id_classe_actuelle);$j++) {
		//$eff_tot_classe_M=0;
		//$eff_tot_classe_F=0;

		if($chaine_id_classe!="") {$chaine_id_classe.=",";}
		$chaine_id_classe.="'$id_classe_actuelle[$j]'";

		//==========================================
		//$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe_actuelle[$j]' ORDER BY e.nom,e.prenom;";
		$num_per2=-1;
		if(($id_classe_actuelle[$j]!='Red')&&($id_classe_actuelle[$j]!='Arriv')) {
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe_actuelle[$j]' ORDER BY e.nom,e.prenom;";

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
		//echo "<tr><td colspan='5'>$sql</tr></tr>\n";
		$res=mysql_query($sql);
		//$eff_tot_classe=mysql_num_rows($res);
		//$eff_tot+=$eff_tot_classe;
		//==========================================
	
		if(mysql_num_rows($res)>0) {
			while($lig=mysql_fetch_object($res)) {

				if(in_array($lig->login,$tab_ele)) {
					$tab_ele_toutes_requetes[]=$lig->login;

					$eff_tot_select++;
					if(strtoupper($lig->sexe)=='F') {$eff_tot_select_F++;} else {$eff_tot_select_M++;}

					//$num_eleve2_id_classe_actuelle[$j]=$cpt;

					//echo "<tr id='tr_eleve_$cpt' class='white_hover'>\n";
					echo "<tr id='tr_eleve_$cpt' class='white_hover'>\n";
					//onmouseover=\"this.style.backgroundColor='white'\" onmouseout=\"this.style.backgroundColor='$tmp_bgcolor'\"
					echo "<td>\n";
					echo "<a name='eleve$cpt'></a>\n";
					//if(file_exists("../photos/eleves/".$lig->elenoet.".jpg")) {
					if(nom_photo($lig->elenoet)) {
						//echo "<a href='#eleve$cpt' onmouseover=\"affiche_photo('".$lig->elenoet.".jpg','".addslashes(strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom)))."');afficher_div('div_photo','y',100,100);\" onmouseout=\"cacher_div('div_photo')\" onclick=\"return false;\">";
						echo "<a href='#eleve$cpt' onmouseover=\"affiche_photo('".nom_photo($lig->elenoet)."','".addslashes(strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom)))."');afficher_div('div_photo','y',100,100);\" onmouseout=\"cacher_div('div_photo')\" onclick=\"return false;\">";

						echo strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom));
						echo "</a>\n";
					}
					else {
						echo strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom));
					}
					echo "<input type='hidden' name='eleve[$cpt]' value='$lig->login' />\n";
					echo "</td>\n";
					$lignes_tab.=strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom)).";";

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
						for($n=0;$n<count($tmp_tab);$n++) {
							if($tmp_tab[$n]!="") {
								$tab_ele_opt[]=strtoupper($tmp_tab[$n]);
							}
						}
					}
					else {
						// On récupère les options de l'année écoulée (année qui se termine)
						$sql="SELECT * FROM j_eleves_groupes jeg, j_groupes_matieres jgm WHERE jeg.id_groupe=jgm.id_groupe AND jeg.login='$lig->login';";
						$res_opt=mysql_query($sql);
						if(mysql_num_rows($res_opt)>0) {
							while($lig_opt=mysql_fetch_object($res_opt)) {
								$tab_ele_opt[]=strtoupper($lig_opt->id_matiere);
							}
						}
					}
					//===================================
					// Profil...
					echo "<td>\n";
					for($m=0;$m<count($tab_profil);$m++) {if($profil==$tab_profil[$m]) {echo "<span style='color:".$tab_couleur_profil[$m].";'>";break;}}
					//echo $profil;
					echo "<span id='div_profil_$cpt' onclick=\"affiche_set_profil($cpt);changement();return false;\">$profil</span>\n";

					echo "</span>\n";
					echo "<input type='hidden' name='profil[$cpt]' id='profil_$cpt' value='$profil' />\n";
					echo "</td>\n";

					// Niveau...
					echo "<td>\n";
					if(($moy!="")&&(strlen(preg_replace("/[0-9\.,]/","",$moy))==0)) {
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
						echo "$moy\n";
						if($num_per2>0) {
							echo "</a>\n";
						}
						echo "</span>";
						echo "<input type='hidden' name='moy[$cpt]' id='moy_$cpt' value='$moy' />\n";

						$tab_moy_eleves[]=$moy;
					}
					else {
						echo "-\n";
						echo "<input type='hidden' name='moy[$cpt]' id='moy_$cpt' value='-' />\n";
					}
					echo "</td>\n";

					//===================================
					echo "<td>\n";
					echo colorise_abs($nb_absences,$non_justifie,$nb_retards);
					echo "<input type='hidden' name='nb_absences[$cpt]' id='nb_absences_$cpt' value='$nb_absences' />\n";
					echo "<input type='hidden' name='non_justifie[$cpt]' id='non_justifie_$cpt' value='$non_justifie' />\n";
					echo "<input type='hidden' name='nb_retards[$cpt]' id='nb_retards_$cpt' value='$nb_retards' />\n";
					echo "</td>\n";

					//===================================
					echo "<td>\n";
					//echo "<input type='hidden' name='classe_fut[$cpt]' id='classe_fut_".$i."_".$cpt."' value='$fut_classe' />\n";
					echo "<input type='hidden' name='ele_classe_fut[$cpt]' id='classe_fut_".$cpt."' value='$fut_classe' />\n";

					echo $classe_actuelle[$j];
					echo "</td>\n";
					$lignes_tab.=$classe_actuelle[$j].";";

					if(count($lv1)>0) {
						echo "<td>\n";
						for($i=0;$i<count($lv1);$i++) {
							if(in_array(strtoupper($lv1[$i]),$tab_ele_opt)) {
								echo $lv1[$i];

								echo "<input type='hidden' name='ele_lv1[$cpt]' id='lv1_".$cpt."' value='$lv1[$i]' />\n";

								$lignes_tab.=$lv1[$i].";";

							}
						}
						echo "</td>\n";
					}
		

					if(count($lv2)>0) {
						echo "<td>\n";
						for($i=0;$i<count($lv2);$i++) {
							if(in_array(strtoupper($lv2[$i]),$tab_ele_opt)) {
								echo $lv2[$i];
								echo "<input type='hidden' name='ele_lv2[$cpt]' id='lv2_".$cpt."' value='$lv2[$i]' />\n";

								$lignes_tab.=$lv2[$i].";";
							}
						}
						echo "</td>\n";
					}

					if(count($lv3)>0) {
						echo "<td>\n";
						for($i=0;$i<count($lv3);$i++) {
							if(in_array(strtoupper($lv3[$i]),$tab_ele_opt)) {
								echo $lv3[$i];
								echo "<input type='hidden' name='ele_lv3[$cpt]' id='lv3_".$cpt."' value='$lv3[$i]' />\n";

								$lignes_tab.=$lv3[$i].";";
							}
						}
						echo "</td>\n";
					}


					if(count($autre_opt)>0) {
						echo "<td>\n";
						$cpt_autre_opt=0;
						for($i=0;$i<count($autre_opt);$i++) {
							if(in_array(strtoupper($autre_opt[$i]),$tab_ele_opt)) {
								if($cpt_autre_opt>0) {echo " ";}
								echo $autre_opt[$i];

								$lignes_tab.=$autre_opt[$i].";";
								$cpt_autre_opt++;
							}
						}
					}

					echo "<td>\n";
					if(($fut_classe!='Red')&&($fut_classe!='Dep')&&($fut_classe!='')) {
						for($i=0;$i<count($tab_ele_opt);$i++) {
							if(in_array($tab_ele_opt[$i],$tab_opt_exclue["$fut_classe"])) {
								echo "<span style='color:red;'>ERREUR: L'option $tab_ele_opt[$i] est exclue en $fut_classe</span>";
							}
						}
					}
					echo "</td>\n";

					echo "</tr>\n";
					$lignes_tab.="\n";
					$cpt++;
				}
			}
		}
	}
	echo "</table>\n";
}
*/

function afficher_contraintes($tab_clas_fut) {
	global $projet;

	$retour="";

	$sql="SELECT * FROM gc_options_classes WHERE projet='$projet' ORDER BY classe_future,opt_exclue;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$cpt=0;
		$classe_prec="";
		$alt=1;
		$retour.="<table class='boireaus' border='1' summary='Contraintes saisies'>\n";
		while($lig=mysql_fetch_object($res)) {
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

	if($cpt==0) {
		$retour="";
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
	$res_opt=mysql_query($sql);
	if(mysql_num_rows($res_opt)>0) {
		$lig_opt=mysql_fetch_object($res_opt);

		$fut_classe=$lig_opt->classe_future;

		$tmp_tab=explode("|",$lig_opt->liste_opt);
		for($loop=0;$loop<count($tmp_tab);$loop++) {
			if($tmp_tab[$loop]!="") {
				$tab_ele_opt[]=strtoupper($tmp_tab[$loop]);
			}
		}
	}
	else {
		// On récupère les options de l'année écoulée
		$sql="SELECT * FROM j_eleves_groupes jeg, j_groupes_matieres jgm WHERE jeg.id_groupe=jgm.id_groupe AND jeg.login='ele_';";
		$res_opt=mysql_query($sql);
		if(mysql_num_rows($res_opt)>0) {
			while($lig_opt=mysql_fetch_object($res_opt)) {
				$tab_ele_opt[]=strtoupper($lig_opt->id_matiere);
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
			if(strtoupper($fut_classe)==strtoupper($classe_fut[$i])) {$retour.="checked ";}
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