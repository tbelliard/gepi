<?php
	/* $Id: fb_moyenne.inc.php 3181 2009-06-03 09:31:02Z crob $ */

	//=====================
	$tab_eleves_OOo[$nb_eleve][$j][3]="";		// on initialise le champ pour ne pas avoir d'erreur
	
	// on calcule la moyenne de la matière
	if($fb_mode_moyenne==1){
		$tab_eleves_OOo[$nb_eleve][$j][3] = $moy_classe[$j];
	}
	else{
		$sql="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
		$res_mat=mysql_query($sql);
		if(mysql_num_rows($res_mat)>0){
			$lig_mat=mysql_fetch_object($res_mat);
			$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
			$res_moy=mysql_query($sql);
			if(mysql_num_rows($res_moy)>0){
				$lig_moy=mysql_fetch_object($res_moy);
				$tab_eleves_OOo[$nb_eleve][$j][3] = $lig_moy->moyenne_mat;
			}
		}
	}
	//=====================
?>