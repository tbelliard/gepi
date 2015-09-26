<?php

/*
 *
 * Copyright 2015 Régis Bouguin
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/*
echo 'AID dans plusieurs fichiers<br />';
print_r($id_AID);
echo '<br />';
print_r($num_fich);
 */

foreach ($id_AID as $id) {
	$filtre = "a.`id_aid` LIKE '".$id."' ";
	$info_AID = get_AID($id)->fetch_object();
	$info_grp = $info_AID->nom_famille.'_'.$info_AID->nom;
	
	$sqlElv = "SELECT  DISTINCT a.`login` , e.`nom` , e.`prenom` , "
	   . "e.`sexe` , e.`naissance` , e.`lieu_naissance` , "
	   . "c.`classe` , e.`no_gep` , e.`elenoet`, e.`ele_id` "
	   . "FROM  `j_aid_eleves` AS a "
	   . "INNER JOIN `eleves` AS e ON e.`login` = a.`login`"
	   . "INNER JOIN `j_eleves_classes` AS jec ON jec.`login` = e.`login` "
	   . "INNER JOIN `classes` AS c ON c.`id` = jec.`id_classe` "
	   . "WHERE ".$filtre." "
	   . "ORDER BY e.nom, e.prenom ";
	// echo $sqlElv.'<br />';
	$resElv = mysqli_query($mysqli, $sqlElv);

	if(mysqli_num_rows($resElv)>0) {
		$tab_eleves_OOo=array();
		$nb_eleve=0;

		while($lig=mysqli_fetch_object($resElv)) {
			$nb_eleve_actuel=$nb_eleve;
			include 'lib/charge_tableau.php';
			$tab_eleves_OOo[$nb_eleve_actuel]['classe']=$lig->classe;
		}
	}
	
	if(count($tab_eleves_OOo)>0) {
		$mode_ooo="imprime";

		include_once('../tbs/tbs_class.php');
		include_once('../tbs/plugins/tbs_plugin_opentbs.php');

		$OOo = new clsTinyButStrong;
		$OOo->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);

		$nom_dossier_modele_a_utiliser = $path."/";// le chemin du fichier est indiqué à partir de l'emplacement de ce fichier
		$nom_fichier_modele_ooo = $tab_file[$num_fich];

		$OOo->LoadTemplate($nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo, OPENTBS_ALREADY_UTF8);

		$OOo->MergeBlock('eleves',$tab_eleves_OOo);


		$nom_fic = remplace_accents($info_grp, "all")."_".$nom_fichier_modele_ooo;

		$tableau_des_fichiers_generes[]=$nom_fic;

		$OOo->Show(OPENTBS_FILE, $chemin_temp."/".$nom_fic);
		$msg.="Fichier $info_grp : <a href='$chemin_temp/$nom_fic' target='_blank'>$nom_fic</a><br />";

	}
	else {
		$msg.="Aucun élève n'a été extrait pour l'enseignement $info_grp.<br />";
	}
	
	
}
			
			