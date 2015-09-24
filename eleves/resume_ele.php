<?php
/*
 * $Id$
 *
 * Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

@set_time_limit(0);

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

$sql="SELECT 1=1 FROM droits WHERE id='/eleves/resume_ele.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/eleves/resume_ele.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='V',
autre='F',
description='Accueil élève résumé',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

require("../edt/edt_ics_lib.php");

$type_edt=isset($_POST['type_edt']) ? $_POST['type_edt'] : (isset($_GET['type_edt']) ? $_GET['type_edt'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : "");
$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : (isset($_GET['login_prof']) ? $_GET['login_prof'] : "");
$num_semaine_annee=isset($_POST['num_semaine_annee']) ? $_POST['num_semaine_annee'] : (isset($_GET['num_semaine_annee']) ? $_GET['num_semaine_annee'] : NULL);
//$affichage=isset($_POST['affichage']) ? $_POST['affichage'] : (isset($_GET['affichage']) ? $_GET['affichage'] : "semaine");
$affichage=isset($_POST['affichage']) ? $_POST['affichage'] : (isset($_GET['affichage']) ? $_GET['affichage'] : strftime("%u"));

$display_date=isset($_POST['display_date']) ? $_POST['display_date'] : (isset($_GET['display_date']) ? $_GET['display_date'] : NULL);

$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : (isset($_GET['login_eleve']) ? $_GET['login_eleve'] : NULL);

//===================================================
// Contrôler si le jour est dans la période de l'année scolaire courante
$ts_debut_annee=getSettingValue('begin_bookings');
$ts_fin_annee=getSettingValue('end_bookings');
//===================================================

//===================================================
if($affichage!="semaine") {
	if(!isset($display_date)) {
		if((isset($num_semaine_annee))&&(preg_match("/^[0-9]{1,}\|[0-9]{4}$/", $num_semaine_annee))) {
			$tmp_tab=explode("|", $num_semaine_annee);
			if(!isset($tmp_tab[1])) {
				$display_date=strftime("%d/%m/%Y");
				$affichage=strftime("%u");
			}
			else {
				$tmp_tab2=get_days_from_week_number($tmp_tab[0] ,$tmp_tab[1]);
				$display_date=$tmp_tab2['num_jour'][1]['jjmmaaaa'];
				$affichage=1;
			}
		}
		else {

			// Tester l'heure courante dans la journée
			$tab_horaire_etab=array();
			$sql="SELECT * FROM horaires_etablissement WHERE jour_horaire_etablissement='".strftime("%A")."' AND fermeture_horaire_etablissement>'".strftime("%H:%M:%S")."';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"],$sql);
			if(mysqli_num_rows($res)>0) {
				$display_date=strftime("%d/%m/%Y");
				$affichage=strftime("%u");
			}
			else {
				$ts_jour_suivant=time()+3600*24;
				$display_date=strftime("%d/%m/%Y", $ts_jour_suivant);
				$affichage=strftime("%u", $ts_jour_suivant);
			}
		}
	}
	elseif(!preg_match("#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#", $display_date)) {
		$msg.="Date $display_date invalide.<br />";
		unset($display_date);
		$display_date=strftime("%d/%m/%Y");
		$affichage=strftime("%u");
	}

	$tmp_tab=explode("/", $display_date);
	$ts_display_date=mktime(12, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
	$ts_debut_jour=mktime(0, 0, 0, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
	$ts_debut_jour_suivant=mktime(23, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2])+1;
	$num_semaine=strftime("%V", $ts_display_date);

	// DEBUG
	/*
	echo "<div style='margin-left:300px'>";
	echo "display_date=$display_date<br />";
	echo "affichage=$affichage<br />";
	echo "ts_display_date=$ts_display_date<br />";
	echo "</div>";
	*/

	$num_semaine_annee=$num_semaine."|".$tmp_tab[2];

	if($affichage=="jour") {
		$affichage=strftime("%u", $ts_display_date);
	}
	elseif($affichage!=strftime("%u", $ts_display_date)) {
		$msg.="Le jour choisi '$affichage' ne correspond pas à la date $display_date<br />";
		$affichage=strftime("%u", $ts_display_date);
	}

	$tab_jour=get_tab_jour_ouverture_etab();

	if(!in_array(strftime("%A", $ts_display_date), $tab_jour)) {
		// Jour suivant
		// Boucler sur 7 jours pour trouver le jour ouvré suivant
		// Il faudrait même chercher une date hors vacances
		$ts_display_date_suivante="";
		$display_date_suivante="";
		$display_date_suivante_num_jour="";
		$ts_test=$ts_display_date;
		$cpt=0;
		while(($cpt<7)&&($ts_test<$ts_fin_annee)) {
			$ts_test+=3600*24;
			if(in_array(strftime("%A", $ts_test), $tab_jour)) {
				$ts_display_date_suivante=$ts_test;
				$display_date_suivante=strftime("%d/%m/%Y", $ts_test);
				$display_date_suivante_num_jour=strftime("%u", $ts_test);
				break;
			}
			$cpt++;
		}
		if($display_date_suivante!="") {
			$ts_display_date=$ts_display_date_suivante;
			$display_date=$display_date_suivante;
			$affichage=$display_date_suivante_num_jour;

			$tmp_tab=explode("/", $display_date);
			$ts_display_date=mktime(12, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
			$ts_debut_jour=mktime(0, 0, 0, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
			$ts_debut_jour_suivant=mktime(23, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2])+1;
			$num_semaine=strftime("%V", $ts_display_date);

			$num_semaine_annee=$num_semaine."|".$tmp_tab[2];
		}
	}

	if($ts_display_date<$ts_debut_annee) {
		$msg.="Première date possible&nbsp;: Début de l'année scolaire.<br />";
		$ts_display_date=$ts_debut_annee;

		$display_date=strftime("%d/%m/%Y", $ts_display_date);
		$affichage=strftime("%u", $ts_display_date);

		$tmp_tab=explode("/", $display_date);
		$ts_debut_jour=mktime(0, 0, 0, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
		$ts_debut_jour_suivant=mktime(23, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2])+1;
		$num_semaine=strftime("%V", $ts_display_date);

		$num_semaine_annee=$num_semaine."|".$tmp_tab[2];
	}
	elseif($ts_display_date>$ts_fin_annee) {
		$msg.="Dernière date possible&nbsp;: Fin de l'année scolaire.<br />";
		$ts_display_date=$ts_fin_annee;

		$display_date=strftime("%d/%m/%Y", $ts_display_date);
		$affichage=strftime("%u", $ts_display_date);

		$tmp_tab=explode("/", $display_date);
		$ts_debut_jour=mktime(0, 0, 0, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
		$ts_debut_jour_suivant=mktime(23, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2])+1;
		$num_semaine=strftime("%V", $ts_display_date);

		$num_semaine_annee=$num_semaine."|".$tmp_tab[2];
	}
	// DEBUG
	/*
	echo "<div style='margin-left:300px'>";
	echo "ts_display_date=$ts_display_date<br />";
	echo "display_date=$display_date<br />";
	echo "affichage=$affichage<br />";
	echo "</div>";
	*/
}
//===================================================
if((!isset($num_semaine_annee))||($num_semaine_annee=="")||(!preg_match("/[0-9]{2}\|[0-9]{4}/", $num_semaine_annee))) {
	$num_semaine_annee="36|".((strftime("%m")>7) ? strftime("%Y") : (strftime("%Y")-1));
}
// DEBUG
/*
echo "<div style='margin-left:300px'>";
echo "num_semaine_annee=$num_semaine_annee<br />";
echo "</div>";
*/
//===================================================
// Ca ne devrait pas être le cas sur cette page
if($affichage=="semaine") {
	$tmp_tab=explode("|", $num_semaine_annee);
	$num_semaine=$tmp_tab[0];
	$annee=$tmp_tab[1];
	$jours=get_days_from_week_number($num_semaine, $annee);

	$ts_display_date=$jours['num_jour'][1]['timestamp'];
}
//===================================================

//===================================================
// Filtrage/contrôle de l'id_classe dans le cas élève/responsable
if($_SESSION['statut']=="eleve") {
	$login_eleve=$_SESSION['login'];
}
elseif($_SESSION['statut']=="responsable") {
	$tab_ele2=array();
	$tab_ele=get_enfants_from_resp_login($_SESSION['login'], 'simple');
	for($loop=0;$loop<count($tab_ele);$loop+=2) {
		$tab_ele2[]=$tab_ele[$loop];
	}

	if(count($tab_ele2)==0) {
		header("Location: ../accueil.php?accueil_simpl=n&msg=Aucun élève trouvé");
		die();
	}

	if((!isset($login_eleve))||(!in_array($login_eleve, $tab_ele2))) {
		$login_eleve=$tab_ele2[0];
	}
	// Il faudra proposer le choix si count($tab_ele2)>1

	$login_ele_prec="";
	$login_ele_suiv="";
	$nom_prenom_ele_prec="";
	$nom_prenom_ele_suiv="";
	$login_ele_trouve=0;
	for($loop=0;$loop<count($tab_ele);$loop+=2) {
		if(($tab_ele[$loop]!=$login_eleve)&&($login_ele_trouve==0)) {
			$login_ele_prec=$tab_ele[$loop];
			$nom_prenom_ele_prec=$tab_ele[$loop+1];
		}
		elseif($tab_ele[$loop]==$login_eleve) {
			$login_ele_trouve++;
		}
		elseif($login_ele_trouve==1) {
			$login_ele_suiv=$tab_ele[$loop];
			$nom_prenom_ele_suiv=$tab_ele[$loop+1];
			$login_ele_trouve++;
		}
	}
}
else {
	if(!isset($login_eleve)) {
		header("Location: ../accueil.php?msg=Elève non choisi");
		die();
	}
}

$info_eleve=get_nom_prenom_eleve($login_eleve, "avec_classe");
$id_classe=get_id_classe_ele_d_apres_date($login_eleve, $ts_display_date);
if($id_classe=="") {
	$id_classe=get_id_classe_derniere_classe_ele($login_eleve);
}
//===================================================

//===================================================
if((isset($_GET['mode']))&&($_GET['mode']=="update_div_cdt")&&(isset($_GET['id_cours']))&&(isset($login_eleve))) {
	$class_notice_dev_fait="color_fond_notices_t_fait";
	$class_notice_dev_non_fait="";

	$CDTPeutPointerTravailFait=getSettingAOui('CDTPeutPointerTravailFait'.ucfirst($_SESSION['statut']));
	if($CDTPeutPointerTravailFait) {
		$tab_etat_travail_fait=get_tab_etat_travail_fait($login_eleve);
		echo js_cdt_modif_etat_travail();
	}

	if(!preg_match("/^[0-9]{1,}$/", $_GET['id_cours'])) {
		// Variables requises via global: $ts_debut_jour, $ts_debut_jour_suivant, $ts_display_date, $display_date, $tab_group_edt, $tab_couleur_matiere, $CDTPeutPointerTravailFait;
		echo travaux_a_faire_cdt_jour($login_eleve, $id_classe);
	}
	else {
		echo travaux_a_faire_cdt_cours($_GET['id_cours'], $login_eleve, $id_classe);
	}

	die();
}
//===================================================

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

//**************** EN-TETE *****************
//if($mode!="afficher_edt") {
	$titre_page = "En résumé : ".$info_eleve;
//}
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

// A FAIRE : UTILISER $_SESSION['resume_ele_display_date'] pour le changement d'élève... ou lors du choix de date, renseigner un champ pris en compte dans le changement d'élève
if(isset($display_date)) {
	$_SESSION['resume_ele_display_date']=$display_date;
}

// DEBUG
/*
echo "<div style='margin-left:300px'>";
echo "ts_display_date=$ts_display_date<br />";
echo "display_date=$display_date<br />";
echo "affichage=$affichage<br />";
echo "</div>";
*/
//=================================
// Contenu EDT
$x0=35;
//$y0=2;
$y0=2;
$largeur_edt=114;
$hauteur_une_heure=60;
//$html=affiche_edt2_eleve($login_eleve, $id_classe, $ts_display_date, $affichage, $x0, $y0, $largeur_edt, $hauteur_une_heure);
$type_affichage="eleve";
$login_prof="";
$html=affiche_edt2($login_eleve, $id_classe, $login_prof, $type_affichage, $ts_display_date, $affichage, $x0, $y0, $largeur_edt, $hauteur_une_heure);

// On récupère de la fonction $hauteur_jour, $hauteur_entete, $tab_group_edt
//=================================

//=================================
// Tailles de polices
$font_size=ceil($hauteur_une_heure/5);
$font_size2=ceil($hauteur_une_heure/8);
$font_size3=ceil($hauteur_une_heure/10);
//=================================

//=================================
//echo "<div style='clear:both'></div>";
//echo "<div style='float:left; width:120px; margin:0.5em;'>$html</div>";
$x1=5;
$y1=125;
$marge_droite=5;
$largeur1=$largeur_edt+2*35;
$hauteur1=$hauteur_jour+$hauteur_entete+6;
$hauteur_div_sous_bandeau=20;
// class='infobulle_corps'
//=================================

//=================================
// Cadre EDT ou choix de la date à gauche
if(((($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable'))&&((getSettingAOui('autorise_edt_eleve'))||(getSettingAOui('autorise_edt2_eleve'))))||
((in_array($_SESSION['statut'], array('professeur', 'cpe', 'scolarite')))&&(getSettingAOui('autorise_edt_tous')))||
(($_SESSION['statut']=='administrateur')&&(getSettingAOui('autorise_edt_admin')))) {
	$affichage_div_edt="y";

	// Cadre Choix élève sous le bandeau d'entête
	echo "
<div id='div_sous_bandeau' style='float:left; width:100%; height:".$hauteur_div_sous_bandeau."px; text-align:center;'>
	<!--
		Cadre vide pour conserver l'espace au-dessus 
		Il faut pouvoir adapter la hauteur en fonction de la réduction ou non du bandeau d'entête
	-->
	<p class='bold'>";

	if((isset($login_ele_prec))&&($login_ele_prec!="")) {
		echo "<a href='".$_SERVER['PHP_SELF']."?login_eleve=".$login_ele_prec."&amp;display_date=$display_date&amp;affichage=jour' title=\"Voir la page pour $nom_prenom_ele_prec\"><img src=\"../images/arrow_left.png\" class='icone16' alt=\"$nom_prenom_ele_prec\" /></a> ";
	}

	echo $info_eleve;

	if((isset($login_ele_suiv))&&($login_ele_suiv!="")) {
		echo " <a href='".$_SERVER['PHP_SELF']."?login_eleve=".$login_ele_suiv."&amp;display_date=$display_date&amp;affichage=jour' title=\"Voir la page pour $nom_prenom_ele_suiv\"><img src=\"../images/arrow_right.png\" class='icone16' alt=\"$nom_prenom_ele_suiv\" /></a>";
	}

	echo "</p>
</div>";

	// Cadre EDT à gauche
	echo "
<div style='float:left; width:".$largeur1."px; height:".$hauteur1."px; margin-right:".$marge_droite."px;'>
	<!-- Cadre vide pour conserver l'espace à gauche -->
</div>

<div id='div_edt' style='position:absolute; top:".$y1."px; left:".$x1."px; width:".$largeur1."px; height:".$hauteur1."px; margin-right:".$marge_droite."px; margin-bottom:".$marge_droite."px; border:1px solid black; background-color:".$tab_couleur_onglet['edt'].";'>
	".$html."
	<div style='width:16px; margin:2px;' title=\"Voir l'emploi du temps de la semaine\"><a href='../edt/index2.php?login_eleve=".$login_eleve."&amp;affichage=semaine&amp;type_affichage=eleve&amp;num_semaine_annee=$num_semaine_annee'><img src='../images/icons/edt.png' class='icone16' alt='EDT' /></a></div>
</div>";
/*
	echo "
<div style='float:left; width:".$largeur1."px; height:".$hauteur1."px; margin-right:".$marge_droite."px;'>";
if(isset($tab_ele)) {
	for($loop=0;$loop<count($tab_ele);$loop+=2) {
		echo "\$tab_ele[$loop]=".$tab_ele[$loop]."<br />";
	}
}
if(isset($login_ele_prec)) {
	echo "\$login_ele_prec=$login_ele_prec<br />";
}
if(isset($login_ele_suiv)) {
	echo "\$login_ele_suiv=$login_ele_suiv<br />";
}
echo "</div>";
*/
}
else {
	$affichage_div_edt="n";
	echo "
<div style='float:left; width:8em;'>
	<form id='form_chgt_date' action='".$_SERVER['PHP_SELF']."' method='post'>
		<input type='hidden' name='login_eleve' value='$login_eleve' />
		<input type='hidden' name='affichage' value='jour' />
		<input type='text' name='display_date' id='display_date' value='$display_date' size='8' onchange=\"document.getElementById('form_chgt_date').submit();\" onBlur=\"document.getElementById('form_chgt_date').submit();\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" title=\"Vous pouvez taper la date manuellement.

Vous pouvez aussi, utiliser les flèches Haut Bas du pavé de direction de votre clavier pour passer au jour précédent/suivant (la validation du choix se fait en cliquant ensuite hors du champ de formulaire de saisie de la date).

Enfin, vous pouvez sélectionner la date en cliquant sur l'image Calendrier.\" />
		".img_calendrier_js("display_date", "img_bouton_display_date")."
	</form>
</div>";

	// Cadre Choix élève sous le bandeau d'entête
	echo "
<div id='div_sous_bandeau' style='float:left; width:80%; height:".$hauteur_div_sous_bandeau."px; text-align:center;'>
	<!--
		Cadre vide pour conserver l'espace au-dessus 
		Il faut pouvoir adapter la hauteur en fonction de la réduction ou non du bandeau d'entête
	-->
	<p class='bold'>";

	if((isset($login_ele_prec))&&($login_ele_prec!="")) {
		echo "<a href='".$_SERVER['PHP_SELF']."?login_eleve=".$login_ele_prec."&amp;display_date=$display_date&amp;affichage=jour' title=\"Voir la page pour $nom_prenom_ele_prec\"><img src=\"../images/arrow_left.png\" class='icone16' alt=\"$nom_prenom_ele_prec\" /></a> ";
	}

	echo $info_eleve;

	if((isset($login_ele_suiv))&&($login_ele_suiv!="")) {
		echo " <a href='".$_SERVER['PHP_SELF']."?login_eleve=".$login_ele_suiv."&amp;display_date=$display_date&amp;affichage=jour' title=\"Voir la page pour $nom_prenom_ele_suiv\"><img src=\"../images/arrow_right.png\" class='icone16' alt=\"$nom_prenom_ele_suiv\" /></a>";
	}

	echo "</p>

	<!-- Proposer le choix de l'élève pour un parent (s'il y a plusieurs élèves) -->

</div>";

}
//<div style='clear:both'></div>

//=================================
$x_courant=$x1+$largeur1+$marge_droite;
//=================================

//=================================
// Cadre dernières notes
if((getSettingAOui('active_carnets_notes'))&&(acces_carnet_notes($_SESSION['statut']))) {
	$largeur_cn=200;

	// Afficher au plus 10 notes
	$nb_max_dev=10;

	$acces_moyenne_chaque_devoir_carnet_notes=acces_moyenne_chaque_devoir_carnet_notes($_SESSION['statut']);
	$acces_moyenne_min_max_chaque_devoir_carnet_notes=acces_moyenne_min_max_chaque_devoir_carnet_notes($_SESSION['statut']);

	if($_SESSION['statut']=='responsable') {
		$url_cn="../cahier_notes/visu_releve_notes_ter.php";
	}
	elseif($_SESSION['statut']=='eleve') {
		$url_cn="../cahier_notes/visu_releve_notes_ter.php";
	}
	elseif($_SESSION['statut']=='professeur') {
		$url_cn="../cahier_notes/visu_releve_notes_bis.php";
	}
	elseif($_SESSION['statut']=='scolarite') {
		$url_cn="../cahier_notes/visu_releve_notes_bis.php";
	}
	elseif($_SESSION['statut']=='cpe') {
		$url_cn="../cahier_notes/visu_releve_notes_bis.php";
	}

	$html="";
	if($url_cn!="") {
		$html.="<div style='float:right; width:4em; font-size:x-small; text-align:right; margin: 3px;'><a href='".$url_cn."' title=\"Consulter le relevé de notes\"><img src='../images/icons/chercher.png' class='icone16' alt='Tout voir' /></a></div>";
	}
	//$html.="<h3>Dernières notes</h3>";
	$html.="<div style='font-weight:bold; font-size: large;' class='fieldset_opacite50'>Dernières notes</div>";

	// A VOIR : resp_legal=0 et droits

	/*
	// A REVOIR : La partie
				cd.display_parents='1' AND 
				cd.date_ele_resp<='".$date_courante."' AND 
			  pourrait ne pas être mise pour les statuts non élève/resp... tout dépend de l'usage qui est fait de cette page.
			  Si on imprime la page pour donner aux parents, sans ces parties de la requête, on peut donner une info non souhaitée par le prof
	*/
	
	$date_courante=strftime("%Y-%m-%d %H:%M:%S");
	$sql="SELECT * FROM cn_notes_devoirs cnd, 
				cn_devoirs cd, 
				cn_cahier_notes ccn
			WHERE cnd.id_devoir=cd.id AND 
				cd.display_parents='1' AND 
				cd.date_ele_resp<='".$date_courante."' AND 
				cnd.login='".$login_eleve."' AND 
				cd.id_racine=ccn.id_cahier_notes AND 
				cnd.statut!='v'
			ORDER BY ccn.periode DESC, cd.date DESC LIMIT $nb_max_dev;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		$html.="Aucune note n'est saisie.";
	}
	else {
		while($lig=mysqli_fetch_object($res)) {
			// Nom de matière,...
			if(!isset($tab_group_edt[$lig->id_groupe])) {
				$tab_group_edt[$lig->id_groupe]=get_group($lig->id_groupe, array('matieres', 'classes', 'profs'));
			}
			$current_matiere_cn=$tab_group_edt[$lig->id_groupe]['matiere']['nom_complet'];

			if(!isset($tab_couleur_matiere[$tab_group_edt[$lig->id_groupe]['matiere']['matiere']])) {
				$tab_couleur_matiere[$tab_group_edt[$lig->id_groupe]['matiere']['matiere']]=get_couleur_edt_matiere($tab_group_edt[$lig->id_groupe]['matiere']['matiere']);
			}

			if($lig->statut!="") {
				$info_note=$lig->statut;
			}
			else {
				$info_note="<strong>".$lig->note."</strong>";
				if($lig->note_sur!=20) {$info_note.="/".$lig->note_sur;}
			}

			// Moyenne de la classe
			$info_moy_classe="";
			$moy_classe="";
			$moy_min_classe="";
			$moy_max_classe="";
			if($acces_moyenne_chaque_devoir_carnet_notes||$acces_moyenne_min_max_chaque_devoir_carnet_notes) {
				// Vérifier qu'il y a au moins une note
				$sql="SELECT 1=1 FROM cn_notes_devoirs WHERE id_devoir='".$lig->id_devoir."' and statut='';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$sql="SELECT ROUND(AVG(note),1) AS moy FROM cn_notes_devoirs WHERE id_devoir='".$lig->id_devoir."' and statut='';";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res2)>0) {
						$lig2=mysqli_fetch_object($res2);
						$moy_classe=$lig2->moy;
						$info_moy_classe="Moy.classe&nbsp;: ".$moy_classe;
						if($lig->note_sur!=20) {$info_moy_classe.="/".$lig->note_sur;}

						$info_moy_classe="<br />
		<span style='font-size:".$font_size3."pt'>$info_moy_classe</span>";
					}

					if($acces_moyenne_min_max_chaque_devoir_carnet_notes) {
						$sql="SELECT MIN(note) as note_min FROM cn_notes_devoirs WHERE id_devoir='".$lig->id_devoir."' and statut='';";
						$res2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res2)>0) {
							$lig2=mysqli_fetch_object($res2);
							$moy_min_classe=$lig2->note_min;
						}

						$sql="SELECT MAX(note) as note_max FROM cn_notes_devoirs WHERE id_devoir='".$lig->id_devoir."' and statut='';";
						$res2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res2)>0) {
							$lig2=mysqli_fetch_object($res2);
							$moy_max_classe=$lig2->note_max;
						}
					}
				}
			}

			$info_devoir=$tab_group_edt[$lig->id_groupe]['name']." (".$tab_group_edt[$lig->id_groupe]['description'].") en ".$tab_group_edt[$lig->id_groupe]['classlist_string']." (".$tab_group_edt[$lig->id_groupe]['profs']['proflist_string'].")
Évaluation : ".$lig->nom_court;
			if(($lig->nom_complet!="")&&($lig->nom_complet!="Nouvelle évaluation")&&($lig->nom_complet!=$lig->nom_court)) {
				$info_devoir.=" (".$lig->nom_complet.")";
			}
			if($lig->description!="") {
				$info_devoir.="\n".$lig->description;
			}
			$info_devoir.="\nCoefficient : ".$lig->coef;
			if(acces_moyenne_min_max_chaque_devoir_carnet_notes($_SESSION['statut'])) {
				$info_devoir.="\nMoy.classe : ".$moy_classe;
				if($lig->note_sur!=20) {$info_devoir.="/".$lig->note_sur;}

				if($acces_moyenne_min_max_chaque_devoir_carnet_notes) {
					$info_devoir.="\nMoy.min     : ".$moy_min_classe;
					if($lig->note_sur!=20) {$info_devoir.="/".$lig->note_sur;}

					$info_devoir.="\nMoy.max    : ".$moy_max_classe;
					if($lig->note_sur!=20) {$info_devoir.="/".$lig->note_sur;}
				}
			}
			elseif(acces_moyenne_chaque_devoir_carnet_notes($_SESSION['statut'])) {
				$info_devoir.="\nMoy.classe : ".$moy_classe;
				if($lig->note_sur!=20) {$info_devoir.="/".$lig->note_sur;}
			}

			$info_devoir.="\nCette note concerne la période ".$lig->periode;

			$html.="
	<div id='div_cn' style='float:left;width:99%; border-bottom: 1px solid black; margin-right:3px; margin-top:3px; margin-bottom:3px; background-color:".$tab_couleur_matiere[$tab_group_edt[$lig->id_groupe]['matiere']['matiere']].";' class='fieldset_opacite50' title=\"$info_devoir\">
		<div style='float:right; text-align:right; width:4em;'>
			".$info_note."
		</div>

		<strong>$current_matiere_cn</strong><br />
		<span style='font-size:".$font_size2."pt'>".formate_date($lig->date)."</span>".$info_moy_classe."
	</div>";
		}
	}

	// class='infobulle_corps'
	echo "
<div style='float:left; width:".$largeur_cn."px; min-height:".($y1+5)."px; margin-right:".$marge_droite."px; margin-bottom:".$marge_droite."px; border:1px solid black; padding: 5px; background-color:".$tab_couleur_onglet['releves'].";'>".$html."</div>";

	$x_courant+=$largeur_cn;
}
//=================================

//=================================
// Cadre CDT
if((getSettingAOui('active_cahiers_texte'))&&(acces_cdt_eleve($_SESSION['login'], $login_eleve))) {
	$largeur_cdt=300;

	$class_notice_dev_fait="color_fond_notices_t_fait";
	$class_notice_dev_non_fait="";

	$CDTPeutPointerTravailFait=getSettingAOui('CDTPeutPointerTravailFait'.ucfirst($_SESSION['statut']));
	if($CDTPeutPointerTravailFait) {
		$tab_etat_travail_fait=get_tab_etat_travail_fait($login_eleve);
		echo js_cdt_modif_etat_travail();
	}

	$html="";

	$html.="
<style type='text/css'>
	.color_fond_notices_t_fait {
		background-color:grey;
	}
</style>";

	$html.="<div id='div_cdt_contenu'>";
	$html.=travaux_a_faire_cdt_jour($login_eleve, $id_classe);
	$html.="</div>";

	echo "
<div id='div_cdt' style='float:left; width:".$largeur_cdt."px; min-height:".($y1+5)."px; margin-right:".$marge_droite."px; margin-bottom:".$marge_droite."px; border:1px solid black; padding: 5px; background-color:".$tab_couleur_onglet['cdt'].";' title=\"Dans ce cadre, les travaux à faire pour la date choisie sont affichés.\nPour consulter les comptes-rendus de séance, cliquez sur la loupe en haut à droite dans ce cadre CDT.\">".$html."</div>";
}
//=================================

//=================================
// Cadre Absences
if((getSettingValue('active_module_absence')==2)&&(acces_abs_eleve($_SESSION['login'], $_SESSION['statut'], $login_eleve))) {
	$largeur_abs=300;

	$html="";

	// Actuellement, l'élève n'a pas l'affichage absences
	$url_abs="";
	//if(($_SESSION['statut']=="eleve")||($_SESSION['statut']=="responsable")) {
	if($_SESSION['statut']=="responsable") {
		$url_abs="../mod_abs2/bilan_parent.php?ele_login=".$login_eleve;
	}
	else {
		$url_abs="../eleves/visu_eleve.php?ele_login=".$login_eleve."&onglet=absences";
	}

	if($url_abs!="") {
		$html="<div style='float:right; width:4em; font-size:x-small; text-align:right; margin: 3px;'><a href='$url_abs' title=\"Consulter le module Absences\"><img src='../images/icons/chercher.png' class='icone16' alt='Tout voir' /></a></div>";
	}

	$html.="<div style='font-weight:bold; font-size: large;' class='fieldset_opacite50'>Absences</div>";

	$html.="<p style='color:red'>Extraction des absences, dans cette page, non encore implémentée.<br />Passez par le menu 'Accueil'";
	if($url_abs!="") {
		$html.="<br />ou par le lien <a href='$url_abs' title=\"Consulter le module Absences\"><img src='../images/icons/chercher.png' class='icone16' alt='Tout voir' /></a>";
	}
	$html.="<br />L'objectif à terme est d'afficher ici, juste les absences/retards du jour sélectionné... ou de la semaine en cours... ou des 7 derniers jours.";
	$html.="</p>";

	echo "
<div id='div_abs' style='float:left; width:".$largeur_abs."px; min-height:".($y1+5)."px; margin-right:".$marge_droite."px; margin-bottom:".$marge_droite."px; border:1px solid black; padding: 5px; background-color:".$tab_couleur_onglet['absences'].";'>".$html."</div>";

}
//=================================

//=================================
// Cadre Discipline
if((getSettingAOui('active_mod_discipline'))&&(acces_incidents_disc_eleve($_SESSION['login'], $_SESSION['statut'], $login_eleve))) {
	$largeur_disc=300;

	$html="";

	if($_SESSION['statut']=="eleve") {
		$url_disc="../mod_discipline/visu_disc.php";
	}
	elseif($_SESSION['statut']=="responsable") {
		$url_disc="../mod_discipline/visu_disc.php?ele_login=".$login_eleve;
	}
	else {
		$url_disc="../mod_discipline/traiter_incident.php?protagoniste_incident=".$login_eleve;
	}

	$html="<div style='float:right; width:4em; font-size:x-small; text-align:right; margin: 3px;'><a href='$url_disc' title=\"Consulter le module Discipline\"><img src='../images/icons/chercher.png' class='icone16' alt='Tout voir' /></a></div>";
	$html.="<div style='font-weight:bold; font-size: large;' class='fieldset_opacite50'>Discipline</div>";

	require_once("../mod_discipline/sanctions_func_lib.php");

	$tableau_des_avertissements_de_fin_de_periode_eleve_de_cet_eleve=tableau_des_avertissements_de_fin_de_periode_eleve($login_eleve);
	if($tableau_des_avertissements_de_fin_de_periode_eleve_de_cet_eleve!='') {
		$html.=$tableau_des_avertissements_de_fin_de_periode_eleve_de_cet_eleve;
	}

	//$html.="<p style='color:red'>Extraction des incidents et sanctions non encore implémentée.<br />Passez par le menu 'Accueil'</p>";



	if((getSettingAOui('active_mod_disc_pointage'))&&
	((($_SESSION['statut']=='eleve')&&((getSettingAOui('disc_pointage_aff_totaux_ele'))||(getSettingAOui('disc_pointage_acces_totaux_ele'))))||
	(($_SESSION['statut']=='responsable')&&((getSettingAOui('disc_pointage_aff_totaux_resp'))||(getSettingAOui('disc_pointage_acces_totaux_resp')))))) {
		$pointages_ele_courant=retourne_tab_html_pointages_disc($login_eleve);
		if($pointages_ele_courant!="") {
			$html.=$pointages_ele_courant;
		}
	}



	$mode="";
	$disc_date_debut="";
	$disc_date_fin="";
	$titre_infobulle=$mod_disc_terme_incident."s, mesures et ".$mod_disc_terme_sanction."s";
	$texte_infobulle=tab_mod_discipline($login_eleve,$mode,$disc_date_debut,$disc_date_fin);
	$tabdiv_infobulle[]=creer_div_infobulle('div_disc_infobulle',$titre_infobulle,"",$texte_infobulle,"",65,0,'y','y','n','n');

	//$html.="<p style='font-weight: bold;'>Totaux des ".$mod_disc_terme_incident."s/mesures/".$mod_disc_terme_sanction."s en tant que Responsable.</p>\n";
	$html.="<p style='font-weight: bold; margin-top:1em;'>".ucfirst($mod_disc_terme_incident)."s</p>\n";
	if(count($tab_incidents_ele[$login_eleve])>0) {
		$html.="<table class='boireaus' border='1' summary='Totaux ".$mod_disc_terme_incident."s'>\n";
		$html.="<tr><th>Nature</th><th>Total</th></tr>\n";
		$alt=1;
		foreach($tab_incidents_ele[$login_eleve] as $key => $value) {
			$alt=$alt*(-1);
			$html.="<tr class='lig$alt'><td>".stripslashes($key)."</td><td>".stripslashes($value)."</td></tr>\n";
		}
		$html.="</table>\n";
	}
	else {
		$html.="<p>Aucun ".$mod_disc_terme_incident." relevé en qualité de responsable.</p>\n";
	}

	//$html.="<p style='font-weight: bold; margin-top:1em;'>Mesures prises</p>\n";
	if(count($tab_mesures_ele[$login_eleve])>0) {
		$html.="<p style='font-weight: bold; margin-top:1em;'>Mesures prises</p>\n";
		$html.="<table class='boireaus' border='1' summary='Totaux mesures prises'>\n";
		$html.="<tr><th>Mesure</th><th>Total</th></tr>\n";
		$alt=1;
		foreach($tab_mesures_ele[$login_eleve] as $key => $value) {
			$alt=$alt*(-1);
			$html.="<tr class='lig$alt'><td>".stripslashes($key)."</td><td>".stripslashes($value)."</td></tr>\n";
		}
		$html.="</table>\n";
	}
	else {
		$html.="<p>Aucune mesure prise en qualité de responsable.</p>\n";
	}

	$html.="<p style='font-weight: bold; margin-top:1em;'>".ucfirst($mod_disc_terme_sanction)."s</p>\n";
	if(count($tab_sanctions_ele[$login_eleve])>0) {
		$html.="<table class='boireaus' border='1' summary='Totaux ".$mod_disc_terme_sanction."s'>\n";
		$html.="<tr><th>Nature</th><th>Total</th><th>Non<br />effectué(e)</th></tr>\n";
		$alt=1;
		foreach($tab_sanctions_ele[$login_eleve] as $key => $tab) {
			$alt=$alt*(-1);
			$html.="<tr class='lig$alt'><td>".stripslashes($key)."</td><td>".stripslashes($tab['total'])."</td><td>".count($tab['non_effectuee'])."</td></tr>\n";
		}
		$html.="</table>\n";
	}
	else {
		$html.="<p>Aucun(e) ".$mod_disc_terme_sanction." en qualité de responsable.</p>\n";
	}

	$html.="<p style='margin-top:1em;'><a href='#' onclick=\"afficher_div('div_disc_infobulle','y',-100,20);\">Voir en détail</a></p>";
	/*
	echo "<pre>";
	print_r($tab_incidents_ele);
	echo "</pre>";
	*/
	echo "
<div id='div_disc' style='float:left; width:".$largeur_disc."px; min-height:".($y1+5)."px; margin-right:".$marge_droite."px; margin-bottom:".$marge_droite."px; border:1px solid black; padding: 5px; background-color:".$tab_couleur_onglet['discipline'].";'>".$html."</div>";

}
//=================================

//=================================
// Cadre Agenda... ou juste messages
// Panneau d'affichage
if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
	// On se limite aux parents et élèves parce que affichage_des_messages.inc.php récupère les message pour $_SESSION['login']
	$largeur_panneau_aff=300;

	$html="";

	$html.="<div style='font-weight:bold; font-size: large;' class='fieldset_opacite50'>Panneau d'affichage</div>";

	include("../affichage_des_messages.inc.php");

	$liste_evenements=afficher_les_evenements();

	$message_remplacements="";
	if((getSettingAOui('active_mod_abs_prof'))&&
		($_SESSION['statut']=="eleve")) {
		$message_remplacements=affiche_remplacements_eleve($_SESSION['login']);
	}
	if((getSettingAOui('active_mod_abs_prof'))&&
		($_SESSION['statut']=="responsable")) {

		$message_remplacements="";
		$tab_eleves_en_responsabilite=get_enfants_from_resp_login($_SESSION['login'], 'avec_classe', "yy");
		for($loop=0;$loop<count($tab_eleves_en_responsabilite);$loop+=2) {
			$tmp_remplacements=affiche_remplacements_eleve($tab_eleves_en_responsabilite[$loop]);
			if($tmp_remplacements!="") {
				$message_remplacements.="<p class='bold'>".$tab_eleves_en_responsabilite[$loop+1]."</p>".$tmp_remplacements;
			}
		}
	}

	if(($texte_messages_resume_ele!="")||($liste_evenements!="")||($message_remplacements!="")) {
		$html.=$texte_messages_resume_ele;
		$html.=$liste_evenements;
		$html.=$message_remplacements;
	}
	else {
		$html.="Aucun message actuellement.";
	}

	echo "
<div id='div_panneau_aff' style='float:left; width:".$largeur_panneau_aff."px; min-height:".($y1+5)."px; margin-right:".$marge_droite."px; margin-bottom:".$marge_droite."px; border:1px solid black; padding: 5px;' class='infobulle_corps'>".$html."</div>";
}
//=================================

//=================================
// Cadre Liens? Ou faire un message pour les liens vers le site du collège,...
//=================================
if($affichage_div_edt=="y") {
	$largeur_indications=300;

	$html="";

	$html="<div style='float:right; width:15px; font-size:x-small; text-align:right; margin: 3px;'><img src='../images/icons/ico_ampoule.png' width='15' height='25' alt='Aide/Astuce' /></div>";

	$html.="<div style='font-weight:bold; font-size: large; min-height:28px;' class='fieldset_opacite50'>Aide</div>";

	$html.="<p>Quelques indications sur la présente page&nbsp;:</p>
<ul>
	<li><p>Vous pouvez afficher les travaux à faire dans les jours qui viennent (<em>et pas juste pour le jour choisi</em>) dans telle matière en cliquant sur la matière correspondante dans l'emploi du temps.</p></li>
	<li><p>Les images <img src='../images/icons/chercher.png' class='icone16' alt='Tout voir' /> en haut à droite dans les cadres affichés permettent d'accéder au module complet.</p></li>";
	if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
		$html.="
	<li><p>Si vous préférez ne pas utiliser cette page comme page d'accueil,<br />si vous préférez le menu classique, vous pouvez paramétrer ce choix dans <a href=''><img src='../images/icons/buddy.png' class='icone16' alt='Mon compte' /> Gérer mon compte</a></p></li>";
	}
	$html.="
</ul>";

	echo "
<div id='div_indications' style='float:left; width:".$largeur_indications."px; min-height:".($y1+5)."px; margin-right:".$marge_droite."px; margin-bottom:".$marge_droite."px; border:1px solid black; padding: 5px; background-color:".$tab_couleur_onglet['indication'].";'>".$html."</div>";
}


if($_SESSION['statut']=='responsable') {
	$largeur_infos_resp=500;

	$html="";

	$html.="<div style='font-weight:bold; font-size: large;' class='fieldset_opacite50'>Informations personnelles</div>
<p class='bold' style='margin-top:2em;'>Voici les informations vous concernant personnellement&nbsp;:</p>
".affiche_tableau_infos_resp($_SESSION['login'])."

<p style='margin-top:1em; text-align:justify;'>Si certaines informations, coordonnées sont erronées, pensez à alerter l'Administration de l'établissement pour qu'elles soient corrigées.<br />
Une absence de correction de votre adresse par exemple pourrait avoir des conséquences sur la réception de courriers de l'établissement (<em>bulletins,...</em>).</p>

<p class='bold' style='margin-top:2em;'>Enfants/élèves dont vous êtes responsable légal&nbsp;:</p>
".affiche_tableau_infos_eleves_associes_au_resp("", $_SESSION['login'])."
</div>";

	echo "
<div id='div_infos_resp' style='float:left; width:".$largeur_infos_resp."px; min-height:".($y1+5)."px; margin-right:".$marge_droite."px; margin-bottom:".$marge_droite."px; border:1px solid black; padding: 5px; background-color:aliceblue' class='infobulle_corps'>".$html."</div>";
}

//=================================
echo "<script type='text/javascript'>
	// Action lancée lors du clic dans le div_edt
	function action_edt_cours(id_cours) {
		//alert(id_cours);
		new Ajax.Updater($('div_cdt_contenu'),'".$_SERVER['PHP_SELF']."?login_eleve=".$login_eleve."&id_cours='+id_cours+'&mode=update_div_cdt&id_classe=".$id_classe."&display_date=".$display_date."',{method: 'get'});
		//afficher_div('edt_classe','y',-20,20);

	}

	// Adaptation de l'emplacement vertical du div_edt
	function check_top_div_edt() {
		var temoin_petit_bandeau=0;
		var liste_pt_bandeau=document.getElementsByClassName('pt_bandeau');
		for(i=0;i<liste_pt_bandeau.length;i++) {
			id=liste_pt_bandeau[i].getAttribute('id');
			if(id=='bandeau') {
				document.getElementById('div_edt').style.top=62+'px';
				temoin_petit_bandeau++;
				break;
			}
		}
		if(temoin_petit_bandeau==0) {
			document.getElementById('div_edt').style.top=124+'px';
		}

		setTimeout('check_top_div_edt()',1000);
	}

	check_top_div_edt();
</script>";
//=================================

echo "<div style='clear:both'></div>";

//debug_var();

require("../lib/footer.inc.php");
?>
