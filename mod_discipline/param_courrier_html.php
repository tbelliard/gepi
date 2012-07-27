<?php
/*
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Begin standart header
$titre_page = "Paramètres de configuration des bulletins scolaires HTML";

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

// Check access
// SQL : INSERT INTO droits VALUES ( '/mod_discipline/param_courrier_html.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Paramètres courrier HTML', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/param_courrier_html.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Paramètres courrier HTML', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if(mb_strtolower(mb_substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

$reg_ok = 'yes';
$msg = '';
$bgcolor = "#DEDEDE";


//==================================
if (isset($_POST['disc_textsize'])) {

    if (!(preg_match("/^[0-9]{1,}$/", $_POST['disc_textsize'])) || $_POST['disc_textsize'] < 1) {
        $_POST['disc_textsize'] = 10;
    }
    if (!saveSetting("disc_textsize", $_POST['disc_textsize'])) {
        $msg .= "Erreur lors de l'enregistrement de disc_textsize !";
        $reg_ok = 'no';
    }
}

if (isset($_POST['disc_p_html_margin'])) {

    if (!(preg_match("/^[0-9]{1,}$/", $_POST['disc_p_html_margin'])) || $_POST['disc_p_html_margin'] < 1) {
        $_POST['disc_p_html_margin'] = 5;
    }
    if (!saveSetting("disc_p_html_margin", $_POST['disc_p_html_margin'])) {
        $msg .= "Erreur lors de l'enregistrement de disc_p_html_margin !";
        $reg_ok = 'no';
    }
}

if (isset($_POST['disc_body_marginleft'])) {

    if (!(preg_match("/^[0-9]{1,}$/", $_POST['disc_body_marginleft']))) {
        $_POST['disc_body_marginleft'] = 1;
    }
    if (!saveSetting("disc_body_marginleft", $_POST['disc_body_marginleft'])) {
        $msg .= "Erreur lors de l'enregistrement de disc_body_marginleft !";
        $reg_ok = 'no';
    }
}

if (isset($_POST['disc_logo'])) {
    if (!saveSetting("disc_logo", $_POST['disc_logo'])) {
        $msg .= "Erreur lors de l'enregistrement de disc_logo !";
        $reg_ok = 'no';
    }
}

if(isset($_POST['disc_affiche_tel'])) {
    if (!saveSetting("disc_affiche_tel", $_POST['disc_affiche_tel'])) {
        $msg .= "Erreur lors de l'enregistrement de disc_affiche_tel !";
        $reg_ok = 'no';
    }
}

if(isset($_POST['disc_affiche_fax'])) {
    if (!saveSetting("disc_affiche_fax", $_POST['disc_affiche_fax'])) {
        $msg .= "Erreur lors de l'enregistrement de disc_affiche_fax !";
        $reg_ok = 'no';
    }
}

if (isset($_POST['disc_addressblock_padding_right'])) {

    if (!(preg_match("/^[0-9]{1,}$/", $_POST['disc_addressblock_padding_right']))) {
        $_POST['disc_addressblock_padding_right'] = 0;
    }
    if (!saveSetting("disc_addressblock_padding_right", $_POST['disc_addressblock_padding_right'])) {
        $msg .= "Erreur lors de l'enregistrement de disc_addressblock_padding_right !";
        $reg_ok = 'no';
    }
}

if (isset($_POST['disc_addressblock_padding_top'])) {

    if (!(preg_match("/^[0-9]{1,}$/", $_POST['disc_addressblock_padding_top']))) {
        $_POST['disc_addressblock_padding_top'] = 0;
    }
    if (!saveSetting("disc_addressblock_padding_top", $_POST['disc_addressblock_padding_top'])) {
        $msg .= "Erreur lors de l'enregistrement de disc_addressblock_padding_top !";
        $reg_ok = 'no';
    }
}

if (isset($_POST['disc_addressblock_padding_text'])) {

    if (!(preg_match("/^[0-9]{1,}$/", $_POST['disc_addressblock_padding_text']))) {
        $_POST['disc_addressblock_padding_text'] = 0;
    }
    if (!saveSetting("disc_addressblock_padding_text", $_POST['disc_addressblock_padding_text'])) {
        $msg .= "Erreur lors de l'enregistrement de disc_addressblock_padding_text !";
        $reg_ok = 'no';
    }
}

if (isset($_POST['disc_addressblock_length'])) {

    if (!(preg_match("/^[0-9]{1,}$/", $_POST['disc_addressblock_length']))) {
        $_POST['disc_addressblock_length'] = 0;
    }
    if (!saveSetting("disc_addressblock_length", $_POST['disc_addressblock_length'])) {
        $msg .= "Erreur lors de l'enregistrement de disc_addressblock_length !";
        $reg_ok = 'no';
    }
}



if (isset($_POST['disc_affich_nom_etab'])) {
	if($_POST['disc_affich_nom_etab']=="n") {
		$disc_affich_nom_etab="n";
	}
	else{
		$disc_affich_nom_etab="y";
	}
	if (!saveSetting("disc_affich_nom_etab", $disc_affich_nom_etab)) {
		$msg .= "Erreur lors de l'enregistrement de disc_affich_nom_etab !";
		$reg_ok = 'no';
	}
}

if (isset($_POST['disc_affich_adr_etab'])) {
	if($_POST['disc_affich_adr_etab']=="n") {
		$disc_affich_adr_etab="n";
	}
	else{
		$disc_affich_adr_etab="y";
	}
	if (!saveSetting("disc_affich_adr_etab", $disc_affich_adr_etab)) {
		$msg .= "Erreur lors de l'enregistrement de disc_affich_adr_etab !";
		$reg_ok = 'no';
	}
}

//==================================
/*
if (isset($_POST['bull_ecart_entete'])) {

    if (!(ereg ("^[0-9]{1,}$", $_POST['bull_ecart_entete']))) {
        $_POST['bull_ecart_entete'] = 0;
    }
    if (!saveSetting("bull_ecart_entete", $_POST['bull_ecart_entete'])) {
        $msg .= "Erreur lors de l'enregistrement de bull_ecart_entete !";
        $reg_ok = 'no';
    }
}

if (isset($_POST['bull_espace_avis'])) {

    if ((!(ereg ("^[0-9]{1,}$", $_POST['bull_espace_avis']))) or ($_POST['bull_espace_avis'] <= 0)) {
        $_POST['bull_espace_avis'] = 1;
    }
    if (!saveSetting("bull_espace_avis", $_POST['bull_espace_avis'])) {
        $msg .= "Erreur lors de l'enregistrement de bull_espace_avis !";
        $reg_ok = 'no';
    }
}

if (isset($_POST['bull_affiche_formule'])) {

    if (!saveSetting("bull_affiche_formule", $_POST['bull_affiche_formule'])) {
        $msg .= "Erreur lors de l'enregistrement de bull_affiche_formule !";
        $reg_ok = 'no';
    }
}
if (isset($_POST['bull_affiche_signature'])) {

    if (!saveSetting("bull_affiche_signature", $_POST['bull_affiche_signature'])) {
        $msg .= "Erreur lors de l'enregistrement de bull_affiche_signature !";
        $reg_ok = 'no';
    }
}
*/

if (($reg_ok == 'yes') and (isset($_POST['ok']))) {
   $msg = "Enregistrement réussi !";
}


// End standart header
//**************** EN-TETE *****************
$titre_page = "Discipline: Paramètres courriers";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a></p>\n";

/*
if ((($_SESSION['statut']=='professeur') AND ((getSettingValue("GepiProfImprBul")!='yes') OR ((getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")!='yes')))) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")!='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")!='yes')))
{
    die("Droits insuffisants pour effectuer cette opération");
}
*/

/*
disc_body_marginleft
disc_textsize
disc_p_html_margin

disc_logo
disc_affich_nom_etab
disc_affich_adr_etab

disc_affiche_tel
disc_affiche_fax

disc_addressblock_padding_right
disc_addressblock_padding_top
disc_addressblock_padding_text
disc_addressblock_length


*/

echo "<form name='formulaire' action='param_bull.php' method='post' style='width: 100%;'>\n";
echo "<h3>Mise en page des courriers</h3>\n";
echo "<table cellpadding='8' cellspacing='0' width='100%' border='0' summary='Mise en page'>\n";

$nb_ligne=0;

//============================================
echo "<tr";
if ($nb_ligne % 2) {echo "bgcolor=".$bgcolor;}
$nb_ligne++;
echo ">\n";
echo "<td style='font-variant: small-caps;'>\n";
echo "<label for='bull_body_marginleft' style='cursor: pointer;'>Marge gauche de la page (en pixels) :</label>\n";
echo "</td>\n";
echo "<td><input type='text' name='bull_body_marginleft' id='bull_body_marginleft' size='20' value='\n";
if(getSettingValue('bull_body_marginleft')) {
	echo getSettingValue('bull_body_marginleft');
}
else{
	echo 1;
}
echo "' onKeyDown='clavier_2(this.id,event,0,1000);' />\n";
echo "</td>\n";
echo "</tr>\n";
//============================================
echo "<tr";
if ($nb_ligne % 2) {echo "bgcolor=".$bgcolor;}
$nb_ligne++;
echo ">\n";
echo "<td style='font-variant: small-caps;'>\n";
echo "<label for='disc_textsize' style='cursor: pointer;'>Taille en points du texte (en pixels) :</label>\n";
echo "</td>\n";
echo "<td><input type='text' name='disc_textsize' id='disc_textsize' size='20' value='\n";
if(getSettingValue('disc_textsize')) {
	echo getSettingValue('disc_textsize');
}
else{
	echo 10;
}
echo "' onKeyDown='clavier_2(this.id,event,0,1000);' />\n";
echo "</td>\n";
echo "</tr>\n";
//============================================
echo "<tr";
if ($nb_ligne % 2) {echo "bgcolor=".$bgcolor;}
$nb_ligne++;
echo ">\n";
echo "<td style='font-variant: small-caps;'>\n";
echo "<label for='disc_p_html_margin' style='cursor: pointer;'>Marges hautes et basses des paragraphes en points du texte :</label>\n";
echo "</td>\n";
echo "<td><input type='text' name='disc_p_html_margin' id='disc_p_html_margin' size='20' value='\n";
if(getSettingValue('disc_p_html_margin')) {
	echo getSettingValue('disc_p_html_margin');
}
else{
	echo 5;
}
echo "' onKeyDown='clavier_2(this.id,event,0,1000);' />\n";
echo "</td>\n";
echo "</tr>\n";
//============================================

// Tester si un logo est défini

echo "<tr";
if ($nb_ligne % 2) {echo "bgcolor=".$bgcolor;}
$nb_ligne++;
echo ">\n";
echo "<td style='font-variant: small-caps;'>\n";
echo "Afficher le logo de l'établissement :\n";
echo "<br /><span style='color:red;'>Tester si un logo est défini</span>";
echo "</td>\n";

echo "<td style='font-variant: small-caps;'>\n";

if(getSettingValue("disc_logo")){
	$disc_logo=getSettingValue("disc_logo");
}
else{
	$disc_logo="y";
}

echo "<label for='disc_logo_y' style='cursor: pointer;'>\n";
echo "<input type=\"radio\" name=\"disc_logo\" id=\"disc_logo_y\" value=\"y\" ";
if ($disc_logo == 'y') {echo " checked";}
echo " />&nbsp;Oui</label>\n";
echo "<br />\n";
echo "<label for='disc_logo_n' style='cursor: pointer;'>\n";
echo "<input type=\"radio\" name=\"disc_logo\" id=\"disc_logo_n\" value=\"n\" ";
if ($disc_logo == 'n') {echo " checked";}
echo " />&nbsp;Non</label>\n";

echo "</td>\n";
echo "</tr>\n";
//============================================
echo "<tr";
if ($nb_ligne % 2) {echo "bgcolor=".$bgcolor;}
$nb_ligne++;
echo ">\n";
echo "<td style='font-variant: small-caps;'>\n";
echo "Faire apparaitre le nom de l'établissement sur le bulletin :<br />(<i>certains établissements ont le nom dans le Logo</i>) :\n";
echo "</td>\n";

echo "<td style='font-variant: small-caps;'>\n";

if(getSettingValue("disc_affich_nom_etab")){
	$disc_affich_nom_etab=getSettingValue("disc_affich_nom_etab");
}
else{
	$disc_affich_nom_etab="y";
}

echo "<label for='disc_affich_nom_etab_y' style='cursor: pointer;'>\n";
echo "<input type=\"radio\" name=\"disc_affich_nom_etab\" id=\"disc_affich_nom_etab_y\" value=\"y\" ";
if ($disc_affich_nom_etab == 'y') {echo " checked";}
echo " />&nbsp;Oui</label>\n";
echo "<br />\n";
echo "<label for='disc_affich_nom_etab_n' style='cursor: pointer;'>\n";
echo "<input type=\"radio\" name=\"disc_affich_nom_etab\" id=\"disc_affich_nom_etab_n\" value=\"n\" ";
if ($disc_affich_nom_etab == 'n') {echo " checked";}
echo " />&nbsp;Non</label>\n";

echo "</td>\n";
echo "</tr>\n";

//============================================
echo "<tr";
if ($nb_ligne % 2) {echo "bgcolor=".$bgcolor;}
$nb_ligne++;
echo ">\n";
echo "<td style='font-variant: small-caps;'>\n";
echo "Faire apparaitre l'adresse de l'établissement sur le bulletin :<br />(<i>certains établissements ont l'adresse dans le Logo</i>) :\n";
echo "</td>\n";

echo "<td style='font-variant: small-caps;'>\n";

if(getSettingValue("disc_affich_adr_etab")){
	$disc_affich_adr_etab=getSettingValue("disc_affich_adr_etab");
}
else{
	$disc_affich_adr_etab="y";
}

echo "<label for='disc_affich_adr_etab_y' style='cursor: pointer;'>\n";
echo "<input type=\"radio\" name=\"disc_affich_adr_etab\" id=\"disc_affich_adr_etab_y\" value=\"y\" ";
if ($disc_affich_adr_etab == 'y') {echo " checked";}
echo " />&nbsp;Oui</label>\n";
echo "<br />\n";
echo "<label for='disc_affich_adr_etab_n' style='cursor: pointer;'>\n";
echo "<input type=\"radio\" name=\"disc_affich_adr_etab\" id=\"disc_affich_adr_etab_n\" value=\"n\" ";
if ($disc_affich_adr_etab == 'n') {echo " checked";}
echo " />&nbsp;Non</label>\n";

echo "</td>\n";
echo "</tr>\n";
//============================================
echo "<tr";
if ($nb_ligne % 2) {echo "bgcolor=".$bgcolor;}
$nb_ligne++;
echo ">\n";
echo "<td style='font-variant: small-caps;'>\n";
echo "Afficher le numéro de téléphone de l'établissement :\n";
echo "</td>\n";

echo "<td style='font-variant: small-caps;'>\n";

if(getSettingValue("disc_affich_tel")) {
	$disc_affich_tel=getSettingValue("disc_affich_tel");
}
else {
	$disc_affich_tel="y";
}

echo "<label for='disc_affich_tel_y' style='cursor: pointer;'>\n";
echo "<input type=\"radio\" name=\"disc_affich_tel\" id=\"disc_affich_tel_y\" value=\"y\" ";
if ($disc_affich_tel == 'y') {echo " checked";}
echo " />&nbsp;Oui</label>\n";
echo "<br />\n";
echo "<label for='disc_affich_tel_n' style='cursor: pointer;'>\n";
echo "<input type=\"radio\" name=\"disc_affich_tel\" id=\"disc_affich_tel_n\" value=\"n\" ";
if ($disc_affich_tel == 'n') {echo " checked";}
echo " />&nbsp;Non</label>\n";

echo "</td>\n";
echo "</tr>\n";
//============================================
echo "<tr";
if ($nb_ligne % 2) {echo "bgcolor=".$bgcolor;}
$nb_ligne++;
echo ">\n";
echo "<td style='font-variant: small-caps;'>\n";
echo "Afficher le numéro de fax de l'établissement :\n";
echo "</td>\n";

echo "<td style='font-variant: small-caps;'>\n";

if(getSettingValue("disc_affich_fax")) {
	$disc_affich_fax=getSettingValue("disc_affich_fax");
}
else {
	$disc_affich_fax="y";
}

echo "<label for='disc_affich_fax_y' style='cursor: pointer;'>\n";
echo "<input type=\"radio\" name=\"disc_affich_fax\" id=\"disc_affich_fax_y\" value=\"y\" ";
if ($disc_affich_fax == 'y') {echo " checked";}
echo " />&nbsp;Oui</label>\n";
echo "<br />\n";
echo "<label for='disc_affich_fax_n' style='cursor: pointer;'>\n";
echo "<input type=\"radio\" name=\"disc_affich_fax\" id=\"disc_affich_fax_n\" value=\"n\" ";
if ($disc_affich_fax == 'n') {echo " checked";}
echo " />&nbsp;Non</label>\n";

echo "</td>\n";
echo "</tr>\n";
//============================================
echo "</table>\n";
echo "<hr />\n";
echo "<center><input type='submit' name='ok' value='Enregistrer' style='font-variant: small-caps;'/></center>\n";
echo "<hr />\n";
//============================================
echo "<h3>Bloc adresse responsable</h3>\n";

echo "<center><table border='1' cellpadding='10' width='90%' summary='Bloc adresse'><tr><td>
Ces options contrôlent le positionnement du bloc adresse du responsable de l'élève directement sur les courriers.
</td></tr></table></center>\n";

echo "<table cellpadding='8' cellspacing='0' width='100%' border='0' summary='Bloca adresse'>\n";

if(getSettingValue("disc_addressblock_padding_right")) {
	$disc_addressblock_padding_right=getSettingValue("disc_addressblock_padding_right");
}
else {
	$disc_addressblock_padding_right=0;
}
?>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor; ?>>
        <td style="font-variant: small-caps;">
        <label for='disc_addressblock_padding_right' style='cursor: pointer;'>Espace en mm entre la marge droite de la feuille et le bloc "adresse" :</label>
        </td>
        <td><input type="text" name="disc_addressblock_padding_right" id="disc_addressblock_padding_right" size="20" value="<?php echo($disc_addressblock_padding_right); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
     </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td colspan="2"><i>Tenez compte de la marge droite d'impression pour calculer l'espace entre le bord droit de la feuille et le bloc adresse</i></td>
     </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;?>>
        <td style="font-variant: small-caps;">
        <label for='disc_addressblock_padding_top' style='cursor: pointer;'>Espace en mm entre la marge haute de la feuille et le bloc "adresse" :</label>
        </td>
        <td><input type="text" name="disc_addressblock_padding_top" id="disc_addressblock_padding_top" size="20" value="<?php echo(getSettingValue("disc_addressblock_padding_top")); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td colspan="2"><i>Tenez compte de la marge haute d'impression pour calculer l'espace entre le bord haut de la feuille et le bloc adresse</i></td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='disc_addressblock_padding_text' style='cursor: pointer;'>Espace vertical en mm entre le bloc "adresse" et le bloc des résultats :</label>
        </td>
        <td><input type="text" name="disc_addressblock_padding_text" id="disc_addressblock_padding_text" size="20" value="<?php echo(getSettingValue("disc_addressblock_padding_text")); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='disc_addressblock_length' style='cursor: pointer;'>Longueur en mm du bloc "adresse" :</label>
        </td>
        <td><input type="text" name="disc_addressblock_length" id="disc_addressblock_length" size="20" value="<?php echo(getSettingValue("disc_addressblock_length")); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='disc_addressblock_font_size' style='cursor: pointer;'>Taille en points des textes du bloc "adresse" :</label>
        </td>
	<?php
		if(!getSettingValue("disc_addressblock_font_size")){
			$disc_addressblock_font_size=12;
		}
		else{
			$disc_addressblock_font_size=getSettingValue("disc_addressblock_font_size");
		}
	?>
        <td><input type="text" name="disc_addressblock_font_size" id="disc_addressblock_font_size" size="20" value="<?php echo $disc_addressblock_font_size; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='disc_addressblock_logo_etab_prop' style='cursor: pointer;'>Proportion (en % de la largeur de page) allouée au logo et à l'adresse de l'établissement :</label>
        </td>
	<?php
		if(!getSettingValue("disc_addressblock_logo_etab_prop")){
			$disc_addressblock_logo_etab_prop=50;
		}
		else{
			$disc_addressblock_logo_etab_prop=getSettingValue("disc_addressblock_logo_etab_prop");
		}
	?>
        <td><input type="text" name="disc_addressblock_logo_etab_prop" id="disc_addressblock_logo_etab_prop" size="20" value="<?php echo $disc_addressblock_logo_etab_prop; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='disc_addressblock_classe_annee' style='cursor: pointer;'>Proportion (en % de la largeur de page) allouée au bloc "Classe, année, période" :</label>
        </td>
	<?php
		if(!getSettingValue("disc_addressblock_classe_annee")){
			$disc_addressblock_classe_annee=35;
		}
		else{
			$disc_addressblock_classe_annee=getSettingValue("disc_addressblock_classe_annee");
		}
	?>
        <td><input type="text" name="disc_addressblock_classe_annee" id="disc_addressblock_classe_annee" size="20" value="<?php echo $disc_addressblock_classe_annee; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
</table>

<hr />
<p style="text-align: center;"><input type="submit" name="ok" value="Enregistrer" style="font-variant: small-caps;"/></p>
</form>

<?php
require("../lib/footer.inc.php");
?>
