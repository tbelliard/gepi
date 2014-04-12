<?php
/*
 * $Id: index.php 7393 2011-07-05 17:58:38Z mleygnac $
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$action = isset($_POST["action"]) ? $_POST["action"] :(isset($_GET["action"]) ? $_GET["action"] :NULL);

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Dans le cas ou on poste un message, pas de traitement anti_inject
// Pour ne pas interférer avec fckeditor
if ((isset($action)) and ($action == 'message') and (isset($_POST['message'])) and isset($_POST['ok']))
	$traite_anti_inject = 'no';

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

include("../ckeditor/ckeditor.php") ;

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(($_SESSION['statut']=='cpe')&&(!getSettingAOui('GepiAccesPanneauAffichageCpe'))) {
	header("Location: ../accueil.php?msg=Acces non autorisé");
	die();
}

//Configuration du calendrier
/*
include("../lib/calendrier/calendrier.class.php");
$cal1 = new Calendrier("formulaire", "display_date_debut");
$cal2 = new Calendrier("formulaire", "display_date_fin");
$cal3 = new Calendrier("formulaire", "display_date_decompte");
*/
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

// initialisation de $id_mess
$id_mess = isset($_POST["id_mess"]) ? $_POST["id_mess"] :(isset($_GET["id_mess"]) ? $_GET["id_mess"] :NULL);

$order_by = isset($_POST["order_by"]) ? $_POST["order_by"] :(isset($_GET["order_by"]) ? $_GET["order_by"] :"date_debut");
if ($order_by != "date_debut" and $order_by != "date_fin" and $order_by != "id") {
	$order_by = "date_debut";
}

/*
function formate_date_decompte($date_decompte) {
	//$tmp_date=getdate($date_decompte);
	return strftime("%d/%m/%Y à %H:%M",$date_decompte);
}
*/

function ajout_bouton_supprimer_message($contenu_cor,$id_message)
	{
	$contenu_cor='
	<form method="POST" action="accueil.php" name="f_suppression_message">
	<input type="hidden" name="supprimer_message" value="'.$id_message.'">
	<button type="submit" title=" Supprimer ce message " style="border: none; background: none; float: right;"><img style="vertical-align: bottom;" src="images/icons/delete.png"></button>
	</form>'.$contenu_cor;
	$r_sql="UPDATE messages SET texte='".$contenu_cor."' WHERE id='".$id_message."'";
	return mysqli_query($GLOBALS["mysqli"], $r_sql)?true:false;
	}

//function update_message($contenu_cor,$date_debut,$date_fin,$date_decompte,$statuts_destinataires,$login_destinataire,$matiere_destinataire)
function update_message($contenu_cor,$date_debut,$date_fin,$date_decompte,$statuts_destinataires,$login_destinataire)
	{
	$r_sql = "UPDATE messages
	SET texte = '".$contenu_cor."',
	date_debut = '".$date_debut."',
	date_fin = '".$date_fin."',
	date_decompte = '".$date_decompte."',
	auteur='".$_SESSION['login']."',
	statuts_destinataires = '".$statuts_destinataires."',
	login_destinataire='".$login_destinataire."'
	WHERE id ='".$_POST['id_mess']."'";
	//", matiere_destinataire='".$matiere_destinataire."'";
	return mysqli_query($GLOBALS["mysqli"], $r_sql)?true:false;
	}

//function set_message($contenu_cor,$date_debut,$date_fin,$date_decompte,$statuts_destinataires,$login_destinataire,$matiere_destinataire)
function set_message($contenu_cor,$date_debut,$date_fin,$date_decompte,$statuts_destinataires,$login_destinataire)
	{
	$r_sql = "INSERT INTO messages
	SET texte = '".$contenu_cor."',
	date_debut = '".$date_debut."',
	date_fin = '".$date_fin."',
	date_decompte = '".$date_decompte."',
	auteur='".$_SESSION['login']."',
	statuts_destinataires = '".$statuts_destinataires."',
	login_destinataire='".$login_destinataire."'";
	//$r_sql.=", matiere_destinataire='".$matiere_destinataire."'";
	$retour=mysqli_query($GLOBALS["mysqli"], $r_sql)?true:false;
	if ($retour)
		{
		$id_message=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
		if (isset($_POST['suppression_possible']) && $_POST['suppression_possible']=="oui" &&  $statuts_destinataires=="_")
			$retour=ajout_bouton_supprimer_message($contenu_cor,$id_message);
		}
	return $retour;
	}

// initialisation des notifications
$msg_erreur=""; $msg_OK="";

//
// Purge des messages
//
if (isset($_POST['purger']))
	{
	check_token();
	//$r_sql="DELETE FROM messages WHERE date_fin+86400 <= ".mktime(0,0,0,date("m"),date("d"),date("Y"));
	$r_sql="DELETE FROM messages WHERE date_fin+86400 <= ".time();
	if (!mysqli_query($GLOBALS["mysqli"], $r_sql)) $msg_erreur="Erreur lors de la purge des messages&nbsp;: ".mysqli_error($GLOBALS["mysqli"]);
	else	{
			$msg_OK="Purge effectuée. ";
			if (mysqli_affected_rows($GLOBALS["mysqli"])==0) $msg_OK.="Aucun message supprimé.";
				else $msg_OK.="Nombre de message(s) supprimé(s)&nbsp;: ".mysqli_affected_rows($GLOBALS["mysqli"]);
			}
	}

//
// Suppression d'un message
//
if ((isset($action)) and ($action == 'sup_entry')) {
	check_token();
	$res = sql_query("delete from messages where id = '".$_GET['id_del']."'");
	if ($res) $msg_OK = "Suppression réussie";
}

//
// Annulation des modifs
//
if ((isset($action)) and ($action == 'message') and (isset($_POST['cancel']))) {
	unset ($id_mess);
}


//
// Insertion ou modification d'un message
//
if ((isset($action)) and ($action == 'message') and (isset($_POST['message'])) and isset($_POST['ok'])) {
	check_token();
	$record = 'yes';
	$contenu_cor = traitement_magic_quotes(corriger_caracteres($_POST['message']));
	//$contenu_cor = html_entity_decode($_POST['message']);

	$statuts_destinataires = '_';
	if (isset($_POST['desti_s'])) $statuts_destinataires .= 's';
	if (isset($_POST['desti_p'])) $statuts_destinataires .= 'p';
	if (isset($_POST['desti_c'])) $statuts_destinataires .= 'c';
	if (isset($_POST['desti_a'])) $statuts_destinataires .= 'a';
	if (isset($_POST['desti_r'])) $statuts_destinataires .= 'r';
	if (isset($_POST['desti_e'])) $statuts_destinataires .= 'e';

	if ($statuts_destinataires=="_" && $_POST['id_classe']=="" && $_POST['login_destinataire']=="" && $_POST['matiere_destinataire']=="" && $_POST['eleves_id_classe']=="" && $_POST['parents_id_classe']=="") {
		$msg_erreur = "ATTENTION : aucun destinataire saisi.<br />(message non enregitré)";
		$record = 'no';
	}

	if ($contenu_cor == '') {
		$msg_erreur = "ATTENTION : aucun texte saisi.<br />(message non enregitré)";
		$record = 'no';
	}

	if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['display_date_debut'])) {
		$anneed = mb_substr($_POST['display_date_debut'],6,4);
		$moisd = mb_substr($_POST['display_date_debut'],3,2);
		$jourd = mb_substr($_POST['display_date_debut'],0,2);
		while ((!checkdate($moisd, $jourd, $anneed)) and ($jourd > 0)) $jourd--;
		$date_debut=mktime(0,0,0,$moisd,$jourd,$anneed);
	} else {
		$msg_erreur = "ATTENTION : La date de début d'affichage n'est pas valide.<br />(message non enregitré)";
		$record = 'no';
	}

	if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['display_date_fin'])) {
		$anneef = mb_substr($_POST['display_date_fin'],6,4);
		$moisf = mb_substr($_POST['display_date_fin'],3,2);
		$jourf = mb_substr($_POST['display_date_fin'],0,2);
		while ((!checkdate($moisf, $jourf, $anneef)) and ($jourf > 0)) $jourf--;
		$date_fin=mktime(23,59,0,$moisf,$jourf,$anneef);
	} else {
		$msg_erreur = "ATTENTION : La date de fin d'affichage n'est pas valide.<br />(message non enregitré)";
		$record = 'no';
	}

	if ($record == 'yes') {
		if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['display_date_decompte'])) {
			$anneed = mb_substr($_POST['display_date_decompte'],6,4);
			$moisd = mb_substr($_POST['display_date_decompte'],3,2);
			$jourd = mb_substr($_POST['display_date_decompte'],0,2);
			//echo "$jourd/$moisd/$anneed<br />";
			while ((!checkdate($moisd, $jourd, $anneed)) and ($jourd > 0)) {
				$jourd--;
				//echo "$jourd/$moisd/$anneed<br />";
			}
			$date_decompte=mktime(0,0,0,$moisd,$jourd,$anneed);
			//echo strftime("%d/%m/%Y à %H:%M",$date_decompte)."<br />";

			if (preg_match("/([0-9]{1,2}):([0-9]{0,2})/", str_ireplace('h',':',$_POST['display_heure_decompte']))) {
				$heured = mb_substr($_POST['display_heure_decompte'],0,2);
				$minuted = mb_substr($_POST['display_heure_decompte'],3,2);
				$date_decompte=$date_decompte+$heured*3600+$minuted*60;
				//echo strftime("%d/%m/%Y à %H:%M",$date_decompte)."<br />";
			} else {
				$msg_erreur = "ATTENTION : L'heure de décompte n'est pas valide.<br />(message non enregitré)";
				$record = 'no';
			}
		} else {
			$msg_erreur = "ATTENTION : La date de décompte n'est pas valide.<br />(message non enregitré)";
			$record = 'no';
		}
	}

	// par sécurité les rédacteurs d'un message ne peuvent y insérer la variable _CRSF_ALEA_
	$pos_crsf_alea=strpos($contenu_cor,"_CRSF_ALEA_");
	if($pos_crsf_alea!==false)
		{
		$contenu_cor=preg_replace("/_CRSF_ALEA_/","",$contenu_cor);
		$msg_erreur = "Contenu interdit.";
		$record = 'no';
		}

	// tableau des utilisateurs destinataires
	$login_destinataire="";
	//$matiere_destinataire="";
	$t_login_destinataires=array();
		// un destinataire
		if ($_POST['login_destinataire']<>"") 
			{$t_login_destinataires[]=$_POST['login_destinataire'];$login_destinataire=$_POST['login_destinataire'];}

		// les professeurs d'une classe
		if ($_POST['id_classe']<>"")
			{
			$id_classe=$_POST['id_classe'];
			$r_sql="SELECT DISTINCT utilisateurs.login FROM j_groupes_classes,groupes,j_groupes_professeurs,utilisateurs WHERE j_groupes_classes.id_classe='".$id_classe."' AND j_groupes_classes.id_groupe=groupes.id AND groupes.id=j_groupes_professeurs.id_groupe AND j_groupes_professeurs.login=utilisateurs.login";
			$R_professeurs=mysqli_query($GLOBALS["mysqli"], $r_sql);
			while ($un_professeur=mysqli_fetch_assoc($R_professeurs))
				if(!in_array($un_professeur['login'], $t_login_destinataires)) {
					$t_login_destinataires[]=$un_professeur['login'];
				}
			}

		// les professeurs d'une matière
		if ($_POST['matiere_destinataire']<>"")
			{
			$matiere_destinataire=$_POST['matiere_destinataire'];
			$r_sql="SELECT DISTINCT u.login FROM j_groupes_matieres jgm, j_groupes_professeurs jgp, utilisateurs u WHERE jgm.id_groupe=jgp.id_groupe AND jgp.login=u.login AND jgm.id_matiere='".$matiere_destinataire."';";
			//echo "$r_sql<br />";
			$R_professeurs=mysqli_query($GLOBALS["mysqli"], $r_sql);
			while ($un_professeur=mysqli_fetch_assoc($R_professeurs))
				if(!in_array($un_professeur['login'], $t_login_destinataires)) {
					$t_login_destinataires[]=$un_professeur['login'];
				}
			}

		// les élèves d'une classe
		if ($_POST['eleves_id_classe']<>"")
			{
			$eleves_id_classe=$_POST['eleves_id_classe'];
			$r_sql="SELECT DISTINCT u.login FROM j_eleves_classes jec, 
									utilisateurs u 
								WHERE jec.id_classe='".$eleves_id_classe."' AND 
								jec.login=u.login";
			$R_eleves=mysqli_query($GLOBALS["mysqli"], $r_sql);
			while ($un_eleve=mysqli_fetch_assoc($R_eleves))
				if(!in_array($un_eleve['login'], $t_login_destinataires)) {
					$t_login_destinataires[]=$un_eleve['login'];
				}
			}

		// les responsables élèves d'une classe
		if ($_POST['parents_id_classe']<>"")
			{
			$parents_id_classe=$_POST['parents_id_classe'];
			$r_sql="SELECT DISTINCT u.login FROM j_eleves_classes jec, 
									eleves e,
									responsables2 r,
									resp_pers rp,
									utilisateurs u 
								WHERE jec.id_classe='".$parents_id_classe."' AND 
								jec.login=e.login AND
								e.ele_id=r.ele_id AND
								r.pers_id=rp.pers_id AND
								rp.login=u.login";
			$R_parents=mysqli_query($GLOBALS["mysqli"], $r_sql);
			while ($un_parent=mysqli_fetch_assoc($R_parents))
				if(!in_array($un_parent['login'], $t_login_destinataires)) {
					$t_login_destinataires[]=$un_parent['login'];
				}
			}

	// on enregistre le message
	if ($record == 'yes') {
		$erreur=false;

		/*
		echo "\$date_debut=$date_debut<br />";
		echo "\$date_fin=$date_fin<br />";
		echo "\$date_decompte=$date_decompte<br />";
		*/

		if (count($t_login_destinataires)==0)
			if (isset($_POST['id_mess']))
				$erreur=!update_message($contenu_cor,$date_debut,$date_fin,$date_decompte,$statuts_destinataires,"");
			else
				$erreur=!set_message($contenu_cor,$date_debut,$date_fin,$date_decompte,$statuts_destinataires,"");
		else
			{
			// pour éviter qu'un utilisateur de satut donné voit n fois le message adressé aux profs d'une classe 
			if (count($t_login_destinataires)>1) $statuts_destinataires="_";
			foreach($t_login_destinataires as $login_destinataire)
				if (isset($_POST['id_mess']))
					$erreur=!update_message($contenu_cor,$date_debut,$date_fin,$date_decompte,$statuts_destinataires,$login_destinataire) && $erreur;
				else
					$erreur=!set_message($contenu_cor,$date_debut,$date_fin,$date_decompte,$statuts_destinataires,$login_destinataire) && $erreur;
			}

		if (!$erreur) {
			$msg_OK = "Le message a été enregistré.";
			unset($contenu_cor);
			unset($_POST['display_date_debut']);
			unset($_POST['display_date_fin']);
			unset($_POST['display_date_decompte']);
			unset($id_mess);
			unset($statuts_destinataires);
			unset($login_destinataire);
			//unset($matiere_destinataire);
			unset($id_classe);
			unset($eleves_id_classe);
			unset($parents_id_classe);
		} else {
			$msg_erreur = "Erreur lors de l'enregistrement du message&nbsp;: <br  />".mysqli_error($GLOBALS["mysqli"]);
		}
	}
}


$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_suppression = "Confirmation de suppression";
//**************** EN-TETE *****************
$titre_page = "Gestion des messages";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();
//onclick=\"return confirm_abandon (this, change, '$themessage')\"
echo "<a name=\"debut_de_page\"></a>";

//debug_var();
echo "<div style='color: #FF0000; text-align: center; padding: 0.5%;'>";
if ($msg_erreur!="") echo "<p style='color: #FF0000; font-variant: small-caps;'>".$msg_erreur."</p>";
if ($msg_OK!="") echo "<p style='color: #0000FF; font-variant: small-caps;'>".$msg_OK."</p>";
echo "</div>";


echo "<script type=\"text/javascript\" language=\"JavaScript\" SRC=\"../lib/clock_fr.js\"></SCRIPT>\n";
//-----------------------------------------------------------------------------------
echo "<p class='bold'><a href='../accueil.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Nouveau message</a>";
if(acces("/classes/dates_classes.php", $_SESSION['statut'])) {
	echo " | <a href='../classes/dates_classes.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Nouvel événement classe</a>";
}
"</p>\n";
echo "<table width=\"98%\" cellspacing=0 align=\"center\">\n";
echo "<tr>\n";
echo "<td valign='top'>\n";
echo "<p>Nous sommes le&nbsp;:&nbsp;\n";
echo "<script type=\"text/javascript\" language=\"javascript\">\n";
echo "<!--\n";
echo "new LiveClock();\n";
echo "//-->\n";
echo "</SCRIPT></p>\n";
echo "</td>\n";

echo "</tr></table><hr />\n";

echo "<table  border = \"0\" cellpadding=\"10\">\n";
echo "<tr>";
echo "<td width = \"350px\" valign=\"top\">\n";

echo "<span class='grand'>Purge des messages</span><br />\n";
echo "<p>La purge des messages consiste à supprimer tous les messages dont la date de fin d'affichage est antérieure de plus de 24 h. à la date actuelle.</p>";
echo "<form align=\"center\" action=\"./index.php\" method=\"post\" style=\"width: 100%;\">\n";
echo add_token_field();
echo "<p align=\"center\"><input type=\"submit\" name=\"purger\" value=\" Purger les messages \"></p>";
echo "</form>";
echo "<br /><br />";
//
// Affichage des messages éditables
//

$appel_messages = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM messages WHERE texte <> '' AND statuts_destinataires <> '_'  AND login_destinataire='' order by ".$order_by." DESC");
$nb_messages = mysqli_num_rows($appel_messages);

if ($nb_messages>0) {
	echo "<span class='grand' title=\"Seuls les messages destinés à tel ou tel statut peuvent être modifiés.
Les messages destinés à une classe, une matière ou un individu ne peuvent pas être modifiés après coup.\">Messages pouvant être modifiés&nbsp;:</span><br />\n";
	echo "<span class='small'>Classer par : ";
	echo "<a href='index.php?order_by=date_debut' onclick=\"return confirm_abandon (this, change, '$themessage')\">date début</a> | <a href='index.php?order_by=date_fin' onclick=\"return confirm_abandon (this, change, '$themessage')\">date fin</a> | <a href='index.php?order_by=id' onclick=\"return confirm_abandon (this, change, '$themessage')\">date création</a>\n";
	echo "</span><br /><br />\n";
	$ind = 0;
	while ($ind < $nb_messages) {
	  $content = old_mysql_result($appel_messages, $ind, 'texte');
	  // Mise en forme du texte
	  $date_debut1 = old_mysql_result($appel_messages, $ind, 'date_debut');
	  $date_fin1 = old_mysql_result($appel_messages, $ind, 'date_fin');
	  $date_decompte1 = old_mysql_result($appel_messages, $ind, 'date_decompte');
	  $auteur1 = old_mysql_result($appel_messages, $ind, 'auteur');
	  $statuts_destinataires1 = old_mysql_result($appel_messages, $ind, 'statuts_destinataires');
	  $login_destinataire1=old_mysql_result($appel_messages, $ind, 'login_destinataire');
	  //$matiere_destinataire1=old_mysql_result($appel_messages, $ind, 'matiere_destinataire');
	//  $nom_auteur = sql_query1("SELECT nom from utilisateurs where login = '".$auteur1."'");
	//  $prenom_auteur = sql_query1("SELECT prenom from utilisateurs where login = '".$auteur1."'");


	  $id_message =  old_mysql_result($appel_messages, $ind, 'id');


	//  echo "<b><i>Message de </i></b>: ".$prenom_auteur." ".$nom_auteur.";
		echo "<b><i>Affichage </i></b>: du <b>".strftime("%a %d %b %Y", $date_debut1)."</b> au <b>".strftime("%a %d %b %Y", $date_fin1)."</b>\n";
		if(strstr($content,'_DECOMPTE_')) {
		//echo "<br />Avec décompte des jours jusqu'au ".formate_date_decompte($date_decompte1);
		echo "<br />Avec décompte des jours jusqu'au ".strftime("%d/%m/%Y à %H:%M",$date_decompte1);
		}
		echo "<br /><b><i>Statut(s) destinataire(s) </i></b> : ";
		/*
		if (strpos($statuts_destinataires1, "p")) echo "professeurs - ";
		if (strpos($statuts_destinataires1, "c")) echo "c.p.e. - ";
		if (strpos($statuts_destinataires1, "s")) echo "scolarité - ";
		if (strpos($statuts_destinataires1, "a")) echo "administrateurs - ";
		if (strpos($statuts_destinataires1, "e")) echo "élèves - ";
		*/
		$chaine_statuts_destinataires="";
		if (strpos($statuts_destinataires1, "p")) {
			$chaine_statuts_destinataires.="professeurs";
		}
		if (strpos($statuts_destinataires1, "c")){
			if($chaine_statuts_destinataires!="") {$chaine_statuts_destinataires.=" - ";}
			$chaine_statuts_destinataires.="c.p.e.";
		}
		if (strpos($statuts_destinataires1, "s")) {
			if($chaine_statuts_destinataires!="") {$chaine_statuts_destinataires.=" - ";}
			$chaine_statuts_destinataires.="scolarité";
		}
		if (strpos($statuts_destinataires1, "a")) {
			if($chaine_statuts_destinataires!="") {$chaine_statuts_destinataires.=" - ";}
			$chaine_statuts_destinataires.="administrateurs";
		}
		if (strpos($statuts_destinataires1, "e")) {
			if($chaine_statuts_destinataires!="") {$chaine_statuts_destinataires.=" - ";}
			$chaine_statuts_destinataires.="élèves";
		}
		if (strpos($statuts_destinataires1, "r")) {
			if($chaine_statuts_destinataires!="") {$chaine_statuts_destinataires.=" - ";}
			$chaine_statuts_destinataires.="responsables";
		}
		echo $chaine_statuts_destinataires;
		//echo "<br /><b><i>Login du destinataire </i></b> : ".$login_destinataire1;
		echo "<br /><a href='index.php?id_mess=$id_message' onclick=\"return confirm_abandon (this, change, '$themessage')\">modifier</a>
		- <a href='index.php?id_del=$id_message&action=sup_entry".add_token_in_url()."' onclick=\"return confirmlink(this, 'Etes-vous sûr de vouloir supprimer ce message ?', '".$message_suppression."')\">supprimer</a>
		<table border=0 width = '100%' cellpadding='5'><tr><td style=\"border:1px solid black\">".$content."</td></tr></table><br />\n";
		$ind++;
	}
}

// Fin de la colonne de gauche
echo "</td>\n";


// Début de la colonne de droite
echo "<td valign=\"top\">\n";

//
// Affichage du message en modification
//
if (isset($id_mess)) {
	$titre_mess = "Modification d'un message";
	$appel_message = mysqli_query($GLOBALS["mysqli"], "SELECT  id, texte, date_debut, date_fin, date_decompte, auteur, statuts_destinataires, login_destinataire  FROM messages
	WHERE (id = '".$id_mess."')");
	$contenu = old_mysql_result($appel_message, 0, 'texte');
	$date_debut = old_mysql_result($appel_message, 0, 'date_debut');
	$date_fin = old_mysql_result($appel_message, 0, 'date_fin');
	$date_decompte = old_mysql_result($appel_message, 0, 'date_decompte');
	$statuts_destinataires = old_mysql_result($appel_message, 0, 'statuts_destinataires');
	$login_destinataire=old_mysql_result($appel_message, 0, 'login_destinataire');
	//$matiere_destinataire=old_mysql_result($appel_message, 0, 'matiere_destinataire');
	$display_date_debut = strftime("%d", $date_debut)."/".strftime("%m", $date_debut)."/".strftime("%Y", $date_debut);
	$display_date_fin = strftime("%d", $date_fin)."/".strftime("%m", $date_fin)."/".strftime("%Y", $date_fin);
	$display_date_decompte = strftime("%d", $date_decompte)."/".strftime("%m", $date_decompte)."/".strftime("%Y", $date_decompte);

	// Récupération du nombre de secondes
	$tmp_sec_decompte=$date_decompte-mktime(0,0,0,strftime("%m", $date_decompte),strftime("%d", $date_decompte),strftime("%Y", $date_decompte));
	$tmp_heure_decompte=floor($tmp_sec_decompte/3600);
	$tmp_minute_decompte=floor(($tmp_sec_decompte-3600*$tmp_heure_decompte)/60);
	$display_heure_decompte=sprintf("%02d",$tmp_heure_decompte).":".sprintf("%02d",$tmp_minute_decompte);
} else {
	$titre_mess = "Nouveau message";
	if (isset($contenu_cor)) $contenu = $contenu_cor ; else $contenu = '';
	//if (isset($_POST['display_date_debut']) and isset($_POST['display_date_fin'])) {
	if (isset($_POST['display_date_debut']) and isset($_POST['display_date_fin']) and isset($_POST['display_date_decompte'])) {
		$display_date_debut = $_POST['display_date_debut'];
		$display_date_fin = $_POST['display_date_fin'];
		$display_date_decompte = $_POST['display_date_decompte'];
	} else {
		$annee = strftime("%Y");
		$mois = strftime("%m");
		$jour = strftime("%d");
		$display_date_debut = $jour."/".$mois."/".$annee;
		$annee = strftime("%Y",time()+86400);
		$mois = strftime("%m",time()+86400);
		$jour = strftime("%d",time()+86400);
		$display_date_fin = $jour."/".$mois."/".$annee;
		$display_date_decompte = $display_date_fin;
	}
	$display_heure_decompte=isset($_POST['display_heure_decompte']) ? $_POST['display_heure_decompte'] : "08:00";
	if (!isset($statuts_destinataires)) $statuts_destinataires = '_';

}
echo "<table style=\"border:1px solid black\" cellpadding=\"5\" cellspacing=\"0\"><tr><td>\n";
echo "<form action=\"./index.php#debut_de_page\" method=\"post\" style=\"width: 100%;\" name=\"formulaire\">\n";
echo "<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>\n";
echo add_token_field();
if (isset($id_mess)) echo "<input type=\"hidden\" name=\"id_mess\" value=\"$id_mess\" />\n";
echo "<input type=\"hidden\" name=\"action\" value=\"message\" />\n";


echo "<table border=\"0\" width = \"100%\" cellspacing=\"1\" cellpadding=\"1\" >\n";

// Aide
$titre_infobulle="AIDE\n";
$texte_infobulle="Un message peut être adressé à :<br />- tous les utilisateurs ayant le(s) même(s) statut(s) ;<br />- ou un utilisateur particulier ;<br />- ou tous les professeurs enseignant dans une même classe.<br /><br />Attention : seuls les messages adressés uniquement à des utilisateurs de même(s) statut(s) peuvent être modifiés après enregistrement.<br /><br />\n";
//$texte_infobulle.="\n";
$tabdiv_infobulle[]=creer_div_infobulle('aide',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

// Titre
echo "<tr><td colspan=\"4\"><span class='grand'>".$titre_mess." ";
echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('aide','y',100,100);\"  onmouseout=\"cacher_div('aide');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";
echo "</span></td></tr>\n";


//Dates
echo "<tr><td colspan=\"4\">\n";
echo "<p><i>Le message sera affiché :</i><br />de la date : ";
echo "<input type='text' name = 'display_date_debut' id= 'display_date_debut' size='10' value = \"".$display_date_debut."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";

//echo "<a href=\"#\" onClick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Calendrier\" /></a>\n";
echo img_calendrier_js("display_date_debut", "img_bouton_display_date_debut");

echo "&nbsp;à la date : ";
echo "<input type='text' name = 'display_date_fin' id = 'display_date_fin' size='10' value = \"".$display_date_fin."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
//echo "<a href=\"#\" onClick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Calendrier\" /></a>\n";
echo img_calendrier_js("display_date_fin", "img_bouton_display_date_fin");

echo "<br />(<span style='font-size:small'>Respectez le format jj/mm/aaaa</span>)</p></td></tr>\n";

//Date pour décompte
echo "<tr><td colspan=\"4\">\n";
echo "<p><i>Décompte des jours jusqu'au :</i> ";
echo "<input type='text' name = 'display_date_decompte' id= 'display_date_decompte' size='10' value = \"".$display_date_decompte."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
//echo "<a href=\"#\" onClick=\"".$cal3->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Calendrier\" /></a>\n";
echo img_calendrier_js("display_date_decompte", "img_bouton_display_date_decompte");

echo " à <input type='text' name = 'display_heure_decompte' id= 'display_heure_decompte' size='5' value = \"".$display_heure_decompte."\" onKeyDown=\"clavier_heure(this.id,event);\" AutoComplete=\"off\" />\n";
echo "<br />(<span style='font-size:small'>Respectez le format jj/mm/aaaa</span>)<br />Saisir une chaine <b>_DECOMPTE_</b> dans le corps du message pour que cette date soit prise en compte.\n";

$titre_infobulle="DECOMPTE\n";
$texte_infobulle="Afin d'afficher un compte à rebours, vous devez écrire un texte du style&nbsp;:<br />Il vous reste _DECOMPTE_ pour saisir vos appréciations du 1er trimestre.<br />\n";
//$texte_infobulle.="\n";
$tabdiv_infobulle[]=creer_div_infobulle('a_propos_DECOMPTE',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_DECOMPTE','y',100,100);\"  onmouseout=\"cacher_div('a_propos_DECOMPTE');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";

echo "</p>";

echo "</td></tr>\n";

//Destinataires
echo "<tr><td  colspan=\"4\"><i>Statut(s) des destinataires du message :</i></td></tr>\n";
echo "<tr>\n";
echo "<td><input type=\"checkbox\" id=\"desti_p\" name=\"desti_p\" value=\"desti_p\"";
if (strpos($statuts_destinataires, "p")) {echo "checked";}
echo " onchange='check_et_acces_champ_suppression_message()'";
echo " /><label for='desti_p' style='cursor: pointer;'>Professeurs</label></td>\n";

echo "<td><input type=\"checkbox\" id=\"desti_c\" name=\"desti_c\" value=\"desti_c\"";
if (strpos($statuts_destinataires, "c")) {echo "checked";}
echo " onchange='check_et_acces_champ_suppression_message()'";
echo " /><label for='desti_c' style='cursor: pointer;'>C.P.E.</label></td>\n";

echo "<td><input type=\"checkbox\" id=\"desti_s\" name=\"desti_s\" value=\"desti_s\"";
if (strpos($statuts_destinataires, "s")) {echo "checked";}
echo " onchange='check_et_acces_champ_suppression_message()'";
echo " /><label for='desti_s' style='cursor: pointer;'>Scolarité</label></td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td><input type=\"checkbox\" id=\"desti_a\" name=\"desti_a\" value=\"desti_a\"";
if (strpos($statuts_destinataires, "a")) {echo "checked";}
echo " onchange='check_et_acces_champ_suppression_message()'";
echo " /><label for='desti_a' style='cursor: pointer;'>Administrateur</label></td>\n";

echo "<td><input type=\"checkbox\" id=\"desti_r\" name=\"desti_r\" value=\"desti_r\"";
if (strpos($statuts_destinataires, "r")) {echo "checked";}
echo " onchange='check_et_acces_champ_suppression_message()'";
echo " /><label for='desti_r' style='cursor: pointer;'>Responsables</label></td>\n";

echo "<td><input type=\"checkbox\" id=\"desti_e\" name=\"desti_e\" value=\"desti_e\"";
if (strpos($statuts_destinataires, "e")) {echo "checked";}
echo " onchange='check_et_acces_champ_suppression_message()'";
echo " /><label for='desti_e' style='cursor: pointer;'>Elèves</label></td>\n";

echo "</tr>\n";


echo "<tr><td  colspan=\"4\" >\n";
?>
<br>
<i>Destinataire du message&nbsp;:&nbsp;</i><br />
	<select name="login_destinataire" style="margin-left: 20px; max-width: 500px; width: 300px;">
		<optgroup>
		<option></option>
	<?php
	$r_sql="SELECT login,nom,prenom,etat, statut FROM utilisateurs WHERE statut IN ('administrateur','professeur','cpe','scolarite','secours','autre') ORDER BY nom,prenom";
	$R_utilisateurs=mysqli_query($GLOBALS["mysqli"], $r_sql);
	$initiale_courante=0;
	while($utilisateur=mysqli_fetch_array($R_utilisateurs))
		{
		$nom=mb_strtoupper($utilisateur['nom'])." ".$utilisateur['prenom'];
		$initiale=ord(mb_strtoupper($nom));
		if ($initiale!=$initiale_courante)
			{
			$initiale_courante=$initiale;
			echo "\t</optgroup><optgroup label=\"".chr($initiale)."\">";
			}
		?>
		<option value="<?php echo $utilisateur['login']; ?>" <?php if (isset($login_destinataire)) {if ($utilisateur['login']==$login_destinataire) {echo "selected";}} if($utilisateur['etat']=="inactif") { echo " style='background-color:grey;'";} ?> title="<?php echo $utilisateur['statut'];?>"><?php echo $nom." (".$utilisateur['login'].")"; ?></option>
		<?php
		}
	?>
		</optgroup>
	</select>
<br>

<?php
echo "</td></tr>\n";

echo "<tr><td  colspan=\"4\" >\n";
?>
<br>
<i>Matière du destinataire du message&nbsp;:&nbsp;</i><br />
	<select name="matiere_destinataire" style="margin-left: 20px; max-width: 500px; width: 300px;">
		<optgroup>
		<option></option>
	<?php
	$r_sql="SELECT DISTINCT m.* FROM utilisateurs u, j_groupes_professeurs jgp, j_groupes_matieres jgm, matieres m
		WHERE u.login=jgp.login AND
			jgp.id_groupe=jgm.id_groupe AND
			jgm.id_matiere=m.matiere
		ORDER BY m.matiere, m.nom_complet";
	$R_matieres=mysqli_query($GLOBALS["mysqli"], $r_sql);
	$initiale_courante=0;
	while($matiere=mysqli_fetch_array($R_matieres))
		{
		/*
		<option value="<?php echo $matiere['matiere']; ?>" <?php if (isset($matiere_destinataire)) {if ($matiere['matiere']==$matiere_destinataire) {echo "selected";}}?>><?php echo $matiere['matiere']." (".$matiere['nom_complet'].")"; ?></option>
		*/
		?>
		<option value="<?php echo $matiere['matiere']; ?>"><?php echo $matiere['matiere']." (".$matiere['nom_complet'].")"; ?></option>
		<?php
		}
	?>
		</optgroup>
	</select>
<br><br>

<?php
echo "</td></tr>\n";

echo "<tr><td  colspan=\"4\" >\n";
?>
<i>Classe dans laquelle enseignent les destinataires du message&nbsp;:&nbsp;</i><br />
	<select name="id_classe" style="margin-left: 20px; max-width: 500px; width: 300px;">
		<optgroup>
		<option></option>
	<?php
	$r_sql="SELECT id,nom_complet,classe FROM classes ORDER BY classe";
	$R_classes=mysqli_query($GLOBALS["mysqli"], $r_sql);
	while($classe=mysqli_fetch_array($R_classes))
		{
		?>
		<option value="<?php echo $classe['id']; ?>" <?php if (isset($id_classe)) if ($classe['id']==$id_classe) echo "selected"; ?> title="Déposer un message sur le Panneau d'affichage
pour tous les professeurs de la classe de <?php echo $classe['classe'];?>.
Pour information, le <?php echo getSettingValue('gepi_prof_suivi')?> de la classe est :
<?php echo liste_des_prof_suivi_de_telle_classe($classe['id']);?>"><?php
			echo $classe['nom_complet'];
			if($classe['nom_complet']!=$classe['classe']) {echo " (".$classe['classe'].")";}
		?></option>
		<?php
		}
	?>
		</optgroup>
	</select>
<br><br>

<?php
echo "</td></tr>\n";

echo "<tr><td  colspan=\"4\" >\n";
?>
<i>Élèves de la classe de&nbsp;:&nbsp;</i><br />
	<select name="eleves_id_classe" style="margin-left: 20px; max-width: 500px; width: 300px;">
		<optgroup>
		<option></option>
	<?php
	$r_sql="SELECT id,nom_complet,classe FROM classes ORDER BY classe";
	$R_classes=mysqli_query($GLOBALS["mysqli"], $r_sql);
	while($classe=mysqli_fetch_array($R_classes))
		{
		?>
		<option value="<?php echo $classe['id']; ?>" <?php if (isset($eleves_id_classe)) if ($classe['id']==$eleves_id_classe) echo "selected"; ?> title="Déposer un message sur le Panneau d'affichage
pour tous les élèves de la classe de <?php echo $classe['classe'];?>.
Pour information, le <?php echo getSettingValue('gepi_prof_suivi')?> de la classe est :
<?php echo liste_des_prof_suivi_de_telle_classe($classe['id']);?>"><?php
			echo $classe['nom_complet'];
			if($classe['nom_complet']!=$classe['classe']) {echo " (".$classe['classe'].")";}
		?></option>
		<?php
		}
	?>
		</optgroup>
	</select>
<br><br>

<?php
echo "</td></tr>\n";

echo "<tr><td  colspan=\"4\" >\n";
?>
<i>Responsables (parents,...) d'élèves de la classe de&nbsp;:&nbsp;</i><br />
	<select name="parents_id_classe" style="margin-left: 20px; max-width: 500px; width: 300px;">
		<optgroup>
		<option></option>
	<?php
	$r_sql="SELECT id,nom_complet,classe FROM classes ORDER BY classe";
	$R_classes=mysqli_query($GLOBALS["mysqli"], $r_sql);
	while($classe=mysqli_fetch_array($R_classes))
		{
		?>
		<option value="<?php echo $classe['id']; ?>" <?php if (isset($parents_id_classe)) if ($classe['id']==$parents_id_classe) echo "selected"; ?> title="Déposer un message sur le Panneau d'affichage
pour tous les responsables (parents,...) d'élèves de la classe de <?php echo $classe['classe'];?>.
Pour information, le <?php echo getSettingValue('gepi_prof_suivi')?> de la classe est :
<?php echo liste_des_prof_suivi_de_telle_classe($classe['id']);?>"><?php
			echo $classe['nom_complet'];
			if($classe['nom_complet']!=$classe['classe']) {echo " (".$classe['classe'].")";}
		?></option>
		<?php
		}
	?>
		</optgroup>
	</select>
<br><br>

<?php
echo "</td></tr>\n";

echo "<tr><td  colspan=\"4\" >\n";
?>

<i>Le destinataire peut supprimer ce message&nbsp;:&nbsp;</i>
<label for='suppression_possible_oui'>Oui </label><input type="radio" name="suppression_possible" id="suppression_possible_oui" value="oui" />
<label for='suppression_possible_non'>Non </label><input type="radio" name="suppression_possible" id="suppression_possible_non" value="non" checked="checked" />

<?php
$titre_infobulle="SUPPRESSION\n";
$texte_infobulle="Après lecture, un utilisateur ne peut pas supprimer un message si celui-ci est destiné à un ou plusieurs statuts.<br />
Seuls les messages destinés à des individus, matière précise ou classe précise peuvent être supprimés par leur destinataire.<br />
<br />
Lors de la définition d'un message destiné à un ou plusieurs statuts, un seul message est enregistré (<em>il peut ainsi être modifié par la suite</em>)<br />
En revanche, lors de la saisie d'un message destiné à des individus, classe, matière, il y a autant de messages générés que d'individus (<em>après leur enregistrement, ils ne peuvent plus être modifiés et ils n'apparaissent pas dans la liste sur la gauche</em>).\n";
//$texte_infobulle.="\n";
$tabdiv_infobulle[]=creer_div_infobulle('SUPPRESSION',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('SUPPRESSION','y',100,100);\"  onmouseout=\"cacher_div('SUPPRESSION');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";
?>
<br><br>

<?php
echo "</td></tr>\n";

// Message
echo "<tr><td  colspan=\"4\">\n";

echo "<i>Mise en forme du message :</i>\n";

$oCKeditor = new CKeditor('../ckeditor/');
$oCKeditor->editor('message',$contenu) ;

echo "</td></tr>";

// Boutons Enregistrer - Annuler
echo "<tr><td colspan=\"4\" align=\"center\"> ";

echo "<input type='hidden' name='ok' value='y' />\n";

echo "<noscript><input type=\"submit\" value=\"Enregistrer\" style=\"font-variant: small-caps;\" name=\"button_ok_sans_javascript\" /></noscript>\n";
//echo "<input type=\"submit\" value=\"Enregistrer\" style=\"font-variant: small-caps;\" name=\"ok\" onclick=\"check_et_valide_form()\" />\n";
echo "<input type=\"button\" value=\"Enregistrer\" style=\"font-variant: small-caps;\" name=\"button_ok_avec_javascript\" onclick=\"check_et_valide_form()\" />\n";

echo "<script type='text/javascript'>
function checkdate (m, d, y) {
    // Returns true(1) if it is a valid date in gregorian calendar  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/checkdate    
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Pyerre
    // +   improved by: Theriault
    // *     example 1: checkdate(12, 31, 2000);
    // *     returns 1: true    // *     example 2: checkdate(2, 29, 2001);
    // *     returns 2: false
    // *     example 3: checkdate(3, 31, 2008);
    // *     returns 3: true
    // *     example 4: checkdate(1, 390, 2000);    
    // *     returns 4: false
    return m > 0 && m < 13 && y > 2000 && y < 32768 && d > 0 && d <= (new Date(y, m, 0)).getDate();
}
function check_et_valide_form() {
	display_date_debut=document.getElementById('display_date_debut').value;
	display_date_fin=document.getElementById('display_date_fin').value;

	tmp=display_date_debut.split('/');
	jour_debut=tmp[0];
	mois_debut=tmp[1];
	annee_debut=tmp[2];
	if(!checkdate(mois_debut,jour_debut,annee_debut)) {
		alert('La date de début d\'affichage est invalide.');
	}
	else {
		tmp=display_date_fin.split('/');
		jour_fin=tmp[0];
		mois_fin=tmp[1];
		annee_fin=tmp[2];
		if(!checkdate(mois_fin,jour_fin,annee_fin)) {
			alert('La date de fin d\'affichage est invalide.');
		}
		else {
			t1=eval(annee_debut*10000+mois_debut*100+jour_debut)
			t2=eval(annee_fin*10000+mois_fin*100+jour_fin)
			if(t2<=t1) {
				alert('La date de fin d\'affichage doit dépasser celle de début.')
			}
			else {
				document.formulaire.submit();
			}
		}
	}
}

function check_et_acces_champ_suppression_message() {
	var tab=new Array('desti_a', 'desti_c', 'desti_e', 'desti_p', 'desti_r', 'desti_s');
	var acces='y';

	for(i=0;i<tab.length;i++) {
		if(document.getElementById(tab[i]).checked==true) {
			acces='n';
			break;
		}
	}

	if(acces=='y') {
		document.getElementById('suppression_possible_oui').disabled=false;
		document.getElementById('suppression_possible_non').disabled=false;
	}
	else {
		// On coche l'interdiction de suppression de message:
		document.getElementById('suppression_possible_non').checked=true;

		document.getElementById('suppression_possible_oui').disabled=true;
		document.getElementById('suppression_possible_non').disabled=true;
	}
}
</script>\n";

if (isset($id_mess)) echo "<input type=\"submit\" value=\"Annuler\" style=\"font-variant: small-caps;\" name=\"cancel\" />\n";

echo "</td></tr>\n";

echo "</table>\n";
echo "</fieldset>\n";
echo "</form></td></tr></table>\n";

// Fin de la colonne de droite

echo "</td></tr></table>\n";
require("../lib/footer.inc.php");
?>
