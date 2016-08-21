<?php
/*
 * $Id$
 *
 * Copyright 2001-2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
//$variables_non_protegees = 'yes';

// Begin standart header

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
include("../ckeditor/ckeditor.php") ;


$sql="SELECT 1=1 FROM droits WHERE id='/bulletin/param_bull_pdf_2016.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/bulletin/param_bull_pdf_2016.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Paramètres des bulletins PDF Réforme CLG 2016',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_bulletins')) {
	header("Location: ../accueil.php?msg=Module bulletins non activé");
	die();
}

$reg_ok = 'yes';
$msg = '';

//debug_var();

$gepi_denom_mention=getSettingValue("gepi_denom_mention");
if($gepi_denom_mention=="") {
	$gepi_denom_mention="mention";
}

if (isset($_POST['is_posted'])) {
	check_token();

	$bull2016_arrondi=isset($_POST["bull2016_arrondi"]) ? $_POST["bull2016_arrondi"] : 0.01;
	if(((!preg_match("/^[0-9]{1,}$/", $bull2016_arrondi))&&
	(!preg_match("/^[0-9]{1,}\.[0-9]{1,}$/", $bull2016_arrondi)))||
	($bull2016_arrondi==0)||
	($bull2016_arrondi=="")) {
		$bull2016_arrondi=0.01;
	}
	if (!saveSetting("bull2016_arrondi", $bull2016_arrondi)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_arrondi !";
		$reg_ok = 'no';
	}

	$bull2016_nb_chiffre_virgule=isset($_POST["bull2016_nb_chiffre_virgule"]) ? $_POST["bull2016_nb_chiffre_virgule"] : 1;
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_nb_chiffre_virgule))||
	($bull2016_nb_chiffre_virgule=="")) {
		$bull2016_nb_chiffre_virgule=1;
	}
	if (!saveSetting("bull2016_nb_chiffre_virgule", $bull2016_nb_chiffre_virgule)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_nb_chiffre_virgule !";
		$reg_ok = 'no';
	}

	$bull2016_chiffre_avec_zero=isset($_POST["bull2016_chiffre_avec_zero"]) ? $_POST["bull2016_chiffre_avec_zero"] : 0;
	if(($bull2016_chiffre_avec_zero!="0")&&($bull2016_chiffre_avec_zero!="1")) {
		$bull2016_chiffre_avec_zero=0;
	}
	if (!saveSetting("bull2016_chiffre_avec_zero", $bull2016_chiffre_avec_zero)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_chiffre_avec_zero !";
		$reg_ok = 'no';
	}

	$bull2016_evolution_moyenne_periode_precedente=isset($_POST["bull2016_evolution_moyenne_periode_precedente"]) ? $_POST["bull2016_evolution_moyenne_periode_precedente"] : "n";
	if(($bull2016_evolution_moyenne_periode_precedente!="y")&&($bull2016_evolution_moyenne_periode_precedente!="n")) {
		$bull2016_evolution_moyenne_periode_precedente="y";
	}
	if (!saveSetting("bull2016_evolution_moyenne_periode_precedente", $bull2016_evolution_moyenne_periode_precedente)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_evolution_moyenne_periode_precedente !";
		$reg_ok = 'no';
	}

	$bull2016_evolution_moyenne_periode_precedente_seuil=isset($_POST["bull2016_evolution_moyenne_periode_precedente_seuil"]) ? $_POST["bull2016_evolution_moyenne_periode_precedente_seuil"] : 0;
	if(((!preg_match("/^[0-9]{1,}$/", $bull2016_evolution_moyenne_periode_precedente_seuil))&&
	(!preg_match("/^[0-9]{1,}\.[0-9]{1,}$/", $bull2016_evolution_moyenne_periode_precedente_seuil)))||
	($bull2016_evolution_moyenne_periode_precedente_seuil=="")) {
		$bull2016_evolution_moyenne_periode_precedente_seuil=0;
	}
	if (!saveSetting("bull2016_evolution_moyenne_periode_precedente_seuil", $bull2016_evolution_moyenne_periode_precedente_seuil)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_evolution_moyenne_periode_precedente_seuil !";
		$reg_ok = 'no';
	}



	$bull2016_affich_mentions=isset($_POST["bull2016_affich_mentions"]) ? $_POST["bull2016_affich_mentions"] : "y";
	if(($bull2016_affich_mentions!="y")&&($bull2016_affich_mentions!="n")) {
		$bull2016_affich_mentions="y";
	}
	if (!saveSetting("bull2016_affich_mentions", $bull2016_affich_mentions)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_affich_mentions !";
		$reg_ok = 'no';
	}

	$bull2016_avec_coches_mentions=isset($_POST["bull2016_avec_coches_mentions"]) ? $_POST["bull2016_avec_coches_mentions"] : "n";
	if(($bull2016_avec_coches_mentions!="y")&&($bull2016_avec_coches_mentions!="n")) {
		$bull2016_avec_coches_mentions="y";
	}
	if (!saveSetting("bull2016_avec_coches_mentions", $bull2016_avec_coches_mentions)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_avec_coches_mentions !";
		$reg_ok = 'no';
	}

	$bull2016_intitule_mentions=isset($_POST["bull2016_intitule_mentions"]) ? $_POST["bull2016_intitule_mentions"] : "n";
	if(($bull2016_intitule_mentions!="y")&&($bull2016_intitule_mentions!="n")) {
		$bull2016_intitule_mentions="y";
	}
	if (!saveSetting("bull2016_intitule_mentions", $bull2016_intitule_mentions)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_intitule_mentions !";
		$reg_ok = 'no';
	}



	//$afficher_nb_heures_perdues="n";

	$bull2016_aff_abs_nj=isset($_POST["bull2016_aff_abs_nj"]) ? $_POST["bull2016_aff_abs_nj"] : "n";
	if(($bull2016_aff_abs_nj!="y")&&($bull2016_aff_abs_nj!="n")) {
		$bull2016_aff_abs_nj="y";
	}
	if (!saveSetting("bull2016_aff_abs_nj", $bull2016_aff_abs_nj)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_aff_abs_nj !";
		$reg_ok = 'no';
	}

	$bull2016_aff_abs_justifiees=isset($_POST["bull2016_aff_abs_justifiees"]) ? $_POST["bull2016_aff_abs_justifiees"] : "n";
	if(($bull2016_aff_abs_justifiees!="y")&&($bull2016_aff_abs_justifiees!="n")) {
		$bull2016_aff_abs_justifiees="y";
	}
	if (!saveSetting("bull2016_aff_abs_justifiees", $bull2016_aff_abs_justifiees)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_aff_abs_justifiees !";
		$reg_ok = 'no';
	}

	$bull2016_aff_total_abs=isset($_POST["bull2016_aff_total_abs"]) ? $_POST["bull2016_aff_total_abs"] : "n";
	if(($bull2016_aff_total_abs!="y")&&($bull2016_aff_total_abs!="n")) {
		$bull2016_aff_total_abs="y";
	}
	if (!saveSetting("bull2016_aff_total_abs", $bull2016_aff_total_abs)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_aff_total_abs !";
		$reg_ok = 'no';
	}

	$bull2016_aff_retards=isset($_POST["bull2016_aff_retards"]) ? $_POST["bull2016_aff_retards"] : "n";
	if(($bull2016_aff_retards!="y")&&($bull2016_aff_retards!="n")) {
		$bull2016_aff_retards="y";
	}
	if (!saveSetting("bull2016_aff_retards", $bull2016_aff_retards)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_aff_retards !";
		$reg_ok = 'no';
	}




	$bull2016_voeux_orientation=isset($_POST["bull2016_voeux_orientation"]) ? $_POST["bull2016_voeux_orientation"] : "n";
	if(($bull2016_voeux_orientation!="y")&&($bull2016_voeux_orientation!="n")) {
		$bull2016_voeux_orientation="y";
	}
	if (!saveSetting("bull2016_voeux_orientation", $bull2016_voeux_orientation)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_voeux_orientation !";
		$reg_ok = 'no';
	}

	$bull2016_orientation_proposee=isset($_POST["bull2016_orientation_proposee"]) ? $_POST["bull2016_orientation_proposee"] : "n";
	if(($bull2016_orientation_proposee!="y")&&($bull2016_orientation_proposee!="n")) {
		$bull2016_orientation_proposee="y";
	}
	if (!saveSetting("bull2016_orientation_proposee", $bull2016_orientation_proposee)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_orientation_proposee !";
		$reg_ok = 'no';
	}

	$bull2016_titre_voeux_orientation=isset($_POST["bull2016_titre_voeux_orientation"]) ? $_POST["bull2016_titre_voeux_orientation"] : "";
	if($bull2016_titre_voeux_orientation=="") {
		$bull2016_titre_voeux_orientation="Voeux";
	}
	if (!saveSetting("bull2016_titre_voeux_orientation", $bull2016_titre_voeux_orientation)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_titre_voeux_orientation !";
		$reg_ok = 'no';
	}

	$bull2016_titre_orientation_proposee=isset($_POST["bull2016_titre_orientation_proposee"]) ? $_POST["bull2016_titre_orientation_proposee"] : "";
	if($bull2016_titre_orientation_proposee=="") {
		$bull2016_titre_orientation_proposee="Orientation proposée";
	}
	if (!saveSetting("bull2016_titre_orientation_proposee", $bull2016_titre_orientation_proposee)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_titre_orientation_proposee !";
		$reg_ok = 'no';
	}

	$bull2016_titre_avis_orientation_proposee=isset($_POST["bull2016_titre_avis_orientation_proposee"]) ? $_POST["bull2016_titre_avis_orientation_proposee"] : "";
	if($bull2016_titre_avis_orientation_proposee=="") {
		$bull2016_titre_avis_orientation_proposee="Commentaire";
	}
	if (!saveSetting("bull2016_titre_avis_orientation_proposee", $bull2016_titre_avis_orientation_proposee)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_titre_avis_orientation_proposee !";
		$reg_ok = 'no';
	}

	$bull2016_orientation_periodes=isset($_POST["bull2016_orientation_periodes"]) ? $_POST["bull2016_orientation_periodes"] : "";
	if($bull2016_orientation_periodes!="") {
		if(!preg_match("/^[0-9;]{1,}$/", $bull2016_orientation_periodes)) {
			$bull2016_orientation_periodes="";
		}
		else {
			$tmp_tab_periode_orientation=explode(";", preg_replace("/[^0-9]/",";",$bull2016_orientation_periodes));
			$bull2016_orientation_periodes="";
			for($loop=0;$loop<count($tmp_tab_periode_orientation);$loop++) {
				if($tmp_tab_periode_orientation[$loop]!="") {
					if($bull2016_orientation_periodes!="") {
						$bull2016_orientation_periodes.=";";
					}
					$bull2016_orientation_periodes.=$tmp_tab_periode_orientation[$loop];
				}
			}
		}
	}
	if (!saveSetting("bull2016_orientation_periodes", $bull2016_orientation_periodes)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_orientation_periodes !";
		$reg_ok = 'no';
	}

}

if (($reg_ok == 'yes') and (isset($_POST['ok']))) {
$msg = "Enregistrement réussi !";
}


$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
// End standart header

//=====================================
$titre_page = "Paramètres de configuration des bulletins PDF Réforme CLG 2016";
require_once("../lib/header.inc.php");
//=====================================

if (!loadSettings()) {
	die("Erreur chargement settings");
}
?>

<script type="text/javascript">
	change='no';
</script>

<p class="bold"><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>
| <a href="./bull_index.php"> Imprimer les bulletins</a>
| <a href="./param_bull.php"> Paramètres d'impression des bulletins HTML</a>
| <a href="./param_bull_pdf.php"> Paramètres d'impression des bulletins PDF</a>
</p>
<?php

if ((($_SESSION['statut']=='professeur') AND ((getSettingValue("GepiProfImprBul")!='yes') OR ((getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")!='yes')))) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")!='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")!='yes')))
{
	die("Droits insuffisants pour effectuer cette opération");
}

$bull2016_arrondi=getSettingValue("bull2016_arrondi");
if(((!preg_match("/^[0-9]{1,}$/", $bull2016_arrondi))&&
(!preg_match("/^[0-9]{1,}\.[0-9]{1,}$/", $bull2016_arrondi)))||
($bull2016_arrondi==0)||
($bull2016_arrondi=="")) {
	$bull2016_arrondi=0.01;
	//echo "Correction de bull2016_arrondi à $bull2016_arrondi";
}

$bull2016_nb_chiffre_virgule=getSettingValue("bull2016_nb_chiffre_virgule");
if((!preg_match("/^[0-9]{1,}$/", $bull2016_nb_chiffre_virgule))||
($bull2016_nb_chiffre_virgule=="")) {
	$bull2016_nb_chiffre_virgule=1;
}

$bull2016_chiffre_avec_zero=getSettingValue("bull2016_chiffre_avec_zero");
if(($bull2016_chiffre_avec_zero!="0")&&($bull2016_chiffre_avec_zero!="1")) {
	$bull2016_chiffre_avec_zero=0;
}

$bull2016_evolution_moyenne_periode_precedente=getSettingValue("bull2016_evolution_moyenne_periode_precedente");
if($bull2016_evolution_moyenne_periode_precedente=="") {
	$bull2016_evolution_moyenne_periode_precedente="y";
}

$bull2016_evolution_moyenne_periode_precedente_seuil=getSettingValue("bull2016_evolution_moyenne_periode_precedente_seuil");
if(((!preg_match("/^[0-9]{1,}$/", $bull2016_evolution_moyenne_periode_precedente_seuil))&&
(!preg_match("/^[0-9]{1,}\.[0-9]{1,}$/", $bull2016_evolution_moyenne_periode_precedente_seuil)))||
($bull2016_evolution_moyenne_periode_precedente_seuil=="")) {
	$bull2016_evolution_moyenne_periode_precedente_seuil=0;
}

$bull2016_affich_mentions=getSettingValue("bull2016_affich_mentions");
if($bull2016_affich_mentions=="") {
	$bull2016_affich_mentions="y";
}

$bull2016_avec_coches_mentions=getSettingValue("bull2016_avec_coches_mentions");
if($bull2016_avec_coches_mentions=="") {
	$bull2016_avec_coches_mentions="y";
}

$bull2016_intitule_mentions=getSettingValue("bull2016_intitule_mentions");
if($bull2016_intitule_mentions=="") {
	$bull2016_intitule_mentions="y";
}

//$afficher_nb_heures_perdues="n";
$bull2016_aff_abs_nj=getSettingValue("bull2016_aff_abs_nj");
if($bull2016_aff_abs_nj=="") {
	$bull2016_aff_abs_nj="y";
}

$bull2016_aff_abs_justifiees=getSettingValue("bull2016_aff_abs_justifiees");
if($bull2016_aff_abs_justifiees=="") {
	$bull2016_aff_abs_justifiees="y";
}

$bull2016_aff_total_abs=getSettingValue("bull2016_aff_total_abs");
if($bull2016_aff_total_abs=="") {
	$bull2016_aff_total_abs="y";
}

$bull2016_aff_retards=getSettingValue("bull2016_aff_retards");
if($bull2016_aff_retards=="") {
	$bull2016_aff_retards="y";
}

$bull2016_titre_voeux_orientation=getSettingValue("bull2016_titre_voeux_orientation");
if($bull2016_titre_voeux_orientation=="") {
	$bull2016_titre_voeux_orientation="Voeux";
}
$bull2016_titre_orientation_proposee=getSettingValue("bull2016_titre_orientation_proposee");
if($bull2016_titre_orientation_proposee=="") {
	$bull2016_titre_orientation_proposee="Orientation proposée";
}
$bull2016_titre_avis_orientation_proposee=getSettingValue("bull2016_titre_avis_orientation_proposee");
if($bull2016_titre_avis_orientation_proposee=="") {
	$bull2016_titre_avis_orientation_proposee="Commentaire";
}

$bull2016_orientation_periodes=getSettingValue("bull2016_orientation_periodes");
?>


<form name="formulaire" action="param_bull_pdf_2016.php" method="post" style="width: 100%;">
<?php
echo add_token_field();
?>
<input type='hidden' name='is_posted' value='y' />
<h3>Paramètres des moyennes</h3>
<table class='boireaus boireaus_alt' summary='Paramètres des moyennes'>
	<tr>
		<td>Arrondi dans le calcul de moyennes&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_arrondi" id="bull2016_arrondi" size="5" onchange="changement()" value="<?php
				echo $bull2016_arrondi;
			?>" onKeyDown="clavier_3(this.id,event, 0.001, 1, 0.001);" />
		</td>
	</tr>
	<tr>
		<td>Nombre de chiffres à droite de la virgule&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_nb_chiffre_virgule" id="bull2016_nb_chiffre_virgule" size="5" onchange="changement()" value="<?php
				echo $bull2016_nb_chiffre_virgule;
			?>" onKeyDown="clavier_2(this.id,event, 0, 5);" />
		</td>
	</tr>
	<tr>
		<td>Afficher les zéros inutiles à droite de la virgule&nbsp;:</td>
		<td>
			<input type="radio" name="bull2016_chiffre_avec_zero" id="bull2016_chiffre_avec_zero_0" onchange="changement()" value="0" <?php
			if(getSettingValue('bull2016_chiffre_avec_zero')!="1") {
				echo "checked ";
			}
			?>/><label for='bull2016_chiffre_avec_zero_0'> Oui</label><br />
			<input type="radio" name="bull2016_chiffre_avec_zero" id="bull2016_chiffre_avec_zero_1" onchange="changement()" value="1" <?php
			if(getSettingValue('bull2016_chiffre_avec_zero')=="1") {
				echo "checked ";
			}
			?>/><label for='bull2016_chiffre_avec_zero_1'> Non</label>
		</td>
	</tr>
	<tr>
		<td>Afficher l'évolution (+/-) par rapport à la période précédente&nbsp;:</td>
		<td>
			<input type="checkbox" name="bull2016_evolution_moyenne_periode_precedente" id="bull2016_evolution_moyenne_periode_precedente" onchange="changement()" value="y" <?php
			if($bull2016_evolution_moyenne_periode_precedente=="y") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td title="Une variation inférieure au nombre proposé ne sera pas considérée comme justifiant l'affichage d'un + ou d'un - indiquant une évolution de la moyenne.">Seuil de la variation pour estimer qu'il y a<br />évolution de la moyenne par rapport à la période précédente&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_evolution_moyenne_periode_precedente_seuil" id="bull2016_evolution_moyenne_periode_precedente_seuil" size="5" onchange="changement()" value="<?php
				echo $bull2016_evolution_moyenne_periode_precedente_seuil;
			?>" onKeyDown="clavier_3(this.id,event, 0.001, 1, 0.001);" />
		</td>
	</tr>
</table>

<h3>Paramètres des <?php echo getSettingValue("gepi_denom_mention");?>s</h3>
<p><em>(sous réserve que des <?php echo getSettingValue("gepi_denom_mention");?>s soient associées aux classes)</em></p>
<table class='boireaus boireaus_alt' summary='Paramètres des mentions'>
	<tr>
		<td>Afficher les <?php echo getSettingValue("gepi_denom_mention");?>s&nbsp;:</td>
		<td>
			<input type="radio" name="bull2016_affich_mentions" id="bull2016_affich_mentions_y" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_affich_mentions')!="n") {
				echo "checked ";
			}
			?>/><label for='bull2016_affich_mentions_y'> Oui</label><br />
			<input type="radio" name="bull2016_affich_mentions" id="bull2016_affich_mentions_n" onchange="changement()" value="n" <?php
			if(getSettingValue('bull2016_affich_mentions')=="n") {
				echo "checked ";
			}
			?>/><label for='bull2016_affich_mentions_n'> Non</label>
		</td>
	</tr>
	<tr>
		<td>Afficher les <?php echo getSettingValue("gepi_denom_mention");?>s sous la forme de cases à cocher&nbsp;:</td>
		<td>
			<input type="checkbox" name="bull2016_avec_coches_mentions" id="bull2016_avec_coches_mentions" onchange="changement()" value="y" <?php
			if($bull2016_avec_coches_mentions!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td>Dans le cas où on n'affiche pas de case à cocher,<br />faire précéder la <?php echo getSettingValue("gepi_denom_mention");?> obtenue de l'intitulé "<?php echo getSettingValue("gepi_denom_mention");?>"&nbsp;:</td>
		<td>
			<input type="checkbox" name="bull2016_intitule_mentions" id="bull2016_intitule_mentions" onchange="changement()" value="y" <?php
			if($bull2016_intitule_mentions!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
</table>

<h3>Paramètres des absences</h3>
<table class='boireaus boireaus_alt' summary='Paramètres des absences'>
	<tr>
		<td>Afficher le nombre d'absences non justifiées&nbsp;:</td>
		<td>
			<input type="checkbox" name="bull2016_aff_abs_nj" id="bull2016_aff_abs_nj" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_aff_abs_nj')!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td>Afficher le nombre d'absences justifiées&nbsp;:</td>
		<td>
			<input type="checkbox" name="bull2016_aff_abs_justifiees" id="bull2016_aff_abs_justifiees" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_aff_abs_justifiees')!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td>Afficher le nombre total d'absences&nbsp;:</td>
		<td>
			<input type="checkbox" name="bull2016_aff_total_abs" id="bull2016_aff_total_abs" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_aff_total_abs')!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td>Afficher le nombre de retards&nbsp;:</td>
		<td>
			<input type="checkbox" name="bull2016_aff_retards" id="bull2016_aff_retards" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_aff_retards')!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
</table>

<h3>Paramètres Orientation</h3>
<p>Sous réserve que le module Orientation soit activé et que l'orientation soit activée pour les MEFS associés à la classe demandée à l'impression.</p>
<table class='boireaus boireaus_alt' summary='Paramètres Orientation'>
	<tr>
		<td>Liste des périodes avec affichage du cadre orientation&nbsp;:<br />
		<em>(laissez vide pour désactiver l'affichage de l'ensemble du cadre Orientation;<br />
		sinon donnez les numéros de périodes, séparés par des point-virgules)</em></td>
		<td>
			<input type="text" name="bull2016_orientation_periodes" id="bull2016_orientation_periodes" size="20" onchange="changement()" value="<?php
				echo $bull2016_orientation_periodes;
			?>" />
		</td>
	</tr>
	<tr>
		<td>Afficher le cadre des Voeux d'orientation&nbsp;:</td>
		<td>
			<input type="checkbox" name="bull2016_voeux_orientation" id="bull2016_voeux_orientation" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_voeux_orientation')!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td>Titre du bloc Voeux&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_titre_voeux_orientation" id="bull2016_titre_voeux_orientation" size="20" onchange="changement()" value="<?php
				echo $bull2016_titre_voeux_orientation;
			?>" />
		</td>
	</tr>
	<tr>
		<td>Afficher le cadre des Orientations proposées&nbsp;:</td>
		<td>
			<input type="checkbox" name="bull2016_orientation_proposee" id="bull2016_orientation_proposee" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_orientation_proposee')!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td>Titre du bloc Orientation proposée&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_titre_orientation_proposee" id="bull2016_titre_orientation_proposee" size="20" onchange="changement()" value="<?php
				echo $bull2016_titre_orientation_proposee;
			?>" />
		</td>
	</tr>
	<tr>
		<td>Titre de l'avis/commentaire sur l'orientation proposée&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_titre_avis_orientation_proposee" id="bull2016_titre_avis_orientation_proposee" size="20" onchange="changement()" value="<?php
				echo $bull2016_titre_avis_orientation_proposee;
			?>" />
		</td>
	</tr>
</table>

<p style="text-align: center;"><input type="submit" name="ok" value="Enregistrer" style="font-variant: small-caps;"/></p>

</form>

<?php require("../lib/footer.inc.php");
