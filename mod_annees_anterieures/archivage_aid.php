<?php
/*
 * $Id : $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
require_once("./fonctions_annees_anterieures.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$id_type=isset($_POST['id_type']) ? $_POST['id_type'] : NULL;
$annee_scolaire=isset($_POST['annee_scolaire']) ? $_POST['annee_scolaire'] : (isset($_GET['annee_scolaire']) ? $_GET['annee_scolaire'] : NULL);
$confirmer=isset($_POST['confirmer']) ? $_POST['confirmer'] : NULL;
$deja_traitee_id_type=isset($_POST['deja_traitee_id_type']) ? $_POST['deja_traitee_id_type'] : NULL;
$log_error=isset($_POST['log_error']) ? $_POST['log_error'] : "n";
// Si le module n'est pas activé...
if(getSettingValue('active_annees_anterieures')!="y"){
	header("Location: ../logout.php?auto=1");
	die();
}

// si le plugin "port_folio" existe et est activé
$test_plugin = sql_query1("select ouvert from plugins where nom='port_folio'");
if ($test_plugin=='y') $flag_port_folio='y';


$msg="";

$style_specifique="mod_annees_anterieures/annees_anterieures";
// Suppression des données archivées pour une année donnée.
if (isset($_GET['action']) and ($_GET['action']=="supp_annee")) {
  	// Suppression des liens élèves/aid
    $sql="SELECT id FROM archivage_aids WHERE annee='".$_GET['annee_supp']."'";
    $res=sql_query($sql);
    $nb_lignes = mysql_num_rows($res);
    $k=0;
    while($k < $nb_lignes) {
      $id = mysql_result($res,$k,"id");
      $res_supp=mysql_query("DELETE FROM archivage_aid_eleve WHERE id_aid='".$id."';");
      $k++;
    }
    $sql="DELETE FROM archivage_appreciations_aid WHERE annee='".$_GET["annee_supp"]."';";
		$res_suppr2=mysql_query($sql);

    $sql="DELETE FROM archivage_aids WHERE annee='".$_GET["annee_supp"]."';";
		$res_suppr1=mysql_query($sql);

    $sql="DELETE FROM archivage_types_aid WHERE annee='".$_GET["annee_supp"]."';";
		$res_suppr3=mysql_query($sql);

    if (isset($flag_port_folio)) {
      $sql="DELETE FROM port_folio_validations_archives  WHERE annee='".$_GET["annee_supp"]."';";
  		mysql_query($sql);
    }

		// Maintenant, on regarde si l'année est encore utilisée dans archivage_disciplines
		// Sinon, on supprime les entrées correspondantes à l'année dans archivage_eleves2 car elles ne servent plus à rien.
		$test = sql_query1("select count(annee) from archivage_disciplines where annee='".$_GET['annee_supp']."'");
		if ($test == 0) {
      $sql="DELETE FROM archivage_eleves2 WHERE annee='".$_GET["annee_supp"]."';";
	  	$res_suppr4=mysql_query($sql);
		} else $res_suppr4 = 1;

    // Maintenant, il faut supprimer les données élèves qui ne servent plus à rien
    suppression_donnees_eleves_inutiles();

		if (($res_suppr1) and ($res_suppr2) and ($res_suppr3) and ($res_suppr4)) {
			$msg = "La suppression des données a été correctement effectuée.";
		} else {
			$msg = "Un ou plusieurs problèmes ont été rencontrés lors de la suppression.";
		}

}
// Suppression des données archivées pour une année donnée.
if (isset($_GET['action']) and ($_GET['action']=="supp_AID")) {
  	// Suppression des liens élèves/aid
    $sql="SELECT id FROM archivage_aids WHERE annee='".$_GET['annee_supp']."' and id_type_aid='".$_GET['type_aid_supp']."'";
    $res=sql_query($sql);
    $nb_lignes = mysql_num_rows($res);
    $k=0;
    while($k < $nb_lignes) {
      $id = mysql_result($res,$k,"id");
      $res_supp1=mysql_query("DELETE FROM archivage_aid_eleve WHERE id_aid='".$id."';");
   		$res_supp2=mysql_query("DELETE FROM archivage_appreciations_aid WHERE annee='".$_GET["annee_supp"]."' and id_aid='".$id."'");
      $k++;
    }


    $sql="DELETE FROM archivage_aids WHERE annee='".$_GET["annee_supp"]."' and id_type_aid='".$_GET['type_aid_supp']."'";
		$res_suppr1=mysql_query($sql);

    $sql="DELETE FROM archivage_types_aid WHERE annee='".$_GET["annee_supp"]."' and id='".$_GET['type_aid_supp']."'";
		$res_suppr2=mysql_query($sql);

    // Maintenant, il faut supprimer les données élèves qui ne servent plus à rien
    suppression_donnees_eleves_inutiles();

		if (($res_suppr1) and ($res_suppr2)) {
			$msg = "La suppression des données a été correctement effectuée.";
		} else {
			$msg = "Un ou plusieurs problèmes ont été rencontrés lors de la suppression.";
		}

}


$themessage  = 'Etes-vous sûr de vouloir supprimer toutes les données AID concernant cette année ?';
$themessage2  = 'Etes-vous sûr de vouloir supprimer toutes les données pour cette AID ?';
//**************** EN-TETE *****************
$titre_page = "Archivage des AIDs";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

if(!isset($annee_scolaire)){
	echo "<div class='norme'><p class=bold><a href='./index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";
	echo "</p></div>\n";


	$sql="SELECT DISTINCT annee FROM archivage_types_aid ORDER BY annee";
	$res_annee=sql_query($sql);
	if(mysql_num_rows($res_annee)==0){
		echo "<p>Aucune année n'est encore sauvegardée.</p>\n";
	}
	else{
		echo "<p>Voici la liste des années sauvegardées :</p>\n";
		echo "<ul>\n";
		while($lig_annee=mysql_fetch_object($res_annee)){
			$annee_scolaire=$lig_annee->annee;
			echo "<li><b>Année $annee_scolaire</b> - <a href='".$_SERVER['PHP_SELF']."?action=supp_annee&amp;annee_supp=".$annee_scolaire."'   onclick=\"return confirm_abandon (this, 'yes', '$themessage')\">Supprimer toutes les données AIDs archivées pour cette année</a></li>\n";
		}
		echo "</ul>\n";
		echo "<p><br /></p>\n";

	}
	echo "<p>Sous quel nom d'année voulez-vous sauvegarder l'année?</p>\n";
	$default_annee=getSettingValue('gepiYear');

	if($default_annee==""){
		$instant=getdate();
		$annee=$instant['year'];
		$mois=$instant['mon'];

		$annee2=$annee+1;
		$default_annee=$annee."-".$annee2;
	}

	echo "<p>Année: <input type='text' name='annee_scolaire' value='$default_annee' /></p>\n";

	echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";
} else {
	echo "<div class='norme'><p class=bold><a href='archivage_aid.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";

	$sql="SELECT nom,nom_complet, id FROM archivage_types_aid WHERE annee='$annee_scolaire'";
	$res_test=sql_query($sql);

	if(mysql_num_rows($res_test)>0){
    if(!isset($confirmer)){
			echo "</p></div>\n";
			$chaine_types_aid='<ul>';
			while($lig_types_aid=mysql_fetch_object($res_test)){
					$chaine_types_aid.="<li> ".$lig_types_aid->nom." (".$lig_types_aid->nom_complet.")
          - <a href='".$_SERVER['PHP_SELF']."?action=supp_AID&amp;annee_scolaire=".$annee_scolaire."&amp;annee_supp=".$annee_scolaire."&amp;type_aid_supp=".$lig_types_aid->id."'   onclick=\"return confirm_abandon (this, 'yes', '$themessage2')\">Supprimer toutes les données archivées de cette AID</a></li>";
			}

			echo "<p>Des données ont déjà été sauvegardées pour l'année $annee_scolaire. Liste des types d'AIDs sauvegardés :<br /> $chaine_types_aid</ul><p>
      <b>ATTENTION :</b> si, à l'étape suivante, vous cochez des types d'AIDs déjà archivés, les données seront archivées une nouvelle fois et s'ajouteront aux autres données déjà archivées. S'il s'agit bien des mêmes types d'AIDS, vous risquez alors de créer des doublons.</p>\n";
 			echo "<p>Vous pouvez également procéder à la suppression d'AIDs archivées</p>";

			echo "<input type='hidden' name='annee_scolaire' value='$annee_scolaire' />\n";

			echo "<center><input type=\"submit\" name='confirmer' value=\"Continuer\" style=\"font-variant: small-caps;\" /></center>\n";
			echo "</form>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}

	if(!isset($id_type)){
		echo "</p></div>\n";

		echo "<h2>Choix des types d'AIDs</h2>\n";

		echo "<p>Conservation des données pour l'année scolaire: $annee_scolaire</p>\n";

		echo "<p>Choisissez les types d'AIDs que vous souhaitez archiver</p>";
		echo "<p>Tout <a href='javascript:modif_coche(true)'>cocher</a> / <a href='javascript:modif_coche(false)'>décocher</a>.</p>";

		// Afficher les types pour lesquelles les données sont déjà migrées...

		$sql="SELECT indice_aid,nom,outils_complementaires,nom_complet FROM aid_config ORDER BY nom";
		$res1=sql_query($sql);
		$nb_types=mysql_num_rows($res1);
		if($nb_types==0){
			echo "<p>ERREUR: Il semble qu'aucun type d'AID ne soit encore défini.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		// Affichage sur 3 colonnes
		$nb_types_par_colonne=round($nb_types/2);

		echo "<table width='100%' border='0'>\n";
		echo "<tr valign='top' align='center'>\n";

		$i = 0;

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while ($i < $nb_types) {

			if(($i>0)&&(round($i/$nb_types_par_colonne)==$i/$nb_types_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}

			$lig_type=mysql_fetch_object($res1);

			echo "<input type='checkbox' id='type".$i."' name='id_type[]' value='$lig_type->indice_aid' /> $lig_type->nom ($lig_type->nom_complet)";
      if ($lig_type->outils_complementaires=='y') echo " *";
      echo "<br />\n";

			$i++;
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<p><input type = \"checkbox\" name=\"log_error\" value=\"y\" ";
		if ($log_error == 'y') echo " cheched ";
    echo "/>Les résultats de l'archivage sont en général très longs. Cochez la case ci-contre si vous souhaitez n'afficher que les erreurs d'archivage.";
    echo "<p class='small'>(*) Types d'AID pour lesquels les outils complémentaire liés aux fiches projets ont été activés.</p>";

		echo "<script type='text/javascript'>
			function modif_coche(statut){
				for(k=0;k<$i;k++){
					if(document.getElementById('type'+k)){
						document.getElementById('type'+k).checked=statut;
					}
				}
				//changement();
			}
		</script>\n";

		echo "<input type='hidden' name='annee_scolaire' value='$annee_scolaire' />\n";
		echo "<input type='hidden' name='confirmer' value='ok' />\n";
		echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";

	}	else {
		echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres types d'AIDs</a> | ";
		echo "</div>\n";
		if(count($id_type)==0){
			echo "<p>ERREUR: Vous n'avez pas coché de type d'AIDs.</p>\n";
			echo "</form>\n";
			require("../lib/footer.inc.php");
			die();
		}

    // Déput du traitement !!!!!
    $sql_aid = "select * from aid_config where indice_aid = '".$id_type[0]."'";
    $res_aid = sql_query($sql_aid);
    $nb_type = mysql_num_rows($res_aid);
    $i = 0;
    // Boucle sur les types d'AID
    $tab1 = "<table class='table_annee_anterieure' border = \"1\">\n<tr><td><b>Id</b></td><td><b>Année</b></td><td><b>Nom</b></td><td><b>Nom complet</b></td><td><b>Note sur</b></td><td><b>Type de note</b></td></tr>\n";
    $flag_tab1 = 0;
    while ($i < $nb_type) {
      $nom_type = mysql_result($res_aid,$i,"nom");
      $nom_complet_type = mysql_result($res_aid,$i,"nom_complet");
      $note_max_type  = mysql_result($res_aid,$i,"note_max");
      $type_note_type = mysql_result($res_aid,$i,"type_note");
      $display_begin = mysql_result($res_aid,$i,"display_begin");
      $display_end = mysql_result($res_aid,$i,"display_end");
      $outils_complementaires = mysql_result($res_aid,$i,"outils_complementaires");
      $display_bulletin = mysql_result($res_aid,$i,"display_bulletin");
      $bull_simplifie = mysql_result($res_aid,$i,"bull_simplifie");
      $outils_complementaires= mysql_result($res_aid,$i,"outils_complementaires");
      if (($display_bulletin == 'y') or ($bull_simplifie=='y'))
          $display = 'y';
      else
          $display = 'n';
      $sql_archiv = "insert into archivage_types_aid set
      nom='".addslashes($nom_type)."',
      nom_complet='".addslashes($nom_complet_type)."',
      note_sur='".addslashes($note_max_type)."',
      type_note='".addslashes($type_note_type)."',
      display_bulletin='".$display."',
      outils_complementaires='".$outils_complementaires."',
      annee='".$annee_scolaire."'";
      $res_insert1=sql_query($sql_archiv);
      if(!$res_insert1){
          $tab1 .= "<tr><td colspan=\"6\"><font color=\"red\">Erreur d'enregistrement pour la requête : ".$sql_archiv."</font></td></tr>";
          $flag_tab1 = 1;
			} else {
  	      $nouveau_id_type = mysql_insert_id();
          if ($log_error != 'y') {
            $tab1 .= "<tr><td class='small'>".$nouveau_id_type."</td><td class='small'>".$annee_scolaire."</td><td class='small'>".$nom_type."</td><td class='small'>".$nom_complet_type."</td><td class='small'>".$note_max_type."</td><td class='small'>".$type_note_type."</td></tr>";
            $flag_tab1 = 1;
          }
      }


      // Boucle sur les fiches projets
      $sql_aid2 = "select * from aid where indice_aid = '".$id_type[0]."'";
      $res_aid2 = sql_query($sql_aid2);
      $nb_aid = mysql_num_rows($res_aid2);
      $j = 0;
      if ($outils_complementaires == 'y') {
          $tab2 = "<table class='table_annee_anterieure' border = \"1\">\n<tr><td><b>Id AID</b></td><td><b>Année</b></td><td><b>Nom</b></td><td><b>Identifiant type AID</b></td><td><b>Productions</b></td><td><b>Résumé</b></td><td><b>Famille</b></td><td><b>Mots clés</b></td><td><b>Adresse 1</b></td><td><b>Adresse 1</b></td><td><b>Public destinataire</b></td><td><b>Contacts</b></td><td><b>Matière 1</b></td><td><b>Matière 2</b></td><td><b>Fiche publique</b></td><td><b>Affiche adresse 1</b></td><td><b>Moyenne des notes</b></td><td><b>Max des notes</b></td><td><b>Min des notes</b></td><td><b>En construction</b></td><td><b>Liste des professeurs responsables</b></td><td><b>Liste des élèves</b></td><td><b>Liste des élèves responsables</b></td></tr>\n";
      } else {
          $tab2 = "<table border = \"1\">\n<tr><td><b>Id AID</b></td><td><b>Année</b></td><td><b>Nom</b></td><td><b>Identifiant type AID</b></td></tr>\n";
      }
      $flag_tab2 = 0;
      $tab3 = "<table class='table_annee_anterieure' border = \"1\">\n<tr><td><b>Id AID</b></td><td><b>Id élève</b></td><td><b>Elève responsable</b></td></tr>\n";
      $tab4 = "<table class='table_annee_anterieure' border = \"1\">\n<tr><td><b>Id élève</b></td><td><b>Année</b></td><td><b>Classe</b></td><td><b>Id AID</b></td><td><b>Numéro période</b></td><td><b>Appréciation</b></td><td><b>Note élève</b></td><td><b>moyenne classe</b></td><td><b>Min classe</b></td><td><b>Max classe</b></td></tr>\n";
      $tab5 = "<table class='table_annee_anterieure' border = \"1\">\n<tr><td><b>Id élève</b></td><td><b>Nom</b></td><td><b>Prénom</b></td><td><b>Date de naisance</b></td></tr>\n";
      $flag_tab3 = 0;
      $flag_tab4 = 0;
      $flag_tab5 = 0;
      while ($j < $nb_aid) {
          $id = mysql_result($res_aid2,$j,"id");
          $nom = mysql_result($res_aid2,$j,"nom");
          $productions = mysql_result($res_aid2,$j,"productions");
          $resume = mysql_result($res_aid2,$j,"resume");
          $famille = mysql_result($res_aid2,$j,"famille");
          $mots_cles = mysql_result($res_aid2,$j,"mots_cles");
          $adresse1 = mysql_result($res_aid2,$j,"adresse1");
          $adresse2 = mysql_result($res_aid2,$j,"adresse2");
          $public_destinataire = mysql_result($res_aid2,$j,"public_destinataire");
          $contacts = mysql_result($res_aid2,$j,"contacts");
          $divers = mysql_result($res_aid2,$j,"divers");
          $matiere1 = mysql_result($res_aid2,$j,"matiere1");
          $matiere2 = mysql_result($res_aid2,$j,"matiere2");
          $fiche_publique = mysql_result($res_aid2,$j,"fiche_publique");
          $affiche_adresse1 = mysql_result($res_aid2,$j,"affiche_adresse1");
          $en_construction = mysql_result($res_aid2,$j,"en_construction");

          // Les responsables des aids
          $liste_profs = "";
          $call_liste_data = sql_query("SELECT u.login, u.prenom, u.nom
          FROM utilisateurs u, j_aid_utilisateurs j
          WHERE (j.id_aid='".$id."' and u.login=j.id_utilisateur and j.indice_aid='".$id_type[0]."')
          order by u.nom, u.prenom");
          $nombre_prof = mysql_num_rows($call_liste_data);
          $k = "0";
          while ($k < $nombre_prof) {
            if ($liste_profs != "") $liste_profs .= ", ";
            $nom_prof = @mysql_result($call_liste_data, $k, "nom");
            $prenom_prof = @mysql_result($call_liste_data, $k, "prenom");
            $nom_prenom = $nom_prof." ".$prenom_prof;
            $liste_profs .= $nom_prenom;
            $k++;
          }

          // Eleves de l'aid
          $call_liste_data = sql_query("SELECT e.login, e.no_gep, e.nom, e.prenom
          FROM eleves e, j_aid_eleves j
          WHERE (j.id_aid='".$id."' and e.login=j.login and j.indice_aid='".$id_type[0]."')");
          $nombre = mysql_num_rows($call_liste_data);
          $k = "0";
          $liste_eleves = "";
          while ($k < $nombre) {
            $login_eleve = mysql_result($call_liste_data, $k, "login");
            $no_gep = mysql_result($call_liste_data, $k, "no_gep");
            $prenom_eleve = @mysql_result($call_liste_data, $k, "prenom");
            $nom_eleve = @mysql_result($call_liste_data, $k, "nom");
            $call_classe = sql_query("SELECT c.classe FROM classes c, j_eleves_classes j WHERE (j.login = '$login_eleve' and j.id_classe = c.id) order by j.periode DESC");
            $classe_eleve = @mysql_result($call_classe, '0', "classe");
            if ($liste_eleves != "") $liste_eleves .= ", ";
            $liste_eleves .=$nom_eleve." ".$prenom_eleve." (".$classe_eleve.")";
            $k++;
          }

          // Eleves responsables
          $call_liste_data = sql_query("SELECT e.login, e.no_gep, e.nom, e.prenom
          FROM eleves e, j_aid_eleves_resp j
          WHERE (j.id_aid='".$id."' and e.login=j.login and j.indice_aid='".$id_type[0]."')");
          $nombre = mysql_num_rows($call_liste_data);
          $k = "0";
          $liste_eleves_resp = "";
          while ($k < $nombre) {
            $login_eleve = mysql_result($call_liste_data, $k, "login");
            $no_gep = mysql_result($call_liste_data, $k, "no_gep");
            $prenom_eleve = @mysql_result($call_liste_data, $k, "prenom");
            $nom_eleve = @mysql_result($call_liste_data, $k, "nom");
            $call_classe = sql_query("SELECT c.classe FROM classes c, j_eleves_classes j WHERE (j.login = '$login_eleve' and j.id_classe = c.id) order by j.periode DESC");
            $classe_eleve = @mysql_result($call_classe, '0', "classe");
            if ($liste_eleves_resp != "") $liste_eleves_resp .= ", ";
            $liste_eleves_resp .=$nom_eleve." ".$prenom_eleve." (".$classe_eleve.")";
            $k++;
          }

        // moyennes, max, min (il s'agit de stats sur l'aid, donc indépendant des classes)
        $sql_moyenne = sql_query("SELECT round(avg(note),1) moyenne, MIN(note) min, Max(note) max, periode FROM aid_appreciations where id_aid='".$id."' and statut='' group by periode order by periode");
        $nombre = mysql_num_rows($sql_moyenne);
        $liste_moyenne = "";
        $liste_min = "";
        $liste_max = "";
        $k = "0";
        $flag="0";
        while ($k < $nombre) {
            if ($flag == '1') {
              $liste_min .= "|";
              $liste_max .= "|";
              $liste_moyenne .= "|";
            }
            $num_periode = mysql_result($sql_moyenne,$k,"periode");
            $min = mysql_result($sql_moyenne,$k,"min");
            if ($min=='') $min = '-';
            $max = mysql_result($sql_moyenne,$k,"max");
            if ($max=='') $max = '-';
            $moyenne = mysql_result($sql_moyenne,$k,"moyenne");
            if ($moyenne=='') $moyenne = '-';
            $liste_min .= "période N° ".$num_periode.":".$min;
            $liste_max .= "période N° ".$num_periode.":".$max;
            $liste_moyenne .= "période N° ".$num_periode.":".$moyenne;

            $flag = "1";
            $k++;

        }

        // Sauvegarde dans l'archive
          $sql_archiv = "insert into archivage_aids set
          nom='".addslashes($nom)."',
          annee='".$annee_scolaire."',
          id_type_aid='".$nouveau_id_type."',
          productions='".addslashes($productions)."',
          resume='".addslashes($resume)."',
          famille='".addslashes($famille)."',
          mots_cles='".addslashes($mots_cles)."',
          adresse1='".addslashes($adresse1)."',
          adresse2='".addslashes($adresse2)."',
          public_destinataire='".addslashes($public_destinataire)."',
          contacts='".addslashes($contacts)."',
          divers='".addslashes($divers)."',
          matiere1='".addslashes($matiere1)."',
          matiere2='".addslashes($matiere2)."',
          fiche_publique='".addslashes($fiche_publique)."',
          affiche_adresse1='".addslashes($affiche_adresse1)."',
          notes_moyenne='".addslashes($liste_moyenne)."',
          notes_max='".addslashes($liste_max)."',
          notes_min='".addslashes($liste_min)."',
          en_construction='".addslashes($en_construction)."',
          responsables='".addslashes($liste_profs)."',
          eleves='".addslashes($liste_eleves)."',
          eleves_resp='".addslashes($liste_eleves_resp)."'";
          $res_insert2=sql_query($sql_archiv);

          if(!$res_insert2){
            if ($outils_complementaires == 'y')
              $tab2 .= "<tr><td colspan=\"24\"><font color=\"red\">Erreur d'enregistrement pour la requête : ".$sql_archiv."</font></td></tr>";
            else
              $tab2 .= "<tr><td colspan=\"4\"><font color=\"red\">Erreur d'enregistrement pour la requête : ".$sql_archiv."</font></td></tr>";
            $flag_tab2 = 1;

			    } else {
              $nouveau_id_aid = mysql_insert_id();
              if ($log_error != 'y') {
                if ($outils_complementaires == 'y')
                  $tab2 .= "<tr><td class='small'>".$nouveau_id_aid."</td><td class='small'>".$annee_scolaire."</td><td class='small'>".$nom."</td><td class='small'>".$nouveau_id_type."</td><td class='small'>".$productions."</td><td class='small'>".$resume."</td><td class='small'>".$famille."</td><td class='small'>".$mots_cles."</td><td class='small'>".$adresse1."</td><td class='small'>".$adresse1."</td><td class='small'>".$public_destinataire."</td><td class='small'>".$contacts."</td><td class='small'>".$matiere1."</td><td class='small'>".$matiere2."</td><td class='small'>".$fiche_publique."</td><td class='small'>".$affiche_adresse1."</td><td class='small'>".$liste_moyenne."</td><td class='small'>".$liste_max."</td><td class='small'>".$liste_min."</td><td class='small'>".$en_construction."</td><td class='small'>".$liste_profs."</td><td class='small'>".$liste_eleves."</td><td class='small'>".$liste_eleves_resp."</td></tr>";
                else
                  $tab2 .= "<tr><td class='small'>".$nouveau_id_aid."</td><td class='small'>".$annee_scolaire."</td><td class='small'>".$nom."</td><td class='small'>".$nouveau_id_type."</td></tr>";
                $flag_tab2 = 1;
              }
          }

          // Enregistrement de archivage_aid_eleve
          $call_liste_data = sql_query("SELECT e.login, e.no_gep FROM eleves e, j_aid_eleves j
          WHERE (j.id_aid='".$id."' and e.login=j.login and j.indice_aid='".$id_type[0]."')");
          $nombre = mysql_num_rows($call_liste_data);
          $k = "0";
          while ($k < $nombre) {
            $login_eleve = mysql_result($call_liste_data, $k, "login");
            $no_gep = mysql_result($call_liste_data, $k, "no_gep");
            if ($no_gep =='') {
                $no_gep = "LOGIN_".$login_eleve;
      					$no_gep = cree_substitut_INE_unique($no_gep);
    				}
            // On vérifie que l'élève est enregistré dans archive_eleves. Sinon, on l'enregistre
            $temp = insert_eleve($login_eleve,$no_gep,$annee_scolaire,$log_error);
            if ($temp != '') $flag_tab5 = 1;
            $tab5 .= $temp;

            $test_is_eleve = sql_query1("select login from j_aid_eleves_resp where login='".$login_eleve."' and id_aid='".$id."'");
            if ($test_is_eleve != '-1')
                $eleve_resp = 'y';
            else
                $eleve_resp = 'n';
            $sql_archiv = "insert into archivage_aid_eleve set
            id_eleve = '".$no_gep."',
            id_aid = '".$nouveau_id_aid."',
            eleve_resp = '".$eleve_resp."'";
            $res_insert3=sql_query($sql_archiv);
            if(!$res_insert3){
                $tab3 .= "<tr><td colspan=\"3\"><font color=\"red\">Erreur d'enregistrement pour la requête : ".$sql_archiv."</font></td></tr>";
                $flag_tab3 = 1;
		  	    } else if ($log_error!='y') {
                $tab3 .= "<tr><td class='small'>".$nouveau_id_aid."</td><td class='small'>".$no_gep."</td><td class='small'>".$eleve_resp."</td></tr>\n";
                $flag_tab3 = 1;
            }


    				// Appréciation AID, min, max moyennes
            $call_liste_data_app = sql_query("SELECT * FROM aid_appreciations WHERE (id_aid='".$id."' and login='".$login_eleve."')");
            $nombre_app = mysql_num_rows($call_liste_data_app);
            $t = "0";
            while ($t < $nombre_app) {
              $login_eleve = mysql_result($call_liste_data_app, $t, "login");
              $periode = mysql_result($call_liste_data_app, $t, "periode");
              $appreciation = mysql_result($call_liste_data_app, $t, "appreciation");
              $call_classe = sql_query("SELECT c.id, c.classe FROM classes c, j_eleves_classes j WHERE (j.login = '".$login_eleve."' and j.id_classe = c.id and j.periode='".$periode."')");
              $id_classe = @mysql_result($call_classe, '0', "id");
              $periode_max = sql_query("select count(num_periode) from periodes where id_classe='".$id_classe."'");
              $last_periode_aid = min($periode_max,$display_end);
              $classe = @mysql_result($call_classe, '0', "classe");
	            if (($periode >= $display_begin) and ($periode <= $display_end) and
		          (($type_note_type == 'every') or (($type_note_type == 'last') and ($periode == $last_periode_aid)))) {
                $statut = mysql_result($call_liste_data_app, $t, "statut");
                if ($statut == '')
                  $note = mysql_result($call_liste_data_app, $t, "note");
                else
                  $note = $statut;
                if ($note == '') $note = '-';
                if ($note == 'other') $note = '-';
                $sql_moyenne = sql_query("SELECT MIN(note) note_min, MAX(note) note_max, round(avg(note),1) moyenne FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '".$id_classe."' and a.statut='' and a.indice_aid='".$id_type[0]."' and a.periode='".$periode."')");
                $flag="0";
                $min = mysql_result($sql_moyenne,0,"note_min");
                if ($min=='') $min = '-';
                $max = mysql_result($sql_moyenne,0,"note_max");
                if ($max=='') $max = '-';
                $moyenne = mysql_result($sql_moyenne,0,"moyenne");
                if ($moyenne=='') $moyenne = '-';
              } else {
                $note = '-';
                $min = '-';
                $max = '-';
                $moyenne = '-';
              }
              $sql_archiv = "insert into archivage_appreciations_aid set
              id_eleve = '".$no_gep."',
              annee='".$annee_scolaire."',
              id_aid = '".$nouveau_id_aid."',
              classe = '".addslashes($classe)."',
              periode = '".addslashes($periode)."',
              appreciation = '".addslashes($appreciation)."',
              note_moyenne_classe = '".addslashes($moyenne)."',
              note_min_classe = '".addslashes($min)."',
              note_max_classe = '".addslashes($max)."',
              note_eleve = '".addslashes($note)."'";
              $res_insert4=sql_query($sql_archiv);
              if(!$res_insert4){
                  $tab4 .= "<tr><td colspan=\"11\"><font color=\"red\">Erreur d'enregistrement pour la requête : ".$sql_archiv."</font></td></tr>";
                  $flag_tab4 = 1;
		  	      } else if ($log_error!='y') {
                  $tab4 .= "<tr><td class='small'>".$no_gep."</td><td class='small'>".$annee_scolaire."</td><td class='small'>".$classe."</td><td class='small'>".$nouveau_id_aid."</td><td class='small'>".$periode."</td><td class='small'>".$appreciation."</td><td class='small'>".$note."</td><td class='small'>".$moyenne."</td><td class='small'>".$min."</td><td class='small'>".$max."</td></tr>\n";
                  $flag_tab4 = 1;
              }

              $t++;
            }

            // si le plugin "port_folio" existe et est activé
            $test_plugin = sql_query1("select ouvert from plugins where nom='port_folio'");
            if (isset($flag_port_folio)) {
              include("../mod_plugins/port_folio/archivage_port_folio.php");
            }

            $k++;
          }
          $j++;
      }
      $tab5 .= "</table>\n";
      $tab4 .= "</table>\n";
      $tab3 .= "</table>\n";
      $tab2 .= "</table>\n";
      $i++;
    }
    $tab1 .= "</table>\n";


		//===================================

		if(isset($deja_traitee_id_type)){
			echo "<h2>Types d'AIDs déjà traités</h2><p>";
			for($i=0;$i<count($deja_traitee_id_type);$i++){
				echo "<input type='hidden' name='deja_traitee_id_type[]' value='$deja_traitee_id_type[$i]' />\n";
  			$nom_type = sql_query1("select nom from aid_config where indice_aid = '".$deja_traitee_id_type[$i]."'");
				if ($i > 0) echo ", ";
        echo "<b>".$nom_type."</b>";
			}
			echo "</p>\n";
		}

		//===================================
    // Fin du traitement
		echo "<hr /><input type='hidden' name='deja_traitee_id_type[]' value='$id_type[0]' />\n";

		$temoin_encore_des_types=0;
		$chaine="";
		for($i=1;$i<count($id_type);$i++){
			echo "<input type='hidden' name='id_type[]' value='$id_type[$i]' />\n";
			$temoin_encore_des_types++;
		  $nom_type = sql_query1("select nom from aid_config where indice_aid = '".$id_type[$i]."'");
			$chaine.=", ".$nom_type;
		}
		if($chaine!=""){
			echo "<h2>Types d'AIDs restant à traiter</h2><p><b>".substr($chaine,2)."</b></p>\n";
		}
		if($temoin_encore_des_types>0){
/*			echo "<script type='text/javascript'>
			setTimeout('document.formulaire.submit();', 5000);
			</script>\n";*/
			echo "<center><input type=\"submit\" name='ok' value=\"Continuer le traitement\" style=\"font-variant: small-caps;\" /></center>\n";
		}	else {
			echo "<h2>Traitement terminé.</h2>\n";
		}
    echo "<hr />";
		$nom_type = sql_query1("select nom from aid_config where indice_aid = '".$id_type[0]."'");
    echo "<h2>Résultats du traitement du type d'AID : $nom_type</h2>\n";


    echo "<h3>Enregistrement du type d'AID";
    if ($flag_tab1 == 1)
        echo $tab1;
    else
        if ($log_error =='y')
            echo " : <b><font color='green'>OK !</font></b></h3>";
        else
            echo " : </h3>Aucun nouvel enregistrement n'a été effectué.";
    echo "<H3>Enregistrement de nouveaux élèves non encore archivés";
    if ($flag_tab5 == 1)
        echo $tab5;
    else
        if ($log_error =='y')
            echo " : <b><font color='green'>OK !</font></b></h3>";
        else
            echo " : </h3>Aucun nouvel enregistrement n'a été effectué.";

    echo "<H3>Enregistrement des AIDs";
    if ($flag_tab2 == 1)
        echo $tab2;
    else
        if ($log_error =='y')
            echo " : <b><font color='green'>OK !</font></b></h3>";
        else
            echo " : </h3>Aucun nouvel enregistrement n'a été effectué.";

    echo "<H3>Enregistrement des liens AIDs/élèves";
    if ($flag_tab3 == 1)
        echo $tab3;
    else
        if ($log_error =='y')
            echo " : <b><font color='green'>OK !</font></b></h3>";
        else
            echo " : </h3>Aucun nouvel enregistrement n'a été effectué.";

    echo "<H3>Enregistrement des appréciations, notes, min, max";
    if ($flag_tab4 == 1)
        echo $tab4;
    else
        if ($log_error =='y')
            echo " : <b><font color='green'>OK !</font></b></h3>";
        else
            echo " : </h3>Aucun nouvel enregistrement n'a été effectué.";

    if (isset($flag_port_folio)) {
      $tab_item .= "</table>\n";
      echo "<H3>Enregistrement des items validés";
      echo $tab_item;
    }


		echo "<input type='hidden' name='annee_scolaire' value='$annee_scolaire' />\n";
		echo "<input type='hidden' name='confirmer' value='ok' />\n";
		echo "<input type='hidden' name='log_error' value='$log_error' />\n";
	}
}

echo "</form>\n";
echo "<br />\n";
require("../lib/footer.inc.php");
?>