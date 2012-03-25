<?php
/*
*
*  Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$no_header=isset($_POST['no_header']) ? $_POST['no_header'] : (isset($_GET['no_header']) ? $_GET['no_header'] : 'n');

$ajout_href_1="";
$ajout_href_2="";
$ajout_form="";
if($no_header!='y') {
	$titre_page = "Equipe pédagogique";
}
else {
	$ajout_href_1="?no_header=y";
	$ajout_href_2="&amp;no_header=y";
	$ajout_form="<input type='hidden' name='no_header' value='y' />\n";

}
//**************** EN-TETE **************************************
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

$id_classe=isset($_GET['id_classe']) ? $_GET["id_classe"] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
if(isset($id_classe)){
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

	echo "<p class='bold'>";
	echo "<a href='".$_SERVER['PHP_SELF'].$ajout_href_1."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

	if (!is_numeric($id_classe)){
		echo "</p>\n";

		echo "<p><b>ERREUR</b>: Le numéro de classe choisi n'est pas valide.</p>\n";
		echo "<p><a href='".$_SERVER['PHP_SELF'].$ajout_href_1."'>Retour</a></p>\n";
	}
	else{
		// =================================
		// AJOUT: boireaus
		//$sql="SELECT id, classe FROM classes ORDER BY classe";
		if($_SESSION['statut']=='scolarite'){
			//$sql="SELECT id,classe FROM classes ORDER BY classe";
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
		}
		if($_SESSION['statut']=='professeur'){
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc,j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgc.id_groupe=jgp.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
		}
		if($_SESSION['statut']=='cpe'){
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe=c.id ORDER BY c.classe";
		}
		if($_SESSION['statut']=='administrateur'){
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
		}

		if(($_SESSION['statut']=='scolarite')&&(getSettingValue("GepiAccesVisuToutesEquipScol") =="yes")){
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
		}
		if(($_SESSION['statut']=='cpe')&&(getSettingValue("GepiAccesVisuToutesEquipCpe") =="yes")){
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
		}
		if(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesVisuToutesEquipProf") =="yes")){
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
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

		if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec".$ajout_href_2."'>Classe précédente</a>";}
		if($chaine_options_classes!="") {
			echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
			echo $chaine_options_classes;
			echo "</select>\n";
		}
		if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv".$ajout_href_2."'>Classe suivante</a>";}

		echo $ajout_form;
		
		echo "</p>\n";
		echo "</form>\n";

		$classe=get_classe($id_classe);

		$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');

		function accord_pluriel($nombre){
			if($nombre>1){
				return "s";
			}
		}

		if(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesVisuToutesEquipProf")!="yes")){
			$test_prof_classe = sql_count(sql_query("SELECT login FROM j_groupes_classes jgc,j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgc.id_groupe=jgp.id_groupe AND jgc.id_classe='$id_classe'"));
			if($test_prof_classe==0){
				echo "<p>ERREUR: Vous n'avez pas accès à cette classe.</p>\n";
				echo "</body></html>\n";
				die();
			}
		}
		// On vérifie les droits donnés par l'administrateur
		if((getSettingValue("GepiAccesVisuToutesEquipCpe") == "yes") AND $_SESSION['statut']=='cpe'){
			echo '<p style="font-size: 0.7em; color: green;">L\'administrateur vous a donné l\'accès à toutes les classes.</p>';
		}elseif($_SESSION['statut']=='cpe'){
			$test_cpe_classe = sql_count(sql_query("SELECT e_login FROM j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe='$id_classe'"));
			if($test_cpe_classe==0){
				echo "<p>ERREUR: Vous n'avez pas accès à cette classe.</p>\n";
				echo "</body></html>\n";
				die();
			}
		}

		echo "<h3>Equipe pédagogique de la classe de ".$classe["classe"]."</h3>\n";

		echo "<script type='text/javascript' language='JavaScript'>
	var fen;
	function ouvre_popup(id_groupe,id_classe){
		eval(\"fen=window.open('popup.php?id_groupe=\"+id_groupe+\"&id_classe=\"+id_classe+\"','','width=400,height=400,menubar=yes,scrollbars=yes')\");
		setTimeout('fen.focus()',500);
	}
</script>\n";


		unset($tabmail);
		$tabmail=array();

		$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe'";
		$res_eleves_classe=mysql_query($sql);
		$nb_eleves_classe=mysql_num_rows($res_eleves_classe);

		echo "<table border='0' summary='Equipe'>\n";

		// Liste des CPE:
		$sql="SELECT DISTINCT u.nom,u.prenom,u.email,jec.cpe_login FROM utilisateurs u,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.e_login=jecl.login AND jecl.id_classe='$id_classe' AND u.login=jec.cpe_login ORDER BY jec.cpe_login";
		$result_cpe=mysql_query($sql);
		if(mysql_num_rows($result_cpe)>0){
			while($lig_cpe=mysql_fetch_object($result_cpe)){
				echo "<tr valign='top'><td>VIE SCOLAIRE</td>\n";

				$sql="SELECT DISTINCT nom,prenom FROM eleves e,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.e_login=jecl.login AND jec.e_login=e.login AND jecl.id_classe='$id_classe' AND jec.cpe_login='$lig_cpe->cpe_login'";
				$result_eleve=mysql_query($sql);
				$nb_eleves=mysql_num_rows($result_eleve);
				echo "<td><a href='javascript:ouvre_popup(\"VIE_SCOLAIRE\",\"$id_classe\");'>".$nb_eleves." ";
		if ($nb_eleves > 1) { echo $gepiSettings['denomination_eleves'];} else { echo $gepiSettings['denomination_eleve'];}
		echo "</a></td>\n";
				

				echo "<td>";
				if($lig_cpe->email!=""){
					echo "<a href='mailto:$lig_cpe->email?".urlencode("subject=[GEPI] classe=".$classe['classe'])."'>".my_strtoupper($lig_cpe->nom)." ".casse_mot($lig_cpe->prenom,'majf2')."</a>";
					$tabmail[]=$lig_cpe->email;
				}
				else{
					echo my_strtoupper($lig_cpe->nom)." ".casse_mot($lig_cpe->prenom,'majf2');
				}
				echo "</td></tr>\n";
			}
		}
		//echo "</table>\n";

		echo "<tr><td colspan='3'>&nbsp;</td></tr>\n";
		//echo "<br />\n";

		//echo "<table border='0'>\n";
		//$sql="SELECT jgm.id_matiere,jgm.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgc.id_groupe=jgm.id_groupe AND jgc.id_classe='$id_classe' ORDER BY jgc.priorite, jgm.id_matiere";
		$sql="SELECT m.nom_complet,jgm.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm, matieres m WHERE jgc.id_groupe=jgm.id_groupe AND m.matiere=jgm.id_matiere AND jgc.id_classe='$id_classe' ORDER BY jgc.priorite, m.matiere";
		//echo "$sql<br />";
		$result_grp=mysql_query($sql);
		while($lig_grp=mysql_fetch_object($result_grp)){

			// Récupération des effectifs du groupe...
			// ... parmi les membres de la classe
			$sql="SELECT DISTINCT e.nom,e.prenom,c.classe FROM j_eleves_groupes jeg, eleves e, j_eleves_classes jec, j_groupes_classes jgc, classes c WHERE jeg.login=e.login AND jeg.id_groupe='$lig_grp->id_groupe' AND jgc.id_classe=c.id AND jgc.id_groupe=jeg.id_groupe AND jec.id_classe=c.id AND jec.login=e.login AND c.id='$id_classe' ORDER BY e.nom,e.prenom";
			$res_eleves=mysql_query($sql);
			$nb_eleves=mysql_num_rows($res_eleves);

			// Le groupe est-il composé uniquement d'élèves de la classe?
			$sql="SELECT * FROM j_groupes_classes jgc WHERE jgc.id_groupe='$lig_grp->id_groupe'";
			$res_nb_class_grp=mysql_query($sql);
			$nb_class_grp=mysql_num_rows($res_nb_class_grp);


			// Matière correspondant au groupe:
			echo "<tr valign='top'><td>".htmlspecialchars($lig_grp->nom_complet)."</td>\n";

			echo "<td>";
			echo "<a href='javascript:ouvre_popup(\"$lig_grp->id_groupe\",\"$id_classe\");'>".$nb_eleves." ";
		if ($nb_eleves > 1) { echo $gepiSettings['denomination_eleves'];} else { echo $gepiSettings['denomination_eleve'];}
		echo "</a>\n";
			if($nb_class_grp>1){
				// Effectif...
				// ... pour tout le groupe
				$sql="SELECT DISTINCT e.nom,e.prenom,c.classe FROM j_eleves_groupes jeg, eleves e, j_eleves_classes jec, j_groupes_classes jgc, classes c WHERE jeg.login=e.login AND jeg.id_groupe='$lig_grp->id_groupe' AND jgc.id_classe=c.id AND jgc.id_groupe=jeg.id_groupe AND jec.id_classe=c.id AND jec.login=e.login ORDER BY e.nom,e.prenom";
				$res_tous_eleves_grp=mysql_query($sql);
				$nb_tous_eleves_grp=mysql_num_rows($res_tous_eleves_grp);

				echo " sur <a href='javascript:ouvre_popup(\"$lig_grp->id_groupe\",\"\");'>".$nb_tous_eleves_grp." ";
		if ($nb_tous_eleves_grp > 1) { echo $gepiSettings['denomination_eleves'];} else { echo $gepiSettings['denomination_eleve'];}
		echo "</a>\n";
		}
		
			echo "</td>\n";


			// Professeurs
			echo "<td>";
			$sql="SELECT jgp.login,u.nom,u.prenom,u.email FROM j_groupes_professeurs jgp,utilisateurs u WHERE jgp.id_groupe='$lig_grp->id_groupe' AND u.login=jgp.login";
			//echo "$sql<br />";
			$result_prof=mysql_query($sql);
			while($lig_prof=mysql_fetch_object($result_prof)){
				if($lig_prof->email!=""){
					echo "<a href='mailto:$lig_prof->email?".urlencode("subject=[GEPI] classe=".$classe['classe'])."'>".my_strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom,'majf2')."</a>";
					$tabmail[]=$lig_prof->email;
				}
				else{
					echo my_strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom,'majf2');
				}

				// Le prof est-il PP d'au moins un élève de la classe?
				$sql="SELECT * FROM j_eleves_professeurs WHERE id_classe='$id_classe' AND professeur='$lig_prof->login'";
				//echo " (<i>$sql</i>)\n";
				$res_pp=mysql_query($sql);
				if(mysql_num_rows($res_pp)>0){

					echo " (<i>".$gepi_prof_suivi."</i>)";
				}
				echo "<br />\n";
			}
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";

		$chaine_mail="";
		if(count($tabmail)>0){
			unset($tabmail2);
			$tabmail2=array();
			//$tabmail=array_unique($tabmail);
			//sort($tabmail);
			$chaine_mail=$tabmail[0];
			for ($i=1;$i<count($tabmail);$i++) {
				if((isset($tabmail[$i]))&&(!in_array($tabmail[$i],$tabmail2))) {
					$chaine_mail.=",".$tabmail[$i];
					$tabmail2[]=$tabmail[$i];
				}
			}
			echo "<p>Envoyer un <a href='mailto:$chaine_mail?".rawurlencode("subject=[GEPI] classe ".$classe['classe'])."'>mail à tous les membres de l'équipe</a>.</p>\n";
		}
	}
}
else {

	echo "<p class='bold'>";
	echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	echo "</p>\n";

	echo "<h3>Equipe pédagogique d'une classe</h3>\n";
	//echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<p>Choix de la classe:</p>\n";

	//$sql="SELECT id,classe FROM classes ORDER BY classe";
	if($_SESSION['statut']=='scolarite'){
		//$sql="SELECT id,classe FROM classes ORDER BY classe";
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	if($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc,j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgc.id_groupe=jgp.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
	}
	if($_SESSION['statut']=='cpe'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe=c.id ORDER BY c.classe";
	}
	if($_SESSION['statut']=='administrateur'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}

	if(($_SESSION['statut']=='scolarite')&&(getSettingValue("GepiAccesVisuToutesEquipScol") =="yes")){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	if(($_SESSION['statut']=='cpe')&&(getSettingValue("GepiAccesVisuToutesEquipCpe") =="yes")){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	if(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesVisuToutesEquipProf") =="yes")){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}

	$result_classes=mysql_query($sql);
	$nb_classes = mysql_num_rows($result_classes);
	//echo "<select name='id_classe' size='1'>\n";
	//echo "<option value='null'>-- Sélectionner la classe --</option>\n";
	/*
	for ($i=0;$i<$nb_classes;$i++) {
		$classe=mysql_result($query, $i, "classe");
		$id_classe=mysql_result($query, $i, "id");
		echo "<option value='$id_classe'>" . htmlspecialchars($classe) . "</option>\n";
	}
	*/
	if(mysql_num_rows($result_classes)==0){
		echo "<p>Il semble qu'aucune classe n'ait encore été créée...<br />... ou alors aucune classe ne vous a été attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else{
		$nb_classes=mysql_num_rows($result_classes);
		$nb_class_par_colonne=round($nb_classes/3);
		echo "<table width='100%' summary='Choix de la classe'>\n";
		echo "<tr valign='top' align='center'>\n";
		$cpt=0;
		//echo "<td style='padding: 0 10px 0 10px'>\n";
		echo "<td>\n";
		while($lig_class=mysql_fetch_object($result_classes)){
			if(($cpt>0)&&(round($cpt/$nb_class_par_colonne)==$cpt/$nb_class_par_colonne)){
				echo "</td>\n";
				//echo "<td style='padding: 0 10px 0 10px'>\n";
				echo "<td>\n";
			}
			//echo "<option value='$lig_class->id'>" . htmlspecialchars("$lig_class->classe") . "</option>\n";
			echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$lig_class->id".$ajout_href_2."'>".htmlspecialchars("$lig_class->classe") . "</a><br />\n";
			$cpt++;
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
	/*
	echo "</select>\n";
	echo "<input type='submit' value='Valider' />\n";
	echo "</form>\n";
	*/
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
