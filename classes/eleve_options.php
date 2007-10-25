<?php
/*
 * $Id$
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
};

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
include "../lib/periodes.inc.php";

if (isset($is_posted)) {
	$msg = '';
	$j = 1;
	while ($j < $nb_periode) {
		$call_group = mysql_query("SELECT DISTINCT g.id, g.name FROM groupes g, j_groupes_classes jgc WHERE (g.id = jgc.id_groupe and jgc.id_classe = '" . $id_classe ."') ORDER BY jgc.priorite, g.name");
		$nombre_ligne = mysql_num_rows($call_group);
		$i=0;
		while ($i < $nombre_ligne) {
			$id_groupe = mysql_result($call_group, $i, "id");
			$nom_groupe = mysql_result($call_group, $i, "name");
			$id_group[$j] = $id_groupe."_".$j;
			$test_query = mysql_query("SELECT 1=1 FROM j_eleves_groupes WHERE (" .
					"id_groupe = '" . $id_groupe . "' and " .
					"login = '" . $login_eleve . "' and " .
					"periode = '" . $j . "')");
			$test = mysql_num_rows($test_query);
			if (isset($_POST[$id_group[$j]])) {
				if ($test == 0) {
					$req = mysql_query("INSERT INTO j_eleves_groupes SET id_groupe = '" . $id_groupe . "', login = '" . $login_eleve . "', periode = '" . $j ."'");
				}
			} else {
				$test1 = mysql_query("SELECT 1=1 FROM matieres_notes WHERE (id_groupe = '".$id_groupe."' and login = '".$login_eleve."' and periode = '$j')");
				$nb_test1 = mysql_num_rows($test1);
				$test2 = mysql_query("SELECT 1=1 FROM matieres_appreciations WHERE (id_groupe = '".$id_groupe."' and login = '".$login_eleve."' and periode = '$j')");
				$nb_test2 = mysql_num_rows($test2);
				if (($nb_test1 != 0) or ($nb_test2 != 0)) {
					$msg = $msg."--> Impossible de supprimer cette option pour l'élève $login_eleve car des moyennes ou appréciations ont déjà été rentrées pour le groupe $nom_groupe pour la période $j ! Commencez par supprimer ces données !<br />";
				} else {
					if ($test != "0")  $req = mysql_query("DELETE FROM j_eleves_groupes WHERE (login='".$login_eleve."' and id_groupe='".$id_groupe."' and periode = '".$j."')");
				}
			}
			$i++;
		}
		$j++;
	}
	//$affiche_message = 'yes';
	$msg = "Les modifications ont été enregistrées !";
}
//$message_enregistrement = "Les modifications ont été enregistrées !";



// =================================
// AJOUT: boireaus
//$sql="SELECT DISTINCT jec.login FROM j_eleves_classes jec, eleves e
$sql="SELECT DISTINCT jec.login,e.nom,e.prenom FROM j_eleves_classes jec, eleves e
						WHERE jec.login=e.login AND
							jec.id_classe='$id_classe'
						ORDER BY e.nom,e.prenom";
//echo "$sql<br />";
//echo "\$login_eleve=$login_eleve<br />";
$res_ele_tmp=mysql_query($sql);
$chaine_options_login_eleves="";
if(mysql_num_rows($res_ele_tmp)>0){
    $login_eleve_prec=0;
    $login_eleve_suiv=0;
    $temoin_tmp=0;
    while($lig_ele_tmp=mysql_fetch_object($res_ele_tmp)){
        if($lig_ele_tmp->login==$login_eleve){
			$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login' selected='true'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";

            $temoin_tmp=1;
            if($lig_ele_tmp=mysql_fetch_object($res_ele_tmp)){
                $login_eleve_suiv=$lig_ele_tmp->login;
				$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
            }
            else{
                $login_eleve_suiv=0;
            }
        }
		else{
			$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
		}

        if($temoin_tmp==0){
            $login_eleve_prec=$lig_ele_tmp->login;
        }
    }
}
// =================================


//**************** EN-TETE **************************************
$titre_page = "Gestion des classes | Gestion des matières par élève";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

//=============================
// MODIF: boireaus
//echo "<p class=bold>|<a href=\"classes_const.php?id_classe=".$id_classe."\">Retour</a>|";
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';

echo "<form action='eleve_options.php' name='form1' method='post'>\n";
echo "<p class=bold><a href=\"classes_const.php?id_classe=".$id_classe."\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if("$login_eleve_prec"!="0"){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;login_eleve=$login_eleve_prec'>Elève précédent</a>";}

echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
echo " | <select name='login_eleve' onchange='document.form1.submit()'>\n";
echo $chaine_options_login_eleves;
echo "</select>\n";

if("$login_eleve_suiv"!="0"){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;login_eleve=$login_eleve_suiv'>Elève suivant</a>";}

echo "</p>\n";
echo "</form>\n";

//debug_var();

//=============================

?>
<form action="eleve_options.php" name='form2' method=post>
<?php

$call_nom_class = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
$classe = mysql_result($call_nom_class, 0, 'classe');

$call_data_eleves = mysql_query("SELECT * FROM eleves WHERE (login = '$login_eleve')");
$nom_eleve = @mysql_result($call_data_eleves, '0', 'nom');
$prenom_eleve = @mysql_result($call_data_eleves, '0', 'prenom');

echo "<h3>".$nom_eleve." ".$prenom_eleve." - Classe : $classe</h3>\n";
//echo "<p>Pour valider les modifications, cliquez sur le bouton qui apparait en bas de la page.</p>\n";
echo "<p>Pour valider les modifications, cliquez sur le bouton.</p>\n";

echo "<p align='center'><input type='submit' value='Enregistrer les modifications' /></p>\n";

// J'appelle les différents groupes existants pour la classe de l'élève

$call_group = mysql_query("SELECT DISTINCT g.id, g.name FROM groupes g, j_groupes_classes jgc WHERE (g.id = jgc.id_groupe and jgc.id_classe = '" . $id_classe ."') ORDER BY jgc.priorite, g.name");
$nombre_ligne = mysql_num_rows($call_group);

//=========================
// MODIF: boireaus
//echo "<table border = '1' cellpadding='5' cellspacing='0'>\n<tr><td><b>Matière</b></td>";
//echo "<table border = '1' cellpadding='5' cellspacing='0'>\n";
echo "<table border = '1' cellpadding='5' cellspacing='0' class='boireaus'>\n";
echo "<tr align='center'><td><b>Matière</b></td>\n";
//=========================
$j = 1;
while ($j < $nb_periode) {
	//=========================
	// MODIF: boireaus
	//echo "<td><b>".$nom_periode[$j]."</b></td>";
	echo "<td><b>".$nom_periode[$j]."</b><br />\n";
	//echo "<input type='button' name='coche_col_$j' id='id_coche_col_$j' value='Coche' onClick='coche($j,\"col\")' />/\n";
	//echo "<input type='button' name='decoche_col_$j' id='id_decoche_col_$j' value='Décoche' onClick='decoche($j,\"col\")' />\n";
	//echo "<input type='button' name='coche_col_$j' value='C' onClick='modif_case($j,\"col\",true)' />/\n";
	//echo "<input type='button' name='decoche_col_$j' value='D' onClick='modif_case($j,\"col\",false)' />\n";
	echo "<a href='javascript:modif_case($j,\"col\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
	echo "<a href='javascript:modif_case($j,\"col\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
	echo "</td>\n";
	//=========================
	$j++;
}
echo "<td>&nbsp;</td>\n";
echo "</tr>\n";

$nb_erreurs=0;
$i=0;
$alt=1;
while ($i < $nombre_ligne) {
	$id_groupe = mysql_result($call_group, $i, "id");
	$nom_groupe = mysql_result($call_group, $i, "name");
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td>".$nom_groupe;
	echo "</td>\n";
	$j = 1;
	while ($j < $nb_periode) {
		$test=mysql_query("SELECT 1=1 FROM j_eleves_groupes WHERE (" .
				"id_groupe = '" . $id_groupe . "' and " .
				"login = '" . $login_eleve . "' and " .
				"periode = '" . $j . "')");

		//$sql="SELECT * FROM j_eleves_classes WHERE login='$login_eleve' AND periode='$j'";
		$sql="SELECT * FROM j_eleves_classes WHERE login='$login_eleve' AND periode='$j' AND id_classe='$id_classe'";
		// CA NE VA PAS... SUR LES GROUPES A REGROUPEMENT, IL FAUT PRENDRE DES PRECAUTIONS...
		$res_test_class_per=mysql_query($sql);
		if(mysql_num_rows($res_test_class_per)==0){
			if (mysql_num_rows($test) == "0") {
				echo "<td>&nbsp;</td>\n";
			}
			else{
				$sql="SELECT DISTINCT id_classe FROM j_groupes_classes WHERE id_groupe='$id_groupe'";
				$res_grp=mysql_query($sql);
				$temoin="";
				while($lig_clas=mysql_fetch_object($res_grp)){
					$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$lig_clas->id_classe' AND login='$login_eleve' AND periode='$j'";
					$res_test_ele=mysql_query($sql);
					if(mysql_num_rows($res_test_ele)==1){
						$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
						$res_tmp=mysql_query($sql);
						$lig_tmp=mysql_fetch_object($res_tmp);
						$clas_tmp=$lig_tmp->classe;

						$temoin=$clas_tmp;
					}
				}

				if($temoin!=""){
					echo "<td><center>".$temoin."<input type='hidden' name=".$id_groupe."_".$j." value='checked' /></center></td>\n";
				}
				else{
					$msg_erreur="Cette case est validée et ne devrait pas l être. Validez le formulaire pour corriger.";
					echo "<td><center><a href='#' alt='$msg_erreur' title='$msg_erreur'><font color='red'>ERREUR</font></a></center></td>\n";
					$nb_erreurs++;
				}
			}
		}
		else{

			/*
			// Un autre test à faire:
			// Si l'élève est resté dans le groupe alors qu'il n'est plus dans cette classe pour la période
			$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='$j' AND login='$login_eleve'";
			*/

			//=========================
			// MODIF: boireaus
			if (mysql_num_rows($test) == "0") {
				//echo "<td><center><input type=checkbox name=".$id_groupe."_".$j." /></center></td>\n";
				echo "<td><center><input type=checkbox id=case".$i."_".$j." name=".$id_groupe."_".$j." onchange='changement();' /></center></td>\n";
			} else {
				//echo "<td><center><input type=checkbox name=".$id_groupe."_".$j." CHECKED /></center></td>\n";
				echo "<td><center><input type=checkbox id=case".$i."_".$j." name=".$id_groupe."_".$j." onchange='changement();' CHECKED /></center></td>\n";
			}
			//=========================
		}
		$j++;
	}
	//=========================
	// AJOUT: boireaus
	echo "<td>\n";
	//echo "<input type='button' name='coche_lig_$i' value='C' onClick='modif_case($i,\"lig\",true)' />/\n";
	//echo "<input type='button' name='decoche_lig_$i' value='D' onClick='modif_case($i,\"lig\",false)' />\n";
	echo "<a href='javascript:modif_case($i,\"lig\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
	echo "<a href='javascript:modif_case($i,\"lig\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
	echo "</td>\n";
	//=========================
	echo "</tr>\n";
	$i++;
}
echo "</table>\n";


//============================================
// AJOUT: boireaus
echo "<script type='text/javascript' language='javascript'>
	function modif_case(rang,type,statut){
		// type: col ou lig
		// rang: le numéro de la colonne ou de la ligne
		// statut: true ou false
		if(type=='col'){
			for(k=0;k<$nombre_ligne;k++){
				if(document.getElementById('case'+k+'_'+rang)){
					document.getElementById('case'+k+'_'+rang).checked=statut;
				}
			}
		}
		else{
			for(k=1;k<$nb_periode;k++){
				if(document.getElementById('case'+rang+'_'+k)){
					document.getElementById('case'+rang+'_'+k).checked=statut;
				}
			}
		}
		changement();
	}
</script>\n";

if($nb_erreurs>0){
	echo "<p style='color:red;'>Cet élève est affecté dans des groupes sur des périodes pour lesquelles il n'est pas dans la classe.<br />Pour supprimer l'élève de ces groupes, validez le présent formulaire.</p>\n";
}
//============================================
?>
<p align='center'><input type='submit' value='Enregistrer les modifications' /></p>
<input type=hidden name=id_classe value=<?php echo $id_classe;?> />
<input type=hidden name=login_eleve value=<?php echo $login_eleve;?> />
<input type=hidden name=is_posted value=1 />
<br />
</form>
<?php require("../lib/footer.inc.php");?>