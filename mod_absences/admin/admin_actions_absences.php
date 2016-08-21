<?php
/*
 *
 * $Id$
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
die();
};
// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
die();
}
$msg = '';

function securite_texte($str)
 {
     $str = str_replace(array('<script','</script>','<?','?>','<?php'), array('','','','',''), $str);
     return $str;
 }

if (empty($_GET['action_sql']) and empty($_POST['action_sql'])) {$action_sql="";}
    else { if (isset($_GET['action_sql'])) {$action_sql=$_GET['action_sql'];} if (isset($_POST['action_sql'])) {$action_sql=$_POST['action_sql'];} }
if (empty($_GET['action']) and empty($_POST['action'])) {exit();}
    else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
if (empty($_GET['id_motif']) and empty($_POST['id_motif'])) { $id_motif="";}
    else { if (isset($_GET['id_motif'])) {$id_motif=$_GET['id_motif'];} if (isset($_POST['id_motif'])) {$id_motif=$_POST['id_motif'];} }
if (empty($_GET['nb_ajout']) and empty($_POST['nb_ajout'])) { $nb_ajout="1";}
    else { if (isset($_GET['nb_ajout'])) {$nb_ajout=$_GET['nb_ajout'];} if (isset($_POST['nb_ajout'])) {$nb_ajout=$_POST['nb_ajout'];} }
if (empty($_GET['init_absence_action']) and empty($_POST['init_absence_action'])) { $init_absence_action=""; }
    else { if (isset($_GET['init_absence_action'])) {$init_absence_action=$_GET['init_absence_action'];} if (isset($_POST['init_absence_action'])) {$init_absence_action=$_POST['init_absence_action'];} }
if (empty($_GET['def_absence_action']) and empty($_POST['def_absence_action'])) { $def_absence_action="";}
    else { if (isset($_GET['def_absence_action'])) {$def_absence_action=$_GET['def_absence_action'];} if (isset($_POST['def_absence_action'])) {$def_absence_action=$_POST['def_absence_action'];} }

$total = 0;
$verification[0] = 1;
$erreur = 0;
$remarque = 0;

if ($action_sql == "ajouter" or $action_sql == "modifier")
{
   while ($total < $nb_ajout)
      {
            // Vérifcation des variable
              $init_absence_action_ins = $_POST['init_absence_action'][$total];
              $def_absence_action_ins = securite_texte($_POST['def_absence_action'][$total]);

              if ($action_sql == "modifier") { $id_absence_action_ins = $_POST['id_motif'][$total]; }

            // Vérification des champs nom et prenom (si il ne sont pas vides ?)
            if($init_absence_action_ins != "" && $def_absence_action_ins != "")
            {
                 if($action_sql == "ajouter") { $test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM absences_actions WHERE init_absence_action = '".$init_absence_action_ins."'"),0); }
                 if($action_sql == "modifier") { $test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM absences_actions WHERE id_absence_action != '".$id_absence_action_ins."' AND init_absence_action = '".$init_absence_action_ins."'"),0); }
                 if ($test == "0")
                  {
                     if($action_sql == "ajouter")
                      {
                            // Requete d'insertion MYSQL
                             $requete = "INSERT INTO absences_actions (init_absence_action,def_absence_action) VALUES ('$init_absence_action_ins','$def_absence_action_ins')";
                      }
                     if($action_sql == "modifier")
                      {
                            // Requete de mise à jour MYSQL
                              $requete = "UPDATE absences_actions SET
                                                  init_absence_action = '$init_absence_action_ins',
                                                  def_absence_action = '$def_absence_action_ins'
                                                  WHERE id_absence_action = '".$id_absence_action_ins."' ";
                      }
                            // Execution de cette requete dans la base cartouche
                             mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$sql.'<br />'.mysqli_error($GLOBALS["mysqli"]));
                             $verification[$total] = 1;
                    } else {
                               // vérification = 2 - C'est initiale pour les motif existe déjas
                                 $verification[$total] = 2;
                                 $erreur = 1;
                            }
            } else {
                     // vérification = 3 - Tous les champs ne sont pas remplie
                     $verification[$total] = 3;
                     $erreur = 1;
                   }
      $total = $total + 1;
      }

      if($erreur == 0)
       {
          $action = "visualiser";
       } else {
                 $o = 0;
                 $n = 0;
                 while ($o < $nb_ajout)
                  {
                    if($verification[$o] != 1)
                     {
                        $init_absence_action_erreur[$n] = $init_absence_action[$o];
                        $def_absence_action_erreur[$n] = $def_absence_action[$o];
                        $verification_erreur[$n] = $verification[$o];
                        if ($action_sql == "modifier") { $id_definie_motif_erreur[$n] = $id_motif[$o]; }
                        $n = $n + 1;
                     }
                     $o = $o + 1;
                  }
                  $nb_ajout = $n;
                  if ($action_sql == "ajouter") { $action = "ajouter"; }
                if ($action_sql == "modifier") { $action = "modifier"; }
              }
}

if ($action_sql == "supprimer") {
      $test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM absences_actions, suivi_eleve_cpe
      WHERE  suivi_eleve_cpe.action_suivi_eleve_cpe = absences_actions.init_absence_action
      and id_absence_action ='".$id_motif."'"),0);
      if ($test == "0")
      {
         //Requete de suppresion MYSQL
            $requete = "DELETE FROM absences_actions WHERE id_absence_action ='$id_motif'";
         // Execution de cette requete
            mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
            $msg = "La suppresion a été effectuée avec succès.";
      } else {
          $msg = "Suppression impossible car une ou plusieurs suivi ont été enregistrées avec ce type d'action. Commencez par supprimer les suivis concernées";
      }

}

if ($action == "modifier")
 {
      $requete_modif_motif = 'SELECT * FROM absences_actions WHERE id_absence_action="'.$id_motif.'"';
      $resultat_modif_motif = mysqli_query($GLOBALS["mysqli"], $requete_modif_motif) or die('Erreur SQL !'.$requete_modif_motif.'<br />'.mysqli_error($GLOBALS["mysqli"]));
      $data_modif_motif = mysqli_fetch_array($resultat_modif_motif);
 }

if ($action == "reinit_lettres_pdf") {
	check_token();

	$f=fopen("../../sql/mod_absences_reinit.sql", "r");
	if(!$f) {
		$msg="Erreur lors de l'ouverture du fichier de réinitialisation les lettres PDF du module Absences 1.<br />\n";
	}
	else {
		$sql="TRUNCATE lettres_cadres;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$sql="TRUNCATE lettres_tcs;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$sql="TRUNCATE lettres_types;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);

		$nb_err=0;
		$nb_reg=0;
		while(!feof($f)) {
			$ligne=ensure_utf8(fgets($f, 4096));
			if(trim($ligne)!="") {
				$res=mysqli_query($GLOBALS["mysqli"], $ligne);
				if(!$res) {
					$nb_err++;
				}
				else {
					$nb_reg++;
				}
			}
		}
		fclose($f);
		if($nb_err==0) {
			if($nb_reg==0) {
				$msg="Pas d'erreur relevée, mais aucun enregistrement effectué???<br />\n";
			}
			else {
				$msg="$nb_reg enregistrement(s) effectué(s).<br />\n";
			}
		}
		else {
			if($nb_reg==0) {
				$msg=$nb_err." erreur(s) relevée(s) et aucun enregistrement effectué???<br />\n";
			}
			else {
				$msg=$nb_err." erreur(s) relevée(s) (???) et $nb_reg enregistrement(s) effectué(s).<br />\n";
			}
		}
	}
}

// header
$titre_page = "Gestion des actions de suivi";
require_once("../../lib/header.inc.php");


echo "<p class='bold'>";
if ($action=="modifier" OR $action=="ajouter") {
	echo "<a href=\"admin_actions_absences.php?action=visualiser\">";
} elseif ($action=="visualiser") {
	echo "<a href='index.php'>";
}
elseif($action == "reinit_lettres_pdf") {
	echo "<a href='index.php'>";
}
echo "<img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>";

//echo "\$action=$action<br />";

if ($action == "reinit_lettres_pdf") {
	echo "<p>Réinitialisation des paramètres des lettres PDF.</p>";
	require("../../lib/footer.inc.php");
	die();
}

?>
<?php if ($action === "visualiser") { ?>
<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
    <h2>Définition des actions de suivi</h2>
    <p><a href="admin_actions_absences.php?action=ajouter"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter une ou des action(s)</a></p>
    <br/>
    <table cellpadding="0" cellspacing="1" class="tab_table">
      <tr>
        <td class="tab_th">Code</td>
        <td class="tab_th">Description</td>
        <td class="tab_th" style="width: 25px;"></td>
        <td class="tab_th" style="width: 25px;"></td>
      </tr>
    <?php
    $requete_motif = 'SELECT * FROM absences_actions WHERE init_absence_action !="DI" AND init_absence_action !="IN" ORDER BY init_absence_action ASC';
    $execution_motif = mysqli_query($GLOBALS["mysqli"], $requete_motif) or die('Erreur SQL !'.$requete_motif.'<br>'.mysqli_error($GLOBALS["mysqli"]));
    $i = '1';
    while ( $data_motif = mysqli_fetch_array( $execution_motif ) ) {
       if ($i === '1') { $couleur_cellule = 'couleur_ligne_1'; $i = '2'; } else { $couleur_cellule = 'couleur_ligne_2'; $i = '1'; } ?>
        <tr class="<?php echo $couleur_cellule; ?>">
          <td><?php echo $data_motif['init_absence_action']; ?></td>
          <td><?php echo $data_motif['def_absence_action']; ?></td>
          <td><a href="admin_actions_absences.php?action=modifier&amp;id_motif=<?php echo $data_motif['id_absence_action']; ?>"><img src="../../images/icons/configure.png" title="Modifier" border="0" alt="" /></a></td>
          <td><?php if ( $data_motif['init_absence_action'] != 'A' and $data_motif['init_absence_action'] != 'LP' and $data_motif['init_absence_action'] != 'RC' and $data_motif['init_absence_action'] != 'RD' and $data_motif['init_absence_action'] != 'CE' ) { ?><a href="admin_actions_absences.php?action=visualiser&amp;action_sql=supprimer&amp;id_motif=<?php echo $data_motif['id_absence_action']; ?>" onClick="return confirm('Etes-vous sur de vouloir supprimer cette action ?')"><img src="../images/x2.png" width="22" height="22" title="Supprimer" border="0" alt="" /></a><?php } ?></td>
        </tr>
     <?php } ?>
    </table>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>

<?php if ($action === "ajouter" or $action === "modifier") { ?>
<div style="text-align:center">
  <?php if ($action === "ajouter") { ?>
<?php /* div de centrage du tableau pour ie5 */ ?>

	<h2>Ajout d'actions de suivi</h2>
    <form method="post" action="admin_actions_absences.php?action=ajouter" name="form1" id="form1">
     <fieldset class="fieldset_efface">
      <table cellpadding="2" cellspacing="2" class="tab_table">
        <tr>
          <th class="tab_th">Nombre d'actions à ajouter</th>
        </tr>
        <tr style="text-align: right;">
          <td class="couleur_ligne_1"><input name="nb_ajout" type="text" size="5" maxlength="5" value="<?php if(isset($nb_ajout)) { echo $nb_ajout; } else { ?>1<?php } ?>" class="input_sans_bord" />&nbsp;&nbsp;&nbsp;<input type="submit" name="Submit2" value="Mettre à jour" /></td>
        </tr>
      </table>
     </fieldset>
    </form>
  <?php } ?>
    <form action="admin_actions_absences.php?action=visualiser&amp;action_sql=<?php if($action=="ajouter") { ?>ajouter<?php } if($action=="modifier") { ?>modifier<?php } ?>" method="post" name="form2" id="form2">
     <fieldset class="fieldset_efface">
      <table cellpadding="2" cellspacing="2" class="tab_table">
        <tr>
          <th class="tab_th">Code</th>
          <th colspan="2" class="tab_th">Description</th>
        </tr>
        <?php
        $i = '1';
        $nb = 0;
        while($nb < $nb_ajout) {
	       if ($i === '1') { $couleur_cellule = 'couleur_ligne_1'; $i = '2'; } else { $couleur_cellule = 'couleur_ligne_2'; $i = '1'; } ?>
        <?php if (isset($verification_erreur[$nb]) and $verification_erreur[$nb] != 1) { ?>
         <tr>
          <td class="centre"><img src="../images/attention.png" width="28" height="28" alt="" /></td>
          <td colspan="2" class="erreur_rouge_jaune"><b>- Erreur -<br />
          <?php if ($verification_erreur[$nb] === 2) { ?>Le code saisi existe déjà<?php } ?>
          <?php if ($verification_erreur[$nb] === 3) { ?>Tous les champs ne sont pas remplis<?php } ?>
          </b><br /></td>
         </tr>
        <?php } ?>
        <tr class="<?php echo $couleur_cellule; ?>">
          <td>
           <?php
           if($action==="modifier") {
               $test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM suivi_eleve_cpe WHERE suivi_eleve_cpe.action_suivi_eleve_cpe = '".$data_modif_motif['init_absence_action']."'"),0);
               if ($test != "0") {
                   ?><input name="init_absence_action[<?php echo $nb; ?>]" type="hidden" id="init_absence_action" size="2" maxlength="2" value="<?php if($action=="modifier") { echo $data_modif_motif['init_absence_action']; } elseif (isset($init_absence_action_erreur[$nb])) { echo $init_absence_action_erreur[$nb]; } ?>" /><?php if($action=="modifier") { echo $data_modif_motif['init_absence_action']; } elseif (isset($init_absence_action_erreur[$nb])) { echo $init_absence_action_erreur[$nb]; } ?><?php
               } else {
                   ?><input name="init_absence_action[<?php echo $nb; ?>]" type="text" id="init_absence_action" size="2" maxlength="2" value="<?php if($action=="modifier") { echo $data_modif_motif['init_absence_action']; } elseif (isset($init_absence_action_erreur[$nb])) { echo $init_absence_action_erreur[$nb]; } ?>" class="input_sans_bord" /><?php
               }
           } else {
               ?><input name="init_absence_action[<?php echo $nb; ?>]" type="text" id="init_absence_action" size="2" maxlength="2" value="<?php if($action=="modifier") { echo $data_modif_motif['init_absence_action']; } elseif (isset($init_absence_action_erreur[$nb])) { echo $init_absence_action_erreur[$nb]; } ?>" class="input_sans_bord" /><?php
           }

            ?>
           </td>
           <td colspan="2">
              <input name="def_absence_action[<?php echo $nb; ?>]" type="text" id="def_absence_action" size="40" maxlength="200" value="<?php if($action=="modifier") { echo $data_modif_motif['def_absence_action']; } elseif (isset($def_absence_action_erreur[$nb])) { echo $def_absence_action_erreur[$nb]; } else { ?><?php } ?>" class="input_sans_bord" />
           </td>
        </tr>
            <?php if($action==='modifier') { ?>
              <input type="hidden" name="id_motif[<?php echo $nb; ?>]" value="<?php if (isset($id_definie_motif_erreur[$nb])) { echo $id_definie_motif_erreur[$nb]; } else { echo $id_motif; } ?>" />
            <?php } ?>
        <?php $nb = $nb + 1; } ?>
      </table>
     <input type="hidden" name="nb_ajout" value="<?php echo $nb_ajout; ?>" />
     <br/>
     <input type="submit" name="Submit" value="Enregistrer" />
     </fieldset>
    </form>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php
}

require("../../lib/footer.inc.php");
 ?>
