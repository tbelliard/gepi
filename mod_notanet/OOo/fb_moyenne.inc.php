<?php
	/* $Id$ */

	//=====================
	$tab_eleves_OOo[$nb_eleve][$j][3]="";		// on initialise le champ pour ne pas avoir d'erreur
	
	// on calcule la moyenne de la matière
	if($fb_mode_moyenne==1){
		$tab_eleves_OOo[$nb_eleve][$j][3] = $moy_classe[$j];
	}
	else{
		$sql="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
		$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res_mat)>0){
			$lig_mat=mysqli_fetch_object($res_mat);

			//if(preg_match('/|/', $lig_mat->matiere)) {
			if(strstr($lig_mat->matiere, '|')) {
				unset($tab_tmp_mat);
				$tab_tmp_mat=explode('|', $lig_mat->matiere);

				$total_note_lv=0;
				$nb_note_lv=0;
				for($loop=0;$loop<count($tab_tmp_mat);$loop++) {
					if($tab_tmp_mat[$loop]!="") {
						$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$tab_tmp_mat[$loop]."' AND note!='AB' AND note!='DI' AND note!='NN';";
						//echo "DEBUG $sql<br />";
						$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_moy)>0){
							$lig_moy=mysqli_fetch_object($res_moy);
							if($lig_moy->moyenne_mat!=NULL) {
								$total_note_lv+=$lig_moy->moyenne_mat;
								$nb_note_lv++;
							}
						}
					}
				}

				if($nb_note_lv>0) {
					$tab_eleves_OOo[$nb_eleve][$j][3] = round(10*$total_note_lv/$nb_note_lv)/10;
				}
				else {
					//$tab_eleves_OOo[$nb_eleve][$j][3] = "";

					$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
					//echo "$sql<br />";
					$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res_moy)>0){
						$lig_moy=mysqli_fetch_object($res_moy);
						$tab_eleves_OOo[$nb_eleve][$j][3] = $lig_moy->moyenne_mat;
					}
				}
			}
			else {
				$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
				//echo "$sql<br />";
				$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_moy)>0){
					$lig_moy=mysqli_fetch_object($res_moy);
					$tab_eleves_OOo[$nb_eleve][$j][3] = $lig_moy->moyenne_mat;
				}
			}
		}
	}
	//=====================
?>
