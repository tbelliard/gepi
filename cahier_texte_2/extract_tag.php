<?php
/*
 *
 * Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Stephane Boireau
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Initialisations files
//require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

function aff_debug($tableau){
	echo '<pre>';
	print_r($tableau);
	echo '</pre>';
}

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/extract_tag.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/cahier_texte_2/extract_tag.php',
	administrateur='V',
	professeur='V',
	cpe='V',
	scolarite='V',
	eleve='F',
	responsable='F',
	secours='F',
	autre='V',
	description='Cahiers de textes: Extraction tags',
	statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (!acces_cdt()) {
	tentative_intrusion(1, "Tentative d'accès au cahier de textes en consultation alors que le module n'est pas activé.");
	die("Le module n'est pas activé.");
}

//=======================
// Pour éviter de refaire le choix des dates en revenant ici, on utilise la SESSION...
$annee = strftime("%Y");
$mois = strftime("%m");
$jour = strftime("%d");
$heure = strftime("%H");
$minute = strftime("%M");

if($mois>8) {$date_debut_tmp="01/09/$annee";} else {$date_debut_tmp="01/09/".($annee-1);}

$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : (isset($_SESSION['display_date_debut']) ? $_SESSION['display_date_debut'] : $date_debut_tmp);

$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : (isset($_SESSION['display_date_fin']) ? $_SESSION['display_date_fin'] : $jour."/".$mois."/".$annee);
//=======================

$mode=isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : NULL);
$type_notice=isset($_POST["type_notice"]) ? $_POST["type_notice"] : array();
$tag=isset($_POST["tag"]) ? $_POST["tag"] : array();
$id_groupe=isset($_POST["id_groupe"]) ? $_POST["id_groupe"] : array();

$id_classe=isset($_POST["id_classe"]) ? $_POST["id_classe"] : array();
$login_prof=isset($_POST["login_prof"]) ? $_POST["login_prof"] : array();

$tab_tag_type=get_tab_tag_cdt();

if((isset($mode))&&($mode=="extraire")) {
	$tab_notices=array();

	$tab_mes_groupes=array();
	if($_SESSION['statut']=="professeur") {
		$sql="SELECT DISTINCT id_groupe FROM j_groupes_professeurs WHERE login='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				$tab_mes_groupes[]=$lig->id_groupe;
			}
		}
	}

	if(count($type_notice)==0) {
		if($_SESSION['statut']=="professeur") {
			$type_notice=array("c", "t", "p");
		}
		else {
			$type_notice=array("c", "t");
		}
	}

	if(preg_match("#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#", $display_date_debut)) {
		$tmp_tab=explode("/", $display_date_debut);
		$jour_debut=$tmp_tab[0];
		$mois_debut=$tmp_tab[1];
		$annee_debut=$tmp_tab[2];

		$ts_debut=mktime(0,0,0,$mois_debut,$jour_debut,$annee_debut);
		if(!$ts_debut) {
			$ts_debut=getSettingValue("begin_bookings");
		}
	}

	if(preg_match("#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#", $display_date_fin)) {
		$tmp_tab=explode("/", $display_date_fin);
		$jour_fin=$tmp_tab[0];
		$mois_fin=$tmp_tab[1];
		$annee_fin=$tmp_tab[2];

		$ts_fin=mktime(0,0,0,$mois_fin,$jour_fin,$annee_fin);
		if(!$ts_fin) {
			$ts_fin=getSettingValue("end_bookings");
		}
	}

	$sql_id_classe="";
	if(count($id_classe)>0) {
		$cpt_clas=0;
		$sql_id_classe=" AND (id_groupe IN (SELECT DISTINCT id_groupe FROM j_groupes_classes WHERE ";
		for($loop=0;$loop<count($id_classe);$loop++) {
			if($cpt_clas>0) {
				$sql_id_classe.=" OR ";
			}
			$sql_id_classe.="id_classe='".$id_classe[$loop]."'";
			$cpt_clas++;
		}
		$sql_id_classe.="))";
	}

	$sql_login_prof="";
	if(count($login_prof)>0) {
		$cpt_prof=0;
		$sql_login_prof=" AND (";
		for($loop=0;$loop<count($login_prof);$loop++) {
			if($cpt_prof>0) {
				$sql_login_prof.=" OR ";
			}
			$sql_login_prof.="id_login='".$login_prof[$loop]."'";
			$cpt_prof++;
		}
		$sql_login_prof.=")";
	}

	$sql_id_groupe="";
	if(count($id_groupe)>0) {
		$cpt_grp=0;
		$sql_id_groupe=" AND (";
		for($loop=0;$loop<count($id_groupe);$loop++) {
			// A FAIRE : Ajouter un test sur les groupes du prof
			if($cpt_grp>0) {
				$sql_id_groupe.=" OR ";
			}
			$sql_id_groupe.="id_groupe='".$id_groupe[$loop]."'";
			$cpt_grp++;
		}
		$sql_id_groupe.=")";
	}

	$sql_tag="";
	if(count($tag)>0) {
		$sql_tag=" AND (";
		for($loop=0;$loop<count($tag);$loop++) {
			if($loop>0) {
				$sql_tag.=" OR ";
			}
			$sql_tag.="ct.id_tag='".$tag[$loop]."'";
		}
		$sql_tag.=")";
	}

	if(in_array("c", $type_notice)) {
		$cpt=0;
		$sql="SELECT DISTINCT ce.* FROM ct_entry ce, ct_tag ct WHERE ce.id_ct=ct.id_ct AND date_ct>='".$ts_debut."' AND date_ct<='".$ts_fin."'".$sql_id_groupe.$sql_tag.$sql_login_prof.$sql_id_classe." AND ct.type_ct='c' ORDER BY date_ct;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_array($res)) {
				if(($_SESSION['statut']!="professeur")||(in_array($lig['id_groupe'], $tab_mes_groupes))) {
					$tab_notices["c"][$cpt]=$lig;

					$sql="SELECT DISTINCT ct.id_tag FROM ct_tag ct WHERE ct.id_ct='".$lig['id_ct']."' AND ct.type_ct='c';";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					while($lig2=mysqli_fetch_object($res2)) {
						$tab_notices["c"][$cpt]["id_tag"][]=$lig2->id_tag;
					}

					$cpt++;
				}
			}
		}
	}

	if(in_array("t", $type_notice)) {
		$cpt=0;
		$sql="SELECT DISTINCT ce.* FROM ct_devoirs_entry ce, ct_tag ct WHERE ce.id_ct=ct.id_ct AND date_ct>='".$ts_debut."' AND date_ct<='".$ts_fin."'".$sql_id_groupe.$sql_tag.$sql_login_prof.$sql_id_classe." AND ct.type_ct='t' ORDER BY date_ct;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_array($res)) {
				if(($_SESSION['statut']!="professeur")||(in_array($lig['id_groupe'], $tab_mes_groupes))) {
					$tab_notices["t"][$cpt]=$lig;

					$sql="SELECT DISTINCT ct.id_tag FROM ct_tag ct WHERE ct.id_ct='".$lig['id_ct']."' AND ct.type_ct='t';";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					while($lig2=mysqli_fetch_object($res2)) {
						$tab_notices["t"][$cpt]["id_tag"][]=$lig2->id_tag;
					}

					$cpt++;
				}
			}
		}
	}

	if(($_SESSION['statut']=="professeur")&&(in_array("p", $type_notice))) {
		$cpt=0;
		$sql="SELECT DISTINCT ce.* FROM ct_private_entry ce, ct_tag ct WHERE ce.id_ct=ct.id_ct AND date_ct>='".$ts_debut."' AND date_ct<='".$ts_fin."'".$sql_id_groupe.$sql_tag.$sql_login_prof.$sql_id_classe." AND ct.type_ct='p' ORDER BY date_ct;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_array($res)) {
				if(($_SESSION['statut']!="professeur")||(in_array($lig['id_groupe'], $tab_mes_groupes))) {
					$tab_notices["p"][$cpt]=$lig;

					$sql="SELECT DISTINCT ct.id_tag FROM ct_tag ct WHERE ct.id_ct='".$lig['id_ct']."' AND ct.type_ct='p';";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					while($lig2=mysqli_fetch_object($res2)) {
						$tab_notices["p"][$cpt]["id_tag"][]=$lig2->id_tag;
					}

					$cpt++;
				}
			}
		}
	}
}

if((isset($_GET['export']))&&($_GET['export']=="csv")) {

	if(count($tab_notices)==0) {
		$msg="Aucune notice n'a été trouvée avec les critères choisis.<br />";
	}
	else {

		$csv="Dates_Ymd;Dates;Enseignements;Classes;Enseignants;Type de notice;Tags;\n";

		$cpt=0;
		$tab_grp=array();
		$tab_prof=array();
		$tab_classe=array();
		foreach($tab_notices as $type_notice => $tab) {
			// Style selon type_notice
			for($loop=0;$loop<count($tab);$loop++) {
				if(!isset($tab_grp[$tab[$loop]["id_groupe"]])) {
					$tab_grp[$tab[$loop]["id_groupe"]]["designation"]=remplace_accents(html_entity_decode(get_info_grp($tab[$loop]["id_groupe"], array('description', 'matieres'),"")));
				}
				if(!isset($tab_prof[$tab[$loop]["id_login"]])) {
					$tab_prof[$tab[$loop]["id_login"]]=remplace_accents(html_entity_decode(civ_nom_prenom($tab[$loop]["id_login"])));
				}
				if(!isset($tab_classe[$tab[$loop]["id_groupe"]])) {
					$tab_classe[$tab[$loop]["id_groupe"]]="";
					$sql="SELECT DISTINCT classe FROM classes c, j_groupes_classes jgc WHERE jgc.id_classe=c.id AND jgc.id_groupe='".$tab[$loop]["id_groupe"]."' ORDER BY classe;";
					//echo "$sql<br />";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)>0) {
						while($lig=mysqli_fetch_object($res)) {
							if($tab_classe[$tab[$loop]["id_groupe"]]!="") {
								$tab_classe[$tab[$loop]["id_groupe"]].=", ";
							}
							$tab_classe[$tab[$loop]["id_groupe"]].=remplace_accents(html_entity_decode($lig->classe));
						}
					}
				}
				$chaine_tags="";
				for($loop2=0;$loop2<count($tab[$loop]["id_tag"]);$loop2++) {
					if($loop2>0) {
						$chaine_tags.=", ";
					}
					$chaine_tags.=remplace_accents(html_entity_decode($tab_tag_type["id"][$tab[$loop]["id_tag"][$loop2]]["nom_tag"]));
				}
				$csv.=strftime("%Y%m%d", $tab[$loop]["date_ct"]).";";
				$csv.=strftime("%a %d/%m/%Y", $tab[$loop]["date_ct"]).";";
				$csv.=$tab_grp[$tab[$loop]["id_groupe"]]["designation"].";";
				$csv.=$tab_classe[$tab[$loop]["id_groupe"]].";";
				$csv.=$tab_prof[$tab[$loop]["id_login"]].";";
				if($type_notice=="t") {
					$csv.="travail/devoir à la maison;";
				}
				elseif($type_notice=="c") {
					$csv.="compte-rendu de séance;";
				}
				else {
					$csv.="notice privée;";
				}
				$csv.=$chaine_tags.";\n";
				$cpt++;
			}
		}

		$nom_fic="extract_tags_CDT_".strftime("%Y%m%d_%H%M%S").".csv";
		send_file_download_headers('text/x-csv',$nom_fic);
		echo echo_csv_encoded($csv);

		die();
	}
}

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

//**************** EN-TETE *****************
$titre_page = "CDT extraction tags";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

//=============================================================
echo "<p class='bold'>
	<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/>Retour à l'accueil</a>
	 | <a href=\"".$_SERVER['PHP_SELF']."\">Effectuer une autre extraction</a>";
if(acces("/cahier_texte_2/see_all.php", $_SESSION['statut'])) {
	echo "
	 | <a href=\"see_all.php\">Consultation CDT</a>";
}
echo "
</p>\n";
//=============================================================

if(count($tab_tag_type)==0) {
	echo "<p>Aucun tag n'est défini.</p>";
	require("../lib/footer.inc.php");
	die();
}

if(!isset($mode)) {
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>
	<fieldset class='fieldset_opacite50'>
		<input type='hidden' name='mode' value='extraire' />
		<p class='bold'>Extraire les notices de CDT&nbsp;:</p>";

		// Checkbox des tag
		echo "
	<p style='text-indent:-3em; margin-left:3em;'>Avec les tags suivants&nbsp;:<br />";
		for($loop=0;$loop<count($tab_tag_type["indice"]);$loop++) {
			echo " 
	<input type='checkbox' name='tag[]' id='tag_".$loop."' value='".$tab_tag_type["indice"][$loop]["id"]."' onchange=\"checkbox_change('tag_".$loop."')\" /><label for='tag_".$loop."' id='texte_tag_".$loop."'>".$tab_tag_type["indice"][$loop]["nom_tag"]."</label><br />";
		}

	// Checkbox des types de notices
	echo "<p style='text-indent:-3em; margin-left:3em;'>Se limiter aux notices du type suivant&nbsp;:<br />
	<input type='checkbox' name='type_notice[]' id='type_notice_c' value='c' onchange=\"checkbox_change('type_notice_c')\" /><label for='type_notice_c' id='texte_type_notice_c'> compte-rendus de séance</label><br />
	<input type='checkbox' name='type_notice[]' id='type_notice_t' value='t' onchange=\"checkbox_change('type_notice_t')\" /><label for='type_notice_t' id='texte_type_notice_t'> devoirs/travaux à la maison</label><br />";

	if($_SESSION['statut']=='professeur') {
		echo "
	<input type='checkbox' name='type_notice[]' id='type_notice_p' value='p' onchange=\"checkbox_change('type_notice_p')\" /><label for='type_notice_p' id='texte_type_notice_p'> notices privées</label><br />";

		// Checkbox des groupes
		$groups=get_groups_for_prof($_SESSION['login']);
		if(count($groups)>0) {
			echo "
	<p style='text-indent:-3em; margin-left:3em;'>Se limiter aux notices des enseignements suivants&nbsp;:<br />";
			for($loop=0;$loop<count($groups);$loop++) {
				echo "
	<input type='checkbox' name='id_groupe[]' id='id_groupe_".$loop."' value='".$groups[$loop]["id"]."' /><label for='id_groupe_".$loop."'> ".$groups[$loop]["name"]." (".$groups[$loop]["description"].") en ".$groups[$loop]["classlist_string"]."</label><br />";
			}
		}

	}
	elseif(($_SESSION['statut']=='cpe')||($_SESSION['statut']=='scolarite')) {
		// A FAIRE : En plusieurs étapes, parce qu'on ne va pas proposer de choisir parmi tous les groupes de l'établissement.

		// Checkbox des classes
		echo "<p>Se limiter aux classes suivantes&nbsp;:</p><div style='margin-left:3em;'>";
		echo liste_checkbox_classes(array(), 'id_classe', 'cocher_decocher_classes');
		echo "<p><a href=\"javascript:cocher_decocher_classes(true)\">Cocher</a> / <a href=\"javascript:cocher_decocher_classes(false)\">décocher</a> toutes les classes.</p>";
		echo "</div>";

		// Checkbox des profs
		echo "<p>Se limiter aux professeurs suivants&nbsp;:</p><div style='margin-left:3em;'>";
		echo liste_checkbox_utilisateurs(array("professeur"), array(), $nom_champ='login_prof', 'cocher_decocher_profs', "n");
		echo "<p><a href=\"javascript:cocher_decocher_profs(true)\">Cocher</a> / <a href=\"javascript:cocher_decocher_profs(false)\">décocher</a> tous les professeurs.</p>";
		echo "</div>";

	}

	// Dates début/fin
	$date_end_bookings=strftime("%d/%m/%Y", getSettingValue('end_bookings'));

	echo "
	<p>Extraire les notices entre le&nbsp;
		<input type='text' name = 'display_date_debut' id = 'display_date_debut' size='10' value = \"".$display_date_debut."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />".img_calendrier_js("display_date_debut", "img_bouton_display_date_debut")."
		&nbsp;et le 
		<input type='text' name = 'display_date_fin' id = 'display_date_fin' size='10' value = \"".$display_date_fin."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />".img_calendrier_js("display_date_fin", "img_bouton_display_date_fin")."
		 <a href=\"#\" onclick=\"document.getElementById('display_date_fin').value='".$date_end_bookings."';return false;\"><img src='../images/icons/wizard.png' width='16' height='16' alt=\"Prendre la date de fin d'année scolaire : ".getSettingValue('end_bookings')."\" title=\"Prendre la date de fin d'année scolaire : ".getSettingValue('end_bookings')."\" /></a><br />
		 (<i>Veillez à respecter le format jj/mm/aaaa</i>)
	</p>
	<p><input type='submit' value=\"Extraire\" /></p>
	</fieldset>
</form>\n";

	echo js_checkbox_change_style("checkbox_change", 'texte_', "y");

}
else {
	/*
	echo "<pre>";
	print_r($tab_notices);
	echo "</pre>";
	*/

	if(count($tab_notices)==0) {
		echo "<p>Aucune notice n'a été trouvée avec les critères choisis.</p>";
	}
	else {

		echo "<div style='float:right; width:16px;'><a href='".$_SERVER['PHP_SELF']."?export=csv&mode=extraire' target='_blank' title=\"Exporter en CSV les dates, enseignements, tags...\"><img src='../images/icons/csv.png' class='icone16' alt='CSV'/></a></div>
<table class='boireaus boireaus_alt sortable resizable'>
	<thead>
		<tr>
			<th>Date</th>
			<th>Enseignement</th>
			<th>Classe</th>
			<th>Prof</th>
			<th>Notice</th>
			<th>Tags</th>
		</tr>
	</thead>
	<tbody>";
		$cpt=0;
		$tab_grp=array();
		$tab_prof=array();
		$tab_classe=array();
		foreach($tab_notices as $type_notice => $tab) {
			// Style selon type_notice
			for($loop=0;$loop<count($tab);$loop++) {
				if(!isset($tab_grp[$tab[$loop]["id_groupe"]])) {
					$tab_grp[$tab[$loop]["id_groupe"]]["designation"]=get_info_grp($tab[$loop]["id_groupe"], array('description', 'matieres'));
				}
				if(!isset($tab_prof[$tab[$loop]["id_login"]])) {
					$tab_prof[$tab[$loop]["id_login"]]=civ_nom_prenom($tab[$loop]["id_login"]);
				}
				if(!isset($tab_classe[$tab[$loop]["id_groupe"]])) {
					$tab_classe[$tab[$loop]["id_groupe"]]="";
					$sql="SELECT DISTINCT classe FROM classes c, j_groupes_classes jgc WHERE jgc.id_classe=c.id AND jgc.id_groupe='".$tab[$loop]["id_groupe"]."' ORDER BY classe;";
					//echo "$sql<br />";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)>0) {
						while($lig=mysqli_fetch_object($res)) {
							if($tab_classe[$tab[$loop]["id_groupe"]]!="") {
								$tab_classe[$tab[$loop]["id_groupe"]].=", ";
							}
							$tab_classe[$tab[$loop]["id_groupe"]].=$lig->classe;
						}
					}
				}
				$chaine_tags="";
				for($loop2=0;$loop2<count($tab[$loop]["id_tag"]);$loop2++) {
					if($loop2>0) {
						$chaine_tags.=", ";
					}
					$chaine_tags.=$tab_tag_type["id"][$tab[$loop]["id_tag"][$loop2]]["nom_tag"];
				}
				echo "
		<tr>
			<td>".strftime("%a %d/%m/%Y", $tab[$loop]["date_ct"])."</td>
			<td>".$tab_grp[$tab[$loop]["id_groupe"]]["designation"]."</td>
			<td>".$tab_classe[$tab[$loop]["id_groupe"]]."</td>
			<td>".$tab_prof[$tab[$loop]["id_login"]]."</td>
			<td class='color_fond_notices_".$type_notice."'>".$tab[$loop]["contenu"]."</td>
			<td>".$chaine_tags."</td>
		</tr>";
				$cpt++;
			}
		}
		echo "
	</tbody>
</table>
<p>$cpt notices extraites.</p>";
	}

}


require("../lib/footer.inc.php");
die();
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


//=============================================================
// Mode d'affichage:
// On force le mode par défaut pour les élèves et responsables
if($_SESSION['statut']=='eleve') {
	$mode='eleve';
	$login_eleve=$_SESSION['login'];
}
elseif($_SESSION['statut']=='responsable') {
	$mode='eleve';

	// On récupère la liste des élèves associés au responsable:
	if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
		$tab_eleve=get_enfants_from_resp_login($_SESSION['login'], "simple", "yy");
	}
	else {
		$tab_eleve=get_enfants_from_resp_login($_SESSION['login']);
	}
	if(count($tab_eleve)==0) {
		echo "<p>Vous n'avez aucun élève en responsabilité&nbsp;???</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	/*
	echo "<pre>";
	echo print_r($tab_eleve);
	echo "</pre>";
	*/
	for($i=0;$i<count($tab_eleve);$i+=2) {
		$tab_eleve_login[]=$tab_eleve[$i];
	}

	// On contrôle que l'élève choisi est bien associé au responsable:
	if((isset($login_eleve))&&(isset($tab_eleve_login))&&(!in_array($login_eleve,$tab_eleve_login))) {
		$login_eleve="";
		// AJOUTER UN APPEL A tentative_intrusion()
	}

	// Initialisation:
	if(!isset($login_eleve)) {$login_eleve="";}

	// On propose le choix de l'élève s'il y a plusieurs élèves associés au responsable
	echo make_eleve_select_html('consultation2.php', $_SESSION['login'], $login_eleve, $year, $month, $day);

	if((!isset($login_eleve))||($login_eleve=='')) {
		// On sélectionne le premier élève de la liste

		if(!isset($tab_eleve[0])) {
			echo "<p>Vous n'avez aucun élève en responsabilité&nbsp;???</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$login_eleve=$tab_eleve[0];
	}

	//echo "\$login_eleve=$login_eleve<br />";
}
else {
	// Proposer le formulaire de choix de mode:

	// Récupération de la liste des profs de l'établissement
	$tab_profs=array();
	$tab_profs2=array();
	$sql="SELECT u.civilite, u.nom, u.prenom, u.login FROM utilisateurs u WHERE etat='actif' AND statut='professeur' ORDER BY u.nom, u.prenom;";
	$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_prof)>0) {
		$cpt=0;
		while($lig_prof=mysqli_fetch_object($res_prof)) {
			$tab_profs[$cpt]['login']=$lig_prof->login;
			$tab_profs[$cpt]['civ_nom_prenom']=$lig_prof->civilite." ".casse_mot($lig_prof->nom,'maj')." ".casse_mot($lig_prof->prenom,'majf2');
			$tab_profs2[$lig_prof->login]=$tab_profs[$cpt]['civ_nom_prenom'];
			$cpt++;
		}
	}

	// Récupération de la liste des classes de l'établissement
	$tab_classe=array();
	$sql="SELECT id, classe FROM classes ORDER BY classe;";
	$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_classe)>0) {
		$cpt=0;
		while($lig_class_prof=mysqli_fetch_object($res_classe)) {
			$tab_classe[$cpt]['id_classe']=$lig_class_prof->id;
			$tab_classe[$cpt]['classe']=$lig_class_prof->classe;
			$cpt++;
		}
	}

	// Récupération de la liste des classes d'un professeur
	if($_SESSION['statut']=='professeur') {
		$tab_classe_du_prof=array();
		$sql="SELECT c.classe, jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp, classes c WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' AND jgc.id_classe=c.id ORDER BY c.classe;";

		$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_classe)>0) {
			$cpt=0;
			while($lig_prof=mysqli_fetch_object($res_classe)) {
				$tab_classe_du_prof[$cpt]['id_classe']=$lig_prof->id_classe;
				$tab_classe_du_prof[$cpt]['classe']=$lig_prof->classe;
				$cpt++;
			}
		}
	}

	if(isset($id_classe)) {
		$classe=get_class_from_id($id_classe);
	
		// Récupérer la liste des élèves de la classe pour proposer l'affichage pour tel élève
		$tab_eleve_de_la_classe=array();
		$sql="SELECT DISTINCT e.nom, e.prenom, e.login FROM j_eleves_classes jec, eleves e WHERE jec.login=e.login AND jec.id_classe='$id_classe' ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />";
		$res_ele_classe=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele_classe)>0) {
			$cpt=0;
			while($lig_ele=mysqli_fetch_object($res_ele_classe)) {
				$tab_eleve_de_la_classe[$cpt]['login']=$lig_ele->login;
				$tab_eleve_de_la_classe[$cpt]['nom_prenom']=casse_mot($lig_ele->nom,'maj')." ".casse_mot($lig_ele->prenom,'majf2');
				$cpt++;
			}
		}
	}

	// Choix par défaut selon le statut:
	if(!isset($mode)) {
		if($_SESSION['statut']=='professeur') {
			$mode='professeur';
		}
		else {
			$mode='classe';
		}
	}

	// Afficher les formulaires de choix pour les non-élève/non-responsable
	if(($_SESSION['statut']!='professeur')||
	(getSettingAOui('GepiAccesCDTToutesClasses'))) {
		// Choix d'une classe
		echo "<form name='form_choix_classe' enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
		echo "<fieldset id='choixClasse' style='border: 1px solid grey; width:15%; float:left; margin-right:1em;'>\n";
		echo "<legend style='border: 1px solid grey;'>Choix d'une classe</legend>\n";
		echo "<input type='hidden' name='mode' value='classe' />\n";

		if(isset($today)) {
			echo "<input type='hidden' name='today' value='$today' />\n";
		}

		echo "<select name='id_classe' onchange='document.form_choix_classe.submit();'>\n";
		echo "<option value=''>---</option>\n";
		for($i=0;$i<count($tab_classe);$i++) {
			echo "<option value='".$tab_classe[$i]['id_classe']."'";
			if((isset($id_classe))&&($id_classe==$tab_classe[$i]['id_classe'])) {echo " selected='selected'";}
			echo ">".$tab_classe[$i]['classe']."</option>\n";
		}
		echo "</select>\n";

		echo "<input type=\"submit\" id='bouton_submit_classe' value=\"Valider\" />\n";
		echo "</fieldset>\n";
		echo "</form>\n";
	}

	// Choix d'une classe du prof connecté
	if(isset($tab_classe_du_prof)) {
		echo "<form name='form_choix_une_de_mes_classes' enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
		echo "<fieldset id='choixUneDe_MesClasses' style='border: 1px solid grey; width:25%; float:left; margin-right:1em;'>\n";
		echo "<legend style='border: 1px solid grey;'>Choix d'une de mes classes</legend>\n";
		echo "<input type='hidden' name='mode' value='classe' />\n";

		if(isset($today)) {
			echo "<input type='hidden' name='today' value='$today' />\n";
		}

		echo "<select name='id_classe' onchange='document.form_choix_une_de_mes_classes.submit();'>\n";
		echo "<option value=''>---</option>\n";
		for($i=0;$i<count($tab_classe_du_prof);$i++) {
			echo "<option value='".$tab_classe_du_prof[$i]['id_classe']."'";
			if((isset($id_classe))&&($id_classe==$tab_classe_du_prof[$i]['id_classe'])) {echo " selected='selected'";}
			echo ">".$tab_classe_du_prof[$i]['classe']."</option>\n";
		}
		echo "</select>\n";

		echo "<input type=\"submit\" id='bouton_submit_une_de_mes_classes' value=\"Valider\" />\n";
		echo "</fieldset>\n";
		echo "</form>\n";
	}

	if(isset($tab_eleve_de_la_classe)) {
		echo "<form name='form_choix_eleve' enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
		echo "<fieldset id='choixEleve' style='border: 1px solid grey; width:25%; float:left; margin-right:1em;'>\n";
		echo "<legend style='border: 1px solid grey;'>Choix d'un élève de ".$classe."</legend>\n";
		echo "<input type='hidden' name='mode' value='eleve' />\n";
		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";

		if(isset($today)) {
			echo "<input type='hidden' name='today' value='$today' />\n";
		}

		echo "<select name='login_eleve' onchange='document.form_choix_eleve.submit();'>\n";
		echo "<option value=''>---</option>\n";
		for($i=0;$i<count($tab_eleve_de_la_classe);$i++) {
			echo "<option value='".$tab_eleve_de_la_classe[$i]['login']."'";
			if((isset($login_eleve))&&($login_eleve==$tab_eleve_de_la_classe[$i]['login'])) {echo " selected='selected'";}
			echo ">".$tab_eleve_de_la_classe[$i]['nom_prenom']."</option>\n";
		}
		echo "</select>\n";

		echo "<input type=\"submit\" id='bouton_submit_eleve' value=\"Valider\" />\n";
		echo "</fieldset>\n";
		echo "</form>\n";
	}

	// Il faudra peut-être revoir plus finement quels statuts peuvent accéder... il peut y avoir pas mal de catégories en statut 'autre'
	if(($_SESSION['statut']!='professeur')) {
		// Choix d'un professeur
		echo "<form name='form_choix_prof' enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
		echo "<fieldset id='choixProf' style='border: 1px solid grey; width:20%; float:left; margin-right:1em;'>\n";
		echo "<legend style='border: 1px solid grey;'>Choix d'un professeur</legend>\n";
		echo "<input type='hidden' name='mode' value='professeur' />\n";

		if(isset($today)) {
			echo "<input type='hidden' name='today' value='$today' />\n";
		}

		echo "<select name='login_prof' onchange='document.form_choix_prof.submit();'>\n";
		echo "<option value=''>---</option>\n";
		for($i=0;$i<count($tab_profs);$i++) {
			echo "<option value='".$tab_profs[$i]['login']."'";
			if((isset($login_prof))&&($login_prof==$tab_profs[$i]['login'])) {echo " selected='selected'";}
			echo ">".$tab_profs[$i]['civ_nom_prenom']."</option>\n";
		}
		echo "</select>\n";

		echo "<input type=\"submit\" id='bouton_submit_prof' value=\"Valider\" />\n";
		echo "</fieldset>\n";
		echo "</form>\n";

	}
	else {
		//echo "<div style='border: 1px solid grey; width:20%; float:left; margin-right:1em;'><a href='".$_SERVER['PHP_SELF']."?mode=professeur'>Mes enseignements</a></div>";
		//echo "<div style='border: 1px solid grey; width:20%; float:left; margin-right:1em;'>";
		echo "<fieldset id='choixMesEnseignements' style='border: 1px solid grey; width:20%; float:left; margin-right:1em;'>\n";
		echo "<legend style='border: 1px solid grey;'>Mes enseignements</legend>\n";
		echo "<a href='".$_SERVER['PHP_SELF']."?mode=professeur";
		if(isset($today)) {
			echo "&amp;today$today";
		}
		echo "'>Mes enseignements</a></div>\n";
		echo "</fieldset>\n";
	}

	// Retour à la ligne pour ce qui va suivre les cadres formulaires de choix:
	echo "<div style='clear:both;'>&nbsp;</div>";

}
//=============================================================

$ts_aujourdhui=time();

$ts_semaine_precedente=$today-7*24*3600;
$ts_semaine_suivante=$today+2*7*24*3600;

$ts_limite_visibilite_devoirs_pour_eleves=$ts_aujourdhui+getSettingValue('delai_devoirs')*24*3600;

//=============================================================
// Définition de valeurs par défaut si nécessaire, et récupération des groupes associés au mode choisi:
if($mode=='classe') {
	if(!isset($id_classe)) {
		if($_SESSION['statut']=='professeur') {
			$sql="SELECT id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp, classes c WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' AND jgc.id_classe=c.id ORDER BY c.classe LIMIT 1;";
			$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_classe)>0) {
				$id_classe = old_mysql_result($res_classe, 0, 'id_classe');
			}
		}

		if(!isset($id_classe)) {
			$sql="SELECT id AS id_classe FROM classes ORDER BY classe LIMIT 1;";
			$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_classe)>0) {
				$id_classe = old_mysql_result($res_classe, 0, 'id_classe');
			}
		}
	}

	if(!isset($id_classe)) {
		echo "<p>Aucune classe n'a été trouvée.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	// Passage à la semaine précédente/courante/suivante
	include("../lib/calendrier/calendrier.class.php");
	$cal1 = new Calendrier("form_choix_date", "today_jjmmaaaa");

	echo "<div style='float: right; width:25em;'>
	<form action='".$_SERVER['PHP_SELF']."' name='form_choix_date' id='form_choix_date' method='post'>
		<input type='hidden' name='today_jjmmaaaa' id='today_jjmmaaaa' value='' />
		<input type='hidden' name='id_classe' value='$id_classe' />
		<input type='hidden' name='mode' value='$mode' />

		<a href='".$_SERVER['PHP_SELF']."?today=".$ts_aujourdhui."&amp;mode=$mode&amp;id_classe=$id_classe'>Aujourd'hui</a>";

		echo "
		<a href=\"#calend\" onclick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170).";\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier début\" style=\"border:0;\" /></a>";

		//echo " ".img_calendrier_js("today_jjmmaaaa", "img_bouton_today_jjmmaaaa");

		echo "
		 - Semaines <a href='".$_SERVER['PHP_SELF']."?today=".$ts_semaine_precedente."&amp;mode=$mode&amp;id_classe=$id_classe'>précédente</a> / <a href='".$_SERVER['PHP_SELF']."?today=".$ts_semaine_suivante."&amp;mode=$mode&amp;id_classe=$id_classe'>suivante</a>
	</form>

	<script type='text/javascript'>
		var today_jjmmaaaa_0='';

		function teste_modif_date() {
			if(document.getElementById('today_jjmmaaaa').value!=today_jjmmaaaa_0) {
				document.getElementById('form_choix_date').submit();
			}
			else {
				setTimeout('teste_modif_date()', 1000);
			}
		}

		setTimeout('teste_modif_date()', 2000);
	</script>
</div>\n";

	$classe=get_class_from_id($id_classe);

	echo "<p>Affichage pour une classe&nbsp;: <strong>".$classe."</strong></p>\n";
	$groups=get_groups_for_class($id_classe);

}
elseif($mode=='eleve') {
	if(!isset($login_eleve)) {
		echo "<p>Aucun élève n'a été choisi.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	if(!isset($id_classe)) {
		$sql="SELECT id_classe FROM j_eleves_classes WHERE login='$login_eleve' ORDER BY periode DESC LIMIT 1;";
		$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_classe)>0) {
			$id_classe = old_mysql_result($res_classe, 0, 'id_classe');
		}
	}

	$classe=get_class_from_id($id_classe);

	$groups=get_groups_for_eleve($login_eleve, $id_classe);

	// Passage à la semaine précédente/courante/suivante
	include("../lib/calendrier/calendrier.class.php");
	$cal1 = new Calendrier("form_choix_date", "today_jjmmaaaa");

	echo "<div style='float: right; width:25em;'>
	<form action='".$_SERVER['PHP_SELF']."' name='form_choix_date' id='form_choix_date' method='post'>
		<input type='hidden' name='today_jjmmaaaa' id='today_jjmmaaaa' value='' />
		<input type='hidden' name='login_eleve' value='$login_eleve' />
		<input type='hidden' name='id_classe' value='$id_classe' />
		<input type='hidden' name='mode' value='$mode' />

		<a href='".$_SERVER['PHP_SELF']."?today=".$ts_aujourdhui."&amp;mode=$mode&amp;login_eleve=$login_eleve&amp;id_classe=$id_classe'>Aujourd'hui</a>";
		
		echo "
		<a href=\"#calend\" onclick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170).";\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier début\" style=\"border:0;\" /></a>";
		
		//echo " ".img_calendrier_js("today_jjmmaaaa", "img_bouton_today_jjmmaaaa");

		echo "
		 - Semaines <a href='".$_SERVER['PHP_SELF']."?today=".$ts_semaine_precedente."&amp;mode=$mode&amp;login_eleve=$login_eleve&amp;id_classe=$id_classe'>précédente</a> / <a href='".$_SERVER['PHP_SELF']."?today=".$ts_semaine_suivante."&amp;mode=$mode&amp;login_eleve=$login_eleve&amp;id_classe=$id_classe'>suivante</a>
	</form>

	<script type='text/javascript'>
		var today_jjmmaaaa_0='';

		function teste_modif_date() {
			if(document.getElementById('today_jjmmaaaa').value!=today_jjmmaaaa_0) {
				document.getElementById('form_choix_date').submit();
			}
			else {
				setTimeout('teste_modif_date()', 1000);
			}
		}

		setTimeout('teste_modif_date()', 2000);
	</script>
 </div>\n";

	echo "<p>Affichage pour un élève&nbsp;: <strong>".civ_nom_prenom($login_eleve)." (<em>$classe</em>)</strong></p>\n";

}
elseif($mode=='professeur') {
	if(!isset($login_prof)) {
		if($_SESSION['statut']=='professeur') {
			$login_prof=$_SESSION['login'];
		}
		else {
			$sql="SELECT u.civilite, u.nom, u.prenom, u.login FROM utilisateurs u WHERE statut='professeur' AND etat='actif' ORDER BY u.nom, u.prenom LIMIT 1;";
			$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_prof)>0) {
				$login_prof = old_mysql_result($res_prefs, 0, 'login');
			}
		}

		if(!isset($login_prof)) {
			echo "<p>Aucun professeur n'a été trouvé.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}

	$groups=get_groups_for_prof($login_prof);

	// Passage à la semaine précédente/courante/suivante
	include("../lib/calendrier/calendrier.class.php");
	$cal1 = new Calendrier("form_choix_date", "today_jjmmaaaa");

	echo "<div style='float: right; width:25em;'>
	<form action='".$_SERVER['PHP_SELF']."' name='form_choix_date' id='form_choix_date' method='post'>
		<input type='hidden' name='today_jjmmaaaa' id='today_jjmmaaaa' value='' />
		<input type='hidden' name='login_prof' value='$login_prof' />
		<input type='hidden' name='mode' value='$mode' />
		<a href='".$_SERVER['PHP_SELF']."?today=".$ts_aujourdhui."&amp;mode=$mode&amp;login_prof=$login_prof'>Aujourd'hui</a>";
	
	echo "
		<a href=\"#calend\" onclick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170).";\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier début\" style=\"border:0;\" /></a>";
	
	//echo " ".img_calendrier_js("today_jjmmaaaa", "img_bouton_today_jjmmaaaa");
	echo "
		 - Semaines <a href='".$_SERVER['PHP_SELF']."?today=".$ts_semaine_precedente."&amp;mode=$mode&amp;login_prof=$login_prof'>précédente</a> / <a href='".$_SERVER['PHP_SELF']."?today=".$ts_semaine_suivante."&amp;mode=$mode&amp;login_prof=$login_prof'>suivante</a>
	</form>

	<script type='text/javascript'>
		var today_jjmmaaaa_0='';

		function teste_modif_date() {
			if(document.getElementById('today_jjmmaaaa').value!=today_jjmmaaaa_0) {
				document.getElementById('form_choix_date').submit();
			}
			else {
				setTimeout('teste_modif_date()', 1000);
			}
		}

		setTimeout('teste_modif_date()', 2000);
	</script>
 </div>\n";

	echo "<p>Affichage pour un professeur&nbsp;: <strong>".$tab_profs2[$login_prof]."</strong></p>\n";
}
//=============================================================

//=============================================================
// Récupération des groupes du professeur connecté:
if($_SESSION['statut']=='professeur') {
	$tab_mes_groupes=array();

	if($mode!='professeur') {
		//$tab_mes_groupes=get_groups_for_prof($_SESSION['login']);
		$sql="SELECT id_groupe FROM j_groupes_professeurs WHERE login='".$_SESSION['login']."'";
		$res_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_tmp)>0) {
			while($lig_tmp=mysqli_fetch_object($res_tmp)) {
				$tab_mes_groupes[]=$lig_tmp->id_groupe;
			}
		}
	}
	else {
		foreach($groups as $key => $value) {
			$tab_mes_groupes[]=$value['id'];
		}
	}
}
//=============================================================

//================================================================
// Récupération des identifiants de couleurs associées aux matières dans l'EDT
$couleur_matiere=array();

$sql="SELECT m.matiere, es.valeur FROM edt_setting es, matieres m WHERE es.reglage = CONCAT('M_',m.matiere);";
//echo "$sql<br />";
$res_couleur=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_couleur)>0) {
	while($lig_couleur=mysqli_fetch_object($res_couleur)) {
		$couleur_matiere[$lig_couleur->matiere]=$lig_couleur->valeur;
	}
}
//================================================================

//================================================================
// Faire une sélection parmi les couleurs... on n'aura jamais autant de classes dans un établissement:
$tab_toutes_couleurs=array("aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen");

$couleur_classe=array();
if(isset($tab_classe)) {
	for($i=0;$i<count($tab_classe);$i++) {
		$couleur_classe[$tab_classe[$i]['id_classe']]=$tab_toutes_couleurs[$i];
	}
}
//================================================================

//=============================================================
// Récupération du premier jour de la semaine:
//$num_jour_semaine=id_j_semaine($today);
$num_jour_semaine=strftime("%w",$today);
if($num_jour_semaine==0) {$num_jour_semaine=7;}
//echo "\$num_jour_semaine=$num_jour_semaine<br />";
$premier_jour_semaine=$today-(3600*24*($num_jour_semaine-1));
//echo "strftime('%d/%m/%Y',\$today)=".strftime("%d/%m/%Y",$today)."<br />";
//echo "strftime('%u',\$today)=".id_j_semaine($today)."<br />";
//echo "strftime('%w',\$today)=".strftime("%w",$today)."<br />";
// %u 	Représentation ISO-8601 du jour de la semaine 	De 1 (pour Lundi) à 7 (pour Dimanche)
// %w 	Représentation numérique du jour de la semaine 	De 0 (pour Dimanche) à 6 (pour Samedi)
//=============================================================

//=============================================================
// Récupération des notices:
$tab_notice=array();
for($i=0;$i<14;$i++) {
	$tab_notice[$i]=array();

	$ts_jour_debut=$premier_jour_semaine+$i*3600*24;
	$ts_jour_fin=$premier_jour_semaine+($i+1)*3600*24;

	//echo "<p>".strftime("%d/%m/%Y",$ts_jour_debut)."</p>";

	foreach($groups as $current_group) {
		$id_groupe=$current_group['id'];

		if(!isset($couleur_matiere[$current_group['matiere']['matiere']])) {
			$couleur_matiere[$current_group['matiere']['matiere']]="";
		}

		$sql="SELECT * FROM ct_entry WHERE id_groupe='$id_groupe' AND date_ct>=$ts_jour_debut AND date_ct<$ts_jour_fin ORDER BY date_ct;";
		//echo "$sql<br />";
		$res_ct=mysqli_query($GLOBALS["mysqli"], $sql);
		$cpt=0;
		while($ligne_ct=mysqli_fetch_object($res_ct)) {
			//if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
			if((($_SESSION['statut']=='professeur')&&(in_array($id_groupe,$tab_mes_groupes)))||
			($ligne_ct->date_ct<=$ts_aujourdhui)) {
				//echo "<div style='border:1px solid black; margin:0.5em;'>".$current_group['name']."<br />".$ligne_ct->contenu."</div>\n";
				$tab_notice[$i][$id_groupe]['ct_entry'][$cpt]['id_ct']=$ligne_ct->id_ct;
				$tab_notice[$i][$id_groupe]['ct_entry'][$cpt]['contenu']="";

				// Lien d'édition de la notice:
				//if(($_SESSION['statut']=='professeur')&&(in_array($id_groupe,$tab_mes_groupes))) {
					if(($_SESSION['statut']=='professeur')&&(($ligne_ct->id_login==$_SESSION['login'])||(getSettingAOui('cdt_autoriser_modif_multiprof')))) {
						if((!getSettingAOui('visa_cdt_inter_modif_notices_visees'))||($ligne_ct->vise!='y')){
							$tab_notice[$i][$id_groupe]['ct_entry'][$cpt]['contenu'].="<div style='float:right; width:16px;'><a href='../cahier_texte/index.php?id_groupe=$id_groupe&amp;id_ct=$ligne_ct->id_ct&amp;type_notice=cr'><img src='../images/edit16.png' width='16' height='16' /></a></div>";
						}
					}

					// Notice proprement dite:
					$tab_notice[$i][$id_groupe]['ct_entry'][$cpt]['contenu'].=$ligne_ct->contenu;
				/*
				}
				else {
					// Un élève,... ne voit pas les compte-rendus dans le futur
					if($ligne_ct->date_ct<=$ts_aujourdhui) {
						$tab_notice[$i][$id_groupe]['ct_entry'][$cpt].=$ligne_ct->contenu;
					}
				}
				*/

				// Documents joints:
				// Dans le futur, ils ne sont vus que par les profs du groupe
				if((($_SESSION['statut']=='professeur')&&(in_array($id_groupe,$tab_mes_groupes)))||
					($ligne_ct->date_ct<=$ts_aujourdhui)) {
					$sql="SELECT * FROM ct_documents where id_ct='$ligne_ct->id_ct';";
					$res_doc=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_doc)>0) {
						$tab_notice[$i][$id_groupe]['ct_entry'][$cpt]['contenu'].="<br /><strong>Documents joints&nbsp;:</strong>";
						while($ligne_ct_doc=mysqli_fetch_object($res_doc)) {
							// Tester si le document est visible ou non dans le cas ele/resp
							if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
							($ligne_ct_doc->visible_eleve_parent==1))
							{
								$tab_notice[$i][$id_groupe]['ct_entry'][$cpt]['contenu'].="<br />\n<a href='$ligne_ct_doc->emplacement' title=\"$ligne_ct_doc->titre\" target='_blank'>".$ligne_ct_doc->titre."</a>";
							}
						}
					}
					$cpt++;
				}
			}
		}

		$sql="SELECT * FROM ct_devoirs_entry WHERE id_groupe='$id_groupe' AND date_ct>=$ts_jour_debut AND date_ct<$ts_jour_fin ORDER BY date_ct;";
		//echo "$sql<br />";
		$res_ct=mysqli_query($GLOBALS["mysqli"], $sql);
		$cpt=0;
		while($ligne_ct=mysqli_fetch_object($res_ct)) {
			if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
			($ligne_ct->date_ct<=$ts_limite_visibilite_devoirs_pour_eleves)) {

				$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$cpt]['id_ct']=$ligne_ct->id_ct;
				$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$cpt]['contenu']="";

				// Lien d'édition de la notice:
				if(($_SESSION['statut']=='professeur')&&(in_array($id_groupe,$tab_mes_groupes))) {
					if(($ligne_ct->id_login==$_SESSION['login'])||(getSettingAOui('cdt_autoriser_modif_multiprof'))) {
						if((!getSettingAOui('visa_cdt_inter_modif_notices_visees'))||($ligne_ct->vise!='y')){
							$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$cpt]['contenu'].="<div style='float:right; width:16px;'><a href='../cahier_texte/index.php?id_groupe=$id_groupe&amp;id_ct=$ligne_ct->id_ct&amp;edit_devoir=yes&amp;type_notice=dev'><img src='../images/edit16.png' width='16' height='16' /></a></div>";
						}
					}
				}

				// Notice proprement dite:
				$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$cpt]['contenu'].=$ligne_ct->contenu;

				// Documents joints:
				$sql="SELECT * FROM ct_devoirs_documents where id_ct_devoir='$ligne_ct->id_ct';";
				$res_doc=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_doc)>0) {
					$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$cpt]['contenu'].="<br /><strong>Documents joints&nbsp;:</strong>";
					while($ligne_ct_doc=mysqli_fetch_object($res_doc)) {
						if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
						($ligne_ct_doc->visible_eleve_parent==1))
						{
							$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$cpt]['contenu'].="<br />\n<a href='$ligne_ct_doc->emplacement' title=\"$ligne_ct_doc->titre\" target='_blank'>".$ligne_ct_doc->titre."</a>";
						}
					}
				}
				$cpt++;
			}
		}

		$sql="SELECT * FROM ct_private_entry WHERE id_groupe='$id_groupe' AND date_ct>=$ts_jour_debut AND date_ct<$ts_jour_fin ORDER BY date_ct;";
		//echo "$sql<br />";
		$res_ct=mysqli_query($GLOBALS["mysqli"], $sql);
		$cpt=0;
		while($ligne_ct=mysqli_fetch_object($res_ct)) {
			//$tab_notice[$i][$id_groupe]['ct_private_entry'][$cpt]="";

			// Lien d'édition de la notice:
			// Les notices privées en multiprof??? sont-elles visibles du seul prof ou des profs du groupe?
			if(($_SESSION['statut']=='professeur')&&(in_array($id_groupe,$tab_mes_groupes))) {
				if(($ligne_ct->id_login==$_SESSION['login'])||(getSettingAOui('cdt_autoriser_modif_multiprof'))) {
					$tab_notice[$i][$id_groupe]['ct_private_entry'][$cpt]['contenu']="<div style='float:right; width:16px;'><a href='../cahier_texte/index.php?id_groupe=$id_groupe&amp;id_ct=$ligne_ct->id_ct&amp;type_notice=np'><img src='../images/edit16.png' width='16' height='16' /></a></div>";

					// Notice proprement dite:
					$tab_notice[$i][$id_groupe]['ct_private_entry'][$cpt]['contenu'].=$ligne_ct->contenu;
					$cpt++;
				}
			}
		}
	}
}
//=============================================================

//=============================================================
$chaine_id_groupe="";
foreach($groups as $current_group) {
	$id_groupe=$current_group['id'];
	if($chaine_id_groupe!="") {$chaine_id_groupe.=", ";}
	$chaine_id_groupe.="'$id_groupe'";
}
//=============================================================

//================================================
// 20130727
$class_notice_dev_fait="color_fond_notices_t_fait";
$class_notice_dev_non_fait="color_fond_notices_t";

$CDTPeutPointerTravailFait=getSettingAOui('CDTPeutPointerTravailFait'.ucfirst($_SESSION['statut']));

if($CDTPeutPointerTravailFait) {
	if($login_eleve!='') {
		$tab_etat_travail_fait=get_tab_etat_travail_fait($login_eleve);
		echo js_cdt_modif_etat_travail();
	}
}
//================================================

//=============================================================
// Boucle sur les 14 jours affichés
$max_cpt_grp=0;
for($i=0;$i<14;$i++) {
	$ts_jour_debut=$premier_jour_semaine+$i*3600*24;

	if($i%7==0) {
		if($i>0) {
			echo "</tr>\n";
			echo "</table>\n";
			echo "</div>\n";

			//echo "<br />";
		}

		echo "<div class='cdt_cadre_semaine'>\n";
		echo "<table border='1' width='100%'>\n";
		echo "<tr class='cdt_tab_semaine'>\n";
	}

	echo "<td>\n";
	echo "<div class='cdt_cadre_jour'>\n";

	$temoin_dev_non_vides=0;
	$temoin_cr_non_vides=0;
	$temoin_np_non_vides=0;

	$jour_courant=ucfirst(strftime("%a %d/%m/%y",$ts_jour_debut));

	echo "<p id='p_jour_$i' class='infobulle_entete' style='text-align:center;'>";
	echo "<a href='#ancre_travail_jour_$i' onclick=\"affichage_travail_jour($i);return false;\" class='cdt_lien_jour'>";
	echo $jour_courant;
	echo "</a>";
	echo "</p>\n";

	$titre_infobulle_jour="<a name='ancre_travail_jour_$i'></a>".$jour_courant;
	$texte_infobulle_jour="";

	$cpt_grp=0;
	$cpt_notice=0;
	// Boucle sur les groupes
	foreach($groups as $current_group) {
		$id_groupe=$current_group['id'];

		// Si il y a une notice pour ce groupe sur le jour courant de la boucle:
		if(isset($tab_notice[$i][$id_groupe])) {
			echo "   <div style='border: 1px solid orange; margin:3px;";
			//echo "opacity:0.5;";
			// Colorisation différente selon le mode d'affichage:
			if($mode=='professeur') {
				if((isset($couleur_classe[$current_group["classes"]["list"][0]]))&&($couleur_classe[$current_group["classes"]["list"][0]]!='')) {echo " background-color:".$couleur_classe[$current_group["classes"]["list"][0]].";";}
			}
			else {
				if((isset($couleur_matiere[$current_group['matiere']['matiere']]))&&($couleur_matiere[$current_group['matiere']['matiere']]!='')&&(isset($tab_couleur_edt[$couleur_matiere[$current_group['matiere']['matiere']]]))) {echo " background-color:".$tab_couleur_edt[$couleur_matiere[$current_group['matiere']['matiere']]].";";}
			}
			echo "'>\n";

			//echo "<div style='color:black; opacity:1;'>";

			/*
			echo "<a href=\"#ancre_travail_jour_".$i."_groupe_".$id_groupe."\" onclick=\"affichage_notices_tel_groupe($i, $id_groupe);return false;\" class='cdt_lien_groupe'>";
			if($mode=='professeur') {
				echo $current_group['name']." (<em>".$current_group['classlist_string']."</em>)";
			}
			else {
				echo $current_group['name'];
			}
			echo "</a>";
			*/

			//==================================================================
			// Cadre pour le groupe courant dans le cadre du jour courant dans l'infobulle du jour:
			$texte_infobulle_jour.="<div id='travail_jour_".$i."_groupe_".$id_groupe."'>\n";

			$texte_dev_courant="";
			if(isset($tab_notice[$i][$id_groupe]['ct_devoirs_entry'])) {
				// Liste des devoirs donnés pour ce jour dans ce groupe:
				for($j=0;$j<count($tab_notice[$i][$id_groupe]['ct_devoirs_entry']);$j++) {
					// 20130727
					$class_color_fond_notice="color_fond_notices_t";
					if($CDTPeutPointerTravailFait) {
						get_etat_et_img_cdt_travail_fait($tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$j]['id_ct']);
					}

					//$texte_dev_courant.="<div id='div_travail_".$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$j]['id_ct']."' style='background-color:".$color_fond_notices['t']."; border: 1px solid black; margin: 1px;'$chaine_class_color_fond_notice>\n";
					$texte_dev_courant.="<div id='div_travail_".$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$j]['id_ct']."' style='border: 1px solid black; margin: 1px;' class='$class_color_fond_notice'>\n";

					if($CDTPeutPointerTravailFait) {
						$texte_dev_courant.="<div id='div_etat_travail_".$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$j]['id_ct']."' style='float:right; width: 16px; margin: 2px; text-align: center;'><a href=\"javascript:cdt_modif_etat_travail('$login_eleve', '".$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$j]['id_ct']."')\" title=\"$texte_etat_travail\"><img src='$image_etat' class='icone16' /></a></div>\n";
					}

					$chaine_tag=get_liste_tag_notice_cdt($tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$j]['id_ct'], 't', "right");
					if($chaine_tag!="") {
						$texte_dev_courant.=$chaine_tag;
					}

					$texte_dev_courant.=$tab_notice[$i][$id_groupe]['ct_devoirs_entry'][$j]['contenu'];
					$texte_dev_courant.="</div>\n";
					$temoin_dev_non_vides++;
				}

				if($texte_dev_courant!="") {
					/*
					$texte_infobulle_jour.="<div style='width: 1em; background-color: pink; float: right; margin-left:3px; text-align:center;'>\n";
					$texte_infobulle_jour.="<a href='#' onclick=\"alterne_affichage('travail_jour_".$i."_groupe_".$id_groupe."_devoirs');return false;\">";
					$texte_infobulle_jour.="T";
					$texte_infobulle_jour.="</a>";
					$texte_infobulle_jour.="</div>\n";
					*/

					$texte_dev_courant="<div id='travail_jour_".$i."_groupe_".$id_groupe."_devoirs' style='background-color:".$color_fond_notices['t']."'>\n".$texte_dev_courant;
					$texte_dev_courant.="</div>\n";
				}
			}

			$texte_cr_courant="";
			if(isset($tab_notice[$i][$id_groupe]['ct_entry'])) {
				// Liste des compte-renddus pour ce jour dans ce groupe:
				for($j=0;$j<count($tab_notice[$i][$id_groupe]['ct_entry']);$j++) {
					$texte_cr_courant.="<div style='background-color:palegreen; border: 1px solid black; margin: 1px;'>\n";

					$chaine_tag=get_liste_tag_notice_cdt($tab_notice[$i][$id_groupe]['ct_entry'][$j]['id_ct'], 'c', "right");
					if($chaine_tag!="") {
						$texte_cr_courant.=$chaine_tag;
					}

					$texte_cr_courant.=$tab_notice[$i][$id_groupe]['ct_entry'][$j]['contenu'];
					$texte_cr_courant.="</div>\n";
					$temoin_cr_non_vides++;
				}

				if($texte_cr_courant!="") {
					/*
					$texte_infobulle_jour.="<div style='width: 1em; background-color: pink; float: right; margin-left:3px; text-align:center;'>\n";
					$texte_infobulle_jour.="<a href='#' onclick=\"alterne_affichage('travail_jour_".$i."_groupe_".$id_groupe."_compte_rendu');return false;\">";
					$texte_infobulle_jour.="C";
					$texte_infobulle_jour.="</a>";
					$texte_infobulle_jour.="</div>\n";
					*/

					$texte_cr_courant="<div id='travail_jour_".$i."_groupe_".$id_groupe."_compte_rendu' style='background-color:".$color_fond_notices['c']."'>\n".$texte_cr_courant;
					$texte_cr_courant.="</div>\n";
				}
			}

			$texte_np_courant="";
			if(isset($tab_notice[$i][$id_groupe]['ct_private_entry'])) {
				// Liste des notices privées pour ce jour dans ce groupe:
				for($j=0;$j<count($tab_notice[$i][$id_groupe]['ct_private_entry']);$j++) {
					$texte_np_courant.="<div style='background-color:".$color_fond_notices['p']."; border: 1px solid black; margin: 1px;'>\n";
					$chaine_tag=get_liste_tag_notice_cdt($tab_notice[$i][$id_groupe]['ct_private_entry'][$j]['id_ct'], 'p', "right");
					if($chaine_tag!="") {
						$texte_np_courant.=$chaine_tag;
					}
					$texte_np_courant.=$tab_notice[$i][$id_groupe]['ct_private_entry'][$j]['contenu'];
					$texte_np_courant.="</div>\n";
					$temoin_np_non_vides++;
				}

				if($texte_np_courant!="") {
					/*
					$texte_infobulle_jour.="<div style='width: 1em; background-color: ".$color_fond_notices['p']."; float: right; margin-left:3px; text-align:center;'>\n";
					$texte_infobulle_jour.="<a href='#' onclick=\"alterne_affichage('travail_jour_".$i."_groupe_".$id_groupe."_notice_privee');return false;\">";
					$texte_infobulle_jour.="P";
					$texte_infobulle_jour.="</a>";
					$texte_infobulle_jour.="</div>\n";
					*/

					$texte_np_courant="<div id='travail_jour_".$i."_groupe_".$id_groupe."_notice_privee' style='background-color:".$color_fond_notices['p']."'>\n".$texte_np_courant;
					$texte_np_courant.="</div>\n";
				}
			}

			// On remplit le cadre pour le groupe courant dans le cadre du jour courant dans l'infobulle du jour
			// avec le nom du groupe, puis les devoirs donnés pour ce jour et enfin les compte-rendus de séance
			$texte_infobulle_jour.="<a name='ancre_travail_jour_".$i."_groupe_".$id_groupe."'></a>\n";
			$texte_infobulle_jour.="<strong>";
			if($mode=='professeur') {
				$texte_infobulle_jour.=$current_group['name']." (<em>".$current_group['classlist_string']."</em>)";
			}
			else {
				$texte_infobulle_jour.=$current_group['name'];
			}
			$texte_infobulle_jour.="</strong>\n";
			$texte_infobulle_jour.=$texte_dev_courant;
			$texte_infobulle_jour.=$texte_cr_courant;
			$texte_infobulle_jour.=$texte_np_courant;

			$texte_infobulle_jour.="&nbsp;<br />\n";

			$texte_infobulle_jour.="</div>\n";
			// Fin du cadre pour le groupe courant dans le cadre du jour courant dans l'infobulle du jour
			//==================================================================


			// Pour repérer les enseignements avec tel ou tel type de notice
			if($texte_np_courant!='') {
				// La restriction des notices visibles est fait plus haut
				echo "      <!-- Témoin de présence de notices privées pour le groupe $id_groupe sur le jour $i -->\n";
				echo "      <div style='width: 1em; background-color: ".$color_fond_notices['p']."; float: right; margin-left:3px; text-align:center;'>\n";
				echo "         <a href='#ancre_travail_jour_".$i."_groupe_".$id_groupe."' onclick=\"affichage_notices_tel_groupe($i, $id_groupe);return false;\" title=\"Notice privée\">P</a>\n";
				echo "      </div>\n";
			}
			if($texte_dev_courant!='') {
				// La restriction des notices visibles est fait plus haut
				echo "      <!-- Témoin de présence de notices de devoirs pour le groupe $id_groupe sur le jour $i -->\n";
				echo "      <div style='width: 1em; background-color: ".$color_fond_notices['t']."; float: right; margin-left:3px; text-align:center;'>\n";
				echo "         <a href='#ancre_travail_jour_".$i."_groupe_".$id_groupe."' onclick=\"affichage_notices_tel_groupe($i, $id_groupe);return false;\" title=\"Travail à faire\">T</a>\n";
				echo "      </div>\n";
			}
			if($texte_cr_courant!='') {
				// La restriction des notices visibles est fait plus haut
				echo "      <!-- Témoin de présence de comptes-rendus pour le groupe $id_groupe sur le jour $i -->\n";
				echo "      <div style='width: 1em; background-color: ".$color_fond_notices['c']."; float: right; margin-left:3px; text-align:center;'>\n";
				echo "         <a href='#ancre_travail_jour_".$i."_groupe_".$id_groupe."' onclick=\"affichage_notices_tel_groupe($i, $id_groupe);return false;\" title=\"Compte-rendu de séance\">C</a>\n";
				echo "      </div>\n";
			}


			echo "      <!-- Lien d'affichage du jour $i pour le groupe $id_groupe -->\n";
			echo "      <a href=\"#ancre_travail_jour_".$i."_groupe_".$id_groupe."\" onclick=\"affichage_notices_tel_groupe($i, $id_groupe);return false;\" class='cdt_lien_groupe'>";
			if($mode=='professeur') {
				echo $current_group['name']." (<em>".$current_group['classlist_string']."</em>)";
			}
			else {
				echo $current_group['name'];
			}
			echo "</a>\n";


			//echo "</div>\n";

			echo "   </div>\n\n";
		}
		$cpt_grp++;
	}

	// Ajouter un lien jour précédent avant
	if($i>0) {
		$indice_prec=$i-1;
		$lien_jour_prec="<a href='#' onclick=\"cacher_div('travail_jour_$i');
												var tmp_x=document.getElementById('travail_jour_$i').style.left;
												var tmp_y=document.getElementById('travail_jour_$i').style.top;
												affichage_travail_jour($indice_prec);
												document.getElementById('travail_jour_$indice_prec').style.left=tmp_x;
												document.getElementById('travail_jour_$indice_prec').style.top=tmp_y;
												return false;\" style='text-decoration:none; color:white'>";

		$lien_jour_prec.="<img src='../images/icons/arrow-left.png' />";
		$lien_jour_prec.="</a>";

		$titre_infobulle_jour=$lien_jour_prec." \n".$titre_infobulle_jour;
	}

	// Ajouter un lien jour suivant après
	if($i<14) {
		$indice_suiv=$i+1;
		$lien_jour_suiv="<a href='#' onclick=\"cacher_div('travail_jour_$i');
												var tmp_x=document.getElementById('travail_jour_$i').style.left;
												var tmp_y=document.getElementById('travail_jour_$i').style.top;
												affichage_travail_jour($indice_suiv);
												document.getElementById('travail_jour_$indice_suiv').style.left=tmp_x;
												document.getElementById('travail_jour_$indice_suiv').style.top=tmp_y;
												return false;\" style='text-decoration:none; color:white'>";
		$lien_jour_suiv.="<img src='../images/icons/arrow-right.png' />";
		$lien_jour_suiv.="</a>";

		$titre_infobulle_jour=$titre_infobulle_jour." \n".$lien_jour_suiv."\n";
	}

	// Masquage de tel type de notice d'un clic:
	if(($temoin_dev_non_vides>0)||($temoin_cr_non_vides>0)||($temoin_np_non_vides>0)) {
		$ajout="";
		if($temoin_cr_non_vides>0) {
			$ajout.="<a id='lien_alterne_affichage_compte_rendu_jour_$i' href='#' onclick=\"alterne_affichage_global('compte_rendu',$i);return false;\" style='background-color: ".$color_fond_notices['c'].";' title=\"Alterner l'affichage/masquage des compte-rendus de séances\">";
			$ajout.="C";
			$ajout.="</a>\n";
		}

		if($temoin_dev_non_vides>0) {
			$ajout.=" ";
			$ajout.="<a id='lien_alterne_affichage_devoirs_jour_$i' href='#' onclick=\"alterne_affichage_global('devoirs',$i);return false;\" style='background-color: ".$color_fond_notices['t'].";' title=\"Alterner l'affichage/masquage des travaux à faire\">";
			$ajout.="T";
			$ajout.="</a>\n";
		}

		if($temoin_np_non_vides>0) {
			$ajout.=" ";
			$ajout.="<a href='#' id='lien_alterne_affichage_notice_privee_jour_$i' onclick=\"alterne_affichage_global('notice_privee',$i);return false;\" style='background-color: ".$color_fond_notices['p'].";' title=\"Alterner l'affichage/masquage des notices privées\">";
			$ajout.="P";
			$ajout.="</a>\n";
		}

		$titre_infobulle_jour=$titre_infobulle_jour." ".$ajout;
	}
	else {
		$texte_infobulle_jour.="Aucune saisie pour ce jour.";
		$texte_infobulle_jour.="&nbsp;<br />";
	}

	if($cpt_grp>$max_cpt_grp) {$max_cpt_grp=$cpt_grp;}

	if($texte_infobulle_jour=="") {

		echo "<script type='text/javascript'>
document.getElementById('p_jour_$i').style.innerHTML='$jour_courant';
</script>\n";
	}

	//$tabdiv_infobulle[]=creer_div_infobulle("travail_jour_".$i,$titre_infobulle_jour,"",$texte_infobulle_jour,"pink",20,0,'y','y','n','n');
	$tabdiv_infobulle[]=creer_div_infobulle2("travail_jour_".$i,$titre_infobulle_jour,"",$texte_infobulle_jour,"pink",20,0,'y','y','n','n');

	echo "</div></td>\n";
}
echo "</tr>\n";
echo "</table>\n";
echo "</div>\n";
//=============================================================

echo "<script type='text/javascript'>
var tab_grp=new Array($chaine_id_groupe);

// Si javascript est actif, on cache les boutons submit inutiles (déclenchement du submit sur onchange()):
if(document.getElementById('bouton_submit_classe')) {document.getElementById('bouton_submit_classe').style.display='none';}
if(document.getElementById('bouton_submit_une_de_mes_classes')) {document.getElementById('bouton_submit_une_de_mes_classes').style.display='none';}
if(document.getElementById('bouton_submit_prof')) {document.getElementById('bouton_submit_prof').style.display='none';}
</script>\n";

/*
echo "<pre>";
echo print_r($tab_mes_groupes);
echo "</pre>";

echo "id_classe=$id_classe<br />";
*/

$tab_grp=array();
/*
if($_SESSION['statut']=='professeur') {

	if($mode=='professeur') {
		//$tab_champs=array();
		$tab_grp=get_groups_for_prof($_SESSION['login']);
	}
}
elseif(($_SESSION['statut']=='responsable')||($_SESSION['statut']=='eleve')) {
	// A VOIR: Cas des élèves qui changent de classe...
	$tab_grp=get_groups_for_eleve($login_eleve, $id_classe);
}
*/

if($mode=='professeur') {
	//$tab_champs=array();
	$tab_grp=get_groups_for_prof($_SESSION['login']);
}
elseif($mode=='classe') {
	$tab_grp=get_groups_for_class($id_classe);
}
elseif($mode=='eleve') {
	// A VOIR: Cas des élèves qui changent de classe...
	$tab_grp=get_groups_for_eleve($login_eleve, $id_classe);
}

if(count($tab_grp)>0) {
	$infos_generales="";

	foreach($tab_grp as $current_group) {
		$id_groupe=$current_group['id'];
		$content="";

		// Affichage des informations générales
		//$sql="SELECT contenu, id_ct  FROM ct_entry WHERE (id_groupe='$id_groupe' and (date_ct='' OR date_ct='0'));";
		$sql="SELECT contenu, id_ct  FROM ct_entry WHERE (id_groupe='$id_groupe' and date_ct='');";
		//echo "$sql<br />";
		$appel_info_cahier_texte = mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_cahier_texte = mysqli_num_rows($appel_info_cahier_texte);
		while($lig_ct=mysqli_fetch_object($appel_info_cahier_texte)) {
			$tmp_content=$lig_ct->contenu;
			$id_ct = $lig_ct->id_ct;
			$tmp_content.=affiche_docs_joints($id_ct,"c");

			if($tmp_content!="") {
				$content.="<div style='margin:1em; padding:0.5em;border:1px solid black;'>";
				$content.=$tmp_content;
				$content.="</div>";
			}
		}

		if($content!="") {
			$infos_generales.="<div class='see_all_general couleur_bord_tableau_notice color_fond_notices_i' style='width:98%;padding:0.5em;margin:1em;'>";
			$infos_generales.="<h3>".$current_group['name']." (<em>".$current_group['description']." en ".$current_group['classlist_string']."</em>)"."</h3>";
			$infos_generales.=$content;
			$infos_generales.="</div>";
		}
	}

	if ($infos_generales != '') {
		echo "<div style='padding:1em;'>\n";
		echo "<h2 class='grande_ligne couleur_bord_tableau_notice'>\n<strong>INFORMATIONS GENERALES</strong>\n</h2>\n";
		echo $infos_generales;
		echo "</div>\n";
	}
}

echo "<hr />\n";
echo "<p style='text-align:center; font-style:italic;'>Cahiers de textes du ";
echo strftime("%d/%m/%Y", getSettingValue("begin_bookings"));
echo " au ";
echo strftime("%d/%m/%Y", getSettingValue("end_bookings"));
echo "</p>\n";

require("../lib/footer.inc.php");
?>
