<?php
/**
 *
 *
 * Copyright 2015 Régis Bouguin
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
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

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}


//On vérifie si le module est activé
//if (getSettingValue("active_module_liste_perso")!='y') {
if (getSettingAOui("active_module_liste_perso")) {
    //die("Le module n'est pas activé.");
}
if ($utilisateur->getStatut()!=="cpe" 
   && $utilisateur->getStatut()!=="scolarite" 
   && $utilisateur->getStatut()!=="professeur")  {
	header("Location: ../logout.php?auto=1");
	die("Vous n'avez pas les droits pour cette page.");
}


	


//==============================================
$style_specifique[] = "mod_listes_perso/lib/style_liste";
$javascript_specifique = "mod_listes_perso/lib/js_listes_perso";
$titre_page = "Listes personnelles";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

?>

<ul class="menu_entete_liste">
    <li class="menu_liste"
		id='menu_lien_tableau'
		onclick=" affiche('tableau'); masque('construction'); masque('eleves'); masque('affichage'); "
		>
		<a href="#lien_tableau">Listes perso</a>
	</li>
    <li class="menu_liste"
		id='menu_affichage'
		onclick=" masque('construction'); masque('tableau'); masque('eleves');affiche('affichage'); "
		>
		<a href="#lien_affichage">Affichage</a>
	</li>
    <li class="menu_liste" 
		id='menu_eleves'
		onclick="affiche('eleves'); masque('tableau'); masque('affichage'); masque('construction'); "
		>
		<a href="#lien_eleves">Choix élèves</a>
	</li>
    <li class="menu_liste" 
		id='menu_construction'
		onclick="affiche('construction'); masque('tableau'); masque('affichage'); masque('eleves'); "
		>
		<a href="#lien_construction">Construction</a>
	</li>
</ul>

<div id="tableau" style="display:block;">
	<p><a id='lien_tableau'></a>0</p>
</div>
<div id="affichage" style="display:block;">
	<p><a id='lien_affichage'></a>1</p>
</div>

<div id="eleves" style="display:block;">
	<p><a id='lien_eleves'></a>2</p>
</div>

<div id="construction" style="display:block;">
	<p><a id='lien_construction'></a> 3</p>
</div>

<script type="text/javascript" >
	afficher_cacher("affichage");
	afficher_cacher("eleves");
	afficher_cacher("construction");			
</script>
<?php
require_once("../lib/footer.inc.php");
