<?php
/*
 * $Id: notanet_admin.php 1353 2008-01-13 17:20:41Z jjocal $
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
   header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
   die();
} else if ($resultat_session == '0') {
   header("Location: ../logout.php?auto=1");
   die();
};

// Check access
if (!checkAccess()) {
   header("Location: ../logout.php?auto=1");
   die();
}


if (isset($_POST['activer'])) {
    if (!saveSetting("active_inscription", $_POST['activer'])){
		$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
	}
	else{
		$msg = "Les modifications ont été enregistrées !";
	}
}


// header
$titre_page = "Gestion du module Inscription";
require_once("../lib/header.inc");

?>
<p class='bold'><a href="../accueil_modules.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2>Activation du module Inscription</h2>

<form action="inscription_admin.php" name="form1" method="post">
<p>Le module Inscription vous permet de définir un ou plusieurs items (stage, intervention, ...),
au(x)quel(s) les professeurs pourront s'inscrire ou se désinscrire en cochant ou décochant une croix.<br />
<ul>
<li>La configuration du module est accéssible aux administrateurs.</li>
<li>L'interface d'inscription/désinscription est accessible aux professeurs, cpe, administrateur et vie scolaire.</li>
</ul>


<input type="radio" name="activer" value="y" <?php if (getSettingValue("active_inscription")=='y') echo " checked"; ?> />
&nbsp;Activer l'accès au module Inscription<br />
<input type="radio" name="activer" value="n" <?php if (getSettingValue("active_inscription")=='n') echo " checked"; ?> />
&nbsp;Désactiver l'accès au module Inscription</p>

<input type="hidden" name="is_posted" value="1" />

<br />
<br />
<center><input type="submit" value="Enregistrer" style="font-variant: small-caps;" /></center>
</form>

<?php require("../lib/footer.inc.php");?>