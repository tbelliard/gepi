<?php

/**
 * Fichier d'initialisation de l'EdT par des fichiers CSV
 *
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$titre_page = "Emploi du temps - Initialisation";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

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
//==============PROTOTYPE===============
$utilisation_prototype = "ok";
//============fin PROTOTYPE=============
// On insère l'entête de Gepi
require_once("../lib/header.inc.php");

// On ajoute le menu EdT
require_once("./menu.inc.php");

//+++++++++++++++++++GESTION DU RETOUR vers absences+++++++++++++++++
$_SESSION["retour"] = "edt_init_csv";
//+++++++++++++++++++FIN GESTION RETOUR vers absences++++++++++++++++

?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php

 // Initialisation des variables
$action = isset($_POST["action"]) ? $_POST["action"] : NULL;
$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
$truncate_cours = isset($_POST["truncate_cours"]) ? $_POST["truncate_cours"] : NULL;
$truncate_salles = isset($_POST["truncate_salles"]) ? $_POST["truncate_salles"] : NULL;
$aff_infos = isset($_POST["aff_infos"]) ? $_POST["aff_infos"] : NULL;

$aff_depart = ""; // pour ne plus afficher le html après une initialisation
$compter_echecs = 2; // pour afficher à la fin le message : Tous ces cours ont bien été enregistrés.

	// Initialisation de l'EdT (fichier g_edt.csv). Librement copié du fichier init_csv/eleves.php
        // On va donc afficher le contenu du fichier tel qu'il va être enregistré dans Gepi
        // en proposant des champs de saisie pour modifier les données si on le souhaite
	if ($action == "upload_file") {
        // On vérifie le nom du fichier...
        if(my_strtolower($csv_file['name']) == "g_edt.csv") {

            // Le nom est ok. On ouvre le fichier
            $fp = fopen($csv_file['tmp_name'],"r");

            if(!$fp) {
                // Prob sur l'ouverture du fichier
                echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
                echo "<p><a href=\"./edt_init_csv.php\">Cliquer ici </a> pour recommencer !</center></p>";
            } //!$fp
            else {
            	// A partir de là, on vide la table edt_cours
            if ($truncate_cours == "oui") {
            	$vider_table = mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE TABLE edt_cours");
            }

            	// On ouvre alors toutes les lignes de tous les champs
            	$nbre = 1;
	while($tab = fgetcsv($fp, 1000, ";")) {
			// On met le commentaire dans les variables et on l'affiche que s'il y a un problème
				$message = "";
				$message1 = "";
				$message2 = "";
				$num = count($tab);
    			$message .= "<p> ".$num." champs pour la ligne ".$nbre.": </p>\n";
    			$message2 .= "La ligne ".$nbre." : ";
    				$nbre++;
    			$message1 .= '<span class="legende">';
    					for ($c=0; $c < $num; $c++) {
        					$message1 .= $tab[$c] . " - \n";
     					}
    			$message1 .= '</span> ';
			$req_insert_csv = "";
    	// On considère qu'il n'y a aucun problème dans la ligne
    		$probleme = "";
    // Pour chaque entrée, on cherche l'id_groupe qui correspond à l'association prof-matière-classe
    	// On récupère le login du prof
    	//$prof_login = my_strtoupper(strtr($tab[0], "éèêë", "eeee"));
    	$prof_login = my_strtoupper(remplace_accents($tab[0]));
    $req_prof = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT nom FROM utilisateurs WHERE login = '".$prof_login."'");
    $rep_prof = mysqli_fetch_array($req_prof);
    	if ($rep_prof["nom"] == "") {
    		$probleme .="<p>Le professeur n'est pas reconnu.</p>\n";
    	}

		// On récupère l'id de la matière et l'id de la classe
		//$matiere = strtoupper(strtr($tab[1], "éèêë", "eeee"));
		$matiere = my_strtoupper(remplace_accents($tab[1]));
		$sql_matiere = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT nom_complet FROM matieres WHERE matiere = '".$matiere."'");
		$rep_matiere = mysqli_fetch_array($sql_matiere);
			if ($rep_matiere["nom_complet"] == "") {
				$probleme .= "<p>Gepi ne retrouve pas la bonne mati&egrave;re.</p>\n";
			}
		//$classe = strtoupper(strtr($tab[2], "éèêë", "eeee"));
		$classe = my_strtoupper(remplace_accents($tab[2]));
	$sql_classe = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM classes WHERE classe = '".$classe."'");
	$rep_classe = mysqli_fetch_array($sql_classe);
		if ($rep_classe == "") {
			$probleme .= "<p>La classe n'a pas &eacute;t&eacute; trouv&eacute;e.</p>\n";
		}

		// On récupère l'id de la salle
	$sql_salle = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id_salle FROM salle_cours WHERE numero_salle = '".$tab[3]."'");
	$req_salle = mysqli_fetch_array($sql_salle);
	$rep_salle = $req_salle["id_salle"];
		if ($rep_salle == "") {
			$probleme .= "<p>La salle n'a pas &eacute;t&eacute; trouv&eacute;e.</p>\n";
		}

		// Le jour
	$rep_jour = $tab[4];

		// Le créneau de début du cours
	$creneau_csv = $tab[5];
	$verif_dec = explode("_", $creneau_csv);
		if ($verif_dec[0] == "d") {
			$rep_heuredeb_dec = '0.5';
			$verif_creneau = $verif_dec[1];
		} else {
			$rep_heuredeb_dec = '0';
			$verif_creneau = $verif_dec[0];
		}
	// On cherche l'id du créneau en question
	$req_creneau = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id_definie_periode FROM edt_creneaux WHERE nom_definie_periode = '".$verif_creneau."'");
	$rep_creneau = mysqli_fetch_array($req_creneau);
			if ($rep_creneau == "") {
				$probleme .= "<p>Le cr&eacute;neau n'a pas &eacute;t&eacute; trouv&eacute;.</p>\n";
			} else {
				$rep_heuredebut = $rep_creneau["id_definie_periode"];
			}

		// et la durée du cours et le type de semaine
		// Il faudrait vérifier si la durée est valide ainsi que le type de semaine
	$tab[6]=preg_replace('/,/','.',$tab[6]);
	$rep_duree = $tab[6] * 2;
	$rep_typesemaine = $tab[7];

		// le champ modif_edt = 0 pour toutes les entrées
		$rep_modifedt = '0';
		// Vérifier si ce cours dure toute l'année ou seulement durant une période
		if ($tab[8] == "0") {
			$rep_calendar = '0';
		}
		else {
			$req_calendar = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id_calendrier FROM edt_calendrier WHERE nom_calendrier = '".$tab[8]."'");
			$req_tab_calendar = mysqli_fetch_array($req_calendar);
				if ($req_tab_calendar == "") {
					$probleme .= "<p>La p&eacute;riode du calendrier n'a pas &eacute;t&eacute; trouv&eacute;e.</p>\n";
				} else {
					$rep_calendar = $req_tab_calendar["id_calendrier"];
				}
		}

		// On retrouve l'id_groupe et on vérifie qu'il est unique
	$req_groupe = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT jgp.id_groupe FROM j_groupes_professeurs jgp, j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgp.login = '".$prof_login."' AND jgc.id_classe = '".$rep_classe["id"]."' AND jgm.id_matiere = '".$matiere."' AND jgp.id_groupe = jgc.id_groupe AND jgp.id_groupe = jgm.id_groupe");
    		$rep_groupe = mysqli_fetch_array($req_groupe);
    		if ($rep_groupe == "") {
				$probleme .= "<p>Gepi ne retrouve pas le bon enseignement.</p>\n";
			} else {
    			if (count($req_groupe) > 1) {
    				echo "Cette combinaison renvoie plusieurs groupes : ";
    				for ($a=0; $a<count($rep_groupe); $a++) {
						// Il faut trouver un truc pour que l'admin choisisse le bon groupe
						// Il faut donc afficher les infos sur les groupes en question
						// (liste d'élève, classe, matière en question) avec une infobulle.
						echo $rep_groupe[$a]." - ";
					}
    			}
    		} // fin du else

		// Si tout est ok, on rentre la ligne dans la table sinon, on affiche le problème
		$insert_csv = "INSERT INTO edt_cours
						(`id_groupe`, `id_salle`, `jour_semaine`, `id_definie_periode`, `duree`, `heuredeb_dec`, `id_semaine`, `id_calendrier`, `modif_edt`, `login_prof`)
						VALUES ('$rep_groupe[0]', '$rep_salle', '$rep_jour', '$rep_heuredebut', '$rep_duree', '$rep_heuredeb_dec', '$rep_typesemaine', '$rep_calendar', '0', '$prof_login')";
			// On vérifie que les items existent
		if ($rep_groupe[0] != "" AND $rep_jour != "" AND $rep_heuredebut != "" AND $probleme == "") {
			$req_insert_csv = mysqli_query($GLOBALS["___mysqli_ston"], $insert_csv);
			// Si l'utilisateur décide de ne pas voir le suivi de ses entrées, on n'affiche rien
			if ($aff_infos == "oui") {
				echo "<br /><span class=\"accept\">".$message2."Cours enregistr&eacute;</span>\n";
			} else {
				// on n'affiche rien
			}
		}
		else {
			echo "<br /><span class=\"refus\">Ce cours n'est pas reconnu par Gepi :</span>\n".$message."(".$message1.")".$probleme.".";
			$compter_echecs = $compter_echecs++;
		}
    	//echo $rep_groupe[0]." salle n°".$tab[4]."(id n° ".$rep_salle["id_salle"]." ) le ".$rep_jour." dans le créneau dont l'id est ".$rep_heuredebut." et pour une durée de ".$rep_duree." demis-créneaux et le calend =".$rep_calendar.".";
	} // while
			} // else du début
		fclose($fp);

		// Si tous les cours ont été enregistrés, on affiche que tant de cours ont été enregistrés.
if ($aff_infos != "oui") {
	// On vérifie $compter_echec
	if ($compter_echecs == 2) {
		$aff_nbr = $nbre - 1;
		echo "<br /><p class=\"accept\">Les ".$aff_nbr." cours ont bien été enregistrés.</p>";
	}
}

		// on n'affiche plus le reste de la page
		$aff_depart = "non";
		echo "<hr /><a href=\"./edt_init_csv.php\">Revenir à l'initialisation par csv.</a>";
	} // if ... == "g_edt.csv")
	else
	echo 'Ce n\'est pas le bon nom de fichier, revenez en arrière en <a href="edt_init_csv.php">cliquant ici</a> !';
} // if ($action == "upload_file")


	// On s'occupe maintenant du fichier des salles
	if ($action == "upload_file_salle") {
        // On vérifie le nom du fichier...
        if(my_strtolower($csv_file['name']) == "g_salles.csv") {

            // Le nom est ok. On ouvre le fichier
            $fp = fopen($csv_file['tmp_name'],"r");

            // A partir de là, on vide la table salle_cours
            if ($truncate_salles == "oui") {
            	$vider_table = mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE TABLE salle_cours");
            }

            if(!$fp) {
                // Prob sur l'ouverture du fichier
                echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
                echo "<p><a href=\"./edt_init_csv.php\">Cliquer ici </a> pour recommencer !</center></p>";
            } // if (!$fp)...
            else {

            	// On affiche alors toutes les lignes de tous les champs
				while($tab_salle = fgetcsv($fp, 1000, ";")) {
					$numero = htmlspecialchars($tab_salle[0]);
					$nom_brut_salle = htmlspecialchars($tab_salle[1]);
				// On ne garde que les 30 premiers caractères du nom de la salle
				$nom_salle = mb_substr($nom_brut_salle, 0, 30);
					if ($nom_salle == "") {
						$affnom_salle = 'Sans nom';
					} else {
						$affnom_salle = $nom_salle;
					}
				// On lance la requête pour insérer les nouvelles salles
				$req_insert_salle = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO salle_cours (`numero_salle`, `nom_salle`) VALUES ('$numero', '$nom_salle')");
					if (!$req_insert_salle) {
						echo "La salle : ".$nom_salle." portant le num&eacute;ro : ".$numero." n'a pas &eacute;t&eacute; enregistr&eacute;e.<br />";
					} else {
						echo "La salle : ".$numero." est enregistr&eacute;e (<i>".$affnom_salle."</i>).<br />";
					}
				} // while
			} // else
		fclose($fp);
			// on n'affiche plus le reste de la page
		$aff_depart = "non";
		echo "<hr /><a href=\"./edt_init_csv.php\">Revenir à l'initialisation par csv.</a>";

		} 
		else {
			echo '
			<h3>Ce n\'est pas le bon nom de fichier !</h3>
			<p><a href="./edt_init_csv.php">Cliquer ici </a> pour recommencer !</center></p>
				';
		}
	} // if ($action == "upload_file_salle")

	// On précise l'état du display du div aff_init_csv en fonction de $aff_depart
	if ($aff_depart == "oui") {
		$aff_div_csv = "block";
	} elseif ($aff_depart == "non") {
		$aff_div_csv = "none";
	} else {
		$aff_div_csv = "block";
	}

	// Pour la liste de <p> de l'aide id="aide_initcsv", on précise les contenus
		$forme_matiere = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT matiere, nom_complet FROM matieres"));
			$aff1_forme_matiere = $forme_matiere["matiere"];
			$aff2_forme_matiere = $forme_matiere["nom_complet"];
	$contenu_matiere = "Attention de bien respecter le nom court utilis&eacute; dans Gepi. Il est de la forme $aff1_forme_matiere pour $aff2_forme_matiere.";
		$forme_classe = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT classe FROM classes WHERE id = '1'"));
		$aff_forme_classe = $forme_classe["classe"];
		// La liste des créneaux
				$aff_liste_creneaux = "";
		$sql_creneaux = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT nom_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause'");
		$nbre_creneaux = mysqli_num_rows($sql_creneaux);
			for ($a=0; $a < $nbre_creneaux; $a++) {
				$liste_creneaux[$a] = mysql_result($sql_creneaux, $a, "nom_definie_periode");
				$aff_liste_creneaux .= $liste_creneaux[$a]." - ";
			}
		// Afficher les différents types de semaine : $aff_type_semaines
		$aff_type_semaines = "";
		$sql_semaines = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT type_edt_semaine FROM edt_semaines") or die ('Erreur dans la requête [Select distinct] : '.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$nbre_types = mysqli_num_rows($sql_semaines);
			for($b=0; $b < $nbre_types; $b++) {
				$liste_types[$b] = mysql_result($sql_semaines, $b, "type_edt_semaine");
				if ($nbre_types === 1) {
					$aff_type_semaines = "Seul le type ".$liste_types[$b]." est d&eacute;fini";
				}
				$aff_type_semaines .= $liste_types[$b]." - ";
			}
		// Afficher le nom des différentes périodes du calendrier
		$aff_calendrier = "";
		$sql_calendar = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT nom_calendrier FROM edt_calendrier") or die ('Erreur dans la requête nom_calendrier :'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$nbre_calendar = mysqli_num_rows($sql_calendar);
			if ($nbre_calendar === 0) {
				$aff_calendrier = "<span class=\"red\">Vous n'avez pas d&eacute;fini de périodes de cours.</span>";
			} else {
				for ($c=0; $c < $nbre_calendar; $c++) {
					$liste_calendar[$c] = mysql_result($sql_calendar, $c, "nom_calendrier");
					$aff_calendrier .= $liste_calendar[$c]." - ";
				}
			}

?>
<div id="aff_init_csv" style="display: <?php echo $aff_div_csv; ?>;">
L'initialisation &agrave; partir de fichiers csv se d&eacute;roule en plusieurs &eacute;tapes:

<hr />
	<h4 class="refus">Premi&egrave;re &eacute;tape</h4>
	<p>Une partie de l'initialisation est commune avec le module
absences : <a href="../edt_organisation/admin_periodes_absences.php?action=visualiser">les diff&eacute;rents cr&eacute;neaux</a> de la journ&eacute;e,
	 <a href="./admin_config_semaines.php?action=visualiser">le type de semaine</a> (paire/impaire, A/B/C, 1/2,...) et
	 <a href="../edt_organisation/admin_horaire_ouverture.php?action=visualiser">les horaires de l'&eacute;tablissement</a>.</p>

<hr />
	<h4 class="refus">Deuxi&egrave;me &eacute;tape</h4>
	<p>Il faut renseigner le calendrier en cliquant dans le menu sur Cr&eacute;ation, Cr&eacute;er les p&eacute;riodes. Toutes les p&eacute;riodes
	qui apparaissent dans l'emploi du temps doivent &ecirc;tre d&eacute;finies : trimestres, vacances, ... Si tous vos
	cours durent le temps de l'ann&eacute;e scolaire, vous pouvez vous passer de cette &eacute;tape.</p>
<hr />
	<h4 class="refus">Troisi&egrave;me &eacute;tape</h4>
	<p>Attention, cette initialisation efface toutes les donn&eacute;es concernant les salles d&eacute;j&agrave; pr&eacute;sentes sauf si vous d&eacute;cochez le bouton.
	Pour les salles de votre &eacute;tablissement, vous devez fournir un fichier csv. Vous pourrez ensuite en ajouter, en supprimer ou modifier leur nom dans le menu Gestion des salles.</p>
	<p>Les champs suivants doivent être pr&eacute;sents, dans l'ordre, <b>s&eacute;par&eacute;s par un point-virgule et encadr&eacute;s par des guillemets ""</b> (sans ligne d'ent&ecirc;te) :</p>
	<ol>
		<li>num&eacute;ro salle (5 caract&egrave;res max.)</li>
		<li>nom salle (30 caract&egrave;res max.)</li>
	</ol>
	<p>Veuillez pr&eacute;ciser le nom complet du fichier <b>g_salles.csv</b>.</p>
	<form enctype="multipart/form-data" action="edt_init_csv.php" method="post">
		<input type="hidden" name="action" value="upload_file_salle" />
		<input type="hidden" name="initialiser" value="ok" />
		<input type="hidden" name="csv" value="ok" />
		<p><label for="truncateSalles">Effacer les salles d&eacute;j&agrave; cr&eacute;&eacute;es </label>
		<input type="checkbox" id="truncateSalles" name="truncate_salles" value="oui" checked="checked" /></p>
		<p><input type="file" size="80" name="csv_file" /></p>
		<p><input type="submit" value="Valider" /></p>
	</form>

<hr />
	<h4 class="refus">Quatri&egrave;me &eacute;tape</h4>
	<p>Attention, cette initialisation efface toutes les donn&eacute;es concernant les cours d&eacute;j&agrave; pr&eacute;sents sauf si vous d&eacute;cochez le bouton.</p>
	<p><span class="red">Attention</span> de bien respecter les heures, jour, nom de mati&egrave;re,... de Gepi que vous avez pr&eacute;cis&eacute; auparavant.
	Pour l'emploi du temps, vous devez fournir un fichier csv dont les champs suivants
	 doivent &ecirc;tre pr&eacute;sents, dans l'ordre, <b>s&eacute;par&eacute;s par un point-virgule et encadr&eacute;s par des guillemets ""</b> (sans ligne d'ent&ecirc;te) :</p>
<!-- AIDE init csv -->

<a href="#ancre1" onclick="javascript:changerDisplayDiv('aide_initcsv');" name="ancre1">
	<img src="../images/info.png" alt="Plus d'infos..." Title="Cliquez pour plus d'infos..." />
</a>
	<div style="display: none;" id="aide_initcsv">
	<hr />
	<span class="red">Attention</span>, ces champs ont des r&egrave;gles &agrave; suivre : il faut respecter la forme retenue par Gepi
	<br />
	<p>"login_prof";"mati&egrave;re";"classe";num&eacute;ro_salle;"jour";"nom_cr&eacute;neau";duree;"type_semaine";
	"nom_periode_cours"</p>
	<p>Pour le login des professeurs, vous pouvez les r&eacute;cup&eacute;rer par ce <a href="<?php echo $gepiPath; ?>/utilisateurs/import_prof_csv.php">LIEN</a>.</p>
	<p>Pour la mati&egrave;re, il faut utiliser le nom court qui est de la forme <?php echo "\"".$aff1_forme_matiere."\" pour ".$aff2_forme_matiere; ?>.</p>
	<p>Pour la classe, le nom court est de la forme "<?php echo $aff_forme_classe; ?>".</p>
	<p>Le num&eacute;ro de la salle et le jour doivent correspondre &agrave; des informations existantes d&eacute;j&agrave;
	dans Gepi.</p>
	<p>Pour le nom du cr&eacute;neau : <?php echo $aff_liste_creneaux; ?>Si un cours commence au milieu d'un cours,
	 il faut pr&eacute;c&eacute;der le nom du cr&eacute;neau par le pr&eacute;fixe d_ (ex : d_M1 pour M1).</p>
	<p>La dur&eacute;e s'exprime en nombre de cr&eacute;neaux occup&eacute;s. Pour les cours qui durent un cr&eacute;neau et demi,
	il faut utiliser la forme "1.5" -</p>
	<p>Le type de semaine est &eacute;gal à "0" pour les cours se d&eacute;roulant toutes les semaines. Pour les autres cours,
	pr&eacute;cisez entre ces types : <?php echo $aff_type_semaines; ?>.</p>
	<p>Pour les cours qui n'ont pas lieu toute l'ann&eacute;e, pr&eacute;cisez le nom de la p&eacute;riode de cours.
	(<?php echo $aff_calendrier; ?>)<br /> Pour les autres cours, ce champ doit &ecirc;tre &eacute;gal &agrave; "0".</p>
	<hr />
	</div>
<!-- Fin aide init csv -->
	<ol>
	 	<li>login professeur</li>
		<li>mati&egrave;re</li>
		<li>classe</li>
		<li>num&eacute;ro de la salle</li>
		<li>jour</li>
		<li>nom du cr&eacute;neau</li>
		<li>dur&eacute;e du cours</li>
		<li>type de semaine</li>
		<li>Nom de la p&eacute;riode de cours</li>
	</ol>

	<p>Veuillez préciser le nom complet du fichier <b>g_edt.csv</b>.</p>
		<form enctype="multipart/form-data" action="edt_init_csv.php" method="post">
			<input type="hidden" name="action" value="upload_file" />
			<input type="hidden" name="initialiser" value="ok" />
			<input type="hidden" name="csv" value="ok" />
			<p><label for="truncateCours">Effacer les cours d&eacute;j&agrave; cr&eacute;&eacute;s </label>
			<input type="checkbox" id="truncateCours" name="truncate_cours" value="oui" checked="checked" />
			<label for="affInfosEdt">Afficher l'enregistrement de tous les cours</label>
			<input type="checkbox" id="affInfosEdT" name="aff_infos" value="oui" checked="checked" /></p>
			<p><input type="file" size="80" name="csv_file" /></p>
			<p><input type="submit" value="Valider" /></p>
		</form>
</div><!-- fin du div aff_init_csv -->
	</div><!-- fin du div lecorps -->

<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>
