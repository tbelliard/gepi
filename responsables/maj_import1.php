<?php
/*
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// Initialisations files
require_once("../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// INSERT INTO `droits` VALUES ('/responsables/maj_import1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour Sconet', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//**************** EN-TETE *****************
$titre_page = "Mise à jour eleves/responsables";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
echo "<p class=bold>";
echo "<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";


// On fournit les fichiers CSV générés depuis les XML de SCONET...
//if (!isset($is_posted)) {
if(!isset($step)) {
	echo "<h2>Import/mise à jour des élèves</h2>\n";

	echo "<p>Cette page est destinée à effectuer l'import des élèves et responsables d'après les modifications et ajouts effectués sur Sconet.</p>\n";

	echo "<p>Vous allez importer les fichiers <b>CSV</b> (<i><a href='../init_xml/lecture_xml_sconet.php?ad_retour=".$_SERVER['PHP_SELF']."'>générés</a> à partir des exports XML de Sconet</i>).<br />\nLes fichiers requis au cours de la procédure sont dans un premier temps ELEVES.CSV, puis les deux fichiers PERSONNES.CSV et ADRESSES.CSV, et enfin le fichier RESPONSABLES.CSV</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	//echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo add_token_field();
	echo "<input type=hidden name='step' value='1' />\n";
	//echo "<input type=hidden name='mode' value='1' />\n";
	echo "<p>Sélectionnez le fichier <b>ELEVES.CSV</b>:<br /><input type=\"file\" size=\"80\" name=\"ele_file\" /></p>\n";
	/*
	echo "<p>Et les fichiers de responsables:</p>\n";
	echo "<p>Sélectionnez le fichier <b>PERSONNES.CSV</b>:<br /><input type='file' size='80' name='pers_file' />\n";
	echo "<p>Sélectionnez le fichier <b>RESPONSABLES.CSV</b>:<br /><input type='file' size='80' name='resp_file' />\n";
	echo "<p>Sélectionnez le fichier <b>ADRESSES.CSV</b>:<br /><input type='file' size='80' name='adr_file' />\n";
	*/
	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p>Il est recommandé d'importer les informations élèves et de ne passer qu'ensuite à l'import des informations responsables.<br />\n";
	echo "<a href='".$_SERVER['PHP_SELF']."?is_posted=y&amp;step=3".add_token_in_url()."'>Passer néanmoins à la page d'importation des responsables</a></p>";
}
else{
	check_token();

	//if(!isset($_POST['step'])){
	switch($step){
		case 1:
		// Affichage des informations élèves
		echo "<h2>Import/mise à jour des élèves</h2>\n";

			echo "<p>Dans le tableau, les classes ne sont mentionnées qu'à titre informatif.<br />L'affectation dans les classes n'est pas (<i>encore</i>) assurée depuis cette page.</p>\n";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

			echo add_token_field();

			//echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "<input type='hidden' name='step' value='2' />\n";

			$csv_file = isset($_FILES["ele_file"]) ? $_FILES["ele_file"] : NULL;
			if(mb_strtoupper($csv_file['name']) == "ELEVES.CSV"){
				//$fp = dbase_open($csv_file['tmp_name'], 0);
				$fp=fopen($csv_file['tmp_name'],"r");

				if(!$fp){
					echo "<p>Impossible d'ouvrir le fichier ELEVES.CSV !</p>";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
				}
				else{
					//$tabchamps = array("ELENOET","ELE_ID");
					$tabchamps = array("ELENOM","ELEPRE","ELESEXE","ELEDATNAIS","ELENOET","ELE_ID","ELEDOUBL","ELENONAT","ELEREG","DIVCOD","ETOCOD_EP", "ELEOPT1", "ELEOPT2", "ELEOPT3", "ELEOPT4", "ELEOPT5", "ELEOPT6", "ELEOPT7", "ELEOPT8", "ELEOPT9", "ELEOPT10", "ELEOPT11", "ELEOPT12");

					$erreur=0;

					$nblignes=0;
					while(!feof($fp)) {
						$ligne=fgets($fp, 4096);
						if($nblignes==0){
							// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
							// On ne retient pas ces ajouts pour $en_tete
							$temp=explode(";",$ligne);
							for($i=0;$i<sizeof($temp);$i++){
								$temp2=explode(",",$temp[$i]);
								$en_tete[$i]=$temp2[0];
							}

							$nbchamps=sizeof($en_tete);
						}
						$nblignes++;
					}
					fclose ($fp);

					// On range dans tabindice les indices des champs retenus
					/*
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[] = $i;
							}
						}
					}
					*/
					$cpt_tmp=0;
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[$cpt_tmp]=$i;
								$cpt_tmp++;
							}
						}
					}

					//=========================
					$fp=fopen($csv_file['tmp_name'],"r");
					// On lit une ligne pour passer la ligne d'entête:
					$ligne = fgets($fp, 4096);
					$cpt=0;
					//$eleves=array();
					echo "<table border='1'>\n";
					echo "<tr style='background-color: rgb(150, 200, 240);'>\n";
					echo "<td style='text-align: center; font-weight: bold;'>Enregistrer<br />\n";

					echo "<a href=\"javascript:modifcase('coche')\">";
					echo "<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
					echo " / ";
					echo "<a href=\"javascript:modifcase('decoche')\">";
					echo "<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";

					echo "</td>\n";
					echo "<td style='text-align: center; font-weight: bold;'>Statut</td>\n";
					echo "<td style='text-align: center; font-weight: bold;'>elenoet</td>\n";
					echo "<td style='text-align: center; font-weight: bold;'>ele_id</td>\n";
					echo "<td style='text-align: center; font-weight: bold;'>Nom</td>\n";
					echo "<td style='text-align: center; font-weight: bold;'>Prénom</td>\n";
					echo "<td style='text-align: center; font-weight: bold;'>Sexe</td>\n";
					echo "<td style='text-align: center; font-weight: bold;'>Naissance</td>\n";
					echo "<td style='text-align: center; font-weight: bold;'>Doublement</td>\n";
					echo "<td style='text-align: center; font-weight: bold;'>N°NAT</td>\n";
					echo "<td style='text-align: center; font-weight: bold;'>Régime</td>\n";
					echo "<td style='text-align: center; font-weight: bold;'>Classe</td>\n";
					echo "</tr>\n";
					$cpt_modif=0;
					$cpt_new=0;
					$alt=1;
					for($k = 1; ($k < $nblignes+1); $k++){
						$temoin_modif="";
						$temoin_nouveau="";
						if(!feof($fp)){
							//=========================
							// MODIF: boireaus 20071024
							//$ligne = fgets($fp, 4096);
							$ligne = my_ereg_replace('"','',fgets($fp, 4096));
							//=========================
							if(trim($ligne)!=""){
								$tabligne=explode(";",$ligne);
								$affiche=array();
								for($i = 0; $i < count($tabchamps); $i++) {
									$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
								}

								//$sql="SELECT * FROM eleves WHERE elenoet='$affiche[4]'";
								$sql="SELECT * FROM eleves WHERE (elenoet='$affiche[4]' OR elenoet='".sprintf("%05d",$affiche[4])."')";
								$res1=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res1)>0){
									//$sql="UPDATE eleves SET ele_id='$affiche[5]' WHERE elenoet='$affiche[4]'";

									// FAUT-IL FAIRE LES UPDATE SANS CONTRÔLE OU SIGNALER LES MODIFS SEULEMENT...
									//$sql="UPDATE eleves SET ele_id='$affiche[5]' WHERE elenoet='$affiche[4]'";

									// STOCKER DANS UN TABLEAU ET AFFICHER SEULEMENT LES MODIFS DANS UN PREMIER TEMPS
									// CASES A COCHER POUR VALIDER


									//$eleves[$cpt]

									$lig_ele=mysqli_fetch_object($res1);
									//$tabtmp=explode("/",$affiche[3]);
									// $lig_ele->naissance!=$tabtmp[2]."-".$tabtmp[1]."-".$tabtmp[0])||

									/*
									if(($lig_ele->nom!=$affiche[0])||
									($lig_ele->prenom!=$affiche[1])||
									($lig_ele->sexe!=$affiche[2])||
									($lig_ele->naissance!=mb_substr($affiche[3],0,4)."-".mb_substr($affiche[3],4,2)."-".mb_substr($affiche[3],6,2))){
									*/

									// On ne retient que le premier prénom:
									$tab_prenom = explode(" ",$affiche[1]);
									$affiche[1] = traitement_magic_quotes(corriger_caracteres($tab_prenom[0]));

									$new_date=mb_substr($affiche[3],0,4)."-".mb_substr($affiche[3],4,2)."-".mb_substr($affiche[3],6,2);

									// Des stripslashes() pour les apostrophes dans les noms
									if((stripslashes($lig_ele->nom)!=stripslashes($affiche[0]))||
									(stripslashes($lig_ele->prenom)!=stripslashes($affiche[1]))||
									($lig_ele->sexe!=$affiche[2])||
									($lig_ele->naissance!=$new_date)||
									($lig_ele->no_gep!=$affiche[7])){
										$temoin_modif='y';
										$cpt_modif++;
									}
									else{
										if($lig_ele->ele_id!=$affiche[5]){
											// GROS PROBLEME SI LES elenoet et ele_id ne sont plus des clés primaires
										}
									}

									// TESTER DANS j_eleves_regime pour doublant et regime
									/*
										table -> $affiche[]
										ext. -> 0
										d/p -> 2

										if ($reg_regime == "0") {$regime = "ext.";}
										if ($reg_regime == "2") {$regime = "d/p";}
										if ($reg_regime == "3") {$regime = "int.";}
										if ($reg_regime == "4") {$regime = "i-e";}


										R pour doublant -> O
										- pour doublant -> N

									*/

									$sql="SELECT * FROM j_eleves_regime WHERE (login='$lig_ele->login')";
									$res2=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res2)>0){
										$tmp_regime="";
										$lig2=mysqli_fetch_object($res2);
										//=========================
										// MODIF: boireaus 20071024
										$tmp_new_regime=traite_regime_sconet($affiche[8]);
										//switch($affiche[8]){
										/*
										switch($tmp_new_regime){
											case 0:
												$tmp_regime="ext.";
												break;
											case 2:
												$tmp_regime="d/p";
												break;
											case 3:
												$tmp_regime="int.";
												break;
											case 4:
												$tmp_regime="i-e";
												break;
										}
										*/
										$temoin_pb_regime_inhabituel="n";
										if("$tmp_new_regime"=="ERR"){
											$tmp_regime="d/p";
											$temoin_pb_regime_inhabituel="y";
										}
										else{
											$tmp_regime=$tmp_new_regime;
										}
										//=========================


										if($tmp_regime!=$lig2->regime){
											$temoin_modif='y';
											$cpt_modif++;
										}


										$tmp_doublant="";
										switch($affiche[6]){
											case "O":
												$tmp_doublant="R";
												break;
											case "N":
												$tmp_doublant="-";
												break;
										}
										if($tmp_doublant!=$lig2->doublant){
											$temoin_modif='y';
											$cpt_modif++;
										}
									}
									else{
										// Apparemment, aucune info n'est encore saisie dans j_eleves_regime
									}


									// Rechercher s'il y a un changement de classe?


								}
								else{
									$temoin_nouveau='y';
									$cpt_new++;
									// C'est un nouvel arrivant...

									// AFFICHER ET STOCKER DANS UN TABLEAU...
									// SUR VALIDATION, INSéRER DANS 'eleves' ET PAR LA SUITE AFFECTER DANS DES CLASSES POUR TELLES ET TELLES PERIODES ET COCHER LES OPTIONS POUR TELLES ET TELLES PERIODES.

									// TRANSMETTRE VIA UN FORMULAIRE POUR PROCEDER AUX AJOUTS, ET POUR LES eleves ENCHAINER AVEC LE CHOIX DE CLASSE ET D'OPTIONS
								}


								if($temoin_modif=='y'){
									//echo "<tr style='background-color:green;'>\n";
									//echo "<tr>\n";
									$alt=$alt*(-1);
									echo "<tr style='background-color:";
									if($alt==1){
										echo "silver";
									}
									else{
										echo "white";
									}
									echo ";'>\n";

		/*
									echo "<td>$affiche[4]</td>\n";
									echo "<td>$affiche[5]</td>\n";
									echo "<td>$affiche[0]</td>\n";
									echo "<td>$affiche[1]</td>\n";
									echo "<td>$affiche[2]</td>\n";
									echo "<td>$affiche[3]</td>\n";
									echo "<td>$affiche[6]</td>\n";
									echo "<td>$affiche[7]</td>\n";
									echo "<td>$affiche[8]</td>\n";
									echo "<td>$affiche[9]</td>\n";
		*/

		/*
									echo "<td>$lig_ele->elenoet -> $affiche[4]</td>\n";
									echo "<td>$lig_ele->ele_id -> $affiche[5]</td>\n";
									echo "<td>$lig_ele->nom -> $affiche[0]</td>\n";
									echo "<td>$lig_ele->prenom -> $affiche[1]</td>\n";
									echo "<td>$lig_ele->sexe -> $affiche[2]</td>\n";
									echo "<td>$lig_ele->naissance -> $affiche[3]</td>\n";
									echo "<td>$affiche[6]</td>\n";
									echo "<td>$affiche[7]</td>\n";
									echo "<td>$affiche[8]</td>\n";
									echo "<td>$affiche[9]</td>\n";
		*/

									echo "<td style='text-align: center;'><input type='checkbox' id='check_".$cpt."' name='modif[]' value='$cpt' /></td>\n";

									echo "<td style='text-align: center; background-color: lightgreen;'>Modif</td>\n";

									// ELENOET:
									echo "<td style='text-align: center;'>";
									echo "$affiche[4]";
									echo "<input type='hidden' name='modif_".$cpt."_elenoet' value='$affiche[4]' />\n";
									echo "</td>\n";
									// ELE_ID:
									echo "<td style='text-align: center;'>";
									echo "$affiche[5]";
									echo "<input type='hidden' name='modif_".$cpt."_eleid' value='$affiche[5]' />\n";
									echo "<input type='hidden' name='modif_".$cpt."_login' value='$lig_ele->login' />\n";
									echo "</td>\n";

									echo "<td style='text-align: center;";
									if(stripslashes($lig_ele->nom)!=stripslashes($affiche[0])){
										echo " background-color:lightgreen;'>";
										if($lig_ele->nom!=''){
											echo stripslashes($lig_ele->nom)." <font color='red'>-&gt;</font>\n";
										}
									}
									else{
										echo "'>";
									}
									echo stripslashes($affiche[0]);
									echo "<input type='hidden' name='modif_".$cpt."_nom' value=\"$affiche[0]\" />\n";
									echo "</td>\n";

									echo "<td style='text-align: center;";
									if(stripslashes($lig_ele->prenom)!=stripslashes($affiche[1])){
										echo " background-color:lightgreen;'>";
										if($lig_ele->prenom!=''){
											echo stripslashes($lig_ele->prenom)." <font color='red'>-&gt;</font>\n";
										}
									}
									else{
										echo "'>";
									}
									echo stripslashes($affiche[1]);
									echo "<input type='hidden' name='modif_".$cpt."_prenom' value=\"$affiche[1]\" />\n";
									echo "</td>\n";

									echo "<td style='text-align: center;";
									if($lig_ele->sexe!=$affiche[2]){
										echo " background-color:lightgreen;'>";
										if($lig_ele->sexe!=''){
											echo "$lig_ele->sexe <font color='red'>-&gt;</font>\n";
										}
									}
									else{
										echo "'>";
									}
									echo "$affiche[2]";
									echo "<input type='hidden' name='modif_".$cpt."_sexe' value='$affiche[2]' />\n";
									echo "</td>\n";

									echo "<td style='text-align: center;";
									if($lig_ele->naissance!=$new_date){
										echo " background-color:lightgreen;'>";
										if($lig_ele->naissance!=''){
											echo "$lig_ele->naissance <font color='red'>-&gt;</font>\n";
										}
									}
									else{
										echo "'>";
									}
									echo "$new_date";
									echo "<input type='hidden' name='modif_".$cpt."_naissance' value='$new_date' />\n";
									echo "</td>\n";

									//echo "<td style='text-align: center;'>$affiche[6]</td>\n";
									echo "<td style='text-align: center;";
									//if($tmp_doublant!=$affiche[6]){
									if($tmp_doublant!=$lig2->doublant){
										echo " background-color:lightgreen;'>";
										if($lig2->doublant!=''){
											echo "$lig2->doublant <font color='red'>-&gt;</font>\n";
										}
									}
									else{
										echo "'>";
									}
									//echo "$affiche[6]";
									echo "$tmp_doublant";
									echo "<input type='hidden' name='modif_".$cpt."_doublant' value='$tmp_doublant' />\n";
									echo "</td>\n";


									echo "<td style='text-align: center;";
									if($lig_ele->no_gep!=$affiche[7]){
										echo " background-color:lightgreen;'>";
										if($lig_ele->no_gep!=''){
											echo "$lig_ele->no_gep <font color='red'>-&gt;</font>\n";
										}
									}
									else{
										echo "'>";
									}
									echo "$affiche[7]";
									echo "<input type='hidden' name='modif_".$cpt."_nonat' value='$affiche[7]' />\n";
									echo "</td>\n";

									//echo "<td style='text-align: center;'>$affiche[8]</td>\n";
									echo "<td style='text-align: center;";
									if($tmp_regime!=$lig2->regime){
										echo " background-color:lightgreen;'>";
										if($lig2->regime!=''){
											echo "$lig2->regime <font color='red'>-&gt;</font>\n";
										}
									}
									else{
										echo "'>";
									}
									//echo "$affiche[8]";
									if($temoin_pb_regime_inhabituel=="y"){
										echo "<span style='color:red'>$tmp_regime</span>";
									}
									else{
										echo "$tmp_regime";
									}
									//echo " <span style='color:red'>DEBUG: ".$affiche[8]."</span> ";
									echo "<input type='hidden' name='modif_".$cpt."_regime' value='$tmp_regime' />\n";
									echo "</td>\n";

									//echo "<td style='text-align: center; background-color: white;'>";
									echo "<td style='text-align: center;'>";
									echo "$affiche[9]";
									echo "</td>\n";

									echo "</tr>\n";
								}
								elseif($temoin_nouveau=='y'){
									//echo "<tr style='background-color:yellow;'>\n";
									//echo "<tr>\n";
									$alt=$alt*(-1);
									echo "<tr style='background-color:";
									if($alt==1){
										echo "silver";
									}
									else{
										echo "white";
									}
									echo ";'>\n";

									echo "<td style='text-align: center;'><input type='checkbox' id='check_".$cpt."' name='new[]' value='$cpt' /></td>\n";

									echo "<td style='text-align: center; background-color: rgb(150, 200, 240);'>Nouveau</td>\n";

									/*
									echo "<td>$affiche[4]</td>\n";
									echo "<td>$affiche[5]</td>\n";
									echo "<td>$affiche[0]</td>\n";
									echo "<td>$affiche[1]</td>\n";
									echo "<td>$affiche[2]</td>\n";
									echo "<td>$affiche[3]</td>\n";
									echo "<td>$affiche[6]</td>\n";
									echo "<td>$affiche[7]</td>\n";
									echo "<td>$affiche[8]</td>\n";
									echo "<td>$affiche[9]</td>\n";
									*/

									echo "<td style='text-align: center;'>";
									echo "$affiche[4]";
									echo "<input type='hidden' name='new_".$cpt."_elenoet' value='$affiche[4]' />\n";
									echo "</td>\n";

									echo "<td style='text-align: center;'>";
									echo "$affiche[5]";
									echo "<input type='hidden' name='new_".$cpt."_eleid' value='$affiche[5]' />\n";
									echo "</td>\n";

									echo "<td style='text-align: center;'>";
									echo stripslashes($affiche[0]);
									echo "<input type='hidden' name='new_".$cpt."_nom' value=\"$affiche[0]\" />\n";
									echo "</td>\n";

									echo "<td style='text-align: center;'>";
									echo stripslashes($affiche[1]);
									echo "<input type='hidden' name='new_".$cpt."_prenom' value=\"$affiche[1]\" />\n";
									echo "</td>\n";

									echo "<td style='text-align: center;'>";
									echo "$affiche[2]";
									echo "<input type='hidden' name='new_".$cpt."_sexe' value='$affiche[2]' />\n";
									echo "</td>\n";

									$new_date=mb_substr($affiche[3],0,4)."-".mb_substr($affiche[3],4,2)."-".mb_substr($affiche[3],6,2);
									echo "<td style='text-align: center;'>";
									echo "$new_date";
									echo "<input type='hidden' name='new_".$cpt."_naissance' value='$new_date' />\n";
									echo "</td>\n";


									$tmp_doublant="";
									switch($affiche[6]){
										case "O":
											$tmp_doublant="R";
											break;
										case "N":
											$tmp_doublant="-";
											break;
									}

									echo "<td style='text-align: center;'>";
									echo "$tmp_doublant";
									echo "<input type='hidden' name='new_".$cpt."_doublant' value='$tmp_doublant' />\n";
									echo "</td>\n";

									echo "<td style='text-align: center;'>";
									echo "$affiche[7]";
									echo "<input type='hidden' name='new_".$cpt."_nonat' value='$affiche[7]' />\n";
									echo "</td>\n";



									$tmp_regime="";
									//=========================
									// MODIF: boireaus 20071024
									$tmp_new_regime=traite_regime_sconet($affiche[8]);
									//switch($affiche[8]){
									/*
									switch($tmp_new_regime){
										case 0:
											$tmp_regime="ext.";
											break;
										case 2:
											$tmp_regime="d/p";
											break;
										case 3:
											$tmp_regime="int.";
											break;
										case 4:
											$tmp_regime="i-e";
											break;
									}
									*/
									if("$tmp_new_regime"=="ERR"){
										$tmp_regime="d/p";

										echo "<td style='text-align: center;'>";
										echo "<span style='color:red'>$tmp_regime</span>";
										//echo " <span style='color:red'>DEBUG: ".$affiche[8]."</span> ";
										echo "<input type='hidden' name='new_".$cpt."_regime' value='$tmp_regime' />\n";
									}
									else{
										$tmp_regime=$tmp_new_regime;

										echo "<td style='text-align: center;'>";
										echo "$tmp_regime";
										//echo " <span style='color:red'>DEBUG: ".$affiche[8]."</span> ";
										echo "<input type='hidden' name='new_".$cpt."_regime' value='$tmp_regime' />\n";
									}
									//=========================

									echo "</td>\n";

									echo "<td style='text-align: center;'>";
									echo "$affiche[9]";
									echo "</td>\n";

									echo "</tr>\n";
								}

								$cpt++;
							}
						}
					}
					echo "</table>\n";
					echo "<p>On compte $cpt_modif champs modifiés et $cpt_new nouveaux élèves.</p>\n";
					fclose($fp);
				}

			}
			else{
				echo "<p style='color:red;'>Le nom du fichier proposé ne coïncide pas avec ce qui est attendu: ELEVES.CSV</p>\n";
				echo "<p><a href='".$_SERVER['PHP_SELF']."'>Retour au choix du fichier ELEVES.CSV</a></p>\n";
				echo "</form>\n";
				echo "</div>\n";
				echo "</body></html>\n";
				die();
			}

			echo "<script type='text/javascript'>
	function modifcase(mode){
		for(i=0;i<$cpt;i++){
			if(document.getElementById('check_'+i)){
				if(mode=='coche'){
					document.getElementById('check_'+i).checked=true;
				}
				else{
					document.getElementById('check_'+i).checked=false;
				}
			}
		}
	}
</script>\n";

			//echo "<input type='hidden' name='cpt' value='$cpt' />\n";
			echo "<p align='center'><input type=submit value='Valider' /></p>\n";
			echo "</form>\n";

			break;
		case 2:
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			$erreur=0;
			if(isset($modif)){
				echo "<p>Mise à jour des informations pour \n";
				for($i=0;$i<count($modif);$i++){
					//echo "<p>\n";

					$cpt=$modif[$i];

					$elenoet=$_POST['modif_'.$cpt.'_elenoet'];
					$ele_id=$_POST['modif_'.$cpt.'_eleid'];
					$login_eleve=$_POST['modif_'.$cpt.'_login'];
					$nom=traitement_magic_quotes(corriger_caracteres($_POST['modif_'.$cpt.'_nom']));
					$prenom=traitement_magic_quotes(corriger_caracteres($_POST['modif_'.$cpt.'_prenom']));
					$sexe=$_POST['modif_'.$cpt.'_sexe'];
					$naissance=$_POST['modif_'.$cpt.'_naissance'];
					$doublant=$_POST['modif_'.$cpt.'_doublant'];
					$regime=$_POST['modif_'.$cpt.'_regime'];
					$nonat=$_POST['modif_'.$cpt.'_nonat'];

					//echo "cpt=$cpt<br />\n";
					//echo "nom=$nom<br />\n";

					if($i>0){echo ", ";}
					echo stripslashes(stripslashes($nom))." ".stripslashes(stripslashes($prenom));

					// FAUT-IL UN stripslashes sur les noms pour les apostrophes?
					// Dans le champ de formulaire soumis, on a des échappements:
					// Ex.: L\'HERNAULT
					// Après soumission du formulaire, ce qui est reçu, c'est L\\\'HERNAULT
					// Est-ce un effet de magic_quotes_gpc?
					// Puis-je appliquer deux stripslashes() sans risque?
					//$sql="UPDATE eleves SET nom='".$nom."',
					//			prenom='".$prenom."',
					$sql="UPDATE eleves SET nom='".stripslashes($nom)."',
								prenom='".stripslashes($prenom)."',
								sexe='$sexe',
								naissance='$naissance',
								no_gep='$nonat'
								WHERE (ele_id='$ele_id')";
					//			WHERE elenoet='$elenoet'";
					$res1=mysqli_query($GLOBALS["mysqli"], $sql);
					//echo "<p>$sql</p>\n";
					if(!$res1){
						//echo " (<font color='red'>erreur</font>)";
						echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
						$erreur++;
					}

					$sql="UPDATE j_eleves_regime SET doublant='$doublant',
								regime='$regime'
								WHERE (login='$login_eleve')";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res2){
						//echo " (<font color='red'>erreur</font>)";
						echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
						$erreur++;
					}

					//echo "</p>\n";
				}
				echo "</p>\n";
			}



			if(isset($new)){
				echo "<p>Insertion de \n";
				for($i=0;$i<count($new);$i++){
					//echo "<p>\n";

					$cpt=$new[$i];

					$elenoet=$_POST['new_'.$cpt.'_elenoet'];
					$ele_id=$_POST['new_'.$cpt.'_eleid'];
					//$ele_login=$_POST['new_'.$cpt.'_login'];
					$nom=traitement_magic_quotes(corriger_caracteres($_POST['new_'.$cpt.'_nom']));
					$prenom=traitement_magic_quotes(corriger_caracteres($_POST['new_'.$cpt.'_prenom']));
					$sexe=$_POST['new_'.$cpt.'_sexe'];
					$naissance=$_POST['new_'.$cpt.'_naissance'];
					$doublant=$_POST['new_'.$cpt.'_doublant'];
					$regime=$_POST['new_'.$cpt.'_regime'];
					$nonat=$_POST['new_'.$cpt.'_nonat'];

					// Générer un login...
					$temp1 = mb_strtoupper($nom);
					$temp1 = mb_strtr($temp1, " '-", "___");
					$temp1 = mb_substr($temp1,0,7);
					$temp2 = mb_strtoupper($prenom);
					$temp2 = mb_strtr($temp2, " '-", "___");
					$temp2 = mb_substr($temp2,0,1);
					$login_eleve = $temp1.'_'.$temp2;

					// On teste l'unicité du login que l'on vient de créer
					$k = 2;
					$test_unicite = 'no';
					$temp = $login_eleve;
					while ($test_unicite != 'yes') {
						//$test_unicite = test_unique_e_login($login_eleve,$i);
						$test_unicite = test_unique_login($login_eleve);
						if ($test_unicite != 'yes') {
							$login_eleve = $temp.$k;
							$k++;
						}
					}

					if($i>0){echo ", ";}
					echo stripslashes(stripslashes($nom))." ".stripslashes(stripslashes($prenom));

					// FAUT-IL UN stripslashes sur les noms pour les apostrophes?

					//$sql="INSERT INTO eleves SET nom='".$nom."',
					//			prenom='".$prenom."',
					$sql="INSERT INTO eleves SET nom='".stripslashes($nom)."',
								prenom='".stripslashes($prenom)."',
								sexe='$sexe',
								naissance='$naissance',
								no_gep='$nonat',
								login='$login_eleve',
								elenoet='$elenoet',
								ele_id='$ele_id'";
					//,			login='$login_eleve'
					//echo "$sql<br />\n";
					$res1=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res1){
						//echo " (<font color='red'>erreur</font>)";
						echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
						$erreur++;
					}
					else{
						$sql="SELECT 1=1 FROM j_eleves_regime WHERE (login='$login_eleve')";
						$res2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res2)==0){
							$sql="INSERT INTO j_eleves_regime SET doublant='$doublant',
										regime='$regime',
										login='$login_eleve'";
							//echo "$sql<br />\n";
							$res3=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res3){
								//echo " (<font color='red'>erreur</font>)";
								echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
								$erreur++;
							}
						}
						else{
							$sql="UPDATE j_eleves_regime SET doublant='$doublant',
										regime='$regime',
										WHERE (login='$login_eleve')";
							//echo "$sql<br />\n";
							$res3=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res3){
								//echo " (<font color='red'>erreur</font>)";
								echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
								$erreur++;
							}
						}
					}
				}
				echo "</p>\n";
			}

			switch($erreur){
				case 0:
					echo "<p>Passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=3".add_token_in_url()."'>import/mise à jour des personnes (<i>responsables</i>) et adresses</a>.</p>\n";
					break;

				case 1:
					echo "<p><font color='red'>Une erreur s'est produite.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=3".add_token_in_url()."'>import/mise à jour des personnes (<i>responsables</i>) et adresses</a>.</p>\n";
					break;

				default:
					echo "<p><font color='red'>$erreur erreurs se sont produites.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=3".add_token_in_url()."'>import/mise à jour des personnes (<i>responsables</i>) et adresses</a>.</p>\n";
					break;
			}

			break;
		case 3:
			echo "<h2>Import/mise à jour des personnes (<i>responsables</i>) et adresses</h2>\n";

			//echo "<p>Vous allez importer les fichiers <b>CSV</b> (<i><a href='../init_xml/lecture_xml_sconet.php?ad_retour=".$_SERVER['PHP_SELF']."'>générés</a> à partir des exports XML de Sconet</i>).<br />\nLes fichiers requis ici sont les trois fichiers PERSONNES.CSV, RESPONSABLES.CSV et ADRESSES.CSV</p>\n";
			echo "<p>Vous allez importer les fichiers <b>CSV</b> (<i><a href='../init_xml/lecture_xml_sconet.php?ad_retour=".$_SERVER['PHP_SELF']."'>générés</a> à partir des exports XML de Sconet</i>).<br />\nLes fichiers requis ici sont les deux fichiers PERSONNES.CSV et ADRESSES.CSV</p>\n";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

			echo add_token_field();

			//echo "<input type=hidden name='is_posted' value='yes' />\n";
			echo "<input type=hidden name='step' value='4' />\n";
			//echo "<input type=hidden name='mode' value='1' />\n";
			echo "<p>Et les fichiers de responsables:</p>\n";
			echo "<p>Sélectionnez le fichier <b>PERSONNES.CSV</b>:<br /><input type='file' size='80' name='pers_file' />\n";
			//echo "<p>Sélectionnez le fichier <b>RESPONSABLES.CSV</b>:<br /><input type='file' size='80' name='resp_file' />\n";
			echo "<p>Sélectionnez le fichier <b>ADRESSES.CSV</b>:<br /><input type='file' size='80' name='adr_file' />\n";

			echo "<p><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";

			echo "<p>Il est recommandé d'importer les informations élèves et de ne passer qu'ensuite à l'import des associations responsables/élèves.<br />\n";
			echo "<a href='".$_SERVER['PHP_SELF']."?is_posted=y&amp;step=6".add_token_in_url()."'>Passer néanmoins à la page d'importation des associations responsables/élèves</a></p>";
			break;
		case 4:
			// Affichage des informations
			echo "<h2>Import/mise à jour des personnes (<i>responsables</i>) et adresses</h2>\n";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

			echo add_token_field();

			//echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "<input type='hidden' name='step' value='5' />\n";

			// Et la partie responsables:
			// C'est la copie de la page /init_xml/responsables.php
			$nb_reg_no1=-1;
			$nb_reg_no2=-1;
			$nb_reg_no3=-1;


			$csv_file = isset($_FILES["adr_file"]) ? $_FILES["adr_file"] : NULL;
			if(mb_strtoupper($csv_file['name']) == "ADRESSES.CSV") {
				$fp=fopen($csv_file['tmp_name'],"r");
				if(!$fp){
					echo "<p>Impossible d'ouvrir le fichier ADRESSES.CSV.</p>";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
				}
				else{
					//$cpt=0;
					$adresse=array();
					$adr_new=array();
					$adr_modif=array();

					// on constitue le tableau des champs à extraire
					$tabchamps=array("adr_id","adr1","adr2","adr3","adr4","cp","pays","commune");

					$nblignes=0;
					while (!feof($fp)) {
						$ligne = fgets($fp, 4096);
						if($nblignes==0){
							// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
							// On ne retient pas ces ajouts pour $en_tete
							$temp=explode(";",$ligne);
							for($i=0;$i<sizeof($temp);$i++){
								$temp2=explode(",",$temp[$i]);
								$en_tete[$i]=$temp2[0];
							}

							$nbchamps=sizeof($en_tete);
						}
						$nblignes++;
					}
					fclose ($fp);

					// On range dans tabindice les indices des champs retenus
					/*
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[] = $i;
							}
						}
					}
					*/
					$cpt_tmp=0;
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[$cpt_tmp]=$i;
								$cpt_tmp++;

							}
						}
					}

					//=========================
					$fp=fopen($csv_file['tmp_name'],"r");
					// On lit une ligne pour passer la ligne d'entête:
					$ligne = fgets($fp, 4096);
					//=========================
					$nb_reg_no2=0;
					$nb_record2=0;
					for($k = 1; ($k < $nblignes+1); $k++){
						//$ligne = dbase_get_record($fp,$k);
						if(!feof($fp)){
							//=========================
							// MODIF: boireaus 20071024
							//$ligne = fgets($fp, 4096);
							$ligne = my_ereg_replace('"','',fgets($fp, 4096));
							//=========================
							if(trim($ligne)!=""){

								//=========================
								// AJOUT: boireaus 20070607
								unset($affiche);
								//=========================

								$tabligne=explode(";",$ligne);
								for($i = 0; $i < count($tabchamps); $i++) {
									//$ind = $tabindice[$i];
									$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
								}

								// Stockage des données:
								$adresse[$affiche[0]]=array();
								for($i=1;$i<count($tabchamps);$i++) {
									$adresse[$affiche[0]]["$tabchamps[$i]"]=$affiche[$i];
								}



								$temoin_nouvelle_adresse="n";
								$sql="SELECT * FROM resp_adr WHERE (adr_id='$affiche[0]')";
								//echo "$sql<br />\n";
								$res1=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res1)==0){
									$sql="SELECT * FROM resp_pers WHERE (adr_id='$affiche[0]')";
									//echo "$sql<br />\n";
									$res2=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res2)==0){
										$adr_new[]=$affiche[0];
										$temoin_nouvelle_adresse="y";
										//echo "Ajout de l'adresse n°$affiche[0]<br />\n";
									}
								}

								if($temoin_nouvelle_adresse=="y"){
									$adr_new[]=$affiche[0];
										//echo "Ajout de l'adresse n°$affiche[0]<br />\n";
								}
								else{
									$lig=mysqli_fetch_object($res1);
									if((stripslashes($lig->adr1)!=stripslashes($affiche[1]))||
									(stripslashes($lig->adr2)!=stripslashes($affiche[2]))||
									(stripslashes($lig->adr3)!=stripslashes($affiche[3]))||
									(stripslashes($lig->adr4)!=stripslashes($affiche[4]))||
									(stripslashes($lig->cp)!=stripslashes($affiche[5]))||
									(stripslashes($lig->pays)!=stripslashes($affiche[6]))||
									(stripslashes($lig->commune)!=stripslashes($affiche[7]))){
										$adr_modif[]=$affiche[0];
										//echo "Modification de l'adresse n°$affiche[0]<br />\n";
									}
								}
							}
						}
					}
					//dbase_close($fp);
					fclose($fp);

					/*
					if ($nb_reg_no2 != 0) {
						echo "<p>Lors de l'enregistrement des données de ADRESSES.CSV, il y a eu $nb_reg_no2 erreurs. Essayez de trouvez la cause de l'erreur.</p>\n";
					} else {
						echo "<p>L'importation des adresses de responsables dans la base GEPI a été effectuée avec succès (".$nb_record2." enregistrements au total).</p>\n";
					}
					*/

				}
			} else if (trim($csv_file['name'])=='') {
				echo "<p>Aucun fichier ADRESSES.CSV n'a été sélectionné !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
				echo "</body>\n</html>\n";
				die();

			} else {
				echo "<p>Le(s) fichier(s) sélectionné(s) n'est(ne sont) pas valide(s) !<br />\n";
				echo "Contrôlez que le(s) nom(s) de fichier(s) coïncide(nt) avec ce qui est réclamé.<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
				echo "</body>\n</html>\n";
				die();
			}




			$csv_file = isset($_FILES["pers_file"]) ? $_FILES["pers_file"] : NULL;
			if(mb_strtoupper($csv_file['name']) == "PERSONNES.CSV") {
				$fp=fopen($csv_file['tmp_name'],"r");
				if(!$fp){
					echo "<p>Impossible d'ouvrir le fichier PERSONNES.CSV.</p>\n";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
				}
				else{

					$personne=array();
					$pers_new=array();
					$pers_modif=array();

					// on constitue le tableau des champs à extraire
					//$tabchamps=array("pers_id","nom","prenom","tel_pers","tel_port","tel_prof","mel","adr_id");
					$tabchamps=array("pers_id","nom","prenom","civilite","tel_pers","tel_port","tel_prof","mel","adr_id");
					//echo "\$tabchamps=array(\"pers_id\",\"nom\",\"prenom\",\"civilite\",\"tel_pers\",\"tel_port\",\"tel_prof\",\"mel\",\"adr_id\");<br />\n";

					$nblignes=0;
					while (!feof($fp)) {
						$ligne = fgets($fp, 4096);
						if($nblignes==0){
							// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
							// On ne retient pas ces ajouts pour $en_tete
							$temp=explode(";",$ligne);
							for($i=0;$i<sizeof($temp);$i++){
								$temp2=explode(",",$temp[$i]);
								$en_tete[$i]=$temp2[0];
								//echo "\$en_tete[$i]=$temp2[0]<br />\n";
							}

							$nbchamps=sizeof($en_tete);
						}
						$nblignes++;
					}
					fclose ($fp);

					// On range dans tabindice les indices des champs retenus
					$cpt_tmp=0;
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
							//if (trim($en_tete[$i]) == trim($tabchamps[$k])) {
								//$tabindice[] = $i;
								//echo "\$tabindice[] = $i<br />\n";

								$tabindice[$cpt_tmp]=$i;
								//echo "\$tabindice[$cpt_tmp]=$i<br />\n";
								$cpt_tmp++;

							}
						}
					}

					//=========================
					$fp=fopen($csv_file['tmp_name'],"r");
					// On lit une ligne pour passer la ligne d'entête:
					$ligne = fgets($fp, 4096);
					//=========================
					$nb_reg_no3=0;
					$nb_record3=0;
					for($k = 1; ($k < $nblignes+1); $k++){
						//$ligne = dbase_get_record($fp,$k);
						if(!feof($fp)){
							//=========================
							// MODIF: boireaus 20071024
							//$ligne = fgets($fp, 4096);
							$ligne = my_ereg_replace('"','',fgets($fp, 4096));
							//=========================
							if(trim($ligne)!=""){
								$tabligne=explode(";",$ligne);
								for($i = 0; $i < count($tabchamps); $i++) {
									//$ind = $tabindice[$i];
									$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
									//echo "\$affiche[$i]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim(\$tabligne[\$tabindice[$i]]))))=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim(\$tabligne[$tabindice[$i]]))))=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim(".$tabligne[$tabindice[$i]]."))));<br />\n";
								}

								// Stockage des données:
								$personne[$affiche[0]]=array();
								for($i=1;$i<count($tabchamps);$i++) {
									$personne[$affiche[0]]["$tabchamps[$i]"]=$affiche[$i];
									//echo "\$personne[$affiche[0]][\"$tabchamps[$i]\"]=\$affiche[$i]=".$affiche[$i]."<br />\n";
								}

								$sql="SELECT * FROM resp_pers WHERE (pers_id='$affiche[0]')";
								$res1=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res1)==0){
									$pers_new[]=$affiche[0];
									//echo "Ajout de pers_id=$affiche[0] pour $affiche[1] $affiche[2]<br />\n";
								}
								else{

									$lig=mysqli_fetch_object($res1);
									if((stripslashes($lig->nom)!=stripslashes($affiche[1]))||
									(stripslashes($lig->prenom)!=stripslashes($affiche[2]))||
									(mb_strtolower(stripslashes($lig->civilite))!=(mb_strtolower(stripslashes($affiche[3]))))||
									(stripslashes($lig->tel_pers)!=stripslashes($affiche[4]))||
									(stripslashes($lig->tel_port)!=stripslashes($affiche[5]))||
									(stripslashes($lig->tel_prof)!=stripslashes($affiche[6]))||
									(stripslashes($lig->mel)!=stripslashes($affiche[7]))||
									(stripslashes($lig->adr_id)!=stripslashes($affiche[8]))) {
										$pers_modif[]=$affiche[0];
										//echo "Modification de pers_id=$affiche[0] pour $affiche[1] $affiche[2]<br />\n";
									}
								}
							}
						}
					}
					//dbase_close($fp);
					fclose($fp);

					//echo "<p>===================================================</p>\n";

					/*
					if ($nb_reg_no3 != 0) {
						echo "<p>Lors de l'enregistrement des données de PERSONNES.CSV, il y a eu $nb_reg_no3 erreurs. Essayez de trouvez la cause de l'erreur.</p>\n";
					} else {
						echo "<p>L'importation des personnes (responsables) dans la base GEPI a été effectuée avec succès (".$nb_record3." enregistrements au total).</p>\n";
					}
					*/
				}
			} else if (trim($csv_file['name'])=='') {
				echo "<p>Aucun fichier PERSONNES.CSV n'a été sélectionné !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
				echo "</body>\n</html>\n";
				die();

			} else {
				echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
				echo "</body>\n</html>\n";
				die();
			}








			// ***************************************

			/*
			// DEBUG:
			echo "count(\$adr_modif)=".count($adr_modif)."<br />";
			echo "count(\$adr_new)=".count($adr_new)."<br />";
			echo "count(\$pers_modif)=".count($pers_modif)."<br />";
			echo "count(\$pers_new)=".count($pers_new)."<br />";
			*/

			// Recherche des personnes sans modif dans personnes.csv,
			// mais avec modif de l'adresse pour l'ajouter au tableau $pers_modif
			for($i=0;$i<count($adr_modif);$i++){
				$temoin="";
				for($j=0;$j<count($pers_modif);$j++){
					if($personne[$pers_modif[$j]]["adr_id"]==$adr_modif[$i]){
						$temoin=1;
					}
				}
				if($temoin==""){
					$sql="SELECT * FROM resp_pers WHERE (adr_id='".$adr_modif[$i]."')";
					$res1=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res1)==0){
						// L'adresse n'est plus utilisée? Mais encore présente dans sconet? et aurait changé?
					}
					else{
						while($lig1=mysqli_fetch_object($res1)){
							$pers_id=$lig1->pers_id;
							$personne[$pers_id]=array();

							$pers_modif[]=$pers_id;
							$personne[$pers_id]["nom"]=$lig1->nom;
							$personne[$pers_id]["prenom"]=$lig1->prenom;
							$personne[$pers_id]["civilite"]=ucfirst(mb_strtolower($lig1->civilite));
							$personne[$pers_id]["tel_pers"]=$lig1->tel_pers;
							$personne[$pers_id]["tel_port"]=$lig1->tel_port;
							$personne[$pers_id]["tel_prof"]=$lig1->tel_prof;
							$personne[$pers_id]["mel"]=$lig1->mel;
							$personne[$pers_id]["adr_id"]=$adr_modif[$i];


						}
					}
				}
			}
			// ***************************************














			// On passe à l'affichage du tableau
			echo "<table border='1'>\n";
			echo "<tr>\n";

			//echo "<td style='text-align:center; font-weight:bold;' rowspan='2'>Enregistrer</td>\n";
			echo "<td style='text-align: center; font-weight: bold;' rowspan='2'>Enregistrer<br />\n";
			echo "<a href=\"javascript:modifcase('coche')\">";
			echo "<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
			echo " / ";
			echo "<a href=\"javascript:modifcase('decoche')\">";
			echo "<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</td>\n";

			echo "<td rowspan='2'>&nbsp;</td>\n";

			echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);' colspan='5'>Responsable</td>\n";
			echo "<td style='text-align:center; font-weight:bold; background-color:#FAFABE;' rowspan='2'>Adresse</td>\n";
			//echo "<td style='text-align:center; font-weight:bold;' colspan='3'>Elève</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>pers_id</td>\n";
			echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Nom</td>\n";
			echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Prénom</td>\n";
			echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Civilité</td>\n";
			echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Contact</td>\n";
			//echo "<td style='text-align:center; font-weight:bold;'>ele_id</td>\n";
			//echo "<td style='text-align:center; font-weight:bold;'>Nom</td>\n";
			//echo "<td style='text-align:center; font-weight:bold;'>Prénom</td>\n";
			echo "</tr>\n";
			$alt=1;
			$cpt=0;
			for($i=0;$i<count($pers_modif);$i++){
				$pers_id=$pers_modif[$i];

				//echo "<tr>\n";
				$alt=$alt*(-1);
				echo "<tr style='background-color:";
				if($alt==1){
					echo "silver";
				}
				else{
					echo "white";
				}
				echo ";'>\n";

				//echo "<td style='text-align:center;'>";
				//echo "</td>\n";
				echo "<td style='text-align: center;'><input type='checkbox' id='check_".$cpt."' name='modif[]' value='$cpt' /></td>\n";

				echo "<td style='text-align: center; background-color: lightgreen;'>Modif</td>\n";


				echo "<td style='text-align:center;'>$pers_id";
				echo "<input type='hidden' name='modif_".$cpt."_pers_id' value='$pers_id' />\n";
				//echo "<input type='text' name='modif_".$cpt."_pers_id' value='$pers_id' />\n";
				echo "</td>\n";

				$sql="SELECT * FROM resp_pers WHERE (pers_id='$pers_id')";
				$res1=mysqli_query($GLOBALS["mysqli"], $sql);
				$lig1=mysqli_fetch_object($res1);

				echo "<td style='text-align:center;";
				if(stripslashes($lig1->nom)!=stripslashes($personne[$pers_id]["nom"])){
					echo " background-color:lightgreen;'>";
					if($lig1->nom!=''){
						echo stripslashes($lig1->nom)." <font color='red'>-&gt;</font>\n";
					}
				}
				else{
					echo "'>";
				}
				echo stripslashes($personne[$pers_id]["nom"]);
				echo "<input type='hidden' name='modif_".$cpt."_nom' value=\"".stripslashes($personne[$pers_id]["nom"])."\" />\n";
				echo "</td>\n";

				echo "<td style='text-align:center;";
				if(stripslashes($lig1->prenom)!=stripslashes($personne[$pers_id]["prenom"])){
					echo " background-color:lightgreen;'>";
					if($lig1->prenom!=''){
						echo stripslashes($lig1->prenom)." <font color='red'>-&gt;</font>\n";
					}
				}
				else{
					echo "'>";
				}
				echo stripslashes($personne[$pers_id]["prenom"]);
				echo "<input type='hidden' name='modif_".$cpt."_prenom' value=\"".stripslashes($personne[$pers_id]["prenom"])."\" />\n";
				echo "</td>\n";


				//======================================
				echo "<td style='text-align:center;";
				if(ucfirst(mb_strtolower(stripslashes($lig1->civilite)))!=ucfirst(mb_strtolower(stripslashes($personne[$pers_id]["civilite"])))) {
					echo " background-color:lightgreen;'>";
					if($lig1->civilite!=''){
						echo stripslashes($lig1->civilite)." <font color='red'>-&gt;</font>\n";
					}
				}
				else{
					echo "'>";
				}
				//echo stripslashes($personne[$pers_id]["civilite"]);
				echo ucfirst(mb_strtolower(stripslashes($personne[$pers_id]["civilite"])));
				echo "<input type='hidden' name='modif_".$cpt."_civilite' value=\"".ucfirst(mb_strtolower(stripslashes($personne[$pers_id]["civilite"])))."\" />\n";
				echo "</td>\n";
				//======================================


				echo "<td style='text-align:center;'>\n";
					echo "<table border='1' width='100%'>\n";
					echo "<tr>\n";
					echo "<td style='text-align:center; font-weight:bold;'>Tel</td>\n";
					echo "<td style='text-align:center;";
					if($lig1->tel_pers!=$personne[$pers_id]["tel_pers"]) {
						if(($lig1->tel_pers!='')||($personne[$pers_id]["tel_pers"]!='')){
							echo " background-color:lightgreen;'>";
							if($lig1->tel_pers!=''){
								echo $lig1->tel_pers." <font color='red'>-&gt;</font>\n";
							}
						}
						else{
							echo "'>";
						}
					}
					else{
						echo "'>";
					}
					echo $personne[$pers_id]["tel_pers"];
					echo "<input type='hidden' name='modif_".$cpt."_tel_pers' value='".$personne[$pers_id]["tel_pers"]."' />\n";
					echo "</td>\n";
					echo "</tr>\n";

					echo "<tr>\n";
					echo "<td style='text-align:center; font-weight:bold;'>TPo</td>\n";
					echo "<td style='text-align:center;";
					if($lig1->tel_port!=$personne[$pers_id]["tel_port"]) {
						if(($lig1->tel_port!='')||($personne[$pers_id]["tel_port"]!='')){
							echo " background-color:lightgreen;'>";
							if($lig1->tel_port!=''){
								echo $lig1->tel_port." <font color='red'>-&gt;</font>\n";
							}
						}
						else{
							echo "'>";
						}
					}
					else{
						echo "'>";
					}
					echo $personne[$pers_id]["tel_port"];
					echo "<input type='hidden' name='modif_".$cpt."_tel_port' value='".$personne[$pers_id]["tel_port"]."' />\n";
					echo "</td>\n";
					echo "</tr>\n";

					echo "<tr>\n";
					echo "<td style='text-align:center; font-weight:bold;'>TPr</td>\n";
					echo "<td style='text-align:center;";
					if($lig1->tel_prof!=$personne[$pers_id]["tel_prof"]) {
						if(($lig1->tel_prof!='')||($personne[$pers_id]["tel_prof"]!='')){
							echo " background-color:lightgreen;'>";
							if($lig1->tel_prof!=''){
								echo $lig1->tel_prof." <font color='red'>-&gt;</font>\n";
							}
						}
						else{
							echo "'>";
						}
					}
					else{
						echo "'>";
					}
					echo $personne[$pers_id]["tel_prof"];
					echo "<input type='hidden' name='modif_".$cpt."_tel_prof' value='".$personne[$pers_id]["tel_prof"]."' />\n";
					echo "</td>\n";
					echo "</tr>\n";

					echo "<tr>\n";
					echo "<td style='text-align:center; font-weight:bold;'>mel</td>\n";
					echo "<td style='text-align:center;";
					if($lig1->mel!=$personne[$pers_id]["mel"]) {
						if(($lig1->mel!='')||($personne[$pers_id]["mel"]!='')){
							echo " background-color:lightgreen;'>";
							if($lig1->mel!=''){
								echo $lig1->mel." <font color='red'>-&gt;</font>\n";
							}
						}
						else{
							echo "'>";
						}
					}
					else{
						echo "'>";
					}
					echo $personne[$pers_id]["mel"];
					echo "<input type='hidden' name='modif_".$cpt."_mel' value='".$personne[$pers_id]["mel"]."' />\n";
					echo "</td>\n";
					echo "</tr>\n";
					echo "</table>\n";
				echo "</td>\n";

				// Adresse
				echo "<td style='text-align:center;";
				$sql="SELECT * FROM resp_adr WHERE (adr_id='".$personne[$pers_id]["adr_id"]."')";
				$adr_id=$personne[$pers_id]["adr_id"];
				$res2=mysqli_query($GLOBALS["mysqli"], $sql);
				$lig2=mysqli_fetch_object($res2);
				if((in_array($personne[$pers_id]["adr_id"],$adr_modif))||(in_array($personne[$pers_id]["adr_id"],$adr_new))){
					echo " background-color:lightgreen;'>";
					if(($lig2->adr1!="")||($lig2->adr2!="")||($lig2->adr3!="")||($lig2->adr4!="")||($lig2->cp!="")||($lig2->commune!="")||($lig2->pays!="")){
						$chaine_adr="";
						if($lig2->adr1!=""){
							//echo "$lig2->adr1, ";
							$chaine_adr.=stripslashes("$lig2->adr1, ");
						}
						if($lig2->adr2!=""){
							//echo "$lig2->adr2, ";
							$chaine_adr.=stripslashes("$lig2->adr2, ");
						}
						if($lig2->adr3!=""){
							//echo "$lig2->adr3, ";
							$chaine_adr.=stripslashes("$lig2->adr3, ");
						}
						if($lig2->adr4!=""){
							//echo "$lig2->adr4, ";
							$chaine_adr.=stripslashes("$lig2->adr4, ");
						}
						if($lig2->cp!=""){
							//echo "$lig2->cp, ";
							$chaine_adr.=stripslashes("$lig2->cp, ");
						}
						if($lig2->commune!=""){
							//echo "$lig2->commune, ";
							$chaine_adr.=stripslashes("$lig2->commune, ");
						}
						if($lig2->pays!=""){
							//echo "$lig2->pays";
							$chaine_adr.=stripslashes("$lig2->pays");
						}
						echo $chaine_adr;
						echo " <font color='red'>-&gt;</font><br />\n";
					}
					$tabadr=array("adr_id","adr1","adr2","adr3","adr4","cp","pays","commune");
					$temoin_non_vide="";
					for($k=1;$k<count($tabadr);$k++){
						if($adresse[$adr_id]["$tabadr[$k]"]!=''){
							$temoin_non_vide="oui";
						}
						echo "<input type='hidden' name='modif_".$cpt."_".$tabadr[$k]."' value=\"".stripslashes($adresse[$adr_id]["$tabadr[$k]"])."\" />\n";
						//echo "<input type='text' name='modif_".$cpt."_".$tabadr[$k]."' value=\"".stripslashes($adresse[$adr_id]["$tabadr[$k]"])."\" />\n";
					}
					if($temoin_non_vide=="oui"){
						if($adresse[$adr_id]["$tabadr[1]"]!=""){echo stripslashes($adresse[$adr_id]["$tabadr[1]"]);}
						for($k=2;$k<count($tabadr);$k++){
							if($adresse[$adr_id]["$tabadr[$k]"]!=''){
								echo ", ".stripslashes($adresse[$adr_id]["$tabadr[$k]"]);
							}
						}
					}
				}
				else{
					echo "'>";
					if(($lig2->adr1!="")||($lig2->adr2!="")||($lig2->adr3!="")||($lig2->adr4!="")||($lig2->cp!="")||($lig2->commune!="")||($lig2->pays!="")){
						$chaine_adr="";
						if($lig2->adr1!=""){
							//echo "$lig2->adr1, ";
							$chaine_adr.=stripslashes("$lig2->adr1, ");
						}
						if($lig2->adr2!=""){
							//echo "$lig2->adr2, ";
							$chaine_adr.=stripslashes("$lig2->adr2, ");
						}
						if($lig2->adr3!=""){
							//echo "$lig2->adr3, ";
							$chaine_adr.=stripslashes("$lig2->adr3, ");
						}
						if($lig2->adr4!=""){
							//echo "$lig2->adr4, ";
							$chaine_adr.=stripslashes("$lig2->adr4, ");
						}
						if($lig2->cp!=""){
							//echo "$lig2->cp, ";
							$chaine_adr.=stripslashes("$lig2->cp, ");
						}
						if($lig2->commune!=""){
							//echo "$lig2->commune, ";
							$chaine_adr.=stripslashes("$lig2->commune, ");
						}
						if($lig2->pays!=""){
							//echo "$lig2->pays";
							$chaine_adr.=stripslashes("$lig2->pays");
						}
						echo $chaine_adr;
					}
					//echo "<b>TEMOIN 0:</b>";
				}
				//echo "$sql";
				//echo "<b>TEMOIN:</b>";
				echo "<input type='hidden' name='modif_".$cpt."_adr_id' value='".$personne[$pers_id]["adr_id"]."' />\n";
				//echo "<input type='text' name='modif_".$cpt."_adr_id' value='".$personne[$pers_id]["adr_id"]."' />\n";
				echo "</td>\n";

				/*
				echo "<td style='text-align:center;'>\n";
				echo "</td>\n";

				echo "<td style='text-align:center;'>\n";
				echo "</td>\n";

				echo "<td style='text-align:center;'>\n";
				echo "</td>\n";
				*/

				echo "</tr>\n";
				$cpt++;
			}





			for($i=0;$i<count($pers_new);$i++){
				$pers_id=$pers_new[$i];

				//echo "<tr>\n";
				$alt=$alt*(-1);
				echo "<tr style='background-color:";
				if($alt==1){
					echo "silver";
				}
				else{
					echo "white";
				}
				echo ";'>\n";

				//echo "<td style='text-align:center;'>";
				//echo "</td>\n";
				echo "<td style='text-align: center;'><input type='checkbox' id='check_".$cpt."' name='new[]' value='$cpt' /></td>\n";

				echo "<td style='text-align: center; background-color: rgb(150, 200, 240);'>Nouveau</td>\n";

				echo "<td style='text-align:center;'>$pers_id";
				echo "<input type='hidden' name='new_".$cpt."_pers_id' value='$pers_id' />\n";
				echo "</td>\n";

				echo "<td style='text-align:center;'>";
				echo stripslashes($personne[$pers_id]["nom"]);
				echo "<input type='hidden' name='new_".$cpt."_nom' value=\"".stripslashes($personne[$pers_id]["nom"])."\" />\n";
				echo "</td>\n";

				echo "<td style='text-align:center;'>";
				echo stripslashes($personne[$pers_id]["prenom"]);
				echo "<input type='hidden' name='new_".$cpt."_prenom' value=\"".stripslashes($personne[$pers_id]["prenom"])."\" />\n";
				echo "</td>\n";

				//======================================
				echo "<td style='text-align:center;'>\n";
				echo ucfirst(mb_strtolower(stripslashes($personne[$pers_id]["civilite"])));
				echo "<input type='hidden' name='new_".$cpt."_civilite' value=\"".ucfirst(mb_strtolower(stripslashes($personne[$pers_id]["civilite"])))."\" />\n";
				echo "</td>\n";
				//======================================

				echo "<td style='text-align:center;'>\n";
					echo "<table border='1' width='100%'>\n";
					echo "<tr>\n";
					echo "<td style='text-align:center; font-weight:bold;'>Tel</td>\n";
					echo "<td style='text-align:center;'>";
					echo $personne[$pers_id]["tel_pers"];
					echo "<input type='hidden' name='new_".$cpt."_tel_pers' value=\"".$personne[$pers_id]["tel_pers"]."\" />\n";
					echo "</td>\n";
					echo "</tr>\n";

					echo "<tr>\n";
					echo "<td style='text-align:center; font-weight:bold;'>TPo</td>\n";
					echo "<td style='text-align:center;'>\n";
					echo $personne[$pers_id]["tel_port"];
					echo "<input type='hidden' name='new_".$cpt."_tel_port' value=\"".$personne[$pers_id]["tel_port"]."\" />\n";
					echo "</td>\n";
					echo "</tr>\n";

					echo "<tr>\n";
					echo "<td style='text-align:center; font-weight:bold;'>TPr</td>\n";
					echo "<td style='text-align:center;'>\n";
					echo $personne[$pers_id]["tel_prof"];
					echo "<input type='hidden' name='new_".$cpt."_tel_prof' value=\"".$personne[$pers_id]["tel_prof"]."\" />\n";
					echo "</td>\n";
					echo "</tr>\n";

					echo "<tr>\n";
					echo "<td style='text-align:center; font-weight:bold;'>mel</td>\n";
					echo "<td style='text-align:center;'>\n";
					echo $personne[$pers_id]["mel"];
					echo "<input type='hidden' name='new_".$cpt."_mel' value=\"".$personne[$pers_id]["mel"]."\" />\n";
					echo "</td>\n";
					echo "</tr>\n";
					echo "</table>\n";
				echo "</td>\n";

				// Adresse
				echo "<td style='text-align:center;";
				$sql="SELECT * FROM resp_adr WHERE (adr_id='".$personne[$pers_id]["adr_id"]."')";
				$adr_id=$personne[$pers_id]["adr_id"];
				$res2=mysqli_query($GLOBALS["mysqli"], $sql);

				unset($lig2);
				if(mysqli_num_rows($res2)>0){
					$lig2=mysqli_fetch_object($res2);
				}

				if((in_array($personne[$pers_id]["adr_id"],$adr_modif))||(in_array($personne[$pers_id]["adr_id"],$adr_new))){
					if(isset($lig2)){
						echo " background-color:lightgreen;'>";
						if(($lig2->adr1!="")||($lig2->adr2!="")||($lig2->adr3!="")||($lig2->adr4!="")||($lig2->cp!="")||($lig2->commune!="")||($lig2->pays!="")){
							// Normalement, il ne devrait pas y avoir d'antislashes dans la BDD.
							$chaine_adr="";
							if($lig2->adr1!=""){
								//echo "$lig2->adr1, ";
								$chaine_adr.=stripslashes("$lig2->adr1, ");
							}
							if($lig2->adr2!=""){
								//echo "$lig2->adr2, ";
								$chaine_adr.=stripslashes("$lig2->adr2, ");
							}
							if($lig2->adr3!=""){
								//echo "$lig2->adr3, ";
								$chaine_adr.=stripslashes("$lig2->adr3, ");
							}
							if($lig2->adr4!=""){
								//echo "$lig2->adr4, ";
								$chaine_adr.=stripslashes("$lig2->adr4, ");
							}
							if($lig2->cp!=""){
								//echo "$lig2->cp, ";
								$chaine_adr.=stripslashes("$lig2->cp, ");
							}
							if($lig2->commune!=""){
								//echo "$lig2->commune, ";
								$chaine_adr.=stripslashes("$lig2->commune, ");
							}
							if($lig2->pays!=""){
								//echo "$lig2->pays";
								$chaine_adr.=stripslashes("$lig2->pays");
							}
							echo $chaine_adr;
							echo " <font color='red'>-&gt;</font><br />\n";
						}
					}
					else{
						echo "'>";
					}
					$tabadr=array("adr_id","adr1","adr2","adr3","adr4","cp","pays","commune");
					$temoin_non_vide="";
					for($k=1;$k<count($tabadr);$k++){
						if($adresse[$adr_id]["$tabadr[$k]"]!=''){
							$temoin_non_vide="oui";
						}
						echo "<input type='hidden' name='new_".$cpt."_".$tabadr[$k]."' value=\"".stripslashes($adresse[$adr_id]["$tabadr[$k]"])."\" />\n";
						//echo "<input type='text' name='new_".$cpt."_".$tabadr[$k]."' value=\"".stripslashes($adresse[$adr_id]["$tabadr[$k]"])."\" />\n";
					}
					if($temoin_non_vide=="oui"){
						if($adresse[$adr_id]["$tabadr[1]"]!=""){echo stripslashes($adresse[$adr_id]["$tabadr[1]"]);}
						for($k=2;$k<count($tabadr);$k++){
							if($adresse[$adr_id]["$tabadr[$k]"]!=''){
								echo ", ".stripslashes($adresse[$adr_id]["$tabadr[$k]"]);
							}
						}
					}
				}
				else{
					echo "'>";
					if(isset($lig2)){
						if(($lig2->adr1!="")||($lig2->adr2!="")||($lig2->adr3!="")||($lig2->adr4!="")||($lig2->cp!="")||($lig2->commune!="")||($lig2->pays!="")){
							// Normalement, il ne devrait pas y avoir d'antislashes dans la BDD.
							$chaine_adr="";
							if($lig2->adr1!=""){
								//echo "$lig2->adr1, ";
								$chaine_adr.=stripslashes("$lig2->adr1, ");
							}
							if($lig2->adr2!=""){
								//echo "$lig2->adr2, ";
								$chaine_adr.=stripslashes("$lig2->adr2, ");
							}
							if($lig2->adr3!=""){
								//echo "$lig2->adr3, ";
								$chaine_adr.=stripslashes("$lig2->adr3, ");
							}
							if($lig2->adr4!=""){
								//echo "$lig2->adr4, ";
								$chaine_adr.=stripslashes("$lig2->adr4, ");
							}
							if($lig2->cp!=""){
								//echo "$lig2->cp, ";
								$chaine_adr.=stripslashes("$lig2->cp, ");
							}
							if($lig2->commune!=""){
								//echo "$lig2->commune, ";
								$chaine_adr.=stripslashes("$lig2->commune, ");
							}
							if($lig2->pays!=""){
								//echo "$lig2->pays";
								$chaine_adr.=stripslashes("$lig2->pays");
							}
							echo $chaine_adr;
						}
					}
				}
				echo "<input type='hidden' name='new_".$cpt."_adr_id' value='".$personne[$pers_id]["adr_id"]."' />\n";
				//echo "<input type='text' name='new_".$cpt."_adr_id' value='".$personne[$pers_id]["adr_id"]."' />\n";
				echo "</td>\n";

				/*
				echo "<td style='text-align:center;'>";
				echo "</td>\n";

				echo "<td style='text-align:center;'>";
				echo "</td>\n";

				echo "<td style='text-align:center;'>";
				echo "</td>\n";
				*/

				echo "</tr>\n";
				$cpt++;
			}

			echo "</table>\n";




			echo "<script type='text/javascript'>
	function modifcase(mode){
		for(i=0;i<$cpt;i++){
			if(document.getElementById('check_'+i)){
				if(mode=='coche'){
					document.getElementById('check_'+i).checked=true;
				}
				else{
					document.getElementById('check_'+i).checked=false;
				}
			}
		}
	}
</script>\n";


			echo "<p align='center'><input type=submit value='Valider' /></p>\n";
			echo "</form>\n";

			break;
		case 5:
			// Import des informations personnes et adresses

			echo "<h2>Import/mise à jour des personnes (<i>responsables</i>) et adresses</h2>\n";

			$erreur=0;
			if(isset($modif)){
				echo "<p>Mise à jour des informations pour ";
				for($i=0;$i<count($modif);$i++){
					$cpt=$modif[$i];

					$pers_id=$_POST['modif_'.$cpt.'_pers_id'];
					$nom=traitement_magic_quotes(corriger_caracteres($_POST['modif_'.$cpt.'_nom']));
					$prenom=traitement_magic_quotes(corriger_caracteres($_POST['modif_'.$cpt.'_prenom']));
					$civilite=$_POST['modif_'.$cpt.'_civilite'];
					$tel_pers=$_POST['modif_'.$cpt.'_tel_pers'];
					$tel_prof=$_POST['modif_'.$cpt.'_tel_prof'];
					$tel_port=$_POST['modif_'.$cpt.'_tel_port'];
					$mel=$_POST['modif_'.$cpt.'_mel'];
					$adr_id=$_POST['modif_'.$cpt.'_adr_id'];

					if($i>0){echo ", ";}
					//echo "$pers_id - $nom - $prenom<br />\n";
					echo "$prenom $nom ($pers_id)\n";

					$sql="UPDATE resp_pers SET nom='$nom',
									prenom='$prenom',
									civilite='$civilite',
									tel_pers='$tel_pers',
									tel_port='$tel_port',
									tel_prof='$tel_prof',
									mel='$mel',
									adr_id='$adr_id'
								WHERE (pers_id='$pers_id')";
					//echo "$sql<br />\n";
					$res1=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res1){
						//echo " (<font color='red'>erreur</font>)";
						echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
						$erreur++;
					}


					// Partie adresse (contrôler si c'est une modif ou un ajout)
					$modif_adresse=isset($_POST['modif_'.$cpt.'_adr1']) ? 1 : NULL;
					if(isset($modif_adresse)){
						$adr1=$_POST['modif_'.$cpt.'_adr1'];
						$adr2=$_POST['modif_'.$cpt.'_adr2'];
						$adr3=$_POST['modif_'.$cpt.'_adr3'];
						$adr4=$_POST['modif_'.$cpt.'_adr4'];
						$cp=$_POST['modif_'.$cpt.'_cp'];
						$commune=$_POST['modif_'.$cpt.'_commune'];
						$pays=$_POST['modif_'.$cpt.'_pays'];

						$sql="SELECT 1=1 FROM resp_adr WHERE (adr_id='$adr_id')";
						//echo "$sql<br />\n";
						$res2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res2)==0){
							$sql="INSERT INTO resp_adr SET adr_id='$adr_id',
											adr1='$adr1',
											adr2='$adr2',
											adr3='$adr3',
											adr4='$adr4',
											cp='$cp',
											commune='$commune',
											pays='$pays'";
							//echo "$sql<br />\n";
							$res3=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res3){
								//echo " (<font color='red'>erreur</font>)";
								echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
								$erreur++;
							}
						}
						else{
							$sql="UPDATE resp_adr SET adr1='$adr1',
											adr2='$adr2',
											adr3='$adr3',
											adr4='$adr4',
											cp='$cp',
											commune='$commune',
											pays='$pays'
										WHERE (adr_id='$adr_id')";
							//echo "$sql<br />\n";
							$res3=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res3){
								//echo " (<font color='red'>erreur</font>)";
								echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
								$erreur++;
							}
						}
					}

					//echo "$pers_id - $nom - $prenom<br />\n";
				}
				echo "</p>\n";
			}

			if(isset($new)){
				echo "<p>Insertion des informations pour ";
				for($i=0;$i<count($new);$i++){
					$cpt=$new[$i];

					$pers_id=$_POST['new_'.$cpt.'_pers_id'];
					$nom=traitement_magic_quotes(corriger_caracteres($_POST['new_'.$cpt.'_nom']));
					$prenom=traitement_magic_quotes(corriger_caracteres($_POST['new_'.$cpt.'_prenom']));
					$civilite=$_POST['new_'.$cpt.'_civilite'];
					$tel_pers=$_POST['new_'.$cpt.'_tel_pers'];
					$tel_prof=$_POST['new_'.$cpt.'_tel_prof'];
					$tel_port=$_POST['new_'.$cpt.'_tel_port'];
					$mel=$_POST['new_'.$cpt.'_mel'];
					$adr_id=$_POST['new_'.$cpt.'_adr_id'];

					if($i>0){echo ", ";}
					echo "$prenom $nom ($pers_id)\n";

					$sql="INSERT INTO resp_pers SET pers_id='$pers_id',
									nom='$nom',
									prenom='$prenom',
									civilite='$civilite',
									tel_pers='$tel_pers',
									tel_port='$tel_port',
									tel_prof='$tel_prof',
									mel='$mel',
									adr_id='$adr_id'";
					//echo "$sql<br />\n";
					$res1=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res1){
						//echo " (<font color='red'>erreur</font>)";
						echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
						$erreur++;
					}


					// Partie adresse (contrôler si c'est une new ou un ajout)
					$new_adresse=isset($_POST['new_'.$cpt.'_adr1']) ? 1 : NULL;
					if(isset($new_adresse)){
						$adr1=$_POST['new_'.$cpt.'_adr1'];
						$adr2=$_POST['new_'.$cpt.'_adr2'];
						$adr3=$_POST['new_'.$cpt.'_adr3'];
						$adr4=$_POST['new_'.$cpt.'_adr4'];
						$cp=$_POST['new_'.$cpt.'_cp'];
						$commune=$_POST['new_'.$cpt.'_commune'];
						$pays=$_POST['new_'.$cpt.'_pays'];

						$sql="SELECT 1=1 FROM resp_adr WHERE (adr_id='$adr_id')";
						//echo "$sql<br />\n";
						$res2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res2)==0){
							$sql="INSERT INTO resp_adr SET adr_id='$adr_id',
											adr1='$adr1',
											adr2='$adr2',
											adr3='$adr3',
											adr4='$adr4',
											cp='$cp',
											commune='$commune',
											pays='$pays'";
							//echo "$sql<br />\n";
							$res3=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res3){
								//echo " (<font color='red'>erreur</font>)";
								echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
								$erreur++;
							}
						}
						else{
							$sql="UPDATE resp_adr SET adr1='$adr1',
											adr2='$adr2',
											adr3='$adr3',
											adr4='$adr4',
											cp='$cp',
											commune='$commune',
											pays='$pays'
										WHERE (adr_id='$adr_id')";
							//echo "$sql<br />\n";
							$res3=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res3){
								//echo " (<font color='red'>erreur</font>)";
								echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
								$erreur++;
							}
						}
					}

					//echo "$pers_id - $nom - $prenom<br />\n";
				}
				echo "</p>\n";
			}


			//echo "<p>Passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=6'>import/mise à jour des responsables</a>.</p>\n";
			switch($erreur){
				case 0:
					echo "<p>Passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=6".add_token_in_url()."'>import/mise à jour des responsables</a>.</p>\n";
					break;

				case 1:
					echo "<p><font color='red'>Une erreur s'est produite.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=6".add_token_in_url()."'>import/mise à jour des responsables</a>.</p>\n";
					break;

				default:
					echo "<p><font color='red'>$erreur erreurs se sont produites.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=6".add_token_in_url()."'>import/mise à jour des responsables</a>.</p>\n";
					break;
			}

			break;
		case 6:

			echo "<h2>Import/mise à jour des responsables</h2>\n";

			// Formulaire pour fournir le fichier RESPONSABLES.CSV
			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

			echo add_token_field();

			//echo "<input type=hidden name='is_posted' value='yes' />\n";
			echo "<input type=hidden name='step' value='7' />\n";
			//echo "<input type=hidden name='mode' value='1' />\n";
			echo "<p>Sélectionnez le fichier <b>RESPONSABLES.CSV</b>:<br /><input type='file' size='80' name='resp_file' />\n";
			echo "<p><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";

			break;

		case 7:
			// Affichage des informations responsables

			echo "<h2>Import/mise à jour des associations responsables/élèves</h2>\n";

			$cpt=0;
			$csv_file = isset($_FILES["resp_file"]) ? $_FILES["resp_file"] : NULL;
			if(mb_strtoupper($csv_file['name']) == "RESPONSABLES.CSV") {
				$fp=fopen($csv_file['tmp_name'],"r");
				if(!$fp){
					echo "<p>Impossible d'ouvrir le fichier RESPONSABLES.CSV.</p>";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
				}
				else{

					echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

					echo add_token_field();

					//echo "<input type=hidden name='is_posted' value='yes' />\n";
					echo "<input type=hidden name='step' value='8' />\n";

					$responsable=array();
					$resp_new=array();
					$resp_modif=array();

					// on constitue le tableau des champs à extraire
					$tabchamps=array("ele_id","pers_id","resp_legal","pers_contact");

					$nblignes=0;
					while (!feof($fp)) {
						$ligne = fgets($fp, 4096);
						if($nblignes==0){
							// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
							// On ne retient pas ces ajouts pour $en_tete
							$temp=explode(";",$ligne);
							for($i=0;$i<sizeof($temp);$i++){
								$temp2=explode(",",$temp[$i]);
								$en_tete[$i]=$temp2[0];
							}

							$nbchamps=sizeof($en_tete);
						}
						$nblignes++;
					}
					fclose ($fp);

					// On range dans tabindice les indices des champs retenus
					/*
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[] = $i;
							}
						}
					}
					*/
					$cpt_tmp=0;
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[$cpt_tmp]=$i;
								$cpt_tmp++;
							}
						}
					}

					//=========================
					$fp=fopen($csv_file['tmp_name'],"r");
					// On lit une ligne pour passer la ligne d'entête:
					$ligne = fgets($fp, 4096);
					//=========================

					echo "<table border='1'>\n";
					echo "<tr>\n";

					echo "<td style='text-align: center; font-weight: bold;' rowspan='2'>Enregistrer<br />\n";
					echo "<a href=\"javascript:modifcase('coche')\">";
					echo "<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
					echo " / ";
					echo "<a href=\"javascript:modifcase('decoche')\">";
					echo "<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
					echo "</td>\n";

					echo "<td rowspan='2'>&nbsp;</td>\n";

					echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);' colspan='5'>Responsable</td>\n";

					echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;' colspan='3'>Elève</td>\n";

					echo "</tr>\n";

					echo "<tr>\n";
					echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>pers_id</td>\n";
					echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Nom</td>\n";
					echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Prénom</td>\n";
					echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>resp_legal</td>\n";
					echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>pers_contact</td>\n";

					echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>Nom</td>\n";
					echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>Prénom</td>\n";
					echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>ele_id</td>\n";
					echo "</tr>\n";


					$alt=1;

					$nb_reg_no1=0;
					$nb_record1=0;
					for($k = 1; ($k < $nblignes+1); $k++){
						//$ligne = dbase_get_record($fp,$k);
						if(!feof($fp)){
							$ligne = fgets($fp, 4096);
							if(trim($ligne)!=""){
								$tabligne=explode(";",$ligne);
								for($i = 0; $i < count($tabchamps); $i++) {
									//$ind = $tabindice[$i];
									$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
								}

								// Stockage des données:
								$responsable[$affiche[0]]=array();
								for($i=1;$i<count($tabchamps);$i++) {
									$responsable[$affiche[0]]["$tabchamps[$i]"]=$affiche[$i];
								}

								$ele_id=$affiche[0];
								$pers_id=$affiche[1];
								$resp_legal=$affiche[2];
								$pers_contact=$affiche[3];

								//echo "<tr>\n";

								//$sql="SELECT * FROM responsables2 WHERE ele_id='$affiche[0]' AND pers_id='$affiche[1]'";
								$sql="SELECT * FROM responsables2 WHERE (ele_id='$ele_id' AND pers_id='$pers_id')";
								$res1=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res1)==0){
									// L'association responsable/eleve n'existe pas encore
									$resp_new[]="$affiche[0]:$affiche[1]";


									$alt=$alt*(-1);
									echo "<tr style='background-color:";
									if($alt==1){
										echo "silver";
									}
									else{
										echo "white";
									}
									echo ";'>\n";

									$sql="SELECT nom,prenom FROM resp_pers WHERE (pers_id='$pers_id')";
									$res2=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res2)==0){
										// Problème: On ne peut pas importer l'association sans que la personne existe.
										// Est-ce que l'étape d'import de la personne a été refusée?
										echo "<td>&nbsp;</td>\n";
										echo "<td>&nbsp;</td>\n";

										echo "<td style='background-color:red;'>&nbsp;</td>\n";
										//echo "<td colspan='5'>Aucune personne associée???</td>\n";
										echo "<td colspan='7'>Aucune personne associée???</td>\n";
									}
									else{
										$lig2=mysqli_fetch_object($res2);
										echo "<td style='text-align:center;'>\n";
										echo "<input type='checkbox' id='check_".$cpt."' name='new[]' value='$cpt' />";
										echo "</td>\n";

										echo "<td style='text-align:center; background-color: rgb(150, 200, 240);'>Nouveau</td>\n";

										echo "<td style='text-align:center;'>\n";
										echo "$pers_id";
										echo "<input type='hidden' name='new_".$cpt."_pers_id' value='$pers_id' />\n";
										echo "</td>\n";

										echo "<td style='text-align:center;'>\n";
										echo "$lig2->nom";
										echo "<input type='hidden' name='new_".$cpt."_resp_nom' value=\"$lig2->nom\" />\n";
										echo "</td>\n";

										echo "<td style='text-align:center;'>\n";
										echo "$lig2->prenom";
										echo "<input type='hidden' name='new_".$cpt."_resp_prenom' value=\"$lig2->prenom\" />\n";
										echo "</td>\n";

										// Existe-t-il déjà un numéro de responsable légal correspondant au nouvel arrivant?

										echo "<td style='text-align:center;";
										//$sql="SELECT 1=1 FROM responsables2 WHERE (pers_id!='$pers_id' AND ele_id='$ele_id' AND resp_legal='$resp_legal')";
										$sql="SELECT 1=1 FROM responsables2 WHERE (pers_id!='$pers_id' AND ele_id='$ele_id' AND resp_legal='$resp_legal' AND (resp_legal='1' OR resp_legal='2'))";
										$res3=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res3)==0){
											echo "'>\n";
										}
										else{
											echo " background-color: lightgreen;'>\n";
										}
										echo "$resp_legal";
										echo "<input type='hidden' name='new_".$cpt."_resp_legal' value='$resp_legal' />\n";
										echo "</td>\n";

										echo "<td style='text-align:center;'>\n";
										echo "$pers_contact";
										echo "<input type='hidden' name='new_".$cpt."_pers_contact' value='$pers_contact' />\n";
										echo "</td>\n";

										// Elève(s) associé(s)
										$sql="SELECT nom,prenom FROM eleves WHERE (ele_id='$ele_id')";
										$res4=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res4)==0){
											echo "<td style='text-align:center; background-color:red;' colspan='3'>\n";
											echo "Aucun élève pour ele_id=$ele_id ???";
											echo "</td>\n";
										}
										else{
											$lig4=mysqli_fetch_object($res4);
											echo "<td style='text-align:center;'>\n";
											echo "$lig4->nom";
											echo "<input type='hidden' name='new_".$cpt."_ele_nom' value=\"$lig4->nom\" />\n";
											echo "</td>\n";

											echo "<td style='text-align:center;'>\n";
											echo "$lig4->prenom";
											echo "<input type='hidden' name='new_".$cpt."_ele_prenom' value=\"$lig4->prenom\" />\n";
											echo "</td>\n";

											echo "<td style='text-align:center;'>\n";
											echo "$ele_id";
											echo "<input type='hidden' name='new_".$cpt."_ele_id' value='$ele_id' />\n";
											echo "</td>\n";
										}

									}
									echo "</tr>\n";
								}
								else{


									$lig1=mysqli_fetch_object($res1);
									if((stripslashes($lig1->resp_legal)!=stripslashes($affiche[2]))||
									(stripslashes($lig1->pers_contact)!=stripslashes($affiche[3]))){
										// L'un des champs resp_legal ou pers_contact au moins a changé
										$resp_modif[]="$affiche[0]:$affiche[1]";

										/*
										echo "<tr>";
										echo "<td>\$ele_id=$ele_id</td>";
										echo "<td>\$pers_id=$pers_id</td>";
										echo "<td>\$resp_legal=$resp_legal</td>";
										echo "<td>\$pers_contact=$pers_contact</td>";
										echo "</tr>";
										*/

										$alt=$alt*(-1);
										echo "<tr style='background-color:";
										if($alt==1){
											echo "silver";
										}
										else{
											echo "white";
										}
										echo ";'>\n";

										$sql="SELECT nom,prenom FROM resp_pers WHERE (pers_id='$pers_id')";
										$res2=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res2)==0){
											// Problème: On ne peut pas importer l'association sans que la personne existe.
											// Est-ce que l'étape d'import de la personne a été refusée?
											echo "<td>&nbsp;</td>\n";
											echo "<td>&nbsp;</td>\n";

											echo "<td style='background-color:red;'>&nbsp;</td>\n";
											echo "<td colspan='5'>Aucune personne associée???</td>\n";
										}
										else{
											$lig2=mysqli_fetch_object($res2);
											echo "<td style='text-align:center;'>\n";
											echo "<input type='checkbox' id='check_".$cpt."' name='modif[]' value='$cpt' />";
											echo "</td>\n";

											echo "<td style='text-align:center; background-color:lightgreen;'>Modif</td>\n";

											echo "<td style='text-align:center;'>\n";
											echo "$pers_id";
											echo "<input type='hidden' name='modif_".$cpt."_pers_id' value='$pers_id' />\n";
											echo "</td>\n";

											echo "<td style='text-align:center;'>\n";
											echo "$lig2->nom";
											//echo "<input type='hidden' name='modif_".$cpt."_resp_nom' value=\"".addslashes($lig2->nom)."\" />\n";
											echo "<input type='hidden' name='modif_".$cpt."_resp_nom' value=\"".$lig2->nom."\" />\n";
											echo "</td>\n";

											echo "<td style='text-align:center;'>\n";
											echo "$lig2->prenom";
											//echo "<input type='hidden' name='modif_".$cpt."_resp_prenom' value=\"".addslashes($lig2->nom)."\" />\n";
											echo "<input type='hidden' name='modif_".$cpt."_resp_prenom' value=\"".$lig2->prenom."\" />\n";
											echo "</td>\n";

											// Existe-t-il déjà un numéro de responsable légal correspondant au nouvel arrivant?

											echo "<td style='text-align:center;";
											//$sql="SELECT 1=1 FROM responsables2 WHERE (pers_id!='$pers_id' AND ele_id='$ele_id' AND resp_legal='$resp_legal')";
											$sql="SELECT 1=1 FROM responsables2 WHERE (pers_id!='$pers_id' AND ele_id='$ele_id' AND resp_legal='$resp_legal' AND (resp_legal='1' OR resp_legal='2'))";
											$res3=mysqli_query($GLOBALS["mysqli"], $sql);
											if(mysqli_num_rows($res3)==0){
												echo "'>\n";
											}
											else{
												echo " background-color: lightgreen;'>\n";
											}
											echo "$resp_legal";
											echo "<input type='hidden' name='modif_".$cpt."_resp_legal' value='$resp_legal' />\n";
											echo "</td>\n";

											echo "<td style='text-align:center;'>\n";
											echo "$pers_contact";
											echo "<input type='hidden' name='modif_".$cpt."_pers_contact' value='$pers_contact' />\n";
											echo "</td>\n";

											// Elève(s) associé(s)
											$sql="SELECT nom,prenom FROM eleves WHERE (ele_id='$ele_id')";
											$res4=mysqli_query($GLOBALS["mysqli"], $sql);
											if(mysqli_num_rows($res4)==0){
												echo "<td style='text-align:center; background-color:red;' colspan='3'>\n";
												echo "Aucun élève pour ele_id=$ele_id ???";
												echo "</td>\n";
											}
											else{
												$lig4=mysqli_fetch_object($res4);
												echo "<td style='text-align:center;'>\n";
												echo "$lig4->nom";
												//echo "<input type='hidden' name='modif_".$cpt."_ele_nom' value=\"".addslashes($lig4->nom)."\" />\n";
												echo "<input type='hidden' name='modif_".$cpt."_ele_nom' value=\"".$lig4->nom."\" />\n";
												echo "</td>\n";

												echo "<td style='text-align:center;'>\n";
												echo "$lig4->prenom";
												//echo "<input type='hidden' name='modif_".$cpt."_ele_prenom' value=\"".addslashes($lig4->prenom)."\" />\n";
												echo "<input type='hidden' name='modif_".$cpt."_ele_prenom' value=\"".$lig4->prenom."\" />\n";
												echo "</td>\n";

												echo "<td style='text-align:center;'>\n";
												echo "$ele_id";
												echo "<input type='hidden' name='modif_".$cpt."_ele_id' value='$ele_id' />\n";
												echo "</td>\n";
											}

										}
										echo "</tr>\n";
									}
									// Sinon, il n'est pas nécessaire de refaire l'inscription déjà présente.
								}

								//echo "</tr>\n";
								$cpt++;
							}
						}
					}
					echo "</table>\n";
					//dbase_close($fp);
					fclose($fp);


					echo "<script type='text/javascript'>
	function modifcase(mode){
		for(i=0;i<$cpt;i++){
			if(document.getElementById('check_'+i)){
				if(mode=='coche'){
					document.getElementById('check_'+i).checked=true;
				}
				else{
					document.getElementById('check_'+i).checked=false;
				}
			}
		}
	}
</script>\n";


					/*
					if ($nb_reg_no1 != 0) {
						echo "<p>Lors de l'enregistrement des données de RESPONSABLES.CSV, il y a eu $nb_reg_no1 erreurs. Essayez de trouvez la cause de l'erreur.</p>\n";
					}
					else {
						echo "<p>L'importation des relations eleves/responsables dans la base GEPI a été effectuée avec succès (".$nb_record1." enregistrements au total).</p>\n";
					}
					*/


					echo "<p align='center'><input type=submit value='Valider' /></p>\n";
					echo "</form>\n";
				}
			} else if (trim($csv_file['name'])=='') {
				echo "<p>Aucun fichier RESPONSABLES.CSV n'a été sélectionné !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
				echo "</body>\n</html>\n";
				die();

			} else {
				echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
				echo "</body>\n</html>\n";
				die();
			}










			break;

		case 8:
			// Import des informations responsables
			echo "<h2>Import/mise à jour des associations responsables/élèves</h2>\n";

			$erreur=0;
			if(isset($modif)){
				echo "<p>Mise à jour des informations pour ";
				for($i=0;$i<count($modif);$i++){
					$cpt=$modif[$i];

					$pers_id=isset($_POST['modif_'.$cpt.'_pers_id']) ? $_POST['modif_'.$cpt.'_pers_id'] : NULL;
					$resp_nom=isset($_POST['modif_'.$cpt.'_resp_nom']) ? traitement_magic_quotes(corriger_caracteres($_POST['modif_'.$cpt.'_resp_nom'])) : NULL;
					$resp_prenom=isset($_POST['modif_'.$cpt.'_resp_prenom']) ? traitement_magic_quotes(corriger_caracteres($_POST['modif_'.$cpt.'_resp_prenom'])) : NULL;

					$ele_id=isset($_POST['modif_'.$cpt.'_ele_id']) ? $_POST['modif_'.$cpt.'_ele_id'] : NULL;
					$ele_nom=isset($_POST['modif_'.$cpt.'_ele_nom']) ? traitement_magic_quotes(corriger_caracteres($_POST['modif_'.$cpt.'_ele_nom'])) : NULL;
					$ele_prenom=isset($_POST['modif_'.$cpt.'_ele_prenom']) ? traitement_magic_quotes(corriger_caracteres($_POST['modif_'.$cpt.'_ele_prenom'])) : NULL;

					$resp_legal=isset($_POST['modif_'.$cpt.'_resp_legal']) ? $_POST['modif_'.$cpt.'_resp_legal'] : NULL;
					$pers_contact=isset($_POST['modif_'.$cpt.'_pers_contact']) ? $_POST['modif_'.$cpt.'_pers_contact'] : NULL;

					if((isset($pers_id))&&(isset($resp_nom))&&(isset($resp_prenom))&&(isset($ele_id))&&(isset($ele_nom))&&(isset($ele_prenom))&&(isset($resp_legal))&&(isset($pers_contact))){
						if($i>0){echo ", ";}
						//echo "$pers_id - $nom - $prenom<br />\n";
						//echo "$resp_prenom $resp_nom ($pers_id) / $ele_prenom $ele_nom ($ele_id)\n";
						echo stripslashes("$resp_prenom $resp_nom")." ($pers_id) / ".stripslashes("$ele_prenom $ele_nom")." ($ele_id)\n";

						$sql="UPDATE responsables2 SET resp_legal='$resp_legal',
										pers_contact='$pers_contact'
									WHERE (pers_id='$pers_id' AND ele_id='$ele_id')";
						//echo "<p>$sql</p>\n";
						$res1=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res1){
							//echo " (<font color='red'>erreur</font>)";
							echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
							$erreur++;
						}
					}
				}
				echo "</p>\n";
			}

			if(isset($new)){
				echo "<p>Insertion des informations pour ";
				for($i=0;$i<count($new);$i++){
					$cpt=$new[$i];

					/*
					$pers_id=$_POST['modif_'.$cpt.'_pers_id'];
					$resp_nom=traitement_magic_quotes(corriger_caracteres($_POST['modif_'.$cpt.'_resp_nom']));
					$resp_prenom=traitement_magic_quotes(corriger_caracteres($_POST['modif_'.$cpt.'_resp_prenom']));

					$ele_id=$_POST['modif_'.$cpt.'_ele_id'];
					$ele_nom=traitement_magic_quotes(corriger_caracteres($_POST['modif_'.$cpt.'_ele_nom']));
					$ele_prenom=traitement_magic_quotes(corriger_caracteres($_POST['modif_'.$cpt.'_ele_prenom']));

					$resp_legal=$_POST['modif_'.$cpt.'_resp_legal'];
					$pers_contact=$_POST['modif_'.$cpt.'_pers_contact'];
					*/

					$pers_id=isset($_POST['new_'.$cpt.'_pers_id']) ? $_POST['new_'.$cpt.'_pers_id'] : NULL;
					$resp_nom=isset($_POST['new_'.$cpt.'_resp_nom']) ? traitement_magic_quotes(corriger_caracteres($_POST['new_'.$cpt.'_resp_nom'])) : NULL;
					$resp_prenom=isset($_POST['new_'.$cpt.'_resp_prenom']) ? traitement_magic_quotes(corriger_caracteres($_POST['new_'.$cpt.'_resp_prenom'])) : NULL;

					$ele_id=isset($_POST['new_'.$cpt.'_ele_id']) ? $_POST['new_'.$cpt.'_ele_id'] : NULL;
					$ele_nom=isset($_POST['new_'.$cpt.'_ele_nom']) ? traitement_magic_quotes(corriger_caracteres($_POST['new_'.$cpt.'_ele_nom'])) : NULL;
					$ele_prenom=isset($_POST['new_'.$cpt.'_ele_prenom']) ? traitement_magic_quotes(corriger_caracteres($_POST['new_'.$cpt.'_ele_prenom'])) : NULL;

					$resp_legal=isset($_POST['new_'.$cpt.'_resp_legal']) ? $_POST['new_'.$cpt.'_resp_legal'] : NULL;
					$pers_contact=isset($_POST['new_'.$cpt.'_pers_contact']) ? $_POST['new_'.$cpt.'_pers_contact'] : NULL;


					if((isset($pers_id))&&(isset($resp_nom))&&(isset($resp_prenom))&&(isset($ele_id))&&(isset($ele_nom))&&(isset($ele_prenom))&&(isset($resp_legal))&&(isset($pers_contact))){
						if($i>0){echo ", ";}
						//echo "$pers_id - $nom - $prenom<br />\n";
						//echo "$resp_prenom $resp_nom ($pers_id) / $ele_prenom $ele_nom ($ele_id)\n";
						echo stripslashes("$resp_prenom $resp_nom")." ($pers_id) / ".stripslashes("$ele_prenom $ele_nom")." ($ele_id)\n";

						// On supprime l'inscription précédente si elle existe:
						$sql="SELECT pers_id FROM responsables2 WHERE (pers_id='$pers_id' AND ele_id='$ele_id')";
						//echo "$sql<br />\n";
						$res1=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res1)>0){
							$sql="DELETE FROM responsables2 WHERE (pers_id='$pers_id' AND
											ele_id='$ele_id')";
							//echo "$sql<br />\n";
							$res2=mysqli_query($GLOBALS["mysqli"], $sql);
						}

						// On teste s'il faut supprimer un autre responsable de même rang resp_legal:
						$sql="SELECT pers_id FROM responsables2 WHERE (pers_id!='$pers_id' AND ele_id='$ele_id' AND resp_legal='$resp_legal')";
						$res1=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res1)==0){
							$sql="INSERT INTO responsables2 SET pers_id='$pers_id',
											ele_id='$ele_id',
											resp_legal='$resp_legal',
											pers_contact='$pers_contact'";
							//echo "$sql<br />\n";
							$res2=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res2){
								//echo " (<font color='red'>erreur</font>)";
								echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
								$erreur++;
							}
						}
						else{
							$lig1=mysqli_fetch_object($res1);
							$sql="DELETE FROM responsables2 WHERE (pers_id='$lig1->pers_id' AND
											ele_id='$ele_id' AND
											resp_legal='$resp_legal')";
							$res2=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res2){
								//echo " (<font color='red'>erreur</font>)";
								echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
								$erreur++;
							}
							else{
								$sql="INSERT INTO responsables2 SET pers_id='$pers_id',
												ele_id='$ele_id',
												resp_legal='$resp_legal',
												pers_contact='$pers_contact'";
								//echo "$sql<br />\n";
								$res3=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$res3){
									//echo " (<font color='red'>erreur</font>)";
									echo "<br />\n<font color='red'>Erreur:</font> $sql<br />\n";
									$erreur++;
								}
							}
						}
					}
				}
				echo "</p>\n";
			}


			//echo "<p>Passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=6'>import/mise à jour des responsables</a>.</p>\n";
			switch($erreur){
				case 0:
					echo "<p>L'importation s'est correctement passée.</p>\n";
					break;

				case 1:
					echo "<p><font color='red'>Une erreur s'est produite.</font><br />\nVous devriez en chercher la cause.</p>\n";
					break;

				default:
					echo "<p><font color='red'>$erreur erreurs se sont produites.</font><br />\nVous devriez en chercher la cause.</p>\n";
					break;
			}

			break;

	}





/*
			if(($nb_reg_no1==0)&&($nb_reg_no2==0)&&($nb_reg_no3==0)&&($erreur==0)){
				echo "<p>L'opération s'est correctement déroulée.</p>\n";
				echo "<center><p><a href='../accueil.php'>Retourner à l'accueil</a></p></center>\n";

				// On renseigne le témoin de mise à jour effectuée:
				saveSetting("conv_new_resp_table", 1);
			}
			else{
				echo "<p>Des erreurs se sont produites.</p>\n";
			}
*/


}

echo "<p><br /></p>\n";
echo "<p><i>NOTES:</i></p>\n";
echo "<ul>\n";
echo "<li>\n";
echo "<p>Les noms de fichiers fournis dans les champs de formulaires doivent coïncider avec le nom indiqué ELEVES.CSV, ADRESSES.CSV,...\n";
echo "</p>\n";
echo "</li>\n";
echo "<li>";
echo "<p>Il reste aussi à assurer l'import de l'établissement d'origine avec les fichiers etablissements.csv et eleves_etablissements.csv<br />\n";
echo "Par ailleurs, l'inscription des élèves dans telle ou telle classe, avec telle et telle option n'est pas encore assurée par cette page d'importation/mise à jour.<br />\n";
echo "(<i>il faut donc par la suite affecter les nouveaux élèves dans les classes et les inscrire dans les groupes/options/matières</i>)<br />\n";
echo "Il faut également, pour les nouveaux élèves inscrits (<i>s'il y en a</i>), faire l'association avec le CPE responsable et le ".getSettingValue('gepi_prof_suivi').".";
echo "</p>\n";
echo "</li>\n";
echo "</ul>\n";

require("../lib/footer.inc.php");
?>
