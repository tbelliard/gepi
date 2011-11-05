<?php
/*
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
// On empêche l'accès direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};
require_once ("Controleur.php");
require_once("ImportModele.php");

/**
 * Contrôleur par défaut: Index
 */
class ImportCtrl extends Controleur {

    public $table;
    public $couleur;
    private $csv = null;
    private $ecriture = true;
    private $erreurs_lignes = Null;

    /**
     * Action par défaut
     */
    function index() {
        $this->vue->LoadTemplate('import.php');
        $this->vue->Show();
    }

    function result() {
        try {
            $this->tmp = $_FILES['fichier']['tmp_name'];
            if (is_uploaded_file($this->tmp)) {
                $this->copy_file($this->tmp);
            } else
                throw new Exception('Aucun fichier ne semble uploadé ');
            $this->csv = '../temp/'.get_user_temp_directory().'/correspondances.csv';            
            if (file_exists($this->csv)) {
                $this->traite_file($this->csv);
                unlink($this->csv);
                if (file_exists($this->csv)){
                    throw new Exception('Impossible de supprimer le fichier csv dans votre repertoire temp. Il est conseillé de le faire manuellement.');
                }
                if (is_null($this->erreurs_lignes)) {
                    $this->vue->LoadTemplate('result.php');
                    if(!is_null($this->table)) $this->vue->MergeBlock('b1', $this->table);
                    $this->vue->Show();
                } else {
                    foreach ($this->erreurs_lignes as $ligne) {
                        $this->table[] = Array("ligne" => $ligne);
                    }
                    $this->vue->LoadTemplate('erreurs_fichier.php');
                    $this->vue->MergeBlock('b1', $this->table);
                    $this->vue->Show();
                }
            } else
                throw new Exception('Le nom du fichier csv est incorrect ');
        } catch (Exception $e) {
            $this->vue->LoadTemplate('exceptions.php');
            $this->mess[] = Array('mess' => $e->getMessage());
            $this->vue->MergeBlock('b1', $this->mess);
            $this->vue->Show();
        }
    }

    private function copy_file($file) {
        $extension = strrchr($_FILES['fichier']['name'], '.');
        if ($extension == '.csv') {
            $this->file = $_FILES['fichier']['name'];
            if ($this->file == 'correspondances.csv') {
                $copie = move_uploaded_file($file,'../temp/'.get_user_temp_directory().'/'. $this->file);
            } else
                throw new Exception('Le nom du fichier est incorrect ');
        } else
            throw new Exception('Le fichier n\'est pas un fichier csv ');
    }

    private function traite_file($file) {

        if (!isset($_POST["choix"]) || ($_POST["choix"] == "ecrit")) {
            $this->ecriture = TRUE;
        } else {
            $this->ecriture = FALSE;
        }
        $data = new ImportModele();
        $this->verif_file($file);
        if (is_null($this->erreurs_lignes)) {
             $this->setVarGlobal('choix_info', 'affich_result');
            $this->fic = fopen($file, 'r');
            while (($this->ligne = fgetcsv($this->fic, 1000, ";")) !== FALSE) {
                $this->ligne[0] = traitement_magic_quotes(corriger_caracteres(htmlspecialchars($this->ligne[0], ENT_QUOTES)));
                $this->ligne[1] = traitement_magic_quotes(corriger_caracteres(htmlspecialchars($this->ligne[1], ENT_QUOTES)));
                $this->messages = $this->get_message($data->get_error($this->ligne[0], $this->ligne[1], $this->ecriture));
                if ($_POST["choix"] == "erreur" && $this->messages[0] == "message_red") {
                    $this->table[] = array('login_gepi' => $this->ligne[0], 'login_sso' => $this->ligne[1], 'couleur' => $this->messages[0], 'message' => $this->messages[1]);
                } else if ($_POST["choix"] != "erreur") {
                    $this->table[] = array('login_gepi' => $this->ligne[0], 'login_sso' => $this->ligne[1], 'couleur' => $this->messages[0], 'message' => $this->messages[1]);
                }
            }
            fclose($this->fic);
            if(is_null($this->table)) {
                if ($_POST["choix"] != "erreur") $this->setVarGlobal('choix_info', 'no_data');
                if ($_POST["choix"] == "erreur") $this->setVarGlobal('choix_info', 'no_error');
            }
        }
    }

    private function verif_file($file) {
        $this->fic = fopen($file, 'r');
        $ligne_erreur = 1;
        while (($this->ligne = fgetcsv($this->fic, 1000, ";")) !== FALSE) {
            if (sizeof($this->ligne) != 2) {
                $this->erreurs_lignes[] = $ligne_erreur;
            }
            $ligne_erreur++;
        }
        fclose($this->fic);
        return($this->erreurs_lignes);
    }

    private function get_message($code) {
        //$NomBloc   : nom du bloc qui appel la fonction (lecture seule)
        //$CurrRec   : tableau contenant les champs de l'enregistrement en cours (lecture/écriture)
        //$RecNum    : numéro de l'enregsitrement en cours (lecture seule)
        switch ($code) {
            case 0:
                $this->class = "message_red";
                $this->message = 'Une entrée existe déja dans la table pour ce login gépi';
                break;
            case 1:
                $this->class = "message_red";
                $this->message = 'Une entrée existe déja dans la table pour ce login sso';
                break;
            case 2:
                $this->class = "message_red";
                $this->message = 'L\'utilisateur n\'existe pas dans gépi.';
                break;
            case 3:
                $this->class = "message_orange";
                $this->message = 'L\'utilisateur existe mais son compte n\'est pas paramétré pour le sso. Il faut corriger absolument pour que la correspondance fonctionne.';
                break;
            case 5:
                $this->class = "message_red";
                $this->message = 'Aucune des deux valeurs ne peut être vide. Il faut rectifier cela.';
                break;
            default:
                $this->class = "message_green";
                $this->message = 'La correspondance est mise en place.';
        }
        return array($this->class, $this->message);
    }

}

?>