<?php
/**
 * saisie des Notes
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @license GNU/GPL
 * @package Carnet_de_notes
 * @subpackage saisie
 * @see add_token_field()
 * @see check_token()
 * @see checkAccess()
 * @see corriger_caracteres()
 * @see creer_div_infobulle()
 * @see formate_date()
 * @see getSettingValue()
 * @see get_group()
 * @see get_groups_for_prof()
 * @see getPref()
 * @see html_entity_decode()
 * @see javascript_tab_stat()
 * @see mise_a_jour_moyennes_conteneurs()
 * @see nom_photo()
 * @see recherche_enfant()
 * @see Session::security_check()
 * @see sous_conteneurs()
 * @see traite_accents_utf8()
 * @see traitement_magic_quotes()
 * @see Verif_prof_cahier_notes()
 */

/*
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
//==============================
// PREPARATIFS boireaus 20080422
// Pour passer à no_anti_inject comme pour les autres saisies d'appréciations
// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$mode_commentaire_20080422="";
//$mode_commentaire_20080422="no_anti_inject";

if($mode_commentaire_20080422=="no_anti_inject") {
	$variables_non_protegees = 'yes';
}
//==============================

/**
 * Fichiers d'initialisation
 */
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

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
	die("Le module n'est pas activé.");
}
unset($id_devoir);
$id_devoir = isset($_POST["id_devoir"]) ? $_POST["id_devoir"] : (isset($_GET["id_devoir"]) ? $_GET["id_devoir"] : NULL);
unset($affiche_message);
$affiche_message = isset($_POST["affiche_message"]) ? $_POST["affiche_message"] : (isset($_GET["affiche_message"]) ? $_GET["affiche_message"] : NULL);

$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "classe");

if ($id_devoir)  {
	$sql="SELECT * FROM cn_devoirs WHERE id ='$id_devoir';";
	//echo "$sql<br />";
	$appel_devoir = mysql_query($sql);
	$nom_devoir = mysql_result($appel_devoir, 0, 'nom_court');
	$ramener_sur_referentiel_dev_choisi=mysql_result($appel_devoir, 0, 'ramener_sur_referentiel');
	$note_sur_dev_choisi=mysql_result($appel_devoir, 0, 'note_sur');

	$sql="SELECT id_conteneur, id_racine FROM cn_devoirs WHERE id = '$id_devoir';";
	$query = mysql_query($sql);
	$id_racine = mysql_result($query, 0, 'id_racine');
	$id_conteneur = mysql_result($query, 0, 'id_conteneur');
} else if ((isset($_POST['id_conteneur'])) or (isset($_GET['id_conteneur']))) {
	$id_conteneur = isset($_POST['id_conteneur']) ? $_POST['id_conteneur'] : (isset($_GET['id_conteneur']) ? $_GET['id_conteneur'] : NULL);
	$query = mysql_query("SELECT id_racine FROM cn_conteneurs WHERE id = '$id_conteneur'");
	if(mysql_num_rows($query)==0) {
		$msg="Le conteneur numero $id_conteneur n'existe pas. C'est une anomalie.";
		header("Location: index.php?msg=$msg");
		die();
	}
	else {
		$id_racine = mysql_result($query, 0, 'id_racine');
	}
} else {
	//debug_var();

	if (($_SESSION['statut']=='professeur')&&(isset($_GET['id_groupe'])&&(isset($_GET['periode_num'])))) {
		$id_groupe = $_GET['id_groupe'];
		$periode_num = $_GET['periode_num'];

		if (is_numeric($id_groupe) && $id_groupe > 0) {
			$current_group = get_group($id_groupe);

			$login_prof = $_SESSION['login'];
			$appel_cahier_notes = mysql_query("SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe='$id_groupe' and periode='$periode_num')");
			$nb_cahier_note = mysql_num_rows($appel_cahier_notes);
			if ($nb_cahier_note == 0) {
				$nom_complet_matiere = $current_group["matiere"]["nom_complet"];
				$nom_court_matiere = $current_group["matiere"]["matiere"];
				$reg = mysql_query("INSERT INTO cn_conteneurs SET id_racine='', nom_court='".traitement_magic_quotes($current_group["description"])."', nom_complet='". traitement_magic_quotes($nom_complet_matiere)."', description = '', mode = '2', coef = '1.0', arrondir = 's1', ponderation = '0.0', display_parents = '0', display_bulletin = '1', parent = '0'");
				if ($reg) {
					$id_racine = mysql_insert_id();
					$reg = mysql_query("UPDATE cn_conteneurs SET id_racine='$id_racine', parent = '0' WHERE id='$id_racine'");
					$reg = mysql_query("INSERT INTO cn_cahier_notes SET id_groupe = '$id_groupe', periode = '$periode_num', id_cahier_notes='$id_racine'");
				}
			} else {
				$id_racine = mysql_result($appel_cahier_notes, 0, 'id_cahier_notes');
			}
			$id_conteneur=$id_racine;
		}
		else {
			header("Location: ../logout.php?auto=1");
			die();
		}
	}
	else {
		header("Location: ../logout.php?auto=1");
		die();
	}
}


//Initialisation pour le pdf
$w_pdf=array();
$w1 = "i"; //largeur de la première colonne
$w1b = "d"; //largeur de la colonne "classe" si présente
$w2 = "n"; // largeur des colonnes "notes"
$w3 = "c"; // largeur des colonnes "commentaires"
$header_pdf=array();
$data_pdf=array();

$mode_utf8_pdf=getSettingValue("mode_utf8_visu_notes_pdf");
if($mode_utf8_pdf=="") {$mode_utf8_pdf="n";}

$appel_conteneur = mysql_query("SELECT * FROM cn_conteneurs WHERE id ='$id_conteneur'");
$nom_conteneur = mysql_result($appel_conteneur, 0, 'nom_court');
$mode = mysql_result($appel_conteneur, 0, 'mode');
$arrondir = mysql_result($appel_conteneur, 0, 'arrondir');
$ponderation = mysql_result($appel_conteneur, 0, 'ponderation');
$display_bulletin = mysql_result($appel_conteneur, 0, 'display_bulletin');

// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
	$mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
	header("Location: index.php?msg=$mess");
	die();
}

//
// On dispose donc pour la suite des trois variables :
// id_racine
// id_conteneur
// id_devoir

$appel_cahier_notes = mysql_query("SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe = mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group = get_group($id_groupe);
$id_classe = $current_group["classes"]["list"][0];
$periode_num = mysql_result($appel_cahier_notes, 0, 'periode');
if (count($current_group["classes"]["list"]) > 1) {
	$multiclasses = true;
} else {
	$multiclasses = false;
	$order_by = "nom";
}
/**
 * Gestion des périodes
 */
include "../lib/periodes.inc.php";

// On teste si la periode est vérouillée !
if (($current_group["classe"]["ver_periode"]["all"][$periode_num] <= 1) and (isset($id_devoir)) and ($id_devoir!='') ) {
	$mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes dont la période est bloquée !");
	header("Location: index.php?msg=$mess");
	die();
}


$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];


$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
$nom_periode = mysql_result($periode_query, $periode_num-1, "nom_periode");

//
// Détermination des sous-conteneurs
//
$nom_sous_cont = array();
$id_sous_cont  = array();
$coef_sous_cont = array();
$display_bulletin_sous_cont = array();
$nb_sous_cont = 0;
if ($mode==1) {
	// on s'intéresse à tous les conteneurs fils, petit-fils, ...
	sous_conteneurs($id_conteneur,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'all');
} else {
	// On s'intéresse uniquement au conteneurs fils
	sous_conteneurs($id_conteneur,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'');
}

//debug_var();
//-------------------------------------------------------------------------------------------------------------------
if (isset($_POST['notes'])) {
	check_token();
	$temp = $_POST['notes']." 1";
	$temp = my_ereg_replace("\\\\r","\r",$temp);
	$temp = my_ereg_replace("\\\\n","\n",$temp);
	$longueur = strlen($temp);
	$i = 0;
	$fin_note = 'yes';
	$indice = $_POST['debut_import']-2;
	$tempo = '';
	if(!isset($note_sur_dev_choisi)) {$note_sur_dev_choisi=20;}
	while (($i < $longueur) and ($indice < $_POST['fin_import'])) {
		$car = substr($temp, $i, 1);
		if (my_ereg('^[0-9.,a-zA-Z-]{1}$', $car)) {
			if (($fin_note=='yes') or ($i == $longueur-1)) {
				$fin_note = 'no';
				if (is_numeric($tempo)) {
					if ($tempo <= $note_sur_dev_choisi) {
						$note_import[$indice] = $tempo;
						$indice++;
					} else {
						$note_import[$indice] = "0";
						$indice++;
					}
				} else {
					$note_import[$indice] = $tempo;
					$indice++;
				}
				$tempo = '';
			}
			$tempo=$tempo.$car;
		} else {
			$fin_note = 'yes';
		}
		$i++;
	}
}

//debug_var();
//-------------------------------------------------------------------------------------------------------------------
if (isset($_POST['import_sacoche'])) {
	//check_token();
	//@TODO check referer
	
	$note_import = array();
	$i = 0;
	if(!isset($note_sur_dev_choisi)) {$note_sur_dev_choisi=20;}
	do {
		$note_import_sacoche[$_POST['log_eleve'][$i]] = round($_POST['note_eleve'][$i]*$note_sur_dev_choisi/100);
		$i++;
	} while ($i < $_POST['indice_max_log_eleve']); 
}

if (isset($_POST['appreciations'])) {
	check_token();

	$temp = $_POST['appreciations']." 1";
	$temp = my_ereg_replace("\\\\r","`",$temp);
	$temp = my_ereg_replace("\\\\n","",$temp);
	$temp = unslashes($temp);
 	$longueur = strlen($temp);
	$i = 0;
	$fin_app = 'yes';
	$indice = $_POST['debut_import']-2;
	$tempo = "";
	while (($i < $longueur) and ($indice < $_POST['fin_import'])) {
		$car = substr($temp, $i, 1);
		if (!my_ereg ("^[`]{1}$", $car)) {
			if (($fin_app=='yes') or ($i == $longueur-1)) {
				$fin_app = 'no';
				$appreciations_import[$indice] = $tempo;
				$indice++;
				$tempo = '';
			}
			$tempo=$tempo.$car;
		} else {
  			$fin_app = 'yes';
		}
		$i++;
	}
}

if (isset($_POST['is_posted'])) {
	check_token();

	$log_eleve=$_POST['log_eleve'];
	$note_eleve=$_POST['note_eleve'];
	if($mode_commentaire_20080422!="no_anti_inject") {
		$comment_eleve=$_POST['comment_eleve'];
	}

	$indice_max_log_eleve=$_POST['indice_max_log_eleve'];

	for($i=0;$i<$indice_max_log_eleve;$i++){
		if(isset($log_eleve[$i])) {
			// La période est-elle ouverte?
			$reg_eleve_login=$log_eleve[$i];
			if(isset($current_group["eleves"][$periode_num]["users"][$reg_eleve_login]["classe"])){
				$id_classe = $current_group["eleves"][$periode_num]["users"][$reg_eleve_login]["classe"];
				if ($current_group["classe"]["ver_periode"][$id_classe][$periode_num] == "N") {
					$note=$note_eleve[$i];
					$elev_statut='';

					// PREPARATIFS boireaus 20080422
					// Pour passer à no_anti_inject comme pour les autres saisies d'appréciations
					if($mode_commentaire_20080422!="no_anti_inject") {
						// Problème: les accents sont codés en HTML...
						$comment=$comment_eleve[$i];
						$comment=addslashes(my_ereg_replace('(\\\r\\\n)+',"\r\n",my_ereg_replace("&#039;","'",html_entity_decode($comment))));
					}
					else {
						if (isset($NON_PROTECT["comment_eleve".$i])){
							$comment = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["comment_eleve".$i]));
						}
						else{
							$comment = "";
						}
						// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
						$comment=my_ereg_replace('(\\\r\\\n)+',"\r\n",$comment);
					}
					
					if (($note == 'disp')||($note == 'd')) {
						$note = '0';
						$elev_statut = 'disp';
					}
					else if (($note == 'abs')||($note == 'a')) {
						$note = '0';
						$elev_statut = 'abs';
					}
					else if (($note == '-')||($note == 'n')) {
						$note = '0';
						$elev_statut = '-';
					}
					else if (my_ereg ("^[0-9\.\,]{1,}$", $note)) {
						$note = str_replace(",", ".", "$note");
                                                $appel_note_sur = mysql_query("SELECT NOTE_SUR FROM cn_devoirs WHERE id = '$id_devoir'");
                                                $note_sur_verif = mysql_result($appel_note_sur,0 ,'note_sur');
						if (($note < 0) or ($note > $note_sur_verif)) {
							$note = '';
							$elev_statut = 'v';
						}
					}
					else {
						$note = '';
						$elev_statut = 'v';
					}

					$test_eleve_note_query = mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$reg_eleve_login' AND id_devoir = '$id_devoir')");
					$test = mysql_num_rows($test_eleve_note_query);
					if ($test != "0") {
						$sql="UPDATE cn_notes_devoirs SET comment='".$comment."', note='$note',statut='$elev_statut' WHERE (login='".$reg_eleve_login."' AND id_devoir='".$id_devoir."');";
						//echo "$sql<br />";
						$register = mysql_query($sql);
					} else {
						$sql="INSERT INTO cn_notes_devoirs SET login='".$reg_eleve_login."', id_devoir='".$id_devoir."',note='".$note."',statut='".$elev_statut."',comment='".$comment."';";
						$register = mysql_query($sql);
					}

				}
			}
		}
	}

	// Mise à jour des moyennes du conteneur et des conteneurs parent, grand-parent, etc...
	
	$arret = 'no';
	mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur,$arret);

    //==========================================================
    // Ajout d'un test:
    // Si on modifie un devoir alors que des notes ont été reportées sur le bulletin, il faut penser à mettre à jour la recopie vers le bulletin.
    $sql="SELECT 1=1 FROM matieres_notes WHERE periode='".$periode_num."' AND id_groupe='".$id_groupe."';";
    $test_bulletin=mysql_query($sql);
    if(mysql_num_rows($test_bulletin)>0) {
        $msg=" ATTENTION: Des notes sont présentes sur le bulletin.<br />Si vous avez modifié ou ajouté des notes, pensez à mettre à jour la recopie vers le bulletin.";
    }
    //==========================================================

	$affiche_message = 'yes';
}

if((isset($_GET['recalculer']))&&(isset($id_conteneur))&&(isset($periode_num))&&(isset($current_group))) {
	check_token();

	if((isset($id_conteneur))&&($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2)) {

		recherche_enfant($id_conteneur);
	}
}
if (isset($_POST['import_sacoche'])) {
	$message_enregistrement = "Vos notes ne sont pas encore enregistrées, veuillez les vérifier et cliquer sur le bouton d'enregistrement";
} else {
	$message_enregistrement = "Les modifications ont été enregistrées !";
}
$themessage  = 'Des notes ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Saisie des notes";
    /**
     * Entête de la page
     */
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var();

unset($_SESSION['chemin_retour']);

?>
<script type="text/javascript" language=javascript>
chargement = false;
</script>

<?php
if($id_conteneur==$id_racine){
	if($nom_conteneur==""){
		$titre=htmlentities($current_group['description'])." (".$nom_periode.")";
	}
	else{
		$titre=$nom_conteneur." (".$nom_periode.")";
	}
}
else{
	$titre=htmlentities(ucfirst(strtolower(getSettingValue("gepi_denom_boite"))))." : ".$nom_conteneur." (".$nom_periode.")";
}

$titre_pdf = urlencode(traite_accents_utf8(html_entity_decode($titre)));
if ($id_devoir != 0) {$titre .= " - SAISIE";} else {$titre .= " - VISUALISATION";}

echo "<script type=\"text/javascript\" language=\"javascript\">";
if (isset($_POST['debut_import'])) {
	$temp = $_POST['debut_import']-1;
	if ((isset($note_import[$temp])) and ($note_import[$temp] != '')) {echo "change = 'yes';";} else {echo "change = 'no';";}
} else {
	echo "change = 'no';";
}
echo "</script>";


// Détermination du nombre de devoirs à afficher
$appel_dev = mysql_query("select * from cn_devoirs where (id_conteneur='$id_conteneur' and id_racine='$id_racine') order by date");
$nb_dev  = mysql_num_rows($appel_dev);

// Détermination des noms et identificateurs des devoirs
$j = 0;
while ($j < $nb_dev) {
	$nom_dev[$j] = mysql_result($appel_dev, $j, 'nom_court');
	$id_dev[$j] = mysql_result($appel_dev, $j, 'id');
	$coef[$j] = mysql_result($appel_dev, $j, 'coef');
	$note_sur[$j] = mysql_result($appel_dev, $j, 'note_sur');
	$ramener_sur_referentiel[$j] = mysql_result($appel_dev, $j, 'ramener_sur_referentiel');
	$facultatif[$j] = mysql_result($appel_dev, $j, 'facultatif');
	$display_parents[$j] = mysql_result($appel_dev, $j, 'display_parents');
	$date = mysql_result($appel_dev, $j, 'date');
	$annee = substr($date,0,4);
	$mois =  substr($date,5,2);
	$jour =  substr($date,8,2);
	$display_date[$j] = $jour."/".$mois."/".$annee;
	$j++;
}

echo "<form enctype=\"multipart/form-data\" name= \"form1\" action=\"saisie_notes.php\" method=\"get\">\n";
echo "<p class='bold'>\n";
echo "<a href=\"../accueil.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a>|";
echo "<a href='index.php'  onclick=\"return confirm_abandon (this, change, '$themessage')\"> Mes enseignements </a>|";




if(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='secours')) {
	if($_SESSION['statut']=='professeur') {
		$login_prof_groupe_courant=$_SESSION["login"];
	}
	else {
		$tmp_current_group=get_group($id_groupe);

		$login_prof_groupe_courant=$tmp_current_group["profs"]["list"][0];
	}

	$tab_groups = get_groups_for_prof($login_prof_groupe_courant,"classe puis matière");

	if(!empty($tab_groups)) {

		$chaine_options_classes="";

		$num_groupe=-1;
		$nb_groupes_suivies=count($tab_groups);
		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		for($loop=0;$loop<count($tab_groups);$loop++) {
			if((!isset($tab_groups[$loop]["visibilite"]["cahier_notes"]))||($tab_groups[$loop]["visibilite"]["cahier_notes"]=='y')) {
				// On ne retient que les groupes qui ont un nombre de périodes au moins égal à la période sélectionnée
				if($tab_groups[$loop]["nb_periode"]>=$periode_num) {
					if($tab_groups[$loop]['id']==$id_groupe){
						$num_groupe=$loop;
	
						$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
	
						$temoin_tmp=1;
						if(isset($tab_groups[$loop+1])){
							$id_grp_suiv=$tab_groups[$loop+1]['id'];
						}
						else{
							$id_grp_suiv=0;
						}
					}
					else {
						$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
					}
	
					if($temoin_tmp==0){
						$id_grp_prec=$tab_groups[$loop]['id'];
					}
				}
			}
		}

		if(($chaine_options_classes!="")&&($nb_groupes_suivies>1)) {

			echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		//alert('thechange='+thechange+' '+document.getElementById('id_groupe').selectedIndex+' '+document.getElementById('id_groupe').options[document.getElementById('id_groupe').selectedIndex].value);
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else {
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_groupe').selectedIndex=$num_groupe;
			}
		}
	}
</script>\n";

			echo "<input type='hidden' name='periode_num' value='$periode_num' />\n";
			echo "Période $periode_num: <select name='id_groupe' id='id_groupe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
			echo $chaine_options_classes;
			echo "</select> | \n";
		}

	}
}

// Recuperer la liste des cahiers de notes
$sql="SELECT * FROM cn_cahier_notes ccn where id_groupe='$id_groupe' ORDER BY periode;";
$res_cn=mysql_query($sql);
if(mysql_num_rows($res_cn)>1) {
	// On ne propose pas de champ SELECT pour un seul canier de notes
	$chaine_options_periodes="";
	while($lig_cn=mysql_fetch_object($res_cn)) {
		$chaine_options_periodes.="<option value='$lig_cn->id_cahier_notes'";
		if($lig_cn->periode==$periode_num) {$chaine_options_periodes.=" selected='true'";}
		$chaine_options_periodes.=">$lig_cn->periode</option>\n";
	}

	$index_num_periode=$periode_num-1;

	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_periode(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.getElementById('form1b_id_conteneur').value=document.getElementById('id_conteneur').options[document.getElementById('id_conteneur').selectedIndex].value;
			document.form1b.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.getElementById('form1b_id_conteneur').value=document.getElementById('id_conteneur').options[document.getElementById('id_conteneur').selectedIndex].value;
				document.form1b.submit();
			}
			else{
				document.getElementById('id_conteneur').selectedIndex=$index_num_periode;
			}
		}
	}
</script>\n";

	echo "<span title='Accéder au cahier de notes de la période (ne sont proposées que les périodes pour lesquelles le cahier de notes a été initialisé)'>Période </span><select name='tmp_id_conteneur' id='id_conteneur' onchange=\"confirm_changement_periode(change, '$themessage');\">\n";
	echo $chaine_options_periodes;
	echo "</select> | \n";

}

echo "<a href=\"index.php?id_racine=$id_racine\" onclick=\"return confirm_abandon (this, change, '$themessage')\"> Mes évaluations</a> |";

// On dé-cache le champ si javascript est actif
echo "<span id='span_chgt_dev' style='display: none;'>\n";
$sql="select * from cn_devoirs where id_conteneur='$id_conteneur' order by date;";
$res_devoirs=mysql_query($sql);
if(mysql_num_rows($res_devoirs)>1) {

	$chaine_options_devoirs="";
	$num_devoir=0;
	$cpt_dev=0;
	while($lig_dev=mysql_fetch_object($res_devoirs)) {
		$chaine_options_devoirs.="<option value='$lig_dev->id'";
		if($lig_dev->id==$id_devoir) {$chaine_options_devoirs.=" selected='true'"; $num_devoir=$cpt_dev;}
		$chaine_options_devoirs.=">$lig_dev->nom_court (".formate_date($lig_dev->date).")</option>\n";
		$cpt_dev++;
	}
	// Seulement avec javascript... parce qu'on a plusieurs submit dans ce formulaire là...
	echo "<select name='select_id_devoir' id='select_id_devoir' onchange=\"confirm_changement_devoir(change, '$themessage');\">\n";
	echo $chaine_options_devoirs;
	echo "</select>";
	echo " | \n";
}
echo "</span>\n";

if((isset($id_devoir))&&($id_devoir!=0)) {echo "<a href=\"saisie_notes.php?id_conteneur=$id_racine\" onclick=\"return confirm_abandon (this, change, '$themessage')\"> Visualisation du CN </a>|";}

if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) {
	//echo "<a href='add_modif_conteneur.php?id_racine=$id_racine&amp;mode_navig=retour_saisie&amp;id_retour=$id_conteneur' onclick=\"return confirm_abandon (this, change,'$themessage')\">Créer une boîte</a>|";
	echo "<a href='add_modif_conteneur.php?id_racine=$id_racine&amp;mode_navig=retour_saisie&amp;id_retour=$id_conteneur' onclick=\"return confirm_abandon (this, change,'$themessage')\"> Créer un";

	if(getSettingValue("gepi_denom_boite_genre")=='f'){echo "e";}

	echo " ".htmlentities(strtolower(getSettingValue("gepi_denom_boite")))." </a>|";

	echo "<a href='add_modif_dev.php?id_conteneur=$id_racine&amp;mode_navig=retour_saisie&amp;id_retour=$id_conteneur' onclick=\"return confirm_abandon (this, change,'$themessage')\"> Créer une évaluation </a>|";
}
echo "<a href=\"../fpdf/imprime_pdf.php?titre=$titre_pdf&amp;id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;nom_pdf_en_detail=oui\" onclick=\"return VerifChargement()\"> Imprimer au format PDF </a>|";

echo "<a href=\"../groupes/signalement_eleves.php?id_groupe=$id_groupe&amp;chemin_retour=../cahier_notes/saisie_notes.php?id_conteneur=$id_conteneur\"> Signaler des erreurs d'affectation</a>";
echo "</p>\n";
echo "</form>\n";

if(isset($num_devoir)) {
	echo "<script type='text/javascript' language='JavaScript'>
	if(document.getElementById('span_chgt_dev')) {document.getElementById('span_chgt_dev').style.display='';}

	function confirm_changement_devoir(thechange, themessage) {
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.location.href=\"".$_SERVER['PHP_SELF']."?id_conteneur=$id_conteneur&id_devoir=\"+document.getElementById('select_id_devoir').options[document.getElementById('select_id_devoir').selectedIndex].value;
		}
		else {
			var is_confirmed = confirm(themessage);
			if(is_confirmed) {
				document.location.href=\"".$_SERVER['PHP_SELF']."?id_conteneur=$id_conteneur&id_devoir=\"+document.getElementById('select_id_devoir').options[document.getElementById('select_id_devoir').selectedIndex].value;
			}
			else {
				document.getElementById('select_id_devoir').selectedIndex=$num_devoir;
			}
		}
	}

</script>\n";
}

// Affichage ou non les colonnes "commentaires"
// Affichage ou non de tous les devoirs
if (isset($_POST['ok'])) {
	if (isset($_POST['affiche_comment'])) {
		$_SESSION['affiche_comment'] = 'no';
	} else {
		$_SESSION['affiche_comment'] = 'yes';
	}
	if (isset($_POST['affiche_tous'])) {
		$_SESSION['affiche_tous'] = 'yes';
	} else {
		$_SESSION['affiche_tous'] = 'no';
	}

}
if (!isset($_SESSION['affiche_comment'])) {$_SESSION['affiche_comment'] = 'yes';}
if (!isset($_SESSION['affiche_tous'])) {$_SESSION['affiche_tous'] = 'no';}
$nb_dev_sous_cont = 0;

// Premier formulaire pour masquer ou non les colonnes "commentaires" non vides des évaluations verrouillées
if ($id_devoir == 0) {
	echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=post name=\"form1b\">\n";
	echo "<fieldset style=\"padding-top: 0px; padding-bottom: 0px;  margin-left: 0px; margin-right: 100px;\">\n";
	echo "<table summary='Paramètres'><tr><td>Masquer les colonnes \"commentaires\" non vides (mode visualisation uniquement) :
	</td><td><input type=\"checkbox\" name=\"affiche_comment\"  ";
	if ($_SESSION['affiche_comment'] != 'yes') echo "checked";
	echo " /></td><td><input type=\"submit\" name=\"ok\" value=\"OK\" /></td></tr>\n";
	$nb_dev_sous_cont = mysql_num_rows(mysql_query("select d.id from cn_devoirs d, cn_conteneurs c where (d.id_conteneur = c.id and c.parent='$id_conteneur')"));
	if ($nb_dev_sous_cont != 0) {
		echo "<tr><td>Afficher les évaluations des \"sous-".htmlentities(strtolower(getSettingValue("gepi_denom_boite")))."s\" : </td><td><input type=\"checkbox\" name=\"affiche_tous\"  ";
		if ($_SESSION['affiche_tous'] == 'yes') {echo "checked";}
		echo " /></td><td></td></tr>\n";
	}
	echo "</table></fieldset>\n";
	echo "<input type='hidden' name='id_conteneur' id='form1b_id_conteneur' value=\"".$id_conteneur."\" />\n";
	echo "<input type='hidden' name='id_devoir' value=\"".$id_devoir."\" />\n";
	echo "</form>\n";
}
else {
	// Formulaire destiné à permettre via javascript de passer à une autre période... on accède alors au mode Visualisation parce que le $id_devoir n'existe pas dans une autre période.
	echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=post name=\"form1b\">\n";
	echo "<input type='hidden' name='id_conteneur' id='form1b_id_conteneur' value=\"".$id_conteneur."\" />\n";
	//echo "<input type='hidden' name='id_devoir' value=\"".$id_devoir."\" />\n";
	echo "</form>\n";
}
// Fin du premier formulaire

// Construction de la variable $detail qui affiche dans un pop-up le mode de calcul de la moyenne
$detail = "Mode de calcul de la moyenne :\\n";
$detail = $detail."La moyenne s\\'effectue sur les colonnes repérées par les cellules de couleur violette.\\n";
if (($nb_dev_sous_cont != 0) and ($_SESSION['affiche_tous'] == 'no'))
	$detail = $detail."ATTENTION : cliquez sur \'Afficher les évaluations des sous-".htmlentities(strtolower(getSettingValue("gepi_denom_boite")))."s\' pour faire apparaître toutes les évaluations qui interviennent dans la moyenne.\\n";
if ($arrondir == 's1') $detail = $detail."La moyenne est arrondie au dixième de point supérieur.\\n";
if ($arrondir == 's5') $detail = $detail."La moyenne est arrondie au demi-point supérieur.\\n";
if ($arrondir == 'se') $detail = $detail."La moyenne est arrondie au point entier supérieur.\\n";
if ($arrondir == 'p1') $detail = $detail."La moyenne est arrondie au dixième de point le plus proche.\\n";
if ($arrondir == 'p5') $detail = $detail."La moyenne est arrondie au demi-point le plus proche.\\n";
if ($arrondir == 'pe') $detail = $detail."La moyenne est arrondie au point entier le plus proche.\\n";
if ($ponderation != 0) $detail = $detail."Pondération : ".$ponderation." (s\\'ajoute au coefficient de la meilleur note de chaque élève).\\n";

// Titre
echo "<h2 class='gepi'>".$titre."</h2>\n";
if (($nb_dev == 0) and ($nb_sous_cont==0)) {

	echo "<p class=cn>";
	if(getSettingValue("gepi_denom_boite_genre")=='f'){echo "La ";}else{echo "Le ";}
	echo htmlentities(strtolower(getSettingValue("gepi_denom_boite")))." $nom_conteneur ne contient aucune évaluation. </p>\n";

/**
 * Pied de page
 */
	require("../lib/footer.inc.php");
	die();
}

// Début du deuxième formulaire
echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=post  name=\"form2\">\n";
if ($id_devoir != 0) {
	echo add_token_field();
	echo "<center><input type='submit' value='Enregistrer' /></center>\n";
}

// Couleurs utilisées
$couleur_devoirs = '#AAE6AA';
$couleur_moy_cont = '#96C8F0';
$couleur_moy_sous_cont = '#FAFABE';
$couleur_calcul_moy = '#AAAAE6';
$note_sur_verif = 20;
if ($id_devoir != 0) {
        $appel_note_sur = mysql_query("SELECT NOTE_SUR FROM cn_devoirs WHERE id = '$id_devoir'");
        $note_sur_verif = mysql_result($appel_note_sur,'0' ,'note_sur');
	echo "<p class='cn'>Taper une note de 0 à ".$note_sur_verif." pour chaque élève, ou à défaut le code 'a' pour 'absent', le code 'd' pour 'dispensé', le code '-' ou 'n' pour absence de note.</p>\n";
	echo "<p class='cn'>Vous pouvez également <b>importer directement vos notes par \"copier/coller\"</b> à partir d'un tableur ou d'une autre application : voir tout en bas de cette page.</p>\n";

}
echo "<p class=cn><b>Enseignement : ".$current_group['description']." (" . $current_group["classlist_string"] . ")";
echo "</b></p>\n";

echo "
<script type='text/javascript' language='JavaScript'>

function verifcol(num_id){
	document.getElementById('n'+num_id).value=document.getElementById('n'+num_id).value.toLowerCase();
	if(document.getElementById('n'+num_id).value=='a'){
		document.getElementById('n'+num_id).value='abs';
	}
	if(document.getElementById('n'+num_id).value=='d'){
		document.getElementById('n'+num_id).value='disp';
	}
	if(document.getElementById('n'+num_id).value=='n'){
		document.getElementById('n'+num_id).value='-';
	}
	note=document.getElementById('n'+num_id).value;
	if((note!='-')&&(note!='disp')&&(note!='abs')&&(note!='')){
		note=note.replace(',','.');

		//if((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0))){

		if(((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0)))||
		((note.search(/^[0-9,]+$/)!=-1)&&(note.lastIndexOf(',')==note.indexOf(',',0)))){
			if((note>".$note_sur_verif.")||(note<0)){
				couleur='red';
			}
			else{
				couleur='$couleur_devoirs';
			}
		}
		else{
			couleur='red';
		}
	}
	else{
		couleur='$couleur_devoirs';
	}
	eval('document.getElementById(\'td_'+num_id+'\').style.background=couleur');
}
</script>
";

$i=0;
while ($i < $nb_dev) {
	$nocomment[$i]='yes';
	$i++;
}

// Tableau destiner à stocker l'id du champ de saisie de note (n$num_id) correspondant à l'élève $i
$indice_ele_saisie=array();

$i = 0;
$num_id=10;
$current_displayed_line = 0;

// On commence par mettre la liste dans l'ordre souhaité
if ($order_by != "classe") {
	$liste_eleves = $current_group["eleves"][$periode_num]["users"];
} else {
	// Ici, on tri par classe
	// On va juste créer une liste des élèves pour chaque classe
	$tab_classes = array();
	foreach($current_group["classes"]["list"] as $classe_id) {
		$tab_classes[$classe_id] = array();
	}
	// On passe maintenant élève par élève et on les met dans la bonne liste selon leur classe
	foreach($current_group["eleves"][$periode_num]["list"] as $e_login) {
		$classe = $current_group["eleves"][$periode_num]["users"][$e_login]["classe"];
		$tab_classes[$classe][$e_login] = $current_group["eleves"][$periode_num]["users"][$e_login];
	}
	// On met tout ça à la suite
	$liste_eleves = array();
	foreach($current_group["classes"]["list"] as $classe_id) {
		$liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
	}
}

$prev_classe = null;

$tab_graph=array();

foreach ($liste_eleves as $eleve) {
	$eleve_login[$i] = $eleve["login"];
	$eleve_nom[$i] = $eleve["nom"];
	$eleve_prenom[$i] = $eleve["prenom"];
	$eleve_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["classe"];
	$eleve_id_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["id"];
	$somme_coef = 0;

	$k=0;
	while ($k < $nb_dev) {
		$note_query = mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$eleve_login[$i]' AND id_devoir='$id_dev[$k]')");
		
		if($note_query){
			if(mysql_num_rows($note_query)>0){
				$eleve_statut = @mysql_result($note_query, 0, "statut");
				$eleve_note = @mysql_result($note_query, 0, "note");
				$eleve_comment = @mysql_result($note_query, 0, "comment");
			}
			else{
				$eleve_statut = "";
				$eleve_note = "";
				$eleve_comment = "";
			}
		}
		else{
			$eleve_statut = "";
			$eleve_note = "";
			$eleve_comment = "";
		}
		if ($eleve_comment != '') { $nocomment[$k]='no'; }
		$eleve_login_note = $eleve_login[$i]."_note";
		$eleve_login_comment = $eleve_login[$i]."_comment";
		if ($id_dev[$k] != $id_devoir) {
			$mess_note[$i][$k] = '';
			$mess_note[$i][$k] =$mess_note[$i][$k]."<td class=cn bgcolor=$couleur_devoirs><center><b>";
			if (($eleve_statut != '') and ($eleve_statut != 'v')) {
				$mess_note[$i][$k] = $mess_note[$i][$k].$eleve_statut;
				$mess_note_pdf[$i][$k] = $eleve_statut;
			} else if ($eleve_statut == 'v') {
				$mess_note[$i][$k] =$mess_note[$i][$k]."&nbsp;";
				$mess_note_pdf[$i][$k] = "";
			} else {
				if ($eleve_note != '') {
					$mess_note[$i][$k] =$mess_note[$i][$k].number_format($eleve_note,1, ',', ' ');
					$mess_note_pdf[$i][$k] = number_format($eleve_note,1, ',', ' ');

					$tab_graph[$k][]=number_format($eleve_note,1, '.', ' ');
				} else {
					$mess_note[$i][$k] =$mess_note[$i][$k]."&nbsp;";
					$mess_note_pdf[$i][$k] = "";
				}
			}
			$mess_note[$i][$k] =$mess_note[$i][$k]."</b></center></td>\n";
			if ($eleve_comment != '') {
				$mess_comment[$i][$k] = "<td class=cn>".$eleve_comment."</td>\n";
				$mess_comment_pdf[$i][$k] = traite_accents_utf8($eleve_comment);

			} else {
				$mess_comment[$i][$k] = "<td class=cn>&nbsp;</td>\n";
				$mess_comment_pdf[$i][$k] = "";
			}
		} else {
			$mess_note[$i][$k] = "<td class='cn' id='td_$num_id' style='text-align:center; background-color:$couleur_devoirs;'>\n";

			$mess_note[$i][$k].="<input type='hidden' name='log_eleve[$i]' id='log_eleve_$i' value='$eleve_login[$i]' />\n";
			
			if ($current_group["classe"]["ver_periode"][$eleve_id_classe[$i]][$periode_num] == "N") {
				
				$indice_ele_saisie[$i]=$num_id;
				$mess_note[$i][$k] .= "<input id=\"n".$num_id."\" onKeyDown=\"clavier(this.id,event);\" type=\"text\" size=\"4\" name=\"note_eleve[$i]\" ";
				$mess_note[$i][$k] .= "autocomplete='off' ";
				$mess_note[$i][$k] .= "value=\"";
			}

			if ((isset($note_import[$current_displayed_line])) and  ($note_import[$current_displayed_line] != '')) {
				$mess_note[$i][$k]=$mess_note[$i][$k].$note_import[$current_displayed_line];
				$mess_note_pdf[$i][$k] = $note_import[$current_displayed_line];
			} else if (isset($note_import_sacoche[$eleve["login"]])){
				$mess_note[$i][$k] .= $note_import_sacoche[$eleve["login"]];
				$mess_note_pdf[$i][$k] = $note_import_sacoche[$eleve["login"]];
			}
			else {
				if (($eleve_statut != '') and ($eleve_statut != 'v')) {
					$mess_note[$i][$k] = $mess_note[$i][$k].$eleve_statut;
					$mess_note_pdf[$i][$k] = $eleve_statut;
				} else if ($eleve_statut == 'v') {
					$mess_note_pdf[$i][$k] = "";
				} else {
					$mess_note[$i][$k] = $mess_note[$i][$k].$eleve_note;

					if($eleve_note=="") {
						// Ca ne devrait pas arriver... si: quand le devoir est créé, mais qu'aucune note n'est saisie, ni enregistrement encore effectué.
						// Le simple fait de cliquer sur Enregistrer remplit la table cn_notes_devoirs avec eleve_note='0.0' et eleve_statut='v' et on n'a plus l'erreur
						$mess_note_pdf[$i][$k] = $eleve_note;
					}
					else {
						if((preg_match("/^[0-9]*.[0-9]*$/",$eleve_note))||
						(preg_match("/^[0-9]*,[0-9]*$/",$eleve_note))||
						(preg_match("/^[0-9]*$/",$eleve_note))) {
							$mess_note_pdf[$i][$k] = number_format($eleve_note,1, ',', ' ');
			
							$tab_graph[$k][]=number_format($eleve_note,1, '.', ' ');
						}
						else {
							echo "<p style='color:red;'>BIZARRE: \$eleve_login[$i]=$eleve_login[$i] \$i=$i et \$k=$j<br />\$eleve_statut=$eleve_statut<br />\$eleve_note=$eleve_note<br />\n";
						}
					}
				}
			}
			if ($current_group["classe"]["ver_periode"][$eleve_id_classe[$i]][$periode_num] == "N") {
				$mess_note[$i][$k] = $mess_note[$i][$k]."\" onfocus=\"javascript:this.select()";

				if(getSettingValue("gepi_pmv")!="n"){
					$sql="SELECT elenoet FROM eleves WHERE login='$eleve_login[$i]';";
					$res_ele=mysql_query($sql);
					if(mysql_num_rows($res_ele)>0) {
						$lig_ele=mysql_fetch_object($res_ele);
						if (nom_photo($lig_ele->elenoet)){
							$mess_note[$i][$k].=";affiche_photo('".nom_photo($lig_ele->elenoet)."','".addslashes(strtoupper($eleve_nom[$i])." ".ucfirst(strtolower($eleve_prenom[$i])))."')";
						}
						else {
							$mess_note[$i][$k].=";document.getElementById('div_photo_eleve').innerHTML='';";
						}
					}
					else {
						$mess_note[$i][$k].=";document.getElementById('div_photo_eleve').innerHTML='';";
					}
				}
				$mess_note[$i][$k].="\" onchange=\"verifcol($num_id);calcul_moy_med();changement();\" />";
			}
			$mess_note[$i][$k] .= "</td>\n";
			$mess_comment[$i][$k] = "<td class='cn' bgcolor='$couleur_devoirs'>";
			if ($current_group["classe"]["ver_periode"][$eleve_id_classe[$i]][$periode_num] == "N"){
				if($mode_commentaire_20080422!="no_anti_inject") {
					if ((isset($appreciations_import[$current_displayed_line])) and  ($appreciations_import[$current_displayed_line] != '')) {
						$eleve_comment = $appreciations_import[$current_displayed_line];
					}
					$mess_comment[$i][$k] .= "<textarea id=\"n1".$num_id."\" onKeyDown=\"clavier(this.id,event);\" name='comment_eleve[$i]' rows=1 cols=60 class='wrap' onchange=\"changement()\"";
				}
				else {
					$mess_comment[$i][$k] .= "<textarea id=\"n1".$num_id."\" onKeyDown=\"clavier(this.id,event);\" name='no_anti_inject_comment_eleve".$i."' rows=1 cols=30 class='wrap' onchange=\"changement()\"";
				}
				if(getSettingValue("gepi_pmv")!="n"){
					$mess_comment[$i][$k] .= " onfocus=\"";
					$sql="SELECT elenoet FROM eleves WHERE login='$eleve_login[$i]';";
					$res_ele=mysql_query($sql);
					if(mysql_num_rows($res_ele)>0) {
						$lig_ele=mysql_fetch_object($res_ele);
						if (nom_photo($lig_ele->elenoet)){
							$mess_comment[$i][$k].=";affiche_photo('".nom_photo($lig_ele->elenoet)."','".addslashes(strtoupper($eleve_nom[$i])." ".ucfirst(strtolower($eleve_prenom[$i])))."')";
						}
						else {
							$mess_comment[$i][$k].=";document.getElementById('div_photo_eleve').innerHTML='';";
						}
					}
					else {
						$mess_comment[$i][$k].=";document.getElementById('div_photo_eleve').innerHTML='';";
					}
					$mess_comment[$i][$k] .= "\"";
				}
				$mess_comment[$i][$k] .= ">".$eleve_comment."</textarea></td>\n";
				
			}
			else{
				$mess_comment[$i][$k] .= $eleve_comment."</td>\n";
			}
			$mess_comment_pdf[$i][$k] = traite_accents_utf8($eleve_comment);
			$num_id++;
		}
		$k++;
	}
	$current_displayed_line++;
	$i++;
}

// Affichage du tableau

echo "<table class='boireaus' cellspacing='2' cellpadding='1' summary=\"Tableau de notes\">\n";

// Première ligne

// on calcule le nombre de colonnes à scinder
if ($id_devoir==0) {
	// Mode consultation
	$nb_colspan = $nb_dev;
	$i = 0;
	while ($i < $nb_dev) {
		if ((($nocomment[$i]!='yes') and ($_SESSION['affiche_comment'] == 'yes')) or ($id_dev[$i] == $id_devoir)) {$nb_colspan++;}
		$i++;
	}
} else {
	// En mode saisie, on n'affiche que le devoir à saisir
	$nb_colspan = 2;
}

// Affichage première ligne

echo "<tr><th class='cn'>&nbsp;</th>\n";
if ($multiclasses) {echo "<th class='cn'>&nbsp;</th>\n";}
echo "\n";
if ($nb_dev != 0) {
	if($nom_conteneur!=""){
		echo "<th class='cn' colspan='$nb_colspan' valign='top'>\n";
		echo "<div style='float:right; width:16;'><a href='javascript:affichage_quartiles();'><img src='../images/icons/histogramme.png' width='16' height='16' alt='Afficher les quartiles' title='Afficher les quartiles' /></a></div>\n";

		echo "$nom_conteneur\n";
		echo "</th>\n";
	}
	else{
		echo "<th class='cn' colspan='$nb_colspan' valign='top'><center>&nbsp;</center></th>\n";
	}
}

// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir==0) {
	$i=0;
	while ($i < $nb_sous_cont) {
		// on affiche les devoirs des sous-conteneurs si l'utilisateur a fait le choix de tout afficher
		if (($_SESSION['affiche_tous'] == 'yes') and ($id_devoir==0)) {
			$query_nb_dev = mysql_query("SELECT * FROM cn_devoirs where (id_conteneur='$id_sous_cont[$i]' and id_racine='$id_racine') order by date");
			$nb_dev_s_cont[$i]  = mysql_num_rows($query_nb_dev);
			$m = 0;
			while ($m < $nb_dev_s_cont[$i]) {
				$id_s_dev[$i][$m] = mysql_result($query_nb_dev, $m, 'id');
				$nom_sous_dev[$i][$m] = mysql_result($query_nb_dev, $m, 'nom_court');
				$coef_s_dev[$i][$m]  = mysql_result($query_nb_dev, $m, 'coef');
				$note_sur_s_dev[$i][$m] = mysql_result($query_nb_dev, $m, 'note_sur');
				$ramener_sur_referentiel_s_dev[$i][$m] = mysql_result($query_nb_dev, $m, 'ramener_sur_referentiel');
				$fac_s_dev[$i][$m]  = mysql_result($query_nb_dev, $m, 'facultatif');
				$date = mysql_result($query_nb_dev, $m, 'date');
				$annee = substr($date,0,4);
				$mois =  substr($date,5,2);
				$jour =  substr($date,8,2);
				$display_date_s_dev[$i][$m] = $jour."/".$mois."/".$annee;

				$m++;
			}
			if($nom_sous_cont[$i]!=""){
				$cellule_nom_sous_cont=$nom_sous_cont[$i];
			}
			else{
				$cellule_nom_sous_cont="&nbsp;";
			}
			if ($nb_dev_s_cont[$i] != 0) { echo "<th class=cn colspan='$nb_dev_s_cont[$i]' valign='top'><center>$cellule_nom_sous_cont</center></th>\n";}
		}
		if($nom_sous_cont[$i]!=""){
			echo "<td class=cn valign='top'><center><b>$nom_sous_cont[$i]</b><br />\n";
		}
		else{
			echo "<td class=cn valign='top'><center><b>&nbsp;</b><br />\n";
		}
		if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 3){
			echo "<a href=\"./add_modif_conteneur.php?mode_navig=retour_saisie&amp;id_conteneur=$id_sous_cont[$i]&amp;id_retour=$id_conteneur\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">Configuration</a><br />\n";
		}

		echo "<a href=\"./saisie_notes.php?id_conteneur=$id_sous_cont[$i]\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">Visualisation</a>\n";
		if ($display_bulletin_sous_cont[$i] == '1') { echo "<br /><font color='red'>Aff.&nbsp;bull.</font>\n";}
		echo "</center></td>\n";
		$i++;
	}
}
// En mode saisie, on n'affiche que le devoir à saisir
if (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) and ($id_devoir==0)){
	if($nom_conteneur!=""){
		echo "<td class=cn  valign='top'><center><b>$nom_conteneur</b><br />";
	}
	else{
		echo "<td class=cn  valign='top'><center><b>&nbsp;</b><br />";
	}
	echo "<a href=\"./add_modif_conteneur.php?mode_navig=retour_saisie&amp;id_conteneur=$id_conteneur&amp;id_retour=$id_conteneur\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">Configuration</a><br /><br /><font color='red'>Aff.&nbsp;bull.</font></center></td>\n";
}

echo "</tr>\n";

// Deuxième ligne
echo "<tr>\n";
echo "<td class=cn valign='top'>&nbsp;</td>\n";
$header_pdf[] = "Evaluation :";
if ($multiclasses) {$header_pdf[] = "";}
$w_pdf[] = $w1;
if ($multiclasses) {echo "<td class='cn'>&nbsp;</td>\n";}
if ($multiclasses) {$w_pdf[] = $w2;}
$i = 0;
while ($i < $nb_dev) {
	// En mode saisie, on n'affiche que le devoir à saisir
	if (($id_devoir==0) or ($id_dev[$i] == $id_devoir)) {
		if ($coef[$i] != 0) {$tmp = " bgcolor = $couleur_calcul_moy ";} else {$tmp = '';}
		$header_pdf[] = traite_accents_utf8($nom_dev[$i]." (".$display_date[$i].")");
		$w_pdf[] = $w2;
		if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) {
			echo "<td class=cn".$tmp." valign='top'><center><b><a href=\"./add_modif_dev.php?mode_navig=retour_saisie&amp;id_retour=$id_conteneur&amp;id_devoir=$id_dev[$i]\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">$nom_dev[$i]</a></b><br /><font size=-2>($display_date[$i])</font>\n";
			if($display_parents[$i]!=0) {
				echo " <img src='../images/icons/visible.png' width='19' height='16' title='Evaluation visible sur le relevé de notes' alt='Evaluation visible sur le relevé de notes' />\n";
			}
			else {
				echo " <img src='../images/icons/invisible.png' width='19' height='16' title='Evaluation non visible sur le relevé de notes' alt='Evaluation non visible sur le relevé de notes' />\n";
			}
			echo "</center></td>\n";
		}
		else {
			echo "<td class=cn".$tmp." valign='top'><center><b>".$nom_dev[$i]."</b><br /><font size=-2>($display_date[$i])</font>\n";
			if($display_parents[$i]!=0) {
				echo " <img src='../images/icons/visible.png' width='19' height='16' title='Evaluation visible sur le relevé de notes' alt='Evaluation visible sur le relevé de notes' />\n";
			}
			else {
				echo " <img src='../images/icons/invisible.png' width='19' height='16' title='Evaluation non visible sur le relevé de notes' alt='Evaluation non visible sur le relevé de notes' />\n";
			}
			echo "</center></td>\n";
		}

		if ((($nocomment[$i]!='yes') and ($_SESSION['affiche_comment'] == 'yes')) or ($id_dev[$i] == $id_devoir)) {
			echo "<td class=cn  valign='top'><center>Commentaire&nbsp;*\n";
			echo "</center></td>\n";
			$header_pdf[] = "Commentaire";
			$w_pdf[] = $w3;
		}
	}
	$i++;
}

// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir==0) {
	$i=0;
	while ($i < $nb_sous_cont) {
		$tmp = '';
		if ($_SESSION['affiche_tous'] == 'yes') {
			$m = 0;
			while ($m < $nb_dev_s_cont[$i]) {
				$tmp = '';
				if (($mode == 1) and ($coef_s_dev[$i][$m] != 0)) $tmp = " bgcolor = $couleur_calcul_moy ";
				$header_pdf[] = traite_accents_utf8($nom_sous_dev[$i][$m]." (".$display_date_s_dev[$i][$m].")");
				$w_pdf[] = $w2;
				echo "<td class=cn".$tmp." valign='top'><center><b><a href=\"./add_modif_dev.php?mode_navig=retour_saisie&amp;id_retour=$id_conteneur&amp;id_devoir=".$id_s_dev[$i][$m]."\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">".$nom_sous_dev[$i][$m]."</a></b><br /><font size=-2>(".$display_date_s_dev[$i][$m].")</font></center></td>\n";
				$m++;
			}
			$tmp = '';
			if (($mode == 2) and ($coef_sous_cont[$i] != 0)) $tmp = " bgcolor = $couleur_calcul_moy ";
		}
		echo "<td class=cn".$tmp." valign='top'><center>Moyenne</center></td>\n";
		$header_pdf[] = traite_accents_utf8("Moyenne : ".$nom_sous_cont[$i]);
		$w_pdf[] = $w2;
		$i++;
	}
}
// En mode saisie, on n'affiche que le devoir à saisir
if ($id_devoir==0) {
	echo "<td class=cn valign='top'><center><b>Moyenne</b>\n";
	if((isset($id_conteneur))&&($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2)) {
		echo "<br /><a href='".$_SERVER['PHP_SELF']."?id_conteneur=$id_conteneur&amp;recalculer=y".add_token_in_url()."'>Recalculer</a>";
	}
	echo "</center></td>\n";
	$header_pdf[] = "Moyenne";
	$w_pdf[] = $w2;
}
echo "</tr>";

//
// Troisième ligne
//
echo "<tr><td class=cn valign='top'>&nbsp;</td>";
if ($multiclasses) {echo "<td class='cn'>&nbsp;</td>";}
echo "\n";
$i = 0;
while ($i < $nb_dev) {
	// En mode saisie, on n'affiche que le devoir à saisir
	if (($id_devoir==0) or ($id_dev[$i] == $id_devoir)) {
		if ($id_dev[$i] == $id_devoir) {
			echo "<td class=cn valign='top'><center><a href=\"saisie_notes.php?id_conteneur=$id_conteneur&amp;id_devoir=0\" onclick=\"return confirm_abandon (this, change,'$themessage')\">Visualiser</a></center></td>\n";
			echo "<td class=cn valign='top'>&nbsp;</td>\n";
		} else {
			if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) {
				echo "<td class=cn valign='top'><center><a href=\"saisie_notes.php?id_conteneur=$id_conteneur&amp;id_devoir=$id_dev[$i]\" onclick=\"return confirm_abandon (this, change,'$themessage')\">saisir</a></center></td>\n";
			}
			else {
				echo "<td class=cn valign='top'>&nbsp;</td>\n";
			}
			if (($nocomment[$i]!='yes')  and ($_SESSION['affiche_comment'] == 'yes')) {echo "<td class=cn valign='top'>&nbsp;</td>\n";}
		}
	}
	$i++;
}
// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir==0) {
	$i=0;
	while ($i < $nb_sous_cont) {
		if ($_SESSION['affiche_tous'] == 'yes') {
			$m = 0;
			while ($m < $nb_dev_s_cont[$i]) {
				echo "<td class=cn valign='top'><center><a href=\"saisie_notes.php?id_conteneur=".$id_sous_cont[$i]."&amp;id_devoir=".$id_s_dev[$i][$m]."\" onclick=\"return confirm_abandon (this, change,'$themessage')\">saisir</a></center></td>\n";
				$m++;
			}
		}
		echo "<td class=cn valign='top'><center>&nbsp;</center></td>\n";
		$i++;
	}
}
// En mode saisie, on n'affiche que le devoir à saisir
if ($id_devoir==0) {echo "<td class='cn' valign='top'>&nbsp;</td>\n";}
echo "</tr>";

// quatrième ligne

echo "<tr><td class='cn' valign='top'><b>" .
		"<a href='saisie_notes.php?id_conteneur=".$id_conteneur."&amp;id_devoir=".$id_devoir."&amp;order_by=nom' onclick=\"return confirm_abandon (this, change,'$themessage')\">Nom Prénom</a></b></td>";
if ($multiclasses) {echo "<td><a href='saisie_notes.php?id_conteneur=".$id_conteneur."&amp;id_devoir=".$id_devoir."&amp;order_by=classe' onclick=\"return confirm_abandon (this, change,'$themessage')\">Classe</a></td>";}
echo "\n";

if(getSettingValue("note_autre_que_sur_referentiel")=="V") {
	$data_pdf[0][] = traite_accents_utf8("Nom Prénom /Note sur (coef)");
} else {
	$data_pdf[0][] = traite_accents_utf8("Nom Prénom \ (coef)");
}

if ($multiclasses) {$data_pdf[0][] = "";}
$i = 0;
while ($i < $nb_dev) {
	// En mode saisie, on n'affiche que le devoir à saisir
	if (($id_devoir==0) or ($id_dev[$i] == $id_devoir)) {
		echo "<td class='cn' valign='top'>";
		if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $note_sur[$i]!=getSettingValue("referentiel_note")) {
			$data_pdf[0][] = "/".$note_sur[$i]." (".number_format($coef[$i],1, ',', ' ').")";
			if ($ramener_sur_referentiel[$i] != 'V') {
				echo "<font size=-2>Note sur ".$note_sur[$i]."<br />";
			} else {
				$tabdiv_infobulle[]=creer_div_infobulle('ramenersurReferentiel_'.$i,"","","La note est ramenée sur ".getSettingValue("referentiel_note")." pour le calcul de la moyenne","",15,0,'n','n','n','n');
				echo "<a href='#' onmouseover=\"delais_afficher_div('ramenersurReferentiel_$i','y',-150,20,1500,10,10);\"";
				echo " onmouseout=\"cacher_div('ramenersurReferentiel_".$i."');\"";
				echo ">";

				echo "<font size=-2>Note sur ".$note_sur[$i];
				echo "</a><br />";
			}
		} else {
			$data_pdf[0][] = "(".number_format($coef[$i],1, ',', ' ').")";
		}
		echo "coef : ".number_format($coef[$i],1, ',', ' ');
		if (($facultatif[$i] == 'B') or ($facultatif[$i] == 'N')) {echo "<br />Bonus";}
		echo "</center></td>\n";
		if ($id_dev[$i] == $id_devoir) {
			echo "<td class='cn' valign='top'>&nbsp;</td>\n";
			$data_pdf[0][] = "";
		} else {
			if (($nocomment[$i]!='yes') and ($_SESSION['affiche_comment'] == 'yes')) {
				echo "<td class='cn' valign='top'>&nbsp;</td>\n";
				$data_pdf[0][] = "";
			}
		}
	}
	$i++;
}

// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir==0) {
	$i=0;
	while ($i < $nb_sous_cont) {
		if ($_SESSION['affiche_tous'] == 'yes') {
			$m = 0;
			while ($m < $nb_dev_s_cont[$i]) {
				echo "<td class='cn' valign='top'>";
				if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $note_sur_s_dev[$i][$m]!=getSettingValue("referentiel_note")) {
					$data_pdf[0][] = "/".$note_sur_s_dev[$i][$m]." (".number_format($coef_s_dev[$i][$m],1, ',', ' ').")";
					if ($ramener_sur_referentiel_s_dev[$i][$m] != 'V') {
						echo "<font size=-2>Note sur ".$note_sur_s_dev[$i][$m]."<br />";
					} else {
						$tabdiv_infobulle[]=creer_div_infobulle("ramenersurReferentiel_s_dev_".$i."_".$m,"","","La note est ramenée sur ".getSettingValue("referentiel_note")." pour le calcul de la moyenne","",15,0,'n','n','n','n');
						echo "<a href='#' onmouseover=\"delais_afficher_div('ramenersurReferentiel_s_dev_".$i."_".$m."','y',-150,20,1500,10,10);\"";
						echo " onmouseout=\"cacher_div('ramenersurReferentiel_s_dev_".$i."_".$m."');\"";
						echo ">";
						echo "<font size=-2>Note sur ".$note_sur_s_dev[$i][$m];
						echo "</a><br />";
					}
				} else {
					$data_pdf[0][] = "(".number_format($coef_s_dev[$i][$m],1, ',', ' ').")";
				}


				echo "<center>coef : ".number_format($coef_s_dev[$i][$m],1, ',', ' ');
				if (($fac_s_dev[$i][$m] == 'B') or ($fac_s_dev[$i][$m] == 'N')) echo "<br />Bonus";
				echo "</center></td>\n";
				$m++;
			}
		}
		if ($mode==2) {
			echo "<td class='cn' valign='top'><center>coef : ".number_format($coef_sous_cont[$i],1, ',', ' ')."</center></td>\n";
			$data_pdf[0][] = number_format($coef_sous_cont[$i],1, ',', ' ');
		} else {
			echo "<td class='cn' valign='top'><center>&nbsp;</center></td>\n";
			$data_pdf[0][] = "";
		}
		$i++;
	}
}

// En mode saisie, on n'affiche que le devoir à saisir
if ($id_devoir==0)  {
	echo "<td class='cn' valign='top'><center><a href=\"javascript:alert('".$detail."')\">Informations</a></center></td>\n";
	$data_pdf[0][] = "";
}

echo "</tr>\n";

$graphe_largeurTotale=200;
$graphe_hauteurTotale=150;
$graphe_titre="Répartition des notes";
$graphe_taille_police=3;
$graphe_epaisseur_traits=2;
$graphe_nb_tranches=5;

$n_dev_0=0;
$n_dev_fin=$nb_dev;
if($id_devoir>0) {
	for($k=0;$k<$nb_dev;$k++) {
		if($id_dev[$k]==$id_devoir) {
			$n_dev_0=$k;
			$n_dev_fin=$k+1;
			break;
		}
	}
	echo "<tr>\n";
	echo "<td>Répartition des notes";
	echo "</td>\n";
	if ($multiclasses) {echo "<td>&nbsp;</td>\n";}
}
elseif($nb_sous_cont==0) {
	echo "<tr>\n";
	echo "<td>Répartition des notes";
	echo "</td>\n";
	if ($multiclasses) {echo "<td>&nbsp;</td>\n";}
}

if(($id_devoir>0)||($nb_sous_cont==0)) {
	for($k=$n_dev_0;$k<$n_dev_fin;$k++) {
		
		echo "<td>";
		if(isset($tab_graph[$k])) {
			$graphe_serie="";
			for($l=0;$l<count($tab_graph[$k]);$l++) {
				if($l>0) {$graphe_serie.="|";}
				$graphe_serie.=$tab_graph[$k][$l];
			}
			$titre=$nom_dev[$k];

			$texte="<div align='center'><object data='../lib/graphe_svg.php?";
			$texte.="serie=$graphe_serie";
			$texte.="&amp;note_sur_serie=$note_sur[$k]";
			$texte.="&amp;nb_tranches=$graphe_nb_tranches";
			$texte.="&amp;titre=$graphe_titre";
			$texte.="&amp;v_legend1=Notes";
			$texte.="&amp;v_legend2=Effectif";
			$texte.="&amp;largeurTotale=$graphe_largeurTotale";
			$texte.="&amp;hauteurTotale=$graphe_hauteurTotale";
			$texte.="&amp;taille_police=$graphe_taille_police";
			$texte.="&amp;epaisseur_traits=$graphe_epaisseur_traits";
			$texte.="'";
			$texte.=" width='$graphe_largeurTotale' height='$graphe_hauteurTotale'";
			$texte.=" type=\"image/svg+xml\"></object></div>\n";

			$tabdiv_infobulle[]=creer_div_infobulle('repartition_notes_'.$k,$titre,"",$texte,"",14,0,'y','y','n','n');

			echo " <a href='#' onmouseover=\"delais_afficher_div('repartition_notes_$k','y',-100,20,1500,10,10);\"";
			echo ">";
			echo "<img src='../images/icons/histogramme.png' alt='Répartition des notes' />";
			echo "</a>";
		}
		else {
			echo "&nbsp;";
		}
		echo "</td>\n";
		if(($nocomment[$k]=='no')&&($_SESSION['affiche_comment'] == 'yes')) {
			echo "<td>&nbsp;</td>\n";
		}
	}
	if($id_devoir==0) {
		// Colonne Moyenne de l'élève
		echo "<td>";
		echo " <a href='#' onmouseover=\"delais_afficher_div('repartition_notes_moyenne','y',-100,20,1500,10,10);\"";
		echo ">";
		echo "<img src='../images/icons/histogramme.png' alt='Répartition des notes' />";
		echo "</a>";
		echo "</td>\n";
	}
	echo "</tr>\n";
}
//========================

// Pour permettre d'afficher moyenne, médiane, quartiles,... en mode Visualisation du carnet de notes
$chaine_input_moy="";

//
// Affichage des lignes "elèves"
//
$alt=1;
$i = 0;
$pointer = 0;
$tot_data_pdf = 1;
$tab_ele_notes=array();
$nombre_lignes = count($current_group["eleves"][$periode_num]["list"]);
while($i < $nombre_lignes) {
	$pointer++;
	$tot_data_pdf++;
	$data_pdf[$pointer][] = traite_accents_utf8($eleve_nom[$i]." ".$eleve_prenom[$i]);
	if ($multiclasses) {
		$data_pdf[$pointer][] = traite_accents_utf8($eleve_classe[$i]);
	}
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	if ($eleve_classe[$i] != $prev_classe && $prev_classe != null && $order_by == "classe") {
		echo "<td class=cn style='border-top: 2px solid blue; text-align:left;'>\n";

		echo "$eleve_nom[$i] $eleve_prenom[$i]\n";
		echo "</td>";
		if ($multiclasses) {echo "<td style='border-top: 2px solid blue;'>$eleve_classe[$i]</td>";}
		echo "\n";
	} else {
		echo "<td class=cn style='text-align:left;'>\n";

		if($id_devoir!=0) {
			echo "<div style='float:right; width:16;'><a href=\"javascript:";
			if(isset($indice_ele_saisie[$i])) {
				echo "if(document.getElementById('n'+".$indice_ele_saisie[$i].")) {document.getElementById('n'+".$indice_ele_saisie[$i].").focus()};";
				echo "affiche_div_photo();";
			}
			else {
				echo "affiche_div_photo();";
			}
			echo "\"><img src='../images/icons/buddy.png' width='16' height='16' alt='Afficher la photo élève' title='Afficher la photo élève' /></a></div>\n";
		}

		echo "$eleve_nom[$i] $eleve_prenom[$i]\n";
		$tab_ele_notes[$i][]="";
		echo "</td>";
		if ($multiclasses) {
			echo "<td>$eleve_classe[$i]</td>";
			$tab_ele_notes[$i][]="";
		}
		echo "\n";
	}
	$prev_classe = $eleve_classe[$i];
	$k=0;
	while ($k < $nb_dev) {
		// En mode saisie, on n'affiche que le devoir à saisir
		if (($id_devoir==0) or ($id_dev[$k] == $id_devoir)) {
			echo $mess_note[$i][$k];
			$tab_ele_notes[$i][]=$mess_note_pdf[$i][$k];
			$data_pdf[$pointer][] = $mess_note_pdf[$i][$k];
			if ((($nocomment[$k]!='yes') and ($_SESSION['affiche_comment'] == 'yes')) or ($id_dev[$k] == $id_devoir)) {
				echo $mess_comment[$i][$k];
				$data_pdf[$pointer][] = traite_accents_utf8($mess_comment_pdf[$i][$k]);
				$tab_ele_notes[$i][]="";
			}
		}
		$k++;
	}

	// Affichage de la moyenne de tous les sous-conteneurs

	// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
	if ($id_devoir==0) {
		$k=0;
		while ($k < $nb_sous_cont) {
			if ($_SESSION['affiche_tous'] == 'yes') {
				$m = 0;
				while ($m < $nb_dev_s_cont[$k]) {
					$temp = $id_s_dev[$k][$m];
					$note_query = mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$eleve_login[$i]' AND id_devoir='$temp')");
					
                    if($note_query){
						$eleve_statut = @mysql_result($note_query, 0, "statut");
						$eleve_note = @mysql_result($note_query, 0, "note");
						if (($eleve_statut != '') and ($eleve_statut != 'v')) {
							$tmp = $eleve_statut;
							$data_pdf[$pointer][] = $eleve_statut;
						} else if ($eleve_statut == 'v') {
							$tmp = "&nbsp;";
							$data_pdf[$pointer][] = "";
						} else {
							if ($eleve_note != '') {
								$tmp = number_format($eleve_note,1, ',', ' ');
								$data_pdf[$pointer][] = number_format($eleve_note,1, ',', ' ');
							} else {
								$tmp = "&nbsp;";
								$data_pdf[$pointer][] = "";
							}
						}
					}
					else{
						$eleve_statut = "";
						$eleve_note = "";
						$tmp = "&nbsp;";
						$data_pdf[$pointer][] = "";
					}
					$tab_ele_notes[$i][]=$eleve_note;

					echo "<td class='cn' bgcolor='$couleur_devoirs'><center><b>$tmp</b></center></td>\n";

					$m++;
				}
			}

			$moyenne_query = mysql_query("SELECT * FROM cn_notes_conteneurs WHERE (login='$eleve_login[$i]' AND id_conteneur='$id_sous_cont[$k]')");
			
            if($moyenne_query){
				$statut_moy = @mysql_result($moyenne_query, 0, "statut");
				if ($statut_moy == 'y') {
					$moy = @mysql_result($moyenne_query, 0, "note");
					$moy = number_format($moy,1, ',', ' ');
					$data_pdf[$pointer][] = $moy;
					$tab_ele_notes[$i][]=$moy;
				} else {
					$tab_ele_notes[$i][]='';
					$moy = '&nbsp;';
					$data_pdf[$pointer][] = "";
				}
			}
			else{
				$tab_ele_notes[$i][]='';
				$statut_moy = "";
				$moy = '&nbsp;';
				$data_pdf[$pointer][] = "";
			}
			echo "<td class='cn' bgcolor='$couleur_moy_sous_cont'><center>$moy</center></td>\n";
			$k++;
		}
	}

	// affichage des moyennes du conteneur (moyennes des élèves sur le conteneur choisi (éventuellement le conteneur racine))

	// En mode saisie, on n'affiche que le devoir à saisir
	if ($id_devoir==0)  {
		$moyenne_query = mysql_query("SELECT * FROM cn_notes_conteneurs WHERE (login='$eleve_login[$i]' AND id_conteneur='$id_conteneur')");
		
        if($moyenne_query){
			$statut_moy = @mysql_result($moyenne_query, 0, "statut");
			if ($statut_moy == 'y') {
				$moy = @mysql_result($moyenne_query, 0, "note");
				$moy = number_format($moy,1, ',', ' ');
				$data_pdf[$pointer][] = $moy;
				$tab_ele_notes[$i][]=$moy;

				$tab_graph_moy[]=number_format(strtr($moy,",","."),1, '.', ' ');
		
			} else {
				$tab_ele_notes[$i][]='';
				$moy = '&nbsp;';
				$data_pdf[$pointer][] = "";
			}
		}
		else{
			$tab_ele_notes[$i][]='';
			$statut_moy = "";
			$moy = '&nbsp;';
			$data_pdf[$pointer][] = "";
		}

		echo "<td class='cn' bgcolor='$couleur_moy_cont'><center><b>$moy</b></center></td>\n";
		if($moy=='&nbsp;') {
			$chaine_input_moy.="<input type='hidden' name='n$i' id='n$i' value='' />\n";
		}
		else {
			$chaine_input_moy.="<input type='hidden' name='n$i' id='n$i' value='$moy' />\n";
		}
	}
	echo "</tr>\n";

	$i++;
}
$nombre_lignes=$i;

// AJOUT: boireaus 20080607
// Génération de l'infobulle pour $tab_graph_moy[]
if($id_devoir==0) {
	$graphe_serie="";
	if(isset($tab_graph_moy)) {
		for($l=0;$l<count($tab_graph_moy);$l++) {
			if($l>0) {$graphe_serie.="|";}
			$graphe_serie.=$tab_graph_moy[$l];
		}
	}

	$titre="Moyenne";

	$texte="<div align='center'><object data='../lib/graphe_svg.php?";
	$texte.="serie=$graphe_serie";
	$texte.="&amp;note_sur_serie=20";
	$texte.="&amp;nb_tranches=5";
	$texte.="&amp;titre=$graphe_titre";
	$texte.="&amp;v_legend1=Notes";
	$texte.="&amp;v_legend2=Effectif";
	$texte.="&amp;largeurTotale=$graphe_largeurTotale";
	$texte.="&amp;hauteurTotale=$graphe_hauteurTotale";
	$texte.="&amp;taille_police=$graphe_taille_police";
	$texte.="&amp;epaisseur_traits=$graphe_epaisseur_traits";
	$texte.="'";
	$texte.=" width='$graphe_largeurTotale' height='$graphe_hauteurTotale'";
	$texte.=" type=\"image/svg+xml\"></object></div>\n";

	$tabdiv_infobulle[]=creer_div_infobulle('repartition_notes_moyenne',$titre,"",$texte,"",14,0,'y','y','n','n');
}

// Dernière ligne

echo "<tr>";
if ($multiclasses) {
	echo "<td class=cn colspan=2>";
} else {
	echo "<td class=cn>";
}

echo "<input type='hidden' name='indice_max_log_eleve' value='$i' />\n";

echo "<b>Moyennes :</b></td>\n";
$w_pdf[] = $w2;
$data_pdf[$tot_data_pdf][] = "Moyennes";
if ($multiclasses) {$data_pdf[$tot_data_pdf][] = "";}
$k='0';
while ($k < $nb_dev) {
	// En mode saisie, on n'affiche que le devoir à saisir
	if (($id_devoir==0) or ($id_dev[$k] == $id_devoir)) {
		$call_moyenne[$k] = mysql_query("SELECT round(avg(n.note),1) moyenne FROM cn_notes_devoirs n, j_eleves_groupes j WHERE (
		j.id_groupe='$id_groupe' AND
		j.periode = '$periode_num' AND
		j.login = n.login AND
		n.statut='' AND
		n.id_devoir='$id_dev[$k]'
		)");
		$moyenne[$k] = mysql_result($call_moyenne[$k], 0, "moyenne");
		if ($moyenne[$k] != '') {
			echo "<td class='cn'><center><b>".number_format($moyenne[$k],1, ',', ' ')."</b></center></td>\n";
			$data_pdf[$tot_data_pdf][] = number_format($moyenne[$k],1, ',', ' ');

		} else {
			echo "<td class='cn'>&nbsp;</td></td>\n";
			$data_pdf[$tot_data_pdf][] = "";
		}
		if ((($nocomment[$k]!='yes') and ($_SESSION['affiche_comment'] == 'yes')) or ($id_dev[$k] == $id_devoir)) {
		echo "<td class='cn'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][] = "";
		}
	}
	$k++;
}

// Moyenne des moyennes des sous-conteneurs
//
// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir==0) {
	$k=0;
	while ($k < $nb_sous_cont) {
		if ($_SESSION['affiche_tous'] == 'yes') {
			$m = 0;
			while ($m < $nb_dev_s_cont[$k]) {
				$temp = $id_s_dev[$k][$m];
				$call_moy = mysql_query("SELECT round(avg(n.note),1) moyenne FROM cn_notes_devoirs n, j_eleves_groupes j WHERE (
				j.id_groupe='$id_groupe' AND
				j.periode = '$periode_num' AND
				j.login = n.login AND
				n.statut='' AND
				n.id_devoir='$temp'
				)");
				$moy_s_dev = mysql_result($call_moy, 0, "moyenne");
				if ($moy_s_dev != '') {
					echo "<td class='cn'><center><b>".number_format($moy_s_dev,1, ',', ' ')."</b></center></td>\n";
					$data_pdf[$tot_data_pdf][] = number_format($moy_s_dev,1, ',', ' ');
				} else {
					echo "<td class='cn'>&nbsp;</td>\n";
					$data_pdf[$tot_data_pdf][] = "";
				}
				$m++;
			}
		}
		$call_moy_moy = mysql_query("SELECT round(avg(n.note),1) moyenne FROM cn_notes_conteneurs n, j_eleves_groupes j WHERE (
		j.id_groupe='$id_groupe' AND
		j.login = n.login AND
		j.periode = '$periode_num' AND
		n.statut='y' AND
		n.id_conteneur='$id_sous_cont[$k]'
		)");
		$moy_moy = mysql_result($call_moy_moy, 0, "moyenne");
		if ($moy_moy != '') {
			echo "<td class='cn'><center><b>".number_format($moy_moy,1, ',', ' ')."</b></center></td>\n";
			$data_pdf[$tot_data_pdf][] = number_format($moy_moy,1, ',', ' ');
		} else {
			echo "<td class='cn'>&nbsp;</td>\n";
			$data_pdf[$tot_data_pdf][] = "";
		}
		$k++;
	}
}

// Moyenne des moyennes du conteneur
//
// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir==0) {

	$call_moy_moy = mysql_query("SELECT round(avg(n.note),1) moyenne FROM cn_notes_conteneurs n, j_eleves_groupes j WHERE (
	j.id_groupe='$id_groupe' AND
	j.login = n.login AND
	j.periode = '$periode_num' AND
	n.statut='y' AND
	n.id_conteneur='$id_conteneur'
	)");
	$moy_moy = mysql_result($call_moy_moy, 0, "moyenne");
	if ($moy_moy != '') {
		echo "<td class='cn'><center><b>".number_format($moy_moy,1, ',', ' ')."</b></center></td>\n";
		$data_pdf[$tot_data_pdf][] = number_format($moy_moy,1, ',', ' ');
	} else {
		echo "<td class='cn'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][] = "";
	}
}
echo "</tr>\n";

//echo "<pre>".print_r($tab_ele_notes)."</pre>";
$indice_premiere_col_note=1;
if ($multiclasses) {$indice_premiere_col_note=2;}
$tab_col_note=array();
for($i=0;$i<count($tab_ele_notes);$i++) {
	for($j=0;$j<count($tab_ele_notes[$i]);$j++) {
		$tab_col_note[$j][$i]=$tab_ele_notes[$i][$j];
	}
}

for($i=0;$i<count($tab_col_note);$i++) {
	$tab_m[$i]=calcule_moy_mediane_quartiles($tab_col_note[$i]);
}

if(getPref($_SESSION['login'], 'cn_avec_min_max', 'y')=='y') {
	$tot_data_pdf++;
	$data_pdf[$tot_data_pdf][]='Min.:';
	echo "<tr>\n";
	echo "<td class='cn bold'><b>Min.&nbsp;:</b></td>\n";
	if ($multiclasses) {
		echo "<td class='cn bold'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][]='';
	}
	for($i=$indice_premiere_col_note;$i<count($tab_m);$i++) {
		echo "<td class='cn bold'>".$tab_m[$i]['min']."</td>\n";
		$data_pdf[$tot_data_pdf][]=$tab_m[$i]['min'];
	}
	echo "</tr>\n";
	
	$tot_data_pdf++;
	$data_pdf[$tot_data_pdf][]='Max.:';
	echo "<tr>\n";
	echo "<td class='cn bold'><b>Max.&nbsp;:</b></td>\n";
	if ($multiclasses) {
		echo "<td class='cn bold'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][]='';
	}
	for($i=$indice_premiere_col_note;$i<count($tab_m);$i++) {
		echo "<td class='cn bold'>".$tab_m[$i]['max']."</td>\n";
		$data_pdf[$tot_data_pdf][]=$tab_m[$i]['max'];
	}
	echo "</tr>\n";
}

if(getPref($_SESSION['login'], 'cn_avec_mediane_q1_q3', 'y')=='y') {
	$tot_data_pdf++;
	$data_pdf[$tot_data_pdf][]='Médianes :';
	echo "<tr>\n";
	echo "<td class='cn bold'><b>Médianes&nbsp;:</b></td>\n";
	if ($multiclasses) {
		echo "<td class='cn bold'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][]='';
	}
	for($i=$indice_premiere_col_note;$i<count($tab_m);$i++) {
		echo "<td class='cn bold'>".$tab_m[$i]['mediane']."</td>\n";
		$data_pdf[$tot_data_pdf][]=$tab_m[$i]['mediane'];
	}
	echo "</tr>\n";
	
	$tot_data_pdf++;
	$data_pdf[$tot_data_pdf][]='1er quartile :';
	echo "<tr>\n";
	echo "<td class='cn bold'><b>1er quartile&nbsp;:</b></td>\n";
	if ($multiclasses) {
		echo "<td class='cn bold'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][]='';
	}
	for($i=$indice_premiere_col_note;$i<count($tab_m);$i++) {
		echo "<td class='cn bold'>".$tab_m[$i]['q1']."</td>\n";
		$data_pdf[$tot_data_pdf][]=$tab_m[$i]['q1'];
	}
	echo "</tr>\n";
	
	$tot_data_pdf++;
	$data_pdf[$tot_data_pdf][]='3è quartile :';
	echo "<tr>\n";
	echo "<td class='cn bold'><b>3è quartile&nbsp;:</b></td>\n";
	if ($multiclasses) {
		echo "<td class='cn bold'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][]='';
	}
	for($i=$indice_premiere_col_note;$i<count($tab_m);$i++) {
		echo "<td class='cn bold'>".$tab_m[$i]['q3']."</td>\n";
		$data_pdf[$tot_data_pdf][]=$tab_m[$i]['q3'];
	}
	echo "</tr>\n";
}
echo "</table>\n";

if((isset($id_devoir))&&($id_devoir!=0)) {
	echo "<div id='div_q_p' style='position: fixed; top: 220px; right: 200px; text-align:center;'>\n";
		echo "<div id='div_quartiles' style='text-align:center; display:none;'>\n";
		javascript_tab_stat('tab_stat_',$num_id);
		echo "</div>\n";

		echo "<br />\n";

		echo "<div id='div_photo_eleve' style='text-align:center; display:none;'></div>\n";
	echo "</div>\n";

	echo "<script type='text/javascript'>
	function affiche_div_photo() {
		if(document.getElementById('div_photo_eleve').style.display=='none') {
			document.getElementById('div_photo_eleve').style.display='';
		}
		else {
			document.getElementById('div_photo_eleve').style.display='none';
		}
	}

	function affiche_photo(photo,nom_prenom) {
 		document.getElementById('div_photo_eleve').innerHTML='<img src=\"'+photo+'\" width=\"150\" alt=\"Photo\" /><br />'+nom_prenom;
	}
</script>\n";
}
//===================================

// Préparation du pdf
$header_pdf=serialize($header_pdf);
$_SESSION['header_pdf']=$header_pdf;

$w_pdf=serialize($w_pdf);
$_SESSION['w_pdf']=$w_pdf;

$data_pdf=serialize($data_pdf);
$_SESSION['data_pdf']=$data_pdf;

if ($id_devoir) echo "<input type='hidden' name='is_posted' value=\"yes\" />\n";

?>

<input type="hidden" name="id_conteneur" value="<?php echo "$id_conteneur";?>" />
<input type="hidden" name="id_devoir" value="<?php echo "$id_devoir";?>" />
<?php if ($id_devoir != 0) echo "<br /><center><div id=\"fixe\"><input type='submit' value='Enregistrer' /></div></center>\n"; ?>
</form>
<?php

// Affichage des quartiles flottants

if((!isset($id_devoir))||($id_devoir=='')||($id_devoir=='0')) {
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='form_calcul_mediane'>\n";
	echo $chaine_input_moy;
	echo "</form>\n";

	echo "<div id='div_q_p' style='position: fixed; top: 220px; right: 200px; text-align:center;'>\n";
		echo "<div id='div_quartiles' style='text-align:center; display:none;'>\n";
		javascript_tab_stat('tab_stat_',$nombre_lignes);
		echo "</div>\n";
	echo "</div>\n";
}

if ($id_devoir) {

	$chaine_indices="";
	for($i=0;$i<$nombre_lignes;$i++) {
		if(isset($indice_ele_saisie[$i])) {
			if($chaine_indices!="") {$chaine_indices.=",";}
			$chaine_indices.=$indice_ele_saisie[$i];
		}
	}

	echo "<p id='p_tri'></p>\n";
	echo "<script type='text/javascript'>
	function affiche_lien_tri() {
		var tab_indices=new Array($chaine_indices);

		chaine1='';
		chaine2='';
		for(i=0;i<$nombre_lignes;i++) {
			//num=eval(10+i);
			num=tab_indices[i];

			if(document.getElementById('n'+num)) {
				if(chaine1!='') {chaine1=chaine1+'|';chaine2=chaine2+'|';}
				//if(chaine2!='') {chaine2=chaine2+'|';}

				chaine1=chaine1+document.getElementById('log_eleve_'+i).value;
				chaine2=chaine2+document.getElementById('n'+num).value;
			}
		}
		//alert(chaine1);
		//alert(chaine2);
		document.getElementById('p_tri').innerHTML='<a href=\'affiche_tri.php?titre=Notes&chaine1='+chaine1+'&chaine2='+chaine2+'\' onclick=\"effectuer_tri(); afficher_div(\'div_tri\',\'y\',-150,20); return false;\" target=\'_blank\'>Afficher les notes triées</a>';
	}

	function effectuer_tri() {
		var tab_indices=new Array($chaine_indices);

		chaine1='';
		chaine2='';
		for(i=0;i<$nombre_lignes;i++) {
			//num=eval(10+i);
			num=tab_indices[i];

			if(document.getElementById('n'+num)) {
				if(chaine1!='') {chaine1=chaine1+'|';chaine2=chaine2+'|';}
				//if(chaine2!='') {chaine2=chaine2+'|';}

				chaine1=chaine1+document.getElementById('log_eleve_'+i).value;
				chaine2=chaine2+document.getElementById('n'+num).value;
			}
		}

		new Ajax.Updater($('notes_triees'),'affiche_tri.php?titre=Notes&chaine1='+chaine1+'&chaine2='+chaine2+'".add_token_in_url(false)."',{method: 'get'});
	}

	affiche_lien_tri();
</script>\n";
	$titre_infobulle="Notes triées";
	$texte_infobulle="<div id='notes_triees'></div>";
	$tabdiv_infobulle[]=creer_div_infobulle('div_tri',$titre_infobulle,"",$texte_infobulle,"",30,0,'y','y','n','n');


	echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;\">\n";
	echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=post>\n";
	echo add_token_field();
	echo "<h3 class='gepi'>Importation directe des notes par copier/coller à partir d'un tableur</h3>\n";
	echo "<table summary=\"Tableau d'import\"><tr>\n";
	echo "<td>De la ligne : ";
		echo "<SELECT name='debut_import' size='1'>\n";
	$k = 1;
	while ($k < $current_displayed_line+1) {
		echo "<option value='$k'>$k</option>\n";
		$k++;
	}
	echo "</select>\n";

	echo "<br /> à la ligne : \n";
	echo "<SELECT name='fin_import' size='1'>\n";
	$k = 1;
	while ($k < $current_displayed_line+1) {
		echo "<option value='$k'";
		if ($k == $current_displayed_line) echo " SELECTED ";
		echo ">$k</option>\n";
		$k++;
	}
	echo "</select>\n";
	echo "</td><td>\n";
	echo "Coller ci-dessous les données à importer : <br />\n";
	if (isset($_POST['notes'])) {$notes=preg_replace("/\\\\n/","\n",preg_replace("/\\\\r/","\r",$_POST['notes']));} else {$notes='';}
	//echo "<textarea name='notes' rows='3' cols='40' wrap='virtual'>$notes</textarea>\n";
	echo "<textarea name='notes' rows='3' cols='40' class='wrap'>$notes</textarea>\n";
	echo "</td></tr></table>\n";

	echo "<input type='hidden' name='id_conteneur' value='$id_conteneur' />\n";
	echo "<input type='hidden' name='id_devoir' value='$id_devoir' />\n";

	echo "<input type='hidden' name='order_by' value='$order_by' />\n";

	echo "<center><input type='submit' value='Importer'  onclick=\"return confirm_abandon (this, change, '$themessage')\" /></center>\n";
	echo "<p><b>Remarque importante :</b> l'importation ne prend en compte que les élèves dont le nom est affiché ci-dessus !<br />Soyez donc vigilant à ne coller que les notes de ces élèves, dans le bon ordre.</p>\n";
	echo "</form></fieldset>\n";

	if (isset($_POST['notes'])) {
		echo "<script type=\"text/javascript\" language=\"javascript\">
		<!--
		alert(\"Attention, les notes importées ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton 'Enregistrer') !\");
		changement();
		//-->
		</script>\n";
	}

  if (isset($_POST['appreciations'])) {
  	echo "<script type=\"text/javascript\" language=\"javascript\">
  	<!--
  	alert(\"Attention, les appréciations importées ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton 'Enregistrer') !\");
  	//-->
  	</script>\n";
  }
}

if ($id_devoir) {
	echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;\">\n";
	echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=post>\n";
	echo add_token_field();
	echo "<h3 class='gepi'>Importation directe des appréciations par copier/coller à partir d'un tableur</h3>\n";
	echo "<table summary=\"Tableau d'import\"><tr>\n";
	echo "<td>De la ligne : ";
		echo "<SELECT name='debut_import' size='1'>\n";
	$k = 1;
	while ($k < $current_displayed_line+1) {
		echo "<option value='$k'>$k</option>\n";
		$k++;
	}
	echo "</select>\n";

	echo "<br /> à la ligne : \n";
	echo "<SELECT name='fin_import' size='1'>\n";
	$k = 1;
	while ($k < $current_displayed_line+1) {
		echo "<option value='$k'";
		if ($k == $current_displayed_line) echo " SELECTED ";
		echo ">$k</option>\n";
		$k++;
	}
	echo "</select>\n";
	echo "</td><td>\n";
	echo "Coller ci-dessous les données à importer&nbsp;: <br />\n";
	if (isset($_POST['appreciations'])) {$appreciations = preg_replace("/\\\\n/","\n",preg_replace("/\\\\r/","\r",$_POST['appreciations']));} else {$appreciations='';}
	echo "<textarea name='appreciations' rows='3' cols='40' class='wrap'>$appreciations</textarea>\n";
	echo "</td></tr></table>\n";
	echo "<input type='hidden' name='id_conteneur' value='$id_conteneur' />\n";
	echo "<input type='hidden' name='id_devoir' value='$id_devoir' />\n";
	echo "<input type='hidden' name='order_by' value='$order_by' />\n";
	echo "<center><input type='submit' value='Importer'  onclick=\"return confirm_abandon (this, change, '$themessage')\" /></center>\n";
	echo "<p><b>Remarque importante :</b> l'importation ne prend en compte que les élèves dont le nom est affiché ci-dessus !<br />Soyez donc vigilant à ne coller que les appréciations de ces élèves, dans le bon ordre.</p>\n";
	echo "</form></fieldset>\n";
}

// Pour qu'un professeur puisse avoir une préférence d'affichage par défaut ou non des quartiles:
$aff_quartiles_par_defaut=getPref($_SESSION['login'],'aff_quartiles_cn',"n");
$aff_photo_cn_par_defaut=getPref($_SESSION['login'],'aff_photo_cn',"n");

?>
<br />
* En conformité avec la CNIL, le professeur s'engage à ne faire figurer dans le carnet de notes que des notes et commentaires portés à la connaissance de l'élève (note et commentaire portés sur la copie, ...).
<script type="text/javascript" language="javascript">
chargement = true;

// La vérification ci-dessous est effectuée après le remplacement des notes supérieures à 20 par des zéros.
// Ces éventuelles erreurs de frappe ne sauteront pas aux yeux.
for(i=10;i<<?php echo $num_id; ?>;i++){
	eval("verifcol("+i+")");
}

// On donne le focus à la première cellule lors du chargement de la page:
if(document.getElementById('n10')){
	document.getElementById('n10').focus();
}

function affichage_quartiles() {
	if(document.getElementById('div_quartiles').style.display=='none') {
		document.getElementById('div_quartiles').style.display='';
	}
	else {
		document.getElementById('div_quartiles').style.display='none';
	}
}
<?php
if($aff_quartiles_par_defaut=='y') {
	echo "affichage_quartiles();\n";
}
if($aff_photo_cn_par_defaut=='y') {
	echo "affiche_div_photo();\n";
}
?>
</script>
<?php 
/**
 * Pied de page
 */
require("../lib/footer.inc.php");
?>
