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
// mise à jour des droits dans la table droits
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
if (getSettingValue("active_module_absence") != '2') {
    die("Le module n'est pas activé.");
}

if ($utilisateur->getStatut() != "cpe" && $utilisateur->getStatut() != "scolarite" && $utilisateur->getStatut() != "professeur" && $utilisateur->getStatut() != "autre" ) {
    die("acces interdit");
}
if($utilisateur->getStatut() == "professeur" && $utilisateur->getClasses()->isEmpty()){
    die("acces interdit");
}

include_once 'lib/function.php';

// Initialisation des variables
//récupération des paramètres de la requète
$nom_eleve = isset($_POST["nom_eleve"]) ? $_POST["nom_eleve"] : (isset($_GET["nom_eleve"]) ? $_GET["nom_eleve"] : (isset($_SESSION["nom_eleve"]) ? $_SESSION["nom_eleve"] : NULL));
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : (isset($_GET["id_classe"]) ? $_GET["id_classe"] : (isset($_SESSION["id_classe_abs"]) ? $_SESSION["id_classe_abs"] : NULL));
$id_eleve = isset($_POST["id_eleve"]) ? $_POST["id_eleve"] : (isset($_GET["id_eleve"]) ? $_GET["id_eleve"] : NULL);
$date_absence_eleve_debut = isset($_POST["date_absence_eleve_debut"]) ? $_POST["date_absence_eleve_debut"] : (isset($_GET["date_absence_eleve_debut"]) ? $_GET["date_absence_eleve_debut"] : (isset($_SESSION["date_absence_eleve_debut"]) ? $_SESSION["date_absence_eleve_debut"] : NULL));
$date_absence_eleve_fin = isset($_POST["date_absence_eleve_fin"]) ? $_POST["date_absence_eleve_fin"] : (isset($_GET["date_absence_eleve_fin"]) ? $_GET["date_absence_eleve_fin"] : (isset($_SESSION["date_absence_eleve_fin"]) ? $_SESSION["date_absence_eleve_fin"] : NULL));
$type_extrait = isset($_POST["type_extrait"]) ? $_POST["type_extrait"] : (isset($_GET["type_extrait"]) ? $_GET["type_extrait"] :(isset($_SESSION["type_extrait"]) ? $_SESSION["type_extrait"] : NULL));
$affichage = isset($_POST["affichage"]) ? $_POST["affichage"] : (isset($_GET["affichage"]) ? $_GET["affichage"] : NULL);
$tri = isset($_POST["tri"]) ? $_POST["tri"] : (isset($_GET["tri"]) ? $_GET["tri"] : NULL);
$sans_commentaire = isset($_POST["sans_commentaire"]) ? $_POST["sans_commentaire"] : (isset($_GET["sans_commentaire"]) ? $_GET["sans_commentaire"] : Null);
$non_traitees = isset($_POST["non_traitees"]) ? $_POST["non_traitees"] : (isset($_GET["non_traitees"]) ? $_GET["non_traitees"] : Null);
$ods2 = isset($_POST["ods2"]) ? $_POST["ods2"] : (isset($_GET["ods2"]) ? $_GET["ods2"] : Null);
$ndj=isset($_POST["ndj"]) ? $_POST["ndj"] : (isset($_GET["ndj"]) ? $_GET["ndj"] :  null);
$ndjnj=isset($_POST["ndjnj"]) ? $_POST["ndjnj"] : (isset($_GET["ndjnj"]) ? $_GET["ndjnj"] :  null);
$nr=isset($_POST["nr"]) ? $_POST["nr"] : (isset($_GET["nr"]) ? $_GET["nr"] : null);
$click_filtrage=isset($_POST["click_filtrage"]) ? $_POST["click_filtrage"] : (isset($_GET["click_filtrage"]) ? $_GET["click_filtrage"] : null);
$filtrage=isset($_POST["filtrage"]) ? $_POST["filtrage"] : (isset($_GET["filtrage"]) ? $_GET["filtrage"] : null);
$type_filtrage=isset($_POST["type_filtrage"]) ? $_POST["type_filtrage"] : (isset($_GET["type_filtrage"]) ? $_GET["type_filtrage"] : "ET" );
$raz=isset($_POST["raz"]) ? $_POST["raz"] : (isset($_GET["raz"]) ? $_GET["raz"] : null);
$texte_conditionnel=isset($_POST["texte_conditionnel"]) ? $_POST["texte_conditionnel"] : (isset($_GET["texte_conditionnel"]) ? $_GET["texte_conditionnel"] : null);

if($ndj=="" || $raz=="ok") $ndj=Null;
if($ndjnj=="" || $raz=="ok") $ndjnj=Null;
if($nr=="" || $raz=="ok") $nr=Null;
if($type_filtrage=="" || $raz=="ok") $type_filtrage="ET";
if (isset($id_classe) && $id_classe != null)
    $_SESSION['id_classe_abs'] = $id_classe;
if (isset($date_absence_eleve_debut) && $date_absence_eleve_debut != null)
    $_SESSION['date_absence_eleve_debut'] = $date_absence_eleve_debut;
if (isset($date_absence_eleve_fin) && $date_absence_eleve_fin != null)
    $_SESSION['date_absence_eleve_fin'] = $date_absence_eleve_fin;
if (isset($type_extrait) && $type_extrait != null)
    $_SESSION['type_extrait'] = $type_extrait;

if ($date_absence_eleve_debut != null) {
    $dt_date_absence_eleve_debut = new DateTime(str_replace("/", ".", $date_absence_eleve_debut));
} else {
    $dt_date_absence_eleve_debut = new DateTime('now');
    $dt_date_absence_eleve_debut->setDate($dt_date_absence_eleve_debut->format('Y'), $dt_date_absence_eleve_debut->format('m') , $dt_date_absence_eleve_debut->format('d'));
}
if ($date_absence_eleve_fin != null) {
    $dt_date_absence_eleve_fin = new DateTime(str_replace("/", ".", $date_absence_eleve_fin));
} else {
    $dt_date_absence_eleve_fin = new DateTime('now');
}
$dt_date_absence_eleve_debut->setTime(0, 0, 0);
$dt_date_absence_eleve_fin->setTime(23, 59, 59);
$inverse_date=false;
if($dt_date_absence_eleve_debut->format("U")>$dt_date_absence_eleve_fin->format("U")){
    $date2=clone $dt_date_absence_eleve_fin;
    $dt_date_absence_eleve_fin= $dt_date_absence_eleve_debut;
    $dt_date_absence_eleve_debut= $date2;
    $inverse_date=true;
    $_SESSION['date_absence_eleve_debut'] = $dt_date_absence_eleve_debut->format('d/m/Y');
    $_SESSION['date_absence_eleve_fin'] = $dt_date_absence_eleve_fin->format('d/m/Y');
}
// fonction de formatage des dates de debut et de fin
/*
function getDateDescription($date_debut,$date_fin) {
	    $message = '';
	    if (strftime("%a %d/%m/%Y", $date_debut)==strftime("%a %d/%m/%Y", $date_fin)) {
		$message .= 'le ';
		$message .= (strftime("%a %d/%m/%Y", $date_debut));
		$message .= ' entre  ';
		$message .= (strftime("%H:%M", $date_debut));
		$message .= ' et ';
		$message .= (strftime("%H:%M", $date_fin));

	    } else {
		$message .= ' entre le ';
		$message .= (strftime("%a %d/%m/%Y %H:%M", $date_debut));
		$message .= ' et ';
		$message .= (strftime("%a %d/%m/%Y %H:%M", $date_fin));
	    }
	    return $message;
	}
 */

//paramétrage des options affichées en fonction du statut
$affichage_liens=true;
$affichage_commentaires_html=true;
if ($utilisateur->getStatut() == "professeur" || $utilisateur->getStatut() == "autre"){
    $affichage_liens=false;
    $affichage_commentaires_html=false;
    if($affichage != null && $affichage != '' && $affichage != 'html' ){
        $affichage == 'html'; //on empeche l'export odt et ods pour les autres statuts
    }
}

$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
$dojo=true;
//**************** EN-TETE *****************
$titre_page = "Les absences";
//suppression des données en session (sauf dans le cas d'un export html et odt ou d'un clic dur le bouton filtrage)
if(isset($_SESSION['donnees_bilan']) && (is_null($affichage) || ($affichage=='html' && $click_filtrage!="ok" && $raz!=="ok"))){
    unset($_SESSION['donnees_bilan']);
}

$limite_temps=true;
if(getSettingValue('Abs2DebrideBilanIndividuelLogins')){
    $logins_authorises=explode(',',getSettingValue('Abs2DebrideBilanIndividuelLogins'));
    foreach($logins_authorises as $login){
        if ($utilisateur->getLogin()==$login){
            $limite_temps=false;
            break;
        }
    }
}
$limite_jours=7;

if(($id_classe=='-1' && $affichage=='html' && $click_filtrage!="ok" && $raz!=="ok") && (is_null($id_eleve) || $id_eleve=='') && (is_null($nom_eleve) || mb_strlen($nom_eleve)<2)){
    //si limitation de temps et si la limite de temps est dépassée en mode toutes les classes on ne lance pas de calculs 
    if($limite_temps && ($dt_date_absence_eleve_fin->format('U')-$dt_date_absence_eleve_debut->format('U'))>($limite_jours*24*3600) ){
        $message=' L\'intervalle de temps choisi pour toutes les classes doit être inférieur à 7 jours ';
        $affichage='';
        $ndj=Null;
        $ndjnj=Null;
        $nr=Null;
        $filtrage=Null;
    }
}

// pas de header ou menu dans le cas de l'export odt 
// début de l'affichage des options
if ($affichage != 'ods' && $affichage != 'odt' ) {
    require_once("../lib/header.inc.php");
    include('menu_abs2.inc.php');
    include('menu_bilans.inc.php');
    if(ob_get_contents()){
        ob_flush();        
    }
    flush();
?>
    <div id="contain_div" class="css-panes">
        <?php if (isset($message)){
          echo'<h2 class="no">'.$message.'</h2>';
        }?>
        <?php if($limite_temps && $utilisateur->getStatut() != "autre" && $utilisateur->getStatut() != "professeur" ) :?>
         <p>
             <strong>La recherche sur toutes les classes n'est permise que pour une durée de <?php echo $limite_jours; ?> jours maximum si aucun nom n'est rentré.</strong>

        <p>
        <?php endif ;?>
            Cette page permet de regrouper jour par jour les saises du même type (non traitées ou ayant le même traitement) et les informations du traitement.<br />
            Pour des saisies ayant des traitements multiples , le décompte des demi-journées correspondantes peut donc apparaitre plusieurs fois. 
            Le total réel des demi-journées calculé par le module s'affiche sous le nom de l'élève.
        </p>
        <p>
            Toute modification doit être validée pour être prise en compte.
        </p>
        <?php if ($inverse_date) :?>
        <h3 class="no">Les dates de début et de fin ont été inversés.</h3>
        <?php endif; ?>
        <form dojoType="dijit.form.Form" id="bilan_individuel" name="bilan_individuel" action="bilan_individuel.php" method="post">
            <fieldset>
              <legend>Paramétrage de l'export (dates, classes, tri...) et affichage</legend>
            <h3>Bilan individuel du
    <input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve_debut" name="date_absence_eleve_debut" value="<?php echo $dt_date_absence_eleve_debut->format('Y-m-d')?>" />
    au               
    <input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve_fin" name="date_absence_eleve_fin" value="<?php echo $dt_date_absence_eleve_fin->format('Y-m-d')?>" />
        </h3>
          <?php
            if ($id_eleve!==null && $id_eleve!=''){
                $eleve=EleveQuery::create()->filterById($id_eleve)->findOne();
                $nom_eleve=$eleve->getNom();
                $id_classe=$eleve->getClasse()->getId();
            }
            ?>
            Nom (facultatif) : <input dojoType="dijit.form.TextBox" type="text" style="width : 10em" name="nom_eleve" size="10" value="<?php echo $nom_eleve ?>" onChange="document.bilan_individuel.id_eleve.value='';"/>
            <input type="hidden" name="id_eleve" value="<?php echo $id_eleve ?>"/>
            <input type="hidden" name="affichage" value="<?php echo $affichage ?>"/>
            <input type="hidden" name="filtrage" value="<?php echo $filtrage ?>"/>
            <input type="hidden" name="type_filtrage" value="<?php echo $type_filtrage ?>"/>
            <input type="hidden" name="ndj" value="<?php echo $ndj ?>" />
            <input type="hidden" name="ndjnj" value="<?php echo $ndjnj ?>" />
            <input type="hidden" name="nr" value="<?php echo $nr ?>" />
            <?php
            //on affiche une boite de selection avec les classe
            if ((getSettingValue("GepiAccesAbsTouteClasseCpe") == 'yes' && $utilisateur->getStatut() == "cpe") || $utilisateur->getStatut() == "autre" ) {
                $classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
            } else {
                $classe_col = $utilisateur->getClasses();
            }
            if (!$classe_col->isEmpty()) {                
                echo ("Classe : <select dojoType=\"dijit.form.Select\" style=\"width :12em;font-size:12px;\" name=\"id_classe\" onChange='document.bilan_individuel.id_eleve.value=\"\";'>");
                if($utilisateur->getStatut() != "autre" && $utilisateur->getStatut() != "professeur" ){
                    echo "<option value='-1'>Toutes les classes</option>\n";
                }
                foreach ($classe_col as $classe) {
                    echo "<option value='" . $classe->getId() . "'";
                    if ($id_classe == $classe->getId())
                        echo " selected='selected' ";
                    echo ">";
                    echo $classe->getNom();
                    echo "</option>\n";                    
                }                
                echo "</select> ";                
            } else {
                echo 'Aucune classe avec élève affecté n\'a été trouvée';
            }
            ?>
            Type :
            <select style="font-size:12px" dojoType="dijit.form.Select" name="type_extrait">
                <option value='1' <?php
            if ($type_extrait == '1') {
                echo 'selected="selected"';
            }
            ?>
			>Données occasionnant un manquement aux obligations de présence</option>
                <option value='2' <?php
                        if ($type_extrait == '2') {
                            echo 'selected="selected"';
                        }
            ?>>Liste de toutes les données</option>
            </select><br />            
            
            <input dojoType="dijit.form.CheckBox" type="checkbox" name="tri" value="tri"  <?php
            if($tri=='tri') {
                echo'checked';
            }            
            ?>
			> Tri des données par manquement aux obligations de présence, retard puis non manquement.
            <br />
            <?php if($utilisateur->getStatut() == "cpe"):?>            
            <input dojoType="dijit.form.CheckBox" type="checkbox" name="non_traitees" value="non_traitees"  <?php
            if($non_traitees) {
                echo'checked';
            } ?>
			> N'afficher que les saisies non traitées ou sans type (non défini et non couverte par un autre traitement)
            <br />
            <?php endif; ?>
            <?php if($utilisateur->getStatut() == "cpe" || $utilisateur->getStatut() == "scolarite"):?>
            <input dojoType="dijit.form.CheckBox" type="checkbox" name="ods2" value="ods2"  <?php
            if($ods2) {
                echo'checked';
            } ?> 
			> Ne pas répéter les informations globales de l'élève par ligne dans l'export tableur (pour totaux par colonne)
            <br />
            <input dojoType="dijit.form.CheckBox" type="checkbox" name="sans_commentaire" value="no"  <?php
            if($sans_commentaire) {
                echo'checked';
            } ?>
			> Ne pas afficher les commentaires dans l'export ods et odt
            <br />
            <input dojoType="dijit.form.CheckBox" type="checkbox" name="texte_conditionnel" value="ok"  <?php
            if($texte_conditionnel) {
                echo'checked';
            } ?>
			> Afficher le texte optionnel en bas de l'export odt
            <br />
            <input dojoType="dijit.form.CheckBox" type='checkbox' name='export_avec_resp' id='export_avec_resp' value='y' <?php if(isset($_POST['export_avec_resp'])) {echo "checked ";}?> /><label for='export_avec_resp'> Inclure les informations responsable légal 1 dans l'export ODS/ODT.</label><br />
            <?php endif; ?>            
            <button type="submit"  style="font-size:12px" dojoType="dijit.form.Button" name="affichage" value="html">Valider les modifications et afficher à l'écran</button>
        </fieldset>
		<br />
        <?php if($affichage_liens):?>
        <fieldset style="width:320px; float:left;">
            <legend>Choix du mode de sortie des données</legend>
            <button type="submit"  style="font-size:12px" dojoType="dijit.form.Button" name="affichage" value="ods" <?php
                 if($affichage==Null || $affichage=='') echo'disabled';?>>Exporter dans un tableur (ods)</button>
            <button type="submit"  style="font-size:12px" dojoType="dijit.form.Button" name="affichage" value="odt" <?php
                 if($affichage==Null || $affichage=='') echo'disabled';?>>Exporter dans un traitement de texte (odt)</button>
        </fieldset>
         <?php endif; ?>
    </form>
    <?php
    if($affichage==Null || $affichage==''){
        $color="grey";
    }else{
        $color="black";
    }
    ?>
    <div id="param-filtre">
     <form id="filtrage" method="POST" action="bilan_individuel.php" >
        <fieldset style="width:540px;">
            <legend>Filtrage des données</legend>
            <p style="color:<?php echo $color;?>">N'afficher que les élèves dont les nombres d'absences ou retards respectent les conditions ci-dessous:<br />
            Choix de la condition si plusieurs conditions sont saisies pour le filtrage : 
            <select dojoType="dijit.form.Select" style="width :3em;font-size:12px;" name="type_filtrage"  <?php if($affichage==Null || $affichage=='') echo'disabled';?>>
                <option value="OU" <?php if($type_filtrage=="OU") echo 'selected="selected"';?>>OU</option>
                <option value="ET" <?php if($type_filtrage=="ET") echo 'selected="selected"';?>>ET</option>
            </select>
            <br />    
            Nombre total de 1/2 journées &ge;: <INPUT dojoType="dijit.form.NumberTextBox" style="width:3em;" constraints="{min:1}" type="text" <?php if($ndj!=Null)echo'value='.$ndj; else echo'value=""'; ?> name="ndj" size="3" maxlength="3"  <?php
             if($affichage==Null || $affichage=='') echo'disabled';?>/><br />
            (OU/ET) Nombre de 1/2 journées non justifiées &ge;: <INPUT dojoType="dijit.form.NumberTextBox" style="width:3em;" constraints="{min:1}" type="text" <?php if($ndjnj!=Null)echo'value='.$ndjnj; else echo'value=""'; ?> name="ndjnj" size="3" maxlength="3"  <?php
             if($affichage==Null || $affichage=='') echo'disabled';?>><br />
            (OU/ET) Nombre de retards &ge;: <INPUT dojoType="dijit.form.NumberTextBox" constraints="{min:1}" style="width:3em;" type="text" <?php if($nr!=Null)echo'value='.$nr; else echo'value=""'; ?> name="nr" size="3" maxlength="3"  <?php
             if($affichage==Null || $affichage=='') echo'disabled';?>><br />
            <input type="hidden" name="nom_eleve"  value="<?php echo $nom_eleve ?>" />
            <input type="hidden" name="affichage" value="html" />
            <input type="hidden" name="tri" value="<?php echo $tri ?>" />
            <input type="hidden" name="non_traitees" value="<?php echo $non_traitees ?>" />
            <input type="hidden" name="ods2" value="<?php echo $ods2 ?>" />
            <input type="hidden" name="texte_conditionnel" value="<?php echo $texte_conditionnel ?>" />
            <input type="hidden" name="sans_commentaire" value="<?php echo $sans_commentaire ?>" />
            <input type="hidden" name="filtrage" value="ok" />
            <button type="submit"  style="font-size:12px" dojoType="dijit.form.Button" name="click_filtrage" value="ok" <?php
             if($affichage==Null || $affichage=='') echo'disabled';?>>Filtrer</button> 
            <button type="submit"  style="font-size:12px" dojoType="dijit.form.Button" name="raz" value="ok" <?php
             if($affichage==Null || $affichage=='') echo'disabled';?>>Réinitialiser</button></p>
        </fieldset> 
    </form>
    </div>  
    <?php
}
// fin de l'affichage des options
// début de la mise en session des données extraites (sauf si on est dans un filtrage des données affichées)

if ($affichage =='html' && $click_filtrage!=="ok" && $raz!=="ok") {

$eleve_query = EleveQuery::create();
if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    } else {
	$eleve_query->filterByUtilisateurProfessionnel($utilisateur);
    }
if ($id_classe !== null && $id_classe != -1 ) {
    $eleve_query->useJEleveClasseQuery()->filterByIdClasse($id_classe)->endUse();
}

if ($nom_eleve !== null && $nom_eleve != '') {
    $eleve_query->filterByNom('%'.$nom_eleve.'%');
}
if ($id_eleve !== null && $id_eleve != '') {
    $eleve_query->filterById($id_eleve);
}
$eleve_query->orderByNom()->orderByPrenom()->distinct();
$table_synchro_ok = AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable($dt_date_absence_eleve_debut,$dt_date_absence_eleve_fin);
    if (!$table_synchro_ok) {//la table n'est pas synchronisée. On va vérifier individuellement les élèves qui ne sont pas synchronisés
		$eleve_col = $eleve_query->find();
		if ($eleve_col->count()>150) {
			echo '<span style="color:red">Il semble que vous demandez des statistiques sur trop d\'élèves et votre table de statistiques n\'est pas synchronisée. Veuillez faire une demande pour moins d\'élèves ou demander à votre administreteur de remplir la table d\'agrégation.</span>';
			if (ob_get_contents()) {
				ob_flush();
			}
			flush();
		}
		foreach ($eleve_col as $eleve) {
			$eleve->checkAndUpdateSynchroAbsenceAgregationTable($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin);
		}
	}
     //on recommence la requetes, maintenant que la table est synchronisé, avec les données d'absence
        
    $eleve_col = $eleve_query->find();
    
if ($eleve_col->isEmpty()) {    
    echo"<h2 class='no'>Aucun élève avec les paramètres sélectionnés n'a été trouvé.</h2>";
    die();
}
$precedent_eleve_id = null;
if (isset($_SESSION['donnees_bilan'])){
    $donnees = unserialize($_SESSION['donnees_bilan']);
}
foreach ($eleve_col as $eleve) {    
  //  $eleve->checkAndUpdateSynchroAbsenceAgregationTable($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin);
    $eleve_id = $eleve->getId();
    //on initialise les donnees pour le nouvel eleve
    if ($precedent_eleve_id != $eleve_id) {
        $donnees[$eleve_id]['nom'] = $eleve->getNom();
        $donnees[$eleve_id]['prenom'] = $eleve->getPrenom();
        $donnees[$eleve_id]['classe'] = $eleve->getClasseNom();

		if ($eleve->getDateSortie()) {
			$donnees[$eleve_id]['sortie'] = date('j/m/Y',strtotime($eleve->getDateSortie()));
		} else {
			$donnees[$eleve_id]['sortie'] = "";
		}

		// 20141105
		if(isset($_POST['export_avec_resp'])) {
			//get_resp_from_ele_login($ele_login, $meme_en_resp_legal_0="n")
			//get_adresse_responsable($pers_id, $login_resp="")
			//responsables_adresses_separees($login_eleve)
			//$sql="SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='".$eleve->getLogin()."' AND rp.pers_id=r.pers_id AND r.ele_id=e.ele_id AND (r.resp_legal='1' OR r.resp_legal='2')";
			$sql="SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='".$eleve->getLogin()."' AND rp.pers_id=r.pers_id AND r.ele_id=e.ele_id AND r.resp_legal='1';";
			//echo "$sql<br />";
			$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_resp)>0) {
				$tab_resp=mysqli_fetch_assoc($res_resp);

				$donnees[$eleve_id]['resp_legal'][1]=$tab_resp;

				$tab_adr=get_adresse_responsable($tab_resp['pers_id']);

				$donnees[$eleve_id]['resp_legal'][1]['adr1']=$tab_adr['adr1'];
				$donnees[$eleve_id]['resp_legal'][1]['adr2']=$tab_adr['adr2'];
				$donnees[$eleve_id]['resp_legal'][1]['adr3']=$tab_adr['adr3'];
				$donnees[$eleve_id]['resp_legal'][1]['cp']=$tab_adr['cp'];
				$donnees[$eleve_id]['resp_legal'][1]['commune']=$tab_adr['commune'];
				$donnees[$eleve_id]['resp_legal'][1]['pays']=$tab_adr['pays'];
				$donnees[$eleve_id]['resp_legal'][1]['en_ligne']=$tab_adr['en_ligne'];

			}
			else {
				$donnees[$eleve_id]['resp_legal'][1]['pers_id']="";
				$donnees[$eleve_id]['resp_legal'][1]['login']="";
				$donnees[$eleve_id]['resp_legal'][1]['nom']="";
				$donnees[$eleve_id]['resp_legal'][1]['prenom']="";
				$donnees[$eleve_id]['resp_legal'][1]['civilite']="";
				$donnees[$eleve_id]['resp_legal'][1]['tel_pers']="";
				$donnees[$eleve_id]['resp_legal'][1]['tel_port']="";
				$donnees[$eleve_id]['resp_legal'][1]['tel_prof']="";
				$donnees[$eleve_id]['resp_legal'][1]['mel']="";
				$donnees[$eleve_id]['resp_legal'][1]['adr_id']="";

				$donnees[$eleve_id]['resp_legal'][1]['adr1']="";
				$donnees[$eleve_id]['resp_legal'][1]['adr2']="";
				$donnees[$eleve_id]['resp_legal'][1]['adr3']="";
				$donnees[$eleve_id]['resp_legal'][1]['cp']="";
				$donnees[$eleve_id]['resp_legal'][1]['commune']="";
				$donnees[$eleve_id]['resp_legal'][1]['pays']="";
				$donnees[$eleve_id]['resp_legal'][1]['en_ligne']="";
			}
		}

        $donnees[$eleve_id]['nbre_lignes_total'] = 0;
    }
	/*
	// 20141105
	echo "<pre>";
	print_r($donnees);
	echo "</pre>";
	*/
    // on récupère les saisies de l'élève
    $saisie_query = AbsenceEleveSaisieQuery::create()
                    ->filterByPlageTemps($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
                    ->filterByEleveId($eleve->getId());

    if ($type_extrait == '1') {
        $saisie_query->filterByManquementObligationPresence(true);
    }
    $saisie_query->orderByDebutAbs();    
    $saisie_col = $saisie_query->find();

    // on traite les saisies et on stocke les informations dans un tableau
    foreach ($saisie_col as $saisie) {
        if ($type_extrait == '1' && !$saisie->getManquementObligationPresence()) {
            continue;
        }
        if (!is_null($non_traitees) && $non_traitees != '' && $saisie->getTraitee() && $saisie->hasModeInterface()) {
            continue;
        }
        if ($saisie->getRetard()) {
            if ($tri != null && $tri != '') {
                $type_tab = 'retard';
            } else {
                $type_tab = 'sans';
            }
            $type_css = 'couleur_retard';
        } elseif ($saisie->getManquementObligationPresence()) {
            if ($tri != null && $tri != '') {
                $type_tab = 'manquement';
            } else {
                $type_tab = 'sans';
            }
            $type_css = 'couleur_manquement';
        } else {
            if ($tri != null && $tri != '') {
                $type_tab = 'sans_manquement';
            } else {
                $type_tab = 'sans';
            }
            $type_css = '';
        }
        if ($saisie->getTraitee()) {
            foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {               
                if (!isset($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()])) {
                    $donnees[$eleve_id]['nbre_lignes_total']++;
                }
                $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['saisies'][] = $saisie->getId();               
                if (isset($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates'])) {
                    if ($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates']['debut'] > $saisie->getDebutAbs('U')) {
                        $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates']['debut'] = $saisie->getDebutAbs('U');
                    }
                    if ($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates']['fin'] < $saisie->getFinAbs('U')) {
                        $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates']['fin'] = $saisie->getFinAbs('U');
                    }
                } else {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['dates'] = Array('debut' => $saisie->getDebutAbs('U'), 'fin' => $saisie->getFinAbs('U'));
                }
                if ($traitement->getAbsenceEleveType() != Null) {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['type'] = $traitement->getAbsenceEleveType()->getNom();
                } else {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['type'] = 'Non défini';
                }
                if ($traitement->getAbsenceEleveMotif() != Null) {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['motif'] = $traitement->getAbsenceEleveMotif()->getNom();
                } else {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['motif'] = '-';
                }
                if ($traitement->getAbsenceEleveJustification() != Null) {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['justification'] = $traitement->getAbsenceEleveJustification()->getNom();
                } else {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['justification'] = '-';
                }
                if ($saisie->getCommentaire() !== '') {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['commentaires'][] = $saisie->getCommentaire();
                }
                $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')][$traitement->getId()]['type_css'] = $type_css;
            }
        } else {
            if (!isset($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees'])) {
                $donnees[$eleve_id]['nbre_lignes_total']++;
            }
            $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['saisies'][] = $saisie->getId();            
            $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['type'] = 'Non traitée(s)';
            $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['motif'] = '-';
            $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['justification'] = '-';
            if (isset($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates'])) {
                if ($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates']['debut'] > $saisie->getDebutAbs('U')) {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates']['debut'] = $saisie->getDebutAbs('U');
                }
                if ($donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates']['fin'] < $saisie->getFinAbs('U')) {
                    $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates']['fin'] = $saisie->getFinAbs('U');
                }
            } else {
                $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['dates'] = Array('debut' => $saisie->getDebutAbs('U'), 'fin' => $saisie->getFinAbs('U'));
            }
            if ($saisie->getCommentaire() !== '') {
                $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['commentaires'][] = $saisie->getCommentaire();
            }
            $donnees[$eleve_id]['infos_saisies'][$type_tab][$saisie->getDebutAbs('d/m/Y')]['non_traitees']['type_css'] = $type_css;
        }        
    }    
    $precedent_eleve_id = $eleve->getId();    
}
//on récupère les demi-journées globales et par ligne
foreach ($donnees as $id => &$eleve) {
    if(!isset($eleve['infos_saisies'])) continue;
$propel_eleve = EleveQuery::create()->filterById($id)->findOne();
       // $propel_eleve->checkAndUpdateSynchroAbsenceAgregationTable($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin);
        $eleve['demi_journees'] = AbsenceAgregationDecompteQuery::create()
                ->filterByEleve($propel_eleve)
                ->filterByDateIntervalle($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
                ->filterByManquementObligationPresence(true)
                ->count();
        $eleve['non_justifiees'] = AbsenceAgregationDecompteQuery::create()
                ->filterByEleve($propel_eleve)
                ->filterByDateIntervalle($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
                ->filterByManquementObligationPresence(true)
                ->filterByNonJustifiee(true)
                ->count();
        $eleve['retards'] = AbsenceAgregationDecompteQuery::create()
                ->filterByEleve($propel_eleve)
                ->filterByDateIntervalle($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
                ->countRetards();
        foreach ($eleve['infos_saisies'] as $type_tab => &$value2) {
        foreach ($value2 as &$journee) {
            foreach ($journee as $key => &$value) {
            $abs_col = AbsenceEleveSaisieQuery::create()->filterById($value['saisies'])->orderByDebutAbs()->find();
            foreach( $abs_col as $saisie){
                if($abs_col->isFirst()){
                    $date_debut_col=new DateTime($saisie->getDebutAbs());
                    $date_fin_col=new DateTime($saisie->getFinAbs());                    
                }else{
                    $date_debut_col_clone=new DateTime($saisie->getDebutAbs());
                    $date_fin_col_clone=new DateTime($saisie->getFinAbs());
                    if($date_debut_col_clone->format('U')<$date_debut_col->format('U')){
                        $date_debut_col=$date_debut_col_clone;
                        
                    }
                    if($date_fin_col_clone->format('U')>$date_fin_col->format('U')){
                        $date_fin_col=$date_fin_col_clone;
                        
                    }
                }                
            } 
            $value['demi_journees'] = AbsenceAgregationDecompteQuery::create()
                ->filterByEleve($propel_eleve)
                ->filterByDateIntervalle($date_debut_col, $date_fin_col)
                ->filterByManquementObligationPresence(true)
                ->count();            
            $value['demi_journees_non_justifiees'] = AbsenceAgregationDecompteQuery::create()
                ->filterByEleve($propel_eleve)
                ->filterByDateIntervalle($date_debut_col, $date_fin_col)
                ->filterByManquementObligationPresence(true)
                ->filterByNonJustifiee(true)
                ->count();
            $value['demi_journees_justifiees'] = $value['demi_journees'] - $value['demi_journees_non_justifiees'];            
            }
        }
    }
}
//on met toutes les donnees en session
$_SESSION['donnees_bilan']=serialize($donnees);

}
// fin de la mise en session des données extraites
// On fais une copie des données en session pour affichage
if($affichage !="odt" && $affichage!="ods"){
    if(isset($_SESSION['donnees_bilan'])){
        $_SESSION['donnees_bilan_affichage']=$_SESSION['donnees_bilan'];
    }    
}
//Prise en compte du filtrage
if ($filtrage == "ok" && ($ndj != null || $ndjnj != null || $nr != null)) {
    if (isset($_SESSION['donnees_bilan_affichage']))
        $donnees_filtrage = unserialize($_SESSION['donnees_bilan_affichage']);
    $cpt_eleve = 0;
    $cpt_eleve_filtre = 0;
    switch ($type_filtrage) {
        case "OU":
            foreach ($donnees_filtrage as $id => $eleve) {
                if (!isset($eleve['demi_journees'])) {
                    continue;                    
                }
                $cpt_eleve++;
                if ($ndj != null && $eleve['demi_journees'] > ($ndj-1)){ 
                    
                    }elseif ($ndjnj != null && $eleve['non_justifiees'] > ($ndjnj-1)){ 
                        
                        }elseif ($nr != null && $eleve['retards'] > ($nr-1)){ 
                            
                            }else{
                                $cpt_eleve_filtre++;
                                unset($donnees_filtrage[$id]);
                                }                    
                }
                break;
        case "ET":
            foreach ($donnees_filtrage as $id => $eleve) {
                if (!isset($eleve['demi_journees'])) {
                    continue;                    
                }
                $cpt_eleve++;
                if (($ndj != null && $eleve['demi_journees'] < $ndj) || ($ndjnj != null && $eleve['non_justifiees'] < $ndjnj) || ($nr != null && $eleve['retards'] < $nr)) {
                    $cpt_eleve_filtre++;
                    unset($donnees_filtrage[$id]);
                }
            }
            break;
    }

    if($affichage !="odt" && $affichage!="ods"){
        echo'<p class="red">Les données affichées ci-dessous ont été filtrées. ';
        echo'Le nombre d\'élèves affiché est de '.($cpt_eleve-$cpt_eleve_filtre).' sur un total initial de '.$cpt_eleve.'</p>';
    } 
    $_SESSION['donnees_bilan_affichage'] = serialize($donnees_filtrage);
}
//Fin prise en compte du filtrage
// début des export
  //export html
if ($affichage == 'html') {
echo'<div id="sortie_ecran">';
echo '<table border="1" cellspacing="0" align="center">';
echo '<tr >';
echo '<td align="center">';
echo 'Informations sur l\'élève';
echo '</td>';
echo '<td align="center">';
echo 'Saisies ';
echo '</td>';
echo '<td align="center">';
echo 'Décompte J';
echo '</td>';
echo '<td align="center">';
echo 'Décompte NJ';
echo '</td>';
echo '<td align="center">';
echo 'Type';
echo '</td>';
echo '<td align="center">';
echo 'Motif';
echo '</td>';
echo '<td align="center">';
echo 'Justification';
echo '</td>';
if($affichage_commentaires_html){
   echo '<td align="center">';
   echo 'Commentaire(s)';
   echo '</td>';
}
echo '</tr>';
$precedent_eleve_id = Null;
if(isset($_SESSION['donnees_bilan_affichage'])) $donnees=unserialize($_SESSION['donnees_bilan_affichage']);
foreach ($donnees as $id => $eleve) {
    if(!isset($eleve['infos_saisies'])){
        continue;
    }
    if($tri!=null && $tri!='') {
        ksort($eleve['infos_saisies']);
    }
    foreach ($eleve['infos_saisies'] as $type_tab=>$value2) {
        foreach ($value2 as $journee) {
            foreach ($journee as $key => $value) {                
                $style=$value['type_css'];
                echo'<tr>';
                if ($precedent_eleve_id != $id) {                    
                    echo '<td rowspan=' . $eleve['nbre_lignes_total'] . '>';
                    echo '<a href="bilan_individuel.php?id_eleve=' . $id . '&affichage=html&tri='.$tri.'&sans_commentaire='.$sans_commentaire.'&texte_conditionnel='.$texte_conditionnel.'&filtrage='.$filtrage.'&ndj='.$ndj.'&ndjnj='.$ndjnj.'&nr='.$nr.'">';
                    echo '<b>' . $eleve['nom'] . ' ' . $eleve['prenom'] . '</b></a><br/> (' . $eleve['classe'] . ')';
                    $propel_eleve=EleveQuery::create()->filterById($id)->findOne();
                    if ($utilisateur->getAccesFicheEleve($propel_eleve)) {
                        echo "<a href='../eleves/visu_eleve.php?ele_login=".$propel_eleve->getLogin()."&amp;onglet=responsables&amp;quitter_la_page=y' target='_blank'>";
                        echo ' (voir fiche)';
                        echo "</a>";
                    }

			if((acces('/edt/index2.php', $_SESSION['statut']))&&(getSettingValue('active_module_absence')=='2')) {
				echo "<a href='$gepiPath/edt/index2.php?affichage=semaine&type_affichage=eleve&login_eleve=".$propel_eleve->getLogin()."&affichage_complementaire_sur_edt=absences2' target='_blank' title=\"Affichage des absences sur un EDT version 2\"><img src='$gepiPath/images/icons/edt2_abs2.png' width='24' height='24' alt='EDT2ABS2' /></a> ";
			}

                    if($affichage_liens){
                      echo'<a href="bilan_individuel.php?id_eleve=' . $id . '&affichage=ods&tri='.$tri.'&sans_commentaire='.$sans_commentaire.'&ods2='.$ods2.'"><img src="../images/icons/ods.png" title="export ods"></a>
                      <a href="bilan_individuel.php?id_eleve=' . $id . '&affichage=odt&tri='.$tri.'&sans_commentaire='.$sans_commentaire.'&texte_conditionnel='.$texte_conditionnel.'"><img src="../images/icons/odt.png" title="export odt"></a><br/>';
                    }
					if ($eleve['sortie']){
						echo'<span style="color:red">Date de sortie de l\'établissement : '.$eleve['sortie'].'</span>';
					}
                    echo'<br />';
                    echo '<ins><em>Absences :</em></ins> <br />';
                    if (strval($eleve['demi_journees']) == 0) {
                        echo 'Aucune demi-journée';
                    } else {
                        echo '<b>' . $eleve['demi_journees'] . '</b> demi-journée';
                        if (strval($eleve['demi_journees']) > 1)
                            echo's';

                        if (strval($eleve['demi_journees'] - $eleve['non_justifiees']) != 0) {
                            echo' <br /> ';
                            echo 'dont ' . strval($eleve['demi_journees'] - $eleve['non_justifiees']) . ' justifiée';
                            if (strval($eleve['demi_journees'] - $eleve['non_justifiees']) > 1)
                                echo's';
                        }

                        if (strval($eleve['non_justifiees']) != 0) {
                            echo'<br />';
                            echo 'dont <b>' . $eleve['non_justifiees'] . ' non justifiée</b>';
                            if (strval($eleve['non_justifiees']) > 1)
                                echo's';
                        }
                    }
                    echo'<br /><br />';
                    echo '<ins><em>Retards :</em></ins><br />';
                    if (strval($eleve['retards']) == 0) {
                        echo 'Aucun retard';
                    } else {
                        echo $eleve['retards'] . ' retard';
                        if (strval($eleve['retards']) > 1)
                            echo's';
                    }
                    echo '</td>';
                }
                echo '<td class="'.$style.'">';
                if($affichage_liens){
                    echo '<a href="./liste_saisies_selection_traitement.php?saisies=' . serialize($value['saisies']) . '" target="_blank">' . getDateDescription($value['dates']['debut'], $value['dates']['fin']) . '</a>';
                }else{
                    echo getDateDescription($value['dates']['debut'], $value['dates']['fin']) ;
                }
                echo '</td>';                
                echo '<td align="center" class="'.$style.'">';
                if (!0 == $value['demi_journees_justifiees'])
                    echo '<font class="ok">' . $value['demi_journees_justifiees'] . '</font>';
                echo '</td>';
                echo '<td align="center" class="'.$style.'">';
                if (!0 == $value['demi_journees_non_justifiees'])
                    echo '<font class="no">' . $value['demi_journees_non_justifiees'] . '</font>';
                echo '</td>';
                echo '<td class="'.$style.'">';               
                if ($value['type'] !== 'Non traitée(s)') {
                    $class = '';
                    if ($value['type'] == 'Non défini') {
                        $class = 'orange';
                    }
                    if($affichage_liens){
                        echo'<a class="' . $class . '" href="./visu_traitement.php?id_traitement=' . $key . '" target="_blank">' . $value['type'] . '</a>';
                    }else{
                        echo'<font class="' . $class . '">' . $value['type'] . '</font>';
                    }
                } else {
                    echo '<font class="orange">' . $value['type'] . '</font>';
                }
                echo '</td>';                
                echo '<td class="'.$style.'">';
                echo $value['motif'];
                echo '</td>';
                echo '<td class="'.$style.'">';
                echo $value['justification'];
                echo '</td>';
                if($affichage_commentaires_html){
                    echo '<td class="'.$style.'">';
                    if (isset($value['commentaires'])) {
                        $besoin_echo_virgule = false;
                        foreach ($value['commentaires'] as $commentaire) {
                            if ($besoin_echo_virgule) {
                                echo ', ';
                            }
                            echo $commentaire;
                            $besoin_echo_virgule = true;
                            }
                      }
                      echo '</td>';
                }
                echo '</tr>';                
                $precedent_eleve_id = $id;
            }
        }
    }
}
echo '<h5>Extraction réalisée le '.date("d/m/Y - H:i").'</h5>';
echo'</div>';
//fin export html; debut export odt et ods
} else if ($affichage == 'ods' || $affichage == 'odt') {
include_once '../orm/helpers/AbsencesNotificationHelper.php';
if(isset($_SESSION['donnees_bilan_affichage'])){
    $donnees=unserialize($_SESSION['donnees_bilan_affichage']);
}
if ($affichage == 'ods') {

	/*
	// 20141105
	echo "<pre>";
	print_r($donnees);
	echo "</pre>";
	die();
	*/

    $extension='ods';
    $export = array();
    foreach ($donnees as $id => $eleve) {
        if(!isset($eleve['infos_saisies'])){
        continue;
        }
        if($id_eleve!=null && $id_eleve !='' && $id!=$id_eleve ){
            continue;
        }
        $indice=TRUE;
        if($tri!=null && $tri!='') {
            ksort($eleve['infos_saisies']);
        }
        foreach ($eleve['infos_saisies'] as $type_tab) {
            foreach ($type_tab as $journee) {
                foreach ($journee as $key => $value) {
                    if($indice){
                        $nom = $eleve['nom'];
                        $prenom = $eleve['prenom'];
                        $classe = $eleve['classe'];

				if(isset($_POST['export_avec_resp'])) {
                        // 20141105
                        $resp_1_nom=$eleve['resp_legal'][1]['nom'];
                        $resp_1_prenom=$eleve['resp_legal'][1]['prenom'];
                        $resp_1_civilite=$eleve['resp_legal'][1]['civilite'];
                        $resp_1_adr1=$eleve['resp_legal'][1]['adr1'];
                        $resp_1_adr2=$eleve['resp_legal'][1]['adr2'];
                        $resp_1_adr3=$eleve['resp_legal'][1]['adr3'];
                        $resp_1_cp=$eleve['resp_legal'][1]['cp'];
                        $resp_1_commune=$eleve['resp_legal'][1]['commune'];
                        $resp_1_pays=$eleve['resp_legal'][1]['pays'];
                        $resp_1_adr_en_ligne=$eleve['resp_legal'][1]['en_ligne'];
				}

                        $total_demi_journees = strval($eleve['demi_journees']);
                        $total_demi_journees_justifiees = strval($eleve['demi_journees'] - $eleve['non_justifiees']);
                        $total_demi_journees_non_justifiees = strval($eleve['non_justifiees']);
                        $retards = $eleve['retards'];
                    }else{
                        $nom = '';
                        $prenom = '';
                        $classe = '';

				if(isset($_POST['export_avec_resp'])) {
                        $resp_1_nom="";
                        $resp_1_prenom="";
                        $resp_1_civilite="";
                        $resp_1_adr1="";
                        $resp_1_adr2="";
                        $resp_1_adr3="";
                        $resp_1_cp="";
                        $resp_1_commune="";
                        $resp_1_pays="";
                        $resp_1_adr_en_ligne="";
				}

                        $total_demi_journees = '';
                        $total_demi_journees_justifiees = '';
                        $total_demi_journees_non_justifiees = '';
                        $retards = '';
                    }
                    if(!is_null($ods2)&& $ods2!=''){
                        $indice=FALSE;
                    }
                    $dates = getDateDescription($value['dates']['debut'], $value['dates']['fin']);
                    $ligne_demi_journees_non_justifiees=$value['demi_journees_non_justifiees'];
                    $ligne_demi_journees_justifiees=$value['demi_journees_justifiees'];
                    $type = $value['type'];
                    $motif = $value['motif'];
                    $justification = $value['justification'];
                    $export_commentaire = '';
                    if (isset($value['commentaires']) && (is_null($sans_commentaire) || $sans_commentaire=='')) {
                        $besoin_echo_virgule = false;
                        foreach ($value['commentaires'] as $commentaire) {
                            if ($besoin_echo_virgule) {
                                $export_commentaire.= ', ';
                            }
                            $export_commentaire.=$commentaire;
                            $besoin_echo_virgule = true;
                        }
                    }
                    // 20141105
				if(isset($_POST['export_avec_resp'])) {
                    $export[] = Array('nom' => $nom, 'prenom' => $prenom, 'classe' => $classe,

                        'total_demi_journees' => $total_demi_journees,
                        'total_demi_journees_justifiees' => $total_demi_journees_justifiees,
                        'total_demi_journees_non_justifiees' => $total_demi_journees_non_justifiees,
                        'retards' => $retards,
                        'dates' => $dates,
                        'ligne_demi_journees_non_justifiees' => $ligne_demi_journees_non_justifiees,
                        'ligne_demi_journees_justifiees' => $ligne_demi_journees_justifiees,
                        'type' => $type,
                        'motif' => $motif,
                        'justification' => $justification,
                        'export_commentaire' => $export_commentaire,

                        'resp_1_nom'=>$resp_1_nom,
                        'resp_1_prenom'=>$resp_1_prenom,
                        'resp_1_civilite'=>$resp_1_civilite,
                        'resp_1_adr1'=>$resp_1_adr1,
                        'resp_1_adr2'=>$resp_1_adr2,
                        'resp_1_adr3'=>$resp_1_adr3,
                        'resp_1_cp'=>$resp_1_cp,
                        'resp_1_commune'=>$resp_1_commune,
                        'resp_1_pays'=>$resp_1_pays,
                        'resp_1_adr_en_ligne'=>$resp_1_adr_en_ligne
                        );
				}
				else {
                    $export[] = Array('nom' => $nom, 'prenom' => $prenom, 'classe' => $classe,
                        'total_demi_journees' => $total_demi_journees,
                        'total_demi_journees_justifiees' => $total_demi_journees_justifiees,
                        'total_demi_journees_non_justifiees' => $total_demi_journees_non_justifiees,
                        'retards' => $retards,
                        'dates' => $dates,
                        'ligne_demi_journees_non_justifiees' => $ligne_demi_journees_non_justifiees,
                        'ligne_demi_journees_justifiees' => $ligne_demi_journees_justifiees,
                        'type' => $type,
                        'motif' => $motif,
                        'justification' => $justification,
                        'export_commentaire' => $export_commentaire);
				}

                    $eleve_current =Null;
                    $abs_col = Null;
                    $ligne_demi_journees=Null;
                    $ligne_demi_journees_non_justifiees=Null;
                    $ligne_demi_journees_justifiees=Null;
                }
            }
        }
    }    
} else {
    $extension = 'odt';
    $export = array();
    foreach ($donnees as $id => $eleve) {
        if(!isset($eleve['infos_saisies'])){
        continue;
        }
        if($id_eleve!=null && $id_eleve !='' && $id!=$id_eleve ){
            continue;
        }
        if($tri!=null && $tri!='') {
            ksort($eleve['infos_saisies']);
        }
        foreach ($eleve['infos_saisies'] as $type_tab) {
            foreach ($type_tab as $journee) {
                foreach ($journee as $key => $value) {
                    $nom = $eleve['nom'];
                    $prenom = $eleve['prenom'];
                    $classe = $eleve['classe'];

				if(isset($_POST['export_avec_resp'])) {
                    // 20141105
                    $resp_1_nom=$eleve['resp_legal'][1]['nom'];
                    $resp_1_prenom=$eleve['resp_legal'][1]['prenom'];
                    $resp_1_civilite=$eleve['resp_legal'][1]['civilite'];
                    $resp_1_adr1=$eleve['resp_legal'][1]['adr1'];
                    $resp_1_adr2=$eleve['resp_legal'][1]['adr2'];
                    $resp_1_adr3=$eleve['resp_legal'][1]['adr3'];
                    $resp_1_cp=$eleve['resp_legal'][1]['cp'];
                    $resp_1_commune=$eleve['resp_legal'][1]['commune'];
                    $resp_1_pays=$eleve['resp_legal'][1]['pays'];
                    $resp_1_adr_en_ligne=$eleve['resp_legal'][1]['en_ligne'];
				}

                    $total_demi_journees = strval($eleve['demi_journees']);
                    $total_demi_journees_justifiees = strval($eleve['demi_journees'] - $eleve['non_justifiees']);
                    $total_demi_journees_non_justifiees = strval($eleve['non_justifiees']);
                    $retards = $eleve['retards'];
                    $dates = getDateDescription($value['dates']['debut'], $value['dates']['fin']);
                    $ligne_demi_journees =$value['demi_journees'];
                    if($ligne_demi_journees >0){
                        $ligne_demi_journees_non_justifiees = $value['demi_journees_non_justifiees'];
                        if($ligne_demi_journees_non_justifiees==0){
                            $ligne_demi_journees_non_justifiees='';
                        }
                        $ligne_demi_journees_justifiees = $value['demi_journees_justifiees'];;
                        if($ligne_demi_journees_justifiees==0){
                            $ligne_demi_journees_justifiees='';
                        }
                    }else{
                        $ligne_demi_journees_non_justifiees = '-';
                        $ligne_demi_journees_justifiees = '-';
                    }                    
                    $type = $value['type'];
                    $motif = $value['motif'];
                    $justification = $value['justification'];
                    $export_commentaire = '';
                    if (isset($value['commentaires']) && (is_null($sans_commentaire) || $sans_commentaire=='')) {
                        $besoin_echo_virgule = false;
                        foreach ($value['commentaires'] as $commentaire) {
                            if ($besoin_echo_virgule) {
                                $export_commentaire.= ', ';
                            }
                            $export_commentaire.=$commentaire;
                            $besoin_echo_virgule = true;
                        }
                    }
                    if (!isset($export[$id])) {
                        // 20141105
				if(isset($_POST['export_avec_resp'])) {
                        $export[$id] = Array('nom' => $nom, 'prenom' => $prenom, 'classe' => $classe,
                            'total_demi_journees' => $total_demi_journees,
                            'total_demi_journees_justifiees' => $total_demi_journees_justifiees,
                            'total_demi_journees_non_justifiees' => $total_demi_journees_non_justifiees,
                            'retards' => $retards,
                            'resp_1_nom'=>$resp_1_nom,
                            'resp_1_prenom'=>$resp_1_prenom,
                            'resp_1_civilite'=>$resp_1_civilite,
                            'resp_1_adr1'=>$resp_1_adr1,
                            'resp_1_adr2'=>$resp_1_adr2,
                            'resp_1_adr3'=>$resp_1_adr3,
                            'resp_1_cp'=>$resp_1_cp,
                            'resp_1_commune'=>$resp_1_commune,
                            'resp_1_pays'=>$resp_1_pays,
                            'resp_1_adr_en_ligne'=>$resp_1_adr_en_ligne
                            );
				}
				else {
                        $export[$id] = Array('nom' => $nom, 'prenom' => $prenom, 'classe' => $classe,
                            'total_demi_journees' => $total_demi_journees,
                            'total_demi_journees_justifiees' => $total_demi_journees_justifiees,
                            'total_demi_journees_non_justifiees' => $total_demi_journees_non_justifiees,
                            'retards' => $retards);
				}
                    }
                    $export[$id]['lignes'][] = Array('dates' => $dates,
                        'ligne_demi_journees_non_justifiees' => $ligne_demi_journees_non_justifiees,
                        'ligne_demi_journees_justifiees' => $ligne_demi_journees_justifiees,
                        'type' => $type,
                        'motif' => $motif,
                        'justification' => $justification,
                        'export_commentaire' => $export_commentaire);
                }
            }
        }
    }    
}
if(isset($_POST['export_avec_resp'])) {
	$extraction_bilans = repertoire_modeles('absence_extraction_bilan_resp.'.$extension);
}
else {
	$extraction_bilans = repertoire_modeles('absence_extraction_bilan.'.$extension);
}
$TBS = AbsencesNotificationHelper::MergeInfosEtab($extraction_bilans);
$titre = 'Bilan individuel du ' . $dt_date_absence_eleve_debut->format('d/m/Y') . ' au ' . $dt_date_absence_eleve_fin->format('d/m/Y');
$classe = null;
if ($id_classe != null && $id_classe != '' && $id_eleve == null) {
    $classe = ClasseQuery::create()->findOneById($id_classe);
    if ($classe != null) {
        $titre .= ' pour la classe ' . $classe->getNom();
    }
}
if ($nom_eleve != null && $nom_eleve != '') {
    $titre .= ' pour les élèves dont le nom ou le prénom contient ' . $nom_eleve;
}
if ($id_eleve != null && $id_eleve != '') {
    $eleve_current=  EleveQuery::create()->filterById($id_eleve)->findOne();
    $titre .= ' pour ' . $eleve_current->getPrenom() . ' ' . $eleve_current->getNom();
}
$TBS->MergeField('titre', $titre);
$TBS->MergeField('date_debut', $dt_date_absence_eleve_debut->format("d/m/Y"));
$TBS->MergeField('date_fin', $dt_date_absence_eleve_fin->format("d/m/Y"));
$TBS->MergeBlock('export', $export);
$TBS->MergeField('texte', $texte_conditionnel);
// Output as a download file (some automatic fields are merged here)
$nom_fichier = 'extrait_bilan_';
if ($classe != null) {
    $nom_fichier .= $classe->getNom() . '_';
}
$nom_fichier .= $dt_date_absence_eleve_fin->format("d_m_Y") . '.'.$extension ;
$TBS->Show(OPENTBS_DOWNLOAD + TBS_EXIT, $nom_fichier);
}
?>
	</div>
 
<?php
$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dojo.parser");
    dojo.require("dijit.form.Button");    
    dojo.require("dijit.form.Form");
    dojo.require("dijit.form.CheckBox");
    dojo.require("dijit.form.DateTextBox");    
    dojo.require("dijit.form.Select");
    dojo.require("dijit.form.NumberTextBox");
    dojo.require("dijit.form.TextBox");
    </script>';
require_once("../lib/footer.inc.php");
?>
