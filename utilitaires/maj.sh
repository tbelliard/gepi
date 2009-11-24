#!/usr/bin/php -q
<?php

// Initialisations, pour avoir l'environnement disponible.
require_once ("../lib/initialisations.inc.php");
require_once ("./update_functions.php");

// Initialisation des options
$force = false; // Force une application de tous les scripts de mise à jour
$start_from = $gepiSettings['version']; // Permet d'appliquer les mises à jour à partir d'une version donnée

if ($argc != 2) {
    $script_error = true;
} else {
    // Premier argument (obligatoire, pour éviter les accidents)
    if (isset($argv[1]) && in_array($argv[1], array('1.4.4','1.5.0','1.5.1','1.5.2','defaut','forcer'))) {
        if ($argv[1] == 'forcer') {
            $force = true;
        } elseif($argv[1] == 'defaut'){
            $start_from = $gepiSettings['version'];
            // Si la version actuelle est un trunk, on force une mise à jour complète.
            if ($start_from == 'trunk') $force = true;
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

    // Numéro de version effective
    $version_old = $gepiSettings['version'];
    // Numéro de version RC effective
    $versionRc_old = $gepiSettings['versionRc'];
    // Numéro de version Beta effective
    $versionBeta_old = $gepiSettings['versionBeta'];

    $rc_old = '';
    if ($versionRc_old != '') {
            $rc_old = "-RC" . $versionRc_old;
    }
    $rc = '';
    if ($gepiRcVersion != '') {
            $rc = "-RC" . $gepiRcVersion;
    }

    $beta_old = '';
    if ($versionBeta_old != '') {
            $beta_old = "-beta" . $versionBeta_old;
    }
    $beta = '';
    if ($gepiBetaVersion != '') {
            $beta = "-beta" . $gepiBetaVersion;
    }


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
    saveSetting("versionRc", $gepiRcVersion);
    saveSetting("versionBeta", $gepiBetaVersion);
    saveSetting("pb_maj", $pb_maj);

}
?>
