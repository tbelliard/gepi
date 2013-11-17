<?php
	echo "<tr>\n";

	echo "<td rowspan='3' colspan='4' width='".$fb_largeur_col_disc."%' style='font-size:".$fb_titretab."pt;'>\n";
	echo "DISCIPLINES";
	echo "</td>\n";

	echo "<td colspan='2' style='border: 1px solid black; text-align:center; font-size:".$fb_titretab."pt;'>\n";
	echo "NOTE MOYENNE<br />affectée du coefficient";
	echo "</td>\n";

	echo "<td rowspan='3' valign='bottom' width='".$fb_largeur_col_note."%' style='line-height: ".$fb_txttab_lineheight."pt;'>\n";
	echo "Note<br />moyenne<br />de la<br />classe";
	//echo "Note<br />moyenne<br />de la<br />classe<br />0 à 20";
	echo "</td>\n";

	echo "<td rowspan='3' style='line-height: ".$fb_txttab_lineheight."pt; font-size:".$fb_titretab."pt;' width='".$fb_largeur_col_app."%'>\n";
	echo "Appréciations des professeurs";
	echo "</td>\n";

	echo "</tr>\n";
	//=====================
	echo "<tr>\n";

	// La colonne discipline est dans le rowspan de la ligne précédente.

	//echo "<td style='border: 1px solid black; text-align:center;'>\n";
	echo "<td colspan='2' style='font-weight:bold; font-size:".$fb_titretab."pt;'>\n";
	//echo "3ème à option";
	echo "3ème";
	echo "</td>\n";

	// Les colonnes note moyenne de la classe et appréciations des profs sont dans le rowspan de la ligne précédente.

	echo "</tr>\n";
	//=====================
	echo "<tr>\n";

	// La colonne discipline est dans le rowspan de la ligne précédente.

	//echo "<td style='border: 1px solid black; text-align:center;'>\n";
	echo "<td width='".$fb_largeur_col_opt."%' style='font-size:".$fb_titretab."pt;'>\n";
	//echo "LV2";
	//echo $fb_intitule_col[1];
	echo $tabmatieres["fb_intitule_col"][1];
	echo "</td>\n";

	echo "<td width='".$fb_largeur_col_opt."%' style='line-height: ".$fb_txttab_lineheight."pt; font-size:".$fb_titretab."pt;'>\n";
	//echo "A module découverte professionnelle<br />6 heures";
	//echo $fb_intitule_col[2];
	echo $tabmatieres["fb_intitule_col"][2];
	echo "</td>\n";

	// Les colonnes note moyenne de la classe et appréciations des profs sont dans le rowspan de la ligne précédente.

	echo "</tr>\n";

	//=====================

	$TOTAL=0;
	$SUR_TOTAL=array();
	$SUR_TOTAL[1]=0;
	$SUR_TOTAL[2]=0;
	$temoin_NOTNONCA=0;
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// FRANCAIS ET MATHS
	//for($j=101;$j<=122;$j++){
	for($j=101;$j<=102;$j++){
		$temoin_note_non_numerique="n";
		//if($tabmatieres[$j][0]!=''){
		//if($tabmatieres[$j][0]!=''){
		if(($tabmatieres[$j][0]!='')&&($tabmatieres[$j]['socle']=='n')){
			//if($tabmatieres[$j][-1]!='NOTNONCA'){
			if(($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')){

				//$tabmatieres[$j]['fb_col'][1]
				//$SUR_TOTAL+=$tabmatieres[$j][-2]*20;
				//echo "<tr><td>".$tabmatieres[$j]['fb_col'][1]."</td></tr>";
				//if(ctype_digit($tabmatieres[$j]['fb_col'][1])){$SUR_TOTAL[1]+=$tabmatieres[$j]['fb_col'][1];}
				//if(ctype_digit($tabmatieres[$j]['fb_col'][2])){$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];}


				// ************************************
				// A REVOIR
				// PROBLEME AVEC CES TOTAUX: SI UN ELEVE EST AB, DI ou NN, IL NE FAUDRAIT PAS AUGMENTER???...
				if((mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][1]))==0)&&($tabmatieres[$j][-1]!='PTSUP')){
					$SUR_TOTAL[1]+=$tabmatieres[$j]['fb_col'][1];
				}
				if((mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][2]))==0)&&($tabmatieres[$j][-1]!='PTSUP')){
					$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];
				}
				// ************************************

				//echo "<tr><td>$SUR_TOTAL[1]</td></tr>";



				// Initialisation
				$lignes_opt_facultative_alternative="";



				echo "<tr>\n";

				// Discipline
				//echo "<td style='border: 1px solid black; text-align:left;'>\n";
				// Les trois colonnes du colspan='3' servent à couvrir les découpages de colonnes sur les lignes B2i,...
				echo "<td colspan='4' style='font-size:".$fb_textetab."pt;'";
				//if($tabmatieres[$j][-1]=='PTSUP'){
				//	echo " rowspan='2'";
				//}
				echo ">\n";
				//echo "<p class='discipline'>";
				//echo "<span class='discipline'>";

				if(!isset($tabmatieres[$j]["lig_speciale"])) {

						echo "<p class='discipline'>";
						echo ucfirst(accent_min(mb_strtolower($tabmatieres[$j][0])));
					//}
				}
				else{
					// Lignes spéciales: LV2 ou SCPHY
					echo "<p class='discipline'>";
					echo "Langue vivante : ";
					// IL FAUDRAIT UN DISPOSITIF POUR INDIQUER A QUELLE OPTION (langue ou scphy) l'élève est inscrit
					echo "......................";
					echo "<br />\n";
					echo "ou sciences physiques";
				}
				//echo "</span>\n";
				echo "</p>\n";
				echo "</td>\n";


				// EXTRACTION POUR LA(ES) COLONNE(S) "NOTE MOYENNE affectée du coefficient"
				$valeur_tmp="&nbsp;";
				//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
				$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
				$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_note)){
					$lig_note=mysqli_fetch_object($res_note);
					//echo "$lig_note->note";
					//$valeur_tmp="$lig_note->note";
					//$valeur_tmp=$lig_note->note*$tabmatieres[$j][-2];
					if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
						$valeur_tmp=$lig_note->note*$tabmatieres[$j][-2];
						/*
						if($tabmatieres[$j][-1]=='PTSUP'){
							$TOTAL+=max(0,$lig_note->note-10);
						}
						else{
						*/
						// Le cas PTSUP est calculé plus loin
						if($tabmatieres[$j][-1]!='PTSUP'){
							//$TOTAL+=$lig_note->note;
							$TOTAL+=$valeur_tmp;
						}
						else {
							$ptsup=$lig_note->note-10;
							if($ptsup>0){
								//echo "$ptsup";
								$valeur_tmp=$ptsup;
								//if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
									$TOTAL+=$ptsup;
								//}
							}
							else{
								//echo "0";
								$valeur_tmp=0;
							}
						}
					}
					else{
						$valeur_tmp=$lig_note->note;
						$temoin_note_non_numerique="y";

						if(($tabmatieres[$j][-1]!='PTSUP')){
							if($num_fb_col==1){
								$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
							}
							else{
								$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
							}
						}
					}
					//$note="$lig_note->note";
				}
				else{
					// FAUT-IL UN TEMOIN POUR DECREMENTER LE SUR_TOTAL ?
					if(($tabmatieres[$j][-1]!='PTSUP')){
						if($num_fb_col==1){
							$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
						}
						else{
							$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
						}
					}
				}


				/*
				if($tabmatieres[$j][-1]=='PTSUP'){
					$valeur_tmp=max($valeur_tmp-10,0);
				}
				*/



				// COLONNE série LV2
				echo "<td ";
				//echo "style='border: 1px solid black; text-align:right;'>\n";
				/*
				if($tabmatieres[$j][-1]=='PTSUP'){
					echo "style='border: 1px solid black; text-align:center;'>\n";
					echo "<b>Points > à 10</b>";
				}
				else{
				*/
					/*
					$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
					$res_note=mysql_query($sql);
					if(mysql_num_rows($res_note)){
						$lig_note=mysql_fetch_object($res_note);
						echo "$lig_note->note";
						if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
							$TOTAL+=$lig_note->note;
						}
						//$note="$lig_note->note";
					}
					else{
						echo "&nbsp;";
						//$note="&nbsp;";
					}
					*/
					echo "style='border: 1px solid black; font-size:".$fb_textetab."pt;";

					/*
					if(($tabmatieres[$j][0]=="OPTION FACULTATIVE")&&($nom_opt=='Latin, grec ou LV2')) {
						echo " background-color:lightgrey;";
					}
					*/

					if($num_fb_col==1){
						echo " text-align:center;";
						echo "'>\n";
						echo "$valeur_tmp";
					}
					else{
						echo " text-align:right;";
						if($tabmatieres[$j]['fb_col'][1]=="X") {
							echo " background-color:lightgrey;";
						}
						echo "'>\n";
						echo "&nbsp;";
					}

					//$nb=$tabmatieres[$j][-2]*20;
					//echo " / $nb";
					//if($tabmatieres[$j][-1]!='PTSUP'){
					if(($tabmatieres[$j][-1]!='PTSUP')&&(mb_strtolower($tabmatieres[$j][0])!='découverte professionnelle 6 heures')) {
						if($tabmatieres[$j]['fb_col'][1]!="X"){
							if(($temoin_note_non_numerique=="n")||($num_fb_col==2)) {
								echo " / ".$tabmatieres[$j]['fb_col'][1];
							}
						}
					}

					// DEBUG:
					// echo "<br />$TOTAL/$SUR_TOTAL[1]";


				//}
				echo "</td>\n";


				// COLONNE série DP6
				//echo "style='border: 1px solid black; text-align:center;'>\n";
				echo "<td ";
				
					//echo "style='border: 1px solid black; text-align:right;'>\n";
					echo "style='border: 1px solid black; font-size:".$fb_textetab."pt;";

					if(mb_strtolower($tabmatieres[$j][0])=='langue vivante 2') {
						echo "background-color:lightgrey;";
					}

					if($num_fb_col==2){
						echo " text-align:center;";
						echo "'>\n";
						echo "$valeur_tmp";
					}
					else{
						echo " text-align:right;";
						echo "'>\n";
						echo "&nbsp;";
					}

					//$nb=$tabmatieres[$j][-2]*20;
					//echo " / $nb";
					//if($tabmatieres[$j][-1]!='PTSUP'){
					if(($tabmatieres[$j][-1]!='PTSUP')&&(mb_strtolower($tabmatieres[$j][0])!='langue vivante 2')) {
						if(($temoin_note_non_numerique=="n")||($num_fb_col==1)) {
							echo " / ".$tabmatieres[$j]['fb_col'][2];
						}
					}
				//}
				echo "</td>\n";


				// Moyenne classe
				echo "<td ";
				echo "style='border: 1px solid black; text-align:center; font-size:".$fb_textetab."pt;'>\n";
				if($fb_mode_moyenne==1){
					echo $moy_classe[$j];
				}
				else{
					//$sql="SELECT mat FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
					$sql="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
					$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res_mat)>0){
						$lig_mat=mysqli_fetch_object($res_mat);
						//echo "$lig_mat->mat: ";

						//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
						$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
						//echo "$sql<br />";
						$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_moy)>0){
							$lig_moy=mysqli_fetch_object($res_moy);
							echo "$lig_moy->moyenne_mat";
						}
					}
				}
				echo "</td>\n";




				// Appréciation
				echo "<td ";
				/*
				if($tabmatieres[$j][-1]=='PTSUP'){
					echo "rowspan='2' ";
				}
				*/
				//echo "style='border: 1px solid black; text-align:center;'>\n";
				echo "style='border: 1px solid black; text-align:left; font-size:".$fb_textetab."pt;'>\n";

				if($avec_app=="y") {
					$sql="SELECT appreciation FROM notanet_app na,
													notanet_corresp nc
												WHERE na.login='$lig1->login' AND
													nc.notanet_mat='".$tabmatieres[$j][0]."' AND
													nc.matiere=na.matiere;";
					//echo "$sql<br />";
					$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res_app)>0){
						$lig_app=mysqli_fetch_object($res_app);
						echo "$lig_app->appreciation";
					}
					else{
						echo "&nbsp;";
					}
				}
				else {
					echo "&nbsp;";
				}

				echo "</td>\n";


				echo "</tr>\n";


			
			}
			else{
				//if($tabmatieres[$j][-4]!='non dispensee dans l etablissement'){
				if($tabmatieres[$j][-1]=="NOTNONCA"){
					$temoin_NOTNONCA++;
					//echo "<!-- \$temoin_NOTNONCA=$temoin_NOTNONCA \n\$tabmatieres[$j][0]=".$tabmatieres[$j][0]."-->\n";
				}
				//}
			}
			// ...=====...($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')

		}
		else{

			// CE CAS NE SE PRODUIT PAS... PAS ENTRE 101 et 102, mais en fin de boucle $j

			// Cas de la série professionnelle sans option de série:
			// Il faut quand même ajouter l'option alternative pour la série professionnelle avec option DP6h
			if(isset($tabmatieres[$j]['fb_lig_alt'])) {
				echo "<tr>\n";

				echo "<td colspan='4' style='font-size:".$fb_textetab."pt;'>\n";
				echo "<p class='discipline'>".$tabmatieres[$j]['fb_lig_alt']."</p>\n";
				echo "</td>\n";

				echo "<td style='border: 1px solid black; text-align:left; background-color:lightgrey;'>&nbsp;</td>\n";
				echo "<td style='border: 1px solid black; text-align:right; font-size:".$fb_textetab."pt;'>/".$tabmatieres[$j]['fb_col'][2]."</td>\n";
				echo "<td style='border: 1px solid black; text-align:left;'>&nbsp;</td>\n";
				echo "<td style='border: 1px solid black; text-align:left;'>&nbsp;</td>\n";

				echo "</tr>\n";
			}

			// $tabmatieres[$j][0]==0, mais il faut quand même afficher la ligne:
			// CAS PARTICULIER DE LA LIGNE DECOUVERTE PROFESSIONNELLE INUTILE MAIS PRESENTE POUR LES SERIES TECHNOLOGIQUE SANS DP6 ET PROFESSIONNELLE SANS DP6

			// CETTE LIGNE SPECIALE N'EST PLUS UTILISEE
			
			// Fin du isset($tabmatieres[$j]["lig_speciale"])
		}
	}


	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// LV1 ET SC.PHY EN UNE SEULE LIGNE

	// Il faut récupérer celle des deux matières suivie par l'élève $lig1->login et afficher la moyenne correspondante

	$tmp_nom_mat_lv="...............";


	$temoin_note_non_numerique="n";
	$tmp_note="&nbsp;";
	$tmp_moy="&nbsp;";
	$valeur_tmp="&nbsp;";
	$j=103;
	//$sql="SELECT mat,note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
	$sql="SELECT matiere,note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
	$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res_mat)>0){
		$lig_mat=mysqli_fetch_object($res_mat);
		//echo "$lig_mat->mat: ";
		$tmp_note=$lig_mat->note;
		if(($tmp_note!='AB')&&($tmp_note!='DI')&&($tmp_note!='NN')) {
			$valeur_tmp=$tmp_note*$tabmatieres[$j][-2];
			$TOTAL+=$valeur_tmp;
		}
		else{
			$valeur_tmp=$tmp_note;
			$temoin_note_non_numerique="y";

			if(($tabmatieres[$j][-1]!='PTSUP')){
				if($num_fb_col==1){
					$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
				}
				else{
					$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
				}
			}
		}


		if($avec_app=="y") {
			$sql="SELECT appreciation FROM notanet_app na,
											notanet_corresp nc
										WHERE na.login='$lig1->login' AND
											nc.notanet_mat='".$tabmatieres[$j][0]."' AND
											nc.matiere=na.matiere;";
			//echo "$sql<br />";
			$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($res_app)>0){
				$lig_app=mysqli_fetch_object($res_app);
				$tmp_app="$lig_app->appreciation";
			}
			else{
				$tmp_app="&nbsp;";
			}
		}
		else {
			$tmp_app="&nbsp;";
		}


		// DANS LE CAS DE LA LANGUE VIVANTE on récupère le nom de la langue
		$sql="SELECT nom_complet FROM matieres WHERE matiere='".$lig_mat->matiere."';";
		$res_nom_mat_lv=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

		if(mysqli_num_rows($res_nom_mat_lv)>0) {
			$lig_lv=mysqli_fetch_object($res_nom_mat_lv);
			$tmp_nom_mat_lv=$lig_lv->nom_complet;
		}
		else {
			$tmp_nom_mat_lv=$lig_mat->matiere;
		}


		//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
		$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
		//echo "$sql<br />";
		$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res_moy)>0){
			$lig_moy=mysqli_fetch_object($res_moy);
			$tmp_moy="$lig_moy->moyenne_mat";
		}
	}
	else{
		$j=104;
		//$sql="SELECT mat,note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
		$sql="SELECT matiere,note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
		$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res_mat)>0){
			$lig_mat=mysqli_fetch_object($res_mat);
			//echo "$lig_mat->mat: ";
			$tmp_note=$lig_mat->note;

			//$valeur_tmp=$tmp_note*$tabmatieres[$j][-2];
			if(($tmp_note!='AB')&&($tmp_note!='DI')&&($tmp_note!='NN')) {
				$valeur_tmp=$tmp_note*$tabmatieres[$j][-2];
				$TOTAL+=$valeur_tmp;
			}
			else{
				$valeur_tmp=$tmp_note;
				$temoin_note_non_numerique="y";

				if(($tabmatieres[$j][-1]!='PTSUP')){
					if($num_fb_col==1){
						$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
					}
					else{
						$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
					}
				}
			}


			if($avec_app=="y") {
				$sql="SELECT appreciation FROM notanet_app na,
												notanet_corresp nc
											WHERE na.login='$lig1->login' AND
												nc.notanet_mat='".$tabmatieres[$j][0]."' AND
												nc.matiere=na.matiere;";
				//echo "$sql<br />";
				$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_app)>0){
					$lig_app=mysqli_fetch_object($res_app);
					$tmp_app="$lig_app->appreciation";
				}
				else{
					$tmp_app="&nbsp;";
				}
			}
			else {
				$tmp_app="&nbsp;";
			}


			$tmp_moy="&nbsp;";
			//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
			$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
			//echo "$sql<br />";
			$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($res_moy)>0){
				$lig_moy=mysqli_fetch_object($res_moy);
				$tmp_moy="$lig_moy->moyenne_mat";
			}
		}
	}


	// CE N'EST PAS EN PTSUP...
	$SUR_TOTAL[1]+=$tabmatieres[$j]['fb_col'][1];
	$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];


	echo "<tr>\n";

	// Discipline
	echo "<td colspan='4' style='font-size:".$fb_textetab."pt;'";
	echo ">\n";
	echo "<p class='discipline'>";

	echo "Langue vivante : $tmp_nom_mat_lv";
	echo "<br />\n";
	echo "ou sciences physiques\n";

	echo "</p>\n";
	echo "</td>\n";


	echo "<td ";
	echo "style='border: 1px solid black; font-size:".$fb_textetab."pt;";
	if($num_fb_col==1){
		echo " text-align:center;";
		echo "'>\n";
		echo "$valeur_tmp";
	}
	else{
		echo " text-align:right;";
		if($tabmatieres[$j]['fb_col'][1]=="X"){echo " background-color:lightgrey;";}
		echo "'>\n";
		echo "&nbsp;";
	}

	//$nb=$tabmatieres[$j][-2]*20;
	//echo " / $nb";
	if($tabmatieres[$j]['fb_col'][1]!="X"){
		if(($temoin_note_non_numerique=="n")||($num_fb_col==2)) {
			echo " / ".$tabmatieres[$j]['fb_col'][1];
		}
	}
	echo "</td>\n";


	echo "<td ";
	//echo "style='border: 1px solid black; text-align:right;'>\n";
	echo "style='border: 1px solid black; font-size:".$fb_textetab."pt;";
	if($num_fb_col==2){
		echo " text-align:center;";
		echo "'>\n";
		echo "$valeur_tmp";
	}
	else{
		echo " text-align:right;";
		echo "'>\n";
		echo "&nbsp;";
	}
	//echo " / ".$tabmatieres[$j]['fb_col'][2];
	if(($temoin_note_non_numerique=="n")||($num_fb_col==1)) {
		echo " / ".$tabmatieres[$j]['fb_col'][2];
	}
	echo "</td>\n";



	// Moyenne classe
	echo "<td ";
	echo "style='border: 1px solid black; text-align:center; font-size:".$fb_textetab."pt;'>\n";
	echo $tmp_moy;
	echo "</td>\n";



	// Appréciation
	echo "<td ";
	echo "style='border: 1px solid black; text-align:center; font-size:".$fb_textetab."pt;'>\n";
	echo $tmp_app;
	//echo "DEBUG: $TOTAL";
	echo "</td>\n";




	echo "</tr>\n";

	// FIN DE LA LIGNE COMMUNE POUR LV1 et SC.PHY


	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// TOUTES LES AUTRES MATIERES:
	// VIE SOCIALE ET PROFESSIONNELLE jusqu'à DP6h
	//for($j=101;$j<=122;$j++){
	for($j=105;$j<=122;$j++){
		$temoin_note_non_numerique="n";
		//if($tabmatieres[$j][0]!=''){
		//if($tabmatieres[$j][0]!=''){
		if(($tabmatieres[$j][0]!='')&&($tabmatieres[$j]['socle']=='n')){
			//if($tabmatieres[$j][-1]!='NOTNONCA'){
			if(($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')){

				//$tabmatieres[$j]['fb_col'][1]
				//$SUR_TOTAL+=$tabmatieres[$j][-2]*20;
				//echo "<tr><td>".$tabmatieres[$j]['fb_col'][1]."</td></tr>";
				//if(ctype_digit($tabmatieres[$j]['fb_col'][1])){$SUR_TOTAL[1]+=$tabmatieres[$j]['fb_col'][1];}
				//if(ctype_digit($tabmatieres[$j]['fb_col'][2])){$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];}

				
				// ************************************
				// A REVOIR
				// PROBLEME AVEC CES TOTAUX: SI UN ELEVE EST AB, DI ou NN, IL NE FAUDRAIT PAS AUGMENTER???...
				if((mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][1]))==0)&&($tabmatieres[$j][-1]!='PTSUP')){
					$SUR_TOTAL[1]+=$tabmatieres[$j]['fb_col'][1];
				}
				if((mb_strlen(my_ereg_replace("[0-9]","",$tabmatieres[$j]['fb_col'][2]))==0)&&($tabmatieres[$j][-1]!='PTSUP')){
					$SUR_TOTAL[2]+=$tabmatieres[$j]['fb_col'][2];
				}
				// ************************************

				//echo "<tr><td>$SUR_TOTAL[1]</td></tr>";



				// Initialisation
				$lignes_opt_facultative_alternative="";



				echo "<tr>\n";

				// Discipline
				//echo "<td style='border: 1px solid black; text-align:left;'>\n";
				// Les trois colonnes du colspan='3' servent à couvrir les découpages de colonnes sur les lignes B2i,...
				echo "<td colspan='4' style='font-size:".$fb_textetab."pt;'";
				//if($tabmatieres[$j][-1]=='PTSUP'){
				//	echo " rowspan='2'";
				//}
				echo ">\n";
				//echo "<p class='discipline'>";
				//echo "<span class='discipline'>";

				if(!isset($tabmatieres[$j]["lig_speciale"])) {

						echo "<p class='discipline'>";
						echo ucfirst(accent_min(mb_strtolower($tabmatieres[$j][0])));
				}
				else{
					echo "<p class='discipline'>";
					echo ucfirst(accent_min(mb_strtolower($tabmatieres[$j]["lig_speciale"])));
				}
				echo "</p>\n";
				echo "</td>\n";



				// EXTRACTION POUR LA(ES) COLONNE(S) "NOTE MOYENNE affectée du coefficient"
				$valeur_tmp="&nbsp;";
				//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
				$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
				$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_note)){
					$lig_note=mysqli_fetch_object($res_note);
					//echo "$lig_note->note";
					//$valeur_tmp="$lig_note->note";
					//$valeur_tmp=$lig_note->note*$tabmatieres[$j][-2];
					if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
						$valeur_tmp=$lig_note->note*$tabmatieres[$j][-2];
						/*
						if($tabmatieres[$j][-1]=='PTSUP'){
							$TOTAL+=max(0,$lig_note->note-10);
						}
						else{
						*/
						// Le cas PTSUP est calculé plus loin
						if($tabmatieres[$j][-1]!='PTSUP'){
							//$TOTAL+=$lig_note->note;
							$TOTAL+=$valeur_tmp;
						}
						else {
							$ptsup=$lig_note->note-10;
							if($ptsup>0){
								//echo "$ptsup";
								$valeur_tmp=$ptsup;
								//if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
									$TOTAL+=$ptsup;
								//}
							}
							else{
								//echo "0";
								$valeur_tmp=0;
							}
						}
					}
					else{
						$valeur_tmp=$lig_note->note;
						$temoin_note_non_numerique="y";

						if(($tabmatieres[$j][-1]!='PTSUP')){
							if($num_fb_col==1){
								$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
							}
							else{
								$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
							}
						}
					}
					//$note="$lig_note->note";
				}
				else{
					// FAUT-IL UN TEMOIN POUR DECREMENTER LE SUR_TOTAL ?
					if(($tabmatieres[$j][-1]!='PTSUP')){
						if($num_fb_col==1){
							$SUR_TOTAL[1]-=$tabmatieres[$j]['fb_col'][1];
						}
						else{
							$SUR_TOTAL[2]-=$tabmatieres[$j]['fb_col'][2];
						}
					}
				}


				/*
				if($tabmatieres[$j][-1]=='PTSUP'){
					$valeur_tmp=max($valeur_tmp-10,0);
				}
				*/



				// COLONNE série LV2
				echo "<td ";
				//echo "style='border: 1px solid black; text-align:right;'>\n";
				/*
				if($tabmatieres[$j][-1]=='PTSUP'){
					echo "style='border: 1px solid black; text-align:center;'>\n";
					echo "<b>Points > à 10</b>";
				}
				else{
				*/
					/*
					$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
					$res_note=mysql_query($sql);
					if(mysql_num_rows($res_note)){
						$lig_note=mysql_fetch_object($res_note);
						echo "$lig_note->note";
						if(($lig_note->note!='AB')&&($lig_note->note!='DI')&&($lig_note->note!='NN')){
							$TOTAL+=$lig_note->note;
						}
						//$note="$lig_note->note";
					}
					else{
						echo "&nbsp;";
						//$note="&nbsp;";
					}
					*/
					echo "style='border: 1px solid black; font-size:".$fb_textetab."pt;";

					/*
					if(($tabmatieres[$j][0]=="OPTION FACULTATIVE")&&($nom_opt=='Latin, grec ou LV2')) {
						echo " background-color:lightgrey;";
					}
					*/

					if($num_fb_col==1){
						echo " text-align:center;";
						echo "'>\n";
						echo "$valeur_tmp";
					}
					else{
						echo " text-align:right;";
						if($tabmatieres[$j]['fb_col'][1]=="X") {
							echo " background-color:lightgrey;";
						}
						echo "'>\n";
						echo "&nbsp;";
					}

					//$nb=$tabmatieres[$j][-2]*20;
					//echo " / $nb";
					//if($tabmatieres[$j][-1]!='PTSUP'){
					if(($tabmatieres[$j][-1]!='PTSUP')&&(mb_strtolower($tabmatieres[$j][0])!='découverte professionnelle 6 heures')) {
						if($tabmatieres[$j]['fb_col'][1]!="X"){
							if(($temoin_note_non_numerique=="n")||($num_fb_col==2)) {
								echo " / ".$tabmatieres[$j]['fb_col'][1];
							}
						}
					}

					// DEBUG:
					// echo "<br />$TOTAL/$SUR_TOTAL[1]";


				//}
				echo "</td>\n";


				// COLONNE série DP6
				//echo "style='border: 1px solid black; text-align:center;'>\n";
				echo "<td ";
				/*
				if($tabmatieres[$j][-1]=='PTSUP'){
					echo "style='border: 1px solid black; text-align:center;'>\n";
					echo "<b>Points > à 10</b>";
				}
				else{
				*/
					/*
					echo "style='border: 1px solid black; text-align:right;'>\n";
					// TRICHE... Mon dispositif ne permet pas de gérer correctement ce double affichage
					// Il faudrait /40 pour la 2è LV ou découverte professionnelle 6H.
					if($tabmatieres[$j][0]=='DEUXIEME LANGUE VIVANTE'){
						echo " / 40";
					}
					else{
						echo " / 20";
					}
					*/
					//echo "style='border: 1px solid black; text-align:right;'>\n";
					echo "style='border: 1px solid black; font-size:".$fb_textetab."pt;";

					if(mb_strtolower($tabmatieres[$j][0])=='langue vivante 2') {
						echo "background-color:lightgrey;";
					}

					if($num_fb_col==2){
						echo " text-align:center;";
						echo "'>\n";
						echo "$valeur_tmp";
					}
					else{
						echo " text-align:right;";
						/*
						if($tabmatieres[$j][0]=="OPTION FACULTATIVE"){
							echo " background-color:lightgrey;";
						}
						*/
						echo "'>\n";
						echo "&nbsp;";
					}

					//$nb=$tabmatieres[$j][-2]*20;
					//echo " / $nb";
					//if($tabmatieres[$j][-1]!='PTSUP'){
					if(($tabmatieres[$j][-1]!='PTSUP')&&(mb_strtolower($tabmatieres[$j][0])!='langue vivante 2')) {
						if(($temoin_note_non_numerique=="n")||($num_fb_col==1)) {
							echo " / ".$tabmatieres[$j]['fb_col'][2];
						}
					}
				//}
				echo "</td>\n";


				// Moyenne classe
				echo "<td ";
				/*
				if($tabmatieres[$j][-1]=='PTSUP'){
					echo "rowspan='2' ";
				}
				*/
				echo "style='border: 1px solid black; text-align:center; font-size:".$fb_textetab."pt;'>\n";
				if($fb_mode_moyenne==1){
					echo $moy_classe[$j];
				}
				else{
					//$sql="SELECT mat FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
					$sql="SELECT matiere FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
					$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res_mat)>0){
						$lig_mat=mysqli_fetch_object($res_mat);
						//echo "$lig_mat->mat: ";

						//$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND mat='".$lig_mat->mat."'";
						$sql="SELECT ROUND(AVG(note),1) moyenne_mat FROM notanet WHERE id_classe='$id_classe[$i]' AND matiere='".$lig_mat->matiere."' AND note!='AB' AND note!='DI' AND note!='NN';";
						//echo "$sql<br />";
						$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_moy)>0){
							$lig_moy=mysqli_fetch_object($res_moy);
							echo "$lig_moy->moyenne_mat";
						}
					}
				}
				echo "</td>\n";




				// Appréciation
				echo "<td ";
				//echo "style='border: 1px solid black; text-align:center;'>\n";
				echo "style='border: 1px solid black; text-align:left; font-size:".$fb_textetab."pt;'>\n";

				if($avec_app=="y") {
					$sql="SELECT appreciation FROM notanet_app na,
													notanet_corresp nc
												WHERE na.login='$lig1->login' AND
													nc.notanet_mat='".$tabmatieres[$j][0]."' AND
													nc.matiere=na.matiere;";
					//echo "$sql<br />";
					$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res_app)>0){
						$lig_app=mysqli_fetch_object($res_app);
						echo "$lig_app->appreciation";
					}
					else{
						echo "&nbsp;";
					}
				}
				else {
					echo "&nbsp;";
				}

				echo "</td>\n";

				echo "</tr>\n";
			}
			else{
				//if($tabmatieres[$j][-4]!='non dispensee dans l etablissement'){
				if($tabmatieres[$j][-1]=="NOTNONCA"){
					$temoin_NOTNONCA++;
					//echo "<!-- \$temoin_NOTNONCA=$temoin_NOTNONCA \n\$tabmatieres[$j][0]=".$tabmatieres[$j][0]."-->\n";
				}
				//}
			}
			// ...=====...($tabmatieres[$j][-1]!='NOTNONCA')&&($tabmatieres[$j][-4]!='non dispensee dans l etablissement')
		}
		else{

			// Cas de la série professionnelle sans option de série:
			// Il faut quand même ajouter l'option alternative pour la série professionnelle avec option DP6h
			if(isset($tabmatieres[$j]['fb_lig_alt'])) {
				echo "<tr>\n";

				echo "<td colspan='4' style='font-size:".$fb_textetab."pt;'>\n";
				echo "<p class='discipline'>".$tabmatieres[$j]['fb_lig_alt']."</p>\n";
				echo "</td>\n";

				echo "<td style='border: 1px solid black; text-align:left; background-color:lightgrey;'>&nbsp;</td>\n";
				echo "<td style='border: 1px solid black; text-align:right; font-size:".$fb_textetab."pt;'>/".$tabmatieres[$j]['fb_col'][2]."</td>\n";
				echo "<td style='border: 1px solid black; text-align:left;'>&nbsp;</td>\n";
				echo "<td style='border: 1px solid black; text-align:left;'>&nbsp;</td>\n";

				echo "</tr>\n";
			}

			// Fin du isset($tabmatieres[$j]["lig_speciale"])
		}
	}
	// FIN DE ...
	//=====================



	//=====================
	/*
	echo "<tr>\n";

	echo "<td style='border: 1px solid black; text-align:left; font-weight:bold;'>\n";
	echo "<p class='discipline'>";
	echo "Socle B2i";
	echo "</p>";
	echo "</td>\n";

	echo "<td colspan='3' style='border: 1px solid black; text-align:center; font-weight:bold;'>\n";
	echo "&nbsp;";
	echo "</td>\n";

	echo "<td style='border: 1px solid black; text-align:left;'>\n";
	echo "&nbsp;";
	echo "</td>\n";

	echo "<td style='border: 1px solid black; text-align:left;'>\n";
	echo "&nbsp;";
	echo "</td>\n";

	echo "</tr>\n";
	//=====================
	echo "<tr>\n";

	echo "<td style='border: 1px solid black; text-align:left; font-weight:bold;'>\n";
	echo "<p class='discipline'>";
	echo "Socle Niveau A2 de langue";
	echo "</p>";
	echo "</td>\n";

	echo "<td colspan='3' style='border: 1px solid black; text-align:center; font-weight:bold;'>\n";
	echo "&nbsp;";
	echo "</td>\n";

	echo "<td style='border: 1px solid black; text-align:left;'>\n";
	echo "&nbsp;";
	echo "</td>\n";

	echo "<td style='border: 1px solid black; text-align:left;'>\n";
	echo "&nbsp;";
	echo "</td>\n";

	echo "</tr>\n";
	*/
	//=====================


	//=====================

	if($temoin_NOTNONCA>0){
		// ON TRAITE LES MATIERES NOTNONCA
		echo "<tr>\n";

		echo "<td colspan='4' style='border: 1px solid black; text-align:left; font-style:italic; font-weight:bold; font-size:".$fb_textetab."pt;'>\n";
		echo "A titre indicatif";
		echo "</td>\n";

		//echo "<td colspan='2' style='font-weight:bold; font-size:".$fb_titretab."pt; line-height: ".$fb_tittab_lineheight."pt;'>\n";
		echo "<td colspan='2' style='font-weight:bold; font-size:".$fb_titretab."pt; line-height: ".$fb_tittab_lineheight."pt;'>\n";
		echo "TOTAL DES POINTS";
		echo "</td>\n";

		/*
		echo "<td style='font-weight:bold; font-size:".$fb_titretab."pt; line-height: ".$fb_tittab_lineheight."pt;'>\n";
		echo "TOTAL DES POINTS";
		echo "</td>\n";
		*/


		$nb_info_bis=$temoin_NOTNONCA+1;
		echo "<td rowspan='$nb_info_bis' style='border: 1px solid black; text-align:center; font-size:".$fb_textetab."pt;'>\n";
		echo "&nbsp;";
		echo "</td>\n";


		// Colonne Appréciations
		echo "<td style='border: 1px solid black; text-align:center;'>\n";
		echo "&nbsp;";
		echo "</td>\n";

		echo "</tr>\n";

		$num_lig=0;
		// On repasse en revue toutes les matières en ne retenant que celles qui sont NOTNONCA
		for($j=101;$j<=122;$j++){
			//if($tabmatieres[$j][0]!=''){
			//if(($tabmatieres[$j][0]!='')&&($tabmatieres[$j][-1]=='NOTNONCA')){
			if(($tabmatieres[$j][0]!='')&&($tabmatieres[$j][-1]=='NOTNONCA')&&($tabmatieres[$j]['socle']=='n')){
				if($tabmatieres[$j][-4]!='non dispensee dans l etablissement'){
					echo "<tr>\n";

					echo "<td colspan='4' style='border: 1px solid black; text-align:left; font-size:".$fb_textetab."pt;'>\n";
					echo "<p class='discipline'>";
					echo ucfirst(accent_min(mb_strtolower($tabmatieres[$j][0])));
					//echo "</p>";
					//echo "</td>\n";
					echo " : ";

					

					// Moyenne de l'élève
					//echo "<td style='border: 1px solid black; text-align:center;'>\n";
					//$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND matiere='".$tabmatieres[$j][0]."'";
					$sql="SELECT note FROM notanet WHERE login='$lig1->login' AND id_classe='$id_classe[$i]' AND notanet_mat='".$tabmatieres[$j][0]."'";
					$res_note=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res_note)){
						$lig_note=mysqli_fetch_object($res_note);
						echo $lig_note->note."/20";
					}
					else{
						echo "&nbsp;";
					}
					

					// Colonne total des lignes calculées (non NOTNONCA)...
					if($num_lig==0){
						$nb_info=$temoin_NOTNONCA;


						//echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right;'>\n";
						if($num_fb_col==1){
							echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:center; font-size:".$fb_textetab."pt;'>\n";
							//echo "$TOTAL / 220";
							echo "$TOTAL";
							echo " / ".$SUR_TOTAL[1];
							echo "</td>\n";

							echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right; font-size:".$fb_textetab."pt;'>\n";
							//echo "/ 240";
							echo " / ".$SUR_TOTAL[2];
							echo "</td>\n";
						}
						else{
							echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right; font-size:".$fb_textetab."pt;'>\n";
							//echo "/ 220";
							echo " / ".$SUR_TOTAL[1];
							echo "</td>\n";

							echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:center; font-size:".$fb_textetab."pt;'>\n";
							//echo "$TOTAL / 220";
							echo "$TOTAL";
							echo " / ".$SUR_TOTAL[2];
							echo "</td>\n";
						}
						//echo "</td>\n";

						//$num_lig++;
					}

					/*
					// Colonne Note moyenne de la classe
					if($num_lig==0) {
						$nb_info_bis=$nb_info+1;
						echo "<td rowspan='$nb_info_bis' style='border: 1px solid black; text-align:center;'>\n";
						echo "&nbsp;";
						echo "</td>\n";
					}
					*/

					// Appréciation
					echo "<td ";
					//style='border: 1px solid black; text-align:center;'>\n";
					//echo "&nbsp;";
					echo "style='border: 1px solid black; text-align:left; font-size:".$fb_textetab."pt;'>\n";

					if($avec_app=="y") {
						$sql="SELECT appreciation FROM notanet_app na,
														notanet_corresp nc
													WHERE na.login='$lig1->login' AND
														nc.notanet_mat='".$tabmatieres[$j][0]."' AND
														nc.matiere=na.matiere;";
						//echo "$sql<br />";
						$res_app=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_app)>0){
							$lig_app=mysqli_fetch_object($res_app);
							echo "$lig_app->appreciation";
						}
						else{
							echo "&nbsp;";
						}
					}
					else {
						echo "&nbsp;";
					}
					echo "</td>\n";


					$num_lig++;

					/*
					echo "<td style='border: 1px solid black; text-align:right;'>\n";
					echo "/20";
					echo "</td>\n";
					*/

					echo "</tr>\n";
				}
				else{
					// Matière 'non dispensee dans l etablissement'
					// On affiche seulement les intitulés et le total des barèmes...
					echo "<tr>\n";

					echo "<td colspan='4' style='border: 1px solid black; text-align:left; font-size:".$fb_textetab."pt;'>\n";
					echo "<p class='discipline'>";
					echo ucfirst(accent_min(mb_strtolower($tabmatieres[$j][0])));
					echo "</p>";
					echo "</td>\n";

				

					if($num_lig==0){
						$nb_info=$temoin_NOTNONCA;

						//echo "</td>\n";
						if($num_fb_col==1){
							echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:center;' font-size:".$fb_textetab."pt;>\n";
							//echo "$TOTAL / 220";
							echo "$TOTAL";
							echo " / ".$SUR_TOTAL[1];
							echo "</td>\n";

							echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right;' font-size:".$fb_textetab."pt;>\n";
							//echo "/ 240";
							echo " / ".$SUR_TOTAL[2];
							echo "</td>\n";
						}
						else{
							echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:right; font-size:".$fb_textetab."pt;'>\n";
							//echo "/ 220";
							echo " / ".$SUR_TOTAL[1];
							echo "</td>\n";

							echo "<td rowspan='$nb_info' style='border: 1px solid black; text-align:center; font-size:".$fb_textetab."pt;'>\n";
							//echo "$TOTAL / 220";
							echo "$TOTAL";
							echo " / ".$SUR_TOTAL[2];
							echo "</td>\n";
						}

						//$num_lig++;
					}

					// Appréciation
					echo "<td style='border: 1px solid black; text-align:center;'>\n";
					echo "&nbsp;";
					echo "</td>\n";

					$num_lig++;

					echo "</tr>\n";
				}
			}
		}
		// FIN DE LA BOUCLE SUR LA LISTE DES MATIERES
	}
	// FIN DU TRAITEMENT DES MATIERES NOTNONCA
?>
