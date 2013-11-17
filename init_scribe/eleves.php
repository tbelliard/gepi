<?php
/*
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

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_eleves;

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des élèves";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

?>
<script type="text/javascript">
<!--
function CocheCase(boul){
  len = document.formulaire.elements.length;
  for( i=0; i<len; i++) {
    if (document.formulaire.elements[i].type=='checkbox') {
      document.formulaire.elements[i].checked = boul ;
    }
  }
 }

function InverseSel(){
  len = document.formulaire.elements.length;
  for( i=0; i<len; i++) {
    if (document.formulaire.elements[i].type=='checkbox') {
      a=!document.formulaire.elements[i].checked  ;
      document.formulaire.elements[i].checked = a
    }
   }
}

function MetVal(cible){
len = document.formulaire.elements.length;
if ( cible== 'nom' ) {
  a=2;
  b=document.formulaire.nom.value;
  } else {
  a=3;
  b=document.formulaire.pour.value;
  }
for( i=0; i<len; i++) {
if ((document.formulaire.elements[i].type=='checkbox')
     &&
    (document.formulaire.elements[i].checked)
    ) {
document.formulaire.elements[i+a].value = b ;
}}}
 // -->
</script>

<?php



echo "<p class='bold'><a href='../init_scribe/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

if (isset($_POST['step'])) {
	check_token(false);

    // L'admin a validé la procédure, on procède donc...
    include "../lib/eole_sync_functions.inc.php";

    // On se connecte au LDAP
    $ldap_server = new LDAPServer;


    //----***** STEP 1 *****-----//

    if ($_POST['step'] == "1") {
        // La première étape consiste à importer les classes

        if ($_POST['record'] == "yes") {
            // Les données ont été postées, on les traite donc immédiatement

            $j=0;
            while ($j < count($liste_tables_del)) {
                if (mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
                    $del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM $liste_tables_del[$j]");
                }
                $j++;
            }

                // On va enregistrer la liste des classes, ainsi que les périodes qui leur seront attribuées
            $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(description=Classe*)");
            $data = ldap_get_entries($ldap_server->ds,$sr);

            for ($i=0;$i<$data["count"];$i++) {

                $classe = $data[$i]["cn"][0];

                // On enregistre la classe
                // On teste d'abord :
                $test = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM classes WHERE (classe='$classe')"),0);

                if ($test == "0") {
                    //$reg_classe = mysql_query("INSERT INTO classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($_POST['reg_nom_complet'][$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($_POST['reg_suivi'][$classe]))."',formule='".traitement_magic_quotes(corriger_caracteres($_POST['reg_formule'][$classe]))."', format_nom='np'");
                    $reg_classe = mysqli_query($GLOBALS["mysqli"], "INSERT INTO classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($_POST['reg_nom_complet'][$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($_POST['reg_suivi'][$classe]))."',formule='".html_entity_decode(traitement_magic_quotes(corriger_caracteres($_POST['reg_formule'][$classe])))."', format_nom='np'");
                } else {
                    //$reg_classe = mysql_query("UPDATE classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($_POST['reg_nom_complet'][$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($_POST['reg_suivi'][$classe]))."',formule='".traitement_magic_quotes(corriger_caracteres($_POST['reg_formule'][$classe]))."', format_nom='np' WHERE classe='$classe'");
                    $reg_classe = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($_POST['reg_nom_complet'][$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($_POST['reg_suivi'][$classe]))."',formule='".html_entity_decode(traitement_magic_quotes(corriger_caracteres($_POST['reg_formule'][$classe])))."', format_nom='np' WHERE classe='$classe'");
                }
                if (!$reg_classe) echo "<p>Erreur lors de l'enregistrement de la classe $classe.";

                // On enregistre les périodes pour cette classe
                // On teste d'abord :
                $id_classe = mysql_result(mysqli_query($GLOBALS["mysqli"], "select id from classes where classe='$classe'"),0,'id');
                $test = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM periodes WHERE (id_classe='$id_classe')"),0);
                if ($test == "0") {
                    $j = '0';
                    while ($j < $_POST['reg_periodes_num'][$classe]) {
                        $num = $j+1;
                        $nom_per = "Période ".$num;
                        if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                        $register = mysqli_query($GLOBALS["mysqli"], "INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                        if (!$register) echo "<p>Erreur lors de l'enregistrement d'une période pour la classe $classe";
                        $j++;
                    }
                } else {
                    // on "démarque" les périodes des classes qui ne sont pas à supprimer
                    $sql = mysqli_query($GLOBALS["mysqli"], "UPDATE periodes SET verouiller='N' where (id_classe='$id_classe' and num_periode='1')");
                    $sql = mysqli_query($GLOBALS["mysqli"], "UPDATE periodes SET verouiller='O' where (id_classe='$id_classe' and num_periode!='1')");
                    //
                    $nb_per = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "select num_periode from periodes where id_classe='$id_classe'"));
                    if ($nb_per > $_POST['reg_periodes_num'][$classe]) {
                        // Le nombre de périodes de la classe est inférieur au nombre enregistré
                        // On efface les périodes en trop
                        $k = 0;
                        for ($k=$_POST['reg_periodes_num'][$classe]+1; $k<$nb_per+1; $k++) {
                            $del = mysqli_query($GLOBALS["mysqli"], "delete from periodes where (id_classe='$id_classe' and num_periode='$k')");
                        }
                    }
                    if ($nb_per < $_POST['reg_periodes_num'][$classe]) {

                        // Le nombre de périodes de la classe est supérieur au nombre enregistré
                        // On enregistre les périodes
                        $k = 0;
                        $num = $nb_per;
                        for ($k=$nb_per+1 ; $k < $_POST['reg_periodes_num'][$classe]+1; $k++) {
                            $num++;
                            $nom_per = "Période ".$num;
                            if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                            $register = mysqli_query($GLOBALS["mysqli"], "INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                            if (!$register) echo "<p>Erreur lors de l'enregistrement d'une période pour la classe $classe";
                        }
                    }
                }
            }

			$sql="update periodes set date_verrouillage='0000-00-00 00:00:00';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if($res) {
				echo "Réinitialisation des dates de verrouillage de périodes effectuée.<br />";
			}
			else {
				echo "Erreur lors de la réinitialisation des dates de verrouillage de périodes.<br />";
			}

            // On efface les classes qui ne sont pas réutilisées cette année  ainsi que les entrées correspondantes dans les groupes
            $sql = mysqli_query($GLOBALS["mysqli"], "select distinct id_classe from periodes where verouiller='T'");
            $k = 0;
            while ($k < mysqli_num_rows($sql)) {
               $id_classe = mysql_result($sql, $k);
               $res1 = mysqli_query($GLOBALS["mysqli"], "delete from classes where id='".$id_classe."'");
               // On supprime les groupes qui étaient liées à la classe
               $get_groupes = mysqli_query($GLOBALS["mysqli"], "SELECT id_groupe FROM j_groupes_classes WHERE id_classe = '" . $id_classe . "'");
               for ($l=0;$l<$nb_groupes;$l++) {
                    $id_groupe = mysql_result($get_groupes, $l, "id_groupe");
                    $delete2 = mysqli_query($GLOBALS["mysqli"], "delete from j_groupes_classes WHERE id_groupe = '" . $id_groupe . "'");
                    // On regarde si le groupe est toujours lié à une autre classe ou pas
                    $check = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM j_groupes_classes WHERE id_groupe = '" . $id_groupe . "'"), 0);
                    if ($check == "0") {
                        $delete1 = mysqli_query($GLOBALS["mysqli"], "delete from groupes WHERE id = '" . $id_groupe . "'");
                        $delete2 = mysqli_query($GLOBALS["mysqli"], "delete from j_groupes_matieres WHERE id_groupe = '" . $id_groupe . "'");
                        $delete2 = mysqli_query($GLOBALS["mysqli"], "delete from j_groupes_professeurs WHERE id_groupe = '" . $id_groupe . "'");
                    }
               }
               $k++;
            }
            $res = mysqli_query($GLOBALS["mysqli"], "delete from periodes where verouiller='T'");
            echo "<p>Vous venez d'effectuer l'enregistrement des données concernant les classes. S'il n'y a pas eu d'erreurs, vous pouvez aller à l'étape suivante pour enregistrer les données concernant les élèves.";
            echo "<center>";
            echo "<form enctype='multipart/form-data' action='eleves.php' method=post name='formulaire'>";
			echo add_token_field();
            echo "<input type=hidden name='record' value='no'>";
            echo "<input type=hidden name='step' value='2'>";
            echo "<input type='submit' value=\"Accéder à l'étape 2\">";
            echo "</form>";
            echo "</center>";

			// On sauvegarde le témoin du fait qu'il va falloir
			// convertir pour générer l'ELE_ID et remplir ensuite les nouvelles tables responsables:
			saveSetting("conv_new_resp_table", 0);

        } else {
            // Les données n'ont pas encore été postées, on affiche donc le tableau des classes

            // On commence par "marquer" les classes existantes dans la base
            $sql = mysqli_query($GLOBALS["mysqli"], "UPDATE periodes SET verouiller='T'");

            $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(description=Classe*)");
            $data = ldap_get_entries($ldap_server->ds,$sr);

            echo "<form enctype='multipart/form-data' action='eleves.php' method=post name='formulaire'>";
			echo add_token_field();
            echo "<input type=hidden name='record' value='yes'>";
            echo "<input type=hidden name='step' value='1'>";

            echo "<p>Les classes en vert indiquent des classes déjà existantes dans la base GEPI.<br />Les classes en rouge indiquent des classes nouvelles et qui vont être ajoutées à la base GEPI.<br /></p>";
            echo "<p>Pour les nouvelles classes, des noms standards sont utilisés pour les périodes (période 1, période 2...), et seule la première période n'est pas verrouillée. Vous pourrez modifier ces paramètres ultérieurement</p>";
            echo "<p>Attention !!! Il n'y a pas de tests sur les champs entrés. Soyez vigilant à ne pas mettre des caractères spéciaux dans les champs ...</p>";
            echo "<p>Essayez de remplir tous les champs, cela évitera d'avoir à le faire ultérieurement.</p>";
            echo "<p>N'oubliez pas <b>d'enregistrer les données</b> en cliquant sur le bouton en bas de la page<br /><br />";

            ?>
            <fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;">
            <legend style="font-variant: small-caps;"> Aide au remplissage </legend>
            <table border="0">
            <tr>
              <td width="2%">&nbsp;</td>
              <td width="2%">&nbsp;</td>
              <td width="2%">&nbsp;</td>
              <td width="2%">&nbsp;</td>
              <td width="25%">&nbsp;</td>
              <td width="53%">&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="5">Vous pouvez remplir les cases <font color="red">
            une à une</font> et/ou <font color="red">globalement</font> grâce aux
            fonctionnalités offertes ci-dessous :</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">1) D'abord, cochez les lignes une à une</td>
            </tr>
              <tr>
              <td colspan="3">&nbsp;</td>
              <td colspan="3">Vous pouvez aussi &nbsp;
              <a href="javascript:CocheCase(true)">
              COCHER</a> ou
              <a href="javascript:CocheCase(false)">
              DECOCHER</a> toutes les lignes , ou
              <a href="javascript:InverseSel()">
              INVERSER </a>la sélection</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">2) Puis, pour les lignes cochées :</td>
            </tr>
             <tr>
              <td colspan="4">&nbsp;</td>
              <td align="right">le nom au bas du bulletin sera &nbsp;:&nbsp;</td>
              <td><input type="text" name="nom" maxlength="80" size="40">
              <input type ="button" name="but_nom" value="Recopier"
            onclick="javascript:MetVal('nom')"></td>
             </td>
            </tr>
             <tr>
              <td colspan="4">&nbsp;</td>
              <td align="right">la formule au bas du bulletin sera
            &nbsp;:&nbsp;</td>
              <td><input type="text" name="pour" maxlength="80" size="40">
              <input type ="button" name="but_pour" value="Recopier"
            onclick="javascript:MetVal('pour')"></td>
             </td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">3) Cliquez sur les boutons "Recopier" pour remplir les champs selectionnés.</td>
            </tr>

            </table>
            </fieldset>
            <br />
            <?php
            echo "<table border=1 cellpadding=2 cellspacing=2>";
            echo "<tr><td><p class=\"small\"><center>Aide<br />Remplissage</center></p></td><td><p class=\"small\">Identifiant de la classe</p></td><td><p class=\"small\">Nom complet</p></td><td><p class=\"small\">Nom apparaissant au bas du bulletin</p></td><td><p class=\"small\">formule au bas du bulletin</p></td><td><p class=\"small\">Nombres de périodes</p></td></tr>";
            for ($i=0;$i<$data["count"];$i++) {
                $classe_id = $data[$i]["cn"][0];
                $test_classe_exist = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes WHERE classe='$classe_id'");
                $nb_test_classe_exist = mysqli_num_rows($test_classe_exist);

                if ($nb_test_classe_exist==0) {
                    $nom_complet = $classe_id;
                    $nom_court = "<font color=red>".$classe_id."</font>";
                    $suivi_par = getSettingValue("gepiAdminPrenom")." ".getSettingValue("gepiAdminNom").", ".getSettingValue("gepiAdminFonction");
                    $formule = "";
                    $nb_per = '3';
                } else {
                    $id_classe = mysql_result($test_classe_exist, 0, 'id');
                    $nb_per = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "select num_periode from periodes where id_classe='$id_classe'"));
                    $nom_court = "<font color=green>".$classe_id."</font>";
                    $nom_complet = mysql_result($test_classe_exist, 0, 'nom_complet');
                    $suivi_par = mysql_result($test_classe_exist, 0, 'suivi_par');
                    $formule = mysql_result($test_classe_exist, 0, 'formule');
                }
                echo "<tr>";
                echo "<td><center><input type=\"checkbox\"></center></td>\n";
                echo "<td>";
                echo "<p><b><center>$nom_court</center></b></p>";
                echo "";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_nom_complet[$classe_id]' value=\"".$nom_complet."\">\n";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_suivi[$classe_id]' value=\"".$suivi_par."\">\n";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_formule[$classe_id]' value=\"".$formule."\">\n";
                echo "</td>";
                echo "<td>";
                echo "<select size=1 name='reg_periodes_num[$classe_id]'>\n";
                for ($k=1;$k<7;$k++) {
                    echo "<option value='$k'";
                    if ($nb_per == "$k") echo " SELECTED";
                    echo ">$k";
                }
                echo "</select>";
                echo "</td></tr>";
            }
            echo "</table>\n";
            echo "<input type=hidden name='step2' value='y'>\n";
            echo "<center><input type='submit' value='Enregistrer les données'></center>\n";
            echo "</form>\n";

        }



    //----***** STEP 2 *****-----//

    } elseif ($_POST['step'] == "2") {
        // La deuxième étape consiste à importer les élèves et à les affecter dans les classes

        // On créé un tableau avec tous les professeurs principaux de chaque classe

        $classes = mysqli_query($GLOBALS["mysqli"], "SELECT id, classe FROM classes");
        $nb_classes = mysqli_num_rows($classes);
        $pp = array();
        for ($i=0;$i<$nb_classes;$i++) {
            $current_classe = mysql_result($classes, $i, "classe");
            $current_classe_id = mysql_result($classes, $i, "id");
            $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(&(objectClass=administrateur)(divcod=" . $current_classe ."))");
            $prof = ldap_get_entries($ldap_server->ds,$sr);
            if (array_key_exists(0, $prof)) {
                $pp[$current_classe_id] = $prof[0]["uid"][0];
            }
        }

        // Debug profs principaux
        //echo "<pre>";
        //print_r($pp);
        //echo "</pre>";

        $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(&(uid=*)(objectClass=Eleves))");
        $info = ldap_get_entries($ldap_server->ds,$sr);

        for($i=0;$i<$info["count"];$i++) {

            // On ajoute l'utilisateur. La fonction s'occupe toute seule de vérifier que
            // le login n'existe pas déjà dans la base. S'il existe, on met simplement à jour
            // les informations

            // function add_eleve($_login, $_nom, $_prenom, $_sexe, $_naissance, $_elenoet) {

            $date_naissance = mb_substr($info[$i]["datenaissance"][0], 0, 4) . "-" .
                                mb_substr($info[$i]["datenaissance"][0], 4, 2) . "-" .
                                mb_substr($info[$i]["datenaissance"][0], 6, 2);

            // -----
            // DEPRECIATION : les lignes ci-dessous ne sont plus nécessaire, Gepi a été mis à jour
            //
            // Pour des raisons de compatibilité avec le code existant de Gepi, il n'est pas possible d'avoir
            // un point dans le login... (le point est transformé bizarrement en "_" dans les $_POST)...

            //$info[$i]["uid"][0] = preg_replace("/\./", "_", $info[$i]["uid"][0]);
			// -----

            // En théorie ici chaque login est de toute façon unique.
            $add = add_eleve($info[$i]["uid"][0],
                            $info[$i]["sn"][0],
                            $info[$i]["givenname"][0],
                            $info[$i]["codecivilite"][0],
                            $date_naissance);
                            //$info[$i]["employeenumber"]);

            $id_classe = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT id FROM classes WHERE classe = '" . $info[$i]["divcod"][0] . "'"), 0);

            $check = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM j_eleves_professeurs WHERE (login = '" . $info[$i]["uid"][0] . "')"), 0);
            if ($check > 0) {
                $del = mysqli_query($GLOBALS["mysqli"], "DELETE from j_eleves_professeurs WHERE login = '" . $info[$i]["uid"][0] . "'");
            }
            if (array_key_exists($id_classe, $pp)) {
                //echo "Debug : $pp[$id_classe]<br/>";
                $res = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_professeurs SET login = '" . $info[$i]["uid"][0] . "', id_classe = '" . $id_classe . "', professeur = '" . $pp[$id_classe] . "'");
            }

            $get_periode_num = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM periodes WHERE (id_classe = '" . $id_classe . "')"), 0);

            $check = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM j_eleves_classes WHERE (login = '" . $info[$i]["uid"][0] . "')"), 0);
            if ($check > 0) {
                $del = mysqli_query($GLOBALS["mysqli"], "DELETE from j_eleves_classes WHERE login = '" . $info[$i]["uid"][0] . "'");
            }

            for ($k=1;$k<$get_periode_num+1;$k++) {
                $res = mysqli_query($GLOBALS["mysqli"], "INSERT into j_eleves_classes SET login = '" . $info[$i]["uid"][0] . "', id_classe = '" . $id_classe . "', periode = '" . $k . "'");
            }

            echo "<br/>Login élève : " . $info[$i]["uid"][0] . "  ---  " . $date_naissance . " --- Classe " . $info[$i]["divcod"][0];
        }

        echo "<p>Opération effectuée.</p>";
        echo "<p>Vous pouvez vérifier l'importation en allant sur la page de <a href='../eleves/index.php'>gestion des eleves</a>.</p>";
        echo "<br />";
        echo "<p><center><a href='professeurs.php'>Phase suivante : importation des professeurs</a></center></p>";
    }

} else {

    echo "<p>L'opération d'importation des élèves depuis le LDAP de Scribe va effectuer les opérations suivantes :</p>";
    echo "<ul>";
    echo "<li>Importation des classes</li>";
    echo "<li>Tentative d'ajout de chaque élèves présent dans le LDAP</li>";
    echo "<li>Si l'utilisateur n'existe pas, il est créé et est directement utilisable</li>";
    echo "<li>Si l'utilisateur existe déjà, ses informations de base sont mises à jour et il passe en état 'actif', devenant directement utilisable</li>";
    echo "<li>Affectation des élèves aux classes</li>";
    echo "</ul>";
    echo "<form enctype='multipart/form-data' action='eleves.php' method=post>";
	echo add_token_field();
    echo "<input type=hidden name='step' value='1'>";
    echo "<input type=hidden name='record' value='no'>";
    $j=0;
    $flag=0;
    while (($j < count($liste_tables_del)) and ($flag==0)) {
        if (mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
            $flag=1;
        }
        $j++;
    }
    if ($flag != 0){
        echo "<p><b>ATTENTION ...</b><br />";
        echo "Des données concernant la constitution des classes et l'affectation des élèves dans les classes sont présentes dans la base GEPI ! Si vous poursuivez la procédure, ces données seront définitivement effacées !</p>";
    }

    echo "<p>Etes-vous sûr de vouloir importer tous les élèves depuis l'annuaire du serveur Scribe vers Gepi ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='Je suis sûr'>";
    echo "</form>";
}
require("../lib/footer.inc.php");
?>
