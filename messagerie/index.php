<?php
/*
 * $Id: index.php 7393 2011-07-05 17:58:38Z mleygnac $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
include("../fckeditor/fckeditor.php") ;

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
//Configuration du calendrier
include("../lib/calendrier/calendrier.class.php");
$cal1 = new Calendrier("formulaire", "display_date_debut");
$cal2 = new Calendrier("formulaire", "display_date_fin");
$cal3 = new Calendrier("formulaire", "display_date_decompte");

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

//
// Suppression d'un message
//
if ((isset($action)) and ($action == 'sup_entry')) {
	check_token();
   $res = sql_query("delete from messages where id = '".$_GET['id_del']."'");
   if ($res) $msg = "Suppression réussie";
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
    $statuts_destinataires = '_';
    if (isset($_POST['desti_s'])) $statuts_destinataires .= 's';
    if (isset($_POST['desti_p'])) $statuts_destinataires .= 'p';
    if (isset($_POST['desti_c'])) $statuts_destinataires .= 'c';
    if (isset($_POST['desti_a'])) $statuts_destinataires .= 'a';
    if (isset($_POST['desti_r'])) $statuts_destinataires .= 'r';
    if (isset($_POST['desti_e'])) $statuts_destinataires .= 'e';
    if ($contenu_cor == '') {
        $msg = "ATTENTION : Le message est vide.<br />L'enregistrement ne peut avoir lieu.";
        $record = 'no';
    }
    if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['display_date_debut'])) {
        $anneed = substr($_POST['display_date_debut'],6,4);
        $moisd = substr($_POST['display_date_debut'],3,2);
        $jourd = substr($_POST['display_date_debut'],0,2);
        while ((!checkdate($moisd, $jourd, $anneed)) and ($jourd > 0)) $jourd--;
        $date_debut=mktime(0,0,0,$moisd,$jourd,$anneed);
    } else {
        $msg = "ATTENTION : La date de début d'affichage n'est pas valide.<br />L'enregistrement ne peut avoir lieu.";
        $record = 'no';

    }
    if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['display_date_fin'])) {
        $anneef = substr($_POST['display_date_fin'],6,4);
        $moisf = substr($_POST['display_date_fin'],3,2);
        $jourf = substr($_POST['display_date_fin'],0,2);
        while ((!checkdate($moisf, $jourf, $anneef)) and ($jourf > 0)) $jourf--;
        $date_fin=mktime(23,59,0,$moisf,$jourf,$anneef);
    } else {
        $msg = "ATTENTION : La date de fin d'affichage n'est pas valide.<br />L'enregistrement ne peut avoir lieu.";
        $record = 'no';
    }

    if ($record == 'yes') {
		if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['display_date_decompte'])) {
			$anneed = substr($_POST['display_date_decompte'],6,4);
			$moisd = substr($_POST['display_date_decompte'],3,2);
			$jourd = substr($_POST['display_date_decompte'],0,2);
			//echo "$jourd/$moisd/$anneed<br />";
			while ((!checkdate($moisf, $jourf, $anneef)) and ($jourf > 0)) {
				$jourf--;
				//echo "$jourd/$moisd/$anneed<br />";
			}
			$date_decompte=mktime(0,0,0,$moisd,$jourd,$anneed);
			//echo strftime("%d/%m/%Y à %H:%M",$date_decompte)."<br />";

			if (preg_match("/([0-9]{1,2}):([0-9]{0,2})/", str_ireplace('h',':',$_POST['display_heure_decompte']))) {
				$heured = substr($_POST['display_heure_decompte'],0,2);
				$minuted = substr($_POST['display_heure_decompte'],3,2);
				$date_decompte=$date_decompte+$heured*3600+$minuted*60;
				//echo strftime("%d/%m/%Y à %H:%M",$date_decompte)."<br />";
			} else {
				$msg = "ATTENTION : L'heure de décompte n'est pas valide.<br />L'enregistrement ne peut avoir lieu.";
				$record = 'no';
			}

		} else {
			$msg = "ATTENTION : La date de décompte n'est pas valide.<br />L'enregistrement ne peut avoir lieu.";
			$record = 'no';
		}
	$login_destinataire=$_POST['login_destinataire'];
	}
	
	// par sécurité les rédacteurs d'un message ne peuvent y insérer la variable _CRSF_ALEA_
	$pos_crsf_alea=strpos($contenu_cor,"_CRSF_ALEA_");
    if($pos_crsf_alea!==false)
		{
        $contenu_cor=preg_replace("/_CRSF_ALEA_/","",$contenu_cor);
		$msg = "Contenu interdit.";
		$record = 'no';
		}

    if ($record == 'yes') {
      if (isset($_POST['id_mess'])) {
          $req = mysql_query("UPDATE messages
          SET texte = '".$contenu_cor."',
          date_debut = '".$date_debut."',
          date_fin = '".$date_fin."',
          date_decompte = '".$date_decompte."',
          auteur='".$_SESSION['login']."',
          statuts_destinataires = '".$statuts_destinataires."',
          login_destinataire='".$login_destinataire."'
          WHERE (id ='".$_POST['id_mess']."')");
      } else {
          $req = mysql_query("INSERT INTO messages
          SET texte = '".$contenu_cor."',
          date_debut = '".$date_debut."',
          date_fin = '".$date_fin."',
          date_decompte = '".$date_decompte."',
          auteur='".$_SESSION['login']."',
          statuts_destinataires = '".$statuts_destinataires."',
          login_destinataire='".$login_destinataire."'
          ");
      }
      if ($req) {
          $msg = "Enregistrement réussi.";
          unset($contenu_cor);
          unset($_POST['display_date_debut']);
          unset($_POST['display_date_fin']);
          unset($_POST['display_date_decompte']);
          unset($id_mess);
          unset($statuts_destinataires);
      } else {
          $msg = "Problème lors de l'enregistrement du message.";
      }
    }
}

$message_suppression = "Confirmation de suppression";
//**************** EN-TETE *****************
$titre_page = "Gestion des messages";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *************
//debug_var();
echo "<script type=\"text/javascript\" language=\"JavaScript\" SRC=\"../lib/clock_fr.js\"></SCRIPT>\n";
//-----------------------------------------------------------------------------------
echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
echo "<table width=\"98%\" cellspacing=0 align=\"center\">\n";
echo "<tr>\n";
echo "<td valign='top'>\n";
echo "<p>Nous sommes le :&nbsp;<br />\n";
echo "<script type=\"text/javascript\" language=\"javascript\">\n";
echo "<!--\n";
echo "new LiveClock();\n";
echo "//-->\n";
echo "</SCRIPT></p>\n";
echo "</td>\n";

echo "</tr></table><hr />\n";

echo "<table width=\"100%\" border = 0 align=\"center\" cellpadding=\"10\">\n";
//
// Affichage des messages
//

echo "<tr><td width = \"40%\" valign=\"top\">\n";
echo "<span class='grand'>Tous les messages</span><br />\n";
echo "<span class='small'>Classer par : ";
echo "<a href='index.php?order_by=date_debut'>date début</a> | <a href='index.php?order_by=date_fin'>date fin</a> | <a href='index.php?order_by=id'>date création</a>\n";
echo "</span><br /><br />\n";

$appel_messages = mysql_query("SELECT id, texte, date_debut, date_fin, date_decompte, auteur, statuts_destinataires, login_destinataire FROM messages
WHERE (texte != '') order by ".$order_by." DESC");

$nb_messages = mysql_num_rows($appel_messages);
$ind = 0;
while ($ind < $nb_messages) {
  $content = mysql_result($appel_messages, $ind, 'texte');
  // Mise en forme du texte
  $date_debut1 = mysql_result($appel_messages, $ind, 'date_debut');
  $date_fin1 = mysql_result($appel_messages, $ind, 'date_fin');
  $date_decompte1 = mysql_result($appel_messages, $ind, 'date_decompte');
  $auteur1 = mysql_result($appel_messages, $ind, 'auteur');
  $statuts_destinataires1 = mysql_result($appel_messages, $ind, 'statuts_destinataires');
  $login_destinataire1=mysql_result($appel_messages, $ind, 'login_destinataire');
//  $nom_auteur = sql_query1("SELECT nom from utilisateurs where login = '".$auteur1."'");
//  $prenom_auteur = sql_query1("SELECT prenom from utilisateurs where login = '".$auteur1."'");

  $id_message =  mysql_result($appel_messages, $ind, 'id');

//  echo "<b><i>Message de </i></b>: ".$prenom_auteur." ".$nom_auteur.";
   echo "<b><i>Affichage </i></b>: du <b>".strftime("%a %d %b %Y", $date_debut1)."</b> au <b>".strftime("%a %d %b %Y", $date_fin1)."</b>\n";
   if(strstr($content,'_DECOMPTE_')) {
      //echo "<br />Avec décompte des jours jusqu'au ".formate_date_decompte($date_decompte1);
      echo "<br />Avec décompte des jours jusqu'au ".strftime("%d/%m/%Y à %H:%M",$date_decompte1);
   }
   echo "<br /><b><i>Statuts destinataire(s) </i></b> : ";
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
	echo "<br /><b><i>Login du destinataire </i></b> : ".$login_destinataire1;
   echo "<br /><a href='index.php?id_mess=$id_message'>modifier</a>
   - <a href='index.php?id_del=$id_message&action=sup_entry".add_token_in_url()."' onclick=\"return confirmlink(this, 'Etes-vous sûr de vouloir supprimer ce message ?', '".$message_suppression."')\">supprimer</a>
   <table border=1 width = '100%' cellpadding='5'><tr><td>".$content."</td></tr></table><br />\n";
  $ind++;
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
    $appel_message = mysql_query("SELECT  id, texte, date_debut, date_fin, date_decompte, auteur, statuts_destinataires, login_destinataire  FROM messages
    WHERE (id = '".$id_mess."')");
    $contenu = mysql_result($appel_message, 0, 'texte');
    $date_debut = mysql_result($appel_message, 0, 'date_debut');
    $date_fin = mysql_result($appel_message, 0, 'date_fin');
    $date_decompte = mysql_result($appel_message, 0, 'date_decompte');
    $statuts_destinataires = mysql_result($appel_message, 0, 'statuts_destinataires');
    $login_destinataire=mysql_result($appel_message, 0, 'login_destinataire');
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
        $display_date_fin = $jour."/".$mois."/".$annee;
        $display_date_decompte = $display_date_fin;
    }
	$display_heure_decompte=isset($_POST['display_heure_decompte']) ? $_POST['display_heure_decompte'] : "08:00";
    if (!isset($statuts_destinataires)) $statuts_destinataires = '_';

}
echo "<table border=\"1\" cellpadding=\"5\" cellspacing=\"0\"><tr><td>\n";
echo "<form action=\"./index.php\" method=\"post\" style=\"width: 100%;\" name=\"formulaire\">\n";
echo add_token_field();
if (isset($id_mess)) echo "<input type=\"hidden\" name=\"id_mess\" value=\"$id_mess\" />\n";
echo "<input type=\"hidden\" name=\"action\" value=\"message\" />\n";


echo "<table border=\"0\" width = \"100%\" cellspacing=\"1\" cellpadding=\"1\">\n";

// Titre
echo "<tr><td colspan=\"4\"><span class='grand'>".$titre_mess."</span></td></tr>\n";
//Enregistrer
echo "<tr><td  colspan=\"4\" align=\"center\"><input type=\"submit\" value=\"Enregistrer\" style=\"font-variant: small-caps;\" name=\"ok\" />\n";
if (isset($id_mess)) echo "<input type=\"submit\" value=\"Annuler\" style=\"font-variant: small-caps;\" name=\"cancel\" />\n";

echo "</td></tr>\n";

//Dates
echo "<tr><td colspan=\"4\">\n";
echo "<p><i>Le message sera affiché :</i><br />de la date : ";
echo "<input type='text' name = 'display_date_debut' id= 'display_date_debut' size='8' value = \"".$display_date_debut."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
echo "<a href=\"#\" onClick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Calendrier\" /></a>\n";
echo "&nbsp;à la date : ";
echo "<input type='text' name = 'display_date_fin' id = 'display_date_fin' size='8' value = \"".$display_date_fin."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
echo "<a href=\"#\" onClick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Calendrier\" /></a>\n";
echo "<br />(<span style='font-size:small'>Respectez le format jj/mm/aaaa</span>)</p></td></tr>\n";

//Date pour décompte
echo "<tr><td colspan=\"4\">\n";
echo "<p><i>Décompte des jours jusqu'au :</i> ";
echo "<input type='text' name = 'display_date_decompte' id= 'display_date_decompte' size='8' value = \"".$display_date_decompte."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
echo "<a href=\"#\" onClick=\"".$cal3->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Calendrier\" /></a>\n";
echo " à <input type='text' name = 'display_heure_decompte' id= 'display_heure_decompte' size='8' value = \"".$display_heure_decompte."\" onKeyDown=\"clavier_heure(this.id,event);\" AutoComplete=\"off\" />\n";
echo "<br />(<span style='font-size:small'>Respectez le format jj/mm/aaaa</span>)<br />Saisir une chaine <b>_DECOMPTE_</b> dans le corps du message pour que cette date soit prise en compte.\n";

$titre_infobulle="DECOMPTE\n";
$texte_infobulle="Afin d'afficher un compte à rebours, vous devez écrire un texte du style&nbsp;:<br />Il vous reste _DECOMPTE_ pour saisir vos appréciations du 1er trimestre.<br />\n";
//$texte_infobulle.="\n";
$tabdiv_infobulle[]=creer_div_infobulle('a_propos_DECOMPTE',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_DECOMPTE','y',100,100);\"  onmouseout=\"cacher_div('a_propos_DECOMPTE');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";

echo "</p>";

echo "</td></tr>\n";

//Destinataires
echo "<tr><td  colspan=\"4\"><i>Statuts destinataires du message :</i></td></tr>\n";
echo "<tr>\n";
echo "<td><input type=\"checkbox\" id=\"desti_p\" name=\"desti_p\" value=\"desti_p\"";
if (strpos($statuts_destinataires, "p")) echo "checked";
echo " /><label for='desti_p' style='cursor: pointer;'>Professeurs</label></td>\n";

echo "<td><input type=\"checkbox\" id=\"desti_c\" name=\"desti_c\" value=\"desti_c\"";
if (strpos($statuts_destinataires, "c")) echo "checked";
echo " /><label for='desti_c' style='cursor: pointer;'>C.P.E.</label></td>\n";

echo "<td><input type=\"checkbox\" id=\"desti_s\" name=\"desti_s\" value=\"desti_s\"";
if (strpos($statuts_destinataires, "s")) echo "checked";
echo " /><label for='desti_s' style='cursor: pointer;'>Scolarité</label></td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td><input type=\"checkbox\" id=\"desti_a\" name=\"desti_a\" value=\"desti_a\"";
if (strpos($statuts_destinataires, "a")) echo "checked";
echo " /><label for='desti_a' style='cursor: pointer;'>Administrateur</label></td>\n";

echo "<td><input type=\"checkbox\" id=\"desti_r\" name=\"desti_r\" value=\"desti_r\"";
if (strpos($statuts_destinataires, "r")) echo "checked";
echo " /><label for='desti_r' style='cursor: pointer;'>Responsables</label></td>\n";

echo "<td><input type=\"checkbox\" id=\"desti_e\" name=\"desti_e\" value=\"desti_e\"";
if (strpos($statuts_destinataires, "e")) echo "checked";
echo " /><label for='desti_e' style='cursor: pointer;'>Elèves</label></td>\n";

echo "</tr>\n";


echo "<tr><td  colspan=\"4\" >\n";
?>
<br>
<i>Login du destinataire du message :&nbsp;</i>
	<select name="login_destinataire" style="margin-left: 20px; max-width: 500px; width: 300px;">
		<optgroup>
		<option></option>
	<?php
	$r_sql="SELECT login,nom,prenom FROM `utilisateurs` WHERE `statut` IN ('administrateur','professeur','cpe','scolarite','secours','autre') ORDER BY login";
	$R_utilisateurs=mysql_query($r_sql);
	$initiale_courante=0;
	while($utilisateur=mysql_fetch_array($R_utilisateurs))
		{
		$nom=strtoupper($utilisateur['nom'])." ".$utilisateur['prenom'];
		$initiale=ord(strtoupper($utilisateur['login']));
		if ($initiale!=$initiale_courante)
			{
			$initiale_courante=$initiale;
			echo "\t</optgroup><optgroup label=\"".chr($initiale)."\">";
			}
		?>
		<option value="<?php echo $utilisateur['login']; ?>" <?php if (isset($id_mess)) if ($utilisateur['login']==$login_destinataire) echo "selected"; ?>><?php echo $utilisateur['login']." (".$nom.")"; ?></option>
		<?php
		}
	?>
		</optgroup>
	</select>

<br><br>
<?php



echo "</tr>\n";

// Message
echo "<tr><td  colspan=\"4\">\n";

echo "<i>Mise en forme du message :</i>\n";

$oFCKeditor = new FCKeditor('message') ;
$oFCKeditor->BasePath = '../fckeditor/' ;  // '/FCKeditor/' is the default value.
$oFCKeditor->Config['DefaultLanguage']      = 'fr' ;
$oFCKeditor->ToolbarSet = 'Basic' ;
$oFCKeditor->Value      = $contenu ;
$oFCKeditor->Create() ;

echo "</td></tr></table>\n";
echo "</form></td></tr></table>\n";

// Fin de la colonne de droite

echo "</td></tr></table>\n";
require("../lib/footer.inc.php");
?>