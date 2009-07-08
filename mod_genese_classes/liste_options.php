<?php
/* $Id$ */
/*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
};

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/liste_options.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/liste_options.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Génèse des classes: Liste des options de classes existantes',
statut='';";
$insert=mysql_query($sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$projet=isset($_POST['projet']) ? $_POST['projet'] : (isset($_GET['projet']) ? $_GET['projet'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

#for i in nom prenom sexe naissance login elenoet ele_id no_gep email matiere;do echo "\$$i=isset(\$_POST['$i']) ? \$_POST['$i'] : NULL;";done
$nom=isset($_POST['nom']) ? $_POST['nom'] : NULL;
$prenom=isset($_POST['prenom']) ? $_POST['prenom'] : NULL;
$sexe=isset($_POST['sexe']) ? $_POST['sexe'] : NULL;
$naissance=isset($_POST['naissance']) ? $_POST['naissance'] : NULL;
$champ_login=isset($_POST['champ_login']) ? $_POST['champ_login'] : NULL;
$elenoet=isset($_POST['elenoet']) ? $_POST['elenoet'] : NULL;
$ele_id=isset($_POST['ele_id']) ? $_POST['ele_id'] : NULL;
$no_gep=isset($_POST['no_gep']) ? $_POST['no_gep'] : NULL;
$email=isset($_POST['email']) ? $_POST['email'] : NULL;
$classe=isset($_POST['classe']) ? $_POST['classe'] : NULL;
$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : NULL;

if(isset($_POST['choix_param'])) {

	$fich="";
	$ligne_entete="";
	$cpt=0;
	for($i=0;$i<count($id_classe);$i++) {
		$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe[$i]' ORDER BY nom,prenom;";
		$res=mysql_query($sql);
		while($lig=mysql_fetch_object($res)) {
			$ligne="";

			if((isset($nom))&&($nom=='y')) {
				$ligne.=$lig->nom.";";
				if($cpt==0) {$ligne_entete.="NOM;";}
			}

			if((isset($prenom))&&($prenom=='y')) {
				$ligne.=$lig->prenom.";";
				if($cpt==0) {$ligne_entete.="PRENOM;";}
			}

			if((isset($sexe))&&($sexe=='y')) {
				$ligne.=$lig->sexe.";";
				if($cpt==0) {$ligne_entete.="SEXE;";}
			}

			if((isset($naissance))&&($naissance=='y')) {
				$ligne.=$lig->naissance.";";
				if($cpt==0) {$ligne_entete.="NAISSANCE;";}
			}

			if((isset($champ_login))&&($champ_login=='y')) {
				$ligne.=$lig->login.";";
				if($cpt==0) {$ligne_entete.="LOGIN;";}
			}

			if((isset($elenoet))&&($elenoet=='y')) {
				$ligne.=$lig->elenoet.";";
				if($cpt==0) {$ligne_entete.="ELENOET;";}
			}

			if((isset($ele_id))&&($ele_id=='y')) {
				$ligne.=$lig->ele_id.";";
				if($cpt==0) {$ligne_entete.="ELE_ID;";}
			}

			if((isset($no_gep))&&($no_gep=='y')) {
				$ligne.=$lig->no_gep.";";
				if($cpt==0) {$ligne_entete.="INE;";}
			}

			if((isset($email))&&($email=='y')) {
				$ligne.=$lig->email.";";
				if($cpt==0) {$ligne_entete.="EMAIL;";}
			}

			if((isset($classe))&&($classe=='y')) {
				if($cpt==0) {$ligne_entete.="CLASSE;";}

				$sql="SELECT DISTINCT c.classe FROM classes c, j_eleves_classes jec WHERE jec.login='$lig->login' AND jec.id_classe=c.id ORDER BY jec.periode;";
				$res_clas=mysql_query($sql);
				$cpt2=0;
				while($lig_clas=mysql_fetch_object($res_clas)) {
					if($cpt2>0) {$ligne.=" ";}
					$ligne.=$lig_clas->classe;
					$cpt2++;
				}
				$ligne.=";";
			}

			if((isset($matiere))&&(count($matiere)>0)) {
				for($j=0;$j<count($matiere);$j++) {
					if($cpt==0) {$ligne_entete.="$matiere[$j];";}

					$sql="SELECT 1=1 FROM j_groupes_matieres jgm, j_eleves_groupes jeg WHERE jeg.login='$lig->login' AND jgm.id_groupe=jeg.id_groupe AND jgm.id_matiere='$matiere[$j]';";
					//echo "$sql<br />";
					$res_grp=mysql_query($sql);
					if(mysql_num_rows($res_grp)>0) {$ligne.="1";}
					$ligne.=";";
				}
			}

			if($cpt==0) {$fich.=$ligne_entete."\n";}
			$fich.=$ligne."\n";
			$cpt++;
		}
	}


	$nom_fic="options_eleves_gepi_".date("Ymd_Hi").".csv";
	$now = gmdate('D, d M Y H:i:s') . ' GMT';
	header('Content-Type: text/x-csv');
	header('Expires: ' . $now);
	if (ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
		header('Content-Disposition: inline; filename="' . $nom_fic . '"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	} else {
		header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
		header('Pragma: no-cache');
	}

	echo $fich;
	die();
}


//**************** EN-TETE *****************
$titre_page = "Génèse classe: Liste des options";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

if((!isset($projet))||($projet=="")) {
	echo "<p style='color:red'>ERREUR: Le projet n'est pas choisi.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='index.php?projet=$projet'>Retour</a>";
echo "</p>\n";
//echo "</div>\n";

echo "<h2>Projet $projet</h2>\n";

$sql="SELECT id_classe FROM gc_divisions WHERE projet='$projet' AND statut='actuelle';";
$res=mysql_query($sql);
while($lig=mysql_fetch_object($res)) {
	$tab_id_classe[]=$lig->id_classe;
}

echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";

echo "<table summary='Choix des paramètres'>\n";
echo "<tr>\n";
echo "<td valign='top'>\n";
	echo "<p>Choix des classes&nbsp;:\n";
	echo "</p>\n";
	
	$sql="SELECT id,classe FROM classes ORDER BY classe;";
	$res_classes=mysql_query($sql);
	$nb_classes=mysql_num_rows($res_classes);
	
	// Affichage sur 4/5 colonnes
	$nb_classes_par_colonne=round($nb_classes/3);
	
	echo "<table width='100%' summary='Choix des classes'>\n";
	echo "<tr valign='top' align='center'>\n";
	
	$cpt_i = 0;
	
	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";
	
	while($lig_clas=mysql_fetch_object($res_classes)) {
	
		//affichage 2 colonnes
		if(($cpt_i>0)&&(round($cpt_i/$nb_classes_par_colonne)==$cpt_i/$nb_classes_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}
	
		echo "<input type='checkbox' name='id_classe[]' id='id_classe_$cpt_i' value='$lig_clas->id' ";
		if(in_array($lig_clas->id,$tab_id_classe)) {echo "checked ";}
		echo "/><label for='id_classe_$cpt_i'>$lig_clas->classe</label>";
		echo "<input type='hidden' name='classe[$lig_clas->id]' value='$lig_clas->classe' />";
		echo "<br />\n";
		$cpt_i++;
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
echo "</td>\n";
echo "<td valign='top'>\n";
	echo "<p>Choix des informations à faire apparaître:<br />\n";
	echo "<blockquote>\n";
	echo "<p><input type='checkbox' name='nom' id='nom' value='y' checked /><label for='nom'>Nom</label><br />\n"; 
	echo "<input type='checkbox' name='prenom' id='prenom' value='y' checked /><label for='prenom'>Prénom</label><br />\n"; 
	echo "<input type='checkbox' name='sexe' id='sexe' value='y' /><label for='sexe'>sexe</label><br />\n"; 
	echo "<input type='checkbox' name='naissance' id='naissance' value='y' /><label for='naissance'>naissance</label><br />\n"; 
	echo "<input type='checkbox' name='champ_login' id='login' value='y' /><label for='login'>Login</label><br />\n"; 
	echo "<input type='checkbox' name='elenoet' id='elenoet' value='y' /><label for='elenoet'>Numéro elenoet</label><br />\n"; 
	echo "<input type='checkbox' name='ele_id' id='ele_id' value='y' /><label for='ele_id'>Numéro ele_id</label><br />\n"; 
	echo "<input type='checkbox' name='no_gep' id='no_gep' value='y' /><label for='no_gep'>Numéro INE</label><br />\n"; 
	echo "<input type='checkbox' name='email' id='email' value='y' /><label for='email'>email</label><br />\n"; 
	
	echo "<input type='checkbox' name='classe' id='classe' value='y' /><label for='classe'>Classe</label></p>\n"; 
	echo "</blockquote>\n";
echo "</td>\n";
echo "<td valign='top'>\n";
	echo "<p>Choix des matières à faire apparaître:<br />\n";
	echo "<blockquote>\n";
	$sql="SELECT matiere FROM matieres ORDER BY matiere;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$cpt=0;
		echo "<div style='width: 20em; height: 20em; overflow: auto;'>";
		while($lig=mysql_fetch_object($res)) {
			echo "<input type='checkbox' name='matiere[]' id='matiere_$cpt' value='$lig->matiere' /><label for='matiere_$cpt'>$lig->matiere</label><br />";
			$cpt++;
		}
		echo "</div>\n";
	}
	echo "</blockquote>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "<p><input type='submit' name='choix_param' value='Valider' /></p>\n";
echo "</form>\n";

echo "<p>Cette page est destinée à générer un CSV des options à pointer en conseil de classe pour les préparatifs de conception de classe de l'année suivante.<br />
Il conviendra d'ajouter des lignes de totaux (SOMME()) à l'aide du tableur.<br />
Les champs comme login, elenoet, ele_id,... sont destinés à faciliter un import en retour des choix dans les tables 'gc_*'.</p>\n";


require("../lib/footer.inc.php");
?>
