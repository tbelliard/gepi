<?php
/*
 * Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Régis Bouguin
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

if((!isset($indice_aid))||(!preg_match("/^[0-9]$/", $indice_aid))) {
	header("Location: ../logout.php?auto=1");
	die();
}

// Vérification du niveau de gestion des AIDs
if (NiveauGestionAid($_SESSION["login"],$indice_aid) < 5) {
	header("Location: ../logout.php?auto=1");
	die();
}

//=======================================
$sql="SELECT * FROM aid_config WHERE indice_aid='$indice_aid';";
$res_famille_aid=mysqli_query($GLOBALS['mysqli'], $sql);
if(mysqli_num_rows($res_famille_aid)==0) {
	header("Location: ../accueil.php?msg=Indice AID $indice_aid inconnu.");
	die();
}
$lig_famille_aid=mysqli_fetch_object($res_famille_aid);
$nom_famille_aid=$lig_famille_aid->nom;
$nom_complet_famille_aid=$lig_famille_aid->nom_complet;
$autoriser_inscript_multiples=$lig_famille_aid->autoriser_inscript_multiples;
//=======================================

include_once 'fonctions_aid.php';
$mysqli = $GLOBALS["mysqli"];
$javascript_specifique = "aid/aid_ajax";

if(!isset($mess)) {$mess="";}

// $is_posted = isset($_POST['is_posted']) ? $_POST['is_posted'] : (isset($is_posted) ? $is_posted : NULL);

$aid_id = isset($aid_id) ? $aid_id : "";
$mode = isset($mode) ? $mode : "";
$action = isset($action) ? $action : "";
$sous_groupe = isset($sous_groupe) ? $sous_groupe : "n";
$parent = isset($parent) ? $parent : "";
$sous_groupe_de =isset($sous_groupe_de) ? $sous_groupe_de : NULL;
$inscrit_direct =isset($inscrit_direct) ? $inscrit_direct : NULL;

if (isset($is_posted) && $is_posted) {
	if ("n" == $sous_groupe) {
		Efface_sous_groupe($aid_id);
		//die($aid_id);
	}
	if ("y" == $sous_groupe || $sous_groupe_de != NULL) {
		$reg_parent = Sauve_sous_groupe($aid_id, $parent);
		if (!$reg_parent) {
		   $mess = rawurlencode("Erreur lors de l'enregistrement des données.");
		   header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
		   die();
		}
	}
	
	//  On regarde si une aid porte déjà le même nom
	$count = mysqli_num_rows(Extrait_aid_sur_nom($aid_nom , $indice_aid));
	check_token();
	if (isset($is_posted) and ($is_posted =="1")) { // nouveau
		// On calcule le nouveau id pour l'aid à insérer → Plus gros id + 1
		$aid_id = Dernier_id ($ordre = "DESC") + 1;
	} else {
		$count--;
	}
//if ($inscrit_direct) die ($inscrit_direct);

	$reg_data = Sauve_definition_aid ($aid_id , $aid_nom , $aid_num , $indice_aid , $sous_groupe , $inscrit_direct);
	if (!$reg_data) {
	   $mess = rawurlencode("Erreur lors de l'enregistrement des données.");
	   header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
	   die();
	}
	else {
		$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : array();

		$nb_ele_inscrits=0;
		for($loop=0;$loop<count($id_classe);$loop++) {
			$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='".$id_classe[$loop]."';";
			$res_ele_clas=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res_ele_clas)>0) {
				while($lig_ele=mysqli_fetch_object($res_ele_clas)) {
					// On commence par vérifier que l'élève n'est pas déjà présent dans cette liste, ni dans aucune.
					if ($autoriser_inscript_multiples == 'y') {
						$filtre =  " AND id_aid='".$aid_id."' ";
					}
					else {
						$filtre =  "";
					}
					$sql = "SELECT * FROM j_aid_eleves WHERE (login='".$lig_ele->login."' AND indice_aid='".$indice_aid."'".$filtre.")";
					//echo $sql;
					$test = mysqli_query($GLOBALS["mysqli"], $sql);
					$test2 = mysqli_num_rows($test);
					$msg = "";
					if ($test2=="0") {
						if($lig_ele->login!='') {
							$reg_data = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_aid_eleves SET login='".$lig_ele->login."', id_aid='$aid_id', indice_aid='$indice_aid'");
							if (!$reg_data) {
								$msg.="Erreur lors de l'ajout de l'élève ".$lig_ele->login."<br />";
							}
							else {
								$nb_ele_inscrits++;
							}
						}
					}
				}
			}
		}

		$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : array();

		$nb_profs_inscrits=0;
		for($loop=0;$loop<count($login_prof);$loop++) {
			$test2=Prof_deja_membre($login_prof[$loop], $aid_id, $indice_aid)->num_rows;
			if ($test2 != "0") {
				$msg.="Le professeur ".$login_prof[$loop]." que vous avez tenté d'ajouter appartient déjà à cet AID.<br />";
			} else {
				if ($login_prof[$loop] != '') {
					$reg_data=Sauve_prof_membre($login_prof[$loop], $aid_id, $indice_aid);
					if (!$reg_data) {
						$msg.="Erreur lors de l'ajout du professeur ".$login_prof[$loop]." !<br />";
					}
					else {
						$nb_profs_inscrits++;
					}
				}
			}
		}
	}
	if ($count == "1") {
		$msg=$msg." Attention, une AID ($nom_famille_aid) portant le même nom existait déja !<br />";
	} else if ($count > 1) {
		$msg=$msg." Attention, plusieurs AID ($nom_famille_aid) portant le même nom existaient déja !<br />";
	}
	if ($mode == "multiple") {
		$msg .= "AID ($nom_famille_aid) enregistrée !<br />" ;

		if((isset($nb_ele_inscrits))&&($nb_ele_inscrits>0)) {
			$msg.=$nb_ele_inscrits." élève(s) inscrit(s).<br />";
		}

		if((isset($nb_profs_inscrits))&&($nb_profs_inscrits>0)) {
			$msg.=$nb_profs_inscrits." professeur(s) inscrit(s).<br />";
		}

		$mess = rawurlencode($msg);
		header("Location: add_aid.php?action=add_aid&mode=multiple&msg=$mess&indice_aid=$indice_aid");
		die();
	} else{
		$msg .= "AID ($nom_famille_aid) enregistrée !<br />";

		if((isset($nb_ele_inscrits))&&($nb_ele_inscrits>0)) {
			$msg.=$nb_ele_inscrits." élève(s) inscrit(s).<br />";
		}

		if((isset($nb_profs_inscrits))&&($nb_profs_inscrits>0)) {
			$msg.=$nb_profs_inscrits." professeur(s) inscrit(s).<br />";
		}

		$mess = rawurlencode($msg);
		header("Location: index2.php?msg=$mess&indice_aid=$indice_aid");
		die();
	 }
} else {
	// on remplit tous les champs pour n'avoir qu'un affichage
	
	$id_aid_prec=-1;
	$id_aid_suiv=-1;
	$temoin_tmp=0;
	$aid_nom = "";
	$aid_num = "";
	$nouveau = "Entrez un nom : ";
	$is_posted = (isset($action) && $action == "modif_aid") ? 2 : ((isset($action) && $action == "add_aid") ? 1 : "" );

	if ("modif_aid" == $action) {
		$res_aid_tmp = Extrait_aid_sur_indice_aid ($indice_aid);
		if(mysqli_num_rows($res_aid_tmp)>0){
			while($lig_aid_tmp=mysqli_fetch_object($res_aid_tmp)){
				if($lig_aid_tmp->id==$aid_id){
					$temoin_tmp=1;
					if($lig_aid_tmp=mysqli_fetch_object($res_aid_tmp)){
						$id_aid_suiv=$lig_aid_tmp->id;
					}
					else{
						$id_aid_suiv=-1;
					}
				}
				if($temoin_tmp==0){
					$id_aid_prec=$lig_aid_tmp->id;
				}
			}
		}
	}
	$res_parents=Extrait_aid_sur_indice_aid ($indice_aid);
	
	if ($action == "modif_aid") {
		$calldata = Extrait_aid_sur_id ($aid_id, $indice_aid)->fetch_object();
		$aid_nom = $calldata->nom;	
		$aid_num = $calldata->numero;
		$sous_groupe = $calldata->sous_groupe;		
		$nouveau = "Entrez le nouveau nom à la place de l'ancien : ";
		if ('y' == $sous_groupe) {
			$res_groupe_de=Extrait_parent ($aid_id);
			if ($res_groupe_de->num_rows) {
				$sous_groupe_de = $res_groupe_de->fetch_object()->parent;
			}
		}
	}
}

//**************** EN-TETE *********************
if ($action == "modif_aid") {
	$titre_page = "Gestion des AID | Modifier Une AID ($nom_famille_aid)";
}
else {
	$titre_page = "Gestion des AID | Ajouter Une AID ($nom_famille_aid)";
}
require_once("../lib/header.inc.php");


// debug_var();
//**************** FIN EN-TETE *****************

if ($_SESSION['statut'] == 'professeur') {
	$retour = 'index2.php';
} else {
	$retour = 'index.php';
}

?>
<p class="bold">
	<a href="<?php echo $retour; ?>?indice_aid=<?php echo $indice_aid; ?>">
		<img src='../images/icons/back.png' alt='Retour' class='back_link'/>
		Retour
	</a>

<?php
	if ($action == "modif_aid") {


		if($id_aid_prec!=-1) {
?>
	|
	<a href='<?php echo $_SERVER['PHP_SELF']; ?>?action=modif_aid&amp;aid_id=<?php echo $id_aid_prec; ?>&amp;indice_aid=<?php echo $indice_aid; ?>' 
	   onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
		AID précédent
	</a>
<?php
		}
		if($id_aid_suiv!=-1) {
?>
	|
	<a href='<?php echo $_SERVER['PHP_SELF']; ?>?action=modif_aid&amp;aid_id=<?php echo $id_aid_suiv; ?>&amp;indice_aid=<?php echo $indice_aid; ?>'
	   onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
		AID suivant
	</a>
<?php
		}
	}
?>

</p>

<form enctype="multipart/form-data" action="add_aid.php" method="post">

	<h2><?php echo $nom_famille_aid; ?>
		<input type="submit" value="Enregistrer" />
	</h2>

    <p><?php echo $nouveau; ?></p>

	<?php
		echo add_token_field();
	?>

    <p>
		<label for="aidRegNom">
			Nom : 
		</label>
		<input type="text" 
			   id="aidRegNom" 
			   name="aid_nom" 
			   size="100" 
				<?php echo " value=\"".$aid_nom."\"";?>/>
	</p>
	<p>
		<label for="aidRegNum">
			Numéro (fac.) : 
		</label>
		<input type="text" id="aidRegNum" name="aid_num" size="4" <?php echo " value=\"".$aid_num."\""; ?> />
	</p>

	<div <?php if (!Multiples_possible ($indice_aid)) {echo " style='display:none;'";} ?> >
		<p title="Cochez pour affecter un parent puis choisissez le parent">
			<label for="sous_groupe">
				Sous-groupe d'une autre AID
			</label>
			<input type="checkbox"
				   name='sous_groupe'
				   id='sous_groupe'
				   value="y"
					<?php if ($sous_groupe=='y') {echo " checked='checked' ";} ?>  
				   onchange="afficher_cacher_parent();"
				   />
		</p>

		<div id="aidParent" >
		
<?php if((isset($res_parents) && $res_parents->num_rows)){ ?>
			<select name="parent" id="choix_parent">
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
		</div>

		<h3>Élèves</h3>
		<div style='margin-left:3em;'>
		<p>
			<label for="inscrit_direct">
				Un élève peut s'inscrire directement
			</label>
			<input type="checkbox"
				   name='inscrit_direct'
				   id='inscrit_direct'
				   value="y"
					<?php if (eleve_inscrit_direct($aid_id, $indice_aid)) {echo " checked='checked' ";} ?>
				   />
		</p>
		</div>
	</div>

	<?php
		//echo "action=$action<br />";
		if ($action=="add_aid") {
			$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_classes jec WHERE c.id=jec.id_classe ORDER BY c.classe, c.nom_complet;";
			$res_classes=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res_classes)>0) {
				if (!Multiples_possible ($indice_aid)) {
					echo "<h3>Élèves</h3>";
				}
				echo "<div style='margin-left:3em;'>";
				$tab_txt=array();
				$tab_nom_champ=array();
				$tab_id_champ=array();
				$tab_valeur_champ=array();
				echo "<p style='margin-top:1em;'>Inscrire dans l'AID tous les élèves des classes cochées&nbsp;:</p>";
				while($lig_clas=mysqli_fetch_object($res_classes)) {
					$tab_txt[]=$lig_clas->classe;
					$tab_nom_champ[]="id_classe[]";
					$tab_id_champ[]="id_classe_".$lig_clas->id;
					$tab_valeur_champ[]=$lig_clas->id;
				}
				tab_liste_checkbox($tab_txt, $tab_nom_champ, $tab_id_champ, $tab_valeur_champ, "checkbox_change", "modif_coche", 5);
				echo "<p>Si vous préférez ne pas affecter tous les élèves de telle(s) ou telle(s) classe(s) dans le $nom_famille_aid, vous pourrez gérer plus finement l'inscription par la suite.</p>";
				echo "</div>";
			}

			$sql="SELECT DISTINCT u.login, u.nom, u.prenom FROM utilisateurs u WHERE u.statut='professeur' ORDER BY u.nom, u.prenom;";
			$res_prof=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res_prof)>0) {
				echo "<h3>Professeurs</h3>";
				echo "<div style='margin-left:3em;'>";
				$tab_txt=array();
				$tab_nom_champ=array();
				$tab_id_champ=array();
				$tab_valeur_champ=array();
				echo "<p style='margin-top:1em;'>Inscrire comme professeur(s) responsable(s) de cet AID les professeurs cochés&nbsp;:</p>";
				$cpt_prof=0;
				while($lig_prof=mysqli_fetch_object($res_prof)) {
					$tab_txt[]=casse_mot($lig_prof->nom, "maj")." ".casse_mot($lig_prof->prenom, "majf2");
					$tab_nom_champ[]="login_prof[]";
					$tab_id_champ[]="login_prof_".$cpt_prof;
					$tab_valeur_champ[]=$lig_prof->login;
					$cpt_prof++;
				}
				tab_liste_checkbox($tab_txt, $tab_nom_champ, $tab_id_champ, $tab_valeur_champ, "checkbox_change_prof", "modif_coche_prof", 5);
				echo "<p>Si vous préférez ne pas affecter les professeurs maintenant, vous pourrez le faire plus tard.</p>";
				echo "</div>";
			}
		}
	?>

	<p style='margin-top:1em;' class="center">
		<input type="hidden" name="indice_aid" value="<?php echo $indice_aid; ?>" />
		<input type="hidden" name="aid_id" value="<?php echo $aid_id; ?>" />
		<input type="hidden" name="mode" value="<?php echo $mode; ?>" />
		<input type="hidden" name="is_posted" value="<?php echo $is_posted; ?>" />
		<input type="submit" value="Enregistrer" />
	</p>
	
</form>

<script type='text/javascript'>
if(document.getElementById('aidRegNom')) {
	document.getElementById('aidRegNom').focus();
}
</script>

<script type='text/javascript'>
	function afficher_cacher(id)
{
    if(document.getElementById(id).style.visibility=="hidden")
    {
        document.getElementById(id).style.visibility="visible";
    }
    else
    {
        document.getElementById(id).style.visibility="hidden";
    }
    return true;
}

	function afficher_cacher_parent()
{
    if(document.getElementById('sous_groupe').checked)
    {
        document.getElementById('choix_parent').style.visibility="visible";
    }
    else
    {
        document.getElementById('choix_parent').style.visibility="hidden";
    }
    return true;
}

afficher_cacher_parent();
</script>

<?php 
require("../lib/footer.inc.php");
