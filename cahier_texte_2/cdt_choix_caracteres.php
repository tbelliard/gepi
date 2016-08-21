<?php
/*
 * $Id$
 *
 * Copyright 2001-2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/cdt_choix_caracteres.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/cdt_choix_caracteres.php',
administrateur='F',
professeur='V',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Choix de caractères spéciaux pour le CDT2',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// A faire: revoir l'ordre pour les regrouper par thème.
// Proposer aussi des présélection Espagnol->telle liste, Maths->telle liste, Grec->telle liste,...
$tab_caracteres_html=array('&quot;',
'&apos;',
'&laquo;',
'&raquo;',
'&amp;',

'&iexcl;',
'&iquest;',

'&nbsp;',
'&cent;',
'&pound;',
'&curren;',
'&yen;',
'&brvbar;',
'&sect;',
'&uml;',
'&copy;',
'&ordf;',

'&not;',
'&shy;',
'&reg;',
'&macr;',

'&acute;',
'&micro;',
'&para;',
'&middot;',
'&cedil;',
'&ordm;',

'&Agrave;',
'&Aacute;',
'&Acirc;',
'&Atilde;',
'&Auml;',
'&Aring;',
'&AElig;',
'&Ccedil;',
'&Egrave;',
'&Eacute;',
'&Ecirc;',
'&Euml;',
'&Igrave;',
'&Iacute;',
'&Icirc;',
'&Iuml;',
'&ETH;',
'&Ntilde;',
'&Ograve;',
'&Oacute;',
'&Ocirc;',
'&Otilde;',
'&Ouml;',

'&Ugrave;',
'&Uacute;',
'&Ucirc;',
'&Uuml;',
'&Yacute;',
'&THORN;',
'&szlig;',
'&agrave;',
'&aacute;',
'&acirc;',
'&atilde;',
'&auml;',
'&aring;',
'&aelig;',
'&ccedil;',
'&egrave;',
'&eacute;',
'&ecirc;',
'&euml;',
'&igrave;',
'&iacute;',
'&icirc;',
'&iuml;',
'&eth;',
'&ntilde;',
'&ograve;',
'&oacute;',
'&ocirc;',
'&otilde;',
'&ouml;',

'&ugrave;',
'&uacute;',
'&ucirc;',
'&uuml;',
'&yacute;',
'&thorn;',
'&yuml;',
'&OElig;',
'&oelig;',
'&Scaron;',
'&scaron;',
'&Yuml;',
'&fnof;',
'&circ;',
'&tilde;',
'&Alpha;',
'&Beta;',
'&Gamma;',
'&Delta;',
'&Epsilon;',
'&Zeta;',
'&Eta;',
'&Theta;',
'&Iota;',
'&Kappa;',
'&Lambda;',
'&Mu;',
'&Nu;',
'&Xi;',
'&Omicron;',
'&Pi;',
'&Rho;',
'&Sigma;',
'&Tau;',
'&Upsilon;',
'&Phi;',
'&Chi;',
'&Psi;',
'&Omega;',
'&alpha;',
'&beta;',
'&gamma;',
'&delta;',
'&epsilon;',
'&zeta;',
'&eta;',
'&theta;',
'&iota;',
'&kappa;',
'&lambda;',
'&mu;',
'&nu;',
'&xi;',
'&omicron;',
'&pi;',
'&rho;',
'&sigmaf;',
'&sigma;',
'&tau;',
'&upsilon;',
'&phi;',
'&chi;',
'&psi;',
'&omega;',
'&thetasym;',
'&upsih;',
'&piv;',
'&ndash;',
'&mdash;',
'&lsquo;',
'&rsquo;',
'&sbquo;',
'&ldquo;',
'&rdquo;',
'&bdquo;',
'&dagger;',
'&Dagger;',
'&bull;',
'&hellip;',
'&permil;',
'&prime;',
'&Prime;',
'&lsaquo;',
'&rsaquo;',
'&oline;',
'&frasl;',
'&euro;',
'&image;',
'&weierp;',
'&real;',
'&trade;',
'&alefsym;',
'&larr;',
'&uarr;',
'&rarr;',
'&darr;',
'&harr;',
'&crarr;',
'&lArr;',
'&uArr;',
'&rArr;',
'&dArr;',
'&hArr;',
'&forall;',
'&part;',
'&exist;',
'&empty;',
'&nabla;',
'&isin;',
'&notin;',
'&ni;',
'&prod;',
'&sum;',
'&minus;',
'&lowast;',
'&radic;',
'&prop;',
'&infin;',
'&ang;',
'&and;',
'&or;',
'&cap;',
'&cup;',

'&deg;',
'&plusmn;',
'&sup1;',
'&sup2;',
'&sup3;',
'&frac14;',
'&frac12;',
'&frac34;',
'&times;',
'&divide;',
'&oslash;',
'&Oslash;',

'&int;',
'&there4;',
'&sim;',
'&cong;',
'&asymp;',
'&ne;',
'&equiv;',
'&le;',
'&ge;',
'&lt;',
'&gt;',
'&sub;',
'&sup;',
'&nsub;',
'&sube;',
'&supe;',
'&oplus;',
'&otimes;',
'&perp;',
'&sdot;',
'&lceil;',
'&rceil;',
'&lfloor;',
'&rfloor;',
'&loz;',
'&spades;',
'&clubs;',
'&hearts;',
'&diams;',
'&lang;');

function get_tab_car_spec_cdt2() {

	$tab_car_spec=array();

	$cdt2_car_spec_liste=getPref($_SESSION['login'], "cdt2_car_spec_liste", "");

	if($cdt2_car_spec_liste!="") {

		$tab=explode(';', $cdt2_car_spec_liste);

		for($loop=0;$loop<count($tab);$loop++) {
			$tab_car_spec[]=$tab[$loop].";";
		}
	}

	return $tab_car_spec;
}

if(isset($_POST['caractere'])) {
	check_token();

	$msg="";
	$caractere=$_POST['caractere'];

	// Tous les caractères mis bout à bout occupent 1628 caractères là où mediumtext permet  16777215 (2^24 − 1) caractères
	// https://dev.mysql.com/doc/refman/5.0/fr/string-type-overview.html#idm47771646626656
	$chaine="";
	for($loop=0;$loop<count($caractere);$loop++) {
		$chaine.=$tab_caracteres_html[$caractere[$loop]];
	}
	if(savePref($_SESSION["login"], 'cdt2_car_spec_liste', $chaine)) {
		$msg.="Liste des caractères enregistrée.<br />";
	}
	else {
		$msg.="ERREUR lors de l'enregistrement de la liste des caractères.<br />";
	}

	$cdt2_car_spec_sous_textarea=isset($_POST['cdt2_car_spec_sous_textarea']) ? $_POST['cdt2_car_spec_sous_textarea'] : "no";
	if(savePref($_SESSION["login"], 'cdt2_car_spec_sous_textarea', $cdt2_car_spec_sous_textarea)) {
		$msg.="Préférence d'affichage enregistrée.<br />";
	}
	else {
		$msg.="ERREUR lors de l'enregistrement de la préférence d'affichage.<br />";
	}

}

//**************** EN-TETE *****************
$titre_page = "CDT2 : Caractères spéciaux";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

$checked_cdt2_car_spec_sous_textarea="";

$affichage_cdt2_car_spec_sous_textarea=getPref($_SESSION['login'], "cdt2_car_spec_sous_textarea", "");
if($affichage_cdt2_car_spec_sous_textarea=="yes") {
	$checked_cdt2_car_spec_sous_textarea=" checked";
}

$tab_car_spec=get_tab_car_spec_cdt2();

echo "<p class='bold'><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2>Choix</h2>

<p>Il vous est proposé de réaliser une sélection de caractères spéciaux à faire apparaître en saisie rapide dans vos pages de saisies des cahiers de textes (<em>cahiers de textes en version 2 uniquement</em>).<br />
Les caractères spéciaux sont accessibles via un bouton de l'interface ckEditor, mais pour les caractères spéciaux les plus fréquemment utilisés, il peut être intéressant de disposer d'une liste accessible en un clic.</p>

<form name='change_footer_sound' method='post' action='".$_SERVER['PHP_SELF']."#footer_sound'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."

		<p style='margin-bottom:1em;'>Un bouton <input type='button' name='bouton' value='&Omega;' style='background-color:lightblue;' /> vous permet de faire apparaître une fenêtre infobulle de choix parmi votre sélection de caractères.</p>

		<p style='margin-bottom:1em;'>Faire apparaître en plus le choix&nbsp;:<br />
			<input type='checkbox' name='cdt2_car_spec_sous_textarea' id='cdt2_car_spec_sous_textarea' value='yes' onchange=\"checkbox_change('cdt2_car_spec_sous_textarea')\" $checked_cdt2_car_spec_sous_textarea/><label for='cdt2_car_spec_sous_textarea' id='texte_cdt2_car_spec_sous_textarea'>en liste/ligne sous le champ de saisie</label><br />
		</p>

		<script type='text/javascript'>
			".js_checkbox_change_style("checkbox_change", 'texte_', "n", 1, "yellow")."
			checkbox_change('cdt2_car_spec_infobulle');
			checkbox_change('cdt2_car_spec_liste');
		</script>

		<p>Liste des caractères souhaités&nbsp;:</p>
		<table width='100%'>
			<tr valign='top' align='center'>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td align='left'>";

$nb_car=count($tab_caracteres_html);
$nb_caracteres_par_colonne=round($nb_car/5);
for($loop=0;$loop<$nb_car;$loop++) {
	if(($loop>0)&&(round($loop/$nb_caracteres_par_colonne)==$loop/$nb_caracteres_par_colonne)){
		echo "
				</td>
				<td align='left'>";
	}

	$checked="";
	if(in_array($tab_caracteres_html[$loop], $tab_car_spec)) {
		$checked="checked ";
	}

	echo "<input type='checkbox' name='caractere[]' id='caractere_$loop' value='$loop' $checked/><label for='caractere_$loop' id='texte_caractere_$loop'>".$tab_caracteres_html[$loop]."</label><br />";
}
echo "
				</td>
			</tr>
		</table>

		<p><input type='submit' value='Valider' /></p>

		<script type='text/javascript'>
			for(i=0;i<$nb_car;i++) {
				checkbox_change('caractere_'+i);
			}
		</script>
		";

//echo "<input type='button' value='&equiv;' />";
//echo "<input type='button' value='&radic;' />";

require("../lib/footer.inc.php");

?>
