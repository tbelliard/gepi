<?php
/*
$Id$
*/

// Paramètres concernant le délais avant affichage d'une infobulle via delais_afficher_div()
// Hauteur de la bande testée pour la position de la souris:
$hauteur_survol_infobulle=20;
// Largeur de la bande testée pour la position de la souris:
$largeur_survol_infobulle=100;
// Délais en ms avant affichage:
$delais_affichage_infobulle=500;

function p_nom($ele_login,$mode="pn") {
	$sql="SELECT * FROM eleves e WHERE e.login='".$ele_login."';";
	$res_ele=mysql_query($sql);
	if(mysql_num_rows($res_ele)>0) {
		$lig_ele=mysql_fetch_object($res_ele);
		if($mode=="pn") {
			return ucfirst(strtolower($lig_ele->prenom))." ".strtoupper($lig_ele->nom);
		}
		else {
			return strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom));
		}
	}
	else {
		return "LOGIN INCONNU";
	}
}

function u_p_nom($u_login) {
	$sql="SELECT nom,prenom,civilite,statut FROM utilisateurs WHERE login='$u_login';";
	//echo "$sql<br />\n";
	$res3=mysql_query($sql);
	if(mysql_num_rows($res3)>0) {
		$lig3=mysql_fetch_object($res3);
		//echo ucfirst(strtolower($lig3->prenom))." ".strtoupper($lig3->nom);
		return $lig3->civilite." ".strtoupper($lig3->nom)." ".ucfirst(substr($lig3->prenom,0,1)).".";
	}
	else {
		return "LOGIN INCONNU";
	}
}

function get_lieu_from_id($id_lieu) {
	$sql="SELECT lieu FROM s_lieux_incidents WHERE id='$id_lieu';";
	$res_lieu_incident=mysql_query($sql);
	if(mysql_num_rows($res_lieu_incident)>0) {
		$lig_lieu_incident=mysql_fetch_object($res_lieu_incident);
		return $lig_lieu_incident->lieu;
	}
	else {
		return "";
	}
}

function formate_date_mysql($date){
	$tab_date=explode("/",$date);

	return $tab_date[2]."-".sprintf("%02d",$tab_date[1])."-".sprintf("%02d",$tab_date[0]);
}

function secondes_to_hms($secondes) {
	$h=floor($secondes/3600);
	$m=floor(($secondes-$h*3600)/60);
	$s=$secondes-$m*60-$h*3600;

	return sprintf("%02d",$h).":".sprintf("%02d",$m).":".sprintf("%02d",$s);
}

function infobulle_photo($eleve_login) {
	global $tabdiv_infobulle;

	$retour="";

	$sql="SELECT elenoet, nom, prenom FROM eleves WHERE login='$eleve_login';";
	$res_ele=mysql_query($sql);
	$lig_ele=mysql_fetch_object($res_ele);
	$eleve_elenoet=$lig_ele->elenoet;
	$eleve_nom=$lig_ele->nom;
	$eleve_prenom=$lig_ele->prenom;

	// Photo...
	$photo=nom_photo($eleve_elenoet);
	//$temoin_photo="";
	if("$photo"!=""){
		$titre="$eleve_nom $eleve_prenom";

		$texte="<div align='center'>\n";
		$texte.="<img src='../photos/eleves/".$photo."' width='150' alt=\"$eleve_nom $eleve_prenom\" />";
		$texte.="<br />\n";
		$texte.="</div>\n";

		$temoin_photo="y";

		$tabdiv_infobulle[]=creer_div_infobulle('photo_'.$eleve_login,$titre,"",$texte,"",14,0,'y','y','n','n');

		$retour.=" <a href='#' onmouseover=\"delais_afficher_div('photo_$eleve_login','y',-100,20,1000,20,20);\"";
		$retour.=">";
		$retour.="<img src='../images/icons/buddy.png' alt='$eleve_nom $eleve_prenom' />";
		$retour.="</a>";
	}

	return $retour;
}

function affiche_mesures_incident($id_incident) {
	global $possibilite_prof_clore_incident;
	global $mesure_demandee_non_validee;
	//global $exclusion_demandee_non_validee;
	//global $retenue_demandee_non_validee;

	$texte="";

	$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident' AND sti.id_mesure=s.id AND s.type='prise' ORDER BY login_ele";
	//$texte.="<br />$sql";
	$res_t_incident=mysql_query($sql);

	$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident' AND sti.id_mesure=s.id AND s.type='demandee' ORDER BY login_ele";
	//$texte.="<br />$sql";
	$res_t_incident2=mysql_query($sql);

	if((mysql_num_rows($res_t_incident)>0)||
		(mysql_num_rows($res_t_incident2)>0)) {
		//$texte.="<br /><table class='boireaus' summary='Mesures' style='margin:1px;'>";
		$texte.="<table class='boireaus' summary='Mesures' style='margin:1px;'>";
	}

	if(mysql_num_rows($res_t_incident)>0) {
		$texte.="<tr class='lig-1'>";
		$texte.="<td style='font-size:x-small; vertical-align:top;' rowspan='".mysql_num_rows($res_t_incident)."'>";
		if(mysql_num_rows($res_t_incident)==1) {
			$texte.="Mesure prise&nbsp;:";
		}
		else {
			$texte.="Mesures prises&nbsp;:";
		}
		$texte.="</td>";
		//$texte.="<td>";
		$cpt_tmp=0;
		while($lig_t_incident=mysql_fetch_object($res_t_incident)) {
			if($cpt_tmp>0) {$texte.="<tr class='lig-1'>\n";}
			$texte.="<td style='font-size:x-small;'>";
			$texte.=p_nom($lig_t_incident->login_ele);

			$tmp_tab=get_class_from_ele_login($lig_t_incident->login_ele);
			if(isset($tmp_tab['liste_nbsp'])) {
				$texte.=" (<em>".$tmp_tab['liste_nbsp']."</em>)";
			}

			$texte.="</td>\n";
			$texte.="<td style='font-size:x-small;'>";
			$texte.="$lig_t_incident->mesure";
			$texte.="</td>\n";
			$texte.="</tr>\n";
			$cpt_tmp++;
		}
		//$texte.="</td>\n";
		//$texte.="</tr>\n";
	}

	//$possibilite_prof_clore_incident='y';
	if(mysql_num_rows($res_t_incident2)>0) {
		if($_SESSION['statut']=='professeur') {$possibilite_prof_clore_incident='n';}
		$texte.="<tr class='lig1'>";
		//$texte.="<td style='font-size:x-small; vertical-align:top;'>";
		$texte.="<td style='font-size:x-small; vertical-align:top;' rowspan='".mysql_num_rows($res_t_incident2)."'>";
		if(mysql_num_rows($res_t_incident2)==1) {
			$texte.="Mesure demandée&nbsp;:";
		}
		else {
			$texte.="Mesures demandées&nbsp;:";
		}
		$texte.="</td>";
		//$texte.="<td>";
		$cpt_tmp=0;
		while($lig_t_incident=mysql_fetch_object($res_t_incident2)) {
			if($cpt_tmp>0) {$texte.="<tr class='lig1'>\n";}
			$texte.="<td style='font-size:x-small;'>";
			$texte.=p_nom($lig_t_incident->login_ele);

			$tmp_tab=get_class_from_ele_login($lig_t_incident->login_ele);
			if(isset($tmp_tab['liste_nbsp'])) {
				$texte.=" (<em>".$tmp_tab['liste_nbsp']."</em>)";
			}

			$texte.="</td>\n";
			$texte.="<td style='font-size:x-small;'>";
			$texte.="$lig_t_incident->mesure";
			$texte.="</td>\n";
			$texte.="</tr>\n";

			if(strtolower($lig_t_incident->mesure)=='retenue') {
				$sql="SELECT 1=1 FROM s_retenues sr, s_sanctions s WHERE s.id_sanction=sr.id_sanction AND s.id_incident='$id_incident' AND s.login='$lig_t_incident->login_ele';";
				//$texte.="<tr><td>$sql</td></tr>";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0) {
					$mesure_demandee_non_validee="y";
					//$retenue_demandee_non_validee="y";
				}
			}
			elseif(strtolower($lig_t_incident->mesure)=='exclusion') {
				$sql="SELECT 1=1 FROM s_exclusions se, s_sanctions s WHERE s.id_sanction=se.id_sanction AND s.id_incident='$id_incident' AND s.login='$lig_t_incident->login_ele';";
				//$texte.="<tr><td>$sql</td></tr>";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0) {
					$mesure_demandee_non_validee="y";
					//$exclusion_demandee_non_validee="y";
				}
			}
			else {
				$sql="SELECT 1=1 FROM s_types_sanctions sts WHERE sts.nature='".addslashes($lig_t_incident->mesure)."';";
				//$texte.="<tr><td>$sql</td></tr>";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0) {
					// Il existe un nom de sanction correspondant au nom de la mesure demandée.

					$sql="SELECT 1=1 FROM s_autres_sanctions sa, s_types_sanctions sts, s_sanctions s WHERE s.id_sanction=sa.id_sanction AND sa.id_nature=sts.id_nature AND sts.nature='".addslashes($lig_t_incident->mesure)."' AND s.id_incident='$id_incident' AND s.login='$lig_t_incident->login_ele';";
					//$texte.="<tr><td>$sql</td></tr>";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)==0) {
						$mesure_demandee_non_validee="y";
					}
				}
			}

			$cpt_tmp++;
		}
		//$texte.="</td>\n";
		//$texte.="</tr>\n";
	}

	if((mysql_num_rows($res_t_incident)>0)||
		(mysql_num_rows($res_t_incident2)>0)) {
		$texte.="</table>";
	}

	return $texte;
}

function rappel_incident($id_incident) {
	echo "<p class='bold'>Rappel de l'incident";
	if(isset($id_incident)) {
		echo " n°$id_incident";

		$sql="SELECT declarant FROM s_incidents WHERE id_incident='$id_incident';";
		$res_dec=mysql_query($sql);
		if(mysql_num_rows($res_dec)>0) {
			$lig_dec=mysql_fetch_object($res_dec);
			echo " (<span style='font-size:x-small; font-style:italic;'>signalé par ".u_p_nom($lig_dec->declarant)."</span>)";
		}
	}
	echo "&nbsp;:</p>\n";
	echo "<blockquote>\n";

	$sql="SELECT * FROM s_incidents WHERE id_incident='$id_incident';";
	//echo "$sql<br />\n";
	$res_incident=mysql_query($sql);
	if(mysql_num_rows($res_incident)>0) {
		$lig_incident=mysql_fetch_object($res_incident);

		echo "<table class='boireaus' border='1' summary='Incident'>\n";
		echo "<tr class='lig1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Date: </td><td style='text-align:left;'>".formate_date($lig_incident->date)."</td></tr>\n";
		echo "<tr class='lig-1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure: </td><td style='text-align:left;'>$lig_incident->heure</td></tr>\n";

		echo "<tr class='lig1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Lieu: </td><td style='text-align:left;'>";
		/*
		$sql="SELECT lieu FROM s_lieux_incidents WHERE id='$lig_incident->id_lieu';";
		$res_lieu_incident=mysql_query($sql);
		if(mysql_num_rows($res_lieu_incident)>0) {
			$lig_lieu_incident=mysql_fetch_object($res_incident);
			echo $lig_lieu_incident->lieu;
		}
		*/
		echo get_lieu_from_id($lig_incident->id_lieu);
		echo "</td></tr>\n";

		echo "<tr class='lig-1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Nature: </td><td style='text-align:left;'>$lig_incident->nature</td></tr>\n";
		echo "<tr class='lig1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Description: </td><td style='text-align:left;'>".nl2br($lig_incident->description)."</td></tr>\n";

		/*
		$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident' AND sti.id_mesure=s.id;";
		$res_t_incident=mysql_query($sql);
		if(mysql_num_rows($res_t_incident)>0) {
			echo "<tr class='lig-1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Mesures&nbsp;: </td>\n";
			echo "<td style='text-align:left;'>";
			while($lig_t_incident=mysql_fetch_object($res_t_incident)) {
				echo "$lig_t_incident->mesure (<em style='color:green;'>mesure $lig_t_incident->type</em>)<br />";
			}
			echo "</td>\n";
			echo "</tr>\n";
		}
		*/
		$texte=affiche_mesures_incident($lig_incident->id_incident);
		if($texte!='') {
			echo "<tr class='lig-1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Mesures&nbsp;: </td>\n";
			echo "<td style='text-align:left;'>";
			echo $texte;
			echo "</td>\n";
			echo "</tr>\n";
		}

		echo "</table>\n";
	}
	else {
		echo "<p>L'incident n°$id_incident ne semble pas enregistré???</p>\n";
	}
	echo "</blockquote>\n";
}

function tab_lignes_adresse($ele_login) {
	global $gepiSchoolPays;

	unset($tab_adr_ligne1);
	unset($tab_adr_ligne2);
	unset($tab_adr_ligne3);

	$sql="SELECT * FROM resp_adr ra, resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND r.ele_id=e.ele_id AND r.pers_id=rp.pers_id AND rp.adr_id=ra.adr_id AND (r.resp_legal='1' OR r.resp_legal='2') ORDER BY resp_legal;";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		return "Aucune adresse de responsable pour cet élève.";
	}
	else {
		$tab_resp=array();
		while($lig=mysql_fetch_object($res)) {
			$num=$lig->resp_legal-1;

			$tab_resp[$num]=array();

			//$tab_resp[$num]['pers_id']=$lig->pers_id;
			$tab_resp[$num]['nom']=$lig->nom;
			$tab_resp[$num]['prenom']=$lig->prenom;
			$tab_resp[$num]['civilite']=$lig->civilite;

			$tab_resp[$num]['adr_id']=$lig->adr_id;
			$tab_resp[$num]['adr1']=$lig->adr1;
			$tab_resp[$num]['adr2']=$lig->adr2;
			$tab_resp[$num]['adr3']=$lig->adr3;
			$tab_resp[$num]['adr4']=$lig->adr4;
			$tab_resp[$num]['cp']=$lig->cp;
			$tab_resp[$num]['commune']=$lig->commune;
			$tab_resp[$num]['pays']=$lig->pays;

		}

		// Préparation des lignes adresse responsable
		if (!isset($tab_resp[0])) {
			$tab_adr_ligne1[0]="<font color='red'><b>ADRESSE MANQUANTE</b></font>";
			$tab_adr_ligne2[0]="";
			$tab_adr_ligne3[0]="";
		}
		else {
			if (isset($tab_resp[1])) {
				if((isset($tab_resp[1]['adr1']))&&
					(isset($tab_resp[1]['adr2']))&&
					(isset($tab_resp[1]['adr3']))&&
					(isset($tab_resp[1]['adr4']))&&
					(isset($tab_resp[1]['cp']))&&
					(isset($tab_resp[1]['commune']))
				) {
					// Le deuxième responsable existe et est renseigné
					if (($tab_resp[0]['adr_id']==$tab_resp[1]['adr_id']) OR
						(
							($tab_resp[0]['adr1']==$tab_resp[1]['adr1'])&&
							($tab_resp[0]['adr2']==$tab_resp[1]['adr2'])&&
							($tab_resp[0]['adr3']==$tab_resp[1]['adr3'])&&
							($tab_resp[0]['adr4']==$tab_resp[1]['adr4'])&&
							($tab_resp[0]['cp']==$tab_resp[1]['cp'])&&
							($tab_resp[0]['commune']==$tab_resp[1]['commune'])
						)
					) {
						// Les adresses sont identiques
						$nb_bulletins=1;

						if(($tab_resp[0]['nom']!=$tab_resp[1]['nom'])&&
							($tab_resp[1]['nom']!="")) {
							// Les noms des responsables sont différents
							//$tab_adr_ligne1[0]=$tab_resp[0]['civilite']." ".$tab_resp[0]['nom']." ".$tab_resp[0]['prenom']." et ".$tab_resp[1]['civilite']." ".$tab_resp[1]['nom']." ".$tab_resp[1]['prenom'];
							$tab_adr_ligne1[0]=$tab_resp[0]['civilite']." ".$tab_resp[0]['nom']." ".$tab_resp[0]['prenom'];
							//$tab_adr_ligne1[0].=" et ";
							$tab_adr_ligne1[0].="<br />\n";
							$tab_adr_ligne1[0].="et ";
							$tab_adr_ligne1[0].=$tab_resp[1]['civilite']." ".$tab_resp[1]['nom']." ".$tab_resp[1]['prenom'];
						}
						else{
							if(($tab_resp[0]['civilite']!="")&&($tab_resp[1]['civilite']!="")) {
								$tab_adr_ligne1[0]=$tab_resp[0]['civilite']." et ".$tab_resp[1]['civilite']." ".$tab_resp[0]['nom']." ".$tab_resp[0]['prenom'];
							}
							else {
								$tab_adr_ligne1[0]="M. et Mme ".$tab_resp[0]['nom']." ".$tab_resp[0]['prenom'];
							}
						}

						$tab_adr_ligne2[0]=$tab_resp[0]['adr1'];
						if($tab_resp[0]['adr2']!=""){
							$tab_adr_ligne2[0].="<br />\n".$tab_resp[0]['adr2'];
						}
						if($tab_resp[0]['adr3']!=""){
							$tab_adr_ligne2[0].="<br />\n".$tab_resp[0]['adr3'];
						}
						if($tab_resp[0]['adr4']!=""){
							$tab_adr_ligne2[0].="<br />\n".$tab_resp[0]['adr4'];
						}
						$tab_adr_ligne3[0]=$tab_resp[0]['cp']." ".$tab_resp[0]['commune'];

						if(($tab_resp[0]['pays']!="")&&(strtolower($tab_resp[0]['pays'])!=strtolower($gepiSchoolPays))) {
							if($tab_adr_ligne3[0]!=" "){
								$tab_adr_ligne3[0].="<br />";
							}
							$tab_adr_ligne3[0].=$tab_resp[0]['pays'];
						}
					}
					else {
						// Les adresses sont différentes
						//if ($un_seul_bull_par_famille!="oui") {
						// On teste en plus si la deuxième adresse est valide
						/*
						if (($un_seul_bull_par_famille!="oui")&&
							($tab_resp[1]['adr1']!="")&&
							($tab_resp[1]['commune']!="")
						) {
							$nb_bulletins=2;
						}
						else {
							$nb_bulletins=1;
						}
						*/

						if (($tab_resp[1]['adr1']!="")&&
							($tab_resp[1]['commune']!="")
						) {
							$nb_bulletins=2;
						}
						else {
							$nb_bulletins=1;
						}

						for($cpt=0;$cpt<$nb_bulletins;$cpt++) {
							if($tab_resp[$cpt]['civilite']!="") {
								$tab_adr_ligne1[$cpt]=$tab_resp[$cpt]['civilite']." ".$tab_resp[$cpt]['nom']." ".$tab_resp[$cpt]['prenom'];
							}
							else {
								$tab_adr_ligne1[$cpt]=$tab_resp[$cpt]['nom']." ".$tab_resp[$cpt]['prenom'];
							}

							$tab_adr_ligne2[$cpt]=$tab_resp[$cpt]['adr1'];
							if($tab_resp[$cpt]['adr2']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_resp[$cpt]['adr2'];
							}
							if($tab_resp[$cpt]['adr3']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_resp[$cpt]['adr3'];
							}
							if($tab_resp[$cpt]['adr4']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_resp[$cpt]['adr4'];
							}
							$tab_adr_ligne3[$cpt]=$tab_resp[$cpt]['cp']." ".$tab_resp[$cpt]['commune'];

							if(($tab_resp[$cpt]['pays']!="")&&(strtolower($tab_resp[$cpt]['pays'])!=strtolower($gepiSchoolPays))) {
								if($tab_adr_ligne3[$cpt]!=" "){
									$tab_adr_ligne3[$cpt].="<br />";
								}
								$tab_adr_ligne3[$cpt].=$tab_resp[$cpt]['pays'];
							}

						}

					}
				}
				else {
					// Il n'y a pas de deuxième adresse, mais il y aurait un deuxième responsable???
					// CA NE DEVRAIT PAS ARRIVER ETANT DONNé LA REQUETE EFFECTUEE QUI JOINT resp_pers ET resp_adr...
						/*
						if ($un_seul_bull_par_famille!="oui") {
							$nb_bulletins=2;
						}
						else {
							$nb_bulletins=1;
						}
						*/
						$nb_bulletins=2;

						for($cpt=0;$cpt<$nb_bulletins;$cpt++) {
							if($tab_resp[$cpt]['civilite']!="") {
								$tab_adr_ligne1[$cpt]=$tab_resp[$cpt]['civilite']." ".$tab_resp[$cpt]['nom']." ".$tab_resp[$cpt]['prenom'];
							}
							else {
								$tab_adr_ligne1[$cpt]=$tab_resp[$cpt]['nom']." ".$tab_resp[$cpt]['prenom'];
							}

							$tab_adr_ligne2[$cpt]=$tab_resp[$cpt]['adr1'];
							if($tab_resp[$cpt]['adr2']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_resp[$cpt]['adr2'];
							}
							if($tab_resp[$cpt]['adr3']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_resp[$cpt]['adr3'];
							}
							if($tab_resp[$cpt]['adr4']!=""){
								$tab_adr_ligne2[$cpt].="<br />\n".$tab_resp[$cpt]['adr4'];
							}
							$tab_adr_ligne3[$cpt]=$tab_resp[$cpt]['cp']." ".$tab_resp[$cpt]['commune'];

							if(($tab_resp[$cpt]['pays']!="")&&(strtolower($tab_resp[$cpt]['pays'])!=strtolower($gepiSchoolPays))) {
								if($tab_adr_ligne3[$cpt]!=" "){
									$tab_adr_ligne3[$cpt].="<br />";
								}
								$tab_adr_ligne3[$cpt].=$tab_resp[$cpt]['pays'];
							}
						}
				}
			}
			else {
				// Il n'y a pas de deuxième responsable
				$nb_bulletins=1;

				if($tab_resp[0]['civilite']!="") {
					$tab_adr_ligne1[0]=$tab_resp[0]['civilite']." ".$tab_resp[0]['nom']." ".$tab_resp[0]['prenom'];
				}
				else {
					$tab_adr_ligne1[0]=$tab_resp[0]['nom']." ".$tab_resp[0]['prenom'];
				}

				$tab_adr_ligne2[0]=$tab_resp[0]['adr1'];
				if($tab_resp[0]['adr2']!=""){
					$tab_adr_ligne2[0].="<br />\n".$tab_resp[0]['adr2'];
				}
				if($tab_resp[0]['adr3']!=""){
					$tab_adr_ligne2[0].="<br />\n".$tab_resp[0]['adr3'];
				}
				if($tab_resp[0]['adr4']!=""){
					$tab_adr_ligne2[0].="<br />\n".$tab_resp[0]['adr4'];
				}
				$tab_adr_ligne3[0]=$tab_resp[0]['cp']." ".$tab_resp[0]['commune'];

				if(($tab_resp[0]['pays']!="")&&(strtolower($tab_resp[0]['pays'])!=strtolower($gepiSchoolPays))) {
					if($tab_adr_ligne3[0]!=" "){
						$tab_adr_ligne3[0].="<br />";
					}
					$tab_adr_ligne3[0].=$tab_resp[0]['pays'];
				}
			}
		}

		$tab_adresses=array($tab_adr_ligne1,$tab_adr_ligne2,$tab_adr_ligne3);
		return $tab_adresses;
	}
}

function tab_mod_discipline($ele_login,$mode,$date_debut,$date_fin) {
	$retour="";

	if($date_debut!="") {
		// Tester la validité de la date
		// Si elle n'est pas valide... la vider
		if(my_ereg("/",$date_debut)) {
			$tmp_tab_date=explode("/",$date_debut);

			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[0],$tmp_tab_date[2])) {
				$date_debut="";
			}
			else {
				$date_debut=$tmp_tab_date[2]."-".$tmp_tab_date[1]."-".$tmp_tab_date[0];
			}
		}
		elseif(my_ereg("-",$date_debut)) {
			$tmp_tab_date=explode("-",$date_debut);
	
			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[2],$tmp_tab_date[0])) {
				$date_debut="";
			}
		}
		else {
			$date_debut="";
		}
	}

	if($date_fin!="") {
		// Tester la validité de la date
		// Si elle n'est pas valide... la vider
		// Tester la validité de la date
		// Si elle n'est pas valide... la vider
		if(my_ereg("/",$date_fin)) {
			$tmp_tab_date=explode("/",$date_fin);

			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[0],$tmp_tab_date[2])) {
				$date_fin="";
			}
			else {
				$date_fin=$tmp_tab_date[2]."-".$tmp_tab_date[1]."-".$tmp_tab_date[0];
			}
		}
		elseif(my_ereg("-",$date_fin)) {
			$tmp_tab_date=explode("-",$date_fin);
	
			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[2],$tmp_tab_date[0])) {
				$date_fin="";
			}
		}
		else {
			$date_fin="";
		}
	}

	$restriction_date="";
	if(($date_debut!="")&&($date_fin!="")) {
		$restriction_date.=" AND (si.date>='$date_debut' AND si.date<='$date_fin') ";
	}
	elseif($date_debut!="") {
		$restriction_date.=" AND (si.date>='$date_debut') ";
	}
	elseif($date_fin!="") {
		$restriction_date.=" AND (si.date<='$date_fin') ";
	}

	$tab_incident=array();
	$tab_sanction=array();
	$tab_mesure=array();

	$sql="SELECT * FROM s_incidents si, s_protagonistes sp WHERE si.id_incident=sp.id_incident AND sp.login='$ele_login' $restriction_date ORDER BY si.date DESC;";
	//echo "$sql<br />\n";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$retour="<p>Tableau des incidents concernant ".p_nom($ele_login)."</p>\n";
		$retour.="<table class='boireaus' border='1' summary='Tableau des incidents concernant $ele_login'>\n";
		$retour.="<tr>\n";
		$retour.="<th>Num</th>\n";
		$retour.="<th>Date</th>\n";
		$retour.="<th>Qualité</th>\n";
		$retour.="<th>Description</th>\n";
		$retour.="<th>Suivi</th>\n";
		$retour.="</tr>\n";
		$alt_1=1;
		while($lig=mysql_fetch_object($res)) {
			$alt_1=$alt_1*(-1);
			$retour.="<tr class='lig$alt_1'>\n";

				$retour.="<td>".$lig->id_incident."</td>\n";

				// Modifier l'accès Consultation d'incident... on ne voit actuellement que ses propres incidents
				//$retour.="<td><a href='' target='_blank'>".$lig->id_incident."</a></td>\n";

			$retour.="<td>".formate_date($lig->date);

			$retour.="<br />\n";

			$retour.="<span style='font-size:small;'>".u_p_nom($lig->declarant)."</span>";

			$retour.="</td>\n";
			$retour.="<td>".$lig->qualite."</td>\n";
			$temoin_eleve_responsable_de_l_incident='n';
			if(strtolower(strtolower($lig->qualite)=='responsable')) {
				if(isset($tab_incident[addslashes($lig->nature)])) {
					$tab_incident[addslashes($lig->nature)]++;
				}
				else {
					$tab_incident[addslashes($lig->nature)]=1;
				}
				$temoin_eleve_responsable_de_l_incident='y';
			}

			$retour.="<td>";
			$retour.="<p style='font-weight: bold;'>".$lig->nature."</p>\n";
			$retour.="<p>".$lig->description."</p>\n";
			/*
			$sql="SELECT * FROM s_protagonistes WHERE id_incident='$lig->id_incident' ORDER BY qualite;";
			$res_prot=mysql_query($sql);
			if(mysql_num_rows($res_prot)>0) {
				$retour.="<p>";
				while($lig_prot=mysql_fetch_object($res_prot)) {
					$retour.=$lig_prot->login." (<i>".$lig_prot->qualite."</i>)<br />\n";
				}
				$retour.="</p>\n";
			}
			*/
			$retour.="</td>\n";

			$retour.="<td style='padding: 2px;'>";

			$sql="SELECT * FROM s_protagonistes WHERE id_incident='$lig->id_incident' ORDER BY qualite;";
			$res_prot=mysql_query($sql);
			if(mysql_num_rows($res_prot)>0) {
				$retour.="<table class='boireaus' border='1' summary='Protagonistes de l incident n°$lig->id_incident'>\n";

				$alt_2=1;
				while($lig_prot=mysql_fetch_object($res_prot)) {
					$alt_2=$alt_2*(-1);
					$retour.="<tr class='lig$alt_2'>\n";
					$retour.="<td>".p_nom($lig_prot->login)."</td>\n";
					$retour.="<td>".$lig_prot->qualite."</td>\n";

					$retour.="<td style='padding: 3px;'>\n";
					$alt=1;

					$sql="SELECT * FROM s_traitement_incident sti, s_mesures sm WHERE sti.id_incident='$lig->id_incident' AND sti.login_ele='$lig_prot->login' AND sm.id=sti.id_mesure ORDER BY mesure;";
					//echo "$sql<br />\n";
					$res_suivi=mysql_query($sql);
					if(mysql_num_rows($res_suivi)>0) {

						//$retour.="<p style='text-align:left;'>Tableau des mesures pour le protagoniste $lig_prot->login de l incident n°$lig->id_incident</p>\n";
						$retour.="<p style='text-align:left; font-weight: bold;'>Mesures</p>\n";

						$retour.="<table class='boireaus' border='1' summary='Tableau des mesures pour le protagoniste $lig_prot->login de l incident n°$lig->id_incident'>\n";

						$retour.="<tr>\n";
						$retour.="<th>Nature</th>\n";
						$retour.="<th>Mesure</th>\n";
						$retour.="</tr>\n";

						while($lig_suivi=mysql_fetch_object($res_suivi)) {
							$alt=$alt*(-1);
							$retour.="<tr class='lig$alt'>\n";
							$retour.="<td>$lig_suivi->mesure</td>\n";
							if($lig_suivi->type=='prise') {
								$retour.="<td>prise par ".u_p_nom($lig_suivi->login_u)."</td>\n";

								if($temoin_eleve_responsable_de_l_incident=='y') {
									if(isset($tab_mesure[addslashes($lig_suivi->mesure)])) {
										$tab_mesure[addslashes($lig_suivi->mesure)]++;
									}
									else {
										$tab_mesure[addslashes($lig_suivi->mesure)]=1;
									}
								}
							}
							else {
								$retour.="<td>demandée par ".u_p_nom($lig_suivi->login_u)."</td>\n";
							}
							$retour.="</tr>\n";
						}
						$retour.="</table>\n";

					}

					$sql="SELECT * FROM s_sanctions s WHERE s.id_incident='$lig->id_incident' AND s.login='$lig_prot->login' ORDER BY nature;";
					//echo "$sql<br />\n";
					$res_suivi=mysql_query($sql);
					if(mysql_num_rows($res_suivi)>0) {

						//$retour.="<p style='text-align:left;'>Tableau des sanctions pour le protagoniste $lig_prot->login de l incident n°$lig->id_incident</p>\n";
						$retour.="<p style='text-align:left; font-weight: bold;'>Sanctions</p>\n";

						$retour.="<table class='boireaus' border='1' summary='Tableau des sanctions pour le protagoniste $lig_prot->login de l incident n°$lig->id_incident'>\n";

						$retour.="<tr>\n";
						$retour.="<th>Nature</th>\n";
						$retour.="<th>Date</th>\n";
						$retour.="<th>Description</th>\n";
						$retour.="<th>Effectuée</th>\n";
						$retour.="</tr>\n";

						while($lig_suivi=mysql_fetch_object($res_suivi)) {
							$alt=$alt*(-1);
							$retour.="<tr class='lig$alt'>\n";
							$retour.="<td>$lig_suivi->nature</td>\n";
							$retour.="<td>";

							if($temoin_eleve_responsable_de_l_incident=='y') {
								if(isset($tab_sanction[addslashes($lig_suivi->nature)])) {
									$tab_sanction[addslashes($lig_suivi->nature)]++;
								}
								else {
									$tab_sanction[addslashes($lig_suivi->nature)]=1;
								}
							}

							if($lig_suivi->nature=='retenue') {
								$sql="SELECT * FROM s_retenues WHERE id_sanction='$lig_suivi->id_sanction';";
								$res_retenue=mysql_query($sql);
								if(mysql_num_rows($res_retenue)>0) {
									$lig_retenue=mysql_fetch_object($res_retenue);
									$retour.=formate_date($lig_retenue->date)." (<i>".$lig_retenue->duree."H</i>)";
								}
								else {
									$retour.="X";
								}
							}
							elseif($lig_suivi->nature=='exclusion') {
								$sql="SELECT * FROM s_exclusions WHERE id_sanction='$lig_suivi->id_sanction';";
								$res_exclusion=mysql_query($sql);
								if(mysql_num_rows($res_exclusion)>0) {
									$lig_exclusion=mysql_fetch_object($res_exclusion);
									$retour.="du ".formate_date($lig_exclusion->date_debut)." (<i>$lig_exclusion->heure_debut</i>) au ".formate_date($lig_exclusion->date_fin)." (<i>$lig_exclusion->heure_fin</i>)<br />$lig_exclusion->lieu";
								}
								else {
									$retour.="X";
								}
							}
							elseif($lig_suivi->nature=='travail') {
								$sql="SELECT * FROM s_travail WHERE id_sanction='$lig_suivi->id_sanction';";
								$res_travail=mysql_query($sql);
								if(mysql_num_rows($res_travail)>0) {
									$lig_travail=mysql_fetch_object($res_travail);
									$retour.="pour le ".formate_date($lig_travail->date_retour)."  (<i>$lig_travail->heure_retour</i>)";
								}
								else {
									$retour.="X";
								}
							}

							$retour.="</td>\n";
							$retour.="<td>$lig_suivi->description</td>\n";
							$retour.="<td>$lig_suivi->effectuee</td>\n";
							$retour.="</tr>\n";
						}
						$retour.="</table>\n";

					}

					$retour.="</td>\n";

					$retour.="</tr>\n";
				}
				$retour.="</table>\n";
			}

			$retour.="</td>\n";
		}
		$retour.="</table>\n";

		// Totaux
		$retour.="<p style='font-weight: bold;'>Totaux des incidents/mesures/sanctions en tant que Responsable.</p>\n";

		$retour.="<div style='float:left; width:33%;'>\n";
		$retour.="<p style='font-weight: bold;'>Incidents</p>\n";
		if(count($tab_incident)>0) {
			$retour.="<table class='boireaus' border='1' summary='Totaux incidents'>\n";
			$retour.="<tr><th>Nature</th><th>Total</th></tr>\n";
			$alt=1;
			foreach($tab_incident as $key => $value) {
				$alt=$alt*(-1);
				$retour.="<tr class='lig$alt'><td>".stripslashes($key)."</td><td>".stripslashes($value)."</td></tr>\n";
			}
			$retour.="</table>\n";
		}
		else {
			$retour.="<p>Aucun incident relevé en qualité de responsable.</p>\n";
		}
		$retour.="</div>\n";

		$retour.="<div style='float:left; width:33%;'>\n";
		if(count($tab_mesure)>0) {
			$retour.="<p style='font-weight: bold;'>Mesures prises</p>\n";
			$retour.="<table class='boireaus' border='1' summary='Totaux mesures prises'>\n";
			$retour.="<tr><th>Mesure</th><th>Total</th></tr>\n";
			$alt=1;
			foreach($tab_mesure as $key => $value) {
				$alt=$alt*(-1);
				$retour.="<tr class='lig$alt'><td>".stripslashes($key)."</td><td>".stripslashes($value)."</td></tr>\n";
			}
			$retour.="</table>\n";
		}
		else {
			$retour.="<p>Aucune mesure prise en qualité de responsable.</p>\n";
		}
		$retour.="</div>\n";

		$retour.="<div style='float:left; width:33%;'>\n";
		$retour.="<p style='font-weight: bold;'>Sanctions</p>\n";
		if(count($tab_sanction)>0) {
			$retour.="<table class='boireaus' border='1' summary='Totaux sanctions'>\n";
			$retour.="<tr><th>Nature</th><th>Total</th></tr>\n";
			$alt=1;
			foreach($tab_sanction as $key => $value) {
				$alt=$alt*(-1);
				$retour.="<tr class='lig$alt'><td>".stripslashes($key)."</td><td>".stripslashes($value)."</td></tr>\n";
			}
			$retour.="</table>\n";
		}
		else {
			$retour.="<p>Aucune mesure prise en qualité de responsable.</p>\n";
		}
		$retour.="</div>\n";

		$retour.="<div style='clear:both;'></div>\n";

	}
	else {
		$retour="<p>Aucun incident relevé.</p>\n";
	}

	return $retour;
}

?>