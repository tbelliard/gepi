<?php
/** Outils complémentaires de gestion des AID
 * 
 * $Id:  $
 * 
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Aid
 * @subpackage Initialisation
 *
*/

/* 
fonction vérifiant les droits d'accès au module selon l'identifiant





$mode : utilisé uniquement si $champ est non vide
* $mode = W -> l'utilisateur a-t-il accès en écriture ?
* Autres valeurs de W -> l'utilisateur a-t-il accès en lecture ?

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

        // Le profeseur est-il responsable de cet AID ?
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


?>
