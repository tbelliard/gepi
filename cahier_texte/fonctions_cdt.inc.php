<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2008
 */

// Fonction qui renvoie la liste des devoirs à faire à partir d'aujourd'hui 00:00:00 sans limitation de temps
// le tout classés de plus récents au plus lointain

// Initialisations files
require_once("../lib/initialisations.inc.php");

function retourneDevoirs($ele_login){
	$date_ct1 = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

	// On récupère tous les devoirs depuis aujourd'hui 00:00:00
	$sql="SELECT DISTINCT ctde.* FROM ct_devoirs_entry ctde, j_eleves_groupes jeg
								WHERE ctde.id_groupe = jeg.id_groupe
								AND jeg.login = '".$ele_login."'
								AND ctde.date_ct >= '".$date_ct1."'
							ORDER BY ctde.date_ct, ctde.id_groupe;";
	//echo "$sql<br />";
	$res_ct = mysql_query($sql);
	$cpt2 = 0; // on initialise un compteur pour le while

	if(mysql_num_rows($res_ct)>0) {

		while($lig_ct = mysql_fetch_object($res_ct)) {

			$tab_ele['cdt_dev'][$cpt2] = array();
			$tab_ele['cdt_dev'][$cpt2]['id_ct'] = $lig_ct->id_ct;
			$tab_ele['cdt_dev'][$cpt2]['id_groupe'] = $lig_ct->id_groupe;
			$tab_ele['cdt_dev'][$cpt2]['date_ct'] = $lig_ct->date_ct;
			$tab_ele['cdt_dev'][$cpt2]['id_login'] = $lig_ct->id_login;
			$tab_ele['cdt_dev'][$cpt2]['contenu'] = $lig_ct->contenu;

			$cpt2++;
		}
			$tab_ele['cdt_dev']['count'] = $cpt2;
	}else{
		$tab_ele['cdt_dev']['count'] = 0;
	}
	return $tab_ele;
}
?>