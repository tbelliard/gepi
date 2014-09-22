#!/usr/bin/php -q
<?php

// Initialisations, pour avoir l'environnement disponible.
require_once ("../lib/initialisations.inc.php");
require_once ("./update_functions.php");

// Initialisation des options
$force = false; // Force une application de tous les scripts de mise à jour
$start_from = $gepiSettings['version']; // Permet d'appliquer les mises à jour à partir d'une version donnée

$script_error=false;

if ($argc != 2) {
    $script_error = true;
} else {
    // Premier argument (obligatoire, pour éviter les accidents)
    if (isset($argv[1]) && in_array($argv[1], array('1.4.4','1.5.0','1.5.1','1.5.2','1.5.3','1.5.3.1','1.5.4','1.5.5','1.6.0','1.6.1','1.6.2','1.6.3','1.6.4','1.6.5','defaut','forcer'))) {
        if ($argv[1] == 'forcer') {
            $force = true;
        } elseif($argv[1] == 'defaut'){
            $start_from = $gepiSettings['version'];
            // Si la version actuelle est un trunk, on force une mise à jour complète.
            if (($start_from == 'trunk')||($start_from == 'master')) $force = true;
        }
        $start_from = $argv[1];
    } else {
        $script_error = true;
    }
}

if ($script_error || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
?>

Ce script requiert des options.

Utilisation :
<?php echo $argv[0]; ?> <version>

<version> peut prendre trois valeurs différentes :
    
    - defaut : indique au script qu'il doit déterminer lui-même la version actuelle
               de votre Gepi (à titre indicatif, pour votre installation : <?php echo $gepiSettings['version'];?>)

    - forcer : le script appliquera la totalité des mises à jour disponible.
               Cette opération est en principe sans risque.
               
    - un numéro de version (ex: 1.5.1) : en spécifiant manuellement un numéro de
               version, le script démarrera explicitement la mise à jour à partir
               de cette version. Soyez sûr de vous si vous spécifiez une version
               manuellement !

En spécifiant seulement --help, -help, -h, et -?, vous pouvez afficher à nouveau ce
message d'aide.

Exemples d'utilisation :

./maj.sh defaut
    Lance une mise à jour avec calcul automatique de la version actuelle de votre
    Gepi.

./maj.sh forcer
    Force une mise à jour complète, depuis le script le plus ancien disponible
    avec votre installation de Gepi.

./maj.sh 1.5.0
    Applique les mises à jour depuis la version 1.5.0.


<?php
} else {
// Si on arrive ici, c'est qu'on a les bons arguments, et qu'on peut appliquer
// la mise à jour.

    $pb_maj = '';
    $result = '';
    $result_inter = '';

    // Remise à zéro de la table des droits d'accès
    require './updates/access_rights.inc.php';


    if ($force || $start_from == '1.4.4') {
        require './updates/144_to_150.inc.php';
    }


    if ($force || $start_from == '1.5.0') {
        require './updates/150_to_151.inc.php';
    }


    if ($force || $start_from == '1.5.1') {
        require './updates/151_to_152.inc.php';
    }


    if ($force || $start_from == '1.5.2') {
        require './updates/152_to_153.inc.php';
    }

    if ($force || $start_from == '1.5.3') {
        require './updates/153_to_1531.inc.php';
    }

    if ($force || $start_from == '1.5.3.1') {
        require './updates/1531_to_154.inc.php';
    }

    if ($force || $start_from == '1.5.4') {
        require './updates/154_to_155.inc.php';
    }

    if ($force || $start_from == '1.5.5') {
        require './updates/155_to_160.inc.php';
    }

    if ($force || $start_from == '1.6.0') {
        require './updates/160_to_161.inc.php';
    }

    if ($force || $start_from == '1.6.1') {
        require './updates/161_to_162.inc.php';
    }

    if ($force || $start_from == '1.6.2') {
        require './updates/162_to_163.inc.php';
    }

    if ($force || $start_from == '1.6.3') {
        require './updates/163_to_164.inc.php';
    }

    if ($force || $start_from == '1.6.4') {
        require './updates/164_to_165.inc.php';
    }

    if ($force || $start_from == '1.6.5') {
        require './updates/165_to_dev.inc.php';
    }

// Test sur la version des plugins (installés ou pas)
require_once("../mod_plugins/verif_version_plugins.php");
$verif_version_plugins=verif_version_plugins(1,"\n");
if ($verif_version_plugins!="") {
	echo "\nAttention ! le ou les plugins suivants :\n";
	echo $verif_version_plugins;
	echo "\nne semblent pas adaptés à la version courante de Gepi (".$gepiVersion.").";
	echo "\n\n";
}

// Nettoyage pour envoyer le résultat dans la console
    $result = str_replace('<br />',"\n",$result);
    $result = str_replace('<br/>',"\n",$result);
    $result = str_replace('&nbsp;','',$result);
    $result = preg_replace('/<font\b[^>]*>/','',$result);
    $result = preg_replace('/<\/font>/','',$result);
    $result = preg_replace('/<b>/','',$result);
    $result = preg_replace('/<\/b>/','',$result);
    echo $result;

    // Mise à jour du numéro de version
    saveSetting("version", $gepiVersion);
    saveSetting("pb_maj", $pb_maj);

}
?>
