<?php
/** Fonctions accessibles dans toutes les pages
 * 
 * 
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Initialisation
 * @subpackage general
 *
*/



/**
 * Fonctions de manipulation du gepi_alea contre les attaques CRSF
 * 
 * @see share-csrf.inc.php
 */
include_once dirname(__FILE__).'/share-csrf.inc.php';
/**
 * Fonctions qui produisent du code html
 * 
 * @see share-html.inc.php
 */
include_once dirname(__FILE__).'/share-html.inc.php';
/**
 * Fonctions de manipulation des conteneurs et des notes
 * 
 * @see share-notes.inc.php
 */
include_once dirname(__FILE__).'/share-notes.inc.php';
/**
 * Fonctions de manipulation des conteneurs et des notes
 * 
 * @see share-aid.inc.php
 */
include_once dirname(__FILE__).'/share-aid.inc.php';
/**
 * Fonctions de manipulation des conteneurs et des notes
 * 
 * @see share-pdf.inc.php
 */
include_once dirname(__FILE__).'/share-pdf.inc.php';








/**
 * Envoi d'un courriel
 *
 * @param string $sujet Le sujet du message
 * @param string $message Le message
 * @param string $destinataire Le destinataire
 * @param string $ajout_headers Text à ajouter dans le header
 */
function envoi_mail($sujet, $message, $destinataire, $ajout_headers='') {

	$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";

	if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}

  $subject = $gepiPrefixeSujetMail."GEPI : $sujet";
  $subject = "=?UTF-8?B?".base64_encode($subject)."?=\r\n";
  
  $headers = "X-Mailer: PHP/" . phpversion()."\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/plain; charset=UTF-8\r\n";
  $headers .= "From: Mail automatique Gepi <ne-pas-repondre@".$_SERVER['SERVER_NAME'].">\r\n";
  $headers .= $ajout_headers;

	// On envoie le mail
	$envoi = mail($destinataire,
		$subject,
		$message,
	  $headers);
}

/**
 * Verification de la validité d'un mot de passe
 * 
 * longueur : getSettingValue("longmin_pwd") minimum
 * 
 * composé de lettres et d'au moins un chiffre
 *

 * @param string $password Mot de passe
 * @param boolean $flag Si $flag = 1, il faut également au moins un caractères spécial
 * @return boolean TRUE si le mot de passe est valable
 * @see getSettingValue()
 */
function verif_mot_de_passe($password,$flag) {
	global $info_verif_mot_de_passe;

	if ($flag == 1) {
		if(preg_match("/(^[a-zA-Z]*$)|(^[0-9]*$)/", $password)) {
			$info_verif_mot_de_passe="Le mot de passe ne doit pas être uniquement numérique ou uniquement alphabétique.";
			return FALSE;
		}
		elseif(preg_match("/^[[:alnum:]\W]{".getSettingValue("longmin_pwd").",}$/", $password) and preg_match("/[\W]+/", $password) and preg_match("/[0-9]+/", $password)) {
			$info_verif_mot_de_passe="";
			return TRUE;
		}
		else {
			if(preg_match("/^[A-Za-z0-9]*$/", $password)) {
				$info_verif_mot_de_passe="Le mot de passe doit comporter au moins un caractère spécial (#, *,...).";
			}
			elseif (mb_strlen($password) < getSettingValue("longmin_pwd")) {
				$info_verif_mot_de_passe="La longueur du mot de passe doit être supérieure ou égale à ".getSettingValue("longmin_pwd").".";
				return FALSE;
			}
			else {
				// Euh... qu'est-ce qui a été saisi?
				$info_verif_mot_de_passe="";
			}
			return FALSE;
		}
	}
	else {
		if(preg_match("/(^[a-zA-Z]*$)|(^[0-9]*$)/", $password)) {
			$info_verif_mot_de_passe="Le mot de passe ne doit pas être uniquement numérique ou uniquement alphabétique.";
			return FALSE;
		}
		elseif (mb_strlen($password) < getSettingValue("longmin_pwd")) {
			$info_verif_mot_de_passe="La longueur du mot de passe doit être supérieure ou égale à ".getSettingValue("longmin_pwd").".";
			return FALSE;
		}
		else {
			$info_verif_mot_de_passe="";
			return TRUE;
		}
	}
}

/**
 * Teste si le login existe déjà dans la base
 *
 * @param string $s le login testé
 * @return string yes si le login existe, no sinon
 */
function test_unique_login($s) {
    // On vérifie que le login ne figure pas déjà dans la base utilisateurs
    $test1 = mysql_num_rows(mysql_query("SELECT login FROM utilisateurs WHERE (login='$s' OR login='".my_strtoupper($s)."')"));
    if ($test1 != "0") {
        return 'no';
    } else {
        $test2 = mysql_num_rows(mysql_query("SELECT login FROM eleves WHERE (login='$s' OR login = '".my_strtoupper($s)."')"));
        if ($test2 != "0") {
            return 'no';
        } else {
			$test3 = mysql_num_rows(mysql_query("SELECT login FROM resp_pers WHERE (login='$s' OR login='".my_strtoupper($s)."')"));
			if ($test3 != "0") {
				return 'no';
			} else {
	            return 'yes';
	        }
        }
    }
}

/**
 * Vérifie l'unicité du login
 * 
 * On vérifie que le login ne figure pas déjà dans une des bases élève des années passées 
 *
 * @param string $s le login à vérifier
 * @param <type> $indice ??
 * @return string yes si le login existe, no sinon
 */
function test_unique_e_login($s, $indice) {
    // On vérifie que le login ne figure pas déjà dans la base utilisateurs
    //$test7 = mysql_num_rows(mysql_query("SELECT login FROM utilisateurs WHERE (login='$s' OR login='".strtoupper($s)."')"));
    //if ($test7 != "0") {

    $test7 = test_unique_login($s);
    if ($test7 == "no") {
        // Si le login figure déjà dans une des bases élève des années passées ou bien
        // dans la base utilisateurs, on retourne 'no' !
        return 'no';
    } else {
        // Si le login ne figure pas dans une des bases élève des années passées ni dans la base
        // utilisateurs, on vérifie qu'un même login ne vient pas d'être attribué !
        $test_tempo2 = mysql_num_rows(mysql_query("SELECT col2 FROM tempo2 WHERE (col2='$s' or col2='".my_strtoupper($s)."')"));
        if ($test_tempo2 != "0") {
            return 'no';
        } else {
            $reg = mysql_query("INSERT INTO tempo2 VALUES ('$indice', '$s')");
            return 'yes';
        }
    }
}

/**
 * Génére le login à partir du nom et du prénom
 * 
 * Génère puis nettoie un login pour qu'il soit valide et unique
 * 
 * Le mode de génération doit être passé en argument
 * 
 * cf. http://sacoche.sesamath.net/appel_doc.php?fichier=support_administrateur__gestion_format_logins
 * 
 * @param string $_nom nom de l'utilisateur
 * @param string $_prenom prénom de l'utilisateur
 * @param string $_mode Le mode de génération ou NULL
 * @param string $_casse La casse du login ('maj', 'min') est par défaut en minuscules
 * @return string|boolean Le login généré ou false si on obtient un login vide
 * @see test_unique_login()
 */
function generate_unique_login($_nom, $_prenom, $_mode, $_casse='min') {

	if(($_mode == NULL)||(!check_format_login($_mode))) {
		$_mode = "nnnnnnnnnnnnnnnnnnnn";
	}

	//==========================
	// Nettoyage des caractères du nom et du prénom

	$_prenom = remplace_accents(preg_replace("/Æ/","AE",preg_replace("/æ/","ae",preg_replace("/Œ/","OE",preg_replace("/œ/","oe",$_prenom)))));

	$prenoms = explode(" ",$_prenom);
	$premier_prenom = $prenoms[0];
	$prenom_compose = '';
	if (isset($prenoms[1])) {$prenom_compose = $prenoms[0]."-".$prenoms[1];}

	$_prenom = preg_replace("/[^a-zA-Z.\-]/", "", $_prenom);

	$_nom = remplace_accents(preg_replace("/Æ/","AE",preg_replace("/æ/","ae",preg_replace("/Œ/","OE",preg_replace("/œ/","oe",$_nom)))));
	$_nom = preg_replace("/[^a-zA-Z.\-]/", "", $_nom);

	//==========================
	// Nettoyage historique... éventuellement à revoir

	$_nom = preg_replace("/[ ']/","", $_nom);
	$_nom = preg_replace("/-/","_", $_nom);

	$_prenom = preg_replace("/[ ']/","", $_prenom);
	$_prenom = preg_replace("/-/","_", $_prenom);

	//==========================
	// On génère le login

	if((preg_match('/n/', $_mode))&&($_nom=="")) {return false;}
	elseif((preg_match('/p/', $_mode))&&($_prenom=="")) {return false;}
	else {
		$nb_n=mb_strlen(preg_replace('/[^n]/', '', $_mode));
		$nb_p=mb_strlen(preg_replace('/[^p]/', '', $_mode));
		$separateur=preg_replace('/[^._-]/', '', $_mode);
		//echo "<br />";
		//echo "\$_prenom=$_prenom<br />";
		//echo "\$_nom=$_nom<br />";

		$part_prenom=mb_substr($_prenom,0,min($nb_p,mb_strlen($_prenom)));
		$part_nom=mb_substr($_nom,0,min($nb_n,mb_strlen($_nom)));

		//echo "\$part_prenom=$part_prenom<br />";
		//echo "\$part_nom=$part_nom<br />";

		if(preg_match('/^p/', $_mode)) {
			// C'est un mode commençant par une portion de prénom
			$temp1=$part_prenom.$separateur.$part_nom;
		}
		else {
			// C'est un mode commençant par une portion de nom
			$temp1=$part_nom.$separateur.$part_prenom;
		}
		//echo "\$temp1=$temp1<br />";

		// Révision de la casse
		if($_casse=='maj') {
			$temp1=my_strtoupper($temp1);
		}
		//elseif($_casse=='min') {
		else {
			$temp1=my_strtolower($temp1);
		}

		// Suppression des _,-,. multiples
		$temp1=preg_replace("/_{2,}/", "_", $temp1);
		$temp1=preg_replace("/\.{2,}/", ".", $temp1);
		$temp1=preg_replace("/\-{2,}/", "-", $temp1);

		$login_user = $temp1;

		//==========================
		// Nettoyage final
		$login_user = mb_substr($login_user, 0, 50);
		$login_user = preg_replace("/[^A-Za-z0-9._\-]/","",trim($login_user));

		$test1 = $login_user{0};
		while ($test1 == "_" OR $test1 == "-" OR $test1 == ".") {
			$login_user = mb_substr($login_user, 1);
			$test1 = $login_user{0};
		}
	
		$test1 = $login_user{mb_strlen($login_user)-1};
		while ($test1 == "_" OR $test1 == "-" OR $test1 == ".") {
			$login_user = mb_substr($login_user, 0, mb_strlen($login_user)-1);
			$test1 = $login_user{mb_strlen($login_user)-1};
		}

		//==========================
		// On teste l'unicité du login que l'on vient de créer
		$m = '';
		$test_unicite = 'no';
		while ($test_unicite != 'yes') {
			if(($m!='') &&($m>99)) {
				$login_user=false;
				break;
			}
			else {
				$test_unicite = test_unique_login($login_user.$m);
				if ($test_unicite != 'yes') {
					if ($m == '') {
						$m = 2;
					} else {
						$m++;
					}
				} else {
					$login_user = $login_user.$m;
				}
			}
		}

		//echo "\$login_user=$login_user<br />";

		return $login_user;
	}
}

/**
 * Génére le login à partir du nom et du prénom
 * 
 * Génère puis nettoie un login pour qu'il soit valide et unique
 * 
 * Le mode de génération doit être passé en argument
 * 
 * name             à partir du nom
 * 
 * name8            à partir du nom, réduit à 8 caractères
 * 
 * name9_p          à partir du nom, réduit à 9 caractères + _ + première lettre du prénom (format historique du login élève dans Gepi)
 * 
 * fname8           première lettre du prénom + nom, réduit à 8 caractères
 * 
 * fname19          première lettre du prénom + nom, réduit à 19 caractères
 * 
 * firstdotname     prénom.nom
 * 
 * firstdotname19   prénom.nom réduit à 19 caractères
 * 
 * namef8           nom réduit à 7 caractères + première lettre du prénom
 * 
 * lcs              première lettre du prénom + premier nom (+ _ + deuxième nom si le 1er nom fait moins de 4 caractères)
 * 
 * si $_mode est NULL, fname8 est utilisé
 * 
 * @param string $_nom nom de l'utilisateur
 * @param string $_prenom prénom de l'utilisateur
 * @param string $_mode Le mode de génération ou NULL
 * @param string $_casse La casse du login ('maj', 'min', '') par défaut la casse n'est pas modifiée
 * @return string|booleanLe login généré ou FALSE si on obtient un login vide
 * @see test_unique_login()
 */
function generate_unique_login_old($_nom, $_prenom, $_mode, $_casse='') {

	if ($_mode == NULL) {
		$_mode = "fname8";
	}
	// On génère le login
	$_prenom = remplace_accents($_prenom);

	$prenoms = explode(" ",$_prenom);
	$premier_prenom = $prenoms[0];
	$prenom_compose = '';
	if (isset($prenoms[1])) {$prenom_compose = $prenoms[0]."-".$prenoms[1];}

	$_prenom = preg_replace("/[^a-zA-Z.\-]/", "", $_prenom);

	$_nom = remplace_accents($_nom);
	$_nom = preg_replace("/[^a-zA-Z.\-]/", "", $_nom);

	if($_nom=='') {return FALSE;}

	if ($_mode == "name") {
		$temp1 = $_nom;
		$temp1 = preg_replace("/ /","", $temp1);
		$temp1 = preg_replace("/-/","_", $temp1);
		$temp1 = preg_replace("/'/","", $temp1);
	} elseif ($_mode == "name8") {
		$temp1 = $_nom;
		$temp1 = preg_replace("/ /","", $temp1);
		$temp1 = preg_replace("/-/","_", $temp1);
		$temp1 = preg_replace("/'/","", $temp1);
		$temp1 = mb_substr($temp1,0,8);
	} elseif ($_mode == "name9_p") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/","", $temp1);
		$temp1 = mb_substr($temp1,0,9);
		if($_prenom!='') {
			$temp2 = preg_replace("/ /","", $_prenom);
			$temp2 = preg_replace("/-/","_", $temp2);
			$temp2 = preg_replace("/'/","", $temp2);
			if($temp2!='') {
				$temp1 .= '_'.mb_substr($temp2,0,1);
			}
		}
	} elseif ($_mode == "name9-p") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/","", $temp1);
		$temp1 = mb_substr($temp1,0,9);
		if($_prenom!='') {
			$temp2 = preg_replace("/ /","", $_prenom);
			$temp2 = preg_replace("/-/","_", $temp2);
			$temp2 = preg_replace("/'/","", $temp2);
			if($temp2!='') {
				$temp1 .= '-'.mb_substr($temp2,0,1);
			}
		}
	} elseif ($_mode == "name9.p") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/","", $temp1);
		$temp1 = mb_substr($temp1,0,9);
		if($_prenom!='') {
			$temp2 = preg_replace("/ /","", $_prenom);
			$temp2 = preg_replace("/-/","_", $temp2);
			$temp2 = preg_replace("/'/","", $temp2);
			if($temp2!='') {
				$temp1 .= '.'.mb_substr($temp2,0,1);
			}
		}
	} elseif ($_mode == "p_name9") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/","", $temp1);
		$temp1 = mb_substr($temp1,0,9);
		if($_prenom!='') {
			$temp2 = preg_replace("/ /","", $_prenom);
			$temp2 = preg_replace("/-/","_", $temp2);
			$temp2 = preg_replace("/'/","", $temp2);
			if($temp2!='') {
				$temp1 = mb_substr($temp2,0,1)."_".$temp1;
			}
		}
	} elseif ($_mode == "p-name9") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/","", $temp1);
		$temp1 = mb_substr($temp1,0,9);
		if($_prenom!='') {
			$temp2 = preg_replace("/ /","", $_prenom);
			$temp2 = preg_replace("/-/","_", $temp2);
			$temp2 = preg_replace("/'/","", $temp2);
			if($temp2!='') {
				$temp1 = mb_substr($temp2,0,1)."-".$temp1;
			}
		}
	} elseif ($_mode == "p.name9") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/","", $temp1);
		$temp1 = mb_substr($temp1,0,9);
		if($_prenom!='') {
			$temp2 = preg_replace("/ /","", $_prenom);
			$temp2 = preg_replace("/-/","_", $temp2);
			$temp2 = preg_replace("/'/","", $temp2);
			if($temp2!='') {
				$temp1 = mb_substr($temp2,0,1).".".$temp1;
			}
		}
	} elseif ($_mode == "name9_ppp") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/","", $temp1);
		$temp1 = mb_substr($temp1,0,9);
		if($_prenom!='') {
			$temp2 = preg_replace("/ /","", $_prenom);
			$temp2 = preg_replace("/-/","_", $temp2);
			$temp2 = preg_replace("/'/","", $temp2);
			if($temp2!='') {
				$temp1 .= '_'.mb_substr($temp2,0,3);
			}
		}
	} elseif ($_mode == "name9-ppp") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/","", $temp1);
		$temp1 = mb_substr($temp1,0,9);
		if($_prenom!='') {
			$temp2 = preg_replace("/ /","", $_prenom);
			$temp2 = preg_replace("/-/","_", $temp2);
			$temp2 = preg_replace("/'/","", $temp2);
			if($temp2!='') {
				$temp1 .= '-'.mb_substr($temp2,0,3);
			}
		}
	} elseif ($_mode == "name9.ppp") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/","", $temp1);
		$temp1 = mb_substr($temp1,0,9);
		if($_prenom!='') {
			$temp2 = preg_replace("/ /","", $_prenom);
			$temp2 = preg_replace("/-/","_", $temp2);
			$temp2 = preg_replace("/'/","", $temp2);
			if($temp2!='') {
				$temp1 .= '.'.mb_substr($temp2,0,3);
			}
		}
	} elseif ($_mode == "ppp_name9") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/","", $temp1);
		$temp1 = mb_substr($temp1,0,9);
		if($_prenom!='') {
			$temp2 = preg_replace("/ /","", $_prenom);
			$temp2 = preg_replace("/-/","_", $temp2);
			$temp2 = preg_replace("/'/","", $temp2);
			if($temp2!='') {
				$temp1 = mb_substr($temp2,0,3)."_".$temp1;
			}
		}
	} elseif ($_mode == "ppp-name9") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/","", $temp1);
		$temp1 = mb_substr($temp1,0,9);
		if($_prenom!='') {
			$temp2 = preg_replace("/ /","", $_prenom);
			$temp2 = preg_replace("/-/","_", $temp2);
			$temp2 = preg_replace("/'/","", $temp2);
			if($temp2!='') {
				$temp1 = mb_substr($temp2,0,3)."-".$temp1;
			}
		}
	} elseif ($_mode == "ppp.name9") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/","", $temp1);
		$temp1 = mb_substr($temp1,0,9);
		if($_prenom!='') {
			$temp2 = preg_replace("/ /","", $_prenom);
			$temp2 = preg_replace("/-/","_", $temp2);
			$temp2 = preg_replace("/'/","", $temp2);
			if($temp2!='') {
				$temp1 = mb_substr($temp2,0,3).".".$temp1;
			}
		}
	} elseif ($_mode == "fname8") {
		if($_prenom=='') {return FALSE;}
		$temp1 = $_prenom{0} . $_nom;
		$temp1 = preg_replace("/ /","", $temp1);
		$temp1 = preg_replace("/-/","_", $temp1);
		$temp1 = preg_replace("/'/","", $temp1);
		$temp1 = mb_substr($temp1,0,8);
	} elseif ($_mode == "fname19") {
		if($_prenom=='') {return FALSE;}
		$temp1 = $_prenom{0} . $_nom;
		$temp1 = preg_replace("/ /","", $temp1);
		$temp1 = preg_replace("/-/","_", $temp1);
		$temp1 = preg_replace("/'/","", $temp1);
		$temp1 = mb_substr($temp1,0,19);
	} elseif ($_mode == "firstdotname") {
		if($_prenom=='') {return FALSE;}

		if ($prenom_compose != '') {
			$firstname = $prenom_compose;
		} else {
			$firstname = $premier_prenom;
		}

		//$temp1 = $_prenom . "." . $_nom;
		$temp1 = $firstname . "." . $_nom;

		$temp1 = preg_replace("/ /","", $temp1);
		$temp1 = preg_replace("/-/","_", $temp1);
		$temp1 = preg_replace("/'/","", $temp1);
	} elseif ($_mode == "firstdotname19") {
		if($_prenom=='') {return FALSE;}

		if ($prenom_compose != '') {
			$firstname = $prenom_compose;
		} else {
			$firstname = $premier_prenom;
		}

		//$temp1 = $_prenom . "." . $_nom;
		$temp1 = $firstname . "." . $_nom;

		$temp1 = preg_replace("/ /","", $temp1);
		$temp1 = preg_replace("/'/","", $temp1);
		$temp1 = mb_substr($temp1,0,19);
	} elseif ($_mode == "namef8") {
		if($_prenom=='') {return FALSE;}
		$temp1 =  mb_substr($_nom,0,7) . $_prenom{0};
		$temp1 = preg_replace("/ /","", $temp1);
		$temp1 = preg_replace("/-/","_", $temp1);
		$temp1 = preg_replace("/'/","", $temp1);
	} elseif ($_mode == "lcs") {
		$temp1 = my_strtolower($_nom);
		if (preg_match("/\s/",$temp1)) {
			$noms = preg_split("/\s/",$temp1);
			$temp1 = $noms[0];
			if (mb_strlen($noms[0]) < 4) {
				$temp1 .= "_". $noms[1];
			}
		}
		$temp1 = my_strtolower(mb_substr($_prenom,0,1)). $temp1;
	} else {
		return FALSE;
	}

	if($_casse=='maj') {
		$temp1=my_strtoupper($temp1);
	}
	elseif($_casse=='min') {
		$temp1=my_strtolower($temp1);
	}

	$login_user = $temp1;

	// Nettoyage final
	$login_user = mb_substr($login_user, 0, 50);
	$login_user = preg_replace("/[^A-Za-z0-9._\-]/","",trim($login_user));

	$test1 = $login_user{0};
	while ($test1 == "_" OR $test1 == "-" OR $test1 == ".") {
		$login_user = mb_substr($login_user, 1);
		$test1 = $login_user{0};
	}

	$test1 = $login_user{mb_strlen($login_user)-1};
	while ($test1 == "_" OR $test1 == "-" OR $test1 == ".") {
		$login_user = mb_substr($login_user, 0, mb_strlen($login_user)-1);
		$test1 = $login_user{mb_strlen($login_user)-1};
	}

	// On teste l'unicité du login que l'on vient de créer
	$m = '';
	$test_unicite = 'no';
	while ($test_unicite != 'yes') {
		$test_unicite = test_unique_login($login_user.$m);
		if ($test_unicite != 'yes') {
			if ($m == '') {
				$m = 2;
			} else {
				$m++;
			}
		} else {
			$login_user = $login_user.$m;
		}
	}

	return $login_user;
}

/**
 * Fonction qui propose l'ordre d'affichage du nom, prénom et de la civilité en fonction des réglages de la classe de l'élève
 *
 * @param string $login login de l'utilisateur
 * @param integer $id_classe Id de la classe
 * @return string nom, prénom, civilité formaté
 */
function affiche_utilisateur($login,$id_classe) {
	$req = mysql_query("select nom, prenom, civilite from utilisateurs where login = '".$login."'");
	$nom = @mysql_result($req, 0, 'nom');
	$prenom = @mysql_result($req, 0, 'prenom');
	$civilite = @mysql_result($req, 0, 'civilite');
	$req_format = mysql_query("select format_nom from classes where id = '".$id_classe."'");
	if(mysql_num_rows($req_format)>0) {
		$format = mysql_result($req_format, 0, 'format_nom');
		$result = "";
		$i='';
		if ((($format == 'ni') OR ($format == 'in') OR ($format == 'cni') OR ($format == 'cin')) AND ($prenom != '')) {
			$temp = explode("-", $prenom);
			$i = mb_substr($temp[0], 0, 1);
			if (isset($temp[1]) and ($temp[1] != '')) $i .= "-".mb_substr($temp[1], 0, 1);
			$i .= ". ";
		}
	}
	else {
		$format="";
	}

	$result="";
	switch( $format ) {
		case 'np':
			$result = $nom." ".$prenom;
			break;
		case 'pn':
			$result = $prenom." ".$nom;
			break;
		case 'in':
			$result = $i.$nom;
			break;
		case 'ni':
			$result = $nom." ".$i;
			break;
		case 'cnp':
			if ($civilite != '') $result = $civilite." ";
			$result .= $nom." ".$prenom;
			break;
		case 'cpn':
			if ($civilite != '') $result = $civilite." ";
			$result .= $prenom." ".$nom;
			break;
		case 'cin':
			if ($civilite != '') $result = $civilite." ";
			$result .= $i.$nom;
			break;
		case 'cni':
			if ($civilite != '') $result = $civilite." ";
			$result .= $nom." ".$i;
			break;
		case 'cn':
			if ($civilite != '') $result = $civilite." ";
			$result .= $nom;
			break;
		default:
			$result = $nom." ".$prenom;
	}

	return $result;
}

/**
 * Verifie si l'extension d_base est active
 *
 * Affiche une page d'avertissement si le module dbase n'est pas actif
 * 
 */
function verif_active_dbase() {
    if (!function_exists("dbase_open"))  {
        echo "<center><p class=grand>ATTENTION : PHP n'est pas configuré pour gérer les fichiers GEP (dbf).
        <br />L'extension d_base n'est pas active. Adressez-vous à l'administrateur du serveur pour corriger le problème.</p></center></body></html>";
        die();
    }
}

/**
 * Ecrit une balise <select> de date jour mois année
 * correction W3C : ajout de la balise de fin </option> à la fin de $out_html
 * Création d'un label pour passer les tests WAI
 *
 * @param string $prefix l'attribut name sera de la forme $prefixday, $prefixMois,...
 * @param integer $day
 * @param integer $month
 * @param integer $year
 * @param string $option Si = more_years, on ajoute +5 et -5 années aux années possibles
 * @see getSettingValue()
 */
function genDateSelector($prefix, $day, $month, $year, $option)
{
    if($day   == 0) $day = date("d");
    if($month == 0) $month = date("m");
    if($year  == 0) $year = date("Y");

	 echo "\n<label for=\"${prefix}jour\"><span style='display:none;'>Jour</span></label>\n";
    echo "<select id=\"${prefix}jour\" name=\"${prefix}day\">\n";

    for($i = 1; $i <= 31; $i++)
        echo "<option value = \"$i\"" . ($i == $day ? " selected=\"selected\"" : "") . ">$i</option>\n";

    echo "</select>\n";

	 echo "\n<label for=\"${prefix}mois\"><span style='display:none;'>Mois</span></label>\n";
    echo "<select id=\"${prefix}mois\" name=\"${prefix}month\">\n";

    for($i = 1; $i <= 12; $i++)
    {
        $m = strftime("%b", mktime(0, 0, 0, $i, 1, $year));

        echo "<option value=\"$i\"" . ($i == $month ? " selected=\"selected\"" : "") . ">$m</option>\n";
    }

    echo "</select>\n";

	 echo "\n<label for=\"${prefix}annee\"><span style='display:none;'>Année</span></label>\n";
    echo "<select id=\"${prefix}annee\" name=\"${prefix}year\">\n";

    $min = strftime("%Y", getSettingValue("begin_bookings"));
    if ($option == "more_years") $min = date("Y") - 5;

    $max = strftime("%Y", getSettingValue("end_bookings"));
    if ($option == "more_years") $max = date("Y") + 5;

    for($i = $min; $i <= $max; $i++)
        print "<option" . ($i == $year ? " selected=\"selected\"" : "") . ">$i</option>\n";
    
    echo "</select>\n";
}

/**
 * Vérifie que la page est bien accessible par l'utilisateur
 *
 * @global string 
 * @return booleanTRUE si la page est accessible, FALSE sinon
 * @see tentative_intrusion()
 */
function checkAccess() {
    global $gepiPath;
    $url = parse_url($_SERVER['SCRIPT_NAME']);
    if ($_SESSION["statut"] == 'autre') {

    	$sql = "SELECT autorisation
	    from droits_speciaux
    	where nom_fichier = '" . mb_substr($url['path'], mb_strlen($gepiPath)) . "'
		AND id_statut = '" . $_SESSION['statut_special_id'] . "'";

    }else{

		$sql = "select " . $_SESSION['statut'] . "
	    from droits
    	where id = '" . mb_substr($url['path'], mb_strlen($gepiPath)) . "'
    	;";

	}

    $dbCheckAccess = sql_query1($sql);
    if (mb_substr($url['path'], 0, mb_strlen($gepiPath)) != $gepiPath) {
        tentative_intrusion(2, "Tentative d'accès avec modification sauvage de gepiPath");
        return (FALSE);
    } else {
/*
$f=fopen("/tmp/debug_acces.txt", "a+");
fwrite($f, strftime("%Y%m%d %H:%M:%S")." : Accès de ".$_SESSION['login']." à ".$url['path']." : $dbCheckAccess\n");
fclose($f);
*/
        if ($dbCheckAccess == 'V') {
            return (TRUE);
        } else {
            tentative_intrusion(1, "Tentative d'accès à un fichier sans avoir les droits nécessaires");
            return (FALSE);
        }
    }
}

/**
 * Recherche dans la base l'adresse courriel d'un utilisateur
 *
 * @param string $login_u Login de l'utilisateur
 * @return string adresse courriel de l'utilisateur
 */
function retourne_email ($login_u) {
$call = mysql_query("SELECT email FROM utilisateurs WHERE login = '$login_u'");
$email = @mysql_result($call, 0, "email");
return $email;

}

/**
 * Renvoie une chaine débarassée de l'encodage ASCII
 *
 * @param string $s le texte à convertir
 * @return string le texte avec les lettres accentuées
 */
function dbase_filter($s){
  for($i = 0; $i < mb_strlen($s); $i++){
    $code = ord($s[$i]);
    switch($code){
    case 129:    $s[$i] = "ü"; break;
    case 130:   $s[$i] = "é"; break;
    case 131:    $s[$i] = "â"; break;
    case 132:    $s[$i] = "ä"; break;
    case 133:    $s[$i] = "à"; break;
    case 135:    $s[$i] = "ç"; break;
    case 136:    $s[$i] = "ê"; break;
    case 137:    $s[$i] = "ë"; break;
    case 138:    $s[$i] = "è"; break;
    case 139:    $s[$i] = "ï"; break;
    case 140:    $s[$i] = "î"; break;
    case 147:    $s[$i] = "ô"; break;
    case 148:    $s[$i] = "ö"; break;
    case 150:    $s[$i] = "û"; break;
    case 151:    $s[$i] = "ù"; break;
    }
  }
  return $s;
}

/**
 * Renvoie le navigateur et sa version
 *
 * @param string $HTTP_USER_AGENT
 * @return string navigateur - version
 */
function detect_browser($HTTP_USER_AGENT) {
	// D'après le fichier db_details_common.php de phpmyadmin
	/*
	$f=fopen("/tmp/detect_browser.txt","a+");
	fwrite($f,date("d/m/Y His").": $HTTP_USER_AGENT\n");
	fclose($f);
	*/
  include_once(dirname(__FILE__).'/HTMLPurifier.standalone.php');
  $config = HTMLPurifier_Config::createDefault();
  $config->set('Core.Encoding', 'utf-8'); // replace with your encoding
  $config->set('HTML.Doctype', 'XHTML 1.0 Strict'); // replace with your doctype
  $purifier = new HTMLPurifier($config);
    
  
	if(function_exists('preg_match')) {
		if (preg_match('/Opera(\/| )([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'OPERA';
		} elseif(preg_match('/MSIE ([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'Internet Explorer';
		} elseif(preg_match('/OmniWeb\/([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'OMNIWEB';
		} elseif(preg_match('/(Konqueror\/)(.*)(;)/', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'KONQUEROR';
		} elseif(preg_match('/Mozilla\/([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version)) {
			if(preg_match('/Chrome\/([0-9.]*)/', $HTTP_USER_AGENT, $log_version2)) {
				$BROWSER_VER = $log_version2[1];
				$BROWSER_AGENT = 'GoogleChrome';
			} elseif(preg_match('/Safari\/([0-9]*)/', $HTTP_USER_AGENT, $log_version2)) {
				$BROWSER_VER = $log_version[1] . '.' . $log_version2[1];
				$BROWSER_AGENT = 'SAFARI';
			} elseif(preg_match('/Firefox\/([0-9.]*)/', $HTTP_USER_AGENT, $log_version2)) {
				$BROWSER_VER = $log_version2[1];
				$BROWSER_AGENT = 'Firefox';
			} else {
				$BROWSER_VER = $log_version[1];
				$BROWSER_AGENT = 'MOZILLA';
			}
		} else {
			$BROWSER_VER = '';
			$BROWSER_AGENT = $purifier->purify($HTTP_USER_AGENT);
		}
	}
	elseif(function_exists('mb_ereg')) {
		if (mb_ereg('Opera(/| )([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'OPERA';
		} elseif(mb_ereg('MSIE ([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'Internet Explorer';
		} elseif(mb_ereg('OmniWeb/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'OMNIWEB';
		} elseif(mb_ereg('(Konqueror/)(.*)(;)', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'KONQUEROR';
		} elseif((mb_ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))&&(mb_ereg('GoogleChrome/([0-9.]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version2[1];
			$BROWSER_AGENT = 'GoogleChrome';
		} elseif((mb_ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))&&(mb_ereg('Safari/([0-9]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version[1] . '.' . $log_version2[1];
			$BROWSER_AGENT = 'SAFARI';
		} elseif((mb_ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))&&(mb_ereg('Firefox/([0-9.]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version2[1];
			$BROWSER_AGENT = 'Firefox';
		} elseif(mb_ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'MOZILLA';
		} else {
			$BROWSER_VER = '';
			$BROWSER_AGENT = $purifier->purify($HTTP_USER_AGENT);
		}
	}
	elseif(function_exists('ereg')) {
		if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'OPERA';
		} elseif(ereg('MSIE ([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'Internet Explorer';
		} elseif(ereg('OmniWeb/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'OMNIWEB';
		} elseif(ereg('(Konqueror/)(.*)(;)', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'KONQUEROR';
		} elseif((ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))&&(ereg('GoogleChrome/([0-9.]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version2[1];
			$BROWSER_AGENT = 'GoogleChrome';
		} elseif((ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))&&(ereg('Safari/([0-9]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version[1] . '.' . $log_version2[1];
			$BROWSER_AGENT = 'SAFARI';
		} elseif(ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'MOZILLA';
		} elseif((ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))&&(ereg('Firefox/([0-9.]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version2[1];
			$BROWSER_AGENT = 'Firefox';
		} else {
			$BROWSER_VER = '';
			$BROWSER_AGENT = $purifier->purify($HTTP_USER_AGENT);
		}
	}
	else {
		$BROWSER_VER = '';
		$BROWSER_AGENT = $purifier->purify($HTTP_USER_AGENT);
	}
	return  $BROWSER_AGENT." - ".$BROWSER_VER;
}

/**
 * Formate une date en jour/mois/année
 * 
 * Accepte les dates aux formats YYYY-MM-DD ou YYYYMMDD ou YYYY-MM-DD xx:xx:xx
 * 
 * Retourne la date passée en argument si le format n'est pas bon
 *
 * @param date $date La date à formater
 * @return string la date formatée
 */
function affiche_date_naissance($date) {
    if (mb_strlen($date) == 10) {
        // YYYY-MM-DD
        $annee = mb_substr($date, 0, 4);
        $mois = mb_substr($date, 5, 2);
        $jour = mb_substr($date, 8, 2);
    }
    elseif (mb_strlen($date) == 8 ) {
        // YYYYMMDD
        $annee = mb_substr($date, 0, 4);
        $mois = mb_substr($date, 4, 2);
        $jour = mb_substr($date, 6, 2);
    }
    elseif (mb_strlen($date) == 19 ) {
        // YYYY-MM-DD xx:xx:xx
        $annee = mb_substr($date, 0, 4);
        $mois = mb_substr($date, 5, 2);
        $jour = mb_substr($date, 8, 2);
    }

    else {
        // Format inconnu
        return($date);
    }
    return $jour."/".$mois."/".$annee ;
}

/**
 *
 * @global mixed 
 * @global mixed 
 * @global mixed 
 * @return boolean TRUE si on a une nouvelle version 
 */
function test_maj() {
    global $gepiVersion, $gepiRcVersion, $gepiBetaVersion;
    $version_old = getSettingValue("version");
    $versionRc_old = getSettingValue("versionRc");
    $versionBeta_old = getSettingValue("versionBeta");

    if ($version_old =='') {
       return TRUE;
       die();
   }
   if ($gepiVersion > $version_old) {
        // On a une nouvelle version stable
       return TRUE;
       die();
   }
   if (($gepiVersion == $version_old) and ($versionRc_old!='')) {
        // On avait une RC
       if (($gepiRcVersion > $versionRc_old) or ($gepiRcVersion=='')) {
            // Soit on a une nouvelle RC, soit on est passé de RC à stable
           return TRUE;
           die();
       }
   }
   if (($gepiVersion == $version_old) and ($versionBeta_old!='')) {
        // On avait une Beta
       if (($gepiBetaVersion > $versionBeta_old) or ($gepiBetaVersion=='')) {
            // Soit on a une nouvelle Beta, soit on est passé à une RC ou une stable
           return TRUE;
           die();
       }
   }
   return FALSE;
}

/**
 * Recherche si la mise à jour est à faire
 *
 * @global mixed 
 * @global mixed 
 * @global mixed 
 * @param mixed $num le numéro de version
 * @return booleanTRUE s'il faut faire la mise à jour
 */
function quelle_maj($num) {
    global $gepiVersion, $gepiRcVersion, $gepiBetaVersion;
    $version_old = getSettingValue("version");
    $versionRc_old = getSettingValue("versionRc");
    $versionBeta_old = getSettingValue("versionBeta");
    if ($version_old < $num) {
        return TRUE;
        die();
    }
    if ($version_old == $num) {
        if ($gepiRcVersion > $versionRc_old) {
            return TRUE;
            die();
        }
        if ($gepiRcVersion == $versionRc_old) {
            if ($gepiBetaVersion > $versionBeta_old) {
                return TRUE;
                die();
            }
        }
    }
    return FALSE;
}

/**
 *
 * @global text
 * @return booleanTRUE si tout c'est bien passé 
 * @see getSettingValue()
 * @see saveSetting()
 */
function check_backup_directory() {

	global $multisite;

	$pref_multi="";
	if(($multisite=='y')&&(isset($_COOKIE['RNE']))) {
		$pref_multi=$_COOKIE['RNE']."_";
	}

    $current_backup_dir = getSettingValue("backup_directory");
    if ($current_backup_dir == NULL) {$current_backup_dir = "no_folder";}
    if (!file_exists("./backup/".$current_backup_dir)) {
        $backupDirName = NULL;
        if ($multisite != 'y') {
        	// On regarde d'abord si le répertoire de backup n'existerait pas déjà...
        	$handle=opendir('./backup');

        	while ($file = readdir($handle)) {
            	if (mb_strlen($file) > 34 and is_dir('./backup/'.$file)) $backupDirName = $file;
        	}

        	closedir($handle);
        }

        if ($backupDirName != NULL) {
            // Il existe : on met simplement à jour le nom du répertoire...
            $update = saveSetting("backup_directory",$backupDirName);
        } else {
            // Il n'existe pas
            // On crée le répertoire de backup
            $length = rand(35, 45);
            for($len=$length,$r='';mb_strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
            $dirname = $pref_multi.$r;
            $create = mkdir("./backup/" . $dirname, 0700);
            //copy("./backup/index.html","./backup/".$dirname."/index.html");
            if ($create) {
                $f=fopen("./backup/".$dirname."/index.html","w+");
                fwrite($f, '<script type="text/javascript">document.location.replace("../../login.php");</script>');
                fclose($f);

                saveSetting("backup_directory", $dirname);
                saveSetting("backupdir_lastchange",time());
            } else {
                return FALSE;
                die();
            }

            // On déplace les éventuels fichiers .sql dans ce nouveau répertoire

            $handle=opendir('./backup');
            $tab_file = array();
            $n=0;
            while ($file = readdir($handle)) {
                if (($file != '.') and ($file != '..') and ($file != 'remove.txt')
                and (preg_match('/sql$/',$file)) and ($file != '.htaccess') and ($file != '.htpasswd') and ($file != 'index.html') ) {
                    $tab_file[] = $file;
                    $n++;
                }
            }
            closedir($handle);
            foreach($tab_file as $filename) {
                rename("backup/".$filename, "backup/".$dirname."/".$filename);
            }
        }
    }

    // On vérifie la date du dernier changement, et on change le nom
    // du répertoire si le dernier changement a eu lieu il y a plus de 48h
    $lastchange = getSettingValue("backupdir_lastchange");
    $current_time = time();

    // Si le dernier changement a eu lieu il y a plus de 48h, on change le nom du répertoire
    if ($current_time-$lastchange > 172800) {
        $dirname = getSettingValue("backup_directory");
        $length = rand(35, 45);
        for($len=$length,$r='';mb_strlen($r)<$len;$r.=chr(!mt_rand(0,2) ? mt_rand(48,57):(!mt_rand(0,1)?mt_rand(65,90):mt_rand(97,122))));
        $newdirname = $pref_multi.$r;
        if (rename("./backup/".$dirname, "./backup/".$newdirname)) {
            // Correction du contenu de l'index.html (bug sur le chemin relatif de la redir à une époque)
            $f=fopen("./backup/".$newdirname."/index.html","w+");
            fwrite($f, '<script type="text/javascript">document.location.replace("../../login.php");</script>');
            fclose($f);

            saveSetting("backup_directory",$newdirname);
            saveSetting("backupdir_lastchange",time());
            return TRUE;
        } else {
            echo "Erreur lors du renommage du dossier de sauvegarde.<br />";
            return FALSE;
        }
    }
    return TRUE;

}

/**
 * Fonction qui retourne le nombre de périodes pour une classe
 *
 * @param int identifiant numérique de la classe
 * @return int Nombre de periodes définies pour cette classe
 */
function get_period_number($_id_classe) {
    $periode_query = mysql_query("SELECT count(*) FROM periodes WHERE id_classe = '" . $_id_classe . "'");
    $nb_periode = mysql_result($periode_query, 0);
    return $nb_periode;
}

/**
 * Renvoie le numéro et le nom de la première période active pour une classe
 *
 * @param int $_id_classe identifiant unique de la classe
 * @return array numéro de la période 'num' et son nom 'nom'
 */
function get_periode_active($_id_classe){
  $periode_query  = mysql_query("SELECT num_periode, nom_periode FROM periodes WHERE id_classe = '" . $_id_classe . "' AND verouiller = 'N'");
  $reponse        = mysql_fetch_array($periode_query);

  return $retour = array('nom' => $reponse["num_periode"], 'nom' => $reponse["nom_periode"]);

}

/**
 * Cette fonction est à appeler dans tous les cas où une tentative
 * d'utilisation illégale de Gepi est manifestement avérée.
 * Elle est à appeler notamment dans tous les tests de sécurité lorsqu'un test est négatif.
 * Possibilité d'envoyer un mail à l'administrateur et de bloquer l'utilisateur
 *
 * @global string
 * @param integer $_niveau Niveau d'intrusion enregistré
 * @param string $_description Message enregistré pour cette tentative
 * @see getSettingValue()
 * @see mail()
 */
function tentative_intrusion($_niveau, $_description) {

	global $gepiPath;

	// On commence par enregistrer la tentative en question

	if (!isset($_SESSION['login'])) {
		// Ici, ça veut dire que l'attaque est extérieure. Il n'y a pas d'utilisateur logué.
		$user_login = "-";
	} else {
		$user_login = $_SESSION['login'];
	}
	$adresse_ip = $_SERVER['REMOTE_ADDR'];
	$date = strftime("%Y-%m-%d %H:%M:%S");
	$url = parse_url($_SERVER['REQUEST_URI']);
    $fichier = mb_substr($url['path'], mb_strlen($gepiPath));
	$res = mysql_query("INSERT INTO tentatives_intrusion SET " .
			"login = '".$user_login."', " .
			"adresse_ip = '".$adresse_ip."', " .
			"date = '".$date."', " .
			"niveau = '".(int)$_niveau."', " .
			"fichier = '".$fichier."', " .
			"description = '".addslashes($_description)."', " .
			"statut = 'new'");

	// On a enregistré.

	// On initialise des marqueurs pour les deux actions possibles : envoie d'un email à l'admin
	// et blocage du compte de l'utilisateur

	$send_email = FALSE;
	$block_user = FALSE;

	// Est-ce qu'on envoie un mail quoi qu'il arrive ?
	if (getSettingValue("security_alert_email_admin") == "yes" AND $_niveau >= getSettingValue("security_alert_email_min_level")) {
		$send_email = TRUE;
	}

	// Si la tentative d'intrusion a été effectuée par un utilisateur connecté à Gepi,
	// on regarde si des seuils ont été dépassés et si certaines actions doivent être
	// effectuées.

	if ($user_login != "-") {
		// On récupère quelques infos
		$req = mysql_query("SELECT nom, prenom, statut, niveau_alerte, observation_securite FROM utilisateurs WHERE (login = '".$user_login."')");
		$user = mysql_fetch_object($req);
		// On va utiliser ça pour générer automatiquement les noms de settings, ça fait du code en moins...
		if ($user->observation_securite == "1") {
			$obs = "probation";
		} else {
			$obs = "normal";
		}

		// D'abord, on met à jour le niveau cumulé
		$nouveau_cumul = (int)$user->niveau_alerte+(int)$_niveau;

		$res = mysql_query("UPDATE utilisateurs SET niveau_alerte = '".$nouveau_cumul ."' WHERE (login = '".$user_login."')");

		$seuil1 = FALSE;
		$seuil2 = FALSE;
		// Maintenant on regarde les seuils.
		if ($nouveau_cumul >= getSettingValue("security_alert1_".$obs."_cumulated_level")
				AND $nouveau_cumul < getSettingValue("security_alert2_".$obs."_cumulated_level")) {
			// Seuil 1
			if (getSettingValue("security_alert1_".$obs."_email_admin") == "yes") $send_email = TRUE;
			if (getSettingValue("security_alert1_".$obs."_block_user") == "yes") $block_user = TRUE;
			$seuil1 = TRUE;

		} elseif ($nouveau_cumul >= getSettingValue("security_alert2_".$obs."_cumulated_level")) {
			// Seuil 2
			if (getSettingValue("security_alert2_".$obs."_email_admin") == "yes") $send_email = TRUE;
			if (getSettingValue("security_alert2_".$obs."_block_user") == "yes") $block_user = TRUE;
			$seuil2 = TRUE;
		}

		// On désactive le compte de l'utilisateur si nécessaire :
		if ($block_user) {
			$res = mysql_query("UPDATE utilisateurs SET etat = 'inactif' WHERE (login = '".$user_login."')");
		}
	} // Fin : if ($user_login != "-")

	// On envoie un email à l'administrateur si nécessaire
	if ($send_email) {
		$message = "** Alerte automatique sécurité Gepi **\n\n";
		$message .= "Une nouvelle tentative d'intrusion a été détectée par Gepi. Les détails suivants ont été enregistrés dans la base de données :\n\n";
		$message .= "Date : ".$date."\n";
		$message .= "Fichier visé : ".$fichier."\n";
		if(isset($_SERVER['HTTP_REFERER'])) {
			$message .= "Url d'origine : ".$_SERVER['HTTP_REFERER']."\n";
		}
		$message .= "Niveau de gravité : ".$_niveau."\n";
		$message .= "Description : ".$_description."\n\n";
		if ($user_login == "-") {
			$message .= "La tentative d'intrusion a été effectuée par un utilisateur non connecté à Gepi.\n";
			$message .= "Adresse IP : ".$adresse_ip."\n";
		} else {
			$message .= "Informations sur l'utilisateur :\n";
			$message .= "Login : ".$user_login."\n";
			$message .= "Nom : ".$user->prenom . " ".$user->nom."\n";
			$message .= "Statut : ".$user->statut."\n";
			$message .= "Score cumulé : ".$nouveau_cumul."\n\n";
			if ($seuil1) $message .= "L'utilisateur a dépassé le seuil d'alerte 1.\n\n";
			if ($seuil2) $message .= "L'utilisateur a dépassé le seuil d'alerte 2.\n\n";
			if ($block_user) $message .= "Le compte de l'utilisateur a été désactivé.\n";
		}

		$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
		if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}

		$subject = $gepiPrefixeSujetMail."GEPI : Alerte sécurité -- Tentative d'intrusion";
		$subject = "=?UTF-8?B?".base64_encode($subject)."?=\r\n";
	
		$headers = "X-Mailer: PHP/" . phpversion()."\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
		$headers .= "From: Mail automatique Gepi <ne-pas-repondre@".$_SERVER['SERVER_NAME'].">\r\n";

		// On envoie le mail
		$envoi = mail(getSettingValue("gepiAdminAdress"),
			$subject,
			$message,
			$headers);
	}
}

/**
 * Fonction destinée à créer un dossier temporaire aléatoire /temp/<alea>
 * 
 * Test le dossier en écriture et le crée au besoin
 *
 * @return booleanTRUE si tout c'est bien passé
 * @see getSettingValue()
 * @see saveSetting()
 */
function check_temp_directory(){

	$dirname=getSettingValue("temp_directory");
	if(($dirname=='')||(!file_exists("./temp/$dirname"))){
		// Il n'existe pas
		// On créé le répertoire temp
		$length = rand(35, 45);
		for($len=$length,$r='';mb_strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
		$dirname = $r;
		$create = mkdir("./temp/".$dirname, 0700);

		if ($create) {
			$fich=fopen("./temp/".$dirname."/index.html","w+");
			fwrite($fich,'<html><head><script type="text/javascript">
    document.location.replace("../../login.php")
</script></head></html>
');
			fclose($fich);

			saveSetting("temp_directory", $dirname);
			return TRUE;
		} else {
			return FALSE;
			die();
		}
	} else {
		return TRUE;
	}
}

/**
 * Fonction destinée à créer un dossier /temp/<alea> propre au professeur
 * 
 * Test le dossier en écriture et le crée au besoin
 * La fonction est appelée depuis la racine de l'arborescence GEPI (sinon ça peut bugger)
 *
 * @return booleanTRUE si tout c'est bien passé
 */
function check_user_temp_directory(){
	global $multisite;

	$pref_multi="";
	if(($multisite=='y')&&(isset($_COOKIE['RNE']))) {
		$pref_multi=$_COOKIE['RNE']."_";
	}

	$sql="SELECT temp_dir FROM utilisateurs WHERE login='".$_SESSION['login']."'";
	$res_temp_dir=mysql_query($sql);

	if(mysql_num_rows($res_temp_dir)==0){
		// Cela revient à dire que l'utilisateur n'est pas dans la table utilisateurs???
		return FALSE;
	}
	else{
		$lig_temp_dir=mysql_fetch_object($res_temp_dir);
		$dirname=$lig_temp_dir->temp_dir;

		if($dirname=="") {
			// Le dossier n'existe pas
			// On créé le répertoire temp
			$length = rand(35, 45);
			for($len=$length,$r='';mb_strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
			$dirname = $pref_multi.$_SESSION['login']."_".$r;
			$create = mkdir("./temp/".$dirname, 0700);

			if($create){
				$fich=fopen("./temp/".$dirname."/index.html","w+");
				fwrite($fich,'<html><head><script type="text/javascript">
	document.location.replace("../../login.php")
</script></head></html>
');
				fclose($fich);

				$sql="UPDATE utilisateurs SET temp_dir='$dirname' WHERE login='".$_SESSION['login']."'";
				$res_update=mysql_query($sql);
				if($res_update){
					return TRUE;
				}
				else{
					return FALSE;
				}
			}
			else{
				return FALSE;
			}
		}
		else {
			if(($pref_multi!='')&&(!preg_match("/^$pref_multi/", $dirname))&&(file_exists("./temp/".$dirname))) {
				// Il faut renommer le dossier
				if(!rename("./temp/".$dirname,"./temp/".$pref_multi.$dirname)) {
					return FALSE;
					exit();
				}
				else {
					$dirname=$pref_multi.$dirname;

					$sql="UPDATE utilisateurs SET temp_dir='$dirname' WHERE login='".$_SESSION['login']."'";
					$res_update=mysql_query($sql);
					if(!$res_update){
						return FALSE;
						exit();
					}
				}
			}

			if(!file_exists("./temp/".$dirname)){
				// Le dossier n'existe pas
				// On créé le répertoire temp
				$create = mkdir("./temp/".$dirname, 0700);

				if($create){
					$fich=fopen("./temp/".$dirname."/index.html","w+");
					fwrite($fich,'<html><head><script type="text/javascript">
	document.location.replace("../../login.php")
</script></head></html>
');
					fclose($fich);
					return TRUE;
				}
				else{
					return FALSE;
				}
			}
			else{
				$fich=fopen("./temp/".$dirname."/test_ecriture.tmp","w+");
				$ecriture=fwrite($fich,'Test d écriture.');
				$fermeture=fclose($fich);
				if(file_exists("./temp/".$dirname."/test_ecriture.tmp")){
					unlink("./temp/".$dirname."/test_ecriture.tmp");
				}

				if(($fich)&&($ecriture)&&($fermeture)){
					return TRUE;
				}
				else{
					return FALSE;
				}
			}
		}
	}
}

/**
 * Renvoie le nom du répertoire temporaire de l'utilisateur
 *
 * @return bool|string retourne FALSE s'il n'existe pas et le nom du répertoire s'il existe, sans le chemin
 */
function get_user_temp_directory(){
	$sql="SELECT temp_dir FROM utilisateurs WHERE login='".$_SESSION['login']."'";
	$res_temp_dir=mysql_query($sql);
	if(mysql_num_rows($res_temp_dir)>0){
		$lig_temp_dir=mysql_fetch_object($res_temp_dir);
		$dirname=$lig_temp_dir->temp_dir;

		if(($dirname!="")&&(mb_strlen(preg_replace("/[A-Za-z0-9_.]/","",$dirname))==0)) {
			if(file_exists("temp/".$dirname)){
				return $dirname;
			}
			elseif(file_exists("../temp/".$dirname)) {
				return $dirname;
			}
			else if(file_exists("../../temp/".$dirname)) {
				return $dirname;
			}
			else{
				return FALSE;
			}
		}
		else{
			return FALSE;
		}
	}
	else{
		return FALSE;
	}
}

/**
 * Retourne un nombre formaté en Mo, ko ou o suivant ça taille
 *
 * @param int $volume le nombre à formater
 * @return string le nombre formaté
 */
function volume_human($volume){
	if($volume>=1048576){
		$volume=round(10*$volume/1048576)/10;
		return $volume." Mo";
	}
	elseif($volume>=1024){
		$volume=round(10*$volume/1024)/10;
		return $volume." ko";
	}
	else{
		return $volume." o";
	}
}

/**
 * Renvoie la taille d'un répertoire
 *
 * @global int 
 * @param string $dir Le répertoire à tester
 * @return string la taille formatée 
 * @see volume_dir()
 * @see volume_human()
 */
function volume_dir_human($dir){
	$volume=volume_dir($dir);
	return volume_human($volume);
}

/**
 * Additionne la taille des répertoires et sous-répertoires
 *
 * @global int
 * @param string $dir répertoire à parser
 * @return int la taille totale du répertoire
 */
function volume_dir($dir){
	//global $totalsize;
	$totalsize=0;

	$handle = @opendir($dir);
	while ($file = @readdir ($handle)){
		if (preg_match("/^\.{1,2}$/i",$file))
			continue;
		if(is_dir("$dir/$file")){
			$totalsize+=volume_dir("$dir/$file");
		}
		else{
			$tabtmpsize=stat("$dir/$file");
			$size=$tabtmpsize[7];

			$totalsize+=$size;
		}
	}
	@closedir($handle);

	return($totalsize);
}

/**
 * Supprime les fichiers d'un dossier
 *
 * @param string $dir le répertoire à vider
 * @return boolean TRUE si tout c'est bien passé
 * @todo En ajoutant un paramètre à la fonction, on pourrait activer la suppression récursive (avec une profondeur par exemple)
 */
function vider_dir($dir){
	$statut=TRUE;
	$handle = @opendir($dir);
	while ($file = @readdir ($handle)){
		if (preg_match("/^\.{1,2}$/i",$file)){
			continue;
		}
		if(is_dir("$dir/$file")){
			// On ne cherche pas à vider récursivement.
			$statut=FALSE;

			echo "<!-- DOSSIER: $dir/$file -->\n";
			// En ajoutant un paramètre à la fonction, on pourrait activer la suppression récursive (avec une profondeur par exemple) lancer ici vider_dir("$dir/$file");
		}
		else{
			if(!unlink($dir."/".$file)) {
				$statut=FALSE;
				echo "<!-- Echec suppression: $dir/$file -->\n";
				break;
			}
		}
	}
	@closedir($handle);

	return $statut;
}


/**
 * Additionne la taille des documents joints dans le CDT d'un groupe
 *
 * @param int $id_groupe Identifiant du groupe
 * @return int la taille totale des documents joints
 */
function volume_docs_joints($id_groupe, $mode="all"){
	$volume_cdt_groupe=0;

	/*
	$sql="SELECT DISTINCT cd.id_ct FROM ct_documents cd, ct_entry ce WHERE cd.id_ct=ce.id_ct AND ce.id_groupe='".$groupe->getId()."';";
	//echo "$sql<br />";
	$res_doc=mysql_query($sql);
	if(mysql_num_rows($res_doc)>0) {
		while($lig_doc=mysql_fetch_object($res_doc)) {
			$volume_cdt_groupe+=volume_dir("../documents/cl".$lig_doc->id_ct);
		}
	}
	$sql="SELECT DISTINCT cde.id_ct FROM ct_devoirs_documents cdd, ct_devoirs_entry cde WHERE cdd.id_ct_devoir=cde.id_ct AND cde.id_groupe='".$groupe->getId()."';";
	//echo "$sql<br />";
	$res_doc=mysql_query($sql);
	if(mysql_num_rows($res_doc)>0) {
		while($lig_doc=mysql_fetch_object($res_doc)) {
			$volume_cdt_groupe+=volume_dir("../documents/cl_dev".$lig_doc->id_ct);
		}
	}
	*/

	if($mode=="devoirs") {
		$sql="SELECT DISTINCT cdd.emplacement FROM ct_devoirs_documents cdd, ct_devoirs_entry cde WHERE cdd.id_ct_devoir=cde.id_ct AND cde.id_groupe='".$id_groupe."';";
	}
	elseif($mode=="compte_rendus") {
		$sql="SELECT DISTINCT cd.emplacement FROM ct_documents cd, ct_entry ce WHERE cd.id_ct=ce.id_ct AND ce.id_groupe='".$id_groupe."';";
	}
	else {
		$sql="(SELECT DISTINCT cd.emplacement FROM ct_documents cd, ct_entry ce WHERE cd.id_ct=ce.id_ct AND ce.id_groupe='".$id_groupe."') UNION (SELECT DISTINCT cdd.emplacement FROM ct_devoirs_documents cdd, ct_devoirs_entry cde WHERE cdd.id_ct_devoir=cde.id_ct AND cde.id_groupe='".$id_groupe."');";
	}
	//echo "$sql<br />";
	$res_doc=mysql_query($sql);
	if(mysql_num_rows($res_doc)>0) {
		while($lig_doc=mysql_fetch_object($res_doc)) {
			if(file_exists($lig_doc->emplacement)) {
				$tabtmpsize=stat($lig_doc->emplacement);
				if(isset($tabtmpsize[7])) {
					$size=$tabtmpsize[7];
					$volume_cdt_groupe+=$size;
				}
			}
		}
	}

	return($volume_cdt_groupe);
}




/**
 * Cette fonction supprime le BOM éventuel d'un fichier encodé en UTF-8
 * A appeler immédiatement après ouverture du fichier
 * Exemple :
 * $handle=fopen("....");
 * skip_bom_utf8($handle)
 *
 * @param handle $h_file : Le pointeur de fichier à tester
 * @return boolean : true si pas de BOM ou si BOM sauté, false dans les autres cas 
 */
function skip_bom_utf8($h_file)
	{
	if (ftell($h_file)!=0) return false;
	$bytes=fread($h_file,3);
	if ($bytes===false) return false;
	if ($bytes!="\xEF\xBB\xBF") return rewind($h_file);
	return true;
	}

/**
 * Cette méthode prend une chaîne de caractères et s'assure qu'elle est bien retournée en UTF-8
 * Attention, certain encodages sont très similaire et ne peuve pas être théoriquement distingué sur une chaine de caractere.
 * Si vous connaissez déjà l'encodage de votre chaine de départ, il est préférable de le préciser
 * 
 * @param string $str La chaine à encoder
 * @param string $encoding L'encodage de départ
 * @return string La chaine en utf8
 * @throws Exception si la chaine n'a pas pu être encodée correctement
 */
function ensure_utf8($str, $from_encoding = null) {
    if ($str === null || $str === '') {
        return $str;
    } else if ($from_encoding == null && detect_utf8($str)) {
	    return $str;
	}
	
    if ($from_encoding != null) {
        $encoding =  $from_encoding;
    } else {
	    $encoding = detect_encoding($str);
    }
	$result = null;
    if ($encoding !== false && $encoding != null) {
        if (function_exists('mb_convert_encoding')) {
            $result = mb_convert_encoding($str, 'UTF-8', $encoding);
        }
    }
	if ($result === null || !detect_utf8($result)) {
	    throw new Exception('Impossible de convertir la chaine vers l\'utf8');
	}
	return $result;
}


/**
 * Cette méthode prend une chaîne de caractères et teste si elle ne contient que 
 * de l'ASCII 7 bits ou si elle contient au moins une suite d'octets codant un
 * caractère en UTF8
 * @param string $str La chaine à tester
 * @return boolean
 */
function detect_utf8 ($str) {
	// Inspiré de http://w3.org/International/questions/qa-forms-utf-8.html
	//
	// on s'assure de bien opérer sur une chaîne de caractère
	$str=(string)$str;
	// La chaîne ne comporte que des octets <= 7F ?
	$full_ascii=true; $i=0;
	while ($full_ascii && $i<strlen($str)) {
		$full_ascii = $full_ascii && (ord($str[$i])<=0x7F);
		$i++;
	}
	// Si oui c'est de l'utf8 sinon on cherche si la chaîne contient
	// au moins une suite d'octets valide en UTF8
	if ($full_ascii) return true;
	else return preg_match('#[\xC2-\xDF][\x80-\xBF]#', $str) || // non-overlong 2-byte
		preg_match('#\xE0[\xA0-\xBF][\x80-\xBF]#', $str) || // excluding overlongs
		preg_match('#[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}#', $str) || // straight 3-byte
		preg_match('#\xED[\x80-\x9F][\x80-\xBF]#', $str) | // excluding surrogates
		preg_match('#\xF0[\x90-\xBF][\x80-\xBF]{2}#', $str) || // planes 1-3
		preg_match('#[\xF1-\xF3][\x80-\xBF]{3}#', $str) || // planes 4-15
		preg_match('# \xF4[\x80-\x8F][\x80-\xBF]{2}#', $str) ; // plane 16
 }

/**
 * Cette méthode prend une chaîne de caractères et teste si elle est bien encodée en UTF-8
 * 
 * @param string $str La chaine à tester
 * @return boolean
 */
function check_utf8 ($str) {
    // Longueur maximale de la chaîne pour éviter un stack overflow
	// dans le test à base d'expression régulière
	$long_max=1000;
	if (substr(PHP_OS,0,3) == 'WIN') $long_max=300; // dans le cas de Window$
    if (mb_strlen($str) < $long_max) {
    // From http://w3.org/International/questions/qa-forms-utf-8.html
    $preg_match_result = 1 == preg_match('%^(?:
          [\x09\x0A\x0D\x20-\x7E]            # ASCII
        | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
        |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
    )*$%xs', $str);
    } else {
        $preg_match_result = FALSE;
    }
    if ($preg_match_result) {
        return true;
    } else {
        //le test preg renvoie faux, et on va vérifier avec d'autres fonctions
        $result = true;
        $test_done = false;
        if (function_exists('mb_check_encoding')) {
            $test_done = true;
            $result = $result && @mb_check_encoding($str, 'UTF-8');
        }

        if (function_exists('mb_detect_encoding')) {
            $test_done = true;
            $result = $result && @mb_detect_encoding($str, 'UTF-8', true);
        }
        if (function_exists('iconv')) {
            $test_done = true;
            $result = $result && ($str === (@iconv('UTF-8', 'UTF-8//IGNORE', $str)));
        }
        if (function_exists('mb_convert_encoding') && !$test_done) {
            $test_done = true;
            $result = $result && ($str === @mb_convert_encoding ( @mb_convert_encoding ( $str, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32' ));
        }
        return ($test_done && $result);
    }
}
    
    
/**
 * Cette méthode prend une chaîne de caractères et détecte son encodage
 * 
 * @param string $str La chaine à tester
 * @return l'encodage ou false si indétectable
 */
function detect_encoding($str) {
    //on commence par vérifier si c'est de l'utf8
    if (detect_utf8($str)) {
        return 'UTF-8';
    }
    
    //on va commencer par tester ces encodages
    static $encoding_list = array('UTF-8', 'ISO-8859-15','windows-1251');
    foreach ($encoding_list as $item) {
        if (function_exists('iconv')) {
            $sample = @iconv($item, $item, $str);
            if (md5($sample) == md5($str)) {
                return $item;
            }
        } else if (function_exists('mb_detect_encoding')) {
            if (@mb_detect_encoding($str, $item, true)) {
                return $item;
            }
        }
    }
    
    //la méthode précédente n'a rien donnée
    if (function_exists('mb_detect_encoding')) {
        return mb_detect_encoding($str);
    } else {
        return false;
    }
}

/**
 * Correspondances de caractères accentués/désaccentués
 * 
 * @global string $GLOBALS['liste_caracteres_accentues']
 * @name $liste_caracteres_accentues
 */
//$GLOBALS['liste_caracteres_accentues']="ÂÄÀÁÃÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕØ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõø¨ûüùúýÿ¸";
$GLOBALS['liste_caracteres_accentues']="ÂÄÀÁÃÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕØÛÜÙÚÝ¾áàâäãåçéèêëîïìíñôöðòóõøûüùúýÿ";

/**
 * Correspondances de caractères accentués/désaccentués
 * 
 * @global string $GLOBALS['liste_caracteres_desaccentues']
 * @name $liste_caracteres_desaccentues
 */
//$GLOBALS['liste_caracteres_desaccentues']="AAAAAACEEEEIIIINOOOOOOSUUUUYYZaaaaaaceeeeiiiinooooooosuuuuyyz";
$GLOBALS['liste_caracteres_desaccentues']="AAAAAACEEEEIIIINOOOOOOUUUUYYaaaaaaceeeeiiiinooooooouuuuyy";

/**
 * Cette méthode prend une chaîne de caractères et s'assure qu'elle est bien retournée en ASCII
 * Attention, certain encodages sont très similaire et ne peuve pas être théoriquement distingué sur une chaine de caractere.
 * Si vous connaissez déjà l'encodage de votre chaine de départ, il est préférable de le préciser
 * 
 * @param string $chaine La chaine à encoder
 * @param string $encoding L'encodage de départ
 * @return string La chaine en ascii
 */
function ensure_ascii($chaine, $encoding = '') {
    if ($chaine == null || $chaine == '') {
        return $chaine;
    }

    $chaine = ensure_utf8($chaine, $encoding);
    $str = null;
    if (function_exists('iconv')) {
        //test : est-ce que iconv est bien implémenté sur ce système ?
        $test = 'c\'est un bel ete' === iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", 'c\'est un bel été');
        if ($test) {
            //on utilise iconv pour la conversion
            $str = @iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $chaine);
        }
    }
    if ($str === null) {
        //on utilise pas iconv pour la conversion
    	$translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');
    	$str = strtr($chaine, $translit);
    }
    if (function_exists('mb_convert_encoding')) {
        $str = @mb_convert_encoding($str,'ASCII','UTF-8');
    }  
    return $str; 
}

/**
 * Remplace les accents dans une chaine, et en fonction du paramètre 'mode' remplace les caractères non alphabétiques par des _ (underscore)
 * 
 * $mode = 'all' ou mode = '' On remplace espaces et apostrophes par des '_' et les caractères accentués par leurs équivalents non accentués.
 * 
 * $mode = 'all_nospace' On remplace apostrophes par des '_' et les caractères accentués par leurs équivalents non accentués.
 * 
 *  Sinon, on remplace les caractères accentués par leurs équivalents non accentués.
 *
 * @global string 
 * @global string 
 * @param type $chaine La chaine à tester
 * @param type $mode Mode de conversion
 * @return type 
 */
function remplace_accents($chaine,$mode=''){
	$str = ensure_ascii($chaine);

	if($mode == 'all'){
		return preg_replace('#[^a-zA-Z0-9\-\_]#', '_', $str); // Pour des noms de fichiers par exemple
	} elseif($mode == 'all_nospace'){
		return preg_replace('#[^a-zA-Z0-9\-\._ ]#', '_', $str);
	} else {
		return preg_replace('#[^a-zA-Z0-9\-\._"\' ;]#', '_', $str);
	}
}
/**
 * @see remplace_accent($chaine,$mode='')
**/
function enleve_accents($chaine,$mode=''){
    return remplace_accents($chaine,$mode='');
}
/**
 * @see remplace_accent($chaine,$mode='')
**/
function accents_enleve($chaine,$mode=''){
    return remplace_accents($chaine,$mode='');
}

/**
 * Nettoyage des caractères d'un nom ou prénom
 * On ne conserve que les lettres (accentuées incluses), l'espace et le tiret
 *
 * @global string 
 * @param type $chaine La chaine à traiter
 * @return La chaine corrigée
 */
function nettoyer_caracteres_nom($chaine, $mode="a", $chaine_autres_caracteres_acceptes="", $caractere_remplacement="", $remplacer_oe_ae="n") {
	global $liste_caracteres_accentues;
	/*
	echo "<p>";
	echo "<span style='color:green'>\$liste_caracteres_accentues=$liste_caracteres_accentues</span><br />";
	echo "<span style='color:green'>\$chaine=$chaine</span><br />";
	*/
	// Pour que le tiret soit à la fin si on le met dans $chaine_autres_caracteres_acceptes
	$chaine_autres_caracteres_acceptes="ÆæŒœ".$chaine_autres_caracteres_acceptes;

	if(is_numeric(trim($chaine))) {
		$retour=trim($chaine);
	}
	else {
		$retour=trim(ensure_utf8($chaine));
	}

	if($remplacer_oe_ae=="y") {$retour=preg_replace("#Æ#u","AE",preg_replace("#æ#u","ae",preg_replace("#Œ#u","OE",preg_replace("#œ#u","oe",$retour))));}
	//if($remplacer_oe_ae=="y") {$retour=preg_replace("/Æ/","AE",preg_replace("/æ/","ae",preg_replace("/Œ/","OE",preg_replace("/œ/","oe",$retour))));}

	// Le /u sur les preg_replace permet de traiter correctement des chaines utf8
	if($mode=='a') {
		
		//echo "<span style='color:green'>\$retour=preg_replace(\"#[^A-Za-z".$liste_caracteres_accentues.$chaine_autres_caracteres_acceptes."]#u\",\"$caractere_remplacement\", $retour)=</span>";
		$retour=preg_replace("#[^A-Za-z".$liste_caracteres_accentues.$chaine_autres_caracteres_acceptes."]#u","$caractere_remplacement", $retour);
		//echo "<span style='color:green'>$retour</span><br />";
		//echo "<br />";

		/*
		echo "\$retour=preg_replace(\"/[^A-Za-z".$liste_caracteres_accentues.$chaine_autres_caracteres_acceptes."]/u\",\"$caractere_remplacement\", $retour)=";
		$retour=preg_replace("/[^A-Za-z".$liste_caracteres_accentues.$chaine_autres_caracteres_acceptes."]/u","$caractere_remplacement", $retour);
		echo "$retour<br />";
		echo "<br />";
		*/
	}
	elseif($mode=='an') {
		//echo "<span style='color:green'>\$retour=preg_replace(\"#[^A-Za-z0-9".$liste_caracteres_accentues.$chaine_autres_caracteres_acceptes."]#u\",\"$caractere_remplacement\", $retour)=</span>";
		$retour=preg_replace("#[^A-Za-z0-9".$liste_caracteres_accentues.$chaine_autres_caracteres_acceptes."]#u","$caractere_remplacement", $retour);
		//echo "<span style='color:green'>$retour</span><br />";
		//echo "<br />";

		/*
		echo "\$retour=preg_replace(\"/[^A-Za-z0-9".$liste_caracteres_accentues.$chaine_autres_caracteres_acceptes."]/u\",\"$caractere_remplacement\", $retour)=";
		$retour=preg_replace("/[^A-Za-z0-9".$liste_caracteres_accentues.$chaine_autres_caracteres_acceptes."]/u","$caractere_remplacement", $retour);
		echo "$retour<br />";
		echo "<br />";
		*/
	}

	return $retour;
}


/**
 * fonction qui renvoie le nom de la classe d'un élève pour chaque période
 *
 * @param string $ele_login login de l'élève
 * @return array Tableau des classes en fonction des périodes
 */
function get_class_from_ele_login($ele_login){
	$sql="SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='$ele_login' ORDER BY periode,classe;";
	$res_class=mysql_query($sql);
	$a = 0;
	$tab_classe=array();
	if(mysql_num_rows($res_class)>0){
		$tab_classe['liste'] = "";
		$tab_classe['liste_nbsp'] = "";
		while($lig_tmp=mysql_fetch_object($res_class)){

			$tab_classe[$lig_tmp->id_classe]=$lig_tmp->classe;

			if($a>0) {$tab_classe['liste'].=", ";}
			$tab_classe['liste'].=$lig_tmp->classe;

			if($a>0) {$tab_classe['liste_nbsp'].=", ";}
			$tab_classe['liste_nbsp'].=preg_replace("/ /","&nbsp;",$lig_tmp->classe);

			$tab_classe['id'.$a] = $lig_tmp->id_classe;
			//$a = $a++;
			$a++;
		}
	}
	return $tab_classe;
}

/**
 * Retourne les classes d'un élève ordonnées par périodes puis classes
 *
 * @param string $ele_login Login de l'élève
 * @return array 
 */
function get_noms_classes_from_ele_login($ele_login){
	$sql="SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='$ele_login' ORDER BY periode,classe;";
	$res_class=mysql_query($sql);

	$tab_classe=array();
	if(mysql_num_rows($res_class)>0){
		while($lig_tmp=mysql_fetch_object($res_class)){
			$tab_classe[]=$lig_tmp->classe;
		}
	}
	return $tab_classe;
}

/**
 * Renvoie les élèves liés à un responsable
 *
 * @param string $resp_login Login du responsable
 * @param string $mode Si avec_classe renvoie aussi la classe
 * @param string $meme_en_resp_legal_0 'y'
 *               (on récupère même les enfants dont $resp_login est resp_legal=0)
 *               'yy' (on récupère aussi les resp_legal=0 mais seulement s'ils ont l'accès aux données en tant qu'utilisateur
 *               ou 'n' (on ne récupère que les enfants dont $resp_login est resp_legal=1 ou 2)
 * @return array 
 * @see get_class_from_ele_login()
 */
function get_enfants_from_resp_login($resp_login, $mode='simple', $meme_en_resp_legal_0="n") {
	$sql="(SELECT e.nom,e.prenom,e.login FROM eleves e,
											responsables2 r,
											resp_pers rp
										WHERE e.ele_id=r.ele_id AND
											rp.pers_id=r.pers_id AND
											rp.login='$resp_login' AND
										(r.resp_legal='1' OR r.resp_legal='2') ORDER BY e.nom,e.prenom)";
	if($meme_en_resp_legal_0=="y") {
		$sql.=" UNION (SELECT e.nom,e.prenom,e.login FROM eleves e,
											responsables2 r,
											resp_pers rp
										WHERE e.ele_id=r.ele_id AND
											rp.pers_id=r.pers_id AND
											rp.login='$resp_login' AND
										r.resp_legal='0' ORDER BY e.nom,e.prenom)";
	}
	elseif($meme_en_resp_legal_0=="yy") {
		$sql.=" UNION (SELECT e.nom,e.prenom,e.login FROM eleves e,
											responsables2 r,
											resp_pers rp
										WHERE e.ele_id=r.ele_id AND
											rp.pers_id=r.pers_id AND
											rp.login='$resp_login' AND
											r.acces_sp='y' AND
										r.resp_legal='0' ORDER BY e.nom,e.prenom)";
	}
	$res_ele=mysql_query($sql);

	$tab_ele=array();
	if(mysql_num_rows($res_ele)>0){
		while($lig_tmp=mysql_fetch_object($res_ele)){
			$tab_ele[]=$lig_tmp->login;
			if($mode=='avec_classe') {
				$tmp_chaine_classes="";

				$tmp_tab_clas=get_class_from_ele_login($lig_tmp->login);
				if(isset($tmp_tab_clas['liste'])) {
					$tmp_chaine_classes=" (".$tmp_tab_clas['liste'].")";
				}

				$tab_ele[]=ucfirst(mb_strtolower($lig_tmp->prenom))." ".mb_strtoupper($lig_tmp->nom).$tmp_chaine_classes;
			}
			else {
				$tab_ele[]=ucfirst(mb_strtolower($lig_tmp->prenom))." ".mb_strtoupper($lig_tmp->nom);
			}
		}
	}
	return $tab_ele;
}

/**
 * Renvoie les élèves liés à un responsable
 *
 * @param string $pers_id identifiant sconet du responsable
 * @param string $mode Si avec_classe renvoie aussi la classe
 * @param string $meme_en_resp_legal_0 'y'
 *               (on récupère même les enfants dont $resp_login est resp_legal=0)
 *               'yy' (on récupère aussi les resp_legal=0 mais seulement s'ils ont l'accès aux données en tant qu'utilisateur
 *               ou 'n' (on ne récupère que les enfants dont $resp_login est resp_legal=1 ou 2)
 * @return array 
 * @see get_class_from_ele_login()
 */
function get_enfants_from_pers_id($pers_id, $mode='simple', $meme_en_resp_legal_0="n"){
	$sql="(SELECT e.nom,e.prenom,e.login FROM eleves e,
											responsables2 r,
											resp_pers rp
										WHERE e.ele_id=r.ele_id AND
											rp.pers_id=r.pers_id AND
											rp.pers_id='$pers_id' AND 
											(r.resp_legal='1' OR r.resp_legal='2') 
										ORDER BY e.nom,e.prenom)";
	if($meme_en_resp_legal_0=="y") {
		$sql.=" UNION (SELECT e.nom,e.prenom,e.login FROM eleves e,
											responsables2 r,
											resp_pers rp
										WHERE e.ele_id=r.ele_id AND
											rp.pers_id=r.pers_id AND
											rp.pers_id='$pers_id' AND 
											r.resp_legal='0' 
										ORDER BY e.nom,e.prenom)";
	}
	elseif($meme_en_resp_legal_0=="yy") {
		$sql.=" UNION (SELECT e.nom,e.prenom,e.login FROM eleves e,
											responsables2 r,
											resp_pers rp
										WHERE e.ele_id=r.ele_id AND
											rp.pers_id=r.pers_id AND
											rp.pers_id='$pers_id' AND 
											r.acces_sp='y' AND
											r.resp_legal='0' 
										ORDER BY e.nom,e.prenom)";
	}
	$res_ele=mysql_query($sql);

	$tab_ele=array();
	if(mysql_num_rows($res_ele)>0){
		while($lig_tmp=mysql_fetch_object($res_ele)){
			$tab_ele[]=$lig_tmp->login;
			if($mode=='avec_classe') {
				$tmp_chaine_classes="";

				$tmp_tab_clas=get_class_from_ele_login($lig_tmp->login);
				if(isset($tmp_tab_clas['liste'])) {
					$tmp_chaine_classes=" (".$tmp_tab_clas['liste'].")";
				}

				$tab_ele[]=ucfirst(mb_strtolower($lig_tmp->prenom))." ".mb_strtoupper($lig_tmp->nom).$tmp_chaine_classes;
			}
			else {
				$tab_ele[]=ucfirst(mb_strtolower($lig_tmp->prenom))." ".mb_strtoupper($lig_tmp->nom);
			}
		}
	}
	return $tab_ele;
}

/**
 * Renvoie le statut avec des accents
 *
 * @param string $user_statut Statut à corriger
 * @return string Le statut corrigé
 */
function statut_accentue($user_statut){
	switch($user_statut){
		case "administrateur":
			$chaine="administrateur";
			break;
		case "scolarite":
			$chaine="scolarité";
			break;
		case "professeur":
			$chaine="professeur";
			break;
		case "secours":
			$chaine="secours";
			break;
		case "cpe":
			$chaine="cpe";
			break;
		case "eleve":
			$chaine="élève";
			break;
		case "responsable":
			$chaine="responsable";
			break;
		default:
			$chaine="statut inconnu";
			break;
	}
	return $chaine;
}

/**
 * Renvoie le nom d'une classe à partir de son Id
 * 
 * Renvoie classes.classe
 *
 * @param type $id_classe Id de la classe
 * @return string|bool Le nom d'une classe ou FALSE
 */
function get_nom_classe($id_classe){
	$sql="SELECT classe FROM classes WHERE id='$id_classe';";
	$res_class=mysql_query($sql);

	if(mysql_num_rows($res_class)>0){
		$lig_tmp=mysql_fetch_object($res_class);
		$classe=$lig_tmp->classe;
		return $classe;
	}
	else{
		return FALSE;
	}
}

/**
 * Formate une date au format jj/mm/aa
 *
 * @param string $date
 * @return string La date formatée
 */
function formate_date($date, $avec_heure="n") {
	$tmp_date=explode(" ",$date);
	$tab_date=explode("-",$tmp_date[0]);

	$retour="";

	if(isset($tab_date[2])) {
		$retour.=sprintf("%02d",$tab_date[2])."/".sprintf("%02d",$tab_date[1])."/".$tab_date[0];
	}
	elseif(isset($tab_date[0])) {
		$retour.=$tab_date[0];
	}
	else {
		$retour.=$date;
	}

	if(($avec_heure=="y")&&(isset($tmp_date[1]))&&(preg_match("/[0-9]{2}:[0-9]{2}:[0-9]{2}/", $tmp_date[1]))) {
		$retour.=" à ".$tmp_date[1];
	}

	return $retour;
}

/**
 * Convertit les codes régimes de Sconet
 *
 * @param int $code_regime Le code Sconet
 * @return string Le régime dans Gépi
 */
function traite_regime_sconet($code_regime){
	$premier_caractere_code_regime=mb_substr($code_regime,0,1);
	switch($premier_caractere_code_regime){
		case "0":
			// 0       EXTERN  EXTERNE LIBRE
			return "ext.";
			break;
		case "1":
			// 1       EX.SUR  EXTERNE SURVEILLE
			return "ext.";
			break;
		case "2":
			/*
			2       DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT
			21      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 1
			22      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 2
			23      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 3
			24      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 4
			25      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 5
			26      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 6
			29      AU TIC  DEMI-PENSIONNAIRE AU TICKET
			*/
			return "d/p";
			break;
		case "3":
			/*
			3       INTERN  INTERNE DANS L'ETABLISSEMENT
			31      INT 1J  INTERNE 1 JOUR
			32      INT 2J  INTERNE 2 JOURS
			33      INT 3J  INTERNE 3 JOURS
			34      INT 4J  INTERNE 4 JOURS
			35      INT 5J  INTERNE 5 JOURS
			36      INT 6J  INTERNE 6 JOURS
			38      1/2 IN  DEMI INTERNE
			39      INT WE  INTERNE WEEK END
			*/
			return "int.";
			break;
		case "4":
			// 4       IN.EX.  INTERNE EXTERNE
			return "i-e";
			break;
		case "5":
			// 5       IN.HEB  INTERNE HEBERGE
			return "int.";
			break;
		case "6":
			// 6       DP HOR  DEMI-PENSIONNAIRE HORS L'ETABLISSEMENT
			return "d/p";
			break;
		default:
			return "ERR";
			//return "d/p";
			break;
	}
}

/**
 * Renvoie les préférences d'un utilisateur pour un item en interrogeant la table preferences
 *
 * @param string $login Login de l'utilisateur
 * @param string $item Item recherché
 * @param string $default Valeur par défaut
 * @return string La valeur de l'item
 */
function getPref($login,$item,$default){
	$sql="SELECT value FROM preferences WHERE login='$login' AND name='$item'";
	$res_prefs=mysql_query($sql);

	if(mysql_num_rows($res_prefs)>0){
		$ligne=mysql_fetch_object($res_prefs);
		return $ligne->value;
	}
	else{
		return $default;
	}
}

/**
 * Enregistre les préférences d'un utilisateur pour un item dans la table preferences
 *
 * @param string $login Login de l'utilisateur
 * @param string $item Item recherché
 * @param string $valeur Valeur à enregistrer
 * @return boolean TRUE si tout c'est bien passé
 */
function savePref($login,$item,$valeur){
	$sql="SELECT value FROM preferences WHERE login='$login' AND name='$item'";
	$res_prefs=mysql_query($sql);

	if(mysql_num_rows($res_prefs)>0){
		$sql="UPDATE preferences SET value='$valeur' WHERE login='$login' AND name='$item';";
	}
	else{
		$sql="INSERT INTO preferences SET login='$login', name='$item', value='$valeur';";
	}
	$res=mysql_query($sql);
	if($res) {return TRUE;} else {return FALSE;}
}

/**
 * Renvoie l'ensemle des paramètres d'une classe en interrogeant la table classes_param
 *
 * @param string $id_classe Identifiant de la classe
 * @return array Tableau associatif des paramètres name=>value
 */
function getAllParamClasse($id_classe) {
	$sql="SELECT * FROM classes_param WHERE id_classe='$id_classe' ORDER BY name;";
	$res_param=mysql_query($sql);

	$tab_param=array();
	if(mysql_num_rows($res_param)>0){
		while($ligne=mysql_fetch_object($res_param)) {
			$tab_param[$ligne->name]=$ligne->value;
		}
	}

	return $tab_param;
}

/**
 * Renvoie les paramètres d'une classe pour un item en interrogeant la table classes_param
 *
 * @param string $id_classe Identifiant de la classe
 * @param string $item Item recherché
 * @param string $default Valeur par défaut
 * @return string La valeur de l'item
 */
function getParamClasse($id_classe,$item,$default) {
	$sql="SELECT value FROM classes_param WHERE id_classe='$id_classe' AND name='$item'";
	$res_param=mysql_query($sql);

	if(mysql_num_rows($res_param)>0){
		$ligne=mysql_fetch_object($res_param);
		return $ligne->value;
	}
	else{
		return $default;
	}
}

/**
 * Enregistre les paramètres d'une classe pour un item dans la table classes_param
 *
 * @param string $id_classe Identifiant de la classe
 * @param string $item Item recherché
 * @param string $valeur Valeur à enregistrer
 * @return boolean TRUE si tout c'est bien passé
 */
function saveParamClasse($id_classe,$item,$valeur) {
	$sql="SELECT value FROM classes_param WHERE id_classe='$id_classe' AND name='$item'";
	$res_param=mysql_query($sql);

	if(mysql_num_rows($res_param)>0){
		$sql="UPDATE classes_param SET value='$valeur' WHERE id_classe='$id_classe' AND name='$item';";
	}
	else{
		$sql="INSERT INTO classes_param SET id_classe='$id_classe', name='$item', value='$valeur';";
	}
	$res=mysql_query($sql);
	if($res) {return TRUE;} else {return FALSE;}
}

/**
 * Position horizontale initiale pour permettre un affichage sans superposition
 *
 * @global int $GLOBALS['$posDiv_infobulle']
 * @name $posDiv_infobulle
 */
$GLOBALS['$posDiv_infobulle'] = 0;

/**
 * 
 * @global array $GLOBALS['tabid_infobulle']
 * @name $tabid_infobulle
 */
$GLOBALS['tabid_infobulle'] = array();

/**
 * 
 * @global string $GLOBALS['unite_div_infobulle']
 * @name $unite_div_infobulle
 */
$GLOBALS['unite_div_infobulle'] = '';

/**
 * Les infobulles ne sont pas décallées si à oui
 * 
 * @global string $GLOBALS['pas_de_decalage_infobulle']
 * @name $pas_de_decalage_infobulle
 */
$GLOBALS['pas_de_decalage_infobulle'] = '';

/**
 * Ajoute un argument aux classes du div
 * 
 * @global string $GLOBALS['class_special_infobulle']
 * @name $class_special_infobulle
 */
$GLOBALS['class_special_infobulle'] = '';

/**
 * $bg_titre: Si $bg_titre est vide, on utilise la couleur par défaut correspondant à .infobulle_entete (défini dans style.css et éventuellement modifié dans style_screen_ajout.css)
 * 
 * $bg_texte: Si $bg_texte est vide, on utilise la couleur par défaut correspondant à .infobulle_corps (défini dans style.css et éventuellement modifié dans style_screen_ajout.css)
 * 
 * $hauteur: En mettant 0, on laisse le DIV s'adapter au contenu (se réduire/s'ajuster)
 * 
 * $bouton_close: S'il est affiché, c'est dans la barre de titre. Si la barre de titre n'est pas affichée, ce bouton ne peut pas être affiché.
		
 * 
 * @global type 
 * @global array 
 * @global type 
 * @global type 
 * @global type 
 * @global type 
 * @param string $id Identifiant du DIV conteneur
 * @param string $titre Texte du titre du DIV
 * @param string $bg_titre Couleur de fond de la barre de titre.
 * @param string $texte Texte du contenu du DIV
 * @param string $bg_texte Couleur de fond du DIV contenant le texte
 * @param int $largeur Largeur du DIV conteneur
 * @param int $hauteur Hauteur (minimale) du DIV conteneur
 * @param string $drag 'y' ou 'n' pour rendre le DIV draggable ('yy' rend même le corps de l'infobulle poignée de drag)
 * @param string $bouton_close 'y' ou 'n' pour afficher le bouton Close
 * @param string $survol_close 'y' ou 'n' pour refermer le DIV automatiquement lorsque le survol quitte le DIV
 * @param string $overflow 'y' ou 'n' activer l'overflow automatique sur la partie Texte. Il faut que $hauteur soit non NULLe
 * @param int $zindex_infobulle 
 * @return string 
 */
function creer_div_infobulle($id,$titre,$bg_titre,$texte,$bg_texte,$largeur,$hauteur,$drag,$bouton_close,$survol_close,$overflow,$zindex_infobulle=1){
	/*	
		
		$overflow:		
	*/
	global $posDiv_infobulle;
	global $tabid_infobulle;
	global $unite_div_infobulle;
	global $niveau_arbo;
	global $pas_de_decalage_infobulle;
	global $class_special_infobulle;

	$style_box="color: #000000; border: 1px solid #000000; padding: 0px; position: absolute; z-index:$zindex_infobulle;";
	
	$style_bar="color: #ffffff; cursor: move; font-weight: bold; padding: 0px;";
	$style_close="color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;";

	// On fait la liste des identifiants de DIV pour cacher les Div avec javascript en fin de chargement de la page (dans /lib/footer.inc.php).
	$tabid_infobulle[]=$id;

	// Conteneur:
	if($bg_texte==''){
		$div="<div id='$id' class='infobulle_corps div_conteneur_infobulle";
		if((isset($class_special_infobulle))&&($class_special_infobulle!='')) {$div.=" ".$class_special_infobulle;}
		$div.="' style='$style_box width: ".$largeur;
		if(is_numeric($largeur)) {
			$div.=$unite_div_infobulle;
		}
		$div.="; ";
	}
	else{
		$div="<div id='$id' ";
		if((isset($class_special_infobulle))&&($class_special_infobulle!='')) {$div.="class='".$class_special_infobulle." div_conteneur_infobulle' ";} else {$div.="class='div_conteneur_infobulle' ";}
		$div.="style='$style_box background-color: $bg_texte; width: ".$largeur;
		if(is_numeric($largeur)) {
			$div.=$unite_div_infobulle;
		}
		$div.="; ";
	}
	if(($hauteur!=0)||($hauteur!="")) {
		$div.="height: ".$hauteur;
		if(is_numeric($hauteur)) {
			$div.=$unite_div_infobulle;
		}
		$div.=$unite_div_infobulle."; ";
	}
	// Position horizontale initiale pour permettre un affichage sans superposition si Javascript est désactivé:
	$div.="left:".$posDiv_infobulle.$unite_div_infobulle.";";
	$div.="'>\n";


	// Barre de titre:
	// Elle n'est affichée que si le titre est non vide
	if($titre!=""){
		if($bg_titre==''){
			$div.="<div class='infobulle_entete' style='$style_bar width: ".$largeur;
			if(is_numeric($largeur)) {
				$div.=$unite_div_infobulle;
			}
			$div.=";'";
		}
		else{
			$div.="<div style='$style_bar background-color: $bg_titre; width: ".$largeur;
			if(is_numeric($largeur)) {
				$div.=$unite_div_infobulle;
			}
			$div.=";'";
		}
		if(($drag=="y")||($drag=="yy")){
			// Là on utilise les fonctions de http://www.brainjar.com stockées dans brainjar_drag.js
			$div.=" onmousedown=\"dragStart(event, '$id')\"";
		}
		$div.=">\n";

		if($bouton_close=="y"){
			//$div.="<div style='$style_close'><a href='#' onclick=\"cacher_div('$id'); return false;\">";
			$div.="<div style='$style_close'><a href=\"javascript:cacher_div('$id')\">";
			if(isset($niveau_arbo)&&$niveau_arbo==0){
				$div.="<img src='./images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			}
			else if(isset($niveau_arbo)&&$niveau_arbo==1) {
				$div.="<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			}
			else if(isset($niveau_arbo)&&$niveau_arbo==2) {
				$div.="<img src='../../images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			}
			else {
				$div.="<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			}
			$div.="</a></div>\n";
		}
		$div.="<span style='padding-left: 1px;'>\n";
		$div.=$titre."\n";
		$div.="</span>\n";
		$div.="</div>\n";
	}


	// Partie texte:
	//==================
	// 20110113
	$div.="<div id='".$id."_contenu_corps'";

	if($drag=="yy"){
		// Là on utilise les fonctions de http://www.brainjar.com stockées dans brainjar_drag.js
		$div.=" onmousedown=\"dragStart(event, '$id')\"";
	}

	//==================
	if($survol_close=="y"){
		// On referme le DIV lorsque la souris quitte la zone de texte.
		$div.=" onmouseout=\"cacher_div('$id');\"";
	}
	$div.=">\n";
	if(($overflow=='y')&&(($hauteur!=0)||($hauteur!=""))) {
		$hauteur_hors_titre=$hauteur-1; // Le calcul n'est correct que dans le cas où l'unité est 'em'
		$div.="<div style='width: ".$largeur;
		if(is_numeric($largeur)) {
			$div.=$unite_div_infobulle;
		}
		$div.="; height: ".$hauteur_hors_titre;
		if(is_numeric($hauteur)) {
			$div.=$unite_div_infobulle;
		}
		$div.="; overflow: auto;'>\n";
		$div.="<div style='padding-left: 1px;'>\n";
		$div.=$texte;
		$div.="</div>\n";
		$div.="</div>\n";
	}
	else{
		$div.="<div style='padding-left: 1px;'>\n";
		$div.=$texte;
		$div.="</div>\n";
	}
	$div.="</div>\n";

	$div.="</div>\n";

	// Les div vont s'afficher côte à côte sans superposition en bas de page si JavaScript est désactivé:
	if (isset($pas_de_decalage_infobulle) AND $pas_de_decalage_infobulle == "oui") {
		// on ne décale pas les div des infobulles
		$posDiv_infobulle = $posDiv_infobulle;
	}else{
		$posDiv_infobulle = $posDiv_infobulle+$largeur;
	}

	return $div;
}

/**
 * tableau des variables transmises d'une page à l'autre
 * 
 * @global array $GLOBALS['debug_var_count']
 * @name $debug_var_count
 */
$GLOBALS['debug_var_count']=array();

/**
 * indice de la variable transmise
 * 
 * @global int $GLOBALS['cpt_debug_debug_var']
 * @name $cpt_debug_debug_var
 */
$GLOBALS['cpt_debug_debug_var']=0;

/**
 * Affiche les variables transmises d'une page à l'autre: GET, POST, SERVER et SESSION
 *
 * @global array
 * @global int
 */
$debug_var_count=array();
$cpt_debug_debug_var=0;
function debug_var() {
	global $debug_var_count;
	global $cpt_debug_debug_var;

	$debug_var_count['POST']=0;
	$debug_var_count['GET']=0;
	$debug_var_count['SESSION']=0;
	$debug_var_count['SERVER']=0;

	$debug_var_count['COOKIE']=0;

	$debug_var_count['FILES']=0;

	// Fonction destinée à afficher les variables transmises d'une page à l'autre: GET, POST et SESSION
	echo "<div style='border: 1px solid black; background-color: white; color: black;'>\n";

	$cpt_debug_debug_var=0;

	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p><strong>Variables transmises en POST, GET, SESSION,...</strong> (<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)</p>\n";

	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;

	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables envoyées en POST: ";
	if(count($_POST)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;

	echo "<script type='text/javascript'>
	tab_etat_debug_var=new Array();
	function affiche_debug_var(id,mode) {
		if(document.getElementById(id)) {
			if(mode==1) {
				document.getElementById(id).style.display='';
			}
			else {
				document.getElementById(id).style.display='none';
			}
		}
	}
</script>\n";


/**
 * Affiche un tableau des valeurs de GET, POST, SERVER ou SESSION
 *
 * @global int 
 * @global array 
 * @param type $chaine_tab_niv1
 * @param type $tableau
 * @param type $pref_chaine 
 */
	function tab_debug_var($chaine_tab_niv1,$tableau,$pref_chaine) {
		global $cpt_debug_debug_var;
		global $debug_var_count;

		echo " (<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)\n";

		echo "<table id='container_debug_var_$cpt_debug_debug_var' summary=\"Tableau de debug\">\n";
		foreach($tableau as $post => $val) {
			echo "<tr><td valign='top'>".$pref_chaine."['".$post."']=</td><td>";

			if(is_array($tableau[$post])) {
				$cpt_debug_debug_var++;

				echo "Array";
				tab_debug_var($chaine_tab_niv1,$tableau[$post],$pref_chaine.'['.$post.']');

				$cpt_debug_debug_var++;
			}
			elseif(isset($debug_var_count[$chaine_tab_niv1])) {
				echo $val;
				$debug_var_count[$chaine_tab_niv1]++;
			}

			echo "</td></tr>\n";
		}
		echo "</table>\n";
	}


	echo "<table summary=\"Tableau de debug\">\n";
	foreach($_POST as $post => $val) {
		echo "<tr><td valign='top'>\$_POST['".$post."']=</td><td>";

		if(is_array($_POST[$post])) {
			echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>Array\n";
			tab_debug_var('POST',$_POST[$post],'$_POST['.$post.']');

			$cpt_debug_debug_var++;
		}
		else {
			echo $val;
			$debug_var_count['POST']++;
		}

		echo "</td></tr>\n";
	}
	echo "</table>\n";

	echo "<p>Nombre de valeurs en POST: <b>".$debug_var_count['POST']."</b></p>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables envoyées en GET: ";
	if(count($_GET)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;
	echo "<table summary=\"Tableau de debug sur GET\">";
	foreach($_GET as $get => $val){
		
		echo "<tr><td valign='top'>\$_GET['".$get."']=</td><td>";

		if(is_array($_GET[$get])) {
			echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>Array\n";
			tab_debug_var('GET',$_GET[$get],'$_GET['.$get.']');

			$cpt_debug_debug_var++;
		}
		else {
			echo $val;
			$debug_var_count['GET']++;
		}

		echo "</td></tr>\n";
	}
	echo "</table>\n";

	echo "<p>Nombre de valeurs en GET: <b>".$debug_var_count['GET']."</b></p>\n";

	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables envoyées en SESSION: ";
	if(count($_SESSION)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;
	echo "<table summary=\"Tableau de debug sur SESSION\">";
	foreach($_SESSION as $variable => $val){
		
		echo "<tr><td valign='top'>\$_SESSION['".$variable."']=</td><td>";
		if(is_array($_SESSION[$variable])) {
			echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>Array\n";
			tab_debug_var('SESSION',$_SESSION[$variable],'$_SESSION['.$variable.']');

			$cpt_debug_debug_var++;
		}
		else {
			echo $val;
			$debug_var_count['SESSION']++;
		}
		echo "</td></tr>\n";

	}
	echo "</table>\n";

	echo "<p>Nombre de valeurs en SESSION: <b>".$debug_var_count['SESSION']."</b></p>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables envoyées en SERVER: ";
	if(count($_SERVER)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;
	echo "<table summary=\"Tableau de debug sur SERVER\">";
	foreach($_SERVER as $variable => $valeur){
		echo "<tr><td>\$_SERVER['".$variable."']=</td><td>".$valeur."</td></tr>\n";
	}
	echo "</table>\n";

	echo "<p>Nombre de valeurs en SERVER: <b>".$debug_var_count['SERVER']."</b></p>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables envoyées en FILES: ";
	if((!isset($_FILES))||(count($_FILES)==0)) {
		echo "aucune";
	}
	else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	if((isset($_FILES))&&(count($_FILES)>0)) {
		echo "<blockquote>\n";
		echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
		$cpt_debug_debug_var++;

		echo "<table summary=\"Tableau de debug\">\n";
		foreach($_FILES as $key => $val) {
			echo "<tr><td valign='top'>\$_FILES['".$key."']=</td><td>";
	
			if(is_array($_FILES[$key])) {
				echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>Array\n";
				tab_debug_var('FILES',$_FILES[$key],'$_FILES['.$key.']');
	
				$cpt_debug_debug_var++;
			}
			else {
				echo $val;
			}

			echo "</td></tr>\n";
		}
		echo "</table>\n";
	
		echo "<p>Nombre de valeurs en FILES: <b>".$debug_var_count['FILES']."</b></p>\n";
		echo "</div>\n";
		echo "</blockquote>\n";
	}

	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables COOKIES: ";
	if(count($_COOKIE)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;
	echo "<table summary=\"Tableau de debug sur COOKIE\">";
	foreach($_COOKIE as $variable => $val){

		echo "<tr><td valign='top'>\$_COOKIE['".$variable."']=</td><td>";

		if(is_array($val)) {
			echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>Array\n";
			tab_debug_var('COOKIE',$val,'$_COOKIE['.$variable.']');

			$cpt_debug_debug_var++;
		}
		else {
			echo $val;
			$debug_var_count['COOKIE']++;
		}

		echo "</td></tr>\n";
	}
	echo "</table>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<script type='text/javascript'>
	// On masque le cadre de debug au chargement:
	//affiche_debug_var('container_debug_var',var_debug_var_etat);

	//for(i=0;i<tab_etat_debug_var.length;i++) {
	for(i=0;i<$cpt_debug_debug_var;i++) {
		if(document.getElementById('container_debug_var_'+i)) {
			affiche_debug_var('container_debug_var_'+i,-1);
		}
		// Variable destinée à alterner affichage/masquage
		tab_etat_debug_var[i]=-1;
	}
</script>\n";

	echo "</div>\n";
	echo "</div>\n";
}

/**
 *permet de vérifier si tel statut peut avoir accès à l'EdT en fonction des settings de l'admin
 * 
 * @param string $statut Statut testé
 * @return string yes si peut avoir accès à l'EdT, no sinon
 * @see getSettingValue()
 */
function param_edt($statut){
		$verif = "";
	if ($statut == "administrateur") {
		$verif = getSettingValue("autorise_edt_admin");
	} elseif ($statut == "professeur" OR $statut == "scolarite" OR $statut == "cpe" OR $statut == "secours" OR $statut == "autre") {
		$verif = getSettingValue("autorise_edt_tous");
	} elseif ($statut = "eleve" OR $statut = "responsable") {
		$verif = getSettingValue("autorise_edt_eleve");
	} else {
		$verif = "";
	}
	// On vérifie $verif et on renvoie le return
	if ($verif == "y" or $verif == "yes") {
		return "yes";
	} else {
		return "no";
	}
}

/**
 * Le message à afficher
 * 
 * @global string $GLOBALS['themessage']
 * @name $themessage
 */
$GLOBALS['themessage'] = '';

/**
 * Affiche un fenêtre de confirmation via javascript
 * 
 * Ajoute un attribut onclick à une balise pour appeler une fonction javascript contenant le message
 *
 * @global string
 * @return  string l'attribut onclick ou vide
 */
function insert_confirm_abandon(){
	global $themessage;

	if(isset($themessage)) {
		if($themessage!="") {
			return " onclick=\"return confirm_abandon(this, change, '$themessage')\" ";
		}
		else{
			return "";
		}
	}
	else{
		return "";
	}
}

/**
 * Largeur maximum désirée
 * 
 * @global int $GLOBALS['photo_largeur_max']
 * @name $photo_largeur_max
 */
$GLOBALS['photo_largeur_max'] = 0;

/**
 * Hauteur maximum désirée;
 * 
 * @global int $GLOBALS['photo_hauteur_max']
 * @name $photo_hauteur_max
 */
$GLOBALS['photo_hauteur_max'] = 0;

/**
 * Renvoie le nom d'une classe à partir de son Id
 *
 * @param int $id_classe Id de la classe recherchée
 * @return type nom de la classe (classe.classes)
 */
function get_class_from_id($id_classe) {
	$sql="SELECT classe FROM classes c WHERE id='$id_classe';";
	$res_class=mysql_query($sql);

	if(mysql_num_rows($res_class)>0){
		$lig_tmp=mysql_fetch_object($res_class);
		$classe=$lig_tmp->classe;
		return $classe;
	}
	else{
		return FALSE;
	}
}




/*
function fdebug_mail_connexion($texte){
	// Passer la variable à "y" pour activer le remplissage du fichier de debug pour calcule_moyenne()
	$local_debug="n";
	if($local_debug=="y") {
		$fich=fopen("/tmp/mail_connexion.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}
*/

/**
 *
 * @global string  
 */
function mail_connexion() {
	global $active_hostbyaddr;

	$test_envoi_mail=getSettingValue("envoi_mail_connexion");

	//$date = strftime("%Y-%m-%d %H:%M:%S");
	//$date = ucfirst(strftime("%A %d-%m-%Y à %H:%M:%S"));
	//fdebug_mail_connexion("\$_SESSION['login']=".$_SESSION['login']."\n\$test_envoi_mail=$test_envoi_mail\n\$date=$date\n====================\n");

	if($test_envoi_mail=="y") {
		$user_login = $_SESSION['login'];

		$sql="SELECT nom,prenom,email FROM utilisateurs WHERE login='$user_login';";
		$res_user=mysql_query($sql);
		if (mysql_num_rows($res_user)>0) {
			$lig_user=mysql_fetch_object($res_user);

			if(check_mail($lig_user->email)) {
				$adresse_ip = $_SERVER['REMOTE_ADDR'];
				$date = ucfirst(strftime("%A %d-%m-%Y à %H:%M:%S"));

				if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
					$result_hostbyaddr = " - ".@gethostbyaddr($adresse_ip);
				}
				else if($active_hostbyaddr == "no_local") {
					if ((mb_substr($adresse_ip,0,3) == 127) or (mb_substr($adresse_ip,0,3) == 10.) or (mb_substr($adresse_ip,0,7) == 192.168)) {
						$result_hostbyaddr = "";
					}
					else{
						$tabip=explode(".",$adresse_ip);
						if(($tabip[0]==172)&&($tabip[1]>=16)&&($tabip[1]<=31)) {
							$result_hostbyaddr = "";
						}
						else{
							$result_hostbyaddr = " - ".@gethostbyaddr($adresse_ip);
						}
					}
				}
				else{
					$result_hostbyaddr = "";
				}

				$message = "** Mail connexion Gepi **\n\n";
				$message .= "\n";
				$message .= "Vous (*) vous êtes connecté à GEPI :\n\n";
				$message .= "Identité                : ".mb_strtoupper($lig_user->nom)." ".ucfirst(mb_strtolower($lig_user->prenom))."\n";
				$message .= "Login                   : ".$user_login."\n";
				$message .= "Date                    : ".$date."\n";
				$message .= "Origine de la connexion : ".$adresse_ip."\n";
				if($result_hostbyaddr!="") {
					$message .= "Adresse IP résolue en   : ".$result_hostbyaddr."\n";
				}
				$message .= "\n";
				$message .= "Ce message, s'il vous parvient alors que vous ne vous êtes pas connecté à la date/heure indiquée, est susceptible d'indiquer que votre identité a pu être usurpée.\nVous devriez contrôler vos données, changer votre mot de passe et avertir l'administrateur (et/ou l'administration de l'établissement) pour qu'il puisse prendre les mesures appropriées.\n";
				$message .= "\n";
				$message .= "(*) Vous ou une personne tentant d'usurper votre identité.\n";

				// On envoie le mail
				//fdebug_mail_connexion("\$message=$message\n====================\n");
				$destinataire=$lig_user->email;
				$sujet="GEPI : Connexion $date";
				envoi_mail($sujet, $message, $destinataire);
			}
		}
	}
}

/**
 * Envoi un courriel à un utilisateur en cas de connexion avec son compte
 *
 * @global string
 * @param string $sujet Sujet du message
 * @param string $texte Texte du message
 * @param type $informer_admin Envoi aussi un courriel à l'administrateur si pas à 'n'
 * @see envoi_mail()
 * @see getSettingValue()
 */
function mail_alerte($sujet,$texte,$informer_admin='n') {
	global $active_hostbyaddr;

	$user_login = $_SESSION['login'];

	$sql="SELECT nom,prenom,email FROM utilisateurs WHERE login='$user_login';";
	$res_user=mysql_query($sql);
	if (mysql_num_rows($res_user)>0) {
		$lig_user=mysql_fetch_object($res_user);

		$adresse_ip = $_SERVER['REMOTE_ADDR'];
		//$date = strftime("%Y-%m-%d %H:%M:%S");
		$date = ucfirst(strftime("%A %d-%m-%Y à %H:%M:%S"));
		//$url = parse_url($_SERVER['REQUEST_URI']);

		if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
			$result_hostbyaddr = " - ".@gethostbyaddr($adresse_ip);
		}
		else if($active_hostbyaddr == "no_local") {
			if ((mb_substr($adresse_ip,0,3) == 127) or (mb_substr($adresse_ip,0,3) == 10.) or (mb_substr($adresse_ip,0,7) == 192.168)) {
				$result_hostbyaddr = "";
			}
			else{
				$tabip=explode(".",$adresse_ip);
				if(($tabip[0]==172)&&($tabip[1]>=16)&&($tabip[1]<=31)) {
					$result_hostbyaddr = "";
				}
				else{
					$result_hostbyaddr = " - ".@gethostbyaddr($adresse_ip);
				}
			}
		}
		else{
			$result_hostbyaddr = "";
		}


		//$message = "** Mail connexion Gepi **\n\n";
		$message=$texte;
		$message .= "\n";
		$message .= "Vous (*) vous êtes connecté à GEPI :\n\n";
		$message .= "Identité                : ".mb_strtoupper($lig_user->nom)." ".ucfirst(mb_strtolower($lig_user->prenom))."\n";
		$message .= "Login                   : ".$user_login."\n";
		$message .= "Date                    : ".$date."\n";
		$message .= "Origine de la connexion : ".$adresse_ip."\n";
		if($result_hostbyaddr!="") {
			$message .= "Adresse IP résolue en   : ".$result_hostbyaddr."\n";
		}
		$message .= "\n";
		$message .= "Ce message, s'il vous parvient alors que vous ne vous êtes pas connecté à la date/heure indiquée, est susceptible d'indiquer que votre identité a pu être usurpée.\nVous devriez contrôler vos données, changer votre mot de passe et avertir l'administrateur (et/ou l'administration de l'établissement) pour qu'il puisse prendre les mesures appropriées.\n";
		$message .= "\n";
		$message .= "(*) Vous ou une personne tentant d'usurper votre identité.\n";

		$ajout="";
		if(($informer_admin!='n')&&(getSettingValue("gepiAdminAdress")!='')) {
			$ajout="Bcc: ".getSettingValue("gepiAdminAdress")."\r\n";
		}

		// On envoie le mail
		//fdebug_mail_connexion("\$message=$message\n====================\n");

		$destinataire=$lig_user->email;
		$sujet="GEPI : $sujet $date";
		envoi_mail($sujet, $message, $destinataire, $ajout);

	}
}

/**
 * Formate un texte
 * 
 * - Si le texte contient des < et >, on affiche tel quel
 * - Sinon, on transforme les retours à la ligne en <br />
 *
 * @param string $texte Le texte à formater
 * @return string Le texte formaté
 */
function texte_html_ou_pas($texte){
	if((strstr($texte,">"))||(strstr($texte,"<"))){
		$retour=$texte;
	}
	else{
		$retour=nl2br($texte);
	}
	return $retour;
}

/**
 * Activer le mode debug, "y" pour oui
 *
 * @global string $GLOBALS['debug']
 * @name $debug
 */
$GLOBALS['debug'] = '';

/**
 * 
 *
 * @global array $GLOBALS['tab_instant']
 * @name $tab_instant
 */
$GLOBALS['tab_instant'] = array();


/**
 * Met une date en français
 *
 * @return text La date formatée 
 */
function get_date_php() {
	$eng_words = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$french_words = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
	$date_str = date('l').' '.date('d').' '.date('F').' '.date('Y');
	$date_str = str_replace($eng_words, $french_words, $date_str);
	return $date_str;
}

/**
 * Met en forme un prénom
 *
 * @param type $prenom Le prénom à traiter
 * @return type Le prénom traité
 */
function casse_prenom($prenom) {
	$tab=explode("-",$prenom);

	$retour="";
	for($i=0;$i<count($tab);$i++) {
		if($i>0) {
			$retour.="-";
		}
		$tab[$i]=casse_mot($tab[$i],'majf2');
		$retour.=$tab[$i];
	}

	return $retour;
}

/**
 * Arrondi un nombre avec un certain nombre de chiffres après la virgule
 *
 * @param type $nombre Le nombre à convertir
 * @param type $nb_chiffre_apres_virgule
 * @return decimal Le nombre arrondi
 */
function nf($nombre,$nb_chiffre_apres_virgule=1) {
	// Formatage des nombres
	// Precision:
	// Pour être sûr d'avoir un entier
	$nb_chiffre_apres_virgule=floor($nb_chiffre_apres_virgule);
	if($nb_chiffre_apres_virgule<1) {
		$precision=0.1;
		$nb_chiffre_apres_virgule=0;
	}
	else {
		$precision=pow(10,-1*$nb_chiffre_apres_virgule);
	}

	if(($nombre=='')||($nombre=='-')) {
		$valeur=$nombre;
	}
	else {
		$nombre=strtr($nombre,",",".");
		$valeur=number_format(round($nombre/$precision)*$precision, $nb_chiffre_apres_virgule, ',', '');
	}
	return $valeur;
}



/**
 * Envoit les informations de debug dans un fichier si à 'fichier', vers l'écran sinon
 *
 * @global string $GLOBALS['mode_my_echo_debug']
 * @name $mode_my_echo_debug
 */
$GLOBALS['mode_my_echo_debug'] = '';

/**
 * Écrit les informations de debug si à 1
 *
 * @global int $GLOBALS['my_echo_debug']
 * @name $my_echo_debug
 */
$GLOBALS['my_echo_debug'] = NULL;

/**
 * Ecrit des informations de debug dans un fichier ou à l'écran
 * 
 * $dossier est à "/tmp" pour simplifier en debug sur une machine perso sous *nix,
 * Commenter la ligne au besoin
 * 
 * @global string 
 * @global int 
 * @global int
 * @param string $texte 
 * @see get_user_temp_directory()
 */
function my_echo_debug($texte) {
	global $mode_my_echo_debug, $my_echo_debug, $niveau_arbo;

	if($my_echo_debug==1) {
		if($mode_my_echo_debug!='fichier') {
			echo $texte;
		}
		else {
			$tempdir=get_user_temp_directory();
			if (isset($niveau_arbo) and ($niveau_arbo == "0")) {
				$points=".";
			}
			elseif (isset($niveau_arbo) and ($niveau_arbo == "2")) {
				$points="../..";
			}
			else {
				$points="..";
			}
			$dossier=$points."/temp/".$tempdir;

			// Pour simplifier en debug sur une machine perso sous *nix:
			$dossier="/tmp";

			$fichier=$dossier."/my_echo_debug_".date("Ymd_Hi").".txt";

			$f=fopen($fichier,"a+");
			fwrite($f,$texte);
			fclose($f);
		}
	}
}

/**
 * Retourne une chaine utf-8 avec la bonne casse
 * 
 * $mode
 * - 'maj'   -> tout en majuscules
 * - 'min'   -> tout en minuscules
 * - 'majf'  -> Première lettre en majuscule, le reste en minuscule
 * - 'majf2' -> Première lettre de tous les mots en majuscule, le reste en minuscule
 *
 * @param type $mot chaine à modifier
 * @param type $mode Mode de conversion
 * @return type chaine mise en forme
 */
function casse_mot($mot,$mode='maj') {
    
    if (function_exists('mb_convert_case')) {
    	if($mode=='maj') {
    		return mb_convert_case(($mot), MB_CASE_UPPER);
    	}
    	elseif($mode=='min') {
    		return mb_convert_case(($mot), MB_CASE_LOWER);
    	}
    	elseif($mode=='majf') {
    	    $temp = mb_convert_case(($mot), MB_CASE_LOWER);
    		return mb_convert_case(mb_substr($temp,0,1), MB_CASE_UPPER).mb_substr($temp,1);
    	}
    	elseif($mode=='majf2') {
    	    $temp = mb_convert_case(($mot), MB_CASE_LOWER);
    		return mb_convert_case(($temp), MB_CASE_TITLE);
    	}
    } else {
        $str = ensure_ascii($mot);
    	if($mode=='maj') {
    		return strtoupper($str);
    	}
    	elseif($mode=='min') {
    		return strtolower($str);
    	}
    	elseif($mode=='majf') {
    		if(mb_strlen($str)>1) {
    			return strtoupper(mb_substr($str,0,1)).strtolower(substr($str,1));
    		}
    		else {
    			return strtoupper($str);
    		}
    	}
    	elseif($mode=='majf2') {
    		$chaine="";
    		$tab=explode(" ",$str);
    		for($i=0;$i<count($tab);$i++) {
    			if($i>0) {$chaine.=" ";}
    			$tab2=explode("-",$tab[$i]);
    			for($j=0;$j<count($tab2);$j++) {
    				if($j>0) {$chaine.="-";}
    				if(mb_strlen($tab2[$j])>1) {
    					$chaine.=mb_strtoupper(mb_substr($tab2[$j],0,1)).strtolower(mb_substr($tab2[$j],1));
    				}
    				else {
    					$chaine.=mb_strtoupper($tab2[$j]);
    				}
    			}
    		}
    		return $chaine;
    	}
    }
    throw new Exception('Parametre '.$mode.' non reconnu');
}

/**
 * Retourne une chaine utf-8 passée en minuscules avec casse_mot()
 * Fonction destinée à remplacer rapidement les appels strtolower() dans les pages
 *
 * @param type $mot chaine à modifier
 * @return type chaine mise en forme
 */
function my_strtolower($mot) {
	return casse_mot($mot,'min');
}

/**
 * Retourne une chaine utf-8 passée en majuscules avec casse_mot()
 * Fonction destinée à remplacer rapidement les appels strtoupper() dans les pages
 *
 * @param type $mot chaine à modifier
 * @return type chaine mise en forme
 */
function my_strtoupper($mot) {
	return casse_mot($mot,'maj');
}

/**
 * Renvoie le nom et le prénom d'un élève
 * 
 * ou civilité nom prénom (non-élève) si ce n'est pas un élève
 *
 * @param string $login_ele Login de l'élève
 * @param string $mode si 'avec_classe' on retourne aussi la(les) classe(s)
 * @return string 
 * @see civ_nom_prenom()
 * @see get_class_from_ele_login()
 * @see casse_mot()
 */
function get_nom_prenom_eleve($login_ele,$mode='simple') {
	$sql="SELECT nom,prenom FROM eleves WHERE login='$login_ele';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		// Si ce n'est pas un élève, c'est peut-être un utilisateur prof, cpe, responsable,...
		$sql="SELECT 1=1 FROM utilisateurs WHERE login='$login_ele';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			return civ_nom_prenom($login_ele)." (non-élève)";
		}
		else {
			return "Elève inconnu ($login_ele)";
		}
	}
	else {
		$lig=mysql_fetch_object($res);

		$ajout="";
		if($mode=='avec_classe') {
			$tmp_tab_clas=get_class_from_ele_login($login_ele);
			if((isset($tmp_tab_clas['liste']))&&($tmp_tab_clas['liste']!='')) {
				$ajout=" (".$tmp_tab_clas['liste'].")";
			}
		}

		return casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2').$ajout;
	}
}

/**
 * Renvoie le nom et le prénom d'un élève
 *
 * @param string $ele_id ele_id de l'élève
 * @param string $mode si 'avec_classe' on retourne aussi la(les) classe(s)
 * @return string 
 * @see civ_nom_prenom()
 * @see get_class_from_ele_login()
 * @see casse_mot()
 */
function get_nom_prenom_eleve_from_ele_id($ele_id, $mode='simple') {
	$sql="SELECT login, nom,prenom FROM eleves WHERE ele_id='$ele_id';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		return "Elève inconnu ($ele_id)";
	}
	else {
		$lig=mysql_fetch_object($res);

		$ajout="";
		if($mode=='avec_classe') {
			$tmp_tab_clas=get_class_from_ele_login($lig->login);
			if((isset($tmp_tab_clas['liste']))&&($tmp_tab_clas['liste']!='')) {
				$ajout=" (".$tmp_tab_clas['liste'].")";
			}
		}

		return casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2').$ajout;
	}
}

/**
 * Retourne une commune à partir de son code insee
 * 
 * $mode :
 * - 0 -> la commune
 * - 1 -> la commune (<em>le département</em>)
 * - 2 -> la commune (le département)
 * 
 * @param string $code_commune_insee
 * @param int $mode
 * @return string La commune
 */
function get_commune($code_commune_insee,$mode){
	$retour="";

	if(strstr($code_commune_insee,'@')) {
		// On a affaire à une commune étrangère
		$tmp_tab=explode('@',$code_commune_insee);
		$sql="SELECT * FROM pays WHERE code_pays='$tmp_tab[0]';";
		//echo "$sql<br />";
		$res_pays=mysql_query($sql);
		if(mysql_num_rows($res_pays)==0) {
			$retour=stripslashes($tmp_tab[1])." ($tmp_tab[0])";
		}
		else {
			$lig_pays=mysql_fetch_object($res_pays);
			$retour=stripslashes($tmp_tab[1])." (".$lig_pays->nom_pays.")";
		}
	}
	else {
		$sql="SELECT * FROM communes WHERE code_commune_insee='$code_commune_insee';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
			if($mode==0) {
				$retour=$lig->commune;
			}
			elseif($mode==1) {
				$retour=$lig->commune." (<em>".$lig->departement."</em>)";
			}
			elseif($mode==2) {
				$retour=$lig->commune." (".$lig->departement.")";
			}
		}
	}
	return $retour;
}

/**
 * Renvoie civilite nom prénom d'un utilisateur avec compte (table 'utilisateurs')
 *
 * @param string $login Login de l'utilisateur recherché
 * @param string $mode si 'prenom' inverse le nom et le prénom
 * @param string $avec_statut avec affichage ou non du statut entre parenthèses
 *
 * @return string civilite nom prénom de l'utilisateur
 */
function civ_nom_prenom($login,$mode='prenom',$avec_statut="n") {
	$retour="";
	$sql="SELECT nom,prenom,civilite,statut FROM utilisateurs WHERE login='$login';";
	$res_user=mysql_query($sql);
	if (mysql_num_rows($res_user)>0) {
		$lig_user=mysql_fetch_object($res_user);
		if($lig_user->civilite!="") {
			$retour.=$lig_user->civilite." ";
		}
		if($mode=='prenom') {
			$retour.=my_strtoupper($lig_user->nom)." ".casse_mot($lig_user->prenom,'majf2');
		}
		else {
			// Initiale
			$retour.=my_strtoupper($lig_user->nom)." ".my_strtoupper(mb_substr($lig_user->prenom,0,1));
		}
		if($avec_statut=='y') {
			if($lig_user->statut=='autre') {
				$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
												WHERE du.login_user = '".$login."'
												AND du.id_statut = ds.id;";
				$res_statut=mysql_query($sql);
				if(mysql_num_rows($res_statut)>0) {
					$lig_statut=mysql_fetch_object($res_statut);
					$retour.=" ($lig_statut->nom_statut)";
				}
			}
			else {
				$retour.=" ($lig_user->statut)";
			}
		}
	}
	return $retour;
}

/**
 * Renvoie civilite nom prénom d'un responsable
 *
 * @param string $pers_id pers_id de l'utilisateur recherché
 * @param string $mode si 'prenom' inverse le nom et le prénom
 *
 * @return string civilite nom prénom de l'utilisateur
 */
function civ_nom_prenom_from_pers_id($pers_id,$mode='prenom') {
	$retour="";
	$sql="SELECT nom,prenom,civilite FROM resp_pers WHERE pers_id='$pers_id';";
	$res_user=mysql_query($sql);
	if (mysql_num_rows($res_user)>0) {
		$lig_user=mysql_fetch_object($res_user);
		if($lig_user->civilite!="") {
			$retour.=$lig_user->civilite." ";
		}
		if($mode=='prenom') {
			$retour.=my_strtoupper($lig_user->nom)." ".casse_mot($lig_user->prenom,'majf2');
		}
		else {
			// Initiale
			$retour.=my_strtoupper($lig_user->nom)." ".my_strtoupper(mb_substr($lig_user->prenom,0,1));
		}
	}
	return $retour;
}

/**
 *Enleve le numéro des titres numérotés ("1. Titre" -> "Titre")
 * 
 * Exemple :  "12. Titre"  donne "Titre"
 * @param string $texte Le titre de départ
 * @return string  Le titre formaté
 */
function supprimer_numero($texte) {
 return preg_replace(",^[[:space:]]*([0-9]+)([.)])[[:space:]]+,S","", $texte);
}


/**
 * Teste si style_screen_ajout.css existe et est accessible en écriture
 *
 * @return boolean TRUE si on peut écrire dans le fichier
 */
function test_ecriture_style_screen_ajout() {
	$nom_fichier='style_screen_ajout.css';
	$f=@fopen("../".$nom_fichier, "a+");
	if($f) {
		$ecriture=fwrite($f, "/* Test d'ecriture dans $nom_fichier */\n");
		fclose($f);
		if($ecriture) {return TRUE;} else {return FALSE;}
	}
	else {
		return FALSE;
	}
}

/**********************************************************************************************
 *                                  Fonctions Trombinoscope
 **********************************************************************************************/


/**
 * Ajoute au début d'un nom de fichier une chaîne 5 caractères pseudo alétaoires
 * le but étant d'empêcher l'accès aux photos élèves.
 *
 * Renvoie le nom de fichier modifié si la valeur '$alea_nom_photo' est définie
 * dans la table 'setting', sinon renvoie le nom de fichier inchangé.
 *
 * @param string $nom_photo le nom du fichier
 * @return string le nom du fichier éventuellement modifié
 * @see active_encodage_nom_photo()
 * 
 */
function encode_nom_photo($nom_photo) {
	if (!getSettingAOui('encodage_nom_photo')) return $nom_photo; // la valeur est déjà définie
	else return substr(md5(getSettingValue('alea_nom_photo').$nom_photo),0,5).$nom_photo;
}

/**
 * Renvoie le nom de la photo de l'élève ou du prof
 *
 * Renvoie NULL si :
 *
 * - le module trombinoscope n'est pas activé
 * - la photo n'existe pas.
 *
 * @param string $_elenoet_ou_login selon les cas, soit l'elenoet de l'élève soit le login du professeur
 * @param string $repertoire "eleves" ou "personnels"
 * @param int $arbo niveau d'aborescence (1 ou 2).
 * @return string Le chemin vers la photo ou NULL
 * @see getSettingValue()
 */
function nom_photo($_elenoet_ou_login,$repertoire="eleves",$arbo=1) {
	if ($arbo==2) {$chemin = "../";} else {$chemin = "";}
	if (($repertoire != "eleves") and ($repertoire != "personnels")) {
		return NULL;
		die();
	}
	if (getSettingValue("active_module_trombinoscopes")!='y') {
		return NULL;
		die();
	}
		$photo=NULL;

	// En multisite, on ajoute le répertoire RNE
	if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		  // On récupère le RNE de l'établissement
      $repertoire2=$_COOKIE['RNE']."/";
	}else{
	  $repertoire2="";
	}


	// Cas des élèves
	if ($repertoire == "eleves") {

		if($_elenoet_ou_login!='') {

			// on vérifie si la photo existe

			if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
				// En multisite, on recherche aussi avec les logins
				if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
					// On récupère le login de l'élève
					$sql = 'SELECT login FROM eleves WHERE elenoet = "'.$_elenoet_ou_login.'"';
					$query = mysql_query($sql);
					$_elenoet_ou_login = mysql_result($query, 0,'login');
				}
			}

			if(file_exists($chemin."../photos/".$repertoire2."eleves/".encode_nom_photo($_elenoet_ou_login).".jpg")) {
				$photo=$chemin."../photos/".$repertoire2."eleves/".encode_nom_photo($_elenoet_ou_login).".jpg";
			}
			else {
				if(file_exists($chemin."../photos/".$repertoire2."eleves/".sprintf("%05d",encode_nom_photo($_elenoet_ou_login)).".jpg")) {
					$photo=$chemin."../photos/".$repertoire2."eleves/".sprintf("%05d",encode_nom_photo($_elenoet_ou_login)).".jpg";
				} else {
					for($i=0;$i<5;$i++){
						if(mb_substr(encode_nom_photo($_elenoet_ou_login),$i,1)=="0"){
							$test_photo=mb_substr($_elenoet_ou_login,$i+1);
							if(($test_photo!='')&&(file_exists($chemin."../photos/".$repertoire2."eleves/".$test_photo.".jpg"))) {
								$photo=$chemin."../photos/".$repertoire2."eleves/".$test_photo.".jpg";
								break;
							}
						}
					}
				}
			}

		}
	}
	// Cas des non-élèves
	else {

		$_elenoet_ou_login = md5(mb_strtolower($_elenoet_ou_login));
			if(file_exists($chemin."../photos/".$repertoire2."personnels/$_elenoet_ou_login.jpg")){
				$photo=$chemin."../photos/".$repertoire2."personnels/$_elenoet_ou_login.jpg";
			} else {
				$photo = NULL;
		}
	}
	return $photo;
}


/**
 * Redimensionne un fichier photo JPG en conservant son ratio d'origine
 * Si les dimensions du fichier source sont plus petites que celles du
 * fichier destination alors le fichier source est inclus dans le fichier
 * destination afin de ne pas perdre en qualité suite à un agrandissement
 * de la photo
 *
 * @param string $file_source fichier à redimensionner
 * @param integer $largeur_destination largeur à obtenir
 * @param integer $$hauteur_destination hauteur à obtenir
 * @param integer $angle_rotation rotation à appliquer à l'image (facultatif)
 * @return true si redimensionnement OK, false sinon ou si inutile de redimenssionner
 */
function redim_photo($file_source,$largeur_destination,$hauteur_destination,$angle_rotation=0)
	{
	if (!is_file($file_source)) return false;
	$source=imagecreatefromjpeg($file_source);
	if ($source===false) return false;

	if ($angle_rotation!=0) $source=imagerotate($source,-$angle_rotation,0xFFFFFF);
	if ($source===false) return false;

	$destination=imagecreatetruecolor($largeur_destination,$hauteur_destination);
	if ($destination===false) return false;
	$blanc=imagecolorallocate($destination,0xFF,0xFF,0xFF);
	if ($blanc===false) return false;

	if (!imagefill($destination,0,0,$blanc)) return false;

	$largeur_source=imagesx($source);
	if ($largeur_source===false) return false;
	$hauteur_source=imagesy($source);
	if ($hauteur_source===false) return false;

	if ($largeur_source==0 || $hauteur_source==0) return false;
	if ($largeur_source==$largeur_destination && $hauteur_source==$hauteur_destination) return true;

	$ratio_lh_source=$largeur_source/$hauteur_source;
	$ratio_lh_destination=$largeur_destination/$hauteur_destination;
	
	if ($ratio_lh_source<$ratio_lh_destination)
		{
		$dest_l=(int)($hauteur_destination*$ratio_lh_source);
		if ($dest_l>$largeur_source) $dest_l=$largeur_source;
		$dest_x=(int)(($largeur_destination-$dest_l)/2);
		$dest_h=$hauteur_destination;
		if ($dest_h>$hauteur_source) $dest_h=$hauteur_source;
		$dest_y=(int)(($hauteur_destination-$dest_h)/2);
		}
	else
		{
		$dest_h=(int)($largeur_destination/$ratio_lh_source);
		if ($dest_h>$hauteur_source) $dest_h=$hauteur_source;
		$dest_y=(int)(($hauteur_destination-$dest_h)/2);
		$dest_l=$largeur_destination;
		if ($dest_l>$largeur_source) $dest_l=$largeur_source;
		$dest_x=(int)(($largeur_destination-$dest_l)/2);
		}

	if (!imagecopyresampled($destination,$source,$dest_x,$dest_y,0,0,$dest_l,$dest_h,$largeur_source,$hauteur_source)) return false;

	if (!imagejpeg($destination, $file_source,100)) return false;
	imagedestroy($destination);
	return true;
	}
/**********************************************************************************************
 *                               Fin Fonctions Trombinoscope
 **********************************************************************************************/

/**********************************************************************************************
 *                                   Fil d'Ariane
 **********************************************************************************************/
/**
 * gestion du fil d'ariane en remplissant le tableau $_SESSION['ariane']
 * @param string $lien page atteinte par le lien
 * @param string $texte texte à afficher dans le fil d'ariane
 * @return boolean True si tout s'est bien passé, False sinon
 */
function suivi_ariane($lien,$texte){
  if (!isset($_SESSION['ariane'])){
	$_SESSION['ariane']['lien'][] =$lien;
	$_SESSION['ariane']['texte'][] =$texte;
	return TRUE;
  }else{
	$trouve=FALSE;
	foreach ($_SESSION['ariane']['lien'] as $index=>$lienActuel){
	  if ($trouve){
		unset ($_SESSION['ariane']['lien'][$index]);
		unset ($_SESSION['ariane']['texte'][$index]);
	  }else{
		if ($lienActuel==$lien)
		  $trouve=TRUE;
	  }
	}
	unset ($index, $lienActuel);
	if (!$trouve){
	  $_SESSION['ariane']['lien'][] =$lien;
	  $_SESSION['ariane']['texte'][] =$texte;
	}
	  return TRUE;
  }
}

/**
 * Affiche le fil d'Ariane
 * 
 * une validation sera demandée en cas de modification de la page si validation est à TRUE 
 * et si le javascript est activé
 * @param <boolean> $validation validation si TRUE,
 * @param <texte> $themessage message à afficher lors de la confirmation
 */
//function affiche_ariane($validation= FALSE,$themessage="" ){
function affiche_ariane($validation= FALSE){
  global $themessage;
  if($themessage!="") {
    $validation=TRUE;
  }
  if (isset($_SESSION['ariane'])){
	echo "<p class='ariane'>";
	foreach ($_SESSION['ariane']['lien'] as $index=>$lienActuel){
	  if ($index!="0"){
		echo " &gt;&gt; ";
	  }
	  if ($validation){
	  echo "<a class='bold' href='".$lienActuel."' onclick='return confirm_abandon (this, change, \"".$themessage."\")' >";
	  } else {
	  echo "<a class='bold' href='".$lienActuel."' >";
	  }
		echo $_SESSION['ariane']['texte'][$index] ;
	  echo " </a>";
	}
	unset ($index,$lienActuel);
	echo "</p>";
  }
}
/**********************************************************************************************
 *                               Fin Fil d'Ariane
 **********************************************************************************************/
/**********************************************************************************************
 *                               Manipulation de fichiers
 **********************************************************************************************/

/**
 * Renvoie le chemin relatif pour remonter à la racine du site
 * @param int $niveau niveau dans l'arborescence
 * @return string chemin relatif vers la racine
 */
function path_niveau($niveau=1){
  switch ($niveau) {
	case 0:
	  $path = "./";
		  break;
	case 1:
	  $path = "../";
		  break;
	case 2:
	  $path = "../../";
	default:
	  $path = "../";
  }
  return $path;
}

/**
 * Crée une archive Zip des dossiers documents ou photos
 *
 * @param string $dossier_a_archiver limité à documents ou photos
 * @param int $niveau niveau dans l'arborescence de la page appelante, racine = 0
 * @return striung message d'erreur, vide si aucune erreur
 * @see cree_zip_archive_msg()
 */
function cree_zip_archive_avec_msg_erreur($dossier_a_archiver,$niveau=1) {
  $path = path_niveau();
  $dirname = "backup/".getSettingValue("backup_directory")."/";
  if (!defined('PCLZIP_TEMPORARY_DIR') || constant('PCLZIP_TEMPORARY_DIR')!=$path.$dirname) {
    @define( 'PCLZIP_TEMPORARY_DIR', $path.$dirname );
  }

  require_once($path.'lib/pclzip.lib.php');

  if (isset($dossier_a_archiver)) {
	$suffixe_zip="_le_".date("Y_m_d_\a_H\hi");
	switch ($dossier_a_archiver) {
	case "documents":
	  $chemin_stockage = $path.$dirname."_cdt".$suffixe_zip.".zip"; //l'endroit où sera stockée l'archive
	  $dossier_a_traiter = $path.'documents/'; //le dossier à traiter
	  $dossier_dans_archive = 'documents'; //le nom du dossier dans l'archive créée
	  break;
	case "photos":
	  $chemin_stockage = $path.$dirname."_photos".$suffixe_zip.".zip";
	  $dossier_a_traiter = $path.'photos/'; //le dossier à traiter
	  if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		if((isset($_COOKIE['RNE']))&&($_COOKIE['RNE']!='')) $dossier_a_traiter .=$_COOKIE['RNE']."/";
		else return "RNE invalide&nbsp;:&nbsp;".$_COOKIE['RNE'];
	  }
	  $dossier_dans_archive = 'photos'; //le nom du dossier dans l'archive créée
	  // Si l'encodage des noms de photos est activé on sauvegarde la valeur 'alea_nom_photo'
	  if (getSettingAOui('encodage_nom_photo'))
		{
		$fic_alea=fopen($dossier_a_traiter."alea_nom_photo.txt","w");
		fwrite($fic_alea,getSettingValue("alea_nom_photo"));
		fclose($fic_alea);
		}
	  break;
	default:
	  $chemin_stockage = '';
	}

	if ($chemin_stockage !='') {
	  $archive = new PclZip($chemin_stockage);
	  $v_list = $archive->create($dossier_a_traiter,
			  PCLZIP_OPT_REMOVE_PATH,$dossier_a_traiter,
			  PCLZIP_OPT_ADD_PATH, $dossier_dans_archive);
	  // Si l'encodage des noms de photos est activé on supprime le fichier alea_nom_photo.txt
	  if (getSettingAOui('encodage_nom_photo') && file_exists($dossier_a_traiter."alea_nom_photo.txt")) @unlink($dossier_a_traiter."alea_nom_photo.txt");
	  if ($v_list == 0) {
		 return "Erreur : ".$archive->errorInfo(TRUE);
	  }else {
		return "";
	  }
	}
  }
}


/**
 * Crée une archive Zip des dossiers documents ou photos
 *
 * @param string $dossier_a_archiver limité à documents ou photos
 * @param int $niveau niveau dans l'arborescence de la page appelante, racine = 0
 * @return boolean
 * @see cree_zip_archive_msg()
 */
function cree_zip_archive($dossier_a_archiver,$niveau=1) {
  return (cree_zip_archive_avec_msg_erreur($dossier_a_archiver,$niveau)=="")?TRUE:FALSE;
}

/**
 * Déplace un fichier de $source vers $dest
 * @param string $source : emplacement du fichier à déplacer
 * @param string $dest : Nouvel emplacement du fichier
 * @return bool
 */
function deplacer_upload($source, $dest) {
    $ok = @copy($source, $dest);
    if (!$ok) $ok = (@move_uploaded_file($source, $dest));
    return $ok;
}

/**
 * Télécharge un fichier dans $dirname après avoir nettoyer son nom 
 * 
 * si tout se passe bien :
 * $sav_file['name']=my_ereg_replace("[^.a-zA-Z0-9_=-]+", "_", $sav_file['name'])
 * @param array $sav_file tableau de type $_FILE["nom_du_fichier"]
 * @param string $dirname
 * @return string ok ou message d'erreur
 * @see deplacer_upload()
 */
function telecharge_fichier($sav_file,$dirname,$ext="",$type=""){
  if (!isset($sav_file['tmp_name']) or ($sav_file['tmp_name'] =='')) {
	return ("Erreur de téléchargement.");
  } else if (!file_exists($sav_file['tmp_name'])) {
	return ("Erreur de téléchargement 2.");
  } else if (($ext!="") && (!preg_match('/'.$ext.'$/i',$sav_file['name']))){
	return ("Erreur : seuls les fichiers ayant l'extension .".$ext." sont autorisés.");
  //} else if ($sav_file['type']!=$type ){
  } else if (($type!="") && (strripos($type,$sav_file['type'])===false)) {
	return ("Erreur : seuls les fichiers de type '".$type."' sont autorisés<br />Votre fichier est de type ".$sav_file['type']);
  } else {
	$nom_corrige = preg_replace("/[^.a-zA-Z0-9_=-]+/", "_", $sav_file['name']);
	if (!deplacer_upload($sav_file['tmp_name'], $dirname."/".$nom_corrige)) {
	  return ("Problème de transfert : le fichier n'a pas pu être transféré sur le répertoire ".$dirname);
	} else {
	  $sav_file['name']=$nom_corrige;
	  return ("ok");
	}
  }
}

/**
 * Extrait une archive Zip
 * @param string $fichier le nom du fichier à dézipper
 * @param string $repertoire le répertoire de destination
 * @param int $niveau niveau dans l'arborescence de la page appelante
 * @return string ok ou message d'erreur
 */
function dezip_PclZip_fichier($fichier,$repertoire,$niveau=1){
  $path = path_niveau();
  require_once($path.'lib/pclzip.lib.php');
  $archive = new PclZip($fichier);
  //if ($archive->extract() == 0) {
if ($archive->extract(PCLZIP_OPT_PATH, $repertoire) == 0) {
	return "Une erreur a été rencontrée lors de l'extraction du fichier zip";
  }else {
	return "ok";
  }
}

/**********************************************************************************************
 *                              Fin Manipulation de fichiers
 **********************************************************************************************/
/**
 * Vérifie qu'un statut à les droits sur une page
 *
 * @param string $id le lien vers la page à tester
 * @param string $statut Le statut à tester
 * @return int  
 */
function check_droit_acces($id,$statut) {
    $tab_id = explode("?",$id);
    $query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
    $droit = @mysql_result($query_droits, 0, $statut);
    if ($droit == "V") {
        return "1";
    } else {
        return "0";
    }
}

/**
 * Renvoie des balises option contenant les élèves
 * 
 * Renvoie une chaine contenant une balise option par élève à insérer dans un select
 *
 * @param int $id_classe Id de la classe
 * @param string $login_eleve_courant Login de l'élève qui sera sélectionné par défaut
 * @param request $sql_ele requête à utiliser
 * @return string Les balises options
 */
function lignes_options_select_eleve($id_classe,$login_eleve_courant,$sql_ele="") {
	if($sql_ele!="") {
		$sql=$sql_ele;
	}
	else {
		$sql="SELECT DISTINCT jec.login,e.nom,e.prenom FROM j_eleves_classes jec, eleves e
							WHERE jec.login=e.login AND
								jec.id_classe='$id_classe'
							ORDER BY e.nom,e.prenom";
	}
	//echo "$sql<br />";
	//echo "\$login_eleve=$login_eleve<br />";
	$res_ele_tmp=mysql_query($sql);
	$chaine_options_login_eleves="";
	$cpt_eleve=0;
	$num_eleve=-1;
	if(mysql_num_rows($res_ele_tmp)>0){
		$login_eleve_prec=0;
		$login_eleve_suiv=0;
		$temoin_tmp=0;
		while($lig_ele_tmp=mysql_fetch_object($res_ele_tmp)){
			if($lig_ele_tmp->login==$login_eleve_courant){
				$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login' selected='selected'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
	
				$num_eleve=$cpt_eleve;
	
				$temoin_tmp=1;
				if($lig_ele_tmp=mysql_fetch_object($res_ele_tmp)){
					$login_eleve_suiv=$lig_ele_tmp->login;
					$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
				}
				else{
					$login_eleve_suiv=0;
				}
			}
			else{
				$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
			}
	
			if($temoin_tmp==0){
				$login_eleve_prec=$lig_ele_tmp->login;
			}
			$cpt_eleve++;
		}
	}

	return $chaine_options_login_eleves;
}

/**
 *Vérifie si un utilisateur est prof principal (gepi_prof_suivi)
 * 
 * $id_classe : identifiant de la classe (si vide, on teste juste si le prof est PP 
 * (éventuellement pour un élève particulier si login_eleve est non vide))
 * 
 * $login_eleve : login de l'élève à tester (si vide, on teste juste si le prof est PP 
 * (éventuellement pour la classe si id_classe est non vide))
 * 
 * @param type string $login_prof login de l'utilisateur à tester
 * @param type integer $id_classe identifiant de la classe
 * @param type string $login_eleve login de l'élève
 * @return boolean 
 */
function is_pp($login_prof,$id_classe="",$login_eleve="", $num_periode="") {
	$retour=FALSE;

	if($login_eleve=='') {
		$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE ";
		if($id_classe!="") {$sql.="id_classe='$id_classe' AND ";}
		$sql.="professeur='$login_prof';";
	}
	else {
		$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE ";
		if($id_classe!="") {$sql.="id_classe='$id_classe' AND ";}
		$sql.="professeur='$login_prof' AND login='$login_eleve';";
	}
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {$retour=TRUE;}

	return $retour;
}

/**
 * Récupère le tableau des classes/élèves dont un utilisateur est prof principal (gepi_prof_suivi)
 * 
 * @param type string $login_prof login de l'utilisateur à tester
 *
 * @return array Tableau d'indices ['login'][] et ['id_classe'][] et ['classe'][]
 */
function get_tab_ele_clas_pp($login_prof) {
	$tab=array();
	$tab['login']=array();
	$tab['id_classe']=array();

	$sql="SELECT DISTINCT jep.login FROM j_eleves_professeurs jep, eleves e, j_eleves_classes jec, classes c WHERE jep.professeur='$login_prof' AND jep.login=e.login AND jec.login=e.login AND jec.id_classe=c.id ORDER BY c.classe, e.nom, e.prenom;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab['login'][]=$lig->login;
		}
	}

	$sql="SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_professeurs jep, j_eleves_classes jec, classes c WHERE jep.professeur='$login_prof' AND jep.login=jec.login AND jec.id_classe=c.id ORDER BY c.classe;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab['id_classe'][]=$lig->id_classe;
			$tab['classe'][]=$lig->classe;
		}
	}

	return $tab;
}

/**
 *Vérifie si un utilisateur est cpe de l'élève choisi ou de la classe choisie
 * 
 * $login_eleve : login de l'élève à tester (si vide, on teste juste si le cpe 
 *                est responsable d'au moins un élève de la classe $id_classe
 * 
 * $id_classe : identifiant de la classe
 *              (pris en compte seulement si le login_eleve est vide)
 * 
 * @param type $login_cpe login de l'utilisateur à tester
 * @param type $id_classe identifiant de la classe
 * @param type $login_eleve login de l'élève
 * @return boolean 
 */
function is_cpe($login_cpe,$id_classe="",$login_eleve="") {
	$retour=FALSE;
	if($login_eleve=='') {
		$sql="SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='$login_cpe' AND e_login='$login_eleve';";
	}
	elseif($id_classe!='') {
		$sql="SELECT 1=1 FROM j_eleves_cpe jecpe, j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND jec.login=jecpe.e_login AND jecpe.cpe_login='$login_cpe';";
	}
        if(isset($sql)) {
            $test=mysql_query($sql);
            if(mysql_num_rows($test)>0) {$retour=TRUE;}
        }
	return $retour;
}


/**
 * Récupère le tableau des login CPE associés à une classe
 * 
 * $id_classe : identifiant de la classe
 *              (si vide, on récupère tous les CPE de l'établissement)
 * 
 * $login_eleve : login de l'élève à tester (si vide, on teste juste si le prof est PP 
 * (éventuellement pour la classe si id_classe est non vide))
 * 
 * @param type $id_classe identifiant de la classe
 * @return array
 */
function tab_cpe($id_classe='') {
	$tab=array();
	if((is_numeric($id_classe))&&($id_classe>0)) {
		$sql="SELECT DISTINCT u.login FROM utilisateurs u, j_eleves_cpe jecpe, j_eleves_classes jec WHERE u.statut='cpe' AND u.etat='actif' AND u.login=jecpe.cpe_login AND jec.login=jecpe.e_login AND jec.id_classe='$id_classe' ORDER BY u.nom, u.prenom;";
	}
	else {
		$sql="SELECT DISTINCT u.login FROM utilisateurs WHERE statut='cpe' AND etat='actif' ORDER BY nom, prenom;";
	}
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab[]=$lig->login;
		}
	}
	return $tab;
}

/**
 * Vérifie qu'un utilisateur a le droit de voir la page en lien
 *
 * @param string $id l'adresse de la page telle qu'enregistrée dans la table droits
 * @param string $statut le statut de l'utilisateur
 * @return entier 1 si l'utilisateur a le droit de voir la page 0 sinon
 * @todo Je l'ai déjà vu au-dessus dans le fichier
 */
function acces($id,$statut) 
{ 
	if ($_SESSION['statut']!='autre') {
		$tab_id = explode("?",$id);
		$query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
		$droit = @mysql_result($query_droits, 0, $statut);
		if ($droit == "V") {
			return "1";
		} else {
			return "0";
		}
	} else {
		$sql="SELECT ds.autorisation FROM `droits_speciaux` ds,  `droits_utilisateurs` du
					WHERE (ds.nom_fichier='".$id."'
						AND ds.id_statut=du.id_statut
						AND du.login_user='".$_SESSION['login']."');" ;
		$result=mysql_query($sql);
		if (!$result) {
			return FALSE;
		} else {
			$row = mysql_fetch_row($result) ;
			if ($row[0]=='V' || $row[0]=='v'){
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}
}

// Méthode pour envoyer les en-têtes HTTP nécessaires au téléchargement de fichier.
// Le content-type est obligatoire, ainsi que le nom du fichier.
/**
 * Méthode pour envoyer les en-têtes HTTP nécessaires au téléchargement de fichier.
 * 
 * Le content-type est obligatoire, ainsi que le nom du fichier.
 * @param string $content_type type Mime
 * @param string $filename Nom du fichier
 * @param type $content_disposition Content-Disposition 'attachment' par défaut
 */
function send_file_download_headers($content_type, $filename, $content_disposition = 'attachment', $encodeSortie = 'utf-8') {

  header('Content-Encoding: '.$encodeSortie);
  
  header('Content-Type: '.$content_type);
  header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
  header('Content-Disposition: '.$content_disposition.'; filename="' . $filename . '"');
  
  // Contournement d'un bug IE lors d'un téléchargement en HTTPS...
  if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)) {
    header('Pragma: private');
    header('Cache-Control: private, must-revalidate');
  } else {
    header('Pragma: no-cache');
  }
}

/**
 * Enregistrer une action à effectuer pour qu'elle soit par la suite affichée en page d'accueil pour tels ou tels utilisateurs
 *
 * @param string $titre titre de l'action/info
 * @param string $description le détail de l'action à effectuer avec autant que possible un lien vers la page et paramètres utiles pour l'action
 * @param string $destinataire le tableau des login ou statuts des utilisateurs pour lesquels l'affichage sera réalisé
 * @param string $mode vaut 'individu' si $destinataire désigne des logins et 'statut' si ce sont des statuts
 * @return int|boolean Id de l'enregistrement s'est bien effectué FALSE sinon
 *
 *
 */
function enregistre_infos_actions($titre,$texte,$destinataire,$mode) {
	if(is_array($destinataire)) {
		$tab_dest=$destinataire;
	}
	else {
		$tab_dest=array($destinataire);
	}

	$sql="INSERT INTO infos_actions SET titre='".mysql_real_escape_string($titre)."', description='".mysql_real_escape_string($texte)."', date=NOW();";
	$insert=mysql_query($sql);
	if(!$insert) {
		return FALSE;
	}
	else {
		$id_info=mysql_insert_id();
		$return=$id_info;
		for($loop=0;$loop<count($tab_dest);$loop++) {
			$sql="INSERT INTO infos_actions_destinataires SET id_info='$id_info', nature='$mode', valeur='$tab_dest[$loop]';";
			$insert=mysql_query($sql);
			if(!$insert) {
				$return=FALSE;
			}
		}

		return $return;
	}
}

/**
 * Supprime une action à effectuer de la base
 *
 * @param type $id_info Id de l'action à effacer de la base
 * @param type $_login   Login concerné par l'action à effacer de la base
 * @param type $_statut  Statut concerné par l'action à effacer de la base
 * (on peut fournir login ou statut)
 *
 * @return boolean TRUE si l'action a été effacée de la base 
 */
function del_info_action($id_info, $_login="", $_statut="") {
	// Dans le cas des infos destinées à un statut... c'est le premier qui supprime qui vire pour tout le monde?
	// S'il s'agit bien de loguer des actions à effectuer... elle ne doit être effectuée qu'une fois.
	// Ou alors il faudrait ajouter des champs pour marquer les actions comme effectuées et n'afficher par défaut que les actions non effectuées

	if($_SESSION['statut']=="administrateur") {
		$sql="SELECT 1=1 FROM infos_actions_destinataires WHERE id_info='$id_info';";
	}
	else {
		$_login=$_SESSION['login'];
		$_statut=$_SESSION['statut'];

		$sql="SELECT 1=1 FROM infos_actions_destinataires WHERE id_info='$id_info' AND ((nature='statut' AND valeur='".$_statut."') OR (nature='individu' AND valeur='".$_login."'));";
	}
	//echo "$sql<br />";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		$sql="DELETE FROM infos_actions_destinataires WHERE id_info='$id_info';";
		//echo "$sql<br />";
		$del=mysql_query($sql);
		if(!$del) {
			return FALSE;
		}
		else {
			$sql="DELETE FROM infos_actions WHERE id='$id_info';";
			//echo "$sql<br />";
			$del=mysql_query($sql);
			if(!$del) {
				return FALSE;
			}
			else {
				return TRUE;
			}
		}
	}
}

/**
 * affiche sous la forme JJ/MM/AAAA la date de sortie d'un élève 
 * présente dans la base comme un timestamp
 *
 * @param date $date_sortie date (timestamp)
 * @return string La date formatée 
 */
function affiche_date_sortie($date_sortie,$heure=FALSE) {
	//
    $eleve_date_de_sortie_time=strtotime($date_sortie);
	//récupération du jour, du mois et de l'année
	$eleve_date_sortie_jour=date('j', $eleve_date_de_sortie_time); 
	$eleve_date_sortie_mois=date('m', $eleve_date_de_sortie_time);
	$eleve_date_sortie_annee=date('Y', $eleve_date_de_sortie_time); 
	$eleve_date_sortie_heure=date('H', $eleve_date_de_sortie_time); 
	$eleve_date_sortie_minute=date('i', $eleve_date_de_sortie_time); 
	if ($heure) {
		return sprintf("%02d", $eleve_date_sortie_jour)."/".sprintf("%02d", $eleve_date_sortie_mois)."/".$eleve_date_sortie_annee." ".$eleve_date_sortie_heure.":".$eleve_date_sortie_minute;
	}
	else {
		return sprintf("%02d", $eleve_date_sortie_jour)."/".sprintf("%02d", $eleve_date_sortie_mois)."/".$eleve_date_sortie_annee;
	}
}

/**
 * Traite une chaine de caractères JJ/MM/AAAA vers un timestamp AAAA-MM-JJ 00:00:00
 * 
 * @param string $date_sortie date (JJ/MM/AAAA)
 * @return date date (timestamp)
 */
function traite_date_sortie_to_timestamp($date_sortie) {
	//
	$date=explode("/", $date_sortie);
	$jour = $date[0];
	$mois = $date[1];
	$annee = $date[2];

	return $annee."-".$mois."-".$jour." 00:00:00"; 
}

/**
 * Supprime les accès au cahier de textes
 *
 * @param int $id_acces Id du cahier de texte
 * @return boolean TRUE si tout c'est bien passé 
 */
function del_acces_cdt($id_acces) {

	$sql="SELECT * FROM acces_cdt WHERE id='$id_acces';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);

		$chemin=preg_replace("#/index.(html|php)#","",$lig->chemin);
		if((!preg_match("#^documents/acces_cdt_#",$chemin))||(strstr($chemin,".."))) {
			echo "<p><span style='color:red'>Chemin $chemin invalide</span></p>";
			return FALSE;
		}
		else {
			if ((isset($GLOBALS['multisite']))&&($GLOBALS['multisite'] == 'y')){
				$test = explode("?", $chemin);
				$chemin = count($test) > 1 ? $test[0] : $chemin;
			}

			$nettoyer_acces="y";
			if(file_exists($chemin)) {
				$suppr=deltree($chemin,TRUE);
				if(!$suppr) {
					echo "<p><span style='color:red'>Erreur lors de la suppression de $chemin</span></p>";
					return FALSE;
					$nettoyer_acces="n";
				}
			}

			if($nettoyer_acces=="y") {
				$sql="DELETE FROM acces_cdt_groupes WHERE id_acces='$id_acces';";
				$del=mysql_query($sql);
				if(!$del) {
					echo "<p><span style='color:red'>Erreur lors de la suppression des groupes associés à l'accès n°$id_acces</span></p>";
					return FALSE;
				}
				else {
					$sql="DELETE FROM acces_cdt WHERE id='$id_acces';";
					$del=mysql_query($sql);
					if(!$del) {
						echo "<p><span style='color:red'>Erreur lors de la suppression de l'accès n°$id_acces</span></p>";
						return FALSE;
					}
					else {
						return TRUE;
					}
				}
			}
		}
	}
}

//=======================================================
// Fonction récupérée dans /mod_ooo/lib/lib_mod_ooo.php

/**
 * Supprime une arborescence
 * 
 * Retourne TRUE si tout s'est bien passé,
 * FALSE si un fichier est resté (problème de permission ou attribut lecture sous Win.
 * Dans tous les cas, le maximum possible est supprimé.
 * @staticvar int $niv niveau dans l'arborescence
 * @param string $rep Le répertoire de départ
 * @param boolean $repaussi TRUE ~> efface aussi $rep
 * @return boolean TRUE si tout s'est bien passé
 */
function deltree($rep,$repaussi=TRUE) {
	static $niv=0;
	$niv++;
	if (!is_dir($rep)) {return FALSE;}
	$handle=opendir($rep);
	if (!$handle) {return FALSE;}
	while ($entree=readdir($handle)) {
		if (is_dir($rep.'/'.$entree)) {
			if ($entree!='.' && $entree!='..') {
				$ok=deltree($rep.'/'.$entree);
			}
			else {$ok=TRUE;}
		}
		else {
			$ok=@unlink($rep.'/'.$entree);
		}
	}
	closedir($handle);
	$niv--;
	if ($niv || $repaussi) $ok &= @rmdir($rep);
	return $ok;
}
//=======================================================


/**
 *
 * @param type $email
 * @param type $mode
 * @return boolean  
 */
function check_mail($email,$mode='simple') {
	if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , $email)) {
		return FALSE;
	}
	else {
		if(($mode=='simple')||(!function_exists('checkdnsrr'))) {
			return TRUE;
		}
		else {
			$tab=explode('@', $email);
			if(checkdnsrr($tab[1], 'MX')) {return TRUE;}
			elseif(checkdnsrr($tab[1], 'A')) {return TRUE;}
		}
	}
}


/**
 * Fonction destinée à prendre une date mysql aaaa-mm-jj HH:MM:SS 
 * et à retourner une date au format jj/mm/aaaa
 * 
 * @param date $mysql_date date (aaaa-mm-jj HH:MM:SS)
 * @return string  date (jj/mm/aaaa)
 * @todo on a déjà cette fonction
 */
function get_date_slash_from_mysql_date($mysql_date) {
	$tmp_tab=explode(" ",$mysql_date);
	if(isset($tmp_tab[0])) {
		$tmp_tab2=explode("-",$tmp_tab[0]);
		if(isset($tmp_tab2[2])) {
			return $tmp_tab2[2]."/".$tmp_tab2[1]."/".$tmp_tab2[0];
		}
		else {
			return "Date '".$tmp_tab[0]."' mal formatée?";
		}
	}
	else {
		return "Date '$mysql_date' mal formatée?";
	}
}

// Fonction destinée à prendre une date mysql aaaa-mm-jj HH:MM:SS et à retourner une heure au format HH:MM

/**
 * Fonction destinée à prendre une date mysql aaaa-mm-jj HH:MM:SS 
 * et à retourner une heure au format HH:MM
 * 
 * @param date $mysql_date date (aaaa-mm-jj HH:MM:SS)
 * @return string  heure (HH:MM)
 */
function get_heure_2pt_minute_from_mysql_date($mysql_date) {
	$tmp_tab=explode(" ",$mysql_date);
	if(isset($tmp_tab[1])) {
		$tmp_tab2=explode(":",$tmp_tab[1]);
		if(isset($tmp_tab2[1])) {
			return $tmp_tab2[0].":".$tmp_tab2[1];
		}
		else {
			return "Heure '".$tmp_tab[1]."' mal formatée?";
		}
	}
	else {
		return "Date '$mysql_date' mal formatée?";
	}
}

/**
 * Fonction destinée à prendre une date mysql aaaa-mm-jj HH:MM:SS 
 * et à retourner une date  au format jj/mm/aaaa HH:MM
 * 
 * @param date $mysql_date date (aaaa-mm-jj HH:MM:SS)
 * @return string  heure (jj/mm/aaaa HH:MM)
 */
function get_date_heure_from_mysql_date($mysql_date) {
	return get_date_slash_from_mysql_date($mysql_date)." ".get_heure_2pt_minute_from_mysql_date($mysql_date);
}

/**
 *
 * @param type $mysql_date
 * @return type 
 */
function mysql_date_to_unix_timestamp($mysql_date) {
	$tmp_tab=explode(" ",$mysql_date);
	$tmp_tab2=explode("-",$tmp_tab[0]);
	if((!isset($tmp_tab[1]))||(!isset($tmp_tab2[2]))) {
		// Ces retours ne sont pas adaptés... on fait généralement une comparaison sur le retour de cette fonction
		return "Date '$mysql_date' mal formatée?";
	}
	else {
		$tmp_tab3=explode(":",$tmp_tab[1]);

		if(!isset($tmp_tab3[2])) {
			// Ces retours ne sont pas adaptés... on fait généralement une comparaison sur le retour de cette fonction
			return "Date '$mysql_date' mal formatée?";
		}
		else {
			$jour=$tmp_tab2[2];
			$mois=$tmp_tab2[1];
			$annee=$tmp_tab2[0];
		
			$heure=$tmp_tab3[0];
			$min=$tmp_tab3[1];
			$sec=$tmp_tab3[2];
		
			return mktime($heure,$min,$sec,$mois,$jour,$annee);
		}
	}
}

/**
 * Recherche les profs principaux d'une classe
 *
 * @param string $id_classe id de la classe
 * @return array Tableau des logins des profs principaux
 */
function get_tab_prof_suivi($id_classe) {
	$tab=array();

	$sql="SELECT DISTINCT jep.professeur 
		FROM j_eleves_professeurs jep, j_eleves_classes jec 
		WHERE jec.id_classe='$id_classe' 
		AND jec.login=jep.login
		AND jec.id_classe=jep.id_classe
		ORDER BY professeur;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab[]=$lig->professeur;
		}
	}

	return $tab;
}

/**
 * Enregistre pour Affichage un message sur la page d'accueil du destinataire (ML 5/2011)
 * 
 * Les appels possibles
 * - message_accueil_utilisateur("UNTEL","Bonjour Untel") : affiche le message "Bonjour Untel" sur la page d'accueil du destinataire de login "UNTEL" dès l'appel de la fonction, pour une durée de 7 jours, avec décompte sur le 7ième jour
 * - message_accueil_utilisateur("UNTEL","Bonjour Untel",130674844) : affiche le message "Bonjour Untel" sur la page du destinataire de login "UNTEL" à partir de la date 130674844, pour une durée de 7 jours, avec décompte sur le 7ième jour	
 *  - message_accueil_utilisateur("UNTEL","Bonjour Untel",130674844,130684567) : affiche le message "Bonjour Untel" sur la page du destinataire de login "UNTEL" à partir de la date 130674844, jusqu'à la date 130684567, avec décompte sur la date 130684567
 * - message_accueil_utilisateur("UNTEL","Bonjour Untel",130674844,130684567,130690844) : affiche le message "Bonjour Untel" sur la page du destinataire de login "UNTEL" à partir de la date 130674844, jusqu'à la date 130684567, avec décompte sur la date 130690844
 * - message_accueil_utilisateur("UNTEL","Bonjour Untel",130674844,130684567,130690844,true) : affiche le message "Bonjour Untel" sur la page du destinataire de login "UNTEL" à partir de la date 130674844, jusqu'à la date 130684567, avec décompte sur la date 130690844, et autorise l'utilisateur à supprimer ce message
 * 
 * @param type string $login_destinataire login du destinataire (obligatoire)
 * @param type string $texte texte du message contenant éventuellement des balises HTML et encodé en iso-8859-1 (obligatoire)
 * @param type timestamp $date_debut date à partir de laquelle est affiché le message (optionnel)
 * @param type timestamp $date_fin date à laquelle le message n'est plus affiché (optionnel)
 * @param type timestamp $date_decompte date butoir du décompte, la chaîne _DECOMPTE_ dans $texte est remplacée par un décompte (optionnel)
 * @param type bolean $bouton_supprimer détermine s'il faut ajouter au message le bouton "Supprimer ce message"
 * @return type TRUE ou FALSE selon que le message a été enregistré ou pas
 */
function message_accueil_utilisateur($login_destinataire,$texte,$date_debut=0,$date_fin=0,$date_decompte=0,$bouton_supprimer=false)
{
	// On arrondit le timestamp d'appel à l'heure (pas nécessaire mais pour l'esthétique)
	$t_appel=time()-(time()%3600);
	// suivant le nombre de paramètres passés :
	switch (func_num_args())
		{
		case 3:
			$date_fin=$date_debut + 3600*24*7;
			$date_decompte=$date_fin;
			break;
		case 4:
			$date_decompte=$date_fin;
			break;
		case 5:
		case 6:
			break;
		default :
			// valeurs par défaut
			$date_debut=$t_appel;
			$date_fin=$t_appel + 3600*24*7;
			$date_decompte=$date_fin;
		}
	$r_sql="INSERT INTO `messages` values('','".addslashes($texte)."','".$date_debut."','".$date_fin."','".$_SESSION['login']."','_','".$login_destinataire."','".$date_decompte."')";
	$retour=mysql_query($r_sql)?true:false;
	if ($retour && $bouton_supprimer)
		{
		$id_message=mysql_insert_id();
		$contenu='
		<form method="POST" action="#" name="f_suppression_message">
		<input type="hidden" name="supprimer_message" value="'.$id_message.'">
		<button type="submit" title=" Supprimer ce message " style="border: none; background: none; float: right;"><img style="vertical-align: bottom;" src="images/icons/delete.png"></button>
		</form>'.addslashes($texte);
		$r_sql="UPDATE `messages` SET `texte`='".$contenu."' WHERE `id`='".$id_message."'";
		$retour=mysql_query($r_sql)?true:false;
		}
	return $retour;

}

/**
 * Transforme un tableau en chaine, les lignes sont séparées par une ,
 *
 * @param array $tableau Le tableau à parser
 * @return string La chaine produite 
 */
function array_to_chaine($tableau) {
	$chaine="";
	$cpt=0;
	foreach($tableau as $key => $value) {
		if($cpt>0) {$chaine.=", ";}
		$chaine.="'$value'";
		$cpt++;
	}
    unset ($key);
    unset ($value);
	return $chaine;
}

/**
 * Supprime les sauts de lignes dupliqués
 * 
 * @param string $chaine La chaine  à parser
 * @return string La chaine produite 
 */
function suppression_sauts_de_lignes_surnumeraires($chaine) {
	$retour=preg_replace('/(\\\r\\\n)+/',"\r\n",$chaine);
	$retour=preg_replace('/(\\\r)+/',"\r",$retour);
	$retour=preg_replace('/(\\\n)+/',"\n",$retour);
	return $retour;
}

/**
 * Affiche le nombre de notes ou commentaires saisis pour les bulletins
 *
 * @param string $type "notes" pour voir les notes sinon commentaires
 * @param int $id_groupe Id du groupe
 * @param int $periode_num numéro de la période
 * @param string $mode Si "couleur" le texte est sur fond orange si tous les élèves ne sont pas notés
 * @return string le nombre de notes ou commentaires saisis
 */
function nb_saisies_bulletin($type, $id_groupe, $periode_num, $mode="") {
	$retour="";

	if($type=="notes") {
		$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='".$id_groupe."' AND periode='".$periode_num."';";
	}
	else {
		$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='".$id_groupe."' AND periode='".$periode_num."';";
	}
	$test=mysql_query($sql);
	$nb_saisies_bulletin=mysql_num_rows($test);

	$tab_champs=array('eleves');
	$current_group=get_group($id_groupe, $tab_champs);
	$effectif_groupe=count($current_group["eleves"][$periode_num]["users"]);

	if($mode=="couleur") {
		if($nb_saisies_bulletin==$effectif_groupe){
			$retour="<span style='font-size: x-small;' title='Saisies complètes'>";
			$retour.="($nb_saisies_bulletin/$effectif_groupe)";
			$retour.="</span>";
		}
		else {
			$retour="<span style='font-size: x-small; background-color: orangered;' title='Saisies incomplètes ou non encore effectuées'>";
			$retour.="($nb_saisies_bulletin/$effectif_groupe)";
			$retour.="</span>";
		}
	}
	else {
		$retour="($nb_saisies_bulletin/$effectif_groupe)";
	}

	return $retour;
}

/**
 * Crée un fichier index.html de redirection vers login.php
 *
 * @param string $chemin_relatif Le répertoire à protéger
 * @param int $niveau_arbo Niveau dans l'arborescence GEPI
 * @return boolean TRUE si le fichier est créé
 */
function creation_index_redir_login($chemin_relatif,$niveau_arbo=1) {
	$retour=TRUE;

	if($niveau_arbo==0) {
		$pref=".";
	}
	else {
		$pref="";
		for($i=0;$i<$niveau_arbo;$i++) {
			if($i>0) {
				$pref.="/";
			}
			$pref.="..";
		}
	}

	$fich=fopen($chemin_relatif."/index.html","w+");
	if(!$fich) {
		$retour=FALSE;
	}
	else {
		$res=fwrite($fich,'<html><head><script type="text/javascript">
    document.location.replace("'.$pref.'/login.php")
</script></head></html>
');
		if(!$res) {
			$retour=FALSE;
		}
		fclose($fich);
	}

	return $retour;
}

/**
 * Renvoie un tableau des fichiers contenus dans le dossier
 *
 * @param string $path Le dossier à parser
 * @param array $tab_exclusion Fichiers à ne pas prendre en compte
 * @return array Tableau des fichiers
 */
function get_tab_file($path,$tab_exclusion=array(".", "..", "remove.txt", ".htaccess", ".htpasswd", "index.html")) {
	$tab_file = array();

	$handle=opendir($path);
	$n=0;
	while ($file = readdir($handle)) {
		if (!in_array(mb_strtolower($file), $tab_exclusion)) {
			$tab_file[] = $file;
			$n++;
		}
	}
	closedir($handle);
	//arsort($tab_file);
	rsort($tab_file);

	return $tab_file;
}


/**
 * Tableau des mentions pour les bulletins
 *
 * @global array $GLOBALS['tableau_des_mentions_sur_le_bulletin']
 * @name $tableau_des_mentions_sur_le_bulletin
 */
$GLOBALS['tableau_des_mentions_sur_le_bulletin'] = array();

/**
 * Retourne une mention pour les bulletins à partir de son Id
 * 
 * @global array
 * @param int $code Id de la mention recherchée
 * @return string 
 * @see get_mentions()
 */
function traduction_mention($code) {
	global $tableau_des_mentions_sur_le_bulletin;

	if((!is_array($tableau_des_mentions_sur_le_bulletin))||(count($tableau_des_mentions_sur_le_bulletin)==0)) {
		$tableau_des_mentions_sur_le_bulletin=get_mentions();
	}

	$retour="";
	if(!isset($tableau_des_mentions_sur_le_bulletin[$code])) {$retour="-";}
	else {$retour=$tableau_des_mentions_sur_le_bulletin[$code];}

	return $retour;
}

/**
 * Retourne un tableau des mentions pour les bulletins
 * 
 * tableau[index de la mention] = texte de la mention;
 * 
 * @param int $id_classe Id de la classe
 * @return array Le tableau des mentions
 */
function get_mentions($id_classe=NULL) {
	$tab=array();
	if(!isset($id_classe)) {
		$sql="SELECT * FROM mentions ORDER BY id;";
	}
	else {
		$sql="SELECT m.* FROM mentions m, j_mentions_classes j WHERE j.id_mention=m.id AND j.id_classe='$id_classe' ORDER BY j.ordre, m.mention, m.id;";
	}
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab[$lig->id]=$lig->mention;
		}
	}
	return $tab;
}

/**
 * Retourne un tableau des mentions déjà utilisées dans les bulletins
 *
 * Pour interdire la suppression d'une mention saisie pour un élève
 * 
 * @param int $id_classe Id de la classe
 * @return array Le tableau des mentions
 */
function get_tab_mentions_affectees($id_classe=NULL) {
	$tab=array();
	if(!isset($id_classe)) {
		$sql="SELECT DISTINCT j.id_mention FROM j_mentions_classes j, avis_conseil_classe a WHERE a.id_mention=j.id_mention;";
	}
	else {
		$sql="SELECT DISTINCT j.id_mention FROM j_mentions_classes j, avis_conseil_classe a, j_eleves_classes jec WHERE a.id_mention=j.id_mention AND j.id_classe=jec.id_classe AND jec.periode=a.periode AND jec.login=a.login AND j.id_classe='$id_classe';";
	}
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab[]=$lig->id_mention;
		}
	}
	return $tab;
}

/**
 * Renvoie une balise <select> avec les mentions de bulletin
 *
 * @param string $nom_champ_select valeur des attribut name et id du select
 * @param int $id_classe Id de la classe
 * @param string $id_mention_selected Id de la mention à sélectionner par défaut
 * @return string La balise
 */
function champ_select_mention($nom_champ_select,$id_classe,$id_mention_selected='') {

	$tab_mentions=get_mentions($id_classe);
	$retour="<select name='$nom_champ_select' id='$nom_champ_select'>\n";
	$retour.="<option value=''";
	if(($id_mention_selected=="")||(!array_key_exists($id_mention_selected,$tab_mentions))) {
		$retour.=" selected='selected'";
	}
	$retour.="> </option>\n";
	foreach($tab_mentions as $key => $value) {
		$retour.="<option value='$key'";
		if($id_mention_selected==$key) {
			$retour.=" selected='selected'";
		}
		//$retour.=">".$value." ".$key."</option>\n";
		$retour.=">".$value."</option>\n";
	}
	$retour.="</select>\n";

	return $retour;
}

/**
 * Teste s'il y a des mentions de bulletin définies pour une classe
 *
 * @param type $id_classe Id de la classe
 * @return boolean TRUE si il y a des mentions 
 */
function test_existence_mentions_classe($id_classe) {
	$sql="SELECT 1=1 FROM j_mentions_classes WHERE id_classe='$id_classe';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		return TRUE;
	}
	else {
		return FALSE;
	}

}

/**
 * Teste si un compte est actif
 * 
 * - 0 si l'utilisateur n'est pas trouvé
 * - 1 compte actif
 * - 2 compte non-actif
 *
 * @param type $login Login de l'utilisateur
 * @return int  
 */
function check_compte_actif($login) {
	$sql="SELECT etat FROM utilisateurs WHERE login='$login';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		return 0;
	}
	else {
		$lig=mysql_fetch_object($res);
		if($lig->etat=='actif') {
			return 1;
		}
		else {
			return 2;
		}
	}
}

/**
 * Crée un lien derrière une image pour modifier les données d'un utilisateur
 *
 * @global string
 * @param string $login id de l'utilisateur cherché
 * @param string $statut statut de l'utilisateur (si '', il sera cherché avec get_statut_from_login())
 * @param string $target pour ouvrir dans une autre fenêtre
 * @param string $avec_lien 'y' ou absent pour créer un lien
 * @return string Le code html
 * @see check_compte_actif()
 * @see get_statut_from_login()
 * @see get_infos_from_login_utilisateur()
 * @todo si $target='_blank' il faudrait ajouter un argument title pour prévenir
 */
function lien_image_compte_utilisateur($login, $statut='', $target='', $avec_lien='y') {
	global $gepiPath;

	$retour="";

	if($target!="") {
		/*
		// Cela masque le title Compte actif/inactif
		if($target=='_blank') {
			$target=" target='$target' title='Ouverture dans un nouvel onglet.'";
		}
		else {
		*/
			$target=" target='$target'";
		//}
	}

	$test=check_compte_actif($login);
	if($test!=0) {
		if($statut=="") {
			$statut=get_statut_from_login($login);
		}
		else {
			$tmp_statut=get_statut_from_login($login);
			if($tmp_statut!=$statut) {$retour.="<img src='../images/icons/flag2.gif' width='17' height='18' title=\"ANOMALIE : Le statut du compte ne coïncide pas avec le statut attendu.
                    Le compte est '$tmp_statut' alors que vous avez fait
                    une recherche pour un compte '$statut'.\" /> ";}
		}

		if($statut!="") {
			$refermer_lien="y";

			if($avec_lien=="y") {
				if($statut=='eleve') {
					$retour.="<a href='".$gepiPath."/eleves/modify_eleve.php?eleve_login=$login'$target>";
				}
				elseif($statut=='responsable') {
					$infos=get_infos_from_login_utilisateur($login);
					if(isset($infos['pers_id'])) {
						$retour.="<a href='".$gepiPath."/responsables/modify_resp.php?pers_id=".$infos['pers_id']."'$target>";
					}
					else {
						$refermer_lien="n";
					}
				}
				elseif($statut=='autre') {
					$retour.="<a href='".$gepiPath."/utilisateurs/creer_statut.php'$target>";
				}
				else {
					$retour.="<a href='".$gepiPath."/utilisateurs/modify_user.php?user_login=$login'$target>";
				}
			}

			if($test==1) {
				$retour.="<img src='".$gepiPath."/images/icons/buddy.png' width='16' height='16' alt='Compte $login actif' title='Compte $login actif' />";
			}
			else {
				$retour.="<img src='".$gepiPath."/images/icons/buddy_no.png' width='16' height='16' alt='Compte $login inactif' title='Compte $login inactif' />";
			}

			if($avec_lien=="y") {
				if($refermer_lien=="y") {
					$retour.="</a>";
				}
			}
		}
	}

	return $retour;
}

/**
 * Renvoie le statut d'un utilisateur à partir de son login
 *
 * @param string $login Login de l'utilisateur
 * @return string Le statut
 */
function get_statut_from_login($login) {
	$sql="SELECT statut FROM utilisateurs WHERE login='$login';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		return "";
	}
	else {
		$lig=mysql_fetch_object($res);
		return $lig->statut;
	}
}

/**
 * Renvoie dans un tableau les informations d'un utilisateur à partir de son login
 * 
 * Champs disponibles dans le tableau
 * - tout utilisateur ->  'nom', 'prenom', 'civilite', 'email','show_email','statut','etat','change_mdp','date_verrouillage','ticket_expiration','niveau_alerte','observation_securite','temp_dir','numind','auth_mode'
 * - responsable -> pers_id
 * - eleve -> 'no_gep','sexe','naissance','lieu_naissance','elenoet','ereno','ele_id','id_eleve','id_mef','date_sortie'
 * 
 * @param string $login Login de l'utilisateur
 * @param string $tab_champs Tableau non utilisé
 * @return array Le tableau des informations
 * @todo $tab_champs n'est pas utilisé pour l'instant
 * @todo Déterminer les champs supplémentaires pour le statut autre
 */
function get_infos_from_login_utilisateur($login, $tab_champs=array()) {
	$tab=array();

	$tab_champs_utilisateur=array('nom', 'prenom', 'civilite', 'email','show_email','statut','etat','change_mdp','date_verrouillage','ticket_expiration','niveau_alerte','observation_securite','temp_dir','numind','auth_mode');
	$sql="SELECT * FROM utilisateurs WHERE login='$login';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);
		foreach($tab_champs_utilisateur as $key => $value) {
			$tab[$value]=$lig->$value;
		}
        unset ($key, $value);

		if($tab['statut']=='responsable') {
			$sql="SELECT pers_id FROM resp_pers WHERE login='$login';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$lig=mysql_fetch_object($res);
				$tab['pers_id']=$lig->pers_id;

				if(in_array('enfants', $tab_champs)) {
					// A compléter
				}
			}
		}
		elseif($tab['statut']=='eleve') {
			$sql="SELECT * FROM eleves WHERE login='$login';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$lig=mysql_fetch_object($res);

				$tab_champs_eleve=array('no_gep','sexe','naissance','lieu_naissance','elenoet','ereno','ele_id','id_eleve','mef_code','date_sortie');
				foreach($tab_champs_eleve as $key => $value) {
					$tab[$value]=$lig->$value;
				}
                unset ($key, $value);

				if(in_array('parents', $tab_champs)) {
					// A compléter
				}
			}

		}
		elseif($tab['statut']=='autre') {
			// A compléter
			$tab['statut_autre']="A EXTRAIRE";
		}
	}
	return $tab;
}

/**
 * Vérifie qu'un responsable a accès au module discipline
 *
 * @param string $login_resp Login du responsable
 * @return boolean TRUE si le responsable a accès
 * @see check_compte_actif()
 * @see getSettingValue()
 */
function acces_resp_disc($login_resp) {
	if((check_compte_actif($login_resp)!=0)&&(getSettingValue('visuRespDisc')=='yes')) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * Vérifie qu'un élève a accès au module discipline
 *
 * @param string $login_ele Login de l'élève
 * @return boolean TRUE si l'élève a accès
 * @see check_compte_actif()
 * @see getSettingValue()
 */
function acces_ele_disc($login_ele) {
	if((check_compte_actif($login_ele)!=0)&&(getSettingValue('visuEleDisc')=='yes')) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * Renvoie un tableau des responsables d'un élève
 * 
 * $tab[indice] = array('login','nom','prenom','civilite','designation'=>civilite nom prenom, 'pers_id')
 *
 * @param string $ele_login Login de l'élève
 * @param string $meme_en_resp_legal_0 'y'
 *               (on récupère même les enfants dont $resp_login est resp_legal=0)
 *               'yy' (on récupère aussi les resp_legal=0 mais seulement s'ils ont l'accès aux données en tant qu'utilisateur
 *               ou 'n' (on ne récupère que les enfants dont $resp_login est resp_legal=1 ou 2)
 *
 * @return array Le tableau
 */
function get_resp_from_ele_login($ele_login, $meme_en_resp_legal_0="n") {
	$tab="";

	$sql="(SELECT rp.* FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND rp.pers_id=r.pers_id AND r.ele_id=e.ele_id AND (r.resp_legal='1' OR r.resp_legal='2'))";
	if($meme_en_resp_legal_0=="y") {
		$sql.=" UNION (SELECT rp.* FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND rp.pers_id=r.pers_id AND r.ele_id=e.ele_id AND r.resp_legal='0')";
	}
	elseif($meme_en_resp_legal_0=="yy") {
		$sql.=" UNION (SELECT rp.* FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND rp.pers_id=r.pers_id AND r.ele_id=e.ele_id AND r.resp_legal='0' AND r.acces_sp='y')";
	}
	$sql.=";";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$cpt=0;
		while($lig=mysql_fetch_object($res)) {
			$tab[$cpt]=array();

			$tab[$cpt]['login']=$lig->login;
			$tab[$cpt]['nom']=$lig->nom;
			$tab[$cpt]['prenom']=$lig->prenom;
			$tab[$cpt]['civilite']=$lig->civilite;

			$tab[$cpt]['designation']=$lig->civilite." ".$lig->nom." ".$lig->prenom;

			$tab[$cpt]['pers_id']=$lig->pers_id;

			$cpt++;
		}
	}

	//print_r($tab);

	return $tab;
}

/**
 *
 * @param type $callback
 * @param ArrayAccess $array
 * @return type 
 */
function array_map_deep($callback, $array) {
    $new = array();
    if (is_array($array) || $array instanceof ArrayAccess) {
    	foreach ($array as $key => $val) {
	        if (is_array($val)) {
	            $new[$key] = array_map_deep($callback, $val);
	        } else {
	            $new[$key] = call_user_func($callback, $val);
	        }
    	}
    }
    else $new = call_user_func($callback, $array);
    return $new;
} 

/**
 * Création de la balise audio pour l'alarme sonore de fin de session
 * 
 * @return string Balises audio
 */
function joueAlarme($niveau_arbo = "0") {
  $retour ="";
  	$footer_sound= isset ($_SESSION['login']) ? getPref($_SESSION['login'],'footer_sound',NULL) : NULL;
	if($footer_sound===NULL) {
		$footer_sound=getSettingValue('footer_sound');
		if($footer_sound==NULL) {
			$footer_sound="KDE_Beep_Pop.wav";
		}
	}
	
	//if($footer_sound!=='') {

	  if ($niveau_arbo == "0") {
		  $chemin_sound="./sounds/".$footer_sound;
	  } elseif ($niveau_arbo == "1") {
		  $chemin_sound="../sounds/".$footer_sound;
	  } elseif ($niveau_arbo == "2") {
		  $chemin_sound="../../sounds/".$footer_sound;
	  } elseif ($niveau_arbo == "3") {
		  $chemin_sound="../../../sounds/".$footer_sound;
	  }
	  else {
		  $chemin_sound="../sounds/".$footer_sound;
	  }

	  if(file_exists($chemin_sound)) { 
		$retour ="<audio id='id_footer_sound' preload='auto' autobuffer>
	<source src=".$chemin_sound." />
</audio>
<script type='text/javascript'>
  function play_footer_sound() {
	  if(document.getElementById('id_footer_sound')) {
		  document.getElementById('id_footer_sound').play();
	  }
  }
  </script>";
	  }
	//}
	return $retour;
} 

/**
 * Recupere le timestamp unix du jour ouvert precedent
 *
 * @param int $timestamp du jour courant
 * @return int $timestamp du jour precedent
 */
function get_timestamp_jour_precedent($timestamp_today) {
	$hier=false;

	$tab_nom_jour=array('dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi');
	$sql="select * from horaires_etablissement WHERE ouverture_horaire_etablissement!=fermeture_horaire_etablissement AND ouvert_horaire_etablissement!='0' ORDER BY id_horaire_etablissement;";
	$res_jours_ouverts=mysql_query($sql);
	if(mysql_num_rows($res_jours_ouverts)>0) {
		$tab_jours_ouverture=array();
		while($lig_j=mysql_fetch_object($res_jours_ouverts)) {
			$tab_jours_ouverture[]=$lig_j->jour_horaire_etablissement;
			//echo "\$tab_jours_ouverture[]=".$lig_j->jour_horaire_etablissement."<br />";
		}

		$compteur=0;
		$j_prec = $timestamp_today - 3600*24;
		while((isset($tab_nom_jour[strftime("%w",$j_prec)]))&&(!in_array($tab_nom_jour[strftime("%w",$j_prec)],$tab_jours_ouverture))&&($compteur<8)) {
			$j_prec -= 3600*24;
			$compteur++;
		}
		if($compteur<7) {
			$hier=$j_prec;
		}
	}

	return $hier;
}

/**
 * Recupere le timestamp unix du jour ouvert suivant
 *
 * @param int $timestamp du jour courant
 * @return int $timestamp du jour suivant
 */
function get_timestamp_jour_suivant($timestamp_today) {
	$demain=false;

	$tab_nom_jour=array('dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi');
	$sql="select * from horaires_etablissement WHERE ouverture_horaire_etablissement!=fermeture_horaire_etablissement AND ouvert_horaire_etablissement!='0' ORDER BY id_horaire_etablissement;";
	$res_jours_ouverts=mysql_query($sql);
	if(mysql_num_rows($res_jours_ouverts)>0) {
		$tab_jours_ouverture=array();
		while($lig_j=mysql_fetch_object($res_jours_ouverts)) {
			$tab_jours_ouverture[]=$lig_j->jour_horaire_etablissement;
			//echo "\$tab_jours_ouverture[]=".$lig_j->jour_horaire_etablissement."<br />";
		}

		$compteur=0;
		$j_prec = $timestamp_today - 3600*24;
		while((isset($tab_nom_jour[strftime("%w",$j_prec)]))&&(!in_array($tab_nom_jour[strftime("%w",$j_prec)],$tab_jours_ouverture))&&($compteur<8)) {
			$j_prec -= 3600*24;
			$compteur++;
		}
		if($compteur<7) {
			$hier=$j_prec;
		}

		$compteur=0;
		$j_suiv = $timestamp_today + 3600*24;
		while((isset($tab_nom_jour[strftime("%w",$j_suiv)]))&&(!in_array($tab_nom_jour[strftime("%w",$j_suiv)],$tab_jours_ouverture))&&($compteur<8)) {
			$j_suiv += 3600*24;
			$compteur++;
		}
		if($compteur<7) {
			$demain=$j_suiv;
		}
	}

	return $demain;
}

/**
 * Retourne la chaine nettoyee des retours à la ligne en trop
 *
 * @param string $texte Texte à nettoyer
 * @return string Texte nettoyé
 */
function nettoyage_retours_ligne_surnumeraires($texte) {
	$retour=preg_replace('/(\\\r\\\n)+/',"\r\n",$texte);
	$retour=preg_replace('/(\\\r)+/',"\r",$retour);
	$retour=preg_replace('/(\\\n)+/',"\n",$retour);

	return $retour;
}

/** fonction de formatage des dates de debut et de fin de saisie d'absence
 *
 * @param date $date_debut
 * @param date $date_fin
 * @return string Les dates formatées 
 */
function getDateDescription($date_debut,$date_fin) {
	$message = '';
	if (strftime("%a %d/%m/%Y", $date_debut)==strftime("%a %d/%m/%Y", $date_fin)) {
	$message .= 'le ';
	$message .= (strftime("%a %d/%m/%Y", $date_debut));
	$message .= ' entre  ';
	$message .= (strftime("%H:%M", $date_debut));
	$message .= ' et ';
	$message .= (strftime("%H:%M", $date_fin));

	} else {
	$message .= ' entre le ';
	$message .= (strftime("%a %d/%m/%Y %H:%M", $date_debut));
	$message .= ' et le ';
	$message .= (strftime("%a %d/%m/%Y %H:%M", $date_fin));
	}
	return $message;
}

/** fonction retournant une chaine encodée pour le download d'un CSV
 *
 * @param string $texte_csv
 * @return string La chaine encodée 
 */
function echo_csv_encoded($texte_csv) {
	// D'après http://www.oxeron.com/2008/09/15/probleme-daccent-dans-un-export-csv-en-php
	//$retour=$texte_csv;
	//$retour=chr(255).chr(254).mb_convert_encoding($texte_csv, 'UTF-16LE', 'UTF-8');
  
	$choix_encodage_csv=getPref($_SESSION['login'], "choix_encodage_csv", "");
	if(!in_array($choix_encodage_csv, array("", "ascii", "utf-8", "windows-1252"))) {$choix_encodage_csv="ascii";}

	if($choix_encodage_csv=="") {
		if($_SESSION['statut']=='administrateur') {
			$retour=$texte_csv;
		}
		else {
			//$retour=mb_convert_encoding($texte_csv, 'ASCII', 'utf-8');
			//$retour=remplace_accents($texte_csv,'csv');
			// Les autres utilisateurs preferont sans doute ca:
			$retour=mb_convert_encoding($texte_csv, "windows-1252", 'utf-8');
		}
	}
	else {
		if($choix_encodage_csv=="ascii") {
			//echo "=======================================<br />\n";
			//echo $texte_csv;
			$retour=ensure_ascii($texte_csv);
			//echo "=======================================<br />\n";
			//echo $retour;
			//echo "=======================================<br />\n";
		}
		else {
			$retour=mb_convert_encoding($texte_csv, $choix_encodage_csv, 'utf-8');
		}
	}

	return $retour;
}

/** fonction retournant le jour traduit en français
 *
 * @param string $jour_en Le jour en anglais (Mon, Tue, Wed,...)
 * @return string La date en français 
 */
function jour_fr($jour_en, $mode="") {
	$tab['mon']="lun";
	$tab['tue']="mar";
	$tab['wed']="mer";
	$tab['thu']="jeu";
	$tab['fri']="ven";
	$tab['sat']="sam";
	$tab['sun']="dim";

	if(isset($tab[mb_strtolower($jour_en)])) {
		if($mode=='majf2') {
			return casse_mot($tab[mb_strtolower($jour_en)], 'majf2');
		}
		else {
			return $tab[mb_strtolower($jour_en)];
		}
	}
	else {
		return $jour_en;
	}
}

/** fonction creant le fichier temp/info_jours.js pris en compte dans le CDT2 pour passer au jours suivant.
 *
 */
function creer_info_jours_js() {
	global $prefix_base, $niveau_arbo;

	//echo "\$niveau_arbo=$niveau_arbo<br />";

	if(!isset($prefix_base)) {$prefix_base="";}

	// tableau semaine
	$tab_sem[0] = 'lundi';
	$tab_sem[1] = 'mardi';
	$tab_sem[2] = 'mercredi';
	$tab_sem[3] = 'jeudi';
	$tab_sem[4] = 'vendredi';
	$tab_sem[5] = 'samedi';
	$tab_sem[6] = 'dimanche';

	$chaine_jours_ouverts="";

	$i=0;
	for($i=0;$i<count($tab_sem);$i++) {
		$sql="SELECT 1=1 FROM ".$prefix_base."horaires_etablissement
				WHERE jour_horaire_etablissement = '".$tab_sem[$i]."' AND
						date_horaire_etablissement = '0000-00-00' AND ouvert_horaire_etablissement='1'";
		$res_j_o=mysql_query($sql);
		if(mysql_num_rows($res_j_o)) {
			if($chaine_jours_ouverts!="") {$chaine_jours_ouverts.=",";}
			$num_jour=$i+1;
			$chaine_jours_ouverts.="'$num_jour'";
		}
	}

	if($chaine_jours_ouverts=='') {
		$chaine_jours_ouverts="'0','1','2','3','4','5','6'";
	}

	if(!isset($niveau_arbo)) {
		$pref_arbo="..";
	}
	elseif("$niveau_arbo"=='public') {
		$pref_arbo="..";
	}
	elseif("$niveau_arbo"=="0") {
		$pref_arbo=".";
	}
	elseif("$niveau_arbo"=="1") {
		$pref_arbo="..";
	}
	elseif("$niveau_arbo"=="2") {
		$pref_arbo="../..";
	}
	elseif("$niveau_arbo"=="3") {
		$pref_arbo="../../..";
	}

	//echo "\$pref_arbo=$pref_arbo<br />";

	$f=fopen("$pref_arbo/temp/info_jours.js","w+");
	fwrite($f,"// Tableau des jours ouverts
	// 0 pour dimanche,
	// 1 pour lundi,...
	var tab_jours_ouverture=new Array($chaine_jours_ouverts);");
	fclose($f);
}

/** Fonction destinée à récupérer les images de formules mathématiques générées
 * sur http://latex.codecogs.com/
 * Cela évite de faire une requête vers le site http://latex.codecogs.com/ pour 
 * chaque image et assure que lors de l'archivage, les images resteront 
 * disponibles même si le site http://latex.codecogs.com/ cesse de fonctionner.
 *
 * @param string $texte Le texte à traiter
 * @param integer $id_groupe L'identifiant du groupe
 * @param string $type_notice Le type de notice
 *               ('c' pour compte-rendu et 't' pour travail à faire)
 * @return string La chaine corrigée après téléchargement des images vers
 * ../documents/cl$idgroupe ou ../documents/cl_dev$idgroupe
 */
function get_img_formules_math($texte, $id_groupe, $type_notice="c") {
	global $multisite;

	$contenu_cor=$texte;

	if((preg_match('|src="http://latex.codecogs.com/|', $contenu_cor))||
	(preg_match('|src="https://latex.codecogs.com/|', $contenu_cor))) {

		$niv_arbo_tmp=2;
		$dest_documents = '../documents/';
		$dossier = '';
		$multi = (isset($multisite) && $multisite == 'y') ? $_COOKIE['RNE'].'/' : NULL;
		if ((isset($multisite) && $multisite == 'y') && is_dir('../documents/'.$multi) === false){
			@mkdir('../documents/'.$multi);
			$dest_documents .= $multi;
			$niv_arbo_tmp++;
		}elseif((isset($multisite) && $multisite == 'y')){
			$dest_documents .= $multi;
			$niv_arbo_tmp++;
		}

		//$type_notice="c";
		if($type_notice=='c') {
			$dest_documents.="/cl".$id_groupe;
		}
		else {
			$dest_documents.="/cl_dev".$id_groupe;
		}

		if(!file_exists($dest_documents)) {
			mkdir($dest_documents);
			creation_index_redir_login($dest_documents,$niv_arbo_tmp);
		}

		$chaine="";
		$tab_tmp=preg_split('/"/',$contenu_cor);
		for($loop=0;$loop<count($tab_tmp);$loop++) {
			if((preg_match("|^http://latex.codecogs.com/|",$tab_tmp[$loop]))||
			(preg_match("|^https://latex.codecogs.com/|",$tab_tmp[$loop]))) {
				$erreur="n";
				$extension_fichier_formule="gif";
				if((preg_match("|^http://latex.codecogs.com/gif.latex|",$tab_tmp[$loop]))||
				(preg_match("|^https://latex.codecogs.com/gif.latex|",$tab_tmp[$loop]))) {
					$extension_fichier_formule="gif";
				}
				elseif((preg_match("|^http://latex.codecogs.com/png.latex|",$tab_tmp[$loop]))||
				(preg_match("|^https://latex.codecogs.com/png.latex|",$tab_tmp[$loop]))) {
					$extension_fichier_formule="png";
				}
				elseif((preg_match("|^http://latex.codecogs.com/swf.latex|",$tab_tmp[$loop]))||
				(preg_match("|^https://latex.codecogs.com/swf.latex|",$tab_tmp[$loop]))) {
					$extension_fichier_formule="swf";
				}
				elseif((preg_match("|^http://latex.codecogs.com/emf.latex|",$tab_tmp[$loop]))||
				(preg_match("|^https://latex.codecogs.com/emf.latex|",$tab_tmp[$loop]))) {
					$extension_fichier_formule="emf";
				}
				elseif((preg_match("|^http://latex.codecogs.com/pdf.latex|",$tab_tmp[$loop]))||
				(preg_match("|^https://latex.codecogs.com/pdf.latex|",$tab_tmp[$loop]))) {
					$extension_fichier_formule="pdf";
				}
				elseif((preg_match("|^http://latex.codecogs.com/svg.latex|",$tab_tmp[$loop]))||
				(preg_match("|^https://latex.codecogs.com/svg.latex|",$tab_tmp[$loop]))) {
					$extension_fichier_formule="svg";
				}

				// Eviter les doublons:
				$nom_tmp=strftime("%Y%m%d_%H%M%S");
				$nom_tmp0=$nom_tmp;
				$cpt=1;
				while(file_exists($dest_documents."/".$nom_tmp.".".$extension_fichier_formule)) {
					$nom_tmp=$nom_tmp0."_".$cpt;
					if($cpt>100) {$erreur="y";}
					$cpt++;
				}

				// Telechargement du fichier:
				if($erreur=="n") {
					$morceau_courant=$dest_documents."/".$nom_tmp.".".$extension_fichier_formule;
					// On a tendance à récupérer des chemins du type ../documents//cl2675/20131101_142603.gif
					// Le // n'est pas très propre...
					$morceau_courant=preg_replace("|/{2,}|","/",$morceau_courant);
					/*
					$f=fopen("/tmp/formule.txt", "a+");
					fwrite($f, strftime('%Y%m%d %H%M%S')." : ".$morceau_courant."\n");
					fclose($f);
					*/
					if(!copy($tab_tmp[$loop],$morceau_courant)) {$morceau_courant=$tab_tmp[$loop];}
				}
				else {
					$morceau_courant=$tab_tmp[$loop];
				}
			}
			else {
				$morceau_courant=$tab_tmp[$loop];
			}

			// On complète la chaine du contenu de la notice:
			if($chaine!="") {
				$chaine.="\"";
			}
			$chaine.=$morceau_courant;
		}
		$contenu_cor=$chaine;
	}

	return $contenu_cor;
}

/** Fonction destinée à contacter le serveur toutes les $intervalle_temps secondes
 *  et afficher si le serveur répond.
 *  Pour contrôler que le serveur est OK avant de valider une page avec beaucoup de saisies
 *
 * @param string $id_div_retour nom du div inséré
 * @param string $nom_js_func nom de la fonction js insérée
 * @param string $nom_var nom de la variable js compteur de secondes
 * @param string $taille la taille du div carré inséré
 * @param integer $intervalle_temps l'intervalle de temps en secondes entre deux contacts du serveur
 *
 * @return string le code HTML et JS
 */

function temoin_check_srv($id_div_retour="retour_ping", $nom_js_func="check_srv", $nom_var="cpt_ping", $taille=10, $intervalle_temps=10) {
	global $gepiPath;

	echo "<div id='retour_ping' style='width:".$taille."px; height:".$taille."px; background-color:red; border:1px solid black; float:left; margin:1px; display:none;' title=\"Témoin de réponse du serveur: Un test est effectué toutes les $intervalle_temps secondes.
Si le témoin se maintient au rouge, c'est que le serveur n'est pas joignable.
Vous devriez dans ce cas (pour vous prémunir d'une perte de ce qui a été saisi et pas encore enregistré), copier dans un Bloc-notes tout ce qui n'a pas encore été enregistré.
Provoquer l'enregistrement/validation des données vers le serveur risque de se solder par un échec (si le serveur est indisponible, il ne recevra pas ce que vous enverrez et tout sera perdu).\"></div>\n";

	echo "<script type='text/javascript'>
	var $nom_var=0;

	document.getElementById('retour_ping').style.display='';

	function $nom_js_func() {

		if(($nom_var==$intervalle_temps)||($nom_var==0)) {
			$nom_var=0;

			$('$id_div_retour').style.opacity=1;
			$('$id_div_retour').innerHTML='';
	
			new Ajax.Updater($('$id_div_retour'),'$gepiPath/lib/echo.php?var=valeur',{method: 'get'});

		}
		else {
			$('$id_div_retour').style.opacity=1-$nom_var/$intervalle_temps;
		}
		$nom_var+=1;
		setTimeout('$nom_js_func()', 1000);
	}

	$nom_js_func();
</script>\n";
}

/** Fonction destinée à télécharger les images générées sur http://latex.codecogs.com/ 
 *  et à corriger les notices en conséquence pour pointer sur uneURL locale
 *
 * @param integer $eff_parcours
 *
 * @return string le code HTML relatant le nombre de notices corrigées.
 */

function correction_notices_cdt_formules_maths($eff_parcours) {
	$tab_grp=array();

	$nb_corr=0;
	$sql="SELECT * FROM ct_entry WHERE contenu LIKE '%src=\"http://latex.codecogs.com/%' OR contenu LIKE '%src=\"https://latex.codecogs.com/%' LIMIT $eff_parcours;";
	$res=mysql_query($sql);
	while($lig=mysql_fetch_object($res)) {
		$id_ct=$lig->id_ct;
		$id_groupe=$lig->id_groupe;
		$contenu=$lig->contenu;
		$type_notice="c";

		if(!isset($tab_grp[$id_groupe])) {
			$tab_grp[$id_groupe]=get_group($id_groupe);
		}

		$contenu_corrige=get_img_formules_math($contenu, $id_groupe, $type_notice);
		$sql="UPDATE ct_entry SET contenu='".mysql_real_escape_string($contenu_corrige)."' WHERE id_ct='$id_ct';";
		$res_ct=mysql_query($sql);
		if(!$res_ct) {
			echo "<div style='border:1px solid red; margin:3px;'>";
			echo "<p style='color:red;'>ERREUR sur<br />$sql";
			echo "</div>\n";
		}
		else {
			echo "<p>Correction sur une notice de <strong>compte-rendu</strong> en ".$tab_grp[$id_groupe]['name']." en ".$tab_grp[$id_groupe]['classlist_string']." : ".strftime("%d/%m/%Y", $lig->date_ct)."<br />\n";
			$nb_corr++;
		}
		flush();
	}
	echo "<p>$nb_corr corrections effectuées sur 'ct_entry'.</p>";

	$nb_corr=0;
	$sql="SELECT * FROM ct_devoirs_entry WHERE contenu LIKE '%src=\"http://latex.codecogs.com/%' OR contenu LIKE '%src=\"https://latex.codecogs.com/%' LIMIT $eff_parcours;";
	$res=mysql_query($sql);
	while($lig=mysql_fetch_object($res)) {
		$id_ct=$lig->id_ct;
		$id_groupe=$lig->id_groupe;
		$contenu=$lig->contenu;
		$type_notice="t";

		if(!isset($tab_grp[$id_groupe])) {
			$tab_grp[$id_groupe]=get_group($id_groupe);
		}

		$contenu_corrige=get_img_formules_math($contenu, $id_groupe, $type_notice);
		$sql="UPDATE ct_devoirs_entry SET contenu='".mysql_real_escape_string($contenu_corrige)."' WHERE id_ct='$id_ct';";
		$res_ct=mysql_query($sql);
		if(!$res_ct) {
			echo "<div style='border:1px solid red; margin:3px;'>";
			echo "<p style='color:red;'>ERREUR sur<br />$sql";
			echo "</div>\n";
		}
		else {
			echo "<p>Correction sur une notice de <strong>devoir</strong> en ".$tab_grp[$id_groupe]['name']." en ".$tab_grp[$id_groupe]['classlist_string']." : ".strftime("%d/%m/%Y", $lig->date_ct)."<br />\n";
			$nb_corr++;
		}
		flush();
	}
	echo "<p>$nb_corr corrections effectuées sur 'ct_devoirs_entry'.</p>";
}


/** Fonction destinée à retourner un tableau PHP des numéros de téléphone responsable (et élève)
 *
 * @param string $ele_login Login de l'élève
 *
 * @return array Tableau PHP des numéros de tel.
 */
function get_tel_resp_ele($ele_login) {
	$tab_tel=array();

	$cpt_resp=0;

	$sql="SELECT rp.*, r.resp_legal, e.tel_pers AS ele_tel_pers, e.tel_port AS ele_tel_port, e.tel_prof AS ele_tel_prof FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND e.ele_id=r.ele_id AND r.pers_id=rp.pers_id AND (r.resp_legal='1' OR r.resp_legal='2') ORDER BY r.resp_legal;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab_tel['responsable'][$cpt_resp]=array();
			$tab_tel['responsable'][$cpt_resp]['resp_legal']=$lig->resp_legal;
			$tab_tel['responsable'][$cpt_resp]['civ_nom_prenom']=$lig->civilite." ".casse_mot($lig->nom,'maj')." ".casse_mot($lig->prenom,'majf2');
			if($lig->tel_pers!='') {
				$tab_tel['responsable'][$cpt_resp]['tel_pers']=$lig->tel_pers;
			}
			if($lig->tel_port!='') {
				$tab_tel['responsable'][$cpt_resp]['tel_port']=$lig->tel_port;
			}
			if($lig->tel_prof!='') {
				$tab_tel['responsable'][$cpt_resp]['tel_prof']=$lig->tel_prof;
			}

			// On va remplir plusieurs fois les champs suivants (mais avec les mêmes valeurs) s'il y a plusieurs responsables
			if((getSettingAOui('ele_tel_pers'))&&($lig->ele_tel_pers!='')) {
				$tab_tel['eleve']['tel_pers']=$lig->ele_tel_pers;
			}
			if((getSettingAOui('ele_tel_port'))&&($lig->ele_tel_port!='')) {
				$tab_tel['eleve']['tel_port']=$lig->ele_tel_port;
			}
			if((getSettingAOui('ele_tel_prof'))&&($lig->ele_tel_prof!='')) {
				$tab_tel['eleve']['tel_prof']=$lig->ele_tel_prof;
			}
			$cpt_resp++;
		}
	}

	$sql="SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND e.ele_id=r.ele_id AND r.pers_id=rp.pers_id AND resp_legal='0' ORDER BY rp.civilite, rp.nom, rp.prenom;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab_tel['responsable'][$cpt_resp]=array();
			$tab_tel['responsable'][$cpt_resp]['resp_legal']=$lig->resp_legal;
			$tab_tel['responsable'][$cpt_resp]['civ_nom_prenom']=$lig->civilite." ".casse_mot($lig->nom,'maj')." ".casse_mot($lig->prenom,'majf2');
			if($lig->tel_pers!='') {
				$tab_tel['responsable'][$cpt_resp]['tel_pers']=$lig->tel_pers;
			}
			if($lig->tel_port!='') {
				$tab_tel['responsable'][$cpt_resp]['tel_port']=$lig->tel_port;
			}
			if($lig->tel_prof!='') {
				$tab_tel['responsable'][$cpt_resp]['tel_prof']=$lig->tel_prof;
			}
			$cpt_resp++;
		}
	}

	return $tab_tel;
}

/** Fonction destinée à retourner un tableau HTML des numéros de téléphone responsable (et élève)
 *
 * @param string $ele_login Login de l'élève
 *
 * @return array Tableau HTML des numéros de tel.
 */
function tableau_tel_resp_ele($ele_login) {
	$retour="";
	$tab_tel=get_tel_resp_ele($ele_login);

	$tab_style[1]="impair";
	$tab_style[-1]="pair";

	if(((isset($tab_tel['responsable']))&&(count($tab_tel['responsable'])>0))||((isset($tab_tel['eleve']))&&(count($tab_tel['eleve'])>0))) {
		$retour.="<table class='boireaus' summary='Tableau des numéros de téléphone'>\n";
		//$retour.="<table class='tb_absences' summary='Tableau des numéros de telephone'>\n";
		$retour.="<tr>\n";
		$retour.="<th></th>\n";
		$retour.="<th>Identité</th>\n";
		$retour.="<th>Personnel</th>\n";
		$retour.="<th>Portable</th>\n";
		$retour.="<th>Professionnel</th>\n";
		$retour.="</tr>\n";

		$alt=1;
		//foreach($tab_tel['responsable'] as $resp_legal => $tab_resp_legal) {
		for($i=0;$i<count($tab_tel['responsable']);$i++) {
			$alt=$alt*(-1);
			$retour.="<tr class='lig$alt white_hover'>\n";
			//$retour.="<tr class='".$tab_style[$alt]." white_hover'>\n";
			$retour.="<td title='Numéro de responsable légal'>".$tab_tel['responsable'][$i]['resp_legal']."</td>\n";
			$retour.="<td>".$tab_tel['responsable'][$i]['civ_nom_prenom']."</td>\n";
			$retour.="<td>";
			if(isset($tab_tel['responsable'][$i]['tel_pers'])) {$retour.=$tab_tel['responsable'][$i]['tel_pers'];}
			$retour.="</td>\n";
			$retour.="<td>";
			if(isset($tab_tel['responsable'][$i]['tel_port'])) {$retour.=$tab_tel['responsable'][$i]['tel_port'];}
			$retour.="</td>\n";
			$retour.="<td>";
			if(isset($tab_tel['responsable'][$i]['tel_prof'])) {$retour.=$tab_tel['responsable'][$i]['tel_prof'];}
			$retour.="</td>\n";
			$retour.="</tr>\n";
		}

		if(isset($tab_tel['eleve'])) {
			$alt=$alt*(-1);
			$retour.="<tr class='lig$alt white_hover'>\n";
			$retour.="<td colspan='2'>Élève</td>\n";

			$retour.="<td>";
			if(isset($tab_tel['eleve']['tel_pers'])) {$retour.=$tab_tel['eleve']['tel_pers'];}
			$retour.="</td>\n";

			$retour.="<td>";
			if(isset($tab_tel['eleve']['tel_port'])) {$retour.=$tab_tel['eleve']['tel_port'];}
			$retour.="</td>\n";

			$retour.="<td>";
			if(isset($tab_tel['eleve']['tel_prof'])) {$retour.=$tab_tel['eleve']['tel_prof'];}
			$retour.="</td>\n";

			$retour.="</tr>\n";
		}
		$retour.="</table>\n";
	}
	else {
		$retour.="<p style='color:red'>Aucun numéro de téléphone n'a été trouvé.</p>\n";
	}
	return $retour;
}

/** Fonction destinée à retourner un tableau des dimensions et type d'une image après redimensionnement
 *
 * @param string $image chemin de l'image
 * @param integer $dim_max_largeur la largeur maximale de l'image
 * @param integer $dim_max_hauteur la hauteur maximale de l'image
 * @param string $mode le type de redimensionnement:
 *                      <vide> la hauteur ne doit pas dépasser $dim_max_hauteur, et la largeur retournées ne doit pas dépasser la $dim_max_largeur
 *                      'largeur' on redimensionne en forçant la largeur à $dim_max_largeur
 *                      'hauteur' on redimensionne en forçant la hauteur à $dim_max_hauteur
 *
 * @return array Tableau des dimensions et type
 */
function redim_img($image, $dim_max_largeur, $dim_max_hauteur, $mode="") {
	$info_image=getimagesize($image);

	$largeur=$info_image[0];
	$hauteur=$info_image[1];

	// calcule le ratio de redimensionnement
	$ratio_l=$largeur/$dim_max_largeur;
	$ratio_h=$hauteur/$dim_max_hauteur;
	if($mode=="") {
		$ratio=($ratio_l>$ratio_h)?$ratio_l:$ratio_h;
	}
	elseif($mode=="largeur") {
		$ratio=$ratio_l;
	}
	else {
		$ratio=$ratio_h;
	}

	// définit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur=round($largeur/$ratio);
	$nouvelle_hauteur=round($hauteur/$ratio);

	$type_img="";
	if(isset($info_image[2])) {
		// 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(orden de bytes intel), 8 = TIFF(orden de bytes motorola), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF, 15 = WBMP, 16 = XBM.
		$type_img=$info_image[2];
	}

	return array($nouvelle_largeur, $nouvelle_hauteur, $type_img);
}

/** Fonction destinée à supprimer les accents HTML dans des enregistrements de la table setting
 *
 * @param string $name champ name dans la table setting
 *
 * @return integer 0 Correction inutile, 1 Correction réussie, 2 Echec de la correction.
 */

function virer_accents_html_setting($name) {
	$tab = array_flip (get_html_translation_table(HTML_ENTITIES));
	$valeur=getSettingValue($name);
	$correction=ensure_utf8(strtr($valeur, $tab));
	/*
	$f=fopen("/tmp/correction_fb.txt", "a+");
	fwrite($f, "=========================================================================\n");
	fwrite($f, "name=$name\n");
	fwrite($f, "value=$valeur\n");
	fwrite($f, "correction=$correction\n");
	fclose($f);
	*/
	if($valeur!=$correction) {
		if(saveSetting($name, mysql_real_escape_string($correction))) {return 1;} else {return 2;}
	}
	else {return 0;}
}

/** Fonction destinée à enregistrer des détails sur la mise à jour Sconet en cours
 *
 * @param string $texte Texte à ajouter au log en cours
 * @param string $fin 'y' ou 'n' pour mettre à jour la date de fin
 *
 * @return boolean Succès ou échec de l'enregistrement.
 */

function enregistre_log_maj_sconet($texte, $fin="n") {
	$ts_maj_sconet=getSettingValue('ts_maj_sconet');
	if($ts_maj_sconet=='') {
		return false;
	}
	else {
		$sql="SELECT * FROM log_maj_sconet WHERE date_debut='$ts_maj_sconet';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);

			$sql="UPDATE log_maj_sconet SET texte='".mysql_real_escape_string($lig->texte.$texte)."'";
			if($fin!="n") {$sql.=", date_fin='".strftime("%Y-%m-%d %H:%M:%S")."'";}
			$sql.=" WHERE date_debut='$ts_maj_sconet';";
		}
		else {
			$sql="INSERT INTO log_maj_sconet SET date_debut='$ts_maj_sconet', login='".$_SESSION['login']."', date_fin='0000-00-00 00:00:00', texte='".mysql_real_escape_string($texte)."';";
		}
		$res=mysql_query($sql);
		if($res) {return true;} else {return false;}
	}
}

/** Fonction destinée à faire un test in_array() insensible à la casse
 *
 * @param string $chaine chaine 
 * @param array $tableau tableau dans lequel on cherche la chaine
 *
 * @return boolean true/false
 */

function in_array_i($chaine, $tableau) {
	$retour=false;
	$chaine=mb_strtolower($chaine);
	foreach($tableau as $key => $value) {
		if($chaine==mb_strtolower($value)) {
			$retour=true;
			break;
		}
	}
	return $retour;
}

/** Fonction destinée à tester si un parent est responsable d'un élève
 *
 * @param string $login_eleve Login de l'élève
 * @param string $login_resp Login du responsable
 * @param string $pers_id Identifiant pers_id du responsable
 * @param string $meme_en_resp_legal_0 'y'
 *               (on récupère même les enfants dont $resp_login est resp_legal=0)
 *               'yy' (on récupère aussi les resp_legal=0 mais seulement s'ils ont l'accès aux données en tant qu'utilisateur
 *               ou 'n' (on ne récupère que les enfants dont $resp_login est resp_legal=1 ou 2)
 *
 *
 * @return boolean true/false
 */

function is_responsable($login_eleve, $login_resp="", $pers_id="", $meme_en_resp_legal_0="n") {
	$retour=false;
	if($login_resp!="") {
		$sql="(SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e WHERE r.pers_id=rp.pers_id AND rp.login='$login_resp' AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND (r.resp_legal='1' OR r.resp_legal='2'))";
		if($meme_en_resp_legal_0=="y") {
			$sql.=" UNION (SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e WHERE r.pers_id=rp.pers_id AND rp.login='$login_resp' AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND r.resp_legal='0')";
		}
		elseif($meme_en_resp_legal_0=="yy") {
			$sql.=" UNION (SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e WHERE r.pers_id=rp.pers_id AND rp.login='$login_resp' AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND r.resp_legal='0' AND r.acces_sp='y')";
		}
		$sql.=";";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$retour=true;
		}
	}
	elseif($pers_id!="") {
		$sql="(SELECT 1=1 FROM responsables2 r, eleves e WHERE r.pers_id='$pers_id' AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND (r.resp_legal='1' OR r.resp_legal='2'))";
		if($meme_en_resp_legal_0=="y") {
			$sql.=" UNION (SELECT 1=1 FROM responsables2 r, eleves e WHERE r.pers_id='$pers_id' AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND r.resp_legal='0')";
		}
		elseif($meme_en_resp_legal_0=="yy") {
			$sql.=" UNION (SELECT 1=1 FROM responsables2 r, eleves e WHERE r.pers_id='$pers_id' AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND r.resp_legal='0' AND r.acces_sp='y')";
		}
		$sql.=";";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$retour=true;
		}
	}
	return $retour;
}

// http://www.siteduzero.com/tutoriel-3-56199-les-captchas-textuels.html
function captchaMath()
{
	$n1 = mt_rand(0,10);
	$n2 = mt_rand(0,10);
	$nbrFr = array('zero','un','deux','trois','quatre','cinq','six','sept','huit','neuf','dix');
	$resultat = $n1 + $n2;
	$phrase = $nbrFr[$n1] .' plus '.$nbrFr[$n2];
	
	return array($resultat, $phrase);	
}

function captcha()
{
	list($resultat, $phrase) = captchaMath();
	$_SESSION['captcha'] = $resultat;
	return $phrase;
}

/** Fonction destinée à retourner un tableau des ouvertures par période en 
 *  consultation parent/élève pour telle classe sur telle à telle période
 *
 * @param integer $periode1  La première période à tester
 * @param integer $periode2  La dernière période à tester
 * @param integer $id_classe L'identifiant de la classe à tester
 * @param string $statut     Le statut 'responsable' ou 'eleve'
 *                           Si le statut est vide, on prend le statut de 
 *                           l'utilisateur connecté.
 *
 * @return array Tableau avec les numéros de période en indice et 'y' ou 'n' 
 *               selon que les appréciations sont ou non accessibles
 */
function acces_appreciations($periode1, $periode2, $id_classe, $statut='') {
	global $delais_apres_cloture;
	global $date_ouverture_acces_app_classe;

	if($delais_apres_cloture==="") {
		$delais_apres_cloture=getSettingValue('delais_apres_cloture');
	}

	$tab_acces_app=array();

	if($statut=="") {
		$statut=$_SESSION['statut'];
	}

	if(($statut=='eleve')||($statut=='responsable')) {
		if(getSettingValue('acces_app_ele_resp')=='periode_close') {
			$timestamp_limite=time()-$delais_apres_cloture*24*3600;
			for($i=$periode1;$i<=$periode2;$i++) {
				$sql="SELECT 1=1 FROM periodes WHERE UNIX_TIMESTAMP(date_verrouillage)<='".$timestamp_limite."' AND id_classe='$id_classe' AND num_periode='$i' AND verouiller='O';";
				//echo "$sql<br />";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					$tab_acces_app[$i]="y";
				}
				else {
					$tab_acces_app[$i]="n";
				}
			}
		}
		elseif(getSettingValue('acces_app_ele_resp')=='date') {
			for($i=$periode1;$i<=$periode2;$i++) {
				$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND
													statut='".$statut."' AND
													periode='$i';";
				//echo "$sql<br />";
				$res=mysql_query($sql);
				if($res) {
					if(mysql_num_rows($res)>0) {
						$lig=mysql_fetch_object($res);
						//echo "\$lig->acces=$lig->acces<br />";
						if($lig->acces=="date") {
							//echo "<p>Période $i: Date limite: $lig->date<br />";
							$tab_date=explode("-",$lig->date);
							$timestamp_limite=mktime(0,0,0,$tab_date[1],$tab_date[2],$tab_date[0]);
							//echo "$timestamp_limite<br />";
							$timestamp_courant=time();
							//echo "$timestamp_courant<br />";

							$date_ouverture_acces_app_classe[$i]=$lig->date;

							if($timestamp_courant>$timestamp_limite){
								$tab_acces_app[$i]="y";
							}
							else {
								$tab_acces_app[$i]="n";
							}
						}
						else {
							$tab_acces_app[$i]="n";
						}
					}
					else {
						$tab_acces_app[$i]="n";
					}
				}
				else {
					$tab_acces_app[$i]="n";
				}
			}
		}
		else {
			// Ouverture manuelle
			for($i=$periode1;$i<=$periode2;$i++) {
				$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND
													statut='".$statut."' AND
													periode='$i';";
				//echo "$sql<br />";
				$res=mysql_query($sql);
				if($res) {
					if(mysql_num_rows($res)>0) {
						$lig=mysql_fetch_object($res);
						//echo "\$lig->acces=$lig->acces<br />";
						if($lig->acces=="y") {
							$tab_acces_app[$i]="y";
						}
						else {
							$tab_acces_app[$i]="n";
						}
					}
					else {
						$tab_acces_app[$i]="n";
					}
				}
				else {
					$tab_acces_app[$i]="n";
				}
			}
		}
	}
	else {
		// Pas de limitations d'accès pour les autres statuts.
		for($i=$periode1;$i<=$periode2;$i++) {
			$tab_acces_app[$i]="y";
		}
	}
	return $tab_acces_app;
}


/** Fonction destinée à tester si les responsables légaux habitent à des adresses distinctes
 *
 * @param string $login_eleve Login de l'élève
 *
 * @return boolean true/false
 */
function responsables_adresses_separees($login_eleve) {
	$retour=false;

	$sql="SELECT DISTINCT adr1, adr2, adr3, adr4, cp, commune, pays FROM resp_adr ra, resp_pers rp, responsables2 r, eleves e WHERE ra.adr_id=rp.adr_id AND r.pers_id=rp.pers_id AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND (r.resp_legal='1' OR r.resp_legal='2');";
	//echo "$sql<br />";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>1) {
		$retour=true;
	}

	return $retour;
}

/** Fonction destinée à récupérer le rang de l'élève
 *
 * @param string $login_eleve Login de l'élève
 * @param integer $id_classe Identifiant de la classe
 * @param integer $periode_num Numéro de la période
 * @param string $forcer_recalcul "y" ou "n" Forcer le recalcul du rang avant extraction du rang
 * @param string $recalcul_si_rang_nul "y" ou "n" 
 *
 * @return integer rang de l'élève
 */
function get_rang_eleve($login_eleve, $id_classe, $periode_num, $forcer_recalcul="n", $recalcul_si_rang_nul="n") {
	global $affiche_categories, $test_coef;
	$retour=0;

	$recalcul_rang="";
	for($loop=1;$loop<=$periode_num;$loop++) {
		$recalcul_rang.="y";
	}

	if($forcer_recalcul=="y") {
		$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));

		$sql="UPDATE groupes SET recalcul_rang='$recalcul_rang' WHERE id in (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe');";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		// Les rangs seront recalculés lors de l'appel à calcul_rang.inc.php

		include("../lib/calcul_rang.inc.php");
	}

	$sql="SELECT rang FROM j_eleves_classes WHERE periode='".$periode_num."' AND id_classe='".$id_classe."' AND login = '".$login_eleve."';";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$retour=mysql_result($res, 0, "rang");
		if(($retour==0)&&($recalcul_si_rang_nul=='y')) {
			$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));

			$sql="UPDATE groupes SET recalcul_rang='$recalcul_rang' WHERE id in (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe');";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			// Les rangs seront recalculés lors de l'appel à calcul_rang.inc.php

			include("../lib/calcul_rang.inc.php");

			$sql="SELECT rang FROM j_eleves_classes WHERE periode='".$periode_num."' AND id_classe='".$id_classe."' AND login = '".$login_eleve."';";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$retour=mysql_result($res, 0, "rang");
			}
		}
	}
	elseif($recalcul_si_rang_nul=='y') {
		$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));

		$sql="UPDATE groupes SET recalcul_rang='$recalcul_rang' WHERE id in (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe');";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		// Les rangs seront recalculés lors de l'appel à calcul_rang.inc.php

		include("../lib/calcul_rang.inc.php");

		$sql="SELECT rang FROM j_eleves_classes WHERE periode='".$periode_num."' AND id_classe='".$id_classe."' AND login = '".$login_eleve."';";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$retour=mysql_result($res, 0, "rang");
		}
	}

	return $retour;
}

/** Fonction destinée à retourner pour un élève, un tableau des classes et dates de périodes en fonction du numéro de période
 *
 * @param string $login_eleve Login de l'élève
 *
 * @return array Tableau d'indice num_periode
 */

function get_class_dates_from_ele_login($login_eleve) {
	$tab=array();

	$sql="SELECT p.*, c.classe, c.nom_complet FROM periodes p, j_eleves_classes jec, classes c WHERE jec.id_classe=p.id_classe AND jec.periode=p.num_periode AND c.id=jec.id_classe AND jec.login='".$login_eleve."' ORDER BY p.num_periode;";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab[$lig->num_periode]['id_classe']=$lig->id_classe;
			$tab[$lig->num_periode]['classe']=$lig->classe;
			$tab[$lig->num_periode]['nom_complet']=$lig->nom_complet;
			$tab[$lig->num_periode]['date_fin']=$lig->date_fin;
		}
	}

	return $tab;
}

function get_info_grp($id_groupe, $tab_infos=array('description', 'matieres', 'classes', 'profs')) {
	$group=get_group($id_groupe, $tab_infos);

	$retour="";
	if(isset($group['name'])) {
		$retour=$group['name'];
		if(in_array('description', $tab_infos)) {$retour.=" (<em>".$group['description']."</em>)";}
		if(in_array('matieres', $tab_infos)) {$retour.=" ".$group['matiere']['matiere'];}
		if(in_array('classes', $tab_infos)) {$retour.=" en ".$group['classlist_string'];}
		if(in_array('profs', $tab_infos)) {$retour.=" (<em>".$group['profs']['proflist_string']."</em>)";}
	}
	return $retour;
}

/** Fonction destinée à tester si un utilisateur a le droit d'accéder au CDT de tel élève
 *
 * @param string $login_user Login de l'utilisateur
 * @param string $login_eleve Login de l'élève
 *
 * @return boolean true/false
 */
function acces_cdt_eleve($login_user, $login_eleve) {
	$retour=false;

	$sql="SELECT statut FROM utilisateurs WHERE login='$login_user';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$statut=mysql_result($res, 0, "statut");

		if(($statut=="eleve")&&($login_user==$login_eleve)&&(getSettingAOui('GepiAccesCahierTexteEleve'))) {
			$retour=true;
		}
		elseif(($statut=="responsable")&&(getSettingAOui('GepiAccesCahierTexteParent'))&&(is_responsable($login_eleve, $login_user))) {
			$retour=true;
		}
		elseif(($statut=="professeur")&&(getSettingAOui('GepiAccesCDTToutesClasses'))) {
			$retour=true;
		}
		elseif($statut=="professeur") {
			$sql="SELECT 1=1 FROM j_groupes_professeurs jgp, j_eleves_groupes jeg WHERE jgp.id_groupe=jeg.id_groupe AND jeg.login='$login_eleve' AND jgp.login='$login_user';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$retour=true;
			}
			// Donner le droit au PP?
		}
		elseif(($statut=="cpe")&&(getSettingAOui('GepiAccesCdtCpe'))) {
			$retour=true;
		}
		elseif(($statut=="cpe")&&(getSettingAOui('GepiAccesCdtCpeRestreint'))) {
			$sql="SELECT 1=1 FROM j_eleves_cpe WHERE e_login='$login_eleve' AND cpe_login='$login_user';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$retour=true;
			}
		}
		elseif(($statut=="scolarite")&&(getSettingAOui('GepiAccesCdtScol'))) {
			$retour=true;
		}
		elseif(($statut=="scolarite")&&(getSettingAOui('GepiAccesCdtScolRestreint'))) {
			$sql="SELECT 1=1 FROM j_scol_classes jsc, j_eleves_classes jec WHERE jsc.id_classe=jec.id_classe AND jsc.login='$login_user' AND jec.login='$login_eleve';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$retour=true;
			}
		}
	}

	return $retour;
}
?>
