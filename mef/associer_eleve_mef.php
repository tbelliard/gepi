<?php
/**
 *
 * @version $Id$
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

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
}

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

//récupération des paramètres de la requète
$nom_eleve = isset($_POST["nom_eleve"]) ? $_POST["nom_eleve"] :(isset($_GET["nom_eleve"]) ? $_GET["nom_eleve"] :(isset($_SESSION["nom_eleve"]) ? $_SESSION["nom_eleve"] : NULL));
$id_eleve = isset($_POST["id_eleve"]) ? $_POST["id_eleve"] :(isset($_GET["id_eleve"]) ? $_GET["id_eleve"] :(isset($_SESSION["id_eleve"]) ? $_SESSION["id_eleve"] : NULL));
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :(isset($_SESSION["id_groupe_abs"]) ? $_SESSION["id_groupe_abs"] : NULL));
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :(isset($_SESSION["id_classe_abs"]) ? $_SESSION["id_classe_abs"] : NULL));
$id_aid = isset($_POST["id_aid"]) ? $_POST["id_aid"] :(isset($_GET["id_aid"]) ? $_GET["id_aid"] :(isset($_SESSION["id_aid"]) ? $_SESSION["id_aid"] : NULL));
$id_creneau = isset($_POST["id_creneau"]) ? $_POST["id_creneau"] :(isset($_GET["id_creneau"]) ? $_GET["id_creneau"] : NULL);
$type_selection = isset($_POST["type_selection"]) ? $_POST["type_selection"] :(isset($_GET["type_selection"]) ? $_GET["type_selection"] :(isset($_SESSION["type_selection"]) ? $_SESSION["type_selection"] : NULL));

if (isset($id_groupe) && $id_groupe != null) $_SESSION['id_groupe_abs'] = $id_groupe;
if (isset($id_classe) && $id_classe != null) $_SESSION['id_classe_abs'] = $id_classe;
if (isset($id_aid) && $id_aid != null) $_SESSION['id_aid'] = $id_aid;
if (isset($nom_eleve) && $nom_eleve != null) $_SESSION['nom_eleve'] = $nom_eleve;
if (isset($id_eleve) && $id_eleve != null) $_SESSION['id_eleve'] = $id_eleve;
if (isset($type_selection) && $type_selection != null) $_SESSION['type_selection'] = $type_selection;


//initialisation des variables
if ($type_selection == 'id_groupe') {
    $current_groupe = GroupeQuery::create()->findPk($id_groupe);
} else if ($type_selection == 'id_aid') {
    $current_aid = AidDetailsQuery::create()->findPk($id_aid);
} else if ($type_selection == 'id_classe') {
    $current_classe = ClasseQuery::create()->findPk($id_classe);
} else {
}

$titre_page = "MEF";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
$javascript_specifique[] = "mod_abs2/lib/include";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<table cellspacing='15px' cellpadding='5px'><tr>";

//on affiche une boite de selection pour l'eleve
echo "<td style='border : 1px solid; padding : 10 px;'>";
echo "<form action=\"\" method=\"post\" style=\"width: 100%;\">\n";
echo '<p>';
echo 'Nom : <input type="hidden" name="type_selection" value="nom_eleve"/> ';
echo '<input type="text" name="nom_eleve" size="10" value="'.$nom_eleve.'"/> ';
echo '<button type="submit">Rechercher</button>';
echo '</p>';
echo '</form>';
echo '</td>';



//on affiche une boite de selection avec les classes
$classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
if (!$classe_col->isEmpty()) {
	echo "<td style='border : 1px solid; padding : 10 px;'>";
	echo "<form action=\"\" method=\"post\" style=\"width: 100%;\">\n";
	echo '<p>';
	echo '<input type="hidden" name="type_selection" value="id_classe"/>';
	echo ("Classe : <select name=\"id_classe\" style=\"width:160px\">");
	echo "<option value='-1'>choisissez une classe</option>\n";
	foreach ($classe_col as $classe) {
		echo "<option value='".$classe->getId()."'";
		if ($id_classe == $classe->getId()) echo " selected='selected' ";
		echo ">";
		echo $classe->getNom();
		echo "</option>\n";
	}
	echo "</select>&nbsp;";

	echo '<button type="submit">Afficher les élèves</button>';
	echo '</p>';
	echo "</form>";
	echo "</td>";
} else {
    echo '<td>Aucune classe avec élève affecté n\'a été trouvée</td>';
}

echo "</tr></table>";

if (isset($message_enregistrement)) {
    echo($message_enregistrement);
}

//afichage des eleves
$eleve_col = new PropelCollection();
if ($type_selection == 'id_eleve') {
    $query = EleveQuery::create();
    $eleve = $query->filterByIdEleve($id_eleve)->findOne();
    if ($eleve != null) {
	$eleve_col->append($eleve);
    }
} else if ($type_selection == 'nom_eleve') {
    $query = EleveQuery::create();
    $eleve_col = $query->filterByNomOrPrenomLike($nom_eleve)->limit(20)->find();
} elseif (isset($current_groupe) && $current_groupe != null) {
    $eleve_col = $current_groupe->getEleves();
} elseif (isset($current_aid) && $current_aid != null) {
    $eleve_col = $current_aid->getEleves();
} elseif (isset($current_classe) && !$current_classe == null) {
    $eleve_col = $current_classe->getEleves();
}

if (!$eleve_col->isEmpty()) {
?>
    <div class="centre_tout_moyen" style="width : 940px;">
		<form method="post" action="enregistrement_eleve_mef.php" id="liste_mef_eleve">
<p>
		    <input type="hidden" name="total_eleves" value="<?php echo($eleve_col->count()); ?>"/>
</p>
			<p class="choix_fin">
				<input value="Enregistrer" name="Valider" type="submit"  onclick="this.form.submit();this.disabled=true;this.value='En cours'" />
			</p>

<!-- Afichage du tableau de la liste des élèves -->
<!-- Legende du tableau-->
	<?php echo ('<p>');
	    $mef_collection = MefQuery::create()->find();
            echo ("MEF : <select name=\"id_mef\" class=\"small\">");
            echo "<option value='-1'></option>\n";
            foreach ($mef_collection as $mef) {
                    echo "<option value='".$mef->getId()."'>";
                    echo $mef->getLibelleEdition().' ';
                    echo $mef->getExtId();
                    echo "</option>\n";
            }
            echo "</select>";
	    echo '</p> ';
	?>
<!-- Fin de la legende -->
<p><input type="hidden" name="total_eleves" value="<?php echo $eleve_col->count()?>" /></p>
<table><tr><td style="vertical-align : top;">
	<table style="width:750px;" >
		<tbody>
			<tr class="titre_tableau_gestion" style="white-space: nowrap;">
				<th style="text-align : center;" abbr="élèves">Liste des &eacute;l&egrave;ves.
				Sélectionner :
				<a href="#" onclick="SetAllCheckBoxes('liste_mef_eleve', 'active_mef_eleve[]', '', true); return false;">Tous</a>
				<a href="#" onclick="SetAllCheckBoxes('liste_mef_eleve', 'active_mef_eleve[]', '', false); return false;">Aucun</a>
				</th>
				<th style="text-align : center;">MEF actuel</th>
				<th style="text-align : center;">modifier</th>
				<!--th></th>
				<th></th-->
			</tr>
<?php
//echo '<input type="hidden" name="total_eleves" value="'.$eleve_col->count().'" />';
foreach($eleve_col as $eleve) {
			if ($eleve_col->getPosition() %2 == '1') {
				$background_couleur="#E8F1F4";
			} else {
				$background_couleur="#C6DCE3";
			}
			echo "<tr style='background-color :$background_couleur'>\n";
?>
			<td style="width:580px;" >
			<p>
				<input type="hidden" name="id_eleve_mef[<?php echo $eleve_col->getPosition(); ?>]" value="<?php echo $eleve->getIdEleve(); ?>" />
<?php
		  echo '<span>'.strtoupper($eleve->getNom()).' '.ucfirst($eleve->getPrenom()).' ('.$eleve->getCivilite().')';
			if(!isset($current_classe)){
                            echo ' '.$eleve->getClasse()->getNom().'';
                        }
                        echo'</span>';
                        //echo '</a>';
			if (isset($message_erreur_eleve[$eleve->getIdEleve()]) && $message_erreur_eleve[$eleve->getIdEleve()] != '') {
			    echo "<br/>Erreur : ".$message_erreur_eleve[$eleve->getIdEleve()];
			}
			echo("</p></td>");


			echo '<td style="vertical-align: top;"><p>';
			if ($eleve->getMEF() != null) {
			    echo $eleve->getMEF()->getLibelleEdition();
                        }
			echo '</p></td> ';

                        echo '<td style="vertical-align: top;"><input style="font-size:88%;" name="active_mef_eleve[]" value="'.$eleve->getPrimaryKey().'" type="checkbox"';
			if ($eleve_col->count() == 1) {
			    echo "checked=\"checked\" ";
			}
			echo '/>';
			echo '</td> ';

			echo("<td style='vertical-align: top;'>");
			// Avec ou sans photo
			if ((getSettingValue("active_module_trombinoscopes")=='y')) {
			    $nom_photo = $eleve->getNomPhoto(1);
			    //$photos = "../photos/eleves/".$nom_photo;
			    $photos = $nom_photo;
			   // if (($nom_photo == "") or (!(file_exists($photos)))) {
			    if (($nom_photo == NULL) or (!(file_exists($photos)))) {
				    $photos = "../mod_trombinoscopes/images/trombivide.jpg";
			    }
			    $valeur = redimensionne_image_petit($photos);
			    ?>
		      <div style="float: left;"><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" />
		      </div>
<?php
			}
			echo '<div style="float: left;">';
			if ($utilisateur->getAccesFicheEleve($eleve)) {
			    //on est pas sur que le statut autre a acces a l'onglet abs de la fiche donc on affiche pas cet onglet au chargement
				echo '<a href="javascript:centrerpopup(\'../eleves/visu_eleve.php?ele_login='.$eleve->getLogin().'\',600,550,\'scrollbars=yes,statusbar=no,resizable=yes\');">
				    Voir fiche</a>';
			}
			echo '</div>';
			echo '</td>';
echo "</tr>";
}

echo "</tbody></table>";
echo "</td>";

echo "</tr>";
echo "</table>";
echo "</form>";
echo "</div>\n";
}
//echo "</div>\n";

require_once("../lib/footer.inc.php");

//fonction redimensionne les photos petit format
function redimensionne_image_petit($photo)
 {
    // prendre les informations sur l'image
    $info_image = getimagesize($photo);
    // largeur et hauteur de l'image d'origine
    $largeur = $info_image[0];
    $hauteur = $info_image[1];
    // largeur et/ou hauteur maximum à afficher
             $taille_max_largeur = 45;
             $taille_max_hauteur = 45;

    // calcule le ratio de redimensionnement
     $ratio_l = $largeur / $taille_max_largeur;
     $ratio_h = $hauteur / $taille_max_hauteur;
     $ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

    // définit largeur et hauteur pour la nouvelle image
     $nouvelle_largeur = $largeur / $ratio;
     $nouvelle_hauteur = $hauteur / $ratio;

   // on renvoit la largeur et la hauteur
    return array($nouvelle_largeur, $nouvelle_hauteur);
 }
?>
