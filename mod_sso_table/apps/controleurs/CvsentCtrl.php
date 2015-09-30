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
class CvsentCtrl extends Controleur {

    public $table;
    public $couleur;
    private $csv = null;
    private $ecriture = false;
    private $erreurs_lignes = Null;
    private $libel_eleve;
    private $libel_responsable;
    private $libel_enseignant;

    /**
     * Action par défaut
     */
    function index() {
        $this->vue->LoadTemplate('cvsent.php');
        $this->vue->Show();
    }

    function result() {
        try {
            $this->tmp = $_FILES['fichier']['tmp_name'];
            if (is_uploaded_file($this->tmp)) {
                $this->copy_file($this->tmp);
            } else
                throw new Exception('Aucun fichier ne semble uploadé ');
            $this->csv = '../temp/'.get_user_temp_directory().'/ENT-Identifiants.csv';
            if (file_exists($this->csv)) {
                $this->traite_file($this->csv);
                unlink($this->csv);
                if (file_exists($this->csv)){
                    throw new Exception('Impossible de supprimer le fichier csv dans votre repertoire temp. Il est conseillé de le faire manuellement.');
                }
                if (is_null($this->erreurs_lignes)) {
                    $this->vue->LoadTemplate('result.php');
                    if (!is_null($this->table)
                        )$this->vue->MergeBlock('b1', $this->table);
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
                throw new Exception('Le nom du fichier csv est incorrect');
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
            if ($this->file == 'ENT-Identifiants.csv') {
                $copie = move_uploaded_file($file,'../temp/'.get_user_temp_directory().'/'. $this->file);
            } elseif ($this->file == 'ENT-Identifiants-'.mb_strtoupper(getSettingValue('gepiSchoolRne')).'.csv') {
                $copie = move_uploaded_file($file,'../temp/'.get_user_temp_directory().'/ENT-Identifiants.csv');
            } elseif (preg_match("/^ENT-Identifiants-".mb_strtoupper(getSettingValue('gepiSchoolRne'))."[A-Za-z0-9_]*.csv$/", $this->file)) {
                $copie = move_uploaded_file($file,'../temp/'.get_user_temp_directory().'/ENT-Identifiants.csv');
            } else
                throw new Exception('Le nom du fichier est incorrect');
        } else
            throw new Exception('Le fichier n\'est pas un fichier csv ');
    }

    private function traite_file($file) {

	$tab_corresp=array();

	$sql="TRUNCATE tempo2_sso;";
	$menage=mysqli_query($GLOBALS["mysqli"], $sql);

        $data = new ImportModele();
        $this->verif_file($file);
        if (is_null($this->erreurs_lignes)) {
            // on crée la table des imports ENT
            $data->cree_table_import();

            $this->fic = fopen($file, 'r');
            skip_bom_utf8($this->fic);
            $statut = 'eleve';
            while (($this->ligne = fgetcsv($this->fic, 1024, ";")) !== FALSE) {
             foreach($this->ligne as &$value){
                     $value= ensure_utf8($value);
             }
             
/*
// DEBUG : 20150929
echo "<pre>";
print_r($this->ligne);
echo "</pre>";
*/
                // On charge la table temporaire
                //$this->ligne[0] : rne
                //$this->ligne[1] : uid
                //$this->ligne[2] : classe
                //$this->ligne[3] : statut
                //$this->ligne[4] : prénom
                //$this->ligne[5] : nom
                //$this->ligne[6] : login
                //$this->ligne[7] : mot de passe
                //$this->ligne[8] : cle de jointure
                //$this->ligne[9] : uid pere
                //$this->ligne[10] : uid pere
                //$this->ligne[11] : uid tuteur1
                //$this->ligne[12] : uid tuteur2
                // si on a un élève, il a un père ou une mère ou un tuteur 1 ou un tuteur 2
                if ($this->ligne[9] != "" || $this->ligne[10] != "" || $this->ligne[11] != "" || $this->ligne[12] != "") {
                // DEBUG : 20150929
                //if ((((mb_strtolower($this->ligne[3])=='eleve')||(mb_strtolower($this->ligne[3])=='tuteur'))&&($this->ligne[2] != "")) || $this->ligne[10] != "" || $this->ligne[11] != "" || $this->ligne[12] != "") {
                // NON : le $recherche est utilisé pour voir si le statut est élève ou non.
                //       Avec $recherche=true, on effectue une recherche dans cherche_login() avec statut LIKE 'eleve'
                    $recherche = TRUE;
                } else {
                    $recherche = FALSE;
                }

// DEBUG : 20150929
//echo "\$recherche=$recherche<br />";
                /*
                echo "$statut<pre>";
                print_r($this->ligne);
                echo "</pre><hr />";
                */
                $this->res = $data->cherche_login($this->ligne, $statut, $recherche);
                if (mysqli_num_rows($this->res) == 1) {
                    // on a un seul utilisateur dans Gepi
                    $row = mysqli_fetch_row($this->res);
                    $login_gepi = $row[0];
                } else {
                    // Pour les autres cas, il faut attendre que la table soit remplie
                    $login_gepi = '';
                }
// DEBUG : 20150929
//echo "\$login_gepi=$login_gepi<br />";
                // On n'inscrit pas un élève ou parent d'un ancien élève plus dans aucune classe cette année... sinon, l'association avec le petit frère va être refusée avec le login_sso UNIQUE/INDEX
                if($this->ligne[2] != "") {
                    $data->ligne_table_import($this->ligne, $login_gepi);
                    $tab_corresp[$this->ligne[1]]=$login_gepi."|".$this->ligne[6]."|".$this->ligne[7];
                }
            }

            // regrouper dans un seul enregistrement les UID présents plusieurs fois
            $data->cree_index_uid();

            // récupérer les libellés élèves, responsables, enseignants
            //$this->req= "SELECT DISTINCT `statut` FROM `utilisateurs` u, `plugin_sso_table_import` e  WHERE u.`statut` = 'professeur' AND e.login = u.login AND e.login != '' ";
            // Ne fonctionne pas, certains profs ont un statut 'personnel' dans l'ENT (remplaçant pas encore remonté, prof des école UPI...)
            // supprimer les élèves sans classe
            $this->res = $data->trouve_statut_eleves();
            if (mysqli_num_rows($this->res) == 1) {
                $row = mysqli_fetch_row($this->res);
                $this->libel_eleve = $row[0];
            } else {
                echo "il y a " . mysqli_num_rows($this->res) . " dénominations pour le statut élève ";
                die ();
            }
            $data->supprime_sans_classe($this->libel_eleve);

            // supprimer les responsables sans classe
            $this->res = $data->trouve_statut_responsables();
            if (mysqli_num_rows($this->res) == 1) {
                $row = mysqli_fetch_row($this->res);
                $this->libel_responsable = $row[0];
            } else {
                echo "il y a " . mysqli_num_rows($this->res) . " dénominations pour le statut responsable ";
                die ();
            }
            $data->supprime_sans_classe($this->libel_responsable);
            // supprimer les responsables sans élève (erreurs dans l'ENT)
            /*
              // si on a un tuteur dans l'ENT qui n'est que tuteur1 ou tuteur2, on peut le supprimer
              $this->tuteur = $data->est_que_tuteur();
              if (mysql_num_rows($this->tuteur) != 0) {
              while ($this->row = mysql_fetch_array($this->tuteur)) {
              // on a bien un compte tuteur dans l'ENT, on peut le supprimer il n'a pas de compte dans Gepi
              $data->del_by_uid($this->row['uid']);
              }
              }
             * 
             */


            /* On traite les doublons */
            // On recherche les enregistrements sans login
            $this->res = $data->login_vide();
            while ($this->row = mysqli_fetch_array($this->res)) {
                $login = '';
                // si on a un responsable, on le retrouve dans père ou mère ou tuteur 1 ou tuteur 2
                $this->resp = $data->est_responsable($this->row);
                if (mysqli_num_rows($this->resp) != 0) {
                    // on a bien un responsable
                    // on regarde déjà si la recherche sur responsable ne régle pas le problème
                    $this->resp1 = $data->cherche_login($this->row, 'responsable');
                    if (mysqli_num_rows($this->resp1) == 1) {
                        $row1 = mysqli_fetch_assoc($this->resp1);
                        $login = $row1['login'];
                    } else if (mysqli_num_rows($this->resp) != 0) {
                        // on recherche le responsable avec ce nom et prénom ayant cet élève
                        $this->eleve = mysqli_fetch_assoc($this->resp);
                        $this->resp2 = $data->cherche_responsable($this->eleve, $this->row);
                        if (mysqli_num_rows($this->resp2) != 0) {
                            $row2 = mysqli_fetch_row($this->resp2);
                            $login = $row2[0];
                        }
                    } else {
                        // on a pas trouver d'élève, il va falloir traiter à la main
                        $login = '';
                    }
                } else {
                    // si on a un élève, il a un père ou une mère ou un tuteur 1 ou un tuteur 2
                    $this->reselv = $data->est_eleve($this->row);
                    if (mysqli_num_rows($this->reselv) != 0) {
                        // on a bien un élève, on recherche l'élève ayant un de ces responsables
                        $this->responsable = mysqli_fetch_assoc($this->reselv);
                        $this->reselv2 = $data->cherche_eleve($this->responsable, $this->row);
                        if (mysqli_num_rows($this->reselv2) == 1) {
                            $rowelv2 = mysqli_fetch_row($this->reselv2);
                            $login = $rowelv2[0];
                        }
                    }
                    // les autres sont ni élève ni responsable
                    $this->resautre = $data->doublon_pro($this->row, $this->libel_eleve, $this->libel_responsable);
                    if (mysqli_num_rows($this->resautre) == 1) {
                        $rowautre = mysqli_fetch_row($this->resautre);
                        $login = $rowautre[0];
                    }
                }
                // on enregistre
// DEBUG : 20150929
//echo "\$data->met_a_jour_ent(".$login.", ".$this->row['uid'].")<br />";
                $data->met_a_jour_ent($login, $this->row['uid']);
            }

            fclose($this->fic);

            // il reste encore les erreurs : 2 comptes ENT -> 1 compte Gepi, on peut nettoyer quand les 2 comptes ne sont pas des comptes parents
            $this->res = $data->doublon_2ent_1gepi();
            if (mysqli_num_rows($this->res) > 0) {
                while ($this->row2 = mysqli_fetch_array($this->res)) {
                    $data->efface_2ent_1gepi($this->row2, $this->libel_responsable);
                }
            }
            $this->res = $data->doublon_2ent_1gepi();
            if (mysqli_num_rows($this->res) > 0) {
                $class = "message_red";
                $message = "Ce compte pose problème";
                while ($this->row2 = mysqli_fetch_array($this->res)) {
                    $this->nom = $this->row2['nom'] . " " . $this->row2['prenom'];
                    $this->table[] = array('login_gepi' => $this->nom, 'login_sso' => $this->ligne['uid'], 'couleur' => $class, 'message' => $message);
                }
            }
            $this->setVarGlobal('choix_info', 'affich_result');
            if (!isset($_POST["choix"]) || ($_POST["choix"] == "ecrit")) {
                $this->ecriture = TRUE;
            } else {
                $this->ecriture = FALSE;
            }
            // On récupère tous les membres de l'ENT ayant un login Gepi
            $this->res = $data->get_gepi_ent();
            if (mysqli_num_rows($this->res) != 0) {
                // ON REUTILISE LA VARIABLE $this->ligne POUR AUTRE CHOSE
                while ($this->ligne = mysqli_fetch_array($this->res)) {
                    $tmp_code_error=$data->get_error($this->ligne['login'], $this->ligne['uid'], $this->ecriture);
                    $this->messages = $this->get_message($tmp_code_error);
                    // DEBUG : 20150930
                    //echo "\$data->get_error(".$this->ligne['login'].", ".$this->ligne['uid'].", ".$this->ecriture.")<br />";

                        //$sql="INSERT INTO tempo2_sso SET col1='".$this->ligne[6]."', col2='".$this->ligne['uid']."';";
                        //echo "$sql<br />";
                    if(($this->ecriture)&&(($tmp_code_error=="3")||($tmp_code_error=="5"))) {
                        //$sql="INSERT INTO tempo2_sso SET col1='".$this->ligne['login']."', col2='".$this->ligne['uid']."';";
                        //$sql="INSERT INTO tempo2_sso SET col1='".$this->ligne[6]."', col2='".$this->ligne['uid']."';";
                        $sql="INSERT INTO tempo2_sso SET col1='".$tab_corresp[$this->ligne['uid']]."', col2='".$this->ligne['uid']."';";
                        //echo "$sql<br />";
                        $insert=mysqli_query($GLOBALS["mysqli"], $sql);
                    }

                    if ($_POST["choix"] == "erreur" && $this->messages[0] == "message_red") {
                        $this->table[] = array('login_gepi' => $this->ligne['login'], 'login_sso' => $this->ligne['uid'], 'couleur' => $this->messages[0], 'message' => $this->messages[1]);
                    } else if ($_POST["choix"] != "erreur") {
                        $this->table[] = array('login_gepi' => $this->ligne['login'], 'login_sso' => $this->ligne['uid'], 'couleur' => $this->messages[0], 'message' => $this->messages[1]);
                    }
                }
            }
            // On récupère les membres de l'ENT sans login présents dans Gepi
            $this->class = "message_red";
            $this->res = $data->get_sans_login();
            if (mysqli_num_rows($this->res) != 0) {
                while ($this->ligne = mysqli_fetch_array($this->res)) {
                    $this->res2 = $data->cherche_login($this->ligne);
                    if (mysqli_num_rows($this->res2) > 0) {
                        $this->message = 'Il y a plusieurs personnes dans Gepi ayant les mêmes noms et prénoms';
                        $nomPrenom = $this->ligne['nom'] . " " . $this->ligne['prenom'];
                        $this->table[] = array('login_gepi' => $nomPrenom, 'login_sso' => $this->ligne['uid'], 'couleur' => $this->class, 'message' => $this->message);
                    }
                }
            }
            // On récupère les membres de l'ENT sans login absents de Gepi
            $this->res = $data->get_sans_login();
            if (mysqli_num_rows($this->res) != 0) {
                while ($this->ligne = mysqli_fetch_array($this->res)) {
                    $this->res2 = $data->cherche_login($this->ligne);
                    if (mysqli_num_rows($this->res2) == 0) {
                        $possibles = Null;
                        $probable = Null;
                        $this->res3 = $data->get_homonymes_sans_correspondance($this->ligne['nom']);
                        while ($this->ligne2 = mysqli_fetch_array($this->res3)) {
                            $possibles[] = $this->ligne2;
                        }
                        if (isset($possibles))
                            $probable = $this->get_probable($this->ligne, $possibles);
                        if ($probable) {
                            $this->message = "L'utilisateur est peut être " . $probable['nom'] . " " . $probable['prenom'] . "( " . $probable['statut'] . ")";
                            $this->class = "message_purple";
                            $nomPrenom = $this->ligne['nom'] . " " . $this->ligne['prenom'];
                            $this->table[] = array('login_gepi' => $nomPrenom, 'login_sso' => $this->ligne['uid'], 'couleur' => $this->class, 'message' => $this->message);
                        } else {
                            $this->message = "L'utilisateur n'existe probablement pas dans Gepi.";
                            $this->class = "message_red";
                            $nomPrenom = $this->ligne['nom'] . " " . $this->ligne['prenom'];
                            $this->table[] = array('login_gepi' => $nomPrenom, 'login_sso' => $this->ligne['uid'], 'couleur' => $this->class, 'message' => $this->message);
                        }
                    }
                }
            }

            // On s'assure que table a bien un enregistrement
            if (is_null($this->table)) {
                //$this->table[] = array('login_gepi' => ' ', 'login_sso' => ' ', 'couleur' => ' ', 'message' => ' ');
                if ($_POST["choix"] != "erreur")
                    $this->setVarGlobal('choix_info', 'no_data');
                if ($_POST["choix"] == "erreur")
                    $this->setVarGlobal('choix_info', 'no_error');
            }
        }
        //$data->supprime_table_import();
    }

    private function verif_file($file) {
        $this->fic = fopen($file, 'r');
        $ligne_erreur = 1;
        while (($this->ligne = fgetcsv($this->fic, 1000, ";")) !== FALSE) {
            if (sizeof($this->ligne) < 13) {
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
                $query = "SELECT `login_sso` FROM `sso_table_correspondance` WHERE `login_gepi`='" . $this->ligne['login'] . "'";
                $result = mysqli_query($GLOBALS["mysqli"], $query);
                // Vérification du résultat
                if (!$result) {
                    $message = 'Requête invalide : ' . mysqli_error($GLOBALS["mysqli"]) . "\n";
                    $message .= 'Requête complète : ' . $query;
                    die($message);
                }
                $row = mysqli_fetch_row($result);
                if ($row[0] == $this->ligne['uid']) {
                    $this->class = "message_blue";
                    $this->message = 'Une entrée identique existe déjà dans la table pour ce login Gepi';
                } else {
                    $this->class = "message_red";
                    $this->message = 'Une entrée différente existe déjà dans la table pour ce login Gepi';
                }
                break;
            case 1:
                $this->class = "message_red";
                $this->message = 'Une entrée existe déjà dans la table pour ce login sso';
                break;
            case 2:
                $this->class = "message_red";
                $this->message = 'L\'utilisateur n\'existe pas dans Gepi.';
                break;
            case 3:
                $this->class = "message_orange";
                $this->message = 'L\'utilisateur existe mais son compte n\'est pas paramétré pour le sso. Il faut corriger absolument pour que la correspondance fonctionne.';
                // DEBUG 20150929 : Enregistrer dans une table tempo2 pour permettre une génération publipostage juste pour ces comptes
                break;
            case 5:
                $this->class = "message_red";
                $this->message = 'Aucune des deux valeurs ne peut être vide. Il faut rectifier cela.';
                break;
            default:
                $this->class = "message_green";
                $this->message = 'La correspondance est mise en place.';
                // DEBUG 20150929 : Enregistrer dans une table tempo2 pour permettre une génération publipostage juste pour ces comptes
        }
        return array($this->class, $this->message);
    }

    private function get_probable($personne, $possibles) {
        foreach ($possibles as $possible) {
            $longueur_min = min(mb_strlen($personne['prenom']), mb_strlen($possible['prenom']));
            if (soundex(mb_substr($personne['prenom'], 0, $longueur_min)) == soundex(mb_substr($possible['prenom'], 0, $longueur_min))) {
                $probables[] = $possible;
            }
        }
        if (isset($probables)) {
            $diff = 255;
            foreach ($probables as $probable) {
                if (levenshtein($probable['prenom'], $personne['prenom']) <= $diff) {
                    $diff = levenshtein($probable['prenom'], $personne['prenom']);
                    $plus_probables[] = $probable;
                }
            }
            if (count($plus_probables) != 1) {
                return false;
            } else {
                return($plus_probables[0]);
            }
        } else {
            return false;
        }
    }

}

?>
