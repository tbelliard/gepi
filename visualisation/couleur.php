<?php
/*
* $Id$
*
* Modification... Stephane Boireau
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


// Ajouter une gestion des droits par la suite
// dans la table MySQL appropriée et décommenter ce passage.
// INSERT INTO droits VALUES ('/visualisation/couleur.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Choix d une couleur pour le graphique des résultats scolaires', '1');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//RECUPERATION DES VARIABLES
$objet=isset($_POST['objet']) ? $_POST['objet'] : (isset($_GET['objet']) ? $_GET['objet'] : NULL);

//ACQUISITION DES DIMENSIONS DE L'IMAGE POUR CONTROLER LES LIMITES DU CLIC
//$image="img/couleurs_gimp2.png";
$image="../images/couleurs_gimp2.png";
$dimensions=getimagesize($image);
$largeur=$dimensions[0];
$hauteur=$dimensions[1];

if(isset($_POST['validation'])) {
	check_token();

	$msg="";

	// On insère la saisie...
	$x=isset($_POST['x']) ? $_POST['x'] : 1;
	$y=isset($_POST['y']) ? $_POST['y'] : 1;

	$img=imagecreatefrompng("$image");

	if (!$img) {
		//echo "<p>L'image source n'a pas pu être rouverte...</p>";
		$msg.="L'image source n'a pas pu être rouverte...<br />";
	}
	else {
		$couleur=imagecolorat($img, $x, $y);
		$R = ($couleur >> 16) & 0xFF;
		$V = ($couleur >> 8) & 0xFF;
		$B = $couleur & 0xFF;

		if((!saveSetting("couleur_".$objet."_R",$R))||(!saveSetting("couleur_".$objet."_V",$V))||(!saveSetting("couleur_".$objet."_B",$B))){
			$msg.="Erreur lors de l'enregistrement de ".$objet." dans la table setting !";
			//echo "$msg";
		}
		else{
			$msg.="Enregistrement de la couleur pour ".$objet." effectué.";
		}
	}

	header("Location: choix_couleurs.php?msg=$msg");
	die();
}

//**************** EN-TETE **************************************
$titre_page = "Choix d'une couleur";
//**************** FIN EN-TETE **********************************

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
<meta HTTP-EQUIV="Content-Type" content="text/html; charset=utf-8" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
<META HTTP-EQUIV="Expires" CONTENT="0" />
<meta HTTP-EQUIV="refresh" content="<?php echo getSettingValue("sessionMaxLength")*60; ?>; URL=<?php echo($gepiPath); ?>/logout.php?auto=3&amp;debut_session=<?php echo urlencode($_SESSION['start']);?>&amp;sessionid=<?php echo session_id();?>" />
<title><?php echo getSettingValue("gepiSchoolName"); ?> : base de données élèves</title>
<link rel="stylesheet" type="text/css" href="<?php echo($gepiPath); ?>/style.css" />

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


if(isset($style_screen_ajout)){
	// Styles paramétrables depuis l'interface:
	if($style_screen_ajout=='y'){
		// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
		// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
	}
}

echo "</head>\n";
?>

<body <?php echo $bgcouleur; ?> onLoad="show_message_deconnexion()">
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

<script type='text/javascript' language="JavaScript">

	IE4 = (document.all) ? 1 : 0;
	NS4 = (document.layers) ? 1 : 0;
	moz = (document.getElementById) ? 1 : 0;
	VERSION4 = (IE4 | NS4 | moz) ? 1 : 0;
	if (!VERSION4) event = null;

	function init() {
		//alert("Initialisation")
		document.onmousedown = mouseDown
		document.onmousemove = mouseMove
	//        document.onmouseup = mouseUp
		if (NS4) document.captureEvents(Event.MOUSEDOWN | Event.MOUSEMOVE | Event.MOUSEUP)
	}

	function mouseMove(e) {
	//Pour les "events", Mozilla se comporte comme Netscape.
		if(NS4 | moz){
			//Xpos=e.pageX;
			//Ypos=e.pageY;
			Xpos=e.clientX;
			Ypos=e.clientY;
		}
		if(IE4){
			Xpos=event.x;
			Ypos=event.y;
		}
		window.status = "x:"+Xpos+" y:"+Ypos;
		return true;
	}

	function mouseDown(e) {
		if(NS4){
			Xpos=e.pageX;
			Ypos=e.pageY;
		}
		if(moz){
			Xpos=e.clientX;
			Ypos=e.clientY;
		}
		if(IE4){
			Xpos=event.x;
			Ypos=event.y;
		}
		document.forms['form1'].x.value=Xpos;
		document.forms['form1'].y.value=Ypos;

		<?php
		echo "if((Xpos>=0)&&(Xpos<=$largeur)&&(Ypos>=0)&&(Ypos<=$hauteur)){";
		?>
			document.forms['form1'].submit();
			//setTimeout("alert('Clic')",1000);
		}
		return true;
	}

	//init();
	setTimeout("init()",1000);
</script>

<div style="position:absolute;top:0;left:0"><img src="<?=$image;?>" width="<?=$dimensions[0];?>" height="<?=$dimensions[1];?>" alt="Couleurs" /></div>

<div style="position:absolute;top:0px;left:<?php $pos=$dimensions[0]+10;echo "$pos";?>px;">
   <div class="headerRight">
      <?php
      echo($_SESSION['prenom'] . " " . $_SESSION['nom'] . "<br />\n");
      if ($_SESSION['statut'] != "professeur") {
         echo($_SESSION['statut']);
      } else {
         $nom_complet_matiere = sql_query1("select nom_complet from matieres
         where matiere = '".$_SESSION['matiere']."'");
         if ($nom_complet_matiere != '-1') {
            //echo("Professeur de : " . $nom_complet_matiere);
            echo("Professeur de : " . htmlspecialchars($nom_complet_matiere));
         } else {
            echo "Invité";
         }
      }
      $temp = '';
  $rc = null;
  $beta = null;
    if ($gepiRcVersion != '') $rc = "-RC".$gepiRcVersion;
    if ($gepiBetaVersion != '') $beta = "-beta".$gepiBetaVersion;
      echo("<br />\nGEPI ".$gepiVersion.$rc.$beta." - ");
      ?>
      <a href="<?php echo($gepiPath); ?>/accueil.php">Accueil</a> - <a href="<?php echo($gepiPath); ?>/utilisateurs/mon_compte.php">Gérer mon compte</a> - <a href="<?php echo($gepiPath); ?>/logout.php?auto=0">Déconnexion</a>
   </div>
   <div class="separation">
   </div>


<h3>Choix d'une couleur</h3>
<p>Cliquez dans l'image sur la couleur souhaitée pour <?php echo $objet;?>.</p>
</div>

<div style="position:absolute;top:<?php $pos=$dimensions[1]+10;echo "$pos";?>px;left:0">
	<form name="form1" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<?php
echo add_token_field();
?>
	<input type="hidden" name="x" />
	<input type="hidden" name="y" />
	<input type="hidden" name="objet" value="<?=$objet;?>" />
	<input type="hidden" name="validation" value="validation" />
	<!--Il faut soumettre le formulaire sans clic ailleurs que dans l'image car le clic sur le bouton modifie Xpos et Ypos-->
	<!--input type="submit" name="Envoyer" value="Envoyer"-->
	</form>
</div>
</body>
</html>
