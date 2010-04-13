<?php
/*
 * $Id: Session.class.php 2315 2008-08-24 19:58:20Z tbelliard $
 *
 * Copyright 2001, 2008 Thomas Belliard
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


# Cette classe sert à manipuler la session en cours.
# Elle gère notamment l'authentification des utilisateurs
# à partir de différentes sources.

class Session {

        # Attributs publics
        var $login = false;
	var $nom = false;
	var $prenom = false;
	var $statut = false;
	var $statut_special = false;
	var $statut_special_id = false;
	var $start = false;
	var $matiere = false;
	var $maxLength = "30"; # Durée par défaut d'une session utilisateur : 30 minutes.
	var $rne = false; # false, n° RNE valide. Utilisé par le multisite.
	var $auth_locale = true; # true, false. Par défaut, on utilise l'authentification locale
	var $auth_ldap = false; # false, true
	var $auth_sso = false; # false, cas, lemon, lcs
	var $current_auth_mode = false;  # gepi, ldap, sso, ou false : le mode d'authentification
					 # utilisé par l'utilisateur actuellement connecté

	# Attributs privés
	var $etat = false; 	# actif/inactif. Utilisé simplement en interne pour vérifier que
							# l'utilisateur authentifié de source externe est bien actif dans Gepi.

	function Session() {

		# On initialise la session
		session_name("GEPI");
		session_start();
		  
		$this->maxLength = getSettingValue("sessionMaxLength");
		# On charge les valeurs déjà présentes en session
		$this->load_session_data();

		# On charge des éléments de configuration liés à l'authentification
		$this->auth_locale = getSettingValue("auth_locale") == 'yes' ? true : false;
		$this->auth_ldap = getSettingValue("auth_ldap") == 'yes' ? true : false;
		$this->auth_sso = in_array(getSettingValue("auth_sso"), array("lemon", "cas", "lcs")) ? getSettingValue("auth_sso") : false;

		if (!$this->is_anonymous()) {
		  # Il s'agit d'une session non anonyme qui existait déjà.
		  # On regarde s'il n'y a pas de timeout
		  if ($this->timeout()) {
		  	# timeout : on remet à zéro.
		  	$debut_session = $_SESSION['start'];
		  	$this->reset(3);
		  	if (isset($GLOBALS['niveau_arbo'])) {
		  		if ($GLOBALS['niveau_arbo'] == "0") {
		  			$logout_path = "./logout.php";
		  		} elseif ($GLOBALS['niveau_arbo'] == "2") {
		  			$logout_path = "../../logout.php";
		  		} elseif ($GLOBALS['niveau_arbo'] == "3") {
		  			$logout_path = "../../../logout.php";
		  		} else {
		  			$logout_path = "../logout.php";
		  		}
		  	} else {
		  		$logout_path = "../logout.php";
		  	}
		  	//echo $logout_path;die();
		  	header("Location:".$logout_path."?auto=3&debut_session=".$debut_session."&session_id=".session_id());
		  	exit();
		  } else {
		  	# Pas de timeout : on met à jour le log
		  	$this->update_log();
		  }
		}

	}

	# S'agit-il d'une session anonyme ?
	function is_anonymous() {
		# Retourne 'true' si login == false
		return !$this->login;
	}

	# Authentification d'un utilisateur pour la session
	# Cette méthode remplace l'ancienne fonction openSession(...)
	# Valeurs retournées :
	# 1 : authentification valide
	# 2 : compte bloqué : trop de tentatives erronées
	# 3 : l'IP utilisée pour se connecter est bloquée
	# 4	: authentification externe réussie, mais utilisateurs défini comme 'inactif'
	# 5 : authentification externe réussie, mais utilisateur défini pour une authentification autre
	# 6 : authentification externe réussie, mais compte inexistant en local et impossible d'importer depuis une source externe
	# 7 : l'administrateur a désactivé les connexions à Gepi.
	# 8 : multisite ; impossibilité d'obtenir le RNE de l'utilisateur qui s'est authentifié correctement.
	# 9 : échec de l'authentification (mauvais couple login/mot de passe, sans doute).
	function authenticate($_login = null, $_password = null) {

		// Quelques petits tests de sécurité

	    // Vérification de la liste noire des adresses IP
	    if (isset($GLOBALS['liste_noire_ip']) && in_array($_SERVER['REMOTE_ADDR'], $GLOBALS['liste_noire_ip'])) {
		  tentative_intrusion(1, "Tentative de connexion depuis une IP sur liste noire (login utilisé : ".$_login.")");
	      return "3";
		  die();
	    }

	    // On initialise la session de l'utilisateur.
	    // On commence par extraire le mode d'authentification défini
	    // pour l'utilisateur. Si l'utilisateur n'existe pas, on essaiera
	    // l'authentification LDAP et le SSO quand même.
		$auth_mode = Session::user_auth_mode($_login);

		switch ($auth_mode) {
			case "gepi":
			  # Authentification locale sur la base de données Gepi
			  $auth = $this->authenticate_gepi($_login,$_password);
			break;
			case "ldap":
			  # Authentification sur un serveur LDAP
			  $auth = $this->authenticate_ldap($_login,$_password);
			break;
			case "sso":
			  # Authentification gérée par un service de SSO
			  # On n'a pas besoin du login ni du mot de passe
			  switch ($this->auth_sso) {
			  	case "cas":
			  		$auth = $this->authenticate_cas();
			  	break;
			  	case "lemon":
			  		$auth = $this->authenticate_lemon();
			  	break;
			  	case "lcs":
			  		$auth = $this->authenticate_lcs();
			  	break;
			  }
			break;
			case false:
			  # L'utilisateur n'existe pas dans la base de données ou bien
			  # n'a pas été passé en paramètre.
			  # On va donc tenter d'abord une authentification LDAP,
			  # puis une authentification SSO, à condition que celles-ci
			  # soient bien sûr configurées.
			  if ($this->auth_ldap && $_login != null && $_password != null) {
			  	$auth = $this->authenticate_ldap($_login,$_password);
			  } else if ($this->auth_sso && $_login == null) {
			  	// L'auth LDAP n'a pas marché, on essaie le SSO
				 switch ($this->auth_sso) {
				  	case "cas":
				  		$auth = $this->authenticate_cas();
				  	break;
				  	case "lemon":
				  		$auth = $this->authenticate_lemon();
				  	break;
				  	case "lcs":
				  		$auth = $this->authenticate_lcs();
				  	break;
				 }
			  } else {
			  	$auth = false;
			  }
			break;
			default:
			  # Si on arrive là, c'est qu'il y a un problème avec la définition
			  # du mode d'authentification pour l'utilisateur en question.
			  $auth = false;
			break;
		}

		// A partir d'ici soit on a un avis d'échec de l'authentification, soit
		// une session valide.
		if ($auth) {
			// L'authentification en elle-même est valide.

			// Dans le cas du multisite, il faut maintenant déterminer le RNE
			// de l'utilisateur avant d'aller plus loin, sauf s'il a déjà été passé
			// en paramètre.
			if (isset($GLOBALS['multisite']) && $GLOBALS['multisite'] == "y") {
				if (!isset($_GET['rne']) AND !isset($_COOKIE["RNE"])) {
					if (LDAPServer::is_setup()) {
						// Le RNE n'a pas été transmis. Il faut le récupérer et recharger la page
						// pour obtenir la bonne base de données
						$ldap = new LDAPServer;
						$user = $ldap->get_user_profile($this->login);
						// On teste pour savoir si on a plusieurs RNE
						$test = count($user["rne"]);

						if ($test >= 1) {
							# On a au moins un RNE, on peut continuer
							if ($test > 1) {
								// On envoie l'utilisateur choisir lui même son RNE
								$rnes = NULL;
								for($a = 0 ; $a < $test ; $a++){
									$rnes .= $user["rne"][$a].'|';
								}

								header("Location: choix_rne.php?nbre=".$test."&lesrne=".$rnes);

							}else{
								// Il n'y en a qu'un, on recharge !
								if ($this->current_auth_mode == "sso") {
									header("Location: login_sso.php?rne=".$user["rne"]);
									exit();
								} else {
									header("Location: login.php?rne=".$user["rne"]);
									exit();
								}
							}

						} else {
							return "8";
							exit();
						}
					} else {
						return "8";
						exit();
					}
				}
			}


			// On va maintenant effectuer quelques tests pour vérifier
			// que le compte n'est pas bloqué.
			if ($this->account_is_locked()) {
				$this->reset(2);
				return "2";
				exit();
			}

			# On charge les données de l'utilisateur
			if (!$this->load_user_data()) {
				# Si on ne parvient pas à charger les données, c'est que
				# l'utilisateur n'est pas présent en base de données.
				# On essaie d'importer son profil depuis le LDAP.
				if (getSettingValue("may_import_user_profile") == "yes") {
					if (!$this->import_user_profile()) {
						return "6";
						exit();
					} else {
						# Si l'import a réussi, on tente à nouveau de charger
						# les données de l'utilisateur.
						$this->load_user_data();
					}
				}
			}

			# On vérifie que l'utilisateur est bien actif
			if ($this->etat != "actif") {
				$this->reset(2);
				return "4";
				exit();
			}

			# On vérifie que les connexions sont bien activées.
		    $disable_login = getSettingValue("disable_login");
		    if ($this->statut != "administrateur" && ($disable_login == "yes" || $disable_login == "soft")) {
		    	$this->reset(2);
		    	return "7";
		    	exit();
		    }

			# On teste la cohérence de mode de connexion
		    $auth_mode = Session::user_auth_mode($this->login);
		    if ($auth_mode != $this->current_auth_mode) {
		    	$this->reset(2);
		    	return "5";
		    	exit;
		    }

			# Tout est bon. On valide définitivement la session.
			$this->start = mysql_result(mysql_query("SELECT now();"),0);
			$_SESSION['start'] = $this->start;
			$this->insert_log();
			# On supprime l'historique des logs conformément à la durée définie.
			sql_query("delete from log where START < now() - interval " . getSettingValue("duree_conservation_logs") . " day and END < now()");

			# On envoie un mail, si l'option a été activée
			mail_connexion();
			return "1";
			exit();
		} else {
			// L'authentification a échoué.
			// On nettoie la session.
			$this->reset(2);

			// On enregistre l'échec.
			// En cas d'échec répété, on renvoie un code d'erreur de
			// verrouillage de compte, pour brouiller les pistes en cas
			// d'attaque brute-force sur les logins.
			if ($this->record_failed_login($_login)) {
				return "2";
				exit();
			}

			// On retourne le code d'erreur générique
			return "9";
		}

	}

	# La méthode ci-dessous est appelée lorsque l'on veut s'assurer que l'on a
	# un utilisateur correctement authentifié, et qu'il est bien autorisé à
	# l'être. Elle remplace la fonction resumeSession qui était préalablement utilisée.
	function security_check() {
		# Codes renvoyés :
		# 0 = logout automatique
		# 1 = session valide
		# c = changement forcé de mot de passe

		# D'abord on regarde si on a une tentative d'accès anonyme à une page protégée :
		if ($this->is_anonymous()) {
			tentative_intrusion(1, "Accès à une page sans être logué (peut provenir d'un timeout de session).");
			return "0";
			exit;
		}

		$sql = "SELECT statut, change_mdp, etat FROM utilisateurs where login = '" . $this->login . "'";
		$res = sql_query($sql);
		$row = mysql_fetch_object($res);

		$change_password = $row->change_mdp != "n" ? true : false;
		$statut_ok = $this->statut == $row->statut ? true : false;
		$etat_ok = $row->etat == "actif" ? true : false;
		$login_allowed = getSettingValue("disable_login") == "yes" ? false : true;

		if (!$login_allowed && $this->statut != "administrateur") {
			return "0";
			exit;
		}

		if (!$statut_ok) {
			return "0";
			exit;
		}

		if (!$etat_ok) {
			return "0";
			exit;
		}

		// Si on est là, ce que l'utilisateur a le droit de rester.

		if ($change_password &&
				($this->current_auth_mode == "gepi" || getSettingValue("ldap_write_access") == "yes"))
			{
				return "c";
			}

		# Mieux vaut deux fois qu'une...
		if ($statut_ok && $etat_ok && ($login_allowed || $this->statut == "administrateur")) {
			return "1";
			exit;
		}
	}

	# On regarde si l'utilisateur existe dans la base de données,
	# et on vérifie quel est le mode d'authentification défini.
	function user_auth_mode($_login) {
		if ($_login == null) {
			return false;
			die();
		}

		$req = mysql_query("SELECT auth_mode FROM utilisateurs WHERE UPPER(login) = '".strtoupper($_login)."'");
		if (mysql_num_rows($req) == 0) {
			return false;
		} else {
			return mysql_result($req, 0, "auth_mode");
		}
	}

	function close ($_auto) {
		// $_auto_ reprend les codes de reset()
		$this->reset($_auto);
	}

	
	// Recréer le log dans la table logs.
	// ATTENTION ! Cette méthode n'est utile que dans un cas très particulier :
	// la restauration d'une sauvegarde, qui compromet la session en cours de
	// l'administrateur. Elle ne devrait jamais être utilisée dans un autre
	// cas.
	// A noter : la méthode ne réinitialise pas la session. Elle ne fait que
	// réenregistrer la session en cours dans la base de données.
	function recreate_log() {
	   // On teste que le login enregistré en session existe bien dans la table
	   // des utilisateurs. Ceci est pour vérifier que cette opération de
	   // réécriture du log est bien nécessaire, et valide !
	   if ($this->login == '') {
	      return false;
           } else {
	      $test = mysql_num_rows(mysql_query("SELECT login FROM utilisateurs WHERE login = '".$this->login."'"));
	      if ($test == 0) {
		  return false;
	       } else {
		  return $this->insert_log();
	      }
	   }
        }

	## METHODE PRIVEES ##

	// Création d'une entrée de log
	function insert_log() {
		if (!isset($_SERVER['HTTP_REFERRER'])) $_SERVER['HTTP_REFERER'] = '';
	    $sql = "INSERT INTO log (LOGIN, START, SESSION_ID, REMOTE_ADDR, USER_AGENT, REFERER, AUTOCLOSE, END) values (
	                '" . $this->login . "',
	                '" . $this->start . "',
	                '" . session_id() . "',
	                '" . $_SERVER['REMOTE_ADDR'] . "',
	                '" . $_SERVER['HTTP_USER_AGENT'] . "',
	                '" . $_SERVER['HTTP_REFERER'] . "',
	                '1',
	                '" . $this->start . "' + interval " . $this->maxLength . " minute
	            )
	        ;";
	    $res = sql_query($sql);
	}

	// Mise à jour du log de l'utilisateur
	function update_log() {
		if ($this->is_anonymous()) {
			return false;
		} else {
			$sql = "UPDATE log SET END = now() + interval " . $this->maxLength . " minute where SESSION_ID = '" . session_id() . "' and START = '" . $this->start . "'";
        	$res = sql_query($sql);
		}
	}

	// Test pour voir si la session de l'utilisateur est en timeout
	function timeout() {
    	$sql = "SELECT now() > END TIMEOUT from log where SESSION_ID = '" . session_id() . "' and START = '" . $this->start . "'";
    	return sql_query1($sql);
	}

	// Remise à zéro de la session : on supprime toutes les informations présentes
	function reset($_auto = "0") {
		# Codes utilisés pour $_auto :
		# 0 : logout normal
		# 2 : logout renvoyé par la fonction checkAccess (problème gepiPath ou accès interdit)
		# 3 : logout lié à un timeout

	    # On teste 'start' simplement pour simplement vérifier que la session n'a pas encore été fermée.
		if ($this->start) {
	      $sql = "UPDATE log SET AUTOCLOSE = '" . $_auto . "', END = now() where SESSION_ID = '" . session_id() . "' and START = '" . $this->start . "'";
          $res = sql_query($sql);
    	}

    	// Détruit toutes les variables de session
	    session_unset();
	    $_SESSION = array();

	    // Détruit le cookie sur le navigateur
	    $CookieInfo = session_get_cookie_params();
	    @setcookie(session_name(), '', time()-3600, $CookieInfo['path']);

	    // détruit la session sur le serveur
	    session_destroy();
	}

	function load_session_data() {
		# On ne met à jour que si la variable de session est assignée.
		# Si elle est assignée et null, on met 'false'.
		if (isset($_SESSION['login'])) {
			$this->login 	= $_SESSION['login'] != null ? $_SESSION["login"] : false;
		}
		if (isset($_SESSION['nom'])) {
			$this->nom 	= $_SESSION['nom'] != null ? $_SESSION["nom"] : false;
		}
		if (isset($_SESSION['prenom'])) {
			$this->prenom 	= $_SESSION['prenom'] != null ? $_SESSION["prenom"] : false;
		}
		if (isset($_SESSION['statut'])) {
			$this->statut 	= $_SESSION['statut'] != null ? $_SESSION["statut"] : false;
		}
		if (isset($_SESSION['start'])) {
			$this->start 	= $_SESSION['start'] != null ? $_SESSION["start"] : false;
		}
		if (isset($_SESSION['matiere'])) {
			$this->matiere 	= $_SESSION['matiere'] != null ? $_SESSION["matiere"] : false;
		}
		if (isset($_SESSION['rne'])) {
			$this->rne 	= $_SESSION['rne'] != null ? $_SESSION["rne"] : false;
		}
		if (isset($_SESSION['statut_special'])) {
			$this->statut_special 	= $_SESSION['statut_special'] != null ? $_SESSION["statut_special"] : false;
		}
		if (isset($_SESSION['statut_special_id'])) {
			$this->statut_special_id 	= $_SESSION['statut_special_id'] != null ? $_SESSION["statut_special_id"] : false;
		}
		if (isset($_SESSION['maxLength'])) {
			$this->maxLength 	= $_SESSION['maxLength'] != null ? $_SESSION["maxLength"] : false;
		}
		if (isset($_SESSION['current_auth_mode'])) {
			$this->current_auth_mode 	= $_SESSION['current_auth_mode'] != null ? $_SESSION["current_auth_mode"] : false;
		}
	}

	# Cette fonction permet de tester sous quelle forme le login est stocké dans la base
	# de données. Elle renvoie true ou false.
	function use_uppercase_login($_login) {
		// On détermine si l'utilisateur a un login en majuscule ou minuscule
		$test_uppercase = "SELECT login FROM utilisateurs WHERE (login = '" . strtoupper($_login) . "')";
		if (sql_count(sql_query($test_uppercase)) == "1") {
			return true;
		} else {
			# On a false soit si l'utilisateur n'est pas présent dans la base, soit s'il est
			# en minuscule.
			return false;
		}
	}

	function authenticate_gepi($_login,$_password) {
		if ($this->use_uppercase_login($_login)) {
			# On passe le login en majuscule pour toute la session.
			$_login = strtoupper($_login);
		}
		$sql = "SELECT login, password FROM utilisateurs WHERE (login = '" . $_login . "' and etat != 'inactif')";
		$query = mysql_query($sql);
		if (mysql_num_rows($query) == "1") {
			# Un compte existe avec ce login
			if (mysql_result($query, 0, "password") == md5($_password)) {
				# Le mot de passe correspond. C'est bon !
				$this->login = $_login;
				$this->current_auth_mode = "gepi";
				return true;
			} else {
				return false;
			}
		} else {
			# Le login est erroné (n'existe pas dans la base)
			return false;
		}
	}

	function authenticate_ldap($_login,$_password) {
		if ($_login == null || $_password == null) {
	        return false;
	        exit();
	    }
	    $ldap_server = new LDAPServer;
	    if ($ldap_server->authenticate_user($_login,$_password)) {
	    	$this->login = $_login;
	    	$this->current_auth_mode = "ldap";
	    	return true;
	    } else {
	    	return false;
	    }
	}

	function authenticate_cas() {
		include_once('CAS.php');
		if ($GLOBALS['mode_debug']) {
		    phpCAS::setDebug($GLOBALS['debug_log_file']);
                }
		// config_cas.inc.php est le fichier d'informations de connexions au serveur cas
		$path = dirname(__FILE__)."/../secure/config_cas.inc.php";
		include($path);

		// Le premier argument est la version du protocole CAS
		phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_root, true);
		phpCAS::setLang('french');

		// redirige vers le serveur d'authentification si aucun utilisateur authentifié n'a
		// été trouvé par le client CAS.
		phpCAS::setNoCasServerValidation();
		
		// Gestion du single sign-out
		phpCAS::handleLogoutRequests(false);
		
		// Authentification
		phpCAS::forceAuthentication();

		$this->login = phpCAS::getUser();

		// On réinitialise la session
		session_name("GEPI");
		session_start();
		$_SESSION['login'] = $this->login;

		$this->current_auth_mode = "sso";

		return true;
	}

	function logout_cas() {
		include_once('CAS.php');

		// config_cas.inc.php est le fichier d'informations de connexions au serveur cas
		$path = dirname(__FILE__)."/../secure/config_cas.inc.php";
		include($path);

		// Le premier argument est la version du protocole CAS
		phpCAS::client(CAS_VERSION_2_0,$cas_host,$cas_port,$cas_root,'');
		phpCAS::setLang('french');
		phpCAS::logout();
		// redirige vers le serveur d'authentification si aucun utilisateur authentifié n'a
		// été trouvé par le client CAS.
		//phpCAS::setNoCasServerValidation();
		//phpCAS::forceAuthentication();

		//$this->login = phpCAS::getUser();

		// On réinitialise la session
		//session_name("GEPI");
		//session_start();
		//$_SESSION['login'] = $this->login;

		//$this->current_auth_mode = "sso";

		return true;
	}

	function authenticate_lemon() {
		#TODO: Vérifier que ça marche bien comme ça !!
	  if (isset($_GET['login'])) $login = $_GET['login']; else $login = "";
	  if (isset($_COOKIE['user'])) $cookie_user = $_COOKIE['user']; else $cookie_user="";
	  if(empty($cookie_user) or $cookie_user != $login) {
	  	return false;
	  } else {
		$this->login = $login;
		$this->current_auth_mode = "sso";
	  	return true;
	  }
	}

	function authenticate_lcs() {
		/*
		global $DBAUTH,$HTTP_COOKIE_VARS,$authlink,$dbHost,$dbUser,$dbPass,$db_nopersist,$dbDb;
		include LCS_PAGE_AUTH_INC_PHP;
		include LCS_PAGE_LDAP_INC_PHP;
		list ($idpers,$login) = isauth();
		*/
		global $login, $idpers;

		$DBAUTH = $GLOBALS['DBAUTH'];
		$HTTP_COOKIE_VARS = $GLOBALS['HTTP_COOKIE_VARS'];
		$authlink = $GLOBALS['authlink'];
		$dbHost = $GLOBALS['dbHost'];
		$dbUser = $GLOBALS['dbUser'];
		$dbPass = $GLOBALS['dbPass'];
		$db_nopersist = $GLOBALS['db_nopersist'];
		$dbDb = $GLOBALS['dbDb'];

		if ($idpers) {
			list($user, $groups)=people_get_variables($login, false);
			#TODO: Utiliser les infos des lignes ci-dessous pour mettre à jour
			# les informations de l'utilisateur dans la base.
			$lcs_tab_login["nom"] = $user["nom"];
			$lcs_tab_login["email"] = $user["email"];
			$long = strlen($user["fullname"]) - strlen($user["nom"]);
			$lcs_tab_login["fullname"] = substr($user["fullname"], 0, $long) ;

			// A ce stade, l'utilisateur est authentifié
			// Etablir à nouveau la connexion à la base
      if (empty($db_nopersist))
				$db_c = mysql_pconnect($dbHost, $dbUser, $dbPass);
			else
				$db_c = mysql_connect($dbHost, $dbUser, $dbPass);

			if (!$db_c || !mysql_select_db ($dbDb)) {
				echo "\n<p>Erreur : Echec de la connexion à la base de données";
				exit;
			}
			$this->login = $login;
			$this->current_auth_mode = "sso";
			return true;
			exit;
		} else {
			// L'utilisateur n'a pas été identifié'
			header("Location:".LCS_PAGE_AUTHENTIF);
			exit;
		}
	}

	# Cette méthode charge en session les données de l'utilisateur,
	# à la suite d'une authentification réussie.
	function load_user_data() {
		# Petit test de départ pour être sûr :
		if (!$this->login || $this->login == null) {
			return false;
			exit();
		}

		# Gestion du multisite : on a besoin du RNE de l'utilisateur.
		if (isset($GLOBALS['multisite']) && $GLOBALS['multisite'] == 'y' && LDAPServer::is_setup()) {
			$ldap = new LDAServer;
			$user = $ldap->get_user_profile($this->login);
			$this->rne = $user["rne"];
		}

		# On regarde si on doit utiliser un login en majuscule. Si c'est le cas, il faut impérativement
		# le faire *après* un éventuel import externe.
		if ($this->use_uppercase_login($this->login)) {
			$this->login = strtoupper($this->login);
		}

		# On interroge la base de données
		$query = mysql_query("SELECT nom, prenom, statut, etat, now() start, change_mdp, auth_mode FROM utilisateurs WHERE (login = '".$this->login."')");

		# Est-ce qu'on a bien une entrée ?
		if (mysql_num_rows($query) != "1") {
			return false;
			exit();
		}

		$sql = "SELECT id_matiere FROM j_professeurs_matieres WHERE (id_professeur = '" . $this->login . "') ORDER BY ordre_matieres LIMIT 1";
        $matiere_principale = sql_query1($sql);

		$row = mysql_fetch_object($query);

	    $_SESSION['login'] = $this->login;
	    $_SESSION['prenom'] = $row->prenom;
	    $_SESSION['nom'] = $row->nom;
	    $_SESSION['statut'] = $row->statut;
	    $_SESSION['start'] = $row->start;
	    $_SESSION['matiere'] = $matiere_principale;
	    $_SESSION['rne'] = $this->rne;
	    $_SESSION['current_auth_mode'] = $this->current_auth_mode;

	    # L'état de l'utilisateur n'est pas stocké en session, mais seulement en interne
	    # pour pouvoir effectuer quelques tests :
	    $this->etat = $row->etat;

		// Ajout pour les statuts privés
	    if ($_SESSION['statut'] == 'autre') {

	    	// On charge aussi le statut spécial
	    	$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
											WHERE du.login_user = '".$this->login."'
											AND du.id_statut = ds.id";
			$query = mysql_query($sql);
			$result = mysql_fetch_array($query);

			$_SESSION['statut_special'] = $result['nom_statut'];
			$_SESSION['statut_special_id'] = $result['id'];

	    }

		$length = rand(35, 45);
		for($len=$length,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
		$_SESSION["gepi_alea"] = $r;

	    # On charge les données dans l'instance de Session.
	    $this->load_session_data();
	    return true;
	}

	function record_failed_login($_login) {
		# Une tentative de login avec un mot de passe erronnée a été détectée.
		$test_login = sql_count(sql_query("SELECT login FROM utilisateurs WHERE (login = '".$_login."')"));

		if ($test_login != "0") {
			tentative_intrusion(1, "Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n'est significative qu'en cas de répétition. (login : ".$_login.")");
			# On a un vrai login.
			# On enregistre un log d'erreur de connexion.
	        $sql = "insert into log (LOGIN, START, SESSION_ID, REMOTE_ADDR, USER_AGENT, REFERER, AUTOCLOSE, END) values (
	        	'" . $_login . "',
	            now(),
	            '',
	            '" . $_SERVER['REMOTE_ADDR'] . "',
	            '" . $_SERVER['HTTP_USER_AGENT'] . "',
	            '" . $_SERVER['HTTP_REFERER'] . "',
	            '4',
	            now());";
	        $res = sql_query($sql);

	        // On compte de nombre de tentatives infructueuse issues de la même adresse IP
	        $sql = "select LOGIN from log where
	                LOGIN = '" . $_login . "' and
	                START > now() - interval " . getSettingValue("temps_compte_verrouille") . " minute and
	                REMOTE_ADDR = '".$_SERVER['REMOTE_ADDR']."'
	                ";
	        $res_test = sql_query($sql);
	        if (sql_count($res_test) > getSettingValue("nombre_tentatives_connexion")) {
	        	$this->lock_account($_login);
	        	return true;
	        } else {
	        	return false;
	        }
		} else {
			tentative_intrusion(1, "Tentative de connexion avec un login incorrect (n'existe pas dans la base Gepi). Ce peut être simplement une faute de frappe. Cette alerte n'est significative qu'en cas de répétition. (login utilisé : ".$_login.")");
			// Le login n'existe pas. On fait donc un test sur l'IP.
			$sql = "select LOGIN from log where
                START > now() - interval " . getSettingValue("temps_compte_verrouille") . " minute and
                REMOTE_ADDR = '".$_SERVER['REMOTE_ADDR']."'";
            $res_test = sql_query($sql);
            if (sql_count($res_test) <= 10) {
				// On a moins de 10 enregistrements. On enregistre et on ne renvoie pas de code
				// de verrouillage.
            	$sql = "insert into log (LOGIN, START, SESSION_ID, REMOTE_ADDR, USER_AGENT, REFERER, AUTOCLOSE, END) values (
                    '" . $_login . "',
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
                return false;
            } else {
            	// On a 10 entrées, on renvoie un code d'erreur de verouillage.
            	return true;
            }
		}
	}

	# Verrouillage d'un compte en raison d'un trop grand nombre d'échec de connexion.
	function lock_account($_login) {
	   if ((!isset($GLOBALS['bloque_compte_admin'])) or ($GLOBALS['bloque_compte_admin'] != "n")) {
	      // On verrouille le compte même si c'est un admin
	      $reg_data = sql_query("UPDATE utilisateurs SET date_verrouillage=now() WHERE login='".$_login."'");
	   } else {
	      // on ne bloque pas le compte d'un administrateur
	      $reg_data = sql_query("UPDATE utilisateurs SET date_verrouillage=now() WHERE login='".$_login."' and statut!='administrateur'");
	   }
	   # On enregistre une alerte de sécurité.
	   tentative_intrusion(2, "Verrouillage du compte ".$_login." en raison d'un trop grand nombre de tentatives de connexion infructueuses. Ce peut être une tentative d'attaque brute-force.");
	   return true;
	}

	# Renvoie true ou false selon que le compte est bloqué ou non.
	function account_is_locked() {
		$test_verrouillage = sql_query1("select login, statut from utilisateurs where
			login = '" . $this->login . "' and
			date_verrouillage > now() - interval " . getSettingValue("temps_compte_verrouille") . " minute ");

		if ($test_verrouillage != "-1") {
			// Le compte est verrouillé.
			if ($this->statut == "administrateur" and $GLOBALS['bloque_compte_admin'] != "n") {
				// On ne veut pas bloquer le compte admin, alors on renvoie false.
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	function import_user_profile() {
		# On ne peut arriver ici quand dans le cas où on a une authentification réussie.
		# L'import d'un utilisateur ne peut se faire qu'à partir d'un LDAP
		if (!LDAPServer::is_setup()) {
			return false;
			die();
		} else {
			# Le serveur LDAP est configuré, on y va.
			# Encore un dernier petit test quand même : est-ce que l'utilisateur
			# est bien absent de la base.
			$sql = mysql_query("SELECT login FROM utilisateurs WHERE (login = '".$this->login."')");
			if (mysql_num_rows($sql) != "0") {
				return false;
				die();
			}

			$ldap_server = new LDAPServer;
			$user = $ldap_server->get_user_profile($this->login);
			if ($user) {
				# On ne refait pas de tests ou de formattage. La méthode get_user_profile
				# s'occupe de tout.
				$res = mysql_query("INSERT INTO utilisateurs SET
										login = '".$this->login."',
										prenom = '".$user["prenom"]."',
										nom = '".$user["nom"]."',
										email = '".$user["email"]."',
										civilite = '".$user["civilite"]."',
										statut = '".$user["statut"]."',
										password = '',
										etat = 'actif',
										auth_mode = '".$this->current_auth_mode."',
										change_mdp = 'n'");
				if (!$res) {
					return false;
				} else {
					return true;
				}
			} else {
				return false;
			}
		}
	}
}
?>