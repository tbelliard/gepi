<?php
/** Outils complémentaires de gestion des AID
 * 
 * 
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Aid
 * @subpackage Initialisation
 *
*/


/**
 * Fonction vérifiant les droits d'accès au module selon l'identifiant
 *
 * $champ : si non vide, on vérifie le droit sur ce champ en particulier, si $champ='', on vérifie le droit de modifier la fiche projet
 * 
 * Cas particulier : $champ = 'eleves_profs'
 * Cette valeur permet de gérer le fait que n'apparaissent pas sur les fiches publiques :
 * - Les elèves responsables du projet,
 * - les professeurs responsables du projet,
 * - les élèves faisant partie du projet.
 * 
 * $_login : identifiant de la personne pour laquelle on vérifie les droits, 
 * si le login n'est pas précisé, on est dans l'interface publique
 * 
 * $mode : utilisé uniquement si $champ est non vide
 * - $mode = W -> l'utilisateur a-t-il accès en écriture ?
 * - Autres valeurs de W -> l'utilisateur a-t-il accès en lecture ?
 * 
 * @param type $_login Login à vérifier
 * @param type $aid_id identifiant de l'AID
 * @param type $indice_aid identifiant de la catégorie d'AID
 * @param type $champ champ à vérifier
 * @param type $mode mode recherché
 * @param type $annee
 * @return type 
 */
function VerifAccesFicheProjet($_login,$aid_id,$indice_aid,$champ,$mode,$annee='') {
 //$annee='' signifie qu'il s'agit de l'année courante
 if ($annee=='') {
    // Les outils complémetaires sont-ils activés ?
    $test_active = sql_query1("select indice_aid from aid_config WHERE outils_complementaires = 'y' and indice_aid='".$indice_aid."'");
    // Les outils complémenatires ne sont activés pour aucune AID, on renvoie FALSE
    if ($test_active == -1) {
        return FALSE;
        die();
    }

    // Si le champ n'est pas activé, on ne l'affiche pas !
    // Deux valeurs possibles :
    // 0 -> le champ n'est pas utilisé
    // 1 -> Le champ est utilisé
    if ($champ != "") {
        $statut_champ = sql_query1("select statut from droits_aid where id = '".$champ."'");
        if ($statut_champ == 0) {
            return FALSE;
            die();
        }
    }

    // Dans la suite,
    // Les outils complémentaires sont activés

    if ($_login!='') {
        $statut_login = sql_query1("select statut from utilisateurs where login='".$_login."' and etat='actif' ");
    } else {
        // si le login n'est pas précisé, on est dans l'interface publique
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

    // S'agit-il d'un utilisateurs ayant des droits sur l'ensemble des AID de la catégorie
    $test_droits_special = sql_query1("select count(id_utilisateur) from j_aidcateg_utilisateurs where indice_aid='".$indice_aid."' and id_utilisateur='".$_login."'");
    // Cas d'un élève
    if (($statut_login=="eleve")) {
        // s'il s'agit d'un élève, les élèves ont-ils accès en modification ?
        // Si l'utilisateur a des droits spéciaux, il peut modifier
        $CheckAccessEleve = sql_query1("select eleve_peut_modifier from aid where id = '".$aid_id."' and indice_aid = '".$indice_aid."'");
        if ($CheckAccessEleve != "y") {
            if ($champ == "") {return FALSE; die();}
        }
        // L'élève est-il responsable de cet AID ?
        $CheckAccessEleve2 = sql_query1("select count(login) from j_aid_eleves_resp WHERE (login='".$_SESSION['login']."' and indice_aid='".$indice_aid."' and id_aid='".$aid_id."')");
        if ($CheckAccessEleve2 == 0) {
             if ($champ == "") {return FALSE; die();}
        }
    }
    // Cas d'un professeur
    if (($statut_login=="professeur")) {

        // s'il s'agit d'un prof, les profs ont-ils accès en modification ?
        $CheckAccessProf = sql_query1("select prof_peut_modifier from aid where id = '".$aid_id."' and indice_aid = '".$indice_aid."'");
        if (($CheckAccessProf != "y") and ($test_droits_special==0) ) {
            if ($champ == "") {return FALSE; die();}
        }

        // Le professeur est-il responsable de cet AID ?
        $CheckAccessProf2 = sql_query1("select count(id_utilisateur) from j_aid_utilisateurs WHERE (id_utilisateur='".$_SESSION['login']."' and indice_aid='".$indice_aid."' and id_aid='".$aid_id."')");
        if (($CheckAccessProf2 == 0) and ($test_droits_special==0) ) {
            if ($champ == "") {return FALSE; die();}
        }
    }
    // Cas d'un CPE
    if (($statut_login=="cpe")) {
        // s'il s'agit d'un CPE, les cpe ont-ils accès en modification ?
        // Si l'utilisateur a des droits spéciaux, il peut modifier
        $CheckAccessCPE = sql_query1("select cpe_peut_modifier from aid where id = '".$aid_id."' and indice_aid = '".$indice_aid."'");
        if (($CheckAccessCPE != "y") and ($test_droits_special==0)) {
            if ($champ == "") {return FALSE; die();}
        }
    }
    // S'il s'agit d'un responsable, de la scolarité ou de secours, pas d'accès
    if (($statut_login=="responsable") or ($statut_login=="scolarite") or ($statut_login=="secours")) {
        return FALSE;
        die();
    }
    // Si le champ n'est pas précisé, c'est terminé
    // Si le champ est précisé, on regarde si l'utilisateur a les droits de modif de ce champ
    //
    if ($champ == "") {
        // Si $champ == "", cela signifie qu'on demande l'accès à une page privée de modif ou de visualisation
        if ($_login !="")
            return TRUE;
        else
            return FALSE;
    } else {
        // Le champ est précisé. On cherche à savoir si l'utilisateur a le droit de voir et/ou de modifier ce champ
        $CheckAccess = sql_query1("select ".$statut_login." from droits_aid where id = '".$champ."'");
        // $CheckAccess='V' -> possibilité de modifier et de voir le champ
        // $CheckAccess='F' -> possibilité de voir le champ mais pas de le modifier
        // $CheckAccess='-' -> Interdiction de voir et ou de modifier le champ
        if (($mode != 'W') and ($CheckAccess != '-'))
            return (TRUE);
        else if (($mode == 'W') and ($CheckAccess == 'V'))
            return (TRUE);
        else
            return (FALSE);

    }
  } else {
  // il s'agit de projets d'une année passée...
    // Les outils complémetaires sont-ils activés ?
    $test_active = sql_query1("select id from archivage_types_aid WHERE outils_complementaires = 'y' and id='".$indice_aid."'");
    // Les outils complémenatires ne sont activés pour aucune AID, on renvoie FALSE
    if ($test_active == -1) {
        return FALSE;
        die();
    }

    if ($_login!='') {
        $statut_login = sql_query1("select statut from utilisateurs where login='".$_login."' and etat='actif' ");
    } else {
        // si le login n'est pas précisé, on est dans l'interface publique
        $statut_login = "public";
    }

    if ($champ == 'eleves_profs') {
    # Cas particulier du champ eleves_profs : ce champ permet de gérer le fait que n'apparaissent pas sur les fiches publiques :
    # Les elèves responsables du projet,
    # les professeurs responsables du projet,
    # les élèves faisant partie du projet.
        if ($statut_login == "public")
            return FALSE;
        else
            return TRUE;
    } else if ($champ != "") {
    // Si le champ n'est pas activé, on ne l'affiche pas !
    // Deux valeurs possibles :
    // 0 -> le champ n'est pas utilisé
    // 1 -> Le champ est utilisé

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
    // Si $champ == "", cela signifie qu'on demande l'accès à une page privée de modif ou de visualisation
       return FALSE;
    // Si le champ est précisé, on regarde si l'utilisateur a les droits de modif de ce champ
    } else {
        // Le champ est précisé. On cherche à savoir si l'utilisateur a le droit de voir et/ou de modifier ce champ
        $CheckAccess = sql_query1("select ".$statut_login." from droits_aid where id = '".$champ."'");
        // $CheckAccess='V' -> possibilité de modifier et de voir le champ
        // $CheckAccess='F' -> possibilité de voir le champ mais pas de le modifier
        // $CheckAccess='-' -> Interdiction de voir et ou de modifier le champ
        if (($mode != 'W') and ($CheckAccess != '-'))
            return (TRUE);
        else if (($mode == 'W') and ($CheckAccess == 'V'))
            return (TRUE);
        else
            return (FALSE);
    }
  }
}

/**
 * vérifie si un Aid est actif
 * 
 * @param int $indice_aid Indice de l'aid
 * @param string $aid_id Id de l'aid
 * @param string $annee l'année de recherche (année courante si vide)
 * @return boolean 
 */
function VerifAidIsActive($indice_aid,$aid_id,$annee='') {
    if ($annee=='')
      $test_active = sql_query1("SELECT indice_aid FROM aid_config WHERE outils_complementaires = 'y' and indice_aid='".$indice_aid."'");
    else
      $test_active = sql_query1("SELECT id FROM archivage_types_aid WHERE outils_complementaires = 'y' and id='".$indice_aid."'");
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

/**
 * renvoie le libellé du champ
 * 
 * @param string $champ Id du champ à tester
 * @return string Le libellé
 */
function LibelleChampAid($champ) {
    $nom = sql_query1("select description from droits_aid where id = '".$champ."'");
    return $nom;
}

function fdebug_aid($texte) {
	global $gepiPath;
	$debug=getSettingValue("DEBUG_acces_aid");
	//$debug=1;
	if($debug==1) {
		//$dirname="$gepiPath/backup/".getSettingValue("backup_directory");
		$dirname="../backup/".getSettingValue("backup_directory");
		if(file_exists($dirname)) {
			$f=fopen($dirname."/DEBUG_acces_aid.txt", "a+");
			fwrite($f, strftime("%Y-%m-%d %H:%M:%S")." : ".$texte."\n");
			fclose($f);
		}
	}
}

/**
 * Calcule le niveau de gestion des AIDs
 * 
 * - 0 : aucun droit
 * - 1 : peut uniquement ajouter / supprimer des élèves
 * - 2 : (pas encore implémenté) peut uniquement ajouter / supprimer des élèves et des professeurs responsables
 * - 3 : ...
 * - 10 : Peut tout faire
 *
 * @param string $_login Login de l'utilisateur
 * @param int $indice_aid Indice de l'aid
 * @param string $aid_id Id de l'aid
 * @return int le niveau de gestion
 */
function NiveauGestionAid($_login,$_indice_aid,$_id_aid="") {
    if ($_SESSION['statut'] == "administrateur") {
        // Pas génial: on ne renvoie pas le niveau de $_login, mais celui de l'administrateur si NiveauGestionAid() n'est pas appelée pour $_SESSION['login']
        fdebug_aid("Acces statut administrateur : $_login, $_indice_aid, $_id_aid\nRetour 10");
        return 10;
        die();
    }
    if (getSettingValue("active_mod_gest_aid")=="y") {
        fdebug_aid("Acces : $_login, $_indice_aid, $_id_aid");

      // l'id de l'aid n'est pas défini : on regarde si l'utilisateur est gestionnaire d'au moins une aid dans la catégorie
      if ($_id_aid == "") {
        fdebug_aid("aid_id est non defini");

        $sql="SELECT count(id_utilisateur) FROM j_aid_utilisateurs_gest WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_indice_aid."')";
        $test1 = sql_query1($sql);
        fdebug_aid("$sql\ntest1=$test1");

        $sql="SELECT count(id_utilisateur) FROM j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_indice_aid."')";
        $test2 = sql_query1($sql);
        fdebug_aid("$sql\ntest2=$test2");

        if ($test2 >= 1) {
            fdebug_aid("Retour 5");
            return 5;
        } else if ($test1 >= 1) {
            fdebug_aid("Retour 1");
            return 1;
        } else
          fdebug_aid("Retour 0");
          return 0;

      } else {

        fdebug_aid("Acces : $_login, $_indice_aid, $_id_aid");

      // l'id de l'aid est défini : on regarde si l'utilisateur est gestionnaire de cette aid
       fdebug_aid("aid_id est defini : $_id_aid");

       $sql="SELECT count(id_utilisateur) FROM j_aid_utilisateurs_gest WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_indice_aid."' and id_aid = '".$_id_aid."')";
        $test1 = sql_query1($sql);
        fdebug_aid("$sql\ntest1=$test1");

        $sql="SELECT count(id_utilisateur) FROM j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_indice_aid."')";
        $test2 = sql_query1($sql);
        fdebug_aid("$sql\ntest2=$test2");

        if ($test2 >= 1) {
            fdebug_aid("Retour 5");
            return 5;
        } else if ($test1 >= 1) {
            fdebug_aid("Retour 1");
            return 1;
        } else
          fdebug_aid("Retour 0");
          return 0;
      }
    } else {
      fdebug_aid("Retour 0");
      return 0;
    }
}

/**
 * Vérifie si un utilisateurs à des droits de suppression sur un Aid
 * 
 * @param string $_login Login de l'utilisateur
 * @param string $_action Action à tester
 * @param string $_cible1 Non utilisé mais obligatoire
 * @param int $_cible2 id_aid
 * @param int $_cible3 indice_aid
 * @return bool TRUE si l'utilisateur a les droits
 * @see getSettingValue()
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

function get_tab_aid($id_aid, $order_by_ele="",$tab_champs=array('all')) {
	$tab_aid=array();

	$get_classes='n';
	$get_eleves='n';
	$get_profs='n';
	if(in_array('all',$tab_champs)) {
		$get_classes='y';
		$get_eleves='y';
		$get_profs='y';
	}
	else {
		if(in_array('classes',$tab_champs)) {$get_classes='y';}
		if(in_array('eleves',$tab_champs)) {$get_eleves='y';$get_classes='y';}
		if(in_array('profs',$tab_champs)) {$get_profs='y';}
	}

	$sql="SELECT a.indice_aid, a.nom AS nom_aid, ac.nom, ac.nom_complet, ac.display_begin, ac.display_end, ac.type_note, ac.order_display1, ac.order_display2, ac.type_aid, ac.display_nom, ac.message, ac.outils_complementaires, ac.autoriser_inscript_multiples, ac.display_bulletin, ac.bull_simplifie FROM aid a, 
											aid_config ac 
										WHERE a.indice_aid=ac.indice_aid AND 
											a.id='".$id_aid."';";
	$res_aid=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_aid)==0) {
		$tab_aid['id_aid']="";
		$tab_aid['indice_aid']="";
		$tab_aid['nom_general_court']="AID";
		$tab_aid['nom']="AID";
		$tab_aid['nom_general_complet']="AID";
		$tab_aid['nom_complet']="AID";
		$tab_aid['nom_aid']="AID";
		$tab_aid['aid_nom']="AID";
		$tab_aid['profs']=array();
		$tab_aid['proflist_string']="...";
		$tab_aid['classes']=array();
		$tab_aid['classlist_string']="";
		$tab_aid['type_note']="every";
		$tab_aid['order_display1']="b";
		$tab_aid['order_display2']=0;
		$tab_aid['type_aid']=0;
		$tab_aid['display_nom']="n";
		$tab_aid['message']="";
		$tab_aid['outils_complementaires']="n";
		$tab_aid['autoriser_inscript_multiples']="n";
		$tab_aid['display_bulletin']="y";
		$tab_aid['bull_simplifie']="y";
	}
	else {
		$lig_aid=mysqli_fetch_object($res_aid);

		$tab_aid['id_aid']=$id_aid;
		$tab_aid['indice_aid']=$lig_aid->indice_aid;
		$tab_aid['nom_general_court']=$lig_aid->nom;
		$tab_aid['nom']=$lig_aid->nom;
		$tab_aid['nom_general_complet']=$lig_aid->nom_complet;
		$tab_aid['nom_complet']=$lig_aid->nom_complet;
		$tab_aid['nom_aid']=$lig_aid->nom_aid;
		$tab_aid['aid_nom']=$lig_aid->nom_aid;
		$tab_aid['display_begin']=$lig_aid->display_begin;
		$tab_aid['display_end']=$lig_aid->display_end;
		$tab_aid['type_note']=$lig_aid->type_note;
		$tab_aid['order_display1']=$lig_aid->order_display1;
		$tab_aid['order_display2']=$lig_aid->order_display2;
		$tab_aid['type_aid']=$lig_aid->type_aid;
		$tab_aid['display_nom']=$lig_aid->display_nom;
		$tab_aid['message']=$lig_aid->message;
		$tab_aid['outils_complementaires']=$lig_aid->outils_complementaires;
		$tab_aid['autoriser_inscript_multiples']=$lig_aid->autoriser_inscript_multiples;
		$tab_aid['display_bulletin']=$lig_aid->display_bulletin;
		$tab_aid['bull_simplifie']=$lig_aid->bull_simplifie;

		if($get_profs=='y') {
			$tab_aid['profs']=array();
			$tab_aid['profs']['list']=array();
			$tab_aid['profs']['users']=array();
			$sql="SELECT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, j_aid_utilisateurs jau 
												WHERE u.login=jau.id_utilisateur AND 
													jau.id_aid='".$id_aid."'
												ORDER BY u.nom, u.prenom;";
			$res_aid_prof=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_aid_prof)==0) {
				$tab_aid['proflist_string']="...";
			}
			else {
				$tab_aid['proflist_string']="";
				$cpt_aid_prof=0;
				while($lig_aid_prof=mysqli_fetch_object($res_aid_prof)) {
					if($cpt_aid_prof>0) {
						$tab_aid['proflist_string'].=", ";
					}
					$tab_aid['proflist_string'].=$lig_aid_prof->civilite." ".$lig_aid_prof->nom." ".mb_substr($lig_aid_prof->prenom,0,1);

					$tab_aid['profs']['list'][]=$lig_aid_prof->login;
					$tab_aid['profs']['users'][$lig_aid_prof->login]=array("login"=>$lig_aid_prof->login, "civilite"=>$lig_aid_prof->civilite, "nom"=>$lig_aid_prof->nom, "prenom"=>$lig_aid_prof->prenom);

					$cpt_aid_prof++;
				}
			}
			$tab_aid['profs']['proflist_string']=$tab_aid['proflist_string'];
		}

		if($get_classes=='y') {
			$tab_aid['classes']=array();
			$tab_aid['classlist_string']="";
			$sql="SELECT DISTINCT c.* FROM classes c, 
								j_eleves_classes jec, 
								j_aid_eleves jae 
							WHERE c.id=jec.id_classe AND 
								jec.login=jae.login AND 
								jae.id_aid='".$id_aid."';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig=mysqli_fetch_assoc($res)) {
					$tab_aid['classes']['list'][]=$lig["id"];
					$tab_aid['classes']['classes'][$lig["id"]]=$lig;
					if($tab_aid['classlist_string']!="") {$tab_aid['classlist_string'].=", ";}
					$tab_aid['classlist_string'].=$lig['classe'];
				}
			}
		}

		if($get_eleves=='y') {

			$tab_aid['eleves']=array();

			// Périodes: Peut-on faire un AID avec des classes à nombre de périodes différent? Oui
			$sql="SELECT max(num_periode) AS maxper FROM periodes p, 
								j_eleves_classes jec, 
								j_aid_eleves jae 
							WHERE p.id_classe=jec.id_classe AND 
								jec.login=jae.login AND 
								jae.id_aid='".$id_aid."';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);
				$maxper=$lig->maxper;

				$tab_aid['maxper']=$maxper;
				$nb_periode=$maxper+1;

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

				foreach ($tab_aid["classes"]["list"] as $c_id) {
					$sql_periode2 = "SELECT * FROM periodes WHERE id_classe = '". $tab_aid["classes"]["classes"][$c_id]["id"] ."' ORDER BY num_periode";
					$periode_query2 = mysqli_query($GLOBALS["mysqli"], $sql_periode2);
					$nb_periode = $periode_query2->num_rows + 1 ;
					$i = "1";
					while ($obj_period2 = $periode_query2->fetch_object()) {
						$tab_aid["classe"]["ver_periode"][$c_id][$i] = $obj_period2->verouiller;
						$liste_ver_per[$i] .= $tab_aid["classe"]["ver_periode"][$c_id][$i];
						$i++;
					}
					$all_clos .= "O";
					$all_open .= "N";
					$all_clos_part .= "P";
					$periode_query2->close();
				}
				$i = "1";
				while ($i < $nb_periode) {
					if ($liste_ver_per[$i] == $all_clos) {
						// Toutes les classes sont closes
						$tab_aid["classe"]["ver_periode"]["all"][$i] = 0;
					}
					else if ($liste_ver_per[$i] == $all_clos_part) {
						// Toutes les classes sont partiellement closes
						$tab_aid["classe"]["ver_periode"]["all"][$i] = 1;
					}
					else if ($liste_ver_per[$i] == $all_open) {
						// Toutes les classes sont ouvertes
						$tab_aid["classe"]["ver_periode"]["all"][$i] = 3;
					}
					else if (substr_count($liste_ver_per[$i], "N") > 0) {
						// Au moins une classe est ouverte
						$tab_aid["classe"]["ver_periode"]["all"][$i] = 2;
					}
					else {
						$tab_aid["classe"]["ver_periode"]["all"][$i] = -1;
					}
					$i++;
				}

				// Elèves

				$tab_aid['eleves']["all"]['list']=array();
				$tab_aid['eleves']["all"]['users']=array();

				if($order_by_ele=="") {
					$order_by_ele=" ORDER BY e.nom, e.prenom, e.naissance";
				}

				for($i=1;$i<=$maxper;$i++) {
					$tab_aid['eleves'][$i]['list']=array();
					$tab_aid['eleves'][$i]['users']=array();

					if(($i>=$tab_aid['display_begin'])&&($i<=$tab_aid['display_end'])) {
						$sql="SELECT DISTINCT e.*, jec.id_classe, c.classe FROM classes c,
														eleves e,
														j_eleves_classes jec, 
														j_aid_eleves jae 
													WHERE c.id=jec.id_classe AND 
														e.login=jec.login AND 
														jec.login=jae.login AND 
														jec.periode='".$i."' AND 
														jae.id_aid='".$id_aid."'".$order_by_ele.";";
						//echo "$sql<br />";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)>0) {
							while($lig=mysqli_fetch_object($res)) {
								$tab_aid["eleves"][$i]["list"][] = $lig->login;
								$tab_aid["eleves"][$i]["users"][$lig->login] = array("login" => $lig->login, "nom" => $lig->nom, "prenom" => $lig->prenom, "id_classe" => $lig->id_classe, "classe" => $lig->id_classe, "nom_classe" => $lig->classe, "sconet_id" => $lig->ele_id, "elenoet" => $lig->elenoet, "sexe" => $lig->sexe, "email" => $lig->email, "naissance" => $lig->naissance);
								if(!in_array($lig->login, $tab_aid['eleves']["all"]['list'])) {
									$tab_aid['eleves']["all"]['list'][]=$lig->login;
									$tab_aid['eleves']["all"]["users"][$lig->login]=$tab_aid["eleves"][$i]["users"][$lig->login];
								}
							}
						}
					}
				}
			}
		}
	}

	return $tab_aid;
}

function get_info_aid($id_aid, $tab_infos=array('nom_general_complet', 'classes', 'profs'), $mode="html") {
	$in_array_classes=in_array("classes", $tab_infos);
	$in_array_profs=in_array("profs", $tab_infos);
	if(($in_array_classes)&&($in_array_profs)) {
		$aid=get_tab_aid($id_aid, "", array("classes", "profs"));
	}
	elseif($in_array_classes) {
		$aid=get_tab_aid($id_aid, "", array("classes"));
	}
	elseif($in_array_profs) {
		$aid=get_tab_aid($id_aid, "", array("profs"));
	}
	else {
		$aid=get_tab_aid($id_aid, "", array());
	}

	$retour="";
	if(isset($aid['nom_aid'])) {
		$retour=$aid['nom_aid'];
		if($mode=="html") {
			if(in_array('nom_general_court', $tab_infos)) {$retour.=" (<em>".$aid['nom_general_court']."</em>)";}
			if(in_array('nom_general_complet', $tab_infos)) {$retour.=" (<em>".$aid['nom_general_complet']."</em>)";}
			if(in_array('classes', $tab_infos)) {$retour.=" en ".$aid['classlist_string'];}
			if(in_array('profs', $tab_infos)) {$retour.=" (<em>".$aid['proflist_string']."</em>)";}
		}
		else {
			if(in_array('nom_general_court', $tab_infos)) {$retour.=" (".$aid['nom_general_court'].")";}
			if(in_array('nom_general_complet', $tab_infos)) {$retour.=" (".$aid['nom_general_complet'].")";}
			if(in_array('classes', $tab_infos)) {$retour.=" en ".$aid['classlist_string'];}
			if(in_array('profs', $tab_infos)) {$retour.=" (".$aid['proflist_string'].")";}
		}
	}

	return $retour;
}

function acces_saisie_aid($login, $indice_aid, $id_aid) {
	global $mysqli;
	$sql="SELECT * FROM j_aid_utilisateurs WHERE id_utilisateur='".$login."' AND id_aid='".$id_aid."' AND indice_aid='".$indice_aid."';";
	//echo "$sql<br />";
	//die();
	$res_aid=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_aid)==0) {
		return false;
	}
	else {
		return true;
	}
}

function get_info_categorie_aid2($indice_aid, $tab_infos=array('nom', 'nom_complet'), $mode="html") {
	global $mysqli;
	$retour="";
	$sql="SELECT * FROM aid_config WHERE indice_aid='".$indice_aid."';";
	//echo "$sql<br />";
	//die();
	$res_aid=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_aid)==0) {
		return $retour;
	}
	else {
		$lig=mysqli_fetch_assoc($res_aid);
		if($mode=="html") {
			if(in_array('nom', $tab_infos)) {$retour.=$lig['nom'];}
			if(in_array('nom_complet', $tab_infos)) {$retour.=" (<em>".$lig['nom_complet']."</em>)";}
			// A COMPLETER POUR LES AUTRES CHAMPS
		}
		else {
			if(in_array('nom', $tab_infos)) {$retour.=$lig['nom'];}
			if(in_array('nom_complet', $tab_infos)) {$retour.=" (".$lig['nom_complet'].")";}
			// A COMPLETER POUR LES AUTRES CHAMPS
		}
	}

	return $retour;
}

function get_tab_aid_ele_clas($login_ele, $id_classe="", $periode_num="", $order_by_ele="",$tab_champs=array('all')) {
	global $mysqli;

	$tab=array();

	if($login_ele!="") {
		$sql="SELECT DISTINCT a.id, a.indice_aid FROM aid_config ac, aid a, j_aid_eleves jae WHERE ac.indice_aid=a.indice_aid AND a.id=jae.id_aid AND ac.indice_aid=jae.indice_aid AND jae.login='".$login_ele."'";
		if($periode_num!="") {
			$sql.=" AND ac.display_begin<='".$periode_num."' AND ac.display_end>='".$periode_num."'";
		}
		$sql.=" ORDER BY ac.order_display1, ac.order_display2, a.numero;";
		//echo "$sql<br />";
		$res_aid=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_aid)>0) {
			while($lig_aid=mysqli_fetch_assoc($res_aid)) {
				$tab[]=get_tab_aid($lig_aid['id']);
			}
		}
	}
	elseif($id_classe!="") {
		$sql="SELECT DISTINCT a.id, a.indice_aid FROM aid_config ac, aid a, j_aid_eleves jae, j_eleves_classes jec WHERE ac.indice_aid=a.indice_aid AND a.id=jae.id_aid AND ac.indice_aid=jae.indice_aid AND jae.login=jec.login AND jec.id_classe='".$id_classe."'";
		if($periode_num!="") {
			$sql.=" AND ac.display_begin<='".$periode_num."' AND ac.display_end>='".$periode_num."' AND jec.periode='".$periode_num."'";
		}
		$sql.=" ORDER BY ac.order_display1, ac.order_display2, a.numero;";
		//echo "$sql<br />";
		$res_aid=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_aid)>0) {
			while($lig_aid=mysqli_fetch_assoc($res_aid)) {
				$tab[]=get_tab_aid($lig_aid['id']);
			}
		}
	}
	return $tab;
}

function get_tab_aid_prof($login_prof, $id_classe="", $periode_num="", $order_by_ele="",$tab_champs=array('all')) {
	global $mysqli;

	$tab=array();

	$sql="SELECT DISTINCT a.id, a.indice_aid FROM aid_config ac, aid a, j_aid_utilisateurs jau WHERE ac.indice_aid=a.indice_aid AND a.id=jau.id_aid AND ac.indice_aid=jau.indice_aid AND jau.id_utilisateur='".$login_prof."'";
	if($periode_num!="") {
		$sql.=" AND ac.display_begin<='".$periode_num."' AND ac.display_end>='".$periode_num."'";
	}
	if($id_classe!="") {
		$sql.=" AND id_aid IN (SELECT DISTINCT id_aid FROM j_aid_eleves jae, j_eleves_classes jec WHERE jae.login=jec.login AND jec.id_classe='".$id_classe."')";
	}
	$sql.=" ORDER BY ac.order_display1, ac.order_display2, a.numero, ac.nom_complet, ac.nom;";
	//echo "$sql<br />";
	$res_aid=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_aid)>0) {
		while($lig_aid=mysqli_fetch_assoc($res_aid)) {
			$tab[]=get_tab_aid($lig_aid['id']);
		}
	}

	return $tab;
}
?>
