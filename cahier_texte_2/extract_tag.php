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
//if (!acces_cdt()) {
if((!getSettingAOui("active_cahiers_texte"))&&(!getSettingAOui('acces_cdt_prof'))) {
	tentative_intrusion(1, "Tentative d'accès au cahier de textes en consultation alors que le module n'est pas activé.");
	die("Le module n'est pas activé.");
}

//debug_var();

//=======================
// Pour éviter de refaire le choix des dates en revenant ici, on utilise la SESSION...
$annee = strftime("%Y");
$mois = strftime("%m");
$jour = strftime("%d");
$heure = strftime("%H");
$minute = strftime("%M");

if($mois>8) {$date_debut_tmp="01/09/$annee";} else {$date_debut_tmp="01/09/".($annee-1);}

$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : (isset($_SESSION['cdt_extract_tag']['display_date_debut']) ? $_SESSION['cdt_extract_tag']['display_date_debut'] : (isset($_SESSION['display_date_debut']) ? $_SESSION['display_date_debut'] : $date_debut_tmp));

$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : (isset($_SESSION['cdt_extract_tag']['display_date_fin']) ? $_SESSION['cdt_extract_tag']['display_date_fin'] : (isset($_SESSION['display_date_fin']) ? $_SESSION['display_date_fin'] : $jour."/".$mois."/".$annee));
//=======================

$mode=isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : NULL);
$type_notice=isset($_POST["type_notice"]) ? $_POST["type_notice"] : array();
$tag=isset($_POST["tag"]) ? $_POST["tag"] : array();
$id_groupe=isset($_POST["id_groupe"]) ? $_POST["id_groupe"] : array();

$id_classe=isset($_POST["id_classe"]) ? $_POST["id_classe"] : array();
$login_prof=isset($_POST["login_prof"]) ? $_POST["login_prof"] : array();

$tab_tag_type=get_tab_tag_cdt();
/*
echo "<pre>";
print_r($tab_tag_type);
echo "</pre>";
*/
if((isset($mode))&&($mode=="extraire")) {
	$tab_notices=array();

	$_SESSION['cdt_extract_tag']=array();

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

		//echo "\$display_date_debut=$display_date_debut<br />";
		$ts_debut=mktime(0,0,0,$mois_debut,$jour_debut,$annee_debut);
		if(!$ts_debut) {
			$ts_debut=getSettingValue("begin_bookings");
		}
		//echo "\$ts_debut=$ts_debut<br />";

		//$_SESSION['cdt_extract_tag']['ts_debut']=$ts_debut;
		$_SESSION['cdt_extract_tag']['display_date_debut']=strftime('%d/%m/%Y', $ts_debut);
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

		//$_SESSION['cdt_extract_tag']['ts_fin']=$ts_fin;
		$_SESSION['cdt_extract_tag']['display_date_fin']=strftime('%d/%m/%Y', $ts_fin);
	}

	$sql_id_classe="";
	if(count($id_classe)>0) {
		$_SESSION['cdt_extract_tag']['id_classe']=array();

		$cpt_clas=0;
		$sql_id_classe=" AND (id_groupe IN (SELECT DISTINCT id_groupe FROM j_groupes_classes WHERE ";
		for($loop=0;$loop<count($id_classe);$loop++) {
			if($cpt_clas>0) {
				$sql_id_classe.=" OR ";
			}
			$sql_id_classe.="id_classe='".$id_classe[$loop]."'";

			$_SESSION['cdt_extract_tag']['id_classe'][]=$id_classe[$loop];

			$cpt_clas++;
		}
		$sql_id_classe.="))";
	}

	$sql_login_prof="";
	if(count($login_prof)>0) {
		$_SESSION['cdt_extract_tag']['login_prof']=array();

		$cpt_prof=0;
		$sql_login_prof=" AND (";
		for($loop=0;$loop<count($login_prof);$loop++) {
			if($cpt_prof>0) {
				$sql_login_prof.=" OR ";
			}
			$sql_login_prof.="id_login='".$login_prof[$loop]."'";

			$_SESSION['cdt_extract_tag']['login_prof'][]=$login_prof[$loop];

			$cpt_prof++;
		}
		$sql_login_prof.=")";
	}

	$sql_id_groupe="";
	if(count($id_groupe)>0) {
		$_SESSION['cdt_extract_tag']['id_groupe']=array();

		$cpt_grp=0;
		$sql_id_groupe=" AND (";
		for($loop=0;$loop<count($id_groupe);$loop++) {
			// A FAIRE : Ajouter un test sur les groupes du prof
			if($cpt_grp>0) {
				$sql_id_groupe.=" OR ";
			}
			$sql_id_groupe.="id_groupe='".$id_groupe[$loop]."'";

			$_SESSION['cdt_extract_tag']['id_groupe'][]=$id_groupe[$loop];

			$cpt_grp++;
		}
		$sql_id_groupe.=")";
	}

	$sql_tag="";
	if(count($tag)>0) {
		$_SESSION['cdt_extract_tag']['tag']=array();

		$sql_tag=" AND (";
		for($loop=0;$loop<count($tag);$loop++) {
			if($loop>0) {
				$sql_tag.=" OR ";
			}
			$sql_tag.="ct.id_tag='".$tag[$loop]."'";

			$_SESSION['cdt_extract_tag']['tag'][]=$tag[$loop];
		}
		$sql_tag.=")";
	}

	$_SESSION['cdt_extract_tag']['type_notice']=$type_notice;

	if(in_array("c", $type_notice)) {

		$cpt=0;
		$sql="SELECT DISTINCT ce.* FROM ct_entry ce, ct_tag ct WHERE ce.id_ct=ct.id_ct AND date_ct>='".$ts_debut."' AND date_ct<='".$ts_fin."'".$sql_id_groupe.$sql_tag.$sql_login_prof.$sql_id_classe." AND ct.type_ct='c' ORDER BY date_ct;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_array($res)) {
				if(($_SESSION['statut']!="professeur")||(in_array($lig['id_groupe'], $tab_mes_groupes))) {
					$tab_notices["c"][$cpt]=$lig;

					if($_SESSION['statut']=='professeur') {
						$tab_notices["c"][$cpt]['contenu']="<div style='float:right; width:16px;'><a href='../cahier_texte_2/affiche_notice.php?id_ct=".$lig['id_ct']."&type_notice=c' title=\"Voir la notice\" target='_blank'><img src='../images/icons/notices_CDT_compte_rendu.png' width='16' height='16' /></a></div>".$tab_notices["c"][$cpt]['contenu'];
					}

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

					if($_SESSION['statut']=='professeur') {
						$tab_notices["t"][$cpt]['contenu']="<div style='float:right; width:16px;'><a href='../cahier_texte_2/affiche_notice.php?id_ct=".$lig['id_ct']."&type_notice=t' title=\"Voir la notice\" target='_blank'><img src='../images/icons/notices_CDT_travail.png' width='16' height='16' /></a></div>".$tab_notices["t"][$cpt]['contenu'];
					}

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

					if($_SESSION['statut']=='professeur') {
						$tab_notices["p"][$cpt]['contenu']="<div style='float:right; width:16px;'><a href='../cahier_texte_2/affiche_notice.php?id_ct=".$lig['id_ct']."&type_notice=t' title=\"Voir la notice\" target='_blank'><img src='../images/icons/notices_CDT_privee.png' width='16' height='16' /></a></div>".$tab_notices["p"][$cpt]['contenu'];
					}

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
			$checked='';
			if((isset($_SESSION['cdt_extract_tag']['tag']))&&(in_array($tab_tag_type["indice"][$loop]["id"], $_SESSION['cdt_extract_tag']['tag']))) {
				$checked=' checked';
			}

			echo " 
	<input type='checkbox' name='tag[]' id='tag_".$loop."' value='".$tab_tag_type["indice"][$loop]["id"]."' onchange=\"checkbox_change('tag_".$loop."')\"".$checked." /><label for='tag_".$loop."' id='texte_tag_".$loop."'>".$tab_tag_type["indice"][$loop]["nom_tag"]."</label><br />";
		}

	// Checkbox des types de notices
	$checked_c='';
	if((isset($_SESSION['cdt_extract_tag']['type_notice']))&&(in_array('c', $_SESSION['cdt_extract_tag']['type_notice']))) {
		$checked_c=' checked';
	}
	$checked_t='';
	if((isset($_SESSION['cdt_extract_tag']['type_notice']))&&(in_array('t', $_SESSION['cdt_extract_tag']['type_notice']))) {
		$checked_t=' checked';
	}
	echo "<p style='text-indent:-3em; margin-left:3em;'>Se limiter aux notices du type suivant&nbsp;:<br />
	<input type='checkbox' name='type_notice[]' id='type_notice_c' value='c' onchange=\"checkbox_change('type_notice_c')\"".$checked_c." /><label for='type_notice_c' id='texte_type_notice_c'> compte-rendus de séance</label><br />
	<input type='checkbox' name='type_notice[]' id='type_notice_t' value='t' onchange=\"checkbox_change('type_notice_t')\"".$checked_t." /><label for='type_notice_t' id='texte_type_notice_t'> devoirs/travaux à la maison</label><br />";

	if($_SESSION['statut']=='professeur') {
		$checked='';
		if((isset($_SESSION['cdt_extract_tag']['type_notice']))&&(in_array('p', $_SESSION['cdt_extract_tag']['type_notice']))) {
			$checked=' checked';
		}

		echo "
	<input type='checkbox' name='type_notice[]' id='type_notice_p' value='p' onchange=\"checkbox_change('type_notice_p')\"".$checked." /><label for='type_notice_p' id='texte_type_notice_p'> notices privées</label><br />";

		// Checkbox des groupes
		$groups=get_groups_for_prof($_SESSION['login']);
		if(count($groups)>0) {
			echo "
	<p style='text-indent:-3em; margin-left:3em;'>Se limiter aux notices des enseignements suivants&nbsp;:<br />";
			for($loop=0;$loop<count($groups);$loop++) {
				$checked='';
				if((isset($_SESSION['cdt_extract_tag']['id_groupe']))&&(in_array($groups[$loop]['id'], $_SESSION['cdt_extract_tag']['id_groupe']))) {
					$checked=' checked';
				}

				echo "
	<input type='checkbox' name='id_groupe[]' id='id_groupe_".$loop."' value='".$groups[$loop]["id"]."' onchange=\"checkbox_change(this.id)\"".$checked." /><label for='id_groupe_".$loop."' id='texte_id_groupe_".$loop."'> ".$groups[$loop]["name"]." (".$groups[$loop]["description"].") en ".$groups[$loop]["classlist_string"]."</label><br />";
			}
		}

	}
	elseif(($_SESSION['statut']=='cpe')||($_SESSION['statut']=='scolarite')) {
		// A FAIRE : En plusieurs étapes, parce qu'on ne va pas proposer de choisir parmi tous les groupes de l'établissement.

		// Checkbox des classes
		echo "<p>Se limiter aux classes suivantes&nbsp;:</p><div style='margin-left:3em;'>";
		echo liste_checkbox_classes((isset($_SESSION['cdt_extract_tag']['id_classe']) ? $_SESSION['cdt_extract_tag']['id_classe'] : array()), 'id_classe', 'cocher_decocher_classes');
		echo "<p><a href=\"javascript:cocher_decocher_classes(true)\">Cocher</a> / <a href=\"javascript:cocher_decocher_classes(false)\">décocher</a> toutes les classes.</p>";
		echo "</div>";

		// Checkbox des profs
		echo "<p>Se limiter aux professeurs suivants&nbsp;:</p><div style='margin-left:3em;'>";
		echo liste_checkbox_utilisateurs(array("professeur"), (isset($_SESSION['cdt_extract_tag']['login_prof']) ? $_SESSION['cdt_extract_tag']['login_prof'] : array()), $nom_champ='login_prof', 'cocher_decocher_profs', "n");
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
	echo js_change_style_all_checkbox('y');

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

					if(isset($tab_tag_type["id"][$tab[$loop]["id_tag"][$loop2]]["drapeau"])) {
						$chaine_tags.="<img src='../".$tab_tag_type["id"][$tab[$loop]["id_tag"][$loop2]]["drapeau"]."' class='icone16' title=\"".$tab_tag_type["id"][$tab[$loop]["id_tag"][$loop2]]["nom_tag"]."\" />";
					}
					else {
						$chaine_tags.=$tab_tag_type["id"][$tab[$loop]["id_tag"][$loop2]]["nom_tag"];
					}
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
?>
