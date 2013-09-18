<?php
/*
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


include_once('./lib/lib_mod_ooo.php'); //les fonctions
$nom_fichier_modele_ooo =''; //variable à initialiser à blanc pour inclure le fichier suivant et éviter une notice. Pour les autres inclusions, cela est inutile.
include_once('./lib/chemin.inc.php'); // le chemin des dossiers contenant les  modèles

//Liste des fichiers à compléter à la main (3 données par fichier)
    // L'entête de la section pour le 1er fichier de la section sinon "" (vide)
    //Le nom du fichier en minuscule avec son extension
	//La description du document

if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OOoUploadCpeDiscipline')))||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OOoUploadScolDiscipline')))) {
    //Retenue
    $lien_wiki[]='';
    $entete_section[]="MODULE DISCIPLINE";
	$fich[]="retenue.odt";
    $utilisation[]="Formulaire de retenue";
	$special[]="";

    //rapport incident
    $lien_wiki[]='';
    $entete_section[]="";
	$fich[]="rapport_incident.odt";
    $utilisation[]="Formulaire de rapport d'incident";
	$special[]="";

	//Exclusion temporaire
    $lien_wiki[]='';
    $entete_section[]="";
	$fich[]="discipline_exclusion.odt";
    $utilisation[]="Exclusion temporaire de l'établissement";
	$special[]="";

	// Travail
    $lien_wiki[]='';
    $entete_section[]="";
	$fich[]="discipline_travail.odt";
    $utilisation[]="Travail à rendre";
	$special[]="";

	// Autre sanction
    $lien_wiki[]='';
    $entete_section[]="";
	$fich[]="discipline_autre.odt";
    $utilisation[]="Autre sanction";
	$special[]="";
}

if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OOoUploadCpeAbs2')))||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OOoUploadScolAbs2')))) {
    //modèle ABS2
    $lien_wiki[]='';
	$entete_section[]="MODULE ABSENCE";
    $fich[]="absence_extraction_demi-journees.ods";
    $utilisation[]="ABS2 : Tableau des demi-journées d'absences";
	$special[]="";
	
    $lien_wiki[]='';
	$entete_section[]="";
    $fich[]="absence_extraction_saisies.ods";
    $utilisation[]="ABS2 : Tableau des saisies d'absences";
	$special[]="";
	
    $lien_wiki[]='';
	$entete_section[]="";
    $fich[]="absence_extraction_traitements.ods";
    $utilisation[]="ABS2 : Tableau des traitements d'absences";
	$special[]="";
    
    $lien_wiki[]='';
    $entete_section[]="";
    $fich[]="absence_taux_absenteisme.ods";
    $utilisation[]="ABS2 : Tableau des taux d'absentéisme";
	$special[]="";

    $lien_wiki[]='';
    $entete_section[]="";
    $fich[]="absence_extraction_bilan.ods";
    $utilisation[]="ABS2 : Tableau bilan par jour par élève au format tableur";
	$special[]="";

    $lien_wiki[]='';
    $entete_section[]="";
    $fich[]="absence_extraction_bilan.odt";
    $utilisation[]="ABS2 : Tableau bilan par jour par élève au format traitement de textes";
	$special[]="";

    $lien_wiki[]='';
	$entete_section[]="";
    $fich[]="absence_modele_lettre_parents.odt";
    $utilisation[]="ABS2 : Modèle de lettre aux parents";
	$special[]="";
	
    $lien_wiki[]='';
	$entete_section[]="";
    $fich[]="absence_email.txt";
    $utilisation[]="ABS2 : Modèle du courriel envoyé aux parents";
	$special[]="";
	
    $lien_wiki[]='';
	$entete_section[]="";
    $fich[]="absence_sms.txt";
    $utilisation[]="ABS2 : Modèle de SMS envoyé aux parents";
	$special[]="";
}

if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OOoUploadCpeNotanet')))||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OOoUploadScolNotanet')))) {
    //Fiches brevet
    $lien_wiki[]='http://www.sylogix.org/projects/gepi/wiki/GepiDoc_fbOooCalc#Gabarits-de-fiches-brevets-dautres-acad%C3%A9mies';
	$entete_section[]="MODULE NOTANET";
    $fich[]="fb_serie_generale.ods";
    $utilisation[]="Fiche brevet série générale";
	$special[]="";

    $lien_wiki[]='';
	$entete_section[]="";
    $fich[]="fb_CLG_lv2.ods";
    $utilisation[]="Fiche brevet série collège LV2";
	$special[]="obsolete";
	
    $lien_wiki[]='';
	$entete_section[]="";
    $fich[]="fb_CLG_dp6.ods";
    $utilisation[]="Fiche brevet série collège ODP 6 heures";
	$special[]="obsolete";

    $lien_wiki[]='';
	$entete_section[]="";
    $fich[]="fb_PRO.ods";
    //$utilisation[]="Fiche brevet série professionnelle sans ODP";
    $utilisation[]="Fiche brevet série professionnelle";
	$special[]="";

    $lien_wiki[]='';
	$entete_section[]="";
    $fich[]="fb_PRO_dp6.ods";
    $utilisation[]="Fiche brevet série professionnelle ODP 6 heures";
	$special[]="obsolete";
	
    $lien_wiki[]='';
	$entete_section[]="";
    $fich[]="fb_PRO_agri.ods";
    $utilisation[]="Fiche brevet série professionnelle option agricole";
	$special[]="";

    $lien_wiki[]='';
	$entete_section[]="";
    $fich[]="fb_TECHNO.ods";
    $utilisation[]="Fiche brevet série technologique sans ODP";
	$special[]="obsolete";
	
    $lien_wiki[]='';
	$entete_section[]="";
    $fich[]="fb_TECHNO_dp6.ods";
    $utilisation[]="Fiche brevet série technologique ODP 6 heures";
	$special[]="obsolete";
	
    $lien_wiki[]='';
	$entete_section[]="";
    $fich[]="fb_TECHNO_agri.ods";
    $utilisation[]="Fiche brevet série technologique option agricole";
	$special[]="obsolete";
}

if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OOoUploadCpeEcts')))||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OOoUploadScolEcts')))) {
    // Module ECTS
    $lien_wiki[]='';
	$entete_section[]="MODULE ECTS";
    $fich[]="documents_ects.odt";
    $utilisation[]="Documents ECTS (pour BTS, prépas...)";
	$special[]="";
}

    $nbfich=sizeof($fich);
// Fin liste des fichiers

$PHP_SELF=basename($_SERVER['PHP_SELF']);
creertousrep($nom_dossier_modeles_ooo_mes_modeles.$rne);

$retour_apres_upload=isset($_POST['retour_apres_upload']) ? $_POST['retour_apres_upload'] : (isset($_GET['retour_apres_upload']) ? $_GET['retour_apres_upload'] : NULL);
if(!isset($retour_apres_upload)) {
	$retour=$_SESSION['retour'];
	$_SESSION['retour']=$_SERVER['PHP_SELF'] ;
}
else {
	$retour="../accueil.php";
}

//**************** EN-TETE *****************
$titre_page = "Modèle Open Office - gérer ses modèles";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
echo "<script language=\"Javascript\" src=\"./lib/mod_ooo.js\"> </script>";
//debug_var();

if (isset($_GET['op'])) { $op=$_GET["op"]; }
if (isset($_GET['fic'])) { $fic=$_GET["fic"]; }
if (isset($_POST['btn'])) { $btn=$_POST["btn"]; }
if (isset($_POST['fich_cible'])) { $fich_cible=$_POST["fich_cible"]; }

echo "<p class='bold'><a href='".$retour;
if(isset($btn)) {echo "?retour_apres_upload=y";}
echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";
echo "<br />\n";
echo "<p>Ce module est destiné à gérer les modèles Open Office de Gepi.</p>\n";
echo "</p>\n";
echo "<br />\n";

if ((isset($op)) && ($op=="supp")) { //Supprimer un fichier perso
     // alert("EFFACER $fic");
	  @unlink($nom_dossier_modeles_ooo_mes_modeles.$rne.$fic);
}

echo "<body>";


if (!isset($btn)) { //premier passage : formulaire
    echo "<p >Un modèle personnalisé, envoyé sur le serveur sera utilisé par Gepi</p><hr>\n";
    echo "<p >Peu importe le nom actuel (gardez le format Open Office : ODT - texte, ODS - tableur ou txt - texte), chaque fichier sera renommé correctement.<br />\n";
    echo "Les fichiers personnalisés peuvent être supprimés (icône poubelle), contrairement à ceux par défaut.<br />\n";
	echo "L'ensemble des fichiers peut être consulté en cliquant sur leur icône.</p><br />\n";
	echo "Lorsque vous créez un nouveau modèle, bien faire attention à la syntaxe des variables utilisées dans le modèle par défaut.</p><br />\n";
    echo "Elles sont sensibles à la case. Le format d'une variable est [var.xxxxx]</p><br /><br />\n";
    echo "<p><u>Cas particulier du modèle de lettre aux parents pour le module absence 2 : </u><br />\n";
    echo "Une modification trop importante de ce modèle peut entraîner des dysfonctionnements ou des problèmes de mise en page avec la fonctionnalité d'impression par lot des courriers.<br />\n";
    echo "C'est pourquoi il est recommandé, dans ce cas là, de se limiter a des modifications simple (nature du texte par exemple) du modèle de base proposé dans Gepi.<br /><br />\n";
    echo "Dans les modèles du module abs2 la chaîne [saisies_string_eleve_id_[el_col.getIdEleve] est remplacée par [saisies_string_eleve_id_[el_col.getId] dans les fichiers absence_email.txt, absence_sms.txt et absence_modele_lettre_parent.odt. Les <b>modèles personnalisés doivent êtres modifiés</b> en conséquence.<br />\n";
    echo "Les modèles personnalisés <b>absence_email.txt et absence_sms.txt</b> doivent être en UTF-8</p><br /><br />\n";
    //Tableau des différents fichiers à envoyer
    echo "<table class='boireaus' align='center'>\n";
    echo "<tr>\n";
    echo "<td>Modèle initial<br/>Visualiser</td>\n";
    echo "<td>Modèle personnel <br/>Supprimer / Visualiser</td>\n";
    echo "<td>Nom du fichier</td>\n";
    echo "<td>Description du fichier</td>\n";
    echo "<td>Choisir le fichier <br/>à télécharger</td>\n";
    echo "<td>Action</td>\n";
    echo "</tr>\n";
	$alt=1;
    for ($i=0;$i<$nbfich;$i++) {
	  $alt=$alt*(-1);
      //Une ligne du tableau
      //paire ou impaire	  
	  if ($entete_section[$i] != "") { // Cas d'un entête
	      echo "<tr>";
	      echo "<td colspan=\"6\"></br></br><b>$entete_section[$i]";
	      echo "<a name='".remplace_accents($entete_section[$i], "all")."'></a>";
	      if($lien_wiki[$i] != "") {echo " <a href='".$lien_wiki[$i]."' target='_blank'><img src='../images/icons/ico_ampoule.png' width='15' height='25' title='Documentation/ressources' /></a>";}
	      echo "</br></br></b></br></br></td>";
		  echo "</tr>";
	  }
	  if($special[$i]!='obsolete') {
		echo "<tr class='lig$alt'>\n<form name=\"form$i\" method='post' ENCTYPE='multipart/form-data' action='$PHP_SELF' onsubmit=\"return bonfich('$i')\" >\n";
	  }
	  else {
		echo "<tr style='background-color:grey;' title=\"Modèle obsolète\">\n<form name=\"form$i\" method='post' ENCTYPE='multipart/form-data' action='$PHP_SELF' onsubmit=\"return bonfich('$i')\" >\n";
	  }
	echo add_token_field();
	  echo "<input type=\"hidden\" name=fich_cible value=$fich[$i] >\n";
		 $type_ext = renvoi_nom_image(extension_nom_fichier($fich[$i]));
		 echo "<td align='center'>\n<a href=\"$nom_dossier_modeles_ooo_par_defaut$fich[$i]\"><img src=\"./images/$type_ext\" border=\"0\" title=\"Consulter le modèle par défaut\"></a>\n";
		 echo "</td>\n";
	  if  (file_exists($nom_dossier_modeles_ooo_mes_modeles.$rne.$fich[$i]))   {
		 echo "<td align='center'><a href=\"$PHP_SELF?op=supp&fic=$fich[$i]".add_token_in_url()."\" onclick='return confirmer()'><img src=\"./images/poubelle.gif\" border=\"0\" title=\"ATTENTION, suppression immédiate !\"></a>\n";
		 echo "&nbsp;&nbsp;<a HREF='".$nom_dossier_modeles_ooo_mes_modeles.$rne.$fich[$i]."'><img src=\"./images/$type_ext\" border=\"0\" title=\"Consulter le nouveau modèle\"></a>\n";
		 echo "</td>\n";
	  } else {
		 echo "</td>\n<td>&nbsp;</td>\n";
	  }

	  echo "<td>$fich[$i]</td>\n<td>\n";
	  echo "$utilisation[$i]</td>\n<td>\n";
	  echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"512000\">";
	  echo "<input type='file' name='monfichier' value='il a cliqué le bougre'>&nbsp;</td><td>\n";
	  echo "&nbsp;&nbsp;<input type='submit' name='btn' Align='middle' value='Envoyer' />&nbsp;&nbsp;  \n";
	  echo "</td>\n</form>\n";
	  echo "</tr>\n";
    }
    echo "</table>\n";

}
else { // passage 2 : le nom du fichier a été choisi
    //print_r($_FILES['monfichier']);
	echo "<h2>fichier envoyé : ".$_FILES['monfichier']['name']." </h2>\n";
	check_token();
    $desterreur=$PHP_SELF;
    $dest=$desterreur;
    //alert($dest);

    //Récup du fichier téléchargé
    $t=$_FILES['monfichier'];
    //print_r($t);

    $monfichiername=$t['name'];
    $monfichiertype=$t['type'];
    $monfichiersize=$t['size'];
    $monfichiertmp_name=$t['tmp_name'];

    if ($monfichiername=="") {
       alert ("Pas de fichier indiqué ! Il faut recommencer...");
       $dest=$desterreur;
       echo "<script language='JavaScript'>\n";
       echo "<!-- \n";
       echo "w=window.open('','mafenetre');\n"; //récupérer le même objet fenêtre
       echo "w.document.writeln('<h3>Fermeture en cours...</h3>');\n";
       echo "// - JavaScript - -->\n";
       echo "</script>\n";
       aller_a($dest);
    }
    else {
        echo "<script language='JavaScript'>\n";
        echo "<!-- \n";
        echo "w=window.open('','mafenetre');\n"; //récupérer le même objet fenêtre
        echo "w.document.writeln('<h3>copie en cours...</h3>');\n";
        echo "// - JavaScript - -->\n";
        echo "</script>\n";


        $fichiercopie=mb_strtolower($monfichiername);
        //alert("fichier copié : ".$fichiercopie);

        $cible=$nom_dossier_modeles_ooo_mes_modeles.$rne.$fich_cible;
        //alert("avant la copie".$cible);
        if (!move_uploaded_file($monfichiertmp_name,$cible)) {
            echo "Erreur de copie<br />\n";
            echo "origine     : $monfichiername <br />\n";

            echo "destination : ".$nom_dossier_modeles_ooo_mes_modeles.$rne.$fichiercopie;
            $me="La copie ne s'est pas effectuée !\n Vérifiez la taille du fichier (max 512ko)\n";
            alert($me);
            $dest=$desterreur;
        }
        else {
            //echo "<p>$cible a été copié</p>";
            $dest.="?fichier=$cible";
            echo($fich_cible." a été copié correctement dans : ".$nom_dossier_modeles_ooo_mes_modeles.$rne.$fichiercopie."<br />");
            echo "<p align='center'>";
            unset($monfichiername);
            echo "<form name='retour' method='POST' action='$PHP_SELF'>\n";
            echo "<input type='hidden' name='retour_apres_upload' value='y' />\n";
            echo "<input type='submit' name='ret' Align='middle' value='Retour' />\n";
            echo "</form>\n";

            }
        } //fin de monfichier != ""
        echo "<script language='JavaScript'>\n";
        echo "<!-- JavaScript\n";
        echo "w.close()\n";
        echo "// - JavaScript - -->\n";
        echo "</script>\n";

}
?>
</body>
</html>
