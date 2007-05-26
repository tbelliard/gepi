<?php
/*
 * $Id : $
 *
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
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();};

// INSERT INTO droits VALUES ('/gestion/config_prefs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des préférences d utilisateurs', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


function getPref($login,$item,$default){
	$sql="SELECT value FROM preferences WHERE login='$login' AND name='$item'";
	$res_prefs=mysql_query($sql);

	if(mysql_num_rows($res_prefs)>0){
		$ligne=mysql_fetch_object($res_prefs);
		return $ligne->value;
	}
	else{
		return $default;
	}
}



$prof=isset($_POST['prof']) ? $_POST['prof'] : NULL;
$page=isset($_POST['page']) ? $_POST['page'] : NULL;
$enregistrer=isset($_POST['enregistrer']) ? $_POST['enregistrer'] : NULL;
$msg="";

if(isset($enregistrer)){

	for($i=0;$i<count($prof);$i++){
		if($page=='add_modif_dev'){
			$tab=array('add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_date','add_modif_dev_boite');
			for($j=0;$j<count($tab);$j++){
				unset($valeur);
				$valeur=isset($_POST[$tab[$j]]) ? $_POST[$tab[$j]] : NULL;

				if(isset($valeur)){
					$sql="DELETE FROM preferences WHERE login='".$prof[$i]."' AND name='".$tab[$j]."'";
					//echo $sql."<br />\n";
					$res_suppr=mysql_query($sql);
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='$valeur'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Erreur lors de l'enregistrement de $tab[$j] pour $prof[$i]<br />\n";
					}
				}
			}
		}
		elseif($page=='add_modif_conteneur'){
			$tab=array('add_modif_conteneur_simpl','add_modif_conteneur_nom_court','add_modif_conteneur_nom_complet','add_modif_conteneur_description','add_modif_conteneur_coef','add_modif_conteneur_boite','add_modif_conteneur_aff_display_releve_notes','add_modif_conteneur_aff_display_bull');
			for($j=0;$j<count($tab);$j++){
				unset($valeur);
				$valeur=isset($_POST[$tab[$j]]) ? $_POST[$tab[$j]] : NULL;

				if(isset($valeur)){
					$sql="DELETE FROM preferences WHERE login='".$prof[$i]."' AND name='".$tab[$j]."'";
					//echo $sql."<br />\n";
					$res_suppr=mysql_query($sql);
					$sql="INSERT INTO preferences SET login='".$prof[$i]."', name='".$tab[$j]."', value='$valeur'";
					//echo $sql."<br />\n";
					if($res_insert=mysql_query($sql)){
					}
					else{
						$msg.="Erreur lors de l'enregistrement de $tab[$j] pour $prof[$i]<br />\n";
					}
				}
			}
		}
	}

	if($msg==""){
		$msg="Enregistrement réussi.";
	}

	unset($page);
}



//**************** EN-TETE *****************
$titre_page = "Configuration des interfaces simplifiées";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
echo "<div class='norme'><p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

if(!isset($prof)){
	echo "</div>\n";

	echo "<h2>Choix des professeurs</h2>\n";

	echo "<p>Choisissez les professeurs dont vous souhaitez paramétrer les interfaces simplifiées.</p>\n";
	echo "<p>Tout <a href='javascript:modif_coche(true)'>cocher</a> / <a href='javascript:modif_coche(false)'>décocher</a>.</p>";

	$sql="SELECT login,nom,prenom FROM utilisateurs WHERE (statut='professeur'AND etat='actif') ORDER BY nom,prenom";
	$res_profs=mysql_query($sql);
	$nb_prof=mysql_num_rows($res_profs);
	if($nb_prof==0){
		echo "<p>ERREUR: Il semble qu'aucun professeur ne soit encore défini.</p>\n";
		echo "</form>\n";
		echo "</body>\n";
		echo "</html>\n";
		die();
	}
	// Affichage sur 3 colonnes
	$nb_prof_par_colonne=round($nb_prof/3);

	echo "<table width='100%'>\n";
	echo "<tr valign='top' align='center'>\n";

	$i = 0;

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	while ($i < $nb_prof) {

		if(($i>0)&&(round($i/$nb_prof_par_colonne)==$i/$nb_prof_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		$lig_prof=mysql_fetch_object($res_profs);

		echo "<input type='checkbox' id='prof".$i."' name='prof[]' value='$lig_prof->login' /> ".ucfirst(strtolower($lig_prof->prenom))." ".strtoupper($lig_prof->nom)."<br />\n";

		$i++;
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<script type='text/javascript'>
		function modif_coche(statut){
			for(k=0;k<$i;k++){
				if(document.getElementById('prof'+k)){
					document.getElementById('prof'+k).checked=statut;
				}
			}
			//changement();
		}
	</script>\n";

	echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";

}
else{
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres professeurs</a>";
	echo "</div>\n";

	if(!isset($page)){
		echo "<h2>Choix de la page</h2>\n";

		if(count($prof)==0){
			echo "<p>ERREUR: Il semble qu'aucun professeur n'ait été sélectionné.</p>\n";
			echo "</form>\n";
			echo "</body>\n";
			echo "</html>\n";
			die();
		}

		echo "<p>Vous allez paramétrer des préférences pour ";
		$chaine_profs="";
		for($i=0;$i<count($prof);$i++){
			echo "<input type='hidden' name='prof[$i]' value='$prof[$i]' />\n";
			$sql="SELECT nom,prenom FROM utilisateurs WHERE login='$prof[$i]' ORDER BY nom,prenom";
			$res_prof=mysql_query($sql);
			$lig_prof=mysql_fetch_object($res_prof);
			$chaine_profs.=ucfirst(strtolower($lig_prof->prenom))." ".strtoupper($lig_prof->nom).", ";
		}
		$chaine_profs=substr($chaine_profs,0,strlen($chaine_profs)-2);
		echo $chaine_profs.".</p>\n";

		/*
		echo "<p>Vous devez maintenant choisir la page pour laquelle vous souhaitez paramétrer les items de l'interface simplifiée.</p>\n";
		echo "<p><input type='radio' name='page' value='add_modif_dev' checked /> Création d'évaluation<br />\n";
		echo "<input type='radio' name='page' value='add_modif_conteneur' /> Création de ".strtolower(getSettingValue("gepi_denom_boite"))."</p>\n";
		*/
		echo "<table border='0'>\n";
		echo "<tr><td valign='top'>Paramétrage de l'interface simplifiée pour :</td>\n";
		echo "<td>";
		echo "<input type='hidden' name='page' id='id_page' />\n";
		echo "<input type='button' name='choix1' value=\"Création d'évaluation\" onclick=\"document.getElementById('id_page').value='add_modif_dev';document.forms['formulaire'].submit();\" /> <br />\n";
		echo "<input type='button' name='choix1' value=\"Création de ".strtolower(getSettingValue("gepi_denom_boite"))."\" onclick=\"document.getElementById('id_page').value='add_modif_conteneur';document.forms['formulaire'].submit();\" />";
		echo "</td></tr>\n";
		echo "</table>\n";
	}
	else{
		$chaine_profs="";
		for($i=0;$i<count($prof);$i++){
			echo "<input type='hidden' name='prof[$i]' value='$prof[$i]' />\n";
			$sql="SELECT nom,prenom FROM utilisateurs WHERE login='$prof[$i]' ORDER BY nom,prenom";
			$res_prof=mysql_query($sql);
			$lig_prof=mysql_fetch_object($res_prof);
			$chaine_profs.=ucfirst(strtolower($lig_prof->prenom))." ".strtoupper($lig_prof->nom).", ";
		}
		$chaine_profs=substr($chaine_profs,0,strlen($chaine_profs)-2);

		echo "<input type='hidden' name='page' value='$page' />\n";

		if($page=='add_modif_dev'){
			echo "<h2>Choix des items de la page: Création d'évaluation</h2>\n";

			echo "<p>Vous allez paramétrer des préférences pour $chaine_profs.</p>\n";

			// Récupération des valeurs.
			/*
			$aff_nom_court=getPref($_SESSION['login'],'add_modif_dev_nom_court','y');
			$aff_nom_complet=getPref($_SESSION['login'],'add_modif_dev_nom_complet','n');
			$aff_description=getPref($_SESSION['login'],'add_modif_dev_description','n');
			$aff_coef=getPref($_SESSION['login'],'add_modif_dev_coef','y');
			$aff_date=getPref($_SESSION['login'],'add_modif_dev_date','y');
			$aff_boite=getPref($_SESSION['login'],'add_modif_dev_boite','y');
			*/

			echo "<p>Pour ce(s) professeur(s), utiliser l'interface simplifiée par défaut: Oui <input type='radio' name='add_modif_dev_simpl' value='y' checked /> / <input type='radio' name='add_modif_dev_simpl' value='n' /> Non</p>\n";

			echo "<p>Pour fixer les valeurs ci-dessous, validez le formulaire.</p>\n";
			echo "<table border='1'>\n";
			echo "<tr>\n";
			echo "<td style='font-weight: bold; text-align:left;'>Item</td>\n";
			echo "<td style='font-weight: bold; text-align:center;'>Afficher</td>\n";
			echo "<td style='font-weight: bold; text-align:center;'>Cacher</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Afficher le champ Nom_court:</td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_dev_nom_court' value='y' checked /></td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_dev_nom_court' value='n' /></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Afficher le champ Nom_complet:</td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_dev_nom_complet' value='y' /></td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_dev_nom_complet' value='n' checked /></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Afficher le champ Description:</td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_dev_description' value='y' /></td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_dev_description' value='n' checked /></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Afficher le champ Coefficient:</td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_dev_coef' value='y' checked /></td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_dev_coef' value='n' /></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Afficher le champ Date:</td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_dev_date' value='y' checked /></td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_dev_date' value='n' /></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			//echo "<td>Afficher le champ Emplacement du/de la ".ucfirst(strtolower(getSettingValue("gepi_denom_boite"))).":</td>\n";
			echo "<td>Afficher le champ Emplacement de l'évaluation :</td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_dev_boite' value='y' checked /></td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_dev_boite' value='n' /></td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			echo "<p>Les champs suivants ne sont pas accessibles en interface simplifiée:</p>\n";
			echo "<ul>\n";
			echo "<li>Faire apparaître la note de l'évaluation sur le relevé de notes de l'élève (<i>valeur par défaut: 'oui'</i>)</li>\n";
			echo "<li>Statut de l'évaluation (<i>valeur par défaut: 'La note de l'évaluation entre dans le calcul de la moyenne'</i>)</li>\n";
			echo "</ul>\n";
			echo "<p>Pour paramétrer autrement ces champs, le professeur doit repasser en interface complète.</p>\n";

			echo "<p><i>NOTE:</i> L'accès à ces champs en interface simplifiée pourra être ajouté dans le futur.</p>\n";


			echo "<input type='hidden' name='enregistrer' value='oui' />\n";

		}
		elseif($page=='add_modif_conteneur'){
			echo "<h2>Choix des items de la page: Création de ".strtolower(getSettingValue("gepi_denom_boite"))."</h2>\n";

			echo "<p>Vous allez paramétrer des préférences pour $chaine_profs.</p>\n";

			echo "<p>Pour ce(s) professeur(s), utiliser l'interface simplifiée par défaut: Oui <input type='radio' name='add_modif_conteneur_simpl' value='y' checked /> / <input type='radio' name='add_modif_conteneur_simpl' value='n' /> Non</p>\n";

			echo "<p>Pour fixer les valeurs ci-dessous, validez le formulaire.</p>\n";
			echo "<table border='1'>\n";
			echo "<tr>\n";
			echo "<td style='font-weight: bold; text-align:left;'>Item</td>\n";
			echo "<td style='font-weight: bold; text-align:center;'>Afficher</td>\n";
			echo "<td style='font-weight: bold; text-align:center;'>Cacher</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Afficher le champ Nom_court:</td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_nom_court' value='y' checked /></td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_nom_court' value='n' /></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Afficher le champ Nom_complet:</td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_nom_complet' value='y' /></td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_nom_complet' value='n' checked /></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Afficher le champ Description:</td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_description' value='y' /></td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_description' value='n' checked /></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Afficher le champ Coefficient:</td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_coef' value='y' checked /></td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_coef' value='n' /></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Afficher le champ Emplacement du/de la ".ucfirst(strtolower(getSettingValue("gepi_denom_boite"))).":</td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_boite' value='y' checked /></td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_boite' value='n' /></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Afficher le champ Afficher le(a) ".ucfirst(strtolower(getSettingValue("gepi_denom_boite")))." sur le relevé de notes:<br />La valeur par défaut du champ (<i>affiché ou non</i>) est 'oui'.</td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_aff_display_releve_notes' value='y' /></td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_aff_display_releve_notes' value='n' checked /></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Afficher le champ Afficher le(a) ".ucfirst(strtolower(getSettingValue("gepi_denom_boite")))." sur le bulletin:<br />La valeur par défaut du champ (<i>affiché ou non</i>) est 'non'.</td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_aff_display_bull' value='y' /></td>\n";
			echo "<td style='text-align:center;'><input type='radio' name='add_modif_conteneur_aff_display_bull' value='n' checked /></td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			echo "<p>Les champs suivants ne sont pas accessibles en interface simplifiée:</p>\n";
			echo "<ul>\n";
			echo "<li>Précision du calcul de la moyenne du/de la ".strtolower(getSettingValue("gepi_denom_boite"))." (<i>valeur par défaut: 'Arrondir au dixième de point supérieur'</i>)</li>\n";
			echo "<li>Pondération: Pour chaque élève, augmente ou diminue de la valeur indiquée ci-contre, le coefficient de la meilleur note du/de la ".strtolower(getSettingValue("gepi_denom_boite"))." (<i>valeur par défaut: '0'</i>)</li>\n";
			echo "<li>Notes prises en comptes dans le calcul de la moyenne du/de la ".strtolower(getSettingValue("gepi_denom_boite"))." (<i>valeur par défaut: 'la moyenne s'effectue sur toutes les notes contenues à la racine du conteneur et sur les moyennes des sous-conteneurs, en tenant compte des options dans ces sous-conteneurs'</i>)</li>\n";
			/*
			Notes prises en comptes dans le calcul de la moyenne de la sous-matière TN
			la moyenne s'effectue sur toutes les notes contenues à la racine de TN et sur les moyennes de la sous-matière TN2, en tenant compte des options dans cette sous-matière
			la moyenne s'effectue sur toutes les notes contenues dans TN et dans la sous-matière TN2, sans tenir compte des options définies dans cette sous-matière
			*/
			echo "</ul>\n";
			echo "<p>Pour paramétrer autrement ces champs, le professeur doit repasser en interface complète.</p>\n";

			echo "<p><i>NOTE:</i> L'accès à ces champs en interface simplifiée pourra être ajouté dans le futur.</p>\n";
		}
		echo "<input type='hidden' name='enregistrer' value='oui' />\n";
		echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";
	}
}

//echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";

echo "</form>\n";
echo "<br />\n";
require("../lib/footer.inc.php");
?>