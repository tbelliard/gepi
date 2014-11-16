<?php
/* $Id$ */
/*
* Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
$sql="SELECT 1=1 FROM droits WHERE id='/lib/ical.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/lib/ical.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='V',
autre='F',
description='ical',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
// INSERT INTO droits VALUES('/lib/ical.php','V','V','V','V','V','V','V','F','ical','');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================


//============================================
//https://gist.github.com/jakebellacera/635416
function dateToCal($timestamp) {
	return date('Ymd\THis\Z', $timestamp);
}
// Escapes a string of characters
function escapeString($string) {
	return preg_replace('/([\,;])/','\\\$1', $string);
}

//============================================
function unhtmlentities($cadena){
// reemplazar entidades numericas
$cadena = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $cadena);
$cadena = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $cadena);
// reemplazar entidades literales
$trans_tbl = get_html_translation_table(HTML_ENTITIES);
$trans_tbl = array_flip($trans_tbl);
return strtr($cadena, $trans_tbl);
}
//============================================
function nettoyage_et_formatage_chaine_description_ics($chaine) {
	$retour=strip_tags($chaine);
	$retour=escapeString($retour);
	$retour=preg_replace("/\t/"," ", $retour);
	$retour=preg_replace("/\n/","\\\n", $retour);
}
//============================================
function get_dernier_dimanche_du_mois2($mois, $annee) {
	// Fonction utilisée pour les mois de mars et octobre (31 jours)
	for($i=31;$i>1;$i--) {
		$ts=mktime(0, 0, 0, $mois , $i, $annee);
		if(strftime("%u", $ts)==7) {
			break;
		}
	}
	return $i;
}
//============================================
function dateToCal2($timestamp) {
	// Pour tenir compte du décalage horaire et de l'heure d'été

	$annee_courante=strftime("%Y", $timestamp);
	$mois_courant=strftime("%m", $timestamp);
	$jour_courant=strftime("%d", $timestamp);
	if(($mois_courant>10)||($mois_courant<3)) {
		$decalage_horaire=1*3600;
	}
	elseif(($mois_courant>3)&&($mois_courant<10)) {
		$decalage_horaire=2*3600;
	}
	elseif($mois_courant==3) {
		if(!isset($num_dernier_dimanche[$annee_courante][$mois_courant])) {
			$num_dernier_dimanche[$annee_courante][$mois_courant]=get_dernier_dimanche_du_mois2($mois_courant, $annee_courante);
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
			$num_dernier_dimanche[$annee_courante][$mois_courant]=get_dernier_dimanche_du_mois2($mois_courant, $annee_courante);
		}

		if($jour_courant>=$num_dernier_dimanche[$annee_courante][$mois_courant]) {
			$decalage_horaire=1*3600;
		}
		else {
			$decalage_horaire=2*3600;
		}
	}

	return dateToCal($timestamp-$decalage_horaire);
}
//============================================

$id_ev=isset($_GET['id_ev']) ? $_GET['id_ev'] : NULL;
if(isset($id_ev)) {

	if(!preg_match("/^[0-9]{1,}$/", $id_ev)) {
		echo "id_ev $id_ev invalide.";
		die();
	}

	if($_SESSION['statut']=='professeur') {
		/*
		$sql="SELECT 1=1 FROM d_dates_evenements dde, 
						d_dates_evenements_classes ddec, 
						d_dates_evenements_utilisateurs ddeu 
					WHERE ddeu.statut='professeur' AND 
						ddeu.id_ev=dde.id_ev AND 
						dde.id_ev=ddec.id_ev AND 
						dde.id_ev='$id_ev' AND 
						id_classe IN (SELECT DISTINCT jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."');";
		*/

		$sql="SELECT d.*,c.classe FROM d_dates_evenements_classes d, classes c WHERE d.id_ev='$id_ev' AND c.id=d.id_classe AND d.id_classe IN (SELECT DISTINCT jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."') ORDER BY date_evenement, classe;";

	}
	elseif($_SESSION['statut']=='cpe') {
		$sql="SELECT d.*,c.classe FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND id_classe IN (SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec, j_eleves_cpe jecpe WHERE jec.e_login=jecpe.cpe_login AND jecpe.cpe_login='".$_SESSION['login']."') ORDER BY date_evenement, classe;";
	}
	elseif($_SESSION['statut']=='scolarite') {
		$sql="SELECT d.*,c.classe FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND id_classe IN (SELECT DISTINCT jsc.id_classe FROM j_scol_classes jsc WHERE jsc.login='".$_SESSION['login']."') ORDER BY date_evenement, classe;";
	}
	elseif($_SESSION['statut']=='administrateur') {
		$sql="SELECT d.*,c.classe FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id ORDER BY date_evenement, classe;";
	}
	elseif($_SESSION['statut']=='responsable') {
		$sql="SELECT DISTINCT d.*,c.classe FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND id_classe IN (SELECT DISTINCT jec.id_classe FROM resp_pers rp, 
														responsables2 r, 
														eleves e, 
														j_eleves_classes jec 
													WHERE rp.login='".$_SESSION['login']."' AND 
														rp.pers_id=r.pers_id AND 
														r.ele_id=e.ele_id AND 
														e.login=jec.login AND 
														(r.resp_legal='1' OR r.resp_legal='2' OR r.acces_sp='y')
													) ORDER BY date_evenement, classe;";
	}
	elseif($_SESSION['statut']=='eleve') {
		$sql="SELECT DISTINCT d.*,c.classe FROM d_dates_evenements_classes d, classes c, j_eleves_classes jec WHERE id_ev='$id_ev' AND d.id_classe=c.id AND d.id_classe=jec.id_classe AND jec.login='".$_SESSION['login']."' ORDER BY date_evenement, classe;";
	}
	else {
		echo "L'id_ev $id_ev ne vous concerne pas.";
		die();
	}

	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0) {
		echo "L'id_ev $id_ev ne vous concerne pas.";
		die();
	}


	$sql="SELECT * FROM d_dates_evenements WHERE id_ev='$id_ev';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$lig=mysqli_fetch_object($res);

	if($lig->type=='conseil_de_classe') {
		$prefixe_sujet="Conseil de classe de ";
		//Problème à définir une durée... ce n'est pas dans la base
		$duree=3600+15*60;
		// Il faudrait un paramètre Durée standard conseil de classe.
	}
	else {
		$prefixe_sujet="Événement ";
		$duree=3600;
	}

	$description=$lig->texte_avant."\n".$lig->texte_apres;

	$gepiSchoolName=getSettingValue('gepiSchoolName');
	//$gepiSchoolRne=getSettingValue('gepiSchoolRne');
	$gepiSchoolCity=getSettingValue('gepiSchoolCity');

	$etab=$gepiSchoolName." à ".$gepiSchoolCity;
	$_etab=remplace_accents($etab, "all");

	$export_ical="BEGIN:VCALENDAR
PRODID:-//GEPI//$etab//FR
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:PUBLISH
X-WR-TIMEZONE:Europe/Paris
X-WR-CALDESC:
BEGIN:VTIMEZONE
TZID:Europe/Paris
X-LIC-LOCATION:Europe/Paris

BEGIN:DAYLIGHT
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
TZNAME:CEST
DTSTART:19700329T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU
END:DAYLIGHT

BEGIN:STANDARD
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
TZNAME:CET
DTSTART:19701025T030000
RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU
END:STANDARD
END:VTIMEZONE";

	$sql="SELECT * FROM d_dates_evenements_classes WHERE id_ev='$id_ev' AND id_classe IN (SELECT DISTINCT jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."');";
	$res2=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig2=mysqli_fetch_object($res2)) {
		$nom_classe=get_nom_classe($lig2->id_classe);

		$uid_ev="...";
		$timestamp1=mysql_date_to_unix_timestamp($lig2->date_evenement);
		$timestamp2=$timestamp1+$duree;

		// On n'enregistre pas la date de modif...
		// On va mettre, il y a 5min... ou alors peut-être la date de début d'année...
		$timestamp_creation=time()-5*60;
		// Est-ce que CREATED est un champ indispensable

		$export_ical.="
BEGIN:VEVENT
DTSTART:".dateToCal2($timestamp1)."
DTEND:".dateToCal2($timestamp2)."
DTSTAMP:".dateToCal2($timestamp1)."
UID:ev_".$id_ev."_id_classe_".$lig2->id_classe."@gepi.$_etab
CREATED:".dateToCal2($timestamp_creation)."
DESCRIPTION:".nettoyage_et_formatage_chaine_description_ics($description)."
LOCATION:".$gepiSchoolCity."
SEQUENCE:0
STATUS:CONFIRMED
SUMMARY:".$prefixe_sujet." ".$nom_classe."
TRANSP:OPAQUE
END:VEVENT";
	}

	$export_ical.="
END:VCALENDAR";

	$nom_fic = "gepi_cal_".$id_ev."_".date('Y.m.d_H.i.s_').preg_replace("/ /","_",microtime()).".ics";
	send_file_download_headers('text/calendar',$nom_fic);
	echo $export_ical;
	die();

/*

J'ai un décalage de 2h sur les horaires... 18h45 au lieu de 16h45

Pas bon: Généré par mon ical.php
J'ai depuis fait des modifs pour tenir compte du décalage horaire et de l'heure d'été

BEGIN:VEVENT
DTSTART:20140612T164500Z
DTEND:20140612T174500Z
DTSTAMP:20140612T164500Z
UID:ev_2_id_classe_37@gepi.College_Le_Hameau_a_Bernay
CREATED:20140611T210554Z
DESCRIPTION:
LOCATION:Bernay
SEQUENCE:0
STATUS:CONFIRMED
SUMMARY:Conseil de classe de  4 B
TRANSP:OPAQUE
END:VEVENT


Bon: créé directement dans Google Agenda

BEGIN:VEVENT
DTSTART:20140612T144500Z
DTEND:20140612T160000Z
DTSTAMP:20140611T191409Z
UID:d91ldfesr9q6p45st1cl6m7i2g@google.com
CREATED:20140611T191341Z
DESCRIPTION:Salle techno SEGPA.\nÉlèves à 17h.
LAST-MODIFIED:20140611T191341Z
LOCATION:Bernay
SEQUENCE:0
STATUS:CONFIRMED
SUMMARY:Conseil 4B
TRANSP:OPAQUE
END:VEVENT

//===========================================================

BEGIN:VCALENDAR
METHOD:PUBLISH
VERSION:2.0
PRODID:-//Thomas Multimedia//Clinic Time//EN
BEGIN:VEVENT
SUMMARY:Emily Henderson
UID:3097
STATUS:CONFIRMED
DTSTART:20120509T031500Z
DTEND:20120509T033000Z
LAST-MODIFIED:20120509T031500Z
LOCATION:Bundall Clinic Room 1
END:VEVENT
END:VCALENDAR

//===========================================================

BEGIN:VCALENDAR
	PRODID:-//Google Inc//Google Calendar 70.9054//EN
	VERSION:2.0
	CALSCALE:GREGORIAN
	METHOD:PUBLISH
	X-WR-CALNAME:Famille
	X-WR-TIMEZONE:Europe/Paris
	X-WR-CALDESC:

	BEGIN:VTIMEZONE
		TZID:Europe/Paris
		X-LIC-LOCATION:Europe/Paris

		BEGIN:DAYLIGHT
			TZOFFSETFROM:+0100
			TZOFFSETTO:+0200
			TZNAME:CEST
			DTSTART:19700329T020000
			RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU
		END:DAYLIGHT

		BEGIN:STANDARD
			TZOFFSETFROM:+0200
			TZOFFSETTO:+0100
			TZNAME:CET
			DTSTART:19701025T030000
			RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU
		END:STANDARD
	END:VTIMEZONE

	BEGIN:VEVENT
		DTSTART:20140702T063000Z
		DTEND:20140702T100000Z
		DTSTAMP:20140611T165116Z
		UID:oj08kud64nv3rbrdg89l2m6hls@google.com
		CREATED:20140602T141247Z
		DESCRIPTION:Collège Pierre Corneille\n6 rue P.Corneille\n0232351533
		LAST-MODIFIED:20140602T141247Z
		LOCATION:Le Neubourg\, France
		SEQUENCE:0
		STATUS:CONFIRMED
		SUMMARY:Correction DNB
		TRANSP:OPAQUE
	END:VEVENT
	BEGIN:VEVENT
		DTSTART:20140619T070000Z
		DTEND:20140619T150000Z
		DTSTAMP:20140611T165116Z
		UID:m6mnnsea57m1r86secgr8j7sf0@google.com
		CREATED:20140602T141320Z
		DESCRIPTION:
		LAST-MODIFIED:20140602T141320Z
		LOCATION:Evreux
		SEQUENCE:0
		STATUS:CONFIRMED
		SUMMARY:ENT Evreux
		TRANSP:OPAQUE
	END:VEVENT
	BEGIN:VEVENT
		DTSTART:20140618T123000Z
		DTEND:20140618T133000Z
		DTSTAMP:20140611T165116Z
		UID:quj37gf9g0ghnga2j71g68f530@google.com
		CREATED:20140611T165056Z
		DESCRIPTION:Clémentine\, Mathieu et moi
		LAST-MODIFIED:20140611T165056Z
		LOCATION:Serquigny\, France
		SEQUENCE:0
		STATUS:CONFIRMED
		SUMMARY:Coiffure
		TRANSP:OPAQUE
	END:VEVENT

END:VCALENDAR

*/



}
else {
	echo "Non implémenté.";
}

die();
?>

