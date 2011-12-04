<?php
/*
 */
//fonction tirée de la gestion des adresses pour les bulletins PDF
function adresse_responsables($login_eleve) {	
    /* 
	$tab_adresse[0]['civilite']="";
	$tab_adresse[0]['civilite_courrier']=""; // Monsieur ou Madame ou Madame, Monsieur
	$tab_adresse[0]['adresse1']="";
	$tab_adresse[0]['adresse2']="";
	$tab_adresse[0]['adresse3']="";
	$tab_adresse[0]['adresse4']="";
	$tab_adresse[0]['cp_ville']="";
	$tab_adresse[0]['pays']="";
	*/
	// Récup infos responsables
	$sql="SELECT rp.civilite,rp.nom,rp.prenom,ra.adr1,ra.adr2,ra.adr3,ra.adr4,ra.cp,ra.commune,ra.pays,ra.adr_id FROM resp_pers rp, resp_adr ra, responsables2 r,eleves e WHERE rp.pers_id=r.pers_id AND rp.adr_id=ra.adr_id AND r.ele_id=e.ele_id AND e.login='$login_eleve' AND (r.resp_legal='1' OR r.resp_legal='2') ORDER BY r.resp_legal;";
	$res_resp=mysql_query($sql);
	//echo "$sql<br />";
	if(mysql_num_rows($res_resp)>0) {
		$cpt=0;
		while($lig_resp=mysql_fetch_object($res_resp)) {
			$tab_ele['resp'][$cpt]=array();
			//$tab_ele['resp'][$cpt]['pers_id']=$lig_resp->pers_id;
			//$tab_ele['resp'][$cpt]['login']=$lig_resp->login;
			$tab_ele['resp'][$cpt]['nom']=$lig_resp->nom;
			$tab_ele['resp'][$cpt]['prenom']=$lig_resp->prenom;
			$tab_ele['resp'][$cpt]['civilite']=$lig_resp->civilite;
			$tab_ele['resp'][$cpt]['adr1']=$lig_resp->adr1;
			$tab_ele['resp'][$cpt]['adr2']=$lig_resp->adr2;
			$tab_ele['resp'][$cpt]['adr3']=$lig_resp->adr3;
			$tab_ele['resp'][$cpt]['adr4']=$lig_resp->adr4;
			$tab_ele['resp'][$cpt]['cp']=$lig_resp->cp;
			$tab_ele['resp'][$cpt]['pays']=$lig_resp->pays;
			$tab_ele['resp'][$cpt]['commune']=$lig_resp->commune;
			$tab_ele['resp'][$cpt]['adr_id']=$lig_resp->adr_id;
			//$tab_ele['resp'][$cpt]['resp_legal']=$lig_resp->resp_legal;
			$cpt++;
		}
	}
		
	if (!isset($tab_ele['resp'][0])) {
		$tab_adresse[0]['civilite']="ADRESSE MANQUANTE";
		$tab_adresse[0]['civilite_courrier']="";
		$tab_adresse[0]['adresse1']="";
		$tab_adresse[0]['adresse2']="";
		$tab_adresse[0]['adresse3']="";
		$tab_adresse[0]['adresse4']="";
		$tab_adresse[0]['cp_ville']="";
		$tab_adresse[0]['pays']="";

		// Initialisation parce qu'on a des blagues s'il n'y a pas de resp:
		$nb_bulletins=1;
	}
	else {
		if (isset($tab_ele['resp'][1])) {
			//echo "<pre>il y a un R2</pre>";		
			if ((isset($tab_ele['resp'][1]['adr1']))&&
				(isset($tab_ele['resp'][1]['adr2']))&&
				(isset($tab_ele['resp'][1]['adr3']))&&
				(isset($tab_ele['resp'][1]['adr4']))&&
				(isset($tab_ele['resp'][1]['cp']))&&
				(isset($tab_ele['resp'][1]['commune']))
			) {
				// Le deuxième responsable existe et est renseigné
				if (($tab_ele['resp'][0]['adr_id']==$tab_ele['resp'][1]['adr_id']) OR
					(
						(mb_strtolower($tab_ele['resp'][0]['adr1'])==mb_strtolower($tab_ele['resp'][1]['adr1']))&&
						(mb_strtolower($tab_ele['resp'][0]['adr2'])==mb_strtolower($tab_ele['resp'][1]['adr2']))&&
						(mb_strtolower($tab_ele['resp'][0]['adr3'])==mb_strtolower($tab_ele['resp'][1]['adr3']))&&
						(mb_strtolower($tab_ele['resp'][0]['adr4'])==mb_strtolower($tab_ele['resp'][1]['adr4']))&&
						($tab_ele['resp'][0]['cp']==$tab_ele['resp'][1]['cp'])&&
						(mb_strtolower($tab_ele['resp'][0]['commune'])==mb_strtolower($tab_ele['resp'][1]['commune']))
					)
				   ) 
				{
					// Les adresses sont identiques
					$nb_bulletins=1;
					//echo "<pre>Les adresses sont identique</pre>";
					if(($tab_ele['resp'][0]['nom']!=$tab_ele['resp'][1]['nom'])&&
						($tab_ele['resp'][1]['nom']!="")) {
					// Les noms des responsables sont différents
						$tab_adresse[0]['civilite']=$tab_ele['resp'][0]['civilite']." ".$tab_ele['resp'][0]['nom']." ".$tab_ele['resp'][0]['prenom']." et ".$tab_ele['resp'][1]['civilite']." ".$tab_ele['resp'][1]['nom']." ".$tab_ele['resp'][1]['prenom'];
						$tab_adresse[0]['civilite_courrier']= "Madame, Monsieur";
					}
					else {// Les noms des responsables sont identique mais sans civilité
						if(($tab_ele['resp'][0]['civilite']!="")&&($tab_ele['resp'][1]['civilite']!="")) {
							$tab_adresse[0]['civilite']=$tab_ele['resp'][0]['civilite']." et ".$tab_ele['resp'][1]['civilite']." ".$tab_ele['resp'][0]['nom']." ".$tab_ele['resp'][0]['prenom'];
							$tab_adresse[0]['civilite_courrier']= "Madame, Monsieur";
						}
						else {
							$tab_adresse[0]['civilite']="M. et Mme ".$tab_ele['resp'][0]['nom']." ".$tab_ele['resp'][0]['prenom'];
							$tab_adresse[0]['civilite_courrier']= "Madame, Monsieur";
						}
					}

					$tab_adresse[0]['adresse1']=$tab_ele['resp'][0]['adr1'];
					if($tab_ele['resp'][0]['adr2']!=""){
						$tab_adresse[0]['adresse2']=$tab_ele['resp'][0]['adr2'];
					}
					if($tab_ele['resp'][0]['adr3']!=""){
						$tab_adresse[0]['adresse3']=$tab_ele['resp'][0]['adr3'];
					}
					//if($tab_ele['resp'][0]['adr4']!=""){
					//	$tab_adresse[0]['adresse4']=$tab_ele['resp'][0]['adr4'];
					//}
					$tab_adresse[0]['cp_ville']=$tab_ele['resp'][0]['cp']." ".$tab_ele['resp'][0]['commune'];

					if(($tab_ele['resp'][0]['pays']!="")&&(mb_strtolower($tab_ele['resp'][0]['pays'])!=mb_strtolower($gepiSchoolPays))) {
						$tab_adresse[0]['pays']=$tab_ele['resp'][0]['pays'];
					}
				}
				else {
				    
					//echo "<pre>Les adresses sont différentes</pre>";
					// Les adresses sont différentes
					//if ($un_seul_bull_par_famille!="oui") {
					// On teste en plus si la deuxième adresse est valide
					if (($un_seul_bull_par_famille!="oui")&&
						($tab_ele['resp'][1]['adr1']!="")&&
						($tab_ele['resp'][1]['commune']!="")
					) {
						$nb_bulletins=2;
					}
					else {
						$nb_bulletins=1;
					}

					for($cpt=0;$cpt<$nb_bulletins;$cpt++) {
						if($tab_ele['resp'][$cpt]['civilite']!="") {
							$tab_adresse[$cpt]['civilite']=$tab_ele['resp'][$cpt]['civilite']." ".$tab_ele['resp'][$cpt]['nom']." ".$tab_ele['resp'][$cpt]['prenom'];
							if ($tab_ele['resp'][$cpt]['civilite']=="Mlle") {$tab_adresse[$cpt]['civilite_courrier']= "Mademoiselle";}
							if ($tab_ele['resp'][$cpt]['civilite']=="Mme") {$tab_adresse[$cpt]['civilite_courrier']= "Madame";}
							if ($tab_ele['resp'][$cpt]['civilite']=="M.")  {$tab_adresse[$cpt]['civilite_courrier']= "Monsieur";}			
						}
						else {
							$tab_adresse[$cpt]['civilite']=$tab_ele['resp'][$cpt]['nom']." ".$tab_ele['resp'][$cpt]['prenom'];
							$tab_adresse[$cpt]['civilite_courrier']= "Civilité manquante dans la base";
						}

						$tab_adresse[$cpt]['adresse1']=$tab_ele['resp'][$cpt]['adr1'];
						if($tab_ele['resp'][$cpt]['adr2']!=""){
							$tab_adresse[$cpt]['adresse2']=$tab_ele['resp'][$cpt]['adr2'];
						}
						if($tab_ele['resp'][$cpt]['adr3']!=""){
							$tab_adresse[$cpt]['adresse3']=$tab_ele['resp'][$cpt]['adr3'];
						}
						
						/*
						if($tab_ele['resp'][$cpt]['adr4']!=""){
							$tab_adresse[$cpt]['adresse4']="<br />\n".$tab_ele['resp'][$cpt]['adr4'];
						}
						*/
						
						$tab_adresse[$cpt]['cp_ville']=$tab_ele['resp'][$cpt]['cp']." ".$tab_ele['resp'][$cpt]['commune'];

						if(($tab_ele['resp'][$cpt]['pays']!="")&&(mb_strtolower($tab_ele['resp'][$cpt]['pays'])!=mb_strtolower($gepiSchoolPays))) {
							$tab_adresse[$cpt]['pays']=$tab_ele['resp'][$cpt]['pays'];
						}
					}

				}
			}
			else {
				// Il n'y a pas de deuxième adresse, mais il y aurait un deuxième responsable???
				// CA NE DEVRAIT PAS ARRIVER ETANT DONNé LA REQUETE EFFECTUEE QUI JOINT resp_pers ET resp_adr...
				if ($un_seul_bull_par_famille!="oui") {
					$nb_bulletins=2;
				$nb_bulletins=1;
				}

				for($cpt=0;$cpt<$nb_bulletins;$cpt++) {
					if($tab_ele['resp'][$cpt]['civilite']!="") {
						$tab_adresse[$cpt]['civilite']=$tab_ele['resp'][$cpt]['civilite']." ".$tab_ele['resp'][$cpt]['nom']." ".$tab_ele['resp'][$cpt]['prenom'];
						if ($tab_ele['resp'][$cpt]['civilite']=="Mlle") {$tab_adresse[$cpt]['civilite_courrier']= "Mademoiselle";}
						if ($tab_ele['resp'][$cpt]['civilite']=="Mme") {$tab_adresse[$cpt]['civilite_courrier']= "Madame";}
						if ($tab_ele['resp'][$cpt]['civilite']=="M.")  {$tab_adresse[$cpt]['civilite_courrier']= "Monsieur";}
					}
					else {
						$tab_adresse[$cpt]['civilite']=$tab_ele['resp'][$cpt]['nom']." ".$tab_ele['resp'][$cpt]['prenom'];
						$tab_adresse[$cpt]['civilite_courrier']= "Civilité manquante dans la base";
					}

					$tab_adresse[$cpt]['adresse1']=$tab_ele['resp'][$cpt]['adr1'];
					if($tab_ele['resp'][$cpt]['adr2']!=""){
						$tab_adresse[$cpt]['adresse2']=$tab_ele['resp'][$cpt]['adr2'];
					}
					if($tab_ele['resp'][$cpt]['adr3']!=""){
						$tab_adresse[$cpt]['adresse3']=$tab_ele['resp'][$cpt]['adr3'];
					}
					/*
					if($tab_ele['resp'][$cpt]['adr4']!=""){
						$tab_adresse[$cpt]['adresse4']="<br />\n".$tab_ele['resp'][$cpt]['adr4'];
					}
					*/
					$tab_adresse[$cpt]['cp_ville']=$tab_ele['resp'][$cpt]['cp']." ".$tab_ele['resp'][$cpt]['commune'];

					if(($tab_ele['resp'][$cpt]['pays']!="")&&(mb_strtolower($tab_ele['resp'][$cpt]['pays'])!=mb_strtolower($gepiSchoolPays))) {
						$tab_adresse[$cpt]['pays']=$tab_ele['resp'][$cpt]['pays'];
					}
				}
			}
		}
		else {
			// Il n'y a pas de deuxième responsable
			$nb_bulletins=1;

			if($tab_ele['resp'][0]['civilite']!="") {
				$tab_adresse[0]['civilite']=$tab_ele['resp'][0]['civilite']." ".$tab_ele['resp'][0]['nom']." ".$tab_ele['resp'][0]['prenom'];
				if ($tab_ele['resp'][0]['civilite']=="Mlle") {$tab_adresse[0]['civilite_courrier']= "Mademoiselle";}
				if ($tab_ele['resp'][0]['civilite']=="Mme") {$tab_adresse[0]['civilite_courrier']= "Madame";}
				if ($tab_ele['resp'][0]['civilite']=="M.")  {$tab_adresse[0]['civilite_courrier']= "Monsieur";}
			}
			else {
				$tab_adresse[0]['civilite']=$tab_ele['resp'][0]['nom']." ".$tab_ele['resp'][0]['prenom'];
				$tab_adresse[0]['civilite_courrier']= "Civilité manquante dans la base";
			}

			$tab_adresse[0]['adresse1']=$tab_ele['resp'][0]['adr1'];
			if($tab_ele['resp'][0]['adr2']!=""){
				$tab_adresse[0]['adresse2']=$tab_ele['resp'][0]['adr2'];
			}
			if($tab_ele['resp'][0]['adr3']!=""){
				$tab_adresse[0]['adresse3']=$tab_ele['resp'][0]['adr3'];
			}
			$tab_adresse[0]['cp_ville']=$tab_ele['resp'][0]['cp']." ".$tab_ele['resp'][0]['commune'];

			if(($tab_ele['resp'][0]['pays']!="")&&(mb_strtolower($tab_ele['resp'][0]['pays'])!=mb_strtolower($gepiSchoolPays))) {
				$tab_adresse[0]['pays']=$tab_ele['resp'][0]['pays'];
			}
		}
	}

return $tab_adresse;
}
?>
