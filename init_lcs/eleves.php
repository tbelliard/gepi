<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
function connect_ldap($l_adresse,$l_port,$l_login,$l_pwd) {
    $ds = @ldap_connect($l_adresse, $l_port);
    if($ds) {
       // On dit qu'on utilise LDAP V3, sinon la V2 par d?faut est utilis? et le bind ne passe pas.
       $norme = @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
       // Acces non anonyme
       if ($l_login != '') {
          // On tente un bind
          $b = @ldap_bind($ds, $l_login, $l_pwd);
       } else {
          // Acces anonyme
          $b = @ldap_bind($ds);
       }
       if ($b) {
           return $ds;
       } else {
           return false;
       }
    } else {
       return false;
    }
}


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

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_eleves;

// Initialisation
$lcs_ldap_people_dn = 'ou=people,'.$lcs_ldap_base_dn;
$lcs_ldap_groups_dn = 'ou=groups,'.$lcs_ldap_base_dn;

function add_eleve($_login, $_nom, $_prenom, $_civilite, $_naissance, $_elenoet = 0) {
    // Fonction d'ajout d'un élève dans la base Gepi
    if ($_civilite != "M" && $_civilite != "F") {
        if ($_civilite == 1) {
            $_civilite = "M";
        } elseif ($_civilite == 0) {
            $_civilite = "F";
        } else {
            $_civilite = "F";
        }
    }

    // Si l'élève existe déjà, on met simplement à jour ses informations...
    $test = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM eleves WHERE login = '" . $_login . "'");
    if (mysqli_num_rows($test) > 0) {
        $record = mysqli_query($GLOBALS["mysqli"], "UPDATE eleves SET nom = '" . $_nom . "', prenom = '" . $_prenom . "', sexe = '" . $_civilite . "', naissance = '" . $_naissance . "', elenoet = '" . $_elenoet . "' WHERE login = '" . $_login . "'");
    } else {
        $query = "INSERT into eleves SET
        login= '" . $_login . "',
        nom = '" . $_nom . "',
        prenom = '" . $_prenom . "',
        sexe = '" . $_civilite . "',
        naissance = '". $_naissance ."',
        elenoet = '".$_elenoet."'";
        $record = mysqli_query($GLOBALS["mysqli"], $query);
    }

    if ($record) {
        return true;
    } else {
        return false;
    }
}


//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des élèves";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<script type="text/javascript">
<!--
function CocheCase(boul){
  len = document.formulaire.elements.length;
  for( i=0; i<len; i++) {
    if (document.formulaire.elements[i].type=='checkbox') {
      document.formulaire.elements[i].checked = boul ;
    }
  }
 }

function InverseSel(){
  len = document.formulaire.elements.length;
  for( i=0; i<len; i++) {
    if (document.formulaire.elements[i].type=='checkbox') {
      a=!document.formulaire.elements[i].checked  ;
      document.formulaire.elements[i].checked = a
    }
   }
}

function MetVal(cible){
len = document.formulaire.elements.length;
if ( cible== 'nom' ) {
  a=2;
  b=document.formulaire.nom.value;
  } else {
  a=3;
  b=document.formulaire.pour.value;
  }
for( i=0; i<len; i++) {
if ((document.formulaire.elements[i].type=='checkbox')
     &&
    (document.formulaire.elements[i].checked)
    ) {
document.formulaire.elements[i+a].value = b ;
}}}
 // -->
</script>

<?php



echo "<p class='bold'><a href='../init_lcs/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

if (isset($_POST['step'])) {
	check_token(false);

    // L'admin a validé la procédure, on procède donc...

    // On se connecte au LDAP
    $ds = connect_ldap($lcs_ldap_host,$lcs_ldap_port,"","");

    //----***** STEP 1 *****-----//

    if ($_POST['step'] == "1") {
        // La première étape consiste à importer les classes

        if ($_POST['record'] == "yes") {
            // Les données ont été postées, on les traite donc immédiatement

			echo "<p><em>On vide d'abord les tables suivantes&nbsp;:</em> ";
			$j=0;
			$k=0;
			while ($j < count($liste_tables_del)) {
				$sql="SHOW TABLES LIKE '".$liste_tables_del[$j]."';";
				//echo "$sql<br />";
				$test = sql_query1($sql);
				if ($test != -1) {
					if($k>0) {echo ", ";}
					$sql="SELECT 1=1 FROM $liste_tables_del[$j];";
					$res_test_tab=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_test_tab)>0) {
						$sql="DELETE FROM $liste_tables_del[$j];";
						$del = @mysqli_query($GLOBALS["mysqli"], $sql);
						echo "<b>".$liste_tables_del[$j]."</b>";
						echo " (".mysqli_num_rows($res_test_tab).")";
					}
					else {
						echo $liste_tables_del[$j];
					}
					$k++;
				}
				$j++;
			}

			// Suppression des comptes d'élèves:
			echo "<br />\n";
			echo "<p><em>On supprime les anciens comptes élèves dans Gepi...</em> ";
			$sql="DELETE FROM utilisateurs WHERE statut='eleve';";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);

			// Pour ne pas mettre une info_action par classe si aucune période edt_calendrier n'est encore saisie
			$sql="SELECT 1=1 FROM edt_calendrier WHERE classe_concerne_calendrier!=';' AND classe_concerne_calendrier!='';";
			$test_cal=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_edt_cal=mysqli_num_rows($test_cal);
			// On ne met alors qu'une seule info_action
			if($nb_edt_cal==0) {
				$info_action_titre="Dates de périodes et de vacances";
				$info_action_texte="Pensez à importer les périodes de vacances et saisir ou mettre à jour les dates de périodes et les classes associées dans <a href='edt_organisation/edt_calendrier.php'>Emplois du temps/Gestion/Gestion du calendrier</a>.<br />Les dates de vacances sont notamment utilisées pour les totaux d'absences.";
				$info_action_destinataire=array("administrateur");
				$info_action_mode="statut";
				enregistre_infos_actions($info_action_titre,$info_action_texte,$info_action_destinataire,$info_action_mode);
			}

            // On va enregistrer la liste des classes, ainsi que les périodes qui leur seront attribuées

            $sr = ldap_search($ds,$lcs_ldap_groups_dn,"(cn=Classe*)");
            $data = ldap_get_entries($ds,$sr);
            for ($i=0;$i<$data["count"];$i++) {
                $classe=preg_replace("/Classe_/","",$data[$i]["cn"][0]);
                // On enregistre la classe
                // On teste d'abord :
                $test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM classes WHERE (classe='$classe')"),0);

                $insert_ou_update_classe="";
                if ($test == "0") {
                    $insert_ou_update_classe="insert";
                    //$reg_classe = mysql_query("INSERT INTO classes SET classe='".$classe."',nom_complet='".$_POST['reg_nom_complet'][$classe]."',suivi_par='".$_POST['reg_suivi'][$classe]."',formule='".$_POST['reg_formule'][$classe]."', format_nom='np'");
                    $reg_classe = mysqli_query($GLOBALS["mysqli"], "INSERT INTO classes SET classe='".$classe."',nom_complet='".$_POST['reg_nom_complet'][$classe]."',suivi_par='".$_POST['reg_suivi'][$classe]."',formule='".html_entity_decode($_POST['reg_formule'][$classe])."', format_nom='np'");

				$tab_id_classe=array();
				$sql="SELECT id FROM classes ORDER BY classe;";
				$res_classe = mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_classe=mysqli_fetch_object($res_classe)) {
					$tab_id_classe[]=$lig_classe->id;
				}

				// Associer aux vacances:
				$sql="SELECT * FROM edt_calendrier WHERE numero_periode='0' AND etabvacances_calendrier='1';";
				$res_cal = mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_cal=mysqli_fetch_object($res_cal)) {
					$chaine_id_classe="";

					$tab_id_classe_deja=explode(";", $lig_cal->classe_concerne_calendrier);
					for($loop=0;$loop<count($tab_id_classe);$loop++) {
						if(($tab_id_classe[$loop]==$id_classe)||(in_array($tab_id_classe[$loop], $tab_id_classe_deja))) {
							$chaine_id_classe.=$tab_id_classe[$loop].";";
						}
					}

					$sql="UPDATE edt_calendrier SET classe_concerne_calendrier='".$chaine_id_classe."' WHERE id_calendrier='".$lig_cal->id_calendrier."';";
					$update_cal = mysqli_query($GLOBALS["mysqli"], $sql);
				}

                } else {
                    $insert_ou_update_classe="update";
                    //$reg_classe = mysql_query("UPDATE classes SET classe='".$classe."',nom_complet='".$_POST['reg_nom_complet'][$classe]."',suivi_par='".$_POST['reg_suivi'][$classe]."',formule='".$_POST['reg_formule'][$classe]."', format_nom='np' WHERE classe='$classe'");
                    $reg_classe = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET classe='".$classe."',nom_complet='".$_POST['reg_nom_complet'][$classe]."',suivi_par='".$_POST['reg_suivi'][$classe]."',formule='".html_entity_decode($_POST['reg_formule'][$classe])."', format_nom='np' WHERE classe='$classe'");
                }
                if (!$reg_classe) echo "<p>Erreur lors de l'enregistrement de la classe $classe.";

                // On enregistre les périodes pour cette classe
                // On teste d'abord :
                $id_classe = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "select id from classes where classe='$classe'"),0,'id');
                $test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM periodes WHERE (id_classe='$id_classe')"),0);
                if ($test == "0") {
                    $j = '0';
                    while ($j < $_POST['reg_periodes_num'][$classe]) {
                        $num = $j+1;
                        $nom_per = "Période ".$num;
                        if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                        $register = mysqli_query($GLOBALS["mysqli"], "INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                        if (!$register) echo "<p>Erreur lors de l'enregistrement d'une période pour la classe $classe";

				// 20150810
                        if($insert_ou_update_classe=="insert") {
                            $sql="SELECT * FROM edt_calendrier WHERE numero_periode='".$num."';";
                            $res_cal = mysqli_query($GLOBALS["mysqli"], $sql);
                            if(mysqli_num_rows($res_cal)==1) {
                                $lig_cal=mysqli_fetch_object($res_cal);

                                $tab_id_classe_deja=explode(";", $lig_cal->classe_concerne_calendrier);
                                $chaine_id_classe="";
                                for($loop=0;$loop<count($tab_id_classe);$loop++) {
                                    if(($tab_id_classe[$loop]==$id_classe)||(in_array($tab_id_classe[$loop], $tab_id_classe_deja))) {
                                        $chaine_id_classe.=$tab_id_classe[$loop].";";
                                    }
                                }

                                $sql="UPDATE edt_calendrier SET classe_concerne_calendrier='".$chaine_id_classe."' WHERE id_calendrier='".$lig_cal->id_calendrier."';";
                                $update_cal = mysqli_query($GLOBALS["mysqli"], $sql);

                                $sql="UPDATE periodes SET date_fin='".$lig_cal->jourfin_calendrier."' WHERE id_classe='".$id_classe."' AND num_periode='".$lig_cal->numero_periode."';";
                                $update_per=mysqli_query($GLOBALS["mysqli"], $sql);
                            }
                            elseif($nb_edt_cal>0) {
                                $info_action_titre="Dates de périodes pour la classe ".get_nom_classe($id_classe);
                                $info_action_texte="Pensez à contrôler que la classe ".get_nom_classe($id_classe)." est bien associée aux périodes et vacances dans <a href='edt_organisation/edt_calendrier.php'>Emplois du temps/Gestion/Gestion du calendrier</a>.";
                                $info_action_destinataire=array("administrateur");
                                $info_action_mode="statut";
                                enregistre_infos_actions($info_action_titre,$info_action_texte,$info_action_destinataire,$info_action_mode);
                            }
                        }

                        $j++;
                    }
                } else {
                    // on "démarque" les périodes des classes qui ne sont pas à supprimer
                    $sql = mysqli_query($GLOBALS["mysqli"], "UPDATE periodes SET verouiller='N' where (id_classe='$id_classe' and num_periode='1')");
                    $sql = mysqli_query($GLOBALS["mysqli"], "UPDATE periodes SET verouiller='O' where (id_classe='$id_classe' and num_periode!='1')");
                    //
                    $nb_per = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "select num_periode from periodes where id_classe='$id_classe'"));
                    if ($nb_per > $_POST['reg_periodes_num'][$classe]) {
                        // Le nombre de périodes de la classe est inférieur au nombre enregistré
                        // On efface les périodes en trop
                        $k = 0;
                        for ($k=$_POST['reg_periodes_num'][$classe]+1; $k<$nb_per+1; $k++) {
                            $del = mysqli_query($GLOBALS["mysqli"], "delete from periodes where (id_classe='$id_classe' and num_periode='$k')");
                        }
                    }
                    if ($nb_per < $_POST['reg_periodes_num'][$classe]) {

                        // Le nombre de périodes de la classe est supérieur au nombre enregistré
                        // On enregistre les périodes
                        $k = 0;
                        $num = $nb_per;
                        for ($k=$nb_per+1 ; $k < $_POST['reg_periodes_num'][$classe]+1; $k++) {
                            $num++;
                            $nom_per = "Période ".$num;
                            if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                            $register = mysqli_query($GLOBALS["mysqli"], "INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                            if (!$register) echo "<p>Erreur lors de l'enregistrement d'une période pour la classe $classe";
                        }
                    }
                }

            }

			$sql="update periodes set date_verrouillage='0000-00-00 00:00:00';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if($res) {
				echo "Réinitialisation des dates de verrouillage de périodes effectuée.<br />";
			}
			else {
				echo "Erreur lors de la réinitialisation des dates de verrouillage de périodes.<br />";
			}

            // On efface les classes qui ne sont pas réutilisées cette année  ainsi que les entrées correspondantes dans  j_classes_matieres_professeurs
            $res_menage = mysqli_query($GLOBALS["mysqli"], "select distinct id_classe from periodes where verouiller='T'");
            $k = 0;
            while ($k < mysqli_num_rows($res_menage)) {
               $id_classe = old_mysql_result($res_menage, $k);
               $res1 = mysqli_query($GLOBALS["mysqli"], "delete from classes where id='".$id_classe."'");
               $res2 = mysqli_query($GLOBALS["mysqli"], "delete from j_classes_matieres_professeurs where id_classe='".$id_classe."'");
               $res3 = mysqli_query($GLOBALS["mysqli"], "delete from d_dates_evenements_classes where id_classe='".$id_classe."'");
               // On supprime les groupes qui étaient liées à la classe
               $get_groupes = mysqli_query($GLOBALS["mysqli"], "SELECT id_groupe FROM j_groupes_classes WHERE id_classe = '" . $id_classe . "'");
               for ($l=0;$l<$nb_groupes;$l++) {
                    $id_groupe = old_mysql_result($get_groupes, $l, "id_groupe");
                    $delete2 = mysqli_query($GLOBALS["mysqli"], "delete from j_groupes_classes WHERE id_groupe = '" . $id_groupe . "'");
                    // On regarde si le groupe est toujours lié à une autre classe ou pas
                    $check = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM j_groupes_classes WHERE id_groupe = '" . $id_groupe . "'"), 0);
                    if ($check == "0") {
                        $delete1 = mysqli_query($GLOBALS["mysqli"], "delete from groupes WHERE id = '" . $id_groupe . "'");
                        $delete2 = mysqli_query($GLOBALS["mysqli"], "delete from j_groupes_matieres WHERE id_groupe = '" . $id_groupe . "'");
                        $delete2 = mysqli_query($GLOBALS["mysqli"], "delete from j_groupes_professeurs WHERE id_groupe = '" . $id_groupe . "'");
                    }
               }

               // 20150810
               $sql="SELECT * FROM edt_calendrier WHERE classe_concerne_calendrier LIKE '".$id_classe.";%' OR classe_concerne_calendrier LIKE '%;".$id_classe.";%';";
               $res_edt=mysqli_query($GLOBALS["mysqli"], $sql);
               if(mysqli_num_rows($res_edt)>0) {
                   while($lig_edt=mysqli_fetch_object($res_edt)) {
                       $sql="UPDATE edt_calendrier SET classe_concerne_calendrier='".preg_replace("/^$id_classe;/", "", preg_replace("/;$id_classe;/", ";", $lig_edt->classe_concerne_calendrier))."' WHERE classe_concerne_calendrier LIKE '".$id_classe.";%' OR classe_concerne_calendrier LIKE '%;".$id_classe.";%';";
                       $update=mysqli_query($GLOBALS["mysqli"], $sql);
                   }
               }

               $k++;
            }
            $res = mysqli_query($GLOBALS["mysqli"], "delete from periodes where verouiller='T'");
            echo "<p>Vous venez d'effectuer l'enregistrement des données concernant les classes. S'il n'y a pas eu d'erreurs, vous pouvez aller à l'étape suivante pour enregistrer les données concernant les élèves.</p>";
            echo "<p><b>ATTENTION</b> :<br>Les champs \"régime\" (demi-pensionnaire, externe, ...), \"doublant\"  et \"identifiant national\" ne sont pas présents dans l'annuaire LDAP.
            Il en est de même de toutes les informations sur les responsables des élèves.
            <br />A l'issue de cette étape, <b>vous devrez donc procéder à une opération consistant à convertir la table \"eleves\" et à importer les informations manquantes.</b>
            <br />Vous devrez pour cela fournir des fichiers CSV (ELEVES.CSV, PERSONNES.CSV, RESPONSABLES.CSV et ADRESSES.CSV) <b><a href=\"../init_xml/lecture_xml_sconet.php\" target=\"_blank\">générés ici</a></b> depuis des fichiers XML extraits de SCONET.</p>";
            echo "<center>";
            echo "<form enctype='multipart/form-data' action='eleves.php' method=post name='formulaire'>";
			echo add_token_field();
            echo "<input type=\"hidden\" name=\"record\" value=\"no\" />";
            echo "<input type=\"hidden\" name=\"step\" value=\"2\" />";
            echo "<input type=\"submit\" value=\"Accéder à l'étape 2\" />";
            echo "</form>";
            echo "</center>";

			// On sauvegarde le témoin du fait qu'il va falloir
			// convertir pour générer l'ELE_ID et remplir ensuite les nouvelles tables responsables:
			saveSetting("conv_new_resp_table", 0);


        } else {
            // Les données n'ont pas encore été postées, on affiche donc le tableau des classes

            // On commence par "marquer" les classes existantes dans la base
            $sql = mysqli_query($GLOBALS["mysqli"], "UPDATE periodes SET verouiller='T'");

            $sr = ldap_search($ds,$lcs_ldap_groups_dn,"(cn=Classe*)");
            $data = ldap_get_entries($ds,$sr);

            // On va enregistrer la liste des classes, ainsi que les périodes qui leur seront attribuées

            echo "<form enctype='multipart/form-data' action='eleves.php' method=post name='formulaire'>";
			echo add_token_field();
            echo "<input type=hidden name='record' value='yes'>";
            echo "<input type=hidden name='step' value='1'>";

            echo "<p>Les classes en vert indiquent des classes déjà existantes dans la base GEPI.<br />Les classes en rouge indiquent des classes nouvelles et qui vont être ajoutées à la base GEPI.<br /></p>";
            echo "<p>Pour les nouvelles classes, des noms standards sont utilisés pour les périodes (période 1, période 2...), et seule la première période n'est pas verrouillée. Vous pourrez modifier ces paramètres ultérieurement</p>";
            echo "<p>Attention !!! Il n'y a pas de tests sur les champs entrés. Soyez vigilant à ne pas mettre des caractères spéciaux dans les champs ...</p>";
            echo "<p>Essayez de remplir tous les champs, cela évitera d'avoir à le faire ultérieurement.</p>";
            echo "<p>N'oubliez pas <b>d'enregistrer les données</b> en cliquant sur le bouton en bas de la page<br /><br />";

            ?>
            <fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;">
            <legend style="font-variant: small-caps;"> Aide au remplissage </legend>
            <table border="0">
            <tr>
              <td width="2%">&nbsp;</td>
              <td width="2%">&nbsp;</td>
              <td width="2%">&nbsp;</td>
              <td width="2%">&nbsp;</td>
              <td width="25%">&nbsp;</td>
              <td width="53%">&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="5">Vous pouvez remplir les cases <font color="red">
            une à une</font> et/ou <font color="red">globalement</font> grâce aux
            fonctionnalités offertes ci-dessous :</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">1) D'abord, cochez les lignes une à une</td>
            </tr>
              <tr>
              <td colspan="3">&nbsp;</td>
              <td colspan="3">Vous pouvez aussi &nbsp;
              <a href="javascript:CocheCase(true)">
              COCHER</a> ou
              <a href="javascript:CocheCase(false)">
              DECOCHER</a> toutes les lignes , ou
              <a href="javascript:InverseSel()">
              INVERSER </a>la sélection</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">2) Puis, pour les lignes cochées :</td>
            </tr>
             <tr>
              <td colspan="4">&nbsp;</td>
              <td align="right">le nom au bas du bulletin sera &nbsp;:&nbsp;</td>
              <td><input type="text" name="nom" maxlength="80" size="40">
              <input type ="button" name="but_nom" value="Recopier"
            onclick="javascript:MetVal('nom')"></td>
             </td>
            </tr>
             <tr>
              <td colspan="4">&nbsp;</td>
              <td align="right">la formule au bas du bulletin sera
            &nbsp;:&nbsp;</td>
              <td><input type="text" name="pour" maxlength="80" size="40">
              <input type ="button" name="but_pour" value="Recopier"
            onclick="javascript:MetVal('pour')"></td>
             </td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">3) Cliquez sur les boutons "Recopier" pour remplir les champs selectionnés.</td>
            </tr>

            </table>
            </fieldset>
            <br />
            <?php
            echo "<table border=1 cellpadding=2 cellspacing=2>";
            echo "<tr><td><p class=\"small\"><center>Aide<br />Remplissage</center></p></td><td><p class=\"small\">Identifiant de la classe</p></td><td><p class=\"small\">Nom complet</p></td><td><p class=\"small\">Nom apparaissant au bas du bulletin</p></td><td><p class=\"small\">formule au bas du bulletin</p></td><td><p class=\"small\">Nombres de périodes</p></td></tr>";
            for ($i=0;$i<$data["count"];$i++) {
                $classe_id = preg_replace("/Classe_/","",$data[$i]["cn"][0]);
                $description= $data[$i]["description"][0];
                if ($description == "") $description = $classe_id;

                $test_classe_exist = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes WHERE classe='$classe_id'");
                $nb_test_classe_exist = mysqli_num_rows($test_classe_exist);

                if ($nb_test_classe_exist==0) {
                    $nom_complet = $description;
                    $nom_court = "<font color=red>".$classe_id."</font>";
                    $suivi_par = getSettingValue("gepiAdminPrenom")." ".getSettingValue("gepiAdminNom").", ".getSettingValue("gepiAdminFonction");
                    $formule = "";
                    $nb_per = '3';
                } else {
                    $id_classe = old_mysql_result($test_classe_exist, 0, 'id');
                    $nb_per = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "select num_periode from periodes where id_classe='$id_classe'"));
                    $nom_court = "<font color=green>".$description."</font>";
                    $nom_complet = old_mysql_result($test_classe_exist, 0, 'nom_complet');
                    $suivi_par = old_mysql_result($test_classe_exist, 0, 'suivi_par');
                    $formule = old_mysql_result($test_classe_exist, 0, 'formule');
                }
                echo "<tr>";
                echo "<td><center><input type=\"checkbox\"></center></td>\n";
                echo "<td>";
                echo "<p><b><center>$nom_court</center></b></p>";
                echo "";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_nom_complet[$classe_id]' value=\"".$nom_complet."\">\n";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_suivi[$classe_id]' value=\"".$suivi_par."\">\n";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_formule[$classe_id]' value=\"".$formule."\">\n";
                echo "</td>";
                echo "<td>";
                echo "<select size=1 name='reg_periodes_num[$classe_id]'>\n";
                for ($k=1;$k<7;$k++) {
                    echo "<option value='$k'";
                    if ($nb_per == "$k") echo " SELECTED";
                    echo ">$k";
                }
                echo "</select>";
                echo "</td></tr>";
            }
            echo "</table>\n";
            echo "<input type=hidden name='step2' value='y'>\n";
            echo "<center><input type='submit' value='Enregistrer les données'></center>\n";
            echo "</form>\n";

        }



    //----***** STEP 2 *****-----//
    } elseif ($_POST['step'] == "2") {
        // LDAP attribute
        $ldap_people_attr = array(
        "uid",               // login
        "cn",                // Prenom  Nom
        "sn",               // Nom
        "givenname",            // Pseudo
        "mail",              // Mail
        "homedirectory",           // Home directory personnal web space
        "description",
        "loginshell",
        "gecos",             // Date de naissance,Sexe (F/M),
        "employeenumber"    // identifiant gep
        );

        // La deuxième étape consiste à importer les élèves et à les affecter dans les classes
        $classes = mysqli_query($GLOBALS["mysqli"], "SELECT id, classe FROM classes");
        $nb_classes = mysqli_num_rows($classes);
        $eleves_de = array();
        echo "<table border=\"1\" cellpadding=\"3\" cellspacing=\"3\">\n<tr><td>Nom de la classe</td><td>Login élève</td><td>Nom </td><td>Prénom</td><td>Sexe</td><td>Date de naissance</td><td>Numéro GEP</td></tr>\n";
        for ($i=0;$i<$nb_classes;$i++) {
            $current_classe = old_mysql_result($classes, $i, "classe");
            $current_classe_id = old_mysql_result($classes, $i, "id");
            $filtre = "(cn=Classe_".$current_classe.")";
            $result= ldap_search ($ds, $lcs_ldap_groups_dn, $filtre);
            if ($result) {
                $info = @ldap_get_entries( $ds, $result );
                for ( $u = 0; $u < $info[0]["memberuid"]["count"] ; $u++ ) {
                  $uid = $info[0]["memberuid"][$u] ;
                  if (trim($uid) !="") {
                    $eleve_de[$current_classe_id]=$uid;
                    // Extraction des infos sur l'élève :
                    $result2 = @ldap_read ( $ds, "uid=".$uid.",".$lcs_ldap_people_dn, "(objectclass=posixAccount)", $ldap_people_attr );
                    if ($result2) {
                        $info2 = @ldap_get_entries ( $ds, $result2 );
                        if ( $info2["count"]) {
                            // Traitement du champ gecos pour extraction de date de naissance, sexe
                            $gecos = $info2[0]["gecos"][0];
                            $tmp = split ("[\,\]",$info2[0]["gecos"][0],4);
                            $ret_people = array (
                            "uid"        			=> $info2[0]["uid"][0],
                            "nom"        			=> stripslashes($info2[0]["sn"][0]),
                            "fullname"        => stripslashes($info2[0]["cn"][0]),
                            "pseudo"      		=> $info2[0]["givenname"][0],
                            "email"       		=> $info2[0]["mail"][0],
                            "homedirectory"   => $info2[0]["homedirectory"][0],
                            "description" 		=> $info2[0]["description"][0],
                            "shell"           => $info2[0]["loginshell"][0],
                            "sexe"            => $tmp[2],
                            "naissance"       => $tmp[1],
                            "no_gep"          => $info2[0]["employeenumber"][0]
                            );
                            $long = mb_strlen($ret_people["fullname"]) - mb_strlen($ret_people["nom"]);
                            $prenom = mb_substr($ret_people["fullname"], 0, $long) ;


                            $add = add_eleve($uid,$ret_people["nom"],$prenom,$tmp[2],$tmp[1],$ret_people["no_gep"]);
                            $get_periode_num = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM periodes WHERE (id_classe = '" . $current_classe_id . "')"), 0);
                            $check = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM j_eleves_classes WHERE (login = '" . $uid . "')"), 0);
                            if ($check > 0)
                                $del = mysqli_query($GLOBALS["mysqli"], "DELETE from j_eleves_classes WHERE login = '" . $uid . "'");
                            for ($k=1;$k<$get_periode_num+1;$k++) {
                                $res = mysqli_query($GLOBALS["mysqli"], "INSERT into j_eleves_classes SET login = '" . $uid . "', id_classe = '" . $current_classe_id . "', periode = '" . $k . "'");
                            }
                            $check = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM j_eleves_regime WHERE (login = '" . $uid . "')"), 0);
                            if ($check > 0)
                                $del = mysqli_query($GLOBALS["mysqli"], "DELETE from j_eleves_regime WHERE login = '" . $uid . "'");
                            $res = mysqli_query($GLOBALS["mysqli"], "INSERT into j_eleves_regime SET login = '" . $uid . "',
                            regime  = 'd/p',
                            doublant  = '-'");
                        }
                        @ldap_free_result ( $result2 );
                    }
                    $date_naissance = mb_substr($tmp[1],6,2)."-".mb_substr($tmp[1],4,2)."-".mb_substr($tmp[1],0,4) ;
                    echo "<tr><td>".$current_classe."</td><td>".$uid."</td><td>".$ret_people["nom"]."</td><td>".$prenom."</td><td>".$tmp[2]."</td><td>".$date_naissance."</td><td>".$ret_people["no_gep"]."</td></tr>\n";
                  }
                }
            }
            @ldap_free_result ( $result );
        }
        echo "</table><p>Opération effectuée.</p>";
        echo "<p>Avant de passer à l'étape suivante, vous devez procéder à la conversion de la table \"eleves\" et à l'importation des données manquantes :
        <a href='../responsables/conversion.php?mode=1'>Conversion et importation des données manquantes</a>.</p>";
    }

} else {
    echo "<p>L'opération d'importation des élèves depuis le LDAP de LCS va effectuer les opérations suivantes :</p>";
    echo "<ul>";
    echo "<li>Importation des classes.</li>";
    echo "<li>Tentative d'ajout de chaque élèves présent dans l'annuaire de LCS.</li>";
    echo "<li>Si l'élève n'existe pas, il est créé.</li>";
    echo "<li>Si l'élève existe déjà, ses informations de base sont mises à jour.</li>";
    echo "<li>Affectation des élèves aux classes.</li>";
    echo "</ul>";


	echo "<form enctype='multipart/form-data' action='eleves.php' method=post>";
	echo add_token_field();
	echo "<input type=hidden name='step' value='1'>";
	echo "<input type=hidden name='record' value='no'>";
	$j=0;
	$flag=0;
	for($j=0;$j<count($liste_tables_del);$j++) {
		$sql="SHOW TABLES LIKE '".$liste_tables_del[$j]."';";
		//echo "$sql<br />";
		$test = sql_query1($sql);
		if ($test != -1) {
			$sql="SELECT 1=1 FROM $liste_tables_del[$j];";
			$res_test_tab=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_test_tab)>0) {
				$flag=1;
				break;
			}
		}
		//flush();
	}

    if ($flag != 0){
        echo "<p><b>ATTENTION ...</b><br />";
        echo "Des données concernant la constitution des classes et l'affectation des élèves dans les classes sont présentes dans la base GEPI ! Si vous poursuivez la procédure, ces données seront définitivement effacées !</p>";
    }

    echo "<p>Etes-vous sûr de vouloir importer tous les élèves depuis l'annuaire du serveur LCS vers Gepi ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='Je suis sûr'>";
    echo "</form>";
}

require("../lib/footer.inc.php");
?>
