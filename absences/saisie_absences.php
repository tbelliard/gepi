<?php

/*
* $Id: saisie_absences.php 6665 2011-03-17 17:33:19Z crob $
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

include "../lib/periodes.inc.php";

// Vérifications
if((!isset($id_classe))||(!isset($id_classe))) {
	$msg="Il faut choisir une classe et une période.";
	header("Location:index.php?msg=$msg");
}

$acces="n";
if($ver_periode[$periode_num]=="N") {
	$acces="y";
}
elseif(($ver_periode[$periode_num]=="P")&&($_SESSION['statut']=='secours')) {
	$acces="y";
}

if($acces=="n") {
	$msg="La période $periode_num est close pour cette classe.";
	header("Location:index.php?id_classe=$id_classe&msg=$msg");
}

if (isset($_POST['is_posted']) and $_POST['is_posted'] == "yes") {
	check_token();

	if ($_SESSION['statut'] == "cpe") {
		$quels_eleves = mysql_query("SELECT e.login FROM eleves e, j_eleves_classes c, j_eleves_cpe j WHERE (c.id_classe='$id_classe' AND j.e_login = c.login AND e.login = j.e_login AND j.cpe_login = '".$_SESSION['login'] . "' AND c.periode = '$periode_num')");
	} else {
		$quels_eleves = mysql_query("SELECT e.login FROM eleves e, j_eleves_classes c WHERE ( c.id_classe='$id_classe' AND c.login = e.login AND c.periode='$periode_num')");
	}

	//=========================
	// AJOUT: boireaus 20071010
	$log_eleve=$_POST['log_eleve'];
	$nb_abs_ele=$_POST['nb_abs_ele'];
	$nb_nj_ele=$_POST['nb_nj_ele'];
	$nb_retard_ele=$_POST['nb_retard_ele'];
	//$app_ele=$_POST['app_ele'];
	//=========================

	$quels_eleves = mysql_query("SELECT e.login FROM eleves e, j_eleves_classes c WHERE (c.id_classe='$id_classe' AND e.login = c.login AND c.periode='$periode_num')");
	$lignes = mysql_num_rows($quels_eleves);
	$j = '0';
	while($j < $lignes) {
		$reg_eleve_login = mysql_result($quels_eleves, $j, "login");

		//=========================
		// AJOUT: boireaus 20071007
		// Récupération du numéro de l'élève dans les saisies:
		$num_eleve=-1;
		for($i=0;$i<count($log_eleve);$i++){
			if($reg_eleve_login==$log_eleve[$i]){
				$num_eleve=$i;
				break;
			}
		}
		if($num_eleve!=-1){
			//=========================

			//=========================
			// MODIF: boireaus 20071007
			/*
			$nom_log_nb_abs = $reg_eleve_login."_nb_abs";
			$nb_absences = $_POST[$nom_log_nb_abs];

			$nom_log_nb_nj = $reg_eleve_login."_nb_nj";
			$nb_nj = $_POST[$nom_log_nb_nj];

			$nom_log_nb_retard = $reg_eleve_login."_nb_retard";
			$nb_retard = $_POST[$nom_log_nb_retard];

			$nom_log_ap = $reg_eleve_login."_ap";
			$ap = $_POST[$nom_log_ap];
			*/

			$nb_absences=$nb_abs_ele[$num_eleve];
			$nb_nj=$nb_nj_ele[$num_eleve];
			$nb_retard=$nb_retard_ele[$num_eleve];
			//$ap=$app_ele[$num_eleve];
			//$ap = traitement_magic_quotes(corriger_caracteres(html_entity_decode_all_version($ap)));


			$app_ele_courant="app_eleve_".$num_eleve;
			//echo "\$app_ele_courant=$app_ele_courant<br />";
			if (isset($NON_PROTECT[$app_ele_courant])){
				$ap = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$app_ele_courant]));
			}
			else{
				$ap = "";
			}
			//echo "\$ap=$ap<br />";

			// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
			//$ap=my_ereg_replace('(\\\r\\\n)+',"\r\n",$ap);
			$ap=preg_replace('/(\\\r\\\n)+/',"\r\n",$ap);
			//=========================

			if (!(preg_match ("/^[0-9]{1,}$/", $nb_absences))) {
					$nb_absences = '';
				}
				if (!(preg_match ("/^[0-9]{1,}$/", $nb_nj))) {
					$nb_nj = '';
				}
				if (!(preg_match ("/^[0-9]{1,}$/", $nb_retard))) {
					$nb_retard = '';
				}

				$test_eleve_nb_absences_query = mysql_query("SELECT * FROM absences WHERE (login='$reg_eleve_login' AND periode='$periode_num')");
				$test_nb = mysql_num_rows($test_eleve_nb_absences_query);
				if ($test_nb != "0") {
					$register = mysql_query("UPDATE absences SET nb_absences='$nb_absences', non_justifie='$nb_nj', nb_retards='$nb_retard', appreciation='$ap' WHERE (login='$reg_eleve_login' AND periode='$periode_num')");
				} else {
					$register = mysql_query("INSERT INTO absences SET login='$reg_eleve_login', periode='$periode_num',nb_absences='$nb_absences',non_justifie='$nb_nj', nb_retards='$nb_retard',appreciation='$ap'");
				}
			if (!$register) {
					$msg = "Erreur lors de l'enregistrement des données";
			}
		}
		$j++;
	}
	//$affiche_message = 'yes';
	if(!isset($msg)){$msg='Les modifications ont été enregistrées !';}
}
$themessage  = 'Des champs ont été modifiés. Voulez-vous vraiment quitter sans enregistrer ?';
//$message_enregistrement = 'Les modifications ont été enregistrées !';

$javascript_specifique = "saisie/scripts/js_saisie";
//**************** EN-TETE *****************
$titre_page = "Saisie des absences";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<script type="text/javascript" language="javascript">
change = 'no';
</script>

<form enctype="multipart/form-data" action="saisie_absences.php" method="post">
<?php
echo add_token_field(true);
?>
<p class="bold">
<a href="index.php?id_classe=<?php echo $id_classe; ?>" onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Choisir une autre période</a> |
<a href="index.php" onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">Choisir une autre classe</a> | <input type="submit" value="Enregistrer" /> | <a href="<?php echo "consulter_absences.php?id_classe=$id_classe&amp;periode_num=$periode_num";?>">Consulter les absences de la classe</a></p>


<?php
$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
$classe = mysql_result($call_classe, "0", "classe");
?>
<p><b>Classe de <?php echo "$classe"; ?> - Saisie des absences : <?php $temp = strtolower($nom_periode[$periode_num]); echo "$temp"; ?></b>
<br />
<!--table border=1 cellspacing=2 cellpadding=5-->
<table class='boireaus' cellspacing='2' cellpadding='5'>
<tr>
	<th align='center'><b>Nom Prénom</b></th>
	<th align='center'><b>Nb. total de 1/2 journées d'absence</b></th>
	<th align='center'><b>Nb. absences non justifiées</b></th>
	<th align='center'><b>Nb. de retard</b></th>
	<th align='center'><b>Observations</b></th>
</tr>
<?php
if ($_SESSION['statut'] == "cpe") {
		$appel_donnees_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c, j_eleves_cpe j WHERE (c.id_classe='$id_classe' AND j.e_login = c.login AND e.login = j.e_login AND j.cpe_login = '".$_SESSION['login'] . "' AND c.periode = '$periode_num') order by e.nom, e.prenom");
	} else {
		$appel_donnees_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c WHERE ( c.id_classe='$id_classe' AND c.login = e.login AND c.periode='$periode_num') order by e.nom, e.prenom");
}

$nombre_lignes = mysql_num_rows($appel_donnees_eleves);
$i = '0';
$num_id=10;
$alt=1;
$chaine_test_vocabulaire="";
while($i < $nombre_lignes) {
	$current_eleve_login = mysql_result($appel_donnees_eleves, $i, "login");
	$current_eleve_absences_query = mysql_query("SELECT * FROM  absences WHERE (login='$current_eleve_login' AND periode='$periode_num')");
	$current_eleve_nb_absences = @mysql_result($current_eleve_absences_query, 0, "nb_absences");
	$current_eleve_nb_nj = @mysql_result($current_eleve_absences_query, 0, "non_justifie");
	$current_eleve_nb_retards = @mysql_result($current_eleve_absences_query, 0, "nb_retards");
	$current_eleve_ap_absences = @mysql_result($current_eleve_absences_query, 0, "appreciation");
	$current_eleve_nom = mysql_result($appel_donnees_eleves, $i, "nom");
	$current_eleve_prenom = mysql_result($appel_donnees_eleves, $i, "prenom");
	$current_eleve_login_nb = $current_eleve_login."_nb_abs";
	$current_eleve_login_nj = $current_eleve_login."_nb_nj";
	$current_eleve_login_retard = $current_eleve_login."_nb_retard";
	$current_eleve_login_ap = $current_eleve_login."_ap";

	$alt=$alt*(-1);
	echo "<tr class='lig$alt'><td align='center'>".strtoupper($current_eleve_nom)." $current_eleve_prenom\n";
	//=========================
	// MODIF: boireaus 20071010
	echo "<input type='hidden' name='log_eleve[$i]' value='$current_eleve_login' />\n";
	echo "</td>\n";
	/*
	echo "<td><input id=\"".$num_id."\" onKeyDown=\"clavier(this.id,event);\" type=text size=4 name=$current_eleve_login_nb value=\"".$current_eleve_nb_absences."\" onchange=\"changement()\" /></td>\n";
	echo "<td><input id=\"1".$num_id."\" onKeyDown=\"clavier(this.id,event);\" type=text size=4 name=$current_eleve_login_nj value=\"".$current_eleve_nb_nj."\" onchange=\"changement()\" /></td>\n";
	echo "<td><input id=\"2".$num_id."\" onKeyDown=\"clavier(this.id,event);\" type=text size=4 name=$current_eleve_login_retard value=\"".$current_eleve_nb_retards."\" onchange=\"changement()\" /></td>\n";
	echo "<td><textarea id=\"3".$num_id."\" onKeyDown=\"clavier(this.id,event);\" onchange=\"changement()\" name=$current_eleve_login_ap rows=2 cols=50  wrap=\"virtual\">$current_eleve_ap_absences</textarea></td></tr>\n";
	*/
	echo "<td align='center'><input id=\"n".$num_id."\" onKeyDown=\"clavier(this.id,event);\" type='text' size='4' name='nb_abs_ele[$i]' value=\"".$current_eleve_nb_absences."\" onchange=\"changement()\" /></td>\n";
	echo "<td align='center'><input id=\"n1".$num_id."\" onKeyDown=\"clavier(this.id,event);\" type='text' size='4' name='nb_nj_ele[$i]' value=\"".$current_eleve_nb_nj."\" onchange=\"changement()\" /></td>\n";
	echo "<td align='center'><input id=\"n2".$num_id."\" onKeyDown=\"clavier(this.id,event);\" type='text' size='4' name='nb_retard_ele[$i]' value=\"".$current_eleve_nb_retards."\" onchange=\"changement()\" /></td>\n";
	//echo "<td><textarea id=\"n3".$num_id."\" onKeyDown=\"clavier(this.id,event);\" onchange=\"changement()\" name='app_ele[$i]' rows='2' cols='50'  wrap=\"virtual\">$current_eleve_ap_absences</textarea></td></tr>\n";
	echo "<td>\n";
	echo "<textarea id=\"n3".$num_id."\" onKeyDown=\"clavier(this.id,event);\" onchange=\"changement()\" name='no_anti_inject_app_eleve_$i' rows='2' cols='50'  wrap=\"virtual\" ";

	echo "onblur=\"ajaxVerifAppreciations('".$current_eleve_login."', '".$id_classe."', 'n3".$num_id."');\"";
	$chaine_test_vocabulaire.="ajaxVerifAppreciations('".$current_eleve_login."', '".$id_classe."', 'n3".$num_id."');\n";

	echo ">$current_eleve_ap_absences</textarea>\n";

	// Espace pour afficher les éventuelles fautes de frappe
	echo "<div id='div_verif_n3".$num_id."' style='color:red;'></div>\n";

	echo "</td>\n";
	echo "</tr>\n";
	//=========================
	$i++;
	$num_id++;
}

?>
</table>
<input type="hidden" name="is_posted" value="yes" />
<input type="hidden" name="id_classe" value=<?php echo "$id_classe";?> />
<input type="hidden" name="periode_num" value=<?php echo "$periode_num";?> />
<center><div id="fixe"><input type="submit" value="Enregistrer" /></div></center>
</form>

<?php

echo "<p>Il est impératif que vous ne laissiez pas de 'champ absence', 'absence_non_justifiee', 'retard' vide.<br />
Un champ retard vide n'est pas compris comme zéro retard, mais comme une absence de remplissage du champ.<br />
Si vous n'avez rempli que les champs non nuls, vous pouvez compléter d'un coup ci-dessous&nbsp;:<br />\n";
echo "<a href='javascript:complete_a_zero_champs_vides()'>Compléter les champs vides par des zéros</a>";
echo "</p>\n";

echo "<script type='text/javascript'>\n";

if((isset($chaine_test_vocabulaire))&&($chaine_test_vocabulaire!="")) {
	echo $chaine_test_vocabulaire;
}

echo "
function complete_a_zero_champs_vides() {
	for(i=10;i<$num_id;i++) {
		if(document.getElementById('n'+i)) {
			if(document.getElementById('n'+i).value=='') {
				document.getElementById('n'+i).value=0;
			}
		}

		if(document.getElementById('n1'+i)) {
			if(document.getElementById('n1'+i).value=='') {
				document.getElementById('n1'+i).value=0;
			}
		}

		if(document.getElementById('n2'+i)) {
			if(document.getElementById('n2'+i).value=='') {
				document.getElementById('n2'+i).value=0;
			}
		}
	}

	changement();
}
</script>\n";
?>
<p><br /></p>
<?php require "../lib/footer.inc.php";?>