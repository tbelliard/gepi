<?php
/**
 *
 *
 * Copyright 2010-2020 Josselin Jacquard, Stephane Boireau
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
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

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activé.");
}

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
    die("acces interdit");
}

include_once 'lib/function.php';

// Initialisation des variables
//récupération des paramètres de la requète
$nom_eleve = isset($_POST["nom_eleve"]) ? $_POST["nom_eleve"] :(isset($_GET["nom_eleve"]) ? $_GET["nom_eleve"] :(isset($_SESSION["nom_eleve"]) ? $_SESSION["nom_eleve"] : NULL));
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :(isset($_SESSION["id_classe_abs"]) ? $_SESSION["id_classe_abs"] : NULL));
$date_absence_eleve_debut = isset($_POST["date_absence_eleve_debut"]) ? $_POST["date_absence_eleve_debut"] :(isset($_GET["date_absence_eleve_debut"]) ? $_GET["date_absence_eleve_debut"] :(isset($_SESSION["date_absence_eleve_debut"]) ? $_SESSION["date_absence_eleve_debut"] : NULL));
$date_absence_eleve_fin = isset($_POST["date_absence_eleve_fin"]) ? $_POST["date_absence_eleve_fin"] :(isset($_GET["date_absence_eleve_fin"]) ? $_GET["date_absence_eleve_fin"] :(isset($_SESSION["date_absence_eleve_fin"]) ? $_SESSION["date_absence_eleve_fin"] : NULL));
$type_extrait = isset($_POST["type_extrait"]) ? $_POST["type_extrait"] :(isset($_GET["type_extrait"]) ? $_GET["type_extrait"] : NULL);
$affichage = isset($_POST["affichage"]) ? $_POST["affichage"] :(isset($_GET["affichage"]) ? $_GET["affichage"] : NULL);

if (isset($id_classe) && $id_classe != null) $_SESSION['id_classe_abs'] = $id_classe;
if (isset($date_absence_eleve_debut) && $date_absence_eleve_debut != null) $_SESSION['date_absence_eleve_debut'] = $date_absence_eleve_debut;
if (isset($date_absence_eleve_fin) && $date_absence_eleve_fin != null) $_SESSION['date_absence_eleve_fin'] = $date_absence_eleve_fin;

if ($date_absence_eleve_debut != null) {
    $dt_date_absence_eleve_debut = new DateTime(str_replace("/",".",$date_absence_eleve_debut));
} else {
    $dt_date_absence_eleve_debut = new DateTime('now');
    $dt_date_absence_eleve_debut->setDate($dt_date_absence_eleve_debut->format('Y'), $dt_date_absence_eleve_debut->format('m') - 1, $dt_date_absence_eleve_debut->format('d'));
}
if ($date_absence_eleve_fin != null) {
    $dt_date_absence_eleve_fin = new DateTime(str_replace("/",".",$date_absence_eleve_fin));
} else {
    $dt_date_absence_eleve_fin = new DateTime('now');
}

$inverse_date=false;
if($dt_date_absence_eleve_debut->format("U")>$dt_date_absence_eleve_fin->format("U")){
    $date2=clone $dt_date_absence_eleve_fin;
    $dt_date_absence_eleve_fin= $dt_date_absence_eleve_debut;
    $dt_date_absence_eleve_debut= $date2;
    $message="Les dates de début et de fin ont été inversées.";
    $inverse_date=true;
    $_SESSION['date_absence_eleve_debut'] = $dt_date_absence_eleve_debut->format('d/m/Y');
    $_SESSION['date_absence_eleve_fin'] = $dt_date_absence_eleve_fin->format('d/m/Y'); 
}
$dt_date_absence_eleve_debut->setTime(0,0,0);
$dt_date_absence_eleve_fin->setTime(23,59,59);

// 20201126: Boucler sur la liste des classes
$boucler_sur_classes=isset($_POST['boucler_sur_classes']) ? $_POST['boucler_sur_classes'] : NULL;
if(isset($boucler_sur_classes)) {
	if(preg_match('/^[0-9]{1,}$/', $boucler_sur_classes)) {
		if($boucler_sur_classes==1) {
			$id_extraction=substr(rand().time(), 0, 10);
			$date_extraction=time();

			if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
				$classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
			} else {
				$classe_col = $utilisateur->getClasses();
			}
			if (!$classe_col->isEmpty()) {
				foreach ($classe_col as $classe) {
					$id_classe=$classe->getId();
					$_SESSION['id_classe_abs'] = $id_classe;
					break;
				}
			}
		}
		else {
			$id_extraction=$_POST['id_extraction'];
			$date_extraction=$_POST['date_extraction'];
		}
		$boucler_sur_classes++;
	}
	else {
		$msg="Valeur invalide pour 'boucler_sur_classes'.<br />";
		unset($boucler_sur_classes);
	}
}


$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
//$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
$javascript_specifique[] = "lib/tablekit";
$dojo=true;
$utilisation_tablekit="ok";
//**************** EN-TETE *****************
$titre_page = "Les absences";
if ($affichage != 'ods') {// on affiche pas de html
    require_once("../lib/header.inc.php");

    include('menu_abs2.inc.php');
    include('menu_bilans.inc.php');
    ?>
    <div id="contain_div" class="css-panes">
         <?php if (isset($message)){
          echo'<h2 class="no">'.$message.'</h2>';
        }



	// 20201126: Affichage bilan
	if((isset($_GET['boucler_sur_classes_affichage_bilan']))&&(isset($_GET['id_extraction']))) {

		$sql="SELECT * FROM a_agregation_demi_journees WHERE id_extraction='".$_GET['id_extraction']."' ORDER BY classe, nom, prenom LIMIT 1;";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p style='color:red'>Aucune donnée n'a été extraite.</p>";
			require_once("../lib/footer.inc.php");
			die();
		}

		$lig=mysqli_fetch_object($res);
		echo "<h2>Les demi-journées du ".formate_date($lig->date_debut)." au ".formate_date($lig->date_fin)."</h2>";

		echo '<table class="sortable resizable boireaus boireaus_alt" style="border-width:1px; border-style:outset">';
		echo '<thead>';
		echo '<tr>';

		echo '<th class="text" style="border-width:1px; border-style: inset;" title ="Cliquez pour trier sur la colonne">';
		echo 'Nom Prénom';
		echo '</th>';

		echo '<th class="text" style="border-width:1px; border-style: inset;" title ="Cliquez pour trier sur la colonne">';
		echo 'Classe';
		echo '</th>';

		echo '<th class="number" style="border-width:1px; border-style: inset;" title ="Cliquez pour trier sur la colonne">';
		echo 'Nbre de demi-journées d\'absence';
		echo '</th>';

		echo '<th class="number" style="border-width:1px; border-style: inset;" title ="Cliquez pour trier sur la colonne">';
		echo 'non justifiees';
		echo '</th>';

		echo '<th class="number" style="border-width:1px; border-style: inset;" title ="Cliquez pour trier sur la colonne">';
		echo 'non valables';
		echo '</th>';

		echo '<th class="number" style="border-width:1px; border-style: inset;" title ="Cliquez pour trier sur la colonne">';
		echo 'nbre de retards';
		echo '</th>';

		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		$csv="ELENOET;NOM;PRENOM;CLASSE;NBABS;NBNJ;NBRET;\n";
		$csv2="ELENOET;NBABS;NBNJ;NBRET;\n";

		$sql="SELECT * FROM a_agregation_demi_journees WHERE id_extraction='".$_GET['id_extraction']."' ORDER BY classe, nom, prenom;";
		$res=mysqli_query($mysqli, $sql);
		while($lig=mysqli_fetch_object($res)) {

			echo "<tr class='white_hover'>
				<td style='border:1px; border-style: inset;'><a href='../eleves/visu_eleve.php?ele_login=".$lig->login."' target='_blank'>".$lig->nom." ".$lig->prenom."</a></td>
				<td style='border:1px; border-style: inset;'>".$lig->classe."</td>
				<td style='border:1px; border-style: inset;'>".$lig->NbAbsences."</td>
				<td style='border:1px; border-style: inset;'>".$lig->NbNonJustifiees."</td>
				<td style='border:1px; border-style: inset;'>".$lig->NbNonValables."</td>
				<td style='border:1px; border-style: inset;'>".$lig->NbRetards."</td>
			</tr>";
		}
		echo "</table>";










		require_once("../lib/footer.inc.php");
		die();
	}



        ?>
    <!--div style='float:right;width:5em;' title="Lien à placer ailleurs par la suite... dans un onglet par exemple"><a href='export_stat.php' target='_blank'>Exports statistiques</a></div-->
    <p>
      <strong>Précision:</strong> Un manquement à l'obligation de présence sur une heure, entraine le décompte de la demi-journée correspondante pour l'élève.
    </p>
    <form dojoType="dijit.form.Form" id="choix_extraction" name="choix_extraction" action="<?php $_SERVER['PHP_SELF']?>" method="post">
	<?php
		// 20201126:
		if(!isset($boucler_sur_classes)) {
	?>
    <h2>Les demi-journées
    du	
    <input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve_debut" name="date_absence_eleve_debut" value="<?php echo $dt_date_absence_eleve_debut->format('Y-m-d')?>" />
    au               
    <input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve_fin" name="date_absence_eleve_fin" value="<?php echo $dt_date_absence_eleve_fin->format('Y-m-d')?>" />
	</h2>
    Nom (facultatif) : <input dojoType="dijit.form.TextBox" type="text" style="width : 10em" name="nom_eleve" size="10" value="<?php echo $nom_eleve?>"/>
	<?php
		// 20201126:
		}
		else {
			echo "<h2>Les demi-journées du ".$dt_date_absence_eleve_debut->format('Y-m-d')."
			<input type='hidden' id='date_absence_eleve_debut' name='date_absence_eleve_debut' value='".$dt_date_absence_eleve_debut->format('Y-m-d')."' /> 
			au ".$dt_date_absence_eleve_fin->format('Y-m-d')."
			<input type='hidden' id='date_absence_eleve_fin' name='date_absence_eleve_fin' value='".$dt_date_absence_eleve_fin->format('Y-m-d')."' />
			</h2>";
		}

    //on affiche une boite de selection avec les classe
    if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
	$classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
    } else {
	$classe_col = $utilisateur->getClasses();
    }
	$classe_courante='';
    if (!$classe_col->isEmpty()) {
		// 20201126: Boucler sur la liste des classes
		if(!isset($boucler_sur_classes)) {
			//echo ("Classe : <select dojoType=\"dijit.form.Select\" id='id_classe' style=\"width :12em;font-size:12px;\" name=\"id_classe\">");
			echo ("Classe : <select id='id_classe' style=\"width :12em;font-size:12px;\" name=\"id_classe\">");
			echo "<option value='-1'>Toutes les classes</option>\n";
			foreach ($classe_col as $classe) {
				echo "<option value='".$classe->getId()."'";
				if ($id_classe == $classe->getId()) {
					echo " selected='selected' ";
					$classe_courante=$classe->getNom();
				}
				echo ">";
				echo $classe->getNom();
				echo "</option>\n";
			}
			echo "</select> ";

			echo " <input type='checkbox' name='boucler_sur_classes' id='boucler_sur_classes' value='1' onchange=\"document.getElementById('id_classe').selectedIndex=1;\" />
		<label for='boucler_sur_classes' id='boucler_sur_classes'>Boucler sur la liste des classes <em>(hors génération ODS)</em></label>";
		}
		else {
			echo "<div style='display:none'> ";
			echo ("Classe : <select id='id_classe' style=\"width :12em;font-size:12px;\" name=\"id_classe\">");
			echo "<option value='-1'>Toutes les classes</option>\n";
			foreach ($classe_col as $classe) {
				echo "<option value='".$classe->getId()."'";
				if ($id_classe == $classe->getId()) {
					echo " selected='selected' ";
					$classe_courante=$classe->getNom();
				}
				echo ">";
				echo $classe->getNom();
				echo "</option>\n";
			}
			echo "</select> ";
			echo "</div> ";

			echo "<input type='hidden' name='id_extraction' id='id_extraction' value='$id_extraction' />";
			echo "<input type='hidden' name='date_extraction' id='date_extraction' value='$date_extraction' />";

			echo " <input type='hidden' name='boucler_sur_classes' id='boucler_sur_classes' value='$boucler_sur_classes' />
		<label for='boucler_sur_classes' id='boucler_sur_classes'><span style='font-weight:bold'>On bloucle sur la liste des classes</span></label>";
		}

    } else {
	echo 'Aucune classe avec élève affecté n\'a été trouvée';
    }
    ?>
    </p>
	<p>
	<?php
		//20201126
		if(!isset($boucler_sur_classes)) {
	?>
    <button type="submit"  style="font-size:12px" dojoType="dijit.form.Button" name="affichage" value="html">Afficher</button>
    <button type="submit"  style="font-size:12px" dojoType="dijit.form.Button" name="affichage" value="ods">Enregistrer au format ods</button>
    &nbsp;<input type="checkbox" id='generer_csv' name="generer_csv" value="y"><label for='generer_csv' title="La génération de CSV est effectuée avec l'affichage HTML dans la présente page (pas lors de la génération d'un fichier ODS).
Cochez la case et cliquez sur Afficher pour obtenir la génération d'un fichier CSV.">Générer un CSV</label>
	<?php
		}
		else {
			echo "<input type='hidden' name='affichage' value='html'/>";
			if(isset($generer_csv)) {
				echo "<input type='hidden' name='generer_csv' value='y'/>";
			}
		}
	?>
	</p>
	</form>

    <?php
}
if ($affichage != null && $affichage != '') {
    $eleve_query = EleveQuery::create()->orderByNom()->orderByPrenom()->distinct();
    if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    } else {
		$eleve_query->filterByUtilisateurProfessionnel($utilisateur);
    }
    if ($id_classe !== null && $id_classe != -1) {
		$eleve_query->useJEleveClasseQuery()->filterByIdClasse($id_classe)->endUse();
    }
    if ($nom_eleve !== null && $nom_eleve != '') {
		$eleve_query->filterByNomOrPrenomLike($nom_eleve);
    }

    $eleve_query->where('Eleve.DateSortie<?','0')
                ->orWhere('Eleve.DateSortie is NULL')
                ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve_debut->format('U'));
    
    $eleve_col = $eleve_query->find();
    $table_synchro_ok = AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable($dt_date_absence_eleve_debut,$dt_date_absence_eleve_fin);
    if (!$table_synchro_ok) {//la table n'est pas synchronisée. On va vérifier individuellement les élèves qui se sont pas synchronisés
		if ($eleve_col->count()>150) {
			echo 'Il semble que vous demandez des statistiques sur trop d\'élèves et votre table de statistiques n\'est pas synchronisée.<br />Veuillez faire une demande pour moins d\'élèves ou';
			if(getSettingAOui('AccesCpeAgregationAbs2')) {
				echo ' <a href="./admin/admin_table_agregation.php" title="ATTENTION : Cette opération est lourde.
                     Elle peut enliser le serveur, perturber les 
                     saisies le temps qu\'elle s\'achève.">remplir la table d\'agrégation</a>.';
			}
			else {
				echo ' demander à votre administrateur de remplir la table d\'agrégation.';
			}
			if (ob_get_contents()) {
				ob_flush();
			}
			flush();
		}
		foreach ($eleve_col as $eleve) {
			$eleve->checkAndUpdateSynchroAbsenceAgregationTable($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin);
		}
	}
    
    //on recommence la requetes, maintenant que la table est synchronisée, avec les données d'absence
    $eleve_query = EleveQuery::create()->filterById($eleve_col->toKeyValue('Id','Id'));
    $eleve_query->useAbsenceAgregationDecompteQuery()->distinct()->filterByDateIntervalle($dt_date_absence_eleve_debut,  $dt_date_absence_eleve_fin)->endUse();
    $eleve_query->withColumn('SUM(AbsenceAgregationDecompte.ManquementObligationPresence)', 'NbAbsences')
    	->withColumn('SUM(AbsenceAgregationDecompte.NonJustifiee)', 'NbNonJustifiees')
    	->withColumn('SUM(AbsenceAgregationDecompte.Retards)', 'NbRetards')
   		->withColumn('SUM(AbsenceAgregationDecompte.ManquementObligationPresence) - SUM(AbsenceAgregationDecompte.NonJustifiee)', 'NbJustifiees')
    	->groupBy('Eleve.Id');
    
    $eleve_col = $eleve_query->find();
}

if ($affichage == 'html') {
    //debug_var();

	// 20201126
	if(isset($classe_courante)) {
		echo "<h3 style='font-weight:bold'>Classe de $classe_courante</h3>";
	}

    if(isset($_POST['generer_csv'])) {
        $user_temp_dir=get_user_temp_directory();
        if(!$user_temp_dir) {
            echo "<p style='color:red'>ERREUR : Il n'a pas été possible d'accéder à votre dossier temporaire pour y générer le CSV.</p>\n";
        }
        echo "<p id='p_csv' style='display:none'></p>\n";
    }

    echo 'Total élèves : '.$eleve_col->count();
    echo '<table class="sortable resizable boireaus" style="border-width:1px; border-style:outset">';
    $precedent_eleve_id = null;
    echo '<thead>';
    echo '<tr>';

    echo '<th class="text" style="border-width:1px; border-style: inset;" title ="Cliquez pour trier sur la colonne">';
    echo 'Nom Prénom';
    echo '</th>';

    echo '<th class="text" style="border-width:1px; border-style: inset;" title ="Cliquez pour trier sur la colonne">';
    echo 'Classe';
    echo '</th>';

    echo '<th class="number" style="border-width:1px; border-style: inset;" title ="Cliquez pour trier sur la colonne">';
    echo 'Nbre de demi-journées d\'absence';
    echo '</th>';

    echo '<th class="number" style="border-width:1px; border-style: inset;" title ="Cliquez pour trier sur la colonne">';
    echo 'non justifiees';
    echo '</th>';

    echo '<th class="number" style="border-width:1px; border-style: inset;" title ="Cliquez pour trier sur la colonne">';
    echo 'non valables';
    echo '</th>';

    echo '<th class="number" style="border-width:1px; border-style: inset;" title ="Cliquez pour trier sur la colonne">';
    echo 'nbre de retards';
    echo '</th>';

    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    $csv="ELENOET;NOM;PRENOM;CLASSE;NBABS;NBNJ;NBRET;\n";
    $csv2="ELENOET;NBABS;NBNJ;NBRET;\n";
    $nb_demijournees = 0;
    $nb_nonjustifiees = 0;
    $nb_retards = 0;
    // 20170914
    $nb_non_valables=0;
    $date_debut_mysql=strftime("%Y-%m-%d %H:%M:%S", $dt_date_absence_eleve_debut->format('U'));
    $date_fin_mysql=strftime("%Y-%m-%d %H:%M:%S", $dt_date_absence_eleve_fin->format('U'));
    /*
    // Remplir le tableau des motifs
    $tab_motifs=array();
	$sql="SELECT * FROM a_motifs;";
	//echo "$sql<br />";
	$res_v=mysqli_query($mysqli, $sql);
	while($lig_v=mysqli_fetch_assoc($res_v)) {
		$tab_motifs[$lig_v["id"]]=$lig_v;
	}
	*/
    // Remplir le tableau des motifs valables
    $tab_id_motif_valable=array();
	$sql="SELECT * FROM a_motifs WHERE valable='y';";
	//echo "$sql<br />";
	$res_v=mysqli_query($mysqli, $sql);
	while($lig_v=mysqli_fetch_object($res_v)) {
		$tab_id_motif_valable[]=$lig_v->id;
	}
	/*
	echo "\$tab_id_motif_valable<pre>";
	print_r($tab_id_motif_valable);
	echo "</pre>";
	*/

	// 20201126: Boucler sur la liste des classes
	if(isset($boucler_sur_classes)) {
		// Créer la table avec des champs id, id_eleve, date_debut, date_fin...
		// Pouvoir parser les date_debut, date_fin déjà enregistrés?
		$sql="CREATE TABLE IF NOT EXISTS a_agregation_demi_journees (
			id INTEGER(11) NOT NULL AUTO_INCREMENT,
			eleve_id INTEGER(11) NOT NULL DEFAULT '0',
			elenoet INTEGER(11) NOT NULL DEFAULT '0',
			login VARCHAR(50) NOT NULL DEFAULT '',
			nom VARCHAR(50) NOT NULL DEFAULT '',
			prenom VARCHAR(50) NOT NULL DEFAULT '',
			id_classe INTEGER(11) NOT NULL DEFAULT '0',
			classe VARCHAR(50) NOT NULL DEFAULT '',
			NbAbsences INTEGER(11) NOT NULL DEFAULT '0',
			NbNonJustifiees INTEGER(11) NOT NULL DEFAULT '0',
			NbNonValables INTEGER(11) NOT NULL DEFAULT '0',
			NbRetards INTEGER(11) NOT NULL DEFAULT '0',
			date_debut DATE NOT NULL default '1970-01-01',
			date_fin DATE NOT NULL default '1970-01-01',
			date_extraction DATETIME NOT NULL default '1970-01-01 00:00:00',
			id_extraction INTEGER NOT NULL DEFAULT '0',
			PRIMARY KEY (id)
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		//echo "$sql<br />";
		$create_table=mysqli_query($mysqli, $sql);

		$date_extraction_mysql=strftime("%Y-%m-%d %H:%M:%S", $date_extraction);
	}

    $alt=1;
    $acces_visu_eleve=acces("/eleves/visu_eleve.php", $_SESSION['statut']);
    foreach ($eleve_col as $eleve) {
	    $alt=$alt*(-1);
	    echo '<tr class="lig'.$alt.' white_hover">';
	    
	    echo '<td style="border:1px; border-style: inset;">';
	    if($acces_visu_eleve) {
			echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."' target='_blank' title=\"Afficher la fiche élève dans un autre onglet ou une autre fenêtre.\">";
			echo $eleve->getNom().' '.$eleve->getPrenom();
			echo "</a>";
		}
		else {
			echo $eleve->getNom().' '.$eleve->getPrenom();
		}
	    $csv.=$eleve->getElenoet().";".$eleve->getNom().";".$eleve->getPrenom().";";
	    $csv2.=$eleve->getElenoet().";";
	    echo '</td>';

	    echo '<td style="border:1px; border-style: inset;">';
	    echo $eleve->getClasseNom();
	    $csv.=$eleve->getClasseNom().";";
	    echo '</td>';

	    echo '<td style="border:1px; border-style: inset;">';
	    echo $eleve->getNbAbsences();
	    $csv.=$eleve->getNbAbsences().";";
	    $csv2.=$eleve->getNbAbsences().";";
	    $nb_demijournees = $nb_demijournees + $eleve->getNbAbsences();
	    echo '</td>';

	    echo '<td style="border:1px; border-style: inset;">';
	    echo $eleve->getNbNonJustifiees();
	    $csv.=$eleve->getNbNonJustifiees().";";
	    $csv2.=$eleve->getNbNonJustifiees().";";
	    $nb_nonjustifiees = $nb_nonjustifiees + $eleve->getNbNonJustifiees();
	    echo '</td>';

	    echo '<td style="border:1px; border-style: inset;">';

		//echo $dt_date_absence_eleve_debut->format('U')."<br />";
		$current_non_valable=0;
		$sql="SELECT * FROM a_agregation_decompte WHERE eleve_id='".$eleve->getId()."' AND 
									date_demi_jounee>='".$date_debut_mysql."' AND 
									date_demi_jounee<'".$date_fin_mysql."' AND 
									manquement_obligation_presence='1';";
		//echo "$sql<br />";
		$res_nv=mysqli_query($mysqli, $sql);
		while($lig_nv=mysqli_fetch_object($res_nv)) {
			//echo $lig_nv->motifs_absences." (".$lig_nv->date_demi_jounee.")<br />";
			$current_valable=0;
			$tmp_tab=explode("|", $lig_nv->motifs_absences);
			for($loop_motif=0;$loop_motif<count($tmp_tab);$loop_motif++) {
				if(in_array(trim($tmp_tab[$loop_motif]), $tab_id_motif_valable)) {
					$current_valable=1;
					break;
				}
			}
			if($current_valable==0) {
				$current_non_valable++;
			}
		}
	    echo $current_non_valable;
	    $csv.=$current_non_valable.";";
	    $csv2.=$current_non_valable.";";
	    $nb_nonjustifiees = $nb_nonjustifiees + $current_non_valable;
	    echo '</td>';

	    echo '<td style="border:1px; border-style: inset;">';
	    echo $eleve->getNbRetards();
	    $csv.=$eleve->getNbRetards().";\n";
	    $csv2.=$eleve->getNbRetards().";\n";
	    $nb_retards = $nb_retards + $eleve->getNbRetards();
	    echo '</td>';

	    echo '</tr>';

		// 20201126
		if(isset($boucler_sur_classes)) {
			$sql="INSERT INTO a_agregation_demi_journees SET eleve_id='".$eleve->getId()."',
			elenoet='".$eleve->getElenoet()."',
			login='".$eleve->getLogin()."',
			nom='".mysqli_real_escape_string($mysqli, $eleve->getNom())."',
			prenom='".mysqli_real_escape_string($mysqli, $eleve->getPrenom())."',
			id_classe='".$id_classe."',
			classe='".mysqli_real_escape_string($mysqli, $classe_courante)."',
			NbAbsences='".$eleve->getNbAbsences()."',
			NbNonJustifiees='".$eleve->getNbNonJustifiees()."',
			NbNonValables='".$current_non_valable."',
			NbRetards='".$eleve->getNbRetards()."',
			date_debut='".$date_debut_mysql."',
			date_fin='".$date_fin_mysql."',
			date_extraction='".$date_extraction_mysql."',
			id_extraction='".$id_extraction."';";
			//echo "$sql<br />";
			$insert=mysqli_query($mysqli, $sql);
		}

    }
    echo '<tbody>';
    echo '<tfoot>';
    echo '<tr>';

    echo '<th style="border:1px; border-style: inset;">';
    echo 'Total élèves : ';
    echo $eleve_col->count();
    echo '</th>';

    echo '<th style="border:1px; border-style: inset;">';
    echo '</th>';

    echo '<th style="border:1px; border-style: inset;">';
    echo $nb_demijournees;
    echo '</th>';

    echo '<th style="border:1px; border-style: inset;">';
    echo $nb_nonjustifiees;
    echo '</th>';

    echo '<th style="border:1px; border-style: inset;">';
    echo $nb_non_valables;
    echo '</th>';

    echo '<th style="border:1px; border-style: inset;">';
    echo $nb_retards;
    echo '</th>';

    echo '</tr>';
    echo '</tfoot>';
    echo '</table>';
    echo '<h5>Extraction faite le '.date("d/m/Y - h:i").'</h5>';

    if(isset($_POST['generer_csv'])) {
        if($user_temp_dir) {
            $chemin_fichier="../temp/".$user_temp_dir."/extraction_abs_".strftime("%Y%m%d%H%M%S").".csv";
            $f=fopen($chemin_fichier, "w+");
            if(!$f) {
                echo "<p style='color:red'>ERREUR : Il n'a pas été possible de créer un fichier CSV dans votre dossier temporaire.</p>\n";
            }
            else {
                fwrite($f, $csv2);
                fclose($f);

                $chemin_fichier1="../temp/".$user_temp_dir."/extraction_abs_plus_".strftime("%Y%m%d%H%M%S").".csv";
                $f=fopen($chemin_fichier1, "w+");
                fwrite($f, $csv);
                fclose($f);
                echo "<p><a href='$chemin_fichier' target='_blank'>Télécharger le CSV</a> ou le <a href='$chemin_fichier1' target='_blank'>CSV avec nom, prénom, classe</a></p>
                <script type='text/javascript'>
                    document.getElementById('p_csv').innerHTML=\"<a href='$chemin_fichier' target='_blank'>Télécharger le CSV</a> ou le <a href='$chemin_fichier1' target='_blank'>CSV avec nom, prénom, classe</a>\";
                    document.getElementById('p_csv').style.display='';
                </script>\n";
            }
        }
    }

	// 20201126: Boucler sur la liste des classes
	if(isset($boucler_sur_classes)) {
		echo "<p id='bilan_boucler_sur_classes' style='display:none;'></p>
		<script type='text/javascript'>
			if($boucler_sur_classes<document.getElementById('id_classe').length) {
				//alert(\"document.getElementById('id_classe').selectedIndex=\"+document.getElementById('id_classe').selectedIndex);
				//alert($boucler_sur_classes);
				document.getElementById('id_classe').selectedIndex=$boucler_sur_classes;
				setTimeout(\"document.getElementById('choix_extraction').submit()\", 3000);
			}
			else {
				document.location.href='".$_SERVER['PHP_SELF']."?boucler_sur_classes_affichage_bilan=y&id_extraction=".$id_extraction."';
			}
		</script>";
	}

} else if ($affichage == 'ods') {
    // load the TinyButStrong libraries    
	include_once('../tbs/tbs_class.php'); // TinyButStrong template engine
    
    //include_once('../tbs/plugins/tbsdb_php.php');
    $TBS = new clsTinyButStrong; // new instance of TBS
    include_once('../tbs/plugins/tbs_plugin_opentbs.php');
    $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

    // Load the template
	$extraction_demi_journees=repertoire_modeles('absence_extraction_demi-journees.ods');
    $TBS->LoadTemplate($extraction_demi_journees, OPENTBS_ALREADY_UTF8);

    $titre = 'Extrait des demi-journées d\'absences du '.$dt_date_absence_eleve_debut->format('d/m/Y').' au '.$dt_date_absence_eleve_fin->format('d/m/Y');
    $classe = null;
    if ($id_classe != null && $id_classe != '') {
	$classe = ClasseQuery::create()->findOneById($id_classe);
	if ($classe != null) {
	    $titre .= ' pour la classe '.$classe->getNom();
	}
    }
    if ($nom_eleve != null && $nom_eleve != '' ) {
	$titre .= ' pour les élèves dont le nom ou le prénom contient '.$nom_eleve;
    }
    $TBS->MergeField('titre', $titre);

    $eleve_array_avec_data = Array();
    $nb_demijournees = 0;
    $nb_nonjustifiees = 0;
    $nb_retards = 0;
    foreach ($eleve_col as $eleve) {
	$eleve_array_avec_data[$eleve->getPrimaryKey()] = Array(
	    'eleve' => $eleve
	    , 'getDemiJourneesAbsencePreRempli' => $eleve->getNbAbsences()
	    , 'getDemiJourneesNonJustifieesPreRempli' => $eleve->getNbNonJustifiees()
	    , 'getRetardsPreRempli' => $eleve->getNbRetards()
		);

	    $nb_demijournees = $nb_demijournees + $eleve->getNbAbsences();
	    $nb_nonjustifiees = $nb_nonjustifiees + $eleve->getNbNonJustifiees();
	    $nb_retards = $nb_retards + $eleve->getNbRetards();
    }


    $TBS->MergeBlock('eleve_col', $eleve_array_avec_data);

    $TBS->MergeField('eleve_count', $eleve_col->count());
    $TBS->MergeField('nb_demijournees',$nb_demijournees);
    $TBS->MergeField('nb_nonjustifiees', $nb_nonjustifiees);
    $TBS->MergeField('nb_retards', $nb_retards);

    // Output as a download file (some automatic fields are merged here)
    $nom_fichier = 'extrait_demi-journee_';
    if ($classe != null) {
	$nom_fichier .= $classe->getNom().'_';
    }
    $nom_fichier .=  $dt_date_absence_eleve_fin->format("d_m_Y").'.ods';
    $TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, $nom_fichier);
}
?>
	</div>
<?php
$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dojo.parser");
    dojo.require("dijit.form.Button");    
    dojo.require("dijit.form.Form");    
    dojo.require("dijit.form.DateTextBox");
    dojo.require("dijit.form.TextBox");
    dojo.require("dijit.form.Select");
    </script>';
require_once("../lib/footer.inc.php");
?>
