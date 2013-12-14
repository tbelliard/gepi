<?php

/*
*/

function bull_simp_annee_anterieure($logineleve,$id_classe,$annee_scolaire,$num_periode,$ine=""){
	/*
		$logineleve:      login actuel de l'élève
		$id_classe:       identifiant de la classe actuelle de l'élève
		$annee_scolaire:  nom de l'année à afficher
		$num_periode:     numéro de la période à afficher
	*/

	//global $gepiPath;
	global $gecko;

	//echo "$annee_scolaire=$annee_scolaire<br />";

	$poursuivre="y";

	if($logineleve!="") {
		$sql="SELECT * FROM eleves WHERE login='$logineleve';";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
	
		if(mysqli_num_rows($res_ele)==0) {
			// On ne devrait pas arriver là.
			echo "<p>L'élève dont le login serait $logineleve n'est pas dans la table 'eleves'.</p>\n";
			$poursuivre="n";
		}
	}
	elseif($ine=="") {
		echo "<p>Aucun login ni INE n'a été proposé.</p>\n";
		$poursuivre="n";
	}
	else {
		$sql="SELECT * FROM archivage_eleves WHERE ine='$ine';";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
	
		if(mysqli_num_rows($res_ele)==0) {
			// On ne devrait pas arriver là.
			echo "<p>L'élève dont l'INE serait $ine n'est pas dans la table 'archivage_eleves'.</p>\n";
			$poursuivre="n";
		}
	}

	if($poursuivre=="y") {
		$lig_ele=mysqli_fetch_object($res_ele);

		// Infos élève
		//$ine: INE de l'élève (identifiant commun aux tables 'eleves' et 'archivage_disciplines')
		if($ine=="") {
			$ine=$lig_ele->no_gep;
		}
		$ele_nom=$lig_ele->nom;
		$ele_prenom=$lig_ele->prenom;
		$naissance=$lig_ele->naissance;
		// Classe actuelle:
		$classe=get_nom_classe($id_classe);

		// Liste des années conservées pour l'élève choisi:
		$sql="SELECT DISTINCT annee FROM archivage_disciplines WHERE ine='$ine' ORDER BY annee";
		$res_annees=mysqli_query($GLOBALS["mysqli"], $sql);
		$annee_precedente="";
		$annee_suivante="";
		$derniere_periode_annee_precedente=1;
		if(mysqli_num_rows($res_annees)>0){
			while($lig_annee=mysqli_fetch_object($res_annees)){
				if($lig_annee->annee!=$annee_scolaire){
					$annee_precedente=$lig_annee->annee;
					$sql="SELECT DISTINCT num_periode FROM archivage_disciplines WHERE ine='$ine' AND annee='$annee_precedente' ORDER BY num_periode DESC";
					$res_per_prec=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_per_prec)>0){
						$lig_per_prec=mysqli_fetch_object($res_per_prec);
						$derniere_periode_annee_precedente=$lig_per_prec->num_periode;
					}
				}
				else{
					if($lig_annee=mysqli_fetch_object($res_annees)){
						$annee_suivante=$lig_annee->annee;
					}
					break;
				}
			}
		}

		// Liste des périodes pour l'année choisie:
		$sql="SELECT DISTINCT num_periode FROM archivage_disciplines WHERE ine='$ine' AND annee='$annee_scolaire' ORDER BY num_periode";
		$res_periodes=mysqli_query($GLOBALS["mysqli"], $sql);

		if(mysqli_num_rows($res_periodes)==0){
			// Ca ne doit pas arriver...
		}
		else{
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
				echo "<a href='".$_SERVER['PHP_SELF']."?";
				if($logineleve!="") {
					echo "id_classe=$id_classe&amp;logineleve=$logineleve";
				}
				else {
					echo "ine=$ine";
				}
				echo "&amp;annee_scolaire=$annee_precedente&amp;num_periode=$derniere_periode_annee_precedente&amp;mode=bull_simp'><img src='../images/icons/back_.png' width='16' height='14' alt='Année précédente' /></a> ";
			}
			echo "<b>$annee_scolaire</b>";
			if($annee_suivante!=""){
				//echo " <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;logineleve=$logineleve&amp;annee_scolaire=$annee_suivante&amp;num_periode=1'>&gt;</a>";
				echo " <a href='".$_SERVER['PHP_SELF']."?";
				if($logineleve!="") {
					echo "id_classe=$id_classe&amp;logineleve=$logineleve";
				}
				else {
					echo "ine=$ine";
				}
				echo "&amp;annee_scolaire=$annee_suivante&amp;num_periode=1&amp;mode=bull_simp'><img src='../images/icons/forward_.png' width='16' height='14' alt='Année suivante' /></a>";
			}

			echo "</li>\n";

			$cpt=0;
			while($lig_periode=mysqli_fetch_object($res_periodes)){
				//if($cpt>0){echo " - ";}
				//echo "<li style='display:inline;'>\n";
				if($lig_periode->num_periode!=$num_periode){
					echo "<li style='display:inline; border: 1px solid black; padding: 0.2em 0.2em 0 0.2em;'>\n";
					//echo "<div style='display:block; border: 1px solid black; width:20%;'>\n";
					echo "<a href='".$_SERVER['PHP_SELF']."?";
					if($logineleve!="") {
						echo "id_classe=$id_classe&amp;logineleve=$logineleve";
					}
					else {
						echo "ine=$ine";
					}
					echo "&amp;annee_scolaire=$annee_scolaire&amp;num_periode=$lig_periode->num_periode&amp;mode=bull_simp'>P".$lig_periode->num_periode."</a>";
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

		$sql="SELECT DISTINCT nom_periode, classe FROM archivage_disciplines WHERE ine='$ine' AND num_periode='$num_periode' AND annee='$annee_scolaire'";
		$res_per=mysqli_query($GLOBALS["mysqli"], $sql);

		if(mysqli_num_rows($res_per)==0){
			$nom_periode="période $num_periode";
			$classe_ant="???";
		}
		else{
			$lig_per=mysqli_fetch_object($res_per);
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

		$sql="SELECT * FROM archivage_disciplines WHERE annee='$annee_scolaire' AND num_periode='$num_periode' AND ine='$ine' AND special='' ORDER BY ordre_matiere, matiere;";
		//echo "$sql<br />\n";
		$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);

		if(mysqli_num_rows($res_mat)==0){
			// On ne devrait pas arriver là.
			echo "<tr><td colspan='6'>Aucun résultat enregistré???</td></tr>\n";
		}
		else{
			while($lig_mat=mysqli_fetch_object($res_mat)){
				echo "<tr>\n";
				echo "<td>";
				echo "<b>".htmlspecialchars(stripslashes($lig_mat->matiere))."</b><br />\n";
				echo "<span class='info_prof'>".htmlspecialchars(stripslashes($lig_mat->prof))."</span>\n";
				echo "</td>\n";
				echo "<td class='td_note_classe'>$lig_mat->moymin</td>\n";
				echo "<td class='td_note_classe'>$lig_mat->moyclasse</td>\n";
				echo "<td class='td_note_classe'>$lig_mat->moymax</td>\n";
				echo "<td class='td_note bold'>$lig_mat->note</td>\n";
				echo "<td>".htmlspecialchars(stripslashes($lig_mat->appreciation))."</td>\n";
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
		$res_aid=mysqli_query($GLOBALS["mysqli"], $sql);
		/*
		if(mysql_num_rows($res_aid)==0){
			// On ne devrait pas arriver là.
			echo "<tr><td colspan='6'>Aucun résultat enregistré???";
			//echo "<br />$sql";
			echo "</td></tr>\n";
		}
		else{
		*/
		if(mysqli_num_rows($res_aid)>0){
			while($lig_aid=mysqli_fetch_object($res_aid)){
				echo "<tr>\n";
				echo "<td>";
				echo "<b>".htmlspecialchars(stripslashes($lig_aid->type_nom))." : ".htmlspecialchars(stripslashes($lig_aid->nom_aid))."</b><br />\n";
				echo "<span class='info_prof'>".htmlspecialchars(stripslashes($lig_aid->responsables))."</span>\n";
				echo "</td>\n";
				echo "<td class='td_note_classe'>$lig_aid->moyenne_aid</td>\n";
				echo "<td class='td_note_classe'>$lig_aid->min_aid</td>\n";
				echo "<td class='td_note_classe'>$lig_aid->max_aid</td>\n";
				echo "<td class='td_note bold'>$lig_aid->note_aid";
				echo "</td>\n";
				echo "<td>";
				if (($lig_aid->note_sur_aid != 20) and ($lig_aid->note_aid !='-')) {
					echo "(note sur ".$lig_aid->note_sur_aid.") ";
				}

				echo htmlspecialchars(stripslashes($lig_aid->appreciation))."</td>\n";
				echo "</tr>\n";
			}
		}

		echo "</table>\n";


		// Affichage des absences
		$sql="SELECT * FROM archivage_disciplines WHERE annee='$annee_scolaire' AND num_periode='$num_periode' AND ine='$ine' AND special='ABSENCES'";
		//echo "$sql<br />\n";
		$res_abs=mysqli_query($GLOBALS["mysqli"], $sql);

		if(mysqli_num_rows($res_abs)==0){
			echo "<p>Aucune information sur les absences/retards.</p>\n";
		}
		elseif(mysqli_num_rows($res_abs)>1){
			echo "<p>Bizarre: Il y a plus d'un enregistrement pour cette élève, cette période et cette année.</p>\n";
		}
		else{
			$lig_abs=mysqli_fetch_object($res_abs);

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
		$res_avis=mysqli_query($GLOBALS["mysqli"], $sql);


		echo "<table class='table_annee_anterieure' width='100%' summary='Avis du conseil'>\n";
		echo "<tr>\n";
		echo "<td align='left'>\n";
		echo "<p><i>Avis du Conseil de classe : </i><br />\n";

		$prof_suivi="";
		if(mysqli_num_rows($res_avis)==0){
			echo "Aucune information sur l'avis du conseil de classe.</p>\n";
		}
		elseif(mysqli_num_rows($res_avis)>1){
			echo "Bizarre : Il y a plus d'un enregistrement pour cette élève, cette période et cette année.</p>\n";
			$prof_suivi="?";
		}
		else{
			$lig_avis=mysqli_fetch_object($res_avis);
			echo "$lig_avis->appreciation</p>\n";
			$prof_suivi=$lig_avis->prof;
		}
		echo "</td>\n";
		echo "<td align='center'>\n";
		echo "<p>Classe suivie par : <b>$prof_suivi</b></p>\n";
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
	$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);

	if(mysqli_num_rows($res_ele)==0){
		// On ne devrait pas arriver là.
		echo "<p>L'élève dont le login serait $logineleve n'est pas dans la table 'eleves'.</p>\n";
	}
	else{
		$lig_ele=mysqli_fetch_object($res_ele);

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
		$res_annees=mysqli_query($GLOBALS["mysqli"], $sql);
		$annee_precedente="";
		$annee_suivante="";
		//$derniere_periode_annee_precedente=1;
		if(mysqli_num_rows($res_annees)>0){
			while($lig_annee=mysqli_fetch_object($res_annees)){
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
					if($lig_annee=mysqli_fetch_object($res_annees)){
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
		$res_avis=mysqli_query($GLOBALS["mysqli"], $sql);

		if(mysqli_num_rows($res_avis)==0){
			echo "Aucune information sur l'avis du conseil de classe.</p>\n";
		}
		else{
			echo "<table class='table_annee_anterieure' width='100%' summary='Avis du conseil'>\n";
			echo "<tr>\n";
			echo "<th>Année-scolaire</th>\n";
			echo "<th>Avis du conseil de classe</th>\n";
			echo "<th>Classe suivie par</th>\n";
			echo "</tr>\n";
			while($lig_avis=mysqli_fetch_object($res_avis)){
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

function tab_choix_anterieure($logineleve,$id_classe=NULL,$ine=''){

	if($logineleve!="") {
		$sql="SELECT * FROM eleves WHERE login='$logineleve';";
	}
	elseif($ine!="") {
		$sql="SELECT * FROM archivage_eleves WHERE ine='$ine';";
	}
	$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);

	if(mysqli_num_rows($res_ele)==0){
		//echo "<p>Aucun élève dans la classe $classe pour la période '$nom_periode'.</p>\n";
		if($logineleve!="") {
			echo "<p>L'élève dont le login serait $logineleve n'est pas dans la table 'eleves'.</p>\n";
		}
		elseif($ine!="") {
			echo "<p>L'élève dont l'INE serait $ine n'est pas dans la table 'archivage_eleves'.</p>\n";
		}
	}
	else{
		$lig_ele=mysqli_fetch_object($res_ele);

		// Infos élève
		if($ine=='') {
			$ine=$lig_ele->no_gep;
		}
		//$nom=$lig_ele->nom;
		//$prenom=$lig_ele->prenom;
		$ele_nom=$lig_ele->nom;
		$ele_prenom=$lig_ele->prenom;
		$naissance=$lig_ele->naissance;
		//$naissance2=formate_date($lig_ele->naissance);


		echo "<p>Liste des années scolaires et périodes pour lesquelles des données concernant $ele_prenom $ele_nom ";
		if((isset($id_classe))&&($id_classe!='')) {
			$classe=get_nom_classe($id_classe);
			echo "(<i>$classe</i>) ";
		}
		echo "ont été conservées:</p>\n";

		//echo "<p>Liste des années scolaires et périodes pour lesquelles des données concernant $ele_prenom $ele_nom (<i>$classe</i>) ont été conservées:</p>\n";

		// Récupérer les années-scolaires et périodes pour lesquelles on trouve l'INE dans archivage_disciplines
		//$sql="SELECT DISTINCT annee,num_periode,nom_periode FROM archivage_disciplines WHERE ine='$ine' ORDER BY annee DESC, num_periode ASC";
		//$sql="SELECT DISTINCT annee FROM archivage_disciplines WHERE ine='$ine' ORDER BY annee DESC;";
		$sql="SELECT DISTINCT annee FROM archivage_disciplines WHERE ine='$ine' ORDER BY annee ASC;";
		$res_ant=mysqli_query($GLOBALS["mysqli"], $sql);

		if(mysqli_num_rows($res_ant)==0){
			echo "<p>Aucun résultat antérieur n'a été conservé pour cet élève.</p>\n";

			$sql="SELECT 1=1 FROM eleves e WHERE e.login='$logineleve' AND e.no_gep!='NULL' AND e.no_gep!='';";
			//echo "$sql<br />";
			$test_ine=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test_ine)==0) {
				echo "<p style='color:red'>Le numéro INE de cet élève n'est pas renseigné.</p>\n";
			}
		}
		else{

			unset($tab_annees);

			$nb_annees=mysqli_num_rows($res_ant);

			//echo "<p>Bulletins simplifiés:</p>\n";
			//echo "<table border='0'>\n";
			$alt=1;
			echo "<table class='boireaus table_annee_anterieure' summary='Bulletins'>\n";
			echo "<tr class='lig$alt'>\n";
			echo "<th rowspan='".$nb_annees."' valign='top'>Bulletins simplifiés:</th>";
			$cpt=0;
			while($lig_ant=mysqli_fetch_object($res_ant)){

				$tab_annees[]=$lig_ant->annee;

				if($cpt>0){
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
				}
				echo "<td style='font-weight:bold;'>$lig_ant->annee : </td>\n";

				$sql="SELECT DISTINCT num_periode,nom_periode FROM archivage_disciplines WHERE ine='$ine' AND annee='$lig_ant->annee' ORDER BY num_periode ASC";
				$res_ant2=mysqli_query($GLOBALS["mysqli"], $sql);

				if(mysqli_num_rows($res_ant2)==0){
					echo "<td>";
					echo "Aucun résultat antérieur n'a été conservé pour cet élève.";
					echo "</td>\n";
				}
				else{
					$cpt=0;
					while($lig_ant2=mysqli_fetch_object($res_ant2)){
						//if($cpt>0){echo "<td> - </td>\n";}
						echo "<td style='text-align:center;'>";
						echo "<a href='".$_SERVER['PHP_SELF']."?";
						if($logineleve!="") {
							echo "id_classe=$id_classe&amp;logineleve=$logineleve";
						}
						else {
							echo "ine=$ine";
						}
						echo "&amp;annee_scolaire=$lig_ant->annee&amp;num_periode=$lig_ant2->num_periode&amp;mode=bull_simp'>$lig_ant2->nom_periode</a>";
						echo "</td>\n";
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
			$alt=1;
			echo "<table class='boireaus table_annee_anterieure' summary='Avis des conseils'>\n";
			echo "<tr class='lig$alt'>\n";
			echo "<th rowspan='".$nb_annees."' valign='top'>Avis des conseils de classes:</th>";
			$cpt=0;
			for($i=0;$i<count($tab_annees);$i++){
				if($cpt>0){
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
				}
				//echo "<td style='font-weight:bold;'>\n";
				echo "<td>\n";

				echo "Année-scolaire <a href='".$_SERVER['PHP_SELF']."?";
				if($logineleve!="") {
					echo "logineleve=$logineleve";
				}
				else {
					echo "ine=$ine";
				}
				echo "&amp;annee_scolaire=".$tab_annees[$i]."&amp;mode=avis_conseil";
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
	//echo "$sql<br />";
	$res_regime=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig_ele=mysqli_fetch_object($res_regime)){
		$regime=$lig_ele->regime;
		$doublant=$lig_ele->doublant;
	}
	$del = sql_query1("delete from archivage_eleve2 where ine ='".$ine."'");
	$sql="INSERT INTO archivage_eleves2 SET
	ine='".$ine."',
	annee = '".$annee."',
	doublant='".addslashes($doublant)."',
	regime='".addslashes($regime)."'";
	//echo "$sql<br />";
	$res_insert_regime=mysqli_query($GLOBALS["mysqli"], $sql);
	// on traite la table archivage_eleve
	$test = sql_query1("select count(ine) from archivage_eleves where ine= '".$ine."'");
	if ($test == 0) {
		$sql="SELECT DISTINCT nom, prenom, no_gep, naissance, sexe FROM eleves WHERE login='".$login."'";
		//echo "$sql<br />";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele)==0) {
			return "<tr><td colspan='4'>Aucune donnée disponible pour l'élève dont l'identifiant est ".$login."</td></tr>";
			die();
		} else {
			while($lig_ele=mysqli_fetch_object($res_ele)){
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
				//echo "$sql<br />";
				$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res_insert){
					return "<tr><td colspan='4'><font color='red'>Erreur d'enregistrement des données pour l'élève dont l'identifiant est ".$login."</font></td></tr>";
					exit();
				} else {
					if ($param != 'y') {
						return "<tr><td class='small'>".$ine."</td><td class='small'>".$nom."</td><td class='small'>".$prenom."</td><td class='small'>".$naissance."</td></tr>";
					}
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
	$req_test = mysqli_query($GLOBALS["mysqli"], "SELECT nom, prenom, sexe, naissance FROM archivage_eleves WHERE (ine='".$login.$m."')");
	$test = mysqli_num_rows($req_test);
	if ($test!=0) {
		// un même identifiant existe déjà !
		// s'agit-il de la même personne. On considère que oui si les noms, prénom, date de naissance et sexe correspondent
		$nom = old_mysql_result($req_test,0,"nom");
		$prenom = old_mysql_result($req_test,0,"prenom");
		$sexe = old_mysql_result($req_test,0,"sexe");
		$naissance = old_mysql_result($req_test,0,"naissance");
		$test_unicite = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT login FROM eleves WHERE (nom='".$nom."' and prenom='".$prenom."' and sexe='".$sexe."' and naissance='".$naissance."')"));
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

	$res_eleve = mysqli_query($GLOBALS["mysqli"], "select ine from archivage_eleves");
	$nb_eleves = mysqli_num_rows($res_eleve);
	$k = 0;
	while($k < $nb_eleves){
	$ine = old_mysql_result($res_eleve,$k,"ine");
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

	$res_eleve = mysqli_query($GLOBALS["mysqli"], "select ine from archivage_eleves2");
	$nb_eleves = mysqli_num_rows($res_eleve);
	$k = 0;
	while($k < $nb_eleves){
	$ine = old_mysql_result($res_eleve,$k,"ine");
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

function check_acces_aa($logineleve) {

	$acces="n";

	if(getSettingValue('active_annees_anterieures')=="y") {
		if($_SESSION['statut']=="administrateur") {
			$acces="y";
		}
		elseif($_SESSION['statut']=="professeur") {
			// $AAProfTout
			// $AAProfPrinc
			// $AAProfClasses
			// $AAProfGroupes
		
			$AAProfTout=getSettingValue('AAProfTout');
			$AAProfPrinc=getSettingValue('AAProfPrinc');
			$AAProfClasses=getSettingValue('AAProfClasses');
			$AAProfGroupes=getSettingValue('AAProfGroupes');
		
			//echo "\$AAProfTout=$AAProfTout<br />";
			//echo "\$AAProfPrinc=$AAProfPrinc<br />";
			//echo "\$AAProfClasses=$AAProfClasses<br />";
			//echo "\$AAProfGroupes=$AAProfGroupes<br />";
		
			if($AAProfTout=="yes") {
				// Le professeur a accès aux données antérieures de tous les élèves
				$acces="y";
			}
			elseif($AAProfClasses=="yes") {
				// Le professeur a accès aux données antérieures des élèves des classes pour lesquelles il fournit un enseignement (sans nécessairement avoir tous les élèves de la classe)
				/*
				$sql="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_classes jgc, j_groupes_professeurs jgp
								WHERE jeg.login='$logineleve' AND
										jeg.id_groupe=jgc.id_groupe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='".$_SESSION['login']."';";
				*/
				$sql="SELECT 1=1 FROM j_eleves_classes jec, j_groupes_classes jgc, j_groupes_professeurs jgp
								WHERE jec.login='$logineleve' AND
										jec.id_classe=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='".$_SESSION['login']."';";
				//echo "$sql<br />";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$acces="y";
				}
			}
			elseif($AAProfGroupes=="yes") {
				// Le professeur a accès aux données antérieures des élèves des groupes auxquels il enseigne
				$sql="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_professeurs jgp
								WHERE jeg.login='$logineleve' AND
										jeg.id_groupe=jgp.id_groupe AND
										jgp.login='".$_SESSION['login']."';";
				//echo "$sql<br />";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$acces="y";
				}
			}
			elseif($AAProfPrinc=="yes") {
				// Le professeur a accès aux données antérieures des élèves dont il est Professeur Principal
				$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."' AND
																login='$logineleve';";
				//echo "$sql<br />";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$acces="y";
				}
			}
		}
		elseif($_SESSION['statut']=="cpe") {
			// $AACpeTout
			// $AACpeResp
		
			$AACpeTout=getSettingValue('AACpeTout');
			$AACpeResp=getSettingValue('AACpeResp');
		
			if($AACpeTout=="yes") {
				// Le CPE a accès aux données antérieures de tous les élèves
				$acces="y";
			}
			elseif($AACpeResp=="yes") {
				$sql="SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='".$_SESSION['login']."' AND
															e_login='$logineleve'";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$acces="y";
				}
			}
		}
		elseif($_SESSION['statut']=="scolarite") {
			// $AAScolTout
			// $AAScolResp
		
			$AAScolTout=getSettingValue('AAScolTout');
			$AAScolResp=getSettingValue('AAScolResp');
		
			if($AAScolTout=="yes") {
				// Les comptes Scolarité ont accès aux données antérieures de tous les élèves
				$acces="y";
			}
			elseif($AAScolResp=="yes") {
				$sql="SELECT 1=1 FROM j_eleves_classes jec, j_scol_classes jsc
								WHERE jec.login='$logineleve' AND
										jec.id_classe=jsc.id_classe AND
										jsc.login='".$_SESSION['login']."';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$acces="y";
				}
			}
		}
		elseif($_SESSION['statut']=="responsable") {
			$AAResponsable=getSettingValue('AAResponsable');
		
			if($AAResponsable=="yes") {
				// Est-ce que le $logineleve est bien celui d'un élève dont le responsable est responsable?
				$sql="SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e WHERE rp.login='".$_SESSION['login']."' AND
																					rp.pers_id=r.pers_id AND
																					r.ele_id=e.ele_id AND
																					e.login='$logineleve'";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)==1) {
					$acces="y";
				}
			}
		}
		elseif($_SESSION['statut']=="eleve") {
			$AAEleve=getSettingValue('AAEleve');
		
			if($AAEleve=="yes") {
				$logineleve=$_SESSION['login'];
				$acces="y";
			}
		}
		elseif($_SESSION['statut']=="autre") {
			//$sql="SELECT 1=1 FROM droits_speciaux ds, droits_utilisateurs du, droits_statut dst WHERE dst.id=ds.id_statut AND du.id_statut=dst.id AND du.login_user='".$_SESSION['login']."' AND ds.nom_fichier='/voir_anna' AND ds.autorisation='V';";
			//$sql="SELECT 1=1 FROM droits_speciaux ds WHERE ds.id_statut='".$_SESSION['statut_special_id']."' AND ds.nom_fichier='/voir_anna' AND ds.autorisation='V';";
		
			$sql="SELECT 1=1 FROM droits_speciaux ds WHERE ds.id_statut='".$_SESSION['statut_special_id']."' AND ds.nom_fichier='/mod_annees_anterieures/ajax_bulletins.php' AND ds.autorisation='V';";
			$res_acces=mysqli_query($GLOBALS["mysqli"], $sql);
		
			if(mysqli_num_rows($res_acces)>0) {
				$acces="y";
			}
		}
	}

	return $acces;
}

//$tab_periodes=array();
// On va retourner par la fonction un $tab_periodes vide ou non selon que l'accès est autorisé ou non et qu'il y a des données archivées
function check_acces_et_liste_periodes($logineleve,$id_classe) {
	$tab_periodes=array();

	if(getSettingValue('active_annees_anterieures')=="y") {

		$acces=check_acces_aa($logineleve);

		if($acces=="y") {

			//$tab_annee=array();
			$tab_periodes=array();
		
			$sql="SELECT * FROM eleves WHERE login='$logineleve';";
			$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		
			if(mysqli_num_rows($res_ele)==0) {
				// On ne devrait pas arriver là.
				echo "<p>L'élève dont le login serait '$logineleve' n'est pas dans la table 'eleves'.</p>\n";
			}
			else {
				$lig_ele=mysqli_fetch_object($res_ele);
		
				// Infos élève
				//$ine: INE de l'élève (identifiant commun aux tables 'eleves' et 'archivage_disciplines')
				$ine=$lig_ele->no_gep;
				//$ele_nom=$lig_ele->nom;
				//$ele_prenom=$lig_ele->prenom;
				//$naissance=$lig_ele->naissance;
				//$naissance2=formate_date($lig_ele->naissance);

				// Classe actuelle:
				$classe=get_nom_classe($id_classe);
		
				// Liste des années conservées pour l'élève choisi:
				$sql="SELECT DISTINCT annee FROM archivage_disciplines WHERE ine='$ine' ORDER BY annee";
				//echo "$sql<br />";
				$res_annees=mysqli_query($GLOBALS["mysqli"], $sql);
				$annee_precedente="";
				$annee_suivante="";
				$derniere_periode_annee_precedente=1;
				if(mysqli_num_rows($res_annees)>0) {
					$cpt=0;
					while($lig_annee=mysqli_fetch_object($res_annees)) {
						//$tab_annee[$cpt]['annee']=$lig_annee->annee;
						//$tab_annee[$cpt]['annee']['annee']=$lig_annee->annee;
		
						$sql="SELECT DISTINCT num_periode FROM archivage_disciplines WHERE ine='$ine' AND annee='$lig_annee->annee' ORDER BY num_periode";
						//echo "$sql<br />";
						$res_periodes=mysqli_query($GLOBALS["mysqli"], $sql);
		
						if(mysqli_num_rows($res_periodes)==0) {
							// ANOMALIE
						}
						else {
							while($lig_per=mysqli_fetch_object($res_periodes)) {
								//$tab_annee[$cpt]['annee']['max_per']=$lig_per->num_periode;
								$tab_periodes[]=$lig_annee->annee."|".$lig_per->num_periode;
							}
						}
		
						if(!isset($annee_scolaire)) {
							// A FAIRE: VOIR si $annee_scolaire est en SESSION... pour qu'en passant à un autre élève, on récupère les mêmes années,...
							$annee_scolaire=$lig_annee->annee;
						}
		
						if($lig_annee->annee!=$annee_scolaire) {
							$annee_precedente=$lig_annee->annee;
							$sql="SELECT DISTINCT num_periode FROM archivage_disciplines WHERE ine='$ine' AND annee='$annee_precedente' ORDER BY num_periode DESC";
							$res_per_prec=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_per_prec)>0) {
								$lig_per_prec=mysqli_fetch_object($res_per_prec);
								$derniere_periode_annee_precedente=$lig_per_prec->num_periode;
							}
						}
						$cpt++;
					}
				}
			}
		}
	}
	return $tab_periodes;
}

function get_indice_annee_precedente($annee_scolaire,$tab_periodes) {

	$annee_precedente="";
	$tab_annee=array();
	$tab_indice_annee=array();
	for($i=0;$i<count($tab_periodes);$i++) {
		$tmp_tab=explode("|",$tab_periodes[$i]);
		$annee_courante=$tmp_tab[0];
	
		if($annee_courante!=$annee_precedente) {
			$tab_annee[]=$annee_courante;
			$tab_indice_annee[]=$i;
	
			$annee_precedente=$annee_courante;
		}
	}

	for($i=0;$i<count($tab_annee);$i++) {
		if($tab_annee[$i]==$annee_scolaire) {
			if(isset($tab_indice_annee[$i-1])) {
				return $tab_indice_annee[$i-1];
			}
			else {
				return "";
			}
		}
	}
}

function get_indice_annee_suivante($annee_scolaire,$tab_periodes) {

	$annee_precedente="";
	$tab_annee=array();
	$tab_indice_annee=array();
	for($i=0;$i<count($tab_periodes);$i++) {
		$tmp_tab=explode("|",$tab_periodes[$i]);
		$annee_courante=$tmp_tab[0];
	
		if($annee_courante!=$annee_precedente) {
			$tab_annee[]=$annee_courante;
			$tab_indice_annee[]=$i;

			//echo "\$tab_annee[]=$annee_courante;<br />
			//\$tab_indice_annee[]=$i;<br />";

			$annee_precedente=$annee_courante;
		}
	}

	for($i=0;$i<count($tab_annee);$i++) {
		if($tab_annee[$i]==$annee_scolaire) {
			if(isset($tab_indice_annee[$i+1])) {
				return $tab_indice_annee[$i+1];
			}
			else {
				return "";
			}
			break;
		}
	}
}

function get_annee_suivante($annee_scolaire,$tab_periodes) {

	$annee_precedente="";
	$tab_annee=array();
	$tab_indice_annee=array();
	for($i=0;$i<count($tab_periodes);$i++) {
		$tmp_tab=explode("|",$tab_periodes[$i]);
		$annee_courante=$tmp_tab[0];
	
		if($annee_courante!=$annee_precedente) {
			$tab_annee[]=$annee_courante;
			$tab_indice_annee[]=$i;

			//echo "\$tab_annee[]=$annee_courante;<br />
			//\$tab_indice_annee[]=$i;<br />";

			$annee_precedente=$annee_courante;
		}
	}

	for($i=0;$i<count($tab_annee);$i++) {
		if($tab_annee[$i]==$annee_scolaire) {
			if(isset($tab_annee[$i+1])) {
				return $tab_annee[$i+1];
			}
			else {
				return "";
			}
			break;
		}
	}
}

function get_annee_precedente($annee_scolaire,$tab_periodes) {

	$annee_precedente="";
	$tab_annee=array();
	$tab_indice_annee=array();
	for($i=0;$i<count($tab_periodes);$i++) {
		$tmp_tab=explode("|",$tab_periodes[$i]);
		$annee_courante=$tmp_tab[0];
	
		if($annee_courante!=$annee_precedente) {
			$tab_annee[]=$annee_courante;
			$tab_indice_annee[]=$i;

			//echo "\$tab_annee[]=$annee_courante;<br />
			//\$tab_indice_annee[]=$i;<br />";

			$annee_precedente=$annee_courante;
		}
	}

	for($i=0;$i<count($tab_annee);$i++) {
		if($tab_annee[$i]==$annee_scolaire) {
			if(isset($tab_annee[$i-1])) {
				return $tab_annee[$i-1];
			}
			else {
				return "";
			}
			break;
		}
	}
}

function affiche_onglets_aa($logineleve, $id_classe, $tab_periodes, $indice_onglet=0) {
	$max_per=0;

	echo "<style type='text/css'>
.conteneur_t_onglet {
	float:left;
	/*border: 1px dashed red;*/
}

.t_onglet {
	float:left;
	margin-bottom:-1px;

	border-top: 1px solid black;
	border-left: 1px solid black;
	border-right: 1px solid black;

	border-radius: 6px 20px 0px 0px;
	-moz-border-radius-topleft: 6px;
	-moz-border-radius-topright: 20px;

	padding: 0.2em 0.2em 0 0.2em;
	margin: 0.2em 0.2em 0 0.2em;
	z-index:100;
}

.t_onglet a {
	text-decoration: none;
	/*
	On ne force pas le fond... il est modifié par javascript pour faire ressortir l'onglet actif
	background-color: white;
	*/
	color: black;
}

.t_onglet_annee {
	float:left;
	margin-left: 10px;
	margin-right: 10px;
	margin-bottom:-1px;
	border-top: 1px solid black;
	border-left: 1px solid black;
	border-right: 1px solid black;

	color: black;
	background-color: white;

	padding: 0.2em 0.2em 0 0.2em;
	margin: 0.2em 0 0 2px;
	z-index:100;
}

.onglet {
/*
	border: 1px solid black;
	border-top: 0px;
	border-right: 0px;
	margin-left: 4px;
*/
	/*padding: 3px;*/
	color:black;
	font-style: normal;
	font-weight: normal;
}
</style>\n";

	echo "<div id='div_annees_anterieures' class='infobulle_corps' style='position: absolute; top: 220px; right: 20px; width: 700px; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>\n";
	
		echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: 700px; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_annees_anterieures')\">\n";
			echo "<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>\n";
			echo "<a href='#' onClick=\"cacher_div('div_annees_anterieures');return false;\">\n";
			echo "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />\n";
			echo "</a>\n";
			echo "</div>\n";
	
			echo "<div id='titre_entete_annees_anterieures'>AAA</div>\n";
		echo "</div>\n";
		
		echo "<div id='corps_annees_anterieures' class='infobulle_corps' style='color: #ffffff; cursor: auto; font-weight: bold; padding: 0px; height: 15em; width: 700px; overflow: auto;'>";

			if(count($tab_periodes)==0) {
				echo "<p>Aucune donnée archivée pour ".get_nom_prenom_eleve($logineleve).".</p>\n";
			}
			else {
				$t_annee_prec="";
				for($i=0;$i<count($tab_periodes);$i++) {
					$tmp_tab=explode("|",$tab_periodes[$i]);
					$annee_courante=$tmp_tab[0];
					$periode_courante=$tmp_tab[1];
		
					if($i==$indice_onglet) {
						$annee_choisie=$annee_courante;
						$periode_choisie=$periode_courante;
					}

					$annee_precedente=get_annee_precedente($annee_courante,$tab_periodes);
					$indice_annee_precedente=get_indice_annee_precedente($annee_courante,$tab_periodes);

					$annee_suivante=get_annee_suivante($annee_courante,$tab_periodes);
					$indice_annee_suivante=get_indice_annee_suivante($annee_courante,$tab_periodes);

					if($periode_courante>$max_per) {$max_per=$periode_courante;}

					//echo "\$annee_courante=$annee_courante<br />";
			
					if($annee_courante!=$t_annee_prec) {
						// On crée un nouvel onglet d'année
						if($t_annee_prec!="") {echo "</div>\n";}
			
						$t_annee_prec=$annee_courante;

						echo "\n\n<!-- Nouvelle annee anterieure -->\n";

						echo "<div id='conteneur_t_annee_$i' class='conteneur_t_onglet'";
						if($i!=$indice_onglet) {
							echo " style='display:none;'";
						}
						echo ">\n";
			
						echo "<div id='t_annee_$i' class='t_onglet_annee'>\n";
						// AJOUTER Lien ANNEE PRECEDENTE
						if($annee_precedente!="") {
							//echo "<a id='lien_annee_precedente' href='../mod_annees_anterieures/popup_annee_anterieure.php?id_classe=$id_classe&logineleve=$logineleve&annee_scolaire=$annee_precedente&num_periode=1&mode=bull_simp' onclick=\"document.getElementById('span_annee_courante').innerHTML='$annee_precedente'; affiche_onglet_aa('$logineleve','$id_classe','".$annee_precedente."','1','".$indice_annee_precedente."');return false;\"><img src='../images/icons/back.png' width='16' height='16' alt='Année suivante' /> </a>\n";
							echo "<a id='lien_annee_precedente' href='../mod_annees_anterieures/popup_annee_anterieure.php?id_classe=$id_classe&logineleve=$logineleve&annee_scolaire=$annee_precedente&num_periode=1&mode=bull_simp' onclick=\"affiche_onglet_aa('$logineleve','$id_classe','".$annee_precedente."','1','".$indice_annee_precedente."');return false;\"><img src='../images/icons/back.png' width='16' height='16' alt='Année suivante' /> </a>\n";
						}
						//echo "<span id='span_annee_courante'>".$annee_courante."</span>";
						echo $annee_courante;

						// AJOUTER Lien ANNEE SUIVANTE
						if($annee_suivante!="") {
							//echo "<a id='lien_annee_suivante' href='../mod_annees_anterieures/popup_annee_anterieure.php?id_classe=$id_classe&logineleve=$logineleve&annee_scolaire=$annee_suivante&num_periode=1&mode=bull_simp' onclick=\"document.getElementById('span_annee_courante').innerHTML='$annee_suivante'; affiche_onglet_aa('$logineleve','$id_classe','".$annee_suivante."','1','".$indice_annee_suivante."');return false;\"> <img src='../images/icons/forward.png' width='16' height='16' alt='Année suivante' /></a>\n";
							echo "<a id='lien_annee_suivante' href='../mod_annees_anterieures/popup_annee_anterieure.php?id_classe=$id_classe&logineleve=$logineleve&annee_scolaire=$annee_suivante&num_periode=1&mode=bull_simp' onclick=\"affiche_onglet_aa('$logineleve','$id_classe','".$annee_suivante."','1','".$indice_annee_suivante."');return false;\"> <img src='../images/icons/forward.png' width='16' height='16' alt='Année suivante' /></a>\n";
						}
						echo "</div>\n\n";
					}
			
					// Ajout de l'onglet de période
					echo "<div id='t_periode_$i' class='t_onglet'";
					if($i!=$indice_onglet) {
						echo " style='border-bottom-color: black;'";
					}
					echo ">\n";
		
					//echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$logineleve&amp;onglet=$i' onclick=\"affiche_onglet('$i');return false;\">P$periode_courante</a>\n";
					echo "<a href='../mod_annees_anterieures/popup_annee_anterieure.php?id_classe=$id_classe&logineleve=$logineleve&annee_scolaire=$annee_courante&num_periode=$periode_courante&mode=bull_simp' onclick=\"affiche_onglet_aa('$logineleve','$id_classe','$annee_courante','$periode_courante','$i');return false;\">P$periode_courante</a>\n";
		
					echo "</div>\n";
			
				}
				echo "</div>\n\n";
			
				echo "<div style='clear:both;'></div>\n";
		
				echo "<div id='contenu_onglet' class='onglet' style='";
				echo "margin-left: 1px; margin-right: 1px; ";
				//echo "border: 1px dashed green;";
				echo "'>\n";
				echo "<h2>$annee_choisie - $periode_choisie</h2>\n";

				echo "<p>Onglet n°$indice_onglet</p>\n";

				// Récupérer/afficher les données de l'année/période pour cet élève
				// Non... on le fait via ajax_bulletins.php

				echo "</div>\n";

			}

		echo "</div>\n";

	echo "</div>\n";

	echo "<script type='text/javascript'>
	// <![CDATA[
	//function affiche_annees_anterieures(login_eleve,id_classe) {
	function affiche_annees_anterieures(login_eleve,id_classe,annee_scolaire) {
		document.getElementById('titre_entete_annees_anterieures').innerHTML='Années antérieures de '+login_eleve;

		if(document.getElementById('conteneur_t_annee_0')) {
			for(i=0;i<=".count($tab_periodes).";i++) {
				if(document.getElementById('conteneur_t_annee_'+i)) {
					document.getElementById('conteneur_t_annee_'+i).style.display='none';
				}
			}

			document.getElementById('conteneur_t_annee_0').style.display='';
		}

		for(i=0;i<=".count($tab_periodes).";i++) {
			if(document.getElementById('t_periode_'+i)) {
				document.getElementById('t_periode_'+i).style.backgroundColor='grey';
			}
		}

		if(document.getElementById('t_periode_0')) {
			document.getElementById('t_periode_0').style.backgroundColor='white';
		}

		new Ajax.Updater($('contenu_onglet'),'../mod_annees_anterieures/ajax_bulletins.php?logineleve='+login_eleve+'&id_classe='+id_classe+'&annee_scolaire='+annee_scolaire,{method: 'get'});
	}

	function affiche_onglet_aa(logineleve,id_classe,annee_scolaire,num_periode,indice_onglet) {
		if(annee_scolaire!='') {

			if(document.getElementById('conteneur_t_annee_'+indice_onglet)) {
				for(i=0;i<=".count($tab_periodes).";i++) {
					if(document.getElementById('conteneur_t_annee_'+i)) {
						document.getElementById('conteneur_t_annee_'+i).style.display='none';
					}
				}

				document.getElementById('conteneur_t_annee_'+indice_onglet).style.display='';
			}

			for(i=0;i<=".count($tab_periodes).";i++) {
				if(document.getElementById('t_periode_'+i)) {
					document.getElementById('t_periode_'+i).style.borderBottom='1 px solid black';
					document.getElementById('t_periode_'+i).style.backgroundColor='grey';
				}
			}
	
			if(document.getElementById('t_periode_'+indice_onglet)) {
				document.getElementById('t_periode_'+indice_onglet).style.borderBottom='0 px solid black';
					document.getElementById('t_periode_'+indice_onglet).style.backgroundColor='white';
			}
	
			if(document.getElementById('t_annee_'+indice_onglet)) {
				document.getElementById('t_annee_'+indice_onglet).style.borderBottom='0 px solid black';
			}
	
			new Ajax.Updater($('contenu_onglet'),'../mod_annees_anterieures/ajax_bulletins.php?logineleve='+logineleve+'&id_classe='+id_classe+'&annee_scolaire='+annee_scolaire+'&num_periode='+num_periode,{method: 'get'});
		}
	}

	//]]>
</script>\n";

}
?>
