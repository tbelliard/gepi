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
function VerifAidIsAcive($indice_aid,$aid_id,$annee='') {
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

/**
 * Calcule le niveau de gestion des AIDs
 * 
 * - 0 : aucun droit
 * - 1 : peut uniquement ajouter / supprimer des élèves
 * - 2 : (pas encore implémenter) peut uniquement ajouter / supprimer des élèves et des professeurs responsables
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
        return 10;
        die();
    }
    if (getSettingValue("active_mod_gest_aid")=="y") {
      // l'id de l'aid n'est pas défini : on regarde si l'utilisateur est gestionnaire d'au moins une aid dans la catégorie
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
      // l'id de l'aid est défini : on regarde si l'utilisateur est gestionnaire de cette aid
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

function get_tab_aid($id_aid) {
	$tab_aid=array();

	$sql="SELECT a.nom AS nom_aid, ac.nom, ac.nom_complet FROM aid a, 
											aid_config ac 
										WHERE a.indice_aid=ac.indice_aid AND 
											a.id='".$id_aid."';";
	$res_aid=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_aid)==0) {
		$tab_aid['nom_general_court']="AID";
		$tab_aid['nom_general_complet']="AID";
		$tab_aid['nom_aid']="AID";
		$tab_aid['proflist_string']="...";
		$tab_aid['classes']=array();
	}
	else {
		$lig_aid=mysqli_fetch_object($res_aid);

		$tab_aid['nom_general_court']=$lig_aid->nom;
		$tab_aid['nom_general_complet']=$lig_aid->nom_complet;
		$tab_aid['nom_aid']=$lig_aid->nom_aid;

		$sql="SELECT u.civilite, u.nom, u.prenom FROM utilisateurs u, j_aid_utilisateurs jau 
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
				$cpt_aid_prof++;
			}
		}

		// A FAIRE : récupérer les classes d'après les élèves inscrits
		$tab_aid['classes']=array();
		$sql="SELECT DISTINCT c.* FROM classes c, ";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_assoc($res)) {
				$tab_aid['classes'][$lig["id"]]=$lig;
			}
		}
	}

	return $tab_aid;
}


?>
