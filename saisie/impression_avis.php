<?php
/*
 *
 * Copyright 2001, 2020 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

//INSERT INTO droits VALUES ('/saisie/impression_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F','Impression des avis trimestrielles des conseils de classe.', '');

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
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


// On teste si un professeur peut saisir les avis
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiRubConseilProf")!='yes') {
	die("Droits insuffisants pour effectuer cette opération");
}

// On teste si le service scolarité peut saisir les avis
if (($_SESSION['statut'] == 'scolarite') and getSettingValue("GepiRubConseilScol")!='yes') {
	die("Droits insuffisants pour effectuer cette opération");
}

if((isset($_GET['mode']))&&($_GET['mode']=='export_csv')&&(isset($_GET['id_classe']))&&(preg_match("/^[0-9]{1,}$/", $_GET['id_classe']))&&(isset($_GET['periode_num']))&&(preg_match("/^[0-9]{1,}$/", $_GET['periode_num']))) {

	// Vérifier dans le cas d'un PP qu'il a accès à la classe
	if($_SESSION['statut']=='professeur') {
		$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id AND c.id='".$_GET['id_classe']."');";
		$test = mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			header("Location: ../accueil.php?msg=Accès non autorisé à cette classe.");
			die();
		}
	}

	$csv="";
	$ligne_entete="";
	$lignes_csv="";

	$ligne_entete="login;nom;prenom;classe;periode;avis;";

	$ligne_entete.=getSettingValue('gepi_denom_mention').";";

	if(getSettingAOui('active_mod_discipline')) {
		$tab_type_avertissement_fin_periode=get_tab_type_avertissement();
		if(count($tab_type_avertissement_fin_periode)>0) {
			$ligne_entete.=getSettingValue('mod_disc_terme_avertissement_fin_periode').";";

			$tab_avt_ele=liste_avertissements_fin_periode_classe($_GET['id_classe'], $_GET['periode_num'], "nom_complet", "n", "n");
		}
	}

	$ligne_entete.="\r\n";

	$nom_classe=remplace_accents(get_nom_classe($_GET['id_classe']), 'all');

	$sql="SELECT e.*, acc.avis, acc.id_mention FROM avis_conseil_classe acc, 
				eleves e,
				j_eleves_classes jec 
			WHERE acc.login=e.login AND 
				e.login=jec.login AND 
				jec.id_classe='".$_GET['id_classe']."' AND 
				jec.periode='".$_GET['periode_num']."' 
			ORDER BY e.nom, e.prenom;";
	//echo $sql."\r\n";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {

			//$avis=preg_replace('/(\\\n){1,}/', '', preg_replace('/(\\\r){1,}/', '', preg_replace('/;/', '.,', $lig->avis)));
			$avis=preg_replace('/\n+/', ' ', preg_replace('/\r+/', ' ', preg_replace('/;/', '.,', $lig->avis)));

			$lignes_csv.=$lig->login.";".$lig->nom.";".$lig->prenom.";".$nom_classe.";".$_GET['periode_num'].";".$avis.";";

			$lignes_csv.=preg_replace('/;/', '.,', traduction_mention($lig->id_mention)).";";


			if(getSettingAOui('active_mod_discipline')) {
				// Récupérer les avertissements des élèves de la classe pour la période en cours.
				if(count($tab_type_avertissement_fin_periode)>0) {

					if(isset($tab_avt_ele[$lig->login])) {
						$lignes_csv.=preg_replace('/;/', '.,', $tab_avt_ele[$lig->login]).";";
					}

				}
			}



			$lignes_csv.="\r\n";
		}
	}

	$csv=$ligne_entete.$lignes_csv;

	$nom_fic=remplace_accents("avis_conseil_classe_".$nom_classe, 'all')."_".strftime("%Y%m%d_%H%M%S").".csv";
	send_file_download_headers('text/x-csv',$nom_fic);
	echo echo_csv_encoded($csv);
	die();
}


//**************** EN-TETE *****************
$titre_page = "Impression des avis du conseil de classe";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

$id_liste_periodes=isset($_POST['id_liste_periodes']) ? $_POST["id_liste_periodes"] : 0;


echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
if ($id_liste_periodes!=0) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres périodes</a>";
}
if((($_SESSION['statut']=='professeur')&&(is_pp($_SESSION['login'])))||($_SESSION['statut']!='professeur')) {
	echo " | <a href='../impression/parametres_impression_pdf_avis.php'>Régler les paramètres du PDF</a>";
}
echo "</p>\n";

if (($_SESSION['statut'] == 'scolarite')||($_SESSION['statut'] == 'cpe')) { // Scolarite ou Cpe

	if (($id_liste_periodes)!=0) {
	   //IMPRESSION A LA CHAINE
	   $nb_periodes = sizeof($id_liste_periodes);
	   $chaine_periodes = "";
	   for ($i=0; $i<$nb_periodes ; $i++) {
		  $chaine_periodes .= $id_liste_periodes[$i];
		  if ($i<$nb_periodes-1) { $chaine_periodes .= ' et ';}
	   }
	   $periode = "Période(s) N° ".$chaine_periodes;
		
		echo "<h3>".$periode;
		echo "</h3>\n";
	} else {
		$periode="";
		echo "<h3>Choix de la période : ";
		echo "</h3>\n";
	}

	// Impression multiple
	echo "<div style=\"text-align: center;\">\n";
	echo "<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>\n";

	if ($id_liste_periodes == 0) {
		echo "<legend style='border: 1px solid grey; background-color: white;'>Dans un premier temps, sélectionnez la (ou les) périodes pour lesquelles vous souhaitez imprimer les avis</legend>\n";
		echo "<form method=\"post\" action=\"impression_avis.php\" name=\"imprime_serie\">\n";
		$requete_periode = "SELECT DISTINCT `num_periode` FROM `periodes`";
		$resultat_periode = mysqli_query($GLOBALS["mysqli"], $requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.mysqli_error($GLOBALS["mysqli"]));
		echo "<select id='id_liste_periodes' name='id_liste_periodes[]' multiple='yes' size='4'>\n";
		echo "		<optgroup label=\"-- Les périodes --\">\n";
		While ($data_periode = mysqli_fetch_array($resultat_periode)) {
			echo "		<option value=\"";
			echo $data_periode['num_periode'];
			echo "\">";
			echo "Période N°".$data_periode['num_periode'];
			echo "</option>\n";
		}
		echo "		</optgroup>\n";
		echo "	</select>\n";	
		echo "<br />Remarque : si vous sélectionnez plusieurs périodes, les données seront triées par élève et par période pour chaque classe";		
		echo "<br /><br /> <input value=\"Valider la période\" name=\"Valider\" type=\"submit\" />\n<br />\n";			
		echo "</form>\n";
	} else {
		echo "<legend style='border: 1px solid grey; background-color: white;'>Puis, pour les périodes choisies, séléctionnez la (ou les) classe(s) pour lesquelles vous souhaitez imprimer les avis.</legend>\n";
		echo "<form method=\"post\" action=\"../impression/avis_pdf.php\" target='_blank' name=\"avis_pdf\">\n";
		
		//passage des paramètres de la période dans la session.
		$_SESSION['id_liste_periodes']=NULL;
		
		if ($id_liste_periodes != 0) {
			$_SESSION['id_liste_periodes']=$id_liste_periodes;
			$id_la_premiere_periode = $id_liste_periodes[0];
		}
		
		echo "<br />\n";

		echo "<select id='liste_classes' name='id_liste_classes[]' multiple='yes' size='5'>\n";
			if($_SESSION['statut']=='scolarite'){ //n'affiche que les classes du profil scolarité
				$login_scolarite = $_SESSION['login'];
				$requete_classe = "SELECT `periodes`.`id_classe`, `classes`.`classe`, `classes`.`nom_complet` , jsc.login, jsc.id_classe
								FROM `periodes`, `classes` , `j_scol_classes` jsc
								WHERE (jsc.login='$login_scolarite'
								AND jsc.id_classe=classes.id
								AND `periodes`.`num_periode` = ".$id_la_premiere_periode."
								AND `classes`.`id` = `periodes`.`id_classe`)
								ORDER BY `nom_complet` ASC";
			}
			elseif(($_SESSION['statut']=='cpe')&&(!getSettingAOui('GepiRubConseilCpeTous'))) { //n'affiche que les classes du profil scolarité
				$requete_classe = "SELECT DISTINCT `periodes`.`id_classe`, `classes`.`classe`, `classes`.`nom_complet` , jecpe.cpe_login
								FROM `periodes`, `classes` , `j_eleves_cpe` jecpe, j_eleves_classes jec
								WHERE (jecpe.cpe_login='".$_SESSION['login']."'
								AND jec.id_classe=classes.id
								AND jec.login=jecpe.e_login
								AND `periodes`.`num_periode` = ".$id_la_premiere_periode."
								AND `classes`.`id` = `periodes`.`id_classe`)
								ORDER BY `nom_complet` ASC";
			} else {
				$requete_classe = "SELECT `periodes`.`id_classe`, `classes`.`classe`, `classes`.`nom_complet` FROM `periodes`, `classes` WHERE `periodes`.`num_periode` = ".$id_la_premiere_periode." AND `classes`.`id` = `periodes`.`id_classe` ORDER BY `nom_complet` ASC";
			}
			$resultat_classe = mysqli_query($GLOBALS["mysqli"], $requete_classe) or die('Erreur SQL !'.$requete_classe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
			echo "		<optgroup label=\"-- Les classes --\">\n";
			while ( $data_classe = mysqli_fetch_array($resultat_classe)) {
				echo "		<option value=\"";
				echo $data_classe['id_classe'];
				echo "\">";
				echo $data_classe['nom_complet']." (".$data_classe['classe'].")";
				echo "</option>\n";
			}
			echo "		</optgroup>\n";
		echo "	</select>\n";
		echo "<br /><br /> <input value=\"Valider les classes\" name=\"Valider\" type=\"submit\" />\n";
		echo "<br />\n";
		
		echo "</form>\n";
	}
	echo "</fieldset>\n";
	echo "</div>";

	echo "<br />\n";
	
	echo "<h3>Liste des classes : </h3>\n"; //modele imprime PDF
	
	// Pour le compte scolarite, possibilité d'imprimer les avis en synthèse (pour ses classes)
	echo "<p>Séléctionnez la classe et la période pour lesquels vous souhaitez imprimer les avis :</p>\n";

	$sql = "SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	$result_classes=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_classes = mysqli_num_rows($result_classes);

	if(mysqli_num_rows($result_classes)==0){
		echo "<p>Il semble qu'aucune classe n'ait encore été créée.</p>\n";
	}
	else {
		$nb_classes=mysqli_num_rows($result_classes);
		$nb_class_par_colonne=round($nb_classes/3);
		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='left'>\n";
		$cpt=0;
		//echo "<td style='padding: 0 10px 0 10px'>\n";
		echo "<td>\n";
		echo "<table border='1' class='boireaus'>\n";
		while($lig_class=mysqli_fetch_object($result_classes)){
			if(($cpt>0)&&(round($cpt/$nb_class_par_colonne)==$cpt/$nb_class_par_colonne)){
				echo "</table>\n";
				echo "</td>\n";
				//echo "<td style='padding: 0 10px 0 10px'>\n";
				echo "<td>\n";
				echo "<table border='1' class='boireaus'>\n";
			}

			$sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$lig_class->id' ORDER BY num_periode";
			$res_per=mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($res_per)==0){
				echo "<p>ERREUR: Aucune période n'est définie pour la classe $lig_class->classe</p>\n";
				echo "</body></html>\n";
				die();
			}
			else{
				echo "<tr>\n";
				echo "<th>$lig_class->classe</th>\n";
				$alt=1;
				while($lig_per=mysqli_fetch_object($res_per)){
					$alt=$alt*(-1);
					echo "<td class='lig$alt'> - <a href='../impression/avis_pdf.php?id_classe=$lig_class->id&amp;periode_num=$lig_per->num_periode' target='_blank'>".$lig_per->nom_periode."</a></td>\n";
				}
				echo "</tr>\n";
			}
			$cpt++;
		}
		echo "</table>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
} elseif($_SESSION['statut']=='professeur') { // appel pour un prof
    echo "<br />";
    $call_prof_classe = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
    $nombre_classe = mysqli_num_rows($call_prof_classe);
    if ($nombre_classe == "0") {
        echo "Vous n'êtes pas ".getSettingValue("gepi_prof_suivi")." ! Il ne vous revient donc pas d'imprimer les avis de conseil de classe.";
    } else {
        $j = "0";
        echo "<p>Vous êtes ".getSettingValue("gepi_prof_suivi")." dans la classe de :</p>";
        while ($j < $nombre_classe) {
            $id_classe = old_mysql_result($call_prof_classe, $j, "id");
            $classe_suivi = old_mysql_result($call_prof_classe, $j, "classe");

            include "../lib/periodes.inc.php";
            $k="1";
            while ($k < $nb_periode) {

                   echo "<br /><b>$classe_suivi </b>- ";
                   echo "<a href='../impression/avis_pdf.php?id_classe=$id_classe&amp;periode_num=$k'>".ucfirst($nom_periode[$k])." <img src='../images/icons/pdf.png' class='icone16' /></a>";

			echo " - <a href='".$_SERVER['PHP_SELF']."?id_classe=".$id_classe."&amp;mode=export_csv&amp;periode_num=$k' target='_blank' title=\"Exporter les avis en CSV.\"><img src='../images/icons/csv.png' class='icone16' /></a>";


               $k++;
            }
            echo "<br />";
		echo "<p><a href='../impression/avis_pdf.php?id_classe=$id_classe&periode_num=toutes' title=\"Imprimer les avis au format PDF de la classe de $classe_suivi pour l'ensemble des périodes.\"><img src='../images/icons/pdf.png' class='icone16' alt='PDF' /> Imprimer les avis du conseil de classe de $classe_suivi pour toutes les périodes.</a></p>";
            $j++;
        }
    }
} else {
	echo "<p style='color:red'>Vous n'avez aucun droit ici.</p>\n";
}
require("../lib/footer.inc.php");
?>
