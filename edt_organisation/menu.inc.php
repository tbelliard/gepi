<?php

/**
 * EdT Gepi : le menu pour les includes require_once().
 *
 * @version $Id$
 * @copyright 2007
 */

// Sécurité : éviter que quelqu'un appelle ce fichier seul
$serveur_script = $_SERVER["SCRIPT_NAME"];
$analyse = explode("/", $serveur_script);
	if ($analyse[3] == "menu.inc.php") {
		die();
	}

//===========================INITIALISATION DES VARIABLES=======
// AJOUT: boireaus
$voirgroup=isset($_GET['voirgroup']) ? $_GET['voirgroup'] : (isset($_POST['voirgroup']) ? $_POST['voirgroup'] : NULL);
$visioedt=isset($_GET['visioedt']) ? $_GET['visioedt'] : (isset($_POST['visioedt']) ? $_POST['visioedt'] : NULL);
$salleslibres=isset($_GET['salleslibres']) ? $_GET['salleslibres'] : (isset($_POST['salleslibres']) ? $_POST['salleslibres'] : NULL);

	// Déterminer l'include dans le div id=lecorps
if ($salleslibres == "ok") $page_inc_edt = 'edt_chercher.php';
elseif ($visioedt == 'eleve1') $page_inc_edt = 'voir_edt_eleve.php';
elseif (($visioedt == 'prof1') OR ($visioedt == 'classe1') OR ($visioedt == 'salle1')) $page_inc_edt = 'voir_edt.php';
else $page_inc_edt = 'accueil_edt.php';
//===========================

?>
<!-- On affiche le menu edt -->

<script src="./script/menuderoulant.js" type="text/javascript" language="javascript"></script>

	<div id="agauche">

		<span class="refus"><?php echo ('Semaine n° '.strftime("%W")); ?></span>
		<p>
		<dl id="menu_edt">
<br />
<br />
<br />

		<dt onclick="javascript:montre();"><a href="index_edt.php">Accueil EdT</a></dt>
<br />
		<dt onmouseover="javascript:montre('smenu2');">Visionner</dt>

			<dd id="smenu2">
				<ul>
					<li><a href="index_edt.php?visioedt=prof1">Professeur</a></li>
					<li><a href="index_edt.php?visioedt=classe1">Classe</a></li>
					<li><a href="index_edt.php?visioedt=salle1">Salle</a></li>
					<li><a href="index_edt.php?visioedt=eleve1">El&egrave;ve</a></li>
				</ul>
			</dd>
<?php /*
if ($_SESSION['statut'] == "administrateur") {
echo '<br />
		<dt onmouseover="javascript:montre(\'smenu3\');">Modifier</dt>

			<dd id="smenu3">
				<ul>
					<li><a href="modif_edt_tempo.php">temporairement</a></li>
				</ul>
			</dd>';
}*/

	// La fonction chercher_salle est paramétrable
$aff_cherche_salle = GetSettingEdt("aff_cherche_salle");
	if ($aff_cherche_salle == "tous") {
		$aff_ok = "oui";
	}
	else if ($aff_cherche_salle == "admin") {
		$aff_ok = "administrateur";
	}
	else
	$aff_ok = "non";
	// En fonction du résultat, on propose l'affichage ou non
	if ($aff_ok == "oui" OR $_SESSION["statut"] == $aff_ok) {
		echo '
<br />
		<dt onmouseover="javascript:montre(\'smenu4\');">Chercher</dt>

			<dd id="smenu4">
				<ul>
					<li><a href="index_edt.php?salleslibres=ok">Salles libres</a></li>
				</ul>
			</dd>
<br />';
	}

if ($_SESSION['statut'] == "administrateur") {
	echo '
		<dt onmouseover="javascript:montre(\'smenu5\');">Admin</dt>

			<dd id="smenu5">
				<ul>
					<li><a href="voir_groupe.php">Les groupes</a></li>
					<li><a href="ajouter_salle.php">Gérer les Salles</a></li>
					<li><a href="edt_initialiser.php">Initialiser</a></li>
					<li><a href="edt_parametrer.php">Paramétrer</a></li>
				</ul>
			</dd>
<br />
		<dt onclick="javascript:montre();"><a href="edt_calendrier.php">Calendrier</a></dt>
		';
}
?>
		</dl>
<br />
		</p>
	</div>