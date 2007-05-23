<?php
/*
 * $Id$
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
//**************** EN-TETE *****************************
$titre_page = "Gestion des utilisateurs";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *************************

unset($display);
$display = isset($_POST["display"]) ? $_POST["display"] : (isset($_GET["display"]) ? $_GET["display"] : (getSettingValue("display_users")!='' ? getSettingValue("display_users"): 'tous'));
// on sauve le choix par défaut
saveSetting("display_users", $display);

unset($order_by);
$order_by = isset($_POST["order_by"]) ? $_POST["order_by"] : (isset($_GET["order_by"]) ? $_GET["order_by"] : 'nom,prenom');
$chemin_retour = urlencode($_SERVER['REQUEST_URI']);
$_SESSION['chemin_retour'] = "../utilisateurs/index.php";

unset($mode);
$mode = isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : '');

if ($mode != "personnels") {
?>
<p class=bold>
<a href="../accueil_admin.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
<br/><br/>
<p>Sur cette page, vous pouvez gérer les comptes d'accès des utilisateurs ayant accès à Gepi grâce à un identifiant et un mot de passe.</p>
<p>Cliquez sur le type d'utilisateurs que vous souhaitez gérer :</p>
<p style='padding-left: 10%; margin-top: 15px;'><a href="index.php?mode=personnels"><img src='../images/icons/forward.png' alt='Personnels' class='back_link' /> Personnels de l'établissement (professeurs, scolarité, CPE, administrateurs)</a></p>
<p style='padding-left: 10%; margin-top: 15px;'><a href="edit_responsable.php"><img src='../images/icons/forward.png' alt='Responsables' class='back_link' /> Responsables d'élèves (parents)</a></p>
<p style='padding-left: 10%; margin-top: 15px;'><a href="edit_eleve.php"><img src='../images/icons/forward.png' alt='Eleves' class='back_link' /> Élèves</a></p>
<?php
} else {
?>
<p class=bold>
<a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
 | <a href="modify_user.php">Ajouter utilisateur</a>
<?php

if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon" and getSettingValue('use_sso') != "lcs" and getSettingValue("use_sso") != "ldap_scribe") OR $block_sso) {
    echo " | <a href=\"reset_passwords.php\" onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?\\n Celle-ci est irréversible, et réinitialisera les mots de passe de tous les utilisateurs marqués actifs, avec un mot de passe alpha-numérique généré aléatoirement.\\n En cliquant sur OK, vous lancerez la procédure, qui génèrera une page contenant les fiches-bienvenue à imprimer immédiatement pour distribution aux utilisateurs concernés.')\" target='_blank'>Réinitialiser mots de passe</a>";
}
?>
 | <a href="tab_profs_matieres.php">Affecter les matières aux professeurs</a>
 | <a href="javascript:centrerpopup('help.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')">Aide</a></p>
<p class='small'><a href="import_prof_csv.php">Télécharger le fichier des professeurs au format csv</a>  (nom - prénom - identifiant GEPI)</p>
<form enctype="multipart/form-data" action="index.php" method="post">
<table border=0>
<tr>
<td><p>Afficher : </p></td>
<td><p>tous les utilisateurs <INPUT TYPE="radio" NAME="display" value='tous' <?php if ($display=='tous') {echo " CHECKED";} ?> /></p></td>
<td><p>
 &nbsp;&nbsp;les utilisateurs actifs<INPUT TYPE="radio" NAME="display" value='actifs' <?php if ($display=='actifs') {echo " CHECKED";} ?> /></p></td>
 <td><p>
 &nbsp;&nbsp;les utilisateurs inactifs<INPUT TYPE="radio" NAME="display" value='inactifs' <?php if ($display=='inactifs') {echo " CHECKED";} ?> /></p></td>
 <td><p><input type=submit value=Valider /></p></td>
 </tr>
 </table>
<input type=hidden name='mode' value='<?php echo $mode; ?>' />
<input type=hidden name=order_by value=<?php echo $order_by; ?> />
</form>
<?php
// Affichage du tableau
echo "<table border=1 cellpadding=3>";
echo "<tr><td><p class=small><b><a href='index.php?mode=$mode&amp;order_by=login&amp;display=$display'>Nom de login</a></b></p></td>";
echo "<td><p class=small><b><a href='index.php?mode=$mode&amp;order_by=nom,prenom&amp;display=$display'>Nom et prénom</a></b></p></td>";
echo "<td><p class=small><b><a href='index.php?mode=$mode&amp;order_by=statut,nom,prenom&amp;display=$display'>Statut</a></b></p></td>";
echo "<td><p class=small><b>matière(s) si professeur</b></p></td>";
echo "<td><p class=small><b>classe(s)</b></p></td>";
echo "<td><p class=small><b>suivi</b></p></td>";
echo "<td><p class=small><b>supprimer</b></p></td>";
echo "<td><p class=small><b>imprimer fiche bienvenue</b></p></td>";
echo "</tr>";
$calldata = mysql_query("SELECT * FROM utilisateurs WHERE (" .
		"statut = 'administrateur' OR " .
		"statut = 'professeur' OR " .
		"statut = 'scolarite' OR " .
		"statut = 'cpe' OR " .
		"statut = 'secours') " .
		"ORDER BY $order_by");
$nombreligne = mysql_num_rows($calldata);
$i = 0;

while ($i < $nombreligne){
    $user_nom = mysql_result($calldata, $i, "nom");
    $user_prenom = mysql_result($calldata, $i, "prenom");
    $user_statut = mysql_result($calldata, $i, "statut");
    $user_login = mysql_result($calldata, $i, "login");
    $user_pwd = mysql_result($calldata, $i, "password");
    $user_etat[$i] = mysql_result($calldata, $i, "etat");
//    $date_verrouillage[$i] = mysql_result($calldata, $i, "date_verrouillage");
    if (($user_etat[$i] == 'actif') and (($display == 'tous') or ($display == 'actifs'))) {
        $affiche = 'yes';
    } else if (($user_etat[$i] != 'actif') and (($display == 'tous') or ($display == 'inactifs'))) {
        $affiche = 'yes';
    } else {
        $affiche = 'no';
    }
    if ($affiche == 'yes') {
    // Affichage des login, noms et prénoms
    $col[$i][1] = $user_login;
    $col[$i][2] = "$user_nom $user_prenom";

    $call_matieres = mysql_query("SELECT * FROM j_professeurs_matieres j WHERE j.id_professeur = '$user_login' ORDER BY ordre_matieres");
    $nb_mat = mysql_num_rows($call_matieres);
    $k = 0;
    while ($k < $nb_mat) {
        $user_matiere_id = mysql_result($call_matieres, $k, "id_matiere");
        $user_matiere[$k] = mysql_result(mysql_query("SELECT matiere FROM matieres WHERE matiere='$user_matiere_id'"),0);
        $k++;
    }

    // Affichage du statut
    $col[$i][3]=$user_statut;
    if ($user_statut == "administrateur") { $color_='red';}
    if ($user_statut == "secours") { $color_='red';}
    if ($user_statut == "professeur") { $color_='green'; }
    if ($user_statut != "administrateur" AND $user_statut != "professeur" AND $user_statut != "secours") { $color_='blue';}
    $col[$i][3] = "<font color=".$color_.">".$col[$i][3]."</font>";

    // Cas LCS : on précise le type d'utilisateur (local ou LCS)
    if (getSettingValue("use_sso") == "lcs")
        if ($user_pwd != "")
            $col[$i][3] .= '<br />(utilisateur local)';
        else
            $col[$i][3] .= '<br />(utilisateur LCS)';
    if (($display == 'tous') and ($user_etat[$i]=='inactif')) $col[$i][3] .= '<br />(inactif)';

    // Affichage des enseignements
    $k = 0;
    $col[$i][4] = '';
    while ($k < $nb_mat) {
        $col[$i][4]=$col[$i][4]." $user_matiere[$k] - ";
        $k++;
    }
    if ($col[$i][4]=='') {$col[$i][4] = "&nbsp;";}

    // Affichage des classes
    $call_classes = mysql_query("SELECT g.id group_id, g.name name, c.classe classe, c.id classe_id " .
            "FROM j_groupes_professeurs jgp, j_groupes_classes jgc, groupes g, classes c WHERE (" .
            "jgp.login = '$user_login' and " .
            "g.id = jgp.id_groupe and " .
            "jgc.id_groupe = jgp.id_groupe and " .
            "c.id = jgc.id_classe) order by jgc.id_classe");
    $nb_classes = mysql_num_rows($call_classes);
    $k = 0;
    $col[$i][5] = '';
    while ($k < $nb_classes) {
        $user_classe['classe_nom_court'] = mysql_result($call_classes, $k, "classe");
        $user_classe['matiere_nom_court'] = mysql_result($call_classes, $k, "name");
        $user_classe['classe_id'] = mysql_result($call_classes, $k, "classe_id");
        $user_classe['group_id'] = mysql_result($call_classes, $k, "group_id");

    //======================================
    // MODIF: boireaus
        //$col[$i][5] = $col[$i][5]."<a href='../groupes/edit_group.php?id_classe=".$user_classe["classe_id"] . "&id_groupe=".$user_classe["group_id"] . "'>" . $user_classe['classe_nom_court']." (".$user_classe['matiere_nom_court'].")</a><br />";
        //$col[$i][5] = $col[$i][5]."<a href='../groupes/edit_group.php?id_classe=".$user_classe["classe_id"] . "&amp;id_groupe=".$user_classe["group_id"] . "&amp;retour=oui'>" . $user_classe['classe_nom_court']." (".$user_classe['matiere_nom_court'].")</a><br />";
        $col[$i][5] = $col[$i][5]."<a href='../groupes/edit_group.php?id_classe=".$user_classe["classe_id"] . "&amp;id_groupe=".$user_classe["group_id"] . "&amp;chemin_retour=$chemin_retour'>" . $user_classe['classe_nom_court']." (".$user_classe['matiere_nom_court'].")</a><br />";
    //======================================
        $k++;
    }
    if ($col[$i][5]=='') {$col[$i][5] = "&nbsp;";}

    // Affichage de la classe suivie
    $call_suivi = mysql_query("SELECT distinct(id_classe) FROM j_eleves_professeurs j WHERE j.professeur = '$user_login'");
    $nb_classes_suivies = mysql_num_rows($call_suivi);
    $k = 0;
    $col[$i][6] = '';
    while ($k < $nb_classes_suivies) {
        $user_classe_suivie_id = mysql_result($call_suivi, $k, "id_classe");
        $user_classe_suivie = mysql_result(mysql_query("SELECT classe FROM classes WHERE id='$user_classe_suivie_id'"),0);
        $col[$i][6]=$col[$i][6]."$user_classe_suivie<br />";
        $k++;
    }
    if ($col[$i][6]=='') {$col[$i][6] = "&nbsp;";}

    if ($user_etat[$i] == 'actif') {
        $bgcolor = '#E9E9E4';
    } else {
        //$bgcolor = 'darkgrey';
        //$bgcolor = 'darkgray';
        $bgcolor = '#A9A9A9';
    }

    echo "<tr><td bgcolor='$bgcolor'><p class=small><span class=bold>{$col[$i][1]}</span></p></td>";
    echo "<td bgcolor='$bgcolor'><p class=small><span class=bold><a href='modify_user.php?user_login=$user_login'>{$col[$i][2]}</a></span></p></td>";
    echo "<td bgcolor='$bgcolor'><p class=small><span class=bold>{$col[$i][3]}</span></p></td>";
    echo "<td bgcolor='$bgcolor'><p class=small><span class=bold>{$col[$i][4]}</span></p></td>";
    echo "<td bgcolor='$bgcolor'><p class=small><span class=bold>{$col[$i][5]}</span></p></td>";
    // Affichage de la classe suivie
    echo "<td bgcolor='$bgcolor'><p class=small><span class=bold>{$col[$i][6]}</span></p></td>";
    // Affichage du lien 'supprimer'
    echo "<td bgcolor='$bgcolor'><p class=small><span class=bold><a href='../lib/confirm_query.php?liste_cible={$col[$i][1]}&amp;action=del_utilisateur&amp;chemin_retour=$chemin_retour'>supprimer</a></span></p></td>";
    // Affichage du lien pour l'impression des paramètres
    echo "<td bgcolor='$bgcolor'><p class=small><span class=bold><a target=\"_blank\" href='impression_bienvenue.php?user_login={$col[$i][1]}'>imprimer la 'fiche bienvenue'</a></span></p></td>";
    // Fin de la ligne courante
    echo "</tr>";
    }
    $i++;
}
echo "</table>";

} // Fin : si $mode == personnels
require("../lib/footer.inc.php");
?>