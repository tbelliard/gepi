<?php
/*
* $Id$
*
* Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
};


// Ajouter une gestion des droits par la suite
// dans la table MySQL appropriée et décommenter ce passage.
// INSERT INTO droits VALUES ('/visualisation/affiche_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '1');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


//$debug=1;
$debug=0;

function affiche_debug($texte){
	global $debug;
	if($debug==1){
		echo "$texte\n";
	}
}


/*
$datay1 = array();
$datay2 = array();
$etiquette = array();
$graph_title = "";
$v_legend1 = "";
$v_legend2 = "";
*/

// Initialisations sans lesquelles EasyPHP râle:
$seriemin="";
$seriemax="";
$seriemoy="";
$graph_title="Graphe";
$compteur=0;
//$mgen[1]="Non_calculee";
//$mgen[2]="Non_calculee";
$mgen[1]="";
$mgen[2]="";

//$periode=1;
//$temoin_imageps="";



if(!isset($msg)){
	$msg="";
}

// On permet au compte scolarité d'enregistrer les paramètres d'affichage du graphe
if($_SESSION['statut']=='scolarite'){
/*
affiche_photo
largeur_imposee_photo
affiche_mgen
affiche_minmax
affiche_moy_annuelle
largeur_graphe
hauteur_graphe
taille_police
epaisseur_traits
temoin_image_escalier
tronquer_nom_court
*/

	if(isset($_POST['save_params'])){
		if($_POST['save_params']=="y"){

			function save_params_graphe($nom,$valeur){
				global $msg;
				if(!saveSetting("$nom", $valeur)){
					$msg.="Erreur lors de l'enregistrement du paramètre $nom<br />";
				}
			}

			//$erreur_save_params="";
			if(isset($_POST['affiche_photo'])){save_params_graphe('graphe_affiche_photo',$_POST['affiche_photo']);}
			else{save_params_graphe('graphe_affiche_photo','non');}
			if(isset($_POST['largeur_imposee_photo'])){save_params_graphe('graphe_largeur_imposee_photo',$_POST['largeur_imposee_photo']);}
			if(isset($_POST['affiche_mgen'])){save_params_graphe('graphe_affiche_mgen',$_POST['affiche_mgen']);}
			else{save_params_graphe('graphe_affiche_mgen','non');}
			if(isset($_POST['affiche_minmax'])){save_params_graphe('graphe_affiche_minmax',$_POST['affiche_minmax']);}
			else{save_params_graphe('graphe_affiche_minmax','non');}
			if(isset($_POST['affiche_moy_annuelle'])){save_params_graphe('graphe_affiche_moy_annuelle',$_POST['affiche_moy_annuelle']);}
			else{save_params_graphe('graphe_affiche_moy_annuelle','non');}

			if(isset($_POST['type_graphe'])){save_params_graphe('graphe_type_graphe',$_POST['type_graphe']);}

			if(isset($_POST['largeur_graphe'])){save_params_graphe('graphe_largeur_graphe',$_POST['largeur_graphe']);}
			if(isset($_POST['hauteur_graphe'])){save_params_graphe('graphe_hauteur_graphe',$_POST['hauteur_graphe']);}
			if(isset($_POST['taille_police'])){save_params_graphe('graphe_taille_police',$_POST['taille_police']);}
			if(isset($_POST['epaisseur_traits'])){save_params_graphe('graphe_epaisseur_traits',$_POST['epaisseur_traits']);}
			if(isset($_POST['temoin_image_escalier'])){save_params_graphe('graphe_temoin_image_escalier',$_POST['temoin_image_escalier']);}
			else{save_params_graphe('graphe_temoin_image_escalier','non');}
			if(isset($_POST['tronquer_nom_court'])){save_params_graphe('graphe_tronquer_nom_court',$_POST['tronquer_nom_court']);}

			if($msg==''){
				$msg="Paramètres enregistrés.";
			}
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Outil de visualisation";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

// Vérifications droits d'accès
if (
	($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesGraphParent") != "yes") OR
	($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesGraphEleve") != "yes")
	) {
	tentative_intrusion(1, "Tentative d'accès à l'outil de visualisation graphique sans y être autorisé.");
	echo "<p>Vous n'êtes pas autorisé à visualiser cette page.</p>";
	require "../lib/footer.inc.php";
	die();
}

//echo '<link rel="stylesheet" type="text/css" media="print" href="impression.css" />';
//echo "\n";


/*
$datay1 = array();
$datay2 = array();
$etiquette = array();
$graph_title = "";
$v_legend1 = "";
$v_legend2 = "";

$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$periode = isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);
$suiv = isset($_GET['suiv']) ? $_GET['suiv'] : 'no';
$prec = isset($_GET['prec']) ? $_GET['prec'] : 'no';
$v_eleve = isset($_POST['v_eleve']) ? $_POST['v_eleve'] : (isset($_GET['v_eleve']) ? $_GET['v_eleve'] : NULL);
*/

// Récupération des variables:
unset($id_classe);
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
// Vérifier s'il peut y avoir des accents dans un id_classe.
if(!is_numeric($id_classe)){$id_classe=NULL;}

unset($login_eleve);
$login_eleve = isset($_POST["login_eleve"]) ? $_POST["login_eleve"] : (isset($_GET["login_eleve"]) ? $_GET["login_eleve"] : NULL);

// Quelques filtrages de départ pour pré-initialiser la variable qui nous importe ici : $login_eleve
if ($_SESSION['statut'] == "responsable") {
	$get_eleves = mysql_query("SELECT e.login, e.prenom, e.nom " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$_SESSION['login']."')");

	if (mysql_num_rows($get_eleves) == 1) {
		// Un seul élève associé : on initialise tout de suite la variable $login_eleve
		$login_eleve = mysql_result($get_eleves, 0);
	} elseif (mysql_num_rows($get_eleves) == 0) {
		echo "<p>Il semble que vous ne soyez associé à aucun élève. Contactez l'administrateur pour résoudre cette erreur.</p>";
		require "../lib/footer.inc.php";
		die();
	} else {
		if ($login_eleve != null) {
			// $login_eleve a été défini mais l'utilisateur a plusieurs élèves associés. On vérifie
			// qu'il a le droit de visualiser les données pour l'élève sélectionné.
			$test = mysql_query("SELECT count(e.login) " .
					"FROM eleves e, responsables2 re, resp_pers r " .
					"WHERE (" .
					"e.login = '" . $login_eleve . "' AND " .
					"e.ele_id = re.ele_id AND " .
					"re.pers_id = r.pers_id AND " .
					"r.login = '" . $_SESSION['login'] . "')");
			if (mysql_result($test, 0) == 0) {
			    tentative_intrusion(2, "Tentative par un parent de visualisation graphique des résultats d'un élève dont il n'est pas responsable légal.");
			    echo "<p>Vous ne pouvez visualiser que les graphiques des élèves pour lesquels vous êtes responsable légal.</p>\n";
			    require("../lib/footer.inc.php");
				die();
			}
		}
	}
} else if ($_SESSION['statut'] == "eleve") {
	// Si l'utilisateur identifié est un élève, pas le choix, il ne peut consulter que son équipe pédagogique
	if ($login_eleve != null and $login_eleve != $_SESSION['login']) {
		tentative_intrusion(2, "Tentative par un élève de visualisation graphique des résultats d'un autre élève.");
	}
	$login_eleve = $_SESSION['login'];
}

if ($login_eleve and $login_eleve != null) {
	// On récupère la classe de l'élève, pour déterminer automatiquement le nombre de périodes
	// On part du postulat que même si l'élève change de classe en cours d'année, c'est pour aller
	// dans une classe qui a le même nombre de périodes...
	$id_classe = mysql_result(mysql_query("SELECT id_classe FROM j_eleves_classes jec WHERE login = '".$login_eleve."' LIMIT 1"), 0);
	$req = mysql_query("SELECT nom, prenom FROM eleves WHERE login='".$login_eleve."'");
	$nom_eleve = mysql_result($req, 0, "nom");
	$prenom_eleve = mysql_result($req, 0, "prenom");
}


include "../lib/periodes.inc.php";
// Cette bibliothèque permet de récupérer des tableaux de $nom_periode et $ver_periode (et $nb_periode)
// pour la classe considérée (valeur courante de $id_classe).

//echo "<p>$id_classe</p>\n";


// Choix de la classe:
if (!isset($id_classe) and $_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve") {
	echo "<div class='noprint'>\n";
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='index.php'>Autre outil de visualisation</a></p>\n";
	echo "</div>\n";

	//echo "<form action='$_PHP_SELF' name='form_choix_classe' method='post'>\n";
	//echo "<form action='".$_SERVER['PHP_SELF']."' name='form_choix_classe' method='post'>\n";
	echo "<p>Sélectionnez la classe : </p>\n";
	echo "<blockquote>\n";
	//$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	if($_SESSION['statut']=='scolarite'){
		//$call_data = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur'){
		//$call_data=mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe");
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
	}
	elseif($_SESSION['statut']=='cpe'){
		/*
		$call_data=mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe");
		*/
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe";
	}

	if(((getSettingValue("GepiAccesReleveProfToutesClasses")=="yes")&&($_SESSION['statut']=='professeur'))||
		((getSettingValue("GepiAccesReleveScol")=='yes')&&($_SESSION['statut']=='scolarite'))||
		((getSettingValue("GepiAccesReleveCpe")=='yes')&&($_SESSION['statut']=='cpe'))) {
		$sql="SELECT DISTINCT c.* FROM classes c ORDER BY classe";
	}

	$call_data=mysql_query($sql);

	$nombre_lignes = mysql_num_rows($call_data);

	unset($lien_classe);
	unset($txt_classe);
	$i = 0;
	while ($i < $nombre_lignes){
		$lien_classe[]=$_SERVER['PHP_SELF']."?id_classe=".mysql_result($call_data, $i, "id");
		$txt_classe[]=ucfirst(mysql_result($call_data, $i, "classe"));
		$i++;
	}

	tab_liste($txt_classe,$lien_classe,3);

	/*
	echo "<select name='id_classe' size='".min($nombre_lignes,10)."'>\n";
	$i = 0;
	while ($i < $nombre_lignes){
		$classe = mysql_result($call_data, $i, "classe");
		$ide_classe = mysql_result($call_data, $i, "id");
		//echo "<a href='eleve_classe.php?id_classe=$ide_classe'>$classe</a><br />\n";
		echo "<option value='$ide_classe'>$classe</option>\n";
		$i++;
	}
	echo "</select><br />\n";
	echo "<input type='submit' name='choix_classe' value='Envoyer' />\n";
	*/
	echo "</blockquote>\n";
	//echo "</p>\n";
	//echo "</form>\n";

	// Après ça, on arrive en fin de page avec le require("../lib/footer.inc.php");

} elseif ($_SESSION['statut'] == "responsable" and $login_eleve == null) {
	// On demande à l'utilisateur de choisir l'élève pour lequel il souhaite visualiser les données
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
	echo "<p>Cliquez sur le nom de l'élève pour lequel vous souhaitez visualiser les moyennes :</p>";
	while ($current_eleve = mysql_fetch_object($get_eleves)) {
		echo "<p><a href='affiche_eleve.php?login_eleve=".$current_eleve->login."'>".$current_eleve->prenom." ".$current_eleve->nom."</a></p>";
	}
	// Après ça, on arrive en fin de page avec le require("../lib/footer.inc.php");

} else {
	// A ce stade:
	// - la classe est choisie (prof, scol ou cpe) ou récupérée d'après le login élève choisi (responsable, eleve): $id_classe
	// - le login élève est imposé pour un utilisateur connecté élève ou responsable: $login_eleve et $eleve1=$login_eleve
	//   sinon, on récupère $_POST['eleve1']

	// Capture des mouvements de la souris et affichage des cadres d'info
	// Remonté pour éviter/limiter des erreurs JavaScript lors du chargement...
	echo "<script type='text/javascript' src='cadre_info.js'></script>\n";

	if ($_SESSION['statut'] != "responsable" and $_SESSION['statut'] != "eleve") {
		/*
		foreach($_POST as $post => $val){
			echo $post.' : '.$val."<br />\n";
		}
		*/

		echo "<div class='noprint'>\n";
		echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='index.php'>Autre outil de visualisation</a>";
		// La classe est choisie.
		// On ajoute l'accès/retour à une autre classe:
		//echo "<a href=\"$_PHP_SELF\">Choisir une autre classe</a>|";
		//echo " | <a href=\"".$_SERVER['PHP_SELF']."\">Choisir une autre classe</a></p>";
		echo " | <a href=\"".$_SERVER['PHP_SELF']."\">Choisir une autre classe</a>";

		// =================================
		// AJOUT: boireaus
		// Pour proposer de passer à la classe suivante ou à la précédente
		//$sql="SELECT id, classe FROM classes ORDER BY classe";
		if($_SESSION['statut']=='scolarite'){
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

		$res_class_tmp=mysql_query($sql);
		if(mysql_num_rows($res_class_tmp)>0){
			$id_class_prec=0;
			$id_class_suiv=0;
			$temoin_tmp=0;
			while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				if($lig_class_tmp->id==$id_classe){
					$temoin_tmp=1;
					if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
						$id_class_suiv=$lig_class_tmp->id;
					}
					else{
						$id_class_suiv=0;
					}
				}
				if($temoin_tmp==0){
					$id_class_prec=$lig_class_tmp->id;
				}
			}
		}
		// =================================

		if(isset($id_class_prec)){
			if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec'>Classe précédente</a>";}
		}
		if(isset($id_class_suiv)){
			if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv'>Classe suivante</a>";}
		}
		echo "</p>\n";
		echo "</div>\n";
	} else {
		echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
	}

	//===============================================
	// Récupération des variables:
	//$id_classe=$_POST['id_classe']; // Récupérée plus haut...
	$eleve1=isset($_POST['eleve1']) ? $_POST['eleve1'] : NULL;
	// Login d'un élève réclamé par Précédent/Suivant:
	$eleve1b=isset($_POST['eleve1b']) ? $_POST['eleve1b'] : NULL;
	if($eleve1b!=''){
		$eleve1=$eleve1b;
	}
	/*
	// Modif: pour éviter une fausse alerte en 'responsable' sur la valeur de $eleve2
	//$eleve2=isset($_POST['eleve2']) ? $_POST['eleve2'] : NULL;
	$eleve2=isset($_POST['eleve2']) ? $_POST['eleve2'] : "moyclasse";
	*/
	$eleve2=isset($_POST['eleve2']) ? $_POST['eleve2'] : NULL;

	// Vérification de sécurité
	if ($_SESSION['statut'] == "eleve") {
		$eleve1 = $login_eleve;
	}
	if ($_SESSION['statut'] == "responsable") {
		if ($login_eleve != null) {
			$eleve1 = $login_eleve;
		}
		$test = mysql_query("SELECT count(e.login) " .
				"FROM eleves e, responsables2 re, resp_pers r " .
				"WHERE (" .
				"e.login = '" . $eleve1 . "' AND " .
				"e.ele_id = re.ele_id AND " .
				"re.pers_id = r.pers_id AND " .
				"r.login = '" . $_SESSION['login'] . "')");
		if (mysql_result($test, 0) == 0) {
		    tentative_intrusion(3, "Tentative (forte) d'un parent de visualisation graphique des résultats d'un élève dont il n'est pas responsable légal.");
		    echo "<p>Vous ne pouvez visualiser que les graphiques des élèves pour lesquels vous êtes responsable légal.\n";
		    require("../lib/footer.inc.php");
			die();
		}
	}
	if ($_SESSION['statut'] == "eleve" OR $_SESSION['statut'] == "responsable") {
		// On filtre eleve2 :
		if(!isset($eleve2)) {$eleve2 = "moyclasse";}
		if ($eleve2 != "moyclasse" and $eleve2 != "moymin" and $eleve2 != "moymax") {
			tentative_intrusion(3, "Tentative de manipulation de la seconde source de données sur la visualisation graphique des résultats (détournement de _eleve2_, qui ne peut, dans le cas d'un utilisateur parent ou eleve, ne correspondre qu'à une moyenne et non un autre élève).");
			$eleve2 = "moyclasse";
		}
	}

	// On évite d'initialiser à NULL pour permettre de pré-cocher le choix_periode.
	//$choix_periode=isset($_POST['choix_periode']) ? $_POST['choix_periode'] : NULL;
	//$choix_periode=isset($_POST['choix_periode']) ? $_POST['choix_periode'] : "toutes_periodes";
	$choix_periode=isset($_POST['choix_periode']) ? $_POST['choix_periode'] : "periode";
	//if($choix_periode!='toutes_periodes'){
	if(($choix_periode!='toutes_periodes')&&(isset($_POST['periode']))){
		$periode=$_POST['periode'];
	}
	else{
		$periode="";
	}



	//======================================================================
	//======================================================================
	//======================================================================

	// On récupère de $_POST les paramètres d'affichage s'ils ont été transmis, sinon, on les récupère dans la base MySQL.

	//$affiche_photo=isset($_POST['affiche_photo']) ? $_POST['affiche_photo'] : '';
	if(isset($_POST['affiche_photo'])){
		$affiche_photo=$_POST['affiche_photo'];
	}
	else{
		if(getSettingValue('graphe_affiche_photo')){
			$affiche_photo=getSettingValue('graphe_affiche_photo');
		}
		else{
			$affiche_photo="non";
		}
	}

	if(isset($_POST['largeur_imposee_photo'])){
		$largeur_imposee_photo=$_POST['largeur_imposee_photo'];
	}
	else{
		if(getSettingValue('graphe_largeur_imposee_photo')){
			$largeur_imposee_photo=getSettingValue('graphe_largeur_imposee_photo');
		}
		else{
			$largeur_imposee_photo=100;
		}
	}
	// On s'assure que la largeur est valide:
	if((strlen(ereg_replace("[0-9]","",$largeur_imposee_photo))!=0)||($largeur_imposee_photo=="")){$largeur_imposee_photo=100;}


	if(isset($_POST['affiche_mgen'])){
		$affiche_mgen=$_POST['affiche_mgen'];
	}
	else{
		if(getSettingValue('graphe_affiche_mgen')){
			$affiche_mgen=getSettingValue('graphe_affiche_mgen');
		}
		else{
			$affiche_mgen="non";
		}
	}

	if(isset($_POST['affiche_minmax'])){
		$affiche_minmax=$_POST['affiche_minmax'];
	}
	else{
		if(getSettingValue('graphe_affiche_minmax')){
			$affiche_minmax=getSettingValue('graphe_affiche_minmax');
		}
		else{
			$affiche_minmax="non";
		}
	}

	if(isset($_POST['affiche_moy_annuelle'])){
		$affiche_moy_annuelle=$_POST['affiche_moy_annuelle'];
	}
	else{
		if(getSettingValue('graphe_affiche_moy_annuelle')){
			$affiche_moy_annuelle=getSettingValue('graphe_affiche_moy_annuelle');
		}
		else{
			$affiche_moy_annuelle="non";
		}
	}



	if(isset($_POST['type_graphe'])){

		//echo "\$_POST['type_graphe']=".$_POST['type_graphe']."<br />\n";

		if($_POST['type_graphe']=='etoile'){
			$type_graphe='etoile';
		}
		else{
			$type_graphe='courbe';
		}
	}
	else{
		if(getSettingValue('graphe_type_graphe')){
			$type_graphe=getSettingValue('graphe_type_graphe');
		}
		else{
			$type_graphe='courbe';
		}
	}

	//echo "\$type_graphe=".$type_graphe."<br />\n";


	if(isset($_POST['largeur_graphe'])){
		$largeur_graphe=$_POST['largeur_graphe'];
	}
	else{
		if(getSettingValue('graphe_largeur_graphe')){
			$largeur_graphe=getSettingValue('graphe_largeur_graphe');
		}
		else{
			$largeur_graphe=600;
		}
	}
	if((strlen(ereg_replace("[0-9]","",$largeur_graphe))!=0)||($largeur_graphe=="")){
		$largeur_graphe=600;
	}


	if(isset($_POST['hauteur_graphe'])){
		$hauteur_graphe=$_POST['hauteur_graphe'];
		//echo "\$hauteur_graphe=$hauteur_graphe<br />";
	}
	else{
		if(getSettingValue('graphe_hauteur_graphe')){
			$hauteur_graphe=getSettingValue('graphe_hauteur_graphe');
		}
		else{
			$hauteur_graphe=400;
		}
	}
	if((strlen(ereg_replace("[0-9]","",$hauteur_graphe))!=0)||($hauteur_graphe=="")){
		$hauteur_graphe=400;
	}


	if(isset($_POST['taille_police'])){
		$taille_police=$_POST['taille_police'];
	}
	else{
		if(getSettingValue('graphe_taille_police')){
			$taille_police=getSettingValue('graphe_taille_police');
		}
		else{
			$taille_police=2;
		}
	}
	if((strlen(ereg_replace("[0-9]","",$taille_police))!=0)||($taille_police<1)||($taille_police>6)||($taille_police=="")){
		$taille_police=2;
	}



	if(isset($_POST['epaisseur_traits'])){
		$epaisseur_traits=$_POST['epaisseur_traits'];
	}
	else{
		if(getSettingValue('graphe_epaisseur_traits')){
			$epaisseur_traits=getSettingValue('graphe_epaisseur_traits');
		}
		else{
			$epaisseur_traits=2;
		}
	}
	if((strlen(ereg_replace("[0-9]","",$epaisseur_traits))!=0)||($epaisseur_traits<1)||($epaisseur_traits>6)||($epaisseur_traits=="")){
		$epaisseur_traits=2;
	}


	// Pour présenter ou non, les noms longs en entier en travers sous le graphe.
	if(isset($_POST['temoin_image_escalier'])){
		$temoin_image_escalier=$_POST['temoin_image_escalier'];
	}
	else{
		if(getSettingValue('graphe_temoin_image_escalier')){
			$temoin_image_escalier=getSettingValue('graphe_temoin_image_escalier');
		}
		else{
			$temoin_image_escalier="non";
		}
	}


	// A zéro caractères, on ne tronque pas
	if(isset($_POST['tronquer_nom_court'])){
		$tronquer_nom_court=$_POST['tronquer_nom_court'];
	}
	else{
		if(getSettingValue('graphe_tronquer_nom_court')){
			$tronquer_nom_court=getSettingValue('graphe_tronquer_nom_court');
		}
		else{
			$tronquer_nom_court=0;
		}
	}


/*	$affiche_photo=isset($_POST['affiche_photo']) ? $_POST['affiche_photo'] : 'non';
	$largeur_imposee_photo=isset($_POST['largeur_imposee_photo']) ? $_POST['largeur_imposee_photo'] : '100';
	// On s'assure que la largeur est valide:
	if((strlen(ereg_replace("[0-9]","",$largeur_imposee_photo))!=0)||($largeur_imposee_photo=="")){$largeur_imposee_photo=100;}

	//$affiche_mgen=isset($_POST['affiche_mgen']) ? $_POST['affiche_mgen'] : '';
	//$affiche_minmax=isset($_POST['affiche_minmax']) ? $_POST['affiche_minmax'] : '';
	$affiche_mgen=isset($_POST['affiche_mgen']) ? $_POST['affiche_mgen'] : 'non';
	$affiche_minmax=isset($_POST['affiche_minmax']) ? $_POST['affiche_minmax'] : 'non';
	$affiche_moy_annuelle=isset($_POST['affiche_moy_annuelle']) ? $_POST['affiche_moy_annuelle'] : 'non';

	$largeur_graphe=isset($_POST['largeur_graphe']) ? $_POST['largeur_graphe'] : '600';
	if((strlen(ereg_replace("[0-9]","",$largeur_graphe))!=0)||($largeur_graphe=="")){
		$largeur_graphe=600;
	}
	$hauteur_graphe=isset($_POST['hauteur_graphe']) ? $_POST['hauteur_graphe'] : '400';
	if((strlen(ereg_replace("[0-9]","",$hauteur_graphe))!=0)||($hauteur_graphe=="")){
		$hauteur_graphe=400;
	}

	$taille_police=isset($_POST['taille_police']) ? $_POST['taille_police'] : '3';
	if((strlen(ereg_replace("[0-9]","",$taille_police))!=0)||($taille_police<1)||($taille_police>6)||($taille_police=="")){
		$taille_police=3;
	}

	$epaisseur_traits=isset($_POST['epaisseur_traits']) ? $_POST['epaisseur_traits'] : '2';
	if((strlen(ereg_replace("[0-9]","",$epaisseur_traits))!=0)||($epaisseur_traits<1)||($epaisseur_traits>6)||($epaisseur_traits=="")){
		$epaisseur_traits=2;
	}

	$temoin_image_escalier=isset($_POST['temoin_image_escalier']) ? $_POST['temoin_image_escalier'] : 'non';

	// A zéro caractères, on ne tronque pas
	$tronquer_nom_court=isset($_POST['tronquer_nom_court']) ? $_POST['tronquer_nom_court'] : '0';
*/
	//===============================================

	//echo "\$temoin_imageps=$temoin_imageps<br />";


	//======================================================================
	//======================================================================
	//======================================================================

	if(isset($_POST['parametrer_affichage'])){
		if($_POST['parametrer_affichage']=='y'){
			/*
			foreach($_POST as $post => $val){
				echo $post.' : '.$val."<br />\n";
			}
			*/

			echo "<h2>Paramétrage de l'affichage du graphique</h2>\n";

			echo "<form action='".$_SERVER['PHP_SELF']."#graph' name='form_parametrage_affichage' method='post'>\n";
			echo "<p align='center'><input type='submit' name='Valider' value='Valider' /></p>\n";

			echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
			echo "<input type='hidden' name='is_posted' value='y' />\n";
			if($_SESSION['statut'] == "eleve" OR $_SESSION['statut'] == "responsable"){
				echo "<input type='hidden' name='eleve1' value='".$login_eleve."'/>\n";
				echo "<input type='hidden' name='login_eleve' value='".$login_eleve."'/>\n";
			}
			else{
				echo "<input type='hidden' name='eleve1' value='".$eleve1."'/>\n";
				echo "<input type='hidden' name='numeleve1' value='".$_POST['numeleve1']."'/>\n";
			}
			echo "<input type='hidden' name='eleve2' value='".$eleve2."'/>\n";
			echo "<input type='hidden' name='choix_periode' value='".$choix_periode."'/>\n";
			echo "<input type='hidden' name='periode' value='".$periode."'/>\n";

			// Paramètres:
			echo "<p><b>Moyennes et périodes</b></p>\n";
			echo "<blockquote>\n";

			if($affiche_mgen=='oui'){$checked=" checked='yes'";}else{$checked="";}
			echo "<table border='0'>\n";
			echo "<tr valign='top'><td>Afficher la moyenne générale:</td><td><input type='checkbox' name='affiche_mgen' value='oui'$checked /></td></tr>\n";

			if($affiche_minmax=='oui'){$checked=" checked='yes'";}else{$checked="";}
			echo "<tr valign='top'><td>Afficher les bandes moyenne minimale/maximale:<br />(<i>cet affichage n'est pas appliqué en mode 'Toutes_les_periodes'</i>)</td><td><input type='checkbox' name='affiche_minmax' value='oui'$checked /></td></tr>\n";

			//$affiche_moy_annuelle
			if($affiche_moy_annuelle=='oui'){$checked=" checked='yes'";}else{$checked="";}
			echo "<tr valign='top'><td>Afficher les moyennes annuelles:<br />(<i>en mode 'Toutes_les_periodes' uniquement</i>)</td><td><input type='checkbox' name='affiche_moy_annuelle' value='oui'$checked /></td></tr>\n";

			echo "</table>\n";
			echo "</blockquote>\n";

			//echo "<hr width='150' />\n";

			// Paramètres d'affichage:
			echo "<p><b>Graphe</b></p>\n";
			echo "<blockquote>\n";
			echo "<table border='0'>\n";

			// Graphe en courbe ou étoile
			echo "<tr><td>Graphe en </td>\n";
			if($type_graphe=='courbe'){$checked=" checked='yes'";}else{$checked="";}
			echo "<td><input type='radio' name='type_graphe' value='courbe'$checked /> courbe<br />\n";
			if($type_graphe=='etoile'){$checked=" checked='yes'";}else{$checked="";}
			echo "<input type='radio' name='type_graphe' value='etoile'$checked /> étoile\n";
			echo "</td></tr>\n";

			// - dimensions de l'image
			echo "<tr><td>Largeur (<i>en pixels</i>):</td><td><input type='text' name='largeur_graphe' value='$largeur_graphe' size='3' /></td></tr>\n";
			//echo " - \n";
			echo "<tr><td>Hauteur (<i>en pixels</i>):</td><td><input type='text' name='hauteur_graphe' value='$hauteur_graphe' size='3' /></td></tr>\n";

			// - taille des polices
			echo "<tr><td>Taille des polices:</td><td><select name='taille_police'>\n";
			for($i=1;$i<=6;$i++){
				if($taille_police==$i){$selected=" selected='yes'";}else{$selected="";}
				echo "<option value='$i'$selected>$i</option>\n";
			}
			echo "</select></td></tr>\n";

			// - epaisseur des traits
			echo "<tr><td>Epaisseur des courbes:</td><td><select name='epaisseur_traits'>\n";
			for($i=1;$i<=6;$i++){
				if($epaisseur_traits==$i){$selected=" selected='yes'";}else{$selected="";}
				echo "<option value='$i'$selected>$i</option>\n";
			}
			echo "</select></td></tr>\n";

			// - modèle de couleurs

			//if($temoin_imageps=='oui'){$checked=" checked='yes'";}else{$checked="";}
			if($temoin_image_escalier=='oui'){$checked=" checked='yes'";}else{$checked="";}
			//echo "Utiliser ImagePs: <input type='checkbox' name='temoin_imageps' value='oui'$checked /><br />\n";
			echo "<tr><td>Afficher les noms longs de matières:<br />(<i>en légende sous le graphe</i>)</td><td><input type='checkbox' name='temoin_image_escalier' value='oui'$checked /></td></tr>\n";

			//echo "<tr><td>Tronquer le nom court<br />de matière à <a href='javascript:alert(\"A zéro caractères, on ne tronque pas le nom court de matière affiché en haut du graphe.\")'>X</a> caractères:</td><td><select name='tronquer_nom_court'>\n";
			echo "<tr><td>Tronquer le nom court de la matière à <a href='#' onclick='alert(\"A zéro caractères, on ne tronque pas le nom court de matière affiché en haut du graphe.\")'>X</a> caractères:<br />(<i>pour éviter des collisions de légendes en haut du graphe</i>)</td><td><select name='tronquer_nom_court'>\n";
			for($i=0;$i<=10;$i++){
				if($tronquer_nom_court==$i){$selected=" selected='yes'";}else{$selected="";}
				echo "<option value='$i'$selected>$i</option>\n";
			}
			echo "</select></td></tr>\n";
			echo "</table>\n";
			echo "</blockquote>\n";



			// - Affichage de la photo
			echo "<p><b>Paramètres des photos</b></p>\n";
			echo "<blockquote>\n";
			echo "<table border='0'>\n";
			if(($affiche_photo=='')||($affiche_photo=='oui')){$checked=" checked='yes'";}else{$checked="";}
			echo "<tr><td>Afficher la photo de l'élève si elle existe:</td><td><input type='radio' name='affiche_photo' value='oui'$checked />Oui / \n";
			if($affiche_photo=='non'){$checked=" checked='yes'";}else{$checked="";}
			echo "Non<input type='radio' name='affiche_photo' value='non'$checked /></td></tr>\n";

			// - Largeur imposée pour la photo
			echo "<tr><td>Largeur de la photo (<i>en pixels</i>):</td><td><input type='text' name='largeur_imposee_photo' value='$largeur_imposee_photo' size='3' /></td></tr>\n";
			//echo "</p>\n";
			echo "</table>\n";
			echo "</blockquote>\n";



			if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
				echo "<p><b>Couleurs</b></p>\n";
				echo "<blockquote>\n";
				//echo "<hr width='150' />\n";
				//echo "<p>\n";
				echo "<a href='choix_couleurs.php' target='blank'>Modifier les couleurs</a>\n";
				//echo "</p>\n";
				echo "</blockquote>\n";
			}


			echo "<p align='center'>";
			if($_SESSION['statut']=='scolarite'){
				//echo "<input type='checkbox' name='save_params' value='y' /> <b>Enregistrer les paramètres</b>\n";
				echo "<input type='hidden' name='save_params' value='' />\n";
				echo "<input type='button' onClick=\"document.forms['form_parametrage_affichage'].save_params.value='y';document.forms['form_parametrage_affichage'].submit();\" name='Enregistrer' value='Enregistrer les paramètres dans la base' />\n";
				echo "<br />\n";
			}

			echo "<input type='submit' name='Valider' value='Valider' /></p>\n";

			echo "</form>\n";

			require("../lib/footer.inc.php");
			die();
		}
	}





	// Nom de la classe:
	$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
	$classe = mysql_result($call_classe, "0", "classe");



	/*
	if ($_SESSION['statut'] != "responsable" and $_SESSION['statut'] != "eleve") {
		if(!isset($eleve1)){
			$call_eleve = mysql_query("SELECT DISTINCT e.login FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' and e.login = c.login) ORDER BY nom,prenom LIMIT 1");
			if(mysql_num_rows($call_eleve)!=0){
				$ligtmp=mysql_fetch_object($call_eleve);
				$eleve1=$ligtmp->login;
				$eleve2='moyclasse';
				$num_periode=1;
				$periode=1;
				$choix_periode="periode";
			}
		}
	}
	*/



	// Infos DEBUG:
	//echo "<p>classe=$classe<br />eleve1=$eleve1<br />eleve2=$eleve2<br />choix_periode=$choix_periode<br />periode=$periode<br />largeur_imposee_photo=$largeur_imposee_photo</p>\n";


	// Capture des mouvements de la souris et affichage des cadres d'info
	//echo "<script type='text/javascript' src='cadre_info.js'></script>\n";


	echo "<table>\n";
	echo "<tr valign='top'>\n";
	//====================================================================
	// Bande de pilotage:
	echo "<td class='noprint' align='center'>\n";
	//echo "<form action='$_PHP_SELF#graph' name='form_choix_eleves' method='post'>\n";
	echo "<form action='".$_SERVER['PHP_SELF']."#graph' name='form_choix_eleves' method='post'>\n";
	//echo "<form action='$_PHP_SELF' name='form_choix_eleves' method='POST'>\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";

	echo "<input type='hidden' name='is_posted' value='y' />\n";

	//echo "\$eleve1=$eleve1 et \$affiche_photo=$affiche_photo<br />";

	// Affichage de la photo si elle existe:
	if((isset($eleve1))&&($affiche_photo!="non")){
		//$chemin_photos='/var/wwws/gepi/photos';

		$sql="SELECT elenoet FROM eleves WHERE login='$eleve1'";
		$res_elenoet=mysql_query($sql);
		if(mysql_num_rows($res_elenoet)==1){
			$lig_elenoet=mysql_fetch_object($res_elenoet);
			$elenoet1=$lig_elenoet->elenoet;

			/*
			//if(file_exists("$chemin_photos/$eleve1.jpg")){
			if(file_exists("../photos/eleves/$elenoet1.jpg")){
				// Récupérer les dimensions de la photo...
				//$dimimg=getimagesize("../photos/$eleve1.jpg");
				$dimimg=getimagesize("../photos/eleves/$elenoet1.jpg");
				//echo "$dimimg[0] et $dimimg[1]";

				$largimg=$largeur_imposee_photo;
				$hautimg=round($dimimg[1]*$largeur_imposee_photo/$dimimg[0]);

				//echo "<img src='../photos/$eleve1.jpg' width='$largimg' height='$hautimg'>\n";
				echo "<img src='../photos/eleves/$elenoet1.jpg' width='$largimg' height='$hautimg' alt='Photo de $eleve1' />\n";
			}
			elseif(file_exists("../photos/eleves/0$elenoet1.jpg")){
				// Récupérer les dimensions de la photo...
				//$dimimg=getimagesize("../photos/$eleve1.jpg");
				$dimimg=getimagesize("../photos/eleves/0$elenoet1.jpg");
				//echo "$dimimg[0] et $dimimg[1]";

				$largimg=$largeur_imposee_photo;
				$hautimg=round($dimimg[1]*$largeur_imposee_photo/$dimimg[0]);

				//echo "<img src='../photos/$eleve1.jpg' width='$largimg' height='$hautimg'>\n";
				echo "<img src='../photos/eleves/0$elenoet1.jpg' width='$largimg' height='$hautimg' alt='Photo de $eleve1' />\n";
			}
			*/
			$photo=nom_photo($elenoet1);
			if("$photo"!=""){
				if(file_exists("../photos/eleves/$photo")){
					$dimimg=getimagesize("../photos/eleves/$photo");

					$largimg=$largeur_imposee_photo;
					$hautimg=round($dimimg[1]*$largeur_imposee_photo/$dimimg[0]);

					echo "<img src='../photos/eleves/$photo' width='$largimg' height='$hautimg' alt='Photo de $eleve1' />\n";
				}
			}

		}
	}

	echo "<p>\n";
	echo "<b>Classe de $classe</b>\n";
	echo "<br />\n";

	if ($_SESSION['statut'] != "responsable" and $_SESSION['statut'] != "eleve") {
		// Choix des élèves:
		$call_eleve = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' and e.login = c.login) order by nom,prenom");
		$nombreligne = mysql_num_rows($call_eleve);

		echo "Choisir l'élève:<br />\n";
		echo "<select name='eleve1' onchange=\"document.forms['form_choix_eleves'].submit();\">\n";
		$cpt=1;
		$numeleve1=0;
		while($ligne=mysql_fetch_object($call_eleve)){
			// Le login est la clé liant les tables eleves et j_eleves_classes
			$tab_login_eleve[$cpt]="$ligne->login";
			$tab_nomprenom_eleve[$cpt]="$ligne->nom $ligne->prenom";
			if($tab_login_eleve[$cpt]==$eleve1){
				$selected=" selected='yes'";
				$numeleve1=$cpt;
			}
			else{
				$selected="";
			}
			echo "<option value='$tab_login_eleve[$cpt]'$selected>$tab_nomprenom_eleve[$cpt]</option>\n";
			$cpt++;
		}
		echo "</select>\n";
		echo "<br />\n";



		echo "et comparer avec:<br />\n";
		echo "<select name='eleve2' onchange=\"document.forms['form_choix_eleves'].submit();\">\n";
		for($cpt=1;$cpt<=$nombreligne;$cpt++){
			if($tab_login_eleve[$cpt]==$eleve2){
				$selected=" selected='yes'";
				$numeleve2=$cpt;
			}
			else{
				$selected="";
			}
			echo "<option value='$tab_login_eleve[$cpt]'$selected>$tab_nomprenom_eleve[$cpt]</option>\n";
		}
		if($eleve2=='moyclasse'){$selected=" selected='yes'";}else{$selected="";}
		if(!isset($eleve2)){$selected=" selected='yes'";}
		echo "<option value='moyclasse'$selected>Moyenne classe</option>\n";
		if($eleve2=='moymax'){$selected=" selected='yes'";}else{$selected="";}
		echo "<option value='moymax'$selected>Moyenne max.</option>\n";
		if($eleve2=='moymin'){$selected=" selected='yes'";}else{$selected="";}
		echo "<option value='moymin'$selected>Moyenne min.</option>\n";
		echo "</select>\n";
		echo "<br />\n";

		// Pour passer à l'élève précédent ou au suivant:
		echo "<script type='text/javascript' language='JavaScript'>\n";
		$precedent=$numeleve1-1;
		$suivant=$numeleve1+1;
		echo "precedent=$precedent\n";
		echo "suivant=$suivant\n";
		echo "function eleve_precedent(){
			if(document.getElementById('numeleve1').value>1){";
	    // On effectue un test pour éviter de tenter de chercher $tab_login_eleve[$precedent] si $precedent=0
	    if($precedent>0){
	        echo "		document.getElementById('eleve1b').value='$tab_login_eleve[$precedent]';
	        document.forms['form_choix_eleves'].submit();";
	    }
		echo "		return true;
			}
			else{
				document.getElementById('eleve1b').value='';
			}
		}
		function eleve_suivant(){
			if(document.getElementById('numeleve1').value<$nombreligne){";
	    if($suivant<$nombreligne+1){
	        echo "document.getElementById('eleve1b').value='$tab_login_eleve[$suivant]';
	        document.forms['form_choix_eleves'].submit();";
	    }
			echo "          return true;
			}
			else{
				document.getElementById('eleve1b').value='';
			}
		}
		</script>\n";

		//echo "<p>\n";
		echo "<input type='hidden' name='numeleve1' id='numeleve1' value='$numeleve1' size='3' />\n";
		// 'eleve1b' est destiné au passage du nom de l'élève par les boutons Précédent/Suivant
	 	// Cette valeur l'emporte sur le contenu de 'eleve1'
		echo "<input type='hidden' name='eleve1b' id='eleve1b' value='' />\n";

	    if($precedent>0){
			//echo "<input type='button' name='precedent' value='<<' onClick='eleve_precedent();' />\n";
			echo "<a href='javascript:eleve_precedent();'>Élève précédent</a><br />\n";
		}

		//echo "<input type='submit' name='choix_eleves' value='Afficher' />\n";
		echo "<a href=\"javascript:document.forms['form_choix_eleves'].submit();\">Actualiser</a>\n";

	    if($suivant<$nombreligne+1){
			echo "<br />\n";
			//echo "<input type='button' name='suivant' value='>>' onClick='eleve_suivant();' />\n";
			echo "<a href='javascript:eleve_suivant();'>Élève suivant</a>";
		}
		echo "</p>\n";

		echo "<hr width='150' />\n";

	} else {
		// Cas d'un responsable ou d'un élève :
		// Pas de sélection de l'élève, il est déjà fixé.
		// Pas de sélection non plus de la comparaison : c'est la moyenne de la classe (ou moy min ou max).
		echo "<p>Eleve : ".$prenom_eleve . " " .$nom_eleve."</p>\n";
		echo "<input type='hidden' name='eleve1' value='".$login_eleve."'/>\n";
		echo "<input type='hidden' name='login_eleve' value='".$login_eleve."'/>\n";
		echo "et <select name='eleve2'>\n";
		if($eleve2=='moyclasse'){$selected=" selected='yes'";}else{$selected="";}
		if(!isset($eleve2)){$selected=" selected='yes'";}
		echo "<option value='moyclasse'$selected>Moyenne classe</option>\n";
		if($eleve2=='moymax'){$selected=" selected='yes'";}else{$selected="";}
		echo "<option value='moymax'$selected>Moyenne max.</option>\n";
		if($eleve2=='moymin'){$selected=" selected='yes'";}else{$selected="";}
		echo "<option value='moymin'$selected>Moyenne min.</option>\n";
		echo "</select>\n";
		echo "<br />\n";
		echo "<input type='submit' name='choix_eleves' value='Afficher' style='margin-bottom: 3px;'/><br />\n";
	}

	// Choix de la période
	echo "Choisir la période:<br />\n";
	if($choix_periode=='periode'){$checked=" checked='yes'";}else{$checked="";}
	//echo "<input type='radio' name='choix_periode' id='choix_periode' value='periode' checked='true'$checked />\n";
	echo "<input type='radio' name='choix_periode' id='choix_periode' value='periode' $checked onchange=\"document.forms['form_choix_eleves'].submit();\" />\n";
	echo "<select name='periode' onfocus=\"document.getElementById('choix_periode').checked='true'\" onchange=\"document.forms['form_choix_eleves'].submit();\">\n";
	for($i=1;$i<$nb_periode;$i++){
		if($periode==$nom_periode[$i]){$selected=" selected='yes'";}else{$selected="";}
		echo "<option value='$nom_periode[$i]'$selected>$nom_periode[$i]</option>\n";
	}
	echo "</select>\n";
	echo "<br />\n";
	if($choix_periode=='toutes_periodes'){$checked=" checked='yes'";}else{$checked="";}
	echo "<input type='radio' name='choix_periode' value='toutes_periodes'$checked onchange=\"document.forms['form_choix_eleves'].submit();\" /> Toutes les périodes\n";

	echo "<hr width='150' />\n";

	//======================================================================
	//======================================================================
	//======================================================================

	//========================
	// PARAMETRES D'AFFICHAGE
	//========================

	echo "<input type='hidden' name='affiche_mgen' value='$affiche_mgen' />\n";
	echo "<input type='hidden' name='affiche_minmax' value='$affiche_minmax' />\n";
	echo "<input type='hidden' name='affiche_moy_annuelle' value='$affiche_moy_annuelle' />\n";
	echo "<input type='hidden' name='type_graphe' value='$type_graphe' />\n";
	echo "<input type='hidden' name='largeur_graphe' value='$largeur_graphe' />\n";
	echo "<input type='hidden' name='hauteur_graphe' value='$hauteur_graphe' />\n";
	echo "<input type='hidden' name='taille_police' value='$taille_police' />\n";
	echo "<input type='hidden' name='epaisseur_traits' value='$epaisseur_traits' />\n";
	echo "<input type='hidden' name='temoin_image_escalier' value='$temoin_image_escalier' />\n";
	echo "<input type='hidden' name='tronquer_nom_court' value='$tronquer_nom_court' />\n";
	echo "<input type='hidden' name='affiche_photo' value='$affiche_photo' />\n";
	echo "<input type='hidden' name='largeur_imposee_photo' value='$largeur_imposee_photo' />\n";

	echo "<input type='hidden' name='parametrer_affichage' value='' />\n";
	echo "<a href='".$_SERVER['PHP_SELF']."' onClick='document.forms[\"form_choix_eleves\"].parametrer_affichage.value=\"y\";document.forms[\"form_choix_eleves\"].submit();return false;'>Paramétrer l'affichage</a>.<br />\n";

/*
	echo "<script type='text/javascript'>
	function display_div(){
		if(document.getElementById('id_params').checked==true){
			document.getElementById('div_params').style.display='block';
			for(i=1;i<=4;i++){
				if(document.getElementById('div_categorie_params'+i).checked==true){
					document.getElementById('div_params_'+i).style.display='block';
				}
				else{
					document.getElementById('div_params_'+i).style.display='none';
				}
			}
		}
		else{
			document.getElementById('div_params').style.display='none';
		}

	}
</script>\n";


	echo "<input type='checkbox' name='params' id='id_params' value='oui' onchange='display_div()' /> <b>Afficher les paramètres</b><br />\n";

	echo "<div id='div_params' style='display:block;'>\n";

	echo "<table border='0'>\n";

	echo "<tr><td><input type='radio' name='div_categorie_params' id='div_categorie_params1' value='1' onchange='display_div()' /> </td><td>Moyennes et périodes</td></tr>\n";
	echo "<tr><td><input type='radio' name='div_categorie_params' id='div_categorie_params2' value='2' onchange='display_div()' /> </td><td>Dimensions</td></tr>\n";
	echo "<tr><td><input type='radio' name='div_categorie_params' id='div_categorie_params3' value='3' onchange='display_div()' /> </td><td>Photo</td></tr>\n";

	if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
		echo "<tr><td><input type='radio' name='div_categorie_params' id='div_categorie_params4' value='4' onchange='display_div()' /> </td><td>Couleurs</td></tr>\n";
	}

	echo "</table>\n";



	echo "<div id='div_params_1' style='display:block; border: 1px solid black;'>";
	echo "<b>Moyennes et périodes</b><br />";

	if($affiche_mgen=='oui'){$checked=" checked='yes'";}else{$checked="";}
	echo "<table border='0'>\n";
	echo "<tr valign='top'><td>Afficher la moyenne générale:</td><td><input type='checkbox' name='affiche_mgen' value='oui'$checked /></td></tr>\n";

	if($affiche_minmax=='oui'){$checked=" checked='yes'";}else{$checked="";}
	echo "<tr valign='top'><td>Afficher les bandes Min/max:<br />(<i>pas en mode 'Toutes_les_periodes'</i>)</td><td><input type='checkbox' name='affiche_minmax' value='oui'$checked /></td></tr>\n";

	//$affiche_moy_annuelle
	if($affiche_moy_annuelle=='oui'){$checked=" checked='yes'";}else{$checked="";}
	echo "<tr valign='top'><td>Moyennes annuelles:<br />(<i>en mode 'Toutes_les_periodes' uniquement</i>)</td><td><input type='checkbox' name='affiche_moy_annuelle' value='oui'$checked /></td></tr>\n";

	echo "</table>\n";

	echo "</div>\n";
	//echo "<hr width='150' />\n";

	// Paramètres d'affichage:
	// - dimensions de l'image
	echo "<div id='div_params_2' style='display:block; border: 1px solid black;'>";
	echo "<b>Graphe</b><br />\n";
	echo "<table border='0'>\n";
	echo "<tr><td>Largeur:</td><td><input type='text' name='largeur_graphe' value='$largeur_graphe' size='3' /></td></tr>\n";
	//echo " - \n";
	echo "<tr><td>Hauteur:</td><td><input type='text' name='hauteur_graphe' value='$hauteur_graphe' size='3' /></td></tr>\n";

	// - taille des polices
	echo "<tr><td>Taille des polices:</td><td><select name='taille_police'>\n";
	for($i=1;$i<=6;$i++){
		if($taille_police==$i){$selected=" selected='yes'";}else{$selected="";}
		echo "<option value='$i'$selected>$i</option>\n";
	}
	echo "</select></td></tr>\n";

	// - epaisseur des traits
	echo "<tr><td>Epaisseur des courbes:</td><td><select name='epaisseur_traits'>\n";
	for($i=1;$i<=6;$i++){
		if($epaisseur_traits==$i){$selected=" selected='yes'";}else{$selected="";}
		echo "<option value='$i'$selected>$i</option>\n";
	}
	echo "</select></td></tr>\n";

	// - modèle de couleurs

	//if($temoin_imageps=='oui'){$checked=" checked='yes'";}else{$checked="";}
	if($temoin_image_escalier=='oui'){$checked=" checked='yes'";}else{$checked="";}
	//echo "Utiliser ImagePs: <input type='checkbox' name='temoin_imageps' value='oui'$checked /><br />\n";
	echo "<tr><td>Afficher les noms<br />longs de matières:</td><td><input type='checkbox' name='temoin_image_escalier' value='oui'$checked /></td></tr>\n";

	//echo "<tr><td>Tronquer le nom court<br />de matière à <a href='javascript:alert(\"A zéro caractères, on ne tronque pas le nom court de matière affiché en haut du graphe.\")'>X</a> caractères:</td><td><select name='tronquer_nom_court'>\n";
	echo "<tr><td>Tronquer le nom court<br />de matière à <a href='#' onclick='alert(\"A zéro caractères, on ne tronque pas le nom court de matière affiché en haut du graphe.\")'>X</a> caractères:</td><td><select name='tronquer_nom_court'>\n";
	for($i=0;$i<=10;$i++){
		if($tronquer_nom_court==$i){$selected=" selected='yes'";}else{$selected="";}
		echo "<option value='$i'$selected>$i</option>\n";
	}
	echo "</select></td></tr>\n";
	echo "</table>\n";



	echo "</div>\n";
	//echo "<hr width='150' />\n";


	// - Affichage de la photo
	echo "<div id='div_params_3' style='display:block; border: 1px solid black;'>";
	echo "<b>Paramètres des photos</b><br />\n";
	if(($affiche_photo=='')||($affiche_photo=='oui')){$checked=" checked='yes'";}else{$checked="";}
	echo "Afficher: <input type='radio' name='affiche_photo' value='oui'$checked />O / \n";
	if($affiche_photo=='non'){$checked=" checked='yes'";}else{$checked="";}
	echo "N<input type='radio' name='affiche_photo' value='non'$checked /><br />\n";

	// - Largeur imposée pour la photo
	echo "Largeur photo: <input type='text' name='largeur_imposee_photo' value='$largeur_imposee_photo' size='3' />\n";
	//echo "</p>\n";
	echo "</div>\n";




	//echo "<b>Paramètres des photos</b><br />";
	echo "<div id='div_params_4' style='display:block; border: 1px solid black;'>";
	if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
		echo "<b>Couleurs</b><br />\n";
		//echo "<hr width='150' />\n";
		//echo "<p>\n";
		echo "<a href='choix_couleurs.php' target='blank'>Modifier les couleurs</a>\n";
		//echo "</p>\n";
	}
	echo "</div>\n";

	if($_SESSION['statut']=='scolarite'){
		//echo "<input type='checkbox' name='save_params' value='y' /> <b>Enregistrer les paramètres</b>\n";
		echo "<input type='hidden' name='save_params' value='' />\n";
		echo "<input type='button' onClick=\"document.forms['form_choix_eleves'].save_params.value='y';document.forms['form_choix_eleves'].submit();\" name='Enregistrer' value='Enregistrer les paramètres' />\n";
	}
	echo "</div>\n";


	echo "<script type='text/javascript'>
	// On cache les div de paramètres au chargement de la page
	document.getElementById('div_params').style.display='none';
	document.getElementById('div_params_1').style.display='none';
	document.getElementById('div_params_2').style.display='none';
	document.getElementById('div_params_3').style.display='none';
	document.getElementById('div_params_4').style.display='none';
	</script>\n";
*/

	//======================================================================
	//======================================================================
	//======================================================================


	//echo "<input type='text' id='id_truc' name='truc' value='' />";
	echo "</form>\n";
	echo "</td>\n";

	echo "<td>\n";
	//====================================================================


	// Récupération des infos personnelles sur l'élève (nom, prénom, sexe, date de naissance et redoublant)
	// Et calcul de l'age (si le serveur est à l'heure;o).
	/*
	if((isset($eleve1) AND $_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve")
		OR (($_SESSION['statut'] == "responsable" OR $_SESSION['statut'] == "eleve") AND $periode != "")){
	*/
	if((isset($eleve1) AND $_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve")
		OR (($_SESSION['statut'] == "responsable" OR $_SESSION['statut'] == "eleve") AND $periode != "")
		OR (($_SESSION['statut'] == "responsable" OR $_SESSION['statut'] == "eleve") AND $choix_periode == "toutes_periodes")){
		// Informations sur l'élève $eleve1:
		$sql="SELECT * FROM eleves WHERE login='$eleve1'";
		$result_infos_eleve=mysql_query($sql);
		if(mysql_num_rows($result_infos_eleve)==1){
			$ligne=mysql_fetch_object($result_infos_eleve);
			$sexe1=$ligne->sexe;
			$nom1=$ligne->nom;
			$prenom1=$ligne->prenom;
			$naissance1=explode("-",$ligne->naissance);
			$ereno1=$ligne->ereno;
		}



		$anneedatenais1=$naissance1[0];
		$moisdatenais1=$naissance1[1];
		$jourdatenais1=$naissance1[2];

		$aujourdhui = getdate();
		$mois = $aujourdhui['mon'];
		//$mjour = $aujourdhui['mday'];
		$jour = $aujourdhui['mday'];
		$annee = $aujourdhui['year'];

		if($mois>$moisdatenais1){
			$age1=$annee-$anneedatenais1;
			$precision1=$mois-$moisdatenais1;
			$precision1="ans et $precision1 mois";
		}
		else{
			if($mois<$moisdatenais1){
				$age1=$annee-$anneedatenais1-1;
				$precision1=12-($moisdatenais1-$mois);
				$precision1="ans et $precision1 mois";
			}
			else{
				if($jour>=$jourdatenais1){
					$age1=$annee-$anneedatenais1;
					$precision1="ans ce mois-ci";
				}
				else{
					$age1=$annee-$anneedatenais1-1;
					$precision1="ans et 1 de plus ce mois-ci";
				}
			}
		}

		$sql="SELECT * FROM j_eleves_regime WHERE login='$eleve1'";
		$result_infos_eleve=mysql_query($sql);

		if(mysql_num_rows($result_infos_eleve)==1){
			$ligne=mysql_fetch_object($result_infos_eleve);
			$doublant1=$ligne->doublant;
			if("$doublant1"=="R"){
				if($sexe1=="M"){$doublant1="Redoublant";}else{$doublant1="Redoublante";}
			}
		}
	//}

		// Initialisation de la liste des matières.
		$liste_matieres="";

		// Séries:
		if($choix_periode=="periode"){
			$nb_series=2;
			$serie=array();
			for($i=1;$i<=$nb_series;$i++){$serie[$i]="";}

			//echo "Elève: $eleve1<br />periode=$periode<br />";

			//$num_periode
			$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' AND nom_periode='".$periode."'";
			$resultat=mysql_query($sql);
			if(mysql_num_rows($resultat)==0){
				//??? Toutes les périodes ?
				echo "<p>PB periode... $periode</p>";
			}
			else{
				$ligne=mysql_fetch_object($resultat);
				$num_periode=$ligne->num_periode;
			}


			// Des coefficients sont-ils saisis pour les différentes matières dans le cadre du calcul de la moyenne générale?
			//$call_classe_infos = mysql_query("SELECT DISTINCT  m.* FROM matieres m,j_classes_matieres_professeurs j WHERE (m.matiere = j.id_matiere AND j.id_classe='$id_classe')");


			$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
			if ($affiche_categories == "y") {
				$affiche_categories = true;
			} else {
				$affiche_categories = false;
			}


			// Récupération des noms courts/longs et priorités des matières de la classe (dans l'ordre de priorité)
			//$call_classe_infos = mysql_query("SELECT DISTINCT  m.* FROM matieres m,j_classes_matieres_professeurs j WHERE (m.matiere = j.id_matiere AND j.id_classe='$id_classe') ORDER BY j.priorite");
			if ($affiche_categories) {
				//$sql="SELECT DISTINCT  m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm,j_professeurs_matieres jpm,j_matieres_categories_classes jmcc WHERE (m.matiere=jgm.id_matiere AND jpm.id_matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe' AND jgc.categorie_id = jmcc.categorie_id) ORDER BY jmcc.priority,jpm.ordre_matieres,m.matiere";
				$sql="SELECT DISTINCT  m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm,j_matieres_categories_classes jmcc WHERE (m.matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe' AND jgc.categorie_id = jmcc.categorie_id) ORDER BY jmcc.priority,jgc.priorite,m.matiere";
			}
			else{
				//$sql="SELECT DISTINCT  m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm,j_professeurs_matieres jpm WHERE (m.matiere=jgm.id_matiere AND jpm.id_matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe') ORDER BY jpm.ordre_matieres,m.matiere";
				$sql="SELECT DISTINCT  m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm WHERE (m.matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe') ORDER BY jgc.priorite,m.matiere";
			}
			affiche_debug("$sql<br />");
			$call_classe_infos = mysql_query($sql);
			$nombre_lignes = mysql_num_rows($call_classe_infos);
			affiche_debug("\$nombre_lignes=$nombre_lignes<br />");

			// Compteur du nombre de notes de l'élève (autres que ABS,...)
			$cpt2=0;

			$matiere=array();
			$matiere_nom=array();

			# Image Map
			//$chaine_map="<map name='imagemap'>\n";
			// $largeurGrad -> 50
			// $largeurBandeDroite=80;
			// $largeur=$largeurTotale-$largeurGrad-$largeurBandeDroite;
			// $largeur=$largeur_graphe-$largeurGrad-$largeurBandeDroite;
			// $nbMat=count($matiere);
			// $largeurMat=round($largeur/$nbMat);
			/*
			$largeurGrad=50;
			$largeurBandeDroite=80;
			$largeur=$largeur_graphe-$largeurGrad-$largeurBandeDroite;
			$nbMat=;
			*/
			$tab_imagemap=array();

			$cpt=1;
			// Boucle sur l'ordre des matières:
			// On ne va retenir que les matières du premier élève.
			while($ligne=mysql_fetch_object($call_classe_infos)){
				// Nom court/long de la matière:
				$current_matiere=$ligne->matiere;
				$current_matiere_nom=$ligne->nom_complet;

				/*
				$matiere[$cpt]=$ligne->matiere;
				$matiere_nom[$cpt]=$ligne->nom_complet;
				$cpt++;
				*/

				//$total_serie[1]=0;
				//$nb_notes_serie=0;
				$coef_serie[1]=array();
				//$matiere=array();
				//$matiere_nom=array();

				// Est-ce une matière que l'élève a?
				//echo "SELECT * FROM j_eleves_matieres WHERE (login='$eleve1' AND matiere='$current_matiere' and periode='$periode')<br />";
				//$eleve_option_query=mysql_query("SELECT * FROM j_eleves_matieres WHERE (login='$eleve1' AND matiere='$current_matiere' and periode='$periode')");
				//$eleve_option_query=mysql_query("SELECT * FROM j_eleves_matieres WHERE (login='$eleve1' AND matiere='$current_matiere' and periode='$num_periode')");

				// Le fait de suivre une matière n'est plus renseigné par j_eleves_matieres qui contenait en fait les exclusions:
				// Les matières qu'un élève n'avait pas y était inscrites.
				// Avec la gestion par groupes, cela ne fonctionne plus ainsi.
				$sql="SELECT * FROM j_eleves_groupes jeg,j_groupes_matieres jgm WHERE (jeg.login='$eleve1' AND jgm.id_matiere='$current_matiere' AND jeg.id_groupe=jgm.id_groupe AND jeg.periode='".$num_periode."')";
				affiche_debug("$sql<br />");
				$eleve_option_query=mysql_query($sql);
				//echo "SELECT * FROM j_eleves_groupes jeg,j_groupes_matieres jgm WHERE (jeg.login='$eleve1' AND jgm.id_matiere='$current_matiere' AND jeg.id_groupe=jgm.id_groupe)<br />\n";
				//if(mysql_num_rows($eleve_option_query)==0){
				if(mysql_num_rows($eleve_option_query)!=0){
					//echo "X\n";
					//echo "$current_matiere_nom: \n";

					$matiere[$cpt]=$ligne->matiere;
					$matiere_nom[$cpt]=$ligne->nom_complet;
					$cpt++;

					//$note_eleve_query=mysql_query("SELECT * FROM matieres_notes WHERE (login='$eleve1' AND periode='$num_periode' AND matiere='$current_matiere')");
					$sql="SELECT mn.* FROM matieres_notes mn, j_groupes_matieres jgm WHERE (mn.login='$eleve1' AND mn.periode='$num_periode' AND jgm.id_matiere='$current_matiere' AND mn.statut ='' AND mn.id_groupe=jgm.id_groupe)";
					affiche_debug("$sql<br />");
					$note_eleve_query=mysql_query($sql);
					// QU'EST-CE QUE C'EST QUE CE STATUT ??? Réponse ci-dessous:
					// Le champ 'note' est numérique.
					// 'ABS' est donc assimilé à un zéro.
					// Le champ 'statut' permet de distinguer un zéro d'un 'ABS',...
					$eleve_matiere_statut = @mysql_result($note_eleve_query, 0, "statut");
					$note_eleve = @mysql_result($note_eleve_query, 0, "note");
					if ($eleve_matiere_statut != "") { $note_eleve = $eleve_matiere_statut;}
					if ($note_eleve == '') {$note_eleve = '-';}
					//echo "$note_eleve<br />\n";

					if($liste_matieres==""){
						$liste_matieres="$current_matiere";
					}
					else{
						$liste_matieres=$liste_matieres."|$current_matiere";
					}

					$cpttmp=$cpt-1;
					/*
					echo "<p>";
					echo $matiere[$cpttmp]." : $note_eleve<br />";
					echo "\$liste_matieres=$liste_matieres<br />";
					echo "</p>";
					*/

					if($serie[1]==""){
						$serie[1]="$note_eleve";
					}
					else{
						$serie[1]=$serie[1]."|$note_eleve";
					}

					// Récupération des vraies notes (non-ABS,...) et coeff de la matière pour la moyenne générale:
					if($affiche_mgen=='oui'){
						//echo "ereg_replace(\"[0-9]*.[0-9]\",\"\",$note_eleve)=".ereg_replace("[0-9]*.[0-9]","",$note_eleve)."<br />";
						//echo "strlen(ereg_replace(\"[0-9]*.[0-9]\",\"\",$note_eleve))=".strlen(ereg_replace("[0-9]*.[0-9]","",$note_eleve))."<br />";
						if(strlen(ereg_replace("[0-9]*.[0-9]","",$note_eleve))==0){
							//$total_serie[1]=$total_serie[1]+$note_eleve;
							//$nb_notes_serie++;

							$cpt2++;
							$note_serie[1][$cpt2]=$note_eleve;
							//$sql="SELECT DISTINCT coef FROM j_classes_matieres_professeurs WHERE id_classe='$id_classe' AND id_matiere='$current_matiere'";
							$sql="SELECT DISTINCT coef FROM j_groupes_classes jgc,j_groupes_matieres jgm WHERE jgc.id_classe='$id_classe' AND jgc.id_groupe=jgm.id_groupe AND jgm.id_matiere='$current_matiere'";
							affiche_debug("$sql<br />");
							$result=mysql_query($sql);
							// PB: Il peut y avoir plusieurs lignes TECHN -> deux profs au collège...
							//    ... et s'ils mettent des coeff différents???
							$ligntmp=mysql_fetch_object($result);
							$coef_serie[1][$cpt2]=$ligntmp->coef;
						}
					}


					$sql="SELECT ma.* FROM matieres_appreciations ma, j_groupes_matieres jgm WHERE (ma.login='$eleve1' AND ma.periode='$num_periode' AND jgm.id_matiere='$current_matiere' AND ma.id_groupe=jgm.id_groupe)";
					affiche_debug("$sql<br />");
					$app_eleve_query=mysql_query($sql);
					echo "<div id='div_matiere_$cpt' style='position: absolute; z-index: 1000; top: 300px; left: 200px; width: 300px; display:none;'>\n";
					if(mysql_num_rows($app_eleve_query)>0){
						$ligtmp=mysql_fetch_object($app_eleve_query);

						echo "<div style='text-align: center; width: 300px; border: 1px solid black; background-color:white;'>\n";
						//echo "<b>Appréciation:</b> $current_matiere ".htmlentities($ligtmp->appreciation);
						echo "<b>".htmlentities($current_matiere_nom).":</b> (<i>$periode</i>)<br />".htmlentities($ligtmp->appreciation);
						echo "</div>\n";

						//$chaine_map.="";
						//$tab_imagemap[]=$cpt;
					}
					echo "</div>\n";

					// On stocke dans un tableau, les numéros $cpt correspondant aux matières que l'élève a.
					$tab_imagemap[]=$cpt;
				}
				else{
					// L'élève n'a pas cette matière.
					//echo "$current_matiere_nom: ---<br />\n";
					echo "<!-- $eleve1 n'a pas $current_matiere_nom -->\n";
				}
				//echo "<br />\n";
			}

			//echo "\$cpt2=$cpt2<br />";



			$sql="SELECT * FROM avis_conseil_classe WHERE login='$eleve1' AND periode='$num_periode' ORDER BY periode";
			$res_avis=mysql_query($sql);
			echo "<div id='div_avis_1' style='position: absolute; z-index: 1000; top: 300px; left: 200px; width: 300px; display:none;'>\n";
			if(mysql_num_rows($res_avis)>0){
				echo "<div style='text-align: center; width: 300px; border: 1px solid black; background-color:white;'>\n";
				echo "<b>Avis du Conseil de classe:</b><br />\n";
				$lig_avis=mysql_fetch_object($res_avis);
				echo htmlentities($lig_avis->avis)."\n";
				echo "</div>\n";
			}
			echo "</div>\n";



			# Image Map
			//$chaine_map="<map name='imagemap'>\n";
			// $largeurGrad -> 50
			// $largeurBandeDroite=80;
			// $largeur=$largeurTotale-$largeurGrad-$largeurBandeDroite;
			// $largeur=$largeur_graphe-$largeurGrad-$largeurBandeDroite;
			// $nbMat=count($matiere);
			// $largeurMat=round($largeur/$nbMat);

			if(count($tab_imagemap)>0){
				$largeurGrad=50;
				$largeurBandeDroite=80;
				$largeur_utile=$largeur_graphe-$largeurGrad-$largeurBandeDroite;
				$nbMat=count($tab_imagemap);
				$largeurMat=round($largeur_utile/$nbMat);

				echo "<map name='imagemap'>\n";
				for($i=0;$i<count($tab_imagemap);$i++){
					$x0=$largeurGrad+$i*$largeurMat;
					$x1=$x0+$largeurMat;
					//echo "<area href=\"javascript:return false;\" onMouseover=\"document.getElementById('div_matiere_".$tab_imagemap[$i]."').style.display=''\" onMouseout=\"document.getElementById('div_matiere_".$tab_imagemap[$i]."').style.display='none'\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";
					echo "<area href=\"#\" onClick='return false;' onMouseover=\"div_info('div_matiere_',$tab_imagemap[$i],'affiche');\" onMouseout=\"div_info('div_matiere_',$tab_imagemap[$i],'cache');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";
				}

				$x0=$largeurGrad+$i*$largeurMat;
				$x1=$largeur_graphe;
				echo "<area href=\"#\" onClick='return false;' onMouseover=\"div_info('div_avis_','1','affiche');\" onMouseout=\"div_info('div_avis_','1','cache');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";

				echo "</map>\n";
			}


			// Calcul de la moyenne générale de l'élève $eleve1:
			if($affiche_mgen=='oui'){
				if($cpt2>0){
					$totaltmp=0;
					if(isset($coef_serie)){
						if(isset($coef_serie[1])){
							for($i=1;$i<=count($coef_serie[1]);$i++){
								if(isset($coef_serie[1][$i])){
									$totaltmp=$totaltmp+$coef_serie[1][$i];
								}
							}
						}
					}
					// Si aucun coeff n'est saisi, on calcule une moyenne non pondérée.
					if($totaltmp==0){
						$totaltmp=0;
						for($i=1;$i<=count($note_serie[1]);$i++){$totaltmp=$totaltmp+$note_serie[1][$i];}
						$mgen[1]=round($totaltmp/$cpt2,1);
					}
					else{
						$somme_des_coeff=$totaltmp;
						$totaltmp=0;
						for($i=1;$i<=count($note_serie[1]);$i++){$totaltmp=$totaltmp+$note_serie[1][$i]*$coef_serie[1][$i];}
						$mgen[1]=round($totaltmp/$somme_des_coeff,1);
					}
					//$mgen[1]=round($total_serie[1]/$nb_notes_serie,1);
				}
				else{
					$mgen[1]="-";
				}
			}

	/*
			echo "<p>\$liste_matieres=$liste_matieres</p>";
			echo "<p>\$serie[1]=$serie[1]</p>";
	*/

			// Compteur du nombre de notes différentes de ABS,...
			$cpt2=0;

			$serie2="";
			$serie[2]="";
			// Listes supplémentaires: (CAS periode (pas Toutes_periodes))
	/*
			//for($i=2;$i<=$nb_series;$i++){
				switch($eleve2){
					case 'moymin':
						for($j=1;$j<=count($matiere);$j++){
							$sql="SELECT min(n.note) as note_min FROM matieres_notes n, j_eleves_classes c WHERE (n.periode='$num_periode' AND n.matiere='$matiere[$j]' AND c.id_classe='$id_classe' AND c.login = n.login AND n.statut =''  AND c.periode='$num_periode')";
							$resultat=mysql_query($sql);
							$note_min=mysql_result($resultat, 0, "note_min");
							if($serie[2]==""){
								$serie[2]="$note_min";
							}
							else{
								$serie[2]=$serie[2]."|$note_min";
							}
						}
						break;
					case 'moyclasse':
						for($j=1;$j<=count($matiere);$j++){
							$sql="SELECT round(avg(n.note),1) as moyenne FROM matieres_notes n, j_eleves_classes c WHERE (n.periode='$num_periode' AND n.matiere='$matiere[$j]' AND c.id_classe='$id_classe' AND c.login = n.login AND n.statut =''  AND c.periode='$num_periode')";
							//affiche_debug("$sql<br />");
							$resultat=mysql_query($sql);
							$note_moy=mysql_result($resultat, 0, "moyenne");
							// Au cas où il n'y ait pas de note dans une matière:
							if($note_moy==""){$note_moy="-";}
							//echo "$note_moy<br />";
							if($serie[2]==""){
								$serie[2]="$note_moy";
							}
							else{
								$serie[2]=$serie[2]."|$note_moy";
							}
						}
						break;
					case 'moymax':
						for($j=1;$j<=count($matiere);$j++){
							$sql="SELECT max(n.note) as note_max FROM matieres_notes n, j_eleves_classes c WHERE (n.periode='$num_periode' AND n.matiere='$matiere[$j]' AND c.id_classe='$id_classe' AND c.login = n.login AND n.statut =''  AND c.periode='$num_periode')";
							$resultat=mysql_query($sql);
							$note_max=mysql_result($resultat, 0, "note_max");
							if($serie[2]==""){
								$serie[2]="$note_max";
							}
							else{
								$serie[2]=$serie[2]."|$note_max";
							}
						}
						break;
					default:
						// Elève 2:
						for($j=1;$j<=count($matiere);$j++){
							$note_eleve_query=mysql_query("SELECT * FROM matieres_notes WHERE (login='$eleve2' AND periode='$num_periode' AND matiere='$matiere[$j]')");
							// QU'EST-CE QUE C'EST QUE CE STATUT ???
							//$eleve_matiere_statut = @mysql_result($note_eleve_query, 0, "statut");
							$note_eleve = @mysql_result($note_eleve_query, 0, "note");
							//if ($eleve_matiere_statut != "") { $note_eleve = $eleve_matiere_statut;}
							if ($note_eleve == '') {$note_eleve = '-';}
							//echo "$note_eleve<br />\n";

							if($serie[2]==""){
								$serie[2]="$note_eleve";
							}
							else{
								$serie[2]=$serie[2]."|$note_eleve";
							}
						}

						// Moyenne générale
						// A REVOIR... Il faut faire la moyenne sur les notes de cet élève... même s'il n'a pas toutes les mêmes matières...
						break;
				}
			//}
			//echo "<p>\$serie[2]=$serie[2]</p>";
	*/

			echo "<!--count(\$matiere)=".count($matiere)."-->\n";

			// Calcul des moyennes minimales pour les matières de $eleve1:
			for($j=1;$j<=count($matiere);$j++){
				//$sql="SELECT min(n.note) as note_min FROM matieres_notes n, j_eleves_classes c WHERE (n.periode='$num_periode' AND n.matiere='$matiere[$j]' AND c.id_classe='$id_classe' AND c.login = n.login AND n.statut =''  AND c.periode='$num_periode')";

				// PROBLEME: Il faut se base sur id_groupe dans matieres_notes... il n'y a plus de champ 'matiere'.
				//$sql="SELECT min(n.note) as note_min FROM matieres_notes n, j_eleves_classes c WHERE (n.periode='$num_periode' AND n.matiere='$matiere[$j]' AND c.id_classe='$id_classe' AND c.login = n.login AND n.statut =''  AND c.periode='$num_periode')";
				$sql="SELECT min(mn.note) as note_min FROM matieres_notes mn, j_groupes_matieres jgm, j_groupes_classes jgc WHERE (mn.periode='$num_periode' AND jgm.id_matiere='$matiere[$j]' AND jgc.id_classe='$id_classe' AND mn.statut ='' AND mn.id_groupe=jgm.id_groupe AND jgc.id_groupe=jgm.id_groupe)";
				affiche_debug("$sql<br />");
				$resultat=mysql_query($sql);
				$note_min=mysql_result($resultat, 0, "note_min");
				if($note_min==""){$note_min="-";}
				if($seriemin==""){
					$seriemin="$note_min";
				}
				else{
					$seriemin=$seriemin."|$note_min";
				}
			}

			echo "<!-- \$seriemin=$seriemin -->\n";

			// Calcul des moyennes de la classe pour les matières de $eleve1:
			for($j=1;$j<=count($matiere);$j++){
				// PROBLEME: Il faut se base sur id_groupe dans matieres_notes... il n'y a plus de champ 'matiere'.
				//$sql="SELECT round(avg(n.note),1) as moyenne FROM matieres_notes n, j_eleves_classes c WHERE (n.periode='$num_periode' AND n.matiere='$matiere[$j]' AND c.id_classe='$id_classe' AND c.login = n.login AND n.statut =''  AND c.periode='$num_periode')";
				$sql="SELECT round(avg(mn.note),1) as moyenne FROM matieres_notes mn, j_groupes_matieres jgm, j_groupes_classes jgc WHERE (mn.periode='$num_periode' AND jgm.id_matiere='$matiere[$j]' AND jgc.id_classe='$id_classe' AND mn.statut ='' AND mn.id_groupe=jgm.id_groupe AND jgc.id_groupe=jgm.id_groupe)";
				affiche_debug("$sql<br />");
				$resultat=mysql_query($sql);
				$note_moy=mysql_result($resultat, 0, "moyenne");
				// Au cas où il n'y ait pas de note dans une matière:
				if($note_moy==""){$note_moy="-";}
				//echo "$note_moy<br />";
				if($seriemoy==""){
					$seriemoy="$note_moy";
				}
				else{
					$seriemoy=$seriemoy."|$note_moy";
				}
			}

			echo "<!-- \$seriemoy=$seriemoy -->\n";


			// Calcul des moyennes maximales pour les matières de $eleve1:
			for($j=1;$j<=count($matiere);$j++){
				// PROBLEME: Il faut se base sur id_groupe dans matieres_notes... il n'y a plus de champ 'matiere'.
				//$sql="SELECT max(n.note) as note_max FROM matieres_notes n, j_eleves_classes c WHERE (n.periode='$num_periode' AND n.matiere='$matiere[$j]' AND c.id_classe='$id_classe' AND c.login = n.login AND n.statut =''  AND c.periode='$num_periode')";
				$sql="SELECT max(mn.note) as note_max FROM matieres_notes mn, j_groupes_matieres jgm, j_groupes_classes jgc WHERE (mn.periode='$num_periode' AND jgm.id_matiere='$matiere[$j]' AND jgc.id_classe='$id_classe' AND mn.statut ='' AND mn.id_groupe=jgm.id_groupe AND jgc.id_groupe=jgm.id_groupe)";
				affiche_debug("$sql<br />");
				$resultat=mysql_query($sql);
				$note_max=mysql_result($resultat, 0, "note_max");
				if($note_max==""){$note_max="-";}
				if($seriemax==""){
					$seriemax="$note_max";
				}
				else{
					$seriemax=$seriemax."|$note_max";
				}
			}

			echo "<!-- \$seriemax=$seriemax -->\n";


			// Affectation de $serie[2] en fonction du choix Moyennes min/classe/max ou moyennes d'un deuxième élève:
			switch($eleve2){
				case 'moymin':
					$serie[2]=$seriemin;
					break;
				case 'moyclasse':
					$serie[2]=$seriemoy;
					break;
				case 'moymax':
					$serie[2]=$seriemax;
					break;
				default:
					// Elève 2:
					for($j=1;$j<=count($matiere);$j++){
						// PROBLEME: Il faut se base sur id_groupe dans matieres_notes... il n'y a plus de champ 'matiere'.
						//$note_eleve_query=mysql_query("SELECT * FROM matieres_notes WHERE (login='$eleve2' AND periode='$num_periode' AND matiere='$matiere[$j]')");
						$sql="SELECT mn.* FROM matieres_notes mn, j_groupes_matieres jgm WHERE (mn.login='$eleve2' AND mn.periode='$num_periode' AND jgm.id_matiere='$matiere[$j]' AND mn.statut ='' AND mn.id_groupe=jgm.id_groupe)";
						affiche_debug("$sql<br />");
						$note_eleve_query=mysql_query($sql);
						// QU'EST-CE QUE C'EST QUE CE STATUT ???
						//$eleve_matiere_statut = @mysql_result($note_eleve_query, 0, "statut");
						$note_eleve = @mysql_result($note_eleve_query, 0, "note");
						//if ($eleve_matiere_statut != "") { $note_eleve = $eleve_matiere_statut;}
						if ($note_eleve == '') {$note_eleve = '-';}

						if($serie[2]==""){
							$serie[2]="$note_eleve";
						}
						else{
							$serie[2]=$serie[2]."|$note_eleve";
						}
					}

					// Calcul de la moyenne générale du deuxième élève:
					// A FAIRE...

					//=============================================================
					//=============================================================
					//=============================================================
					// Récupération des noms courts/longs et priorités des matières de la classe (dans l'ordre de priorité)
					//$call_classe_infos = mysql_query("SELECT DISTINCT  m.* FROM matieres m,j_classes_matieres_professeurs j WHERE (m.matiere = j.id_matiere AND j.id_classe='$id_classe') ORDER BY j.priorite");
					//$sql="SELECT DISTINCT  m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm,j_professeurs_matieres jpm WHERE (m.matiere=jgm.id_matiere AND jpm.id_matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe') ORDER BY jpm.ordre_matieres,m.matiere";
					$sql="SELECT DISTINCT  m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm WHERE (m.matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe') ORDER BY jgc.priorite,m.matiere";
					affiche_debug("$sql<br />");
					$call_classe_infos = mysql_query($sql);

					// Compteur du nombre de notes de l'élève (autres que ABS,...)
					$cpt2=0;

					$cpt=1;
					// Boucle sur l'ordre des matières:
					// On ne va retenir que les matières de l'élève.
					while($ligne=mysql_fetch_object($call_classe_infos)){
						// Nom court/long de la matière:
						$current_matiere=$ligne->matiere;
						$current_matiere_nom=$ligne->nom_complet;

						$coef_serie[2]=array();

						// Est-ce une matière que l'élève a?
						//echo "SELECT * FROM j_eleves_matieres WHERE (login='$eleve1' AND matiere='$current_matiere' and periode='$periode')<br />";
						//$eleve_option_query=mysql_query("SELECT * FROM j_eleves_matieres WHERE (login='$eleve1' AND matiere='$current_matiere' and periode='$periode')");
						//$eleve_option_query=mysql_query("SELECT * FROM j_eleves_matieres WHERE (login='$eleve2' AND matiere='$current_matiere' and periode='$num_periode')");
						// Le fait de suivre une matière n'est plus renseigné par j_eleves_matieres qui contenait en fait les exclusions:
						// Les matières qu'un élève n'avait pas y était inscrites.
						// Avec la gestion par groupes, cela ne fonctionne plus ainsi.
						$sql="SELECT * FROM j_eleves_groupes jeg,j_groupes_matieres jgm WHERE (jeg.login='$eleve2' AND jgm.id_matiere='$current_matiere' AND jeg.id_groupe=jgm.id_groupe AND jeg.periode='$num_periode')";
						affiche_debug("$sql<br />");
						$eleve_option_query=mysql_query($sql);
						if(mysql_num_rows($eleve_option_query)==0){
							//$note_eleve_query=mysql_query("SELECT * FROM matieres_notes WHERE (login='$eleve2' AND periode='$num_periode' AND matiere='$current_matiere')");
							$sql="SELECT mn.* FROM matieres_notes mn, j_groupes_matieres jgm WHERE (mn.login='$eleve2' AND mn.periode='$num_periode' AND jgm.id_matiere='$current_matiere' AND mn.statut ='' AND mn.id_groupe=jgm.id_groupe)";
							affiche_debug("$sql<br />");
							$note_eleve_query=mysql_query($sql);

							$eleve_matiere_statut = @mysql_result($note_eleve_query, 0, "statut");
							$note_eleve = @mysql_result($note_eleve_query, 0, "note");
							if ($eleve_matiere_statut != "") { $note_eleve = $eleve_matiere_statut;}
							if ($note_eleve == '') {$note_eleve = '-';}

							// Récupération des vraies notes (non-ABS,...) et coeff de la matière pour la moyenne générale:
							if($affiche_mgen=='oui'){
								if(strlen(ereg_replace("[0-9]*.[0-9]","",$note_eleve))==0){
									$cpt2++;
									$note_serie[2][$cpt2]=$note_eleve;
									//$sql="SELECT DISTINCT coef FROM j_classes_matieres_professeurs WHERE id_classe='$id_classe' AND id_matiere='$current_matiere'";
									$sql="SELECT DISTINCT coef FROM j_groupes_classes jgc,j_groupes_matieres jgm WHERE jgc.id_classe='$id_classe' AND jgc.id_groupe=jgm.id_groupe AND jgm.id_matiere='$current_matiere'";
									affiche_debug("$sql<br />");
									$result=mysql_query($sql);
									// PB: Il peut y avoir plusieurs lignes TECHN -> deux profs au collège...
									//    ... et s'ils mettent des coeff différents???
									$ligntmp=mysql_fetch_object($result);
									$coef_serie[2][$cpt2]=$ligntmp->coef;
								}
							}

						}
						else{
							// L'élève n'a pas cette matière.
							//echo "$current_matiere_nom: ---<br />\n";
							echo "<!-- $eleve2 n'a pas $current_matiere_nom -->\n";
						}
						//echo "<br />\n";
					}

					//echo "\$cpt2=$cpt2<br />";

					// Calcul de la moyenne générale de l'élève $eleve1:
					if($affiche_mgen=='oui'){
						if($cpt2>0){
							$totaltmp=0;
							for($i=1;$i<=count($coef_serie[2]);$i++){$totaltmp=$totaltmp+$coef_serie[2][$i];}
							// Si aucun coeff n'est saisi, on calcule une moyenne non pondérée.
							if($totaltmp==0){
								$totaltmp=0;
								for($i=1;$i<=count($note_serie[2]);$i++){$totaltmp=$totaltmp+$note_serie[2][$i];}
								$mgen[2]=round($totaltmp/$cpt2,1);
							}
							else{
								$somme_des_coeff=$totaltmp;
								$totaltmp=0;
								for($i=1;$i<=count($note_serie[2]);$i++){$totaltmp=$totaltmp+$note_serie[2][$i]*$coef_serie[2][$i];}
								$mgen[2]=round($totaltmp/$somme_des_coeff,1);
							}
							//$mgen[2]=round($total_serie[2]/$nb_notes_serie,1);
						}
						else{
							$mgen[2]="-";
						}
					}

					//=============================================================
					//=============================================================
					//=============================================================
					break;
			}

			// **********************************************
			// **********************************************
			// Il faudrait afficher aussi la moyenne générale,
			// l'age, le fait d'être redoublant...
			// **********************************************
			// **********************************************

			/*
			$eleves[1]="$eleve1";
			$eleves[2]="$eleve2";
			*/

			/*
			// Non: Il vaut mieux traiter la moyenne générale à part.
			if($affiche_mgen=="oui"){
				$liste_matieres="$liste_matieres|M.GEN";
				$serie[1]="$serie[1]|".trim($mgen[1]);
				//$serie2="$serie[2]|$mgen[2]";
				$serie[2]="$serie[2]|-";

			*/

			//echo "";



			/*
			for($j=1;$j<=count($matiere);$j++){
				$sql="SELECT ";
				echo "<div id='div_matiere_$j' style='visibility:hidden'></div>";
			}
			*/



			echo "<a name='graph'></a>\n";
			//echo "<img src='draw_artichow_fig7.php?temp1=$temp1&temp2=$temp2&etiquette=$etiq&titre=$graph_title&v_legend1=$v_legend1&v_legend2=$v_legend2&compteur=$compteur&nb_data=3'>";
			//echo "<img src='draw_artichow_fig7.php?temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=$eleve2&compteur=$compteur&nb_data=3'>";
			//echo "<p>img src='draw_artichow_fig7.php?&temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=$eleve2&compteur=$compteur&nb_series=$nb_series&id_classe=$id_classe'</p>";
			//echo "<img src='draw_artichow_fig7.php?&temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=$eleve2&compteur=$compteur&nb_series=$nb_series&id_classe=$id_classe'>";
			//echo "<img src='draw_artichow_fig7.php?&temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=$eleve2&compteur=$compteur&nb_series=$nb_series&id_classe=$id_classe&mgen1=$mgen[1]&mgen2=$mgen[2]&largeur_graphe=$largeur_graphe&hauteur_graphe=$hauteur_graphe&taille_police=$taille_police'>";

			//echo "<a href=\"javascript:document.getElementById('div_matiere_2').style.display=''\" onMouseover=\"document.getElementById('div_matiere_2').style.display=''\" onMouseout=\"document.getElementById('div_matiere_2').style.display='none'\">";

			//echo "\$type_graphe=".$type_graphe."<br />\n";

			if($type_graphe=='courbe'){
				if(count($matiere)>0){
					echo "<img src='draw_graphe.php?";
					//echo "&amp;temp1=$serie[1]";
					echo "temp1=$serie[1]";
					echo "&amp;temp2=$serie[2]";
					echo "&amp;etiquette=$liste_matieres";
					echo "&amp;titre=$graph_title";
					echo "&amp;v_legend1=$eleve1";
					echo "&amp;v_legend2=$eleve2";
					echo "&amp;compteur=$compteur";
					echo "&amp;nb_series=$nb_series";
					echo "&amp;id_classe=$id_classe";
					echo "&amp;mgen1=$mgen[1]";
					echo "&amp;mgen2=$mgen[2]";
					//echo "&amp;periode=$periode";
					echo "&amp;periode=".rawurlencode($periode);
					echo "&amp;largeur_graphe=$largeur_graphe";
					echo "&amp;hauteur_graphe=$hauteur_graphe";
					echo "&amp;taille_police=$taille_police";
					echo "&amp;epaisseur_traits=$epaisseur_traits";
					if($affiche_minmax=="oui"){
						echo "&amp;seriemin=$seriemin";
						echo "&amp;seriemax=$seriemax";
					}
					echo "&amp;tronquer_nom_court=$tronquer_nom_court";
					//echo "'>";
					//echo "&amp;temoin_imageps=$temoin_imageps";
					echo "&amp;temoin_image_escalier=$temoin_image_escalier";
					echo "' style='border: 1px solid black;' height='$hauteur_graphe' width='$largeur_graphe' alt='Graphe' ";
					echo "usemap='#imagemap' ";
					echo "/>\n";
					//echo "</a>\n";
				}
			}
			else{
				if(count($matiere)>0){
					echo "<img src='draw_graphe_star.php?";
					//echo "&amp;temp1=$serie[1]";
					echo "temp1=$serie[1]";
					echo "&amp;temp2=$serie[2]";
					echo "&amp;etiquette=$liste_matieres";
					echo "&amp;titre=$graph_title";
					echo "&amp;v_legend1=$eleve1";
					echo "&amp;v_legend2=$eleve2";
					echo "&amp;compteur=$compteur";
					echo "&amp;nb_series=$nb_series";
					echo "&amp;id_classe=$id_classe";
					echo "&amp;mgen1=$mgen[1]";
					echo "&amp;mgen2=$mgen[2]";
					//echo "&amp;periode=$periode";
					echo "&amp;periode=".rawurlencode($periode);
					echo "&amp;largeur_graphe=$largeur_graphe";
					echo "&amp;hauteur_graphe=$hauteur_graphe";
					echo "&amp;taille_police=$taille_police";
					echo "&amp;epaisseur_traits=$epaisseur_traits";
					if($affiche_minmax=="oui"){
						echo "&amp;seriemin=$seriemin";
						echo "&amp;seriemax=$seriemax";
					}
					echo "&amp;tronquer_nom_court=$tronquer_nom_court";
					//echo "'>";
					//echo "&amp;temoin_imageps=$temoin_imageps";
					echo "&amp;temoin_image_escalier=$temoin_image_escalier";
					echo "' style='border: 1px solid black;' height='$hauteur_graphe' width='$largeur_graphe' alt='Graphe' ";
					//echo "usemap='#imagemap' ";
					echo "/>\n";
					//echo "</a>\n";
				}
			}
			//===================================

			//echo "<img src='draw_artichow_fig7.php?eleves=$eleves&temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=$eleve2&compteur=$compteur&nb_series=$nb_series'>";


			/*
			if(isset($_SESSION['graphe_largeurMat'])){echo "\$_SESSION['graphe_largeurMat']=".$_SESSION['graphe_largeurMat']."<br />";}
			if(isset($_SESSION['graphe_x0'])){echo "\$_SESSION['graphe_x0']=".$_SESSION['graphe_x0']."<br />";}
			*/

			// $largeurGrad -> 50
			// $largeurBandeDroite=80;
			// $largeur=$largeurTotale-$largeurGrad-$largeurBandeDroite;
			// $nbMat=count($matiere);
			// $largeurMat=round($largeur/$nbMat);


		}
		else{
			// On va afficher toutes les périodes





			$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
			if ($affiche_categories == "y") {
				$affiche_categories = true;
			} else {
				$affiche_categories = false;
			}


			// Récupération des noms courts/longs et priorités de toutes les matières de la classe (dans l'ordre de priorité)
			//$call_classe_infos = mysql_query("SELECT DISTINCT  m.* FROM matieres m,j_classes_matieres_professeurs j WHERE (m.matiere = j.id_matiere AND j.id_classe='$id_classe') ORDER BY j.priorite");
			//$sql="SELECT DISTINCT  m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm,j_professeurs_matieres jpm WHERE (m.matiere=jgm.id_matiere AND jpm.id_matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe') ORDER BY jpm.ordre_matieres,m.matiere";

			if ($affiche_categories) {
				//$sql="SELECT DISTINCT  m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm,j_professeurs_matieres jpm,j_matieres_categories_classes jmcc WHERE (m.matiere=jgm.id_matiere AND jpm.id_matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe' AND jgc.categorie_id = jmcc.categorie_id) ORDER BY jmcc.priority,jpm.ordre_matieres,m.matiere";
				$sql="SELECT DISTINCT  m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm,j_matieres_categories_classes jmcc WHERE (m.matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe' AND jgc.categorie_id = jmcc.categorie_id) ORDER BY jmcc.priority,jgc.priorite,m.matiere";
			}
			else{
				//$sql="SELECT DISTINCT  m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm,j_professeurs_matieres jpm WHERE (m.matiere=jgm.id_matiere AND jpm.id_matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe') ORDER BY jpm.ordre_matieres,m.matiere";
				$sql="SELECT DISTINCT  m.* FROM matieres m,j_groupes_classes jgc,j_groupes_matieres jgm WHERE (m.matiere=jgm.id_matiere AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='$id_classe') ORDER BY jgc.priorite,m.matiere";
			}

			affiche_debug("$sql<br />");
			$call_classe_infos = mysql_query($sql);
			$nombre_lignes = mysql_num_rows($call_classe_infos);
			affiche_debug("\$nombre_lignes=$nombre_lignes<br />");

			$liste_matieres="";
			$matiere=array();
			$matiere_nom=array();

			$cpt=1;
			// Boucle sur l'ordre des matières:
			// On ne va retenir que les matières du premier élève.
			while($ligne=mysql_fetch_object($call_classe_infos)){
				// Nom court/long de la matière:
				$current_matiere=$ligne->matiere;
				$current_matiere_nom=$ligne->nom_complet;

				affiche_debug("\$current_matiere=$current_matiere<br />");

				// Est-ce une matière que l'élève a? sur une des périodes au moins?
				//$eleve_option_query=mysql_query("SELECT * FROM j_eleves_matieres WHERE (login='$eleve1' AND matiere='$current_matiere' and periode='$num_periode')");
				//$eleve_option_query=mysql_query("SELECT * FROM j_eleves_matieres WHERE (login='$eleve1' AND matiere='$current_matiere')");
				//$eleve_option_query=mysql_query("SELECT * FROM j_eleves_matieres WHERE (login='$eleve1' AND matiere='$current_matiere')");
				$sql="SELECT * FROM j_eleves_groupes jeg,j_groupes_matieres jgm WHERE (jeg.login='$eleve1' AND jgm.id_matiere='$current_matiere' AND jeg.id_groupe=jgm.id_groupe)";
				affiche_debug("$sql<br />");
				$eleve_option_query=mysql_query($sql);
				//if(mysql_num_rows($eleve_option_query)==0){
				if(mysql_num_rows($eleve_option_query)!=0){
					$matiere[$cpt]=$ligne->matiere;
					$matiere_nom[$cpt]=$ligne->nom_complet;

					if($liste_matieres==""){
						$liste_matieres="$matiere[$cpt]";
					}
					else{
						$liste_matieres=$liste_matieres."|$matiere[$cpt]";
					}

					$cpt++;
				}
			}

			// Toutes les périodes...
			$sql="SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode";
			$result_periode=mysql_query($sql);
			$nb_periode=mysql_num_rows($result_periode);

			$nb_series=$nb_periode;
			for($i=1;$i<=$nb_series;$i++){$serie[$i]="";}


			unset($tab_imagemap);
			$tab_imagemap=array();

			//$temoin_au_moins_une_vraie_moyenne="";
			// $liste_temp va contenir les séries à envoyer au graphe et éventuellement les moyennes générales sur les différentes périodes.
			$liste_temp="";
			$cpt=1;
			while($lign_periode=mysql_fetch_object($result_periode)){

				$num_periode[$cpt]=$lign_periode->num_periode;
				//$nom_periode[$cpt]=$lign_periode->nom_periode;
				$tab_imagemap[$cpt]=array();

				// Compteur du nombre de matières avec une note autre que ABS,...
				$cpt2=0;
				for($i=1;$i<=count($matiere);$i++){
					//$note_eleve_query=mysql_query("SELECT * FROM matieres_notes WHERE (login='$eleve1' AND periode='$num_periode[$cpt]' AND matiere='$matiere[$i]')");
					$sql="SELECT mn.* FROM matieres_notes mn, j_groupes_matieres jgm WHERE (mn.login='$eleve1' AND mn.periode='$num_periode[$cpt]' AND jgm.id_matiere='$matiere[$i]' AND mn.statut ='' AND mn.id_groupe=jgm.id_groupe)";
					affiche_debug("$sql<br />");
					$note_eleve_query=mysql_query($sql);
					// QU'EST-CE QUE C'EST QUE CE STATUT ???
					//$eleve_matiere_statut = @mysql_result($note_eleve_query, 0, "statut");
					$note_eleve = @mysql_result($note_eleve_query, 0, "note");
					//if ($eleve_matiere_statut != "") { $note_eleve = $eleve_matiere_statut;}
					if ($note_eleve == '') {$note_eleve = '-';}
					//echo "$note_eleve<br />\n";


					if($serie[$cpt]==""){
						$serie[$cpt]="$note_eleve";
					}
					else{
						$serie[$cpt]=$serie[$cpt]."|$note_eleve";
					}

					if($affiche_mgen=='oui'){
						//echo "ereg_replace(\"[0-9]*.[0-9]\",\"\",$note_eleve)=".ereg_replace("[0-9]*.[0-9]","",$note_eleve)."<br />";
						//echo "strlen(ereg_replace(\"[0-9]*.[0-9]\",\"\",$note_eleve))=".strlen(ereg_replace("[0-9]*.[0-9]","",$note_eleve))."<br />";
						if(strlen(ereg_replace("[0-9]*.[0-9]","",$note_eleve))==0){
							//$total_serie[1]=$total_serie[1]+$note_eleve;
							//$nb_notes_serie++;

							$cpt2++;
							$note_serie[$cpt][$cpt2]=$note_eleve;
							//$sql="SELECT DISTINCT coef FROM j_classes_matieres_professeurs WHERE id_classe='$id_classe' AND id_matiere='".$matiere[$i]."'";
							$sql="SELECT DISTINCT coef FROM j_groupes_classes jgc,j_groupes_matieres jgm WHERE jgc.id_classe='$id_classe' AND jgc.id_groupe=jgm.id_groupe AND jgm.id_matiere='".$matiere[$i]."'";
							affiche_debug("$sql<br />");
							$result=mysql_query($sql);
							// PB: Il peut y avoir plusieurs lignes TECHN -> deux profs au collège...
							//    ... et s'ils mettent des coeff différents???
							$ligntmp=mysql_fetch_object($result);
							$coef_serie[$cpt][$cpt2]=$ligntmp->coef;

							//$temoin_au_moins_une_vraie_moyenne="oui";
						}
					}





					$sql="SELECT ma.* FROM matieres_appreciations ma, j_groupes_matieres jgm WHERE (ma.login='$eleve1' AND ma.periode='$num_periode[$cpt]' AND jgm.id_matiere='$matiere[$i]' AND ma.id_groupe=jgm.id_groupe)";
					affiche_debug("$sql<br />");
					$app_eleve_query=mysql_query($sql);
					//echo "<div id='div_matiere_$cpt' style='position: absolute; z-index: 1000; top: 300px; left: 200px; width: 300px; display:none;'>\n";
					if(mysql_num_rows($app_eleve_query)>0){
						$ligtmp=mysql_fetch_object($app_eleve_query);

						/*
						echo "<div style='text-align: center; width: 300px; border: 1px solid black; background-color:white;'>\n";
						//echo "<b>Appréciation:</b> $current_matiere ".htmlentities($ligtmp->appreciation);
						echo "<b>$current_matiere:</b> (<i>$periode</i>)<br />".htmlentities($ligtmp->appreciation);
						echo "</div>\n";
						*/

						$tab_imagemap[$cpt][$i]=htmlentities($ligtmp->appreciation);
						$info_imagemap[$i]="Au moins une appréciation";
					}
					else{
						$tab_imagemap[$cpt][$i]="";
					}
					//echo "</div>\n";

				}

				if($liste_temp==""){
					$liste_temp="temp$cpt=$serie[$cpt]";
				}
				else{
					$liste_temp="$liste_temp&amp;temp$cpt=$serie[$cpt]";
				}


				if($affiche_mgen=='oui'){
					if($cpt2>0){
					//if($temoin_au_moins_une_vraie_moyenne=="oui"){
						$totaltmp=0;
						for($i=1;$i<=count($coef_serie[$cpt]);$i++){$totaltmp=$totaltmp+$coef_serie[$cpt][$i];}
						// Si aucun coeff n'est saisi, on calcule une moyenne non pondérée.
						if($totaltmp==0){
							$totaltmp=0;
							for($i=1;$i<=count($note_serie[$cpt]);$i++){$totaltmp=$totaltmp+$note_serie[$cpt][$i];}
							$mgen[$cpt]=round($totaltmp/$cpt2,1);
						}
						else{
							$somme_des_coeff=$totaltmp;
							$totaltmp=0;
							for($i=1;$i<=count($note_serie[$cpt]);$i++){$totaltmp=$totaltmp+$note_serie[$cpt][$i]*$coef_serie[$cpt][$i];}
							$mgen[$cpt]=round($totaltmp/$somme_des_coeff,1);
						}
					}
					else{
						$mgen[$cpt]="-";
					}

					$liste_temp="$liste_temp&amp;mgen$cpt=$mgen[$cpt]";
				}


				$cpt++;
			}




			for($i=1;$i<=count($matiere);$i++){
				echo "<div id='div_matiere_$i' style='position: absolute; z-index: 1000; top: 300px; left: 200px; width: 300px; display:none;'>\n";
				if(isset($info_imagemap[$i])){
					echo "<div style='text-align: center; width: 300px; border: 1px solid black; background-color:white;'>\n";
					echo "<b>".htmlentities($matiere_nom[$i]).":</b><br />";
					//echo "<table border='1' align='center'>\n";
					echo "<table class='boireaus' style='margin:2px;' width='99%'>\n";
					for($j=1;$j<=count($num_periode);$j++){
						if($tab_imagemap[$j][$i]!=""){
							echo "<tr><td style='font-weight:bold;'>$j</td><td style='text-align:center;'>".$tab_imagemap[$j][$i]."</td></tr>\n";
						}
					}
					echo "</table>\n";
					echo "</div>\n";
				}
				echo "</div>\n";
			}

			$sql="SELECT * FROM avis_conseil_classe WHERE login='$eleve1' ORDER BY periode";
			$res_avis=mysql_query($sql);
			echo "<div id='div_avis_1' style='position: absolute; z-index: 1000; top: 300px; left: 200px; width: 300px; display:none;'>\n";
			if(mysql_num_rows($res_avis)>0){
				echo "<div style='text-align: center; width: 300px; border: 1px solid black; background-color:white;'>\n";
				echo "<b>Avis du Conseil de classe:</b><br />";
				//echo "<table border='1' align='center'>\n";
				echo "<table class='boireaus' style='margin:2px;' width='99%'>\n";
				while($lig_avis=mysql_fetch_object($res_avis)){
					echo "<tr><td style='font-weight:bold;'>$lig_avis->periode</td><td style='text-align:center;'>".htmlentities($lig_avis->avis)."</td></tr>\n";
				}
				echo "</table>\n";
				echo "</div>\n";
			}
			echo "</div>\n";

			//if(count($tab_imagemap)>0){
				$largeurGrad=50;
				$largeurBandeDroite=80;
				$largeur_utile=$largeur_graphe-$largeurGrad-$largeurBandeDroite;
				$nbMat=count($matiere);
				$largeurMat=round($largeur_utile/$nbMat);

				echo "<map name='imagemap'>\n";
				//for($i=0;$i<count($tab_imagemap);$i++){
				for($i=1;$i<=count($matiere);$i++){
					$x0=$largeurGrad+($i-1)*$largeurMat;
					$x1=$x0+$largeurMat;
					echo "<area href=\"#\" onClick='return false;' onMouseover=\"div_info('div_matiere_',$i,'affiche');\" onMouseout=\"div_info('div_matiere_',$i,'cache');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";
				}

				$x0=$largeurGrad+($i-1)*$largeurMat;
				$x1=$largeur_graphe;
				echo "<area href=\"#\" onClick='return false;' onMouseover=\"div_info('div_avis_','1','affiche');\" onMouseout=\"div_info('div_avis_','1','cache');\" shape=\"rect\" coords=\"$x0,0,$x1,$hauteur_graphe\">";

				echo "</map>\n";
			//}



			$nbp=$nb_periode+1;

			echo "<a name='graph'></a>\n";

			if($type_graphe=='courbe'){
				//echo "<img src='draw_artichow_fig7.php?temp1=$temp1&temp2=$temp2&etiquette=$etiq&titre=$graph_title&v_legend1=$v_legend1&v_legend2=$v_legend2&compteur=$compteur&nb_data=3'>";
				//echo "<img src='draw_artichow_fig7.php?temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=Toutes_les_périodes&compteur=$compteur&nb_data=$nbp'>";
				//echo "<img src='draw_artichow_fig7.php?$liste_temp&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=Toutes_les_périodes&compteur=$compteur&nb_data=$nbp'>";
				//echo "<img src='draw_artichow_fig7.php?$liste_temp&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=Toutes_les_périodes&compteur=$compteur&nb_series=$nb_series&id_classe=$id_classe'>";
				echo "<img src='draw_graphe.php?";
				// $liste_temp contient les séries et les moyennes générales.
				echo "$liste_temp";
				echo "&amp;etiquette=$liste_matieres";
				echo "&amp;titre=$graph_title";
				echo "&amp;v_legend1=$eleve1";
				//echo "&amp;v_legend2=Toutes_les_périodes";
				echo "&amp;v_legend2=".rawurlencode("Toutes_les_périodes");
				echo "&amp;compteur=$compteur";
				echo "&amp;nb_series=$nb_series";
				echo "&amp;id_classe=$id_classe";
				echo "&amp;largeur_graphe=$largeur_graphe";
				echo "&amp;hauteur_graphe=$hauteur_graphe";
				echo "&amp;taille_police=$taille_police";
				echo "&amp;epaisseur_traits=$epaisseur_traits";
				if($affiche_moy_annuelle=="oui"){
					echo "&amp;affiche_moy_annuelle=$affiche_moy_annuelle";
				}
				echo "&amp;tronquer_nom_court=$tronquer_nom_court";
				//echo "'>";
				//echo "&amp;temoin_imageps=$temoin_imageps";
				echo "&amp;temoin_image_escalier=$temoin_image_escalier";
				echo "' style='border: 1px solid black;' height='$hauteur_graphe' width='$largeur_graphe' alt='Graphe' ";
				echo "usemap='#imagemap' ";
				echo "/>\n";
			}
			else{
				echo "<img src='draw_graphe_star.php?";
				//echo "<img src='draw_graphe.php?";
				// $liste_temp contient les séries et les moyennes générales.
				echo "$liste_temp";
				echo "&amp;etiquette=$liste_matieres";
				echo "&amp;titre=$graph_title";
				echo "&amp;v_legend1=$eleve1";
				//echo "&amp;v_legend2=Toutes_les_périodes";
				echo "&amp;v_legend2=".rawurlencode("Toutes_les_périodes");
				echo "&amp;compteur=$compteur";
				echo "&amp;nb_series=$nb_series";
				echo "&amp;id_classe=$id_classe";
				echo "&amp;largeur_graphe=$largeur_graphe";
				echo "&amp;hauteur_graphe=$hauteur_graphe";
				echo "&amp;taille_police=$taille_police";
				echo "&amp;epaisseur_traits=$epaisseur_traits";
				if($affiche_moy_annuelle=="oui"){
					echo "&amp;affiche_moy_annuelle=$affiche_moy_annuelle";
				}
				echo "&amp;tronquer_nom_court=$tronquer_nom_court";
				//echo "'>";
				//echo "&amp;temoin_imageps=$temoin_imageps";
				echo "&amp;temoin_image_escalier=$temoin_image_escalier";
				echo "' style='border: 1px solid black;' height='$hauteur_graphe' width='$largeur_graphe' alt='Graphe' ";
				//echo "usemap='#imagemap' ";
				echo "/>\n";
			}

			//===================================

		}


	/*
		echo "<p>\n";
		echo "\$liste_matieres=$liste_matieres<br />\n";
		for($i=1;$i<=count($serie);$i++){
			echo "\$serie[$i]=$serie[$i]<br />\n";
		}
		echo "</p>\n";
	*/



	/*
		echo "\$nb_periode=$nb_periode<br />";
		$num_periode=1;

		$cpt=1;
		// Boucle sur l'ordre des matières:
		while($ligne=mysql_fetch_object($call_classe_infos)){
			// Nom court/long de la matière:
			$matiere[$cpt]=$ligne->matiere;
			$matiere_nom[$cpt]=$ligne->nom_complet;
			$cpt++;
		}

		for(){
	*/

		if(isset($prenom1)){
			echo "<p align='center'>$prenom1 $nom1";
			//if($doublant1!="-"){echo " (<i>$doublant1</i>)";}
			if(($doublant1!="-")&&($doublant1!="")){echo " (<i>$doublant1</i>)";}
			echo " né";
			if($sexe1=="F"){echo "e";}
			echo " le $naissance1[2]/$naissance1[1]/$naissance1[0] (<i>soit $age1 $precision1</i>).</p>";
		}
	}
	else{
		if ($_SESSION['statut'] == "eleve" OR $_SESSION['statut'] == "responsable") {
			echo "<p align='center'>Choisissez une période et validez.</p>\n";
		} else {
			echo "<p align='center'>Choisissez un élève et validez.</p>\n";
		}
	}
	echo "</td>\n";
	//====================================================================
/*
	// Bande d'affichage de l'image:
	echo "<td>\n";
	echo "<a name='graph'></a>\n";
	//echo "<img src='draw_artichow_fig7.php?temp1=$temp1&temp2=$temp2&etiquette=$etiq&titre=$graph_title&v_legend1=$v_legend1&v_legend2=$v_legend2&compteur=$compteur&nb_data=3'>";
	echo "<img src='draw_artichow_fig7.php?temp1=$serie[1]&temp2=$serie[2]&etiquette=$liste_matieres&titre=$graph_title&v_legend1=$eleve1&v_legend2=$eleve2&compteur=$compteur&nb_data=3'>";
	echo "</td>\n";
*/
	//====================================================================
	echo "</tr>\n";
	echo "</table>\n";

	if(!isset($_POST['is_posted'])){
		// Pour la première validation lors de l'accès à la page de graphe et ainsi obtenir directement le premier affichage:
		echo "<script type='text/javascript'>
	document.forms['form_choix_eleves'].submit();
</script>\n";
	}

	//echo "<div id='div_truc' style='position: absolute; z-index: 1000; top: 300px; left: 0px; width: 0px; border: 1px solid black; background-color:white; display:none;'>BLABLA</div>\n";
	//echo "<div id='div_truc' class='infodiv'>BLABLA</div>\n";
	//echo "<div id='divtruc' class='infodiv'>BLABLA</div>\n";

}
require("../lib/footer.inc.php");
?>