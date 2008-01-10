<?php

/**
 * Fichier qui permet de construire la barre de menu
 *
 * @version $Id$
 * @copyright 2008
 */

/* On fixe l'ensemble des modules qui sont ouverts pour faire la liste des <li> */
	// module absence
	if (getSettingValue("active_module_absence_professeur")=='y') {
		$absence = '<li><a href="'.$gepiPath.'/mod_absences/professeurs/prof_ajout_abs.php">Absences</a></li>';
	}else{$absence = '';}

	// Module Cahier de textes
	if (getSettingValue("active_cahiers_texte") == 'y') {
		$textes = '<li><a href="'.$gepiPath.'/cahier_texte/index.php">C. de Textes</a></li>';
	}else{$textes = '';}

	// Module carnet de notes
	if(getSettingValue("active_carnets_notes") == 'y'){
		$note = '<li><a href="'.$gepiPath.'/cahier_notes/index.php">Notes</a></li>
		<li><a href="'.$gepiPath.'/saisie/index.php">Bulletins</a></li>';
	}else{$note = '';}

	// Module emploi du temps
	if (getSettingValue("autorise_edt_tous") == "y") {
		$edt = '<li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=prof1&amp;login_edt='.$_SESSION["login"].'&amp;type_edt_2=prof">Emploi du tps</a></li>';
	}else{$edt = '';}

	echo '
	<ol id="essaiMenu">
		<li><a href="'.$gepiPath.'/accueil.php">Accueil</a></li>
		'.$absence.'
		'.$textes.'
		'.$note.'
		'.$edt.'
		<li><a href="'.$gepiPath.'/utilisateurs/mon_compte.php">Mon compte</a></li>
	</ol>
	';
?>