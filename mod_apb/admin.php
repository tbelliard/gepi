<?php
/*
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard
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
require_once("../lib/initialisations.inc.php");
// Resume session
$resultat_session = $session_gepi->security_check();

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}
$msg = '';
if (isset($_POST['activer'])) {
    if (!saveSetting("active_mod_apb", $_POST['activer'])) {
      $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
    } else {
      $msg = "Le statut d'activation du module a bien été enregistré.";
    }
}

if (isset($_POST['posted_selection'])) {
  $errors = false;
  $req_classes = mysql_query('SELECT id,classe,nom_complet,apb_niveau FROM classes ORDER BY classe');
  while ($classe = mysql_fetch_object($req_classes)) {
    $new_value = (isset($_POST['classe_'.$classe->id]) && $_POST['classe_'.$classe->id] == '1') ? 'terminale' : '';
    if ($classe->apb_niveau != $new_value) {
      $rec_classe = mysql_query("UPDATE classes SET apb_niveau = '".$new_value."' WHERE id = '".$classe->id."'");
      if (!$rec_classe) {
        $errors = true;
        $msg .= "Erreur lors de l'enregistrement de la nouvelle valeur pour la classe ".$classe->classe.".";
      }
    }
  }
  if (!$errors) {
    $msg .= "Les données ont été enregistrées avec succès.";
  }
}

// header
$titre_page = "Gestion de l'export APB";
require_once("../lib/header.inc");
?>

<p class=bold><a href="../accueil_modules.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<h2>Configuration générale</h2>
<i>La désactivation de ce module ne supprime pas les paramétrages déjà réalisés.</i>
<br />
<form action="admin.php" name="form1" method="post">

<p>
<input type="radio" name="activer" id='activer_y' value="y" <?php if (getSettingValue("active_mod_apb")=='y') echo " checked"; ?> />&nbsp;<label for='activer_y' style='cursor: pointer;'>Activer l'export "Admissions Post-Bac"</label><br />
<input type="radio" name="activer" id='activer_n' value="n" <?php if (getSettingValue("active_mod_apb")=='n') echo " checked"; ?> />&nbsp;<label for='activer_n' style='cursor: pointer;'>Désactiver l'export "Admissions Post-Bac"</label>
</p>
<input type="submit" value="Enregistrer le statut" style="font-variant: small-caps;"/>
</form>
<br/>
<h2>Sélection des classes de terminale</h2>
<p>Le tableau ci-dessous vous permet de préciser quelles classes correspondent au niveau 'Terminale'. Seuls les élèves des classes sélectionnées ici seront intégrés à l'export vers le système APB.</p>

<form action="admin.php" name="form2" method="post">
<input type="hidden" name="posted_selection" value="1" />
<p><input type="submit" value="Enregistrer la sélection" style="font-variant: small-caps;"/></p>
<table style='border: 1px solid black; border-collapse: true;'>
  <tr>
    <td>Code</td>
    <td>Nom</td>
    <td>Terminale ?</td>
  </tr>
  
<?php
$req_classes = mysql_query('SELECT id,classe,nom_complet,apb_niveau FROM classes ORDER BY classe');
while($classe = mysql_fetch_object($req_classes)) {
  echo '<tr><td>'.$classe->classe.'</td>';
  echo '<td>'.$classe->nom_complet.'</td>';
  echo '<td style="text-align: center;"><input type="checkbox" name="classe_'.$classe->id.'" value="1"';
  if ($classe->apb_niveau == 'terminale') echo ' checked';
  echo ' />';
  echo '</td></tr>';
}
?>
</table>
<p><input type="submit" value="Enregistrer la sélection" style="font-variant: small-caps;"/></p>

</form>

<?php
	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
?>
