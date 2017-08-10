<?php
@set_time_limit(0);

/*
if((isset($_POST['deposer_message']))&&
(isset($_POST['message']))) {
	$traite_anti_inject = 'no';
}
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

$sql="SELECT 1=1 FROM droits WHERE id='/edt/import_vacances_ics.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/edt/import_vacances_ics.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='EDT : Import des vacances depuis l ICAL officiel EducNat',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$zone=isset($_POST['zone']) ? $_POST['zone'] : (isset($_GET['zone']) ? $_GET['zone'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

$sql="CREATE TABLE IF NOT EXISTS calendrier_vacances (
id int(11) NOT NULL auto_increment,
nom_calendrier varchar(100) NOT NULL default '',
debut_calendrier_ts varchar(11) NOT NULL,
fin_calendrier_ts varchar(11) NOT NULL,
jourdebut_calendrier date NOT NULL default '0000-00-00',
heuredebut_calendrier time NOT NULL default '00:00:00',
jourfin_calendrier date NOT NULL default '0000-00-00',
heurefin_calendrier time NOT NULL default '00:00:00',
PRIMARY KEY (id)) 
ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

if(isset($_POST['enregistrer_vacances'])) {
	check_token();

	$msg="";

	$sql="TRUNCATE calendrier_vacances;";
	//echo "$sql<br />";
	$menage=mysqli_query($GLOBALS["mysqli"], $sql);

	$edt_vacances=array();
	$edt_vacances2=array();
	$sql="SELECT * FROM edt_calendrier WHERE etabvacances_calendrier='1';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$cpt=0;
		while($lig=mysqli_fetch_assoc($res)) {
			$edt_vacances[$cpt]=$lig;
			$nom_corrige=remplace_accents(mb_strtolower(stripslashes($lig['nom_calendrier'])), "all");
			$edt_vacances2[$nom_corrige]=$cpt;
			$cpt++;
		}
	}

	$sql="SELECT id FROM classes ORDER BY classe;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$chaine_id_classe="";
		while($lig=mysqli_fetch_object($res)) {
			if($chaine_id_classe!="") {
				$chaine_id_classe.=";";
			}
			$chaine_id_classe.=$lig->id;
		}
	}

	$nb_reg=0;
	$date_vacances=isset($_POST['date_vacances']) ? $_POST['date_vacances'] : array();

	if(count($date_vacances)>0) {
		$sql="SELECT * FROM infos_actions WHERE titre='Dates des vacances et jours fériés';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				del_info_action($lig->id);
			}
		}
	}

	for($loop=0;$loop<count($date_vacances);$loop++) {
		$tab=explode("|", $date_vacances[$loop]);
		if(!isset($tab[2])) {
			$msg.="Date n°$loop invalide&nbsp;: ".$date_vacances[$loop]."<br />";
		}
		else {
			$nom_corrige=remplace_accents(mb_strtolower(stripslashes($tab[0])), "all");
			if(array_key_exists($nom_corrige, $edt_vacances2)) {
				//echo "<p>".$date_vacances[$loop]." trouvé&nbsp;:</p>";
				/*
				echo "<pre>";
				print_r($edt_vacances[$edt_vacances2[$nom_corrige]]);
				echo "</pre>";
				*/
				$sql="DELETE FROM edt_calendrier WHERE id_calendrier='".$edt_vacances[$edt_vacances2[$nom_corrige]]['id_calendrier']."';";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
			}

			$tab_date=explode("/", $tab[1]);
			$debut_calendrier_ts=mktime(0,0,0,$tab_date[1], $tab_date[0], $tab_date[2]);
			$jourdebut_calendrier=strftime("%Y-%m-%d", $debut_calendrier_ts);
			$heuredebut_calendrier=strftime("%H:%M:%S", $debut_calendrier_ts);
			//echo "<p style='margin-top:1em;'>Du ".strftime("%A %d/%m/%Y à %H:%M:%S", $debut_calendrier_ts)."";

			$tab_date=explode("/", $tab[2]);
			$fin_calendrier_ts=mktime(0,0,0,$tab_date[1], $tab_date[0], $tab_date[2])-60;
			$jourfin_calendrier=strftime("%Y-%m-%d", $fin_calendrier_ts);
			$heurefin_calendrier=strftime("%H:%M:%S", $fin_calendrier_ts);
			//echo " au ".strftime("%A %d/%m/%Y à %H:%M:%S", $fin_calendrier_ts)."<br />";

			$sql="INSERT INTO edt_calendrier SET classe_concerne_calendrier='".$chaine_id_classe."', 
									nom_calendrier='".$tab[0]."',
									debut_calendrier_ts='".$debut_calendrier_ts."',
									fin_calendrier_ts='".$fin_calendrier_ts."',
									jourdebut_calendrier='".$jourdebut_calendrier."',
									heuredebut_calendrier='".$heuredebut_calendrier."',
									jourfin_calendrier='".$jourfin_calendrier."',
									heurefin_calendrier='".$heurefin_calendrier."',
									numero_periode='0',
									etabferme_calendrier='2',
									etabvacances_calendrier='1';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if($res) {
				$nb_reg++;

				$sql="INSERT INTO calendrier_vacances SET nom_calendrier='".$tab[0]."',
									debut_calendrier_ts='".$debut_calendrier_ts."',
									fin_calendrier_ts='".$fin_calendrier_ts."',
									jourdebut_calendrier='".$jourdebut_calendrier."',
									heuredebut_calendrier='".$heuredebut_calendrier."',
									jourfin_calendrier='".$jourfin_calendrier."',
									heurefin_calendrier='".$heurefin_calendrier."';";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
			}
			else {
				$msg.="Erreur lors de l'enregistrement pour ".$tab[0]."<br />";
			}
		}
	}

	$msg.=$nb_reg." enregistrements effectués.<br />";
}

//**************** EN-TETE *****************
$titre_page = "Import vacances";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'>
	<a href='../edt_organisation/edt_calendrier.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">
		<img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour
	</a>";

$begin_bookings=getSettingValue("begin_bookings");
$end_bookings=getSettingValue("end_bookings");
if(($begin_bookings=="")||($end_bookings=="")) {
	echo "
</p>
<p style='color:red'>Les dates de début et de fin d'année scolaire ne sont pas.<br />Voir <a href='../gestion/param_gen.php'>Gestion générale/Configuration générale</a></p>";
	require("../lib/footer.inc.php");
	die();
}

if(!isset($zone)) {
	echo "
</p>
<h2>Choix de la zone</h2>
<p>De quelle zone souhaitez-vous télécharger les dates de vacances&nbsp;?</p>
<ul>
	<li><p><a href='".$_SERVER['PHP_SELF']."?zone=A&amp;mode=telech'>Zone A</a>&nbsp;: Besançon, Bordeaux, Clermont-Ferrand, Dijon, Grenoble, Limoges, Lyon, Poitiers</p></li>
	<li><p><a href='".$_SERVER['PHP_SELF']."?zone=B&amp;mode=telech'>Zone B</a>&nbsp;: Aix-Marseille, Amiens, Caen, Lille, Nancy-Metz, Nantes, Nice, Orléans-Tours, Reims, Rennes, Rouen, Strasbourg</p></li>
	<li><p><a href='".$_SERVER['PHP_SELF']."?zone=C&amp;mode=telech'>Zone C</a>&nbsp;: Créteil, Montpellier, Paris, Toulouse, Versailles</p></li>
</ul>";

}
elseif($mode=="telech") {
	echo "
	 | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre zone</a>
</p>
<h2>Téléchargement du fichier pour la zone $zone<br />
	<span style='font-size:xx-small'>(<a href='http://cache.media.education.gouv.fr/ics/Calendrier_Scolaire_Zone_".mb_strtoupper($zone).".ics' target='_blank'>http://cache.media.education.gouv.fr/ics/Calendrier_Scolaire_Zone_".mb_strtoupper($zone).".ics</a>)</span>
</h2>
";

/*
	$f=fopen("http://cache.media.education.gouv.fr/ics/Calendrier_Scolaire_Zone_".mb_strtoupper($zone).".ics", "r");
	if(!$f) {
		echo "<p style='color:red'>Échec du téléchargement.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$niveau_arbo=1;
	if (!check_user_temp_directory()) {
		echo "<p style='color:red'>Échec de l'accès au dossier temporaire.</p>";
		require("../lib/footer.inc.php");
		die();
	}
*/
	$content=my_file_get_contents("http://cache.media.education.gouv.fr/ics/Calendrier_Scolaire_Zone_".mb_strtoupper($zone).".ics");
	$temp_perso="../temp/".get_user_temp_directory();
	if(!file_put_contents($temp_perso."/fichier_vacances_scolaires.ics", $content)) {
		echo "<p style='color:red'>Échec de l'écriture du fichier temporaire.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p style='margin-bottom:1em;'>L'année scolaire telle que saisie dans <a href='../gestion/param_gen.php'>Gestion générale/Configuration générale</a> court du ".strftime("%d/%m/%Y", $begin_bookings)." au ".strftime("%d/%m/%Y", $end_bookings)."</p>
		<p>Choisissez les dates à importer&nbsp;</p>";

	require("./edt_ics_lib.php");
	require("../lib/class.iCalReader.php");
	$ical2 = new ICal($temp_perso."/fichier_vacances_scolaires.ics");

	// Mode debug:
	$debug_edt="n";

	echo "<p style='margin-bottom:1em; font-size:x-small;'>".$ical2->event_count." enregistrements dans ce fichier (<em>une partie seulement sera affichée (celle correspondant à l'année scolaire en cours)</em>).</p>";
	if($debug_edt=="y") {
		echo '<pre>';
	}
	$cpt=0;
	foreach( $ical2->events() as $event ) {
		$tmp_ts_debut=$ical2->iCalDateToUnixTimestamp($event['DTSTART']);
		if(($tmp_ts_debut>$begin_bookings)&&($tmp_ts_debut<$end_bookings)) {
			if($debug_edt=="y") {
				echo "<hr />";
				print_r($event);
			}

			$annee_courante=strftime("%Y", $tmp_ts_debut);
			$mois_courant=strftime("%m", $tmp_ts_debut);
			$jour_courant=strftime("%d", $tmp_ts_debut);
			if(($mois_courant>10)||($mois_courant<3)) {
				$decalage_horaire=1*3600;
			}
			elseif(($mois_courant>3)&&($mois_courant<10)) {
				$decalage_horaire=2*3600;
			}
			elseif($mois_courant==3) {
				if(!isset($num_dernier_dimanche[$annee_courante][$mois_courant])) {
					$num_dernier_dimanche[$annee_courante][$mois_courant]=get_dernier_dimanche_du_mois($mois_courant, $annee_courante);
				}
				if($jour_courant>=$num_dernier_dimanche[$annee_courante][$mois_courant]) {
					$decalage_horaire=2*3600;
				}
				else {
					$decalage_horaire=1*3600;
				}
			}
			elseif($mois_courant==10) {
				if(!isset($num_dernier_dimanche[$annee_courante][$mois_courant])) {
					$num_dernier_dimanche[$annee_courante][$mois_courant]=get_dernier_dimanche_du_mois($mois_courant, $annee_courante);
				}
				if($jour_courant>=$num_dernier_dimanche[$annee_courante][$mois_courant]) {
					$decalage_horaire=1*3600;
				}
				else {
					$decalage_horaire=2*3600;
				}
			}

			$ts_debut=$ical2->iCalDateToUnixTimestamp($event['DTSTART'])+$decalage_horaire;
			if(isset($event['DTEND'])) {
				$ts_fin=$ical2->iCalDateToUnixTimestamp($event['DTEND'])+$decalage_horaire;
			}
			else {
				$ts_fin=$ts_debut+24*3600-1;
				if($debug_edt=="y") {
					echo "<p><span style='color:red'>Pas de date de fin pour cet événement</span></p>";
				}
			}
			if($debug_edt=="y") {
				echo "<p><span style='color:red'>Du ".strftime("%a %d/%m/%Y %H:%M:%S", $ts_debut)." au ".strftime("%a %d/%m/%Y %H:%M:%S", $ts_fin)."</span></p>";
			}

			if(isset($event['DESCRIPTION'])) {
				if(isset($event['DTEND'])) {
					echo "<p><input type='checkbox' name='date_vacances[]' id='date_vacances_$cpt' value=\"".preg_replace('/"/', '', $event['DESCRIPTION'])."|".strftime("%d/%m/%Y", $ts_debut)."|".strftime("%d/%m/%Y", $ts_fin)."\" onchange=\"checkbox_change(this.id); changement();\" /><label for='date_vacances_$cpt' id='texte_date_vacances_$cpt'> ".$event['DESCRIPTION']."&nbsp;: Du ".strftime("%A %d/%m/%Y", $ts_debut)." <span style='font-size:xx-small; font-style:italic;'>(inclus)</span> au ".strftime("%A %d/%m/%Y", $ts_fin)." <span style='font-size:xx-small; font-style:italic;'>(exclus)</span></label></p>";
				}
			}

			$cpt++;
		}
	}
	if($debug_edt=="y") {
		echo '</pre>';
	}

	$annee_0=strftime("%Y", $begin_bookings);
	$annee_1=$annee_0+1;

	$ts_11_novembre=mktime(0,0,0,11,11,$annee_0);
	echo "
		<br />
		<p>Dates hors du fichier ICS officiel&nbsp;:</p>
		<p><input type='checkbox' name='date_vacances[]' id='date_vacances_$cpt' value=\"11 novembre|11/11/".$annee_0."|12/11/".$annee_0."\" onchange=\"checkbox_change(this.id); changement();\" /><label for='date_vacances_$cpt' id='texte_date_vacances_$cpt'> 11 novembre (<em>Armistice</em>)&nbsp;: Du ".strftime("%A %d/%m/%Y", $ts_11_novembre)." <span style='font-size:xx-small; font-style:italic;'>(inclus)</span> au ".strftime("%A %d/%m/%Y", $ts_11_novembre+24*3600)." <span style='font-size:xx-small; font-style:italic;'>(exclus)</span></label></p>";
	$cpt++;

	$ts_1er_mai=mktime(0,0,0,5,1,$annee_1);
	echo "
		<p><input type='checkbox' name='date_vacances[]' id='date_vacances_$cpt' value=\"1er mai|01/05/".$annee_1."|02/05/".$annee_1."\" onchange=\"checkbox_change(this.id); changement();\" /><label for='date_vacances_$cpt' id='texte_date_vacances_$cpt'> 1er mai (<em>Fête du travail</em>)&nbsp;: Du ".strftime("%A %d/%m/%Y", $ts_1er_mai)." <span style='font-size:xx-small; font-style:italic;'>(inclus)</span> au ".strftime("%A %d/%m/%Y", $ts_1er_mai+24*3600)." <span style='font-size:xx-small; font-style:italic;'>(exclus)</span></label></p>";
	$cpt++;

	$ts_8_mai=mktime(0,0,0,5,8,$annee_1);
	echo "
		<p><input type='checkbox' 
					name='date_vacances[]' 
					id='date_vacances_$cpt' "
	   . "value=\"8 mai|08/05/".$annee_1."|09/05/".$annee_1."\" "
	   . "onchange=\"checkbox_change(this.id); changement();\" />"
	   . "<label for='date_vacances_$cpt' id='texte_date_vacances_$cpt'> 8 mai (<em>Fête de la victoire de 1945</em>)&nbsp;: Du ".strftime("%A %d/%m/%Y", $ts_8_mai)." <span style='font-size:xx-small; font-style:italic;'>(inclus)</span> au ".strftime("%A %d/%m/%Y", $ts_8_mai+24*3600)." <span style='font-size:xx-small; font-style:italic;'>(exclus)</span></label></p>";
	$cpt++;

?>
<p style='margin-top:1em;'>Dates à jour non fixe&nbsp;:</p>
<p>
	On utilise l'<a href='https://fr.wikipedia.org/wiki/Calcul_de_la_date_de_P%C3%A2ques_selon_la_m%C3%A9thode_de_Meeus#cite_ref-13'>algorithme de Butcher</a> 
	pour calculer le samedi de Pâques puis on calcule les autres dates.
</p>
<p>
	<input type='checkbox' 
		   name='date_vacances[]' 
		   id='date_vacances_<?php echo $cpt; ?>' 
		   value="Pâques|<?php echo strftime('%d/%m/%Y', LundiPaques($annee_1)); ?>|<?php echo strftime('%d/%m/%Y',JourSuivant(LundiPaques($annee_1))); ?>" 
		   onchange="checkbox_change(this.id); changement();" />
	<label for='date_vacances_<?php echo $cpt; ?>' id='texte_date_vacances_<?php echo $cpt; ?>'>
		Lundi de Pâques : <?php echo strftime('%d %B %Y', LundiPaques($annee_1)); ?>
	</label>
</p>
<?php $cpt++; ?>
<p>
	<input type='checkbox' 
		   name='date_vacances[]' 
		   id='date_vacances_<?php echo $cpt; ?>' 
		   value="Ascension|<?php echo strftime('%d/%m/%Y', Ascension($annee_1)); ?>|<?php echo strftime('%d/%m/%Y',JourSuivant(Ascension($annee_1))); ?>" 
		   onchange="checkbox_change(this.id); changement();" />
	<label for='date_vacances_<?php echo $cpt; ?>' id='texte_date_vacances_<?php echo $cpt; ?>'>
		Ascension : <?php echo strftime('%d %B %Y', Ascension($annee_1)); ?>
	</label>
</p>
<?php $cpt++; ?>
<p>
	<input type='checkbox' 
		   name='date_vacances[]' 
		   id='date_vacances_<?php echo $cpt; ?>' 
		   value="Vendredi suivant l'Ascension|<?php echo strftime('%d/%m/%Y', VendrediAscension($annee_1)); ?>|<?php echo strftime('%d/%m/%Y',JourSuivant(VendrediAscension($annee_1))); ?>" 
		   onchange="checkbox_change(this.id); changement();" />
	<label for='date_vacances_<?php echo $cpt; ?>' id='texte_date_vacances_<?php echo $cpt; ?>'>
		Vendredi de l'Ascension : <?php echo strftime('%d %B %Y', VendrediAscension($annee_1)); ?>
	</label>
</p>
<?php $cpt++; ?>
<p>
	<input type='checkbox' 
		   name='date_vacances[]' 
		   id='date_vacances_<?php echo $cpt; ?>' 
		   value="Lundi de Pentecôte|<?php echo strftime('%d/%m/%Y', Pentecote($annee_1)); ?>|<?php echo strftime('%d/%m/%Y',JourSuivant(Pentecote($annee_1))); ?>" 
		   onchange="checkbox_change(this.id); changement();" />
	<label for='date_vacances_<?php echo $cpt; ?>' id='texte_date_vacances_<?php echo $cpt; ?>'>
		Lundi de Pentecôte : <?php echo strftime('%d %B %Y', Pentecote($annee_1)); ?>
	</label>
</p>
<?php $cpt++; ?>
	
<p style='margin-top:1em;'>
	<a href='javascript:tout_cocher(true)'>Tout cocher</a>
	- 
	<a href='javascript:tout_cocher(false)'>Tout décocher</a>
</p>
<?php
	echo "
		<p style='margin-top:1em;'>Choisir ces dates pour toutes les classes existantes&nbsp;: <input type='submit' value='Enregistrer' /></p>
<!--
		<p style='color:red;text-indent:-4.8em; margin-left:4.8em; margin-top:1em;'>A FAIRE&nbsp;: Pour permettre l'import des dates qui ne sont pas fixes&nbsp;: Générer un ICS avec les dates de Pentecote, Ascension, lundi Pâques,... pour les prochaines années et l'héberger par exemple chez Sylogix ou sur http://gepi.mutualibre.org à moins qu'il n'y ait moyen de calculer sans erreur ces dates.</p>
-->
		<!--
			Pâques
				dimanche 5 avril 2015
				dimanche 27 mars 2016
				dimanche 16 avril 2017
			Ascension
				jeudi 14 mai 2015
				jeudi 5 mai 2016
				jeudi 25 mai 2017
			Pentecôte
				dimanche 24 mai 2015
				dimanche 15 mai 2016
				dimanche 4 juin 2017
		-->
		
		<p><input type='hidden' name='enregistrer_vacances' value='y' /></p>
		<script type='text/javascript'>
			".js_checkbox_change_style()."
			for(i=0;i<$cpt;i++) {
				checkbox_change('date_vacances_'+i);
			}

			function tout_cocher(mode) {
				cases=document.getElementsByTagName('input');
				for(i=0;i<cases.length;i++) {
					if(cases[i].getAttribute('type')=='checkbox') {
						cases[i].checked=mode;
						checkbox_change(cases[i].getAttribute('id'));
					}
				}
			}
		</script>
	</fieldset>
</form>";

}
else {
	echo "<p style='color:red;text-indent:-4.8em; margin-left:4.8em; margin-top:1em;'>Mode inconnu.</p>";
}

require("../lib/footer.inc.php");
?>
