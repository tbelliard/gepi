<?php
/*
 * $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*/

function generate_token() {
    if (!isset($_SESSION["gepi_alea"])) {
		$length = rand(35, 45);
		for($len=$length,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
		// Virer le gepi_alea par la suite
		$_SESSION["gepi_alea"] = $r;
		//$_SESSION["token"] = $r;
	
		if(getSettingValue('csrf_log')=='y') {
			$csrf_log_chemin=getSettingValue('csrf_log_chemin');
			if($csrf_log_chemin=='') {$csrf_log_chemin="/home/root/csrf";}
			if(isset($_SESSION['login'])) {
				$f=fopen("$csrf_log_chemin/csrf_".$_SESSION['login'].".log","a+");
				//$f=fopen("$csrf_log_chemin/csrf_".$login.".log","a+");
				fwrite($f,"Initialisation de la session ".strftime("%a %d/%m/%Y %H:%M:%S")." avec\n\$_SESSION['gepi_alea']=".$_SESSION['gepi_alea']."\n");
				fwrite($f,"session_id()=".session_id()."\n");
				fclose($f);
			}
		}
    }
}

function add_token_field($avec_id=false) {
	// Dans une page, il ne devrait y avoir qu'un seul appel ‡ add_token_field(true), les autres... dans les autres formulaires Ètant avec add_token_field()
	// A VOIR... on pourrait utiliser une variable globale pour... si l'id csrf_alea est dÈj‡ dÈfini ne plus l'ajouter...

	if($avec_id) {
		return "<input type='hidden' name='csrf_alea' id='csrf_alea' value='".$_SESSION['gepi_alea']."' />\n";
	}
	else {
		return "<input type='hidden' name='csrf_alea' value='".$_SESSION['gepi_alea']."' />\n";
	}
}

function add_token_in_url($html_chars = true) {
	if($html_chars) {
		return "&amp;csrf_alea=".$_SESSION['gepi_alea'];
	}
	else {
		return "&csrf_alea=".$_SESSION['gepi_alea'];
	}
}

function add_token_in_js_func() {
	return $_SESSION['gepi_alea'];
}

function check_token($redirection=true) {
	// Avant le Header, on appelle check_token()
	// AprËs le Header, on appelle check_token(false)
	global $niveau_arbo;
	global $gepiPath;
	global $gepiShowGenTime;

	$csrf_alea=isset($_POST['csrf_alea']) ? $_POST['csrf_alea'] : (isset($_GET['csrf_alea']) ? $_GET['csrf_alea'] : "");

	if(isset($niveau_arbo)) {
		if($niveau_arbo=="0") {
		}
		elseif($niveau_arbo==1) {
			$pref_arbo="..";
		}
		elseif($niveau_arbo==2) {
			$pref_arbo="../..";
		}
		elseif($niveau_arbo==3) {
			$pref_arbo="../../..";
		}
		elseif ($niveau_arbo == "public") {
			$pref_arbo="..";
			// A REVOIR... SI C'EST PUBLIC, ON N'EST PAS LOGUÈ
			// NORMALEMENT, EN PUBLIC on ne devrait pas avoir de page sensible
		}
	}
	else {
		$pref_arbo="..";
	}

	if(getSettingValue('csrf_mode')=='strict') {
		if($csrf_alea!=$_SESSION['gepi_alea']) {
			action_alea_invalide();
			if($redirection) {
				header("Location: $pref_arbo/accueil.php?msg=OpÈration non autorisÈe");
			}
			else {
				echo "<p style='color:red'>OpÈration non autorisÈe</p>\n";
				require("$pref_arbo/lib/footer.inc.php");
			}
			die();
		}
	}
	elseif(getSettingValue('csrf_mode')=='mail_seul') {
		if($csrf_alea!=$_SESSION['gepi_alea']) {
			action_alea_invalide();
		}
	}
	else {
		if($csrf_alea!=$_SESSION['gepi_alea']) {
			// Sans mail
			action_alea_invalide(false);
		}
	}
}

/**
 * Action en cas d'attaque CSRF
 *
 */
function action_alea_invalide($envoyer_mail=true) {
	//debug_var();
	/*
	$_SERVER['REQUEST_URI']=	/steph/gepi-trunk/lib/confirm_query.php
	$_SERVER['SCRIPT_NAME']=	/steph/gepi-trunk/lib/confirm_query.php
	$_SERVER['PHP_SELF']=	/steph/gepi-trunk/lib/confirm_query.php
	*/

	// NE pas donner dans le mail les valeurs du token pour Èviter des problËmes lors d'une Èventuelle capture du mail.

	$details="La personne victime de l'attaque Ètait ".$_SESSION['login'].".\n";
	$details.="La page cible Ètait ".$_SERVER['PHP_SELF']." avec les variables suivantes:\n";
	$details.="Variables en \$_POST:\n";
	foreach($_POST as $key => $value) {
		//if(!is_array($value)) {
			$details.="   \$_POST[$key]=$value\n";
		/*
		}
		else {

		}
		*/
	}

	$details.="Variables en \$_GET:\n";
	foreach($_GET as $key => $value) {
		$details.="   \$_GET[$key]=$value\n";
	}

	if($envoyer_mail) {
		// Envoyer un mail ‡ l'admin
		$envoi_mail_actif=getSettingValue('envoi_mail_actif');
		if($envoi_mail_actif!="n") {
			$destinataire=getSettingValue('gepiAdminAdress');
			if($destinataire!='') {
				$sujet="Attaque CSRF";
				$message="La variable csrf_alea ne coincide pas avec le gepi_alea en SESSION.\n";
				$message.=$details;
				envoi_mail($sujet, $message,$destinataire);
			}
		}
	}

	if(getSettingValue('csrf_log')=='y') {
		$csrf_log_chemin=getSettingValue('csrf_log_chemin');
		if($csrf_log_chemin=='') {$csrf_log_chemin="/home/root/csrf";}
		$f=fopen("$csrf_log_chemin/csrf_".$_SESSION['login'].".log","a+");
		fwrite($f,"Alerte CSRF ".strftime("%a %d/%m/%Y %H:%M:%S")." avec\n");
		fwrite($f,"\$_SESSION['gepi_alea']=".$_SESSION['gepi_alea']."\n");
		fwrite($f,$details."\n");
		fwrite($f,"================================================\n");
		fclose($f);
	}
}

/**
 * Envoi de mail
 *
 */
function envoi_mail($sujet, $message, $destinataire, $ajout_headers='') {

	$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";

	if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}

	if($ajout_headers!='') {$ajout_headers="\r\n".$ajout_headers;}

  $subject = $gepiPrefixeSujetMail."GEPI : $sujet";
  $subject = "=?ISO-8859-1?B?".base64_encode($subject)."?=\r\n";
  
  $headers = "X-Mailer: PHP/" . phpversion()."\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
  $headers .= "From: Mail automatique Gepi <ne-pas-repondre@".$_SERVER['SERVER_NAME'].">\r\n";
  $headers .= $ajout_headers;

	// On envoie le mail
	$envoi = mail($destinataire,
		$subject,
		$message,
	  $headers);
}

/**
 * Verification de la validitÈ d'un mot de passe
 * longueur : getSettingValue("longmin_pwd") minimum
 * composÈ de lettre et d'au moins un chiffre
 *
 * @global string $char_spec liste des caractËres spÈciaux
 * @param string $password Mot de passe
 * @param boolean $flag Si $flag = 1, il faut Ègalement au moins un caractËres spÈcial (voir $char_spec dans global.inc)
 * @return boolean false/true
 */
function verif_mot_de_passe($password,$flag) {
	global $char_spec;
	if ($flag == 1) {
		//if(ereg("(^[a-zA-Z]*$)|(^[0-9]*$)", $password)) {
		if(my_ereg("(^[a-zA-Z]*$)|(^[0-9]*$)", $password)) {
			return false;
		}
		elseif(preg_match("/^[[:alnum:]\W]{".getSettingValue("longmin_pwd").",}$/", $password) and preg_match("/[\W]+/", $password) and preg_match("/[0-9]+/", $password)) {
			return true;
		}
		else {
			return false;
		}
	}
	else {
		//if(ereg("(^[a-zA-Z]*$)|(^[0-9]*$)", $password)) {
		if(my_ereg("(^[a-zA-Z]*$)|(^[0-9]*$)", $password)) {
			return false;
		}
		elseif (strlen($password) < getSettingValue("longmin_pwd")) {
			return false;
		}
		else {
			return true;
		}
	}
}

/**
 * Teste si le login existe dÈj‡ dans la base
 *
 * @param string $s le login testÈ
 * @return string yes/no
 */
function test_unique_login($s) {
    // On vÈrifie que le login ne figure pas dÈj‡ dans la base utilisateurs
    $test1 = mysql_num_rows(mysql_query("SELECT login FROM utilisateurs WHERE (login='$s' OR login='".strtoupper($s)."')"));
    if ($test1 != "0") {
        return 'no';
    } else {
        $test2 = mysql_num_rows(mysql_query("SELECT login FROM eleves WHERE (login='$s' OR login = '".strtoupper($s)."')"));
        if ($test2 != "0") {
            return 'no';
        } else {
			$test3 = mysql_num_rows(mysql_query("SELECT login FROM resp_pers WHERE (login='$s' OR login='".strtoupper($s)."')"));
			if ($test3 != "0") {
				return 'no';
			} else {
	            return 'yes';
	        }
        }
    }
}

/**
 * fonction vÈrifiant l'unicitÈ du login
 * On vÈrifie que le login ne figure pas dÈj‡ dans une des bases ÈlËve des annÈes passÈes (??)
 *
 * @param string $s le login ‡ vÈrifier
 * @param <type> $indice ??
 * @return string yes/no
 */
function test_unique_e_login($s, $indice) {
    //  $s
    //
/*    $test1 = mysql_num_rows(mysql_query("SELECT login FROM a1_eleves WHERE (login='$s')"));
    $test2 = mysql_num_rows(mysql_query("SELECT login FROM a2_eleves WHERE (login='$s')"));
    $test3 = mysql_num_rows(mysql_query("SELECT login FROM a3_eleves WHERE (login='$s')"));
    $test4 = mysql_num_rows(mysql_query("SELECT login FROM a4_eleves WHERE (login='$s')"));
    $test5 = mysql_num_rows(mysql_query("SELECT login FROM a5_eleves WHERE (login='$s')"));
    $test6 = mysql_num_rows(mysql_query("SELECT login FROM a6_eleves WHERE (login='$s')"));
*/
    // On vÈrifie que le login ne figure pas dÈj‡ dans la base utilisateurs
    $test7 = mysql_num_rows(mysql_query("SELECT login FROM utilisateurs WHERE (login='$s' OR login='".strtoupper($s)."')"));
//    if (($test1 != "0") or ($test2 != "0") or ($test3 != "0") or ($test4 != "0") or ($test5 != "0") or ($test6 != "0") or ($test7 != "0")) {

    if ($test7 != "0") {

        // Si le login figure dÈj‡ dans une des bases ÈlËve des annÈes passÈes ou bien
        // dans la base utilisateurs, on retourne 'no' !
        return 'no';
    } else {
        // Si le login ne figure pas dans une des bases ÈlËve des annÈes passÈes ni dans la base
        // utilisateurs, on vÈrifie qu'un mÍme login ne vient pas d'Ítre attribuÈ !
        $test_tempo2 = mysql_num_rows(mysql_query("SELECT col2 FROM tempo2 WHERE (col2='$s' or col2='".strtoupper($s)."')"));
        if ($test_tempo2 != "0") {
            return 'no';
        } else {
            $reg = mysql_query("INSERT INTO tempo2 VALUES ('$indice', '$s')");
            return 'yes';
        }
    }
}

//
/**
 * Fonction pour gÈnÈrer le login ‡ partir du nom et du prÈnom
 * Le mode de gÈnÈration doit Ítre passÈ en argument
 *
 * @param string $_nom
 * @param string $_prenom
 * @param string $_mode
 * @return string
 */
function generate_unique_login($_nom, $_prenom, $_mode) {

	if ($_mode == null) {
		$_mode = "fname8";
	}
    // On gÈnËre le login
    //$_prenom = strtr($_prenom, "ÈËÎÍ…»À ¸˚‹€Ôœ‰‡ƒ¿", "eeeeEEEEuuUUiIaaAA");
	$_prenom = strtr($_prenom, "ÁÈËÎÍ…»À ¸˚˘‹€ÔÓœŒ‰‚‡ƒ¬¿", "ceeeeEEEEuuuUUiiIIaaaAAA");
    $_prenom = preg_replace("/[^a-zA-Z.\-]/", "", $_prenom);
    //$_nom = strtr($_nom, "ÈËÎÍ…»À ¸˚‹€Ôœ‰‡ƒ¿", "eeeeEEEEuuUUiIaaAA");
	$_nom = strtr($_nom, "ÁÈËÎÍ…»À ¸˚˘‹€ÔÓœŒ‰‚‡ƒ¬¿", "ceeeeEEEEuuuUUiiIIaaaAAA");
    $_nom = preg_replace("/[^a-zA-Z.\-]/", "", $_nom);

	if($_nom=='') {return false;}

    if ($_mode == "name") {
            $temp1 = $_nom;
            //$temp1 = strtoupper($temp1);
            $temp1 = my_ereg_replace(" ","", $temp1);
            $temp1 = my_ereg_replace("-","_", $temp1);
            $temp1 = my_ereg_replace("'","", $temp1);
            //$temp1 = substr($temp1,0,8);
        } elseif ($_mode == "name8") {
            $temp1 = $_nom;
            //$temp1 = strtoupper($temp1);
            $temp1 = my_ereg_replace(" ","", $temp1);
            $temp1 = my_ereg_replace("-","_", $temp1);
            $temp1 = my_ereg_replace("'","", $temp1);
            $temp1 = substr($temp1,0,8);
        } elseif ($_mode == "fname8") {
			if($_prenom=='') {return false;}
            $temp1 = $_prenom{0} . $_nom;
            //$temp1 = strtoupper($temp1);
            $temp1 = my_ereg_replace(" ","", $temp1);
            $temp1 = my_ereg_replace("-","_", $temp1);
            $temp1 = my_ereg_replace("'","", $temp1);
            $temp1 = substr($temp1,0,8);
        } elseif ($_mode == "fname19") {
			if($_prenom=='') {return false;}
            $temp1 = $_prenom{0} . $_nom;
            //$temp1 = strtoupper($temp1);
            $temp1 = my_ereg_replace(" ","", $temp1);
            $temp1 = my_ereg_replace("-","_", $temp1);
            $temp1 = my_ereg_replace("'","", $temp1);
            $temp1 = substr($temp1,0,19);
        } elseif ($_mode == "firstdotname") {
			if($_prenom=='') {return false;}
            $temp1 = $_prenom . "." . $_nom;
            //$temp1 = strtoupper($temp1);

            $temp1 = my_ereg_replace(" ","", $temp1);
            $temp1 = my_ereg_replace("-","_", $temp1);
            $temp1 = my_ereg_replace("'","", $temp1);
            //$temp1 = substr($temp1,0,19);
        } elseif ($_mode == "firstdotname19") {
			if($_prenom=='') {return false;}
            $temp1 = $_prenom . "." . $_nom;
            //$temp1 = strtoupper($temp1);
            $temp1 = my_ereg_replace(" ","", $temp1);
            //$temp1 = my_ereg_replace("-","_", $temp1);
            $temp1 = my_ereg_replace("'","", $temp1);
            $temp1 = substr($temp1,0,19);
        } elseif ($_mode == "namef8") {
			if($_prenom=='') {return false;}
			//echo "\$_nom=$_nom<br />";
			//echo "\$_prenom=$_prenom<br />";
            $temp1 =  substr($_nom,0,7) . $_prenom{0};
            //$temp1 = strtoupper($temp1);
            $temp1 = my_ereg_replace(" ","", $temp1);
            $temp1 = my_ereg_replace("-","_", $temp1);
            $temp1 = my_ereg_replace("'","", $temp1);
            //$temp1 = substr($temp1,0,8);
        } else {
        	return false;
        }

        $login_user = $temp1;

		//echo "\$login_user=$login_user<br /><hr width='100' />";
		/*
        // On teste l'unicitÈ du login que l'on vient de crÈer
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

		echo "\$login_user=$login_user<br />";
		*/

        // Nettoyage final
        $login_user = substr($login_user, 0, 50);
        $login_user = preg_replace("/[^A-Za-z0-9._\-]/","",trim($login_user));

		//echo "\$login_user=$login_user<br />";

        $test1 = $login_user{0};
		while ($test1 == "_" OR $test1 == "-" OR $test1 == ".") {
			$login_user = substr($login_user, 1);
			$test1 = $login_user{0};
		}

		$test1 = $login_user{strlen($login_user)-1};
		while ($test1 == "_" OR $test1 == "-" OR $test1 == ".") {
			$login_user = substr($login_user, 0, strlen($login_user)-1);
			$test1 = $login_user{strlen($login_user)-1};
		}

        // On teste l'unicitÈ du login que l'on vient de crÈer
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

		//echo "\$login_user=$login_user<br />";

		return $login_user;
}

/**
 * Fonction qui propose l'ordre d'affichage du nom, prÈnom et de la civilitÈ en fonction des rÈglages de la classe de l'ÈlËve
 *
 * @param string $login
 * @param integer $id_classe
 * @return string
 */
function affiche_utilisateur($login,$id_classe) {
    $req = mysql_query("select nom, prenom, civilite from utilisateurs where login = '".$login."'");
	//$tmp="mysql_num_rows($req)=".mysql_num_rows($req);
    $nom = @mysql_result($req, 0, 'nom');
    $prenom = @mysql_result($req, 0, 'prenom');
    $civilite = @mysql_result($req, 0, 'civilite');
    $req_format = mysql_query("select format_nom from classes where id = '".$id_classe."'");
    $format = mysql_result($req_format, 0, 'format_nom');
    $result = "";
    $i='';
    if ((($format == 'ni') OR ($format == 'in') OR ($format == 'cni') OR ($format == 'cin')) and ($prenom != '')) {
        $temp = explode("-", $prenom);
        $i = substr($temp[0], 0, 1);
        //if (isset($temp[1]) and ($temp[1] != '')) $i .= ".-".substr($temp[1], 0, 1);
        if (isset($temp[1]) and ($temp[1] != '')) $i .= "-".substr($temp[1], 0, 1);
        $i .= ". ";
    }
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
    $result = $nom." ".$prenom;

    }
    return $result;
    //return $tmp;
}

/**
 * Verifie si l'extension d_base est active
 *
 * @return echo rÈponse
 */
function verif_active_dbase() {
    if (!function_exists("dbase_open"))  {
        echo "<center><p class=grand>ATTENTION : PHP n'est pas configurÈ pour gÈrer les fichiers GEP (dbf).
        <br />L'extension d_base n'est pas active. Adressez-vous ‡ l'administrateur du serveur pour corriger le problËme.</p></center></body></html>";
        die();
    }
}

/**
 * Verifie si un groupe appartient bien ‡ la personne connectÈe
 *
 * @deprecated Cette fonction ne peut plus fonctionner car la requÍte SQL n'est plus valide
 * @param integer $id_groupe identifiant du groupe
 * @return boolean 0/1
 */
function verif_groupe_appartient_prof($id_groupe) {
    $test = mysql_query("select id from groupes where (id='$id_groupe' and login_user = '".$_SESSION['login']."')");
    if (mysql_num_rows($test) == 0) {
        return 0;
    } else {
        return 1;
    }
}

/**
 * Verifie si un ÈlËve appartient ‡ un groupe
 *
 * @deprecated La requÍte SQL n'est plus possible
 * @param integer $id_eleve
 * @param integer $id_groupe
 * @return boolean 0/1
 */
function verif_eleve_dans_groupe($id_eleve, $id_groupe) {
    if ($id_groupe != "-1") {
        // On verifie l'appartenance de id_eleve au groupe id_groupe
        if (!(verif_groupe_appartient_prof($id_groupe))) {
            return 0;
            exit();
        }
        $test = mysql_query("select id_eleve from groupe_eleve where (id_eleve='$id_eleve' and id_groupe = '$id_groupe')");
        if (mysql_num_rows($test) == 0) {
            return 0;
        } else {
            return 1;
        }
    } else {
        // On verifie l'appartenance de id_eleve ‡ un groupe quelconque du professeur connectÈ
        $test = mysql_query("select id_eleve from groupe_eleve ge, groupes g where (ge.id_eleve='$id_eleve' and ge.id_groupe = g.id and g.login_user='".$_SESSION['login']."')");
        if (mysql_num_rows($test) == 0) {
            return 0;
        } else {
            return 1;
        }

    }
}

/**
 * Construit un tableau des groupes de l'utilisateur connectÈ
 *
 * @deprecated La requÍte SQL n'est plus possible
 * @return array
 */
function make_tables_of_groupes() {
    $tab_groupes = array();
    $test = mysql_query("select id, nom_court, nom_complet from groupes where login_user = '".$_SESSION['login']."'");
    $nb_test = mysql_num_rows($test);
    $i = 0;
    while ($i < $nb_test) {
      $tab_groupes['id'][$i] = mysql_result($test, $i, "id");
      $tab_groupes['nom'][$i] = mysql_result($test, $i, "nom_court");
      $tab_groupes['nom_complet'][$i] = mysql_result($test, $i, "nom_complet");
      $i++;
    }
      return $tab_groupes;
}

/**
 * Construit un tableau des classes et matiËres de l'utilisateur connectÈ (professeur)
 *
 * @return array Liste des classes et matiËres de ce professeur
 */
function make_tables_of_classes_matieres () {
  $tab_class_mat = array();
  $appel_classes = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
  $nb_classes = mysql_num_rows($appel_classes);
  $i = 0;
  while($i < $nb_classes){
    $id_classe = mysql_result($appel_classes, $i, "id");
    $appel_mat = mysql_query("SELECT DISTINCT m.matiere, m.nom_complet " .
            "FROM matieres m, j_groupes_matieres jgm, j_groupes_classes jgc, j_groupes_professeurs jgp" .
            " WHERE ( " .
            "m.matiere = jgm.id_matiere and " .
            "jgm.id_groupe = jgp.id_groupe and " .
            "jgp.login = '" . $_SESSION['login'] . "' and " .
            "jgp.id_groupe = jgc.id_groupe and " .
            "jgc.id_classe='$id_classe')" .
            " ORDER BY m.nom_complet");
    $nb_mat = mysql_num_rows($appel_mat);
    $j = 0;
    while($j < $nb_mat){
      $tab_class_mat['id_c'][] = $id_classe;
      $tab_class_mat['id_m'][] = mysql_result($appel_mat, $j, "matiere");
      $tab_class_mat['nom_c'][] = mysql_result($appel_classes, $i, "classe");
      $tab_class_mat['nom_m'][] = mysql_result($appel_mat, $j, "nom_complet");
      $j++;
    }
    $i++;
  }
  return $tab_class_mat;
}

/**
 * Construit un tableau des classes de l'utilisateur connectÈ (professeur)
 *
 * @return array Liste des classes (id_classe)
 */
function make_tables_of_classes () {
  $tab_class = array();
  $appel_classes = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
  $nb_classes = mysql_num_rows($appel_classes);
  $i = 0;
  $nb=0;
  while($i < $nb_classes){
    $id_classe = mysql_result($appel_classes, $i, "id");
    $test_prof_classe = mysql_query("SELECT DISTINCT jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (" .
            "jgc.id_classe='$id_classe' and " .
            "jgc.id_groupe = jgp.id_groupe and " .
            "jgp.login='".$_SESSION['login']."')");
    $test = mysql_num_rows($test_prof_classe);
    if ($test != 0) {
        $tab_class[$nb] = $id_classe;
        $nb++;
    }
    $i++;
  }
  return $tab_class;
}

/**
 * Construit du html pour les cahiers de textes
 *
 * @deprecated La requÍte SQL n'est plus valide
 * @param string $link Le lien
 * @param <type> $current_classe
 * @param <type> $current_matiere
 * @param integer $year annÈe
 * @param integer $month le mois
 * @param integer $day le jour
 * @return string echo rÈsultat
 */
function make_area_list_html($link, $current_classe, $current_matiere, $year, $month, $day) {
  echo "<strong><em>Cahier&nbsp;de&nbsp;texte&nbsp;de&nbsp;:</em></strong><br />";
  $appel_donnees = mysql_query("SELECT * FROM classes ORDER BY classe");
  $lignes = mysql_num_rows($appel_donnees);
  $i = 0;
  while($i < $lignes){
    $id_classe = mysql_result($appel_donnees, $i, "id");
    $appel_mat = mysql_query("SELECT DISTINCT m.* FROM matieres m, j_classes_matieres_professeurs j WHERE (j.id_classe='$id_classe' AND j.id_matiere=m.matiere) ORDER BY m.nom_complet");
    $nb_mat = mysql_num_rows($appel_mat);
    $j = 0;
    while($j < $nb_mat){
      $flag2 = "no";
      $matiere_nom = mysql_result($appel_mat, $j, "nom_complet");
      $matiere_nom_court = mysql_result($appel_mat, $j, "matiere");
      $id_matiere = mysql_result($appel_mat, $j, "matiere");
      $call_profs = mysql_query("SELECT * FROM j_classes_matieres_professeurs WHERE ( id_classe='$id_classe' and id_matiere = '$id_matiere') ORDER BY ordre_prof");
      $nombre_profs = mysql_num_rows($call_profs);
      $k = 0;
      while ($k < $nombre_profs) {
        $temp = strtoupper(@mysql_result($call_profs, $k, "id_professeur"));
        if ($temp == $_SESSION['login']) {$flag2 = "yes";}
        $k++;
      }
      if ($flag2 == "yes") {
        echo "<strong>";
        $display_class = mysql_result($appel_donnees, $i, "classe");
        if (($id_classe == $current_classe) and ($id_matiere == $current_matiere)) {
           echo ">$display_class&nbsp;-&nbsp;$matiere_nom&nbsp;($matiere_nom_court)&nbsp;";
        } else {
           //echo "<a href=\"".$link."?id_classe=$id_classe&id_matiere=$id_matiere&year=$year&month=$month&day=$day\">$display_class&nbsp;-&nbsp;$matiere_nom&nbsp;($matiere_nom_court)</a>";
       echo "<a href=\"".$link."?id_classe=$id_classe&amp;id_matiere=$id_matiere&amp;year=$year&amp;month=$month&amp;day=$day\">$display_class&nbsp;-&nbsp;$matiere_nom&nbsp;($matiere_nom_court)</a>";

        }
        echo "</strong><br />";
      }
      $j++;
    }
    $i++;
  }
}

/**
 * Ecrit une balise <select> de date jour mois annÈe
 * correction W3C : ajout de la balise de fin </option> ‡ la fin de $out_html
 * CrÈation d'un label pour passer les tests WAI
 *
 * @param string $prefix l'attribut name sera de la forme $prefixday, $prefixMois,...
 * @param integer $day
 * @param integer $month
 * @param integer $year
 * @param string $option Si = more_years, on ajoute +5 et -5 annÈes aux annÈes possibles
 */
function genDateSelector($prefix, $day, $month, $year, $option)
{
    if($day   == 0) $day = date("d");
    if($month == 0) $month = date("m");
    if($year  == 0) $year = date("Y");

	// correction w3c : SELECT NAME -> select name + label + <span>
	 echo "\n<label for=\"${prefix}jour\"><span style='display:none;'>Jour</span></label>\n";
    echo "<select id=\"${prefix}jour\" name=\"${prefix}day\">\n";

	// correction w3c : OPTION -> option + =\"selected\
    for($i = 1; $i <= 31; $i++)
        //echo "<option" . ($i == $day ? " selected=\"selected\"" : "") . ">$i</option>\n";
        echo "<option value = \"$i\"" . ($i == $day ? " selected=\"selected\"" : "") . ">$i</option>\n";

        //echo "<OPTION" . ($i == $day ? " SELECTED" : "") . ">$i\n";

	// correction w3c : SELECT NAME -> select name
    echo "</select>\n";

	// correction w3c : SELECT NAME -> select name + label + <span>
	 echo "\n<label for=\"${prefix}mois\"><span style='display:none;'>Mois</span></label>\n";
    echo "<select id=\"${prefix}mois\" name=\"${prefix}month\">\n";

    for($i = 1; $i <= 12; $i++)
    {
        $m = strftime("%b", mktime(0, 0, 0, $i, 1, $year));

        //print "<OPTION VALUE=\"$i\"" . ($i == $month ? " SELECTED" : "") . ">$m\n";

        // Si problËme avec l'encodage, essayer la ligne suivante
        //print "<OPTION VALUE=\"$i\"" . ($i == $month ? " SELECTED" : "") . ">".iconv('UTF-8','ISO-8859-1', $m)."</option>\n";
	// correction w3c : OPTION VALUE -> option value + =\"selected\
        echo "<option value=\"$i\"" . ($i == $month ? " selected=\"selected\"" : "") . ">$m</option>\n";
    }

	// correction w3c : SELECT NAME -> select name
    echo "</select>\n";

    //echo "<select name=\"${prefix}year\">\n";
	// correction w3c : SELECT NAME -> select name + label + <span>
	 echo "\n<label for=\"${prefix}annee\"><span style='display:none;'>AnnÈe</span></label>\n";
    echo "<select id=\"${prefix}annee\" name=\"${prefix}year\">\n";

    $min = strftime("%Y", getSettingValue("begin_bookings"));
    if ($option == "more_years") $min = date("Y") - 5;

    $max = strftime("%Y", getSettingValue("end_bookings"));
    if ($option == "more_years") $max = date("Y") + 5;

    for($i = $min; $i <= $max; $i++)
	// correction w3c : OPTION SELECTED -> option selected + =\"selected\
        print "<option" . ($i == $year ? " selected=\"selected\"" : "") . ">$i</option>\n";
        //print "<OPTION" . ($i == $year ? " SELECTED" : "") . ">$i\n";

	// correction w3c : SELECT -> select
    echo "</select>\n";
}

/**
 * DÈtruit les conteneurs vides qui ne sont pas rattachÈs ‡ un parent (@‡ vÈrifier)
 *
 * @param integer $id_conteneur
 * @param <type> $id_racine ??
 */
function test_conteneurs_vides($id_conteneur,$id_racine) {
        // On teste si le conteneur est vide
        if ($id_conteneur !=0) {
            $sql= mysql_query("SELECT id FROM cn_devoirs WHERE id_conteneur='$id_conteneur'");
            $nb_dev = mysql_num_rows($sql);
            $sql= mysql_query("SELECT id FROM cn_conteneurs WHERE parent='$id_conteneur'");
            $nb_cont = mysql_num_rows($sql);
            if (($nb_dev == 0) or ($nb_cont == 0)) {
                $query_parent = mysql_query("SELECT parent FROM cn_conteneurs WHERE id='$id_conteneur'");
                $id_par = mysql_result($query_parent, 0, 'parent');
                $sql = mysql_query("DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$id_conteneur'");
//                if ($id_conteneur != $id_racine) $sql = mysql_query("DELETE FROM cn_conteneurs WHERE id='$id_conteneur'");
                test_conteneurs_vides($id_par,$id_racine);
            }
        }
}

function mise_a_jour_moyennes_conteneurs($_current_group, $periode_num,$id_racine,$id_conteneur,&$arret) {
    //remarque : les variables $periode_num et id_racine auraient pus Ítre rÈcupÈrÈes
    //‡ partir de $id_conteneur, mais on Èvite ainsi trop de calculs !

    foreach ($_current_group["eleves"][$periode_num]["list"] as $_eleve_login) {
		if($_eleve_login!=""){
			calcule_moyenne($_eleve_login, $id_racine, $id_conteneur);
		}
    }

    if ($arret != 'yes') {
        //
        // DÈtermination du conteneur parent
        $query_id_parent = mysql_query("SELECT parent FROM cn_conteneurs WHERE id='$id_conteneur'");
        $id_parent = mysql_result($query_id_parent, 0, 'parent');
        if ($id_parent != 0) {
            $arret = 'no';
            mise_a_jour_moyennes_conteneurs($_current_group, $periode_num,$id_racine,$id_parent,$arret);
        } else {
            $arret = 'yes';
            mise_a_jour_moyennes_conteneurs($_current_group, $periode_num,$id_racine,$id_racine,$arret);
        }

    }
}



//
// Liste des sous-conteneur d'un conteneur
//
function sous_conteneurs($id_conteneur,&$nb_sous_cont,&$nom_sous_cont,&$coef_sous_cont,&$id_sous_cont,&$display_bulletin_sous_cont,$type) {
	fdebug("===================================\n");
	fdebug("LANCEMENT DE sous_conteneurs() SUR\n");
	fdebug("id_conteneur=$id_conteneur avec type=$type\n");

    $query_sous_cont = mysql_query("SELECT * FROM cn_conteneurs WHERE (parent ='$id_conteneur' and id!='$id_conteneur') order by nom_court");
    $nb = mysql_num_rows($query_sous_cont);
    $i=0;
    while ($i < $nb) {
        $nom_sous_cont[$nb_sous_cont] = mysql_result($query_sous_cont, $i, 'nom_court');
        $coef_sous_cont[$nb_sous_cont] = mysql_result($query_sous_cont, $i, 'coef');
        $id_sous_cont[$nb_sous_cont] = mysql_result($query_sous_cont, $i, 'id');
        $display_bulletin_sous_cont[$nb_sous_cont] = mysql_result($query_sous_cont, $i, 'display_bulletin');
        $temp = $id_sous_cont[$nb_sous_cont];
        $nb_sous_cont++;
        if ($type=='all') {
            sous_conteneurs($temp,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'all');
        }
        $i++;
    }
}

function fdebug($texte){
	// Passer la variable ‡ "y" pour activer le remplissage du fichier de debug pour calcule_moyenne()
	$local_debug="n";
	if($local_debug=="y") {
		$fich=fopen("/tmp/calcule_moyenne.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}

//
// Calcul de la moyenne d'un ÈlËve
//
function calcule_moyenne($login, $id_racine, $id_conteneur) {
	fdebug("===================================\n");
	fdebug("LANCEMENT DE calcule_moyenne SUR\n");
	fdebug("login=$login: id_racine=$id_racine - id_conteneur=$id_conteneur\n");

    $total_point = 0;
    $somme_coef = 0;
    $exist_dev_fac = '';
    // On efface les moyennes de la table
    $sql="DELETE FROM cn_notes_conteneurs WHERE (login='$login' and id_conteneur='$id_conteneur');";
	fdebug("$sql\n");
    $delete = mysql_query($sql);

    // Appel des paramËtres du conteneur
    $appel_conteneur = mysql_query("SELECT * FROM cn_conteneurs WHERE id ='$id_conteneur'");
    $arrondir =  mysql_result($appel_conteneur, 0, 'arrondir');
    $mode =  mysql_result($appel_conteneur, 0, 'mode');
    $ponderation = mysql_result($appel_conteneur, 0, 'ponderation');

	fdebug("Conteneur n∞$id_conteneur\n");
	fdebug("\$arrondir=$arrondir\n");
	fdebug("\$mode=$mode\n");
	fdebug("\$ponderation=$ponderation\n");


    // DÈtermination des sous-conteneurs ‡ prendre en compte
    $nom_sous_cont = array();
    $id_sous_cont  = array();
    $coef_sous_cont = array();
    $nb_sous_cont = 0;
    if ($mode==1) {
        //la moyenne s'effectue sur toutes les notes contenues ‡ la racine ou dans les sous-conteneurs
        // sans tenir compte des options dÈfinies dans cette(ces) boÓte(s).

        //
        // on s'intÈresse ‡ tous les conteneurs fils, petit-fils, ...
        sous_conteneurs($id_conteneur,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'all');
        //
        // On fait la moyenne des devoirs du conteneur et des sous-conteneurs
        $nb_boucle = $nb_sous_cont+1;
        $id_cont[0] = $id_conteneur;
        $i=1;
        while ($i < $nb_boucle) {
            $id_cont[$i] = $id_sous_cont[$i-1];
            $i++;
        }

    } else {
        //la moyenne s'effectue sur toutes les notes contenues ‡ la racine du conteneur
        //et sur les moyennes du ou des sous-conteneurs, en tenant compte des options dans ce(s) boÓte(s).

        // On s'intÈresse uniquement aux conteneurs fils
        sous_conteneurs($id_conteneur,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'');
        //
        // on ne fait la moyenne que des devoirs du conteneur
        $nb_boucle = 1;
        $id_cont[0] = $id_conteneur;

    }



    //
    // Prise en compte de la pondÈration
    // Calcul de l'indice du coefficient ‡ pondÈrer
    //
    if ($ponderation != 0) {
        $sql="SELECT * FROM cn_devoirs WHERE id_conteneur='$id_conteneur' ORDER BY date,id";
		fdebug("$sql\n");
        $appel_dev = mysql_query($sql);
        $nb_dev  = mysql_num_rows($appel_dev);
		fdebug("\$nb_dev=$nb_dev\n");
        $max = 0;
        $indice_pond = 0;
        $k = 0;
        while ($k < $nb_dev) {
            $id_dev = mysql_result($appel_dev, $k, 'id');
            $coef[$k] = mysql_result($appel_dev, $k, 'coef');
			fdebug("\$id_dev=$id_dev : \$coef[$k]=$coef[$k]\n");
            $sql="SELECT * FROM cn_notes_devoirs WHERE (login='$login' AND id_devoir='$id_dev')";
			fdebug("$sql\n");
            $note_query = mysql_query($sql);
            $statut = @mysql_result($note_query, 0, "statut");
            $note = @mysql_result($note_query, 0, "note");
			fdebug("\$nb_dev=$nb_dev\n");
            if (($statut == '') and ($note!='')) {
                if (($note > $max) or (($note == $max) and ($coef[$k] > $coef[$indice_pond]))) {
                    $max = $note;
                    $indice_pond = $k;
                }
            }
            $k++;
        }
    }
	if(isset($indice_pond)) {
		fdebug("\$indice_pond=$indice_pond\n");
		fdebug("\$max=$max\n");
	}


    //
    // Calcul du total des points et de la somme des coefficients
    //
	// Pour $mode==1, pour les devoirs ‡ Bonus, il faudrait faire la liste de tous les devoirs situÈs dans le conteneur et les sous-conteneurs triÈs par date et parcourir ici ces devoirs au lieu de faire une boucle sur la liste des sous-conteneurs (while ($j < $nb_boucle))
    $j=0;
	//=========================
	// AJOUT: boireaus 20080202
	$m=0;
	//=========================
    while ($j < $nb_boucle) {
		//=========================
		// MODIF: boireaus 20080202
        //$appel_dev = mysql_query("SELECT * FROM cn_devoirs WHERE id_conteneur='$id_cont[$j]'");
        $sql="SELECT * FROM cn_devoirs WHERE id_conteneur='$id_cont[$j]' ORDER BY date,id";
		fdebug("$sql\n");
        $appel_dev = mysql_query($sql);
		//=========================
        $nb_dev  = mysql_num_rows($appel_dev);
        $k = 0;
        while ($k < $nb_dev) {
            $id_dev = mysql_result($appel_dev, $k, 'id');
			fdebug("\n\$id_dev=$id_dev\n");

            $coef[$k] = mysql_result($appel_dev, $k, 'coef');
			fdebug("\$coef[$k]=$coef[$k]\n");

            $note_sur[$k] = mysql_result($appel_dev, $k, 'note_sur');
			fdebug("\$note_sur[$k]=$note_sur[$k]\n");

            $ramener_sur_referentiel[$k] = mysql_result($appel_dev, $k, 'ramener_sur_referentiel');
			fdebug("\$ramener_sur_referentiel[$k]=$ramener_sur_referentiel[$k]\n");

            // Prise en compte de la pondÈration
            if (($ponderation != 0) and ($j==0) and ($k==$indice_pond)) $coef[$k] = $coef[$k] + $ponderation;
			fdebug("\$ponderation=$ponderation\n");

            $facultatif[$k] = mysql_result($appel_dev, $k, 'facultatif');
			fdebug("\$facultatif[$k]=$facultatif[$k]\n");

            $note_query = mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$login' AND id_devoir='$id_dev')");
            $statut = @mysql_result($note_query, 0, "statut");
			fdebug("\$statut=$statut\n");

            $note = @mysql_result($note_query, 0, "note");
			fdebug("\$note=$note\n");

            if (($statut == '') and ($note!='')) {
                if ($note_sur[$k] != getSettingValue("referentiel_note")) {
                    if ($ramener_sur_referentiel[$k] != 'V') {
                        //on ramene la note sur le referentiel mais on modifie le coefficient pour prendre en compte le rÈfÈrentiel
                        $note = $note * getSettingValue("referentiel_note") / $note_sur[$k];
                        $coef[$k] = $coef[$k] * $note_sur[$k] / getSettingValue("referentiel_note");
                    } else {
                        //on fait comme si c'Ètait une note sur le referentiel avec une regle de trois ;)
                        $note = $note * getSettingValue("referentiel_note") / $note_sur[$k];
                    }
                }
                fdebug("Correction note autre que sur referentiel : \$note=$note\n");
                fdebug("Correction note autre que sur referentiel : \$coef[$k]=$coef[$k]\n");

                if ($facultatif[$k] == 'O') {
                    // le devoir n'est pas facultatif (Obligatoire) et entre systÈmatiquement dans le calcul de la moyenne si le coef est diffÈrent de zÈro
					fdebug("\$total_point = $total_point + $coef[$k] * $note = ");
                    $total_point = $total_point + $coef[$k]*$note;
					fdebug("$total_point\n");

					fdebug("\$somme_coef = $somme_coef + $coef[$k] = ");
                    $somme_coef = $somme_coef + $coef[$k];
					fdebug("$somme_coef\n");
                } else if ($facultatif[$k] == 'B') {
                    //le devoir est facultatif comme un bonus : seuls les points supÈrieurs ‡ 10 sont pris en compte dans le calcul de la moyenne.
                    if ($note > ($note_sur[$k]/2)) {
                        $total_point = $total_point + $coef[$k]*$note;
                        $somme_coef = $somme_coef + $coef[$k];
                    }
                } else {
                    //$facultatif == 'N' le devoir est facultatif comme une note : Le devoir est pris en compte dans la moyenne uniquement s'il amÈliore la moyenne de l'ÈlËve.
                    $exist_dev_fac = 'yes';
					//=========================
					// MODIF: boireaus 20080202
                    /*
					$total_point = $total_point + $coef[$k]*$note;
                    $somme_coef = $somme_coef + $coef[$k];
                    $points[$k] = $coef[$k]*$note;
                    */
					// On ne compte pas la note dans la moyenne pour le moment.
					// On regardera plus loin si cela amÈliore la moyenne ou non.
					$f_coef[$m]=$coef[$k];
					$points[$m] = $f_coef[$m]*$note;

					fdebug("\$points[$m]=$points[$m]\n");
					fdebug("\$f_coef[$m]=$f_coef[$m]\n");

					$m++;
					//=========================
                }
				fdebug("\$total_point=$total_point\n");
				fdebug("\$somme_coef=$somme_coef\n");
				/*
				if(isset($points[$k])){
					fdebug("\$points[$k]=$points[$k]\n");
				}
				*/
            }
            $k++;
        }
        $j++;
    }


    //
    // Prise en comptes des sous-conteneurs si mode=2
    //
    if ($mode == 2) {
		fdebug("\$mode=$mode\n");
        $j=0;
        while ($j < $nb_sous_cont) {
            $sql="SELECT coef FROM cn_conteneurs WHERE id='$id_sous_cont[$j]'";
			fdebug("$sql\n");
            $appel_cont = mysql_query($sql);
            $coefficient = mysql_result($appel_cont, 0, 'coef');
			fdebug("\$coefficient=$coefficient\n");

            $sql="SELECT * FROM cn_notes_conteneurs WHERE (login='$login' AND id_conteneur='$id_sous_cont[$j]')";
			fdebug("$sql\n");
            $moyenne_query = mysql_query($sql);
            $statut_moy = @mysql_result($moyenne_query, 0, "statut");
			fdebug("\$statut_moy=$statut_moy\n");

            if ($statut_moy == 'y') {
                $moy = @mysql_result($moyenne_query, 0, "note");
				fdebug("\$moy=$moy\n");

				fdebug("\$somme_coef = $somme_coef + $coefficient = ");
                $somme_coef = $somme_coef + $coefficient;
				fdebug("$somme_coef\n");

				fdebug("\$total_point = $total_point + $coefficient * $moy = ");
                $total_point = $total_point + $coefficient*$moy;
				fdebug("$total_point\n");
            }
            $j++;
        }
    }


    //
    // calcul de la moyenne des Èvaluations
    //
	//=========================
	// A FAIRE: boireaus 20080202
	// Il faudrait considÈrer le cas vicieux: prÈsence de note ‡ bonus et pas d'autre note...
    if ($somme_coef != 0) {
	//=========================
		fdebug("\$moyenne= = $total_point / $somme_coef = ");
        $moyenne = $total_point/$somme_coef;
		fdebug($moyenne."\n");
        //
        // si un des devoirs a l'option "N", on prend la meilleure moyenne :
        //
		// Ca ne fonctionne bien que pour $mode==2
		// Pour $mode==1, il faudrait faire la liste de tous les devoirs situÈs dans le conteneur et les sous-conteneurs triÈs par date et parcourir ces devoirs plus haut au lieu de faire une boucle sur la liste des sous-conteneurs
        if ($exist_dev_fac == 'yes') {
			fdebug("\$exist_dev_fac=".$exist_dev_fac."\n");
			/*
            $k=0;
            while ($k < $nb_dev) {
                if ((($somme_coef - $coef[$k]) != 0) and ($facultatif[$k]=='N')) {
                    if (isset($points[$k])) {
                       $points[$k] = ($total_point-$points[$k])/($somme_coef - $coef[$k]);
						fdebug("\$points[$k]=$points[$k]\n");
						fdebug("\$moyenne=max($moyenne,$points[$k])=");
                       $moyenne = max($moyenne,$points[$k]);
						fdebug("$moyenne\n");
                    }
                }
                $k++;
            }
			*/
			$m=0;
            while ($m<count($points)) {
				fdebug("count(\$points)=".count($points)."\n");
				if((isset($points[$m]))&&(isset($f_coef[$m]))) {
					fdebug("\$points[$m]=$points[$m] et \$f_coef[$m]=$f_coef[$m]\n");
					$tmp_moy=($total_point+$points[$m])/($somme_coef+$f_coef[$m]);
					fdebug("\$tmp_moy=$tmp_moy et \$moyenne=$moyenne\n");
					if($tmp_moy>$moyenne){
						$moyenne=$tmp_moy;
						$total_point=$total_point+$points[$m];
						$somme_coef=$somme_coef+$f_coef[$m];
					}
					fdebug("\$moyenne=$moyenne\n");
				}
				$m++;
			}
        }

		fdebug("Moyenne avant arrondi: $moyenne\n");

/*
if(($login=='RUQUIER_S')&&($id_racine=='2916')&&($id_conteneur=='2917')) {
	echo "<div style='color:red;'><b> ($login, $id_racine, $id_conteneur)</b>
Moyenne avant arrondi: $moyenne<br />
   \$moyenne=$moyenne<br />
   10*\$moyenne=".(10*$moyenne)."<br />
   ceil(10*\$moyenne)=".ceil(10*$moyenne)."<br />
   ceil(10*\$moyenne)/10=".(ceil(10*$moyenne)/10)."<br />
   number_format(ceil(10*\$moyenne)/10,1,'.','')=".number_format(ceil(10*$moyenne)/10,1,'.','')."<br />
   number_format(ceil(100*\$moyenne)/100,1,'.','')=".number_format(ceil(100*$moyenne)/100,1,'.','')."<br />
   printf('%.40f', (10*\$moyenne))=".printf('%.40f', (10*$moyenne))."<br />
   ceil(strval((10*\$moyenne))=".ceil(strval((10*$moyenne)))."
<div>\n";
}
*/
        //
        // Calcul des arrondis
        //
        if ($arrondir == 's1') {
            // s1 : arrondir au dixiËme de point supÈrieur
			fdebug("Mode s1:
   \$moyenne=$moyenne
   10*\$moyenne=".(10*$moyenne)."
   ceil(10*\$moyenne)=".ceil(10*$moyenne)."
   ceil(10*\$moyenne)/10=".(ceil(10*$moyenne)/10)."
   number_format(ceil(10*\$moyenne)/10,1,'.','')=".number_format(ceil(10*$moyenne)/10,1,'.','')."
   number_format(ceil(100*\$moyenne)/100,1,'.','')=".number_format(ceil(100*$moyenne)/100,1,'.','')."\n");
            //$moyenne = number_format(ceil(10*$moyenne)/10,1,'.','');
			$moyenne = number_format(ceil(strval(10*$moyenne))/10,1,'.','');
        } else if ($arrondir == 's5') {
            // s5 : arrondir au demi-point supÈrieur
            $moyenne = number_format(ceil(strval(2*$moyenne))/2,1,'.','');
        } else if ($arrondir == 'se') {
            // se : arrondir au point entier supÈrieur
            $moyenne = number_format(ceil(strval($moyenne)),1,'.','');
        } else if ($arrondir == 'p1') {
            // s1 : arrondir au dixiËme le plus proche
            $moyenne = number_format(round(strval(10*$moyenne))/10,1,'.','');
        } else if ($arrondir == 'p5') {
            // s5 : arrondir au demi-point le plus proche
            $moyenne = number_format(round(strval(2*$moyenne))/2,1,'.','');
        } else if ($arrondir == 'pe') {
            // se : arrondir au point entier le plus proche
            $moyenne = number_format(round(strval($moyenne)),1,'.','');
        }

        $sql="INSERT INTO cn_notes_conteneurs SET login='$login', id_conteneur='$id_conteneur',note='$moyenne',statut='y',comment='';";
		fdebug("$sql\n");
        $register = mysql_query($sql);

    } else {
        $sql="INSERT INTO cn_notes_conteneurs SET login='$login', id_conteneur='$id_conteneur',note='0',statut='',comment='';";
		fdebug("$sql\n");
        $register = mysql_query($sql);

    }

}

//
// Affichage de la liste des conteneurs
//
function affiche_devoirs_conteneurs($id_conteneur,$periode_num, &$empty, $ver_periode) {
	global $tabdiv_infobulle;
	global $gepiClosedPeriodLabel;
	global $id_groupe;
	global $eff_groupe;
	if((isset($id_groupe))&&(!isset($eff_groupe))) {
		$sql="SELECT 1=1 FROM j_eleves_groupes WHERE id_groupe='$id_groupe' AND periode='$periode_num';";
		//echo "$sql<br />";
		$res_ele_grp=mysql_query($sql);
		$eff_groupe=mysql_num_rows($res_ele_grp);
	}
	//
	// Cas particulier de la racine
	//
	//$message_cont = "Etes-vous s˚r de vouloir supprimer le conteneur ci-dessous et les Èvaluations qu\\'il contient ?";
	$gepi_denom_boite=getSettingValue("gepi_denom_boite");
	if(getSettingValue("gepi_denom_boite_genre")=='m'){
		//$lela="le";$il_ou_elle="il";
		$message_cont = "Etes-vous s˚r de vouloir supprimer le ".getSettingValue("gepi_denom_boite")." ci-dessous ?";
		$message_cont_non_vide = "Le ".getSettingValue("gepi_denom_boite")." est non vide. Il ne peut pas Ítre supprimÈ.";
	}
	else{
		//$lela="la";$il_ou_elle="elle";
		$message_cont = "Etes-vous s˚r de vouloir supprimer la ".getSettingValue("gepi_denom_boite")." ci-dessous ?";
		$message_cont_non_vide = "La ".getSettingValue("gepi_denom_boite")." est non vide. Elle ne peut pas Ítre supprimÈe.";
	}
	//$message_cont = "Etes-vous s˚r de vouloir supprimer $lela ".getSettingValue("gepi_denom_boite")." ci-dessous et les Èvaluations qu\\'il contient ?";
	//$message_cont = "Etes-vous s˚r de vouloir supprimer $lela ".getSettingValue("gepi_denom_boite")." ci-dessous ?";
	//$message_cont_non_vide = ucfirst($lela)." ".getSettingValue("gepi_denom_boite")." est non vide. ".ucfirst($il_ou_elle)." ne peut pas Ítre supprimÈ.";
	$message_dev = "Etes-vous s˚r de vouloir supprimer l\\'Èvaluation ci-dessous et les notes qu\\'elle contient ?";
	//$appel_conteneurs = mysql_query("SELECT * FROM cn_conteneurs WHERE (parent='0' and id_racine='$id_conteneur')");
	$sql="SELECT * FROM cn_conteneurs WHERE (parent='0' and id_racine='$id_conteneur')";
	//echo "$sql<br />\n";
	$appel_conteneurs = mysql_query($sql);
	$nb_cont = mysql_num_rows($appel_conteneurs);
	if ($nb_cont != 0) {
		echo "<ul>\n";
		$id_cont = mysql_result($appel_conteneurs, 0, 'id');
		$id_parent = mysql_result($appel_conteneurs, 0, 'parent');
		$id_racine = mysql_result($appel_conteneurs, 0, 'id_racine');
		$nom_conteneur = mysql_result($appel_conteneurs, 0, 'nom_court');
		echo "<li>\n";
		echo "$nom_conteneur ";
		//echo "id=$id_cont id_racine=$id_racine parent=$id_parent ";
		if ($ver_periode <= 1) {
			echo " (<strong>".$gepiClosedPeriodLabel."</strong>) ";
		}
		echo "- <a href='saisie_notes.php?id_conteneur=$id_cont'>Visualisation</a> - <a href = 'add_modif_conteneur.php?id_conteneur=$id_cont&amp;mode_navig=retour_index'>Configuration</a>\n";
		$appel_dev = mysql_query("select * from cn_devoirs where id_conteneur='$id_cont' order by date");
		$nb_dev  = mysql_num_rows($appel_dev);
		if ($nb_dev != 0) {$empty = 'no';}
		if ($ver_periode >= 2) {
			$j = 0;
			if($nb_dev>0){
				echo "<ul>\n";
				while ($j < $nb_dev) {
					$nom_dev = mysql_result($appel_dev, $j, 'nom_court');
					$id_dev = mysql_result($appel_dev, $j, 'id');
					echo "<li>\n";
					echo "<font color='green'>$nom_dev</font>";
					echo " - <a href='saisie_notes.php?id_conteneur=$id_cont&amp;id_devoir=$id_dev'>Saisie</a>";

					//$sql="SELECT 1=1 FROM cn_notes_devoirs WHERE id_devoir='$id_dev' AND statut!='-' AND statut!='v';";
					//$sql="SELECT 1=1 FROM cn_notes_devoirs cnd, j_eleves_classes jec WHERE cnd.id_devoir='$id_dev' AND cnd.statut!='-' AND cnd.statut!='v' AND jec.login=cnd.login AND jec.periode='$periode_num';";
					$sql="SELECT 1=1 FROM cn_notes_devoirs cnd, j_eleves_classes jec WHERE cnd.id_devoir='$id_dev' AND cnd.statut!='v' AND jec.login=cnd.login AND jec.periode='$periode_num';";
					//echo "$sql<br />";
					$res_eff_dev=mysql_query($sql);
					$eff_dev=mysql_num_rows($res_eff_dev);
					echo " <span title=\"Effectif des notes saisies/effectif total de l'enseignement\" style='font-size:small;";
					if(isset($eff_groupe)) {if($eff_dev==$eff_groupe) {echo "color:green;";} else {echo "color:red;";}}
					echo "'>($eff_dev";
					if(isset($eff_groupe)) {echo "/$eff_groupe";}
					echo ")</span>";

					// Pour dÈtecter une anomalie:
					 $sql="SELECT * FROM cn_notes_devoirs cnd, j_eleves_classes jec WHERE cnd.id_devoir='$id_dev' AND cnd.statut!='v' AND jec.login=cnd.login AND jec.periode='$periode_num' AND jec.login not in (select login from j_eleves_groupes where id_groupe='$id_groupe' and periode='$periode_num');";
					//echo "$sql<br />"; // DÈcommenter et exÈcuter dans une console mysql ou dans phpMyAdmin
					$test_anomalie=mysql_query($sql);
					if(mysql_num_rows($test_anomalie)>0) {
						$titre_infobulle="Note pour un fantÙme";
						$texte_infobulle="Une ou des notes existent pour un ou des ÈlËves qui ne sont plus inscrits dans cet enseignement&nbsp;:<br />";
						$cpt_ele_anomalie=0;
						while($lig_anomalie=mysql_fetch_object($test_anomalie)) {
							if($cpt_ele_anomalie>0) {$texte_infobulle.=", ";}
							$texte_infobulle.=get_nom_prenom_eleve($lig_anomalie->login,'avec_classe')."&nbsp;(<i>";
							if($lig_anomalie->statut=='') {$texte_infobulle.=$lig_anomalie->note;}
							elseif($lig_anomalie->statut=='v') {$texte_infobulle.="_";}
							else {$texte_infobulle.=$lig_anomalie->statut;}
							$texte_infobulle.="</i>)";
							$cpt_ele_anomalie++;
						}
						$texte_infobulle.="<br />";
						$texte_infobulle.="Cliquer <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;clean_anomalie_dev=$id_dev".add_token_in_url()."'>ici</a> pour supprimer les notes associÈes?";
						$tabdiv_infobulle[]=creer_div_infobulle('anomalie_'.$id_dev,$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

						echo " <a href=\"#\" onclick=\"afficher_div('anomalie_$id_dev','y',100,100);return false;\"><img src='../images/icons/flag.png' width='17' height='18' /></a>";
					}

					//echo " - <a href = 'add_modif_dev.php?id_conteneur=$id_conteneur&amp;id_devoir=$id_dev&amp;mode_navig=retour_index'>Configuration</a> - <a href = 'index.php?id_racine=$id_racine&amp;del_dev=$id_dev' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_dev)."', '".$message_dev."')\">Suppression</a>\n";
					echo " - <a href = 'add_modif_dev.php?id_conteneur=$id_conteneur&amp;id_devoir=$id_dev&amp;mode_navig=retour_index'>Configuration</a>";

					$display_parents=mysql_result($appel_dev, $j, 'display_parents');
					$coef=mysql_result($appel_dev, $j, 'coef');
					echo " (<i><span title='Coefficient $coef'>$coef</span> ";
					if($display_parents==1) {echo "<img src='../images/icons/visible.png' width='19' height='16' title='Evaluation visible sur le relevÈ de notes' alt='Evaluation visible sur le relevÈ de notes' />";}
					else {echo " <img src='../images/icons/invisible.png' width='19' height='16' title='Evaluation non visible sur le relevÈ de notes' alt='Evaluation non visible sur le relevÈ de notes' />\n";}
					echo "</i>)";
					//echo " - <a href = 'index.php?id_racine=$id_racine&amp;del_dev=$id_dev&amp;alea=".$_SESSION['gepi_alea']."' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_dev)."', '".$message_dev."')\">Suppression</a>\n";
					echo " - <a href = 'index.php?id_racine=$id_racine&amp;del_dev=$id_dev".add_token_in_url()."' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_dev)."', '".$message_dev."')\">Suppression</a>\n";
					echo "</li>\n";
					$j++;
				}
				echo "</ul>\n";
			}
		}
	}
	if ($ver_periode >= 2) {
		$appel_conteneurs = mysql_query("SELECT * FROM cn_conteneurs WHERE (parent='$id_conteneur') order by nom_court");
		$nb_cont = mysql_num_rows($appel_conteneurs);
		if($nb_cont>0) {
			echo "<ul>\n";
			$i = 0;
			while ($i < $nb_cont) {
				$id_cont = mysql_result($appel_conteneurs, $i, 'id');
				$id_parent = mysql_result($appel_conteneurs, $i, 'parent');
				$id_racine = mysql_result($appel_conteneurs, $i, 'id_racine');
				$nom_conteneur = mysql_result($appel_conteneurs, $i, 'nom_court');
				if ($id_cont != $id_parent) {
					echo "<li>\n";
					//echo "$nom_conteneur - <a href='saisie_notes.php?id_conteneur=$id_cont'>Visualisation</a> - <a href = 'add_modif_conteneur.php?id_conteneur=$id_cont&amp;mode_navig=retour_index'>Configuration</a> - <a href = 'index.php?id_racine=$id_racine&amp;del_cont=$id_cont' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_conteneur)."', '".$message_cont."')\">Suppression</a>\n";
					echo "$nom_conteneur - <a href='saisie_notes.php?id_conteneur=$id_cont'>Visualisation</a>";
					echo " - <a href = 'add_modif_conteneur.php?id_conteneur=$id_cont&amp;mode_navig=retour_index'>Configuration</a>\n";

					$display_bulletin=mysql_result($appel_conteneurs, $i, 'display_bulletin');
					$coef=mysql_result($appel_conteneurs, $i, 'coef');
					echo " (<i><span title='Coefficient $coef'>$coef</span> ";
					if($display_bulletin==1) {echo "<img src='../images/icons/visible.png' width='19' height='16' title='$gepi_denom_boite visible sur le bulletin' alt='$gepi_denom_boite visible sur le bulletin' />";}
					else {echo " <img src='../images/icons/invisible.png' width='19' height='16' title='$gepi_denom_boite non visible sur le bulletin' alt='$gepi_denom_boite non visible sur le bulletin' />\n";}
					echo "</i>)";

					//$appel_dev = mysql_query("select * from cn_devoirs where id_conteneur='$id_cont'");
					$appel_dev = mysql_query("select * from cn_devoirs where id_conteneur='$id_cont' order by date");
					$nb_dev  = mysql_num_rows($appel_dev);
					if ($nb_dev != 0) {$empty = 'no';}

					// Existe-t-il des sous-conteneurs?
					$sql="SELECT 1=1 FROM cn_conteneurs WHERE (parent='$id_cont')";
					$test_sous_cont=mysql_query($sql);
					$nb_sous_cont=mysql_num_rows($test_sous_cont);
					//echo "<br />$sql<br />$nb_sous_cont<br />";

					if(($nb_dev==0)&&($nb_sous_cont==0)) {
						//echo " - <a href = 'index.php?id_racine=$id_racine&amp;del_cont=$id_cont' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_conteneur)."', '".$message_cont."')\">Suppression</a>\n";
						//echo " - <a href = 'index.php?id_racine=$id_racine&amp;del_cont=$id_cont&amp;alea=".$_SESSION['gepi_alea']."' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_conteneur)."', '".$message_cont."')\">Suppression</a>\n";
						echo " - <a href = 'index.php?id_racine=$id_racine&amp;del_cont=$id_cont".add_token_in_url()."' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_conteneur)."', '".$message_cont."')\">Suppression</a>\n";
					}
					else {
						echo " - <a href = '#' onclick='alert(\"$message_cont_non_vide\")'><font color='gray'>Suppression</font></a>\n";
					}

					$j = 0;
					if($nb_dev>0) {
						echo "<ul>\n";
						while ($j < $nb_dev) {
							$nom_dev = mysql_result($appel_dev, $j, 'nom_court');
							$id_dev = mysql_result($appel_dev, $j, 'id');
							echo "<li>\n";
							echo "<font color='green'>$nom_dev</font> - <a href='saisie_notes.php?id_conteneur=$id_cont&amp;id_devoir=$id_dev'>Saisie</a>";

							//$sql="SELECT 1=1 FROM cn_notes_devoirs WHERE id_devoir='$id_dev' AND statut!='-' AND statut!='v';";
							$sql="SELECT 1=1 FROM cn_notes_devoirs cnd, j_eleves_classes jec WHERE cnd.id_devoir='$id_dev' AND cnd.statut!='-' AND cnd.statut!='v' AND jec.login=cnd.login AND jec.periode='$periode_num';";
							//echo "$sql<br />";
							$res_eff_dev=mysql_query($sql);
							$eff_dev=mysql_num_rows($res_eff_dev);
							echo " <span title=\"Effectif des notes saisies/effectif total de l'enseignement\" style='font-size:small;";
							if(isset($eff_groupe)) {if($eff_dev==$eff_groupe) {echo "color:green;";} else {echo "color:red;";}}
							echo "'>($eff_dev";
							if(isset($eff_groupe)) {echo "/$eff_groupe";}
							echo ")</span>";

							// Pour dÈtecter une anomalie:
							$sql="SELECT * FROM cn_notes_devoirs cnd, j_eleves_classes jec WHERE cnd.id_devoir='$id_dev' AND cnd.statut!='v' AND jec.login=cnd.login AND jec.periode='$periode_num' AND jec.login not in (select login from j_eleves_groupes where id_groupe='$id_groupe' and periode='$periode_num');";
							//echo "$sql<br />"; // DÈcommenter et exÈcuter dans une console mysql ou dans phpMyAdmin
							$test_anomalie=mysql_query($sql);
							if(mysql_num_rows($test_anomalie)>0) {
								$titre_infobulle="Note pour un fantÙme";
								$texte_infobulle="Une ou des notes existent pour un ou des ÈlËves qui ne sont plus inscrits dans cet enseignement&nbsp;:<br />";
								$cpt_ele_anomalie=0;
								while($lig_anomalie=mysql_fetch_object($test_anomalie)) {
									if($cpt_ele_anomalie>0) {$texte_infobulle.=", ";}
									$texte_infobulle.=get_nom_prenom_eleve($lig_anomalie->login,'avec_classe')."&nbsp;(<i>";
									if($lig_anomalie->statut=='') {$texte_infobulle.=$lig_anomalie->note;}
									elseif($lig_anomalie->statut=='v') {$texte_infobulle.="_";}
									else {$texte_infobulle.=$lig_anomalie->statut;}
									$texte_infobulle.="</i>)";
									$cpt_ele_anomalie++;
								}
								$texte_infobulle.="<br />";
								$texte_infobulle.="Cliquer <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;clean_anomalie_dev=$id_dev".add_token_in_url()."'>ici</a> pour supprimer les notes associÈes?";
								$tabdiv_infobulle[]=creer_div_infobulle('anomalie_'.$id_dev,$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');
		
								echo " <a href=\"#\" onclick=\"afficher_div('anomalie_$id_dev','y',100,100);return false;\"><img src='../images/icons/flag.png' width='17' height='18' /></a>";
							}

							//echo " - <a href = 'add_modif_dev.php?id_conteneur=$id_conteneur&amp;id_devoir=$id_dev&amp;mode_navig=retour_index'>Configuration</a> - <a href = 'index.php?id_racine=$id_racine&amp;del_dev=$id_dev' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_dev)."', '".$message_dev."')\">Suppression</a>\n";
							echo " - <a href = 'add_modif_dev.php?id_conteneur=$id_conteneur&amp;id_devoir=$id_dev&amp;mode_navig=retour_index'>Configuration</a>";

							$display_parents=mysql_result($appel_dev, $j, 'display_parents');
							$coef=mysql_result($appel_dev, $j, 'coef');
							echo " (<i><span title='Coefficient $coef'>$coef</span> ";
							if($display_parents==1) {echo "<img src='../images/icons/visible.png' width='19' height='16' title='Evaluation visible sur le relevÈ de notes' alt='Evaluation visible sur le relevÈ de notes' />";}
							else {echo " <img src='../images/icons/invisible.png' width='19' height='16' title='Evaluation non visible sur le relevÈ de notes' alt='Evaluation non visible sur le relevÈ de notes' />\n";}
							echo "</i>)";

							//echo " - <a href = 'index.php?id_racine=$id_racine&amp;del_dev=$id_dev&amp;alea=".$_SESSION['gepi_alea']."' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_dev)."', '".$message_dev."')\">Suppression</a>\n";
							echo " - <a href = 'index.php?id_racine=$id_racine&amp;del_dev=$id_dev".add_token_in_url()."' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_dev)."', '".$message_dev."')\">Suppression</a>\n";
							echo "</li>\n";
							$j++;
						}
						echo "</ul>\n";
					}
				}
				if ($id_conteneur != $id_cont) {affiche_devoirs_conteneurs($id_cont,$periode_num, $empty,$ver_periode);}
				if ($id_cont != $id_parent) {
					echo "</li>\n";
				}
				$i++;
			}
			echo "</ul>\n";
		}
	}
	if ($empty != 'no') return 'yes';
}


function checkAccess() {
    global $gepiPath;
    $url = parse_url($_SERVER['REQUEST_URI']);
    if ($_SESSION["statut"] == 'autre') {

    	$sql = "SELECT autorisation
	    from droits_speciaux
    	where nom_fichier = '" . substr($url['path'], strlen($gepiPath)) . "'
		AND id_statut = '" . $_SESSION['statut_special_id'] . "'";

    }else{

		$sql = "select " . $_SESSION['statut'] . "
	    from droits
    	where id = '" . substr($url['path'], strlen($gepiPath)) . "'
    	;";

	}

    $dbCheckAccess = sql_query1($sql);
    if (substr($url['path'], 0, strlen($gepiPath)) != $gepiPath) {
        tentative_intrusion(2, "Tentative d'accËs avec modification sauvage de gepiPath");
        return (false);
    } else {
        if ($dbCheckAccess == 'V') {
            return (true);
        } else {
            tentative_intrusion(1, "Tentative d'accËs ‡ un fichier sans avoir les droits nÈcessaires");
            return (false);
        }
    }
}

function Verif_prof_cahier_notes ($_login,$_id_racine) {
    if(empty($_login) || empty($_id_racine)) {return false;die();}
    $test_prof = mysql_query("SELECT id_groupe FROM cn_cahier_notes WHERE id_cahier_notes ='" . $_id_racine . "'");
    $_id_groupe = mysql_result($test_prof, 0, 'id_groupe');

    $call_prof = mysql_query("SELECT login FROM j_groupes_professeurs WHERE (id_groupe='".$_id_groupe."' and login='" . $_login . "')");
    $nb = mysql_num_rows($call_prof);

    if ($nb != 0) {
        return true;
    } else {
        return false;
    }
}


function Verif_prof_classe_matiere ($login,$id_classe,$matiere) {
    if(empty($login) || empty($id_classe) || empty($matiere)) {return false;}
    $call_prof = mysql_query("SELECT id_professeur FROM j_classes_matieres_professeurs WHERE (id_classe='".$id_classe."' AND id_matiere='".$matiere."')");
    $nb_profs = mysql_num_rows($call_prof);
    $k = 0;
    $flag = 0;
    while ($k < $nb_profs) {
        $prof = @mysql_result($call_prof, $k, "id_professeur");
        if (strtolower($login) == strtolower($prof)) {$flag = 1;}
        $k++;
    }
    if ($flag == 0) {
        return false;
    } else {
        return true;
    }
}
//***********************************************************************************************
function affich_aid($affiche_graph, $affiche_rang, $affiche_coef, $test_coef,$affiche_nbdev,$indice_aid, $aid_id,$current_eleve_login,$periode_num,$id_classe,$style_bulletin) {
    //============================
    // AJOUT: boireaus
    global $min_max_moyclas;
    //============================

    $call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = $indice_aid");
    $AID_NOM_COMPLET = @mysql_result($call_data, 0, "nom_complet");
    $note_max = @mysql_result($call_data, 0, "note_max");
    $type_note = @mysql_result($call_data, 0, "type_note");
    $message = @mysql_result($call_data, 0, "message");
    $display_nom = @mysql_result($call_data, 0, "display_nom");
    $display_end = @mysql_result($call_data, 0, "display_end");


    $aid_nom_query = mysql_query("SELECT nom FROM aid WHERE (id='$aid_id' and indice_aid='$indice_aid')");
    $aid_nom = @mysql_result($aid_nom_query, 0, "nom");
    //------
    // On regarde maintenant quelle sont les profs responsables de cette AID
    $aid_prof_resp_query = mysql_query("SELECT id_utilisateur FROM j_aid_utilisateurs WHERE (id_aid='$aid_id'  and indice_aid='$indice_aid')");
    $nb_lig = mysql_num_rows($aid_prof_resp_query);
    $n = '0';
    while ($n < $nb_lig) {
        $aid_prof_resp_login[$n] = mysql_result($aid_prof_resp_query, $n, "id_utilisateur");
        $n++;
    }
    //------
    // On appelle l'apprÈciation de l'ÈlËve, et sa note
    //------
    $current_eleve_aid_appreciation_query = mysql_query("SELECT * FROM aid_appreciations WHERE (login='$current_eleve_login' AND periode='$periode_num' and id_aid='$aid_id' and indice_aid='$indice_aid')");
    $current_eleve_aid_appreciation = @mysql_result($current_eleve_aid_appreciation_query, 0, "appreciation");
    $periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
    $periode_max = mysql_num_rows($periode_query);
    if ($type_note == 'last') {$last_periode_aid = min($periode_max,$display_end);}
    if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
        $place_eleve = "";
        $current_eleve_aid_note = @mysql_result($current_eleve_aid_appreciation_query, 0, "note");
        $current_eleve_aid_statut = @mysql_result($current_eleve_aid_appreciation_query, 0, "statut");
        if (($current_eleve_aid_statut == '') and ($note_max != 20) ) {
            $current_eleve_aid_appreciation = "(note sur ".$note_max.") ".$current_eleve_aid_appreciation;
        }
        if ($current_eleve_aid_note == '') {
            $current_eleve_aid_note = '-';
        } else {
            if ($affiche_graph == 'y')  {
                if ($current_eleve_aid_note<5) { $place_eleve=6;}
                if (($current_eleve_aid_note>=5) and ($current_eleve_aid_note<8))  { $place_eleve=5;}
                if (($current_eleve_aid_note>=8) and ($current_eleve_aid_note<10)) { $place_eleve=4;}
                if (($current_eleve_aid_note>=10) and ($current_eleve_aid_note<12)) {$place_eleve=3;}
                if (($current_eleve_aid_note>=12) and ($current_eleve_aid_note<15)) { $place_eleve=2;}
                if ($current_eleve_aid_note>=15) { $place_eleve=1;}
            }
            $current_eleve_aid_note=number_format($current_eleve_aid_note,1, ',', ' ');
        }
        $aid_note_min_query = mysql_query("SELECT MIN(note) note_min FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");

        $aid_note_min = @mysql_result($aid_note_min_query, 0, "note_min");
        if ($aid_note_min == '') {
            $aid_note_min = '-';
        } else {
            $aid_note_min=number_format($aid_note_min,1, ',', ' ');
        }
        $aid_note_max_query = mysql_query("SELECT MAX(note) note_max FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
        $aid_note_max = @mysql_result($aid_note_max_query, 0, "note_max");

        if ($aid_note_max == '') {
            $aid_note_max = '-';
        } else {
            $aid_note_max=number_format($aid_note_max,1, ',', ' ');
        }

        $aid_note_moyenne_query = mysql_query("SELECT round(avg(note),1) moyenne FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
        $aid_note_moyenne = @mysql_result($aid_note_moyenne_query, 0, "moyenne");
        if ($aid_note_moyenne == '') {
            $aid_note_moyenne = '-';
        } else {
            $aid_note_moyenne=number_format($aid_note_moyenne,1, ',', ' ');
        }

    }
    //------
    // On affiche l'apprÈciation aid :
    //------
    echo "<tr>\n<td style=\"height: ".getSettingValue("col_hauteur")."px; width: ".getSettingValue("col_matiere_largeur")."px;\"><span class='$style_bulletin'><strong>$AID_NOM_COMPLET</strong><br />";
    $chaine_prof="";
    $n = '0';
    while ($n < $nb_lig) {
        $chaine_prof.=affiche_utilisateur($aid_prof_resp_login[$n],$id_classe)."<br />";
        $n++;
    }
    if($n!=0){
	echo "<em>".$chaine_prof."</em>";
    }
    echo "</span></td>\n";
    if ($test_coef != 0 AND $affiche_coef == "y") echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";

    if ($affiche_nbdev=="y"){echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";}

    if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
	//==========================
	// MODIF: boireaus
	if($min_max_moyclas!=1){
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_min</span></td>\n";

		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_max</span></td>\n";

		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_moyenne</span></td>\n";
	}
	else{
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_min<br />\n";
		echo "$aid_note_max<br />\n";
		echo "$aid_note_moyenne</span></td>\n";
	}
	//==========================

	echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
	echo "<span class='$style_bulletin'><strong>";
	if ($current_eleve_aid_statut == '') {
		echo $current_eleve_aid_note;
	} else if ($current_eleve_aid_statut == 'other') {
		echo "-";
	} else {
		echo $current_eleve_aid_statut;
	}
	echo "</strong></span></td>\n";
    } else {
	//==========================
	// MODIF: boireaus
	if($min_max_moyclas!=1){
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
	}
	else{
		// On ne met pas trois tirets.
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
	}
	//==========================
	echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
    }
    if ($affiche_graph == 'y')  {
      if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid)))  {
        $quartile1_classe = sql_query1("SELECT COUNT( a.note ) as quartile1 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=15)");
        $quartile2_classe = sql_query1("SELECT COUNT( a.note ) as quartile2 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=12 AND a.note<15)");
        $quartile3_classe = sql_query1("SELECT COUNT( a.note ) as quartile3 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=10 AND a.note<12)");
        $quartile4_classe = sql_query1("SELECT COUNT( a.note ) as quartile4 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=8 AND a.note<10)");
        $quartile5_classe = sql_query1("SELECT COUNT( a.note ) as quartile5 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=5 AND a.note<8)");
        $quartile6_classe = sql_query1("SELECT COUNT( a.note ) as quartile6 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note<5)");
        echo "<td style=\"text-align: center; \"><img height=40 witdh=40 src='../visualisation/draw_artichow4.php?place_eleve=$place_eleve&temp1=$quartile1_classe&temp2=$quartile2_classe&temp3=$quartile3_classe&temp4=$quartile4_classe&temp5=$quartile5_classe&temp6=$quartile6_classe&nb_data=7' /></td>\n";
     } else
      echo "<td style=\"text-align: center; \"><span class='".$style_bulletin."'>-</span></td>\n";
    }
    if ($affiche_rang == 'y') echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";
    if (getSettingValue("bull_affiche_appreciations") == 'y') {
        echo "<td style=\"\" colspan=\"2\"><span class='$style_bulletin'>";
        if (($message != '') or ($display_nom == 'y')) {
            echo "$message ";
            if ($display_nom == 'y') {echo "<strong>$aid_nom</strong><br />";}
        }
    }
    echo "$current_eleve_aid_appreciation</span></td>\n</tr>\n";
    //------
}

function retourne_email ($login_u) {
$call = mysql_query("SELECT email FROM utilisateurs WHERE login = '$login_u'");
$email = @mysql_result($call, 0, "email");
return $email;

}
function parametres_tableau($larg_tab, $bord) {
    echo "<table border='1' width='680' cellspacing='1' cellpadding='1' summary=\"Tableau de paramËtres\">\n";
    echo "<tr><td><span class=\"norme\">largeur en pixel : <input type=\"text\" name=\"larg_tab\" size=\"3\" value=\"".$larg_tab."\" />\n";
    echo "bords en pixel : <input type=\"text\" name=\"bord\" size=\"3\" value=\"".$bord."\" />\n";
    echo "<input type=\"submit\" value=\"Valider\" />\n";
    echo "</span></td></tr></table>\n";
}
function affiche_tableau($nombre_lignes, $nb_col, $ligne1, $col, $larg_tab, $bord, $col1_centre, $col_centre, $couleur_alterne) {
    // $col1_centre = 1 --> la premiËre colonne est centrÈe
    // $col1_centre = 0 --> la premiËre colonne est alignÈe ‡ gauche
    // $col_centre = 1 --> toutes les autres colonnes sont centrÈes.
    // $col_centre = 0 --> toutes les autres colonnes sont alignÈes.
    // $couleur_alterne --> les couleurs de fond des lignes sont alternÈs
	global $num_debut_colonnes_matieres, $num_debut_lignes_eleves, $vtn_coloriser_resultats, $vtn_borne_couleur, $vtn_couleur_texte, $vtn_couleur_cellule;

	//echo "\$num_debut_colonnes_matieres=$num_debut_colonnes_matieres<br />";
	//echo "\$coloriser_resultats=$coloriser_resultats<br />";

    echo "<table border=\"$bord\" cellspacing=\"0\" width=\"$larg_tab\" cellpadding=\"1\" summary=\"Tableau\">\n";
    echo "<tr>\n";
    $j = 1;
    while($j < $nb_col+1) {
        //echo "<th class='small'>$ligne1[$j]</th>\n";
        echo "<th class='small' id='td_ligne1_$j'>$ligne1[$j]</th>\n";
        $j++;
    }
    echo "</tr>\n";
    $i = "0";
    $bg_color = "";
    $flag = "1";
    while($i < $nombre_lignes) {
        if ($couleur_alterne) {
            if ($flag==1) {$bg_color = "bgcolor=\"#C0C0C0\"";} else {$bg_color = "     ";}
        }

        echo "<tr>\n";
        $j = 1;
        while($j < $nb_col+1) {
            if ((($j == 1) and ($col1_centre == 0)) or (($j != 1) and ($col_centre == 0))) {

				echo "<td class='small' ".$bg_color;
				if(($vtn_coloriser_resultats=='y')&&($j>=$num_debut_colonnes_matieres)&&($i>=$num_debut_lignes_eleves)) {
					if(strlen(my_ereg_replace('[0-9.,]','',$col[$j][$i]))==0) {
						for($loop=0;$loop<count($vtn_borne_couleur);$loop++) {
							if(my_ereg_replace(',','.',$col[$j][$i])<=my_ereg_replace(',','.',$vtn_borne_couleur[$loop])) {
								echo " style='";
								if($vtn_couleur_texte[$loop]!='') {echo "color:$vtn_couleur_texte[$loop]; ";}
								if($vtn_couleur_cellule[$loop]!='') {echo "background-color:$vtn_couleur_cellule[$loop]; ";}
								echo "'";
								break;
							}
						}
					}
				}
				echo ">{$col[$j][$i]}</td>\n";

            } else {
				echo "<td align=\"center\" class='small' ".$bg_color;
				if(($vtn_coloriser_resultats=='y')&&($j>=$num_debut_colonnes_matieres)&&($i>=$num_debut_lignes_eleves)) {
					if(strlen(my_ereg_replace('[0-9.,]','',$col[$j][$i]))==0) {
						for($loop=0;$loop<count($vtn_borne_couleur);$loop++) {
							if(my_ereg_replace(',','.',$col[$j][$i])<=my_ereg_replace(',','.',$vtn_borne_couleur[$loop])) {
								echo " style='";
								if($vtn_couleur_texte[$loop]!='') {echo "color:$vtn_couleur_texte[$loop]; ";}
								if($vtn_couleur_cellule[$loop]!='') {echo "background-color:$vtn_couleur_cellule[$loop]; ";}
								echo "'";
								break;
							}
						}
					}
				}
				echo ">{$col[$j][$i]}</td>\n";
            }
            $j++;
        }
        echo "</tr>\n";
        if ($flag == "1") {$flag = "0";} else {$flag = "1";}
        $i++;
    }
    echo "</table>\n";
}

function dbase_filter($s){
  for($i = 0; $i < strlen($s); $i++){
    $code = ord($s[$i]);
    switch($code){
    case 129:    $s[$i] = "¸"; break;
    case 130:   $s[$i] = "È"; break;
    case 131:    $s[$i] = "‚"; break;
    case 132:    $s[$i] = "‰"; break;
    case 133:    $s[$i] = "‡"; break;
    case 135:    $s[$i] = "Á"; break;
    case 136:    $s[$i] = "Í"; break;
    case 137:    $s[$i] = "Î"; break;
    case 138:    $s[$i] = "Ë"; break;
    case 139:    $s[$i] = "Ô"; break;
    case 140:    $s[$i] = "Ó"; break;
    case 147:    $s[$i] = "Ù"; break;
    case 148:    $s[$i] = "ˆ"; break;
    case 150:    $s[$i] = "˚"; break;
    case 151:    $s[$i] = "˘"; break;
    }
  }
  return $s;
}

function detect_browser($HTTP_USER_AGENT) {
	// D'aprËs le fichier db_details_common.php de phpmyadmin
	/*
	$f=fopen("/tmp/detect_browser.txt","a+");
	fwrite($f,date("d/m/Y His").": $HTTP_USER_AGENT\n");
	fclose($f);
	*/
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
		/*
		} elseif((preg_match('/Mozilla\/([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version))&&(preg_match('/Chrome\/([0-9.]*)/', $HTTP_USER_AGENT, $log_version2))) {
		//} elseif(preg_match('/Chrome/', $HTTP_USER_AGENT, $log_version2)) {
			//$BROWSER_VER = $log_version[1] . '.' . $log_version2[1];
			$BROWSER_VER = $log_version2[1];
			$BROWSER_AGENT = 'GoogleChrome';
		} elseif((preg_match('/Mozilla\/([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version))&&(preg_match('/Safari\/([0-9]*)/', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version[1] . '.' . $log_version2[1];
			$BROWSER_AGENT = 'SAFARI';
		} elseif(preg_match('/Mozilla\/([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'MOZILLA';
		*/
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
			$BROWSER_AGENT = $HTTP_USER_AGENT;
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
			$BROWSER_AGENT = $HTTP_USER_AGENT;
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
			$BROWSER_AGENT = $HTTP_USER_AGENT;
		}
	}
	else {
		$BROWSER_VER = '';
		$BROWSER_AGENT = $HTTP_USER_AGENT;
	}
	return  $BROWSER_AGENT." - ".$BROWSER_VER;
	//return  $BROWSER_AGENT." - ".$BROWSER_VER." ($HTTP_USER_AGENT)";
}

// Retourne la version de Mysql

function affiche_date_naissance($date) {
    if (strlen($date) == 10) {
        // YYYY-MM-DD
        $annee = substr($date, 0, 4);
        $mois = substr($date, 5, 2);
        $jour = substr($date, 8, 2);
    }
    elseif (strlen($date) == 8 ) {
        // YYYYMMDD
        $annee = substr($date, 0, 4);
        $mois = substr($date, 4, 2);
        $jour = substr($date, 6, 2);
    }
    elseif (strlen($date) == 19 ) {
        // YYYY-MM-DD xx:xx:xx
        $annee = substr($date, 0, 4);
        $mois = substr($date, 5, 2);
        $jour = substr($date, 8, 2);
    }

    else {
        // Format inconnu
        return($date);
    }
    return $jour."/".$mois."/".$annee ;
}

function test_maj() {
    global $gepiVersion, $gepiRcVersion, $gepiBetaVersion;
    $version_old = getSettingValue("version");
    $versionRc_old = getSettingValue("versionRc");
    $versionBeta_old = getSettingValue("versionBeta");

   if ($version_old =='') {
       return true;
       die();
   }
   if ($gepiVersion > $version_old) {
        // On a une nouvelle version stable
       return true;
       die();
   }
   if (($gepiVersion == $version_old) and ($versionRc_old!='')) {
        // On avait une RC
       if (($gepiRcVersion > $versionRc_old) or ($gepiRcVersion=='')) {
            // Soit on a une nouvelle RC, soit on est passÈ de RC ‡ stable
           return true;
           die();
       }
   }
   if (($gepiVersion == $version_old) and ($versionBeta_old!='')) {
        // On avait une Beta
       if (($gepiBetaVersion > $versionBeta_old) or ($gepiBetaVersion=='')) {
            // Soit on a une nouvelle Beta, soit on est passÈ ‡ une RC ou une stable
           return true;
           die();
       }
   }
   return false;
}

function quelle_maj($num) {
    global $gepiVersion, $gepiRcVersion, $gepiBetaVersion;
    $version_old = getSettingValue("version");
    $versionRc_old = getSettingValue("versionRc");
    $versionBeta_old = getSettingValue("versionBeta");
    if ($version_old < $num) {
        return true;
        die();
    }
    if ($version_old == $num) {
        if ($gepiRcVersion > $versionRc_old) {
            return true;
            die();
        }
        if ($gepiRcVersion == $versionRc_old) {
            if ($gepiBetaVersion > $versionBeta_old) {
                return true;
                die();
            }
        }
    }
    return false;
}

function check_backup_directory() {

	global $multisite;

    $current_backup_dir = getSettingValue("backup_directory");
    if ($current_backup_dir == null) $current_backup_dir = "no_folder";
    if (!file_exists("./backup/".$current_backup_dir)) {
        $backupDirName = null;
        if ($multisite != 'y') {
        	// On regarde d'abord si le rÈpertoire de backup n'existerait pas dÈj‡...
        	$handle=opendir('./backup');

        	while ($file = readdir($handle)) {
            	if (strlen($file) > 34 and is_dir('./backup/'.$file)) $backupDirName = $file;
        	}

        	closedir($handle);
        }

        if ($backupDirName != null) {
            // Il existe : on met simplement ‡ jour le nom du rÈpertoire...
            $update = saveSetting("backup_directory",$backupDirName);
        } else {
            // Il n existe pas
            // On crÈÈ le rÈpertoire de backup
            $length = rand(35, 45);
            for($len=$length,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
            $dirname = $r;
            $create = mkdir("./backup/" . $dirname, 0700);
            copy("./backup/index.html","./backup/".$dirname."/index.html");
            if ($create) {
                saveSetting("backup_directory", $dirname);
                saveSetting("backupdir_lastchange",time());
            } else {
                return false;
                die();
            }

            // On dÈplace les Èventuels fichiers .sql dans ce nouveau rÈpertoire

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

    // On vÈrifie la date du dernier changement, et on change le nom
    // du rÈpertoire si le dernier changement a eu lieu il y a plus de 48h
    $lastchange = getSettingValue("backupdir_lastchange");
    $current_time = time();

    // Si le dernier changement a eu lieu il y a plus de 48h, on change le nom du rÈpertoire
    if ($current_time-$lastchange > 172800) {
        $dirname = getSettingValue("backup_directory");
        $length = rand(35, 45);
        for($len=$length,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2) ? mt_rand(48,57):(!mt_rand(0,1)?mt_rand(65,90):mt_rand(97,122))));
        $newdirname = $r;
        if (rename("./backup/".$dirname, "./backup/".$newdirname)) {
            saveSetting("backup_directory",$newdirname);
            saveSetting("backupdir_lastchange",time());
            return true;
        } else {
            echo "Erreur lors du renommage du dossier de sauvegarde.<br />";
            return false;
        }
    }
    return true;

}

/**
 * Fonction qui retourne le nombre de pÈriodes pour une classe
 *
 * @param integer $_id_classe identifiant numÈrique de la classe
 * @return integer Nombre de periodes dÈfinies pour cette classe
 */
function get_period_number($_id_classe) {
    $periode_query = mysql_query("SELECT count(*) FROM periodes WHERE id_classe = '" . $_id_classe . "'");
    $nb_periode = mysql_result($periode_query, 0);
    return $nb_periode;
}

/**
 * Renvoie le numÈro et le nom de la premiËre pÈriode active (O) pour une classe
 *
 * @param integer $_id_classe identifiant unique de la classe
 * @return array numÈro de la pÈriode 'num' et son nom 'nom'
 */
function get_periode_active($_id_classe){
  $periode_query  = mysql_query("SELECT num_periode, nom_periode FROM periodes WHERE id_classe = '" . $_id_classe . "' AND verouiller = 'N'");
  $reponse        = mysql_fetch_array($periode_query);

  return $retour = array('nom' => $reponse["num_periode"], 'nom' => $reponse["nom_periode"]);

}

// Pour les utilisateurs ayant des versions antÈrieures ‡ PHP 4.3.0 :
// la fonction html_entity_decode() est disponible a partir de la version 4.3.0 de php.
function html_entity_decode_all_version ($string)
{
   global $use_function_html_entity_decode;
   if (isset($use_function_html_entity_decode) and ($use_function_html_entity_decode == 0)) {
       // Remplace les entitÈs numÈriques
       $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
       $string = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $string);
       // Remplace les entitÈs litÈrales
       $trans_tbl = get_html_translation_table (HTML_ENTITIES);
       $trans_tbl = array_flip ($trans_tbl);
       return strtr ($string, $trans_tbl);
   } else
       return html_entity_decode($string);
}

function make_classes_select_html($link, $current, $year, $month, $day)

{
  // Pour le multisite, on doit rÈcupÈrer le RNE de l'Ètablissement
  $rne = isset($_GET['rne']) ? $_GET['rne'] : (isset($_POST['rne']) ? $_POST['rne'] : 'aucun');
  $aff_input_rne = $aff_get_rne = NULL;
  if ($rne != 'aucun') {
	$aff_input_rne = '<input type="hidden" name="rne" value="' . $rne . '" />' . "\n";
	$aff_get_rne = '&amp;rne=' . $rne;
  }
  $out_html = "<form name=\"classe\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\"><strong><em>Classe :</em></strong><br />
  " . $aff_input_rne . "
  <select name=\"classe\" onchange=\"classe_go()\">";
	// correction W3C : onChange = onchange
  $out_html .= "<option value=\"".$link."?year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;id_classe=-1\">(Choisissez une classe)";
  // Ligne suivante corrigÈe sur suggestion tout ‡ fait pertinente de StÈphane, mail du 1er septembre 06


  if (isset($_SESSION['statut']) && ($_SESSION['statut']=='scolarite'
		  && getSettingValue('GepiAccesCdtScolRestreint')=="yes")){
  $sql = "SELECT DISTINCT c.id, c.classe
	FROM classes c, j_groupes_classes jgc, ct_entry ct, j_scol_classes jsc
	WHERE (c.id = jgc.id_classe
	  AND jgc.id_groupe = ct.id_groupe
	  AND jsc.id_classe=jgc.id_classe
	  AND jsc.login='".$_SESSION ['login']."'
		)
	ORDER BY classe ;";

  } else if (isset($_SESSION['statut']) && ($_SESSION['statut']=='cpe'
		  && getSettingValue('GepiAccesCdtCpeRestreint')=="yes")){


	$sql = "SELECT DISTINCT c.id, c.classe
	  FROM classes c, j_groupes_classes jgc, ct_entry ct, j_eleves_cpe jec,j_eleves_classes jecl
	  WHERE (c.id = jgc.id_classe
	  AND jgc.id_groupe = ct.id_groupe
	  AND jec.cpe_login = '".$_SESSION ['login']."'
	  AND jec.e_login = jecl.login
	  AND jecl.id_classe = jgc.id_classe)
	  ORDER BY classe ;";
  }else{

 /* if(getSettingValue('GepiAccesCdtCpeRestreint')!="yes"
		  || getSettingValue('GepiAccesCdtScolRestreint')!="yes"){*/
	$sql = "SELECT DISTINCT c.id, c.classe
	  FROM classes c, j_groupes_classes jgc, ct_entry ct
	  WHERE (c.id = jgc.id_classe
	  AND jgc.id_groupe = ct.id_groupe)
	  ORDER BY classe";
  }

  //GepiAccesCdtCpeRestreint

  $res = sql_query($sql);


  if ($res) for ($i = 0; ($row = sql_row($res, $i)); $i++)
  {
    $selected = ($row[0] == $current) ? "selected" : "";
    $link2 = "$link?year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$row[0]" . $aff_get_rne;
    $out_html .= "<option $selected value=\"$link2\">" . htmlspecialchars($row[1]);
  }
  $out_html .= "</select>
  <script type=\"text/javascript\">
  <!--
  function classe_go()
  {
  box = document.forms[\"classe\"].classe;
  destination = box.options[box.selectedIndex].value;
  if (destination) location.href = destination;
  }
  // -->
  </script>
  <noscript>
  <input type=submit value=\"OK\" />
  </noscript>
  </form>";
  return $out_html;
}

function make_matiere_select_html($link, $id_ref, $current, $year, $month, $day, $special='')
{
	// $id_ref peut Ítre soit l'ID d'une classe, auquel cas on affiche tous les groupes
	// pour la classe, soit le login d'un ÈlËve, auquel cas on affiche tous les groupes
	// pour l'ÈlËve en question

	// correction W3C : onChange = onchange
	//						  Ajout de balises <p>...</p> pour pouvoir mettre en forme le texte
	//						  CrÈation d'un label pour passer les tests WAI
	//						  Ajout de balises <p>...</p> pour encadrer <select>...
	/*
	$out_html = "<form name=\"matiere\"  method=\"post\" action=\"".$_SERVER['PHP_SELF']."\"><strong><em>MatiËre :</em></strong><br />
	<select name=\"matiere\" onchange=\"matiere_go()\">\n";
	*/
	// Pour le multisite, on doit rÈcupÈrer le RNE de l'Ètablissement
	$prof="";
	
	$rne = isset($_GET['rne']) ? $_GET['rne'] : (isset($_POST['rne']) ? $_POST['rne'] : 'aucun');
	$aff_input_rne = $aff_get_rne = NULL;
	if ($rne != 'aucun') {
		$aff_input_rne = '<input type="hidden" name="rne" value="' . $rne . '" />' . "\n";
		$aff_get_rne = '&amp;rne=' . $rne;
	}
		$out_html = "<form id=\"matiere\"  method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n" . $aff_input_rne . "\n
	<h2 class='h2_label'> \n<label for=\"enseignement\"><strong><em>MatiËre :<br /></em></strong></label>\n</h2>\n<p>\n<select id=\"enseignement\" name=\"matiere\" onchange=\"matiere_go()\">\n ";
	
		// correction W3C : ajout de la balise de fin </option> ‡ la fin de $out_html
	if (is_numeric($id_ref)) {
		//$out_html .= "<option value=\"".$link."?&amp;year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;id_classe=$id_ref\">(Choisissez un enseignement)</option>";
		$out_html .= "<option value=\"".$link."?&amp;year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;id_classe=$id_ref\">(Choisissez un enseignement)</option>\n";
	
		if($special!='') {
			$selected="";
			if($special=='Toutes_matieres') {$selected=" selected='true'";}
			if (is_numeric($id_ref)) {
				$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$id_ref&amp;id_groupe=Toutes_matieres" . $aff_get_rne;
			} else {
				$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;login_eleve=$id_ref&amp;id_groupe=Toutes_matieres" . $aff_get_rne;
			}
			$out_html .= "<option $selected value=\"$link2\"$selected>Toutes les matiËres</option>\n";
		}
	
		$sql = "select DISTINCT g.id, g.name, g.description from j_groupes_classes jgc, groupes g, ct_entry ct where (" .
				"jgc.id_classe='".$id_ref."' and " .
				"g.id = jgc.id_groupe and " .
				"jgc.id_groupe = ct.id_groupe" .
				") order by g.name";
	} else {
		// correction W3C : ajout de la balise de fin </option> ‡ la fin de $out_html
		//$out_html .= "<option value=\"".$link."?&amp;year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;login_eleve=$id_ref\">(Choisissez un enseignement)</option>";
		$out_html .= "<option value=\"".$link."?&amp;year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;login_eleve=$id_ref\">(Choisissez un enseignement)</option>\n";

		if($special!='') {
			$selected="";
			if($special=='Toutes_matieres') {$selected=" selected='true'";}
			if (is_numeric($id_ref)) {
				$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$id_ref&amp;id_groupe=Toutes_matieres" . $aff_get_rne;
			} else {
				$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;login_eleve=$id_ref&amp;id_groupe=Toutes_matieres" . $aff_get_rne;
			}
			$out_html .= "<option $selected value=\"$link2\"$selected>Toutes les matiËres</option>\n";
		}

		$sql = "select DISTINCT g.id, g.name, g.description from j_eleves_groupes jec, groupes g, ct_entry ct where (" .
				"jec.login='".$id_ref."' and " .
				"g.id = jec.id_groupe and " .
				"jec.id_groupe = ct.id_groupe" .
				") order by g.name";
	}
	$res = sql_query($sql);
	if ($res) for ($i = 0; ($row = sql_row($res, $i)); $i++)
	{
		$test_prof = "SELECT nom, prenom FROM j_groupes_professeurs j, utilisateurs u WHERE (j.id_groupe='".$row[0]."' and u.login=j.login) ORDER BY nom, prenom";
		$res_prof = sql_query($test_prof);
		$chaine = "";
		for ($k=0;$prof=sql_row($res_prof,$k);$k++) {
			if ($k != 0) $chaine .= ", ";
			$chaine .= htmlspecialchars($prof[0])." ".substr(htmlspecialchars($prof[1]),0,1).".";
		}
		//$chaine .= ")";
		
		
		//$selected = ($row[0] == $current) ? "selected" : "";
		$selected = ($row[0] == $current) ? "selected=\"selected\"" : "";
		if (is_numeric($id_ref)) {
			$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$id_ref&amp;id_groupe=$row[0]" . $aff_get_rne;
		} else {
			$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;login_eleve=$id_ref&amp;id_groupe=$row[0]" . $aff_get_rne;
		}
		// correction W3C : ajout de la balise de fin </option> ‡ la fin de $out_html
		//$out_html .= "<option $selected value=\"$link2\">" . htmlspecialchars($row[2] . " - ")." ".$chaine."</option>";
		$out_html .= "<option $selected value=\"$link2\">" . htmlspecialchars($row[2] . " - ")." ".$chaine."</option>";
	}
	$out_html .= "\n</select>\n</p>\n
	
	<script type=\"text/javascript\">
	<!--
	function matiere_go()
	{
		box = document.forms[\"matiere\"].matiere;
		destination = box.options[box.selectedIndex].value;
		if (destination) location.href = destination;
	}
	// -->
	</script>
	
	<noscript><p>\n";
		if (is_numeric($id_ref)) {
			$out_html .= "<input type=\"hidden\" name=\"id_classe\" value=\"$id_ref\" />\n";
		} else {
			$out_html .= "<input type=\"hidden\" name=\"login_eleve\" value=\"$id_ref\" />\n";
		}
		$out_html .= "<input type=\"hidden\" name=\"year\" value=\"$year\" />
		<input type=\"hidden\" name=\"month\" value=\"$month\" />
		<input type=\"hidden\" name=\"day\" value=\"$day\" />
		<input type=\"submit\" value=\"OK\" />
		</p>
	</noscript>
	</form>\n";
	// correction W3C : ajout de \" pour encadrer submit ci-dessus et de <p>...</p> pour encadrer <input...>
	return $out_html;
}

function make_eleve_select_html($link, $login_resp, $current, $year, $month, $day)
{
	global $selected_eleve;
	// $current est le login de l'ÈlËve actuellement sÈlectionnÈ
	$sql="SELECT e.login, e.nom, e.prenom " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$login_resp."' AND (re.resp_legal='1' OR re.resp_legal='2'));";
	//echo "$sql<br />\n";
	$get_eleves = mysql_query($sql);

	if (mysql_num_rows($get_eleves) == 0) {
			// Aucun ÈlËve associÈ
		$out_html = "<p>Vous semblez n'Ítre responsable d'aucun ÈlËve ! Contactez l'administrateur pour corriger cette erreur.</p>";
	} elseif (mysql_num_rows($get_eleves) == 1) {
			// Un seul ÈlËve associÈ : pas de formulaire nÈcessaire
		$selected_eleve = mysql_fetch_object($get_eleves);
		$out_html = "<p class='bold'>ElËve : ".$selected_eleve->prenom." ".$selected_eleve->nom."</p>";
	} else {
		// Plusieurs ÈlËves : on affiche un formulaire pour choisir l'ÈlËve
	// correction W3C : onChange = onchange + ajout de balise <p> +fermeture balise <option>
	  $out_html = "<form id=\"eleve\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n<h2 class='h2_label'>\n<label for=\"choix_eleve\"><strong><em>ElËve :</em></strong></label>\n</h2>\n<p>\n<select id=\"choix_eleve\" name=\"eleve\" onchange=\"eleve_go()\">\n";
	  $out_html .= "<option value=\"".$link."?year=".$year."&amp;month=".$month."&amp;day=".$day."\">(Choisissez un ÈlËve)</option>\n";
		while ($current_eleve = mysql_fetch_object($get_eleves)) {
		   if ($current) {
		   	$selected = ($current_eleve->login == $current->login) ? "selected='selected'" : "";
		   } else {
		   	$selected = "";
		   }
		   $link2 = "$link?year=$year&amp;month=$month&amp;day=$day&amp;login_eleve=".$current_eleve->login;
		   $out_html .= "<option $selected value=\"$link2\">" . htmlspecialchars($current_eleve->prenom . " - ".$current_eleve->nom)."</option>\n";
		}
	// ajout de la fermeture de p </p> et de \" pour encadrer submit
	  $out_html .= "</select></p>
	  <script type=\"text/javascript\">
	  <!--
	  function eleve_go()
	  {
	    box = document.forms[\"eleve\"].eleve;
	    destination = box.options[box.selectedIndex].value;
	    if (destination) location.href = destination;
	  }
	  // -->
	  </script>

	  <noscript>
		<p>
		  <input type=\"hidden\" name=\"year\" value=\"$year\" />
		  <input type=\"hidden\" name=\"month\" value=\"$month\" />
		  <input type=\"hidden\" name=\"day\" value=\"$day\" />
		  <input type=\"submit\" value=\"OK\" />
		</p>
		</noscript>
	  </form>\n";
	}
	return $out_html;
}

function affiche_docs_joints($id_ct,$type_notice) {
// documents joints
$html = '';
$architecture="/documents/cl_dev";
if ($type_notice == "t") {
    $sql = "SELECT titre, emplacement FROM ct_devoirs_documents WHERE id_ct_devoir='$id_ct' ORDER BY 'titre'";
} else if ($type_notice == "c") {
    $sql = "SELECT titre, emplacement FROM ct_documents WHERE id_ct='$id_ct' ORDER BY 'titre'";
}

$res = sql_query($sql);
  if (($res) and (sql_count($res)!=0)) {
    $html .= "<span class='petit'>Document(s) joint(s):</span>";
    //$html .= "<ul type=\"disc\" style=\"padding-left: 15px;\">";
    $html .= "<ul style=\"padding-left: 15px;\">";
    for ($i=0; ($row = sql_row($res,$i)); $i++) {
              $titre = $row[0];
              $emplacement = $row[1];
              //$html .= "<li style=\"padding: 0px; margin: 0px; font-family: arial, sans-serif; font-size: 80%;\"><a href=\"$emplacement\" target=\"blank\">$titre</a></li>";
			// Ouverture dans une autre fenÍtre conservÈe parce que si le fichier est un PDF, un TXT, un HTML ou tout autre document susceptible de s'ouvrir dans le navigateur, on risque de refermer sa session en croyant juste refermer le document.
			// alternative, utiliser un javascript
              $html .= "<li style=\"padding: 0px; margin: 0px; font-family: arial, sans-serif; font-size: 80%;\"><a onclick=\"window.open(this.href, '_blank'); return false;\" href=\"$emplacement\">$titre</a></li>";

    }
    $html .= "</ul>";
   }
  return $html;
 }

/**
 * Cette fonction est ‡ appeler dans tous les cas o˘ une tentative
 * d'utilisation illÈgale de Gepi est manifestement avÈrÈe.
 * Elle est ‡ appeler notamment dans tous les tests de sÈcuritÈ lorsqu'un test est nÈgatif.
 * PossibilitÈ d'envoyer un mail ‡ l'administrateur et de bloquer l'utilisateur
 *
 * @global array $_SERVER obsolËte car $_SERVER est une superglobale
 * @global array $_SESSION obsolËte car $_SESSION est une super globale
 * @global string $gepiPath Path de Gepi (connect.inc.php)
 * @param integer $_niveau Niveau d'intrusion enregistrÈ
 * @param string $_description Message enregistrÈ pour cette tentative
 */
function tentative_intrusion($_niveau, $_description) {
	// On permet l'accËs ‡ $_SERVER et $_SESSION
	global $_SERVER;
	global $_SESSION;
	global $gepiPath;

	// On commence par enregistrer la tentative en question

	if (!isset($_SESSION['login'])) {
		// Ici, Áa veut dire que l'attaque est extÈrieure. Il n'y a pas d'utilisateur loguÈ.
		$user_login = "-";
	} else {
		$user_login = $_SESSION['login'];
	}
	$adresse_ip = $_SERVER['REMOTE_ADDR'];
	$date = strftime("%Y-%m-%d %H:%M:%S");
	$url = parse_url($_SERVER['REQUEST_URI']);
    $fichier = substr($url['path'], strlen($gepiPath));
	$res = mysql_query("INSERT INTO tentatives_intrusion SET " .
			"login = '".$user_login."', " .
			"adresse_ip = '".$adresse_ip."', " .
			"date = '".$date."', " .
			"niveau = '".(int)$_niveau."', " .
			"fichier = '".$fichier."', " .
			"description = '".addslashes($_description)."', " .
			"statut = 'new'");

	// On a enregistrÈ.

	// On initialise des marqueurs pour les deux actions possibles : envoie d'un email ‡ l'admin
	// et blocage du compte de l'utilisateur

	$send_email = false;
	$block_user = false;

	// Est-ce qu'on envoie un mail quoi qu'il arrive ?
	if (getSettingValue("security_alert_email_admin") == "yes" AND $_niveau >= getSettingValue("security_alert_email_min_level")) {
		$send_email = true;
	}

	// Si la tentative d'intrusion a ÈtÈ effectuÈe par un utilisateur connectÈ ‡ Gepi,
	// on regarde si des seuils ont ÈtÈ dÈpassÈs et si certaines actions doivent Ítre
	// effectuÈes.

	if ($user_login != "-") {
		// On rÈcupËre quelques infos
		$req = mysql_query("SELECT nom, prenom, statut, niveau_alerte, observation_securite FROM utilisateurs WHERE (login = '".$user_login."')");
		$user = mysql_fetch_object($req);
		// On va utiliser Áa pour gÈnÈrer automatiquement les noms de settings, Áa fait du code en moins...
		if ($user->observation_securite == "1") {
			$obs = "probation";
		} else {
			$obs = "normal";
		}

		// D'abord, on met ‡ jour le niveau cumulÈ
		$nouveau_cumul = (int)$user->niveau_alerte+(int)$_niveau;

		$res = mysql_query("UPDATE utilisateurs SET niveau_alerte = '".$nouveau_cumul ."' WHERE (login = '".$user_login."')");

		$seuil1 = false;
		$seuil2 = false;
		// Maintenant on regarde les seuils.
		if ($nouveau_cumul >= getSettingValue("security_alert1_".$obs."_cumulated_level")
				AND $nouveau_cumul < getSettingValue("security_alert2_".$obs."_cumulated_level")) {
			// Seuil 1
			if (getSettingValue("security_alert1_".$obs."_email_admin") == "yes") $send_email = true;
			if (getSettingValue("security_alert1_".$obs."_block_user") == "yes") $block_user = true;
			$seuil1 = true;

		} elseif ($nouveau_cumul >= getSettingValue("security_alert2_".$obs."_cumulated_level")) {
			// Seuil 2
			if (getSettingValue("security_alert2_".$obs."_email_admin") == "yes") $send_email = true;
			if (getSettingValue("security_alert2_".$obs."_block_user") == "yes") $block_user = true;
			$seuil2 = true;
		}

		// On dÈsactive le compte de l'utilisateur si nÈcessaire :
		if ($block_user) {
			$res = mysql_query("UPDATE utilisateurs SET etat = 'inactif' WHERE (login = '".$user_login."')");
		}
	} // Fin : if ($user_login != "-")

	// On envoie un email ‡ l'administrateur si nÈcessaire
	if ($send_email) {
		$message = "** Alerte automatique sÈcuritÈ Gepi **\n\n";
		$message .= "Une nouvelle tentative d'intrusion a ÈtÈ dÈtectÈe par Gepi. Les dÈtails suivants ont ÈtÈ enregistrÈs dans la base de donnÈes :\n\n";
		$message .= "Date : ".$date."\n";
		$message .= "Fichier visÈ : ".$fichier."\n";
		$message .= "Niveau de gravitÈ : ".$_niveau."\n";
		$message .= "Description : ".$_description."\n\n";
		if ($user_login == "-") {
			$message .= "La tentative d'intrusion a ÈtÈ effectuÈe par un utilisateur non connectÈ ‡ Gepi.\n";
			$message .= "Adresse IP : ".$adresse_ip."\n";
		} else {
			$message .= "Informations sur l'utilisateur :\n";
			$message .= "Login : ".$user_login."\n";
			$message .= "Nom : ".$user->prenom . " ".$user->nom."\n";
			$message .= "Statut : ".$user->statut."\n";
			$message .= "Score cumulÈ : ".$nouveau_cumul."\n\n";
			if ($seuil1) $message .= "L'utilisateur a dÈpassÈ le seuil d'alerte 1.\n\n";
			if ($seuil2) $message .= "L'utilisateur a dÈpassÈ le seuil d'alerte 2.\n\n";
			if ($block_user) $message .= "Le compte de l'utilisateur a ÈtÈ dÈsactivÈ.\n";
		}

		$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
		if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}

    $subject = $gepiPrefixeSujetMail."GEPI : Alerte sÈcuritÈ -- Tentative d'intrusion";
    $subject = "=?ISO-8859-1?B?".base64_encode($subject)."?=\r\n";

    
    $headers = "X-Mailer: PHP/" . phpversion()."\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
    $headers .= "From: Mail automatique Gepi <ne-pas-repondre@".$_SERVER['SERVER_NAME'].">\r\n";

		// On envoie le mail
		$envoi = mail(getSettingValue("gepiAdminAdress"),
		    $subject,
		    $message,
        $headers);
	}
}

/**
 * Fonction destinÈe ‡ prÈsenter une liste de liens rÈpartis en $nbcol colonnes
 *
 * @param array $tab_txt tableau des textes
 * @param array $tab_lien tableau des liens
 * @param integer $nbcol Nombre de colonnes
 */
function tab_liste($tab_txt,$tab_lien,$nbcol,$extra_options = null){

	// Nombre d'enregistrements ‡ afficher
	$nombreligne=count($tab_txt);

	if(!is_int($nbcol)){
		$nbcol=3;
	}

	// Nombre de lignes dans chaque colonne:
	$nb_class_par_colonne=round($nombreligne/$nbcol);

	echo "<table width='100%' summary=\"Tableau de choix\">\n";
	echo "<tr valign='top' align='center'>\n";
	echo "<td align='left'>\n";

	$i = 0;
	while ($i < $nombreligne){

		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		//echo "<br />\n";
		echo "<a href='".$tab_lien[$i]."'";
    if ($extra_options) echo ' '.$extra_options;
    echo ">".$tab_txt[$i]."</a>";
		echo "<br />\n";
		$i++;
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}

/**
 * Fonction destinÈe ‡ crÈer un dossier temporaire alÈatoire /temp/<alea>
 *
 * @return boolean true/false
 */
function check_temp_directory(){

	$dirname=getSettingValue("temp_directory");
	if(($dirname=='')||(!file_exists("./temp/$dirname"))){
		// Il n'existe pas
		// On crÈÈ le rÈpertoire temp
		$length = rand(35, 45);
		for($len=$length,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
		$dirname = $r;
		$create = mkdir("./temp/".$dirname, 0700);
		//chmod("/temp/".$dirname, 0700);

		if ($create) {
			$fich=fopen("./temp/".$dirname."/index.html","w+");
			fwrite($fich,'<html><head><script type="text/javascript">
    document.location.replace("../../login.php")
</script></head></html>
');
			fclose($fich);

			saveSetting("temp_directory", $dirname);
			//return $dirname;
			return true;
		} else {
			return false;
			die();
		}
	} else {
		return true;
	}
	/*
	else{
		return $dirname;
	}
	*/
}

/**
 * Fonction destinÈe ‡ crÈer un dossier /temp/<alea> propre au professeur
 *
 * @return boolean true/false
 */
function check_user_temp_directory(){

	$sql="SELECT temp_dir FROM utilisateurs WHERE login='".$_SESSION['login']."'";
	$res_temp_dir=mysql_query($sql);

	if(mysql_num_rows($res_temp_dir)==0){
		// Cela revient ‡ dire que l'utilisateur n'est pas dans la table utilisateurs???
		return false;
	}
	else{
		$lig_temp_dir=mysql_fetch_object($res_temp_dir);
		$dirname=$lig_temp_dir->temp_dir;

		if($dirname==""){
			// Le dossier n'existe pas
			// On crÈÈ le rÈpertoire temp
			$length = rand(35, 45);
			for($len=$length,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
			$dirname = $_SESSION['login']."_".$r;
			$create = mkdir("./temp/".$dirname, 0700);
			//chmod("/temp/".$dirname, 0700);

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
					//return $dirname;
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		else{
			if(!file_exists("./temp/".$dirname)){
				// Le dossier n'existe pas
				// On crÈÈ le rÈpertoire temp
				$create = mkdir("./temp/".$dirname, 0700);
				//chmod("/temp/".$dirname, 0700);

				if($create){
					$fich=fopen("./temp/".$dirname."/index.html","w+");
					fwrite($fich,'<html><head><script type="text/javascript">
	document.location.replace("../../login.php")
</script></head></html>
');
					fclose($fich);
					return true;
				}
				else{
					return false;
				}
			}
			else{
				$fich=fopen("./temp/".$dirname."/test_ecriture.tmp","w+");
				$ecriture=fwrite($fich,'Test d Ècriture.');
				$fermeture=fclose($fich);
				if(file_exists("./temp/".$dirname."/test_ecriture.tmp")){
					unlink("./temp/".$dirname."/test_ecriture.tmp");
				}

				if(($fich)&&($ecriture)&&($fermeture)){
					return true;
				}
				else{
					return false;
				}
			}
		}
	}
}

/**
 * Renvoie le nom du rÈpertoire temporaire de l'utilisateur
 *
 * @return string retourne false s'il n'existe pas et le nom du rÈpertoire s'il existe sans le chemin
 */
function get_user_temp_directory(){
	$sql="SELECT temp_dir FROM utilisateurs WHERE login='".$_SESSION['login']."'";
	$res_temp_dir=mysql_query($sql);
	if(mysql_num_rows($res_temp_dir)>0){
		$lig_temp_dir=mysql_fetch_object($res_temp_dir);
		$dirname=$lig_temp_dir->temp_dir;

		if(($dirname!="")&&(strlen(my_ereg_replace("[A-Za-z0-9_.]","",$dirname))==0)) {
			if(file_exists("temp/".$dirname)){
				return $dirname;
			}
			else if(file_exists("../temp/".$dirname)) {
				return $dirname;
			}
			else if(file_exists("../../temp/".$dirname)) {
				return $dirname;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}

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

function volume_dir_human($dir){
	global $totalsize;
	$totalsize=0;

	$volume=volume_dir($dir);
	return volume_human($volume);
}

function volume_dir($dir){
	global $totalsize;

	$handle = @opendir($dir);
	while ($file = @readdir ($handle)){
		if (my_eregi("^\.{1,2}$",$file))
			continue;
		//if(is_dir($dir.$file)){
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

function vider_dir($dir){
	$statut=true;
	$handle = @opendir($dir);
	while ($file = @readdir ($handle)){
		if (my_eregi("^\.{1,2}$",$file)){
			continue;
		}
		//if(is_dir($dir.$file)){
		if(is_dir("$dir/$file")){
			// On ne cherche pas ‡ vider rÈcursivement.
			$statut=false;

			echo "<!-- DOSSIER: $dir/$file -->\n";
			// En ajoutant un paramËtre ‡ la fonction, on pourrait activer la suppression rÈcursive (avec une profondeur par exemple) lancer ici vider_dir("$dir/$file");
		}
		else{
			if(!unlink($dir."/".$file)) {
				$statut=false;
				echo "<!-- Echec suppression: $dir/$file -->\n";
				break;
			}
		}
	}
	@closedir($handle);

	return $statut;
}


/*
 * Cette mÈthode prend une chaÓne de caractËres et s'assure qu'elle est bien
 * retournÈe en ISO-8859-1.
 */
function ensure_iso8859_1($str) {
	$encoding = mb_detect_encoding($str);
	if ($encoding == 'ISO-8859-1') {
		return $str;
	} else {
		return mb_convert_encoding($str, 'ISO-8859-1');
	}
}


function caract_ooo($chaine){
	if(function_exists('utf8_encode')){
		$retour=utf8_encode($chaine);
	}
	else{
		$caract_accent=array("¿","‡","¬","‚","ƒ","‰","…","È","»","Ë"," ","Í","À","Î","Œ","Ó","œ","Ô","‘","Ù","÷","ˆ","Ÿ","˘","€","˚","‹","¸");
		$caract_utf8=array("√Ä","√ ","√Ç","√¢","√Ñ","√§","√â","√©","√®","√ä","√™","√ã","√´","√é","√Æ","√è","√Ø","√î","√¥","√ñ","√∂","√ô","√π","√õ","√ª","√ú","√º","u");

		$retour=$chaine;
		for($i=0;$i<count($caract_accent);$i++){
			$retour=str_replace($caract_accent[$i],$caract_utf8[$i],$retour);
		}
	}

	$caract_special=array("&",
							'"',
							"'",
							"<",
							">");

	$caract_sp_encode=array("&amp;",
							"&quot;",
							"&apos;",
							"&lt;",
							"&gt;");

	for($i=0;$i<count($caract_special);$i++){
		$retour=str_replace($caract_special[$i],$caract_sp_encode[$i],$retour);
	}

	return $retour;
}

//================================================
// Correspondances de caractËres accentuÈs/dÈsaccentuÈs
$liste_caracteres_accentues   ="¬ƒ¿¡√≈« À»…ŒœÃÕ—‘÷“”’ÿ¶€‹Ÿ⁄›æ¥·‡‚‰„ÂÁÈËÍÎÓÔÏÌÒÙˆÚÛı¯®˚¸˘˙˝ˇ∏";
$liste_caracteres_desaccentues="AAAAAACEEEEIIIINOOOOOOSUUUUYYZaaaaaaceeeeiiiinooooooosuuuuyyz";
//================================================

function remplace_accents($chaine,$mode){
	global $liste_caracteres_accentues, $liste_caracteres_desaccentues;

	if($mode == 'all'){
		// On remplace espaces et apostrophes par des '_' et les caractËres accentuÈs par leurs Èquivalents non accentuÈs.
		$retour=strtr(preg_replace("/∆/","AE",preg_replace("/Ê/","ae",preg_replace("/º/","OE",preg_replace("/Ω/","oe","$chaine"))))," '$liste_caracteres_accentues","__$liste_caracteres_desaccentues");
	}
	elseif($mode == 'all_nospace'){
		// On remplace apostrophes par des '_' et les caractËres accentuÈs par leurs Èquivalents non accentuÈs.
		$retour1 = strtr(my_ereg_replace("∆","AE",my_ereg_replace("Ê","ae",ereg_replace("º","OE",my_ereg_replace("Ω","oe","$chaine")))),"'¬ƒ¿¡√ƒ≈« À»…ŒœÃÕ—‘÷“”’¶€‹Ÿ⁄›æ¥·‡‚‰„ÂÁÈËÍÎÓÔÏÌÒÙˆÚÛı®˚¸˘˙˝ˇ∏"," AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz");
		$retour1=strtr(preg_replace("/∆/","AE",preg_replace("/Ê/","ae",preg_replace("/º/","OE",preg_replace("/Ω/","oe","$chaine")))),"'$liste_caracteres_accentues"," $liste_caracteres_desaccentues");
		// On enlËve aussi les guillemets
		$retour = preg_replace('/"/', '', $retour1);
	}
	else{
		// On remplace les caractËres accentuÈs par leurs Èquivalents non accentuÈs.
		$retour=strtr(preg_replace("/∆/","AE",preg_replace("/Ê/","ae",preg_replace("/º/","OE",preg_replace("/Ω/","oe","$chaine")))),"$liste_caracteres_accentues","$liste_caracteres_desaccentues");
	}
	return $retour;
}

/**
 * Fonction qui renvoie le login d'un ÈlËve en Èchange de son ele_id
 *
 * @param integer $id_eleve ele_id de l'ÈlËve
 * @return string login de l'ÈlËve
 */
function get_login_eleve($id_eleve){

	$sql = "SELECT login FROM eleves WHERE id_eleve = '".$id_eleve."'";
	$query = mysql_query($sql) OR trigger_error('Impossible de rÈcupÈrer le login de cet ÈlËve.', E_USER_ERROR);
	if ($query) {
		$retour = mysql_result($query, 0,"login");
	}else{
		$retour = 'erreur';
	}
	return $retour;

}

/**
 * fonction qui renvoie le nom de la classe d'un ÈlËve pour chaque pÈriode
 *
 * @param string $ele_login login de l'ÈlËve
 * @return array Tableau des classes en fonction des pÈriodes
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
			$tab_classe['liste_nbsp'].=my_ereg_replace(" ","&nbsp;",$lig_tmp->classe);

			$tab_classe['id'.$a] = $lig_tmp->id_classe;
			$a = $a++;
		}
	}
	return $tab_classe;
}

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

function get_enfants_from_resp_login($resp_login,$mode='simple'){
	$sql="SELECT e.nom,e.prenom,e.login FROM eleves e,
											responsables2 r,
											resp_pers rp
										WHERE e.ele_id=r.ele_id AND
											rp.pers_id=r.pers_id AND
											rp.login='$resp_login' AND
											(r.resp_legal='1' OR r.resp_legal='2')
										ORDER BY e.nom,e.prenom;";
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

				$tab_ele[]=ucfirst(strtolower($lig_tmp->prenom))." ".strtoupper($lig_tmp->nom).$tmp_chaine_classes;
			}
			else {
				$tab_ele[]=ucfirst(strtolower($lig_tmp->prenom))." ".strtoupper($lig_tmp->nom);
			}
		}
	}
	return $tab_ele;
}

function liens_class_from_ele_login($ele_login){
	$chaine="";
	$tab_classe=get_class_from_ele_login($ele_login);
	if(isset($tab_classe)){
		if(count($tab_classe)>0){
			foreach ($tab_classe as $key => $value){
				if(strlen(my_ereg_replace("[0-9]","",$key))==0) {
					if($_SESSION['statut']=='administrateur') {
						$chaine.=", <a href='../classes/classes_const.php?id_classe=$key'>$value</a>";
					}
					else {
						$chaine.=", <a href='../eleves/index.php?id_classe=$key&amp;quelles_classes=certaines&amp;case_2=yes'>$value</a>";
					}
				}
			}
			$chaine="(".substr($chaine,2).")";
		}
	}
	return $chaine;
}

function statut_accentue($user_statut){
	switch($user_statut){
		case "administrateur":
			$chaine="administrateur";
			break;
		case "scolarite":
			$chaine="scolaritÈ";
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
			$chaine="ÈlËve";
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

function get_nom_classe($id_classe){
	$sql="SELECT classe FROM classes WHERE id='$id_classe';";
	$res_class=mysql_query($sql);

	if(mysql_num_rows($res_class)>0){
		$lig_tmp=mysql_fetch_object($res_class);
		$classe=$lig_tmp->classe;
		return $classe;
	}
	else{
		return false;
	}
}

function formate_date($date){
	$tmp_date=explode(" ",$date);
	$tab_date=explode("-",$tmp_date[0]);

	return sprintf("%02d",$tab_date[2])."/".sprintf("%02d",$tab_date[1])."/".$tab_date[0];
}

function traite_regime_sconet($code_regime){
	$premier_caractere_code_regime=substr($code_regime,0,1);
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

function creer_div_infobulle($id,$titre,$bg_titre,$texte,$bg_texte,$largeur,$hauteur,$drag,$bouton_close,$survol_close,$overflow,$zindex_infobulle=1){
	/*
		$id:			Identifiant du DIV conteneur
		$titre:			Texte du titre du DIV
		$bg_titre:		Couleur de fond de la barre de titre.
						Si $bg_titre est vide, on utilise la couleur par dÈfaut
						correspondant ‡ .infobulle_entete (dÈfini dans style.css
						et Èventuellement modifiÈ dans style_screen_ajout.css)
		$texte:			Texte du contenu du DIV
		$bg_texte:		Couleur de fond du DIV contenant le texte.
						Si $bg_texte est vide, on utilise la couleur par dÈfaut
						correspondant ‡ .infobulle_corps (dÈfini dans style.css
						et Èventuellement modifiÈ dans style_screen_ajout.css)
		$largeur:		Largeur du DIV conteneur
		$hauteur:		Hauteur (minimale) du DIV conteneur
						En mettant 0, on laisse le DIV s'adapter au contenu (se rÈduire/s'ajuster)
		$drag:			'y' ou 'n' pour rendre le DIV draggable
		$bouton_close:	'y' ou 'n' pour afficher le bouton Close
						S'il est affichÈ, c'est dans la barre de titre.
						Si la barre de titre n'est pas affichÈe, ce bouton ne peut pas Ítre affichÈ.
		$survol_close:	'y' ou 'n' pour refermer le DIV automatiquement lorsque le survol quitte le DIV
		$overflow:		'y' ou 'n' activer l'overflow automatique sur la partie Texte.
						Il faut que $hauteur soit non nulle
	*/
	global $posDiv_infobulle;
	global $tabid_infobulle;
	global $unite_div_infobulle;
	global $niveau_arbo;
	global $pas_de_decalage_infobulle;
	//global $style_special_infobulle;
	global $class_special_infobulle;

	//$style_box="color: #000000; border: 1px solid #000000; padding: 0px; position: absolute;";
	$style_box="color: #000000; border: 1px solid #000000; padding: 0px; position: absolute; z-index:$zindex_infobulle;";
	//if((isset($style_special_infobulle))&&($style_special_infobulle!='')) {$style_box.=$style_special_infobulle;}

	$style_bar="color: #ffffff; cursor: move; font-weight: bold; padding: 0px;";
	//$style_close="color: #ffffff; cursor: move; font-weight: bold; float:right; width: 1em;";
	$style_close="color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;";


	// On fait la liste des identifiants de DIV pour cacher les Div avec javascript en fin de chargement de la page (dans /lib/footer.inc.php).
	$tabid_infobulle[]=$id;

	// Conteneur:
	if($bg_texte==''){
		$div="<div id='$id' class='infobulle_corps";
		if((isset($class_special_infobulle))&&($class_special_infobulle!='')) {$div.=" ".$class_special_infobulle;}
		$div.="' style='$style_box width: ".$largeur.$unite_div_infobulle."; ";
	}
	else{
		$div="<div id='$id' ";
		if((isset($class_special_infobulle))&&($class_special_infobulle!='')) {$div.="class='".$class_special_infobulle."' ";}
		$div.="style='$style_box background-color: $bg_texte; width: ".$largeur.$unite_div_infobulle."; ";
	}
	if($hauteur!=0){
		$div.="height: ".$hauteur.$unite_div_infobulle."; ";
	}
	// Position horizontale initiale pour permettre un affichage sans superposition si Javascript est dÈsactivÈ:
	$div.="left:".$posDiv_infobulle.$unite_div_infobulle.";";
	$div.="'>\n";


	// Barre de titre:
	// Elle n'est affichÈe que si le titre est non vide
	if($titre!=""){
		if($bg_titre==''){
			$div.="<div class='infobulle_entete' style='$style_bar width: ".$largeur.$unite_div_infobulle.";'";
		}
		else{
			$div.="<div style='$style_bar background-color: $bg_titre; width: ".$largeur.$unite_div_infobulle.";'";
		}
		if($drag=="y"){
			// L‡ on utilise les fonctions de http://www.brainjar.com stockÈes dans brainjar_drag.js
			$div.=" onmousedown=\"dragStart(event, '$id')\"";
		}
		$div.=">\n";

		if($bouton_close=="y"){
			//$div.="<div style='$style_close'><a href='#' onClick=\"cacher_div('$id');return false;\">X</a></div>\n";
			$div.="<div style='$style_close'><a href='#' onclick=\"cacher_div('$id');return false;\">";
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
	//$div.="<div";
	//==================
	// 20110113
	$div.="<div id='".$id."_contenu_corps'";
	//==================
	if($survol_close=="y"){
		// On referme le DIV lorsque la souris quitte la zone de texte.
		$div.=" onmouseout=\"cacher_div('$id');\"";
	}
	$div.=">\n";
	if(($overflow=='y')&&($hauteur!=0)){
		$hauteur_hors_titre=$hauteur-1;
		$div.="<div style='width: ".$largeur.$unite_div_infobulle."; height: ".$hauteur_hors_titre.$unite_div_infobulle."; overflow: auto;'>\n";
		//$div.="<span style='padding-left: 1px;'>\n";
		$div.="<div style='padding-left: 1px;'>\n";
		$div.=$texte;
		//$div.="</span>\n";
		$div.="</div>\n";
		$div.="</div>\n";
	}
	else{
		//$div.="<span style='padding-left: 1px;'>\n";
		$div.="<div style='padding-left: 1px;'>\n";
		$div.=$texte;
		//$div.="</span>\n";
		$div.="</div>\n";
	}
	$div.="</div>\n";

	$div.="</div>\n";

	// Les div vont s'afficher cÙte ‡ cÙte sans superposition en bas de page si JavaScript est dÈsactivÈ:
	if (isset($pas_de_decalage_infobulle) AND $pas_de_decalage_infobulle == "oui") {
		// on ne dÈcale pas les div des infobulles
		$posDiv_infobulle = $posDiv_infobulle;
	}else{
		$posDiv_infobulle = $posDiv_infobulle+$largeur;
	}

	return $div;
}

$debug_var_count=array();
function debug_var() {
	global $debug_var_count;

	$debug_var_count['POST']=0;
	$debug_var_count['GET']=0;

	// Fonction destinÈe ‡ afficher les variables transmises d'une page ‡ l'autre: GET, POST et SESSION
	echo "<div style='border: 1px solid black; background-color: white; color: black;'>\n";

	$cpt_debug=0;

	echo "<p><strong>Variables transmises en POST, GET, SESSION,...</strong> (<a href='#' onclick=\"tab_etat_debug_var[$cpt_debug]=tab_etat_debug_var[$cpt_debug]*(-1);affiche_debug_var('container_debug_var_$cpt_debug',tab_etat_debug_var[$cpt_debug]);return false;\">*</a>)</p>\n";

	echo "<div id='container_debug_var_$cpt_debug'>\n";
	$cpt_debug++;

	echo "<p>Variables envoyÈes en POST: ";
	if(count($_POST)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#' onclick=\"tab_etat_debug_var[$cpt_debug]=tab_etat_debug_var[$cpt_debug]*(-1);affiche_debug_var('container_debug_var_$cpt_debug',tab_etat_debug_var[$cpt_debug]);return false;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug'>\n";
	$cpt_debug++;

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
	/*
	echo "<table summary=\"Tableau de debug\">\n";
	foreach($_POST as $post => $val){
		//echo "\$_POST['".$post."']=".$val."<br />\n";
		//echo "<tr><td>\$_POST['".$post."']=</td><td>".$val."</td></tr>\n";
		echo "<tr><td valign='top'>\$_POST['".$post."']=</td><td>".$val;

		if(is_array($_POST[$post])) {
			echo " (<a href='#' onclick=\"tab_etat_debug_var[$cpt_debug]=tab_etat_debug_var[$cpt_debug]*(-1);affiche_debug_var('container_debug_var_$cpt_debug',tab_etat_debug_var[$cpt_debug]);return false;\">*</a>)";
			echo "<table id='container_debug_var_$cpt_debug' summary=\"Tableau de debug\">\n";
			foreach($_POST[$post] as $key => $value) {
				echo "<tr><td>\$_POST['$post'][$key]=</td><td>$value</td></tr>\n";
			}
			echo "</table>\n";
			//echo "<script type='text/javascript'>affiche_debug_var('debug_var_$post',tab_etat_debug_var[$cpt_debug]);</script>\n";
			$cpt_debug++;
		}

		echo "</td></tr>\n";
	}
	echo "</table>\n";
	*/

	function tab_debug_var($chaine_tab_niv1,$tableau,$pref_chaine,$cpt_debug) {
		//global $cpt_debug;
		global $debug_var_count;

		echo " (<a href='#' onclick=\"tab_etat_debug_var[$cpt_debug]=tab_etat_debug_var[$cpt_debug]*(-1);affiche_debug_var('container_debug_var_$cpt_debug',tab_etat_debug_var[$cpt_debug]);return false;\">*</a>)\n";

		echo "<table id='container_debug_var_$cpt_debug' summary=\"Tableau de debug\">\n";
		foreach($tableau as $post => $val) {
			echo "<tr><td valign='top'>".$pref_chaine."['".$post."']=</td><td>".$val;

			if(is_array($tableau[$post])) {

				tab_debug_var($chaine_tab_niv1,$tableau[$post],$pref_chaine.'['.$post.']',$cpt_debug);

				$cpt_debug++;
			}
			elseif(isset($debug_var_count[$chaine_tab_niv1])) {
				$debug_var_count[$chaine_tab_niv1]++;
			}

			echo "</td></tr>\n";
		}
		echo "</table>\n";
	}


	echo "<table summary=\"Tableau de debug\">\n";
	foreach($_POST as $post => $val) {
		echo "<tr><td valign='top'>\$_POST['".$post."']=</td><td>".$val;

		if(is_array($_POST[$post])) {
			tab_debug_var('POST',$_POST[$post],'$_POST['.$post.']',$cpt_debug);

			$cpt_debug++;
		}
		else {
			$debug_var_count['POST']++;
		}

		echo "</td></tr>\n";
	}
	echo "</table>\n";

	echo "<p>Nombre de valeurs en POST: <b>".$debug_var_count['POST']."</b></p>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<p>Variables envoyÈes en GET: ";
	if(count($_GET)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#' onclick=\"tab_etat_debug_var[$cpt_debug]=tab_etat_debug_var[$cpt_debug]*(-1);affiche_debug_var('container_debug_var_$cpt_debug',tab_etat_debug_var[$cpt_debug]);return false;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug'>\n";
	$cpt_debug++;
	echo "<table summary=\"Tableau de debug sur GET\">";
	foreach($_GET as $get => $val){
		//echo "\$_GET['".$get."']=".$val."<br />\n";
		//echo "<tr><td>\$_GET['".$get."']=</td><td>".$val."</td></tr>\n";

		echo "<tr><td valign='top'>\$_GET['".$get."']=</td><td>".$val;

		if(is_array($_GET[$get])) {
			tab_debug_var('GET',$_GET[$get],'$_GET['.$get.']',$cpt_debug);

			$cpt_debug++;
		}
		else {
			$debug_var_count['GET']++;
		}

		echo "</td></tr>\n";
	}
	echo "</table>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<p>Variables envoyÈes en SESSION: ";
	if(count($_SESSION)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#' onclick=\"tab_etat_debug_var[$cpt_debug]=tab_etat_debug_var[$cpt_debug]*(-1);affiche_debug_var('container_debug_var_$cpt_debug',tab_etat_debug_var[$cpt_debug]);return false;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug'>\n";
	$cpt_debug++;
	echo "<table summary=\"Tableau de debug sur SESSION\">";
	foreach($_SESSION as $variable => $val){
		//echo "\$_SESSION['".$variable."']=".$val."<br />\n";
		echo "<tr><td>\$_SESSION['".$variable."']=</td><td>".$val."</td></tr>\n";
	}
	echo "</table>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<p>Variables envoyÈes en SERVER: ";
	if(count($_SERVER)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#' onclick=\"tab_etat_debug_var[$cpt_debug]=tab_etat_debug_var[$cpt_debug]*(-1);affiche_debug_var('container_debug_var_$cpt_debug',tab_etat_debug_var[$cpt_debug]);return false;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug'>\n";
	$cpt_debug++;
	echo "<table summary=\"Tableau de debug sur SERVER\">";
	foreach($_SERVER as $variable => $valeur){
		//echo "\$_SERVER['".$variable."']=".$valeur."<br />\n";
		echo "<tr><td>\$_SERVER['".$variable."']=</td><td>".$valeur."</td></tr>\n";
	}
	echo "</table>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<p>Variables envoyÈes en FILES: ";
	if(count($_FILES)==0) {
		echo "aucune";
	}
	else {
		echo "(<a href='#' onclick=\"tab_etat_debug_var[$cpt_debug]=tab_etat_debug_var[$cpt_debug]*(-1);affiche_debug_var('container_debug_var_$cpt_debug',tab_etat_debug_var[$cpt_debug]);return false;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug'>\n";
	$cpt_debug++;

	echo "<table summary=\"Tableau de debug\">\n";
	foreach($_FILES as $key => $val) {
		echo "<tr><td valign='top'>\$_FILES['".$key."']=</td><td>".$val;

		if(is_array($_FILES[$key])) {
			tab_debug_var('FILES',$_FILES[$key],'$_FILES['.$key.']',$cpt_debug);

			$cpt_debug++;
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
	for(i=0;i<$cpt_debug;i++) {
		if(document.getElementById('container_debug_var_'+i)) {
			affiche_debug_var('container_debug_var_'+i,-1);
		}
		// Variable destinÈe ‡ alterner affichage/masquage
		tab_etat_debug_var[i]=-1;
	}
</script>\n";

	echo "</div>\n";
	echo "</div>\n";
}

function param_edt($statut){
	// Fonction qui permet de vÈrifier si tel statut peut avoir accËs ‡ l'EdT en fonction des settings de l'admin
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
	// On vÈrifie $verif et on renvoie le return
	if ($verif == "y" or $verif == "yes") {
		return "yes";
	} else {
		return "no";
	}
}

/**
* Renvoie le nom de la photo de l'ÈlËve ou du prof
 *
* Renvoie NULL si :
 *
* - le module trombinoscope n'est pas activÈ
 *
* - ou bien la photo n'existe pas.
*
*@var $_elenoet_ou_login : selon les cas, soir l'elenoet de l'ÈlËve ou bien lelogin du professeur
*@var $repertoire : "eleves" ou "personnels"
*@var $arbo : niveau d'aborescence (1 ou 2).
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


	// En multisite, on ajoute le rÈpertoire RNE
	if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		  // On rÈcupËre le RNE de l'Ètablissement
	  $repertoire2=getSettingValue("gepiSchoolRne")."/";
	}else{
	  $repertoire2="";
	}


	// Cas des ÈlËves
	if ($repertoire == "eleves") {
	  /*
		// En multisite, le login est prÈfÈrable ‡ l'ELENOET
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			// On rÈcupËre l'INE de cet ÈlËve
			$sql = 'SELECT login FROM eleves WHERE elenoet = "'.$_elenoet_ou_login.'"';
			$query = mysql_query($sql);
			$_elenoet_ou_login = mysql_result($query, 0,'login');
		}

		$photo="";
		if($_elenoet_ou_login!='') {
			if(file_exists($chemin."../photos/eleves/$_elenoet_ou_login.jpg")) {
				$photo="$_elenoet_ou_login.jpg";
			}
			else {
				if(file_exists($chemin."../photos/eleves/".sprintf("%05d",$_elenoet_ou_login).".jpg")) {
					$photo=sprintf("%05d",$_elenoet_ou_login).".jpg";
				} else {
					for($i=0;$i<5;$i++){
						if(substr($_elenoet_ou_login,$i,1)=="0"){
							$test_photo=substr($_elenoet_ou_login,$i+1);
							//if(file_exists($chemin."../photos/eleves/".$test_photo.".jpg")){
							if(($test_photo!='')&&(file_exists($chemin."../photos/eleves/".$test_photo.".jpg"))) {
								$photo=$test_photo.".jpg";
								break;
							}
						}
					}
				}
			}
		}
	*/
	  if($_elenoet_ou_login!='') {

		// on vÈrifie si la photo existe

		if(file_exists($chemin."../photos/".$repertoire2."eleves/".$_elenoet_ou_login.".jpg")) {
			$photo=$chemin."../photos/".$repertoire2."eleves/".$_elenoet_ou_login.".jpg";
		}
		else if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y')
		{
		  // En multisite, on recherche aussi avec les logins
		  if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			// On rÈcupËre le login de l'ÈlËve
			$sql = 'SELECT login FROM eleves WHERE elenoet = "'.$_elenoet_ou_login.'"';
			$query = mysql_query($sql);
			$_elenoet_ou_login = mysql_result($query, 0,'login');
		  }

		  if(file_exists($chemin."../photos/".$repertoire2."eleves/$_elenoet_ou_login.jpg")) {
				$photo=$chemin."../photos/".$repertoire2."eleves/$_elenoet_ou_login.jpg";
			}
			else {
				if(file_exists($chemin."../photos/".$repertoire2."eleves/".sprintf("%05d",$_elenoet_ou_login).".jpg")) {
					$photo=$chemin."../photos/".$repertoire2."eleves/".sprintf("%05d",$_elenoet_ou_login).".jpg";
				} else {
					for($i=0;$i<5;$i++){
						if(substr($_elenoet_ou_login,$i,1)=="0"){
							$test_photo=substr($_elenoet_ou_login,$i+1);
							//if(file_exists($chemin."../photos/eleves/".$test_photo.".jpg")){
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
	}
	// Cas des non-ÈlËves
	else {

		$_elenoet_ou_login = md5(strtolower($_elenoet_ou_login));
			//if(file_exists($chemin."../photos/personnels/$_elenoet_ou_login.jpg")){
			if(file_exists($chemin."../photos/".$repertoire2."personnels/$_elenoet_ou_login.jpg")){
				//$photo="$_elenoet_ou_login.jpg";
				$photo=$chemin."../photos/".$repertoire2."personnels/$_elenoet_ou_login.jpg";
			} else {
				$photo = NULL;
		}
	}
	return $photo;
}




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

function redimensionne_image2($photo){
	global $photo_largeur_max, $photo_hauteur_max;

	// prendre les informations sur l'image
	$info_image=getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur=$info_image[0];
	$hauteur=$info_image[1];

	// calcule le ratio de redimensionnement
	$ratio_l=$largeur/$photo_largeur_max;
	$ratio_h=$hauteur/$photo_hauteur_max;
	$ratio=($ratio_l>$ratio_h)?$ratio_l:$ratio_h;

	// dÈfinit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur=round($largeur/$ratio);
	$nouvelle_hauteur=round($hauteur/$ratio);

	return array($nouvelle_largeur, $nouvelle_hauteur);
}

function calc_moy_debug($texte){
	// Passer ‡ 1 la variable pour gÈnÈrer un fichier de debug...
	$debug=0;
	if($debug==1){
		$tmp_dir=get_user_temp_directory();
		if((!$tmp_dir)||(!file_exists("../temp/".$tmp_dir))) {$tmp_dir="/tmp";} else {$tmp_dir="../temp/".$tmp_dir;}
		$fich=fopen($tmp_dir."/calc_moy_debug.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}

function get_class_from_id($id_classe) {
	$sql="SELECT classe FROM classes c WHERE id='$id_classe';";
	$res_class=mysql_query($sql);

	if(mysql_num_rows($res_class)>0){
		$lig_tmp=mysql_fetch_object($res_class);
		$classe=$lig_tmp->classe;
		return $classe;
	}
	else{
		return false;
	}
}
/* Outils complÈmentaires de gestion des AID
fonction vÈrifiant les droits d'accËs au module selon l'identifiant

$_login :  identifiant de la personne pour laquelle on vÈrifie les droits
           si le login n'est pas prÈcisÈ, on est dans l'interface publique

$aid_id : identifiant de l'AID
$indice_aid : identifiant de la catÈgorie d'AID

$champ : si non vide, on vÈrifie le droit sur ce champ en particulier
         si $champ='', on vÈrifie le droit de modifier la fiche projet

Cas particulier : $champ = 'eleves_profs'
Cette valeur permet de gÈrer le fait que n'apparaissent pas sur les fiches publiques :
    # Les elËves responsables du projet,
    # les professeurs responsables du projet,
    # les ÈlËves faisant partie du projet.

$mode : utilisÈ uniquement si $champ est non vide
* $mode = W -> l'utilisateur a-t-il accËs en Ècriture ?
* Autres valeurs de W -> l'utilisateur a-t-il accËs en lecture ?

*/
function VerifAccesFicheProjet($_login,$aid_id,$indice_aid,$champ,$mode,$annee='') {
 //$annee='' signifie qu'il s'agit de l'annÈe courante
 if ($annee=='') {
    // Les outils complÈmetaires sont-ils activÈs ?
    $test_active = sql_query1("select indice_aid from aid_config WHERE outils_complementaires = 'y' and indice_aid='".$indice_aid."'");
    // Les outils complÈmenatires ne sont activÈs pour aucune AID, on renvoie FALSE
    if ($test_active == -1) {
        return false;
        die();
    }

    // Si le champ n'est pas activÈ, on ne l'affiche pas !
    // Deux valeurs possibles :
    // 0 -> le champ n'est pas utilisÈ
    // 1 -> Le champ est utilisÈ
    if ($champ != "") {
        $statut_champ = sql_query1("select statut from droits_aid where id = '".$champ."'");
        if ($statut_champ == 0) {
            return FALSE;
            die();
        }
    }


    // Dans la suite,
    // Les outils complÈmentaires sont activÈs

    if ($_login!='') {
        $statut_login = sql_query1("select statut from utilisateurs where login='".$_login."' and etat='actif' ");
    } else {
        // si le login n'est pas prÈcisÈ, on est dans l'interface publique
        $statut_login = "public";
    }
    // Admin ?
    if  ($statut_login == "administrateur") {
        return TRUE;
        die();
    }


    // S'agit-il d'un super gestionnaire ?
    $test_super_gestionnaire = sql_query1("select count(id_utilisateur) from j_aidcateg_super_gestionnaires where indice_aid='".$indice_aid."' and id_utilisateur='".$_login."'");
    if  ($test_super_gestionnaire != "0") {
        return TRUE;
        die();
    }


    // S'agit-il d'un utilisateurs ayant des droits sur l'ensemble des AID de la catÈgorie
    $test_droits_special = sql_query1("select count(id_utilisateur) from j_aidcateg_utilisateurs where indice_aid='".$indice_aid."' and id_utilisateur='".$_login."'");
    // Cas d'un ÈlËve
    if (($statut_login=="eleve")) {
        // s'il s'agit d'un ÈlËve, les ÈlËves ont-ils accËs en modification ?
        // Si l'utilisateur a des droits spÈciaux, il peut modifier
        $CheckAccessEleve = sql_query1("select eleve_peut_modifier from aid where id = '".$aid_id."' and indice_aid = '".$indice_aid."'");
        if ($CheckAccessEleve != "y") {
            if ($champ == "") {return false; die();}
        }
        // L'ÈlËve est-il responsable de cet AID ?
        $CheckAccessEleve2 = sql_query1("select count(login) from j_aid_eleves_resp WHERE (login='".$_SESSION['login']."' and indice_aid='".$indice_aid."' and id_aid='".$aid_id."')");
        if ($CheckAccessEleve2 == 0) {
             if ($champ == "") {return false; die();}
        }
    }
    // Cas d'un professeur
    if (($statut_login=="professeur")) {

        // s'il s'agit d'un prof, les profs ont-ils accËs en modification ?
        $CheckAccessProf = sql_query1("select prof_peut_modifier from aid where id = '".$aid_id."' and indice_aid = '".$indice_aid."'");
        if (($CheckAccessProf != "y") and ($test_droits_special==0) ) {
            if ($champ == "") {return false; die();}
        }

        // Le profeseur est-il responsable de cet AID ?
        $CheckAccessProf2 = sql_query1("select count(id_utilisateur) from j_aid_utilisateurs WHERE (id_utilisateur='".$_SESSION['login']."' and indice_aid='".$indice_aid."' and id_aid='".$aid_id."')");
        if (($CheckAccessProf2 == 0) and ($test_droits_special==0) ) {
            if ($champ == "") {return false; die();}
        }
    }
    // Cas d'un CPE
    if (($statut_login=="cpe")) {
        // s'il s'agit d'un CPE, les cpe ont-ils accËs en modification ?
        // Si l'utilisateur a des droits spÈciaux, il peut modifier
        $CheckAccessCPE = sql_query1("select cpe_peut_modifier from aid where id = '".$aid_id."' and indice_aid = '".$indice_aid."'");
        if (($CheckAccessCPE != "y") and ($test_droits_special==0)) {
            if ($champ == "") {return false; die();}
        }
    }
    // S'il s'agit d'un responsable, de la scolaritÈ ou de secours, pas d'accËs
    if (($statut_login=="responsable") or ($statut_login=="scolarite") or ($statut_login=="secours")) {
        return false;
        die();
    }
    // Si le champ n'est pas prÈcisÈ, c'est terminÈ
    // Si le champ est prÈcisÈ, on regarde si l'utilisateur a les droits de modif de ce champ
    //
    if ($champ == "") {
        // Si $champ == "", cela signifie qu'on demande l'accËs ‡ une page privÈe de modif ou de visualisation
        if ($_login !="")
            return TRUE;
        else
            return FALSE;
    } else {
        // Le champ est prÈcisÈ. On cherche ‡ savoir si l'utilisateur a le droit de voir et/ou de modifier ce champ
        $CheckAccess = sql_query1("select ".$statut_login." from droits_aid where id = '".$champ."'");
        // $CheckAccess='V' -> possibilitÈ de modifier et de voir le champ
        // $CheckAccess='F' -> possibilitÈ de voir le champ mais pas de le modifier
        // $CheckAccess='-' -> Interdiction de voir et ou de modifier le champ
        if (($mode != 'W') and ($CheckAccess != '-'))
            return (true);
        else if (($mode == 'W') and ($CheckAccess == 'V'))
            return (true);
        else
            return (false);

    }
  } else {
  // il s'agit de projets d'une annÈe passÈe...
    // Les outils complÈmetaires sont-ils activÈs ?
    $test_active = sql_query1("select id from archivage_types_aid WHERE outils_complementaires = 'y' and id='".$indice_aid."'");
    // Les outils complÈmenatires ne sont activÈs pour aucune AID, on renvoie FALSE
    if ($test_active == -1) {
        return false;
        die();
    }

    if ($_login!='') {
        $statut_login = sql_query1("select statut from utilisateurs where login='".$_login."' and etat='actif' ");
    } else {
        // si le login n'est pas prÈcisÈ, on est dans l'interface publique
        $statut_login = "public";
    }


    if ($champ == 'eleves_profs') {
    # Cas particulier du champ eleves_profs : ce champ permet de gÈrer le fait que n'apparaissent pas sur les fiches publiques :
    # Les elËves responsables du projet,
    # les professeurs responsables du projet,
    # les ÈlËves faisant partie du projet.
        if ($statut_login == "public")
            return FALSE;
        else
            return TRUE;
    } else if ($champ != "") {
    // Si le champ n'est pas activÈ, on ne l'affiche pas !
    // Deux valeurs possibles :
    // 0 -> le champ n'est pas utilisÈ
    // 1 -> Le champ est utilisÈ

        $statut_champ = sql_query1("select statut from droits_aid where id = '".$champ."'");
        if ($statut_champ == 0) {
            return FALSE;
            die();
        }
    }
    // Admin ?
    if  ($statut_login == "administrateur") {
        return TRUE;
        die();
    }
    if ($champ == "") {
    // Si $champ == "", cela signifie qu'on demande l'accËs ‡ une page privÈe de modif ou de visualisation
       return FALSE;
    // Si le champ est prÈcisÈ, on regarde si l'utilisateur a les droits de modif de ce champ
    } else {
        // Le champ est prÈcisÈ. On cherche ‡ savoir si l'utilisateur a le droit de voir et/ou de modifier ce champ
        $CheckAccess = sql_query1("select ".$statut_login." from droits_aid where id = '".$champ."'");
        // $CheckAccess='V' -> possibilitÈ de modifier et de voir le champ
        // $CheckAccess='F' -> possibilitÈ de voir le champ mais pas de le modifier
        // $CheckAccess='-' -> Interdiction de voir et ou de modifier le champ
        if (($mode != 'W') and ($CheckAccess != '-'))
            return (true);
        else if (($mode == 'W') and ($CheckAccess == 'V'))
            return (true);
        else
            return (false);
    }

  }
}
/* Outils complÈmentaires de gestion des AID
fonction vÈrifiant si les outils complÈmetaires sont-ils activÈs
*/
function VerifAidIsAcive($indice_aid,$aid_id,$annee='') {
    if ($annee=='')
      $test_active = sql_query1("select indice_aid from aid_config WHERE outils_complementaires = 'y' and indice_aid='".$indice_aid."'");
    else
      $test_active = sql_query1("select id from archivage_types_aid WHERE outils_complementaires = 'y' and id='".$indice_aid."'");
    if ($test_active == -1)
       return FALSE;
    else {
       if ($aid_id != "") {
         if ($annee=='')
           $test_aid_existe = sql_query1("select count(id) from aid WHERE indice_aid='".$indice_aid."' and id='".$aid_id."'");
        else
           $test_aid_existe = sql_query1("select count(id) from archivage_aids WHERE id_type_aid='".$indice_aid."' and id='".$aid_id."'");
        if ($test_aid_existe != 1)
           return FALSE;
        else
           return TRUE;
       } else
           return TRUE;

    }
}
/* Outils complÈmentaires de gestion des AID
fonction qui renvoie le libellÈ du champ
*/
function LibelleChampAid($champ) {
    $nom = sql_query1("select description from droits_aid where id = '".$champ."'");
    return $nom;
}

/* Gestion des AIDs
fonction qui calcul le niveau de gestion des AIDs
0 : aucun droit
1 : peut uniquement ajouter / supprimer des ÈlËves
2 : (pas encore implÈmenter) peut uniquement ajouter / supprimer des ÈlËves et des professeurs responsables
3 : ...
10 : Peut tout faire
*/
function NiveauGestionAid($_login,$_indice_aid,$_id_aid="") {
    if ($_SESSION['statut'] == "administrateur") {
        return 10;
        die();
    }
    if (getSettingValue("active_mod_gest_aid")=="y") {
      // l'id de l'aid n'est pas dÈfini : on regarde si l'utilisateur est gestionnaire d'au moins une aid dans la catÈgorie
      if ($_id_aid == "") {
        $test1 = sql_query1("SELECT count(id_utilisateur) FROM j_aid_utilisateurs_gest WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_indice_aid."')");
        $test2 = sql_query1("SELECT count(id_utilisateur) FROM j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_indice_aid."')");
        if ($test2 >= 1) {
            return 5;
        } else if ($test1 >= 1) {
            return 1;
        } else
          return 0;
      } else {
      // l'id de l'aid est dÈfini : on regarde si l'utilisateur est gestionnaire de cette aid
        $test1 = sql_query1("SELECT count(id_utilisateur) FROM j_aid_utilisateurs_gest WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_indice_aid."' and id_aid = '".$_id_aid."')");
        $test2 = sql_query1("SELECT count(id_utilisateur) FROM j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_indice_aid."')");
        if ($test2 >= 1) {
            return 5;
        } else if ($test1 >= 1) {
            return 1;
        } else
          return 0;
      }
    } else
      return 0;
}

/* Gestion des droits d'accËs ‡ confirm_query.php
*/
function PeutEffectuerActionSuppression($_login,$_action,$_cible1,$_cible2,$_cible3) {
    if ($_SESSION['statut'] == "administrateur") {
        return TRUE;
        die();
    }
    if (getSettingValue("active_mod_gest_aid")=="y") {
      if (($_action=="del_eleve_aid") or ($_action=="del_prof_aid") or ($_action=="del_aid")) {
      // on regarde si l'utilisateur est gestionnaire de l'aid
        $test1 = sql_query1("SELECT count(id_utilisateur) FROM j_aid_utilisateurs_gest WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_cible3."' and id_aid = '".$_cible2."')");
        $test2 = sql_query1("SELECT count(id_utilisateur) FROM j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_cible3."')");
        $test = max($test1,$test2);
        if ($test >= 1) {
            return TRUE;
        } else {
            return FALSE;
        }
      }
    } else
    return FALSE;
}

/*
function fdebug_mail_connexion($texte){
	// Passer la variable ‡ "y" pour activer le remplissage du fichier de debug pour calcule_moyenne()
	$local_debug="n";
	if($local_debug=="y") {
		$fich=fopen("/tmp/mail_connexion.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}
*/

function mail_connexion() {
	global $active_hostbyaddr;

	$test_envoi_mail=getSettingValue("envoi_mail_connexion");

	//$date = strftime("%Y-%m-%d %H:%M:%S");
	//$date = ucfirst(strftime("%A %d-%m-%Y ‡ %H:%M:%S"));
	//fdebug_mail_connexion("\$_SESSION['login']=".$_SESSION['login']."\n\$test_envoi_mail=$test_envoi_mail\n\$date=$date\n====================\n");

	if($test_envoi_mail=="y") {
		$user_login = $_SESSION['login'];

		$sql="SELECT nom,prenom,email FROM utilisateurs WHERE login='$user_login';";
		$res_user=mysql_query($sql);
		if (mysql_num_rows($res_user)>0) {
			$lig_user=mysql_fetch_object($res_user);

			$adresse_ip = $_SERVER['REMOTE_ADDR'];
			//$date = strftime("%Y-%m-%d %H:%M:%S");
			$date = ucfirst(strftime("%A %d-%m-%Y ‡ %H:%M:%S"));
			//$url = parse_url($_SERVER['REQUEST_URI']);

			if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
				$result_hostbyaddr = " - ".@gethostbyaddr($adresse_ip);
			}
			else if($active_hostbyaddr == "no_local") {
				if ((substr($adresse_ip,0,3) == 127) or (substr($adresse_ip,0,3) == 10.) or (substr($adresse_ip,0,7) == 192.168)) {
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
			$message .= "Vous (*) vous Ítes connectÈ ‡ GEPI :\n\n";
			$message .= "IdentitÈ                : ".strtoupper($lig_user->nom)." ".ucfirst(strtolower($lig_user->prenom))."\n";
			$message .= "Login                   : ".$user_login."\n";
			$message .= "Date                    : ".$date."\n";
			$message .= "Origine de la connexion : ".$adresse_ip."\n";
			if($result_hostbyaddr!="") {
				$message .= "Adresse IP rÈsolue en   : ".$result_hostbyaddr."\n";
			}
			$message .= "\n";
			$message .= "Ce message, s'il vous parvient alors que vous ne vous Ítes pas connectÈ ‡ la date/heure indiquÈe, est susceptible d'indiquer que votre identitÈ a pu Ítre usurpÈe.\nVous devriez contrÙler vos donnÈes, changer votre mot de passe et avertir l'administrateur (et/ou l'administration de l'Ètablissement) pour qu'il puisse prendre les mesures appropriÈes.\n";
			$message .= "\n";
			$message .= "(*) Vous ou une personne tentant d'usurper votre identitÈ.\n";

			// On envoie le mail
			/*
			// getSettingValue("gepiAdminAdress")
			$envoi = mail($lig_user->email,
				"GEPI : Connexion $date",
				$message,
			"From: Mail automatique Gepi\r\n"."X-Mailer: PHP/" . phpversion());
			//fdebug_mail_connexion("\$message=$message\n====================\n");
			*/
			$destinataire=$lig_user->email;
			$sujet="GEPI : Connexion $date";
			envoi_mail($sujet, $message, $destinataire);
		}
	}
}



function mail_alerte($sujet,$texte,$informer_admin='n') {
	global $active_hostbyaddr;

	$user_login = $_SESSION['login'];

	$sql="SELECT nom,prenom,email FROM utilisateurs WHERE login='$user_login';";
	$res_user=mysql_query($sql);
	if (mysql_num_rows($res_user)>0) {
		$lig_user=mysql_fetch_object($res_user);

		$adresse_ip = $_SERVER['REMOTE_ADDR'];
		//$date = strftime("%Y-%m-%d %H:%M:%S");
		$date = ucfirst(strftime("%A %d-%m-%Y ‡ %H:%M:%S"));
		//$url = parse_url($_SERVER['REQUEST_URI']);

		if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
			$result_hostbyaddr = " - ".@gethostbyaddr($adresse_ip);
		}
		else if($active_hostbyaddr == "no_local") {
			if ((substr($adresse_ip,0,3) == 127) or (substr($adresse_ip,0,3) == 10.) or (substr($adresse_ip,0,7) == 192.168)) {
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
		$message .= "Vous (*) vous Ítes connectÈ ‡ GEPI :\n\n";
		$message .= "IdentitÈ                : ".strtoupper($lig_user->nom)." ".ucfirst(strtolower($lig_user->prenom))."\n";
		$message .= "Login                   : ".$user_login."\n";
		$message .= "Date                    : ".$date."\n";
		$message .= "Origine de la connexion : ".$adresse_ip."\n";
		if($result_hostbyaddr!="") {
			$message .= "Adresse IP rÈsolue en   : ".$result_hostbyaddr."\n";
		}
		$message .= "\n";
		$message .= "Ce message, s'il vous parvient alors que vous ne vous Ítes pas connectÈ ‡ la date/heure indiquÈe, est susceptible d'indiquer que votre identitÈ a pu Ítre usurpÈe.\nVous devriez contrÙler vos donnÈes, changer votre mot de passe et avertir l'administrateur (et/ou l'administration de l'Ètablissement) pour qu'il puisse prendre les mesures appropriÈes.\n";
		$message .= "\n";
		$message .= "(*) Vous ou une personne tentant d'usurper votre identitÈ.\n";

		$ajout="";
		if(($informer_admin!='n')&&(getSettingValue("gepiAdminAdress")!='')) {
			$ajout="Bcc: ".getSettingValue("gepiAdminAdress")."\r\n";
		}

		// On envoie le mail
		/*
		// getSettingValue("gepiAdminAdress")
		$envoi = mail($lig_user->email,
			"GEPI : $sujet $date",
			$message,
		"From: Mail automatique Gepi\r\n".$ajout."X-Mailer: PHP/" . phpversion());
		//fdebug_mail_connexion("\$message=$message\n====================\n");
		*/

		$destinataire=$lig_user->email;
		$sujet="GEPI : $sujet $date";
		envoi_mail($sujet, $message, $destinataire, $ajout);

	}
}



function texte_html_ou_pas($texte){
	// Si le texte contient des < et >, on affiche tel quel
	if((strstr($texte,">"))||(strstr($texte,"<"))){
		$retour=$texte;
	}
	// Sinon, on transforme les retours ‡ la ligne en <br />
	else{
		$retour=nl2br($texte);
	}
	return $retour;
}

function decompte_debug($motif,$texte) {
	global $tab_instant, $debug;
	if($debug=="y") {
		$instant=microtime();
		if(isset($tab_instant[$motif])) {
			$tmp_tab1=explode(" ",$instant);
			$tmp_tab2=explode(" ",$tab_instant[$motif]);
			if($tmp_tab1[1]!=$tmp_tab2[1]) {
				$diff=$tmp_tab1[1]-$tmp_tab2[1];
			}
			else {
				$diff=$tmp_tab1[0]-$tmp_tab2[0];
			}
			//if($debug=="y") {
				echo "<p style='color:green;'>$texte: ".$diff." s</p>\n";
			//}
		}
		else {
			//if($debug=="y") {
				echo "<p style='color:green;'>$texte</p>\n";
			//}
		}
		$tab_instant[$motif]=$instant;
	}
}


// Fonction qui retourne l'URI des ÈlËves pour les flux rss
function retourneUri($eleve, $https, $type){

	global $gepiPath;
	$rep = array();

	// on vÈrifie que la table e nquestion existe dÈj‡
	$test_table = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'rss_users'"));
	if ($test_table >= 1) {

		$sql = "SELECT user_uri FROM rss_users WHERE user_login = '".$eleve."' LIMIT 1";
		$query = mysql_query($sql);
		$nbre = mysql_num_rows($query);
		if ($nbre == 1) {
			$uri = mysql_fetch_array($query);
			if ($https == 'y') {
				$web = 'https://';
			}else{
				$web = 'http://';
			}
			if ($type == 'cdt') {
				$rep["uri"] = $web.$_SERVER["SERVER_NAME"].$gepiPath.'/class_php/syndication.php?rne='.getSettingValue("gepiSchoolRne").'&amp;ele_l='.$_SESSION["login"].'&amp;type=cdt&amp;uri='.$uri["user_uri"];
				$rep["text"] = $web.$_SERVER["SERVER_NAME"].$gepiPath.'/class_php/syndication.php?rne='.getSettingValue("gepiSchoolRne").'&amp;ele_l='.$_SESSION["login"].'&amp;type=cdt&amp;uri='.$uri["user_uri"];
			}

		}else{
			$rep["text"] = 'erreur1';
			$rep["uri"] = '#';
		}
	}else{

		$rep["text"] = 'Demandez ‡ votre administrateur de gÈnÈrer les URI.';
		$rep["uri"] = '#';

	}

	return $rep;
}

function get_date_php() {
	$eng_words = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$french_words = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche', 'Janvier', 'FÈvrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Ao˚t', 'Septembre', 'Octobre', 'Novembre', 'DÈcembre');
	$date_str = date('l').' '.date('d').' '.date('F').' '.date('Y');
	$date_str = str_replace($eng_words, $french_words, $date_str);
	return $date_str;
}

function casse_prenom($prenom) {
	$tab=explode("-",$prenom);

	$retour="";
	for($i=0;$i<count($tab);$i++) {
		if($i>0) {
			$retour.="-";
		}
		$tab[$i]=ucwords(strtolower($tab[$i]));
		$retour.=$tab[$i];
	}

	return $retour;
}

function traite_accents_utf8($chaine) {
	global $mode_utf8_pdf;
	if($mode_utf8_pdf=="y") {
		return utf8_encode($chaine);
	}
	else {
		return $chaine;
	}
}

function nf($nombre,$nb_chiffre_apres_virgule=1) {
	// Formatage des nombres
	// Precision:
	// Pour Ítre s˚r d'avoir un entier
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
		//$valeur=strtr($valeur,".",",");
	}
	return $valeur;
}


function cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align='C',$align='L',$increment=0.3,$r_interligne=0.3) {
	global $pdf;

	// $increment:     nombre dont on rÈduit la police ‡ chaque essai
	// $r_interligne:  proportion de la taille de police pour les interlignes
	// $bordure:       LRBT
	// $v_align:       C(enter) ou T(op)

	// En cas de pb avec cell_ajustee1(), effectuer:
	// INSERT INTO setting SET name='cell_ajustee_old_way', value='y';
	// ou
	// UPDATE setting SET value='y' WHERE name='cell_ajustee_old_way';
	if(getSettingValue('cell_ajustee_old_way')=='y') {
		// On vire les balises en utilisant l'ancienne fonction qui ne gÈrait pas les balises
		$texte=preg_replace('/<(.*)>/U','',$texte);
		cell_ajustee0($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align,$increment,$r_interligne);
	}
	else {
		cell_ajustee1($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align,$increment,$r_interligne);
	}
}

function my_echo_debug($texte) {
	global $mode_my_echo_debug;
	global $my_echo_debug;
	global $niveau_arbo;

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

function cell_ajustee1($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align='C',$align='L',$increment=0.3,$r_interligne=0.3) {
	global $pdf;
	// Pour que la variable puisse Ítre rÈcupÈrÈe dans la fonction my_echo_debug(), il faut la dÈclarer comme globale:
	global $my_echo_debug, $mode_my_echo_debug;

	// $increment:     nombre dont on rÈduit la police ‡ chaque essai
	// $r_interligne:  proportion de la taille de police pour les interlignes
	// $bordure:       LRBT
	// $v_align:       C(enter) ou T(op)

	$texte=trim($texte);
	$hauteur_texte=$hauteur_max_font;
	//$pdf->SetFontSize($hauteur_texte);

	//================================
	// Options de debug
	// Passer ‡ 1 pour dÈbugger
	$my_echo_debug=0;
	//$my_echo_debug=1;

	// Les modes sont 'fichier' ou n'importe quoi d'autre qui provoque des echo... donc un Èchec de la gÈnÈration de PDF... ‡ ouvrir avec un bloc-notes, pas avec un lecteur PDF
	// Voir la fonction my_echo_debug() pour l'emplacement du fichier gÈnÈrÈ
	$mode_my_echo_debug='fichier';
	//$mode_my_echo_debug='';
	//================================

	if($my_echo_debug==1) my_echo_debug("\n\n=========================================================\n");
	if($my_echo_debug==1) my_echo_debug("Lancement de\nmy_cell_ajustee(\$texte=$texte,\n\$x=$x,\n\$y=$y,\n\$largeur_dispo=$largeur_dispo,\n\$h_cell=$h_cell,\n\$hauteur_max_font=$hauteur_max_font,\n\$hauteur_min_font=$hauteur_min_font,\n\$bordure=$bordure,\n\$v_align=$v_align,\n\$align=$align,\n\$increment=$increment,\n\$r_interligne=$r_interligne)\n\n");

	if($my_echo_debug==1) my_echo_debug("\$texte=\"$texte\"\n");

	// Pour rÈduire la taille du texte, il se peut qu'il faille supprimer les balises,...
	$supprimer_balises="n";
	$supprimer_retours_a_la_ligne="n";
	$tronquer="n";

	// On commence par essayer de remplir la cellule avec la taille de police proposÈe
	// Et rÈduire la taille de police si cela ne tient pas.
	// Si on arrive ‡ une taille de police trop faible, on va supprimer des retours ‡ la ligne, des balises ou mÍme tronquer.

	// Pour forcer en debug:
	//$tronquer='y';

	//while(true) {
	while($tronquer!='y') {
		// On (re)dÈmarre un essai avec une taille de police

		$pdf->SetFontSize($hauteur_texte);

		// Nombre max de lignes avec la hauteur courante de police
		// Il manque l'interligne de bas de cellule
		//$nb_max_lig=max(1,floor($h_cell/((1+$r_interligne)*($hauteur_texte*26/100))));
		//$nb_max_lig=max(1,floor($h_cell/((1+2*$r_interligne)*($hauteur_texte*26/100))));
		$nb_max_lig=max(1,floor(($h_cell-$r_interligne*($hauteur_texte*26/100))/((1+$r_interligne)*($hauteur_texte*26/100))));
		//$nb_max_lig=max(1,floor(($h_cell-$r_interligne*($hauteur_texte*26/100))/((1+$r_interligne)*($hauteur_texte*26/100)))-1);

		if($my_echo_debug==1) my_echo_debug("\nOn lance un tour avec la taille de police:\n\$hauteur_texte=$hauteur_texte\n");
		if($my_echo_debug==1) my_echo_debug("\$nb_max_lig=$nb_max_lig\n");

		// Lignes dans la cellule
		unset($ligne);
		$ligne=array();

		// Compteur des lignes
		$cpt=0;

		// On prÈvoit deux... trois espaces de marge en gras pour s'assurer que la ligne ne dÈbordera pas
		$pdf->SetFont('','B');
		$un_espace_gras=$pdf->GetStringWidth(' ');
		if($my_echo_debug==1) my_echo_debug("Un espace en gras mesure $un_espace_gras\n");
		$marge_espaces=3*$un_espace_gras;
		if($my_echo_debug==1) my_echo_debug("On compte trois espaces de marge, soit $marge_espaces\n");
		$largeur_utile=$largeur_dispo-$marge_espaces;
		if($my_echo_debug==1) my_echo_debug("D'o˘ \$largeur_utile=$largeur_utile\n");

		// CONTROLER QUE \$largeur_utile>0
		if($largeur_utile<=0) {
			// On se laisse une chance que cela tienne en tronquant
			$tronquer="y";
			break;
		}

		$style_courant='';
		$pdf->SetFont('',$style_courant);

		// (RÈ-)initialisation du tÈmoin
		$temoin_reduire_police="n";

		if($supprimer_retours_a_la_ligne=="y") {
			//$texte=str_replace('\n'," ",$texte);
			$texte=trim(my_ereg_replace("\n"," ",$texte));
		}

		$chaine_longueur_ligne_courante="0";

		$tab=preg_split('/<(.*)>/U',$texte,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($tab as $i=>$valeur) {
			//$pdf->Cell(150,7, "\$tab[$i]=$valeur",0,2,'');
			// Avec $i pair on a le texte et les indices impairs correspondent aux balises (b et /b,...)

			// On initialise la ligne courante si nÈcessaire pour le cas o˘ on aurait $texte="<b>Blabla..."
			// Il faut que la ligne soit initialisÈe pour pouvoir ajouter le <b> dans $i%2!=0
			if(!isset($ligne[$cpt])) {
			//if(!isset($test_ligne)) {
				$ligne[$cpt]='';
				$longueur_ligne_courante=0;
				$chaine_longueur_ligne_courante="0";
				//$test_ligne="";
			}

			if($i%2==0) {
				if($my_echo_debug==1) my_echo_debug("\nParcours avec l'ÈlÈment \$i=$i: \"$tab[$i]\"\n");


				//$tab2=preg_split("/[\s]+/",$tab[$i]); // Ca compterait les espaces, tabulations, \n et \r
				$tab2=split(" ",$tab[$i]);
				// Si on gËre aussi les virgules et tirets, il y a une difficultÈ supplÈmentaire ‡ gÈrer pour re-concatÈner (normalement aprËs une virgule, on doit avoir un espace)... donc on ne gËre que les espaces

				if($my_echo_debug==1) my_echo_debug("_____________________________________________\n");
				for($j=0;$j<count($tab2);$j++) {
					if($my_echo_debug==1) my_echo_debug("Mot \$tab2[$j]=\"$tab2[$j]\"\n");
				}
				if($my_echo_debug==1) my_echo_debug("_____________________________________________\n");

				for($j=0;$j<count($tab2);$j++) {
					if($my_echo_debug==1) my_echo_debug("Mot \$tab2[$j]=\"$tab2[$j]\"\n");

					// Si un des mots dÈpasse $largeur_dispo, il faut rÈduire la police (et si avec la police minimale, Áa dÈpasse $largeur_dispo, il faudra couper n'importe o˘...)
					if($pdf->GetStringWidth($tab2[$j])>$largeur_utile) {
						$temoin_reduire_police="y";
						break;
					}

					if($j>0) {
						// Il ne faut ajouter un espace que si on a augmentÈ $j... (on n'est plus au premier mot de la ligne ~ voire... pb avec les dÈcoupes suivant les balises HTML)
						$largeur_espace=$pdf->GetStringWidth(' ');
						$longueur_ligne_courante+=$largeur_espace;
						$chaine_longueur_ligne_courante.="+".$largeur_espace;

						if($my_echo_debug==1) my_echo_debug("\$longueur_ligne_courante=$longueur_ligne_courante et \$largeur_utile=$largeur_utile\n");
						if($my_echo_debug==1) my_echo_debug("\$chaine_longueur_ligne_courante=$chaine_longueur_ligne_courante\n");

						if($longueur_ligne_courante>$largeur_utile) {
							// En ajoutant un espace, on dÈpasse la largeur_dispo
							$cpt++;
							if($cpt+1>$nb_max_lig) {
								// On dÈpasse le nombre max de lignes avec la taille de police courante
								$temoin_reduire_police="y";
								// On quitte la boucle sur les \n (boucle sur $tab3)
								break;
							}

							$ligne[$cpt]='';
							$longueur_ligne_courante=0;
							$chaine_longueur_ligne_courante="0";
						}
						else {
							$ligne[$cpt].=' ';
							if($my_echo_debug==1) my_echo_debug("On a ajoutÈ un espace dans la longueur qui prÈcËde.\n");
							if($my_echo_debug==1) my_echo_debug("Longueur calculÈe sans gÈrer les balises ".$pdf->GetStringWidth($ligne[$cpt])."\n");
						}
					}

					// Il n'y a pas d'espace dans $tab2[$j]
					// Si on scinde avec des \n, on aura un mot par indice de $tab3
					unset($tab3);
					$tab3=array();

					if($my_echo_debug==1) my_echo_debug("\$supprimer_retours_a_la_ligne=$supprimer_retours_a_la_ligne\n");
					// Prendre en compte ‡ ce niveau les \n
					if($supprimer_retours_a_la_ligne=="n") {
						if($my_echo_debug==1) my_echo_debug("On dÈcoupe si nÈcessaire les retours ‡ la ligne\n");
						//$tab3=split("\n",$tab2[$j]);
						$tab3=split("\n",$tab2[$j]);
						for($loop=0;$loop<count($tab3);$loop++) {if($my_echo_debug==1) my_echo_debug("   \$tab3[$loop]=\"$tab3[$loop]\"\n");}
					}
					else {
						//$tab3[0]=str_replace("\n"," ",$tab2[$j]);
						$tab3[0]=$tab2[$j];
					}

					// Si supprimer_retours_a_la_ligne=='y', on ne fait qu'un tour dans la boucle
					for($k=0;$k<count($tab3);$k++) {
						if($k>0) {
							// On change de ligne

							if($my_echo_debug==1) my_echo_debug("\$ligne[$cpt]=\"$ligne[$cpt]\"\n");
							if($my_echo_debug==1) my_echo_debug("\$longueur_ligne_courante=$longueur_ligne_courante\n");
							if($my_echo_debug==1) my_echo_debug("\$chaine_longueur_ligne_courante=$chaine_longueur_ligne_courante\n");

							$cpt++;
							if($cpt+1>$nb_max_lig) {
								// On dÈpasse le nombre max de lignes avec la taille de police courante
								$temoin_reduire_police="y";
								// On quitte la boucle sur les \n (boucle sur $tab3)
								break;
							}
							$ligne[$cpt]='';
							$longueur_ligne_courante=0;
							$chaine_longueur_ligne_courante="0";
							//$test_ligne="";
						}
						//$test_ligne.=$tab3[$k];
						//$longueur_ligne_courante+=$pdf->GetStringWidth($tab3[$k]);
						$test_longueur_ligne_courante=$longueur_ligne_courante+$pdf->GetStringWidth($tab3[$k]);
						if($my_echo_debug==1) my_echo_debug("La longueur du mot \$tab3[$k]=\"$tab3[$k]\" est ".$pdf->GetStringWidth($tab3[$k])."\n");

						//if($pdf->GetStringWidth($test_ligne)>$largeur_dispo) {
						//if($longueur_ligne_courante>$largeur_dispo) {
						if($test_longueur_ligne_courante>$largeur_utile) {
							$cpt++;
							if($cpt+1>$nb_max_lig) {
								// On dÈpasse le nombre max de lignes avec la taille de police courante
								$temoin_reduire_police="y";
								// On quitte la boucle sur les \n (boucle sur $tab3)
								break;
							}
							$ligne[$cpt]=$tab3[$k];
							$longueur_mot=$pdf->GetStringWidth($tab3[$k]);
							$longueur_ligne_courante=$longueur_mot;
							$chaine_longueur_ligne_courante=$longueur_mot;
						}
						else {
							// Ca tient encore sur la ligne courante
							$ligne[$cpt].=$tab3[$k];
							//$ligne[$cpt]=$test_ligne;
							$longueur_mot=$pdf->GetStringWidth($tab3[$k]);
							$longueur_ligne_courante+=$longueur_mot;
							$chaine_longueur_ligne_courante.="+".$longueur_mot;
						}
						if($my_echo_debug==1) my_echo_debug("\$ligne[$cpt]=\"$ligne[$cpt]\"\n");
						if($my_echo_debug==1) my_echo_debug("\$longueur_ligne_courante=$longueur_ligne_courante\n");
						if($my_echo_debug==1) my_echo_debug("\$chaine_longueur_ligne_courante=$chaine_longueur_ligne_courante\n");
					}

					if($temoin_reduire_police=="y") {
						// On quitte la boucle sur les mots (boucle sur $tab2)
						break;
					}
				}
			}
			elseif($supprimer_balises=="n") {
				// On tient compte des balises
				if($valeur{0}=='/') {
					// On referme une balise
					if(strtoupper($valeur)=='/B') {
						$style_courant=preg_replace("/B/i","",$style_courant);
						$pdf->SetFont('',$style_courant);
						$ligne[$cpt].="</B>";
					}
					elseif(strtoupper($valeur)=='/I') {
						$style_courant=preg_replace("/I/i","",$style_courant);
						$pdf->SetFont('',$style_courant);
						$ligne[$cpt].="</I>";
					}
					elseif(strtoupper($valeur)=='/U') {
						$style_courant=preg_replace("/U/i","",$style_courant);
						$pdf->SetFont('',$style_courant);
						$ligne[$cpt].="</U>";
					}
				}
				else {
					// On ouvre une balise
					if(strtoupper($valeur)=='B') {
						$style_courant=$style_courant.'B';
						$pdf->SetFont('',$style_courant);
						$ligne[$cpt].="<B>";
					}
					elseif(strtoupper($valeur)=='I') {
						$style_courant=$style_courant.'I';
						$pdf->SetFont('',$style_courant);
						$ligne[$cpt].="<I>";
					}
					elseif(strtoupper($valeur)=='U') {
						$style_courant=$style_courant.'U';
						$pdf->SetFont('',$style_courant);
						$ligne[$cpt].="<U>";
					}
				}
				if($my_echo_debug==1) my_echo_debug("\$ligne[$cpt]=\"$ligne[$cpt]\"\n");
				if($my_echo_debug==1) my_echo_debug("\$longueur_ligne_courante=$longueur_ligne_courante\n");
				if($my_echo_debug==1) my_echo_debug("\$style_courant=$style_courant\n");
			}

			if($temoin_reduire_police=="y") {
				$hauteur_texte-=$increment;
				//if($hauteur_texte<=0) {
				if(($hauteur_texte<=0)||($hauteur_texte<$hauteur_min_font)) {
					// ProblËme... il va falloir:
					// - ne pas prendre en compte les \n
					// - ne pas prendre en compte les balises
					// - tronquer

					if($supprimer_retours_a_la_ligne=='n') {
						// On va virer les \n en les remplaÁant par des espaces
						$supprimer_retours_a_la_ligne='y';
						if($my_echo_debug==1) my_echo_debug("+++ On va supprimer les retours ‡ la ligne.\n");
					}
					elseif($supprimer_balises=='n') {
						// On va un cran plus loin... en virant les balises... on ne gagnera que sur les mots en gras qui sont plus larges
						$supprimer_balises='y';
						if($my_echo_debug==1) my_echo_debug("+++ On va supprimer les balises.\n");
					}
					else {
						// Il va falloir tronquer... pas cool!

						// A FAIRE
						$tronquer="y";

						if($my_echo_debug==1) my_echo_debug("+++ On va tronquer.\n");
					}

					// RÈinitialiser la taille de police:
					$hauteur_texte=$hauteur_max_font;
				}
				else {
					if($my_echo_debug==1) my_echo_debug("+++++++++++++++\n");
					if($my_echo_debug==1) my_echo_debug("\nOn rÈduit la taille de police:\n");
					if($my_echo_debug==1) my_echo_debug("\$hauteur_texte=".$hauteur_texte."\n");
				}

				// On quitte la boucle sur le tableau des dÈcoupages de balises HTML (boucle sur $tab)
				break;
			}
		}

		if($my_echo_debug==1) my_echo_debug("\$temoin_reduire_police=$temoin_reduire_police\n");

		if($temoin_reduire_police!="y") {
			// On a fini par trouver une taille  de police convenable

			if($my_echo_debug==1) my_echo_debug("\nOn a trouvÈ la bonne la taille de police:\n");

			// On quitte la boucle pour procÈder ‡ l'affichage du contenu de $ligne plus bas
			break;
		}
	}

	if($tronquer=='y') {
		// A FAIRE: On va remplir en coupant n'importe o˘ dans les mots sans chercher ‡ conserver des mots entiers
		//          Faut-il faire la boucle sur la taille de police?
		//          Ou prendre directement la taille minimale?

		if($my_echo_debug==1) my_echo_debug("---------------------------------\n");
		if($my_echo_debug==1) my_echo_debug("--- On va remplir en tronquant...\n");

		$hauteur_texte=$hauteur_min_font;

		$pdf->SetFontSize($hauteur_texte);

		// Nombre max de lignes avec la hauteur courante de police
		$nb_max_lig=max(1,floor(($h_cell-$r_interligne*($hauteur_texte*26/100))/((1+$r_interligne)*($hauteur_texte*26/100))));

		if($my_echo_debug==1) my_echo_debug("\$hauteur_texte=$hauteur_texte\n");
		if($my_echo_debug==1) my_echo_debug("\$nb_max_lig=$nb_max_lig\n");

		// Lignes dans la cellule
		unset($ligne);
		$ligne=array();

		// Compteur des lignes
		$cpt=0;

		$longueur_max_atteinte="n";

		// On prÈvoit deux... trois espaces de marge en gras pour s'assurer que la ligne ne dÈbordera pas
		$pdf->SetFont('','B');
		$marge_espaces=3*$pdf->GetStringWidth(' ');
		$largeur_utile=$largeur_dispo-$marge_espaces;

		// CONTROLER QUE \$largeur_utile>0
		if($largeur_utile>0) {
			$style_courant='';
			$pdf->SetFont('',$style_courant);

			// On va supprimer les retours ‡ la ligne
			$texte=trim(my_ereg_replace("\n"," ",$texte));
			if($my_echo_debug==1) my_echo_debug("\$texte=$texte\n");

			// On supprime les balises
			$texte=preg_replace('/<(.*)>/U','',$texte);
			if($my_echo_debug==1) my_echo_debug("\$texte=$texte\n");
			for($j=0;$j<strlen($texte);$j++) {

				if(!isset($ligne[$cpt])) {
					$ligne[$cpt]='';
				}
				if($my_echo_debug==1) my_echo_debug("\$ligne[$cpt]=\"$ligne[$cpt]\"\n");

				$chaine=$ligne[$cpt].substr($texte,$j,1);
				if($my_echo_debug==1) my_echo_debug("\$chaine=\"$chaine\"\n");

				if($pdf->GetStringWidth($chaine)>$largeur_utile) {

					if($my_echo_debug==1) my_echo_debug("Avec \$chaine, Áa dÈpasse.\n");

					if($cpt+1>$nb_max_lig) {
						$longueur_max_atteinte="y";

						if($my_echo_debug==1) my_echo_debug("\$cpt=$cpt et \$nb_max_lig=$nb_max_lig.\nOn ne peut plus ajouter une ligne.\n");

						break;
					}

					$cpt++;
					$ligne[$cpt]=substr($texte,$j,1);
					if($my_echo_debug==1) my_echo_debug("On commence une nouvelle ligne avec le dernier caractËre: \"".substr($texte,$j-1,1)."\"\n");
					if($my_echo_debug==1) my_echo_debug("\$ligne[$cpt]=\"$ligne[$cpt]\"\n");
				}
				else {
					$ligne[$cpt].=substr($texte,$j,1);
					if($my_echo_debug==1) my_echo_debug("\$ligne[$cpt]=\"$ligne[$cpt]\"\n");
				}
			}

			if($my_echo_debug==1) my_echo_debug("On a fini le texte... ou atteint une limite\n");

		}
	}


	// On va afficher le texte

	// Hauteur de la police en mm
	$hauteur_texte_mm=$hauteur_texte*26/100;
	// Hauteur de la police en pt
	$taille_police=$hauteur_texte;
	// Hauteur totale du texte
	$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
	// Marge verticale en mm entre les lignes
	$marge_verticale=$hauteur_texte_mm*$r_interligne;


	if($my_echo_debug==1) my_echo_debug("\$hauteur_texte=".$hauteur_texte."\n");
	if($my_echo_debug==1) my_echo_debug("\$hauteur_texte_mm=".$hauteur_texte_mm."\n");
	if($my_echo_debug==1) my_echo_debug("\$hauteur_totale=".$hauteur_totale."\n");
	if($my_echo_debug==1) my_echo_debug("\$marge_verticale=".$marge_verticale."\n");


	// On trace le rectangle (vide) du cadre:
	$pdf->SetXY($x,$y);
	$pdf->Cell($largeur_dispo,$h_cell, '',$bordure,2,'');

	// On va Ècrire les lignes avec la taille de police optimale dÈterminÈe (cf. $ifmax)
	$nb_lig=count($ligne);
	$h=$nb_lig*$hauteur_texte_mm*(1+$r_interligne);
	$t=$h_cell-$h;
	if($my_echo_debug==1) my_echo_debug("\$t=".$t."\n");
	$bord_debug='';
	//$bord_debug='LRBT';

	// On ne gËre que les v_align Top et Center... et ajout d'un mode aÈrÈ
	if($v_align=='E') {
		// Mode aÈrÈ
		//$espace_v=($h_cell-2*$marge_verticale-$nb_lig*$hauteur_texte_mm)/max(1,$nb_lig-1);
		$espace_v=($h_cell-4*$marge_verticale-$nb_lig*$hauteur_texte_mm)/max(1,$nb_lig-1);
	}
	elseif($v_align!='T') {
		// Par dÈfaut c'est Center
		//$decalage_v_top=($h_cell-$nb_lig*$hauteur_texte_mm-($nb_lig-1)*$marge_verticale)/2;
		$decalage_v_top=($h_cell-($nb_lig+1)*$hauteur_texte_mm-$nb_lig*$marge_verticale)/2;
	}

	for($i=0;$i<count($ligne);$i++) {

		if($v_align=='T') {
			$pdf->SetXY($x,$y+$i*($hauteur_texte_mm+$marge_verticale));

			// Pour pouvoir afficher le $bord_debug
			$pdf->Cell($largeur_dispo,$hauteur_texte_mm+2*$marge_verticale, '',$bord_debug,1,$align);

			$y_courant=$y+$i*($hauteur_texte_mm+$marge_verticale)-$marge_verticale;
			$pdf->SetXY($x,$y_courant);
			if($my_echo_debug==1) {
				$pdf->myWriteHTML($ligne[$i]." ".$i." ".round($y_courant));
			}
			else {
				$pdf->myWriteHTML($ligne[$i]);
			}
		}
		elseif($v_align=='E') {
			$y_courant=$y+$marge_verticale+$i*($hauteur_texte_mm+$espace_v);
			$pdf->SetXY($x,$y_courant);

			// Pour pouvoir afficher le $bord_debug
			$pdf->Cell($largeur_dispo,$h_cell/$nb_lig, '',$bord_debug,1,$align);

			$pdf->SetXY($x,$y_courant);
			$pdf->myWriteHTML($ligne[$i]);
		}
		else {
			$y_courant=$y+$decalage_v_top+$i*($hauteur_texte_mm+$marge_verticale);

			// Pour pouvoir afficher le $bord_debug A REFAIRE
			//$pdf->SetXY($x,$y_courant);
			//$pdf->Cell($largeur_dispo,$h_cell/count($tab_lig[$ifmax]['lignes']), '',$bord_debug,1,$align);

			$pdf->SetXY($x,$y_courant);
			/*
			if(preg_match('/<(.*)>/U',$ligne[$i])) {
				$pdf->WriteHTML($ligne[$i]);
			}
			else {
				$pdf->Cell($largeur_dispo,$hauteur_texte_mm, $ligne[$i],$bord_debug,1,$align);
			}
			*/
			$pdf->myWriteHTML($ligne[$i]);
		}
	}
}

// Ancienne fonction cell_ajustee() ne gÈrant pas les balises HTML B,I et U
function cell_ajustee0($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align='C',$align='L',$increment=0.3,$r_interligne=0.3) {
	global $pdf;

	// $increment:     nombre dont on rÈduit la police ‡ chaque essai
	// $r_interligne:  proportion de la taille de police pour les interlignes
	// $bordure:       LRBT
	// $v_align:       C(enter) ou T(op)

	$texte=trim($texte);
	$hauteur_texte=$hauteur_max_font;
	$pdf->SetFontSize($hauteur_texte);
	$taille_texte_total=$pdf->GetStringWidth($texte);

	// Ca nous donne le nombre max de lignes en hauteur avec la taille de police maxi
	// Il faudrait plutÙt dÈterminer ce nombre d'aprËs une taille minimale acceptable de police
	$nb_max_lig=max(1,floor($h_cell/((1+$r_interligne)*($hauteur_min_font*26/100))));
	// echo "\$nb_max_lig=$nb_max_lig<br />";

	//$ifmax=0;
	//$ifmax=1;
	$fmax=0;

	$tab_lig=array();
	for($j=1;$j<=$nb_max_lig;$j++) {
		$hauteur_texte=$hauteur_max_font;

		unset($ligne);
		$ligne=array();

		$tab=split(" ",$texte);
		$cpt=0;
		$i=0;
		while(true) {
			if(isset($ligne[$cpt])) {$ligne[$cpt].=" ";} else {$ligne[$cpt]="";}

			if(my_ereg("\n",$tab[$i])) {
				$tmp_tab=split("\n",$tab[$i]);

				for($k=0;$k<count($tmp_tab)-1;$k++) {
					if(!isset($ligne[$cpt])) {$ligne[$cpt]="";}
					$ligne[$cpt].=$tmp_tab[$k];
					$cpt++;
				}
				if(!isset($ligne[$cpt])) {$ligne[$cpt]="";}
				$ligne[$cpt].=$tmp_tab[$k];
			}
			else {
				if($pdf->GetStringWidth($ligne[$cpt].$tab[$i])>=$largeur_dispo) {
					$cpt++;
					$ligne[$cpt]=$tab[$i];
				}
				else {
					$ligne[$cpt].=$tab[$i];
				}
			}
			$i++;
			if(!isset($tab[$i])) {break;}
		}

		// Recherche de la plus longue ligne:
		$taille_texte_ligne=0;
		$num=0;
		for($i=0;$i<count($ligne);$i++) {
			// echo "\$ligne[$i]=$ligne[$i]<br />";
			$l=$pdf->GetStringWidth($ligne[$i]);
			if($taille_texte_ligne<$l) {$taille_texte_ligne=$l;$num=$i;}
		}

		// On calcule la hauteur en mm de la police (proportionnalitÈ: 100pt -> 26mm)
		$hauteur_texte_mm=$hauteur_texte*26/100;
		// Hauteur totale: Nombre de lignes multipliÈ par la hauteur de police avec les marges verticales
		$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);

		// echo "On calcule la taille de la police d'aprËs \$ligne[$num]=".$ligne[$num]."<br/>";
		// On ajuste la taille de police avec la plus grande ligne pour que cela tienne en largeur
		// et on contrÙle aussi que cela tient en hauteur, sinon on continue ‡ rÈduire la police.
		$grandeur_texte='test';
		while($grandeur_texte!='ok') {
			//if($largeur_dispo<$taille_texte_ligne) {
			if(($largeur_dispo<$taille_texte_ligne)||($hauteur_totale>$h_cell)) {
				$hauteur_texte=$hauteur_texte-$increment;
				if($hauteur_texte<$hauteur_min_font) {break;}
				$hauteur_texte_mm=$hauteur_texte*26/100;
				$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
				//$pdf->SetFont('Arial','',$hauteur_texte);
				$pdf->SetFontSize($hauteur_texte);
				$taille_texte_ligne=$pdf->GetStringWidth($ligne[$num]);
				// echo "\$hauteur_texte=$hauteur_texte -&gt; \$taille_texte_ligne=".$taille_texte_ligne."<br/>";
			}
			else {
				$grandeur_texte='ok';
			}
		}

		if($grandeur_texte=='ok') {
			// Hauteur de la police en mm
			$hauteur_texte_mm=$hauteur_texte*26/100;
			$tab_lig[$j]['hauteur_texte_mm']=$hauteur_texte_mm;
			// Hauteur de la police en pt
			$tab_lig[$j]['taille_police']=$hauteur_texte;
			// Hauteur totale du texte
			$tab_lig[$j]['hauteur_totale']=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
			// Marge verticale en mm entre les lignes
			$marge_verticale=$hauteur_texte_mm*$r_interligne;
			$tab_lig[$j]['marge_verticale']=$marge_verticale;
			// Tableau des lignes
			$tab_lig[$j]['lignes']=$ligne;

			// On choisit la hauteur de police la plus grande possible pour laquelle les lignes tiennent en hauteur
			// (la largeur a dÈj‡ ÈtÈ utilisÈe pour dÈcouper en lignes).
			if(($hauteur_texte>$fmax)&&($tab_lig[$j]['hauteur_totale']<=$h_cell)) {
				$ifmax=$j;
			}
		}
	}

	if((!isset($ifmax))||($tab_lig[$ifmax]['taille_police']<$hauteur_min_font)) {
		// On relance en remplaÁant les retours forcÈs ‡ la ligne (\n) par des espaces.

		$fmax=0;

		$tab_lig=array();
		for($j=1;$j<=$nb_max_lig;$j++) {
			$hauteur_texte=$hauteur_max_font;

			unset($ligne);
			$ligne=array();

			$tab=split(" ",trim(my_ereg_replace("\n"," ",$texte)));
			$cpt=0;
			$i=0;
			while(true) {
				if(isset($ligne[$cpt])) {$ligne[$cpt].=" ";} else {$ligne[$cpt]="";}

				if($pdf->GetStringWidth($ligne[$cpt].$tab[$i])>=$largeur_dispo) {
					$cpt++;
					$ligne[$cpt]=$tab[$i];
				}
				else {
					$ligne[$cpt].=$tab[$i];
				}
				$i++;
				if(!isset($tab[$i])) {break;}
			}

			// Recherche de la plus longue ligne:
			$taille_texte_ligne=0;
			$num=0;
			for($i=0;$i<count($ligne);$i++) {
				// echo "\$ligne[$i]=$ligne[$i]<br />";
				$l=$pdf->GetStringWidth($ligne[$i]);
				if($taille_texte_ligne<$l) {$taille_texte_ligne=$l;$num=$i;}
			}

			// On calcule la hauteur en mm de la police (proportionnalitÈ: 100pt -> 26mm)
			$hauteur_texte_mm=$hauteur_texte*26/100;
			// Hauteur totale: Nombre de lignes multipliÈ par la hauteur de police avec les marges verticales
			$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);

			// echo "On calcule la taille de la police d'aprËs \$ligne[$num]=".$ligne[$num]."<br/>";
			// On ajuste la taille de police avec la plus grande ligne pour que cela tienne en largeur
			// et on contrÙle aussi que cela tient en hauteur, sinon on continue ‡ rÈduire la police.
			$grandeur_texte='test';
			while($grandeur_texte!='ok') {
				//if($largeur_dispo<$taille_texte_ligne) {
				if(($largeur_dispo<$taille_texte_ligne)||($hauteur_totale>$h_cell)) {
					$hauteur_texte=$hauteur_texte-$increment;
					if($hauteur_texte<$hauteur_min_font) {break;}
					$hauteur_texte_mm=$hauteur_texte*26/100;
					$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
					//$pdf->SetFont('Arial','',$hauteur_texte);
					$pdf->SetFontSize($hauteur_texte);
					$taille_texte_ligne=$pdf->GetStringWidth($ligne[$num]);
					// echo "\$hauteur_texte=$hauteur_texte -&gt; \$taille_texte_ligne=".$taille_texte_ligne."<br/>";
				}
				else {
					$grandeur_texte='ok';
				}
			}

			if($grandeur_texte=='ok') {
				// Hauteur de la police en mm
				$hauteur_texte_mm=$hauteur_texte*26/100;
				$tab_lig[$j]['hauteur_texte_mm']=$hauteur_texte_mm;
				// Hauteur de la police en pt
				$tab_lig[$j]['taille_police']=$hauteur_texte;
				// Hauteur totale du texte
				$tab_lig[$j]['hauteur_totale']=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
				// Marge verticale en mm entre les lignes
				$marge_verticale=$hauteur_texte_mm*$r_interligne;
				$tab_lig[$j]['marge_verticale']=$marge_verticale;
				// Tableau des lignes
				$tab_lig[$j]['lignes']=$ligne;

				// On choisit la hauteur de police la plus grande possible pour laquelle les lignes tiennent en hauteur
				// (la largeur a dÈj‡ ÈtÈ utilisÈe pour dÈcouper en lignes).
				if(($hauteur_texte>$fmax)&&($tab_lig[$j]['hauteur_totale']<=$h_cell)) {
					$ifmax=$j;
				}
			}
		}


		// Si Áa ne passe toujours pas, on prend $hauteur_min_font sans retours ‡ la ligne et on tronque
		if(!isset($ifmax)) {
			/*
			$tab_lig=array();
			$j=1;
			$ifmax=$j;
			$hauteur_texte=$hauteur_min_font;
			$hauteur_texte_mm=$hauteur_texte*26/100;
			$tab_lig[$j]['hauteur_texte_mm']=$hauteur_texte_mm;
			// Hauteur de la police en pt
			$tab_lig[$j]['taille_police']=$hauteur_texte;
			// Hauteur totale du texte
			$tab_lig[$j]['hauteur_totale']=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
			// Marge verticale en mm entre les lignes
			$marge_verticale=$hauteur_texte_mm*$r_interligne;
			$tab_lig[$j]['marge_verticale']=$marge_verticale;
			// Tableau des lignes
			$tab_lig[$j]['lignes'][]="Texte trop long";
			*/

			$fmax=0;

			$tab_lig=array();
			$hauteur_texte=$hauteur_min_font;
			unset($ligne);
			$ligne=array();

			$tab=split(" ",trim(my_ereg_replace("\n"," ",$texte)));
			$cpt=0;
			$i=0;
			while(true) {
				if(isset($ligne[$cpt])) {$ligne[$cpt].=" ";} else {$ligne[$cpt]="";}

				if($pdf->GetStringWidth($ligne[$cpt].$tab[$i])>=$largeur_dispo) {

					if(($cpt+2)*$hauteur_texte*(1+$r_interligne)*26/100>$h_cell) {
						$d=1;
						while(($pdf->GetStringWidth(substr($ligne[$cpt],0,strlen($ligne[$cpt])-$d)."...")>=$largeur_dispo)&&($d<strlen($ligne[$cpt]))) {
							$d++;
						}
						$ligne[$cpt]=substr($ligne[$cpt],0,strlen($ligne[$cpt])-$d)."...";
						break;
					}

					$cpt++;
					$ligne[$cpt]=$tab[$i];
				}
				else {
					$ligne[$cpt].=$tab[$i];
				}
				$i++;
				if(!isset($tab[$i])) {break;} // On ne devrait pas quitter sur Áa puisque le texte va Ítre trop long
			}

			$j=1;
			$ifmax=$j;
			$hauteur_texte_mm=$hauteur_texte*26/100;
			$tab_lig[$j]['hauteur_texte_mm']=$hauteur_texte_mm;
			// Hauteur de la police en pt
			$tab_lig[$j]['taille_police']=$hauteur_texte;
			// Hauteur totale du texte
			$tab_lig[$j]['hauteur_totale']=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
			// Marge verticale en mm entre les lignes
			$marge_verticale=$hauteur_texte_mm*$r_interligne;
			$tab_lig[$j]['marge_verticale']=$marge_verticale;
			// Tableau des lignes
			$tab_lig[$j]['lignes']=$ligne;

		}
	}

	// On trace le rectangle (vide) du cadre:
	$pdf->SetXY($x,$y);
	$pdf->Cell($largeur_dispo,$h_cell, '',$bordure,2,'');

	// On va Ècrire les lignes avec la taille de police optimale dÈterminÈe (cf. $ifmax)
	//$marge_h=round(($h_cell-(count($ligne)*$hauteur_texte_mm+(count($ligne)-1)*$marge_verticale))/2);
	//$marge_h=round(($h_cell-$tab_lig[$ifmax]['hauteur_totale'])/2);
	$nb_lig=count($tab_lig[$ifmax]['lignes']);
	$h=count($tab_lig[$ifmax]['lignes'])*$tab_lig[$ifmax]['hauteur_texte_mm']*(1+$r_interligne);
	$t=$h_cell-$h;
	$bord_debug='';
	//$bord_debug='LRBT';
	for($i=0;$i<count($tab_lig[$ifmax]['lignes']);$i++) {

		//$pdf->SetXY(10,$y+$i*($hauteur_texte_mm+$marge_verticale)+$marge_h);
		$pdf->SetXY($x,$y+$i*($tab_lig[$ifmax]['hauteur_texte_mm']+$tab_lig[$ifmax]['marge_verticale']));

		//if($i==1) {$bord_debug='LRBT';} else {$bord_debug='';}
		//$pdf->Cell($largeur_dispo-4,$h_cell/count($tab_lig[$ifmax]['lignes']), $tab_lig[$ifmax]['lignes'][$i],$bord_debug,2,'');

		if($v_align=='T') {
			$pdf->Cell($largeur_dispo,$tab_lig[$ifmax]['hauteur_texte_mm']+2*$tab_lig[$ifmax]['marge_verticale'], $tab_lig[$ifmax]['lignes'][$i],$bord_debug,1,$align);
		}
		else {
			$pdf->Cell($largeur_dispo,$h_cell/count($tab_lig[$ifmax]['lignes']), $tab_lig[$ifmax]['lignes'][$i],$bord_debug,1,$align);
		}
	}
	//if($tab_lig[$ifmax]['taille_police']!=$hauteur_max_font) {$pdf->Cell(20,$h_cell, $tab_lig[$ifmax]['taille_police'],$bord_debug,2,'');}

}

function casse_mot($mot,$mode='maj') {
	if($mode=='maj') {
		return strtr(strtoupper($mot),"‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯","ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ");
	}
	elseif($mode=='min') {
		return strtr(strtolower($mot),"ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ","‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯");
	}
	elseif($mode=='majf') {
		if(strlen($mot)>1) {
			return strtr(strtoupper(substr($mot,0,1)),"‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯","ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ").strtr(strtolower(substr($mot,1)),"ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ","‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯");
		}
		else {
			return strtr(strtoupper($mot),"‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯","ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ");
		}
	}
	elseif($mode=='majf2') {
		$chaine="";
		$tab=explode(" ",$mot);
		for($i=0;$i<count($tab);$i++) {
			if($i>0) {$chaine.=" ";}
			$tab2=explode("-",$tab[$i]);
			for($j=0;$j<count($tab2);$j++) {
				if($j>0) {$chaine.="-";}
				if(strlen($mot)>1) {
					$chaine.=strtr(strtoupper(substr($mot,0,1)),"‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯","ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ").strtr(strtolower(substr($mot,1)),"ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ","‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯");
				}
				else {
					$chaine.=strtr(strtoupper($mot),"‰‚‡·Â„ÈËÎÍÚÛÙıˆ¯ÏÌÓÔ˘˙˚¸˝ÒÁ˛ˇÊΩ¯","ƒ¬¿¡≈√…»À “”‘’÷ÿÃÕŒœŸ⁄€‹›—«ﬁ›∆º–ÿ");
				}
			}
		}
		return $chaine;
	}
}


function javascript_tab_stat($pref_id,$cpt) {
	// Fonction ‡ appeler avec une portion de code du type:
	/*
	echo "<div style='position: fixed; top: 200px; right: 200px;'>\n";
	javascript_tab_stat('tab_stat_',$cpt);
	echo "</div>\n";
	*/

	$alt=1;
	echo "<table class='boireaus' summary='Statistiques'>\n";
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<th>Moyenne</th>\n";
	echo "<td id='".$pref_id."moyenne'></td>\n";
	echo "</tr>\n";

	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<th>1er quartile</th>\n";
	echo "<td id='".$pref_id."q1'></td>\n";
	echo "</tr>\n";

	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<th>MÈdiane</th>\n";
	echo "<td id='".$pref_id."mediane'></td>\n";
	echo "</tr>\n";

	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<th>3Ë quartile</th>\n";
	echo "<td id='".$pref_id."q3'></td>\n";
	echo "</tr>\n";

	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<th>Min</th>\n";
	echo "<td id='".$pref_id."min'></td>\n";
	echo "</tr>\n";

	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<th>Max</th>\n";
	echo "<td id='".$pref_id."max'></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<script type='text/javascript' language='JavaScript'>

function calcul_moy_med() {
	var eff_utile=0;
	var total=0;
	var valeur;
	var tab_valeur=new Array();
	var i=0;
	var j=0;
	var n=0;
	var mediane;
	var moyenne;
	var q1;
	var q3;
	var rang=0;

	for(i=0;i<$cpt;i++) {
		if(document.getElementById('n'+i)) {
			valeur=document.getElementById('n'+i).value;

			valeur=valeur.replace(',','.');

			if((valeur!='abs')&&(valeur!='disp')&&(valeur!='-')&&(valeur!='')) {
				tab_valeur[j]=valeur;
				// Tambouille pour Èviter que 'valeur' soit pris pour une chaine de caractËres
				total=eval((total*100+valeur*100)/100);
				eff_utile++;
				j++;
			}
		}
	}
	if(eff_utile>0) {
		moyenne=Math.round(10*total/eff_utile)/10;
		document.getElementById('".$pref_id."moyenne').innerHTML=moyenne;

		tab_valeur.sort((function(a,b){return a - b}));
		n=tab_valeur.length;
		if(n/2==Math.round(n/2)) {
			// Les indices commencent ‡ zÈro
			// Tambouille pour Èviter que 'valeur' soit pris pour une chaine de caractËres
			mediane=((eval(100*tab_valeur[n/2-1]+100*tab_valeur[n/2]))/100)/2;
		}
		else {
			mediane=tab_valeur[(n-1)/2];
		}
		document.getElementById('".$pref_id."mediane').innerHTML=mediane;

		if(eff_utile>=4) {
			rang=Math.ceil(eff_utile/4);
			q1=tab_valeur[rang-1];

			rang=Math.ceil(3*eff_utile/4);
			q3=tab_valeur[rang-1];

			document.getElementById('".$pref_id."q1').innerHTML=q1;
			document.getElementById('".$pref_id."q3').innerHTML=q3;
		}
		else {
			document.getElementById('".$pref_id."q1').innerHTML='-';
			document.getElementById('".$pref_id."q3').innerHTML='-';
		}

		document.getElementById('".$pref_id."min').innerHTML=tab_valeur[0];
		document.getElementById('".$pref_id."max').innerHTML=tab_valeur[n-1];
	}
	else {
		document.getElementById('".$pref_id."moyenne').innerHTML='-';
		document.getElementById('".$pref_id."mediane').innerHTML='-';
		document.getElementById('".$pref_id."q1').innerHTML='-';
		document.getElementById('".$pref_id."q3').innerHTML='-';
		document.getElementById('".$pref_id."min').innerHTML='-';
		document.getElementById('".$pref_id."max').innerHTML='-';
	}
}

calcul_moy_med();
</script>
";
}


function calcule_moy_mediane_quartiles($tab) {
	$tab2=array();

	//echo "<p>";
	$total=0;
	for($i=0;$i<count($tab);$i++) {
		//echo "\$tab[$i]=".$tab[$i]."<br />\n";
		if(($tab[$i]!='')&&($tab[$i]!='-')&&($tab[$i]!='&nbsp;')&&($tab[$i]!='abs')&&($tab[$i]!='disp')) {
			$tab2[]=my_ereg_replace(',','.',$tab[$i]);
			$total+=my_ereg_replace(',','.',$tab[$i]);
		}
	}

	// Initialisation
	$tab_retour['moyenne']='-';
	$tab_retour['mediane']='-';
	$tab_retour['min']='-';
	$tab_retour['max']='-';
	$tab_retour['q1']='-';
	$tab_retour['q3']='-';

	if(count($tab2)>0) {
		sort($tab2);

		/*
		echo "<p>";
		for($i=0;$i<count($tab2);$i++) {
			echo "\$tab2[$i]=".$tab2[$i]."<br />\n";
		}
		*/

		$moyenne=round(10*$total/count($tab2))/10;

		if(count($tab2)%2==0) {
			$mediane=($tab2[count($tab2)/2-1]+$tab2[count($tab2)/2])/2;
		}
		else {
			$mediane=$tab2[(count($tab2)-1)/2];
		}

		$min=min($tab2);
		$max=max($tab2);

		if(count($tab2)>=4) {
			$q1=$tab2[ceil(count($tab2)/4)-1];
			$q3=$tab2[ceil(3*count($tab2)/4)-1];
		}

		$tab_retour['moyenne']=$moyenne;
		$tab_retour['mediane']=$mediane;
		$tab_retour['min']=$min;
		$tab_retour['max']=$max;
		$tab_retour['q1']=$q1;
		$tab_retour['q3']=$q3;
	}

	/*
	echo "<p>";
	foreach($tab_retour as $key => $value) {
		echo "\$tab_retour['$key']=".$value."<br />\n";
	}
	*/

	return $tab_retour;
}


function get_nom_prenom_eleve($login_ele,$mode='simple') {
	$sql="SELECT nom,prenom FROM eleves WHERE login='$login_ele';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		// Si ce n'est pas un ÈlËve, c'est peut-Ítre un utilisateur prof, cpe, responsable,...
		$sql="SELECT 1=1 FROM utilisateurs WHERE login='$login_ele';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			return civ_nom_prenom($login_ele)." (non-ÈlËve)";
		}
		else {
			return "ElËve inconnu ($login_ele)";
		}
	}
	else {
		$lig=mysql_fetch_object($res);

		$ajout="";
		if($mode=='avec_classe') {
			$tmp_tab_clas=get_class_from_ele_login($login_ele);
			if($tmp_tab_clas['liste']!='') {
				$ajout=" (".$tmp_tab_clas['liste'].")";
			}
		}

		return casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2').$ajout;
	}
}

function get_commune($code_commune_insee,$mode){
	$retour="";

	if(strstr($code_commune_insee,'@')) {
		// On a affaire ‡ une commune ÈtrangËre
		$tmp_tab=split('@',$code_commune_insee);
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
				$retour=$lig->commune." (<i>".$lig->departement."</i>)";
			}
			elseif($mode==2) {
				$retour=$lig->commune." (".$lig->departement.")";
			}
		}
	}
	return $retour;
}


function civ_nom_prenom($login,$mode='prenom') {
	$retour="";
	$sql="SELECT nom,prenom,civilite FROM utilisateurs WHERE login='$login';";
	$res_user=mysql_query($sql);
	if (mysql_num_rows($res_user)>0) {
		$lig_user=mysql_fetch_object($res_user);
		if($lig_user->civilite!="") {
			$retour.=$lig_user->civilite." ";
		}
		if($mode=='prenom') {
			$retour.=strtoupper($lig_user->nom)." ".ucfirst(strtolower($lig_user->prenom));
		}
		else {
			// Initiale
			$retour.=strtoupper($lig_user->nom)." ".strtoupper(substr($lig_user->prenom,0,1));
		}
	}
	return $retour;
}

// Enleve le numÈro des titres numÈrotÈs ("1. Titre" -> "Titre")
// Exemple :  "12. Titre"  donne "Titre"
function supprimer_numero($texte) {
 return preg_replace(",^[[:space:]]*([0-9]+)([.)])[[:space:]]+,S","", $texte);
}


function test_ecriture_dossier() {
    global $gepiPath;

	//$tab_dossiers_rw=array("documents","images","secure","photos","backup","temp","mod_ooo/mes_modele","mod_ooo/tmp","mod_notanet/OOo/tmp","lib/standalone/HTMLPurifier/DefinitionCache/Serializer");
	//$tab_dossiers_rw=array("documents","images","photos","backup","temp","mod_ooo/mes_modele","mod_ooo/tmp","mod_notanet/OOo/tmp","lib/standalone/HTMLPurifier/DefinitionCache/Serializer");
	$tab_dossiers_rw=array("artichow/cache","backup","documents","images","images/background","lib/standalone/HTMLPurifier/DefinitionCache/Serializer","mod_ooo/mes_modeles","mod_ooo/tmp","photos","temp");

	$nom_fichier_test='test_acces_rw';

	echo "<table class='boireaus' summary='Tableau des dossiers qui doivent Ítre accessibles en Ècriture'>\n";
	echo "<tr>\n";
	echo "<th>Dossier</th>\n";
	echo "<th>Ecriture</th>\n";
	echo "</tr>\n";
	$alt=1;
	for($i=0;$i<count($tab_dossiers_rw);$i++) {
		$ok_rw="no";
		if ($f = @fopen("../".$tab_dossiers_rw[$i]."/".$nom_fichier_test, "w")) {
			@fputs($f, '<'.'?php $ok_rw = "yes"; ?'.'>');
			@fclose($f);
			include("../".$tab_dossiers_rw[$i]."/".$nom_fichier_test);
			$del = @unlink("../".$tab_dossiers_rw[$i]."/".$nom_fichier_test);
		}
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td style='text-align:left;'>$gepiPath/$tab_dossiers_rw[$i]</td>\n";
		echo "<td>";
		if($ok_rw=='yes') {
			echo "<img src='../images/enabled.png' height='20' width='20' alt=\"Le dossier est accessible en Ècriture.\" />";
		}
		else {
			echo "<img src='../images/disabled.png' height='20' width='20' alt=\"Le dossier n'est pas accessible en Ècriture.\" />";
		}
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
}


function test_ecriture_style_screen_ajout() {
	$nom_fichier='style_screen_ajout.css';
	$f=@fopen("../".$nom_fichier, "a+");
	if($f) {
		$ecriture=fwrite($f, "/* Test d'ecriture dans $nom_fichier */\n");
		fclose($f);
		if($ecriture) {return true;} else {return false;}
	}
	else {
		return false;
	}
}

function journal_connexions($login,$duree,$page='mon_compte',$pers_id=NULL) {
	switch( $duree ) {
	case 7:
		$display_duree="une semaine";
		break;
	case 15:
		$display_duree="quinze jours";
		break;
	case 30:
		$display_duree="un mois";
		break;
	case 60:
		$display_duree="deux mois";
		break;
	case 183:
		$display_duree="six mois";
		break;
	case 365:
		$display_duree="un an";
		break;
	case 'all':
		$display_duree="le dÈbut";
		break;
	}

	if($page=='mon_compte') {
		echo "<h2>Journal de vos connexions depuis <b>".$display_duree."</b>**</h2>\n";
	}
	else {
		echo "<h2>Journal des connexions de ".civ_nom_prenom($login)." depuis <b>".$display_duree."</b>**</h2>\n";
	}
	$requete = '';
	if ($duree != 'all') {$requete = "and START > now() - interval " . $duree . " day";}

	$sql = "select START, SESSION_ID, REMOTE_ADDR, USER_AGENT, AUTOCLOSE, END from log where LOGIN = '".$login."' ".$requete." order by START desc";
	//echo "$sql<br />";
	$day_now   = date("d");
	$month_now = date("m");
	$year_now  = date("Y");
	$hour_now  = date("H");
	$minute_now = date("i");
	$seconde_now = date("s");
	$now = mktime($hour_now, $minute_now, $seconde_now, $month_now, $day_now, $year_now);

	echo "<ul>
<li>Les lignes en rouge signalent une tentative de connexion avec un mot de passe erronÈ.</li>
<li>Les lignes en orange signalent une session close pour laquelle vous ne vous Ítes pas dÈconnectÈ correctement.</li>
<li>Les lignes en noir signalent une session close normalement.</li>
<li>Les lignes en vert indiquent les sessions en cours (cela peut correspondre ‡ une connexion actuellement close mais pour laquelle vous ne vous Ítes pas dÈconnectÈ correctement).</li>
</ul>
<table class='col' style='width: 90%; margin-left: auto; margin-right: auto; margin-bottom: 32px;' cellpadding='5' cellspacing='0' summary='Connexions'>
	<tr>
		<th class='col'>DÈbut session</th>
		<th class='col'>Fin session</th>
		<th class='col'>Adresse IP et nom de la machine cliente</th>
		<th class='col'>Navigateur</th>
	</tr>
";

	$res = sql_query($sql);
	if ($res) {
		for ($i = 0; ($row = sql_row($res, $i)); $i++)
		{
			$annee_b = substr($row[0],0,4);
			$mois_b =  substr($row[0],5,2);
			$jour_b =  substr($row[0],8,2);
			$heures_b = substr($row[0],11,2);
			$minutes_b = substr($row[0],14,2);
			$secondes_b = substr($row[0],17,2);
			$date_debut = $jour_b."/".$mois_b."/".$annee_b." ‡ ".$heures_b." h ".$minutes_b;

			$annee_f = substr($row[5],0,4);
			$mois_f =  substr($row[5],5,2);
			$jour_f =  substr($row[5],8,2);
			$heures_f = substr($row[5],11,2);
			$minutes_f = substr($row[5],14,2);
			$secondes_f = substr($row[5],17,2);
			$date_fin = $jour_f."/".$mois_f."/".$annee_f." ‡ ".$heures_f." h ".$minutes_f;
			$end_time = mktime($heures_f, $minutes_f, $secondes_f, $mois_f, $jour_f, $annee_f);

			$temp1 = '';
			$temp2 = '';
			if ($end_time > $now) {
				$temp1 = "<font color='green'>";
				$temp2 = "</font>";
			} else if (($row[4] == 1) or ($row[4] == 2) or ($row[4] == 3)) {
				//$temp1 = "<font color=orange>\n";
				$temp1 = "<font color='#FFA500'>";
				$temp2 = "</font>";
			} else if ($row[4] == 4) {
				$temp1 = "<b><font color='red'>";
				$temp2 = "</font></b>";

			}

			echo "<tr>\n";
			echo "<td class=\"col\">".$temp1.$date_debut.$temp2."</td>\n";
			if ($row[4] == 2) {
				echo "<td class=\"col\">".$temp1."Tentative de connexion<br />avec mot de passe erronÈ.".$temp2."</td>\n";
			}
			else {
				echo "<td class=\"col\">".$temp1.$date_fin.$temp2."</td>\n";
			}
			if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
				$result_hostbyaddr = " - ".@gethostbyaddr($row[2]);
			}
			else if ($active_hostbyaddr == "no_local") {
				if ((substr($row[2],0,3) == 127) or
					(substr($row[2],0,3) == 10.) or
					(substr($row[2],0,7) == 192.168)) {
					$result_hostbyaddr = "";
				}
				else {
					$tabip=explode(".",$row[2]);
					if(($tabip[0]==172)&&($tabip[1]>=16)&&($tabip[1]<=31)) {
						$result_hostbyaddr = "";
					}
					else {
						$result_hostbyaddr = " - ".@gethostbyaddr($row[2]);
					}
				}
			}
			else {
				$result_hostbyaddr = "";
			}

			echo "<td class=\"col\"><span class='small'>".$temp1.$row[2].$result_hostbyaddr.$temp2. "</span></td>\n";
			echo "<td class=\"col\">".$temp1. detect_browser($row[3]) .$temp2. "</td>\n";
			echo "</tr>\n";
			flush();
		}
	}


	echo "</table>\n";

	echo "<form action=\"".$_SERVER['PHP_SELF']."#connexion\" name=\"form_affiche_log\" method=\"post\">\n";

	if($page=='modify_user') {
		echo "<input type='hidden' name='user_login' value='$login' />\n";
		echo "<input type='hidden' name='journal_connexions' value='y' />\n";
	}
	elseif($page=='modify_eleve') {
		echo "<input type='hidden' name='eleve_login' value='$login' />\n";
		echo "<input type='hidden' name='journal_connexions' value='y' />\n";
	}
	elseif($page=='modify_resp') {
		echo "<input type='hidden' name='pers_id' value='$pers_id' />\n";
		echo "<input type='hidden' name='journal_connexions' value='y' />\n";
	}

	echo "Afficher le journal des connexions depuis : <select name=\"duree\" size=\"1\">\n";
	echo "<option ";
	if ($duree == 7) echo "selected";
	echo " value=7>Une semaine</option>\n";
	echo "<option ";
	if ($duree == 15) echo "selected";
	echo " value=15 >Quinze jours</option>\n";
	echo "<option ";
	if ($duree == 30) echo "selected";
	echo " value=30>Un mois</option>\n";
	echo "<option ";
	if ($duree == 60) echo "selected";
	echo " value=60>Deux mois</option>\n";
	echo "<option ";
	if ($duree == 183) echo "selected";
	echo " value=183>Six mois</option>\n";
	echo "<option ";
	if ($duree == 365) echo "selected";
	echo " value=365>Un an</option>\n";
	echo "<option ";
	if ($duree == 'all') echo "selected";
	echo " value='all'>Le dÈbut</option>\n";
	echo "</select>\n";
	echo "<input type=\"submit\" name=\"Valider\" value=\"Valider\" />\n";

	echo "</form>\n";

	echo "<p class='small'>** Les renseignements ci-dessus peuvent vous permettre de vÈrifier qu'une connexion pirate n'a pas ÈtÈ effectuÈe sur votre compte.
	Dans le cas d'une connexion inexpliquÈe, vous devez immÈdiatement en avertir l'<a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">administrateur</a>.</p>\n";
}

/**********************************************************************************************
 *                                  Fonctions Trombinoscope
 **********************************************************************************************/

/**
 * CrÈe les rÈpertoires photos/RNE_Etablissement, photos/RNE_Etablissement/eleves et
 * photos/RNE_Etablissement/personnels s'ils n'existent pas
 * @return bool true si tout se passe bien ou false si la crÈation d'un rÈpertoire Èchoue
 */
function cree_repertoire_multisite() {
  if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		// On rÈcupËre le RNE de l'Ètablissement
	if (!$repertoire=getSettingValue("gepiSchoolRne"))
	  return FALSE;
	//on vÈrifie que le dossier photos/RNE_Etablissement n'existe pas
	if (!is_dir("../photos/".$repertoire)){
	  // On crÈe le rÈpertoire photos/RNE_Etablissement
	  if (!mkdir("../photos/".$repertoire, 0700))
		return FALSE;
	  // On enregistre un fichier index.html dans photos/RNE_Etablissement
	  if (!copy  (  "../photos/index.html"  ,  "../photos/".$repertoire."/index.html" ))
		return FALSE;
	}
	//on vÈrifie que le dossier photos/RNE_Etablissement/eleves n'existe pas
	if (!is_dir("../photos/".$repertoire."/eleves")){
	  // On crÈe le rÈpertoire photos/RNE_Etablissement/eleves
	  if (!mkdir("../photos/".$repertoire."/eleves", 0700))
		return FALSE;
	  // On enregistre un fichier index.html dans photos/RNE_Etablissement/eleves
	  if (!copy  (  "../photos/index.html"  ,  "../photos/".$repertoire."/eleves/index.html" ))
		return FALSE;
	 }
	//on vÈrifie que le dossier photos/RNE_Etablissement/personnels n'existe pas
	if (!is_dir("../photos/".$repertoire."/personnels")){
	  // On crÈe le rÈpertoire photos/RNE_Etablissement/personnels
	  if (!mkdir("../photos/".$repertoire."/personnels", 0700))
		return FALSE;
	  // On enregistre un fichier index.html dans photos/RNE_Etablissement/personnels
	  if (!copy  (  "../photos/index.html"  ,  "../photos/".$repertoire."/personnels/index.html" ))
		return FALSE;
	  }
	}
	return TRUE;
}

/**
 * Recherche les ÈlËves sans photos
 *
 * @return array tableau de login - nom - prÈnom - classe - classe court - eleonet
 */
function recherche_eleves_sans_photo() {
  $eleve=NULL;
  $requete_liste_eleve = "SELECT e.elenoet, e.login, e.nom, e.prenom, c.nom_complet, c.classe
	FROM eleves e, j_eleves_classes jec, classes c
	WHERE e.login = jec.login
	AND jec.id_classe = c.id
	GROUP BY e.login
	ORDER BY id_classe, nom, prenom ASC";
  $res_eleve = mysql_query($requete_liste_eleve);
  while ($row = mysql_fetch_object($res_eleve)) {
	$nom_photo = nom_photo($row->elenoet);
	if (!($nom_photo and file_exists($nom_photo))) {
	  $eleve[]=$row;
	}
  }
  return $eleve;
}

/**
 *
 * @param text $statut statut recherchÈ
 * @return array tableau des personnels sans photo ou NULL
 */
function recherche_personnel_sans_photo($statut='professeur') {
  $personnel=NULL;
  $requete_liste_personnel = "SELECT login,nom,prenom FROM utilisateurs u
	WHERE u.statut='".$statut."'
	ORDER BY nom, prenom ASC";
  $res_personnel = mysql_query($requete_liste_personnel);
  while ($row = mysql_fetch_object($res_personnel)) {
	$nom_photo = nom_photo($row->login,"personnels");
	if (!($nom_photo and file_exists($nom_photo))) {
	  $personnel[]=$row;
	}
  }
  return $personnel;
}

/**
 * Efface le dossier photo passÈ en argument
 * @param text $photos le dossier ‡ effacer personnels ou eleves
 * @return text L'Ètat de la suppression
 */
function efface_photos($photos) {
// on liste les fichier du dossier photos/personnels ou photos/eleves
  if (!($photos=="eleves" || $photos=="personnels"))
	return ("Le dossier <strong>".$photos."</strong> n'ai pas valide.");
  if (cree_zip_archive("photos")==TRUE){
	$fichier_sup=array();
	if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		  // On rÈcupËre le RNE de l'Ètablissement
	  if (!$repertoire=getSettingValue("gepiSchoolRne"))
		return ("Erreur lors de la rÈcupÈration du dossier Ètablissement.");
	} else {
	  $repertoire="";
	}
	$folder = "../photos/".$repertoire.$photos."/";
	$dossier = opendir($folder);
	while ($Fichier = readdir($dossier)) {
	  if ($Fichier != "." && $Fichier != ".." && $Fichier != "index.html") {
		$nomFichier = $folder."".$Fichier;
		$fichier_sup[] = $nomFichier;
	  }
	}
	closedir($dossier);
	if(count($fichier_sup)==0) {
	  return ("Le dossier <strong>".$folder."</strong> ne contient pas de photo.") ;
	} else {
	  foreach ($fichier_sup as $fic_efface) {
		if(file_exists($fic_efface)) {
		  @unlink($fic_efface);
		  if(file_exists($fic_efface)) {
			return ("Le fichier  <strong>".$fic_efface."</strong> n'a pas pu Ítre effacÈ.");
		  }
		}
	  }
	  unset ($fic_efface);
	  return ("Le dossier <strong>".$folder."</strong> a ÈtÈ vidÈ.") ;
	}
  }else{
	return ("Erreur lors de la crÈation de l'archive.") ;
  }

}

/**********************************************************************************************
 *                               Fin Fonctions Trombinoscope
 **********************************************************************************************/

/**********************************************************************************************
 *                                   Fil d'Ariane
 **********************************************************************************************/
/**
 * gestion du fil d'ariane en remplissant le tableau $_SESSION['ariane']
 * @param text $lien page atteinte par le lien
 * @param text $texte texte ‡ afficher dans le fil d'ariane
 * @return bool True si tout s'est bien passÈ, False sinon
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
 * @param <boolean> $validation si True,
 * une validation sera demandÈe en cas de modification de la page
 * @param <texte> $themessage message ‡ afficher lors de la confirmation
 */
function affiche_ariane($validation= FALSE,$themessage="" ){
  if (isset($_SESSION['ariane'])){
	echo "<p class='ariane'>";
	foreach ($_SESSION['ariane']['lien'] as $index=>$lienActuel){
	  if ($index!="0"){
		echo " >> ";
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
 * Renvoie le chemin relatif pour remonter ‡ la racine du site
 * @param int $niveau niveau dans l'arborescence
 * @return text chemin relatif vers la racine
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
 *
 * @param text $dossier_a_archiver limitÈ ‡ documents ou photos
 * @param int $niveau niveau dans l'arborescence de la page appelante, racine = 0
 * @return bool
 */
function cree_zip_archive($dossier_a_archiver,$niveau=1) {
  $path = path_niveau();
  $dirname = "backup/".getSettingValue("backup_directory")."/";
  define( 'PCLZIP_TEMPORARY_DIR', $path.$dirname );
  require_once($path.'lib/pclzip.lib.php');

  if (isset($dossier_a_archiver)) {
	$suffixe_zip="_le_".date("Y_m_d_\a_H\hi");
	switch ($dossier_a_archiver) {
	case "documents":
	  $chemin_stockage = $path.$dirname."_cdt".$suffixe_zip.".zip"; //l'endroit o˘ sera stockÈe l'archive
	  $dossier_a_traiter = $path.'documents/'; //le dossier ‡ traiter
	  $dossier_dans_archive = 'documents'; //le nom du dossier dans l'archive crÈÈe
	  break;
	case "photos":
	  $chemin_stockage = $path.$dirname."_photos".$suffixe_zip.".zip";
	  $dossier_a_traiter = $path.'photos/'; //le dossier ‡ traiter
	  if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		$dossier_a_traiter .=getSettingValue("gepiSchoolRne")."/";
	  }
	  $dossier_dans_archive = 'photos'; //le nom du dossier dans l'archive crÈer
	  break;
	default:
	  $chemin_stockage = '';
	}

	if ($chemin_stockage !='') {
	  $archive = new PclZip($chemin_stockage);
	  $v_list = $archive->create($dossier_a_traiter,
			  PCLZIP_OPT_REMOVE_PATH,$dossier_a_traiter,
			  PCLZIP_OPT_ADD_PATH, $dossier_dans_archive);
	  if ($v_list == 0) {
		 die("Error : ".$archive->errorInfo(true));
		return FALSE;
	  }else {
		return TRUE;
	  }
	}
  }
}

/**
 * DÈplace un fichier de $source vers $dest
 * @param text $source : emplacement du fichier ‡ dÈplacer
 * @param text $dest : Nouvel emplacement du fichier
 * @return bool
 */
function deplacer_upload($source, $dest) {
    $ok = @copy($source, $dest);
    if (!$ok) $ok = (@move_uploaded_file($source, $dest));
    return $ok;
}

/**
 * TÈlÈcharge un fichier dans $dirname aprËs avoir nettoyer son nom si tout se passe bien :
 * $sav_file['name']=my_ereg_replace("[^.a-zA-Z0-9_=-]+", "_", $sav_file['name'])
 * @param array $sav_file tableau de type $_FILE["nom_du_fichier"]
 * @param text $dirname
 * @return text ok ou message d'erreur
 */
function telecharge_fichier($sav_file,$dirname,$type,$ext){
  if (!isset($sav_file['tmp_name']) or ($sav_file['tmp_name'] =='')) {
	return ("Erreur de tÈlÈchargement.");
  } else if (!file_exists($sav_file['tmp_name'])) {
	return ("Erreur de tÈlÈchargement 2.");
  } else if (!preg_match('/'.$ext.'$/',$sav_file['name'])){
	return ("Erreur : seuls les fichiers ayant l'extension .".$ext." sont autorisÈs.");
  } else if ($sav_file['type']!=$type ){
	return ("Erreur : seuls les fichiers de type '".$type."' sont autorisÈs.");
  } else {
	$nom_corrige = my_ereg_replace("[^.a-zA-Z0-9_=-]+", "_", $sav_file['name']);
	if (!deplacer_upload($sav_file['tmp_name'], $dirname."/".$nom_corrige)) {
	  return ("ProblËme de transfert : le fichier n'a pas pu Ítre transfÈrÈ sur le rÈpertoire ".$dirname);
	} else {
	  $sav_file['name']=$nom_corrige;
	  return ("ok");
	}
  }
}

/**
 * Extrait une archive Zip
 * @param text $fichier le nom du fichier ‡ dÈzipper
 * @param text $repertoire le rÈpertoire de destination
 * @param int $niveau niveau dans l'arborescence de la page appelante
 * @return text ok ou message d'erreur
 */
function dezip_PclZip_fichier($fichier,$repertoire,$niveau=1){
  $path = path_niveau();
  require_once($path.'lib/pclzip.lib.php');
  $archive = new PclZip($fichier);
  //if ($archive->extract() == 0) {
if ($archive->extract(PCLZIP_OPT_PATH, $repertoire) == 0) {
	return "Une erreur a ÈtÈ rencontrÈe lors de l'extraction du fichier zip";
  }else {
	return "ok";
  }
}

/**********************************************************************************************
 *                              Fin Manipulation de fichiers
 **********************************************************************************************/

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
				$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login' selected='true'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
	
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

function is_pp($login_prof,$id_classe,$login_eleve="") {
	$retour=false;
	if($login_eleve=='') {
		$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE id_classe='$id_classe' AND professeur='$login_prof';";
	}
	else {
		$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE id_classe='$id_classe' AND professeur='$login_prof' AND login='$login_eleve';";
	}
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {$retour=true;}

	return $retour;
}

/**
 *
 * VÈrifie qu'un utilisateur ‡ le droit de voir la page en lien
 *
 * @var string $id l'adresse de la page
 * telle qu'enregistrÈe dans la base droits
 * @var string $statut le statut de l'utilisateur
 *
 * @return entier 1 si l'utilisateur a le droit de voir la page
 * 0 sinon
 *
 *
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

function ajout_index_sous_dossiers($dossier) {
	global $niveau_arbo;

	$nb_creation=0;
	$nb_erreur=0;
	$nb_fich_existant=0;

	$retour="";

	//$dossier="../documents";
	$dir= opendir($dossier);
	if(!$dir) {
		$retour.="<p style='color:red'>Erreur lors de l'accËs au dossier '$dossier'.</p>\n";
	}
	else {
		$retour.="<p style='color:green'>SuccËs de l'accËs au dossier '$dossier'.</p>\n";
		while($entree=@readdir($dir)) {
			//$retour.="$dossier/$entree<br />\n";
			if(is_dir($dossier.'/'.$entree)&&($entree!='.')&&($entree!='..')) {
				if(!file_exists($dossier."/".$entree."/index.html")) {
					if ($f = @fopen($dossier.'/'.$entree."/index.html", "w")) {
						if((!isset($niveau_arbo))||($niveau_arbo==1)) {
							@fputs($f, '<script type="text/javascript">document.location.replace("../login.php")</script>');
						}
						elseif($niveau_arbo==0) {
							@fputs($f, '<script type="text/javascript">document.location.replace("./login.php")</script>');
						}
						elseif($niveau_arbo==2) {
							@fputs($f, '<script type="text/javascript">document.location.replace("../../login.php")</script>');
						}
						else {
							@fputs($f, '<script type="text/javascript">document.location.replace("../../../login.php")</script>');
						}
						@fclose($f);
						$nb_creation++;
					}
					else {
						$retour.="<span style='color:red'>Erreur lors de la crÈation de '$dir/$entree/index.html'.</span><br />\n";
						$nb_erreur++;
					}
				}
				else {
					$nb_fich_existant++;
				}
			}
		}

		if($nb_erreur>0) {
			$retour.="<p style='color:red'>$nb_erreur erreur(s) lors du traitement.</p>\n";
		}
		else {
			$retour.="<p style='color:green'>Aucune erreur lors de la crÈation des fichiers index.html</p>\n";
		}
	
		if($nb_creation>0) {
			$retour.="<p style='color:green'>CrÈation de $nb_creation fichier(s) index.html</p>\n";
		}
		else {
			$retour.="<p style='color:green'>Aucune crÈation de fichiers index.html n'a ÈtÈ effectuÈe.</p>\n";
		}
		$retour.="<p style='color:blue'>Il existait avant l'opÈration $nb_fich_existant fichier(s) index.html</p>\n";
	}

	return $retour;
}

// MÈthode pour envoyer les en-tÍtes HTTP nÈcessaires au tÈlÈchargement de fichier.
// Le content-type est obligatoire, ainsi que le nom du fichier.
function send_file_download_headers($content_type, $filename, $content_disposition = 'attachment') {

  //header('Content-Encoding: utf-8');
  header('Content-Type: '.$content_type);
  header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
  header('Content-Disposition: '.$content_disposition.'; filename="' . $filename . '"');
  
  // Contournement d'un bug IE lors d'un tÈlÈchargement en HTTPS...
  if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
    header('Pragma: private');
    header('Cache-Control: private, must-revalidate');
  } else {
    header('Pragma: no-cache');
  }
}


function affiche_infos_actions() {
	$sql="SELECT ia.* FROM infos_actions ia, infos_actions_destinataires iad WHERE
	ia.id=iad.id_info AND
	((iad.nature='individu' AND iad.valeur='".$_SESSION['login']."') OR
	(iad.nature='statut' AND iad.valeur='".$_SESSION['statut']."'));";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	$chaine_id="";
	if(mysql_num_rows($res)>0) {
		echo "<div id='div_infos_actions' style='width: 60%; border: 2px solid red; padding:3px; margin-left: 20%;'>\n";
		echo "<div id='info_action_titre' style='font-weight: bold;' class='infobulle_entete'>\n";
			echo "<div id='info_action_pliage' style='float:right; width: 1em'>\n";
			echo "<a href=\"javascript:div_alterne_affichage('conteneur')\"><span id='img_pliage_conteneur'><img src='images/icons/remove.png' width='16' height='16' /></span></a>";
			echo "</div>\n";
			echo "Actions en attente";
		echo "</div>\n";

		echo "<div id='info_action_corps_conteneur'>\n";

		$cpt_id=0;
		while($lig=mysql_fetch_object($res)) {
			echo "<div id='info_action_$lig->id' style='border: 1px solid black; margin:2px;'>\n";
				echo "<div id='info_action_titre_$lig->id' style='font-weight: bold;' class='infobulle_entete'>\n";
					echo "<div id='info_action_pliage_$lig->id' style='float:right; width: 1em'>\n";
					echo "<a href=\"javascript:div_alterne_affichage('$lig->id')\"><span id='img_pliage_$lig->id'><img src='images/icons/remove.png' width='16' height='16' /></span></a>";
					echo "</div>\n";
					echo $lig->titre;
				echo "</div>\n";

				echo "<div id='info_action_corps_$lig->id' style='padding:3px;' class='infobulle_corps'>\n";
					echo "<div style='float:right; width: 9em; text-align: right;'>\n";
					echo "<a href=\"".$_SERVER['PHP_SELF']."?del_id_info=$lig->id".add_token_in_url()."\" onclick=\"return confirmlink(this, '".traitement_magic_quotes($lig->description)."', 'Etes-vous s˚r de vouloir supprimer ".traitement_magic_quotes($lig->titre)."')\">Supprimer</span></a>";
					echo "</div>\n";

					echo nl2br($lig->description);
				echo "</div>\n";
			echo "</div>\n";
			if($cpt_id>0) {$chaine_id.=", ";}
			$chaine_id.="'$lig->id'";
			$cpt_id++;
		}
		echo "</div>\n";
		echo "</div>\n";

		echo "<script type='text/javascript'>
	function div_alterne_affichage(id) {
		if(document.getElementById('info_action_corps_'+id)) {
			if(document.getElementById('info_action_corps_'+id).style.display=='none') {
				document.getElementById('info_action_corps_'+id).style.display='';
				document.getElementById('img_pliage_'+id).innerHTML='<img src=\'images/icons/remove.png\' width=\'16\' height=\'16\' />'
			}
			else {
				document.getElementById('info_action_corps_'+id).style.display='none';
				document.getElementById('img_pliage_'+id).innerHTML='<img src=\'images/icons/add.png\' width=\'16\' height=\'16\' />'
			}
		}
	}

	chaine_id_action=new Array($chaine_id);
	for(i=0;i<chaine_id_action.length;i++) {
		id_a=chaine_id_action[i];
		if(document.getElementById('info_action_corps_'+id_a)) {
			div_alterne_affichage(id_a);
		}
	}
</script>\n";
	}
}

/**
 *
 * Enregistrer une action ‡ effectuer pour qu'elle soit par la suite affichÈe en page d'accueil pour tels ou tels utilisateurs
 *
 * @var string $titre titre de l'action/info
 * @var string $description le dÈtail de l'action ‡ effectuer avec autant que possible un lien vers la page et paramËtres utiles pour l'action
 * @var string $destinataire le tableau des login ou statuts des utilisateurs pour lesquels l'affichage sera rÈalisÈ
 * @var string $mode vaut 'individu' si $destinataire dÈsigne des logins et 'statut' si ce sont des statuts
 *
 * @return bolean true si l'enregistrement s'est bien effectuÈ
 * false sinon
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

	$sql="INSERT INTO infos_actions SET titre='".addslashes($titre)."', description='".addslashes($texte)."', date=NOW();";
	$insert=mysql_query($sql);
	if(!$insert) {
		return false;
	}
	else {
		$return=true;
		$id_info=mysql_insert_id();
		for($loop=0;$loop<count($tab_dest);$loop++) {
			$sql="INSERT INTO infos_actions_destinataires SET id_info='$id_info', nature='$mode', valeur='$tab_dest[$loop]';";
			$insert=mysql_query($sql);
			if(!$insert) {
				$return=false;
			}
		}

		return $return;
	}
}

function del_info_action($id_info) {
	// Dans le cas des infos destinÈes ‡ un statut... c'est le premier qui supprime qui vire pour tout le monde?
	// S'il s'agit bien de loguer des actions ‡ effectuer... elle ne doit Ítre effectuÈe qu'une fois.
	// Ou alors il faudrait ajouter des champs pour marquer les actions comme effectuÈes et n'afficher par dÈfaut que les actions non effectuÈes

	$sql="SELECT 1=1 FROM infos_actions_destinataires WHERE id_info='$id_info' AND ((nature='statut' AND valeur='".$_SESSION['statut']."') OR (nature='individu' AND valeur='".$_SESSION['login']."'));";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		$sql="DELETE FROM infos_actions_destinataires WHERE id_info='$id_info';";
		$del=mysql_query($sql);
		if(!$del) {
			return false;
		}
		else {
			$sql="DELETE FROM infos_actions WHERE id='$id_info';";
			$del=mysql_query($sql);
			if(!$del) {
				return false;
			}
			else {
				return true;
			}
		}
	}
}
?>
