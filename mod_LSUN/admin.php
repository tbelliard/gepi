<?php

/*
*
* Copyright 2016 Régis Bouguin
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 
*/


// Initialisations files
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

include_once 'lib/requetes_tables.php';

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

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

$sauver = filter_input(INPUT_POST, 'valider') ==="y" ? TRUE : FALSE ;
$ouvre = filter_input(INPUT_POST, 'ouvre') ? filter_input(INPUT_POST, 'ouvre') : 'n';



//==============================================
include_once 'lib/fonctions.php';
//==============================================
if ($sauver) {
	droitLSUN($ouvre);
}
$droit = droitLSUN();


//$droit = DroitSurListeOuvert();


$titre_page = "Ouverture du Livret Scolaire Unique";
if (!suivi_ariane($_SERVER['PHP_SELF'],"Ouverture LSU")) {echo "erreur lors de la création du fil d'ariane";}
require_once("../lib/header.inc.php");

?>



<form action="admin.php" method="post" name="formulaire" id="formulaire">
	<fieldset>
		<p>
			<input type="radio" 
				   id="ouvreDroit" 
				   name="ouvre"
					<?php if($droit) {echo " checked ";} ?>
				   value="y" />
			<label for="choix">Ouverture du module <em>Livret Scolaire Unique</em></label>
		</p>
		
		<p>
			<input type="radio" 
				   id="fermeDroit" 
				   name="ouvre"
					<?php if(!$droit) {echo " checked ";} ?>
				   value="n" />
			<label for="fermeDroit">
				Fermer le module LSU
			</label>
		</p>
		<button name="valider" value="y">Valider</button>
		
	</fieldset>
</form>
<?php
debug_var();
require_once("../lib/footer.inc.php");
