<?php
    /**
     * Fichier permettant de créer la classe qui étend activeRecordGepi
     * en mettant en dur les propriétés et les méthodes usuelles
     *
     * @author Julien Jocal
     */

// Variables
$_classe = isset($_POST["table"]) ? $_POST["table"] : NULL;

if ($_classe !== NULL){
    // On considère que le nouveau fichier va être créé dans le répertoire courant
    $filename = ucfirst(substr($_classe, 0, -1)); // on enlève le s à la fin et on met une majuscule au début
    $fichier = fopen($filename, "r+");


    // Ici, on calcule ce qui doit être écrit
    $texte_a_ecrire = '';





    fwrite($fichier, $texte_a_ecrire);
    fclose();
}


?>
