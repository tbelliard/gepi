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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_engagements/extraction_engagements.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_engagements/extraction_engagements.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Extraction des engagements',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$engagement_ele=isset($_POST['engagement_ele']) ? $_POST['engagement_ele'] : (isset($_GET['engagement_ele']) ? $_GET['engagement_ele'] : array());
$engagement_resp=isset($_POST['engagement_resp']) ? $_POST['engagement_resp'] : (isset($_GET['engagement_resp']) ? $_GET['engagement_resp'] : array());

$tab_engagements=get_tab_engagements("");
if(count($tab_engagements['indice'])==0) {
	header("Location: ../accueil.php?msg=Aucun type d engagement n est actuellement défini.");
	die();
}

$nb_engagements=count($tab_engagements['indice']);

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

// ===================== entete Gepi ======================================//
$titre_page = "Extraction engagements";
require_once("../lib/header.inc.php");
// ===================== fin entete =======================================//

//debug_var();

echo "<div class='noprint'>";
if($_SESSION['statut']=='administrateur') {
	echo "<p class='bold'><a href='../classes/classes_const.php";
	if(isset($id_classe[0])) {
		echo "?id_classe=".$id_classe[0];
	}
	echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}
else {
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}

if(acces("/mod_engagements/index_admin.php", $_SESSION['statut'])) {
	echo " | <a href='index_admin.php'>Définir les types d'engagements</a>";
}

if(acces("/mod_engagements/saisie_engagements.php", $_SESSION['statut'])) {
	echo " | <a href='saisie_engagements.php'>Saisir les engagements</a>";
}

if(acces("/mod_engagements/imprimer_documents.php", $_SESSION['statut'])) {
	echo " | <a href='imprimer_documents.php'>Imprimer les documents liés aux engagements</a>";
}

if((!isset($id_classe))||((count($engagement_ele)==0)&&(count($engagement_resp)==0))) {
	echo "</p>
</div>\n";

	echo "<p class='bold'>Choix des classes, statuts et engagements&nbsp;:</p>\n";

	// Liste des classes avec élève:
	$sql="(SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c, engagements_user eu WHERE (c.id=jec.id_classe AND eu.valeur=jec.id_classe AND eu.id_type='id_classe' AND eu.login=jec.login) ORDER BY c.classe)";
	$sql.=" UNION (SELECT DISTINCT c.* FROM eleves e, 
								responsables2 r, 
								resp_pers rp, 
								j_eleves_classes jec, 
								classes c, 
								engagements_user eu 
							WHERE (c.id=jec.id_classe AND 
								eu.valeur=jec.id_classe AND 
								eu.id_type='id_classe' AND 
								eu.login=rp.login AND 
								rp.pers_id=r.pers_id AND 
								r.ele_id=e.ele_id AND 
								e.login=jec.login) ORDER BY c.classe);";
	//echo "$sql<br />";
	$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);

	$nb_classes=mysqli_num_rows($call_classes);
	if($nb_classes==0){
		echo "<p>Aucune classe avec engagement saisi n'a été trouvée.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	// Affichage sur 3 colonnes
	$nb_classes_par_colonne=round($nb_classes/3);
	$cpt = 0;

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>
	<fieldset class='fieldset_opacite50'>
		<p class='bold'>Engagements liés à des classes&nbsp;:</p>

		<table width='100%' summary='Choix des classes'>
			<tr valign='top' align='center'>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td align='left'>\n";

	while($lig_clas=mysqli_fetch_object($call_classes)) {

		//affichage 2 colonnes
		if(($cpt>0)&&(round($cpt/$nb_classes_par_colonne)==$cpt/$nb_classes_par_colonne)){
			echo "
				</td>
				<td align='left'>";
		}

		echo "
					<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='id_classe[]' id='tab_id_classe_$cpt' value='$lig_clas->id' onchange='change_style_classe($cpt)' /> $lig_clas->classe</label><br />";
		$cpt++;
	}

	echo "
				</td>
			</tr>
		</table>

		<p><a href='#' onClick='ModifCase(true);return false;'>Tout cocher</a> / <a href='#' onClick='ModifCase(false);return false;'>Tout décocher</a></p>

		<table>
			<tr>
				<!--td style='vertical-align:top'>
					<input type='checkbox' name='engagement_statut[]' id='engagement_statut_eleve' value='eleve' checked onchange=\"checkbox_change('engagement_statut_responsable');checkbox_change('engagement_statut_eleve')\" />
				</td-->
				<td style='vertical-align:top'>
					<label for='engagement_statut_eleve' id='texte_engagement_statut_eleve'>Extraire des engagements élèves</label>
				</td>
				<td style='vertical-align:top'>";
	$cpt=0;
	foreach($tab_engagements['indice'] as $key => $current_engagement) {
		if($current_engagement['ConcerneEleve']) {
			echo "<input type='checkbox' name='engagement_ele[]' id='engagement_ele_$cpt' value='".$current_engagement['id']."' onchange=\"checkbox_change('engagement_ele_$cpt')\" /><label for='engagement_ele_$cpt' id='texte_engagement_ele_$cpt' title=\"".$current_engagement['nom']." (".$current_engagement['description'].") ".$current_engagement['effectif']." engagement(s) saisi(s).\"> ".$current_engagement['nom']."</label><br />";
			$cpt++;
		}
	}
	echo "
				</td>
			</tr>
			<tr>
				<!--td style='vertical-align:top'>
					<input type='checkbox' name='engagement_statut[]' id='engagement_statut_responsable' value='responsable' onchange=\"checkbox_change('engagement_statut_responsable');checkbox_change('engagement_statut_eleve')\" />
				</td-->
				<td style='vertical-align:top'>
					<label for='engagement_statut_responsable' id='texte_engagement_statut_responsable'>Extraire des engagements responsables</label>
				</td>
				<td style='vertical-align:top'>";
	$cpt=0;
	foreach($tab_engagements['indice'] as $key => $current_engagement) {
		if($current_engagement['ConcerneResponsable']) {
			echo "<input type='checkbox' name='engagement_resp[]' id='engagement_resp_$cpt' value='".$current_engagement['id']."' onchange=\"checkbox_change('engagement_resp_$cpt')\" /><label for='engagement_resp_$cpt' id='texte_engagement_resp_$cpt' title=\"".$current_engagement['nom']." (".$current_engagement['description'].") ".$current_engagement['effectif']." engagement(s) saisi(s).\"> ".$current_engagement['nom']."</label><br />";
			$cpt++;
		}
	}
	echo "
				</td>
			</tr>
		</table>

		<p><input type='submit' value='Valider' /></p>

		<p style='text-indent:-4em; margin-left:4em; margin-top:1em;'><em>NOTE&nbsp;:</em> Seules apparaissent les classes dans lesquelles des engagements sont saisis.</p>
	</fieldset>
</form>

<p><br /></p>

<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$nb_classes;k++) {
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

	".js_checkbox_change_style('checkbox_change', 'texte_', "n", 0.5)."
</script>\n";

/*
		<pre>";
	print_r($tab_engagements);
	echo "
		</pre>
*/

	require("../lib/footer.inc.php");
	die();
}

echo " | <a href='".$_SERVER['PHP_SELF']."'>Extraire les engagements pour d'autres classes</a></p>
</div>\n";

//debug_var();

// Afficher les personnes extraites
// Pouvoir générer un CSV...
// Pouvoir envoyer un mail...

echo "<table class='boireaus boireaus_alt sortable resizable'>
	<tr>
		<th class='text'>Nom</th>
		<th class='text'>Prénom</th>
		<th class='text'>Statut</th>
		<th class='text'>Classe</th>
		<th class='text'>Engagements</th>
	</tr>";
for($loop=0;$loop<count($id_classe);$loop++) {
	$tab=get_tab_engagements_user("", $id_classe[$loop]);
	$nom_classe=get_nom_classe($id_classe[$loop]);

	foreach($tab['login_user'] as $current_login => $tab_engagement_current_user) {
		$tab_user=get_info_user($current_login);

		$chaine_tr="
	<tr>
		<td>".$tab_user['nom'];
				/*
				echo "<pre>";
				echo print_r($tab_user);
				echo "</pre>";
				*/
		$chaine_tr.="</td>
		<td>".$tab_user['prenom']."</td>
		<td>".$tab_user['statut']."</td>
		<td>".$nom_classe."</td>
		<td>";

		$temoin_engagement_recherche="n";
		for($loop2=0;$loop2<count($tab_engagement_current_user);$loop2++) {
			if((($tab_user['statut']=="eleve")&&(in_array($tab['indice'][$tab_engagement_current_user[$loop2]]['id_engagement'], $engagement_ele)))||
			(($tab_user['statut']=="responsable")&&(in_array($tab['indice'][$tab_engagement_current_user[$loop2]]['id_engagement'], $engagement_resp)))) {
				/*
				echo "
				<pre>";
				print_r($tab);
				echo "
				</pre>";
				*/
				$chaine_tr.=$tab['indice'][$tab_engagement_current_user[$loop2]]['nom_engagement']."<br />";

				$temoin_engagement_recherche="y";
			}
		}

		$chaine_tr.="
		</td>
	</tr>";

		if($temoin_engagement_recherche=="y") {
			echo $chaine_tr;
		}

	}

}
echo "
</table>";
/*
echo "<pre>";
print_r($tab_engagements);
echo "</pre>";
*/
require("../lib/footer.inc.php");
?>
