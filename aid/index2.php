<?php
/*
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
//Initialisation des variables
$indice_aid = isset($_GET["indice_aid"]) ? $_GET["indice_aid"] : (isset($_POST["indice_aid"]) ? $_POST["indice_aid"] : NULL);
$order_by = isset($_GET["order_by"]) ? $_GET["order_by"] : NULL;

// Vérification du niveau de gestion des AIDs
if (NiveauGestionAid($_SESSION["login"],$indice_aid) <= 0) {
    header("Location: ../logout.php?auto=1");
    die();
}


if ($indice_aid =='') {
    header("Location: index.php");
    die();
}
$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
$nom_aid = @old_mysql_result($call_data, 0, "nom");
$activer_outils_comp = @old_mysql_result($call_data, 0, "outils_complementaires");

if ((NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10) and (isset($_POST["is_posted"]))) {
	check_token();

    // Enregistrement des données
    // On va chercher les aid déjà existantes
    $calldata = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid WHERE indice_aid='$indice_aid'");
    $nombreligne = mysqli_num_rows($calldata);
    $i = 0;
    $msg_inter = "";
    while ($i < $nombreligne){
        $aid_id = @old_mysql_result($calldata, $i, "id");
        // Enregistrement de fiche publique
        if (isset($_POST["fiche_publique_".$aid_id])) {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set fiche_publique='y' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        } else {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set fiche_publique='n' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        };
        if (!$register)
			    $msg_inter .= "Erreur lors de l'enregistrement de la donnée fiche_publique de l'aid $aid_id <br />\n";
        // Enregistrement de eleve_peut_modifier
        if (isset($_POST["eleve_peut_modifier_".$aid_id])) {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set eleve_peut_modifier='y' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        } else {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set eleve_peut_modifier='n' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        };
        if (!$register)
			    $msg_inter .= "Erreur lors de l'enregistrement de la donnée eleve_peut_modifier de l'aid $aid_id <br />\n";
         // Enregistrement de prof_peut_modifier
        if (isset($_POST["prof_peut_modifier_".$aid_id])) {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set prof_peut_modifier='y' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        } else {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set prof_peut_modifier='n' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        };
        if (!$register)
			    $msg_inter .= "Erreur lors de l'enregistrement de la donnée prof_peut_modifier de l'aid $aid_id <br />\n";
        // Enregistrement de cpe_peut_modifier
        if (isset($_POST["cpe_peut_modifier_".$aid_id])) {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set cpe_peut_modifier='y' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        } else {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set cpe_peut_modifier='n' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        };
        if (!$register)
			    $msg_inter .= "Erreur lors de l'enregistrement de la donnée cpe_peut_modifier de l'aid $aid_id <br />\n";

        // Enregistrement de affiche_adresse1
        if (isset($_POST["affiche_adresse1_".$aid_id])) {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set affiche_adresse1='y' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        } else {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set affiche_adresse1='n' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        };
        if (!$register)
			    $msg_inter .= "Erreur lors de l'enregistrement de la donnée affiche_adresse1 de l'aid $aid_id <br />\n";
        // Enregistrement de en_construction
        if (isset($_POST["en_construction_".$aid_id])) {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set en_construction='y' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        } else {
            $register = mysqli_query($GLOBALS["mysqli"], "update aid set en_construction='n' where indice_aid='".$indice_aid."' and id = '".$aid_id."'");
        };
        if (!$register)
			    $msg_inter .= "Erreur lors de l'enregistrement de la donnée en_construction de l'aid $aid_id <br />\n";
        $i++;
    }
    if ($msg_inter == "") {
        $msg = "Les modifications ont été enregistrées.";
    } else {
        $msg = $msg_inter;
    }
}


// On va chercher les aid déjà existantes, et on les affiche.
if (!isset($order_by)) {$order_by = "numero,nom";}
$calldata = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid WHERE indice_aid='$indice_aid' ORDER BY $order_by");
$nombreligne = mysqli_num_rows($calldata);



//**************** EN-TETE *********************
$titre_page = "Gestion des ".$nom_aid;
// if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
// if (!suivi_ariane($_SESSION['chemin_retour'],$titre_page))
$fil = "";
if ($indice_aid != NULL) $fil = $_SERVER['PHP_SELF']."?indice_aid=".$indice_aid;
if (!suivi_ariane($fil ,$titre_page))
		echo "erreur lors de la création du fil d'ariane";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();
?>
<p class="bold">
<?php if (NiveauGestionAid($_SESSION["login"],$indice_aid) >= 5) { ?>
	<!-- | -->
	<a href="add_aid.php?action=add_aid&amp;mode=unique&amp;indice_aid=<?php echo $indice_aid; ?>">
		Ajouter un(e) <?php echo $nom_aid; ?>
	</a>
	|
	<a href="add_aid.php?action=add_aid&amp;mode=multiple&amp;indice_aid=<?php echo $indice_aid; ?>">
		Ajouter des <?php echo $nom_aid; ?> à la chaîne
	</a>
<?php }
if (NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10) { ?>
	|
	<a href="export_csv_aid.php?indice_aid=<?php echo $indice_aid; ?>">
		Importation de données depuis un fichier vers GEPI
	</a>
<?php } ?>
</p>
<?php if ((NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10) and ($activer_outils_comp == "y")) { ?>
<p class="medium">
	Les droits d'accès aux différents champs sont configurables pour l'ensemble des AID dans la page 
	<strong><em>Gestion des AID -> <a href='./config_aid_fiches_projet.php'>Configurer les fiches projet</a></em></strong>
	.
</p>
<?php } ?>
<p class="center">
	<input type="submit" name="Valider" />
</p>
<?php if ((NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10) and ($activer_outils_comp == "y")) { ?>
<form action="index2.php" name="form1" method="post">
<?php } ?>
	<table class='boireaus'>
		<tr>
			<th>
				<a href='index2.php?order_by=numero,nom&amp;indice_aid=$indice_aid'>N°</a>
			</th>
			<th>
				<a href='index2.php?order_by=nom&amp;indice_aid=$indice_aid'>Nom</a>
			</th>
<?php
// En tete de la colonne "Ajouter, supprimer des professeurs"
if (NiveauGestionAid($_SESSION["login"],$indice_aid) >= 5)
  if(!((getSettingValue("num_aid_trombinoscopes")==$indice_aid) and (getSettingValue("active_module_trombinoscopes")=='y')))
    echo "<th>&nbsp;</th>";
// En tete de la colonne "Ajouter, supprimer des élèves"
?>
			<th>&nbsp;</th>
<?php
  // En tete de la colonne "Ajouter, supprimer des gestionnairess"
if (NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10)
  if (getSettingValue("active_mod_gest_aid")=="y")
?>
			<th>&nbsp;</th>
<?php
// colonne publier la fiche
if ((NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10) and ($activer_outils_comp == "y")) {
?>
			<th class="small" style="font-weight: normal;">
				La fiche est visible sur la 
				<a href="javascript:centrerpopup('../public/index_fiches.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')">
					partie publique
				</a>
				<br />
				<a href="javascript:CocheColonne(1);changement();">
					<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' />
				</a>
				/
				<a href="javascript:DecocheColonne(1);changement();">
					<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' />
				</a>
			</th>
			<th class="small" style="font-weight: normal;">
				Les élèves reponsables peuvent modifier la fiche (*)<br />
				<a href="javascript:CocheColonne(2);changement();">
					<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' />
				</a>
				/
				<a href="javascript:DecocheColonne(2);changement();">
					<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' />
				</a>
			</th>
			<th class="small" style="font-weight: normal;">
				Les professeurs reponsables peuvent modifier la fiche (*)<br />
				<a href="javascript:CocheColonne(3);changement();">
					<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' />
				</a>
				/
				<a href="javascript:DecocheColonne(3);changement();">
					<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' />
				</a>
			</th>
			<th class="small" style="font-weight: normal;">
				Les CPE peuvent modifier la fiche (*)<br />
				<a href="javascript:CocheColonne(4);changement();">
					<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' />
				</a>
				/
				<a href="javascript:DecocheColonne(4);changement();">
					<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' />
				</a>
			</th>
			<th class="small" style="font-weight: normal;">
				Le lien "adresse publique" est visible sur la partie publique<br />
				<a href="javascript:CocheColonne(5);changement();">
					<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' />
				</a>
				/
				<a href="javascript:DecocheColonne(5);changement();">
					<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' />
				</a>
			</th>
			<th class="small" style="font-weight: normal;">
				Le lien "adresse publique" est accompagné d'une message "En construction"<br />
				<a href="javascript:CocheColonne(6);changement();">
					<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' />
				</a>
				/
				<a href="javascript:DecocheColonne(6);changement();">
					<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' />
				</a>
			</th>
<?php
}
// Colonne "supprimer
if (NiveauGestionAid($_SESSION["login"],$indice_aid) >= 5) {
?>
			<th>&nbsp;</th>
<?php } ?>
		</tr>
<?php

$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
$i = 0;
$alt=1;
while ($i < $nombreligne){
    $aid_nom = @old_mysql_result($calldata, $i, "nom");
    $aid_num = @old_mysql_result($calldata, $i, "numero");
    $eleve_peut_modifier = @old_mysql_result($calldata, $i, "eleve_peut_modifier");
    $prof_peut_modifier = @old_mysql_result($calldata, $i, "prof_peut_modifier");
    $cpe_peut_modifier = @old_mysql_result($calldata, $i, "cpe_peut_modifier");
    $fiche_publique = @old_mysql_result($calldata, $i, "fiche_publique");
    $affiche_adresse1 = @old_mysql_result($calldata, $i, "affiche_adresse1");
    $en_construction = @old_mysql_result($calldata, $i, "en_construction");
    if ($aid_num =='') {$aid_num='&nbsp;';}
    $aid_id = @old_mysql_result($calldata, $i, "id");
    $alt=$alt*(-1);
    // Première colonne du numéro de l'AID
?>
		<tr class='lig<?php echo $alt; ?>'>
<?php if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 1) { ?>
			<td class='medium'><strong><?php echo $aid_num; ?></strong></td>
<?php
	}
    // Colonne du nom de l'AID
    if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 10) {
		if ($activer_outils_comp == "y") {
?>
			<td class='medium'>
				<a href='modif_fiches.php?aid_id=<?php echo $aid_id; ?>&amp;indice_aid=<?php echo $indice_aid; ?>&amp;action=modif&amp;retour=index2.php'>
					<strong><?php echo $aid_nom; ?></strong>
				</a>
			</td>
<?php	} else { ?>
			<td class='medium'>
				<a href='add_aid.php?action=modif_aid&amp;aid_id=<?php echo $aid_id; ?>&amp;indice_aid=<?php echo $indice_aid; ?>'>
					<strong><?php echo $aid_nom; ?></strong>
				</a>
			</td>
<?php 	}
	} else if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 5) { ?>
			<td class='medium'>
				<a href='add_aid.php?action=modif_aid&amp;aid_id=<?php echo $aid_id; ?>&amp;indice_aid=<?php echo $indice_aid; ?>'>
					<strong><?php echo $aid_nom; ?></strong>
				</a>
			</td>
<?php } else if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 1) { ?>
			<td class='medium'>
				<strong>><?php echo $aid_nom; ?></strong>>
			</td>
<?php 
	}
    // colonne "Ajouter, supprimer des professeurs"
    if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 5) {
		if (!((getSettingValue("num_aid_trombinoscopes")==$indice_aid) and (getSettingValue("active_module_trombinoscopes")=='y'))) {
?>
			<td class='medium'>
				<a href='modify_aid.php?flag=prof&amp;aid_id=<?php echo $aid_id; ?>&amp;indice_aid=<?php echo $indice_aid; ?>'>
					Ajouter, supprimer des professeurs
				</a>
			</td>
<?php
		} 
	} 
    // colonne "Ajouter, supprimer des élèves"
    if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 1) {
?>
			<td class='medium'>
				<a href='modify_aid.php?flag=eleve&amp;aid_id=<?php echo $aid_id; ?>&amp;indice_aid=<?php echo $indice_aid; ?>'>
					Ajouter, supprimer des élèves
				</a>
			</td>
 <?php } 
    // colonne "Ajouter, supprimer des gestionnaires"
	if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 10) {
	   if (getSettingValue("active_mod_gest_aid")=="y") {
?>
			<td class='medium'>
				<a href='modify_aid.php?flag=prof_gest&amp;aid_id=<?php echo $aid_id; ?>&amp;indice_aid=<?php echo $indice_aid; ?>'>
					Ajouter, supprimer des gestionnaires
				</a>
			</td>
<?php
		} 
	} 
	if ((NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 10) and ($activer_outils_comp == "y")) {
        // La fiche est-elle publique ?
?>
			<td class="center">
				<input type="checkbox" 
					   name="fiche_publique_<?php echo $aid_id; ?>" 
					   value="y" 
					   id="case_1_<?php echo $i; ?>"
<?php					if ($fiche_publique == "y") {echo " checked = 'checked' ";} ?>
					   />
			</td>
 <?php  // Les élèves peuvent-ils modifier la fiche ? ?>
			<td class="center">
				<input type="checkbox" 
					   name="eleve_peut_modifier_<?php echo $aid_id; ?>" 
					   value="y" 
					   id="case_2_<?php echo $i; ?>"
<?php					if ($eleve_peut_modifier == "y") {echo " checked = 'checked' ";} ?>
					   />
			</td>
<?php	// Les profs peuvent-ils modifier la fiche ? ?>
			<td class="center">
				<input type="checkbox" 
					   name="prof_peut_modifier_<?php echo $aid_id; ?>" 
					   value="y" 
					   id="case_3_<?php echo $i; ?>"
<?php					if ($prof_peut_modifier == "y") {echo " checked = 'checked' ";} ?>
					   />
			</td>
<?php	// Les CPE peuvent-ils modifier la fiche ? ?>
			<td class="center">
				<input type="checkbox" 
					   name="cpe_peut_modifier_<?php echo $aid_id; ?>"
					   value="y" 
					   id="case_4_<?php echo $i; ?>"
 <?php					if ($cpe_peut_modifier == "y") {echo " checked = 'checked' ";} ?>
					   />
			</td>
<?php	// Le lien public est-il visible sur la partie publique ? ?>
			<td class="center">
				<input type="checkbox" 
					   name="affiche_adresse1_<?php echo $aid_id; ?>" 
					   value="y" 
					   id="case_5_<?php echo $i; ?>"
 <?php					if ($affiche_adresse1 == "y") {echo " checked = 'checked' ";} ?>
					   />
			</td>
<?php	// Avertissement "en construction" ?>
			<td class="center">
				<input type="checkbox" 
					   name="en_construction_<?php echo $aid_id; ?>" 
					   value="y" 
					   id="case_6_<?php echo $i; ?>"
<?php					if ($en_construction == "y") {echo " checked = 'checked' ";} ?>
					   />
			</td>
<?php
    }
    // colonne "Supprimer"
    if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) >= 5) 
?>
			<td class='medium'>
				<a href='../lib/confirm_query.php?liste_cible=<?php echo $aid_id; ?>&amp;liste_cible3=<?php echo $indice_aid ?>&amp;action=del_aid<?php echo add_token_in_url() ?>'>
					supprimer
				</a>
			</td>
		</tr>
<?php
$i++;
}

?>
	</table>
<?php
if ((NiveauGestionAid($_SESSION["login"],$indice_aid) >= 10) and ($activer_outils_comp == "y")) {
?>
	<p style="padding-bottom:1em;">
		(*) Uniquement si l'administrateur a ouvert cette possibilité pour le projet concerné.
	</p>
	<div class="center" id='fixe'>
		<p style="font-weight: bolder;  padding: .5em; ">
			<input type="submit" name="Valider" />
		</p>
	</div>
	<p>
		<input type="hidden" name="indice_aid" value="<?php echo $indice_aid; ?>" />
		<input type="hidden" name="is_posted" value="y" />
	</p>
	<?php echo add_token_field(); ?>
</form>
<script type='text/javascript'>
  function CocheColonne(i) {
	 for (var ki=0;ki<<?php echo $nombreligne; ?>;ki++) {
		if(document.getElementById('case_'+i+'_'+ki)){
			document.getElementById('case_'+i+'_'+ki).checked = true;
		}
	 }
  }
  function DecocheColonne(i) {
	 for (var ki=0;ki<<?php echo $nombreligne; ?>;ki++) {
		if(document.getElementById('case_'+i+'_'+ki)){
			document.getElementById('case_'+i+'_'+ki).checked = false;
		}
	 }
  }
</script>
<?php
}
require("../lib/footer.inc.php");