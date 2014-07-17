<?php

// Passer à y pour afficher des infos de debugage
$debug_edt="n";

// Pouvoir régler l'opacité des couleurs de cours
$opacity_couleur=0.5;

$tab_couleur_edt[1]="blue";
$tab_couleur_edt[2]="fuchsia";
$tab_couleur_edt[3]="lime";
$tab_couleur_edt[4]="maroon";
$tab_couleur_edt[5]="purple";
$tab_couleur_edt[6]="red";
$tab_couleur_edt[7]="white";
$tab_couleur_edt[8]="yellow";
$tab_couleur_edt[9]="aqua";
$tab_couleur_edt[10]="grey";
$tab_couleur_edt[11]="green";
$tab_couleur_edt[12]="olive";
$tab_couleur_edt[13]="teal";
$tab_couleur_edt[14]="#799C13";
$tab_couleur_edt[15]="#4BA829";
$tab_couleur_edt[16]="#D4D600";
$tab_couleur_edt[17]="#FFEC00";
$tab_couleur_edt[18]="#FCC300";
$tab_couleur_edt[19]="#DBAA73";
$tab_couleur_edt[20]="#745A32";
$tab_couleur_edt[21]="#E95D0F";
$tab_couleur_edt[22]="#99141B";
$tab_couleur_edt[23]="#009EE0";
$tab_couleur_edt[24]="#C19CC4";
// Les couleurs sont dans l'EDT classique Gepi définies dans templates/DefaultEDT/css/style_edt.css avec des .cadreCouleur1, .cadreCouleur2,...

function get_days_from_week_number($num_semaine ,$annee) {
	$tab=array();

	for($num_jour=1; $num_jour<=7; $num_jour++) {
		$ts=strtotime($annee."W".$num_semaine.$num_jour);
		$tab['num_jour'][$num_jour]['timestamp']=$ts;
		$tab['num_jour'][$num_jour]['jjmmaaaa']=date('d/m/Y', $ts);
	}

	return $tab;
}

function get_nom_matiere_gepi_pour_matiere_ics($matiere_ics) {
	$retour=$matiere_ics;

	$sql="SELECT matiere FROM edt_ics_matiere WHERE matiere_ics='$matiere_ics';";
	//$html.="$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$retour=$lig->matiere;
	}

	return $retour;
}

function get_tab_matiere_gepi_pour_matiere_ics($matiere_ics) {
	$retour['matiere']=$matiere_ics;
	$retour['nom_complet']=$matiere_ics;

	$sql="SELECT m.* FROM matieres m, edt_ics_matiere eim WHERE eim.matiere_ics='$matiere_ics' AND eim.matiere=m.matiere;";
	//$html.="$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$retour['matiere']=$lig->matiere;
		$retour['nom_complet']=$lig->nom_complet;
	}

	return $retour;
}

function get_nom_prof_gepi_pour_prof_ics($prof_ics) {
	$retour=$prof_ics;

	$sql="SELECT u.civilite, u.nom, u.prenom FROM utilisateurs u, edt_ics_prof eip WHERE eip.prof_ics='$prof_ics' AND eip.login=u.login;";
	//$html.="$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$retour=$lig->nom;
	}

	return $retour;
}

function get_tab_prof_gepi_pour_prof_ics($prof_ics) {
	global $debug_edt;

	$retour['nom']=$prof_ics;
	$retour['designation']=$prof_ics;

	$sql="SELECT u.civilite, u.nom, u.prenom FROM utilisateurs u, edt_ics_prof eip WHERE eip.prof_ics='$prof_ics' AND eip.login_prof=u.login;";
	//$html.="$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$retour['nom']=$lig->nom;
		$retour['prenom']=$lig->prenom;
		$retour['civilite']=$lig->civilite;
		$retour['designation']=casse_mot($lig->civilite." ".$lig->nom." ".mb_substr($lig->prenom,0,1),'maj');
	}

	if($debug_edt=="y") {
		echo "<pre>";
		print_r($retour);
		echo "</pre>";
	}

	return $retour;
}

function get_couleur_edt_matiere($matiere) {
	global $tab_couleur_edt;
	//$retour="white";
	$retour="azure";

	$sql="SELECT valeur FROM edt_setting WHERE reglage='M_".$matiere."';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		//echo "\$tab_couleur_edt[".$lig->valeur."]=".$tab_couleur_edt[$lig->valeur]."<br />";
		if(isset($tab_couleur_edt[$lig->valeur])) {
			$retour=$tab_couleur_edt[$lig->valeur];
		}
	}
	return $retour;
}

function check_pas_de_collision($x1, $y1, $x2, $y2) {
	global $tab_coord_prises;
	$retour=true;

	for($loop=0;$loop<count($tab_coord_prises);$loop++) {
		$tab=explode(",", $tab_coord_prises[$loop]);
		$x1b=$tab[0];
		$y1b=$tab[1];
		$x2b=$tab[2];
		$y2b=$tab[3];

		if(($x1<=$x1b)&&($x2>$x1b)) {
			if(($y1b>=$y1)&&($y1b<=$y2)) {
				$retour=false;
			}
			elseif(($y1>=$y1b)&&($y1<=$y2b)) {
				$retour=false;
			}
		}
		elseif(($x1>=$x1b)&&($x1<$x2b)) {
			if(($y1b>=$y1)&&($y1b<=$y2)) {
				$retour=false;
			}
			elseif(($y1>=$y1b)&&($y1<=$y2b)) {
				$retour=false;
			}
		}
	}

	return $retour;
}

//function affiche_edt_ics($num_semaine_annee, $type_edt, $id_classe="", $login_prof="") {
function affiche_edt_ics($num_semaine_annee, $type_edt, $id_classe="", $login_prof="", $largeur_edt=800, $x0=50, $y0=60, $hauteur_une_heure=60, $hauteur_titre=10, $hauteur_entete=40) {
	global $debug_edt, $opacity_couleur;
	global $mode_infobulle;
	global $tab_coord_prises;

	$tab_coord_prises=array();

	if($opacity_couleur=="") {
		$opacity_couleur=0.5;
	}

	$html="";

	$tab=explode("|", $num_semaine_annee);

	$num_semaine=$tab[0];
	$annee=$tab[1];

	$_SESSION['edt_ics_num_semaine']=$num_semaine;
	$_SESSION['edt_ics_annee']=$annee;

	$jours=get_days_from_week_number($num_semaine, $annee);

	if($type_edt=="classe") {
		$_SESSION['edt_ics_id_classe']=$id_classe;

		$titre_edt="EDT de ".get_nom_classe($id_classe);
		$param_lien_edt="type_edt=classe&amp;id_classe=$id_classe";

		$sql_lien_semaine_precedente="SELECT num_semaine FROM edt_ics WHERE id_classe='$id_classe' AND num_semaine!='$num_semaine' AND date_debut<'".strftime("%Y-%m-%d %H:%M:%S", $jours['num_jour'][1]['timestamp'])."' ORDER BY date_debut DESC LIMIT 1;";

		$sql_lien_semaine_suivante="SELECT num_semaine FROM edt_ics WHERE id_classe='$id_classe' AND num_semaine!='$num_semaine' AND date_debut>'".strftime("%Y-%m-%d %H:%M:%S", $jours['num_jour'][7]['timestamp'])."' ORDER BY date_debut ASC LIMIT 1";

		$sql_cours_de_la_semaine="SELECT * FROM edt_ics WHERE id_classe='$id_classe' AND num_semaine='$num_semaine' ORDER BY date_debut;";
	}
	elseif($type_edt=="prof") {
		$_SESSION['edt_ics_login_prof']=$login_prof;

		// Désignation du prof à changer:
		$titre_edt="EDT de ".civ_nom_prenom($login_prof);
		$param_lien_edt="type_edt=prof&amp;login_prof=$login_prof";

		$sql_lien_semaine_precedente="SELECT num_semaine FROM edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof='$login_prof' AND num_semaine!='$num_semaine' AND date_debut<'".strftime("%Y-%m-%d %H:%M:%S", $jours['num_jour'][1]['timestamp'])."' ORDER BY date_debut DESC LIMIT 1;";

		$sql_lien_semaine_suivante="SELECT num_semaine FROM edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof='$login_prof' AND num_semaine!='$num_semaine' AND date_debut>'".strftime("%Y-%m-%d %H:%M:%S", $jours['num_jour'][7]['timestamp'])."' ORDER BY date_debut ASC LIMIT 1";

		$sql_cours_de_la_semaine="SELECT ei.* FROM edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof='$login_prof' AND num_semaine='$num_semaine' ORDER BY date_debut;";
	}

	//$html.="<h2>".$titre_edt."</h2>";

	$lien_semaine_prec="";
	//$html.="$sql_lien_semaine_precedente<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql_lien_semaine_precedente);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$annee_b=$annee;
		if($lig->num_semaine>$num_semaine) {
			$annee_b=$annee-1;
		}
		$lien_semaine_prec="<a href='".$_SERVER['PHP_SELF']."?$param_lien_edt&amp;num_semaine_annee=".$lig->num_semaine."|$annee_b&amp;mode=afficher_edt' title=\"Semaine précédente\"";
		if($mode_infobulle=="y") {
			$lien_semaine_prec.=" onclick=\"edt_semaine_suivante('$lig->num_semaine', '$annee_b', '$id_classe', '$login_prof'); return false;\"";
		}
		$lien_semaine_prec.="><img src='../images/arrow_left.png' class='icone16' alt='Semaine précédente'></a> - ";
	}

	$lien_semaine_suiv="";
	//$html.="$sql_lien_semaine_suivante<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql_lien_semaine_suivante);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$annee_b=$annee;
		if($lig->num_semaine<$num_semaine) {
			$annee_b=$annee+1;
		}
		$lien_semaine_suiv=" - <a href='".$_SERVER['PHP_SELF']."?$param_lien_edt&amp;num_semaine_annee=".$lig->num_semaine."|$annee_b&amp;mode=afficher_edt' title=\"Semaine suivante\"";
		if($mode_infobulle=="y") {
			$lien_semaine_suiv.=" onclick=\"edt_semaine_suivante('$lig->num_semaine', '$annee_b', '$id_classe', '$login_prof'); return false;\"";
		}
		$lien_semaine_suiv.="><img src='../images/arrow_right.png' class='icone16' alt='Semaine suivante'></a>";
	}

	//$html.="<h3>".$lien_semaine_prec."Semaine $num_semaine (".$jours['num_jour'][1]['jjmmaaaa']." - ".$jours['num_jour'][7]['jjmmaaaa'].")".$lien_semaine_suiv."</h3>";

	//$html.="$sql_cours_de_la_semaine<br />";
	$res_cours_de_la_semaine=mysqli_query($GLOBALS["mysqli"], $sql_cours_de_la_semaine);
	if(mysqli_num_rows($res_cours_de_la_semaine)==0) {
		$html.="<p>Aucun EDT n'est enregistré.</p>";
	}
	else {
		$chaine_alea=remplace_accents(rand(1, 1000000)."_".microtime(),"all");

		$html.="<script type='text/javascript'>
	//alert('plop');
	function edt_semaine_suivante(num_semaine, annee, id_classe, login_prof) {
		new Ajax.Updater($('div_edt_".$chaine_alea."'),'../edt/index.php?num_semaine_annee='+num_semaine+'|'+annee+'&id_classe='+id_classe+'&login_prof='+login_prof+'&type_edt=$type_edt&mode=afficher_edt_js',{method: 'get'});
	}
</script>

<div id='div_edt_".$chaine_alea."'>";

		if($debug_edt=="y") {
			echo "<pre>";
			print_r($jours);
			echo "</pre>";
		}


		//$tab_jour=array("lundi", "mardi", "mercredi", "jeudi", "vendredi");
		$tab_jour=array();
		$tmp_tab_jour=array("lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche");
		for($loop=0;$loop<count($tmp_tab_jour);$loop++) {
			$sql="SELECT DISTINCT jour_horaire_etablissement FROM horaires_etablissement WHERE jour_horaire_etablissement='".$tmp_tab_jour[$loop]."';";
			$test_jour=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test_jour)>0) {
				$tab_jour[]=$tmp_tab_jour[$loop];
			}
		}

		/*
		// 60px pour 1h
		$hauteur_une_heure=60;

		$hauteur_titre=10;
		$hauteur_entete=40;

		$x0=500;
		$y0=200;

		$largeur_edt=800;
		*/
		$largeur_jour=$largeur_edt/count($tab_jour);

		// Titre au-dessus de l'EDT
		$html.="<div style='position:absolute; top:".($y0-$hauteur_entete-$hauteur_titre)."px; left:".$x0."px; width:".$largeur_edt."px; height:".$hauteur_titre."px; text-align:center;'><h3>".$lien_semaine_prec.$titre_edt." - "."Semaine $num_semaine (".$jours['num_jour'][1]['jjmmaaaa']." - ".$jours['num_jour'][7]['jjmmaaaa'].")".$lien_semaine_suiv."</h3></div>";


		//=================================================================================
		$premiere_heure=8;
		//$derniere_heure=16.5;
		$derniere_heure=17;

		// Récupérer les horaires de début et de fin de journée dans le module EDT
		$sql="SELECT * FROM edt_creneaux ORDER BY heuredebut_definie_periode ASC LIMIT 1;";
		$res_premiere_heure=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_premiere_heure)>0) {
			$lig_premiere_heure=mysqli_fetch_object($res_premiere_heure);
			$tmp_tab=explode(":", $lig_premiere_heure->heuredebut_definie_periode);
			$premiere_heure=$tmp_tab[0]+$tmp_tab[1]/60;
		}

		$sql="SELECT * FROM edt_creneaux ORDER BY heuredebut_definie_periode DESC LIMIT 1;";
		$res_derniere_heure=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_derniere_heure)>0) {
			$lig_derniere_heure=mysqli_fetch_object($res_derniere_heure);
			$tmp_tab=explode(":", $lig_derniere_heure->heurefin_definie_periode);
			$derniere_heure=$tmp_tab[0]+$tmp_tab[1]/60;
		}
		//=================================================================================


		$tmp_tab=explode(".", $premiere_heure);
		$heure_debut_jour=$tmp_tab[0];
		$min_debut_jour=0;
		if(isset($tmp_tab[1])) {
			$min_debut_jour=floor($tmp_tab[1]*60);
		}
		$sec_debut_jour=0;

		$hauteur_jour=($derniere_heure-$premiere_heure)*$hauteur_une_heure;

		$y_max=$y0+$hauteur_entete+$hauteur_jour;

		$x_jour=array();
		for($i=0;$i<count($tab_jour);$i++) {
			$x_courant=$x0+$i*$largeur_jour;
			$x_jour[$i]=$x_courant;

			// Abscisse du jour au-dessus de l'entête (pour debug)
			if($debug_edt=="y") {
				$html.="<div style='position:absolute; top:".($y0-$hauteur_entete-2)."px; left:".$x_courant."px; width:".$largeur_jour."px; height:".$hauteur_entete."px;'>".$x_jour[$i]."</div>";
			}

			// Nom du jour en entête
			$html.="<div style='position:absolute; top:".$y0."px; left:".$x_courant."px; width:".$largeur_jour."px; height:".$hauteur_entete."px; border:1px solid black; text-align:center; background-color:silver;' title=\"".$jours['num_jour'][$i+1]['jjmmaaaa']."\">".ucfirst($tab_jour[$i])."<br /><span style='font-size:x-small'>".$jours['num_jour'][$i+1]['jjmmaaaa']."</span></div>";

			// Bande verticale de la journée
			$y_courant=$y0+$hauteur_entete;
			$html.="<div style='position:absolute; top:".$y_courant."px; left:".$x_courant."px; width:".$largeur_jour."px; height:".$hauteur_jour."px; border:1px solid black; background-color:white;z-index:1;'></div>";
			// Pour avoir les traits verticaux
			$html.="<div style='position:absolute; top:".$y_courant."px; left:".$x_courant."px; width:".$largeur_jour."px; height:".$hauteur_jour."px; border:1px solid black;z-index:4;'></div>";
		}

		/*
		$cpt=0;
		$y_courant=$y0;
		while($y_courant<$y_max) {
			$y_courant=$y0+$hauteur_entete+$cpt*$hauteur_une_heure;
			// Lignes horizontales des heures:
			//$html.="<div style='position:absolute; top:".$y_courant."px; left:".$x0."px; width:".$largeur_edt."px; height:".$hauteur_une_heure."px; border:1px solid grey; z-index:2;'></div>";

			// Affichage des heures sur la gauche:
			//$html.="<div style='position:absolute; top:".($y_courant)."px; left:".($x0-35)."px; width:20px; height:".$hauteur_une_heure."px; text-align:right;'>".($premiere_heure+$cpt)."H</div>";
			// Affichage des heures sur la droite:
			$html.="<div style='position:absolute; top:".($y_courant)."px; left:".($x0+$largeur_edt)."px; width:30px; height:".$hauteur_une_heure."px; text-align:center;'>".($premiere_heure+$cpt)."H</div>";
			// Si la première heure tombe sur une demi-heure, on va avoir des 8.5H, 9.5H,...

			$cpt++;
		}
		*/

		$heure_ronde_debut_jour=floor($premiere_heure);
		$heure_courante=$heure_ronde_debut_jour;
		$heure_ronde_debut_jour=floor($derniere_heure);
		$hauteur_texte=12; // A la louche
		$hauteur_demi_texte=ceil($hauteur_texte/2);
		while($heure_courante<$heure_ronde_debut_jour) {
			$y_courant=$y0+$hauteur_entete+($heure_courante-$premiere_heure)*$hauteur_une_heure-$hauteur_demi_texte;

			$html.="<div style='position:absolute; top:".($y_courant)."px; left:".($x0+$largeur_edt)."px; width:30px; height:".$hauteur_une_heure."px; text-align:center;'>".$heure_courante."H</div>";

			$heure_courante++;
		}

		// CRENEAUX
		$sql="SELECT * FROM edt_creneaux ORDER BY heuredebut_definie_periode;";
		$res_creneaux=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_creneaux)>0) {
			while($lig=mysqli_fetch_object($res_creneaux)) {
				$tab_h=explode(":", $lig->heuredebut_definie_periode);
				$c=$tab_h[0]+$tab_h[1]/60-$premiere_heure;
				$y1_courant=$y0+$hauteur_entete+round($c*$hauteur_une_heure);

				$tab_h=explode(":", $lig->heurefin_definie_periode);
				$c=$tab_h[0]+$tab_h[1]/60-$premiere_heure;
				$y2_courant=$y0+$hauteur_entete+round($c*$hauteur_une_heure);

				$hauteur_courante=$y2_courant-$y1_courant;

				$style_fond_creneau="background-color:white;";
				if($lig->type_creneaux=="pause") {
					$style_fond_creneau="background-color:silver;";
				}
				elseif($lig->type_creneaux=="repas") {
					$style_fond_creneau="background-color:grey;";
				}

				// Nom du créneau sur la gauche
				$html.="<div style='position:absolute; top:".($y1_courant)."px; left:".($x0-32)."px; width:30px; height:".$hauteur_courante."px; text-align:center; border:1px solid black; vertical-align:middle;".$style_fond_creneau."' title=\"Créneau $lig->nom_definie_periode\nDe $lig->heuredebut_definie_periode à $lig->heurefin_definie_periode.\"><div style='position:relative; width:2em; height:1em;'>".$lig->nom_definie_periode."</div></div>";

				// Bandes horizontales du créneau
				$html.="<div style='position:absolute; top:".($y1_courant)."px; left:".$x0."px; width:".$largeur_edt."px; height:".$hauteur_courante."px; border:1px solid silver; z-index:2;".$style_fond_creneau."'></div>";
			}
		}

		$marge_secu=6;

		$tab_cours=array();
		$tab_nom_classe=array();

		while($lig=mysqli_fetch_object($res_cours_de_la_semaine)) {
			if($debug_edt=="y") {
				echo "<pre style='border:1px solid red; margin:0.5em;'>";
				print_r($lig);
				echo "</pre>";
			}

			$ts_debut=mysql_date_to_unix_timestamp($lig->date_debut);
			$horaire_debut=strftime("%H:%M", $ts_debut);
			$ts_fin=mysql_date_to_unix_timestamp($lig->date_fin);
			$horaire_fin=strftime("%H:%M", $ts_fin);

			$num_jour=strftime("%u", $ts_debut)-1;

			$jour_debut_jour=strftime("%d", $ts_debut);
			$mois_debut_jour=strftime("%m", $ts_debut);
			$annee_debut_jour=strftime("%Y", $ts_debut);
			$ts_debut_jour=mktime($heure_debut_jour,$min_debut_jour,$sec_debut_jour,$mois_debut_jour,$jour_debut_jour,$annee_debut_jour);

			$duree_en_min=floor(($ts_fin-$ts_debut)/60);
			$hauteur_courante=floor($duree_en_min*$hauteur_une_heure/60);
			//$hauteur_courante=floor($duree_en_min*$hauteur_une_heure/60)-ceil($marge_secu/2);

			$duree_depuis_debut_journee=floor(($ts_debut-$ts_debut_jour)/3600);

			//$y_courant=$y0+$hauteur_entete+$duree_depuis_debut_journee*$hauteur_une_heure;
			$y_courant=$y0+$hauteur_entete+$duree_depuis_debut_journee*$hauteur_une_heure+ceil($marge_secu/2);

			if($debug_edt=="y") {
				$html.="\$ts_debut_jour=$ts_debut_jour<br />";
				$html.="\$ts_debut=$ts_debut<br />";
				$html.="\$duree_depuis_debut_journee=$duree_depuis_debut_journee<br />";
				$html.="y_courant=$y_courant<br />";
			}

			$cpt_courant=0;
			if(isset($tab_cours[$num_jour]['y'][$y_courant])) {
				$cpt_courant=count($tab_cours[$num_jour]['y'][$y_courant]);
			}
			$tab_cours[$num_jour]['y'][$y_courant][$cpt_courant]['hauteur']=$hauteur_courante;
			// A FAIRE : Stocker dans des tableaux les retours de fonction qui suivent pour ne pas faire plusieurs fois les mêmes appels
			$tab_cours[$num_jour]['y'][$y_courant][$cpt_courant]['matiere']=get_tab_matiere_gepi_pour_matiere_ics($lig->matiere_ics);
			$tab_cours[$num_jour]['y'][$y_courant][$cpt_courant]['prof']=get_tab_prof_gepi_pour_prof_ics($lig->prof_ics);
			$tab_cours[$num_jour]['y'][$y_courant][$cpt_courant]['salle']=$lig->salle_ics;
			$tab_cours[$num_jour]['y'][$y_courant][$cpt_courant]['id_cours']=$lig->id;
			$tab_cours[$num_jour]['y'][$y_courant][$cpt_courant]['id_classe']=$lig->id_classe;
			if(!array_key_exists($lig->id_classe, $tab_nom_classe)) {
				$tab_nom_classe[$lig->id_classe]=get_nom_classe($lig->id_classe);
			}
			$tab_cours[$num_jour]['y'][$y_courant][$cpt_courant]['classe']=$tab_nom_classe[$lig->id_classe];
			$tab_cours[$num_jour]['y'][$y_courant][$cpt_courant]['horaire_debut']=$horaire_debut;
			$tab_cours[$num_jour]['y'][$y_courant][$cpt_courant]['horaire_fin']=$horaire_fin;
		}

		if($debug_edt=="y") {
			echo "\$tab_cours<pre>";
			print_r($tab_cours);
			echo "</pre>";
		}

		if($type_edt=="classe") {

			$tab_collisions=array();

			$tab_collisions2=array();

			foreach($tab_cours as $num_jour => $tab) {

				/*
				if($num_jour==0) {
					echo "\$tab_cours[$num_jour]<pre>";
					print_r($tab_cours[$num_jour]);
					echo "</pre>";
				}
				*/

				foreach($tab['y'] as $y_courant => $tab2) {
					for($loop=0;$loop<count($tab2);$loop++) {
						$hauteur_courante=$tab2[$loop]['hauteur'];
						$y_courant_fin=$y_courant+$hauteur_courante;

						$id_cours_courant=$tab2[$loop]['id_cours'];

						foreach($tab_cours[$num_jour]['y'] as $y3=> $tab3_cours) {
							/*
							echo "y3=$y3<pre>";
							print_r($tab3_cours);
							echo "</pre>";
							*/

							for($loop3=0;$loop3<count($tab3_cours);$loop3++) {
								if($tab3_cours[$loop3]['id_cours']!=$id_cours_courant) {
									$y_test_debut=$y3;
									$y_test_fin=$y3+$tab3_cours[$loop3]['hauteur'];
									$id_cours_test=$tab3_cours[$loop3]['id_cours'];

									if(($y_test_debut>=$y_courant)&&($y_test_debut<$y_courant_fin)) {
										if((!isset($tab_collisions[$id_cours_courant]))||(!in_array($id_cours_test, $tab_collisions[$id_cours_courant]))) {
											$tab_collisions[$id_cours_courant][]=$id_cours_test;
										}
									}
									elseif(($y_courant>=$y_test_debut)&&($y_courant<$y_test_fin)) {
										if((!isset($tab_collisions[$id_cours_courant]))||(!in_array($id_cours_test, $tab_collisions[$id_cours_courant]))) {
											$tab_collisions[$id_cours_courant][]=$id_cours_test;
										}
									}

								}
							}

						}
					}
				}
			}

			/*
			//$tab_aff=array(7352,7367,7359);
			$tab_aff=array(7396,7402,7394);
			for($loop=0;$loop<count($tab_aff);$loop++) {
				echo "tab_collisions[".$tab_aff[$loop]."]=<pre>";
				print_r($tab_collisions[$tab_aff[$loop]]);
				echo "</pre>";
			}

			echo "<pre>";
			print_r($tab_collisions);
			echo "</pre>";
			*/

			$tab_coord_prises=array();
			foreach($tab_cours as $num_jour => $tab) {
				foreach($tab['y'] as $y_courant => $tab2) {
					for($loop=0;$loop<count($tab2);$loop++) {
						//$hauteur_courante=$tab2[$loop]['hauteur'];
						$hauteur_courante=$tab2[$loop]['hauteur']-$marge_secu;

						$title="".$tab2[$loop]['matiere']['nom_complet'];
						if($tab2[$loop]['prof']['designation']!="") {
							$title.=" avec ".$tab2[$loop]['prof']['designation'];
						}
						if($tab2[$loop]['salle']!="") {
							$title.=" en salle ".$tab2[$loop]['salle'];
						}
						$title.="\nDe ".$tab2[$loop]['horaire_debut']." à ".$tab2[$loop]['horaire_fin'].".";

						$id_cours_courant=$tab2[$loop]['id_cours'];

						$x_courant=$x_jour[$num_jour]+$marge_secu;
						$largeur_courante=$largeur_jour-2*$marge_secu;
						$text_color="";
						$font_size="";

						$style_font_size1=" style='font-size:normal;'";
						$style_font_size2=" style='font-size:x-small;'";
						$contenu_courant_ajout="";
						if(isset($tab_collisions[$id_cours_courant])) {

							$style_font_size1=" style='font-size:x-small;'";
							$style_font_size2=" style='font-size:xx-small;'";

							if($debug_edt=="y") {
								$contenu_courant_ajout.="<br />nb_col=".count($tab_collisions[$id_cours_courant]);
							}

							// Compter les collisions effectives
							$nb=count($tab_collisions[$id_cours_courant]);
							foreach($tab_collisions[$id_cours_courant] as $id_cours_test) {
								$nb=min($nb,count($tab_collisions[$id_cours_test]));
							}
							if($debug_edt=="y") {
								$contenu_courant_ajout.="<br />nb_reel=".$nb;
							}
							// Largeur du div de ce cours
							$largeur_courante=floor($largeur_jour/($nb+1))-2*$marge_secu;


							//$font_size="font-size:x-small;";
							//$font_size="font-size:smaller;";
							$tmp_tab=array();
							$tmp_tab[]=$id_cours_courant;
							//foreach($tab_collisions as $tmp_current_id_cours => $tmp_current_id_cours_collision) {
							foreach($tab_collisions[$id_cours_courant] as $tmp_current_id_cours_collision) {
								$tmp_tab[]=$tmp_current_id_cours_collision;
							}
							sort($tmp_tab);

							$chaine="";
							for($loop2=0;$loop2<count($tmp_tab);$loop2++) {
								if($chaine!="") {
									$chaine.="|";
								}
								$chaine.=$tmp_tab[$loop2];
							}

							while($x_courant<$x_jour[$num_jour]+$largeur_jour) {
								if(check_pas_de_collision($x_courant,$y_courant,$x_courant+$largeur_courante,$y_courant+$hauteur_courante)) {
									$text_color="";
									break;
								}
								else {
									//$x_courant+=$largeur_courante;
									//$x_courant+=floor($largeur_jour/($nb+1))+$marge_secu;
									$x_courant+=floor($largeur_jour/($nb+1));
									$text_color="color:red;";
								}
							}

							if($text_color!="") {
								$x_courant=$x_jour[$num_jour];
							}

							$tab_coord_prises[]=$x_courant.",".$y_courant.",".($x_courant+$largeur_courante).",".($y_courant+$hauteur_courante);
							if($debug_edt=="y") {
								$title.="\nCoordonnées : ".$x_courant.",".$y_courant.",".($x_courant+$largeur_courante).",".($y_courant+$hauteur_courante);
							}
						}

						$contenu_courant="<span title=\"$title\"$style_font_size1>".$tab2[$loop]['matiere']['matiere']."</span>";

						// Ne pas inclure ce qui suit pour l'emploi du temps du prof
						if($type_edt!="prof") {
							$contenu_courant.="<br /><span$style_font_size2 title=\"".$tab2[$loop]['prof']['designation']."\">".$tab2[$loop]['prof']['nom']."</span>";
						}
						else {
							$contenu_courant.="<br /><span$style_font_size2 title=\"".$tab2[$loop]['classe']."\">".$tab2[$loop]['classe']."</span>";
						}
						// Ne pas inclure ce qui suit pour l'emploi du temps d'une salle
						$contenu_courant.="<br /><span$style_font_size2 title=\"Salle ".$tab2[$loop]['salle']."\">".$tab2[$loop]['salle']."</span>";

						if($debug_edt=="y") {
							$contenu_courant.="<br />id_cours=".$id_cours_courant;
						}

						$contenu_courant.=$contenu_courant_ajout;

						// Fond blanc pour masquer les lignes d'heures
						$html.="<div style='position:absolute; top:".$y_courant."px; left:".$x_courant."px; width:".$largeur_courante."px; height:".$hauteur_courante."px; background-color:white; z-index:18; '></div>";
						// Cadre de couleur avec une opacité réglable
						if(!isset($tab_couleur_matiere[$tab2[$loop]['matiere']['matiere']])) {
							$tab_couleur_matiere[$tab2[$loop]['matiere']['matiere']]=get_couleur_edt_matiere($tab2[$loop]['matiere']['matiere']);
						}
						$couleur_courante=$tab_couleur_matiere[$tab2[$loop]['matiere']['matiere']];
						$html.="<div style='position:absolute; top:".$y_courant."px; left:".$x_courant."px; width:".$largeur_courante."px; height:".$hauteur_courante."px; border:1px solid black; background-color:".$couleur_courante."; opacity:$opacity_couleur; z-index:19; text-align:center;".$text_color.$font_size."' title='$title'></div>";
						// Cadre du contenu de la cellule
						$html.="<div style='position:absolute; top:".$y_courant."px; left:".$x_courant."px; width:".$largeur_courante."px; height:".$hauteur_courante."px; border:1px solid black; z-index:20; text-align:center; overflow:hidden; ".$text_color.$font_size."' title='$title'>".$contenu_courant."</div>";
					}

				}
			}
		}
		else {
			// EDT PROF

			foreach($tab_cours as $num_jour => $tab) {
				foreach($tab['y'] as $y_courant => $tab2) {
					/*
					if($num_jour==2) {
						echo "\$y_courant=$y_courant \$tab2<pre>";
						print_r($tab2);
						echo "</pre>";
					}
					*/
					$liste_classe="";
					for($loop=0;$loop<count($tab2);$loop++) {
						if($liste_classe!="") {
							$liste_classe.=", ";
						}
						$liste_classe.=$tab2[$loop]['classe'];
					}

					//for($loop=0;$loop<count($tab2);$loop++) {
					$loop=0;

						//$hauteur_courante=$tab2[$loop]['hauteur'];
						$hauteur_courante=$tab2[$loop]['hauteur']-$marge_secu;

						$title="Enseignement de ".$tab2[$loop]['matiere']['nom_complet']."\n";
						if($liste_classe!="") {
							if(count($tab2)>1) {
								$title.="Classe(s) : ".$liste_classe."\n";
							}
							else {
								$title.="Classe : ".$liste_classe."\n";
							}
						}
						if($tab2[$loop]['prof']['designation']!="") {
							$title.="Professeur : ".$tab2[$loop]['prof']['designation']."\n";
						}
						if($tab2[$loop]['salle']!="") {
							$title.="En salle ".$tab2[$loop]['salle'];
						}
						$title.="\nDe ".$tab2[$loop]['horaire_debut']." à ".$tab2[$loop]['horaire_fin'].".";

						$id_cours_courant=$tab2[$loop]['id_cours'];

						$x_courant=$x_jour[$num_jour]+$marge_secu;
						$largeur_courante=$largeur_jour-2*$marge_secu;
						$text_color="";
						$font_size="";

						$style_font_size1=" style='font-size:normal;'";
						$style_font_size2=" style='font-size:x-small;'";

						$contenu_courant_ajout="";

						$contenu_courant="<span title=\"$title\"$style_font_size1>".$tab2[$loop]['matiere']['matiere']."</span>";

						// Ne pas inclure ce qui suit pour l'emploi du temps du prof
						if($type_edt!="prof") {
							$contenu_courant.="<br /><span$style_font_size2 title=\"".$tab2[$loop]['prof']['designation']."\">".$tab2[$loop]['prof']['nom']."</span>";
						}
						else {
							$contenu_courant.="<br /><span$style_font_size2 title=\"".$liste_classe."\">".$liste_classe."</span>";
						}
						// Ne pas inclure ce qui suit pour l'emploi du temps d'une salle
						$contenu_courant.="<br /><span$style_font_size2 title=\"Salle ".$tab2[$loop]['salle']."\">".$tab2[$loop]['salle']."</span>";

						if($debug_edt=="y") {
							$contenu_courant.="<br />id_cours=".$id_cours_courant;
						}

						$contenu_courant.=$contenu_courant_ajout;

						// Fond blanc pour masquer les lignes d'heures/créneaux
						$html.="<div style='position:absolute; top:".$y_courant."px; left:".$x_courant."px; width:".$largeur_courante."px; height:".$hauteur_courante."px; background-color:white; z-index:18; '></div>";
						// Cadre de couleur avec une opacité réglable
						if(!isset($tab_couleur_matiere[$tab2[$loop]['matiere']['matiere']])) {
							$tab_couleur_matiere[$tab2[$loop]['matiere']['matiere']]=get_couleur_edt_matiere($tab2[$loop]['matiere']['matiere']);
						}
						$couleur_courante=$tab_couleur_matiere[$tab2[$loop]['matiere']['matiere']];
						$html.="<div style='position:absolute; top:".$y_courant."px; left:".$x_courant."px; width:".$largeur_courante."px; height:".$hauteur_courante."px; border:1px solid black; background-color:".$couleur_courante."; opacity:$opacity_couleur; z-index:19; text-align:center;".$text_color.$font_size."' title='$title'></div>";
						// Cadre du contenu de la cellule
						$html.="<div style='position:absolute; top:".$y_courant."px; left:".$x_courant."px; width:".$largeur_courante."px; height:".$hauteur_courante."px; border:1px solid black; z-index:20; text-align:center; overflow:hidden; ".$text_color.$font_size."' title='$title'>".$contenu_courant."</div>";
					//}

				}
			}


		}




		//=================================================================================
		// Ancien essai, affiché plus bas pour faciliter le debug de l'affichage au-dessus
		if($debug_edt=="y") {
			foreach($tab_cours as $num_jour => $tab) {
				foreach($tab['y'] as $y_courant => $tab2) {
					$y_courant=$y_courant+$hauteur_jour+100;

					if(count($tab2)==1) {
						$hauteur_courante=$tab2[0]['hauteur'];
						$contenu_courant=$tab2[0]['matiere']['matiere'];
						//$contenu_courant.="<br />".$hauteur_courante;

						$html.="<div style='position:absolute; top:".$y_courant."px; left:".$x_jour[$num_jour]."px; width:".$largeur_jour."px; height:".$hauteur_courante."px; border:1px solid black; background-color:orange; z-index:1000; opacity:0.5;text-align:center;'>".$contenu_courant."</div>";
					}
					else {
						for($loop=0;$loop<count($tab2);$loop++) {
							$hauteur_courante=$tab2[$loop]['hauteur'];
							$contenu_courant=$tab2[$loop]['matiere']['matiere'];
							//$contenu_courant.="<br />".$hauteur_courante;

							$largeur_courante=floor($largeur_jour/count($tab2));
							$x_courant=$x_jour[$num_jour]+$loop*$largeur_courante;

						$html.="<div style='position:absolute; top:".$y_courant."px; left:".$x_courant."px; width:".$largeur_courante."px; height:".$hauteur_courante."px; border:1px solid black; background-color:orange; z-index:1000; opacity:0.5; font-size:x-small;text-align:center;'>".$contenu_courant."</div>";
						}
					}
				}
			}
		}
		//=================================================================================


		$html.="</div>";
	}

	return $html;
}

?>
