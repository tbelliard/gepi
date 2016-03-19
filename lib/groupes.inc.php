<?php
/** Fonctions de gestion des groupes
 * 
 * 
 * @package Initialisation
 * @subpackage groupes
 *
 */

/** Renvoie un tableau des groupes d'un enseignant
 * 
 * Chaque ligne contient les retours de get_group() d'un des groupes de l'enseignant
 *
 * @param text $_login Le login de l'enseignant
 * @param mixed $mode Tri sur j_groupes_matieres.id_matiere puis classes.classe si NULL, classe puis id_matiere sinon
 * @param array $tab_champs Les champs qu'on veut récupérer avec get_group()
 * @return array Le tableau des groupes
 * @see get_group()
 */
function get_groups_for_prof($_login,$mode=NULL,$tab_champs=array()) {
    global $mysqli;
	// Par discipline puis par classe
	if(!isset($mode)){
		$requete_sql = "SELECT jgp.id_groupe, jgm.id_matiere, jgc.id_classe
						FROM j_groupes_professeurs jgp, j_groupes_matieres jgm, j_groupes_classes jgc, classes c
						WHERE (" .
						"login = '" . $_login . "'
						AND jgp.id_groupe=jgm.id_groupe
						AND jgp.id_groupe=jgc.id_groupe
						AND jgc.id_classe=c.id) " .
						"GROUP BY jgp.id_groupe ".
						"ORDER BY jgm.id_matiere, c.classe" ;
	}
	else {
		$requete_sql = "SELECT jgp.id_groupe, jgm.id_matiere, jgc.id_classe
						FROM j_groupes_professeurs jgp, j_groupes_matieres jgm, j_groupes_classes jgc, classes c
						WHERE (" .
						"login = '" . $_login . "'
						AND jgp.id_groupe=jgm.id_groupe
						AND jgp.id_groupe=jgc.id_groupe
						AND jgc.id_classe=c.id) " .
						"GROUP BY jgp.id_groupe ".
						"ORDER BY c.classe, jgm.id_matiere" ;
	}
         
        $resultat = mysqli_query($mysqli, $requete_sql);  
        $nb = $resultat->num_rows;
        $groups = array();
        while ($obj = $resultat->fetch_object()) {
            $_id_groupe = $obj->id_groupe;
            if(count($tab_champs)>0) {
                $groups[] = get_group($_id_groupe,$tab_champs);
            } else {
                $groups[] = get_group($_id_groupe);
            }
        }
		
    return $groups;
}

/** Renvoie un tableau des groupes d'une classe
 * 
 * ATTENTION: Avec les catégories, les groupes dans aucune catégorie n'apparaissent pas.
 * 
 *
 * @param int $_id_classe Id de la classe
 * @param string $ordre Détermine l'ordre de tri
 * @param string $d_apres_categories Détermine comment on prend en compte les catégories
 * @return array Le tableau des groupes
 *         (on ne récupère que les indices id, name, description du groupe et les classes associées,
 *          pas les indices profs, eleves, periodes, matieres)
 */
function get_groups_for_class($_id_classe, $ordre="", $d_apres_categories="n") {
	global $get_groups_for_class_avec_proflist, $get_groups_for_class_avec_visibilite;
	// ATTENTION: Avec les catégories, les groupes dans aucune catégorie n'apparaissent pas.
	// Avec le choix "n" sur les catégories, on reste sur un fonctionnement proche de celui d'origine (cf old_way)

	if (!is_numeric($_id_classe)) {$_id_classe = "0";}

	if($d_apres_categories=="auto") {
		$d_apres_categories="n";

		$sql="SELECT display_mat_cat FROM classes WHERE id='".$_id_classe."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$d_apres_categories=old_mysql_result($res,0,"display_mat_cat");
		}
	}

	if($d_apres_categories=='y') {
		$sql="SELECT DISTINCT g.name, g.id, g.description, jgm.id_matiere
				FROM j_groupes_classes jgc, 
					j_groupes_matieres jgm, 
					j_matieres_categories_classes jmcc, 
					matieres m, 
					matieres_categories mc,
					groupes g
				WHERE ( mc.id=jmcc.categorie_id AND 
					jgc.categorie_id = jmcc.categorie_id AND 
					jgc.id_classe=jmcc.classe_id AND 
					jgc.id_classe='".$_id_classe."' AND 
					jgm.id_groupe=jgc.id_groupe AND 
					m.matiere = jgm.id_matiere AND
					g.id=jgc.id_groupe)
				ORDER BY jmcc.priority,mc.priority,jgc.priorite,m.nom_complet, g.name;";
	}
	else {
		if($ordre=="old_way") {
			// Ce que l'on avait auparavant.
			$sql="select DISTINCT g.name, g.id, g.description ".
								"from groupes g, j_groupes_classes j ".
								"where (" .
								"g.id = j.id_groupe " .
								" and j.id_classe = '" . $_id_classe . "'".
								") ORDER BY j.priorite, g.name";
		}
		else {
			$sql="select DISTINCT g.name, g.id, g.description, jgm.id_matiere FROM groupes g, 
					j_groupes_classes jgc, 
					j_groupes_matieres jgm
				WHERE (
					jgc.id_classe='".$_id_classe."' AND
					jgm.id_groupe=jgc.id_groupe
					AND jgc.id_groupe=g.id
					)
				ORDER BY jgc.priorite,jgm.id_matiere, g.name;";
		}
	}
	$query = mysqli_query($GLOBALS["mysqli"], $sql);

	$nb = mysqli_num_rows($query);
	$temp = array();
	for ($i=0;$i<$nb;$i++) {
		$temp[$i]["name"] = old_mysql_result($query, $i, "name");
		$temp[$i]["description"] = old_mysql_result($query, $i, "description");
		$temp[$i]["id"] = old_mysql_result($query, $i, "id");

		$temp[$i]["matiere"]["matiere"] = old_mysql_result($query, $i, "id_matiere");

		$get_classes = mysqli_query($GLOBALS["mysqli"], "SELECT c.id, c.classe, c.nom_complet FROM classes c, j_groupes_classes j WHERE (" .
										"c.id = j.id_classe and j.id_groupe = '" . $temp[$i]["id"]."')");
		$nb_classes = mysqli_num_rows($get_classes);
		for ($k=0;$k<$nb_classes;$k++) {
			$c_id = old_mysql_result($get_classes, $k, "id");
			$c_classe = old_mysql_result($get_classes, $k, "classe");
			$c_nom_complet = old_mysql_result($get_classes, $k, "nom_complet");

			$temp[$i]["classes"][] = array("id" => $c_id, "classe" => $c_classe, "nom_complet" => $c_nom_complet);
			if($k==0) {$temp[$i]["classlist_string"]="";} else {$temp[$i]["classlist_string"].=", ";}
			$temp[$i]["classlist_string"].=$c_classe;
		}

		if($get_groups_for_class_avec_proflist=="y") {
			$tmp_grp=get_profs_for_group($temp[$i]["id"]);
			$temp[$i]["proflist_string"]=$tmp_grp['proflist_string'];
			$temp[$i]['profs']["proflist_string"]=$tmp_grp['proflist_string'];
		}

		if($get_groups_for_class_avec_visibilite=="y") {
			$tmp_grp=get_visibilite_for_group($temp[$i]["id"]);
			$temp[$i]["visibilite"]=$tmp_grp['visibilite'];
		}

	}

	return $temp;
}

/** Renvoie un tableau des groupes d'un élève
 * 
 * ATTENTION: Avec les catégories, les groupes dans aucune catégorie n'apparaissent pas.
 * 
 *
 * @param int $_login_eleve Login de l'élève
 * @param int $_id_classe Identifiant de la classe de l'élève
 * @param string $ordre Détermine l'ordre de tri
 * @param string $d_apres_categories Détermine comment on prend en compte les catégories
 * @return array Le tableau des groupes
 *         (on ne récupère que les indices id, name, description du groupe et les classes associées,
 *          pas les indices profs, eleves, periodes, matieres)
 */
function get_groups_for_eleve($_login_eleve, $_id_classe, $ordre="", $d_apres_categories="n", $periode="") {
	// ATTENTION: Avec les catégories, les groupes dans aucune catégorie n'apparaissent pas.
	// Avec le choix "n" sur les catégories, on reste sur un fonctionnement proche de celui d'origine (cf old_way)

	if (!is_numeric($_id_classe)) {$_id_classe = "0";}

	$chaine_periode="";
	if ($periode!="") {
		$chaine_periode=" AND jeg.periode='$periode' ";
	}

	if($d_apres_categories=="auto") {
		$d_apres_categories="n";

		$sql="SELECT display_mat_cat FROM classes WHERE id='".$_id_classe."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$d_apres_categories=old_mysql_result($res,0,"display_mat_cat");
		}
	}

	if($d_apres_categories=='y') {
		$sql="SELECT DISTINCT g.name, g.id, g.description, jgm.id_matiere
				FROM j_eleves_groupes jeg, 
					j_groupes_classes jgc, 
					j_groupes_matieres jgm, 
					j_matieres_categories_classes jmcc, 
					matieres m, 
					matieres_categories mc,
					groupes g
				WHERE ( mc.id=jmcc.categorie_id AND 
					jgc.categorie_id = jmcc.categorie_id AND 
					jgc.id_classe=jmcc.classe_id AND 
					jgc.id_classe='".$_id_classe."' AND 
					jgm.id_groupe=jgc.id_groupe AND 
					m.matiere = jgm.id_matiere AND
					g.id=jgc.id_groupe AND
					jeg.id_groupe=jgc.id_groupe AND
					jeg.login='$_login_eleve'".$chaine_periode.")
				ORDER BY jmcc.priority,mc.priority,jgc.priorite,m.nom_complet, g.name;";
	}
	else {
		if($ordre=="old_way") {
			// Ce que l'on avait auparavant.
			$sql="select DISTINCT g.name, g.id, g.description ".
								"from groupes g, j_groupes_classes jgc, j_eleves_groupes jeg ".
								"where (" .
								"g.id = jgc.id_groupe " .
								" and jgc.id_classe = '" . $_id_classe . "'".
								" AND jeg.id_groupe=jgc.id_groupe".
								" AND jeg.login='$_login_eleve'".$chaine_periode.
								") ORDER BY jgc.priorite, g.name";
		}
		else {
			$sql="select DISTINCT g.name, g.id, g.description, jgm.id_matiere FROM groupes g, 
					j_groupes_classes jgc, 
					j_groupes_matieres jgm, 
					j_eleves_groupes jeg
				WHERE (
					jgc.id_classe='".$_id_classe."' AND
					jgm.id_groupe=jgc.id_groupe
					AND jgc.id_groupe=g.id AND
					jeg.id_groupe=jgc.id_groupe AND
					jeg.login='$_login_eleve'".$chaine_periode."
					)
				ORDER BY jgc.priorite,jgm.id_matiere, g.name;";
		}
	}
	//echo "$sql<br />";
	$query = mysqli_query($GLOBALS["mysqli"], $sql);

	$temp=array();
	$nb = mysqli_num_rows($query);
	for ($i=0;$i<$nb;$i++) {
		$temp[$i]["name"] = old_mysql_result($query, $i, "name");
		$temp[$i]["description"] = old_mysql_result($query, $i, "description");
		$temp[$i]["id"] = old_mysql_result($query, $i, "id");

		$temp[$i]["matiere"]["matiere"] = old_mysql_result($query, $i, "id_matiere");

		$get_classes = mysqli_query($GLOBALS["mysqli"], "SELECT c.id, c.classe, c.nom_complet FROM classes c, j_groupes_classes j WHERE (" .
										"c.id = j.id_classe and j.id_groupe = '" . $temp[$i]["id"]."')");
		$nb_classes = mysqli_num_rows($get_classes);
		for ($k=0;$k<$nb_classes;$k++) {
			$c_id = old_mysql_result($get_classes, $k, "id");
			$c_classe = old_mysql_result($get_classes, $k, "classe");
			$c_nom_complet = old_mysql_result($get_classes, $k, "nom_complet");

			$temp[$i]["classes"][] = array("id" => $c_id, "classe" => $c_classe, "nom_complet" => $c_nom_complet);
			if($k==0) {$temp[$i]["classlist_string"]="";} else {$temp[$i]["classlist_string"].=", ";}
			$temp[$i]["classlist_string"].=$c_classe;
		}
	}

	return $temp;
}

/** Renvoie un tableau des profs d'un groupe
 *
 * @param int $_id_groupe Id du groupe
 * @return array Le tableau des profs
 */
function get_profs_for_group($_id_groupe) {
	$temp["list"] = array();
	$temp["users"] = array();
	$temp["proflist_string"] = "";

	$get_profs = mysqli_query($GLOBALS["mysqli"], "SELECT u.login, u.nom, u.prenom, u.civilite 
		FROM utilisateurs u, j_groupes_professeurs j 
		WHERE (u.login = j.login and j.id_groupe = '".$_id_groupe."') 
		ORDER BY u.nom, u.prenom");

	$nb = mysqli_num_rows($get_profs);
	for ($i=0;$i<$nb;$i++){
		if($i>0) {$temp["proflist_string"].=", ";}
		$p_login = old_mysql_result($get_profs, $i, "login");
		$p_nom = old_mysql_result($get_profs, $i, "nom");
		$p_prenom = old_mysql_result($get_profs, $i, "prenom");
		$civilite = old_mysql_result($get_profs, $i, "civilite");
		$temp["list"][] = $p_login;
		$temp["users"][$p_login] = array("login" => $p_login, "nom" => $p_nom, "prenom" => $p_prenom, "civilite" => $civilite);
		$temp["proflist_string"].=$civilite." ".casse_mot($p_nom,'maj')." ".my_strtoupper(mb_substr($p_prenom,0,1));
	}

	return $temp;
}

/** Renvoie un tableau de la visibilité du groupe dans les différents domaines (bulletins, cahier_notes,...)
 *
 * @param int $_id_groupe Id du groupe
 * @return array Le tableau des visibilités
 */
function get_visibilite_for_group($_id_groupe) {
	global $tab_domaines;
	$temp["visibilite"]=array();

	for($loop=0;$loop<count($tab_domaines);$loop++) {
		$temp["visibilite"][$tab_domaines[$loop]]="y";
	}

	$sql="SELECT * FROM j_groupes_visibilite WHERE id_groupe='" . $_id_groupe . "';";
	$res_vis=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_vis)>0) {
		while($lig_vis=mysqli_fetch_object($res_vis)) {
			$temp["visibilite"][$lig_vis->domaine]=$lig_vis->visible;
		}
	}

	return $temp;
}

/**
 * Renvoie les informations sur le groupe demandé
 *
 * @param integer $_id_groupe identifiant du groupe
 * @param array $tab_champs réglages permis par la fonction : all, matieres, classes, eleves, periodes, profs, visibilite
 * @return array Tableaux imbriques des informations du groupe
 */
function get_group($_id_groupe,$tab_champs=array('all')) {
    global $mysqli;
	$temp=array();

	$get_matieres='n';
	$get_classes='n';
	$get_eleves='n';
	$get_periodes='n';
	$get_profs='n';
	$get_visibilite='n';
	if(in_array('all',$tab_champs)) {
		$get_matieres='y';
		$get_classes='y';
		$get_eleves='y';
		$get_profs='y';
		$get_periodes='y';
		$get_visibilite='y';
	}
	else {
		if(in_array('matieres',$tab_champs)) {$get_matieres='y';}
		if(in_array('classes',$tab_champs)) {$get_classes='y';}
		if(in_array('eleves',$tab_champs)) {$get_eleves='y';$get_classes='y';$get_periodes='y';}
		if(in_array('periodes',$tab_champs)) {$get_periodes='y';$get_classes='y';}
		if(in_array('profs',$tab_champs)) {$get_profs='y';}
		if(in_array('visibilite',$tab_champs)) {$get_visibilite='y';}
	}

    if (!is_numeric($_id_groupe)) {$_id_groupe = "0";}

	// Informations générales sur le groupe:
    $sql="select name, id, description ".
                            "from groupes ".
                            "where (" .
                            "id = '" . $_id_groupe . "'".
                            ")"; 
		$resultat = mysqli_query($mysqli, $sql);
        if($resultat->num_rows==0) {
            echo "<span style='color:red'>Le groupe n°$_id_groupe n'existe pas.</span><br />";
        } else {
            $obj = $resultat->fetch_object();
            $temp["name"] = $obj->name;
            $temp["description"] = $obj->description;
            $temp["id"] = $obj->id;
            if($get_visibilite=='y') {
                $temp["visibilite"]=array();
                $sql="SELECT * FROM j_groupes_visibilite WHERE id_groupe='" . $_id_groupe . "';";
                $res_vis = mysqli_query($mysqli, $sql);
                if($res_vis->num_rows > 0) {
                    while($lig_vis = $res_vis->fetch_object()) {
                        $temp["visibilite"][$lig_vis->domaine]=$lig_vis->visible;
                    }
                } 
                $res_vis->close();
            }            
            if($get_matieres=='y') {
                $sql_matieres = "SELECT m.matiere, m.nom_complet, m.categorie_id, m.code_matiere FROM matieres m, j_groupes_matieres j " .
                                                                "WHERE (" .
                                                                "m.matiere = j.id_matiere and " .
                                                                "j.id_groupe = '" . $_id_groupe . "')";
                $matiere = mysqli_query($mysqli, $sql_matieres);
                if ($matiere->num_rows > 0) {
                    $obj_matieres = $matiere->fetch_object();
                    $temp["matiere"]["matiere"] = $obj_matieres->matiere;
                    $temp["matiere"]["nom_complet"] = $obj_matieres->nom_complet;
                    $temp["matiere"]["categorie_id"] = $obj_matieres->categorie_id;
                    $temp["matiere"]["code_matiere"] = $obj_matieres->code_matiere;
                }
                $matiere->close();
            }
            if($get_classes=='y') {
                // Classes
                $sql_classes="SELECT c.id, c.classe, c.nom_complet, j.priorite, j.coef, j.mode_moy, j.categorie_id, j.saisie_ects, j.valeur_ects 
                  FROM classes c, j_groupes_classes j 
                    WHERE (c.id = j.id_classe AND j.id_groupe = '".$_id_groupe."') 
                      ORDER BY c.classe, c.nom_complet;";
                $get_classes = mysqli_query($mysqli, $sql_classes);
                $nb_classes = $get_classes->num_rows;                
                while ($obj_classe = $get_classes->fetch_object()) {
                    $c_id = $obj_classe->id;
                    $c_classe = $obj_classe->classe;
                    $c_nom_complet = $obj_classe->nom_complet;
                    $c_priorite = $obj_classe->priorite;
                    $c_coef = $obj_classe->coef;
                    $c_mode_moy = $obj_classe->mode_moy;
                    $c_saisie_ects = ($obj_classe->saisie_ects > '0') ? TRUE : FALSE;
                    $c_valeur_ects = $obj_classe->valeur_ects;
                    $c_cat_id = $obj_classe->categorie_id;
                    $temp["classes"]["list"][] = $c_id;
                    $temp["classes"]["classes"][$c_id] = array("id" => $c_id, "classe" => $c_classe, "nom_complet" => $c_nom_complet, "priorite" => $c_priorite, "coef" => $c_coef, "mode_moy" => $c_mode_moy, "saisie_ects" => $c_saisie_ects, "valeur_ects" => $c_valeur_ects, "categorie_id" => $c_cat_id);
                }

                if(isset($temp["classes"]["classes"])) {
                    $str = NULL;
                    foreach ($temp["classes"]["classes"] as $classe) {
                        $str .= $classe["classe"] . ", ";
                    }
                    $str = mb_substr($str, 0, -2);
                    $temp["classlist_string"] = $str;
                }
                
                $get_classes->close();
            }
            if($get_profs=='y') {
                // Professeurs
                $temp["profs"]["list"] = array();
                $temp["profs"]["users"] = array();
                $temp["profs"]["proflist_string"] = "";

                $sql_profs = "SELECT u.login, u.nom, u.prenom, u.civilite 
                  FROM utilisateurs u, j_groupes_professeurs j 
                  WHERE (u.login = j.login and j.id_groupe = '".$_id_groupe."') 
                    ORDER BY u.nom, u.prenom";
                $get_profs = mysqli_query($mysqli, $sql_profs);
                $nb = $get_profs->num_rows; 
                $i = 0;
                while ($obj_prof = $get_profs->fetch_object()) {
                    if($i>0) {$temp["profs"]["proflist_string"].=", "; }
                    $p_login = $obj_prof->login;
                    $p_nom = casse_mot($obj_prof->nom,'maj');
                    $p_prenom = casse_mot($obj_prof->prenom,'majf2');
                    $civilite = $obj_prof->civilite;
					$temp["profs"]["list"][] = $p_login;
                    $temp["profs"]["users"][$p_login] = array("login" => $p_login, "nom" => $p_nom, "prenom" => $p_prenom, "civilite" => $civilite);
                    $temp["profs"]["proflist_string"].=$civilite." ".$p_nom." ".my_strtoupper(mb_substr($p_prenom,0,1));
                    $i++;
                }
                $get_profs->close();
                $temp["proflist_string"]=$temp["profs"]["proflist_string"];
            }
            if($get_periodes=='y') {
                // Périodes
                $temp["periodes"]=array();
                // Pour le nom et le nombre de periodes, on suppose qu'elles sont identiques dans toutes les classes du groupe
                $sql_periode = "SELECT * FROM periodes WHERE id_classe = '". $temp["classes"]["list"][0] ."' ORDER BY num_periode";
                $periode_query = mysqli_query($mysqli, $sql_periode);
                $nb_periode = $periode_query->num_rows + 1;
                $i = "1";
                while ($obj_period = $periode_query->fetch_object()) {
                    $temp["periodes"][$i]["nom_periode"] = $obj_period->nom_periode;
                    $temp["periodes"][$i]["num_periode"] = $i;
                    $i++;
                }
                $temp["nb_periode"] = $nb_periode;
                // Verrouillage
                
                // Initialisation
                $i = "1";
                $all_clos = "";
                $all_open = "";
                $all_clos_part = "";
                while ($i < $nb_periode) {
                    $liste_ver_per[$i] = "";
                    $i++;
                }

                foreach ($temp["classes"]["list"] as $c_id) {
                    $sql_periode2 = "SELECT * FROM periodes WHERE id_classe = '". $temp["classes"]["classes"][$c_id]["id"] ."' ORDER BY num_periode";
                    $periode_query2 = mysqli_query($mysqli, $sql_periode2);
                    $nb_periode = $periode_query2->num_rows + 1 ;
                    $i = "1";
                    while ($obj_period2 = $periode_query2->fetch_object()) {
                        $temp["classe"]["ver_periode"][$c_id][$i] = $obj_period2->verouiller;
                        $liste_ver_per[$i] .= $temp["classe"]["ver_periode"][$c_id][$i];
                        $i++;
                    }
                    $all_clos .= "O";
                    $all_open .= "N";
                    $all_clos_part .= "P";
                    $periode_query2->close();
                }
                $i = "1";
                while ($i < $nb_periode) {
                    if ($liste_ver_per[$i] == $all_clos)
                        // Toutes les classes sont closes
                        $temp["classe"]["ver_periode"]["all"][$i] = 0;
                    else if ($liste_ver_per[$i] == $all_clos_part)
                        // Toutes les classes sont partiellement closes
                        $temp["classe"]["ver_periode"]["all"][$i] = 1;
                    else if ($liste_ver_per[$i] == $all_open)
                        // Toutes les classes sont ouvertes
                        $temp["classe"]["ver_periode"]["all"][$i] = 3;
                    else if (substr_count($liste_ver_per[$i], "N") > 0)
                        // Au moins une classe est ouverte
                        $temp["classe"]["ver_periode"]["all"][$i] = 2;
                    else
                        $temp["classe"]["ver_periode"]["all"][$i] = -1;
                    $i++;
                }
                $periode_query->close();
            }
            
            if($get_eleves=='y') {
                // Elèves
                foreach ($temp["periodes"] as $key => $period) {
                    $temp["eleves"][$key]["list"] = array();
                    $temp["eleves"][$key]["users"] = array();

                    $temp["eleves"][$key]["telle_classe"] = array();
                    foreach($temp["classes"]["list"] as $tmp_id_classe) {
                        $temp["eleves"][$key]["telle_classe"][$tmp_id_classe] = array();
                    }
                    
                    $sql_eleves = "SELECT distinct j.login, e.nom, e.prenom, e.ele_id, e.elenoet, e.sexe, e.date_sortie FROM eleves e, j_eleves_groupes j WHERE (" .
                                                "e.login = j.login and j.id_groupe = '" . $_id_groupe . "' and j.periode = '" . $period["num_periode"] . "') " .
                                                "ORDER BY e.nom, e.prenom";
                    $get_eleves = mysqli_query($mysqli, $sql_eleves);
                    $nb = $get_eleves->num_rows;
                    while ($obj_eleve = $get_eleves->fetch_object()) {
                        $e_login = $obj_eleve->login;
                        $e_nom = $obj_eleve->nom;
                        $e_prenom = $obj_eleve->prenom;
                        $e_sexe = $obj_eleve->sexe;
                        $e_date_sortie = $obj_eleve->date_sortie;
                        $sql="SELECT id_classe FROM j_eleves_classes WHERE (login = '" . $e_login . "' and periode = '" . $key . "')";
                        $res_classe_eleve_periode =  mysqli_query($mysqli, $sql);
                        if($res_classe_eleve_periode->num_rows > 0) {
                            $row_classe = $res_classe_eleve_periode->fetch_row();
                            $e_classe = $row_classe[0];
                            $temp["eleves"][$key]["telle_classe"][$e_classe][] = $e_login;
                        } else {
                            $e_classe=-1;
                        }
                        $e_sconet_id = $obj_eleve->ele_id;
                        $e_elenoet = $obj_eleve->elenoet;
                        $temp["eleves"][$key]["list"][] = $e_login;
                        $temp["eleves"][$key]["users"][$e_login] = array("login" => $e_login, "nom" => $e_nom, "prenom" => $e_prenom, "id_classe" => $e_classe, "classe" => $e_classe, "sconet_id" => $e_sconet_id, "elenoet" => $e_elenoet, "sexe" => $e_sexe, "date_sortie" => $e_date_sortie);
                        $res_classe_eleve_periode->close();
                    }
                    
                    $get_eleves->close();
                }
                
                $sql_all_eleves = "SELECT distinct j.login, e.nom, e.prenom FROM eleves e, j_eleves_groupes j WHERE (" .
                                                "e.login = j.login and j.id_groupe = '" . $_id_groupe . "') " .
                                                "ORDER BY e.nom, e.prenom";
                $get_all_eleves = mysqli_query($mysqli, $sql_all_eleves);
                $nb = $get_all_eleves->num_rows;
                $temp["eleves"]["all"]["list"] = array();
                while ($obj_all_eleve = $get_all_eleves->fetch_object()) {
                    $e_login = $obj_all_eleve->login;
                    $temp["eleves"]["all"]["list"][] = $e_login;
                    foreach ($temp["periodes"] as $key => $period) {
                        if (in_array($e_login, $temp["eleves"][$key]["list"])) {
                            $temp["eleves"]["all"]["users"][$e_login] = $temp["eleves"][$key]["users"][$e_login];
                            break 1;
                        }
                    }
                }
                $get_all_eleves->close();
            }
        }
        $resultat->close();
    return $temp;
}

/** Enregistre un nouveau groupe
 *
 * @param text $_name nom du groupe
 * @param text $_description description du groupe 
 * @param text $_matiere matière enseignée
 * @param array $_classes Tableau des Id des classes concernées
 * @param int $_categorie En mettant $_categorie=-1 on récupère la catégorie par défaut associées à la matière
 * @return int| mysql_error Le numéro du groupe ou l'erreur lors de la création
 * @see get_period_number()
 */
function create_group($_name, $_description, $_matiere, $_classes, $_categorie = 1) {

    $_insert = mysqli_query($GLOBALS["mysqli"], "INSERT INTO groupes SET name = '" . addslashes($_name) . "', description = '" . addslashes($_description) . "'");
    $_group_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

    if (!$_insert) {
        $error = mysqli_error($GLOBALS["mysqli"]);
    }
    if (!is_numeric($_categorie)) {
        $_categorie = 1;
    }
	if($_categorie!='0') {
		// On vérifie que la catégorie existe
		$temptemp = null;
		$temptemp = mysqli_query($GLOBALS["mysqli"], "SELECT count(id) FROM matieres_categories WHERE id = '" . $_categorie . "'");
		if (!$temptemp) {
			$test_cat = "0";
		} else {
			$test_cat = old_mysql_result($temptemp, 0);
		}

		if ($test_cat == "0") {
			// La catégorie n'existe pas : on met la catégorie par défaut
			//$_categorie = 1;
			// La catégorie 1 peut ne pas exister
			$sql="SELECT m.categorie_id FROM matieres m, matieres_categories mc WHERE mc.id=m.categorie_id AND m.matiere='".$_matiere."';";
			$res_cat=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_cat)>0) {
				$_categorie=old_mysql_result($res_cat, 0);
			}
			else {
				$sql="SELECT 1=1 FROM matieres_categories mc WHERE mc.id='1';";
				$test_cat=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test_cat)>0) {
					$_categorie = 1;
				}
				else {
					$sql="SELECT id FROM matieres_categories mc ORDER BY priority;";
					$res_cat=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_cat)>0) {
						$_categorie=old_mysql_result($res_cat, 0);
					}
					else {
						$_categorie = 0;
					}
				}
			}
		}
	}
    $_insert2 = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_groupes_matieres SET id_groupe = '" . $_group_id . "', id_matiere = '" . $_matiere . "'");
    $_priorite = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT priority FROM matieres WHERE matiere = '" . $_matiere . "'"), 0);
    if (count($_classes) > 0) {
        $test_per = get_period_number($_classes[0]);
    }
    foreach ($_classes as $_id_classe) {
        // On vérifie que les classes ont bien le même nombre de période
        if (get_period_number($_id_classe) == $test_per) {
            $sql = "insert into j_groupes_classes set id_groupe = '" . $_group_id . "', id_classe = '" . $_id_classe . "', priorite = '" . $_priorite . "';";
            $_res = mysqli_query($GLOBALS["mysqli"], $sql);

			if (!$_res) {
				echo "<span style='color:red;'>Bug&nbsp;:</span> ".$sql."'<br />";
				echo "<span style='color:red;'>".mysqli_error($GLOBALS["mysqli"])."</span>";
				echo "<br />";
			}
			$sql="update j_groupes_classes set categorie_id = '".$_categorie."'WHERE (id_groupe = '" . $_group_id . "' and id_classe = '" . $_id_classe . "');";
			$res = mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$_res) {
				echo "<span style='color:red;'>Bug&nbsp;:</span> ".$sql."'<br />";
				echo "<span style='color:red;'>".mysqli_error($GLOBALS["mysqli"])."</span>";
				echo "<br />";
			}

        }
    }

    if (!$_insert) {
        return $error;
    } else {
        return $_group_id;
    }
}

/**
 * Met à jour la table de jointure groupes - classes
 * 
 * @param int $_id_groupe Id du groupe
 * @param int $_id_classe Id de la classe
 * @param type $_options tableau des options à mettre à jour
 * @return bool TRUE si la mise à jour c'est bien passée, FALSE sinon
 */
function update_group_class_options($_id_groupe, $_id_classe, $_options) {
    if (!is_numeric($_id_groupe)) {$_id_groupe = 0;}
    if (!is_numeric($_id_classe)) {$_id_classe = 0;}
    if (!is_numeric($_options["coef"])) {$_options["coef"] = 0;}
    if (!is_numeric($_options["priorite"])) {$_options["priorite"] = 0;}

    if(!isset($_options["mode_moy"])) {$_options["mode_moy"]="-";}
    elseif (($_options["mode_moy"]!='sup10')&&($_options["mode_moy"]!='bonus')) {$_options["mode_moy"]="-";}

    if ((!isset($_options["saisie_ects"]))||(!in_array($_options["saisie_ects"],array("0","1")))) {$_options["saisie_ects"] = 0;}
    if ((!isset($_options["valeur_ects"]))||(!is_numeric($_options["valeur_ects"]))) {$_options["valeur_ects"] = 0;}
    if (!is_numeric($_options["coef"])) {$_options["coef"] = 0;}
    if (!is_numeric($_options["categorie_id"])) {$_options["categorie_id"] = 0;}
    $sql="update j_groupes_classes set priorite = '" . $_options["priorite"] . "',
                                                     coef = '" . $_options["coef"] . "',
                                                     mode_moy='".$_options["mode_moy"]."',
                                                     saisie_ects = '" . $_options['saisie_ects']."',
                                                     valeur_ects = '" . $_options['valeur_ects']."',
                                                     categorie_id = '" . $_options["categorie_id"] ."' ".
                        "WHERE (id_groupe = '" . $_id_groupe . "' and id_classe = '" . $_id_classe . "');";
    $res = mysqli_query($GLOBALS["mysqli"], $sql);
    if (!$res) {
        return FALSE;
    } else {
        return TRUE;
    }

}

/** Met à jour un groupe dans la base
 *
 * 
 * 
 * @param int $_id_groupe Id du groupe
 * @param text $_name Le nom court
 * @param text $_description Description du groupe
 * @param text $_matiere l'id de la matière dans j_groupes_matieres
 * @param array $_classes tableau des Id des classes
 * @param type $_professeurs tableau des logins des enseignants
 * @param type $_eleves tableau des logins des élèves concernés
 * @return bool TRUE si tout c'est bien passé 
 * @see get_group()
 * @see get_period_number()
 * @see test_before_eleve_removal()
 */
function update_group($_id_groupe, $_name, $_description, $_matiere, $_classes, $_professeurs, $_eleves) {
    global $msg;
    $former_groupe = get_group($_id_groupe);
    $errors = false;
    if ($_name != $former_groupe["name"] OR $_description != $former_groupe["description"]) {
        $sql="UPDATE groupes SET name = '" . $_name . "', description = '" . $_description . "' WHERE id = '" . $_id_groupe . "';";
        $update = mysqli_query($GLOBALS["mysqli"], $sql);
        if (!$update) {
			$errors = true;
			$msg.="ERREUR sur $sql<br />";
		}
    }

    if ($_matiere != $former_groupe["matiere"]["matiere"]) {
        $sql="UPDATE j_groupes_matieres SET id_matiere = '" . $_matiere . "' WHERE id_groupe = '" . $_id_groupe . "';";
        $update2=mysqli_query($GLOBALS["mysqli"], $sql);
        if (!$update2) {
			$errors = true;
			$msg.="ERREUR sur $sql<br />";
		}
    }

    // Mise à jour des classes

    $deleted_classes = array_diff($former_groupe["classes"]["list"], $_classes);
    $new_classes = array_diff($_classes, $former_groupe["classes"]["list"]);

    // Avant de modifier quoi que ce soit, il faut s'assurer que les nouvelles classes ont le même nombre de périodes
    $check_periods = get_period_number($former_groupe["classes"]["list"][0]);
    $per_error = false;
    foreach ($new_classes as $id_classe) {
        if (get_period_number($id_classe)!= $check_periods) {
            $per_error = true;
			$msg.="ERREUR: get_period_number($id_classe)=".get_period_number($id_classe)." mais \$check_periods=$check_periods<br />";
        }
    }
    if (!$per_error) {
        $mat_priority = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT priority FROM matieres WHERE matiere = '".$_matiere ."'"), 0);
        foreach ($new_classes as $id_classe) {
            $sql="INSERT into j_groupes_classes SET id_groupe = '" . $_id_groupe . "', id_classe = '" . $id_classe . "', priorite = '".$mat_priority."';";
            $res = mysqli_query($GLOBALS["mysqli"], $sql);
            if (!$res) {
				$errors = true;
				$msg.="ERREUR sur $sql<br />";
			}
        }

        foreach ($deleted_classes as $id_classe) {
            $sql="DELETE FROM j_groupes_classes WHERE (id_groupe = '" . $_id_groupe . "' AND id_classe = '" . $id_classe . "');";
            $res = mysqli_query($GLOBALS["mysqli"], $sql);
            if (!$res) {
				$errors = true;
				$msg.="ERREUR sur $sql<br />";
			}
        }
    } else {
        $errors = true;
    }

    // Mise à jour des professeurs

    $deleted_profs = array_diff((array)$former_groupe["profs"]["list"], (array)$_professeurs);
    $new_profs = array_diff((array)$_professeurs, (array)$former_groupe["profs"]["list"]);

    foreach ($new_profs as $p_login) {
        $sql="insert into j_groupes_professeurs set id_groupe = '" . $_id_groupe . "', login = '" . $p_login . "';";
        $res=mysqli_query($GLOBALS["mysqli"], $sql);
		if (!$res) {
			$errors = true;
			$msg.="ERREUR sur $sql<br />";
		}
    }

    foreach ($deleted_profs as $p_login) {
        $sql="delete from j_groupes_professeurs where (id_groupe = '" . $_id_groupe . "' and login = '" . $p_login . "');";
        $res = mysqli_query($GLOBALS["mysqli"], $sql);
		if (!$res) {
			$errors = true;
			$msg.="ERREUR sur $sql<br />";
		}
    }


    // Mise à jour des élèves

    // Cette première étape est juste pour les situations où l'on a envoyé un tableau vite comme argument
    // signalant que l'on ne veut pas manipuler les élèves
    if (count($_eleves) != 0) {
        foreach($former_groupe["periodes"] as $period) {
            $deleted_eleves = array_diff((array)$former_groupe["eleves"][$period["num_periode"]]["list"], (array)$_eleves[$period["num_periode"]]);
            $new_eleves = array_diff((array)$_eleves[$period["num_periode"]], (array)$former_groupe["eleves"][$period["num_periode"]]["list"]);

            foreach ($new_eleves as $e_login) {
                $sql="insert into j_eleves_groupes set id_groupe = '" . $_id_groupe . "', login = '" . $e_login . "', periode = '" . $period["num_periode"] . "';";
                $res = mysqli_query($GLOBALS["mysqli"], $sql);
				if (!$res) {
					$errors = true;
					$msg.="ERREUR sur $sql<br />";
				}
            }

            foreach ($deleted_eleves as $e_login) {
                if (test_before_eleve_removal($e_login, $_id_groupe, $period["num_periode"])) {
                    $sql="delete from j_eleves_groupes where (id_groupe = '" . $_id_groupe . "' and login = '" . $e_login . "' and periode = '" . $period["num_periode"] . "');";
                    $res = mysqli_query($GLOBALS["mysqli"], $sql);
					if (!$res) {
						$errors = true;
						$msg.="ERREUR sur $sql<br />";
					}
                } else {
                    $msg .= "Erreur lors de la suppression de l'élève ayant le login '" . $e_login . "', pour la période '" . $period["num_periode"] . " (des notes ou appréciations existent).<br/>";
                }
            }
        }
    }

    if ($errors) {
        return FALSE;
    } else {
        return TRUE;
    }
}

/** Vérifie si on peut supprimer un groupe 
 * (possible si matieres_notes et matieres_appreciations ne contiennent pas d'entrées pour ce groupe)
 *
 * @param type $_id_groupe
 * @return bool TRUE si tout on peut supprimer, FALSE sinon
 */
function test_before_group_deletion($_id_groupe) {

    $test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM matieres_notes WHERE id_groupe = '" . $_id_groupe . "'"), 0);
    $test2 = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM matieres_appreciations WHERE id_groupe = '" . $_id_groupe . "'"), 0);

    if ($test == 0 and $test2 == 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Vérifie si un élève a des notes ou des appréciations
 *
 * @param text $_login Login de l'élève
 * @param int $_id_groupe Id du groupe
 * @param int $_periode numéro de la période
 * @return bool TRUE si on peut effacer 
 */
function test_before_eleve_removal($_login, $_id_groupe, $_periode) {
    $test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "select count(*) FROM matieres_notes WHERE (login = '" . $_login . "' AND id_groupe = '" . $_id_groupe . "' AND periode = '" . $_periode . "')"), 0);
    $test2 = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "select count(*) FROM matieres_appreciations WHERE (login = '" . $_login . "' AND id_groupe = '" . $_id_groupe . "' AND periode = '" . $_periode . "')"), 0);

    if ($test == 0 and $test2 == 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Supprime un groupe de la base (et les données associées)
 *
 * @param int $_id_groupe Id du groupe
 * @return bool|text  TRUE si tout c'est bien passé, un message d'erreur sinon
 */
function delete_group($_id_groupe) {
    $errors = null;
    $del1 = mysqli_query($GLOBALS["mysqli"], "DELETE from j_groupes_matieres WHERE id_groupe = '" . $_id_groupe . "'");
    if (!$del1) $errors .= "Erreur lors de la suppression du lien groupe-matiere.<br/>";
    $del2 = mysqli_query($GLOBALS["mysqli"], "DELETE from j_groupes_professeurs WHERE id_groupe = '" . $_id_groupe . "'");
    if (!$del2) $errors .= "Erreur lors de la suppression du lien groupe-professeurs.<br/>";
    $del3 = mysqli_query($GLOBALS["mysqli"], "DELETE from j_eleves_groupes WHERE id_groupe = '" . $_id_groupe . "'");
    if (!$del3) $errors .= "Erreur lors de la suppression du lien groupe-eleves.<br/>";
    $del4 = mysqli_query($GLOBALS["mysqli"], "DELETE from j_groupes_classes WHERE id_groupe = '" . $_id_groupe . "'");
    if (!$del4) $errors .= "Erreur lors de la suppression du lien groupe-classes.<br/>";
    $del6 = mysqli_query($GLOBALS["mysqli"], "DELETE from cn_cahier_notes, cn_conteneurs, cn_devoirs, cn_notes_conteneurs, cn_notes_devoirs WHERE (" .
            "cn_cahier_notes.id_groupe = '" . $_id_groupe . "' AND " .
            "cn_conteneurs.id_racine = cn_cahier_notes.id_cahier_notes AND " .
            "cn_notes_conteneurs.id_conteneur = cn_conteneurs.id AND " .
            "(cn_devoirs.id_racine = cn_cahier_notes.id_cahier_notes OR cn_devoirs.id_conteneur = cn_conteneurs.id) AND " .
            "cn_notes_devoirs.id_devoir = cn_devoirs.id)");
    if (!$del6) $errors .= "Erreur lors de la suppression des données relatives au carnet de notes lié au groupe.<br/>";
    $text_ct = sql_query1("SELECT count(id_groupe) from ct_entry WHERE (ct_entry.id_groupe = '" . $_id_groupe . "'");
    if ($text_ct > 0) $errors .= "Attention un cahier de textes lié au groupe supprimé est maintenant \"orphelin\". Rendez-vous dans le module \"cahier de textes\" pour régler le problème.<br/>";
    $del7 = mysqli_query($GLOBALS["mysqli"], "DELETE from j_signalement WHERE id_groupe = '" . $_id_groupe . "'");
    if (!$del7) $errors .= "Erreur lors de la suppression des signalements associés au groupe.<br/>";
    $del8 = mysqli_query($GLOBALS["mysqli"], "DELETE from j_groupes_visibilite WHERE id_groupe = '" . $_id_groupe . "'");
    if (!$del8) $errors .= "Erreur lors de la suppression des enregistrement de visibilité ou non du groupe.<br/>";
    $del9 = mysqli_query($GLOBALS["mysqli"], "DELETE from acces_cdt_groupes WHERE id_groupe = '" . $_id_groupe . "'");
    if (!$del9) $errors .= "Erreur lors de la suppression des accès CDT pour ce groupe.<br/>";

    $del10 = mysqli_query($GLOBALS["mysqli"], "DELETE from edt_cours WHERE id_groupe = '" . $_id_groupe . "'");
    if (!$del10) $errors .= "Erreur lors de la suppression des cours dans l'EDT pour ce groupe.<br/>";

    $del5 = mysqli_query($GLOBALS["mysqli"], "DELETE from groupes WHERE id = '" . $_id_groupe . "'");
    if (!$del5) $errors .= "Erreur lors de la suppression du groupe.<br/>";

    if (!empty($errors)) {
        return $errors;
    } else {
        return TRUE;
    }
}

/**
 * Renvoie un réglage pour un élève
 *
 * @param text $_login Login de l'élève
 * @param int $_id_groupe groupe de l'élève
 * @param text $_setting_name le réglage recherché
 * @return text la valeur du réglage
 */
function get_eleve_groupe_setting($_login, $_id_groupe, $_setting_name) {
    $value = null;
    $select = mysqli_query($GLOBALS["mysqli"], "select value from eleves_groupes_settings WHERE (" .
                        "login = '" . $_login . "' and ".
                        "id_groupe = '" . $_id_groupe . "' and ".
                        "name = '" . $_setting_name . "'".
                        ")");

    $nb = mysqli_num_rows($select);
    if ($nb == "0") {
        $value = false;
    } else {
        $value = array();
        for ($i=0;$i<$nb;$i++) {
            $value[] = old_mysql_result($select, $i, "value");
        }
    }

    return $value;
}

/**
 * Met à jour la table eleves_groupes_settings
 *
 * 
 * @param text $_login Le login de l'élève
 * @param int $_id_groupe Le groupe concerné
 * @param text $_setting_name Le réglage à modifier
 * @param array $_setting_value Tableau des valeurs pour le réglage
 * @return bool TRUE si tout se passe bien 
 * @see get_eleve_groupe_setting()
 */
function set_eleve_groupe_setting($_login, $_id_groupe, $_setting_name, $_setting_value) {

    $test = get_eleve_groupe_setting($_login, $_id_groupe, $_setting_name);
    $queries = array();

    if ($test) {
        $queries[] = "DELETE FROM eleves_groupes_settings WHERE (login = '" . $_login . "' AND id_groupe = '" . $_id_groupe . "' AND name = '" . $_setting_name . "')";
    }

    foreach($_setting_value as $value) {
        if ($value != "") $queries[] = "INSERT INTO eleves_groupes_settings SET login = '" . $_login . "', id_groupe = '" . $_id_groupe . "', name = '" . $_setting_name . "', value = '" . $value ."'";
    }

    foreach($queries as $query) {
        $res = mysqli_query($GLOBALS["mysqli"], $query);
    }
	if ($_setting_name == "coef") {
		$req = mysqli_query($GLOBALS["mysqli"], "UPDATE groupes SET recalcul_rang = 'y' WHERE (id='".$_id_groupe."')");
	}

    return true;
}

/** Vérifie qu'un enseignant fait bien partie d'un groupe
 *
 * @param text $_login Login de l'enseignant
 * @param type $_id_groupe Id du groupe
 * @return bool TRUE si l'enseignant fait parti du groupe
 */
function check_prof_groupe($_login, $_id_groupe) {
    if(empty($_login) || empty($_id_groupe)) {return false;}
    $call_prof = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_groupes_professeurs WHERE (id_groupe='".$_id_groupe."' and login='" . $_login . "')");
    $nb = mysqli_num_rows($call_prof);

    if ($nb == 0) {
        return FALSE;
    } else {
        return TRUE;
    }
}

/**
 * Verifie si un groupe appartient bien à la personne connectée
 *
 * 
 * @param integer $id_groupe identifiant du groupe
 * @return int 1 si le prof enseigne dans le groupe, 0 sinon
 */
function verif_groupe_appartient_prof($id_groupe) {
    $test = mysqli_query($GLOBALS["mysqli"], "SELECT 1=1 FROM j_groupes_professeurs WHERE (id_groupe='$id_groupe' and login= '".$_SESSION['login']."');");
    if (mysqli_num_rows($test) == 0) {
        return 0;
    } else {
        return 1;
    }
}

/**
 * Construit un tableau des classes et matières de l'utilisateur connecté (professeur)
 * 
 * Recherche les classes de l'enseignant puis pour chacune recherche la matiere, le nom court et le nom long.
 * 
 * La correspondance se fait sur l'indice du tableau
 * 
 * $tab_class_mat['id_c']  -> classes.id
 * $tab_class_mat['id_m']  -> matieres.matiere
 * $tab_class_mat['nom_c'] -> classes.classe
 * $tab_class_mat['nom_m'] -> classes.nom_complet
 *
 * @return array Liste des classes et matières de ce professeur
 */
function make_tables_of_classes_matieres () {
  $tab_class_mat = array();
  $appel_classes = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
  $nb_classes = mysqli_num_rows($appel_classes);
  $i = 0;
  while($i < $nb_classes){
    $id_classe = old_mysql_result($appel_classes, $i, "id");
    $appel_mat = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT m.matiere, m.nom_complet " .
            "FROM matieres m, j_groupes_matieres jgm, j_groupes_classes jgc, j_groupes_professeurs jgp" .
            " WHERE ( " .
            "m.matiere = jgm.id_matiere and " .
            "jgm.id_groupe = jgp.id_groupe and " .
            "jgp.login = '" . $_SESSION['login'] . "' and " .
            "jgp.id_groupe = jgc.id_groupe and " .
            "jgc.id_classe='$id_classe')" .
            " ORDER BY m.nom_complet");
    $nb_mat = mysqli_num_rows($appel_mat);
    $j = 0;
    while($j < $nb_mat){
      $tab_class_mat['id_c'][] = $id_classe;
      $tab_class_mat['id_m'][] = old_mysql_result($appel_mat, $j, "matiere");
      $tab_class_mat['nom_c'][] = old_mysql_result($appel_classes, $i, "classe");
      $tab_class_mat['nom_m'][] = old_mysql_result($appel_mat, $j, "nom_complet");
      $j++;
    }
    $i++;
  }
  return $tab_class_mat;
}

/**
 * Construit un tableau des classes de l'utilisateur connecté (professeur)
 *
 * @return array Liste des classes (id_classe)
 */
function make_tables_of_classes () {
  $tab_class = array();
  $appel_classes = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
  $nb_classes = mysqli_num_rows($appel_classes);
  $i = 0;
  $nb=0;
  while($i < $nb_classes){
    $id_classe = old_mysql_result($appel_classes, $i, "id");
    $test_prof_classe = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (" .
            "jgc.id_classe='$id_classe' and " .
            "jgc.id_groupe = jgp.id_groupe and " .
            "jgp.login='".$_SESSION['login']."')");
    $test = mysqli_num_rows($test_prof_classe);
    if ($test != 0) {
        $tab_class[$nb] = $id_classe;
        $nb++;
    }
    $i++;
  }
  return $tab_class;
}



/**
 * Verifie si un élève appartient à un groupe
 *
 * @deprecated La requête SQL n'est plus possible
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
        $test = mysqli_query($GLOBALS["mysqli"], "select id_eleve from groupe_eleve where (id_eleve='$id_eleve' and id_groupe = '$id_groupe')");
        if (mysqli_num_rows($test) == 0) {
            return 0;
        } else {
            return 1;
        }
    } else {
        // On verifie l'appartenance de id_eleve à un groupe quelconque du professeur connecté
        $test = mysqli_query($GLOBALS["mysqli"], "select id_eleve from groupe_eleve ge, groupes g where (ge.id_eleve='$id_eleve' and ge.id_groupe = g.id and g.login_user='".$_SESSION['login']."')");
        if (mysqli_num_rows($test) == 0) {
            return 0;
        } else {
            return 1;
        }

    }
}

/**
 * Construit un tableau des groupes de l'utilisateur connecté
 *
 * @deprecated La requête SQL n'est plus possible
 * @return array
 */
function make_tables_of_groupes() {
    $tab_groupes = array();
    $test = mysqli_query($GLOBALS["mysqli"], "select id, nom_court, nom_complet from groupes where login_user = '".$_SESSION['login']."'");
    $nb_test = mysqli_num_rows($test);
    $i = 0;
    while ($i < $nb_test) {
      $tab_groupes['id'][$i] = old_mysql_result($test, $i, "id");
      $tab_groupes['nom'][$i] = old_mysql_result($test, $i, "nom_court");
      $tab_groupes['nom_complet'][$i] = old_mysql_result($test, $i, "nom_complet");
      $i++;
    }
      return $tab_groupes;
}

/** Fonction destinée à retourner les classes associées à un id_groupe
 *
 * @param string $login_eleve Login de l'élève
 *
 * @return array Tableau d'indice num_periode
 */

function get_classes_from_id_groupe($id_groupe) {
	$tab=array();

	$tab['classlist_string']="";

	$sql="SELECT DISTINCT id, classe, nom_complet FROM classes c, j_groupes_classes jgc WHERE jgc.id_classe=c.id AND jgc.id_groupe='$id_groupe' ORDER BY c.classe, c.nom_complet;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			if($cpt>0) {$tab['classlist_string'].=", ";}
			$tab['classlist_string'].=$lig->classe;
			if(($lig->nom_complet!='')&&($lig->nom_complet!=$lig->classe)) {
				$tab['classlist_string'].=" (".$lig->nom_complet.")";
			}
			$tab[$cpt]['id_classe']=$lig->id;
			$tab[$cpt]['classe']=$lig->classe;
			$tab[$cpt]['nom_complet']=$lig->nom_complet;
			$cpt++;
		}
	}

	return $tab;
}

/** Renvoie un tableau des groupes associés à une matière
 * 
 * Chaque ligne contient les retours de get_group() d'un des groupes associés à la matière
 *
 * @param text $_matiere L'identifiant de matière
 * @param array $tab_champs Les champs qu'on veut récupérer avec get_group()
 * @return array Le tableau des groupes
 * @see get_group()
 */
function get_groups_for_matiere($_matiere,$mode=NULL,$tab_champs=array()) {
	global $mysqli;
	$requete_sql = "SELECT DISTINCT jgm.id_groupe
					FROM j_groupes_matieres jgm, j_groupes_classes jgc, classes c, groupes g
					WHERE (" .
					"jgm.id_matiere='".$_matiere."'
					AND jgm.id_groupe=g.id
					AND jgm.id_groupe=jgc.id_groupe
					AND jgc.id_classe=c.id) ".
					"ORDER BY c.classe, g.name, g.description;" ;

	$resultat = mysqli_query($mysqli, $requete_sql);  
	$nb = $resultat->num_rows;
	$groups = array();
	while ($obj = $resultat->fetch_object()) {
		$_id_groupe = $obj->id_groupe;
		if(count($tab_champs)>0) {
			$groups[] = get_group($_id_groupe,$tab_champs);
		} else {
			$groups[] = get_group($_id_groupe);
		}
	}

	return $groups;
}

/** Renvoie un tableau des élèves associés à une groupe
 * 
 * @param integer $id_groupe L'identifiant du groupe
 * @param integer $periode Le numéro de période
 *
 * @return array Le tableau des élèves
 */
function get_eleves_from_groupe($id_groupe,$periode="") {
	global $mysqli;

	$tab=array();
	if($periode=="") {
		$sql="SELECT DISTINCT j.login, 
						e.nom, 
						e.prenom, 
						e.ele_id, 
						e.elenoet, 
						e.sexe, 
						e.no_gep 
					FROM eleves e, 
						j_eleves_groupes j 
					WHERE (e.login=j.login AND 
						j.id_groupe='".$id_groupe."')
					ORDER BY e.nom, e.prenom;";
	}
	else {
		$sql="SELECT DISTINCT j.login, 
						e.nom, 
						e.prenom, 
						e.ele_id, 
						e.elenoet, 
						e.sexe, 
						e.no_gep
					FROM eleves e, 
						j_eleves_groupes j 
					WHERE (e.login=j.login AND 
						j.id_groupe='".$id_groupe."' AND 
						j.periode='".$periode."') 
					ORDER BY e.nom, e.prenom;";
	}
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tab['list'][]=$lig->login;

		$id_classe="";
		if($periode=="") {
			$sql="SELECT id_classe FROM j_eleves_classes WHERE (login = '" . $lig->login . "') ORDER BY periode DESC;";
		}
		else {
			$sql="SELECT id_classe FROM j_eleves_classes WHERE (login = '" . $lig->login . "' and periode = '" . $periode . "');";
		}
		//echo "$sql<br />";
		$res_classe_eleve =  mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_classe_eleve)>0) {
			$lig_class_tmp=mysqli_fetch_object($res_classe_eleve);
			$id_classe=$lig_class_tmp->id_classe;
		}

		$tab['users'][$lig->login]=array("login" => $lig->login, "nom" => $lig->nom, "prenom" => $lig->prenom, "classe" => $id_classe, "sconet_id" => $lig->ele_id, "elenoet" => $lig->elenoet, "sexe" => $lig->sexe, "no_gep" => $lig->no_gep);
	}

	return $tab;
}

?>
