<?php
/*
 * Last modification  : 14/03/2005
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);


// Resume session
$resultat_session = resumeSession();

if ($resultat_session == 'c') {
   header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}



if (isset($is_posted) and ($is_posted == "1")) {
    if ($display_end < $display_begin) {$display_end = $display_begin;}
    if (!isset($display_bulletin)) $display_bulletin = 'n'; // Compatibilité avec version
    $del = mysql_query("DELETE FROM aid_config WHERE indice_aid = '$indice_aid'");
    echo "<!-- DELETE FROM aid_config WHERE indice_aid = '$indice_aid' -->";
    $reg_data = mysql_query("INSERT INTO aid_config SET nom='".$reg_nom."', nom_complet='".$reg_nom_complet."', note_max='$note_max', display_begin='$display_begin', display_end='$display_end', type_note='$type_note', order_display1 = '$order_display1', order_display2 = '$order_display2', message ='".$message."', display_nom='$display_nom', indice_aid='$indice_aid', display_bulletin='$display_bulletin'");
    if (!$reg_data) {
        $msg = "Erreur lors de l'enregistrement des données !";
    } else {
        $msg = "Enregistrement réussi !";
    }
}


//**************** EN-TETE *********************
$titre_page = "Gestion des AID";
require_once("../lib/header.inc");
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

    // Compatibilité avec version
    if ($display_bulletin=='')  $display_bulletin = "y";

/*
    // AJOUT: boireaus
    echo "<script language='javascript' type='text/javascript'>
    setTimeout('window.location=\"index.php\"',1000);
</script>\n";
*/
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
}

?>

<!--form enctype="multipart/form-data" name= "formulaire" action="config_aid.php" method=post onsubmit="return (emptyFormElements(this, 'reg_nom_complet') && (emptyFormElements(this, 'reg_nom')) && checkFormElementInRange(this, 'order_display2', 0, 100))"-->
<!--form enctype="multipart/form-data" name= "formulaire" action="config_aid.php" method=post onsubmit="return (emptyFormElements(this, 'reg_nom_complet') &amp;&amp; (emptyFormElements(this, 'reg_nom')) &amp;&amp;s checkFormElementInRange(this, 'order_display2', 0, 100))"-->
<!--form enctype="multipart/form-data" name= "formulaire" action="config_aid.php" method=post onsubmit="return (emptyFormElements('formulaire', 'reg_nom_complet') && (emptyFormElements('formulaire', 'reg_nom')) && checkFormElementInRange('formulaire', 'order_display2', 0, 100))"-->
<form enctype="multipart/form-data" name= "formulaire" action="config_aid.php" method=post onsubmit="return (emptyFormElements('formulaire', 'reg_nom_complet') &amp;&amp; (emptyFormElements('formulaire', 'reg_nom')) &amp;&amp; checkFormElementInRange('formulaire', 'order_display2', 0, 100))">

<div class='norme'><p class=bold>|<a href="index.php">Retour</a>|

<input type=submit value=Enregistrer /><br />

<br /><b>Configuration des AID (Activités Inter-Disciplinaires) :</b>

<hr />

Choisissez le nom complet de l'AID (par exemple Travaux Pratiques Encadrés) :

<br />Nom complet : <input type=text name=reg_nom_complet size=40 <?php if (isset($reg_nom_complet)) { echo "value=\"".$reg_nom_complet."\"";}?> />

<br /><br />Choisissez le nom abrégé de l'AID (par exemple T.P.E.) :

<br />Nom : <input type=text name=reg_nom size=20 <?php if (isset($reg_nom)) { echo "value=\"".$reg_nom."\"";}?> />

<hr />

Type de notation :  <br />

<input type=radio name=type_note value=every <?php if (($type_note == "every") or ($type_note == "")) { echo "CHECKED" ;} ?> /> Une note pour chaque période

<input type=radio name=type_note value=last <?php if ($type_note == "last") { echo "CHECKED" ;} ?> /> Une note uniquement pour la dernière période

<input type=radio name=type_note value=no <?php if ($type_note == "no") { echo "CHECKED" ;} ?> onclick="mise_a_zero()" /> Pas de note

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

    echo "<option"; if ($display_begin==$i) {echo " selected";} echo ">$i";

    $i++;

}

?>

</SELECT>

(incluse) jusqu'à la période

<SELECT name="display_end">

<?php

$i = 1;

while ($i < $max_periode) {

    echo "<option"; if ($display_end==$i) {echo " selected";} echo ">$i";

    $i++;

}

?>

</SELECT>

(incluse).



<hr />

Choisissez le cas échéant la note maximum sur laquelle est notée l'AID :

<br />Note maximum : <input type=text name=note_max size=20 <?php if ($note_max) { echo "value=\"".$note_max."\"";}?> onBlur="verif_type_note()" />

<hr />

Dans le bulletin final, le titre complet apparaît et précède l'appréciation dans la case appréciation :<br />

<input type=radio name=display_nom value=y <?php if (($display_nom == "y") or ($display_nom == "")) { echo "CHECKED" ;} ?> /> Oui

<input type=radio name=display_nom value=n <?php if ($display_nom == "n") { echo "CHECKED" ;} ?> /> Non

<hr />

Dans le bulletin final, le message suivant précède le titre complet dans la case appréciation :<br />

<input type=text name=message size=40 <?php if ($message) { echo "value=\"".$message."\"";}?> />

<hr />

Place de la case réservée à cette aid dans le bulletin final :<br />

<input type=radio name=order_display1 value=b <?php if (($order_display1 == "b") or (!$order_display1)) { echo "CHECKED" ;} ?> /> En début du bulletin

<input type=radio name=order_display1 value=e <?php if ($order_display1 == "e") { echo "CHECKED" ;} ?> /> En fin de bulletin

<br />

Position par rapport aux autres aid (entrez un nombre entre 1 et 100) :

<input type=text name=order_display2 size=1 <?php if (isset($order_display2)) { echo "value=\"".$order_display2."\"";}?> />

<hr />

Affichage :  <br />

<input type=checkbox name=display_bulletin value='y' <?php if ($display_bulletin == "y") { echo "CHECKED" ;} ?> /> L'AID apparaît dans le bulletin officiel (si la case est décochée la rubrique AID n'apparaît que dans le bulletin simplifié)



</div>

<input type=hidden name=is_posted value=1 />

<input type=hidden name=indice_aid value=<?php echo $indice_aid;?> />

<input type=submit value=Enregistrer />

</form>

</body>

</html>