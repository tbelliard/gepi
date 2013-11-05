<?php
/*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

//include "../lib/mincals.inc";


unset($day);
$day = isset($_POST["day"]) ? $_POST["day"] : (isset($_GET["day"]) ? $_GET["day"] : date("d"));
unset($month);
$month = isset($_POST["month"]) ? $_POST["month"] : (isset($_GET["month"]) ? $_GET["month"] : date("m"));
unset($year);
$year = isset($_POST["year"]) ? $_POST["year"] : (isset($_GET["year"]) ? $_GET["year"] : date("Y"));

unset($id_classe);
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : (isset($_GET["id_classe"]) ? $_GET["id_classe"] : -1);

unset($id_groupe);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);

if (is_numeric($id_groupe)) {
	$current_group = get_group($id_groupe);
} else {
	$current_group = false;
}

unset($selected_eleve);
$login_eleve = isset($_POST["login_eleve"]) ? $_POST["login_eleve"] :(isset($_GET["login_eleve"]) ? $_GET["login_eleve"] :false);
if ($login_eleve) {
	$selected_eleve = mysql_fetch_object(mysql_query("SELECT e.login, e.nom, e.prenom FROM eleves e WHERE login = '" . $login_eleve . "'"));
} else {
	$selected_eleve = false;
}

if ($_SESSION['statut'] == 'eleve') {
	$selected_eleve = mysql_fetch_object(mysql_query("SELECT e.login, e.nom, e.prenom FROM eleves e WHERE login = '".$_SESSION['login'] . "'"));
}
elseif ($_SESSION['statut'] == "responsable") {
	$get_eleves = mysql_query("SELECT e.login, e.nom, e.prenom " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$_SESSION['login']."')");

	if (mysql_num_rows($get_eleves) == 1) {
			// Un seul élève associé : on initialise tout de suite la variable $selected_eleve
			// Cela signifie entre autre que l'on ne prend pas en compte $login_eleve, fermant ainsi une
			// potentielle faille de sécurité.
		$selected_eleve = mysql_fetch_object($get_eleves);
	} elseif (mysql_num_rows($get_eleves) == 0) {
		$selected_eleve = false;
	} elseif (mysql_num_rows($get_eleves) > 1 and $selected_eleve) {
		// Si on est là, c'est que la variable $login_eleve a été utilisée pour
		// générer $selected_eleve
		// On va vérifier que l'élève ainsi sélectionné fait bien partie des élèves
		// associés à l'utilisateur au statut 'responsable'
		$ok = false;
		while($test = mysql_fetch_object($get_eleves)) {
			if ($test->login == $selected_eleve->login) {$ok = true;}
		}
		if (!$ok) {$selected_eleve = false;}
	}
}
$selected_eleve_login = $selected_eleve ? $selected_eleve->login : "";

// Nom complet de la classe
//if($id_classe!='-1') {
if (($id_classe!=-1)&&($id_classe!='')) {
	$sql="SELECT classe FROM classes WHERE id='$id_classe';";
	$appel_classe=mysql_query($sql);
	if(mysql_num_rows($appel_classe)>0) {
		$classe_nom = mysql_result($appel_classe, 0, "classe");
	}
}

// Nom complet de la matière
if($current_group) {
	$matiere_nom = $current_group["matiere"]["nom_complet"];
}

//if(!isset($_GET['ordre']) or (($_GET['ordre'] != '') and ($_GET['ordre']!= 'DESC'))) {$current_ordre='';} else {$current_ordre=$_GET['ordre'];}
//if($current_ordre == '') {$ordre='DESC';} else {$ordre='';}
if(!isset($_GET['ordre'])) {
	$current_ordre='DESC';
	// Ordre inverse:
	$ordre_inverse='ASC';
}
elseif($_GET['ordre']=='ASC') {
	$current_ordre='ASC';
	$ordre_inverse='DESC';
} else {
	$current_ordre='DESC';
	$ordre_inverse='ASC';
}
//if($current_ordre == '') {$ordre_inverse='DESC';} else {$ordre_inverse='';}

if(!isset($_GET['imprime']) or (($_GET['imprime'] != 'y') and ($_GET['imprime']!= 'n'))) {$current_imprime='n';} else {$current_imprime=$_GET['imprime'];}
if ($current_imprime == 'n') {
	$imprime='y';
	$text_imprime="Version sans bandeaux";
	$largeur = "30%";
}
else {
	$imprime='n';
	$text_imprime="Retour";
	$largeur = "5%";
}

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

//**************** EN-TETE *****************
if ($current_imprime=='n') $titre_page = "Cahier de textes - Vue d'ensemble";
require_once("../lib/header.inc.php");
if ($current_imprime=='y') echo "<div id='container'>\n";
//**************** FIN EN-TETE *************

//debug_var();

// Création d'un espace entre le bandeau et le reste 
//echo "<p></p>\n";
//echo "<div style='float:right; width:10em;z-index:10000;'><a href='consultation2.php'>Affichage semaine</a></div>\n";
echo "<p><a href='consultation2.php'>Affichage semaine</a></p>\n";

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("<p class='grand centre_texte'>Le cahier de textes n'est pas accessible pour le moment.</p>");
}

$content="";

echo "<div class='centre_table'>\n";

	$infos_generales="";
	if((isset($id_groupe))&&($id_groupe=='Toutes_matieres')) {
		// Informations générales
		if (($_SESSION['statut'] == 'responsable')&&(isset($selected_eleve_login))) {
			// A VOIR: Cas des élèves qui changent de classe...

			//if((!isset($id_classe))||($id_classe<1)) {
				$sql="SELECT id_classe FROM j_eleves_classes WHERE login='$selected_eleve_login' ORDER BY periode DESC LIMIT 1;";
				//echo "$sql<br />";
				$res_classe=mysql_query($sql);
				if(mysql_num_rows($res_classe)>0) {
					//$id_classe = mysql_result($res_classe, 0, 'id_classe');
					$tmp_id_classe = mysql_result($res_classe, 0, 'id_classe');
				}
			//}

			//if(isset($id_classe)) {
			if(isset($tmp_id_classe)) {
				//$tab_grp=get_groups_for_eleve($selected_eleve_login, $id_classe);
				$tab_grp=get_groups_for_eleve($selected_eleve_login, $tmp_id_classe);
				//echo "get_groups_for_eleve($selected_eleve_login, $id_classe)<br />";

				foreach($tab_grp as $tmp_current_group) {
					$tmp_id_groupe=$tmp_current_group['id'];

					// Affichage des informations générales
					//$sql="SELECT contenu, id_ct  FROM ct_entry WHERE (id_groupe='$id_groupe' and (date_ct='' OR date_ct='0'));";
					$sql="SELECT contenu, id_ct  FROM ct_entry WHERE (id_groupe='$tmp_id_groupe' and date_ct='');";
					//echo "$sql<br />";
					$appel_info_cahier_texte = mysql_query($sql);
					$nb_cahier_texte = mysql_num_rows($appel_info_cahier_texte);
					$content .= @mysql_result($appel_info_cahier_texte, 0, 'contenu');
					$id_ct = @mysql_result($appel_info_cahier_texte, 0, 'id_ct');

					$content .= affiche_docs_joints($id_ct,"c");

					if($content!="") {
						$infos_generales.="<div class='see_all_general couleur_bord_tableau_notice color_fond_notices_i' style='width:98%;'>";
						$infos_generales.="<h3>".$tmp_current_group['name']." (<em>".$tmp_current_group['description']." en ".$tmp_current_group['classlist_string']."</em>)"."</h3>";
						$infos_generales.=$content;
						$infos_generales.="</div>";
					}
				}

				if ($infos_generales != '') {
					$titre_infobulle="Informations générales";
					$texte_infobulle="<div style='padding:1em;'>\n";
					$texte_infobulle.="<h2 class='grande_ligne couleur_bord_tableau_notice'>\n<strong>INFORMATIONS GENERALES</strong>\n</h2>\n";
					$texte_infobulle.=$infos_generales;
					$texte_infobulle.="</div>\n";

					$tabdiv_infobulle[]=creer_div_infobulle("div_informations_generales",$titre_infobulle,"",$texte_infobulle,"pink",30,0,'y','y','n','n');

					echo "<div class='color_fond_notices_i' style='float:right; width:10em; text-align:center;'><a href='#informations_generales' onclick=\"afficher_div('div_informations_generales','y',10,10);return false;\">Informations générales</a></div>";
				}
			}
		}
		

	}

	// Choix classe et matière
	echo "<div class='ct_col_gauche'>\n";
		if ($current_imprime=='n') {
			if ($_SESSION['statut'] == 'responsable') {
				//echo make_eleve_select_html('see_all.php', $_SESSION['login'], $selected_eleve, $year, $month, $day);
				if((isset($id_groupe))&&($id_groupe=='Toutes_matieres')) {
					echo make_eleve_select_html('see_all.php', $_SESSION['login'], $selected_eleve, $year, $month, $day, "Toutes_matieres");
				}
				else {
					echo make_eleve_select_html('see_all.php', $_SESSION['login'], $selected_eleve, $year, $month, $day, "avec_choix_Toutes_matieres");
				}
			}

			if ($selected_eleve_login != "") {
				//echo make_matiere_select_html('see_all.php', $selected_eleve_login, $id_groupe, $year, $month, $day);
				if((isset($id_groupe))&&($id_groupe=='Toutes_matieres')) {
					echo make_matiere_select_html('see_all.php', $selected_eleve_login, $id_groupe, $year, $month, $day, "Toutes_matieres");
				}
				else {
					echo make_matiere_select_html('see_all.php', $selected_eleve_login, $id_groupe, $year, $month, $day, "avec_choix_Toutes_matieres");
				}
			}

			if ($_SESSION['statut'] != "responsable" and $_SESSION['statut'] != "eleve") {
				echo make_classes_select_html('see_all.php', $id_classe, $year, $month, $day);
				if ($id_classe != -1) {
					//if((isset($id_groupe))&&($id_groupe=='Toutes_matieres')) {
					if((!isset($id_groupe))||($id_groupe=='Toutes_matieres')) {
						$id_groupe="Toutes_matieres";
						echo make_matiere_select_html('see_all.php', $id_classe, $id_groupe, $year, $month, $day,"Toutes_matieres");
					}
					else {
						echo make_matiere_select_html('see_all.php', $id_classe, $id_groupe, $year, $month, $day,"avec_choix_Toutes_matieres");
					}
				}
			}
		}
	echo "</div>\n";

	// Titre du CDT
	echo "<div class='ct_col_centre'>\n";
		echo "<p>\n";
			echo "<span class='grand'>\n";
				echo "Cahier de textes";
				if($id_groupe=='Toutes_matieres') {
					echo " - Toutes les matières";
				}
				else {
					if ($current_group) {
						echo " - $matiere_nom";
						echo " - classe de ".$current_group['classlist_string'];
					}
				}
				if (($id_classe!=-1)&&($id_classe!='')) {
					echo "<br />\n$classe_nom\n";
				}
			echo "</span>\n";

			// Test si le cahier de texte est partagé
			if ($current_group) {
				echo "<br />\n<strong>(";
				$i=0;
				foreach ($current_group["profs"]["users"] as $prof) {
					if ($i != 0) {echo ", ";}
					echo mb_substr($prof["prenom"],0,1) . ". " . $prof["nom"];
					$i++;
				}
				echo ")</strong>";
			}
		echo "</p>\n";
	echo "</div>\n";
echo "</div>\n";

if ($current_group) {
	echo "<div class='no_print'>\n";
		if ($current_imprime=='n') {
			if ($_SESSION["statut"] == "professeur" OR $_SESSION["statut"] == "scolarite" OR $_SESSION["statut"] == "cpe" OR $_SESSION["statut"] == "autre") {
				echo "<a href='see_all.php'>\n<img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour\n</a> - ";
				if ($_SESSION["statut"] == "professeur") {
					echo "<a href='./index.php'>\n<img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour vers mes cahiers de textes\n</a> - ";
				}
			} else {
				echo "<a href='consultation.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe'>\n<img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour\n</a> - ";
			}
			// if ($current_imprime=='n') 
			echo "<a href='see_all.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe&amp;ordre=$ordre_inverse&amp;imprime=$current_imprime'>\nTrier dans l'ordre inverse\n</a> - \n";
		}
		echo "<a href='see_all.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe&amp;ordre=$current_ordre&amp;imprime=$imprime'>\n$text_imprime\n</a>\n";
		// } retour ne s'affichait pas sur la page imprimable
	echo "</div>\n";
}

//================================================
// 20130727
$afficher_compte_rendus_seulement=isset($_GET['afficher_compte_rendus_seulement']) ? $_GET['afficher_compte_rendus_seulement'] : "n";
$afficher_travail_a_faire_seulement=isset($_GET['afficher_travail_a_faire_seulement']) ? $_GET['afficher_travail_a_faire_seulement'] : "n";

$class_notice_dev_fait="see_all_notice couleur_bord_tableau_notice color_fond_notices_t_fait";
$class_notice_dev_non_fait="see_all_notice couleur_bord_tableau_notice color_fond_notices_t";

$CDTPeutPointerTravailFait=getSettingAOui('CDTPeutPointerTravailFait'.ucfirst($_SESSION['statut']));

if(($afficher_compte_rendus_seulement!='y')&&($CDTPeutPointerTravailFait)) {
	if($selected_eleve_login!='') {
		$tab_etat_travail_fait=get_tab_etat_travail_fait($selected_eleve_login);
		echo js_cdt_modif_etat_travail();
	}
}
//================================================

//echo "\$id_classe=$id_classe<br />";
//if(($id_groupe=='Toutes_matieres')&&($id_classe!=-1)) {
if(($id_groupe=='Toutes_matieres')&&
(($selected_eleve_login!='')||($id_classe!=-1))) {
	if($id_classe==-1) {
		// Cas élève
		$sql="SELECT id_classe FROM j_eleves_classes WHERE login='$selected_eleve_login' ORDER BY periode DESC LIMIT 1;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			require("../lib/footer.inc.php");
			die();
		}
		$lig=mysql_fetch_object($res);
		$id_classe=$lig->id_classe;
	}

	echo "<div class='no_print'>\n";
		if ($current_imprime=='n') {
			if ($_SESSION["statut"] == "professeur" OR $_SESSION["statut"] == "scolarite" OR $_SESSION["statut"] == "cpe" OR $_SESSION["statut"] == "autre") {
				echo "<a href='see_all.php'>\n<img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour\n</a> - ";
				if ($_SESSION["statut"] == "professeur") {
					echo "<a href='./index.php'>\n<img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour vers mes cahiers de textes\n</a> - ";
				}
			} else {
				echo "<a href='consultation.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe'>\n<img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour\n</a> - ";
			}
			// if ($current_imprime=='n') 
			echo "<a href='see_all.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe&amp;ordre=$ordre_inverse&amp;imprime=$current_imprime'>\nTrier dans l'ordre inverse\n</a> - \n";
		}
		echo "<a href='see_all.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe&amp;ordre=$current_ordre&amp;imprime=$imprime'>\n$text_imprime\n</a>\n";
		// } retour ne s'affichait pas sur la page imprimable

		//$afficher_compte_rendus_seulement=isset($_GET['afficher_compte_rendus_seulement']) ? $_GET['afficher_compte_rendus_seulement'] : "n";
		//$afficher_travail_a_faire_seulement=isset($_GET['afficher_travail_a_faire_seulement']) ? $_GET['afficher_travail_a_faire_seulement'] : "n";

		if($afficher_travail_a_faire_seulement=='n') {
			echo "- ";
			echo "<a href='see_all.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe&amp;ordre=$current_ordre&amp;imprime=$current_imprime&amp;afficher_travail_a_faire_seulement=y'>\nN'afficher que le Travail à faire\n</a>\n";
		}

		if($afficher_compte_rendus_seulement=='n') {
			echo "- ";
			echo "<a href='see_all.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe&amp;ordre=$current_ordre&amp;imprime=$current_imprime&amp;afficher_compte_rendus_seulement=y'>\nN'afficher que les Compte-rendus de séance\n</a>\n";
		}

		if(($afficher_travail_a_faire_seulement!='n')||($afficher_compte_rendus_seulement!='n')) {
			echo "- ";
			echo "<a href='see_all.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe&amp;ordre=$current_ordre&amp;imprime=$current_imprime'>\nAfficher les Compte-rendus de séance et Travaux à faire\n</a>\n";
		}

		//================================================
		$date_debut_cdt_mail=isset($_POST['date_debut_cdt_mail']) ? $_POST['date_debut_cdt_mail'] : strftime("%d/%m/%Y");
		$mail_dest=isset($_POST['mail_dest']) ? $_POST['mail_dest'] : NULL;
		$envoi_mail=isset($_POST['envoi_mail']) ? $_POST['envoi_mail'] : "n";

		$timestamp_date_debut_cdt_mail=getSettingValue("begin_bookings");
		if(isset($date_debut_cdt_mail)) {
			$tmp_tab_date=explode("/", $date_debut_cdt_mail);
			if(isset($tmp_tab_date[2])) {
				$timestamp_date_debut_cdt_mail=mktime(0,0,0,$tmp_tab_date[1],$tmp_tab_date[0],$tmp_tab_date[2]);
			}
		}

		if($envoi_mail=="y") {
			$contexte_affichage_docs_joints="visu_eleve";
		}

		//include("../lib/calendrier/calendrier.class.php");
		//$cal1 = new Calendrier("form_envoi_cdt_mail", "date_debut_cdt_mail");

		// Choisir qui a le droit
		if(($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
			//echo "<span id='lien_mail' style='display:none'> - <a href='see_all.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe&amp;ordre=$current_ordre&amp;imprime=$current_imprime' onclick=\"\" title=\"Envoyer par mail une partie du cahier de textes (par exemple pour envoyer à un parent d'élève qui a oublié ses compte et mot de passe).\">Mail</a></span>\n";
			echo "<span id='lien_mail' style='display:none'> - <a href=\"javascript:afficher_div('div_envoi_cdt_par_mail','y',10,10)\" title=\"Envoyer par mail une partie du cahier de textes (par exemple pour envoyer à un parent d'élève qui a oublié ses compte et mot de passe).\">Mail</a></span>
			<script type='text/javascript'>document.getElementById('lien_mail').style.display=''</script>\n";
		echo "</div>\n";

		$titre_infobulle="Envoi du CDT par mail";
		$texte_infobulle="<form action='".$_SERVER['PHP_SELF']."' name='form_envoi_cdt_mail' method='post'>
	<input type='hidden' name='envoi_mail' value='y' />
	<input type='hidden' name='id_classe' value='$id_classe' />
	<input type='hidden' name='login_eleve' value='$login_eleve' />
	<input type='hidden' name='id_groupe' value='$id_groupe' />
	<input type='hidden' name='current_ordre' value='$current_ordre' />
	<input type='hidden' name='imprime' value='$current_imprime' />
	<p>Précisez à quelle adresse vous souhaitez envoyer le contenu du cahier de textes&nbsp;:<br />
	Mail&nbsp;:&nbsp;<input type='text' name='mail_dest' value='' /><br />
	Indiquez également, quelle partie du cahier de textes vous souhaitez envoyer&nbsp;:<br />
	A partir du&nbsp;:&nbsp;<input type='text' name='date_debut_cdt_mail' id='date_debut_cdt_mail' size='10' value='".$date_debut_cdt_mail."' onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />";
	$texte_infobulle.=img_calendrier_js("date_debut_cdt_mail", "img_bouton_date_debut_cdt_mail");
	//$texte_infobulle.="<a href=\"#calend\" onClick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>";
		$texte_infobulle.="<input type='submit' value='Envoyer' />
</form>";
		$tabdiv_infobulle[]=creer_div_infobulle('div_envoi_cdt_par_mail',$titre_infobulle,"",$texte_infobulle,"",30,0,'y','y','n','n');
		}
		//================================================

	echo "<hr />\n";

	echo "<div id='div_compte_rendu_envoi_mail' style='text-align:center;'></div>";

	$tab_id_grp=array();
	$tab_grp=array();
	$tab_dates=array();
	$tab_dates2=array();

	$tab_timestamp_dates=array();
	//$tab_notices_exclues_mail=array();

	$sql="SELECT DISTINCT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe' ORDER BY priorite;";
	$res=mysql_query($sql);
	while($lig=mysql_fetch_object($res)) {
		$tab_id_grp[]=$lig->id_groupe;
	}

	if($afficher_travail_a_faire_seulement=='n') {
		$sql="SELECT cte.* FROM ct_entry cte, j_groupes_classes jgc WHERE (contenu != ''
			AND date_ct != ''
			AND date_ct >= '".getSettingValue("begin_bookings")."'
			AND date_ct <= '".getSettingValue("end_bookings")."'
			AND jgc.id_groupe=cte.id_groupe
			AND jgc.id_classe='$id_classe'
			) ORDER BY date_ct DESC, heure_entry DESC, jgc.priorite DESC;";
			//) ORDER BY date_ct ".$current_ordre.", heure_entry ".$current_ordre.", jgc.priorite;";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		$cpt=0;
		while($lig=mysql_fetch_object($res)) {
			$date_notice=strftime("%a %d %b %y", $lig->date_ct);
			$tab_timestamp_dates[$date_notice]=$lig->date_ct;

			$notice_visible="y";
			if($lig->date_ct>time()) {
				// Les élèves et parents ne voient pas les comptes-rendus dans le futur (saisis à l'avance)
				if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')||($envoi_mail=='y')) {
					$notice_visible="n";
				}
				//$tab_notices_exclues_mail[]=$lig->id_ct;
			}

			if($notice_visible=="y") {
				//echo "$lig->date_ct<br />";
				//$date_notice=strftime("%a %d %b %y", $lig->date_ct);
				if(!in_array($date_notice,$tab_dates)) {
					$tab_dates[]=$date_notice;
					$tab_dates2[]=$lig->date_ct;
				}
				$tab_notices[$date_notice][$lig->id_groupe][$cpt]['id_ct']=$lig->id_ct;
				$tab_notices[$date_notice][$lig->id_groupe][$cpt]['id_login']=$lig->id_login;
				$tab_notices[$date_notice][$lig->id_groupe][$cpt]['contenu']=$lig->contenu;
				//echo " <span style='color:red'>\$tab_notices[$date_notice][$lig->id_groupe][$cpt]['contenu']=$lig->contenu</span><br />";
				$cpt++;
			}
		}
	}

	$ts_limite_visibilite_devoirs_pour_eleves=time()+getSettingValue('delai_devoirs')*24*3600;

	if($afficher_compte_rendus_seulement=='n') {
		$sql="SELECT ctd.* FROM ct_devoirs_entry ctd, j_groupes_classes jgc WHERE (contenu != ''
			AND date_ct != ''
			AND date_ct >= '".getSettingValue("begin_bookings")."'
			AND date_ct <= '".getSettingValue("end_bookings")."'
			AND jgc.id_groupe=ctd.id_groupe
			AND jgc.id_classe='$id_classe'
			) ORDER BY date_ct DESC, jgc.priorite DESC;";
			//) ORDER BY date_ct ".$current_ordre.", jgc.priorite;";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		$cpt=0;
		$timestamp_courant=time();
		while($lig=mysql_fetch_object($res)) {
			$date_dev=strftime("%a %d %b %y", $lig->date_ct);
			$tab_timestamp_dates[$date_dev]=$lig->date_ct;

			$notice_visible="y";
			//$notice_visible_mail="y";
			if($lig->date_ct>$ts_limite_visibilite_devoirs_pour_eleves) {
				// Les élèves et parents ne voient pas les notices de travaux à faire dans le futur au-delà de 'delai_devoirs'
				if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')||($envoi_mail=='y')) {
					$notice_visible="n";
				}
				//$notice_visible_mail="n";
				$tab_notices_exclues_mail[]=$lig->id_ct;
			}

			if($notice_visible=="y") {
				if(($lig->date_visibilite_eleve=="")||
					(($lig->date_visibilite_eleve!="")&&(mysql_date_to_unix_timestamp($lig->date_visibilite_eleve)<=$timestamp_courant))||
					((verif_groupe_appartient_prof($lig->id_groupe)==1)&&($envoi_mail=="n"))) {
					//echo "$lig->date_ct<br />";
					//$date_dev=strftime("%a %d %b %y", $lig->date_ct);
					if(!in_array($date_dev,$tab_dates)) {
						$tab_dates[]=$date_dev;
						$tab_dates2[]=$lig->date_ct;
					}
					$tab_dev[$date_dev][$lig->id_groupe][$cpt]['id_ct']=$lig->id_ct;
					$tab_dev[$date_dev][$lig->id_groupe][$cpt]['id_login']=$lig->id_login;
					$tab_dev[$date_dev][$lig->id_groupe][$cpt]['contenu']=$lig->contenu;
					$tab_dev[$date_dev][$lig->id_groupe][$cpt]['date_visibilite_eleve']=$lig->date_visibilite_eleve;
					//echo " <span style='color:green'>\$tab_dev[$date_notice][$lig->id_groupe][$cpt]['contenu']=$lig->contenu</span><br />";
					$cpt++;
				}
			}
		}
	}

	//echo "\$current_ordre=$current_ordre<br />";
	//sort($tab_dates);
	if($current_ordre=='ASC') {
		array_multisort ($tab_dates, SORT_DESC, SORT_NUMERIC, $tab_dates2, SORT_ASC, SORT_NUMERIC);
	}
	else {
		array_multisort ($tab_dates, SORT_ASC, SORT_NUMERIC, $tab_dates2, SORT_DESC, SORT_NUMERIC);
	}

	if(($afficher_compte_rendus_seulement=='n')&&($afficher_travail_a_faire_seulement=='n')) {
		$perc_col1=20;
		$perc_col_suivantes=39;
	}
	else {
		$perc_col1=20;
		$perc_col_suivantes=78;
	}


	//$lignes_cdt="";
	$lignes_cdt_mail="";
	//$chaine_travail_a_faire_futur="";
	for($i=0;$i<count($tab_dates);$i++) {
		$lignes_date_courante="";

		$lignes_date_courante.="<div class='infobulle_corps' style='border:1px solid black; margin:3px; padding: 3px;'>\n";
		$lignes_date_courante.="<h3 class='see_all_h3'>$tab_dates[$i]</h3>\n";
		$alt=1;
		$lignes_date_courante.="<table class='boireaus' border='1' summary='' width='100%'>\n";
		$lignes_date_courante.="<tr>\n";
		$lignes_date_courante.="<th>Enseignement</th>\n";
		if($afficher_compte_rendus_seulement=='n') {
			$lignes_date_courante.="<th>Travail à faire</th>\n";
		}
		if($afficher_travail_a_faire_seulement=='n') {
			$lignes_date_courante.="<th>Compte-rendu de séance</th>\n";
		}
		$lignes_date_courante.="</tr>\n";
		for($j=0;$j<count($tab_id_grp);$j++) {
			if((isset($tab_dev[$tab_dates[$i]][$tab_id_grp[$j]]))||(isset($tab_notices[$tab_dates[$i]][$tab_id_grp[$j]]))) {
				if(!isset($tab_grp[$tab_id_grp[$j]])) {
					$tab_champs=array('matieres', 'classes', 'profs');
					$tab_grp[$tab_id_grp[$j]]=get_group($tab_id_grp[$j]);
				}
	
				$alt=$alt*(-1);
				$lignes_date_courante.="<tr class='lig$alt'>\n";
				$lignes_date_courante.="<td width='".$perc_col1."%'><span style='font-size:x-small'>".$tab_grp[$tab_id_grp[$j]]['name']."</span><br /><span style='font-weight:bold'>".$tab_grp[$tab_id_grp[$j]]['matiere']['nom_complet']."</span><br />";
				$str="";
				foreach ($tab_grp[$tab_id_grp[$j]]['profs']['users'] as $tmp_prof) {
					$str.=$tmp_prof["civilite"]." ".my_strtoupper($tmp_prof["nom"])." ".my_strtoupper(mb_substr($tmp_prof["prenom"],0,1)).", ";
				}
				$str = mb_substr($str, 0, -2);
				$lignes_date_courante.="<span style='font-size:small'>";
				$lignes_date_courante.=$str;
				$lignes_date_courante.="</span>";
				//$lignes_date_courante.=" <span style='color:red'>$tab_id_grp[$j]</span>";
				$lignes_date_courante.="</td>\n";

				if($afficher_compte_rendus_seulement=='n') {
					$lignes_date_courante.="<td width='".$perc_col_suivantes."%' style='text-align:left; vertical-align: top;'>\n";
					if(isset($tab_dev[$tab_dates[$i]][$tab_id_grp[$j]])) {

						/*
						$chaine_travail_a_faire_futur.="<hr />\n";
						$chaine_travail_a_faire_futur.="<p>";
						$chaine_travail_a_faire_futur.="<span style='font-size:x-small'>".$tab_grp[$tab_id_grp[$j]]['name']."</span><br /><span style='font-weight:bold'>".$tab_grp[$tab_id_grp[$j]]['matiere']['nom_complet']."</span><br />\n";
						$chaine_travail_a_faire_futur.="<span style='font-size:small'>".$str."</span>";
						$chaine_travail_a_faire_futur.="</p>";
						*/

						//for($k=0;$k<count($tab_dev[$tab_dates[$i]][$tab_id_grp[$j]]);$k++) {
							//$lignes_date_courante.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_t' style='margin: 1px; padding: 1px; border: 1px solid black;'>".$tab_dev[$tab_dates[$i]][$tab_id_grp[$j]][$k]['contenu']."</div>\n";
						foreach($tab_dev[$tab_dates[$i]][$tab_id_grp[$j]] as $key => $value) {
							// 20130727
							$class_color_fond_notice="color_fond_notices_t";
							if($CDTPeutPointerTravailFait) {
								if(array_key_exists($value['id_ct'], $tab_etat_travail_fait)) {
									if($tab_etat_travail_fait[$value['id_ct']]['etat']=='fait') {
										$image_etat="../images/edit16b.png";
										$texte_etat_travail="FAIT: Le travail est actuellement pointé comme fait.\n";
										if($tab_etat_travail_fait[$value['id_ct']]['date_modif']!=$tab_etat_travail_fait[$value['id_ct']]['date_initiale']) {
											$texte_etat_travail.="Le travail a été pointé comme fait la première fois le ".formate_date($tab_etat_travail_fait[$value['id_ct']]['date_initiale'], "y")."\net modifié pour la dernière fois par la suite le ".formate_date($tab_etat_travail_fait[$value['id_ct']]['date_modif'], "y")."\n";
										}
										else {
											$texte_etat_travail.="Le travail a été pointé comme fait le ".formate_date($tab_etat_travail_fait[$value['id_ct']]['date_initiale'], "y")."\n";
										}
										$texte_etat_travail.="Cliquer pour corriger si le travail n'est pas encore fait.";
										$class_color_fond_notice="color_fond_notices_t_fait";
									}
									else {
										$image_etat="../images/edit16.png";
										$texte_etat_travail="NON FAIT: Le travail n'est actuellement pas fait.\nCliquer pour pointer le travail comme fait.";
									}
								}
								else {
									$image_etat="../images/edit16.png";
									$texte_etat_travail="NON FAIT: Le travail n'est actuellement pas fait.\nCliquer pour pointer le travail comme fait.";
								}
							}

							$lignes_date_courante.="<div id='div_travail_".$value['id_ct']."' class='see_all_notice couleur_bord_tableau_notice $class_color_fond_notice' style='margin: 1px; padding: 1px; border: 1px solid black; width: 99%; min-height:2em;'>";

							if($value['date_visibilite_eleve']!='0000-00-00 00:00:00') {
								$donne_le=formate_date($value['date_visibilite_eleve']);
								$lignes_date_courante.="<div style='float:right; width: 6em; border: 1px solid black; margin: 2px; font-size: xx-small; text-align: center;' title=\"Travail donné le $donne_le\">Donné le ".$donne_le."</div>\n";
								//$chaine_travail_a_faire_futur.="Donné le ".formate_date($value['date_visibilite_eleve'])."<br />";
							}

							if($CDTPeutPointerTravailFait) {
								$lignes_date_courante.="<div id='div_etat_travail_".$value['id_ct']."' style='float:right; width: 16px; margin: 2px; text-align: center;'><a href=\"javascript:cdt_modif_etat_travail('$selected_eleve_login', '".$value['id_ct']."')\" title=\"$texte_etat_travail\"><img src='$image_etat' class='icone16' /></a></div>\n";
							}

							$lignes_date_courante.=$value['contenu'];
							//$chaine_travail_a_faire_futur.=$value['contenu'];
							$adj=affiche_docs_joints($value['id_ct'],"t");
							if($adj!='') {
								$lignes_date_courante.="<div style='border: 1px dashed black'>\n";
								$lignes_date_courante.=$adj;
								//$chaine_travail_a_faire_futur.=$adj."<br />";
								$lignes_date_courante.="</div>\n";
							}
							$lignes_date_courante.="</div>\n";
						}
					}
					$lignes_date_courante.="</td>\n";
				}

				if($afficher_travail_a_faire_seulement=='n') {
					$lignes_date_courante.="<td width='".$perc_col_suivantes."%' style='text-align:left; vertical-align: top;'>\n";
					if(isset($tab_notices[$tab_dates[$i]][$tab_id_grp[$j]])) {
						//for($k=0;$k<count($tab_notices[$tab_dates[$i]][$tab_id_grp[$j]]);$k++) {
						foreach($tab_notices[$tab_dates[$i]][$tab_id_grp[$j]] as $key => $value) {
							$lignes_date_courante.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_c' style='margin: 1px; padding: 1px; border: 1px solid black; width: 99%;'>".$value['contenu'];
							$adj=affiche_docs_joints($value['id_ct'],"c");
							if($adj!='') {
								$lignes_date_courante.="<div style='border: 1px dashed black'>\n";
								$lignes_date_courante.=$adj;
								$lignes_date_courante.="</div>\n";
							}
							$lignes_date_courante.="</div>\n";
						}
					}
					$lignes_date_courante.="</td>\n";
				}
				$lignes_date_courante.="</tr>\n";
	
			}
		}
		$lignes_date_courante.="</table>\n";
		$lignes_date_courante.="</div>\n";

		echo $lignes_date_courante;

		if($tab_timestamp_dates[$tab_dates[$i]]>=$timestamp_date_debut_cdt_mail) {
			$lignes_cdt_mail.=$lignes_date_courante;
		}
	}

	if ($infos_generales != '') {
		echo "<a name='informations_generales'></a>";
		echo "<div style='padding:1em;'>\n";
		echo "<h2 class='grande_ligne couleur_bord_tableau_notice'>\n<strong>INFORMATIONS GENERALES</strong>\n</h2>\n";
		echo $infos_generales;
		echo "</div>\n";
	}

	echo "<hr />\n";
	echo "<p style='text-align:center; font-style:italic;'>Cahiers de textes du ";
	echo strftime("%d/%m/%Y", getSettingValue("begin_bookings"));
	echo " au ";
	echo strftime("%d/%m/%Y", getSettingValue("end_bookings"));
	echo "</p>\n";

	// 20130812
	if($envoi_mail=="y") {
		if(!check_mail($_POST['mail_dest'])) {
			$message="L'adresse mail choisie '".$_POST['mail_dest']."' est invalide.";
			echo "<p style='color:red; text-align:center;'>$message</p>
			<script type='text/javascript'>
				document.getElementById('div_compte_rendu_envoi_mail').innerHTML=\"<span style='color:red'>$message</span>\";
			</script>\n";
		}
		else {
			$sujet="Cahier de textes";
			$message="Bonjour(soir),\nVoici le contenu du cahier de textes à compter du ".$date_debut_cdt_mail.":\n".$lignes_cdt_mail;
			$destinataire=$_POST['mail_dest'];
			$header_suppl="";
			if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
				$header_suppl.="Bcc:".$_SESSION['email']."\r\n";
			}
			$envoi=envoi_mail($sujet, $message, $destinataire, $header_suppl, "html");
			if($envoi) {
				$message="Le cahier de textes a été expédié à l'adresse mail choisie '".$_POST['mail_dest']."'.";
				echo "<p style='color:green; text-align:center;'>$message</p>
				<script type='text/javascript'>
					document.getElementById('div_compte_rendu_envoi_mail').innerHTML=\"<span style='color:green'>$message</span>\";
				</script>\n";
			}
			else {
				$message="Echec de l'envoi du cahier de textes à l'adresse mail choisie '".$_POST['mail_dest']."'.";
				echo "<p style='color:red; text-align:center;'>$message</p>
				<script type='text/javascript'>
					document.getElementById('div_compte_rendu_envoi_mail').innerHTML=\"<span style='color:red'>$message</span>\";
				</script>\n";
			}
		}

		// DEBUG:
		//echo "<div style='border: 1px solid red; text-align:center;'>$lignes_cdt_mail</div>";
	}

	require("../lib/footer.inc.php");
	die();
}

echo "<hr />\n";

$test_cahier_texte = mysql_query("SELECT contenu  FROM ct_entry WHERE (id_groupe='$id_groupe')");
$nb_test = mysql_num_rows($test_cahier_texte);
if ($nb_test == 0) {
	echo "\n<h2 class='gepi centre_texte'>\n";
	if ($_SESSION['statut'] == "responsable") {
		echo "Choisissez un élève et une matière.";
	} elseif ($_SESSION['statut'] == "eleve") {
		echo "Choisissez une matière.";
	} else {
		echo "Choisissez une classe et une matière.";
	}
	echo "\n</h2>\n";

	echo "<hr />\n";
	echo "<p style='text-align:center; font-style:italic;'>Cahiers de textes du ";
	echo strftime("%d/%m/%Y", getSettingValue("begin_bookings"));
	echo " au ";
	echo strftime("%d/%m/%Y", getSettingValue("end_bookings"));
	echo "</p>\n";

	require("../lib/footer.inc.php");
	die();
}
// Affichage des informations générales
$appel_info_cahier_texte = mysql_query("SELECT contenu, id_ct  FROM ct_entry WHERE (id_groupe='$id_groupe' and date_ct='')");
$nb_cahier_texte = mysql_num_rows($appel_info_cahier_texte);
$content = @mysql_result($appel_info_cahier_texte, 0, 'contenu');
$id_ct = @mysql_result($appel_info_cahier_texte, 0, 'id_ct');
$content .= affiche_docs_joints($id_ct,"c");
if ($content != '') {
	echo "<h2 class='grande_ligne couleur_bord_tableau_notice'>\n<strong>INFORMATIONS GENERALES</strong>\n</h2>\n";
	echo "<div class='see_all_general couleur_bord_tableau_notice color_fond_notices_i' style='width:98%'>".$content."</div>";
}

	// echo "<div  style=\"border-bottom-style: solid; border-width:2px; border-color: ".$couleur_bord_tableau_notice."; \"><strong>CAHIER DE TEXTES: comptes rendus de séance</strong></div><br />";
echo "<h2 class='grande_ligne couleur_bord_tableau_notice'>\n<strong>CAHIER DE TEXTES: comptes rendus de séance</strong>\n</h2>\n";

if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
	$req_notices =
		"select 'c' type, contenu, date_ct, id_ct
		from ct_entry
		where (contenu != ''
		and id_groupe='".$id_groupe."'
		and date_ct != ''
		and date_ct >= '".getSettingValue("begin_bookings")."'
		and date_ct <= '".getSettingValue("end_bookings")."'
		and date_ct <= '".time()."')
		ORDER BY date_ct ".$current_ordre.", heure_entry ".$current_ordre;
}
else {
	$req_notices =
		"select 'c' type, contenu, date_ct, id_ct
		from ct_entry
		where (contenu != ''
		and id_groupe='".$id_groupe."'
		and date_ct != ''
		and date_ct >= '".getSettingValue("begin_bookings")."'
		and date_ct <= '".getSettingValue("end_bookings")."')
		ORDER BY date_ct ".$current_ordre.", heure_entry ".$current_ordre;
}
//echo "$req_notices<br />";
$res_notices = mysql_query($req_notices);
$notice = mysql_fetch_object($res_notices);

$ts_limite_visibilite_devoirs_pour_eleves=time()+getSettingValue('delai_devoirs')*24*3600;

if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
	$ts_limite_dev=$ts_limite_visibilite_devoirs_pour_eleves;
}
else {
	$ts_limite_dev=getSettingValue("end_bookings");
}
$req_devoirs =
	"select 't' type, contenu, date_ct, id_ct, date_visibilite_eleve
	from ct_devoirs_entry
	where (contenu != ''
	and id_groupe = '".$id_groupe."'
	and date_ct != ''
	and date_ct >= '".getSettingValue("begin_bookings")."'
	and date_ct <= '".$ts_limite_dev."'
	) order by date_ct ".$current_ordre;
$res_devoirs = mysql_query($req_devoirs);
$devoir = mysql_fetch_object($res_devoirs);

$timestamp_courant=time();
// Boucle d'affichage des notices dans la colonne de gauche
$date_ct_old = -1;
while (true) {
	if ($current_ordre == "DESC") {
		// On met les notices du jour avant les devoirs à rendre aujourd'hui
		if ($notice && (!$devoir || $notice->date_ct >= $devoir->date_ct)) {
			// Il y a encore une notice et elle est plus récente que le prochain devoir, où il n'y a plus de devoirs
			$not_dev = $notice;
			$notice = mysql_fetch_object($res_notices);

			$type_notice="notice";
		} elseif($devoir) {
			// Plus de notices et toujours un devoir, ou devoir plus récent
			$not_dev = $devoir;
			$devoir = mysql_fetch_object($res_devoirs);

			$type_notice="devoir";
		} else {
			// Plus rien à afficher, on sort de la boucle
			break;
		}
	} else {
		// On met les notices du jour avant les devoirs à rendre aujourd'hui
		if ($notice && (!$devoir || $notice->date_ct <= $devoir->date_ct)) {
			// Il y a encore une notice et elle est plus récente que le prochain devoir, où il n'y a plus de devoirs
			$not_dev = $notice;
			$notice = mysql_fetch_object($res_notices);

			$type_notice="notice";
		} elseif($devoir) {
			// Plus de notices et toujours un devoir, ou devoir plus récent
			$not_dev = $devoir;
			$devoir = mysql_fetch_object($res_devoirs);

			$type_notice="devoir";
		} else {
			// Plus rien à afficher, on sort de la boucle
			break;
		}
	}

	/*
	if($type_notice=="devoir") {
		echo "<p>".$not_dev->date_visibilite_eleve."<br />";
		echo mysql_date_to_unix_timestamp($not_dev->date_visibilite_eleve)."<br />";
		echo $timestamp_courant."<br />";
		echo $not_dev->contenu."<br />";
		echo "</p>";
	}
	*/

	if(($type_notice!="devoir")||
		($not_dev->date_visibilite_eleve=="")||
		(($not_dev->date_visibilite_eleve!="")&&(mysql_date_to_unix_timestamp($not_dev->date_visibilite_eleve)<=$timestamp_courant))||
		(verif_groupe_appartient_prof($id_groupe)==1)) {

		// Passage en HTML
		// INSERT INTO setting SET name='depolluer_MSOffice', value='y';
		if(getSettingValue('depolluer_MSOffice')=='y') {
			$content = &preg_replace('#.*<\!\[endif\]-->#',"",$not_dev->contenu);
		}
		else {
			$content = &$not_dev->contenu;
		}
	
		$content .= affiche_docs_joints($not_dev->id_ct,$not_dev->type);
		echo "<h3 class='see_all_h3'>\n<strong>\n";
			if ($not_dev->type == "t") {
				echo "<a name='travail_".$not_dev->id_ct."'></a>";
				echo("A faire pour le : ");
			}
			else {
				echo "<a name='compte_rendu_".$not_dev->id_ct."'></a>";
			}
			echo(strftime("%a %d %b %y", $not_dev->date_ct));
		echo "</strong>\n</h3>\n";
		// Numérotation des notices si plusieurs notices sur la même journée
		if ($not_dev->type == "c") {
			if ($date_ct_old == $not_dev->date_ct) {
				$num_notice++;
				echo " <strong><em>(notice N° ".$num_notice.")</em></strong>";
			} else {
				// on afffiche "(notice N° 1)" uniquement s'il y a plusieurs notices dans la même journée
				$nb_notices = sql_query1("SELECT count(id_ct) FROM ct_entry WHERE (id_groupe='" . $current_group["id"] ."' and date_ct='".$not_dev->date_ct."')");
				if ($nb_notices > 1)
					echo " <strong><em>(notice N° 1)</em></strong>";
				// On réinitialise le compteur
				$num_notice = 1;
			}
		}
		// echo("<table style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice.";\" width=\"100%\" cellpadding=\"1\" bgcolor=\"".$color_fond_notices[$not_dev->type]."\">\n<tr>\n<td>\n$content</td>\n</tr>\n</table>\n<br/>\n");
		if($not_dev->type=="t") {

			// 20130727
			$class_color_fond_notice="color_fond_notices_t";
			if($CDTPeutPointerTravailFait) {
				get_etat_et_img_cdt_travail_fait($not_dev->id_ct);
				/*
				if(array_key_exists($not_dev->id_ct, $tab_etat_travail_fait)) {
					if($tab_etat_travail_fait[$not_dev->id_ct]['etat']=='fait') {
						$image_etat="../images/edit16b.png";
						$texte_etat_travail="FAIT: Le travail est actuellement pointé comme fait.\n";
						if($tab_etat_travail_fait[$not_dev->id_ct]['date_modif']!=$tab_etat_travail_fait[$not_dev->id_ct]['date_initiale']) {
							$texte_etat_travail.="Le travail a été pointé comme fait la première fois le ".formate_date($tab_etat_travail_fait[$not_dev->id_ct]['date_initiale'], "y")."\net modifié pour la dernière fois par la suite le ".formate_date($tab_etat_travail_fait[$not_dev->id_ct]['date_modif'], "y")."\n";
						}
						else {
							$texte_etat_travail.="Le travail a été pointé comme fait le ".formate_date($tab_etat_travail_fait[$not_dev->id_ct]['date_initiale'], "y")."\n";
						}
						$texte_etat_travail.="Cliquer pour corriger si le travail n'est pas encore fait.";
						$class_color_fond_notice="color_fond_notices_t_fait";
					}
					else {
						$image_etat="../images/edit16.png";
						$texte_etat_travail="NON FAIT: Le travail n'est actuellement pas fait.\nCliquer pour pointer le travail comme fait.";
					}
				}
				else {
					$image_etat="../images/edit16.png";
					$texte_etat_travail="NON FAIT: Le travail n'est actuellement pas fait.\nCliquer pour pointer le travail comme fait.";
				}
				*/
			}

			echo "<div id='div_travail_".$not_dev->id_ct."' class='see_all_notice couleur_bord_tableau_notice $class_color_fond_notice' style='min-height:2em;'>";
		}
		else {
			echo "<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_".$not_dev->type."''>";
		}

// id='div_travail_".$value['id_ct']."' class='see_all_notice couleur_bord_tableau_notice $class_color_fond_notice

		/* if ($not_dev->type == "t") {
			echo "see_all_a_faire'>\n";
		} else {
			echo "see_all_compte_rendu'>\n";
		}*/

		if(($type_notice=='devoir')&&($not_dev->date_visibilite_eleve!='0000-00-00 00:00:00')) {
			$donne_le=formate_date($not_dev->date_visibilite_eleve);
			echo "<div style='float:right; width: 6em; border: 1px solid black; margin: 2px; font-size: xx-small; text-align: center;' title=\"Travail donné le $donne_le\">Donné le ".$donne_le."</div>\n";
		}

		if(($type_notice=='devoir')&&($CDTPeutPointerTravailFait)) {
			echo "<div id='div_etat_travail_".$not_dev->id_ct."' style='float:right; width: 16px; margin: 2px; text-align: center;'><a href=\"javascript:cdt_modif_etat_travail('$selected_eleve_login', '".$not_dev->id_ct."')\" title=\"$texte_etat_travail\"><img src='$image_etat' class='icone16' /></a></div>\n";
		}

		echo "$content\n</div>\n";
		if ($not_dev->type == "c") {$date_ct_old = $not_dev->date_ct;}
	}
}

//if ($current_imprime=='n') echo "</td></tr></table>";
//echo "</td></tr></table>";

echo "<hr />\n";
echo "<p style='text-align:center; font-style:italic;'>Cahiers de textes du ";
echo strftime("%d/%m/%Y", getSettingValue("begin_bookings"));
echo " au ";
echo strftime("%d/%m/%Y", getSettingValue("end_bookings"));
echo "</p>\n";

require("../lib/footer.inc.php");
?>
