<?php
/*
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*/


// Verification de la validitÈ d'un mot de passe
// longueur : getSettingValue("longmin_pwd") minimum
// composÈ de lettre et d'au moins un chiffre
// Si $flag = 1, il faut Ègalement au moins un caractËres  spÈcial (voir $char_spec dans global.inc)
function verif_mot_de_passe($password,$flag) {
    global $char_spec;
    if ($flag == 1)
        if ( ereg("(^[a-zA-Z]*$)|(^[0-9]*$)", $password) )
            return false;
        elseif ( preg_match("/^[[:alnum:]\W]{".getSettingValue("longmin_pwd").",}$/", $password) and preg_match("/[\W]+/", $password) and preg_match("/[0-9]+/", $password))
            return true; else return false;
    else
        if ( ereg("(^[a-zA-Z]*$)|(^[0-9]*$)", $password) )
            return false;
        elseif (strlen($password) < getSettingValue("longmin_pwd"))
            return false;
        else
            return true;

}

function test_unique_login($s) {
    // On vÈrifie que le login ne figure pas dÈj‡ dans la base utilisateurs
    $test1 = mysql_num_rows(mysql_query("SELECT login FROM utilisateurs WHERE (login='$s')"));
    if ($test1 != "0") {
        return 'no';
    } else {
        $test2 = mysql_num_rows(mysql_query("SELECT login FROM eleves WHERE (login='$s')"));
        if ($test2 != "0") {
            return 'no';
        } else {
            return 'yes';
        }
    }
}

function test_unique_e_login($s, $indice) {
    // fonction vÈrifiant l'unicitÈ du login $s
    // On vÈrifie que le login ne figure pas dÈj‡ dans une des bases ÈlËve des annÈes passÈes
/*    $test1 = mysql_num_rows(mysql_query("SELECT login FROM a1_eleves WHERE (login='$s')"));
    $test2 = mysql_num_rows(mysql_query("SELECT login FROM a2_eleves WHERE (login='$s')"));
    $test3 = mysql_num_rows(mysql_query("SELECT login FROM a3_eleves WHERE (login='$s')"));
    $test4 = mysql_num_rows(mysql_query("SELECT login FROM a4_eleves WHERE (login='$s')"));
    $test5 = mysql_num_rows(mysql_query("SELECT login FROM a5_eleves WHERE (login='$s')"));
    $test6 = mysql_num_rows(mysql_query("SELECT login FROM a6_eleves WHERE (login='$s')"));
*/
    // On vÈrifie que le login ne figure pas dÈj‡ dans la base utilisateurs
    $test7 = mysql_num_rows(mysql_query("SELECT login FROM utilisateurs WHERE (login='$s')"));
//    if (($test1 != "0") or ($test2 != "0") or ($test3 != "0") or ($test4 != "0") or ($test5 != "0") or ($test6 != "0") or ($test7 != "0")) {

    if ($test7 != "0") {

        // Si le login figure dÈj‡ dans une des bases ÈlËve des annÈes passÈes ou bien
        // dans la base utilisateurs, on retourne 'no' !
        return 'no';
    } else {
        // Si le login ne figure pas dans une des bases ÈlËve des annÈes passÈes ni dans la base
        // utilisateurs, on vÈrifie qu'un mÍme login ne vient pas d'Ítre attribuÈ !
        $test_tempo2 = mysql_num_rows(mysql_query("SELECT col2 FROM tempo2 WHERE (col2='$s')"));
        if ($test_tempo2 != "0") {
            return 'no';
        } else {
            $reg = mysql_query("INSERT INTO tempo2 VALUES ('$indice', '$s')");
            return 'yes';
        }
    }
}

// Fonction pour gÈnÈrer le login ‡ partir du nom et du prÈnom
// Le mode de gÈnÈration doit Ítre passÈ en argument
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
    if ($_mode == "name") {
            $temp1 = $_nom;
            $temp1 = strtoupper($temp1);
            $temp1 = ereg_replace(" ","", $temp1);
            $temp1 = ereg_replace("-","_", $temp1);
            $temp1 = ereg_replace("'","", $temp1);
            //$temp1 = substr($temp1,0,8);
        } elseif ($_mode == "name8") {
            $temp1 = $_nom;
            $temp1 = strtoupper($temp1);
            $temp1 = ereg_replace(" ","", $temp1);
            $temp1 = ereg_replace("-","_", $temp1);
            $temp1 = ereg_replace("'","", $temp1);
            $temp1 = substr($temp1,0,8);
        } elseif ($_mode == "fname8") {
            $temp1 = $_prenom{0} . $_nom;
            $temp1 = strtoupper($temp1);
            $temp1 = ereg_replace(" ","", $temp1);
            $temp1 = ereg_replace("-","_", $temp1);
            $temp1 = ereg_replace("'","", $temp1);
            $temp1 = substr($temp1,0,8);
        } elseif ($_mode == "fname19") {
            $temp1 = $_prenom{0} . $_nom;
            $temp1 = strtoupper($temp1);
            $temp1 = ereg_replace(" ","", $temp1);
            $temp1 = ereg_replace("-","_", $temp1);
            $temp1 = ereg_replace("'","", $temp1);
            $temp1 = substr($temp1,0,19);
        } elseif ($_mode == "firstdotname") {

            $temp1 = $_prenom . "." . $_nom;
            $temp1 = strtoupper($temp1);

            $temp1 = ereg_replace(" ","", $temp1);
            $temp1 = ereg_replace("-","_", $temp1);
            $temp1 = ereg_replace("'","", $temp1);
            //$temp1 = substr($temp1,0,19);
        } elseif ($_mode == "firstdotname19") {
            $temp1 = $_prenom . "." . $_nom;
            $temp1 = strtoupper($temp1);
            $temp1 = ereg_replace(" ","", $temp1);
            //$temp1 = ereg_replace("-","_", $temp1);
            $temp1 = ereg_replace("'","", $temp1);
            $temp1 = substr($temp1,0,19);
        } elseif ($_mode == "namef8") {
            $temp1 =  substr($_nom,0,7) . $_prenom{0};
            $temp1 = strtoupper($temp1);
            $temp1 = ereg_replace(" ","", $temp1);
            $temp1 = ereg_replace("-","_", $temp1);
            $temp1 = ereg_replace("'","", $temp1);
            //$temp1 = substr($temp1,0,8);
        } else {
        	return false;
        }

        $login_user = $temp1;
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

        // Nettoyage final
        $login_user = substr($login_user, 0, 50);
        $login_user = preg_replace("/[^A-Za-z0-9._\-]/","",trim(strtoupper($login_user)));

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

		return $login_user;
}


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
        if (isset($temp[1]) and ($temp[1] != '')) $i .= ".-".substr($temp[1], 0, 1);
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

// Verifie si l'extension d_base est active
function verif_active_dbase() {
    if (!function_exists("dbase_open"))  {
        echo "<center><p class=grand>ATTENTION : PHP n'est pas configurÈ pour gÈrer les fichiers GEP (dbf).
        <br />L'extension d_base n'est pas active. Adressez-vous ‡ l'administrateur du serveur pour corriger le problËme.</p></center></body></html>";
        die();
    }
}

// Verifie si un groupe appartient bien ‡ la personne connectÈe
//
function verif_groupe_appartient_prof($id_groupe) {
    $test = mysql_query("select id from groupes where (id='$id_groupe' and login_user = '".$_SESSION['login']."')");
    if (mysql_num_rows($test) == 0) {
        return 0;
    } else {
        return 1;
    }
}
//
// Verifie si un ÈlËve appartient ‡ un groupe
//
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
//
// Construit un tableau des groupes de l'utilisateur connectÈ
//
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

//
// Construit un tableau des classes et matiËres de l'utilisateur connectÈ
//
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

//
// Construit un tableau des classes de l'utilisateur connectÈ
//
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


function make_area_list_html($link, $current_classe, $current_matiere, $year, $month, $day) {
  echo "<b><i>Cahier&nbsp;de&nbsp;texte&nbsp;de&nbsp;:</i></b><br />";
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
        echo "<b>";
        $display_class = mysql_result($appel_donnees, $i, "classe");
        if (($id_classe == $current_classe) and ($id_matiere == $current_matiere)) {
           echo ">$display_class&nbsp;-&nbsp;$matiere_nom&nbsp;($matiere_nom_court)&nbsp;";
        } else {
           //echo "<a href=\"".$link."?id_classe=$id_classe&id_matiere=$id_matiere&year=$year&month=$month&day=$day\">$display_class&nbsp;-&nbsp;$matiere_nom&nbsp;($matiere_nom_court)</a>";
       echo "<a href=\"".$link."?id_classe=$id_classe&amp;id_matiere=$id_matiere&amp;year=$year&amp;month=$month&amp;day=$day\">$display_class&nbsp;-&nbsp;$matiere_nom&nbsp;($matiere_nom_court)</a>";

        }
        echo "</b><br />";
      }
      $j++;
    }
    $i++;
  }
}


function genDateSelector($prefix, $day, $month, $year, $option)
{
    if($day   == 0) $day = date("d");
    if($month == 0) $month = date("m");
    if($year  == 0) $year = date("Y");

    echo "<SELECT NAME=\"${prefix}day\">\n";

    for($i = 1; $i <= 31; $i++)
        echo "<OPTION" . ($i == $day ? " SELECTED" : "") . ">$i</option>\n";
        //echo "<OPTION" . ($i == $day ? " SELECTED" : "") . ">$i\n";

    echo "</SELECT>\n";
    echo "<SELECT NAME=\"${prefix}month\">\n";

    for($i = 1; $i <= 12; $i++)
    {
        $m = strftime("%b", mktime(0, 0, 0, $i, 1, $year));

        //print "<OPTION VALUE=\"$i\"" . ($i == $month ? " SELECTED" : "") . ">$m\n";

        // Si problËme avec l'encodage, essayer la ligne suivante
        //print "<OPTION VALUE=\"$i\"" . ($i == $month ? " SELECTED" : "") . ">".iconv('UTF-8','ISO-8859-1', $m)."</option>\n";
        echo "<OPTION VALUE=\"$i\"" . ($i == $month ? " SELECTED" : "") . ">$m</option>\n";
    }

    echo "</SELECT>\n";
    echo "<SELECT NAME=\"${prefix}year\">\n";

    $min = strftime("%Y", getSettingValue("begin_bookings"));
    if ($option == "more_years") $min = date("Y") - 5;

    $max = strftime("%Y", getSettingValue("end_bookings"));
    if ($option == "more_years") $max = date("Y") + 5;

    for($i = $min; $i <= $max; $i++)
        print "<OPTION" . ($i == $year ? " SELECTED" : "") . ">$i</option>\n";
        //print "<OPTION" . ($i == $year ? " SELECTED" : "") . ">$i\n";

    echo "</SELECT>\n";
}



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

//
// Calul de la moyenne d'un ÈlËve
//
function calcule_moyenne($login, $id_racine, $id_conteneur) {
    $total_point = 0;
    $somme_coef = 0;
    $exist_dev_fac = '';
    // On efface les moyennes de la table
    $delete = mysql_query("DELETE FROM cn_notes_conteneurs WHERE (login='$login' and id_conteneur='$id_conteneur')");

    // Appel des paramËtres du conteneur
    $appel_conteneur = mysql_query("SELECT * FROM cn_conteneurs WHERE id ='$id_conteneur'");
    $arrondir =  mysql_result($appel_conteneur, 0, 'arrondir');
    $mode =  mysql_result($appel_conteneur, 0, 'mode');
    $ponderation = mysql_result($appel_conteneur, 0, 'ponderation');

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
        $appel_dev = mysql_query("SELECT * FROM cn_devoirs WHERE id_conteneur='$id_conteneur'");
        $nb_dev  = mysql_num_rows($appel_dev);
        $max = 0;
        $indice_pond = 0;
        $k = 0;
        while ($k < $nb_dev) {
            $id_dev = mysql_result($appel_dev, $k, 'id');
            $coef[$k] = mysql_result($appel_dev, $k, 'coef');
            $note_query = mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$login' AND id_devoir='$id_dev')");
            $statut = @mysql_result($note_query, 0, "statut");
            $note = @mysql_result($note_query, 0, "note");
            if (($statut == '') and ($note!='')) {
                if (($note > $max) or (($note == $max) and ($coef[$k] > $coef[$indice_pond]))) {
                    $max = $note;
                    $indice_pond = $k;
                }
            }
            $k++;
        }
    }

    //
    // Calcul du total des points et de la somme des coefficients
    //
    $j=0;
    while ($j < $nb_boucle) {
        $appel_dev = mysql_query("SELECT * FROM cn_devoirs WHERE id_conteneur='$id_cont[$j]'");
        $nb_dev  = mysql_num_rows($appel_dev);
        $k = 0;
        while ($k < $nb_dev) {
            $id_dev = mysql_result($appel_dev, $k, 'id');
            $coef[$k] = mysql_result($appel_dev, $k, 'coef');
            // Prise en compte de la pondÈration
            if (($ponderation != 0) and ($j==0) and ($k==$indice_pond)) $coef[$k] = $coef[$k] + $ponderation;
            $facultatif[$k] = mysql_result($appel_dev, $k, 'facultatif');
            $note_query = mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$login' AND id_devoir='$id_dev')");
            $statut = @mysql_result($note_query, 0, "statut");
            $note = @mysql_result($note_query, 0, "note");
            if (($statut == '') and ($note!='')) {
                if ($facultatif[$k] == 'O') {
                    // le devoir n'est pas facultatif (Obligatoire) et entre systÈmatiquement dans le calcul de la moyenne si le coef est diffÈrent de zÈro
                    $total_point = $total_point + $coef[$k]*$note;
                    $somme_coef = $somme_coef + $coef[$k];
                } else if ($facultatif[$k] == 'B') {
                    //le devoir est facultatif comme un bonus : seuls les points supÈrieurs ‡ 10 sont pris en compte dans le calcul de la moyenne.
                    if ($note > 10) {
                        $total_point = $total_point + $coef[$k]*$note;
                        $somme_coef = $somme_coef + $coef[$k];
                    }
                } else {
                    //$facultatif == 'N' le devoir est facultatif comme une note : Le devoir est pris en compte dans la moyenne uniquement s'il amÈliore la moyenne de l'ÈlËve.
                    $exist_dev_fac = 'yes';
                    $total_point = $total_point + $coef[$k]*$note;
                    $somme_coef = $somme_coef + $coef[$k];
                    $points[$k] = $coef[$k]*$note;
                }
            }
            $k++;
        }
        $j++;
    }

    //
    // Prise en comptes des sous-conteneurs si mode=2
    //
    if ($mode == 2) {
        $j=0;
        while ($j < $nb_sous_cont) {
            $appel_cont = mysql_query("SELECT coef FROM cn_conteneurs WHERE id='$id_sous_cont[$j]'");
            $coefficient = mysql_result($appel_cont, 0, 'coef');
            $moyenne_query = mysql_query("SELECT * FROM cn_notes_conteneurs WHERE (login='$login' AND id_conteneur='$id_sous_cont[$j]')");
            $statut_moy = @mysql_result($moyenne_query, 0, "statut");
            if ($statut_moy == 'y') {
                $moy = @mysql_result($moyenne_query, 0, "note");
                $somme_coef = $somme_coef + $coefficient;
                $total_point = $total_point + $coefficient*$moy;
            }
            $j++;
        }
    }

    //
    // calcul de la moyenne des Èvaluations
    //
    if ($somme_coef != 0) {
        $moyenne = $total_point/$somme_coef;
        //
        // si un des devoirs a l'option "N", on prend la meilleure moyenne :
        //
        if ($exist_dev_fac == 'yes') {
            $k=0;
            while ($k < $nb_dev) {
                if ((($somme_coef - $coef[$k]) != 0) and ($facultatif[$k]=='N')) {
                    if (isset($points[$k])) {
                       $points[$k] = ($total_point-$points[$k])/($somme_coef - $coef[$k]);
                       $moyenne = max($moyenne,$points[$k]);
                    }
                }
                $k++;
            }
        }
        //
        // Calcul des arrondis
        //
        if ($arrondir == 's1') {
            // s1 : arrondir au dixiËme de point supÈrieur
            $moyenne = number_format(ceil(10*$moyenne)/10,1,'.','');
        } else if ($arrondir == 's5') {
            // s5 : arrondir au demi-point supÈrieur
            $moyenne = number_format(ceil(2*$moyenne)/2,1,'.','');
        } else if ($arrondir == 'se') {
            // se : arrondir au point entier supÈrieur
            $moyenne = number_format(ceil($moyenne),1,'.','');
        } else if ($arrondir == 'p1') {
            // s1 : arrondir au dixiËme le plus proche
            $moyenne = number_format(round(10*$moyenne)/10,1,'.','');
        } else if ($arrondir == 'p5') {
            // s5 : arrondir au demi-point le plus proche
            $moyenne = number_format(round(2*$moyenne)/2,1,'.','');
        } else if ($arrondir == 'pe') {
            // se : arrondir au point entier le plus proche
            $moyenne = number_format(round($moyenne),1,'.','');
        }
        $register = mysql_query("INSERT INTO cn_notes_conteneurs SET login='$login', id_conteneur='$id_conteneur',note='$moyenne',statut='y',comment=''");

    } else {
        $register = mysql_query("INSERT INTO cn_notes_conteneurs SET login='$login', id_conteneur='$id_conteneur',note='0',statut='',comment=''");

    }

}

//
// Affichage de la liste des conteneurs
//
function affiche_devoirs_conteneurs($id_conteneur,$periode_num, &$empty, $ver_periode) {
    global $gepiClosedPeriodLabel;
    //
    // Cas particulier de la racine
    //
    //$message_cont = "Etes-vous s˚r de vouloir supprimer le conteneur ci-dessous et les Èvaluations qu\\'il contient ?";
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
        if ($ver_periode <= 1)
            echo " (<b>".$gepiClosedPeriodLabel."</b>) ";
        echo "- <a href='saisie_notes.php?id_conteneur=$id_cont'>Visualisation</a> - <a href = 'add_modif_conteneur.php?id_conteneur=$id_cont&amp;mode_navig=retour_index'>Configuration</a>\n";
        $appel_dev = mysql_query("select * from cn_devoirs where id_conteneur='$id_cont' order by date");
        $nb_dev  = mysql_num_rows($appel_dev);
        if ($nb_dev != 0) $empty = 'no';
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
                    echo " - <a href = 'add_modif_dev.php?id_conteneur=$id_conteneur&amp;id_devoir=$id_dev&amp;mode_navig=retour_index'>Configuration</a> - <a href = 'index.php?id_racine=$id_racine&amp;del_dev=$id_dev' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_dev)."', '".$message_dev."')\">Suppression</a>\n";
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
        if($nb_cont>0){
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
                    echo "$nom_conteneur - <a href='saisie_notes.php?id_conteneur=$id_cont'>Visualisation</a> - <a href = 'add_modif_conteneur.php?id_conteneur=$id_cont&amp;mode_navig=retour_index'>Configuration</a>\n";
                    //$appel_dev = mysql_query("select * from cn_devoirs where id_conteneur='$id_cont'");
                    $appel_dev = mysql_query("select * from cn_devoirs where id_conteneur='$id_cont' order by date");
                    $nb_dev  = mysql_num_rows($appel_dev);
                    if ($nb_dev != 0) $empty = 'no';

		    // Existe-t-il des sous-conteneurs?
		    $sql="SELECT 1=1 FROM cn_conteneurs WHERE (parent='$id_cont')";
		    $test_sous_cont=mysql_query($sql);
		    $nb_sous_cont=mysql_num_rows($test_sous_cont);
		    //echo "<br />$sql<br />$nb_sous_cont<br />";

                    if(($nb_dev==0)&&($nb_sous_cont==0)){
			echo " - <a href = 'index.php?id_racine=$id_racine&amp;del_cont=$id_cont' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_conteneur)."', '".$message_cont."')\">Suppression</a>\n";
		    }
		    else{
			echo " - <a href = '#' onclick='alert(\"$message_cont_non_vide\")'><font color='gray'>Suppression</font></a>\n";
		    }

                    $j = 0;
                    if($nb_dev>0){
                        echo "<ul>\n";
                        while ($j < $nb_dev) {
                            $nom_dev = mysql_result($appel_dev, $j, 'nom_court');
                            $id_dev = mysql_result($appel_dev, $j, 'id');
                            echo "<li>\n";
                            echo "<font color='green'>$nom_dev</font> - <a href='saisie_notes.php?id_conteneur=$id_cont&amp;id_devoir=$id_dev'>Saisie</a> - <a href = 'add_modif_dev.php?id_conteneur=$id_conteneur&amp;id_devoir=$id_dev&amp;mode_navig=retour_index'>Configuration</a> - <a href = 'index.php?id_racine=$id_racine&amp;del_dev=$id_dev' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_dev)."', '".$message_dev."')\">Suppression</a>\n";
                            echo "</li>\n";
                            $j++;
                        }
                        echo "</ul>\n";
                    }
                    }
                if ($id_conteneur != $id_cont) affiche_devoirs_conteneurs($id_cont,$periode_num, $empty,$ver_periode);
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
    $sql = "select " . $_SESSION['statut'] . "
    from droits
    where id = '" . substr($url['path'], strlen($gepiPath)) . "'
    ;";
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
    echo "<tr>\n<td style=\"height: ".getSettingValue("col_hauteur")."px; width: ".getSettingValue("col_matiere_largeur")."px;\"><span class='$style_bulletin'><b>$AID_NOM_COMPLET</b><br />";
    $chaine_prof="";
    $n = '0';
    while ($n < $nb_lig) {
        $chaine_prof.=affiche_utilisateur($aid_prof_resp_login[$n],$id_classe)."<br />";
        $n++;
    }
    if($n!=0){
	echo "<i>".$chaine_prof."</i>";
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
	echo "<span class='$style_bulletin'><b>";
	if ($current_eleve_aid_statut == '') {
		echo $current_eleve_aid_note;
	} else if ($current_eleve_aid_statut == 'other') {
		echo "-";
	} else {
		echo $current_eleve_aid_statut;
	}
	echo "</b></span></td>\n";
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
            if ($display_nom == 'y') {echo "<b>$aid_nom</b><br />";}
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
    echo "<table border='1' width='680' cellspacing='1' cellpadding='1'>\n";
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

    echo "<table border=\"$bord\" cellspacing=\"0\" width=\"$larg_tab\" cellpadding=\"1\">\n";
    echo "<tr>\n";
    $j = 1;
    while($j < $nb_col+1) {
        echo "<th class='small'>$ligne1[$j]</th>\n";
        $j++;
    }
    echo "</tr>\n";
    $i = "0";
    $bg_color = "";
    $flag = "1";
    while($i < $nombre_lignes) {
        if ($couleur_alterne) {
            if ($flag==1) $bg_color = "bgcolor=\"#C0C0C0\""; else $bg_color = "     " ;
        }

        echo "<tr>\n";
        $j = 1;
        while($j < $nb_col+1) {
            if ((($j == 1) and ($col1_centre == 0)) or (($j != 1) and ($col_centre == 0))){
                echo "<td class='small' ".$bg_color.">{$col[$j][$i]}</td>\n";
            } else {
                echo "<td align=\"center\" class='small' ".$bg_color.">{$col[$j][$i]}</td>\n";
            }
            $j++;
        }
        echo "</tr>\n";
        if ($flag == "1") $flag = "0"; else $flag = "1";
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
    if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
        $BROWSER_VER = $log_version[2];
        $BROWSER_AGENT = 'OPERA';
    } else if (ereg('MSIE ([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
        $BROWSER_VER = $log_version[1];
        $BROWSER_AGENT = 'Internet Explorer';
    } else if (ereg('OmniWeb/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
        $BROWSER_VER = $log_version[1];
        $BROWSER_AGENT = 'OMNIWEB';
    } else if (ereg('(Konqueror/)(.*)(;)', $HTTP_USER_AGENT, $log_version)) {
        $BROWSER_VER = $log_version[2];
        $BROWSER_AGENT = 'KONQUEROR';
    } else if (ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)
               && ereg('Safari/([0-9]*)', $HTTP_USER_AGENT, $log_version2)) {
        $BROWSER_VER = $log_version[1] . '.' . $log_version2[1];
        $BROWSER_AGENT = 'SAFARI';
    } else if (ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
        $BROWSER_VER = $log_version[1];
        $BROWSER_AGENT = 'MOZILLA';
    } else {
        $BROWSER_VER = '';
        $BROWSER_AGENT = $HTTP_USER_AGENT;
    }
    return  $BROWSER_AGENT." - ".$BROWSER_VER;
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

    $current_backup_dir = getSettingValue("backup_directory");
    if ($current_backup_dir == null) $current_backup_dir = "no_folder";
    if (!file_exists("./backup/".$current_backup_dir)) {
        // On regarde d'abord si le rÈpertoire de backup n'existerait pas dÈj‡...
        $handle=opendir('./backup');
        $backupDirName = null;
        while ($file = readdir($handle)) {
            if (strlen($file) > 34 and is_dir('./backup/'.$file)) $backupDirName = $file;
        }

        closedir($handle);

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

function get_period_number($_id_classe) {
    $periode_query = mysql_query("SELECT count(*) FROM periodes WHERE id_classe = '" . $_id_classe . "'");
    $nb_periode = mysql_result($periode_query, 0);
    return $nb_periode;
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
  $out_html = "<form name=\"classe\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\"><b><i>Classe :</i></b><br />
  <select name=\"classe\" onChange=\"classe_go()\">";
  $out_html .= "<option value=\"".$link."?year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;id_classe=-1\">(Choisissez une classe)";
  // Ligne suivante corrigÈe sur suggestion tout ‡ fait pertinente de StÈphane, mail du 1er septembre 06
  $sql = "select DISTINCT c.id, c.classe from classes c, j_groupes_classes jgc, ct_entry ct WHERE (c.id = jgc.id_classe and jgc.id_groupe = ct.id_groupe) order by classe";

  $res = sql_query($sql);
  if ($res) for ($i = 0; ($row = sql_row($res, $i)); $i++)
  {
    $selected = ($row[0] == $current) ? "selected" : "";
    $link2 = "$link?year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$row[0]";
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
  </SCRIPT>
  <noscript>
  <input type=submit value=\"OK\" />
  </noscript>
  </form>";
  return $out_html;
}

function make_matiere_select_html($link, $id_ref, $current, $year, $month, $day)
{
	// $id_ref peut Ítre soit l'ID d'une classe, auquel cas on affiche tous les groupes
	// pour la classe, soit le login d'un ÈlËve, auquel cas on affiche tous les groupes
	// pour l'ÈlËve en question

  $out_html = "<form name=\"matiere\"  method=\"post\" action=\"".$_SERVER['PHP_SELF']."\"><b><i>MatiËre :</i></b><br />
  <select name=\"matiere\" onChange=\"matiere_go()\">\n";

  if (is_numeric($id_ref)) {
  	  $out_html .= "<option value=\"".$link."?&amp;year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;id_classe=$id_ref\">(Choisissez un enseignement)";
	  $sql = "select DISTINCT g.id, g.name, g.description from j_groupes_classes jgc, groupes g, ct_entry ct where (" .
	        "jgc.id_classe='".$id_ref."' and " .
	        "g.id = jgc.id_groupe and " .
	        "jgc.id_groupe = ct.id_groupe" .
	        ") order by g.name";
  } else {
  	  $out_html .= "<option value=\"".$link."?&amp;year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;login_eleve=$id_ref\">(Choisissez un enseignement)";
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


   $selected = ($row[0] == $current) ? "selected" : "";
   if (is_numeric($id_ref)) {
   		$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$id_ref&amp;id_groupe=$row[0]";
   } else {
   		$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;login_eleve=$id_ref&amp;id_groupe=$row[0]";
   }
   $out_html .= "<option $selected value=\"$link2\">" . htmlspecialchars($row[2] . " - ")." ".$chaine;
   }
  $out_html .= "\n</select>
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

  <noscript>
  <input type=submit value=\"OK\" />
  </noscript>
  </form>\n";

  return $out_html;
}

function make_eleve_select_html($link, $login_resp, $current, $year, $month, $day)
{
	global $selected_eleve;
	// $current est le login de l'ÈlËve actuellement sÈlectionnÈ
	$get_eleves = mysql_query("SELECT e.login, e.nom, e.prenom " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$login_resp."')");

	if (mysql_num_rows($get_eleves) == 0) {
			// Aucun ÈlËve associÈ
		$out_html = "<p>Vous semblez n'Ítre responsable d'aucun ÈlËve ! Contactez l'administrateur pour corriger cette erreur.</p>";
	} elseif (mysql_num_rows($get_eleves) == 1) {
			// Un seul ÈlËve associÈ : pas de formulaire nÈcessaire
		$selected_eleve = mysql_fetch_object($get_eleves);
		$out_html = "<p class='bold'>ElËve : ".$selected_eleve->prenom." ".$selected_eleve->nom."</p>";
	} else {
		// Plusieurs ÈlËves : on affiche un formulaire pour choisir l'ÈlËve
	  $out_html = "<form name=\"eleve\"  method=\"post\" action=\"".$_SERVER['PHP_SELF']."\"><b><i>ElËve :</i></b><br />
	  <select name=\"eleve\" onChange=\"eleve_go()\">\n";
	  $out_html .= "<option value=\"".$link."?&amp;year=".$year."&amp;month=".$month."&amp;day=".$day."\">(Choisissez un ÈlËve)";
		while ($current_eleve = mysql_fetch_object($get_eleves)) {
		   if ($current) {
		   	$selected = ($current_eleve->login == $current->login) ? "selected" : "";
		   } else {
		   	$selected = "";
		   }
		   $link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;login_eleve=".$current_eleve->login;
		   $out_html .= "<option $selected value=\"$link2\">" . htmlspecialchars($current_eleve->prenom . " - ".$current_eleve->nom)."</option>\n";
		}
	  $out_html .= "</select>
	  <script type=\"text/javascript\">
	  <!--
	  function eleve_go()
	  {
	    box = document.forms[\"eleve\"].eleve;
	    destination = box.options[box.selectedIndex].value;
	    if (destination) location.href = destination;
	  }
	  // -->
	  </SCRIPT>

	  <noscript>
	  <input type=submit value=\"OK\" />
	  </noscript>
	  </form>\n";
	}
	return $out_html;
}

function affiche_docs_joints($id_ct,$type_notice) {
// documents joints
$html = '';
$architecture="/documents/cl_dev";
if ($type_notice == "t")
    $sql = "SELECT titre, emplacement FROM ct_documents WHERE id_ct='$id_ct' AND emplacement LIKE '%".$architecture."%'  ORDER BY 'titre'";
else
    $sql = "SELECT titre, emplacement FROM ct_documents WHERE id_ct='$id_ct' AND emplacement NOT LIKE '%".$architecture."%'  ORDER BY 'titre'";

$res = sql_query($sql);
  if (($res) and (sql_count($res)!=0)) {
    $html .= "<small style=\"font-weight: bold;\">Document(s) joint(s):</small>";
    $html .= "<ul type=\"disc\" style=\"padding-left: 15px;\">";
    for ($i=0; ($row = sql_row($res,$i)); $i++) {
              $titre = $row[0];
              $emplacement = $row[1];
              $html .= "<li style=\"padding: 0px; margin: 0px; font-family: arial, sans-serif; font-size: 80%;\"><a href=\"$emplacement\" target=\"blank\">$titre</a></li>";
    }
    $html .= "</ul>";
   }
  return $html;
 }

// Cette fonction est ‡ appeler dans tous les cas o˘ une tentative
// d'utilisation illÈgale de Gepi est manifestement avÈrÈe.
// Elle est ‡ appeler notamment dans tous les tests de sÈcuritÈ lorsqu'un test
// est nÈgatif.
function tentative_intrusion($_niveau, $_description) {
	// On permet l'accËs ‡ $_SERVER et $_SESSION
	global $_SERVER;
	global $_SESSION;
	global $gepiPath;

	// On commence par enregistrer la tentative en question

	if (!isset($_SESSION['login'])) {
		// Ici, Áa veut dire que l'attaque est extÈrieure. Il n'y a pas d'utiliser loguÈ.
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

		// On envoie le mail
		$envoi = mail(getSettingValue("gepiAdminAdress"),
		    "GEPI : Alerte sÈcuritÈ -- Tentative d'intrusion",
		    $message,
		   "From: Mail automatique Gepi\r\n"."X-Mailer: PHP/" . phpversion());
	}
}

function tab_liste($tab_txt,$tab_lien,$nbcol){
	// Fonction destinÈe ‡ prÈsenter une liste de liens rÈpartis en $nbcol colonnes

	// Nombre d'enregistrements ‡ afficher
	$nombreligne=count($tab_txt);

	if(!is_int($nbcol)){
		$nbcol=3;
	}

	// Nombre de lignes dans chaque colonne:
	$nb_class_par_colonne=round($nombreligne/$nbcol);

	echo "<table width='100%'>\n";
	echo "<tr valign='top' align='center'>\n";
	echo "<td align='left'>\n";

	$i = 0;
	while ($i < $nombreligne){

		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		echo "<br />\n";
		echo "<a href='".$tab_lien[$i]."'>".$tab_txt[$i]."</a>";
		$i++;
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}

function check_temp_directory(){
	// Fonction destinÈe ‡ crÈer un dossier /temp/<alea>

	$dirname=getSettingValue("temp_directory");
	if(($dirname=='')||(!file_exists("./temp/$dirname"))){
		// Il n'existe pas
		// On crÈÈ le rÈpertoire temp
		$length = rand(35, 45);
		for($len=$length,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
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

function check_user_temp_directory(){
	// Fonction destinÈe ‡ crÈer un dossier /temp/<alea> propre au prof

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

function get_user_temp_directory(){
	$sql="SELECT temp_dir FROM utilisateurs WHERE login='".$_SESSION['login']."'";
	$res_temp_dir=mysql_query($sql);
	if(mysql_num_rows($res_temp_dir)>0){
		$lig_temp_dir=mysql_fetch_object($res_temp_dir);
		$dirname=$lig_temp_dir->temp_dir;

		if(($dirname!="")&&(strlen(ereg_replace("[A-Za-z0-9_]","",$dirname))==0)) {
			if(file_exists("../temp/$dirname")){
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
		if (eregi("^\.{1,2}$",$file))
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
		if (eregi("^\.{1,2}$",$file)){
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

function remplace_accents($chaine,$mode){
	//$retour=strtr(ereg_replace("∆","AE",ereg_replace("Ê","ae",ereg_replace("º","OE",ereg_replace("Ω","oe","$chaine"))))," '¬ƒ¿¡√ƒ≈« À»…ŒœÃÕ—‘÷“”’¶€‹Ÿ⁄›æ¥·‡‚‰„ÂÁÈËÍÎÓÔÏÌÒÙˆÚÛı®˚¸˘˙˝ˇ∏","__AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz");
	if($mode='all'){
		// On remplace espaces et apostrophes par des '_' et les caractËres accentuÈs par leurs Èquivalents non accentuÈs.
		$retour=strtr(ereg_replace("∆","AE",ereg_replace("Ê","ae",ereg_replace("º","OE",ereg_replace("Ω","oe","$chaine"))))," '¬ƒ¿¡√ƒ≈« À»…ŒœÃÕ—‘÷“”’¶€‹Ÿ⁄›æ¥·‡‚‰„ÂÁÈËÍÎÓÔÏÌÒÙˆÚÛı®˚¸˘˙˝ˇ∏","__AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz");
	}
	else{
		// On remplace les caractËres accentuÈs par leurs Èquivalents non accentuÈs.
		$retour=strtr(ereg_replace("∆","AE",ereg_replace("Ê","ae",ereg_replace("º","OE",ereg_replace("Ω","oe","$chaine")))),"¬ƒ¿¡√ƒ≈« À»…ŒœÃÕ—‘÷“”’¶€‹Ÿ⁄›æ¥·‡‚‰„ÂÁÈËÍÎÓÔÏÌÒÙˆÚÛı®˚¸˘˙˝ˇ∏","AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz");
	}
	return $retour;
}

function get_class_from_ele_login($ele_login){
	$sql="SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='$ele_login' ORDER BY periode,classe;";
	$res_class=mysql_query($sql);

	$tab_classe=array();
	if(mysql_num_rows($res_class)>0){
		while($lig_tmp=mysql_fetch_object($res_class)){
			$tab_classe[$lig_tmp->id_classe]=$lig_tmp->classe;
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

function get_enfants_from_resp_login($resp_login){
	$sql="SELECT e.nom,e.prenom,e.login FROM eleves e,
											responsables2 r,
											resp_pers rp
										WHERE e.ele_id=r.ele_id AND
											rp.pers_id=r.pers_id AND
											rp.login='$resp_login'
										ORDER BY e.nom,e.prenom;";
	$res_ele=mysql_query($sql);

	$tab_ele=array();
	if(mysql_num_rows($res_ele)>0){
		while($lig_tmp=mysql_fetch_object($res_ele)){
			$tab_ele[]=$lig_tmp->login;
			$tab_ele[]=ucfirst(strtolower($lig_tmp->prenom))." ".strtoupper($lig_tmp->nom);
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
				$chaine.=", <a href='../classes/classes_const.php?id_classe=$key'>$value</a>";
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

function creer_div_infobulle($id,$titre,$bg_titre,$texte,$bg_texte,$largeur,$hauteur,$drag,$bouton_close,$survol_close,$overflow){
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

	$style_box="color: #000000; border: 1px solid #000000; padding: 0px; position: absolute;";
	$style_bar="color: #ffffff; cursor: move; font-weight: bold; padding: 0px;";
	//$style_close="color: #ffffff; cursor: move; font-weight: bold; float:right; width: 1em;";
	$style_close="color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;";


	// On fait la liste des identifiants de DIV pour cacher les Div avec javascript en fin de chargement de la page (dans /lib/footer.inc.php).
	$tabid_infobulle[]=$id;

	// Conteneur:
	if($bg_texte==''){
		$div="<div id='$id' class='infobulle_corps' style='$style_box width: ".$largeur.$unite_div_infobulle."; ";
	}
	else{
		$div="<div id='$id' style='$style_box background-color: $bg_texte; width: ".$largeur.$unite_div_infobulle."; ";
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
			$div.="<div style='$style_close'><a href='#' onClick=\"cacher_div('$id');return false;\">";
			if(isset($niveau_arbo)&&$niveau_arbo==0){
				$div.="<img src='./images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			}
			else{
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
	$div.="<div";
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
	$posDiv_infobulle=$posDiv_infobulle+$largeur;

	return $div;
}

function debug_var(){
	// Fonction destinÈe ‡ afficher les variables transmises d'une page ‡ l'autre: GET, POST et SESSION
	echo "<div style='border: 1px solid black; background-color: white; color: black;'>\n";
	echo "<p>Variables envoyÈes en POST:</p>\n";
	echo "<blockquote>\n";
	foreach($_POST as $post => $val){
		echo "\$_POST['".$post."']=".$val."<br />\n";
	}
	echo "</blockquote>\n";

	echo "<p>Variables envoyÈes en GET:</p>\n";
	echo "<blockquote>\n";
	foreach($_GET as $get => $val){
		echo "\$_GET['".$get."']=".$val."<br />\n";
	}
	echo "</blockquote>\n";

	echo "<p>Variables envoyÈes en SESSION:</p>\n";
	echo "<blockquote>\n";
	foreach($_SESSION as $variable => $val){
		echo "\$_SESSION['".$variable."']=".$val."<br />\n";
	}
	echo "</blockquote>\n";
	echo "</div>\n";
}
?>
