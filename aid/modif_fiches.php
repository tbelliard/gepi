<?php
/*
 * Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Régis Bouguin
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
$nb_max_cases = 3;
$non_defini = "<font color='red'>Non défini</font>";
require_once("../lib/initialisations.inc.php");

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

//debug_var();

include_once 'fonctions_aid.php';
global $mysqli;
$javascript_specifique = "aid/aid_ajax";

// Initialisation des variables
$indice_aid = \filter_input(\INPUT_POST, 'indice_aid') ? \filter_input(\INPUT_POST, 'indice_aid') : (\filter_input(\INPUT_GET, 'indice_aid') ? \filter_input(\INPUT_GET, 'indice_aid') : NULL);
$aid_id = \filter_input(\INPUT_POST, 'aid_id') ? \filter_input(\INPUT_POST, 'aid_id') : (\filter_input(\INPUT_GET, 'aid_id') ? \filter_input(\INPUT_GET, 'aid_id') : NULL);
$annee = \filter_input(\INPUT_POST, 'annee') ? \filter_input(\INPUT_POST, 'annee') : (\filter_input(\INPUT_GET, 'annee') ? \filter_input(\INPUT_GET, 'annee') : NULL);
$sous_groupe = \filter_input(\INPUT_POST, 'sous_groupe');
$parent = \filter_input(\INPUT_POST, 'parent');
$inscrit_direct = \filter_input(\INPUT_POST, 'inscrit_direct');
$eleve_s_inscrit = \filter_input(\INPUT_POST, 'eleve_s_inscrit');

// Vérification de la validité de $indice_aid et $aid_id
if (!VerifAidIsActive($indice_aid,$aid_id,$annee)) {
	echo $aid_id.' '.$indice_aid;
    echo "<p>Vous tentez d'accéder à des outils qui ne sont pas activés. veuillez contacter l'administrateur.</p></body></html>";
    die();
}

if ($_SESSION['statut'] === "eleve" && $eleve_s_inscrit) {
	Sauve_eleve_membre($aid_id, $indice_aid, $_SESSION['login']);
}

// Gestion du lien retour
$retour = \filter_input(\INPUT_GET, 'retour');
if ($retour != NULL) {
    if ($retour == '') {$_SESSION['retour']='';}
    else
    {$_SESSION['retour']= $retour."?indice_aid=".$indice_aid;}
    if ($retour=="annees_anterieures_accueil.php") {$_SESSION['retour'] .= "&amp;annee_scolaire=".$annee;}
    if ($retour=="index_fiches.php") {$_SESSION['retour'] .= "&amp;action=liste_projet";}
}
// Par défaut, on revient à index_fiches.php.
if (!$retour) {$retour = "index_fiches.php?indice_aid=".$indice_aid."&amp;action=liste_projet";}
// $action = isset($_POST["action"]) ? $_POST["action"] : (isset($_GET["action"]) ? $_GET["action"] : "visu");
$action = \filter_input(\INPUT_POST, 'action') ? \filter_input(\INPUT_POST, 'action') : (\filter_input(\INPUT_GET, 'action') ? \filter_input(\INPUT_GET, 'action') : "visu");
if ($annee=='') {
    $nom_projet = sql_query1("select nom from aid_config where indice_aid='".$indice_aid."'");
}
else {
    $nom_projet = sql_query1("select nom from  archivage_types_aid where id='".$indice_aid."' and annee='".$annee."'");
}
$call_productions = mysqli_query($GLOBALS["mysqli"], "select * from aid_productions");
$nb_productions = mysqli_num_rows($call_productions);
$call_public = mysqli_query($GLOBALS["mysqli"], "select * from aid_public order by ordre_affichage");
$nb_public = mysqli_num_rows($call_public );

// Si l'utilisateur n'est pas autorisé à modifier la fiche, on force $action = "visu"
if (!VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'','',$annee)) $action = "visu";

// Enregistrement des données
if (isset($_POST["is_posted"])) {
	check_token();

    // La personne connectée a-t-telle le droit d'enregistrer ?
    if (!VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'','',$annee)) {
        die();
	}
    $msg = "";
    if ($annee=='') {
		$sql_aid = "UPDATE aid SET ";
	} else {
        $sql_aid = "UPDATE archivage_aids SET ";
	}

    $met_virgule='n';
    // Résumé
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'resume','W',$annee)) and (isset($_POST["reg_resume"]))) {
      $reg_resume = isset($_POST["reg_resume"]) ? $_POST["reg_resume"] : NULL;
	  $resumeSurBulletin = filter_input(INPUT_POST, 'integreResume'); // ligne 740
	  saveResumeSurBulletin($resumeSurBulletin, $aid_id) ;
	  
      if (mb_strlen($reg_resume) > 600) {
        $reg_resume = mb_substr($reg_resume,0,597)."...";
        $msg .= "Erreur : Votre résumé excède 600 caractères.<br />";
      }
      if ($met_virgule=='y') {$sql_aid .=",";}
      $sql_aid .= "resume = '".$reg_resume."'";
      $met_virgule='y';
    }
    // Divers
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'divers','W',$annee)) and (isset($_POST["reg_divers"]))) {
      $reg_divers = isset($_POST["reg_divers"]) ? $_POST["reg_divers"] : NULL;
      if (mb_strlen($reg_divers) > 600) {
        if ($msg != "")  {$msg .= "<br />";}
        $msg .= "Erreur : le champs divers excède 600 caractères.<br />";
        $reg_divers = mb_substr($reg_divers,0,597)."...";
      }
      if ($met_virgule=='y') {$sql_aid .=",";}
      $sql_aid .= "divers = '".$reg_divers."'";
      $met_virgule='y';
    }
    // Contacts
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'contacts','W',$annee)) and (isset($_POST["reg_contacts"]))) {
      $reg_contacts = isset($_POST["reg_contacts"]) ? $_POST["reg_contacts"] : NULL;
      if (mb_strlen($reg_contacts) > 600) {
        if ($msg != "") {$msg .= "<br />";}
        $msg .= "Erreur : le champs \"Contacts Extérieurs\" excède 600 caractères.<br />";
        $reg_contacts = mb_substr($reg_contacts,0,597)."...";
      }
      if ($met_virgule=='y'){ $sql_aid .=",";}
      $sql_aid .= "contacts = '".$reg_contacts."'";
      $met_virgule='y';

    }
    // productions
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'productions','W',$annee)) {
      $reg_productions = "";
      $i = 0;
      $nb_cases = 0;
      while ($i < $nb_productions) {
        $temp = "p".$i;
        if (isset($_POST[$temp])) {
            $nb_cases++;
            if ($nb_cases <= $nb_max_cases){
                $reg_productions .= $_POST[$temp]."|";
			}
        }
        $i++;
      }
      if ($nb_cases > $nb_max_cases) {
        if ($msg != "") {$msg .= "<br />";}
        $msg .= "Erreur : vous devez cocher au maximum ".$nb_max_cases." items pour caractériser votre projet.";
      }
      if ($met_virgule=='y') {$sql_aid .=",";}
      $sql_aid .= "productions = '".$reg_productions."'";
      $met_virgule='y';
    }
    // public
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'public_destinataire','W',$annee)) {
      $reg_public = "";
      $i = 0;
      while ($i < $nb_public) {
        $temp = "public".$i;
        if (isset($_POST[$temp])) {
            $reg_public .= $_POST[$temp]."|";
        }
        $i++;
      }
      if ($met_virgule=='y') {$sql_aid .=",";}
      $sql_aid .= "public_destinataire = '".$reg_public."'";
      $met_virgule='y';
    }
    //Mots clés :
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'mots_cles','W',$annee)) {
      $reg_mots_cles = "";
      $k = 0;
      while ($k < 5) {
        $temp = "mc".$k;
        if ((isset($_POST[$temp])) and (trim($_POST[$temp]) != "")) {
            $reg_mots_cles .= $_POST[$temp]."|";
		}
        $k++;
      }
      if ($met_virgule=='y') {$sql_aid .=",";
		}
      $sql_aid .= "mots_cles = '".$reg_mots_cles."'";
      $met_virgule='y';
    }

    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'adresse1','W',$annee)) and (isset($_POST["reg_adresse1"]))) {
      if ($met_virgule=='y') {$sql_aid .=",";}
      $sql_aid .= "adresse1 = '".$_POST["reg_adresse1"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'adresse2','W',$annee)) and (isset($_POST["reg_adresse2"]))) {
      if ($met_virgule=='y') {$sql_aid .=",";}
      $sql_aid .= "adresse2 = '".$_POST["reg_adresse2"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'famille','W',$annee)) and (isset($_POST["reg_famille"]))) {
      if ($met_virgule=='y') {$sql_aid .=",";}
      $sql_aid .= "famille = '".$_POST["reg_famille"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'matiere1','W',$annee)) and (isset($_POST["reg_discipline1"]))) {
      if ($met_virgule=='y') {$sql_aid .=",";}
      $sql_aid .= "matiere1 = '".$_POST["reg_discipline1"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'matiere2','W',$annee)) and (isset($_POST["reg_discipline2"]))) {
      if ($met_virgule=='y') {$sql_aid .=",";}
      $sql_aid .= "matiere2 = '".$_POST["reg_discipline2"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso1','W',$annee)) and (isset($_POST["reg_perso1"]))) {
      if ($met_virgule=='y') {$sql_aid .=",";}
      $sql_aid .= "perso1 = '".$_POST["reg_perso1"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso2','W',$annee)) and (isset($_POST["reg_perso2"]))) {
      if ($met_virgule=='y') {$sql_aid .=",";}
      $sql_aid .= "perso2 = '".$_POST["reg_perso2"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso3','W',$annee)) and (isset($_POST["reg_perso3"]))) {
      if ($met_virgule=='y') {$sql_aid .=",";}
      $sql_aid .= "perso3 = '".$_POST["reg_perso3"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'numero','W',$annee)) and (isset($_POST["reg_num"]))) {
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "numero = '".$_POST["reg_num"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'nom','W',$annee)) and (isset($_POST["reg_nom"]))) {
      if ($annee=='') {
      //  On regarde si une aid porte déjà le même nom
        $test = sql_query1("SELECT count(id) FROM aid WHERE (nom='".$_POST["reg_nom"]."' and indice_aid='".$indice_aid."' and id!='".$aid_id."')");
        if ($test == "1") {
            if ($msg != "") $msg .= "<br />";
            $msg .= " Attention, une AID portant le même nom existait déja !";
        } else if ($test > 1) {
            if ($msg != "") $msg .= "<br />";
            $msg .= " Attention, plusieurs AID portant le même nom existaient déja !";
        }
      }
      if ($met_virgule=='y') $sql_aid .=",";
      $sql_aid .= "nom = '".$_POST["reg_nom"]."'";
      $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'eleve_peut_modifier','W',$annee)) and ($annee=='')) {
        if (isset($_POST["reg_eleve_peut_modifier"]))
            $reg_eleve_peut_modifier = "y";
        else
            $reg_eleve_peut_modifier = "n";
        if ($met_virgule=='y') $sql_aid .=",";
        $sql_aid .= "eleve_peut_modifier = '".$reg_eleve_peut_modifier."'";
        $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'prof_peut_modifier','W',$annee)) and ($annee=='')) {
        if (isset($_POST["reg_prof_peut_modifier"]))
            $reg_prof_peut_modifier = "y";
        else
            $reg_prof_peut_modifier = "n";
        if ($met_virgule=='y') $sql_aid .=",";
        $sql_aid .= "prof_peut_modifier = '".$reg_prof_peut_modifier."'";
        $met_virgule='y';
    }
    if ((VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'cpe_peut_modifier','W',$annee))  and ($annee=='')) {
        if (isset($_POST["reg_cpe_peut_modifier"]))
            $reg_cpe_peut_modifier = "y";
        else
            $reg_cpe_peut_modifier = "n";
        if ($met_virgule=='y') $sql_aid .=",";
        $sql_aid .= "cpe_peut_modifier = '".$reg_cpe_peut_modifier."'";
        $met_virgule='y';
    }
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'fiche_publique','W',$annee)) {
        if (isset($_POST["reg_fiche_publique"]))
            $reg_fiche_publique = "y";
        else
            $reg_fiche_publique = "n";
        if ($met_virgule=='y') $sql_aid .=",";
        $sql_aid .= "fiche_publique = '".$reg_fiche_publique."'";
        $met_virgule='y';
    }
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'affiche_adresse1','W',$annee)) {
        if (isset($_POST["reg_affiche_adresse1"])){
            $reg_affiche_adresse1 = "y";
		}
        else{
            $reg_affiche_adresse1 = "n";
		}
        if ($met_virgule=='y') {$sql_aid .=",";}
        $sql_aid .= "affiche_adresse1 = '".$reg_affiche_adresse1."'";
        $met_virgule='y';
    }
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'en_construction','W',$annee)) {
        if (isset($_POST["reg_en_construction"])) {
            $reg_en_construction = "y";
		}
        else {
            $reg_en_construction = "n";}
        if ($met_virgule=='y') {$sql_aid .=",";}
        $sql_aid .= "en_construction = '".$reg_en_construction."'";
        $met_virgule='y';
    }
	
	// Sous-groupe de
	if ($met_virgule=='y') {$sql_aid .=",";}
	if ($sous_groupe ==="y") {
		$sql_aid .= " sous_groupe = 'y' ";
		Sauve_sous_groupe ($aid_id, $parent);
	} else {
		$sql_aid .= " sous_groupe = 'n' ";
		Efface_sous_groupe($aid_id);
	}
    $met_virgule='y';
	
	// inscription directe
	if ($met_virgule=='y') {$sql_aid .=",";}
	if ($inscrit_direct ==="y") {
		$sql_aid .= " inscrit_direct = 'y' ";
	} else {
		$sql_aid .= " inscrit_direct = 'n' ";
	}
    $met_virgule='y';
	
    // Fin de la requête
    if ($annee=='') {
        $sql_aid .= " WHERE (id = '".$aid_id."' AND indice_aid='".$indice_aid."')";
    }
    else {
        $sql_aid .= " WHERE (id = '".$aid_id."' AND id_type_aid='".$indice_aid."' AND annee='".$annee."')";
    }
	
	//echo $sql_aid;

    $enr_aid = sql_query($sql_aid);
    if ($msg != "")  {$msg .= "<br />";}
    if (!$enr_aid) {
        $msg .= "Erreur lors de l'enregistrement !";
    }
    else {
        $msg .= "Enregistrement réussi !";
    }


}
// Appel de toutes les infos sur le projet
if ($annee=='') {
  $call_data_projet = mysqli_query($GLOBALS["mysqli"], "select * from aid where (id = '$aid_id' and indice_aid='$indice_aid')");
}
else {
  $call_data_projet = mysqli_query($GLOBALS["mysqli"], "select * from archivage_aids where (id = '$aid_id' and id_type_aid='$indice_aid' and annee='".$annee."')");
}

$aid_nom = old_mysql_result($call_data_projet, 0, "nom");
$reg_resume = old_mysql_result($call_data_projet,0,"resume");
$reg_contacts = old_mysql_result($call_data_projet,0,"contacts");
$reg_divers = old_mysql_result($call_data_projet,0,"divers");
$reg_famille = old_mysql_result($call_data_projet,0,"famille");
$reg_discipline1 = old_mysql_result($call_data_projet,0,"matiere1");
$reg_discipline2 = old_mysql_result($call_data_projet,0,"matiere2");
$reg_adresse1 = old_mysql_result($call_data_projet,0,"adresse1");
$reg_productions = old_mysql_result($call_data_projet,0,"productions");
$reg_public = old_mysql_result($call_data_projet,0,"public_destinataire");
$reg_mots_cles = old_mysql_result($call_data_projet,0,"mots_cles");
$reg_adresse2 = old_mysql_result($call_data_projet,0,"adresse2");
$reg_num = old_mysql_result($call_data_projet,0,"numero");
$reg_fiche_publique = old_mysql_result($call_data_projet,0,"fiche_publique");
$reg_affiche_adresse1 = old_mysql_result($call_data_projet,0,"affiche_adresse1");
$reg_en_construction = old_mysql_result($call_data_projet,0,"en_construction");
if ($annee=='') {
  $reg_perso1 = old_mysql_result($call_data_projet,0,"perso1");
  $reg_perso2 = old_mysql_result($call_data_projet,0,"perso2");
  $reg_perso3 = old_mysql_result($call_data_projet,0,"perso3");
  $reg_eleve_peut_modifier = old_mysql_result($call_data_projet,0,"eleve_peut_modifier");
  $reg_prof_peut_modifier = old_mysql_result($call_data_projet,0,"prof_peut_modifier");
  $reg_cpe_peut_modifier = old_mysql_result($call_data_projet,0,"cpe_peut_modifier");
} else {
  $eleves_resp = old_mysql_result($call_data_projet,0,"eleves_resp");
  $eleves = old_mysql_result($call_data_projet,0,"eleves");
  $responsables = old_mysql_result($call_data_projet,0,"responsables");
}

$style_specifique = "aid/style_fiche";

$sous_groupe_de = Extrait_info_parent ($aid_id) ? Extrait_info_parent ($aid_id)->fetch_object()->id : NULL;
//print_r($sous_groupe_de->fetch_object()->id);
$res_parents=Extrait_aid_sur_indice_aid ($indice_aid);

$themessage = 'Des modifications n ont pas été enregistrées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *********************
if ($action == "visu")
    $titre_page = "Visualisation d'une fiche projet ".$nom_projet;
else
    $titre_page = "Modification d'une fiche projet ".$nom_projet;
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

$NiveauGestionAid_categorie=NiveauGestionAid($_SESSION["login"],$indice_aid);
$NiveauGestionAid_AID_courant=NiveauGestionAid($_SESSION["login"],$indice_aid, $aid_id);

if ($action != "visu") {
?>
<script type="text/javascript">
//*** Paramètres
//*** texte : objet représentant le textarea
//*** max : nombre de caractères maximum
function CaracMax(texte, max)
{
if (texte.value.length >= max)
{
alert('Pas plus de ' + max + ' caractère(s) !!!') ;
texte.value = texte.value.substr(0, max - 1) ;
}
}

function compteur_coches(obj) {
max = <?php echo $nb_max_cases; ?>;
nombre = 0;
<?php
$k = 0;
while ($k < $nb_productions) {
    echo "if (document.forms[\"form\"].elements['p".$k."'].checked) nombre++;";
    $k++;
}
?>
if (nombre > max) {
alert("Vous ne pouvez pas cocher plus de " + max + " cases !");
obj.checked = false;
}
}

</script>
<?php } ?>
<p class=bold>
<?php
if ($_SESSION['retour']!='') { ?>
	<a href="<?php echo $_SESSION['retour']; ?>">
		<img src='../images/icons/back.png' alt='Retour' class='back_link'/>
		Retour
	</a>
<?php }
elseif(acces("/aid/index_fiches.php", $_SESSION['statut'])) {
	echo "<a href='index_fiches.php?indice_aid=$indice_aid'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}
else {
	echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}

if ($action == "visu") {
    if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,"","",$annee)) {
?>
	| <a href='modif_fiches.php?aid_id=<?php echo $aid_id; ?>&amp;indice_aid=<?php echo $indice_aid; ?>&amp;annee=<?php echo $annee; ?>&amp;action=modif'>
		Modifier la fiche
	</a>  
<?php }
} else {
?>
	| <a href='modif_fiches.php?aid_id=<?php echo $aid_id; ?>&amp;indice_aid=<?php echo $indice_aid; ?>&amp;annee=<?php echo $annee; ?>&amp;action=visu'>
		Visualiser la fiche
	</a>
<?php
} 

if($NiveauGestionAid_AID_courant>=1) {
	echo "
	| <a href='modify_aid.php?flag=eleve&aid_id=".$aid_id."&indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Élèves de l'AID</a>";
}
if($NiveauGestionAid_AID_courant>=2) {
	echo "
	| <a href='modify_aid.php?flag=prof&aid_id=".$aid_id."&indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Professeurs de l'AID</a>";
}
if($NiveauGestionAid_AID_courant>=5) {
	echo "
	| <a href='modify_aid.php?flag=prof_gest&aid_id=".$aid_id."&indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestionnaires de l'AID</a>";
}
if($NiveauGestionAid_categorie==10) {
	echo "
	| <a href='config_aid.php?indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Catégorie AID</a>";
}
?>
</p>
<!-- Nom du projet -->
<p class='grand'>
	projet <?php echo $nom_projet; ?> : <?php echo $aid_nom; ?>
</p>
<?php

// Début du formulaire
If ($action != "visu") { ?>
<form action="modif_fiches.php" name="form" method="post">
	<?php
	echo add_token_field();
} ?>

	<div class='bloc'>

<!-- Elèves responsables -->
<p class = 'bold'>
	Elèves responsables du projet :
<?php
if ($annee=='') {
  // appel de la liste des élèves de l'AID :
  $call_liste_data = mysqli_query($GLOBALS["mysqli"], "SELECT e.login, e.nom, e.prenom
  FROM eleves e, j_aid_eleves_resp j
  WHERE (j.id_aid='$aid_id' and e.login=j.login and j.indice_aid='$indice_aid')
  ORDER BY nom, prenom");
  $nombre = mysqli_num_rows($call_liste_data);
  $i = "0";
  while ($i < $nombre) {
    $login_eleve = old_mysql_result($call_liste_data, $i, "login");
    $nom_eleve = old_mysql_result($call_liste_data, $i, "nom");
    $prenom_eleve = old_mysql_result($call_liste_data, $i, "prenom");
    $call_classe = mysqli_query($GLOBALS["mysqli"], "SELECT c.classe FROM classes c, j_eleves_classes j WHERE (j.login = '$login_eleve' and j.id_classe = c.id) order by j.periode DESC");
    $classe_eleve = old_mysql_result($call_classe, '0', "classe");
    echo $nom_eleve." ".$prenom_eleve." (".$classe_eleve.")";
    if ($i < $nombre-1) {echo " - "; }
    $i++;
 }
} else {
   echo $eleves_resp;
} ?>
</p>
<!-- Professeurs responsables -->
<p class = 'bold' style="margin-bottom: 1em">
	Professeurs responsables du projet : 
<?php
if ($annee=='') {
  // appel de la liste des professeurs de l'AID :
  $call_liste_data = mysqli_query($GLOBALS["mysqli"], "SELECT e.login, e.nom, e.prenom
  FROM utilisateurs e, j_aid_utilisateurs j
  WHERE (j.id_aid='$aid_id' and e.login=j.id_utilisateur and j.indice_aid='$indice_aid')
  ORDER BY nom, prenom");
  $nombre = mysqli_num_rows($call_liste_data);
  $i = "0";
  while ($i < $nombre) {
    $login_eleve = old_mysql_result($call_liste_data, $i, "login");
    $nom_eleve = old_mysql_result($call_liste_data, $i, "nom");
    $prenom_eleve = old_mysql_result($call_liste_data, $i, "prenom");
    echo $nom_eleve." ".$prenom_eleve;
    if ($i < $nombre-1) {echo " - ";}
  $i++;
  }
} else {
  echo $responsables;
} ?>
</p>
<?php
if ($annee=='') {
 // perso1
 If ($action != "visu") {
  if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso1','W',$annee)) {
    echo "<p><span class = 'bold'>".LibelleChampAid("perso1")." : </span>\n";
    echo "<input type=\"text\" name=\"reg_perso1\" value=\"".htmlspecialchars($reg_perso1)."\" size=\"40\" onchange=\"changement()\" /></p>\n";
  }
 } else {
  if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso1','R',$annee)) {
    echo "<p><span class = 'bold'>".LibelleChampAid("perso1")." : </span>\n";
    echo htmlspecialchars($reg_perso1)."</p>\n";
  }
 }
 // perso2
 If ($action != "visu") {
  if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso2','W',$annee)) {
    echo "<p><span class = 'bold'>".LibelleChampAid("perso2")." : </span>\n";
    echo "<input type=\"text\" name=\"reg_perso2\" value=\"".htmlspecialchars($reg_perso2)."\" size=\"40\" onchange=\"changement()\" /></p>\n";
  }
 } else {
  if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso2','R',$annee)) {
    echo "<p><span class = 'bold'>".LibelleChampAid("perso2")." : </span>\n";
    echo htmlspecialchars($reg_perso2)."</p>\n";
  }
 }
 // perso3
 If ($action != "visu") {
  if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso3','W',$annee)) {
    echo "<p><span class = 'bold'>".LibelleChampAid("perso3")." : </span>\n";
    echo "<input type=\"text\" name=\"reg_perso3\" value=\"".htmlspecialchars($reg_perso3)."\" size=\"40\" onchange=\"changement()\" /></p>\n";
  }
 } else {
  if (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'perso3','R',$annee)) {
    echo "<p><span class = 'bold'>".LibelleChampAid("perso3")." : </span>\n";
    echo htmlspecialchars($reg_perso3)."</p>\n";
  }
 }
}
?>
	</div>	
<?php if (($_SESSION['statut'] != 'eleve') && ($_SESSION['statut'] != 'responsable')){ ?>
	<div class='bloc' <?php if (!Multiples_possible ($indice_aid)) {echo " style='display:none;'";} ?> >
		<p title="Cochez pour affecter un parent puis choisissez le parent">
			<label for="sous_groupe">
				Sous-groupe d'une autre AID
			</label>
			<input type="checkbox"
				   name='sous_groupe'
				   id='sous_groupe'
				   value="y"
					<?php if (a_parent($aid_id, $indice_aid)) {echo " checked='checked' ";} ?>  
				   onchange="afficher_cacher_parent();changement();"
				   />
		
		<span id="aidParent">
		
<?php if((Categorie_a_enfants ($indice_aid) != NULL) && Categorie_a_enfants ($indice_aid)->num_rows){ ?>
			<select name="parent" id="choix_parent" onchange="changement()">
				<option value="" 
						<?php if (!$sous_groupe_de) {echo " selected='selected' ";} ?>
						>
					Aucun parent

				<?php while ($parent = $res_parents->fetch_object()){ ?>
				<option value="<?php echo $parent->id; ?>" 
						<?php if ($parent->id == $sous_groupe_de) {echo " selected='selected' ";} ?>
						>
					<?php echo $parent->nom; ?>
				</option>
				<?php } ?>
			</select>
<?php } ?>
		
		</span>
		</p>
		<p>
			<label for="inscrit_direct">
				Un élève peut s'inscrire directement
			</label>
			<input type="checkbox"
				   name='inscrit_direct'
				   id='inscrit_direct'
				   value="y"
					<?php if (eleve_inscrit_direct($aid_id, $indice_aid)) {echo " checked='checked' ";} ?>  
				   onchange="afficher_cacher_parent();changement();"
				   />
		</p>
	</div>
<?php } elseif ($_SESSION['statut'] == 'eleve' 
   && Multiples_possible ($indice_aid) 
   && eleve_inscrit_direct($aid_id, $indice_aid)) { 
	$login = $_SESSION['login'] ?>
	<div class='bloc' >
		<form  action="modif_fiches.php" name="form" method="post">
			<p>
				<label for="eleve_s_inscrit">
					Je suis inscrit à cette activité
				</label>
				<input type="checkbox"
					   name='eleve_s_inscrit'
					   id='eleve_s_inscrit'
					   value="y"
						<?php if (Eleve_est_deja_membre ($login, $indice_aid, $aid_id)->num_rows) {echo " checked='checked' ";} ?>
					   onchange="changement()"
					   />
				<input type='hidden' name="indice_aid" value="<?php echo $indice_aid; ?>" />
				<input type='hidden' name="aid_id" value="<?php echo $aid_id; ?>" />
			</p>
			<p>
				<input type="submit" value="Enregistrer" />
			</p>
		</form>
	</div>
<?php
}
// Partie réservée à l'admin
if ($_SESSION["statut"]=="administrateur") {
    echo "<div class='bloc'>\n";
    If ($action != "visu") {
        //nom
        echo "<p>Nom : <input type=\"text\" name=\"reg_nom\" size=\"50\" value=\"".htmlspecialchars($aid_nom)."\" onchange=\"changement()\" /></p>\n";
        if ($annee=='') {
          //numero
          echo "<p>Numéro (fac.) : <input type=\"text\" name=\"reg_num\" size=\"4\" value=\"".$reg_num."\" onchange=\"changement()\" /></p>\n";
          //eleve_peut_modifier
          echo "<p><input type=\"checkbox\" name=\"reg_eleve_peut_modifier\" id=\"reg_eleve_peut_modifier\" value=\"y\" onchange=\"changement()\" ";
          if ($reg_eleve_peut_modifier == 'y') echo " checked ";
          echo "/> \n";
          echo "<label for='reg_eleve_peut_modifier'>Les élèves responsables peuvent modifier la fiche.</label></p>\n";
          //prof_peut_modifier
          echo "<p><input type=\"checkbox\" name=\"reg_prof_peut_modifier\" id=\"reg_prof_peut_modifier\" value=\"y\" onchange=\"changement()\" ";
          if ($reg_prof_peut_modifier == 'y') echo " checked ";
          echo "/> \n";
          echo "<label for='reg_prof_peut_modifier'>Les professeurs responsables peuvent modifier la fiche.</label></p>\n";
          //cpe_peut_modifier
          echo "<p><input type=\"checkbox\" name=\"reg_cpe_peut_modifier\" id=\"reg_cpe_peut_modifier\" value=\"y\" onchange=\"changement()\" ";
          if ($reg_cpe_peut_modifier == 'y') echo " checked ";
          echo "/> \n";
          echo "<label for='reg_cpe_peut_modifier'>Les CPE peuvent modifier la fiche.</label></p>\n";
        }
        //fiche_publique
        echo "<p><input type=\"checkbox\" name=\"reg_fiche_publique\" id=\"reg_fiche_publique\" value=\"y\" onchange=\"changement()\" ";
        if ($reg_fiche_publique == 'y') echo " checked ";
        echo "/> \n";
        echo "<label for='reg_fiche_publique'>La fiche est visible dans </label><a href=\"javascript:centrerpopup('../public/index_fiches.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">l'interface publique</a>.</p>\n";
        //affiche_adresse1
        echo "<p><input type=\"checkbox\" name=\"reg_affiche_adresse1\" id=\"reg_affiche_adresse1\" value=\"y\" onchange=\"changement()\" ";
        if ($reg_affiche_adresse1 == 'y') echo " checked ";
        echo "/> \n";
        echo "<label for='reg_affiche_adresse1'>L'adresse publique d'accès à la production est en lien sur la fiche publique.</label></p>\n";
        //en_construction
        echo "<p><input type=\"checkbox\" name=\"reg_en_construction\" id=\"reg_en_construction\" value=\"y\" onchange=\"changement()\" ";
        if ($reg_en_construction == 'y') echo " checked ";
        echo "/> \n";
        echo "<label for='reg_en_construction'>L'adresse publique est déclarée \"en construction\" sur la fiche publique.</label></p>\n";
    } else {
        echo "<ul>";
        if ($annee=='') {
          //eleve_peut_modifier
          if ($reg_eleve_peut_modifier == 'y')
              echo "<li>Les élèves responsables peuvent modifier la fiche.</li>\n";
          else
              echo "<li>Les élèves responsables ne peuvent pas modifier la fiche.</li>\n";
          //prof_peut_modifier
          if ($reg_prof_peut_modifier == 'y')
              echo "<li>Les professeurs responsables peuvent modifier la fiche.</li>\n";
          else
              echo "<li>Les professeurs responsables ne peuvent pas modifier la fiche.</li>\n";
          //cpe_peut_modifier
          if ($reg_cpe_peut_modifier == 'y')
              echo "<li>Les CPE peuvent modifier la fiche.</li>\n";
          else
              echo "<li>Les CPE ne peuvent pas modifier la fiche.</li>\n";
        }
        //fiche_publique
        if ($reg_fiche_publique == 'y')
            echo "<li>La fiche est visible dans <a href=\"javascript:centrerpopup('../public/index_fiches.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">l'interface publique</a>.</li>\n";
        else
            echo "<li>La fiche n'est pas visible dans <a href=\"javascript:centrerpopup('../public/index_fiches.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">l'interface publique</a>.</li>\n";
        //affiche_adresse1
        if ($reg_affiche_adresse1 == 'y')
            echo "<li>L'adresse publique d'accès à la production est en lien sur la fiche publique.</li>\n";
        else
            echo "<li>Il n'y a pas de lien sur la fiche publique vers l'adresse publique d'accès à la production.</li>\n";
        //en_construction
        if ($reg_en_construction == 'y')
            echo "<li>L'adresse publique est déclarée \"en construction\" sur la fiche publique.</li>\n";
        else
            echo "<li>L'adresse publique n'est pas déclarée \"en construction\" sur la fiche publique.</li>\n";
        echo "</ul>\n";

    }


    echo "</div>\n";
}

// résumé
$epiApParcours = estEpiApParcours($indice_aid);


if ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'resume',"W",$annee)) {
    echo "<div class='bloc'><span class = 'bold'>Résumé</span> (limité à 600 caractères) :\n";
    echo "<p><i>Présentation du projet, objectifs, réalisations, ....</i></p>\n";
    echo "<p><textarea name=\"reg_resume\" rows=\"6\" cols=\"100\" onKeyPress=\"CaracMax(this, 600)\" >".htmlspecialchars($reg_resume)."</textarea></p>\n";
	if ($epiApParcours) {
	?>
	<p>
		<label for="integreResume">Intégrer le résumé aux appréciations élève du bulletin</label> 
		<input type="checkbox" name="integreResume" id="integreResume" value="y" onchange="changement()" <?php if (aidEstAfficheBulletin($aid_id)) {echo " checked ='checked' ";} ?>/>
	</p>
	<?php
	}
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'resume',"R",$annee)) {
    echo "<div class='bloc'><span class = 'bold'>Résumé</span> (Présentation du projet, objectifs, réalisations, ....)\n";
    if ($reg_resume == "") {
        echo "<br />".$non_defini."\n";
    } else {
        echo "<br />".htmlspecialchars($reg_resume)."\n";
    } 
  }
}
echo "</div>\n";

// Famille
echo "<div class='bloc'>";
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'famille',"W",$annee)) {
    echo "<span class = 'bold'>Classez votre projet parmi la liste suivante (classification Dewey) : </span><br />\n";
    $call_famille = mysqli_query($GLOBALS["mysqli"], "select * from aid_familles order by ordre_affichage");
    $nb_famille = mysqli_num_rows($call_famille);
    echo "<select name=\"reg_famille\" size=\"1\" onchange=\"changement()\">\n";
    echo "<option value=\"\">(choisissez)</option>\n";
    $k = 0;
    while ($k < $nb_famille) {
        $id_famille = old_mysql_result($call_famille,$k,"id");
        $nom_famille = old_mysql_result($call_famille,$k,"type");
        echo "<option value=\"".$id_famille."\" ";
        if ($id_famille == $reg_famille) echo " selected ";
        echo ">".$nom_famille."</option>\n";
        $k++;
    }
    echo "</select>\n";
    echo "<br />N'hésitez à demander conseil au documentatiste.
    Pour vous aider, <a href=\"javascript:centrerpopup('100cases.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">un tableau détaillé est également disponible</a>.";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'famille',"R",$annee)) {
    $famille = sql_query1("select type from aid_familles where id = '".$reg_famille."'");
    if ($famille == -1) $famille = $non_defini;
    echo "<span class = 'bold'>Projet classé dans la famille</span> : ".$famille."\n";
  }
}
echo "</div>\n";

//Mots clés :
$mc = explode("|",$reg_mots_cles);
$k = 0;
while ($k < 5) {
    if ((!isset($mc[$k])) or (trim($mc[$k]) == "")) $mc[$k] = "";
    $k++;
}

echo "<div class='bloc'>\n";
If ($action != "visu")  {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'mots_cles',"W",$annee)) {
    echo "<span class = 'bold'>Mots clés </span> :\n";
    echo "<br /><i>Tapez entre 3 et 5 mots-cles</i>";
    echo "<table><tr>";
    $k = 0;
    while ($k < 5) {
        echo "<td><input type=\"text\" name=\"mc".$k."\" value=\"".htmlspecialchars($mc[$k])."\" size=\"15\" onchange=\"changement()\" /></td>\n";
        $k++;
    }
    echo "</tr></table>";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'mots_cles',"R",$annee)) {
    echo "<span class = 'bold'>Mots clés </span> :\n";
    $aff_motcle = "";
    $k = 0;
    while ($k < 5) {
        if ($mc[$k] != "") {
            if ($aff_motcle != "") $aff_motcle .= " - ";
            $aff_motcle .= $mc[$k];
        }
        $k++;
    }
    if ($aff_motcle == "")
        echo $non_defini;
    else
        echo $aff_motcle;
  }
}
echo "</div>\n";

//Production
$p = explode("|",$reg_productions);
$k = 0;
while ($k < $nb_productions) {
    if (!isset($p[$k]) or (trim($p[$k]) == "")) $p[$k] = "";
    $k++;
}

echo "<div class='bloc'>\n";
If ($action != "visu")  {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'productions',"W",$annee)) {
    echo "<p><span class = 'bold'>Production </span> :\n";
    echo "<br /><i>Cochez au maximum ".$nb_max_cases." items qui caractérisent au mieux votre projet.</i>";
    echo "<table border = 1>";
    $newligne = 1;
    $k = 0;
    while ($k < $nb_productions) {
        if ($newligne == 1) echo "<tr>";
        $id_productions = old_mysql_result($call_productions,$k,"id");
        $nom_productions = old_mysql_result($call_productions,$k,"nom");
        echo "<td><input type=\"checkbox\" name=\"p".$k."\" id=\"p".$k."\" value=\"".$id_productions."\" ";
        if (in_array($id_productions, $p))  echo " checked ";
        echo " onClick=\"compteur_coches(this)\" onchange=\"changement()\" /><label for='p".$k."'>";
        echo $nom_productions."</label></td>\n";
        $newligne++;
        if ($newligne == 5) {
            echo "</tr>";
            $newligne = 1;
        }
        $k++;
    }
    if ($newligne != 1) echo "</tr>";
    echo "</table>";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'productions',"R",$annee)) {
    echo "<p><span class = 'bold'>Production(s) attendue(s) </span> :\n";
    $k = 0;
    $liste = "";
    while ($k < $nb_productions) {
        $nom_productions = sql_query1("select nom from aid_productions where id = '".$p[$k]."'");
        if ($nom_productions != -1) {
            if ($liste != "") $liste .= " - ";
            $liste .= $nom_productions;
        }
        $k++;
    }
    if ($liste == "")
        echo $non_defini;
    else
        echo $liste;
  }
}
echo "</div>\n";

//Public
$public = explode("|",$reg_public);
$k = 0;
while ($k < $nb_public) {
    if (!isset($public[$k]) or (trim($public[$k]) == "")) $public[$k] = "";
    $k++;
}

echo "<div class='bloc'>\n";
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'public_destinataire',"W",$annee)) {
    echo "<p><span class = 'bold'>Public destinataire</span> :\n";
    echo "<br /><i>Cochez les items correspondant au(x) public(s) au(x)quel s'adresse le projet.</i>";
    echo "<table border = 1>";
    $newligne = 1;
    $k = 0;
    while ($k < $nb_public) {
        if ($newligne == 1) echo "<tr>";
        $id_public = old_mysql_result($call_public,$k,"id");
        $nom_public = old_mysql_result($call_public,$k,"public");
        echo "<td><input type=\"checkbox\" name=\"public".$k."\" id=\"public".$k."\" value=\"".$id_public."\" onchange=\"changement()\" ";
        if (in_array($id_public, $public))  echo " checked ";
        echo " /><label for='public".$k."'>";
        echo $nom_public."</label></td>\n";
        $newligne++;
        if ($newligne == 7) {
            echo "</tr>";
            $newligne = 1;
        }
        $k++;
    }
    if ($newligne != 1) echo "</tr>";
    echo "</table>";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'public_destinataire',"R",$annee)) {
    echo "<p><span class = 'bold'>Public destinataire</span> :\n";
    $k = 0;
    $liste = "";
    while ($k < $nb_public) {
        $nom_public = sql_query1("select public from aid_public where id = '".$public[$k]."'");
        if ($nom_public != -1) {
            if ($liste != "") $liste .= " - ";
            $liste .= $nom_public;
        }
        $k++;
    }
    if ($liste == "")
        echo $non_defini;
    else
        echo $liste;
  }
}
echo "</div>\n";


// Adresses publique :
echo "<div class='bloc'>\n";
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'adresse1',"W",$annee)) {
    echo "<span class = 'bold'>Indiquez éventuellement ci-dessous un <strong>lien public de type internet</strong> qui donne accès à la production :</span>\n";
    echo "<br /><input type=\"text\" name=\"reg_adresse1\" value=\"".htmlspecialchars($reg_adresse1)."\" size=\"50\" onchange=\"changement()\" />\n";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'adresse1',"R",$annee)) {
    echo "<span class = 'bold'>Lien public donnant accès à la production : </span>\n";
    if ($reg_adresse1 == "") echo $non_defini;
    else echo "<br />".$reg_adresse1;
  }
}
echo "</div>\n";

// Adresses à accès restreint :
echo "<div class='bloc'>\n";
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'adresse2',"W",$annee)) {
    echo "<span class = 'bold'>Indiquez ci-dessous un <b>lien à accès restreint</B>
    <br />(par exemple, <b>chemin d'accès à la production sur un serveur</b>) :</span>\n";
    echo "<br /><input type=\"text\" name=\"reg_adresse2\" value=\"".htmlspecialchars($reg_adresse2)."\" size=\"50\" onchange=\"changement()\" />\n";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'adresse2',"R",$annee)) {
    echo "<span class = 'bold'>Accès restreint à la production : </span>\n";
    if ($reg_adresse2 == "") echo $non_defini;
    else echo "<br />".$reg_adresse2;
  }
}
echo "</div>\n";

// Contacts
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'contacts',"W",$annee)) {
    echo "<div class='bloc'><span class = 'bold'>Contacts pris à l'extérieur de l'établissement, personnes ressources...</span> (limité à 600 caractères) :\n";
    echo "<br /><i>Liste des contacts extérieurs (nom, prénom, association, raison sociale, ... )</i>\n";
    echo "<br /><textarea name=\"reg_contacts\" rows=\"6\" cols=\"100\" onKeyPress=\"CaracMax(this, 600)\" >".htmlspecialchars($reg_contacts)."</textarea>\n";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'contacts',"R",$annee)) {
    echo "<div class='bloc'><span class = 'bold'>Liste des contacts extérieurs, personnes ressources...</span>\n";
    if ($reg_contacts == "")
        echo "<br />".$non_defini."\n";
    else
        echo "<br />".htmlspecialchars($reg_contacts)."\n";
  }
}
echo "</div>\n";


// Disciplines 1
echo "<div class='bloc'>";
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'matiere1',"W",$annee)) {
    echo "<span class = 'bold'>Indiquez la discipline principale à laquelle se rattache votre projet : </span><br />\n";
    $call_discipline = mysqli_query($GLOBALS["mysqli"], "select matiere, nom_complet from matieres where (matiere_aid='y') order by nom_complet");
    $nb_discipline = mysqli_num_rows($call_discipline);
    echo "<select name=\"reg_discipline1\" size=\"1\" onchange=\"changement()\">\n";
    echo "<option value=\"\">(choisissez)</option>\n";
    $k = 0;
    $discipline_reconnue=FALSE;
    while ($k < $nb_discipline) {
        $id_discipline = old_mysql_result($call_discipline,$k,"matiere");
        $nom_discipline = old_mysql_result($call_discipline,$k,"nom_complet");
        echo "<option value=\"".$id_discipline."\" ";
        if ($id_discipline == $reg_discipline1) {
            echo " selected ";
            $discipline_reconnue=TRUE;
        }
        echo ">".$nom_discipline."</option>\n";
        $k++;
    }
    if ((!$discipline_reconnue) and ($reg_discipline1!=''))
        echo "<option value=\"".$reg_discipline1."\" selected >Code discipline non reconnue : ".$reg_discipline1."</option>\n";
    echo "</select>\n";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'matiere1',"R",$annee)) {
    $discipline1 = sql_query1("select nom_complet from matieres where matiere = '".$reg_discipline1."'");
    if ($discipline1 == "-1") {
      if ($reg_discipline1=='')
        $discipline1 = $non_defini;
      else
        $discipline1 = $reg_discipline1;
    }

    echo "<span class = 'bold'>Discipline principale à laquelle se rattache le projet</span> : ".$discipline1."\n";
  }
}
echo "</div>\n";

// Disciplines 2
echo "<div class='bloc'>";
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'matiere2',"W,$annee")) {
    echo "<span class = 'bold'>Indiquez la discipline secondaire à laquelle se rattache votre projet : </span><br />\n";
    $call_discipline = mysqli_query($GLOBALS["mysqli"], "select matiere, nom_complet from matieres where (matiere_aid='y') order by nom_complet");
    $nb_discipline = mysqli_num_rows($call_discipline);
    echo "<select name=\"reg_discipline2\" size=\"1\" onchange=\"changement()\">\n";
    echo "<option value=\"\">(choisissez)</option>\n";
    $k = 0;
    $discipline_reconnue=FALSE;
    while ($k < $nb_discipline) {
        $id_discipline = old_mysql_result($call_discipline,$k,"matiere");
        $nom_discipline = old_mysql_result($call_discipline,$k,"nom_complet");
        echo "<option value=\"".$id_discipline."\" ";
        if ($id_discipline == $reg_discipline2) {
            echo " selected ";
            $discipline_reconnue=TRUE;
        }
        echo ">".$nom_discipline."</option>\n";
        $k++;
    }
    if ((!$discipline_reconnue) and ($reg_discipline2!=''))
        echo "<option value=\"".$reg_discipline2."\" selected >Code discipline non reconnue : ".$reg_discipline2."</option>\n";

    echo "</select>\n";

  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'matiere2',"R",$annee)) {
    $discipline2 = sql_query1("select nom_complet from matieres where matiere = '".$reg_discipline2."'");
    if ($discipline2 == "-1") {
      if ($reg_discipline2=='')
        $discipline2 = $non_defini;
      else
        $discipline2 = $reg_discipline2;
    }
    echo "<span class = 'bold'>Discipline secondaire à laquelle se rattache le projet</span> : ".$discipline2."\n";
  }
}
echo "</div>\n";


// divers
If ($action != "visu") {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'divers',"W",$annee)) {
    echo "<div class='bloc'><span class = 'bold'>Divers</span> (limité à 600 caractères) :\n";
    echo "<br /><textarea name=\"reg_divers\" rows=\"6\" cols=\"100\" onKeyPress=\"CaracMax(this, 600)\" >".htmlspecialchars($reg_divers)."</textarea>\n";
  }
} else {
  If (VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'divers',"R",$annee)) {
    echo "<div class='bloc'><span class = 'bold'>Divers</span>\n";
    if ($reg_divers == "")
        echo "<br />".$non_defini."\n";
    else
        echo "<br />".htmlspecialchars($reg_divers)."\n";
   }
}
echo "</div>\n";


if ($action != "visu") { ?>
	<p>
		<input type="hidden" name="indice_aid" value="<?php echo $indice_aid; ?>" />
		<input type="hidden" name="aid_id" value="<?php echo $aid_id; ?>" />
		<input type="hidden" name="action" value="<?php echo $action; ?>" />
		<input type="hidden" name="annee" value="<?php echo $annee; ?>" />
		<input type="hidden" name="is_posted" value="yes" />
	</p>
	<div class="center" id = "fixe">
		<p>
			<input type="submit" value="Enregistrer" />
		</p>
	</div>
</form>


<script type='text/javascript'>
	afficher_cacher_parent();
</script>
<?php }
include "../lib/footer.inc.php";
?>
