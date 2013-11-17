<?php
/*
 *
 * $Id$
 *
 *
 */


// gestion des fonctions sur les absences, dispences, retard, infirmerie

// fonction qui permet de vérifier si la variable ne contient que des caractère
function verif_texte($texte_ver) {
	if(!my_ereg("^[a-zA-Z_]+$",$texte_ver)){ $texte_ver = FALSE; } else { $texte_ver = $texte_ver; }
	return $texte_ver;
 }

// fonction qui permet de vérifier si la variable ne contient que des chiffres
function verif_num($texte_ver) {
	if(!my_ereg("^[0-9]+$",$texte_ver)){ $texte_ver = FALSE; } else { $texte_ver = $texte_ver; }
	return $texte_ver;
 }


/* ************************************************************* */
/* DEBUT - GESTION DES COURIERS                                  */
/* modif_suivi_du_courrier( numéro id de l'absence )             */
// permet de supprimer un courrier s'il y a besoin par rapport à l'id de l'absence
function modif_suivi_du_courrier($id_absence_eleve, $eleve_absence_eleve='')
{

	global $prefix_base;

	$requete_a_qui_appartient_id = 'SELECT * FROM '.$prefix_base.'absences_eleves WHERE id_absence_eleve = "' . $id_absence_eleve . '"';
    $execution_a_qui_appartient_id = mysqli_query($GLOBALS["___mysqli_ston"], $requete_a_qui_appartient_id) or die('Erreur SQL !'.$requete_a_qui_appartient_id.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while ( $donnee_a_qui_appartient_id = mysqli_fetch_array( $execution_a_qui_appartient_id ) ) {

		$eleve_absence_eleve = $donnee_a_qui_appartient_id['eleve_absence_eleve'];

	}

		// on vérify s'il y a un courrier si oui on le supprime s'il fait parti d'un ensemble de courrier alors on le modifi.
		// première option il existe une lettre qui fait seulement référence à cette id donc suppression
		$cpt_lettre_suivi = mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE quirecois_lettre_suivi = '".$eleve_absence_eleve."' AND partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi = ',".$id_absence_eleve.",'"),0);
		if( $cpt_lettre_suivi == 1 )
		{

	              $requete = "DELETE
	              			    FROM ".$prefix_base."lettres_suivis
	              			   WHERE partde_lettre_suivi = 'absences_eleves'
	              			  	 AND type_lettre_suivi = '6'
	              			  	 AND partdenum_lettre_suivi = ',".$id_absence_eleve.",'";
	              mysqli_query($GLOBALS["___mysqli_ston"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

		}
		else
		{

			// deuxième option il existe une lettre qui fait référence à cette id mais à d'autre aussi donc modification
			$cpt_lettre_suivi = mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE quirecois_lettre_suivi = '".$eleve_absence_eleve."' AND partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi LIKE '%,".$id_absence_eleve.",%'"),0);
			if( $cpt_lettre_suivi == 1 )
			{

		    	$requete = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT *
		    						      FROM ".$prefix_base."lettres_suivis
		    						     WHERE partde_lettre_suivi = 'absences_eleves'
		    						       AND type_lettre_suivi = '6'
		    						       AND partdenum_lettre_suivi LIKE '%,".$id_absence_eleve.",%'"
		    						  );

		    	$donnee = mysqli_fetch_array($requete);
		    	$remplace_sa = ','.$id_absence_eleve.',';
		    	$modifier_par = my_ereg_replace($remplace_sa,',',$donnee['partdenum_lettre_suivi']);
		    	$requete = "UPDATE ".$prefix_base."lettres_suivis
		    				SET partdenum_lettre_suivi = '".$modifier_par."',
		    					envoye_date_lettre_suivi = '',
		    					envoye_heure_lettre_suivi = '',
		    					quienvoi_lettre_suivi = ''
		    				WHERE partde_lettre_suivi = 'absences_eleves'
		    				  AND type_lettre_suivi = '6'
		    				  AND partdenum_lettre_suivi LIKE '%,".$id_absence_eleve.",%'";
	            mysqli_query($GLOBALS["___mysqli_ston"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

			}

		}
}
/* ******************************************** */

// fonction permettant de supprimer un ou plusieurs id dans une table donnée
// à partir d'un tableau qui contiendrais les ids
// $tableau_des_ids: tableau avec les numéro id
// $prefix_base: préfix de la base s'il y en a
// $table: nom de la table choisie
// $selection: avoir un variable sélection
function supprime_id($tableau_des_ids, $prefix_base, $table, $selection)
 {
	$id_init = '0';
	while(!empty($tableau_des_ids[$id_init]))
	 {

		// on attribue les variables
		$id_selectionne = $tableau_des_ids[$id_init];
		if ( isset($selection[$id_init]) and $selection[$id_init] != '' )
		{

			$cocher = 'oui';

		}
		else
		{

			$cocher = 'non';

		}

		// si les variables sont correct et non vide on continue
		if(verif_texte($table) and verif_num($id_selectionne) and $id_selectionne != '' and $table != '' and $cocher === 'oui')
		{


			// on vérifie s'il y a du courrier
			if ( $table === 'absences_eleves' )
			{

				modif_suivi_du_courrier($id_selectionne);

			}

			// suppression dans la table absence_rb
       		suppr_absences_rb($id_selectionne);

          	$requete = "DELETE
           			    FROM ".$prefix_base.$table."
           			    WHERE id_absence_eleve ='".$id_selectionne."'";
            mysqli_query($GLOBALS["___mysqli_ston"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

        }

	 $id_init = $id_init + 1;

	 }

 }

// fonction gérant l'insertion d'une absences ou plusieurs absence
// par rapport à un tableau d'information qui contient les informations ci-dessous
// du, au, de, a, motif, justification, justification plus d'info
function ajout_abs($tableau_des_donnees)
 {
	$id_init = '0';
	while(!empty($tableau_des_donnees[$id_init]['id']))
	{

	 	$id_init = $id_init + 1;

	}
 }


/* *************************************************************** */
/* Fonction gérant l'insertion d'absence dans la table absences_rb */
function gerer_absence($id='',$eleve_id,$retard_absence,$groupe_id='',$edt_id='',$jour_semaine='',$creneau_id='',$debut_ts,$fin_ts,$date_saisie,$login_saisie='',$action)
{

	global $prefix_base;

	/*
	$eleve_id -> login de l'élève
	$retard_absence -> R ou A
	$groupe_id -> vide
	$edt_id -> vide
	$jour_semaine -> vide
	$creneau_id -> vide
	$debut_ts -> debut en timestamp / mktime(heure, minute, 0, mois, jour, annee);
	$fin_ts -> fin en timestamp / mktime(heure, minute, 0, mois, jour, annee);
	$date_saisie -> date de saisi en timestamp / mktime(heure, minute, 0, mois, jour, annee);
	$login_saisie -> login de la personne pour la saisi
	$action -> ajouter
	*/

	if ( $action === 'ajouter' )
	{

		// on vérifie qu'une absence ne se trouve pas entre le début et la fin de celle saisie
		$cpt_ligne = mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(*)
										   		 FROM " . $prefix_base . "absences_rb
										   		WHERE eleve_id = '" . $eleve_id . "'
										   		  AND retard_absence = '" . $retard_absence . "'
										   		  AND debut_ts >=  '" . $debut_ts . "'
										   		  AND fin_ts <= '" . $fin_ts . "'"
										 	  ),0);

		// s'il n'y aucun enregistrement qui correspond alors on l'ajoute
		if ( $cpt_ligne == 0 )
		{

			$saisie_sql = "INSERT INTO absences_rb
						   		(eleve_id, retard_absence, groupe_id, edt_id, jour_semaine, creneau_id, debut_ts, fin_ts, date_saisie, login_saisie)
						   VALUES
						   		('" . $eleve_id . "', '" . $retard_absence . "', '" . $groupe_id . "', '0', '" . $jour_semaine . "', '" . $creneau_id . "', '" . $debut_ts . "', '" . $fin_ts . "', '" . $date_saisie . "', '" . $_SESSION["login"] . "')";
			$insere_abs = mysqli_query($GLOBALS["___mysqli_ston"], $saisie_sql) OR DIE ('Erreur SQL !'.$saisie_sql.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));//('Impossible d\'enregistrer l\'absence de '.$eleve_absent[$a]);

		}
		else
		{

			// nous allons lister toutes les enregistrement
			$requete = ("SELECT *
				 		   FROM " . $prefix_base . "absences_rb
					 	  WHERE eleve_id = '" . $eleve_id . "'
					   		AND retard_absence = '" . $retard_absence . "'
					   		AND debut_ts >=  '" . $debut_ts . "'
					   		AND fin_ts <= '" . $fin_ts . "'"
				   	   );

			$execution = mysqli_query($GLOBALS["___mysqli_ston"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
        	while ($donnee = mysqli_fetch_array($execution))
        	{

				// si le debut est la fin sont compris entre les deux valeur mais égale à aucun début
				// on les supprimes
				if ( $debut_ts > $donnee['debut_ts'] and $fin_ts < $donnee['fin_ts'] )
				{

					gerer_absence($donnee['id'],$eleve_id,$retard_absence,'','','','',$donnee['debut_ts'],$donnee['fin_ts'],$donnee['date_saisie'],'','supprimer');

				}

				// si le debut est égale à la valeur de début et que la fin est inférieur à la fin
				// ???????????????????????
				// en attente de plus d'information

			}

		}

	}

	if ( $action === 'supprimer' )
	{

		if ( !verif_num($id) )
		{

			$req_delete = "DELETE
						     FROM " . $prefix_base . "absences_rb
						    WHERE id = '" . $id . "'
						      AND retard_absence = '" . $retard_absence . "'
						      AND debut_ts >=  '" . $debut_ts . "'
					   		  AND fin_ts <= '" . $fin_ts . "'
					  	  ";

        	$req_sql = mysqli_query($GLOBALS["___mysqli_ston"], $req_delete);

		}

	}

}
/*                                                                 */
/* *************************************************************** */


/* *************************************************************** */
/* Fonction gérant la suppression des absences dans la table absences_rb */
function suppr_absences_rb($id)
{

	global $prefix_base;

	/*
	$id -> id de la table absences_eleves
	$type -> R ou A
	*/

	if ( $id != '' )
    {

		// on vérifie qu'une absence ne se trouve pas entre le début et la fin de celle saisie
		$cpt_ligne = mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(*)
											   FROM " . $prefix_base . "absences_eleves
											   WHERE id_absence_eleve = '" . $id . "'"
											 ),0);

		// s'il y un enregistrement
		if ( $cpt_ligne != 0 )
		{

			// on ne connait pas l'id dans la table absences_rb donc il vas falloir utilise d'autre information avant la supprimession
			$requete = "SELECT *
						FROM " . $prefix_base . "absences_eleves
						WHERE id_absence_eleve = '" . $id . "' ";

	        $resultat = mysqli_query($GLOBALS["___mysqli_ston"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    	    while ( $donnee = mysqli_fetch_array($resultat) )
        	{

	        	$type_absence = $donnee['type_absence_eleve'];
    	        $eleve_absent = $donnee['eleve_absence_eleve'];
        	    $d_date_absence_eleve = $donnee['d_date_absence_eleve'];
            	$a_date_absence_eleve = $donnee['a_date_absence_eleve'];
            	$d_heure_absence_eleve = $donnee['d_heure_absence_eleve'];
            	$a_heure_absence_eleve = $donnee['a_heure_absence_eleve'];

        	}

			if ( $type_absence === 'R' )
			{

				$a_heure_absence_eleve = $d_heure_absence_eleve;

			}

        	$explode_heuredeb = explode(":", $d_heure_absence_eleve);
			$explode_heurefin = explode(":", $a_heure_absence_eleve);
			$explode_date_debut = explode('/', date_fr($d_date_absence_eleve));
			$explode_date_fin = explode('/', date_fr($a_date_absence_eleve));
			$debut_ts = mktime($explode_heuredeb[0], $explode_heuredeb[1], 0, $explode_date_debut[1], $explode_date_debut[0], $explode_date_debut[2]);
			$fin_ts = mktime($explode_heurefin[0], $explode_heurefin[1], 0, $explode_date_fin[1], $explode_date_fin[0], $explode_date_fin[2]);

			if ( $debut_ts != '' and $fin_ts != '' )
			{

				$req_delete = "DELETE
						   	   FROM " . $prefix_base . "absences_rb
						   	   WHERE retard_absence = '" . $type_absence . "'
				    		 	 AND debut_ts >=  '" . $debut_ts . "'
			   		 			 AND fin_ts <= '" . $fin_ts . "'
			   		 			 AND eleve_id = '" . $eleve_absent . "'
			  			  	  ";

       			$req_sql = mysqli_query($GLOBALS["___mysqli_ston"], $req_delete);

			}

		}

	}

}
/*                                                                 */
/* *************************************************************** */


/* *************************************************************** */
/* Fonction gérant la modification des absences dans la table absences_rb */
function modifier_absences_rb($id,$debut_ts_modif,$fin_ts_modif)
{

	global $prefix_base;

	/*
	$id -> id de la table absences_eleves
	$type -> R ou A
	*/

	if ( $id != '' )
    {

		// on vérifie qu'une absence ne se trouve pas entre le début et la fin de celle saisie
		$cpt_ligne = mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(*)
											   FROM " . $prefix_base . "absences_eleves
											   WHERE id_absence_eleve = '" . $id . "'"
											 ),0);

		// s'il y un enregistrement
		if ( $cpt_ligne != 0 )
		{

			// on ne connait pas l'id dans la table absences_rb donc il vas falloir utilise d'autre information avant la supprimession
			$requete = "SELECT *
						FROM " . $prefix_base . "absences_eleves
						WHERE id_absence_eleve = '" . $id . "' ";

	        $resultat = mysqli_query($GLOBALS["___mysqli_ston"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    	    while ( $donnee = mysqli_fetch_array($resultat) )
        	{

	        	$type_absence = $donnee['type_absence_eleve'];
    	        $eleve_absent = $donnee['eleve_absence_eleve'];
        	    $d_date_absence_eleve = $donnee['d_date_absence_eleve'];
            	$a_date_absence_eleve = $donnee['a_date_absence_eleve'];
            	$d_heure_absence_eleve = $donnee['d_heure_absence_eleve'];
            	$a_heure_absence_eleve = $donnee['a_heure_absence_eleve'];

        	}

			if ( $type_absence === 'R' )
			{

				$a_heure_absence_eleve = $d_heure_absence_eleve;

			}

        	$explode_heuredeb = explode(":", $d_heure_absence_eleve);
			$explode_heurefin = explode(":", $a_heure_absence_eleve);
			$explode_date_debut = explode('/', date_fr($d_date_absence_eleve));
			$explode_date_fin = explode('/', date_fr($a_date_absence_eleve));
			$debut_ts = mktime($explode_heuredeb[0], $explode_heuredeb[1], 0, $explode_date_debut[1], $explode_date_debut[0], $explode_date_debut[2]);
			$fin_ts = mktime($explode_heurefin[0], $explode_heurefin[1], 0, $explode_date_fin[1], $explode_date_fin[0], $explode_date_fin[2]);

			if ( $debut_ts != '' and $fin_ts != '' )
			{

				// on cherche l'id de la table absence_rb
				$requete = "SELECT *
							FROM " . $prefix_base . "absences_rb
							WHERE retard_absence = '" . $type_absence . "'
				    		  AND debut_ts >=  '" . $debut_ts . "'
			   		 		  AND fin_ts <= '" . $fin_ts . "'
			   		 		  AND eleve_id = '" . $eleve_absent . "'
			   		 	   ";

	        $resultat = mysqli_query($GLOBALS["___mysqli_ston"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    	    while ( $donnee = mysqli_fetch_array($resultat) )
        	{

        		$id_absences_rb = $donnee['id'];

        						$req_modifier = "UPDATE " . $prefix_base . "absences_rb
								 SET debut_ts = '" . $debut_ts_modif . "',
								     fin_ts = '" . $fin_ts_modif . "'
						   	   	 WHERE id = '" . $id_absences_rb . "'
			  			  	  ";

       			$req_sql = mysqli_query($GLOBALS["___mysqli_ston"], $req_modifier);

			}




			}

		}

	}

}
/*                                                                 */
/* *************************************************************** */
?>
