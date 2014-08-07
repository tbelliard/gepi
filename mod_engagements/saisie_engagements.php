<?php
/*
 *
 *
 * Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_engagements/saisie_engagements.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_engagements/saisie_engagements.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Saisie des engagements',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

// A FAIRE : Pouvoir gérer les engagements parents

$tab_tous_engagements=get_tab_engagements("eleve");
if($_SESSION['statut']=='administrateur') {
	$tab_engagements=$tab_tous_engagements;
}
elseif($_SESSION['statut']=='cpe') {
	$tab_engagements=get_tab_engagements("eleve", "cpe");
}
elseif($_SESSION['statut']=='scolarite') {
	$tab_engagements=get_tab_engagements("eleve", "scolarite");
}

if(count($tab_tous_engagements['indice'])==0) {
	header("Location: ../accueil.php?msg=Aucun type d engagement n est actuellement défini.");
	die();
}

$nb_tous_engagements=count($tab_tous_engagements['indice']);
$nb_engagements=count($tab_engagements['indice']);

//debug_var();

if((isset($id_classe))&&(isset($_POST['is_posted']))) {
	check_token();

	$msg="";
	/*
	$_POST['id_classe']=	Array (*)
	$_POST[id_classe]['0']=	33
	$_POST['engagement']=	Array (*)
	$_POST[engagement]['0']=	33|beaaaa|1
	$_POST[engagement]['1']=	33|beraaaa|3
	$_POST[engagement]['2']=	33|daaaaa|1
	$_POST[engagement]['3']=	33|gosaaa|3
	*/

	$nb_inscriptions=0;
	$tab_engagements_classe=array();
	$engagement=isset($_POST['engagement']) ? $_POST['engagement'] : array();
	for($loop=0;$loop<count($engagement);$loop++) {
		$tab=explode("|", $engagement[$loop]);
		$current_id_classe=$tab[0];
		$current_login=$tab[1];
		$current_id_engagement=$tab[2];

		if(!array_key_exists($current_id_classe, $tab_engagements_classe)) {
			$tab_engagements_classe[$current_id_classe]=get_tab_engagements_user("", $current_id_classe);
		}
		if(array_key_exists($current_id_engagement, $tab_engagements['id_engagement'])) {
			// L'utilisateur a accès à la saisie de ce type d'engagement
			if((!isset($tab_engagements_classe[$current_id_classe]['id_engagement_user'][$current_id_engagement]))||(!in_array($current_login, $tab_engagements_classe[$current_id_classe]['id_engagement_user'][$current_id_engagement]))) {
/*
echo "$current_login n'est pas dans \$tab_engagements_classe[$current_id_classe]['id_engagement_user'][$current_id_engagement]<pre>";
print_r($tab_engagements_classe[$current_id_classe]['id_engagement_user'][$current_id_engagement]);
echo "</pre>";
*/
				$sql="INSERT INTO engagements_user SET login='$current_login', id_type='id_classe', valeur='$current_id_classe', id_engagement='$current_id_engagement';";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'inscription de l'engagement n°$current_id_engagement en classe ".get_nom_classe($current_id_classe)." pour ".civ_nom_prenom($current_login)."<br />";
				}
				else {
					$nb_inscriptions++;
				}
			}

		}
	}
	$msg.=$nb_inscriptions." inscription(s) d'engagements.<br />";

	// Désinscriptions
	$nb_desinscriptions=0;
	for($loop=0;$loop<count($id_classe);$loop++) {
		if(!array_key_exists($id_classe[$loop], $tab_engagements_classe)) {
			$tab_engagements_classe[$id_classe[$loop]]=get_tab_engagements_user("", $id_classe[$loop]);
		}

		foreach($tab_engagements_classe[$id_classe[$loop]]['id_engagement_user'] as $current_id_engagement => $current_login) {
/*
echo "<pre>";
print_r($current_login);
echo "</pre>";
*/
			for($loop2=0;$loop2<count($current_login);$loop2++) {
				$chaine=$id_classe[$loop]."|".$current_login[$loop2]."|".$current_id_engagement;
				//echo "$chaine<br />";
				if(!in_array($chaine, $engagement)) {
					$sql="DELETE FROM engagements_user WHERE login='".$current_login[$loop2]."' AND id_type='id_classe' AND valeur='".$id_classe[$loop]."' AND id_engagement='$current_id_engagement';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						$msg.="Erreur lors de la suppression de l'engagement n°$current_id_engagement en classe ".get_nom_classe($id_classe[$loop])." pour ".civ_nom_prenom($current_login[$loop2])."<br />";
					}
					else {
						$nb_desinscriptions++;
					}
				}
			}
		}
	}

	$msg.=$nb_desinscriptions." suppression(s) d'engagements.<br />";
}

// ======================== CSS et js particuliers ========================
//$utilisation_win = "non";
//$utilisation_jsdivdrag = "non";
//$javascript_specifique = ".js";
//$style_specifique = ".css";

// ===================== entete Gepi ======================================//
$titre_page = "Saisie engagements";
require_once("../lib/header.inc.php");
// ===================== fin entete =======================================//

//debug_var();

//echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "<p class='bold'><a href='../classes/classes_const.php";
if(isset($id_classe[0])) {
	echo "?id_classe=".$id_classe[0];
}
echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if(!isset($id_classe)) {
	echo "</p>\n";

	echo "<p class='bold'>Choix des classes&nbsp;:</p>\n";

	// Liste des classes avec élève:
	$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) ORDER BY c.classe;";
	$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);

	$nb_classes=mysqli_num_rows($call_classes);
	if($nb_classes==0){
		echo "<p>Aucune classe avec élève affecté n'a été trouvée.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	// Affichage sur 3 colonnes
	$nb_classes_par_colonne=round($nb_classes/3);

	echo "<table width='100%' summary='Choix des classes'>\n";
	echo "<tr valign='top' align='center'>\n";

	$cpt = 0;

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	while($lig_clas=mysqli_fetch_object($call_classes)) {

		//affichage 2 colonnes
		if(($cpt>0)&&(round($cpt/$nb_classes_par_colonne)==$cpt/$nb_classes_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='id_classe[]' id='tab_id_classe_$cpt' value='$lig_clas->id' onchange='change_style_classe($cpt)' /> $lig_clas->classe</label>";
		echo "<br />\n";
		$cpt++;
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>

<p style='text-indent:-4em;margin-left:4em;'><em>NOTE&nbsp;:</em> Cette page est destinée saisir les engagements élèves pour telle ou telle classe (<em>délégué de classe,...</em>).</p>

<p style='color:red'>A FAIRE : Pouvoir saisir les engagements de parents (délégués de parents,...), et prendre en compte pour la génération de convocations aux conseils de classe,... avec modèle OOo...</p>

<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('tab_id_classe_'+k)){
				document.getElementById('tab_id_classe_'+k).checked = mode;
				change_style_classe(k);
			}
		}
	}

	function change_style_classe(num) {
		if(document.getElementById('tab_id_classe_'+num)) {
			if(document.getElementById('tab_id_classe_'+num).checked) {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";
	require("../lib/footer.inc.php");
	die();
}

echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a></p>\n";

echo "<p class='bold'>Choix des élèves&nbsp;:</p>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
echo "<input type='hidden' name='is_posted' value='1' />\n";
echo add_token_field();

$cpt=0;
for($i=0;$i<count($id_classe);$i++) {
	$sql="SELECT DISTINCT e.login, e.nom, e.prenom, e.sexe, e.naissance FROM eleves e, j_eleves_classes jec WHERE (e.login=jec.login AND jec.id_classe='".$id_classe[$i]."') ORDER BY e.nom, e.prenom;";
	//echo "$sql<br />";
	$call_eleves=mysqli_query($GLOBALS["mysqli"], $sql);
	$nombre_ligne=mysqli_num_rows($call_eleves);
	if($nombre_ligne==0) {
		echo "<p style='color:red;'>Aucun élève n'est inscrit dans la classe de ".get_class_from_id($id_classe[$i]).".</p>\n";
	}
	else {
		$tab_engagements_classe=get_tab_engagements_user("", $id_classe[$i]);

		echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";

		$first_ele[$id_classe[$i]]=$cpt;
		echo "<table class='boireaus' summary='Classe n°$id_classe[$i]'/>\n";
		echo "<tr>\n";
		echo "<th>\n";
		echo "Classe de ".get_class_from_id($id_classe[$i])."\n";
		echo "</th>\n";
		echo "<th colspan='$nb_tous_engagements'>Engagements</th>\n";
		echo "</tr>\n";

		echo "<tr>\n";

		/*
		echo "<th>\n";
		//echo "Cocher/décocher\n";
		echo "<p><a href='#' onClick='ModifCase(".$id_classe[$i].",true);return false;'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href='#' onClick='ModifCase(".$id_classe[$i].",false);return false;'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a></p>\n";
		echo "</th>\n";
		*/

		echo "<th>Elève</th>\n";
		for($loop=0;$loop<$nb_tous_engagements;$loop++) {
			echo "<th>".$tab_tous_engagements['indice'][$loop]['nom']."</th>\n";
		}
		echo "</tr>\n";

		$alt=1;
		while($lig_ele=mysqli_fetch_object($call_eleves)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
			/*
			echo "<td>\n";
			echo "<input type='checkbox' name='login_eleve_".$id_classe[$i]."[]' id='login_eleve_$cpt' value='$lig_ele->login' onchange='change_style_eleve($cpt)' checked />\n";
			echo "</td>\n";
			*/

			echo "<td style='text-align:left;'><span id='eleve_$cpt'>$lig_ele->nom $lig_ele->prenom</span></td>\n";

			for($loop=0;$loop<$nb_tous_engagements;$loop++) {
				echo "<td>\n";
				if(($_SESSION['statut']=='administrateur')||
				(($_SESSION['statut']=='cpe')&&($tab_engagements['indice'][$loop]['SaisieCpe']=='yes'))||
				(($_SESSION['statut']=='scolarite')&&($tab_engagements['indice'][$loop]['SaisieScol']=='yes'))
				) {
					$checked="";
					if((isset($tab_engagements_classe['id_engagement_user'][$tab_engagements['indice'][$loop]['id']]))&&(in_array($lig_ele->login, $tab_engagements_classe['id_engagement_user'][$tab_engagements['indice'][$loop]['id']]))) {
						$checked=" checked";
					}
					echo "<input type='checkbox' name='engagement[]' id='engagement_".$loop."_".$cpt."' value=\"".$id_classe[$i]."|".$lig_ele->login."|".$tab_engagements['indice'][$loop]['id']."\"$checked />";
				}
				else {
					if((isset($tab_engagements_classe['id_engagement_user'][$tab_engagements['indice'][$loop]['id']]))&&(in_array($lig_ele->login, $tab_engagements_classe['id_engagement_user'][$tab_engagements['indice'][$loop]['id']]))) {
						echo "<img src='../images/enabled.png' class='icone20' />";
					}
				}
				echo "</td>\n";
			}

			echo "</tr>\n";
			$cpt++;
		}
		echo "</table>\n";
		$last_ele[$id_classe[$i]]=$cpt;

	}
}

echo "<p><input type='submit' value='Valider' /></p>\n";
echo "</form>\n";

require_once("../lib/footer.inc.php");
?>
