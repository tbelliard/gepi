<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
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
require_once("../lib/initialisationsPropel.inc.php");
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

if (getSettingValue("GepiCahierTexteVersion") != '2') {
  tentative_intrusion(1, "Tentative d'accès au cahier de textes v2 alors qu'il n'est pas ouvert.");
  header("Location: ../cahier_texte/consultation.php");
  die();
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

include "../lib/mincals.inc";

unset($id_classe);
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : (isset($_GET["id_classe"]) ? $_GET["id_classe"] : NULL);
unset($day);
$day = isset($_POST["day"]) ? $_POST["day"] : (isset($_GET["day"]) ? $_GET["day"] : date("d"));
unset($month);
$month = isset($_POST["month"]) ? $_POST["month"] : (isset($_GET["month"]) ? $_GET["month"] : date("m"));
unset($year);
$year = isset($_POST["year"]) ? $_POST["year"] : (isset($_GET["year"]) ? $_GET["year"] : date("Y"));
unset($id_matiere);
$id_matiere = isset($_POST["id_matiere"]) ? $_POST["id_matiere"] : (isset($_GET["id_matiere"]) ? $_GET["id_matiere"] : -1);
unset($id_groupe);
// modification Régis : traité "matiere" au cas où le javascript est désactivé
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] : (isset($_POST['matiere']) ?  mb_substr(strstr($_POST['matiere'],"id_groupe="),10) : (isset($_GET["matiere"]) ?  mb_substr(strstr($_GET["matiere"],"id_groupe="),10) :  NULL)));
if (is_numeric($id_groupe)) {
    $current_group = get_group($id_groupe);
    //if ($id_classe == NULL) $id_classe = $current_group["classes"]["list"][0];
} else {
    $current_group = false;
}

unset($selected_eleve);
// modification Régis : traité "eleve" au cas où le javascript est désactivé
$login_eleve = isset($_POST["login_eleve"]) ? $_POST["login_eleve"] :(isset($_GET["login_eleve"]) ? $_GET["login_eleve"] :(isset($_POST['eleve']) ? mb_substr(strstr($_POST['eleve'],"login_eleve="),12) : (isset($_GET["eleve"]) ? mb_substr(strstr($_GET["eleve"],"login_eleve="),12) :false)));
if ($login_eleve) {
	$selected_eleve = mysqli_fetch_object(mysqli_query($GLOBALS["mysqli"], "SELECT e.login, e.nom, e.prenom FROM eleves e WHERE (login = '" . $login_eleve . "')"));
} else {
	$selected_eleve = false;
}

if ($_SESSION['statut'] == 'eleve') {
	// On enregistre si un élève essaie de voir le cahier de texte d'un autre élève
	if ($selected_eleve) {
		if (my_strtolower($selected_eleve->login) != my_strtolower($_SESSION['login'])) {tentative_intrusion(2, "Tentative d'un élève d'accéder au cahier de textes d'un autre élève.");}
	}
	$selected_eleve = mysqli_fetch_object(mysqli_query($GLOBALS["mysqli"], "SELECT e.login, e.nom, e.prenom FROM eleves e WHERE login = '".$_SESSION['login'] . "'"));
} elseif ($_SESSION['statut'] == "responsable") {
	$sql="(SELECT e.login, e.nom, e.prenom " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$_SESSION['login']."' AND (re.resp_legal='1' OR re.resp_legal='2')))";

	if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
		$sql.=" UNION (SELECT e.login, e.nom, e.prenom " .
							"FROM eleves e, resp_pers r, responsables2 re " .
							"WHERE (" .
							"e.ele_id = re.ele_id AND " .
							"re.pers_id = r.pers_id AND " .
							"r.login = '".$_SESSION['login']."' AND 
							re.resp_legal='0' AND 
							re.acces_sp='y'))";
	}
	$sql.=";";
	//echo "$sql<br />";
	$get_eleves = mysqli_query($GLOBALS["mysqli"], $sql);

	if (mysqli_num_rows($get_eleves) == 1) {
			// Un seul élève associé : on initialise tout de suite la variable $selected_eleve
			// Cela signifie entre autre que l'on ne prend pas en compte $login_eleve, fermant ainsi une
			// potentielle faille de sécurité.
		$selected_eleve = mysqli_fetch_object($get_eleves);
	} elseif (mysqli_num_rows($get_eleves) == 0) {
		$selected_eleve = false;
	} elseif (mysqli_num_rows($get_eleves) > 1 and $selected_eleve) {
		// Si on est là, c'est que la variable $login_eleve a été utilisée pour
		// générer $selected_eleve
		// On va vérifier que l'élève ainsi sélectionné fait bien partie des élèves
		// associés à l'utilisateur au statut 'responsable'
		$ok = false;
		while($test = mysqli_fetch_object($get_eleves)) {
			if (my_strtolower($test->login) == my_strtolower($selected_eleve->login)) {$ok = true;}
		}
		if (!$ok) {
			// Si on est là, ce qu'un utilisateur au statut 'responsable' a essayé
			// de sélectionner un élève pour lequel il n'est pas responsable.
			tentative_intrusion(2, "Tentative d'accès par un parent au cahier de textes d'un autre élève que le ou les sien(s).");
			$selected_eleve = false;
		}
	}

	if((isset($login_eleve))&&($login_eleve!="")) {
		$sql="(SELECT 1=1 FROM resp_pers r, responsables2 re, eleves e WHERE r.pers_id=re.pers_id AND re.ele_id=e.ele_id AND r.login='".$_SESSION['login']."' AND (re.resp_legal='1' OR re.resp_legal='2') AND e.login='".$login_eleve."')";
		if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
			$sql.=" UNION (SELECT 1=1 FROM eleves e, resp_pers r, responsables2 re 
							WHERE (e.login = '" . $login_eleve . "' AND
								e.ele_id = re.ele_id AND 
								re.pers_id = r.pers_id AND 
								r.login = '".$_SESSION['login']."' AND 
								re.resp_legal='0' AND 
								re.acces_sp='y'))";
		}
		$sql.=";";
		//echo "$sql<br />";
		$verif_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($verif_ele)==0) {
			tentative_intrusion(2, "Tentative d'accès par un parent au cahier de textes d'un autre élève que le ou les sien(s).");
			header("Location: ../logout.php?auto=1");
			die();
			//echo "PB intrusion<br />";
		}
	}
}
$selected_eleve_login = $selected_eleve ? $selected_eleve->login : "";

// Nom complet de la classe
$appel_classe = mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM classes WHERE id='$id_classe'");
$classe_nom = @old_mysql_result($appel_classe, 0, "classe");
// Nom complet de la matière
$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
// Vérification sur les dates
settype($month,"integer");
settype($day,"integer");
settype($year,"integer");
$minyear = strftime("%Y", getSettingValue("begin_bookings"));
$maxyear = strftime("%Y", getSettingValue("end_bookings"));

//echo "\$year=$year<br />";
//echo "\$minyear=$minyear<br />";
//echo "\$maxyear=$maxyear<br />";

if ($day < 1) {$day = 1;}
if ($day > 31) {$day = 31;}
if ($month < 1) {$month = 1;}
if ($month > 12) {$month = 12;}
if ($year < $minyear) {$year = $minyear;}
if ($year > $maxyear) {$year = $maxyear;}

//echo "\$year=$year<br />";

// On empêche un élève ou un parent de voir les CR des jours futurs
  /* --------- Ajout pour les séquences ---------------- */
/*
if ($_SESSION["statut"] == 'eleve' OR $_SESSION["statut"] == 'responsable'){

  if ($day > date("d")) {$day = date("d");}
  if ($month > date("m")) {$month = date("m");}
  if ($year > date("Y")) {$year = date("Y");}

}
*/

# Make the date valid if day is more then number of days in month
while (!checkdate($month, $day, $year)) $day--;
$today=mktime(0,0,0,$month,$day,$year);
//**************** EN-TETE *****************
$titre_page = "Cahier de textes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

if(!getSettingAOui("active_cahiers_texte")) {
	// On doit avoir acces_cdt_prof=y
	echo "<p style='text-align:center; color:red'>Ces cahiers de textes sont personnels. Ils ne sont pas accessibles des élèves, parents,...</p>";
}

// 20130727
$CDTPeutPointerTravailFait=getSettingAOui('CDTPeutPointerTravailFait'.ucfirst($_SESSION['statut']));

$class_notice_dev_fait="cdt_fond_not_dev color_fond_notices_t_fait";
$class_notice_dev_non_fait="cdt_fond_not_dev couleur_cellule_gen";
if(($selected_eleve_login!='')&&($CDTPeutPointerTravailFait)) {
	$tab_etat_travail_fait=get_tab_etat_travail_fait($selected_eleve_login);
	echo js_cdt_modif_etat_travail();
}

//debug_var();

//echo "<p>\$selected_eleve_login=$selected_eleve_login</p>";
//echo "<p>id_classe=$id_classe</p>";
//echo "<p>\$today=$today</p>";
if($selected_eleve_login!=""){
	$sql="SELECT * FROM j_eleves_classes WHERE login='$selected_eleve_login' ORDER BY periode DESC";
	//echo "$sql<br />\n";
	$res_ele_classe=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ele_classe)>0){
		$ligtmp=mysqli_fetch_object($res_ele_classe);
		//echo "<p>id_classe=$ligtmp->id_classe et periode=$ligtmp->periode</p>";
		$selected_eleve_classe=$ligtmp->id_classe;
	}
}

// On vérifie que la date demandée est bien comprise entre la date de début des cahiers de texte et la date de fin des cahiers de texte :
if ($today < getSettingValue("begin_bookings")) {
   $today = getSettingValue("begin_bookings");
} else if ($today > getSettingValue("end_bookings")) {
   $today = getSettingValue("end_bookings");
}
echo "<script type=\"text/javascript\" src=\"../lib/clock_fr.js\"></script>\n";
//-----------------------------------------------------------------------------------
//echo "<table width=\"98%\" cellspacing=\"0\" align=\"center\">\n<tr>\n";
//echo "<td valign='top'>\n";
// correction Regis : ajout d'une classe "centre_table", "ct_col_gauche"
echo "<div class=\"centre_table\">\n";
	// echo "<tr>\n";
		echo "<div class=\"ct_col_gauche\">\n";
			echo "<p class=\"menu_retour\">\n";
				echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/>\n";
					echo "Retour à l'accueil\n";
				echo "</a>\n";
				echo " | ";
				echo "<a href=\"consultation2.php";
				if(($_SESSION['statut']=='responsable')&&(isset($login_eleve))) {echo "?login_eleve=$login_eleve";}
				echo "\">\n";
					echo "Affichage semaine\n";
				echo "</a>\n";
			echo "</p>\n";
			echo "<p>Nous sommes le :&nbsp;<br />\n";
			echo "<script type=\"text/javascript\">\n";
			echo "<!--\n";
			echo "new LiveClock();\n";
			echo "//-->";
			echo "\n</script>\n</p>\n";
			echo "<noscript>\n<p>".my_strftime("%A %d %B %Y", $today)."</p>\n</noscript>";
//<p class='menu_retour'>".get_date_php()."</p>\n</noscript>";
			// On gère la sélection de l'élève
			if ($_SESSION['statut'] == 'responsable') {
				echo make_eleve_select_html('consultation.php', $_SESSION['login'], $selected_eleve, $year, $month, $day);
			}
			if ($selected_eleve_login != "") {
				echo make_matiere_select_html('consultation.php', $selected_eleve_login, $id_groupe, $year, $month, $day);
				echo "<a href='see_all.php?&year=$year&amp;month=$month&amp;day=$day&amp;login_eleve=$selected_eleve_login&amp;id_groupe=Toutes_matieres'>Voir l'ensemble du cahier de textes</a>";
			}
		echo "</div>\n";
		//echo "<td align=\"right\">\n";
		// Modification Régis : la colonne de droite doit être avant la colonne centrale
		echo "<div class=\"ct_col_droit\">\n";
			echo "<h2 class='invisible'>Calendrier</h2>";
			echo "<form action=\"./consultation.php\" method=\"post\" style=\"width: 100%;\">\n";
				echo "<p>";
					genDateSelector("", $day, $month, $year,'');
					echo "<input type=\"hidden\" name=\"id_groupe\" value=\"$id_groupe\" />\n";
					echo "<input type=\"hidden\" name=\"id_classe\" value=\"$id_classe\" />\n";
					echo "<input type=\"hidden\" name=\"login_eleve\" value=\"$login_eleve\" />\n";
					echo "<input type=\"submit\" value=\"OK\" />\n";
				echo "</p>\n";
			echo "</form>\n";
			//Affiche le calendrier
			minicals($year, $month, $day, $id_groupe, 'consultation.php?');
		echo "</div>\n";
		//echo "<td style=\"text-align:center;\">\n";
		//echo "<td class=\"ct_col_centre\" style=\"text-align:center;\">\n";
		// Modification Régis : la colonne centrale doit être à la fin pour que son contenu se positionne entre les 2 autres
		echo "<div class=\"ct_col_centre\">\n";
			echo "<p>\n";
				echo "<span class='grand'>Cahier de textes";
				if ($current_group) {echo " - $matiere_nom ($matiere_nom_court)";}
				if ($id_classe != null) {echo "<br />$classe_nom";}
				elseif((isset($id_groupe))&&(is_numeric($id_groupe))) {
					$tmp_tab=get_classes_from_id_groupe($id_groupe);
					if(isset($tmp_tab['classlist_string'])) {
						echo "<br />".$tmp_tab['classlist_string'];
					}
				}
				echo "</span>\n";

				// Test si le cahier de texte est partagé
				//if ($current_group) {
				if (($current_group)&&(isset($selected_eleve_classe))) {
					//echo "<br />\n<strong>(";
					echo "<br />\n<strong>(";
					$i=0;
					foreach ($current_group["profs"]["users"] as $prof) {
						if ($i != 0) echo ", ";
						//echo mb_substr($prof["prenom"],0,1) . ". " . $prof["nom"];
						//echo "\$id_classe=$id_classe<br />".$prof["login"]."<br />";
						echo affiche_utilisateur($prof["login"],$selected_eleve_classe);
						$i++;
					}
				  //echo ")</strong>\n";
				  echo ")</strong>\n";
				}

			echo "</p>\n";
		echo "</div>\n";
	//echo "</tr>\n";
echo "</div>\n";
echo "<hr />\n";

// TEST: Est-ce qu'une période au moins est ouverte en saisie dans le cas élève?
if($selected_eleve) {
	$sql="SELECT * FROM periodes p, j_eleves_classes jec WHERE jec.id_classe=p.id_classe AND jec.login=''";

	// Pour les élèves qui ont changé de classe, on risque des infos erronées si on se base sur la dernière période ouverte en saisie (si leur classe actuelle est fermée pour toutes les périodes et que l'ancienne classe est ouverte sur une période... on va récupérer les devoirs de l'autre classe)

}

// Modification Regis : mise en page par CSS des devoirs à faire si la matière n'est pas sélectionnée

$test_cahier_texte = mysqli_query($GLOBALS["mysqli"], "SELECT contenu FROM ct_entry WHERE (id_groupe='$id_groupe')");
$nb_test = mysqli_num_rows($test_cahier_texte);
$delai = getSettingValue("delai_devoirs");
//Affichage des devoirs globaux s'il n'y a pas de notices dans ct_entry à afficher

if (($nb_test == 0) and ($id_classe != null OR $selected_eleve) and ($delai != 0)) {

	//echo "plop";
	//echo "id_classe=$id_classe<br />";

	if((isset($selected_eleve))&&($selected_eleve)) {

		$tab_date_fin=array();
		$tab_id_classe_ele=array();
		$tab_periode_ele=array();

		$tab_per_eleve=get_class_dates_from_ele_login($selected_eleve->login);

		foreach($tab_per_eleve as $key => $value) {
			$tab_date_fin[]=mysql_date_to_unix_timestamp($value['date_fin']);
			$tab_id_classe_ele[]=$value['id_classe'];
			$tab_periode_ele[]=$key;
		}
	}

	$restriction_visibilite_notices_devoirs="";
	if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
		$restriction_visibilite_notices_devoirs=" AND ct.date_visibilite_eleve<='".strftime("%Y-%m-%d %H:%M:%S")."'";
	}

    if ($delai == "") die("Erreur : Délai de visualisation du travail personnel non défini. Contactez l'administrateur de GEPI de votre établissement.");
    $nb_dev = 0;
    for ($i = 0; $i <= $delai; $i++) {
        //$aujourhui = $aujourdhui = mktime(0,0,0,date("m"),date("d"),date("Y"));
        $aujourdhui = mktime(0,0,0,date("m"),date("d"),date("Y"));
        $jour = mktime(0, 0, 0, date('m',$aujourdhui), (date('d',$aujourdhui) + $i), date('Y',$aujourdhui) );
        $jour_suivant=$jour+24*3600;
        if (is_numeric($id_classe) AND $id_classe > 0) {
	        $sql="SELECT ct.id_sequence, ct.contenu, g.id, g.description, ct.date_ct, ct.id_ct " .
	            "FROM ct_devoirs_entry ct, groupes g, j_groupes_classes jc WHERE (" .
	            "ct.id_groupe = jc.id_groupe and " .
	            "g.id = jc.id_groupe and " .
	            "jc.id_classe = '" . $id_classe . "' and " .
	            "ct.contenu != '' and " .
	            "ct.date_ct >= '$jour' and
	             ct.date_ct < '$jour_suivant'".$restriction_visibilite_notices_devoirs."
	            );";

        } elseif ($selected_eleve) {
			// Le DISTINCT est quand même utile parce que si plusieurs périodes sont ouvertes en saisie, on a une multiplication des retours par le nombre de périodes ouvertes en saisie
	        /*
	        $sql="SELECT DISTINCT ct.id_sequence, ct.contenu, g.id, g.description, ct.date_ct, ct.id_ct " .
                "FROM ct_devoirs_entry ct, groupes g, j_eleves_groupes jeg, j_eleves_classes jec, periodes p WHERE (" .
                "ct.id_groupe = jeg.id_groupe and " .
                "g.id = jeg.id_groupe and " .
                "jeg.login = '" . $selected_eleve->login . "' and " .
                "jeg.periode = p.num_periode and " .
                "jec.periode = p.num_periode and " .
                "p.verouiller = 'N' and " .
                "p.id_classe = jec.id_classe and " .
                "jec.login = '" . $selected_eleve->login ."' and " .
                "ct.contenu != '' and " .
                "ct.date_ct = '$jour')";
			*/
			$periode_courante=1;
			for($loop=0;$loop<count($tab_date_fin);$loop++) {
				if($jour<=$tab_date_fin[$loop]) {
					$periode_courante=$tab_periode_ele[$loop];
					$id_classe_courante=$tab_id_classe_ele[$loop];
					break;
				}
			}

			//if(isset($id_classe_courante)) {

	        //$sql="SELECT DISTINCT ct.id_sequence, ct.contenu, g.id, g.description, ct.date_ct, ct.id_ct, ct.special " .
	        $sql="SELECT DISTINCT ct.id_sequence, ct.contenu, g.id, g.description, ct.date_ct, ct.id_ct " .
                "FROM ct_devoirs_entry ct, groupes g, j_eleves_groupes jeg, j_eleves_classes jec WHERE (" .
                "ct.id_groupe = jeg.id_groupe and " .
                "g.id = jeg.id_groupe and " .
                "jeg.login = '" . $selected_eleve->login . "' and " .
                "jeg.periode = jec.periode and " .
                "jeg.periode = '".$periode_courante."' and " .
                "jec.login = '" . $selected_eleve->login ."' and " .
                "ct.contenu != '' and " .
                "ct.date_ct >= '$jour' and
	             ct.date_ct < '$jour_suivant'".$restriction_visibilite_notices_devoirs."
	            )";

        }
		//echo strftime("%a %d/%m/%y",$jour)."<br />";
		//echo "$sql<br />";
		$appel_devoirs_cahier_texte = mysqli_query($GLOBALS["mysqli"], $sql);
        $nb_devoirs_cahier_texte = mysqli_num_rows($appel_devoirs_cahier_texte);
        $ind = 0;
        if ($nb_devoirs_cahier_texte != 0) {
          $nb_dev++;
          if ($nb_dev == '1') {

            // Correction Régis : création de classes pour gérer la mise en page par fichier CSS
            echo "<p class=\"centre_texte no_print\">Date sélectionnée : ".my_strftime("%A %d %B %Y", $today)."\n</p>\n";
            echo "<h2 class=\"centre_texte_pt_cap petit_h2\">Travaux personnels des $delai jours suivant le ".my_strftime("%d %B %Y", $today)."</h2>\n";

            echo "<div class='cel_trav_futur couleur_bord_tableau_notice color_fond_notices_f'>\n";

          }

          // On range les devoirs les uns à côté des autres en fonction du jour
          /**
           * @todo il faudrait inverser le tableau et la méthode avec old_mysql_result ne le permet pas facilement
           * pour afficher les devoirs en fonction du jour à la place des uns en dessous des autres.
           */
          //if ($i > 0) {$margin_left = 'margin-left:'.($i * 15).'%;';}else{$margin_left = NULL;}
          //echo "\n".'<div style="position relative;'.$margin_left.'width: 15%;">';
          echo "<h3 class=\"titre_a_faire couleur_bord_tableau_notice color_fond_notices_f color_police_travaux\">\n
                  Travaux personnels pour le ".my_strftime("%a %d %b", $jour)."</h3>\n";

			// 20130727
			$class_notice_dev_fait="matiere_a_faire couleur_bord_tableau_notice color_police_matieres color_fond_notices_t_fait";
			$class_notice_dev_non_fait="matiere_a_faire couleur_bord_tableau_notice couleur_cellule_f color_police_matieres";

          // Affichage des devoirs dans chaque matière
          while ($ind < $nb_devoirs_cahier_texte) {
            $content = old_mysql_result($appel_devoirs_cahier_texte, $ind, 'contenu');
            $date_devoirs = old_mysql_result($appel_devoirs_cahier_texte, $ind, 'date_ct');
            $id_devoirs =  old_mysql_result($appel_devoirs_cahier_texte, $ind, 'id_ct');
            $id_groupe_devoirs = old_mysql_result($appel_devoirs_cahier_texte, $ind, 'id');
            $_id_sequence = old_mysql_result($appel_devoirs_cahier_texte, $ind, 'id_sequence');
            $matiere_devoirs = old_mysql_result($appel_devoirs_cahier_texte,$ind, 'description');
            //$special_devoirs = old_mysql_result($appel_devoirs_cahier_texte,$ind, 'special');
            //$test_prof = "SELECT nom, prenom FROM j_groupes_professeurs j, utilisateurs u WHERE (j.id_groupe='".$id_groupe_devoirs."' and u.login=j.login) ORDER BY nom, prenom";
            $test_prof = "SELECT nom, prenom,u.login FROM j_groupes_professeurs j, utilisateurs u 
                                                      WHERE (j.id_groupe='".$id_groupe_devoirs."' and u.login=j.login)
                                                      ORDER BY nom, prenom";
            $res_prof = sql_query($test_prof);
            $chaine = "";
            for ($k=0 ; $prof=sql_row($res_prof,$k) ; $k++) {
              if ($k != 0) $chaine .= ", ";
              //$chaine .= htmlspecialchars($prof[0])." ".mb_substr(htmlspecialchars($prof[1]),0,1).".";
            	// ???????????????????????????????
        			// Faudrait-il modifier ici pour utiliser
            	$chaine.=affiche_utilisateur($prof[2],$selected_eleve_classe);
              // Comment est utilisé $chaine???
              // ???????????????????????????????
            }

            // On ajoute le nom de la séquence si elle existe
            // On n'utilise pas les objets propel pour ne pas surcharger mais il faudra réécrire avec
            $aff_titre_seq = NULL;
            if ($_id_sequence != '0'){
              $sql_seq        = "SELECT titre FROM ct_sequences WHERE id = '".$_id_sequence."'";
              $query_seq      = mysqli_query($GLOBALS["mysqli"], $sql_seq);
              $rep_seq        = mysqli_fetch_array($query_seq);
              $aff_titre_seq  = '<p class="bold"> - <em>' . $rep_seq["titre"] . '</em> - </p>';
            }

			/*
            $content = "<div class=\"matiere_a_faire couleur_bord_tableau_notice couleur_cellule_f color_police_matieres\">\n
                      <h4 class=\"souligne\">".$matiere_devoirs." (".$chaine."):</h4>\n".$aff_titre_seq."".$content;
			*/
			// 20130727
			$class_color_fond_notice="couleur_cellule_f";
			if($CDTPeutPointerTravailFait) {
				get_etat_et_img_cdt_travail_fait($id_devoirs);
			}

            $content_ini=$content;

            $content = "<div id='div_travail_".$id_devoirs."' class=\"matiere_a_faire couleur_bord_tableau_notice $class_color_fond_notice color_police_matieres\">\n<h4 class=\"souligne\">".$matiere_devoirs." (".$chaine."):</h4>\n";

			if($CDTPeutPointerTravailFait) {
                 $content.="<div id='div_etat_travail_".$id_devoirs."' style='float:right; width: 16px; margin: 2px; text-align: center;'><a href=\"javascript:cdt_modif_etat_travail('".$selected_eleve->login."', '".$id_devoirs."')\" title=\"$texte_etat_travail\"><img src='$image_etat' class='icone16' /></a></div>\n";
			}

			/*
			if($special_devoirs=="controle") {
				$content.="<div style='float:right; width:16px;'><img src='$gepiPath/images/icons/flag2.gif' class='icone16' alt='Contrôle' title=\"Un contrôle/évaluation est programmé pour le ".my_strftime("%A %d/%m/%Y", $date_devoirs)."\" /></div>";
			}
			*/
			$chaine_tag=get_liste_tag_notice_cdt($id_devoirs, 't', "right");
			if($chaine_tag!="") {
				//$content.="<div style='float:right; width:16px;'>".$chaine_tag."</div>";
				$content.=$chaine_tag;
			}

            $content .= $aff_titre_seq.$content_ini;

            // fichier joint
            $content .= affiche_docs_joints($id_devoirs,"t");
            $content .="</div>";
            if ($nb_devoirs_cahier_texte != 0)
                echo $content;
            $ind++;
          }
        	//echo "</div>";
        }
    }

    if ($nb_dev != 0) {echo "</div>";}

	echo "<hr />\n";
	echo "<p style='text-align:center; font-style:italic;'>Cahiers de textes du ";
	echo strftime("%d/%m/%Y", getSettingValue("begin_bookings"));
	echo " au ";
	echo strftime("%d/%m/%Y", getSettingValue("end_bookings"));
	echo "</p>\n";

	require("../lib/footer.inc.php");
    die();
    //Affichage page de garde
} elseif ($nb_test == 0) {
	//echo "<center>"; correction Régis : balise <center> dépréciée
	if ($_SESSION['statut'] == "responsable") {
		echo "<p class='gepi_garde'>Choisissez un élève et une matière.</p>\n"; //correction Régis : h3 doit venir après h1 et h2
	} elseif ($_SESSION['statut'] == "eleve") {
		echo "<p class='gepi_garde'>Choisissez une matière</p>\n";
	} else {
		echo "<p class='gepi_garde'>Choisissez une classe et une matière.</p>\n";
	}
	//echo "</center>";

	echo "<hr />\n";
	echo "<p style='text-align:center; font-style:italic;'>Cahiers de textes du ";
	echo strftime("%d/%m/%Y", getSettingValue("begin_bookings"));
	echo " au ";
	echo strftime("%d/%m/%Y", getSettingValue("end_bookings"));
	echo "</p>\n";

	require("../lib/footer.inc.php");
	die();
}
//echo "______________";
// Affichage des comptes rendus et des travaux à faire.

// Modification Regis : mise en page sur 2 colonnes par CSS

// echo "<table width=\"98%\" border=\"0\" align=\"center\">\n";
// echo "<table class=\"centre_cont_texte\" summary=\"Tableau des comptes rendus de travaux à effectuer\">\n";

// ---------------------------- Début du conteneur 2 colonnes (div) ----

echo "<div class=\"centre_cont_texte\">\n";

// Première colonne : affichage du 'travail à faire' à venir
//echo "<tr><td width=\"30%\" valign=\"top\">\n";
// correction Regis : mise en page déplacée dans ccs
//   echo "<tr>\n";

// ---------------------------- Début de la colonne de gauche (div div)  ----

    echo "<div class=\"cct_gauche\">\n";
	 // ?????????????????????????????????????????????????????????
// ---------------------------- Lien vers see_all.php  ----
	   echo "<a href='see_all.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe'>Voir l'ensemble du cahier de textes de ".$matiere_nom."</a>\n<br />\n";
	// Cela provoque une déconnexion de l'élève et le compte est rendu 'inactif'???
	// ?????????????????????????????????????????????????????????

// ---------------------------- Affichage des devoirs  ---

      if ($delai == "") die("Erreur : Délai de visualisation des devoirs non défini. Contactez l'administrateur de GEPI de votre établissement.");
// Si l'affichage des devoirs est activée, on affiche les devoirs
      if ($delai != 0) {
// Affichage de la semaine en cours
        $nb_dev = 0;
        for ($i = 0; $i <= $delai; $i++) {
          $jour = mktime(0, 0, 0, date('m',$today), (date('d',$today) + $i), date('Y',$today) );
          $jour_suivant=$jour+24*3600;
        // On regarde pour chaque jour, s'il y a des devoirs dans à faire
          if ($selected_eleve) {
			// On détermine la période active, pour ne pas avoir de duplication des entrées
			// Le DISTINCT est quand même utile parce que si plusieurs périodes sont ouvertes en saisie, on a une multiplication des retours par le nombre de périodes ouvertes en saisie
		//$sql="SELECT DISTINCT ct.id_sequence, ct.contenu, g.id, g.description, ct.date_ct, ct.id_ct, ct.special " .
	         $sql="SELECT DISTINCT ct.id_sequence, ct.contenu, g.id, g.description, ct.date_ct, ct.id_ct " .
                "FROM ct_devoirs_entry ct, groupes g, j_eleves_groupes jeg, j_eleves_classes jec, periodes p WHERE (" .
                "ct.id_groupe = jeg.id_groupe and " .
                "g.id = jeg.id_groupe and " .
                "jeg.login = '" . $selected_eleve->login . "' and " .
                "jeg.periode = p.num_periode and " .
                "jeg.periode = jec.periode and " .
                "p.verouiller = 'N' and " .
                "p.id_classe = jec.id_classe and " .
                "jec.login = '" . $selected_eleve->login ."' and " .
                "ct.contenu != '' and " .
                "ct.date_ct >= '$jour' and
	             ct.date_ct < '$jour_suivant'
	            );";
          } else {
		//$sql="SELECT ct.id_sequence, ct.contenu, g.id, g.description, ct.date_ct, ct.id_ct, ct.special " .
	         $sql="SELECT ct.id_sequence, ct.contenu, g.id, g.description, ct.date_ct, ct.id_ct " .
	             "FROM ct_devoirs_entry ct, groupes g, j_groupes_classes jgc WHERE (" .
	             "ct.id_groupe = jgc.id_groupe and " .
	             "g.id = jgc.id_groupe and " .
	             "jgc.id_classe = '" . $id_classe . "' and " .
	             "ct.contenu != '' and " .
	             "ct.date_ct >= '$jour' and
	             ct.date_ct < '$jour_suivant'
	            );";
          }
		//echo strftime("%a %d/%m/%y",$jour)."<br />";
		//echo "$sql<br /><br />";
		$appel_devoirs_cahier_texte = mysqli_query($GLOBALS["mysqli"], $sql);
          $nb_devoirs_cahier_texte = mysqli_num_rows($appel_devoirs_cahier_texte);
          $ind = 0;
          if ($nb_devoirs_cahier_texte != 0) {
            $nb_dev++;
            if ($nb_dev == '1') {
              if ((my_strftime("%a",$today) == "lun") or (my_strftime("%a",$today) == "lun.")) {$debutsemaine = $today;}
              if ((my_strftime("%a",$today) == "mar") or (my_strftime("%a",$today) == "mar.")) {$debutsemaine = mktime(0, 0, 0, date('m',$today), (date('d',$today) - 1), date('Y',$today) );}
              if ((my_strftime("%a",$today) == "mer") or (my_strftime("%a",$today) == "mer.")) {$debutsemaine = mktime(0, 0, 0, date('m',$today), (date('d',$today) - 2), date('Y',$today) );}
              if ((my_strftime("%a",$today) == "jeu") or (my_strftime("%a",$today) == "jeu.")) {$debutsemaine = mktime(0, 0, 0, date('m',$today), (date('d',$today) - 3), date('Y',$today) );}
              if ((my_strftime("%a",$today) == "ven") or (my_strftime("%a",$today) == "ven.")) {$debutsemaine = mktime(0, 0, 0, date('m',$today), (date('d',$today) - 4), date('Y',$today) );}
              if ((my_strftime("%a",$today) == "sam") or (my_strftime("%a",$today) == "sam.")) {$debutsemaine = mktime(0, 0, 0, date('m',$today), (date('d',$today) - 5), date('Y',$today) );}
              if ((my_strftime("%a",$today) == "dim") or (my_strftime("%a",$today) == "dim.")) {$debutsemaine = mktime(0, 0, 0, date('m',$today), (date('d',$today) - 6), date('Y',$today) );}
              $finsemaine = mktime(0, 0, 0, date('m',$debutsemaine), (date('d',$debutsemaine) + 6), date('Y',$debutsemaine) );
 //echo "<p><strong><font color='blue' style='font-variant: small-caps;'>Semaine du ".strftime("%d %B", $debutsemaine)." au ".strftime("%d %B %Y", $finsemaine)."</font></strong></p>\n";
//echo "<strong>Travaux personnels des $delai prochains jours</strong>\n";
//echo "<table style=\"border-style:solid; border-width:0px; border-color: ".$couleur_bord_tableau_notice.";\" width = '100%' cellpadding='2'><tr><td>\n";

// ---------------------------- Affichage de la semaine et du titre  ---

// Correction Régis : ajout de class pour gérer la mise en page + <strong> à la place de <strong>
              echo "<p class=\"sem_du_au\"><strong>Semaine du ".my_strftime("%d %B", $debutsemaine)." au ".my_strftime("%d %B %Y", $finsemaine)."</strong></p>\n";
              echo "<h2 class='h2_label'><strong>Travaux personnels des $delai prochains jours</strong></h2>\n";

// ---------------------------- Affichage des travaux à faire (div div div)  ---

//              echo "<div class=\"a_faire_gauche\">\n";
              echo "<div class='cel_trav_futur couleur_bord_tableau_notice color_fond_notices_f color_police_travaux'>\n";
//                echo "<tr>\n";
//                  echo "<div>\n";
            }

            //echo "<div style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: ".$color_fond_notices["f"].";\"><div style='color: ".$color_police_travaux."; font-variant: small-caps; text-align: center; font-weight: bold;'>Travaux personnels<br />pour le ".my_strftime("%a %d %b", $jour)."</div>\n";
            echo "<h3 class='titre_a_faire color_police_travaux'>Travaux personnels pour le<br />".my_strftime("%a %d %b", $jour)."</h3>\n";

            // Affichage des devoirs dans chaque matière
            while ($ind < $nb_devoirs_cahier_texte) {
              $content = old_mysql_result($appel_devoirs_cahier_texte, $ind, 'contenu');
              $date_devoirs       = old_mysql_result($appel_devoirs_cahier_texte, $ind, 'date_ct');
              $id_devoirs         = old_mysql_result($appel_devoirs_cahier_texte, $ind, 'id_ct');
              $id_groupe_devoirs  = old_mysql_result($appel_devoirs_cahier_texte, $ind, 'id');
              $matiere_devoirs    = old_mysql_result($appel_devoirs_cahier_texte, $ind, 'description');
              $_id_sequence       = old_mysql_result($appel_devoirs_cahier_texte, $ind, 'id_sequence');
              //$special_devoirs       = old_mysql_result($appel_devoirs_cahier_texte, $ind, 'special');

              $test_prof = "SELECT nom, prenom,u.login FROM j_groupes_professeurs j, utilisateurs u WHERE (j.id_groupe='".$id_groupe_devoirs."' and u.login=j.login) ORDER BY nom, prenom";
              $res_prof = sql_query($test_prof);
              $chaine = "";
              for ($k=0;$prof=sql_row($res_prof,$k);$k++) {
                if ($k != 0) $chaine .= ", ";
                //$chaine .= htmlspecialchars($prof[0])." ".mb_substr(htmlspecialchars($prof[1]),0,1).".";
              $chaine.=affiche_utilisateur($prof[2],$selected_eleve_classe);
              }

              // On ajoute le nom de la séquence si elle existe
              // On n'utilise pas les objets propel pour ne pas surcharger mais il faudra réécrire avec
              $aff_titre_seq = NULL;
              if ($_id_sequence != '0'){
                $sql_seq        = "SELECT titre FROM ct_sequences WHERE id = '".$_id_sequence."'";
                $query_seq      = mysqli_query($GLOBALS["mysqli"], $sql_seq);
                $rep_seq        = mysqli_fetch_array($query_seq);
                $aff_titre_seq  = '<p class="bold"> - <em>' . $rep_seq["titre"] . '</em> - </p>';
              }

		/*
		$temoin_controle="";
		if($special_devoirs=="controle") {
			$temoin_controle.="<div style='float:right; width:16px;'><img src='$gepiPath/images/icons/flag2.gif' class='icone16' alt='Contrôle' title=\"Un contrôle/évaluation est programmé pour le ".my_strftime("%A %d/%m/%Y", $date_devoirs)."\" /></div>";
		}
		*/

		$chaine_tag=get_liste_tag_notice_cdt($id_devoirs, 't', "");

// Correction Régis : ajout de class pour gérer la mise en page
              $content = "<div class='matiere_a_faire couleur_bord_tableau_notice couleur_cellule_f color_police_matieres'>\n
                  <h4 class='a_faire_titre color_police_matieres'>".$matiere_devoirs." (".$chaine.") :</h4>".$aff_titre_seq."\n<div class='txt_gauche'>\n".$chaine_tag.$content;
              // fichier joint
              $content .= affiche_docs_joints($id_devoirs,"t");
              $content .="</div>\n</div>\n";
              if ($nb_devoirs_cahier_texte != 0) echo $content;
              $ind++;
            }
			 //echo "</div><br />\n";
			 //echo "</div>\n";
        }
      }
      //if ($nb_dev != 0) echo "</td>\n</tr>\n</table>\n";
      if ($nb_dev != 0) echo "</div>\n";
    }
// ---------------------------- Fin Affichage des travaux à faire (div div /div) ---

// ---------------------------- Affichage des informations générales (div div div) ---
$infos_generales="";
$sql="SELECT contenu, id_ct  FROM ct_entry WHERE (id_groupe='$id_groupe' and date_ct='');";
//echo "$sql<br />";
$appel_info_cahier_texte = mysqli_query($GLOBALS["mysqli"], $sql);
$nb_cahier_texte = mysqli_num_rows($appel_info_cahier_texte);
while($lig_ct=mysqli_fetch_object($appel_info_cahier_texte)) {

	$content=$lig_ct->contenu;
	if($content!="") {
		$id_ct = $lig_ct->id_ct;
		$content .= affiche_docs_joints($id_ct,"c");

		$infos_generales.="<div class='see_all_general couleur_bord_tableau_notice color_fond_notices_i' style='width:93%; margin-top:1em; padding:0.5em;'>";
		$infos_generales.=$content;
		$infos_generales.="</div>";
	}

}

//if ($content != '') {
if ($infos_generales != '') {
	echo "<h2 class='grande_ligne couleur_bord_tableau_notice'>\n<strong>INFORMATIONS GENERALES</strong>\n</h2>\n";
	echo $infos_generales;
}
    echo "</div>\n";
// ----------------------------  Fin de la colonne de gauche (div /div) ---


// ----------------------------  Début de la deuxième de droite (div div) ---
//echo "<td valign=\"top\">";
    echo "<div class=\"cct_droit\">\n";
// ----------------------------  Titre (div div div) --
            echo "<div class='titre_notice'>\n";
              echo "<h2 class='h2_label'><strong>les dix dernières séances jusqu'au ".my_strftime("%A %d %B %Y", $today)." :</strong></h2>\n";
            echo "</div>\n";
// ----------------------------  Fin titre (div div /div) --

// ----------------------------  Dates (div div div) --
      echo "<div class='cdt_dates'>\n";
// Première ligne
//echo "<tr><td style=\"width:50%\"><strong>" . strftime("%A %d %B %Y", $today) . "</strong>";
//        echo "<tr>\n";
// ----------------------------  Date du jour (div div div div) --
//          echo "<div class='cdt_dates_jour'>\n";
//            echo "<strong>" . strftime("%A %d %B %Y", $today) . "</strong>\n";
//          echo "</div>\n";
// ----------------------------  Fin date du jour (div div div /div) --

#y? sont les année, mois et jour précédents
#t? sont les année, mois et jour suivants
$i= mktime(0,0,0,$month,$day-1,$year);
$yy = date("Y",$i);
$ym = date("m",$i);
$yd = date("d",$i);
$i= mktime(0,0,0,$month,$day+1,$year);
$ty = date("Y",$i);
$tm = date("m",$i);
$td = date("d",$i);
//echo "</td>\n<td><a title=\"Aller au jour précédent\" href=\"consultation.php?year=$yy&amp;month=$ym&amp;day=$yd&amp;id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe\"><img src='".$gepiPath."/images/icons/back.png' alt='Jour précédent'></a></td>\n<td align=\"center\"><a href=\"consultation.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe\">Aujourd'hui</a></td>\n<td align=\"right\"><a title=\"Aller au jour suivant\" href=\"consultation.php?year=$ty&amp;month=$tm&amp;day=$td&amp;id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe\"><img src='".$gepiPath."/images/icons/forward.png' alt='Jour suivant'></a></td>\n</tr>\n";
// correction Régis : mise en page dans CSS
// ----------------------------  Jour précédent (div div div div) --
          echo "<div class='cdt_dates_precedent'>\n";
            echo "<a title=\"Aller au jour précédent\" href=\"consultation.php?year=$yy&amp;month=$ym&amp;day=$yd&amp;id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe\">\n";
              echo "<img src='".$gepiPath."/images/icons/back.png' alt='Jour précédent' />\n";
            echo "</a>\n";
          echo "</div>\n";
// ----------------------------  Fin jour précédent (div div div /div) --

// ----------------------------  Aujourd'hui (div div div div) --
          echo "<div class=\"cdt_dates_aujourdhui\">\n";
            echo "<a href=\"consultation.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe\">\n";
              echo "Aujourd'hui\n";
            echo "</a>";
          echo "</div>\n";
// ----------------------------  Fin aujourd'hui (div div div /div) --

// ----------------------------  Jour suivant (div div div div) --
          echo "<div class=\"cdt_dates_suivant droite_texte\">\n";
            echo "<a title=\"Aller au jour suivant\" href=\"consultation.php?year=$ty&amp;month=$tm&amp;day=$td&amp;id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe\">\n";
              echo "<img src='".$gepiPath."/images/icons/forward.png' alt='Jour suivant' />\n";
            echo "</a>\n";
          echo "</div>\n";
// ----------------------------  Fin jour suivant (div div div /div) --
        echo "</div>\n";
// ----------------------------  Fin dates (div div /div) --

// ----------------------------  Notices 1 (div div div) --
//        echo "<div>\n";
//        echo "</tr>\n";

// affichage du texte
//        echo "<tr>\n";
          //  echo "<div>\n";
// echo "<center><strong>les dix dernières séances jusqu'au ".strftime("%A %d %B %Y", $today)." :</strong></center></td>\n</tr>\n";
// echo "<tr><td colspan=\"4\" style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background: rgb(199, 255, 153); padding: 2px; margin: 2px;\">";
// echo "<tr>\n<td colspan=\"4\" style=\"border-style:solid; border-width:0px; border-color: ".$couleur_bord_tableau_notice."; padding: 2px; margin: 2px;\">\n";

			   // correction Régis : mise en page dans CSS
            // echo "<div class=\"centre_texte\">\n";
              // echo "<h2 class='h2_label'><strong>les dix dernières séances jusqu'au ".strftime("%A %d %B %Y", $today)." :</strong></h2>\n";
           //  echo "</div>\n";
          //  echo "</div>\n";
//        echo "</tr>\n";
//echo "<tr><td colspan=\"4\" style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background: rgb(199, 255, 153); padding: 2px; margin: 2px;\">";
//        echo "<tr>\n";

// ---------------------------- Toutes les notices (div div div div) --
          echo "<div class=\"ct_jour couleur_bord_tableau_notice\">\n";

          $req_notices =
              "select 'c' type, contenu, date_ct, id_ct, id_sequence
              from ct_entry
              where (contenu != ''
              and id_groupe='$id_groupe'
              and date_ct <= '$today'";
          if ($_SESSION["statut"] == 'eleve' OR $_SESSION["statut"] == 'responsable') {
              $req_notices.="and date_ct <= '".time()."'";
          }
          $req_notices.="
              and date_ct != ''
              and date_ct >= '".getSettingValue("begin_bookings")."')
              ORDER BY date_ct DESC, heure_entry DESC limit 10";
          $res_notices = mysqli_query($GLOBALS["mysqli"], $req_notices);
          $notice = mysqli_fetch_object($res_notices);

          //    "select 't' type, contenu, date_ct, id_ct, id_sequence, special
          $req_devoirs =
              "select 't' type, contenu, date_ct, id_ct, id_sequence 
              from ct_devoirs_entry
              where (contenu != ''
              and id_groupe = '".$id_groupe."'
              and date_ct != ''
              and date_ct <= '$today'
              and date_ct >= '".getSettingValue("begin_bookings")."'
              and date_ct <= '".getSettingValue("end_bookings")."'
              ) order by date_ct DESC limit 10";
          $res_devoirs = mysqli_query($GLOBALS["mysqli"], $req_devoirs);
          $devoir = mysqli_fetch_object($res_devoirs);

          // Boucle d'affichage des notices dans la colonne de gauche
          $date_ct_old = -1;
           while (true) {
            // On met les notices du jour avant les devoirs à rendre aujourd'hui
            if ($notice && (!$devoir || $notice->date_ct >= $devoir->date_ct)) {
                // Il y a encore une notice et elle est plus récente que le prochain devoir, où il n'y a plus de devoirs
                $not_dev = $notice;
                $notice = mysqli_fetch_object($res_notices);
              } elseif($devoir) {
                // Plus de notices et toujours un devoir, ou devoir plus récent
                $not_dev = $devoir;
                $devoir = mysqli_fetch_object($res_devoirs);
              } else {
                // Plus rien à afficher, on sort de la boucle
                break;
              }
              $content=$not_dev->contenu;
              $content .= affiche_docs_joints($not_dev->id_ct,$not_dev->type);
              $titre = "";
              if ($not_dev->type == "t") {
                $titre .= "<strong>A faire pour le : </strong>\n";
              }
              $titre .= "<strong>" . my_strftime("%a %d %b %y", $not_dev->date_ct) . "</strong>\n";
              // Numérotation des notices si plusieurs notice sur la même journée
            if ($not_dev->type == "c") {
              if ($date_ct_old == $not_dev->date_ct) {
                $num_notice++;
                $titre .= " <stong><em>(notice N° ".$num_notice.")</em></strong>";
              } else {
                // on afffiche "(notice N° 1)" uniquement s'il y a plusieurs notices dans la même journée
                $nb_notices = sql_query1("SELECT count(id_ct) FROM ct_entry WHERE (id_groupe='" . $current_group["id"] ."' and date_ct='".$not_dev->date_ct."')");
                if ($nb_notices > 1) $titre .= " <strong><em>(notice N° 1)</em></strong>";
                //$titre .= " <strong><i>(notice N° 1)</i></strong>";
                // On réinitialise le compteur
                $num_notice = 1;
              }
            }
            // On ajoute le nom de la séquence si elle existe
            // On n'utilise pas les objets propel pour ne pas surcharger mais il faudra réécrire avec
            $aff_titre_seq = NULL;
            if ($not_dev->id_sequence != '0'){
              $sql_seq        = "SELECT titre FROM ct_sequences WHERE id = '".$not_dev->id_sequence."'";
              $query_seq      = mysqli_query($GLOBALS["mysqli"], $sql_seq);
              $rep_seq        = mysqli_fetch_array($query_seq);
              $aff_titre_seq  = '<p class="bold"> - <em>' . $rep_seq["titre"] . '</em> - </p>';
            }
// ---------------------------- contenu chaque notice (div div div div div) --
            echo "<div class='cdt_une_notice '>\n";
//            echo "<tr>\n";
// ---------------------------- Titre notices (div div div div div div) --
// choisir le fond en fonction de $devoir ou $notice

             if ($not_dev->type == "c") {
                echo "<div class='cdt_titre_not_dev couleur_bord_tableau_notice color_fond_notices_".$not_dev->type."'>";

			$chaine_tag=get_liste_tag_notice_cdt($not_dev->id_ct, $not_dev->type);
			if($chaine_tag!="") {
				echo "<div style='float:right; width:16px;'>".$chaine_tag."</div>";
			}
             }
             else {
				// 20130727
				//$class_color_fond_notice="color_fond_notices_t";
				$class_color_fond_notice="couleur_cellule_gen";
				if($CDTPeutPointerTravailFait) {
					get_etat_et_img_cdt_travail_fait($not_dev->id_ct);
				}

				echo "<div class='cdt_titre_not_dev couleur_bord_tableau_notice color_fond_notices_t' style='min-height:2em;'>";

				if($CDTPeutPointerTravailFait) {
					echo "<div id='div_etat_travail_".$not_dev->id_ct."' style='float:right; width: 16px; margin: 2px; text-align: center;'><a href=\"javascript:cdt_modif_etat_travail('$selected_eleve_login', '".$not_dev->id_ct."')\" title=\"$texte_etat_travail\"><img src='$image_etat' class='icone16' /></a></div>\n";
				}


				/*
				if($not_dev->special=="controle") {
					echo "<div style='float:right; width:16px;'><img src='$gepiPath/images/icons/flag2.gif' class='icone16' alt='Contrôle' title=\"Un contrôle/évaluation est programmé pour le ".my_strftime("%A %d/%m/%Y", $not_dev->date_ct)."\" /></div>";
				}
				*/

				$chaine_tag=get_liste_tag_notice_cdt($not_dev->id_ct, $not_dev->type, "right");
				if($chaine_tag!="") {
					//echo "<div style='float:right; width:16px;'>".$chaine_tag."</div>";
					echo $chaine_tag;
				}

             }

             /* if ($not_dev->type == "c") {
                echo "c'>";
              } else {
              	 echo "t'>";
              }*/
              echo "<h3>\n".$titre."</h3>".$aff_titre_seq."\n</div>\n";
// ---------------------------- Fin titre notices (div div div div div /div) --
//            echo "</tr>\n";
// ---------------------------- contenu notices (div div div div div div) --
//            echo "<tr>\n";
             if ($not_dev->type == "c") {
                 echo "<div class='cdt_fond_not_dev couleur_cellule_gen'>".$content."</div>\n";
              }
              else {
                 echo "<div id='div_travail_".$not_dev->id_ct."' class='cdt_fond_not_dev $class_color_fond_notice'>".$content."</div>\n";
              }
// ---------------------------- Fin contenu notices (div div div div div /div) --
//            echo "</tr>\n";
           echo "</div>\n";
// ---------------------------- Fin contenu chaque notice (div div div div /div) --
//           echo "<br />\n";
           if ($not_dev->type == "c") $date_ct_old = $not_dev->date_ct;
          }
          echo "</div>\n";
// ---------------------------- Fin toutes les notices (div div div /div) --
//        echo "</tr>\n";
//      echo "</div>\n";
// ---------------------------- Fin notices 1 (div div /div) --
    echo "</div>\n";
// ---------------------------- Fin de la colonne de droite (div /div) ---
//   echo "</tr>\n";
echo "</div>\n";
// ---------------------------- Fin du conteneur 2 colonnes (/div) --

	echo "<hr />\n";
	echo "<p style='text-align:center; font-style:italic;'>Cahiers de textes du ";
	echo strftime("%d/%m/%Y", getSettingValue("begin_bookings"));
	echo " au ";
	echo strftime("%d/%m/%Y", getSettingValue("end_bookings"));
	echo "</p>\n";

	require("../lib/footer.inc.php");
?>
