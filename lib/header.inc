<?php
/*
 * $Id$
 *
 * Basé sur  header.inc 2968 2009-03-03 20:11:00Z jjocal $
 * modifié avec getSettingValue("contact_admin_mailto")=='y'
 
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Patrick Duthilleul
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

function last_connection() {
	global $gepiPath;
   $sql = "select START, AUTOCLOSE, REMOTE_ADDR from log where LOGIN = '".$_SESSION['login']."' and SESSION_ID != '".session_id()."' order by START desc";
   $res = sql_query($sql);
   $r = '';
   if ($res) {
      $row = sql_row($res, 0);
      $annee_b = substr($row[0],0,4);
      $mois_b =  substr($row[0],5,2);
      $jour_b =  substr($row[0],8,2);
      $heures_b = substr($row[0],11,2);
      $minutes_b = substr($row[0],14,2);
      $secondes_b = substr($row[0],17,2);
      if ($row[0]  != '') {
          if ($row[1]  == "4") {
              $r = "<span style=\"color: red\"><strong>Tentative de connexion le ".$jour_b."/".$mois_b."/".$annee_b." à ".$heures_b." h ".$minutes_b. " avec un mot de passe erroné</strong></span> (<a href='".$gepiPath."/utilisateurs/mon_compte.php#connexion'".insert_confirm_abandon().">journal des connexions</a>)";
              // On compte le nombre de tentatives infructueuses successives
              $nb_tentative = 0;
              $flag = 0;
              for ($i = 0; (($row_b = sql_row($res, $i)) and ($flag < 1)); $i++)
              {
				if (($row_b[1]  == "2") and ($row_b[2]  == $row[2])) {
					$nb_tentative++;
				}
				else {
					$flag = 1;
				}
              }
              if ($nb_tentative > 1) {$r .= "<br /><strong>Nombre de tentatives de connexion successives : ".$nb_tentative.".</strong></font>";}
          } else {
              $r = "  Dernière session ouverte le ".$jour_b."/".$mois_b."/".$annee_b." à ".$heures_b." h ".$minutes_b. " (<a href='".$gepiPath."/utilisateurs/mon_compte.php#connexion'".insert_confirm_abandon().">journal des connexions</a>)";
			}
      }
    }
    return $r;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta http-equiv="refresh" content="<?php echo getSettingValue("sessionMaxLength")*60; ?>; URL=<?php echo($gepiPath); ?>/logout.php?auto=3&amp;debut_session=<?php echo urlencode($_SESSION['start']);?>&amp;session_id=<?php echo session_id();?>" />
<!-- correction Regis : déclaration par défaut pour les scripts et les mises en page-->
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<!--fin correction Regis html xmlns + majuscules -->

<title><?php echo getSettingValue("gepiSchoolName"); ?> : base de données élèves</title>

<!--correction Regis javascript -->
<script type="text/javascript">
<?php if (isset($affiche_message) and ($affiche_message == 'yes')) { ?>
<!--
alert("<?php echo $message_enregistrement; ?>");
//-->
<?php } ?>
function changement() {
change = 'yes';
}
</script>


<!-- Gestion de l'expiration des session - Patrick Duthilleul -->
<!--correction Regis javascript -->
<script type="text/javascript">
<!--
var debut=new Date()
function show_message_deconnexion() {
  var seconds_before_alert = 180;
  var seconds_int_betweenn_2_msg = 30;

  var digital=new Date()
  var seconds=(digital-debut)/1000
  if (seconds><?php echo getSettingValue("sessionMaxLength")*60; ?> - seconds_before_alert) {
    var seconds_reste = Math.floor(<?php echo (getSettingValue("sessionMaxLength"))*60; ?> - seconds);
    now=new Date()
    var hrs=now.getHours();
    var mins=now.getMinutes();
    var secs=now.getSeconds();

    var heure = hrs + " H " + mins + "' " + secs + "'' ";
    alert("A "+ heure + ", il vous reste moins de 3 minutes avant d'être déconnecté ! \nPour éviter cela, rechargez cette page en ayant pris soin d'enregistrer votre travail !");
  }
  setTimeout("show_message_deconnexion()",seconds_int_betweenn_2_msg*1000)
}
//-->
</script>

<!-- christian -->
<!--correction Regis javascript -->
<script type="text/javascript">
    function ouvre_popup(url) {
        eval("window.open('<?php echo($gepiPath); ?>/mod_miseajour/utilisateur/fenetre.php','fen','width=600,height=500,menubar=no,scrollbars=yes')");
        fen.focus();
    }
</script>

<?php


// ====================== Affichage du favicon ========================
// correction Regis LINK REL -> link rel
echo '
<link rel="SHORTCUT ICON" href="'.$gepiPath.'/favicon.ico" />
	'."\n";
// ====================== Fin affichage favicon =======================

// ====================== Affichage des javascripts ===================
echo '	<!-- Début des fichiers en javascript -->'."\n";
echo '<script type="text/javascript" src="'.$gepiPath.'/lib/functions.js"></script>'."\n";

// Ajout du framework prototype 1.6.0.3
echo '<script type="text/javascript" src="'.$gepiPath.'/lib/prototype.js"></script>'."\n";

// Ajout de la librairie Scriptaculous.js conditionné à la variable $utilisation_scriptaculous="ok"
$scriptaculous = isset($utilisation_scriptaculous) ? $utilisation_scriptaculous : NULL;
$script_effet = isset($scriptaculous_effet) ? $scriptaculous_effet : NULL;
if ($scriptaculous == "ok") {
  // On affiche le lien qui charge scriptaculous
  echo '<script type="text/javascript" src="'.$gepiPath.'/lib/scriptaculous.js?load='.$script_effet.'"></script>'."\n";
}
// Utilisation de windows.js
$windows = isset($utilisation_win) ? $utilisation_win : NULL;
if ($windows == 'oui') {
 	echo '
	<script type="text/javascript" src="'.$gepiPath.'/edt_effets/javascripts/effects.js"> </script>
	<script type="text/javascript" src="'.$gepiPath.'/edt_effets/javascripts/window.js"> </script>
	<script type="text/javascript" src="'.$gepiPath.'/edt_effets/javascripts/window_effects.js"> </script>
	<link href="'.$gepiPath.'/edt_effets/themes/default.css" rel="stylesheet" type="text/css"/>
	<link href="'.$gepiPath.'/edt_effets/themes/alphacube.css" rel="stylesheet" type="text/css"/>
	 ';
 }
// Utilisation de tablekit
$tablekit = isset($utilisation_tablekit) ? $utilisation_tablekit : NULL;
if ($tablekit == "ok") {
  // On affiche le lien qui charge tablekit
  echo '<script type="text/javascript" src="'.$gepiPath.'/lib/tablekit.js"></script>'."\n";
}
// js spécifique
if(isset($javascript_specifique)) {
	// Il faudrait filtrer le contenu de la variable...
	// On ajoute le ".js" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence

	if(is_array($javascript_specifique)) {
		foreach($javascript_specifique as $current_javascript_specifique) {
			if(strlen(my_ereg_replace("[A-Za-z0-9_/\-]","",$current_javascript_specifique))==0) {
				// Javascript spécifique à une page:
				echo "<script type='text/javascript' src='$gepiPath/$current_javascript_specifique.js'></script>\n";
			}
		}
	}
	else {
		if(strlen(my_ereg_replace("[A-Za-z0-9_/\-]","",$javascript_specifique))==0) {
			// Javascript spécifique à une page:
			echo "<script type='text/javascript' src='$gepiPath/$javascript_specifique.js'></script>\n";
		}
	}
}
// On affiche tout le temps brainjar sauf quand on dit à Gepi de ne pas le faire
$utilisation_jsdivdrag = isset($utilisation_jsdivdrag) ? $utilisation_jsdivdrag : NULL;
if (isset($utilisation_jsdivdrag) AND $utilisation_jsdivdrag == "non") {
	echo "<!-- Pas de brainjar-->\n";
} else {
	//===================================
	// Pour aérer les infobulles si jamais Javascript n'est pas actif.
	// Sinon, avec le position:absolute, les div se superposent.
	$posDiv_infobulle=0;
	// $posDiv_infobulle permet de fixer la position horizontale initiale du Div.

	$tabdiv_infobulle=array();
	$tabid_infobulle=array();

	// Choix de l'unité pour les dimensions des DIV: em, px,...
	$unite_div_infobulle="em";
	// Pour l'overflow dans les DIV d'aide, il vaut mieux laisser 'em'.

	// Variable passée à 'ok' en fin de page via le /lib/footer.inc.php
	echo "<script type='text/javascript'>
		var temporisation_chargement='n';
	</script>\n";

	echo "<script type='text/javascript' src='$gepiPath/lib/brainjar_drag.js'></script>\n";
	echo "<script type='text/javascript' src='$gepiPath/lib/position.js'></script>\n";


} // if(isset($utilisation_jsdivdrag...




echo "\n".'	<!-- fin des fichiers en javascript -->'."\n\n";
// ======================= fin de l'affichage des javascripts ========================
// ======================= Début de l'affichage des feuilles de style ================
echo '	<!-- Début des styles -->'."\n";
$style = getSettingValue("gepi_stylesheet");
if (empty($style)) $style = "style";

echo '<link rel="stylesheet" type="text/css" href="'.$gepiPath.'/'.$style.'.css" />'."\n";
// Couleur de fond des pages
if (!isset($titre_page)) $bgcouleur = "bgcolor= \"#FFFFFF\""; else $bgcouleur = "";


if(isset($style_specifique)) {
	// Il faudrait filtrer le contenu de la variable...
	// ne doit contenir que certains types de caractères et se terminer par .css
	// Non... on ajoute le ".css" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence

	if(is_array($style_specifique)) {
		foreach($style_specifique as $current_style_specifique) {
			if(strlen(my_ereg_replace("[A-Za-z0-9_/]","",$current_style_specifique))==0) {
				// Styles spécifiques à une page:
				echo "<link rel='stylesheet' type='text/css' href='$gepiPath/$current_style_specifique.css' />\n";
			}
		}
	}
	else {
		if(strlen(my_ereg_replace("[A-Za-z0-9_/]","",$style_specifique))==0) {
			// Styles spécifiques à une page:
			echo "<link rel='stylesheet' type='text/css' href='$gepiPath/$style_specifique.css' />\n";
		}
	}
}

   // vérifie si on est dans le modules absences
   $files = array("gestion_absences", "select", "ajout_abs", "ajout_ret", "ajout_dip", "ajout_inf", "tableau", "impression_absences", "prof_ajout_abs", "statistiques", "alert_suivi", "admin_config_semaines", "admin_motifs_absences", "admin_horaire_ouverture", "admin_actions_absences", "admin_periodes_absences");
    if(in_array(basename($_SERVER['PHP_SELF'],".php"), $files)) {
        ?><link rel='stylesheet' type='text/css' href='<?php echo $gepiPath; ?>/mod_absences/styles/mod_absences.css' /><?php
    }


// Voir si on restreint l'usage aux pages du module cahier de textes
// Restreint aux pages modifiées pour une meilleure accessibilité
// Voir si on restreint l'usage aux pages du module cahier de textes
// Restreint aux pages modifiées pour une meilleure accessibilité

if(isset($accessibilite)) {
	if($accessibilite=="y") {
		echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/accessibilite.css' media='screen' />\n";
		// Feuilles de styles Paramètres de configuration du cahier de textes à partir de /lib/global.inc
		// echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/maj_global.css' media='screen' />\n";
      // Feuilles de styles imprimante
		echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/accessibilite_print.css' media='print' />\n";
   }
}


echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/portable.css' media='handheld' />\n";


// Feuilles de styles du bandeau
echo "\n<link title='bandeau' rel='stylesheet' type='text/css' href='$gepiPath/css/bandeau_r01.css' media='screen' />\n";
echo "<!--[if lte IE 7]>\n";
	echo "	<link title='bandeau' rel='stylesheet' type='text/css' href='$gepiPath/css/bandeau_r01_ie.css' media='screen' />\n";
echo "<![endif]-->\n";
echo "<!--[if lte IE 6]>\n";
	echo "	<link title='bandeau' rel='stylesheet' type='text/css' href='$gepiPath/css/bandeau_r01_ie6.css' media='screen' />\n";
echo "<![endif]-->\n";
echo "<!--[if IE 7]>\n";
	echo "	<link title='bandeau' rel='stylesheet' type='text/css' href='$gepiPath/css/bandeau_r01_ie7.css' media='screen' />\n";
echo "<![endif]-->\n";

// fin modification Régis

if(isset($style_screen_ajout)) {

	// Styles paramétrables depuis l'interface:
	if($style_screen_ajout=='y') {
		// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
		// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
		// Il faudra définir un nom de la forme style_screen_ajout_RNE.css pour le multisite
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			if (@file_exists($gepiPath.'/style_screen_ajout_'.getSettingValue("gepiSchoolRne").'.css')) {
					echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout_".getSettingValue("gepiSchoolRne").".css' />\n";
			}
		}
		else {
			if(isset($niveau_arbo)) {
				if((($niveau_arbo==0)&&(file_exists('./style_screen_ajout.css')))||
				(($niveau_arbo==1)&&(file_exists('../style_screen_ajout.css')))||
				(($niveau_arbo==2)&&(file_exists('../../style_screen_ajout.css')))) {
					echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
				}
			}
			elseif(file_exists('../style_screen_ajout.css')) {
				// La plupart du temps, on ne renseigne pas $niveau_arbo pour le niveau 1
				echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
			}
		}
	}
}

//echo "<link href='".$gepiPath."/grid.css' rel='stylesheet' type='text/css'/>\n";


echo '	<!-- Fin des styles -->'."\n\n";

include 'maj_coul_global.inc';

// ============================== FIN des feuilles de style =======================
// *********** sur le onload *************** //
if (isset($use_observeur) AND $use_observeur == 'ok') {
  $charger_observeur = ' observeur();';
}else{
  $charger_observeur = NULL;
}
?>
</head>
<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion();<?php echo $charger_observeur; ?>">

<?php
if (getSettingValue("impose_petit_entete_prof") == 'y' AND $_SESSION['statut'] == 'professeur') {
	$_SESSION['cacher_header']="y";
}
// Taille à récupérer dans la base pour initialiser $_SESSION['cacher_header']
	// petit bandeau toute valeur sauf "n" ;
	// grand bandeau "n";
if (isset($titre_page)) {
	if(!isset($_SESSION['cacher_header'])) {
		$_SESSION['cacher_header']="n";
	}



// ----- modifications Régis Bandeau -----
// début des modifications du bandeau

	echo "<!-- Header start -->\n";

/* ===== affichage du bandeau ===== */

	if(getSettingValue('gepi_stylesheet')=='style') {
		// Détermine le fond du bandeau
		if(getSettingValue('utiliser_degrade')=='y') {
			$degrade_entete="degrade1";
		}
		else{
			$degrade_entete="darkfade";
		}
	}
	else{
			$degrade_entete="no_style";
	}

// Initialisation du bandeau à grand bandeau darkfade pour ceux qui n'ont pas de javascript
	echo "<div id='bandeau' class='gd_bandeau darkfade gd_bandeau_darkfade'>\n";

// Initialisation du bandeau à la bonne couleur
	echo "<script type='text/javascript'>
		<!--
			modifier_couleur_bandeau('$degrade_entete');
		// -->
	</script>";
// Initialisation du bandeau à la bonne taille
	$cacher_header = isset($_SESSION['cacher_header']) ? $_SESSION['cacher_header'] : "n";

	//=====================
	// AJOUT boireaus 20080806
	if(isset($mode_header_reduit)) {
		$cacher_header="y";
	}
	//=====================

	if($cacher_header=="n") {
		$taille_bandeau_header="g";
	}
	else {
		$taille_bandeau_header="p";
	}
	// 	echo $_SESSION['login'];
	if($taille_bandeau_header=="p") {
		echo "<script type='text/javascript'>
			<!--
				modifier_taille_bandeau();
			// -->
		</script>";
	}

			echo "<!-- Page title, access rights -->\n";
			echo "<!-- User name, status, main matter, home, logout, account management -->\n";

			// Bandeau de gauche
			// echo "<div id='bandeau_gauche'>\n";

				//=====================
				// AJOUT boireaus 20080806
				if(!isset($mode_header_reduit)) {
				//=====================
					//echo "<div class='bt_change_taille'>";
						// Bouton réduire le bandeau
						echo "<a class='change_taille_gd' href=\"#\" onclick=\"modifier_taille_bandeau();change_mode_header('y', '".$gepiPath."');return false;\">";
							echo "<img src='$gepiPath/images/up.png' alt='Cacher le bandeau' title='Cacher le bandeau' />";
						echo "</a>";
						// Bouton agrandir le bandeau
						echo "<a class='change_taille_pt' href=\"#\" onclick=\"modifier_taille_bandeau();change_mode_header('n', '".$gepiPath."');return false;\">";
							echo "<img src='$gepiPath/images/down.png' alt='Afficher le bandeau' title='Afficher le bandeau' />";
						echo "</a>";
					//echo "</div>\n";
				//=====================
				// AJOUT boireaus 20080806
				}
				//=====================

				//=== Titre de la page ===
				echo "<h1>".$titre_page."</h1>\n";
				echo "<!-- Dernière connexion -->\n";
				
				//=== Nom Prénom utilisateur ===
				echo "<p id='bd_nom'>\n";
					// echo "<span id='bd_nom'>";
					if((!isset($_SESSION['prenom']))||(!isset($_SESSION['nom']))) {
						$sql="SELECT nom, prenom FROM utilisateurs WHERE login='".$_SESSION['login']."';";
						$res_np=mysql_query($sql);
						if(mysql_num_rows($res_np)>0) {
							$lig_np=mysql_fetch_object($res_np);
							$_SESSION['prenom']=$lig_np->prenom;
							$_SESSION['nom']=$lig_np->nom;
						}
					}
					if((isset($_SESSION['prenom']))||(isset($_SESSION['nom']))) {
						echo($_SESSION['prenom'] . " " . $_SESSION['nom'] . "\n");
					}
					else {
						echo "NOM Prenom";
					}
					// echo "</span>\n";
				echo "</p>\n";
				
				//=== Dernière connexion ===
				if (isset($affiche_connexion)) {
					echo "<p id='dern_connect'>".last_connection();
					echo "&nbsp;"; // Pour éviter un <p></p> lors de la première connexion
					echo "</p>\n";
				}
				
				//=== statut utilisateur ===
				echo "<p id='bd_parent'>\n";
					if ($_SESSION['statut'] == "administrateur") {
						echo "<span class='red'>Administrateur</span>\n";
					} 
					elseif ($_SESSION['statut'] == "professeur") {
						$nom_complet_matiere = sql_query1("select nom_complet from matieres
						where matiere = '".$_SESSION['matiere']."'");
						if ($nom_complet_matiere != '-1') {
							//echo("Professeur de : " . $nom_complet_matiere);
							echo("Professeur de : " . htmlentities($nom_complet_matiere));
						} else {
							echo "Invité";
						}
					} 
					elseif ($_SESSION['statut'] == "scolarite") {
						echo "Scolarité";
					} 
					elseif ($_SESSION['statut'] == "cpe") {
						echo "CPE";
					} 
					elseif ($_SESSION['statut'] == "eleve") {
						$tab_tmp_info_classes=get_noms_classes_from_ele_login($_SESSION['login']);
						echo "Élève de ".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1];
					} 
					elseif ($_SESSION['statut'] == "responsable") {
						$tab_tmp_ele=get_enfants_from_resp_login($_SESSION['login']);
						$chaine_enfants="";
						if(count($tab_tmp_ele)>0) {
							$chaine_enfants=$tab_tmp_ele[1];
							$tab_tmp_info_classes=get_noms_classes_from_ele_login($tab_tmp_ele[0]);
							if(count($tab_tmp_info_classes)>0) {
								$chaine_enfants.=" (<em>".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1]."</em>)";
							}
							for($i=3;$i<count($tab_tmp_ele);$i+=2) {
								$chaine_enfants.=", ".$tab_tmp_ele[$i];
								unset($tab_tmp_info_classes);
								$tab_tmp_info_classes=get_noms_classes_from_ele_login($tab_tmp_ele[$i-1]);
								if(count($tab_tmp_info_classes)>0) {
									$chaine_enfants.=" (<em>".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1]."</em>)";
								}
							}
						}
						echo "Responsable de $chaine_enfants";
					}
					elseif($_SESSION["statut"] == "autre") {
						echo $_SESSION["statut_special"];
					}
				echo "</p>\n";
					
				//On vérifie si le module de mise à jour est activé
				if (getSettingValue("active_module_msj")==='y' and $_SESSION['statut'] == 'administrateur') {
					echo "<a href='javascript:ouvre_popup()'>";
						echo "<img style='border: 0px; width: 15px; height: 15px;' src='$gepiPath/images/info.png' alt='info' title='info' align='top' />";
					echo "</a>\n<br />\n";
				}
				
				//christian
				// menus de droite
				// menu accueil
				echo "<ol id='premier_menu'>\n";
					if ($session_gepi->current_auth_mode == "sso" && $gepiSettings['sso_display_portail'] == 'yes') {
					echo "<li class='ligne_premier_menu'>\n";
						echo " <a href='".$gepiSettings['sso_url_portail']."'>";
							echo "<img src='$gepiPath/images/icons/home.png' alt='Portail' title='Retour portail' />";
							echo "<span class='menu_bandeau'>";
								echo "&nbsp;Retour portail";
							echo "</span>";
						echo "</a>\n";
					echo "</li>\n";
					}
					if ($session_gepi->current_auth_mode != "sso" || $gepiSettings['sso_hide_logout'] != 'yes') {
					echo "<li class='ligne_premier_menu'>\n";
						echo " <a href='$gepiPath/logout.php?auto=0'".insert_confirm_abandon().">";
							echo "<img src='$gepiPath/images/icons/quit_16.png' alt='Se déconnecter' title='Se déconnecter' />";
							echo "<span class='menu_bandeau'>";
								echo "&nbsp;Déconnexion";
							echo "</span>";
						echo "</a>\n";
					echo "</li>\n";
					}
					echo "<li class='ligne_premier_menu'>\n";
						echo " <a href='$gepiPath/utilisateurs/mon_compte.php'".insert_confirm_abandon().">";
							echo "<img src='$gepiPath/images/icons/buddy.png' alt='Mon compte' title='Mon compte' />";
							echo "<span class='menu_bandeau'>";
								echo "&nbsp;Gérer mon compte";
							echo "</span>";
						echo "</a>\n";
					echo "</li>\n";
					echo "<li class='ligne_premier_menu'>\n";
						echo "<a href='$gepiPath/accueil.php'".insert_confirm_abandon().">";
							echo "<img src='$gepiPath/images/icons/home.png' alt='Accueil' title='Accueil' />";
							echo "<span class='menu_bandeau'>";
								echo "&nbsp;Accueil";
							echo "</span>";
						echo "</a>\n";
					echo "</li>\n";
				echo "</ol>\n";
				echo "<div class='spacer'> </div>\n"; // sépare les 2 menus
				// menu contact
				echo "<ol id='deux_menu'>";
						echo "<!-- Contact -->\n";
					$prefix = '';
					if (!isset($niveau_arbo)) {
						$prefix = "../";
					}elseif($niveau_arbo==1) {
						$prefix = "../";
					}elseif ($niveau_arbo==2) {
						$prefix = "../../";
					}
					echo "<li class='ligne_deux_menu'>\n";
						if ($_SESSION['statut'] == 'administrateur') {
							echo "<a onclick=\"window.open(this.href, '_blank'); return false;\" href=\"http://gepi.mutualibre.org/\">Visiter le site de GEPI</a>";
						} else {
							if (getSettingValue("contact_admin_mailto")=='y') {
								$gepiAdminAdress=getSettingValue("gepiAdminAdress");
								$tmp_date=getdate();
								echo "<a href='mailto:$gepiAdminAdress?Subject=Gepi&amp;body=";
								if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
								echo ",%0d%0aCordialement.'>Contacter l'administrateur</a>";
							}
							else {
								//echo("<a href=\"javascript:centrerpopup('$gepiPath/gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Contacter l'administrateur</a>");
								echo("<a href=\"$gepiPath/gestion/contacter_admin.php\" onclick=\"centrerpopup('$gepiPath/gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes');return false;\" target='_blank'>Contacter l'administrateur</a>");
							}
						}
					echo "</li>\n";
					echo "<li class='ligne_deux_menu'>\n";
						echo "<a href=\"javascript:centrerpopup('$gepiPath/gestion/info_gepi.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Informations générales</a>";
					echo "</li>\n";
					echo "<li class='ligne_deux_menu'>\n";
						echo "<a href='javascript:centrerpopup(\"$gepiPath/gestion/info_vie_privee.php\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")'>Vie privée</a>";
					echo "</li>\n";
				echo "</ol>\n";
				
				// echo "<div class='spacer_bas'> </div>\n"; // bloque les menus dans le bandeau
	
		 //=== Affichage de la version de Gepi ===
				if ($_SESSION['statut'] == "administrateur") {
					  $version_gepi = '';
					  if ($gepiVersion == 'trunk') {
							// On a une version trunk
							$version_gepi .= "gepi-trunk";
					  } else {
							// On a une version numérotée
							$version_gepi .= "v".$gepiVersion;
							if ($gepiRcVersion != '') $version_gepi .= "-RC".$gepiRcVersion;
							if ($gepiBetaVersion != '') $version_gepi .= "-beta".$gepiBetaVersion;
							if ($gepiVersionMaintenance != '') $version_gepi .= ".".$gepiVersionMaintenance;
					  }
					  if ($gepiSvnRev != '') $version_gepi .= " r".$gepiSvnRev;

					//echo "<div id='version_gepi'"
					echo "<p id='num_version'>".$version_gepi."</p>";
				}
				else{
					echo "<p id='num_version'>&nbsp;</p>"; // nécessaire pour recaler le bandeau dans tous les navigateurs
				}

	// Fin du conteneur de Header

// ----- Régis : fin des modifications du bandeau -----

	echo "</div>\n";


// ==========> On ajoute la barre de menu <========================= //
if (!isset($nobar)) { $nobar = "non"; }
if (getSettingValue("utiliserMenuBarre") == "yes" AND $_SESSION["statut"] == "professeur" AND $nobar != 'oui') {
	// On vérifie que l'utilisateur ne l'a pas enlevée
	if (getPref($_SESSION["login"], "utiliserMenuBarre", "yes") == "yes") {
		// ne pourrait-on pas utiliser $gepiPath plutôt que construire $prefix un peu plus haut ?
		if (file_exists($prefix."edt_organisation/fonctions_calendrier.php")) {
			require_once($prefix."edt_organisation/fonctions_calendrier.php");
		}elseif(file_exists("fonctions_calendrier.php")) {
			require_once("./fonctions_calendrier.php");
		}
		include("header_barre_menu.php");
	}
}

echo "<div id='container'>\n";
if ((isset($_GET['msg'])) or (isset($_POST['msg'])) or (isset($msg))) {
	$msg = isset($_POST['msg']) ? unslashes($_POST['msg']) : (isset($_GET['msg']) ? unslashes($_GET['msg']) : $msg);
	if ($msg != '') {
		echo "<div class=\"headerMessage\">";
		echo($msg);
		echo "</div>";
	}
}
}
// Décommenter la ligne ci -dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
//debug_var();

//if (isset($titre_page)) echo "</div>";
?>

