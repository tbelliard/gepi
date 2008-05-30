<?php

/**
 * Fichier qui permet de construire la barre de menu
 *
 * @version $Id$
 * @copyright 2008
 */
// ====== SECURITE =======

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

	// Pour permettre d'utiliser le module EdT avec les autres modules
	$groupe_abs = $groupe_text = '';
	if (getSettingValue("autorise_edt_tous") == "y") {
		// Actuellement, ce professeur à ce cours (id_cours):
		$cours_actu = retourneCours($_SESSION["login"]);
		// Qui correspond à cet id_groupe :
		if ($cours_actu != "non") {
			$queryG = mysql_query("SELECT id_groupe FROM edt_cours WHERE id_cours = '".$cours_actu."'");
			$groupe_actu = mysql_fetch_array($queryG);
			// Il faudrait vérifier si ce n'est pas une AID
			$test = explode("|", $groupe_actu["id_groupe"]);
			if ($test[0] == "AID") {
				$groupe_abs = '?groupe='.$groupe_actu["id_groupe"].'&amp;menuBar=ok';
				$groupe_text = '';
			}else{
				$groupe_text = '?id_groupe='.$groupe_actu["id_groupe"].'&amp;year='.date("Y").'&amp;month='.date("n").'&amp;day='.date("d").'&amp;edit_devoir=';
				$groupe_abs = '?groupe='.$groupe_actu["id_groupe"].'&amp;menuBar=ok';
			}
		}
	}

/* On fixe l'ensemble des modules qui sont ouverts pour faire la liste des <li> */
	// module absence
	if (getSettingValue("active_module_absence_professeur")=='y') {
		$absence = '<li><a href="'.$gepiPath.'/mod_absences/professeurs/prof_ajout_abs.php'.$groupe_abs.'">Absences</a></li>';
	}else{$absence = '';}

	// Module Cahier de textes
	if (getSettingValue("active_cahiers_texte") == 'y') {
		$textes = '<li><a href="'.$gepiPath.'/cahier_texte/index.php'.$groupe_text.'">C. de Textes</a></li>';
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

	// Module notanet
	if (getSettingValue("active_notanet") == "y") {
		$notanet = '<li><a href="'.$gepiPath.'/mod_notanet/index.php">Brevet</a></li>';
	}else{ $notanet = '';}


	echo '
	<ol id="essaiMenu">
		<li><a href="'.$gepiPath.'/accueil.php">Accueil</a></li>
		'.$absence.'
		'.$textes.'
		'.$note.'
		'.$edt.'
		'.$notanet.'
		<li><a href="'.$gepiPath.'/utilisateurs/mon_compte.php">Mon compte</a></li>
	</ol>
	';
?>