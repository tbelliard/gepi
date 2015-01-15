<?php

/**
 * Fichier temporaire uniquement présent dans les versions RC pour teter les configurations serveur
 * et d'autres paramètres pour comprendre certaines erreurs.
 *
 *
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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



// On initialise
$titre_page = "Administration - Paramètres du serveur";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Définition de la classe php
require_once("../class_php/serveur_infos.class.php");

//fonction de tests d'encodage
require_once(dirname(__FILE__)."/test_encoding_functions.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Traitement de force_error_reporting
if (isset($_POST['force_error_reporting'])) {
	check_token();
	saveSetting('force_error_reporting',$_POST['force_error_reporting']);
	}

// Instance de la classe infos (voir serveur_infos.class.php)
$test_infos_serveur = new infos;

// Analyse des paramètres
if ($test_infos_serveur->secureServeur() == 'on') {
	$style_register = ' style="color: red; font-weight: bold;"';
}elseif($test_infos_serveur->secureServeur() == 'off'){
	$style_register = '';
}else{
	$style_register = ' style="color: red; font-style: italic;"';
}
if ($test_infos_serveur->maxExecution() <= '30') {
	$warning_maxExec = '&nbsp;(Cette valeur peut être un peu courte si votre établissement est important)';
}else{
	$warning_maxExec = '&nbsp;(Cette valeur devrait suffire dans la grande majorité des cas)';
}
$charset = $test_infos_serveur->defautCharset();
/*+++++++++++++++++++++ On insère l'entête de Gepi ++++++++++++++++++++*/
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "edt_organisation/style_edt";

require_once("../lib/header.inc.php");
/*++++++++++++++++++++++ fin entête ++++++++++++++++++++++++++++++++++++*/
echo '
<p class="bold"><a href="../gestion/index.php#test_serveur">
	<img src="../images/icons/back.png" alt="Retour" class="back_link" /> Retour</a>
</p>
';


/* ======= Affichage des paramètres ============= */

$OS=PHP_OS." - ".@php_uname();
if($OS==" - ") {$OS="indéterminé";}

echo '
	<h4>Les données de base de votre serveur web :</h4>
	<p>OS serveur&nbsp;: '.$OS.'</p>
	<p'.$style_register.'>Le register_globals est à '.$test_infos_serveur->secureServeur().'.</p>
	<p>Le serveur web est '.$test_infos_serveur->version_serveur().'</p>
	<p>Encodage '.$charset['toutes'].' -> encodage par défaut : '.$charset['defaut'].'.</p>';

echo '<p>Votre version de php est la '.$test_infos_serveur->versionPhp().'.</p>
	<p>Votre version de serveur de base de données MySql est la '.$test_infos_serveur->versionMysql().'.</p>';
if ($test_infos_serveur->versionGd()) {
	echo '<p>Votre version du module GD est la '.$test_infos_serveur->versionGd().'&nbsp;(indispensable pour toutes les images).</p>';
} else {
	echo '<p class="red">GD n\'est pas installé (le module GD est indispensable pour les images)';
}
	echo '<br />
	<hr />
	<h4>&nbsp;&nbsp;Liste des modules implémentés avec votre php : </h4>'.$test_infos_serveur->listeExtension().'
	<hr />
	<a name="reglages_php"></a>
	<h4>Les réglages php : </h4>
	<ul style="list-style-type:circle; margin-left:3em;">
	<li style="list-style-type:circle">La mémoire maximale allouée à php est de '.$test_infos_serveur->memoryLimit().' (<i>memory_limit</i>).
	</li>
	<li style="list-style-type:circle">La taille maximum d\'une variable envoyée à Gepi ne doit pas dépasser '.$test_infos_serveur->maxSize().' (<i>post_max_size</i>).
	</li>
	<li style="list-style-type:circle">Le temps maximum alloué à php pour traiter un script est de '.$test_infos_serveur->maxExecution().' secondes'.$warning_maxExec.' (<i>max_execution_time</i>).
	</li>
	<li style="list-style-type:circle">La taille maximum d\'un fichier envoyé à Gepi est de '.$test_infos_serveur->tailleMaxFichier().' (<i>upload_max_filesize</i>).
	</li>';
	$max_file_uploads=ini_get('max_file_uploads');
	echo '
	<li style="list-style-type:circle">Il peut être uploadé au maximum '.$max_file_uploads.' fichier(s) à la fois (<i>max_file_uploads</i>).
	</li>';
	$session_gc_maxlifetime=ini_get("session.gc_maxlifetime");
	$session_gc_maxlifetime_minutes=$session_gc_maxlifetime/60;
	if((getSettingValue("sessionMaxLength")!="")&&($session_gc_maxlifetime_minutes<getSettingValue("sessionMaxLength"))) {
		echo '
	<li style="list-style-type:circle">La durée maximum de session est réglée à <span style="color:red; font-weight:bold;">'.$session_gc_maxlifetime.' secondes</span>, soit un maximum de <span style="color:red; font-weight:bold;">'.$session_gc_maxlifetime_minutes.' minutes</span> (<i>session.maxlifetime</i> dans le fichier php.ini).<br />
	Cela restreint la durée maximale de session davantage que ce qui est paramétré dans <a href="../gestion/param_gen.php#sessionMaxLength">Configuration générale</a>.</li>
	C\'est la valeur la plus faible/restrictive qui est prise en compte.</li>';
	}
	else {
		echo '
	<li style="list-style-type:circle">La durée maximum de session est réglée à '.$session_gc_maxlifetime.' secondes, soit un maximum de '.$session_gc_maxlifetime_minutes.' minutes (<i>session.maxlifetime</i> dans le fichier php.ini).</li>';
	}
	echo "</ul>\n";

	$suhosin_post_max_totalname_length=ini_get('suhosin.post.max_totalname_length');
	if($suhosin_post_max_totalname_length!='') {
		echo "<h4>Configuration suhosin</h4>\n";
		echo "<p>Le module suhosin est activé.<br />\nUn paramétrage trop restrictif de ce module peut perturber le fonctionnement de Gepi, particulièrement dans les pages comportant de nombreux champs de formulaire (<i>comme par exemple dans la page de saisie des appréciations par les professeurs</i>)</p>\n";

		$tab_suhosin=array('suhosin.cookie.max_totalname_length', 
		'suhosin.get.max_totalname_length', 
		'suhosin.post.max_totalname_length', 
		'suhosin.post.max_value_length', 
		'suhosin.post.max_vars', 
		'suhosin.request.max_totalname_length', 
		'suhosin.request.max_value_length', 
		'suhosin.request.max_vars');

		for($i=0;$i<count($tab_suhosin);$i++) {
			echo "- ".$tab_suhosin[$i]." = ".ini_get($tab_suhosin[$i])."<br />\n";
		}

		echo "En cas de problème, vous pouvez, soit désactiver le module, soit augmenter les valeurs.<br />\n";
		echo "Le fichier de configuration de suhosin est habituellement en /etc/php5/conf.d/suhosin.ini<br />\nEn cas de modification de ce fichier, pensez à relancer le service apache ensuite pour prendre en compte la modification.<br />\n";
	}

	echo "<br />\n";
	echo "<hr />\n";
	echo "<a name='force_error_reporting'></a><h4>Affichage des erreurs PHP</h4>\n";
	echo "<p>Il peut être nécessaire <b>momentanément</b> de configurer Gepi pour forcer l'affichage des erreurs PHP afin de résoudre des dysfonctionnements. Attention ! En temps normal l'affichage des erreurs PHP doit être désactivé.</p>\n";
	echo "<form action='#force_error_reporting' id='form_force_error_reporting' method='post'>\n";
	echo "Forcer l'affichage des erreurs PHP : ";
	echo "<input type='radio' name='force_error_reporting' id='force_error_reporting_y' value='y' ";
	if (getSettingAOui('force_error_reporting')) echo "checked";
	echo " onchange=\"document.getElementById('form_force_error_reporting').submit();\" >\n";
	echo "<label for='force_error_reporting_y' style='cursor: pointer;'>Oui</label>\n";
	echo " ";
	echo "<input type='radio' name='force_error_reporting' id='force_error_reporting_n' value='n' ";
	if (!getSettingAOui('force_error_reporting')) echo "checked";
	echo " onchange=\"document.getElementById('form_force_error_reporting').submit();\" >\n";
	echo "<label for='force_error_reporting_n' style='cursor: pointer;'>Non</label>\n";
	echo add_token_field();
	echo "</form>\n";
	echo "<br />\n";
	echo "<hr />\n";

	echo "<h4>Encodage des caractères : </h4>\n";
	if (function_exists('iconv')) {
	    echo "iconv est installé sur votre système<br />";
	} else {
	    echo "iconv n'est pas installé sur votre système, ça n'est pas indispensable mais c'est recomandé<br />";
	}
	if (function_exists('mb_convert_encoding')) {
	    echo "mbstring est installé sur votre système<br />";
	} else {
	    echo "<p style=\"color:red;\">mbstring (Chaînes de caractères multi-octets) n'est pas installé sur votre système, c'est nécessaire à partir de la version 1.6.0</p>";
	}
	
	echo "<p style=\"color:red;\">";
	if (!test_check_utf8()) {
	    echo ' : échec de test_check_utf8()</p>';
	} else {
	    echo "</p>réussite de test_check_utf8()<br />\n";
	}
	echo "<p style=\"color:red;\">";
	if (!test_detect_encoding()) {
	    echo ' : échec de test_detect_encoding()</p>';
	} else {
        echo "</p>réussite de test_detect_encoding()<br />\n";
	}
	echo "<p style=\"color:red;\">";
	if (!test_ensure_utf8()) {
	    echo ' : échec de test_ensure_utf8()</p>';
	} else {
	    echo "</p>réussite de test_ensure_utf8()<br />\n";
	}
	echo "<p style=\"color:red;\">";
	if (!test_casse_mot()) {
	    echo ' : échec de test_casse_mot()</p>';
	} else {
	    echo "</p>réussite de test_casse_mot()<br />\n";
	}
	echo "<br />\n";
	
	echo "<hr />\n";
	echo "<h4>Locales du système : </h4>\n";
	$locale = setlocale(LC_TIME,0);
	echo "locale actuellement utilisée : $locale";
	if (!strstr(mb_strtolower($locale), 'utf')) {
	    echo "<p style=\"color:red;\">";
	    echo 'Votre système ne semble pas avoir de locale utf-8 d\'installée. Il est possible que sans locale utf-8 certains affichages de dates soient inesthétiques.</p>';
	}
	echo "<br />\n";
	
	
	echo "<hr />\n";
	echo "<h4>Droits sur les dossiers : </h4>\n";
	echo "Certains dossiers doivent être accessibles en écriture pour Gepi.<br />\n";
	test_ecriture_dossier();
	echo "Si les droits ne sont pas corrects, vous devrez les corriger en FTP, SFTP ou en console selon l'accès dont vous disposez sur le serveur.<br />\n";

	echo "<br />\n";
	echo "<p>Test d'écriture dans le fichier de personnalisation des couleurs (<i>voir <a href='../gestion/param_couleurs.php'>Gestion générale/Paramétrage des couleurs</a></i>)&nbsp;:<br />";
	if(file_exists('../style_screen_ajout.css')){
            $test_ecriture_style_screen_ajout=test_ecriture_style_screen_ajout();
            if($test_ecriture_style_screen_ajout) {
                echo "Le fichier style_screen_ajout.css à la racine de l'arborescence Gepi est accessible en écriture.\n";
            } else {
                echo "<span style='color:red'><b>ERREUR</b>&nbsp;: Le fichier style_screen_ajout.css à la racine de l'arborescence Gepi n'a pas pu être créé ou n'est pas accessible en écriture.</span>\n";
            }
        }elseif(file_exists('../style_screen_ajout.css.ori')) {
            echo "<span style='color:red'> Le fichier style_screen_ajout.css.ori à la racine de l'arborescence Gepi doit être renommé en style_screen_ajout.css et être accessible en écriture.</span>\n";
        }else{
            echo "<span style='color:red'><b>ERREUR</b>&nbsp;: Le fichier style_screen_ajout.css à la racine de l'arborescence Gepi est manquant. Il faut en créer un vide qui doit être accessible en écriture.</span>\n";
        } 
	echo "</p>\n";

echo '<br /><br /><br />';

/**
 * inclusion du footer
 */
require_once("../lib/footer.inc.php");
?>
