<?php

/**
 *
 * @copyright 2008-2013
 */

// Fonction qui renvoie la liste des devoirs à faire à partir d'aujourd'hui 00:00:00 sans limitation de temps
// le tout classés de plus récents au plus lointain

// Initialisations files
require_once("../lib/initialisations.inc.php");

function retourneDevoirs($ele_login){
	$date_ct1 = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

	$date_limite=$date_ct1+3600*24*getSettingValue('delai_devoirs');

	// On récupère tous les devoirs depuis aujourd'hui 00:00:00
	$sql="SELECT DISTINCT ctde.* FROM ct_devoirs_entry ctde, j_eleves_groupes jeg
								WHERE ctde.id_groupe = jeg.id_groupe
								AND jeg.login = '".$ele_login."'
								AND ctde.date_ct >= '".$date_ct1."'
								AND ctde.date_ct <= '".$date_limite."'
								AND ctde.date_visibilite_eleve<='".date("Y")."-".date("m")."-".date("d")." ".date("H").":".date("i").":00'
							ORDER BY ctde.date_ct, ctde.id_groupe;";
	//echo "$sql<br />";
	/*
	$fich=fopen("/tmp/cdt.txt", "a+");
	fwrite($fich, strftime("%Y%m%d %H%M%S")." : ".preg_replace("/\t/","",$sql)."\n");
	fclose($fich);
	*/
	$res_ct = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$cpt2 = 0; // on initialise un compteur pour le while

	if(mysqli_num_rows($res_ct)>0) {

		while($lig_ct = mysqli_fetch_object($res_ct)) {

			$tab_ele['cdt_dev'][$cpt2] = array();
			$tab_ele['cdt_dev'][$cpt2]['id_ct'] = $lig_ct->id_ct;
			$tab_ele['cdt_dev'][$cpt2]['id_groupe'] = $lig_ct->id_groupe;
			$tab_ele['cdt_dev'][$cpt2]['date_ct'] = $lig_ct->date_ct;
			$tab_ele['cdt_dev'][$cpt2]['id_login'] = $lig_ct->id_login;
			$tab_ele['cdt_dev'][$cpt2]['contenu'] = $lig_ct->contenu;

			$sql="SELECT * FROM ct_devoirs_documents WHERE id_ct_devoir='".$lig_ct->id_ct."' AND visible_eleve_parent='1';";
			$res_doc_joint=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($res_doc_joint)>0) {
				$tab_ele['cdt_dev'][$cpt2]['doc_joint'] = array();
				$cpt_doc=0;
				while($lig_doc_joint=mysqli_fetch_object($res_doc_joint)) {
					$tab_ele['cdt_dev'][$cpt2]['doc_joint'][$cpt_doc]['titre'] = $lig_doc_joint->titre;
					$tab_ele['cdt_dev'][$cpt2]['doc_joint'][$cpt_doc]['emplacement'] = $lig_doc_joint->emplacement;
					$cpt_doc++;
				}
			}

			$cpt2++;
		}
		$tab_ele['cdt_dev']['count'] = $cpt2;
	}else{
		$tab_ele['cdt_dev']['count'] = 0;
	}
	return $tab_ele;
}
?>
