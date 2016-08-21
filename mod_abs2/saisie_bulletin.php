<?php
/*
* $Id$
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if ((getSettingValue("active_module_absence")!='2')||(!getSettingAOui('active_bulletins'))) {
	die("Le module n'est pas activé.");
}

include_once 'lib/function.php';

$msg="";

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);

if((isset($id_classe))&&(isset($num_periode))&&(getSettingAOui("abs2_import_manuel_bulletin"))) {
	if(etat_verrouillage_classe_periode($id_classe, $num_periode)=="N") {
		header("Location: ../absences/saisie_absences.php?id_classe=$id_classe&periode_num=$num_periode");
	}
	else {
		header("Location: ../absences/consulter_absences.php?id_classe=$id_classe&periode_num=$num_periode");
	}
	die();
}

if(isset($_POST['enregistrement_saisie'])) {
	check_token();

	if(etat_verrouillage_classe_periode($id_classe, $num_periode)!="N") {
		$msg="La période est close.<br />";
	}
	else {
		$msg="";

		$tab_login=array();
		if (getSettingValue('GepiAccesAbsTouteClasseCpe')=='yes') {
			$sql="SELECT login FROM j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND periode='$num_periode';";
		} else {
			$sql="SELECT login FROM j_eleves_classes jec, j_eleves_cpe jecpe WHERE (jec.id_classe='$id_classe' AND periode='$num_periode' AND jecpe.e_login=jec.login AND jecpe.cpe_login = '".$_SESSION['login']."';";
		}
		//echo "$sql<br />";
		$res_ele = mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig_ele=mysqli_fetch_object($res_ele)) {
			$tab_login[]=$lig_ele->login;
		}

		$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : array();

		$nb_reg=0;
		$nb_err=0;
		for($loop=0;$loop<count($login_eleve);$loop++) {
			if(!in_array($login_eleve[$loop], $tab_login)) {
				$msg.="Enregistrement non effectué pour l'élève ".get_nom_prenom_eleve($login_eleve[$loop]).".<br />";
			}
			else {
				$app_ele_courant="app_eleve_".$loop;
				//echo "\$app_ele_courant=$app_ele_courant<br />";
				if (isset($NON_PROTECT[$app_ele_courant])){
					$ap = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$app_ele_courant]));
				}
				else{
					$ap = "";
				}

				$ap=nettoyage_retours_ligne_surnumeraires($ap);

				$sql="SELECT * FROM absences WHERE (login='".$login_eleve[$loop]."' AND periode='$num_periode')";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$sql="UPDATE absences SET appreciation='$ap' WHERE (login='".$login_eleve[$loop]."' AND periode='$num_periode');";
				} else {
					$sql="INSERT INTO absences SET login='".$login_eleve[$loop]."', periode='$num_periode', appreciation='$ap';";
				}
				//echo "$sql<br />";
				$register = mysqli_query($GLOBALS["mysqli"], $sql);
				if (!$register) {
					$nb_err++;
				}
				else {
					$nb_reg++;
				}
			}
		}

		$msg.="$nb_reg appréciation(s) enregistrée(s) ou mise(s) à jour.<br />";
		if($nb_err>0) {
			$msg.="$nb_err erreur(s) lors de l'opération.<br />";
		}
	}
}

$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
//$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";

$javascript_specifique[] = "saisie/scripts/js_saisie";

$javascript_specifique[] = "lib/tablekit";
//$dojo=true;
$utilisation_tablekit="ok";
//**************** EN-TETE *****************
$titre_page = "Bulletins : Saisie abs";
require_once("../lib/header.inc.php");
//**************** EN-TETE *****************
include('menu_abs2.inc.php');
include('menu_bilans.inc.php');

//debug_var();

?>
<div id="contain_div" class="css-panes">

<?php

if((!isset($id_classe))||(!isset($num_periode))) {


	if (getSettingValue('GepiAccesAbsTouteClasseCpe')=='yes') {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe;";
	} else {
		$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_cpe e, j_eleves_classes jc WHERE (e.cpe_login = '".$_SESSION['login']."' AND jc.login = e.e_login AND c.id = jc.id_classe)  ORDER BY classe;";
	}
	$calldata = mysqli_query($GLOBALS["mysqli"], $sql);
	$nombreligne = mysqli_num_rows($calldata);

	echo "<p>Total : $nombreligne classe";
	if($nombreligne>1){echo "s";}
	echo " - ";
	echo "Cliquez sur la classe pour laquelle vous souhaitez saisir les absences :</p>\n";
	if (!getSettingAOui('GepiAccesAbsTouteClasseCpe')) {
		echo "<p>Remarque : s'affichent toutes les classes pour lesquelles vous êtes responsable du suivi d'au moins un ".$gepiSettings['denomination_eleve']." de la classe.</p>\n";
	}

	while($lig_classe=mysqli_fetch_object($calldata)) {
		$tab_id_classe[]=$lig_classe->id;

		echo "<p onmouseover=\"this.style.backgroundColor='white'\" onmouseout=\"this.style.backgroundColor=''\"><span class='bold'>".$lig_classe->classe."&nbsp;:</span> ";
		$sql="SELECT * FROM periodes WHERE id_classe='".$lig_classe->id."' ORDER BY num_periode;";
		$res_per = mysqli_query($GLOBALS["mysqli"], $sql);
		$cpt=0;
		while($lig_per=mysqli_fetch_object($res_per)) {
			if($cpt>0) {
				echo " - ";
			}
			echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=".$lig_classe->id."&amp;num_periode=".$lig_per->num_periode."'>";
			if($lig_per->verouiller=="N") {
				echo "<img src='../images/edit16.png' class='icone16' alt='Saisir' />&nbsp;";
			}
			else {
				echo "<img src='../images/icons/chercher.png' class='icone16' alt='Consulter' />&nbsp;";
			}
			echo "<span style='color:".$couleur_verrouillage_periode[$lig_per->verouiller]."' title=\"Période ".$traduction_verrouillage_periode[$lig_per->verouiller]."
".$explication_verrouillage_periode[$lig_per->verouiller]."\">".$lig_per->nom_periode."</span></a>";
			$cpt++;
		}
		echo "</p>\n";
	}

	echo "</div>\n";
	require_once("../lib/footer.inc.php");
	die();
}

//=========================================================

// Classe et période sont choisies

$etat_periode=etat_verrouillage_classe_periode($id_classe, $num_periode);

echo "<p><a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe/période</a></p>";

if (getSettingValue('GepiAccesAbsTouteClasseCpe')!='yes') {
	$sql="SELECT 1=1 FROM classes c, j_eleves_cpe e, j_eleves_classes jc WHERE (e.cpe_login = '".$_SESSION['login']."' AND jc.login = e.e_login AND c.id = jc.id_classe AND c.id='$id_classe');";
}
$test = mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {

	echo "<p style='color:red'>Vous n'avez pas accès à cette classe.</p>";

	echo "</div>\n";
	require_once("../lib/footer.inc.php");
	die();
}

echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		<p class='bold'>Classe de ".get_nom_classe($id_classe)." en période ".$num_periode."</p>

		".add_token_field(true)."
		<input type='hidden' name='enregistrement_saisie' value='y' />
		<input type='hidden' name='id_classe' value='".$id_classe."' />
		<input type='hidden' name='num_periode' value='".$num_periode."' />

		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th>Élève</th>
					<th title=\"Nombre d'absences\">Nb.abs</th>
					<th title=\"Nombre d'absences non justifiées\">Nb.nj</th>
					<th title=\"Nombre de retards\">Nb.ret</th>
					<th>Appréciation</th>
				</tr>
			</thead>
			<tbody>";

$cpt=0;
$num_id=10;
$chaine_test_vocabulaire="";
$sql="SELECT e.nom, e.prenom, e.login FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND jec.periode='$num_periode' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
while($lig_ele=mysqli_fetch_object($res_ele)) {
	$eleve = EleveQuery::create()->findOneByLogin($lig_ele->login);
	if ($eleve != null) {
		$current_eleve_absences = strval($eleve->getDemiJourneesAbsenceParPeriode($num_periode)->count());
		$current_eleve_nj = strval($eleve->getDemiJourneesNonJustifieesAbsenceParPeriode($num_periode)->count());
		$current_eleve_retards = strval($eleve->getRetardsParPeriode($num_periode)->count());

		$sql="SELECT * FROM absences WHERE (login='".$lig_ele->login."' AND periode='$num_periode');";
		//echo "$sql< br />";
		$current_eleve_absences_query = mysqli_query($GLOBALS["mysqli"], $sql);
		$current_eleve_appreciation_absences_objet = $current_eleve_absences_query->fetch_object();
		$current_eleve_appreciation_absences = '';
		if ($current_eleve_appreciation_absences_objet) { 
			$current_eleve_appreciation_absences = $current_eleve_appreciation_absences_objet->appreciation;
		}
	}

	if($etat_periode=="N") {
		$chaine_test_vocabulaire.="ajaxVerifAppreciations('".$lig_ele->login."', '".$id_classe."', 'n3".$num_id."');\n";

		echo "
				<tr>
					<td>".casse_mot($lig_ele->nom, 'maj')." ".casse_mot($lig_ele->prenom, 'majf2')."</td>
					<td>$current_eleve_absences</td>
					<td>$current_eleve_nj</td>
					<td>$current_eleve_retards</td>
					<td>
						<input type='hidden' name='login_eleve[$cpt]' value='".$lig_ele->login."' />
						<textarea id=\"n3".$num_id."\" name='no_anti_inject_app_eleve_$cpt' rows='2' cols='50'  wrap=\"virtual\" 
											onKeyDown=\"clavier(this.id,event);\" 
											onchange=\"changement()\" 
											onblur=\"ajaxVerifAppreciations('".$lig_ele->login."_t".$num_periode."', '".$id_classe."', 'n3".$num_id."');\">$current_eleve_appreciation_absences</textarea>
						<div id='div_verif_n3".$num_id."' style='color:red;'></div>
					</td>
				</tr>";
	}
	else {
		echo "
				<tr>
					<td>".casse_mot($lig_ele->nom, 'maj')." ".casse_mot($lig_ele->prenom, 'majf2')."</td>
					<td>$current_eleve_absences</td>
					<td>$current_eleve_nj</td>
					<td>$current_eleve_retards</td>
					<td>".nl2br($current_eleve_appreciation_absences)."</td>
				</tr>";
	}

	$cpt++;
	$num_id++;
}
echo "
			</tbody>
		</table>";

	if($etat_periode=="N") {
		echo "
			<p><input type='submit' value='Enregistrer' /></p>";
	}
	echo "
	</fieldset>
</form>

<script type='text/javascript'>\n";

if((isset($chaine_test_vocabulaire))&&($chaine_test_vocabulaire!="")) {
	echo $chaine_test_vocabulaire;
}

echo "</script>\n";

echo "</div>\n";

require_once("../lib/footer.inc.php");
?>
