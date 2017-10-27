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
$corriger_manuellement=isset($_POST['corriger_manuellement']) ? $_POST['corriger_manuellement'] : (isset($_GET['corriger_manuellement']) ? $_GET['corriger_manuellement'] : NULL);

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

	// Récupération des vacances actuellement enregistrées pour les classes
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

	// On importe des vacances depuis un ICS.
	// Suppression d'un éventuel message en page d'accueil
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
			// On ramène une minute avant pour passer du jour de reprise, au jour de fin des vacances à 23:59:59
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
elseif(isset($_GET['imposer_pour_toutes_les_classes'])) {
	check_token();

	$msg="";

	/*
	$sql="TRUNCATE calendrier_vacances;";
	//echo "$sql<br />";
	$menage=mysqli_query($GLOBALS["mysqli"], $sql);
	*/

	// Récupération des vacances actuellement enregistrées pour les classes
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
	/*
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
	*/

	//preg_replace('/"/', '', $event['DESCRIPTION'])."|".strftime("%d/%m/%Y", $ts_debut)."|".strftime("%d/%m/%Y", $ts_fin)
	$date_vacances=array();

	// Récupération des dates de vacances enregistrées dans 'calendrier_vacances'
	$sql="SELECT * FROM calendrier_vacances ORDER BY debut_calendrier_ts;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$date_vacances[]=$lig->nom_calendrier."|".strftime("%d/%m/%Y", $lig->debut_calendrier_ts)."|".strftime("%d/%m/%Y", $lig->fin_calendrier_ts);
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
			// On ramène une minute avant pour passer du jour de reprise, au jour de fin des vacances à 23:59:59
			$fin_calendrier_ts=mktime(0,0,0,$tab_date[1], $tab_date[0], $tab_date[2])-60;
			$jourfin_calendrier=strftime("%Y-%m-%d", $fin_calendrier_ts);
			$heurefin_calendrier=strftime("%H:%M:%S", $fin_calendrier_ts);
			//echo " au ".strftime("%A %d/%m/%Y à %H:%M:%S", $fin_calendrier_ts)."<br />";

			$sql="INSERT INTO edt_calendrier SET classe_concerne_calendrier='".$chaine_id_classe."', 
									nom_calendrier='".mysqli_real_escape_string($mysqli, $tab[0])."',
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
				/*
				$sql="INSERT INTO calendrier_vacances SET nom_calendrier='".$tab[0]."',
									debut_calendrier_ts='".$debut_calendrier_ts."',
									fin_calendrier_ts='".$fin_calendrier_ts."',
									jourdebut_calendrier='".$jourdebut_calendrier."',
									heuredebut_calendrier='".$heuredebut_calendrier."',
									jourfin_calendrier='".$jourfin_calendrier."',
									heurefin_calendrier='".$heurefin_calendrier."';";
				echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				*/
			}
			else {
				$msg.="Erreur lors de l'enregistrement pour ".$tab[0]."<br />";
			}
		}
	}

	$msg.=$nb_reg." enregistrements effectués.<br />";
}
elseif(isset($_POST['enregistrer_correction_manuelle'])) {
	check_token();

	$msg="";

	$nb_reg=0;
	//$date_vacances=isset($_POST['date_vacances']) ? $_POST['date_vacances'] : array();
	$id_calendrier=isset($_POST['id_calendrier']) ? $_POST['id_calendrier'] : array();

	// Récupérer le tableau des vacances déjà enregistrées
	$date_vacances=array();
	$sql="SELECT * FROM calendrier_vacances ORDER BY debut_calendrier_ts;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_assoc($res)) {
			$date_vacances[$lig['id']]=$lig;
		}
	}

	// Parcourir les éventuelles modifs
	for($loop=0;$loop<count($id_calendrier);$loop++) {
		if(preg_match("/^[0-9]{1,}$/", $id_calendrier[$loop])) {
			$date_debut=isset($_POST['date_debut_'.$id_calendrier[$loop]]) ? $_POST['date_debut_'.$id_calendrier[$loop]] : NULL;
			$date_fin=isset($_POST['date_fin_'.$id_calendrier[$loop]]) ? $_POST['date_fin_'.$id_calendrier[$loop]] : NULL;
			if((!isset($date_debut))||
			(!isset($date_fin))||
			(!preg_match("#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#", $date_debut))||
			(!preg_match("#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#", $date_fin))) {
				$msg.="Une (au moins) des dates proposées pour les vacances n°".$id_calendrier[$loop]." n'est pas valide.<br />";
				// Pour revenir à la page de correction manuelle:
				$corriger_manuellement="y";
			}
			else {
				// Reste à contrôler la validité des dates
				$tab_debut=explode("/", $date_debut);
				$tab_fin=explode("/", $date_fin);

				$poursuivre=true;
				if(!checkdate($tab_debut[1], $tab_debut[0], $tab_debut[2])) {
					$msg.="La date de début des vacances n°".$id_calendrier[$loop]." n'est pas valide.<br />";
					$poursuivre=false;
					// Pour revenir à la page de correction manuelle:
					$corriger_manuellement="y";
				}

				if(!checkdate($tab_fin[1], $tab_fin[0], $tab_fin[2])) {
					$msg.="La date de fin des vacances n°".$id_calendrier[$loop]." n'est pas valide.<br />";
					$poursuivre=false;
					// Pour revenir à la page de correction manuelle:
					$corriger_manuellement="y";
				}

				if($poursuivre) {
					// Comparer $date_vacances[$id_calendrier[$loop]]['jourdebut_calendrier'] au format mysql avec $tab_debut[2]-$tab_debut[1]-$tab_debut[0];
					// Mois et jour à formater sur deux chiffres?

					$sql_ajout="";
					$mysql_date_debut=$tab_debut[2]."-".$tab_debut[1]."-".$tab_debut[0];
					if($mysql_date_debut!=$date_vacances[$id_calendrier[$loop]]['jourdebut_calendrier']) {
						$debut_calendrier_ts=mktime(0,0,0,$tab_debut[1], $tab_debut[0], $tab_debut[2]);

						$sql_ajout.=" jourdebut_calendrier='".$mysql_date_debut."', heuredebut_calendrier='00:00:00', debut_calendrier_ts='".$debut_calendrier_ts."'";


						$mysql_date_fin=$tab_fin[2]."-".$tab_fin[1]."-".$tab_fin[0];
						if($mysql_date_fin!=$date_vacances[$id_calendrier[$loop]]['jourfin_calendrier']) {
							$fin_calendrier_ts=mktime(23,59,0,$tab_fin[1], $tab_fin[0], $tab_fin[2]);

							$sql_ajout.=", jourfin_calendrier='".$mysql_date_fin."', heurefin_calendrier='23:59:00', fin_calendrier_ts='".$fin_calendrier_ts."'";
						}
					}
					else {
						$mysql_date_fin=$tab_fin[2]."-".$tab_fin[1]."-".$tab_fin[0];
						if($mysql_date_fin!=$date_vacances[$id_calendrier[$loop]]['jourfin_calendrier']) {
							$fin_calendrier_ts=mktime(23,59,0,$tab_fin[1], $tab_fin[0], $tab_fin[2]);

							$sql_ajout.=" jourfin_calendrier='".$mysql_date_fin."', heurefin_calendrier='23:59:00', fin_calendrier_ts='".$fin_calendrier_ts."'";
						}
					}

					if($sql_ajout!='') {
						$sql="UPDATE calendrier_vacances SET ".$sql_ajout." WHERE id='".$id_calendrier[$loop]."';";
						//echo "$sql<br />";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if($res) {
							$nb_reg++;
						}
						else {
							$msg.="Erreur sur<br />$sql<br />";
							// Pour revenir à la page de correction manuelle:
							$corriger_manuellement="y";
						}
					}
				}
			}
		}
	}

	// Ajout d'une période de vacances
	if((isset($_POST['ajout_date_vacances']))&&
	($_POST['ajout_date_vacances']!="")&&
	(isset($_POST['date_debut_ajout']))&&
	(preg_match("#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#", $_POST['date_debut_ajout']))&&
	(isset($_POST['date_fin_ajout']))&&
	(preg_match("#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#", $_POST['date_fin_ajout']))) {

		$tab_debut=explode("/", $_POST['date_debut_ajout']);
		$tab_fin=explode("/", $_POST['date_fin_ajout']);

		$poursuivre=true;
		if(!checkdate($tab_debut[1], $tab_debut[0], $tab_debut[2])) {
			$msg.="La date de début des vacances pour la période ajoutée n'est pas valide.<br />";
			$poursuivre=false;
			// Pour revenir à la page de correction manuelle:
			$corriger_manuellement="y";
		}

		if(!checkdate($tab_fin[1], $tab_fin[0], $tab_fin[2])) {
			$msg.="La date de fin des vacances pour la période ajoutée n'est pas valide.<br />";
			$poursuivre=false;
			// Pour revenir à la page de correction manuelle:
			$corriger_manuellement="y";
		}

		if($poursuivre) {

			$mysql_date_debut=$tab_debut[2]."-".$tab_debut[1]."-".$tab_debut[0];
			$debut_calendrier_ts=mktime(0,0,0,$tab_debut[1], $tab_debut[0], $tab_debut[2]);
			$mysql_date_fin=$tab_fin[2]."-".$tab_fin[1]."-".$tab_fin[0];
			$fin_calendrier_ts=mktime(23,59,0,$tab_fin[1], $tab_fin[0], $tab_fin[2]);

			$sql="INSERT INTO calendrier_vacances SET nom_calendrier='".mysqli_real_escape_string($mysqli, stripslashes($_POST['ajout_date_vacances']))."', 
										jourdebut_calendrier='".$mysql_date_debut."', 
										heuredebut_calendrier='00:00:00', 
										debut_calendrier_ts='".$debut_calendrier_ts."', 
										jourfin_calendrier='".$mysql_date_fin."', 
										heurefin_calendrier='23:59:00', 
										fin_calendrier_ts='".$fin_calendrier_ts."';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if($res) {
				$nb_reg++;
			}
			else {
				$msg.="Erreur lors de l'ajout d'une période de vacances.<br />";
			}
		}
	}

	// Suppression d'une période de vacances
	$nb_suppr=0;
	$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : array();
	for($loop=0;$loop<count($suppr);$loop++) {
		$sql="DELETE FROM calendrier_vacances WHERE id='".$suppr[$loop]."';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$del) {
			
		}
		else {
			$nb_suppr++;
		}
	}

	$msg.=$nb_reg." enregistrements effectués.<br />";
	if($nb_suppr>0) {
		$msg.=$nb_suppr." enregistrements supprimés.<br />";
	}
}

// Configuration du calendrier
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

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

if(isset($corriger_manuellement)) {
	echo "
	 | <a href='".$_SERVER['PHP_SELF']."'>Retour à la page d'accueil de l'import des vacances et jours fériés</a>
</p>
<h2>Correction manuelle</h2>
<p>Vous pouvez corriger manuellement ici le contenu de la table 'calendrier_vacances'.</p>
<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='enregistrer_correction_manuelle' value='y' />";

	$sql="SELECT * FROM calendrier_vacances ORDER BY debut_calendrier_ts;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "<table class='boireaus boireaus_alt' summary='Vacances scolaires'>
	<tr>
		<th>Titre</th>
		<th colspan='2'>Du premier jour des vacances</th>
		<th colspan='2'>Au dernier jour des vacances inclus</th>
		<th>Supprimer</th>
	</tr>";
		while($lig=mysqli_fetch_object($res)) {
			echo "
	<tr>
		<th>".$lig->nom_calendrier."<input type='hidden' name='id_calendrier[]' value='".$lig->id."' /></th>
		<td>".strftime("%a %d/%m/%Y", $lig->debut_calendrier_ts)."</td>
		<td><input type='text' name='date_debut_".$lig->id."' id='date_debut_".$lig->id."' value='".strftime("%d/%m/%Y", $lig->debut_calendrier_ts)."'  onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />".img_calendrier_js("date_debut_".$lig->id, "img_bouton_date_debut_".$lig->id)."</td>
		<td>".strftime("%a %d/%m/%Y", $lig->fin_calendrier_ts)."</td>
		<td><input type='text' name='date_fin_".$lig->id."' id='date_fin_".$lig->id."' value='".strftime("%d/%m/%Y", $lig->fin_calendrier_ts)."'  onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />".img_calendrier_js("date_fin_".$lig->id, "img_bouton_date_fin_".$lig->id)."</td>
		<td>
			<input type='checkbox' name='suppr[]' value='".$lig->id."' />
		</td>
	</tr>";
		}
		echo "</table>";
	}

	// Ajouter une date manuellement:
	echo "
	<p style='margin-top:2em;'>Ajouter une période de vacances ou un jour férié&nbsp;:<br />
	<em>(laisser un des champs ci-dessous vide, pour ne pas ajouter de période de vacances)</em></p>
	<table>
		<tr>
			<th>
				Désignation&nbsp;: 
			</th>
			<td>
				<input type='text' name='ajout_date_vacances' value='' />
			</td>
		</tr>
		<tr>
			<th>
				Du 
			</th>
			<td>
				<input type='text' name='date_debut_ajout' id='date_debut_ajout' value=''  onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />".img_calendrier_js("date_debut_ajout", "img_bouton_date_debut_ajout")."
			</td>
		</tr>
		<tr>
			<th>
				Au 
			</th>
			<td>
				<input type='text' name='date_fin_ajout' id='date_fin_ajout' value=''  onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />".img_calendrier_js("date_fin_ajout", "img_bouton_date_fin_ajout")."
			</td>
		</tr>
	</table>";

	echo "
		<p align='center' style='margin-top:1em;'><input type='submit' value='Valider' /></p>";


	echo "
	</fieldset>
</form>";
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


	$html_tab_vacances=affiche_tableau_vacances("", "n", "y");
	if($html_tab_vacances!="") {
		echo "<div align='center' style='margin-top:1em;'>
		<p class='bold'>Les vacances et jours fériés enregistrées dans la table 'calendrier_vacances' sont les suivantes&nbsp;:</p>
		".$html_tab_vacances."
		<p>Vous pouvez importer fichier de zone plus haut dans la page,<br />
		ou <a href='".$_SERVER["PHP_SELF"]."?corriger_manuellement=y'>corriger manuellement</a><br />
		ou <a href='".$_SERVER["PHP_SELF"]."?imposer_pour_toutes_les_classes=y".add_token_in_url()."'>imposer les valeurs ci-dessus pour toutes les classes</a>.</p>
	</div>";
	}

	echo "<p style='text-indent:-4em;margin-left:4em;margin-top:1em;'><em>NOTES&nbsp;:</em></p>
	<ul>
		<li><p>Les dates de vacances et jours fériés sont enregistrés ici dans une table 'edt_vacances'.<br />
		Les dates de cette table sont utilisées dans les calendriers affichés pour les personnels de l'établissement.<br />
		Les élèves ont pour leur part des calendriers par classe <em>(table 'edt_calendrier')</em>.</p></li>
		<li><p>La table 'edt_vacances' sert de modèle pour remplir le calendrier par classe, mais pour chaque classe, vous pouvez modifier ,via <strong>Emplois du temps/Gestion/Gestion du calendrier</strong>, les dates de vacances et jours fériés pour telle ou telle classe.<br />
	En principe cependant, toutes les classes devraient tout de même avoir les mêmes vacances et dans ce cas, en important les dates depuis un fichier ICS officiel, vous imposerez les dates de vacances et jours fériés par défaut à toutes les classes.<br />
	Libre à vous d'ajuster ensuite si nécessaire pour telle ou telle classe, par exemple pour ajouter une semaine de stage durant laquelle les élèves ne seront pas considérés comme absents dans le module Absences 2.</p></li>
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
