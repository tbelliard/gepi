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

/*
  // mise à jour des droits dans la table droits
  $sql="INSERT INTO `droits` ( `id` , `administrateur` , `professeur` , `cpe` , `scolarite` , `eleve` , `responsable` , `secours` , `autre` , `description` , `statut` )
  VALUES ('/mod_abs2/bilan_parent.php', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'F', 'Affichage parents des absences de leurs enfants', '')
  ON DUPLICATE KEY UPDATE `responsable` = 'V'";

  $result = mysqli_query($mysqli, $sql);
 */

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
if (getSettingValue("active_module_absence") != '2') {
  die("Le module n'est pas activé.");
}

if ($utilisateur->getStatut() != "responsable") {
  die("acces interdit");
}
require_once("helpers/EdtHelper.php");
// Initialisation des variables
$date_absence_eleve = isset($_POST["date_absence_eleve"]) ? $_POST["date_absence_eleve"] : (isset($_GET["date_absence_eleve"]) ? $_GET["date_absence_eleve"] : (isset($_SESSION["date_absence_eleve"]) ? $_SESSION["date_absence_eleve"] : NULL));
if ($date_absence_eleve != null) {
  $_SESSION["date_absence_eleve"] = $date_absence_eleve;
}

if ($date_absence_eleve != null) {
  $dt_date_absence_eleve = new DateTime(str_replace("/", ".", $date_absence_eleve));
} else {
  $dt_date_absence_eleve = new DateTime('now');
}


// Initialisation toutes absences

$dt_fin_toutes = isset($_POST["date_fin_toute_absence"]) ? new DateTime(str_replace("/", ".", $_POST["date_fin_toute_absence"])) : date_create();

if ($dt_fin_toutes->format('m') >= 8) {
  $date_debut_toutes = $dt_fin_toutes->format('Y') . "-08-01";
} else {
  $date_debut_toutes = ($dt_fin_toutes->format('Y') - 1) . "-08-01";
}
$dt_debut_toutes = isset($_POST["date_debut_toute_absence"]) ? new DateTime(str_replace("/", ".", $_POST["date_debut_toute_absence"])) : date_create($date_debut_toutes);

if ($dt_fin_toutes < $dt_debut_toutes) {
  $dt = $dt_fin_toutes;
  $dt_fin_toutes = $dt_debut_toutes;
  $dt_debut_toutes = $dt;
}

$dt_debut_toutes->setTime(0, 0, 0);
$dt_fin_toutes->setTime(23, 59, 59);

//$date_interval= new DateInterval("P1D");
//$date_traite="";
// Fin Initialisation toutes absences


$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
//$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";

//renvoi la priorite d'affichage : 1:Retard Justifie ; 2 Absence Justifiee ; 3 Retard Non justifé ; 4 Absence non justifiée
function get_priorite($abs) {
  if ($abs->getJustifiee()) {
    if ($abs->getRetard()) {
      $priorite = 1;
    } else {
      $priorite = 2;
    }
  } else {
    if ($abs->getRetard()) {
      $priorite = 3;
    } else {
      $priorite = 4;
    }
  }
  return($priorite);
}

//On verifie que l'absence est un manquement et n'est pas incluse dans des crenaux fermes(mercredi après midi par exemple)
function isAffichable($abs, $date, $eleve) {
  $creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();
  $test_ouverture = false;
  foreach ($creneau_col as $creneau) {
    $datedebutabs = explode(" ", $abs->getDebutAbs());
    $dt_date_debut_abs = new DateTime($datedebutabs[0]);
    $heure_debut = explode(":", $datedebutabs[1]);
    $dt_date_debut_abs->setTime($heure_debut[0], $heure_debut[1], $heure_debut[2]);
    $tab_heure = explode(":", $creneau->getHeuredebutDefiniePeriode());
    $date->setTime($tab_heure[0], $tab_heure[1], $tab_heure[2]);
    //on verifie si le creneau est ouvert et s'il est posterieur au debut de l'absence
    if ($date->Format('U') > $dt_date_debut_abs->Format('U') && EdtHelper::isEtablissementOuvert($date)) {
      $test_ouverture = true;
    }
  }
  if ($test_ouverture && $abs->getManquementObligationPresence()) {
    return true;
  } else {
    return false;
  }
}

//**************** EN-TETE *****************
$titre_page = "Absences";
require_once("../lib/header.inc.php");
include('menu_abs2.inc.php');
include('menu_bilans.inc.php');

$mois_precedent="";
?>
<div id="contain_div" class="css-panes">
  <form id="choix_date" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
    <h2>
      <label for="date_absence_eleve_1">Les saisies du</label>
      <input size="8" id="date_absence_eleve_1" name="date_absence_eleve" onchange="document.getElementById('choix_date').submit()" value="<?php echo $dt_date_absence_eleve->format('d/m/Y') ?>" />
      <script type="text/javascript">
        Calendar.setup({
          inputField     :    "date_absence_eleve_1",     // id of the input field
          ifFormat       :    "%d/%m/%Y",      // format of the input field
          button         :    "date_absence_eleve_1",  // trigger for the calendar (button ID)
          align          :    "Bl",           // alignment (defaults to "Bl")
          singleClick    :    true
        });
      </script>
      <button type="submit">Changer</button>
    </h2>
  </form>

  <!-- Absences du jour -->
  <table style="border: 1px solid black;" cellpadding="5" cellspacing="5" title="Les absences du jour" class='boireaus boireaus_alt'>
    <?php $creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime(); ?>
    <tr>
      <th style="border: 1px solid black; background-color: gray; min-width: 300px; max-width: 500px;">Nom Pr&eacute;nom</th>
      <?php
//afficher les créneaux
      foreach (EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $creneau) {
      ?>
        <th style="border: 1px solid black; background-color: gray;" title="De <?php echo preg_replace("/:[0-9]*$/","",$creneau->getHeuredebutDefiniePeriode())." à ".preg_replace("/:[0-9]*$/","",$creneau->getHeurefinDefiniePeriode()); ?>">
        <?php echo $creneau->getNomDefiniePeriode(); ?>
      </th>
      <?php
      }
      ?>
    </tr>
    <?php
      $dt_debut = $dt_date_absence_eleve;
      $dt_debut->setTime(0, 0, 0);
      $dt_fin = clone $dt_date_absence_eleve;
      $dt_fin->setTime(23, 59, 59);
    ?>
      <tr>
        <td colspan="<?php echo ($creneau_col->count() + 1); ?>"></td>
      </tr>

    <?php
      $eleve_col = EleveQuery::create()
                      ->orderByNom()
                      ->filterByUtilisateurProfessionnel($utilisateur)
                      ->distinct()->find();

      $temoin_abs_ou_retard_ce_jour=0;
      foreach ($eleve_col as $eleve) {
		$eleve_a_afficher=is_responsable($eleve->getLogin(), $utilisateur->getLogin(), "", "yy");

		if($eleve_a_afficher) {
		    $affichage = false;
		    foreach ($eleve->getAbsenceEleveSaisiesDuJour($dt_date_absence_eleve) as $abs) {
		      if (isAffichable($abs, $dt_date_absence_eleve, $eleve)) {
		        $affichage = true;
		      }
		    }
        if ($affichage) {
            $temoin_abs_ou_retard_ce_jour++;
    ?>
        <tr>
          <td>
        <?php
        echo $eleve->getNom() . ' ' . $eleve->getPrenom();
        ?>
      </td>
      <?php
          // On traite alors pour chaque créneau
          foreach ($creneau_col as $creneau) {
            $abs_col = $eleve->getAbsenceEleveSaisiesDecompteDemiJourneesDuCreneau($creneau, $dt_date_absence_eleve);
            $abs_col->addCollection($eleve->getRetardsDuCreneau($creneau, $dt_date_absence_eleve));
            $tab_heure = explode(":", $creneau->getHeuredebutDefiniePeriode());
            $date_actuelle_heure_creneau = clone $dt_date_absence_eleve;
            $date_actuelle_heure_creneau->setTime($tab_heure[0], $tab_heure[1], $tab_heure[2]);
            if ($abs_col->isEmpty() || !EdtHelper::isEtablissementOuvert($date_actuelle_heure_creneau)) {
      ?>
              <td> </td>        
      <?php
            } else {
              $priorite = 5;
              $current_minus_4 = new DateTime();
              $current_minus_4->modify('-4 hours');
              foreach ($abs_col as $abs) {
                if (($abs->getTraitee() || $abs->getCreatedAt(null) < $current_minus_4) && get_priorite($abs) < $priorite) {
                  $priorite = get_priorite($abs);
                }
              }
              switch ($priorite) {
                case 1:
      ?>
                  <td style="background:aqua;">RJ</td>
      <?php
                  break;
                case 2:
      ?>
                  <td style="background:blue;">J</td>
      <?php
                  break;
                case 3:
      ?>
                  <td style="background:fuchsia;">RNJ</td>
      <?php
                  break;
                case 4:
      ?>
                  <td style="background:red;">NJ</td>
      <?php
                  break;
              }
            }
          }
        }
      ?>
        </tr>
    <?php
      }
		}
    ?>
    </table>
<?php
	if($temoin_abs_ou_retard_ce_jour==0) {
		echo "<p style='margin-top:1em;' class='bold'>Aucune absence saisie le ".strftime("%A %d/%m/%Y", $dt_debut->getTimestamp()).".</p>";
	}
?>
    <p style='margin-top:1em;'>
      <span style="background-color:red;">&nbsp;&nbsp;NJ&nbsp;&nbsp;</span>
              	Manquement aux obligations scolaires : Absence non justifiée (<em style='font-weight:bold' title="Merci de fournir un justificatif à la Vie Scolaire (coupon dans le carnet ou certificat médical ou autre)">justificatif attendu</em>)
    </p>
    <p>
      <span style="background-color:fuchsia;">&nbsp;RNJ&nbsp;</span>
              	Manquement aux obligations scolaires : Retard non justifié (<em style='font-weight:bold' title="Merci de fournir un justificatif à la Vie Scolaire (coupon dans le carnet ou certificat médical ou autre)">justificatif attendu</em>)
    </p>
    <p>
      <span style="background-color:blue;">&nbsp;&nbsp;&nbsp;J&nbsp;&nbsp;&nbsp;&nbsp;</span>
              	Manquement aux obligations scolaires : Absence justifiée
    </p>
    <p>
      <span style="background-color:aqua;">&nbsp;&nbsp;RJ&nbsp;&nbsp;</span>
              	Manquement aux obligations scolaires : Retard justifié
    </p>

    <!-- Absences totales -->

    <form id="toutes_abs" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
      <h2>
        <label for="date_debut_toute_absence">Les absences entre le</label>
        <input size="8" id="date_debut_toute_absence" name="date_debut_toute_absence" onchange="document.getElementById('toutes_abs').submit()" value="<?php echo $dt_debut_toutes->format('d/m/Y') ?>" />
        <script type="text/javascript">
          Calendar.setup({
            inputField     :    "date_debut_toute_absence",     // id of the input field
            ifFormat       :    "%d/%m/%Y",      // format of the input field
            button         :    "date_debut_toute_absence",  // trigger for the calendar (button ID)
            align          :    "Bl",           // alignment (defaults to "Bl")
            singleClick    :    true
          });
        </script>
        <label for="date_fin_toute_absence"> et le</label>
        <input size="8" id="date_fin_toute_absence" name="date_fin_toute_absence" onchange="document.getElementById('toutes_abs').submit()" value="<?php echo $dt_fin_toutes->format('d/m/Y') ?>" />
        <script type="text/javascript">
          Calendar.setup({
            inputField     :    "date_fin_toute_absence",     // id of the input field
            ifFormat       :    "%d/%m/%Y",      // format of the input field
            button         :    "date_fin_toute_absence",  // trigger for the calendar (button ID)
            align          :    "Bl",           // alignment (defaults to "Bl")
            singleClick    :    true
          });
        </script>
        <button type="submit">Changer</button>
      </h2>
    </form>
  <?php
      $eleve_col = EleveQuery::create()
                      ->orderByNom()
                      ->filterByUtilisateurProfessionnel($utilisateur)
                      ->useAbsenceEleveSaisieQuery()->filterByPlageTemps($dt_debut_toutes, $dt_fin_toutes)->endUse()
                      ->distinct()->find();


      foreach ($eleve_col as $eleve) {
		$eleve_a_afficher=is_responsable($eleve->getLogin(), $utilisateur->getLogin(), "", "yy");

		if($eleve_a_afficher) {
  ?>

        <br />
        <table style="border: 1px solid black;" cellpadding="5" cellspacing="5" title="Toutes les absences de <?php echo $eleve->getNom() . ' ' . $eleve->getPrenom(); ?>">
    <?php $creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime(); ?>
        <tr>
          <th style="border: 1px solid black; background-color: gray; min-width: 300px; max-width: 500px;"><?php echo $eleve->getNom() . ' ' . $eleve->getPrenom(); ?></th>
      <?php
        //afficher les créneaux
        foreach (EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $creneau) {
      ?>
          <th style="border: 1px solid black; background-color: gray;" title="De <?php echo preg_replace("/:[0-9]*$/","",$creneau->getHeuredebutDefiniePeriode())." à ".preg_replace("/:[0-9]*$/","",$creneau->getHeurefinDefiniePeriode()); ?>">
        <?php
          echo $creneau->getNomDefiniePeriode();
          /*
          echo "<pre>";
          print_r($creneau);
          echo "</pre>";
          */
        ?>
        </th>
      <?php
        }
      ?>
      </tr>
      <tr>
        <td colspan="<?php echo ($creneau_col->count() + 1); ?>"></td>
      </tr>
    <?php
        unset($date_actuelle);
        $date_actuelle = clone $dt_fin_toutes;
        $cpt_abs=0;
        while ($date_actuelle >= $dt_debut_toutes) {
          //on regarde si une des saisies du jour est affichable selon les critères (etab ouvert et manquement obligation)
          $affichage = false;
          foreach ($eleve->getAbsenceEleveSaisiesDuJour($date_actuelle) as $abs) {
            if (isAffichable($abs, $date_actuelle, $eleve)) {
              $affichage = true;
            }
          }
          //Il y'a au moins une absence affichable donc peut afficher
          if ($affichage) {
			if ($cpt_abs%2==0) {
				//$background_couleur="rgb(220, 220, 220);";
				$background_couleur="silver;";
			} else {
				//$background_couleur="rgb(210, 220, 230);";
				$background_couleur="lightblue;";
			}
			$cpt_abs++;

			$tmp_date_actuelle=$date_actuelle->format('d/m/Y');
			$tmp_tab=explode("/",$tmp_date_actuelle);
			if($tmp_tab[1]!=$mois_precedent) {
				echo '<tr class="white_hover"><td colspan="'.($creneau_col->count() + 1).'" style="text-align:center; background-color: gray;">'.ucfirst(strftime("%B %Y", mktime(13,59,0,$tmp_tab[1],$tmp_tab[0],$tmp_tab[2]))).'</td></tr>';
				$mois_precedent=$tmp_tab[1];
			}

    ?>
            <tr class='white_hover' style="background-color :<?php echo $background_couleur;?>">
              <td style="text-align:center;"><?php
              	//$tmp_date_actuelle=date("l", mktime(13,59,0,$tmp_tab[1],$tmp_tab[0],$tmp_tab[2]))." ".$tmp_date_actuelle;
              	$tmp_date_actuelle=strftime("%A", mktime(13,59,0,$tmp_tab[1],$tmp_tab[0],$tmp_tab[2]))." ".$tmp_date_actuelle;
              	echo ucfirst($tmp_date_actuelle);
              ?></td>
      <?php
            foreach ($creneau_col as $creneau) {
              $tab_heure = explode(":", $creneau->getHeuredebutDefiniePeriode());
              $date_actuelle_heure_creneau = clone $date_actuelle;
              $date_actuelle_heure_creneau->setTime($tab_heure[0], $tab_heure[1], $tab_heure[2]);
              $abs_col = $eleve->getAbsenceEleveSaisiesDecompteDemiJourneesDuCreneau($creneau, $date_actuelle);
              $abs_col->addCollection($eleve->getRetardsDuCreneau($creneau, $dt_date_absence_eleve));
              
              $tab_heure_fin = explode(":", $creneau->getHeurefinDefiniePeriode());
              $info_creneau_balise_title=$tab_heure[0]."h".$tab_heure[1]." à ".$tab_heure_fin[0]."h".$tab_heure_fin[1];
              
              if ($abs_col->isEmpty() || !EdtHelper::isEtablissementOuvert($date_actuelle_heure_creneau)) {
      ?>
                <td></td>
      <?php
              } else {
                $priorite = 5;
                foreach ($abs_col as $abs) {
                  if ($abs->getTraitee() && get_priorite($abs) < $priorite) {
                    $priorite = get_priorite($abs);
                  }
                }
                switch ($priorite) {
                  case 1:
      ?>
                    <td style="background:aqua;" title="Retard justifié : Le <?php echo $tmp_date_actuelle.' de '.$info_creneau_balise_title;?>">RJ</td>
      <?php
                    break;
                  case 2:
      ?>
                    <td style="background:blue;" title="Absence justifiée : Le <?php echo $tmp_date_actuelle.' de '.$info_creneau_balise_title;?>">J</td>
      <?php
                    break;
                  case 3:
      ?>
                    <td style="background:fuchsia;" title="Retard non justifié : Le <?php echo $tmp_date_actuelle.' de '.$info_creneau_balise_title;?>">RNJ</td>
      <?php
                    break;
                  case 4:
      ?>
                    <td style="background:red;" title="Absence non justifiée : Le <?php echo $tmp_date_actuelle.' de '.$info_creneau_balise_title;?>">NJ</td>
      <?php
                    break;
                  default:
      ?>
                    <td style="background:yellow;" title="Absence non encore traitée par le service de Vie Scolaire">...</td>
      <?php
                    break;
                }
              }
            }
      ?>
          </tr>
    <?php
          }
          //$date_actuelle = date_add($date_actuelle , $date_interval);

          date_date_set($date_actuelle, $date_actuelle->format('Y'), $date_actuelle->format('m'), $date_actuelle->format('d') - 1);
        }
    ?>
      </table>
  <?php
      }
		}
  ?>



      <p class="bold small">Impression faite le <?php echo date("d/m/Y - H:i"); ?>.</p>
    </div>
<?php
      require("../lib/footer.inc.php");
?>
