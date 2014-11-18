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

$sql="SELECT 1=1 FROM droits WHERE id='/edt/index2.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/edt/index2.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='V',
autre='F',
description='EDT 2 : Index',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(((($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable'))&&((getSettingAOui('autorise_edt_eleve'))||(getSettingAOui('autorise_edt2_eleve'))))||
((in_array($_SESSION['statut'], array('professeur', 'cpe', 'scolarite')))&&(getSettingAOui('autorise_edt_tous')))||
(($_SESSION['statut']=='administrateur')&&(getSettingAOui('autorise_edt_admin')))) {
	// On va afficher l'EDT
}
else {
	header("Location: ../accueil.php?msg=Accès non autorisé");
	die();
}

$msg="";

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

require("edt_ics_lib.php");

$type_edt=isset($_POST['type_edt']) ? $_POST['type_edt'] : (isset($_GET['type_edt']) ? $_GET['type_edt'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : "");
$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : (isset($_GET['login_prof']) ? $_GET['login_prof'] : "");
$num_semaine_annee=isset($_POST['num_semaine_annee']) ? $_POST['num_semaine_annee'] : (isset($_GET['num_semaine_annee']) ? $_GET['num_semaine_annee'] : NULL);
$affichage=isset($_POST['affichage']) ? $_POST['affichage'] : (isset($_GET['affichage']) ? $_GET['affichage'] : "semaine");

$display_date=isset($_POST['display_date']) ? $_POST['display_date'] : (isset($_GET['display_date']) ? $_GET['display_date'] : NULL);

$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : (isset($_GET['login_eleve']) ? $_GET['login_eleve'] : NULL);

//===================================================
// Contrôler si le jour est dans la période de l'année scolaire courante
$ts_debut_annee=getSettingValue('begin_bookings');
$ts_fin_annee=getSettingValue('end_bookings');
//===================================================

//===================================================
if($affichage!="semaine") {
	if(!isset($display_date)) {
		if((isset($num_semaine_annee))&&(preg_match("/^[0-9]{1,}\|[0-9]{4}$/", $num_semaine_annee))) {
			$tmp_tab=explode("|", $num_semaine_annee);
			if(!isset($tmp_tab[1])) {
				$display_date=strftime("%d/%m/%Y");
				$affichage=strftime("%u");
			}
			else {
				$tmp_tab2=get_days_from_week_number($tmp_tab[0] ,$tmp_tab[1]);
				/*
				echo "<pre>";
				print_r($tmp_tab2);
				echo "</pre>";
				*/
				if(isset($tmp_tab2['num_jour'][$affichage])) {
					$display_date=$tmp_tab2['num_jour'][$affichage]['jjmmaaaa'];
				}
				else {
					$display_date=$tmp_tab2['num_jour'][1]['jjmmaaaa'];
					$affichage=1;
				}
			}
		}
		else {
			$display_date=strftime("%d/%m/%Y");
			$affichage=strftime("%u");
		}
	}
	elseif(!preg_match("#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#", $display_date)) {
		$msg.="Date $display_date invalide.<br />";
		unset($display_date);
		$display_date=strftime("%d/%m/%Y");
		$affichage=strftime("%u");
	}

	$tmp_tab=explode("/", $display_date);
	$ts_display_date=mktime(12, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
	$ts_debut_jour=mktime(0, 0, 0, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
	$ts_debut_jour_suivant=mktime(23, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2])+1;
	$num_semaine=strftime("%V", $ts_display_date);

	$num_semaine_annee=$num_semaine."|".$tmp_tab[2];

	if($affichage=="jour") {
		$affichage=strftime("%u", $ts_display_date);
	}
	elseif($affichage!=strftime("%u", $ts_display_date)) {
		$msg.="Le jour choisi '$affichage' ne correspond pas à la date $display_date<br />";
		$affichage=strftime("%u", $ts_display_date);
	}

	$tab_jour=get_tab_jour_ouverture_etab();

	if(!in_array(strftime("%A", $ts_display_date), $tab_jour)) {
		// Jour suivant
		// Boucler sur 7 jours pour trouver le jour ouvré suivant
		// Il faudrait même chercher une date hors vacances
		$ts_display_date_suivante="";
		$display_date_suivante="";
		$display_date_suivante_num_jour="";
		$ts_test=$ts_display_date;
		$cpt=0;
		while(($cpt<7)&&($ts_test<$ts_fin_annee)) {
			$ts_test+=3600*24;
			if(in_array(strftime("%A", $ts_test), $tab_jour)) {
				$ts_display_date_suivante=$ts_test;
				$display_date_suivante=strftime("%d/%m/%Y", $ts_test);
				$display_date_suivante_num_jour=strftime("%u", $ts_test);
				break;
			}
			$cpt++;
		}
		if($display_date_suivante!="") {
			$ts_display_date=$ts_display_date_suivante;
			$display_date=$display_date_suivante;
			$affichage=$display_date_suivante_num_jour;

			$tmp_tab=explode("/", $display_date);
			$ts_display_date=mktime(12, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
			$ts_debut_jour=mktime(0, 0, 0, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
			$ts_debut_jour_suivant=mktime(23, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2])+1;
			$num_semaine=strftime("%V", $ts_display_date);

			$num_semaine_annee=$num_semaine."|".$tmp_tab[2];
		}
	}

	if($ts_display_date<$ts_debut_annee) {
		$msg.="Première date possible&nbsp;: Début de l'année scolaire.<br />";
		$ts_display_date=$ts_debut_annee;

		$display_date=strftime("%d/%m/%Y", $ts_display_date);
		$affichage=strftime("%u", $ts_display_date);

		$tmp_tab=explode("/", $display_date);
		$ts_debut_jour=mktime(0, 0, 0, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
		$ts_debut_jour_suivant=mktime(23, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2])+1;
		$num_semaine=strftime("%V", $ts_display_date);

		$num_semaine_annee=$num_semaine."|".$tmp_tab[2];
	}
	elseif($ts_display_date>$ts_fin_annee) {
		$msg.="Dernière date possible&nbsp;: Fin de l'année scolaire.<br />";
		$ts_display_date=$ts_fin_annee;

		$display_date=strftime("%d/%m/%Y", $ts_display_date);
		$affichage=strftime("%u", $ts_display_date);

		$tmp_tab=explode("/", $display_date);
		$ts_debut_jour=mktime(0, 0, 0, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
		$ts_debut_jour_suivant=mktime(23, 59, 59, $tmp_tab[1], $tmp_tab[0], $tmp_tab[2])+1;
		$num_semaine=strftime("%V", $ts_display_date);

		$num_semaine_annee=$num_semaine."|".$tmp_tab[2];
	}
}
//===================================================
if((!isset($num_semaine_annee))||($num_semaine_annee=="")||(!preg_match("/[0-9]{2}\|[0-9]{4}/", $num_semaine_annee))) {
	$num_semaine_annee="36|".((strftime("%m")>7) ? strftime("%Y") : (strftime("%Y")-1));
}
//===================================================
if($affichage=="semaine") {
	$tmp_tab=explode("|", $num_semaine_annee);
	$num_semaine=$tmp_tab[0];
	$annee=$tmp_tab[1];
	$jours=get_days_from_week_number($num_semaine, $annee);

	$ts_display_date=$jours['num_jour'][1]['timestamp'];
}
//===================================================

//===================================================
// Filtrage/contrôle de l'id_classe dans le cas élève/responsable
if($_SESSION['statut']=="eleve") {
	$login_eleve=$_SESSION['login'];
}
elseif($_SESSION['statut']=="responsable") {
	$tab_ele2=array();
	$tab_ele=get_enfants_from_resp_login($_SESSION['login'], 'simple');
	for($loop=0;$loop<count($tab_ele);$loop+=2) {
		$tab_ele2[]=$tab_ele[$loop];
	}

	if(count($tab_ele2)==0) {
		header("Location: ../accueil.php?msg=Aucun élève trouvé");
		die();
	}

	if((!isset($login_eleve))||(!in_array($login_eleve, $tab_ele2))) {
		$login_eleve=$tab_ele2[0];
	}
	// Il faudra proposer le choix si count($tab_ele2)>1

	$login_ele_prec="";
	$login_ele_suiv="";
	$nom_prenom_ele_prec="";
	$nom_prenom_ele_suiv="";
	$login_ele_trouve=0;
	for($loop=0;$loop<count($tab_ele);$loop+=2) {
		if(($tab_ele[$loop]!=$login_eleve)&&($login_ele_trouve==0)) {
			$login_ele_prec=$tab_ele[$loop];
			$nom_prenom_ele_prec=$tab_ele[$loop+1];
		}
		elseif($tab_ele[$loop]==$login_eleve) {
			$login_ele_trouve++;
		}
		elseif($login_ele_trouve==1) {
			$login_ele_suiv=$tab_ele[$loop];
			$nom_prenom_ele_suiv=$tab_ele[$loop+1];
			$login_ele_trouve++;
		}
	}
}
else {
	if(!isset($login_eleve)) {
		header("Location: ../accueil.php?msg=Elève non choisi");
		die();
	}
}

$info_eleve=get_nom_prenom_eleve($login_eleve, "avec_classe");
$id_classe=get_id_classe_ele_d_apres_date($login_eleve, $ts_display_date);
if($id_classe=="") {
	$id_classe=get_id_classe_derniere_classe_ele($login_eleve);
}

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

// onclick=\"return confirm_abandon (this, change, '$themessage')\"
echo "<p class='bold'><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

//debug_var();

$tab_jour=get_tab_jour_ouverture_etab();

echo "<p class='bold'>";

if((isset($login_ele_prec))&&($login_ele_prec!="")) {
	if($affichage=="semaine") {
		echo "<a href='".$_SERVER['PHP_SELF']."?login_eleve=".$login_ele_prec."&amp;affichage=semaine&amp;num_semaine_annee=$num_semaine_annee' title=\"Voir la page pour $nom_prenom_ele_prec\"><img src=\"../images/arrow_left.png\" class='icone16' alt=\"$nom_prenom_ele_prec\" /></a> ";
	}
	else {
		echo "<a href='".$_SERVER['PHP_SELF']."?login_eleve=".$login_ele_prec."&amp;display_date=$display_date&amp;affichage=jour' title=\"Voir la page pour $nom_prenom_ele_prec\"><img src=\"../images/arrow_left.png\" class='icone16' alt=\"$nom_prenom_ele_prec\" /></a> ";
	}
}

echo $info_eleve;

if((isset($login_ele_suiv))&&($login_ele_suiv!="")) {
	if($affichage=="semaine") {
		echo "<a href='".$_SERVER['PHP_SELF']."?login_eleve=".$login_ele_suiv."&amp;affichage=semaine&amp;num_semaine_annee=$num_semaine_annee' title=\"Voir la page pour $nom_prenom_ele_suiv\"><img src=\"../images/arrow_right.png\" class='icone16' alt=\"$nom_prenom_ele_suiv\" /></a> ";
	}
	else {
		echo " <a href='".$_SERVER['PHP_SELF']."?login_eleve=".$login_ele_suiv."&amp;display_date=$display_date&amp;affichage=jour' title=\"Voir la page pour $nom_prenom_ele_suiv\"><img src=\"../images/arrow_right.png\" class='icone16' alt=\"$nom_prenom_ele_suiv\" /></a>";
	}
}

echo "</p>";
/*
echo "\$tab_jour<pre>";
print_r($tab_jour);
echo "</pre>";
*/

//============================================================
// Formulaire de choix de la semaine

$selected_semaine="";
$selected_lundi="";
$selected_mardi="";
$selected_mercredi="";
$selected_jeudi="";
$selected_vendredi="";
$selected_samedi="";
$selected_dimanche="";
if(in_array($affichage, array("1", "2", "3", "4", "5", "6", "7"))) {
	$tab_jours_aff=array($affichage);
	if($affichage==1) {
		$selected_lundi=" selected";
	}
	elseif($affichage==2) {
		$selected_mardi=" selected";
	}
	elseif($affichage==3) {
		$selected_mercredi=" selected";
	}
	elseif($affichage==4) {
		$selected_jeudi=" selected";
	}
	elseif($affichage==5) {
		$selected_vendredi=" selected";
	}
	elseif($affichage==6) {
		$selected_samedi=" selected";
	}
	elseif($affichage==7) {
		$selected_dimanche=" selected";
	}
}
else {
	// Affichage semaine
	$tab_jours_aff=array();

	if(in_array("lundi", $tab_jour)) {
		$tab_jours_aff[]=1;
	}
	if(in_array("mardi", $tab_jour)) {
		$tab_jours_aff[]=2;
	}
	if(in_array("mercredi", $tab_jour)) {
		$tab_jours_aff[]=3;
	}
	if(in_array("jeudi", $tab_jour)) {
		$tab_jours_aff[]=4;
	}
	if(in_array("vendredi", $tab_jour)) {
		$tab_jours_aff[]=5;
	}
	if(in_array("samedi", $tab_jour)) {
		$tab_jours_aff[]=6;
	}
	if(in_array("dimanche", $tab_jour)) {
		$tab_jours_aff[]=7;
	}

	$selected_semaine=" selected";
}

/*
echo "\$tab_jours_aff<pre>";
print_r($tab_jours_aff);
echo "</pre>";
*/

echo "
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='login_eleve' value=\"$login_eleve\" />
		<p>
			Semaine choisie&nbsp;: <select name='num_semaine_annee'>
				<option value=''></option>";

if(strftime("%m")>=8) {
	$annee=strftime("%Y");
}
else {
	$annee=strftime("%Y")-1;
}
for($n=36;$n<52;$n++) {
	$tmp_tab=get_days_from_week_number($n ,$annee);

	$selected="";
	if("$n|$annee"==$num_semaine_annee) {
		$selected=" selected='selected'";
	}

	echo "
				<option value='$n|$annee'$selected>Semaine n° $n   - (du ".$tmp_tab['num_jour'][1]['jjmmaaaa']." au ".$tmp_tab['num_jour'][7]['jjmmaaaa'].")</option>";
}
$annee++;
for($n=1;$n<28;$n++) {
	$m=(($n<10) ? "0".$n : $n);
	$tmp_tab=get_days_from_week_number($m ,$annee);

	$selected="";
	if("$m|$annee"==$num_semaine_annee) {
		$selected=" selected='selected'";
	}
	echo "
				<option value='".$m."|$annee'$selected>Semaine n° $m   - (du ".$tmp_tab['num_jour'][1]['jjmmaaaa']." au ".$tmp_tab['num_jour'][7]['jjmmaaaa'].")</option>";
}

echo "
			</select><br />

			Afficher <select name='affichage'>
				<option value='semaine'>semaine</option>";
if(in_array("lundi" , $tab_jour)) {
	echo "
				<option value='1'$selected_lundi>lundi</option>";
}
if(in_array("mardi" , $tab_jour)) {
	echo "
				<option value='2'$selected_mardi>mardi</option>";
}
if(in_array("mercredi" , $tab_jour)) {
	echo "
				<option value='3'$selected_mercredi>mercredi</option>";
}
if(in_array("jeudi" , $tab_jour)) {
	echo "
				<option value='4'$selected_jeudi>jeudi</option>";
}
if(in_array("vendredi" , $tab_jour)) {
	echo "
				<option value='5'$selected_vendredi>vendredi</option>";
}
if(in_array("samedi" , $tab_jour)) {
	echo "
				<option value='6'$selected_samedi>samedi</option>";
}
if(in_array("dimanche" , $tab_jour)) {
	echo "
				<option value='7'$selected_dimanche>dimanche</option>";
}
echo "

			</select>
			
			<input type='submit' id='input_submit' value='Valider' />
		</p>

	</fieldset>
</form>";
//============================================================

if($affichage=="semaine") {
	$largeur_edt=800;
}
else {
	$largeur_edt=114;
}

$x0=50;
$y0=10;
$hauteur_une_heure=60;

$html=affiche_edt2_eleve($login_eleve, $id_classe, $ts_display_date, $affichage, $x0, $y0, $largeur_edt, $hauteur_une_heure);

$x1=10;
//$y1=150;
$y1=212;
$marge_droite=5;
$largeur1=$largeur_edt+2*35;
$hauteur1=$hauteur_jour+$hauteur_entete+6;
$hauteur_div_sous_bandeau=20;
// border:1px solid black; background-color:".$tab_couleur_onglet['edt'].";
echo "<div id='div_edt' style='position:absolute; top:".$y1."px; left:".$x1."px; width:".$largeur1."px; height:".$hauteur1."px; margin-right:".$marge_droite."px; margin-bottom:".$marge_droite."px;'>".$html."</div>";

/*
echo "<div style='clear:both'></div>";

// Pour éviter une erreur:
$hauteur_jour=800;
$unite_div_infobulle="px";
$titre_infobulle="EDT";
$texte_infobulle=$html;
$tabdiv_infobulle[]=creer_div_infobulle('edt_test',$titre_infobulle,"",$texte_infobulle,"","1000",($hauteur_jour+200),'y','y','n','n');

echo "<p><a href=\"javascript:afficher_div('edt_test','y',-10,20)\">Afficher en infobulle l'EDT choisi</a></p>";
*/
echo "<script type='text/javascript'>
	// Action lancée lors du clic dans le div_edt
	function action_edt_cours(id_cours) {
		// Actuellement, aucune action n'est lancée.
		// La fonction est là pour éviter une erreur JavaScript
	}

	// Adaptation de l'emplacement vertical du div_edt
	function check_top_div_edt() {
		var temoin_petit_bandeau=0;
		var liste_pt_bandeau=document.getElementsByClassName('pt_bandeau');
		for(i=0;i<liste_pt_bandeau.length;i++) {
			id=liste_pt_bandeau[i].getAttribute('id');
			if(id=='bandeau') {
				document.getElementById('div_edt').style.top=150+'px';
				temoin_petit_bandeau++;
				break;
			}
		}
		if(temoin_petit_bandeau==0) {
			document.getElementById('div_edt').style.top=212+'px';
		}

		setTimeout('check_top_div_edt()',1000);
	}

	check_top_div_edt();
</script>";

require("../lib/footer.inc.php");
?>
