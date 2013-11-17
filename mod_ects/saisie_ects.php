<?php
/*
 *
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

$gepiYear = $gepiSettings['gepiYear'];

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

include "../lib/bulletin_simple.inc.php";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// On va initialiser des marqueurs qui simplifieront les tests conditionnels par la suite
$acces_prof_suivi = false;
$acces_prof = false;
$acces_scol = false;

$prof_suivi = sql_count(sql_query("SELECT professeur FROM j_eleves_professeurs  WHERE professeur = '".$_SESSION['login']."'")) != "0" ? true : false;

if (($_SESSION['statut'] == 'professeur') && $gepiSettings["GepiAccesSaisieEctsPP"] =='yes' && $prof_suivi) {
  $acces_prof_suivi = true;
}
if (($_SESSION['statut'] == 'professeur') && $gepiSettings["GepiAccesSaisieEctsProf"] =='yes') {
  $acces_prof = true;
}
if ((($_SESSION['statut'] == 'scolarite') and $gepiSettings["GepiAccesSaisieEctsScolarite"] =='yes') or $_SESSION['statut'] == 'secours') {
  $acces_scol = true;
}

if (!$acces_prof_suivi && !$acces_prof && !$acces_scol) {
  die("Droits insuffisants pour accéder à cette page.");
}

// initialisations
$mode_saisie = isset($_POST["mode"]) ? $_POST["mode"] :(isset($_GET["mode"]) ? $_GET["mode"] : NULL);
$presaisie = $mode_saisie == 'presaisie' ? true : false;
if ($acces_scol) $presaisie = false; // On force une saisie normale si profil scolarité
if ($acces_prof and !$acces_prof_suivi) $presaisie = true; // On force le mode 'présaisie' si on a un prof 'normal'
// Maintenant on peut utiliser $presaisie dans les tests de manière relativement fiable.

$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :NULL);
$periode_num = isset($_POST["periode_num"]) ? $_POST["periode_num"] :(isset($_GET["periode_num"]) ? $_GET["periode_num"] :NULL);
$fiche = isset($_POST["fiche"]) ? $_POST["fiche"] :(isset($_GET["fiche"]) ? $_GET["fiche"] :NULL);
$current_eleve_login = isset($_POST["current_eleve_login"]) ? $_POST["current_eleve_login"] :(isset($_GET["current_eleve_login"]) ? $_GET["current_eleve_login"] :NULL);
$ind_eleve_login_suiv = isset($_POST["ind_eleve_login_suiv"]) ? $_POST["ind_eleve_login_suiv"] :(isset($_GET["ind_eleve_login_suiv"]) ? $_GET["ind_eleve_login_suiv"] :NULL);
$current_eleve_login_ap = isset($NON_PROTECT["current_eleve_login_ap"]) ? traitement_magic_quotes(corriger_caracteres($NON_PROTECT["current_eleve_login_ap"])) :NULL;
$affiche_message = isset($_GET["affiche_message"]) ? $_GET["affiche_message"] :NULL;

$CurrentUser = UtilisateurProfessionnelQuery::create()->findPK($_SESSION['login']);




include "../lib/periodes.inc.php";

//*******************************************************************************************************
$msg = '';

// Le formulaire a été posté
if (isset($_POST['is_posted'])) {

	check_token();

    // On s'assure que la période sur laquelle on effectue l'enregistrement est valide
    if (($periode_num < $nb_periode) and ($periode_num > 0) and ($ver_periode[$periode_num] == "N" OR $ver_periode[$periode_num] == "P"))  {
      
        $reg = 'yes';
        
        // On fait des tests de cohérence de droits d'accès
        
        // Si on est en mode de saisie globale et que l'on n'est pas 
        if (!$presaisie and !$acces_scol) {
             
             $test_prof_suivi = sql_query1("select professeur from j_eleves_professeurs
             where login = '$current_eleve_login' and
             professeur = '".$_SESSION['login']."' and
             id_classe = '".$id_classe."'
             ");
             if ($test_prof_suivi == '-1') {
                 $msg = "Vous n'êtes pas professeur de suivi de cet élève.";
                 $reg = 'no';
             }
         }
         if ($reg == 'yes') {

             // Suppression ou édition ?
             if (isset($_POST['delete'])) {
                 if (!$presaisie) { // Pas de reset des données en mode 'présaisie' !
                   if (isset($_POST['delete_all']) and $_POST['delete_all'] == 'yes') {
                       // La case a bien été cochée, on supprime.
                      $Eleve = ElevePeer::retrieveByLOGIN($current_eleve_login);
                      $groupes = $Eleve->getEctsGroupes($periode_num);
                      foreach($groupes as $groupe) {
                          // On a l'élève, le groupe, et la période. On peut supprimer.
                          $Eleve->resetEctsCredit($periode_num,$groupe->getId());
                      }
                      $Eleve->setCreditEctsGlobal(null);
                      $msg = "Les données de cet élève viennent d'être supprimées.";
                   } else {
                       $msg = 'Pour supprimer des données, vous devez cocher la case au-dessus du bouton de validation de suppression.';
                   }
                 }

             } else {

                 // Ici on a affaire à une édition. C'est dans ce bloc que l'enregistrement se passe réellement.
                $Eleve = ElevePeer::retrieveByLOGIN($current_eleve_login);
                $groupes = $Eleve->getEctsGroupes($periode_num);
                foreach($groupes as $groupe) {
                    // On a l'élève, le groupe, et la période. On peut enregistrer.
                    if (!$presaisie) {
                      $valeur_ects = $_POST['valeur_ects_'.$groupe->getId()];
                      $mention_ects = $_POST['mention_ects_'.$groupe->getId()];
                      if (!empty($valeur_ects) && !is_numeric($valeur_ects)) $valeur_ects = "0";
                      if (!in_array($mention_ects, array("A","B","C","D","E","F"))) $mention_ects = '';
                      $Eleve->setEctsCredit($periode_num,$groupe->getId(),$valeur_ects,$mention_ects);
                    } else {
                      // On vérifie que le prof est bien prof de ce groupe
                      if ($groupe->getProfesseurs()->contains($CurrentUser)) {
                        $mention_prof_ects = $_POST['mention_ects_'.$groupe->getId()];
                        if (!in_array($mention_prof_ects, array("A","B","C","D","E","F"))) $mention_prof_ects = '';
                        $Eleve->setEctsCredit($periode_num,$groupe->getId(),null,null,$mention_prof_ects);
                      }
                    }                    
                }
                
                // Le crédit global, uniquement saisi si accès complet
                if (!$presaisie) {
                  $mention_globale = $_POST['credit_ects_global'];
                  if (!in_array($mention_globale, array("A","B","C","D","E","F"))) $mention_globale = '';
                  $Eleve->setCreditEctsGlobal($mention_globale);
                }
             }
        }
    } else {
        $msg = "La période sur laquelle vous voulez enregistrer est verrouillée";
    }

    if (isset($_POST['ok1']) OR isset($_POST['delete']))  {
        
        // En accès complet ou en présaisie, on récupère tous les élèves de la classe.
        if ($acces_scol) {
            $appel_donnees_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
            WHERE (
            c.id_classe='$id_classe' AND
            c.login = e.login AND
            c.periode = '".$periode_num."'

            ) ORDER BY nom,prenom");
            
        // Pour un prof principal, on ne récupère que les élèves dont il est responsable
        } else if ($acces_prof_suivi && !$presaisie) {
            $appel_donnees_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c, j_eleves_professeurs p
            WHERE (c.id_classe='$id_classe' AND
            c.login = e.login AND
            p.login = c.login AND
            p.professeur = '".$_SESSION['login']."' AND
            c.periode = '".$periode_num."'
            ) ORDER BY nom,prenom");
            
        // En présaisie, on ne propose que les élèves pour lesquels le prof enseigne au moins
        // dans un groupe de la classe.
        } else if ($presaisie) {
            $appel_donnees_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT e.* FROM eleves e, j_eleves_groupes jeg, j_groupes_professeurs jgp
              WHERE (
                e.login = jeg.login AND
                jeg.id_groupe = jgp.id_groupe AND
                jgp.login = '".$_SESSION['login']."' AND
                e.login IN (SELECT jec.login FROM j_eleves_classes jec
                                  WHERE
                                      jec.id_classe='$id_classe' AND
                                      jec.periode = '".$periode_num."')              
              ) ORDER BY nom,prenom");
        }

        $nb_eleve = mysqli_num_rows($appel_donnees_eleves);
        if (!isset($_POST['delete'])) {
            $current_eleve_login = @mysql_result($appel_donnees_eleves, $ind_eleve_login_suiv, "login");
            $ind_eleve_login_suiv++;
        }
        if ($ind_eleve_login_suiv >= $nb_eleve)  $ind_eleve_login_suiv = 0;

        header("Location: saisie_ects.php?mode=$mode_saisie&periode_num=$periode_num&id_classe=$id_classe&current_eleve_login=$current_eleve_login&ind_eleve_login_suiv=$ind_eleve_login_suiv&fiche=y&msg=$msg&affiche_message=$affiche_message#app");
    }
}
//*******************************************************************************************************
$message_enregistrement = "Les modifications ont été enregistrées !";
$themessage = 'Des valeurs ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Saisie des ECTS";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<script type="text/javascript" language="javascript">
change = 'no';

</script>
<?php

// Première étape : la classe est définie, on definit la période
if (isset($id_classe) and (!isset($periode_num))) {
    $classe_suivi = sql_query1("SELECT nom_complet FROM classes WHERE id = '".$id_classe."'");
    echo "<p class=bold><a href=\"index_saisie.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Mes classes</a></p>\n";
    echo "<p><b>".$classe_suivi.", choisissez la période : </b></p>\n";
    include "../lib/periodes.inc.php";
    $i="1";
    echo "<ul>\n";
    while ($i < $nb_periode) {
        if ($ver_periode[$i] != "O") {
            echo "<li><a href='saisie_ects.php?mode=$mode_saisie&id_classe=".$id_classe."&amp;periode_num=".$i."'>".ucfirst($nom_periode[$i])."</a></li>\n";
        } else {
            echo "<li>".ucfirst($nom_periode[$i])." (".$gepiClosedPeriodLabel.", saisie impossible)</li>\n";
        }
    $i++;
    }
    echo "</ul>\n";
}

// Deuxième étape : la classe est définie, la période est définie, on affiche la liste des élèves
if (isset($id_classe) and (isset($periode_num)) and (!isset($fiche))) {
    $classe_suivi = sql_query1("SELECT nom_complet FROM classes WHERE id = '".$id_classe."'");
    ?>

	<form enctype="multipart/form-data" action="saisie_ects.php" name="form1" method='post'>

    <p class=bold><a href="saisie_ects.php?id_classe=<?php echo $id_classe; ?>"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Choisir une autre période</a>

	<?php

	echo "<input type='hidden' name='periode_num' value='$periode_num' />\n";

// Ajout lien classe précédente / classe suivante
if($_SESSION['statut'] == 'scolarite'){
	$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc, j_groupes_classes jgc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
}
elseif($acces_prof_suivi && !$presaisie){
	$sql="SELECT DISTINCT c.id,c.classe FROM classes c,
										j_eleves_classes jec,
										j_eleves_professeurs jep,
                                        j_groupes_classes jgc
								WHERE   c.id = jec.id_classe AND
                                        jec.login = jep.login AND
                                        jec.periode = '$periode_num' AND
                                        jec.id_classe = jgc.id_classe AND
                                        jgc.saisie_ects = TRUE AND
										jep.professeur='".$_SESSION['login']."'
								ORDER BY c.classe;";
}
elseif($presaisie) {
  $sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE
        (jgp.login = '" . $_SESSION['login'] . "' AND jgc.id_groupe = jgp.id_groupe AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE)
        ORDER BY c.classe;";
}
elseif($_SESSION['statut'] == 'autre'){
	// On recherche toutes les classes pour ce statut qui n'est accessible que si l'admin a donné les bons droits
	$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc WHERE p.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE  ORDER BY classe";
}
elseif($_SESSION['statut'] == 'secours'){
	$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc WHERE p.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE  ORDER BY classe";
}

$chaine_options_classes="";

$cpt_classe=0;
$num_classe=-1;

$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_classes_suivies=mysqli_num_rows($res_class_tmp);
if($nb_classes_suivies>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;
	while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
			$temoin_tmp=1;
			if($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		else {
			$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
		}
		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}

		$cpt_classe++;

	}
}

// =================================
if (isset($id_class_prec) && $id_class_prec!=0) {
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec&amp;periode_num=$periode_num' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe précédente</a>";
}

if(($chaine_options_classes!="")&&($nb_classes_suivies>1)) {

	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_classe').selectedIndex=$num_classe;
			}
		}
	}
</script>\n";

	//echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
	echo " | <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
	echo $chaine_options_classes;
	echo "</select>\n";
}

if(isset($id_class_suiv)){
	if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv&amp;periode_num=$periode_num' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suivante</a>";}
}
//fin ajout lien classe précédente / classe suivante
echo "</p>\n";

echo "</form>\n";

	?>

    <p class='grand'>Classe : <?php echo $classe_suivi; ?></p>

    <?php
    if ($ver_periode[$periode_num] != "N" AND $ver_periode[$periode_num] != "P") {
        echo "Cette période est fermée pour cette classe !";
    } else {
        ?>

        <p>Cliquez sur le nom de l'élève pour lequel vous voulez entrer ou modifier les crédits ECTS.</p>
        <?php
        if ($acces_scol) {
            $sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
            WHERE (c.id_classe='$id_classe' AND
               c.login = e.login AND
               c.periode = '".$periode_num."'
               ) ORDER BY nom,prenom";
        } else if ($acces_prof_suivi && !$presaisie) {
            $sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c, j_eleves_professeurs p
            WHERE (c.id_classe='$id_classe' AND
               c.login = e.login AND
               p.login = c.login AND
               p.professeur = '".$_SESSION['login']."' AND
               c.periode = '".$periode_num."'
               ) ORDER BY nom,prenom";
        } else if ($presaisie) {
            $sql = "SELECT DISTINCT e.* FROM eleves e, j_eleves_groupes jeg, j_groupes_professeurs jgp
              WHERE (
                e.login = jeg.login AND
                jeg.id_groupe = jgp.id_groupe AND
                jgp.login = '".$_SESSION['login']."' AND
                e.login IN (SELECT jec.login FROM j_eleves_classes jec
                                  WHERE
                                      jec.id_classe='$id_classe' AND
                                      jec.periode = '".$periode_num."')              
              ) ORDER BY nom,prenom";
        }

        $appel_donnees_eleves = mysqli_query($GLOBALS["mysqli"], $sql);
        $nombre_lignes = mysqli_num_rows($appel_donnees_eleves);
        $i = "0";
        $alt=1;
        while($i < $nombre_lignes) {
            $current_eleve_login = mysql_result($appel_donnees_eleves, $i, "login");
            $ind_eleve_login_suiv = 0;
            if ($i < $nombre_lignes-1) $ind_eleve_login_suiv = $i+1;
            $current_eleve_nom = mysql_result($appel_donnees_eleves, $i, "nom");
            $current_eleve_prenom = mysql_result($appel_donnees_eleves, $i, "prenom");
            $alt=$alt*(-1);
            echo "<a href = 'saisie_ects.php?mode=$mode_saisie&periode_num=$periode_num&amp;id_classe=$id_classe&amp;fiche=y&amp;current_eleve_login=$current_eleve_login&amp;ind_eleve_login_suiv=$ind_eleve_login_suiv#app'>$current_eleve_nom $current_eleve_prenom</a><br/>\n";
            $i++;
        }
    }
}


if (isset($fiche)) {

    if ($ver_periode[$periode_num] != "N" AND $ver_periode[$periode_num] != "P") {
        echo "La période est fermée !";
        die();
    }

?>
<script type="text/javascript"><!--
function updatesum() {
 $('total_ects').value = 0;
 $$('select.valeur').each(function(a){
     $('total_ects').value = (($('total_ects').value-0) + (a.value-0));
 })
}

function updateCredits(id,valeur){
    selectElmt = $(id)
    if (selectElmt.options[selectElmt.selectedIndex].value == '0'){
      // On cherche l'index de l'option avec la bonne valeur
      for(var i=0; i< selectElmt.options.length; i++)
      {
        if(selectElmt.options[i].value == valeur){
          newSelectedIndex = i;
        }
      }
      selectElmt.selectedIndex = newSelectedIndex;
      updatesum();
    }
}

function updateMention(id,valeur){
    if (valeur == 0) {
        $(id+'_F').checked = true;
    } else if ($(id+'_F').checked == true) {
        $(id+'_A').checked = true;
    }
}

//--></script>


<?php

    $Eleve = ElevePeer::retrieveByLOGIN($current_eleve_login);
    $redoublant = sql_count(sql_query("SELECT * FROM j_eleves_regime WHERE login = '".$Eleve->getLogin()."' AND doublant = 'R'")) != "0" ? true : false;
    $Classe = ClassePeer::retrieveByPK($id_classe);
    $annees_precedentes = $Eleve->getEctsAnneesPrecedentes();
    $nb_cols = 0;
    // On compte le total de colonnes (= le nombre de périodes pour chaque année archivée).
    foreach($annees_precedentes as $a) {
        $nb_cols += count($a['periodes']);
    }
    $nb_cols += $periode_num+1;
    // On affiche les menus de navigation
  	echo "<form action='".$_SERVER['PHP_SELF']."' name='form_navigation' method='post'>\n";

	echo "<div class='norme'><p class='bold'><a href='saisie_ects.php?mode=$mode_saisie&id_classe=$id_classe&periode_num=$periode_num'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

    if ($acces_scol) {
        $sql="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, j_eleves_classes c
        WHERE (c.id_classe='$id_classe' AND
           c.login = e.login AND
           c.periode = '".$periode_num."'
           ) ORDER BY nom,prenom";
    } else if ($acces_prof_suivi && !$presaisie) {
        $sql="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, j_eleves_classes c, j_eleves_professeurs p
        WHERE (c.id_classe='$id_classe' AND
           c.login = e.login AND
           p.login = c.login AND
           p.professeur = '".$_SESSION['login']."' AND
           c.periode = '".$periode_num."'
           ) ORDER BY nom,prenom";
    } else if ($presaisie) {
            $sql = "SELECT DISTINCT e.* FROM eleves e, j_eleves_groupes jeg, j_groupes_professeurs jgp
              WHERE (
                e.login = jeg.login AND
                jeg.id_groupe = jgp.id_groupe AND
                jgp.login = '".$_SESSION['login']."' AND
                e.login IN (SELECT jec.login FROM j_eleves_classes jec
                                  WHERE
                                      jec.id_classe='$id_classe' AND
                                      jec.periode = '".$periode_num."')              
              ) ORDER BY nom,prenom";
    }

    $chaine_options_eleves="";

    $res_ele_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
    if(mysqli_num_rows($res_ele_tmp)>0){
        $ele_login_prec="";
        $ele_login_suiv="";
        $ind_eleve_login_suiv = 0;
        $temoin_tmp=0;
        while($lig_ele_tmp=mysqli_fetch_object($res_ele_tmp)) {
            if($lig_ele_tmp->login==$Eleve->getLogin()) {
                $chaine_options_eleves.="<option value='$lig_ele_tmp->login' selected='true'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
                $temoin_tmp=1;
                $ind_eleve_login_suiv++;
                if($lig_ele_tmp=mysqli_fetch_object($res_ele_tmp)) {
                    $chaine_options_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
                    $ele_login_suiv=$lig_ele_tmp->login;
                }
                else {
                    $ele_login_suiv="";
                }
            }
            else {
                $chaine_options_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
            }
            if($temoin_tmp==0) {
                $ele_login_prec=$lig_ele_tmp->login;
                $ind_eleve_login_suiv++;
            }
        }
    }
    // =================================

    if($ele_login_prec!=""){
        echo " | <a href='".$_SERVER['PHP_SELF']."?mode=$mode_saisie&fiche=y&amp;periode_num=$periode_num&amp;current_eleve_login=$ele_login_prec&amp;id_classe=$id_classe";
        echo "'>".ucfirst($gepiSettings['denomination_eleve'])." précédent</a>";
    }
    if($chaine_options_eleves!="") {
        echo " | <select name='current_eleve_login' onchange=\"document.forms['form_navigation'].submit();\">\n";
        echo $chaine_options_eleves;
        echo "</select>\n";
    }
    if($ele_login_suiv!=""){
        echo " | <a href='".$_SERVER['PHP_SELF']."?mode=$mode_saisie&fiche=y&amp;periode_num=$periode_num&amp;current_eleve_login=$ele_login_suiv&amp;id_classe=$id_classe";
        echo "'>".ucfirst($gepiSettings['denomination_eleve'])." suivant</a>";
    }
    echo " - ".$Classe->getNom();
    //echo "</p>\n";

    echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
    echo "<input type='hidden' name='periode_num' value='$periode_num' />\n";
    echo "<input type='hidden' name='fiche' value='y' />\n";
    echo "<input type='hidden' name='mode' value='$mode_saisie' />\n";
	echo "</p>\n";
	echo "</div>\n";
	echo "</form>\n";

	echo "<form enctype=\"multipart/form-data\" name='ects_form' id='ects_form' action=\"saisie_ects.php\" method=\"post\">\n";
    echo "<p><b>Principaux domaines d'études</b> : ".$Classe->getEctsDomainesEtude()."</p>";

	echo add_token_field();
    
    echo "<div style='float:left;'>";
	echo "<table class='boireaus' style='margin-left: 50px; border-top: 0px solid black; border-right: 0px solid black; border-left: 0px solid black;' summary=\"Elève ".$Eleve->getLogin()."\">\n";

    echo "<tr><td style='border-top: 0px solid black; border-right: 0px solid black; border-left: 0px solid black;' colspan='";
    echo $nb_cols+1;
    echo "'>";
    echo "<h2>".$Eleve->getPrenom()." ".$Eleve->getNom()."</h2>";
    echo "</td></tr>";

    echo "<tr><td>Enseignements</td>";
    foreach ($annees_precedentes as $a) {
        echo "<td colspan='".count($a['periodes'])."'>".$a['annee']."</td>";
    }

    echo "<td colspan='";
    echo $periode_num+1;
    echo "'>".$gepiSettings['gepiYear'];
    if ($redoublant) echo " - <span style='font-weight:bold; color: red;'>Redoublant</span>";
    echo "</td>";

    echo "</tr>";


    echo "<tr><td>&nbsp;</td>";
    foreach($annees_precedentes as $a) {
        foreach($a['periodes'] as $p_num => $p) {
            echo "<td>".$p."</td>";
        }
    }
    for ($i=1;$i<=$periode_num;$i++) {
        echo "<td";
        if ($i == $periode_num) {
            echo " colspan=2";
        }
        echo ">";
        echo $nom_periode[$i];
        echo "</td>";
    }
    echo "</tr>";



    // Cette variable contient tous les groupes pour la période, organisés par catégories.
    $categories = $Eleve->getEctsGroupesByCategories($periode_num);

    // Pour s'assurer qu'on affiche bien tout, il faut néanmoins récupérer tout de suite
    // les crédits ECTS précédents, sans quoi on ne saura si tout a été affiché.
    $periodes_precedentes = array();
    if ($periode_num > 1) {
        for($i=1;$i<$periode_num;$i++) {
            $periodes_precedentes[$i] = $Eleve->getEctsGroupesByCategories($i);
            foreach($periodes_precedentes[$i] as $key => $cat_per_prec) {
                if (!array_key_exists($key,$categories)) {
                    $categories[$key][0] = $cat_per_prec[0];
                    $categories[$key][1] = array();
                }
            }
        }
    }
    // Idem pour les crédits archivés : on stock en avance tous les crédits archivés
    $archived_credits = array();
    foreach($annees_precedentes as $a) {
        $archived_credits[$a['annee']] = array();
        foreach($a['periodes'] as $p_num => $p) {
            $archived_credits[$a['annee']][$p_num] = $Eleve->getArchivedEctsCredits($a['annee'], $p_num);
        }
    }

    // Ce tableau liste les ID des groupes déjà affichés, pour chaque période.
    $groupes_id = array();
    $donnees_enregistrees = true;
    $total_valeur = array();
    for ($i=1;$i<=$periode_num;$i++) {
        $total_valeur[$i] = 0;
    }
    $mentions = array('A','B','C','D','E','F');

    // Ce tableau liste les ID des ects archivés déjà affichés, pour éviter des doublons à la fin,
    // et déterminer ceux qui n'ont pas encore été affichés.
    $archives_id = array();

    // Ce tableau sert à calculer les totaux de crédits.
    $archives_valeurs_globales = array();
    foreach($annees_precedentes as $a) {
        $archives_valeurs_globales[$a['annee']] = array();
        foreach($a['periodes'] as $p_num => $p) {
            $archives_valeurs_globales[$a['annee']][$p_num] = 0;
        }
    }

    $dossier_valide_conseil = false;

    foreach($categories as $categorie) {
            echo "<tr><td colspan='";
                echo 1+$nb_cols;
            echo "' style='text-align:left; padding-left: 10px; background-color: lightgray;'><b><i>".$categorie[0]->getNomComplet()."</i></b></td></tr>";

        // On traite tous les groupes présents pour la catégorie.
        // Ces groupes sont basés sur la période courante.
        foreach($categorie[1] as $group) {
         
            // On va stocker les dernières valeurs archivées, pour le cas des redoublants
            $derniere_annee_archivee = array();
            
            echo "<tr>";
            echo "<td class='bull_simple'>";
            // Information sur la matière
            echo "<p><b>".$group->getDescription()."</b>";
            echo " (".intval($group->getEctsDefaultValue($id_classe)).")";
            echo "</p></td>";
            // Affichage des éventuels résultats précédents
            foreach($annees_precedentes as $a) {
                foreach($a['periodes'] as $p_num => $p) {
                    $archive = $Eleve->getArchivedEctsCredit($a['annee'], $p_num, $group->getDescription());
                    if ($archive != null and !in_array($archive->getId(), $archives_id)) {
                        echo "<td>";
                        // On stocke l'ID pour voir plus tard si on a bien affiché tous les crédits obtenus par le passé
                        $archives_id[] = $archive->getId();
                        echo $archive->getValeur()." - ".$archive->getMention();
                        $derniere_annee_archivee[$p_num] = array('mention' => $archive->getMention(), 'valeur' => $archive->getValeur());
                        $archives_valeurs_globales[$a['annee']][$p_num] += $archive->getValeur();
                        echo "</td>";
                        $archives_id[] = $archive->getId();
                    } else {
                        echo "<td>-</td>";
                    }
                }
            }

            // Maintenant on attaque les périodes de l'année en cours.
            for ($i=1;$i<=$periode_num;$i++) {
                $CreditEcts = $Eleve->getEctsCredit($i,$group->getId());

                // Si on est rendu à la période courante :
                if ($i == $periode_num) {
                    if ($CreditEcts == null or $CreditEcts->getMention() == null or $CreditEcts->getMention() == '') $donnees_enregistrees = false; // On indique que des données n'ont pas été enregistrées en base de données
                    echo "<td class='bull_simple'>";
                    $valeur_ects = $CreditEcts == null ? $group->getEctsDefaultValue($id_classe) : $CreditEcts->getValeur();
                    if ($valeur_ects == null) $valeur_ects = $group->getEctsDefaultValue($id_classe);
                    $max_ects = $group->getEctsDefaultValue($id_classe) >= $valeur_ects ? $group->getEctsDefaultValue($id_classe)+3 : $valeur_ects+3;
                    $disability = $presaisie ? ' DISABLED ' : '';
                    echo "\n<select class='valeur' id='valeur_ects_".$group->getId()."' name='valeur_ects_".$group->getId()."' onchange=\"updatesum();updateMention('mention_ects_".$group->getId()."',this.options[this.selectedIndex].value);\"$disability>";
                    for($c=0;$c<=$max_ects;$c++) {
                      if (!$redoublant || ($redoublant && isset($derniere_annee_archivee[$i]) && $derniere_annee_archivee[$i]['valeur'] <= $c) || $c == 0) {
                        echo "\n<option value='".$c."'";
                        if ($valeur_ects == $c || ($redoublant && isset($derniere_annee_archivee[$i]) && $derniere_annee_archivee[$i]['valeur'] == $c)) echo " SELECTED";
                        echo ">".$c."</option>";
                      }
                    }
                    echo "</select>";
                    echo "</td>";
                    echo "<td class='bull_simple' style='padding:10px;'>";
                    $mention_ects = $CreditEcts == null ? null : $CreditEcts->getMention();
                    $official_credit_exists = $CreditEcts && $mention_ects != null && $mention_ects != '' ? true : false;
                    if ($official_credit_exists) $dossier_valide_conseil = true;
                    if ($CreditEcts && ($mention_ects == null or $mention_ects == '')) {
                      $mention_ects = $CreditEcts->getMentionProf();
                    }
                    
                    $valeur_forcee = false;
                    if ((!$presaisie || ($presaisie && ($group->getProfesseurs()->contains($CurrentUser)))) && (!$mention_ects || $mention_ects == '')) { $mention_ects = 'A'; $valeur_forcee = true;}
                    
                    $block_lower_credits = false;
                    foreach($mentions as $mention) {
                        echo "\n<input id='mention_ects_".$group->getId()."_$mention' type='radio' name='mention_ects_".$group->getId()."' value='$mention'";
                        if ($mention == $mention_ects) echo " CHECKED ";
                        
                        if ($block_lower_credits || ($presaisie && (!$group->getProfesseurs()->contains($CurrentUser) or $official_credit_exists))) echo " DISABLED ";
                        
                        // Si on a un redoublant et qu'on atteint le crédit de l'année précédente,
                        // on empêche la sélection des crédits inférieurs.
                        if ($redoublant && isset($derniere_annee_archivee[$i]) && $mention == $derniere_annee_archivee[$i]['mention']) {
                          $block_lower_credits = true;
                        }
                        
                        if ($mention == 'F') {
                            echo "onclick=\"$('valeur_ects_".$group->getId()."').selectedIndex=0;\"";
                        } else {
                            echo "onclick=\"updateCredits('valeur_ects_".$group->getId()."','".$group->getEctsDefaultValue($id_classe)."');\"";
                        }
                        $style = !$valeur_forcee && !$official_credit_exists && $mention_ects == $mention ? 'text-decoration: underline; font-style: italic;' : '';
                        echo "/><label for='mention_ects_".$group->getId()."_$mention' style='$style'>$mention</label>";
                    }
                    echo "</td>";
                    $total_valeur[$i] += $valeur_ects;
                    
                    
                // Ici on est aux périodes précédentes
                } else {

                    // Ici on affiche simplement les valeurs de la période, et seulement
                    // si le groupe en question n'a pas été déjà traité.
                    // Il peut arriver que ce groupe ait été traité dans une catégorie
                    // précédente, s'il était assigné à une autre catégorie au semestre
                    // précédent pour l'élève en question... (c'est tordu, oui...)
                    if (!in_array($group->getId(),$groupes_id)) {
                        echo "<td>";
                        if ($CreditEcts == null) {
                            echo "-";
                            $valeur_ects = 0;
                        } else {
                            echo $CreditEcts->getValeur()." - ".$CreditEcts->getMention();
                            $valeur_ects = $CreditEcts->getValeur();
                        }
                        echo "</td>";
                        $total_valeur[$i] += $valeur_ects;
                    } else {
                        echo "<td>-</td>";
                    }

                    // On enlève ce groupe de la liste des groupes pour les périodes précédentes.
                    // Cela va nous permettre de vérifier s'il reste des groupes non traités ayant
                    // cette catégorie (typiquement des groupes auxquels l'élève n'est plus
                    // inscrit.
                    // Attention, il y a un cas de figure non pris en compte : si ce même groupe
                    // est en réalité dans une catégorie différente pour la période précédente !!
                    // (ce serait un cas tordu, mais le bug engendra serait très embêtant, car le
                    // crédit serait affiché deux fois).
                    // Pour pallier ce problème, on doit donc identifier la liste des groupes déjà
                    // affichés pour chaque période. Et on testera cette liste si jamais on doit afficher
                    // des crédits qui 'extra'.
                    if (isset($periodes_precedentes[$i][$categorie[0]->getId()][1][$group->getId()])) {
                        unset($periodes_precedentes[$i][$categorie[0]->getId()][1][$group->getId()]);
                    }
                }
                
            }
            echo "</tr>";
            
            // On indique que le groupe en question a été traité
            $groupes_id[] = $group->getId();
        }

        // Maintenant on a fait le tour de tous les groupes actifs pour la période
        // courante. On regarde si des groupes supplémentaires doivent être affichés
        // pour cette catégorie pour des périodes précédentes.
        $extra_groups = array();
        for ($i=1;$i<$periode_num;$i++) {
            // Test 1 : est-ce qu'il reste des groupes supplémentaires ?
            if (count($periodes_precedentes[$i][$categorie[0]->getId()][1]) > 0) {
                // Oui... Alors on les passe en boucle.
                foreach($periodes_precedentes[$i][$categorie[0]->getId()][1] as $group) {
                    // Test 2 : quand même, ils ont pu être déjà traités sans être
                    // supprimés, s'ils étaient dans une autre catégorie. On regarde
                    // alors la liste des groupes déjà traités pour les périodes
                    // précédentes.
                    if(!in_array($group->getId(), $groupes_id)) {
                        // Il n'a pas été traité, alors on le prend !
                        $extra_groups[] = $group;
                    }
                }
            }
        }
        
        foreach($extra_groups as $group) {
            // On a encore des groupes à traiter... On va donc refaire la même procédure que ci-dessus.
            // Pas très propre ce copier/coller de code (même si c'est en réalité une version
            // simplifiée ici), mais bon... Difficile de faire autrement vue la
            // complexité des contraintes.
            echo "<tr>";
            echo "<td class='bull_simple'>";
            // Information sur la matière
            echo "<p><b>".$group->getDescription()."</b>";
            echo "</p></td>";
            // Affichage des éventuels résultats archivés
            foreach($annees_precedentes as $a) {
                foreach($a['periodes'] as $p_num => $p) {
                    $archive = $Eleve->getArchivedEctsCredit($a['annee'], $p, $group->getDescription());
                    if ($archive != null and !in_array($archive->getId(), $archives_id)) {
                        echo "<td>";
                        // On stocke l'ID pour voir plus tard si on a bien affiché tous les crédits obtenus par le passé
                        $archives_id[] = $archive->getId();
                        echo $archive->getValeur()." - ".$archive->getMention();
                        $archives_valeurs_globales[$a['annee']][$p_num] += $archive->getValeur();
                        echo "</td>";
                        $archives_id[] = $archive->getId();
                    } else {
                        echo "<td>-</td>";
                    }
                }
            }

            // Maintenant on attaque les périodes de l'année en cours.
            for ($i=1;$i<$periode_num;$i++) {
                $CreditEcts = $Eleve->getEctsCredit($i,$group->getId());

                // Si on est rendu à la période courante :
                if ($i == $periode_num) {

                // Ici on est aux périodes précédentes
                } else {
                    // Ici on affiche simplement les valeurs de la période, et seulement
                    // si le groupe en question n'a pas été déjà traité.
                    if (!in_array($group->getId(),$groupes_id)) {
                        echo "<td>";
                        if ($CreditEcts == null) {
                            echo "-";
                            $valeur_ects = 0;
                        } else {
                            echo $CreditEcts->getValeur()." - ".$CreditEcts->getMention();
                            $valeur_ects = $CreditEcts->getValeur();
                        }
                        echo "</td>";
                        $total_valeur[$i] += $valeur_ects;
                    } else {
                        echo "<td>-</td>";
                    }
                    if (isset($periodes_precedentes[$i][$categorie[0]->getId()][1][$group->getId()])) {
                        unset($periodes_precedentes[$i][$categorie[0]->getId()][1][$group->getId()]);
                    }
                }

            }
            // On affiche la période courante vide :
            echo "<td>-</td><td>-</td>";
            echo "</tr>";
            // On indique que le groupe en question a été traité
            $groupes_id[] = $group->getId();
        }
    }


    // Enfin, le traitement ultime : l'hypothèse où il reste des matières archivées
    // dont les ECTS n'ont pas encore été affichés...
    // On ne se pose plus la question des catégories, à ce stade...

    $extra_matieres = array();
    foreach($annees_precedentes as $a) {
        foreach($a['periodes'] as $p_num => $p) {
            $credits = $Eleve->getArchivedEctsCredits($a['annee'], $p_num);
            foreach($credits as $archive) {
                if (!in_array($archive->getId(), $archives_id) and !in_array($archive->getMatiere(), $extra_matieres)) {
                    $extra_matieres[] = $archive->getMatiere();
                }
            }
        }
    }


    foreach($extra_matieres as $matiere) {
        echo "<tr>";
        echo "<td class='bull_simple'>";
        // Information sur la matière
        echo "<p><b>".$matiere."</b>";
        echo "</p></td>";
        // Affichage des éventuels résultats archivés
        foreach($annees_precedentes as $a) {
            foreach($a['periodes'] as $p_num => $p) {
                $archive = $Eleve->getArchivedEctsCredit($a['annee'], $p_num, $matiere);
                if ($archive != null and !in_array($archive->getId(), $archives_id)) {
                    echo "<td>";
                    // On stocke l'ID pour voir plus tard si on a bien affiché tous les crédits obtenus par le passé
                    $archives_id[] = $archive->getId();
                    echo $archive->getValeur()." - ".$archive->getMention();
                    $archives_valeurs_globales[$a['annee']][$p_num] += $archive->getValeur();
                    echo "</td>";
                    $archives_id[] = $archive->getId();
                } else {
                    echo "<td>-</td>";
                }
            }
        }

        // Maintenant on attaque les périodes de l'année en cours.
        for ($i=1;$i<$periode_num;$i++) {
            echo "<td>-</td>";
        }
        // On affiche la période courante vide :
        echo "<td>-</td><td>-</td>";
        echo "</tr>";
    }

    // Maintenant on arrive aux totaux :
    echo "<tr style='border-top: 3px solid black; background-color: lightgray;'><td>Global</td>";
    foreach($annees_precedentes as $a) {
        foreach($a['periodes'] as $p_num => $p) {
            echo "<td>".$archives_valeurs_globales[$a['annee']][$p_num]."</td>";
        }
    }

    for($i=1;$i<=$periode_num;$i++) {
        echo "<td style='text-align: center;'>";
        if ($i == $periode_num) {
            echo "<input id='total_ects' name='total_ects' readonly style='font-weight: bold; width: 40px; background-color: lightgray; cursor: default; border-color: black; text-align: center;' value='$total_valeur[$i]'/></td>";
        } else {
            echo $total_valeur[$i];
        }
        echo "</td>";
    }


      echo "<td style='padding:10px;'>";
      $credit_global = $Eleve->getCreditEctsGlobal();
      if ($credit_global == null || $credit_global->getMention() == null) {
          $mention_globale = 'A';
      } else {
          $mention_globale = $credit_global->getMention();
      }
      foreach($mentions as $mention) {
          echo "<input id='credit_global_$mention' type='radio' name='credit_ects_global' value='$mention'";
          if ($mention_globale == $mention) echo " CHECKED ";
          if ($presaisie) echo " DISABLED ";
          echo "/><label for='credit_global_$mention'>$mention</label>";
      }
      echo "</td>";
    
    echo "</tr>";


    // On affiche un message si le dossier a été validé en conseil de classe
    if ($presaisie && $dossier_valide_conseil) {
      echo "<tr><td colspan='";
      echo $nb_cols+1;
      echo "'>";
      echo "<p style='color: red;'>Dossier validé en conseil de classe</p>";
      echo "</td></tr>";
    }

    // On affiche le statut des données
    if (!$presaisie) {
      echo "<tr><td colspan='";
      echo $nb_cols+1;
      echo "'>";
      if (!$donnees_enregistrees) {
          echo "<p style='color: red;'>Dossier non-examiné<br/><span style='font-size: small;'>Les valeurs par défaut sont pré-saisies, mais ne sont pas encore enregistrées en base de données.</span></p>";
      } else {
          // Des données sont présentes en base de données. Seul les variations de total influent sur le message à afficher
          if ($total_valeur[$periode_num] < 30) {
              echo "<p style='color: red;'>Non validé<br/><span style='font-size: small;'>Des crédits sont enregistrés en base de données, mais le total est inférieur à 30.</span></p>";
          } elseif ($total_valeur[$periode_num] == 30) {
              echo "<p style='color: blue;'>Validé<br/><span style='font-size: small;'>30 crédits ECTS sont enregistrés en base de données pour cette période.</span></p>";
          } else {
              echo "<p style='color: red;'>Excès de crédit<br/><span style='font-size: small;'>Plus de 30 crédits sont enregistrés en base de données pour cette période.</span></p>";
          }
      }
      echo "</td></tr>";
    }    
    
    if (!$presaisie || ($presaisie && !$dossier_valide_conseil)) {
      echo "<tr><td colspan='";
      echo $nb_cols+1;
      echo "' style='padding: 10px;'>";
      if($ele_login_suiv!=""){
          echo "<input type='submit' NAME='ok1' value=\"Enregistrer et passer à l'élève suivant\" />";
      }
      ?>
      <input type="submit" NAME="ok2" value="Enregistrer et revenir à la liste" />
      </td></tr>
<?php

    }

    if (!$presaisie) {

?>
    <tr><td colspan="<?php echo $nb_cols+1;?>" style='padding: 10px;'>
    <p style='font-size: small;'>Si vous souhaitez supprimer toutes les données ECTS de cet élève pour cette période,<br/>cochez la case ci-dessous puis validez avec le bouton 'Supprimer les données'.<br/>Cette opération est irréversible.</p>
    <input id="delete_all" type="checkbox" name="delete_all" value="yes" /><label for="delete_all" style='font-size: small;color: red;'>Supprimez toutes les données.</label><br/>
    <br/>
    <input type="submit" NAME="delete" value="Supprimer les données" />
    </td></tr>    
<?php

    }
?>
    </table>
    <input type=hidden name=id_classe value=<?php echo "$id_classe";?> />
    <input type=hidden name=is_posted value="yes" />
    <input type=hidden name=periode_num value="<?php echo "$periode_num";?>" />
    <input type=hidden name=mode value="<?php echo "$mode_saisie";?>" />
    <input type=hidden name=current_eleve_login value="<?php echo "$current_eleve_login";?>" />
    <input type=hidden name=ind_eleve_login_suiv value="<?php echo "$ind_eleve_login_suiv";?>" />
    <!--br /-->
    <br/>

    </div>
    <div style='padding-left: 30px; padding-top: 70px; float:left;'>
    <p>A = Très bien</p>
    <p>B = Bien</p>
    <p>C = Assez bien</p>
    <p>D = Convenable</p>
    <p>E = Passable</p>
    <p>F = Insuffisant</p>
    </div>
    </form>
    <?php

}

//**********************************************************************************************************
require("../lib/footer.inc.php");
?>
