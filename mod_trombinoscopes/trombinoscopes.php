<?php
/*
*$Id$
*
* Copyright 2001, 2002 Thomas Belliard, Laurent Delineau, Edouard Hue,Eric Lebrun, Christian Chapel
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

function classe_de($id_classe_eleve)
		{
		include("../secure/connect.inc.php");
			$requete_classe_eleve ="SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.classe, ".$prefix_base."classes.id, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."eleves.login='".$id_classe_eleve."' AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id";
			$execution_classe_eleve = mysql_query($requete_classe_eleve) or die('Erreur SQL !'.$requete_classe_eleve.'<br />'.mysql_error());
			$data_classe_eleve = mysql_fetch_array($execution_classe_eleve);
			$id_classe_eleve = $data_classe_eleve['nom_complet'];
		return($id_classe_eleve);
		}

function annee_en_cours_t($date)
{
	$date = explode('-', $date);
	if (empty($annee_d)) {if ($date[1] < 8) {$annee_d = $date[0] - 1;} else {$annee_d = $date[0];}}
	if (empty($annee_f)) {if ($date[1] >= 8){$annee_f = $date[0] + 1;} else {$annee_f = $date[0];}}
	//Annee en cours
	$annee_en_cours = $annee_d."-".$annee_f;
	return($annee_en_cours);
}

function redimensionne_image($photo)
{
	// prendre les informations sur l'image
	$info_image = getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur = $info_image[0];
	$hauteur = $info_image[1];
	// largeur et/ou hauteur maximum à afficher
	if(basename($_SERVER['PHP_SELF'],".php") === "trombi_impr") {
		// si pour impression
		$taille_max_largeur = getSettingValue("l_max_imp_trombinoscopes");
		$taille_max_hauteur = getSettingValue("h_max_imp_trombinoscopes");
	} else {
	// si pour l'affichage écran
		$taille_max_largeur = getSettingValue("l_max_aff_trombinoscopes");
		$taille_max_hauteur = getSettingValue("h_max_aff_trombinoscopes");
	}

	// calcule le ratio de redimensionnement
	$ratio_l = $largeur / $taille_max_largeur;
	$ratio_h = $hauteur / $taille_max_hauteur;
	$ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

	// définit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur = $largeur / $ratio;
	$nouvelle_hauteur = $hauteur / $ratio;

	return array($nouvelle_largeur, $nouvelle_hauteur);
}

if (empty($_GET['etape']) and empty($_POST['etape'])) { $etape = '1'; }
	else { if (isset($_GET['etape'])) {$etape=$_GET['etape'];} if (isset($_POST['etape'])) {$etape=$_POST['etape'];} }
if (empty($_GET['page']) and empty($_POST['page'])) { $page = ''; }
	else { if (isset($_GET['page'])) {$page=$_GET['page'];} if (isset($_POST['page'])) {$page=$_POST['page'];} }
if (empty($_GET['toutes']) and empty($_POST['toutes'])) { $toutes = '0'; }
	else { if (isset($_GET['toutes'])) {$toutes=$_GET['toutes'];} if (isset($_POST['toutes'])) {$toutes=$_POST['toutes'];} }

if (empty($_GET['classe']) and empty($_POST['classe'])) { $classe = ''; }
else { if (isset($_GET['classe'])) { $classe = $_GET['classe']; } if (isset($_POST['classe'])) { $classe = $_POST['classe']; } }
if (empty($_GET['groupe']) and empty($_POST['groupe'])) { $groupe = ''; }
else { if (isset($_GET['groupe'])) { $groupe = $_GET['groupe']; } if (isset($_POST['groupe'])) { $groupe = $_POST['groupe']; } }
if (empty($_GET['equipepeda']) and empty($_POST['equipepeda'])) { $equipepeda = ''; }
else { if (isset($_GET['equipepeda'])) { $equipepeda = $_GET['equipepeda']; } if (isset($_POST['equipepeda'])) { $equipepeda = $_POST['equipepeda']; } }
if (empty($_GET['discipline']) and empty($_POST['discipline'])) { $discipline = ''; }
else { if (isset($_GET['discipline'])) { $discipline = $_GET['discipline']; } if (isset($_POST['discipline'])) { $discipline = $_POST['discipline']; } }
if (empty($_GET['statusgepi']) and empty($_POST['statusgepi'])) { $statusgepi = ''; }
else { if (isset($_GET['statusgepi'])) { $statusgepi = $_GET['statusgepi']; } if (isset($_POST['statusgepi'])) { $statusgepi = $_POST['statusgepi']; } }
if (empty($_GET['affdiscipline']) and empty($_POST['affdiscipline'])) { $affdiscipline = ''; }
else { if (isset($_GET['affdiscipline'])) { $affdiscipline = $_GET['affdiscipline']; } if (isset($_POST['affdiscipline'])) { $affdiscipline = $_POST['affdiscipline']; } }

if (empty($_POST['eleve_absent'])) {$eleve_absent = ''; } else {$eleve_absent=$_POST['eleve_absent']; }
if (empty($_GET['action'])) {$action = ''; } else {$action=$_GET['action']; }
if (empty($_POST['eleve_initial'])) {$eleve_initial = ''; } else {$eleve_initial=$_POST['eleve_initial']; }
if (empty($_GET['id'])) {$id = ''; } else {$id=$_GET['id']; }
if (empty($_POST['valider'])) {$valider = ''; } else {$valider=$_POST['valider']; }





/*
// =================================
// AJOUT: boireaus
if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='cpe')||($_SESSION['statut']=='professeur')) {
	$chaine_options_classes="";

	if($_SESSION['statut']=='administrateur') {
		$sql="SELECT id, classe FROM classes ORDER BY classe";
	}
	elseif($_SESSION['statut']=='scolarite') {
		$sql="SELECT id, classe FROM classes ORDER BY classe";
	}
	elseif($_SESSION['statut']=='cpe') {
		$sql="SELECT id, classe FROM classes ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur') {
		$sql="SELECT id, classe FROM classes ORDER BY classe";
	}

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
}
// =================================
*/

// =========== Style spécifique ================
$style_specifique = "mod_trombinoscopes/styles/styles";
//**************** EN-TETE *********************
$titre_page = "Visualisation des trombinoscopes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var();
?>
<script type="text/javascript">

function inputenable(id,state) {
	var divObj = null;
	if (document.getElementById) {
		divObj = document.getElementById(id);
	} else if(document.all) {
		divObj = document.all(id);
	} else if (document.layers) {
		divObj = document.layers[id];
	}
	if(state && divObj) {
		divObj.removeAttribute("readonly");
	} else if(divObj) {
		divObj.setAttribute("readonly","readonly");
	}
}


function desactiver(mavar) {
	mavar = mavar.split(',');
	for (i=0; i<mavar.length; i++)
	{
		//document.getElementById(mavar[i]).disabled=true;
		if(document.getElementById(mavar[i])) {document.getElementById(mavar[i]).disabled=true;}
	}
	/*document.getElementById(mavar[i]).disabled=true;*/
	/*document.form1.equipepeda.disabled = true;*/
}

function reactiver(mavar) {
	mavar = mavar.split(',');
	for (var i in mavar)
	{
		//document.getElementById(mavar[i]).disabled=false;
		if(document.getElementById(mavar[i])) {document.getElementById(mavar[i]).disabled=false;}
	}
	/*document.getElementById(mavar[i]).disabled=false;*/
	/*document.form1.equipepeda.disabled = false;*/
}

</script>

<?php

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	echo " | <a href='trombinoscopes.php'>Effectuer une autre sélection</a>";

	function acces($id,$statut) {
		$tab_id = explode("?",$id);
		$query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
		$droit = @mysql_result($query_droits, 0, $statut);
		if ($droit == "V") {
			return "1";
		} else {
			return "0";
		}
	}

	if( $etape === '2' and $classe != 'toutes' and $groupe != 'toutes' and $equipepeda != 'toutes' and $discipline != 'toutes' and ( $classe != '' or $groupe != '' or $equipepeda != '' or $discipline != '' or $statusgepi != '' ) ) {
		//echo " | <a href='trombinoscopes.php'>Retour à la sélection</a>";

		if(acces('/mod_trombinoscopes/trombi_impr.php',$_SESSION['statut'])) {
			echo " | <a href='trombi_impr.php?classe=$classe&amp;groupe=$groupe&amp;equipepeda=$equipepeda&amp;discipline=$discipline&amp;statusgepi=$statusgepi&amp;affdiscipline=$affdiscipline' target='_blank'>Format imprimable</a>";
		}
	}

	$id_classe=$classe;
	//if((isset($id_classe))&&(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='cpe')||($_SESSION['statut']=='professeur'))) {
	if(($id_classe!='')&&(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='cpe')||($_SESSION['statut']=='professeur'))) {
		// ===========================================
		// Ajout lien classe précédente / classe suivante
		if($_SESSION['statut']=='administrateur'){
			$sql="SELECT id, classe FROM classes ORDER BY classe";
		}
		elseif($_SESSION['statut']=='scolarite'){
			$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
		}
		elseif($_SESSION['statut']=='professeur'){
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
		}
		elseif($_SESSION['statut']=='cpe'){
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
				p.id_classe = c.id AND
				jec.id_classe=c.id AND
				jec.periode=p.num_periode AND
				jecpe.e_login=jec.login AND
				jecpe.cpe_login='".$_SESSION['login']."'
				ORDER BY classe";
		}
		$chaine_options_classes="";
	
		$res_class_tmp=mysql_query($sql);
		if(mysql_num_rows($res_class_tmp)>0){
			$id_class_prec=0;
			$id_class_suiv=0;
			$temoin_tmp=0;
			while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				if($lig_class_tmp->id==$id_classe){
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
			}
		}
		// =================================
		if(isset($id_class_prec)){
			//if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec'>Classe précédente</a>";}
			if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?classe=$id_class_prec&amp;etape=2'>Classe précédente</a>";}
		}
		if($chaine_options_classes!="") {
			//echo " | Classe : <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
			echo " | Classe : <select name='classe' onchange=\"document.forms['form1'].submit();\">\n";
			echo $chaine_options_classes;
			echo "</select>\n";
			echo "<input type='hidden' name='etape' value='2' />\n";
		}
		if(isset($id_class_suiv)){
			//if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv'>Classe suivante</a>";}
			if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?classe=$id_class_suiv&amp;etape=2'>Classe suivante</a>";}
		}
		//fin ajout lien classe précédente / classe suivante
		// ===========================================
	}

	echo "</p>\n";
	echo "</form>\n";


	$GepiAccesEleTrombiTousEleves=getSettingValue("GepiAccesEleTrombiTousEleves");
	$GepiAccesEleTrombiElevesClasse=getSettingValue("GepiAccesEleTrombiElevesClasse");
	$GepiAccesEleTrombiPersonnels=getSettingValue("GepiAccesEleTrombiPersonnels");
	$GepiAccesEleTrombiProfsClasse=getSettingValue("GepiAccesEleTrombiProfsClasse");

	/*
	echo "\$GepiAccesEleTrombiTousEleves=$GepiAccesEleTrombiTousEleves<br />";
	echo "\$GepiAccesEleTrombiElevesClasse=$GepiAccesEleTrombiElevesClasse<br />";
	echo "\$GepiAccesEleTrombiPersonnels=$GepiAccesEleTrombiPersonnels<br />";
	echo "\$GepiAccesEleTrombiProfsClasse=$GepiAccesEleTrombiProfsClasse<br />";
	*/

	$affichage_div_gauche="n";

	if ( ( $classe === 'toutes' or $groupe === 'toutes' or $equipepeda === 'toutes' or $discipline === 'toutes' ) or ( $classe === '' and $groupe === '' and $equipepeda === '' and $discipline === '' and $statusgepi === '' ) ) {

		echo "<form method='post' action='trombinoscopes.php' name='form1' style='font-size: 0.71em;'>\n";
		echo "<div style='margin: auto; padding: 0px 20px 0px 20px;'>\n";

		$acces="y";
		if(($_SESSION['statut']=='eleve')&&($GepiAccesEleTrombiTousEleves!="yes")&&($GepiAccesEleTrombiElevesClasse!="yes")) {
			$acces="n";
		}

		if((getSettingValue('active_module_trombinoscopes')=='y')&&($acces=="y")) {
			$affichage_div_gauche="y";
			echo "<div style='width: 45%; float: left; padding: 5px;'>\n";

			echo "<div style='font: normal small-caps normal 14pt Verdana; border-collapse: separate; border-spacing: 0px; border: none; border-bottom: 1px solid lightgrey;'>".ucfirst($gepiSettings['denomination_eleve'])."</div>\n";

			//=================================================================
			// CLASSES
			echo "<span style='margin-left: 15px;'>Par classe</span><br />\n";
			echo "<select name='classe' id='classe' style='margin-left: 15px;'>\n";

			//if ( $_SESSION['statut'] != 'professeur' ) { $classe = 'toutes'; }
			if(($_SESSION['statut']=='scolarite')||
				($_SESSION['statut']=='cpe')||
				($_SESSION['statut']=='administrateur')||
				($_SESSION['statut']=='autre')
			) {
				$classe = 'toutes';
			}

			//if ( $classe == '' ) {
			if (($classe=='')&&($_SESSION['statut']=='professeur')) {
				// Le prof ne voit par défaut que ses classes... mais en sélectionnant 'Toutes', il peut provoquer l'affichage des autres classes dans le champ select.
				$requete_classe_prof = ('SELECT * FROM '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'classes c
							WHERE jgp.id_groupe = jgc.id_groupe AND jgc.id_classe = c.id AND jgp.login = "'.$_SESSION['login'].'"
							GROUP BY c.id
							ORDER BY nom_complet ASC');
			}

			if (($classe=='')&&($_SESSION['statut']=='eleve')) {
				/*
				if($GepiAccesEleTrombiTousEleves=="yes") {
					$classe = 'toutes';
				}
				else {
				*/
					$requete_classe_prof = ("SELECT DISTINCT c.* FROM classes c, j_eleves_classes jec WHERE jec.id_classe=c.id AND jec.login='".$_SESSION['login']."'
					ORDER BY c.nom_complet ASC");
				//}
			}

			if ( $classe == 'toutes' ) {
				$requete_classe_prof = ('SELECT * FROM '.$prefix_base.'classes c
					ORDER BY c.nom_complet ASC');
			}
			$resultat_classe_prof = mysql_query($requete_classe_prof) or die('Erreur SQL !'.$requete_classe_prof.'<br />'.mysql_error());

			echo "<option value='' ";
			if ( empty($classe) ) { echo "selected='selected'"; }
			echo " onclick=\"reactiver('equipepeda,groupe,discipline,statusgepi,affdiscipline');\">pas de s&eacute;lection</option>\n";

			if (( $classe != 'toutes' )&&($_SESSION['statut']!='eleve')) {
				echo "<option value='toutes'>voir toutes les classes</option>\n";
			}

			if (( $classe != 'toutes' )&&($_SESSION['statut']=='eleve')&&($GepiAccesEleTrombiTousEleves=="yes")) {
				echo "<option value='toutes'>voir toutes les classes</option>\n";
			}

			//if ( $classe == 'toutes' and $_SESSION['statut'] == 'professeur' ) {
			if ( $classe == 'toutes' and ($_SESSION['statut'] == 'professeur')||($_SESSION['statut'] == 'eleve')) {
				echo "<option value=''>voir mes classes</option>\n";
			}

			echo "<optgroup label='-- Les classes --'>\n";

			while ( $data_classe_prof = mysql_fetch_array ($resultat_classe_prof)) {
				echo "<option value='".$data_classe_prof['id']."'";
				if(!empty($classe) and $classe == $data_classe_prof['id']) {
					echo " selected='selected'";
				}
				echo " onclick=\"desactiver('equipepeda,groupe,discipline,statusgepi,affdiscipline');\">";
				echo ucwords($data_classe_prof['nom_complet']).' ('.ucwords($data_classe_prof['classe']).')';
				echo "</option>\n";
			}

			echo "</optgroup>\n";
			echo "</select><br /><br />\n";

			//=================================================================
			// GROUPES
			echo "<span style='margin-left: 15px;'>Par groupe</span><br />\n";
			echo "<select name='groupe' id='groupe' style='margin-left: 15px;'>\n";

			//if ( $_SESSION['statut'] != 'professeur' ) { $groupe = 'toutes'; }
			if(($_SESSION['statut']=='scolarite')||
				($_SESSION['statut']=='cpe')||
				($_SESSION['statut']=='administrateur')||
				($_SESSION['statut']=='autre')
			) {
				$groupe = 'toutes';
			}


			//if($groupe == '') {
			if (($groupe=='')&&($_SESSION['statut']=='professeur')) {
				$requete_groupe_prof = ('SELECT * FROM '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'groupes g, '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'classes c
							WHERE jgp.id_groupe = g.id
							AND jgp.login = "'.$_SESSION['login'].'"
							AND g.id = jgc.id_groupe
							AND jgc.id_classe = c.id
							GROUP BY g.id
							ORDER BY name ASC');
			}

			if (($groupe=='')&&($_SESSION['statut']=='eleve')) {
				$requete_groupe_prof = ("SELECT DISTINCT g.*, jeg.id_groupe FROM groupes g, j_eleves_groupes jeg WHERE jeg.id_groupe=g.id AND jeg.login='".$_SESSION['login']."'
					ORDER BY g.name ASC");
			}


			if($groupe == "toutes") {
				$requete_groupe_prof = ("SELECT * FROM groupes g,
											j_groupes_classes jgc,
											classes c
										WHERE g.id = jgc.id_groupe AND
											jgc.id_classe = c.id
										ORDER BY name ASC, nom_complet ASC");
			}
			$resultat_groupe_prof = mysql_query($requete_groupe_prof) or die('Erreur SQL !'.$requete_groupe_prof.'<br />'.mysql_error());

			echo "<option value=''";
			if ( empty($classe) ) {
				echo " selected='selected'";
			}
			echo "onclick=\"reactiver('classe,equipepeda,discipline,statusgepi,affdiscipline');\">pas de s&eacute;lection</option>\n";
			/*
			if ( $groupe != 'toutes' ) {
				echo "<option value='toutes'>voir tous les groupes</option>\n";
			}
			*/
			if (( $groupe != 'toutes' )&&($_SESSION['statut']!='eleve')) {
				echo "<option value='toutes'>voir tous les groupes</option>\n";
			}

			if (( $groupe != 'toutes' )&&($_SESSION['statut']=='eleve')&&($GepiAccesEleTrombiTousEleves=="yes")) {
				echo "<option value='toutes'>voir tous les groupes</option>\n";
			}

			if ( $groupe == 'toutes' and ($_SESSION['statut'] == 'professeur')||($_SESSION['statut'] == 'eleve')) {
				echo "<option value=''>voir mes groupes</option>\n";
			}

			echo "<optgroup label='-- Les groupes --'>\n";
			while ( $donnee_groupe_prof = mysql_fetch_array ($resultat_groupe_prof)) {
				echo "<option value='".$donnee_groupe_prof['id_groupe']."'  onclick=\"desactiver('classe,equipepeda,discipline,statusgepi,affdiscipline');\">";

				//modif ERIC
				echo ucwords($donnee_groupe_prof['description']);
				//echo ' ('.ucwords($donnee_groupe_prof['classe']).')';
				$tmp_grp=get_group($donnee_groupe_prof['id_groupe']);
				echo ' ('.ucwords($tmp_grp['classlist_string']).')';

				echo "</option>\n";
			}

			echo "</optgroup>\n";
			echo "</select><br /><br />";

			echo "<input value='2' name='etape' type='hidden' />\n";

			echo "<input value='valider' name='Valider' id='valid1' type='submit' onClick=\"this.form.submit();this.disabled=true;this.value='En cours'\" />\n";
			echo "</div>";
		}


		//======================================================================================
		// TROMBINOSCOPES DES PERSONNELS

		$acces="y";
		if(($_SESSION['statut']=='eleve')&&($GepiAccesEleTrombiPersonnels!="yes")&&($GepiAccesEleTrombiProfsClasse!="yes")) {
			$acces="n";
		}

		//echo "\$acces=$acces<br />";

		if((getSettingValue('active_module_trombino_pers')=='y')&&($acces=="y")) {

			//if(getSettingValue('active_module_trombinoscopes')=='y') {
			if($affichage_div_gauche=="y") {
				echo "<div style='width: 45%; float: right; padding: 5px;'>\n";
			}
			else {
				echo "<div style='width: 45%; float: left; padding: 5px;'>\n";
			}

			echo "<div style='font: normal small-caps normal 14pt Verdana; border-collapse: separate; border-spacing: 0px; border: none; border-bottom: 1px solid lightgrey;'>Personnels</div>\n";

			//==========================================================
			// EQUIPES PEDAGOGIQUES
			echo "<span style='margin-left: 15px;'>Par équipe pédagogique</span><br />\n";
			echo "<select name='equipepeda' id='equipepeda' style='margin-left: 15px;'>\n";

			//if ( $_SESSION['statut'] != 'professeur' ) { $equipepeda = 'toutes'; }
			if(($_SESSION['statut']=='scolarite')||
				($_SESSION['statut']=='cpe')||
				($_SESSION['statut']=='administrateur')
			) {
				$equipepeda = 'toutes';
			}

			//if ( $equipepeda == '' ) {
			if (($equipepeda=='')&&($_SESSION['statut']=='professeur')) {
				$requete_equipe_pedagogique = ('SELECT * FROM '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'classes c
							WHERE jgp.id_groupe = jgc.id_groupe AND jgc.id_classe = c.id AND jgp.login = "'.$_SESSION['login'].'"
							GROUP BY c.id
							ORDER BY nom_complet ASC');
			}

			if (($equipepeda=='')&&($_SESSION['statut']=='eleve')) {
				$requete_equipe_pedagogique = ("SELECT DISTINCT c.* FROM classes c, j_eleves_classes jec WHERE jec.id_classe=c.id AND jec.login='".$_SESSION['login']."'
					ORDER BY c.classe ASC");
			}

			if ( $equipepeda == 'toutes' ) {
				$requete_equipe_pedagogique = ('SELECT * FROM '.$prefix_base.'classes c
					ORDER BY c.nom_complet ASC');
			}
			$resultat_equipe_pedagogique = mysql_query($requete_equipe_pedagogique) or die('Erreur SQL !'.$requete_equipe_pedagogique.'<br />'.mysql_error());

			echo "<option value=''";
			if ( empty($equipepeda) ) {
				echo " selected='selected'";
			}
			echo " onclick=\"reactiver('classe,groupe,discipline,statusgepi');\">pas de s&eacute;lection</option>\n";


			//if ( $equipepeda != 'toutes' ) {
			if (( $equipepeda != 'toutes' )&&($_SESSION['statut']!='eleve')) {
				echo "<option value='toutes'>voir toutes les équipes pedagogique</option>\n";
			}

			if (( $equipepeda != 'toutes' )&&($_SESSION['statut']=='eleve')&&($GepiAccesEleTrombiPersonnels=="yes")) {
				echo "<option value='toutes'>voir toutes les équipes pedagogique</option>\n";
			}

			if ( $equipepeda == 'toutes' and ($_SESSION['statut'] == 'professeur')||($_SESSION['statut'] == 'eleve')) {
				echo "<option value=''>voir mes equipepedas</option>\n";
			}

			echo "<optgroup label='-- Les classes --'>\n";
			while ( $donnee_equipe_pedagogique = mysql_fetch_array ($resultat_equipe_pedagogique)) {

				echo "<option value='".$donnee_equipe_pedagogique['id']."'";
				if(!empty($equipepeda) and $equipepeda == $donnee_equipe_pedagogique['id']) {
					echo " selected='selected'";
				}
				echo " onclick=\"desactiver('classe,groupe,discipline,statusgepi');\">";
				echo ucwords($donnee_equipe_pedagogique['nom_complet']).' ('.ucwords($donnee_equipe_pedagogique['classe']).')';
				echo "</option>\n";
			}
			echo "</optgroup>\n";
			echo "</select>\n";


			//==========================================================
			// PAR DISCIPLINES
			if((($_SESSION['statut']!='eleve')||(($_SESSION['statut']=='eleve')&&($GepiAccesEleTrombiPersonnels=='yes')))&&
				($_SESSION['statut']!='responsable')) {
				echo "<br />&nbsp;&nbsp;&nbsp;<input type='checkbox' name='affdiscipline' id='affdiscipline' value='oui' />&nbsp;<label for='affdiscipline' style='cursor: pointer; cursor: hand;'>Afficher les disciplines</label>\n";
				echo "<br /><br />\n";

				echo "<span style='margin-left: 15px;'>Par discipline</span><br />\n";
				echo "<select name='discipline' id='discipline' style='margin-left: 15px;'>\n";

				if ( $_SESSION['statut'] != 'professeur' ) { $discipline = 'toutes'; }
				/*
				if(($_SESSION['statut']=='scolarite')||
					($_SESSION['statut']=='cpe')||
					($_SESSION['statut']=='administrateur')
				) {
					$discipline = 'toutes';
				}
				*/

				//if ( $discipline == '' ) {
				if (($discipline=='')&&($_SESSION['statut']=='professeur')) {
					$requete_discipline = ('SELECT * FROM '.$prefix_base.'j_professeurs_matieres jpm, '.$prefix_base.'matieres m
								WHERE jpm.id_professeur = "'.$_SESSION['login'].'"
								AND jpm.id_matiere = m.matiere
								GROUP BY m.matiere
								ORDER BY m.nom_complet ASC');
				}

				if ( $discipline == 'toutes' ) {
					$requete_discipline = ('SELECT * FROM '.$prefix_base.'matieres m
								ORDER BY m.nom_complet ASC');
				}
				$resultat_discipline = mysql_query($requete_discipline) or die('Erreur SQL !'.$requete_discipline.'<br />'.mysql_error());



				echo "<option value=''";
				if ( empty($discipline) ) {
					echo " selected='selected'";
				}
				echo " onclick=\"reactiver('classe,groupe,equipepeda,statusgepi,affdiscipline');\">pas de s&eacute;lection</option>\n";

				if ( $discipline != 'toutes' ) {
					echo "<option value='toutes'>voir toutes les disciplines</option>\n";
				}
				if ( $discipline == 'toutes' and $_SESSION['statut'] == 'professeur' ) {
					echo "<option value=''>voir mes disciplines</option>\n";
				}

				echo "<optgroup label='-- Les disciplines --'>\n";
				while ( $donnee_discipline = mysql_fetch_array ($resultat_discipline)) {
					echo "<option value='".$donnee_discipline['matiere']."'";

					if(!empty($discipline) and $discipline == $donnee_discipline['matiere']) {
						echo " selected='selected'";
					}
					echo " onclick=\"desactiver('classe,groupe,equipepeda,statusgepi,affdiscipline');\">\n";
					echo ucwords($donnee_discipline['nom_complet']);
					echo "</option>\n";
				}
				echo "</optgroup>\n";
				echo "</select>\n";
			}
			echo "<br /><br />\n";
			//==========================================================
			// PAR STATUT
			echo "<span style='margin-left: 15px;'>Par statut (CPE/Professeur/Scolarité)</span><br />\n";
			echo "<select name='statusgepi' id='statusgepi' style='margin-left: 15px;'>\n";

			if ( $statusgepi == '' ) {
				if(($_SESSION['statut']=='eleve')&&($GepiAccesEleTrombiPersonnels!='yes')) {
					$requete_statusgepi = ("SELECT * FROM utilisateurs u
							WHERE u.statut='professeur' AND u.etat='actif'
							GROUP BY u.statut
							ORDER BY u.statut ASC");
				}
				else {
					$requete_statusgepi = ('SELECT * FROM '.$prefix_base.'utilisateurs u
							WHERE (u.statut = "professeur" OR u.statut = "cpe" OR u.statut="scolarite" OR u.statut="autre") AND etat="actif"
							GROUP BY u.statut
							ORDER BY u.statut ASC');
				}
			}
			$resultat_statusgepi = mysql_query($requete_statusgepi) or die('Erreur SQL !'.$requete_statusgepi.'<br />'.mysql_error());


			echo "<option value=''";
			if ( empty($statusgepi) ) {
				echo " selected='selected'";
			}
			echo " onclick=\"reactiver('classe,groupe,equipepeda,discipline,affdiscipline');\">pas de s&eacute;lection</option>\n";
			echo "<optgroup label='-- Les statuts --'>\n";
			while ( $donnee_statusgepi = mysql_fetch_array ($resultat_statusgepi)) {
				echo "<option value='".$donnee_statusgepi['statut']."'";
				if(!empty($statusgepi) and $statusgepi == $donnee_statusgepi['statut']) {
					echo " selected='selected'";
				}
				echo " onclick=\"desactiver('classe,groupe,equipepeda,discipline,affdiscipline');\">";
				echo my_ereg_replace("Scolarite","Scolarité",ucwords($donnee_statusgepi['statut']));
				echo "</option>\n";
			}

			echo "</optgroup>\n";
			echo "</select><br /><br />\n";

			echo "<input value='2' name='etape' type='hidden' />\n";
			echo "<input value='valider' name='Valider' id='valid2' type='submit' onClick=\"this.form.submit();this.disabled=true;this.value='En cours'\" />\n";
			echo "</div>\n";
			echo "</div>\n";

		}

		echo "</form>\n";
	}

//==================================================================================
/* affichage vignettes */
if ( $etape === '2' and $classe != 'toutes' and $groupe != 'toutes' and $discipline != 'toutes' and $equipepeda != 'toutes' and ( $classe != '' or $groupe != '' or $equipepeda != '' or $discipline != '' or $statusgepi != '') ) {

	echo "<div style='text-align: center;'>\n";
	echo "<table width='100%' border='0' cellspacing='0' cellpadding='2' style='border : thin dashed #242424; background-color: #FFFFB8;' summary='Choix'>\n";
	echo "<tr valign='top'>\n";
	echo "<td align='left'><font face='Arial, Helvetica, sans-serif'>TROMBINOSCOPE ";

	$datej = date('Y-m-d');
	$annee_en_cours_t=annee_en_cours_t($datej);
	echo $annee_en_cours_t;

	echo "<br />\n";
	echo "<b>\n";

	// on regarde ce qui a été choisi
	if ( $classe != '' and $groupe === '' and $equipepeda === '' and $discipline === '' and $statusgepi === '' ) {
		// c'est une classe
		$action_affiche = 'classe';
	}
	elseif ( $classe === '' and $groupe != '' and $equipepeda === '' and $discipline === '' and $statusgepi === '' ) {
		// c'est un groupe
		$action_affiche = 'groupe';
	}
	elseif ( $classe === '' and $groupe === '' and $equipepeda != '' and $discipline === '' and $statusgepi === '' ) {
		// c'est une équipe pédagogique
		$action_affiche = 'equipepeda';
	}
	elseif ( $classe === '' and $groupe === '' and $equipepeda === '' and $discipline != '' and $statusgepi === '' ) {
		// c'est une discipline
		$action_affiche = 'discipline';
	}
	elseif ( $classe === '' and $groupe === '' and $equipepeda === '' and $discipline === '' and $statusgepi != '' ) {
		// c'est un status de gepi
		$action_affiche = 'statusgepi';
	}

	if ( $action_affiche === 'classe' ) {
		if($_SESSION['statut']=='eleve') {
			if($GepiAccesEleTrombiTousEleves=='yes') {
				$requete_qui = 'SELECT c.id, c.nom_complet, c.classe FROM '.$prefix_base.'classes c WHERE c.id = "'.$classe.'"';
			}
			elseif($GepiAccesEleTrombiElevesClasse=='yes') {
				$requete_qui="SELECT DISTINCT c.id, c.nom_complet, c.classe FROM classes c, j_eleves_classes jec WHERE c.id='$classe' AND c.id=jec.id_classe AND jec.login='".$_SESSION['login']."';";
			}
			else {
				echo "<p>Vous n'avez pas accès aux trombinoscopes de classes.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
		else {
			$requete_qui = 'SELECT c.id, c.nom_complet, c.classe FROM '.$prefix_base.'classes c WHERE c.id = "'.$classe.'"';
		}
	}

	if ( $action_affiche === 'groupe' ) {
		if($_SESSION['statut']=='eleve') {
			if($GepiAccesEleTrombiTousEleves=='yes') {
				$requete_qui = 'SELECT g.id, g.name FROM '.$prefix_base.'groupes g WHERE g.id = "'.$groupe.'"';
			}
			elseif($GepiAccesEleTrombiElevesClasse=='yes') {
				$requete_qui="SELECT DISTINCT g.id, g.name FROM groupes g, j_eleves_groupes jeg WHERE g.id='$groupe' AND g.id=jeg.id_groupe AND jeg.login='".$_SESSION['login']."';";
			}
			else {
				echo "<p>Vous n'avez pas accès aux trombinoscopes de groupes.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
		else {
			$requete_qui = 'SELECT g.id, g.name FROM '.$prefix_base.'groupes g WHERE g.id = "'.$groupe.'"';
		}
	}

	if ( $action_affiche === 'equipepeda' ) {
		if($_SESSION['statut']=='eleve') {
			if($GepiAccesEleTrombiPersonnels=='yes') {
				$requete_qui = 'SELECT c.id, c.nom_complet, c.classe FROM '.$prefix_base.'classes c WHERE c.id = "'.$equipepeda.'"';
			}
			elseif($GepiAccesEleTrombiProfsClasse=='yes') {
				$requete_qui="SELECT DISTINCT c.id, c.nom_complet, c.classe FROM classes c, j_eleves_classes jec WHERE c.id='$equipepeda' AND c.id=jec.id_classe AND jec.login='".$_SESSION['login']."';";
			}
			else {
				echo "<p>Vous n'avez pas accès aux trombinoscopes d'équipes pédagogiques.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
		else {
			$requete_qui = 'SELECT c.id, c.nom_complet, c.classe FROM '.$prefix_base.'classes c WHERE c.id = "'.$equipepeda.'"';
		}
	}

	if ( $action_affiche === 'discipline' ) {
		if($_SESSION['statut']=='eleve') {
			if($GepiAccesEleTrombiPersonnels=='yes') {
				$requete_qui = 'SELECT m.matiere, m.nom_complet FROM '.$prefix_base.'matieres m WHERE m.matiere = "'.$discipline.'"';
			}
			else {
				echo "<p>Vous n'avez pas accès aux trombinoscopes par disciplines.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
		else {
			$requete_qui = 'SELECT m.matiere, m.nom_complet FROM '.$prefix_base.'matieres m WHERE m.matiere = "'.$discipline.'"';
		}
	}

	//if ( $action_affiche === 'statusgepi' ) { $requete_qui = 'SELECT statut FROM '.$prefix_base.'utilisateurs u WHERE u.statut = "'.$statusgepi.'"'; }
	if ( $action_affiche === 'statusgepi' ) {
		if($_SESSION['statut']=='eleve') {
			if(($GepiAccesEleTrombiPersonnels!='yes')&&($GepiAccesEleTrombiProfsClasse=='yes')) {
				$statusgepi='professeur';
			}
			else {
				echo "<p>Vous n'avez pas accès aux trombinoscopes par statut.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}

		$requete_qui = 'SELECT statut FROM '.$prefix_base.'utilisateurs u WHERE u.statut = "'.$statusgepi.'" AND etat="actif";';
	}

	$execute_qui = mysql_query($requete_qui) or die('Erreur SQL !'.$requete_qui.'<br />'.mysql_error());
	if(mysql_num_rows($execute_qui)==0) {
		// On doit être dans le cas d'un élève qui a tenté d'accéder aux photos d'une classe, groupe, équipe,... à laquelle il n'est pas associé.
		echo "<p>La requête n'a retourné aucun enregistrement.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	$donnees_qui = mysql_fetch_array($execute_qui) or die('Erreur SQL !'.$execute_qui.'<br />'.mysql_error());

	if ( $action_affiche === 'classe' ) {
		//echo "Classe : ".htmlentities($donnees_qui['nom_complet']);
		//echo ' ('.htmlentities(ucwords($donnees_qui['classe'])).')';
		echo "Classe : ".$donnees_qui['nom_complet'];
		echo ' ('.ucwords($donnees_qui['classe']).')';

		$repertoire = 'eleves';

		$requete_trombi = "SELECT e.login, e.nom, e.prenom, e.elenoet, jec.login, jec.id_classe, jec.periode, c.classe, c.id, c.nom_complet
								FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c
								WHERE e.login = jec.login
								AND jec.id_classe = c.id
								AND id = '".$classe."'
								GROUP BY nom, prenom";
	}

	if ( $action_affiche === 'groupe' ) {
		$current_group=get_group($groupe);
		echo "Groupe : ".htmlentities($donnees_qui['name'])." (<i>".$current_group['classlist_string']."</i>)";

		$repertoire = 'eleves';

		$requete_trombi = "SELECT jeg.login, jeg.id_groupe, jeg.periode, e.login, e.nom, e.prenom, e.elenoet, g.id, g.name, g.description
								FROM ".$prefix_base."eleves e, ".$prefix_base."groupes g, ".$prefix_base."j_eleves_groupes jeg
								WHERE jeg.login = e.login
								AND jeg.id_groupe = g.id
								AND g.id = '".$groupe."'
								GROUP BY nom, prenom";
	}
	//if ( $action_affiche === 'equipepeda' ) { echo "Equipe pédagogique : ".htmlentities($donnees_qui['nom_complet']); }
	//if ( $action_affiche === 'discipline' ) { echo "Discipline : ".htmlentities($donnees_qui['nom_complet'])." (".htmlentities($donnees_qui['matiere']).")"; }

	if ( $action_affiche === 'equipepeda' ) {
		echo "Equipe pédagogique : ".$donnees_qui['nom_complet']." (<i>".$donnees_qui['classe']."</i>)";

		$repertoire = 'personnels';

		$requete_trombi = 'SELECT * FROM '.$prefix_base.'utilisateurs u, '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'classes c
								WHERE jgp.id_groupe = jgc.id_groupe
								AND jgc.id_classe = c.id
								AND u.login = jgp.login
								AND c.id = "'.$equipepeda.'"
								AND u.etat="actif"
								GROUP BY u.nom, u.prenom
								ORDER BY nom ASC, prenom ASC';
	}
	if ( $action_affiche === 'discipline' ) {
		echo "Discipline : ".$donnees_qui['nom_complet']." (".$donnees_qui['matiere'].")";

		$repertoire = 'personnels';

		$requete_trombi = 'SELECT * FROM '.$prefix_base.'utilisateurs u, '.$prefix_base.'j_professeurs_matieres jpm, '.$prefix_base.'matieres m
								WHERE u.login = jpm.id_professeur
								AND m.matiere = jpm.id_matiere
								AND m.matiere = "'.$discipline.'"
								AND u.etat="actif"
								GROUP BY u.nom, u.prenom
								ORDER BY nom ASC, prenom ASC';
	}

	if ( $action_affiche === 'statusgepi' ) {
		echo "Statut : ".my_ereg_replace("scolarite","scolarité",$statusgepi);

		$repertoire = 'personnels';

		if($_SESSION['statut']=='eleve') {
			if($GepiAccesEleTrombiPersonnels=='yes') {
				$requete_trombi = 'SELECT * FROM '.$prefix_base.'utilisateurs u
									WHERE u.statut = "'.$statusgepi.'"
									AND u.etat="actif"
									GROUP BY u.nom, u.prenom
									ORDER BY nom ASC, prenom ASC';
			}
			elseif($GepiAccesEleTrombiProfsClasse=='yes') {
				$requete_trombi = "SELECT DISTINCT u.* FROM utilisateurs u, j_groupes_professeurs jgp, j_eleves_groupes jeg
									WHERE u.statut = 'professeur' AND
										jgp.id_groupe=jeg.id_groupe AND
										jgp.login=u.login AND
										jeg.login='".$_SESSION['login']."'
										AND u.etat='actif'
									GROUP BY u.nom, u.prenom
									ORDER BY nom ASC, prenom ASC";
			}
			else {
				echo "<p>Vous n'avez pas accès aux trombinoscopes par statut.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
		else {
			$requete_trombi = 'SELECT * FROM '.$prefix_base.'utilisateurs u
								WHERE u.statut = "'.$statusgepi.'"
								AND u.etat="actif"
								GROUP BY u.nom, u.prenom
								ORDER BY nom ASC, prenom ASC';
		}
	}

	//===========================================
	function matiereprof($prof, $equipepeda) {
		global $prefix_base;

		$prof_de = '';
		if ( $prof != '' ) {
			$requete_matiere = 'SELECT * FROM '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'j_groupes_matieres jgm, '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'matieres m
					WHERE jgc.id_classe = "'.$equipepeda.'"
					AND jgc.id_groupe = jgp.id_groupe
					AND jgm.id_matiere = m.matiere
					AND jgp.id_groupe = jgm.id_groupe
					AND jgp.login = "'.$prof.'"';
			$execution_matiere = mysql_query($requete_matiere) or die('Erreur SQL !'.$requete_matiere.'<br />'.mysql_error());
			while ($donnee_matiere = mysql_fetch_array($execution_matiere)) {
				$prof_de = $prof_de.'<br />'.htmlentities($donnee_matiere['nom_complet']).' ';
			}
		}
		return ($prof_de);
	}
	//===========================================


	$execution_trombi = mysql_query($requete_trombi) or die('Erreur SQL !'.$requete_trombi.'<br />'.mysql_error());
	$cpt_photo = 1;
	while ($donnee_trombi = mysql_fetch_array($execution_trombi))
	{
		//insertion de l'élève dans la varibale $eleve_absent
		$login_trombinoscope[$cpt_photo] = $donnee_trombi['login'];
		$nom_trombinoscope[$cpt_photo] = $donnee_trombi['nom'];
		$prenom_trombinoscope[$cpt_photo] = $donnee_trombi['prenom'];

		if ( $action_affiche === 'classe' ) { $id_photo_trombinoscope[$cpt_photo] = strtolower($donnee_trombi['elenoet']); }
		if ( $action_affiche === 'groupe' ) { $id_photo_trombinoscope[$cpt_photo] = strtolower($donnee_trombi['elenoet']); }
		if ( $action_affiche === 'equipepeda' ) { $id_photo_trombinoscope[$cpt_photo] = $donnee_trombi['login']; }
		if ( $action_affiche === 'discipline' ) { $id_photo_trombinoscope[$cpt_photo] = $donnee_trombi['login']; }
		if ( $action_affiche === 'statusgepi' ) { $id_photo_trombinoscope[$cpt_photo] = $donnee_trombi['login']; }

		$matiere_prof[$cpt_photo] = '';
		if ( $action_affiche === 'equipepeda' and $affdiscipline === 'oui' ) {
			$matiere_prof[$cpt_photo] = matiereprof($login_trombinoscope[$cpt_photo], $equipepeda);
		}

		$cpt_photo = $cpt_photo + 1;
	}
	$total = $cpt_photo;

	echo "</b></font>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<p align='center'><img src='images/barre.gif' width='550' height='2' alt='Barre' /></p>\n";

	echo "<!--table width='100%' border='0' cellspacing='0' cellpadding='4' summary='Trombino'-->\n";

	unset($tmp_id_classe);
	if (($action_affiche=='equipepeda')||
		($action_affiche=='discipline')||
		($action_affiche=='statusgepi')) {

		if($_SESSION['statut']=='eleve') {
			$tmp_id_classe=1;

			$tmp_clas=get_class_from_ele_login($_SESSION['login']);
			foreach($tmp_clas as $key_tmp => $value_tmp) {
				if(strlen(my_ereg_replace("[0-9]","",$key_tmp))==0) {
					$tmp_id_classe=$key_tmp;
					break;
				}
			}
		}
		elseif($_SESSION['statut']=='responsable') {

			$tmp_tab_enfants=get_enfants_from_resp_login($_SESSION['login']);
			for($loop=0;$loop<count($tmp_tab_enfants);$loop+=2) {
				$tmp_id_classe=-1;

				if(isset($tmp_tab_enfants[$loop])) {
					//echo "\$tmp_tab_enfants[$loop]=$tmp_tab_enfants[$loop]<br />";
					$tmp_clas=get_class_from_ele_login($tmp_tab_enfants[$loop]);
					foreach($tmp_clas as $key_tmp => $value_tmp) {
						//echo "\$tmp_clas[$key_tmp]=$value_tmp<br />";
						if(strlen(my_ereg_replace("[0-9]","",$key_tmp))==0) {
							$tmp_id_classe=$key_tmp;
							break;
						}
					}
				}

				//echo "\$tmp_id_classe=$tmp_id_classe<br />";

				if($tmp_id_classe!=-1) {break;}
			}

			if($tmp_id_classe==-1) {$tmp_id_classe=1;}
		}
	}

	echo "<table width='100%' border='0' cellspacing='0' cellpadding='4' summary='Trombino'>\n";

	$i = 1;
	while( $i < $total) {
		echo "<tr align='center' valign='top'>\n";
		for($j=0;$j<3;$j++){
			echo "<td>\n";
			if ($i < $total) {
				$nom_es = strtoupper($nom_trombinoscope[$i]);
				$prenom_es = ucfirst($prenom_trombinoscope[$i]);

				if (($action_affiche=='equipepeda')||
					($action_affiche=='discipline')||
					($action_affiche=='statusgepi')) {

					if(($_SESSION['statut']=='eleve')&&(isset($tmp_id_classe))) {
						$alt_nom_prenom_aff=affiche_utilisateur($login_trombinoscope[$i],$tmp_id_classe);
						$nom_prenom_aff=$alt_nom_prenom_aff."</span>";
					}
					elseif(($_SESSION['statut']=='responsable')&&(isset($tmp_id_classe))) {
						$alt_nom_prenom_aff=affiche_utilisateur($login_trombinoscope[$i],$tmp_id_classe);
						$nom_prenom_aff=$alt_nom_prenom_aff."</span>";
					}
					else {
						$nom_prenom_aff="<b>".$nom_es."</b></span><br />".$prenom_es;
						$alt_nom_prenom_aff=$nom_es." ".$prenom_es;
					}
				}
				else {
					$nom_prenom_aff="<b>".$nom_es."</b></span><br />".$prenom_es;
					$alt_nom_prenom_aff=$nom_es." ".$prenom_es;
				}

				$nom_photo = nom_photo($id_photo_trombinoscope[$i],$repertoire);
				//$photo = "../photos/".$repertoire."/".$nom_photo;
				$photo = $nom_photo;

				//if (($nom_photo != "") and (file_exists($photo))) {
				if (($nom_photo) and (file_exists($photo))) {
					$valeur=redimensionne_image($photo);
				} else {
					$valeur[0]=getSettingValue("l_max_aff_trombinoscopes");
					$valeur[1]=getSettingValue("h_max_aff_trombinoscopes");
				}

				echo "<img src='";
				//if (($nom_photo != "") and (file_exists($photo))) {
				if (($nom_photo) and (file_exists($photo))) {
					echo $photo;
				}
				else {
					echo "images/trombivide.jpg";
				}

				//echo "' style='border: 0px; width: ".$valeur[0]."px; height: ".$valeur[1]."px;' alt='".$prenom_es." ".$nom_es."' title='".$prenom_es." ".$nom_es."' />\n";
				echo "' style='border: 0px; width: ".$valeur[0]."px; height: ".$valeur[1]."px;' alt=\"".$alt_nom_prenom_aff."\" title=\"".$alt_nom_prenom_aff."\" />\n";
				echo "<br /><span style='font-family: Arial, Helvetica, sans-serif'>\n";

				//echo "<b>$nom_es</b></span><br />\n";
				//echo $prenom_es;
				echo $nom_prenom_aff;

				if ( $matiere_prof[$i] != '' ) {
					echo "<span style='font: normal 10pt Arial, Helvetica, sans-serif;'>$matiere_prof[$i]</span>\n";
				}
				if (( $action_affiche === 'groupe' )&&(strstr($current_group['classlist_string'],","))) {
					/*
					$sql="SELECT c.classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='".$login_trombinoscope[$i]."' ORDER BY jec.periode;";
					$res_class_ele=mysql_query($sql);
					*/
					$tab_ele_classes=get_class_from_ele_login($login_trombinoscope[$i]);
					echo "<br />".$tab_ele_classes['liste'];
				}

				$i = $i + 1;
				//echo "</span>\n";
			}
			else{
				echo "&nbsp;";
			}
			echo "</td>\n";
		}
		echo "</tr>\n";

		?>
		<tr align="center" valign="top">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?php
	}
	echo "</table>\n";
	echo "<p align='center'><img src='images/barre.gif' width='550' height='2' alt='Barre' /></p>\n";
	echo "</div>\n";
}
mysql_close();
require("../lib/footer.inc.php");
?>

