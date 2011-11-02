<?php

/*
 * $Id: avertir_famille.php 7138 2011-06-05 17:37:14Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$variables_non_protegees = 'yes';

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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/avertir_famille.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Avertir famille incident', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/avertir_famille.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Avertir famille incident', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

if(strtolower(substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

$id_incident=isset($_POST['id_incident']) ? $_POST['id_incident'] : (isset($_GET['id_incident']) ? $_GET['id_incident'] : NULL);

$id_communication=isset($_POST['id_communication']) ? $_POST['id_communication'] : (isset($_GET['id_communication']) ? $_GET['id_communication'] : NULL);

$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL);

$msg="";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: Avertir la famille";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

$gepiSchoolPays=strtolower(getSettingValue('gepiSchoolPays'));

/*
loadSettings();
foreach($gepiSettings as $key => $value) {
	echo "\$gepiSettings['$key']=$value<br />";
}
*/

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Index</a>\n";

echo " | <a href='saisie_incident.php?id_incident=$id_incident' onclick=\"return confirm_abandon (this, change, '$themessage')\">Retour incident n°$id_incident</a>\n";

echo " | <a href='traiter_incident.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Liste des incidents</a>\n";

if(isset($id_communication)) {
	// Est-ce qu'on propose de modifier?
	// On ne garde pas trace de ce qui a déjà été envoyé...
	// Ou alors il faudrait un champ Révision dans s_communication


}


//echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
echo "<form enctype='multipart/form-data' action='avertir_famille_html.php' method='post' name='formulaire' target='_blank'>\n";

echo "<p>Avertir la famille de ";
echo p_nom($ele_login);

$tmp_tab=get_class_from_ele_login($ele_login);
if(isset($tmp_tab['liste_nbsp'])) {echo " <em style='font-size:x-small;'>(".$tmp_tab['liste_nbsp'].")</em>";}
echo "</p>\n";
echo "<blockquote>\n";

echo "<table class='boireaus' border='1' summary='Communication'>\n";
echo "<tr class='lig1'>\n";
echo "<td style='font-weight:bold; text-align:left; vertical-align: top;'>Nature&nbsp;:</td>\n";
echo "<td style='text-align:left;' colspan='4'>\n";
echo "<select name='nature' onchange='changement();'>\n";
echo "<option value='html'>HTML</option>\n";
echo "<option value='pdf'>PDF</option>\n";
echo "<option value='mail'>Mail</option>\n";
echo "</select>\n";
echo "</td>\n";
echo "</tr>\n";

/*
$sql="SELECT rp.nom, rp.prenom, rp.civilite, rp.pers_id, rp.adr_id, r.resp_legal FROM resp_pers rp, responsables2 r, eleves e WHERE e.ele_id=r.ele_id AND r.pers_id=rp.pers_id AND e.login='$ele_login' ORDER BY r.resp_legal;";
$res_dest=mysql_query($sql);
if(mysql_num_rows($res_dest)==0) {
	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold; text-align:left; vertical-align: top;'>Destinataires&nbsp;:</td>\n";
	echo "<td colspan='4'>Aucun destinataire n'a été trouvé dans la table 'resp_pers'.</td>\n";
	echo "</tr>\n";
}
else {
	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold; text-align:left; vertical-align: top;' rowspan='".mysql_num_rows($res_dest)."'>Destinataires&nbsp;:</td>\n";
	$cpt=0;
	while($lig_dest=mysql_fetch_object($res_dest)) {
		if($cpt>0) {echo "<tr class='lig-1'>";}
		echo "<td style='text-align:left;'>\n";

		// Adresse:
		$sql="SELECT * FROM resp_adr WHERE adr_id='$lig_dest->adr_id';";
		$res_adr=mysql_query($sql);
		if(mysql_num_rows($res_adr)==0) {
			echo "<span style='color:red;'>-</span>\n";
		}
		else {
			echo "<input type='checkbox' name='destinataire[]' id='destinataire_".$lig_dest->pers_id."' value='$lig_dest->pers_id' />";
			//echo "<input type='checkbox' name='destinataire[]' id='destinataire_".$lig_dest->pers_id."' value='$lig_dest->resp_legal' />";
		}
		echo "</td>\n";

		echo "<td style='text-align:left;'>\n";
		echo "<label for='destinataire_".$lig_dest->pers_id."' style='cursor: pointer;'>\n";
		echo " ".$lig_dest->civilite." ".strtoupper($lig_dest->nom)." ".ucwords(strtolower($lig_dest->prenom));
		echo "</label>\n";
		echo "</td>\n";

		echo "<td style='text-align:center;'>\n";
		echo " (<em>resp.légal ".$lig_dest->resp_legal."</em>)";
		echo "</td>\n";

		echo "<td style='text-align:left; font-size: x-small;'>\n";
		// Adresse:
		//$sql="SELECT * FROM resp_adr WHERE adr_id='$lig_dest->adr_id';";
		//$res_adr=mysql_query($sql);
		if(mysql_num_rows($res_adr)==0) {
			echo "<span style='color:red;'>Pas d'adresse</span>\n";
		}
		else {
			$lig_adr=mysql_fetch_object($res_adr);

			if($lig_adr->adr1!="") {echo $lig_adr->adr1."<br />\n";}
			if($lig_adr->adr2!="") {echo $lig_adr->adr2."<br />\n";}
			if($lig_adr->adr3!="") {echo $lig_adr->adr3."<br />\n";}
			if($lig_adr->adr4!="") {echo $lig_adr->adr4."<br />\n";}
			if($lig_adr->cp!="") {echo $lig_adr->cp.", \n";}
			if($lig_adr->commune!="") {echo $lig_adr->commune.", \n";}
			if(($lig_adr->pays!="")&&(strtolower($lig_adr->pays)!=$gepiSchoolPays)) {echo "<br />\n$lig_adr->pays";}

		}
		echo "</td>\n";
		//echo "<br />\n";
		echo "</tr>\n";
		$cpt++;
	}
}
//echo "</td>\n";
//echo "</tr>\n";
*/


$sql="SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e WHERE e.ele_id=r.ele_id AND r.pers_id=rp.pers_id AND e.login='$ele_login' AND (r.resp_legal='1' OR r.resp_legal='2') ORDER BY r.resp_legal;";
//echo "$sql<br />\n";
$res_dest=mysql_query($sql);
$nb_resp_legaux=mysql_num_rows($res_dest);

$sql="SELECT rp.nom, rp.prenom, rp.civilite, rp.pers_id, rp.adr_id, r.resp_legal, ra.* FROM resp_pers rp, responsables2 r, eleves e, resp_adr ra WHERE ra.adr_id=rp.adr_id AND e.ele_id=r.ele_id AND r.pers_id=rp.pers_id AND e.login='$ele_login' AND (r.resp_legal='1' OR r.resp_legal='2') ORDER BY r.resp_legal;";
$res_dest=mysql_query($sql);
if(mysql_num_rows($res_dest)>0) {
	while($lig=mysql_fetch_object($res_dest)) {
		$num=$lig->resp_legal-1;

		$tab_resp[$num]=array();

		$tab_resp[$num]['pers_id']=$lig->pers_id;
		$tab_resp[$num]['nom']=$lig->nom;
		$tab_resp[$num]['prenom']=$lig->prenom;
		$tab_resp[$num]['civilite']=$lig->civilite;

		$tab_resp[$num]['adr_id']=$lig->adr_id;
		$tab_resp[$num]['adr1']=$lig->adr1;
		$tab_resp[$num]['adr2']=$lig->adr2;
		$tab_resp[$num]['adr3']=$lig->adr3;
		$tab_resp[$num]['adr4']=$lig->adr4;
		$tab_resp[$num]['cp']=$lig->cp;
		$tab_resp[$num]['commune']=$lig->commune;
		$tab_resp[$num]['pays']=$lig->pays;
	}

	$nb_adr=1;
	if(count($tab_resp)==2) {
		// On compare les adresses
		if($tab_resp[0]['adr_id']==$tab_resp[1]['adr_id']) {
			// Une seule adresse
			$nb_adr=1;
		}
		elseif(($tab_resp[0]['adr1']!=$tab_resp[1]['adr1'])||
			($tab_resp[0]['adr2']!=$tab_resp[1]['adr2'])||
			($tab_resp[0]['adr3']!=$tab_resp[1]['adr3'])||
			($tab_resp[0]['adr4']!=$tab_resp[1]['adr4'])||
			($tab_resp[0]['cp']!=$tab_resp[1]['cp'])||
			($tab_resp[0]['commune']!=$tab_resp[1]['commune'])) {
			// Deux adresses
			$nb_adr=2;
		}
		else {
			// Une seule adresse
			$nb_adr=1;
		}
	}
	elseif($nb_resp_legaux>1) {
		// Il manque un resp_legal... qui n'aurait pas d'adresse
		if(isset($tab_resp[0]['pers_id'])) {
			$num_resp_sans_adr=2;
		}
		else {
			$num_resp_sans_adr=1;
		}

		$sql="SELECT rp.nom, rp.prenom, rp.civilite, rp.pers_id, rp.adr_id, r.resp_legal FROM resp_pers rp, responsables2 r, eleves e WHERE e.ele_id=r.ele_id AND r.pers_id=rp.pers_id AND e.login='$ele_login' AND r.resp_legal='$num_resp_sans_adr';";
		$res_dest=mysql_query($sql);
		if(mysql_num_rows($res_dest)>0) {
			$nb_adr=2;

			// Il y a un autre responsable légal
			$lig=mysql_fetch_object($res_dest);
			$num=$lig->resp_legal-1;

			$tab_resp[$num]=array();

			$tab_resp[$num]['pers_id']=$lig->pers_id;
			$tab_resp[$num]['nom']=$lig->nom;
			$tab_resp[$num]['prenom']=$lig->prenom;
			$tab_resp[$num]['civilite']=$lig->civilite;

			$tab_resp[$num]['adr_id']=$lig->adr_id;
		}
	}
}
else {
	// Aucune adresse pour les resp_legal 1 et 2
	$sql="SELECT rp.nom, rp.prenom, rp.civilite, rp.pers_id, rp.adr_id, r.resp_legal FROM resp_pers rp, responsables2 r, eleves e WHERE e.ele_id=r.ele_id AND r.pers_id=rp.pers_id AND e.login='$ele_login' ORDER BY r.resp_legal;";
	$res_dest=mysql_query($sql);

	$nb_adr=mysql_num_rows($res_dest);
	if($nb_adr==0) {
		/*
		echo "<tr class='lig-1'>\n";
		echo "<td style='font-weight:bold; text-align:left; vertical-align: top;'>Destinataires&nbsp;:</td>\n";
		echo "<td colspan='4'>Aucun destinataire n'a été trouvé dans la table 'resp_pers'.</td>\n";
		echo "</tr>\n";
		*/
	}
	else {
		while($lig=mysql_fetch_object($res_dest)) {
			$num=$lig->resp_legal-1;

			$tab_resp[$num]=array();

			$tab_resp[$num]['pers_id']=$lig->pers_id;
			$tab_resp[$num]['nom']=$lig->nom;
			$tab_resp[$num]['prenom']=$lig->prenom;
			$tab_resp[$num]['civilite']=$lig->civilite;

			$tab_resp[$num]['adr_id']=$lig->adr_id;
		}
	}
}


$cpt=0;
if($nb_adr==0) {
	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold; text-align:left; vertical-align: top;'>Destinataires&nbsp;:</td>\n";
	echo "<td colspan='4'>Aucun responsable n'a été trouvé dans la table 'resp_pers'.</td>\n";
	echo "</tr>\n";
}
elseif($nb_adr==1) {
	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold; text-align:left; vertical-align: top;' rowspan='".$nb_resp_legaux."'>Destinataires&nbsp;:</td>\n";

	if(count($tab_resp)==1) {
		echo "<td style='text-align:center;'>\n";
	}
	else {
		echo "<td style='text-align:center;' rowspan='".count($tab_resp)."'>\n";
	}

	if(isset($tab_resp[0]['adr1'])) {
		echo "<input type='checkbox' name='destinataire[]' id='destinataire_".$cpt."' value='".$tab_resp[0]['pers_id']."' />";
	}
	elseif(isset($tab_resp[1]['adr1'])) {
		echo "<input type='checkbox' name='destinataire[]' id='destinataire_".$cpt."' value='".$tab_resp[1]['pers_id']."' />";
	}
	else {
		echo "<span style='color:red;'>-</span>\n";
	}
	echo "</td>\n";


	for($i=0;$i<count($tab_resp);$i++) {
		if($i>0) {echo "<tr class='lig-1'>\n";}
		echo "<td style='text-align: left;'>\n";
		echo "<label for='destinataire_".$cpt."' style='cursor: pointer;'>\n";
		echo " ".$tab_resp[$i]['civilite']." ".strtoupper($tab_resp[$i]['nom'])." ".ucwords(strtolower($tab_resp[$i]['prenom']));
		echo "</label>\n";

		//echo "<span style='color:green;'>".$tab_resp[$i]['adr_id']."</span>";

		echo "</td>\n";

		if($i==0) {
			if(count($tab_resp)==1) {
				echo "<td style='text-align:center;'>\n";
			}
			else {
				echo "<td style='text-align:center;' rowspan='".count($tab_resp)."'>\n";
			}

			if((!isset($tab_resp[0]['adr1']))&&(!isset($tab_resp[1]['adr1']))) {
				echo "<span style='color:red;'>Pas d'adresse</span>";
			}
			elseif(isset($tab_resp[0]['adr1'])) {
				if($tab_resp[0]['adr1']!="") {echo $tab_resp[0]['adr1']."<br />\n";}
				if($tab_resp[0]['adr2']!="") {echo $tab_resp[0]['adr2']."<br />\n";}
				if($tab_resp[0]['adr3']!="") {echo $tab_resp[0]['adr3']."<br />\n";}
				if($tab_resp[0]['adr4']!="") {echo $tab_resp[0]['adr4']."<br />\n";}
				if($tab_resp[0]['cp']!="") {echo $tab_resp[0]['cp'].", \n";}
				if($tab_resp[0]['commune']!="") {echo $tab_resp[0]['commune']."\n";}
				if(($tab_resp[0]['pays']!="")&&(strtolower($tab_resp[0]['pays'])!=$gepiSchoolPays)) {echo "<br />\n".$tab_resp[0]['pays'];}
			}
			else {
				if($tab_resp[1]['adr1']!="") {echo $tab_resp[1]['adr1']."<br />\n";}
				if($tab_resp[1]['adr2']!="") {echo $tab_resp[1]['adr2']."<br />\n";}
				if($tab_resp[1]['adr3']!="") {echo $tab_resp[1]['adr3']."<br />\n";}
				if($tab_resp[1]['adr4']!="") {echo $tab_resp[1]['adr4']."<br />\n";}
				if($tab_resp[1]['cp']!="") {echo $tab_resp[1]['cp'].", \n";}
				if($tab_resp[1]['commune']!="") {echo $tab_resp[1]['commune']."\n";}
				if(($tab_resp[1]['pays']!="")&&(strtolower($tab_resp[1]['pays'])!=$gepiSchoolPays)) {echo "<br />\n".$tab_resp[1]['pays'];}
			}
			echo "</td>\n";
		}
		echo "</tr>\n";
	}
}
else {
	// Deux adresses
	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold; text-align:left; vertical-align: top;' rowspan='".$nb_resp_legaux."'>Destinataires&nbsp;:</td>\n";

	for($i=0;$i<count($tab_resp);$i++) {
		if($i>0) {echo "<tr class='lig-1'>\n";}
		echo "<td style='text-align:center;'>\n";
		if(isset($tab_resp[$i]['adr1'])) {
			echo "<input type='checkbox' name='destinataire[]' id='destinataire_".$i."' value='".$tab_resp[$i]['pers_id']."' />";
		}
		else {
			echo "<span style='color:red;'>-</span>\n";
		}
		echo "</td>\n";

		echo "<td style='text-align: left;'>\n";
		echo "<label for='destinataire_".$i."' style='cursor: pointer;'>\n";
		echo " ".$tab_resp[$i]['civilite']." ".strtoupper($tab_resp[$i]['nom'])." ".ucwords(strtolower($tab_resp[$i]['prenom']));
		echo "</label>\n";
		echo "</td>\n";

		echo "<td style='text-align:left;'>\n";
		if(!isset($tab_resp[$i]['adr1'])) {
			echo "<span style='color:red;'>Pas d'adresse</span>";
		}
		else {
			if($tab_resp[$i]['adr1']!="") {echo $tab_resp[$i]['adr1']."<br />\n";}
			if($tab_resp[$i]['adr2']!="") {echo $tab_resp[$i]['adr2']."<br />\n";}
			if($tab_resp[$i]['adr3']!="") {echo $tab_resp[$i]['adr3']."<br />\n";}
			if($tab_resp[$i]['adr4']!="") {echo $tab_resp[$i]['adr4']."<br />\n";}
			if($tab_resp[$i]['cp']!="") {echo $tab_resp[$i]['cp'].", \n";}
			if($tab_resp[$i]['commune']!="") {echo $tab_resp[$i]['commune']."\n";}
			if(($tab_resp[$i]['pays']!="")&&(strtolower($tab_resp[$i]['pays'])!=$gepiSchoolPays)) {echo "<br />\n".$tab_resp[$i]['pays'];}
		}
		echo "</td>\n";

		echo "</tr>\n";
	}
}



echo "<tr class='lig1'>\n";
echo "<td style='font-weight:bold; text-align:left; vertical-align: top;'>Courrier&nbsp;:</td>\n";
echo "<td style='text-align:left;' colspan='4'>\n";
echo "<textarea id=\"courrier\" class='wrap' name=\"no_anti_inject_courrier\" rows='12' cols='80' onchange=\"changement()\">";

if(!isset($id_communication)) {
	// Afficher les détails de l'incident.

	$sql="SELECT * FROM s_incidents WHERE id_incident='$id_incident';";
	$res_incident=mysql_query($sql);
	if(mysql_num_rows($res_incident)==0) {
		echo "??? L'incident n°$id_incident n'existe pas ???";
	}
	else {
		$lig_inc=mysql_fetch_object($res_incident);
		echo "Nature: $lig_inc->nature

Description:
$lig_inc->description";
	}
}

echo "</textarea>\n";
echo "</td>\n";
echo "</tr>\n";

echo "</table>\n";


echo "<input type='hidden' name='ele_login' value=\"$ele_login\" />\n";
echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";

echo "</blockquote>\n";

echo "</form>\n";

echo "<p style='color:red;'><b>A FAIRE:</b> Afficher aussi les numéros de téléphone.<br />
Ne proposer 'mail' que si les adresses mail des resp sont renseignées.<br />
Pouvoir enregistrer le fait que les parents ont été avertis.<br />
Comment conserver aussi une trace des courriers envoyés? et pouvoir effacer les essais.</p>\n";

echo "<p><br /></p>\n";

rappel_incident($id_incident);

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>