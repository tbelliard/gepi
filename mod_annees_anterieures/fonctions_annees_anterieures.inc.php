<?php

function bull_simp_annee_anterieure($logineleve,$id_classe,$annee_scolaire,$num_periode){
	/*
		$logineleve:      login actuel de l'élève
		$id_classe:       identifiant de la classe actuelle de l'élève
		$annee_scolaire:  nom de l'année à afficher
		$num_periode:     numéro de la période à afficher
	*/

	//global $gepiPath;
	global $gecko;

	$sql="SELECT * FROM eleves WHERE login='$logineleve';";
	$res_ele=mysql_query($sql);

	if(mysql_num_rows($res_ele)==0){
		// On ne devrait pas arriver là.
		echo "<p>L'élève dont le login serait $logineleve n'est pas dans la table 'eleves'.</p>\n";
	}
	else{
		$lig_ele=mysql_fetch_object($res_ele);

		// Infos élève
		//$ine: INE de l'élève (identifiant commun aux tables 'eleves' et 'archivage_disciplines')
		$ine=$lig_ele->no_gep;
		//$nom=$lig_ele->nom;
		//$prenom=$lig_ele->prenom;
		$ele_nom=$lig_ele->nom;
		$ele_prenom=$lig_ele->prenom;
		$naissance=$lig_ele->naissance;
		//$naissance2=formate_date($lig_ele->naissance);

		// Classe actuelle:
		$classe=get_nom_classe($id_classe);

		/*
						// A DEPLACER VERS styles.css
						echo "<style type='text/css'>
			.table_annee_anterieure{
				border: 1px solid black;
				border-collapse: collapse;
			}

			.table_annee_anterieure th{
				border: 1px solid black;
				font-weight: bold;
				text-align: center;
			}

			.table_annee_anterieure td{
				border: 1px solid black;
				padding: 0.1em;
			}

			.table_annee_anterieure td.td_note{
				text-align: center;
			}

			.table_annee_anterieure td.td_note_classe, th.td_note_classe{
				text-align: center;
				font-size: small;
			}

			.table_annee_anterieure .info_prof{
				font-style: italic;
				font-size: small;
			}

		</style>\n";
		*/

		// Liste des années conservées pour l'élève choisi:
		$sql="SELECT DISTINCT annee FROM archivage_disciplines WHERE ine='$ine' ORDER BY annee";
		$res_annees=mysql_query($sql);
		$annee_precedente="";
		$annee_suivante="";
		$derniere_periode_annee_precedente=1;
		if(mysql_num_rows($res_annees)>0){
			while($lig_annee=mysql_fetch_object($res_annees)){
				if($lig_annee->annee!=$annee_scolaire){
					$annee_precedente=$lig_annee->annee;
					$sql="SELECT DISTINCT num_periode FROM archivage_disciplines WHERE ine='$ine' AND annee='$annee_precedente' ORDER BY num_periode DESC";
					$res_per_prec=mysql_query($sql);
					if(mysql_num_rows($res_per_prec)>0){
						$lig_per_prec=mysql_fetch_object($res_per_prec);
						$derniere_periode_annee_precedente=$lig_per_prec->num_periode;
					}
				}
				else{
					if($lig_annee=mysql_fetch_object($res_annees)){
						$annee_suivante=$lig_annee->annee;
					}
					break;
				}
			}
		}

		// Liste des périodes pour l'année choisie:
		$sql="SELECT DISTINCT num_periode FROM archivage_disciplines WHERE ine='$ine' AND annee='$annee_scolaire' ORDER BY num_periode";
		$res_periodes=mysql_query($sql);

		if(mysql_num_rows($res_periodes)==0){
			// Ca ne doit pas arriver...
		}
		else{
			//echo "<p><b>$annee_scolaire</b>: ";
			echo "<ul style='list-style-type: none; margin-bottom:0;'>\n";
			if($gecko){
				echo "<li style='display:inline; border: 1px solid black; background-image: url(\"../images/background/opacite50.png\"); padding: 0.2em 0.2em 0 0.2em;'>";
			}
			else{
				echo "<li style='display:inline; border: 1px solid black; background-color: white; padding: 0.2em 0.2em 0 0.2em;'>";
			}

			if($annee_precedente!=""){
				// https://127.0.0.1/steph/gepi-trunk/annees_anterieures/consultation_annee_anterieure.php?id_classe=4&logineleve=DUVAL_R&annee_scolaire=2004/2005&num_periode=1
				//echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;logineleve=$logineleve&amp;annee_scolaire=$annee_precedente&amp;num_periode=1'>&lt;</a> ";
				echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;logineleve=$logineleve&amp;annee_scolaire=$annee_precedente&amp;num_periode=$derniere_periode_annee_precedente&amp;mode=bull_simp'><img src='../images/icons/back_.png' width='16' height='14' alt='Année précédente' /></a> ";
			}
			echo "<b>$annee_scolaire</b>";
			if($annee_suivante!=""){
				//echo " <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;logineleve=$logineleve&amp;annee_scolaire=$annee_suivante&amp;num_periode=1'>&gt;</a>";
				echo " <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;logineleve=$logineleve&amp;annee_scolaire=$annee_suivante&amp;num_periode=1&amp;mode=bull_simp'><img src='../images/icons/forward_.png' width='16' height='14' alt='Année suivante' /></a>";
			}

			echo "</li>\n";



			/*
			if($gecko){
				echo "<li style='display:inline; border: 1px solid black; background-image: url(\"../images/background/opacite50.png\"); padding: 0.2em 0.2em 0 0.2em;'>";
			}
			else{
				echo "<li style='display:inline; border: 1px solid black; background-color: white; padding: 0.2em 0.2em 0 0.2em;'>";
			}
			echo "$classe";
			echo "</li>\n";
			*/


			//echo "<div style='display:block; border: 1px solid black; background-color: white; width:20%;'><b>$annee_scolaire</b></div>\n";
			$cpt=0;
			while($lig_periode=mysql_fetch_object($res_periodes)){
				//if($cpt>0){echo " - ";}
				//echo "<li style='display:inline;'>\n";
				if($lig_periode->num_periode!=$num_periode){
					echo "<li style='display:inline; border: 1px solid black; padding: 0.2em 0.2em 0 0.2em;'>\n";
					//echo "<div style='display:block; border: 1px solid black; width:20%;'>\n";
					echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;logineleve=$logineleve&amp;annee_scolaire=$annee_scolaire&amp;num_periode=$lig_periode->num_periode&amp;mode=bull_simp'>P".$lig_periode->num_periode."</a>";
				}
				else{
					if($gecko){
						echo "<li style='display:inline; border: 1px solid black; background-image: url(\"../images/background/opacite50.png\"); padding: 0.2em 0.2em 0 0.2em;'>\n";
					}
					else{
						echo "<li style='display:inline; border: 1px solid black; background-color: white; padding: 0.2em 0.2em 0 0.2em;'>\n";
					}
					//echo "<div style='display:block; border: 1px solid black; background-color: white; width:20%;'>\n";
					echo "P".$lig_periode->num_periode;
				}
				echo "</li>\n";
				//echo "</div>\n";
				$cpt++;
			}
			echo "</ul>\n";
		}

		if($gecko){
			echo "<div style='border: 1px solid black; background-image: url(\"../images/background/opacite50.png\"); padding: 3px;'>\n";
		}
		else{
			echo "<div style='border: 1px solid black; background-color: white; padding: 3px;'>\n";
		}

		//$sql="SELECT DISTINCT nom_periode FROM archivage_disciplines WHERE ine='$ine' AND num_periode='$num_periode' AND annee='$annee_scolaire'";
		$sql="SELECT DISTINCT nom_periode, classe FROM archivage_disciplines WHERE ine='$ine' AND num_periode='$num_periode' AND annee='$annee_scolaire'";
		$res_per=mysql_query($sql);

		if(mysql_num_rows($res_per)==0){
			$nom_periode="période $num_periode";
			$classe_ant="???";
		}
		else{
			$lig_per=mysql_fetch_object($res_per);
			$nom_periode=$lig_per->nom_periode;
			$classe_ant=$lig_per->classe;
		}

		echo "<h2>Antécédents de $ele_prenom $ele_nom: millésime $annee_scolaire</h2>\n";

		//echo "<p>Bulletin simplifié de $prenom $nom pour la période $num_periode de l'année scolaire $annee_scolaire</p>";
		echo "<p>Bulletin simplifié de $ele_prenom $ele_nom: $nom_periode de l'année scolaire $annee_scolaire en <strong>$classe_ant</strong> <em style='font-size: x-small;'>(actuellement en $classe)</em></p>\n";

		// Affichage des infos élève

		// Affichage des matières
		echo "<table class='table_annee_anterieure' width='100%' summary='Matières/notes'>\n";
		echo "<tr>\n";
		echo "<th rowspan='2'>Matière</th>\n";
		echo "<th colspan='3'>Classe</th>\n";
		echo "<th rowspan='2'>Elève</th>\n";
		echo "<th rowspan='2'>Appréciations/Conseils</th>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<th class='td_note_classe'>min</th>\n";
		echo "<th class='td_note_classe'>moy</th>\n";
		echo "<th class='td_note_classe'>max</th>\n";
		echo "</tr>\n";

		$sql="SELECT * FROM archivage_disciplines WHERE annee='$annee_scolaire' AND num_periode='$num_periode' AND ine='$ine' AND special='' ORDER BY matiere";
		//echo "$sql<br />\n";
		$res_mat=mysql_query($sql);

		if(mysql_num_rows($res_mat)==0){
			// On ne devrait pas arriver là.
			echo "<tr><td colspan='6'>Aucun résultat enregistré???</td></tr>\n";
		}
		else{
			while($lig_mat=mysql_fetch_object($res_mat)){
				echo "<tr>\n";
				echo "<td>";
				echo "<b>".htmlentities(stripslashes($lig_mat->matiere))."</b><br />\n";
				echo "<span class='info_prof'>".htmlentities(stripslashes($lig_mat->prof))."</span>\n";
				echo "</td>\n";
				echo "<td class='td_note_classe'>$lig_mat->moymin</td>\n";
				echo "<td class='td_note_classe'>$lig_mat->moyclasse</td>\n";
				echo "<td class='td_note_classe'>$lig_mat->moymax</td>\n";
				echo "<td class='td_note'>$lig_mat->note</td>\n";
				echo "<td>".htmlentities(stripslashes($lig_mat->appreciation))."</td>\n";
				echo "</tr>\n";
			}
		}
		// Affichage des AIds
		$sql="SELECT type.nom type_nom, aid.nom nom_aid, aid.responsables responsables, app.note_moyenne_classe moyenne_aid, app.note_min_classe min_aid, app.note_max_classe max_aid, app.note_eleve note_aid, app.appreciation appreciation,
    type.note_sur note_sur_aid, type.type_note type_note
    FROM archivage_appreciations_aid app, archivage_aids aid, archivage_types_aid type
    WHERE
    app.annee='$annee_scolaire' and
    app.periode='$num_periode' and
    app.id_eleve='$ine' and
    app.id_aid=aid.id and
    aid.id_type_aid=type.id and
    type.display_bulletin='y'
    ORDER BY type.nom, aid.nom";
		//echo "$sql<br />";
		$res_aid=mysql_query($sql);
		/*
		if(mysql_num_rows($res_aid)==0){
			// On ne devrait pas arriver là.
			echo "<tr><td colspan='6'>Aucun résultat enregistré???";
			//echo "<br />$sql";
			echo "</td></tr>\n";
		}
		else{
		*/
		if(mysql_num_rows($res_aid)>0){
			while($lig_aid=mysql_fetch_object($res_aid)){
				echo "<tr>\n";
				echo "<td>";
				echo "<b>".htmlentities(stripslashes($lig_aid->type_nom))." : ".htmlentities(stripslashes($lig_aid->nom_aid))."</b><br />\n";
				echo "<span class='info_prof'>".htmlentities(stripslashes($lig_aid->responsables))."</span>\n";
				echo "</td>\n";
				echo "<td class='td_note_classe'>$lig_aid->moyenne_aid</td>\n";
				echo "<td class='td_note_classe'>$lig_aid->min_aid</td>\n";
				echo "<td class='td_note_classe'>$lig_aid->max_aid</td>\n";
				echo "<td class='td_note'>$lig_aid->note_aid";
				echo "</td>\n";
				echo "<td>";
				if (($lig_aid->note_sur_aid != 20) and ($lig_aid->note_aid !='-')) {
					echo "(note sur ".$lig_aid->note_sur_aid.") ";
				}

				echo htmlentities(stripslashes($lig_aid->appreciation))."</td>\n";
				echo "</tr>\n";
			}
		}

		echo "</table>\n";


		// Affichage des absences
		$sql="SELECT * FROM archivage_disciplines WHERE annee='$annee_scolaire' AND num_periode='$num_periode' AND ine='$ine' AND special='ABSENCES'";
		//echo "$sql<br />\n";
		$res_abs=mysql_query($sql);

		if(mysql_num_rows($res_abs)==0){
			echo "<p>Aucune information sur les absences/retards.</p>\n";
		}
		elseif(mysql_num_rows($res_abs)>1){
			echo "<p>Bizarre: Il y a plus d'un enregistrement pour cette élève, cette période et cette année.</p>\n";
		}
		else{
			$lig_abs=mysql_fetch_object($res_abs);

			$nb_absences=$lig_abs->nb_absences;
			$non_justifie=$lig_abs->non_justifie;
			$nb_retards=$lig_abs->nb_retards;

			echo "<p>";
			if ($nb_absences=='0') {
				echo "<i>Aucune demi-journée d'absence</i>.";
			}
			else {
				echo "<i>Nombre de demi-journées d'absence ";
				if ($non_justifie=='0') {echo "justifiées ";}
				echo ": </i><b>$nb_absences</b>";
				if ($non_justifie != '0') {
					echo " (dont <b>$non_justifie</b> non justifiée"; if ($non_justifie != '1') {echo "s";}
					echo ")";
				}
				echo ".";
			}
			if ($nb_retards!='0') {
				echo "<i> Nombre de retards : </i><b>$nb_retards</b>";
			}
			echo "  (C.P.E. chargé(e)";
			echo " du suivi : ".$lig_abs->prof.")";
			if ($lig_abs->appreciation!= ""){echo "<br />$lig_abs->appreciation";}
			echo "</p>\n";
		}

		// Affichage de l'avis du conseil
		$sql="SELECT * FROM archivage_disciplines WHERE annee='$annee_scolaire' AND num_periode='$num_periode' AND ine='$ine' AND special='AVIS_CONSEIL'";
		//echo "$sql<br />\n";
		$res_avis=mysql_query($sql);


		echo "<table class='table_annee_anterieure' width='100%' summary='Avis du conseil'>\n";
		echo "<tr>\n";
		echo "<td align='left'>\n";
		echo "<p><i>Avis du Conseil de classe : </i><br />\n";

		$prof_suivi="";
		if(mysql_num_rows($res_avis)==0){
			echo "Aucune information sur l'avis du conseil de classe.</p>\n";
		}
		elseif(mysql_num_rows($res_avis)>1){
			echo "Bizarre: Il y a plus d'un enregistrement pour cette élève, cette période et cette année.</p>\n";
			$prof_suivi="?";
		}
		else{
			$lig_avis=mysql_fetch_object($res_avis);
			echo "$lig_avis->appreciation</p>\n";
			$prof_suivi=$lig_avis->prof;
		}
		echo "</td>\n";
		echo "<td align='center'>\n";
		echo "<p>Classe suivie par: <b>$prof_suivi</b></p>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "</div>\n";
		// Afficher des liens permettant de passer rapidement à la période suivante/précédente
		// + un tableau des années/périodes (années sur une ligne en colspan=nb_per et num_periode en dessous)
	}
}


//function avis_conseils_de_classes_annee_anterieure($logineleve,$annee_scolaire,$id_classe=NULL){
function avis_conseils_de_classes_annee_anterieure($logineleve,$annee_scolaire){
	global $gecko;
	global $id_classe;

	// Tableau des avis de conseils de classes
	$sql="SELECT * FROM eleves WHERE login='$logineleve';";
	$res_ele=mysql_query($sql);

	if(mysql_num_rows($res_ele)==0){
		// On ne devrait pas arriver là.
		echo "<p>L'élève dont le login serait $logineleve n'est pas dans la table 'eleves'.</p>\n";
	}
	else{
		$lig_ele=mysql_fetch_object($res_ele);

		// Infos élève
		//$ine: INE de l'élève (identifiant commun aux tables 'eleves' et 'archivage_disciplines')
		$ine=$lig_ele->no_gep;
		//$nom=$lig_ele->nom;
		//$prenom=$lig_ele->prenom;
		$ele_nom=$lig_ele->nom;
		$ele_prenom=$lig_ele->prenom;
		$naissance=$lig_ele->naissance;
		//$naissance2=formate_date($lig_ele->naissance);

		// Liste des années conservées pour l'élève choisi:
		$sql="SELECT DISTINCT annee FROM archivage_disciplines WHERE ine='$ine' ORDER BY annee";
		$res_annees=mysql_query($sql);
		$annee_precedente="";
		$annee_suivante="";
		//$derniere_periode_annee_precedente=1;
		if(mysql_num_rows($res_annees)>0){
			while($lig_annee=mysql_fetch_object($res_annees)){
				if($lig_annee->annee!=$annee_scolaire){
					$annee_precedente=$lig_annee->annee;
					/*
					$sql="SELECT DISTINCT num_periode FROM archivage_disciplines WHERE ine='$ine' AND annee='$annee_precedente' ORDER BY num_periode DESC";
					$res_per_prec=mysql_query($sql);
					if(mysql_num_rows($res_per_prec)>0){
						$lig_per_prec=mysql_fetch_object($res_per_prec);
						$derniere_periode_annee_precedente=$lig_per_prec->num_periode;
					}
					*/
				}
				else{
					if($lig_annee=mysql_fetch_object($res_annees)){
						$annee_suivante=$lig_annee->annee;
					}
					break;
				}
			}
		}


		echo "<ul style='list-style-type: none; margin-bottom:0;'>\n";
		if($gecko){
			echo "<li style='display:inline; border: 1px solid black; background-image: url(\"../images/background/opacite50.png\"); padding: 0.2em 0.2em 0 0.2em;'>\n";
		}
		else{
			echo "<li style='display:inline; border: 1px solid black; background-color: white; padding: 0.2em 0.2em 0 0.2em;'>\n";
		}
		if($annee_precedente!=""){
			echo "<a href='".$_SERVER['PHP_SELF']."?logineleve=$logineleve&amp;annee_scolaire=$annee_precedente&amp;mode=avis_conseil";
			if(isset($id_classe)){echo "&amp;id_classe=$id_classe";}
			echo "'><img src='../images/icons/back_.png' width='16' height='14' alt='Année précédente' /></a> \n";
		}
		echo "<b>$annee_scolaire</b>\n";
		if($annee_suivante!=""){
			echo " <a href='".$_SERVER['PHP_SELF']."?logineleve=$logineleve&amp;annee_scolaire=$annee_suivante&amp;mode=avis_conseil";
			if(isset($id_classe)){echo "&amp;id_classe=$id_classe";}
			echo "'><img src='../images/icons/forward_.png' width='16' height='14' alt='Année suivante' /></a>\n";
		}
		echo "</li>\n";
		echo "</ul>\n";


		if($gecko){
			echo "<div style='border: 1px solid black; background-image: url(\"../images/background/opacite50.png\"); padding: 3px;'>";
		}
		else{
			echo "<div style='border: 1px solid black; background-color: white; padding: 3px;'>\n";
		}

		echo "<h2>Antécédents de $ele_prenom $ele_nom: millésime $annee_scolaire</h2>\n";

		echo "<p>Avis des conseils de classe pour $ele_prenom $ele_nom lors de l'année scolaire $annee_scolaire</p>\n";

		// Affichage de l'avis du conseil
		$sql="SELECT * FROM archivage_disciplines WHERE annee='$annee_scolaire' AND ine='$ine' AND special='AVIS_CONSEIL' ORDER BY num_periode";
		//echo "$sql<br />\n";
		$res_avis=mysql_query($sql);

		if(mysql_num_rows($res_avis)==0){
			echo "Aucune information sur l'avis du conseil de classe.</p>\n";
		}
		else{
			echo "<table class='table_annee_anterieure' width='100%' summary='Avis du conseil'>\n";
			echo "<tr>\n";
			echo "<th>Année-scolaire</th>\n";
			echo "<th>Avis du conseil de classe</th>\n";
			echo "<th>Classe suivie par</th>\n";
			echo "</tr>\n";
			while($lig_avis=mysql_fetch_object($res_avis)){
				echo "<tr>\n";
				echo "<th>\n";
				/*
				$sql="SELECT DISTINCT nom_periode FROM archivage_disciplines WHERE ine='$ine' AND num_periode='$lig_avis->num_periode' AND annee='$annee_scolaire'";
				$res_per=mysql_query($sql);
				if(mysql_num_rows($res_avis)==0){
					echo "Aucune information sur l'avis du conseil de classe.</p>\n";
				}
				else{
				}
				*/
				echo "$lig_avis->nom_periode";
				echo "</th>\n";

				echo "<td>\n";
				//echo "<p>Classe suivie par: <b>$lig_avis->prof</b>\n";
				//echo "<br />\n";
				echo "$lig_avis->appreciation\n";
				echo "</td>\n";
				echo "<td style='text-align:center;'>\n";
				//echo "<p>Classe suivie par: <b>$lig_avis->prof</b>\n";
				echo "$lig_avis->prof\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		}
		echo "</div>\n";
	}
}

function tab_choix_anterieure($logineleve,$id_classe=NULL){

	$sql="SELECT * FROM eleves WHERE login='$logineleve';";
	$res_ele=mysql_query($sql);

	if(mysql_num_rows($res_ele)==0){
		//echo "<p>Aucun élève dans la classe $classe pour la période '$nom_periode'.</p>\n";
		echo "<p>L'élève dont le login serait $logineleve n'est pas dans la table 'eleves'.</p>\n";
	}
	else{
		$lig_ele=mysql_fetch_object($res_ele);

		// Infos élève
		$ine=$lig_ele->no_gep;
		//$nom=$lig_ele->nom;
		//$prenom=$lig_ele->prenom;
		$ele_nom=$lig_ele->nom;
		$ele_prenom=$lig_ele->prenom;
		$naissance=$lig_ele->naissance;
		//$naissance2=formate_date($lig_ele->naissance);


		echo "<p>Liste des années scolaires et périodes pour lesquelles des données concernant $ele_prenom $ele_nom ";
		if(isset($id_classe)){
			$classe=get_nom_classe($id_classe);
			echo "(<i>$classe</i>) ";
		}
		echo "ont été conservées:</p>\n";

		//echo "<p>Liste des années scolaires et périodes pour lesquelles des données concernant $ele_prenom $ele_nom (<i>$classe</i>) ont été conservées:</p>\n";

		// Récupérer les années-scolaires et périodes pour lesquelles on trouve l'INE dans archivage_disciplines
		//$sql="SELECT DISTINCT annee,num_periode,nom_periode FROM archivage_disciplines WHERE ine='$ine' ORDER BY annee DESC, num_periode ASC";
		//$sql="SELECT DISTINCT annee FROM archivage_disciplines WHERE ine='$ine' ORDER BY annee DESC;";
		$sql="SELECT DISTINCT annee FROM archivage_disciplines WHERE ine='$ine' ORDER BY annee ASC;";
		$res_ant=mysql_query($sql);

		if(mysql_num_rows($res_ant)==0){
			echo "<p>Aucun résultat antérieur n'a été conservé pour cet élève.</p>\n";
		}
		else{

			unset($tab_annees);

			$nb_annees=mysql_num_rows($res_ant);

			//echo "<p>Bulletins simplifiés:</p>\n";
			//echo "<table border='0'>\n";
			echo "<table class='table_annee_anterieure' summary='Bulletins'>\n";
			echo "<tr>\n";
			echo "<th rowspan='".$nb_annees."' valign='top'>Bulletins simplifiés:</th>";
			$cpt=0;
			while($lig_ant=mysql_fetch_object($res_ant)){

				$tab_annees[]=$lig_ant->annee;

				if($cpt>0){
					echo "<tr>\n";
				}
				echo "<td style='font-weight:bold;'>$lig_ant->annee : </td>\n";

				$sql="SELECT DISTINCT num_periode,nom_periode FROM archivage_disciplines WHERE ine='$ine' AND annee='$lig_ant->annee' ORDER BY num_periode ASC";
				$res_ant2=mysql_query($sql);

				if(mysql_num_rows($res_ant2)==0){
					echo "<td>Aucun résultat antérieur n'a été conservé pour cet élève.</td>\n";
				}
				else{
					$cpt=0;
					while($lig_ant2=mysql_fetch_object($res_ant2)){
						//if($cpt>0){echo "<td> - </td>\n";}
						echo "<td style='text-align:center;'><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;logineleve=$logineleve&amp;annee_scolaire=$lig_ant->annee&amp;num_periode=$lig_ant2->num_periode&amp;mode=bull_simp'>$lig_ant2->nom_periode</a></td>\n";
						$cpt++;
					}
				}
				echo "</tr>\n";
				$cpt++;
			}
			echo "</table>\n";

			//echo "<p><br /></p>\n";
			echo "<br />\n";

			//echo "<p>Avis des conseils de classes:<br />\n";
			//echo "<table border='0'>\n";
			echo "<table class='table_annee_anterieure' summary='Avis des conseils'>\n";
			echo "<tr>\n";
			echo "<th rowspan='".$nb_annees."' valign='top'>Avis des conseils de classes:</th>";
			$cpt=0;
			for($i=0;$i<count($tab_annees);$i++){
				if($cpt>0){
					echo "<tr>\n";
				}
				//echo "<td style='font-weight:bold;'>\n";
				echo "<td>\n";

				echo "Année-scolaire <a href='".$_SERVER['PHP_SELF']."?logineleve=$logineleve&amp;annee_scolaire=".$tab_annees[$i]."&amp;mode=avis_conseil";
				if(isset($id_classe)){echo "&amp;id_classe=$id_classe";}
				echo "'>$tab_annees[$i]</a>";
				//echo "<br />\n";

				echo "</td>\n";
				echo "</tr>\n";
				$cpt++;
			}
			echo "</table>\n";
			//echo "</p>\n";
		}
	}
}
function insert_eleve($login,$ine,$annee,$param) {
  // on insère le regime et le statut doublant
  $sql="SELECT DISTINCT regime, doublant FROM j_eleves_regime WHERE login='".$login."'";
  $res_regime=mysql_query($sql);
  while($lig_ele=mysql_fetch_object($res_regime)){
   $regime=$lig_ele->regime;
   $doublant=$lig_ele->doublant;
  }
  $del = sql_query1("delete from archivage_eleve2 where ine ='".$ine."'");
  $sql="INSERT INTO archivage_eleves2 SET
  ine='".$ine."',
  annee = '".$annee."',
  doublant='".addslashes($doublant)."',
  regime='".addslashes($regime)."'";
  $res_insert_regime=mysql_query($sql);
  // on traite la table archivage_eleve
  $test = sql_query1("select count(ine) from archivage_eleves where ine= '".$ine."'");
  if ($test == 0) {
    $sql="SELECT DISTINCT nom, prenom, no_gep, naissance, sexe FROM eleves WHERE login='".$login."'";
    $res_ele=mysql_query($sql);
    if(mysql_num_rows($res_ele)==0) {
        return "<tr><td colspan='4'>Aucune donnée disponible pour l'élève dont l'identifiant est ".$login."</td></tr>";
        die();
    } else {
        while($lig_ele=mysql_fetch_object($res_ele)){
          // Infos élève
          $nom=$lig_ele->nom;
          $prenom=$lig_ele->prenom;
          $naissance=$lig_ele->naissance;
          $sexe=$lig_ele->sexe;
          $ine=$lig_ele->no_gep;
          if($ine=="")
            $ine="LOGIN_".$login;
          $sql="INSERT INTO archivage_eleves SET
		      ine='$ine',
          nom='".addslashes($nom)."',
          prenom='".addslashes($prenom)."',
          sexe='".addslashes($sexe)."',
		      naissance='$naissance'";
          $res_insert=mysql_query($sql);
		      if(!$res_insert){
            return "<tr><td colspan='4'><font color='red'>Erreur d'enregistrement des données pour l'élève dont l'identifiant est ".$login."</font></td></tr>";
            exit();
          } else {
            if ($param != 'y')
                return "<tr><td class='small'>".$ine."</td><td class='small'>".$nom."</td><td class='small'>".$prenom."</td><td class='small'>".$naissance."</td></tr>";
          }
        }
    }
  }
}

function cree_substitut_INE_unique($login){
    $m = '';
    $test_unicite = '';
    while ($test_unicite != 1) {
      // On vérifie que le login ne figure pas déjà dans la table archivage_eleves
      $req_test = mysql_query("SELECT nom, prenom, sexe, naissance FROM archivage_eleves WHERE (ine='".$login.$m."')");
      $test = mysql_num_rows($req_test);
      if ($test!=0) {
          // un même identifiant existe déjà !
          // s'agit-il de la même personne. On considère que oui si les noms, prénom, date de naissance et sexe correspondent
          $nom = mysql_result($req_test,0,"nom");
          $prenom = mysql_result($req_test,0,"prenom");
          $sexe = mysql_result($req_test,0,"sexe");
          $naissance = mysql_result($req_test,0,"naissance");
          $test_unicite = mysql_num_rows(mysql_query("SELECT login FROM eleves WHERE (nom='".$nom."' and prenom='".$prenom."' and sexe='".$sexe."' and naissance='".$naissance."')"));
      } else
          $test_unicite = 1;
      if ($test_unicite != 1) {
        if ($m == '') {
            $m = 2;
       	} else {
            $m++;
        }
      } else {
        $login = $login.$m;
      }
    }
    return $login;
}

function suppression_donnees_eleves_inutiles(){
    // Supprimer des données élèves qui ne servent plus à rien

    $res_eleve = mysql_query("select ine from archivage_eleves");
    $nb_eleves = mysql_num_rows($res_eleve);
    $k = 0;
    while($k < $nb_eleves){
      $ine = mysql_result($res_eleve,$k,"ine");
      $test1 = sql_query1("SELECT count(ae.ine) FROM archivage_eleves ae, archivage_aid_eleve aae
      where ((aae.id_eleve = ae.ine) and (ae.ine='".$ine."'))");
      $test2 = sql_query1("SELECT count(ae.ine) FROM archivage_eleves ae, archivage_appreciations_aid aa
      where ((aa.id_eleve = ae.ine) and (ae.ine='".$ine."'))");
      $test3 = sql_query1("SELECT count(ae.ine) FROM archivage_eleves ae, archivage_disciplines ad
      where ((ad.INE = ae.ine) and (ae.ine='".$ine."'))");
      $test4 = sql_query1("SELECT count(ae.ine) FROM archivage_eleves ae, archivage_ects aects
      where ((aects.ine = ae.ine) and (ae.ine='".$ine."'))");
      if (($test1==0) and ($test2==0) and ($test3==0) and ($test4==0))
          sql_query("DELETE FROM archivage_eleves WHERE ine='".$ine."'");
      $k++;
    }

    $res_eleve = mysql_query("select ine from archivage_eleves2");
    $nb_eleves = mysql_num_rows($res_eleve);
    $k = 0;
    while($k < $nb_eleves){
      $ine = mysql_result($res_eleve,$k,"ine");
      $test1 = sql_query1("SELECT count(ae.ine) FROM archivage_eleves2 ae, archivage_aid_eleve aae
      where ((aae.id_eleve = ae.ine) and (ae.ine='".$ine."'))");
      $test2 = sql_query1("SELECT count(ae.ine) FROM archivage_eleves2 ae, archivage_appreciations_aid aa
      where ((aa.id_eleve = ae.ine) and (ae.ine='".$ine."'))");
      $test3 = sql_query1("SELECT count(ae.ine) FROM archivage_eleves2 ae, archivage_disciplines ad
      where ((ad.INE = ae.ine) and (ae.ine='".$ine."'))");
      $test4 = sql_query1("SELECT count(ae.ine) FROM archivage_eleves2 ae, archivage_ects ad
      where ((ad.ine = ae.ine) and (ae.ine='".$ine."'))");
      if (($test1==0) and ($test2==0) and ($test3==0) and ($test4==0))
          sql_query("DELETE FROM archivage_eleves2 WHERE ine='".$ine."'");
      $k++;
    }

}
?>
