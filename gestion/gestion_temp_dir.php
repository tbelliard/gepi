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

// Resume session
$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
   header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// INSERT INTO droits VALUES ('/gestion/gestion_temp_dir.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des dossiers temporaires d utilisateurs', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


$chemin_temp="../temp";

$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : NULL;
$reinit=isset($_POST['reinit']) ? $_POST['reinit'] : NULL;

$reinitialiser=isset($_POST['reinitialiser']) ? $_POST['reinitialiser'] : (isset($_GET['reinitialiser']) ? $_GET['reinitialiser'] : NULL);


//if((isset($_POST['is_posted']))&&(($suppr))||(isset($reinit))) {
if(isset($reinitialiser)) {
	check_token();

	$msg="";

	$nb_reinit=0;
	$nb_suppr=0;
	for($i=0;$i<count($reinit);$i++){
		if(mb_strlen(preg_replace("/[A-Za-z0-9_.\-]/","",$reinit[$i]))!=0) {
			$msg.="Le choix $reinit[$i] n'est pas valide.<br />\n";
		}
		else{
			$sql="SELECT temp_dir FROM utilisateurs WHERE login='$reinit[$i]';";
			$res_td=mysql_query($sql);

			if(mysql_num_rows($res_td)=="1"){
				$lig_td=mysql_fetch_object($res_td);

				$temp_dir=$lig_td->temp_dir;

				if(($temp_dir=="")||(mb_strlen(preg_replace("/[A-Za-z0-9_.-]/","",$temp_dir))!=0)) {
					$msg.="La valeur de temp_dir pour $reinit[$i] est inattendue: <font color='green'>'</font>$temp_dir<font color='green'>'</font><br />\n";
				}
				else {
					if(file_exists("$chemin_temp/$temp_dir")){
						//if(unlink("$chemin_temp/$temp_dir")){
						if(is_file("$chemin_temp/$temp_dir")) {
							$res_suppr=unlink("$chemin_temp/$temp_dir");
						}
						else {
							$suppression=vider_dir("$chemin_temp/$temp_dir", array("signature"));
							if(is_array($suppression)) {
								$res_suppr=false;
								$msg.="$temp_dir contient des fichiers ou dossiers exclus de la suppression : ";
								for($loop=0;$loop<count($suppression);$loop++) {
									if($loop>0) {$msg.=", ";}
									$msg.=$suppression[$loop];
								}
								$msg.=".<br />\n";
							}
							elseif($suppression) {
								$res_suppr=rmdir("$chemin_temp/$temp_dir");
							}
							else{
								$res_suppr=false;
								$msg.="Il n'a pas été possible de vider $temp_dir<br />\n";
							}
						}

						if($res_suppr){
							$nb_suppr++;
						}
						else{
							$msg.="Erreur lors de la suppression du dossier temporaire de $reinit[$i]<br />\n";
						}
					}
					else{
						$msg.="Le dossier $temp_dir n'existe pas.<br />\n";
						$res_suppr=true;
					}

					if($res_suppr) {
						// On vide le champ temp_dir... une nouvelle valeur sera générée au prochain login
						$sql="UPDATE utilisateurs SET temp_dir='' WHERE login='".$reinit[$i]."'";
						$res_update=mysql_query($sql);
						if(!$res_update) {
							$msg.="Erreur lors de la réinitialisation de temp_dir pour $reinit[$i].<br />\n";
						}
					}
				}
			}
		}
	}
}
else{
	if((isset($_POST['is_posted']))&&(isset($suppr))) {
		check_token();
		$msg="";

		$nb_suppr=0;
		for($i=0;$i<count($suppr);$i++){
			//if(!my_ereg("_[A-Za-z0-9]{40}",$suppr[$i])) {
			if(!preg_match("/_/",$suppr[$i])) {
				$msg.="Le choix $suppr[$i] n'est pas valide.<br />\n";
			}
			elseif(mb_strlen(preg_replace("/[A-Za-z0-9_.-]/","",$suppr[$i]))!=0) {
				$msg.="Le choix $suppr[$i] n'est pas valide.<br />\n";
			}
			else{
					if(file_exists("$chemin_temp/$suppr[$i]")){
						if(is_file("$chemin_temp/$suppr[$i]")) {
							$res_suppr=unlink("$chemin_temp/$suppr[$i]");
						}
						else {
							if(isset($_POST['sans_exclusion'])) {
								$res_suppr=deltree("$chemin_temp/$suppr[$i]");
							}
							else {
								$suppression=vider_dir("$chemin_temp/$suppr[$i]", array("signature"));
								if(is_array($suppression)) {
									$res_suppr=false;
									$msg.="$suppr[$i] contient des fichiers ou dossiers exclus de la suppression : ";
									for($loop=0;$loop<count($suppression);$loop++) {
										if($loop>0) {$msg.=", ";}
										$msg.=$suppression[$loop];
									}
									$msg.=".<br />\n";
								}
								elseif($suppression) {
									$res_suppr=rmdir("$chemin_temp/$suppr[$i]");
								}
								else{
									$res_suppr=false;
									$msg.="Il n'a pas été possible de vider $suppr[$i]<br />\n";
								}
							}
						}

						if($res_suppr){
							$nb_suppr++;
						}
						else{
							$msg.="Erreur lors de la suppression de $suppr[$i]<br />\n";
						}
					}
					else{
						$msg.="$suppr[$i] n'existe pas.<br />\n";
					}
				//}
			}
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Outil de gestion | Gestion des dossiers temporaires";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href='index.php#gestion_temp_dir'><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour</a> | \n";
if(isset($reinitialiser)) {
	check_token(false);

	echo "<a href='".$_SERVER['PHP_SELF']."'>Suppression</a>";
	echo "</p>\n";
	echo "<h2>Réinitialisation des dossiers temporaires</h2>\n";

	echo "<p>La réinitialisation des dossiers temporaires permet de supprimer le dossier temporaire d'un utilisateur et de vider le chemin aléatoire de ce dossier de façon à ce qu'une nouvelle valeur soit générée au login suivant.</p>\n";

	$sql="SELECT login,nom,prenom FROM utilisateurs WHERE temp_dir!='' ORDER BY statut,nom,prenom";
	$res_user=mysql_query($sql);

	if(mysql_num_rows($res_user)==0){
		echo "<p>Aucun utilisateur n'est encore concerné par la réinitialisation...</p>\n";
	}
	else{
		echo "<form action='".$_SERVER['PHP_SELF']."' method=\"post\" name=\"formulaire\">\n";
		echo add_token_field();

		echo "<p>Voici la liste des utilisateurs dont l'aléa peut être recalculé<br />(<i>Les utilisateurs qui n'apparaissent pas, auront de toute façon un nouveau dossier temporaire généré lors de leur prochain login</i>):</p>\n";

		while ($lig_user=mysql_fetch_object($res_user)){
			$tab_user_login[]=$lig_user->login;
			$tab_user_info[]=my_strtoupper($lig_user->nom)." ".casse_mot($lig_user->prenom,'majf2');
		}

		// Nombre d'enregistrements à afficher
		$nombreligne=count($tab_user_login);
		$nbcol=3;

		// Nombre de lignes dans chaque colonne:
		$nb_class_par_colonne=round($nombreligne/$nbcol);

		echo "<table width='100%' class='boireaus' summary='Tableau des utilisateurs et volumes'>\n";
		echo "<tr>\n";
		$alt=1;
		echo "<td class='lig$alt' style='text-align:left;vertical-align:top;'>\n";
		$i = 0;
		while ($i < $nombreligne){

			if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
				echo "</td>\n";
				$alt=$alt*(-1);
				echo "<td class='lig$alt' style='text-align:left;vertical-align:top;'>\n";
			}

			echo "<br />\n";
			//echo "<input type='checkbox' id='case$i' name='reinit[]' value='".$tab_user_login[$i]."' /> ".$tab_user_info[$i];
			echo "<label id='label_case_$i' for='case$i' style='cursor: pointer;'><input type='checkbox' id='case$i' name='reinit[]' value='".$tab_user_login[$i]."' onchange='change_style_case($i)' /> $tab_user_info[$i]</label>";
			$i++;
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<input type='hidden' name='is_posted' value='1' />\n";
		echo "<center><a href='javascript:modif_case(true)'>Tout cocher</a> / \n";
		echo "<a href='javascript:modif_case(false)'>Tout décocher</a></center>\n";
		echo "<center><input type='submit' name='reinitialiser' value='Réinitialiser' /></center>\n";
		echo "</form>\n";


		echo "<script type='text/javascript' language='javascript'>
	function modif_case(statut){
		// statut: true ou false
		for(k=0;k<$nombreligne;k++){
			if(document.getElementById('case'+k)){
				document.getElementById('case'+k).checked=statut;
				change_style_case(k);
			}
		}
		changement();
	}

	function change_style_case(num) {
		if(document.getElementById('case'+num)) {
			if(document.getElementById('case'+num).checked) {
				document.getElementById('label_case_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_case_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";

	}
}
else{
	echo "<a href='".$_SERVER['PHP_SELF']."?reinitialiser=y".add_token_in_url()."'>Réinitialisation</a>";
	echo "</p>\n";
	echo "<h2>Suppression de dossiers temporaires</h2>\n";

	echo "<div align='center' class='gestion_temp_dir'>\n";
	echo "<form action='".$_SERVER['PHP_SELF']."' method=\"post\" name=\"formulaire\">\n";
	echo add_token_field();

	echo "<table border='1' class='boireaus' summary='Tableau des utilisateurs et volumes'>\n";
	echo "<tr>\n";
	echo "<th>Login</th>\n";
	echo "<th>Nom</th>\n";
	echo "<th>Prénom</th>\n";
	echo "<th>Statut</th>\n";
	echo "<th>Etat</th>\n";
	echo "<th style='background-color: #96c8f0;'>Type</th>\n";
	echo "<th style='background-color: #96c8f0;'>Volume</th>\n";
	echo "<th>Supprimer";
	echo "<br />\n";

	echo "<a href='javascript:modif_case(true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
	echo "<a href='javascript:modif_case(false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";

	echo "</th>\n";
	//echo "<th>Réinitialiser</th>\n";
	echo "</tr>\n";


	$handle=opendir($chemin_temp);
	$cpt=0;
	$bizarre=0;
	$alt=1;
	while ($file=readdir($handle)) {
		$afficher_dossier='y';
		if(($multisite=='y')&&(isset($_COOKIE['RNE']))) {
			$pref_multi=$_COOKIE['RNE']."_";
			if(!preg_match("/^$pref_multi/", $file)) {
				$afficher_dossier='n';
			}
		}

		//if(($file!=".")&&($file!="..")) {
		if(($file!=".")&&($file!="..")&&($file!="index.html")&&($afficher_dossier=='y')) {
			//$num=$cpt%2;
			//echo "<tr class='lig".$num."'>\n";
			$alt=$alt*(-1);
			echo "<tr class='lig".$alt."'>\n";

			// Test:
			if((mb_strlen(preg_replace("/[A-Za-z0-9_.-]/","",$file))!=0)||(!preg_match("/_/",$file))) {
				// Il y a des caractères inattendus dans le nom de dossier
				$bizarre++;

				echo "<td colspan='5' style='background-color:red; text-align:center;'>$file</td>\n";
				if(is_file($chemin_temp."/".$file)) {
					echo "<td>Fichier</td>";
					echo "<td>";
					unset($tab_file_tmp);
					$tab_file_tmp=stat($chemin_temp."/".$file);
					//echo "Fichier: ".$tab_file_tmp[0]." soit ".$tab_file_tmp[7];
					echo volume_human($tab_file_tmp[7]);
					echo "</td>\n";
				}
				else{
					echo "<td>Dossier</td>";
					echo "<td>";
					//echo "Dossier: ".disk_total_space($chemin_temp."/".$file);
					echo volume_dir_human($chemin_temp."/".$file);
					echo "</td>\n";
				}
				echo "<td style='background-color:red; text-align:center;'>?</td>\n";
				//echo "<td style='background-color:red; text-align:center;'>?</td>\n";
			}
			else{
				$tabtmp=explode("_",$file);
				if(mb_strlen(preg_replace("/[A-Za-z0-9.-]/","",$tabtmp[0]))!=0) {
					$bizarre++;

					echo "<td colspan='5' style='background-color:red; text-align:center;'>$file</td>\n";
					echo "<td>";
					if(is_file($chemin_temp."/".$file)) {
						echo "<td>Fichier</td>";
						echo "<td>";
						unset($tab_file_tmp);
						$tab_file_tmp=stat($chemin_temp."/".$file);
						//echo "Fichier: ".$tab_file_tmp[0]." soit ".$tab_file_tmp[7];
						echo volume_human($tab_file_tmp[7]);
						echo "</td>\n";
					}
					else{
						echo "<td>Dossier</td>";
						echo "<td>";
						//echo "Dossier: ".disk_total_space($chemin_temp."/".$file);
						echo volume_dir_human($chemin_temp."/".$file);
						echo "</td>\n";
					}
					echo "<td style='background-color:red; text-align:center;'>?</td>\n";
					//echo "<td style='background-color:red; text-align:center;'>?</td>\n";
				}
				else{
					$sql="SELECT nom,prenom,statut,etat FROM utilisateurs WHERE login='$tabtmp[0]'";
					//echo "<!-- $sql -->\n";
					$res_user=mysql_query($sql);

					if(mysql_num_rows($res_user)==0){
						echo "<td>$tabtmp[0]</td>\n";
						echo "<td colspan='4' style='color:red;'>Login inconnu.</td>\n";
						/*
						echo "<td>X</td>\n";
						echo "<td>X</td>\n";
						echo "<td>X</td>\n";
						*/
						if(is_file($chemin_temp."/".$file)) {
							echo "<td>Fichier</td>";
							echo "<td>";
							unset($tab_file_tmp);
							$tab_file_tmp=stat($chemin_temp."/".$file);
							//echo "Fichier: ".$tab_file_tmp[0]." soit ".$tab_file_tmp[7];
							echo volume_human($tab_file_tmp[7]);
							echo "</td>\n";
						}
						else{
							echo "<td>Dossier</td>";
							echo "<td>";
							//echo "Dossier: ".disk_total_space($chemin_temp."/".$file);
							echo volume_dir_human($chemin_temp."/".$file);
							echo "</td>\n";
						}
						echo "<td style='text-align:center;'>";
						echo "<input type='checkbox' id='case$cpt' name='suppr[]' value='$file' />";
						echo "</td>\n";
						/*
						echo "<td style='text-align:center;'>";
						echo "-";
						echo "</td>\n";
						*/
					}
					else{
						$ligtmp=mysql_fetch_object($res_user);
						echo "<td>$tabtmp[0]</td>\n";
						echo "<td>$ligtmp->nom</td>\n";
						echo "<td>$ligtmp->prenom</td>\n";
						echo "<td>$ligtmp->statut</td>\n";
						echo "<td>$ligtmp->etat</td>\n";
						if(is_file($chemin_temp."/".$file)) {
							echo "<td>Fichier</td>";
							echo "<td>";
							unset($tab_file_tmp);
							$tab_file_tmp=stat($chemin_temp."/".$file);
							echo volume_human($tab_file_tmp[7]);
							//echo $tab_file_tmp[0]." soit ".$tab_file_tmp[7];
							echo "</td>\n";
						}
						else{
							echo "<td>Dossier</td>";
							echo "<td>";
							//echo disk_total_space($chemin_temp."/".$file);
							//$totalsize=0;
							//echo volume_dir($chemin_temp."/".$file);
							echo volume_dir_human($chemin_temp."/".$file);
							echo "</td>\n";
						}
						echo "<td style='text-align:center;'>";
						echo "<input type='checkbox' id='case$cpt' name='suppr[]' value='$file' />";
						echo "</td>\n";
						/*
						echo "<td style='text-align:center;'>";
						echo "<input type='checkbox' name='reinit[]' value='$tabtmp[0]' />";
						echo "</td>\n";
						*/
					}
				}
			}
			echo "</tr>\n";
			$cpt++;
		}
	}
	echo "</table>\n";

	echo "<input type='checkbox' id='sans_exclusion' name='sans_exclusion' value='y' /><label for='sans_exclusion'> Supprimer aussi les sous-dossiers et sans exclure certains dossiers/fichiers protégés (<em>fichiers de signature des bulletins,...</em>)</label><br />\n";
	echo "<input type='hidden' name=is_posted value = '1' />\n";
	echo "<center><input type='submit' name='Valider' value='Valider' /></center>\n";
	echo "</form>\n";


	$nombreligne=$cpt;
	echo "<script type='text/javascript' language='javascript'>
	function modif_case(statut){
		// statut: true ou false
		for(k=0;k<$nombreligne;k++){
			if(document.getElementById('case'+k)){
				document.getElementById('case'+k).checked=statut;
			}
		}
		changement();
	}
</script>\n";


	if($bizarre>0){
		echo "<p><i>NOTE:</i> ";
		if($bizarre==1){
			echo "Un fichier/dossier a un nom inattendu.<br />Par précaution, on ne propose pas de le supprimer.";
		}
		else{
			echo "Des fichiers/dossiers ont des noms inattendus.<br />Par précaution, on ne propose pas de les supprimer.";
		}
		echo "</p>";
	}

	echo "</div>\n";
}
require("../lib/footer.inc.php");
?>
