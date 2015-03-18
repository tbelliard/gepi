<?php
@set_time_limit(0);

/*
if((isset($_POST['deposer_message']))&&
(isset($_POST['message']))) {
	$traite_anti_inject = 'no';
}
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
description='EDT ICAL : Index',
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
/*
if((isset($_POST['deposer_message']))&&(acces_depos_message())) {
	check_token();
	$traite_anti_inject = 'no';
}
*/
//debug_var();

if((($_SESSION['statut']=="professeur")&&(!getSettingAOui('EdtIcalProf')))||
(($_SESSION['statut']=="eleve")&&(!getSettingAOui('EdtIcalEleve')))||
(($_SESSION['statut']=="responsable")&&(!getSettingAOui('EdtIcalResponsable')))) {
	header("Location: ../accueil.php?msg=Accès non autorisé.");
	die();
}

//================================================================
// Rapprochements
if((isset($_POST['rapprochements']))&&
(
	($_SESSION['statut']=="administrateur")||
	(getSettingAOui("EdtIcalUpload".casse_mot($_SESSION['statut'],"majf")))
)) {
	check_token();

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

if(($_SESSION['statut']=="professeur")&&(!getSettingAOui('EdtIcalProfTous'))&&(isset($mode))&&(($mode=="afficher_edt")||($mode=="afficher_edt_js"))) {
	if($id_classe!="") {
		$sql="SELECT 1=1 FROM j_groupes_professeurs jgp, j_groupes_classes jgc WHERE jgp.login='".$_SESSION['login']."' AND jgp.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			// Loguer les tentatives d'accès à un autre EDT?
			$sql="SELECT id_classe FROM j_groupes_professeurs jgp, j_groupes_classes jgc WHERE jgp.login='".$_SESSION['login']."' AND jgp.id_groupe=jgc.id_groupe;";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)==0) {
				$msg="Accès non autorisé.<br />Aucune classe ne vous est associée.<br />";
				unset($mode);
			}
			else {
				$msg="Accès non autorisé.<br />Voici une classe qui vous est associée.<br />";
				$lig=mysqli_fetch_object($test);
				$id_classe=$lig->id_classe;
			}
		}
	}
	elseif($login_prof!="") {
		// Loguer les tentatives d'accès à un autre EDT?
		$login_prof=$_SESSION['login'];
	}
	else {
		// Afficher l'EDT sans choix fait?
		unset($mode);
	}
}

//================================================================
// Affichage de l'EDT en infobulle
if((isset($_GET['mode']))&&($_GET['mode']=="afficher_edt_js")) {

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

//include("../ckeditor/ckeditor.php") ;

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

//**************** EN-TETE *****************
if($mode!="afficher_edt") {
	$titre_page = "EDT";
}
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

if(!isset($mode)) {
	$sql="SELECT DISTINCT id, classe FROM classes c, periodes p WHERE c.id=p.id_classe ORDER BY classe";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tab_classe[$lig->id]=$lig->classe;
	}

	$display_date_debut=strftime("%d/%m/%Y");
	$display_date_fin=strftime("%d/%m/%Y");
	//$contenu="___CLASSE___ : <a href='$gepiPath/edt/index.php?mode=afficher_edt&type_edt=classe&id_classe=___ID_CLASSE___&num_semaine=___NUM_SEMAINE___'>Emploi du temps modifié</a> pour la semaine n°___NUM_SEMAINE___";
	$contenu="<p>___CLASSE___ : ___LIEN_EMPLOI_DU_TEMPS___ modifi&eacute; pour la semaine n°___NUM_SEMAINE___</p>";

	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	if($_SESSION['statut']=='administrateur') {
		echo " | <a href='./index_admin.php'>Administrer le module</a>";
	}
	echo "</p>

<h2 class='gepi'>EDT d'après un fichier ICAL/ICS</h2>";

	if(($_SESSION['statut']=="administrateur")||
	(getSettingAOui("EdtIcalUpload".casse_mot($_SESSION['statut'],"majf")))) {
		include("../ckeditor/ckeditor.php") ;

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
		<input type=\"file\" size=\"65\" name=\"fich_ics_file\" id='input_ics_file' class='fieldset_opacite50' />
		<input type='hidden' name='mode' value='upload' />
		<input type='hidden' name='is_posted' value='yes' />

		<input type='submit' id='input_submit2' value='Valider' />
		<input type='button' id='input_button2' value='Valider' style='display:none;' onclick=\"check_champ_file()\" /></p>";

		if(acces_depos_message()) {
			echo "

		<br />

		<p><input type='checkbox' name='deposer_message' id='deposer_message' value='y' onchange=\"checkbox_change('deposer_message'); change_affichage_details_message();\" /><label for='deposer_message' id='texte_deposer_message'> Déposer un message en page d'accueil à destination des utilisateurs suivants
		<span id='span_nbsp_destinataires'>&nbsp;:</span>
		<span id='span_nbsp_destinataires_bis' style='display:none;'>...</span>
		</label></p>
		<div id='div_details_message'>
			<ul>
				<li><input type='checkbox' name='destinataire[]' id='destinataire_administrateur' value='administrateur' onchange=\"checkbox_change('destinataire_administrateur')\" /><label for='destinataire_administrateur' id='texte_destinataire_administrateur'> administrateurs</label></li>
				<li><input type='checkbox' name='destinataire[]' id='destinataire_scolarite' value='scolarite' onchange=\"checkbox_change('destinataire_administrateur')\" /><label for='destinataire_scolarite' id='texte_destinataire_scolarite'> comptes scolarité suivant cette classe</label></li>
				<li><input type='checkbox' name='destinataire[]' id='destinataire_cpe' value='cpe' onchange=\"checkbox_change('destinataire_cpe')\" /><label for='destinataire_cpe' id='texte_destinataire_cpe'> cpe</label></li>
				<li><input type='checkbox' name='destinataire[]' id='destinataire_professeur' value='professeur' onchange=\"checkbox_change('destinataire_professeur')\" /><label for='destinataire_professeur' id='texte_destinataire_professeur'> professeurs de la classe</label></li>
				<li><input type='checkbox' name='destinataire[]' id='destinataire_eleve' value='eleve' onchange=\"checkbox_change('destinataire_eleve')\" /><label for='destinataire_eleve' id='texte_destinataire_eleve'> élèves de la classe</label></li>
				<li><input type='checkbox' name='destinataire[]' id='destinataire_responsable' value='responsable' onchange=\"checkbox_change('destinataire_responsable')\" /><label for='destinataire_responsable' id='texte_destinataire_responsable'> parents d'élèves de la classe</label></li>
			</ul>

			<p>Le message sera visible du <input type='text' name = 'display_date_debut' id= 'display_date_debut' size='10' value = \"".$display_date_debut."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" title=\"Vous pouvez modifier les dates à l'aide des flèches Haut/bas du clavier.\" />".img_calendrier_js("display_date_debut", "img_bouton_display_date_debut")." au <input type='text' name = 'display_date_fin' id= 'display_date_fin' size='10' value = \"".$display_date_fin."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" title=\"Vous pouvez modifier les dates à l'aide des flèches Haut/bas du clavier.\" />".img_calendrier_js("display_date_fin", "img_bouton_display_date_fin").".</p>

			<p><i title=\"La suppression du message ne supprimera pas l'emploi du temps.
La suppression permet seulement à l'utilisateur d'alléger
sa page d'accueil une fois le message lu.\">Le destinataire peut supprimer ce message&nbsp;:&nbsp;</i>
			<label for='suppression_possible_oui'>Oui </label><input type='radio' name='suppression_possible' id='suppression_possible_oui' value='oui' checked='checked' />
			<label for='suppression_possible_non'>Non </label><input type='radio' name='suppression_possible' id='suppression_possible_non' value='non' /><br />
			La suppression de ces messages EDT est toujours possible pour les comptes administrateur, scolarite et cpe.</p>";

			$oCKeditor = new CKeditor('../ckeditor/');
			$oCKeditor->editor('message',$contenu) ;

			echo "
			<p>Dans le cas où vous déposez un message, vous pouvez, en précisant le numéro de semaine ci-dessous, faire pointer le lien EDT du message directement sur la semaine souhaitée&nbsp;: 
				<select name='num_semaine_annee'>
					<option value=''></option>";

			if(strftime("%m")>=8) {
				$annee=strftime("%Y");
			}
			else {
				$annee=strftime("%Y")-1;
			}
			for($n=36;$n<52;$n++) {
				$tmp_tab=get_days_from_week_number($n ,$annee);
				echo "
					<option value='$n|$annee'>Semaine n° $n   - (du ".$tmp_tab['num_jour'][1]['jjmmaaaa']." au ".$tmp_tab['num_jour'][7]['jjmmaaaa'].")</option>";
			}
			$annee++;
			for($n=1;$n<28;$n++) {
				$m=(($n<10) ? "0".$n : $n);
				$tmp_tab=get_days_from_week_number($m ,$annee);
				echo "
					<option value='".$m."|$annee'>Semaine n° $m   - (du ".$tmp_tab['num_jour'][1]['jjmmaaaa']." au ".$tmp_tab['num_jour'][7]['jjmmaaaa'].")</option>";
			}

			echo "
				</select><br />
				Le numéro de semaine choisi ci-dessus n'empêchera pas l'import de l'ensemble du fichier ICS fourni.
			</p>

			<p><input type='submit' id='input_submit' value='Valider' />
			<input type='button' id='input_button' value='Valider' style='display:none;' onclick=\"check_champ_file()\" /></p>

		</div>";
		}
		echo "

	</fieldset>

	<script type='text/javascript'>
		document.getElementById('input_submit').style.display='none';
		document.getElementById('input_button').style.display='';
		document.getElementById('input_submit2').style.display='none';
		document.getElementById('input_button2').style.display='';

		document.getElementById('span_nbsp_destinataires').style.display='none';
		document.getElementById('span_nbsp_destinataires_bis').style.display='';
		document.getElementById('div_details_message').style.display='none';

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

		function change_affichage_details_message() {
			if(document.getElementById('deposer_message').checked==true) {
				document.getElementById('span_nbsp_destinataires').style.display='';
				document.getElementById('span_nbsp_destinataires_bis').style.display='none';
				document.getElementById('div_details_message').style.display='';
			}
			else {
				document.getElementById('span_nbsp_destinataires').style.display='none';
				document.getElementById('span_nbsp_destinataires_bis').style.display='';
				document.getElementById('div_details_message').style.display='none';
			}
		}

		".js_checkbox_change_style("checkbox_change","texte_","n",0.5)."

		checkbox_change('destinataire_administrateur');
		checkbox_change('destinataire_scolarite');
		checkbox_change('destinataire_cpe');
		checkbox_change('destinataire_professeur');
		checkbox_change('destinataire_eleve');
		checkbox_change('destinataire_responsable');
	</script>
</form>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li>
		<p>Certaines chaines du message (<em>si vous en déposez un</em>) seront traitées de la façon suivante&nbsp;:<br />
		___CLASSE___ sera remplacé par le nom de la classe choisie dans le champ SELECT en haut du formulaire.<br />
		___LIEN_EMPLOI_DU_TEMPS___ sera remplacé par un lien vers l'emploi du temps avec les paramètres appropriés.<br />
		___ID_CLASSE___ sera remplacé par l'identifiant de la classe choisie (<em>le lien pointera vers l'EDT de cette classe</em>).<br />
		___NUM_SEMAINE___ sera remplacé par le numéro de la semaine pour que le lien pointe directement sur l'emploi du temps de la semaine souhaitée.</p>
	</li>
	<li style='margin-top:1em;'>
		<p>La FAQ EDT d'IndexEducation indique&nbsp;:</p>
		<pre>
		Pour exporter les emplois du temps en iCal :
		1. Rendez-vous dans le groupe de travail Gestion par semaine et absences et affichez pour les ressources souhaitées L'emploi du temps de la semaine....
		2. Lancez la commande Fichier &gt; Imprimer.
		3. Choisissez comme type de sortie iCal.
		4. Définissez les paramètres et les options de sortie puis cliquez sur Générer.
		5. EDT génère un fichier *.ics par ressource.
		</pre>
	</li>
</ul>

<pre style='color:red'>A FAIRE :
- En affichage prof, afficher des couleurs par classe plutôt que par matière
- Problème ACCPE: Cas de 2CO: 11 cours sur un créneau,
  c'est illisible.
- Vérifier le bon fonctionnement du rapprochement pour un prof avec apostrophe dans son nom.
- Pouvoir générer un EDT de salle,
  un EDT de matière (?) probablement difficile à lire sur un gros établissement
- Pouvoir choisir la taille de l'EDT? 800 (1h=60px), 1024 (1h=90px?)
- Pouvoir passer en paramètre les valeurs de x0 et y0,
  et mettre un JS pour décaler?
- Réduire la largeur du ckeditor</pre>

</div>";

		//echo "<br />";
	}

	echo "
<h3 class='gepi'>Consultation EDT</h3>
<div style='margin-left:3em;'>";
	//=================================================
	// Formulaire d'affichage de l'EDT pour les classes avec EDT renseigné

	if((in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe')))||
	(($_SESSION['statut']=='professeur')&&(getSettingAOui('EdtIcalProfTous')))) {
		$sql="SELECT DISTINCT c.id, c.classe FROM classes c, periodes p, edt_ics ei WHERE c.id=p.id_classe AND ei.id_classe=p.id_classe ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur') {
		$sql="SELECT DISTINCT c.id, c.classe FROM classes c, 
									periodes p, 
									edt_ics ei, 
									j_groupes_classes jgc, 
									j_groupes_professeurs jgp 
								WHERE c.id=p.id_classe AND 
									ei.id_classe=p.id_classe AND 
									jgc.id_classe=p.id_classe AND 
									jgc.id_groupe=jgp.id_groupe AND 
									jgp.login='".$_SESSION['login']."'
								ORDER BY classe";
	}
	elseif($_SESSION['statut']=='eleve') {
		$sql="SELECT DISTINCT c.id, c.classe FROM classes c, 
									periodes p, 
									edt_ics ei, 
									j_eleves_classes jec 
								WHERE c.id=p.id_classe AND 
									ei.id_classe=p.id_classe AND 
									jec.id_classe=p.id_classe AND 
									jec.login='".$_SESSION['login']."'
								ORDER BY classe";
	}
	elseif($_SESSION['statut']=='responsable') {
		$sql="SELECT DISTINCT c.id, c.classe FROM classes c, 
									periodes p, 
									edt_ics ei, 
									j_eleves_classes jec, 
									eleves e, 
									responsables2 r,
									resp_pers rp
								WHERE c.id=p.id_classe AND 
									ei.id_classe=p.id_classe AND 
									jec.id_classe=p.id_classe AND 
									jec.login=e.login AND 
									e.ele_id=r.ele_id AND 
									r.pers_id=rp.pers_id AND 
									rp.login='".$_SESSION['login']."'
								ORDER BY classe";
	}
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucun emploi du temps de classe n'est encore importé.</p>";
	}
	else {
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
	if(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe', 'professeur'))) {

		if((in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe')))||
		(($_SESSION['statut']=='professeur')&&(getSettingAOui('EdtIcalProfTous')))) {
			$sql="SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof=u.login ORDER BY u.nom, u.prenom;";
		}
		elseif($_SESSION['statut']=='professeur') {
			$sql="SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof=u.login AND u.login='".$_SESSION['login']."' ORDER BY u.nom, u.prenom;";
		}
	
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
	(getSettingAOui("EdtIcalUpload".casse_mot($_SESSION['statut'],"majf")))
)) {
	check_token(false);

	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='".$_SERVER['PHP_SELF']."'>Importer un autre fichier ICS/ICAL</a></p>

<center><h3 class='gepi'>Import d'un fichier ICAL/ICS d'emploi du temps</h3></center>";

	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
	}

	$post_max_size=ini_get('post_max_size');
	$upload_max_filesize=ini_get('upload_max_filesize');
	$max_execution_time=ini_get('max_execution_time');
	$memory_limit=ini_get('memory_limit');

	$ics_file = isset($_FILES["fich_ics_file"]) ? $_FILES["fich_ics_file"] : NULL;

	echo "<p class='bold'>Traitement du fichier ICAL/ICS&nbsp;:</p>";

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

		echo "<p>Le fichier ".$ics_file['name']." a été uploadé.</p>\n";

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
						$matiere_ics=trim(preg_replace("/^ *Matière : /", "", $tab[$loop]));
					}
					elseif(preg_match("/^ *Professeur : /", $tab[$loop])) {
						$prof_ics=trim(preg_replace("/^ *Professeur : /", "", $tab[$loop]));
					}
					elseif(preg_match("/^ *Classe : /", $tab[$loop])) {
						$classe_ics=trim(preg_replace("/^ *Classe : /", "", $tab[$loop]));
					}
					elseif(preg_match("/^ *Salle : /", $tab[$loop])) {
						$salle_ics=trim(preg_replace("/^ *Salle : /", "", $tab[$loop]));
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

		//===============================================================
		// Enregistrement du message associé s'il y en a un
		//debug_var();

		if(($nb_reg>0)&&
		(acces_depos_message())&&
		(isset($_POST['deposer_message']))&&
		(isset($_POST['message']))&&
		($_POST['message']!="")&&
		(isset($_POST['num_semaine_annee']))&&
		(isset($_POST['destinataire']))) {
			$destinataire=$_POST['destinataire'];
			$suppression_possible=$_POST['suppression_possible'];
			$date_debut=$_POST['display_date_debut'];
			$date_fin=$_POST['display_date_fin'];
			$date_decompte=$date_fin;

			echo "<br /><p class='bold'>Traitement du message&nbsp;:</p>";

			$record="yes";
			if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $date_debut)) {
				$anneed = mb_substr($date_debut,6,4);
				$moisd = mb_substr($date_debut,3,2);
				$jourd = mb_substr($date_debut,0,2);
				while ((!checkdate($moisd, $jourd, $anneed)) and ($jourd > 0)){$jourd--;}
				$date_debut=mktime(0,0,0,$moisd,$jourd,$anneed);
			} else {
				echo "<span style='color:red'>ATTENTION : La date de début d'affichage n'est pas valide.<br />(message non enregitré)</span><br />";
				$record = 'no';
			}

			if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $date_fin)) {
				$anneef = mb_substr($date_fin,6,4);
				$moisf = mb_substr($date_fin,3,2);
				$jourf = mb_substr($date_fin,0,2);
				while ((!checkdate($moisf, $jourf, $anneef)) and ($jourf > 0)){$jourf--;}
				$date_fin=mktime(23,59,0,$moisf,$jourf,$anneef);
			} else {
				echo "<span style='color:red'>ATTENTION : La date de fin d'affichage n'est pas valide.<br />(message non enregitré)</span><br />";
				$record = 'no';
			}

			if($record!='no') {
				//$contenu_cor = trim(traitement_magic_quotes(corriger_caracteres($_POST['message'])));
				//$contenu_cor = trim($_POST['message']);
				//$contenu_cor = nl2br(trim($_POST['message']));
				//$contenu_cor=preg_replace("/<p>\n /", "", $contenu_cor);
				// Il reste encore des \\n que je n'arrive pas à virer.

				$contenu_cor = trim(preg_replace('/(\\\n)*/',"",$_POST['message']));


				//echo "\$num_semaine_annee=$num_semaine_annee<br />";
				$num_semaine_annee=$_POST['num_semaine_annee'];
				if($num_semaine_annee!="") {
					if(!preg_match("/[0-9]{2}\|[0-9]{4}/", $num_semaine_annee)) {
						$num_semaine_annee="36|".((strftime("%m")>7) ? strftime("%Y") : (strftime("%Y")-1));
					}

					$tab=explode("|", $num_semaine_annee);

					$num_semaine=$tab[0];
					$annee=$tab[1];

					if(($num_semaine<10)&&(mb_substr($num_semaine, 0, 1)!="0")) {
						$num_semaine="0".$num_semaine;
					}

					$num_semaine_annee=$num_semaine."|".$annee;
				}
				//echo "\$num_semaine_annee=$num_semaine_annee<br />";

				$classe=get_nom_classe($id_classe);
				$contenu_cor=preg_replace("/___ID_CLASSE___/", $id_classe, $contenu_cor);
				$contenu_cor=preg_replace("/___CLASSE___/", $classe, $contenu_cor);
				$contenu_cor=preg_replace("/___NUM_SEMAINE___/", $num_semaine, $contenu_cor);

				//$contenu_cor=preg_replace("/.*$classe/", "<p>$classe", $contenu_cor);
				// Cela fonctionne pour les \\n au début du message, mais cela nécessite qu'il y ait $classe au début du message, et cela ne règle pas le problème du \\n en fin de message.

				$contenu_cor=preg_replace("/___LIEN_EMPLOI_DU_TEMPS___/", "<a href='$gepiPath/edt/index.php?mode=afficher_edt&type_edt=classe&id_classe=$id_classe&num_semaine_annee=".$num_semaine_annee."'>Emploi du temps</a>", $contenu_cor);

				// par sécurité les rédacteurs d'un message ne peuvent y insérer la variable _CSRF_ALEA_
				$pos_crsf_alea=strpos($contenu_cor,"_CSRF_ALEA_");
				if($pos_crsf_alea!==false) {
					$contenu_cor=preg_replace("/_CSRF_ALEA_/","",$contenu_cor);
					echo "<p style='color:red'>Le message proposé contenait une chaine interdite.<br />Il n'a pas été enregistré.</p>";
				}
				else {

					// A VOIR : Faudrait-il effectuer un traitement HTMLpurifier sur $contenu_cor après les remplacements?

					$contenu_cor=mysqli_real_escape_string($GLOBALS["mysqli"], $contenu_cor);

					/*
					function ajout_bouton_supprimer_message($contenu_cor,$id_message)
					{
						$contenu_cor='
						<form method="POST" action="accueil.php" name="f_suppression_message">
						<input type="hidden" name="csrf_alea" value="_CSRF_ALEA_">
						<input type="hidden" name="supprimer_message" value="'.$id_message.'">
						<button type="submit" title=" Supprimer ce message " style="border: none; background: none; float: right;"><img style="vertical-align: bottom;" src="images/icons/delete.png"></button>
						</form>'.$contenu_cor;
						$r_sql="UPDATE messages SET texte='".$contenu_cor."' WHERE id='".$id_message."'";
						return mysqli_query($GLOBALS["mysqli"], $r_sql)?true:false;
					}

					function set_message($contenu_cor,$date_debut,$date_fin,$date_decompte,$statuts_destinataires,$login_destinataire)
					{
						global $suppression_possible;

						$r_sql = "INSERT INTO messages
						SET texte = '".$contenu_cor."',
						date_debut = '".$date_debut."',
						date_fin = '".$date_fin."',
						date_decompte = '".$date_decompte."',
						auteur='".$_SESSION['login']."',
						statuts_destinataires = '".$statuts_destinataires."',
						login_destinataire='".$login_destinataire."'";
						//echo "$r_sql<br />";
						$retour=mysqli_query($GLOBALS["mysqli"], $r_sql)?true:false;
						if ($retour)
						{
							$id_message=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

							if ($suppression_possible=="oui" &&  $statuts_destinataires=="_") {
								$retour=ajout_bouton_supprimer_message($contenu_cor,$id_message);
							}
						}
						return $retour;
					}
					*/

					$cpt_dest=0;
					$statuts_destinataires="_";
					$t_login_destinataires=array();

					if(in_array('professeur', $destinataire)) {
						// les profs de la classe
						$r_sql="SELECT DISTINCT utilisateurs.login FROM j_groupes_classes,groupes,j_groupes_professeurs,utilisateurs WHERE j_groupes_classes.id_classe='".$id_classe."' AND j_groupes_classes.id_groupe=groupes.id AND groupes.id=j_groupes_professeurs.id_groupe AND j_groupes_professeurs.login=utilisateurs.login";
						$R_professeurs=mysqli_query($GLOBALS["mysqli"], $r_sql);
						while ($un_professeur=mysqli_fetch_assoc($R_professeurs)) {
							if(!in_array($un_professeur['login'], $t_login_destinataires)) {
								$t_login_destinataires[]=$un_professeur['login'];
							}
						}
					}

					if(in_array('professeur', $destinataire)) {
						// les élèves de la classe
						$r_sql="SELECT DISTINCT u.login FROM j_eleves_classes jec, 
												utilisateurs u 
											WHERE jec.id_classe='".$id_classe."' AND 
											jec.login=u.login";
						$R_eleves=mysqli_query($GLOBALS["mysqli"], $r_sql);
						while ($un_eleve=mysqli_fetch_assoc($R_eleves)) {
							if(!in_array($un_eleve['login'], $t_login_destinataires)) {
								$t_login_destinataires[]=$un_eleve['login'];
							}
						}
					}

					if(in_array('professeur', $destinataire)) {
						// les responsables élèves de la classe
						$r_sql="SELECT DISTINCT u.login FROM j_eleves_classes jec, 
												eleves e,
												responsables2 r,
												resp_pers rp,
												utilisateurs u 
											WHERE jec.id_classe='".$id_classe."' AND 
											jec.login=e.login AND
											e.ele_id=r.ele_id AND
											r.pers_id=rp.pers_id AND
											rp.login=u.login";
						$R_parents=mysqli_query($GLOBALS["mysqli"], $r_sql);
						while ($un_parent=mysqli_fetch_assoc($R_parents)) {
							if(!in_array($un_parent['login'], $t_login_destinataires)) {
								$t_login_destinataires[]=$un_parent['login'];
							}
						}
					}

					foreach($t_login_destinataires as $login_destinataire) {
						if(!set_message($contenu_cor,$date_debut,$date_fin,$date_decompte,$statuts_destinataires,$login_destinataire)) {
							echo "<span style='color:red'>Erreur lors de l'enregistrement du message à destination de ".civ_nom_prenom($login_destinataire)."</span><br />";
						}
						else {
							$cpt_dest++;
						}
					}

					$suppression_possible="oui";
					$t_login_destinataires=array();
					if(in_array('administrateur', $destinataire)) {
						// les comptes administrateur
						$r_sql="SELECT DISTINCT utilisateurs.login FROM utilisateurs WHERE statut='administrateur' AND etat='actif';";
						$R_user=mysqli_query($GLOBALS["mysqli"], $r_sql);
						while ($un_user=mysqli_fetch_assoc($R_user)) {
							if(!in_array($un_user['login'], $t_login_destinataires)) {
								$t_login_destinataires[]=$un_user['login'];
							}
						}
					}

					if(in_array('scolarite', $destinataire)) {
						// les comptes scolarité de la classe
						$r_sql="SELECT DISTINCT u.login FROM j_scol_classes jsc, utilisateurs u WHERE jsc.login=u.login AND jsc.id_classe='$id_classe';";
						while ($un_user=mysqli_fetch_assoc($R_user)) {
							if(!in_array($un_user['login'], $t_login_destinataires)) {
								$t_login_destinataires[]=$un_user['login'];
							}
						}
					}

					if(in_array('cpe', $destinataire)) {
						// les comptes cpe de la classe
						$r_sql="SELECT DISTINCT u.login FROM j_eleves_cpe jecpe, j_eleves_classes jec, utilisateurs u WHERE jecpe.cpe_login=u.login AND jecpe.e_login=jec.login AND jec.id_classe='$id_classe';";
						while ($un_user=mysqli_fetch_assoc($R_user)) {
							if(!in_array($un_user['login'], $t_login_destinataires)) {
								$t_login_destinataires[]=$un_user['login'];
							}
						}
					}

					foreach($t_login_destinataires as $login_destinataire) {
						if(!set_message($contenu_cor,$date_debut,$date_fin,$date_decompte,$statuts_destinataires,$login_destinataire)) {
							echo "<span style='color:red'>Erreur lors de l'enregistrement du message à destination de ".civ_nom_prenom($login_destinataire)."</span><br />";
						}
						else {
							$cpt_dest++;
						}
					}

					if($cpt_dest>0) {
						echo "<p>Message enregistré pour $cpt_dest destinataire(s).</p>";
					}
				}
			}
		}
		//===============================================================

		// Rapprochements profs et matières
		echo "<br />

<p class='bold'>Rapprochements&nbsp;:</p>

<p>Pour obtenir un EDT plus lisible, ne pas afficher les prénoms des professeur,... il est recommandé de procéder aux rapprochements ci-dessous (<em>rapprochements entre le contenu du fichier ICAL/ICS et les matières et professeurs déclarés dans Gepi</em>).<br />
Les rapprochements sont également utilisés dans les champs de sélection des professeurs dont ont souhaite afficher l'EDT.</p>

<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi' method='post'>
	<fieldset class='fieldset_opacite50'>
	".add_token_field();

		$sql="SELECT DISTINCT prof_ics FROM edt_ics WHERE prof_ics!='' AND prof_ics NOT IN (SELECT prof_ics FROM edt_ics_prof);";
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

			$tab_prof=array();
			$cpt=0;
			$lignes_profs="";
			while($lig_prof=mysqli_fetch_object($res_prof)) {
				$lignes_profs.="<option value='".$lig_prof->login."'";
				if($lig_prof->etat=='inactif') {
					$lignes_profs.=" style='color:grey' title=\"Compte utilisateur inactif.\"";
				}
				$lignes_profs.=">".$lig_prof->civilite." ".$lig_prof->nom." ".$lig_prof->prenom."</option>";

				$tab_prof[$cpt]['login']=$lig_prof->login;
				$tab_prof[$cpt]['nom']=$lig_prof->nom;
				$tab_prof[$cpt]['prenom']=$lig_prof->prenom;
				$tab_prof[$cpt]['designation']=$lig_prof->civilite." ".casse_mot($lig_prof->nom, "maj")." ".casse_mot($lig_prof->prenom, "majf2");
				$tab_prof[$cpt]['etat']=$lig_prof->etat;
				$cpt++;
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
				<option value=''>---</option>";
				//echo $lignes_profs;
				for($loop=0;$loop<count($tab_prof);$loop++) {
					$selected="";
					$chaine_debug="";
					//$chaine_debug=" ".remplace_accents(casse_mot($lig->prof_ics, "maj"),'all')." comparé à ".remplace_accents(casse_mot($tab_prof[$loop]['designation'], "maj"),'all');
					if(((getSettingValue('EdtIcalFormatNomProf')!="nom")&&(trim(casse_mot($lig->prof_ics, "maj"))==casse_mot($tab_prof[$loop]['designation'], "maj")))||
					((getSettingValue('EdtIcalFormatNomProf')=="nom")&&(trim(casse_mot($lig->prof_ics, "maj"))==casse_mot($tab_prof[$loop]['nom'], "maj")))) {
						$selected=" selected='selected'";
					}
					$style_opt="";
					if($tab_prof[$loop]['etat']=='inactif') {
						$style_opt.=" style='color:grey' title=\"Compte utilisateur inactif.\"";
					}

					echo "
				<option value='".$tab_prof[$loop]['login']."'".$style_opt.$selected.">".$tab_prof[$loop]['designation'].$chaine_debug."</option>";
				}
				echo "
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

			$tab_mat=array();
			$cpt=0;
			$lignes_mat="";
			while($lig_mat=mysqli_fetch_object($res_mat)) {
				$style_opt="";
				$sql="SELECT 1=1 FROM j_groupes_matieres WHERE id_matiere='$lig_mat->matiere';";
				$res_grp_mat=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_grp_mat)==0) {
					$style_opt.=" style='color:grey' title=\"Aucun enseignement n'est associé à la matière $lig_mat->matiere.\"";
				}

				$lignes_mat.="<option value='".$lig_mat->matiere."'";
				$lignes_mat.=$style_opt;
				$lignes_mat.=">".$lig_mat->nom_complet."</option>";

				$tab_mat[$cpt]['matiere']=$lig_mat->matiere;
				$tab_mat[$cpt]['nom_complet']=$lig_mat->nom_complet;
				$tab_mat[$cpt]['designation_supposee_edt_ics']=casse_mot($lig_mat->matiere." ".$lig_mat->nom_complet, 'maj');
				$tab_mat[$cpt]['style_opt']=$style_opt;
				$cpt++;
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
				";
				//echo $lignes_mat;

				for($loop=0;$loop<count($tab_mat);$loop++) {
					$chaine_debug="";
					$selected=" ";
					if(((getSettingValue('EdtIcalFormatNomMatière')=="nom_court")&&(trim(casse_mot($lig->matiere_ics, "maj"))==casse_mot($tab_mat[$loop]['matiere'], "maj")))||
					((getSettingValue('EdtIcalFormatNomMatière')=="nom_complet")&&(trim(casse_mot($lig->matiere_ics, "maj"))==casse_mot($tab_mat[$loop]['nom_complet'], "maj")))||
					((getSettingValue('EdtIcalFormatNomMatière')=="nom_court nom_complet")&&(trim(casse_mot($lig->matiere_ics, "maj"))==casse_mot($tab_mat[$loop]['designation_supposee_edt_ics'], "maj")))) {
						$selected=" selected='selected'";
					}
					echo "
				<option value='".$tab_mat[$loop]['matiere']."'".$selected.$tab_mat[$loop]['style_opt'].">".$tab_mat[$loop]['designation_supposee_edt_ics'].$chaine_debug."</option>";
				}
				echo "
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
			if(($_SESSION['statut']=='administrateur')||
			(($_SESSION['statut']=='scolarite')&&(getSettingAOui('EdtIcalUploadScolarite')))|| 
			(($_SESSION['statut']=='cpe')&&(getSettingAOui('EdtIcalUploadCpe')))) {
				echo "<p class='bold'><a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
			}
			else {
				echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
			}
			echo "<p style='color:red'>Aucun enregistrement trouvé.</p>";
			require("../lib/footer.inc.php");
			die();
		}
	}

	echo "<div style='float:left;width:35em; font-weight:bold'>
	<form action='".$_SERVER['PHP_SELF']."' id='form_choix_autre_semaine' method='post'>
		<!--a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link' title=\"Retour au menu EDT\"/> Retour</a-->
		<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour</a>";

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
	/*
	if(($_SESSION['statut']!="eleve")&&($_SESSION['statut']!="responsable")) {
		// Choisir un EDT de classe:
		$sql="SELECT DISTINCT c.id, c.classe FROM classes c, periodes p, edt_ics ei WHERE c.id=p.id_classe AND ei.id_classe=p.id_classe ORDER BY classe";
	*/

		if((in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe')))||
		(($_SESSION['statut']=='professeur')&&(getSettingAOui('EdtIcalProfTous')))) {
			$sql="SELECT DISTINCT c.id, c.classe FROM classes c, periodes p, edt_ics ei WHERE c.id=p.id_classe AND ei.id_classe=p.id_classe ORDER BY classe";
		}
		elseif($_SESSION['statut']=='professeur') {
			$sql="SELECT DISTINCT c.id, c.classe FROM classes c, 
										periodes p, 
										edt_ics ei, 
										j_groupes_classes jgc, 
										j_groupes_professeurs jgp 
									WHERE c.id=p.id_classe AND 
										ei.id_classe=p.id_classe AND 
										jgc.id_classe=p.id_classe AND 
										jgc.id_groupe=jgp.id_groupe AND 
										jgp.login='".$_SESSION['login']."'
									ORDER BY classe";
		}
		elseif($_SESSION['statut']=='eleve') {
			$sql="SELECT DISTINCT c.id, c.classe FROM classes c, 
										periodes p, 
										edt_ics ei, 
										j_eleves_classes jec 
									WHERE c.id=p.id_classe AND 
										ei.id_classe=p.id_classe AND 
										jec.id_classe=p.id_classe AND 
										jec.login='".$_SESSION['login']."'
									ORDER BY classe";
		}
		elseif($_SESSION['statut']=='responsable') {
			$sql="SELECT DISTINCT c.id, c.classe FROM classes c, 
										periodes p, 
										edt_ics ei, 
										j_eleves_classes jec, 
										eleves e, 
										responsables2 r,
										resp_pers rp
									WHERE c.id=p.id_classe AND 
										ei.id_classe=p.id_classe AND 
										jec.id_classe=p.id_classe AND 
										jec.login=e.login AND 
										e.ele_id=r.ele_id AND 
										r.pers_id=rp.pers_id AND 
										rp.login='".$_SESSION['login']."'
									ORDER BY classe";
		}
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
		//$sql="SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof=u.login ORDER BY u.nom, u.prenom;";
		if(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe', 'professeur'))) {

			if((in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe')))||
			(($_SESSION['statut']=='professeur')&&(getSettingAOui('EdtIcalProfTous')))) {
				$sql="SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof=u.login ORDER BY u.nom, u.prenom;";
			}
			elseif($_SESSION['statut']=='professeur') {
				$sql="SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, edt_ics ei, edt_ics_prof eip WHERE ei.prof_ics=eip.prof_ics AND eip.login_prof=u.login AND u.login='".$_SESSION['login']."' ORDER BY u.nom, u.prenom;";
			}
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
	//}
	// ================================================
	//    FIN DE LA LIGNE DE CHOIX SOUS L'ENTETE
	// ================================================

	echo "
<div style='clear:both'></div>";

	//echo "\$num_semaine_annee=$num_semaine_annee<br />";
	if(($num_semaine_annee=="")||(!preg_match("/[0-9]{2}\|[0-9]{4}/", $num_semaine_annee))) {
		$num_semaine_annee="36|".((strftime("%m")>7) ? strftime("%Y") : (strftime("%Y")-1));
	}

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
		$y0=200;

		$largeur_edt=800;

		// 60px pour 1h
		$hauteur_une_heure=60;

		$hauteur_titre=10;
		$hauteur_entete=40;

		$html=affiche_edt_ics($num_semaine_annee, $type_edt, $id_classe, $login_prof, $largeur_edt, $x0, $y0, $hauteur_une_heure, $hauteur_titre, $hauteur_entete);

		echo $html;

		/*
		// Pour tester:
		$mode_infobulle="y";
		$html=affiche_edt_ics($num_semaine_annee, $type_edt, $id_classe, $login_prof);

		$titre_infobulle=$titre_edt;
		$texte_infobulle="<div style='width:1px; height:700px'>&nbsp;</div>".$html;
		//$tabdiv_infobulle[]=creer_div_infobulle('div_edt',$titre_infobulle,"",$texte_infobulle,"",60,0,'y','y','n','n');
		$tabdiv_infobulle[]=creer_div_infobulle('div_edt',$titre_infobulle,"",$texte_infobulle,"",($largeur_edt+100)."px",0,'y','y','n','n', 30);
		echo "<p><a href='#' onclick=\"afficher_div('div_edt','y',-20,20)\">EDT en infobulle</a></p>";
		*/
	}


}
else {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='".$_SERVER['PHP_SELF']."'>Importer un fichier ICS/ICAL</a></p>";

	echo "<p>Mode non implémenté.</p>";
}
require("../lib/footer.inc.php");
?>
