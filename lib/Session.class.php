<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard
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

$debug_test_mdp="n";
if(getSettingValue('debug_test_mdp_file')!='') {
	$debug_test_mdp_file=getSettingValue('debug_test_mdp_file');
}
else {
	$debug_test_mdp_file="/tmp/test_mdp.txt";
}
//$debug_test_mdp_file="/tmp/test_mdp.txt";

// Passer à 'y' pour loguer les premiers accès (pour expliquer à l'utilisateur ce qu'il fait de travers lors de sa première connexion)
$debug_login_nouveaux_comptes="n";
// Ne pas toucher: La variable est déclarée ici pour être globale et modifiée à y ou n par la suite
$loguer_nouveau_login="n";

# Cette classe sert à manipuler la session en cours.
# Elle gère notamment l'authentification des utilisateurs
# à partir de différentes sources.

function my_warning_handler($errno, $errstr) {
    if ($errno == E_WARNING && strpos($errstr, 'PHP_Incomplete_Class') !== false)  {
	//ignore warning, this one is probably due to propel unserialization wuthout correct class declaration
    	return true;
    } else {
	return false;
    }
}

class Session {
	public $login = false;
	public $nom = false;
	public $prenom = false;
	public $statut = false;
	public $statut_special = false;
	public $statut_special_id = false;
	public $start = false;
	public $matiere = false;
	public $maxLength = "30"; # Durée par défaut d'une session utilisateur : 30 minutes.
	public $rne = false; # false, n° RNE valide. Utilisé par le multisite.
	public $auth_locale = true; # true, false. Par défaut, on utilise l'authentification locale
	public $auth_ldap = false; # false, true
	public $auth_sso = false; # false, cas, lemon, lcs
	public $auth_simpleSAML = false; # false, cas, lemon, lcs
	private $login_sso = false; //login (ou uid) du sso auquel on est connecté (peut être différent du login gepi, la correspondance est faite dans mod_sso_table) 
	public $current_auth_mode = false;  # gepi, ldap, sso, ou false : le mode d'authentification
    public $mysqli = '';

	private $etat = false; 	# actif/inactif. Utilisé simplement en interne pour vérifier que
							# l'utilisateur authentifié de source externe est bien actif dans Gepi.
							
  private $cas_extra_attributes = false; # D'éventuels attributs chargés depuis la réponse CAS

	public function __construct($login_CAS_en_cours = false) {

    global $temoin_pas_d_update_session_table_log;
    global $mysqli;
    $this->mysqli = $mysqli;

    if (!$login_CAS_en_cours) {
      # On initialise la session
      session_name("GEPI");
      set_error_handler("my_warning_handler", E_WARNING);
		if(!isset($_SESSION)) {
			session_start();
		}
      restore_error_handler();
    }

		# Avant de faire quoi que ce soit, on initialise le fuseau horaire
		if (isset($GLOBALS['timezone']) && $GLOBALS['timezone'] != '') {
		    $this->update_timezone($GLOBALS['timezone']);
        }

		$this->maxLength = getSettingValue("sessionMaxLength");
		$this->verif_CAS_multisite();

		# On charge les valeurs déjà présentes en session
		$this->load_session_data();
		# On charge des éléments de configuration liés à l'authentification
		$this->auth_locale = getSettingValue("auth_locale") == 'yes' ? true : false;
		$this->auth_ldap = getSettingValue("auth_ldap") == 'yes' ? true : false;
		$this->auth_simpleSAML = getSettingValue("auth_simpleSAML") == 'yes' ? true : false;
		$this->auth_sso = in_array(getSettingValue("auth_sso"), array("lemon", "cas", "lcs")) ? getSettingValue("auth_sso") : false;

		if (!$this->is_anonymous()) {
		  # Il s'agit d'une session non anonyme qui existait déjà.
      if (!$login_CAS_en_cours) {
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
      	# On regarde s'il n'y a pas de timeout
        if ($this->start && $this->timeout()) {
           # timeout : on remet à zéro.
          $debut_session = $_SESSION['start'];
          $this->reset(3);
          header("Location:".$logout_path."?auto=3&debut_session=".$debut_session."&session_id=".session_id());
          exit();
        } elseif (isset($GLOBALS['multisite']) && $GLOBALS['multisite'] == 'y') {
        	//echo ($_COOKIE['RNE'].' '.$this->rne);die;
        	if ($_COOKIE['RNE'] != $this->rne){
			  //le rne a été modifié en cours de session
			  $this->reset(2);
	          header("Location:".$logout_path."?auto=0&session_id=".session_id());
	          exit();
            } elseif ((getSettingValue('gepiSchoolRne')!='')&&(mb_strtoupper($_COOKIE['RNE']) != mb_strtoupper(getSettingValue('gepiSchoolRne')))) {
			  //le rne ne correspond pas à celui de la base
			  $this->reset(2);
	          header("Location:".$logout_path."?auto=2&session_id=".session_id());
	          exit();
            }
        } else {
			$debug_maintien_session="n";
			if($debug_maintien_session=="y") {
				$sql = "SELECT END from log where SESSION_ID = '" . session_id() . "' and START = '" . $this->start . "';";
                
                if($this->mysqli !="") {
                    $result = mysqli_query($this->mysqli, $sql);
                    $tmp_fin_session = $result->fetch_object();
                    $tmp_fin_session = $tmp_fin_session->END;
                    $result->close();
                } else {
                    $tmp_res_fin_session=mysqli_query($GLOBALS["mysqli"], $sql);
                    $tmp_fin_session=old_mysql_result($tmp_res_fin_session,0,'END');
                }  
                
			}

			if((!isset($temoin_pas_d_update_session_table_log))||($temoin_pas_d_update_session_table_log!="y")) {
				# Pas de timeout : on met à jour le log
				$this->update_log();

				if($debug_maintien_session=="y") {
					$fich=fopen("/tmp/update_log.txt", "a+");
					fwrite($fich, strftime("%Y%m%d %H%M%S")." : Update log à $tmp_fin_session\n");
					fwrite($fich, "$sql\n");
					fclose($fich);
				}
			}
			else {
				if($debug_maintien_session=="y") {
					$fich=fopen("/tmp/update_log.txt", "a+");
					fwrite($fich, strftime("%Y%m%d %H%M%S")." : Pas d update log \nLa fin de session reste à $tmp_fin_session\n".(isset($temoin_pas_d_update_session_table_log) ? "\$temoin_pas_d_update_session_table_log=".$temoin_pas_d_update_session_table_log : "\$temoin_pas_d_update_session_table_log non initialise")."\n");
					fwrite($fich, "$sql\n");
					fclose($fich);
				}
			}
        }
      }
		}

	}

	# S'agit-il d'une session anonyme ?
	public function is_anonymous() {
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
	public function authenticate($_login = null, $_password = null) {
		global $debug_test_mdp, $debug_test_mdp_file, $debug_login_nouveaux_comptes, $loguer_nouveau_login;
		global $mysqli;

		// Quelques petits tests de sécurité

	    // Vérification de la liste noire des adresses IP
	    if (isset($GLOBALS['liste_noire_ip']) && in_array($_SERVER['REMOTE_ADDR'], $GLOBALS['liste_noire_ip'])) {
		  tentative_intrusion(1, "Tentative de connexion depuis une IP sur liste noire (login utilisé : ".$_login.")");
	      return "3";
		  die();
		}

		if ($_login != null && mb_strtoupper($_login) != mb_strtoupper($this->login)) {
			//on a une connexion sous un nouveau login, on purge la session
			$this->reset("10");
		}

		if($debug_test_mdp=="y") {
			$f_tmp=fopen($debug_test_mdp_file,"a+");
			fwrite($f_tmp,strftime("%a %d/%m/%Y - %H%M%S").": \$_login=$_login et \$_password=$_password\n");
			fclose($f_tmp);
		}
		elseif($debug_login_nouveaux_comptes=="y") {
			$loguer_nouveau_login="n";
			if(preg_match("/[A-Za-z0-9_\.-]/", $_login)) {
				$sql="SELECT 1=1 FROM utilisateurs WHERE login='$_login' AND change_mdp='y';";
                 
                    $resultat = mysqli_query($mysqli, $sql);  
                    $nb_lignes = $resultat->num_rows;
                    $resultat->close();
                if($nb_lignes>0) {
                    $loguer_nouveau_login="y";

                    $f_tmp=fopen($debug_test_mdp_file,"a+");
                    fwrite($f_tmp,strftime("%a %d/%m/%Y - %H%M%S").": \$_login=$_login et \$_password=$_password : ");
                    fclose($f_tmp);
                }
			}
		}

	    // On initialise la session de l'utilisateur.
	    // On commence par extraire le mode d'authentification défini
	    // pour l'utilisateur. Si l'utilisateur n'existe pas, on essaiera
	    // l'authentification LDAP et le SSO quand même.
		$auth_mode = self::user_auth_mode($_login);

		// 20140301
		$auth_sso_secours=isset($_POST['auth_sso_secours']) ? $_POST['auth_sso_secours'] : NULL;
		if((isset($auth_sso_secours))&&
			($auth_sso_secours=="y")&&
			($_login!="")&&
			($_password!="")&&
			(getSettingAOui('autoriser_sso_password_auth'))) {
			$auth_mode="gepi";
		}

		switch ($auth_mode) {
			case "gepi":
			  # Authentification locale sur la base de données Gepi
			  $auth = $this->authenticate_gepi($_login,$_password);
			break;
			case "ldap":
			  # Authentification sur un serveur LDAP
			  $auth = $this->authenticate_ldap($_login,$_password);
			  break;
		  	case "simpleSAML":
		  		$auth = $this->authenticate_simpleSAML();
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
			  # On va donc tenter d'abord une authentification simpleSAML, puis LDAP,
			  # puis une authentification SSO, à condition que celles-ci
			  # soient bien sûr configurées.
			  if ($this->auth_ldap && $_login != null && $_password != null) {
			  	$auth = $this->authenticate_ldap($_login,$_password);
			  }if ($this->auth_simpleSAML) {
			  	$auth = $this->authenticate_simpleSAML();
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

				if (!isset($_GET['rne']) AND (!isset($_COOKIE["RNE"]) OR $_COOKIE["RNE"] == 'RNE')) {
					if (isset($GLOBALS['mode_choix_base']) && $GLOBALS['mode_choix_base'] == "url") {
						// dans ce cas, on se connecte à l'url $url_cas_sso donnée par le secure/connect.inc.php
						$t_rne = file_get_contents($GLOBALS[url_cas_sso] . '?login=' . $this->login . '&cle=' . $GLOBALS['cle_url_cas']);
						if ($t_rne != 'erreur') {
							$rep_rne = explode("|", $t_rne);
							$nbre_rne = count($rep_rne);
							if ($nbre_rne > 1) {
								header("Location: choix_rne.php?nbre=".$nbre_rne."&lesrne=".$t_rne);
								exit();
							} else{
								if ($this->current_auth_mode == "sso") {
									setcookie('RNE', $t_rne, null, '/');
									header("Location: login_sso.php?rne=".$t_rne);
									exit();
								} else {
									header("Location: login.php?rne=".$t_rne);
									exit();
								}
							}
						}
					} elseif (LDAPServer::is_setup()) {
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
								exit();

							}else{
								// Il n'y en a qu'un, on recharge !
								if ($this->current_auth_mode == "sso") {
									setcookie('RNE', $user["rne"][0], null, '/');
									header("Location: login_sso.php?rne=".$user["rne"][0]);
									exit();
								} else {
									header("Location: login.php?rne=".$user["rne"][0]);
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

				# Si on a activé la synchro Scribe, on utilise alors l'import spécifique
				if (getSettingValue("may_import_user_profile") == "yes" && getSettingValue("sso_scribe") == "yes") {
					$load = $this->import_user_profile_from_scribe();

					# Sinon, on utilise l'import classique, très basique.
				} elseif (getSettingValue("may_import_user_profile")=="yes") {
					$load = $this->import_user_profile();
				}
				if (!$load) {
					return "6";
					exit();
				} else {
					# Si l'import a réussi, on tente à nouveau de charger
					# les données de l'utilisateur.
					$this->load_user_data();
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
			// 20140301
			if((!isset($auth_sso_secours))||
				($auth_sso_secours!="y")||
				(!getSettingAOui('autoriser_sso_password_auth'))) {
				$auth_mode = self::user_auth_mode($this->login);
				if ($this->current_auth_mode != 'simpleSAML' && $auth_mode != $this->current_auth_mode) {
					$this->reset(2);
					return "5";
					exit;
				}
			}

			# Si on est en mode CAS, on met à jour à la volée les attributs de
			# l'utilisateur (le cas échéant)
			if ($this->auth_sso == 'cas') {
				$this->update_user_with_cas_attributes();
			}

			# Tout est bon. On valide définitivement la session.

			$sql_start = mysqli_query($mysqli, "SELECT now();");
			$row = $sql_start->fetch_row();
			$this->start = $row[0];
			$sql_start->close();
			
			$_SESSION['start'] = $this->start;
			$this->insert_log();
			# On supprime l'historique des logs conformément à la durée définie.
			$sql_del = "delete from log where START < now() - interval " . getSettingValue("duree_conservation_logs") . " day and END < now()";
			$resultat = mysqli_query($mysqli, $sql_del);

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
	public function security_check() {
		global $pas_acces_a_une_page_sans_etre_logue;
		global $mysqli;
		# Codes renvoyés :
		# 0 = logout automatique
		# 1 = session valide
		# c = changement forcé de mot de passe

		# D'abord on regarde si on a une tentative d'accès anonyme à une page protégée :
		if ($this->auth_simpleSAML == 'yes') {
			include_once(dirname(__FILE__).'/simplesaml/lib/_autoload.php');
			$auth = new SimpleSAML_Auth_GepiSimple();
			if (!$this->login || !$auth->isAuthenticated()) {
				$this->authenticate();
			}
		} else if ($this->is_anonymous()) {
			if((!isset($pas_acces_a_une_page_sans_etre_logue))||($pas_acces_a_une_page_sans_etre_logue!="y")) {
				tentative_intrusion(1, "Accès à une page sans être logué (peut provenir d'un timeout de session).");
			}
			return "0";
			exit;
		}

		$sql = "SELECT statut, change_mdp, etat FROM utilisateurs where login = '" . $this->login . "'";
		
        
            $result = mysqli_query($mysqli, $sql);
            $row = $result->fetch_object();

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
				($this->current_auth_mode == "gepi" || $this->current_auth_mode == "simpleSAML" || getSettingValue("ldap_write_access") == "yes"))
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
	public static function user_auth_mode($_login) {
        global $mysqli;
		if ($_login == null) {
			return false;
			die();
		}
        
        $sql = "SELECT auth_mode FROM utilisateurs WHERE UPPER(login) = '".mb_strtoupper($_login)."'";
            $resultat = mysqli_query($mysqli, $sql);  
            $nb_lignes = $resultat->num_rows;
            $result = $resultat->fetch_object();
            $retour = $result->auth_mode;
            $resultat->close();
		if ($nb_lignes == 0) {
			return false;
		} else {
			return $retour;
		}
	}

	public function close ($_auto) {
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
	public function recreate_log() {
		// On teste que le login enregistré en session existe bien dans la table
		// des utilisateurs. Ceci est pour vérifier que cette opération de
		// réécriture du log est bien nécessaire, et valide !
		if ($this->login == '') {
			return false;
		} else {
            global $mysqli;
            $sql = "SELECT login FROM utilisateurs WHERE login = '".$this->login."'";
                    		         
                $resultat = mysqli_query($mysqli, $sql);  
                $nb_lignes = $resultat->num_rows;
                $resultat->close();
			
			if ($test == 0) {
				return false;
			} else {
				return $this->insert_log();
			}
		}
	}

	## METHODE PRIVEES ##

	// Création d'une entrée de log
	public function insert_log() {
        global $mysqli;
    include_once(dirname(__FILE__).'/HTMLPurifier.standalone.php');
    $config = HTMLPurifier_Config::createDefault();
    $config->set('Core.Encoding', 'utf-8'); // replace with your encoding
    $config->set('HTML.Doctype', 'XHTML 1.0 Strict'); // replace with your doctype
    $purifier = new HTMLPurifier($config);
    
		if (!isset($_SERVER['HTTP_REFERRER'])) $_SERVER['HTTP_REFERER'] = '';
	    $sql = "INSERT INTO log (LOGIN, START, SESSION_ID, REMOTE_ADDR, USER_AGENT, REFERER, AUTOCLOSE, END) values (
	                '" . $this->login . "',
	                '" . $this->start . "',
	                '" . session_id() . "',
	                '" . $purifier->purify($_SERVER['REMOTE_ADDR']) . "',
	                '" . $purifier->purify($_SERVER['HTTP_USER_AGENT']) . "',
	                '" . $purifier->purify($_SERVER['HTTP_REFERER']) . "',
	                '1',
	                '" . $this->start . "' + interval " . $this->maxLength . " minute
	            )
	        ;";        
            $res = mysqli_query($mysqli, $sql);
	    
	}

	// Mise à jour du log de l'utilisateur
	private function update_log() {
        global $mysqli;
		if ($this->is_anonymous()) {
			return false;
		} else {
			$sql = "UPDATE log SET END = now() + interval " . $this->maxLength . " minute where SESSION_ID = '" . session_id() . "' and START = '" . $this->start . "'";
            $res = mysqli_query($mysqli, $sql); 
		}
	}

	// Dans le cas du multisite on vérifie si la session a été initialisée dans la bonne base
	private function verif_CAS_multisite(){
        global $mysqli;
		if (isset($_GET['rne']) AND $GLOBALS['multisite'] == 'y' AND isset($_SESSION["login"]) && getSettingValue("auth_simpleSAML") != 'yes') {
			// Alors, on initialise la session ici

			if (!preg_match("/^[0-9A-Za-z]*$/", $_GET["rne"])) {
				die("RNE invalide.");
			}
         
                $resultat = mysqli_query($mysqli, "SELECT now();");
                $result = $resultat->fetch_row();
                $this->start = $row[0];
                $resultat->close();
			
			$_SESSION['start'] = $this->start;
			$this->recreate_log();

		}
	}

	// Test pour voir si la session de l'utilisateur est en timeout
	private function timeout() {
    	$sql = "SELECT now() > END TIMEOUT from log where SESSION_ID = '" . session_id() . "' and START = '" . $this->start . "'";
    	return sql_query1($sql);
	}

  // Function appelée par phpCAS lors du logout (cf. login_sso.php), destinée
  // à enregistrer proprement un logout initié par le serveur CAS lui-même
  // dans le cas d'une déconnexion depuis une autre application.
  function cas_logout_callback($ticket) {
    // On enregistre la fin de la session dans le journal
    $this->register_logout(0);
    
    // Rien d'autre à faire. C'est phpCAS qui va détruire la session totalement.
  }

  // Enregistrement de la fin de la session dans la base de données
  private function register_logout($_auto) {
      global $mysqli;
      $sql = "UPDATE log SET AUTOCLOSE = '" . $_auto . "', END = now() where SESSION_ID = '" . session_id() . "' and START = '" . $this->start . "'";
              
            $res = mysqli_query($mysqli, $sql);
              

			if((getSettingValue('csrf_log')=='y')&&(isset($_SESSION['login']))) {
				$csrf_log_chemin=getSettingValue('csrf_log_chemin');
				if($csrf_log_chemin=='') {$csrf_log_chemin="/home/root/csrf";}
				//$f=fopen("$csrf_log_chemin/csrf_".$_SESSION['login'].".log","a+");
				$f=fopen("$csrf_log_chemin/csrf_".$_SESSION['login'].".log","a+");
				fwrite($f,"Fin de session ".strftime("%a %d/%m/%Y %H:%M:%S")." avec\n");
				if(isset($_SESSION['gepi_alea'])) {fwrite($f,"\$_SESSION['gepi_alea']=".$_SESSION['gepi_alea']."\n");}
				fwrite($f,"$sql\n");
				fwrite($f,"-----------------\n");
				fclose($f);
			}
  }


	// Remise à zéro de la session : on supprime toutes les informations présentes
	private function reset($_auto = "0") {
		# Codes utilisés pour $_auto :
		# 0 : logout normal
		# 2 : logout renvoyé par la fonction checkAccess (problème gepiPath ou accès interdit)
		# 3 : logout lié à un timeout
		# 10 : logout lié à une nouvelle connexion sous un nouveau profil

	    # On teste 'start' simplement pour simplement vérifier que la session n'a pas encore été fermée.
	    if ($this->start) {
        $this->register_logout($_auto);
	    }

		if ($this->auth_simpleSAML == 'yes') {
				include_once(dirname(__FILE__).'/simplesaml/lib/_autoload.php');
				$auth = new SimpleSAML_Auth_GepiSimple();				
				if ($auth->isAuthenticated()) {
					$auth->logout();
					//attention, cette fonction ->logout() ne retourne, pas, le reste du script ne sera pas éxécuter à partir de cette ligne.
					//Il à y avoir un refresh automatique de la page suite au ->logout(), et donc le script va être re-éxecuter, avec cette fois
					//$auth->isAuthenticated() qui vaudra false, et donc le reste du reset va être éxecuter
				}
		}
		
	    // Détruit toutes les variables de session
	    session_unset();
	    $_SESSION = array();

	    // Détruit le cookie sur le navigateur
	    $CookieInfo = session_get_cookie_params();
	    @setcookie(session_name(), '', time()-3600, $CookieInfo['path']);


	    // détruit la session sur le serveur
	    session_destroy();

		//on redémarre une nouvelle session
		session_start();
		session_regenerate_id();
		
		$this->login = null;
		
		//si une url de portail est donnée, on redirige
		if (isset($_REQUEST['portal_return_url'])) {
			header('Location:'.$_REQUEST['portal_return_url']);
			die;
		}
		
	}

	private function load_session_data() {
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

	function authenticate_gepi($_login,$_password) {
		global $debug_test_mdp, $debug_test_mdp_file;
		global $debug_login_nouveaux_comptes, $loguer_nouveau_login;
        global $mysqli;

        $sql = "SELECT login, password FROM utilisateurs WHERE (login = '" . $_login . "' and etat != 'inactif')";
               
            $resultat = mysqli_query($mysqli, $sql);  
            $nb_lignes = $resultat->num_rows;
            if ($nb_lignes == "1") {
                $result_query = $resultat->fetch_object();
                $sql_salt = "SELECT salt FROM utilisateurs WHERE (login = '" . $_login . "' and etat != 'inactif')";
                $query_salt = mysqli_query($mysqli, $sql_salt);
                    if ($query_salt !== false) {
                        $row = $query_salt->fetch_row();
                        $db_salt = $row[0];
                        $query_salt->close();
                    } else {
                        $db_salt = '';
                    }
                $db_password = $result_query->password;
                # Un compte existe avec ce login
                if ($db_salt == '') {
                    //on va tester avec le md5
                    if ($db_password == md5($_password)) {
                    } else {
                        $this->debug_login_mdp($debug_test_mdp, $debug_test_mdp_file, 'Recu: '.$_password."\n");
                        $tmp_mdp = array_flip (get_html_translation_table(HTML_ENTITIES));
                        $_password_unhtmlentities = strtr ($_password, $tmp_mdp);
                        if ($db_password == md5($_password_unhtmlentities)) {
                            $this->debug_login_mdp($debug_test_mdp, $debug_test_mdp_file, 'Authentification md5 OK avec unhtmlentities()'."\n");
                        } else {
                            $this->debug_login_mdp($debug_test_mdp, $debug_test_mdp_file, 'Authentification md5 en echec avec et sans modification unhtmlentities'."\n");
                            return false;
                        }
                    }
                    //l'authentification est réussie sinon on serait déjà sorti de la fonction
                     $this->debug_login_mdp($debug_test_mdp, $debug_test_mdp_file, 'Authentification md5 OK'."\n");
                     $sql_show = "SHOW COLUMNS FROM utilisateurs LIKE 'salt';";
                     $query_show = mysqli_query($mysqli, $sql_show);
                     if ($query_show->num_rows > 0) {
                        //on va passer le hash en hmac scha256
                        $salt = md5(uniqid(rand(), 1));
                        $hmac_password = hash_hmac('sha256', $_password, $salt);
                        $sql_update = "UPDATE utilisateurs SET password = '".$hmac_password."', salt = '".$salt."' WHERE login = '".$_login."'";
                        $update_query = mysqli_query($mysqli, $sql_update);
                        if ($update_query) {
                            $this->debug_login_mdp($debug_test_mdp, $debug_test_mdp_file, 'Password ameliore en hmac'."\n");
                        } else {
                            $this->debug_login_mdp($debug_test_mdp, $debug_test_mdp_file, 'Echec password ameliore en hmac'."\n");
                        }
                    }            
                    
                } else {
                    //login deja en hmac sha256
                    if ($db_password == hash_hmac('sha256', $_password, $db_salt)) {
                        $this->debug_login_mdp($debug_test_mdp, $debug_test_mdp_file, 'Authentification hmac OK sans modification'."\n");
                    } else {
                        $tmp_mdp = array_flip (get_html_translation_table(HTML_ENTITIES));
                        $_password_unhtmlentities = strtr ($_password, $tmp_mdp);
                        if ($db_password == hash_hmac('sha256', $_password_unhtmlentities, $db_salt)) {
                            $this->debug_login_mdp($debug_test_mdp, $debug_test_mdp_file, 'Authentification hmac OK avec unhtmlentities()'."\n");
                        } else {
                            $this->debug_login_mdp($debug_test_mdp, $debug_test_mdp_file, 'Authentification hmac en echec avec et sans modification unhtmlentities'."\n");
                            return false;
                        }
                    }
                }
                //si le login fait échec, la fonction a déjà retourné avec false
                $this->login = $result_query->login;
                $this->current_auth_mode = "gepi";
                return true;
              
            } else {
                # Le login est erroné (n'existe pas dans la base)
                return false;
            }             
            $resultat->close();
	}

    static function change_password_gepi($user_login,$password) {
        global $mysqli;
        $sql = "SHOW COLUMNS FROM utilisateurs LIKE 'salt';";
               
            $resultat = mysqli_query($mysqli, $sql); 
            if ($resultat->num_rows > 0) {
                $salt = md5(uniqid(rand(), 1));
                $hmac_password = hash_hmac('sha256', $password, $salt);
                $result = mysqli_query($mysqli, "UPDATE utilisateurs SET password='$hmac_password', salt = '$salt' WHERE login='" . $user_login . "'");
                $resultat->close();
                return $result;                
            } else {
                $result = mysqli_query($mysqli, "UPDATE utilisateurs SET password='".md5($password)."' WHERE login='" . $user_login . "'");
                $resultat->close();
                return $result;                
            } 

    }

	private function authenticate_ldap($_login,$_password) {
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

	private function authenticate_cas() {
/* *****
 *  Toute la partie authentification en elle-même a été déplacée dans le
 *  fichier login_sso.php, afin de permettre à phpCAS de gérer tout seul
 *  la session PHP.
 * *****
 * 
		include_once('CAS.php');
		if ($GLOBALS['mode_debug']) {
		    phpCAS::setDebug($GLOBALS['debug_log_file']);
    }
		// config_cas.inc.php est le fichier d'informations de connexions au serveur cas
		$path = dirname(__FILE__)."/../secure/config_cas.inc.php";
		include($path);

		# On défini l'URL de base, pour que phpCAS ne se trompe pas dans la génération
		# de l'adresse de retour vers le service (attention, requiert patchage manuel
		# de phpCAS !!)
		if (isset($GLOBALS['gepiBaseUrl'])) {
			$url_base = $GLOBALS['gepiBaseUrl'];
		} else {
			$url_base = $this->https_request() ? 'https' : 'http';
			$url_base .= '://';
			$url_base .= $_SERVER['SERVER_NAME'];
		}

		// Le premier argument est la version du protocole CAS
		// Le dernier argument a été ajouté par patch manuel de phpCAS.
		phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_root, false, $url_base);
		phpCAS::setLang('french');

		// redirige vers le serveur d'authentification si aucun utilisateur authentifié n'a
		// été trouvé par le client CAS.
		phpCAS::setNoCasServerValidation();

		// Gestion du single sign-out
		phpCAS::handleLogoutRequests(false);
		
		// Authentification
		phpCAS::forceAuthentication();
*/
if (getSettingValue("sso_cas_table") == 'yes') {
            $this->login_sso = phpCAS::getUser();
            $test = $this->test_loginsso();
            if ($test == '0') {
                //la correspondance n'existe pas dans gépi; on detruit la session avant de rediriger.            
                session_destroy();
                header("Location:login_failure.php?error=11&mode=sso_table");
                exit;
            } else {
                $this->login = $test;
            }
        } else {
            $this->login = phpCAS::getUser();
        }
		
/* La session est gérée par phpCAS directement, en amont. On n'y touche plus.
		session_name("GEPI");
		session_start();
*/
		$_SESSION['login'] = $this->login;

		$this->current_auth_mode = "sso";
    
    // Extractions des attributs supplémentaires, le cas échéant
    $tab = phpCAS::getAttributes();
    $attributs = array('prenom','nom','email');
    foreach($attributs as $attribut) {
      $code_attribut = getSettingValue('cas_attribut_'.$attribut);
      // Si un attribut a été spécifié, on va le chercher
      if (!empty($code_attribut)) {
      	if (isset($tab[$code_attribut])) {
        	$valeur = $tab[$code_attribut];
					if (!empty($valeur)){
					    // L'attribut est trouvé et non vide, on l'assigne pour mettre à jour l'utilisateur
						// On s'assure que la chaîne est bien enregistrée en UTF-8.
						$valeur = ensure_utf8($valeur);
						$this->cas_extra_attributes[$attribut] = trim(mysqli_real_escape_string($GLOBALS["mysqli"], $valeur));
					}
        }
      }
    }
		return true;
	}

    private function test_loginsso()
  {
        global $mysqli;
        $requete = "SELECT login_gepi FROM sso_table_correspondance WHERE login_sso='$this->login_sso'";
                   
            $result = mysqli_query($mysqli, $requete);
            $valeur = $result->fetch_row();
            if ($valeur[0] == '') {
                return "0";
            } else {
                return $valeur[0];
            } 
  }

	public function logout_cas() {
		include_once('CAS.php');

		// config_cas.inc.php est le fichier d'informations de connexions au serveur cas
		$path = dirname(__FILE__)."/../secure/config_cas.inc.php";
		include($path);

		// Le premier argument est la version du protocole CAS
		phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_root, true, $url_base);
		phpCAS::setLang(PHPCAS_LANG_FRENCH);
		if ($cas_use_logout) {
			phpCAS::logout();
		}else{
			if ($cas_logout_url != '') {
				header("Location:".$cas_logout_url);
				exit();
			}else{
				// Il faudra trouver mieux
				echo '<html><head><title>GEPI</title></head><body><h2>Vous &ecirc;tes d&eacute;connect&eacute;.</h2></body></html>';
				exit();
			}

		}
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

	private function authenticate_simpleSAML() {
		include_once(dirname(__FILE__).'/simplesaml/lib/_autoload.php');
		$auth = new SimpleSAML_Auth_GepiSimple();
		$auth->requireAuth();
		$attributes = $auth->getAttributes();
		
		//exploitation des attributs
		if (empty($attributes)) {
			//authentification échouée
			return false;
		}
		$this->login = $attributes['login_gepi'][0];

		$this->current_auth_mode = "simpleSAML";
    
	    // Extractions des attributs supplémentaires, le cas échéant
	    // inutile pour le moment
		return true;
	}
	
	private function authenticate_lemon() {
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

	private function authenticate_lcs() {
		/*
		include LCS_PAGE_AUTH_INC_PHP;
		include LCS_PAGE_LDAP_INC_PHP;
		# LCS a besoin de quelques variables extérieures...
		# L'initialisation ci-dessous n'est pas très propre, il faudra
		# reprendre ça...
		*/
		global $login;

		$DBAUTH = $GLOBALS['DBAUTH'];
		$HTTP_COOKIE_VARS = $GLOBALS['HTTP_COOKIE_VARS'];
		$authlink = $GLOBALS['authlink'];
		$dbHost = $GLOBALS['dbHost'];
		$dbUser = $GLOBALS['dbUser'];
		$dbPass = $GLOBALS['dbPass'];
		$dbDb = $GLOBALS['dbDb'];

		if ($login!="") {
			list($user, $groups)=people_get_variables($login, false);
			#TODO: Utiliser les infos des lignes ci-dessous pour mettre à jour
			# les informations de l'utilisateur dans la base.
			$lcs_tab_login["nom"] = $user["nom"];
			$lcs_tab_login["email"] = $user["email"];
			$long = mb_strlen($user["fullname"]) - mb_strlen($user["nom"]);
			$lcs_tab_login["fullname"] = mb_substr($user["fullname"], 0, $long) ;

			// A ce stade, l'utilisateur est authentifié
			// Etablir à nouveau la connexion à la base
			if (isset($GLOBALS['db_nopersist']) && !$GLOBALS['db_nopersist'])
				$db_c = ($GLOBALS["mysqli"] = mysqli_connect($dbHost,  $dbUser,  $dbPass));
			else
				$db_c = ($GLOBALS["mysqli"] = mysqli_connect($dbHost,  $dbUser,  $dbPass));

			if (!$db_c || !((bool)mysqli_query($GLOBALS["mysqli"], "USE $dbDb"))) {
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
	public function load_user_data() {
        global $mysqli;
		# Petit test de départ pour être sûr :
		if (!$this->login || $this->login == null) {
			return false;
			exit();
		}

		# Gestion du multisite : on a besoin du RNE de l'utilisateur.
		if (isset($GLOBALS['multisite']) && $GLOBALS['multisite'] == 'y' && LDAPServer::is_setup()) {
			$ldap = new LDAPServer;
			$user = $ldap->get_user_profile($this->login);
			$this->rne = $user["rne"][0];
		} elseif (isset($GLOBALS['multisite']) && $GLOBALS['multisite'] == 'y') {
			$this->rne = $_COOKIE['RNE'];
		}

		# On interroge la base de données
        $sql = "SELECT login, nom, prenom, email, statut, etat, now() start, change_mdp, auth_mode FROM utilisateurs WHERE (login = '".$this->login."')";
                
        if($this->mysqli !="") {
            $query = mysqli_query($this->mysqli, $sql);
            # Est-ce qu'on a bien une entrée ?
            if ($query->num_rows != "1") {
                return false;
                exit();
            }
            $sql = "SELECT id_matiere FROM j_professeurs_matieres WHERE (id_professeur = '" . $this->login . "') ORDER BY ordre_matieres LIMIT 1";
            $matiere_principale = sql_query1($sql);
            $row = $query->fetch_object();
        } else {
            $query = mysqli_query($GLOBALS["mysqli"], $sql);

            # Est-ce qu'on a bien une entrée ?
            if (mysqli_num_rows($query) != "1") {
                return false;
                exit();
            }

            $sql = "SELECT id_matiere FROM j_professeurs_matieres WHERE (id_professeur = '" . $this->login . "') ORDER BY ordre_matieres LIMIT 1";
            $matiere_principale = sql_query1($sql);

            $row = mysqli_fetch_object($query);	
        }

	    $_SESSION['login'] = $this->login;
		if ($row->login != null) {
				$_SESSION['login'] = $row->login;
		} else {
				$_SESSION['login'] = $this->login;
		}

	    $_SESSION['prenom'] = $row->prenom;
	    $_SESSION['nom'] = $row->nom;
		$_SESSION['email'] = $row->email;
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
                    
                $query = mysqli_query($mysqli, $sql);
                $result = $query->fetch_object();
                      
			$_SESSION['statut_special'] = $result->nom_statut;
			$_SESSION['statut_special_id'] = $result->id;

	    }

		
		//generate_token($_SESSION['login']);
		generate_token();

	    # On charge les données dans l'instance de Session.
	    $this->load_session_data();
	    return true;
	}


	public function record_failed_login($_login) {
        global $mysqli;
  
    include_once(dirname(__FILE__).'/HTMLPurifier.standalone.php');
    $config = HTMLPurifier_Config::createDefault();
    $config->set('Core.Encoding', 'utf-8'); // replace with your encoding
    $config->set('HTML.Doctype', 'XHTML 1.0 Strict'); // replace with your doctype
    $purifier = new HTMLPurifier($config);
    
		# Une tentative de login avec un mot de passe erronnée a été détectée.
    $sql_login = "SELECT login FROM utilisateurs WHERE (login = '".$_login."')";
       
        $resultat = mysqli_query($mysqli, $sql_login);  
        $test_login = $resultat->num_rows;
		if ($test_login != "0") {
            tentative_intrusion(1, "Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n'est significative qu'en cas de répétition. (login : ".$_login.")");
			# On a un vrai login.
			# On enregistre un log d'erreur de connexion.
	        $sql = "insert into log (LOGIN, START, SESSION_ID, REMOTE_ADDR, USER_AGENT, REFERER, AUTOCLOSE, END) values (
	        	'" . $_login . "',
	            now(),
	            '',
	            '" . $purifier->purify($_SERVER['REMOTE_ADDR']) . "',
	            '" . $purifier->purify($_SERVER['HTTP_USER_AGENT']) . "',
	            '" . $purifier->purify($_SERVER['HTTP_REFERER']) . "',
	            '4',
	            now());";
	        $res = mysqli_query($mysqli, $sql);
            
	        // On compte de nombre de tentatives infructueuse issues de la même adresse IP
	        $sql = "select LOGIN from log where
	                LOGIN = '" . $_login . "' and
	                START > now() - interval " . getSettingValue("temps_compte_verrouille") . " minute and
	                REMOTE_ADDR = '".$_SERVER['REMOTE_ADDR']."'
	                ";
	        $res_test = mysqli_query($mysqli, $sql);
	        if ($res_test->num_rows > getSettingValue("nombre_tentatives_connexion")) {
	        	$this->lock_account($_login);
                $resultat->close(); 
	        	return true;
            } else {
                $resultat->close(); 
	        	return false;
            }
        } else {
            tentative_intrusion(1, "Tentative de connexion avec un login incorrect (n'existe pas dans la base Gepi). Ce peut être simplement une faute de frappe. Cette alerte n'est significative qu'en cas de répétition. (login utilisé : ".$_login.")");
            // Le login n'existe pas. On fait donc un test sur l'IP.
            $sql = "select LOGIN from log where
                START > now() - interval " . getSettingValue("temps_compte_verrouille") . " minute and
                REMOTE_ADDR = '".$purifier->purify($_SERVER['REMOTE_ADDR'])."'";
            $res_test = mysqli_query($mysqli, $sql);
            if ($res_test->num_rows <= 10) {
                // On a moins de 10 enregistrements. On enregistre et on ne renvoie pas de code
                // de verrouillage.
                $sql = "insert into log (LOGIN, START, SESSION_ID, REMOTE_ADDR, USER_AGENT, REFERER, AUTOCLOSE, END) values (
                    '" . $_login . "',
                    now(),
                    '',
                    '" . $purifier->purify($_SERVER['REMOTE_ADDR']) . "',
                    '" . $purifier->purify($_SERVER['HTTP_USER_AGENT']) . "',
                    '" . $purifier->purify($_SERVER['HTTP_REFERER']) . "',
                    '4',
                    now()
                    )
                    ;";
                $res = mysqli_query($mysqli, $sql);
                $resultat->close(); 
                return false;
            } else {
                // On a 10 entrées, on renvoie un code d'erreur de verouillage.
                $resultat->close(); 
                return true;
            }  
        }
        
        $resultat->close(); 
	}
  

	# Verrouillage d'un compte en raison d'un trop grand nombre d'échec de connexion.
	private function lock_account($_login) {
        global $mysqli;
       if ((!isset($GLOBALS['bloque_compte_admin'])) or ($GLOBALS['bloque_compte_admin'] != "n")) {
          // On verrouille le compte même si c'est un admin 
                $reg_data = mysqli_query($mysqli,"UPDATE utilisateurs SET date_verrouillage=now() WHERE login='".$_login."'" );
            
       } else {
          // on ne bloque pas le compte d'un administrateur    
                $reg_data = mysqli_query($mysqli,"UPDATE utilisateurs SET date_verrouillage=now() WHERE login='".$_login."' and statut!='administrateur'");
            
       }
       # On enregistre une alerte de sécurité.
       tentative_intrusion(2, "Verrouillage du compte ".$_login." en raison d'un trop grand nombre de tentatives de connexion infructueuses. Ce peut être une tentative d'attaque brute-force.", $_login);
       return true;
	}

	# Renvoie true ou false selon que le compte est bloqué ou non.
	private function account_is_locked() {
        global $mysqli;
        $sql_verrouillage = "select login, statut from utilisateurs where
			login = '" . $this->login . "' and
			date_verrouillage > now() - interval " . getSettingValue("temps_compte_verrouille") . " minute ";

        $test_verrouillage = sql_query1($sql_verrouillage);
        
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

	private function import_user_profile() {
        global $mysqli;
		# On ne peut arriver ici quand dans le cas où on a une authentification réussie.
		# L'import d'un utilisateur ne peut se faire qu'à partir d'un LDAP
		if (!LDAPServer::is_setup()) {
			return false;
			die();
		} else {
			# Le serveur LDAP est configuré, on y va.
			# Encore un dernier petit test quand même : est-ce que l'utilisateur
			# est bien absent de la base.
			$sql = "SELECT login FROM utilisateurs WHERE (login = '".$this->login."')";
                    
                $resultat = mysqli_query($mysqli, $sql); 
                if ($resultat->num_rows != "0") {
                    $resultat->close();
                    return false;
                    die();
                }
                $resultat->close();

			$ldap_server = new LDAPServer;
			$user = $ldap_server->get_user_profile($this->login);
			if ($user) {
				# On ne refait pas de tests ou de formattage. La méthode get_user_profile
				# s'occupe de tout.
                $sql = "INSERT INTO utilisateurs SET
										login = '".$this->login."',
										prenom = '".$user["prenom"]."',
										nom = '".$user["nom"]."',
										email = '".$user["email"]."',
										civilite = '".$user["civilite"]."',
										statut = '".$user["statut"]."',
										password = '',
										etat = 'actif',
										auth_mode = '".$this->current_auth_mode."',
										change_mdp = 'n'";
                        
                    $res = mysqli_query($mysqli, $sql);
                
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


	private function import_user_profile_from_scribe() {
        global $mysqli;
		# On ne peut arriver ici quand dans le cas où on a une authentification réussie.
		# L'import d'un utilisateur ne peut se faire qu'à partir d'un LDAP de Scribe, ici.
		if (!LDAPServer::is_setup()) {
			return false;
			die();
		} else {
      
      // config_cas.inc.php est le fichier d'informations de connexions au serveur cas
      $path = dirname(__FILE__)."/LDAPServerScribe.class.php";
      include($path);
      
			# Le serveur LDAP est configuré, on y va.
			# Encore un dernier petit test quand même : est-ce que l'utilisateur
			# est bien absent de la base.
			$sql = "SELECT login FROM utilisateurs WHERE (login = '".$this->login."')"; 
                $resultat = mysqli_query($mysqli, $sql);  
                if ($resultat->num_rows != "0") {
                    $resultat->close();
                    return false;
                    die();                    
                }
                $resultat->close();

			$ldap_server = new LDAPServerScribe;
      
			$user = $ldap_server->get_user_profile($this->login);
			if ($user) {
				# On ne refait pas de tests ou de formattage. La méthode get_user_profile
				# s'occupe de tout.
        
        $errors = false;
        
        // On s'occupe de tous les traitements spécifiques à chaque statut
        
        // Eleve
        if ($user['statut'] == 'eleve') {
          // On a un élève : on vérifie s'il existe dans la table 'eleves',
          // sur la base de son INE, ou nom et prénom.
          $sql_test = "SELECT * FROM eleves
                            WHERE (no_gep = '".$user['raw']['ine'][0]."'
                                OR (nom = '".$user['nom']."' AND prenom = '".$user['prenom']."'))";
                 
                $resultat = mysqli_query($mysqli, $sql);  
                $test = $resultat->num_rows;
                $resultat->close();
          
          if ($test == 0) {
            // L'élève n'existe pas du tout. On va donc le créer.
            $nouvel_eleve = new Eleve();
            $nouvel_eleve->setLogin($this->login);
            $nouvel_eleve->setNom($user['nom']);
            $nouvel_eleve->setPrenom($user['prenom']);
            $nouvel_eleve->setSexe($user['raw']['entpersonsexe'][0]);
            
            $naissance = $user['raw']['entpersondatenaissance'][0];
            if ($naissance != '') {
              $annee = mb_substr($naissance, 0, 4);
              $mois = mb_substr($naissance, 4, 2);
              $jour = mb_substr($naissance, 6, 2);
            } else {
              $annee = '0000';
              $mois = '00';
              $jour = '00';
            }
            
            $nouvel_eleve->setNaissance("$annee-$mois-$jour");
            $nouvel_eleve->setLieuNaissance('');
            $nouvel_eleve->setElenoet($user['raw']['employeenumber'][0] || '');
            $nouvel_eleve->setEreno('');
            $nouvel_eleve->setEleid($user['raw']['intid'][0] || '');
            $nouvel_eleve->setNoGep($user['raw']['ine'][0] || '');
            $nouvel_eleve->setEmail($user['email']);
            
            if (!$nouvel_eleve->save()) $errors = true;
            
            /*
             * Récupération des CLASSES de l'eleve :
             * Pour chaque eleve, on parcours ses classes, et on ne prend que celles
             * qui correspondent à la branche de l'établissement courant, et on les stocke
             */
            $nb_classes = $user['raw']['enteleveclasses']['count'];

            // Pour chaque classe trouvée..
            $eleve_added_to_classe = false;
            for ($cpt=0; $cpt<$nb_classes; $cpt++) {
                if ($eleve_added_to_classe) break;
                $classe_from_ldap = explode("$", $user['raw']['enteleveclasses'][$cpt]);
                // $classe_from_ldap[0] contient le DN de l'établissement
                // $classe_from_ldap[1] contient l'id de la classe
                $code_classe = $classe_from_ldap[1];

                // Si le SIREN de la classe trouvée correspond bien au SIREN de l'établissement courant,
                // on crée une entrée correspondante dans le tableau des classes disponibles
                // Sinon c'est une classe d'un autre établissement, on ne doit donc pas en tenir compte
                if (strcmp($classe_from_ldap[0], $ldap_server->get_base_branch()) == 0) {

                    /*
                     * On test si la classe que l'on souhaite ajouter existe déjà
                     * en la cherchant dans la base (
                     */
                    $classe_courante = ClasseQuery::create()
                          ->filterByClasse($code_classe)
                          ->findOne();

                    if ($classe_courante) {
                      
                      foreach($classe_courante->getPeriodeNotes() as $periode) {
                          // On associe l'élève à la classe
                          $sql_classe = "INSERT INTO j_eleves_classes SET
                              login = '".$this->login."', 
                              id_classe = '".$classe_courante->getId()."',
                              periode = '".$periode->getNumPeriode()."'";
                                  
                            $res = mysqli_query($mysqli, $sql); 
                            $res->close();
                          
                      } // Fin boucle périodes
                      $eleve_added_to_classe = true;
                    } // Fin test classe
                } //Fin du if classe appartient a l'etablissement courant
            } //Fin du parcours des classes de l'eleve

            
            // On a maintenant un élève en base, qui appartient à sa classe
            // pour toutes les périodes à partir de la période courante
            
            // On ne l'associe pas aux enseignements, car c'est un peu trop
            // risqué et bancal pour être réalisé dynamiquement ici, dans
            // la mesure où l'on n'a pas une information précise sur la
            // composition des groupes.
            
            
          } else {
            // L'élève existe déjà dans la base. On ne créé que l'utilisateur correspondant.
            // Pour ça, on va devoir s'assurer que l'identifiant est identique !
            $sql_login = "SELECT login FROM eleves
                            WHERE (no_gep = '".$user['raw']['ine'][0]."'
                                OR (nom = '".$user['nom']."' AND prenom = '".$user['prenom']."'))";
                    
                $resultat = mysqli_query($mysqli, $sql);
                $res = $resultatè->fetch_object();
                $test_login = $res->login;
                $resultat->close();
            
            
            if ($test_login != $this->login) {
              // Le login est différent, on ne peut rien faire... Il faudrait renommer
              // le login partout dans l'application, mais il n'existe pas de mécanisme
              // pour le faire de manière fiable.
              $errors = true;
            }
          }
          
        } elseif ($user['statut'] == 'responsable') {
          // Si on a un responsable, il faut l'associer à un élève
          
          $resp = new ResponsableEleve();
          $resp->setLogin($this->login);
          $resp->setNom($user['nom']);
          $resp->setPrenom($user['prenom']);
          $resp->setCivilite($user['raw']['personaltitle'][0]);
          $resp->setTelPers($user['raw']['homephone'][0]);
          $resp->setTelProf($user['raw']['telephonenumber'][0]);
          $resp->setTelPort($user['raw']['mobile'][0]);
          $resp->setMel($user['email']);
          $resp->setAdresseId($user['raw']['intid'][0]);
                    
          // On créé l'adresse associée
          
          $adr = new Adresse();
          $adr->setAdresseId($user['raw']['intid'][0]);
          $adr->setAdr1($user['raw']['entpersonadresse'][0]);
          $adr->setAdr2('');
          $adr->setAdr3('');
          $adr->setAdr4('');
          $adr->setCommune($user['raw']['entpersonville'][0]);
          $adr->setCp($user['raw']['entpersoncodepostal'][0]);
          $adr->setPays($user['raw']['entpersonpays'][0]);
          
          $resp->setAdresse($adr);
          
          $resp->save();

          $nb_eleves_a_charge = $user['raw']['entauxpersreleleveeleve']['count'];

          //pour chaque dn d'eleve
          for ($i=0;$i<$nb_eleves_a_charge;$i++) {
              $eleve_uid = explode(",",$user['raw']['entauxpersreleleveeleve'][$i]);
              $eleve_associe_login = mb_substr($eleve_uid[0], 4);
              $eleve_sql = "SELECT ele_id FROM eleves WHERE login = '$eleve_associe_login'";
                      
                    $eleve_query = mysqli_query($mysqli, $sql);
                    if ($eleve_query->num_rows == 1) {
                        $eleve_associe_obj = $eleve_query->fetch_object();
                        $eleve_associe_ele_id = $eleve_associe_obj->ele_id;
                        // Gepi donne un ordre aux responsables, il faut donc verifier combien de responsables sont deja enregistres pour l'eleve
                        // On initialise le numero de responsable
                        $numero_responsable = 1;
                        $req_nb_resp_deja_presents = "SELECT count(*) FROM responsables2 WHERE ele_id = '$eleve_associe_ele_id'";
                        $res_nb_resp = mysqli_query($mysqli, $req_nb_resp_deja_presents);
                        $nb_resp = $res_nb_resp->fetch_array($res_nb_resp);
                        if ($nb_resp[0] > 0) {
                            // Si deja 1 ou plusieurs responsables legaux pour cet eleve,on ajoute le nouveau responsable en incrementant son numero
                            $numero_responsable += $nb_resp[0];

                            //--
                            // TODO: tester si on a des adresses identiques, et n'utiliser qu'un seul objet adresse dans ce cas.
                            //--
                        }

                        // Ajout de la relation entre Responsable et Eleve dans la table "responsables2" pour chaque eleve
                        $req_ajout_lien_eleve_resp = "INSERT INTO responsables2 VALUES('$eleve_associe_ele_id','".$resp->getResponsableEleveId()."','$numero_responsable','')";
                        $insert_lien = mysqli_query($mysqli, $req_ajout_lien_eleve_resp);
                    }
            }
          
        } elseif ($user['statut'] == 'professeur') {
          // Rien de spécial à ce stade.
          
        } else {
          // Ici : que fait-on si l'on n'a pas un statut directement reconnu
          // et compatible Gepi ?
          // On applique le statut par défaut, configuré par l'admin.
          $user['statut'] = getSettingValue("statut_utilisateur_defaut");
        }
        
        // On créé l'utilisateur, s'il n'y a pas eu d'erreurs.
        if (!$errors) {
            $new_compte_utilisateur = new UtilisateurProfessionnel();
            $new_compte_utilisateur->setAuthMode('sso');
            $new_compte_utilisateur->setCivilite($user['civilite']);
            $new_compte_utilisateur->setEmail($user['email']);
            $new_compte_utilisateur->setEtat('actif');
            $new_compte_utilisateur->setLogin($this->login);
            $new_compte_utilisateur->setNom($user['nom']);
            $new_compte_utilisateur->setPrenom($user['prenom']);
            $new_compte_utilisateur->setShowEmail('no');
            $new_compte_utilisateur->setStatut($user['statut']);
            //$new_compte_utilisateur->save();
			if ($new_compte_utilisateur->save()) {
				return true;
			} else {
				return false;
			}
        }
        
			} else {
				return false;
			}
		}
	}

  # Mise à jour de quelques attributs de l'utilisateur à partir des attributs transmis
  # par CAS directement.
  private function update_user_with_cas_attributes(){
      global $mysqli;
    $need_update = false;
    if (isset($GLOBALS['debug_log_file'])){
    error_log("Mise à jour de l'utilisateur à partir des attributs CAS\n", 3, $GLOBALS['debug_log_file']);
    error_log("Attribut email :".$this->cas_extra_attributes['email']."\n", 3, $GLOBALS['debug_log_file']);
    error_log("Attribut prenom :".$this->cas_extra_attributes['prenom']."\n", 3, $GLOBALS['debug_log_file']);
    error_log("Attribut nom :".$this->cas_extra_attributes['nom']."\n", 3, $GLOBALS['debug_log_file']);
    }
    if (!empty($this->cas_extra_attributes)) {
      $query = 'UPDATE utilisateurs SET ';
      $first = true;
      foreach($this->cas_extra_attributes as $attribute => $value) {				
				// On compare la valeur envoyée avec la valeur présente dans Gepi
        if ($_SESSION[$attribute] != $value){
          $_SESSION[$attribute] = $value;
          $need_update = true;
          if (!$first) {
            $query .= ", ";
          }

          $query .= "$attribute = '$value'";
          $first = false;
        }
      }
      $query .= " WHERE login = '$this->login'";
			error_log("Détail requête : ".$query."\n", 3, $GLOBALS['debug_log_file']);
              
        if ($need_update) $res = mysqli_query($mysqli, $query);
      if ($need_update && $this->statut == 'eleve') {
        # On a eu une mise à jour qui concerne un élève, il faut synchroniser l'info dans la table eleves
        $sql = "UPDATE eleves, utilisateurs
                      SET eleves.nom = utilisateurs.nom,
                          eleves.prenom = utilisateurs.prenom,
                          eleves.email = utilisateurs.email
                      WHERE eleves.login = utilisateurs.login
                        AND utilisateurs.login = '".$this->login."'";  
        
            mysqli_query($mysqli, $sql);
      }
      return $res;
    }
  }



	# Cette méthode sert à forcer PHP et MySQL à utiliser un fuseau horaire
	# particulier.
	# Le fuseau horaire est simplement paramétré dans connect.inc.php,
	# en assignant $timezone.
	private function update_timezone($_timezone) {

	    # Mise à jour du fuseau horaire pour PHP
	    $update_timezone = date_default_timezone_set($_timezone);

	    # Mise à jour pour MySQL
	    if ($update_timezone) {

		# Il faut qu'on formatte le fuseau
		$timezone = new DateTimeZone(date_default_timezone_get());
		$time = new DateTime("now", $timezone);
		$offset = $timezone->getOffset($time);
		$offset_sign = $offset < 0 == "-" ? "-" : "+";
		$offset = abs($offset);
		$offset_hours = $offset / 3600 % 24;
		$offset_hours = mb_strlen($offset_hours) == '1' ? "0".$offset_hours : $offset_hours;
		$offset_minutes = $offset / 60 % 60;
		$offset_minutes = mb_strlen($offset_minutes) == '1' ? "0".$offset_minutes : $offset_minutes;
		$mysql_offset = $offset_sign . $offset_hours . ":" . $offset_minutes;
		$test = mysqli_query($GLOBALS["mysqli"], "SET time_zone = '".$mysql_offset."'");
	    }
	    return $update_timezone;
    }
    
  # Renvoie 'true' si l'accès à Gepi se fait en https
  static function https_request() {
  	if (!isset($_SERVER['HTTPS'])
    			OR (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != "on")
    			OR (isset($_SERVER['X-Forwaded-Proto']) AND $_SERVER['X-Forwaded-Proto'] != "https")) {
    	return false;
    } else {
      return true;
    }
  }

  # écrit dans un fichier un message de debug
  static private function debug_login_mdp($debug_test_mdp,$debug_test_mdp_file,$debug_test_mdp_message) {
	global $debug_login_nouveaux_comptes, $loguer_nouveau_login;

    if(($debug_test_mdp=="y")||
		(($debug_login_nouveaux_comptes=="y")&&($loguer_nouveau_login=="y"))) {
		$f_tmp=fopen($debug_test_mdp_file,"a+");
		fwrite($f_tmp,$debug_test_mdp_message);
		fclose($f_tmp);
    }
  }
}

?>
