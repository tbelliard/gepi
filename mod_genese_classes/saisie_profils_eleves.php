<?php
/*
* Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/saisie_profils_eleves.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/saisie_profils_eleves.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Genèse des classes: Saisie des profils des élèves',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

$sql="CREATE TABLE IF NOT EXISTS gc_eleves_profils (id int(11) unsigned NOT NULL auto_increment, login VARCHAR( 50 ) NOT NULL , profil VARCHAR(10) NOT NULL default 'RAS', PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

//$projet=isset($_POST['projet']) ? $_POST['projet'] : (isset($_GET['projet']) ? $_GET['projet'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$msg="";

include("gc_func.inc.php");
include("lib_gc.php");

if((isset($_GET['mode']))&&($_GET['mode']=="vider")) {
	check_token();
	$sql="DELETE FROM gc_eleves_profils;";
	//echo "$sql<br />";
	$vide=mysqli_query($GLOBALS["mysqli"], $sql);
	$msg.="Nettoyage effectué (".strftime("%d/%m/%Y %H:%M:%S").").<br />";
}

if((isset($_GET['mode']))&&($_GET['mode']=="transferer")) {
	check_token();
	$sql="SELECT * FROM gc_eleves_profils;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$tab_profils_saisis=array();
	while($lig=mysqli_fetch_object($res)) {
		$tab_profils_saisis[$lig->login]=$lig->profil;
	}

	$nb_maj=0;
	$sql="SELECT * FROM gc_eleves_options;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig=mysqli_fetch_object($res)) {
		if((isset($tab_profils_saisis[$lig->login]))&&($tab_profils_saisis[$lig->login]!=$lig->profil)) {
			$sql="UPDATE gc_eleves_options SET profil='".$tab_profils_saisis[$lig->login]."' WHERE login='".$lig->login."';";
			//echo "$sql<br />";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if($update) {
				$nb_maj++;
			}
			else {
				$msg.="Erreur lors de la mise à jour du profil pour ".get_nom_prenom_eleve($lig->login)." dans le projet ".$lig->projet.".<br />";
			}
		}
	}

	$msg.=$nb_maj." mise(s) à jour de profil effectuée(s) (".strftime("%d/%m/%Y %H:%M:%S").").<br />";
}

if(isset($_POST['enregistrer_profils'])) {
	check_token();

	if($_SESSION['statut']=="professeur") {
		$tab_ele_pp=get_tab_ele_clas_pp($_SESSION['login']);
		$pp=retourne_denomination_pp($id_classe);
	}

	$nb_reg=0;
	$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : array();
	$profil=isset($_POST['profil']) ? $_POST['profil'] : array();
	for($loop=0;$loop<count($login_ele);$loop++) {
		$poursuivre="y";
		if(isset($profil[$loop])) {
			if(($_SESSION['statut']=="professeur")&&(!in_array($login_ele[$loop], $tab_ele_pp['login']))) {
				$msg.="Vous n'êtes pas ".$pp." de ".get_nom_prenom_eleve($login_ele[$loop]).".<br />";
				$poursuivre="n";
			}
			if($poursuivre=="y") {
				$sql="SELECT id FROM gc_eleves_profils WHERE login='".$login_ele[$loop]."';";
				//echo "$sql<br />";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$sql="UPDATE gc_eleves_profils SET profil='".$profil[$loop]."' WHERE login='".$login_ele[$loop]."';";
					//echo "$sql<br />";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if($update) {
						$nb_reg++;
					}
					else {
						$msg.="Erreur lors de la mise à jour du profil pour ".get_nom_prenom_eleve($login_ele[$loop]).".<br />";
					}
				}
				else {
					$sql="INSERT INTO gc_eleves_profils SET profil='".$profil[$loop]."', login='".$login_ele[$loop]."';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if($insert) {
						$nb_reg++;
					}
					else {
						$msg.="Erreur lors de l'enregistrement du profil pour ".get_nom_prenom_eleve($login_ele[$loop]).".<br />";
					}
				}
			}
		}
		else {
			$msg.="Profil non précisé pour ".get_nom_prenom_eleve($login_ele[$loop])."<br />";
		}
	}

	$msg.=$nb_reg." enregistrement(s) effectué(s) (".strftime("%d/%m/%Y %H:%M:%S").").";
}

$style_specifique[]="mod_genese_classes/mod_genese_classes";
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Genèse classe: Profils élèves";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

if((isset($_POST['temoin_suhosin_1']))&&(!isset($_POST['temoin_suhosin_2']))) {
	echo "<p style='color:red; font-weight:bold; text-align:center;'>Il semble que certaines variables n'ont pas été transmises.<br />Cela peut arriver lorsqu'on tente de transmettre trop de variables.<br />Vous devriez opter pour un autre mode d'extraction.</p>\n";
	echo "<div style='margin-left:3em; background-image: url(\"../images/background/opacite50.png\");'>";
	echo alerte_config_suhosin();
	echo "</div>\n";
	echo "<p><br /></p>\n";
}

//debug_var();

if($_SESSION['statut']=='administrateur') {
	echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Retour</a>";
}
else {
	echo "<p class='bold'><a href='../accueil.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Retour</a>";
}

if($_SESSION['statut']=='professeur') {
	$tab_pp=get_tab_ele_clas_pp($_SESSION['login']);
	/*
	echo "<pre>";
	print_r($tab_pp);
	echo "</pre>";
	*/
	if(count($tab_pp['id_classe'])==1) {
		$id_classe=$tab_pp['id_classe'][0];
	}
	elseif(isset($id_classe)) {
		echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choisir une autre classe</a>";
	}
}
elseif(isset($id_classe)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choisir une autre classe</a>";
}
echo "</p>\n";

echo "<h2>Profils des élèves</h2>\n";

if(!isset($id_classe)) {
	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form_choix_classe'>
	".add_token_field()."
	<input type='hidden' name='temoin_suhosin_1' value='y' />
	<p>Choisissez&nbsp;:</p>
	<ul>";
	if($_SESSION['statut']=='administrateur') {
		echo "
		<li>
			<p><a href='".$_SERVER['PHP_SELF']."?mode=vider".add_token_in_url()."' onclick=\"return confirm('Attention : L\'opération est irréversible. Êtes-vous sûr de vouloir vider la table des profils élèves ?')\">Vider la table des profils élèves</a></p>
		</li>
		<li>
			<p><a href='".$_SERVER['PHP_SELF']."?mode=transferer".add_token_in_url()."' onclick=\"return confirm('Attention : L\'opération est irréversible. Êtes-vous sûr de vouloir mettre à jour les projets d\'après le contenu de la table des profils élèves ?')\">Remplir les profils élèves dans les projets de Genèse des classes d'après le contenu de la table des profils élèves.</a><br />
			Les saisies de profils préalablement effectués dans les projets de classes futures seront écrasées.<br />
			Les affectactions des élèves dans telle ou telle classe et les options saisies ne seront en revanche pas impactées.</p>
		</li>";
	}

	echo "
		<li>
			<p>Effectuer les saisies pour la classe de&nbsp;: 
				<select name='id_classe'>";
	if(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe'))) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE c.id=p.id_classe ORDER BY c.classe;";
	}
	elseif($_SESSION['statut']=='professeur') {
		$sql="SELECT DISTINCT jec.id_classe AS id, c.classe FROM j_eleves_professeurs jep, j_eleves_classes jec, classes c WHERE jep.professeur='".$_SESSION['login']."' AND jep.login=jec.login AND jec.id_classe=c.id ORDER BY c.classe;";
	}
	$res_clas=mysqli_query($GLOBALS['mysqli'], $sql);
	$liste_sql="";
	while($lig_clas=mysqli_fetch_object($res_clas)) {
		$sql="SELECT 1=1 FROM periodes WHERE id_classe='".$lig_clas->id."';";
		$res_per=mysqli_query($GLOBALS['mysqli'], $sql);
		$nb_per=mysqli_num_rows($res_per);

		$chaine_saisies="";
		$sql="SELECT gc.* FROM eleves e, gc_eleves_profils gc WHERE e.login=gc.login AND e.login IN (SELECT jec.login FROM j_eleves_classes jec WHERE jec.id_classe='".$lig_clas->id."' AND jec.periode='".$nb_per."') ORDER BY e.nom,e.prenom;";
		//echo "$sql<br />";
		$liste_sql.=$lig_clas->classe." ".$sql."<br />";
		$res_profil=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($res_profil)>0) {
			$tmp_tab=array();
			/*
			$tmp_tab["GC"]=0;
			$tmp_tab["C"]=0;
			$tmp_tab["RAS"]=0;
			$tmp_tab["B"]=0;
			$tmp_tab["TB"]=0;
			*/
			for($loop_profil=0;$loop_profil<count($tab_profil);$loop_profil++) {
				$tmp_tab[$tab_profil[$loop_profil]]=0;
			}
			while($lig_profil=mysqli_fetch_object($res_profil)) {
				$tmp_tab[$lig_profil->profil]++;
			}
			foreach($tmp_tab as $key => $value) {
				if($chaine_saisies!="") {
					$chaine_saisies.=" - ";
				}
				else {
					$chaine_saisies.=" (";
				}
				$chaine_saisies.="$key:$value";
			}
			if($chaine_saisies!="") {
				$chaine_saisies.=")";
			}
		}
		echo "
					<option value='".$lig_clas->id."'>".$lig_clas->classe.$chaine_saisies."</option>";
	}
	echo "
				</select><input type='submit' value='Valider' />
			</p>
		</li>
	</ul>
	<input type='hidden' name='temoin_suhosin_2' value='y' />
</form>";
	//echo $liste_sql;
	require("../lib/footer.inc.php");
	die();
}

// La classe est choisie
echo "<h3>Classe de ".get_nom_classe($id_classe)."</h3>";

echo "<p id='p_temoin_modif_non_enregistrees' style='text-align:center; color:red'></p>";

$temoin_erreur_eleves_en_doublon="";
echo "<p id='p_message' style='text-align:center; color:red'></p>";

//echo "<p><a href='".$_SERVER['PHP_SELF']."?projet=$projet'";
echo "<p><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">Rafraichir sans enregistrer</a></p>\n";

$sql="SELECT 1=1 FROM periodes WHERE id_classe='".$id_classe."';";
$res_per=mysqli_query($GLOBALS['mysqli'], $sql);
$nb_per=mysqli_num_rows($res_per);

$tab_profils_saisis=array();
$sql="SELECT gc.* FROM eleves e, gc_eleves_profils gc WHERE e.login=gc.login AND e.login IN (SELECT jec.login FROM j_eleves_classes jec WHERE jec.id_classe='".$id_classe."' AND jec.periode='".$nb_per."') ORDER BY e.nom,e.prenom;";
//echo "$sql<br />";
$res_profil=mysqli_query($GLOBALS['mysqli'], $sql);
if(mysqli_num_rows($res_profil)>0) {
	while($lig_profil=mysqli_fetch_object($res_profil)) {
		$tab_profils_saisis[$lig_profil->login]=$lig_profil->profil;
	}
}

$sql="SELECT e.* FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='".$id_classe."' AND jec.periode='".$nb_per."' ORDER BY e.nom,e.prenom;";
//echo "$sql<br />";
$res_ele=mysqli_query($GLOBALS['mysqli'], $sql);
if(mysqli_num_rows($res_ele)==0) {
	echo "<p style='color:red'>Aucun élève dans cette classe.</p>";
}
else {
	if(!getSettingAOui("mod_genese_classes_profils_v2")) {
		echo "<p>Il n'est pas question ici du niveau de l'élève, mais de son attitude, de son influence positive ou négative sur l'ambiance de classe,...<br />
	Ainsi, TB est à comprendre comme une Très Bonne attitude, un élève moteur pour la classe.</p>";
	}
	else {
		if(acces("/prepa_conseil/visu_toutes_notes.php", $_SESSION['statut'])) {
			echo "<div style='float:right; width:10em;text-align:center;' class='fieldset_opacite50'>
	<a href='../prepa_conseil/visu_toutes_notes.php?mode=pdf&id_classe=".$id_classe."&num_periode=annee&aff_abs=y&aff_reg=y&aff_doub=y&aff_date_naiss=y&aff_moy_cat=y&aff_moy_gen=y&avec_moy_gen_periodes_precedentes=y&forcer_hauteur_ligne_pdf=y&visu_toutes_notes_h_cell_pdf=10
' target='_blank'>Imprimer les moyennes générales annuelles des élèves pour aider à la saisie des niveaux scolaires</a>
</div>";
		}

		echo "<p>Les profils traduisent à la fois le comportement <em>(noté de A (Très bonne attitude) à E (Fortement perturbateur))</em><br />
		et le niveau scolaire <em>(noté de 1 (Très bon élève) à 5 (Niveau faible))</em>.</p>";

/*
	$tab_lettres_profil=array("A", "B", "C", "D", "E");
	$tab_niveau_profil=array(1, 2, 3, 4, 5);
*/
	echo "<table class='boireaus boireaus_alt'>
	<tr>
		<th colspan='2'>
			<p style='text-align:right'>Comportement &rarr;</p>
			<p style='text-align:left'>Niveau scolaire<br />&darr;</p>
		</th>";
	for($loop_profil=0;$loop_profil<count($tab_lettres_profil);$loop_profil++) {
		echo "
		<th>
			".$tmp_tab_profil_traduction[$loop_profil]."<br />
			".$tab_lettres_profil[$loop_profil]."
		</th>";
	}
	echo "
	</tr>";
	for($loop_niveau=0;$loop_niveau<5;$loop_niveau++) {
		echo "
	<tr>
		<th>".$tmp_tab_niveau_traduction[$loop_niveau]."</th>
		<th>".$tab_niveau_profil[$loop_niveau]."</th>";
		for($loop_profil=0;$loop_profil<count($tab_lettres_profil);$loop_profil++) {
			echo "
		<td style='color:".$tab_couleur_profil_assoc[$tab_lettres_profil[$loop_profil].$tab_niveau_profil[$loop_niveau]]."'>
			".$tab_lettres_profil[$loop_profil].$tab_niveau_profil[$loop_niveau]."
		</td>";
		}
		echo "
	</tr>";
	}
	echo "
</table>
<br />";
}

echo "
<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form_saisie_profils'>
	<fieldset class='fieldset_opacite50'>
	".add_token_field()."
	<input type='hidden' name='temoin_suhosin_1' value='y' />
	<input type='hidden' name='id_classe' value='".$id_classe."' />
	<table class='boireaus boireaus_alt'>
		<thead>
			<tr>
				<th rowspan='2'>Élève</th>
				<th rowspan='2'>Profil<br />préalablement<br />saisi</th>
				<th colspan='".count($tab_profil)."'>Choisir un profil</th>
			</tr>
			<tr>";
	for($loop_profil=0;$loop_profil<count($tab_profil);$loop_profil++) {
		echo "
				<th title=\"".$tab_profil_traduction[$loop_profil]."\" style='color:".$tab_couleur_profil_assoc[$tab_profil[$loop_profil]]."'>".$tab_profil[$loop_profil]."</th>";
	}
	echo "
			</tr>
		</thead>
		<tbody>";
	$cpt=0;
	while($lig_ele=mysqli_fetch_object($res_ele)) {
		echo "
			<tr onmouseover=\"this.style.backgroundColor='white'\" onmouseout=\"this.style.backgroundColor=''\">
				<td>";

		$designation_eleve=mb_strtoupper($lig_ele->nom)." ".ucfirst(mb_strtolower($lig_ele->prenom));

		if(nom_photo($lig_ele->elenoet)) {
			echo "<a href='#eleve$cpt' onclick=\"affiche_photo('".nom_photo($lig_ele->elenoet)."','".addslashes(mb_strtoupper($lig_ele->nom)." ".ucfirst(mb_strtolower($lig_ele->prenom)))."', '".$lig_ele->login."');afficher_div('div_photo','y',100,100);return false;\">";
			echo "<span id='nom_prenom_eleve_numero_$cpt' class='col_nom_eleve'>";
			echo $designation_eleve;
			echo "</span>";
			echo "</a>\n";
		}
		else {
			echo "<span id='nom_prenom_eleve_numero_$cpt' class='col_nom_eleve'>";
			echo $designation_eleve;
			echo "</span>";
		}

		echo "
					<input type='hidden' name='login_ele[$cpt]' value=\"".$lig_ele->login."\" />
				</td>
				<td>";
		$profil="";
		$checked=array();
		for($loop_profil=0;$loop_profil<count($tab_profil);$loop_profil++) {
			$checked[$tab_profil[$loop_profil]]="";
		}
		if(isset($tab_profils_saisis[$lig_ele->login])) {
			if(isset($tab_couleur_profil_assoc[$tab_profils_saisis[$lig_ele->login]])) {
				$profil="<span style='color:".$tab_couleur_profil_assoc[$tab_profils_saisis[$lig_ele->login]]."'>".$tab_profils_saisis[$lig_ele->login]."</span>";
			}
			else {
				$profil=$tab_profils_saisis[$lig_ele->login];
			}
			$checked[$tab_profils_saisis[$lig_ele->login]]="checked ";
		}
		else {
			$checked["RAS"]="checked ";
		}
		echo "<div id='div_profil_$cpt'>$profil</div>\n";

		echo "</td>";
		for($loop_profil=0;$loop_profil<count($tab_profil);$loop_profil++) {
			echo "
				<td title=\"".$designation_eleve."\n".$tab_profil_traduction[$loop_profil]."\"><input type='radio' name='profil[$cpt]' value='".$tab_profil[$loop_profil]."' ".$checked[$tab_profil[$loop_profil]]."/></td>";
		}
		echo "
			</tr>";
		$cpt++;
	}
	echo "
		</tbody>
	</table>
	<p style='margin-bottom:1em;'><input type='submit' value='Valider' /></p>
	<br />
	&nbsp;
	<input type='hidden' name='enregistrer_profils' value='y' />
	<input type='hidden' name='temoin_suhosin_2' value='y' />
	</fieldset>
</form>

<script type='text/javascript'>
function affiche_photo(photo,nom_prenom,login_ele) {
	//alert(nom_prenom);
	document.getElementById('entete_div_photo_eleve').innerHTML=nom_prenom;
	//document.getElementById('corps_div_photo_eleve').innerHTML='<img src=\"'+photo+'\" width=\"150\" alt=\"Photo\" /><a href=\"../eleves/visu_eleve.php?ele_login='+login_ele+'\" target=\"_blank\"><img src=\"../images/icons/ele_onglets.png\" class=\"icone16\" alt=\"Onglets élève\" /></a><br />';
	document.getElementById('corps_div_photo_eleve').innerHTML='<img src=\"'+photo+'\" width=\"150\" alt=\"Photo\" /><br />';
}
</script>";

	$titre="<span id='entete_div_photo_eleve'>Elève</span>";
	$texte="<div id='corps_div_photo_eleve' align='center'>\n";
	$texte.="<br />\n";
	$texte.="</div>\n";

	$tabdiv_infobulle[]=creer_div_infobulle('div_photo',$titre,"",$texte,"",14,0,'y','y','n','n');
}
require("../lib/footer.inc.php");
?>
