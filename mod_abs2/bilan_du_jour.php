<?php
/**
 *
 *
 * Copyright 2010 Josselin Jacquard
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

// Initialisation des variables
$date_absence_eleve = isset($_POST["date_absence_eleve"]) ? $_POST["date_absence_eleve"] :(isset($_GET["date_absence_eleve"]) ? $_GET["date_absence_eleve"] :(isset($_SESSION["date_absence_eleve"]) ? $_SESSION["date_absence_eleve"] : NULL));
if ($date_absence_eleve != null) {$_SESSION["date_absence_eleve"] = $date_absence_eleve;}

if ($date_absence_eleve != null) {
    $dt_date_absence_eleve = new DateTime(str_replace("/",".",$date_absence_eleve));
} else {
    $dt_date_absence_eleve = new DateTime('now');
}

$afficher_retard="y";
$afficher_manquement="y";
$afficher_non_manquement="y";
if(isset($_POST["date_absence_eleve"])) {
	$afficher_retard=isset($_POST['afficher_retard']) ? "y" : "n";
	$afficher_manquement=isset($_POST['afficher_manquement']) ? "y" : "n";
	$afficher_non_manquement=isset($_POST['afficher_non_manquement']) ? "y" : "n";
}

$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
//$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
$dojo=true;
//**************** EN-TETE *****************
$titre_page = "Les absences";
require_once("../lib/header.inc.php");
include('menu_abs2.inc.php');
include('menu_bilans.inc.php');
?>

<div id="contain_div" class="css-panes">
	<div class="legende">
		<h3 class="legende">Légende</h3>
		<font color="orange">&#9632;</font> Retard<br />
		<font color="red">&#9632;</font> Manquement aux obligations de présence<br />
		<font color="blue">&#9632;</font> Non manquement aux obligations de présence<br />
	</div>

<form dojoType="dijit.form.Form" id="choix_date" name="choix_date" action="<?php $_SERVER['PHP_SELF']?>" method="post">
	<h2>Les saisies du
		<input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve" name="date_absence_eleve" onchange="document.choix_date.submit()" value="<?php echo $dt_date_absence_eleve->format('Y-m-d')?>" />
		<button style="font-size:12px" dojoType="dijit.form.Button" type="submit">Changer</button>

		 - Afficher&nbsp;:<input type='checkbox' name='afficher_retard' id='afficher_retard' value='y' <?php if($afficher_retard=="y") {echo "checked ";}?>/><label for='afficher_retard'>Retards</label>
		 - <input type='checkbox' name='afficher_manquement' id='afficher_manquement' value='y' <?php if($afficher_manquement=="y") {echo "checked ";}?>/><label for='afficher_manquement'>Manquements</label>
		 - <input type='checkbox' name='afficher_non_manquement' id='afficher_non_manquement' value='y' <?php if($afficher_non_manquement=="y") {echo "checked ";}?>/><label for='afficher_non_manquement'>Non-manquements</label>
	</h2>
</form>

<table style="border: 1px solid black; background-color:lightgrey" cellpadding="5" cellspacing="5">
<?php $creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();?>
	<tr>
		<th style="border: 1px solid black; background-color: gray;">Classe</th>
		<th style="border: 1px solid black; background-color: gray; min-width: 300px; max-width: 500px;">Nom Pr&eacute;nom</th>
<?php
		//afficher les créneaux
		foreach(EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $creneau){
			echo "<th style=\"border: 1px solid black; background-color: grey;\">".$creneau->getNomDefiniePeriode()."</th>\n";
		}
?>
	</tr>
<?php
$dt_debut = $dt_date_absence_eleve;
$dt_debut->setTime(0,0,0);
$dt_fin = clone $dt_date_absence_eleve;
$dt_fin->setTime(23,59,59);

if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    $classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
} else {
    $classe_col = $utilisateur->getClasses();
}
if ($classe_col->isEmpty()) {
    echo '	<tr>
			<td colspan="'.($creneau_col->count() + 2).'">Aucune classe avec élève affecté n\'a été trouvée</td>
		</tr>';
}
foreach($classe_col as $classe) {
	echo '
		<tr>
			<td>'.$classe->getNom().'</td>
			<td colspan="'.($creneau_col->count() + 1).'"></td>
		</tr>
		';
	$eleve_query = EleveQuery::create()
		->orderByNom()
		->useAbsenceEleveSaisieQuery()->filterByPlageTemps($dt_debut, $dt_fin)->where('AbsenceEleveSaisie.DeletedAt is Null')->endUse()
		->filterByClasse($classe,$dt_date_absence_eleve)
        ->where('Eleve.DateSortie<?','0')
        ->orWhere('Eleve.DateSortie is NULL')
        ->orWhere('Eleve.DateSortie>?', $dt_date_absence_eleve->format('U'))
		->distinct();

	if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
	    //on ne filtre pas
	} else {
	    $eleve_query->filterByUtilisateurProfessionnel($utilisateur);
	}
	$eleve_col = $eleve_query->find();
	$cpt_eleve=0;
	foreach($eleve_col as $eleve){
			$affiche = false;
			foreach($eleve->getAbsenceEleveSaisiesDuJour($dt_debut) as $abs) {
			    $affiche = false;
			    if (!$abs->getManquementObligationPresenceSpecifie_NON_PRECISE() ) {
					$affiche = true;
					break;
			    }
			}

			if (!$affiche) {
                continue;
			}

			if(($afficher_retard!="y")||($afficher_manquement!="y")||($afficher_non_manquement!="y")) {
				// On passe en revue les "absences" de cet élève pour déterminer si l'une d'elle doit être affichée
				$au_moins_une_a_afficher="n";
				foreach($creneau_col as $creneau) {
					if($au_moins_une_a_afficher=="y") {break;}
					$abs_col = $eleve->getAbsenceEleveSaisiesDuCreneau($creneau, $dt_date_absence_eleve);
					if (!$abs_col->isEmpty()) {
						foreach($abs_col as $abs) {
							if (!$abs->getManquementObligationPresenceSpecifie_NON_PRECISE()){
								if(($abs->getColor()=='red')&&($afficher_manquement=="y")) {
									$au_moins_une_a_afficher="y";
									break;
								}
								elseif(($abs->getColor()=='orange')&&($afficher_retard=="y")) {
									$au_moins_une_a_afficher="y";
									break;
								}
								elseif(($abs->getColor()=='blue')&&($afficher_non_manquement=="y")) {
									$au_moins_une_a_afficher="y";
									break;
								}
							}
						}
					}
				}

				if($au_moins_une_a_afficher=="n") {$affiche=false;}
			}

			if (!$affiche) {
                continue;
			}

			if ($cpt_eleve%2==0) {
				//$background_couleur="rgb(220, 220, 220);";
				$background_couleur="silver;";
			} else {
				//$background_couleur="rgb(210, 220, 230);";
				$background_couleur="lightblue;";
			}
			$cpt_eleve++;

            echo '<tr style="background-color :'.$background_couleur.'">
                <td><pre>';
            //print_r($abs);
            echo '</pre></td>
			<td>';
			if ($utilisateur->getAccesFicheEleve($eleve)) {
			    //echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."' target='_blank'>";
			    echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."&amp;onglet=responsables&amp;quitter_la_page=y' target='_blank'>";
			    echo $eleve->getNom().' '.$eleve->getPrenom();
			    echo "</a>";
			} else {
			    echo $eleve->getNom().' '.$eleve->getPrenom();
			}
			echo '</td>';
			// On traite alors pour chaque créneau
			foreach($creneau_col as $creneau) {
			    $abs_col = $eleve->getAbsenceEleveSaisiesDuCreneau($creneau, $dt_date_absence_eleve);
			    if ($abs_col->isEmpty()){
				echo '<td></td>';
			    } else {
				foreach($abs_col as $abs) {
                    if ($abs->getManquementObligationPresenceSpecifie_NON_PRECISE()){
                        echo '<td></td>';
                        break;
                    }else{
                        echo '<td style="background-color:'.$abs->getColor().';text-align:center;"';
                        if($abs->getColor()=='red') {
                        	//echo " title=\"Manquement aux obligations de présence\"><span style=\"color:".$abs->getColor()."\">M</span>";
                        	echo " title=\"Manquement aux obligations de présence\">";
                        	echo "<a href='visu_saisie.php?id_saisie=".$abs->getId()."'>";
                        	echo "M";
                        	echo "</a>";
                        }
                        elseif($abs->getColor()=='orange') {
                        	//echo " title=\"Retard\"><span style=\"color:".$abs->getColor()."\">R</span>";
                        	echo " title=\"Retard\">";
                        	echo "<a href='visu_saisie.php?id_saisie=".$abs->getId()."'>";
                        	echo "R";
                        	echo "</a>";
                        }
                        elseif($abs->getColor()=='blue') {
                        	//echo " title=\"Non manquement aux obligations de présence\"><span style=\"color:".$abs->getColor()."\">NM</span>";
                        	echo " title=\"Non manquement aux obligations de présence\">";
                        	echo "<a href='visu_saisie.php?id_saisie=".$abs->getId()."'>";
                        	echo "NM";
                        	echo "</a>";
                        }
                        else {
                        	echo ">";
                        }
                        echo '</td>';
                        break; 
                    }
				}
				/*
				if (!$red) {
				    
				}
				 * 
				 */
			    }
			}
			echo '</tr>';
	}
}
?>
</table>
<br />
  <span class="bold">Impression faite le <?php echo date("d/m/Y - H:i"); ?>.</span>
</div>
<?php
$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dojo.parser");
    dojo.require("dijit.form.Button");    
    dojo.require("dijit.form.Form");    
    dojo.require("dijit.form.DateTextBox");
    </script>';
require_once("../lib/footer.inc.php");
?>
