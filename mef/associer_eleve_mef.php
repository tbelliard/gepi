<?php
/**
*
*
* Copyright 2010-2014 Josselin Jacquard, Stephane Boireau
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

$photo_redim_taille_max_largeur=45;
$photo_redim_taille_max_hauteur=45;

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

$motif_filtrage_classe=isset($_POST['motif_filtrage_classe']) ? preg_replace("/^[^A-Za-z0-9 _]*$/", "%", $_POST['motif_filtrage_classe']) : "";
$type_filtrage_classe=isset($_POST['type_filtrage_classe']) ? $_POST['type_filtrage_classe'] : "%";
if($type_filtrage_classe!="") {
	$type_filtrage_classe=preg_replace("/^[^A-Za-z0-9 _%]*$/", "%", $_POST['type_filtrage_classe']);
}
$afficher_tous_eleves=isset($_POST['afficher_tous_eleves']) ? $_POST['afficher_tous_eleves'] : "y";

//debug_var();

//initialisation des variables
if ($type_selection == 'id_groupe') {
	$current_groupe = GroupeQuery::create()->findPk($id_groupe);
} else if ($type_selection == 'id_aid') {
	$current_aid = AidDetailsQuery::create()->findPk($id_aid);
} else if ($type_selection == 'id_classe') {
	$current_classe = ClasseQuery::create()->findPk($id_classe);
} else if ($type_selection == 'classe_filtre') {
	$tab_ele=array();
	$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec, classes c WHERE c.id=jec.id_classe AND jec.login=e.login AND c.classe LIKE '".$type_filtrage_classe.$motif_filtrage_classe."%' ORDER BY c.classe, e.nom, e.prenom;";
	//echo "$sql<br />";
	$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ele)>0) {
		$cpt_ele=0;
		while($lig_ele=mysqli_fetch_object($res_ele)) {
			$tab_ele[$cpt_ele]['id_eleve']=$lig_ele->id_eleve;
			/*
			$tab_ele[$cpt_ele]['login']=$lig_ele->login;
			$tab_ele[$cpt_ele]['prenom']=$lig_ele->prenom;
			$tab_ele[$cpt_ele]['nom']=$lig_ele->nom;
			*/
			$cpt_ele++;
		}
	}
} else {
}

$titre_page = "MEF";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
$javascript_specifique[] = "mod_abs2/lib/include";

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<table style='border-spacing: 15px;' >
	<tr>";

//on affiche une boite de selection pour l'eleve
echo "
		<td style='padding : 5px;'>
			<form action=\"#\" method=\"post\" style=\"width: 100%;\">
				<fieldset class='fieldset_opacite50'>
					<p>
						<label for=\"nom_eleve\">Nom</label> : <input type=\"hidden\" name=\"type_selection\" value=\"nom_eleve\"/> 
						<input type=\"text\" name=\"nom_eleve\" id=\"nom_eleve\" size=\"10\" value=\"".$nom_eleve."\"/> 
						<button type=\"submit\">Rechercher</button>
					</p>
				</fieldset>
			</form>
		</td>";

//on affiche une boite de selection avec les classes
$classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
if (!$classe_col->isEmpty()) {
	echo "
		<td style='padding : 5px;'>
			<form action=\"#\" method=\"post\" style=\"width: 100%;\">
				<fieldset class='fieldset_opacite50'>
					<p>
						<input type=\"hidden\" name=\"type_selection\" value=\"id_classe\"/>
						<label for='id_classe'>Classe</label> : 
						<select id=\"id_classe\" name=\"id_classe\" style=\"width:160px\">
							<option value='-1'>choisissez une classe</option>";
	foreach ($classe_col as $classe) {
		echo "
							<option value='".$classe->getId()."'";
		if ($id_classe == $classe->getId()) {echo " selected='selected' ";}
		echo ">".$classe->getNom()."</option>";
	}
	echo "
						</select><br />
						<input type='checkbox' name='afficher_tous_eleves' id='afficher_tous_eleves' value='n' ".(($afficher_tous_eleves=="n") ? " checked " : "")."/><label for='afficher_tous_eleves'> N'afficher que les élèves dont le MEF est vide</label><br />
						<button type=\"submit\">Afficher les élèves</button>
					</p>
				</fieldset>
			</form>
		</td>

		<td style='padding : 5px;'>
			<form action=\"#\" method=\"post\" style=\"width: 100%;\">
				<fieldset class='fieldset_opacite50'>
					<p>
						<input type=\"hidden\" name=\"type_selection\" value=\"classe_filtre\"/>
						Le nom de la classe 
						<select id=\"type_filtrage_classe\" name=\"type_filtrage_classe\">
							<option value='%'>contient</option>
							<option value=''".(($type_filtrage_classe=="") ? " selected " : "").">commence par</option>
						</select> 
						<input type=\"text\" name=\"motif_filtrage_classe\" value=\"".$motif_filtrage_classe."\"/><br />
						<input type='checkbox' name='afficher_tous_eleves' id='afficher_tous_eleves2' value='n' ".(($afficher_tous_eleves=="n") ? " checked " : "")."/><label for='afficher_tous_eleves2'> N'afficher que les élèves dont le MEF est vide</label><br />
						<button type=\"submit\">Afficher les élèves</button>
					</p>
				</fieldset>
			</form>
		</td>";
} else {
	echo '
		<td>Aucune classe avec élève affecté n\'a été trouvée</td>';
}

echo "
	</tr>
</table>";

if (isset($message_enregistrement)) {
	echo($message_enregistrement);
}

//afichage des eleves
$eleve_col = new PropelCollection();
if ($type_selection == 'id_eleve') {
	$query = EleveQuery::create();
	$eleve = $query->filterById($id_eleve)->findOne();
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
elseif ($type_selection == 'classe_filtre') {
	for($loop=0;$loop<count($tab_ele);$loop++) {
		$id_eleve=$tab_ele[$loop]['id_eleve'];
		$query = EleveQuery::create();
		$eleve = $query->filterById($id_eleve)->findOne();
		if ($eleve != null) {
			$eleve_col->append($eleve);
		}
	}
}

if (!$eleve_col->isEmpty()) {

	echo '
<div class="centre_tout_moyen" style="width : 940px;">
	<form method="post" action="enregistrement_eleve_mef.php" id="liste_mef_eleve">
		<p>
			<input type="hidden" name="total_eleves" value="'.$eleve_col->count().'"/>
		</p>

		<p class="choix_fin">
			<input value="Enregistrer" name="Valider" type="submit"  onclick="this.form.submit();this.disabled=true;this.value=\'En cours\'" />
		</p>

		<!-- Afichage du tableau de la liste des élèves -->
		<!-- Legende du tableau-->
		<p>';

	$mef_collection = MefQuery::create()->find();
	echo "
			<label for=\"id_mef\">MEF</label> : 
			<select id=\"id_mef\" name=\"id_mef\" class=\"small\">
				<option value='-1'></option>";
	foreach ($mef_collection as $mef) {
		echo "
				<option value='".$mef->getId()."'>".$mef->getLibelleEdition()." (".$mef->getMefCode().")</option>";
	}
	echo "
			</select>
		</p> 

		<!-- Fin de la legende -->
		<p><input type=\"hidden\" name=\"total_eleves\" value=\"".$eleve_col->count()."\" /></p>
		<table class='joss_alt'>
			<tr>
				<td style=\"vertical-align : top;\">
					<table style=\"width:750px;\" class='sortable resizable'>
						<tbody>
							<tr class=\"titre_tableau_gestion\" style=\"white-space: nowrap;\">
								<th style=\"text-align : center;\" class='text' title='Cliquer pour trier'>Liste des &eacute;l&egrave;ves. 
									Sélectionner :
									<a href=\"#\" onclick=\"SetAllCheckBoxes('liste_mef_eleve', 'active_mef_eleve[]', '', true); return false;\">Tous</a> 
									<a href=\"#\" onclick=\"SetAllCheckBoxes('liste_mef_eleve', 'active_mef_eleve[]', '', false); return false;\">Aucun</a>
								</th>
								<th style=\"text-align : center;\" class='text' title='Cliquer pour trier'>Classe</th>
								<th style=\"text-align : center;\" class='text' title='Cliquer pour trier'>MEF actuel</th>
								<th style=\"text-align : center;\">modifier</th>
								<th> </th>
							</tr>";

	foreach($eleve_col as $eleve) {

		if(($afficher_tous_eleves!="n")||($eleve->getMEF() == null)) {

			if ($eleve_col->getPosition() %2 == '1') {
				$background_couleur="#E8F1F4";
			} else {
				$background_couleur="#C6DCE3";
			}
			echo "
							<!--tr style='background-color :$background_couleur'-->
							<tr>
								<td style=\"width:580px;\" >
									<p>
										<input type=\"hidden\" name=\"id_eleve_mef[".$eleve_col->getPosition()."]\" value=\"".$eleve->getId()."\" />
										<label for='active_mef_".$eleve->getPrimaryKey()."'><span>".strtoupper($eleve->getNom()).' '.ucfirst($eleve->getPrenom()).' ('.$eleve->getCivilite().')';
		echo "</span></label>";
			if (isset($message_erreur_eleve[$eleve->getId()]) && $message_erreur_eleve[$eleve->getId()] != '') {
				echo "
										<br/>Erreur : ".$message_erreur_eleve[$eleve->getId()];
			}
			echo "
									</p>
								</td>
								<td style=\"width:580px;\" >
									".$eleve->getClasse()->getNom()."
								</td>
								<td style=\"vertical-align: top;\">
									<p>";
			if ($eleve->getMEF() != null) {
				echo $eleve->getMEF()->getLibelleEdition();
			}
			echo '</p>
								</td>
								<td style="vertical-align: top;">
									<label for="active_mef_'.$eleve->getPrimaryKey().'" class="invisible">mef de '.$eleve->getPrimaryKey().'</label>
									<input style="font-size:88%;" id="active_mef_'.$eleve->getPrimaryKey().'" name="active_mef_eleve[]" value="'.$eleve->getPrimaryKey().'" type="checkbox"';
			if ($eleve_col->count() == 1) {
				echo "checked=\"checked\" ";
			}
			echo '/>
								</td>
								<td style="vertical-align: top;">';

			// Avec ou sans photo
			if ((getSettingValue("active_module_trombinoscopes")=='y')) {
				$nom_photo = $eleve->getNomPhoto(1);
				$photos = $nom_photo;
				if (($nom_photo == NULL) or (!(file_exists($photos)))) {
					$photos = "../mod_trombinoscopes/images/trombivide.jpg";
				}
				$valeur = redimensionne_image_petit($photos);

				echo "
									<div style='float: left;'>
										<img src='".$photos."' style='width: ".$valeur[0]."px; height: ".$valeur[1]."px; border: 0px' alt='' title='' />
									</div>";
			}
			echo '
									<div style="float: left;">';
			if ($utilisateur->getAccesFicheEleve($eleve)) {
				//on est pas sur que le statut autre a acces a l'onglet abs de la fiche donc on affiche pas cet onglet au chargement
				echo '<a href="javascript:centrerpopup(\'../eleves/visu_eleve.php?ele_login='.$eleve->getLogin().'\',600,550,\'scrollbars=yes,statusbar=no,resizable=yes\');">Voir fiche</a>';
			}
			echo '</div>
								</td>
							</tr>';
		}
	}

	echo "
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	</form>
</div>\n";
}

require_once("../lib/footer.inc.php");

?>
