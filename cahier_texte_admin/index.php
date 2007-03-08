<?php
/*
 * $Id$
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
// Check access
if (!checkAccess()) {
   header("Location: ../logout.php?auto=1");
   die();
}
if (isset($_POST['activer'])) {
    if (!saveSetting("active_cahiers_texte", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
}
if (isset($_POST['cahiers_texte_login_pub'])) {
    $mdp = $_POST['cahiers_texte_passwd_pub'];
    $user_ct = $_POST['cahiers_texte_login_pub'];

    if ((trim($mdp)=='') and (trim($user_ct) !='')) {
        $_POST['cahiers_texte_login_pub'] = '';
        $msg .= "Vous devez choisir un mot de passe.";
    }
    if ((trim($mdp) !='')and (trim($user_ct) == '')) {
       $_POST['cahiers_texte_passwd_pub'] = '';
       $msg .= "Vous devez choisir un identifiant.";
    }

    if (!saveSetting("cahiers_texte_passwd_pub", $_POST['cahiers_texte_passwd_pub'])) $msg .= "Erreur lors de l'enregistrement du mot de passe !";
//    include_once( '../lib/class.htaccess.php' );
    if (!saveSetting("cahiers_texte_login_pub", $_POST['cahiers_texte_login_pub'])) $msg .= "Erreur lors de l'enregistrement du login !";

}

if (isset($_POST['begin_day']) and isset($_POST['begin_month']) and isset($_POST['begin_year'])) {
    $begin_bookings = mktime(0,0,0,$_POST['begin_month'],$_POST['begin_day'],$_POST['begin_year']);
    if (!saveSetting("begin_bookings", $begin_bookings)) $msg .= "Erreur lors de l'enregistrement de begin_bookings !";
}
if (isset($_POST['end_day']) and isset($_POST['end_month']) and isset($_POST['end_year'])) {
    $end_bookings = mktime(0,0,0,$_POST['end_month'],$_POST['end_day'],$_POST['end_year']);
    if (!saveSetting("end_bookings", $end_bookings)) $msg .= "Erreur lors de l'enregistrement de end_bookings !";
}

if (isset($_POST['cahier_texte_acces_public'])) {
	if ($_POST['cahier_texte_acces_public'] == "yes") {
	    $temp = "yes";
	} else {
	    $temp = "no";
	    }
	if (!saveSetting("cahier_texte_acces_public", $temp)) {
	    $msg .= "Erreur lors de l'enregistrement de cahier_texte_acces_public !";
	}
}

if (isset($_POST['is_posted'])) $msg = "Les modifications ont été enregistrées !";
// header
$titre_page = "Gestion des cahiers de texte";
require_once("../lib/header.inc");

if (isset($_POST['delai_devoirs'])) {
    if (!saveSetting("delai_devoirs", $_POST['delai_devoirs'])) $msg .= "Erreur lors de l'enregistrement du délai de visualisation des devoirs";
}

?>
<p class=bold><a href="../accueil_modules.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<H2>Activation des cahiers de texte</H2>
<form action="index.php" name="form1" method="post">
<i>La désactivation des cahiers de texte n'entraîne aucune suppression des données. Lorsque le module est désactivé, les professeurs n'ont pas accès au module et la consultation publique des cahiers de texte est impossible.</i>
<br />
<input type="radio" name="activer" value="y" <?php if (getSettingValue("active_cahiers_texte")=='y') echo " checked"; ?> />
&nbsp;Activer les cahiers de texte (consultation et édition)<br />
<input type="radio" name="activer" value="n" <?php if (getSettingValue("active_cahiers_texte")=='n') echo " checked"; ?> />
&nbsp;Désactiver les cahiers de texte (consultation et édition)
<H2>Début et fin des cahiers de texte</H2>
<i>Seules les rubriques dont la date est comprise entre la date de début et la date de fin des cahiers de texte sont visibles dans
l'interface de consultation publique.
<br />L'édition (modification/suppression/ajout) des cahiers de texte par les utilisateurs de GEPI n'est pas affectée par ces dates.
</i><br />
<table>
     <tr>
        <td>
        Date de début des cahiers de texte :
        </td>
        <td><?php
        $bday = strftime("%d", getSettingValue("begin_bookings"));
        $bmonth = strftime("%m", getSettingValue("begin_bookings"));
        $byear = strftime("%Y", getSettingValue("begin_bookings"));
        genDateSelector("begin_", $bday, $bmonth, $byear,"more_years") ?>
        </td>
    </tr>
    <tr>
        <td>
        Date de fin des cahiers de texte :
        </td>
        <td><?php
        $eday = strftime("%d", getSettingValue("end_bookings"));
        $emonth = strftime("%m", getSettingValue("end_bookings"));
        $eyear= strftime("%Y", getSettingValue("end_bookings"));
        genDateSelector("end_",$eday,$emonth,$eyear,"more_years") ?>
        </td>
    </tr>
</table>
<input type="hidden" name="is_posted" value="1" />
<h2>Accès public</h2>
<input type='radio' name='cahier_texte_acces_public' value='no'<?php if (getSettingValue("cahier_texte_acces_public") == "no") echo " CHECKED";?> /> Désactiver la consultation publique des cahiers de texte (seuls des utilisateurs logués pourront y avoir accès en consultation, s'ils y sont autorisés)<br/>
<input type='radio' name='cahier_texte_acces_public' value='yes'<?php if (getSettingValue("cahier_texte_acces_public") == "yes") echo " CHECKED";?> /> Activer la consultation publique des cahiers de texte (tous les cahiers de textes visibles directement, ou par la saisie d'un login/mdp global)<br/>
<p>-> Accès à l'<a href='../public/index.php' target='_blank'>interface publique de consultation des cahiers de texte</a></p>
<i>En l'absence de mot de passe et d'identifiant, l'accès à l'interface publique de consultation des cahiers de texte est totalement libre.</i>
<br />
Identifiant :
<input type="text" name="cahiers_texte_login_pub" value="<?php echo getSettingValue("cahiers_texte_login_pub"); ?>" size="20" />
<br />Mot de passe :
<input type="text" name="cahiers_texte_passwd_pub" value="<?php echo getSettingValue("cahiers_texte_passwd_pub"); ?>" size="20" />
<H2>Délai de visualisation des devoirs</H2>
<i>Indiquez ici le délai en jours pendant lequel les devoirs seront visibles, à compter du jour de visualisation sélectionné, dans l'interface publique de consulation des cahiers de texte.
<br />Mettre la valeur 0 si vous ne souhaitez pas activer le module de remplissage des devoirs.
Dans ce cas, les professeurs font figurer les devoirs à faire dans la même case que le contenu des séances.
</i>
<br />Délai :
<input type="text" name="delai_devoirs" value="<?php echo getSettingValue("delai_devoirs"); ?>" size="2" /> jours

<br /><br /><center><input type="submit" value="Enregistrer" style="font-variant: small-caps;" /></center>
</form>

<hr />
<H2>Gestion des cahiers de texte</H2>
<ul>
<li><a href='modify_limites.php'>Espace disque maximal, taille maximale d'un fichier</a></li>
<li><a href='modify_type_doc.php'>Types de fichiers autorisés en téléchargement</a></li>
<li><a href='admin_ct.php'>Administration des cahiers de texte</a> (recherche des incohérences, modifications, suppressions)</li>
</ul>
<?php require("../lib/footer.inc.php");?>