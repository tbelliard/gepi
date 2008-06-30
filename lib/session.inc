<?php
/*
 * $Id$
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

/**
 * Open a new session
 *
 * Check the provided login and password
 * Register data from the database to the session cookie
 * Log the session
 *
 * Returns true if login succeeded, false otherwise
 *
 * @_login              string                  Login of the user
 * @_password           string                  Password
 *
 * @return              bool                    The session is open
 */

function openSession($_login, $_password, $_sso_login = '', $lcs_tab_login=array())
{
    global $liste_noire_ip, $bloque_compte_admin;
    // Vérification de la liste noire des adresses IP
    $autorisation = 'y';
    if (isset($liste_noire_ip)) {
        foreach ($liste_noire_ip as $key => $value) {
           if ($_SERVER['REMOTE_ADDR'] == $value ) $autorisation = 'n';
        }
    }
    if ($autorisation == 'n') {
        return "liste_noire";
        die();
    }
    if ($_sso_login == 'cas' or $_sso_login == 'lemon' or $_sso_login == 'lcs') {
        // L'utilisateur a déjà été authentifié par CAS ou LemonLDAP. On appelle donc ses données
        // présentes dans la base de données de Gepi
        $sql = "select upper(login) login, password, prenom, nom, statut, now() start, change_mdp
                from utilisateurs
                where login = '" . $_login . "' and password = ''";

        $res_user = sql_query($sql);
        $num_row = sql_count($res_user);

        if ($num_row == 1) {  // L'utilisateur est présent dans la base locale
            $etat_utilisateur = sql_query1("select etat from utilisateurs where login = '" . $_login . "' and password = ''");
            if ($etat_utilisateur!="actif") {
                return "6";
            } else {
              if ($_sso_login == 'lcs') {
                $nom_user = $lcs_tab_login["nom"];
                $email_user = $lcs_tab_login["email"];
                $prenom_user = $lcs_tab_login["fullname"];
                // On met à jour
                $sql = "UPDATE utilisateurs SET
                nom='".$nom_user."',
                prenom='".$prenom_user."',
                email='".$email_user."'
                where login='".$_login."'";
                if (sql_command($sql) < 0) {
                    return "2";
                    die();
                }
              }

              // on récupère les données de l'utilisateur dans $row
              $row = sql_row($res_user,0);
              // Get principal matter of this user
              $sql2 = "select id_matiere from j_professeurs_matieres where id_professeur = '" . $_login . "' order by ordre_matieres limit 1";
              $matiere_princ = sql_query1($sql2);
            }
        } else { // L'utilisateur n'est pas présent dans la base locale
            // On teste si un utilisateur porte déjà le même login
            $test = mysql_num_rows(mysql_query("select login from utilisateurs where login = '".$_login."'"));
            if ($test != '0') {
                // le login existe déjà : impossible d'importer le profil.
                return "3";
                die();
            } else {
                // le login n'existe pas :  impossible de se connecter
                return "4";
                die();
            }
        }
        // Fin de la partie spécifique au SSO
    } else {
        //
        // Authentification sans SSO
        //
        $auth_ldap = "no";
        if ($_sso_login == 'ldap_scribe') {
            //
            // Authentification avec LDAP
            //
            if ($user_ldap_info = verif_ldap($_login, $_password)) {
                $auth_ldap = 'yes';
            }
        }


        // 1ère étape : le compte est-il verrouillé ?
        if ((!isset($bloque_compte_admin)) or ($bloque_compte_admin != "n"))
            $test_verrouillage = sql_query1("select login from utilisateurs where
            login = '" . strtoupper($_login) . "' and
            date_verrouillage > now() - interval " . getSettingValue("temps_compte_verrouille") . " minute ");
        else
            $test_verrouillage = sql_query1("select login from utilisateurs where
            login = '" . strtoupper($_login) . "' and
            statut != 'administrateur' and
            date_verrouillage > now() - interval " . getSettingValue("temps_compte_verrouille") . " minute ");
        if ($test_verrouillage != "-1") {
            // Le compte est verrouillé
            // Si le login et le mot de passe sont valides, on prévient l'utilisateur que son compte est temporairement verrouillé
            // Si le login ou le mot de passe ne sont pas valides, on se contente de renvoyer le message habituel "Identifiant ou mot de passe incorrect"
            // de façon à ne pas donner d'indices aux pirates.
//            $test = sql_query1("select login from utilisateurs where login = '" . strtoupper($_login) . "' and password = '" . $_password . "' and etat != 'inactif'");
//            if ($test != "-1")
                return "verrouillage";
//            else
//                return "0";
            die();
        }

        // 2ème étape
        // On récupère les données de l'utilisateur
        if ($auth_ldap != "yes") {
        	$sql = "select upper(login) login, password, prenom, nom, statut, now() start, change_mdp from utilisateurs where (login = '" . strtoupper($_login) . "' and password = '" . $_password . "' and etat != 'inactif')";
        } else {
        	// Si auth sur ldap, il n'y a pas de password !
        	$sql = "select upper(login) login, password, prenom, nom, statut, now() start, change_mdp from utilisateurs where (login = '" . strtoupper($_login) . "' and etat != 'inactif')";
        }

        $res_user = sql_query($sql);
        $num_row = sql_count($res_user);

        if ($num_row == 1 OR $auth_ldap == "yes") {
            // Le login et le mot de passe sont valides
            // on détermine la matière principale
            $sql2 = "select id_matiere from j_professeurs_matieres where id_professeur = '" . strtoupper($_login) . "' order by ordre_matieres limit 1";
            $matiere_princ = sql_query1($sql2);
        } else {
            // le login ou le mot de passe est erroné
            $test_login = sql_query1("select login from utilisateurs where login = '" . strtoupper($_login) . "'");
            if ($test_login != "-1") {
                // le login existe
                $test_mdp = sql_query1("select login from utilisateurs where login = '" . strtoupper($_login) . "' and password = '" . $_password . "'");
                if ($test_mdp == "-1") {
                    // Le mot de passe est erroné ! on enregistre le log
                    $sql = "insert into log (LOGIN, START, SESSION_ID, REMOTE_ADDR, USER_AGENT, REFERER, AUTOCLOSE, END) values (
                    '" . strtoupper($_login) . "',
                    now(),
                    '',
                    '" . $_SERVER['REMOTE_ADDR'] . "',
                    '" . $_SERVER['HTTP_USER_AGENT'] . "',
                    '" . $_SERVER['HTTP_REFERER'] . "',
                    '4',
                    now()
                    )
                    ;";
                    $res = sql_query($sql);
                    // On compte de nombre de tentatives infructueuse issues de la même adresse IP
                    $sql = "select LOGIN from log where
                    LOGIN = '" . strtoupper($_login) . "' and
                    START > now() - interval " . getSettingValue("temps_compte_verrouille") . " minute and
                    REMOTE_ADDR = '".$_SERVER['REMOTE_ADDR']."'
                    ";
                    $res_test = sql_query($sql);
                    if (sql_count($res_test) > getSettingValue("nombre_tentatives_connexion")) {
                        if ((!isset($bloque_compte_admin)) or ($bloque_compte_admin != "n")) {
                            // On verrouille le compte
                            $reg_data = sql_query("UPDATE utilisateurs SET date_verrouillage=now() WHERE login='".strtoupper($_login)."'");
                        } else {
                            // on ne bloque pas le compte d'un administrateur
                            $reg_data = sql_query("UPDATE utilisateurs SET date_verrouillage=now() WHERE login='".strtoupper($_login)."' and statut!='administrateur'");
                        }

                    }

                }
            } else {

            	// Le login n'existe pas. On compte de nombre de tentatives infructueuse
                // issues de la même adresse IP depuis temps_compte_verrouille minutes
                // Si ce nombre n'exède pas 10, on enregistre le log
                $sql = "select LOGIN from log where
                START > now() - interval " . getSettingValue("temps_compte_verrouille") . " minute and
                REMOTE_ADDR = '".$_SERVER['REMOTE_ADDR']."'
                ";
                $res_test = sql_query($sql);
                if (sql_count($res_test) <= 10) {
                    $sql = "insert into log (LOGIN, START, SESSION_ID, REMOTE_ADDR, USER_AGENT, REFERER, AUTOCLOSE, END) values (
                    '" . strtoupper($_login) . "',
                    now(),
                    '',
                    '" . $_SERVER['REMOTE_ADDR'] . "',
                    '" . $_SERVER['HTTP_USER_AGENT'] . "',
                    '" . $_SERVER['HTTP_REFERER'] . "',
                    '4',
                    now()
                    )
                    ;";
                    $res = sql_query($sql);
                } else {
                    // Pour brouiller les pistes, on renvoie le message :
                    // "Trop de tentatives de connexion infructueuses : votre compte est momentanément verrouillé."
                    return "verrouillage";
                    die();
                }

            }
            // Echec de connexion
            return "0";
        }

        $row = sql_row($res_user,0);

        if (!$row && $_sso_login == "ldap_scribe") {
            // On ne devrait arriver ici que si l'utilisateur n'a pas été importé lors de la synchro
            // AJOUTER ICI TOUT LE CODE POUR AJOUTER L'UTILISATEUR DANS LA BASE
            require "lib/eole_sync_functions.inc.php";

            if (strstr($user_ldap_info[0]["dn"],"eleves") OR strstr($user_ldap_info[0]["dn"],"personnels")) {
                // La personne qui s'authentifie fait partie du personnel de l'établissement
                // ou bien est un élève.
                // On l'enregistre dans la base.

				if (!array_key_exists("mail", $user_ldap_info[0])) {
					$user_ldap_info[0]["mail"] = array();
					$user_ldap_info[0]["mail"][0] = null;
				}

				if (!array_key_exists("codecivilite", $user_ldap_info[0])) {
					$user_ldap_info[0]["codecivilite"] = array();
					$user_ldap_info[0]["codecivilite"][0] = 1;
				}

                if ($user_ldap_info[0]["uid"][0] == "administrateur" OR $user_ldap_info[0]["uid"][0] == "admin") {
                    $u_statut = "administrateur";
                } else if (strstr($user_ldap_info[0]["dn"],"eleves")) {
                    	$u_statut = "eleve";
                } else if (strstr($user_ldap_info[0]["dn"],"personnels")){
                	$u_statut = "professeur";
                }

                $add = add_user($user_ldap_info[0]["uid"][0],
                                $user_ldap_info[0]["sn"][0],
                                $user_ldap_info[0]["givenname"][0],
                                $user_ldap_info[0]["codecivilite"][0],
                                $u_statut,
                                $user_ldap_info[0]["mail"][0]
                                );
                $sql = "select login, password, prenom, nom, statut, now() start, change_mdp from utilisateurs where login = '" . $user_ldap_info[0]["uid"][0] . "' and password = '' and etat != 'inactif'";
                $res_user = sql_query($sql);
                $row = sql_row($res_user,0);
            } elseif ($user_ldap_info[0]["uid"][0] == "administrateur") {
                //  function add_user($_login, $_nom, $_prenom, $_civilite, $_statut, $_email)
				if (!array_key_exists("mail", $user_ldap_info[0])) {
					$user_ldap_info[0]["mail"] = array();
					$user_ldap_info[0]["mail"][0] = null;
				}

				if (!array_key_exists("codecivilite", $user_ldap_info[0])) {
					$user_ldap_info[0]["codecivilite"] = array();
					$user_ldap_info[0]["codecivilite"][0] = 1;
				}
                $add = add_user($user_ldap_info[0]["uid"][0],
                                $user_ldap_info[0]["sn"][0],
                                $user_ldap_info[0]["givenname"][0],
                                $user_ldap_info[0]["codecivilite"][0],
                                "administrateur",
                                $user_ldap_info[0]["mail"][0]);

                $sql = "select login, password, prenom, nom, statut, now() start, change_mdp from utilisateurs where login = '" . $user_ldap_info[0]["uid"][0] . "' and password = '' and etat != 'inactif'";
                $res_user = sql_query($sql);
                $row = sql_row($res_user,0);
            }

        }

    }

    // Partie commune à tout type de connexion
    //if ((getSettingValue("disable_login")=='yes') and ($row[4] != "administrateur")) {
    if ((getSettingValue("disable_login")!='no') and ($row[4] != "administrateur")) {
        return "dl";
        die();
    }


    $test_change_mdp = $row[6];

    // Session starts now
    session_name("GEPI");
    session_start();

    // Is this user already connected ?
    $sql = "select SESSION_ID from log where SESSION_ID = '" . session_id() . "' and LOGIN = '" . strtoupper($_login) . "' and now() between START and END";
    $res = sql_query($sql);
    $num_row = sql_count($res);
    if (($num_row > 0) and isset($_SESSION['start'])) {
        $sql = "update log set END = now() + interval " . getSettingValue("sessionMaxLength") . " minute where SESSION_ID = '" . session_id() . "' and START = '" . $_SESSION['start'] . "'";
        $res = sql_query($sql);
        if ($test_change_mdp == 'y') return "c"; else return "1";
    } else {
        session_unset();
    }



    $_SESSION = array();
    $_SESSION['login'] = $row[0];
    $_SESSION['password'] = ($_sso_login == 'ldap') ? null : $row[1];
    $_SESSION['prenom'] = $row[2];
    $_SESSION['nom'] = $row[3];
    $_SESSION['statut'] = $row[4];
    $_SESSION['start'] = $row[5];
    $_SESSION['matiere'] = $matiere_princ;
    $_SESSION['maxLength'] = getSettingValue("sessionMaxLength");
    $_SESSION['rne'] = 'RNE';

    // Ajout pour les statuts privés
    if ($_SESSION['statut'] == 'autre') {

    	// On charge aussi le statut spécial
    	$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
										WHERE du.login_user = '".$_SESSION['login']."'
										AND du.id_statut = ds.id";
		$query = mysql_query($sql);
		$result = mysql_fetch_array($query);

		$_SESSION["special"] = $result["nom_statut"];
		$_SESSION["special_id"] = $result["id"];

    }

    // It's a new connection, insert into log
    $sql = "insert into log (LOGIN, START, SESSION_ID, REMOTE_ADDR, USER_AGENT, REFERER, AUTOCLOSE, END) values (
                '" . $_SESSION['login'] . "',
                '" . $_SESSION['start'] . "',
                '" . session_id() . "',
                '" . $_SERVER['REMOTE_ADDR'] . "',
                '" . $_SERVER['HTTP_USER_AGENT'] . "',
                '" . $_SERVER['HTTP_REFERER'] . "',
                '1',
                '" . $_SESSION['start'] . "' + interval " . getSettingValue("sessionMaxLength") . " minute
            )
        ;";
    $res = sql_query($sql);
    if ($test_change_mdp == 'y' && ($_sso_login != 'lemon' && $_sso_login != 'cas' && $_sso_login != 'ldap_scribe')) return "c"; else return "1";
}

/**
 * Resume a session
 *
 * Check that all the expected data is present
 * Check login / password against database
 * Update the timeout in the log table
 *
 * Returns true if session resumes, false otherwise
 *
 *
 * @return              bool                    The session resumed
 */
function resumeSession()
{
    global $use_sso;

		// Resuming session
    	session_name("GEPI");
    	session_start();

    if ((!isset($_SESSION)) or (!isset($_SESSION['login']))){
        tentative_intrusion(1, "Tentative d'accès à une page sans être logué (peut provenir d'un timeout de session).");
        return "0";
        die();
    }

    if ((getSettingValue("disable_login")=='yes') and ($_SESSION['statut'] != "administrateur")) {
        return "0";
        die();
    }


    // Validating session data
    $sql = "select password = '" . $_SESSION['password'] . "' PASSWORD, login = '" . $_SESSION['login'] . "' LOGIN, statut = '" . $_SESSION['statut'] . "' STATUT, change_mdp = 'n' CHANGE_MDP, etat = 'actif' ETAT from utilisateurs where login = '" . $_SESSION['login'] . "'";

    $res = sql_query($sql);
    $row = sql_row($res, 0);

    // Checking for a timeout
    $sql2 = "select now() > END TIMEOUT from log where SESSION_ID = '" . session_id() . "' and START = '" . $_SESSION['start'] . "'";

    if ($row[0] != "1" || $row[1] != "1" || $row[2] != "1" || $row[4] != "1") {
        return "0";
    } else if (sql_query1($sql2)) {
        return "0";
    } else if ($row[3] != "1" && !$use_sso) {
        return "c";
    } else {
        $sql = "update log set END = now() + interval " . $_SESSION['maxLength'] . " minute where SESSION_ID = '" . session_id() . "' and START = '" . $_SESSION['start'] . "'";
        $res = sql_query($sql);
        return "1";
    }
}

/**
 * Close a session
 *
 * Set the closing time in the logs
 * Destroy all session data
 * @_auto               string                  Session auto-close flag
 * @return              nothing
 */
function closeSession(&$_auto)
{
    session_name("GEPI");
    @session_start();
    // Sometimes 'start' may not exist, because the session was previously closed by another window
    // It's not necessary to log this, then
    if (isset($_SESSION['start'])) {
            $sql = "update log set AUTOCLOSE = '" . $_auto . "', END = now() where SESSION_ID = '" . session_id() . "' and START = '" . $_SESSION['start'] . "'";
        $res = sql_query($sql);
    }
    // Détruit toutes les variables de session
    session_unset();
    $_SESSION = array();

    // Détruit le cookie sur le navigateur
    $CookieInfo = session_get_cookie_params();
    @setcookie(session_name(), '', time()-3600, $CookieInfo['path']);

    /*
    // Pour une gestion plus fine de la suppression du cookie.
    $CookieInfo = session_get_cookie_params();
    if ( (empty($CookieInfo['domain'])) && (empty($CookieInfo['secure'])) ) {
       setcookie(session_name(), '', time()-3600, $CookieInfo['path']);
    } elseif (empty($CookieInfo['secure'])) {
       setcookie(session_name(), '', time()-3600, $CookieInfo['path'], $CookieInfo['domain']);
    } else {
       setcookie(session_name(), '', time()-3600, $CookieInfo['path'], $CookieInfo['domain'], $CookieInfo['secure']);
    }
    */


    // détruit la session sur le serveur
    session_destroy();
}

function verif_ldap($_login, $_password) {
    if ($_password == '') {
        return false;
        exit();
    }
    include "secure/config_ldap.inc.php";
    $ds = connect_ldap($ldap_adresse,$ldap_port,$ldap_login,$ldap_pwd);
    if ($ds) {
        $login_search = ereg_replace("[^-@._[:space:][:alnum:]]", "", $_login); // securite
        $sr = ldap_search($ds,$ldap_base,"(uid=" . $login_search . ")");
        $info = array();
        $info = ldap_get_entries($ds,$sr);
        if (array_key_exists(0, $info)) {
            // tentative de bind
            if (!(($info[0]["dn"]) and $_password and (@ldap_bind($ds,$info[0]["dn"],$_password)==1))) {
                return false;
            } else {
                return $info;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function connect_ldap($l_adresse,$l_port,$l_login,$l_pwd) {
    $ds = @ldap_connect($l_adresse, $l_port);
    if($ds) {
       // On dit qu'on utilise LDAP V3, sinon la V2 par d?faut est utilis? et le bind ne passe pas.
       $norme = @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
       // Acc?s non anonyme
       if ($l_login != '') {
          // On tente un bind
          $b = @ldap_bind($ds, $l_login, $l_pwd);
       } else {
          // Acc?s anonyme
          $b = @ldap_bind($ds);
       }
       if ($b) {
           return $ds;
       } else {
           return false;
       }
    } else {
       return false;
    }
}
?>