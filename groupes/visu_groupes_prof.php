<?php
/*
*
*  Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$sql="SELECT 1=1 FROM droits WHERE id='/groupes/visu_groupes_prof.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/groupes/visu_groupes_prof.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='V',
autre='F',
description='Voir les enseignements du professeur',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
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

$login_prof=isset($_GET['login_prof']) ? $_GET["login_prof"] : (isset($_POST['login_prof']) ? $_POST["login_prof"] : NULL);

/*
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
		$sql="SELECT DISTINCT c.id,c.classe, c.suivi_par FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	if($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.id,c.classe, c.suivi_par FROM classes c,j_groupes_classes jgc,j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgc.id_groupe=jgp.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
	}
	if($_SESSION['statut']=='cpe'){
		$sql="SELECT DISTINCT c.id,c.classe, c.suivi_par FROM classes c,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe=c.id ORDER BY c.classe";
	}
	if($_SESSION['statut']=='administrateur'){
		$sql="SELECT DISTINCT c.id,c.classe, c.suivi_par FROM classes c ORDER BY c.classe";
	}

	if(($_SESSION['statut']=='scolarite')&&(getSettingValue("GepiAccesVisuToutesEquipScol") =="yes")){
		$sql="SELECT DISTINCT c.id,c.classe, c.suivi_par FROM classes c ORDER BY c.classe";
	}
	if(($_SESSION['statut']=='cpe')&&(getSettingValue("GepiAccesVisuToutesEquipCpe") =="yes")){
		$sql="SELECT DISTINCT c.id,c.classe, c.suivi_par FROM classes c ORDER BY c.classe";
	}
	if(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesVisuToutesEquipProf") =="yes")){
		$sql="SELECT DISTINCT c.id,c.classe, c.suivi_par FROM classes c ORDER BY c.classe";
	}

	if(($_SESSION['statut']=='autre')&&(acces('/groupes/visu_profs_class.php', 'autre'))) {
		$sql="SELECT DISTINCT c.id,c.classe, c.suivi_par FROM classes c ORDER BY c.classe";
	}

	$result_classes=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_classes = mysqli_num_rows($result_classes);
	$tab_classe=array();
	$tab_suivi_par=array();
	if(mysqli_num_rows($result_classes)==0){
		$msg="<p>Il semble qu'aucune classe n'ait encore été créée...<br />... ou alors aucune classe ne vous a été attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else {
		$nb_classes=mysqli_num_rows($result_classes);
		while($lig_class=mysqli_fetch_object($result_classes)){
			$tab_classe[$lig_class->id]=$lig_class->classe;
			$tab_suivi_par[$lig_class->id]=$lig_class->suivi_par;
		}

		$pp=ucfirst(getSettingValue('gepi_prof_suivi'));
		$csv="Classe;".$pp.";Mails;Classe suivie par;\r\n";

		$tab_pp=get_tab_prof_suivi();
		foreach($tab_classe as $current_id_classe => $current_classe) {
			if(isset($tab_pp[$current_id_classe])) {
				for($loop=0;$loop<count($tab_pp[$current_id_classe]);$loop++) {
					$csv.=$current_classe.";".civ_nom_prenom($tab_pp[$current_id_classe][$loop]).";".get_mail_user($tab_pp[$current_id_classe][$loop]).";".$tab_suivi_par[$current_id_classe].";\r\n";
				}
			}
		}

		$nom_fic=remplace_accents($pp, "all").".csv";
		send_file_download_headers('text/x-csv',$nom_fic);
		echo echo_csv_encoded($csv);
		die();
	}
}
*/

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

//		echo "<p><a href='".$_SERVER['PHP_SELF'].$ajout_href_1."'>Retour</a></p>\n";

// Liaison avec j_groupes_professeurs plutôt que utilisateurs.etat='actif' pour récupérer la liste des profs associés à des groupes, même s'ils ont été désactivés
$tab_profs=array();
$sql="SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom, u.etat FROM utilisateurs u, j_groupes_professeurs jgp WHERE u.login=jgp.login AND u.statut='professeur' ORDER BY u.nom, u.prenom;";
//echo "$sql<br />";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	while($lig=mysqli_fetch_assoc($res)) {
		$tab_profs[]=$lig;
	}
}

if(!isset($login_prof)) {
	echo "
<p class='bold'>
	<a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>";

	if(count($tab_profs)==0){
		echo "<p style='color:red'>Aucun professeur avec enseignement associé n'a été trouvé.</p>";
	}
	else {
		echo "<p style='text-indent:-3em;margin-left:3em;'>Pour quel professeur souhaitez-vous consulter la liste des enseignements&nbsp;:<br />";
		if($_SESSION['statut']=="professeur") {
			echo "<br /><a href='".$_SERVER['PHP_SELF']."?login_prof=".$_SESSION["login"]."'>Mes enseignements</a><br /><br />";
		}
		for($loop=0;$loop<count($tab_profs);$loop++) {
			//echo "<a href='".$_SERVER['PHP_SELF']."?login_prof=".$tab_profs[$loop]["login"]."'>".$tab_profs[$loop]["civilite"]." ".$tab_profs[$loop]["nom"]." ".$tab_profs[$loop]["prenom"]."</a><br />";
			echo "<a href='".$_SERVER['PHP_SELF']."?login_prof=".$tab_profs[$loop]["login"]."'>".casse_mot($tab_profs[$loop]["nom"], "maj")." ".casse_mot($tab_profs[$loop]["prenom"], "majf2")."</a><br />";
		}
		echo "</p>";
	}

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();
}

// Prof choisi

$prof_nom_prenom="";
echo "
<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>
	<p class='bold'>
		<a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
		 | 
		<select name='login_prof' onchange=\"document.forms[0].submit()\">";
	for($loop=0;$loop<count($tab_profs);$loop++) {
		if($tab_profs[$loop]["login"]==$login_prof) {
			$selected=" selected='true'";
			$prof_nom_prenom=casse_mot($tab_profs[$loop]["nom"], "maj")." ".casse_mot($tab_profs[$loop]["prenom"], "majf2");
		}
		else {
			$selected="";
		}
		echo "
			<option value='".$tab_profs[$loop]["login"]."'".$selected.">".casse_mot($tab_profs[$loop]["nom"], "maj")." ".casse_mot($tab_profs[$loop]["prenom"], "majf2")."</option>";
	}
	echo "
		</select>
	</p>
</form>";

$acces_modify_user=acces("/utilisateurs/modify_user.php", $_SESSION['statut']);
if($acces_modify_user) {
	$chaine_prof="<a href='../utilisateurs/modify_user.php?user_login=".$login_prof."' title=\"Modifier cet utilisateur\">".$prof_nom_prenom." <img src='../images/edit16.png' alt='Éditer' class='edit16'/></a>";
}
else {
	$chaine_prof=$prof_nom_prenom;
}
$chaine_pp=liste_classes_pp_prof_suivi($login_prof);
if($chaine_pp!="") {
	$chaine_prof.=" <em title=\"".getSettingValue("gepi_prof_suivi")." pour la ou les classes.\">(".$chaine_pp.")</em>";
}

$groups=get_groups_for_prof($login_prof,NULL,array('classes', 'matieres', 'periodes', 'eleves'));
if(count($groups)==0) {
	echo "
<p style='color:red'>Aucun enseignement n'a été trouvé pour ".$chaine_prof.".</p>";
}
else {
	$acces_edit_group=acces("/groupes/edit_group.php", $_SESSION['statut']);
	$acces_visu_profs_class=acces("/groupes/visu_profs_class.php", $_SESSION['statut']);
	$acces_groupes_edit_eleves=acces("/groupes/edit_eleves.php", $_SESSION['statut']);
	$acces_add_aid=acces("/aid/add_aid.php", $_SESSION['statut']);

	$acces_edt=false;
	if(($_SESSION['statut']=="administrateur")&&(getSettingAOui("autorise_edt_admin"))) {
		$acces_edt=true;
		$acces_edt_prof=true;
	}
	elseif(getSettingAOui("autorise_edt_tous")) {
		$acces_edt=true;

		$acces_edt_prof=true;
		if(($_SESSION['statut']=="professeur")&&(!getSettingAOui("AccesProf_EdtProfs"))) {
			$acces_edt_prof=false;
		}
	}

	// Afficher les enseignements avec des effectifs par périodes, la liste des classes, des liens EDT, des liens ABS2, la possibilité d'un lien publipostage?

	echo "
<p>Voici la liste des enseignements de ".$chaine_prof.($acces_edt_prof ? " <a href='../edt/index2.php?login_prof=$login_prof&affichage=semaine&type_affichage=prof' title=\"Voir l'emploi du temps de ce professeur\"><img src='../images/icons/edt2_prof.png' class='icone16' alt='EDT' /></a></td>" : "")."</p>
<table class='boireaus boireaus_alt sortable resizable'>
	<thead>
		<tr>
			<th>Enseignement</th>
			<th>Classes</th>
			<th>Effectif</th>
			".($acces_edt ? "<th>Emploi du temps</th>" : "")."
			".($acces_edit_group ? "<th title=\"Modifier l'enseignement\">Éditer</th>" : "")."
		</tr>
	</thead>
	<tbody>";

	foreach($groups as $current_group) {
		echo "
		<tr>
			<td>
				".$current_group['name']." (<em>".$current_group['description'];
		if((($current_group['name']!=$current_group['matiere']['matiere']))&&
		 (($current_group['description']!=$current_group['matiere']['nom_complet']))) {
			echo " (".$current_group['matiere']['matiere'].")";
		}
		echo "</em>)
			</td>
			<td>";
		if($acces_visu_profs_class) {
			$cpt=0;
			foreach($current_group["classes"]["classes"] as $id_classe => $current_classe) {
				if($cpt>0) {
					echo " - ";
				}
				echo "
				<a href='../groupes/visu_profs_class.php?id_classe=$id_classe' style='text-decoration:none;' title=\"Voir la liste des enseignements de la classe\">".$current_classe["classe"]."</a>";
				$cpt++;
			}
		}
		else {
			echo $current_group['classlist_string'];
		}
		echo "
			</td>
			<td>";
		if($acces_groupes_edit_eleves) {
			echo "<a href='../groupes/edit_eleves.php?id_groupe=".$current_group["id"]."' title=\"Modifier la liste des élèves inscrits.\">";
		}
		$cpt=0;
		foreach($current_group["periodes"] as $num_periode => $period) {
			if($cpt>0) {
				echo " - ";
			}
			echo "<span title=\"Effectif de l'enseignement en période $num_periode\">".count($current_group["eleves"][$num_periode]["list"])."</span>";
			$cpt++;
		}
		if($acces_groupes_edit_eleves) {
			echo "</a>";
		}
		echo "</td>";
		if($acces_edt) {
			echo "
			<td>";
			$cpt=0;
			foreach($current_group["classes"]["classes"] as $id_classe => $current_classe) {
				if($cpt>0) {
					echo " - ";
				}
				echo "
				<a href='../edt/index2.php?affichage=semaine&type_affichage=classe&id_classe=$id_classe' style='text-decoration:none;color:black;' title=\"Voir l'emploi du temps de cette classe\">".$current_classe["classe"]."<img src='../images/icons/edt2.png' class='icone16' alt='EDT' /></a>";
				$cpt++;
			}
			echo "
			</td>";
		}
		if($acces_edit_group) {
			echo "
			<td>
				<a href='../groupes/edit_group.php?id_groupe=".$current_group['id']."' title='Éditer cet enseignement' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/edit16.png' class='icone16' alt='Éditer cet enseignement' /></a>
			</td>";
		}
		echo "
		</tr>";
	}
	echo "
	</tbody>
</table>";

}

$tab_aid=get_tab_aid_prof($login_prof, "", "", "", array("classes"));
if(count($tab_aid)>0) {
	echo "<p>&nbsp;</p>\n";
	echo "<p style='text-indent:-3em;margin-left:3em;'>Le professeur est associé aux AID suivants&nbsp;:<br />";
	$k = 0;
	foreach($tab_aid as $current_aid) {
		echo $current_aid['nom_aid']." (<em>".$current_aid['nom_complet']."</em>) en ".$current_aid['classlist_string'];

		//echo " <span style='color:orange'>".NiveauGestionAid($login_prof,$current_aid['indice_aid'],$current_aid['id_aid'])."</span>";

		if($acces_add_aid) {
			if(NiveauGestionAid($_SESSION['login'],$current_aid['indice_aid'],$current_aid['id_aid'])>=5) {
				if ($current_aid['outils_complementaires']=="y") {
					echo " <a href='../aid/modif_fiches.php?action=modif&aid_id=".$current_aid['id_aid']."&indice_aid=".$current_aid['indice_aid']."' title='Éditer cet AID' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/edit16.png' class='icone16' alt='Éditer cet AID' /></a>";
				}
				else {
					echo " <a href='../aid/add_aid.php?action=modif_aid&aid_id=".$current_aid['id_aid']."&indice_aid=".$current_aid['indice_aid']."' title='Éditer cet AID' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/edit16.png' class='icone16' alt='Éditer cet AID' /></a>";
				}
			}
			elseif(NiveauGestionAid($_SESSION['login'],$current_aid['indice_aid'],$current_aid['id_aid'])>=1) {
				echo " <a href='../aid/modify_aid.php?flag=eleve&aid_id=".$current_aid['id_aid']."&indice_aid=".$current_aid['indice_aid']."' title='Gérer la liste des élèves' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/edit16.png' class='icone16' alt='Éditer cet AID' /></a>";
			}

			if($login_prof!=$_SESSION['login']) {
				if(NiveauGestionAid($login_prof,$current_aid['indice_aid'],$current_aid['id_aid'])>=5) {
					if ($current_aid['outils_complementaires']=="y") {
						echo " <img src='../images/bulle_verte.png' class='icone9' alt='Éditer' title='Peut éditer la fiche Projet de cet AID' />";
					}
					else {
						echo " <img src='../images/bulle_verte.png' class='icone9' alt='Éditer' title='Peut éditer cet AID'  />";
					}
				}
				elseif(NiveauGestionAid($login_prof,$current_aid['indice_aid'],$current_aid['id_aid'])>=1) {
					echo " <img src='../images/bulle_bleue.png' class='icone9' alt='Éditer' title='Peut gérer la liste des élèves de cet AID' />";
				}
			}
		}
		echo "<br />\n";
		$k++;
	}
	echo "</p>";
}

/*
// Export CSV à mettre au point
<div style='float:right; width:16px'><a href='".$_SERVER['PHP_SELF']."?export_prof_suivi=y&amp;export=csv' class='noprint' title=\"Exporter la liste des ".$gepi_prof_suivi." au format CSV (tableur)\" target='_blank'><img src='../images/icons/csv.png' class='icone16' alt='CSV' /></a></div>
*/

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
