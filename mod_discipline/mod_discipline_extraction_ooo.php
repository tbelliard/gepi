<?php
/*
* $Id$
*
* Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_discipline/mod_discipline_extraction_ooo.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_discipline/mod_discipline_extraction_ooo.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Discipline : Extrait OOo des incidents',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/mod_discipline_extraction_ooo.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline : Extrait OOo des incidents', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/mod_discipline_extraction_ooo.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline : Extrait OOo des incidents', '');";
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// debug_var();

$mod_disc_terme_incident=getSettingValue('mod_disc_terme_incident');
if($mod_disc_terme_incident=="") {$mod_disc_terme_incident="incident";}

$mod_disc_terme_sanction=getSettingValue('mod_disc_terme_sanction');
if($mod_disc_terme_sanction=="") {$mod_disc_terme_sanction="sanction";}

$sql_classes="SELECT DISTINCT id, classe, nom_complet FROM classes ORDER BY classe, nom_complet;";
$option_toutes_classes="Toutes classes confondues";
$sql_ele="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, s_protagonistes sp WHERE e.login=sp.login ORDER BY e.nom, e.prenom;";
$sql_ele_responsable="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, s_protagonistes sp WHERE e.login=sp.login AND sp.qualite='Responsable' ORDER BY e.nom, e.prenom;";
if($_SESSION['statut']=="professeur") {
	if(getSettingAOui('extractDiscProf')) {
		$sql_classes="SELECT DISTINCT c.id, c.classe, c.nom_complet FROM classes c, 
						j_groupes_classes jgc, 
						j_groupes_professeurs jgp 
						WHERE 
						c.id=jgc.id_classe AND 
						jgc.id_groupe=jgp.id_groupe AND 
						jgp.login='".$_SESSION['login']."' 
						ORDER BY classe, nom_complet;";
		$option_toutes_classes="Toutes mes classes";
		$sql_ele="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, 
											s_protagonistes sp, 
											j_eleves_groupes jeg, 
											j_groupes_professeurs jgp 
										WHERE e.login=sp.login AND 
											e.login=jeg.login AND 
											jeg.id_groupe=jgp.id_groupe AND 
											jgp.login='".$_SESSION['login']."' 
										ORDER BY e.nom, e.prenom;";
		$sql_ele_responsable="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, 
											s_protagonistes sp, 
											j_eleves_groupes jeg, 
											j_groupes_professeurs jgp 
										WHERE e.login=sp.login AND 
											e.login=jeg.login AND 
											jeg.id_groupe=jgp.id_groupe AND 
											jgp.login='".$_SESSION['login']."' AND 
											sp.qualite='Responsable' 
										ORDER BY e.nom, e.prenom;";
	}
	elseif(getSettingAOui('extractDiscProfP')) {
		$sql_classes="SELECT DISTINCT c.id, c.classe, c.nom_complet FROM classes c, 
						j_eleves_classes jec, 
						j_eleves_professeurs jep 
						WHERE 
						c.id=jec.id_classe AND 
						jec.login=jep.login AND 
						jep.professeur='".$_SESSION['login']."' 
						ORDER BY classe, nom_complet;";
		$option_toutes_classes="Toutes mes classes";
		$sql_ele="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, 
											s_protagonistes sp, 
											j_eleves_professeurs jep 
										WHERE e.login=sp.login AND 
											e.login=jep.login AND 
											jep.professeur='".$_SESSION['login']."' 
										ORDER BY e.nom, e.prenom;";
		$sql_ele_responsable="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, 
											s_protagonistes sp, 
											j_eleves_professeurs jep 
										WHERE e.login=sp.login AND 
											e.login=jep.login AND 
											jep.professeur='".$_SESSION['login']."' AND 
											sp.qualite='Responsable' 
										ORDER BY e.nom, e.prenom;";
	}
}

/*
$prefixe_generation_hors_dossier_mod_ooo="../mod_ooo/";

include_once('../mod_ooo/lib/lib_mod_ooo.php'); // les fonctions
$nom_fichier_modele_ooo =''; // variable a initialiser a blanc pour inclure le fichier suivant et eviter une notice. Pour les autres inclusions, cela est inutile.
include_once('../mod_ooo/lib/chemin.inc.php'); // le chemin des dossiers contenant les  modèles
*/

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

if((isset($mode))&&($mode=="choix")) {
	//**************** EN-TETE *******************************
	$titre_page = "Extraction discipline";
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE ****************************

	echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post' target='_blank'>
	<fieldset id='infosPerso' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); '>
		<legend style='border: 1px solid grey; background-color: white; '>Choix de l'extraction</legend>

		<p>Extraire les ".$mod_disc_terme_incident."s&nbsp;:<br />
		<select name='id_classe_incident'>
			<option value='' selected='true'>$option_toutes_classes</option>";

//$sql_classes="SELECT DISTINCT id, classe, nom_complet FROM classes ORDER BY classe, nom_complet;";
$res=mysqli_query($GLOBALS["mysqli"], $sql_classes);
while($lig=mysqli_fetch_object($res)) {
	echo "
			<option value='$lig->id'>$lig->classe ($lig->nom_complet)</option>";
}
echo "
		</select><br />
		Uniquement les ".$mod_disc_terme_incident."s concernant (<em>pas nécessairement en responsable</em>) l'élève suivant&nbsp;:<br />
		<select name='protagoniste_incident'>
			<option value='' selected='true'>Tous élèves confondus</option>";
//$sql_ele="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, s_protagonistes sp WHERE e.login=sp.login ORDER BY e.nom, e.prenom;";
$res=mysqli_query($GLOBALS["mysqli"], $sql_ele);
while($lig=mysqli_fetch_object($res)) {
	echo "
			<option value='$lig->login'>".casse_mot($lig->nom,"maj")." ".casse_mot($lig->prenom,"majf2")."</option>";
}
echo "
		</select>
		</p>

		<input type='submit' value='Extraire' />
	</fieldset>
</form>\n";


echo "<form action='".$_SERVER['PHP_SELF']."' name='form2' method='post' target='_blank'>
	<fieldset class='fieldset_opacite50' style='margin-top:1em; '>
		<legend style='border: 1px solid grey; background-color: white; '>Autre mode d'extraction</legend>

		<p>Extraire les ".$mod_disc_terme_incident."s concernant l'élève suivant (<em>en tant que responsable</em>)&nbsp;:<br />
		<select name='protagoniste_incident'>
			<!--option value='' selected='true'>Tous élèves confondus</option-->
			<option value='' selected='true'>---</option>";

$res=mysqli_query($GLOBALS["mysqli"], $sql_ele_responsable);
while($lig=mysqli_fetch_object($res)) {
	echo "
			<option value='$lig->login'>".casse_mot($lig->nom,"maj")." ".casse_mot($lig->prenom,"majf2")."</option>";
}
echo "
		</select><br />
		<input type='checkbox' name='avec_bloc_adresse_resp' id='avec_bloc_adresse_resp' value='y' /><label for='avec_bloc_adresse_resp' id='texte_avec_bloc_adresse_resp'> Avec le bloc adresse responsable/parent.</label><br />
		<input type='checkbox' name='anonymer_autres_protagonistes_eleves' id='anonymer_autres_protagonistes_eleves' value='y' /><label for='anonymer_autres_protagonistes_eleves' id='texte_anonymer_autres_protagonistes_eleves'> Anonymer (<em>remplacer par des XXXXXXXXX</em>) ce qui concerne les autres protagonistes élèves des ".$mod_disc_terme_incident."s extraits.</label><br />
		<input type='checkbox' name='cacher_autres_protagonistes_eleves' id='cacher_autres_protagonistes_eleves' value='y' /><label for='cacher_autres_protagonistes_eleves' id='texte_cacher_autres_protagonistes_eleves'> Cacher ce qui concerne les autres protagonistes élèves des ".$mod_disc_terme_incident."s extraits.</label><br />

		<!--input type='checkbox' name='debug' id='debug' value='y' /><label for='debug' id='texte_mode_test' style='color:red'> Debug pour contrôler les indices du tableau produit</label><br />

		<input type='checkbox' name='mode_test' id='mode_test' value='y' /><label for='mode_test' id='texte_mode_test' style='color:red'> Mode test...<br />
		Pour le moment, ça ne fonctionne pas.<br />
		C'est le même problème que pour l'adresse<br />
		Editer/modifier le fichier mod_discipline_liste_incidents2b.odt</label><br /-->

		<input type='hidden' name='mode' value='extract_responsable' />
		</p>

		<input type='submit' value='Extraire' />
	</fieldset>
</form>\n";

// 20160508
echo "<form action='".$_SERVER['PHP_SELF']."' name='form2' method='post' target='_blank'>
	<fieldset class='fieldset_opacite50' style='margin-top:1em; '>
		<legend style='border: 1px solid grey; background-color: white; '>Bilan classe</legend>

		<p>Extraire les ".$mod_disc_terme_incident."s&nbsp;:<br />
		<select name='id_classe_incident'>
			<option value='' selected='true'>$option_toutes_classes</option>";

$res=mysqli_query($GLOBALS["mysqli"], $sql_classes);
while($lig=mysqli_fetch_object($res)) {
	echo "
			<option value='$lig->id'>$lig->classe ($lig->nom_complet)</option>";
}
echo "
		</select><br />";
/*
echo " concernant l'élève suivant (<em>en tant que responsable</em>)&nbsp;:<br />
		<select name='protagoniste_incident'>
			<!--option value='' selected='true'>Tous élèves confondus</option-->
			<option value='' selected='true'>---</option>";

//$sql="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, s_protagonistes sp WHERE e.login=sp.login ORDER BY e.nom, e.prenom;";
// A FAIRE: Permettre de choisir les 'qualite'
//$sql_ele_responsable="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, s_protagonistes sp WHERE e.login=sp.login AND sp.qualite='Responsable' ORDER BY e.nom, e.prenom;";
$res=mysqli_query($GLOBALS["mysqli"], $sql_ele_responsable);
while($lig=mysqli_fetch_object($res)) {
	echo "
			<option value='$lig->login'>".casse_mot($lig->nom,"maj")." ".casse_mot($lig->prenom,"majf2")."</option>";
}
echo "
		</select><br />";
*/

$sql="SELECT MAX(num_periode) AS maxper FROM periodes;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	$lig=mysqli_fetch_object($res);

	echo "
		pour <input type='radio' name='num_periode' id='num_periode_vide' value='' checked /><label for='num_periode_vide' id='texte_num_periode_vide'> toute l'année</label><br />";
	$maxper=$lig->maxper;
	for($i=1;$i<=$maxper;$i++) {
		if($i==1) {
			echo "
		ou seulement une période&nbsp;:<br />";
		}
		echo "
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='num_periode' id='num_periode_".$i."' value='".$i."' /><label for='num_periode_".$i."' id='texte_num_periode_".$i."'> Période ".$i."</label><br />";
	}
}

$sql="SELECT DISTINCT id_nature, nature FROM s_types_sanctions2 ORDER BY rang, type, nature;";
$res_sts=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res_sts)>0) {
	echo "
		<p style='margin-left:3em;text-indent:-3em;'>Afficher pour chaque élève un tableau des totaux de sanctions de <span title='si le nombre de sanctions est non nul.'>chaque type(*)</span> pour&nbsp;:<br />";
	while($lig_sts=mysqli_fetch_object($res_sts)) {
		$checked="";
		if(getPref($_SESSION['login'], 'mod_disc_extract_bilan_id_nature_sanction_'.$lig_sts->id_nature, "n")=='y') {
			$checked=" checked";
		}
		echo "
		<input type='checkbox' name='id_nature[]' id='id_nature_".$lig_sts->id_nature."' value='".$lig_sts->id_nature."'$checked /><label for='id_nature_".$lig_sts->id_nature."' id='texte_id_nature_".$lig_sts->nature."'>".$lig_sts->nature."</label><br />";
	}
	echo "</p>";
}

$checked_anonymer=(getPref($_SESSION['login'], 'mod_disc_extract_bilan_anonymer', "n")=="y" ? " checked" : "");
$checked_cacher=(getPref($_SESSION['login'], 'mod_disc_extract_bilan_cacher_autres_protagonistes', "n")=="y" ? " checked" : "");
$checked_extraire_incidents_avec_sanction=(getPref($_SESSION['login'], 'mod_disc_extract_bilan_extraire_incidents_avec_sanction', "n")=="y" ? " checked" : "");

echo "
		<!--
		<input type='checkbox' name='avec_bloc_adresse_resp' id='avec_bloc_adresse_resp' value='y' /><label for='avec_bloc_adresse_resp' id='texte_avec_bloc_adresse_resp'> Avec le bloc adresse responsable/parent.</label><br />
		-->
		<input type='checkbox' name='anonymer_autres_protagonistes_eleves' id='anonymer_autres_protagonistes_eleves_bilan' value='y'".$checked_anonymer." /><label for='anonymer_autres_protagonistes_eleves_bilan' id='texte_anonymer_autres_protagonistes_eleves_bilan'> Anonymer (<em>remplacer par des XXXXXXXXX</em>) ce qui concerne les autres protagonistes élèves des ".$mod_disc_terme_incident."s extraits.</label><br />
		<input type='checkbox' name='cacher_autres_protagonistes_eleves' id='cacher_autres_protagonistes_eleves_bilan' value='y'".$checked_cacher." /><label for='cacher_autres_protagonistes_eleves_bilan' id='texte_cacher_autres_protagonistes_eleves_bilan'> Cacher ce qui concerne les autres protagonistes élèves des ".$mod_disc_terme_incident."s extraits.</label><br />

		<input type='checkbox' name='extraire_incidents_avec_sanction' id='extraire_incidents_avec_sanction' value='y'".$checked_extraire_incidents_avec_sanction." /><label for='extraire_incidents_avec_sanction' id='texte_extraire_incidents_avec_sanction'> N'extraire que les ".$mod_disc_terme_incident."s avec ".$mod_disc_terme_sanction.".</label><br />

		<input type='checkbox' name='extraire_incidents_avec_sanction_choix' id='extraire_incidents_avec_sanction_choix' value='y' /><label for='extraire_incidents_avec_sanction_choix' id='texte_extraire_incidents_avec_sanction_choix'> N'extraire que les ".$mod_disc_terme_incident."s avec ".$mod_disc_terme_sanction." parmi celles cochées ci-dessus pour les totaux affichés.</label><br />

		<input type='checkbox' name='debug' id='debug' value='y' /><label for='debug' id='texte_mode_test' style='color:red'> Debug pour contrôler les indices du tableau produit</label><br />

		<!--
		<input type='checkbox' name='mode_test' id='mode_test' value='y' /><label for='mode_test' id='texte_mode_test' style='color:red'> Mode test...<br />
		Pour le moment, ça ne fonctionne pas.<br />
		C'est le même problème que pour l'adresse<br />
		Editer/modifier le fichier mod_discipline_liste_incidents2b.odt</label><br /-->

		<input type='hidden' name='mode' value='classe2' />
		</p>

		<input type='submit' value='Extraire' />

	</fieldset>
</form>\n";

	// A FAIRE: Permettre de choisir les 'qualite',...
	echo "<p style='margin-top:1em; margin-left:5em; text-indent:-5em;'><em>NOTES&nbsp;:</em> Seuls les élèves avec $mod_disc_terme_incident déclaré sont proposés ici.<br />Les élèves ne sont pas nécessairement responsables de l'".$mod_disc_terme_incident.".<br />Ils peuvent être témoin, victime,...</p>";

	require("../lib/footer.inc.php");
	die();
}
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
elseif((isset($mode))&&($mode=="extract_responsable")) {

	// A FAIRE : Ajouter un test sur l'accès aux infos parents pour la personne connectée.
	$avec_bloc_adresse_resp=isset($_POST['avec_bloc_adresse_resp']) ? $_POST['avec_bloc_adresse_resp'] : (isset($_GET['avec_bloc_adresse_resp']) ? $_GET['avec_bloc_adresse_resp'] : "n");
	$anonymer_autres_protagonistes_eleves=isset($_POST['anonymer_autres_protagonistes_eleves']) ? $_POST['anonymer_autres_protagonistes_eleves'] : (isset($_GET['anonymer_autres_protagonistes_eleves']) ? $_GET['anonymer_autres_protagonistes_eleves'] : "n");
	$cacher_autres_protagonistes_eleves=isset($_POST['cacher_autres_protagonistes_eleves']) ? $_POST['cacher_autres_protagonistes_eleves'] : (isset($_GET['cacher_autres_protagonistes_eleves']) ? $_GET['cacher_autres_protagonistes_eleves'] : "n");

	$sql_restriction_dates="";
	$date_debut=isset($_POST['date_debut']) ? $_POST['date_debut'] : (isset($_GET['date_debut']) ? $_GET['date_debut'] : NULL);
	$date_fin=isset($_POST['date_fin']) ? $_POST['date_fin'] : (isset($_GET['date_fin']) ? $_GET['date_fin'] : NULL);
	if(isset($date_debut)) {
		$sql_restriction_dates.=" AND date>='".$date_debut."'";
	}
	if(isset($date_fin)) {
		$sql_restriction_dates.=" AND date<='".$date_fin."'";
	}

	// A FAIRE : Pouvoir restreindre l'extraction à telles ou telles sanctions (s_types_sanctions2)

	//$path='../mod_ooo/'.$nom_dossier_modele_a_utiliser;

	require_once("../mod_discipline/sanctions_func_lib.php");

	// Ce champ n'est plus posté
	$id_classe_incident=isset($_POST['id_classe_incident']) ? $_POST['id_classe_incident'] : (isset($_GET['id_classe_incident']) ? $_GET['id_classe_incident'] : "");

	$chaine_criteres="";
	$date_incident="";
	$heure_incident="";
	$nature_incident="---";
	$protagoniste_incident=isset($_POST['protagoniste_incident']) ? $_POST['protagoniste_incident'] : (isset($_GET['protagoniste_incident']) ? $_GET['protagoniste_incident'] : "");
	$declarant_incident="---";
	$incidents_clos="y";

	// Actuellement on limite l'accès à un protagoniste en particulier
	if($protagoniste_incident=="") {
		echo "<p style='color:red'>Aucun protagoniste n'a été choisi.</p>";
		die();
	}

	if($_SESSION['statut']=="professeur") {
		if(!acces_extract_disc("", $protagoniste_incident)) {
			echo "<p style='color:red'>Vous n'avez pas accès au protagoniste choisi ($protagoniste_incident).</p>";
			die();
		}
	}

	if((!isset($id_classe_incident))||($id_classe_incident=="")) {
		$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp WHERE sp.id_incident=si.id_incident";
	}
	else {
		$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp, j_eleves_classes jec WHERE sp.id_incident=si.id_incident AND jec.id_classe='$id_classe_incident' AND jec.login=sp.login";
	}

	$ajout_sql="";
	if($date_incident!="") {$ajout_sql.=" AND si.date='$date_incident'";$chaine_criteres.="&amp;date_incident=$date_incident";}
	if($heure_incident!="") {$ajout_sql.=" AND si.heure='$heure_incident'";$chaine_criteres.="&amp;heure_incident=$heure_incident";}
	if($nature_incident!="---") {$ajout_sql.=" AND si.nature='$nature_incident'";$chaine_criteres.="&amp;nature_incident=$nature_incident";}
	if($protagoniste_incident!="") {$ajout_sql.=" AND sp.login='$protagoniste_incident'";$chaine_criteres.="&amp;protagoniste_incident=$protagoniste_incident";}


	// A FAIRE : Permettre de choisir les 'qualite', des dates,...
	//           Actuellement, on n'extrait par ce mode que les responsables
	$qualite="Responsable";

	$ajout_sql.=" AND sp.qualite='$qualite'";
	$chaine_criteres.="&amp;qualite=$qualite";

	//echo "\$declarant_incident=$declarant_incident<br />";

	if($declarant_incident!="---") {$ajout_sql.=" AND si.declarant='$declarant_incident'";$chaine_criteres.="&amp;declarant_incident=$declarant_incident";}

	if($id_classe_incident!="") {
		$chaine_criteres.="&amp;id_classe_incident=$id_classe_incident";
	}

	$sql.=$ajout_sql;
	$sql.=$sql_restriction_dates;
	$sql2=$sql;
	if($incidents_clos!="y") {$sql.=" AND si.etat!='clos'";}

	$sql.=")";
	$sql2.=")";

	$sql.=" ORDER BY date DESC, heure DESC;";
	$sql2.=" ORDER BY date DESC, heure DESC;";

	//echo "$sql<br />";
	//echo "$sql2<br />";

	$tab_lignes_OOo_eleve=array();
	$tab_lignes_OOo=array();

	// Test
	//$tab_lignes_OOo_eleve['eleve']['etab']=getSettingValue("gepiSchoolName");

	$tab_lignes_OOo_eleve['etab']=getSettingValue("gepiSchoolName");
	$tab_lignes_OOo_eleve['acad']=getSettingValue("gepiSchoolAcademie");
	$tab_lignes_OOo_eleve['adr1']=getSettingValue("gepiSchoolAdress1")." ".getSettingValue("gepiSchoolAdress2");
	$tab_lignes_OOo_eleve['cp']=getSettingValue("gepiSchoolZipCode");
	$tab_lignes_OOo_eleve['ville']=getSettingValue("gepiSchoolCity");

	// Extraire l'adresse des responsables/parents...
	// get_adresse_responsable($pers_id) retourne $tab_adresse
	// Voir bull_func.lib.php
	$sql_resp="SELECT rp.* FROM resp_pers rp, responsables2 r, eleves e WHERE e.ele_id=r.ele_id AND r.resp_legal='1' AND r.pers_id=rp.pers_id AND e.login='".$protagoniste_incident."';";
	$res_resp=mysqli_query($GLOBALS["mysqli"], $sql_resp);
	if(mysqli_num_rows($res_resp)==0) {
		$tab_lignes_OOo_eleve['responsable']["civilite"]="";
		$tab_lignes_OOo_eleve['responsable']["nom"]="";
		$tab_lignes_OOo_eleve['responsable']["prenom"]="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['adr_id']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['adr1']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['adr2']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['adr3']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['cp']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['commune']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['pays']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['en_ligne']="";

		$tab_lignes_OOo_eleve["resp_civilite"]="";
		$tab_lignes_OOo_eleve["resp_nom"]="";
		$tab_lignes_OOo_eleve["resp_prenom"]="";
		$tab_lignes_OOo_eleve['resp_adr_id']="";
		$tab_lignes_OOo_eleve['resp_adr1']="";
		$tab_lignes_OOo_eleve['resp_adr2']="";
		$tab_lignes_OOo_eleve['resp_adr3']="";
		$tab_lignes_OOo_eleve['resp_cp']="";
		$tab_lignes_OOo_eleve['resp_commune']="";
		$tab_lignes_OOo_eleve['resp_pays']="";
		$tab_lignes_OOo_eleve['resp_adr_en_ligne']="";
	}
	else {
		$lig_resp=mysqli_fetch_object($res_resp);
		$tab_lignes_OOo_eleve['responsable']["civilite"]=$lig_resp->civilite;
		$tab_lignes_OOo_eleve['responsable']["nom"]=$lig_resp->nom;
		$tab_lignes_OOo_eleve['responsable']["prenom"]=$lig_resp->prenom;
		$tab_adr_courante=get_adresse_responsable($lig_resp->pers_id);
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]=$tab_adr_courante;

		$tab_lignes_OOo_eleve["resp_civilite"]=$lig_resp->civilite;
		$tab_lignes_OOo_eleve["resp_nom"]=$lig_resp->nom;
		$tab_lignes_OOo_eleve["resp_prenom"]=$lig_resp->prenom;
		$tab_lignes_OOo_eleve['resp_adr_id']=$tab_adr_courante['adr_id'];
		$tab_lignes_OOo_eleve['resp_adr1']=$tab_adr_courante['adr1'];
		$tab_lignes_OOo_eleve['resp_adr2']=$tab_adr_courante['adr2'];
		$tab_lignes_OOo_eleve['resp_adr3']=$tab_adr_courante['adr3'];
		$tab_lignes_OOo_eleve['resp_cp']=$tab_adr_courante['cp'];
		$tab_lignes_OOo_eleve['resp_commune']=$tab_adr_courante['commune'];
		$tab_lignes_OOo_eleve['resp_pays']=$tab_adr_courante['pays'];
		$tab_lignes_OOo_eleve['resp_adr_en_ligne']=$tab_adr_courante['en_ligne'];
	}

	$nb_ligne=0;
	$res_incident=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig_incident=mysqli_fetch_object($res_incident)) {
		$tab_lignes_OOo[$nb_ligne]=array();

		$tab_lignes_OOo[$nb_ligne]['etab']=getSettingValue("gepiSchoolName");
		$tab_lignes_OOo[$nb_ligne]['acad']=getSettingValue("gepiSchoolAcademie");
		$tab_lignes_OOo[$nb_ligne]['adr1']=getSettingValue("gepiSchoolAdress1")." ".getSettingValue("gepiSchoolAdress2");
		$tab_lignes_OOo[$nb_ligne]['cp']=getSettingValue("gepiSchoolZipCode");
		$tab_lignes_OOo[$nb_ligne]['ville']=getSettingValue("gepiSchoolCity");

		$tab_lignes_OOo[$nb_ligne]['id_incident']=$lig_incident->id_incident;
		$tab_lignes_OOo[$nb_ligne]['declarant']=civ_nom_prenom($lig_incident->declarant,'');
		$tab_lignes_OOo[$nb_ligne]['date']=formate_date($lig_incident->date);
		$tab_lignes_OOo[$nb_ligne]['heure']=$lig_incident->heure;
		$tab_lignes_OOo[$nb_ligne]['nature']=$lig_incident->nature;
		$tab_lignes_OOo[$nb_ligne]['description']=$lig_incident->description;
		$tab_lignes_OOo[$nb_ligne]['etat']=$lig_incident->etat;

		// Lieu
		$tab_lignes_OOo[$nb_ligne]['lieu']=get_lieu_from_id($lig_incident->id_lieu);

		// Protagonistes
		$tab_protagonistes_eleves=array();
		$sql="SELECT * FROM s_protagonistes WHERE id_incident='$lig_incident->id_incident' ORDER BY statut,qualite,login;";
		$res2=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$tab_lignes_OOo[$nb_ligne]['protagonistes']="Aucun";
		}
		else {
			$liste_protagonistes="";
			while($lig2=mysqli_fetch_object($res2)) {

				if(($lig2->statut=='eleve')&&($cacher_autres_protagonistes_eleves=="y")&&($lig2->login!=$protagoniste_incident)) {
					// On n'affiche pas du tout l'info.
				}
				else {
					if($liste_protagonistes!="") {$liste_protagonistes.=", ";}
					if($lig2->statut=='eleve') {
						if(($anonymer_autres_protagonistes_eleves=="y")&&($lig2->login!=$protagoniste_incident)) {
							$liste_protagonistes.="XXX";
						}
						else {
							$liste_protagonistes.=get_nom_prenom_eleve($lig2->login,'avec_classe');
						}
						$tab_protagonistes_eleves[]=$lig2->login;
					}
					else {
						$liste_protagonistes.=civ_nom_prenom($lig2->login,'',"y");
					}

					if($lig2->qualite!='') {
						$liste_protagonistes.=" $lig2->qualite";
					}
				}
			}
		}
		$tab_lignes_OOo[$nb_ligne]['protagonistes']=$liste_protagonistes;

		$id_incident_courant=$lig_incident->id_incident;

		// Mesures prises
		$texte="";
		$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='prise'";
		//$texte.="<br />$sql";
		$res_t_incident=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_login_ele_mesure_prise=mysqli_num_rows($res_t_incident);

		if($nb_login_ele_mesure_prise>0) {
			while($lig_t_incident=mysqli_fetch_object($res_t_incident)) {
				if(($cacher_autres_protagonistes_eleves=="y")&&($lig_t_incident->login_ele!=$protagoniste_incident)) {
					// On n'affiche pas du tout l'info.
				}
				else {
					$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='prise' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
					$res_mes_ele=mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_mes_ele=mysqli_num_rows($res_mes_ele);

					if(($anonymer_autres_protagonistes_eleves=="y")&&($lig_t_incident->login_ele!=$protagoniste_incident)) {
						$texte.="XXX :";
					}
					else {
						$texte.=civ_nom_prenom($lig_t_incident->login_ele,'')." :";
					}
					while($lig_mes_ele=mysqli_fetch_object($res_mes_ele)) {
						$texte.=" ".$lig_mes_ele->mesure;
					}
					$texte.="\n";
				}
			}
		}
		$tab_lignes_OOo[$nb_ligne]['mesures_prises']=$texte;

		// Mesures demandees
		$texte="";
		$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='demandee' ORDER BY login_ele";
		//$texte.="<br />$sql";
		$res_t_incident2=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_login_ele_mesure_demandee=mysqli_num_rows($res_t_incident2);

		if($nb_login_ele_mesure_demandee>0) {
			while($lig_t_incident=mysqli_fetch_object($res_t_incident2)) {
				if(($cacher_autres_protagonistes_eleves=="y")&&($lig_t_incident->login_ele!=$protagoniste_incident)) {
					// On n'affiche pas du tout l'info.
				}
				else {
					$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='demandee' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
					$res_mes_ele=mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_mes_ele=mysqli_num_rows($res_mes_ele);

					if(($anonymer_autres_protagonistes_eleves=="y")&&($lig_t_incident->login_ele!=$protagoniste_incident)) {
						$texte.="XXX :";
					}
					else {
						$texte.=civ_nom_prenom($lig_t_incident->login_ele,'')." :";
					}
					while($lig_mes_ele=mysqli_fetch_object($res_mes_ele)) {
						$texte.=" ".$lig_mes_ele->mesure;
					}
					$texte.="\n";
				}
			}
		}
		$tab_lignes_OOo[$nb_ligne]['mesures_demandees']=$texte;


		// Sanctions
		$texte_sanctions="";
		for($i=0;$i<count($tab_protagonistes_eleves);$i++) {
			$ele_login=$tab_protagonistes_eleves[$i];

			if(($cacher_autres_protagonistes_eleves=="y")&&($ele_login!=$protagoniste_incident)) {
				// On n'affiche pas du tout l'info.
			}
			else {

				if(($anonymer_autres_protagonistes_eleves=="y")&&($ele_login!=$protagoniste_incident)) {
					$designation_eleve="XXX";
				}
				else {
					$designation_eleve=civ_nom_prenom($ele_login,'');
				}

				// Retenues
				$sql="SELECT * FROM s_sanctions s, s_retenues sr WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND sr.id_sanction=s.id_sanction ORDER BY sr.date, sr.heure_debut;";
				//echo "$sql<br />\n";
				$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sanction)>0) {
					$texte_sanctions.=$designation_eleve;

					while($lig_sanction=mysqli_fetch_object($res_sanction)) {
						//$texte_sanctions.=" : Retenue ";
						$nature_sanction_courante=ucfirst($lig_sanction->nature);
						$texte_sanctions.=" : ".$nature_sanction_courante." ";

						// 20160505
						if(!isset($tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction])) {
							$tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction]['nature']=$nature_sanction_courante;
							$tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction]['total']=0;
						}
						$tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction]['total']++;

						$nombre_de_report=nombre_reports($lig_sanction->id_sanction,0);
						if($nombre_de_report!=0) {$texte_sanctions.=" ($nombre_de_report reports)";}

						$texte_sanctions.=formate_date($lig_sanction->date);
						$texte_sanctions.=" $lig_sanction->heure_debut";
						$texte_sanctions.=" (".$lig_sanction->duree."H)";
						$texte_sanctions.=" $lig_sanction->lieu";
						//$texte_sanctions.="<td>".nl2br($lig_sanction->travail)."</td>\n";
	
						$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
						if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
							$texte="Aucun travail";
						}
						else {
							$texte=$lig_sanction->travail;
							if($tmp_doc_joints!="") {
								if($texte!="") {$texte.="\n";}
								$texte.=$tmp_doc_joints;
							}
						}

						$texte_sanctions.=" : ".$texte."\n";
					}
				}
	
				// Exclusions
				$sql="SELECT * FROM s_sanctions s, s_exclusions se WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND se.id_sanction=s.id_sanction ORDER BY se.date_debut, se.heure_debut;";
				//$retour.="$sql<br />\n";
				$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sanction)>0) {
					$texte_sanctions.=$designation_eleve;

					while($lig_sanction=mysqli_fetch_object($res_sanction)) {
						//$texte_sanctions.=" : Exclusion ";
						$nature_sanction_courante=ucfirst($lig_sanction->nature);
						$texte_sanctions.=" : ".$nature_sanction_courante." ";

						// 20160505
						if(!isset($tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction])) {
							$tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction]['nature']=$nature_sanction_courante;
							$tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction]['total']=0;
						}
						$tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction]['total']++;

						$texte_sanctions.=" ".formate_date($lig_sanction->date_debut);
						$texte_sanctions.=" ".$lig_sanction->heure_debut;
						$texte_sanctions.=" - ".formate_date($lig_sanction->date_fin);
						$texte_sanctions.=" ".$lig_sanction->heure_fin;
						$texte_sanctions.=" (".$lig_sanction->lieu.")";
	
						$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
						if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
							$texte="Aucun travail";
						}
						else {
							$texte=$lig_sanction->travail;
							if($tmp_doc_joints!="") {
								if($texte!="") {$texte.="\n";}
								$texte.=$tmp_doc_joints;
							}
						}
						$texte_sanctions.=" : ".$texte;
					}
				}
	
				// Simple travail
				$sql="SELECT * FROM s_sanctions s, s_travail st WHERE s.id_incident=$id_incident_courant AND s.login='".$ele_login."' AND st.id_sanction=s.id_sanction ORDER BY st.date_retour;";
				//$retour.="$sql<br />\n";
				$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sanction)>0) {
					$texte_sanctions.=$designation_eleve;

					while($lig_sanction=mysqli_fetch_object($res_sanction)) {
						//$texte_sanctions.=" : Travail pour le ";
						$nature_sanction_courante=ucfirst($lig_sanction->nature);
						$texte_sanctions.=" : ".$nature_sanction_courante." pour le ";

						// 20160505
						if(!isset($tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction])) {
							$tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction]['nature']=$nature_sanction_courante;
							$tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction]['total']=0;
						}
						$tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction]['total']++;

						$texte_sanctions.=formate_date($lig_sanction->date_retour);
	
						$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
						if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
							$texte="Aucun travail";
						}
						else {
							$texte=$lig_sanction->travail;
							if($tmp_doc_joints!="") {
								if($texte!="") {$texte.="\n";}
								$texte.=$tmp_doc_joints;
							}
						}
						$texte_sanctions.=" : ".$texte;
					}
				}
	
				// Autres sanctions
				$sql="SELECT * FROM s_sanctions s, s_autres_sanctions sa, s_types_sanctions2 sts WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND sa.id_sanction=s.id_sanction AND sa.id_nature=sts.id_nature ORDER BY sts.nature;";
				//echo "$sql<br />\n";
				$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sanction)>0) {
					$texte_sanctions.=$designation_eleve;

					while($lig_sanction=mysqli_fetch_object($res_sanction)) {
						$texte_sanctions.=" : $lig_sanction->description ";

						// 20160505
						$nature_sanction_courante=ucfirst($lig_sanction->nature);
						if(!isset($tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction])) {
							$tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction]['nature']=$nature_sanction_courante;
							$tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction]['total']=0;
						}
						$tab_lignes_OOo_eleve['sanctions'][$lig_sanction->id_nature_sanction]['total']++;

						$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
						if($tmp_doc_joints!="") {
							$texte_sanctions.=$tmp_doc_joints;
						}
						$texte_sanctions.="\n";
					}
				}
			}
		}

		$tab_lignes_OOo[$nb_ligne]['sanctions']=$texte_sanctions;


		$nb_ligne++;
	}

	$tab_lignes_OOo_eleve['incident']=$tab_lignes_OOo;

	if(isset($_POST['debug'])) {
		echo "<hr />tab_lignes_OOo_eleve<pre>";
		print_r($tab_lignes_OOo_eleve);
		echo "</pre>";
	}

	//die();

	$mode_ooo="imprime";
	
	include_once('../tbs/tbs_class.php');
	include_once('../tbs/plugins/tbs_plugin_opentbs.php');
	
	// Création d'une classe  TBS OOo class

	$OOo = new clsTinyButStrong;
	$OOo->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
	
	
	//$mode_test=isset($_POST['mode_test']) ? $_POST['mode_test'] : (isset($_GET['mode_test']) ? $_GET['mode_test'] : NULL);
	//if(isset($mode_test)) {
	if($avec_bloc_adresse_resp=="y") {
		$fichier_a_utiliser="mod_discipline_liste_incidents_bloc_adresse.odt";

		$tableau_a_utiliser=$tab_lignes_OOo_eleve;
		$tab_tmp_test['eleve']=$tab_lignes_OOo_eleve;
		$tableau_a_utiliser=$tab_tmp_test;

		$nom_a_utiliser="eleve";
	}
	else {
		$fichier_a_utiliser="mod_discipline_liste_incidents.odt";
		$tableau_a_utiliser=$tab_lignes_OOo;
		$nom_a_utiliser="incident";
	}

	$prefixe_generation_hors_dossier_mod_ooo="../mod_ooo/";
	include_once('../mod_ooo/lib/lib_mod_ooo.php'); // les fonctions
	$nom_fichier_modele_ooo = $fichier_a_utiliser;
	include_once('../mod_ooo/lib/chemin.inc.php'); // le chemin des dossiers contenant les  modèles

	$OOo->LoadTemplate($nom_dossier_modele_a_utiliser."/".$nom_fichier_modele_ooo, OPENTBS_ALREADY_UTF8);

	// $OOo->MergeBlock('eleves',$tab_eleves_OOo);
	$OOo->MergeBlock($nom_a_utiliser,$tableau_a_utiliser);
	
	$nom_fic = $fichier_a_utiliser;
	
	$OOo->Show(OPENTBS_DOWNLOAD, $nom_fic);
	
	$OOo->remove(); //suppression des fichiers de travail
	
	$OOo->close();

	die();

}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
elseif((isset($mode))&&($mode=="classe2")) {

	// 20160508

	// A FAIRE : Ajouter un test sur l'accès aux infos parents pour la personne connectée.
	$avec_bloc_adresse_resp=isset($_POST['avec_bloc_adresse_resp']) ? $_POST['avec_bloc_adresse_resp'] : (isset($_GET['avec_bloc_adresse_resp']) ? $_GET['avec_bloc_adresse_resp'] : "n");

	$anonymer_autres_protagonistes_eleves=isset($_POST['anonymer_autres_protagonistes_eleves']) ? $_POST['anonymer_autres_protagonistes_eleves'] : (isset($_GET['anonymer_autres_protagonistes_eleves']) ? $_GET['anonymer_autres_protagonistes_eleves'] : "n");
	savePref($_SESSION['login'], 'mod_disc_extract_bilan_anonymer', $anonymer_autres_protagonistes_eleves);

	$cacher_autres_protagonistes_eleves=isset($_POST['cacher_autres_protagonistes_eleves']) ? $_POST['cacher_autres_protagonistes_eleves'] : (isset($_GET['cacher_autres_protagonistes_eleves']) ? $_GET['cacher_autres_protagonistes_eleves'] : "n");
	savePref($_SESSION['login'], 'mod_disc_extract_bilan_cacher_autres_protagonistes', $cacher_autres_protagonistes_eleves);

	$extraire_incidents_avec_sanction=isset($_POST['extraire_incidents_avec_sanction']) ? $_POST['extraire_incidents_avec_sanction'] : (isset($_GET['extraire_incidents_avec_sanction']) ? $_GET['extraire_incidents_avec_sanction'] : "n");
	savePref($_SESSION['login'], 'mod_disc_extract_bilan_extraire_incidents_avec_sanction', $extraire_incidents_avec_sanction);

	$extraire_incidents_avec_sanction_choix=isset($_POST['extraire_incidents_avec_sanction_choix']) ? $_POST['extraire_incidents_avec_sanction_choix'] : (isset($_GET['extraire_incidents_avec_sanction_choix']) ? $_GET['extraire_incidents_avec_sanction_choix'] : "n");

	$date_debut=isset($_POST['date_debut']) ? $_POST['date_debut'] : (isset($_GET['date_debut']) ? $_GET['date_debut'] : NULL);
	$date_fin=isset($_POST['date_fin']) ? $_POST['date_fin'] : (isset($_GET['date_fin']) ? $_GET['date_fin'] : NULL);

	$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);

	$sql_ajout_protagoniste="";
	$protagoniste_incident=isset($_POST['protagoniste_incident']) ? $_POST['protagoniste_incident'] : (isset($_GET['protagoniste_incident']) ? $_GET['protagoniste_incident'] : "");
	if($protagoniste_incident!="") {
		$sql_ajout_protagoniste=" AND e.login='".$protagoniste_incident."'";
	}

	// A FAIRE : Pouvoir restreindre l'extraction à telles ou telles sanctions (s_types_sanctions2)

	//$path='../mod_ooo/'.$nom_dossier_modele_a_utiliser;

	require_once("../mod_discipline/sanctions_func_lib.php");


	$id_nature=isset($_POST['id_nature']) ? $_POST['id_nature'] : (isset($_GET['id_nature']) ? $_GET['id_nature'] : array());
	$sql="DELETE FROM preferences WHERE login='".$_SESSION['login']."' AND name LIKE 'mod_disc_extract_bilan_id_nature_sanction_%';";
	$menage=mysqli_query($mysqli, $sql);
	for($loop=0;$loop<count($id_nature);$loop++) {
		savePref($_SESSION['login'], 'mod_disc_extract_bilan_id_nature_sanction_'.$id_nature[$loop], 'y');
	}

	$tab_id_nature_sanction=array();
	$tab_nature_sanction=array();
	$sql="SELECT DISTINCT id_nature, nature FROM s_types_sanctions2 ORDER BY rang, type, nature;";
	$res_sts=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_sts)>0) {
		while($lig_sts=mysqli_fetch_object($res_sts)) {
			$tab_id_nature_sanction[]=$lig_sts->id_nature;
			$tab_nature_sanction[]=$lig_sts->nature;
		}
	}

	$id_classe_incident=isset($_POST['id_classe_incident']) ? $_POST['id_classe_incident'] : (isset($_GET['id_classe_incident']) ? $_GET['id_classe_incident'] : "");
if(preg_match("/^[0-9]{1,}$/", $id_classe_incident)) {
		$sql="SELECT DISTINCT e.*, c.id AS id_classe, c.classe FROM j_eleves_classes jec, eleves e, classes c WHERE jec.id_classe='".$id_classe_incident."' AND jec.login=e.login AND c.id=jec.id_classe".$sql_ajout_protagoniste." ORDER BY e.nom, e.prenom;";
	}
	else {
		$sql="SELECT DISTINCT e.*, c.id AS id_classe, c.classe FROM j_eleves_classes jec, classes c, eleves e WHERE jec.id_classe=c.id AND jec.login=e.login".$sql_ajout_protagoniste." ORDER BY c.classe, e.nom, e.prenom;";
	}
	$res_ele=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_ele)==0) {
		echo "<p style='color:red'>Aucun élève n'a été trouvé.</p>";
		die();
	}


	$tab_lignes_OOo_eleve=array();

	$cpt_ele=0;
	while($lig_ele=mysqli_fetch_object($res_ele)) {
		//$chaine_criteres="";
		$date_incident="";
		$heure_incident="";
		$nature_incident="---";
		$declarant_incident="---";
		$incidents_clos="y";

		// On force le protagoniste:
		$protagoniste_incident=$lig_ele->login;

		$extraire="y";
		if($_SESSION['statut']=="professeur") {
			if($protagoniste_incident!="") {
				if(!acces_extract_disc("", $protagoniste_incident)) {
					$extraire="n";
				}
			}
			else {
				if(!acces_extract_disc($lig_ele->id_classe, "")) {
					$extraire="n";
				}
			}
		}

		if($extraire=="y") {
			$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp WHERE sp.id_incident=si.id_incident AND sp.login='".$lig_ele->login."'";

			$ajout_sql="";
			if($date_incident!="") {
				$ajout_sql.=" AND si.date='$date_incident'";
				//$chaine_criteres.="&amp;date_incident=$date_incident";
			}
			if($heure_incident!="") {
				$ajout_sql.=" AND si.heure='$heure_incident'";
				//$chaine_criteres.="&amp;heure_incident=$heure_incident";
			}
			if($nature_incident!="---") {
				$ajout_sql.=" AND si.nature='$nature_incident'";
				//$chaine_criteres.="&amp;nature_incident=$nature_incident";
			}
			if($protagoniste_incident!="") {
				$ajout_sql.=" AND sp.login='$protagoniste_incident'";
				//$chaine_criteres.="&amp;protagoniste_incident=$protagoniste_incident";
			}


			// A FAIRE : Permettre de choisir les 'qualite', des dates,...
			//           Actuellement, on n'extrait par ce mode que les responsables
			$qualite="Responsable";

			$ajout_sql.=" AND sp.qualite='$qualite'";
			//$chaine_criteres.="&amp;qualite=$qualite";

			//echo "\$declarant_incident=$declarant_incident<br />";

			if($declarant_incident!="---") {
				$ajout_sql.=" AND si.declarant='$declarant_incident'";
				//$chaine_criteres.="&amp;declarant_incident=$declarant_incident";
			}

			/*
			if($id_classe_incident!="") {
				$chaine_criteres.="&amp;id_classe_incident=$id_classe_incident";
			}
			*/


			$id_classe_courante=$lig_ele->id_classe;

			$nom_periode="";
			$sql_restriction_dates="";
			if((isset($num_periode))&&(preg_match("/^[0-9]{1,}$/", $num_periode))&&($num_periode>0)) {
				// Récupérer les dates de la période pour cette classe
				$dates_periode=get_dates_debut_fin_classe_periode($id_classe_courante, $num_periode, 2);
				if(isset($dates_periode['debut']['mysql_date'])) {
					$date_debut=$dates_periode['debut']['mysql_date'];
				}
				if(isset($dates_periode['fin']['mysql_date'])) {
					$date_fin=$dates_periode['fin']['mysql_date'];
				}

				$sql_per="SELECT nom_periode FROM periodes WHERE id_classe='".$id_classe_courante."' AND num_periode='".$num_periode."';";
				$res_per=mysqli_query($GLOBALS["mysqli"], $sql_per);
				if(mysqli_num_rows($res_per)>0) {
					$lig_per=mysqli_fetch_object($res_per);
					$nom_periode=$lig_per->nom_periode;
				}
			}

			if(isset($date_debut)) {
				$sql_restriction_dates.=" AND date>='".$date_debut."'";
			}
			if(isset($date_fin)) {
				$sql_restriction_dates.=" AND date<='".$date_fin."'";
			}




			$sql.=$ajout_sql;
			$sql.=$sql_restriction_dates;
			$sql2=$sql;
			if($incidents_clos!="y") {$sql.=" AND si.etat!='clos'";}

			$sql.=")";
			$sql2.=")";

			$sql.=" ORDER BY date DESC, heure DESC;";
			$sql2.=" ORDER BY date DESC, heure DESC;";

			//echo "$sql<br />";
			//echo "$sql2<br />";

			$res_incident=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_incident)>0) {

				$tab_lignes_OOo_eleve[$cpt_ele]['nom']=$lig_ele->nom;
				$tab_lignes_OOo_eleve[$cpt_ele]['prenom']=$lig_ele->prenom;
				$tab_lignes_OOo_eleve[$cpt_ele]['classe']=$lig_ele->classe;

				$tab_lignes_OOo_eleve[$cpt_ele]['date_edition']=strftime("%d/%m/%Y à %Hh%M");

				$tab_lignes_OOo_eleve[$cpt_ele]['periode']="";
				$tab_lignes_OOo_eleve[$cpt_ele]['num_periode']=$nom_periode;
				if((isset($num_periode))&&(preg_match("/^[0-9]{1,}$/", $num_periode))&&($num_periode>0)) {
					$tab_lignes_OOo_eleve[$cpt_ele]['num_periode']=$num_periode;
					$tab_lignes_OOo_eleve[$cpt_ele]['periode']=$nom_periode;
				}

				$tab_lignes_OOo_eleve[$cpt_ele]['date_debut']="";
				$tab_lignes_OOo_eleve[$cpt_ele]['dates']="";
				if(isset($date_debut)) {
					$tab_lignes_OOo_eleve[$cpt_ele]['date_debut']=get_date_slash_from_mysql_date($date_debut);
					$tab_lignes_OOo_eleve[$cpt_ele]['dates']="(depuis le ".$tab_lignes_OOo_eleve[$cpt_ele]['date_debut'].")";
				}
				$tab_lignes_OOo_eleve[$cpt_ele]['date_fin']="";
				if(isset($date_fin)) {
					$tab_lignes_OOo_eleve[$cpt_ele]['date_fin']=get_date_slash_from_mysql_date($date_fin);
					if($tab_lignes_OOo_eleve[$cpt_ele]['dates']=="") {
						$tab_lignes_OOo_eleve[$cpt_ele]['dates']="(jusqu'au ".$tab_lignes_OOo_eleve[$cpt_ele]['date_fin'].")";
					}
					else {
						$tab_lignes_OOo_eleve[$cpt_ele]['dates']="(du ".$tab_lignes_OOo_eleve[$cpt_ele]['date_debut']." au ".$tab_lignes_OOo_eleve[$cpt_ele]['date_fin'].")";
					}
				}

				/*
				for($loop_sts=0;$loop_sts<count($tab_id_nature_sanction);$loop_sts++) {
					$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$tab_id_nature_sanction[$loop_sts]]['total']=0;
					$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$tab_id_nature_sanction[$loop_sts]]['nature']=$tab_nature_sanction[$loop_sts];
				}
				*/

				// Test
				//$tab_lignes_OOo_eleve[$cpt_ele]['eleve']['etab']=getSettingValue("gepiSchoolName");

				$tab_lignes_OOo_eleve[$cpt_ele]['etab']=getSettingValue("gepiSchoolName");
				$tab_lignes_OOo_eleve[$cpt_ele]['acad']=getSettingValue("gepiSchoolAcademie");
				$tab_lignes_OOo_eleve[$cpt_ele]['adr1']=getSettingValue("gepiSchoolAdress1")." ".getSettingValue("gepiSchoolAdress2");
				$tab_lignes_OOo_eleve[$cpt_ele]['cp']=getSettingValue("gepiSchoolZipCode");
				$tab_lignes_OOo_eleve[$cpt_ele]['ville']=getSettingValue("gepiSchoolCity");

				// Extraire l'adresse des responsables/parents...
				// get_adresse_responsable($pers_id) retourne $tab_adresse
				// Voir bull_func.lib.php
				$sql_resp="SELECT rp.* FROM resp_pers rp, responsables2 r, eleves e WHERE e.ele_id=r.ele_id AND r.resp_legal='1' AND r.pers_id=rp.pers_id AND e.login='".$lig_ele->login."';";
				$res_resp=mysqli_query($GLOBALS["mysqli"], $sql_resp);
				if(mysqli_num_rows($res_resp)==0) {
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["civilite"]="";
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["nom"]="";
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["prenom"]="";
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["tab_adresse"]['adr_id']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["tab_adresse"]['adr1']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["tab_adresse"]['adr2']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["tab_adresse"]['adr3']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["tab_adresse"]['cp']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["tab_adresse"]['commune']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["tab_adresse"]['pays']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["tab_adresse"]['en_ligne']="";

					$tab_lignes_OOo_eleve[$cpt_ele]["resp_civilite"]="";
					$tab_lignes_OOo_eleve[$cpt_ele]["resp_nom"]="";
					$tab_lignes_OOo_eleve[$cpt_ele]["resp_prenom"]="";
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_adr_id']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_adr1']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_adr2']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_adr3']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_cp']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_commune']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_pays']="";
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_adr_en_ligne']="";
				}
				else {
					$lig_resp=mysqli_fetch_object($res_resp);
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["civilite"]=$lig_resp->civilite;
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["nom"]=$lig_resp->nom;
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["prenom"]=$lig_resp->prenom;
					$tab_adr_courante=get_adresse_responsable($lig_resp->pers_id);
					$tab_lignes_OOo_eleve[$cpt_ele]['responsable']["tab_adresse"]=$tab_adr_courante;

					$tab_lignes_OOo_eleve[$cpt_ele]["resp_civilite"]=$lig_resp->civilite;
					$tab_lignes_OOo_eleve[$cpt_ele]["resp_nom"]=$lig_resp->nom;
					$tab_lignes_OOo_eleve[$cpt_ele]["resp_prenom"]=$lig_resp->prenom;
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_adr_id']=$tab_adr_courante['adr_id'];
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_adr1']=$tab_adr_courante['adr1'];
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_adr2']=$tab_adr_courante['adr2'];
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_adr3']=$tab_adr_courante['adr3'];
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_cp']=$tab_adr_courante['cp'];
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_commune']=$tab_adr_courante['commune'];
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_pays']=$tab_adr_courante['pays'];
					$tab_lignes_OOo_eleve[$cpt_ele]['resp_adr_en_ligne']=$tab_adr_courante['en_ligne'];
				}

				//++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				// Tableau des incidents de l'élève courant
				$tab_lignes_OOo=array();
				$nb_ligne=0;
				//$res_incident=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_incident=mysqli_fetch_object($res_incident)) {
					$nb_sanctions_incident_courant=0;
					$nb_sanctions_incident_courant_types_choisis=0;
					$tab_lignes_OOo[$nb_ligne]=array();

					$tab_lignes_OOo[$nb_ligne]['etab']=getSettingValue("gepiSchoolName");
					$tab_lignes_OOo[$nb_ligne]['acad']=getSettingValue("gepiSchoolAcademie");
					$tab_lignes_OOo[$nb_ligne]['adr1']=getSettingValue("gepiSchoolAdress1")." ".getSettingValue("gepiSchoolAdress2");
					$tab_lignes_OOo[$nb_ligne]['cp']=getSettingValue("gepiSchoolZipCode");
					$tab_lignes_OOo[$nb_ligne]['ville']=getSettingValue("gepiSchoolCity");

					$tab_lignes_OOo[$nb_ligne]['id_incident']=$lig_incident->id_incident;
					$tab_lignes_OOo[$nb_ligne]['declarant']=civ_nom_prenom($lig_incident->declarant,'');
					$tab_lignes_OOo[$nb_ligne]['date']=formate_date($lig_incident->date);
					$tab_lignes_OOo[$nb_ligne]['heure']=$lig_incident->heure;
					$tab_lignes_OOo[$nb_ligne]['nature']=$lig_incident->nature;
					$tab_lignes_OOo[$nb_ligne]['description']=$lig_incident->description;
					$tab_lignes_OOo[$nb_ligne]['etat']=$lig_incident->etat;

					// Lieu
					$tab_lignes_OOo[$nb_ligne]['lieu']=get_lieu_from_id($lig_incident->id_lieu);

					// Protagonistes
					$tab_protagonistes_eleves=array();
					$sql="SELECT * FROM s_protagonistes WHERE id_incident='$lig_incident->id_incident' ORDER BY statut,qualite,login;";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)==0) {
						$tab_lignes_OOo[$nb_ligne]['protagonistes']="Aucun";
					}
					else {
						$liste_protagonistes="";
						while($lig2=mysqli_fetch_object($res2)) {

							if(($lig2->statut=='eleve')&&($cacher_autres_protagonistes_eleves=="y")&&($lig2->login!=$protagoniste_incident)) {
								// On n'affiche pas du tout l'info.
							}
							else {
								if($liste_protagonistes!="") {$liste_protagonistes.=", ";}
								if($lig2->statut=='eleve') {
									if(($anonymer_autres_protagonistes_eleves=="y")&&($lig2->login!=$protagoniste_incident)) {
										$liste_protagonistes.="XXX";
									}
									else {
										$liste_protagonistes.=get_nom_prenom_eleve($lig2->login,'avec_classe');
									}
									$tab_protagonistes_eleves[]=$lig2->login;
								}
								else {
									$liste_protagonistes.=civ_nom_prenom($lig2->login,'',"y");
								}

								if($lig2->qualite!='') {
									$liste_protagonistes.=" $lig2->qualite";
								}
							}
						}
					}
					$tab_lignes_OOo[$nb_ligne]['protagonistes']=$liste_protagonistes;

					$id_incident_courant=$lig_incident->id_incident;

					// Mesures prises
					$texte="";
					$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='prise'";
					//$texte.="<br />$sql";
					$res_t_incident=mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_login_ele_mesure_prise=mysqli_num_rows($res_t_incident);

					if($nb_login_ele_mesure_prise>0) {
						while($lig_t_incident=mysqli_fetch_object($res_t_incident)) {
							if(($cacher_autres_protagonistes_eleves=="y")&&($lig_t_incident->login_ele!=$protagoniste_incident)) {
								// On n'affiche pas du tout l'info.
							}
							else {
								$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='prise' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
								$res_mes_ele=mysqli_query($GLOBALS["mysqli"], $sql);
								$nb_mes_ele=mysqli_num_rows($res_mes_ele);

								if(($anonymer_autres_protagonistes_eleves=="y")&&($lig_t_incident->login_ele!=$protagoniste_incident)) {
									$texte.="XXX :";
								}
								else {
									$texte.=civ_nom_prenom($lig_t_incident->login_ele,'')." :";
								}
								while($lig_mes_ele=mysqli_fetch_object($res_mes_ele)) {
									$texte.=" ".$lig_mes_ele->mesure;
								}
								$texte.="\n";
							}
						}
					}
					$tab_lignes_OOo[$nb_ligne]['mesures_prises']=$texte;

					// Mesures demandees
					$texte="";
					$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='demandee' ORDER BY login_ele";
					//$texte.="<br />$sql";
					$res_t_incident2=mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_login_ele_mesure_demandee=mysqli_num_rows($res_t_incident2);

					if($nb_login_ele_mesure_demandee>0) {
						while($lig_t_incident=mysqli_fetch_object($res_t_incident2)) {
							if(($cacher_autres_protagonistes_eleves=="y")&&($lig_t_incident->login_ele!=$protagoniste_incident)) {
								// On n'affiche pas du tout l'info.
							}
							else {
								$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='demandee' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
								$res_mes_ele=mysqli_query($GLOBALS["mysqli"], $sql);
								$nb_mes_ele=mysqli_num_rows($res_mes_ele);

								if(($anonymer_autres_protagonistes_eleves=="y")&&($lig_t_incident->login_ele!=$protagoniste_incident)) {
									$texte.="XXX :";
								}
								else {
									$texte.=civ_nom_prenom($lig_t_incident->login_ele,'')." :";
								}
								while($lig_mes_ele=mysqli_fetch_object($res_mes_ele)) {
									$texte.=" ".$lig_mes_ele->mesure;
								}
								$texte.="\n";
							}
						}
					}
					$tab_lignes_OOo[$nb_ligne]['mesures_demandees']=$texte;


					// Sanctions
					$texte_sanctions="";
					for($i=0;$i<count($tab_protagonistes_eleves);$i++) {
						$ele_login=$tab_protagonistes_eleves[$i];

						if(($cacher_autres_protagonistes_eleves=="y")&&($ele_login!=$protagoniste_incident)) {
							// On n'affiche pas du tout l'info.
						}
						else {

							if(($anonymer_autres_protagonistes_eleves=="y")&&($ele_login!=$protagoniste_incident)) {
								$designation_eleve="XXX";
							}
							else {
								$designation_eleve=civ_nom_prenom($ele_login,'');
							}

							// Retenues
							$sql="SELECT * FROM s_sanctions s, s_retenues sr WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND sr.id_sanction=s.id_sanction ORDER BY sr.date, sr.heure_debut;";
							//echo "$sql<br />\n";
							$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_sanction)>0) {
								$texte_sanctions.=$designation_eleve;

								while($lig_sanction=mysqli_fetch_object($res_sanction)) {
									$nb_sanctions_incident_courant++;
									//$texte_sanctions.=" : Retenue ";
									$nature_sanction_courante=ucfirst($lig_sanction->nature);
									$texte_sanctions.=" : ".$nature_sanction_courante." ";

									// 20160505
									//+++++++++++++++++++++++++++++++++++++++++++++++
									if(in_array($lig_sanction->id_nature_sanction, $id_nature)) {
										if(!isset($tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction])) {
											$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction]['nature']=$nature_sanction_courante;
											$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction]['total']=0;
										}
										$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction]['total']++;
										$nb_sanctions_incident_courant_types_choisis++;
									}
									//+++++++++++++++++++++++++++++++++++++++++++++++

									$nombre_de_report=nombre_reports($lig_sanction->id_sanction,0);
									if($nombre_de_report!=0) {$texte_sanctions.=" ($nombre_de_report reports)";}

									$texte_sanctions.=formate_date($lig_sanction->date);
									$texte_sanctions.=" $lig_sanction->heure_debut";
									$texte_sanctions.=" (".$lig_sanction->duree."H)";
									$texte_sanctions.=" $lig_sanction->lieu";
									//$texte_sanctions.="<td>".nl2br($lig_sanction->travail)."</td>\n";
	
									$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
									if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
										$texte="Aucun travail";
									}
									else {
										$texte=$lig_sanction->travail;
										if($tmp_doc_joints!="") {
											if($texte!="") {$texte.="\n";}
											$texte.=$tmp_doc_joints;
										}
									}

									$texte_sanctions.=" : ".$texte."\n";
								}
							}
	
							// Exclusions
							$sql="SELECT * FROM s_sanctions s, s_exclusions se WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND se.id_sanction=s.id_sanction ORDER BY se.date_debut, se.heure_debut;";
							//$retour.="$sql<br />\n";
							$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_sanction)>0) {
								$texte_sanctions.=$designation_eleve;

								while($lig_sanction=mysqli_fetch_object($res_sanction)) {
									$nb_sanctions_incident_courant++;
									//$texte_sanctions.=" : Exclusion ";
									$nature_sanction_courante=ucfirst($lig_sanction->nature);
									$texte_sanctions.=" : ".$nature_sanction_courante." ";

									// 20160505
									//+++++++++++++++++++++++++++++++++++++++++++++++
									if(in_array($lig_sanction->id_nature_sanction, $id_nature)) {
										if(!isset($tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction])) {
											$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction]['nature']=$nature_sanction_courante;
											$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction]['total']=0;
										}
										$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction]['total']++;
										$nb_sanctions_incident_courant_types_choisis++;
									}
									//+++++++++++++++++++++++++++++++++++++++++++++++

									$texte_sanctions.=" ".formate_date($lig_sanction->date_debut);
									$texte_sanctions.=" ".$lig_sanction->heure_debut;
									$texte_sanctions.=" - ".formate_date($lig_sanction->date_fin);
									$texte_sanctions.=" ".$lig_sanction->heure_fin;
									$texte_sanctions.=" (".$lig_sanction->lieu.")";
	
									$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
									if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
										$texte="Aucun travail";
									}
									else {
										$texte=$lig_sanction->travail;
										if($tmp_doc_joints!="") {
											if($texte!="") {$texte.="\n";}
											$texte.=$tmp_doc_joints;
										}
									}
									$texte_sanctions.=" : ".$texte;
								}
							}
	
							// Simple travail
							$sql="SELECT * FROM s_sanctions s, s_travail st WHERE s.id_incident=$id_incident_courant AND s.login='".$ele_login."' AND st.id_sanction=s.id_sanction ORDER BY st.date_retour;";
							//$retour.="$sql<br />\n";
							$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_sanction)>0) {
								$texte_sanctions.=$designation_eleve;

								while($lig_sanction=mysqli_fetch_object($res_sanction)) {
									$nb_sanctions_incident_courant++;
									//$texte_sanctions.=" : Travail pour le ";
									$nature_sanction_courante=ucfirst($lig_sanction->nature);
									$texte_sanctions.=" : ".$nature_sanction_courante." pour le ";

									// 20160505
									//+++++++++++++++++++++++++++++++++++++++++++++++
									if(in_array($lig_sanction->id_nature_sanction, $id_nature)) {
										if(!isset($tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction])) {
											$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction]['nature']=$nature_sanction_courante;
											$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction]['total']=0;
										}
										$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction]['total']++;
										$nb_sanctions_incident_courant_types_choisis++;
									}
									//+++++++++++++++++++++++++++++++++++++++++++++++

									$texte_sanctions.=formate_date($lig_sanction->date_retour);
	
									$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
									if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
										$texte="Aucun travail";
									}
									else {
										$texte=$lig_sanction->travail;
										if($tmp_doc_joints!="") {
											if($texte!="") {$texte.="\n";}
											$texte.=$tmp_doc_joints;
										}
									}
									$texte_sanctions.=" : ".$texte;
								}
							}
	
							// Autres sanctions
							$sql="SELECT * FROM s_sanctions s, s_autres_sanctions sa, s_types_sanctions2 sts WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND sa.id_sanction=s.id_sanction AND sa.id_nature=sts.id_nature ORDER BY sts.nature;";
							//echo "$sql<br />\n";
							$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_sanction)>0) {
								$texte_sanctions.=$designation_eleve;

								while($lig_sanction=mysqli_fetch_object($res_sanction)) {
									$nb_sanctions_incident_courant++;
									$texte_sanctions.=" : $lig_sanction->description ";

									// 20160505
									$nature_sanction_courante=ucfirst($lig_sanction->nature);
									//+++++++++++++++++++++++++++++++++++++++++++++++
									if(in_array($lig_sanction->id_nature_sanction, $id_nature)) {
										if(!isset($tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction])) {
											$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction]['nature']=$nature_sanction_courante;
											$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction]['total']=0;
										}
										$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$lig_sanction->id_nature_sanction]['total']++;
										$nb_sanctions_incident_courant_types_choisis++;
									}
									//+++++++++++++++++++++++++++++++++++++++++++++++

									$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
									if($tmp_doc_joints!="") {
										$texte_sanctions.=$tmp_doc_joints;
									}
									$texte_sanctions.="\n";
								}
							}
						}
					}

					$tab_lignes_OOo[$nb_ligne]['sanctions']=$texte_sanctions;

					if(($nb_sanctions_incident_courant==0)&&($extraire_incidents_avec_sanction=="y")) {
						unset($tab_lignes_OOo[$nb_ligne]);
					}
					elseif(($nb_sanctions_incident_courant_types_choisis==0)&&($extraire_incidents_avec_sanction_choix=="y")) {
						unset($tab_lignes_OOo[$nb_ligne]);
					}
					else {
						$nb_ligne++;
					}
				}

				if(count($tab_lignes_OOo)>0) {
					$tab_lignes_OOo_eleve[$cpt_ele]['incident']=$tab_lignes_OOo;

					// Pour réordonner les types sanctions:
					for($loop_s=0;$loop_s<count($id_nature);$loop_s++) {
						if(isset($tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$id_nature[$loop_s]])) {
							$tab_lignes_OOo_eleve[$cpt_ele]['sanctions2'][]=$tab_lignes_OOo_eleve[$cpt_ele]['sanctions'][$id_nature[$loop_s]];
						}
					}

					if(isset($_POST['debug'])) {
						echo "<hr />tab_lignes_OOo_eleve[$cpt_ele]<pre>";
						print_r($tab_lignes_OOo_eleve[$cpt_ele]);
						echo "</pre>";
					}

					$cpt_ele++;
				}
				else {
					unset($tab_lignes_OOo_eleve[$cpt_ele]);
				}
			}
		}
	}

	//die();

	$mode_ooo="imprime";
	
	include_once('../tbs/tbs_class.php');
	include_once('../tbs/plugins/tbs_plugin_opentbs.php');
	
	// Création d'une classe  TBS OOo class

	$OOo = new clsTinyButStrong;
	$OOo->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);

	$fichier_a_utiliser="mod_discipline_liste_incidents_bilan_classe.odt";
	$tableau_a_utiliser=$tab_lignes_OOo_eleve;
	$nom_a_utiliser="eleve";

	$prefixe_generation_hors_dossier_mod_ooo="../mod_ooo/";
	include_once('../mod_ooo/lib/lib_mod_ooo.php'); // les fonctions
	$nom_fichier_modele_ooo = $fichier_a_utiliser;
	include_once('../mod_ooo/lib/chemin.inc.php'); // le chemin des dossiers contenant les  modèles

	$OOo->LoadTemplate($nom_dossier_modele_a_utiliser."/".$nom_fichier_modele_ooo, OPENTBS_ALREADY_UTF8);

	$OOo->MergeBlock($nom_a_utiliser,$tableau_a_utiliser);
	
	$nom_fic = $fichier_a_utiliser;
	
	$OOo->Show(OPENTBS_DOWNLOAD, $nom_fic);
	
	$OOo->remove(); //suppression des fichiers de travail
	
	$OOo->close();

	die();

}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// Mode historique:

//$path='../mod_ooo/'.$nom_dossier_modele_a_utiliser;

require_once("../mod_discipline/sanctions_func_lib.php");

$id_classe_incident=isset($_POST['id_classe_incident']) ? $_POST['id_classe_incident'] : (isset($_GET['id_classe_incident']) ? $_GET['id_classe_incident'] : "");
$chaine_criteres="";
$date_incident="";
$heure_incident="";
$nature_incident="---";
$protagoniste_incident=isset($_POST['protagoniste_incident']) ? $_POST['protagoniste_incident'] : (isset($_GET['protagoniste_incident']) ? $_GET['protagoniste_incident'] : "");
$declarant_incident="---";
$incidents_clos="y";

if((!isset($id_classe_incident))||($id_classe_incident=="")) {
	$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp WHERE sp.id_incident=si.id_incident";
}
else {
	$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp, j_eleves_classes jec WHERE sp.id_incident=si.id_incident AND jec.id_classe='$id_classe_incident' AND jec.login=sp.login";

	if($_SESSION['statut']=="professeur") {
		if(!acces_extract_disc($id_classe_incident, "")) {
			echo "<p style='color:red'>Vous n'avez pas accès à la classe ".get_nom_classe($id_classe_incident).".</p>";
			die();
		}
	}

}

$ajout_sql="";
if($date_incident!="") {$ajout_sql.=" AND si.date='$date_incident'";$chaine_criteres.="&amp;date_incident=$date_incident";}
if($heure_incident!="") {$ajout_sql.=" AND si.heure='$heure_incident'";$chaine_criteres.="&amp;heure_incident=$heure_incident";}
if($nature_incident!="---") {$ajout_sql.=" AND si.nature='$nature_incident'";$chaine_criteres.="&amp;nature_incident=$nature_incident";}
if($protagoniste_incident!="") {
	$ajout_sql.=" AND sp.login='$protagoniste_incident'";$chaine_criteres.="&amp;protagoniste_incident=$protagoniste_incident";

	if($_SESSION['statut']=="professeur") {
		if(!acces_extract_disc("", $protagoniste_incident)) {
			echo "<p style='color:red'>Vous n'avez pas accès au protagoniste choisi ($protagoniste_incident).</p>";
			die();
		}
	}

}

//echo "\$declarant_incident=$declarant_incident<br />";

if($declarant_incident!="---") {$ajout_sql.=" AND si.declarant='$declarant_incident'";$chaine_criteres.="&amp;declarant_incident=$declarant_incident";}

if($id_classe_incident!="") {
	$chaine_criteres.="&amp;id_classe_incident=$id_classe_incident";
}

$sql.=$ajout_sql;
$sql2=$sql;
if($incidents_clos!="y") {$sql.=" AND si.etat!='clos'";}

$sql.=")";
$sql2.=")";

$sql.=" ORDER BY date DESC, heure DESC;";
$sql2.=" ORDER BY date DESC, heure DESC;";

//echo "$sql<br />";
//echo "$sql2<br />";

$tab_lignes_OOo=array();

$nb_ligne=0;
$res_incident=mysqli_query($GLOBALS["mysqli"], $sql);
while($lig_incident=mysqli_fetch_object($res_incident)) {
	$tab_lignes_OOo[$nb_ligne]=array();
	
	$tab_lignes_OOo[$nb_ligne]['id_incident']=$lig_incident->id_incident;
	$tab_lignes_OOo[$nb_ligne]['declarant']=civ_nom_prenom($lig_incident->declarant,'');
	$tab_lignes_OOo[$nb_ligne]['date']=formate_date($lig_incident->date);
	$tab_lignes_OOo[$nb_ligne]['heure']=$lig_incident->heure;
	$tab_lignes_OOo[$nb_ligne]['nature']=$lig_incident->nature;
	$tab_lignes_OOo[$nb_ligne]['description']=$lig_incident->description;
	$tab_lignes_OOo[$nb_ligne]['etat']=$lig_incident->etat;

	// Lieu
	$tab_lignes_OOo[$nb_ligne]['lieu']=get_lieu_from_id($lig_incident->id_lieu);

	// Protagonistes
	$tab_protagonistes_eleves=array();
	$sql="SELECT * FROM s_protagonistes WHERE id_incident='$lig_incident->id_incident' ORDER BY statut,qualite,login;";
	$res2=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		$tab_lignes_OOo[$nb_ligne]['protagonistes']="Aucun";
	}
	else {
		$liste_protagonistes="";
		while($lig2=mysqli_fetch_object($res2)) {
			if($liste_protagonistes!="") {$liste_protagonistes.=", ";}
			if($lig2->statut=='eleve') {
				$liste_protagonistes.=get_nom_prenom_eleve($lig2->login,'avec_classe');
				$tab_protagonistes_eleves[]=$lig2->login;
			}
			else {
				$liste_protagonistes.=civ_nom_prenom($lig2->login,'',"y");
			}

			if($lig2->qualite!='') {
				$liste_protagonistes.=" $lig2->qualite";
			}
		}
	}
	$tab_lignes_OOo[$nb_ligne]['protagonistes']=$liste_protagonistes;

	$id_incident_courant=$lig_incident->id_incident;

	// Mesures prises
	$texte="";
	$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='prise'";
	//$texte.="<br />$sql";
	$res_t_incident=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_login_ele_mesure_prise=mysqli_num_rows($res_t_incident);

	if($nb_login_ele_mesure_prise>0) {
		while($lig_t_incident=mysqli_fetch_object($res_t_incident)) {
			$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='prise' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
			$res_mes_ele=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_mes_ele=mysqli_num_rows($res_mes_ele);

			$texte.=civ_nom_prenom($lig_t_incident->login_ele,'')." :";
			while($lig_mes_ele=mysqli_fetch_object($res_mes_ele)) {
				$texte.=" ".$lig_mes_ele->mesure;
			}
			$texte.="\n";
		}
	}
	$tab_lignes_OOo[$nb_ligne]['mesures_prises']=$texte;

	// Mesures demandees
	$texte="";
	$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='demandee' ORDER BY login_ele";
	//$texte.="<br />$sql";
	$res_t_incident2=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_login_ele_mesure_demandee=mysqli_num_rows($res_t_incident2);

	if($nb_login_ele_mesure_demandee>0) {
		while($lig_t_incident=mysqli_fetch_object($res_t_incident2)) {
			$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='demandee' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
			$res_mes_ele=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_mes_ele=mysqli_num_rows($res_mes_ele);

			$texte.=civ_nom_prenom($lig_t_incident->login_ele,'')." :";
			while($lig_mes_ele=mysqli_fetch_object($res_mes_ele)) {
				$texte.=" ".$lig_mes_ele->mesure;
			}
			$texte.="\n";
		}
	}
	$tab_lignes_OOo[$nb_ligne]['mesures_demandees']=$texte;


	// Sanctions
	$texte_sanctions="";
	for($i=0;$i<count($tab_protagonistes_eleves);$i++) {
		$ele_login=$tab_protagonistes_eleves[$i];

		$designation_eleve=civ_nom_prenom($ele_login,'');

		// Retenues
		$sql="SELECT * FROM s_sanctions s, s_retenues sr WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND sr.id_sanction=s.id_sanction ORDER BY sr.date, sr.heure_debut;";
		//echo "$sql<br />\n";
		$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_sanction)>0) {
			$texte_sanctions.=$designation_eleve;

			while($lig_sanction=mysqli_fetch_object($res_sanction)) {
				//$texte_sanctions.=" : Retenue ";
				$texte_sanctions.=" : ".ucfirst($lig_sanction->nature)." ";

				$nombre_de_report=nombre_reports($lig_sanction->id_sanction,0);
				if($nombre_de_report!=0) {$texte_sanctions.=" ($nombre_de_report reports)";}

				$texte_sanctions.=formate_date($lig_sanction->date);
				$texte_sanctions.=" $lig_sanction->heure_debut";
				$texte_sanctions.=" (".$lig_sanction->duree."H)";
				$texte_sanctions.=" $lig_sanction->lieu";
				//$texte_sanctions.="<td>".nl2br($lig_sanction->travail)."</td>\n";
	
				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
					$texte="Aucun travail";
				}
				else {
					$texte=$lig_sanction->travail;
					if($tmp_doc_joints!="") {
						if($texte!="") {$texte.="\n";}
						$texte.=$tmp_doc_joints;
					}
				}

				$texte_sanctions.=" : ".$texte."\n";
			}
		}
	
		// Exclusions
		$sql="SELECT * FROM s_sanctions s, s_exclusions se WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND se.id_sanction=s.id_sanction ORDER BY se.date_debut, se.heure_debut;";
		//$retour.="$sql<br />\n";
		$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_sanction)>0) {
			$texte_sanctions.=$designation_eleve;

			while($lig_sanction=mysqli_fetch_object($res_sanction)) {
				//$texte_sanctions.=" : Exclusion ";
				$texte_sanctions.=" : ".ucfirst($lig_sanction->nature)." ";

				$texte_sanctions.=" ".formate_date($lig_sanction->date_debut);
				$texte_sanctions.=" ".$lig_sanction->heure_debut;
				$texte_sanctions.=" - ".formate_date($lig_sanction->date_fin);
				$texte_sanctions.=" ".$lig_sanction->heure_fin;
				$texte_sanctions.=" (".$lig_sanction->lieu.")";
	
				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
					$texte="Aucun travail";
				}
				else {
					$texte=$lig_sanction->travail;
					if($tmp_doc_joints!="") {
						if($texte!="") {$texte.="\n";}
						$texte.=$tmp_doc_joints;
					}
				}
				$texte_sanctions.=" : ".$texte;
			}
		}
	
		// Simple travail
		$sql="SELECT * FROM s_sanctions s, s_travail st WHERE s.id_incident=$id_incident_courant AND s.login='".$ele_login."' AND st.id_sanction=s.id_sanction ORDER BY st.date_retour;";
		//$retour.="$sql<br />\n";
		$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_sanction)>0) {
			$texte_sanctions.=$designation_eleve;

			while($lig_sanction=mysqli_fetch_object($res_sanction)) {
				//$texte_sanctions.=" : Travail pour le ";
				$texte_sanctions.=" : ".ucfirst($lig_sanction->nature)." pour le ";
				$texte_sanctions.=formate_date($lig_sanction->date_retour);
	
				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
					$texte="Aucun travail";
				}
				else {
					$texte=$lig_sanction->travail;
					if($tmp_doc_joints!="") {
						if($texte!="") {$texte.="\n";}
						$texte.=$tmp_doc_joints;
					}
				}
				$texte_sanctions.=" : ".$texte;
			}
		}
	
		// Autres sanctions
		$sql="SELECT * FROM s_sanctions s, s_autres_sanctions sa, s_types_sanctions2 sts WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND sa.id_sanction=s.id_sanction AND sa.id_nature=sts.id_nature ORDER BY sts.nature;";
		//echo "$sql<br />\n";
		$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_sanction)>0) {
			$texte_sanctions.=$designation_eleve;

			while($lig_sanction=mysqli_fetch_object($res_sanction)) {
				$texte_sanctions.=" : $lig_sanction->description ";

				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if($tmp_doc_joints!="") {
					$texte_sanctions.=$tmp_doc_joints;
				}
				$texte_sanctions.="\n";
			}
		}
	}

	$tab_lignes_OOo[$nb_ligne]['sanctions']=$texte_sanctions;


	$nb_ligne++;
}

/*
	echo "<pre>";
	print_r($tab_lignes_OOo);
	echo "</pre>";

	die();
*/

$mode_ooo="imprime";
	
include_once('../tbs/tbs_class.php');
include_once('../tbs/plugins/tbs_plugin_opentbs.php');

// Création d'une classe  TBS OOo class

$OOo = new clsTinyButStrong;
$OOo->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
	
$mode_test=isset($_POST['mode_test']) ? $_POST['mode_test'] : (isset($_GET['mode_test']) ? $_GET['mode_test'] : NULL);
if(isset($mode_test)) {
	$fichier_a_utiliser="mod_discipline_liste_incidents2b.odt";
	$tableau_a_utiliser=$tab_lignes_OOo_eleve;
	$nom_a_utiliser="eleve";
}
else {
	$fichier_a_utiliser="mod_discipline_liste_incidents.odt";
	$tableau_a_utiliser=$tab_lignes_OOo;
	$nom_a_utiliser="incident";
}

// le chemin du fichier est indique a partir de l'emplacement de ce fichier
//   $path."/".$tab_file[$num_fich]

$prefixe_generation_hors_dossier_mod_ooo="../mod_ooo/";
include_once('../mod_ooo/lib/lib_mod_ooo.php'); // les fonctions
$nom_fichier_modele_ooo = $fichier_a_utiliser;
include_once('../mod_ooo/lib/chemin.inc.php'); // le chemin des dossiers contenant les  modèles

//$path='../mod_ooo/'.$nom_dossier_modele_a_utiliser;

//$nom_dossier_modele_a_utiliser = $path."/";

$OOo->LoadTemplate($nom_dossier_modele_a_utiliser."/".$nom_fichier_modele_ooo, OPENTBS_ALREADY_UTF8);

$OOo->MergeBlock($nom_a_utiliser,$tableau_a_utiliser);

$nom_fic = $fichier_a_utiliser;

$OOo->Show(OPENTBS_DOWNLOAD, $nom_fic);


$OOo->remove(); //suppression des fichiers de travail

$OOo->close();

die();
?>
