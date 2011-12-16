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
 * but WITHOUT ANY WARRANTY; without even the  warranty of
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


//INSERT INTO droits VALUES ('/groupes/get_csv.php', 'F', 'V', 'V', 'V', 'F', 'V', 'Génération de CSV élèves', '');
//INSERT INTO droits VALUES ('/groupes/get_csv.php', 'V', 'V', 'V', 'V', 'F', 'V', 'Génération de CSV élèves', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$mode = isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

//$tab=array('avec_classe','avec_login','avec_nom','avec_prenom','avec_sexe','avec_naiss','avec_email','avec_statut','avec_ine','avec_elenoet','avec_ele_id','avec_prof');
$tab=array('avec_classe','avec_login','avec_nom','avec_prenom','avec_sexe','avec_naiss','avec_lieu_naiss','avec_email','avec_statut','avec_elenoet','avec_ele_id','avec_no_gep','avec_prof');
for($i=0;$i<count($tab);$i++) {
	$champ=$tab[$i];
	$$champ = isset($_POST[$champ]) ? $_POST[$champ] : (isset($_GET[$champ]) ? $_GET[$champ] : NULL);

	if((isset($_POST[$champ]))||(isset($_GET[$champ]))) {
		$_SESSION['mes_listes_'.$tab[$i]]="y";
	}
	else {
		$_SESSION['mes_listes_'.$tab[$i]]="n";
	}
}

if((isset($avec_naiss))&&($avec_naiss=='y')) {
	$format_naiss=isset($_POST['format_naiss']) ? $_POST['format_naiss'] : 'aaaammjj';
	$_SESSION['mes_listes_format_naiss']=$format_naiss;
}

//echo "1 - \$id_groupe=$id_groupe<br />";

//$periode_num = isset($_GET['periode_num']) ? $_GET['periode_num'] : 0;
$periode_num = isset($_POST['periode_num']) ? $_POST['periode_num'] : (isset($_GET['periode_num']) ? $_GET['periode_num'] : 1);

//if (!is_numeric($periode_num)) {$periode_num = 0;}
if (!is_numeric($periode_num)) {$periode_num = 1;}

$_SESSION['mes_listes_periode_num']=$periode_num;

if (is_numeric($id_groupe) && $id_groupe > 0) {
	$current_group = get_group($id_groupe);
	//echo "2<br />";
} else {
	$current_group = false;
}

if ($current_group) {
	$nom_fic = $current_group["name"] . "-" . remplace_accents(preg_replace('/, /','~',$current_group["classlist_string"]),'all') . ".csv";
} else {
	$classe = mysql_result(mysql_query("SELECT classe FROM classes WHERE id = '" . $id_classe . "'"), 0);
	$nom_fic = remplace_accents($classe,"all") . ".csv";
}

//debug_var();

send_file_download_headers('text/x-csv',$nom_fic);

include "../lib/periodes.inc.php";
$fd = '';

if(!isset($mode)) {
	$fd.="CLASSE;LOGIN;NOM;PRENOM;SEXE;DATE_NAISS\n";
	$avec_classe="y";
	$avec_login="y";
	$avec_nom="y";
	$avec_prenom="y";
	$avec_sexe="y";
	$avec_naiss="y";
}
else {
	if((isset($avec_classe))&&($avec_classe=='y')) {$fd.="CLASSE;";}
	if((isset($avec_login))&&($avec_login=='y')) {$fd.="LOGIN;";}
	if((isset($avec_nom))&&($avec_nom=='y')) {$fd.="NOM;";}
	if((isset($avec_prenom))&&($avec_prenom=='y')) {$fd.="PRENOM;";}
	if((isset($avec_sexe))&&($avec_sexe=='y')) {$fd.="SEXE;";}
	if((isset($avec_naiss))&&($avec_naiss=='y')) {$fd.="DATE_NAISS;";}
	if((isset($avec_lieu_naiss))&&($avec_lieu_naiss=='y')) {$fd.="LIEU_NAISS;";}

	if((isset($avec_email))&&($avec_email=='y')) {$fd.="EMAIL;";}
	if((isset($avec_statut))&&($avec_statut=='y')) {$fd.="STATUT;";}
	if($_SESSION['statut']!='professeur') {
		//if((isset($avec_ine))&&($avec_ine=='y')) {$fd.="INE;";}
		if((isset($avec_elenoet))&&($avec_elenoet=='y')) {$fd.="ELENOET;";}
		if((isset($avec_ele_id))&&($avec_ele_id=='y')) {$fd.="ELE_ID;";}
		if((isset($avec_no_gep))&&($avec_no_gep=='y')) {$fd.="INE;";}
	}

	// Suppression du ; en fin de ligne
	$fd=preg_replace('/;$/','',$fd);
	$fd.="\n";
}

if($current_group) {
	//echo "\$current_group<br />\n";
	//echo "\$avec_prof=$avec_prof<br />\n";
	if($_SESSION['statut']!='professeur') {
		if((isset($avec_prof))&&($avec_prof=='y')) {
			$sql="SELECT u.login, u.nom, u.prenom, u.email, u.civilite, u.numind FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.login=u.login AND jgp.id_groupe='$id_groupe';";
			//echo "$sql<br />\n";
			$res_prof=mysql_query($sql);
			if(mysql_num_rows($res_prof)>0) {
				while($lig=mysql_fetch_object($res_prof)) {
					$ligne="";
					if((isset($avec_classe))&&($avec_classe=='y')) {$ligne.=";";}
					if((isset($avec_login))&&($avec_login=='y')) {$ligne.="$lig->login;";}
					if((isset($avec_nom))&&($avec_nom=='y')) {$ligne.="$lig->nom;";}
					if((isset($avec_prenom))&&($avec_prenom=='y')) {$ligne.="$lig->prenom;";}
					if((isset($avec_sexe))&&($avec_sexe=='y')) {$ligne.="$lig->civilite;";}
					if((isset($avec_naiss))&&($avec_naiss=='y')) {$ligne.=";";}
					if((isset($avec_lieu_naiss))&&($avec_lieu_naiss=='y')) {$ligne.=";";}
				
					if((isset($avec_email))&&($avec_email=='y')) {$ligne.="$lig->email;";}
					if((isset($avec_statut))&&($avec_statut=='y')) {$ligne.="professeur;";}
					if($_SESSION['statut']!='professeur') {
						//if((isset($avec_ine))&&($avec_ine=='y')) {$ligne.=";";}
						if((isset($avec_elenoet))&&($avec_elenoet=='y')) {$ligne.="$lig->numind;";}
						if((isset($avec_ele_id))&&($avec_ele_id=='y')) {$ligne.=";";}
						if((isset($avec_no_gep))&&($avec_no_gep=='y')) {$ligne.=";";}
					}
				
					// Suppression du ; en fin de ligne
					$ligne=preg_replace('/;$/','',$ligne);
			
					$fd.=$ligne."\n";
				}
			}
		}
	}

	/*
	echo "\$periode_num=$periode_num<br />\n";
	foreach($current_group["eleves"][$periode_num]["users"] as $current_eleve) {
		echo $current_eleve['login']."<br />\n";
	}
	echo "<br />\n";
	echo "<br />\n";
	*/

	foreach($current_group["eleves"][$periode_num]["users"] as $current_eleve) {
	//foreach($current_group["eleves"]["all"]["users"] as $current_eleve) {
		$eleve_login = $current_eleve["login"];
		$eleve_nom = $current_eleve["nom"];
		$eleve_prenom = $current_eleve["prenom"];

		//$eleve_classe = $current_eleve["classe"];
		$sql="SELECT classe FROM classes WHERE id='".$current_eleve["classe"]."'";
		$res_tmp=mysql_query($sql);
		if(mysql_num_rows($res_tmp)==0){
			die("$eleve_login ne serait dans aucune classe???</body></html>");
		}
		else{
			$lig_tmp=mysql_fetch_object($res_tmp);
			$eleve_classe=$lig_tmp->classe;
		}

		// La fonction get_group() dans /lib/groupes.inc.php ne récupère pas le sexe et la date de naissance...
		// ... pourrait-on l'ajouter?
		$sql="SELECT sexe,naissance,lieu_naissance,email,no_gep,elenoet,ele_id FROM eleves WHERE login='$eleve_login'";
		$res_tmp=mysql_query($sql);

		if(mysql_num_rows($res_tmp)==0){
			die("Problème avec les infos de $eleve_login</body></html>");
		}
		else{
			$lig_tmp=mysql_fetch_object($res_tmp);
			$eleve_sexe=$lig_tmp->sexe;
			if((isset($format_naiss))&&($format_naiss=='jjmmaaaa')) {
				$eleve_naissance=formate_date($lig_tmp->naissance);
			}
			else {
				$eleve_naissance=$lig_tmp->naissance;
			}
			$eleve_email=$lig_tmp->email;
			$eleve_no_gep=$lig_tmp->no_gep;
			$eleve_elenoet=$lig_tmp->elenoet;
			$eleve_ele_id=$lig_tmp->ele_id;

			if($avec_lieu_naiss=='y') {
				$eleve_lieu_naissance=get_commune($lig_tmp->lieu_naissance,'2');
			}
		}

		//$fd.="$eleve_classe;$eleve_login;$eleve_nom;$eleve_prenom;$eleve_sexe;$eleve_naissance\n";

		$ligne="";
		if((isset($avec_classe))&&($avec_classe=='y')) {$ligne.="$eleve_classe;";}
		if((isset($avec_login))&&($avec_login=='y')) {$ligne.="$eleve_login;";}
		if((isset($avec_nom))&&($avec_nom=='y')) {$ligne.="$eleve_nom;";}
		if((isset($avec_prenom))&&($avec_prenom=='y')) {$ligne.="$eleve_prenom;";}
		if((isset($avec_sexe))&&($avec_sexe=='y')) {$ligne.="$eleve_sexe;";}
		if((isset($avec_naiss))&&($avec_naiss=='y')) {$ligne.="$eleve_naissance;";}
		if($avec_lieu_naiss=='y') {$ligne.="$eleve_lieu_naissance;";}

		if((isset($avec_email))&&($avec_email=='y')) {$ligne.="$eleve_email;";}
		if((isset($avec_statut))&&($avec_statut=='y')) {$ligne.="eleve;";}
		if($_SESSION['statut']!='professeur') {
			//if((isset($avec_ine))&&($avec_ine=='y')) {$ligne.="$eleve_no_gep;";}
			if((isset($avec_elenoet))&&($avec_elenoet=='y')) {$ligne.="$eleve_elenoet;";}
			if((isset($avec_ele_id))&&($avec_ele_id=='y')) {$ligne.="$eleve_ele_id;";}
			if((isset($avec_no_gep))&&($avec_no_gep=='y')) {$ligne.="$eleve_no_gep;";}
		}
	
		// Suppression du ; en fin de ligne
		$ligne=preg_replace('/;$/','',$ligne);

		$fd.=$ligne."\n";
	}
} else {

	$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.*
	    FROM eleves e, j_eleves_classes j
	    WHERE (
	    j.id_classe='".$id_classe."' AND
	    j.login = e.login AND
	    periode='".$periode_num."'
	    ) ORDER BY nom, prenom");
	$nombre_lignes = mysql_num_rows($appel_donnees_eleves);
	$i = 0;
	while($i < $nombre_lignes) {
		$eleve_login = mysql_result($appel_donnees_eleves, $i, "login");
		$eleve_nom = mysql_result($appel_donnees_eleves, $i, "nom");
		$eleve_prenom = mysql_result($appel_donnees_eleves, $i, "prenom");
		$eleve_sexe = mysql_result($appel_donnees_eleves, $i, "sexe");
		$eleve_naissance = mysql_result($appel_donnees_eleves, $i, "naissance");
		if((isset($format_naiss))&&($format_naiss=='jjmmaaaa')) {
			$eleve_naissance=formate_date($eleve_naissance);
		}
		if($avec_lieu_naiss=='y') {
			$eleve_lieu_naissance=get_commune(mysql_result($appel_donnees_eleves, $i, "lieu_naissance"),'2');
		}

		//$fd.="$classe;$eleve_login;$eleve_nom;$eleve_prenom;$eleve_sexe;$eleve_naissance\n";

		$eleve_email=mysql_result($appel_donnees_eleves, $i, "email");
		$eleve_no_gep=mysql_result($appel_donnees_eleves, $i, "no_gep");
		$eleve_elenoet=mysql_result($appel_donnees_eleves, $i, "elenoet");
		$eleve_ele_id=mysql_result($appel_donnees_eleves, $i, "ele_id");

		$ligne="";
		if((isset($avec_classe))&&($avec_classe=='y')) {$ligne.="$classe;";}
		if((isset($avec_login))&&($avec_login=='y')) {$ligne.="$eleve_login;";}
		if((isset($avec_nom))&&($avec_nom=='y')) {$ligne.="$eleve_nom;";}
		if((isset($avec_prenom))&&($avec_prenom=='y')) {$ligne.="$eleve_prenom;";}
		if((isset($avec_sexe))&&($avec_sexe=='y')) {$ligne.="$eleve_sexe;";}
		if((isset($avec_naiss))&&($avec_naiss=='y')) {$ligne.="$eleve_naissance;";}
		if((isset($avec_lieu_naiss))&&($avec_lieu_naiss=='y')) {$ligne.="$eleve_lieu_naissance;";}

		if((isset($avec_email))&&($avec_email=='y')) {$ligne.="$eleve_email;";}
		if((isset($avec_statut))&&($avec_statut=='y')) {$ligne.="eleve;";}
		if($_SESSION['statut']!='professeur') {
			//if((isset($avec_ine))&&($avec_ine=='y')) {$ligne.="$eleve_no_gep;";}
			if((isset($avec_elenoet))&&($avec_elenoet=='y')) {$ligne.="$eleve_elenoet;";}
			if((isset($avec_ele_id))&&($avec_ele_id=='y')) {$ligne.="$eleve_ele_id;";}
			if((isset($avec_no_gep))&&($avec_no_gep=='y')) {$ligne.="$eleve_no_gep;";}
		}
	
		// Suppression du ; en fin de ligne
		$ligne=preg_replace('/;$/','',$ligne);

		$fd.=$ligne."\n";

		$i++;
	}
}
echo echo_csv_encoded($fd);

?>
