<?php

/**
 * Fichier d'initiapsation de l'EdT par des fichiers CSV
 *
 * @version $Id$
 * @copyright 2007
 */
/* A REFAIRE COMME LES AUTRES ET AJOUTER LES DOIRTS DANS SQL ET MAJ.php */

$titre_page = "Emploi du temps - Initialisation";
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
// CSS particulier à l'EdT
$style_specifique = "edt_organisation/style_edt";

// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php"); ?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php

 // Initialisation des variables
 $action = isset($_POST["action"]) ? $_POST["action"] : NULL;
$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	// Initialisation de l'EdT (fichier g_edt.csv). Librement copié du fichier init_csv/eleves.php
        // On va donc afficher le contenu du fichier tel qu'il va être enregistré dans Gepi
        // en proposant des champs de saisie pour modifier les données si on le souhaite
	if ($action == "upload_file") {
        // On vérifie le nom du fichier...
        if(strtolower($csv_file['name']) == "g_edt.csv") {

            // Le nom est ok. On ouvre le fichier
            $fp=fopen($csv_file['tmp_name'],"r");

            if(!$fp) {
                // Prob sur l'ouverture du fichier
                echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
                echo "<p><a href=\"./edt_init_csv.php\">Cliquer ici </a> pour recommencer !</center></p>";
            } //!$fp
            else {
            	// A partir de là, on vide la table edt_cours
            $vider_table = mysql_query("TRUNCATE TABLE edt_cours");
            	// On affiche alors toutes les lignes de tous les champs
            	$nbre = 1;
				while($tab = fgetcsv($fp, 1000, ";")) {
					$num = count($tab);
    				echo "<p> $num champs pour la ligne $nbre: <br /></p>\n";
    				$nbre++;
    				echo '<span class="legende">';
    					for ($c=0; $c < $num; $c++) {
        					echo $tab[$c] . " - \n";
     					} // for $c
    				echo '</span> ';
    // On considère qu'il n'y a aucun problème dans la ligne
    	$probleme = "non";
    // Pour chaque entrée, on cherche l'id_groupe qui correspond à l'association prof-matière-classe
    	// On récupère le login du prof
    	$nom = strtoupper(strtr($tab[0], "éèêë", "eeee"));
    	$prenom = strtoupper(strtr($tab[1], "éèêë", "eeee"));
    $req_prof = mysql_query("SELECT login FROM utilisateurs WHERE nom = '".$nom."' AND prenom = '".$prenom."'");
    $rep_prof = mysql_fetch_array($req_prof);

		// On récupère l'id de la matière et l'id de la classe
		$matiere = strtoupper(strtr($tab[2], "éèêë", "eeee"));
		$classe = strtoupper(strtr($tab[3], "éèêë", "eeee"));
	$rep_classe = mysql_fetch_array(mysql_query("SELECT id FROM classes WHERE classe = '".$classe."'"));

		// On récupère l'id de la salle
	$req_salle = mysql_fetch_array(mysql_query("SELECT id_salle FROM salle_cours WHERE numero_salle = '".$tab[4]."'"));
	$rep_salle = $req_salle["id_salle"];

		// Le jour et le créneau de début du cours
	$rep_jour = $tab[5];
		$req_heuredebut = mysql_fetch_array(mysql_query("SELECT id_definie_periode FROM absences_creneaux WHERE heuredebut_definie_periode = '".$tab[6]."'"));
			// le champ heuredeb_dec = 0 par défaut mais = 0.5 si le cours commence au milieu du créneau
		if ($req_heuredebut["id_definie_periode"] == "") {
			$rep_heuredeb_dec = '0.5';
			// On détermine dans quel créneau on est
			$req_creneau = mysql_query("SELECT id_definie_periode FROM absences_creneaux WHERE heuredebut_definie_periode < '".$tab[6]."' AND heurefin_definie_periode > '".$tab[6]."'");
			$rep_creneau = mysql_fetch_array($req_creneau);
			$rep_heuredebut = $rep_creneau["id_definie_periode"];
		}
		else {
		$rep_heuredebut = $req_heuredebut["id_definie_periode"];
		$rep_heuredeb_dec = '0';
		}
		// et la durée du cours et le type de semaine
	$rep_duree = $tab[7] * 2;
	$rep_typesemaine = $tab[8];
	/*$req_type_sem = mysql_query("SELECT SQL_SMALL_RESULT DISTINCT type_edt_semaine FROM edt_semaines LIMIT 5");
	$rep_type_sem = mysql_fetch_array($req_type_sem);
	$nbre_type_sem = mysql_num_rows($req_type_sem);

		if ($tab[8] == "0" OR $tab[8] == "1" OR $tab[8] == "2") {
			$rep_typesemaine = $tab[8];
		}
		for($a=0; $a<$nbre_type_sem; $a++) {
			if ($rep_type_sem[$a] == $tab[8]) {
				$rep_typesemaine == $tab[8];
			}
			else $rep_typesemaine = "0";
		}*/

		// le champ modif_edt = 0 pour toutes les entrées
		$rep_modifedt = '0';
		// Vérifier si ce cours dure toute l'année ou seulement durant une période
		if ($tab[9] == "0" OR $tab[10] == "0") {
			$rep_calendar = '0';
		}
		else {
			$req_calendar = mysql_query("SELECT id_calendrier FROM edt_calendrier WHERE jourdebut_calendrier = '".$tab[9]."' AND jourfin_calendrier = '".$tab[10]."'");
			$req_tab_calendar = mysql_fetch_array($req_calendar);
			$rep_calendar = $req_tab_calendar[0];
		}

		// On retrouve l'id_groupe et on vérifie qu'il est unique
	$req_groupe = mysql_query("SELECT jgp.id_groupe FROM j_groupes_professeurs jgp, j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgp.login = '".$rep_prof["login"]."' AND jgc.id_classe = '".$rep_classe["id"]."' AND jgm.id_matiere = '".$matiere."' AND jgp.id_groupe = jgc.id_groupe AND jgp.id_groupe = jgm.id_groupe");
    $rep_groupe = mysql_fetch_array($req_groupe);
    	if (count($req_groupe) > 1) {
    		echo "Cette combinaison renvoie plusieurs groupes : ";
    		for ($a=0; $a<count($rep_groupe); $a++) {
				// Il faut trouver un truc pour que l'admin choisisse le bon groupe
				// Il faut donc afficher les infos sur les groupes en question
				// (liste d'élève, classe, matière en question) avec une infobulle.
				echo $rep_groupe[$a]." - ";
			}
    	}
		// Si tout est ok, on rentre la ligne dans la table sinon, on affiche le problème
		$insert_csv = "INSERT INTO edt_cours (`id_groupe`, `id_salle`, `jour_semaine`, `id_definie_periode`, `duree`, `heuredeb_dec`, `id_semaine`, `id_calendrier`, `modif_edt`) VALUES ('$rep_groupe[0]', '$rep_salle', '$rep_jour', '$rep_heuredebut', '$rep_duree', '$rep_heuredeb_dec', '$rep_typesemaine', '$rep_calendar', '0')";
			// On vérifie que les items existent
		if ($rep_groupe[0] != "" AND $rep_jour != "" AND $rep_heuredebut != "") {
			$req_insert_csv = mysql_query($insert_csv);
			echo "<br /><span class=\"accept\">Cours enregistré</span>";
		}
		else {
			$req_insert_csv = "";
			echo "<br /><span class=\"refus\">Ce cours n'est pas reconnu par Gepi.</span>";
		}
    	//echo $rep_groupe[0]." salle n°".$tab[4]."(id n° ".$rep_salle["id_salle"]." ) le ".$rep_jour." dans le créneau dont l'id est ".$rep_heuredebut." et pour une durée de ".$rep_duree." demis-créneaux et le calend =".$rep_calendar.".";
				} // while
			} // else du début
		fclose($fp);
	} // if ... == "g_edt.csv")
	else
	echo 'Ce n\'est pas le bon nom de fichier, revenez en arrière en <a href="edt_init_csv.php">cliquant ici</a> !';
} // if ($action == "upload_file")

	// On s'occupe maintenant du fichier des salles
	if ($action == "upload_file_salle") {
        // On vérifie le nom du fichier...
        if(strtolower($csv_file['name']) == "g_salles.csv") {

            // Le nom est ok. On ouvre le fichier
            $fp=fopen($csv_file['tmp_name'],"r");

            if(!$fp) {
                // Prob sur l'ouverture du fichier
                echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
                echo "<p><a href=\"./edt_init_csv.php\">Cliquer ici </a> pour recommencer !</center></p>";
            } //!$fp
            else {
            	// On affiche alors toutes les lignes de tous les champs
				while($tab_salle = fgetcsv($fp, 1000, ";")) {
					$numero = htmlentities($tab_salle[0]);
					$nom_salle = htmlentities($tab_salle[1]);
				// On lance la requête pour insérer les nouvelles salles
				$req_insert_salle = mysql_query("INSERT INTO salle_cours (`numero_salle`, `nom_salle`) VALUES ('$numero', '$nom_salle')");
				} // while
			}
		}
		else {
			echo '<h3>Ce n\'est pas le bon nom de fichier !</h3>';
			echo "<p><a href=\"./edt_init_csv.php\">Cliquer ici </a> pour recommencer !</center></p>";
		}
	} // if ($action == "upload_file_salle")

	// Pour la liste de <p>, on précise les contenus des infobulles
		$forme_matiere = mysql_fetch_array(mysql_query("SELECT matiere, nom_complet FROM matieres"));
			$aff1_forme_matiere = $forme_matiere["matiere"];
			$aff2_forme_matiere = $forme_matiere["nom_complet"];
	$contenu_matiere = "Attention de bien respecter le nom court utilisé dans Gepi. Il est de la forme $aff1_forme_matiere pour $aff2_forme_matiere.";
		$forme_classe = mysql_fetch_array(mysql_query("SELECT classe FROM classes WHERE id = '1'"));
		$aff_forme_classe = $forme_classe["classe"];
	$contenu_classe = "Attention de bien respecter le nom court utilisé dans Gepi. Il est de la forme $aff_forme_classe.";
	$contenu_heuredebut = "Attention de bien respecter la forme <span class='red'>HH:MM:SS</span>. Quand un cours commence au début d'un créneau, ce qui est le cas le plus courant, l'heure doit correspondre à ce qui a été indiqué dans le paramétrage !";
	$contenu_duree = "La durée s'exprime en nombre de créneaux occupés. Pour les cours qui durent un créneau et demi, il faut utiliser la forme 1.5 -";
	$contenu_typesemaine = "Par défaut, ce champ est égal à 0 pour les cours se déroulant toutes les semaines. Pour les semaines par quinzaine, précisez les mêmes types que dans le paramétrage du module absences.";
	$contenu_datedebut = "Pour les cours qui n'ont pas lieux toute l'année, précisez la date de début (incluse) du cours sous la forme <span class='red'>AAAA-MM-JJ</span>. Pour les autres cours, ce champ = 0.";
	$contenu_datefin = "Pour les cours qui n'ont pas lieux toute l'année, précisez la date de fin (incluse) du cours sous la forme <span class='red'>AAAA-MM-JJ</span>. Pour les autres cours, ce champ = 0.";
?>

L'initialisation &agrave; partir de fichiers csv se d&eacute;roule en plusieurs &eacute;tapes:

<hr />
	<h4 class='refus'>Premi&egrave;re &eacute;tape</h4>
	<p>Pour &eacute;viter de multiplier les r&eacute;glages, une partie de l'initialisation
	se fait par le module absences : les diff&eacute;rents cr&eacute;neaux de la journ&eacute;e, le type de semaine (paire ou impaire) et les horaires de l'&eacute;tablissement.
	Il faut aller dans le module absence m&ecirc;me si vous ne l'utilisez pas en cliquant sur ce <a href="../mod_absences/admin/index.php">lien</a>, dans la partie intitul&eacute;e "Configuration avanc&eacute;e".</p>
	 <center><h3 class='red'>ATTENTION !</h3></center>
	 <p>Il ne faut pas pr&eacute;ciser les temps de pause (r&eacute;cr&eacute;ation) et les types de semaine doivent &ecirc;tre 1 ou 2 (impair et pair)
	 pour pouvoir utiliser l'emploi du temps.</p>
<hr />
	<h4 class='refus'>Deuxi&egrave;me &eacute;tape</h4>
	<p>Il faut renseigner le calendrier en cliquant sur le menu &agrave; gauche. Toutes les p&eacute;riodes
	qui apparaissent dans l'emploi du temps doivent &ecirc;tre d&eacute;finies : trimestres, vacances, ...</p>
<hr />
	<h4 class='refus'>Troisi&egrave;me &eacute;tape</h4>
	<p>Attention, cette initialisation efface toutes les donn&eacute;es d&eacute;j&agrave; pr&eacute;sentes
	Pour les salles de votre &eacute;tablissement, vous devez fournir un fichier csv. Vous pourrez ensuite en ajouter, en supprimer ou modifier leur nom dans le menu Gestion des salles.</p>
	<p>Les champs suivants doivent être présents, dans l'ordre, et <b>séparés par un point-virgule</b> :</p>
	<ol>
		<li>num&eacute;ro salle (5 caract&egrave;res max.)</li>
		<li>nom salle (30 caract&egrave;res max.)</li>
	</ol>
	<p>Veuillez préciser le nom complet du fichier <b>g_salles.csv</b>.</p>
	<form enctype='multipart/form-data' action='edt_init_csv.php' method='post'>
		<input type='hidden' name='action' value='upload_file_salle' />
		<input type='hidden' name='initialiser' value='ok' />
		<input type='hidden' name='csv' value='ok' />
		<p><input type="file" size="80" name="csv_file" /></p>
		<p><input type='submit' value='Valider' /></p>
	</form>

<hr />
	<h4 class='refus'>Quatri&egrave;me &eacute;tape</h4>
	<p><span class='red'>Attention</span> de bien respecter les heures, jour, nom de mati&egrave;re,... de Gepi que vous avez pr&eacute;cis&eacute; auparavant.
	Pour l'emploi du temps, vous devez fournir un fichier csv dont les champs suivants
	 doivent être présents, dans l'ordre, et <b>séparés par un point-virgule</b> :</p>
	<ol>
	 	<li>nom professeur</li>
		<li>prenom professeur</li>
		<li><?php echo "<a href=\"#\" onmouseover=\"afficher_div('matiere','Y',10,10);return false;\">matiere</a>\n".creer_div_infobulle("matiere", "La matière", "#330033", $contenu_matiere, "#FFFFFF", 15,0,"n","n","y","n"); ?></li>
		<li><?php echo "<a href=\"#\" onmouseover=\"afficher_div('classe','Y',10,10);return false;\">classe</a>\n".creer_div_infobulle("classe", "La classe", "#330033", $contenu_classe, "#FFFFFF", 15,0,"n","n","y","n"); ?></li>
		<li>numero salle</li>
		<li>jour</li>
		<li><?php echo "<a href=\"#\" onmouseover=\"afficher_div('heuredebut','Y',10,10);return false;\">heure debut</a>\n".creer_div_infobulle("heuredebut", "Heure de début de cours", "#330033", $contenu_heuredebut, "#FFFFFF", 15,0,"n","n","y","n"); ?></li>
		<li><?php echo "<a href=\"#\" onmouseover=\"afficher_div('duree','Y',10,10);return false;\">duree</a>\n".creer_div_infobulle("duree", "La durée", "#330033", $contenu_duree, "#FFFFFF", 15,0,"n","n","y","n"); ?></li>
		<li><?php echo "<a href=\"#\" onmouseover=\"afficher_div('typesemaine','Y',10,10);return false;\">type semaine</a>\n".creer_div_infobulle("typesemaine", "Type de semaine", "#330033", $contenu_typesemaine, "#FFFFFF", 15,0,"n","n","y","n"); ?></li>
		<li><?php echo "<a href=\"#\" onmouseover=\"afficher_div('datedebut','Y',10,10);return false;\">date debut</a>\n".creer_div_infobulle("datedebut", "Date de début", "#330033", $contenu_datedebut, "#FFFFFF", 15,0,"n","n","y","n"); ?></li>
		<li><?php echo "<a href=\"#\" onmouseover=\"afficher_div('datefin','Y',10,10);return false;\">date fin</a>\n".creer_div_infobulle("datefin", "Date de fin", "#330033", $contenu_datefin, "#FFFFFF", 15,0,"n","n","y","n"); ?></li>
	</ol>

	<p>Veuillez préciser le nom complet du fichier <b>g_edt.csv</b>.</p>
		<form enctype='multipart/form-data' action='edt_init_csv.php' method='post'>
			<input type='hidden' name='action' value='upload_file' />
			<input type='hidden' name='initialiser' value='ok' />
			<input type='hidden' name='csv' value='ok' />
			<p><input type="file" size="80" name="csv_file" /></p>
			<p><input type='submit' value='Valider' /></p>
		</form>

	</div>

<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>