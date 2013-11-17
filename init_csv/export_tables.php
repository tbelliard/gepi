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

$sql="SELECT 1=1 FROM droits WHERE id='/init_csv/export_tables.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/init_csv/export_tables.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Initialisation CSV: Export tables',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//**************** EN-TETE *****************
$titre_page = "Export des tables";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<?php 

//if(!isset($_GET['export'])) {
if((!isset($_GET['export']))&&(!isset($_GET['nettoyer']))) {
	echo "<p>Vous pouvez exporter le contenu actuel des tables au format CSV.<br />\n";
	echo "Les champs des fichiers obtenus seront conformes à ce qui est requis pour une initialisation CSV.</p>\n";

	echo "<p>Les fichiers ainsi obtenus peuvent permettre de préparer une nouvelle année, si peu de modifications sont requises.</p>\n";

	echo "<p><a href='".$_SERVER['PHP_SELF']."?export=y'>Exporter</a></p>\n";

	require("../lib/footer.inc.php");
	die();
}

if($_SESSION['user_temp_directory']!='y') {
	echo "<p>ERREUR&nbsp;: Le dossier temporaire ne semble pas accessible.<br />Les fichiers CSV ne pourront pas être générés.</p>\n";

	require("../lib/footer.inc.php");
	die();
}

$dirname=get_user_temp_directory();

if(isset($_GET['nettoyer'])) {
	echo "<p>Nettoyage du dossier temporaire $dirname.</p>\n";

	$tab_fich=array("g_eleves.csv", "g_eleves_classes.csv", "g_eleves_options.csv", "g_responsables.csv", "g_disciplines.csv", "g_professeurs.csv", "g_prof_disc_classes.csv");
	for($i=0;$i<count($tab_fich);$i++) {
		@unlink("../temp/$dirname/$tab_fich[$i]");
	}
	echo "<p>Terminé.</p>\n";

	require("../lib/footer.inc.php");
	die();
}

echo "<p>Génération des fichiers CSV au format 'init_csv'&nbsp;:</p>\n";

// Nom ; Prénom ; Date de naissance ; n° identifiant interne (étab) ; n° identifiant national ; Code établissement précédent ; Doublement (OUI | NON) ; Régime (INTERN | EXTERN | IN.EX. | DP DAN) ; Sexe (F ou M)
$sql="SELECT * FROM eleves e, j_eleves_regime jer WHERE jer.login=e.login ORDER BY e.nom, e.prenom;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>Aucun élève n'a été trouvé.</p>\n";
}
else {
	$lignes="";
	while($lig=mysqli_fetch_object($res)) {
		$lignes.="$lig->nom;$lig->prenom;".formate_date($lig->naissance).";$lig->elenoet;$lig->no_gep;";

		$sql="SELECT * FROM j_eleves_etablissements WHERE id_eleve='$lig->elenoet';";
		$res2=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res2)>0) {
			$lig2=mysqli_fetch_object($res2);
			$lignes.=$lig2->id_etablissement;
		}
		$lignes.=";";

		if($lig->doublant=='O') {$lignes.="OUI;";} else {$lignes.="NON;";}

		if($lig->regime=='d/p') {
			$lignes.="DP DAN";
		}
		elseif($lig->regime=='ext.') {
			$lignes.="EXTERN";
		}
		elseif($lig->regime=='i-e') {
			$lignes.="IN.EX.";
		}
		elseif($lig->regime=='int.') {
			$lignes.="INTERN";
		}
		$lignes.=";";

		$lignes.="$lig->sexe;\r\n";
	}

	$fich=fopen("../temp/".$dirname."/g_eleves.csv","w+");
	if(!$fich) {
		echo "<p>ERREUR&nbsp;: Le fichier g_eleves.csv n'a pas pu être généré dans le dossier temporaire.</p>\n";
	}
	else {
		fwrite($fich,$lignes);
		fclose($fich);
	}

	echo "<a href='../temp/".$dirname."/g_eleves.csv'>g_eleves.csv</a><br />\n";
}

//n° d'identifiant élève interne à l'établissement ; Nom du responsable ; Prénom du responsable ; Civilité ; Ligne 1 Adresse ; Ligne 2 Adresse ; Code postal ; Commune
$sql="SELECT r.*, rp.*, ra.*, e.elenoet FROM eleves e, responsables2 r, resp_pers rp, resp_adr ra WHERE r.pers_id=rp.pers_id AND rp.adr_id=ra.adr_id AND r.ele_id=e.ele_id AND r.resp_legal!='0' ORDER BY rp.nom, rp.prenom;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>Aucun responsable n'a été trouvé.</p>\n";
}
else {
	$lignes="";
	while($lig=mysqli_fetch_object($res)) {
		$lignes.="$lig->elenoet;$lig->nom;$lig->prenom;$lig->civilite;$lig->adr1;$lig->adr2;$lig->cp;$lig->adr1;$lig->commune;";
		$lignes.="\r\n";
	}

	$fich=fopen("../temp/".$dirname."/g_responsables.csv","w+");
	if(!$fich) {
		echo "<p>ERREUR&nbsp;: Le fichier g_responsables.csv n'a pas pu être généré dans le dossier temporaire.</p>\n";
	}
	else {
		fwrite($fich,$lignes);
		fclose($fich);
	}

	echo "<a href='../temp/".$dirname."/g_responsables.csv'>g_responsables.csv</a><br />\n";
}

//Nom court matière ; Nom long matière
$sql="SELECT matiere, nom_complet FROM matieres;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>Aucune matière n'a été trouvée.</p>\n";
}
else {
	$lignes="";
	while($lig=mysqli_fetch_object($res)) {
		$lignes.="$lig->matiere;$lig->nom_complet;";
		$lignes.="\r\n";
	}

	$fich=fopen("../temp/".$dirname."/g_disciplines.csv","w+");
	if(!$fich) {
		echo "<p>ERREUR&nbsp;: Le fichier g_disciplines.csv n'a pas pu être généré dans le dossier temporaire.</p>\n";
	}
	else {
		fwrite($fich,$lignes);
		fclose($fich);
	}

	echo "<a href='../temp/".$dirname."/g_disciplines.csv'>g_disciplines.csv</a><br />\n";
}

//Nom ; Prénom ; Civilité ; Adresse e-mail
$sql="SELECT * FROM utilisateurs WHERE statut='professeur' ORDER BY nom, prenom;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>Aucun professeur n'a été trouvé.</p>\n";
}
else {
	$lignes="";
	while($lig=mysqli_fetch_object($res)) {
		$lignes.="$lig->nom;$lig->prenom;$lig->civilite;$lig->email;";
		$lignes.="\r\n";
	}

	$fich=fopen("../temp/".$dirname."/g_professeurs.csv","w+");
	if(!$fich) {
		echo "<p>ERREUR&nbsp;: Le fichier g_professeurs.csv n'a pas pu être généré dans le dossier temporaire.</p>\n";
	}
	else {
		fwrite($fich,$lignes);
		fclose($fich);
	}

	echo "<a href='../temp/".$dirname."/g_professeurs.csv'>g_professeurs.csv</a><br />\n";
}

//n° d'identifiant élève interne à l'établissement ; Identifiant court de la classe 
$tab_ele_clas=array();
$sql="SELECT DISTINCT e.elenoet, c.classe FROM eleves e, j_eleves_classes jec, classes c WHERE jec.login=e.login AND c.id=jec.id_classe ORDER BY c.classe,e.nom,e.prenom;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>Aucune association élève/classe n'a été trouvée.</p>\n";
}
else {
	$lignes="";
	while($lig=mysqli_fetch_object($res)) {
		$tab_ele_clas[$lig->elenoet]=$lig->classe;

		$lignes.="$lig->elenoet;$lig->classe;";
		$lignes.="\r\n";
	}

	$fich=fopen("../temp/".$dirname."/g_eleves_classes.csv","w+");
	if(!$fich) {
		echo "<p>ERREUR&nbsp;: Le fichier g_eleves_classes.csv n'a pas pu être généré dans le dossier temporaire.</p>\n";
	}
	else {
		fwrite($fich,$lignes);
		fclose($fich);
	}

	echo "<a href='../temp/".$dirname."/g_eleves_classes.csv'>g_eleves_classes.csv</a><br />\n";
}

//Login du professeur ; Nom court de la matière ; Le ou les identifiants de classe (séparés par des !) ; Le type de cours (CG (= cours général) | OPT (= option))
$sql="SELECT * FROM utilisateurs WHERE statut='professeur' ORDER BY nom, prenom;";
$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_prof)==0) {
	echo "<p>Aucun professeur n'a été trouvé.</p>\n";
}
else {
	$lignes="";
	while($lig_prof=mysqli_fetch_object($res_prof)) {
		//$sql="SELECT jgm.id_matiere, FROM j_groupes_professeurs jgp, j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgm.id_groupe=jgp.id_groupe AND jgc.id_groupe=jgp.id_groupe AND jgp.login='$lig->login' ORDER BY jgc.id_classe, jgm.id_matiere;";
		$sql="SELECT jgm.id_matiere, jgm.id_groupe FROM j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE jgm.id_groupe=jgp.id_groupe AND jgp.login='$lig_prof->login' ORDER BY jgm.id_matiere;";
		$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_grp)>0) {
			while($lig_grp=mysqli_fetch_object($res_grp)) {
				$sql="SELECT DISTINCT c.id, c.classe FROM j_groupes_classes jgc, classes c WHERE jgc.id_groupe='$lig_grp->id_groupe' AND jgc.id_classe=c.id ORDER BY c.classe;";
				$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_clas)>0) {
					$classes="";
					$cpt_classes=0;
					$current_id_classe="";
					$first_classe="";
					while($lig_clas=mysqli_fetch_object($res_clas)) {
						if($cpt_classes>0) {
							$classes.="!";
							$tab_clas_opt["$lig_clas->classe"][]=$lig_grp->id_matiere;
						}
						else {
							$first_classe=$lig_clas->classe;
						}

						$classes.=$lig_clas->classe;

						$current_id_classe=$lig_clas->id;

						$cpt_classes++;
					}

					$lignes.="$lig_prof->login;$lig_grp->id_matiere;$classes;";

					// Est-ce un cours général? ou une option?
					if($cpt_classes>1) {
						$lignes.="OPT;";
						$tab_clas_opt["$first_classe"][]=$lig_grp->id_matiere;
					}
					else {
						// Tous les élèves de la classe suivent-ils l'enseignement?
						$sql="SELECT 1=1 FROM j_eleves_groupes WHERE id_groupe='$lig_grp->id_groupe';";
						$test1=mysqli_query($GLOBALS["mysqli"], $sql);

						//$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$lig_clas->id';";
						$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$current_id_classe';";
						$test2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test1)==mysqli_num_rows($test2)) {
							$lignes.="CG;";
						}
						else {
							$lignes.="OPT;";
							$tab_clas_opt["$first_classe"][]=$lig_grp->id_matiere;
						}
					}
					$lignes.="\r\n";
				}
			}
		}
	}

	$fich=fopen("../temp/".$dirname."/g_prof_disc_classes.csv","w+");
	if(!$fich) {
		echo "<p>ERREUR&nbsp;: Le fichier g_prof_disc_classes.csv n'a pas pu être généré dans le dossier temporaire.</p>\n";
	}
	else {
		fwrite($fich,$lignes);
		fclose($fich);
	}

	echo "<a href='../temp/".$dirname."/g_prof_disc_classes.csv'>g_prof_disc_classes.csv</a><br />\n";
}

//n° d'identifiant élève interne à l'établissement ; Identifiants des matières suivies en option, séparés par des !
$sql="SELECT e.elenoet, e.login FROM eleves e ORDER BY e.nom,e.prenom;";
$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_ele)==0) {
	echo "<p>Aucun élève n'a été trouvé.</p>\n";
}
else {
	$lignes="";
	while($lig_ele=mysqli_fetch_object($res_ele)) {

		$sql="SELECT DISTINCT jgm.id_matiere FROM j_eleves_groupes jeg, j_groupes_matieres jgm WHERE jeg.login='$lig_ele->login' AND jeg.id_groupe=jgm.id_groupe ORDER BY id_matiere;";
		$res_matiere=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_matiere)>0) {
			$matieres="";
			$nb_mat=0;
			while($lig_mat=mysqli_fetch_object($res_matiere)) {
				$current_elenoet=$lig_ele->elenoet;
				if(isset($tab_clas_opt["$tab_ele_clas[$current_elenoet]"])) {
					if(in_array($lig_mat->id_matiere,$tab_clas_opt["$tab_ele_clas[$current_elenoet]"])) {
						if($nb_mat>0) {
							$matieres.="!";
						}
		
						$matieres.=$lig_mat->id_matiere;
		
						$nb_mat++;
					}
				}
			}

			if($nb_mat>0) {
				$lignes.="$lig_ele->elenoet;$matieres;";
				$lignes.="\r\n";
			}
		}
	}

	$fich=fopen("../temp/".$dirname."/g_eleves_options.csv","w+");
	if(!$fich) {
		echo "<p>ERREUR&nbsp;: Le fichier g_eleves_options.csv n'a pas pu être généré dans le dossier temporaire.</p>\n";
	}
	else {
		fwrite($fich,$lignes);
		fclose($fich);
	}

	echo "<a href='../temp/".$dirname."/g_eleves_options.csv'>g_eleves_options.csv</a><br />\n";

}

echo "<p>Les fichiers ont été générés dans un dossier temporaire.<br />Après récupération des fichiers, vous devriez faire le ménage dans ce dossier temporaire.<br /><a href='".$_SERVER['PHP_SELF']."?nettoyer=y'>Supprimer les fichiers générés</a></p>\n";

require("../lib/footer.inc.php");
?>
