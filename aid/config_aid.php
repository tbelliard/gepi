<?php
/*
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


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
// ========== Iniialisation des variables ==========
$reg_nom = isset($_POST["reg_nom"]) ? $_POST["reg_nom"] : NULL;
$reg_nom_complet = isset($_POST["reg_nom_complet"]) ? $_POST["reg_nom_complet"] : NULL;
$note_max = isset($_POST["note_max"]) ? $_POST["note_max"] : NULL;
$display_begin = isset($_POST["display_begin"]) ? $_POST["display_begin"] : NULL;
$display_end = isset($_POST["display_end"]) ? $_POST["display_end"] : NULL;
$type_note = isset($_POST["type_note"]) ? $_POST["type_note"] : NULL;
$order_display1 = isset($_POST["order_display1"]) ? $_POST["order_display1"] : NULL;
$order_display2 = isset($_POST["order_display2"]) ? $_POST["order_display2"] : NULL;
$message = isset($_POST["message"]) ? $_POST["message"] : NULL;
$display_nom = isset($_POST["display_nom"]) ? $_POST["display_nom"] : NULL;
$indice_aid = isset($_POST["indice_aid"]) ? $_POST["indice_aid"] : (isset($_GET["indice_aid"]) ? $_GET["indice_aid"] : NULL);
$display_bulletin = isset($_POST["display_bulletin"]) ? $_POST["display_bulletin"] : 'n';
$autoriser_inscript_multiples = isset($_POST["autoriser_inscript_multiples"]) ? $_POST["autoriser_inscript_multiples"] : 'n';
$bull_simplifie = isset($_POST["bull_simplifie"]) ? $_POST["bull_simplifie"] : 'n';
$activer_outils_comp = isset($_POST["activer_outils_comp"]) ? $_POST["activer_outils_comp"] : 'n';
$feuille_presence = isset($_POST["feuille_presence"]) ? $_POST["feuille_presence"] : 'n';
$is_posted = isset($_POST["is_posted"]) ? $_POST["is_posted"] : NULL;
// ========== fin initialisation ===================

if (isset($is_posted) and ($is_posted == "1")) {
  check_token();
  $msg_inter = "";
  if ($autoriser_inscript_multiples != 'y') {
    $test = sql_query1("select count(login) c from j_aid_eleves where indice_aid='".$indice_aid."' group by login order by c desc limit 1");
    if ($test > 1) {
      $msg_inter = "Actuellement, un ou plusieurs élèves sont inscrits dans plusieurs AID à la fois.
      Impossible donc de supprimer l'autorisation d'inscrire un &eacute;l&egrave;ve &agrave; plusieurs AID d'une m&ecirc;me cat&eacute;gorie.";
      $autoriser_inscript_multiples = 'y';
    }
  }
	if ($display_end < $display_begin) {$display_end = $display_begin;}
	$del = mysql_query("DELETE FROM aid_config WHERE indice_aid = '".$indice_aid."'");
	echo "<!-- DELETE FROM aid_config WHERE indice_aid = '".$indice_aid."' -->";
	$reg_data = mysql_query("INSERT INTO aid_config SET
			nom='".$reg_nom."',
			nom_complet='".$reg_nom_complet."',
			note_max='".$note_max."',
			display_begin='".$display_begin."',
			display_end='".$display_end."',
			type_note='".$type_note."',
			order_display1 = '".$order_display1."',
			order_display2 = '".$order_display2."',
			message ='".$message."',
			display_nom='".$display_nom."',
			indice_aid='".$indice_aid."',
			display_bulletin='".$display_bulletin."',
			autoriser_inscript_multiples='".$autoriser_inscript_multiples."',
			bull_simplifie = '".$bull_simplifie."',
			feuille_presence = '".$feuille_presence."',
			outils_complementaires = '".$activer_outils_comp."'");
	  if (!$reg_data)
		  $msg_inter .= "Erreur lors de l'enregistrement des données !<br />";

		// Suppression de professeurs dans le cas des outils complémentaire
		$call_profs = mysql_query("SELECT id_utilisateur FROM j_aidcateg_utilisateurs WHERE (indice_aid='$indice_aid')");
		$nb_profs = mysql_num_rows($call_profs);
		$i = 0;
		while($i < $nb_profs) {
		    $login_prof = mysql_result($call_profs,$i);
		    if (isset($_POST["delete_".$login_prof])) {
		        $reg_data = mysql_query("delete from j_aidcateg_utilisateurs WHERE (id_utilisateur = '$login_prof' and indice_aid='$indice_aid')");
            if (!$reg_data) $msg_inter .= "Erreur lors de la suppression du professeur $login_prof!<br />";
		    }
		    $i++;
		} // while
    if (isset($_POST["reg_prof_login"]) and ($_POST["reg_prof_login"] !="")) {
        // On commence par vérifier que le professeur n'est pas déjà présent dans cette liste.
        $test = sql_query1("SELECT count(id_utilisateur) FROM j_aidcateg_utilisateurs WHERE (id_utilisateur = '$reg_prof_login' and indice_aid='$indice_aid')");
        if ($test != "0") {
            $msg = "Le professeur que vous avez tenté d'ajouter appartient déjà à cet AID";
        } else {
            $reg_data = mysql_query("INSERT INTO j_aidcateg_utilisateurs SET id_utilisateur= '".$_POST["reg_prof_login"]."', indice_aid='".$indice_aid."'");
            if (!$reg_data) $msg_inter .= "Erreur lors de l'ajout du professeur !<br />";
        }
    }
		// Suppression de "super-gestionaires"
		$call_profs = mysql_query("SELECT id_utilisateur FROM j_aidcateg_super_gestionnaires WHERE (indice_aid='$indice_aid')");
		$nb_profs = mysql_num_rows($call_profs);
		$i = 0;
		while($i < $nb_profs) {
		    $login_gestionnaire = mysql_result($call_profs,$i);
		    if (isset($_POST["delete_gestionnaire_".$login_gestionnaire])) {
		        $reg_data = mysql_query("delete from j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '$login_gestionnaire' and indice_aid='$indice_aid')");
            if (!$reg_data) $msg_inter .= "Erreur lors de la suppression du professeur $login_gestionnaire!<br />";
		    }
		    $i++;
		} // while


    if (isset($_POST["reg_gestionnaire_login"]) and ($_POST["reg_gestionnaire_login"] !="")) {
        // On commence par vérifier que le professeur n'est pas déjà présent dans cette liste.
        $test = sql_query1("SELECT count(id_utilisateur) FROM j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '$reg_gestionnaire_login' and indice_aid='$indice_aid')");
        if ($test != "0") {
            $msg = "Le professeur que vous avez tenté d'ajouter appartient déjà à cet AID";
        } else {
            $reg_data = mysql_query("INSERT INTO j_aidcateg_super_gestionnaires SET id_utilisateur= '".$_POST["reg_gestionnaire_login"]."', indice_aid='".$indice_aid."'");
            if (!$reg_data) $msg_inter .= "Erreur lors de l'ajout du professeur !<br />";
        }
    }


    if ($msg_inter !="") {
        $msg = $msg_inter;
    } else {
        $msg = "Enregistrement réussi !";
    }
}


//**************** EN-TETE *********************
$titre_page = "Gestion des AID";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

?>

<script type="text/javascript" language="javascript">
var errorMsg0  = 'Le formulaire est incomplet !';
var errorMsg1  = ' veuillez rentrer un nombre ! ';
var errorMsg2  = ' : cette valeur n\'est pas autorisée ! ';
function mise_a_zero() {
    window.document.formulaire.note_max.value = '';
}

function verif_type_note() {
    if (window.document.formulaire.type_note[2].checked == true) {
        window.document.formulaire.note_max.value = '';
    }
    if (window.document.formulaire.type_note[2].checked != true && window.document.formulaire.note_max.value == '')
        {
            window.document.formulaire.note_max.value = '20';
        }
}

//=================================
// AJOUT: boireaus
function emptyFormElements(formulaire,champ){
	//eval("document.forms['"+formulaire+"']."+champ+".value=''");
	// J'ai viré la ligne parce qu'elle vide le champ avant que la valeur soit transmise
	// et du coup on insère dans la table des noms vides.
	return true;
}

function checkFormElementInRange(formulaire,champ,vmin,vmax){
	eval("vchamp=document.forms['"+formulaire+"']."+champ+".value");
	chaine_reg=new RegExp('[0-9]+');
	if((vchamp<0)||(vchamp>100)||(vchamp.replace(chaine_reg,'')).length!=0){
		alert("La valeur du champ "+champ+" ("+vchamp+") n'est pas comprise entre 0 et 100.");
		return false;
	}
	else{
		return true;
	}
}
//=================================

</script>

<?php
if (isset($indice_aid)) {
    $call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
    $reg_nom = @mysql_result($call_data, 0, "nom");
    $reg_nom_complet = @mysql_result($call_data, 0, "nom_complet");
    $note_max = @mysql_result($call_data, 0, "note_max");
    $order_display1 = @mysql_result($call_data, 0, "order_display1");
    $order_display2 = @mysql_result($call_data, 0, "order_display2");
    $type_note = @mysql_result($call_data, 0, "type_note");
    $display_begin = @mysql_result($call_data, 0, "display_begin");
    $display_end = @mysql_result($call_data, 0, "display_end");
    $message = @mysql_result($call_data, 0, "message");
    $display_nom = @mysql_result($call_data, 0, "display_nom");
    $display_bulletin = @mysql_result($call_data, 0, "display_bulletin");
    $autoriser_inscript_multiples = @mysql_result($call_data, 0, "autoriser_inscript_multiples");
    $bull_simplifie = @mysql_result($call_data, 0, "bull_simplifie");
    $activer_outils_comp = @mysql_result($call_data, 0, "outils_complementaires");
    $feuille_presence = @mysql_result($call_data, 0, "feuille_presence");
    // Compatibilité avec version
    if ($display_bulletin=='')  $display_bulletin = "y";
    if ($autoriser_inscript_multiples=='')  $autoriser_inscript_multiples = "n";
} else {
    $call_data = mysql_query("SELECT max(indice_aid) max FROM aid_config");
    $indice_aid = @mysql_result($call_data, 0, "max");
    $indice_aid++;
    $note_max = 20;
    $display_begin = '';
    $display_end = '';
    $display_nom = '';
    $message = '';
    $order_display1 = '';
    $order_display2 = '';
    $type_note = "every";
    $display_bulletin = "y";
    $autoriser_inscript_multiples = "n";
    $bull_simplifie = "y";
    $activer_outils_comp = "n";
    $feuille_presence = "n";
}
?>

<!--form enctype="multipart/form-data" name= "formulaire" action="config_aid.php" method=post onsubmit="return (emptyFormElements(this, 'reg_nom_complet') && (emptyFormElements(this, 'reg_nom')) && checkFormElementInRange(this, 'order_display2', 0, 100))"-->
<!--form enctype="multipart/form-data" name= "formulaire" action="config_aid.php" method=post onsubmit="return (emptyFormElements(this, 'reg_nom_complet') &amp;&amp; (emptyFormElements(this, 'reg_nom')) &amp;&amp;s checkFormElementInRange(this, 'order_display2', 0, 100))"-->
<!--form enctype="multipart/form-data" name= "formulaire" action="config_aid.php" method=post onsubmit="return (emptyFormElements('formulaire', 'reg_nom_complet') && (emptyFormElements('formulaire', 'reg_nom')) && checkFormElementInRange('formulaire', 'order_display2', 0, 100))"-->
<form enctype="multipart/form-data" name="formulaire" action="config_aid.php" method="post" onsubmit="return (emptyFormElements('formulaire', 'reg_nom_complet') &amp;&amp; (emptyFormElements('formulaire', 'reg_nom')) &amp;&amp; checkFormElementInRange('formulaire', 'order_display2', 0, 100))">

<?php
echo add_token_field();
?>
<div class='norme'><p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>

<input type="submit" value="Enregistrer" /><br />

<br /><b>Configuration des AID (Activités Inter-Disciplinaires) :</b>

<hr />

Choisissez le nom complet de l'AID (par exemple Travaux Personnels Encadrés) :

<br />Nom complet : <input type="text" name="reg_nom_complet" size="40" <?php if (isset($reg_nom_complet)) { echo "value=\"".$reg_nom_complet."\"";}?> />

<br /><br />Choisissez le nom abrégé de l'AID (par exemple T.P.E.) :

<br />Nom : <input type="text" name="reg_nom" size="20" <?php if (isset($reg_nom)) { echo "value=\"".$reg_nom."\"";}?> />

<hr />

Type de notation :  <br />

<input type="radio" name="type_note" value="every" <?php if (($type_note == "every") or ($type_note == "")) { echo ' checked="checked"';} ?> /> Une note pour chaque période

<input type="radio" name="type_note" value="last" <?php if ($type_note == "last") { echo ' checked="checked"';} ?> /> Une note uniquement pour la dernière période

<input type="radio" name="type_note" value="no" <?php if ($type_note == "no") { echo ' checked="checked"';} ?> onclick="mise_a_zero()" /> Pas de note

<hr />



<?php

$query_max_periode = mysql_query("SELECT max(num_periode) max FROM periodes");

$max_periode = mysql_result($query_max_periode, 0, "max")+1;

echo "Durée de l'AID : ";

if ($max_periode == '1') {

   echo " <font color='red'>Attention, aucune période n'est actuellement définie (commencez par créer une ou plusieurs classes sur une ou plusieurs périodes).</font>";

   $max_periode = '2';

} echo "<br /> L'aid débute à la période";

echo "<SELECT name=\"display_begin\">";

$i = 1;

while ($i < $max_periode) {

    echo "<option"; if ($display_begin==$i) {echo ' selected="selected"';} echo ">$i";

    $i++;

}

?>

</SELECT>

(incluse) jusqu'à la période

<SELECT name="display_end">

<?php

$i = 1;

while ($i < $max_periode) {

    echo "<option"; if ($display_end==$i) {echo ' selected="selected"';} echo ">$i";

    $i++;

}

?>

</SELECT>

(incluse).



<hr />

Choisissez le cas échéant la note maximum sur laquelle est notée l'AID :

<br />Note maximum : <input type="text" name="note_max" size="20" <?php if ($note_max) { echo "value=\"".$note_max."\"";}?> onBlur="verif_type_note()" />

<hr />

Dans le bulletin final, le titre complet apparaît et précède l'appréciation dans la case appréciation :<br />

<input type="radio" name="display_nom" value="y" <?php if (($display_nom == "y") or ($display_nom == "")) { echo ' checked="checked"';} ?> /> Oui

<input type="radio" name="display_nom" value="n" <?php if ($display_nom == "n") { echo ' checked="checked"';} ?> /> Non

<hr />

Dans le bulletin final, le message suivant précède le titre complet dans la case appréciation :<br />

<input type="text" name="message" size="40" maxlength="40" <?php if ($message) { echo "value=\"".$message."\"";}?> /><br />
<span style='font-size:small;'>(Ce message prendra de la place dans la case appréciation sur le bulletin)</span>

<hr />

<p>Place de la case réservée à cette aid dans le bulletin final :</p>
<p>
<input type="radio" id="orderDisplay1Y" name="order_display1" value="b" <?php if (($order_display1 == "b") or (!$order_display1)) { echo ' checked="checked"';;} ?> />
<label for="orderDisplay1Y"> En début du bulletin</label>
<input type="radio"id="orderDisplay1N" name="order_display1" value="e" <?php if ($order_display1 == "e") { echo ' checked="checked"';;} ?> />
<label for="orderDisplay1N"> En fin de bulletin</label>
</p>





<br />

Position par rapport aux autres aid (entrez un nombre entre 1 et 100) :

<input type="text" name="order_display2" size="1" <?php if (isset($order_display2)) { echo "value=\"".$order_display2."\"";}?> />

<hr />

<p><b>Affichage :  </b></p>
<p>
<input type="checkbox" id="display_Bulletin" name="display_bulletin" value="y" <?php if ($display_bulletin == "y") { echo ' checked="checked"';} ?> />
<label for="display_Bulletin">L'AID apparaît dans le bulletin officiel</label>
</p>
<p>
<input type="checkbox" id="bullSimplifie" name="bull_simplifie" value='y' <?php if ($bull_simplifie == "y") { echo ' checked="checked"';} ?> />
<label for="bullSimplifie">L'AID appara&icirc;t dans le bulletin simplifi&eacute;.</label>
</p>

<hr />

<p><b>Inscriptions multiples :  </b></p>
<p>
Par d&eacute;faut, un &eacute;l&egrave;ve ne peut &ecirc;tre inscrit dans plus d'un AID par cat&eacute;gorie d'AID.
<br />Cependant, dans certains cas, il peut &ecirc;tre utile d'autoriser l'inscription d'un &eacute;l&egrave;ve &agrave; plusieurs AID d'une m&ecirc;me cat&eacute;gorie.</p>
<input type="checkbox" id="autoriser_inscript_multiples" name="autoriser_inscript_multiples" value="y" <?php if ($autoriser_inscript_multiples == "y") { echo ' checked="checked"';} ?> />
<label for="autoriser_inscript_multiples">Autoriser les inscriptions multiples</label>
</p>

<hr />
<?php
// si le plugin "gestion_autorisations_publications" existe et est activé, on exclue la rubrique correspondante
$test_plugin = sql_query1("select ouvert from plugins where nom='gestion_autorisations_publications'");

if ((getSettingValue("active_mod_gest_aid")=="y") and ($test_plugin=='y') and (getSettingValue("indice_aid_autorisations_publi") != $indice_aid)) {
?>
<p><b>Ajout/suppression de "super-gestionnaires"</b></p>
<p>En plus des professeurs responsable de chaque AID, vous pouvez indiquer ci-dessous des utilisateurs (professeurs ou CPE) ayant le droit de g&eacute;rer les AIDs de cette cat&eacute;gorie (ajout, suppression, modification d'AID, de professeurs ou d'&eacute;l&egrave;ves)</p>
<?php
$call_liste_data = mysql_query("SELECT u.login, u.prenom, u.nom FROM utilisateurs u, j_aidcateg_super_gestionnaires j WHERE (j.indice_aid='$indice_aid' and u.login=j.id_utilisateur and (statut='professeur' or statut='cpe'))  order by u.nom, u.prenom");
$nombre = mysql_num_rows($call_liste_data);
if ($nombre !=0)
    echo "<table border=0>";
$i = "0";
while ($i < $nombre) {
    $login_gestionnaire = mysql_result($call_liste_data, $i, "login");
    $nom_prof = mysql_result($call_liste_data, $i, "nom");
    $prenom_prof = @mysql_result($call_liste_data, $i, "prenom");
    echo "<tr><td><b>";
    echo "$nom_prof $prenom_prof</b></td><td> <input type=\"checkbox\" name=\"delete_gestionnaire_".$login_gestionnaire."\" value=\"y\" /> (cocher pour supprimer)</td></tr>\n";

    $i++;
}
if ($nombre !=0)
    echo "</table>";
echo "<select size=1 name=reg_gestionnaire_login>\n";
echo "<option value=''>(aucun)</option>\n";
$call_prof = mysql_query("SELECT login, nom, prenom FROM utilisateurs WHERE  etat!='inactif' AND (statut = 'professeur' OR statut = 'cpe') order by nom");
$nombreligne = mysql_num_rows($call_prof);
$i = "0" ;
while ($i < $nombreligne) {
    $login_prof = mysql_result($call_prof, $i, 'login');
    $nom_el = mysql_result($call_prof, $i, 'nom');
    $prenom_el = mysql_result($call_prof, $i, 'prenom');
    echo "<option value=\"".$login_prof."\">".$nom_el." ".$prenom_el."</option>\n";
    $i++;
}
?>
</select>
<hr />
<?php } ?>

<p><b>Outils complémentaires de gestion des AIDs :</b></p>
<p>En activant les outils complémentaires de gestion des AIDs, vous avez accès à des champs supplémentaires
(attribution d'une salle, possibilité de définir un résumé, le type de production, des mots_clés, un public destinataire...).
<a href="javascript:centrerpopup('help.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')">Consulter l'aide</a>.</p>
<p>
<input type="radio" onclick="javascript:Element.show('outils_comp');" name="activer_outils_comp" value="y" <?php if ($activer_outils_comp=='y') echo " checked"; ?> />&nbsp;Activer les outils compl&eacute;mentaires<br />
<input type="radio" onclick="javascript:Element.hide('outils_comp');" name="activer_outils_comp" value="n" <?php if ($activer_outils_comp=='n') echo " checked"; ?> />&nbsp;Désactiver les outils compl&eacute;mentaires
</p>
<?php if ($activer_outils_comp=='y') {?>
    <div id="outils_comp">
<?php } else { ?>
    <div id="outils_comp" style="display:none;">
<?php } ?>
<hr />
<p><b>Modification des fiches projet : </b></p>
<p>En plus des professeurs responsable de chaque AID, vous pouvez indiquer ci-dessous des utilisateurs (professeurs ou CPE) ayant le droit de modifier les fiches projet (documentaliste, ...)
même lorsque l'administrateur a désactivé cette possibilité pour les professeurs responsables.</p>
<?php
$call_liste_data = mysql_query("SELECT u.login, u.prenom, u.nom FROM utilisateurs u, j_aidcateg_utilisateurs j WHERE (j.indice_aid='$indice_aid' and u.login=j.id_utilisateur and (statut='professeur' or statut='cpe'))  order by u.nom, u.prenom");
$nombre = mysql_num_rows($call_liste_data);
if ($nombre !=0)
    echo "<table border=0>";
$i = "0";
while ($i < $nombre) {
    $login_prof = mysql_result($call_liste_data, $i, "login");
    $nom_prof = mysql_result($call_liste_data, $i, "nom");
    $prenom_prof = @mysql_result($call_liste_data, $i, "prenom");
    echo "<tr><td><b>";
    echo "$nom_prof $prenom_prof</b></td><td> <input type=\"checkbox\" name=\"delete_".$login_prof."\" value=\"y\" /> (cocher pour supprimer)</td></tr>\n";

    $i++;
}
if ($nombre !=0)
    echo "</table>";
echo "<select size=1 name=reg_prof_login>\n";
echo "<option value=''>(aucun)</option>\n";
$call_prof = mysql_query("SELECT login, nom, prenom FROM utilisateurs WHERE  etat!='inactif' AND (statut = 'professeur' OR statut = 'cpe') order by nom");
$nombreligne = mysql_num_rows($call_prof);
$i = "0" ;
while ($i < $nombreligne) {
    $login_gestionnaire = mysql_result($call_prof, $i, 'login');
    $nom_el = mysql_result($call_prof, $i, 'nom');
    $prenom_el = mysql_result($call_prof, $i, 'prenom');
    echo "<option value=\"".$login_gestionnaire."\">".$nom_el." ".$prenom_el."</option>\n";
    $i++;
}

?>
</select>
<hr /><p><b>Feuille de présence : </b></p>
<p>En cochant la case présence ci-dessous, vous avez la possibilité, dans l'interface de visualisation, d'afficher un lien permettant d'imprimer des feuilles de présence.</p>
<p>
<input type="checkbox" id="feuillePresence" name="feuille_presence" value="y" <?php if ($feuille_presence == "y") { echo ' checked="checked"';} ?> />
<label for="feuille_presence"> Afficher un lien permettant l'impression de feuilles de pr&eacute;sence</label>
</p>
</div>

</div>
<input type="hidden" name="is_posted" value="1" />
<input type="hidden" name="indice_aid" value="<?php echo $indice_aid;?>" />
<div id='fixe'>
<input type="submit" value="Enregistrer" />
</div>
</form>
<?php require("../lib/footer.inc.php"); ?>
