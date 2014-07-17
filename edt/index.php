<?php
@set_time_limit(0);

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

// Pour le moment, seul l'admin a accès:
$sql="SELECT 1=1 FROM droits WHERE id='/edt/index.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/edt/index.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='F',
autre='F',
description='EDT ICS : Index',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

$sql="CREATE TABLE IF NOT EXISTS edt_ics (
id int(11) NOT NULL AUTO_INCREMENT,
id_classe INT(11) NOT NULL,
classe_ics varchar(100) NOT NULL DEFAULT '',
prof_ics varchar(200) NOT NULL DEFAULT '',
matiere_ics varchar(100) NOT NULL DEFAULT '',
salle_ics varchar(100) NOT NULL DEFAULT '',
jour_semaine varchar(10) NOT NULL DEFAULT '',
num_semaine varchar(10) NOT NULL DEFAULT '',
annee char(4) NOT NULL DEFAULT '',
date_debut DATETIME NOT NULL default '0000-00-00 00:00:00',
date_fin DATETIME NOT NULL default '0000-00-00 00:00:00',
description TEXT NOT NULL DEFAULT '',
PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
//echo "$sql<br />";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS edt_ics_prof (
id int(11) NOT NULL AUTO_INCREMENT,
login_prof varchar(100) NOT NULL DEFAULT '',
prof_ics varchar(200) NOT NULL DEFAULT '',
PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
//echo "$sql<br />";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS edt_ics_matiere (
id int(11) NOT NULL AUTO_INCREMENT,
matiere varchar(100) NOT NULL DEFAULT '',
matiere_ics varchar(100) NOT NULL DEFAULT '',
PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
//echo "$sql<br />";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

require("edt_ics_lib.php");

//================================================================
// Rapprochements
if((isset($_POST['rapprochements']))&&
(
	($_SESSION['statut']=="administrateur")||
	(getSettingAOui("accesUploadIcsEdt".$_SESSION['statut']))
)) {
	check_token();

	//debug_var();

	$prof_ics=isset($_POST['prof_ics']) ? $_POST['prof_ics'] : array();
	$prof_gepi=isset($_POST['prof_gepi']) ? $_POST['prof_gepi'] : array();

	$matiere_ics=isset($_POST['matiere_ics']) ? $_POST['matiere_ics'] : array();
	$matiere_gepi=isset($_POST['matiere_gepi']) ? $_POST['matiere_gepi'] : array();

	$nb_assoc_prof_suppr=0;
	$nb_assoc_prof_ajoutees=0;
	for($loop=0;$loop<count($prof_ics);$loop++) {
		if($prof_gepi[$loop]=="") {
			$sql="SELECT 1=1 FROM edt_ics_prof WHERE prof_ics='".$prof_ics[$loop]."';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				$sql="DELETE FROM edt_ics_prof WHERE prof_ics='".$prof_ics[$loop]."';";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_assoc_prof_suppr++;
			}
		}
		else {
			$sql="INSERT INTO edt_ics_prof SET prof_ics='".$prof_ics[$loop]."', login_prof='".$prof_gepi[$loop]."';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_assoc_prof_ajoutees++;
		}
	}

	$nb_assoc_matiere_suppr=0;
	$nb_assoc_matiere_ajoutees=0;
	for($loop=0;$loop<count($matiere_ics);$loop++) {
		if($matiere_gepi[$loop]=="") {
			$sql="SELECT 1=1 FROM edt_ics_matiere WHERE matiere_ics='".$matiere_ics[$loop]."';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				$sql="DELETE FROM edt_ics_matiere WHERE matiere_ics='".$matiere_ics[$loop]."';";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_assoc_matiere_suppr++;
			}
		}
		else {
			$sql="INSERT INTO edt_ics_matiere SET matiere_ics='".html_entity_decode($matiere_ics[$loop])."', matiere='".$matiere_gepi[$loop]."';";
			//echo "$sql<br />";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_assoc_matiere_ajoutees++;
		}
	}

	$msg="$nb_assoc_prof_ajoutees association(s) professeur(s) ajoutée(s).<br />";
	$msg.="$nb_assoc_prof_suppr association(s) professeur(s) supprimée(s).<br />";
	$msg.="$nb_assoc_matiere_ajoutees association(s) matière(s) ajoutée(s).<br />";
	$msg.="$nb_assoc_matiere_suppr association(s) matière(s) supprimée(s).<br />";
}

$type_edt=isset($_POST['type_edt']) ? $_POST['type_edt'] : (isset($_GET['type_edt']) ? $_GET['type_edt'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : "");
$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : (isset($_GET['login_prof']) ? $_GET['login_prof'] : "");
$num_semaine_annee=isset($_POST['num_semaine_annee']) ? $_POST['num_semaine_annee'] : (isset($_GET['num_semaine_annee']) ? $_GET['num_semaine_annee'] : NULL);

//================================================================
// Filtrage/contrôle de l'id_classe dans le cas élève/responsable
if($_SESSION['statut']=="eleve") {
	$type_edt="classe";
	$mode="afficher_edt";

	if($id_classe!="") {
		$sql="SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec WHERE jec.login='".$_SESSION['login']."' AND jec.id_classe='$id_classe';;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$id_classe="";
		}
	}

	if($id_classe=="") {
		$sql="SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec WHERE jec.login='".$_SESSION['login']."' ORDER BY periode DESC;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$id_classe=$lig->id_classe;
		}
		else {
			unset($mode);
		}
	}
}
elseif($_SESSION['statut']=="responsable") {
	$type_edt="classe";

	if($id_classe!="") {
		$sql="SELECT 1=1 FROM eleves e, j_eleves_classes jec, responsables2 r, resp_pers rp WHERE e.login=jec.login AND 
																	jec.id_classe='$id_classe' AND 
																	e.ele_id=r.ele_id AND 
																	r.pers_id=rp.pers_id AND 
																	rp.login='".$_SESSION['login']."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			// Il y a tentative d'accès à un autre EDT.
			$id_classe="";
		}
	}

	if($id_classe=="") {
		// Choisir l'élève et donc la classe
		$sql="(SELECT DISTINCT jec.id_classe FROM eleves e, j_eleves_classes jec, responsables2 r, resp_pers rp
				WHERE (e.ele_id=r.ele_id AND
						r.pers_id=rp.pers_id AND
						rp.login='".$_SESSION['login']."' AND
						(r.resp_legal='1' OR r.resp_legal='2') AND jec.login=e.login) ORDER BY jec.periode DESC)";
		if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
			$sql.=" UNION (SELECT DISTINCT jec.id_classe FROM eleves e, j_eleves_classes jec, responsables2 r, resp_pers rp
				WHERE (e.ele_id=r.ele_id AND
						r.pers_id=rp.pers_id AND
						rp.login='".$_SESSION['login']."' AND
						r.resp_legal='0' AND r.acces_sp='y' AND jec.login=e.login) ORDER BY jec.periode DESC)";
		}
		$sql.=";";
		$res_ele_clas=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele_clas)==1) {
			// S'il n'y a qu'une seule classe, faire la sélection directement
			$lig=mysqli_fetch_object($res_ele_clas);
			$id_classe=$lig->id_classe;
		}
		else {
			unset($mode);
		}
	}
}
//================================================================
// Affichage de l'EDT en infobulle
if((isset($_GET['mode']))&&($_GET['mode']=="afficher_edt_js")) {

	/*
	$type_edt=isset($_GET['type_edt']) ? $_GET['type_edt'] : NULL;
	$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : "";
	$login_prof=isset($_GET['login_prof']) ? $_GET['login_prof'] : "";
	$num_semaine_annee=isset($_GET['num_semaine_annee']) ? $_GET['num_semaine_annee'] : NULL;
	*/

	// A FAIRE : Contrôler les droits d'accès








	if((!isset($type_edt))||(!in_array($type_edt, array('classe', 'prof')))) {
		echo "<p style='color:red'>Type d'EDT non choisi.</p>";
		die();
	}
	elseif(($type_edt=="classe")&&(!isset($id_classe))) {
		echo "<p style='color:red'>Classe non choisie.</p>";
		die();
	}
	elseif(($type_edt=="prof")&&(!isset($login_prof))) {
		echo "<p style='color:red'>Professeur non choisi.</p>";
		die();
	}

	if(!isset($num_semaine_annee)) {
		// Récupérer la première semaine avec EDT pour le choix effectué
		if($type_edt=="classe") {
			$sql="SELECT num_semaine, annee FROM edt_ics WHERE id_classe='$id_classe' ORDER BY date_debut ASC LIMIT 1;";
		}
		else {
			$sql="SELECT num_semaine, annee FROM edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof='$login_prof' ORDER BY date_debut ASC LIMIT 1;";
		}
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$num_semaine_annee=$lig->num_semaine."|".$lig->annee;
		}
		else {
			echo "<p style='color:red'>Aucun enregistrement trouvé.</p>";
			die();
		}
	}

	$mode_infobulle="y";
	$html=affiche_edt_ics($num_semaine_annee, $type_edt, $id_classe, $login_prof);

	echo $html;

	die();
}

//**************** EN-TETE *****************
$titre_page = "EDT";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

if(!isset($mode)) {
	$sql="SELECT DISTINCT id, classe FROM classes c, periodes p WHERE c.id=p.id_classe ORDER BY classe";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tab_classe[$lig->id]=$lig->classe;
	}

	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2 class='gepi'>EDT d'après un fichier ICAL/ICS</h2>";

	if(($_SESSION['statut']=="administrateur")||
	(getSettingAOui("accesUploadIcsEdt".$_SESSION['statut']))) {
		echo "
<h3 class='gepi'>Envoi de fichiers emploi du temps au format ICAL/ICS</h3>
<div style='margin-left:3em;'>
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p>Veuillez choisir la classe et fournir le fichier ICS/ICAL&nbsp;:<br />
		Classe&nbsp;: <select name='id_classe'>";

		foreach($tab_classe as $id_classe => $classe) {
			$sql="SELECT 1=1 FROM edt_ics WHERE id_classe='$id_classe';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				$commentaire="       - (un emploi du temps est déjà saisi/importé)";
			}
			else {
				$commentaire="";
			}
			echo "
				<option value='$id_classe'>".$classe.$commentaire."</option>";
		}
		echo "
		</select><br />
		<input type=\"file\" size=\"65\" name=\"fich_ics_file\" id='input_ics_file' class='fieldset_opacite50' /><br />
		<input type='hidden' name='mode' value='upload' />
		<input type='hidden' name='is_posted' value='yes' />
		<p><input type='submit' id='input_submit' value='Valider' />
		<input type='button' id='input_button' value='Valider' style='display:none;' onclick=\"check_champ_file()\" /></p>
	</fieldset>

	<script type='text/javascript'>
		document.getElementById('input_submit').style.display='none';
		document.getElementById('input_button').style.display='';

		function check_champ_file() {
			fichier=document.getElementById('input_ics_file').value;
			//alert(fichier);
			if(fichier=='') {
				alert('Vous n\'avez pas sélectionné de fichier ICS à envoyer.');
			}
			else {
				document.getElementById('form_envoi').submit();
			}
		}
	</script>
</form>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTES&nbsp;:</em> Pour le moment, il faut renommer le fichier à envoyer avec l'extension TXT.<br />
L'extension ICS ne fait en effet pas actuellement partie des extensions autorisées pour les fichiers uploadés dans Gepi.<br />
A voir: faut-il vider tout ce qui concerne la classe dont on importe l'ICS?<br />
Est-ce qu'EDT propose des exports pour telle semaine seulement?</p>

<pre style='color:red'>A FAIRE :
- Gérer des droits: consultation, upload,... selon les statuts.
- Problème ACCPE: Cas de 2CO: 11 cours sur un créneau,
  c'est illisible.
- Pouvoir générer un EDT de salle,
  un EDT de matière (?) probablement difficile à lire sur un gros établissement
</pre>

</div>";

		//echo "<br />";
	}

	echo "
<h3 class='gepi'>Consultation EDT</h3>
<div style='margin-left:3em;'>";
	//=================================================
	// Formulaire d'affichage de l'EDT pour les classes avec EDT renseigné

	$sql="SELECT DISTINCT c.id, c.classe FROM classes c, periodes p, edt_ics ei WHERE c.id=p.id_classe AND ei.id_classe=p.id_classe ORDER BY classe";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_choix_classe' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."

		<p>Afficher l'EDT de la classe 
			<select name='id_classe'>";
		while($lig=mysqli_fetch_object($res)) {
			$selected="";
			if((isset($_SESSION['edt_ics_id_classe']))&&($_SESSION['edt_ics_id_classe']==$lig->id)) {
				$selected=" selected='selected'";
			}

			echo "
				<option value='".$lig->id."'$selected>".$lig->classe."</option>";
		}
		echo "
			</select> 
			en semaine 
			<select name='num_semaine_annee'>";
		$sql="SELECT DISTINCT num_semaine, annee FROM edt_ics WHERE num_semaine!='' ORDER BY date_debut;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				$jours=get_days_from_week_number($lig->num_semaine, $lig->annee);

				$selected="";
				if((isset($_SESSION['edt_ics_num_semaine']))&&(isset($_SESSION['edt_ics_annee']))&&
				($_SESSION['edt_ics_num_semaine']==$lig->num_semaine)&&
				($_SESSION['edt_ics_annee']==$lig->annee)) {
					$selected=" selected='selected'";
				}

				echo "
					<option value='".$lig->num_semaine."|".$lig->annee."'$selected>semaine ".$lig->num_semaine." (".$jours['num_jour'][1]['jjmmaaaa']." - ".$jours['num_jour'][7]['jjmmaaaa'].")</option>";
			}
		}
		echo "
			</select> 
			<input type='hidden' name='type_edt' value='classe' />
			<input type='hidden' name='mode' value='afficher_edt' />
			<input type='submit' id='input_submit' value='Valider' />
		</p>

	</fieldset>
</form>";
	}

	//=================================================
	echo "<br />";

	// Formulaire d'affichage de l'EDT pour les profs avec EDT renseigné

	$sql="SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof=u.login ORDER BY u.nom, u.prenom;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_choix_prof' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."

		<p>Afficher l'EDT de 
			<select name='login_prof'>";
		while($lig=mysqli_fetch_object($res)) {
			$selected="";
			if((isset($_SESSION['edt_ics_login_prof']))&&($_SESSION['edt_ics_login_prof']==$lig->login)) {
				$selected=" selected='selected'";
			}

			echo "
				<option value='".$lig->login."'$selected>".casse_mot($lig->civilite, "majf")." ".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, "majf2")."</option>";
		}
		echo "
			</select> 
			en semaine 
			<select name='num_semaine_annee'>";
		$sql="SELECT DISTINCT num_semaine, annee FROM edt_ics WHERE num_semaine!='' ORDER BY date_debut;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				$jours=get_days_from_week_number($lig->num_semaine, $lig->annee);

				$selected="";
				if((isset($_SESSION['edt_ics_num_semaine']))&&(isset($_SESSION['edt_ics_annee']))&&
				($_SESSION['edt_ics_num_semaine']==$lig->num_semaine)&&
				($_SESSION['edt_ics_annee']==$lig->annee)) {
					$selected=" selected='selected'";
				}

				echo "
					<option value='".$lig->num_semaine."|".$lig->annee."'$selected>semaine ".$lig->num_semaine." (".$jours['num_jour'][1]['jjmmaaaa']." - ".$jours['num_jour'][7]['jjmmaaaa'].")</option>";
			}
		}
		echo "
			</select> 
			<input type='hidden' name='type_edt' value='prof' />
			<input type='hidden' name='mode' value='afficher_edt' />
			<input type='submit' id='input_submit' value='Valider' />
		</p>

	</fieldset>
</form>";

	}
	echo "
</div>";

}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//                             TRAITEMENT DU FICHIER ICAL/ICS UPLOADé
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
elseif(($mode=="upload")&&
(
	($_SESSION['statut']=="administrateur")||
	(getSettingAOui("accesUploadIcsEdt".$_SESSION['statut']))
)) {
	check_token(false);

	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='".$_SERVER['PHP_SELF']."'>Importer un autre fichier ICS/ICAL</a></p>

<center><h3 class='gepi'>Test EDT</h3></center>";

	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
	}

	$post_max_size=ini_get('post_max_size');
	$upload_max_filesize=ini_get('upload_max_filesize');
	$max_execution_time=ini_get('max_execution_time');
	$memory_limit=ini_get('memory_limit');

	$ics_file = isset($_FILES["fich_ics_file"]) ? $_FILES["fich_ics_file"] : NULL;

	if(!is_uploaded_file($ics_file['tmp_name'])) {
		echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

		echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
		echo "post_max_size=$post_max_size<br />\n";
		echo "upload_max_filesize=$upload_max_filesize<br />\n";
		echo "</p>\n";

		require("../lib/footer.inc.php");
		die();
	}
	else {
		//echo "\$ics_file['tmp_name']=".$ics_file['tmp_name']."<br />";

		if(!file_exists($ics_file['tmp_name'])){
			echo "<p style='color:red;'>Le fichier aurait été uploadé... mais ne serait pas présent/conservé.</p>\n";

			echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
			echo "post_max_size=$post_max_size<br />\n";
			echo "upload_max_filesize=$upload_max_filesize<br />\n";
			echo "et le volume de ".$ics_file['name']." serait<br />\n";
			echo "\$ics_file['size']=".volume_human($ics_file['size'])."<br />\n";
			echo "</p>\n";

			require("../lib/footer.inc.php");
			die();
		}

		echo "<p>Le fichier a été uploadé.</p>\n";

		$source_file=$ics_file['tmp_name'];
		$dest_file="../temp/".$tempdir."/fichier.ics";
		$res_copy=copy("$source_file" , "$dest_file");
		if(!$res_copy){
			echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		//https://code.google.com/p/ics-parser/
		//https://code.google.com/p/ics-parser/source/browse/?r=8#svn%2Ftrunk
		//https://ics-parser.googlecode.com/svn-history/r8/trunk/example.php

		require("../lib/class.iCalReader.php");

		// On vide les enregistrements précédents pour la classe.
		$sql="DELETE FROM edt_ics WHERE id_classe='".$id_classe."';";
		//echo "$sql<br />";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);

		$tab_classe=array();

		$tab_nom_jour[1]="lundi";
		$tab_nom_jour[2]="mardi";
		$tab_nom_jour[3]="mercredi";
		$tab_nom_jour[4]="jeudi";
		$tab_nom_jour[5]="vendredi";
		$tab_nom_jour[6]="samedi";
		$tab_nom_jour[7]="dimanche";

		// Ce décalage n'est valable que sur une partie de l'année... on extrait autrement le décalage.
		$decalage_horaire=2*3600;

		function get_dernier_dimanche_du_mois($mois, $annee) {
			// Fonction utilisée pour les mois de mars et octobre (31 jours)
			for($i=31;$i>1;$i--) {
				$ts=mktime(0, 0, 0, $mois , $i, $annee);
				if(strftime("%u", $ts)==7) {
					break;
				}
			}
			return $i;
		}

		$nb_reg=0;
		$ical2 = new ICal($dest_file);
		echo "<p>".$ical2->event_count." cours (ou enregistrements) dans ce fichier.</p>";
		if($debug_edt=="y") {
			echo '<pre>';
		}
		foreach( $ical2->events() as $event ) {
			if($debug_edt=="y") {
				echo "<hr />";
				print_r($event);
			}

			$tmp_ts_debut=$ical2->iCalDateToUnixTimestamp($event['DTSTART']);
			$annee_courante=strftime("%Y", $tmp_ts_debut);
			$mois_courant=strftime("%m", $tmp_ts_debut);
			$jour_courant=strftime("%d", $tmp_ts_debut);
			if(($mois_courant>10)||($mois_courant<3)) {
				$decalage_horaire=1*3600;
			}
			elseif(($mois_courant>3)&&($mois_courant<10)) {
				$decalage_horaire=2*3600;
			}
			elseif($mois_courant==3) {
				if(!isset($num_dernier_dimanche[$annee_courante][$mois_courant])) {
					$num_dernier_dimanche[$annee_courante][$mois_courant]=get_dernier_dimanche_du_mois($mois_courant, $annee_courante);
				}
				if($jour_courant>=$num_dernier_dimanche[$annee_courante][$mois_courant]) {
					$decalage_horaire=2*3600;
				}
				else {
					$decalage_horaire=1*3600;
				}
			}
			elseif($mois_courant==10) {
				if(!isset($num_dernier_dimanche[$annee_courante][$mois_courant])) {
					$num_dernier_dimanche[$annee_courante][$mois_courant]=get_dernier_dimanche_du_mois($mois_courant, $annee_courante);
				}
				if($jour_courant>=$num_dernier_dimanche[$annee_courante][$mois_courant]) {
					$decalage_horaire=1*3600;
				}
				else {
					$decalage_horaire=2*3600;
				}
			}

			$ts_debut=$ical2->iCalDateToUnixTimestamp($event['DTSTART'])+$decalage_horaire;
			$ts_fin=$ical2->iCalDateToUnixTimestamp($event['DTEND'])+$decalage_horaire;
			if($debug_edt=="y") {
				echo "<p><span style='color:red'>Du ".strftime("%a %d/%m/%Y %H:%M:%S", $ts_debut)." au ".strftime("%a %d/%m/%Y %H:%M:%S", $ts_fin)."</span></p>";
			}

			if(isset($event['DESCRIPTION;LANGUAGE=fr'])) {
				$date_debut=strftime("%Y-%m-%d %H:%M:%S", $ts_debut);
				$date_fin=strftime("%Y-%m-%d %H:%M:%S", $ts_fin);
				$tab=explode("\\n", $event['DESCRIPTION;LANGUAGE=fr']);
				if($debug_edt=="y") {
					print_r($tab);
				}
				//$matiere_ics=preg_replace("//", "",$event['DESCRIPTION']);
				$matiere_ics="";
				$prof_ics="";
				$classe_ics="";
				$salle_ics="";
				for($loop=0;$loop<count($tab);$loop++) {
					if(preg_match("/^ *Matière : /", $tab[$loop])) {
						$matiere_ics=preg_replace("/^ *Matière : /", "", $tab[$loop]);
					}
					elseif(preg_match("/^ *Professeur : /", $tab[$loop])) {
						$prof_ics=preg_replace("/^ *Professeur : /", "", $tab[$loop]);
					}
					elseif(preg_match("/^ *Classe : /", $tab[$loop])) {
						$classe_ics=preg_replace("/^ *Classe : /", "", $tab[$loop]);
					}
					elseif(preg_match("/^ *Salle : /", $tab[$loop])) {
						$salle_ics=preg_replace("/^ *Salle : /", "", $tab[$loop]);
					}
				}

				if($debug_edt=="y") {
					echo "<p style='color:green;'>";
					echo "Matière : $matiere_ics<br />";
					echo "Prof : $prof_ics<br />";
					echo "Classe : $classe_ics<br />";
					echo "Salle : $salle_ics<br />";
					echo "</p>";
				}

				if(($classe_ics!="")&&(!in_array($classe_ics, $tab_classe))) {
					$tab_classe[]=$classe_ics;
				}

				// Certains enregistrements ont un classe_ics vide. Les groupes?
				$sql="INSERT INTO edt_ics SET id_classe='$id_classe',
									classe_ics='$classe_ics',
									prof_ics='$prof_ics',
									matiere_ics='$matiere_ics',
									salle_ics='$salle_ics',
									jour_semaine='".$tab_nom_jour[strftime("%u", $ts_debut)]."',
									num_semaine='".strftime("%V", $ts_debut)."',
									annee='".strftime("%Y", $ts_debut)."',
									date_debut='$date_debut',
									date_fin='$date_fin',
									description='".$event['DESCRIPTION;LANGUAGE=fr']."';";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_reg++;
			}
		}
		if($debug_edt=="y") {
			echo '</pre>';
		}

		echo "<p>$nb_reg enregistrement(s) effectué(s).</p>";

		echo "<p class='bold'>Rapprochements&nbsp;:</p>

<p>Pour obtenir un EDT plus lisible, ne pas afficher les prénoms des professeur,... il est recommandé de procéder aux rapprochements ci-dessous (<em>rapprochements entre le contenu du fichier ICAL/ICS et les matières et professeurs déclarés dans Gepi</em>).<br />
Les rapprochements sont également utilisés dans les champs de sélection des professeurs dont ont souhaite afficher l'EDT.</p>

<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi' method='post'>
	<fieldset class='fieldset_opacite50'>
	".add_token_field();

		$sql="SELECT DISTINCT prof_ics FROM edt_ics WHERE prof_ics NOT IN (SELECT prof_ics FROM edt_ics_prof);";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p>Tous les professeurs du fichier ICS fourni sont associés à un professeur dans Gepi.</p>";
		}
		else {
			echo "<p>".mysqli_num_rows($res)." professeur(s) du fichier ICS fourni n'est(ne sont) pas associé(s) à un professeur dans Gepi.<br />
Vous pouvez effectuer le ou les rapprochements ci-dessous&nbsp;:</p>";

			$sql="SELECT * FROM utilisateurs u WHERE statut='professeur' ORDER BY nom, prenom;";
			$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_prof)==0) {
				echo "<p>Aucun professeur n'a été trouvé dans Gepi.<br />Il faut initialiser l'année d'abord.</p>";
				require("../lib/footer.inc.php");
				die();
			}

			//$tab_prof=array();
			//$cpt=0;
			$lignes_profs="";
			while($lig_prof=mysqli_fetch_object($res_prof)) {
				$lignes_profs.="<option value='".$lig_prof->login."'";
				if($lig_prof->etat=='inactif') {
					$lignes_profs.=" style='color:grey' title=\"Compte utilisateur inactif.\"";
				}
				$lignes_profs.=">".$lig_prof->civilite." ".$lig_prof->nom." ".$lig_prof->prenom."</option>";

				//$tab_prof[$cpt]="";
				//$cpt++;
			}

			echo "<table class='boireaus boireaus_alt'>";
			$cpt=0;
			while($lig=mysqli_fetch_object($res)) {
				echo "
	<tr>
		<td>
			<input type='hidden' name='prof_ics[".$cpt."]' value=\"".$lig->prof_ics."\" />
			".$lig->prof_ics."&nbsp;: 
		</td>
		<td>
			<select name='prof_gepi[".$cpt."]'>
				<option value=''>---</option>
				".$lignes_profs."
			</select>
		</td>
	</tr>";
						$cpt++;
				}
				echo "
</table>";

		}

		echo "<br />";


		$sql="SELECT DISTINCT matiere_ics FROM edt_ics WHERE matiere_ics NOT IN (SELECT matiere_ics FROM edt_ics_matiere);";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p>Toutes les matières du fichier ICS fourni sont associés à une matière dans Gepi.</p>";
		}
		else {
			echo "<p>".mysqli_num_rows($res)." matière(s) du fichier ICS fourni n'est(ne sont) pas associé(s) à une matière dans Gepi.<br />
Vous pouvez effectuer le ou les rapprochements ci-dessous&nbsp;:</p>";

			$sql="SELECT * FROM matieres ORDER BY matiere;";
			$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_mat)==0) {
				echo "<p>Aucune matière n'a été trouvée dans Gepi.<br />Il faut initialiser l'année d'abord.</p>";
				require("../lib/footer.inc.php");
				die();
			}

			//$tab_mat=array();
			//$cpt=0;
			$lignes_mat="";
			while($lig_mat=mysqli_fetch_object($res_mat)) {
				$lignes_mat.="<option value='".$lig_mat->matiere."'";
				// Ajouter un test sur le nombre de groupes associés
				//	$lignes_mat.=" style='color:grey' title=\"Compte utilisateur inactif.\"";
				$lignes_mat.=">".$lig_mat->nom_complet."</option>";

				//$tab_prof[$cpt]="";
				//$cpt++;
			}

			echo "<table class='boireaus boireaus_alt'>";
			$cpt=0;
			while($lig=mysqli_fetch_object($res)) {
				echo "
	<tr>
		<td>
			<input type='hidden' name='matiere_ics[".$cpt."]' value=\"".html_entity_decode($lig->matiere_ics)."\" />
			".$lig->matiere_ics."&nbsp;: 
		</td>
		<td>
			<select name='matiere_gepi[".$cpt."]'>
				<option value=''>---</option>
				".$lignes_mat."
			</select>
		</td>
	</tr>";
					$cpt++;
				}
				echo "
</table>";

		}




		echo "
		<input type='hidden' name='rapprochements' value=\"y\" />
		<p><input type='submit' value='Valider' /></p>
	</fieldset>
</form>";

	}
}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//                                       AFFICHAGE EDT
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
elseif($mode=="afficher_edt") {

	/*
	$type_edt=isset($_POST['type_edt']) ? $_POST['type_edt'] : (isset($_GET['type_edt']) ? $_GET['type_edt'] : NULL);
	//$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
	//$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : (isset($_GET['login_prof']) ? $_GET['login_prof'] : NULL);
	$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : "");
	$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : (isset($_GET['login_prof']) ? $_GET['login_prof'] : "");
	$num_semaine_annee=isset($_POST['num_semaine_annee']) ? $_POST['num_semaine_annee'] : (isset($_GET['num_semaine_annee']) ? $_GET['num_semaine_annee'] : NULL);
	*/

	if($_SESSION['statut']=="eleve") {
		$type_edt="classe";

		$sql="SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='".$_SESSION['login']."' ORDER BY periode DESC, classe;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
			echo "<p style='color:red'>Vous semblez n'être inscrit(e) dans aucune classe.</p>";
			require("../lib/footer.inc.php");
			die();
		}
		$lig=mysqli_fetch_object($res);
		$id_classe=$lig->id_classe;
	}
	elseif($_SESSION['statut']=="responsable") {
		$type_edt="classe";

		if($id_classe!="") {
			$sql="SELECT 1=1 FROM eleves e, j_eleves_classes jec, responsables2 r, resp_pers rp WHERE e.login=jec.login AND 
																		jec.id_classe='$id_classe' AND 
																		e.ele_id=r.ele_id AND 
																		r.pers_id=rp.pers_id AND 
																		rp.login='".$_SESSION['login']."';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				// Il y a tentative d'accès à un autre EDT.
				$id_classe="";
			}
		}

		if($id_classe=="") {

			// Choisir l'élève et donc la classe
			$sql="(SELECT DISTINCT jec.id_classe FROM eleves e, j_eleves_classes jec, responsables2 r, resp_pers rp
					WHERE (e.ele_id=r.ele_id AND
							r.pers_id=rp.pers_id AND
							rp.login='".$_SESSION['login']."' AND
							(r.resp_legal='1' OR r.resp_legal='2') AND jec.login=e.login) ORDER BY jec.periode DESC)";
			if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
				$sql.=" UNION (SELECT DISTINCT jec.id_classe FROM eleves e, j_eleves_classes jec, responsables2 r, resp_pers rp
					WHERE (e.ele_id=r.ele_id AND
							r.pers_id=rp.pers_id AND
							rp.login='".$_SESSION['login']."' AND
							r.resp_legal='0' AND r.acces_sp='y' AND jec.login=e.login) ORDER BY jec.periode DESC)";
			}
			$sql.=";";
			$res_ele_clas=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_ele_clas)==0) {
				echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
				echo "<p style='color:red'>Vous n'êtes responsable d'aucun élève???</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			elseif(mysqli_num_rows($res_ele_clas)==1) {
				// S'il n'y a qu'une seule classe, faire la sélection directement
				$lig=mysqli_fetch_object($res_ele_clas);
				$id_classe=$lig->id_classe;
			}
			else {
				echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

				echo "<p>De quel élève/classe souhaitez-vous afficher l'emploi du temps&nbsp;?</p>";

				$sql="(SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec, responsables2 r, resp_pers rp
						WHERE (e.ele_id=r.ele_id AND
								r.pers_id=rp.pers_id AND
								rp.login='".$_SESSION['login']."' AND
								(r.resp_legal='1' OR r.resp_legal='2') AND jec.login=e.login) ORDER BY e.naissance)";
				if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
					$sql.=" UNION (SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec, responsables2 r, resp_pers rp
						WHERE (e.ele_id=r.ele_id AND
								r.pers_id=rp.pers_id AND
								rp.login='".$_SESSION['login']."' AND
								r.resp_legal='0' AND r.acces_sp='y' AND jec.login=e.login) ORDER BY e.naissance)";
				}
				$sql.=";";
				$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_ele)==0) {
					// On ne devrait pas passer par là avec les tests qui précèdent
					echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
					echo "<p style='color:red'>Vous n'êtes responsable d'aucun élève???</p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				while($lig=mysqli_fetch_object($res_ele)) {
					echo "<strong>".$lig->prenom." ".$lig->nom."&nbsp;:</strong> ";
					$sql="SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='$lig->login' ORDER BY periode,classe;";
					$res_class=mysqli_query($mysqli, $sql);
					$a = 0;
					$tab_classe=array();
					if($res_class->num_rows > 0) {
						$cpt_clas=0;
						while($lig_clas=mysqli_fetch_object($res_class)) {
							if($cpt_clas>0) {
								echo ", ";
							}
							echo "<a href='".$_SERVER['PHP_SELF']."?mode=afficher_edt&amp;type_edt=classe&amp;id_classe=$lig_clas->id_classe'>".$lig_clas->classe."</a>";
							$cpt_clas++;
						}
					}
					echo "<br />";
				}

				require("../lib/footer.inc.php");
				die();
			}
		}


		// A FAIRE ENCORE : Adapter les liens semaine précédente/suivante... ou juste se contenter des tests dans l'affichage infobulle
	}

	if((!isset($type_edt))||(!in_array($type_edt, array('classe', 'prof')))) {
		echo "<p class='bold'><a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
		echo "<p style='color:red'>Type d'EDT non choisi.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	elseif(($type_edt=="classe")&&(!isset($id_classe))) {
		echo "<p class='bold'><a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
		echo "<p style='color:red'>Classe non choisie.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	elseif(($type_edt=="prof")&&(!isset($login_prof))) {
		echo "<p class='bold'><a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
		echo "<p style='color:red'>Professeur non choisi.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	if(!isset($num_semaine_annee)) {
		// Récupérer la première semaine avec EDT pour le choix effectué
		if($type_edt=="classe") {
			$sql="SELECT num_semaine, annee FROM edt_ics WHERE id_classe='$id_classe' ORDER BY date_debut ASC LIMIT 1;";
		}
		else {
			$sql="SELECT num_semaine, annee FROM edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof='$login_prof' ORDER BY date_debut ASC LIMIT 1;";
		}
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$num_semaine_annee=$lig->num_semaine."|".$lig->annee;
		}
		else {
		echo "<p class='bold'><a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
			echo "<p style='color:red'>Aucun enregistrement trouvé.</p>";
			require("../lib/footer.inc.php");
			die();
		}
	}

	echo "<div style='float:left;width:35em; font-weight:bold'>
	<form action='".$_SERVER['PHP_SELF']."' id='form_choix_autre_semaine' method='post'>
		<a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link' title=\"Retour au menu EDT\"/> Retour</a>";

	if($type_edt=="classe") {
		$sql="SELECT DISTINCT num_semaine, annee FROM edt_ics WHERE id_classe='$id_classe' ORDER BY date_debut ASC;";
	}
	else {
		$sql="SELECT DISTINCT num_semaine, annee FROM edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof='$login_prof' ORDER BY date_debut ASC;";
	}
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "
		 | Autre semaine&nbsp;:<select name='num_semaine_annee' onchange=\"document.getElementById('form_choix_autre_semaine').submit()\" width='100'>";
		while($lig=mysqli_fetch_object($res)) {
			$jours=get_days_from_week_number($lig->num_semaine, $lig->annee);

			echo "
			<option value='".$lig->num_semaine."|".$lig->annee."' ".(($num_semaine_annee==$lig->num_semaine."|".$lig->annee) ? " selected='selected'" : "").">semaine ".$lig->num_semaine." (".$jours['num_jour'][1]['jjmmaaaa']." - ".$jours['num_jour'][7]['jjmmaaaa'].")</option>";
		}
		echo "
		</select>
		<input type='hidden' name='mode' value='afficher_edt' />
		<input type='hidden' name='type_edt' value='$type_edt' />";
		if($type_edt=="prof") {
			echo "
		<input type='hidden' name='login_prof' value='$login_prof' />";
		}
		else {
			echo "
		<input type='hidden' name='id_classe' value='$id_classe' />";
		}
	}

	echo "
	</form>
</div>";

	// A REVOIR: FAIRE UNE FONCTION DE TEST SUR CE QUI EST ACCESSIBLE SELON LES STATUTS ET DROITS
	if(($_SESSION['statut']!="eleve")&&($_SESSION['statut']!="responsable")) {
		// Choisir un EDT de classe:
		$sql="SELECT DISTINCT c.id, c.classe FROM classes c, periodes p, edt_ics ei WHERE c.id=p.id_classe AND ei.id_classe=p.id_classe ORDER BY classe";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			echo "
	<div style='float:left;width:8em; font-weight:bold'>
		<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_choix_classe' method='post'>
			".add_token_field()."

			 | <select name='id_classe' onchange=\"document.getElementById('form_choix_classe').submit()\" title=\"Afficher une classe (même semaine)\" style='width:8em;'>
				<option value=''>Choix de classe</option>";
		while($lig=mysqli_fetch_object($res)) {
			$selected="";
			if(($type_edt=="classe")&&($id_classe==$lig->id)) {
				$selected=" selected='selected'";
			}

			echo "
			<option value='".$lig->id."'$selected>".$lig->classe."</option>";
		}
		echo "
			</select>
			<input type='hidden' name='num_semaine_annee' value='$num_semaine_annee' />
			<input type='hidden' name='type_edt' value='classe' />
			<input type='hidden' name='mode' value='afficher_edt' />
			<input type='submit' id='input_submit_classe' value='Valider' title=\"Valider le choix de classe\" />

			<script type='text/javascript'>
				document.getElementById('input_submit_classe').style.display='none';
			</script>
		</form>
	</div>";
		}

		// Choisir un EDT de prof:
		$sql="SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof=u.login ORDER BY u.nom, u.prenom;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			echo "
	<div style='float:left;width:10em; font-weight:bold'>
		<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_choix_prof' method='post'>
			".add_token_field()."

			 | <select name='login_prof' onchange=\"document.getElementById('form_choix_prof').submit()\" title=\"Afficher l'EDT d'un professeur (même semaine)\" style='width:10em;'>
				<option value=''>Choix du prof</option>";
			while($lig=mysqli_fetch_object($res)) {
				$selected="";
				if(($type_edt=="prof")&&($login_prof==$lig->login)) {
					$selected=" selected='selected'";
				}

				echo "
				<option value='".$lig->login."'$selected>".casse_mot($lig->civilite, "majf")." ".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, "majf2")."</option>";
			}
			echo "
			</select>
			<input type='hidden' name='num_semaine_annee' value='$num_semaine_annee' />
			<input type='hidden' name='type_edt' value='prof' />
			<input type='hidden' name='mode' value='afficher_edt' />
			<input type='submit' id='input_submit_prof' value='Valider' title=\"Valider le choix du prof\" />

			<script type='text/javascript'>
				document.getElementById('input_submit_prof').style.display='none';
			</script>
		</form>
	</div>";
		}
	}
	// ================================================
	//    FIN DE LA LIGNE DE CHOIX SOUS L'ENTETE
	// ================================================

	echo "
<div style='clear:both'></div>";

	$tab=explode("|", $num_semaine_annee);

	$num_semaine=$tab[0];
	$annee=$tab[1];

	$_SESSION['edt_ics_num_semaine']=$num_semaine;
	$_SESSION['edt_ics_annee']=$annee;

	$jours=get_days_from_week_number($num_semaine, $annee);

	if($type_edt=="classe") {
		$_SESSION['edt_ics_id_classe']=$id_classe;

		$titre_edt="EDT de ".get_nom_classe($id_classe);
		$param_lien_edt="type_edt=classe&amp;id_classe=$id_classe";

		$sql_lien_semaine_precedente="SELECT num_semaine FROM edt_ics WHERE id_classe='$id_classe' AND num_semaine!='$num_semaine' AND date_debut<'".strftime("%Y-%m-%d %H:%M:%S", $jours['num_jour'][1]['timestamp'])."' ORDER BY date_debut DESC LIMIT 1;";

		$sql_lien_semaine_suivante="SELECT num_semaine FROM edt_ics WHERE id_classe='$id_classe' AND num_semaine!='$num_semaine' AND date_debut>'".strftime("%Y-%m-%d %H:%M:%S", $jours['num_jour'][7]['timestamp'])."' ORDER BY date_debut ASC LIMIT 1";

		$sql_cours_de_la_semaine="SELECT * FROM edt_ics WHERE id_classe='$id_classe' AND num_semaine='$num_semaine' ORDER BY date_debut;";
	}
	elseif($type_edt=="prof") {
		$_SESSION['edt_ics_login_prof']=$login_prof;

		// Désignation du prof à changer:
		$titre_edt="EDT de ".civ_nom_prenom($login_prof);
		$param_lien_edt="type_edt=prof&amp;login_prof=$login_prof";

		$sql_lien_semaine_precedente="SELECT num_semaine FROM edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof='$login_prof' AND num_semaine!='$num_semaine' AND date_debut<'".strftime("%Y-%m-%d %H:%M:%S", $jours['num_jour'][1]['timestamp'])."' ORDER BY date_debut DESC LIMIT 1;";

		$sql_lien_semaine_suivante="SELECT num_semaine FROM edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof='$login_prof' AND num_semaine!='$num_semaine' AND date_debut>'".strftime("%Y-%m-%d %H:%M:%S", $jours['num_jour'][7]['timestamp'])."' ORDER BY date_debut ASC LIMIT 1";

		$sql_cours_de_la_semaine="SELECT ei.* FROM edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof='$login_prof' AND num_semaine='$num_semaine' ORDER BY date_debut;";
	}

	echo "<h2>".$titre_edt."</h2>";

	$lien_semaine_prec="";
	//echo "$sql_lien_semaine_precedente<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql_lien_semaine_precedente);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$annee_b=$annee;
		if($lig->num_semaine>$num_semaine) {
			$annee_b=$annee-1;
		}
		$lien_semaine_prec="<a href='".$_SERVER['PHP_SELF']."?$param_lien_edt&amp;num_semaine_annee=".$lig->num_semaine."|$annee_b&amp;mode=afficher_edt' title=\"Semaine précédente\"><img src='../images/arrow_left.png' class='icone16' alt='Semaine précédente'></a> - ";
	}

	$lien_semaine_suiv="";
	//echo "$sql_lien_semaine_suivante<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql_lien_semaine_suivante);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$annee_b=$annee;
		if($lig->num_semaine<$num_semaine) {
			$annee_b=$annee+1;
		}
		$lien_semaine_suiv=" - <a href='".$_SERVER['PHP_SELF']."?$param_lien_edt&amp;num_semaine_annee=".$lig->num_semaine."|$annee_b&amp;mode=afficher_edt' title=\"Semaine suivante\"><img src='../images/arrow_right.png' class='icone16' alt='Semaine suivante'></a>";
	}

	echo "<h3>".$lien_semaine_prec."Semaine $num_semaine (".$jours['num_jour'][1]['jjmmaaaa']." - ".$jours['num_jour'][7]['jjmmaaaa'].")".$lien_semaine_suiv."</h3>";

	//echo "$sql_cours_de_la_semaine<br />";
	$res_cours_de_la_semaine=mysqli_query($GLOBALS["mysqli"], $sql_cours_de_la_semaine);
	if(mysqli_num_rows($res_cours_de_la_semaine)==0) {
		echo "<p>Aucun EDT n'est enregistré.</p>";
	}
	else {
		//==========================================
		//      AFFICHAGE EDT PROPREMENT DIT
		//==========================================

		//include("edt_ics_lib.php");

		$x0=200;
		$y0=300;

		$largeur_edt=800;

		// 60px pour 1h
		$hauteur_une_heure=60;

		$hauteur_titre=10;
		$hauteur_entete=40;

		$html=affiche_edt_ics($num_semaine_annee, $type_edt, $id_classe, $login_prof, $largeur_edt, $x0, $y0, $hauteur_une_heure, $hauteur_titre, $hauteur_entete);

		echo $html;

		$mode_infobulle="y";
		$html=affiche_edt_ics($num_semaine_annee, $type_edt, $id_classe, $login_prof);

		$titre_infobulle=$titre_edt;
		$texte_infobulle="<div style='width:1px; height:700px'>&nbsp;</div>".$html;
		//$tabdiv_infobulle[]=creer_div_infobulle('div_edt',$titre_infobulle,"",$texte_infobulle,"",60,0,'y','y','n','n');
		$tabdiv_infobulle[]=creer_div_infobulle('div_edt',$titre_infobulle,"",$texte_infobulle,"",($largeur_edt+100)."px",0,'y','y','n','n', 30);
		echo "<p><a href='#' onclick=\"afficher_div('div_edt','y',-20,20)\">EDT en infobulle</a></p>";

	}


}
else {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='".$_SERVER['PHP_SELF']."'>Importer un fichier ICS/ICAL</a></p>";

	echo "<p>Mode non implémenté.</p>";
}
require("../lib/footer.inc.php");
?>
