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

$id_classe=isset($_GET['id_classe']) ? $_GET["id_classe"] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
$export=isset($_GET['export']) ? $_GET["export"] : (isset($_POST['export']) ? $_POST["export"] : NULL);

$acces_classe="n";
// Remplissage d'un tableau pour la classe choisie
if((isset($id_classe))&&(is_numeric($id_classe))) {
	$acces_classe="y";

	if(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesVisuToutesEquipProf")!="yes")){
		$test_prof_classe = sql_count(sql_query("SELECT login FROM j_groupes_classes jgc,j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgc.id_groupe=jgp.id_groupe AND jgc.id_classe='$id_classe'"));
		if($test_prof_classe==0) {
			$acces_classe="n";
		}
	}
	// On vérifie les droits donnés par l'administrateur
	if((getSettingValue("GepiAccesVisuToutesEquipCpe") == "yes") AND $_SESSION['statut']=='cpe'){
		//echo '<p style="font-size: 0.7em; color: green;">L\'administrateur vous a donné l\'accès à toutes les classes.</p>';
		$acces_classe="y";
	}elseif($_SESSION['statut']=='cpe'){
		$test_cpe_classe = sql_count(sql_query("SELECT e_login FROM j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe='$id_classe'"));
		if($test_cpe_classe==0){
			$acces_classe="n";
		}
	}

	if($acces_classe=="y") {
		$classe=get_classe($id_classe);

		include("../lib/periodes.inc.php");

		$tab_enseignements=array();
		$tab_mail=array();
		$cpt=0;

		// Liste des CPE:
		$sql="SELECT DISTINCT u.nom,u.prenom,u.email,jec.cpe_login FROM utilisateurs u,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.e_login=jecl.login AND jecl.id_classe='$id_classe' AND u.login=jec.cpe_login ORDER BY u.nom, u.prenom, jec.cpe_login";
		$result_cpe=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($result_cpe)>0){
			$tab_enseignements[$cpt]['id_groupe']="VIE_SCOLAIRE";
			$tab_enseignements[$cpt]['grp_name']="VIE SCOLAIRE";
			$tab_enseignements[$cpt]['grp_description']="VIE SCOLAIRE";

			for($loop=0;$loop<count($nom_periode);$loop++) {
				$sql="SELECT DISTINCT nom,prenom FROM eleves e,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.e_login=jecl.login AND jec.e_login=e.login AND jecl.id_classe='$id_classe' AND jecl.periode='".($loop+1)."';";
				$result_eleve=mysqli_query($GLOBALS["mysqli"], $sql);
				$tab_enseignements[$cpt]['nb_eleves'][$loop+1]=mysqli_num_rows($result_eleve);
			}

			$cpt2=0;
			while($lig_cpe=mysqli_fetch_object($result_cpe)) {

				$tab_enseignements[$cpt]['prof'][$cpt2]['designation_prof']=my_strtoupper($lig_cpe->nom)." ".casse_mot($lig_cpe->prenom,'majf2');
				if($lig_cpe->email!=""){
					$tab_enseignements[$cpt]['prof'][$cpt2]['designation_prof_mailto']="<a href='mailto:$lig_cpe->email?".urlencode("subject=".getSettingValue('gepiPrefixeSujetMail')."[GEPI] classe=".$classe['classe'])."' title=\"Envoyer un mail\">".my_strtoupper($lig_cpe->nom)." ".casse_mot($lig_cpe->prenom,'majf2')."</a>";
					$tab_enseignements[$cpt]['prof'][$cpt2]['mail']=$lig_cpe->email;
					$tabmail[]=$lig_cpe->email;
				}

				for($loop=0;$loop<count($nom_periode);$loop++) {
					$sql="SELECT DISTINCT nom,prenom FROM eleves e,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.e_login=jecl.login AND jec.e_login=e.login AND jecl.id_classe='$id_classe' AND jec.cpe_login='$lig_cpe->cpe_login' AND jecl.periode='".($loop+1)."';";
					$result_eleve=mysqli_query($GLOBALS["mysqli"], $sql);
					$tab_enseignements[$cpt]['prof'][$cpt2]['nb_eleves'][$loop+1]=mysqli_num_rows($result_eleve);
				}

				$cpt2++;
			}
			$cpt++;
		}

		// Liste des enseignements et professeurs:
		$sql="SELECT m.nom_complet,jgm.id_groupe, g.name, g.description FROM j_groupes_classes jgc, j_groupes_matieres jgm, matieres m, groupes g WHERE jgc.id_groupe=jgm.id_groupe AND m.matiere=jgm.id_matiere AND jgc.id_classe='$id_classe' AND g.id=jgc.id_groupe ORDER BY jgc.priorite, m.matiere";
		//echo "$sql<br />";
		$result_grp=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig_grp=mysqli_fetch_object($result_grp)){
			$tab_enseignements[$cpt]['id_groupe']=$lig_grp->id_groupe;
			$tab_enseignements[$cpt]['grp_name']=$lig_grp->name;
			$tab_enseignements[$cpt]['matiere_nom_complet']=$lig_grp->nom_complet;
			$tab_enseignements[$cpt]['grp_description']=$lig_grp->description;

			// Le groupe est-il composé uniquement d'élèves de la classe?
			$sql="SELECT * FROM j_groupes_classes jgc WHERE jgc.id_groupe='$lig_grp->id_groupe'";
			$res_nb_class_grp=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_class_grp=mysqli_num_rows($res_nb_class_grp);
			$tab_enseignements[$cpt]['nb_class_grp']=$nb_class_grp;

			for($loop=0;$loop<count($nom_periode);$loop++) {
				// Récupération des effectifs du groupe...
				// ... parmi les membres de la classe
				$sql="SELECT DISTINCT e.nom,e.prenom,c.classe FROM j_eleves_groupes jeg, 
																	eleves e, 
																	j_eleves_classes jec, 
																	j_groupes_classes jgc, 
																	classes c 
																WHERE jeg.login=e.login AND 
																	jeg.id_groupe='$lig_grp->id_groupe' AND 
																	jgc.id_classe=c.id AND 
																	jgc.id_groupe=jeg.id_groupe AND 
																	jec.id_classe=c.id AND 
																	jec.login=e.login AND 
																	c.id='$id_classe' AND 
																	jeg.periode=jec.periode AND 
																	jec.periode='".($loop+1)."' 
																ORDER BY e.nom,e.prenom";
				$res_eleves=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_eleves=mysqli_num_rows($res_eleves);
				$tab_enseignements[$cpt]['nb_eleves'][$loop+1]=$nb_eleves;

				if($nb_class_grp>1){
					// Effectif...
					// ... pour tout le groupe
					$sql="SELECT DISTINCT e.nom,e.prenom,c.classe FROM j_eleves_groupes jeg, 
																		eleves e, 
																		j_eleves_classes jec, 
																		j_groupes_classes jgc, 
																		classes c 
																	WHERE jeg.login=e.login AND 
																		jeg.id_groupe='$lig_grp->id_groupe' AND 
																		jgc.id_classe=c.id AND 
																		jgc.id_groupe=jeg.id_groupe AND 
																		jec.id_classe=c.id AND 
																		jeg.periode=jec.periode AND 
																		jec.periode='".($loop+1)."' AND 
																		jec.login=e.login 
																	ORDER BY e.nom,e.prenom";
					$res_tous_eleves_grp=mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_tous_eleves_grp=mysqli_num_rows($res_tous_eleves_grp);

					$tab_enseignements[$cpt]['nb_tous_eleves_grp'][$loop+1]=$nb_tous_eleves_grp;
				}
			}


			// Professeurs
			$sql="SELECT jgp.login,u.nom,u.prenom,u.email FROM j_groupes_professeurs jgp,utilisateurs u WHERE jgp.id_groupe='$lig_grp->id_groupe' AND u.login=jgp.login";
			//echo "$sql<br />";
			$result_prof=mysqli_query($GLOBALS["mysqli"], $sql);
			$cpt2=0;
			while($lig_prof=mysqli_fetch_object($result_prof)){

				$tab_enseignements[$cpt]['prof'][$cpt2]['designation_prof']=my_strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom,'majf2');
				if($lig_prof->email!=""){
					$tab_enseignements[$cpt]['prof'][$cpt2]['designation_prof_mailto']="<a href='mailto:$lig_prof->email?".urlencode("subject=".getSettingValue('gepiPrefixeSujetMail')."[GEPI] classe=".$classe['classe'])."' title=\"Envoyer un mail\">".my_strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom,'majf2')."</a>";
					$tab_enseignements[$cpt]['prof'][$cpt2]['mail']=$lig_prof->email;
					$tabmail[]=$lig_prof->email;
				}

				// Le prof est-il PP d'au moins un élève de la classe?
				$tab_enseignements[$cpt]['prof'][$cpt2]['is_pp']="n";
				$sql="SELECT * FROM j_eleves_professeurs WHERE id_classe='$id_classe' AND professeur='$lig_prof->login'";
				//echo " (<i>$sql</i>)\n";
				$res_pp=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_pp)>0){
					$tab_enseignements[$cpt]['prof'][$cpt2]['is_pp']="y";
				}
				$cpt2++;
			}
			$cpt++;
		}
	}
}

// Export CSV: on utilise le tableau $tab_enseignements
if((isset($id_classe))&&(is_numeric($id_classe))&&(isset($export))&&($export=='csv')&&($acces_classe=="y")) {
	$msg="";

	$csv="Identifiant;";
	$csv.="Enseignement;";
	$csv.="Matière;";
	for($loop=0;$loop<count($nom_periode);$loop++) {
		$csv.="Eff.".$nom_periode[$loop+1].";";
	}
	$csv.="Enseignants;Mails;\r\n";

	for($i=0;$i<count($tab_enseignements);$i++) {
		$csv.=$tab_enseignements[$i]['id_groupe'].";";
		$csv.=$tab_enseignements[$i]['grp_name'].";";
		if(isset($tab_enseignements[$i]['matiere_nom_complet'])) {
			$csv.=$tab_enseignements[$i]['matiere_nom_complet'].";";
		}
		else {
			$csv.=";";
		}
		for($loop=0;$loop<count($nom_periode);$loop++) {
			$csv.=$tab_enseignements[$i]['nb_eleves'][$loop+1].";";
		}

		for($loop=0;$loop<count($tab_enseignements[$i]['prof']);$loop++) {
			if($loop>0) {$csv.=", ";}
			$csv.=$tab_enseignements[$i]['prof'][$loop]['designation_prof'];
		}
		$csv.=";";

		$nb_mail=0;
		for($loop=0;$loop<count($tab_enseignements[$i]['prof']);$loop++) {
			if($nb_mail>0) {$csv.=", ";}
			if(isset($tab_enseignements[$i]['prof'][$loop]['mail'])) {
				$csv.=$tab_enseignements[$i]['prof'][$loop]['mail'];
				$nb_mail++;
			}
		}
		$csv.=";\r\n";
	}

	$nom_fic=remplace_accents("Equipe_pedagogique_".$classe['classe'], "all").".csv";
	send_file_download_headers('text/x-csv',$nom_fic);
	echo echo_csv_encoded($csv);
	die();
}

if((isset($_GET['export_prof_suivi']))&&(isset($export))&&($export=='csv')) {
	$msg="";

	if($_SESSION['statut']=='scolarite'){
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

	if(($_SESSION['statut']=='autre')&&(acces('/groupes/visu_profs_class.php', 'autre'))) {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}

	$result_classes=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_classes = mysqli_num_rows($result_classes);
	$tab_classe=array();
	if(mysqli_num_rows($result_classes)==0){
		$msg="<p>Il semble qu'aucune classe n'ait encore été créée...<br />... ou alors aucune classe ne vous a été attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else {
		$nb_classes=mysqli_num_rows($result_classes);
		while($lig_class=mysqli_fetch_object($result_classes)){
			$tab_classe[$lig_class->id]=$lig_class->classe;
		}

		$pp=ucfirst(getSettingValue('gepi_prof_suivi'));
		$csv="Classe;".$pp.";Mails;\r\n";

		$tab_pp=get_tab_prof_suivi();
		foreach($tab_classe as $current_id_classe => $current_classe) {
			if(isset($tab_pp[$current_id_classe])) {
				for($loop=0;$loop<count($tab_pp[$current_id_classe]);$loop++) {
					$csv.=$current_classe.";".civ_nom_prenom($tab_pp[$current_id_classe][$loop]).";".get_mail_user($tab_pp[$current_id_classe][$loop]).";\r\n";
				}
			}
		}

		$nom_fic=remplace_accents($pp, "all").".csv";
		send_file_download_headers('text/x-csv',$nom_fic);
		echo echo_csv_encoded($csv);
		die();
	}
}

//**************** EN-TETE **************************************
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

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

		if(($_SESSION['statut']=='autre')&&(acces('/groupes/visu_profs_class.php', 'autre'))) {
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
		}

		$chaine_options_classes="";

		$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_class_tmp)>0){
			$id_class_prec=0;
			$id_class_suiv=0;
			$temoin_tmp=0;
			while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
				if($lig_class_tmp->id==$id_classe){
					$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
					$temoin_tmp=1;
					if($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
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

		$gepi_prof_suivi=getParamClasse($id_classe, 'gepi_prof_suivi', getSettingValue('gepi_prof_suivi'));

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

		echo "<h3>Equipe pédagogique de la classe de ".$classe["classe"]." <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;export=csv' class='noprint' title=\"Exporter l'équipe au format CSV (tableur)\" target='_blank'><img src='../images/icons/csv.png' class='icone16' alt='CSV' /></a>";
		echo "<span id='span_mail'></span>";
		echo "</h3>\n";

		echo "<script type='text/javascript' language='JavaScript'>
	var fen;
	function ouvre_popup(id_groupe,id_classe){
		eval(\"fen=window.open('popup.php?id_groupe=\"+id_groupe+\"&id_classe=\"+id_classe+\"','','width=400,height=400,menubar=yes,scrollbars=yes')\");
		setTimeout('fen.focus()',500);
	}

	function ouvre_popup2(id_groupe,id_classe, periode_num){
		eval(\"fen=window.open('popup.php?id_groupe=\"+id_groupe+\"&id_classe=\"+id_classe+\"&periode_num=\"+periode_num+\"','','width=400,height=400,menubar=yes,scrollbars=yes')\");
		setTimeout('fen.focus()',500);
	}
</script>\n";

		$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe'";
		$res_eleves_classe=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_eleves_classe=mysqli_num_rows($res_eleves_classe);

		if(count($tab_enseignements)==0) {
			echo "<p style='color:red'>Aucun enseignement.</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$acces_edit_group=acces("/groupes/edit_group.php", $_SESSION['statut']);

		//echo "<div style='float:right; width:45%'>";
		echo "<table class='boireaus boireaus_alt boireaus_white_hover' border='1' summary='Equipe'>
	<tr>
		<th rowspan='2'>Enseignement</th>
		<th colspan='".count($nom_periode)."'>Effectifs</th>
		<th rowspan='2'>Personnel</th>
	</tr>
	<tr>";
		for($loop=0;$loop<count($nom_periode);$loop++) {
			echo "
		<th>".$nom_periode[$loop+1]."</th>";
		}
		echo "
	</tr>";

		for($i=0;$i<count($tab_enseignements);$i++) {
			// Enseignements
			echo "
	<tr class='white_hover' onmouseover=\"this.style.backgroundColor='white'\" onmouseout=\"this.style.backgroundColor=''\">
	<!--tr-->
		<td>";
			// AJOUTER DES LIENS VERS L'ENSEIGNEMENT SI ON A LE DROIT
			if(($acces_edit_group)&&($tab_enseignements[$i]['id_groupe']!="")&&(is_numeric($tab_enseignements[$i]['id_groupe']))) {
				echo "<a href='edit_group.php?id_groupe=".$tab_enseignements[$i]['id_groupe']."' title=\"Editer cet enseignement.";
				if(isset($tab_enseignements[$i]['matiere_nom_complet'])) {
					echo "\nMatière : ".$tab_enseignements[$i]['matiere_nom_complet']."\">".htmlspecialchars($tab_enseignements[$i]['grp_name'])."<br /><span style='font-size: x-small;'>".htmlspecialchars($tab_enseignements[$i]['grp_description'])."</a>\n";
				}
				else {
					echo "\">";
					echo htmlspecialchars($tab_enseignements[$i]['grp_name']);
					echo "</a>";
				}
			}
			else {
				if(isset($tab_enseignements[$i]['matiere_nom_complet'])) {
					echo "<span title=\"Matière : ".$tab_enseignements[$i]['matiere_nom_complet']."\">".htmlspecialchars($tab_enseignements[$i]['grp_name'])."<br /><span style='font-size: x-small;'>".htmlspecialchars($tab_enseignements[$i]['grp_description'])."</span></span>\n";
				}
				else {
					echo htmlspecialchars($tab_enseignements[$i]['grp_name']);
				}
			}

			echo "</td>";

			// Effectifs
			for($loop=0;$loop<count($nom_periode);$loop++) {
				echo "
		<td>";

				echo "<a href='javascript:ouvre_popup2(\"".$tab_enseignements[$i]['id_groupe']."\",\"$id_classe\", \"".($loop+1)."\");' style='font-weight:bold' title=\"";
				if((isset($tab_enseignements[$i]['nb_class_grp']))&&($tab_enseignements[$i]['nb_class_grp']>1)) {
					echo "Dans ce groupe de ".$tab_enseignements[$i]['nb_tous_eleves_grp'][$loop+1]." élèves, ".$tab_enseignements[$i]['nb_eleves'][$loop+1]." élèves sont en ".$classe['classe'].".\n";
				}
				echo "Afficher un listing de l'enseignement\"> ".$tab_enseignements[$i]['nb_eleves'][$loop+1]." ";
				//if ($tab_enseignements[$i]['nb_eleves'][$loop+1] > 1) { echo $gepiSettings['denomination_eleves'];} else { echo $gepiSettings['denomination_eleve'];}
				echo " </a>";

				if((isset($tab_enseignements[$i]['nb_class_grp']))&&($tab_enseignements[$i]['nb_class_grp']>1)) {
					echo "<span style='font-size:x-small;'> sur <a href='javascript:ouvre_popup(\"".$tab_enseignements[$i]['id_groupe']."\",\"\");' title='Groupe de ".$tab_enseignements[$i]['nb_tous_eleves_grp'][$loop+1]." élèves'>".$tab_enseignements[$i]['nb_tous_eleves_grp'][$loop+1]."</a></span>";
				}

				echo "</td>";
			}

			// Professeurs
			echo "
		<td>";

			if(isset($tab_enseignements[$i]['prof'])) {
				for($loop=0;$loop<count($tab_enseignements[$i]['prof']);$loop++) {
					if($loop>0) {
						echo "
			<br />";
					}

					if(isset($tab_enseignements[$i]['prof'][$loop]['designation_prof_mailto'])) {
						echo $tab_enseignements[$i]['prof'][$loop]['designation_prof_mailto'];
					}
					else {
						echo $tab_enseignements[$i]['prof'][$loop]['designation_prof'];
					}

					if((isset($tab_enseignements[$i]['prof'][$loop]['is_pp']))&&($tab_enseignements[$i]['prof'][$loop]['is_pp']=="y")) {
						echo " (<i>".$gepi_prof_suivi."</i>)";
					}
				}
			}

			echo "</td>
	</tr>";
		}
		echo "
</table>\n";

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
			echo "<p>Envoyer un <a href='mailto:$chaine_mail?".rawurlencode("subject=".getSettingValue('gepiPrefixeSujetMail')."[GEPI] classe ".$classe['classe'])."'>mail à tous les membres de l'équipe</a>.</p>
<script type='text/javascript'>
	if(document.getElementById('span_mail')) {
		document.getElementById('span_mail').innerHTML=\" <a href='mailto:$chaine_mail?".rawurlencode("subject=".getSettingValue('gepiPrefixeSujetMail')."[GEPI] classe ".$classe['classe'])."' title='Envoyer un mail à tous les membres de l équipe'><img src='../images/icons/courrier_envoi.png' class='icone16' alt='Mail' /></a>\";
	}
</script>\n";

		}

		//echo "</div>";

		/*
		unset($tabmail);
		$tabmail=array();

		echo "<table class='boireaus' border='1' summary='Equipe'>\n";
		$alt=1;

		// Liste des CPE:
		$sql="SELECT DISTINCT u.nom,u.prenom,u.email,jec.cpe_login FROM utilisateurs u,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.e_login=jecl.login AND jecl.id_classe='$id_classe' AND u.login=jec.cpe_login ORDER BY jec.cpe_login";
		$result_cpe=mysql_query($sql);
		if(mysql_num_rows($result_cpe)>0){
			while($lig_cpe=mysql_fetch_object($result_cpe)){
				$alt=$alt*(-1);
				echo "<tr class='lig$alt white_hover' valign='top'><td>VIE SCOLAIRE</td>\n";

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

		echo "<tr><td colspan='3' class='infobulle_corps'>&nbsp;</td></tr>\n";
		//echo "<br />\n";

		//echo "<table border='0'>\n";
		//$sql="SELECT jgm.id_matiere,jgm.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgc.id_groupe=jgm.id_groupe AND jgc.id_classe='$id_classe' ORDER BY jgc.priorite, jgm.id_matiere";
		$sql="SELECT m.nom_complet,jgm.id_groupe, g.name, g.description FROM j_groupes_classes jgc, j_groupes_matieres jgm, matieres m, groupes g WHERE jgc.id_groupe=jgm.id_groupe AND m.matiere=jgm.id_matiere AND jgc.id_classe='$id_classe' AND g.id=jgc.id_groupe ORDER BY jgc.priorite, m.matiere";
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
			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover' valign='top'>\n";
			echo "<td>\n";
			//echo htmlspecialchars($lig_grp->nom_complet);
			echo "<span title=\"Matière : $lig_grp->nom_complet\">".htmlspecialchars($lig_grp->name)."<br /><span style='font-size: x-small;'>".htmlspecialchars($lig_grp->description)."</span></span>\n";
			echo "</td>\n";
			echo "<td>";

			if($nb_class_grp>1){
				// Effectif...
				// ... pour tout le groupe
				$sql="SELECT DISTINCT e.nom,e.prenom,c.classe FROM j_eleves_groupes jeg, eleves e, j_eleves_classes jec, j_groupes_classes jgc, classes c WHERE jeg.login=e.login AND jeg.id_groupe='$lig_grp->id_groupe' AND jgc.id_classe=c.id AND jgc.id_groupe=jeg.id_groupe AND jec.id_classe=c.id AND jec.login=e.login ORDER BY e.nom,e.prenom";
				$res_tous_eleves_grp=mysql_query($sql);
				$nb_tous_eleves_grp=mysql_num_rows($res_tous_eleves_grp);
			}

			echo "<a href='javascript:ouvre_popup(\"$lig_grp->id_groupe\",\"$id_classe\");'";
			if($nb_class_grp>1){
				echo " title=\"Dans ce groupe de $nb_tous_eleves_grp élèves, $nb_eleves élèves sont en ".$classe['classe']."\"";
			}
			echo ">".$nb_eleves." ";
			if ($nb_eleves > 1) { echo $gepiSettings['denomination_eleves'];} else { echo $gepiSettings['denomination_eleve'];}
			echo "</a>\n";

			if($nb_class_grp>1){
				echo " sur <a href='javascript:ouvre_popup(\"$lig_grp->id_groupe\",\"\");' title='Groupe de $nb_tous_eleves_grp élèves'>".$nb_tous_eleves_grp." ";
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
		*/
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

	if(($_SESSION['statut']=='autre')&&(acces('/groupes/visu_profs_class.php', 'autre'))) {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}

	$result_classes=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_classes = mysqli_num_rows($result_classes);
	//echo "<select name='id_classe' size='1'>\n";
	//echo "<option value='null'>-- Sélectionner la classe --</option>\n";
	/*
	for ($i=0;$i<$nb_classes;$i++) {
		$classe=old_mysql_result($query, $i, "classe");
		$id_classe=old_mysql_result($query, $i, "id");
		echo "<option value='$id_classe'>" . htmlspecialchars($classe) . "</option>\n";
	}
	*/
	$tab_classe=array();
	if(mysqli_num_rows($result_classes)==0){
		echo "<p>Il semble qu'aucune classe n'ait encore été créée...<br />... ou alors aucune classe ne vous a été attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else{
		$nb_classes=mysqli_num_rows($result_classes);
		$nb_class_par_colonne=round($nb_classes/3);
		echo "<table width='100%' summary='Choix de la classe'>\n";
		echo "<tr valign='top' align='center'>\n";
		$cpt=0;
		//echo "<td style='padding: 0 10px 0 10px'>\n";
		echo "<td>\n";
		while($lig_class=mysqli_fetch_object($result_classes)){
			if(($cpt>0)&&(round($cpt/$nb_class_par_colonne)==$cpt/$nb_class_par_colonne)){
				echo "</td>\n";
				//echo "<td style='padding: 0 10px 0 10px'>\n";
				echo "<td>\n";
			}
			//echo "<option value='$lig_class->id'>" . htmlspecialchars("$lig_class->classe") . "</option>\n";
			echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$lig_class->id".$ajout_href_2."'>".htmlspecialchars("$lig_class->classe") . "</a><br />\n";
			$tab_classe[$lig_class->id]=$lig_class->classe;
			$cpt++;
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}

	// Tableau des PP
	echo "<a name='liste_pp'></a>
<div align='center'>
	<table class='boireaus boireaus_alt'>
		<tr>
			<th>Classe</th>
			<th>
				<div style='float:right; width:16px'><a href='".$_SERVER['PHP_SELF']."?export_prof_suivi=y&amp;export=csv' class='noprint' title=\"Exporter l'équipe au format CSV (tableur)\" target='_blank'><img src='../images/icons/csv.png' class='icone16' alt='CSV' /></a></div>
				".ucfirst(getSettingValue('gepi_prof_suivi'))."
			</th>
		</tr>";
	$tab_pp=get_tab_prof_suivi();
	foreach($tab_classe as $current_id_classe => $current_classe) {
		echo "
		<tr>
			<td>$current_classe</td>
			<td>";
		if(isset($tab_pp[$current_id_classe])) {
			for($loop=0;$loop<count($tab_pp[$current_id_classe]);$loop++) {
				if($loop>0) {echo "<br />";}
				$designation_user=civ_nom_prenom($tab_pp[$current_id_classe][$loop]);
				echo "<div style='float:right; width:16px'>".affiche_lien_mailto_si_mail_valide($tab_pp[$current_id_classe][$loop], $designation_user)."</div>";
				echo $designation_user;
			}
		}
			echo "</td>
		</tr>";
	}
	echo "
	</table>
</div>";

}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
