<?php
/*
 * Last modification  : 04/04/2005
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

if (isset($is_posted) and ($is_posted == '1')) {
    if (($nom1 != '') and ($prenom1 != '') and ($adr1 != '') and ($commune1 != '') and ($cp1 != '') ) {
        if (($nom2 != '') or ($prenom2 != '') or ($adr2 != '') or ($commune2 != '') or ($cp2 != '') or ($adr2_comp != '')) {
           if (($nom2 != '') and ($prenom2 != '') and ($adr2 != '') and ($commune2 != '') and ($cp2 != '') ) {
               $ok = 'yes';
           } else {
               $msg = "les données concernant le responsable secondaire sont incomplètes.";
               $ok = 'no';
           }
        } else {
           $ok = 'yes';
        }
    } else {
        $msg = "Un ou plusieurs champs obligatoires sont vides !";
        $ok = 'no';
    }

    if ($ok == 'yes') {
        if ($nouv_resp == 'no') {
            $register_resp = mysql_query("UPDATE responsables SET
            nom1 = '$nom1',
            prenom1 = '$prenom1',
            adr1 = '$adr1',
            adr1_comp = '$adr1_comp',
            cp1 = '$cp1',
            commune1 = '$commune1',
            nom2 = '$nom2',
            prenom2 = '$prenom2',
            adr2 = '$adr2',
            adr2_comp = '$adr2_comp',
            cp2 = '$cp2',
            commune2 = '$commune2'
            WHERE ereno = '$ereno'");
            if (!$register_resp) {
                $msg = "Une erreur s'est produite lors de la modification de la fiche.";
            } else {
                $msg = "La fiche a bien été modifiée.";
            }
         } else {
            //$max = @mysql_result(mysql_query("select ereno from responsables order by ereno DESC"), 0, 'ereno');
            //if ($max=='') $max = 0;
// le tri doit être numérique et non sur les chaines de caractères
//            $max = @mysql_result(mysql_query("select ereno from responsables order by ereno DESC"), 0, 'ereno');
	    $table_ereno = mysql_query("select ereno from responsables order by ereno DESC");
            if (!$table_ereno){
		$max = 0;
	    }
	    else {
		$liste_ereno = mysql_fetch_assoc($table_ereno);
		$max = $liste_ereno['ereno'];
		while ($liste_ereno = mysql_fetch_assoc($table_ereno)) {
			if ($max < $liste_ereno['ereno']) {
				$max = $liste_ereno['ereno'];
			}
		}
	    }
            $max++;
            $register_resp = mysql_query("INSERT INTO responsables SET
            ereno = '$max',
            nom1 = '$nom1',
            prenom1 = '$prenom1',
            adr1 = '$adr1',
            adr1_comp = '$adr1_comp',
            cp1 = '$cp1',
            commune1 = '$commune1',
            nom2 = '$nom2',
            prenom2 = '$prenom2',
            adr2 = '$adr2',
            adr2_comp = '$adr2_comp',
            cp2 = '$cp2',
            commune2 = '$commune2'
            ");

            if (!$register_resp) {
                $msg = "Une erreur s'est produite lors de l'enregistrement de la fiche.";
            } else {
                $msg = "La nouvelle fiche a bien été enregistrée.";
            }
        }
    }
}

//**************** EN-TETE *******************************
$titre_page = "Gestion des responsables | Ajouter, modifier un responsable";
require_once("../lib/header.inc");
//**************** FIN EN-TETE ***************************
?>
<p class=bold>
|<a href="index.php">Retour</a>|
</p>

<?php
if (isset($ereno)) {
    $call_resp = mysql_query("SELECT * FROM responsables WHERE ereno = '$ereno'");
    $nom1 = mysql_result($call_resp , 0, "nom1");
    $prenom1 = mysql_result($call_resp , 0, "prenom1");
    $adr1 = mysql_result($call_resp , 0, "adr1");
    $adr1_comp = mysql_result($call_resp , 0, "adr1_comp");
    $commune1 = mysql_result($call_resp , 0, "commune1");
    $cp1 = mysql_result($call_resp , 0, "cp1");
    $nom2 = mysql_result($call_resp , 0, "nom2");
    $prenom2 = mysql_result($call_resp , 0, "prenom2");
    $adr2 = mysql_result($call_resp , 0, "adr2");
    $adr2_comp = mysql_result($call_resp , 0, "adr2_comp");
    $commune2 = mysql_result($call_resp , 0, "commune2");
    $cp2 = mysql_result($call_resp , 0, "cp2");
}

if (!isset($nom1)) $nom1='';
if (!isset($prenom1)) $prenom1='';
if (!isset($adr1)) $adr1='';
if (!isset($adr1_comp)) $adr1_comp='';
if (!isset($commune1)) $commune1='';
if (!isset($cp1)) $cp1='';
if (!isset($nom2)) $nom2='';
if (!isset($prenom2)) $prenom2='';
if (!isset($adr2)) $adr2='';
if (!isset($adr2_comp)) $adr2_comp='';
if (!isset($commune2)) $commune2='';
if (!isset($cp2)) $cp2='';
if (!isset($adr2_comp)) $adr2_comp='';


?>
<form enctype="multipart/form-data" action="modify_resp.php" method="post">
<?php
//echo "<table>";
if (!(isset($ereno)) or ($ereno == '')) {
    echo "<input type=hidden name=nouv_resp value=yes />\n";
    echo "<table>\n";
} else {
    echo "<table>\n";
    echo "<tr><td>Identifiant GEPI : </td><td>$ereno";
    echo "<input type=hidden name=ereno value=$ereno />";
    echo "<input type=hidden name=nouv_resp value=no /></td></tr>\n";
    echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
    $temp = "";
    $call_eleves = mysql_query("SELECT * FROM eleves WHERE ereno='$ereno'");
    $nombreeleves = mysql_num_rows($call_eleves);
    $j = 0;
    while ($j < $nombreeleves){
        $eleve_nom = mysql_result($call_eleves, $j, "nom");
        $eleve_prenom = mysql_result($call_eleves, $j, "prenom");
        if ($j > 0) $temp .= "<br>";
        $temp .= $eleve_prenom." ".$eleve_nom;
        $j++;
    }
    if ($temp == "") $temp = "Aucun";
    echo "<tr><td>Elève(s) rattaché(s) : </td>";
    echo "<td>".$temp."</td>";
    echo "</tr>\n";
    echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n";

}
echo "<tr><td><b>Responsable principal </b></td><td></td></tr>\n";
echo "<tr><td>Nom * : </td><td><input type=text size=30 name=nom1 value = \"".$nom1."\" /></td></tr>\n";
echo "<tr><td>Prénom * : </td><td><input type=text size=30 name=prenom1 value = \"".$prenom1."\" /></td></tr>\n";
echo "<tr><td>Adresse * : </td><td><input type=text size=100 name=adr1 value = \"".$adr1."\" /></td></tr>\n";
echo "<tr><td>Complément d'adresse : </td><td><input type=text size=100 name=adr1_comp value = \"".$adr1_comp."\" /></td></tr>\n";
echo "<tr><td>Code postal * : </td><td><input type=text size=6 name=cp1 value = \"".$cp1."\" /></td></tr>\n";
echo "<tr><td>Ville * : </td><td><input type=text size=30 name=commune1 value = \"".$commune1."\" /></td></tr>\n";

echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n";

echo "<tr><td><b>Responsable secondaire </b></td><td>\n";
echo "<input type='checkbox' name='meme_adresse' value='oui' onchange='meme_adr()' /> Même adresse\n";


echo "<script type='text/javascript'>
function meme_adr(){
	if(document.forms[0].meme_adresse.checked==true){
		document.forms[0].adr2.value=document.forms[0].adr1.value;
		document.forms[0].adr2_comp.value=document.forms[0].adr1_comp.value;
		document.forms[0].cp2.value=document.forms[0].cp1.value;
		document.forms[0].commune2.value=document.forms[0].commune1.value;
	}
	else{
		document.forms[0].adr2.value='';
		document.forms[0].adr2_comp.value='';
		document.forms[0].cp2.value='';
		document.forms[0].commune2.value='';
	}
}
</script>\n";
echo "</td></tr>\n";
echo "<tr><td>Nom : </td><td><input type=text size=30 name=nom2 value = \"".$nom2."\" /></td></tr>\n";
echo "<tr><td>Prénom : </td><td><input type=text size=30 name=prenom2 value = \"".$prenom2."\" /></td></tr>\n";
echo "<tr><td>Adresse : </td><td><input type=text size=100 name=adr2 value = \"".$adr2."\" /></td></tr>\n";
echo "<tr><td>Complément d'adresse : </td><td><input type=text size=100 name=adr2_comp value = \"".$adr2_comp."\" /></td></tr>\n";
echo "<tr><td>Code postal : </td><td><input type=text size=6 name=cp2 value = \"".$cp2."\" /></td></tr>\n";
echo "<tr><td>Ville : </td><td><input type=text size=30 name=commune2 value = \"".$commune2."\" /></td></tr>\n";


echo "</table>";
echo "<input type=hidden name=is_posted value=1 />";

echo "<br />Les adresses ci-dessus sont utilisées pour l'envoi des bulletins par voie postale.
<br />Si vous n'envoyez pas les bulletins scolaires par voie postale, vous pouvez ignorer cette rubrique.";
echo "<br />Dans le cas contraire : <br />";



if (isset($ereno)) {
echo "<table border=1 width=100%><tr><td><center>";
if (substr($adr1, 0, strlen($adr1)-1) == substr($adr2, 0, strlen($adr1)-1) and ($cp1 == $cp2) and ($commune1 == $commune2)) {
    echo  "Les adresses des deux responsables légaux sont identiques. Par conséquent, <b>le bulletin ne sera envoyé qu'à la première adresse.</b>";
} else {
    if ($adr2 != '') {
        echo   "Les adresses des deux responsables légaux ne sont pas identiques. Par conséquent, <b>le bulletin sera envoyé aux deux responsables légaux.</b>";
    } else {
        echo   "<b>Le bulletin sera envoyé au responsable légal ci-dessus.</b>";
    }
}
 echo "</center></td></tr></table>\n";
}


echo "<br /><br />Les champs marqués d'un astérisque (*) sont obligatoires.<br />";
echo "<center><input type=submit value=Enregistrer /></center>";
?>
</form>
</body>
</html>