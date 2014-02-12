<?php

/**
 * Fichier qui permet de faire l'import de l'EdT depuis un logiciel propriétaire
 *

Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal

This file is part of GEPI.

GEPI is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

GEPI is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with GEPI; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
$titre_page = "Emploi du temps - Initialisation";
$affiche_connexion = 'yes';
$niveau_arbo = 1;
// Initialisations files
require_once("../lib/initialisations.inc.php");
// fonctions edt
require_once("./fonctions_edt.php");
require_once("./edt_init_fonctions.php");
// Resume session
$resultat_session = $session_gepi->security_check();
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
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
    Die(ASK_AUTHORIZATION_TO_ADMIN);
}
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";
// ==============PROTOTYPE===============
$utilisation_prototype = "ok";
// ============fin PROTOTYPE=============
// On insère l'entête de Gepi
require_once("../lib/header.inc.php");
// On ajoute le menu EdT
require_once("./menu.inc.php");
// +++++++++++++++++++GESTION DU RETOUR vers absences+++++++++++++++++
$_SESSION["retour"] = "edt_init_csv2";
// +++++++++++++++++++FIN GESTION RETOUR vers absences++++++++++++++++
//debug_var();
$debug_init="n";

/*
$tab_udt_lignes=array();
$sql="SELECT uc.*, ul.matiere, ul.prof, ul.groupe, ul.regroup, ul.mo FROM udt_lignes ul, udt_corresp uc WHERE ul.division=uc.nom_udt;";
$res_udt_lignes=mysql_query($sql);
if(mysql_num_rows($res_udt_lignes)>0) {
	$cpt=0;
	$tab_champs=array('nom_gepi', 'nom_udt', 'matiere', 'prof', 'groupe', 'regroup', 'mo');
	while($lig_udt=mysql_fetch_object($res_udt_lignes)) {
		$tab_udt_lignes[$cpt]=array();
		for($loop=0;$loop<count($tab_champs);$loop++) {
			$champ_courant=$tab_champs[$loop];
			$tab_udt_lignes[$cpt][$champ_courant]=$lig_udt->$champ_courant;
		}
		$cpt++;
	}
}
*/
function cherche_udt_ligne($nom_regroup) {
	$retour="";
	$sql="SELECT uc.*, ul.matiere, ul.prof, ul.groupe, ul.regroup, ul.mo FROM udt_lignes ul, udt_corresp uc WHERE ul.division=uc.nom_udt AND ul.regroup='".addslashes($nom_regroup)."';";
	$res_udt_ligne=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_udt_ligne)>0) {
		$cpt=0;
		while($lig_udt=mysqli_fetch_object($res_udt_ligne)) {
			if($cpt>0) {$retour.=", ";}
			$retour.=$lig_udt->regroup." (".$lig_udt->nom_gepi.") avec ".$lig_udt->prof;
			$cpt++;
		}
	}
	return $retour;
}

?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php
// Initialisation des variables
$action = isset($_POST["action"]) ? $_POST["action"] : null;
$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : null;
$truncate_cours = isset($_POST["truncate_cours"]) ? $_POST["truncate_cours"] : null;
$aff_infos = isset($_POST["aff_infos"]) ? $_POST["aff_infos"] : null;
$recommencer = isset($_POST["recommencer"]) ? $_POST["recommencer"] : null;
$module_gr = isset($_POST["module_gr"]) ? $_POST["module_gr"] : NULL;
$etape = null;
$aff_etape = null;
$exist = NULL;
$msg_gr = NULL;

// On récupère le répertoire temporaire de l'admin
$tempdir = get_user_temp_directory();
if (!$tempdir) {
	// On crée alors le répertoire adéquat
	$creer_rep = check_user_temp_directory();
	if (!$creer_rep) {
		trigger_error('Impossible d\'enregistrer le fichier sur le serveur, veuillez vérifier les droits en écriture sur le répertoire /temp', E_USER_ERROR);
	}
}

// On force le fait de ne plus utiliser la table supprimée edt_gr_nom
saveSetting('mod_edt_gr', 'n');
/*// On vérifie que l'utilisateur utilise ou pas le module edt_gr
if ($action == "modgr") {
	if ($module_gr == 'y') {
		$value = 'y';
	}else{
		$value = 'n';
	}

	// On met à jour le setting
	$save = saveSetting('mod_edt_gr', $value);
	if ($save) {
		$msg_gr = '<span style="color: green;">Modification enregistrée</span>';
	}else{
		$msg_gr = '<span class="red">Impossible d\'enregistrer</span>';
	}
}
// on règle le checked
if (getSettingValue('mod_edt_gr') == "y") {
	$checked_gr = ' checked="checked"';
}else{
	$checked_gr = '';
}
*/

// Si l'utilisateur veut recommencer, on efface toutes les entrées de l'étape qu'il a demandée
if ($recommencer != 'non' AND is_numeric($recommencer)) {


    // On efface toutes les entrées de cette étape (les étapes vont de 0 à 12)
    if ($recommencer == '0' AND file_exists("../temp/".$tempdir."/g_edt_2.csv")) {
    	// On efface le fichier
    	unlink("../temp/".$tempdir."/g_edt_2.csv");
    	$_SESSION["explications"] = "oui";
    }

    $supprimer = mysqli_query($GLOBALS["mysqli"], "DELETE FROM edt_init WHERE ident_export >= '" . $recommencer . "' AND ident_export != 'fichierTexte2'")
    OR trigger_error('Erreur, La table edt_init n\'a pas été mise à jour : ' . mysqli_error($GLOBALS["mysqli"]), E_USER_ERROR);
    $modifier = mysqli_query($GLOBALS["mysqli"], "UPDATE edt_init SET nom_export = '" . $recommencer . "' WHERE ident_export = 'fichierTexte2'")
    OR trigger_error('Erreur dans le retour en arrière : ' . mysqli_error($GLOBALS["mysqli"]), E_USER_ERROR);

	// On vérifie que la demande d'effacement des cours précédents soit bien cochée
	if ($truncate_cours == "oui") {
		$vider_table = mysqli_query($GLOBALS["mysqli"], "TRUNCATE TABLE edt_cours");
    }

}elseif($recommencer == 'non' AND is_numeric($recommencer)){

	// On vérifie que la demande d'effacement des cours précédents soit bien cochée
	if ($truncate_cours == "oui") {
		$vider_table = mysqli_query($GLOBALS["mysqli"], "TRUNCATE TABLE edt_cours");
    }

}


// On garde en mémoire les deux coches pour effacer la table de cours et afficher les informations quand on enregistre les cours
$referer = explode("/", $_SERVER['HTTP_REFERER']);
if ($aff_infos == "oui" OR ($referer[5] == "edt_init_concordance2.php" AND $_SESSION["afficher_infos"] == ' checked="checked"')) {
    $_SESSION["afficher_infos"] = ' checked="checked"';
} else {
    $_SESSION["afficher_infos"] = '';
}
if ($truncate_cours == "oui" OR ($referer[5] == "edt_init_concordance2.php" AND $_SESSION["effacer_cours"] == ' checked="checked"')) {
    $_SESSION["effacer_cours"] = ' checked="checked"';
} else {
    $_SESSION["effacer_cours"] = '';
}


// On teste d'abord pour savoir à quelle étape on est
$query = mysqli_query($GLOBALS["mysqli"], "SELECT nom_export, nom_gepi FROM edt_init WHERE ident_export = 'fichierTexte2'");
// On affiche le numéro de l'étape
if ($query) {
    $etape_effectuee = mysqli_fetch_array($query);
    $etape = $etape_effectuee["nom_export"];
    $date_last = explode(" ", $etape_effectuee["nom_gepi"]);
    // Si $etape est null, on crée l'entrée
    if ($etape == '') {
        $insert = mysqli_query($GLOBALS["mysqli"], "INSERT INTO edt_init SET ident_export = 'fichierTexte2', nom_export = '0', nom_gepi = '" . date("d-m-Y h:i") . "'");
        $etape = '0';
    }
    $aff_etape = '
		<h3 class="gepi" style="font-weight: bold;">Vous êtes actuellement à l\'étape numéro ' . $etape . '</h3>
	';
    if ($date_last != '' AND $etape != 0) {
        echo '
		<p>Cette initialisation a été commencée le : ' . $date_last[0] . ' à ' . $date_last[1] . '</p>
		';
    }
} else {
    // On crée le compteur d'étapes
    $insert = mysqli_query($GLOBALS["mysqli"], "INSERT INTO edt_init SET ident_export = 'fichierTexte2', nom_export = '0', nom_gepi = '" . date("d-m-Y h:i") . "'");
    $etape = 0;
    $aff_etape = '
	<p class="red">Vous n\'avez pas commencé la concordance.</p>';
}
echo '<div id="divCsv2">';
echo $aff_etape;


// On vérifie si un fichier de ce type n'existe pas déjà
	if (file_exists("../temp/".$tempdir."/g_edt_2.csv")) {
	 	// On peut continuer la concordance
	 	$action = "upload_file";
	 	$exist = "oui";
	 }

// On commence le travail sur le fichier
if ($action == "upload_file") {

	// et on enregistre le fichier si nécessaire
	if ($exist != "oui") {
		if (my_strtolower($csv_file['name']) == "g_edt_2.csv") {
			$source_file = ($csv_file['tmp_name']);
			$dest_file = "../temp/".$tempdir."/g_edt_2.csv";
			$res_copy = copy("$source_file" , "$dest_file");
		}else{
			echo "Ce n'est pas le bon nom de fichier.";
			die();
		}
	}

    // On vérifie le nom du fichier...
    if (file_exists("../temp/".$tempdir."/g_edt_2.csv") === TRUE) {
        // plus besoin d'afficher les explications
        $_SESSION["explications"] = "non";
        // Le nom est ok. On ouvre le fichier
        $fp = fopen("../temp/".$tempdir."/g_edt_2.csv", "r");

        if (!$fp) {
            // Prob sur l'ouverture du fichier
            echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
            echo "<p><a href=\"./edt_init_csv.php\">Cliquer ici </a> pour recommencer !</center></p>";
        } else {
            // A partir de là, on vide la table edt_cours
            if ($truncate_cours == "oui") {
                $vider_table = mysqli_query($GLOBALS["mysqli"], "TRUNCATE TABLE edt_cours");
            }
            // On ouvre alors toutes les lignes de tous les champs
            $tableau = array();
            // On affiche le tire pour chaque étape
            $titre = array('Les jours de la semaine',
                'Les créneaux : vous devez faire la concordance sur les créneaux de début de cours <br />&nbsp;&nbsp;&nbsp;-> <span style="font-style: italic;">Précisez le créneau de début pour tous les créneaux (si le cours commence au milieu du créneau M1, il faut donc choisir le créneau M1).</span>.',
                'Les divisions : le nom des classes',
                'Les matières enseignées : ',
                'Les professeurs : ',
                'Les salles : <b>Gepi vérifie l\'existence de ces salles et crée celles qui n\'existent pas.</b>',
                'Les groupes : subdivisions des regroupements',
                'Les regroupements : ce sont les enseignements ou les AID de Gepi',
                'Les effectifs : non utilisés pour l\'EdT',
                'Les Modalités : non utilisées pour l\'EdT',
                'La fréquence : le type de semaine ainsi que les cours qui ne durent pas toute l\'année',
                'L\'aire : non utilisée dans Gepi',
                'Vous allez pouvoir enregistrer les cours dans la base');
            // On détermine quel est le helper appelé
            $helpers = array('select_jours', 'select_creneaux', 'select_classes', 'select_matieres', 'select_professeurs', 'aucun', 'aucun',
                'select_aid_groupes', 'aucun', 'aucun', 'select_frequence', 'aucun');

			$tab_matiere=array();
			
            echo '<p>' . $titre[$etape] . '</p>';
            if ($etape != 12) {
            	$aff_enregistrer = 'Enregistrer ces concordances';
                while ($tab = fgetcsv($fp, 1024, ";")) {
					/*
					echo "<pre>";
					echo print_r($tab);
					echo "</pre>";
					*/
                    if (in_array($tab[$etape], $tableau) === false) {
                        // Puisque la valeur du champ n'est pas encore dans $tableau, on l'insère pour éviter les doublons
                        if ($tab[$etape] != '') {
                            $tableau[] = $tab[$etape];

							if($tab[3]!="") {
								$tab_matiere[remplace_accents($tab[$etape], 'all_nospace')]=$tab[3];
							}
                        }
                    }
                }
                // On range les infos du tableau dans l'ordre alphabétique
                asort($tableau);

                // On commence le traitement des entrées et des sorties
                echo '<form name="edtInitCsv2" action="edt_init_concordance2.php" method="post">';
                $nbre_lignes = count($tableau);

                //for($l = 0; $l < $nbre_lignes; $l++)
                $l = 0; // comme itérateur
				echo "<table class='boireaus'>\n";
				$alt=1;
				foreach ($tableau as $key => $val) {
					$alt=$alt*(-1);
                	// On enlève les guillemets et les apostrophes et les accents
                	//$valeur = my_ereg_replace("'", "wkzx", my_ereg_replace('"', "zxwk", $val));
                	$valeur = remplace_accents($val, 'all_nospace');
					//echo "<p>";
					echo "<tr";
					echo " class='lig$alt'";
					//if($alt==1) {echo " style='background-color:white;'";}
					//else {echo " style='background-color:silver;'";}
					echo ">\n";
					echo "<td style='text-align:left; vertical-align:top;'>\n";
                    echo '
					<input type="hidden" name="nom_export_' . $l . '" value="' . $valeur . '" />
					<label for="nomGepi' . $l . '" id="texte_nomGepi' . $l . '" ><b>' . $val . '</b></label>
					';

                    echo "</td>\n";
                    echo "<td style='text-align:left; vertical-align:top;'>\n";

					// On ne garde que le premier nom de la valeur du champ de l'import pour tester ensuite le selected du select
                    if ($etape != 2) {
                        $test_selected = explode(" ", $valeur);
                    } elseif ($etape == 0) {
                    	// Pour les jours, on enlève l'espace devant
                        $test_selected[0] = trim($valeur);
                    }else{
						$test_selected[0] = $valeur;
					}

					// Pour les salles, on annonce celles qui existent déjà
					if (salleifexists($valeur) == "oui" AND $etape == 5) {
						echo '<span style="font-style: italic; color: green; font-size: 0.8em;">Salle existante, non créée !</span>';
					}elseif (salleifexists($valeur) != "oui" AND $etape == 5){
						echo '<span style="font-style: italic; color: red; font-size: 0.8em;">Salle à créer !</span>';
					}

                    $nom_select = 'nom_gepi_' . $l; // pour le nom du select
                    //if ($etape == 4) {
                    	// Pour les prof, on met tout en majuscule
                    	$nom_selected = my_strtoupper($test_selected[0]);


                    //}else{
					//	$nom_selected = $test_selected[0]; // pour le selected du helper
					//}
                    $nom_id_select = 'nomGepi' . $l; // pour le id du select (en mettre en liaison avec le for du label ci-dessus)
                    $style_select = ' style="text-align: center;"';
                    // On appelle le bon helper
                    if ($helpers[$etape] != 'aucun') {
                        include("helpers/" . $helpers[$etape] . ".php");
                    } else {
                        echo '
						<input type="hidden" name="' . $nom_select . '" value="none" />';
                    }

					if($etape==7) {
						$udt_ligne=cherche_udt_ligne($val);
						if($udt_ligne=="") {$udt_ligne=cherche_udt_ligne($valeur);}
						echo " <span style='font-size:x-small'>".$udt_ligne."</span>";
					}


                    //echo '</p>';
                    echo "</td>\n";
                    echo "</tr>\n";
                    $l++;
                }
				echo "</table>\n";
                if ($etape == 6 OR $etape == 8 OR $etape == 9 OR $etape == 11) {
                	$aff_enregistrer = 'Passer à l\'étape suivante (aucun enregistrement)';
                }elseif($etape == 5){
					$aff_enregistrer = 'Enregistrer ces salles';
				}
            } elseif ($etape == 12) {
				$sql="CREATE TABLE IF NOT EXISTS tempo5 (
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				texte TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				info VARCHAR(200) NOT NULL
				) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
				$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

				//$sql="TRUNCATE tempo2;";
				$sql="TRUNCATE tempo5;";
				$menage=mysqli_query($GLOBALS["mysqli"], $sql);

                echo '
				<form name="edtInitCsv2" action="edt_init_concordance2.php" method="post">';
                $b = 0; // c'est pour le while

                // C'est là qu'on enregistre les cours en se servant des données recueillies auparavant
                while ($tab = fgetcsv($fp, 1024, ";")) {
					if($debug_init=="y") {echo "<br /><p>";}
                    $nbre_lignes = $b;
                    $toutelaligne = null;
                    // On rentre toutes les cellules de la ligne dans une seule variable
                    for($t = 0; $t < 12; $t++) {

                        // On élimine les guillemets et l'apostrophe qui mettent la pagaille
                        //$toutelaligne .= my_ereg_replace("'", "wkzx", my_ereg_replace('"', "zxwk", $tab[$t])) . '|';
						if(isset($tab[$t])) {
							$toutelaligne .= remplace_accents($tab[$t], 'all_nospace');
							if($debug_init=="y") {echo "\$tab[$t]=$tab[$t]<br />";}
						}
                        $toutelaligne .= '|';

                    }

                    //echo '					<input type="hidden" name="ligne_' . $b . '" value="' . $toutelaligne . '" />';
					//$sql="INSERT INTO tempo2 SET col1='".mysql_real_escape_string($toutelaligne)."';";
					$sql="INSERT INTO tempo5 SET texte='".mysqli_real_escape_string($GLOBALS["mysqli"], $toutelaligne)."';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);

                    $b++; // on incrémente le compteur pour le name
                }
                echo 'Votre fichier comporte ' . $nbre_lignes . ' cours.';
                $aff_enregistrer = 'Enregistrer ces cours';
            } else {
                // rien pour le moment
            }
            echo '
					<input type="hidden" name="nbre_lignes" value="' . $nbre_lignes . '" />
					<input type="hidden" name="etape" value="' . $etape . '" />
					<input type="hidden" name="aff_infos" value="' . $aff_infos . '" />
					<input type="hidden" name="concord_csv2" value="ok" />
					<input type="submit" name="enregistrer" value="' . $aff_enregistrer . '" />
				</form>';
        }
    } else {
        // Ce n'est pas le bon nom de fichier
        echo '<p>Ce n\'est pas le bon nom de fichier !</p>
			<a href="./edt_init_csv2.php">Recommencer avec le bon fichier.</a>
		';
    }
}
echo '</div>'; // fin du div id="DivCsv2"

if (isset($_SESSION["explications"]) AND $_SESSION["explications"] == "non") {
    echo '<div style="display: none;">';
}

?>
<h3 class="red">Initialiser l'emploi du temps de Gepi &agrave; partir d'un export csv d'un logiciel propri&eacute;taire.</h3>

<p style="font-weight: bold;">UDT(profil concepteur) > Recherche > Emploi du temps > Rechercher.
(en laissant l'option "cours" cochée par défaut, et en laissant
a priori toutes les divisions/professeurs/matières/salles/...,
sauf si on ne veut extraire qu'une partie de l'EdT)</p>
<p class="red">Attention, il faut sauvegarder le fichier avec un tableur comme Calc d'OpenOffice.org car M$Excel fournit un csv qui pose des probl&egrave;mes &agrave; l'utilisation.</p>
<p>Il faut aussi enlever la ligne d'ent&ecirc;te.<br />
Si l'export généré est au format TXT (<i>séparateur tabulation</i>), se rendre dans le menu Outils/Préférences pour choisir CSV plutôt que TXT pour l'export.</p>
<br />

<p>Pour chaque partie, vous allez devoir faire le lien avec les informations de Gepi. Vous devrez donc faire passer le fichier csv 12 fois et
la derni&egrave;re sera la plus longue. Par contre, les 11 premi&egrave;res &eacute;tapes seront conserv&eacute;es par Gepi et vous pourrez faire la derni&egrave;re
 &eacute;tape (importation des cours eux-m&ecirc;mes) autant de fois que vous le d&eacute;sirez (en effa&ccedil;ant les anciens cours ou non).</p>
	<p>Attention, l'&eacute;tape 12 n'efface pas les donn&eacute;es d&eacute;j&agrave; existantes pour les cours sauf si vous cochez le bouton.</p>
	<p><span class="red">Attention</span> de respecter au mieux les heures, jour, nom de mati&egrave;re,... de Gepi que vous avez pr&eacute;cis&eacute;s auparavant,
	l'initialisation de l'emploi du temps en sera simplifi&eacute;e.</p>
	<p>
	Vous devez fournir un fichier csv dont les champs suivants doivent &ecirc;tre pr&eacute;sents, dans l'ordre, <b>s&eacute;par&eacute;s
	par un point-virgule et encadr&eacute;s par des guillemets ""</b> <span style="color: green; font-weight: bold;">(sans ligne d'ent&ecirc;te)</span> :</p>
<ol>
	<li>Jour</li>
	<li>Heure</li>
	<li>Div</li>
	<li>Matière</li>
	<li>Professeur</li>
	<li>Salle</li>
	<li>Groupe</li>
	<li>Regroup</li>
	<li>Eff</li>
	<li>Mo</li>
	<li>Freq</li>
	<li>Aire</li>
</ol>

	<p>Veuillez préciser le nom complet du fichier <b>g_edt_2.csv</b>.</p>
		<form enctype="multipart/form-data" action="edt_init_csv2.php" method="post">

			<input type="hidden" name="action" value="upload_file" />
			<input type="hidden" name="initialiser" value="ok" />

			<p>
			<label for="affInfosEdt">Afficher l'enregistrement de tous les cours</label>
			<input type="checkbox" id="affInfosEdT" name="aff_infos" value="oui"<?php echo $_SESSION["afficher_infos"]; ?> /></p>

			<p><input type="file" size="80" name="csv_file" /></p>
			<p><input type="submit" value="Valider" /></p>
		</form>
<?php
if (isset($_SESSION["explications"]) AND $_SESSION["explications"] == "non") {
    echo '</div>';
}

?>
<br /><br />

	<!--div class="mode_gr">
	<p>Voulez-vous que Gepi cr&eacute;e tous les cours qu'il ne reconnait pas ?</p>
	<p> Si vous utilisez ce mode, vous pourrez ensuite v&eacute;rifier et compl&eacute;ter les professeurs et les &eacute;l&egrave;ves dans le lien [Les groupes] du menu &agrave; gauche.</p>

	<form name="formGr" action="./edt_init_csv2.php" method="post">

		<input type="hidden" name="action" value="modgr" />
		<p>
		<label for="moduleGr">Actionner le module de cr&eacute;ation des groupes_edt</label>
		<input type="checkbox" id="moduleGr" name="module_gr" value="y" onclick="document.formGr.submit();"<?php echo $checked_gr; ?> />
		<?php echo $msg_gr; ?>
		</p>

	</form>
	</div>
<br /-->
	<div class="mode_gr">

		<form name="refaire" action="edt_init_csv2.php" method="post">

			<p style="font-weight: bold;" title="Cochez pour effacer tous les cours (mais pas les concordances)">
			<label for="truncateCours">Effacer les cours d&eacute;j&agrave; cr&eacute;&eacute;s </label>
			<input type="checkbox" id="truncateCours" name="truncate_cours" value="oui"<?php echo $_SESSION["effacer_cours"]; ?> />
			&nbsp;&nbsp;<input type="submit" name="recommencer2" value="Recommencer" />
			</p>

			<h3 class="gepi">Attention, l'option ci-dessous permet de recommencer &agrave; une &eacute;tape ant&eacute;rieure.
			Si vous demandez l'&eacute;tape 0, il faudra fournir de nouveau le fichier csv.</h3>

			<p><label for="recom">Vous pouvez recommencer depuis l'&eacute;tape : </label>
			<select name="recommencer" id="recom">
				<option value="non">non</option>
				<option value="0">0&nbsp;:&nbsp;Les jours</option>
				<option value="1">1&nbsp;:&nbsp;les cr&eacute;neaux</option>
				<option value="2">2&nbsp;:&nbsp;les classes</option>
				<option value="3">3&nbsp;:&nbsp;les mati&egrave;res</option>
				<option value="4">4&nbsp;:&nbsp;Les professeurs</option>
				<option value="5">5&nbsp;:&nbsp;Les salles</option>
				<option value="6">6</option>
				<option value="7">7&nbsp;:&nbsp;les regroupements</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10&nbsp;:&nbsp;Les fr&eacute;quences</option>
				<option value="11">11</option>
				<option value="12">12&nbsp;:&nbsp;Les cours</option>
			</select>
			<input type="submit" name="recommencer3" value="Recommencer" />
			</p>
		</form>
	</div>
	</div>
<?php
// inclusion du footer
require("../lib/footer.inc.php");

?>
