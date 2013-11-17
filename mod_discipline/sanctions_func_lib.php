<?php
/*
*/

// Initialisations diverses pour le module Discipline
$mod_disc_terme_incident=getSettingValue('mod_disc_terme_incident');
if($mod_disc_terme_incident=="") {$mod_disc_terme_incident="incident";}

$mod_disc_terme_sanction=getSettingValue('mod_disc_terme_sanction');
if($mod_disc_terme_sanction=="") {$mod_disc_terme_sanction="sanction";}

// Paramètres concernant le délais avant affichage d'une infobulle via delais_afficher_div()
// Hauteur de la bande testée pour la position de la souris:
$hauteur_survol_infobulle=20;
// Largeur de la bande testée pour la position de la souris:
$largeur_survol_infobulle=100;
// Délais en ms avant affichage:
$delais_affichage_infobulle=500;

// Familles de sanctions:
$types_autorises=array('exclusion', 'retenue', 'travail', 'autre');

$dossier_documents_discipline="documents/discipline";
if(((isset($multisite))&&($multisite=='y'))||(getSettingValue('multisite')=='y')) {
	if(isset($_COOKIE['RNE'])) {
		$dossier_documents_discipline.="_".$_COOKIE['RNE'];
		if(!file_exists("../$dossier_documents_discipline")) {
			@mkdir("../$dossier_documents_discipline",0770);
		}
	}
}

function p_nom($ele_login,$mode="pn") {
	$sql="SELECT * FROM eleves e WHERE e.login='".$ele_login."';";
	$res_ele=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res_ele)>0) {
		$lig_ele=mysqli_fetch_object($res_ele);
		if($mode=="pn") {
			return ucfirst(mb_strtolower($lig_ele->prenom))." ".mb_strtoupper($lig_ele->nom);
		}
		else {
			return mb_strtoupper($lig_ele->nom)." ".ucfirst(mb_strtolower($lig_ele->prenom));
		}
	}
	else {
		return "LOGIN INCONNU";
	}
}

function u_p_nom($u_login) {
	$sql="SELECT nom,prenom,civilite,statut FROM utilisateurs WHERE login='$u_login';";
	//echo "$sql<br />\n";
	$res3=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res3)>0) {
		$lig3=mysqli_fetch_object($res3);
		return $lig3->civilite." ".mb_strtoupper($lig3->nom)." ".ucfirst(mb_substr($lig3->prenom,0,1)).".";
	}
	else {
		return "LOGIN INCONNU";
	}
}

function get_lieu_from_id($id_lieu) {
	$sql="SELECT lieu FROM s_lieux_incidents WHERE id='$id_lieu';";
	$res_lieu_incident=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res_lieu_incident)>0) {
		$lig_lieu_incident=mysqli_fetch_object($res_lieu_incident);
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

	$sql="SELECT elenoet, nom, prenom, sexe FROM eleves WHERE login='$eleve_login';";
	$res_ele=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$lig_ele=mysqli_fetch_object($res_ele);
	$eleve_elenoet=$lig_ele->elenoet;
	$eleve_nom=$lig_ele->nom;
	$eleve_prenom=$lig_ele->prenom;
	$eleve_sexe=$lig_ele->sexe;

	// Photo...
	$photo=nom_photo($eleve_elenoet);
	//$temoin_photo="";
	//if("$photo"!=""){
	if($photo){
		$titre="$eleve_nom $eleve_prenom";

		$texte="<div align='center'>\n";
		$texte.="<img src='".$photo."' width='150' alt=\"$eleve_nom $eleve_prenom\" />";
		$texte.="<br />\n";
		$texte.="</div>\n";

		$temoin_photo="y";

		$tabdiv_infobulle[]=creer_div_infobulle('photo_'.$eleve_login,$titre,"",$texte,"",14,0,'y','y','n','n');

		$retour.=" <a href='#' onmouseover=\"delais_afficher_div('photo_$eleve_login','y',-100,20,1000,20,20);\"";
		$retour.=">";
		//$retour.="<img src='../images/icons/buddy.png' alt='$eleve_nom $eleve_prenom' />";
		$retour.="<img src='../mod_trombinoscopes/images/";
		if($eleve_sexe=="F") {
			$retour.="photo_f.png";
		}
		else {
			$retour.="photo_g.png";
		}
		$retour.="' class='icone20' alt='$eleve_nom $eleve_prenom' />";
		$retour.="</a>";
	}

	return $retour;
}

function affiche_mesures_incident($id_incident) {
	global $possibilite_prof_clore_incident;
	global $mesure_demandee_non_validee;
	global $dossier_documents_discipline;
	//global $exclusion_demandee_non_validee;
	//global $retenue_demandee_non_validee;

	$texte="";

	$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident' AND sti.id_mesure=s.id AND s.type='prise'";
	$res_t_incident=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$nb_login_ele_mesure_prise=mysqli_num_rows($res_t_incident);

	$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident' AND sti.id_mesure=s.id AND s.type='demandee' ORDER BY login_ele";
	//$texte.="<br />$sql";
	$res_t_incident2=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$nb_login_ele_mesure_demandee=mysqli_num_rows($res_t_incident2);

	if(($nb_login_ele_mesure_prise>0)||
		($nb_login_ele_mesure_demandee>0)) {
		$texte.="<table class='boireaus' summary='Mesures' style='margin:1px;'>\n";
	}

	if($nb_login_ele_mesure_prise>0) {
		$texte.="<tr class='lig-1'>\n";
		$texte.="<td style='font-size:x-small; vertical-align:top;'>\n";
		if(mysqli_num_rows($res_t_incident)==1) {
			$texte.="Mesure prise&nbsp;:";
		}
		else {
			$texte.="Mesures prises&nbsp;:";
		}
		$texte.="</td>\n";
		//$texte.="<td>";
		$texte.="<td style='font-size:x-small;'>\n";

			$texte.="<table class='boireaus'>\n";
			$cpt_tmp=0;
			while($lig_t_incident=mysqli_fetch_object($res_t_incident)) {
				$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident' AND sti.id_mesure=s.id AND s.type='prise' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
				$res_mes_ele=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				$nb_mes_ele=mysqli_num_rows($res_mes_ele);

				$texte.="<tr class='lig-1'>\n";
				$texte.="<td style='font-size:x-small;' rowspan='$nb_mes_ele'>\n";
				$texte.=p_nom($lig_t_incident->login_ele);
				$tmp_tab=get_class_from_ele_login($lig_t_incident->login_ele);
				if(isset($tmp_tab['liste_nbsp'])) {
					$texte.=" (<em>".$tmp_tab['liste_nbsp']."</em>)";
				}
				$texte.="</td>\n";
				$cpt_mes=0;
				while($lig_mes_ele=mysqli_fetch_object($res_mes_ele)) {
					if($cpt_mes>0) {$texte.="<tr>\n";}
					$texte.="<td style='font-size:x-small;'>";
					$texte.="$lig_mes_ele->mesure";
					$texte.="</td>\n";
					$texte.="</tr>\n";
					$cpt_mes++;
				}
				//$texte.="</tr>\n";
				$cpt_tmp++;
			}
			$texte.="</table>\n";

		$texte.="</td>\n";
		$texte.="</tr>\n";
	}

	//$possibilite_prof_clore_incident='y';
	if($nb_login_ele_mesure_demandee>0) {
		if($_SESSION['statut']=='professeur') {$possibilite_prof_clore_incident='n';}
		$texte.="<tr class='lig1'>";
		//$texte.="<td style='font-size:x-small; vertical-align:top;'>";
		//$texte.="<td style='font-size:x-small; vertical-align:top;' rowspan='".mysql_num_rows($res_t_incident2)."'>";
		$texte.="<td style='font-size:x-small; vertical-align:top;'>";
		if(mysqli_num_rows($res_t_incident2)==1) {
			$texte.="Mesure demandée&nbsp;:";
		}
		else {
			$texte.="Mesures demandées&nbsp;:";
		}
		$texte.="</td>";
		//$texte.="<td>";
		$texte.="<td style='font-size:x-small;'>\n";

			$texte.="<table class='boireaus'>\n";
			$cpt_tmp=0;
			while($lig_t_incident=mysqli_fetch_object($res_t_incident2)) {
				$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident' AND sti.id_mesure=s.id AND s.type='demandee' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
				$res_mes_ele=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				$nb_mes_ele=mysqli_num_rows($res_mes_ele);

				$texte.="<tr class='lig1'>\n";
				$texte.="<td style='font-size:x-small;' rowspan='$nb_mes_ele'>\n";
				$texte.=p_nom($lig_t_incident->login_ele);
				$tmp_tab=get_class_from_ele_login($lig_t_incident->login_ele);
				if(isset($tmp_tab['liste_nbsp'])) {
					$texte.=" (<em>".$tmp_tab['liste_nbsp']."</em>)";
				}
				$texte.="</td>\n";
				$cpt_mes=0;
				while($lig_mes_ele=mysqli_fetch_object($res_mes_ele)) {
					if($cpt_mes>0) {$texte.="<tr>\n";}
					$texte.="<td style='font-size:x-small;'>\n";
					$texte.="$lig_mes_ele->mesure";
					$texte.="</td>\n";

					if($cpt_mes==0) {
						$tab_doc_joints=get_documents_joints($id_incident, "mesure", $lig_t_incident->login_ele);
						$chemin="../$dossier_documents_discipline/incident_".$id_incident."/mesures/".$lig_t_incident->login_ele;
						if(count($tab_doc_joints)>0) {
							$texte.="<td rowspan='$nb_mes_ele'>\n";
							for($loop=0;$loop<count($tab_doc_joints);$loop++) {
								$texte.="<a href='$chemin/$tab_doc_joints[$loop]' target='_blank'>$tab_doc_joints[$loop]</a><br />\n";
							}
							$texte.="</td>\n";
						}
					}

					$texte.="</tr>\n";


					// 
					if(mb_strtolower($lig_mes_ele->mesure)=='retenue') {
						$sql="SELECT 1=1 FROM s_retenues sr, s_sanctions s WHERE s.id_sanction=sr.id_sanction AND s.id_incident='$id_incident' AND s.login='$lig_t_incident->login_ele';";
						//$texte.="<tr><td>$sql</td></tr>";
						$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($test)==0) {
							$mesure_demandee_non_validee="y";
							//$retenue_demandee_non_validee="y";
						}
					}
					elseif(mb_strtolower($lig_mes_ele->mesure)=='exclusion') {
						$sql="SELECT 1=1 FROM s_exclusions se, s_sanctions s WHERE s.id_sanction=se.id_sanction AND s.id_incident='$id_incident' AND s.login='$lig_t_incident->login_ele';";
						//$texte.="<tr><td>$sql</td></tr>";
						$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($test)==0) {
							$mesure_demandee_non_validee="y";
							//$exclusion_demandee_non_validee="y";
						}
					}
					else {
						$sql="SELECT 1=1 FROM s_types_sanctions sts WHERE sts.nature='".addslashes($lig_mes_ele->mesure)."';";
						//$texte.="<tr><td>$sql</td></tr>";
						$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($test)>0) {
							// Il existe un nom de sanction correspondant au nom de la mesure demandée.
		
							$sql="SELECT 1=1 FROM s_autres_sanctions sa, s_types_sanctions sts, s_sanctions s WHERE s.id_sanction=sa.id_sanction AND sa.id_nature=sts.id_nature AND sts.nature='".addslashes($lig_mes_ele->mesure)."' AND s.id_incident='$id_incident' AND s.login='$lig_t_incident->login_ele';";
							//$texte.="<tr><td>$sql</td></tr>";
							$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
							if(mysqli_num_rows($test)==0) {
								$mesure_demandee_non_validee="y";
							}
						}
					}

					$cpt_mes++;
				}
	
				$cpt_tmp++;
			}
			$texte.="</table>\n";

		//$texte.="</td>\n";
		//$texte.="</tr>\n";
	}

	if((mysqli_num_rows($res_t_incident)>0)||
		(mysqli_num_rows($res_t_incident2)>0)) {
		$texte.="</table>";
	}

	return $texte;
}

function rappel_incident($id_incident, $mode_retour='echo') {
	global $mod_disc_terme_incident;

	$retour="";

	$retour.="<p class='bold'>Rappel de l'".$mod_disc_terme_incident;
	if(isset($id_incident)) {
		$retour.=" n°$id_incident";

		$sql="SELECT declarant FROM s_incidents WHERE id_incident='$id_incident';";
		$res_dec=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res_dec)>0) {
			$lig_dec=mysqli_fetch_object($res_dec);
			$retour.=" (<span style='font-size:x-small; font-style:italic;'>signalé par ".u_p_nom($lig_dec->declarant)."</span>)";
		}
	}
	$retour.="&nbsp;:</p>\n";
	$retour.="<blockquote>\n";

	$sql="SELECT * FROM s_incidents WHERE id_incident='$id_incident';";
	//$retour.="$sql<br />\n";
	$res_incident=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res_incident)>0) {
		$lig_incident=mysqli_fetch_object($res_incident);

		$retour.="<table class='boireaus' border='1' summary='".ucfirst($mod_disc_terme_incident)."'>\n";
		$retour.="<tr class='lig1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Date: </td><td style='text-align:left;'>".formate_date($lig_incident->date)."</td></tr>\n";
		$retour.="<tr class='lig-1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure: </td><td style='text-align:left;'>$lig_incident->heure</td></tr>\n";

		$retour.="<tr class='lig1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Lieu: </td><td style='text-align:left;'>";
		/*
		$sql="SELECT lieu FROM s_lieux_incidents WHERE id='$lig_incident->id_lieu';";
		$res_lieu_incident=mysql_query($sql);
		if(mysql_num_rows($res_lieu_incident)>0) {
			$lig_lieu_incident=mysql_fetch_object($res_incident);
			$retour.=$lig_lieu_incident->lieu;
		}
		*/
		$retour.=get_lieu_from_id($lig_incident->id_lieu);
		$retour.="</td></tr>\n";

		$retour.="<tr class='lig-1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Nature: </td><td style='text-align:left;'>$lig_incident->nature</td></tr>\n";
		$retour.="<tr class='lig1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Description: </td><td style='text-align:left;'>".nl2br($lig_incident->description)."</td></tr>\n";

		/*
		$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident' AND sti.id_mesure=s.id;";
		$res_t_incident=mysql_query($sql);
		if(mysql_num_rows($res_t_incident)>0) {
			$retour.="<tr class='lig-1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Mesures&nbsp;: </td>\n";
			$retour.="<td style='text-align:left;'>";
			while($lig_t_incident=mysql_fetch_object($res_t_incident)) {
				$retour.="$lig_t_incident->mesure (<em style='color:green;'>mesure $lig_t_incident->type</em>)<br />";
			}
			$retour.="</td>\n";
			$retour.="</tr>\n";
		}
		*/
		$texte=affiche_mesures_incident($lig_incident->id_incident);
		if($texte!='') {
			$retour.="<tr class='lig-1'><td style='font-weight:bold;vertical-align:top;text-align:left;'>Mesures&nbsp;: </td>\n";
			$retour.="<td style='text-align:left;'>";
			$retour.=$texte;
			$retour.="</td>\n";
			$retour.="</tr>\n";
		}
		$retour.="</table>\n";
	}
	else {
		$retour.="<p>L'".$mod_disc_terme_incident." n°$id_incident ne semble pas enregistré???</p>\n";
	}
	$retour.="</blockquote>\n";

	if($mode_retour=='echo') {
		echo $retour;
	}
	else {
		return $retour;
	}
}

function tab_lignes_adresse($ele_login) {
	global $gepiSchoolPays;

	unset($tab_adr_ligne1);
	unset($tab_adr_ligne2);
	unset($tab_adr_ligne3);

	$sql="SELECT * FROM resp_adr ra, resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND r.ele_id=e.ele_id AND r.pers_id=rp.pers_id AND rp.adr_id=ra.adr_id AND (r.resp_legal='1' OR r.resp_legal='2') ORDER BY resp_legal;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)==0) {
		return "Aucune adresse de responsable pour cet élève.";
	}
	else {
		$tab_resp=array();
		while($lig=mysqli_fetch_object($res)) {
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

						if(($tab_resp[0]['pays']!="")&&(mb_strtolower($tab_resp[0]['pays'])!=mb_strtolower($gepiSchoolPays))) {
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

							if(($tab_resp[$cpt]['pays']!="")&&(mb_strtolower($tab_resp[$cpt]['pays'])!=mb_strtolower($gepiSchoolPays))) {
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

							if(($tab_resp[$cpt]['pays']!="")&&(mb_strtolower($tab_resp[$cpt]['pays'])!=mb_strtolower($gepiSchoolPays))) {
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

				if(($tab_resp[0]['pays']!="")&&(mb_strtolower($tab_resp[0]['pays'])!=mb_strtolower($gepiSchoolPays))) {
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
	global $mod_disc_terme_incident, $mod_disc_terme_sanction;

	$retour="";

	if($date_debut!="") {
		// Tester la validité de la date
		// Si elle n'est pas valide... la vider
		if(preg_match("#/#",$date_debut)) {
			$tmp_tab_date=explode("/",$date_debut);

			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[0],$tmp_tab_date[2])) {
				$date_debut="";
			}
			else {
				$date_debut=$tmp_tab_date[2]."-".$tmp_tab_date[1]."-".$tmp_tab_date[0];
			}
		}
		elseif(preg_match("/-/",$date_debut)) {
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
		if(preg_match("#/#",$date_fin)) {
			$tmp_tab_date=explode("/",$date_fin);

			if(!checkdate($tmp_tab_date[1],$tmp_tab_date[0],$tmp_tab_date[2])) {
				$date_fin="";
			}
			else {
				$date_fin=$tmp_tab_date[2]."-".$tmp_tab_date[1]."-".$tmp_tab_date[0];
			}
		}
		elseif(preg_match("/-/",$date_fin)) {
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
	$zone_de_commentaire = "";
	$sql="SELECT * FROM s_incidents si, s_protagonistes sp WHERE si.id_incident=sp.id_incident AND sp.login='$ele_login' $restriction_date ORDER BY si.date DESC;";
	//echo "$sql<br />\n";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)>0) {
		$retour="<p>Tableau des ".$mod_disc_terme_incident."s concernant ".p_nom($ele_login)."</p>\n";
		$retour.="<table class='boireaus' border='1' summary=\"Tableau des ".$mod_disc_terme_incident."s concernant $ele_login\">\n";
		$retour.="<tr>\n";
		$retour.="<th>Num</th>\n";
		$retour.="<th>Date</th>\n";
		$retour.="<th>Qualité</th>\n";
		$retour.="<th>Description</th>\n";
		$retour.="<th>Suivi</th>\n";
		$retour.="</tr>\n";
		$alt_1=1;
		
		while($lig=mysqli_fetch_object($res)) {
			$alt_1=$alt_1*(-1);
			$retour.="<tr class='lig$alt_1'>\n";

				$retour.="<td>".$lig->id_incident."</td>\n";

				// Modifier l'accès Consultation d'incident... on ne voit actuellement que ses propres incidents
				//$retour.="<td><a href='' target='_blank'>".$lig->id_incident."</a></td>\n";

			$retour.="<td>".formate_date($lig->date);

			$retour.="<br />\n";

			$retour.="<span style='font-size:small;'>".u_p_nom($lig->declarant)."</span>";
			
			$zone_de_commentaire = $lig->commentaire;

			$retour.="</td>\n";
			$retour.="<td>".$lig->qualite."</td>\n";
			$temoin_eleve_responsable_de_l_incident='n';
			if(mb_strtolower(mb_strtolower($lig->qualite)=='responsable')) {
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
			
			$retour.="</td>\n";

			$retour.="<td style='padding: 2px;'>";

			$sql="SELECT * FROM s_protagonistes WHERE id_incident='$lig->id_incident' ORDER BY qualite;";
			//echo "$sql<br />\n";
			$res_prot=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($res_prot)>0) {
				$retour.="<table class='boireaus' border='1' summary=\"Protagonistes de l ".$mod_disc_terme_incident." n°$lig->id_incident\">\n";

				$alt_2=1;
				while($lig_prot=mysqli_fetch_object($res_prot)) {
					if($_SESSION['statut']=='eleve') {
						if($_SESSION['login']==$lig_prot->login) {
							$ligne_visible="y";
						}
						else {
							$ligne_visible="n";
						}
					}
					elseif($_SESSION['statut']=='responsable') {
						$ligne_visible="n";
						if(is_responsable($lig_prot->login, $_SESSION['login'])) {
							$ligne_visible="y";
						}
					}

					$alt_2=$alt_2*(-1);
					$retour.="<tr class='lig$alt_2'>\n";
					$retour.="<td>";
					if($lig_prot->statut=='eleve') {
						if((($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable'))&&($ligne_visible!="y")) {
							$retour.="XXX";
						}
						else {
							$retour.=p_nom($lig_prot->login);
						}
					}
					else {
						$retour.=civ_nom_prenom($lig_prot->login,"");
					}
					$retour.="</td>\n";

					$retour.="<td>";
					if($lig_prot->statut=='eleve') {
						if((($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable'))&&($ligne_visible!="y")) {
							$retour.="XXX";
						}
						else {
							$retour.=$lig_prot->qualite;
						}
					}
					else {
						$retour.=$lig_prot->qualite;
					}
					$retour.="</td>\n";

					$retour.="<td style='padding: 3px;'>\n";
					if($lig_prot->statut=='eleve') {
						if((($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable'))&&($ligne_visible!="y")) {
							$retour.="XXX";
						}
						else {
							$retour.=$lig_prot->qualite;
						}
					}
					else {
						$retour.=$lig_prot->qualite;
					}


					if((($_SESSION['statut']!='responsable')&&($_SESSION['statut']!='eleve'))||
					($lig_prot->statut!='eleve')||
					(($_SESSION['statut']=='responsable')&&($ligne_visible=="y"))||
					(($_SESSION['statut']=='eleve')&&($ligne_visible=="y"))
					) {
						$alt=1;
						$sql="SELECT * FROM s_traitement_incident sti, s_mesures sm WHERE sti.id_incident='$lig->id_incident' AND sti.login_ele='$lig_prot->login' AND sm.id=sti.id_mesure ORDER BY mesure;";
						$res_suivi=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_suivi)>0) {

							//$retour.="<p style='text-align:left;'>Tableau des mesures pour le protagoniste $lig_prot->login de l incident n°$lig->id_incident</p>\n";
							$retour.="<p style='text-align:left; font-weight: bold;'>Mesures</p>\n";

							$retour.="<table class='boireaus' border='1' summary=\"Tableau des mesures pour le protagoniste $lig_prot->login de l ".$mod_disc_terme_incident." n°$lig->id_incident\">\n";

							$retour.="<tr>\n";
							$retour.="<th>Nature</th>\n";
							$retour.="<th>Mesure</th>\n";
							$retour.="</tr>\n";

							while($lig_suivi=mysqli_fetch_object($res_suivi)) {
								$alt=$alt*(-1);
								$retour.="<tr class='lig$alt'>\n";
								$retour.="<td>$lig_suivi->mesure</td>\n";
							
								if($lig_suivi->type=='prise') {
									$retour.="<td>prise par ".u_p_nom($lig_suivi->login_u)."</td>\n";

									if($temoin_eleve_responsable_de_l_incident=='y') {
										if ($lig_suivi->login_ele==$ele_login) {  //Ajout ERIC test pour ne compter que pour l'élève demandé
											if(isset($tab_mesure[addslashes($lig_suivi->mesure)])) {
											   $tab_mesure[addslashes($lig_suivi->mesure)]++;
											}
											else {
												$tab_mesure[addslashes($lig_suivi->mesure)]=1;
											}
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
						
						//$sql="SELECT * FROM s_sanctions s WHERE s.id_incident='$lig->id_incident' AND s.login='$lig_prot->login' ORDER BY nature;";
						$sql="SELECT * FROM s_sanctions s, s_types_sanctions2 sts WHERE s.id_incident='$lig->id_incident' AND s.login='$lig_prot->login' AND sts.id_nature=s.id_nature_sanction ORDER BY sts.nature;";
						//echo "$sql<br />\n";
						$res_suivi=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_suivi)>0) {

							//$retour.="<p style='text-align:left;'>Tableau des sanctions pour le protagoniste $lig_prot->login de l incident n°$lig->id_incident</p>\n";
							$retour.="<p style='text-align:left; font-weight: bold;'>".ucfirst($mod_disc_terme_sanction)."s</p>\n";

							$retour.="<table class='boireaus' border='1' summary=\"Tableau des ".$mod_disc_terme_sanction."s pour le protagoniste $lig_prot->login de l ".$mod_disc_terme_incident." n°$lig->id_incident\">\n";

							$retour.="<tr>\n";
							$retour.="<th>Nature</th>\n";
							$retour.="<th>Date</th>\n";
							$retour.="<th>Description</th>\n";
							$retour.="<th>Effectuée</th>\n";
							$retour.="</tr>\n";
				
						
							while($lig_suivi=mysqli_fetch_object($res_suivi)) {
								$alt=$alt*(-1);
								$retour.="<tr class='lig$alt'>\n";
								$retour.="<td>$lig_suivi->nature</td>\n";
								$retour.="<td>";

								if($temoin_eleve_responsable_de_l_incident=='y') {
									if ($lig_suivi->login==$ele_login) { //Ajout ERIC test pour ne compter que pour l'élève demandé
										if(isset($tab_sanction[addslashes($lig_suivi->nature)])) {
											$tab_sanction[addslashes($lig_suivi->nature)]++;
										}
										else {
											$tab_sanction[addslashes($lig_suivi->nature)]=1;
										}
									}
								}

								//if($lig_suivi->nature=='retenue') {
								if(mb_strtolower($lig_suivi->type)=='retenue') {
									$sql="SELECT * FROM s_retenues WHERE id_sanction='$lig_suivi->id_sanction';";
									//echo "$sql<br />\n";
									$res_retenue=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									if(mysqli_num_rows($res_retenue)>0) {
										$lig_retenue=mysqli_fetch_object($res_retenue);
										$retour.=formate_date($lig_retenue->date)." (<i>".$lig_retenue->duree."H</i>)";
									}
									else {
										$retour.="X";
									}
								}
								//elseif($lig_suivi->nature=='exclusion') {
								elseif(mb_strtolower($lig_suivi->type)=='exclusion') {
									$sql="SELECT * FROM s_exclusions WHERE id_sanction='$lig_suivi->id_sanction';";
									//echo "$sql<br />\n";
									$res_exclusion=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									if(mysqli_num_rows($res_exclusion)>0) {
										$lig_exclusion=mysqli_fetch_object($res_exclusion);
										$retour.="du ".formate_date($lig_exclusion->date_debut)." (<i>$lig_exclusion->heure_debut</i>) au ".formate_date($lig_exclusion->date_fin)." (<i>$lig_exclusion->heure_fin</i>)<br />$lig_exclusion->lieu";
									}
									else {
										$retour.="X";
									}
								}
								//elseif($lig_suivi->nature=='travail') {
								elseif(mb_strtolower($lig_suivi->type)=='travail') {
									$sql="SELECT * FROM s_travail WHERE id_sanction='$lig_suivi->id_sanction';";
									//echo "$sql<br />\n";
									$res_travail=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									if(mysqli_num_rows($res_travail)>0) {
										$lig_travail=mysqli_fetch_object($res_travail);
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
					}
					else {
						$retour.="XXX";
					}

					$retour.="</td>\n";

					$retour.="</tr>\n";

				}
				$retour.="</table>\n";
				
				// Ajout Eric de la zone de commentaire
				//affichage du commentaire
				if ($zone_de_commentaire !="") {
				$retour .=  "<p style='text-align:left;'><b>Commentaires sur l'".$mod_disc_terme_incident."&nbsp;:&nbsp;</b></br></br>$zone_de_commentaire</p>";	
				}
			}

			$retour.="</td>\n";
		}
		$retour.="</table>\n";

		// Totaux
		$retour.="<p style='font-weight: bold;'>Totaux des ".$mod_disc_terme_incident."s/mesures/".$mod_disc_terme_sanction."s en tant que Responsable.</p>\n";

		$retour.="<div style='float:left; width:33%;'>\n";
		$retour.="<p style='font-weight: bold;'>".ucfirst($mod_disc_terme_incident)."s</p>\n";
		if(count($tab_incident)>0) {
			$retour.="<table class='boireaus' border='1' summary='Totaux ".$mod_disc_terme_incident."s'>\n";
			$retour.="<tr><th>Nature</th><th>Total</th></tr>\n";
			$alt=1;
			foreach($tab_incident as $key => $value) {
				$alt=$alt*(-1);
				$retour.="<tr class='lig$alt'><td>".stripslashes($key)."</td><td>".stripslashes($value)."</td></tr>\n";
			}
			$retour.="</table>\n";
		}
		else {
			$retour.="<p>Aucun ".$mod_disc_terme_incident." relevé en qualité de responsable.</p>\n";
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
		$retour.="<p style='font-weight: bold;'>".ucfirst($mod_disc_terme_sanction)."s</p>\n";
		if(count($tab_sanction)>0) {
			$retour.="<table class='boireaus' border='1' summary='Totaux ".$mod_disc_terme_sanction."s'>\n";
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
		$retour="<p>Aucun ".$mod_disc_terme_incident." relevé.</p>\n";
	}

	return $retour;
}

function get_destinataires_mail_alerte_discipline($tab_id_classe, $nature="") {
	$retour="";

	$id_nature="";
	if($nature!="") {
		$sql="SELECT sn.id FROM s_natures sn WHERE sn.nature='".((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $nature) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
		$res_nature=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res_nature)) {
			$id_nature=mysql_result($res_nature, 0, "id");
		}
	}

	$tab_dest=array();
	$temoin=false;
	for($i=0;$i<count($tab_id_classe);$i++) {
		$sql="SELECT * FROM s_alerte_mail WHERE id_classe='".$tab_id_classe[$i]."';";
		$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				if($lig->destinataire=='cpe') {
					$sql="SELECT DISTINCT u.nom,u.prenom,u.email,u.login FROM utilisateurs u, j_eleves_cpe jecpe, j_eleves_classes jec WHERE jec.id_classe='".$tab_id_classe[$i]."' AND jec.login=jecpe.e_login AND jecpe.cpe_login=u.login AND u.email!='';";
				}
				elseif($lig->destinataire=='professeurs') {
					$sql="SELECT DISTINCT u.nom,u.prenom,u.email,u.login FROM utilisateurs u, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp WHERE jec.id_classe='".$tab_id_classe[$i]."' AND jec.login=jeg.login AND jeg.id_groupe=jgp.id_groupe AND jgp.login=u.login AND u.email!='';";
				}
				elseif($lig->destinataire=='pp') {
					$sql="SELECT DISTINCT u.nom,u.prenom,u.email,u.login FROM utilisateurs u, j_eleves_professeurs jep, j_eleves_classes jec WHERE jec.id_classe='".$tab_id_classe[$i]."' AND jec.id_classe=jep.id_classe AND jec.login=jep.login AND jep.professeur=u.login AND u.email!='';";
				}
				elseif($lig->destinataire=='administrateur') {
					$sql="SELECT DISTINCT u.nom,u.prenom,u.email,u.login FROM utilisateurs u WHERE u.statut='administrateur' AND u.email!='';";
				}
				elseif($lig->destinataire=='scolarite') {
					$sql="SELECT DISTINCT u.nom,u.prenom,u.email,u.login FROM utilisateurs u, j_scol_classes jsc WHERE jsc.id_classe='".$tab_id_classe[$i]."' AND jsc.login=u.login AND u.email!='';";
				}
				elseif($lig->destinataire=='mail') {
					$temoin=true;
					$adresse_sup = $lig->adresse;
				}

				//echo $sql;
				if ($temoin) { //Cas d'une adresse mail autre
					$tab_dest[] = $adresse_sup;
				} else {
					$res2=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res2)>0) {
						while($lig2=mysqli_fetch_object($res2)) {
						$ajouter_mail="y";
							if($id_nature!="") {
								$sql="SELECT * FROM preferences WHERE login='$lig2->login' AND name='mod_discipline_natures_exclues_mail' AND value LIKE '%|$id_nature|%';";
								$test_nat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
								if(mysqli_num_rows($test_nat)>0) {
									$ajouter_mail="n";
								}
							}
							else {
								$mod_discipline_natures_non_categorisees_exclues_mail=getPref($_SESSION['login'],'mod_discipline_natures_non_categorisees_exclues_mail',"n");
								if($mod_discipline_natures_non_categorisees_exclues_mail=="y") {
									$ajouter_mail="n";
								}
							}

							if($ajouter_mail!="n") {
								if(!in_array($lig2->email,$tab_dest)) {
									$tab_dest[]=$lig2->email;
									//$tab_dest[]="$lig2->prenom $lig2->nom <$lig2->email>";
								}
							}
						}
					}
				}
				$temoin=false;
			}
		}
	}

	for($i=0;$i<count($tab_dest);$i++) {
		if($i>0) {$retour.=", ";}
		$retour.=$tab_dest[$i];
	}
	return $retour;
}

// Retourne à partir de l'id d'un incident le login du déclarant
function get_login_declarant_incident($id_incident) {
	global $mod_disc_terme_incident;

	$retour="";
    //$sql_declarant="SELECT DISTINCT SI.id_incident, SI.declarant FROM s_incidents SI, s_sanctions SS WHERE SI.id_incident='$id_incident' AND SI.id_incident=SS.id_incident;";
    $sql_declarant="SELECT DISTINCT SI.id_incident, SI.declarant FROM s_incidents SI WHERE SI.id_incident='$id_incident';";
		//echo $sql_declarant;
		$res_declarant=mysqli_query($GLOBALS["___mysqli_ston"], $sql_declarant);
        if(mysqli_num_rows($res_declarant)>0) {
		$lig_declarant=mysqli_fetch_object($res_declarant);
		  $retour= $lig_declarant->declarant;	
		} else {
		  $retour=ucfirst($mod_disc_terme_incident).' inconnu';
		}
	return $retour;
}

//Fonction dressant la liste des reports pour une sanction ($id_type_sanction)
function afficher_tableau_des_reports($id_sanction) {
	global $mod_disc_terme_sanction;
	global $id_incident;
	$retour="";
	$sql="SELECT * FROM s_reports WHERE id_sanction='$id_sanction' ORDER BY id_report;";
	//echo $sql;
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "<table class='boireaus' border='1' summary='Liste des reports' style='margin:2px;'>\n";
		echo "<tr>\n";
		echo "<th>Report N°</th>\n";
		echo "<th>Date</th>\n";
		echo "<th>Information</th>\n";
		echo "<th>motif</th>\n";
		echo "<th>Suppr</th>\n";
		echo "</tr>\n";
		$alt_b=1;
		$cpt=1;
		while($lig=mysqli_fetch_object($res)) {
			$alt_b=$alt_b*(-1);
			echo "<tr class='lig$alt_b'>\n";
			echo "<td>".$cpt."</td>\n";
			$tab_date=explode("-",$lig->date);
			echo "<td>".$tab_date[2]."-".sprintf("%02d",$tab_date[1])."-".sprintf("%02d",$tab_date[0])."</td>\n";
			echo "<td>".$lig->informations."</td>\n";
			echo "<td>".$lig->motif_report."</td>\n";
			echo "<td><a href='".$_SERVER['PHP_SELF']."?mode=suppr_report&amp;id_report=$lig->id_report&amp;id_sanction=$lig->id_sanction&amp;id_incident=$id_incident&amp;".add_token_in_url()."' title='Supprimer le report n°$lig->id_report'><img src='../images/icons/delete.png' width='16' height='16' alt='Supprimer le report n°$lig->id_report' /></a></td>\n";

			echo "<tr/>";
			$cpt++;
		}
		echo "</table>\n";
	} else {
		$retour = "Aucun report actuellement pour cette ".$mod_disc_terme_sanction.".";
	}	
	return $retour;
}

//Fonction donnant le nombre de reports pour une sanction ($id_type_sanction)
function nombre_reports($id_sanction,$aucun) {
	$sql="SELECT * FROM s_reports WHERE id_sanction='$id_sanction' ORDER BY id_report;";
	//echo $sql;
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)>0) {
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			$cpt++;
		}
	} else {
		$cpt = $aucun;
	}	
	return $cpt;
}

// Retourne à partir de l'id d'un incident le login du déclarant
function get_protagonistes($id_incident,$roles=array(),$statuts=array()) {
	$retour=array();

	$chaine_roles="";
	if(count($roles)>0) {
		$chaine_roles=" AND (";
		for($loop=0;$loop<count($roles);$loop++) {
			if($loop>0) {$chaine_roles.=" OR ";}
			$chaine_roles.="qualite='$roles[$loop]'";
		}
		$chaine_roles.=")";
	}

	$chaine_statuts="";
	if(count($statuts)>0) {
		$chaine_statuts=" AND (";
		for($loop=0;$loop<count($statuts);$loop++) {
			if($loop>0) {$chaine_statuts.=" OR ";}
			$chaine_statuts.="statut='$statuts[$loop]'";
		}
		$chaine_statuts.=")";
	}

	$sql="SELECT * FROM s_protagonistes WHERE id_incident='$id_incident' $chaine_roles $chaine_statuts ORDER BY qualite, login;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
		  $retour[]=$lig->login;
		}
	}
	return $retour;
}
/*
function get_documents_joints($id, $type) {
	global $dossier_documents_discipline;
	// $type: mesure ou sanction
	$tab_file=array();

	if(($type=="mesure")||($type=="sanction")) {
		if($type=="mesure") {
			$sql="SELECT id_incident FROM s_traitement_incident WHERE id='$id';";
		}
		else {
			$sql="SELECT id_incident FROM s_sanctions WHERE id_sanction='$id';";
		}
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
			$id_incident=$lig->id_incident;

			if(file_exists("../$dossier_documents_discipline/incident_".$id_incident."/".$type."_".$id)) {
				$handle=opendir($path);
				$n=0;
				while ($file = readdir($handle)) {
					if (($file != '.') and ($file != '..') and ($file != 'remove.txt')
					and ($file != '.htaccess') and ($file != '.htpasswd') and ($file != 'index.html')) {
						$tab_file[] = $file;
					}
				}
				closedir($handle);
				sort($tab_file);
			}
		}
	}

	return $tab_file;
}
*/
function get_documents_joints($id, $type, $login_ele="") {
	// $type: mesure ou sanction
	// $login_ele doit être non vide pour les mesures
	global $dossier_documents_discipline;
	$tab_file=array();

	$id_incident="";

	if(($type=="mesure")||($type=="sanction")) {
		if($type=="mesure") {
			$id_incident=$id;

			$path="../$dossier_documents_discipline/incident_".$id_incident."/mesures/$login_ele";
		}
		else {
			$sql="SELECT id_incident FROM s_sanctions WHERE id_sanction='$id';";
			$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);
				$id_incident=$lig->id_incident;

				$path="../$dossier_documents_discipline/incident_".$id_incident."/sanction_".$id;
			}
		}

		if(isset($path)) {
			$tab_file=get_file_in_dir($path);
		}
	}

	return $tab_file;
}

function get_file_in_dir($path) {
	$tab_file=array();

	if(file_exists($path)) {
		$handle=opendir($path);
		$n=0;
		while ($file = readdir($handle)) {
			if (($file != '.') and ($file != '..') and ($file != 'remove.txt')
			and ($file != '.htaccess') and ($file != '.htpasswd') and ($file != 'index.html')) {
				$tab_file[] = $file;
			}
		}
		closedir($handle);
		sort($tab_file);
	}

	return $tab_file;
}

function sanction_documents_joints($id_incident, $ele_login) {
	global $id_sanction;
	global $dossier_documents_discipline;

	if((isset($id_sanction))&&($id_sanction!='')) {
		$tab_doc_joints=get_documents_joints($id_sanction, "sanction", $ele_login);
		if(count($tab_doc_joints)>0) {
			$chemin="../$dossier_documents_discipline/incident_".$id_incident."/sanction_".$id_sanction;

			echo "<table class='boireaus' width='100%'>\n";
			echo "<tr>\n";
			echo "<th>Fichiers joints</th>\n";
			echo "<th>Supprimer</th>\n";
			echo "</tr>\n";
			$alt3=1;
			for($loop=0;$loop<count($tab_doc_joints);$loop++) {
				$alt3=$alt3*(-1);
				echo "<tr class='lig$alt3 white_hover'>\n";
				echo "<td><a href='$chemin/$tab_doc_joints[$loop]' target='_blank'>$tab_doc_joints[$loop]</a></td>\n";
				echo "<td><input type='checkbox' name='suppr_doc_joint[]' value=\"$tab_doc_joints[$loop]\" /></td>\n";
				// PB: Est-ce qu'on ne risque pas de permettre d'aller supprimer des fichiers d'un autre incident?
				//     Tester le nom de fichier et l'id_incident
				//     Fichier en ../$dossier_documents_discipline/incident_<$id_incident>/mesures/<LOGIN_ELE>
				echo "</tr>\n";
			}
			echo "</table>\n";
		}
	}

	echo "<p>Joindre un fichier&nbsp;: <input type=\"file\" size=\"15\" name=\"document_joint\" id=\"document_joint\" /><br />\n";


	$tab_doc_joints2=get_documents_joints($id_incident, "mesure", $ele_login);
	if(count($tab_doc_joints2)>0) {
		$temoin_deja_tous_joints="n";
		if(isset($tab_doc_joints)) {
			$temoin_deja_tous_joints="y";
			for($loop=0;$loop<count($tab_doc_joints2);$loop++) {
				if(!in_array($tab_doc_joints2[$loop], $tab_doc_joints)) {
					$temoin_deja_tous_joints="n";
					break;
				}
			}
		}

		if($temoin_deja_tous_joints=="n") {
			//echo "Joindre&nbsp;:<br />\n";
			$chemin="../$dossier_documents_discipline/incident_".$id_incident."/mesures/".$ele_login;
	
			echo "<b>Fichiers proposés lors de la saisie des mesures demandées&nbsp;:</b>";
			echo "<table class='boireaus' width='100%'>\n";
			echo "<tr>\n";
			echo "<th>Joindre</th>\n";
			echo "<th>Fichier</th>\n";
			echo "</tr>\n";
			$alt3=1;
			for($loop=0;$loop<count($tab_doc_joints2);$loop++) {
				if((!isset($tab_doc_joints))||(!in_array($tab_doc_joints2[$loop],$tab_doc_joints))) {
					$alt3=$alt3*(-1);
					echo "<tr class='lig$alt3 white_hover'>\n";
					echo "<td><input type='checkbox' name='ajouter_doc_joint[]' value=\"$tab_doc_joints2[$loop]\" ";
					//if((!isset($tab_doc_joints))||(!in_array($tab_doc_joints2[$loop],$tab_doc_joints))) {
						echo "checked ";
					//}
					echo "/>\n";
					echo "</td>\n";
					echo "<td><a href='$chemin/$tab_doc_joints2[$loop]' target='_blank'>$tab_doc_joints2[$loop]</a></td>\n";
					echo "</tr>\n";
				}
			}
			echo "</table>\n";
		}
	}
}

function liste_doc_joints_sanction($id_sanction) {
	global $dossier_documents_discipline;
	$retour="";

	$tab_doc_joints=get_documents_joints($id_sanction, "sanction");
	if(count($tab_doc_joints)>0) {
		$sql="SELECT id_incident FROM s_sanctions WHERE id_sanction='$id_sanction';";
		$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$id_incident=$lig->id_incident;

			$chemin="../$dossier_documents_discipline/incident_".$id_incident."/sanction_".$id_sanction;
	
			for($loop=0;$loop<count($tab_doc_joints);$loop++) {
				$retour.="<a href='$chemin/$tab_doc_joints[$loop]' target='_blank'>$tab_doc_joints[$loop]</a><br />\n";
			}
		}
	}

	return $retour;
}

function suppr_doc_joints_incident($id_incident, $suppr_doc_sanction='n') {
	global $dossier_documents_discipline;
	$retour="";

	$sql="SELECT login FROM s_protagonistes WHERE id_incident='$id_incident';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)>0) {
		$temoin_erreur="n";

		while($lig=mysqli_fetch_object($res)) {
			//echo "\$lig->login=$lig->login<br />";
			$tab_doc_joints=get_documents_joints($id_incident, "mesure", $lig->login);
			//echo "count(\$tab_doc_joints)=".count($tab_doc_joints)."<br />";
			if(count($tab_doc_joints)>0) {
				$chemin="../$dossier_documents_discipline/incident_".$id_incident."/mesures/".$lig->login;
				//echo "$chemin<br />";
				$temoin_erreur="n";
				for($loop=0;$loop<count($tab_doc_joints);$loop++) {
					if(!unlink($chemin."/".$tab_doc_joints[$loop])) {
						$retour.="Erreur lors de la suppression de $chemin/$tab_doc_joints[$loop]<br />";
						$temoin_erreur="y";
					}
				}
				if($temoin_erreur=="n") {
					rmdir($chemin);
				}
			}
		}

		if($temoin_erreur=="n") {
			if($suppr_doc_sanction=='y') {
				$sql="SELECT id_sanction FROM s_sanctions WHERE id_incident='$id_incident';";
				$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						$retour.=suppr_doc_joints_sanction($lig->id_sanction);
					}
				}
			}

			if((file_exists("../$dossier_documents_discipline/incident_".$id_incident."/mesures"))&&(rmdir("../$dossier_documents_discipline/incident_".$id_incident."/mesures"))) {
				if(file_exists("../$dossier_documents_discipline/incident_".$id_incident)) {rmdir("../$dossier_documents_discipline/incident_".$id_incident);}
			}
		}
	}

	return $retour;
}

function suppr_doc_joints_sanction($id_sanction) {
	global $dossier_documents_discipline;

	$retour="";

	$sql="SELECT id_incident FROM s_sanctions WHERE id_sanction='$id_sanction';";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$id_incident=$lig->id_incident;

		$tab_doc_joints=get_documents_joints($id_sanction, "sanction");
		if(count($tab_doc_joints)>0) {
			$chemin="../$dossier_documents_discipline/incident_".$id_incident."/sanction_".$id_sanction;
			for($loop=0;$loop<count($tab_doc_joints);$loop++) {
				if(!unlink($chemin."/".$tab_doc_joints[$loop])) {
					$retour.="Erreur lors de la suppression de $chemin/$tab_doc_joints[$loop]<br />";
				}
			}
			rmdir($chemin);
		}
	}

	return $retour;
}

function lien_envoi_mail_rappel($id_sanction, $num, $id_incident="") {
	global $mod_disc_terme_incident;
	global $mod_disc_terme_sanction;

	$retour="";

	if(($id_sanction!="")||($id_incident!="")) {
		$trame_message="Bonjour, \n";

		if($id_sanction=="") {
			$login_declarant=get_login_declarant_incident($id_incident);

			//pour le mail
			$mail_declarant = retourne_email($login_declarant);
			//echo add_token_field(true);
			$retour.="<input type='hidden' name='sujet_mail_rappel_$num' id='sujet_mail_rappel_$num' value=\"[GEPI] Discipline : Demande de travail pour une ".$mod_disc_terme_sanction."\" />\n";
			$retour.="<input type='hidden' name='destinataire_mail_rappel_$num' id='destinataire_mail_rappel_$num' value=\"".$mail_declarant."\" />\n";

			$num_incident=$id_incident;

			$chaine_protagonistes="";
			$tab_protagonistes=get_protagonistes($id_incident, array('Responsable'), array('eleve'));
			for($loop=0;$loop<count($tab_protagonistes);$loop++) {
				if($loop>0) {$chaine_protagonistes.=", ";}
				$chaine_protagonistes.=get_nom_prenom_eleve($tab_protagonistes[$loop],'avec_classe');
			}

			//$trame_message.="La sanction (voir l'incident N°%num_incident%) de %prenom_nom% (%classe%) est planifiée.\n";
			$trame_message.="La ".$mod_disc_terme_sanction." (voir l'".$mod_disc_terme_incident." N°$num_incident) de $chaine_protagonistes est planifiée.\n";
		}
		else {
			$sql="SELECT * FROM s_sanctions WHERE id_sanction='$id_sanction';";
			$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig_sanction=mysqli_fetch_object($res);
	
				$login_declarant=get_login_declarant_incident($lig_sanction->id_incident);
			
				//pour le mail
				$mail_declarant = retourne_email($login_declarant);
				//echo add_token_field(true);
				$retour.="<input type='hidden' name='sujet_mail_rappel_$num' id='sujet_mail_rappel_$num' value=\"[GEPI] Discipline : Demande de travail pour une $lig_sanction->nature\" />\n";
				$retour.="<input type='hidden' name='destinataire_mail_rappel_$num' id='destinataire_mail_rappel_$num' value=\"".$mail_declarant."\" />\n";

				$num_incident=$lig_sanction->id_incident;
				$prenom_nom=p_nom($lig_sanction->login) ;
				$tmp_tab=get_class_from_ele_login($lig_sanction->login);
				if(isset($tmp_tab['liste_nbsp'])) {$classe= $tmp_tab['liste_nbsp'];}
		
				if($lig_sanction->nature="retenue") {
					//$trame_message.="La $lig_sanction->nature (voir l'incident N°%num_incident%) de %prenom_nom% (%classe%) est planifiée le %jour% en/à %heure% pour une durée de %duree%H \n";
					$trame_message.="La retenue (voir l'".$mod_disc_terme_incident." N°%num_incident%) de %prenom_nom% (%classe%) est planifiée le %jour% en/à %heure% pour une durée de %duree%H \n";
		
					$sql="SELECT * FROM s_retenues WHERE id_sanction='$lig_sanction->id_sanction';";
					$res2=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res2)>0) {
						$lig_retenue=mysqli_fetch_object($res2);
					
						$date=formate_date($lig_retenue->date);
						$heure=$lig_retenue->heure_debut;
						$duree=$lig_retenue->duree;
		
						$trame_message=str_replace("%jour%",$date,$trame_message);
						$trame_message=str_replace("%heure%",$heure,$trame_message);
						$trame_message=str_replace("%duree%",$duree,$trame_message);
					}
				}
				elseif($lig_sanction->nature="exclusion") {
					$trame_message.="L'exclusion (voir l'".$mod_disc_terme_incident." N°%num_incident%) de %prenom_nom% (%classe%) est planifiée du %jour_debut% au %jour_fin% \n";
		
					$sql="SELECT * FROM s_exclusions WHERE id_sanction='$lig_sanction->id_sanction';";
					$res2=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res2)>0) {
						$lig_exclusion=mysqli_fetch_object($res2);
					
						$date_debut=formate_date($lig_exclusion->date_debut);
						$date_fin=formate_date($lig_exclusion->date_fin);
		
						$trame_message=str_replace("%jour_debut%",$date_debut,$trame_message);
						$trame_message=str_replace("%jour_fin%",$date_fin,$trame_message);
					}
				}
				elseif($lig_sanction->nature="travail") {
					$trame_message.="Le travail (voir l'".$mod_disc_terme_incident." N°%num_incident%) de %prenom_nom% (%classe%) est planifié pour une date de retour au %jour_retour% à %heure_retour% \n";
		
					$sql="SELECT * FROM s_travail WHERE id_sanction='$lig_sanction->id_sanction';";
					$res2=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res2)>0) {
						$lig_travail=mysqli_fetch_object($res2);
					
						$date_retour=formate_date($lig_travail->date_retour);
						$heure_retour=formate_date($lig_travail->heure_retour);
		
						$trame_message=str_replace("%jour_retour%",$date_retour,$trame_message);
						$trame_message=str_replace("%heure_retour%",$heure_retour,$trame_message);
					}
				}
				else {
					$trame_message.="La ".$mod_disc_terme_sanction." '$lig_sanction->nature' (voir l'".$mod_disc_terme_incident." N°%num_incident%) de %prenom_nom% (%classe%) est planifiée.\n";
				}
			}

			$trame_message=str_replace("%num_incident%",$num_incident,$trame_message);
			$trame_message=str_replace("%prenom_nom%",$prenom_nom,$trame_message);
			$trame_message=str_replace("%classe%",$classe,$trame_message);

		}
	
		//echo "<td>\n";	
		$ligne_nom_declarant=u_p_nom($login_declarant);
		$retour.="$ligne_nom_declarant";

		$trame_message.="Merci d'apporter le travail prévu à la vie scolaire.\n\n-- \nLa vie scolaire";

		//echo $trame_message;
		$retour.="<input type='hidden' name='message_mail_rappel_$num' id='message_mail_rappel_$num' value=\"$trame_message\"/>\n";

		//on autorise l'envoi de mail que pour les statuts Admin / CPE / Scolarite
		if(($_SESSION['statut']=='administrateur') || ($_SESSION['statut']=='cpe') || ($_SESSION['statut']=='scolarite')) {
			//if($lig_sanction->effectuee!="O") {
			if((!isset($lig_sanction))||($lig_sanction->effectuee!="O")) {
				$retour.="<span id='mail_envoye_$num'><a href='#' onclick=\"envoi_mail_rappel_sanction($num);return false;\"><img src='../images/icons/icone_mail.png' width='25' height='25' alt='Envoyer un mail pour demander le travail au déclarant' title='Envoyer un mail pour demander le travail au déclarant' /></a></span>";
			}
		}
	}
	return $retour;
}

function envoi_mail_rappel_js() {
	$retour="<script type='text/javascript'>
	// <![CDATA[
	function envoi_mail_rappel_sanction(num) {
		csrf_alea=document.getElementById('csrf_alea').value;
		destinataire=document.getElementById('destinataire_mail_rappel_'+num).value;
		sujet_mail=document.getElementById('sujet_mail_rappel_'+num).value;
		message=document.getElementById('message_mail_rappel_'+num).value;
		new Ajax.Updater($('mail_envoye_'+num),'../bulletin/envoi_mail.php?destinataire='+destinataire+'&sujet_mail='+sujet_mail+'&message='+escape(message)+'&csrf_alea='+csrf_alea,{method: 'get'});
	}
	//]]>
</script>\n";
	return $retour;
}

function get_nature_sanction($id_nature_sanction) {
	$retour="";
	$sql="SELECT nature FROM s_types_sanctions2 WHERE id_nature='$id_nature_sanction';";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)>0) {
		$retour=mysql_result($res,0,"nature");
	}
	return $retour;
}

function sanction_saisie_par($id_sanction, $login) {
	$sql="SELECT 1=1 FROM s_sanctions WHERE id_sanction='$id_sanction' AND saisie_par='".$login."';";
	$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($test)==0) {
		return false;
	}
	else {
		return true;
	}
}
?>
