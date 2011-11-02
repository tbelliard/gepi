<?php
/*
 * $Id: ajax_devoirs_classe.php 6856 2011-04-30 17:18:39Z crob $
 *
 * Copyright 2009-2011 Josselin Jacquard, Stephane Boireau
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

header('Content-Type: text/html; charset=ISO-8859-1');
// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) {$traite_anti_inject = "yes";}
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	//header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	//header("Location: ../logout.php?auto=1");
	die();
}

// INSERT INTO droits SET id='/cahier_texte_2/ajax_devoirs_classe.php',administrateur='V',professeur='V',cpe='V',scolarite='V',eleve='F',responsable='F',secours='F',autre='V',description='Cahiers de textes : Devoirs d une classe pour tel jour',statut='';

if (!checkAccess()) {
	//header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

// Récupération des variables
$id_classe=isset($_GET["id_classe"]) ? $_GET["id_classe"] : NULL;
$today=isset($_GET["today"]) ? $_GET["today"] : NULL;

//debug_var();

// Contrôler que le prof est associé à cette classe?

$id_classe=preg_replace("/[^0-9]/","",$id_classe);
$today=preg_replace("/[^0-9]/","",$today);

if($id_classe=="") {
	echo "<p style='color:red;'>L'identifiant de classe est incorrect.</p>";
}
elseif($today=="") {
	echo "<p style='color:red;'>Le format de la date (<i>timestamp</i>) est incorrect.</p>";
}
else {
	require("cdt_lib.php");
	
	echo devoirs_tel_jour($id_classe, strftime("%d/%m/%Y", $today), "n");
}
?>
