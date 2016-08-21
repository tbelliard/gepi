<?php
/* $Id$ */
/*
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





//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
// INSERT INTO droits VALUES('/mod_notanet/select_eleves.php','V','F','F','F','F','F','F','F','Sélection des élèves par type de brevet','');
// Pour décommenter le passage, il suffit de supprimer le 'slash-etoile' ci-dessus et l'étoile-slash' ci-dessous.
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================



// Récupération des variables:
// Tableau des classes:
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
// Vérifier s'il peut y avoir des accents dans un id_classe.

// Type de brevet:
$type_brevet=isset($_POST['type_brevet']) ? $_POST['type_brevet'] : (isset($_GET['type_brevet']) ? $_GET['type_brevet'] : NULL);
if(($type_brevet!=0)&&
($type_brevet!=0)&&
($type_brevet!=1)&&
($type_brevet!=2)&&
($type_brevet!=3)&&
($type_brevet!=4)&&
($type_brevet!=5)&&
($type_brevet!=6)&&
($type_brevet!=7)) {
	$type_brevet=NULL;
}

$choix_eleves=isset($_POST['choix_eleves']) ? $_POST['choix_eleves'] : NULL;
$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : NULL;
$coche_ele_login=isset($_POST['coche_ele_login']) ? $_POST['coche_ele_login'] : NULL;

if(!isset($msg)) {$msg="";}

if((isset($type_brevet))&&(isset($choix_eleves))&&(isset($ele_login))) {
	check_token();

	$sql="CREATE TABLE IF NOT EXISTS notanet_ele_type (
login VARCHAR( 50 ) NOT NULL ,
type_brevet TINYINT NOT NULL ,
PRIMARY KEY ( login )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(!$res) {
		$msg.="ERREUR lors de la création de la table 'notanet_ele_type'.<br />";
	}
	else {
		$sql="DELETE FROM notanet_ele_type WHERE type_brevet='$type_brevet';";
		$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);

		$nb_err=0;
		$cpt_enr=0;
		for($i=0;$i<count($ele_login);$i++) {
			$sql="SELECT type_brevet FROM notanet_ele_type WHERE login='$ele_login[$i]';";
			$res1=mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($res1)==0) {
				if(isset($coche_ele_login[$i])) {
					$sql="INSERT INTO notanet_ele_type SET login='$ele_login[$i]', type_brevet='$type_brevet';";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res2) {
						$msg.="ERREUR lors de l'insertion de l'association pour $ele_login[$i].<br />";
						$nb_err++;
					}
					else {
						$cpt_enr++;
					}
				}
			}
			else {
				$lig1=mysqli_fetch_object($res1);
				if($lig1->type_brevet==$type_brevet) {
					if(isset($coche_ele_login[$i])) {
						$sql="UPDATE notanet_ele_type SET type_brevet='$type_brevet' WHERE login='$ele_login[$i]';";
						$res2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res2) {
							$msg.="ERREUR lors de la mise à jour de l'association pour $ele_login[$i].<br />";
							$nb_err++;
						}
						else {
							$cpt_enr++;
						}
					}
					else {
						$sql="DELETE FROM notanet_ele_type WHERE login='$ele_login[$i]';";
						$res2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res2) {
							$msg.="ERREUR lors de la mise à jour de l'association pour $ele_login[$i].<br />";
							$nb_err++;
						}
						else {
							$cpt_enr++;
						}
					}
				}
				else {
					if(isset($coche_ele_login[$i])) {
						$sql="UPDATE notanet_ele_type SET type_brevet='$type_brevet' WHERE login='$ele_login[$i]';";
						$res2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res2) {
							$msg.="ERREUR lors de la mise à jour de l'association pour $ele_login[$i].<br />";
							$nb_err++;
						}
						else {
							$cpt_enr++;
						}
					}
				}
			}
		}

		if($nb_err==0) {$msg.="Enregistrement effectué pour $cpt_enr élève(s).<br />\n";}
	}
}



/*
// Déplacée dans lib_brevets.php
function get_classe_from_id($id){
	//$sql="SELECT * FROM classes WHERE id='$id_classe[0]'";
	$sql="SELECT * FROM classes WHERE id='$id'";
	$resultat_classe=mysql_query($sql);
	if(mysql_num_rows($resultat_classe)!=1){
		//echo "<p>ERREUR! La classe d'identifiant '$id_classe[0]' n'a pas pu être identifiée.</p>";
		echo "<p>ERREUR! La classe d'identifiant '$id' n'a pas pu être identifiée.</p>";
	}
	else{
		$ligne_classe=mysql_fetch_object($resultat_classe);
		$classe=$ligne_classe->classe;
		return $classe;
	}
}
*/


//echo '<link rel="stylesheet" type="text/css" media="print" href="impression.css">';

$style_specifique="mod_notanet/mod_notanet";

//**************** EN-TETE *****************
$titre_page = "Notanet: Associations élèves/type de brevet";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

// Bibliothèque pour Notanet et Fiches brevet
include("lib_brevets.php");

//==============================================
// A déplacer dans une feuille de styles externe
/*
echo "<style type='text/css'>
	tr.lig1:hover,
	tr.lig-1:hover {
		background-color:white;
	}
</style>\n";
*/
//==============================================

echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='../accueil.php'>Accueil</a> | <a href='index.php'>Retour à l'accueil Notanet</a>";

//debug_var();

//echo "<h2></h2>\n";

// Choix du type de Brevet:
if (!isset($type_brevet)) {
	echo "</p>\n";
	echo "</div>\n";
	echo "<h3>Choix du type de brevet</h3>\n";

	echo "<p>Choisissez un type de brevet&nbsp;: Série<br />\n";
	/*
	for($i=0;$i<count($tab_type_brevet);$i++){
		echo "<a href='".$_SERVER['PHP_SELF']."?type_brevet=$i'>$tab_type_brevet[$i]</a><br />\n";
	}
	*/
	foreach($tab_type_brevet as $key => $value){
		echo "<a href='".$_SERVER['PHP_SELF']."?type_brevet=$key'>$tab_type_brevet[$key]</a><br />\n";
	}
	echo "</p>\n";
}
else {

	// Choix des classes:
	//if (!isset($id_classe)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre type de brevet</a>";
	if ((!isset($id_classe))||(count($id_classe)==0)) {
		echo "</p>\n";
		echo "</div>\n";

		echo "<h3>Choix des classes pour le brevet série ".$tab_type_brevet[$type_brevet]."</h3>\n";

		echo "<form action='".$_SERVER['PHP_SELF']."' name='form_choix_classe' method='post'>\n";

		//echo "<input type='hidden' name='choix1' value='export' />\n";
		echo "<input type='hidden' name='type_brevet' value='$type_brevet' />\n";
		echo "<p>Sélectionnez les classes : </p>\n";
		echo "<blockquote>\n";
		$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
		$nombre_lignes = mysqli_num_rows($call_data);
		//echo "<select name='id_classe[]' multiple='true' size='10'>\n";

		$nb_class_par_colonne=round($nombre_lignes/3);
		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='center'>\n";

		$i = '0';

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		$i = 0;
		while ($i < $nombre_lignes){
			$classe = old_mysql_result($call_data, $i, "classe");
			$ide_classe = old_mysql_result($call_data, $i, "id");
			//echo "<a href='eleve_classe.php?id_classe=$ide_classe'>$classe</a><br />\n";
			//echo "<option value='$ide_classe'>$classe</option>\n";

			if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
				echo "</td>\n";
				//echo "<td style='padding: 0 10px 0 10px'>\n";
				echo "<td align='left'>\n";
			}

			echo "<label for='classe_$ide_classe' style='cursor: pointer;'>\n";
			echo "<input type='checkbox' name='id_classe[]' id='classe_$ide_classe' value='$ide_classe' ";

			$sql="SELECT 1=1 FROM eleves e, j_eleves_classes jec, notanet_ele_type n
					WHERE jec.login=e.login AND
							e.login=n.login AND
							jec.id_classe='$ide_classe' AND
							n.type_brevet='$type_brevet'
					ORDER BY e.nom,e.prenom";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)!=0) {
				echo "checked ";
			}

			echo "/>\n";
			echo " $classe<br />";
			echo "</label>\n";

			$i++;
		}

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		//echo "</select><br />\n";
		echo "<p align='center'><input type='submit' name='choix_classe' value='Envoyer' /></p>\n";
		echo "</blockquote>\n";
		//echo "</p>\n";
		echo "</form>\n";

		echo "<p style='text-indent:-4.5em; margin-left:4.5em;'><em>NOTES&nbsp;:</em> Vous devez sélectionner ici les classes dans lesquelles des élèves passent le brevet série ".$tab_type_brevet[$type_brevet].".<br />Une fois ce paramétrage fait, inutile d'y revenir.<br />Vous pourrez procéder à des extractions classe par classe (<em>à l'étape 6</em>)<br />et les impressions de fiches brevet également (<em>à l'étape 13</em>)<br />si vous le souhaitez ou si vos paramètres serveur l'imposent,<br />mais vous n'avez pas à modifier ici la liste des élèves susceptibles de passer tel DNB.</p>\n";


	}
	else {
		echo " | <a href='".$_SERVER['PHP_SELF']."?type_brevet=$type_brevet'>Choisir d'autres classes</a>";
		echo "</p>\n";
		echo "</div>\n";

		echo "<h3>Choix des élèves pour le brevet $tab_type_brevet[$type_brevet]</h3>\n";
		echo "<p>Les classes choisies sont: ";
		for($i=0;$i<count($id_classe);$i++) {
			if($i>0){echo ", ";}
			echo get_classe_from_id($id_classe[$i]);
		}
		echo "</p>\n";

		if(count($id_classe)>1) {
			echo "<p><a href='".$_SERVER['PHP_SELF']."?type_brevet=$type_brevet' onclick='tout_cocher();return false;'>Cocher tous les élèves de toutes les classes</a><br /><a href='".$_SERVER['PHP_SELF']."?type_brevet=$type_brevet' onclick='tout_decocher();return false;'>Décocher tous les élèves de toutes les classes</a></p>\n";
		}

		echo "<form action='".$_SERVER['PHP_SELF']."' name='form_choix_classe' method='post'>\n";
		echo add_token_field();
		echo "<input type='hidden' name='type_brevet' value='$type_brevet' />\n";

		$cpt=0;
		for($i=0;$i<count($id_classe);$i++) {
			echo "<p class='bold'>Classe de ".get_classe_from_id($id_classe[$i])."</p>\n";
			//echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
			$sql="SELECT DISTINCT e.login,e.nom,e.prenom
					FROM eleves e, j_eleves_classes jec
					WHERE jec.login=e.login AND
							jec.id_classe='$id_classe[$i]'
					ORDER BY e.nom,e.prenom";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<p>Aucun élève n'est affecté dans cette classe.</p>\n";
			}
			else {
				echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
				echo "<table class='boireaus'>\n";

				echo "<tr>\n";
				echo "<th>Elève</th>\n";
				echo "<th>Brevet $tab_type_brevet[$type_brevet]";
				echo "<br />\n";

				$cpt2=$cpt+mysqli_num_rows($res);

				echo "<a href=\"javascript:coche($cpt,$cpt2)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:decoche($cpt,$cpt2)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";

				echo "</th>\n";
				echo "</tr>\n";

				$alt=1;
				while($lig=mysqli_fetch_object($res)) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td><label for='ele_$cpt' style='cursor: pointer;'>".mb_strtoupper($lig->nom)." ".ucfirst(mb_strtolower($lig->prenom))."</label></td>\n";
					echo "<td>";

					//echo "<input type='checkbox' name='ele_login[]' id='ele_$cpt' value=\"$lig->login\" ";
					echo "<input type='hidden' name='ele_login[$cpt]' value=\"$lig->login\" />\n";
					echo "<input type='checkbox' name='coche_ele_login[$cpt]' id='ele_$cpt' value=\"$lig->login\" ";

					/*
					$sql="SELECT 1=1 FROM notanet_ele_type WHERE login='$lig->login' AND type_brevet='$type_brevet';";
					$res1=mysql_query($sql);
					if(mysql_num_rows($res1)!=0) {
						echo "checked ";
					}
					*/

					$sql="SELECT * FROM notanet_ele_type WHERE login='$lig->login';";
					$res1=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res1)!=0) {
						$lig_tmp=mysqli_fetch_object($res1);
						if($lig_tmp->type_brevet==$type_brevet) {
							echo "checked ";
						}
					}

					echo "/>\n";

					if(mysqli_num_rows($res1)!=0) {
						if($lig_tmp->type_brevet!=$type_brevet) {
							echo " <span style='font-size: xx-small;'>".$tab_type_brevet[$lig_tmp->type_brevet]."</span>";
						}
					}

					echo "</td>\n";
					echo "</tr>\n";

					$cpt++;
				}

				echo "</table>\n";
			}
			echo "<p><br /></p>\n";
		}

		if($cpt>0) {
			echo "<input type='hidden' name='choix_eleves' value='Enregistrer' />\n";
			echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";
			echo "<p><br /></p>\n";

			echo "<div id='fixe'><input type='submit' value='Enregistrer' /></div>\n";
		}
		echo "</form>\n";

		echo "<script type='text/javascript'>

function coche(i,j) {
	for (var k=i;k<j;k++) {
		if(document.getElementById('ele_'+k)){
			document.getElementById('ele_'+k).checked = true;
		}
	}
}

function decoche(i,j) {
	for (var k=i;k<j;k++) {
		if(document.getElementById('ele_'+k)){
			document.getElementById('ele_'+k).checked = false;
		}
	}
}

function tout_cocher() {
	coche(0,$cpt2);
}

function tout_decocher() {
	decoche(0,$cpt2);
}

</script>
";

	}

}

//echo "</div>\n";
require("../lib/footer.inc.php");
?>
