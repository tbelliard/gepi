<?php
/*
 *
 * Copyright 2001-2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// Check access
// INSERT INTO droits VALUES ('/saisie/saisie_mentions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Saisie de mentions', '');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";
$saisie_mention=isset($_POST['saisie_mention']) ? $_POST['saisie_mention'] : NULL;
$nouvelle_mention=isset($_POST['nouvelle_mention']) ? $_POST['nouvelle_mention'] : NULL;
$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : NULL;
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

$associer_mentions_classes=isset($_POST['associer_mentions_classes']) ? $_POST['associer_mentions_classes'] : (isset($_GET['associer_mentions_classes']) ? $_GET['associer_mentions_classes'] : NULL);

$saisie_association_mentions_classes=isset($_POST['saisie_association_mentions_classes']) ? $_POST['saisie_association_mentions_classes'] : NULL;
$id_mention=isset($_POST['id_mention']) ? $_POST['id_mention'] : array();

$saisie_ordre_mentions=isset($_POST['saisie_ordre_mentions']) ? $_POST['saisie_ordre_mentions'] : NULL;
$ordre_id_mention=isset($_POST['ordre_id_mention']) ? $_POST['ordre_id_mention'] : array();

$gepi_denom_mention=getSettingValue("gepi_denom_mention");
if($gepi_denom_mention=="") {
	$gepi_denom_mention="mention";
}

//debug_var();

if(isset($saisie_mention)) {
	check_token();

	$cpt_suppr=0;
	$tab_mentions=get_mentions();
	$tab_mentions_aff=get_tab_mentions_affectees();
	for($i=0;$i<count($suppr);$i++) {
		if(!in_array($suppr[$i],$tab_mentions_aff)) {
			$sql="DELETE FROM j_mentions_classes WHERE id_mention='$suppr[$i]';";
			//echo "$sql<br />";
			$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$nettoyage) {
				$msg.="Erreur lors de la suppression de l'association de la $gepi_denom_mention <b>".$tab_mentions[$suppr[$i]]."</b> avec une ou des classes.<br />";
			}
			else {
				$sql="DELETE FROM mentions WHERE id='$suppr[$i]';";
				//echo "$sql<br />";
				$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$nettoyage) {
					$msg.="Erreur lors de la suppression de la $gepi_denom_mention <b>".$tab_mentions[$suppr[$i]]."</b><br />";
				}
				else {
					$cpt_suppr++;
				}
			}
		}
	}
	if($cpt_suppr>0) {
		$msg.="$cpt_suppr $gepi_denom_mention(s) supprimée(s).<br />";
	}

	if($nouvelle_mention!="") {
		$sql="SELECT 1=1 FROM mentions WHERE mention='$nouvelle_mention';";
		//echo "$sql<br />";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0) {
			$msg.="La $gepi_denom_mention <b>$nouvelle_mention</b> existe déjà.<br />";
		}
		else {
			$sql="INSERT INTO mentions SET mention='$nouvelle_mention';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$res) {
				$msg.="Erreur lors de l'ajout de la $gepi_denom_mention <b>$nouvelle_mention</b><br />";
			}
			else {
				$msg.=ucfirst($gepi_denom_mention)." <b>$nouvelle_mention</b> ajoutée.<br />";
			}
		}
	}
}


if(isset($saisie_association_mentions_classes)) {
	check_token();

	$enregistrement_ok="n";

	$cpt_reg=0;
	$tab_mentions=get_mentions();
	for($i=0;$i<count($id_classe);$i++) {
		$tab_mentions_aff=get_tab_mentions_affectees($id_classe[$i]);

		$tab_mentions_classe=array();
		//$sql="SELECT DISTINCT a.id_mention FROM avis_conseil_classe a, j_eleves_classes j WHERE j.periode=a.periode AND j.login=a.login AND j.id_classe='$id_classe[$i]';";
		$sql="SELECT DISTINCT j.id_mention FROM j_mentions_classes j WHERE j.id_classe='$id_classe[$i]';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while ($lig=mysqli_fetch_object($res)) {
				$tab_mentions_classe[]=$lig->id_mention;
			}
		}

		/*
		echo "<p>\$tab_mentions_classe:<br />";
		foreach($tab_mentions_classe as $key => $value) {
			echo "\$tab_mentions_classe[$key]=$value<br />";
		}
		echo "</p>
		<hr />";
		echo "<p>\$id_mention:<br />";
		foreach($id_mention as $key => $value) {
			echo "\$id_mention[$key]=$value<br />";
		}
		echo "</p>
		<hr />";
		*/

		foreach($tab_mentions as $key => $value) {
			//echo "\$key=$key<br />";
			if((!in_array($key, $id_mention))&&(in_array($key, $tab_mentions_classe))&&(!in_array($key, $tab_mentions_aff))) {
				$sql="DELETE FROM j_mentions_classes WHERE id_classe='$id_classe[$i]' AND id_mention='$key';";
				//echo "$sql<br />";
				$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$nettoyage) {
					$msg.="Erreur lors de la suppression de la $gepi_denom_mention <b>".$value."</b> pour la classe <b>".get_class_from_id($id_classe[$i])."</b>.<br />";
				}
				else {$cpt_reg++;}
			}
			elseif((in_array($key, $id_mention))&&(!in_array($key, $tab_mentions_classe))) {
				$sql="INSERT INTO j_mentions_classes SET id_classe='$id_classe[$i]', id_mention='$key';";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'association de la $gepi_denom_mention <b>".$value."</b> avec la classe <b>".get_class_from_id($id_classe[$i])."</b>.<br />";
				}
				else {$cpt_reg++;}
			}
		}
	}
	if(($msg=="")&&($cpt_reg>0)) {
		$msg.=$cpt_reg." enregistrement(s) effectué(s) <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
		$enregistrement_ok="y";
	}
}

if(isset($saisie_ordre_mentions)) {
	check_token();

	$cpt_reg=0;
	$tab_mentions=get_mentions();

	for($i=0;$i<count($id_classe);$i++) {
		for($j=0;$j<count($ordre_id_mention);$j++) {
			$sql="UPDATE j_mentions_classes SET ordre='$j' WHERE id_classe='$id_classe[$i]' AND id_mention='$ordre_id_mention[$j]';";
			//echo "$sql<br />";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$update) {
				$msg.="Erreur lors de l'enregistrement de l'ordre $j pour la $gepi_denom_mention <b>".$tab_mentions[$ordre_id_mention[$j]]."</b> pour la classe <b>".get_class_from_id($id_classe[$i])."</b>.<br />";
			}
			else {$cpt_reg++;}
		}
	}

	if(($msg=="")&&($cpt_reg>0)) {
		$msg.=$cpt_reg." enregistrement(s) effectué(s) <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
		$enregistrement_ok="y";
	}
}

// Begin standart header
$titre_page = "Saisie de ".$gepi_denom_mention."s";
//====================================
// End standart header
require_once("../lib/header.inc.php");
if (!loadSettings()) {
	die("Erreur chargement settings");
}
//====================================

//debug_var();

function insere_mentions_par_defaut() {
	$cpt_erreur=0;
	$cpt_reg=0;
	$retour="";

	$tab_mentions=array('Félicitations', 'Mention honorable', 'Encouragements');
	for($i=0;$i<count($tab_mentions);$i++) {
		$sql="SELECT 1=1 FROM mentions WHERE mention='$tab_mentions[$i]';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0) {
			$retour.="La $gepi_denom_mention '$tab_mentions[$i]' est déjà enregistrée.<br />\n";
		}
		else {
			$sql="INSERT INTO mentions SET mention='$tab_mentions[$i]';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$res) {$cpt_erreur++;} else {$cpt_reg++;}
		}
	}

	if($cpt_erreur>0) {
		$retour.="$cpt_erreur erreur(s) lors de l'insertion des ".$gepi_denom_mention."s par défaut.<br />\n";
	}

	if($cpt_reg>0) {
		$retour.="$cpt_reg $gepi_denom_mention(s) enregistrée(s).<br />\n";
	}

	return $retour;
}

$sql="SHOW TABLES LIKE 'mentions';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="CREATE TABLE IF NOT EXISTS mentions (
	id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	mention VARCHAR(255) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	//echo "$sql<br />";
	$resultat_creation_table=mysqli_query($GLOBALS["mysqli"], $sql);

	echo "<p style='color:red'>".insere_mentions_par_defaut()."</p>\n";
}

$sql="CREATE TABLE IF NOT EXISTS j_mentions_classes (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_mention INT(11) NOT NULL ,
id_classe INT(11) NOT NULL ,
ordre TINYINT(4) NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$resultat_creation_table=mysqli_query($GLOBALS["mysqli"], $sql);

echo "<p class='bold'><a href='../accueil.php'>Retour</a>";
if(!isset($associer_mentions_classes)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."?associer_mentions_classes=y'>Sélectionner les ".$gepi_denom_mention."s associées aux classes</a>";
	echo "</p>\n";

	echo "<form name='formulaire' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();

	echo "<p>Liste des ".$gepi_denom_mention."s définies&nbsp;:</p>";
	echo "<table class='boireaus boireaus_alt' summary='Tableau des ".$gepi_denom_mention."s définies'>\n";
	echo "<tr>\n";
	echo "<th>".ucfirst($gepi_denom_mention)."</th>\n";
	echo "<th>Supprimer</th>\n";
	echo "</tr>\n";
	$tab_mentions=get_mentions();
	$tab_mentions_aff=get_tab_mentions_affectees();
	//for($i=0;$i<count($tab_mentions);$i++) {
	foreach($tab_mentions as $key => $value) {
		echo "<tr>\n";
		echo "<td><label for='suppr_$key'>$value</label></td>\n";
		echo "<td>";
		if(!in_array($key,$tab_mentions_aff)) {
			echo "<input type='checkbox' name='suppr[]' id='suppr_$key' value='$key' />";
		}
		else {
			echo "<img src='../images/disabled.png' width='20' height='20' alt='Suppression impossible: ".ucfirst($gepi_denom_mention)." donnée à au moins un élève.' title='Suppression impossible: ".ucfirst($gepi_denom_mention)." donnée à au moins un élève.' />";
		}
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";

	echo "<p>".ucfirst($gepi_denom_mention)." à ajouter&nbsp;: <input type='text' name='nouvelle_mention' value='' /></p>\n";
	echo "<input type='hidden' name='saisie_mention' value='y' /></p>\n";
	echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";

	echo "<p><i>NOTES</i>&nbsp;:</p>
<ul>
	<li>
		<p>L'intitulé <b>$gepi_denom_mention</b> peut être modifié dans la page <a href='../gestion/param_gen.php#gepi_denom_mention'>Configuration générale</a></p>
	</li>
	<li>
		<p>Pour que le champ de saisie d'une $gepi_denom_mention n'apparaisse pas (<i>lors de la saisie de l'avis du conseil de classe</i>) pour une classe donnée, il suffit qu'aucune $gepi_denom_mention ne soit associée à la classe.</p>
	</li>
	<li>
		<p><b>Extrait de l'article R511-13 du code de l'éducation : </b>Il rappelle que dans les lycées et collèges relevant du ministre chargé de l'éducation, les sanctions qui peuvent être prononcées à l'encontre des élèves sont les suivantes :</p>
		<ol>
			<li>L'avertissement</li>
			<li>Le blâme ;</li>
			<li>La mesure de responsabilisation ;</li>
			<li>L'exclusion temporaire de la classe. Pendant l'accomplissement de la sanction, l'élève est accueilli dans l'établissement. La durée de cette exclusion ne peut excéder huit jours ;</li>
			<li>L'exclusion temporaire de l'établissement ou de l'un de ses services annexes. La durée de cette exclusion ne peut excéder huit jours ;</li>
			<li>L'exclusion définitive de l'établissement ou de l'un de ses services annexes.</li>
		</ol>
		<p>Les sanctions peuvent être assorties d'un sursis total ou partiel.<br />
		<b>L'avertissement, le blâme et la mesure de responsabilisation sont effacés du dossier administratif de l'élève à l'issue de l'année scolaire.</b></br> 
		Les autres sanctions, hormis l'exclusion définitive, sont effacées du dossier administratif de l'élève au bout d'un an.</br></br>
		Le règlement intérieur reproduit l'échelle des sanctions et prévoit les mesures de prévention et d'accompagnement ainsi que les modalités de la mesure de responsabilisation.<br />
		<a href='http://www.legifrance.gouv.fr/affichCodeArticle.do?cidTexte=LEGITEXT000006071191&idArticle=LEGIARTI000020663068&dateTexte=&categorieLien=cid'>Le lien sur Légifrance</a>.</p>
	</li>
</ul>\n";
	echo "<p style='color:red'>Pour ne pas poser de problèmes sur les bulletins PDF, il est recommandé pour le moment (<i>à améliorer</i>)&nbsp;:</p>
<ul style='color:red'>
<li>de ne pas dépasser 18 caractères dans une $gepi_denom_mention</li>
<li>de ne pas définir plus de 8 ".$gepi_denom_mention."s différentes pour une même classe</li>
</ul>\n";
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Saisir des ".$gepi_denom_mention."s</a>";

	if(!isset($id_classe)) {
		echo "</p>\n";

		echo "<p>Pour quelle(s) classe(s) souhaitez-vous choisir les ".$gepi_denom_mention."s&nbsp;?</p>\n";

		$sql="select distinct id,classe from classes order by classe";
		$classes_list=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb=mysqli_num_rows($classes_list);
		if($nb==0){
			echo "<p>Aucune classe n'est encore définie...</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
		echo add_token_field();

		$nb_class_par_colonne=round($nb/3);
		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";

		$i=0;
		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while ($i < $nb) {
			$id_classe=old_mysql_result($classes_list, $i, 'id');
			//$temp = "id_classe_".$id_classe;
			$classe=old_mysql_result($classes_list, $i, 'classe');

			if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
				echo "</td>\n";
				//echo "<td style='padding: 0 10px 0 10px'>\n";
				echo "<td align='left'>\n";
			}

			echo "<input type='checkbox' name='id_classe[]' id='id_classe_$i' value='$id_classe' ";
			echo "onchange=\"checkbox_change($i)\" ";
			echo "/><label for='id_classe_$i'><span id='texte_id_classe_$i'>Classe : ".$classe.".</span></label><br />\n";
			$i++;
		}

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		//echo "<input type='hidden' name='is_posted' value='2' />\n";

		echo "<input type='hidden' name='associer_mentions_classes' value='y' />\n";
		echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
		echo "</form>\n";

		echo "<p><a href=\"javascript:cocher_toutes_classes('cocher')\">Cocher</a> / <a href=\"javascript:cocher_toutes_classes('decocher')\">décocher</a> toutes les classes.</p>\n";

		echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('id_classe_'+cpt)) {
		if(document.getElementById('id_classe_'+cpt).checked) {
			document.getElementById('texte_id_classe_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_id_classe_'+cpt).style.fontWeight='normal';
		}
	}
}

function cocher_toutes_classes(mode) {
	if(mode=='cocher') {
		for(i=0;i<$nb;i++) {
			if(document.getElementById('id_classe_'+i)) {
				//alert('i='+i);
				document.getElementById('id_classe_'+i).checked=true;
				checkbox_change(i);
			}
		}
	}
	else {
		for(i=0;i<$nb;i++) {
			if(document.getElementById('id_classe_'+i)) {
				document.getElementById('id_classe_'+i).checked=false;
				checkbox_change(i);
			}
		}
	}
}
</script>\n";

	}
	elseif(!isset($enregistrement_ok)) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?associer_mentions_classes=y'>Choisir d'autres classes</a>";
		echo "</p>\n";

		echo "<form name='formulaire' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo add_token_field();

		$tab_nom_classe=array();
		$sql="SELECT DISTINCT id_mention FROM j_mentions_classes WHERE (";
		echo "<p>Choisir les ".$gepi_denom_mention."s pour la ou les classes&nbsp;: ";
		for($i=0;$i<count($id_classe);$i++) {
			if($i>0) {
				echo ", ";
				$sql.=" OR ";
			}
			$tab_nom_classe[$id_classe[$i]]=get_class_from_id($id_classe[$i]);
			echo "<strong>".$tab_nom_classe[$id_classe[$i]]."</strong>";
			echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
			//echo " ($id_classe[$i])";
			$sql.="id_classe='$id_classe[$i]'";

			$tab_mentions_classe[$i]=get_tab_mentions_affectees($id_classe[$i]);
		}
		$sql.=")";
		echo "<br />\n";

		$tab_mentions=get_mentions();
		$tab_mentions_aff=get_tab_mentions_affectees();
		//select * from avis_conseil_classe acc, j_eleves_classes jec where acc.login=jec.login AND id_mention='2' AND id_classe='44';
		//delete from avis_conseil_classe where login in (select login from j_eleves_classes where id_classe='44');
		//update avis_conseil_classe set id_mention='0' where login in (select login from j_eleves_classes where id_classe='33');

		$tab_mentions_classes=array();
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				$tab_mentions_classes[]=$lig->id_mention;
			}
		}

		echo "parmi les ".$gepi_denom_mention."s suivantes&nbsp;:</p>";
		echo "<table class='boireaus boireaus_alt' summary='Tableau des ".$gepi_denom_mention."s'>\n";
		echo "<tr>\n";
		echo "<th>Cocher</th>\n";
		echo "<th>".ucfirst($gepi_denom_mention)."</th>\n";
		echo "<th>Classes déjà associées</th>\n";
		echo "<th>Classes manquantes</th>\n";
		echo "</tr>\n";
		//for($i=0;$i<count($tab_mentions);$i++) {
		foreach($tab_mentions as $key => $value) {
			echo "<tr>\n";
			echo "<td>";

			$tmp_tab_clas_mention_courante=array();
			$chaine_classes="";
			$sql="SELECT DISTINCT c.classe, c.id FROM classes c, j_mentions_classes j WHERE j.id_classe=c.id AND j.id_mention='$key' ORDER BY c.classe;";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$cpt_classe=0;
				while($lig=mysqli_fetch_object($res)) {
					$tmp_tab_clas_mention_courante[]=$lig->id;

					if($cpt_classe>0) {$chaine_classes.=", ";}
					if(in_array($lig->id, $id_classe)) {
						$chaine_classes.="<strong>".$lig->classe."</strong>";
					}
					else {
						$chaine_classes.=$lig->classe;
					}
					$cpt_classe++;
				}
			}

			/*
			echo "<input type='checkbox' name='id_mention[]' id='id_mention_$key'value='$key' ";
			//if($chaine_classes!="") {echo "checked ";}
			if(in_array($key, $tab_mentions_classes)) {echo "checked ";}
			for($i=0;$i<count($id_classe);$i++) {
				if(in_array($key, $tab_mentions_classe[$i])) {
					echo "disabled title=\"La mention est attribuée à au moins un élève pour la ou les classes choisies.\"";
					break;
				}
			}
			echo "/>";
			*/

			$mention_associee="n";
			for($i=0;$i<count($id_classe);$i++) {
				if(in_array($key, $tab_mentions_classe[$i])) {
					$mention_associee="y";
					break;
				}
			}

			if($mention_associee=="y") {
				echo "<input type='hidden' name='id_mention[]' value='$key' />
					<img src='../images/enabled.png' class='icone20' title=\"La mention est attribuée à au moins un élève pour la ou les classes choisies.\" />";
			}
			else {
				echo "<input type='checkbox' name='id_mention[]' id='id_mention_$key'value='$key' ";
				if(in_array($key, $tab_mentions_classes)) {echo "checked ";}
				echo "/>";
			}

			echo "</td>\n";

			echo "<td><label for='id_mention_$key'>$value</label></td>\n";

			echo "<td>";
			echo $chaine_classes;
			echo "</td>\n";

			echo "<td style='color:red'>";
			$chaine_classes_manquantes="";
			for($i=0;$i<count($id_classe);$i++) {
				if(!in_array($id_classe[$i], $tmp_tab_clas_mention_courante)) {
					if($chaine_classes_manquantes!="") {
						$chaine_classes_manquantes.=", ";
					}
					$chaine_classes_manquantes.=$tab_nom_classe[$id_classe[$i]];
				}
			}
			echo $chaine_classes_manquantes;
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";

		echo "<input type='hidden' name='associer_mentions_classes' value='y' />\n";
		echo "<input type='hidden' name='saisie_association_mentions_classes' value='y' />\n";
		echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
		echo "</form>\n";
	}
	else {
		echo " | <a href='".$_SERVER['PHP_SELF']."?associer_mentions_classes=y'>Choisir d'autres classes</a>";
		echo "</p>\n";

		// Ordre des mentions

		echo "<form name='formulaire' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo add_token_field();

		$sql="SELECT DISTINCT id_mention FROM j_mentions_classes WHERE (";

		echo "<p>Choisir l'ordre des ".$gepi_denom_mention."s pour la ou les classes&nbsp;: ";
		for($i=0;$i<count($id_classe);$i++) {
			if($i>0) {
				echo ", ";
				$sql.=" OR ";
			}
			echo "<strong>".get_class_from_id($id_classe[$i])."</strong>";
			echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
			$sql.="id_classe='$id_classe[$i]'";
		}
		echo ".</p>\n";
		$sql.=") ORDER BY ordre, id_mention;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p style='color:red'>Aucune $gepi_denom_mention n'est associée à ces classes.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$tab_mentions=get_mentions();

		echo "<p>Les ".$gepi_denom_mention."s sont&nbsp;:</p>\n";
		echo "<ul>\n";
		$tab_mentions_classes=array();
		while($lig=mysqli_fetch_object($res)) {
			$tab_mentions_classes[]=$lig->id_mention;
			echo "<li>".$tab_mentions[$lig->id_mention];
			echo "<input type='hidden' name='id_mention[]' value='$lig->id_mention' />\n";
			echo "</li>\n";
		}
		echo "</ul>\n";

		echo "<p>Ordre choisi&nbsp;:</p>\n";
		echo "<ol>\n";
		for($i=0;$i<count($tab_mentions_classes);$i++) {
			echo "<li>\n";
			echo "<select name='ordre_id_mention[]'>\n";
			for($j=0;$j<count($tab_mentions_classes);$j++) {
				echo "<option value='".$tab_mentions_classes[$j]."'";
				if($j==$i) {echo " selected";}
				echo ">".$tab_mentions[$tab_mentions_classes[$j]]."</option>\n";
			}
			echo "</select>\n";
			echo "</li>\n";
		}
		echo "</ol>\n";

		echo "<input type='hidden' name='associer_mentions_classes' value='y' />\n";
		echo "<input type='hidden' name='saisie_ordre_mentions' value='y' />\n";
		echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
		echo "</form>\n";
	}
}

require("../lib/footer.inc.php");
die();
?>
