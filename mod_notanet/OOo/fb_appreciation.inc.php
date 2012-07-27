<?php
	/* $Id$ */

	//=====================
	// on va chercher les appréciations si besoin
	$tab_eleves_OOo[$nb_eleve][$j][4]="";			// on initialise le champ pour ne pas avoir d'erreur
				
	if($avec_app=="y") {
		// On vérifie que la matière est dispensée
		if($tabmatieres[$j][-4]=="non dispensee dans l etablissement") {
			$tab_eleves_OOo[$nb_eleve][$j][4]="non dispensée dans l établissement";		
		}else{		
			$sql="SELECT DISTINCT na.appreciation FROM notanet_app na,
											notanet_corresp nc
									 WHERE na.login='$lig1->login' AND
											nc.notanet_mat='".$tabmatieres[$j][0]."' AND
											nc.matiere=na.matiere";
			if(isset($matiere_gepi_courante)) {
				$sql.=" AND na.matiere='$matiere_gepi_courante'";
			}
			// Si une des appréciations est non vide pour une matière gepi associée à la matière notanet courante, sans note saisie... on sélectionne celle-là:
			$sql.=" AND na.appreciation!='';";
			//echo "$sql<br />";
			$res_app=mysql_query($sql);
			if(mysql_num_rows($res_app)>0){
				$lig_app=mysql_fetch_object($res_app);
				$tab_eleves_OOo[$nb_eleve][$j][4]=$lig_app->appreciation;
				//echo "\$tab_eleves_OOo[$nb_eleve][$j][4]=$lig_app->appreciation;<br />";
			}
		}
	}
	//=====================
?>
