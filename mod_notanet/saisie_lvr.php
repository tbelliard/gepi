<?php
/*
* $Id: saisie_lvr.php 5984 2010-11-24 14:54:46Z crob $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Laurent Viénot-Hauger, Stephane Boireau
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


//==============================================
/* Ajout des droits dans la table droits */
$sql="SELECT 1=1 FROM droits WHERE id='/mod_notanet/saisie_lvr.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_notanet/saisie_lvr.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Notanet: Saisie des notes de Langue Vivante Regionale',
statut='';";
$insert=mysql_query($sql);
}
//==============================================

// INSERT INTO droits VALUES('/mod_notanet/saisie_lvr.php','V','F','F','V','F','F','F','F','Notanet: Saisie des notes de Langue Vivante Regionale','');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$msg="";

if((isset($_POST['is_posted']))&&(isset($mode))) {
	check_token();

	$pb_record="no";

	if($mode=='set_lvr') {
		$lvr=isset($_POST['lvr']) ? $_POST['lvr'] : NULL;
		$nouvelle_lvr=isset($_POST['nouvelle_lvr']) ? $_POST['nouvelle_lvr'] : NULL;
		$suppr_lvr=isset($_POST['suppr_lvr']) ? $_POST['suppr_lvr'] : NULL;

		if(isset($lvr)) {
			foreach($lvr as $key => $value) {
				$sql="UPDATE notanet_lvr SET intitule='$value' WHERE id='$key';";
				$update=mysql_query($sql);
				if(!$update) {$msg.="Erreur lors de la mise à jour de la LVR $value<br />";$pb_record='y';}
			}
		}

		if(isset($suppr_lvr)) {
			foreach($suppr_lvr as $key => $value) {
				$sql="SELECT 1=1 FROM notanet_lvr_ele WHERE id_lvr='$value';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0) {
					$sql="DELETE FROM notanet_lvr WHERE id='$value';";
					$del=mysql_query($sql);
					if(!$del) {$msg.="Erreur lors de la suppression de la LVR n°$value<br />";$pb_record='y';}
				}
				else {
					$msg.="La LVR n°$value est associée à ".mysql_num_rows($test)." élève(s).<br />";
					$pb_record='y';
				}
			}
		}

		if((isset($nouvelle_lvr))&&($nouvelle_lvr!='')) {
			$ajouter_lvr='y';
			if((isset($lvr))&&(in_array($nouvelle_lvr,$lvr))) {
				$ajouter_lvr='n';
			}

			if($ajouter_lvr=='y') {
				$sql="INSERT INTO notanet_lvr SET intitule='$nouvelle_lvr';";
				$insert=mysql_query($sql);
				if(!$insert) {$msg.="Erreur lors de l'ajout de la LVR $nouvelle_lvr<br />";$pb_record='y';}
			}
		}
	}
	elseif($mode=='select_eleves') {
		$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : array();
		$lvr=isset($_POST['lvr']) ? $_POST['lvr'] : array();

		$sql="SELECT DISTINCT id,intitule FROM notanet_lvr ORDER BY intitule;";
		//echo "<p>$sql<br />";
		$res_lvr=mysql_query($sql);
		while($lig_lvr=mysql_fetch_object($res_lvr)) {
			$tab_id_lvr[]=$lig_lvr->id;
		}

		for($i=0;$i<count($lvr);$i++) {
			if(isset($lvr[$i])) {
				if($lvr[$i]=='') {
					$sql="DELETE FROM notanet_lvr_ele WHERE login='$login_ele[$i]';";
					//echo "$sql<br />";
					$del=mysql_query($sql);
					if(!$del) {$msg.="Erreur lors de la suppression pour $login_ele[$i]<br />";$pb_record='y';}
				}
				elseif(in_array($lvr[$i],$tab_id_lvr)) {
					$sql="DELETE FROM notanet_lvr_ele WHERE login='$login_ele[$i]';";
					//echo "$sql<br />";
					$del=mysql_query($sql);
					if(!$del) {$msg.="Erreur lors de la réinitialisation pour $login_ele[$i]<br />";$pb_record='y';}
					else {
						$sql="INSERT INTO notanet_lvr_ele SET login='$login_ele[$i]', id_lvr='$lvr[$i]';";
						//echo "$sql<br />";
						$insert=mysql_query($sql);
						if(!$insert) {$msg.="Erreur lors de l'enregistrement $login_ele[$i]<br />";$pb_record='y';}
					}
				}
			}
		}

	}
	elseif($mode=='saisie') {
		$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : array();
		//$lvr=isset($_POST['lvr']) ? $_POST['lvr'] : array();
		$note_lvr=isset($_POST['note_lvr']) ? $_POST['note_lvr'] : array();

		$tab_valeurs_autorisees=array('','VA','NV');

		for($i=0;$i<count($note_lvr);$i++) {
			if((isset($note_lvr[$i]))&&(in_array($note_lvr[$i],$tab_valeurs_autorisees))) {
				//$sql="UPDATE notanet_lvr_ele SET note_lvr='' WHERE login='$login_ele[$i]' AND id_lvr='$lvr[$i]';";
				$sql="UPDATE notanet_lvr_ele SET note='$note_lvr[$i]' WHERE login='$login_ele[$i]';";
				//echo "$sql<br />";
				$update=mysql_query($sql);
				if(!$update) {$msg.="Erreur lors de l'enregistrement $login_ele[$i]<br />";$pb_record='y';}
			}
		}
	}

	if ($pb_record == 'no') {
		//$affiche_message = 'yes';
		$msg="Les modifications ont été enregistrées !";
	}
}


$themessage = 'Des modifications ont été effectuées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";

//**************** EN-TETE *****************
$titre_page = "Notanet | Saisie des notes de LVR";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

$tmp_timeout=(getSettingValue("sessionMaxLength"))*60;

$sql="CREATE TABLE IF NOT EXISTS notanet_lvr (
id int(11) NOT NULL auto_increment,
intitule VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( id )
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_lvr_ele (
id int(11) NOT NULL auto_increment,
login VARCHAR( 255 ) NOT NULL ,
id_lvr INT( 11 ) NOT NULL ,
note ENUM ('', 'VA','NV') NOT NULL DEFAULT '',
PRIMARY KEY ( id )
);";
$create_table=mysql_query($sql);


?>
<script type="text/javascript" language="javascript">
change = 'no';
</script>

<p class=bold><a href="../accueil.php" onclick="return confirm_abandon(this, change, '<?php echo $themessage; ?>')"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>

<?php

echo " | <a href='index.php'>Accueil Notanet</a>";

if(!isset($mode)) {
	echo "</p>\n";

	echo "<p>Choisissez&nbsp;:</p>\n";
	echo "<ul>\n";
	echo "<li>\n";
	echo "<a href='".$_SERVER['PHP_SELF']."?mode=set_lvr'>Définir les langues régionales</a>";
	echo "</li>\n";

	$sql="SELECT * FROM notanet_lvr ORDER BY intitule;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		echo "<li>\n";
		echo "<a href='".$_SERVER['PHP_SELF']."?mode=select_eleves'>Sélectionner les élèves suivant une Langue Vivante Régionale</a>";
		echo "<br />\n";
		echo "Les élèves peuvent choisir de ne pas faire apparaître la Langue Vivante Régionale sur leur Fiche Brevet.<br />\n";
		echo "Dans ce cas, ne sélectionnez pas l'élève.";
		echo "</li>\n";
		//echo "<li>\n";
		//echo "<a href='".$_SERVER['PHP_SELF']."?mode=select_profs'>Sélectionner des professeurs...</a>";
		//echo "</li>\n";

		// On teste si des élèves ont été affectés dans des LVR...
		$sql="SELECT 1=1 FROM notanet_lvr_ele;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {

			echo "<li>\n";
			echo "<a href='".$_SERVER['PHP_SELF']."?mode=saisie'>Saisir les 'notes' pour une Langue Vivante Régionale</a>";
			echo "</li>\n";
		}
	}
	echo "</ul>\n";
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Menu Langue Vivante Régionale</a>\n";

	if($mode=='set_lvr') {
		echo "</p>\n";

		echo "<h2>Définition/saisie des LVR</h2>\n";

		//$cpt=0;
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
		echo add_token_field();
		$sql="SELECT * FROM notanet_lvr ORDER BY intitule;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			echo "<table class='boireaus' summary='Liste des LVR'>\n";
			echo "<tr>\n";
			echo "<th>Intitulé</th>\n";
			echo "<th>Supprimer</th>\n";
			echo "</tr>\n";
			$alt=1;
			while($lig=mysql_fetch_object($res)) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				echo "<td><input type='text' name='lvr[$lig->id]' value=\"$lig->intitule\" /></td>\n";
				echo "<td><input type='checkbox' name='suppr_lvr[]' value='$lig->id' /></td>\n";
				echo "</tr>\n";
				//$cpt++;
			}
			echo "</table>\n";
		}

		echo "<p>Ajouter une LVR&nbsp;: <input type='text' name='nouvelle_lvr' value=\"\" /></p>\n";

		echo "<input type='hidden' name='is_posted' value='1' />\n";
		echo "<input type='hidden' name='mode' value='$mode' />\n";
		echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
		echo "</form>\n";
	}
	elseif($mode=='select_eleves') {

		if(!isset($id_classe)) {
			echo "</p>\n";

			echo "<h2>Sélection des élèves pour les LVR</h2>\n";

			// Choisir une classe
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_eleves_classes jec, notanet_ele_type net WHERE p.id_classe = c.id AND c.id=jec.id_classe AND jec.login=net.login ORDER BY classe;";
			$call_classes=mysql_query($sql);
		
			$nb_classes=mysql_num_rows($call_classes);
			if($nb_classes==0) {
				echo "<p>Aucune classe ne semble encore définie.</p>\n";
		
				require("../lib/footer.inc.php");
				die();
			}
			else {
				// Choix de la classe...
				//echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
		
				// Affichage sur 3 colonnes
				$nb_classes_par_colonne=round($nb_classes/2);
		
				echo "<table width='100%' summary='Choix des classes'>\n";
				echo "<tr valign='top' align='center'>\n";
		
				$cpt_i = 0;
		
				echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
				echo "<td align='left'>\n";
		
				while($lig_clas=mysql_fetch_object($call_classes)) {
		
					//affichage 2 colonnes
					if(($cpt_i>0)&&(round($cpt_i/$nb_classes_par_colonne)==$cpt_i/$nb_classes_par_colonne)) {
						echo "</td>\n";
						echo "<td align='left'>\n";
					}

					//echo "<input type='checkbox' name='id_classe[]' id='id_classe_".$cpt_i."' value='$lig_clas->id' />";
					//echo "<label for='id_classe_".$cpt_i."' style='cursor: pointer;'>";
					//echo "$lig_clas->classe</label>";

					echo "<a href='".$_SERVER['PHP_SELF']."?mode=select_eleves&amp;id_classe=$lig_clas->id'>";
					echo "$lig_clas->classe</a>";

					echo "<br />\n";
					$cpt_i++;
				}
		
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
		
				//echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
				//echo "</form>\n";
			}
		}
		else {
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=select_eleves'>Choix de la classe</a>\n";
			echo "</p>\n";

			echo "<h2>Sélection des élèves pour les LVR</h2>\n";

			// Sélectionner des élèves ou s'appuyer sur un groupe

			$sql="SELECT DISTINCT e.login, e.prenom, e.nom FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND id_classe='$id_classe' ORDER BY e.nom, e.prenom";
			$res_ele=mysql_query($sql);
		
			$nb_ele=mysql_num_rows($res_ele);
			if($nb_ele==0) {

				echo "<p>Aucun élève dans la classe ".get_class_from_id($id_classe).".</p>\n";

				//echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=select_eleves'>Retour au choix de la classe</a></p>\n";

				require("../lib/footer.inc.php");
				die();
			}
			else {

				echo "<p class='bold'>Classe de ".get_class_from_id($id_classe).".</p>\n";

				echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
				echo add_token_field();

				echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
				echo "<input type='hidden' name='mode' value='select_eleves' />\n";
				echo "<input type='hidden' name='is_posted' value='1' />\n";

				$tab_lvr_ele=array();
				$sql="SELECT * FROM notanet_lvr_ele;";
				$res_lvr_ele=mysql_query($sql);
				while($lig_lvr_ele=mysql_fetch_object($res_lvr_ele)) {
					$tab_lvr_ele[$lig_lvr_ele->login]=$lig_lvr_ele->id_lvr;
				}

				echo "<table class='boireaus' summary='Choix des LVR des élèves'>\n";

				echo "<tr>\n";
				echo "<th align='left'>\n";
				echo "Elève\n";
				echo "</th>\n";
	
				echo "<th>\n";
				echo "Sans LVR\n";
				echo "</th>\n";

				$tab_id_lvr=array();
				$tab_intitule_lvr=array();
				$sql="SELECT DISTINCT id,intitule FROM notanet_lvr ORDER BY intitule;";
				$res_lvr=mysql_query($sql);
				while($lig_lvr=mysql_fetch_object($res_lvr)) {
					echo "<th>\n";
					echo $lig_lvr->intitule;
					$tab_intitule_lvr[]=$lig_lvr->intitule;
					$tab_id_lvr[]=$lig_lvr->id;
					echo "</th>\n";
				}
				echo "</tr>\n";

				$cpt=0;
				$alt=1;
				while($lig_ele=mysql_fetch_object($res_ele)) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'>\n";
					echo "<td align='left'>\n";
					echo "$lig_ele->nom $lig_ele->prenom\n";
					echo "<input type='hidden' name='login_ele[$cpt]' value=\"$lig_ele->login\" />\n";
					echo "</td>\n";
		
					echo "<td>\n";
					if(!isset($tab_lvr_ele[$lig_ele->login])) {$checked="checked ";} else {$checked="";}
					echo "<input type='radio' name='lvr[$cpt]' id='lvr_$cpt' value='' ";
					echo "title='$lig_ele->login -&gt; Sans LVR' ";
					echo "$checked/>\n";
					echo "</td>\n";

					for($i=0;$i<count($tab_id_lvr);$i++) {
						echo "<td>\n";
						if((isset($tab_lvr_ele[$lig_ele->login]))&&($tab_lvr_ele[$lig_ele->login]==$tab_id_lvr[$i])) {$checked="checked ";} else {$checked="";}
						echo "<input type='radio' name='lvr[$cpt]' id='lvr_$cpt' value='".$tab_id_lvr[$i]."' ";
						echo "title='$lig_ele->login -&gt; $tab_intitule_lvr[$i]' ";
						echo "$checked/>\n";
						echo "</td>\n";
					}
					echo "</tr>\n";
					$cpt++;
				}
				echo "</table>\n";
		
				echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
				echo "</form>\n";
			}

		}
	}
	elseif($mode=='saisie') {
		echo "</p>\n";

		echo "<h2>Saisie des 'notes' des élèves aux LVR</h2>\n";

		//$sql="SELECT DISTINCT e.login, e.prenom, e.nom, c.classe, nle.id_lvr FROM eleves e, j_eleves_classes jec, classes c, notanet_lvr_ele nle WHERE e.login=jec.login AND jec.id_classe='$id_classe' AND jec.id_classe=c.id AND nle.login=e.login ORDER BY c.classe, e.nom, e.prenom";
		$sql="SELECT DISTINCT e.login, e.prenom, e.nom, c.classe, nle.id_lvr FROM eleves e, j_eleves_classes jec, classes c, notanet_lvr_ele nle WHERE e.login=jec.login AND jec.id_classe=c.id AND nle.login=e.login ORDER BY c.classe, e.nom, e.prenom;";
		//echo "$sql<br />";
		$res_ele=mysql_query($sql);

		$nb_ele=mysql_num_rows($res_ele);
		if($nb_ele==0) {
			echo "<p>Aucun élève n'a été trouvé.</p>\n";
		}
		else {
			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
			echo add_token_field();

			echo "<input type='hidden' name='mode' value='saisie' />\n";
			echo "<input type='hidden' name='is_posted' value='1' />\n";

			$tab_lvr_ele=array();
			$sql="SELECT * FROM notanet_lvr_ele;";
			//echo "$sql<br />";
			$res_lvr_ele=mysql_query($sql);
			while($lig_lvr_ele=mysql_fetch_object($res_lvr_ele)) {
				$tab_lvr_ele[$lig_lvr_ele->login]=$lig_lvr_ele->id_lvr;
				$tab_note_lvr_ele[$lig_lvr_ele->login]=$lig_lvr_ele->note;
			}

			$tab_id_lvr=array();
			$tab_intitule_lvr=array();
			$sql="SELECT DISTINCT id,intitule FROM notanet_lvr ORDER BY intitule;";
			//echo "$sql<br />";
			$res_lvr=mysql_query($sql);
			while($lig_lvr=mysql_fetch_object($res_lvr)) {
				$tab_intitule_lvr[]=$lig_lvr->intitule;
				$tab_id_lvr[]=$lig_lvr->id;
			}

			$classe_precedente="";
			$cpt=0;
			while($lig_ele=mysql_fetch_object($res_ele)) {
				if($classe_precedente!=$lig_ele->classe) {

					if($classe_precedente!="") {
						echo "</table>\n";
						echo "</blockquote>\n";
					}

					$classe_precedente=$lig_ele->classe;

					echo "<p class='bold'>Classe de $lig_ele->classe</p>\n";
					echo "<blockquote>\n";

					echo "<table class='boireaus' summary='Choix des LVR des élèves'>\n";
	
					echo "<tr>\n";
					echo "<th rowspan='2'>\n";
					echo "Elève\n";
					echo "</th>\n";
		
					echo "<th colspan='3'>\n";
					echo "Note\n";
					echo "</th>\n";
	
					for($i=0;$i<count($tab_intitule_lvr);$i++) {
						echo "<th rowspan='2'>\n";
						echo $tab_intitule_lvr[$i];
						echo "</th>\n";
					}
					echo "</tr>\n";

					echo "<tr>\n";
					echo "<th>-</th>\n";
					echo "<th>VA</th>\n";
					echo "<th>NV</th>\n";
					echo "</tr>\n";

					$alt=1;
				}

					//while($lig_ele=mysql_fetch_object($res_ele)) {
						$alt=$alt*(-1);
						echo "<tr class='lig$alt white_hover'>\n";
						echo "<td align='left'>\n";
						echo "$lig_ele->nom $lig_ele->prenom\n";
						echo "<input type='hidden' name='login_ele[$cpt]' value=\"$lig_ele->login\" />\n";
						echo "</td>\n";

						echo "<td>\n";
						if($tab_note_lvr_ele[$lig_ele->login]=='') {$checked="checked ";} else {$checked="";}
						echo "<input type='radio' name='note_lvr[$cpt]' id='note_lvr_$cpt' value='' ";
						echo "title='$lig_ele->login -&gt; -' ";
						echo "$checked/>\n";
						echo "</td>\n";

						echo "<td>\n";
						if($tab_note_lvr_ele[$lig_ele->login]=='VA') {$checked="checked ";} else {$checked="";}
						echo "<input type='radio' name='note_lvr[$cpt]' id='note_lvr_$cpt' value='VA' ";
						echo "title='$lig_ele->login -&gt; VA' ";
						echo "$checked/>\n";
						echo "</td>\n";

						echo "<td>\n";
						if($tab_note_lvr_ele[$lig_ele->login]=='NV') {$checked="checked ";} else {$checked="";}
						echo "<input type='radio' name='note_lvr[$cpt]' id='note_lvr_$cpt' value='NV' ";
						echo "title='$lig_ele->login -&gt; NV' ";
						echo "$checked/>\n";
						echo "</td>\n";
	
						for($i=0;$i<count($tab_id_lvr);$i++) {
							echo "<td>\n";
							if((isset($tab_lvr_ele[$lig_ele->login]))&&($tab_lvr_ele[$lig_ele->login]==$tab_id_lvr[$i])) {echo "X";}
							echo "</td>\n";
						}
						echo "</tr>\n";
						$cpt++;
					//}
					//echo "</table>\n";

				//}
			}
			echo "</table>\n";
			echo "</blockquote>\n";

			echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";
		}

	}
	else {
		echo "<p style='color:red'>Choix invalide&nbsp;: $mode</p>\n";
	}
}

require("../lib/footer.inc.php");
die();
?>
