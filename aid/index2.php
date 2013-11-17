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
//Initialisation des variables
$indice_aid = isset($_GET["indice_aid"]) ? $_GET["indice_aid"] : (isset($_POST["indice_aid"]) ? $_POST["indice_aid"] : NULL);
$order_by = isset($_GET["order_by"]) ? $_GET["order_by"] : NULL;

// Vérification du niveau de gestion des AIDs
if (NiveauGestionAid($_SESSION["login"],$indice_aid) <= 0) {
    header("Location: ../logout.php?auto=1");
    die();
}


if ($indice_aid =='') {
    header("Location: index.php");
    die();
}
$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
$nom_aid = @old_mysql_result($call_data, 0, "nom");
$activer_outils_comp = @old_mysql_result($call_data, 0, "outils_complementaires");

if ((NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10) and (isset($_POST["is_posted"]))) {
	check_token();

    // Enregistrement des données
    // On va chercher les aid déjà existantes
    $calldata = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid WHERE indice_aid='$indice_aid'");
    $nombreligne = mysqli_num_rows($calldata);
    $i = 0;
    $msg_inter = "";
    while ($i < $nombreligne){
        $aid_id = @old_mysql_result($calldata, $i, "id");
        // Enregistrement de fiche publique
        if (isset($_POST["fiche_publique_".$aid_id])) {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set fiche_publique='y' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        } else {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set fiche_publique='n' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        };
        if (!$register)
			    $msg_inter .= "Erreur lors de l'enregistrement de la donnée fiche_publique de l'aid $aid_id <br />\n";
        // Enregistrement de eleve_peut_modifier
        if (isset($_POST["eleve_peut_modifier_".$aid_id])) {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set eleve_peut_modifier='y' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        } else {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set eleve_peut_modifier='n' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        };
        if (!$register)
			    $msg_inter .= "Erreur lors de l'enregistrement de la donnée eleve_peut_modifier de l'aid $aid_id <br />\n";
         // Enregistrement de prof_peut_modifier
        if (isset($_POST["prof_peut_modifier_".$aid_id])) {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set prof_peut_modifier='y' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        } else {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set prof_peut_modifier='n' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        };
        if (!$register)
			    $msg_inter .= "Erreur lors de l'enregistrement de la donnée prof_peut_modifier de l'aid $aid_id <br />\n";
        // Enregistrement de cpe_peut_modifier
        if (isset($_POST["cpe_peut_modifier_".$aid_id])) {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set cpe_peut_modifier='y' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        } else {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set cpe_peut_modifier='n' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        };
        if (!$register)
			    $msg_inter .= "Erreur lors de l'enregistrement de la donnée cpe_peut_modifier de l'aid $aid_id <br />\n";

        // Enregistrement de affiche_adresse1
        if (isset($_POST["affiche_adresse1_".$aid_id])) {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set affiche_adresse1='y' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        } else {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set affiche_adresse1='n' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        };
        if (!$register)
			    $msg_inter .= "Erreur lors de l'enregistrement de la donnée affiche_adresse1 de l'aid $aid_id <br />\n";
        // Enregistrement de en_construction
        if (isset($_POST["en_construction_".$aid_id])) {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set en_construction='y' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        } else {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set en_construction='n' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        };
        if (!$register)
			    $msg_inter .= "Erreur lors de l'enregistrement de la donnée en_construction de l'aid $aid_id <br />\n";
        $i++;
    }
    if ($msg_inter == "") {
        $msg = "Les modifications ont été enregistrées.";
    } else {
        $msg = $msg_inter;
    }
}

//**************** EN-TETE *********************
$titre_page = "Gestion des ".$nom_aid;
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************


echo "<p class=bold>";
if ($_SESSION['statut']=="administrateur")
    echo "<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";
else
    echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";
  if (NiveauGestionAid($_SESSION["login"],$indice_aid) >= 5) {
    echo "|<a href=\"add_aid.php?action=add_aid&amp;mode=unique&amp;indice_aid=$indice_aid\">Ajouter un(e) $nom_aid</a>|<a href=\"add_aid.php?action=add_aid&amp;mode=multiple&amp;indice_aid=$indice_aid\">Ajouter des $nom_aid à la chaîne</a>|";
  }
  if (NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10) {
    echo "<a href=\"export_csv_aid.php?indice_aid=$indice_aid\">Importation de données depuis un fichier vers GEPI</a>|";
}
echo "</p>";

if ((NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10) and ($activer_outils_comp == "y"))
    echo "<br /><p class=\"medium\">Les droits d'accès aux différents champs sont configurables pour l'ensemble des AID dans la page <b><i>Gestion des AID -> <a href='./config_aid_fiches_projet.php'>Configurer les fiches projet</a></i></b>.</p>";

echo "<p class=\"medium\">";
// On va chercher les aid déjà existantes, et on les affiche.
if (!isset($order_by)) {$order_by = "numero,nom";}
$calldata = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid WHERE indice_aid='$indice_aid' ORDER BY $order_by");
$nombreligne = mysqli_num_rows($calldata);

if ((NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10) and ($activer_outils_comp == "y"))
    echo "<form action=\"index2.php\" name=\"form1\" method=\"post\">\n";
echo "<table border='1' cellpadding='5' class='boireaus' summary=''>";
echo "<tr><th><p><a href='index2.php?order_by=numero,nom&amp;indice_aid=$indice_aid'>N°</a></p></th>\n";
echo "<th><p><a href='index2.php?order_by=nom&amp;indice_aid=$indice_aid'>Nom</a></p></th>";
// En tete de la colonne "Ajouter, supprimer des professeurs"
if (NiveauGestionAid($_SESSION["login"],$indice_aid) >= 5)
  if(!((getSettingValue("num_aid_trombinoscopes")==$indice_aid) and (getSettingValue("active_module_trombinoscopes")=='y')))
    echo "<th>&nbsp;</th>";
// En tete de la colonne "Ajouter, supprimer des élèves"
echo "<th>&nbsp;</th>";
  // En tete de la colonne "Ajouter, supprimer des gestionnairess"
if (NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10)
  if (getSettingValue("active_mod_gest_aid")=="y")
    echo "<th>&nbsp;</th>";
// colonne publier la fiche
if ((NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10) and ($activer_outils_comp == "y")) {
    echo "<th><p class=\"small\">La fiche est visible sur la <a href=\"javascript:centrerpopup('../public/index_fiches.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">partie publique</a></p>\n";
    echo "<a href=\"javascript:CocheColonne(1);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne(1);changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
    echo "</th>\n";

    echo "<th><p class=\"small\">Les élèves reponsables peuvent modifier la fiche (*)</p>\n";
    echo "<a href=\"javascript:CocheColonne(2);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne(2);changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
    echo "</th>\n";

    echo "<th><p class=\"small\">Les professeurs reponsables peuvent modifier la fiche (*)</p>\n";
    echo "<a href=\"javascript:CocheColonne(3);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne(3);changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
    echo "</th>\n";

    echo "<th><p class=\"small\">Les CPE peuvent modifier la fiche (*)</p>\n";
    echo "<a href=\"javascript:CocheColonne(4);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne(4);changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
    echo "</th>\n";

    echo "<th><p class=\"small\">Le lien \"adresse publique\" est visible sur la partie publique</p>\n";
    echo "<a href=\"javascript:CocheColonne(5);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne(5);changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
    echo "</th>\n";

    echo "<th><p class=\"small\">Le lien \"adresse publique\" est accompagné d'une message \"En construction\"</p>\n";
    echo "<a href=\"javascript:CocheColonne(6);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne(6);changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
    echo "</th>\n";
}
// Colonne "supprimer
if (NiveauGestionAid($_SESSION["login"],$indice_aid) >= 5)
    echo "<th>&nbsp;</th></tr>";

$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
$i = 0;
$alt=1;
while ($i < $nombreligne){
    $aid_nom = @old_mysql_result($calldata, $i, "nom");
    $aid_num = @old_mysql_result($calldata, $i, "numero");
    $eleve_peut_modifier = @old_mysql_result($calldata, $i, "eleve_peut_modifier");
    $prof_peut_modifier = @old_mysql_result($calldata, $i, "prof_peut_modifier");
    $cpe_peut_modifier = @old_mysql_result($calldata, $i, "cpe_peut_modifier");
    $fiche_publique = @old_mysql_result($calldata, $i, "fiche_publique");
    $affiche_adresse1 = @old_mysql_result($calldata, $i, "affiche_adresse1");
    $en_construction = @old_mysql_result($calldata, $i, "en_construction");
    if ($aid_num =='') {$aid_num='&nbsp;';}
    $aid_id = @old_mysql_result($calldata, $i, "id");
    $alt=$alt*(-1);
    // Première colonne du numéro de l'AID
    if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 1)
        echo "<tr class='lig$alt'><td><p class='medium'><b>$aid_num</b></p></td>";
    // Colonne du nom de l'AID
    if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 10)
      if ($activer_outils_comp == "y")
        echo "<td><p class='medium'><a href='modif_fiches.php?aid_id=$aid_id&amp;indice_aid=$indice_aid&amp;action=modif&amp;retour=index2.php'><b>$aid_nom</b></a></p></td>\n";
      else
        echo "<td><p class='medium'><a href='add_aid.php?action=modif_aid&amp;aid_id=$aid_id&amp;indice_aid=$indice_aid'><b>$aid_nom</b></a></p></td>\n";
    else if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 5)
        echo "<td><p class='medium'><a href='add_aid.php?action=modif_aid&amp;aid_id=$aid_id&amp;indice_aid=$indice_aid'><b>$aid_nom</b></a></p></td>\n";
    else if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 1)
      echo "<td><p class='medium'><b>$aid_nom</b></p></td>\n";

    // colonne "Ajouter, supprimer des professeurs"
    if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 5)
      if (!((getSettingValue("num_aid_trombinoscopes")==$indice_aid) and (getSettingValue("active_module_trombinoscopes")=='y')))
        echo "<td><p class='medium'><a href='modify_aid.php?flag=prof&amp;aid_id=$aid_id&amp;indice_aid=$indice_aid'>Ajouter, supprimer des professeurs</a></p></td>\n";
    // colonne "Ajouter, supprimer des élèves"
    if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 1)
        echo "<td><p class='medium'><a href='modify_aid.php?flag=eleve&amp;aid_id=$aid_id&amp;indice_aid=$indice_aid'>Ajouter, supprimer des élèves</a></p></td>\n";
    // colonne "Ajouter, supprimer des gestionnaires"
    if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 10)
      if (getSettingValue("active_mod_gest_aid")=="y")
            echo "<td><p class='medium'><a href='modify_aid.php?flag=prof_gest&amp;aid_id=$aid_id&amp;indice_aid=$indice_aid'>Ajouter, supprimer des gestionnaires</a></p></td>\n";
    if ((NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 10) and ($activer_outils_comp == "y")) {
        // La fiche est-elle publique ?
        echo "<td><center><input type=\"checkbox\" name=\"fiche_publique_".$aid_id."\" value=\"y\" id=\"case_1_".$i."\" ";
        if ($fiche_publique == "y") echo "checked";
        echo " /></center></td>\n";
        // Les élèves peuvent-ils modifier la fiche ?
        echo "<td><center><input type=\"checkbox\" name=\"eleve_peut_modifier_".$aid_id."\" value=\"y\" id=\"case_2_".$i."\" ";
        if ($eleve_peut_modifier == "y") echo "checked";
        echo " /></center></td>\n";
        // Les profs peuvent-ils modifier la fiche ?
        echo "<td><center><input type=\"checkbox\" name=\"prof_peut_modifier_".$aid_id."\" value=\"y\" id=\"case_3_".$i."\" ";
        if ($prof_peut_modifier == "y") echo "checked";
        echo " /></center></td>\n";
        // Les CPE peuvent-ils modifier la fiche ?
        echo "<td><center><input type=\"checkbox\" name=\"cpe_peut_modifier_".$aid_id."\" value=\"y\" id=\"case_4_".$i."\" ";
        if ($cpe_peut_modifier == "y") echo "checked";
        echo " /></center></td>\n";
        // Le lien public est-il visible sur la partie publique ?
        echo "<td><center><input type=\"checkbox\" name=\"affiche_adresse1_".$aid_id."\" value=\"y\" id=\"case_5_".$i."\" ";
        if ($affiche_adresse1 == "y") echo "checked";
        echo " /></center></td>\n";
        // Avertissement "en construction"
        echo "<td><center><input type=\"checkbox\" name=\"en_construction_".$aid_id."\" value=\"y\" id=\"case_6_".$i."\" ";
        if ($en_construction == "y") echo "checked";
        echo " /></center></td>\n";

    }
    // colonne "Supprimer"
    if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 5)
        echo "<td><p class='medium'><a href='../lib/confirm_query.php?liste_cible=$aid_id&amp;liste_cible3=$indice_aid&amp;action=del_aid".add_token_in_url()."'>supprimer</a></p></td></tr>\n";

$i++;
}

?>
</table>
<?php
if ((NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10) and ($activer_outils_comp == "y")) {
  echo "<br />(*) Uniquement si l'administrateur a ouvert cette possibilité pour le projet concerné.";
  echo "<br /><br /><br /><center>\n";
	echo "<div id='fixe'>\n";
  echo "<input type=\"submit\" name=\"Valider\" />";
	echo "</div>\n";
	echo "</center>\n";
  echo "<input type=\"hidden\" name=\"indice_aid\" value=\"".$indice_aid."\" />\n";
  echo "<input type=\"hidden\" name=\"is_posted\" value=\"y\" />\n";

  echo add_token_field();

  echo "</form>\n";
  echo "<script type='text/javascript'>
  function CocheColonne(i) {
	 for (var ki=0;ki<$nombreligne;ki++) {
		if(document.getElementById('case_'+i+'_'+ki)){
			document.getElementById('case_'+i+'_'+ki).checked = true;
		}
	 }
  }
  function DecocheColonne(i) {
	 for (var ki=0;ki<$nombreligne;ki++) {
		if(document.getElementById('case_'+i+'_'+ki)){
			document.getElementById('case_'+i+'_'+ki).checked = false;
		}
	 }
  }
</script>
";
}
require("../lib/footer.inc.php");