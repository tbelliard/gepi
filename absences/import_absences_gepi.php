<?php
@set_time_limit(0);
/*
 * $Id: import_absences_gepi.php 6615 2011-03-03 17:47:06Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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
if(getSettingValue("active_module_absence")==='2'){
    require_once("../lib/initialisationsPropel.inc.php");
}
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

//Configuration du calendrier
include("../lib/calendrier/calendrier.class.php");
$cal_1 = new Calendrier("form_absences", "du");
$cal_2 = new Calendrier("form_absences", "au");

// variable définie
    $date_ce_jour = date('d/m/Y'); 
	$erreur = '';

// variable
 if (empty($_GET['action_sql']) and empty($_POST['action_sql'])) { $action_sql = ''; }
   else { if (isset($_GET['action_sql'])) { $action_sql = $_GET['action_sql'];} if (isset($_POST['action_sql'])) { $action_sql = $_POST['action_sql']; } }
 if (empty($_GET['etape']) and empty($_POST['etape'])) { $etape = '0'; }
   else { if (isset($_GET['etape'])) { $etape = $_GET['etape']; } if (isset($_POST['etape'])) {$etape=$_POST['etape'];} }
 if (empty($_GET['id_classe']) and empty($_POST['id_classe'])) { $id_classe = ''; }
   else { if (isset($_GET['id_classe'])) { $id_classe = $_GET['id_classe']; } if (isset($_POST['id_classe'])) { $id_classe = $_POST['id_classe']; } }
 if (empty($_GET['periode_num']) and empty($_POST['periode_num'])) { $periode_num = ''; }
   else { if (isset($_GET['periode_num'])) { $periode_num = $_GET['periode_num']; } if (isset($_POST['periode_num'])) { $periode_num = $_POST['periode_num']; } }

// gestion des dates
	if (empty($_GET['du']) and empty($_POST['du'])) {
		if(isset($_SESSION['import_absences_du'])) {
			$du = $_SESSION['import_absences_du'];
		}
		else {
			//$du = $date_ce_jour;
			// On met le début de l'année... ça ne conviendra que pour la première période, mais bon...
			$annee = strftime("%Y");
			$mois = strftime("%m");
			$jour = strftime("%d");
			if($mois>7) {$du="01/09/$annee";} else {$du="01/09/".($annee-1);}
		}
	}
	else { 
		if (isset($_GET['du'])) { 
			$du = $_GET['du']; 
		} 
		if (isset($_POST['du'])) { 
			$du = $_POST['du']; 
		} 
		$_SESSION['import_absences_du']=$du;
	}

	if (empty($_GET['au']) and empty($_POST['au'])) {
		if(isset($_SESSION['import_absences_au'])) {
			$au = $_SESSION['import_absences_au'];
		}
		else {
			//$au = 'JJ/MM/AAAA';
			$au = $date_ce_jour;
		}
	}
	else { 
		if (isset($_GET['au'])) {
			$au=$_GET['au'];
		} 
		if (isset($_POST['au'])) {
			$au=$_POST['au'];
		} 
		$_SESSION['import_absences_au']=$au;
	}
 if (getSettingValue("active_module_absence") === '2') {
    $date_absence_eleve_debut = isset($_POST["du"]) ? $_POST["du"] : (isset($_GET["du"]) ? $_GET["du"] : NULL);
    $date_absence_eleve_fin = isset($_POST["au"]) ? $_POST["au"] : (isset($_GET["au"]) ? $_GET["au"] : NULL);
    if ($date_absence_eleve_debut != null) {
        if (isset($_SESSION['import_absences_du'])) {
            $date_absence_eleve_debut = new DateTime(str_replace("/", ".",$_SESSION['import_absences_du']));
        } else {
            $date_absence_eleve_debut = new DateTime(str_replace("/", ".", $date_absence_eleve_debut));
        }
    } else {
        $date_absence_eleve_debut = new DateTime('now');
        $date_absence_eleve_debut->setDate($date_absence_eleve_debut->format('Y'), $date_absence_eleve_debut->format('m') - 1, $date_absence_eleve_debut->format('d'));
    }
    if ($date_absence_eleve_fin != null) {
        if (isset($_SESSION['import_absences_au'])) {
            $date_absence_eleve_fin = new DateTime(str_replace("/", ".",$_SESSION['import_absences_au']));
        } else {
        $date_absence_eleve_fin = new DateTime(str_replace("/", ".", $date_absence_eleve_fin));
        }
    } else {
        $date_absence_eleve_fin = new DateTime('now');
    }
    $date_absence_eleve_debut->setTime(0, 0, 0);
    $date_absence_eleve_fin->setTime(23, 59, 59);
}

// fonction de sécurité
// uid de pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post===$_SESSION['uid_prime']
 if(empty($_SESSION['uid_prime'])) { $_SESSION['uid_prime']=''; }
 if (empty($_GET['uid_post']) and empty($_POST['uid_post'])) {$uid_post='';}
    else { if (isset($_GET['uid_post'])) {$uid_post=$_GET['uid_post'];} if (isset($_POST['uid_post'])) {$uid_post=$_POST['uid_post'];} }
	$uid = md5(uniqid(microtime(), 1));
	$valide_form='';
	   // on remplace les %20 par des espaces
	    $uid_post = preg_replace('/%20/',' ',$uid_post);
	if($uid_post===$_SESSION['uid_prime']) { $valide_form = 'yes'; } else { $valide_form = 'no'; }
	$_SESSION['uid_prime'] = $uid;
// fin de la fonction de sécurité

include "../lib/periodes.inc.php";
include "../mod_absences/lib/functions.php";

//===========================================================
$acces="n";
if($ver_periode[$periode_num]=="N") {
	$acces="y";
}
elseif(($ver_periode[$periode_num]=="P")&&($_SESSION['statut']=='secours')) {
	$acces="y";
}

if($acces=="n") {
	$msg="La période $periode_num est close pour cette classe.";
	header("Location:index.php?id_classe=$id_classe&msg=$msg");
}
//===========================================================

//**************** EN-TETE *****************
$titre_page = "Outil d'importation des absences du module d'absence de GEPI";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var();
?>

<script type="text/javascript" language="javascript">
<!--
function getDate(input_pass,form_choix){
 var date_select=new Date();
 var jour=date_select.getDate(); if(jour<10){jour="0"+jour;}
 var mois=date_select.getMonth()+1; if(mois<10){mois="0"+mois;}
 var annee=date_select.getFullYear();
 var date_jour = jour+"/"+mois+"/"+annee;
// nom du formulaire
  var form_action = form_choix;
// id des élèments
  var input_pass_id = input_pass.id;
  var input_pass_value = input_pass.value;
// modifie le contenue de l'élèment
if(document.forms[form_action].elements[input_pass_id].value=='JJ/MM/AAAA' || document.forms[form_action].elements[input_pass_id].value=='') { document.forms[form_action].elements[input_pass_id].value=date_jour; }
}
 // -->
</script>

<div class="bold"><a href="index.php?id_classe=<?php echo $id_classe; ?>"><img src="../images/icons/back.png" alt="Retour" title="Retour" class="back_link" />&nbsp;Retour</a></div>

<?php
if ( $etape === '0' ) {
// etape de présentation et de demande d'information
	if ( $periode_num != '' ) {
	// si une période est bien sélectionner alors on demande la date de début et de fin de cette période

		$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
		$classe = mysql_result($call_classe, "0", "classe");
?>

<div style="text-align: center;">
   <form method="post" action="import_absences_gepi.php" name="form_absences">
<?php
		echo add_token_field();
?>
      <fieldset style="width: 450px; margin: auto;" class="couleur_ligne_3">
         <legend style="font: normal 10pt Arial;">&nbsp;Sélection&nbsp;</legend>
            <div style="color: #E8F1F4; text-align: left; font: normal 12pt verdana, sans-serif; font-weight: bold; background-image: url(../mod_absences/images/haut_tab.png); border: 0px solid #F8F8F8;">Importation des absences</div>
            <div style="text-align: center; color: #330033; font: normal 10pt Arial;">
		Pour la classe de <?php echo "$classe"; ?><br /><br />
		Définissez les dates de début et de fin pour la période du <?php echo $nom_periode[$periode_num]; ?><br />
                du <input name="du" type="text" size="11" maxlength="11" value="<?php echo $du; ?>" /><a href="#calend" onClick="<?php  echo $cal_1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
		au <input name="au" id="au" type="text" size="11" maxlength="11" value="<?php echo $au; ?>" onClick="getDate(au,'form_absences')" /><a href="#calend" onClick="<?php  echo $cal_2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
                <input type="hidden" name="id_classe" value="<?php echo $id_classe; ?>" />
                <input type="hidden" name="etape" value="1" />
                <input type="hidden" name="periode_num" value="<?php echo $periode_num; ?>" /><br /><br />
                <input type="submit" name="Submit32" value="Importer" /><br />
            </div>
            <div style="text-align: center; color: #FF0000; font: normal 10pt Arial;">
		Attention, vous allez importer les absences<br />gérées par le module absence de GEPI
	    </div>
      </fieldset>
    </form>
</div>
<?php

	} else { echo 'Vous n\'avez sélectionné aucune période. Il vous est donc impossible d\'importer les données'; }

}

if ( $etape === '1' ) {
	check_token(false);

 // affiché le résultats

	 // si la date au et vide ou alors erroné alors on prend du
	if ( empty($au) or verif_date(date_sql($au)) === 'erreur' ) { $au = $du; }

?>

	<form enctype="multipart/form-data" action="saisie_absences.php" method=post>
	<?php
		echo add_token_field();

		$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '".$id_classe."'");
		$classe = mysql_result($call_classe, "0", "classe");
	?>

<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center;">

	<span style="font: normal small-caps normal 14pt Verdana; line-height: 125%;">Classe de <?php echo "$classe"; ?> - Importation des absences de GEPI</span>
	<br />pour le : <b><?php $temp = strtolower($nom_periode[$periode_num]); echo "$temp"; ?></b>
	<br /><br />


	<span style="font-weight: bold; color: #FF0000;">Attention</span>
	<ul><li style="color: #FF0000;">l'importation sera terminée quand vous aurez cliqué sur "Enregistrer".</li>
	<li style="color: #FF0000;">Après avoir cliqué sur "Enregistrer" ces données écraseront les données qui auraient déja été saisies pour cette période.</li></ul>

	<table style="margin: auto; border: 0px; background: #088CB9; color: #E0EDF1; text-align: center;" cellspacing="1" cellpadding="0">
	<tr>
	    <td style="text-align: center; padding-left: 2px; padding-right: 2px; font-weight: bold; color: #FFFFFF; padding-left: 2px; padding-right: 2px; font-size: 10px;">Nom Prénom</td>
	    <td style="text-align: center; padding-left: 2px; padding-right: 2px; font-weight: bold; color: #FFFFFF; padding-left: 2px; padding-right: 2px; font-size: 10px;">Nb. total de 1/2 journées d'absence</td>
	    <td style="text-align: center; padding-left: 2px; padding-right: 2px; font-weight: bold; color: #FFFFFF; padding-left: 2px; padding-right: 2px; font-size: 10px;">Nb. absences non justifiées</td>
	    <td style="text-align: center; padding-left: 2px; padding-right: 2px; font-weight: bold; color: #FFFFFF; padding-left: 2px; padding-right: 2px; font-size: 10px;">Nb. de retard</td>
	    <td style="text-align: center; padding-left: 2px; padding-right: 2px; font-weight: bold; color: #FFFFFF; padding-left: 2px; padding-right: 2px; font-size: 10px;">Observations</td>
	</tr>

	<?php
	if ($_SESSION['statut'] == "cpe") {
	        $appel_donnees_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c, j_eleves_cpe j WHERE (c.id_classe='$id_classe' AND j.e_login = c.login AND e.login = j.e_login AND j.cpe_login = '".$_SESSION['login'] . "' AND c.periode = '$periode_num') order by e.nom, e.prenom");
	    } else {
        	$appel_donnees_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c WHERE ( c.id_classe='$id_classe' AND c.login = e.login AND c.periode='$periode_num') order by e.nom, e.prenom");
	}

	$nombre_lignes = mysql_num_rows($appel_donnees_eleves);
	$i = '0';
	$num_id=10;
	while($i < $nombre_lignes) {
	    $current_eleve_login = mysql_result($appel_donnees_eleves, $i, "login");
        if(getSettingValue("active_module_absence")==='2'){
            $eleve = EleveQuery::create()->findOneByLogin($current_eleve_login);
        }
	    $current_eleve_absences_query = mysql_query("SELECT * FROM  absences WHERE (login='$current_eleve_login' AND periode='$periode_num')");
        if(getSettingValue("active_module_absence")==='2'){
            $current_eleve_nb_absences = strval($eleve->getDemiJourneesAbsence($date_absence_eleve_debut, $date_absence_eleve_fin)->count());
        }else{
	    $current_eleve_nb_absences = nb_total_demijournee_absence($current_eleve_login, $du, $au, $id_classe);
        }
	    if ( $current_eleve_nb_absences == '0' ) { $current_eleve_nb_absences = ''; }
        if(getSettingValue("active_module_absence")==='2'){
            $current_eleve_nb_nj=strval($eleve->getDemiJourneesNonJustifieesAbsence($date_absence_eleve_debut, $date_absence_eleve_fin)->count());
        }else{
	    $current_eleve_nb_nj = nb_absences_nj($current_eleve_login, $du, $au, $id_classe);
        }
	    if ( $current_eleve_nb_nj == '0' ) { $current_eleve_nb_nj = ''; }
        if(getSettingValue("active_module_absence")==='2'){
            $current_eleve_nb_retards=strval($eleve->getRetards($date_absence_eleve_debut, $date_absence_eleve_fin)->count());
        }else{
	    $current_eleve_nb_retards = nb_retard($current_eleve_login, $du, $au, $id_classe);
        }
	    if ( $current_eleve_nb_retards == '0' ) { $current_eleve_nb_retards = ''; }

	    $current_eleve_ap_absences = @mysql_result($current_eleve_absences_query, 0, "appreciation");
	    $current_eleve_nom = mysql_result($appel_donnees_eleves, $i, "nom");
	    $current_eleve_prenom = mysql_result($appel_donnees_eleves, $i, "prenom");

	    $current_eleve_login_nb = $current_eleve_login."_nb_abs";
	    $current_eleve_login_nj = $current_eleve_login."_nb_nj";
	    $current_eleve_login_retard = $current_eleve_login."_nb_retard";

	    $current_eleve_login_ap = $current_eleve_login."_ap"; ?>

	<tr>
		<td style="background: #B7DDFF;"><input type="hidden" name="log_eleve[<?php echo $i; ?>]" value="<?php echo $current_eleve_login; ?>" /><?php echo $current_eleve_nom.' '.$current_eleve_prenom; ?></td>
	    	<td style="background: #B7DDFF;"><input id="i<?php echo $num_id; ?>" onKeyDown="clavier(this.id,event);" type="text" size="4" name="nb_abs_ele[<?php echo $i; ?>]" value="<?php echo $current_eleve_nb_absences; ?>" onchange="changement()" /></td>
	    	<td style="background: #B7DDFF;"><input id="i1<?php echo $num_id; ?>" onKeyDown="clavier(this.id,event);" type="text" size="4" name="nb_nj_ele[<?php echo $i; ?>]" value="<?php echo $current_eleve_nb_nj; ?>" onchange="changement()" /></td>
	    	<td style="background: #B7DDFF;"><input id="i2<?php echo $num_id; ?>" onKeyDown="clavier(this.id,event);" type="text" size="4" name="nb_retard_ele[<?php echo $i; ?>]" value="<?php echo $current_eleve_nb_retards; ?>" onchange="changement()" /></td>
	    	<td style="background: #B7DDFF;"><textarea id="i3<?php echo $num_id; ?>" onKeyDown="clavier(this.id,event);" onchange="changement()" name="no_anti_inject_app_eleve_<?php echo $i; ?>" rows="2" cols="50" wrap="virtual"><?php echo $current_eleve_ap_absences; ?></textarea></td>
	</tr>

	<?php $i++; $num_id++;
	} ?>
	</table>

	<input type="hidden" name="is_posted" value="yes" />
	<input type="hidden" name="id_classe" value=<?php echo "$id_classe";?> />
	<input type="hidden" name="periode_num" value=<?php echo "$periode_num";?> />
	<center><div id="fixe"><input type="submit" value="Enregistrer" /></div></center>
	</form>

<?php /* fin div de centrage du tableau pour ie5 */ ?>
</div>

<?php
}

require("../lib/footer.inc.php");

?>
