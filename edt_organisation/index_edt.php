<?php

/**
 * Fichier de gestion de l'emploi du temps dans Gepi version 1.5.x
 *
 * Ci dessous les commandes SQL d'insertion des droits dans la table 'droits' et de la création des tables
 *
 *
INSERT INTO `droits` ( `id` , `administrateur` , `professeur` , `cpe` , `scolarite` , `eleve` , `responsable` , `secours` , `description` , `statut` )
VALUES (
'/edt_organisation/index_edt.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'Gestion de l''emploi du temps', ''
);
 INSERT INTO `droits` ( `id` , `administrateur` , `professeur` , `cpe` , `scolarite` , `eleve` , `responsable` , `secours` , `description` , `statut` )
VALUES (
'/edt_organisation/edt_initialiser.php?initialiser=ok', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''emploi du temps', ''
);
*
* CREATE TABLE `salle_cours` (
`id_salle` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`numero_salle` VARCHAR( 10 ) NOT NULL ,
`nom_salle` VARCHAR( 50 ) NOT NULL
) ENGINE = MYISAM CHARACTER SET latin1 COLLATE latin1_general_ci;
*
* CREATE TABLE `edt_cours` (
  `id_cours` int(3) NOT NULL auto_increment,
  `id_groupe` varchar(10) collate latin1_general_ci NOT NULL,
  `id_salle` varchar(3) collate latin1_general_ci NOT NULL,
  `jour_semaine` varchar(10) collate latin1_general_ci NOT NULL,
  `id_definie_periode` varchar(3) collate latin1_general_ci NOT NULL,
  `duree` varchar(10) collate latin1_general_ci NOT NULL default '2',
  `heuredeb_dec` varchar(3) collate latin1_general_ci NOT NULL default '0',
  `id_semaine` varchar(3) collate latin1_general_ci NOT NULL default '0',
  `id_calendrier` varchar(3) collate latin1_general_ci NOT NULL default '0',
  `modif_edt` varchar(3) collate latin1_general_ci NOT NULL default '0',
  PRIMARY KEY  (`id_cours`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=51 ;

*
*
* CREATE TABLE `edt_setting` (
`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`reglage` VARCHAR( 30 ) NOT NULL ,
`valeur` VARCHAR( 30 ) NOT NULL
) ENGINE = MYISAM CHARACTER SET latin1 COLLATE latin1_general_ci;
*--
-- Contenu de la table `edt_setting`
--

INSERT INTO `edt_setting` (`id`, `reglage`, `valeur`) VALUES
(1, 'nom_creneaux_s', '1'),
(7, 'edt_aff_salle', 'nom'),
(3, 'edt_aff_matiere', 'long'),
(4, 'edt_aff_creneaux', 'noms'),
(8, 'edt_aff_init_infos', 'oui'),
(6, 'edt_aff_couleur', 'nb'),
(9, 'edt_aff_init_infos2', 'oui');
*
CREATE TABLE `edt_calendrier` (
`id_calendrier` int(11) NOT NULL auto_increment,
`classe_concerne_calendrier` text NOT NULL,
`nom_calendrier` varchar(100) NOT NULL default '',
`jourdebut_calendrier` date NOT NULL default '0000-00-00',
`heuredebut_calendrier` time NOT NULL default '00:00:00',
`jourfin_calendrier` date NOT NULL default '0000-00-00',
`heurefin_calendrier` time NOT NULL default '00:00:00',
`numero_periode` tinyint(4) NOT NULL default '0',
`etabferme_calendrier` tinyint(4) NOT NULL,
`etabvacances_calendrier` tinyint(4) NOT NULL,
PRIMARY KEY (`id_calendrier`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;
 *
 * @copyright 2007
 */

	// Définir le sous-titre
$calendrier = isset($_GET['calendrier']) ? $_GET['calendrier'] : (isset($_POST['calendrier']) ? $_POST['calendrier'] : NULL);
	if ($calendrier == "ok") {
		$sous_titre = " - <span class='legende'>Calendrier</span>";
	}
	else
	$sous_titre = "";

$titre_page = "Emploi du temps".$sous_titre;
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt

require_once("./fonctions_edt.php");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

unset ($_SESSION['order_by']);

// End standart header
require_once("../lib/header.inc");

function acces($id,$statut) {
    $tab_id = explode("?",$id);
    $query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
    $droit = @mysql_result($query_droits, 0, $statut);
    if ($droit == "V") {
        return "1";
    } else {
        return "0";
    }
}

//===========================INITIALISATION DES VARIABLES=======
// AJOUT: boireaus
$modifedt=isset($_GET['modifedt']) ? $_GET['modifedt'] : (isset($_POST['modifedt']) ? $_POST['modifedt'] : NULL);
$voirgroup=isset($_GET['voirgroup']) ? $_GET['voirgroup'] : (isset($_POST['voirgroup']) ? $_POST['voirgroup'] : NULL);
$ajoutsalle=isset($_GET['ajoutsalle']) ? $_GET['ajoutsalle'] : (isset($_POST['ajoutsalle']) ? $_POST['ajoutsalle'] : NULL);
$visioedt=isset($_GET['visioedt']) ? $_GET['visioedt'] : (isset($_POST['visioedt']) ? $_POST['visioedt'] : NULL);
$initialiser=isset($_GET['initialiser']) ? $_GET['initialiser'] : (isset($_POST['initialiser']) ? $_POST['initialiser'] : NULL);
$parametrer=isset($_GET['parametrer']) ? $_GET['parametrer'] : (isset($_POST['parametrer']) ? $_POST['parametrer'] : NULL);
$salleslibres=isset($_GET['salleslibres']) ? $_GET['salleslibres'] : (isset($_POST['salleslibres']) ? $_POST['salleslibres'] : NULL);
$init_csv = isset($_GET['csv']) ? $_GET['csv'] : (isset($_POST['csv']) ? $_POST['csv'] : NULL);
$init_xml = isset($_GET['xml']) ? $_GET['xml'] : (isset($_POST['xml']) ? $_POST['xml'] : NULL);

	// Déterminer l'include dans le div id=lecorps
if ($modifedt == "tempo") $page_inc_edt = 'modif_edt_tempo.php';
elseif ($voirgroup == "ok") $page_inc_edt = 'voir_groupe.php';
elseif ($ajoutsalle == "ok") $page_inc_edt = 'ajouter_salle.php';
elseif ($initialiser == "ok" AND $init_csv == "ok") $page_inc_edt = 'edt_init_csv.php';
elseif ($initialiser == "ok" AND $init_xml == "ok") $page_inc_edt = 'edt_init_xml.php';
elseif ($initialiser == "ok" AND $init_csv != "ok" AND $init_xml != "ok") $page_inc_edt = 'edt_initialiser.php';
elseif ($parametrer == "ok") $page_inc_edt = 'edt_parametrer.php';
elseif ($salleslibres == "ok") $page_inc_edt = 'edt_chercher.php';
elseif ($calendrier == "ok") $page_inc_edt = 'edt_calendrier.php';
elseif ($visioedt == 'eleve1') $page_inc_edt = 'voir_edt_eleve.php';
elseif (($visioedt == 'prof1') OR ($visioedt == 'classe1') OR ($visioedt == 'salle1')) $page_inc_edt = 'voir_edt.php';
else $page_inc_edt = 'accueil_edt.php';
//===========================

?>
<!-- On affiche le menu edt -->
<link href="../DomTT/examples/example.css" rel="stylesheet" type="text/css" />
<link href="style_edt.css" rel="stylesheet" type="text/css" />

<script src="./script/menuderoulant.js" type="text/javascript" language="javascript"></script>
<!-- js pour la fonction affpopup() -->
    <script type="text/javascript" language="javascript" src="../DomTT/domLib.js"></script>
    <script type="text/javascript" language="javascript" src="../DomTT/domTT.js"></script>
    <script type="text/javascript" language="javascript" src="../DomTT/domTT_drag.js"></script>

<div id="conteneur">

	<div id="agauche">
		<span class="refus"><?php echo ('Semaine n° '.strftime("%W")); ?></span>
		<p>
		<dl id="menu">
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
					<li><a href="index_edt.php?modifedt=tempo">temporairement</a></li>
				</ul>
			</dd>';
}*/
?>
<br />
		<dt onmouseover="javascript:montre('smenu4');">Chercher</dt>

			<dd id="smenu4">
				<ul>
					<li><a href="index_edt.php?salleslibres=ok">Salles libres</a></li>
				</ul>
			</dd>
<br />
<?php
if ($_SESSION['statut'] == "administrateur") {
	echo '
		<dt onmouseover="javascript:montre(\'smenu5\');">Admin</dt>

			<dd id="smenu5">
				<ul>
					<li><a href="index_edt.php?voirgroup=ok">id_group</a></li>
					<li><a href="index_edt.php?ajoutsalle=ok">Gérer les Salles</a></li>
					<li><a href="index_edt.php?initialiser=ok">Initialiser</a></li>
					<li><a href="index_edt.php?parametrer=ok">Paramétrer</a></li>
				</ul>
			</dd>
<br />
		<dt onclick="javascript:montre();"><a href="index_edt.php?calendrier=ok">Calendrier</a></dt>
		';
}
?>
		</dl>
<br />
		</p>
	</div>
<br />
<!-- la page du corps de l'EdT -->
<div id="lecorps">
<center>
<?php include($page_inc_edt); ?>
</center>
</div>
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>
