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
$resultat_session = $session_gepi->security_check();
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

$msg_complement="";

$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
$classe = mysql_result($call_classe, "0", "classe");

if (isset($is_posted) and ($is_posted == 1)) {
	$gepiProfSuivi=getSettingValue("gepi_prof_suivi");

	$call_eleves = mysql_query("SELECT login FROM eleves ORDER BY nom, prenom");
	$nombreligne = mysql_num_rows($call_eleves);

	//=========================
	// AJOUT: boireaus 20071010
	$log_eleve=$_POST['log_eleve'];
	//$cpt_k=$_POST['cpt_k'];
	$regime_eleve=isset($_POST['regime_eleve']) ? $_POST['regime_eleve'] : NULL;
	$doublant_eleve=isset($_POST['doublant_eleve']) ? $_POST['doublant_eleve'] : NULL;
	//=========================

	$k = '0';
	while ($k < $nombreligne) {
		$pb = 'no';
		$login_eleve = mysql_result($call_eleves, $k, 'login');

		//=========================
		// MODIF: boireaus 20071010
		//$temp = $login_eleve."_item";
		//$item_login = isset($_POST[$temp])?$_POST[$temp]:NULL;

		// Récupération du numéro de l'élève dans les saisies:
		$num_eleve=-1;
		for($i=0;$i<count($log_eleve);$i++){
			if($login_eleve==$log_eleve[$i]){
				$num_eleve=$i;
				break;
			}
		}
		if($num_eleve!=-1){

			$item_login = isset($login_eleve[$num_eleve]) ? $login_eleve[$num_eleve] : NULL;

			//=========================

			$i="1";
			/*
			while ($i < $nb_periode) {
				$temp = "ajout_".$login_eleve."_".$i;
				$reg_login[$i] = isset($_POST[$temp])?$_POST[$temp]:NULL;
				$i++;
			}
			*/
			$reg_login = isset($_POST["ajout_eleve_".$num_eleve]) ? $_POST["ajout_eleve_".$num_eleve] : NULL;

			//=========================
			// MODIF: boireaus 20071010
			//if ($item_login == 'yes') {
			if (isset($log_eleve[$num_eleve])) {
			//=========================

				$reg_data = 'yes';

				//=========================
				// NOTE: boireaus 20071010
				// Variables devenues inutiles:
				$regime_login = "regime_".$login_eleve;
				$doublant_login = "doublant_".$login_eleve;
				//=========================

				//=========================
				// MODIF: boireaus 20071010
				//$reg_regime = isset($_POST[$regime_login])?$_POST[$regime_login]:NULL;
				//$reg_doublant = isset($_POST[$doublant_login])?$_POST[$doublant_login]:NULL;
				$reg_regime=isset($regime_eleve[$num_eleve]) ? $regime_eleve[$num_eleve] : NULL;
				$reg_doublant=isset($doublant_eleve[$num_eleve]) ? $doublant_eleve[$num_eleve] : NULL;
				//=========================

				$call_regime = mysql_query("SELECT * FROM j_eleves_regime WHERE login='$login_eleve'");
				$nb_test_regime = mysql_num_rows($call_regime);
				if ($nb_test_regime == 0) {
					$reg_data = mysql_query("INSERT INTO j_eleves_regime SET login='$login_eleve', doublant='$reg_doublant', regime='$reg_regime'");
					if (!($reg_data)) $reg_ok = 'no';
				} else {
					$reg_data = mysql_query("UPDATE j_eleves_regime SET doublant = '$reg_doublant', regime = '$reg_regime'  WHERE login='$login_eleve'");
					if (!($reg_data)) $reg_ok = 'no';
				}
			}
			$i="1";
			while ($i < $nb_periode) {
				//=========================
				// MODIF: boireaus 20071010
				if(isset($reg_login[$i])){
				//=========================
					if ($reg_login[$i] == 'yes') {
						if (mysql_num_rows(mysql_query("SELECT login FROM j_eleves_classes WHERE
						(login = '$login_eleve' and
						id_classe = '$id_classe' and
						periode = '$i')")) == 0) {
							$call_data = mysql_query("INSERT INTO j_eleves_classes VALUES('$login_eleve', '$id_classe', $i, '0')");
							if (!($reg_data))  {$reg_ok = 'no';}
						}


						// UPDATE: Ajouter l'élève à tous les groupes pour la période:
						$sql="SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe'";
						$res_liste_grp_classe=mysql_query($sql);
						if(mysql_num_rows($res_liste_grp_classe)>0){
							while($lig_tmp=mysql_fetch_object($res_liste_grp_classe)){
								$sql="SELECT 1=1 FROM j_eleves_groupes WHERE login='$login_eleve' AND id_groupe='$lig_tmp->id_groupe' AND periode='$i'";
								$test=mysql_query($sql);
								if(mysql_num_rows($test)==0){
									$sql="INSERT INTO j_eleves_groupes SET login='$login_eleve',id_groupe='$lig_tmp->id_groupe',periode='$i'";
									$insert_grp=mysql_query($sql);
								}
							}
						}

						$sql="SELECT DISTINCT cpe_login FROM j_eleves_cpe jecpe, j_eleves_classes jec
									WHERE (
										jec.id_classe='$id_classe' AND
										jecpe.e_login=jec.login AND
										jec.periode='$i'
									)";
						//echo "$sql<br />";
						$res_cpe=mysql_query($sql);
						if(mysql_num_rows($res_cpe)==1) {
							$sql="DELETE FROM j_eleves_cpe WHERE e_login='$login_eleve';";
							//echo "$sql<br />";
							$nettoyage=mysql_query($sql);

							$lig_tmp=mysql_fetch_object($res_cpe);
							$sql="INSERT INTO j_eleves_cpe SET cpe_login='$lig_tmp->cpe_login', e_login='$login_eleve';";
							//echo "$sql<br />";
							$insert_cpe=mysql_query($sql);
						}
						else {
							$msg_complement.="<br />L'élève $login_eleve n'a pas été associé à un CPE.";
						}

						$sql="SELECT DISTINCT professeur FROM j_eleves_professeurs jep
									WHERE (
										jep.id_classe='$id_classe'
									)";
						//echo "$sql<br />";
						$res_pp=mysql_query($sql);
						if(mysql_num_rows($res_pp)==1) {
							$sql="DELETE FROM j_eleves_professeurs WHERE login='$login_eleve';";
							//echo "$sql<br />";
							$nettoyage=mysql_query($sql);

							$lig_tmp=mysql_fetch_object($res_pp);
							$sql="INSERT INTO j_eleves_professeurs SET professeur='$lig_tmp->professeur', login='$login_eleve', id_classe='$id_classe';";
							//echo "$sql<br />";
							$insert_pp=mysql_query($sql);
						}
						else {
							$msg_complement.="<br />L'élève $login_eleve n'a pas été associé à un ".$gepiProfSuivi.".";
						}
					}
				}
				$i++;
			}
		}
		$k++;
	}

	if (($reg_data) == 'yes') {
	$msg = "L'enregistrement des données a été correctement effectué !";
	} else {
	$msg = "Il y a eu un problème lors de l'enregistrement !";
	}
	$msg.=$msg_complement;
}

// =================================
/*
// AJOUT: boireaus
$sql="SELECT id, classe FROM classes ORDER BY classe";
$res_class_tmp=mysql_query($sql);
if(mysql_num_rows($res_class_tmp)>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;
	while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			$temoin_tmp=1;
			if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}
	}
}
*/
// =================================
// AJOUT: boireaus
$chaine_options_classes="";
$sql="SELECT id, classe FROM classes ORDER BY classe";
$res_class_tmp=mysql_query($sql);
if(mysql_num_rows($res_class_tmp)>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;

	$cpt_classe=0;
	$num_classe=-1;

	while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
			$temoin_tmp=1;
			if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		else {
			$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
		}

		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}

		$cpt_classe++;
	}
}
// =================================


$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
$titre_page = "Gestion des classes | Ajout d'élèves à une classe";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

?>
<script type='text/javascript' language='javascript'>
/*
function CochePeriode() {
	nbParams = CochePeriode.arguments.length;
	for (var i=0;i<nbParams;i++) {
		theElement = CochePeriode.arguments[i];
		if (document.formulaire.elements[theElement])
			document.formulaire.elements[theElement].checked = true;
	}
}

function DecochePeriode() {
	nbParams = DecochePeriode.arguments.length;
	for (var i=0;i<nbParams;i++) {
		theElement = DecochePeriode.arguments[i];
		if (document.formulaire.elements[theElement])
			document.formulaire.elements[theElement].checked = false;
	}
}
*/
<?php
echo "function CocheLigne(ki) {
	for (var i=1;i<$nb_periode;i++) {
		if(document.getElementById('case_'+ki+'_'+i)){
			document.getElementById('case_'+ki+'_'+i).checked = true;
		}
	}
}
";

echo "function DecocheLigne(ki) {
	for (var i=1;i<$nb_periode;i++) {
		if(document.getElementById('case_'+ki+'_'+i)){
			document.getElementById('case_'+ki+'_'+i).checked = false;
		}
	}
}
";
?>
</script>

<form enctype="multipart/form-data" action="classes_ajout.php" name="form1" method=post>

<p class=bold>
<a href="classes_const.php?id_classe=<?php echo $id_classe;?>"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à la page de gestion des élèves</a>

<?php
if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec'>Classe précédente</a>";}
if($chaine_options_classes!="") {

	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_classe').selectedIndex=$num_classe;
			}
		}
	}
</script>\n";


	echo " | <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
	echo $chaine_options_classes;
	echo "</select>\n";
}
if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv'>Classe suivante</a>";}
?>
</p>
</form>

<form enctype="multipart/form-data" action="classes_ajout.php" name="formulaire" method=post>
<p><b>Ajout d'élèves à la classe de <?php echo $classe; ?></b><br />Liste des élèves non affectés à une classe :</p>

<?php

$call_eleves = mysql_query("SELECT * FROM eleves ORDER BY nom, prenom");
$nombreligne = mysql_num_rows($call_eleves);
if ($nombreligne == '0') {
	echo "<p>Il n'y a pas d'élèves actuellement dans la base.</p>\n";
} else {
	$eleves_non_affectes = 'no';
	echo "<table class='boireaus' cellpadding='5'>\n";
	echo "<tr>\n";
	echo "<th><p><b>Nom Prénom </b></p></th>\n<th><p><b>Régime</b></p></th>\n<th><p><b>Redoublant</b></p></th>\n";
	$i="1";
	while ($i < $nb_periode) {
		echo "<th><p><b>Ajouter per. $i</b></p></th>\n";
		$i++;
	}

	//echo "<th><b><center>cocher / décocher <br/>toutes périodes</center></b></p></th>\n";
	echo "<th><p style='font-weight:bold; text-align:center;'>cocher / décocher <br />toutes périodes</p></th>\n";
	echo "</tr>";
	$k = '0';
	//=========================
	// AJOUT: boireaus 20071010
	// Compteur des élèves effectivement non affectés:
	$ki=0;
	//=========================
	$alt=1;
	While ($k < $nombreligne) {
		$login_eleve = mysql_result($call_eleves, $k, 'login');
		$nom_eleve = mysql_result($call_eleves, $k, 'nom');
		$prenom_eleve = mysql_result($call_eleves, $k, 'prenom');
		$call_regime = mysql_query("SELECT * FROM j_eleves_regime WHERE login='$login_eleve'");
		$doublant = @mysql_result($call_regime, 0, 'doublant');
		if ($doublant == '') {$doublant = '-';}
		$regime = @mysql_result($call_regime, 0, 'regime');
		if ($regime == '') {$regime = 'd/p';}
		$i="1";
		while ($i < $nb_periode) {
			$ajout_login[$i] = "ajout_".$login_eleve."_".$i;
			$i++;
		}
		//=========================
		// NOTE: boireaus 20071010
		// Ces variables ne sont plus utiles:
		$item_login = $login_eleve."_item";
		$regime_login = "regime_".$login_eleve;
		$doublant_login = "doublant_".$login_eleve;
		//=========================
		$inserer_ligne = 'no';
		$call_data = mysql_query("SELECT id_classe FROM j_eleves_classes WHERE login = '$login_eleve'");
		$test = mysql_num_rows($call_data);
		if ($test == 0) {
			$inserer_ligne = 'yes';
			$eleves_non_affectes = 'yes';
			$i="1";
			while ($i < $nb_periode) {
				$nom_classe[$i] = 'vide';
				$i++;
			}
		} else {
			$id_classe_eleve = mysql_result($call_data, 0, "id_classe");
			$query_periode_max = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe_eleve'");
			$periode_max = mysql_num_rows($query_periode_max) + 1 ;
			// si l'élève est déjà dans une classe dont le nombre de périodes est différent du nombre de périodes de la classe selctionnée, on ne fait rien. Dans la cas contraire :
			if ($periode_max == $nb_periode) {
				$i = '1';
				while ($i < $nb_periode) {
					$call_data2 = mysql_query("SELECT id_classe FROM j_eleves_classes WHERE (login = '$login_eleve' and periode = '$i')");
					$test2 = mysql_num_rows($call_data2);
					if ($test2 == 0) {
						// l'élève n'est affecté à aucune classe pour cette période
						$inserer_ligne = 'yes';
						$eleves_non_affectes = 'yes';
						$nom_classe[$i] = 'vide';
					} else {
						$idd_classe = mysql_result($call_data2, 0, "id_classe");
						$call_classe = mysql_query("SELECT classe FROM classes WHERE (id = '$idd_classe')");
						$nom_classe[$i] = mysql_result($call_classe, 0, "classe");
					}
					$i++;
				}
			}
		}
		if ($inserer_ligne == 'yes') {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'><td>\n";
			//=========================
			// MODIF: boireaus 20071010
			/*
			echo "<input type='hidden' name=$item_login value='yes' />\n";
			//echo "<tr><td><p>$nom_eleve $prenom_eleve</p></td>\n";
			echo "<p>$nom_eleve $prenom_eleve</p></td>\n";
			echo "<td><p>Ext.|Int.|D/P|I-ext.<br /><input type='radio' name='$regime_login' value='ext.'";
			if ($regime == 'ext.') { echo " checked ";}
			echo " />\n";
			echo "&nbsp;&nbsp;&nbsp;<input type=radio name='$regime_login' value='int.'";
			if ($regime == 'int.') { echo " checked ";}
			echo " />\n";
			echo "&nbsp;&nbsp;&nbsp;<input type=radio name='$regime_login' value='d/p' ";
			if ($regime == 'd/p') { echo " checked ";}
			echo " />\n";
			echo "&nbsp;&nbsp;&nbsp;<input type=radio name='$regime_login' value='i-e'";
			if ($regime == 'i-e') { echo " checked ";}
			echo " />\n";

			//echo "</p></td><td><p><center><INPUT TYPE=CHECKBOX NAME='$doublant_login' VALUE='R'";
			echo "</p></td>\n<td><p align='center'><input type='checkbox' name='$doublant_login' value='R'";
			*/
			echo "<input type='hidden' name='log_eleve[$ki]' value=\"$login_eleve\" />\n";
			echo "<p>".strtoupper($nom_eleve)." $prenom_eleve</p></td>\n";

			echo "<td><p>Ext.|Int.|D/P|I-ext.<br /><input type='radio' name='regime_eleve[$ki]' value='ext.'";
			if ($regime == 'ext.') { echo " checked ";}
			echo " onchange='changement()' />\n";
			echo "&nbsp;&nbsp;&nbsp;<input type=radio name='regime_eleve[$ki]' value='int.'";
			if ($regime == 'int.') { echo " checked ";}
			echo " onchange='changement()' />\n";
			echo "&nbsp;&nbsp;&nbsp;<input type=radio name='regime_eleve[$ki]' value='d/p' ";
			if ($regime == 'd/p') { echo " checked ";}
			echo " onchange='changement()' />\n";
			echo "&nbsp;&nbsp;&nbsp;<input type=radio name='regime_eleve[$ki]' value='i-e'";
			if ($regime == 'i-e') { echo " checked ";}
			echo " onchange='changement()' />\n";
			//echo "</p></td><td><p><center><INPUT TYPE=CHECKBOX NAME='$doublant_login' VALUE='R'";
			echo "</p></td>\n<td><p align='center'><input type='checkbox' name='doublant_eleve[$ki]' value='R'";
			//=========================
			if ($doublant == 'R') { echo " checked ";}
			echo " onchange='changement()' />";

			//echo "</center></p></td>";
			echo "</p></td>\n";
			$i="1";
			while ($i < $nb_periode) {
				echo "<td><p align='center'>";
				if ($nom_classe[$i] == 'vide') {
					//=========================
					// MODIF: boireaus 20071010
					//echo "<input type='checkbox' name='$ajout_login[$i]' value='yes' />";
					echo "<input type='checkbox' name='ajout_eleve_".$ki."[$i]' id='case_".$ki."_".$i."' value='yes' onchange='changement()' />";
					//=========================
				} else {
					echo "$nom_classe[$i]";
				}
				echo "</p></td>\n";
				$i++;
			}
			$elementlist = null;
			for ($i=1;$i<=sizeof($ajout_login);$i++) {
			//echo $ajout_login[$i]."<br>";
			$elementlist .= "'".$ajout_login[$i]."',";
			}
			$elementlist = substr($elementlist, 0, -1);
			//echo "<td><center><a href=\"javascript:CochePeriode($elementlist)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecochePeriode($elementlist)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a></center></td>\n";
			echo "<td><center><a href=\"javascript:CocheLigne($ki);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne($ki);changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a></center></td>\n";
			echo "</tr>\n";

			//=========================
			// AJOUT: boireaus 20071010
			$ki++;
			//=========================
		}
		$k++;
	}
	echo "</table>\n";

	if ($eleves_non_affectes == 'no') {
		echo "<p>Il n'y a aucun élève de disponible à ajouter !";
	} else {
		echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";
	}
}
?>
<input type='hidden' name='id_classe' value='<?php echo $id_classe;?>' />
<input type='hidden' name='is_posted' value='1' />
</form>
<p><br /></p>
<?php require("../lib/footer.inc.php");?>
