<?php
/*
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//INSERT INTO droits VALUES ('/groupes/mes_listes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'Accès aux CSV des listes d élèves', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//**************** EN-TETE **************************************
//$titre_page = "Gestion des groupes";
$titre_page = "Listes CSV";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

//debug_var();

//$id_classe=isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL;
$id_classe=isset($_GET['id_classe']) ? $_GET["id_classe"] : NULL;
$id_groupe=isset($_GET['id_groupe']) ? $_GET["id_groupe"] : NULL;
$periode_num=isset($_GET['periode_num']) ? $_GET["periode_num"] : NULL;
$ok=isset($_GET['ok']) ? $_GET["ok"] : NULL;

$refermer_onglet=isset($_POST['refermer_onglet']) ? $_POST['refermer_onglet'] : (isset($_GET['refermer_onglet']) ? $_GET['refermer_onglet'] : 'n');
//$chemin_retour=isset($_POST['chemin_retour']) ? $_POST['chemin_retour'] : (isset($_GET['chemin_retour']) ? $_GET['chemin_retour'] : '../accueil.php');
$chemin_retour=isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "../accueil.php";

echo "<p class='bold'>";

//if((isset($id_groupe))||(isset($classe))) {
if(((isset($id_groupe))||(isset($classe)))&&($refermer_onglet=='y')) {
	echo "<a href='javascript:self.close();'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Refermer</a>";
}
else {
	echo "<a href='$chemin_retour'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}
if(check_droit_acces('/impression/impression_serie.php',$_SESSION['statut'])) {
	echo " | <a href='../impression/impression_serie.php'>Listes PDF</a>";
}
echo "</p>\n";


echo "<h3>Mes listes d'".$gepiSettings['denomination_eleves']."</h3>\n";

echo "<p class='bold'>Listes standard&nbsp;:</p>\n";

if($_SESSION['statut']=='professeur') {
	echo "<p>Sélectionnez l'enseignement et la période pour lesquels vous souhaitez télécharger un fichier CSV des ".$gepiSettings['denomination_eleves']."&nbsp;:</p>\n";
	//$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc,j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgc.id_groupe=jgp.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
	//$sql="SELECT DISTINCT g.id,g.description FROM groupes g, j_groupes_professeurs jgp, j_groupes_classes jgc, classe c WHERE
	$sql="SELECT DISTINCT g.id,g.description FROM groupes g, j_groupes_professeurs jgp WHERE
		jgp.login = '".$_SESSION['login']."' AND
		g.id=jgp.id_groupe
		ORDER BY g.description";
	$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);

	if(mysqli_num_rows($res_grp)==0){
		echo "<p>Vous n'avez apparemment aucun enseignement.</p>\n";
		echo "</body></html>\n";
		die();
	}
	else {
		$message_erreur="";
		echo "<table>\n";
		while($lig_grp=mysqli_fetch_object($res_grp)){
			echo "<tr>\n";
			unset($tabnumper);
			unset($tabnomper);
			$sql="SELECT DISTINCT c.classe,c.id FROM classes c, j_groupes_classes jgc WHERE
				jgc.id_groupe='$lig_grp->id' AND
				jgc.id_classe=c.id
				ORDER BY c.classe";
			//echo "$sql<br />\n";
			$res_class=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_class)>0){
				$chaine_class="";
				$cpt=0;
				while($lig_class=mysqli_fetch_object($res_class)){
					$chaine_class.=",$lig_class->classe";

					if($cpt==0){
						$tabnumper=array();
						$tabnomper=array();
						$sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$lig_class->id' ORDER BY num_periode";
						$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_per)==0){
							$message_erreur.="<p><span style='color:red'>ERREUR&nbsp;:</span> Aucune période n'est définie pour la classe $lig_class->classe</p>\n";
							/*
							echo "<p>ERREUR: Aucune période n'est définie pour la classe $lig_class->classe</p>\n";
							echo "</body></html>\n";
							die();
							*/
						}
						else{
							while($lig_per=mysqli_fetch_object($res_per)){
								$tabnumper[]=$lig_per->num_periode;
								$tabnomper[]=$lig_per->nom_periode;
							}
						}
					}
					$cpt++;
				}
				$chaine_class=mb_substr($chaine_class,1);

			}

			//echo "<a href='".$_SERVER['PHP_SELF']."?id_groupe=$lig_grp->id&amp;ok=y'>".htmlspecialchars($lig_grp->description)." ($chaine_class)</a><br />\n";
			//echo "<a href='get_csv.php?id_groupe=$lig_grp->id'>".htmlspecialchars($lig_grp->description)." ($chaine_class)</a><br />\n";
			//echo "<td style='font-weight:bold;'>\n";
			//echo htmlspecialchars($lig_grp->description)." ($chaine_class):";
			echo "<td>\n";
			echo "<b>$chaine_class</b>: ".htmlspecialchars($lig_grp->description,ENT_QUOTES,"UTF-8");
			echo "</td>\n";
			for($i=0;$i<count($tabnumper);$i++){
				if($i>0){echo "<td> - </td>\n";}
				echo "<td>\n";
				echo "<a href='get_csv.php?id_groupe=$lig_grp->id&amp;periode_num=$tabnumper[$i]'>".htmlspecialchars($tabnomper[$i],ENT_QUOTES,"UTF-8")."</a>\n";
				echo "</td>\n";
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo $message_erreur;

		echo "<br />\n";

		$groups=get_groups_for_prof($_SESSION['login']);

		if(count($groups)>0) {
			echo "<form action='get_csv.php' method='post'>\n";
			echo "<fieldset style='border: 1px solid grey;background-image: url(\"../images/background/opacite50.png\");'>\n";
			//echo "<legend style='border: 1px solid grey;background-color: white;'></legend>\n";
			echo "<p class='bold'>Listes personnalisées&nbsp;:</p>\n";

			echo "<select name='id_groupe' id='id_groupe' onchange='update_champs_periode()'>\n";
			foreach($groups as $current_group) {
				if(!isset($first_group)) {$first_group=$current_group;}
				echo "<option value='".$current_group['id']."'>".$current_group['name']." (".$current_group['description'].") en ".$current_group['classlist_string']."</option>\n";
			}
			echo "</select>\n";

			echo "<div id='div_champs_periodes'>\n";
			for($i=1;$i<$first_group['nb_periode'];$i++) {
				echo "<input type='radio' id='periode_num_$i' name='periode_num' value='".$i."' ";
				//if($i==1) {echo "checked ";}
				if((isset($_GET['periode_num']))&&(is_numeric($_GET['periode_num']))&&($_GET['periode_num']<=$first_group['nb_periode'])) {
					if($_GET['periode_num']==$i) {
						echo "checked ";
					}
				}
				elseif((isset($_SESSION['mes_listes_periode_num']))&&($_SESSION['mes_listes_periode_num']<=$first_group['nb_periode'])) {
					if($_SESSION['mes_listes_periode_num']==$i) {
						echo "checked ";
					}
				}
				else {
					if($i==1) {echo "checked ";}
				}
				echo "/><label for='periode_num_$i'> Période $i</label><br />\n";
			}
			echo "</div>\n";

	echo "<script type='text/javascript'>
	// <![CDATA[
	function update_champs_periode() {
		//id_groupe=document.getElementById('id_groupe').options[document.getElementById('id_groupe').selectedIndex].value;
		id_groupe=document.getElementById('id_groupe').value;
		//alert('id_groupe='+id_groupe);
		new Ajax.Updater($('div_champs_periodes'),'update_champs_periode.php?id_groupe='+id_groupe+'&csrf_alea=".add_token_in_js_func()."',{method: 'get'});
	}
	//]]>
</script>\n";

			echo "<p><b>Inclure</b>&nbsp;:<br />\n";
			echo "<input type='checkbox' id='avec_classe' name='avec_classe' value='y' ";
			if(((isset($_SESSION['mes_listes_avec_classe']))&&($_SESSION['mes_listes_avec_classe']=='y'))||
				(!isset($_SESSION['mes_listes_avec_classe']))) {echo "checked ";}
			echo "/><label for='avec_classe'> le nom de la classe</label><br />\n";
		
			echo "<input type='checkbox' id='avec_login' name='avec_login' value='y' ";
			if(((isset($_SESSION['mes_listes_avec_login']))&&($_SESSION['mes_listes_avec_login']=='y'))||
				(!isset($_SESSION['mes_listes_avec_login']))) {echo "checked ";}
			echo "/><label for='avec_login'> le login des élèves</label><br />\n";
		
			echo "<input type='checkbox' id='avec_nom' name='avec_nom' value='y' ";
			if(((isset($_SESSION['mes_listes_avec_nom']))&&($_SESSION['mes_listes_avec_nom']=='y'))||
				(!isset($_SESSION['mes_listes_avec_nom']))) {echo "checked ";}
			echo "/><label for='avec_nom'> le nom</label><br />\n";
		
			echo "<input type='checkbox' id='avec_prenom' name='avec_prenom' value='y' ";
			if(((isset($_SESSION['mes_listes_avec_prenom']))&&($_SESSION['mes_listes_avec_prenom']=='y'))||
				(!isset($_SESSION['mes_listes_avec_prenom']))) {echo "checked ";}
			echo "/><label for='avec_prenom'> le prénom</label><br />\n";
		
			echo "<input type='checkbox' id='avec_sexe' name='avec_sexe' value='y' ";
			if(((isset($_SESSION['mes_listes_avec_sexe']))&&($_SESSION['mes_listes_avec_sexe']=='y'))||
				(!isset($_SESSION['mes_listes_avec_sexe']))) {echo "checked ";}
			echo "/><label for='avec_sexe'> le sexe des élèves</label><br />\n";

			echo "<input type='checkbox' id='avec_naiss' name='avec_naiss' value='y' ";
			if(((isset($_SESSION['mes_listes_avec_naiss']))&&($_SESSION['mes_listes_avec_naiss']=='y'))||
				(!isset($_SESSION['mes_listes_avec_naiss']))) {echo "checked ";}
			echo "/><label for='avec_naiss'> la date de naissance</label>";

			echo " au format ";
			echo "<input type='radio' id='format_naiss_aaaammjj' name='format_naiss' value='aaaammjj' ";
			if(((isset($_SESSION['mes_listes_format_naiss']))&&($_SESSION['mes_listes_format_naiss']=='aaaammjj'))||
				(!isset($_SESSION['mes_listes_format_naiss']))) {echo "checked ";}
			//echo " onchange=\"document.getElementById('avec_naiss').checked='true'\" ";
			echo "/><label for='format_naiss_aaaammjj'>&nbsp;aaaa-mm-jj</label>";
			echo " ou ";
			echo "<input type='radio' id='format_naiss_jjmmaaaa' name='format_naiss' value='jjmmaaaa' ";
			if((isset($_SESSION['mes_listes_format_naiss']))&&($_SESSION['mes_listes_format_naiss']=='jjmmaaaa')) {echo "checked ";}
			echo "/><label for='format_naiss_jjmmaaaa'>&nbsp;jj/mm/aaaa</label>";
			echo "<br />\n";

			echo "<input type='checkbox' id='avec_email' name='avec_email' value='y' ";
			if((isset($_SESSION['mes_listes_avec_email']))&&($_SESSION['mes_listes_avec_email']=='y')) {echo "checked ";}
			echo "/><label for='avec_email'> l'email</label><br />\n";

			echo "<input type='checkbox' id='avec_doublant' name='avec_doublant' value='y' ";
			if((isset($_SESSION['mes_listes_avec_doublant']))&&($_SESSION['mes_listes_avec_doublant']=='y')) {echo "checked ";}
			echo "/><label for='avec_doublant'> le statut redoublant ou non</label><br />\n";

			echo "<input type='checkbox' id='avec_regime' name='avec_regime' value='y' ";
			if((isset($_SESSION['mes_listes_avec_regime']))&&($_SESSION['mes_listes_avec_regime']=='y')) {echo "checked ";}
			echo "/><label for='avec_regime'> le régime</label><br />\n";

			/*
			//echo "<input type='checkbox' id='avec_prof' name='avec_prof' value='y' /><label for='avec_prof'> les informations professeurs</label><br />\n";
			//echo "<input type='checkbox' id='avec_statut' name='avec_statut' value='y' /><label for='avec_statut'> le statut</label><br />\n";
			echo "<input type='checkbox' id='avec_no_gep' name='avec_no_gep' value='y' ";
			if((isset($_SESSION['mes_listes_avec_no_gep']))&&($_SESSION['mes_listes_avec_no_gep']=='y')) {echo "checked ";}
			echo "/><label for='avec_no_gep'> le numéro national des élèves (INE)</label><br />\n";
		
			echo "<input type='checkbox' id='avec_elenoet' name='avec_elenoet' value='y' ";
			if((isset($_SESSION['mes_listes_avec_elenoet']))&&($_SESSION['mes_listes_avec_elenoet']=='y')) {echo "checked ";}
			echo "/><label for='avec_elenoet'> le numéro interne (ELENOET)</label><br />\n";
		
			echo "<input type='checkbox' id='avec_ele_id' name='avec_ele_id' value='y' ";
			if((isset($_SESSION['mes_listes_avec_ele_id']))&&($_SESSION['mes_listes_avec_ele_id']=='y')) {echo "checked ";}
			echo "/><label for='avec_ele_id'> le numéro ELE_ID</label><br />\n";
			*/

			echo "<input type='hidden' name='mode' value='personnalise' />\n";

			echo "<input type='submit' value='Exporter' />\n";
			echo "</fieldset>\n";
			echo "</form>\n";
		}

		require("../lib/footer.inc.php");
		die();
	}

}

if(isset($id_groupe)) {
	$current_group=get_group($id_groupe);
}
elseif(isset($id_classe)) {
	$classe=get_class_from_id($id_classe);
}
else {
	if($_SESSION['statut']=='cpe'){
		echo "<p>Sélectionnez la classe et la période pour lesquels vous souhaitez télécharger un fichier CSV des ".$gepiSettings['denomination_eleves']."&nbsp;:</p>\n";
		if(getSettingAOui('GepiAccesTouteFicheEleveCpe')) {
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY classe";
		}
		else {
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe=c.id ORDER BY c.classe";
		}
	}
	elseif($_SESSION['statut']=='scol'){
		echo "<p>Sélectionnez la classe et la période pour lesquels vous souhaitez télécharger un fichier CSV des ".$gepiSettings['denomination_eleves']."&nbsp;:</p>\n";
		//$sql="SELECT id,classe FROM classes ORDER BY classe";
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	else {
		echo "<p>Sélectionnez la classe et la période pour lesquels vous souhaitez télécharger un fichier CSV des ".$gepiSettings['denomination_eleves']."&nbsp;:</p>\n";
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY classe";
	}
	$result_classes=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_classes = mysqli_num_rows($result_classes);

	if(mysqli_num_rows($result_classes)==0){
		echo "<p>Il semble qu'aucune classe n'ait encore été créée...<br />... ou alors aucune classe ne vous a été attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	$nb_classes=mysqli_num_rows($result_classes);
	$nb_class_par_colonne=round($nb_classes/3);

	$tab_id_classe=array();
	$tab_classe=array();

	$message_erreur="";
	echo "<table width='100%'>\n";
	echo "<tr valign='top' align='left'>\n";
	$cpt=0;
	//echo "<td style='padding: 0 10px 0 10px'>\n";
	echo "<td>\n";
	echo "<table border='0'>\n";
	while($lig_class=mysqli_fetch_object($result_classes)){
		if(($cpt>0)&&(round($cpt/$nb_class_par_colonne)==$cpt/$nb_class_par_colonne)){
			echo "</table>\n";
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td>\n";
			echo "<table border='0'>\n";
		}

		$sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$lig_class->id' ORDER BY num_periode";
		$res_per=mysqli_query($GLOBALS["mysqli"], $sql);

		if(mysqli_num_rows($res_per)==0){
			$message_erreur.="<p><span style='color:red'>ERREUR&nbsp;:</span> Aucune période n'est définie pour la classe $lig_class->classe</p>\n";
			/*
			echo "<p>ERREUR: Aucune période n'est définie pour la classe $lig_class->classe</p>\n";
			echo "</body></html>\n";
			die();
			*/
		}
		else{
			$tab_classe[]=$lig_class->classe;
			$tab_id_classe[]=$lig_class->id;

			echo "<tr>\n";
			echo "<td>$lig_class->classe</td>\n";
			while($lig_per=mysqli_fetch_object($res_per)){
				echo "<td> - <a href='get_csv.php?id_classe=$lig_class->id&amp;periode_num=$lig_per->num_periode'>".$lig_per->nom_periode."</a></td>\n";
			}
			echo "</tr>\n";
		}
		$cpt++;
	}
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo $message_erreur;

}

echo "<br />\n";

echo "<form action='get_csv.php' method='post'>\n";
echo "<fieldset style='border: 1px solid grey;background-image: url(\"../images/background/opacite50.png\");'>\n";

if(isset($current_group)) {
	echo "<p class='bold'>Liste de ".$current_group['name']." (".$current_group['description'].") en ".$current_group['classlist_string']."&nbsp;:</p>\n";
	echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";

	echo "<div id='div_champs_periodes'>\n";
	for($i=1;$i<$current_group['nb_periode'];$i++) {
		echo "<input type='radio' id='periode_num_$i' name='periode_num' value='".$i."' ";
		//if($i==1) {echo "checked ";}
		if((isset($_GET['periode_num']))&&(is_numeric($_GET['periode_num']))&&($_GET['periode_num']<=$current_group['nb_periode'])) {
			if($_GET['periode_num']==$i) {
				echo "checked ";
			}
		}
		elseif((isset($_SESSION['mes_listes_periode_num']))&&($_SESSION['mes_listes_periode_num']<=$current_group['nb_periode'])) {
			if($_SESSION['mes_listes_periode_num']==$i) {
				echo "checked ";
			}
		}
		else {
			if($i==1) {echo "checked ";}
		}
		echo "/><label for='periode_num_$i'> Période $i</label><br />\n";
	}
	echo "</div>\n";

}
elseif(isset($classe)) {
	echo "<p class='bold'>Liste de $classe&nbsp;:</p>\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";

	include("../lib/periodes.inc.php");

	echo "<div id='div_champs_periodes'>\n";
	for($i=1;$i<$nb_periode;$i++) {
		echo "<input type='radio' id='periode_num_$i' name='periode_num' value='".$i."' ";
		//if($i==1) {echo "checked ";}
		if((isset($_GET['periode_num']))&&(is_numeric($_GET['periode_num']))&&($_GET['periode_num']<=$nb_periode)) {
			if($_GET['periode_num']==$i) {
				echo "checked ";
			}
		}
		elseif((isset($_SESSION['mes_listes_periode_num']))&&($_SESSION['mes_listes_periode_num']<=$nb_periode)) {
			if($_SESSION['mes_listes_periode_num']==$i) {
				echo "checked ";
			}
		}
		else {
			if($i==1) {echo "checked ";}
		}
		echo "/><label for='periode_num_$i'> Période $i</label><br />\n";
	}
	echo "</div>\n";

}
else {
	echo "<p class='bold'>Listes personnalisées&nbsp;:</p>\n";
	echo "<select name='id_classe' id='id_classe' onchange='update_champs_periode()'>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<option value='".$tab_id_classe[$i]."'>".$tab_classe[$i]."</option>\n";
	}
	echo "</select>\n";

	$sql="SELECT MAX(num_periode) AS maxper FROM periodes WHERE id_classe='".$tab_id_classe[0]."';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_per=mysql_result($res, 0);

	echo "<div id='div_champs_periodes'>\n";
	for($i=1;$i<=$nb_per;$i++) {
		echo "<input type='radio' id='periode_num_$i' name='periode_num' value='".$i."' ";
		//if($i==1) {echo "checked ";}
		if((isset($_GET['periode_num']))&&(is_numeric($_GET['periode_num']))&&($_GET['periode_num']<=$nb_per)) {
			if($_GET['periode_num']==$i) {
				echo "checked ";
			}
		}
		elseif((isset($_SESSION['mes_listes_periode_num']))&&($_SESSION['mes_listes_periode_num']<=$nb_per)) {
			if($_SESSION['mes_listes_periode_num']==$i) {
				echo "checked ";
			}
		}
		else {
			if($i==1) {echo "checked ";}
		}
		echo "/><label for='periode_num_$i'> Période $i</label><br />\n";
	}
	echo "</div>\n";

	echo "<script type='text/javascript'>
	// <![CDATA[
	function update_champs_periode() {
		id_classe=document.getElementById('id_classe').value;
		//alert('id_classe='+id_classe);
		new Ajax.Updater($('div_champs_periodes'),'update_champs_periode.php?id_classe='+id_classe+'&csrf_alea=".add_token_in_js_func()."',{method: 'get'});
	}
	//]]>
</script>\n";
}

echo "<p><b>Inclure</b>&nbsp;:<br />\n";
echo "<input type='checkbox' id='avec_classe' name='avec_classe' value='y' ";
if(((isset($_SESSION['mes_listes_avec_classe']))&&($_SESSION['mes_listes_avec_classe']=='y'))||
	(!isset($_SESSION['mes_listes_avec_classe']))) {echo "checked ";}
echo "/><label for='avec_classe'> le nom de la classe</label><br />\n";

echo "<input type='checkbox' id='avec_login' name='avec_login' value='y' ";
if(((isset($_SESSION['mes_listes_avec_login']))&&($_SESSION['mes_listes_avec_login']=='y'))||
	(!isset($_SESSION['mes_listes_avec_login']))) {echo "checked ";}
echo "/><label for='avec_login'> le login des élèves</label><br />\n";

echo "<input type='checkbox' id='avec_nom' name='avec_nom' value='y' ";
if(((isset($_SESSION['mes_listes_avec_nom']))&&($_SESSION['mes_listes_avec_nom']=='y'))||
	(!isset($_SESSION['mes_listes_avec_nom']))) {echo "checked ";}
echo "/><label for='avec_nom'> le nom</label><br />\n";

echo "<input type='checkbox' id='avec_prenom' name='avec_prenom' value='y' ";
if(((isset($_SESSION['mes_listes_avec_prenom']))&&($_SESSION['mes_listes_avec_prenom']=='y'))||
	(!isset($_SESSION['mes_listes_avec_prenom']))) {echo "checked ";}
echo "/><label for='avec_prenom'> le prénom</label><br />\n";

echo "<input type='checkbox' id='avec_sexe' name='avec_sexe' value='y' ";
if(((isset($_SESSION['mes_listes_avec_sexe']))&&($_SESSION['mes_listes_avec_sexe']=='y'))||
	(!isset($_SESSION['mes_listes_avec_sexe']))) {echo "checked ";}
echo "/><label for='avec_sexe'> le sexe des élèves</label><br />\n";

echo "<input type='checkbox' id='avec_naiss' name='avec_naiss' value='y' ";
if(((isset($_SESSION['mes_listes_avec_naiss']))&&($_SESSION['mes_listes_avec_naiss']=='y'))||
	(!isset($_SESSION['mes_listes_avec_naiss']))) {echo "checked ";}
echo "/><label for='avec_naiss'> la date de naissance</label>\n";

echo " au format ";
echo "<input type='radio' id='format_naiss_aaaammjj' name='format_naiss' value='aaaammjj' ";
if(((isset($_SESSION['mes_listes_format_naiss']))&&($_SESSION['mes_listes_format_naiss']=='aaaammjj'))||
	(!isset($_SESSION['mes_listes_format_naiss']))) {echo "checked ";}
//echo " onchange=\"document.getElementById('avec_naiss').checked='true'\" ";
echo "/><label for='format_naiss_aaaammjj'>&nbsp;aaaa-mm-jj</label>";
echo " ou ";
echo "<input type='radio' id='format_naiss_jjmmaaaa' name='format_naiss' value='jjmmaaaa' ";
if((isset($_SESSION['mes_listes_format_naiss']))&&($_SESSION['mes_listes_format_naiss']=='jjmmaaaa')) {echo "checked ";}
echo "/><label for='format_naiss_jjmmaaaa'>&nbsp;jj/mm/aaaa</label>";
echo "<br />\n";

if(getSettingValue('ele_lieu_naissance')=='y') {
	echo "<input type='checkbox' id='avec_lieu_naiss' name='avec_lieu_naiss' value='y' ";
	if((isset($_SESSION['mes_listes_avec_lieu_naiss']))&&($_SESSION['mes_listes_avec_lieu_naiss']=='y')) {echo "checked ";}
	echo "/><label for='avec_lieu_naiss'> le lieu de naissance</label><br />\n";
}

echo "<input type='checkbox' id='avec_email' name='avec_email' value='y' ";
if((isset($_SESSION['mes_listes_avec_email']))&&($_SESSION['mes_listes_avec_email']=='y')) {echo "checked ";}
echo "/><label for='avec_email'> l'email</label><br />\n";

if(isset($current_group)) {
	echo "<input type='checkbox' id='avec_prof' name='avec_prof' value='y' ";
	if((isset($_SESSION['mes_listes_avec_prof']))&&($_SESSION['mes_listes_avec_prof']=='y')) {echo "checked ";}
	echo "/><label for='avec_prof'> les informations professeurs</label><br />\n";

	echo "<input type='checkbox' id='avec_statut' name='avec_statut' value='y' ";
	if((isset($_SESSION['mes_listes_avec_statut']))&&($_SESSION['mes_listes_avec_statut']=='y')) {echo "checked ";}
	echo "/><label for='avec_statut'> le statut (<i>élève ou professeur</i>)</label><br />\n";
}

echo "<input type='checkbox' id='avec_no_gep' name='avec_no_gep' value='y' ";
if((isset($_SESSION['mes_listes_avec_no_gep']))&&($_SESSION['mes_listes_avec_no_gep']=='y')) {echo "checked ";}
echo "/><label for='avec_no_gep'> le numéro national des élèves (INE)</label><br />\n";

echo "<input type='checkbox' id='avec_elenoet' name='avec_elenoet' value='y' ";
if((isset($_SESSION['mes_listes_avec_elenoet']))&&($_SESSION['mes_listes_avec_elenoet']=='y')) {echo "checked ";}
echo "/><label for='avec_elenoet'> le numéro interne (ELENOET)</label><br />\n";

echo "<input type='checkbox' id='avec_ele_id' name='avec_ele_id' value='y' ";
if((isset($_SESSION['mes_listes_avec_ele_id']))&&($_SESSION['mes_listes_avec_ele_id']=='y')) {echo "checked ";}
echo "/><label for='avec_ele_id'> le numéro ELE_ID</label><br />\n";

echo "<input type='checkbox' id='avec_doublant' name='avec_doublant' value='y' ";
if((isset($_SESSION['mes_listes_avec_doublant']))&&($_SESSION['mes_listes_avec_doublant']=='y')) {echo "checked ";}
echo "/><label for='avec_doublant'> le statut redoublant ou non</label><br />\n";

echo "<input type='checkbox' id='avec_regime' name='avec_regime' value='y' ";
if((isset($_SESSION['mes_listes_avec_regime']))&&($_SESSION['mes_listes_avec_regime']=='y')) {echo "checked ";}
echo "/><label for='avec_regime'> le régime</label><br />\n";

if(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe'))) {
	echo "<input type='checkbox' id='avec_infos_resp' name='avec_infos_resp' value='y' ";
	if((isset($_SESSION['mes_listes_avec_infos_resp']))&&($_SESSION['mes_listes_avec_infos_resp']=='y')) {echo "checked ";}
	echo "/><label for='avec_infos_resp'> les informations responsables (<em>nom, prénom et téléphones</em>)</label><br />\n";
}
echo "<input type='hidden' name='mode' value='personnalise' />\n";

echo "<input type='submit' value='Exporter' />\n";
echo "</fieldset>\n";
echo "</form>\n";

require("../lib/footer.inc.php");
?>
