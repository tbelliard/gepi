<?php
/*
 * Last modification  : 08/12/2006
 *
 * Copyright 2001, 2006 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 0;
// Initialisations files
require_once("./lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ./utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ./logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ./logout.php?auto=1");
    die();
}

// uid de pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post===$_SESSION['uid_prime']
 if(empty($_SESSION['uid_prime'])) { $_SESSION['uid_prime']=''; }
 if(empty($_GET['uid_post']) and empty($_POST['uid_post'])) {$uid_post='';}
    else { if (isset($_GET['uid_post'])) {$uid_post=$_GET['uid_post'];} if (isset($_POST['uid_post'])) {$uid_post=$_POST['uid_post'];} }
	$uid = md5(uniqid(microtime(), 1));
	   // on remplace les %20 par des espaces
	    $uid_post = my_eregi_replace('%20',' ',$uid_post);
	if($uid_post===$_SESSION['uid_prime']) { $valide_form = 'yes'; } else { $valide_form = 'no'; }
	$_SESSION['uid_prime'] = $uid;

// le référencement de GEPI se passe en 3 partie
// - 1er partie saisie des données
// - 2ème partie vérification des données
// 	-- possibilité de modifier ou de valider l'envoye
// - 3ème partie envoye des données vers le site national de GEPI

// variable du formulaire et de sa constitution
if (empty($_GET['etape']) AND empty($_POST['etape'])) { $etape="1"; }
    else { if (isset($_GET['etape'])) { $etape=$_GET['etape']; } if (isset($_POST['etape'])) { $etape=$_POST['etape']; } }

// si nous nous trouvons à la deuxième partie 
// nous insérons les informations dans la base du GEPI installé
if($etape==='2') {
	// on vérifie si le nom n'est pas vide dans la base sinon on le met à jour(update)
	// on ajoute(update) le statu de l'installation - production/test/formation
	// on ajoute(update) le RNE
}

if($etape==='3') {
	// on envoie les informations par un header avec des variable de SESSIONS
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
<title>Référencement de GEPI</title>
<meta HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
<META HTTP-EQUIV="Expires" CONTENT="0" />
<link rel="stylesheet" type="text/css" href="style.css" />

<script type="text/javascript">
function fermeFenetre() {
  window.open('','_parent','');
  window.close();
}
</script>

<style type="text/css">
label {
	margin-left: 0px ;
}
label:hover {
	cursor: pointer;
	cursor: hand; /* IE */
}

.input {
	width: 150px;
	background-color: #FFFFFF ;
	font-family: Arial, Helvetica, sans-serif ;
	color: #000000;
	border-style:solid; border-width:1px; border-color: #6F6968;
	margin: 2px;
}

select {
	width: 150px;
	background-color: #FFFFFF ;
	font-family: Arial, Helvetica, sans-serif ;
	color: #000000;
	border-style:solid; border-width:1px; border-color: #6F6968;
	margin: 2px;
}

</style>
</head>
<body bgcolor="#FFFFFF">
<center>

<?php if($etape==='explication') { ?>
<div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: #5A7ACF;  padding: 2px; margin-left: 2px; margin-right: 2px; margin-top: 2px; margin-bottom: 0px;  text-align: left;">
   <div style="border-style:solid; border-width:0px; border-color: #6F6968; background-color: #5A7ACF;  padding: 2px; margin: 2px; font-family: Helvetica,Arial,sans-serif; font-weight: bold; color: #FFFFFF;">Pourquoi s'enregistrer ?</div>
   <div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: #FFFFFF;  padding: 20px; margin: 2px; text-align:justify">
	En enregistrant votre &eacute;tablissement parmi les &eacute;tablissements utilisateurs de Gepi:<br /><br />
	<ul>
	  <li>vous encouragez l'&eacute;quipe des d&eacute;veloppeurs. (bénévoles pour la plupart, il faut le rappeler)</li>
	  <li>vous donnez du poids et de la consistance &agrave; la communaut&eacute; des utilisateurs de Gepi, ce qui peut avoir de l'importance au moment de certains choix politiques ou n&eacute;gociations techniques (demande d'ouverture des exports de Sconet par exemple).</li>
	  <li>vous pouvez &eacute;ventuellement devenir vous-m&ecirc;me un interlocuteur pour des coll&egrave;gues recherchant des informations sur Gepi.</li>
	</ul><br />
	<span style="font-style: italic">Cet enregistrement n'est pas obligatoire, cela va sans dire. La validation du formulaire d'enregistrement en ayant choisi l'option "ne pas m'enregistrer" fera dispara&icirc;tre le bandeau de rappel de votre page d'accueil.</span>
	<br /><br />
	<div style="text-align: center;"><a href="referencement.php?etape=1" title="Je souhaite enregistrer mon établissement.">Je souhaite enregistrer mon établissement.</a></div>
   </div>
</div>
<?php } ?>

<?php if($etape==='1') { ?>
<div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: #5A7ACF;  padding: 2px; margin-left: 2px; margin-right: 2px; margin-top: 2px; margin-bottom: 0px;  text-align: left;">
   <div style="border-style:solid; border-width:0px; border-color: #6F6968; background-color: #5A7ACF;  padding: 2px; margin: 2px; font-family: Helvetica,Arial,sans-serif; font-weight: bold; color: #FFFFFF;">Enregistrement de Gepi</div>
   <div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: #FFFFFF;  padding: 6px; margin: 2px; text-align:justify">
   <span style="font-style: italic">Cet enregistrement n'est pas obligatoire : en choisissant "ne pas m'enregistrer", aucune information ne sera envoyée, la seule action effectuée sera la désactivation du rappel en page d'accueil.</span>
   <div style="text-align: right;"><a href="referencement.php?etape=explication" title="Pourquoi enregistrer mon établissement ?">Pourquoi enregistrer mon établissement ?</a></div>
   </div>
   <div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: #FFFFFF;  padding: 0px; margin: 0px;">
	<form action="fenetre.php" method="post" enctype="text/plain">
   	    <fieldset style="background-color: #EFF3FF">
		<legend style="font-variant: small-caps;"> Formulaire </legend>
		<table style="width: 100%;" cellpadding="0" cellspacing="0">
	        <tr style="background-color: #DFE8FF"><td style="width: 300px;"><label for="pref211">Adresse mél du correspondant Gepi</label></td><td style="width: 150px;"><input maxlength="200" size="15" name="pref211" id="pref211" class="input" onfocus="javascript:this.select()" value="" /></td></tr>
	        <tr style="background-color: #EFF3FF"><td style="width: 300px;"><label for="pref1">Cette version de Gepi est installée à des fins</label></td><td style="width: 150px;">
		<select name="pref1" id="pref1">
			<option selected="selected">d'exploitation</option>
			<option>de test</option>
			<option>de démonstration</option>
			<option>autre</option>
		</select></td></tr></table>
		<strong>Votre établissement:</strong><br />
		<table style="width: 100%;" cellpadding="2" cellspacing="0"><tr style="background-color: #DFE8FF;"><td style="width: 300px;"><label for="pref2">Type</label></td><td style="width: 150px;">
		<select name="pref2" id="pref2">
			<option>------------</option>
			<option value="collège">Collège</option>
			<option value="lycée général">Lycée général</option>
			<option value="lycée professionnel">Lycée professionnel</option>
			<option value="lycée général/professionnel">Lycée général/professionnel</option>
			<option value="école">Ecole</option>
			<option value="autre">Autre</option>
		</select></td></tr>
		<tr style="background-color: #EFF3FF;"><td><label for="pref21">Nom</label></td><td><input maxlength="200" size="15" name="pref21" id="pref21" class="input" onfocus="javascript:this.select()" value="<?php echo getSettingValue("gepiSchoolName"); ?>" /></td></tr>
		<tr style="background-color: #DFE8FF;"><td><label for="pref31">Code postal</label></td><td><input maxlength="4" size="2" name="pref31" id="pref31" class="input" onfocus="javascript:this.select()" /></td></tr>
		<tr style="background-color: #EFF3FF;"><td><label for="pref3">N&deg; du d&eacute;partement</label></td><td><input maxlength="4" size="2" name="pref3" id="pref3" class="input" onfocus="javascript:this.select()" <?php $num_dp=getSettingValue("gepiSchoolZipCode"); ?> value="<?php echo $num_dp{0}.$num_dp{1}; ?>" /></td></tr>
		<tr style="background-color: #DFE8FF;"><td><label for="pref4">RNE</label></td><td><input maxlength="20" size="15" name="pref4" id="pref4" class="input" onfocus="javascript:this.select()" /></td></tr>

<?php /*		<tr style="background-color: #EFF3FF;"><td><label for="pref5">Mon établissement (nom patronymique et ville seulement) peut figurer dans la liste des "établissements utilisateurs de Gepi" sur le site public de Gepi.</label></td><td style="text-align: center;"><input checked="checked" name="pref5" id="pref5" type="radio" /></td></tr>
		<tr style="background-color: #DFE8FF;"><td><label for="pref6">Seule l'équipe des développeurs peut avoir connaissance de l'utilisation de Gepi par mon établissement.</label></td><td style="text-align: center;"><input name="pref5" id="pref6" type="radio" /></td></tr>
		<tr style="background-color: #EFF3FF;"><td><label for="pref7">Ne pas enregistrer mon établissement.</label></td><td style="text-align: center;"><input name="pref5" id="pref7" type="radio" /></td></tr>
*/ ?>
		<tr style="background-color: #EFF3FF;"><td colspan="2"><input checked="checked" name="pref5" id="pref5" type="radio" /><label for="pref5">Mon établissement (nom patronymique et ville seulement) peut figurer dans la liste des "établissements utilisateurs de Gepi" sur le site public de Gepi.</label></td></tr>
		<tr style="background-color: #DFE8FF;"><td colspan="2"><input name="pref5" id="pref6" type="radio" /><label for="pref6">Seule l'équipe des développeurs peut avoir connaissance de l'utilisation de Gepi par mon établissement.</label></td></tr>
		<tr style="background-color: #EFF3FF;"><td colspan="2"><input name="pref5" id="pref7" type="radio" /><label for="pref7">Ne pas enregistrer mon établissement.</label></td></tr>
		<tr><td></td><td style="text-align: center;">
			<input type="hidden" name="version" value="<?php echo getSettingValue("version"); ?>" />
			<input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
			<input type="submit" value="Valider" /></td></tr></table>
	   </fieldset>
	</form>
   </div>
</div>
<?php } ?>
<a href="javascript:window.close();">Fermer la fenêtre</a>
</center>
</body>
</html>
