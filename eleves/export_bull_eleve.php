<?php

/*
 *
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$titre_page = "Export bulletin eleve";

$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions complémentaires et/ou librairies utiles


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
// SQL : INSERT INTO droits VALUES ( '/eleves/export_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Exportation bulletin élève', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/eleves/export_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Exportation bulletin élève', '');";
//


if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}





/*
function get_content($db, $table,$from,$limit) {
    $search       = array("\x00", "\x0a", "\x0d", "\x1a");
    $replace      = array('\0', '\n', '\r', '\Z');
    // les données de la table
    $def = '';
    $query = "SELECT * FROM $table LIMIT $from,$limit";
    $resData = @mysql_query($query);
    //peut survenir avec la corruption d'une table, on prévient
    if (!$resData) {
        $def .="Problème avec les données de $table, corruption possible !\n";
    } else {
        if (@mysql_num_rows($resData) > 0) {
             $sFieldnames = "";
             $num_fields = mysql_num_fields($resData);
              $sInsert = "INSERT INTO $table $sFieldnames values ";
              while($rowdata = mysql_fetch_row($resData)) {
                  $lesDonnees = "";
                  for ($mp = 0; $mp < $num_fields; $mp++) {
                  $lesDonnees .= "'" . str_replace($search, $replace, traitement_magic_quotes($rowdata[$mp])) . "'";
                  //on ajoute à la fin une virgule si nécessaire
                      if ($mp<$num_fields-1) $lesDonnees .= ", ";
                  }
                  $lesDonnees = "$sInsert($lesDonnees);\n";
                  $def .="$lesDonnees";
              }
        }
     }
     return $def;
}
*/

function get_bull($ele_login) {
	$lignes="";

    $search       = array("\x00", "\x0a", "\x0d", "\x1a");
    $replace      = array('\0', '\n', '\r', '\Z');

	$sql="SELECT * FROM eleves WHERE login='$ele_login';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$lignes.="INFOS_ELEVE;$ele_login;".str_replace($search, $replace, traitement_magic_quotes($lig->nom)).";".str_replace($search, $replace, traitement_magic_quotes($lig->prenom)).";".$lig->naissance.";".$lig->no_gep."\n";
		}
	}

	$gepiSchoolName=getSettingValue('gepiSchoolName');
	$gepiSchoolCity=getSettingValue('gepiSchoolCity');
	$gepiSchoolRne=getSettingValue('gepiSchoolRne');
	$lignes.="INFOS_ETABLISSEMENT;".str_replace($search, $replace, traitement_magic_quotes($gepiSchoolName)).";".str_replace($search, $replace, traitement_magic_quotes($gepiSchoolCity)).";".str_replace($search, $replace, traitement_magic_quotes($gepiSchoolRne))."\n";

	/*
	$sql="SELECT m.nom_complet, m.matiere, mn.*, ma.appreciation FROM matieres_notes mn, matieres_appreciations ma, j_groupes_matieres jgm, matieres m
			WHERE mn.login='$ele_login' AND
				mn.login=ma.login AND
				mn.id_groupe=ma.id_groupe AND
				mn.periode=ma.periode AND
				jgm.id_groupe=mn.id_groupe AND
				m.matiere=jgm.id_matiere
			ORDER BY mn.periode, m.matiere
				;";
	*/
	$sql="SELECT m.nom_complet, m.matiere, mn.* FROM matieres_notes mn, j_groupes_matieres jgm, matieres m
			WHERE mn.login='$ele_login' AND
				jgm.id_groupe=mn.id_groupe AND
				m.matiere=jgm.id_matiere
			ORDER BY mn.periode, m.matiere
				;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			//$lignes.="$lig->matiere;$lig->nom_complet;$lig->periode;$lig->note;$lig->statut;$lig->appreciation\n";
			//$lignes.="$lig->matiere;$lig->nom_complet;$lig->periode;$lig->note;$lig->statut;".str_replace($search, $replace, traitement_magic_quotes($lig->appreciation))."\n";
			$app="";
			$sql="SELECT appreciation FROM matieres_appreciations
				WHERE login='$ele_login' AND
				id_groupe='$lig->id_groupe' AND
				periode='$lig->periode';";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res2)>0) {
				$lig2=mysqli_fetch_object($res2);
				$app=$lig2->appreciation;
			}
			//$lignes.="$lig->matiere;$lig->nom_complet;$lig->periode;$lig->id_groupe;$lig->note;$lig->statut;".str_replace($search, $replace, traitement_magic_quotes($app))."\n";
			$lignes.="$lig->matiere;$lig->nom_complet;$lig->periode;$lig->id_groupe;$lig->note;$lig->statut;".str_replace($search, $replace, traitement_magic_quotes(my_ereg_replace(";","_POINT_VIRGULE_",$app)))."\n";
		}
	}

	$sql="SELECT * FROM avis_conseil_classe WHERE login='$ele_login' ORDER BY periode;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			//$lignes.="AVIS_CONSEIL_CLASSE;$lig->periode;".str_replace($search, $replace, traitement_magic_quotes($lig->avis))."\n";
			$lignes.="AVIS_CONSEIL_CLASSE;$lig->periode;".str_replace($search, $replace, traitement_magic_quotes(my_ereg_replace(";","_POINT_VIRGULE_",$lig->avis)))."\n";
		}
	}


	// Récupérer aussi les absences?
	$sql="SELECT * FROM absences WHERE login='$ele_login' ORDER BY periode;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			//$lignes.="ABSENCES;$lig->periode;$lig->nb_absences;$lig->non_justifie;$lig->nb_retards;".str_replace($search, $replace, traitement_magic_quotes($lig->appreciation))."\n";
			$lignes.="ABSENCES;$lig->periode;$lig->nb_absences;$lig->non_justifie;$lig->nb_retards;".str_replace($search, $replace, traitement_magic_quotes(my_ereg_replace(";","_POINT_VIRGULE_",$lig->appreciation)))."\n";
		}
	}


	return $lignes;
}


function get_nom_prenom_from_login($ele_login,$mode) {
	$retour="";

	$sql="SELECT nom,prenom FROM eleves WHERE login='$ele_login';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		$retour="LOGIN INCONNU";
	}
	else {
		$lig=mysqli_fetch_object($res);

		if($mode=="np") {
			$retour=my_strtoupper($lig->nom)." ".casse_mot($lig->prenom,'majf2');
		}
		else {
			$retour=casse_mot($lig->prenom,'majf2')." ".my_strtoupper($lig->nom);
		}
	}

	return $retour;
}

// PB: Il faut remplacer le login PROF par ANONYME_EXT... ou le nom de l'établissement
// A l'import, il faut avoir créé l'élève,
//             créer des enseignements? dans une classe EXTERIEUR... il faut une classe par élève...
//             si on a plusieurs arrivées, ça fait des matières en plus,... pas un pb...
//             seules les matières suivies par l'élève sont prises en compte...
//             créer des cours différents pour chaque élève pour éviter des moyennes de classe fantaisistes
// A l'export, une ligne pour l'association: LOGIN_ETAB -> Nom, prénom pour la table utilisateurs


// Nom: gepiSchoolName
// Prénom: gepiSchoolCity

// ======================== CSS et js particuliers ========================
$utilisation_win = "non";
$utilisation_jsdivdrag = "non";
//$javascript_specifique = ".js";
//$style_specifique = ".css";

// ===================== entete Gepi ======================================//
require_once("../lib/header.inc.php");
// ===================== fin entete =======================================//

$page="export_bull_eleve.php";

$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL);

//debug_var();

//echo "<div class='norme'><p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "<div class='norme'><p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

if(getSettingValue('exp_imp_chgt_etab')!='yes') {
	// Pour activer le dispositif:
	// DELETE FROM setting WHERE name='exp_imp_chgt_etab';INSERT INTO setting SET name='exp_imp_chgt_etab', value='yes';
	echo "<p>Cette page est destinée à exporter les moyennes et appréciations du bulletin d'un élève pour permettre un réimport dans un autre établissement.</p>\n";
	echo "<p><br /></p>\n";
	echo "<p>Le dispositif (<i>encore en cours de test au 17/07/2008</i>) ne semble pas activé.</p>\n";
	require_once("../lib/footer.inc.php");
	die();
}

//if(!isset($ele_login)) {
if((!isset($ele_login))&&(!isset($_POST['Recherche_sans_js']))) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Cette page est destinée à exporter les moyennes et appréciations du bulletin d'un élève pour permettre un réimport dans un autre établissement.</p>\n";

	// Formulaire pour navigateur SANS Javascript:
	echo "<noscript>
	<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire1'>
		Afficher les élèves dont le <b>nom</b> contient: <input type='text' name='rech_nom' value='' />
		<input type='hidden' name='page' value='$page' />
		<input type='submit' name='Recherche_sans_js' value='Rechercher' />
	</form>
</noscript>\n";

	// Portion d'AJAX:
	echo "<script type='text/javascript'>
	function cherche_eleves() {
		rech_nom=document.getElementById('rech_nom').value;

		var url = 'liste_eleves.php';
		var myAjax = new Ajax.Request(
			url,
			{
				method: 'post',
				postBody: 'rech_nom='+rech_nom+'&page=$page',
				onComplete: affiche_eleves
			});
	}

	function affiche_eleves(xhr) {
		if (xhr.status == 200) {
			document.getElementById('liste_eleves').innerHTML = xhr.responseText;
		}
		else {
			document.getElementById('liste_eleves').innerHTML = xhr.status;
		}
	}
</script>\n";

	// DIV avec formulaire pour navigateur AVEC Javascript:
	echo "<div id='recherche_avec_js' style='display:none;'>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' onsubmit='cherche_eleves();return false;' method='post' name='formulaire'>";
	echo "Afficher les élèves dont le <b>nom</b> contient: <input type='text' name='rech_nom' id='rech_nom' value='' />\n";
	echo "<input type='hidden' name='page' value='$page' />\n";
	echo "<input type='button' name='Recherche' value='Rechercher' onclick='cherche_eleves()' />\n";
	echo "</form>\n";

	echo "<div id='liste_eleves'></div>\n";

	echo "</div>\n";
	echo "<script type='text/javascript'>document.getElementById('recherche_avec_js').style.display='';</script>\n";

}
elseif(isset($_POST['Recherche_sans_js'])) {
	// On ne passe ici que si JavaScript est désactivé
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre élève</a>\n";
	echo "</p>\n";
	echo "</div>\n";

	include("recherche_eleve.php");
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre élève</a>\n";
	echo "</p>\n";
	echo "</div>\n";

	$info_eleve=get_nom_prenom_from_login($ele_login,"pn");
	if($info_eleve=="LOGIN INCONNU") {
		echo "<p>Le login '$ele_login' est inconnu dans la table 'eleves'.</p>\n";

		require_once("../lib/footer.inc.php");
		die;
	}

	echo "<p>Vous avez choisi ".$info_eleve."</p>\n";

	//echo nl2br(get_bull($ele_login));

	$fich=fopen("../temp/".$ele_login.".csv","w+");
	fwrite($fich,get_bull($ele_login));
	fclose($fich);

	echo "<p><a href=\"../temp/".$ele_login.".csv\">Télécharger le CSV</a></p>\n";

}


// Inclusion du bas de page
require_once("../lib/footer.inc.php");
?>
