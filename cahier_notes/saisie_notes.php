<?php
/*
 * Last modification  : 27/09/2006
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

// Resume session
$resultat_session = resumeSession();
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

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
    die("Le module n'est pas activé.");
}
unset($id_devoir);
$id_devoir = isset($_POST["id_devoir"]) ? $_POST["id_devoir"] : (isset($_GET["id_devoir"]) ? $_GET["id_devoir"] : NULL);
unset($affiche_message);
$affiche_message = isset($_POST["affiche_message"]) ? $_POST["affiche_message"] : (isset($_GET["affiche_message"]) ? $_GET["affiche_message"] : NULL);

$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "classe");

if ($id_devoir)  {
    $appel_devoir = mysql_query("SELECT * FROM cn_devoirs WHERE id ='$id_devoir'");
    $nom_devoir = mysql_result($appel_devoir, 0, 'nom_court');
    $query = mysql_query("SELECT id_conteneur, id_racine FROM cn_devoirs WHERE id = '$id_devoir'");
    $id_racine = mysql_result($query, 0, 'id_racine');
    $id_conteneur = mysql_result($query, 0, 'id_conteneur');
} else if ((isset($_POST['id_conteneur'])) or (isset($_GET['id_conteneur']))) {
    $id_conteneur = isset($_POST['id_conteneur']) ? $_POST['id_conteneur'] : (isset($_GET['id_conteneur']) ? $_GET['id_conteneur'] : NULL);
    $query = mysql_query("SELECT id_racine FROM cn_conteneurs WHERE id = '$id_conteneur'");
    $id_racine = mysql_result($query, 0, 'id_racine');
} else {
    header("Location: ../logout.php?auto=1");
    die();
}


//Initialisation pour le pdf
$w_pdf=array();
$w1 = "i"; //largeur de la première colonne
$w1b = "d"; //largeur de la colonne "classe" si présente
$w2 = "n"; // largeur des colonnes "notes"
$w3 = "c"; // largeur des colonnes "commentaires"
$header_pdf=array();
$data_pdf=array();


$appel_conteneur = mysql_query("SELECT * FROM cn_conteneurs WHERE id ='$id_conteneur'");
$nom_conteneur = mysql_result($appel_conteneur, 0, 'nom_court');
$mode = mysql_result($appel_conteneur, 0, 'mode');
$arrondir = mysql_result($appel_conteneur, 0, 'arrondir');
$ponderation = mysql_result($appel_conteneur, 0, 'ponderation');
$display_bulletin = mysql_result($appel_conteneur, 0, 'display_bulletin');

// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
    header("Location: index.php?msg=$mess");
    die();
}

//
// On dispose donc pour la suite des trois variables :
// id_racine
// id_conteneur
// id_devoir

$appel_cahier_notes = mysql_query("SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe = mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group = get_group($id_groupe);
$id_classe = $current_group["classes"]["list"][0];
$periode_num = mysql_result($appel_cahier_notes, 0, 'periode');
if (count($current_group["classes"]["list"]) > 1) {
    $multiclasses = true;
} else {
    $multiclasses = false;
    $order_by = "nom";
}
include "../lib/periodes.inc.php";

// On teste si la periode est vérouillée !
if (($current_group["classe"]["ver_periode"]["all"][$periode_num] <= 1) and (isset($id_devoir)) and ($id_devoir!='') ) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes dont la période est bloquée !");
    header("Location: index.php?msg=$mess");
    die();
}


$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];


$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
$nom_periode = mysql_result($periode_query, $periode_num-1, "nom_periode");

//
// Détermination des sous-conteneurs
//
$nom_sous_cont = array();
$id_sous_cont  = array();
$coef_sous_cont = array();
$display_bulletin_sous_cont = array();
$nb_sous_cont = 0;
if ($mode==1) {
    // on s'intéresse à tous les conteneurs fils, petit-fils, ...
    sous_conteneurs($id_conteneur,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'all');
} else {
    // On s'intéresse uniquement au conteneurs fils
    sous_conteneurs($id_conteneur,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'');
}


//-------------------------------------------------------------------------------------------------------------------
if (isset($_POST['notes'])) {
//=======================================================
// MODIF: boireaus
// J'ai déplacé vers le bas l'alert Javascript qui lors d'un import des notes était inscrit avant même la balise <html>
// Cela faisait une page HTML non valide.
    $temp = $_POST['notes']." 1";
    $temp = ereg_replace("\\\\r","\r",$temp);
    $temp = ereg_replace("\\\\n","\n",$temp);
    $longueur = strlen($temp);
    $i = 0;
    $fin_note = 'yes';
    $indice = $_POST['debut_import']-2;
    $tempo = '';
    while (($i < $longueur) and ($indice < $_POST['fin_import'])) {
        $car = substr($temp, $i, 1);
        if (ereg ("^[0-9\.\,\a-z\A-Z\-]{1}$", $car)) {
            if (($fin_note=='yes') or ($i == $longueur-1)) {
                $fin_note = 'no';
                if (is_numeric($tempo)) {
                    if ($tempo <= 20) {
                        $note_import[$indice] = $tempo;
                        $indice++;
                    } else {
                        $note_import[$indice] = "0";
                        $indice++;
                    }
                } else {
            $note_import[$indice] = $tempo;
            $indice++;
                }
                $tempo = '';
            }
            $tempo=$tempo.$car;
        } else {
            $fin_note = 'yes';
        }
        $i++;
    }
}


if (isset($_POST['is_posted']))   {
    foreach ($current_group["eleves"][$periode_num]["users"] as $eleve) {
      $reg_eleve_login = $eleve["login"];
      $id_classe = $current_group["eleves"][$periode_num]["users"][$reg_eleve_login]["classe"];
      if ($current_group["classe"]["ver_periode"][$id_classe][$periode_num] == "N") {
        $nom_log = $reg_eleve_login."_note";
        $note = '';
        if (isset($_POST[$nom_log])) $note = $_POST[$nom_log];
        $elev_statut = '';
        $nom_log = $reg_eleve_login."_comment";
        $comment = '';
        if (isset($_POST[$nom_log])) $comment = $_POST[$nom_log];
        if (($note == 'disp')) { $note = '0'; $elev_statut = 'disp';
        } else if (($note == 'abs')) { $note = '0'; $elev_statut = 'abs';
        } else if (($note == '-')) { $note = '0'; $elev_statut = '-';
        } else if (ereg ("^[0-9\.\,]{1,}$", $note)) {
            $note = str_replace(",", ".", "$note");
            if (($note < 0) or ($note > 20)) { $note = ''; $elev_statut = '';}
        } else {
            $note = ''; $elev_statut = 'v';
        }
        $test_eleve_note_query = mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$reg_eleve_login' AND id_devoir = '$id_devoir')");
        $test = mysql_num_rows($test_eleve_note_query);
        if ($test != "0") {
            $register = mysql_query("UPDATE cn_notes_devoirs SET comment='".$comment."', note='$note',statut='$elev_statut' WHERE (login='".$reg_eleve_login."' AND id_devoir='".$id_devoir."')");
        } else {
            $register = mysql_query("INSERT INTO cn_notes_devoirs SET login='".$reg_eleve_login."', id_devoir='".$id_devoir."',note='".$note."',statut='".$elev_statut."',comment='".$comment."'");
        }
      }
    //$j++;
    }
    //
    // Mise à jour des moyennes du conteneur et des conteneurs parent, grand-parent, etc...
    //
    $arret = 'no';
    mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur,$arret);
    $affiche_message = 'yes';
}

$message_enregistrement = "Les modifications ont été enregistrées !";
$themessage  = 'Des notes ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Saisie des notes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//=======================================================
// MODIF: boireaus
// Avertissement redescendu ici pour éviter d'avoir une page web avec une section Javascript avant même la balise <html>
if (isset($_POST['notes'])) {
    echo "<script type=\"text/javascript\" language=\"javascript\">
    <!--
    alert(\"Attention, les notes importées ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton 'Enregistrer') !\");
    //-->
    </script>\n";
}
//=======================================================
?>
<script type="text/javascript" language=javascript>
chargement = false;
</script>

<?php
//$titre =  "Boîte : ".$nom_conteneur." (".$nom_periode.")";
if($id_conteneur==$id_racine){
	if($nom_conteneur==""){
		$titre=htmlentities($current_group['description'])." (".$nom_periode.")";
	}
	else{
		$titre=$nom_conteneur." (".$nom_periode.")";
	}
}
else{
	$titre=htmlentities(ucfirst(strtolower(getSettingValue("gepi_denom_boite"))))." : ".$nom_conteneur." (".$nom_periode.")";
}

$titre_pdf = urlencode($titre);
if ($id_devoir != 0) $titre .= " - SAISIE";  else $titre .= " - VISUALISATION";

echo "<script type=\"text/javascript\" language=\"javascript\">";
if (isset($_POST['debut_import'])) {
    $temp = $_POST['debut_import']-1;
    if ((isset($note_import[$temp])) and ($note_import[$temp] != '')) echo "change = 'yes';"; else echo "change = 'no';";
} else {
    echo "change = 'no';";
}
echo "</script>";


// Détermination du nombre de devoirs à afficher
$appel_dev = mysql_query("select * from cn_devoirs where (id_conteneur='$id_conteneur' and id_racine='$id_racine') order by date");
$nb_dev  = mysql_num_rows($appel_dev);

// Détermination des noms et identificateurs des devoirs
$j = 0;
while ($j < $nb_dev) {
    $nom_dev[$j] = mysql_result($appel_dev, $j, 'nom_court');
    $id_dev[$j] = mysql_result($appel_dev, $j, 'id');
    $coef[$j] = mysql_result($appel_dev, $j, 'coef');
    $facultatif[$j] = mysql_result($appel_dev, $j, 'facultatif');
    $date = mysql_result($appel_dev, $j, 'date');
    $annee = substr($date,0,4);
    $mois =  substr($date,5,2);
    $jour =  substr($date,8,2);
    $display_date[$j] = $jour."/".$mois."/".$annee;
    $j++;
}

echo "<p class=bold>";
echo "<a href=\"../accueil.php\"  onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a>|";
echo "<a href='index.php'  onclick=\"return confirm_abandon (this, change, '$themessage')\"> Mes enseignements </a>|";
echo "<a href=\"index.php?id_racine=$id_racine\" onclick=\"return confirm_abandon (this, change, '$themessage')\"> Mes évaluations </a>|";
if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) {
    //echo "<a href='add_modif_conteneur.php?id_racine=$id_racine&amp;mode_navig=retour_saisie&amp;id_retour=$id_conteneur' onclick=\"return confirm_abandon (this, change,'$themessage')\">Créer une boîte</a>|";
    echo "<a href='add_modif_conteneur.php?id_racine=$id_racine&amp;mode_navig=retour_saisie&amp;id_retour=$id_conteneur' onclick=\"return confirm_abandon (this, change,'$themessage')\"> Créer un";

	if(getSettingValue("gepi_denom_boite_genre")=='f'){echo "e";}

	echo " ".htmlentities(strtolower(getSettingValue("gepi_denom_boite")))." </a>|";

    echo "<a href='add_modif_dev.php?id_conteneur=$id_racine&amp;mode_navig=retour_saisie&amp;id_retour=$id_conteneur' onclick=\"return confirm_abandon (this, change,'$themessage')\"> Créer une évaluation </a>|";
}
//echo "<a href=\"../fpdf/imprime_pdf.php?titre=$titre_pdf&amp;id_groupe=$id_groupe&amp;periode_num=$periode_num\" target=\"_blank\" onclick=\"return VerifChargement()\">Imprimer au format PDF</a>|";
//echo "<a href=\"../fpdf/imprime_pdf.php?titre=$titre_pdf&amp;id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;nom_pdf_en_detail=oui\" target=\"_blank\" onclick=\"return VerifChargement()\">Imprimer au format PDF</a>|";
echo "<a href=\"../fpdf/imprime_pdf.php?titre=$titre_pdf&amp;id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;nom_pdf_en_detail=oui\" onclick=\"return VerifChargement()\"> Imprimer au format PDF </a>|";
echo "</p>\n";

// Affichage ou non les colonnes "commentaires"
// Affichage ou non de tous les devoirs
if (isset($_POST['ok'])) {
    if (isset($_POST['affiche_comment'])) {
        $_SESSION['affiche_comment'] = 'no';
    } else {
        $_SESSION['affiche_comment'] = 'yes';
    }
    if (isset($_POST['affiche_tous'])) {
        $_SESSION['affiche_tous'] = 'yes';
    } else {
        $_SESSION['affiche_tous'] = 'no';
    }

}
if (!isset($_SESSION['affiche_comment'])) $_SESSION['affiche_comment'] = 'yes';
if (!isset($_SESSION['affiche_tous'])) $_SESSION['affiche_tous'] = 'no';
$nb_dev_sous_cont = 0;

// Premier formulaire pour masquer ou non les colonnes "commentaires" non vides des évaluations verrouillées
if ($id_devoir == 0) {
    echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=post name=\"form1\">\n";
    echo "<fieldset style=\"padding-top: 0px; padding-bottom: 0px;  margin-left: 0px; margin-right: 100px;\">\n";
    echo "<table><tr><td>Masquer les colonnes \"commentaires\" non vides (mode visualisation uniquement) :
    </td><td><input type=\"checkbox\" name=\"affiche_comment\"  ";
    if ($_SESSION['affiche_comment'] != 'yes') echo "checked";
    echo " /></td><td><input type=\"submit\" name=\"ok\" value=\"OK\" /></td></tr>\n";
    $nb_dev_sous_cont = mysql_num_rows(mysql_query("select d.id from cn_devoirs d, cn_conteneurs c where (d.id_conteneur = c.id and c.parent='$id_conteneur')"));
    if ($nb_dev_sous_cont != 0) {
        //echo "<tr><td>Afficher les évaluations des \"sous-boîtes\" : </td><td><input type=\"checkbox\" name=\"affiche_tous\"  ";
        echo "<tr><td>Afficher les évaluations des \"sous-".htmlentities(strtolower(getSettingValue("gepi_denom_boite")))."s\" : </td><td><input type=\"checkbox\" name=\"affiche_tous\"  ";
        if ($_SESSION['affiche_tous'] == 'yes') echo "checked";
        echo " /></td><td></td></tr>\n";
    }
    echo "</table></fieldset>\n";
    echo "<input type='hidden' name='id_conteneur' value=\"".$id_conteneur."\" />\n";
    echo "<input type='hidden' name='id_devoir' value=\"".$id_devoir."\" />\n";
    echo "</form>\n";
}
// Fin du premier formulaire

// Construction de la variable $detail qui affiche dans un pop-up le mode de calcul de la moyenne
$detail = "Mode de calcul de la moyenne :\\n";
$detail = $detail."La moyenne s\\'effectue sur les colonnes repérées par les cellules de couleur violette.\\n";
if (($nb_dev_sous_cont != 0) and ($_SESSION['affiche_tous'] == 'no'))
    //$detail = $detail."ATTENTION : cliquez sur \'Afficher les évaluations des sous-boîtes\' pour faire apparaître toutes les évaluations qui interviennent dans la moyenne.\\n";
    $detail = $detail."ATTENTION : cliquez sur \'Afficher les évaluations des sous-".htmlentities(strtolower(getSettingValue("gepi_denom_boite")))."s\' pour faire apparaître toutes les évaluations qui interviennent dans la moyenne.\\n";
if ($arrondir == 's1') $detail = $detail."La moyenne est arrondie au dixième de point supérieur.\\n";
if ($arrondir == 's5') $detail = $detail."La moyenne est arrondie au demi-point supérieur.\\n";
if ($arrondir == 'se') $detail = $detail."La moyenne est arrondie au point entier supérieur.\\n";
if ($arrondir == 'p1') $detail = $detail."La moyenne est arrondie au dixième de point le plus proche.\\n";
if ($arrondir == 'p5') $detail = $detail."La moyenne est arrondie au demi-point le plus proche.\\n";
if ($arrondir == 'pe') $detail = $detail."La moyenne est arrondie au point entier le plus proche.\\n";
if ($ponderation != 0) $detail = $detail."Pondération : ".$ponderation." (s\\'ajoute au coefficient de la meilleur note de chaque élève).\\n";

// Titre
echo "<h2 class='gepi'>".$titre."</h2>\n";
if (($nb_dev == 0) and ($nb_sous_cont==0)) {

    //echo "<p class=cn>La boîte $nom_conteneur ne contient aucune évaluation. </p>\n";
    echo "<p class=cn>";
    if(getSettingValue("gepi_denom_boite_genre")=='f'){echo "La ";}else{echo "Le ";}
    echo htmlentities(strtolower(getSettingValue("gepi_denom_boite")))." $nom_conteneur ne contient aucune évaluation. </p>\n";
    echo "</body></html>\n";
    die();
}

// Début du deuxième formulaire
echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=post  name=\"form2\">\n";
if ($id_devoir != 0) echo "<center><input type='submit' value='Enregistrer' /></center>\n";

// Couleurs utilisées
$couleur_devoirs = '#AAE6AA';
$couleur_moy_cont = '#96C8F0';
$couleur_moy_sous_cont = '#FAFABE';
$couleur_calcul_moy = '#AAAAE6';
if ($id_devoir != 0) {
    //echo "<p class='cn'>Taper une note de 0 à 20 pour chaque élève, ou à défaut le code 'abs' pour 'absent', le code 'disp' pour 'dispensé', le code '-' pour absence de note.</p>\n";
    echo "<p class='cn'>Taper une note de 0 à 20 pour chaque élève, ou à défaut le code 'a' pour 'absent', le code 'd' pour 'dispensé', le code '-' ou 'n' pour absence de note.</p>\n";
    echo "<p class='cn'>Vous pouvez également <b>importer directement vos notes par \"copier/coller\"</b> à partir d'un tableur ou d'une autre application : voir tout en bas de cette page.</p>\n";

}
echo "<p class=cn><b>Enseignement : ".$current_group['description']." (" . $current_group["classlist_string"] . ")";
echo "</b></p>\n";

//=============================================================
// MODIF: boireaus
echo "
<script type='text/javascript' language='JavaScript'>

function verifcol(num_id){
    document.getElementById('n'+num_id).value=document.getElementById('n'+num_id).value.toLowerCase();
    if(document.getElementById('n'+num_id).value=='a'){
        document.getElementById('n'+num_id).value='abs';
    }
    if(document.getElementById('n'+num_id).value=='d'){
        document.getElementById('n'+num_id).value='disp';
    }
    if(document.getElementById('n'+num_id).value=='n'){
        document.getElementById('n'+num_id).value='-';
    }
    note=document.getElementById('n'+num_id).value;
    if((note!='-')&&(note!='disp')&&(note!='abs')&&(note!='')){
        //if((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0))){
        if(((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0)))||
	((note.search(/^[0-9,]+$/)!=-1)&&(note.lastIndexOf(',')==note.indexOf(',',0)))){
            if((note>20)||(note<0)){
                couleur='red';
            }
            else{
                couleur='$couleur_devoirs';
            }
        }
        else{
            couleur='red';
        }
    }
    else{
        couleur='$couleur_devoirs';
    }
    eval('document.getElementById(\'td_'+num_id+'\').style.background=couleur');
}
</script>
";
//=============================================================

$i=0;
while ($i < $nb_dev) {
    $nocomment[$i]='yes';
    $i++;
}


$i = 0;
$num_id=10;
$current_displayed_line = 0;

// On commence par mettre la liste dans l'ordre souhaité
if ($order_by != "classe") {
    $liste_eleves = $current_group["eleves"][$periode_num]["users"];
} else {
    // Ici, on tri par classe
    // On va juste créer une liste des élèves pour chaque classe
    $tab_classes = array();
    foreach($current_group["classes"]["list"] as $classe_id) {
        $tab_classes[$classe_id] = array();
    }
    // On passe maintenant élève par élève et on les met dans la bonne liste selon leur classe
    foreach($current_group["eleves"][$periode_num]["list"] as $e_login) {
        $classe = $current_group["eleves"][$periode_num]["users"][$e_login]["classe"];
        $tab_classes[$classe][$e_login] = $current_group["eleves"][$periode_num]["users"][$e_login];
    }
    // On met tout ça à la suite
    $liste_eleves = array();
    foreach($current_group["classes"]["list"] as $classe_id) {
        $liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
    }
}

$prev_classe = null;

foreach ($liste_eleves as $eleve) {
    $eleve_login[$i] = $eleve["login"];
    $eleve_nom[$i] = $eleve["nom"];
    $eleve_prenom[$i] = $eleve["prenom"];
    $eleve_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["classe"];
    $eleve_id_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["id"];
    $somme_coef = 0;

        $k=0;
        while ($k < $nb_dev) {
            $note_query = mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$eleve_login[$i]' AND id_devoir='$id_dev[$k]')");
			// ==========================
			// MODIF: boireaus 20070913
            //$eleve_statut = @mysql_result($note_query, 0, "statut");
            //$eleve_note = @mysql_result($note_query, 0, "note");
            //$eleve_comment = @mysql_result($note_query, 0, "comment");
            if($note_query){
				if(mysql_num_rows($note_query)>0){
					$eleve_statut = @mysql_result($note_query, 0, "statut");
					$eleve_note = @mysql_result($note_query, 0, "note");
					$eleve_comment = @mysql_result($note_query, 0, "comment");
				}
				else{
					$eleve_statut = "";
					$eleve_note = "";
					$eleve_comment = "";
				}
			}
			else{
				$eleve_statut = "";
				$eleve_note = "";
				$eleve_comment = "";
			}
			// ==========================
            if ($eleve_comment != '') $nocomment[$k]='no';
            $eleve_login_note = $eleve_login[$i]."_note";
            $eleve_login_comment = $eleve_login[$i]."_comment";
            if ($id_dev[$k] != $id_devoir) {
                //
                //
                $mess_note[$i][$k] = '';
                $mess_note[$i][$k] =$mess_note[$i][$k]."<td class=cn bgcolor=$couleur_devoirs><center><b>";
                if (($eleve_statut != '') and ($eleve_statut != 'v')) {
                    $mess_note[$i][$k] = $mess_note[$i][$k].$eleve_statut;
                    $mess_note_pdf[$i][$k] = $eleve_statut;
                } else if ($eleve_statut == 'v') {
                    $mess_note[$i][$k] =$mess_note[$i][$k]."&nbsp;";
                    $mess_note_pdf[$i][$k] = "";
                } else {
                    if ($eleve_note != '') {
                        $mess_note[$i][$k] =$mess_note[$i][$k].number_format($eleve_note,1, ',', ' ');
                        $mess_note_pdf[$i][$k] = number_format($eleve_note,1, ',', ' ');
                    } else {
                        $mess_note[$i][$k] =$mess_note[$i][$k]."&nbsp;";
                        $mess_note_pdf[$i][$k] = "";
                    }
                }
                $mess_note[$i][$k] =$mess_note[$i][$k]."</b></center></td>\n";
                if ($eleve_comment != '') {
                    $mess_comment[$i][$k] = "<td class=cn>".$eleve_comment."</td>\n";
                    $mess_comment_pdf[$i][$k] = $eleve_comment;

                } else {
                    $mess_comment[$i][$k] = "<td class=cn>&nbsp;</td>\n";
                    $mess_comment_pdf[$i][$k] = "";
                }
            } else {
                   $mess_note[$i][$k] = "<td class='cn' id='td_$num_id' style='background-color:$couleur_devoirs;'>";
                   if ($current_group["classe"]["ver_periode"][$eleve_id_classe[$i]][$periode_num] == "N")
                       $mess_note[$i][$k] .= "<input id=\"n".$num_id."\" onKeyDown=\"clavier(this.id,event);\" type=\"text\" size=\"4\" name=\"".$eleve_login_note."\" value=\"";
                    if ((isset($note_import[$current_displayed_line])) and  ($note_import[$current_displayed_line] != '')) {
                        $mess_note[$i][$k] =$mess_note[$i][$k].$note_import[$current_displayed_line];
                        $mess_note_pdf[$i][$k] = $note_import[$current_displayed_line];
                    } else {
                        if (($eleve_statut != '') and ($eleve_statut != 'v')) {
                            $mess_note[$i][$k] = $mess_note[$i][$k].$eleve_statut;
                            $mess_note_pdf[$i][$k] = $eleve_statut;
                        } else if ($eleve_statut == 'v') {
                            $mess_note_pdf[$i][$k] = "";
                         } else {
                            $mess_note[$i][$k] = $mess_note[$i][$k].$eleve_note;
                            $mess_note_pdf[$i][$k] = number_format($eleve_note,1, ',', ' ');
                         }
                    }
                    if ($current_group["classe"]["ver_periode"][$eleve_id_classe[$i]][$periode_num] == "N")
                        $mess_note[$i][$k] = $mess_note[$i][$k]."\" onfocus=\"javascript:this.select()\" onchange=\"verifcol($num_id);changement()\" />";
                    $mess_note[$i][$k] .= "</td>\n";
        //=========================================================
                //$mess_comment[$i][$k] = "<td class='cn' bgcolor='$couleur_devoirs'><textarea id=\"1".$num_id."\" onKeyDown=\"clavier(this.id,event);\" name='".$eleve_login_comment."' rows=1 cols=30 wrap='virtual' onchange=\"changement()\">".$eleve_comment."</textarea></td>\n";
                $mess_comment[$i][$k] = "<td class='cn' bgcolor='$couleur_devoirs'>";
                if ($current_group["classe"]["ver_periode"][$eleve_id_classe[$i]][$periode_num] == "N")
                    $mess_comment[$i][$k] .= "<textarea id=\"n1".$num_id."\" onKeyDown=\"clavier(this.id,event);\" name='".$eleve_login_comment."' rows=1 cols=30 wrap='virtual' onchange=\"changement()\">".$eleve_comment."</textarea></td>\n";
                else
                    $mess_comment[$i][$k] .= $eleve_comment."</td>\n";
        //=========================================================
                $mess_comment_pdf[$i][$k] = $eleve_comment;
                $num_id++;
            }
            $k++;
        }
        $current_displayed_line++;
    $i++;
}

//
// Affichage du tableau
//
echo "<table border='1' cellspacing='2' cellpadding='1'>\n";
//
// Première ligne
//

// on calcule le nombre de colonnes à scinder
if ($id_devoir==0) {
    // Mode consultation
    $nb_colspan = $nb_dev;
    $i = 0;
    while ($i < $nb_dev) {
        if ((($nocomment[$i]!='yes') and ($_SESSION['affiche_comment'] == 'yes')) or ($id_dev[$i] == $id_devoir)) $nb_colspan++;
        $i++;
    }
} else {
    // En mode saisie, on n'affiche que le devoir à saisir
    $nb_colspan = 2;
}

// Affichage première ligne

echo "<tr><td class='cn'>&nbsp;</td>";
if ($multiclasses) echo "<td class='cn'>&nbsp;</td>";
echo "\n";
if ($nb_dev != 0) {
    if($nom_conteneur!=""){
        echo "<th class='cn' colspan='$nb_colspan' valign='top'><center>$nom_conteneur</center></th>\n";
    }
    else{
        echo "<th class='cn' colspan='$nb_colspan' valign='top'><center>&nbsp;</center></th>\n";
    }
}

// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir==0) {
    $i=0;
    while ($i < $nb_sous_cont) {
        // on affiche les devoirs des sous-conteneurs si l'utilisateur a fait le choix de tout afficher
        if (($_SESSION['affiche_tous'] == 'yes') and ($id_devoir==0)) {
            $query_nb_dev = mysql_query("SELECT * FROM cn_devoirs where (id_conteneur='$id_sous_cont[$i]' and id_racine='$id_racine') order by date");
            $nb_dev_s_cont[$i]  = mysql_num_rows($query_nb_dev);
            $m = 0;
            while ($m < $nb_dev_s_cont[$i]) {
                $id_s_dev[$i][$m] = mysql_result($query_nb_dev, $m, 'id');
                $nom_sous_dev[$i][$m] = mysql_result($query_nb_dev, $m, 'nom_court');
                $coef_s_dev[$i][$m]  = mysql_result($query_nb_dev, $m, 'coef');
                $fac_s_dev[$i][$m]  = mysql_result($query_nb_dev, $m, 'facultatif');
                $date = mysql_result($query_nb_dev, $m, 'date');
                $annee = substr($date,0,4);
                $mois =  substr($date,5,2);
                $jour =  substr($date,8,2);
                $display_date_s_dev[$i][$m] = $jour."/".$mois."/".$annee;

                $m++;
            }
            // ===============================
            // MODIF: boireaus
            //if ($nb_dev_s_cont[$i] != 0) echo "<th class=cn colspan='$nb_dev_s_cont[$i]' valign='top'><center>$nom_sous_cont[$i]</center></th>\n";
            if($nom_sous_cont[$i]!=""){
            $cellule_nom_sous_cont=$nom_sous_cont[$i];
        }
        else{
            $cellule_nom_sous_cont="&nbsp;";
        }
            if ($nb_dev_s_cont[$i] != 0) echo "<th class=cn colspan='$nb_dev_s_cont[$i]' valign='top'><center>$cellule_nom_sous_cont</center></th>\n";
            // ===============================
        }
        if($nom_sous_cont[$i]!=""){
	        echo "<td class=cn valign='top'><center><b>$nom_sous_cont[$i]</b><br />\n";
        }
	else{
		echo "<td class=cn valign='top'><center><b>&nbsp;</b><br />\n";
        }
    if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 3)
            echo "<a href=\"./add_modif_conteneur.php?mode_navig=retour_saisie&amp;id_conteneur=$id_sous_cont[$i]&amp;id_retour=$id_conteneur\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">Configuration</a><br />\n";

        echo "<a href=\"./saisie_notes.php?id_conteneur=$id_sous_cont[$i]\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">Visualisation</a>\n";
        if ($display_bulletin_sous_cont[$i] == '1') echo "<br /><font color='red'>Aff.&nbsp;bull.</font>\n";
        echo "</center></td>\n";
        $i++;
    }
}
// En mode saisie, on n'affiche que le devoir à saisir
if (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) and ($id_devoir==0)){
    if($nom_conteneur!=""){
    echo "<td class=cn  valign='top'><center><b>$nom_conteneur</b><br />";
    }else{
    echo "<td class=cn  valign='top'><center><b>&nbsp;</b><br />";
    }
    echo "<a href=\"./add_modif_conteneur.php?mode_navig=retour_saisie&amp;id_conteneur=$id_conteneur&amp;id_retour=$id_conteneur\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">Configuration</a><br /><br /><font color='red'>Aff.&nbsp;bull.</font></center></td>\n";
}
else{
    echo "<td class=cn  valign='top'>&nbsp;</td>\n";
}
echo "</tr>\n";

// Deuxième ligne
echo "<tr>\n";
echo "<td class=cn valign='top'>&nbsp;</td>\n";
$header_pdf[] = "Evaluation :";
if ($multiclasses) $header_pdf[] = "";
$w_pdf[] = $w1;
//if ($multiclasses) $w_pdf[] = $w1b;
if ($multiclasses) echo "<td class='cn'>&nbsp;</td>\n";
if ($multiclasses) $w_pdf[] = $w2;
$i = 0;
while ($i < $nb_dev) {
    // En mode saisie, on n'affiche que le devoir à saisir
    if (($id_devoir==0) or ($id_dev[$i] == $id_devoir)) {
        if ($coef[$i] != 0) $tmp = " bgcolor = $couleur_calcul_moy "; else $tmp = '';
        $header_pdf[] = $nom_dev[$i]." (".$display_date[$i].")";
        $w_pdf[] = $w2;
        if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2)
            echo "<td class=cn".$tmp." valign='top'><center><b><a href=\"./add_modif_dev.php?mode_navig=retour_saisie&amp;id_retour=$id_conteneur&amp;id_devoir=$id_dev[$i]\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">$nom_dev[$i]</a></b><br /><font size=-2>($display_date[$i])</font></center></td>\n";
        else
            echo "<td class=cn".$tmp." valign='top'><center><b>".$nom_dev[$i]."</b><br /><font size=-2>($display_date[$i])</font></center></td>\n";
        if ((($nocomment[$i]!='yes') and ($_SESSION['affiche_comment'] == 'yes')) or ($id_dev[$i] == $id_devoir)) {
            echo "<td class=cn  valign='top'><center>Commentaire&nbsp;*</center></td>\n";
            $header_pdf[] = "Commentaire";
            //$w_pdf[] = $w2;
            //if ($multiclasses) $w_pdf[] = $w2;
            $w_pdf[] = $w3;
        }
    }
    $i++;
}

// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir==0) {
    $i=0;
    while ($i < $nb_sous_cont) {
        $tmp = '';
        if ($_SESSION['affiche_tous'] == 'yes') {
            $m = 0;
            while ($m < $nb_dev_s_cont[$i]) {
                $tmp = '';
                if (($mode == 1) and ($coef_s_dev[$i][$m] != 0)) $tmp = " bgcolor = $couleur_calcul_moy ";
                $header_pdf[] = $nom_sous_dev[$i][$m]." (".$display_date_s_dev[$i][$m].")";
                $w_pdf[] = $w2;
                echo "<td class=cn".$tmp." valign='top'><center><b><a href=\"./add_modif_dev.php?mode_navig=retour_saisie&amp;id_retour=$id_conteneur&amp;id_devoir=".$id_s_dev[$i][$m]."\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">".$nom_sous_dev[$i][$m]."</a></b><br /><font size=-2>(".$display_date_s_dev[$i][$m].")</font></center></td>\n";
                $m++;
            }
            $tmp = '';
            if (($mode == 2) and ($coef_sous_cont[$i] != 0)) $tmp = " bgcolor = $couleur_calcul_moy ";
        }
        echo "<td class=cn".$tmp." valign='top'><center>Moyenne</center></td>\n";
        $header_pdf[] = "Moyenne : ".$nom_sous_cont[$i];
        $w_pdf[] = $w2;
        $i++;
    }
}
// En mode saisie, on n'affiche que le devoir à saisir
if ($id_devoir==0) {
    echo "<td class=cn valign='top'><center><b>Moyenne</b></center></td>\n";
    $header_pdf[] = "Moyenne";
    $w_pdf[] = $w2;
}
echo "</tr>";

//
// Troisième ligne
//
echo "<tr><td class=cn valign='top'>&nbsp;</td>";
if ($multiclasses) echo "<td class='cn'>&nbsp;</td>";
echo "\n";
$i = 0;
while ($i < $nb_dev) {
    // En mode saisie, on n'affiche que le devoir à saisir
    if (($id_devoir==0) or ($id_dev[$i] == $id_devoir)) {
        if ($id_dev[$i] == $id_devoir) {
            echo "<td class=cn valign='top'><center><a href=\"saisie_notes.php?id_conteneur=$id_conteneur&amp;id_devoir=0\" onclick=\"return confirm_abandon (this, change,'$themessage')\">Visualiser</a></center></td>\n";
//            echo "<td class=cn valign='top'><center><a href=\"saisie_notes.php?id_conteneur=$id_conteneur&id_devoir=0&affiche_message=$affiche_message\" onclick=\"form2.submit(); return false;\">verrouiller</a></center></td>";
//            echo "<td class=cn valign='top'><center><a href=\"saisie_notes.php?id_conteneur=$id_conteneur&id_devoir=0&affiche_message=$affiche_message\" onclick=\"form2.submit();\">verrouiller</a></center></td>";
            echo "<td class=cn valign='top'>&nbsp;</td>\n";
        } else {
            if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2)
               echo "<td class=cn valign='top'><center><a href=\"saisie_notes.php?id_conteneur=$id_conteneur&amp;id_devoir=$id_dev[$i]\" onclick=\"return confirm_abandon (this, change,'$themessage')\">saisir</a></center></td>\n";
            else
                echo "<td class=cn valign='top'>&nbsp;</td>\n";
            if (($nocomment[$i]!='yes')  and ($_SESSION['affiche_comment'] == 'yes')) echo "<td class=cn valign='top'>&nbsp;</td>\n";
        }
    }
    $i++;
}
// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir==0) {
    $i=0;
    while ($i < $nb_sous_cont) {
        if ($_SESSION['affiche_tous'] == 'yes') {
            $m = 0;
            while ($m < $nb_dev_s_cont[$i]) {
                echo "<td class=cn valign='top'><center><a href=\"saisie_notes.php?id_conteneur=".$id_sous_cont[$i][$m]."&amp;id_devoir=".$id_s_dev[$i][$m]."\" onclick=\"return confirm_abandon (this, change,'$themessage')\">saisir</a></center></td>\n";
                $m++;
            }
        }
        echo "<td class=cn valign='top'><center>&nbsp;</center></td>\n";
        $i++;
    }
}
// En mode saisie, on n'affiche que le devoir à saisir
if ($id_devoir==0) echo "<td class='cn' valign='top'>&nbsp;</td>\n";
echo "</tr>";

//
// quatrième ligne
//
echo "<tr><td class='cn' valign='top'><b>" .
        "<a href='saisie_notes.php?id_conteneur=".$id_conteneur."&amp;id_devoir=".$id_devoir."&amp;order_by=nom'>Nom Prénom</a></b></td>";
if ($multiclasses) echo "<td><a href='saisie_notes.php?id_conteneur=".$id_conteneur."&amp;id_devoir=".$id_devoir."&amp;order_by=classe'>Classe</a></td>";
echo "\n";
$data_pdf[0][] = "Nom Prénom\Coef.";
if ($multiclasses) $data_pdf[0][] = "";
$i = 0;
while ($i < $nb_dev) {
    // En mode saisie, on n'affiche que le devoir à saisir
    if (($id_devoir==0) or ($id_dev[$i] == $id_devoir)) {
        echo "<td class='cn' valign='top'><center>coef : ".number_format($coef[$i],1, ',', ' ');
        $data_pdf[0][] = number_format($coef[$i],1, ',', ' ');
        if (($facultatif[$i] == 'B') or ($facultatif[$i] == 'N')) echo "<br />Bonus";
        echo "</center></td>\n";
        if ($id_dev[$i] == $id_devoir) {
            echo "<td class='cn' valign='top'>&nbsp;</td>\n";
            $data_pdf[0][] = "";
        } else {
            if (($nocomment[$i]!='yes') and ($_SESSION['affiche_comment'] == 'yes')) {
                echo "<td class='cn' valign='top'>&nbsp;</td>\n";
                $data_pdf[0][] = "";
            }
        }
    }
    $i++;
}

// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir==0) {
    $i=0;
    while ($i < $nb_sous_cont) {
        if ($_SESSION['affiche_tous'] == 'yes') {
            $m = 0;
            while ($m < $nb_dev_s_cont[$i]) {
                echo "<td class='cn' valign='top'><center>coef : ".number_format($coef_s_dev[$i][$m],1, ',', ' ');
                $data_pdf[0][] = number_format($coef_s_dev[$i][$m],1, ',', ' ');
                if (($fac_s_dev[$i][$m] == 'B') or ($fac_s_dev[$i][$m] == 'N')) echo "<br />Bonus";
                echo "</center></td>\n";
                $m++;
            }
        }
        if ($mode==2) {
            echo "<td class='cn' valign='top'><center>coef : ".number_format($coef_sous_cont[$i],1, ',', ' ')."</center></td>\n";
            $data_pdf[0][] = number_format($coef_sous_cont[$i],1, ',', ' ');
        } else {
            echo "<td class='cn' valign='top'><center>&nbsp;</center></td>\n";
            $data_pdf[0][] = "";
        }
        $i++;
    }
}

// En mode saisie, on n'affiche que le devoir à saisir
if ($id_devoir==0)  {
    echo "<td class='cn' valign='top'><center><a href=\"javascript:alert('".$detail."')\">Informations</a></center></td>\n";
    $data_pdf[0][] = "";
}
//echo "<td class=cn>mode = $mode<br />arrondir = $arrondir<br />";
//if ($ponderation != 0) echo "pondération = $ponderation";
//echo "</td>

echo "</tr>\n";

//
// Affichage des lignes "elèves"
//
$i = 0;
$pointer = 0;
$tot_data_pdf = 1;
$nombre_lignes = count($current_group["eleves"][$periode_num]["list"]);
while($i < $nombre_lignes) {
        $pointer++;
        $tot_data_pdf++;
        $data_pdf[$pointer][] = $eleve_nom[$i]." ".$eleve_prenom[$i];
        if ($multiclasses) $data_pdf[$pointer][] = $eleve_classe[$i];
        echo "<tr>";
        if ($eleve_classe[$i] != $prev_classe && $prev_classe != null && $order_by == "classe") {
            echo "<td class=cn style='border-top: 2px solid blue;'>$eleve_nom[$i] $eleve_prenom[$i]</td>";
            if ($multiclasses) echo "<td style='border-top: 2px solid blue;'>$eleve_classe[$i]</td>";
            echo "\n";
        } else {
            echo "<td class=cn>$eleve_nom[$i] $eleve_prenom[$i]</td>";
            if ($multiclasses) echo "<td>$eleve_classe[$i]</td>";
            echo "\n";
        }
        $prev_classe = $eleve_classe[$i];
        $k=0;
        while ($k < $nb_dev) {
            // En mode saisie, on n'affiche que le devoir à saisir
            if (($id_devoir==0) or ($id_dev[$k] == $id_devoir)) {
                echo $mess_note[$i][$k];
                $data_pdf[$pointer][] = $mess_note_pdf[$i][$k];
                if ((($nocomment[$k]!='yes') and ($_SESSION['affiche_comment'] == 'yes')) or ($id_dev[$k] == $id_devoir)) {
                    echo $mess_comment[$i][$k];
                    $data_pdf[$pointer][] = $mess_comment_pdf[$i][$k];
                }
            }
            $k++;
        }
        //
        // Affichage de la moyenne de tous les sous-conteneurs
        //

        // on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
        if ($id_devoir==0) {
            $k=0;
            while ($k < $nb_sous_cont) {
                if ($_SESSION['affiche_tous'] == 'yes') {
                    $m = 0;
                    while ($m < $nb_dev_s_cont[$k]) {
                        $temp = $id_s_dev[$k][$m];
                        $note_query = mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$eleve_login[$i]' AND id_devoir='$temp')");
                        $eleve_statut = @mysql_result($note_query, 0, "statut");
                        $eleve_note = @mysql_result($note_query, 0, "note");
                        if (($eleve_statut != '') and ($eleve_statut != 'v')) {
                            $tmp = $eleve_statut;
                            $data_pdf[$pointer][] = $eleve_statut;
                        } else if ($eleve_statut == 'v') {
                            $tmp = "&nbsp;";
                            $data_pdf[$pointer][] = "";
                        } else {
                            if ($eleve_note != '') {
                                $tmp = number_format($eleve_note,1, ',', ' ');
                                $data_pdf[$pointer][] = number_format($eleve_note,1, ',', ' ');
                            } else {
                                $tmp = "&nbsp;";
                                $data_pdf[$pointer][] = "";
                            }
                        }
                        echo "<td class='cn' bgcolor='$couleur_devoirs'><center><b>$tmp</b></center></td>\n";

                        $m++;
                    }
                }

                $moyenne_query = mysql_query("SELECT * FROM cn_notes_conteneurs WHERE (login='$eleve_login[$i]' AND id_conteneur='$id_sous_cont[$k]')");
                $statut_moy = @mysql_result($moyenne_query, 0, "statut");
                if ($statut_moy == 'y') {
                    $moy = @mysql_result($moyenne_query, 0, "note");
                    $moy = number_format($moy,1, ',', ' ');
                    $data_pdf[$pointer][] = $moy;
                } else {
                    $moy = '&nbsp;';
                    $data_pdf[$pointer][] = "";
                }
                echo "<td class='cn' bgcolor='$couleur_moy_sous_cont'><center>$moy</center></td>\n";
                $k++;
            }
        }
        //
        // affichage des moyennes du conteneur
        //
        // En mode saisie, on n'affiche que le devoir à saisir
        if ($id_devoir==0)  {
            $moyenne_query = mysql_query("SELECT * FROM cn_notes_conteneurs WHERE (login='$eleve_login[$i]' AND id_conteneur='$id_conteneur')");
            $statut_moy = @mysql_result($moyenne_query, 0, "statut");
            if ($statut_moy == 'y') {
               $moy = @mysql_result($moyenne_query, 0, "note");
               $moy = number_format($moy,1, ',', ' ');
               $data_pdf[$pointer][] = $moy;
            } else {
                $moy = '&nbsp;';
                $data_pdf[$pointer][] = "";
            }
            echo "<td class='cn' bgcolor='$couleur_moy_cont'><center><b>$moy</b></center></td>\n";
        }
        echo "</tr>\n";

    $i++;
}


//
// Dernière ligne
//
echo "<tr>";
if ($multiclasses) {
    echo "<td class=cn colspan=2>";
} else {
    echo "<td class=cn>";
}
echo "<b>Moyennes :</b></td>\n";
$w_pdf[] = $w2;
$data_pdf[$tot_data_pdf][] = "Moyennes";
if ($multiclasses) $data_pdf[$tot_data_pdf][] = "";
$k='0';
while ($k < $nb_dev) {
    // En mode saisie, on n'affiche que le devoir à saisir
    if (($id_devoir==0) or ($id_dev[$k] == $id_devoir)) {
        $call_moyenne[$k] = mysql_query("SELECT round(avg(n.note),1) moyenne FROM cn_notes_devoirs n, j_eleves_groupes j WHERE (
        j.id_groupe='$id_groupe' AND
        j.periode = '$periode_num' AND
        j.login = n.login AND
        n.statut='' AND
        n.id_devoir='$id_dev[$k]'
        )");
        $moyenne[$k] = mysql_result($call_moyenne[$k], 0, "moyenne");
        if ($moyenne[$k] != '') {
            echo "<td class='cn'><center><b>".number_format($moyenne[$k],1, ',', ' ')."</b></center></td>\n";
            $data_pdf[$tot_data_pdf][] = number_format($moyenne[$k],1, ',', ' ');

        } else {
            echo "<td class='cn'>&nbsp;</td></td>\n";
            $data_pdf[$tot_data_pdf][] = "";
        }
        if ((($nocomment[$k]!='yes') and ($_SESSION['affiche_comment'] == 'yes')) or ($id_dev[$k] == $id_devoir)) {
           echo "<td class='cn'>&nbsp;</td>\n";
           $data_pdf[$tot_data_pdf][] = "";
        }
    }
    $k++;
}
//
// Moyenne des moyennes des sous-conteneurs
//
// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir==0) {
    $k=0;
    while ($k < $nb_sous_cont) {
        if ($_SESSION['affiche_tous'] == 'yes') {
            $m = 0;
            while ($m < $nb_dev_s_cont[$k]) {
                $temp = $id_s_dev[$k][$m];
                $call_moy = mysql_query("SELECT round(avg(n.note),1) moyenne FROM cn_notes_devoirs n, j_eleves_groupes j WHERE (
                j.id_groupe='$id_groupe' AND
                j.periode = '$periode_num' AND
                j.login = n.login AND
                n.statut='' AND
                n.id_devoir='$temp'
                )");
                $moy_s_dev = mysql_result($call_moy, 0, "moyenne");
                if ($moy_s_dev != '') {
                    echo "<td class='cn'><center><b>".number_format($moy_s_dev,1, ',', ' ')."</b></center></td>\n";
                    $data_pdf[$tot_data_pdf][] = number_format($moy_s_dev,1, ',', ' ');
                } else {
                    //echo "<td class=cn>&nbsp;</td></td>";
                    echo "<td class='cn'>&nbsp;</td>\n";
                    $data_pdf[$tot_data_pdf][] = "";
                }
                $m++;
            }
        }
        $call_moy_moy = mysql_query("SELECT round(avg(n.note),1) moyenne FROM cn_notes_conteneurs n, j_eleves_groupes j WHERE (
        j.id_groupe='$id_groupe' AND
        j.login = n.login AND
        j.periode = '$periode_num' AND
        n.statut='y' AND
        n.id_conteneur='$id_sous_cont[$k]'
        )");
        $moy_moy = mysql_result($call_moy_moy, 0, "moyenne");
        if ($moy_moy != '') {
            echo "<td class='cn'><center><b>".number_format($moy_moy,1, ',', ' ')."</b></center></td>\n";
            $data_pdf[$tot_data_pdf][] = number_format($moy_moy,1, ',', ' ');
        } else {
            echo "<td class='cn'>&nbsp;</td>\n";
            $data_pdf[$tot_data_pdf][] = "";
        }
        $k++;
    }
}
//
// Moyenne des moyennes du conteneur
//
// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir==0) {

    $call_moy_moy = mysql_query("SELECT round(avg(n.note),1) moyenne FROM cn_notes_conteneurs n, j_eleves_groupes j WHERE (
    j.id_groupe='$id_groupe' AND
    j.login = n.login AND
    j.periode = '$periode_num' AND
    n.statut='y' AND
    n.id_conteneur='$id_conteneur'
    )");
    $moy_moy = mysql_result($call_moy_moy, 0, "moyenne");
    if ($moy_moy != '') {
        echo "<td class='cn'><center><b>".number_format($moy_moy,1, ',', ' ')."</b></center></td>\n";
        $data_pdf[$tot_data_pdf][] = number_format($moy_moy,1, ',', ' ');
    } else {
        echo "<td class='cn'>&nbsp;</td>\n";
        $data_pdf[$tot_data_pdf][] = "";
    }
}
echo "</tr></table>\n";

// Préparation du pdf
$header_pdf=serialize($header_pdf);
$_SESSION['header_pdf']=$header_pdf;

$w_pdf=serialize($w_pdf);
$_SESSION['w_pdf']=$w_pdf;

$data_pdf=serialize($data_pdf);
$_SESSION['data_pdf']=$data_pdf;

if ($id_devoir) echo "<input type='hidden' name='is_posted' value=\"yes\" />\n";

?>

<input type="hidden" name="id_conteneur" value="<?php echo "$id_conteneur";?>" />
<input type="hidden" name="id_devoir" value="<?php echo "$id_devoir";?>" />
<?php if ($id_devoir != 0) echo "<br /><center><div id=\"fixe\"><input type='submit' value='Enregistrer' /></div></center>\n"; ?>
</form>
<?php
if ($id_devoir) {
    echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;\">\n";
    echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=post>\n";
    echo "<h3 class='gepi'>Importation directe des notes par copier/coller à partir d'un tableur</h3>\n";
    echo "<table><tr>\n";
    echo "<td>De la ligne : ";
        echo "<SELECT name='debut_import' size='1'>\n";
    $k = 1;
    while ($k < $current_displayed_line+1) {
        echo "<option value='$k'>$k</option>\n";
        $k++;
    }
    echo "</select>\n";

    echo "<br /> à la ligne : \n";
    echo "<SELECT name='fin_import' size='1'>\n";
    $k = 1;
    while ($k < $current_displayed_line+1) {
        echo "<option value='$k'";
        if ($k == $current_displayed_line) echo " SELECTED ";
        echo ">$k</option>\n";
        $k++;
    }
    echo "</select>\n";
    echo "</td><td>\n";
    echo "Coller ci-dessous les données à importer : <br />\n";
    if (isset($_POST['notes'])) $notes = $_POST['notes']; $notes='';
    echo "<textarea name='notes' rows='3' cols='40' wrap='virtual'>$notes</textarea>\n";
    echo "</td></tr></table>\n";

    echo "<input type='hidden' name='id_conteneur' value='$id_conteneur' />\n";
    echo "<input type='hidden' name='id_devoir' value='$id_devoir' />\n";
    echo "<center><input type='submit' value='Importer'  onclick=\"return confirm_abandon (this, change, '$themessage')\" /></center>\n";
    echo "<p><b>Remarque importante :</b> l'importation ne prend en compte que les élèves dont le nom est affiché ci-dessus !<br />Soyez donc vigilant à ne coller que les notes de ces élèves, dans le bon ordre.</p>\n";
    echo "</form></fieldset>\n";
}

?>
<br />
* En conformité avec la CNIL, le professeur s'engage à ne faire figurer dans le carnet de notes que des notes et commentaires portés à la connaissance de l'élève (note et commentaire portés sur la copie, ...).
<script type="text/javascript" language="javascript">
chargement = true;

// La vérification ci-dessous est effectuée après le remplacement des notes supérieures à 20 par des zéros.
// Ces éventuelles erreurs de frappe ne sauteront pas aux yeux.
for(i=10;i<<?php echo $num_id; ?>;i++){
    eval("verifcol("+i+")");
}

// On donne le focus à la première cellule lors du chargement de la page:
if(document.getElementById('n10')){
	document.getElementById('n10').focus();
}
</script>
<?php require("../lib/footer.inc.php");?>