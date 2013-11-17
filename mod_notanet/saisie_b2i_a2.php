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


// INSERT INTO droits VALUES('/mod_notanet/saisie_b2i_a2.php','V','F','F','F','F','F','F','F','Notanet: Saisie des notes de socle B2i et A2','');
// INSERT INTO droits VALUES('/mod_notanet/saisie_b2i_a2.php','V','F','F','V','F','F','F','F','Notanet: Saisie des notes de socle B2i et A2','');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}



$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$msg="";

if (isset($_POST['is_posted'])) {
	check_token();

	$pb_record="no";

	$ele_login=isset($_POST["ele_login"]) ? $_POST["ele_login"] : NULL;
	$b2i=isset($_POST["b2i"]) ? $_POST["b2i"] : NULL;
	$a2=isset($_POST["a2"]) ? $_POST["a2"] : NULL;
	$lv=isset($_POST["lv"]) ? $_POST["lv"] : NULL;


	for($i=0;$i<count($ele_login);$i++) {
		// Vérifier si l'élève est bien dans la classe?
		// Inutile si seul l'admin accède et qu'on ne limite pas l'accès à telle ou telle classe

		if((isset($b2i[$i]))||(isset($a2[$i]))||(isset($lv[$i]))) {
			//echo "<p>Traitement de ".$ele_login[$i]." avec \$b2i[$i]=".$b2i[$i].", \$a2[$i]=".$a2[$i]." et \$lv[$i]=".$lv[$i]."<br />\n";

			$sql2="";
			$sql3="";
			$maj_notanet="n";
			$sql="SELECT 1=1 FROM notanet WHERE login='".$ele_login[$i]."';";
			$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($test)>0) {
				$maj_notanet="y";
			}

			$sql="DELETE FROM notanet_socles WHERE login='".$ele_login[$i]."';";
			$nettoyage=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

			$sql="INSERT INTO notanet_socles SET login='".$ele_login[$i]."'";

			if((isset($b2i[$i]))&&(($b2i[$i]=='MS')||($b2i[$i]=='ME')||($b2i[$i]=='MN')||($b2i[$i]=='AB'))) {
				$sql.=",b2i='".$b2i[$i]."'";

				if($sql2=='') {
					$sql2="UPDATE notanet SET note='".$b2i[$i]."', note_notanet='".$b2i[$i]."'";
				}
				else {
					$sql2.=", note='".$b2i[$i]."', note_notanet='".$b2i[$i]."'";
				}
			}
			else {
				$sql.=",b2i=''";

				if($sql2=='') {
					$sql2="UPDATE notanet SET note='', note_notanet=''";
				}
				else {
					$sql2.=", note='',  note_notanet=''";
				}
			}

			if((isset($a2[$i]))&&(($a2[$i]=='MS')||($a2[$i]=='ME')||($a2[$i]=='MN')||($a2[$i]=='AB'))) {
				$sql.=",a2='".$a2[$i]."'";

				if($sql3=='') {
					$sql3="UPDATE notanet SET note='".$a2[$i]."', note_notanet='".$a2[$i]."'";
				}
				else {
					$sql3=", note='".$a2[$i]."', note_notanet='".$a2[$i]."'";
				}
			}
			else {
				$sql.=",a2=''";

				if($sql2=='') {
					$sql3="UPDATE notanet SET note='', note_notanet=''";
				}
				else {
					$sql3=", note='', note_notanet=''";
				}
			}

			if((isset($lv[$i]))&&($lv[$i]!='')) {
				$sql2="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_matieres jgm WHERE (jeg.login='".$ele_login[$i]."' AND jeg.id_groupe=jgm.id_groupe AND jgm.id_matiere='".$lv[$i]."');";
				$res_mat_suivie=mysqli_query($GLOBALS["___mysqli_ston"], $sql2);
				if(mysqli_num_rows($res_mat_suivie)!=0) {
					$sql.=",lv='".$lv[$i]."'";
				}
				else {
					$sql.=",lv=''";
				}
			}
			else {
				$sql.=",lv=''";
			}
			$sql.=";";
			$sql2.=" WHERE login='".$ele_login[$i]."' AND notanet_mat='SOCLE B2I';";
			$sql3.=" WHERE login='".$ele_login[$i]."' AND notanet_mat='SOCLE NIVEAU A2 DE LANGUE';";

			//echo "$sql<br />";
			$register=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if (!$register) {
				$msg .= "Erreur lors de l'enregistrement des données pour $ele_login[$i]<br />";
				//echo "ERREUR<br />";
				$pb_record = 'yes';
			}

			if($maj_notanet=='y') {
				// On met à jour la table notanet avec les corrections apportées sur notanet_socles
				$register=mysqli_query($GLOBALS["___mysqli_ston"], $sql2);
				$register=mysqli_query($GLOBALS["___mysqli_ston"], $sql3);
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
$titre_page = "Notanet | Saisie des notes de socles B2i et A2";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

$tmp_timeout=(getSettingValue("sessionMaxLength"))*60;

?>
<script type="text/javascript" language="javascript">
change = 'no';
</script>

<p class="bold"><a href="../accueil.php" onclick="return confirm_abandon(this, change, '<?php echo $themessage; ?>')"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>

<?php

echo " | <a href='index.php'>Accueil Notanet</a>";

if(!isset($id_classe)) {
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

	$sql="CREATE TABLE IF NOT EXISTS notanet_socles (
		login VARCHAR( 50 ) NOT NULL ,
		b2i ENUM( 'MS', 'ME', 'MN', 'AB', '' ) NOT NULL ,
		a2 ENUM( 'MS', 'ME', 'MN', 'AB', '' ) NOT NULL ,
		lv VARCHAR( 50 ) NOT NULL ,
		PRIMARY KEY ( login )
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$create_table=mysqli_query($GLOBALS["___mysqli_ston"], $sql);


	echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo add_token_field();

	$tabdiv_infobulle[]=creer_div_infobulle('MS',"","","<center>Maîtrise du socle</center>","",10,0,'y','y','n','n');
	$tabdiv_infobulle[]=creer_div_infobulle('ME',"","","<center>Maîtrise de certains éléments du socle</center>","",12,0,'y','y','n','n');
	$tabdiv_infobulle[]=creer_div_infobulle('MN',"","","<center>Maîtrise du socle non évaluée</center>","",10,0,'y','y','n','n');
	$tabdiv_infobulle[]=creer_div_infobulle('AB',"","","<center>Absent</center>","",8,0,'y','y','n','n');

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
			echo "<table class='boireaus' border='1' summary='Saisie B2I A2'>\n";

			$sql="SELECT DISTINCT id_matiere FROM j_groupes_classes jgc, j_groupes_matieres jgm, notanet_corresp nc WHERE (jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='".$id_classe[$i]."' AND nc.matiere=jgm.id_matiere AND notanet_mat LIKE 'LANGUE VIVANTE %') ORDER BY id_matiere;";
			$res_mat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

			//$nb_colspan_mat=3+mysql_num_rows($res_mat);
			$nb_colspan_mat=4+mysqli_num_rows($res_mat);

			echo "<tr>\n";
			echo "<th rowspan='3'>Elève</th>\n";
			echo "<th colspan='4'>Socle B2i</th>\n";
			echo "<th colspan='".$nb_colspan_mat."'>Socle A2</th>\n";
			echo "</tr>\n";

			echo "<tr>\n";

			// B2i
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
			echo "<a href='#' onmouseover=\"afficher_div('AB','y',-20,20);\"";
			echo " onmouseout=\"cacher_div('AB')\" onclick=\"return false;\"";
			echo ">";
			echo "AB";
			echo "</a>\n";
			echo "</th>\n";


			// Niveau A2
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
			echo "<a href='#' onmouseover=\"afficher_div('AB','y',-20,20);\"";
			echo " onmouseout=\"cacher_div('AB')\" onclick=\"return false;\"";
			echo ">";
			echo "AB";
			echo "</a>\n";
			echo "</th>\n";

			// Liste des langues de la classe
			unset($tab_mat);
			$tab_mat=array();
			while($lig_mat=mysqli_fetch_object($res_mat)) {
				echo "<th>".$lig_mat->id_matiere."</th>\n";
				$tab_mat[]=$lig_mat->id_matiere;
			}
			echo "</tr>\n";






			echo "<tr>\n";

			// B2i
			echo "<th>";
			echo "<a href=\"javascript:CocheColonne('b2i_MS_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('b2i_MS_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</th>\n";

			echo "<th>";
			echo "<a href=\"javascript:CocheColonne('b2i_ME_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('b2i_ME_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</th>\n";

			echo "<th>";
			echo "<a href=\"javascript:CocheColonne('b2i_MN_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('b2i_MN_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</th>\n";

			echo "<th>";
			echo "<a href=\"javascript:CocheColonne('b2i_AB_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('b2i_AB_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</th>\n";


			// Niveau A2
			echo "<th>";
			echo "<a href=\"javascript:CocheColonne('a2_MS_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('a2_MS_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</th>\n";

			echo "<th>";
			echo "<a href=\"javascript:CocheColonne('a2_ME_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('a2_ME_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</th>\n";

			echo "<th>";
			echo "<a href=\"javascript:CocheColonne('a2_MN_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('a2_MN_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</th>\n";

			echo "<th>";
			echo "<a href=\"javascript:CocheColonne('a2_AB_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('a2_AB_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</th>\n";

			// Liste des langues de la classe
			for($j=0;$j<count($tab_mat);$j++) {
				echo "<th>";
				echo "<a href=\"javascript:CocheColonne('lv_".$tab_mat[$j]."_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('lv_".$tab_mat[$j]."_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				echo "</th>\n";
			}
			echo "</tr>\n";


			$alt=1;
			while($lig_ele=mysqli_fetch_object($res_ele)) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td>";
				echo "<input type='hidden' name='ele_login[$cpt]' value=\"".$lig_ele->login."\" />\n";
				echo $lig_ele->nom." ".$lig_ele->prenom;
				echo "</td>\n";

				$sql="SELECT * FROM notanet_socles WHERE login='".$lig_ele->login."';";
				$res_socle=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_socle)==0) {
					$def_b2i="";
					$def_a2="";
					$def_lv="";
				}
				else {
					$lig_soc=mysqli_fetch_object($res_socle);
					$def_b2i=$lig_soc->b2i;
					$def_a2=$lig_soc->a2;
					$def_lv=$lig_soc->lv;
				}

				echo "<td><input type='radio' name='b2i[$cpt]' id='b2i_MS_".$cpt."_".$i."' value='MS' onchange='changement();' ";
				if($def_b2i=='MS') {echo "checked ";}
				echo "title='$lig_ele->login B2i -&gt; MS' ";
				echo "/></td>\n";
				echo "<td><input type='radio' name='b2i[$cpt]' id='b2i_ME_".$cpt."_".$i."' value='ME' onchange='changement();' ";
				if($def_b2i=='ME') {echo "checked ";}
				echo "title='$lig_ele->login B2i -&gt; ME' ";
				echo "/></td>\n";
				echo "<td><input type='radio' name='b2i[$cpt]' id='b2i_MN_".$cpt."_".$i."' value='MN' onchange='changement();' ";
				if($def_b2i=='MN') {echo "checked ";}
				echo "title='$lig_ele->login B2i -&gt; MN' ";
				echo "/></td>\n";
				echo "<td><input type='radio' name='b2i[$cpt]' id='b2i_AB_".$cpt."_".$i."' value='AB' onchange='changement();' ";
				if($def_b2i=='AB') {echo "checked ";}
				echo "title='$lig_ele->login B2i -&gt; AB' ";
				echo "/></td>\n";

				echo "<td><input type='radio' name='a2[$cpt]' id='a2_MS_".$cpt."_".$i."' value='MS' onchange='changement();' ";
				if($def_a2=='MS') {echo "checked ";}
				echo "title='$lig_ele->login A2 -&gt; MS' ";
				echo "/></td>\n";
				echo "<td><input type='radio' name='a2[$cpt]' id='a2_ME_".$cpt."_".$i."' value='ME' onchange='changement();' ";
				if($def_a2=='ME') {echo "checked ";}
				echo "title='$lig_ele->login A2 -&gt; ME' ";
				echo "/></td>\n";
				echo "<td><input type='radio' name='a2[$cpt]' id='a2_MN_".$cpt."_".$i."' value='MN' onchange='changement();' ";
				if($def_a2=='MN') {echo "checked ";}
				echo "title='$lig_ele->login A2 -&gt; MN' ";
				echo "/></td>\n";
				echo "<td><input type='radio' name='a2[$cpt]' id='a2_AB_".$cpt."_".$i."' value='AB' onchange='changement();' ";
				if($def_a2=='AB') {echo "checked ";}
				echo "title='$lig_ele->login A2 -&gt; AB' ";
				echo "/></td>\n";

				for($j=0;$j<count($tab_mat);$j++) {
					$sql="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_matieres jgm WHERE (jeg.login='".$lig_ele->login."' AND jeg.id_groupe=jgm.id_groupe AND jgm.id_matiere='".$tab_mat[$j]."');";
					$res_mat_suivie=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res_mat_suivie)==0) {
						echo "<td>&nbsp;</td>\n";
					}
					else {
						echo "<td><input type='radio' name='lv[$cpt]' id='lv_".$tab_mat[$j]."_".$cpt."_".$i."' value=\"".$tab_mat[$j]."\" onchange='changement();' ";
						if($def_lv==$tab_mat[$j]) {echo "checked ";}
						echo "title='$lig_ele->login -&gt; $tab_mat[$j]' ";
						echo "/></td>\n";
					}
				}
				echo "</tr>\n";
				$cpt++;
			}

			echo "</table>\n";

			echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
		}
	}

	echo "<input type='hidden' name='is_posted' value='y' />\n";
	//echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";


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

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
die();
?>
