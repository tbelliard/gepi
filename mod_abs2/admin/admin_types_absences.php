<?php
/*
 *
 *
 * Copyright 2010-2011 Josselin Jacquard
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

$niveau_arbo = 2;
// Initialisations files
include("../../lib/initialisationsPropel.inc.php");
require_once("../../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
}

// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

if (empty($_GET['action']) and empty($_POST['action'])) { $action="";}
    else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
if (empty($_GET['id']) and empty($_POST['id'])) { $id="";}
    else { if (isset($_GET['id'])) {$id=$_GET['id'];} if (isset($_POST['id'])) {$id=$_POST['id'];} }
if (empty($_GET['statut_id']) and empty($_POST['statut_id'])) { $statut_id="";}
    else { if (isset($_GET['statut_id'])) {$statut_id=$_GET['statut_id'];} if (isset($_POST['statut_id'])) {$statut_id=$_POST['statut_id'];} }
if (empty($_GET['nom']) and empty($_POST['nom'])) { $nom="";}
    else { if (isset($_GET['nom'])) {$nom=$_GET['nom'];} if (isset($_POST['nom'])) {$nom=$_POST['nom'];} }
if (empty($_GET['commentaire']) and empty($_POST['commentaire'])) { $commentaire="";}
    else { if (isset($_GET['commentaire'])) {$commentaire=$_GET['commentaire'];} if (isset($_POST['commentaire'])) {$commentaire=$_POST['commentaire'];} }
if (empty($_GET['justification_exigible']) and empty($_POST['justification_exigible'])) { $justification_exigible="";}
    else { if (isset($_GET['justification_exigible'])) {$justification_exigible=$_GET['justification_exigible'];} if (isset($_POST['justification_exigible'])) {$justification_exigible=$_POST['justification_exigible'];} }
if (empty($_GET['sous_responsabilite_etablissement']) and empty($_POST['sous_responsabilite_etablissement'])) { $sous_responsabilite_etablissement=AbsenceEleveType::SOUS_RESP_ETAB_NON_PRECISE;}
    else { if (isset($_GET['sous_responsabilite_etablissement'])) {$sous_responsabilite_etablissement=$_GET['sous_responsabilite_etablissement'];} if (isset($_POST['sous_responsabilite_etablissement'])) {$sous_responsabilite_etablissement=$_POST['sous_responsabilite_etablissement'];} }
if (empty($_GET['manquement_obligation_presence']) and empty($_POST['manquement_obligation_presence'])) { $manquement_obligation_presence=AbsenceEleveType::MANQU_OBLIG_PRESE_NON_PRECISE;}
    else { if (isset($_GET['manquement_obligation_presence'])) {$manquement_obligation_presence=$_GET['manquement_obligation_presence'];} if (isset($_POST['manquement_obligation_presence'])) {$manquement_obligation_presence=$_POST['manquement_obligation_presence'];} }
if (empty($_GET['retard_bulletin']) and empty($_POST['retard_bulletin'])) { $retard_bulletin=AbsenceEleveType::RETARD_BULLETIN_FAUX;}
    else { if (isset($_GET['retard_bulletin'])) {$retard_bulletin=$_GET['retard_bulletin'];} if (isset($_POST['retard_bulletin'])) {$retard_bulletin=$_POST['retard_bulletin'];} }
if (empty($_GET['type_saisie']) and empty($_POST['type_saisie'])) { $type_saisie="";}
    else { if (isset($_GET['type_saisie'])) {$type_saisie=$_GET['type_saisie'];} if (isset($_POST['type_saisie'])) {$type_saisie=$_POST['type_saisie'];} }
if (empty($_GET['id_lieu']) and empty($_POST['id_lieu'])) { $id_lieu=Null;}
    else { if (isset($_GET['id_lieu'])) {$id_lieu=$_GET['id_lieu'];} if (isset($_POST['id_lieu'])) {$id_lieu=$_POST['id_lieu'];} }
if (empty($_GET['ajout_statut_type_saisie']) and empty($_POST['ajout_statut_type_saisie'])) { $ajout_statut_type_saisie="";}
    else { if (isset($_GET['ajout_statut_type_saisie'])) {$type_saisie=$_GET['ajout_statut_type_saisie'];} if (isset($_POST['ajout_statut_type_saisie'])) {$ajout_statut_type_saisie=$_POST['ajout_statut_type_saisie'];} }
if($id_lieu=='-1'){
    $id_lieu=Null;
}

include("function.php");

//$type = new AbsenceEleveType();
$type = AbsenceEleveTypeQuery::create()->findPk($id);
if ($action == 'supprimer') {
	check_token();
    if ($type != null) {
	$type->delete();
    }
} elseif ($action == "monter") {
	check_token();
    if ($type != null) {
	$type->moveUp();
    }
} elseif ($action == 'descendre') {
	check_token();
    if ($type != null) {
	$type->moveDown();
    }
} elseif ($action == 'ajouterdefaut') {
	check_token();
    //include("function.php");
    ajoutTypesParDefaut();
} elseif ($action == 'supprimer_statut') {
	check_token();
	$type_statut = AbsenceEleveTypeStatutAutoriseQuery::create()->findPk($statut_id);
	if ($type_statut != null) {
	    $type_statut->delete();
	}
} else {
    if ($nom != '') {
		check_token();
		$type = AbsenceEleveTypeQuery::create()->findPk($id);
		if ($type == null) {
			$type = new AbsenceEleveType();
		}
		$type->setNom(stripslashes($nom));
		$type->setCommentaire(stripslashes($commentaire));
		$type->setJustificationExigible($justification_exigible);
		$type->setSousResponsabiliteEtablissement($sous_responsabilite_etablissement);
		$type->setManquementObligationPresence($manquement_obligation_presence);
		$type->setRetardBulletin($retard_bulletin);
		$type->setModeInterface($type_saisie);
        $type->setIdLieu($id_lieu);
		$type->getAbsenceEleveTypeStatutAutorises(); //corrige un bug de propel sur la lecture de la base
		if ($ajout_statut_type_saisie != '') {
			//test si le statut est deja autorisé
			if (AbsenceEleveTypeStatutAutoriseQuery::create()->
				filterByStatut($ajout_statut_type_saisie)->
				filterByIdAType($type->getId())->
				find()->isEmpty()) {
				//on creer un nouveau statut autorisé
			$statut_ajout = new AbsenceEleveTypeStatutAutorise();
			$statut_ajout->setStatut($ajout_statut_type_saisie);
			$type->addAbsenceEleveTypeStatutAutorise($statut_ajout);
			$statut_ajout->save();
			}
			$action = "modifier";
		}
		$type->save();
    }
}

if(isset($_GET['corriger'])) {
	check_token();

	$table="a_types";

	$sql="SELECT * FROM $table ORDER BY sortable_rank, nom;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$cpt=1;
	while($lig=mysqli_fetch_object($res)) {
		$sql="UPDATE $table SET sortable_rank='$cpt' WHERE id='$lig->id';";
		//echo "$sql<br />";
		$update=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$update) {
			$msg="Erreur lors de la correction des rangs.<br />";
			break;
		}
		$cpt++;
	}
	$msg="Correction effectuée.<br />";
}
//==========================================
// header
$titre_page = "Gestion des types d'absence";
require_once("../../lib/header.inc.php");
//==========================================

echo "<p class='bold'>";
echo "<a href=\"index.php\">";
echo "<img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>";
?>

<div style="text-align:center">
    <h2>Définition des types d'absence</h2>
    <p>
        <span style="font-weight: bold;">Attention :</span> Associer un type à une saisie implique la création d'un traitement de cette saisie.<br />
        Pour éviter une multiplicité des traitements pour une saisie, il peut être intéressant de limiter les statuts pouvant saisir un type.<br />
        Par exemple si l'on souhaite que seule la vie scolaire crée le traitement absence ou retard on n'affectera pas ces types au statut professeur.        
    </p>
<?php if ($action == "ajouter" OR $action == "modifier" OR $action == "supprimer_statut") { ?>
<div style="text-align:center">
    <?php
    	if($action=="ajouter") { 
	    echo "<h2>Ajout d'un type</h2>";
	} elseif ($action=="modifier") {
	    echo "<h2>Modifier un type</h2>";
	}
	?>

    <form action="admin_types_absences.php" method="post" name="form2" id="form2">
<?php
echo add_token_field();
?>
     <fieldset class="fieldset_efface">
      <table cellpadding="2" cellspacing="2" class="menu">
        <tr>
          <td>Nom (obligatoire)</td>
          <td>Commentaire (facultatif)</td>
	    <td>Justification exigible</td>
	    <td>L'élève est sous la responsabilité de l'établissement</td>
	    <td>Manquement obligations (apparaît sur le bulletin)</td>
	    <td>Comptabilisée comme retard sur le bulletin (apparaît sur le bulletin)</td>
	    <td>Type de saisie</td>
        <td>Lieu</td>
	    <td>Statut(s) autorisé(s) à la saisie</td>
       </tr>
        <tr>
          <td>
           <?php
           //$type = AbsenceEleveTypeQuery::create()->findPk($id);
	   if ($type != null) { ?>
	      <input name="id" type="hidden" id="id" value="<?php echo $id ?>" />
	   <?php } ?>
	      <input name="nom" type="text" id="nom" size="14" maxlength="50" value="<?php  if ($type != null) {echo $type->getNom();} ?>" />
           </td>
           <td><textarea name="commentaire" rows="3" cols="22"><?php  if ($type != null) {echo $type->getCommentaire();} ?></textarea></td>
           <td><input name="justification_exigible" type="checkbox" id="justification_exigible" <?php  if ($type != null && $type->getJustificationExigible()) {echo "checked";} ?> /></td>
           <td>
	     <select name="sous_responsabilite_etablissement" id="sous_responsabilite_etablissement">
		<option value='<?php echo AbsenceEleveType::SOUS_RESP_ETAB_VRAI?>' <?php  if ($type != null && $type->getSousResponsabiliteEtablissement() == AbsenceEleveType::SOUS_RESP_ETAB_VRAI) {echo "selected='selected'";} ?>>oui</option>
		<option value='<?php echo AbsenceEleveType::SOUS_RESP_ETAB_FAUX?>' <?php  if ($type != null && $type->getSousResponsabiliteEtablissement() == AbsenceEleveType::SOUS_RESP_ETAB_FAUX) {echo "selected='selected'";} ?>>non</option>
		<option value='<?php echo AbsenceEleveType::SOUS_RESP_ETAB_NON_PRECISE?>' <?php  if ($type != null && $type->getSousResponsabiliteEtablissement() == AbsenceEleveType::SOUS_RESP_ETAB_NON_PRECISE) {echo "selected='selected'";} ?>>non precisé</option>
	     </select>
	   </td>
           <td>
	     <select name="manquement_obligation_presence" id="manquement_obligation_presence">
		<option value='<?php echo AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI?>' <?php  if ($type != null && $type->getManquementObligationPresence() == AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI) {echo "selected='selected'";} ?>>oui</option>
		<option value='<?php echo AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX?>' <?php  if ($type != null && $type->getManquementObligationPresence() == AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX) {echo "selected='selected'";} ?>>non</option>
		<option value='<?php echo AbsenceEleveType::MANQU_OBLIG_PRESE_NON_PRECISE?>' <?php  if ($type != null && $type->getManquementObligationPresence() == AbsenceEleveType::MANQU_OBLIG_PRESE_NON_PRECISE) {echo "selected='selected'";} ?>>non precisé</option>
	     </select>
	   </td>
           <td>
	     <select name="retard_bulletin" id="retard_bulletin">
		<option value='<?php echo AbsenceEleveType::RETARD_BULLETIN_FAUX?>' <?php  if ($type != null && $type->getRetardBulletin() == AbsenceEleveType::RETARD_BULLETIN_FAUX) {echo "selected='selected'";} ?>>non</option>
		<option value='<?php echo AbsenceEleveType::RETARD_BULLETIN_VRAI?>' <?php  if ($type != null && $type->getRetardBulletin() == AbsenceEleveType::RETARD_BULLETIN_VRAI) {echo "selected='selected'";} ?>>oui</option>
	     </select>
	   </td>
           <td>
	     <select name="type_saisie" id="type_saisie">
		<option value='NON_PRECISE' <?php  if ($type != null && $type->getModeInterface() == 'NON_PRECISE') {echo "selected='selected'";} ?>>Type de saisie non précisé</option>
		<!--<option value='DEBUT_ABS' <?php  if ($type != null && $type->getModeInterface() == 'DEBUT_ABS') {echo "selected='selected'";} ?>>Saisir le moment de debut de l'absence</option>
		<option value='FIN_ABS' <?php  if ($type != null && $type->getModeInterface() == 'FIN_ABS') {echo "selected='selected'";} ?>>Saisir le moment de fin de l'absence</option>
		<option value='DEBUT_ET_FIN_ABS' <?php  if ($type != null && $type->getModeInterface() == 'DEBUT_ET_FIN_ABS') {echo "selected='selected'";} ?>>Saisir le moment de debut et de fin</option>
		<option value='COMMENTAIRE_EXIGE' <?php  if ($type != null && $type->getModeInterface() == 'COMMENTAIRE_EXIGE') {echo "selected='selected'";} ?>>Saisir un commentaire</option>
		--><option value='DISCIPLINE' <?php  if ($type != null && $type->getModeInterface() == 'DISCIPLINE') {echo "selected='selected'";} ?>>Saisir un incident disciplinaire</option>
		<option value='CHECKBOX' <?php  if ($type != null && $type->getModeInterface() == 'CHECKBOX') {echo "selected='selected'";} ?>><?php echo AbsenceEleveType::$LISTE_LABEL_TYPE_SAISIE[AbsenceEleveType::MODE_INTERFACE_CHECKBOX]?></option>
	    <option value='CHECKBOX_HIDDEN' <?php  if ($type != null && $type->getModeInterface() == 'CHECKBOX_HIDDEN') {echo "selected='selected'";} ?>><?php echo AbsenceEleveType::$LISTE_LABEL_TYPE_SAISIE[AbsenceEleveType::MODE_INTERFACE_CHECKBOX_HIDDEN]?></option>
	    <option value='CHECKBOX_HIDDEN_REGIME' <?php  if ($type != null && $type->getModeInterface() == 'CHECKBOX_HIDDEN_REGIME') {echo "selected='selected'";} ?>><?php echo AbsenceEleveType::$LISTE_LABEL_TYPE_SAISIE[AbsenceEleveType::MODE_INTERFACE_CHECKBOX_HIDDEN_REGIME]?></option>
	     </select>
	   </td>
        <td>
	     <select name="id_lieu" id="id_lieu">
             <option value='-1' <?php  if ($type != null && $type->getIdLieu()== null) {echo "selected='selected'";} ?>> </option>
		<?php
        $lieux=AbsenceEleveLieuQuery::create()->find();
        foreach ($lieux as $lieu) :?>
             <option value='<?php echo $lieu->getId();?>' <?php if ($type != null && $type->getIdLieu() == $lieu->getId()) {echo "selected='selected'";} ?>><?php echo $lieu->getNom();?></option>
	    <?php endforeach; ?>
         </select>
	   </td>
           <td>
		<table class="menu"><?php
		if ($type != null) {
			foreach ($type->getAbsenceEleveTypeStatutAutorises() as $statut_saisie) {
				echo "<tr><td>";
				echo $statut_saisie->getStatut();
				echo "</td>";
		  		echo '<td><a href="admin_types_absences.php?action=supprimer_statut&amp;id='. $type->getId(). '&amp;statut_id='. $statut_saisie->getId() .add_token_in_url().'"><img src="../../images/icons/delete.png" width="22" height="22" title="Supprimer" border="0" alt="" /></a></td>';
				echo "</tr>";
			}
		}
	  	?>
           <tr><td>
	     <select name="ajout_statut_type_saisie" id="ajout_statut_type_saisie">
		<option value=''>Ajout d'un statut</option>
		<option value='professeur'>professeur</option>
		<option value='cpe'>cpe</option>
		<option value='scolarite'>scolarite</option>
		<option value='autre'>autre</option>
	     </select>
	   </td></tr>

		</table>
	   </td>
        </tr>
      </table>
     </fieldset>
     <input type="submit" name="Submit" value="Enregistrer" />
    </form>
<br/><br/>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php
} ?>
	<a href="admin_types_absences.php?action=ajouter"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter un nouveau type</a>
	<br/><br/>
	<a href="admin_types_absences.php?action=ajouterdefaut<?php echo add_token_in_url();?>"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter les types par défaut</a>
	<br/><br/>
    <table cellpadding="0" cellspacing="1" class="menu" style="width:80%">
      <tr>
        <td>Nom</td>
        <td>Commentaire</td>
        <td>Justification exigible</td>
	<td>L'élève est sous la responsabilité de l'établissement</td>
	<td>Manquement obligations (apparaît sur le bulletin)</td>
	<td>Retard</td>
        <td>Type de saisie</td>
        <td>Lieu</td>
	<td>Statut(s) autorisé(s) à la saisie</td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
        <td style="width: 25px;"></td>
      </tr>
    <?php
    $type_collection = new PropelCollection();
    $type_collection = AbsenceEleveTypeQuery::create()
                       ->leftJoinWith('AbsenceEleveType.AbsenceEleveLieu')
                       ->findList();
    $type = new AbsenceEleveType();
    $i = '1';
    foreach ($type_collection as $type) { ?>
        <tr onmouseover="this.style.backgroundColor='white';" onmouseout="this.style.backgroundColor='';">
	  <td><?php echo $type->getNom(); ?></td>
	  <td><?php echo $type->getCommentaire(); ?></td>
	  <td><?php if ($type->getJustificationExigible()) { ?><img src='../../images/enabled.png' width='20' height='20' title='oui' alt='oui' /><?php } ?></td>
	  <td>
	    <?php if ($type->getSousResponsabiliteEtablissement() == AbsenceEleveType::SOUS_RESP_ETAB_VRAI) { echo "<img src='../../images/enabled.png' width='20' height='20' title='oui' alt='oui' />"; }
		else if ($type->getSousResponsabiliteEtablissement() == AbsenceEleveType::SOUS_RESP_ETAB_FAUX) { echo "<img src='../../images/disabled.png' width='20' height='20' title='non' alt='non' />"; }
		//si le ManquementObligationPresence est non precisé on affiche rien
	    ?>
	  </td>
	  <td>
	    <?php if ($type->getManquementObligationPresence() == AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI) { echo "<img src='../../images/enabled.png' width='20' height='20' title='oui' alt='oui' />"; }
		else if ($type->getManquementObligationPresence() == AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX) { echo "<img src='../../images/disabled.png' width='20' height='20' title='non' alt='non' />"; }
		//si le ManquementObligationPresence est non precisé on affiche rien
	    ?>
	  </td>
	  <td>
	    <?php if ($type->getRetardBulletin() == AbsenceEleveType::RETARD_BULLETIN_VRAI) { echo "<img src='../../images/enabled.png' width='20' height='20' title='oui' alt='oui' />"; }
		//else if ($type->getRetardBulletin() == AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX) { echo "<img src='../../images/disabled.png' width='20' height='20' title='oui' alt='non' />"; }
		else { echo "<img src='../../images/disabled.png' width='20' height='20' title='non' alt='non' />"; }
	    ?>
	  </td>
	  <td><?php if ($type->getModeInterface() != AbsenceEleveType::MODE_INTERFACE_NON_PRECISE) {echo $type->getModeInterfaceDescription();} ?></td>
      <td><?php if ($type->getAbsenceEleveLieu() != null) {echo $type->getAbsenceEleveLieu()->getNom();} ?></td>
	  <td><?php
		foreach ($type->getAbsenceEleveTypeStatutAutorises() as $statut_saisie) {
			echo $statut_saisie->getStatut();
			echo " ";
		}
	  ?></td>
          <td><a href="admin_types_absences.php?action=modifier&amp;id=<?php echo $type->getId(); echo add_token_in_url();?>"><img src="../../images/icons/configure.png" title="Modifier le type '<?php echo preg_replace("/\"/"," ",$type->getNom());?>'" border="0" alt="" /></a></td>
          <td><a href="admin_types_absences.php?action=supprimer&amp;id=<?php echo $type->getId(); echo add_token_in_url();?>" onClick="return confirm('Etes-vous sûr de vouloir supprimer le type '<?php echo preg_replace("/\"/"," ",$type->getNom());?>' ?')"><img src="../../images/icons/delete.png" width="22" height="22" title="Supprimer" border="0" alt="" /></a></td>
          <td><a href="admin_types_absences.php?action=monter&amp;id=<?php echo $type->getId(); echo add_token_in_url();?>"><img src="../../images/up.png" width="22" height="22" title="Monter le type '<?php echo preg_replace("/\"/"," ",$type->getNom());?>'" border="0" alt="" /></a></td>
          <td><a href="admin_types_absences.php?action=descendre&amp;id=<?php echo $type->getId(); echo add_token_in_url();?>"><img src="../../images/down.png" width="22" height="22" title="Descendre le type '<?php echo preg_replace("/\"/"," ",$type->getNom());?>'" border="0" alt="" /></a></td>
        </tr>
     <?php } ?>
    </table>
    <br/><br/>
</div>

<?php
	echo check_sortable_rank_trouble('a_types', 'types');
	require("../../lib/footer.inc.php");
?>
