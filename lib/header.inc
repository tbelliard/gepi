<?php
/*
 * $Id$
 *
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
              $r = "<font color = 'red'><b>Tentative de connexion le ".$jour_b."/".$mois_b."/".$annee_b." à ".$heures_b." h ".$minutes_b. " avec un mot de passe erroné</b></font> (<a href='utilisateurs/mon_compte.php#connexion'>journal des connexions</a>)";
              // On compte le nombre de tentatives infructueuses successives
              $nb_tentative = 0;
              $flag = 0;
              for ($i = 0; (($row_b = sql_row($res, $i)) and ($flag < 1)); $i++)
              {
              if (($row_b[1]  == "2") and ($row_b[2]  == $row[2]))
                  $nb_tentative++;
              else
                  $flag = 1;
              }
              if ($nb_tentative > 1) $r .= "<br><b>Nombre de tentatives de connexion successives : ".$nb_tentative.".</b></font>";
          } else
              $r = "  Dernière session ouverte le ".$jour_b."/".$mois_b."/".$annee_b." à ".$heures_b." h ".$minutes_b. " (<a href='utilisateurs/mon_compte.php#connexion'>journal des connexions</a>)";
      }
    }
    return $r;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
<meta HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
<META HTTP-EQUIV="Expires" CONTENT="0" />
<meta HTTP-EQUIV="refresh" content="<?php echo getSettingValue("sessionMaxLength")*60; ?>; URL=<?php echo($gepiPath); ?>/logout.php?auto=3&amp;debut_session=<?php echo urlencode($_SESSION['start']);?>&amp;sessionid=<?php echo session_id();?>" />
<title><?php echo getSettingValue("gepiSchoolName"); ?> : base de données élèves</title>
<?php $style = getSettingValue("gepi_stylesheet");
if (empty($style)) $style = "style";
?>
<link rel="stylesheet" type="text/css" href="<?php echo($gepiPath); ?>/<?php echo $style;?>.css" />
<!-- ======================================== -->
<!-- AJOUT pour les trombinoscopes et absences-->
<?php
    // vérifie si on est dans le module trombinoscope si oui on ajoute ce css
    if(basename($_SERVER['PHP_SELF'],".php")==="trombinoscopes"){
        ?><link rel="stylesheet" type="text/css" href="<?php echo $gepiPath; ?>/mod_trombinoscopes/styles/styles.css" /><?php
    }

   // vérifie si on est dans le modules absences
   $files = array("gestion_absences", "select", "ajout_abs", "ajout_ret", "ajout_dip", "ajout_inf", "tableau", "impression_absences", "prof_ajout_abs", "statistiques", "alert_suivi", "admin_config_semaines", "admin_motifs_absences", "admin_horaire_ouverture", "admin_actions_absences", "admin_periodes_absences");
    if(in_array(basename($_SERVER['PHP_SELF'],".php"), $files)) {
        ?><link rel="stylesheet" type="text/css" href="<?php echo $gepiPath; ?>/mod_absences/styles/mod_absences.css" /><?php
    }
    if(basename($_SERVER['PHP_SELF'],".php")==="prof_ajout_abs"){
        ?><link rel="stylesheet" type="text/css" href="<?php echo $gepiPath; ?>/mod_absences/styles/saisie_absences.css" /><?php
    }
?>
<!-- ======================================== -->

<script type="text/javascript" language="javascript">
<?php if (isset($affiche_message) and ($affiche_message == 'yes')) { ?>
<!--
alert("<?php echo $message_enregistrement; ?>");
//-->
<?php } ?>
function changement() {
change = 'yes';
}
</script>

<!--
Pour affichage d'un fixe en bas à droite (ne marche pas avec IE 6)
-->
<style type="text/css">
@media screen  {
  div#fixe   {
    position: fixed;
    bottom: 5%;
    right: 5%;
  }
}
</style>

<!-- Gestion de l'expiration des session - Patrick Duthilleul -->
<script type="text/javascript" language="JavaScript">
<!--
var debut=new Date()
function show_message_deconnexion(){
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
<script type='text/javascript' language='JavaScript'>
    function ouvre_popup(url){
        eval("window.open('<?php echo($gepiPath); ?>/mod_miseajour/utilisateur/fenetre.php','fen','width=600,height=500,menubar=no,scrollbars=yes')");
        fen.focus();
    }
</script>

<!-- christian référencement de GEPI -->
<script type='text/javascript' language='JavaScript'>
    function ouvre_popup_reference(url){
        eval("window.open(url,'fen','width=500,height=600,menubar=no,scrollbars=yes')");
        fen.focus();
    }
</script>

<?php if (isset($niveau_arbo) and ($niveau_arbo == 0)) {
   echo "<script src=\"lib/functions.js\" type=\"text/javascript\" language=\"javascript\"></script>\n";
   echo "<LINK REL=\"SHORTCUT ICON\" href=\"./favicon.ico\" />\n";
} else if (isset($niveau_arbo) and ($niveau_arbo == 2)) {
   echo "<script src=\"../../lib/functions.js\" type=\"text/javascript\" language=\"javascript\"></script>\n";
   echo "<LINK REL=\"SHORTCUT ICON\" href=\"../../favicon.ico\" />\n";
} else {
   echo "<script src=\"../lib/functions.js\" type=\"text/javascript\" language=\"javascript\"></script>\n";
   echo "<LINK REL=\"SHORTCUT ICON\" href=\"../favicon.ico\" />\n";
}
// Couleur de fond des pages
if (!isset($titre_page)) $bgcouleur = "bgcolor= \"#FFFFFF\""; else $bgcouleur = "";


if(isset($style_specifique)){
	// Il faudrait filtrer le contenu de la variable...
	// ne doit contenir que certains types de caractères et se terminer par .css
	// Non... on ajoute le ".css" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence
	if(strlen(ereg_replace("[A-Za-z0-9_/]","",$style_specifique))==0){
		// Styles spécifiques à une page:
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/$style_specifique.css' />\n";
	}
}

// Ajout du framework prototype 1.5.1.1 conditionné à la variable $utilisation_prototype="ok"
$prototype = isset($utilisation_prototype) ? $utilisation_prototype : NULL;
	if ($prototype == "ok") {
		// On affiche alors le lien qui ouvre prototype à la page en question
    	echo '<script type="text/javascript" src="'.$gepiPath.'/lib/prototype.js"></script>'."\n";
} else {
    echo '';
}

if(isset($javascript_specifique)){
	// Il faudrait filtrer le contenu de la variable...
	// On ajoute le ".js" automatiquement et on exclus les "." qui pourrait permettre des ".." pour remonter dans l'arborescence
	if(strlen(ereg_replace("[A-Za-z0-9_/]","",$javascript_specifique))==0){
		// Javascript spécifique à une page:
		echo "<script type='text/javascript' src='$gepiPath/$javascript_specifique.js'></script>\n";
	}
}



if(isset($style_screen_ajout)){
	// Styles paramétrables depuis l'interface:
	if($style_screen_ajout=='y'){
		// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
		// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
	}
}


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

echo "<script type='text/javascript' src='$gepiPath/lib/brainjar_drag.js'></script>\n";
echo "<script type='text/javascript' src='$gepiPath/lib/position.js'></script>\n";

// Variable passée à 'ok' en fin de page via le /lib/footer.inc.php
echo "<script type='text/javascript'>
	temporisation_chargement='n';
</script>\n";

//===================================


?>
</head>
<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->
<body onLoad="show_message_deconnexion()">

<?php
if (isset($titre_page)) {
	if ($_SESSION['statut'] == "administrateur") {
		$rc = null;
		$beta = null;
		if ($gepiRcVersion != '') $rc = "-RC".$gepiRcVersion;
		if ($gepiBetaVersion != '') $beta = "-beta".$gepiBetaVersion;
		echo "<div id='version_gepi'>v".$gepiVersion.$rc.$beta."</div>";
	}


	echo "<!-- Header start -->\n";
	echo "<div id='header'>\n";
	//echo "<table id='table_header' border='0' width='100%' height='100px;' style='color: white; background-image: url(\"$gepiPath/images/background/darkfade.png\"); margin: 0; padding: 0;'>\n";
	if(getSettingValue('gepi_stylesheet')=='style'){
		//echo "<table id='table_header' border='0' width='100%' style='color: white; background-image: url(\"$gepiPath/images/background/darkfade.png\"); background-repeat: repeat-x; margin: 0; padding: 0;'>\n";

		if(getSettingValue('utiliser_degrade')=='y'){
			$degrade_entete="degrade1";
		}
		else{
			$degrade_entete="darkfade";
		}

		echo "<table id='table_header' border='0' width='100%' style='color: white; background-image: url(\"$gepiPath/images/background/$degrade_entete.png\"); background-repeat: repeat-x; margin: 0; padding: 0;'>\n";
	}
	else{
		echo "<table id='table_header' border='0' width='100%'>\n";
	}

	//echo "<tr height='20px'>\n";
	//echo "<tr height='20%'>\n";
	echo "<tr>\n";
		//echo "<td rowspan='3' style='margin:0;padding:0;'><img src='$gepiPath/images/background/darkfade.png' alt='Bricolage pas chouette pour fixer la hauteur du tableau'  style='margin:0;padding:0;' /></td>\n";
		//echo "<td rowspan='2' style='margin:0;padding:0;'><div style='width:0px; height:100px; margin:0;padding:0;'></div></td>\n";
		echo "<td rowspan='2' style='margin:0;padding:0;'><div style='width:0px; height:96px; margin:0;padding:0;'></div></td>\n";

		echo "<!-- Page title, access rights -->\n";
		echo "<td id='td_headerLeft' rowspan='2'>\n";

		// Pour empêcher de trop réduire la taille de la cellule de gauche
		echo "<div style='width:380px;'></div>\n";

		//echo "<div style='border: 1px solid lime; margin-top:20px; margin-bottom:20px;'><h3>".$titre_page."</h3></div>\n";
		//echo "<div style='margin-top:20px; margin-bottom:15px;'><h3>".$titre_page."</h3></div>\n";
		echo "<div style='margin-top:20px; margin-bottom:15px;'><h3>".$titre_page."</h3></div>\n";
		//echo "<h3>".$titre_page."</h3>\n";


		echo "<!-- Dernière connexion -->\n";
		if (isset($affiche_connexion)){echo last_connection()."\n";}

		echo "</td>\n";



		echo "<!-- User name, status, main matter, home, logout, account management -->\n";
		echo "<td id='td_headerTopRight'>\n";

		// Pour empêcher de trop réduire la taille de la cellule de droite
		echo "<div style='width:400px;'></div>\n";


		echo($_SESSION['prenom'] . " " . $_SESSION['nom'] . "<br />\n");
		if ($_SESSION['statut'] == "administrateur") {
			echo "<span class='red'>Administrateur</span>\n";
		} elseif ($_SESSION['statut'] == "professeur") {
			$nom_complet_matiere = sql_query1("select nom_complet from matieres
			where matiere = '".$_SESSION['matiere']."'");
			if ($nom_complet_matiere != '-1') {
				//echo("Professeur de : " . $nom_complet_matiere);
				echo("Professeur de : " . htmlentities($nom_complet_matiere));
			} else {
				echo "Invité";
			}
		} elseif ($_SESSION['statut'] == "scolarite") {
			echo "Scolarité";
		} elseif ($_SESSION['statut'] == "cpe") {
			echo "CPE";
		} elseif ($_SESSION['statut'] == "eleve") {
			$tab_tmp_info_classes=get_noms_classes_from_ele_login($_SESSION['login']);
			echo "Élève de ".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1];
		} elseif ($_SESSION['statut'] == "responsable") {
			$tab_tmp_ele=get_enfants_from_resp_login($_SESSION['login']);
			$chaine_enfants="";
			if(count($tab_tmp_ele)>0){
				$chaine_enfants=$tab_tmp_ele[1];
				$tab_tmp_info_classes=get_noms_classes_from_ele_login($tab_tmp_ele[0]);
				if(count($tab_tmp_info_classes)>0){
					$chaine_enfants.=" (<i>".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1]."</i>)";
				}
				for($i=3;$i<count($tab_tmp_ele);$i+=2){
					$chaine_enfants.=", ".$tab_tmp_ele[$i];
					unset($tab_tmp_info_classes);
					$tab_tmp_info_classes=get_noms_classes_from_ele_login($tab_tmp_ele[$i-1]);
					if(count($tab_tmp_info_classes)>0){
						$chaine_enfants.=" (<i>".$tab_tmp_info_classes[count($tab_tmp_info_classes)-1]."</i>)";
					}
				}
			}
			echo "Responsable de $chaine_enfants";
		}

		// A QUOI SERT CE $temp ?
		$temp = '';
		// christian
		echo "<br />\n";
		//On vérifie si le module de mise à jour est activé
		if (getSettingValue("active_module_msj")==='y' and $_SESSION['statut'] == 'administrateur') {
			echo "<a href='javascript:ouvre_popup()'><img style='border: 0px; width: 15px; height: 15px;' src='$gepiPath/images/info.png' alt='info' title='info' align='top' /></a>\n";
		}
		//christian

		echo "</td>\n";
		echo "</tr>\n";



		echo "<tr>\n";
		// Les TD précédents sont dans des rowspan

		echo "<td  id='td_headerBottomRight'>\n";
		echo "<span class='grey'>";
		echo "<a href='$gepiPath/accueil.php'><img src='$gepiPath/images/icons/home.png' alt='Accueil' style='bottom: -1px;' />&nbsp;Accueil</a>\n";
		echo " - <a href='$gepiPath/utilisateurs/mon_compte.php'><img src='$gepiPath/images/icons/buddy.png' alt='Mon compte' />&nbsp;Gérer mon compte</a>\n";
		echo " - <a href='$gepiPath/logout.php?auto=0'><img src='$gepiPath/images/icons/quit_16.png' alt='Se déconnecter' />&nbsp;Déconnexion</a>\n";
		echo "</span>\n";

		echo "<br />\n";

		echo "<!-- Contact -->\n";
		$prefix = '';
		if (!isset($niveau_arbo)) $prefix = "../" ; else if ($niveau_arbo==2) $prefix = "../../";

		echo "<span style='border-top: 1px dotted orange;'>\n";
		if ($_SESSION['statut'] == 'administrateur') {
			echo "<a href=\"http://gepi.mutualibre.org/\" target='_blank'>Visiter le site de GEPI</a>";
		} else {
			echo("<a href=\"javascript:centrerpopup('".$prefix."gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Contacter l'administrateur</a>");
		//         echo("<a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">Contacter l'administrateur</a>");
		}
		echo " - <a href=\"javascript:centrerpopup('".$prefix."gestion/info_gepi.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Informations générales</a>
		- <a href='javascript:centrerpopup(\"".$prefix."gestion/info_vie_privee.php\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")'>Vie privée</a>";
		//- <a href='javascript:centrerpopup(\"".$prefix."gestion/info_vie_privee.php\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")'>Vie privée</a></b>";
		echo "</span>\n";

		echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";
	echo "\n";
	echo "</div>\n";
	echo "<!-- Header end -->\n";
	echo "\n";
}

/*
	echo "<div id='header'>\n";
	echo "<!-- Header start -->\n";

		echo "\n";
		echo "<!-- Page title, access rights -->\n";
		echo "<div id='headerLeft'>\n";
			echo("<h3>" . $titre_page . "</h3>\n");
		echo "</div>\n";

		echo "\n";
		echo "<!-- User name, status, main matter, home, logout, account management -->\n";
		echo "<div id='headerRight'>\n";
			echo($_SESSION['prenom'] . " " . $_SESSION['nom'] . "<br />\n");
			if ($_SESSION['statut'] == "administrateur") {
				echo "<span class='red'>Administrateur</span>";
			} elseif ($_SESSION['statut'] == "professeur") {
				$nom_complet_matiere = sql_query1("select nom_complet from matieres
				where matiere = '".$_SESSION['matiere']."'");
				if ($nom_complet_matiere != '-1') {
					//echo("Professeur de : " . $nom_complet_matiere);
					echo("Professeur de : " . htmlentities($nom_complet_matiere));
				} else {
					echo "Invité";
				}
			} elseif ($_SESSION['statut'] == "scolarite") {
				echo "Scolarité";
			} elseif ($_SESSION['statut'] == "cpe") {
				echo "CPE";
			}
			$temp = '';
			// christian
			echo "<br />\n";
			//On vérifie si le module de mise à jour est activé
			if (getSettingValue("active_module_msj")==='y' and $_SESSION['statut'] == 'administrateur') {
				echo "<a href='javascript:ouvre_popup()'><img style='border: 0px; width: 15px; height: 15px;' src='$gepiPath/images/info.png' alt='info' title='info' align='top' /></a>\n";
			}
			//christian


			echo "<br />\n";
			echo "<span class='grey'>";
			echo "<a href='$gepiPath/accueil.php'><img src='$gepiPath/images/icons/home.png' alt='Accueil' style='bottom: -1px;' />  Accueil</a> - <a href='$gepiPath/utilisateurs/mon_compte.php'><img src='$gepiPath/images/icons/buddy.png' alt='Mon compte' /> Gérer mon compte</a> - <a href='$gepiPath/logout.php?auto=0'><img src='$gepiPath/images/icons/quit_16.png' alt='Se déconnecter' /> Déconnexion</a>";
			echo "</span>\n";
		echo "</div>\n";

		echo "\n";
		echo "<div id='headerBottomLeft'>\n";
			if (isset($affiche_connexion)){echo last_connection()."\n";}
		echo "</div>\n";

		echo "\n";
		echo "<!-- Contact -->\n";
		echo "<div id='headerBottomRight'>\n";
			$prefix = '';
			if (!isset($niveau_arbo)) $prefix = "../" ; else if ($niveau_arbo==2) $prefix = "../../";

			if ($_SESSION['statut'] == 'administrateur') {
				echo "<a href=\"http://gepi.mutualibre.org/\">Visiter le site de GEPI</a>";
			} else {
				echo("<a href=\"javascript:centrerpopup('".$prefix."gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Contacter l'administrateur</a>");
			//         echo("<a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">Contacter l'administrateur</a>");
			}
			echo " - <a href=\"javascript:centrerpopup('".$prefix."gestion/info_gepi.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Informations générales</a>
			- <a href='javascript:centrerpopup(\"".$prefix."gestion/info_vie_privee.php\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")'>Vie privée</a>";
			//- <a href='javascript:centrerpopup(\"".$prefix."gestion/info_vie_privee.php\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")'>Vie privée</a></b>";
		echo "</div>\n";
	echo "\n";
	echo "<!-- Header end -->\n";
	echo "</div>\n";
	echo "\n";
}
*/

echo "<div id='container'>\n";
if ((isset($_GET['msg'])) or (isset($_POST['msg'])) or (isset($msg))){
	$msg = isset($_POST['msg']) ? unslashes($_POST['msg']) : (isset($_GET['msg']) ? unslashes($_GET['msg']) : $msg);
	if ($msg != '') {
		echo "<div class=\"headerMessage\">";
		echo($msg);
		echo "</div>";
	}
}

//if (isset($titre_page)) echo "</div>";
?>