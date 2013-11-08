<?php

require_once(dirname(__FILE__)."/HTMLPurifier.standalone.php");

/**
 * Corrige les caracteres degoutants utilises par les Windozeries
 *
 * @param type $texte
 * @return type 
 * @todo Fout le bazar et inutile en UTF-8
 */
function corriger_caracteres($texte) {
    return ensure_utf8($texte);
}

function traitement_magic_quotes($_value) {
   if (get_magic_quotes_gpc())    $_value = stripslashes($_value);
   if (!is_numeric($_value)) {
        $_value = mysql_real_escape_string($_value);
   }
   return $_value;
}

function unslashes($s)
{
    if (get_magic_quotes_gpc()) return stripslashes($s);
    else return $s;
}

# Nettoyage des variables dans $_POST et $_GET pour prévenir tout problème
# d'injection SQL
function anti_inject(&$_value, $_key) {
    global $mysqli;

    if (is_array($_value)) {
       foreach ($_value as $key2 => $value2) {
           $value2 = corriger_caracteres($value2);
           if (get_magic_quotes_gpc()) $_value[$key2] = stripslashes($value2);
           if (!is_numeric($_value[$key2])) {
                if($mysqli != "") {
                    $_value[$key2] = $mysqli->real_escape_string($_value[$key2]);
                } else {
                    $_value[$key2] = mysql_real_escape_string($_value[$key2]);
                }    
           }
       }
   } else {
       $_value = corriger_caracteres($_value);
       if (get_magic_quotes_gpc())    $_value = stripslashes($_value);
       if (!is_numeric($_value)) {           
            if($mysqli != "") {
                $_value = $mysqli->real_escape_string($_value);
            } else {
                $_value = mysql_real_escape_string($_value);
            }           
       }
   }
}

// Crée des variables à partir du tableau $_POST qui ne sont pas traitées par la fonction anti_inject
// Exemple : traitement particulier des mots de passe
// Ce sont des variables du type $_POST["no_anti_inject_nom_quelquonque"]
// On crée alors des variables $NON_PROTECT['nom_quelquonque']
function cree_variables_non_protegees() {
    global $NON_PROTECT;
    foreach ($_POST as $key => $value) {
        if (mb_substr($key,0,15) == "no_anti_inject_") {
            $temp = mb_substr($key,15,mb_strlen($key));
            if (get_magic_quotes_gpc())
                $NON_PROTECT[$temp] = stripslashes($_POST[$key]);
            else
                $NON_PROTECT[$temp] = $_POST[$key];

        }
    }
}

// Supprime les tag images avec une extension php, ce qui pourrait faire une attaque si un utilisateur
// forge un lien malfaisant vers une page php du serveur gepi et la place dans un tag image sur le site
function no_php_in_img($chaine) {
	global $niveau_arbo;

	if(isset($niveau_arbo)) {
		if($niveau_arbo == "0") {
			$pref_arbo="./";
		}
		elseif($niveau_arbo == "2") {
			$pref_arbo="../../";
		}
		elseif($niveau_arbo == "3") {
			$pref_arbo="../../../";
		}
	}
	else {
		$pref_arbo="../";
	}

	//$fich=fopen("/tmp/debug_img.txt","a+");

	$chaine_corrigee="";
	if(preg_match("/<img/i", $chaine)) {
		unset($tab);
		$tab=explode("<", $chaine);

		//fwrite($fich,"=============================================\n");

		// Il ne faut pas avoir de trucs du genre <img title='<' src='' />
		// htmlpurifier le change avant en <img title='&lt;' src='' />
		for($i=0;$i<count($tab);$i++) {

			//fwrite($fich,"\$tab[$i]=$tab[$i]\n++++++++++++++++++++++\n");

			if(preg_match("/^img/i", $tab[$i])) {
				unset($tab2);
				$tab2=explode(">",$tab[$i],2);

				//fwrite($fich,"\$tab2[0]=$tab2[0]\n\$tab2[1]=$tab2[1]\n++++++++++++++++++++++\n");

				// Est-ce qu'un <img src='' sans fermeture de la balise est quand même interprêté?

				unset($tab3);
				$tab3=explode(" ",preg_replace("/ *=/","=",preg_replace("/= */","=", strtr($tab2[0], "\n\r","  "))));
				for($j=0;$j<count($tab3);$j++) {
					//fwrite($fich,"\$tab3[$j]=$tab3[$j]\n++++++++++++++++++++++\n");
					if($j>0) {$chaine_corrigee.=" ";}
					if((preg_match("/^src=/i", $tab3[$j]))&&(preg_match("/\.php/i", $tab3[$j]))) {
						$chaine_corrigee.="src='".$pref_arbo."images/disabled.png'";
					}
					else {
						$chaine_corrigee.=$tab3[$j];
					}
					//fwrite($fich,"1. \$chaine_corrigee=$chaine_corrigee\n++++++++++++++++++++++\n");
				}

				if(isset($tab2[1])) {$chaine_corrigee.=">".$tab2[1];}
				//fwrite($fich,"2. \$chaine_corrigee=$chaine_corrigee\n++++++++++++++++++++++\n");
			}
			else {
				$chaine_corrigee.=$tab[$i];
				//if($i<count($tab)) {$chaine_corrigee.="<";}
				//fwrite($fich,"3. \$chaine_corrigee=$chaine_corrigee\n++++++++++++++++++++++\n");
			}
			if($i<count($tab)-1) {$chaine_corrigee.="<";}
			//fwrite($fich,"3b. \$chaine_corrigee=$chaine_corrigee\n++++++++++++++++++++++\n");
		}
	}
	else {
		$chaine_corrigee=$chaine;
	}

	//fwrite($fich,"4. \$chaine_corrigee=$chaine_corrigee\n++++++++++++++++++++++\n");
	//fclose($fich);

	return $chaine_corrigee;
}



// on force la valeur de magic_quotes_runtime à off de façon à ce que les valeurs récupérées dans la base
// puissent être affichées directement, sans caractère "\"
@set_magic_quotes_runtime(0);


$config = HTMLPurifier_Config::createDefault();
$config->set('Core.Encoding', 'utf-8'); // replace with your encoding
$config->set('HTML.Doctype', 'XHTML 1.0 Strict'); // replace with your doctype
$purifier = new HTMLPurifier($config);
$magic_quotes = get_magic_quotes_gpc();

foreach($_GET as $key => $value) {
	if(!is_array($value)) {
		if ($magic_quotes) $value = stripslashes($value);
		$_GET[$key]=$purifier->purify($value);
		if ($magic_quotes) $_GET[$key] = addslashes($_GET[$key]);
	}
	else {
		foreach($_GET[$key] as $key2 => $value2) {
			if ($magic_quotes) $value2 = stripslashes($value2);
			$_GET[$key][$key2]=$purifier->purify($value2);
			if ($magic_quotes) $_GET[$key][$key2] = addslashes($_GET[$key][$key2]);
		}
	}
}

foreach($_POST as $key => $value) {
	if(!is_array($value)) {
		if ($magic_quotes) $value = stripslashes($value);
		$_POST[$key]=$purifier->purify($value);
		if ($magic_quotes) $_POST[$key] = addslashes($_POST[$key]);
	}
	else {
		foreach($_POST[$key] as $key2 => $value2) {
			if ($magic_quotes) $value2 = stripslashes($value2);
			$_POST[$key][$key2]=$purifier->purify($value2);
			if ($magic_quotes) $_POST[$key][$key2] = addslashes($_POST[$key][$key2]);
		}
	}
}

if(isset($NON_PROTECT)) {
	foreach($NON_PROTECT as $key => $value) {
		if(!is_array($value)) {
			if ($magic_quotes) $value = stripslashes($value);
			$NON_PROTECT[$key]=$purifier->purify($value);
			if ($magic_quotes) $NON_PROTECT[$key] = addslashes($NON_PROTECT[$key]);
		}
		else {
			foreach($NON_PROTECT[$key] as $key2 => $value2) {
				if ($magic_quotes) $value2 = stripslashes($value2);
				$NON_PROTECT[$key][$key2]=$purifier->purify($value2);
				if ($magic_quotes) $NON_PROTECT[$key][$key2] = addslashes($NON_PROTECT[$key][$key2]);
			}
		}
	}
}

//echo "utiliser_no_php_in_img=$utiliser_no_php_in_img<br />";
//if($utiliser_no_php_in_img=='y') {
	//on purge aussi les images avec une extension php
	if(isset($_GET)) {
		foreach($_GET as $key => $value) {
			if(!is_array($value)) {
				$_GET[$key]=no_php_in_img($value);
			}
			else {
				foreach($_GET[$key] as $key2 => $value2) {
					$_GET[$key][$key2]=no_php_in_img($value2);
				}
			}
		}
	}

	if(isset($_POST)) {
		foreach($_POST as $key => $value) {
			if(!is_array($value)) {
				$_POST[$key]=no_php_in_img($value);
			}
			else {
				foreach($_POST[$key] as $key2 => $value2) {
					$_POST[$key][$key2]=no_php_in_img($value2);
				}
			}
		}
	}
	if(isset($NON_PROTECT)) {
		foreach($NON_PROTECT as $key => $value) {
			if(!is_array($value)) {
				$NON_PROTECT[$key]=no_php_in_img($value);
			}
			else {
				foreach($NON_PROTECT[$key] as $key2 => $value2) {
					$NON_PROTECT[$key][$key2]=no_php_in_img($value2);
				}
			}
		}
	}
//}

if (isset($variables_non_protegees)) cree_variables_non_protegees();

unset($liste_scripts_non_traites);
// Liste des scripts pour lesquels les données postées ne sont pas traitées si $traite_anti_inject = 'no';
$liste_scripts_non_traites = array(
"/visualisation/draw_artichow1.php",
"/visualisation/draw_artichow2.php",
"/public/contacter_admin_pub.php",
"/lib/create_im_mat.php",
"/gestion/contacter_admin.php",
"/messagerie/index.php",
"/gestion/accueil_sauve.php",
"/cahier_texte/index.php",
"/cahier_texte_2/ajax_enregistrement_compte_rendu.php",
"/cahier_texte_2/ajax_enregistrement_devoir.php",
"/cahier_texte_2/ajax_enregistrement_notice_privee.php",
"/cahier_texte_2/creer_sequence.php"
);

// On ajoute la possibilité pour les plugins de s'ajouter à la liste
if (isset($_ajouter_fichier_anti_inject)){
  $liste_scripts_non_traites[] = "/mod_plugins/" . $_ajouter_fichier_anti_inject;
}


$url = parse_url($_SERVER['REQUEST_URI']);
// On traite les données postées si nécessaire avec l'anti-injection mysql
if ((!(in_array(mb_substr($url['path'], mb_strlen($gepiPath)),$liste_scripts_non_traites))) OR ((in_array(mb_substr($url['path'], mb_strlen($gepiPath)),$liste_scripts_non_traites)) AND (!(isset($traite_anti_inject)) OR (isset($traite_anti_inject) AND $traite_anti_inject !="no")))) {
  array_walk($_GET, 'anti_inject');
  array_walk($_POST, 'anti_inject');
  array_walk($_REQUEST, 'anti_inject');
}

// On nettoie aussi $_SERVER et $_COOKIE de manière systématique
$anti_inject_var_SERVER="y";
$sql="SELECT 1=1 FROM setting WHERE name='anti_inject_var_SERVER' AND value='n';";

if(isset($useMysqli) && (TRUE == $useMysqli)) {
    $test_anti_inject_var_SERVER = mysqli_query($mysqli, $sql);
    if ($test_anti_inject_var_SERVER->num_rows > 0) {
        $anti_inject_var_SERVER="n";
    }
    if ($anti_inject_var_SERVER!="n") {
        array_walk($_SERVER, 'anti_inject');
    }
} else {
    $test_anti_inject_var_SERVER = mysql_query($sql);
    if(mysql_num_rows($test_anti_inject_var_SERVER)>0) {$anti_inject_var_SERVER="n";}
    if($anti_inject_var_SERVER!="n") {
        array_walk($_SERVER, 'anti_inject');
    }
}

/*
 * foreach($_SERVER as $key => $value) {
	echo "<p>\$_SERVER[$key]=$value<br />\n";
	//$_SERVER[$key]=anti_inject($value,$key);
	anti_inject($value,$key);
	echo "\$_SERVER[$key]=$value<br />\n";
}
 */
array_walk($_COOKIE, 'anti_inject');

//===========================================================


//On rétablit les "&" dans $_SERVER['REQUEST_URI']
$_SERVER['REQUEST_URI'] = str_replace("&amp;","&",$_SERVER['REQUEST_URI']);

if((isset($filtrage_extensions_fichiers_table_ct_types_documents))&&($filtrage_extensions_fichiers_table_ct_types_documents=='y')) {
	// On ne filtre pas ici.
	// Le filtrage est assuré dans le module CDT
}
else {
	$sql="SELECT 1=1 FROM setting WHERE name='filtrage_extensions_fichiers' AND value='n';";
               
    if($mysqli !="") {
        $test = mysqli_query($mysqli, $sql);
        $nb_lignes = $test->num_rows;
    } else {
        $test = mysql_query($sql);
        $nb_lignes = mysql_num_rows($test);
    }   
    
    
	if($nb_lignes == 0) {
		// Et on traite les fichiers uploadés
		if (!isset($AllowedFilesExtensions)) {
			$AllowedFilesExtensions = array("bmp","csv","doc","dot","epg","gif","ggb","gz","ico","jpg","jpeg","odg","odp","ods","odt","pdf","png","ppt","pptx","sql","swf","txt","xcf","xls","xlsx","xml","zip","pps","docx");
		}
		
		if (isset($_FILES) and !empty($_FILES)) {		
			foreach ($_FILES as &$file) {
				if (is_array($file['name'])) {
					$i = 0;
					while (isset($file['name'][$i])) {
						if ($file['name'][$i] != "") {
							if (!is_uploaded_file($file['tmp_name'][$i])) {
								$file['name'][$i] = "";
							}
							$delete_file = true;
							$k = 0;
							trim($file['name'][$i]);
							while (isset($AllowedFilesExtensions[$k])) {
								if (preg_match("/".$AllowedFilesExtensions[$k]."$/i",$file['name'][$i])) $delete_file = false;
								$k++;
							}
							if ($delete_file) {
								$file['name'][$i] = "";
								unlink($file['tmp_name'][$i]);
							}
						}
						$i++;
					}
				}
				else {
					if (isset($file['name'])) {
						if ($file['name'] != "") {
							if (!is_uploaded_file($file['tmp_name'])) {
								$file['name'] = "";
							}
							$delete_file = true;
							$k = 0;
							trim($file['name']);
							while (isset($AllowedFilesExtensions[$k])) {
								if (preg_match("/".$AllowedFilesExtensions[$k]."$/i",$file['name'])) $delete_file = false;
								$k++;
							}
							if ($delete_file) {
								$file['name'] = "";
								unlink($file['tmp_name']);
							}
						}
					}		
				
				}
			}
		}
	}
}


?>
