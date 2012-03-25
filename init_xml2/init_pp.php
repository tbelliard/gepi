<?php

@set_time_limit(0);
/*
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$liste_tables_del = array(
"j_eleves_professeurs"
);

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des professeurs principaux";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>

<?php

//debug_var();

if (!isset($step1)) {

	$dirname=get_user_temp_directory();
	if(@file_exists("../temp/".$dirname."/matiere_principale.csv")) {

		$fich_mp=fopen("../temp/".$dirname."/matiere_principale.csv","r");
		if($fich_mp) {
			echo "<p class='bold'>Rétablissement de la matière principale de chaque professeur d'après les enregistrements de l'année précédente.</p>\n";

			$temoin_erreur=0;

			$temoin_debut_mp=0;
			while(!feof($fich_mp)) {
				$ligne=trim(fgets($fich_mp, 4096));

				$tab=explode(";",$ligne);
				if(($tab[0]!="")&&($tab[1]!="")) {
					$login_prof=$tab[0];
					$matiere_prof=$tab[1];
	
					unset($tab_matiere_prof);
					unset($tab_matiere_prof_reordonne);
					$tab_matiere_prof=array();
					$tab_matiere_prof_reordonne=array();
					$sql="SELECT * FROM j_professeurs_matieres WHERE id_professeur='$login_prof' ORDER BY ordre_matieres;";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)>0) {
						while($lig_mp=mysql_fetch_object($res)) {
							$tab_matiere_prof[]=$lig_mp->id_matiere;
						}
					}
	
					if(in_array($matiere_prof,$tab_matiere_prof)) {
						// On va contrôler si la matière est bien au premier rang
						if($tab_matiere_prof[0]!=$matiere_prof) {
							// Il faut réordonner
							$tab_matiere_prof_reordonne[]=$matiere_prof;
							for($loop=0;$loop<count($tab_matiere_prof);$loop++) {
								if($tab_matiere_prof[$loop]!=$matiere_prof) {
									$tab_matiere_prof_reordonne[]=$tab_matiere_prof[$loop];
								}
							}
	
							if($temoin_debut_mp==0) {
								echo "<p>Correction de la matière principale pour ";
							}
							else {
								echo ", ";
							}
	
							echo "<a href='../utilisateurs/modify_user.php?user_login=$login_prof' target='_blank'>$login_prof</a>";
	
							$sql="DELETE FROM j_professeurs_matieres WHERE id_professeur='$login_prof';";
							//echo "$sql<br />\n";
							$del=mysql_query($sql);
							if(!$del) {
								echo " (<span style='color:red'>ERREUR</span>)";
								$temoin_erreur++;
							}
							else {
								for($loop=0;$loop<count($tab_matiere_prof_reordonne);$loop++) {
									$k=$loop+1;
									$sql="INSERT INTO j_professeurs_matieres SET id_professeur='$login_prof', id_matiere='".$tab_matiere_prof_reordonne[$loop]."', ordre_matieres='$k';";
									//echo "$sql<br />\n";
									$insert=mysql_query($sql);
									if($insert) {
										echo " (<span style='color:green";
										if($loop==0) {echo "; font-weight: bold";}
										echo "'>".$tab_matiere_prof_reordonne[$loop]."</span>)";
									}
									else {
										echo " (<span style='color:red'>".$tab_matiere_prof_reordonne[$loop]."</span>)";
										$temoin_erreur++;
									}
								}
							}
							$temoin_debut_mp++;
						}
						//else {
						//	echo "$login_prof a déjà la bonne matière principale&nbsp;: $matiere_prof<br />";
						//}
					}
				}
			}
			fclose($fich_mp);

			if($temoin_erreur==0) {
				unlink("../temp/".$dirname."/matiere_principale.csv");
			}

			if($temoin_debut_mp>0) {
				echo ".</p>\n";
	
				echo "<p><i>Remarque&nbsp;:</i> Les professeurs pour lesquels la matière principale est déjà la bonne n'apparaissent pas dans les corrections ci-dessus.</p>\n";
			}
		}
	}

	echo "<center><h3 class='gepi'>Sixième phase<br />Importation des professeurs principaux</h3></center>\n";

	$j=0;
	$flag=0;
	while (($j < count($liste_tables_del)) and ($flag==0)) {
		if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
			$flag=1;
		}
		$j++;
	}

	if ($flag != 0){
		echo "<p><b>ATTENTION ...</b><br />\n";
		echo "Des professeurs principaux sont actuellement définis dans la base GEPI (<i>table 'j_eleves_professeurs'</i>)<br /></p>\n";
		//echo "<p>Si vous poursuivez la procédure ces données seront supprimées et remplacées par celles de votre fichier F_DIV.CSV</p>\n";
		echo "<p>Si vous poursuivez la procédure ces données seront supprimées et remplacées par celles de votre import XML.</p>\n";

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo add_token_field();
		echo "<input type='hidden' name='step1' value='y' />\n";
		echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />\n";
		echo "</form>\n";
		echo "<br />\n";
		require("../lib/footer.inc.php");
		die();
	}
}
else {
	echo "<center><h3 class='gepi'>Sixième phase<br />Importation des professeurs principaux</h3></center>\n";
}

$tempdir=get_user_temp_directory();
if(!$tempdir){
	echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
	// Il ne faut pas aller plus loin...
	// SITUATION A GERER
}

if (!isset($is_posted)) {
	if(isset($step1)) {
		check_token(false);
		$j=0;
		while ($j < count($liste_tables_del)) {
			if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				//echo "DELETE FROM $liste_tables_del[$j]<br />";
				$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
			}
			$j++;
		}
	}

	if(!file_exists("../temp/$tempdir/f_div.csv")){
		echo "<p>Le fichier <b>f_div.csv</b> n'est pas présent dans votre dossier temporaire.<br />Auriez-vous sauté l'étape de l'importation des professeurs???</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<p><b>ATTENTION ...</b><br />Vous ne devez procéder à cette opération uniquement si la constitution des classes a été effectuée et si les professeurs ont été importés !</p>\n";
	//echo "<p>Importation du fichier <b>F_div.csv</b> (<i>généré lors de l'importation des professeurs</i>) contenant les associations classe/professeur principal.</p>\n";
	echo "<p>Importation des associations classe/professeur principal.</p>\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo "<input type=hidden name='step1' value='y' />\n";
	//echo "<p><input type='file' size='80' name='dbf_file' />";
	echo "<p><input type=submit value='Valider' /></p>\n";
	echo "</form>\n";

} else {
	check_token(false);

	$fp = fopen("../temp/$tempdir/f_div.csv","r");
	if(!$fp) {
		echo "<p>Impossible d'ouvrir le fichier CSV normalement généré lors de l'import des professeurs.</p>\n";
		echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
	} else {
		// on constitue le tableau des champs à extraire
		$tabchamps = array("DIVCOD","NUMIND");

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

				//$en_tete=explode(";",$ligne);
				$nbchamps=sizeof($en_tete);
			}
			$nblignes++;
		}
		fclose ($fp);

		$cpt_tmp=0;
		for ($k = 0; $k < count($tabchamps); $k++) {
			for ($i = 0; $i < count($en_tete); $i++) {
				if (trim($en_tete[$i]) == $tabchamps[$k]) {
					$tabindice[$cpt_tmp]=$i;
					$cpt_tmp++;
				}
			}
		}

		echo "<table border='1' class='boireaus' cellpadding='2' cellspacing='2' summary='Tableau des professeurs principaux'>\n";
		echo "<tr><th><p class=\"small\">Classe</p></th><th><p class=\"small\">Professeur principal</p></th></tr>\n";


		//=========================
		$fp = fopen("../temp/$tempdir/f_div.csv","r");
		// On lit une ligne pour passer la ligne d'entête:
		$ligne = fgets($fp, 4096);
		//=========================
		$nb_reg_no = 0;
		$nb_reg_ok = 0;
		for($k = 1; ($k < $nblignes+1); $k++){
			if(!feof($fp)){
				//====================
				// Suppression des guillemets éventuels
				$ligne = preg_replace('/"/','',fgets($fp, 4096));
				//====================
				if(trim($ligne)!=""){
					$tabligne=explode(";",$ligne);
					$temoin_erreur="non";
					for($i = 0; $i < count($tabchamps); $i++) {
						//$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
						$affiche[$i] = trim($tabligne[$tabindice[$i]]);
						//echo "<tr><td colspan='2'>|\$affiche[$i]|=|$affiche[$i]|</td></tr>";
						if($affiche[$i]==""){
							$temoin_erreur="oui";
							$nb_reg_no++;
						}
					}

					if($temoin_erreur!="oui"){
						$sql="SELECT id FROM classes WHERE classe='$affiche[0]'";
						$res_classe=mysql_query($sql);
						if(mysql_num_rows($res_classe)==1){
							$lig_classe=mysql_fetch_object($res_classe);
							$id_classe=$lig_classe->id;

							$sql="SELECT col1 FROM tempo2 WHERE col2='$affiche[1]'";
							//echo "<tr><td>$sql</td></tr>\n";
							$res_prof=mysql_query($sql);
							$lig_prof=mysql_fetch_object($res_prof);

							$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY login";
							$res_eleve=mysql_query($sql);
							$temoin_erreur_classe=0;
							while($lig_eleve=mysql_fetch_object($res_eleve)){
								$sql="INSERT INTO j_eleves_professeurs VALUES('$lig_eleve->login','$lig_prof->col1','$id_classe')";
								$res_prof_eleve=mysql_query($sql);
								if(!$res_prof_eleve){
									$temoin_erreur_classe++;
									//echo "<tr><td>$sql</td></tr>\n";
								}
							}

							if($temoin_erreur_classe==0){
								echo "<tr style='background-color:#aae6aa;'><td>$affiche[0]</td><td>$lig_prof->col1</td></tr>\n";
								$nb_reg_ok++;
							}
							else{
								echo "<tr style='background-color:red;'><td>$affiche[0]</td><td>$lig_prof->col1</td></tr>\n";
								$nb_reg_no++;
							}
							//echo "<tr><td>$affiche[0]</td><td>$lig_prof->col1</td></tr>\n";
						}
					}
					else{
						echo "<tr style='background-color:red;'><td>$affiche[0]</td><td>$affiche[1]</td></tr>\n";
					}
				}
			}
		}
		echo "</table>\n";
		fclose($fp);

		if ($nb_reg_no != 0) {
			echo "<p>Lors de l'enregistrement des données, il y a eu $nb_reg_no erreurs.<br />Essayez d'en trouver la cause et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
		} else {
			if($nb_reg_ok>0){
				echo "<p>L'importation des professeurs principaux dans la base GEPI a été effectuée avec succès !</p>\n";
			}
			else{
				echo "<p>Aucun professeur principal n'a été inscrit dans la base GEPI !</p>\n";
			}

			if(getSettingValue("mode_sauvegarde")=="mysqldump") {$mode_sauvegarde="system_dump";}
			else {$mode_sauvegarde="dump";}

			echo "<p>Avant de procéder à un nettoyage des tables pour supprimer les données inutiles, vous devriez effectuer une <a href='../gestion/accueil_sauve.php?action=$mode_sauvegarde&amp;quitter_la_page=y".add_token_in_url()."' target='_blank'>sauvegarde</a><br />\n";
			echo "Après cette sauvegarde, effectuez le nettoyage en repassant par 'Gestion générale/Initialisation des données à partir de fichiers DBF et XML/Procéder à la septième phase'.<br />\n";
			echo "Si les données sont effectivement inutiles, c'est terminé.<br />\n";
			echo "Sinon, vous pourrez restaurer votre sauvegarde et vous aurez pu noter les associations profs/matières/classes manquantes... à effectuer par la suite manuellement dans 'Gestion des bases'.</p>\n";

			echo "<p>Vous pouvez procéder à l'étape suivante de nettoyage des tables GEPI.</p>\n";
			echo "<center><p><a href='clean_tables.php?a=a".add_token_in_url()."'>Suppression des données inutiles</a></p></center>\n";
		}
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>