<?php

function affiche_calendrier_crob($mois="", $annee="", $id_classe="", $mode="") {
	global $mysqli, $gepiPath;


	$tab_conseils_de_classe=get_tab_dates_evenements_classes("", "conseil_de_classe", "y", "y", "date_ev");
	$tab_periodes=get_tab_dates_periodes();

	$YYYYmmjj_aujourdhui=strftime("%Y%m%d");

	$tab_creneaux=get_heures_debut_fin_creneaux();

	$ts_debut_annee_scolaire=getSettingValue('begin_bookings');
	$ts_fin_annee_scolaire=getSettingValue('end_bookings');
	//echo "\$ts_debut_annee_scolaire=$ts_debut_annee_scolaire<br />";
	//echo "\$ts_fin_annee_scolaire=$ts_fin_annee_scolaire<br />";

	$temoin_debug=0;
	$retour="";

	$jour=1;
	if(!isset($mois)) {
		$mois=strftime("%m");
	}
	if(!isset($annee)) {
		$annee=strftime("%Y");
	}

	$ts=mktime(12, 0, 0, $mois, $jour, $annee);
	$num_jsem=id_j_semaine($ts);
	$nom_mois=french_strftime("%B", $ts);

	if($temoin_debug==1) {
		$retour.="<p>Le $jour/$mois/$annee est un ".french_strftime("%A", $ts)."</p>";
	}
	if($num_jsem!="1") {
		$ts=$ts-($num_jsem-1)*24*3600;
		if($temoin_debug==1) {
			$retour.="<p>Le lundi précédent le $jour/$mois/$annee est le ".french_strftime("%A %d/%m/%Y", $ts)."</p>";
		}
	}

	// Repérer le premier dimanche après le mois
	if($mois<12) {
		$jour_suivant=1;
		$mois_suivant=$mois+1;
		$annee_suivante=$annee;
	}
	else {
		$jour_suivant=1;
		$mois_suivant=1;
		$annee_suivante=$annee+1;
	}

	if($mois>1) {
		$jour_prec=1;
		$mois_prec=$mois-1;
		$annee_prec=$annee;
	}
	else {
		$jour_prec=1;
		$mois_prec=12;
		$annee_prec=$annee-1;
	}


	$complement_lien_mois_precedent="";
	$complement_lien_mois_suivant="";
	if($mode=="popup") {
		$complement_lien_mois_precedent=" onclick=\"affiche_calendrier_crob($mois_prec, $annee_prec, '$id_classe');return false;\"";
		$complement_lien_mois_suivant=" onclick=\"affiche_calendrier_crob($mois_suivant, $annee_suivante, '$id_classe');return false;\"";
	}



	$ts_j1_mois_suiv=mktime(12, 0, 0, $mois_suivant, $jour_suivant, $annee_suivante);
	$num_jsem_suiv=id_j_semaine($ts_j1_mois_suiv);

	$lien_mois_suivant="";
	$afficher_lien_mois_suivant="y";
	if($ts_j1_mois_suiv>$ts_fin_annee_scolaire) {
		$afficher_lien_mois_suivant="n";
	}
	else {
		$lien_mois_suivant="<a href='$gepiPath/lib/calendrier_crob.php?id_classe=$id_classe&amp;annee=$annee_suivante&amp;mois=$mois_suivant'".$complement_lien_mois_suivant."><img src='$gepiPath/images/icons/forward.png' class='icone16' alt='Mois suivant' /></a>";
	}

	$lien_mois_precedent="";
	$afficher_lien_mois_precedent="y";
	if($ts<$ts_debut_annee_scolaire-30*24*3600) {
		$afficher_lien_mois_precedent="n";
	}
	else {
		$lien_mois_precedent="<a href='$gepiPath/lib/calendrier_crob.php?id_classe=$id_classe&amp;annee=$annee_prec&amp;mois=$mois_prec'".$complement_lien_mois_precedent."><img src='$gepiPath/images/icons/back.png' class='icone16' alt='Mois précédent' /></a>";
	}

	$ts_dim_suiv=$ts_j1_mois_suiv;
	if($temoin_debug==1) {
		$retour.="<p>Le $jour_suivant/$mois_suivant/$annee_suivante est un ".french_strftime("%A", $ts_j1_mois_suiv)."</p>";
	}
	if($num_jsem_suiv!="1") {
		$ts_dim_suiv=$ts_j1_mois_suiv+(7-$num_jsem_suiv)*24*3600;
		if($temoin_debug==1) {
			$retour.="<p>Le premier dimanche suivant le mois $mois est le ".french_strftime("%A %d/%m/%Y", $ts_dim_suiv)." ($ts_dim_suiv)"."</p>";
		}
	}


	$retour.="<div style='text-align:center;'>
	<p>
		$lien_mois_precedent
		- $nom_mois $annee -
		$lien_mois_suivant
	</p>";


	//$tab_jour_vacance=get_tab_jours_vacances($id_classe);
	$tab_jour_vacance=get_tab_jours_vacances2($id_classe);

	$tab_jour_ouverture=get_tab_jour_ouverture_etab();
	$tab_jfr=array();
	$tab_jfr[1]="lundi";
	$tab_jfr[2]="mardi";
	$tab_jfr[3]="mercredi";
	$tab_jfr[4]="jeudi";
	$tab_jfr[5]="vendredi";
	$tab_jfr[6]="samedi";
	$tab_jfr[7]="dimanche";

	$tab_sem=array();
	$sql="SELECT * FROM edt_semaines;";
	$res_sem=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res_sem)>0) {
		while($lig_sem=mysqli_fetch_object($res_sem)) {
			$tab_sem[sprintf("%02d", $lig_sem->num_edt_semaine)]=$lig_sem->type_edt_semaine;
		}
	}

	$retour.="<!--div id='div_details_date' style='float:right; width:30em;'></div-->

	<table class='boireaus boireaus_alt' align='center'>
		<thead>
			<tr>
				<th>Num.<br />semaine</th>
				<th>Type</th>
				<th>L</th>
				<th>Ma</th>
				<th>Me</th>
				<th>J</th>
				<th>V</th>
				<th>S</th>
				<th>D</th>
			</tr>
		</thead>
		<tbody>";
	$temoin_abs=0;
	$chaine_debug="";
	$ts_courant=$ts;
	$temoin_mois_suiv=0;
	while($ts_courant-2*3600<$ts_dim_suiv) {
	//while($temoin_mois_suiv==0) {
		$jour_courant=strftime("%d", $ts_courant);
		$mois_courant=strftime("%m", $ts_courant);
		$annee_courant=strftime("%Y", $ts_courant);

		$num_jsem_courant=id_j_semaine($ts_courant);
		if($num_jsem_courant==1) {
			$num_semaine=strftime("%U", $ts_courant);
			if(isset($tab_sem[$num_semaine])) {
				$type_semaine=$tab_sem[$num_semaine];
			}
			else {
				$type_semaine="";
			}
			$retour.="
			<tr>
				<th>".$num_semaine."</th>
				<th>".$type_semaine."</th>";
		}



		$ajout="";
		if(($ts_courant<$ts_debut_annee_scolaire)||($ts_courant>$ts_fin_annee_scolaire)) {
			$style=" style='background-color:grey;'";
			$ajout="<br /><div style='width:2em; margin-right:1px; float:left;'></div><div style='width:2em;float:left; '>&nbsp;</div>";
		}
		/*
		//elseif(in_array(strftime("%d/%m/%Y", $ts_courant), $tab_jour_vacance)) {
		elseif(in_array(strftime("%Y%m%d", $ts_courant), $tab_jour_vacance)) {
			$style=" style='background-color:grey;' title=\"Vacances ou jour férié.\"";
			$ajout="<br /><div style='width:2em; margin-right:1px; float:left;'></div><div style='width:2em;float:left; '>&nbsp;</div>";
		}
		*/
		elseif(in_array(strftime("%Y%m%d", $ts_courant), $tab_jour_vacance['jour'])) {
			$details_vacances="Vacances ou jour férié";
			$tmp_tab=array_keys($tab_jour_vacance['jour'], strftime("%Y%m%d", $ts_courant));
			if(count($tmp_tab)>0) {
				$cpt_tab=$tmp_tab[0];
				$details_vacances=$tab_jour_vacance['nom_ferie'][$cpt_tab];
			}
			$style=" style='background-color:grey;' title=\"".$details_vacances.".\"";
			//$ajout="<br /><div style='width:2em; margin-right:1px; float:left;'></div><div style='width:2em;float:left; '>&nbsp;</div>";
			/*
			echo "<hr />".strftime("%Y%m%d", $ts_courant)."<br /><pre>";
			print_r($tmp_tab);
			echo "</pre>";
			*/
		}
		elseif(!in_array($tab_jfr[$num_jsem_courant], $tab_jour_ouverture)) {
			$style=" style='background-color:grey;'";
			//$ajout="<br /><div style='width:2em; margin-right:1px; float:left;'></div><div style='width:2em; float:left;'>&nbsp;</div>";
		}
		else {
			$style="";
			if(strftime("%m", $ts_courant)>$mois) {
				$style=" style='background-color:lavender;' title=\"Mois suivant\"";
				//$ajout="<br /><div style='width:2em; margin-right:1px; float:left;'></div><div style='width:2em;float:left; '>&nbsp;</div>";
			}
			elseif(strftime("%m", $ts_courant)<$mois) {
				$style=" style='background-color:lavender;' title=\"Mois précédent\"";
				//$ajout="<br /><div style='width:2em; margin-right:1px; float:left;'></div><div style='width:2em;float:left; '>&nbsp;</div>";
			}

			//$ajout.="<br /><div style='width:2em; margin-right:1px; float:left;'>&nbsp;</div>";
			//$ajout="<br /><div style='width:2em; margin-right:1px; float:left;'></div><div style='width:2em; float:left;'>&nbsp;</div>";
		}

		if($temoin_debug==1) {
			$chaine_debug="<br />$ts_courant<br />".(($ts_dim_suiv-$ts_courant)/3600)."h<br />$temoin_mois_suiv<br />".strftime("%m", $ts_courant);
		}

		//$texte_jour=strftime("%d", $ts_courant);
		$texte_jour="<span title=\"".french_strftime("%A %d/%m/%Y", $ts_courant)."\">".strftime("%d", $ts_courant)."</span>";
		if($YYYYmmjj_aujourdhui==$annee_courant.$mois_courant.$jour_courant) {
			$texte_jour="<span style='color:red; font-weight:bold;' title=\"Aujourd'hui ".french_strftime("%A", $ts_courant)." $jour_courant/$mois_courant/$annee_courant\">".strftime("%d", $ts_courant)."</span>";
		}
		$tmp_mysql_date=strftime("%Y-%m-%d 00:00:00", $ts_courant);
		if((isset($tab_periodes["date_fin"]))&&(array_key_exists($tmp_mysql_date, $tab_periodes["date_fin"]))) {
			$texte_jour.="<span title=\"Fin de période pour ";
			for($loop_date_fin=0;$loop_date_fin<count($tab_periodes["date_fin"][$tmp_mysql_date]);$loop_date_fin++) {
				if($loop_date_fin>0) {
					$texte_jour.=", ";
				}
				//$texte_jour.=$tab_periodes["date_fin"][$tmp_mysql_date][$loop_date_fin]["classe"]." (".$tab_periodes["date_fin"][$tmp_mysql_date][$loop_date_fin]['num_periode'].")";
				$texte_jour.=$tab_periodes["date_fin"][$tmp_mysql_date][$loop_date_fin]["classe"];
			}
			$texte_jour.="\"><img src='$gepiPath/images/bulle_bleue.png' width='9' height='9' alt='FP' /></span>";
		}
		$tmp_jjmmaaaa_date=strftime("%d/%m/%Y", $ts_courant);
		if(array_key_exists($tmp_jjmmaaaa_date, $tab_conseils_de_classe)) {
			$texte_jour.="<span title=\"Conseil de classe de ";
			$cpt_clas=0;
			foreach($tab_conseils_de_classe[$tmp_jjmmaaaa_date] as $tmp_id_classe => $tmp_classe) {
				if($cpt_clas>0) {
					$texte_jour.=", ";
				}
				$texte_jour.=$tmp_classe["classe"]." (".$tmp_classe['slashdate_heure_ev'].")";
				$cpt_clas++;
			}
			$texte_jour.="\"><img src='$gepiPath/images/bulle_verte.png' width='9' height='9' alt='CC' /></span>";
		}

		$retour.="
				<td$style>".$texte_jour.$ajout.$chaine_debug."</td>";

		$ts_courant+=3600*24;

		// On considère le mois du jour qui suit ce tour dans la boucle while()
		$mois_courant=strftime("%m", $ts_courant);
		if(($mois<12)&&($mois_courant!=12)&&($mois_courant>$mois)) {
			//($mois_courant!=12) pour le cas 12/14 -> 01/15
			$temoin_mois_suiv++;
		}
		elseif(($mois==12)&&($mois_courant==1)) {
			$temoin_mois_suiv++;
		}
		else {
			// Si le dernier jour du mois est un dimanche (11/2014)
			if(($mois_courant!=$mois)&&(strftime("%d", $ts_courant)==1)) {
				$temoin_mois_suiv++;
			}
		}

		if($num_jsem_courant==7) {
			$retour.="
			</tr>";

			if($temoin_mois_suiv>0) {
				break;
			}
		}
	}
	$retour.="
		</tbody>
	</table>
</div>";

	return $retour;
}

?>
