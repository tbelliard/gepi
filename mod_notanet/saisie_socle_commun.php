<?php
/*
* $Id$
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Laurent Viénot-Hauger
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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


// INSERT INTO droits VALUES('/mod_notanet/saisie_socle_commun.php','V','F','F','V','F','F','F','F','Notanet: Saisie socle commun','');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}



$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$msg="";

if (isset($_POST['is_posted'])) {
	check_token();

	$pb_record="no";

	$ele_login=isset($_POST["ele_login"]) ? $_POST["ele_login"] : NULL;

	$socle_commun=isset($_POST["socle_commun"]) ? $_POST["socle_commun"] : NULL;

	for($i=0;$i<count($ele_login);$i++) {
		// Vérifier si l'élève est bien dans la classe?
		// Inutile si seul l'admin accède et qu'on ne limite pas l'accès à telle ou telle classe

		if(isset($socle_commun[$i])) {

			$sql2="";
			$sql3="";
			$maj_notanet="n";
			$sql="SELECT 1=1 FROM notanet WHERE login='".$ele_login[$i]."';";
			$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($test)>0) {
				$maj_notanet="y";
			}

			$sql="DELETE FROM notanet_socle_commun WHERE login='".$ele_login[$i]."' AND champ='116';";
			$nettoyage=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

			$sql="INSERT INTO notanet_socle_commun SET login='".$ele_login[$i]."', champ='116', valeur='".$socle_commun[$i]."';";
			//$sql2="UPDATE notanet SET note='".$socle_commun[$i]."', note_notanet='116'";
			//$sql2.=" WHERE login='".$ele_login[$i]."' AND notanet_mat='SOCLE COMMUN';";

			//echo "$sql<br />";
			$register=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if (!$register) {
				$msg .= "Erreur lors de l'enregistrement des données pour $ele_login[$i]<br />";
				//echo "ERREUR<br />";
				$pb_record = 'yes';
			}

			/*
			if($maj_notanet=='y') {
				// On met à jour la table notanet avec les corrections apportées sur notanet_socles
				$register=mysql_query($sql2);
			}
			*/
		}
	}

	if ($pb_record == 'no') {
		//$affiche_message = 'yes';
		$msg="Les modifications ont été enregistrées !";
	}
}
elseif((isset($_POST['action']))&&($_POST['action']=='upload_file')) {
	check_token();

	$xml_file = isset($_FILES["xml_file"]) ? $_FILES["xml_file"] : NULL;
	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
	if(isset($xml_file)) {

		$tempdir=get_user_temp_directory();
		$source_file=$xml_file['tmp_name'];

		if(!file_exists($source_file)) {
			$msg="L'upload du fichier XML semble avoir échoué.<br />\n";
		}
		else {
			$dest_file="../temp/".$tempdir."/notanet_socle.xml";
			if(file_exists($dest_file)) {
				unlink($dest_file);
			}
			$res_copy=copy("$source_file" , "$dest_file");

			$lpc_xml=simplexml_load_file($dest_file);
			if(!$lpc_xml) {
				$msg="ECHEC du chargement du fichier avec simpleXML.<br />\n";
			}
			else {
				$nom_racine=$lpc_xml->getName();
				if(my_strtoupper($nom_racine)!='SOCLE') {
					$msg="ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML LPC.<br />Sa racine devrait être 'SOCLE' et en l'occurence, c'est '$nom_racine'.<br />\n";
				}
				else {
					$nb_reg=0;
					$objet_donnees=($lpc_xml->donnees);
					foreach ($objet_donnees->children() as $key => $value) {
						//echo "$key->$value<br />";
						$ligne = $value;
						if(trim($ligne)!="") {
							$tab=explode("|",trim($ligne));
							if((isset($tab[0]))&&($tab[0]!='')&&(isset($tab[1]))&&($tab[1]!='')&&(isset($tab[2]))&&($tab[2]!='')) {
								$sql="SELECT DISTINCT login FROM notanet WHERE ine='$tab[0]';";
								//echo "$sql<br />\n";
								$res_login=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
								if(mysqli_num_rows($res_login)==1) {
									$lig=mysqli_fetch_object($res_login);
									$sql="DELETE FROM notanet_socle_commun WHERE login='$lig->login' AND champ='$tab[1]';";
									//echo "$sql<br />";
									$nettoyage=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

									$sql="INSERT INTO notanet_socle_commun SET login='$lig->login', champ='$tab[1]', valeur='$tab[2]';";
									//echo "$sql<br />";
									$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									if($insert) {$nb_reg++;} else {$msg.="Erreur sur la requête $sql<br />";}
								}
								else {
									$info_supplementaire="";
									$sql="SELECT DISTINCT nom, prenom, classe FROM eleves e, j_eleves_classes jec, classes c WHERE e.login=jec.login AND jec.id_classe=c.id AND e.no_gep='$tab[0]';";
									$res_ele_clas=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									if(mysqli_num_rows($res_ele_clas)==1) {
										$lig_ele_clas=mysqli_fetch_object($res_ele_clas);
										$info_supplementaire=" (<em>$lig_ele_clas->nom $lig_ele_clas->prenom ($lig_ele_clas->classe)</em>)";
									}
									$msg.="Ligne non identifiée : ".$ligne.$info_supplementaire."<br />";
									//$msg.="$sql<br />\n";
								}
							}
						}
					}
					if($nb_reg>0) {$msg.="$nb_reg enregistrement(s) effectué(s).<br />";}
				}
			}
		}
	}
	elseif(isset($csv_file)) {
		$fp=fopen($csv_file['tmp_name'],"r");

		if(!$fp) {
			$msg.="Impossible d'ouvrir le fichier CSV !<br />";
		} else {
			//$k = 0;
			$nb_reg=0;
			while (!feof($fp)) {
				$ligne = fgets($fp, 4096);
				if(trim($ligne)!="") {
					$tab=explode("|",trim($ligne));
					if((isset($tab[0]))&&($tab[0]!='')&&(isset($tab[1]))&&($tab[1]!='')&&(isset($tab[2]))&&($tab[2]!='')) {
						$sql="SELECT DISTINCT login FROM notanet WHERE ine='$tab[0]';";
						//echo "$sql<br />\n";
						$res_login=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_login)==1) {
							$lig=mysqli_fetch_object($res_login);
							$sql="DELETE FROM notanet_socle_commun WHERE login='$lig->login' AND champ='$tab[1]';";
							//echo "$sql<br />";
							$nettoyage=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

							$sql="INSERT INTO notanet_socle_commun SET login='$lig->login', champ='$tab[1]', valeur='$tab[2]';";
							//echo "$sql<br />";
							$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
							if($insert) {$nb_reg++;} else {$msg.="Erreur sur la requête $sql<br />";}
						}
						else {
							$info_supplementaire="";
							$sql="SELECT DISTINCT nom, prenom, classe FROM eleves e, j_eleves_classes jec, classes c WHERE e.login=jec.login AND jec.id_classe=c.id AND e.no_gep='$tab[0]';";
							$res_ele_clas=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
							if(mysqli_num_rows($res_ele_clas)==1) {
								$lig_ele_clas=mysqli_fetch_object($res_ele_clas);
								$info_supplementaire=" (<em>$lig_ele_clas->nom $lig_ele_clas->prenom ($lig_ele_clas->classe)</em>)";
							}
							$msg.="Ligne non identifiée : ".$ligne.$info_supplementaire."<br />";
							//$msg.="$sql<br />\n";
						}
					}
				}
			}
			if($nb_reg>0) {$msg.="$nb_reg enregistrement(s) effectué(s).<br />";}
		}
	}
}

$themessage = 'Des modifications ont été effectuées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";

//**************** EN-TETE *****************
$titre_page = "Notanet | Saisie Socle commun";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

$tmp_timeout=(getSettingValue("sessionMaxLength"))*60;

if((isset($_POST['temoin_suhosin_1']))&&(!isset($_POST['temoin_suhosin_2']))) {
	echo "<p style='color:red; font-weight:bold; text-align:center;'>Il semble que certaines variables n'ont pas été transmises.<br />Cela peut arriver lorsqu'on tente de transmettre trop de variables.<br />Vous devriez opter pour un autre mode d'extraction.</p>\n";
	echo "<div style='margin-left:3em; background-image: url(\"../images/background/opacite50.png\");'>";
	echo alerte_config_suhosin();
	echo "</div>\n";
	echo "<p><br /></p>\n";
}

?>
<script type="text/javascript" language="javascript">
change = 'no';
</script>

<p class="bold"><a href="../accueil.php" onclick="return confirm_abandon(this, change, '<?php echo $themessage; ?>')"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>

<?php

echo " | <a href='index.php'>Accueil Notanet</a>";
echo " | <a href='".$_SERVER['PHP_SELF']."?mode=import_csv'>Importer un CSV</a>\n";
echo " | <a href='".$_SERVER['PHP_SELF']."?mode=import_xml'>Importer un XML</a>\n";

if((isset($mode))&&($mode=='import_csv')) {
	echo "</p>\n";

	echo "<p>L'application nationale LPC permet d'exporter les saisies effectuées.<br />\n";
	echo "En 2011, le fichier était au format CSV.<br />\n";
	echo "Il semble depuis être passé au <a href='".$_SERVER['PHP_SELF']."?mode=import_xml'>format XML</a>.<br />\n";
	echo "Pour obtenir ce CSV, sur l'application LPC, il faut \"confirmer\" la maîtrise pour les élèves, puis effectuer la procédure d'export vers NOTANET.</p>\n";

	echo "<p>Veuillez fournir le fichier&nbsp;:</p>\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />\n";
	echo "<p><input type='submit' value='Valider' />\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";
	echo "<p><i>NOTES</i>&nbsp;:</p>
<ul>
	<li><p>L'extraction des moyennes doit avoir été effectuée avant l'import.<br />Les élèves pour lesquels l'extraction n'a pas été faite, mais pour lesquels la saisie LPC a été effectuée risquent d'apparaître en erreur (<em>il n'y a pas lieu de s'alarmer, mais il faudra sans doute s'occuper de ces élèves à un moment</em>).</p></li>\n";
}
elseif((isset($mode))&&($mode=='import_xml')) {
	echo "</p>\n";

	echo "<p>L'application nationale LPC permet d'exporter les saisies effectuées.<br />";
	echo "Pour obtenir ce XML, sur l'application LPC, il faut \"confirmer\" la maîtrise pour les élèves, puis dans le menu Administration, effectuer la procédure d'export vers NOTANET.</p>\n";

	echo "<p>Veuillez fournir le fichier&nbsp;:</p>\n";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"xml_file\" />\n";
	echo "<p><input type='submit' value='Valider' />\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";
	echo "<p><i>NOTES</i>&nbsp;:</p>
<ul>
	<li><p>L'extraction des moyennes doit avoir été effectuée avant l'import.<br />Les élèves pour lesquels l'extraction n'a pas été faite, mais pour lesquels la saisie LPC a été effectuée risquent d'apparaître en erreur (<em>il n'y a pas lieu de s'alarmer, mais il faudra sans doute s'occuper de ces élèves à un moment</em>).</p></li>\n";
}
elseif(!isset($id_classe)) {
	echo "</p>\n";
	//$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, notanet n,notanet_ele_type net WHERE p.id_classe = c.id AND c.id=n.id_classe ORDER BY classe;";
	$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_eleves_classes jec, notanet_ele_type net WHERE p.id_classe = c.id AND c.id=jec.id_classe AND jec.login=net.login ORDER BY classe;";
	$call_classes=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

	$nb_classes=mysqli_num_rows($call_classes);
	if($nb_classes==0){
		echo "<p>Aucune classe ne semble encore définie.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}
	else{
		// Choix de la classe...
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

		// Affichage sur 3 colonnes
		$nb_classes_par_colonne=round($nb_classes/2);

		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";

		$cpt_i = 0;

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while($lig_clas=mysqli_fetch_object($call_classes)) {

			//affichage 2 colonnes
			if(($cpt_i>0)&&(round($cpt_i/$nb_classes_par_colonne)==$cpt_i/$nb_classes_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}

			echo "<input type='checkbox' name='id_classe[]' id='id_classe_".$cpt_i."' value='$lig_clas->id' />";
			echo "<label for='id_classe_".$cpt_i."' style='cursor: pointer;'>";
			echo "$lig_clas->classe</label>";
			echo "<br />\n";
			$cpt_i++;
		}

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
		echo "</form>\n";
	}
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a>\n";
	echo "</p>\n";

	$chaine_classes="";
	for($loop=0;$loop<count($id_classe);$loop++) {
		if($loop>0) {
			$chaine_classes.="&amp;";
		}
		$chaine_classes.="id_classe[$loop]=".$id_classe[$loop];
	}

	if(!isset($mode)) {
		echo "<p class='bold'>Mode simple</p>\n";
		echo "<p>Seule la validation ou non du socle commun est prise en compte.<br />Pour saisir compétence par compétence les validations, utiliser le <a href='".$_SERVER['PHP_SELF']."?mode=detail&amp;$chaine_classes'>mode détaillé</a>.</p>\n";
	
		echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
		echo add_token_field();

		echo "<input type='hidden' name='temoin_suhosin_1' value='y' />\n";

		$tabdiv_infobulle[]=creer_div_infobulle('MS',"","","<center>Socle ou compétence validée</center>","",10,0,'y','y','n','n');
		$tabdiv_infobulle[]=creer_div_infobulle('ME',"","","<center>Socle ou compétence non validée</center>","",12,0,'y','y','n','n');
		$tabdiv_infobulle[]=creer_div_infobulle('MN',"","","<center>Socle ou compétence non évaluée</center>","",10,0,'y','y','n','n');
		$tabdiv_infobulle[]=creer_div_infobulle('vider',"","","<center>Supprimer l'enregistrement existant pour effectuer la saisie plus tard</center>","",12,0,'y','y','n','n');
	
		//$tabdiv_infobulle[]=creer_div_infobulle('MN',"","","<center>Maîtrise du socle non évaluée</center>","",10,0,'y','y','n','n');
		//$tabdiv_infobulle[]=creer_div_infobulle('AB',"","","<center>Absent</center>","",8,0,'y','y','n','n');
	
		$cpt=0;
		for($i=0;$i<count($id_classe);$i++) {
	
			echo "<p>Classe de <b>".get_class_from_id($id_classe[$i])."</b><br />\n";
			echo "<input type='hidden' name='id_classe[$i]' value='".$id_classe[$i]."' />\n";
	
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE (jec.id_classe='".$id_classe[$i]."' AND jec.login=e.login) ORDER BY e.nom,e.prenom,e.naissance;";
			$res_ele=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($res_ele)==0) {
				echo "Aucun élève dans cette classe.</p>\n";
			}
			else {
				echo "<table class='boireaus' border='1' summary='Saisie socle commun'>\n";
	
				echo "<tr>\n";
				echo "<th rowspan='3'>Elève</th>\n";
				echo "<th colspan='4'>Socle commun</th>\n";
				echo "</tr>\n";
	
				echo "<tr>\n";
				echo "<th>";
				echo "<a href='#' onmouseover=\"afficher_div('MS','y',-20,20);\"";
				echo " onmouseout=\"cacher_div('MS')\" onclick=\"return false;\"";
				echo ">";
				echo "MS";
				echo "</a>\n";
				echo "</th>\n";
	
				echo "<th>";
				echo "<a href='#' onmouseover=\"afficher_div('ME','y',-20,20);\"";
				echo " onmouseout=\"cacher_div('ME')\" onclick=\"return false;\"";
				echo ">";
				echo "ME";
				echo "</a>\n";
				echo "</th>\n";
	
				echo "<th>";
				echo "<a href='#' onmouseover=\"afficher_div('MN','y',-20,20);\"";
				echo " onmouseout=\"cacher_div('MN')\" onclick=\"return false;\"";
				echo ">";
				echo "MN";
				echo "</a>\n";
				echo "</th>\n";
	
				echo "<th>";
				echo "<a href='#' onmouseover=\"afficher_div('vider','y',-20,20);\"";
				echo " onmouseout=\"cacher_div('vider')\" onclick=\"return false;\"";
				echo ">";
				echo "Vider";
				echo "</a>\n";
				echo "</th>\n";

				echo "</tr>\n";

				//=========================
	
				echo "<tr>\n";
	
				echo "<th>";
				echo "<a href=\"javascript:CocheColonne('MS_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('MS_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				echo "</th>\n";
	
				echo "<th>";
				echo "<a href=\"javascript:CocheColonne('ME_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('ME_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				echo "</th>\n";
	
				echo "<th>";
				echo "<a href=\"javascript:CocheColonne('MN_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('MN_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				echo "</th>\n";
	
				echo "<th>";
				echo "<a href=\"javascript:CocheColonne('vider_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('vider_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				echo "</th>\n";
	
				echo "</tr>\n";
	
	
				$alt=1;
				while($lig_ele=mysqli_fetch_object($res_ele)) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'>\n";
					echo "<td>";
					echo "<input type='hidden' name='ele_login[$cpt]' value=\"".$lig_ele->login."\" />\n";
					echo $lig_ele->nom." ".$lig_ele->prenom;
					echo "</td>\n";
	
					$sql="SELECT * FROM notanet_socle_commun WHERE login='".$lig_ele->login."' AND champ='116';";
					$res_socle=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res_socle)==0) {
						$def_socle="";
					}
					else {
						$lig_soc=mysqli_fetch_object($res_socle);
						$def_socle=$lig_soc->valeur;
					}
	
					echo "<td><input type='radio' name='socle_commun[$cpt]' id='MS_".$cpt."_".$i."' value='MS' onchange='changement();' ";
					if($def_socle=='MS') {echo "checked ";}
					echo "title='$lig_ele->login Socle commun -&gt; MS' ";
					echo "/></td>\n";
	
					echo "<td><input type='radio' name='socle_commun[$cpt]' id='ME_".$cpt."_".$i."' value='ME' onchange='changement();' ";
					if($def_socle=='ME') {echo "checked ";}
					echo "title='$lig_ele->login Socle commun -&gt; ME' ";
					echo "/></td>\n";
	
					echo "<td><input type='radio' name='socle_commun[$cpt]' id='MN_".$cpt."_".$i."' value='MN' onchange='changement();' ";
					if($def_socle=='MN') {echo "checked ";}
					echo "title='$lig_ele->login Socle commun -&gt; MN' ";
					echo "/></td>\n";
	
					echo "<td><input type='radio' name='socle_commun[$cpt]' id='vider_".$cpt."_".$i."' value='' onchange='changement();' ";
					if($def_socle=='') {echo "checked ";}
					echo "title='$lig_ele->login Socle commun -&gt; Vider' ";
					echo "/></td>\n";
	
					echo "</tr>\n";
					$cpt++;
				}
	
				echo "</table>\n";
	
				echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
			}
		}

		echo "<input type='hidden' name='is_posted' value='y' />\n";
		//echo "<p align='center'><input type='submit' value='Valider' /></p>\n";

		echo "<input type='hidden' name='temoin_suhosin_2' value='y' />\n";
		echo "</form>\n";

	}
	else {
		echo "<p class='bold'>Mode détaillé</p>\n";
		echo "<p>Pour seulement enregistrer la validation ou non du socle commun utiliser le <a href='".$_SERVER['PHP_SELF']."?$chaine_classes'>mode simple</a>.</p>\n";

		echo "<p style='color:red'>Mode détaillé encore à implémenter...</p>\n";

		$cpt=0;

		/*
		echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
		echo add_token_field();
	
		$tabdiv_infobulle[]=creer_div_infobulle('MS',"","","<center>Socle ou compétence validée</center>","",10,0,'y','y','n','n');
		$tabdiv_infobulle[]=creer_div_infobulle('ME',"","","<center>Socle ou compétence non validée</center>","",12,0,'y','y','n','n');
		$tabdiv_infobulle[]=creer_div_infobulle('MN',"","","<center>Socle ou compétence non évaluée</center>","",10,0,'y','y','n','n');
		$tabdiv_infobulle[]=creer_div_infobulle('vider',"","","<center>Supprimer l'enregistrement existant pour effectuer la saisie plus tard</center>","",12,0,'y','y','n','n');
	
		//$tabdiv_infobulle[]=creer_div_infobulle('MN',"","","<center>Maîtrise du socle non évaluée</center>","",10,0,'y','y','n','n');
		//$tabdiv_infobulle[]=creer_div_infobulle('AB',"","","<center>Absent</center>","",8,0,'y','y','n','n');
	
		$cpt=0;
		for($i=0;$i<count($id_classe);$i++) {
	
			echo "<p>Classe de <b>".get_class_from_id($id_classe[$i])."</b><br />\n";
			echo "<input type='hidden' name='id_classe[$i]' value='".$id_classe[$i]."' />\n";
	
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE (jec.id_classe='".$id_classe[$i]."' AND jec.login=e.login) ORDER BY e.nom,e.prenom,e.naissance;";
			$res_ele=mysql_query($sql);
			if(mysql_num_rows($res_ele)==0) {
				echo "Aucun élève dans cette classe.</p>\n";
			}
			else {
				echo "<table class='boireaus' border='1' summary='Saisie socle commun'>\n";
	
				echo "<tr>\n";
				echo "<th rowspan='3'>Elève</th>\n";
				echo "<th colspan='4'>Socle commun</th>\n";
				echo "</tr>\n";
	
				echo "<tr>\n";
				echo "<th>";
				echo "<a href='#' onmouseover=\"afficher_div('MS','y',-20,20);\"";
				echo " onmouseout=\"cacher_div('MS')\" onclick=\"return false;\"";
				echo ">";
				echo "MS";
				echo "</a>\n";
				echo "</th>\n";
	
				echo "<th>";
				echo "<a href='#' onmouseover=\"afficher_div('ME','y',-20,20);\"";
				echo " onmouseout=\"cacher_div('ME')\" onclick=\"return false;\"";
				echo ">";
				echo "ME";
				echo "</a>\n";
				echo "</th>\n";
	
				echo "<th>";
				echo "<a href='#' onmouseover=\"afficher_div('MN','y',-20,20);\"";
				echo " onmouseout=\"cacher_div('MN')\" onclick=\"return false;\"";
				echo ">";
				echo "MN";
				echo "</a>\n";
				echo "</th>\n";
	
				echo "<th>";
				echo "<a href='#' onmouseover=\"afficher_div('vider','y',-20,20);\"";
				echo " onmouseout=\"cacher_div('vider')\" onclick=\"return false;\"";
				echo ">";
				echo "Vider";
				echo "</a>\n";
				echo "</th>\n";

				echo "</tr>\n";

				//===================

				echo "<tr>\n";
	
				echo "<th>";
				echo "<a href=\"javascript:CocheColonne('MS_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('MS_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				echo "</th>\n";
	
				echo "<th>";
				echo "<a href=\"javascript:CocheColonne('ME_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('ME_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				echo "</th>\n";
	
				echo "<th>";
				echo "<a href=\"javascript:CocheColonne('MN_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('MN_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				echo "</th>\n";
	
				echo "<th>";
				echo "<a href=\"javascript:CocheColonne('vider_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('vider_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				echo "</th>\n";
	
				echo "</tr>\n";
	
	
				$alt=1;
				while($lig_ele=mysql_fetch_object($res_ele)) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'>\n";
					echo "<td>";
					echo "<input type='hidden' name='ele_login[$cpt]' value=\"".$lig_ele->login."\" />\n";
					echo $lig_ele->nom." ".$lig_ele->prenom;
					echo "</td>\n";
	
					$sql="SELECT * FROM notanet_socle_commun WHERE login='".$lig_ele->login."' AND champ='116';";
					$res_socle=mysql_query($sql);
					if(mysql_num_rows($res_socle)==0) {
						$def_socle="";
					}
					else {
						$lig_soc=mysql_fetch_object($res_socle);
						$def_socle=$lig_soc->valeur;
					}
	
					echo "<td><input type='radio' name='socle_commun[$cpt]' id='MS_".$cpt."_".$i."' value='MS' onchange='changement();' ";
					if($def_socle=='MS') {echo "checked ";}
					echo "title='$lig_ele->login Socle commun -&gt; MS' ";
					echo "/></td>\n";
	
					echo "<td><input type='radio' name='socle_commun[$cpt]' id='ME_".$cpt."_".$i."' value='ME' onchange='changement();' ";
					if($def_socle=='ME') {echo "checked ";}
					echo "title='$lig_ele->login Socle commun -&gt; ME' ";
					echo "/></td>\n";
	
					echo "<td><input type='radio' name='socle_commun[$cpt]' id='MN_".$cpt."_".$i."' value='MN' onchange='changement();' ";
					if($def_socle=='MN') {echo "checked ";}
					echo "title='$lig_ele->login Socle commun -&gt; MN' ";
					echo "/></td>\n";
	
					echo "<td><input type='radio' name='socle_commun[$cpt]' id='vider_".$cpt."_".$i."' value='' onchange='changement();' ";
					if($def_socle=='') {echo "checked ";}
					echo "title='$lig_ele->login Socle commun -&gt; Vider' ";
					echo "/></td>\n";
	
					echo "</tr>\n";
					$cpt++;
				}
	
				echo "</table>\n";
	
				echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
			}
		}
		echo "<input type='hidden' name='mode' value='detail' />\n";


		echo "<input type='hidden' name='is_posted' value='y' />\n";
		//echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
		echo "</form>\n";

		*/
	}


	echo "<script type='text/javascript'>

function CocheColonne(nom_col,num_classe) {
	for (var ki=0;ki<$cpt;ki++) {
		if(document.getElementById(nom_col+ki+'_'+num_classe)){
			document.getElementById(nom_col+ki+'_'+num_classe).checked = true;
		}
	}
}

function DecocheColonne(nom_col,num_classe) {
	for (var ki=0;ki<$cpt;ki++) {
		if(document.getElementById(nom_col+ki+'_'+num_classe)){
			document.getElementById(nom_col+ki+'_'+num_classe).checked = false;
		}
	}
}

</script>
";

}

if((isset($mode))&&(($mode=='import_xml')||($mode=='import_csv'))) {
	// Le début de la liste des notes de bas de page a déjà été affiché
}
else {
	echo "<p><br /></p>\n";
	echo "<p><i>NOTES</i>&nbsp;:</p>
<ul>\n";
}
echo "<li>Voir <a href='https://www.sylogix.org/projects/gepi/wiki/Gepi_socle_commun_notanet' target='_blank'>https://www.sylogix.org/projects/gepi/wiki/Gepi_socle_commun_notanet</a></li>
<li>Dans l'interface Notanet (<em>hors de Gepi</em>), il semble qu'il faille fournir l'export CSV des notes de Gepi et aussi l'export XML de LPC.<br />
Cela se fait avec le même choix 'Mettre à jour depuis un fichier externe' dans Notanet.</li>
<li>Les élèves qui ont eu leur brevet l'an dernier, mais ont néanmoins redoublé, peuvent apparaître en erreur.<br />
De la même façon, les élèves de SEGPA peuvent avoir la saisie de Socle commun faite dans LPC, mais ils ne passent pas le brevet.<br />
Ils peuvent apparaître en erreur également.</li>
</ul>\n";
require("../lib/footer.inc.php");
die();
?>
