<?php
/*
*$Id$
*
* Copyright 2001, 2002 Thomas Belliard, Laurent Delineau, Edouard Hue,Eric Lebrun, Christian Chapel
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
$resultat_session = $session_gepi->security_check();
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

function classe_de($id_classe_eleve)
		{
		include("../secure/connect.inc.php");
			$requete_classe_eleve ="SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.classe, ".$prefix_base."classes.id, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."eleves.login='".$id_classe_eleve."' AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id";
			$execution_classe_eleve = mysql_query($requete_classe_eleve) or die('Erreur SQL !'.$requete_classe_eleve.'<br />'.mysql_error());
			$data_classe_eleve = mysql_fetch_array($execution_classe_eleve);
			$id_classe_eleve = $data_classe_eleve['nom_complet'];
		return($id_classe_eleve);
		}

function annee_en_cours_t($date)
{
	$date = explode('-', $date);
	if (empty($annee_d)) {if ($date[1] < 8) {$annee_d = $date[0] - 1;} else {$annee_d = $date[0];}}
	if (empty($annee_f)) {if ($date[1] >= 8){$annee_f = $date[0] + 1;} else {$annee_f = $date[0];}}
	//Annee en cours
	$annee_en_cours = $annee_d."-".$annee_f;
	return($annee_en_cours);
}

function redimensionne_image($photo)
{
	// prendre les informations sur l'image
	$info_image = getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur = $info_image[0];
	$hauteur = $info_image[1];
	// largeur et/ou hauteur maximum à afficher
	if(basename($_SERVER['PHP_SELF'],".php") === "trombi_impr") {
		// si pour impression
		$taille_max_largeur = getSettingValue("l_max_imp_trombinoscopes");
		$taille_max_hauteur = getSettingValue("h_max_imp_trombinoscopes");
	} else {
	// si pour l'affichage écran
		$taille_max_largeur = getSettingValue("l_max_aff_trombinoscopes");
		$taille_max_hauteur = getSettingValue("h_max_aff_trombinoscopes");
	}

	// calcule le ratio de redimensionnement
	$ratio_l = $largeur / $taille_max_largeur;
	$ratio_h = $hauteur / $taille_max_hauteur;
	$ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

	// définit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur = $largeur / $ratio;
	$nouvelle_hauteur = $hauteur / $ratio;

	return array($nouvelle_largeur, $nouvelle_hauteur);
}

if (empty($_GET['etape']) and empty($_POST['etape'])) { $etape = '1'; }
	else { if (isset($_GET['etape'])) {$etape=$_GET['etape'];} if (isset($_POST['etape'])) {$etape=$_POST['etape'];} }
if (empty($_GET['page']) and empty($_POST['page'])) { $page = ''; }
	else { if (isset($_GET['page'])) {$page=$_GET['page'];} if (isset($_POST['page'])) {$page=$_POST['page'];} }
if (empty($_GET['toutes']) and empty($_POST['toutes'])) { $toutes = '0'; }
	else { if (isset($_GET['toutes'])) {$toutes=$_GET['toutes'];} if (isset($_POST['toutes'])) {$toutes=$_POST['toutes'];} }

if (empty($_GET['classe']) and empty($_POST['classe'])) { $classe = ''; }
else { if (isset($_GET['classe'])) { $classe = $_GET['classe']; } if (isset($_POST['classe'])) { $classe = $_POST['classe']; } }
if (empty($_GET['groupe']) and empty($_POST['groupe'])) { $groupe = ''; }
else { if (isset($_GET['groupe'])) { $groupe = $_GET['groupe']; } if (isset($_POST['groupe'])) { $groupe = $_POST['groupe']; } }
if (empty($_GET['equipepeda']) and empty($_POST['equipepeda'])) { $equipepeda = ''; }
else { if (isset($_GET['equipepeda'])) { $equipepeda = $_GET['equipepeda']; } if (isset($_POST['equipepeda'])) { $equipepeda = $_POST['equipepeda']; } }
if (empty($_GET['discipline']) and empty($_POST['discipline'])) { $discipline = ''; }
else { if (isset($_GET['discipline'])) { $discipline = $_GET['discipline']; } if (isset($_POST['discipline'])) { $discipline = $_POST['discipline']; } }
if (empty($_GET['statusgepi']) and empty($_POST['statusgepi'])) { $statusgepi = ''; }
else { if (isset($_GET['statusgepi'])) { $statusgepi = $_GET['statusgepi']; } if (isset($_POST['statusgepi'])) { $statusgepi = $_POST['statusgepi']; } }
if (empty($_GET['affdiscipline']) and empty($_POST['affdiscipline'])) { $affdiscipline = ''; }
else { if (isset($_GET['affdiscipline'])) { $affdiscipline = $_GET['affdiscipline']; } if (isset($_POST['affdiscipline'])) { $affdiscipline = $_POST['affdiscipline']; } }

if (empty($_POST['eleve_absent'])) {$eleve_absent = ''; } else {$eleve_absent=$_POST['eleve_absent']; }
if (empty($_GET['action'])) {$action = ''; } else {$action=$_GET['action']; }
if (empty($_POST['eleve_initial'])) {$eleve_initial = ''; } else {$eleve_initial=$_POST['eleve_initial']; }
if (empty($_GET['id'])) {$id = ''; } else {$id=$_GET['id']; }
if (empty($_POST['valider'])) {$valider = ''; } else {$valider=$_POST['valider']; }

// =========== Style spécifique ================
$style_specifique = "mod_trombinoscopes/styles/styles";
//**************** EN-TETE *********************
$titre_page = "Visualisation des trombinoscopes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

?>
<script type="text/javascript">

function inputenable(id,state) {
var divObj = null;
if (document.getElementById) {
divObj = document.getElementById(id);
} else if(document.all) {
divObj = document.all(id);
} else if (document.layers) {
divObj = document.layers[id];
}
if(state && divObj) {
divObj.removeAttribute("readonly");
} else if(divObj) {
divObj.setAttribute("readonly","readonly");
}
}


function desactiver(mavar)
{

mavar = mavar.split(',');

	for (i=0; i<mavar.length; i++)
	{
		document.getElementById(mavar[i]).disabled=true;
	}

	/*document.getElementById(mavar[i]).disabled=true;*/
	/*document.form1.equipepeda.disabled = true;*/
}

function reactiver(mavar)
{

mavar = mavar.split(',');

	for (var i in mavar)
	{
		document.getElementById(mavar[i]).disabled=false;
	}

	/*document.getElementById(mavar[i]).disabled=false;*/
	/*document.form1.equipepeda.disabled = false;*/
}

</script>

<p class='bold'><a href='../accueil.php'><img src="../images/icons/back.png" alt="Retour" title="Retour" class="back_link" />&nbsp;Retour</a>
<?php if( $etape === '2' and $classe != 'toutes' and $groupe != 'toutes' and $equipepeda != 'toutes' and $discipline != 'toutes' and ( $classe != '' or $groupe != '' or $equipepeda != '' or $discipline != '' or $statusgepi != '' ) ) { ?> | <a href='trombinoscopes.php'>Retour à la sélection</a> | <?php } ?>
<?php if( $etape === '2' and $classe != 'toutes' and $groupe != 'toutes' and $equipepeda != 'toutes' and $discipline != 'toutes' and ( $classe != '' or $groupe != '' or $equipepeda != '' or $discipline != '' or $statusgepi != '' ) ) { ?><a href="trombi_impr.php?classe=<?php echo $classe; ?>&amp;groupe=<?php echo $groupe; ?>&amp;equipepeda=<?php echo $equipepeda; ?>&amp;discipline=<?php echo $discipline; ?>&amp;statusgepi=<?php echo $statusgepi; ?>&amp;affdiscipline=<?php echo $affdiscipline; ?>" target="_blank">Format imprimable</a> <?php } ?>
</p>

<?php if ( ( $classe === 'toutes' or $groupe === 'toutes' or $equipepeda === 'toutes' or $discipline === 'toutes' ) or ( $classe === '' and $groupe === '' and $equipepeda === '' and $discipline === '' and $statusgepi === '' ) ) { ?>

<form method="post" action="trombinoscopes.php" name="form1" style="font-size: 0.71em;">
<div style="margin: auto; padding: 0px 20px 0px 20px;">

<?php
	if(getSettingValue('active_module_trombinoscopes')=='y') {
?>

<div style="width: 45%; float: left; padding: 5px;">
	<div style="font: normal small-caps normal 14pt Verdana; border-collapse: separate; border-spacing: 0px; border: none; border-bottom: 1px solid lightgrey;">Elèves</div>
	<span style="margin-left: 15px;">Par classe</span><br />
		<select name="classe" id="classe" style="margin-left: 15px;">
		<?php
		if ( $_SESSION['statut'] != 'professeur' ) { $classe = 'toutes'; }
		if ( $classe == '' ) {
			$requete_classe_prof = ('SELECT * FROM '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'classes c
						WHERE jgp.id_groupe = jgc.id_groupe AND jgc.id_classe = c.id AND jgp.login = "'.$_SESSION['login'].'"
						GROUP BY c.id
						ORDER BY nom_complet ASC');
		}
				if ( $classe == 'toutes' ) {
			$requete_classe_prof = ('SELECT * FROM '.$prefix_base.'classes c
						ORDER BY c.nom_complet ASC');
		}
				$resultat_classe_prof = mysql_query($requete_classe_prof) or die('Erreur SQL !'.$requete_classe_prof.'<br />'.mysql_error());

				?><option value="" <?php if ( empty($classe) ) { ?>selected="selected"<?php } ?>  onclick="reactiver('equipepeda,groupe,discipline,statusgepi,affdiscipline');">pas de s&eacute;lection</option><?php
				if ( $classe != 'toutes' ) {
				?><option value="toutes">voir toutes les classes</option><?php }
				if ( $classe == 'toutes' and $_SESSION['statut'] == 'professeur' ) {
				?><option value="">voir mes classes</option><?php } ?>
				<optgroup label="-- Les classes --">
						<?php while ( $data_classe_prof = mysql_fetch_array ($resultat_classe_prof)) { ?>
							<option value="<?php echo $data_classe_prof['id']; ?>" <?php if(!empty($classe) and $classe == $data_classe_prof['id']) { ?>selected="selected"<?php } ?> onclick="desactiver('equipepeda,groupe,discipline,statusgepi,affdiscipline');"><?php echo ucwords($data_classe_prof['nom_complet']); echo ' ('.ucwords($data_classe_prof['classe']).')'; ?></option>
						<?php } ?>
				</optgroup>
				</select><br /><br />

	<span style="margin-left: 15px;">Par groupe</span><br />
		<select name="groupe" id="groupe" style="margin-left: 15px;">
		<?php
		if ( $_SESSION['statut'] != 'professeur' ) { $groupe = 'toutes'; }
		if($groupe == '') { $requete_groupe_prof = ('SELECT * FROM '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'groupes g, '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'classes c
								WHERE jgp.id_groupe = g.id
								AND jgp.login = "'.$_SESSION['login'].'"
								AND g.id = jgc.id_groupe
								AND jgc.id_classe = c.id
								GROUP BY g.id
								ORDER BY name ASC'); }
				if($groupe == "toutes") { $requete_groupe_prof = ('SELECT * FROM '.$prefix_base.'groupes g, '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'classes c WHERE g.id = jgc.id_groupe AND jgc.id_classe = c.id ORDER BY name ASC, nom_complet ASC'); }
				$resultat_groupe_prof = mysql_query($requete_groupe_prof) or die('Erreur SQL !'.$requete_groupe_prof.'<br />'.mysql_error());
		?>
		<option value="" <?php if ( empty($classe) ) { ?>selected="selected"<?php } ?>  onclick="reactiver('classe,equipepeda,discipline,statusgepi,affdiscipline');">pas de s&eacute;lection</option>
				<?php if ( $groupe != 'toutes' ) {
				?><option value="toutes">voir tous les groupes</option><?php }
				if ( $groupe == 'toutes' and $_SESSION['statut'] == 'professeur' ) {
				?><option value="">voir mes groupes</option><?php } ?>

			<optgroup label="-- Les groupes --">
						<?php while ( $donnee_groupe_prof = mysql_fetch_array ($resultat_groupe_prof)) { ?>
							<option value="<?php echo $donnee_groupe_prof['id_groupe']; ?>"  onclick="desactiver('classe,equipepeda,discipline,statusgepi,affdiscipline');">
							<?php
									//modif ERIC
									echo ucwords($donnee_groupe_prof['description']);
									//echo ' ('.ucwords($donnee_groupe_prof['classe']).')';
									$tmp_grp=get_group($donnee_groupe_prof['id_groupe']);
									echo ' ('.ucwords($tmp_grp['classlist_string']).')';
							?>
							</option>
					<?php } ?>
					</optgroup>
		</select><br /><br />
			<input value="valider" name="Valider" id="valid1" type="submit" onClick="this.form.submit();this.disabled=true;this.value='En cours'" />
</div>

<?php
	}
	if(getSettingValue('active_module_trombino_pers')=='y') {


		if(getSettingValue('active_module_trombinoscopes')=='y') {
			echo "<div style='width: 45%; float: right; padding: 5px;'>\n";
		}
		else {
			echo "<div style='width: 45%; float: left; padding: 5px;'>\n";
		}
?>

	<div style="font: normal small-caps normal 14pt Verdana; border-collapse: separate; border-spacing: 0px; border: none; border-bottom: 1px solid lightgrey;">Personnels</div>
	<span style="margin-left: 15px;">Par équipe pédagogique</span><br />
		<select name="equipepeda" id="equipepeda" style="margin-left: 15px;">
		<?php
		if ( $_SESSION['statut'] != 'professeur' ) { $equipepeda = 'toutes'; }
		if ( $equipepeda == '' ) {
			$requete_equipe_pedagogique = ('SELECT * FROM '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'classes c
						WHERE jgp.id_groupe = jgc.id_groupe AND jgc.id_classe = c.id AND jgp.login = "'.$_SESSION['login'].'"
						GROUP BY c.id
						ORDER BY nom_complet ASC');
		}
				if ( $equipepeda == 'toutes' ) {
			$requete_equipe_pedagogique = ('SELECT * FROM '.$prefix_base.'classes c
						ORDER BY c.nom_complet ASC');
		}
				$resultat_equipe_pedagogique = mysql_query($requete_equipe_pedagogique) or die('Erreur SQL !'.$requete_equipe_pedagogique.'<br />'.mysql_error());

				?><option value="" <?php if ( empty($equipepeda) ) { ?>selected="selected"<?php } ?>  onclick="reactiver('classe,groupe,discipline,statusgepi');">pas de s&eacute;lection</option><?php
				if ( $equipepeda != 'toutes' ) {
				?><option value="toutes">voir toutes les équipes pedagogique</option><?php }
				if ( $equipepeda == 'toutes' and $_SESSION['statut'] == 'professeur' ) {
				?><option value="">voir mes equipepedas</option><?php } ?>
				<optgroup label="-- Les classes --">
						<?php while ( $donnee_equipe_pedagogique = mysql_fetch_array ($resultat_equipe_pedagogique)) { ?>
							<option value="<?php echo $donnee_equipe_pedagogique['id']; ?>" <?php if(!empty($equipepeda) and $equipepeda == $donnee_equipe_pedagogique['id']) { ?>selected="selected"<?php } ?> onclick="desactiver('classe,groupe,discipline,statusgepi');"><?php echo ucwords($donnee_equipe_pedagogique['nom_complet']); ?></option>
						<?php } ?>
				</optgroup>
		</select>
			<br />&nbsp;&nbsp;&nbsp;<input type="checkbox" name="affdiscipline" id="affdiscipline" value="oui" />&nbsp;<label for="affdiscipline" style="cursor: pointer; cursor: hand;">Afficher les disciplines</label>
		<br /><br />

	<span style="margin-left: 15px;">Par discipline</span><br />
		<select name="discipline" id="discipline" style="margin-left: 15px;">
		<?php
		if ( $_SESSION['statut'] != 'professeur' ) { $discipline = 'toutes'; }
		if ( $discipline == '' ) {
			$requete_discipline = ('SELECT * FROM '.$prefix_base.'j_professeurs_matieres jpm, '.$prefix_base.'matieres m
						WHERE jpm.id_professeur = "'.$_SESSION['login'].'"
						AND jpm.id_matiere = m.matiere
						GROUP BY m.matiere
						ORDER BY m.nom_complet ASC');
		}
				if ( $discipline == 'toutes' ) {
			$requete_discipline = ('SELECT * FROM '.$prefix_base.'matieres m
						ORDER BY m.nom_complet ASC');
		}
				$resultat_discipline = mysql_query($requete_discipline) or die('Erreur SQL !'.$requete_discipline.'<br />'.mysql_error());

				?><option value="" <?php if ( empty($discipline) ) { ?>selected="selected"<?php } ?> onclick="reactiver('classe,groupe,equipepeda,statusgepi,affdiscipline');">pas de s&eacute;lection</option><?php
				if ( $discipline != 'toutes' ) {
				?><option value="toutes">voir toutes les disciplines</option><?php }
				if ( $discipline == 'toutes' and $_SESSION['statut'] == 'professeur' ) {
				?><option value="">voir mes disciplines</option><?php } ?>
				<optgroup label="-- Les disciplines --">
						<?php while ( $donnee_discipline = mysql_fetch_array ($resultat_discipline)) { ?>
							<option value="<?php echo $donnee_discipline['matiere']; ?>" <?php if(!empty($discipline) and $discipline == $donnee_discipline['matiere']) { ?>selected="selected"<?php } ?> onclick="desactiver('classe,groupe,equipepeda,statusgepi,affdiscipline');"><?php echo ucwords($donnee_discipline['nom_complet']); ?></option>
						<?php } ?>
				</optgroup>
		</select><br /><br />

	<span style="margin-left: 15px;">Par statut (CPE/Professeur/Scolarité)</span><br />
		<select name="statusgepi" id="statusgepi" style="margin-left: 15px;">
		<?php
		if ( $statusgepi == '' ) {
			/*
			$requete_statusgepi = ('SELECT * FROM '.$prefix_base.'utilisateurs u
						WHERE u.statut = "professeur" OR u.statut = "cpe"
						GROUP BY u.statut
						ORDER BY u.statut ASC');
			*/
			$requete_statusgepi = ('SELECT * FROM '.$prefix_base.'utilisateurs u
						WHERE u.statut = "professeur" OR u.statut = "cpe" OR u.statut="scolarite" OR u.statut="autre"
						GROUP BY u.statut
						ORDER BY u.statut ASC');
		}
				$resultat_statusgepi = mysql_query($requete_statusgepi) or die('Erreur SQL !'.$requete_statusgepi.'<br />'.mysql_error());

				?><option value="" <?php if ( empty($statusgepi) ) { ?>selected="selected"<?php } ?> onclick="reactiver('classe,groupe,equipepeda,discipline,affdiscipline');">pas de s&eacute;lection</option>
				<optgroup label="-- Les statuts --">
						<?php while ( $donnee_statusgepi = mysql_fetch_array ($resultat_statusgepi)) { ?>
							<option value="<?php echo $donnee_statusgepi['statut']; ?>" <?php if(!empty($statusgepi) and $statusgepi == $donnee_statusgepi['statut']) { ?>selected="selected"<?php } ?> onclick="desactiver('classe,groupe,equipepeda,discipline,affdiscipline');"><?php echo ucwords($donnee_statusgepi['statut']); ?></option>
						<?php } ?>
				</optgroup>
		</select><br /><br />

			<input value="2" name="etape" type="hidden" />
			<input value="valider" name="Valider" id="valid2" type="submit" onClick="this.form.submit();this.disabled=true;this.value='En cours'" />
	</div>
</div>
<?php
	}
?>

</form>
<?php } ?>



<?php /* affichage vignette */?>
<?php if ( $etape === '2' and $classe != 'toutes' and $groupe != 'toutes' and $discipline != 'toutes' and $equipepeda != 'toutes' and ( $classe != '' or $groupe != '' or $equipepeda != '' or $discipline != '' or $statusgepi != '') ) { ?>

<div style="text-align: center;">
<table width="100%" border="0" cellspacing="0" cellpadding="2" style="border : thin dashed #242424; background-color: #FFFFB8;" summary='Choix'>
<tr valign="top">
	<td align="left"><font face="Arial, Helvetica, sans-serif">TROMBINOSCOPE <?php
		$datej = date('Y-m-d');
		$annee_en_cours_t=annee_en_cours_t($datej);
		echo $annee_en_cours_t; ?>
		<br />
		<b>
		<?php
	// on regarde ce qui à était choisie
	// c'est une classe
	if ( $classe != '' and $groupe === '' and $equipepeda === '' and $discipline === '' and $statusgepi === '' ) { $action_affiche = 'classe'; }
	// c'est un groupe
	if ( $classe === '' and $groupe != '' and $equipepeda === '' and $discipline === '' and $statusgepi === '' ) { $action_affiche = 'groupe'; }
	// c'est une équipe pédagogique
	if ( $classe === '' and $groupe === '' and $equipepeda != '' and $discipline === '' and $statusgepi === '' ) { $action_affiche = 'equipepeda'; }
	// c'est une discipline
	if ( $classe === '' and $groupe === '' and $equipepeda === '' and $discipline != '' and $statusgepi === '' ) { $action_affiche = 'discipline'; }
	// c'est un status de gepi
	if ( $classe === '' and $groupe === '' and $equipepeda === '' and $discipline === '' and $statusgepi != '' ) { $action_affiche = 'statusgepi'; }

	if ( $action_affiche === 'classe' ) { $requete_qui = 'SELECT c.id, c.nom_complet, c.classe FROM '.$prefix_base.'classes c WHERE c.id = "'.$classe.'"'; }
	if ( $action_affiche === 'groupe' ) { $requete_qui = 'SELECT g.id, g.name FROM '.$prefix_base.'groupes g WHERE g.id = "'.$groupe.'"'; }
	if ( $action_affiche === 'equipepeda' ) { $requete_qui = 'SELECT c.id, c.nom_complet FROM '.$prefix_base.'classes c WHERE c.id = "'.$equipepeda.'"'; }
	if ( $action_affiche === 'discipline' ) { $requete_qui = 'SELECT m.matiere, m.nom_complet FROM '.$prefix_base.'matieres m WHERE m.matiere = "'.$discipline.'"'; }
	if ( $action_affiche === 'statusgepi' ) { $requete_qui = 'SELECT statut FROM '.$prefix_base.'utilisateurs u WHERE u.statut = "'.$statusgepi.'"'; }
			$execute_qui = mysql_query($requete_qui) or die('Erreur SQL !'.$requete_qui.'<br />'.mysql_error());
			$donnees_qui = mysql_fetch_array($execute_qui) or die('Erreur SQL !'.$execute_qui.'<br />'.mysql_error());
	if ( $action_affiche === 'classe' ) { echo "Classe : ".htmlentities($donnees_qui['nom_complet']);
											echo ' ('.htmlentities(ucwords($donnees_qui['classe'])).')';}
	if ( $action_affiche === 'groupe' ) {
		$current_group=get_group($groupe);
		echo "Groupe : ".htmlentities($donnees_qui['name'])." (<i>".$current_group['classlist_string']."</i>)";
	}
	if ( $action_affiche === 'equipepeda' ) { echo "Equipe pédagogique : ".htmlentities($donnees_qui['nom_complet']); }
	if ( $action_affiche === 'discipline' ) { echo "Discipline : ".htmlentities($donnees_qui['nom_complet'])." (".htmlentities($donnees_qui['matiere']).")"; }
	if ( $action_affiche === 'statusgepi' ) { echo "Statut : ".$statusgepi; }


	// choix du répertoire ou chercher les photos entre professeur ou élève
	if ( $action_affiche === 'classe' ) { $repertoire = 'eleves'; }
	if ( $action_affiche === 'groupe' ) { $repertoire = 'eleves'; }
	if ( $action_affiche === 'equipepeda' ) { $repertoire = 'personnels'; }
	if ( $action_affiche === 'discipline' ) { $repertoire = 'personnels'; }
	if ( $action_affiche === 'statusgepi' ) { $repertoire = 'personnels'; }

	//je recherche les personnes concerné pour la sélection effectué
	// élève d'une classe
		if ( $action_affiche === 'classe' ) { $requete_trombi = "SELECT e.login, e.nom, e.prenom, e.elenoet, jec.login, jec.id_classe, jec.periode, c.classe, c.id, c.nom_complet
									FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c
									WHERE e.login = jec.login
									AND jec.id_classe = c.id
									AND id = '".$classe."'
									GROUP BY nom, prenom"; }
	// élève d'un groupe
		if ( $action_affiche === 'groupe' ) { $requete_trombi = "SELECT jeg.login, jeg.id_groupe, jeg.periode, e.login, e.nom, e.prenom, e.elenoet, g.id, g.name, g.description
									FROM ".$prefix_base."eleves e, ".$prefix_base."groupes g, ".$prefix_base."j_eleves_groupes jeg
									WHERE jeg.login = e.login
									AND jeg.id_groupe = g.id
									AND g.id = '".$groupe."'
									GROUP BY nom, prenom"; }

	// professeurs d'une équipe pédagogique
		if ( $action_affiche === 'equipepeda' ) { $requete_trombi = 'SELECT * FROM '.$prefix_base.'utilisateurs u, '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'classes c
										WHERE jgp.id_groupe = jgc.id_groupe
									AND jgc.id_classe = c.id
									AND u.login = jgp.login
										AND c.id = "'.$equipepeda.'"
										GROUP BY u.nom, u.prenom
										ORDER BY nom ASC, prenom ASC'; }

	// professeurs par discipline
		if ( $action_affiche === 'discipline' ) { $requete_trombi = 'SELECT * FROM '.$prefix_base.'utilisateurs u, '.$prefix_base.'j_professeurs_matieres jpm, '.$prefix_base.'matieres m
										WHERE u.login = jpm.id_professeur
									AND m.matiere = jpm.id_matiere
										AND m.matiere = "'.$discipline.'"
										AND u.etat="actif"
										GROUP BY u.nom, u.prenom
										ORDER BY nom ASC, prenom ASC'; }

	// par statut cpe ou professeur
		if ( $action_affiche === 'statusgepi' ) { $requete_trombi = 'SELECT * FROM '.$prefix_base.'utilisateurs u
										WHERE u.statut = "'.$statusgepi.'"
										GROUP BY u.nom, u.prenom
										ORDER BY nom ASC, prenom ASC'; }

function matiereprof($prof, $equipepeda) {

	global $prefix_base;

	$prof_de = '';
	if ( $prof != '' ) {
		$requete_matiere = 'SELECT * FROM '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'j_groupes_matieres jgm, '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'matieres m
				WHERE jgc.id_classe = "'.$equipepeda.'"
				AND jgc.id_groupe = jgp.id_groupe
				AND jgm.id_matiere = m.matiere
				AND jgp.id_groupe = jgm.id_groupe
				AND jgp.login = "'.$prof.'"';
		$execution_matiere = mysql_query($requete_matiere) or die('Erreur SQL !'.$requete_matiere.'<br />'.mysql_error());
		while ($donnee_matiere = mysql_fetch_array($execution_matiere)) {
			$prof_de = $prof_de.'<br />'.htmlentities($donnee_matiere['nom_complet']).' ';
		}
	}
	return ($prof_de);
}

$execution_trombi = mysql_query($requete_trombi) or die('Erreur SQL !'.$requete_trombi.'<br />'.mysql_error());
$cpt_photo = 1;
while ($donnee_trombi = mysql_fetch_array($execution_trombi))
{
	//insertion de l'élève dans la varibale $eleve_absent
	$login_trombinoscope[$cpt_photo] = $donnee_trombi['login'];
	$nom_trombinoscope[$cpt_photo] = $donnee_trombi['nom'];
	$prenom_trombinoscope[$cpt_photo] = $donnee_trombi['prenom'];

	if ( $action_affiche === 'classe' ) { $id_photo_trombinoscope[$cpt_photo] = strtolower($donnee_trombi['elenoet']); }
	if ( $action_affiche === 'groupe' ) { $id_photo_trombinoscope[$cpt_photo] = strtolower($donnee_trombi['elenoet']); }
	if ( $action_affiche === 'equipepeda' ) { $id_photo_trombinoscope[$cpt_photo] = $donnee_trombi['login']; }
	if ( $action_affiche === 'discipline' ) { $id_photo_trombinoscope[$cpt_photo] = $donnee_trombi['login']; }
	if ( $action_affiche === 'statusgepi' ) { $id_photo_trombinoscope[$cpt_photo] = $donnee_trombi['login']; }

	$matiere_prof[$cpt_photo] = '';
	if ( $action_affiche === 'equipepeda' and $affdiscipline === 'oui' ) {
		$matiere_prof[$cpt_photo] = matiereprof($login_trombinoscope[$cpt_photo], $equipepeda);
	}

	$cpt_photo = $cpt_photo + 1;
}
$total = $cpt_photo;
		?>
		</b></font>
	</td>
</tr>
</table>

<p align="center"><img src="images/barre.gif" width="550" height="2" alt="Barre" /></p>

<table width="100%" border="0" cellspacing="0" cellpadding="4" summary='Trombino'>
<?php
	$i = 1;
	while( $i < $total) {
		echo "<tr align='center' valign='top'>\n";
		for($j=0;$j<3;$j++){
			echo "<td>\n";
			if ($i < $total) {
				$nom_es = strtoupper($nom_trombinoscope[$i]);
				$prenom_es = ucfirst($prenom_trombinoscope[$i]);
				$nom_photo = nom_photo($id_photo_trombinoscope[$i],$repertoire);
        $photo = "../photos/".$repertoire."/".$nom_photo;

        if (($nom_photo != "") and (file_exists($photo))) {
					$valeur=redimensionne_image($photo);
				} else {
          $valeur[0]=getSettingValue("l_max_aff_trombinoscopes");
					$valeur[1]=getSettingValue("h_max_aff_trombinoscopes");
				}

				echo "<img src='";
				if (($nom_photo != "") and (file_exists($photo))) {
					echo $photo;
				}
				else {
					echo "images/trombivide.jpg";
				}

				echo "' style='border: 0px; width: ".$valeur[0]."px; height: ".$valeur[1]."px;' alt='".$prenom_es." ".$nom_es."' title='".$prenom_es." ".$nom_es."' />\n";
				echo "<br /><span style='font-family: Arial, Helvetica, sans-serif'>\n";
				echo "<b>$nom_es</b></span><br />\n";
				echo $prenom_es;
				if ( $matiere_prof[$i] != '' ) {
					echo "<span style='font: normal 10pt Arial, Helvetica, sans-serif;'>$matiere_prof[$i]</span>\n";
				}
				if (( $action_affiche === 'groupe' )&&(strstr($current_group['classlist_string'],","))) {
					/*
					$sql="SELECT c.classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='".$login_trombinoscope[$i]."' ORDER BY jec.periode;";
					$res_class_ele=mysql_query($sql);
					*/
					$tab_ele_classes=get_class_from_ele_login($login_trombinoscope[$i]);
					echo "<br />".$tab_ele_classes['liste'];
				}

				$i = $i + 1;
				//echo "</span>\n";
			}
			else{
				echo "&nbsp;";
			}
			echo "</td>\n";
		}
		echo "</tr>\n";

		?>
		<tr align="center" valign="top">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?php
	}
	echo "</table>\n";
	echo "<p align='center'><img src='images/barre.gif' width='550' height='2' alt='Barre' /></p>\n";
	echo "</div>\n";
}
mysql_close();
require("../lib/footer.inc.php");
?>

