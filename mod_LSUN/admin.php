<?php

/*
*
* Copyright 2016-2017 Régis Bouguin, Stephane Boireau
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 
*/


// Initialisations files
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

include_once 'lib/requetes_tables.php';

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

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";

$sauver = filter_input(INPUT_POST, 'valider') ==="y" ? TRUE : FALSE ;
$ouvre = filter_input(INPUT_POST, 'ouvre') ? filter_input(INPUT_POST, 'ouvre') : 'n';

if(isset($_POST['enregistrer_Saisie_Socle'])) {
	check_token();

	$nb_reg=0;
	if(isset($_POST['SocleSaisieComposantes'])) {
		if(!saveSetting("SocleSaisieComposantes", $_POST['SocleSaisieComposantes'])) {
			$msg.="Erreur lors de l'enregistrement de l'activation/Désactivation du dispositif de saisie des composantes du socle.<br />";
		}
		else {
			$nb_reg++;
		}
	}

	if(isset($_POST['SocleOuvertureSaisieComposantes'])) {
		if(!saveSetting("SocleOuvertureSaisieComposantes", $_POST['SocleOuvertureSaisieComposantes'])) {
			$msg.="Erreur lors de l'ouverture/fermeture des saisies de composantes du socle.<br />";
		}
		else {
			$nb_reg++;
		}
	}

	if(isset($_POST['SocleSaisieComposantesConcurrentes'])) {
		if(!saveSetting("SocleSaisieComposantesConcurrentes", $_POST['SocleSaisieComposantesConcurrentes'])) {
			$msg.="Erreur lors de l'enregistrement du choix pour les saisies concurrentes.<br />";
		}
		else {
			$nb_reg++;
		}
	}

	$tab=array("SocleSaisieComposantes_scolarite", "SocleSaisieComposantes_cpe", "SocleSaisieComposantes_PP", "SocleSaisieComposantes_professeur", "SocleSaisieComposantesForcer_scolarite", "SocleSaisieComposantesForcer_cpe", "SocleSaisieComposantesForcer_PP", "SocleSaisieComposantesForcer_professeur", "SocleOuvertureSaisieComposantes_scolarite", "SocleOuvertureSaisieComposantes_cpe", "SocleSaisieSyntheses_scolarite", "SocleSaisieSyntheses_cpe", "SocleSaisieSyntheses_PP", "SocleSaisieSyntheses_professeur");
	for($loop=0;$loop<count($tab);$loop++) {
		if(isset($_POST[$tab[$loop]])) {
			$valeur="y";
		}
		else {
			$valeur="n";
		}
		if(!saveSetting($tab[$loop], $valeur)) {
			$msg.="Erreur lors de l'enregistrement du paramètre '".$tab[$loop]."'.<br />";
		}
		else {
			$nb_reg++;
		}
	}

	$msg.=$nb_reg." paramètre(s) enregistré(s) <em>(".strftime("le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
}


//==============================================
include_once 'lib/fonctions.php';
//==============================================
if ($sauver) {
	check_token();

	droitLSUN($ouvre);
}
$droit = droitLSUN();


//$droit = DroitSurListeOuvert();

//**************** EN-TETE *****************
$titre_page = "Ouverture du Livret Scolaire Unique";
if (!suivi_ariane($_SERVER['PHP_SELF'],"Ouverture LSU")) {echo "erreur lors de la création du fil d'ariane";}
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

$SocleSaisieComposantes=getSettingAOui("SocleSaisieComposantes");
$SocleOuvertureSaisieComposantes=getSettingAOui("SocleOuvertureSaisieComposantes");
$SocleSaisieComposantesConcurrentes=getSettingValue("SocleSaisieComposantesConcurrentes");
?>

<form action="admin.php" method="post" name="formulaire" id="formulaire">
	<fieldset class='fieldset_opacite50'>
		<?php
			echo add_token_field();
		?>
		<p>
			<input type="radio" 
				   id="ouvreDroit" 
				   name="ouvre"
					<?php if($droit) {echo " checked ";} ?>
				   value="y" 
				   onchange="change_style_radio()" />
			<label for="ouvreDroit" id='texte_ouvreDroit'>Ouverture du module <em>Livret Scolaire Unique</em></label>
		</p>
		
		<p>
			<input type="radio" 
				   id="fermeDroit" 
				   name="ouvre"
					<?php if(!$droit) {echo " checked ";} ?>
				   value="n" 
				   onchange="change_style_radio()" />
			<label for="fermeDroit" id='texte_fermeDroit'>
				Fermer le module LSU
			</label>
		</p>
		<button name="valider" value="y">Valider</button>
		
	</fieldset>
</form>

<br />

<?php
/*
	<domaines-socle>
		<domaine-socle code="CPD_FRA" libelle="Comprendre, s'exprimer en utilisant la langue française à l'oral et à l'écrit" />
		<domaine-socle code="CPD_ETR" libelle="Comprendre, s'exprimer en utilisant une langue étrangère et, le cas échéant, une langue régionale" />
		<domaine-socle code="CPD_SCI" libelle="Comprendre, s'exprimer en utilisant les langages mathématiques, scientifiques et informatiques" />
		<domaine-socle code="CPD_ART" libelle="Comprendre, s'exprimer en utilisant les langages des arts et du corps" />
		<domaine-socle code="MET_APP" libelle="Les méthodes et outils pour apprendre" />
		<domaine-socle code="FRM_CIT" libelle="La formation de la personne et du citoyen" />
		<domaine-socle code="SYS_NAT" libelle="Les systèmes naturels et les systèmes techniques" />
		<domaine-socle code="REP_MND" libelle="Les représentations du monde et l'activité humaine" />
	</domaines-socle>

mysql> select * from nomenclatures_valeurs;
+-----+---------+-------------+----------------------+---------------------------------------------------------+
| id  | type    | code        | nom                  | valeur                                                  |
+-----+---------+-------------+----------------------+---------------------------------------------------------+
|   1 | matiere | 004000      | code_matiere         | 004000                                                  |
|   2 | matiere | 004000      | code_gestion         | IDNCH                                                   |
*/
$tab_domaine_socle=array();
$tab_domaine_socle["CPD_FRA"]="Comprendre, s'exprimer en utilisant la langue française à l'oral et à l'écrit";
$tab_domaine_socle["CPD_ETR"]="Comprendre, s'exprimer en utilisant une langue étrangère et, le cas échéant, une langue régionale";
$tab_domaine_socle["CPD_SCI"]="Comprendre, s'exprimer en utilisant les langages mathématiques, scientifiques et informatiques";
$tab_domaine_socle["CPD_ART"]="Comprendre, s'exprimer en utilisant les langages des arts et du corps";
$tab_domaine_socle["MET_APP"]="Les méthodes et outils pour apprendre";
$tab_domaine_socle["FRM_CIT"]="La formation de la personne et du citoyen";
$tab_domaine_socle["SYS_NAT"]="Les systèmes naturels et les systèmes techniques";
$tab_domaine_socle["REP_MND"]="Les représentations du monde et l'activité humaine";
echo "<p>Contrôle de l'initialisation des domaines du socle&nbsp;:";
$temoin_modif_reg_domaine=0;
foreach($tab_domaine_socle as $code => $libelle) {
	$sql="SELECT * FROM nomenclatures_valeurs WHERE type='domaine_socle' AND code='".$code."';";
	$test=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($test)==0) {
		$sql="INSERT INTO nomenclatures_valeurs SET type='domaine_socle', code='".$code."', nom='".$code."', valeur='".mysqli_real_escape_string($GLOBALS['mysqli'], $libelle)."';";
		$insert=mysqli_query($GLOBALS['mysqli'], $sql);
		if($insert) {
			echo "<br /><span style='color:green'>Enregistrement du code <strong>$code</strong> pour <em>$libelle</em> effectué.</span>";
		}
		else {
			echo "<br /><span style='color:red'>Erreur lors de l'enregistrement du code <strong>$code</strong> pour <em>$libelle</em>.</span>";
		}
		$temoin_modif_reg_domaine++;
	}
	/*
	else {
		$sql="UPDATE nomenclatures_valeurs SET nom='".$code."', valeur='".$libelle."' WHERE type='domaine_socle' AND code='".$code."';";
		$update=mysqli_query($GLOBALS['mysqli'], $sql);
	}
	*/
}
if($temoin_modif_reg_domaine==0) {
	echo " <span style='color:green'>OK.</span>";
}
echo "</p>";
?>
<form action="admin.php" method="post" name="formulaire2" id="formulaire2">
	<fieldset class='fieldset_opacite50'>
		<?php
			echo add_token_field();
		?>
		<p style='margin-left:3em; text-indent:-3em;'>
			Activer/désactiver la possibilité de saisir les Bilans de composantes du socle dans Gepi&nbsp;:<br />
			<input type="radio" 
				   id="SocleSaisieComposantes_y" 
				   name="SocleSaisieComposantes"
					<?php if($SocleSaisieComposantes) {echo " checked ";} ?>
				   value="y" 
				   onchange="change_style_radio()" />
			<label for="SocleSaisieComposantes_y" id='texte_SocleSaisieComposantes_y'>Ouvrir le module de saisie des <em>Bilans de composantes du socle</em></label>
			<br />
			<input type="radio" 
				   id="SocleSaisieComposantes_n" 
				   name="SocleSaisieComposantes"
					<?php if(!$SocleSaisieComposantes) {echo " checked ";} ?>
				   value="n" 
				   onchange="change_style_radio()" />
			<label for="SocleSaisieComposantes_n" id='texte_SocleSaisieComposantes_n'>
				Fermer le module de saisie des <em>Bilans de composantes du socle</em>
			</label>
		</p>

		<p style='margin-top:1em; margin-left:3em; text-indent:-3em;'>
			État d'ouverture ou non de la saisie des Bilans de composantes du socle dans Gepi&nbsp;:<br />
			<input type="radio" 
				   id="SocleOuvertureSaisieComposantes_y" 
				   name="SocleOuvertureSaisieComposantes"
					<?php if($SocleOuvertureSaisieComposantes) {echo " checked ";} ?>
				   value="y" 
				   onchange="change_style_radio()" />
			<label for="SocleOuvertureSaisieComposantes_y" id='texte_SocleOuvertureSaisieComposantes_y'>Saisie des <em>Bilans de composantes du socle</em> ouverte</label>
			<br />
			<input type="radio" 
				   id="SocleOuvertureSaisieComposantes_n" 
				   name="SocleOuvertureSaisieComposantes"
					<?php if(!$SocleOuvertureSaisieComposantes) {echo " checked ";} ?>
				   value="n" 
				   onchange="change_style_radio()" />
			<label for="SocleOuvertureSaisieComposantes_n" id='texte_SocleOuvertureSaisieComposantes_n'>
				Saisie des <em>Bilans de composantes du socle</em> fermée
			</label>
		</p>

		<p style='margin-top:1em; margin-left:3em; text-indent:-3em;'>
			Les profils autorisés à <strong>ouvrir/fermer l'accès à la saisie</strong> <em>(en plus des comptes administrateurs)</em>&nbsp;:<br />
			<input type="checkbox" 
				   id="SocleOuvertureSaisieComposantes_scolarite" 
				   name="SocleOuvertureSaisieComposantes_scolarite"
					<?php if(getSettingAOui("SocleOuvertureSaisieComposantes_scolarite")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleOuvertureSaisieComposantes_scolarite" id='texte_SocleOuvertureSaisieComposantes_scolarite'>
				les comptes Scolarité
			</label>
			<br />

			<input type="checkbox" 
				   id="SocleOuvertureSaisieComposantes_cpe" 
				   name="SocleOuvertureSaisieComposantes_cpe"
					<?php if(getSettingAOui("SocleOuvertureSaisieComposantes_cpe")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleOuvertureSaisieComposantes_cpe" id='texte_SocleOuvertureSaisieComposantes_cpe'>
				les comptes CPE
			</label>
		</p>

		<p style='margin-top:1em; margin-left:3em; text-indent:-3em;'>
			Les profils autorisés à <strong>saisir les bilans</strong> sont&nbsp;:<br />
			<input type="checkbox" 
				   id="SocleSaisieComposantes_scolarite" 
				   name="SocleSaisieComposantes_scolarite"
					<?php if(getSettingAOui("SocleSaisieComposantes_scolarite")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleSaisieComposantes_scolarite" id='texte_SocleSaisieComposantes_scolarite'>
				les comptes Scolarité associés à la classe
			</label>
			<br />

			<input type="checkbox" 
				   id="SocleSaisieComposantes_cpe" 
				   name="SocleSaisieComposantes_cpe"
					<?php if(getSettingAOui("SocleSaisieComposantes_cpe")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleSaisieComposantes_cpe" id='texte_SocleSaisieComposantes_cpe'>
				les comptes CPE associés à la classe
			</label>
			<br />

			<input type="checkbox" 
				   id="SocleSaisieComposantes_PP" 
				   name="SocleSaisieComposantes_PP"
					<?php if(getSettingAOui("SocleSaisieComposantes_PP")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleSaisieComposantes_PP" id='texte_SocleSaisieComposantes_PP'>
				les comptes <?php echo getSettingValue("gepi_prof_suivi");?> associés à la classe
			</label>
			<br />

			<input type="checkbox" 
				   id="SocleSaisieComposantes_professeur" 
				   name="SocleSaisieComposantes_professeur"
					<?php if(getSettingAOui("SocleSaisieComposantes_professeur")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleSaisieComposantes_professeur" id='texte_SocleSaisieComposantes_professeur'>
				les comptes Professeurs associés à la classe
			</label>
		</p>

		<p style='margin-top:1em; margin-left:3em; text-indent:-3em;'>
			En cas de saisies simultanées concurrentes&nbsp;:<br />
			<input type="radio" 
				   id="SocleSaisieComposantesConcurrentes_derniere" 
				   name="SocleSaisieComposantesConcurrentes"
					<?php if($SocleSaisieComposantesConcurrentes!="meilleure") {echo " checked ";} ?>
				   value="derniere" 
				   onchange="change_style_radio()" />
			<label for="SocleSaisieComposantesConcurrentes_derniere" id='texte_SocleSaisieComposantesConcurrentes_derniere'>Retenir la dernière saisie effectuée.</label>
			<br />
			<input type="radio" 
				   id="SocleSaisieComposantesConcurrentes_meilleure" 
				   name="SocleSaisieComposantesConcurrentes"
					<?php if($SocleSaisieComposantesConcurrentes=="meilleure") {echo " checked ";} ?>
				   value="meilleure" 
				   onchange="change_style_radio()" />
			<label for="SocleSaisieComposantesConcurrentes_meilleure" id='texte_SocleSaisieComposantesConcurrentes_meilleure'>
				Retenir la saisie la plus favorable à l'élève.
			</label>
		</p>

		<p style='margin-top:1em; margin-left:3em; text-indent:-3em;'>
			Dans le cas où on retient la saisie la plus favorable à l'élève, permettre aux statuts suivants de forcer la saisie pour baisser le niveau de maitrise de l'élève.<br />
			<input type="checkbox" 
				   id="SocleSaisieComposantesForcer_scolarite" 
				   name="SocleSaisieComposantesForcer_scolarite"
					<?php if(getSettingAOui("SocleSaisieComposantesForcer_scolarite")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleSaisieComposantesForcer_scolarite" id='texte_SocleSaisieComposantesForcer_scolarite'>
				Comptes Scolarité associés à la classe
			</label>
			<br />
			<input type="checkbox" 
				   id="SocleSaisieComposantesForcer_cpe" 
				   name="SocleSaisieComposantesForcer_cpe"
					<?php if(getSettingAOui("SocleSaisieComposantesForcer_cpe")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleSaisieComposantesForcer_cpe" id='texte_SocleSaisieComposantesForcer_cpe'>
				Comptes CPE associés à la classe
			</label>
			<br />
			<input type="checkbox" 
				   id="SocleSaisieComposantesForcer_PP" 
				   name="SocleSaisieComposantesForcer_PP"
					<?php if(getSettingAOui("SocleSaisieComposantesForcer_PP")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleSaisieComposantesForcer_PP" id='texte_SocleSaisieComposantesForcer_PP'>
				Comptes <?php echo getSettingValue("gepi_prof_suivi");?> associés à la classe
			</label>
			<br />
			<input type="checkbox" 
				   id="SocleSaisieComposantesForcer_professeur" 
				   name="SocleSaisieComposantesForcer_professeur"
					<?php if(getSettingAOui("SocleSaisieComposantesForcer_professeur")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleSaisieComposantesForcer_professeur" id='texte_SocleSaisieComposantesForcer_professeur'>
				Tous les comptes professeurs associés à la classe
			</label>
		</p>

		<p style='margin-top:1em; margin-left:3em; text-indent:-3em;'>
			Les profils autorisés à <strong>saisir la synthèse pour chaque élève</strong> sont&nbsp;:<br />
			<input type="checkbox" 
				   id="SocleSaisieSyntheses_scolarite" 
				   name="SocleSaisieSyntheses_scolarite"
					<?php if(getSettingAOui("SocleSaisieSyntheses_scolarite")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleSaisieSyntheses_scolarite" id='texte_SocleSaisieSyntheses_scolarite'>
				les comptes Scolarité associés à la classe
			</label>
			<br />

			<input type="checkbox" 
				   id="SocleSaisieSyntheses_cpe" 
				   name="SocleSaisieSyntheses_cpe"
					<?php if(getSettingAOui("SocleSaisieSyntheses_cpe")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleSaisieSyntheses_cpe" id='texte_SocleSaisieSyntheses_cpe'>
				les comptes CPE associés à la classe
			</label>
			<br />

			<input type="checkbox" 
				   id="SocleSaisieSyntheses_PP" 
				   name="SocleSaisieSyntheses_PP"
					<?php if(getSettingAOui("SocleSaisieSyntheses_PP")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleSaisieSyntheses_PP" id='texte_SocleSaisieSyntheses_PP'>
				les comptes <?php echo getSettingValue("gepi_prof_suivi");?> associés à la classe
			</label>
			<br />

			<input type="checkbox" 
				   id="SocleSaisieSyntheses_professeur" 
				   name="SocleSaisieSyntheses_professeur"
					<?php if(getSettingAOui("SocleSaisieSyntheses_professeur")) {echo " checked ";} ?>
				   value="y" 
				   onchange="checkbox_change(this.id)" />
			<label for="SocleSaisieSyntheses_professeur" id='texte_SocleSaisieSyntheses_professeur'>
				les comptes Professeurs associés à la classe
			</label>
			<br />
			Limiter les saisies aux comptes Scolarité, CPE et/ou <?php echo getSettingValue("gepi_prof_suivi");?> parait raisonnable.
		</p>

		<input type="hidden" name="enregistrer_Saisie_Socle" value="y" />
		<input type="submit" value="Valider" />

	</fieldset>
</form>
<?php
//debug_var();
echo "<script type='text/javascript'>
".js_change_style_radio("change_style_radio", "n", "y")."

change_style_radio();

item=document.getElementsByTagName('input');
for(i=0;i<item.length;i++) {
	if(item[i].getAttribute('type')=='checkbox') {
		checkbox_change(item[i].getAttribute('id'));
	}
}
</script>";

require_once("../lib/footer.inc.php");
