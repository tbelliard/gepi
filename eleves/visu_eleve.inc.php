<?php

/*
 *
 * Copyright 2001, 2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Stephane Boireau
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

if(($_SERVER['SCRIPT_NAME']!="$gepiPath/eleves/visu_eleve.php")&&
($_SERVER['SCRIPT_NAME']!="$gepiPath/mod_abs2/fiche_eleve.php")) {
	echo "<p style='color:red'>Inclusion non autorisée depuis ".$_SERVER['SCRIPT_NAME']."</p>\n";
	require_once("../lib/footer.inc.php");
	die();
}

//debug_var();

$Recherche_sans_js=isset($_POST['Recherche_sans_js']) ? $_POST['Recherche_sans_js'] : (isset($_GET['Recherche_sans_js']) ? $_GET['Recherche_sans_js'] : NULL);

if((!isset($ele_login))&&(!isset($Recherche_sans_js))) {
	echo "<div class='norme'>\n";
	echo "<p class='bold'>\n";
	if((isset($quitter_la_page))&&($quitter_la_page=='y')) {
		echo "<a href='visu_eleve.php' onClick='self.close();return false;'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Refermer la page </a>\n";
	}
	else {
		echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
	}
	echo "</p>\n";
	echo "</div>\n";

	//=============================================
	// Formulaire pour navigateur SANS Javascript:
	echo "<noscript>
	<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire1'>
		<p>
		Afficher les ".$gepiSettings['denomination_eleves']." dont le <strong>nom</strong> contient&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type='text' name='rech_nom' value='' />
		<input type='hidden' name='page' value='$page' />
		<input type='submit' name='Recherche_sans_js' value='Rechercher' />
		$champ_quitter_page_ou_non
		</p>
	</form>

	<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire1'>
		<p>
		Afficher les ".$gepiSettings['denomination_eleves']." dont le <strong>prénom</strong> contient&nbsp;: <input type='text' name='rech_prenom' value='' />
		<input type='hidden' name='page' value='$page' />
		<input type='submit' name='Recherche_sans_js' value='Rechercher' />
		$champ_quitter_page_ou_non
		</p>
	</form>

</noscript>\n";
	//=============================================

	// Portion d'AJAX:
	echo "<script type='text/javascript'>

	function cherche_eleves(type) {
		rech_nom_ou_prenom=document.getElementById('rech_'+type).value;

		//var url = 'liste_eleves.php';
		var url = '../eleves/liste_eleves.php';
		var myAjax = new Ajax.Request(
			url,
			{
				method: 'post',
				postBody: 'rech_'+type+'='+rech_nom_ou_prenom+'&page=$page',
				onComplete: affiche_eleves
			});

	}

	function affiche_eleves(xhr) {
		if (xhr.status == 200) {
			document.getElementById('liste_eleves').innerHTML = xhr.responseText;
		}
		else {
			document.getElementById('liste_eleves').innerHTML = xhr.status;
		}
	}

	function affichage_et_action(type) {
		if(document.getElementById('rech_'+type).value=='') {
			document.getElementById('Recherche_'+type).style.display='none';
		}
		else {
			document.getElementById('Recherche_'+type).style.display='';
			cherche_eleves(type);
		}
	}

	function affichage_sans_action(type) {
		if(document.getElementById('rech_'+type).value=='') {
			document.getElementById('Recherche_'+type).style.display='none';
		}
		else {
			document.getElementById('Recherche_'+type).style.display='';
		}
	}

	/*
	function cherche_eleves(type) {
		rech_nom_ou_prenom=document.getElementById('rech_'+type).value;

		new Ajax.Updater($('liste_eleves'),'../eleves/liste_eleves.php?rech_'+type+'='+rech_nom_ou_prenom+'&page=$page',{method: 'get'});
	}
	*/
</script>\n";


	// DIV avec formulaire pour navigateur AVEC Javascript:
	echo "<div id='recherche_avec_js' style='display:none;'>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' onsubmit=\"cherche_eleves('nom');return false;\" method='post' name='formulaire'>";
	echo "<p>\n";
	//echo "Afficher les ".$gepiSettings['denomination_eleves']." dont le <strong>nom</strong> contient&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type='text' name='rech_nom' id='rech_nom' value='".(isset($_SESSION['rech_nom']) ? $_SESSION['rech_nom'] : "")."' onchange=\"affichage_et_action('nom')\" />\n";
	echo "Afficher les ".$gepiSettings['denomination_eleves']." dont le <strong>nom</strong> contient&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type='text' name='rech_nom' id='rech_nom' value='' onkeyup=\"affichage_sans_action('nom')\" onchange=\"affichage_et_action('nom')\" />\n";
	echo "<input type='hidden' name='page' value='$page' />\n";
	echo "<input type='button' name='Recherche' id='Recherche_nom' value='Rechercher' onclick=\"cherche_eleves('nom')\" />\n";
	//echo "<a href=\"#\" onclick=\"document.getElementById('rech_nom').value=''; return false;\" title='Vider le critère de recherche sur le nom.'><img src='../images/icons/balai.png' class='icone16' alt='Vider' /></a>";
	echo $champ_quitter_page_ou_non;
	echo "</p>\n";
	echo "</form>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' onsubmit=\"cherche_eleves('prenom');return false;\" method='post' name='formulaire'>";
	echo "<p>\n";
	//echo "Afficher les ".$gepiSettings['denomination_eleves']." dont le <strong>prénom</strong> contient&nbsp;: <input type='text' name='rech_prenom' id='rech_prenom' value='".(isset($_SESSION['rech_prenom']) ? $_SESSION['rech_prenom'] : "")."' onchange=\"affichage_et_action('prenom')\" />\n";
	echo "Afficher les ".$gepiSettings['denomination_eleves']." dont le <strong>prénom</strong> contient&nbsp;: <input type='text' name='rech_prenom' id='rech_prenom' value='' onkeyup=\"affichage_sans_action('prenom')\" onchange=\"affichage_et_action('prenom')\" />\n";
	echo "<input type='hidden' name='page' value='$page' />\n";
	echo "<input type='button' name='Recherche' id='Recherche_prenom' value='Rechercher' onclick=\"cherche_eleves('prenom')\" />\n";
	//echo "<a href=\"#\" onclick=\"document.getElementById('rech_prenom').value=''; return false;\" title='Vider le critère de recherche sur le prénom.'><img src='../images/icons/balai.png' class='icone16' alt='Vider' /></a>";
	echo $champ_quitter_page_ou_non;
	echo "</p>\n";
	echo "</form>\n";

	echo "<div id='liste_eleves'></div>\n";

	echo "</div>\n";
	echo "<script type='text/javascript'>
document.getElementById('recherche_avec_js').style.display='';
affichage_et_action('nom');
affichage_et_action('prenom');

if(document.getElementById('rech_nom')) {document.getElementById('rech_nom').focus();}
</script>\n";


	if(isset($id_classe)) {
		$sql="SELECT DISTINCT e.login,e.nom,e.prenom FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe' ORDER BY e.nom,e.prenom;";
		//echo "$sql<br />";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele)>0) {
			echo "<a name='classe'></a><p class='bold'>".casse_mot($gepiSettings['denomination_eleves'], 'majf2')." de la classe de ".get_class_from_id($id_classe).":</p>\n";

			$tab_txt=array();
			$tab_lien=array();

			while($lig_ele=mysqli_fetch_object($res_ele)) {
				//$tab_txt[]=casse_mot($lig_ele->prenom,'majf2')." ".my_strtoupper($lig_ele->nom);
				$tab_txt[]=my_strtoupper($lig_ele->nom)." ".casse_mot($lig_ele->prenom,'majf2');
				$tab_lien[]=$_SERVER['PHP_SELF']."?ele_login=".$lig_ele->login."&amp;id_classe=".$id_classe;
			}

			echo "<blockquote>\n";
			tab_liste($tab_txt,$tab_lien,3);
			echo "</blockquote>\n";

		}
	}

	if($_SESSION['statut']=='scolarite') {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur') {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc,j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgc.id_groupe=jgp.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
	}
	elseif(($_SESSION['statut']=='cpe')&&
		(getSettingAOui('GepiAccesReleveCpeTousEleves'))||
		(getSettingAOui('GepiRubConseilCpeTous'))||
		(getSettingAOui('GepiAccesCdtCpe'))||
		(getSettingAOui('AACpeTout'))||
		(getSettingAOui('GepiAccesTouteFicheEleveCpe'))||
		(getSettingAOui('GepiAccesAbsTouteClasseCpe'))
	) {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	elseif($_SESSION['statut']=='cpe') {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe=c.id ORDER BY c.classe";
	}
	elseif(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='secours')) {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	elseif($_SESSION['statut'] == 'autre'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	//echo "$sql<br />";
	$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_clas)>0) {
		echo "<p>Ou choisir un ".$gepiSettings['denomination_eleve']." dans une classe:</p>\n";

		$tab_txt=array();
		$tab_lien=array();

		while($lig_clas=mysqli_fetch_object($res_clas)) {
			$tab_txt[]=$lig_clas->classe;
			$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id."#classe";
		}

		echo "<blockquote>\n";
		tab_liste($tab_txt,$tab_lien,4);
		echo "</blockquote>\n";
	}

	//=============================================

}
elseif(isset($Recherche_sans_js)) {
	// On ne passe ici que si JavaScript est désactivé
	echo "<div class='norme'><p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
	echo " | <a href='".$_SERVER['PHP_SELF'];
	if($chaine_quitter_page_ou_non!="") {
		echo "?a=a$chaine_quitter_page_ou_non";
	}
	echo "'>Choisir un autre ".$gepiSettings['denomination_eleve']."</a>\n";
	echo "</p>\n";
	echo "</div>\n";

	include("../eleves/recherche_eleve.php");
}
else {
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
	echo $champ_quitter_page_ou_non;

	//echo "<div class='norme'><p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
	echo "<div class='norme'>\n";
	echo "<p class='bold'>\n";
	if((isset($quitter_la_page))&&($quitter_la_page=='y')) {
		echo "<a href='visu_eleve.php' onClick='self.close();return false;'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Refermer la page </a>\n";
	}
	else {
		echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
	}

	echo " | <a href='".$_SERVER['PHP_SELF'];
	if($chaine_quitter_page_ou_non!="") {
		echo "?a=a$chaine_quitter_page_ou_non";
	}
	echo "'>Choisir un autre ".$gepiSettings['denomination_eleve']."/classe</a>\n";

	if(!isset($id_classe)) {
		$sql="SELECT id_classe FROM j_eleves_classes WHERE login='$ele_login' ORDER BY periode DESC;";
		$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_class_tmp)>0){
			$lig_class_tmp=mysqli_fetch_object($res_class_tmp);
			$id_classe=$lig_class_tmp->id_classe;
		}
	}

	if(isset($id_classe)) {

		$sql="SELECT DISTINCT e.nom,e.prenom,e.login FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe' ORDER BY e.nom, e.prenom;";

		$chaine_options_eleves="";

		$res_ele_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele_tmp)>0){
			$ele_login_prec="";
			$ele_login_suiv="";
			$temoin_tmp=0;
			while($lig_ele_tmp=mysqli_fetch_object($res_ele_tmp)) {
				if($lig_ele_tmp->login==$ele_login) {
					$chaine_options_eleves.="<option value='$lig_ele_tmp->login' selected='true'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
					$temoin_tmp=1;
					if($lig_ele_tmp=mysqli_fetch_object($res_ele_tmp)) {
						$chaine_options_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
						$ele_login_suiv=$lig_ele_tmp->login;
					}
					else {
						$ele_login_suiv="";
					}
				}
				else {
					$chaine_options_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
				}
				if($temoin_tmp==0) {
					$ele_login_prec=$lig_ele_tmp->login;
				}
			}
		}
		// =================================

		// Initialisation
		if(!isset($onglet)) {
			$onglet="eleve";
		}

		echo "<span id='champ_select_classe' style='display:none'> : ".champ_select_classe($_SESSION['login'], $_SESSION['statut'], 'select_id_classe', 'select_id_classe', $id_classe, "y", "change_classe_et_submit()")."</span>";

		if($ele_login_prec!=""){
			echo " | <a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login_prec&amp;id_classe=$id_classe";
			echo $chaine_quitter_page_ou_non;
			echo "'";
			echo " onclick=\"passer_a_eleve('$ele_login_prec','$id_classe');return false;\"";
			echo ">".ucfirst($gepiSettings['denomination_eleve'])." précédent</a>";
		}
		if($chaine_options_eleves!="") {
			echo " | <select name='ele_login' onchange=\"document.forms['form1'].submit();\">\n";
			echo $chaine_options_eleves;
			echo "</select>\n";
		}
		if($ele_login_suiv!=""){
			echo " | <a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login_suiv&amp;id_classe=$id_classe";
			echo $chaine_quitter_page_ou_non;
			echo "'";
			echo " onclick=\"passer_a_eleve('$ele_login_suiv','$id_classe');return false;\"";
			echo ">".ucfirst($gepiSettings['denomination_eleve'])." suivant</a>";
		}

		if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
			echo " | <a href='modify_eleve.php?eleve_login=".$ele_login."' title=\"Modifier les nom, prénom, date de naissance, régime,... de l'élève.\">Gestion de l'élève</a>";
		}

		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";

		unset($id_classe);
	}

	// Bizarre : c'est masqué par le javascript de fin de page avant même la fin du chargement des onglets ???
	echo "<span id='spinner_chargement' style='margin-left:3em; color:red; padding:0.5em;' class='fieldset_opacite50' title=\"Veuillez patienter le temps du chargement des différents onglets...\"> <img src='../images/spinner.gif' class='icone16' alt='Patientez' /> Veuillez patienter...</span>";

	echo "</p>\n";
	echo "</div>\n";

	echo "<input type='hidden' name='onglet' id='onglet_courant' value='";
	if(isset($onglet)) {echo $onglet;}
	echo "' />\n";


	echo "<input type='hidden' name='onglet2' id='onglet_bull_courant' value='";
	if(isset($onglet2)) {echo $onglet2;}
	echo "' />\n";
	echo "</form>\n";

	flush();
	ob_flush();

	echo "<form id='form_changement_classe' action='".$_SERVER['PHP_SELF']."' method='post'>
	<input type='hidden' name='id_classe' id='id_classe_form_changement_classe' value='' />
</form>\n";

	// Affichage des onglets pour l'élève choisi

	echo "<div id='patience'>
<noscript>
Patientez pendant l'extraction des données... merci.
</noscript>
</div>\n";
	// Avec ça, le message ne disparait pas quand on a désactivé JavaScript...

	echo "<script type='text/javascript'>
	document.getElementById('patience').innerHTML=\"Patientez pendant l'extraction des données... merci.\";

	// On affiche le champ si JS est actif
	if(document.getElementById('champ_select_classe')) {
		document.getElementById('champ_select_classe').style.display='';
	}

	function change_classe_et_submit() {
		id_classe=document.getElementById('select_id_classe').options[document.getElementById('select_id_classe').selectedIndex].value;
		document.getElementById('id_classe_form_changement_classe').value=id_classe;
		document.getElementById('form_changement_classe').submit();
	}

	function passer_a_eleve(ele_login,id_classe) {
		if(document.getElementById('onglet_courant')) {
			onglet=document.getElementById('onglet_courant').value;
		}
		else {
			onglet='eleve';
		}

		if(document.getElementById('onglet_bull_courant')) {
			onglet2=document.getElementById('onglet_bull_courant').value;
		}
		else {
			onglet2='';
		}

		if(onglet2=='') {
			//alert('".$_SERVER['PHP_SELF']."?id_classe='+id_classe+'&ele_login='+ele_login+'&onglet='+onglet);
			document.location.replace('".$_SERVER['PHP_SELF']."?id_classe='+id_classe+'&ele_login='+ele_login+'&onglet='+onglet);
		}
		else {
			document.location.replace('".$_SERVER['PHP_SELF']."?id_classe='+id_classe+'&ele_login='+ele_login+'&onglet='+onglet+'&onglet2='+onglet2);
		}
	}
</script>\n";

	flush();

	// Couleurs pour les onglets:
	$tab_couleur['eleve']="moccasin";
	$tab_couleur['responsables']="mintcream";
	$tab_couleur['enseignements']="whitesmoke";
	$tab_couleur['bulletins']="lightyellow";
	$tab_couleur['bulletin']="lemonchiffon";
	$tab_couleur['releves']="papayawhip";
	$tab_couleur['releve']="seashell";
	$tab_couleur['cdt']="linen";
	$tab_couleur['anna']="blanchedalmond";
	$tab_couleur['absences']="azure";
	$tab_couleur['discipline']="salmon";
	$tab_couleur['fp']="linen";

	// On vérifie que l'élève existe
	$sql="SELECT 1=1 FROM eleves WHERE login='$ele_login';";
	$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);

	if(mysqli_num_rows($res_ele)==0){
		// On ne devrait pas arriver là.
		echo "<p>L'".$gepiSettings['denomination_eleve']." dont le login serait $ele_login n'est pas dans la table 'eleves'.</p>\n";
	}
	else {
		$check_mae=check_mae($_SESSION['login']);

		if(getSettingAOui('active_mod_discipline')) {
			require("../mod_discipline/mod_discipline.lib.php");
		}


		if((isset($_GET['envoi_bulletin_resp_legal_0']))&&(($_GET['envoi_bulletin_resp_legal_0']=='y')||($_GET['envoi_bulletin_resp_legal_0']=='n'))) {
			check_token();

			$sql="UPDATE responsables2 SET envoi_bulletin='".$_GET['envoi_bulletin_resp_legal_0']."' WHERE pers_id='".$_GET['pers_id']."' AND ele_id='".$_GET['ele_id']."';";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if($update) {
				$msg="Modification de la génération ou non des bulletins pour pers_id=".$_GET['pers_id']." et ele_id=".$_GET['ele_id']." effectuée.<br />";
			}
			else {
				$msg="Erreur lors de la modification de la génération ou non des bulletins pour pers_id=".$_GET['pers_id']." et ele_id=".$_GET['ele_id']."<br />";
			}
		}


		//================================
		unset($day);
		$day = isset($_POST["day"]) ? $_POST["day"] : (isset($_GET["day"]) ? $_GET["day"] : date("d"));
		unset($month);
		$month = isset($_POST["month"]) ? $_POST["month"] : (isset($_GET["month"]) ? $_GET["month"] : date("m"));
		unset($year);
		$year = isset($_POST["year"]) ? $_POST["year"] : (isset($_GET["year"]) ? $_GET["year"] : date("Y"));
		// Vérification
		settype($month,"integer");
		settype($day,"integer");
		settype($year,"integer");
		$minyear = strftime("%Y", getSettingValue("begin_bookings"));
		$maxyear = strftime("%Y", getSettingValue("end_bookings"));
		if ($day < 1) $day = 1;
		if ($day > 31) $day = 31;
		if ($month < 1) $month = 1;
		if ($month > 12) $month = 12;
		if ($year < $minyear) $year = $minyear;
		if ($year > $maxyear) $year = $maxyear;
		# Make the date valid if day is more then number of days in month
		while (!checkdate($month, $day, $year)) $day--;
		$today=mktime(0,0,0,$month,$day,$year);
		//================================
		// Dates pour l'extraction des cahiers de textes: 1j avant et 7j après
		$date_ct1=$today-1*24*3600;
		$date_ct2=$today+7*24*3600;

		$j_sem_prec=date("d",$today-7*24*3600);
		$m_sem_prec=date("m",$today-7*24*3600);
		$y_sem_prec=date("Y",$today-7*24*3600);

		$j_sem_suiv=date("d",$today+7*24*3600);
		$m_sem_suiv=date("m",$today+7*24*3600);
		$y_sem_suiv=date("Y",$today+7*24*3600);
		//================================

		// A FAIRE:
		// Contrôler si la personne connectée a le droit de consulter les infos sur cet élève
		$acces_eleve="n";
		$acces_responsables="n";
		$acces_enseignements="n";
		$acces_releves="n";
		$acces_bulletins="n";
		$acces_anna="n";
		$acces_absences="n";
		$acces_discipline="n";
		$acces_fp="n";

		// 20190101
		$acces_tel_responsable=false;
		$acces_adresse_responsable=false;
		$acces_email_responsable=false;
		$acces_email_ele=false;
		$acces_tel_ele=false;

		$active_annees_anterieures=getSettingValue('active_annees_anterieures');

		if($_SESSION['statut']=='administrateur') {
			$acces_eleve="y";
			$acces_responsables="y";
			$acces_enseignements="y";
			$acces_releves="n";
			$acces_bulletins="y";
			$acces_absences="y";
			if($active_annees_anterieures=='y') {
				$acces_anna="y";
			}
			$acces_discipline="y";
			$acces_fp="y";

			// 20190101
			$acces_tel_responsable=true;
			$acces_adresse_responsable=true;
			$acces_email_responsable=true;

			$acces_email_ele=true;
			$acces_tel_ele=true;
		}
		elseif($_SESSION['statut']=='scolarite') {
			if (getSettingValue("GepiAccesTouteFicheEleveScolarite")!='yes') {
				$sql="SELECT 1=1 FROM j_scol_classes jsc, j_eleves_classes jec WHERE jec.id_classe=jsc.id_classe AND jsc.login='".$_SESSION['login']."' AND jec.login='".$ele_login."';";

				$test=mysqli_query($GLOBALS["mysqli"], $sql);

				if(mysqli_num_rows($test)==0) {
					echo "<p>Vous n'êtes pas responsable d'un ".$gepiSettings['denomination_eleve']." dont le login serait $ele_login.</p>\n";
					echo "<script type='text/javascript'>
						if(document.getElementById('patience')) {
							document.getElementById('patience').style.display='none';
						}

						if(document.getElementById('spinner_chargement')) {
							//setTimeout(\"document.getElementById('spinner_chargement').style.display='none'\", 2000);
							document.getElementById('spinner_chargement').style.display='none';
						}
					</script>\n";
					require_once("../lib/footer.inc.php");
					die();
				}
			}

			$acces_eleve="y";
			$acces_responsables="y";
			$acces_enseignements="y";
			$acces_absences="y";

			$acces_discipline="y";
			// Le droit n'y est pas.
			$acces_fp="n";

			// 20190101
			$acces_tel_responsable=true;
			$acces_adresse_responsable=true;
			$acces_email_responsable=true;

			$acces_email_ele=true;
			$acces_tel_ele=true;

			$GepiAccesReleveScol=getSettingValue('GepiAccesReleveScol');
			if($GepiAccesReleveScol=="yes") {
				$acces_releves="y";
			}

			if($active_annees_anterieures=='y') {
				$AAScolTout=getSettingValue('AAScolTout');
				if($AAScolTout=="yes") {
					$acces_anna="y";
				}
				else {
					$AAScolResp=getSettingValue('AAScolResp');
					if($AAScolResp=="yes") {
						$acces_anna="y";
					}
				}
			}

			$acces_bulletins="y";
		}
		elseif($_SESSION['statut']=='cpe') {
			if (getSettingValue("GepiAccesTouteFicheEleveCpe")!='yes') {
				$sql="SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='".$_SESSION['login']."' AND e_login='".$ele_login."';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);

				if(mysqli_num_rows($test)==0) {
					echo "<p>Vous n'êtes pas responsable d'un ".$gepiSettings['denomination_eleve']." dont le login serait $ele_login.</p>\n";
					echo "<script type='text/javascript'>
						if(document.getElementById('patience')) {
							document.getElementById('patience').style.display='none';
						}

						if(document.getElementById('spinner_chargement')) {
							//setTimeout(\"document.getElementById('spinner_chargement').style.display='none'\", 2000);
							document.getElementById('spinner_chargement').style.display='none';
						}
					</script>\n";
					require_once("../lib/footer.inc.php");
					die();
				}
			}

			$acces_eleve="y";
			$acces_responsables="y";
			$acces_enseignements="y";
			$acces_absences="y";

			$acces_discipline="y";
			$acces_fp="y";

			// 20190101
			$acces_tel_responsable=true;
			$acces_adresse_responsable=true;
			$acces_email_responsable=true;

			$acces_email_ele=true;
			$acces_tel_ele=true;

			$GepiAccesReleveCpeTousEleves=getSettingValue('GepiAccesReleveCpeTousEleves');
			$GepiAccesReleveCpe=getSettingValue('GepiAccesReleveCpe');
			if($GepiAccesReleveCpeTousEleves=='yes') {
				$acces_releves="y";
			}
			elseif($GepiAccesReleveCpe=="yes") {
				$sql="SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='".$_SESSION['login']."' AND e_login='".$ele_login."';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);

				if(mysqli_num_rows($test)>0) {
					$acces_releves="y";
				}
			}

			if($active_annees_anterieures=='y') {
				$AACpeTout=getSettingValue('AACpeTout');
				if($AACpeTout=="yes") {
					$acces_anna="y";
				}
				else {
					$AACpeResp=getSettingValue('AACpeResp');
					if($AACpeResp=="yes") {
						$acces_anna="y";
					}
				}
			}

			$acces_bulletins="y";
		}
		elseif($_SESSION['statut']=='professeur') {

			$acces_eleve="y";
			$acces_responsables="n";
			$acces_enseignements="y";
			$acces_releves="n";
			$acces_bulletins="n";
			$acces_absences="n";

			$acces_discipline="n";
			$acces_fp="y";

			$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE login='".$ele_login."' AND professeur='".$_SESSION['login']."';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($test)>0) {
				$is_pp="y";
				$acces_absences="y";
			}
			else {
				$is_pp="n";
			}

			// Contrôle de l'accès à l'onglet Responsables
			$GepiAccesGestElevesProf=getSettingValue('GepiAccesGestElevesProf');
			if($GepiAccesGestElevesProf=="yes") {
				$acces_responsables="y";
			}
			else {
				$GepiAccesGestElevesProfP=getSettingValue('GepiAccesGestElevesProfP');
				if(($GepiAccesGestElevesProfP=="yes")&&($is_pp=="y")) {
					$acces_responsables="y";
				}
			}

			// Contrôle de l'accès du prof au relevé de notes:
			$GepiAccesReleveProfP=getSettingValue('GepiAccesReleveProfP');
			if(($GepiAccesReleveProfP=="yes")&&($is_pp=="y")) {
				$acces_releves="y";
			}

			$eleve_classe_prof="n";
			$eleve_groupe_prof="n";

			//=====================================
			$sql="SELECT 1=1 FROM j_eleves_classes jec,
								j_groupes_classes jgc,
								j_groupes_professeurs jgp
							WHERE jec.login='".$ele_login."' AND
									jec.id_classe=jgc.id_classe AND
									jgc.id_groupe=jgp.id_groupe AND
									jgp.login='".$_SESSION['login']."';";
			//echo "$sql<br />";
			$test_eleve_classe_prof=mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($test_eleve_classe_prof)>0) {
				$eleve_classe_prof="y";
			}
			//=====================================
			$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
								j_groupes_professeurs jgp
							WHERE jeg.login='".$ele_login."' AND
									jeg.id_groupe=jgp.id_groupe AND
									jgp.login='".$_SESSION['login']."';";
			//echo "$sql<br />";
			$test_eleve_groupe_prof=mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($test_eleve_groupe_prof)>0) {
				$eleve_groupe_prof="y";
			}
			//=====================================

			if($acces_releves=='n') {
				$GepiAccesReleveProfToutesClasses=getSettingValue('GepiAccesReleveProfToutesClasses');
				if($GepiAccesReleveProfToutesClasses=='yes') {
					$acces_releves="y";
				}
				else {
					$GepiAccesReleveProfTousEleves=getSettingValue('GepiAccesReleveProfTousEleves');
					if($GepiAccesReleveProfTousEleves=='yes') {
						
						if($eleve_classe_prof=='y') {
							$acces_releves="y";
							//$eleve_classe_prof="y";
						}
					}

					if($acces_releves=='n') {
						$GepiAccesReleveProf=getSettingValue('GepiAccesReleveProf');
						if($GepiAccesReleveProf=='yes') {

							if($eleve_groupe_prof=='y') {
								$acces_releves="y";
							}
						}
					}
				}
			}

			if(($eleve_classe_prof=="y")||($eleve_groupe_prof=="y")) {
				$acces_absences="y";
			}

			if((($eleve_classe_prof=="y")&&(mb_substr(getSettingValue('visuDiscProfClasses'),0,1)=='y'))||
				(($eleve_groupe_prof=="y")&&(mb_substr(getSettingValue('visuDiscProfGroupes'),0,1)=='y'))) {
				$acces_discipline="y";
			}

			// Contrôle de l'accès du prof aux bulletins:
			$GepiAccesBulletinSimplePP=getSettingValue('GepiAccesBulletinSimplePP');
			if(($GepiAccesBulletinSimplePP=="yes")&&($is_pp=="y")) {
				$acces_bulletins="y";
			}

			if($acces_bulletins=='n') {
				$GepiAccesBulletinSimpleProfToutesClasses=getSettingValue('GepiAccesBulletinSimpleProfToutesClasses');
				if($GepiAccesBulletinSimpleProfToutesClasses=='yes') {
					$acces_bulletins="y";
				}
				else {
					$GepiAccesBulletinSimpleProfTousEleves=getSettingValue('GepiAccesBulletinSimpleProfTousEleves');
					if($GepiAccesBulletinSimpleProfTousEleves=='yes') {
						if ($eleve_classe_prof=="y") {
							$acces_bulletins="y";
						}
						else {
							if($eleve_classe_prof=='y') {
								$acces_bulletins="y";
							}
						}
					}

					if($acces_bulletins=='n') {
						$GepiAccesBulletinSimpleProf=getSettingValue('GepiAccesBulletinSimpleProf');
						if($GepiAccesBulletinSimpleProf=='yes') {
							if ($eleve_groupe_prof=="y") {
								$acces_bulletins="y";
							}
							else {
								if($eleve_groupe_prof=='y') {
									$acces_bulletins="y";
								}
							}
						}
					}
				}
			}

			if($active_annees_anterieures=='y') {

				$AAProfTout=getSettingValue('AAProfTout');
				if($AAProfTout=='yes') {
					$acces_anna="y";
				}
				else {
					$AAProfClasses=getSettingValue('AAProfClasses');
					if($AAProfClasses=='yes') {
						if ($eleve_classe_prof=="y") {
							$acces_anna="y";
						}
						else {
							if($eleve_classe_prof=='y') {
								$acces_anna="y";
							}
						}
					}
					else {
						$AAProfGroupes=getSettingValue('AAProfGroupes');
						if($AAProfGroupes=='yes') {
							if ($eleve_groupe_prof=="y") {
								$acces_anna="y";
							}
							else {
								if($eleve_groupe_prof=='y') {
									$acces_anna="y";
								}
							}
						}
					}
				}
				if(($acces_anna!="y")&&(getSettingAOui('AAProfPrinc'))&&(is_pp($_SESSION['login'], '', $ele_login))) {
					$acces_anna="y";
				}
			}

			// 20190101
			if(getSettingAOui('GepiAccesAdresseTousParentsProf')) {
				$acces_adresse_responsable=true;
			}
			if(getSettingAOui('GepiAccesTelTousParentsProf')) {
				$acces_tel_responsable=true;
			}
			if(getSettingAOui('GepiAccesMailTousParentsProf')) {
				$acces_email_responsable=true;
			}

			if(($eleve_classe_prof=="y")||($eleve_groupe_prof=="y")) {
				if(getSettingAOui('GepiAccesGestElevesProf')) {
					$acces_adresse_responsable=true;
				}

				if(getSettingAOui('GepiAccesAdresseParentsRespProf')) {
					$acces_adresse_responsable=true;
				}

				if(getSettingAOui('GepiAccesTelParentsRespProf')) {
					$acces_tel_responsable=true;
				}

				if(getSettingAOui('GepiAccesMailParentsRespProf')) {
					$acces_email_responsable=true;
				}
			}

			if((is_pp($_SESSION['login'], '', $ele_login))) {
				if(getSettingAOui('GepiAccesGestElevesProfP')) {
					$acces_adresse_responsable=true;
				}

				if(getSettingAOui('GepiAccesAdresseParentsRespPP')) {
					$acces_adresse_responsable=true;
				}

				if(getSettingAOui('GepiAccesTelParentsRespPP')) {
					$acces_tel_responsable=true;
				}

				if(getSettingAOui('GepiAccesMailParentsRespPP')) {
					$acces_email_responsable=true;
				}
			}

			$acces_email_ele=get_acces_mail_ele($ele_login);
			$acces_tel_ele=get_acces_tel_ele($ele_login);

		}
		elseif($_SESSION['statut'] == 'autre'){

			// On récupère les droits de ce statuts pour savoir ce qu'on peut afficher
			$sql_d = "SELECT * FROM droits_speciaux WHERE id_statut = '" . $_SESSION['statut_special_id'] . "'";
			$query_d = mysqli_query($GLOBALS["mysqli"], $sql_d);
			$auth_other = array();

			// 20190101 : Droits à gérer/éplucher
			$acces_tel_responsable=false;
			$acces_adresse_responsable=false;
			$acces_email_responsable=false;

			while($rep_d = mysqli_fetch_array($query_d)){

				//print_r($rep_d);
				if ($rep_d['nom_fichier'] == '/voir_resp' AND $rep_d['autorisation'] == 'V') {
					$acces_responsables = "y";
				}
				if ($rep_d['nom_fichier'] == '/voir_ens' AND $rep_d['autorisation'] == 'V') {
					$acces_enseignements = "y";
				}
				if ($rep_d['nom_fichier'] == '/voir_notes' AND $rep_d['autorisation'] == 'V') {
					$acces_releves = "y";
				}if ($rep_d['nom_fichier'] == '/voir_bulle' AND $rep_d['autorisation'] == 'V') {
					$acces_bulletins = "y";
				}
				if ($rep_d['nom_fichier'] == '/voir_abs' AND $rep_d['autorisation'] == 'V') {
					$acces_absences = "y";
				}
				if ($rep_d['nom_fichier'] == '/voir_anna' AND $rep_d['autorisation'] == 'V') {
					$acces_anna = "y";
				}

				if ($rep_d['nom_fichier'] == '/mod_discipline/saisie_incident.php' AND $rep_d['autorisation'] == 'V') {
					$acces_discipline="y";
				}

				if ($rep_d['nom_fichier'] == '/AccesAdresseParents' AND $rep_d['autorisation'] == 'V') {
					$acces_adresse_responsable=true;
				}
				if ($rep_d['nom_fichier'] == '/AccesTelParents' AND $rep_d['autorisation'] == 'V') {
					$acces_tel_responsable=true;
				}
				if ($rep_d['nom_fichier'] == '/AccesMailParents' AND $rep_d['autorisation'] == 'V') {
					$acces_email_responsable=true;
				}
				if ($rep_d['nom_fichier'] == '/AccesTelEleves' AND $rep_d['autorisation'] == 'V') {
					$acces_tel_ele=true;
				}
				if ($rep_d['nom_fichier'] == '/AccesMailEleves' AND $rep_d['autorisation'] == 'V') {
					$acces_email_ele=true;
				}

			}

			// A GERER $acces_discipline="y";

		}

		// 20210112
		if(!getSettingAOui('active_bulletins')) {
			$acces_bulletins='n';
		}

		// A REVOIR par la suite
		$active_cahiers_texte=getSettingValue("active_cahiers_texte");
		if($active_cahiers_texte=='y') {
			$acces_cdt="n";

			if(acces_cdt_eleve($_SESSION['login'], $ele_login)) {
				$acces_cdt="y";
			}
		}
		else {
			$acces_cdt="n";
		}

		$test_outils_comp = sql_query1("select count(outils_complementaires) from aid_config where outils_complementaires='y'");
		if ($test_outils_comp != 0) {
			$acces_global_fp="y";
		}
		else {
			$acces_global_fp="n";
		}

		$active_mod_discipline=getSettingValue("active_mod_discipline");
		if($active_mod_discipline!='y') {
			$acces_discipline="n";
		}

		//===========================================
		// Extraction de quelques données sur l'établissement
		$RneEtablissement=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
		$gepiSchoolName=getSettingValue("gepiSchoolName") ? getSettingValue("gepiSchoolName") : "gepiSchoolName";
		$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1") ? getSettingValue("gepiSchoolAdress1") : "";
		$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2") ? getSettingValue("gepiSchoolAdress2") : "";
		$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode") ? getSettingValue("gepiSchoolZipCode") : "";
		$gepiSchoolCity=getSettingValue("gepiSchoolCity") ? getSettingValue("gepiSchoolCity") : "";
		$gepiSchoolPays=getSettingValue("gepiSchoolPays") ? getSettingValue("gepiSchoolPays") : "";
		$gepiYear = getSettingValue("gepiYear");


		// Photo si module trombino actif
		$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes");
		$photo_largeur_max=150;
		$photo_hauteur_max=150;

		// Lieu de naissance (peut ne pas être activé):
		$ele_lieu_naissance=getSettingValue("ele_lieu_naissance") ? getSettingValue("ele_lieu_naissance") : "n";

		//===========================================
		// Initialisations concernant les relevés de notes
		$p_releve_margin=getSettingValue("p_releve_margin") ? getSettingValue("p_releve_margin") : "";
		$releve_textsize=getSettingValue("releve_textsize") ? getSettingValue("releve_textsize") : 10;
		$releve_titlesize=getSettingValue("releve_titlesize") ? getSettingValue("releve_titlesize") : 16;

		$active_cahiers_texte=getSettingValue("active_cahiers_texte") ? getSettingValue("active_cahiers_texte") : "n";

		// Récupération des variables du bloc adresses:
		// Liste de récupération à extraire de la boucle élèves pour limiter le nombre de requêtes... A FAIRE
		// Il y a d'autres récupération de largeur et de positionnement du bloc adresse à extraire...
		// PROPORTION 30%/70% POUR LE 1er TABLEAU ET ...
		$releve_addressblock_logo_etab_prop=getSettingValue("releve_addressblock_logo_etab_prop") ? getSettingValue("releve_addressblock_logo_etab_prop") : 40;
		$releve_addressblock_autre_prop=100-$releve_addressblock_logo_etab_prop;

		// Taille des polices sur le bloc adresse:
		$releve_addressblock_font_size=getSettingValue("releve_addressblock_font_size") ? getSettingValue("releve_addressblock_font_size") : 12;

		// Taille de la cellule Classe et Année scolaire sur le bloc adresse:
		$releve_addressblock_classe_annee=getSettingValue("releve_addressblock_classe_annee") ? getSettingValue("releve_addressblock_classe_annee") : 35;
		// Calcul du pourcentage par rapport au tableau contenant le bloc Classe, Année,...
		$releve_addressblock_classe_annee2=round(100*$releve_addressblock_classe_annee/(100-$releve_addressblock_logo_etab_prop));

		// Débug sur l'entête pour afficher les cadres
		$releve_addressblock_debug=getSettingValue("releve_addressblock_debug") ? getSettingValue("releve_addressblock_debug") : "n";

		// Nombre de sauts de lignes entre le tableau logo+etab et le nom, prénom,... de l'élève
		$releve_ecart_bloc_nom=getSettingValue("releve_ecart_bloc_nom") ? getSettingValue("releve_ecart_bloc_nom") : 0;

		// Afficher l'établissement d'origine de l'élève:
		$releve_affiche_etab=getSettingValue("releve_affiche_etab") ? getSettingValue("releve_affiche_etab") : "n";

		// Bordure classique ou trait-noir:
		$releve_bordure_classique=getSettingValue("releve_bordure_classique") ? getSettingValue("releve_bordure_classique") : "y";
		if($releve_bordure_classique!="y"){
			$releve_class_bordure=" class='uneligne' ";
		}
		else{
			$releve_class_bordure="";
		}

		$releve_addressblock_length=getSettingValue("releve_addressblock_length") ? getSettingValue("releve_addressblock_length") : 6;
		$releve_addressblock_padding_top=getSettingValue("releve_addressblock_padding_top") ? getSettingValue("releve_addressblock_padding_top") : 0;
		$releve_addressblock_padding_text=getSettingValue("releve_addressblock_padding_text") ? getSettingValue("releve_addressblock_padding_text") : 0;
		$releve_addressblock_padding_right=getSettingValue("releve_addressblock_padding_right") ? getSettingValue("releve_addressblock_padding_right") : 0;



		// Affichage ou non du nom et de l'adresse de l'établissement
		$releve_affich_nom_etab=getSettingValue("releve_affich_nom_etab") ? getSettingValue("releve_affich_nom_etab") : "y";
		$releve_affich_adr_etab=getSettingValue("releve_affich_adr_etab") ? getSettingValue("releve_affich_adr_etab") : "y";
		if(($releve_affich_nom_etab!="n")&&($releve_affich_nom_etab!="y")) {$releve_affich_nom_etab="y";}
		if(($releve_affich_adr_etab!="n")&&($releve_affich_adr_etab!="y")) {$releve_affich_adr_etab="y";}

		$releve_ecart_entete=getSettingValue("releve_ecart_entete") ? getSettingValue("releve_ecart_entete") : 0;


		$releve_mention_doublant=getSettingValue("releve_mention_doublant") ? getSettingValue("releve_mention_doublant") : "n";


		$releve_cellspacing=getSettingValue("releve_cellspacing") ? getSettingValue("releve_cellspacing") : 2;
		$releve_cellpadding=getSettingValue("releve_cellpadding") ? getSettingValue("releve_cellpadding") : 5;


		$releve_affiche_numero=getSettingValue("releve_affiche_numero") ? getSettingValue("releve_affiche_numero") : "n";


		$releve_affiche_signature=getSettingValue("releve_affiche_signature") ? getSettingValue("releve_affiche_signature") : "y";

		$releve_affiche_formule=getSettingValue("releve_affiche_formule") ? getSettingValue("releve_affiche_formule") : "n";
		$releve_formule_bas=getSettingValue("releve_formule_bas") ? getSettingValue("releve_formule_bas") : "Relevé à conserver précieusement. Aucun duplicata ne sera délivré. - GEPI : solution libre de gestion et de suivi des résultats scolaires.";


		$releve_col_hauteur=getSettingValue("releve_col_hauteur") ? getSettingValue("releve_col_hauteur") : 0;
		$releve_largeurtableau=getSettingValue("releve_largeurtableau") ? getSettingValue("releve_largeurtableau") : 800;
		$releve_col_matiere_largeur=getSettingValue("releve_col_matiere_largeur") ? getSettingValue("releve_col_matiere_largeur") : 150;

		$gepi_prof_suivi=getSettingValue("gepi_prof_suivi") ? getSettingValue("gepi_prof_suivi") : "professeur principal";
		$gepi_cpe_suivi=getSettingValue("gepi_cpe_suivi") ? getSettingValue("gepi_cpe_suivi") : "C.P.E.";

		$releve_affiche_eleve_une_ligne=getSettingValue("releve_affiche_eleve_une_ligne") ? getSettingValue("releve_affiche_eleve_une_ligne") : "n";
		$releve_mention_nom_court=getSettingValue("releve_mention_nom_court") ? getSettingValue("releve_mention_nom_court") : "y";

		$releve_photo_largeur_max=getSettingValue("releve_photo_largeur_max") ? getSettingValue("releve_photo_largeur_max") : 100;
		$releve_photo_hauteur_max=getSettingValue("releve_photo_hauteur_max") ? getSettingValue("releve_photo_hauteur_max") : 100;

		$releve_categ_font_size=getSettingValue("releve_categ_font_size") ? getSettingValue("releve_categ_font_size") : 10;
		$releve_categ_bgcolor=getSettingValue("releve_categ_bgcolor") ? getSettingValue("releve_categ_bgcolor") : "";

		$releve_affiche_tel=getSettingValue("releve_affiche_tel") ? getSettingValue("releve_affiche_tel") : "n";
		$releve_affiche_fax=getSettingValue("releve_affiche_fax") ? getSettingValue("releve_affiche_fax") : "n";

		if($releve_affiche_fax=="y"){
			$gepiSchoolFax=getSettingValue("gepiSchoolFax");
		}

		if($releve_affiche_tel=="y"){
			$gepiSchoolTel=getSettingValue("gepiSchoolTel");
		}

		$releve_affiche_INE_eleve=getSettingValue("releve_affiche_INE_eleve") ? getSettingValue("releve_affiche_INE_eleve") : "n";

		$genre_periode=getSettingValue("genre_periode") ? getSettingValue("genre_periode") : "M";

		$activer_photo_releve=getSettingValue("activer_photo_releve") ? getSettingValue("activer_photo_releve") : "n";
		$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes") ? getSettingValue("active_module_trombinoscopes") : "n";
		//===========================================

		// Bibliothèque de fonctions:
		include("../eleves/visu_ele_func.lib.php");

		// On extrait un tableau de l'ensemble des infos sur l'élève (bulletins, relevés de notes,... inclus)
		$tab_ele=info_eleve($ele_login);

		$acces_impression_bulletin=false;
		$acces_impression_releve_notes=false;
		if(($acces_releves=="y")||($acces_bulletins=="y")) {
			$acces_impression_bulletin=acces_impression_bulletin($ele_login);
			$acces_impression_releve_notes=acces_impression_releve_notes($ele_login);
		}

		// 20161205
		$acces_bulletin_simple=acces_impression_bulletins_simplifies($ele_login);

		$date_debut_log=get_date_debut_log();
		/*
		echo "<pre>";
		print_r($tab_ele);
		echo "</pre>";
		*/

		// 20191211
		$tab_id_classe_exclues_module_bulletins=get_classes_exclues_tel_module('bulletins');
		$temoin_classe_a_bulletins=0;
		foreach($tab_ele['classe'] as $key => $tmp_current_classe) {
			if(!in_array($tmp_current_classe['id_classe'], $tab_id_classe_exclues_module_bulletins)) {
				$temoin_classe_a_bulletins++;
				break;
			}
		}
		if($temoin_classe_a_bulletins==0) {
			$acces_bulletins='n';
			$acces_bulletin_simple=false;
		}

		$indice_derniere_classe=count($tab_ele['classe'])-1;
		if(!isset($tab_ele['classe'][$indice_derniere_classe]['pp'])) {
			echo "<p style='color:red;'>Aucun ".$gepi_prof_suivi." n'est associé à cet(te) élève.";
			if(acces("/classes/classes_const.php", $_SESSION['statut'])) {
				/*
				echo "<p>\$num_per_derniere_classe=$indice_derniere_classe</p>";
				echo "<pre>";
				print_r($tab_ele['periodes']);
				echo "</pre>";
				*/
				if(isset($tab_ele['classe'][$indice_derniere_classe]['id_classe'])) {
					echo " <a href='../classes/classes_const.php?id_classe=".$tab_ele['classe'][$indice_derniere_classe]['id_classe']."#".$tab_ele['login']."'>Associer</a>.";
				}
			}
			echo "</p>\n";
		}
		if(!isset($tab_ele['cpe'])) {
			echo "<p style='color:red;'>Aucun ".$gepi_cpe_suivi." n'est associé à cet(te) élève.";
			if(isset($tab_ele['classe'][$indice_derniere_classe]['id_classe'])) {
			   if(acces("/classes/classes_const.php", $_SESSION['statut'])) {
				   echo " <a href='../classes/classes_const.php?id_classe=".$tab_ele['classe'][$indice_derniere_classe]['id_classe']."#".$tab_ele['login']."'>Associer</a>.";
			   }
			
			}
			echo "</p>\n";
			
		}

		if((getSettingAOui('autorise_edt_tous'))||
			((getSettingAOui('autorise_edt_admin'))&&($_SESSION['statut']=='administrateur'))||
			((getSettingAOui('autorise_edt_eleve'))&&(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')))
		) {
			// Actuellement, les élèves et parents n'ont pas accès à visu_eleve.inc.php

			if(getSettingValue('edt_version_defaut')=='2') {
				$lien_edt=retourne_lien_edt2_eleve($ele_login, time());
			}
			else {
				$lien_edt=retourne_lien_edt_eleve($ele_login);
			}
			if($lien_edt!="") {
				echo "<div style='float:right; width:3em;'>".$lien_edt."</div>\n";
			}

		}

		//==============================================
			$temoin_rss_ele=retourne_temoin_ou_lien_rss($ele_login);
			if($temoin_rss_ele!="") {
				echo "<div style='float:right; margin-right:0.5em; '>".$temoin_rss_ele."</div>\n";
			}
		//==============================================


		echo "<script type='text/javascript'>
	document.getElementById('patience').style.display='none';
</script>\n";

		//====================================
		// Onglet Informations générales sur l'élève
		echo "<div id='t_eleve' class='t_onglet' style='";
		if($onglet=='eleve') {
			echo "border-bottom-color: ".$tab_couleur['eleve']."; ";
		}
		else {
			echo "border-bottom-color: black; ";
		}
		echo "background-color: ".$tab_couleur['eleve']."; ";
		echo "'>";
		echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=eleve$chaine_quitter_page_ou_non' onclick=\"affiche_onglet('eleve');return false;\">";
		//echo "<strong>".$tab_ele['nom']." ".$tab_ele['prenom']." (<em>".$tab_ele['liste_classes']."</em>)</strong>";
		echo "<strong>".$tab_ele['nom']." ".$tab_ele['prenom']." (<em>";
		if(isset($tab_ele['liste_classes'])) {
			echo $tab_ele['liste_classes'];
		}
		else {
			echo "Aucune classe";
		}
		echo "</em>)</strong>";
		echo "</a>";
		echo "</div>\n";

		// Onglet Informations responsables
		if($acces_responsables=="y") {
			echo "<div id='t_responsables' class='t_onglet' style='";
			if($onglet=='responsables') {
				echo "border-bottom-color: ".$tab_couleur['responsables']."; ";
			}
			else {
				echo "border-bottom-color: black; ";
			}
			echo "background-color: ".$tab_couleur['responsables']."; ";
			echo "'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=responsables$chaine_quitter_page_ou_non' onclick=\"affiche_onglet('responsables');return false;\">Responsables</a>";
			echo "</div>\n";
		}

		// Onglet Enseignements suivis
		if($acces_enseignements=="y") {
			echo "<div id='t_enseignements' class='t_onglet' style='";
			if($onglet=='enseignements') {
				echo "border-bottom-color: ".$tab_couleur['enseignements']."; ";
			}
			else {
				echo "border-bottom-color: black; ";
			}
			echo "background-color: ".$tab_couleur['enseignements']."; ";
			echo "'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=enseignements$chaine_quitter_page_ou_non' onclick=\"affiche_onglet('enseignements');return false;\">Enseignements</a>";
			echo "</div>\n";
		}

		// Onglet Bulletins
		if($acces_bulletins=="y") {
			echo "<div id='t_bulletins' class='t_onglet' style='";
			if($onglet=='bulletins') {
				echo "border-bottom-color: ".$tab_couleur['bulletins']."; ";
			}
			else {
				echo "border-bottom-color: black; ";
			}
			echo "background-color: ".$tab_couleur['bulletins']."; ";
			echo "'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=bulletins$chaine_quitter_page_ou_non' onclick=\"affiche_onglet('bulletins');return false;\">Bulletins</a>";
			echo "</div>\n";
		}

		// Onglet Relevés de notes
		if($acces_releves=="y") {
			echo "<div id='t_releves' class='t_onglet' style='";
			if($onglet=='releves') {
				echo "border-bottom-color: ".$tab_couleur['releves']."; ";
			}
			else {
				echo "border-bottom-color: black; ";
			}
			echo "background-color: ".$tab_couleur['releves']."; ";
			echo "'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=releves$chaine_quitter_page_ou_non' onclick=\"affiche_onglet('releves');return false;\">Relevés de notes</a>";
			echo "</div>\n";
		}

		// Onglet Cahier de textes
		if($acces_cdt=="y") {
			echo "<div id='t_cdt' class='t_onglet' style='";
			if($onglet=='cdt') {
				echo "border-bottom-color: ".$tab_couleur['cdt']."; ";
			}
			else {
				echo "border-bottom-color: black; ";
			}
			echo "background-color: ".$tab_couleur['cdt']."; ";
			echo "'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=cdt$chaine_quitter_page_ou_non' onclick=\"affiche_onglet('cdt');return false;\">Cahier de textes</a>";
			echo "</div>\n";
		}

		// Onglet fiches projet
		if(($acces_fp=="y")&&($acces_global_fp=="y")) {
			echo "<div id='t_fp' class='t_onglet' style='";
			if($onglet=='fp') {
				echo "border-bottom-color: ".$tab_couleur['fp']."; ";
			}
			else {
				echo "border-bottom-color: black; ";
			}
			echo "background-color: ".$tab_couleur['fp']."; ";
			echo "'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=fp$chaine_quitter_page_ou_non' onclick=\"affiche_onglet('fp');return false;\">Tous les projets</a>";
			echo "</div>\n";
		}


		// Onglet Absences
		if($acces_absences=="y") {
			echo "<div id='t_absences' class='t_onglet' style='";
			if($onglet=='absences') {
				echo "border-bottom-color: ".$tab_couleur['absences']."; ";
			}
			else {
				echo "border-bottom-color: black; ";
			}
			echo "background-color: ".$tab_couleur['absences']."; ";
			echo "'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=absences$chaine_quitter_page_ou_non' onclick=\"affiche_onglet('absences');return false;\">Absences</a>";
			echo "</div>\n";
		}

		// Onglet Discipline
		if($acces_discipline=="y") {
			echo "<div id='t_discipline' class='t_onglet' style='";
			if($onglet=='discipline') {
				echo "border-bottom-color: ".$tab_couleur['discipline']."; ";
			}
			else {
				echo "border-bottom-color: black; ";
			}
			echo "background-color: ".$tab_couleur['discipline']."; ";
			echo "'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=discipline$chaine_quitter_page_ou_non' onclick=\"affiche_onglet('discipline');return false;\">Discipline</a>";
			echo "</div>\n";
		}

		// Onglet Années antérieures
		if($acces_anna=="y") {
			echo "<div id='t_anna' class='t_onglet' style='";
			if($onglet=='anna') {
				echo "border-bottom-color: ".$tab_couleur['anna']."; ";
			}
			else {
				echo "border-bottom-color: black; ";
			}
			echo "background-color: ".$tab_couleur['anna']."; ";
			echo "'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=anna$chaine_quitter_page_ou_non' onclick=\"affiche_onglet('anna');return false;\">Années ant.</a>";
			echo "</div>\n";
		}
		//=====================================================================================

		//====================================
		echo "<div style='clear:both;'></div>\n";
		//====================================

		// Utilisé pour dire bonjour ou bonsoir autour de 18h
		$tmp_date=getdate();

		//=====================================================================================
		// 20170727
		echo necessaire_modif_tel_resp_ele();
		$acces_saisie_tel_resp=acces_saisie_telephone("responsable");
		$acces_saisie_tel_ele=acces_saisie_telephone("eleve");
		//=====================================================================================

		// On passe aux cadres contenu des onglets

		//===================
		// Onglet ELEVE
		//===================

		echo "<div id='eleve' class='onglet' style='";
		if($onglet!="eleve") {echo " display:none;";}
		echo "background-color: ".$tab_couleur['eleve']."; ";
		echo "'>";

		if((getSettingAOui('active_mod_discipline'))&&(acces("/mod_discipline/saisie_incident.php", $_SESSION['statut']))) {
			//require("../mod_discipline/mod_discipline.lib.php");;
			echo "<div style='float:right; text-align:center; width: 7em; background-image: url(\"../images/background/opacite50.png\"); border:1px solid black;'><a href='../mod_discipline/saisie_incident.php?ele_login[0]=".$ele_login."&amp;is_posted=y".add_token_in_url()."' title=\"Saisir un nouvel ".$mod_disc_terme_incident." dans le module Discipline\">Saisie<br />".$mod_disc_terme_incident."</a></div>";
		}

		echo "<h2>Informations sur l'".$gepiSettings['denomination_eleve']." ".$tab_ele['nom']." ".$tab_ele['prenom'];
		if((getSettingAOui("active_mod_alerte"))&&($check_mae)) {
			//echo "<div style='float:right;width:16px;'><a href='../mod_alerte/form_message.php?mode=rediger_message&sujet=".$tab_ele['nom']." ".$tab_ele['prenom']."&message=Bonjour' target='_blank' title=\"Déposer une alerte dans le module d'alerte.\"><img src='../images/icons/$icone_deposer_alerte' class='icone16' alt='Alerter' /></a></div>";
			echo " <a href='../mod_alerte/form_message.php?mode=rediger_message&sujet=".$tab_ele['nom']." ".$tab_ele['prenom']."&message=Bonjour' target='_blank' title=\"Déposer une alerte dans le module d'alerte.\"><img src='../images/icons/$icone_deposer_alerte' class='icone16' alt='Alerter' /></a>";
		}
		echo "</h2>\n";
		//affichage de la date de sortie de l'élève de l'établissement
		if ($tab_ele['date_sortie']!=0) {
		   echo "<span style=\"color:red\">Date de sortie de l'établissement : le ".affiche_date_sortie($tab_ele['date_sortie'])."<br/><br/></span>";
		}

		if(isset($tab_ele['compte_utilisateur'])) {
			if((in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe')))||
			(($_SESSION['statut']=='professeur')&&((isset($tab_ele['compte_utilisateur']['DerniereConnexionEle']))||(isset($tab_ele['compte_utilisateur']['DerniereConnexionEle_Echec']))))) {
				echo "<div style='float:right; width:20em; text-align:center;'>\n";
					echo "<strong>Compte</strong>\n";
					echo "<table class='boireaus' summary='Infos compte élève'>\n";
					$alt=1;
					$alt=$alt*(-1);
					if(acces("/utilisateurs/edit_eleve.php", $_SESSION['statut'])) {
						echo "<tr class='lig$alt'><th style='text-align: left;'>Compte&nbsp;:</th><td><a href='../utilisateurs/edit_eleve.php?critere_recherche=".preg_replace("/[^A-Za-z]/","%",ensure_ascii($tab_ele['nom']))."' title=\"Éditer le compte utilisateur de l'élève\">".$tab_ele['compte_utilisateur']['login']."</a></td></tr>";
					}
					else {
						echo "<tr class='lig$alt'><th style='text-align: left;'>Compte&nbsp;:</th><td>".$tab_ele['compte_utilisateur']['login']."</td></tr>";
					}
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'><th style='text-align: left;'>Etat&nbsp;:</th><td>".$tab_ele['compte_utilisateur']['etat']."</td></tr>";
					if(isset($tab_ele['compte_utilisateur']['DerniereConnexionEle'])) {
						$alt=$alt*(-1);
						if(isset($tab_ele['compte_utilisateur']['DerniereConnexionEle']['START'])) {
							echo "<tr class='lig$alt'><th style='text-align: left;'>Dernière connexion&nbsp;:</th><td>";
							echo formate_date($tab_ele['compte_utilisateur']['DerniereConnexionEle']['START'], 'y');
						}
						elseif(isset($tab_ele['compte_utilisateur']['DerniereConnexionEle_Echec']['START'])) {
							echo "<tr style='background-color:red' title=\"Cet utilisateur ne s'est jamais connecté avec succès (du moins, si les log n'ont pas été vidés récemment).\nEn revanche, un échec de connexion est constaté à la date indiquée.\"><th style='text-align: left;'>Dernière tentative de connexion&nbsp;:</th><td>";
							echo formate_date($tab_ele['compte_utilisateur']['DerniereConnexionEle_Echec']['START'], 'y');
						}
						else {
							echo "<tr class='lig$alt'><th style='text-align: left;'>Dernière connexion&nbsp;:</th><td>";
							echo "<img src='../images/disabled.png' class='icone20' title=\"Cet élève ne s'est jamais connecté (aussi loin que remontent les journaux de connexion (à savoir : $date_debut_log)).\"/>";
						}
						echo "</td></tr>";
					}
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'><th style='text-align: left;'>Authentification&nbsp;:</th><td title=\"Gepi permet selon les configurations plusieurs modes d'authentification:
- gepi : Authentification sur la base mysql de Gepi,
- sso : Authentification CAS ou LCS assurée par une autre machine,
- ldap : Authentification en recherchant la correspondance login/mot_de_passe dans un annuaire LDAP.\">".$tab_ele['compte_utilisateur']['auth_mode'];
					echo temoin_compte_sso($tab_ele['login']);
					echo "</td></tr>";

					if(($_SESSION['statut']=='administrateur')||
						(($_SESSION['statut']=='scolarite')&&(getSettingAOui('ScolResetPassEle')))||
						(($_SESSION['statut']=='cpe')&&(getSettingAOui('CpeResetPassEle')))
					) {
						if($_SESSION['statut']=="administrateur") {
							$alt=$alt*(-1);
							echo "<tr class='lig$alt'><th style='text-align: left;'>Dépannage :</th><td>";
							echo affiche_actions_compte($tab_ele['compte_utilisateur']['login']);
							if(($tab_ele['compte_utilisateur']['auth_mode']=='gepi')||
								(($tab_ele['compte_utilisateur']['auth_mode']=='ldap')&&($gepiSettings['ldap_write_access'] == "yes"))) {
								echo "<br />\n";
								echo affiche_reinit_password($tab_ele['compte_utilisateur']['login']);
							}
							echo "</td></tr>\n";
						}
						elseif((($tab_ele['compte_utilisateur']['auth_mode']=='gepi')||
						(($tab_ele['compte_utilisateur']['auth_mode']=='ldap')&&($gepiSettings['ldap_write_access'] == "yes")))&&
						(acces('/utilisateurs/reset_passwords.php', $_SESSION['statut']))) {
							$alt=$alt*(-1);
							echo "<tr class='lig$alt'><th style='text-align: left;'>Dépannage :</th><td>";
							echo affiche_reinit_password($tab_ele['compte_utilisateur']['login']);
							echo "</td></tr>\n";
						}
					}

					echo "</table>\n";
				echo "</div>\n";
			}
		}

		//==============================================
		// Engagements
		if(getSettingAOui('active_mod_engagements')) {
			$tab_engagements_user=get_tab_engagements_user($ele_login);
			if(count($tab_engagements_user['indice'])>0) {
				echo "<div style='float: right; width:15em; text-align: center; margin:0.5em; margin:0.2em;' class='fieldset_opacite50' title=\"Engagements du responsable\">";
				if(acces("/mod_engagements/saisie_engagements_user.php", $_SESSION['statut'])) {
					echo "
			<div style='float: right; width:20px; height:20px;' title=\"Saisir/Modifier les engagements\"><a href='../mod_engagements/saisie_engagements_user.php?login_user=$ele_login&amp;retour=visu_eleve'><img src='../images/icons/plus_moins.png' class='icone16' alt='Ajouter/Enlever'/></a></div>";
				}

				/*
				echo "<pre>";
				print_r($tab_engagements_user['indice']);
				echo "</pre>";
				*/
				echo "<div id='div_engagements_eleve'>";
				for($loop=0;$loop<count($tab_engagements_user['indice']);$loop++) {
					$detail_eng="";
					//if($tab_engagements_user['indice'][$loop]['id_type']=='id_classe') {
					if(($tab_engagements_user['indice'][$loop]['type']=='id_classe')&&($tab_engagements_user['indice'][$loop]['id_type']=='id_classe')) {
						$detail_eng=" en ".get_nom_classe($tab_engagements_user['indice'][$loop]['valeur']);
					}
					echo "<span title=\"".$tab_engagements_user['indice'][$loop]['nom_engagement'].$detail_eng."\n(".$tab_engagements_user['indice'][$loop]['engagement_description'].")\">".$tab_engagements_user['indice'][$loop]['nom_engagement'].$detail_eng."</span><br />";
				}
				echo "</div>\n";

				echo "</div>\n";
			}
		}

		echo "<div style='margin-top:1em; text-align:center; float:right; width:10em;margin-right:2px;' class='fieldset_opacite50' title=\"Modalités d'accompagnement\">";
		$tab_modalites_accompagnement_eleve=get_tab_modalites_accompagnement_eleve($ele_login);

		if(isset($tab_modalites_accompagnement_eleve["code"])) {
			$tab_modalites_accompagnement=get_tab_modalites_accompagnement();
			$tmp_tab_deja=array();
			foreach($tab_modalites_accompagnement_eleve["code"] as $current_code => $tmp_tab) {
				echo " <span title=\"".$tab_modalites_accompagnement["code"][$current_code]["libelle"];
				$tmp_tab_commentaires=array();
				for($loop=0;$loop<count($tmp_tab);$loop++) {
					if(isset($tmp_tab[$loop]["commentaire"])) {
						$tmp_commentaire=preg_replace('/"/', "''", trim($tmp_tab[$loop]["commentaire"]));
						if(!in_array($tmp_commentaire, $tmp_tab_commentaires)) {
							$tmp_tab_commentaires[]=$tmp_commentaire;
						}
					}
				}
				$liste_commentaires="";
				for($loop=0;$loop<count($tmp_tab_commentaires);$loop++) {
					$liste_commentaires.="\n- ".$tmp_tab_commentaires[$loop];
				}
				if($liste_commentaires!="") {
					echo " :".$liste_commentaires;
				}
				echo "\">".$current_code."</span>";
				$tmp_tab_deja[]=$current_code;
			}
		}
		else {
			echo "Aucune modalité d'accompagnement n'est définie.";
		}

		if(acces_saisie_modalites_accompagnement()) {
			echo "<br /><a href='../gestion/saisie_modalites_accompagnement.php?login_eleve=".$ele_login."' onclick=\"return confirm_abandon (this, change, '$themessage')\" style='font-size:small; text-decoration:none; color:black;'><img src='../images/icons/add.png' class='icone16' alt='Add' />Ajouter/Modifier des modalités d'accompagnement.</a>";
		}
		echo "</div>\n";
		// Fin des DIV float sur la droite de l'onglet

		// Tableau des infos élève sur la gauche de l'onglet
		echo "<table border='0' summary='Infos élève'>\n";
		echo "<tr>\n";
		echo "<td valign='top'>\n";

			echo "<table class='boireaus' summary='Infos élève (1)'>\n";
			$alt=-1;
			echo "<tr class='lig$alt'><th style='text-align: left;'>Nom&nbsp;:</th><td>";
			if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||
			(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiAccesTouteFicheEleveCpe')))||
			(($_SESSION['statut']=='cpe')&&(is_cpe($_SESSION['login'],'',$ele_login)))||
			(($_SESSION['statut']=='professeur')&&(is_pp($_SESSION['login'],"",$ele_login))&&(getSettingAOui('GepiAccesGestElevesProfP')))) {
				echo "<a href='modify_eleve.php?eleve_login=".$ele_login."&amp;quelles_classes=certaines&amp;order_type=nom,prenom&amp;motif_rech=' title=\"Modifier la fiche élève\">".$tab_ele['nom']."</a>";
			}
			else {
				echo $tab_ele['nom'];
			}
			echo "</td></tr>\n";
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'><th style='text-align: left;'>Prénom&nbsp;:</th><td>".$tab_ele['prenom']."</td></tr>\n";
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'><th style='text-align: left;'>Sexe&nbsp;:</th><td>".$tab_ele['sexe']."</td></tr>\n";
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'><th style='text-align: left;'>Né";
			if($tab_ele['sexe']=='F') {echo "e";}
			echo " le&nbsp;:</th><td>".$tab_ele['naissance']."</td></tr>\n";
			if(isset($tab_ele['lieu_naissance'])) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'><th style='text-align: left;'>à&nbsp;:</th><td>".$tab_ele['lieu_naissance']."</td></tr>\n";
			}

			$alt=$alt*(-1);
			echo "<tr class='lig$alt'><th style='text-align: left;'>Régime&nbsp;:</th><td>";
			if ($tab_ele['regime'] == "d/p") {echo "Demi-pensionnaire";}
			if ($tab_ele['regime'] == "ext.") {echo "Externe";}
			if ($tab_ele['regime'] == "int.") {echo "Interne";}
			if ($tab_ele['regime'] == "i-e"){
				echo "Interne&nbsp;externé";
				if (my_strtoupper($tab_ele['sexe'])!= "F") {echo "e";}
			}
			echo "</td></tr>\n";

			$alt=$alt*(-1);
			echo "<tr class='lig$alt'><th style='text-align: left;'>Redoublant&nbsp;:</th><td>";
			if ($tab_ele['doublant'] == 'R'){
				echo "Oui";
			}
			else {
				echo "Non";
			}
			echo "</td></tr>\n";

			if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='cpe')) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'><th style='text-align: left;'>Elenoet&nbsp;:</th><td>".$tab_ele['elenoet']."</td></tr>\n";
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'><th style='text-align: left;'>Ele_id&nbsp;:</th><td>".$tab_ele['ele_id']."</td></tr>\n";
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'><th style='text-align: left;'>N°INE&nbsp;:</th><td>".$tab_ele['no_gep']."</td></tr>\n";
			}

			$alt=$alt*(-1);
			echo "<tr class='lig$alt'><th style='text-align: left;'>MEF&nbsp;:</th><td>";
			if(acces("/mef/associer_eleve_mef.php", $_SESSION['statut'])) {
				echo "<div style='float:right;width:16px;'><a href='../mef/associer_eleve_mef.php?type_selection=nom_eleve&nom_eleve=".preg_replace("/^[^A-Za-z0-9 _]*$/", "%", $tab_ele['nom'])."' target='_blank'><img src='../images/edit16.png' class='icone16' alt='Éditer' /></a></div>";
			}
			echo $tab_ele['mef']."</td></tr>\n";

			$chaine_acces_modif_tel="";
			if($acces_tel_ele) {
				if($acces_saisie_tel_ele) {
					$chaine_acces_modif_tel="<div style='float:right; width:16px;margin-left:0.5em;'><a href='$gepiPath/gestion/saisie_contact.php?login_ele=".$ele_login."' onclick=\"affiche_corrige_tel_ele('".$ele_login."');return false;\" target='_blank' title=\"Modifier/corriger les numéros de téléphone, email.\nAprès une modification, il faudra rafraichir la présente page.\"><img src='".$gepiPath."/images/edit16.png' class='icone16' alt='Éditer' /></a></div>\n";
				}

				if((isset($tab_ele["tel_pers"]))&&($tab_ele["tel_pers"]!="")&&(in_array($_SESSION["statut"], array("administrateur", "scolarite", "cpe")))) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'><th style='text-align: left;'>Tel.pers&nbsp;:</th><td>".$chaine_acces_modif_tel.affiche_numero_tel_sous_forme_classique($tab_ele['tel_pers'])."</td></tr>\n";
				}

				if((isset($tab_ele["tel_port"]))&&($tab_ele["tel_port"]!="")&&(in_array($_SESSION["statut"], array("administrateur", "scolarite", "cpe")))) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'><th style='text-align: left;'>Tel.port&nbsp;:</th><td>".$chaine_acces_modif_tel.affiche_numero_tel_sous_forme_classique($tab_ele['tel_port'])."</td></tr>\n";
				}

				if((isset($tab_ele["tel_prof"]))&&($tab_ele["tel_prof"]!="")&&(in_array($_SESSION["statut"], array("administrateur", "scolarite", "cpe")))) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'><th style='text-align: left;'>Tel.prof&nbsp;:</th><td>".$chaine_acces_modif_tel.affiche_numero_tel_sous_forme_classique($tab_ele['tel_prof'])."</td></tr>\n";
				}
			}

			if($acces_email_ele) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'><th style='text-align: left;'>Email&nbsp;:</th><td>".$chaine_acces_modif_tel;
				//$tmp_date=getdate();
				//echo "<a href='mailto:".$tab_ele['email']."?subject=GEPI&amp;body=";
				echo "<a href='mailto:".$tab_ele['email']."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI&amp;body=";
				if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
				echo ",%0d%0aCordialement.'>";
				echo $tab_ele['email'];
				echo "</a>";
				echo "</td></tr>\n";
			}

			//echo "<tr><th>:</th><td>".$tab_ele['']."</td></tr>\n";
			echo "</table>\n";
		echo "</td>\n";

		if($active_module_trombinoscopes=="y") {
			echo "<td valign='top'>\n";
				$photo=nom_photo($tab_ele['elenoet']);
				if($photo){
					if(file_exists($photo)){
						$dimphoto=redimensionne_image_releve($photo);
						echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />'."\n";
					}
				}
			echo "</td>\n";
		}
		echo "</tr>\n";
		echo "</table>\n";

		if(isset($tab_ele['etab_id'])) {
			if ($tab_ele['etab_id'] != '990') {
				if ($RneEtablissement != $tab_ele['etab_id']) {
					echo "<p>Etablissement d'origine : ";
					echo $tab_ele['etab_niveau_nom']." ".$tab_ele['etab_type']." ".$tab_ele['etab_nom']." (".$tab_ele['etab_cp']." ".$tab_ele['etab_ville'].")\n";
				}
			} else {
				echo "<p>Etablissement d'origine : ";
				echo "hors de France\n";
			}
			echo "</p>\n";
		}
		echo "</div>\n";

		//===================================================

		//=======================
		// Onglet RESPONSABLES
		//=======================

		//$tmp_date=getdate();
		if($acces_responsables=="y") {
			echo "<div id='responsables' class='onglet' style='";
			if($onglet!="responsables") {echo " display:none;";}
			echo "background-color: ".$tab_couleur['responsables']."; ";
			echo "'>";
			echo "<h2>Responsables de l'".$gepiSettings['denomination_eleve']." ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";

			if((!isset($tab_ele['resp']))||(count($tab_ele['resp'])==0)) {
				echo "<p>Aucun responsable n'a été enregistré.</p>\n";
			}
			else {
				echo "<table border='0' summary='Infos responsables'>\n";
				echo "<tr>\n";
				$cpt_resp_legal0=0;
				for($i=0;$i<count($tab_ele['resp']);$i++) {
					if($tab_ele['resp'][$i]['resp_legal']!=0) {
						echo "<td valign='top'>\n";
						echo "<p>Responsable légal <strong>".$tab_ele['resp'][$i]['resp_legal']."</strong>";

						// 20180623
						$code_parente='';
						if(($tab_ele['resp'][$i]['code_parente']!='')&&(isset($tab_code_parente[$tab_ele['resp'][$i]['code_parente']]))) {
							if(isset($tab_code_parente[$tab_ele['resp'][$i]['code_parente']]['libelle'])) {
								$code_parente=$tab_code_parente[$tab_ele['resp'][$i]['code_parente']]['libelle'];
							}
							else {
								$code_parente=$tab_ele['resp'][$i]['code_parente'];
							}
						}
						if($code_parente!='') {
							echo " <em>($code_parente)</em>";
						}

						echo "</p>\n";

						echo "<table class='boireaus' summary='Infos responsables (1)'>\n";
						$alt=-1;
						echo "<tr class='lig$alt'><th style='text-align: left;'>Nom:</th><td>";

						if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
							echo "<a href='../responsables/modify_resp.php?pers_id=".$tab_ele['resp'][$i]['pers_id']."' title=\"Modifier la fiche responsable\">".$tab_ele['resp'][$i]['nom']."</a>";
						}
						else {
							echo $tab_ele['resp'][$i]['nom'];
						}

						echo "</td></tr>\n";
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'><th style='text-align: left;'>Prénom:</th><td>".$tab_ele['resp'][$i]['prenom']."</td></tr>\n";
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'><th style='text-align: left;'>Civilité:</th><td>".$tab_ele['resp'][$i]['civilite']."</td></tr>\n";

						// 20170727
						$chaine_acces_modif_tel="";
						if($acces_saisie_tel_resp) {
							$chaine_acces_modif_tel="<div style='float:right; width:16px;margin-left:0.5em;'><a href='$gepiPath/gestion/saisie_contact.php?pers_id=".$tab_ele['resp'][$i]['pers_id']."' onclick=\"affiche_corrige_tel_resp('".$tab_ele['resp'][$i]['pers_id']."');return false;\" target='_blank' title=\"Modifier/corriger les numéros de téléphone, email\"><img src='".$gepiPath."/images/edit16.png' class='icone16' alt='Éditer' /></a></div>\n";
						}

						/*
						GepiAccesAdresseParentsRespProf
						GepiAccesAdresseTousParentsProf
						GepiAccesTelParentsRespProf
						GepiAccesTelTousParentsProf
						GepiAccesMailParentsRespProf
						GepiAccesMailTousParentsProf

						GepiAccesAdresseParentsRespPP
						GepiAccesTelParentsRespPP
						GepiAccesMailParentsRespPP
						*/
						// 20190101
						if($acces_tel_responsable) {
							if($tab_ele['resp'][$i]['tel_pers']!='') {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Tél.pers:</th><td>".$chaine_acces_modif_tel.affiche_numero_tel_sous_forme_classique($tab_ele['resp'][$i]['tel_pers'])."</td></tr>\n";
							}
							if($tab_ele['resp'][$i]['tel_port']!='') {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Tél.port:</th><td>".$chaine_acces_modif_tel.affiche_numero_tel_sous_forme_classique($tab_ele['resp'][$i]['tel_port'])."</td></tr>\n";
							}
							if($tab_ele['resp'][$i]['tel_prof']!='') {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Tél.prof:</th><td>".$chaine_acces_modif_tel.affiche_numero_tel_sous_forme_classique($tab_ele['resp'][$i]['tel_prof'])."</td></tr>\n";
							}
						}

						// 20190101
						if($acces_email_responsable) {
							if($tab_ele['resp'][$i]['mel']!='') {
								//$tmp_date=getdate();
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Courriel:</th><td>".$chaine_acces_modif_tel;
								echo "<a href='mailto:".$tab_ele['resp'][$i]['mel']."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI&amp;body=";
								if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
								echo ",%0d%0aCordialement.'>";
								echo $tab_ele['resp'][$i]['mel'];
								echo "</a>";
								echo "</td></tr>\n";
							}
						}

						if(!isset($tab_ele['resp'][$i]['etat'])) {
							$alt=$alt*(-1);
							echo "<tr class='lig$alt'><th style='text-align: left;'>Dispose d'un compte:</th><td>Non</td></tr>\n";
						}
						else {
							$alt=$alt*(-1);
							echo "<tr class='lig$alt'><th style='text-align: left;'>Dispose d'un compte:</th><td>";
							if(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe'))) {
								if(acces("/utilisateurs/edit_eleve.php", $_SESSION['statut'])) {
									echo "<a href='../utilisateurs/edit_responsable.php?critere_recherche=".preg_replace("/[^A-Za-z]/","%",ensure_ascii($tab_ele['resp'][$i]['nom']))."' title=\"Éditer le compte utilisateur du responsable\">".$tab_ele['resp'][$i]['login']."</a>";
								}
								else {
									echo $tab_ele['resp'][$i]['login'];
								}
							}
							else {
								echo "Oui";
							}
							echo " (";
							if($tab_ele['resp'][$i]['etat']=='actif') {
								echo "<span style='color:green;'>";
							}
							else{
								echo "<span style='color:red;'>";
							}
							echo $tab_ele['resp'][$i]['etat'];
							echo "</span>)\n";

							if(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe'))) {
								echo "<br />\n";
								echo "<span title=\"Gepi permet selon les configurations plusieurs modes d'authentification:
- gepi : Authentification sur la base mysql de Gepi,
- sso : Authentification CAS ou LCS assurée par une autre machine,
- ldap : Authentification en recherchant la correspondance login/mot_de_passe dans un annuaire LDAP.\">";
								echo "Auth.: ".$tab_ele['resp'][$i]['auth_mode'];
								echo temoin_compte_sso($tab_ele['resp'][$i]['login']);
								echo "</span>";
							}

							if(isset($tab_ele['resp'][$i]['DerniereConnexionResp'])) {
								if(isset($tab_ele['resp'][$i]['DerniereConnexionResp']['START'])) {
									echo "<br />Dernière connexion&nbsp;: ".formate_date($tab_ele['resp'][$i]['DerniereConnexionResp']['START'], 'y');
								}
								elseif(isset($tab_ele['resp'][$i]['DerniereConnexionResp_Echec']['START'])) {
									echo "<br /><span title=\"Cet utilisateur ne s'est jamais connecté avec succès (du moins, si les log n'ont pas été vidés récemment). En revanche, un échec de connexion est constaté à la date indiquée.\">Dernière tentative de connexion&nbsp;: ".formate_date($tab_ele['resp'][$i]['DerniereConnexionResp_Echec']['START'], 'y')."</span>";
								}
								else {
									echo "<br />Dernière connexion&nbsp;: <img src='../images/disabled.png' class='icone20' title=\"Cet utilisateur ne s'est jamais connecté (aussi loin que remontent les journaux de connexion (à savoir : $date_debut_log)).\"/>";
								}
							}

							// Vérifier qui peut avoir accès à cette adresse
							// 20190101
							if($acces_email_responsable) {
								if((isset($tab_ele['resp'][$i]['email']))&&($tab_ele['resp'][$i]['email']!='')&&
								((!isset($tab_ele['resp'][$i]['mel']))||($tab_ele['resp'][$i]['email']!=$tab_ele['resp'][$i]['mel']))) {
									echo "<br />";
									echo "<a href='mailto:".$tab_ele['resp'][$i]['email']."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI&amp;body=";
									if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
									echo ",%0d%0aCordialement.'>";
									echo $tab_ele['resp'][$i]['email'];
									echo "</a>";
								}
							}
							echo "</td></tr>\n";

							if(($_SESSION['statut']=='administrateur')||
								(($_SESSION['statut']=='scolarite')&&(getSettingAOui('ScolResetPassResp')))||
								(($_SESSION['statut']=='cpe')&&(getSettingAOui('CpeResetPassResp')))
							) {
								if($_SESSION['statut']=="administrateur") {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'><th style='text-align: left;'>Dépannage :</th><td>";
									echo affiche_actions_compte($tab_ele['resp'][$i]['login']);
									if(($tab_ele['resp'][$i]['auth_mode']=='gepi')||
										(($tab_ele['resp'][$i]['auth_mode']=='ldap')&&($gepiSettings['ldap_write_access'] == "yes"))) {
										echo "<br />\n";
										echo affiche_reinit_password($tab_ele['resp'][$i]['login']);
									}
									echo "</td></tr>\n";
								}
								elseif((($tab_ele['resp'][$i]['auth_mode']=='gepi')||
								(($tab_ele['resp'][$i]['auth_mode']=='ldap')&&($gepiSettings['ldap_write_access'] == "yes")))&&
								(acces('/utilisateurs/reset_passwords.php', $_SESSION['statut']))) {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'><th style='text-align: left;'>Dépannage :</th><td>";
									echo affiche_reinit_password($tab_ele['resp'][$i]['login']);
									echo "</td></tr>\n";
								}
							}

						}

						// 20190101
						if($acces_adresse_responsable) {
							if($tab_ele['resp'][$i]['adr1']!='') {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Ligne 1 adresse:</th><td>".$tab_ele['resp'][$i]['adr1']."</td></tr>\n";
							}
							if($tab_ele['resp'][$i]['adr2']!='') {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Ligne 2 adresse:</th><td>".$tab_ele['resp'][$i]['adr2']."</td></tr>\n";
							}
							if($tab_ele['resp'][$i]['adr3']!='') {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Ligne 3 adresse:</th><td>".$tab_ele['resp'][$i]['adr3']."</td></tr>\n";
							}
							if($tab_ele['resp'][$i]['adr4']!='') {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Ligne 4 adresse:</th><td>".$tab_ele['resp'][$i]['adr4']."</td></tr>\n";
							}
							if($tab_ele['resp'][$i]['cp']!='') {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Code postal:</th><td>".$tab_ele['resp'][$i]['cp']."</td></tr>\n";
							}
							if($tab_ele['resp'][$i]['commune']!='') {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Commune:</th><td>".$tab_ele['resp'][$i]['commune']."</td></tr>\n";
							}
							if($tab_ele['resp'][$i]['pays']!='') {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Pays:</th><td>".$tab_ele['resp'][$i]['pays']."</td></tr>\n";
							}
						}

						// 20140522
						if(($acces_bulletins=="y")&&($acces_impression_bulletin)) {
							$alt=$alt*(-1);
							echo "<tr class='lig$alt'><th style='text-align: left;'>Bulletins:</th><td>";
							// Imprimer le bulletin avec l'adresse de ce parent en particulier.


							if(isset($tab_ele['periodes'])) {
								$type_bulletin_par_defaut="pdf";
								if(getSettingValue("type_bulletin_par_defaut")=="pdf_2016") {
									$type_bulletin_par_defaut="pdf_2016";
								}

								$chaine_intercaler_releve_notes="";
								if($acces_impression_releve_notes) {
									$chaine_intercaler_releve_notes="&intercaler_releve_notes=y&rn_param_auto=y";
								}

								for($loop_per=0;$loop_per<count($tab_ele['periodes']);$loop_per++) {
									$current_id_classe=$tab_ele['periodes'][$loop_per]['id_classe'];
									$current_num_periode=$tab_ele['periodes'][$loop_per]['num_periode'];
									if($loop_per>0) {
										echo " - ";
									}
									echo "<a href='../bulletin/bull_index.php?mode_bulletin=".$type_bulletin_par_defaut.$chaine_intercaler_releve_notes."&type_bulletin=-1&choix_periode_num=fait&valide_select_eleves=y&tab_selection_ele_0_0[0]=".$ele_login."&tab_id_classe[0]=".$current_id_classe."&tab_periode_num[0]=".$current_num_periode."&pers_id=".$tab_ele['resp'][$i]['pers_id']."' target='_blank' title=\"Voir dans un nouvel onglet le bulletin ".casse_mot($type_bulletin_par_defaut, "maj")." de la période ".$current_num_periode.".

Le bulletin sera affiché/généré pour l'adresse responsable de ".$tab_ele['resp'][$i]['civilite']." ".$tab_ele['resp'][$i]['nom']." ".$tab_ele['resp'][$i]['prenom']."\">P".$current_num_periode."</a>";
								}

								$possibilite_envoi_bulletin_par_mail="n";
								if((isset($tab_ele['resp'][$i]['mel']))&&(check_mail($tab_ele['resp'][$i]['mel']))) {
									$possibilite_envoi_bulletin_par_mail="y";
								}
								if($possibilite_envoi_bulletin_par_mail=="y") {
									echo "<br />";
									for($loop_per=0;$loop_per<count($tab_ele['periodes']);$loop_per++) {
										$current_id_classe=$tab_ele['periodes'][$loop_per]['id_classe'];
										$current_num_periode=$tab_ele['periodes'][$loop_per]['num_periode'];
										if($loop_per>0) {
											echo " - ";
										}
										echo "<a href='../bulletin/bull_index.php?mode_bulletin=".$type_bulletin_par_defaut.$chaine_intercaler_releve_notes."&type_bulletin=-1&choix_periode_num=fait&valide_select_eleves=y&tab_selection_ele_0_0[0]=".$ele_login."&tab_id_classe[0]=".$current_id_classe."&tab_periode_num[0]=".$current_num_periode."&pers_id=".$tab_ele['resp'][$i]['pers_id']."&dest_mail=".$tab_ele['resp'][$i]['mel']."' target='_blank' title=\"Envoyer le bulletin par mail à l'adresse ".$tab_ele['resp'][$i]['mel'].", et ouvrir ensuite dans un nouvel onglet le bulletin ".casse_mot($type_bulletin_par_defaut, "maj")." de la période ".$current_num_periode.".

Le bulletin sera affiché/généré pour l'adresse responsable de ".$tab_ele['resp'][$i]['civilite']." ".$tab_ele['resp'][$i]['nom']." ".$tab_ele['resp'][$i]['prenom']."\"><img src='../images/icons/mail.png' class='icone16' alt='Mail' />P".$current_num_periode."</a>";
									}
								}
							}

							echo "</td></tr>\n";
						}

						if(($acces_releves=="y")&&($acces_impression_releve_notes)&&(isset($tab_ele['periodes']))) {
							$alt=$alt*(-1);
							echo "<tr class='lig$alt'><th style='text-align: left;'>Relevés:</th><td>";

							$type_bulletin_par_defaut="pdf";

							for($loop_per=0;$loop_per<count($tab_ele['periodes']);$loop_per++) {
								$current_id_classe=$tab_ele['periodes'][$loop_per]['id_classe'];
								$current_num_periode=$tab_ele['periodes'][$loop_per]['num_periode'];
								if($loop_per>0) {
									echo " - ";
								}
								echo "<a href='../cahier_notes/visu_releve_notes_bis.php?tab_id_classe[0]=$current_id_classe&choix_periode=periode&tab_periode_num[0]=$current_num_periode&mode_bulletin=$type_bulletin_par_defaut&valide_select_eleves=y&choix_parametres=effectue&tab_selection_ele_0_0[0]=$ele_login&rn_adr_resp[0]=y&pers_id=".$tab_ele['resp'][$i]['pers_id']."&rn_param_auto=y' target='_blank' title=\"Voir dans un nouvel onglet le relevé de notes ".casse_mot($type_bulletin_par_defaut, "maj")." de la période ".$current_num_periode."\">P".$current_num_periode."</a>";
							}

							echo "</td></tr>\n";
						}


						//==============================================
						// Engagements
						if((getSettingAOui('active_mod_engagements'))&&(isset($tab_ele['resp'][$i]['login']))&&($tab_ele['resp'][$i]['login']!="")) {
							$alt=$alt*(-1);
							echo "<tr class='lig$alt'><th style='text-align: left;'>Engagements</th><td>";
							if(acces("/mod_engagements/saisie_engagements_user.php", $_SESSION['statut'])) {
								echo "
						<div style='float: right; width:20px; height:20px;' title=\"Saisir/Modifier les engagements\"><a href='../mod_engagements/saisie_engagements_user.php?login_user=".$tab_ele['resp'][$i]['login']."&amp;retour=visu_eleve&amp;retour_eleve=".$ele_login."'><img src='../images/icons/plus_moins.png' class='icone16' alt='Ajouter/Enlever'/></a></div>";
							}

							$tab_engagements_user=get_tab_engagements_user($tab_ele['resp'][$i]['login']);
							if(count($tab_engagements_user['indice'])>0) {

								/*
								echo "<pre>";
								print_r($tab_engagements_user['indice']);
								echo "</pre>";
								*/
								for($loop=0;$loop<count($tab_engagements_user['indice']);$loop++) {
									$detail_eng="";
									//if($tab_engagements_user['indice'][$loop]['id_type']=='id_classe') {
									if(($tab_engagements_user['indice'][$loop]['type']=='id_classe')&&($tab_engagements_user['indice'][$loop]['id_type']=='id_classe')) {
										$detail_eng=" en ".get_nom_classe($tab_engagements_user['indice'][$loop]['valeur']);
									}
									echo "<span title=\"".$tab_engagements_user['indice'][$loop]['nom_engagement'].$detail_eng."\n(".$tab_engagements_user['indice'][$loop]['engagement_description'].")\">".$tab_engagements_user['indice'][$loop]['nom_engagement'].$detail_eng."</span><br />";
								}
							}
							echo "</td></tr>";
						}
						//=========

						echo "</table>\n";
						echo "</td>\n";
					}
					else {
						$cpt_resp_legal0++;
					}
				}
				echo "</tr>\n";
				echo "</table>\n";

				// Simples contacts non responsables légaux
				if($cpt_resp_legal0>0) {
					echo "<table border='0' summary='Infos responsables (0)'>\n";
					echo "<tr>\n";
					for($i=0;$i<count($tab_ele['resp']);$i++) {

						if($tab_ele['resp'][$i]['resp_legal']==0) {
							echo "<td valign='top'>\n";
							echo "<p>Contact (<em>non responsable légal</em>)";

							// 20180623
							$code_parente='';
							if(($tab_ele['resp'][$i]['code_parente']!='')&&(isset($tab_code_parente[$tab_ele['resp'][$i]['code_parente']]))) {
								if(isset($tab_code_parente[$tab_ele['resp'][$i]['code_parente']]['libelle'])) {
									$code_parente=$tab_code_parente[$tab_ele['resp'][$i]['code_parente']]['libelle'];
								}
								else {
									$code_parente=$tab_ele['resp'][$i]['code_parente'];
								}
							}
							if($code_parente!='') {
								echo " <em>($code_parente)</em>";
							}

							echo "</p>\n";

							echo "<table class='boireaus' summary='Infos responsables (0)'>\n";
							$alt=-1;
							echo "<tr class='lig$alt'><th style='text-align: left;'>Nom:</th><td>";

							if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
								echo "<a href='../responsables/modify_resp.php?pers_id=".$tab_ele['resp'][$i]['pers_id']."' title=\"Modifier la fiche responsable\">".$tab_ele['resp'][$i]['nom']."</a>";
							}
							else {
								echo $tab_ele['resp'][$i]['nom'];
							}

							echo "</td></tr>\n";
							$alt=$alt*(-1);
							echo "<tr class='lig$alt'><th style='text-align: left;'>Prénom:</th><td>".$tab_ele['resp'][$i]['prenom']."</td></tr>\n";
							$alt=$alt*(-1);
							echo "<tr class='lig$alt'><th style='text-align: left;'>Civilité:</th><td>".$tab_ele['resp'][$i]['civilite']."</td></tr>\n";

							// 20170727
							$chaine_acces_modif_tel="";
							if($acces_saisie_tel_resp) {
								$chaine_acces_modif_tel="<div style='float:right; width:16px;margin-left:0.5em;'><a href='$gepiPath/gestion/saisie_contact.php?pers_id=".$tab_ele['resp'][$i]['pers_id']."' onclick=\"affiche_corrige_tel_resp('".$tab_ele['resp'][$i]['pers_id']."');return false;\" target='_blank' title=\"Modifier/corriger les numéros de téléphone, email\"><img src='".$gepiPath."/images/edit16.png' class='icone16' alt='Éditer' /></a></div>\n";
							}

							// 20190101
							if($acces_tel_responsable) {
								if($tab_ele['resp'][$i]['tel_pers']!='') {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'><th style='text-align: left;'>Tél.pers:</th><td>".$chaine_acces_modif_tel.affiche_numero_tel_sous_forme_classique($tab_ele['resp'][$i]['tel_pers'])."</td></tr>\n";
								}
								if($tab_ele['resp'][$i]['tel_port']!='') {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'><th style='text-align: left;'>Tél.port:</th><td>".$chaine_acces_modif_tel.affiche_numero_tel_sous_forme_classique($tab_ele['resp'][$i]['tel_port'])."</td></tr>\n";
								}
								if($tab_ele['resp'][$i]['tel_prof']!='') {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'><th style='text-align: left;'>Tél.prof:</th><td>".$chaine_acces_modif_tel.affiche_numero_tel_sous_forme_classique($tab_ele['resp'][$i]['tel_prof'])."</td></tr>\n";
								}
							}
							// 20190101
							if($acces_email_responsable) {
								if($tab_ele['resp'][$i]['mel']!='') {
									//$tmp_date=getdate();
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'><th style='text-align: left;'>Courriel:</th><td>".$chaine_acces_modif_tel;
									echo "<a href='mailto:".$tab_ele['resp'][$i]['mel']."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI&amp;body=";
									if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
									echo ",%0d%0aCordialement.'>";
									echo $tab_ele['resp'][$i]['mel'];
									echo "</a>";
									echo "</td></tr>\n";
								}
							}

							if(($tab_ele['resp'][$i]['envoi_bulletin']!='')&&($temoin_classe_a_bulletins>0)) {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Envoi bulletin:</th><td>";
								if(in_array($_SESSION['statut'], array('administrateur', 'scolarite'))) {
									if($tab_ele['resp'][$i]['envoi_bulletin']=="y") {
										echo " <a href='".$_SERVER['PHP_SELF']."?ele_login=".$ele_login."&amp;onglet=responsables&amp;pers_id=".$tab_ele['resp'][$i]['pers_id']."&amp;ele_id=".$tab_ele['ele_id']."&amp;envoi_bulletin_resp_legal_0=n".add_token_in_url()."'";
										echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
										echo "><img src='../images/icons/bulletin.png' width='16' height='16' title=\"Le responsable non légal ".$tab_ele['resp'][$i]['prenom']." ".$tab_ele['resp'][$i]['nom']." est destinataire des bulletins générés dans Gepi.

Cliquez pour supprimer la génération de bulletins à destination de ce responsable.\" /></a>";
									}
									else {
										echo " <a href='".$_SERVER['PHP_SELF']."?ele_login=".$ele_login."&amp;onglet=responsables&amp;pers_id=".$tab_ele['resp'][$i]['pers_id']."&amp;ele_id=".$tab_ele['ele_id']."&amp;envoi_bulletin_resp_legal_0=y".add_token_in_url()."'";
										echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
										echo "><img src='../images/icons/bulletin_barre.png' width='16' height='16' title=\"Le responsable non légal ".$tab_ele['resp'][$i]['prenom']." ".$tab_ele['resp'][$i]['nom']." n'est pas destinataire des bulletins générés dans Gepi.

Cliquez pour activer la génération des bulletins à destination de ce responsable.\" /></a>";
									}
								}
								else {
									if($tab_ele['resp'][$i]['envoi_bulletin']=="y") {
										echo " <img src='../images/icons/bulletin.png' width='16' height='16' title=\"Le responsable non légal ".$tab_ele['resp'][$i]['prenom']." ".$tab_ele['resp'][$i]['nom']." est destinataire des bulletins générés dans Gepi.\" />";
									}
									else {
										echo " <img src='../images/icons/bulletin_barre.png' width='16' height='16' title=\"Le responsable non légal ".$tab_ele['resp'][$i]['prenom']." ".$tab_ele['resp'][$i]['nom']." n'est pas destinataire des bulletins générés dans Gepi.\" />";
									}
								}
							}

							if(!isset($tab_ele['resp'][$i]['etat'])) {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Dispose d'un compte:</th><td>Non</td></tr>\n";
							}
							else {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Dispose d'un compte:</th><td>";
								if(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe'))) {
									echo $tab_ele['resp'][$i]['login'];

									if($tab_ele['resp'][$i]['acces_sp']=="y") {
										echo " <img src='../images/vert.png' width='16' height='16' title=\"Bien que non responsable légal, ce 'responsable/contact' a accès aux informations de l'élève s'il se connecte.\" />";
									}
									else {
										echo " <img src='../images/rouge.png' width='16' height='16' title=\"Ce 'responsable/contact' qui n'est pas responsable légal de l'élève, n'a pas accès aux informations de l'élève s'il se connecte.\" />";
									}
								}
								else {
									echo "Oui";
								}
								echo " (";
								if($tab_ele['resp'][$i]['etat']=='actif') {
									echo "<span style='color:green;'>";
								}
								else{
									echo "<span style='color:red;'>";
								}
								echo $tab_ele['resp'][$i]['etat'];
								echo "</span>)\n";

								if(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe'))) {
									echo "<br />\n";
									echo "<span title=\"Gepi permet selon les configurations plusieurs modes d'authentification:
	- gepi : Authentification sur la base mysql de Gepi,
	- sso : Authentification CAS ou LCS assurée par une autre machine,
	- ldap : Authentification en recherchant la correspondance login/mot_de_passe dans un annuaire LDAP.\">";
									echo "Auth.: ".$tab_ele['resp'][$i]['auth_mode'];
									echo temoin_compte_sso($tab_ele['resp'][$i]['login']);
									echo "</span>";
								}

								if(isset($tab_ele['resp'][$i]['DerniereConnexionResp'])) {
									if(isset($tab_ele['resp'][$i]['DerniereConnexionResp']['START'])) {
										echo "<br />Dernière connexion&nbsp;: ".formate_date($tab_ele['resp'][$i]['DerniereConnexionResp']['START'], 'y');
									}
									elseif(isset($tab_ele['resp'][$i]['DerniereConnexionResp_Echec']['START'])) {
										echo "<br /><span title=\"Cet utilisateur ne s'est jamais connecté avec succès (du moins, si les log n'ont pas été vidés récemment). En revanche, un échec de connexion est constaté à la date indiquée.\">Dernière tentative de connexion&nbsp;: ".formate_date($tab_ele['resp'][$i]['DerniereConnexionResp_Echec']['START'], 'y')."</span>";
									}
									else {
										echo "<br />Dernière connexion&nbsp;: <img src='../images/disabled.png' class='icone20' title=\"Cet utilisateur ne s'est jamais connecté (aussi loin que remontent les journaux de connexion (à savoir : $date_debut_log)).\"/>";
									}
								}

								// 20190101
								if($acces_email_responsable) {
									// Vérifier qui peut avoir accès à cette adresse
									if((isset($tab_ele['resp'][$i]['email']))&&($tab_ele['resp'][$i]['email']!='')&&
									((!isset($tab_ele['resp'][$i]['mel']))||($tab_ele['resp'][$i]['email']!=$tab_ele['resp'][$i]['mel']))) {
										echo "<br />";
										echo "<a href='mailto:".$tab_ele['resp'][$i]['email']."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI&amp;body=";
										if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
										echo ",%0d%0aCordialement.'>";
										echo $tab_ele['resp'][$i]['email'];
										echo "</a>";
									}
								}
								echo "</td></tr>\n";


								if(($_SESSION['statut']=='administrateur')||
									(($_SESSION['statut']=='scolarite')&&(getSettingAOui('ScolResetPassResp')))||
									(($_SESSION['statut']=='cpe')&&(getSettingAOui('CpeResetPassResp')))
								) {
									if($_SESSION['statut']=="administrateur") {
										$alt=$alt*(-1);
										echo "<tr class='lig$alt'><th style='text-align: left;'>Dépannage :</th><td>";
										echo affiche_actions_compte($tab_ele['resp'][$i]['login']);
										if(($tab_ele['resp'][$i]['auth_mode']=='gepi')||
											(($tab_ele['resp'][$i]['auth_mode']=='ldap')&&($gepiSettings['ldap_write_access'] == "yes"))) {
											echo "<br />\n";
											echo affiche_reinit_password($tab_ele['resp'][$i]['login']);
										}
										echo "</td></tr>\n";
									}
									elseif((($tab_ele['resp'][$i]['auth_mode']=='gepi')||
									(($tab_ele['resp'][$i]['auth_mode']=='ldap')&&($gepiSettings['ldap_write_access'] == "yes")))&&
									(acces('/utilisateurs/reset_passwords.php', $_SESSION['statut']))) {
										$alt=$alt*(-1);
										echo "<tr class='lig$alt'><th style='text-align: left;'>Dépannage :</th><td>";
										echo affiche_reinit_password($tab_ele['resp'][$i]['login']);
										echo "</td></tr>\n";
									}
								}

							}

							// 20190101
							if($acces_adresse_responsable) {
								if($tab_ele['resp'][$i]['adr1']!='') {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'><th style='text-align: left;'>Ligne 1 adresse:</th><td>".$tab_ele['resp'][$i]['adr1']."</td></tr>\n";
								}
								if($tab_ele['resp'][$i]['adr2']!='') {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'><th style='text-align: left;'>Ligne 2 adresse:</th><td>".$tab_ele['resp'][$i]['adr2']."</td></tr>\n";
								}
								if($tab_ele['resp'][$i]['adr3']!='') {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'><th style='text-align: left;'>Ligne 3 adresse:</th><td>".$tab_ele['resp'][$i]['adr3']."</td></tr>\n";
								}
								if($tab_ele['resp'][$i]['adr4']!='') {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'><th style='text-align: left;'>Ligne 4 adresse:</th><td>".$tab_ele['resp'][$i]['adr4']."</td></tr>\n";
								}
								if($tab_ele['resp'][$i]['cp']!='') {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'><th style='text-align: left;'>Code postal:</th><td>".$tab_ele['resp'][$i]['cp']."</td></tr>\n";
								}
								if($tab_ele['resp'][$i]['commune']!='') {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'><th style='text-align: left;'>Commune:</th><td>".$tab_ele['resp'][$i]['commune']."</td></tr>\n";
								}
								if($tab_ele['resp'][$i]['pays']!='') {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'><th style='text-align: left;'>Pays:</th><td>".$tab_ele['resp'][$i]['pays']."</td></tr>\n";
								}
							}


							// 20140522
							if(($acces_bulletins=="y")&&($acces_impression_bulletin)) {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Bulletins:</th><td>";

								// Imprimer le bulletin avec l'adresse de ce parent en particulier.

								$type_bulletin_par_defaut="pdf";
								if(getSettingValue("type_bulletin_par_defaut")=="pdf_2016") {
									$type_bulletin_par_defaut="pdf_2016";
								}

								$chaine_intercaler_releve_notes="";
								if($acces_impression_releve_notes) {
									$chaine_intercaler_releve_notes="&intercaler_releve_notes=y&rn_param_auto=y";
								}

								for($loop_per=0;$loop_per<count($tab_ele['periodes']);$loop_per++) {
									$current_id_classe=$tab_ele['periodes'][$loop_per]['id_classe'];
									$current_num_periode=$tab_ele['periodes'][$loop_per]['num_periode'];
									if($loop_per>0) {
										echo " - ";
									}
									echo "<a href='../bulletin/bull_index.php?mode_bulletin=".$type_bulletin_par_defaut.$chaine_intercaler_releve_notes."&type_bulletin=-1&choix_periode_num=fait&valide_select_eleves=y&tab_selection_ele_0_0[0]=".$ele_login."&tab_id_classe[0]=".$current_id_classe."&tab_periode_num[0]=".$current_num_periode."&pers_id=".$tab_ele['resp'][$i]['pers_id']."' target='_blank' title=\"Voir dans un nouvel onglet le bulletin ".casse_mot($type_bulletin_par_defaut, "maj")." de la période ".$current_num_periode.".

Le bulletin sera affiché/généré pour l'adresse responsable de ".$tab_ele['resp'][$i]['civilite']." ".$tab_ele['resp'][$i]['nom']." ".$tab_ele['resp'][$i]['prenom']."\">P".$current_num_periode."</a>";

								}

								echo "</td></tr>\n";
							}

							if(($acces_releves=="y")&&($acces_impression_releve_notes)) {
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'><th style='text-align: left;'>Relevés:</th><td>";

								$type_bulletin_par_defaut="pdf";

								for($loop_per=0;$loop_per<count($tab_ele['periodes']);$loop_per++) {
									$current_id_classe=$tab_ele['periodes'][$loop_per]['id_classe'];
									$current_num_periode=$tab_ele['periodes'][$loop_per]['num_periode'];
									if($loop_per>0) {
										echo " - ";
									}
									echo "<a href='../cahier_notes/visu_releve_notes_bis.php?tab_id_classe[0]=$current_id_classe&choix_periode=periode&tab_periode_num[0]=$current_num_periode&mode_bulletin=$type_bulletin_par_defaut&valide_select_eleves=y&choix_parametres=effectue&tab_selection_ele_0_0[0]=$ele_login&rn_adr_resp[0]=y&pers_id=".$tab_ele['resp'][$i]['pers_id']."&rn_param_auto=y' target='_blank' title=\"Voir dans un nouvel onglet le relevé de notes ".casse_mot($type_bulletin_par_defaut, "maj")." de la période ".$current_num_periode."\">P".$current_num_periode."</a>";
								}

								echo "</td></tr>\n";
							}

							echo "</table>\n";
							echo "</td>\n";
						}
					}
					echo "</tr>\n";
					echo "</table>\n";
				}
			}
			echo "</div>\n";
		}

		//===================================================

		//========================
		// Onglet ENSEIGNEMENTS
		//========================

		if($acces_enseignements=="y") {
			echo "<div id='enseignements' class='onglet' style='";
			if($onglet!="enseignements") {echo " display:none;";}
			echo "background-color: ".$tab_couleur['enseignements']."; ";
			echo "'>";
			echo "<h2>Enseignements suivis par l'".$gepiSettings['denomination_eleve']." ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";

			$acces_edit_group=acces("/groupes/edit_group.php", $_SESSION['statut']);

			$acces_eleve_options=acces("/classes/eleve_options.php", $_SESSION['statut']);
			if(($acces_eleve_options)&&(isset($tab_ele['periodes']))) {
				for($j=0;$j<count($tab_ele['periodes']);$j++) {
					$tab_classe_acces_eleve_options[$j]=true;

					if($_SESSION['statut']=="scolarite") {
						// Tester si le compte scolarité a accès à cette classe...
						// Si ce n'est pas le cas -> intrusion...

						$sql="SELECT 1=1 FROM j_scol_classes jsc WHERE jsc.id_classe='".$tab_ele['periodes'][$j]['id_classe']."' AND jsc.login='".$_SESSION['login']."';";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						if ($test == "0") {
							$tab_classe_acces_eleve_options[$j]=false;
						}
					}
				}
			}

			$acces_edit_eleves=acces("/groupes/edit_eleves.php", $_SESSION['statut']);
			if($acces_edit_eleves) {
				if(($_SESSION['statut']=='cpe')&&(!getSettingAOui('CpeEditElevesGroupes'))) {
					$acces_edit_eleves=false;
				}
				elseif(($_SESSION['statut']=='scolarite')&&(!getSettingAOui('ScolEditElevesGroupes'))) {
					$acces_edit_eleves=false;
				}
			}

			if((!isset($tab_ele['periodes']))||(!isset($tab_ele['groupes']))) {
				echo "<p>Aucune période ou aucun enseignement n'a été trouvé pour cet ".$gepiSettings['denomination_eleve'].".</p>\n";
			}
			else {
				echo "<table class='boireaus' summary='Enseignements'>\n";
				echo "<tr>\n";
				echo "<th>Enseignements";
				if(($acces_eleve_options)&&($tab_classe_acces_eleve_options[0])) {
					echo " <a href='../classes/eleve_options.php?login_eleve=".$ele_login."&amp;id_classe=".$tab_ele['periodes'][0]['id_classe']."' title=\"Modifier la liste des enseignements suivis par cet élève.\"><img src='../images/icons/plus_moins.png' class='icone16' alt='Ajouter/enlever' /></a>";
				}
				echo "</th>\n";
				echo "<th>Professeur(s)</th>\n";
				for($j=0;$j<count($tab_ele['periodes']);$j++) {
					echo "<th>\n";

					if(($acces_eleve_options)&&($tab_classe_acces_eleve_options[$j])) {
						echo "<a href='../classes/eleve_options.php?login_eleve=".$ele_login."&amp;id_classe=".$tab_ele['periodes'][$j]['id_classe']."' title=\"Modifier la liste des enseignements suivis par cet élève.\">".$tab_ele['periodes'][$j]['nom_periode']."</a>";
					}
					else {
						echo $tab_ele['periodes'][$j]['nom_periode'];
					}

					echo "</th>\n";
				}
				echo "</tr>\n";

				$alt=1;
				for($i=0;$i<count($tab_ele['groupes']);$i++) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					if($acces_edit_group) {
						echo "<th><a href='../groupes/edit_group.php?id_groupe=".$tab_ele['groupes'][$i]['id_groupe']."&amp;mode=groupe' title=\"Modifier cet enseignement.\">".htmlspecialchars($tab_ele['groupes'][$i]['name'])."<br /><span style='font-size: x-small;'>".htmlspecialchars($tab_ele['groupes'][$i]['description'])."</span></a></th>\n";
					}
					else {
						echo "<th>".htmlspecialchars($tab_ele['groupes'][$i]['name'])."<br /><span style='font-size: x-small;'>".htmlspecialchars($tab_ele['groupes'][$i]['description'])."</span></th>\n";
					}
					echo "<td>\n";
					$nbre_professeurs = isset($tab_ele['groupes'][$i]['prof']) ? count($tab_ele['groupes'][$i]['prof']) : 0;
					for($j=0;$j<$nbre_professeurs;$j++) {
						if($tab_ele['groupes'][$i]['prof'][$j]['email']!='') {
							echo "<a href='mailto:".$tab_ele['groupes'][$i]['prof'][$j]['email']."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI - [".remplace_accents($tab_ele['nom'],'all')." ".remplace_accents($tab_ele['prenom'],'all')."]&amp;body=";
							if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
							echo ",%0d%0aCordialement.' title=\"Envoyer un email à ce professeur\">";
						}
						if(isset($tab_ele['classe'][0]['id_classe'])) {
							echo affiche_utilisateur($tab_ele['groupes'][$i]['prof'][$j]['prof_login'], $tab_ele['classe'][0]['id_classe']);
						}
						else {
							echo casse_mot($tab_ele['groupes'][$i]['prof'][$j]['prenom'],'majf2');
							echo " ";
							echo casse_mot($tab_ele['groupes'][$i]['prof'][$j]['nom'],'majf2');
						}
						if($tab_ele['groupes'][$i]['prof'][$j]['email']!='') {echo "</a>";}

						echo "<br />\n";
					}
					echo "</td>\n";
					for($j=0;$j<count($tab_ele['periodes']);$j++) {
						echo "<td";
						if(in_array($tab_ele['periodes'][$j]['num_periode'],$tab_ele['groupes'][$i]['periodes'])) {
							echo ">\n";
							//echo "X";

							if($acces_edit_eleves) {
								echo "<a href='../groupes/edit_eleves.php?id_groupe=".$tab_ele['groupes'][$i]['id_groupe']."' title=\"Modifier la liste des élèves inscrits dans cet enseignement.\">".$tab_ele['periodes'][$j]['classe']."</a>";
							}
							else {
								echo $tab_ele['periodes'][$j]['classe'];
							}
						}
						else {
							echo " style='background-color: gray;";
							echo "'>\n";
							echo "&nbsp;";
						}
						echo "</td>\n";
					}
					echo "</tr>\n";
				}
				echo "</table>\n";

				if(count($tab_ele['classe'])==1) {
					$gepi_prof_suivi_current_classe=retourne_denomination_pp($tab_ele['classe'][0]['id_classe']);
					echo "<p><strong>".ucfirst($gepi_prof_suivi_current_classe)."</strong>: ";
				}
				else {
					echo "<p><strong>".ucfirst($gepi_prof_suivi)."</strong>: ";
				}
				for($loop=0;$loop<count($tab_ele['classe']);$loop++) {
					if(isset($tab_ele['classe'][$loop]['pp'])) {
						if($loop>0) {echo ", ";}
						if($tab_ele['classe'][$loop]['pp']['email']!="") {
							$gepi_prof_suivi_current_classe=retourne_denomination_pp($tab_ele['classe'][$loop]['id_classe']);
							//echo "<a href='mailto:".$tab_ele['classe'][$loop]['pp']['email']."'>";
							//echo "<a href='mailto:".$tab_ele['classe'][$loop]['pp']['email']."'>";
							echo "<a href='mailto:".$tab_ele['classe'][$loop]['pp']['email']."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI - [".remplace_accents($tab_ele['nom'],'all')." ".remplace_accents($tab_ele['prenom'],'all')."]&amp;body=";
							if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
							echo ",%0d%0aCordialement.' title=\"Envoyer un email au ".$gepi_prof_suivi_current_classe."\">";
						}
						echo $tab_ele['classe'][$loop]['pp']['civ_nom_prenom'];
						if($tab_ele['classe'][$loop]['pp']['email']!="") {
							echo "</a>";
						}
						echo " (<em>".$tab_ele['classe'][$loop]['classe']."</em>)";
					}
				}
				echo "</p>\n";

				echo "<p><strong>".ucfirst($gepi_cpe_suivi)." chargé(e) du suivi</strong>: ";
				if(isset($tab_ele['cpe'])) {
					if($tab_ele['cpe']['email']!="") {
						//echo "<a href='mailto:".$tab_ele['cpe']['email']."'>";
						//echo "<a href='mailto:".$tab_ele['cpe']['email']."'>";
						echo "<a href='mailto:".$tab_ele['cpe']['email']."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI - [".remplace_accents($tab_ele['nom'],'all')." ".remplace_accents($tab_ele['prenom'],'all')."]&amp;body=";
						if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
						echo ",%0d%0aCordialement.' title=\"Envoyer un email au ".$gepi_cpe_suivi."\">";
					}
					echo $tab_ele['cpe']['civ_nom_prenom'];
					if($tab_ele['cpe']['email']!="") {
						echo "</a>";
					}
				}
				else {
					echo "<span style='color:red'>Aucun CPE</span>";
				}
				echo "</p>\n";

				if($tab_ele['equipe_liste_email']!="") {
					//$tmp_date=getdate();
					//echo "<p>Ecrire un email à <a href='mailto:".$tab_ele['equipe_liste_email']."?subject=GEPI&amp;body=";
					echo "<p>Ecrire un email à <a href='mailto:".$tab_ele['equipe_liste_email']."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI - [".remplace_accents($tab_ele['nom'],'all')." ".remplace_accents($tab_ele['prenom'],'all')."]&amp;body=";
					if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
					if(preg_match("/,/",$tab_ele['equipe_liste_email'])) {echo " à tou(te)s";}
					echo ",%0d%0aCordialement.'>tous les enseignants et au ".$gepi_cpe_suivi." de l'élève</a>.</p>\n";
				}
			}
			echo "</div>\n";
		}
		//===================================================

		//===================
		// Onglet BULLETINS
		//===================

		$tab_onglets_bull=array();
		if($acces_bulletins=="y") {
			echo "<div id='bulletins' class='onglet' style='";
			if($onglet!="bulletins") {echo " display:none;";}
			echo "background-color: ".$tab_couleur['bulletins']."; ";
			echo "'>";

			if(($acces_impression_bulletin)&&(isset($tab_ele['periodes']))) {
				echo "<div style='float:right; width:9em; text-align:center;' class='fieldset_opacite50' title=\"Imprimer les bulletins\">";

				$type_bulletin_par_defaut="pdf";
				if(getSettingValue("type_bulletin_par_defaut")=="pdf_2016") {
					$type_bulletin_par_defaut="pdf_2016";
				}

				$chaine_intercaler_releve_notes="";
				if($acces_impression_releve_notes) {
					$chaine_intercaler_releve_notes="&intercaler_releve_notes=y&rn_param_auto=y";
				}

				$chaine_periodes="";
				$current_id_classe=$tab_ele['periodes'][0]['id_classe'];
				$chaine_preselection_eleve="";
				for($loop_per=0;$loop_per<count($tab_ele['periodes']);$loop_per++) {
					$chaine_periodes.="&amp;tab_periode_num[$loop_per]=".$tab_ele['periodes'][$loop_per]['num_periode'];
					$chaine_preselection_eleve.="&amp;preselection_eleves[".$tab_ele['periodes'][$loop_per]['num_periode']."]=|".$ele_login."|";
				}
				//valide_select_eleves=y&
				echo "<a href='../bulletin/bull_index.php?mode_bulletin=".$type_bulletin_par_defaut.$chaine_intercaler_releve_notes."&type_bulletin=-1&choix_periode_num=fait&tab_selection_ele_0_0[0]=".$ele_login."&tab_id_classe[0]=".$current_id_classe.$chaine_periodes.$chaine_preselection_eleve."' target='_blank' title=\"Voir dans un nouvel onglet les bulletins de cet élève.\"><img src='../images/icons/print.png' class='icone16' alt='Imprimer' /></a>";

				for($loop_per=0;$loop_per<count($tab_ele['periodes']);$loop_per++) {
					$current_id_classe=$tab_ele['periodes'][$loop_per]['id_classe'];
					$current_num_periode=$tab_ele['periodes'][$loop_per]['num_periode'];
					echo " - <a href='../bulletin/bull_index.php?mode_bulletin=".$type_bulletin_par_defaut.$chaine_intercaler_releve_notes."&type_bulletin=-1&choix_periode_num=fait&valide_select_eleves=y&tab_selection_ele_0_0[0]=".$ele_login."&tab_id_classe[0]=".$current_id_classe."&tab_periode_num[0]=".$current_num_periode."' target='_blank' title=\"Voir dans un nouvel onglet le bulletin ".casse_mot($type_bulletin_par_defaut, "maj")." de la période ".$current_num_periode."\">P".$current_num_periode."</a>";
					/*
					A AJOUTER

					&intercaler_releve_notes=y

					A CREER/PRENDRE EN COMPTE:
					&param_rn_defaut=y
					pour récupérer les paramètres de la classe.
					*/
				}

				echo "</div>";
			}

			// 20161205: Afficher des liens Bull Simp Ele/Clas
			if($acces_bulletin_simple) {
				if(count($tab_ele["classe"])>0) {
					if(count($tab_ele["classe"])==1) {
						echo "<div style='float:right; width:16px; text-align:center;margin-right:3px;' class='fieldset_opacite50' title=\"Bulletins simplifiés\">";
						// Le statut administrateur n'a pas accès à prepa_conseil/index3.php, mais seulement à prepa_conseil/edit_limite.php
						//echo "<a href='../prepa_conseil/index3.php?id_classe=".$tab_ele["classe"][0]["id_classe"]."' target='_blank' title=\"Voir dans un nouvel onglet les bulletins simplifiés.\"><img src='../images/icons/bulletin_16.png' class='icone16' alt='BullSimp' /></a>";
						echo "<a href='../prepa_conseil/edit_limite.php?id_classe=".$tab_ele["classe"][0]["id_classe"]."&amp;periode1=1&amp;periode2=".count($tab_ele['classe'][0]['periodes'])."&amp;choix_edit=2&amp;login_eleve=".$ele_login."' target='_blank' title=\"Voir dans un nouvel onglet les bulletins simplifiés.\"><img src='../images/icons/bulletin_16.png' class='icone16' alt='BullSimp' /></a>";
						echo "</div>";
					}
					else {
						for($loop_classe=0;$loop_classe<count($tab_ele["classe"]);$loop_classe++) {
							echo "<div style='float:right; width:16px; text-align:center;margin-right:3px;' class='fieldset_opacite50' title=\"Bulletins simplifiés\">";
							//echo "<a href='../prepa_conseil/index3.php?id_classe=".$tab_ele["classe"][$loop_classe]["id_classe"]."' target='_blank' title=\"Voir dans un nouvel onglet les bulletins simplifiés.\">".$tab_ele["classe"][$loop_classe]["classe"]."<img src='../images/icons/bulletin_16.png' class='icone16' alt='BullSimp' /></a>";
							echo "<a href='../prepa_conseil/edit_limite.php?id_classe=".$tab_ele["classe"][$loop_classe]["id_classe"]."&amp;periode1=1&amp;periode2=".count($tab_ele['classe'][$loop_classe]['periodes'])."&amp;choix_edit=2&amp;login_eleve=".$ele_login."' target='_blank' title=\"Voir dans un nouvel onglet les bulletins simplifiés.\">".$tab_ele["classe"][$loop_classe]["classe"]."<img src='../images/icons/bulletin_16.png' class='icone16' alt='BullSimp' /></a>";
							echo "</div>";
						}
					}
				}
			}

			echo "<h2>Bulletins de l'".$gepiSettings['denomination_eleve']." ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";

			$sql="SELECT MIN(periode) AS min_per, MAX(periode) AS max_per FROM matieres_notes WHERE login='".$ele_login."';";
			//echo "$sql<br />";
			$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_per)>0) {
				$lig_per=mysqli_fetch_object($res_per);
				// Afficher les trois trimestres sur le bulletin simplifié affiche des infos erronées quant au nom des professeurs si l'élève a changé de classe.
				$periode_numero_1=$lig_per->min_per;
				$periode_numero_2=$lig_per->max_per;

				//echo "\$periode_numero_1=$periode_numero_1<br />";
				//echo "\$periode_numero_2=$periode_numero_2<br />";

				if(($periode_numero_1!='')&&($periode_numero_2!='')) {

					include "../lib/bulletin_simple.inc.php";

					//$tab_onglets_bull=array();
					for($n_per=$periode_numero_1;$n_per<=$periode_numero_2;$n_per++) {
						$periode1=$n_per;
						$tab_onglets_bull[]="bulletin_$periode1";

						echo "<div id='t_bulletin_$periode1' class='t_onglet' style='";
						if(isset($onglet2)) {
							if($onglet2=="bulletin_$periode1") {
								echo "border-bottom-color: ".$tab_couleur['bulletin']."; ";
							}
						}
						else {
							if($n_per==$periode_numero_1) {
								echo "border-bottom-color: ".$tab_couleur['bulletin']."; ";
							}
						}
						echo "background-color: ".$tab_couleur['bulletin']."; ";
						echo "'>";

						echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=bulletins&amp;onglet2=bulletin_$periode1".$chaine_quitter_page_ou_non."' onclick=\"affiche_onglet('bulletins');affiche_onglet_bull('bulletin_$periode1');return false;\">";
						echo "Période $periode1";
						echo "</a>";
						echo "</div>\n";

					}

					//====================================
					echo "<div style='clear:both;'></div>\n";
					//====================================

					for($n_per=$periode_numero_1;$n_per<=$periode_numero_2;$n_per++) {
						$periode1=$n_per;
						$periode2=$n_per;

						$index_per=-1;

						for($loop=0;$loop<count($tab_ele['periodes']);$loop++) {
							if($tab_ele['periodes'][$loop]['num_periode']==$n_per) {
								$index_per=$loop;
								break;
							}
						}

						$id_classe=$tab_ele['periodes'][$index_per]['id_classe'];

						// Boucle sur la liste des classes de l'élève pour que $id_classe soit fixé avant l'appel: periodes.inc.php
						include "../lib/periodes.inc.php";


						// On teste la présence d'au moins un coeff pour afficher la colonne des coef
						$test_coef = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
						// Apparemment, $test_coef est réaffecté plus loin dans un des include()
						$nb_coef_superieurs_a_zero=$test_coef;

						// On regarde si on affiche les catégories de matières
						$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
						if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}

						// Si le rang des élèves est demandé, on met à jour le champ rang de la table matieres_notes
						$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
						if ($affiche_rang == 'y') {
							$periode_num=$periode1;
							while ($periode_num < $periode2+1) {
								include "../lib/calcul_rang.inc.php";
								$periode_num++;
							}
						}

						$coefficients_a_1="non";
						$affiche_graph = 'n';

						//unset($tab_moy_gen);
						//unset($tab_moy_cat_classe);
						for($loop=$periode1;$loop<=$periode2;$loop++) {
							$periode_num=$loop;
							include "../lib/calcul_moy_gen.inc.php";
							//$tab_moy_gen[$loop]=$moy_generale_classe;

							//======================================================================
							$tab_moy['periodes'][$periode_num]=array();
							$tab_moy['periodes'][$periode_num]['tab_login_indice']=$tab_login_indice;         // [$login_eleve]
							$tab_moy['periodes'][$periode_num]['moy_gen_eleve']=$moy_gen_eleve;               // [$i]
							$tab_moy['periodes'][$periode_num]['moy_gen_eleve1']=$moy_gen_eleve1;             // [$i]
							//$tab_moy['periodes'][$periode_num]['moy_gen_classe1']=$moy_gen_classe1;           // [$i]
							$tab_moy['periodes'][$periode_num]['moy_generale_classe']=$moy_generale_classe;
							$tab_moy['periodes'][$periode_num]['moy_generale_classe1']=$moy_generale_classe1;
							$tab_moy['periodes'][$periode_num]['moy_max_classe']=$moy_max_classe;
							$tab_moy['periodes'][$periode_num]['moy_min_classe']=$moy_min_classe;

							// Il faudrait récupérer/stocker les catégories?
							$tab_moy['periodes'][$periode_num]['moy_cat_eleve']=$moy_cat_eleve;               // [$i][$cat]
							$tab_moy['periodes'][$periode_num]['moy_cat_classe']=$moy_cat_classe;             // [$i][$cat]
							$tab_moy['periodes'][$periode_num]['moy_cat_min']=$moy_cat_min;                   // [$i][$cat]
							$tab_moy['periodes'][$periode_num]['moy_cat_max']=$moy_cat_max;                   // [$i][$cat]

							$tab_moy['periodes'][$periode_num]['quartile1_classe_gen']=$quartile1_classe_gen;
							$tab_moy['periodes'][$periode_num]['quartile2_classe_gen']=$quartile2_classe_gen;
							$tab_moy['periodes'][$periode_num]['quartile3_classe_gen']=$quartile3_classe_gen;
							$tab_moy['periodes'][$periode_num]['quartile4_classe_gen']=$quartile4_classe_gen;
							$tab_moy['periodes'][$periode_num]['quartile5_classe_gen']=$quartile5_classe_gen;
							$tab_moy['periodes'][$periode_num]['quartile6_classe_gen']=$quartile6_classe_gen;
							$tab_moy['periodes'][$periode_num]['place_eleve_classe']=$place_eleve_classe;

							$tab_moy['periodes'][$periode_num]['current_eleve_login']=$current_eleve_login;   // [$i]
							//$tab_moy['periodes'][$periode_num]['current_group']=$current_group;
							if($loop==$periode1) {
								$tab_moy['current_group']=$current_group;                                     // [$j]
							}
							$tab_moy['periodes'][$periode_num]['current_eleve_note']=$current_eleve_note;     // [$j][$i]
							$tab_moy['periodes'][$periode_num]['current_eleve_statut']=$current_eleve_statut; // [$j][$i]
							//$tab_moy['periodes'][$periode_num]['current_group']=$current_group;
							$tab_moy['periodes'][$periode_num]['current_coef']=$current_coef;                 // [$j]
							$tab_moy['periodes'][$periode_num]['current_classe_matiere_moyenne']=$current_classe_matiere_moyenne; // [$j]

							$tab_moy['periodes'][$periode_num]['current_coef_eleve']=$current_coef_eleve;     // [$i][$j] ATTENTION
							$tab_moy['periodes'][$periode_num]['moy_min_classe_grp']=$moy_min_classe_grp;     // [$j]
							$tab_moy['periodes'][$periode_num]['moy_max_classe_grp']=$moy_max_classe_grp;     // [$j]
							if(isset($current_eleve_rang)) {
								// $current_eleve_rang n'est pas renseigné si $affiche_rang='n'
								$tab_moy['periodes'][$periode_num]['current_eleve_rang']=$current_eleve_rang; // [$j][$i]
							}
							$tab_moy['periodes'][$periode_num]['quartile1_grp']=$quartile1_grp;               // [$j]
							$tab_moy['periodes'][$periode_num]['quartile2_grp']=$quartile2_grp;               // [$j]
							$tab_moy['periodes'][$periode_num]['quartile3_grp']=$quartile3_grp;               // [$j]
							$tab_moy['periodes'][$periode_num]['quartile4_grp']=$quartile4_grp;               // [$j]
							$tab_moy['periodes'][$periode_num]['quartile5_grp']=$quartile5_grp;               // [$j]
							$tab_moy['periodes'][$periode_num]['quartile6_grp']=$quartile6_grp;               // [$j]
							$tab_moy['periodes'][$periode_num]['place_eleve_grp']=$place_eleve_grp;           // [$j][$i]

							$tab_moy['periodes'][$periode_num]['current_group_effectif_avec_note']=$current_group_effectif_avec_note; // [$j]
							//======================================================================

						}

						$display_moy_gen=sql_query1("SELECT display_moy_gen FROM classes WHERE id='".$id_classe."'");

						$cette_etiquette_d_onglet_en_gras="y";
						echo "<div id='bulletin_$periode1' class='onglet' style='";
						echo " background-color: ".$tab_couleur['bulletin'].";";
						if((isset($onglet2))&&(mb_substr($onglet2,0,9)=='bulletin_')) {
							if('bulletin_'.$n_per!=$onglet2) {
								echo " display:none;";
								$cette_etiquette_d_onglet_en_gras="n";
							}
						}
						else {
							if($n_per!=$periode_numero_1) {
								echo " display:none;";
								$cette_etiquette_d_onglet_en_gras="n";
							}
						}
						echo "'>\n";

						if($cette_etiquette_d_onglet_en_gras=="y") {
							echo "<script type='text/javascript'>
	document.getElementById('t_bulletin_$n_per').style.fontWeight='bold';
	document.getElementById('t_bulletin_$n_per').style.borderBottomColor='white';
	document.getElementById('t_bulletin_$n_per').style.borderBottomWidth='0px';
</script>\n";
						}

						//bulletin($ele_login,1,1,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories,'y');
						bulletin($tab_moy,$ele_login,1,1,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories,'y');


						if(acces('/visualisation/draw_graphe.php', $_SESSION['statut'])) {
							// Si on donne un jour un accès à cette page pour les parents/élèves, il faudra ajouter des filtres
							//============================================================
							// Graphes

							unset($graphe_chaine_etiquette);
							unset($graphe_chaine_temp);
							unset($graphe_chaine_mgen);

							for($loop=$periode1;$loop<=$periode2;$loop++) {
								$graphe_chaine_etiquette="";
								$graphe_chaine_temp_eleve="";
								$graphe_chaine_temp_classe="";
								$graphe_chaine_mgen_eleve="";
								$graphe_chaine_mgen_classe="";
								$graphe_chaine_seriemin="";
								$graphe_chaine_seriemax="";

								// Recherche de l'indice de l'élève:
								$eleve_trouve="n";
								for($i=0;$i<count($tab_moy['periodes'][$loop]['current_eleve_login']);$i++) {
									if($tab_moy['periodes'][$loop]['current_eleve_login'][$i]==$ele_login) {
										$eleve_trouve="y";
										break;
									}
								}

								if($eleve_trouve=="y") {
									$compteur_groupes_eleve=0;
									for($j=0;$j<count($tab_moy['current_group']);$j++) {
										$current_group=$tab_moy['current_group'][$j];

										if(in_array($ele_login, $current_group["eleves"][$loop]["list"])) {
											$current_group=$tab_moy['current_group'][$j];

											if($compteur_groupes_eleve>0) {
												$graphe_chaine_etiquette.="|";
												$graphe_chaine_temp_classe.="|";
												$graphe_chaine_seriemin.="|";
												$graphe_chaine_seriemax.="|";
												$graphe_chaine_temp_eleve.="|";
											}
											/*
											if($graphe_chaine_etiquette!="") {$graphe_chaine_etiquette.="|";}
											$graphe_chaine_etiquette.=$current_group["matiere"]["matiere"];

											if($graphe_chaine_temp_classe!="") {$graphe_chaine_temp_classe.="|";}
											$graphe_chaine_temp_classe.=$tab_moy['periodes'][$loop]['current_classe_matiere_moyenne'][$j];

											if($graphe_chaine_seriemin!="") {$graphe_chaine_seriemin.="|";}
											$graphe_chaine_seriemin.=$tab_moy['periodes'][$loop]['moy_min_classe_grp'][$j];

											if($graphe_chaine_seriemax!="") {$graphe_chaine_seriemax.="|";}
											$graphe_chaine_seriemax.=$tab_moy['periodes'][$loop]['moy_max_classe_grp'][$j];

											if($graphe_chaine_temp_eleve!="") {$graphe_chaine_temp_eleve.="|";}
											*/

											$graphe_chaine_etiquette.=$current_group["matiere"]["matiere"];
											$graphe_chaine_temp_classe.=$tab_moy['periodes'][$loop]['current_classe_matiere_moyenne'][$j];
											$graphe_chaine_seriemin.=$tab_moy['periodes'][$loop]['moy_min_classe_grp'][$j];
											$graphe_chaine_seriemax.=$tab_moy['periodes'][$loop]['moy_max_classe_grp'][$j];

											if(isset($tab_moy['periodes'][$loop]['current_eleve_note'][$j][$i])) {
												//$graphe_chaine_temp_eleve.=$tab_moy['periodes'][$loop]['current_eleve_note'][$j][$i];
												if($tab_moy['periodes'][$loop]['current_eleve_statut'][$j][$i]=='') {
													$graphe_chaine_temp_eleve.=$tab_moy['periodes'][$loop]['current_eleve_note'][$j][$i];
												}
												else {
													$graphe_chaine_temp_eleve.=$tab_moy['periodes'][$loop]['current_eleve_statut'][$j][$i];
												}
											}
											$compteur_groupes_eleve++;
										}
									}

									//echo "\$tab_moy['periodes'][$loop]['moy_gen_eleve'][$i]=".$tab_moy['periodes'][$loop]['moy_gen_eleve'][$i]."<br />";
									$graphe_chaine_mgen_eleve=$tab_moy['periodes'][$loop]['moy_gen_eleve'][$i];
									if(is_numeric($tab_moy['periodes'][$loop]['moy_gen_eleve'][$i])) {
										$graphe_chaine_mgen_eleve=number_format($tab_moy['periodes'][$loop]['moy_gen_eleve'][$i],1);
									}
									//echo "\$tab_moy['periodes'][$loop]['moy_generale_classe']=".$tab_moy['periodes'][$loop]['moy_generale_classe']."<br />";
									$graphe_chaine_mgen_classe=$tab_moy['periodes'][$loop]['moy_generale_classe'];
									if(is_numeric($tab_moy['periodes'][$loop]['moy_generale_classe'])) {
										$graphe_chaine_mgen_classe=number_format($tab_moy['periodes'][$loop]['moy_generale_classe'],1);
									}


									$graphe_chaine_periode[$loop]=
									"temp1=".$graphe_chaine_temp_eleve."&amp;".
									"temp2=".$graphe_chaine_temp_classe."&amp;".
									"etiquette=".$graphe_chaine_etiquette."&amp;".
									"titre=Graphe&amp;".
									"v_legend1=".$ele_login."&amp;".
									"v_legend2=moyclasse&amp;".
									"compteur=0&amp;".
									"nb_series=2&amp;".
									"id_classe=".$id_classe."&amp;".
									"largeur_graphe=600&amp;".
									"hauteur_graphe=400&amp;".
									"taille_police=4&amp;".
									"epaisseur_traits=2&amp;".
									"epaisseur_croissante_traits_periodes=non&amp;".
									"tronquer_nom_court=4&amp;".
									"temoin_image_escalier=oui&amp;".
									"seriemin=".$graphe_chaine_seriemin."&amp;".
									"seriemax=".$graphe_chaine_seriemax;
									if(isset($graphe_chaine_mgen_eleve)) {
										$graphe_chaine_periode[$loop].="&amp;"."mgen1=".$graphe_chaine_mgen_eleve;
									}
									if(isset($graphe_chaine_mgen_classe)) {
										$graphe_chaine_periode[$loop].="&amp;"."mgen2=".$graphe_chaine_mgen_classe;
									}
								}
							}

							for($loop=$periode1;$loop<=$periode2;$loop++) {
								$titre_infobulle=$tab_ele['nom']." ".$tab_ele['prenom']." (Période $loop)";

								$texte_infobulle="<div align='center'>\n";
								$texte_infobulle.="<img src='../visualisation/draw_graphe.php?".$graphe_chaine_periode[$loop]."' width='600' height='400' alt=\"".$tab_ele['nom']." ".$tab_ele['prenom']." (Période $loop)\" title=\"".$tab_ele['nom']." ".$tab_ele['prenom']." (Période $loop)\" />";
								$texte_infobulle.="<br />\n";
								$texte_infobulle.="</div>\n";

								$tabdiv_infobulle[]=creer_div_infobulle('graphe_periode_'.$loop.'_'.$ele_login,$titre_infobulle,"",$texte_infobulle,"",'610px','410px','y','y','n','n');

								echo "<a href='../visualisation/draw_graphe.php?".
								$graphe_chaine_periode[$loop].
								"' onclick=\"afficher_div('graphe_periode_".$loop."_".$ele_login."','y',20,20); return false;\" target='_blank'>Graphe période ".$loop."</a>";
							}
							echo "<br />\n";
							//============================================================
						}

						echo "</div>\n";
					}
				}
				else {
					// Il ne faut pas proposer de bulletin
					echo "<p>Aucun bulletin à ce jour.</p>\n";
				}
			}
			else {
				// Il ne faut pas proposer de bulletin
				echo "<p>Aucun bulletin à ce jour.</p>\n";
			}

			echo "</div>\n";
		}
		//===================================================

		//===================================================

		//==========================
		// Onglet RELEVES DE NOTES
		//==========================

		$tab_onglets_rel=array();
		if($acces_releves=="y") {
			echo "<div id='releves' class='onglet' style='";
			if($onglet!="releves") {echo " display:none;";}
			echo "background-color: ".$tab_couleur['releves']."; ";
			echo "'>";

			$sql="SELECT MIN(ccn.periode) AS min_per, MAX(ccn.periode) AS max_per FROM cn_cahier_notes ccn,j_eleves_groupes jeg WHERE jeg.login='".$ele_login."' AND jeg.id_groupe=ccn.id_groupe AND jeg.periode=ccn.periode;";
			//echo "$sql<br />";
			$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_per)>0) {
				$lig_per=mysqli_fetch_object($res_per);
				$periode_numero_1=$lig_per->min_per;
				$periode_numero_2=$lig_per->max_per;
			}
			else {
				// Il ne faut pas proposer de relevé de notes?
			}

			if($acces_impression_releve_notes) {
				echo "<div style='float:right; width:9em; text-align:center;' class='fieldset_opacite50' title=\"Imprimer les relevés de notes\">";

				$type_bulletin_par_defaut="pdf";

				$chaine_periodes="";
				$current_id_classe=$tab_ele['periodes'][0]['id_classe'];
				$chaine_preselection_eleve="";
				for($loop_per=0;$loop_per<count($tab_ele['periodes']);$loop_per++) {
					$chaine_periodes.="&amp;tab_periode_num[$loop_per]=".$tab_ele['periodes'][$loop_per]['num_periode'];
					//$chaine_preselection_eleve.="&amp;preselection_eleves[".$tab_ele['periodes'][$loop_per]['num_periode']."]=|".$ele_login."|";
					$chaine_preselection_eleve.="&amp;tab_selection_ele_0_".$loop_per."[]=".$ele_login."";
				}

				echo "<a href='../cahier_notes/visu_releve_notes_bis.php?tab_id_classe[0]=".$current_id_classe.$chaine_periodes.$chaine_preselection_eleve."&choix_periode=periode' target='_blank' title=\"Voir dans un nouvel onglet les relevés de notes de cet élève.\"><img src='../images/icons/print.png' class='icone16' alt='Imprimer' /></a>";
				//&valide_select_eleves=y&mode_bulletin=pdf&choix_parametres=effectue&tab_selection_ele_0_0[0]=chandelc&rn_param_auto=y

				for($loop_per=0;$loop_per<count($tab_ele['periodes']);$loop_per++) {
					$current_id_classe=$tab_ele['periodes'][$loop_per]['id_classe'];
					$current_num_periode=$tab_ele['periodes'][$loop_per]['num_periode'];
					echo " - <a href='../cahier_notes/visu_releve_notes_bis.php?tab_id_classe[0]=$current_id_classe&choix_periode=periode&tab_periode_num[0]=$current_num_periode&mode_bulletin=$type_bulletin_par_defaut&valide_select_eleves=y&choix_parametres=effectue&tab_selection_ele_0_0[0]=$ele_login&rn_param_auto=y' target='_blank' title=\"Voir dans un nouvel onglet le relevé de notes ".casse_mot($type_bulletin_par_defaut, "maj")." de la période ".$current_num_periode."\">P".$current_num_periode."</a>";
				}

				echo "</div>";
			}

			echo "<h2>Relevés de notes de l'".$gepiSettings['denomination_eleve']." ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";

			$id_releve_1="";

			//echo "\$periode_numero_1=$periode_numero_1<br />";
			//echo "\$periode_numero_2=$periode_numero_2<br />";

			if(($periode_numero_1!="")&&($periode_numero_2!="")) {
				//$tab_onglets_rel=array();
				for($n_per=$periode_numero_1;$n_per<=$periode_numero_2;$n_per++) {
					$periode1=$n_per;
					$tab_onglets_rel[]="releve_$periode1";

					echo "<div id='t_releve_$periode1' class='t_onglet' style='";
					if(isset($onglet2)) {
						if($onglet2=="releve_$periode1") {
							echo "border-bottom-color: ".$tab_couleur['releve']."; ";
						}
					}
					else {
						if($n_per==$periode_numero_1) {
							echo "border-bottom-color: ".$tab_couleur['releve']."; ";
						}
					}
					echo "background-color: ".$tab_couleur['releve']."; ";
					echo "'>";
					echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=releves&amp;onglet2=releve_$periode1".$chaine_quitter_page_ou_non."' onclick=\"affiche_onglet('releves');affiche_onglet_rel('releve_$periode1');return false;\">";
					echo "Période $periode1";
					echo "</a>";
					echo "</div>\n";
				}

				//====================================
				echo "<div style='clear:both;'></div>\n";
				//====================================

				// Liste des infos à faire apparaitre sur le relevé de notes:
				// Si des appréciations ont été saisies et que dans les paramètres du devoir il est précisé qu'elles doivent être visibles des parents, il n'y a pas de raison de ne pas les afficher
				$tab_ele['rn_app']='y';
				// Non pris en compte: directement extrait dans visu_ele_func.lib.php
				//$tab_ele['rn_col_moy']='y';

				/*
				$tab_ele['rn_app']='n';
				$tab_ele['rn_nomdev']='y';
				$tab_ele['rn_toutcoefdev']='y';
				$tab_ele['rn_coefdev_si_diff']='y';
				$tab_ele['rn_datedev']='y';
				$tab_ele['rn_sign_chefetab']='n';
				$tab_ele['rn_sign_pp']='n';
				$tab_ele['rn_sign_resp']='n';
				$tab_ele['rn_formule']='';
				*/

				for($n_per=$periode_numero_1;$n_per<=$periode_numero_2;$n_per++) {
					$periode1=$n_per;
					$periode2=$n_per;

					$index_per=-1;
					for($loop=0;$loop<count($tab_ele['periodes']);$loop++) {
						if($tab_ele['periodes'][$loop]['num_periode']==$n_per) {
							$index_per=$loop;
							break;
						}
					}

					if($index_per!=-1) {
						// On récupère la classe de l'élève sur la période considérée
						$id_classe=$tab_ele['periodes'][$index_per]['id_classe'];
						//echo "\$id_classe=$id_classe<br />";

						// Boucle sur la liste des classes de l'élève pour que $id_classe soit fixé avant l'appel: periodes.inc.php
						include "../lib/periodes.inc.php";

						// On teste la présence d'au moins un coeff pour afficher la colonne des coef
						$test_coef = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
						//echo "\$test_coef=$test_coef<br />";
						// Apparemment, $test_coef est réaffecté plus loin dans un des include()
						$nb_coef_superieurs_a_zero=$test_coef;

						// On regarde si on affiche les catégories de matières
						$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
						if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}

						$cette_etiquette_d_onglet_en_gras="y";
						echo "<div id='releve_$periode1' class='onglet' style='";
						echo " background-color: ".$tab_couleur['releve'].";";
						if((isset($onglet2))&&(mb_substr($onglet2,0,7)=='releve_')) {
							if('releve_'.$n_per!=$onglet2) {
								echo " display:none;";
								$cette_etiquette_d_onglet_en_gras="n";
							}
						}
						else {
							if($n_per!=$periode_numero_1) {
								echo " display:none;";
								$cette_etiquette_d_onglet_en_gras="n";
							}
						}
						echo "'>\n";
						if($cette_etiquette_d_onglet_en_gras=="y") {
							echo "<script type='text/javascript'>
	document.getElementById('t_releve_$n_per').style.fontWeight='bold';
	document.getElementById('t_releve_$n_per').style.borderBottomColor='white';
	document.getElementById('t_releve_$n_per').style.borderBottomWidth='0px';
</script>\n";
						}
						//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						// IL MANQUE UN PAQUET D'INITIALISATIONS POUR LES APPELS global DANS releve_html()
						//echo "<pre>";
						//print_r($tab_ele);
						//echo "</pre>";
						releve_html($tab_ele,$id_classe,$periode1,$index_per);
						//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						echo "</div>\n";
					}
				}
			}
			else {
				echo "<p>Aucune note à ce jour.</p>\n";
			}
			echo "</div>\n";
		}

		//=============================================
		// 20170621
		if((isset($tab_ele['classe']))&&(count($tab_ele['classe'])>0)) {
			$id_derniere_classe=$tab_ele['classe'][count($tab_ele['classe'])-1]['id_classe'];
		}
		//=============================================

		//========================
		// Onglet CAHIER DE TEXTES
		//========================

		if($acces_cdt=="y") {
			$contexte_affichage_docs_joints="visu_eleve";
			$lignes_cdt_mail="";

			$mail_dest=isset($_POST['mail_dest']) ? $_POST['mail_dest'] : NULL;
			$envoi_mail=isset($_POST['envoi_mail']) ? $_POST['envoi_mail'] : "n";

			echo "<div id='cdt' class='onglet' style='";
			if($onglet!="cdt") {echo " display:none;";}
			echo "background-color: ".$tab_couleur['cdt']."; ";
			echo "'>";
			if(isset($id_derniere_classe)) {
				if(acces("/cahier_texte_2/consultation2.php", $_SESSION['statut'])) {
					//echo "c";
					echo "<div style='float:right; width:16'><a href='../cahier_texte_2/consultation2.php?mode=eleve&amp;login_eleve=$ele_login&amp;id_classe=$id_derniere_classe' title='Affichage semaine du cahier de textes'><img src='../images/icons/date.png' width='16' height='16' /></a></div>\n";
				}
			}

			$chaine_tmp="<h2>Cahier de textes de l'".$gepiSettings['denomination_eleve']." ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";
			$lignes_cdt_mail.=$chaine_tmp;
			echo $chaine_tmp;

			echo "<div id='div_compte_rendu_envoi_mail' style='text-align:center;'></div>";

			echo "<p align='center'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=cdt&amp;day=$j_sem_prec&amp;month=$m_sem_prec&amp;year=$y_sem_prec".$chaine_quitter_page_ou_non."'><img src='../images/icons/back.png' width='16' height='16' alt='Semaine précédente' /></a> ";

			$chaine_tmp="Du ".jour_en_fr(date("D",$date_ct1))." ".date("d/m/Y",$date_ct1)." au ".jour_en_fr(date("D",$date_ct2))." ".date("d/m/Y",$date_ct2);
			$lignes_cdt_mail.=$chaine_tmp;
			echo $chaine_tmp;

			echo " <a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=cdt&amp;day=$j_sem_suiv&amp;month=$m_sem_suiv&amp;year=$y_sem_suiv".$chaine_quitter_page_ou_non."'><img src='../images/icons/forward.png' width='16' height='16' alt='Semaine suivante' /></a>";
			echo "</p>\n";

			$couleur_dev="#FFCCCF";
			$couleur_entry="#C7FF99";

			echo "<div align='center'>\n";
			$chaine_tmp="<table class='boireaus' border='1' summary='CDT'>\n";
			$chaine_tmp.="<tr><th>Date</th><th>Travail à effectuer</th><th>Compte rendu de séance</th></tr>\n";

			// On compte les entrées du cdt
			if (isset($tab_ele['cdt'])) {
				$nbre_cdt = count($tab_ele['cdt']);
			}else{
				$nbre_cdt = 0;
			}

			for($j=0;$j<$nbre_cdt;$j++) {

				$chaine_tmp.="<tr>\n";
				$chaine_tmp.="<td>\n";
				//echo "Date ".jour_en_fr(date("D",$tab_ele['cdt'][$j]['date_ct']))." ".date("d/m/Y",$tab_ele['cdt'][$j]['date_ct'])."<br />\n";
				$chaine_tmp.=ucfirst(jour_en_fr(date("D",$tab_ele['cdt'][$j]['date_ct'])))." ".date("d/m/Y",$tab_ele['cdt'][$j]['date_ct'])."<br />\n";
				$chaine_tmp.="</td>\n";

				//echo "<td valign='top' style='padding:3px;'>\n";
				$chaine_tmp.="<td valign='top'>\n";
				//echo "<div style='border:1px solid black; padding:2px;'>\n";
				if(isset($tab_ele['cdt'][$j]['dev'])) {
					for($k=0;$k<count($tab_ele['cdt'][$j]['dev']);$k++) {
						//echo "<div style='border:1px solid black; background-color: lightyellow; margin:1px; display:block; width:40%;'>\n";
						$chaine_tmp.="<table class='boireaus' border='1' style='margin:3px; width:100%;' summary='CDT'>\n";
						$chaine_tmp.="<tr style='background-color:$couleur_dev;'>\n";
						$chaine_tmp.="<td>\n";
						$chaine_tmp.=$tab_ele['groupes'][$tab_ele['index_grp'][$tab_ele['cdt'][$j]['dev'][$k]['id_groupe']]]['matiere_nom_complet']." <span style='font-size:x-small;'>(".$tab_ele['groupes'][$tab_ele['index_grp'][$tab_ele['cdt'][$j]['dev'][$k]['id_groupe']]]['name'].")</span>";
						$chaine_tmp.="</td>\n";

						$chaine_tmp.="<td>\n";
						//echo "Prof ".$tab_ele['cdt'][$j]['dev'][$k]['id_login']."<br />\n";
						$chaine_tmp.=$tab_ele['groupes'][$tab_ele['index_grp'][$tab_ele['cdt'][$j]['dev'][$k]['id_groupe']]]['prof_liste']."<br />\n";
						$chaine_tmp.="</td>\n";
						$chaine_tmp.="</tr>\n";

						$chaine_tmp.="<tr style='background-color:$couleur_dev;'>\n";
						$chaine_tmp.="<td colspan='2' style='text-align:left;'>\n";
						//echo "Date ".jour_en_fr(date("D",$tab_ele['cdt'][$j]['dev'][$k]['date_ct']))." ".date("d/m/Y",$tab_ele['cdt'][$j]['dev'][$k]['date_ct'])."<br />\n";
						$chaine_tmp.=nl2br($tab_ele['cdt'][$j]['dev'][$k]['contenu']);

						$adj=affiche_docs_joints($tab_ele['cdt'][$j]['dev'][$k]['id_ct'],"t");
						if($adj!='') {
							$chaine_tmp.="<div style='border: 1px dashed black'>\n";
							$chaine_tmp.=$adj;
							$chaine_tmp.="</div>\n";
						}

						//echo "</div>\n";
						$chaine_tmp.="</td>\n";
						$chaine_tmp.="</tr>\n";
						$chaine_tmp.="</table>\n";
					}
				}
				$chaine_tmp.="</td>\n";

				//echo "<td valign='top' style='padding:3px;'>\n";
				$chaine_tmp.="<td valign='top'>\n";
				if(isset($tab_ele['cdt'][$j]['entry'])) {
					for($k=0;$k<count($tab_ele['cdt'][$j]['entry']);$k++) {
						//echo "<div style='border:1px solid black; background-color: lightgreen; margin:1px; display:block; width:40%;'>\n";
						//echo "Groupe ".$tab_ele['cdt'][$j]['entry'][$k]['id_groupe']."<br />\n";
						//echo "Prof ".$tab_ele['cdt'][$j]['entry'][$k]['id_login']."<br />\n";
						//echo "Date ".jour_en_fr(date("D",$tab_ele['cdt'][$j]['dev'][$k]['date_ct']))." ".date("d/m/Y",$tab_ele['cdt'][$j]['dev'][$k]['date_ct'])."<br />\n";
						//echo $tab_ele['cdt'][$j]['entry'][$k]['contenu'];
						$chaine_tmp.="<table class='boireaus' border='1' style='margin:3px; width:100%;' summary='CDT'>\n";
						$chaine_tmp.="<tr style='background-color:$couleur_entry;'>\n";
						$chaine_tmp.="<td>\n";
						$chaine_tmp.=$tab_ele['groupes'][$tab_ele['index_grp'][$tab_ele['cdt'][$j]['entry'][$k]['id_groupe']]]['matiere_nom_complet']." <span style='font-size:x-small;'>(".$tab_ele['groupes'][$tab_ele['index_grp'][$tab_ele['cdt'][$j]['entry'][$k]['id_groupe']]]['name'].")</span>";
						$chaine_tmp.="</td>\n";

						$chaine_tmp.="<td>\n";
						$chaine_tmp.=$tab_ele['groupes'][$tab_ele['index_grp'][$tab_ele['cdt'][$j]['entry'][$k]['id_groupe']]]['prof_liste']."<br />\n";
						$chaine_tmp.="</td>\n";
						$chaine_tmp.="</tr>\n";

						$chaine_tmp.="<tr style='background-color:$couleur_entry;'>\n";
						$chaine_tmp.="<td colspan='2' style='text-align:left;'>\n";
						$chaine_tmp.=nl2br($tab_ele['cdt'][$j]['entry'][$k]['contenu']);

						$adj=affiche_docs_joints($tab_ele['cdt'][$j]['entry'][$k]['id_ct'],"c");
						if($adj!='') {
							$chaine_tmp.="<div style='border: 1px dashed black'>\n";
							$chaine_tmp.=$adj;
							$chaine_tmp.="</div>\n";
						}
						$chaine_tmp.="</td>\n";
						$chaine_tmp.="</tr>\n";
						$chaine_tmp.="</table>\n";
					}
				}

				//echo "</div>\n";
				$chaine_tmp.="</tr>\n";
			}

			//echo "</div>\n";
			$chaine_tmp.="</table>\n";
			$chaine_tmp.="</div>\n";
			$lignes_cdt_mail.=$chaine_tmp;
			echo $chaine_tmp;

			if($envoi_mail=="y") {
				if(!check_mail($_POST['mail_dest'])) {
					$message="L'adresse mail choisie '".$_POST['mail_dest']."' est invalide.";
					echo "<p style='color:red; text-align:center;'>$message</p>
					<script type='text/javascript'>
						document.getElementById('div_compte_rendu_envoi_mail').innerHTML=\"<span style='color:red'>$message</span>\";
					</script>\n";
				}
				else {
					$sujet="Cahier de textes";
					$message="Bonjour(soir),\nVoici le contenu du cahier de textes pour pour la semaine choisie :\n";
					if(getSettingValue('url_racine_gepi')!="") {
						$message.=preg_replace("#../documents/#", getSettingValue('url_racine_gepi')."/documents/", $lignes_cdt_mail);
					}
					else {
						$message.=$lignes_cdt_mail;
					}
					$destinataire=$_POST['mail_dest'];
					$tab_param_mail['destinataire']=$destinataire;
					$header_suppl="";
					if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
						$header_suppl.="Bcc:".$_SESSION['email']."\r\n";
						$tab_param_mail['bcc']=$_SESSION['email'];
					}
					$envoi=envoi_mail($sujet, $message, $destinataire, $header_suppl, "html", $tab_param_mail);
					if($envoi) {
						$message="Le cahier de textes de la semaine choisie a été expédié à l'adresse mail choisie '".$_POST['mail_dest']."'.";
						echo "<p style='color:green; text-align:center;'>$message</p>
						<script type='text/javascript'>
							document.getElementById('div_compte_rendu_envoi_mail').innerHTML=\"<span style='color:green'>$message</span>\";
						</script>\n";
					}
					else {
						$message="Echec de l'envoi du cahier de textes à l'adresse mail choisie '".$_POST['mail_dest']."'.";
						echo "<p style='color:red; text-align:center;'>$message</p>
						<script type='text/javascript'>
							document.getElementById('div_compte_rendu_envoi_mail').innerHTML=\"<span style='color:red'>$message</span>\";
						</script>\n";
					}
				}

				// DEBUG:
				//echo "<div style='border: 1px solid red; text-align:center;'>$lignes_cdt_mail</div>";
			}

			//++++++++++++++++++++++++++++++
			echo "<div id='lien_mail' style='text-align:right; display:none'><a href=\"javascript:afficher_div('div_envoi_cdt_par_mail','y',10,10)\" title=\"Envoyer par mail *la semaine affichée* du cahier de textes
(par exemple pour envoyer à un parent d'élève qui a oublié ses compte et mot de passe).
Pour envoyer plus d'une semaine par mail, vous pouvez utiliser la page de consultation des cahiers de textes.\"><img src='../images/icons/courrier_envoi.png' class='icone16' alt='Mail' /></a></div>
			<script type='text/javascript'>document.getElementById('lien_mail').style.display=''</script>\n";
			//echo "</div>\n";

			$titre_infobulle="Envoi du CDT par mail";
			$texte_infobulle="<form action='".$_SERVER['PHP_SELF']."' name='form_envoi_cdt_mail' method='post'>
		<input type='hidden' name='envoi_mail' value='y' />
		<input type='hidden' name='ele_login' value='$ele_login' />
		<input type='hidden' name='onglet' value='cdt' />";

			//https://127.0.0.1/steph/gepi_git_trunk/eleves/visu_eleve.php?ele_login=allaixe&onglet=cdt&day=25&month=10&year=2013
			if((isset($day))&&(isset($month))&&(isset($year))) {
				$texte_infobulle.="
		<input type='hidden' name='day' value='$day' />
		<input type='hidden' name='month' value='$month' />
		<input type='hidden' name='year' value='$year' />";
			}

			$texte_infobulle.="
		<p>Précisez à quelle adresse vous souhaitez envoyer le contenu du cahier de textes&nbsp;:<br />
		Mail&nbsp;:&nbsp;<input type='text' name='mail_dest' value='' />
		<input type='submit' value='Envoyer' />
</form>";
			$tabdiv_infobulle[]=creer_div_infobulle('div_envoi_cdt_par_mail',$titre_infobulle,"",$texte_infobulle,"",30,0,'y','y','n','n');
			//++++++++++++++++++++++++++++++

			/*
			if((getSettingAOui('rss_cdt_eleve'))||(getSettingAOui('rss_cdt_responsable'))) {
				if($_SESSION['statut']=='administrateur') {
					$test_https = 'y';
					if (!isset($_SERVER['HTTPS'])
						OR (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != "on")
						OR (isset($_SERVER['X-Forwaded-Proto']) AND $_SERVER['X-Forwaded-Proto'] != "https"))
					{
						$test_https = 'n';
					}

					echo "<div style='text-align:right;'>\n";
					$uri_el = retourneUri($ele_login, $test_https, 'cdt');
					if($uri_el['uri']!="#") {
						echo "<a href='".$uri_el['uri']."' title='Flux RSS du cahier de textes de cet élève' target='_blank'><img src='../images/icons/rss.png' width='16' height='16' /></a>";
					}
					else {
						echo "<a href='../cahier_texte_admin/rss_cdt_admin.php#rss_initialisation_cas_par_cas' target='_blank' title=\"Le flux RSS du cahier de textes de cet élève n'est pas initialisé. Cliquez pour accéder au paramétrage du module RSS et créer le flux de cet élève\"><img src='../images/icons/rss_non_initialise.png' width='16' height='16' /></a>";
					}
					echo "</div>\n";
				}
				elseif((($_SESSION['statut']=='scolarite')&&(getSettingAOui('rss_cdt_scol')))||
				(($_SESSION['statut']=='cpe')&&(getSettingAOui('rss_cdt_cpe')))||
				(($_SESSION['statut']=='professeur')&&(getSettingAOui('rss_cdt_pp'))&&(is_pp($_SESSION['login'], "", $ele_login)))) {
					$test_https = 'y';
					if (!isset($_SERVER['HTTPS'])
						OR (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != "on")
						OR (isset($_SERVER['X-Forwaded-Proto']) AND $_SERVER['X-Forwaded-Proto'] != "https"))
					{
						$test_https = 'n';
					}

					echo "<div style='text-align:right;'>\n";
					$uri_el = retourneUri($ele_login, $test_https, 'cdt');
					if($uri_el['uri']!="#") {
						echo "<a href='".$uri_el['uri']."' title='Flux RSS du cahier de textes de cet élève' target='_blank'><img src='../images/icons/rss.png' width='16' height='16' /></a>";
					}
					else {
						echo "<img src='../images/icons/rss_non_initialise.png' width='16' height='16' title=\"Le flux RSS du cahier de textes de cet élève n'est pas initialisé. Contactez l'administrateur\" />";
					}
					echo "</div>\n";
				}
			}
			*/
			$temoin_rss_ele=retourne_temoin_ou_lien_rss($ele_login);
			if($temoin_rss_ele!="") {
				echo "<div style='text-align:right;'>".$temoin_rss_ele."</div>\n";
			}

			echo "</div>\n";
		}


		//=============================================

		//========================
		// Onglet FICHES PROJET
		//========================

		if(($acces_fp=="y")&&($acces_global_fp=="y")) {
			echo "<div id='fp' class='onglet' style='";
			if($onglet!="fp") {echo " display:none;";}
			echo "background-color: ".$tab_couleur['fp']."; ";
			echo "'>";
      $call_data = mysqli_query($GLOBALS["mysqli"], "SELECT j.indice_aid, j.id_aid FROM  j_aid_eleves j, aid_config a where
       j.login='$ele_login' and a.indice_aid=j.indice_aid and a.outils_complementaires='y' order by j.indice_aid");

      $nb_aid = mysqli_num_rows($call_data);
      if ($nb_aid>0) {
  			echo "<h2>Tous les projets de l'".$gepiSettings['denomination_eleve']." ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";
        echo "<table width=\"80%\" border=\"1\" cellspacing=\"1\" cellpadding=\"3\">";
        $z=0;
        while ($z < $nb_aid) {
          $indice_aid = @old_mysql_result($call_data, $z, "indice_aid");
          $aid_id =  @old_mysql_result($call_data, $z, "id_aid");
          $nom_type_aid =  sql_query1("SELECT nom FROM aid_config  WHERE (indice_aid='$indice_aid')");
          $nom_aid = sql_query1("SELECT nom FROM aid WHERE (id='$aid_id' and indice_aid='$indice_aid')");
          $aid_prof_resp_query = mysqli_query($GLOBALS["mysqli"], "SELECT id_utilisateur FROM j_aid_utilisateurs WHERE (id_aid='$aid_id' and indice_aid='$indice_aid')");
          $nb_lig = mysqli_num_rows($aid_prof_resp_query);
          $n = '0';
          while ($n < $nb_lig) {
            $aid_prof_resp_login = @old_mysql_result($aid_prof_resp_query, $n, "id_utilisateur");
            $aid_prof_query = @mysqli_query($GLOBALS["mysqli"], "SELECT nom,prenom FROM utilisateurs WHERE login='$aid_prof_resp_login'");
            $aid_prof_resp_nom[$n] = @old_mysql_result($aid_prof_query, 0, "nom");
            $aid_prof_resp_prenom[$n] = @old_mysql_result($aid_prof_query, 0, "prenom");
            $n++;
          }
          echo "<tr><td><span class='small'><strong>$nom_type_aid</strong>";
          $n = '0';
          while ($n < $nb_lig) {
            echo "<br /><em>$aid_prof_resp_nom[$n] $aid_prof_resp_prenom[$n]</em>";
            $n++;
          }
          echo "</span></td>";
          echo "<td><span class='small'><a href='../aid/modif_fiches.php?aid_id=$aid_id&amp;indice_aid=$indice_aid&amp;annee=&amp;action=visu&amp;retour=' target='_blank'>$nom_aid</a></span></td></tr>";
          $z++;
        }
        echo "</table>";
      } else {
  			echo "<h2>L'".$gepiSettings['denomination_eleve']." ".$tab_ele['nom']." ".$tab_ele['prenom']." n'est actuellement inscrit dans aucun projet.</h2>\n";
      }

      // Affichage des projets des années antérieures
      $id_nat = sql_query1("select no_gep from eleves where login='".$ele_login."'");
      $sql_projet_aa="select ta.annee, 
                             ta.id AS ta_id, 
                             a.id AS a_id, 
                             ta.nom AS ta_nom, 
                             a.nom AS a_nom, 
                             a.responsables
      from archivage_aids a, archivage_types_aid ta, archivage_aid_eleve ae
      where ta.outils_complementaires='y' and
      a.id=ae.id_aid and
      ae.id_eleve='".$id_nat."' and
      a.id_type_aid = ta.id
      order by ta.annee";
      $call_data = mysqli_query($GLOBALS["mysqli"], $sql_projet_aa);

      $nb_aid = mysqli_num_rows($call_data);
      if ($nb_aid>0) {
        echo "<h2>Les projets des années antérieures</h2>";
        echo "<table width=\"$larg_tab\" border=\"1\" cellspacing=\"1\" cellpadding=\"3\">";
        $z=0;
        //while ($z < $nb_aid) {
        while($obj_projet_aa=$call_data->fetch_object()) {
          $annee = $obj_projet_aa->annee;
          $indice_aid = $obj_projet_aa->ta_id;
          $aid_id =  $obj_projet_aa->a_id;
          $nom_type_aid = $obj_projet_aa->ta_nom;
          $nom_aid =  $obj_projet_aa->a_nom;
          $aid_prof_resp =  $obj_projet_aa->responsables;
          echo "<tr>\n";
          echo "<td><span class='small'>".$annee."</td>\n";
          echo "<td><span class='small'><strong>$nom_type_aid</strong>";
          echo "<br /><em>$aid_prof_resp</em>";
          echo "</span></td>\n";
          echo "<td><span class='small'><a href='../aid/modif_fiches.php?aid_id=$aid_id&amp;indice_aid=$indice_aid&amp;annee=$annee&amp;action=visu&amp;retour=' target='_blank'>$nom_aid</a></span></td>\n</tr>\n";
          $z++;
        }
        echo "</table>\n";
      }

      /**************************************************************************
      * Cas ou le plugin "gestion_autorisations_publications" existe et est activé
      ****************************************************************************/
      //On vérifie si le module est activé
      $test_plugin = sql_query1("select ouvert from plugins where nom='gestion_autorisations_publications'");
      if ($test_plugin=='y') {
        include_once("../mod_plugins/gestion_autorisations_publications/functions_gestion_autorisations_publications.php");
        echo verifie_autorisation_publication($ele_login,"professeur");
      }

			echo "</div>\n";
		}


		//===================================================

		//===================================================

		//========================
		// Onglet ABSENCES
		//========================


		if($acces_absences=="y") {
			echo "<div id='absences' class='onglet' style='";
			if($onglet!="absences") {echo " display:none;";}
			echo "background-color: ".$tab_couleur['absences']."; ";
			echo "'>";

			if((acces('/mod_abs2/saisir_eleve.php', $_SESSION['statut']))&&
			(in_array($_SESSION['statut'], array('cpe', 'scolarite', 'autre')))) {
				$id_eleve=get_valeur_champ("eleves", "login='$ele_login'", "id_eleve");
				if(preg_match("/^[0-9]{1,}$/", $id_eleve)) {
					echo "<div style='float:right; width:16px; margin:0.2em;'><a href='../mod_abs2/saisir_eleve.php?type_selection=id_eleve&id_eleve=$id_eleve' title=\"Saisir une absence de l'élève\" target='_blank'><img src='../images/icons/absences_edit.png' class='icone16' alt='Saisir' /></a></div>";
				}
			}

			// 20160503
			if(acces('/mod_abs2/bilan_individuel.php', $_SESSION['statut'])) {
				$date_absence_eleve_debut=isset($_POST['date_absence_eleve_debut']) ? $_POST['date_absence_eleve_debut'] : (isset($_GET['date_absence_eleve_debut']) ? $_GET['date_absence_eleve_debut'] : (isset($_SESSION['date_absence_eleve_debut']) ? $_SESSION['date_absence_eleve_debut'] : strftime("%Y-%m-%d")));
				$date_absence_eleve_fin=isset($_POST['date_absence_eleve_fin']) ? $_POST['date_absence_eleve_fin'] : (isset($_GET['date_absence_eleve_fin']) ? $_GET['date_absence_eleve_fin'] : (isset($_SESSION['date_absence_eleve_fin']) ? $_SESSION['date_absence_eleve_fin'] : strftime("%Y-%m-%d")));

				echo "<div style='float:right; width:16px; margin:0.2em;'><a href='../mod_abs2/bilan_individuel.php?id_eleve=".$tab_ele['id_eleve']."&date_absence_eleve_debut=".$date_absence_eleve_debut."&date_absence_eleve_fin=".$date_absence_eleve_fin."&affichage=html' title=\"Voir le bilan individuel de l'élève\" target='_blank'><img src='../images/icons/absences.png' class='icone16' alt='Bilan individuel' /></a></div>";
			}

			if(acces('/mod_abs2/visu_eleve_calendrier.php', $_SESSION['statut'])) {
				echo "<div style='float:right; width:16px; margin:0.2em;'><a href='../mod_abs2/visu_eleve_calendrier.php?login_ele=$ele_login' title=\"Voir les absences de l'élève par mois sur un calendrier\" target='_blank'><img src='../images/icons/absences_calendrier.png' class='icone16' alt='Absences sur calendrier' /></a></div>";
			}

			if((acces('/edt/index2.php', $_SESSION['statut']))&&(getSettingValue('active_module_absence')=='2')) {
				echo "<div style='float:right; width:24px; margin:5px;' title=\"Affichage des absences sur un EDT version 2\"><a href='$gepiPath/edt/index2.php?affichage=semaine&type_affichage=eleve&login_eleve=".$ele_login."&affichage_complementaire_sur_edt=absences2' target='_blank'><img src='$gepiPath/images/icons/edt2_abs2.png' width='24' height='24' alt='EDT2' /></a></div>";
			}

			if(getSettingValue("active_module_absence")=='y' || getSettingValue("abs2_import_manuel_bulletin")=='y') {
				echo "<h2>Absences et retards de l'".$gepiSettings['denomination_eleve']." ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";
				if(count($tab_ele['absences'])==0) {
					echo "<p>Aucun bilan d'absences n'est enregistré.</p>\n";
				}
				else {
					echo "<table class='boireaus'>\n";
					echo "<caption>
						<strong>Etat périodique de l'absentéisme porté sur le bulletin</strong>
						</br>
						<em>Ce bilan est figé le jour de la bascule de l'état sur le bulletin";
						if (getSettingValue("active_module_absence")=='2') {
							echo "</br>
							Il peut différer du bilan des saisies ci-dessous (justification tardive, ...)";
						}
						echo "</em>
						</caption>\n";
				    echo "<tr>\n";
				    echo "<th>Période</th>\n";
				    echo "<th>Nombre d'absences</th>\n";
				    echo "<th>Absences non justifiées</th>\n";
				    echo "<th>Nombre de retards</th>\n";
				    echo "<th>Appréciation</th>\n";
				    echo "</tr>\n";
				    $alt=1;
				    for($loop=0;$loop<count($tab_ele['absences']);$loop++) {
					    $alt=$alt*(-1);
					    echo "<tr class='lig$alt'>\n";
					    echo "<td>".$tab_ele['absences'][$loop]['periode']."</td>\n";
					    echo "<td>".$tab_ele['absences'][$loop]['nb_absences']."</td>\n";
					    echo "<td>".$tab_ele['absences'][$loop]['non_justifie']."</td>\n";
					    echo "<td>".$tab_ele['absences'][$loop]['nb_retards']."</td>\n";
					    echo "<td>".$tab_ele['absences'][$loop]['appreciation']."</td>\n";
					    echo "</tr>\n";
				    }
				    echo "</table>\n";
			    }
						    // On ajoute le suivi par créneaux si il y en a
			    if ($tab_ele['abs_quotidien']['autorisation'] == 'oui') {
				    // On affiche
				    echo '<br /><p class="bold">Le détail des absences enregistrées : </p>';

				    echo '
				    <table class="boireaus" style="margin-left: 4em;" summary="Détail des absences">
					    <tr>
						    <th>R/A</th>
						    <th>Jour</th>
						    <th>Heure</th>
						    <th>Créneau</th>
					    </tr>';
				    foreach($tab_ele["abs_quotidien"] as $abs){
					    if (isset($abs["retard_absence"]) AND ($abs["retard_absence"] == 'A' OR $abs["retard_absence"] == 'R')) {
						    $aff_couleur = ' style="background-color: green;"';
						    $aff_abs_lettre = 'R';
						    if ($abs["retard_absence"] == 'A') {
							    $aff_couleur = ' style="background-color: red;"';
							    $aff_abs_lettre = 'A';
						    }
						    echo '
					    <tr>
						    <td' . $aff_couleur . '>' . $aff_abs_lettre . '</td>
						    <td>' . $abs["jour_semaine"] . '</td>
						    <td>' . $abs["debut_heure"] . '</td>
						    <td>' . $abs["creneau"] . '</td>
					    </tr>';
					    }
				    }
				    echo '</table>'."\n";
			    }
				
				if (getSettingValue("active_module_absence")=='2') {
				  require_once("../lib/initialisationsPropel.inc.php");
				  $eleve = EleveQuery::create()->findOneByLogin($ele_login);
				  include 'visu_eleve_abs2.inc.php';
			    }
				
			} elseif (getSettingValue("active_module_absence")=='2') {
			    echo "<h2>Absences et retards de l'".$gepiSettings['denomination_eleve']." ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";

				//affichage de la date de sortie de l'élève de l'établissement
				if ($tab_ele['date_sortie']!=0) {
					echo "<p style=\"color:red\">Date de sortie de l'établissement : le ".affiche_date_sortie($tab_ele['date_sortie'])."</p>";;
				}
				
				// Initialisations files
			    require_once("../lib/initialisationsPropel.inc.php");
			    $eleve = EleveQuery::create()->findOneByLogin($ele_login);

				// 20170621
				if(isset($id_derniere_classe)) {
					$maxper=get_max_per($id_derniere_classe);
					$tab_date_dernier_conseil_classe=get_date_conseil_classe($id_derniere_classe, $maxper);
				}

			    echo "<table class='boireaus boireaus_alt'>\n";
				echo "<caption>Bilan des absences</caption>\n";
			    echo "<tr>\n";
			    echo "<th title=\"Les dates de fin de période correspondent à ce qui est paramétré en colonne 'Date de fin' de la page de Verrouillage des périodes de notes (page accessible en compte scolarité).\">Période</th>\n";
			    echo "<th>Nombre d'absences<br/>(1/2 journées)</th>\n";
			    echo "<th>Absences non justifiées</th>\n";
			    echo "<th>Nombre de retards</th>\n";
			    echo "<th>Appréciation</th>\n";
			    echo "</tr>\n";
			    foreach($eleve->getPeriodeNotes() as $periode_note) {
				    if ($periode_note->getDateDebut() == null) {
					//periode non commencee
					continue;
				    }

					// 20170621
					$info_complementaire_abs="";
					$info_complementaire_nj="";
					$info_complementaire_retards="";
					if(isset($id_derniere_classe)) {
						if(($periode_note->getNumPeriode()==$maxper)&&
							($tab_date_dernier_conseil_classe['date_conseil_classe_valide'])&&
							(getSettingAOui("abs2_limiter_abs_date_conseil_fin_annee"))) {

							$current_periode_note=$eleve->getPeriodeNote($periode_note->getNumPeriode());
							// A déclarer hors de la boucle eleves
							//$date_fin_abs=new DateTime(str_replace("/", ".", formate_date($tab_date_dernier_conseil_classe['date_conseil_classe'])));
							$date_fin_abs=$tab_date_dernier_conseil_classe['date_conseil_classe_DateTime'];

							$date_conseil_classe=formate_date($tab_date_dernier_conseil_classe['date_conseil_classe']);

							$current_eleve_absences = strval($eleve->getDemiJourneesAbsence($current_periode_note->getDateDebut(null), $date_fin_abs)->count());
							$info_complementaire_abs=" <span title=\"".$current_eleve_absences." comptées jusqu'à la date du conseil de classe (".$date_conseil_classe.").\">(".$current_eleve_absences.")</span>";
							//echo "\$current_eleve_absences = strval(\$eleve->getDemiJourneesAbsence(".$current_periode_note->getDateDebut(null)->format("d/m/Y").", ".$date_fin_abs->format("d/m/Y").")->count())=".$current_eleve_absences."<br />\n";
					
							$current_eleve_nj = strval($eleve->getDemiJourneesNonJustifieesAbsence($current_periode_note->getDateDebut(null), $date_fin_abs)->count());
							$info_complementaire_nj=" <span title=\"".$current_eleve_nj." comptées jusqu'à la date du conseil de classe (".$date_conseil_classe.").\">(".$current_eleve_nj.")</span>";
							$current_eleve_retards = strval($eleve->getRetards($current_periode_note->getDateDebut(null), $date_fin_abs)->count());
							$info_complementaire_retards=" <span title=\"".$current_eleve_retards." comptés jusqu'à la date du conseil de classe (".$date_conseil_classe.").\">(".$current_eleve_retards.")</span>";
						}
					}

				    echo "<tr>\n";
				    echo "<td>".$periode_note->getNomPeriode();
				    echo " du ".$periode_note->getDateDebut('d/m/Y');
				    echo " au ";
				    if ($periode_note->getDateFin() == null) {
					echo '(non précisé)';
				    } else {
					echo $periode_note->getDateFin('d/m/Y');
				    }
				    echo "</td>\n";
				    echo "<td>";
				    echo $eleve->getDemiJourneesAbsenceParPeriode($periode_note)->count().$info_complementaire_abs;
				    echo "</td>\n";
				    echo "<td>";
				    echo $eleve->getDemiJourneesNonJustifieesAbsenceParPeriode($periode_note)->count().$info_complementaire_nj;
				    echo "</td>\n";
				    echo "<td>";
				    echo $eleve->getRetardsParPeriode($periode_note)->count().$info_complementaire_retards;
				    echo "</td>\n";
				    echo "<td>";
				    // PROBLEME: On n'a plus accès à cette table si on ne remplit pas la table absences.
				    //           Revoir la façon dont on remplit l'appréciation, peut-être donner l'accès à la page absences/saisie_absences.php
				    //           sans permettre la modif des retards/abs/nj)
					$sql="SELECT * FROM absences WHERE (login='".$ele_login."' AND periode='".$periode_note->getNumPeriode()."');";
					$current_eleve_absences_query = mysqli_query($GLOBALS["mysqli"], $sql);
					$current_eleve_appreciation_absences = @old_mysql_result($current_eleve_absences_query, 0, "appreciation");
					echo $current_eleve_appreciation_absences;
				    echo "</td>\n";
				    echo "</tr>\n";
			    }
			    echo "</table>\n";
				
				include 'visu_eleve_abs2.inc.php';
			}
			echo "</div>\n";
		}


		//===================================================

		//========================
		// Onglet DISCIPLINE
		//========================
		if($acces_discipline=="y") {
			echo "<div id='discipline' class='onglet' style='";
			if($onglet!="discipline") {echo " display:none;";}
			echo "background-color: ".$tab_couleur['discipline']."; ";
			echo "'>";

			$div_en_haut_a_droite="";
			if(acces('/mod_discipline/saisie_incident.php', $_SESSION['statut'])) {
				//echo "<div style='float:right; width:4em;'>\n";
				//echo "<a href='../mod_discipline/saisie_incident.php?ele_login[0]=".$ele_login."&amp;Ajouter=Ajouter".add_token_in_url()."' title='Saisir un incident'><img src='../images/icons/saisie.png' width='16' height='16' /></a>";
				$div_en_haut_a_droite.="<form action='../mod_discipline/saisie_incident.php' name='form_saisie_disc' method='post' />\n";
				$div_en_haut_a_droite.=add_token_field();
				$div_en_haut_a_droite.="<input type='hidden' name='ele_login[0]' value=\"$ele_login\" />\n";
				$div_en_haut_a_droite.="<input type='hidden' name='is_posted' value=\"y\" />\n";
				$div_en_haut_a_droite.="<input type='hidden' name='Ajouter' value=\"Ajouter\" />\n";
				$div_en_haut_a_droite.="<input type='submit' name='Saisir' value=\"Saisir\" title=\"Saisir un nouvel $mod_disc_terme_incident dans le module Discipline\" />\n";
				$div_en_haut_a_droite.="</form>\n";
				//$div_en_haut_a_droite.="</div>\n";
			}

			// A FAIRE: 20140418
			$div_en_haut_a_droite.="<div style='float:right; width:4em; color:red; text-align:center;'>\n";
			//if (($_SESSION['statut']=='administrateur') || ($_SESSION['statut']=='scolarite') || ($_SESSION['statut']=='cpe')){
			if(acces_extract_disc("", $ele_login)) {
				$div_en_haut_a_droite.="<a href='../mod_discipline/mod_discipline_extraction_ooo.php?protagoniste_incident=$ele_login' title=\"Exporter les ".$mod_disc_terme_incident."s au format ODT.\" target='_blank'>ODT</a><br />";
			}
	
			$div_en_haut_a_droite.="<a href='../mod_discipline/afficher_incidents_eleve.php?login_ele=$ele_login' title=\"Afficher cette page avec/sans les informations concernant les autres protagonistes des ".$mod_disc_terme_incident."s.
Vous pourrez choisir d'afficher ou non les informations concernant les éventuels autres protagonistes.\">HTML</a><br />
</div>\n";

			if((acces('/mod_discipline/saisie_avertissement_fin_periode.php', $_SESSION['statut']))&&(acces_saisie_avertissement_fin_periode($ele_login))) {
				$div_en_haut_a_droite.="<div style='float:right; width:4em; text-align:center;'><a href='../mod_discipline/saisie_avertissement_fin_periode.php?login_ele=$ele_login' title=\"Saisir un ".getSettingValue('mod_disc_terme_avertissement_fin_periode')."\">Saisie AVT</a></div>\n";
			}

			if($div_en_haut_a_droite!="") {
				echo "<div style='float:right; width:4em;'>$div_en_haut_a_droite</div>";
			}

			//if(acces('/mod_discipline/imprimer_bilan_periode.php', $_SESSION['statut'])) {
				$tableau_des_avertissements_de_fin_de_periode_eleve_de_cet_eleve=tableau_des_avertissements_de_fin_de_periode_eleve($ele_login);
				if($tableau_des_avertissements_de_fin_de_periode_eleve_de_cet_eleve!='') {
					echo "<div style='float:right; width:25em; margin-bottom:0.5em; margin-left:0.5em;'>".$tableau_des_avertissements_de_fin_de_periode_eleve_de_cet_eleve."</div>\n";
				}
			//}

			echo "<h2>".ucfirst($mod_disc_terme_incident)."s \"concernant\" l'".$gepiSettings['denomination_eleve']." ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";

			if(getSettingAOui('active_mod_disc_pointage')) {
				if(getSettingAOui('disc_pointage_aff_totaux_visu_ele')) {
					echo retourne_tab_html_pointages_disc($ele_login);
				}
			}

			//=======================
			//Configuration du calendrier
			/*
			include("../lib/calendrier/calendrier.class.php");
			$cal1 = new Calendrier("form_date_disc", "date_debut_disc");
			$cal2 = new Calendrier("form_date_disc", "date_fin_disc");
			*/
			//=======================

			echo "<form action='".$_SERVER['PHP_SELF']."' name='form_date_disc' method='post' />\n";
			echo $champ_quitter_page_ou_non;
			echo "<p>Extraire les incidents entre le ";
			//echo "<input type='text' name='date_debut_disc' value='' />\n";
			echo "<input type='text' name = 'date_debut_disc' id= 'date_debut_disc' size='10' value = \"".$date_debut_disc."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
			//echo "<a href=\"#\" onClick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
			echo img_calendrier_js("date_debut_disc", "img_bouton_date_debut_disc");
			echo "et le ";
			//echo "<input type='text' name='date_fin_disc' value='' />\n";
			echo "<input type='text' name = 'date_fin_disc' id= 'date_fin_disc' size='10' value = \"".$date_fin_disc."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
			//echo "<a href=\"#\" onClick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
			echo img_calendrier_js("date_fin_disc", "img_bouton_date_fin_disc");

			echo "<input type='submit' name='restreindre_intervalle_dates' value='Valider' />\n";

			echo "<input type='hidden' name='onglet' value='discipline' />\n";
			echo "<input type='hidden' name='ele_login' value=\"$ele_login\" />\n";
			//echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";

			echo "</p>\n";
			echo "</form>\n";

			if(isset($tab_ele['tab_mod_discipline'])) {
				echo $tab_ele['tab_mod_discipline'];
			}

			echo "</div>\n";
		}
		//===================================================


		//===================================================

		//========================
		// Onglet ANNEES ANTERIEURES
		//========================

		if($acces_anna=="y") {
			echo "<div id='anna' class='onglet' style='";
			if($onglet!="anna") {echo " display:none;";}
			echo "background-color: ".$tab_couleur['anna']."; ";
			echo "'>";

			if((!isset($id_classe))&&(isset($tab_ele['classe'][0]['id_classe']))) {
				$id_classe=$tab_ele['classe'][0]['id_classe'];
			}
			if(isset($id_classe)) {
				$lien_annees_anterieures=" <a href='../mod_annees_anterieures/consultation_annee_anterieure.php?id_classe=".$id_classe."&logineleve=".$ele_login."' target='_blank' title=\"Voir dans un nouvel onglet les données d'années antérieures\"'><img src='../images/icons/chercher.png' class='icone16' alt='Voir' /></a>";
			}
			else {
				$lien_annees_anterieures='';
			}

			echo "<h2>Données d'années antérieures de l'".$gepiSettings['denomination_eleve']." ".$tab_ele['nom']." ".$tab_ele['prenom'].$lien_annees_anterieures."</h2>\n";

			require("../mod_annees_anterieures/fonctions_annees_anterieures.inc.php");

			//echo $_SERVER['HTTP_USER_AGENT']."<br />\n";
			if(preg_match("/gecko/i",$_SERVER['HTTP_USER_AGENT'])){
				//echo "gecko=true<br />";
				$gecko=true;
			}
			else{
				//echo "gecko=false<br />";
				$gecko=false;
			}



			echo "<p>Liste des années scolaires et périodes pour lesquelles des données ont été conservées pour cet ".$gepiSettings['denomination_eleve']." :</p>\n";

			// Récupérer les années-scolaires et périodes pour lesquelles on trouve l'INE dans archivage_disciplines
			//$sql="SELECT DISTINCT annee,num_periode,nom_periode FROM archivage_disciplines WHERE ine='$ine' ORDER BY annee DESC, num_periode ASC";
			//$sql="SELECT DISTINCT annee FROM archivage_disciplines WHERE ine='$ine' ORDER BY annee DESC;";
			$sql="SELECT DISTINCT annee FROM archivage_disciplines WHERE ine='".$tab_ele['no_gep']."' AND ine!='' ORDER BY annee ASC;";
			$res_ant=mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($res_ant)==0){
				echo "<p>Aucun résultat antérieur n'a été conservé pour cet ".$gepiSettings['denomination_eleve'].".</p>\n";
			}
			else{

				unset($tab_annees);

				$nb_annees=mysqli_num_rows($res_ant);

				//echo "<p>Bulletins simplifiés:</p>\n";
				//echo "<table border='0'>\n";
				echo "<table class='boireaus' summary='Bulletins'>\n";
				$alt=1;
				echo "<tr class='lig$alt'>\n";
				echo "<th rowspan='".$nb_annees."' valign='top'>Bulletins simplifiés:</th>";
				$cpt=0;
				while($lig_ant=mysqli_fetch_object($res_ant)){

					$tab_annees[]=$lig_ant->annee;

					if($cpt>0){
						//echo "<tr>\n";
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
					}
					echo "<td style='font-weight:bold;'>$lig_ant->annee : </td>\n";

					$sql="SELECT DISTINCT num_periode,nom_periode FROM archivage_disciplines WHERE ine='".$tab_ele['no_gep']."' AND annee='$lig_ant->annee' ORDER BY num_periode ASC";
					$res_ant2=mysqli_query($GLOBALS["mysqli"], $sql);

					if(mysqli_num_rows($res_ant2)==0){
						echo "<td>Aucun résultat antérieur n'a été conservé pour cet ".$gepiSettings['denomination_eleve'].".</td>\n";
					}
					else{

						if((!isset($id_classe))&&(isset($tab_ele['classe'][0]['id_classe']))) {
							$id_classe=$tab_ele['classe'][0]['id_classe'];
						}

						if(isset($id_classe)) {
							$cpt=0;
							while($lig_ant2=mysqli_fetch_object($res_ant2)){
								//if($cpt>0){echo "<td> - </td>\n";}

								// $id_classe=$tab_ele['periodes'][$index_per]['id_classe']

								echo "<td style='text-align:center;'><a href='../mod_annees_anterieures/popup_annee_anterieure.php?id_classe=".$id_classe."&amp;logineleve=".$ele_login."&amp;annee_scolaire=".$lig_ant->annee."&amp;num_periode=".$lig_ant2->num_periode."&amp;mode=bull_simp' onclick=\"ajax_annee_anterieure_bull_simp('".$ele_login."', ".$id_classe.", '".$lig_ant->annee."', ".$lig_ant2->num_periode.");return false;\" target='_blank'>$lig_ant2->nom_periode</a></td>\n";
								$cpt++;
							}
						}
					}
					echo "</tr>\n";
					$cpt++;
				}
				echo "</table>\n";

				//echo "<p><br /></p>\n";
				echo "<br />\n";

				//echo "<p>Avis des conseils de classes:<br />\n";
				//echo "<table border='0'>\n";
				echo "<table class='boireaus' summary='Avis des conseils'>\n";
				$alt=1;
				echo "<tr class='lig$alt'>\n";
				echo "<th rowspan='".$nb_annees."' valign='top'>Avis des conseils de classes:</th>";
				$cpt=0;
				for($i=0;$i<count($tab_annees);$i++){
					if($cpt>0){
						//echo "<tr>\n";
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
					}
					//echo "<td style='font-weight:bold;'>\n";
					echo "<td>\n";

					echo "Année-scolaire <a href='../mod_annees_anterieures/popup_annee_anterieure.php?logineleve=".$ele_login."&amp;annee_scolaire=".$tab_annees[$i]."&amp;mode=avis_conseil";
					if(isset($id_classe)){echo "&amp;id_classe=$id_classe";}
					echo "' target='_blank'";
					echo " onclick=\"ajax_annee_anterieure_avis('".$ele_login."', '".$tab_annees[$i]."');return false;\"";
					echo ">".$tab_annees[$i]."</a>";
					//echo "<br />\n";

					echo "</td>\n";
					echo "</tr>\n";
					$cpt++;
				}
				echo "</table>\n";
				//echo "</p>\n";

				// 20151018
				echo "<div id='div_mod_annee_anterieure'></div>";
			}

			echo "</div>\n";
		}
		//===================================================

		//=====================================================================================

		//========================
		// Bricolages Javascript
		//========================

		// Liste des onglets de niveau 1
		//$tab_onglets=array('eleve','responsables','enseignements','releves','bulletins','cdt','anna','absences');
		$tab_onglets=array('eleve','responsables','enseignements','releves','bulletins','cdt','fp','anna','absences','discipline');
		$chaine_tab_onglets="tab_onglets=new Array(";
		for($i=0;$i<count($tab_onglets);$i++) {
			if($i>0) {$chaine_tab_onglets.=", ";}
			$chaine_tab_onglets.="'".$tab_onglets[$i]."'";
		}
		$chaine_tab_onglets.=");";

		// Liste des onglets dans l'onglet bulletins
		$chaine_tab_onglets_bull="tab_onglets=new Array(";
		for($i=0;$i<count($tab_onglets_bull);$i++) {
			if($i>0) {$chaine_tab_onglets_bull.=", ";}
			$chaine_tab_onglets_bull.="'".$tab_onglets_bull[$i]."'";
		}
		$chaine_tab_onglets_bull.=");";

		// Liste des onglets dans l'onglet relevés de notes
		$chaine_tab_onglets_rel="tab_onglets=new Array(";
		for($i=0;$i<count($tab_onglets_rel);$i++) {
			if($i>0) {$chaine_tab_onglets_rel.=", ";}
			$chaine_tab_onglets_rel.="'".$tab_onglets_rel[$i]."'";
		}
		$chaine_tab_onglets_rel.=");";


		echo "<script type='text/javascript'>
	function affiche_onglet(id) {
		$chaine_tab_onglets

		for(i=0;i<=tab_onglets.length;i++) {
			if(document.getElementById(tab_onglets[i])) {
				document.getElementById(tab_onglets[i]).style.display='none';
			}
			if(document.getElementById('t_'+tab_onglets[i])) {
				document.getElementById('t_'+tab_onglets[i]).style.borderBottomColor='black';
				document.getElementById('t_'+tab_onglets[i]).style.borderBottomWidth='1px';
			}
		}

		if(document.getElementById(id)) {
			document.getElementById(id).style.display='';
		}
		if(document.getElementById('t_'+id)) {
			document.getElementById('t_'+id).style.borderBottomColor='white';

			document.getElementById('t_'+id).style.borderBottomWidth='0px';
		}

		document.getElementById('onglet_courant').value=id;
	}

	function affiche_onglet_bull(id) {
		$chaine_tab_onglets_bull

		for(i=0;i<tab_onglets.length;i++) {
			if(document.getElementById(tab_onglets[i])) {
				document.getElementById(tab_onglets[i]).style.display='none';
			}
			if(document.getElementById('t_'+tab_onglets[i])) {
				document.getElementById('t_'+tab_onglets[i]).style.borderBottomColor='black';
				document.getElementById('t_'+tab_onglets[i]).style.borderBottomWidth='1px';
				document.getElementById('t_'+tab_onglets[i]).style.fontWeight='';
			}
		}

		if(document.getElementById(id)) {
			document.getElementById(id).style.display='';

			document.getElementById('onglet_bull_courant').value=id;
		}
		if(document.getElementById('t_'+id)) {
			document.getElementById('t_'+id).style.borderBottomColor='white';
			document.getElementById('t_'+id).style.borderBottomWidth='0px';
			document.getElementById('t_'+id).style.fontWeight='bold';
		}
	}

	function affiche_onglet_rel(id) {
		$chaine_tab_onglets_rel

		for(i=0;i<tab_onglets.length;i++) {
			if(document.getElementById(tab_onglets[i])) {
				document.getElementById(tab_onglets[i]).style.display='none';
			}
			if(document.getElementById('t_'+tab_onglets[i])) {
				document.getElementById('t_'+tab_onglets[i]).style.borderBottomColor='black';
				document.getElementById('t_'+tab_onglets[i]).style.borderBottomWidth='1px';
				document.getElementById('t_'+tab_onglets[i]).style.fontWeight='';
			}
		}

		if(document.getElementById(id)) {
			document.getElementById(id).style.display='';

			document.getElementById('onglet_bull_courant').value=id;
		}
		if(document.getElementById('t_'+id)) {
			document.getElementById('t_'+id).style.borderBottomColor='white';
			document.getElementById('t_'+id).style.borderBottomWidth='0px';
			document.getElementById('t_'+id).style.fontWeight='bold';
		}
	}
	
	window.focus();

	if(document.getElementById('spinner_chargement')) {
		setTimeout(\"document.getElementById('spinner_chargement').style.display='none'\", 2000);
	}
</script>\n";

		/*
		echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo "<input type='hidden' name='onglet_courant' id='onglet_courant' value='";
		if(isset($onglet)) {echo $onglet;}
		echo "' />\n";
		echo "</form>\n";
		*/
		echo "<p><br /></p>\n";
	}
}
?>
