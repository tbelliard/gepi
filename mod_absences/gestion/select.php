<?php
/*
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

// Reçoit en argument : $type
// Renvoie vers un script (en fonction de la valeur de $type) les infos $classe_choix et eleve_absent[] .

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

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

//**************** EN-TETE *****************
$titre_page = "Sélection d'un ou plusieurs élèves";
require_once("../../lib/header.inc.php");
//**************** FIN EN-TETE *****************

if (empty($_GET['type'])) {$type = ""; } else {$type = $_GET['type']; }
if (empty($_GET['classe_choix']) and empty($_POST['classe_choix'])) { $classe_choix="tous"; }
    else { if (isset($_GET['classe_choix'])) { $classe_choix=$_GET['classe_choix']; } if (isset($_POST['classe_choix'])) { $classe_choix=$_POST['classe_choix']; } }

if ($type == "A") {$page = "ajout_abs"; }
if ($type == "D") {$page = "ajout_dip"; }
if ($type == "I") {$page = "ajout_inf"; }
if ($type == "R") {$page = "ajout_ret"; }
if ($type == "") {exit(); }

// On ajoute un paramètre sur les élèves de ce CPE en particulier
$sql_eleves_cpe = "SELECT e_login FROM j_eleves_cpe WHERE cpe_login = '".$_SESSION['login']."'";
$query_eleves_cpe = mysqli_query($GLOBALS["mysqli"], $sql_eleves_cpe) OR die('Erreur SQL ! <br />' . $sql_eleves_cpe . ' <br /> ' . mysqli_error($GLOBALS["mysqli"]));
$test_cpe = array();

$test_nbre_eleves_cpe = mysqli_num_rows($query_eleves_cpe);
while($test_eleves_cpe = mysqli_fetch_array($query_eleves_cpe)){
	$test_cpe[] = $test_eleves_cpe['e_login'];
}
// requete liste classe en fonction du cpe responsable didier
if ($test_nbre_eleves_cpe === 0){

  
   	$requete_liste_classe = "SELECT id, classe, nom_complet FROM classes ORDER BY nom_complet ASC";
   
}
else
{
	$requete_liste_classe = "SELECT  id, classe, nom_complet FROM classes c, j_eleves_cpe jecp ,j_eleves_classes jec
                                 WHERE (jecp.cpe_login = '".$_SESSION['login']."' AND jecp.e_login=jec.login AND jec.id_classe=c.id )
								 GROUP BY id ORDER BY nom_complet ASC";
}
   
if ($classe_choix == "tous"){
	// On récupère tous les élèves qu'on range avec le nom de leur classe dans l'ordre alpha
	$requete_liste_eleve = "SELECT e.login, e.nom, e.prenom, c.classe
									FROM eleves e, j_eleves_classes jec, classes c
									WHERE e.login = jec.login
									AND jec.id_classe = c.id
									GROUP BY e.login, e.nom, e.prenom
									ORDER BY nom, prenom ASC";
}else{
    settype($classe_choix,"integer");
    $requete_liste_eleve = "SELECT eleves.login, eleves.nom, eleves.prenom, j_eleves_classes.login, j_eleves_classes.id_classe, j_eleves_classes.periode, classes.id, classes.classe, classes.nom_complet FROM eleves, j_eleves_classes, classes WHERE eleves.login=j_eleves_classes.login AND j_eleves_classes.id_classe=classes.id AND classes.id='".$classe_choix."' GROUP BY eleves.login, eleves.nom, eleves.prenom ORDER BY nom, prenom ASC";
}

$date_ce_jour = date('d/m/Y');

// On paramètre le retour pour le statut 'autre'
if ($_SESSION["statut"] == 'autre') {
	$retour = '../../accueil.php';
}else{
	$retour = './gestion_absences.php?type='.$type;
}
?>
<p class="bold"><a href='<?php echo $retour; ?>'><img src="../../images/icons/back.png" alt="Retour" title="Retour" class="back_link" /> Retour</a>
</p>
<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
  <table class="entete_tableau_selection" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td class="titre_tableau_selection" colspan="2"><b>
      <?php if($type == "A") { ?>Absences d'un ou plusieurs &eacute;l&egrave;ve(s)<?php }
       if($type == "D") { ?>Dispences d'un &eacute;l&egrave;ve(s)<?php }
       if($type == "R") { ?>Retards d'un ou plusieurs &eacute;l&egrave;ve(s)<?php }
       if($type == "I") { ?>Infirmerie d'un &eacute;l&egrave;ve(s)<?php } ?>
       </b></td>
    </tr>
    <tr>
      <td class="td_tableau_selection">
        <form name="form1" method="post" action="select.php?type=<?php echo $type; ?>">
         <fieldset class="fieldset_efface" style="width: 450px;">
            Sélection de la classe :
            <select name="classe_choix" onchange="javascript:document.form1.submit();"> <?php /* correction pour ie didier  */ ?>
            	<option value="tous" selected="selected" onclick="javascript:document.form1.submit()">Toutes les classes</option>
			<?php
			$resultat_liste_classe = mysqli_query($GLOBALS["mysqli"], $requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
			while($data_liste_classe = mysqli_fetch_array($resultat_liste_classe))
			{
            	if ($classe_choix==$data_liste_classe['id']) {
					$selected = 'selected="selected"';
				} else {
					$selected = "";
				}?>
            	<option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?> onclick="javascript:document.form1.submit()"><?php echo mb_substr($data_liste_classe['nom_complet'], 0, 40)." (".$data_liste_classe['classe'].")"; ?></option>
    		<?php
			} ?>
            </select>
            <noscript>
            <p><input type="submit" name="submit3" value="Ok" /></p>
            </noscript>
          </fieldset>
         </form>
      <?php if($type == "A" OR $type == "R") { ?>
         <span class="norme_absence_bleu">* Pour sélectionner plusieurs élèves : touche CTRL enfoncée puis sélectionner les différents élèves en cliquant dessus.</span><br /><br />
      <?php } ?>
      <?php if($type == "A") { ?>
         <span class="norme_absence_bleu">* Si aucun élève n'est sélectionné, la classe entière sera sélectionnée.</span><br /><br />
      <?php } ?>
      </td>
      <td class="td_tableau_selection">
	<form method="post" action="<?php echo $page; ?>.php?action=ajouter&amp;type=<?php echo $type; ?>" name="form2">
            <p>Sélection :<br />
			<?php
				//echo "$requete_liste_eleve<br />";
			?>
            <select name="eleve_absent[]" size="10" <?php if ($type == "D" or $type == "I") {} else {?>multiple="multiple"<?php } ?> style="width: 350px;">
            <?php
			$resultat_liste_eleve = mysqli_query($GLOBALS["mysqli"], $requete_liste_eleve) or die('Erreur SQL !'.$requete_liste_eleve.'<br />'.mysqli_error($GLOBALS["mysqli"]));
            while($data_liste_eleve = mysqli_fetch_array($resultat_liste_eleve))
			{
				//if (in_array($data_liste_eleve['login'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {
				if (in_array_i($data_liste_eleve['login'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {
			?>

                <option value="<?php echo $data_liste_eleve['login']; ?>"><?php echo strtoupper($data_liste_eleve['nom'])." ".ucfirst($data_liste_eleve['prenom']); ?>&nbsp;(<?php echo $data_liste_eleve['classe']; ?>)</option>
            <?php
            	}
			} ?>
            </select>
         <input type="hidden" name="classe_choix" value="<?php echo $classe_choix; ?>" /></p>
         <p><input type="submit" name="submit" value="Valider votre sélection" /></p>
        </form>
      </td>
    </tr>
  </table>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>

<?php require("../../lib/footer.inc.php"); ?>

