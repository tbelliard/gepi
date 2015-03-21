<?php
/*
 *
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


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


$msg="";

//GepiAccesRestrAccesAppProfP
if($_SESSION['statut']=="professeur") {
	if(getSettingValue('GepiAccesRestrAccesAppProfP')!="yes") {
		$msg="Accès interdit au paramétrage des accès aux appréciations/avis pour les parents et élèves.";
		header("Location: ../accueil.php?msg=".rawurlencode($msg));
	    die();
	}

	$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0){
		$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
		$msg="Vous n'êtes pas ".$gepi_prof_suivi.".<br />Vous ne devriez donc pas accéder à cette page.";
		header("Location: ../accueil.php?msg=".rawurlencode($msg));
	    die();
	}
}


$sql="CREATE TABLE IF NOT EXISTS `matieres_appreciations_acces` (
`id_classe` INT( 11 ) NOT NULL ,
`statut` VARCHAR( 255 ) NOT NULL ,
`periode` INT( 11 ) NOT NULL ,
`date` DATE NOT NULL ,
`acces` ENUM( 'y', 'n', 'date', 'd' ) NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$creation_table=mysqli_query($GLOBALS["mysqli"], $sql);
/*
if(isset($_POST['submit'])) {
	$max_per=isset($_POST['max_per']) ? $_POST['max_per'] : 0;
	$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
	$nb_classe=isset($_POST['nb_classe']) ? $_POST['nb_classe'] : 0;

	unset($tab);
	$tab=array();
	$tab['ele']='eleve';
	$tab['resp']='responsable';

	$cpt=0;

	foreach($tab as $pref => $statut) {
		for($j=0;$j<$nb_classe;$j++){
			if(isset($id_classe[$j])) {
				for($i=1;$i<=$max_per;$i++){
					if(isset($_POST[$pref.'_mode_'.$j.'_'.$i])) {
						$mode=$_POST[$pref.'_mode_'.$j.'_'.$i];
						if($mode=="manuel") {
							if(isset($_POST[$pref.'_acces_'.$j.'_'.$i])) {
								$accessible="y";
							}
							else {
								$accessible="n";
							}
							$sql="DELETE FROM matieres_appreciations_acces
									WHERE id_classe='$id_classe[$j]' AND
											statut='$statut' AND
											periode='$i';";
							$suppr=mysql_query($sql);

							$sql="INSERT INTO matieres_appreciations_acces
									SET id_classe='$id_classe[$j]',
											statut='$statut',
											periode='$i',
											acces='$accessible';";
							$insert=mysql_query($sql);
							if(!$insert) {$msg.="Erreur sur l'accès aux appréciations de la classe ".get_class_from_id($id_classe[$j])." en $statut pour la période $i.<br />\n";}else{$cpt++;}
						}
						else {
							if(isset($_POST[$pref.'_display_date_'.$j.'_'.$i])) {
								$tmp_date=$_POST[$pref.'_display_date_'.$j.'_'.$i];
								// Contrôler le format de la date et sa validité.

								$tabdate=explode("/",$tmp_date);

								if(checkdate($tabdate[1],$tabdate[0],$tabdate[2])) {
									$date=sprintf("%04d",$tabdate[2])."-".$tabdate[1]."-".$tabdate[0];

									$sql="DELETE FROM matieres_appreciations_acces
											WHERE id_classe='$id_classe[$j]' AND
													statut='$statut' AND
													periode='$i';";
									$suppr=mysql_query($sql);

									$sql="INSERT INTO matieres_appreciations_acces
											SET id_classe='$id_classe[$j]',
													statut='$statut',
													periode='$i',
													date='$date',
													acces='date';";
									$insert=mysql_query($sql);
									if(!$insert) {$msg.="Erreur sur l'accès aux appréciations de la classe ".get_class_from_id($id_classe[$j])." en $statut pour la période $i.<br />\n";}else{$cpt++;}
								}
								else {
									$msg.="La date $tmp_date n'est pas valide pour la classe ".get_class_from_id($id_classe[$j])." en $statut pour la période $i.<br />\n";
								}
							}
						}
					}
				}
			}
		}
	}
	if(($msg=="")&&($cpt>0)) {
		if($cpt==1) {
			$msg="Enregistrement effectué.<br />\n";
		}
		else{
			$msg="Enregistrements effectués ($cpt).<br />\n";
		}
	}
}
*/

$javascript_specifique[]="classes/acces_appreciations";

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

//include "../lib/periodes.inc.php";
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Accès aux appréciations";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href='../accueil.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>\n";
if($_SESSION['statut']=="administrateur") {
	echo " | <a href='../classes/index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Index Classes</a>\n";
}
echo "</p>\n";

//debug_var();

if($_SESSION['statut']=="professeur") {
	$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');

	$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0){
		echo "<p>Vous n'êtes pas ".$gepi_prof_suivi.".<br />Vous ne devriez donc pas accéder à cette page.</p>\n";
		echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
		exit();
	}

	$sql="SELECT DISTINCT c.* FROM j_eleves_professeurs jep, j_eleves_classes jec, classes c
					WHERE jep.professeur='".$_SESSION['login']."' AND
						jep.login=jec.login AND
						jec.id_classe=c.id
					ORDER BY c.classe;";
}
elseif($_SESSION['statut']=="scolarite") {
	$sql="SELECT DISTINCT c.* FROM j_scol_classes jsc, classes c
					WHERE jsc.login='".$_SESSION['login']."' AND
						jsc.id_classe=c.id
					ORDER BY c.classe;";
}
elseif($_SESSION['statut']=="administrateur") {
	$sql="SELECT DISTINCT c.* FROM classes c ORDER BY c.classe;";
}
$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);

if(mysqli_num_rows($res_classe)==0) {
	echo "<p>Vous n'avez accès à aucune classe.</p>\n";
	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	exit();
}

if(isset($_POST['choix_date_valider2'])) {
	check_token(false);

	$periode2=isset($_POST['periode2']) ? $_POST['periode2'] : NULL;
	$choix_date2=isset($_POST['choix_date2']) ? $_POST['choix_date2'] : NULL;

	$poursuivre="y";
	if($choix_date2=='') {
		$poursuivre="n";
		//echo "<script type='text/javascript'>alert('Veuillez saisir une date valide.');</script>\n";
		echo "<span style='color:red'>Date saisie invalide</span>";
	}
	elseif(!my_ereg("[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}",$choix_date2)) {
		$poursuivre="n";
		echo "<span style='color:red'>Date saisie invalide</span>";
	}
	else {
		$tabdate=explode("/",$choix_date2);
		$jour=$tabdate[0];
		$mois=$tabdate[1];
		$annee=$tabdate[2];

		if(!checkdate($mois,$jour,$annee)) {
			$poursuivre="n";
			echo "<span style='color:red'>Date saisie invalide</span>";
		}
	}

	if($poursuivre=="y") {
		if(($periode2!=NULL)&&($choix_date2!=NULL)) {
			$tabdate=explode("/",$choix_date2);
			$mysql_date=$tabdate[2]."-".$tabdate[1]."-".$tabdate[0];
	
			while ($lig=mysqli_fetch_object($res_classe)) {
				$sql2="UPDATE matieres_appreciations_acces SET acces='date', date='$mysql_date' WHERE id_classe='$lig->id' AND periode='$periode2';";
				//echo "$sql2<br />";
				$update=mysqli_query($GLOBALS["mysqli"], $sql2);
			}
		}
	}

	// On refait la requête de liste des classes
	$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
}
elseif(isset($_POST['modif_manuelle_periode'])) {
	check_token(false);

	$periode=isset($_POST['periode']) ? $_POST['periode'] : NULL;
	if(mb_strlen(preg_replace('/[0-9]/','',$periode))!=0) {$periode=NULL;}
	if($periode=='') {$periode=NULL;}

	$acces=isset($_POST['acces']) ? $_POST['acces'] : NULL;
	if(($acces!='y')&&($acces!='n')) {$acces=NULL;}

	if(($periode!=NULL)&&($acces!=NULL)) {
		while ($lig=mysqli_fetch_object($res_classe)) {
			$sql2="UPDATE matieres_appreciations_acces SET acces='$acces' WHERE id_classe='$lig->id' AND periode='$periode';";
			//echo "$sql2<br />";
			$update=mysqli_query($GLOBALS["mysqli"], $sql2);
		}
	}

	// On refait la requête de liste des classes
	$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
}

$tab_classe=array();
$cpt=0;
$max_per=0;
while($lig=mysqli_fetch_object($res_classe)){

	$sql="SELECT MAX(num_periode) AS max_per FROM periodes WHERE id_classe='$lig->id';";
	$res_per=mysqli_query($GLOBALS["mysqli"], $sql);

	if(mysqli_num_rows($res_per)!=0) {
		$tab_classe[$cpt]=array();
		$tab_classe[$cpt]['id']=$lig->id;
		$tab_classe[$cpt]['classe']=$lig->classe;

		$lig_per=mysqli_fetch_object($res_per);
		if($lig_per->max_per>$max_per) {$max_per=$lig_per->max_per;}

		$cpt++;
	}
}

$acces_app_ele_resp=getSettingValue('acces_app_ele_resp');
if($acces_app_ele_resp=="") {$acces_app_ele_resp='manuel';saveSetting('acces_app_ele_resp','manuel');}
$delais_apres_cloture=getSettingValue('delais_apres_cloture');

echo "<p>Vous pouvez définir ici quand les comptes utilisateurs pour des responsables et des élèves peuvent accéder aux appréciations des professeurs et avis du conseil de classe.<br />
Il est souvent apprécié de pouvoir interdire l'accès aux élèves et responsables avant que le conseil de classe se soit déroulé.<br />
Cet accès est conditionné par l'existence des comptes responsables et élèves.</p>\n";
echo "<br />\n";

if($acces_app_ele_resp=='manuel') {
	echo "<p>Cliquez sur la clef <img src='../images/icons/configure.png' width='16' height='16' alt=\"Manuel\" /> pour donner ou supprimer l'accès aux appréciations.</p>\n";
}
elseif($acces_app_ele_resp=='date') {
	echo "<p>Cliquez sur le calendrier <img src='../images/icons/date.png' width='16' height='16' alt=\"Choix d'une date de déverrouillage\" /> pour donner ou supprimer l'accès aux appréciations.</p>\n";
}
else {
	if($_SESSION['statut']=='scolarite') {
		echo "<p>L'accès est automatiquement ouvert <b>$delais_apres_cloture</b> jours après la <a href='../bulletin/verrouillage.php'>clôture de la période</a>.</p>\n";
	}
	else {
		echo "<p>L'accès est automatiquement ouvert <b>$delais_apres_cloture</b> jours après la clôture de la période.</p>\n";
	}
}

/*
echo "<p>L'ouverture/fermeture de l'accès aux appréciations peut se faire selon trois critères&nbsp;:</p>\n";
echo "<ul>\n";
echo "<li><img src='../images/icons/configure.png' width='16' height='16' alt=\"Manuel\" /> Bascule manuelle de l'accès ou de l'interdiction d'accès.</li>\n";
echo "<li><img src='../images/icons/date.png' width='16' height='16' alt=\"Choix d'une date de déverrouillage\" /> Ouverture automatique de l'accès à la date choisie.</li>\n";
echo "<li><img src='../images/icons/securite.png' width='16' height='16' alt=\"Période close\" /> Ouverture automatique de l'accès une fois la période complètement close.<br />\n";
*/

//echo "<form method='post' action='".$_SERVER['PHP_SELF']."' name='form2'>\n";
//echo "<p align='center'><input type='submit' name='submit' value='Valider' /></p>\n";





//=============================================

/*
include("../lib/calendrier/calendrier.class.php");
$cal = new Calendrier("form", "choix_date");
*/

$titre="Choix de la date";
//$texte="<input type='text' name='choix_date' id='choix_date' size='10' value='$display_date'";
$texte="<form name='form' action='".$_SERVER['PHP_SELF']."' method='get'>\n";
$texte.="<p align='center'>\n";
//$texte.=add_token_field();
//$texte.="<input type='hidden' id='csrf_alea' name='csrf_alea' value='".$_SESSION['gepi_alea']."' />\n";
$texte.=add_token_field(true);
$texte.="<input type='hidden' name='id_div' id='choix_date_id_div' value='' />\n";
$texte.="<input type='hidden' name='statut' id='choix_date_statut' value='' />\n";
$texte.="<input type='hidden' name='id_classe' id='choix_date_id_classe' value='' />\n";
$texte.="<input type='hidden' name='periode' id='choix_date_periode' value='' />\n";
$texte.="<input type='text' name='choix_date' id='choix_date' size='10' value='' onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
//$texte.="<a href='#calend' onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170).";document.getElementById('choix_date').checked='true';\"><img src='../lib/calendrier/petit_calendrier.gif' alt='Calendrier' border='0' /></a>\n";
$texte.=img_calendrier_js("choix_date", "img_bouton_choix_date");
$texte.="<br />\n";
$texte.="<input type='button' name='choix_date_valider' value='Valider' onclick=\"g_date()\" />\n";
$texte.="</p>\n";
$texte.="</form>\n";

$tabdiv_infobulle[]=creer_div_infobulle('infobulle_choix_date',$titre,"",$texte,"",14,0,'y','y','n','n');

//=============================================

//$cal2 = new Calendrier("form3", "choix_date2");

$titre="Choix de la date";
//$texte="<input type='text' name='choix_date' id='choix_date' size='10' value='$display_date'";
$texte="<form name='form3' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
$texte.="<p align='center'>\n";
//$texte.="<input type='hidden' name='id_div' id='choix_date_id_div' value='' />\n";
//$texte.="<input type='hidden' name='statut' id='choix_date_statut' value='' />\n";
//$texte.="<input type='hidden' name='id_classe' id='choix_date_id_classe' value='' />\n";
//$texte.=add_token_field();
//$texte.="<input type='hidden' id='csrf_alea' name='csrf_alea' value='".$_SESSION['gepi_alea']."' />\n";
$texte.=add_token_field(true);
$texte.="<input type='hidden' name='periode2' id='choix_date_periode2' value='' />\n";
$texte.="<input type='text' name='choix_date2' id='choix_date2' size='10' value='' onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
//$texte.="<a href='#calend' onClick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170).";\"><img src='../lib/calendrier/petit_calendrier.gif' alt='Calendrier' border='0' /></a>\n";
$texte.=img_calendrier_js("choix_date2", "img_bouton_choix_date2");
$texte.="<br />\n";
//$texte.="<input type='button' name='choix_date_valider2' value='Valider' onclick=\"g_date()\" />\n";
$texte.="<input type='submit' name='choix_date_valider2' value='Valider' />\n";
$texte.="</p>\n";
$texte.="</form>\n";

$tabdiv_infobulle[]=creer_div_infobulle('infobulle_choix_date2',$titre,"",$texte,"",14,0,'y','y','n','n');

//=============================================


if($acces_app_ele_resp=='manuel') {
	// Le mode global paramétré est 'manuel'
	// Si des paramétrages particuliers sont à autre chose que 'manuel', on bascule/modifie vers 'manuel'.

	$tab_dates_prochains_conseils=array();
	$date_courante=strftime("%Y-%m-%d %H:%M:%S");
	$sql="SELECT DISTINCT ddec.id_ev, ddec.id_classe, ddec.date_evenement FROM d_dates_evenements dde, d_dates_evenements_classes ddec WHERE dde.type='conseil_de_classe' AND dde.id_ev=ddec.id_ev AND dde.date_debut<='$date_courante' AND ddec.date_evenement>='$date_courante' ORDER BY date_evenement DESC;";
	$res_cc=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_cc)>0) {
		while($lig_cc=mysqli_fetch_object($res_cc)) {
			$tab_dates_prochains_conseils[$lig_cc->id_classe]=formate_date($lig_cc->date_evenement,"y2","court");
		}
	}

	echo "<form method='post' action='".$_SERVER['PHP_SELF']."' name='form_manuel'>\n";
	//echo "<p align='center'><input type='submit' name='submit' value='Valider' /></p>\n";
	//echo add_token_field();
	//echo "<input type='hidden' id='csrf_alea' name='csrf_alea' value='".$_SESSION['gepi_alea']."' />\n";
	echo add_token_field(true);

	echo "<table class='boireaus' width='100%'>\n";
	echo "<tr>\n";
	echo "<th rowspan='3'>Classe</th>\n";
	//echo "<th rowspan='2'>Statut</th>\n";
	echo "<th colspan='$max_per'>Périodes</th>\n";
	if(count($tab_dates_prochains_conseils)>0) {
		echo "<th rowspan='3' title=\"Si des dates de conseil de classe ont été saisies, elles apparaîtront dans cette colonne.\">Date du prochain<br />conseil de classe</th>\n";
	}
	echo "</tr>\n";

	echo "<tr>\n";
	for($i=1;$i<=$max_per;$i++) {
		$sql="SELECT DISTINCT nom_periode FROM periodes WHERE num_periode='$i';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==1) {
			$lig_per=mysqli_fetch_object($test);
			echo "<th>$lig_per->nom_periode</th>\n";
		}
		else{
			echo "<th>Période $i</th>\n";
		}
	}
	echo "</tr>\n";

	echo "<tr>\n";
	for($i=1;$i<=$max_per;$i++) {
		echo "<th>\n";

		echo "<a href='#' onclick='modif_periode($i,\"y\");return false;'><img src='../images/enabled.png' width='15' height='15' alt='Rendre accessible' /></a>/\n";
		echo "<a href='#' onclick='modif_periode($i,\"n\");return false;'><img src='../images/disabled.png' width='15' height='15' alt='Rendre inaccessible' /></a>\n";

		echo "</th>\n";
	}
	echo "</tr>\n";

	/*	
	$annee = strftime("%Y");
	$mois = strftime("%m");
	$jour = strftime("%d");

	$display_date=$jour."/".$mois."/".$annee;
	*/

	//include("../lib/calendrier/calendrier.class.php");
	
	$tab_statut=array('eleve', 'responsable');
	$tab_statut2=array('Elève', 'Responsable');

	$alt=1;
	for($j=0;$j<count($tab_classe);$j++) {
		$alt=$alt*(-1);
		$id_classe=$tab_classe[$j]['id'];
		unset($nom_periode);
		unset($ver_periode);
		include "../lib/periodes.inc.php";
		if(isset($nom_periode)) {
			if(count($nom_periode)>0){
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td>".$tab_classe[$j]['classe'];
				echo "<input type='hidden' name='id_classe[$j]' value='$id_classe' />\n";
				echo "</td>\n";

				for($i=1;$i<=count($nom_periode);$i++) {

					echo "<td>\n";

					// Avec le nouveau dispositif, on ne distingue pas élève et responsable
					//$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='$tab_statut[$k]';";
					$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='eleve';";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)==0) {
						$mode="manuel";
						$accessible="n";


						// On synchronise aussi pour les responsables
						$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
						//echo "$sql<br />\n";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)==0) {
							$sql="INSERT INTO matieres_appreciations_acces SET acces='$accessible', id_classe='$id_classe', periode='$i', statut='responsable';";
							//echo "$sql<br />\n";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						}
						else {
							$sql="UPDATE matieres_appreciations_acces SET acces='$accessible' WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
							//echo "$sql<br />\n";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);
						}
					}
					else {
						$lig=mysqli_fetch_object($res);

						if($lig->acces=="date") {
							$mode="date";
							$tabdate=explode("-",$lig->date);
							$display_date=$tabdate[2]."/".$tabdate[1]."/".$tabdate[0];

							$timestamp_limite=mktime(0,0,0,$tabdate[1],$tabdate[2],$tabdate[0]);
							$timestamp_courant=time();
							if($timestamp_courant>$timestamp_limite) {
								$accessible="y";
							}
							else {
								$accessible="n";
							}

							// On force la valeur en mode 'manuel'
							$sql="UPDATE matieres_appreciations_acces SET acces='$accessible' WHERE id_classe='$id_classe' AND periode='$i';";
							//echo "$sql<br />\n";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);


							// On synchronise aussi pour les responsables
							$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
							//echo "$sql<br />\n";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res)==0) {
								$sql="INSERT INTO matieres_appreciations_acces SET acces='$accessible', id_classe='$id_classe', periode='$i', statut='responsable';";
								//echo "$sql<br />\n";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							}
							else {
								$sql="UPDATE matieres_appreciations_acces SET acces='$accessible' WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
								//echo "$sql<br />\n";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
							}
						}
						elseif($lig->acces=="d") {
							$mode="d";

							if($ver_periode[$i]!='O') {
								$accessible="n";
							}
							else {
								$tmp_tabdate=explode(" ",$date_ver_periode[$i]);
								$tabdate=explode("-",$tmp_tabdate[0]);
								$display_date=$tabdate[2]."/".$tabdate[1]."/".$tabdate[0];

								$timestamp_limite=mktime(0,0,0,$tabdate[1],$tabdate[2],$tabdate[0])+$delais_apres_cloture*24*3600;
								$timestamp_courant=time();
								if($timestamp_courant>=$timestamp_limite) {
									$accessible="y";
								}
								else {
									$accessible="n";
								}
							}

							// On force la valeur en mode 'manuel'
							$sql="UPDATE matieres_appreciations_acces SET acces='$accessible' WHERE id_classe='$id_classe' AND periode='$i';";
							//echo "$sql<br />\n";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);


							// On synchronise aussi pour les responsables
							$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
							//echo "$sql<br />\n";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res)==0) {
								$sql="INSERT INTO matieres_appreciations_acces SET acces='$accessible', id_classe='$id_classe', periode='$i', statut='responsable';";
								//echo "$sql<br />\n";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							}
							else {
								$sql="UPDATE matieres_appreciations_acces SET acces='$accessible' WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
								//echo "$sql<br />\n";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
							}

						}
						else {
							$mode='manuel';
							$accessible=$lig->acces;

							// On synchronise pour les responsables
							$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
							//echo "$sql<br />\n";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res)==0) {
								$sql="INSERT INTO matieres_appreciations_acces SET acces='$accessible', id_classe='$id_classe', periode='$i', statut='responsable';";
								//echo "$sql<br />\n";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							}
							else {
								$sql="UPDATE matieres_appreciations_acces SET acces='$accessible' WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
								//echo "$sql<br />\n";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
							}
						}
					}

					//echo "<td>\n";

						$current_statut='ele_resp';
						$id_div=$current_statut."_".$j."_".$i;

						echo "<div style='float:left; width:20px; padding-left: 10px;'>\n";
						//echo "<a href='#' onclick=\"g_manuel('$id_div', $id_classe, $i,'$accessible','$tab_statut[$k]');return false;\"><img src='../images/icons/configure.png' width='16' height='16' alt=\"Manuel\" /></a>\n";
						echo "<a href='#' onclick=\"g_manuel('$id_div', $id_classe, $i,'$accessible','$current_statut');return false;\"><img src='../images/icons/configure.png' width='16' height='16' alt=\"Manuel\" /></a>\n";
						echo "</div>\n";

						echo "<div id='$id_div' style='width:100%; height:100%;";
						if($accessible=="y") {
							echo " background-color:lightgreen;\n";
							echo "'>\n";
							echo "Accessible";
						}
						else {
							echo " background-color:orangered;\n";
							echo "'>\n";
							echo "Inaccessible";
						}
						echo "</div>\n";

					echo "</td>\n";
				}
				if(count($tab_dates_prochains_conseils)>0) {
					echo "<td>";
					if(isset($tab_dates_prochains_conseils[$id_classe])) {
						echo $tab_dates_prochains_conseils[$id_classe];
					}
					else {
						echo "-";
					}
					echo "</td>\n";
				}
				echo "</tr>\n";
			}
		}
	}
	
	echo "</table>\n";

	echo "<input type='hidden' name='periode' id='periode' value='' />\n";
	echo "<input type='hidden' name='acces' id='acces' value='' />\n";
	echo "<input type='hidden' name='modif_manuelle_periode' value='y' />\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>

	function modif_periode(periode,acces) {
		document.getElementById('periode').value=periode;
		document.getElementById('acces').value=acces;
		document.forms['form_manuel'].submit();
	}

</script>\n";

}
elseif($acces_app_ele_resp=='date') {
	// Le mode global paramétré est 'date'
	// Si des paramétrages particuliers sont à autre chose que 'date', on bascule/modifie vers 'date'.


	$annee = strftime("%Y");
	$mois = strftime("%m");
	$jour = strftime("%d");

	$display_date=$jour."/".$mois."/".$annee;

	//echo "\$display_date=$display_date<br />";

	echo "<table class='boireaus' width='100%'>\n";
	echo "<tr>\n";
	echo "<th rowspan='3'>Classe</th>\n";
	//echo "<th rowspan='2'>Statut</th>\n";
	echo "<th colspan='$max_per'>Périodes</th>\n";
	echo "</tr>\n";
	
	echo "<tr>\n";
	for($i=1;$i<=$max_per;$i++) {
		$sql="SELECT DISTINCT nom_periode FROM periodes WHERE num_periode='$i';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==1) {
			$lig_per=mysqli_fetch_object($test);
			echo "<th>$lig_per->nom_periode</th>\n";
		}
		else{
			echo "<th>Période $i</th>\n";
		}
	}
	echo "</tr>\n";

	echo "<tr>\n";
	for($i=1;$i<=$max_per;$i++) {
		echo "<th>\n";

		echo "<a href='#' onclick=\"$('choix_date_periode2').value=$i;afficher_div('infobulle_choix_date2','y',-100,20);return false;\"><img src='../images/icons/date.png' width='16' height='16' alt=\"Choix d'une date de déverrouillage\" /></a>\n";

		echo "</th>\n";
	}
	echo "</tr>\n";

	//include("../lib/calendrier/calendrier.class.php");
	
	$tab_statut=array('eleve', 'responsable');
	$tab_statut2=array('Elève', 'Responsable');

	$alt=1;
	for($j=0;$j<count($tab_classe);$j++) {
		$alt=$alt*(-1);
		$id_classe=$tab_classe[$j]['id'];
		unset($nom_periode);
		unset($ver_periode);
		include "../lib/periodes.inc.php";
		if(isset($nom_periode)) {
			if(count($nom_periode)>0){
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td>".$tab_classe[$j]['classe'];
				echo "<input type='hidden' name='id_classe[$j]' value='$id_classe' />\n";
				echo "</td>\n";

				for($i=1;$i<=count($nom_periode);$i++) {
					$chaine_debug="";

					// Avec le nouveau dispositif, on ne distingue pas élève et responsable
					//$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='$tab_statut[$k]';";
					$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='eleve';";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)==0) {
						// Initialisation
						$mode="date";
						$accessible="n";

						$chaine_debug.="Initialisation: ";

						// Mettre une date future
						$tmp_date=getdate(time()+4*30*24*3600);
						$tmp_jour=sprintf("%02d",$tmp_date['mday']);
						$tmp_mois=sprintf("%02d",$tmp_date['mon']);
						$tmp_annee=$tmp_date['year'];

						// On force la valeur en mode 'date' (pour eleve et responsable)
						$sql="INSERT INTO matieres_appreciations_acces SET acces='date', date='$tmp_annee-$tmp_mois-$tmp_jour', id_classe='$id_classe', periode='$i', statut='eleve';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);

						// On synchronise aussi pour les responsables
						$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)==0) {
							$sql="INSERT INTO matieres_appreciations_acces SET acces='date', date='$tmp_annee-$tmp_mois-$tmp_jour', id_classe='$id_classe', periode='$i', statut='responsable';";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						}
						else {
							$sql="UPDATE matieres_appreciations_acces SET acces='date', date='$tmp_annee-$tmp_mois-$tmp_jour' WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);
						}

						$display_date="$tmp_jour/$tmp_mois/$tmp_annee";
						$chaine_debug.="1 \$display_date=$display_date<br />";
					}
					else {
						$lig=mysqli_fetch_object($res);

						$chaine_debug.="\$lig->acces=$lig->acces<br />";

						if($lig->acces=="date") {
							$mode="date";
							$tabdate=explode("-",$lig->date);
							$display_date=$tabdate[2]."/".$tabdate[1]."/".$tabdate[0];

							$timestamp_limite=mktime(0,0,0,$tabdate[1],$tabdate[2],$tabdate[0]);
							$timestamp_courant=time();
							if($timestamp_courant>$timestamp_limite) {
								$accessible="y";
							}
							else {
								$accessible="n";
							}

							$chaine_debug.="\$timestamp_courant=$timestamp_courant<br />";
							$chaine_debug.="\$timestamp_limite=$timestamp_limite<br />";
							$chaine_debug.="\$accessible=$accessible<br />";

						}
						elseif($lig->acces=="d") {
							$mode="d";

							if($ver_periode[$i]!='O') {
								$accessible="n";

								// Mettre une date future
								$tmp_date=getdate(time()+4*30*24*3600);
								$tmp_jour=sprintf("%02d",$tmp_date['mday']);
								$tmp_mois=sprintf("%02d",$tmp_date['mon']);
								$tmp_annee=$tmp_date['year'];
								$display_date="$tmp_jour/$tmp_mois/$tmp_annee";

							}
							else {
								$tmp_tabdate=explode(" ",$date_ver_periode[$i]);
								$tabdate=explode("-",$tmp_tabdate[0]);
								$display_date=$tabdate[2]."/".$tabdate[1]."/".$tabdate[0];

								$timestamp_limite=mktime(0,0,0,$tabdate[1],$tabdate[2],$tabdate[0])+$delais_apres_cloture*24*3600;
								$timestamp_courant=time();
								if($timestamp_courant>=$timestamp_limite) {
									$accessible="y";

									// Mettre une date passée: hier
									$tmp_date=getdate(time()-24*3600);
									$tmp_jour=sprintf("%02d",$tmp_date['mday']);
									$tmp_mois=sprintf("%02d",$tmp_date['mon']);
									$tmp_annee=$tmp_date['year'];
								}
								else {
									$accessible="n";

									// Mettre une date future
									$tmp_date=getdate(time()+4*30*24*3600);
									$tmp_jour=sprintf("%02d",$tmp_date['mday']);
									$tmp_mois=sprintf("%02d",$tmp_date['mon']);
									$tmp_annee=$tmp_date['year'];
								}
							}

							// On force la valeur en mode 'date'
							$sql="UPDATE matieres_appreciations_acces SET acces='date', date='$tmp_annee-$tmp_mois-$tmp_jour' WHERE id_classe='$id_classe' AND periode='$i';";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);

							// On synchronise aussi pour les responsables
							$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res)==0) {
								$sql="INSERT INTO matieres_appreciations_acces SET acces='date', date='$tmp_annee-$tmp_mois-$tmp_jour', id_classe='$id_classe', periode='$i', statut='responsable';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							}
							else {
								$sql="UPDATE matieres_appreciations_acces SET acces='date', date='$tmp_annee-$tmp_mois-$tmp_jour' WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
							}

							$chaine_debug.="2 \$display_date=$display_date<br />";

						}
						else {
							$mode='manuel';
							$accessible=$lig->acces;

							if($accessible=='y') {
								// Mettre une date passée: hier
								$tmp_date=getdate(time()-24*3600);
								$tmp_jour=sprintf("%02d",$tmp_date['mday']);
								$tmp_mois=sprintf("%02d",$tmp_date['mon']);
								$tmp_annee=$tmp_date['year'];
							}
							else {
								// Mettre une date future
								$tmp_date=getdate(time()+4*30*24*3600);
								$tmp_jour=sprintf("%02d",$tmp_date['mday']);
								$tmp_mois=sprintf("%02d",$tmp_date['mon']);
								$tmp_annee=$tmp_date['year'];
							}

							// On force la valeur en mode 'date'
							$sql="UPDATE matieres_appreciations_acces SET acces='date', date='$tmp_annee-$tmp_mois-$tmp_jour' WHERE id_classe='$id_classe' AND periode='$i';";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);

							// On synchronise aussi pour les responsables
							$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res)==0) {
								$sql="INSERT INTO matieres_appreciations_acces SET acces='date', date='$tmp_annee-$tmp_mois-$tmp_jour', id_classe='$id_classe', periode='$i', statut='responsable';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							}
							else {
								$sql="UPDATE matieres_appreciations_acces SET acces='date', date='$tmp_annee-$tmp_mois-$tmp_jour' WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
							}

							$display_date="$tmp_jour/$tmp_mois/$tmp_annee";
							$chaine_debug.="3 \$display_date=$display_date<br />";

						}
					}

					echo "<td>\n";

						//echo $chaine_debug;
	
						$current_statut='ele_resp';
						$id_div=$current_statut."_".$j."_".$i;

						echo "<div style='float:left; width:20px; padding-left: 10px;'>\n";
						//echo "<a href='#' onclick=\"$('choix_date_id_div').value='$id_div';$('choix_date_id_classe').value=$id_classe;$('choix_date_statut').value='$tab_statut[$k]';$('choix_date_periode').value=$i;afficher_div('infobulle_choix_date','y',-100,20);return false;\"><img src='../images/icons/date.png' width='16' height='16' alt=\"Choix d'une date de déverrouillage\" /></a>\n";
						echo "<a href='#' onclick=\"$('choix_date_id_div').value='$id_div';$('choix_date_id_classe').value=$id_classe;$('choix_date_statut').value='$current_statut';$('choix_date_periode').value=$i;afficher_div('infobulle_choix_date','y',-100,20);return false;\"><img src='../images/icons/date.png' width='16' height='16' alt=\"Choix d'une date de déverrouillage\" /></a>\n";
						echo "</div>\n";

						echo "<div id='$id_div' style='width:100%; height:100%;";
						if($accessible=="y") {
							echo " background-color:lightgreen;\n";
							echo "'>\n";
							echo "Accessible&nbsp;: ";
						}
						else {
							echo " background-color:orangered;\n";
							echo "'>\n";
							echo "Inaccessible&nbsp;: ";
						}
						echo "$display_date";
						echo "</div>\n";

					echo "</td>\n";
				}
				echo "</tr>\n";
			}
		}
	}
	
	echo "</table>\n";

}
elseif($acces_app_ele_resp=='periode_close') {
	// Le mode global paramétré est 'periode_close'
	// Si des paramétrages particuliers sont à autre chose que 'periode_close', on bascule/modifie vers 'periode_close'.
	echo "<table class='boireaus' width='100%'>\n";
	echo "<tr>\n";
	//echo "<th rowspan='3'>Classe</th>\n";
	echo "<th rowspan='2'>Classe</th>\n";
	//echo "<th rowspan='2'>Statut</th>\n";
	echo "<th colspan='$max_per'>Périodes</th>\n";
	echo "</tr>\n";
	
	echo "<tr>\n";
	for($i=1;$i<=$max_per;$i++) {
		$sql="SELECT DISTINCT nom_periode FROM periodes WHERE num_periode='$i';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==1) {
			$lig_per=mysqli_fetch_object($test);
			echo "<th>$lig_per->nom_periode</th>\n";
		}
		else{
			echo "<th>Période $i</th>\n";
		}
	}
	echo "</tr>\n";

	/*
	echo "<tr>\n";
	for($i=1;$i<=$max_per;$i++) {
		echo "<th>\n";
		echo "Coche...";
		echo "</th>\n";
	}
	echo "</tr>\n";
	*/

	$annee = strftime("%Y");
	$mois = strftime("%m");
	$jour = strftime("%d");

	$display_date=$jour."/".$mois."/".$annee;

	//include("../lib/calendrier/calendrier.class.php");
	
	$tab_statut=array('eleve', 'responsable');
	$tab_statut2=array('Elève', 'Responsable');

	$alt=1;
	for($j=0;$j<count($tab_classe);$j++) {
		$alt=$alt*(-1);
		$id_classe=$tab_classe[$j]['id'];
		unset($nom_periode);
		unset($ver_periode);
		include "../lib/periodes.inc.php";
		if(isset($nom_periode)) {
			if(count($nom_periode)>0){
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td>".$tab_classe[$j]['classe'];
				echo "<input type='hidden' name='id_classe[$j]' value='$id_classe' />\n";
				echo "</td>\n";

				for($i=1;$i<=count($nom_periode);$i++) {
					$chaine_debug="";

					// Avec le nouveau dispositif, on ne distingue pas élève et responsable
					//$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='$tab_statut[$k]';";
					$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='eleve';";
					$chaine_debug.="$sql<br />";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)==0) {
						// Initialisation
						$mode="d";

						if($ver_periode[$i]!='O') {
							$accessible="n";
						}
						else {
							$tmp_tabdate=explode(" ",$date_ver_periode[$i]);
							$tabdate=explode("-",$tmp_tabdate[0]);
							$display_date=$tabdate[2]."/".$tabdate[1]."/".$tabdate[0];
	
							$timestamp_limite=mktime(0,0,0,$tabdate[1],$tabdate[2],$tabdate[0])+$delais_apres_cloture*24*3600;
							$timestamp_courant=time();
							if($timestamp_courant>=$timestamp_limite) {
								$accessible="y";
							}
							else {
								$accessible="n";
							}
						}

						// On force la valeur en mode 'date' (pour eleve et responsable)
						$sql="INSERT INTO matieres_appreciations_acces SET acces='d', id_classe='$id_classe', periode='$i', statut='eleve';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);

						// On synchronise aussi pour les responsables
						$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)==0) {
							$sql="INSERT INTO matieres_appreciations_acces SET acces='d', id_classe='$id_classe', periode='$i', statut='responsable';";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						}
						else {
							$sql="UPDATE matieres_appreciations_acces SET acces='d' WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);
						}
					}
					else {
						$lig=mysqli_fetch_object($res);

						$chaine_debug.="\$lig->acces=$lig->acces<br />";

						if($lig->acces=="date") {
							$mode="date";

							if($ver_periode[$i]!='O') {
								$accessible="n";
							}
							else {
								$tmp_tabdate=explode(" ",$date_ver_periode[$i]);
								$tabdate=explode("-",$tmp_tabdate[0]);
								$display_date=$tabdate[2]."/".$tabdate[1]."/".$tabdate[0];
		
								$timestamp_limite=mktime(0,0,0,$tabdate[1],$tabdate[2],$tabdate[0])+$delais_apres_cloture*24*3600;
								$timestamp_courant=time();
								if($timestamp_courant>=$timestamp_limite) {
									$accessible="y";
								}
								else {
									$accessible="n";
								}
							}

							// On force la valeur en mode 'd' soit 'periode_close' pour eleve et responsable
							$sql="UPDATE matieres_appreciations_acces SET acces='d' WHERE id_classe='$id_classe' AND periode='$i';";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);

							// On synchronise aussi pour les responsables
							$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res)==0) {
								$sql="INSERT INTO matieres_appreciations_acces SET acces='d', id_classe='$id_classe', periode='$i', statut='responsable';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							}
							/*
							else {
								$sql="UPDATE matieres_appreciations_acces SET acces='d' WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
								$update=mysql_query($sql);
							}
							*/
						}
						elseif($lig->acces=="d") {
							$mode="d";

							if($ver_periode[$i]!='O') {
								$accessible="n";
							}
							else {
								if($date_ver_periode[$i]=="0000-00-00 00:00:00") {
									$display_date="Clôture";
									$accessible="n";
								}
								else {
									$tmp_tabdate=explode(" ",$date_ver_periode[$i]);
									$tabdate=explode("-",$tmp_tabdate[0]);
									$display_date=$tabdate[2]."/".$tabdate[1]."/".$tabdate[0];

									$timestamp_limite=mktime(0,0,0,$tabdate[1],$tabdate[2],$tabdate[0])+$delais_apres_cloture*24*3600;
									$timestamp_courant=time();
									if($timestamp_courant>=$timestamp_limite) {
										$accessible="y";
									}
									else {
										$accessible="n";
									}
								}
							}

							// On synchronise aussi pour les responsables
							$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res)==0) {
								$sql="INSERT INTO matieres_appreciations_acces SET acces='d', id_classe='$id_classe', periode='$i', statut='responsable';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							}
							else {
								$sql="UPDATE matieres_appreciations_acces SET acces='d' WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
							}

						}
						else {
							$mode='manuel';

							if($ver_periode[$i]!='O') {
								$accessible="n";
							}
							else {
								$tmp_tabdate=explode(" ",$date_ver_periode[$i]);
								$tabdate=explode("-",$tmp_tabdate[0]);
								$display_date=$tabdate[2]."/".$tabdate[1]."/".$tabdate[0];

								$timestamp_limite=mktime(0,0,0,$tabdate[1],$tabdate[2],$tabdate[0])+$delais_apres_cloture*24*3600;
								$timestamp_courant=time();
								if($timestamp_courant>=$timestamp_limite) {
									$accessible="y";
								}
								else {
									$accessible="n";
								}
							}


							// On force la valeur en mode 'd' soit 'periode_close' pour eleve et responsable
							$sql="UPDATE matieres_appreciations_acces SET acces='d' WHERE id_classe='$id_classe' AND periode='$i';";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);

							// On synchronise aussi pour les responsables
							$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res)==0) {
								$sql="INSERT INTO matieres_appreciations_acces SET acces='d', id_classe='$id_classe', periode='$i', statut='responsable';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							}
							/*
							else {
								$sql="UPDATE matieres_appreciations_acces SET acces='d' WHERE id_classe='$id_classe' AND periode='$i' AND statut='responsable';";
								$update=mysql_query($sql);
							}
							*/
						}


						// On force la valeur en mode 'd' soit 'periode_close' pour eleve et responsable
						$sql="UPDATE matieres_appreciations_acces SET acces='d' WHERE id_classe='$id_classe' AND periode='$i';";
						$chaine_debug.="$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);

					}

					echo "<td>\n";

						//echo $chaine_debug;
	
						$current_statut='ele_resp';
						$id_div=$current_statut."_".$j."_".$i;

						echo "<div style='float:left; width:20px; padding-left: 10px;'>\n";
						//echo "<a href='#' onclick=\"g_periode_close('$id_div', $id_classe, $i,'$tab_statut[$k]');return false;\"><img src='../images/icons/securite.png' width='16' height='16' alt=\"Période close\" /></a>\n";
						echo "<a href='#' onclick=\"g_periode_close('$id_div', $id_classe, $i,'$current_statut');return false;\"><img src='../images/icons/securite.png' width='16' height='16' alt=\"Période close\" /></a>\n";
						echo "</div>\n";

						echo "<div id='$id_div' style='width:100%; height:100%;";
						if($accessible=="y") {
							echo " background-color:lightgreen;\n";
							echo "'>\n";
							echo "Accessible";
	
							if($display_date!='00/00/0000') {
								//echo "&nbsp;: ";
								echo " depuis le ";
								echo $display_date;
								if($delais_apres_cloture>0) {echo " + ".$delais_apres_cloture."j";}
							}
							else {
								echo " <span style='font-size:x-small;'>depuis la clôture de la période</span>";
							}

						}
						else {
							echo " background-color:orangered;\n";
							echo "'>\n";
							echo "Inaccessible";

							if($ver_periode[$i]=='N') {
								echo " <span style='font-size:x-small;'>période ouverte</span>";
							}
							elseif($ver_periode[$i]=='P') {
								echo " <span style='font-size:x-small;'>période partiellement close</span>";
							}
							else {
								// On est dans le cas du délais après cloture

								echo " <span style='font-size:x-small;'>$display_date + $delais_apres_cloture jour(s)</span>";

							}
						}

						echo "</div>\n";

					echo "</td>\n";
				}
				echo "</tr>\n";
			}
		}
	}
	
	echo "</table>\n";
}

if($_SESSION['statut']=="administrateur") {
	echo "Le mode d'accès aux appréciations (<i>manuel/date/période close</i>) ainsi que le délai après clôture de période se paramètrent en administrateur dans <a href='../gestion/param_gen.php#delais_apres_cloture'>Gestion générale/Configuration générale</a>";
}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
