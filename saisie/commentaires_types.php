<?php
/*
 *
 * Copyright 2001-2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Begin standart header
$titre_page = "Saisie de commentaires-types";

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

// Check access
// INSERT INTO droits VALUES ('/saisie/commentaires_types.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'Saisie de commentaires-types', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//==========================================
// End standart header
require_once("../lib/header.inc.php");
if (!loadSettings()) {
	die("Erreur chargement settings");
}
//==========================================

$sql="CREATE TABLE IF NOT EXISTS `commentaires_types` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`commentaire` TEXT NOT NULL ,
`num_periode` INT NOT NULL ,
`id_classe` INT NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$resultat_creation_table=mysqli_query($GLOBALS["___mysqli_ston"], $sql);


function get_classe_from_id($id){
	//$sql="SELECT * FROM classes WHERE id='$id_classe[0]'";
	$sql="SELECT * FROM classes WHERE id='$id'";
	$resultat_classe=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($resultat_classe)!=1){
		//echo "<p>ERREUR! La classe d'identifiant '$id_classe[0]' n'a pas pu être identifiée.</p>";
		echo "<p>ERREUR! La classe d'identifiant '$id' n'a pas pu être identifiée.</p>";
	}
	else{
		$ligne_classe=mysqli_fetch_object($resultat_classe);
		$classe=$ligne_classe->classe;
		return $classe;
	}
}

?>

<p class="bold"><a href="../accueil.php">Retour</a>
 | <a href="commentaires_types.php">Saisir des commentaires</a>
 | <a href="commentaires_types.php?recopie=oui">Recopier des commentaires</a>
</p>

<?php
/*
if ((($_SESSION['statut']=='professeur') AND ((getSettingValue("GepiProfImprBul")!='yes') OR ((getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")!='yes')))) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")!='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")!='yes')))
{
	die("Droits insuffisants pour effectuer cette opération");
}
*/
if ((($_SESSION['statut']=='professeur') AND (getSettingValue("CommentairesTypesPP")=='yes') AND (mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."'"))>0))
	OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("CommentairesTypesScol")=='yes'))
	OR (($_SESSION['statut']=='cpe') AND (getSettingValue("CommentairesTypesCpe")=='yes'))
	)
{
	// Accès autorisé à la page
}
else{
	die("Droits insuffisants pour effectuer cette opération");
}

?>


<form name="formulaire" action="commentaires_types.php" method="post">

<?php
	echo add_token_field();

	//echo "\$_GET['recopie']=".$_GET['recopie']."<br />";
	$recopie=isset($_GET['recopie']) ? $_GET['recopie'] : (isset($_POST['recopie']) ? $_POST['recopie'] : "");

	//echo "\$recopie=$recopie<br />";

	if($recopie!="oui"){

		// =============================================
		// Définition/modification des commentaires-type
		// =============================================

		if(!isset($_POST['id_classe'])){

			// Choix de la classe

			//echo "<p>Pour quelle classe et quelles périodes souhaitez-vous définir/modifier les commentaires-type?</p>\n";
			echo "<p>Pour quelle classe souhaitez-vous définir/modifier les commentaires-type?</p>\n";
			echo "<blockquote>\n";

			// A REVOIR: Il ne faut lister que les classes appropriées.
			//$sql="select distinct id,classe from classes order by classe";
			// if ((($_SESSION['statut']=='professeur') AND (getSettingValue("CommentairesTypesPP")=='yes') AND (mysql_num_rows(mysql_query("SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."'"))>0))
			// OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("CommentairesTypesScol")=='yes')))
			if($_SESSION['statut']=='professeur'){
				$sql="SELECT DISTINCT c.id,c.classe FROM j_eleves_classes jec, classes c, j_eleves_professeurs jep
									WHERE jec.id_classe=c.id AND
										jec.login=jep.login AND
										jep.professeur='".$_SESSION['login']."'
									ORDER BY c.classe";
			}
			elseif($_SESSION['statut']=='scolarite'){
				$sql="SELECT DISTINCT c.id,c.classe FROM j_scol_classes jsc, classes c

									WHERE jsc.id_classe=c.id AND
										jsc.login='".$_SESSION['login']."'
									ORDER BY c.classe";
			}
			elseif(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiRubConseilCpe'))) {
				$sql="SELECT DISTINCT c.id,c.classe FROM j_eleves_cpe jecpe, j_eleves_classes jec, classes c
									WHERE jec.id_classe=c.id AND
									jec.login=jecpe.e_login AND
										jecpe.cpe_login='".$_SESSION['login']."'
									ORDER BY c.classe";
			}
			elseif(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiRubConseilCpeTous'))) {
				$sql="select distinct id,classe from classes order by classe";
			}
			else {
				// CA NE DEVRAIT PAS ARRIVER...
				//$sql="select distinct id,classe from classes order by classe";
				echo "<p>Statut incorrect.</p>\n";
				die();
				require("../lib/footer.inc.php");
			}

			$resultat_classes=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($resultat_classes)==0){
				echo "<p>Aucune classe n'est encore définie...</p>\n</form>\n</body>\n</html>\n";
				exit();
			}

			/*
			$cpt=0;
			while($ligne_classe=mysql_fetch_object($resultat_classes)){
				if($cpt==0){
					$checked="checked ";
				}
				else{
					$checked="";
				}
				//echo "<input type='radio' name='id_classe' value='$ligne_classe->id' $checked/> $ligne_classe->classe<br />\n";
				echo "<input type='radio' name='id_classe' id='id_classe".$ligne_classe->id."' value='$ligne_classe->id' $checked/><label for='id_classe".$ligne_classe->id."' style='cursor: pointer;'> $ligne_classe->classe</label><br />\n";
				$cpt++;
			}
			*/

			$nb_classes=mysqli_num_rows($resultat_classes);
			$nb_class_par_colonne=round($nb_classes/3);
			echo "<table width='100%'>\n";
			echo "<tr valign='top' align='center'>\n";
			$cpt=0;
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
			while($ligne_classe=mysqli_fetch_object($resultat_classes)){
				if(($cpt>0)&&(round($cpt/$nb_class_par_colonne)==$cpt/$nb_class_par_colonne)){
					echo "</td>\n";
					//echo "<td style='padding: 0 10px 0 10px'>\n";
					echo "<td align='left'>\n";
				}

				if($cpt==0){
					$checked="checked ";
				}
				else{
					$checked="";
				}

				//echo "<input type='radio' name='id_classe' value='$ligne_classe->id' /> $ligne_classe->classe<br />\n";
				echo "<input type='radio' name='id_classe' id='id_classe".$ligne_classe->id."' value='$ligne_classe->id' $checked/><label for='id_classe".$ligne_classe->id."' style='cursor: pointer;'> $ligne_classe->classe</label><br />\n";
				$cpt++;
			}
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";







			echo "</blockquote>\n";
			echo "<center><input type='submit' name='ok' value='Valider' /></center>\n";
		}
		else{
			if(!isset($_POST['num_periode'])){

				// ==================
				// Choix des périodes
				// ==================

				// Récupération des variables:
				$id_classe=$_POST['id_classe'];
				//echo "\$id_classe=$id_classe<br />\n";

				echo "<h2>Saisie/Modification des commentaires-types pour la classe de ".get_classe_from_id($id_classe)."</h2>\n";

				// Rappel des commentaires-type saisis pour cette classe sur toutes les périodes définies:

				$sql="select * from periodes where id_classe='$id_classe' order by num_periode";
				//echo "$sql<br />";
				$resultat_num_periode=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($resultat_num_periode)==0){
					echo "Aucune période n'est encore définie pour cette classe...<br />\n";
					echo "</body>\n</html>\n";
					exit();
				}
				else{
					echo "<p>Voici les commentaires-type actuellement saisis pour cette classe:</p>\n";
					echo "<ul>\n";
					while($ligne_periode=mysqli_fetch_object($resultat_num_periode)){
					//for($i=0;$i<count($num_periode);$i++){
						echo "<li>\n";
						//$sql="select nom_periode from periodes where num_periode='$ligne_periode->num_periode'";
						//$resultat_periode=mysql_query($sql);
						//$ligne_nom_periode=mysql_fetch_object($resultat_periode);
						//echo "<p><b>$ligne_nom_periode->nom_periode</b>:</p>\n";
						echo "<p><b>$ligne_periode->nom_periode</b>:</p>\n";

						// AFFICHER LES COMMENTAIRES-TYPE POUR CHAQUE PERIODE
						$sql="select * from commentaires_types where id_classe='$id_classe' and num_periode='$ligne_periode->num_periode' order by commentaire";
						$resultat_commentaires=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($resultat_commentaires)>0){
							echo "<ul>\n";
							while($ligne_commentaires=mysqli_fetch_object($resultat_commentaires)){
								echo "<li>".stripslashes(nl2br(trim($ligne_commentaires->commentaire)))."</li>\n";
							}
							echo "</ul>\n";
						}
						else{
							echo "<p style='color:red;'>Aucun commentaire-type n'est saisi pour cette classe sur cette période.</p>\n";
						}
						echo "</li>\n";
					}
					echo "</ul>\n";
				}



				// Choix des périodes:

				echo "<p>Pour quelles périodes souhaitez-vous définir/modifier les commentaires-type?</p>\n";
				//echo "<p>\n";
				echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";

				// Récupération du nom de la classe
				$sql="select * from classes where id='$id_classe'";
				$resultat_classe=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($resultat_classe)==0){
					echo "<p>L'identifiant de la classe semble erroné.</p>\n</form>\n</body>\n</html>\n";
					exit();
				}
				$ligne_classe=mysqli_fetch_object($resultat_classe);
				$classe_courante="$ligne_classe->classe";
				echo "<p><b>$classe_courante</b>: ";

				$sql="select * from periodes where id_classe='$id_classe' order by num_periode";
				//echo "$sql<br />";
				$resultat_num_periode=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($resultat_num_periode)==0){
					echo "Aucune période n'est encore définie pour cette classe...<br />\n";
				}
				else{
					/*
					$ligne_num_periode=mysql_fetch_object($resultat_num_periode);
					$sql="select * from periodes where num_periode='$ligne_num_periode->num_periode'";
					$resultat_periode=mysql_query($sql);
					$ligne_periode=mysql_fetch_object($resultat_periode);
					//echo "<input type='checkbox' name='num_periode[]' value='$ligne_periode->num_periode'> $ligne_periode->nom_periode\n";
					echo "<input type='checkbox' name='num_periode[]' id='num_periode_".$ligne_periode->num_periode."' value='$ligne_periode->num_periode' /><label for='num_periode_".$ligne_periode->num_periode."' style='cursor: pointer;'> $ligne_periode->nom_periode</label>\n";

					while($ligne_num_periode=mysql_fetch_object($resultat_num_periode)){
						//$cpt++;
						$sql="select * from periodes where num_periode='$ligne_num_periode->num_periode'";
						$resultat_periode=mysql_query($sql);
						$ligne_periode=mysql_fetch_object($resultat_periode);
						//echo " &nbsp;&nbsp;&nbsp;- <input type='checkbox' name='num_periode[]' value='$ligne_periode->num_periode'> $ligne_periode->nom_periode\n";
						echo " &nbsp;&nbsp;&nbsp;- <input type='checkbox' name='num_periode[]' id='num_periode_".$ligne_periode->num_periode."' value='$ligne_periode->num_periode' /><label for='num_periode_".$ligne_periode->num_periode."' style='cursor: pointer;'> $ligne_periode->nom_periode</label>\n";
					}
					*/
					$cpt_per=0;
					while($ligne_num_periode=mysqli_fetch_object($resultat_num_periode)){
						if($cpt_per>0) {echo " &nbsp;&nbsp;&nbsp;- ";}
						echo "<input type='checkbox' name='num_periode[]' id='num_periode_".$ligne_num_periode->num_periode."' value='$ligne_num_periode->num_periode' /><label for='num_periode_".$ligne_num_periode->num_periode."' style='cursor: pointer;'> $ligne_num_periode->nom_periode</label>\n";
						$cpt_per++;
					}

					echo "<br />\n";
				}
				echo "</p>\n";
				echo "<center><input type='submit' name='ok' value='Valider' /></center>\n";
			}
			else {
				check_token(false);

				// ==============================================================
				// Saisie, modification, suppression, validation des commentaires
				// ==============================================================

				// Récupération des variables:
				$id_classe=$_POST['id_classe'];
				$num_periode=$_POST['num_periode'];
				$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : "";


				/*
				$nom_log = "app_eleve_".$k."_".$i;

				//echo "\$nom_log=$nom_log<br />";

				if (isset($NON_PROTECT[$nom_log])){
					$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
				}
				else{
					$app = "";
				}
				*/

				$compteur_nb_commentaires=isset($_POST['compteur_nb_commentaires']) ? $_POST['compteur_nb_commentaires'] : NULL;

				//if(isset($_POST['commentaire_1'])){
				if(isset($compteur_nb_commentaires)){
				//if(isset($_POST['commentaire'])){
					// Récupération des variables:
					//$commentaire=$_POST['commentaire'];
					//$commentaire=html_entity_decode($_POST['commentaire']);

					// Nettoyage des commentaires déjà saisis pour cette classe et ces périodes:
					$sql="delete from commentaires_types where id_classe='$id_classe' and (num_periode='$num_periode[0]'";

					for($i=1;$i<count($num_periode);$i++){
						$sql=$sql." or num_periode='$num_periode[$i]'";
					}
					$sql=$sql.")";
					//echo "sql=$sql<br />";
					$resultat_nettoyage=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

					// Validation des saisies/modifs...
					//for($i=1;$i<=count($commentaire);$i++){
					for($i=1;$i<=$compteur_nb_commentaires;$i++){
						//echo "\$suppr[$i]=$suppr[$i]<br />";
						//if(($suppr[$i]=="")&&($commentaire[$i]!="")){

						//if((!isset($suppr[$i]))&&($commentaire[$i]!="")){
						if(!isset($suppr[$i])) {
							$nom_log = "commentaire_".$i;
							if (isset($NON_PROTECT[$nom_log])){
								$commentaire_courant = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));

									if($commentaire_courant!=""){
									for($j=0;$j<count($num_periode);$j++){
										//$sql="insert into commentaires_types values('','$commentaire[$i]','$num_periode[$j]','$id_classe')";
										//=========================
										// MODIF: boireaus 20071121
										//$sql="insert into commentaires_types values('','".html_entity_decode($commentaire[$i])."','$num_periode[$j]','$id_classe')";
										//$tmp_commentaire=my_ereg_replace("&#039;","'",html_entity_decode($commentaire[$i]));
										//$sql="insert into commentaires_types values('','".addslashes($tmp_commentaire)."','$num_periode[$j]','$id_classe')";
										//$sql="insert into commentaires_types values('','".addslashes($commentaire_courant)."','$num_periode[$j]','$id_classe')";
										$sql="insert into commentaires_types values('','".$commentaire_courant."','$num_periode[$j]','$id_classe')";
										//=========================
										//echo "sql=$sql<br />";
										$resultat_insertion_commentaire=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									}
								}
							}
						}
					}
				}

				echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";

				/*
				echo "$id_classe: ";
				for($i=0;$i<count($num_periode);$i++){
					echo "$num_periode[$i] -";
				}
				echo "<br />";
				*/


				// Récupération du nom de la classe
				$sql="select * from classes where id='$id_classe'";
				$resultat_classe=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($resultat_classe)==0){
					echo "<p>L'identifiant de la classe semble erroné.</p>\n</form>\n</body>\n</html>\n";
					exit();
				}
				$ligne_classe=mysqli_fetch_object($resultat_classe);
				$classe_courante="$ligne_classe->classe";
				//echo "<p><b>Classe de $classe_courante</b></p>\n";
				echo "<h2>Classe de $classe_courante</h2>\n";

				// Recherche des commentaires déjà saisis:
				//$sql="select * from commentaires_types where id_classe='$id_classe' and (num_periode='$num_periode[0]'";
				//$sql="select distinct commentaire,id from commentaires_types where id_classe='$id_classe' and (num_periode='$num_periode[0]'";
				$sql="select distinct commentaire,id from commentaires_types where id_classe='$id_classe' and (num_periode='$num_periode[0]'";
				echo "<input type='hidden' name='num_periode[0]' value='$num_periode[0]' />\n";

				for($i=1;$i<count($num_periode);$i++){
					$sql=$sql." or num_periode='$num_periode[$i]'";
					echo "<input type='hidden' name='num_periode[$i]' value='$num_periode[$i]' />\n";
				}
				//$sql=$sql.")";
				$sql=$sql.") order by commentaire";
				//echo "$sql";
				$resultat_commentaires=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				$cpt=1;
				if(mysqli_num_rows($resultat_commentaires)!=0){
					echo "<p>Voici la liste des commentaires-type existants pour la classe et la/les période(s) choisie(s):</p>\n";
					echo "<blockquote>\n";
					echo "<table class='boireaus' border='1'>\n";
					echo "<tr style='text-align:center;'>\n";
					echo "<th>Commentaire</th>\n";
					echo "<th>Supprimer</th>\n";
					echo "</tr>\n";

					$precedent_commentaire="";

					//$cpt=1;
					$alt=1;
					while($ligne_commentaire=mysqli_fetch_object($resultat_commentaires)){
						if("$ligne_commentaire->commentaire"!="$precedent_commentaire"){
							$alt=$alt*(-1);
							echo "<tr class='lig$alt' style='text-align:center;'>\n";

							echo "<td>";
							//echo "<textarea name='commentaire[$cpt]' cols='60'>".stripslashes($ligne_commentaire->commentaire)."</textarea>";
							echo "<textarea name='no_anti_inject_commentaire_".$cpt."' cols='60' onchange='changement()'>".stripslashes($ligne_commentaire->commentaire)."</textarea>";
							echo "</td>\n";

							echo "<td><input type='checkbox' name='suppr[$cpt]' value='$ligne_commentaire->id' /></td>\n";
							echo "</tr>\n";
							$cpt++;
							$precedent_commentaire="$ligne_commentaire->commentaire";
						}
					}
					echo "</table>\n";
					echo "</blockquote>\n";
				}

				echo "<p>Saisie d'un nouveau commentaire:</p>";
				echo "<blockquote>\n";
				//echo "<textarea name='commentaire[$cpt]' cols='60'></textarea><br />\n";
				echo "<textarea name='no_anti_inject_commentaire_".$cpt."' id='no_anti_inject_commentaire_".$cpt."' cols='60' onchange='changement()'></textarea><br />\n";

				echo "<input type='hidden' name='compteur_nb_commentaires' value='$cpt' />\n";

				echo "<center><input type='submit' name='ok' value='Valider' /></center>\n";
				echo "</blockquote>\n";

				echo "<script type='text/javascript'>
	document.getElementById('no_anti_inject_commentaire_".$cpt."').focus();
</script>\n";
			}
		}
	}
	else{
		//==================================================================
		// ==============================================================

		// ============================
		// Recopie de commentaires-type
		// ============================

		echo "<input type='hidden' name='recopie' value='oui' />\n";

		if(!isset($_POST['id_classe'])){

			// =========================
			// Choix de la classe modèle
			// =========================

			echo "<p>De quelle classe souhaitez-vous recopier les commentaires-type?</p>\n";
			echo "<blockquote>\n";
			$sql="select distinct id,classe from classes order by classe";
			$resultat_classes=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($resultat_classes)==0){
				echo "<p>Aucune classe n'est encore définie...</p>\n</form>\n</body>\n</html>\n";
				exit();
			}

			$nb_classes=mysqli_num_rows($resultat_classes);
			$nb_class_par_colonne=round($nb_classes/3);
			echo "<table width='100%'>\n";
			echo "<tr valign='top' align='center'>\n";
			$cpt=0;
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
			while($ligne_classe=mysqli_fetch_object($resultat_classes)){
				if(($cpt>0)&&(round($cpt/$nb_class_par_colonne)==$cpt/$nb_class_par_colonne)){
					echo "</td>\n";
					//echo "<td style='padding: 0 10px 0 10px'>\n";
					echo "<td align='left'>\n";
				}

				//echo "<input type='radio' name='id_classe' value='$ligne_classe->id' /> $ligne_classe->classe<br />\n";
				echo "<input type='radio' name='id_classe' id='id_classe".$ligne_classe->id."' value='$ligne_classe->id' /><label for='id_classe".$ligne_classe->id."' style='cursor: pointer;'> $ligne_classe->classe</label><br />\n";
				$cpt++;
			}
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			echo "</blockquote>\n";
			echo "<center><input type='submit' name='ok' value='Valider' /></center>\n";
		}
		else{

			// ============================
			// La classe-modèle est choisie
			// ============================

			// Récupération des variables:
			$id_classe=$_POST['id_classe'];

			echo "<h2>Recopie de commentaires-types</h2>\n";

/*
			echo "<p>Voici les commentaires-type saisis pour cette classe:</p>\n";
			echo "<ul>\n";
			for($i=0;$i<count($num_periode);$i++){
				echo "<li>\n";
				$sql="select nom_periode from periodes where num_periode='$num_periode[$i]'";
				$resultat_periode=mysql_query($sql);
				$ligne_nom_periode=mysql_fetch_object($resultat_periode);
				echo "<p><b>$ligne_nom_periode->nom_periode</b>:</p>\n";

				echo "<input type='hidden' name='num_periode[$i]' value='$num_periode[$i]'>\n";

				// AFFICHER LES COMMENTAIRES-TYPE POUR CHAQUE PERIODE
				$sql="select * from commentaires_types where id_classe='$id_classe' and num_periode='$num_periode[$i]' order by commentaire";
				$resultat_commentaires=mysql_query($sql);
				echo "<ul>\n";
				while($ligne_commentaires=mysql_fetch_object($resultat_commentaires)){
					echo "<li>".stripslashes(nl2br(trim($ligne_commentaires->commentaire)))."</li>\n";
				}
				echo "</ul>\n";
			echo "</li>\n";
			}
			echo "</ul>\n";
*/




			echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";

			// Récupération du nom de la classe
			$sql="select * from classes where id='$id_classe'";
			$resultat_classe=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($resultat_classe)==0){
				echo "<p>L'identifiant de la classe semble erroné.</p>\n</form>\n</body>\n</html>\n";
				exit();
			}
			$ligne_classe=mysqli_fetch_object($resultat_classe);
			$classe_source="$ligne_classe->classe";

			echo "<p><b>Classe modèle:</b> $classe_source</p>\n";

			if(!isset($_POST['num_periode'])){

				// =============================
				// Choix des périodes à recopier
				// =============================

				// Rappel des commentaires-type saisis pour cette classe sur toutes les périodes définies:

				$sql="select * from periodes where id_classe='$id_classe' order by num_periode";
				//echo "$sql<br />";
				$resultat_num_periode=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($resultat_num_periode)==0){
					echo "Aucune période n'est encore définie pour cette classe...<br />\n";
					echo "</body>\n</html>\n";
					exit();
				}
				else{
					$compteur_commentaires=0;
					echo "<p>Voici les commentaires-type saisis pour cette classe:</p>\n";
					echo "<ul>\n";
					while($ligne_periode=mysqli_fetch_object($resultat_num_periode)){
					//for($i=0;$i<count($num_periode);$i++){
						echo "<li>\n";
						//$sql="select nom_periode from periodes where num_periode='$ligne_periode->num_periode'";
						//$resultat_periode=mysql_query($sql);
						//$ligne_nom_periode=mysql_fetch_object($resultat_periode);
						//echo "<p><b>$ligne_nom_periode->nom_periode</b>:</p>\n";
						echo "<p><b>$ligne_periode->nom_periode</b>:</p>\n";

						// AFFICHER LES COMMENTAIRES-TYPE POUR CHAQUE PERIODE
						$sql="select * from commentaires_types where id_classe='$id_classe' and num_periode='$ligne_periode->num_periode' order by commentaire";
						$resultat_commentaires=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($resultat_commentaires)>0){
							echo "<ul>\n";
							while($ligne_commentaires=mysqli_fetch_object($resultat_commentaires)){
								echo "<li>".stripslashes(nl2br(trim($ligne_commentaires->commentaire)))."</li>\n";
								$compteur_commentaires++;
							}
							echo "</ul>\n";
						}
						else{
							echo "<p style='color:red;'>Aucun commentaire-type n'est saisi pour cette classe sur cette période.</p>\n";
							//echo "</body>\n</html>\n";
							//exit();
						}
						echo "</li>\n";
					}
					echo "</ul>\n";
					if($compteur_commentaires==0){
						echo "</body>\n</html>\n";
						exit();
					}
				}




				// Choix des périodes:

				echo "<p>Pour quelles périodes souhaitez-vous recopier les commentaires-type?</p>\n";
				//echo "<p>\n";
				//echo "<input type='hidden' name='id_classe' value='$id_classe'>\n";

				//echo "<p><b>$classe_source</b>: ";

				$sql="select * from periodes where id_classe='$id_classe' order by num_periode";
				//echo "$sql<br />";
				$resultat_num_periode=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($resultat_num_periode)==0){
					echo "<p>Aucune période n'est encore définie pour cette classe...</p>\n";
				}
				else{
					/*
					$ligne_num_periode=mysql_fetch_object($resultat_num_periode);
					$sql="select * from periodes where num_periode='$ligne_num_periode->num_periode'";
					$resultat_periode=mysql_query($sql);
					$ligne_periode=mysql_fetch_object($resultat_periode);
					//echo "<input type='checkbox' name='num_periode[]' value='$ligne_periode->num_periode'> $ligne_periode->nom_periode\n";
					echo "<input type='checkbox' name='num_periode[]' id='num_periode_".$ligne_periode->num_periode."' value='$ligne_periode->num_periode' /><label for='num_periode_".$ligne_periode->num_periode."' style='cursor: pointer;'> $ligne_periode->nom_periode</label>\n";

					while($ligne_num_periode=mysql_fetch_object($resultat_num_periode)){
						//$cpt++;
						$sql="select * from periodes where num_periode='$ligne_num_periode->num_periode'";
						$resultat_periode=mysql_query($sql);
						$ligne_periode=mysql_fetch_object($resultat_periode);
						//echo " &nbsp;&nbsp;&nbsp;- <input type='checkbox' name='num_periode[]' value='$ligne_periode->num_periode'> $ligne_periode->nom_periode\n";
						echo " &nbsp;&nbsp;&nbsp;- <input type='checkbox' name='num_periode[]' id='num_periode_".$ligne_periode->num_periode."' value='$ligne_periode->num_periode' /><label for='num_periode_".$ligne_periode->num_periode."' style='cursor: pointer;'> $ligne_periode->nom_periode</label>\n";
					}
					*/
					$cpt_per=0;
					while($ligne_num_periode=mysqli_fetch_object($resultat_num_periode)){
						if($cpt_per>0) {echo " &nbsp;&nbsp;&nbsp;- ";}
						echo "<input type='checkbox' name='num_periode[]' id='num_periode_".$ligne_num_periode->num_periode."' value='$ligne_num_periode->num_periode' /><label for='num_periode_".$ligne_num_periode->num_periode."' style='cursor: pointer;'> $ligne_num_periode->nom_periode</label>\n";
						$cpt_per++;
					}

					echo "<br />\n";
					echo "<center><input type='submit' name='ok' value='Valider' /></center>\n";
				}
				//echo "</p>\n";
				//echo "<input type='submit' name='ok' value='Valider'>\n";
			}
			else{
				// =========================================================
				// La classe-modèle et les périodes à recopier sont choisies
				// =========================================================

				// Récupération des variables:
				$num_periode=$_POST['num_periode'];

/*
				echo "<p>Voici les commentaires-type saisis pour cette classe:</p>\n";
				echo "<ul>\n";
				for($i=0;$i<count($num_periode);$i++){
					echo "<li>\n";
					$sql="select nom_periode from periodes where num_periode='$num_periode[$i]'";
					$resultat_periode=mysql_query($sql);
					$ligne_nom_periode=mysql_fetch_object($resultat_periode);
					echo "<p><b>$ligne_nom_periode->nom_periode</b>:</p>\n";

					echo "<input type='hidden' name='num_periode[$i]' value='$num_periode[$i]'>\n";

					// AFFICHER LES COMMENTAIRES-TYPE POUR CHAQUE PERIODE
					$sql="select * from commentaires_types where id_classe='$id_classe' and num_periode='$num_periode[$i]' order by commentaire";
					$resultat_commentaires=mysql_query($sql);
					echo "<ul>\n";
					while($ligne_commentaires=mysql_fetch_object($resultat_commentaires)){
						echo "<li>".stripslashes(nl2br(trim($ligne_commentaires->commentaire)))."</li>\n";
					}
					echo "</ul>\n";
					echo "</li>\n";
				}
				echo "</ul>\n";
*/

				if(!isset($_POST['id_dest_classe'])){

					// ==========================================================
					// Choix des classes vers lesquelles la recopie doit se faire
					// ==========================================================

					echo "<p>Voici les commentaires-type saisis pour cette classe et les périodes choisies:</p>\n";
					echo "<ul>\n";
					for($i=0;$i<count($num_periode);$i++){
						echo "<li>\n";
						$sql="select nom_periode from periodes where num_periode='$num_periode[$i]'";
						$resultat_periode=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						$ligne_nom_periode=mysqli_fetch_object($resultat_periode);
						echo "<p><b>$ligne_nom_periode->nom_periode</b>:</p>\n";

						echo "<input type='hidden' name='num_periode[$i]' value='$num_periode[$i]' />\n";

						// AFFICHER LES COMMENTAIRES-TYPE POUR CHAQUE PERIODE
						$sql="select * from commentaires_types where id_classe='$id_classe' and num_periode='$num_periode[$i]' order by commentaire";
						$resultat_commentaires=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						echo "<ul>\n";
						while($ligne_commentaires=mysqli_fetch_object($resultat_commentaires)){
							echo "<li>".stripslashes(nl2br(trim($ligne_commentaires->commentaire)))."</li>\n";
						}
						echo "</ul>\n";
						echo "</li>\n";
					}
					echo "</ul>\n";

					echo "<p>Pour quelles classes souhaitez-vous supprimer les commentaires-type existant et les remplacer par ceux de $classe_source?</p>\n";
					// AJOUTER UN JavaScript POUR 'Tout cocher'

					//$sql="select distinct id,classe from classes order by classe";
					if($_SESSION['statut']=='professeur'){
						$sql="SELECT DISTINCT c.id,c.classe FROM j_eleves_classes jec, classes c, j_eleves_professeurs jep
											WHERE jec.id_classe=c.id AND
												jec.login=jep.login AND
												jep.professeur='".$_SESSION['login']."'
											ORDER BY c.classe";
					}
					elseif($_SESSION['statut']=='scolarite'){
						$sql="SELECT DISTINCT c.id,c.classe FROM j_scol_classes jsc, classes c
											WHERE jsc.id_classe=c.id AND
												jsc.login='".$_SESSION['login']."'
											ORDER BY c.classe";
					}
					else{
						// CA NE DEVRAIT PAS ARRIVER...
						$sql="select distinct id,classe from classes order by classe";
					}
					$resultat_classes=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($resultat_classes)==0){
						echo "<p>Aucune classe n'est encore définie...</p>\n</form>\n</body>\n</html>\n";
						exit();
					}

					$cpt=0;
					while($ligne_classe=mysqli_fetch_object($resultat_classes)){
						if("$ligne_classe->id"!="$id_classe"){
							echo "<label for='id_dest_classe$cpt' style='cursor: pointer;'><input type='checkbox' name='id_dest_classe[]' id='id_dest_classe$cpt' value='$ligne_classe->id' /> $ligne_classe->classe</label><br />\n";
							$cpt++;
						}
					}
					//echo "</blockquote>\n";
					echo "<!--script language='JavaScript'-->
<script language='JavaScript' type='text/javascript'>
function tout_cocher(){
	for(i=0;i<$cpt;i++){
		document.getElementById('id_dest_classe'+i).checked=true;
	}
}
function tout_decocher(){
	for(i=0;i<$cpt;i++){
		document.getElementById('id_dest_classe'+i).checked=false;
	}
}
</script>
";
					echo "<input type='button' name='toutcocher' value='Tout cocher' onClick='tout_cocher();' /> - \n";
					echo "<input type='button' name='toutdecocher' value='Tout décocher' onClick='tout_decocher();' />\n";
					echo "<center><input type='submit' name='ok' value='Valider' /></center>\n";
				}
				else {
					check_token(false);

					// =======================
					// Recopie proprement dite
					// =======================

					$id_dest_classe=$_POST['id_dest_classe'];

					//echo count($num_periode)."<br />";
					//flush();

					// Nettoyage des commentaires déjà saisis pour ces classes et ces périodes:
					for($i=0;$i<count($id_dest_classe);$i++){
						$sql="delete from commentaires_types where id_classe='$id_dest_classe[$i]' and (num_periode='$num_periode[0]'";

						//for($i=0;$i<count($num_periode);$i++){
						for($j=1;$j<count($num_periode);$j++){
							$sql=$sql." or num_periode='$num_periode[$j]'";
						}
						$sql=$sql.")";
						//echo "sql=$sql<br />";
						$resultat_nettoyage=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					}

/*
					$sql="select commentaire from commentaires_types where id_classe='$id_classe' and (num_periode='$num_periode[0]'";

					for($i=1;$i<count($num_periode);$i++){
						$sql=$sql." or num_periode='$num_periode[$i]'";
					}
					$sql=$sql.") order by commentaire";
					echo "sql=$sql<br />";
					$resultat_commentaires_source=mysql_query($sql);
					if(mysql_num_rows($resultat_commentaires_source)==0){
						echo "<p>C'est malin... il n'existe pas de commentaires-type pour la/les classe(s) et la/les période(s) choisie(s).<br />\nDe plus, les commentaires existants ont été supprimés...</p>\n";
					}
					else{
						while($ligne_commentaires_source=mysql_fetch_object($resultat_commentaires_source)){

						}
					}
*/




					for($i=0;$i<count($num_periode);$i++){
						// Nom de la période courante:
						$sql="select nom_periode from periodes where num_periode='$num_periode[$i]'";
						$resultat_periode=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						$ligne_nom_periode=mysqli_fetch_object($resultat_periode);
						echo "<p><b>$ligne_nom_periode->nom_periode</b>:</p>\n";
						echo "<blockquote>\n";

						// Récupération des commentaires à insérer:
						$sql="select commentaire from commentaires_types where id_classe='$id_classe' and num_periode='$num_periode[$i]' order by commentaire";
						//echo "sql=$sql<br />";
						$resultat_commentaires_source=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($resultat_commentaires_source)==0){
							echo "<p>C'est malin... il n'existe pas de commentaires-type pour la classe modèle et la période choisie.<br />\nDe plus, les commentaires existants pour les classes destination ont été supprimés...</p>\n";
						}
						else{
							while($ligne_commentaires_source=mysqli_fetch_object($resultat_commentaires_source)){
								echo "<table>\n";
								for($j=0;$j<count($id_dest_classe);$j++){

									// Récupération du nom de la classe:
									$sql="select classe from classes where id='$id_dest_classe[$j]'";
									$resultat_classe_dest=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
									$ligne_classe_dest=mysqli_fetch_object($resultat_classe_dest);

									//echo "<b>Insertion pour $ligne_classe_dest->classe:</b><br /> ".stripslashes(nl2br(trim($ligne_commentaires_source->commentaire)))."<br />\n";
									echo "<tr valign=\"top\"><td><b>Insertion pour $ligne_classe_dest->classe:</b></td><td> ".stripslashes(nl2br(trim($ligne_commentaires_source->commentaire)))."</td></tr>\n";

									//$sql="insert into commentaires_types values('','$ligne_commentaires_source->commentaire','$num_periode[$i]','$id_dest_classe[$j]')";

									$commentaire_courant=traitement_magic_quotes(corriger_caracteres($ligne_commentaires_source->commentaire));

									$sql="insert into commentaires_types values('','$commentaire_courant','$num_periode[$i]','$id_dest_classe[$j]')";

									//echo "sql=$sql<br />";
									$resultat_insertion_commentaire=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
								}
								echo "</table>\n";
							}
							echo "<p>Insertions terminées pour la période.</p>\n";
						}
						echo "</blockquote>\n";
					}






/*
					// Validation des saisies/modifs...
					for($i=1;$i<=count($commentaire);$i++){
						echo "\$suppr[$i]=$suppr[$i]<br />";
						if(($suppr[$i]=="")&&($commentaire[$i]!="")){
							for($j=0;$j<count($num_periode);$j++){
								$sql="insert into commentaires_types values('','$commentaire[$i]','$num_periode[$j]','$id_classe')";
								echo "sql=$sql<br />";
								$resultat_insertion_commentaire=mysql_query($sql);
							}
						}
					}
*/
				}
			}
		}




	}
?>
</form>
<p><br /></p>
<?php
require("../lib/footer.inc.php");
?>
