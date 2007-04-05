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
<link rel="stylesheet" type="text/css" href="<?php echo($gepiPath); ?>/style.css" />
<!-- ======================================== -->
<!-- AJOUT pour les trombinoscopes et absences-->
<?php
    // vérifie si on est dans le module trombinoscope si oui on ajoute ce css
    if(basename($_SERVER['PHP_SELF'],".php")==="trombinoscopes"){
        ?><link rel="stylesheet" type="text/css" href="<?php echo $gepiPath; ?>/mod_trombinoscopes/styles/styles.css" /><?php
    }

   // vérifie si on nest dans le modules absences
   $files = array("gestion_absences", "select", "ajout_abs", "ajout_ret", "ajout_dip", "ajout_inf", "tableau", "impression_absences", "prof_ajout_abs", "statistiques", "alert_suivi");
    if(in_array(basename($_SERVER['PHP_SELF'],".php"), $files)) {
        ?><link rel="stylesheet" type="text/css" href="<?php echo $gepiPath; ?>/mod_absences/styles/mod_absences.css" /><?php
    }
    if(basename($_SERVER['PHP_SELF'],".php")==="prof_ajout_abs"){
        ?><link rel="stylesheet" type="text/css" href="<?php echo $gepiPath; ?>/mod_absences/styles/saisie_absences.css" /><?php
    }
?>
<!-- ============================= -->

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
@media print  {
  visibility: hidden;
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
?>
</head>
<body onLoad="show_message_deconnexion()">

<?php if (isset($titre_page)) {
  if ($_SESSION['statut'] == "administrateur") {
  	$rc = null;
  	$beta = null;
    if ($gepiRcVersion != '') $rc = "-RC".$gepiRcVersion;
    if ($gepiBetaVersion != '') $beta = "-beta".$gepiBetaVersion;
	echo "<div id='version_gepi'>v".$gepiVersion.$rc.$beta."</div>";	    
  }
?>
<div id="header">
<!-- Header start -->
   <!-- Page title, access rights -->
   <div id="headerLeft">
      <?php
      echo("<h3>" . $titre_page . "</h3>\n");
      ?>
   </div>
   <!-- User name, status, main matter, home, logout, account management -->
   <div id="headerRight">
      <?php
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
        ?><a href="javascript:ouvre_popup()"><img style="border: 0px; width: 15px; height: 15px;" src="<?php echo($gepiPath); ?>/images/info.png" alt="info" title="info" align="top" /></a> <?php
    }
    //christian


      echo "<br />\n";
      echo "<span class='grey'>";
      ?>
      <a href="<?php echo($gepiPath); ?>/accueil.php"><img src='<?php echo($gepiPath); ?>/images/icons/home.png' alt='Accueil' style='bottom: -1px;'/>  Accueil</a> - <a href="<?php echo($gepiPath); ?>/utilisateurs/mon_compte.php"><img src='<?php echo($gepiPath); ?>/images/icons/buddy.png' alt='Mon compte' /> Gérer mon compte</a> - <a href="<?php echo($gepiPath); ?>/logout.php?auto=0"><img src='<?php echo($gepiPath); ?>/images/icons/quit_16.png' alt='Se déconnecter' /> Déconnexion</a>
      </span>
   </div>
   <div id="headerBottomLeft">
      <?php if (isset($affiche_connexion)) echo last_connection()."\n"; ?>
   </div>

   <!-- Contact -->
   <div id="headerBottomRight">
      <?php
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
      ?>

   </div>
</div>
   <?php
}
echo "<div id='container'>";
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