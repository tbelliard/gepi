<?php
/*
*
* Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

if(!acces_saisie_modalites_accompagnement()) {
	header("Location: ../accueil.php?msg=Saisie des modalités d'accompagnement non autorisée.");
	die();
}

$msg="";

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : (isset($_GET['login_eleve']) ? $_GET['login_eleve'] : NULL);

if(isset($id_classe)) {
	include("../lib/periodes.inc.php");
}

$tab_modalite_accompagnement=get_tab_modalites_accompagnement();

//debug_var();

if(isset($_POST['is_posted_modalites_classes'])) {
	check_token();

	$nb_maj=0;
	$nb_reg=0;
	$nb_del=0;

/*
 $_POST['accompagnement_4459_PAP_1']=	PAP
$_POST['accompagnement_4459_PAP_2']=	PAP
$_POST['accompagnement_4459_PAP_3']=	PAP
$_POST['no_anti_inject_textarea_4459_PPRE_1']=	
$_POST['no_anti_inject_textarea_4459_PPRE_2']=	
$_POST['no_anti_inject_textarea_4459_PPRE_3']=	
$_POST['accompagnement_4460_PPRE_1']=	PPRE
$_POST['no_anti_inject_textarea_4460_PPRE_1']=	Bidule PPRE BAVENCOFF T1
$_POST['no_anti_inject_textarea_4460_PPRE_2']=	
$_POST['accompagnement_4460_PPRE_3']=	PPRE
$_POST['no_anti_inject_textarea_4460_PPRE_3']=	Bidule PPRE BAVENCOFF T3
$_POST['accompagnement_4460_SEGPA_1']=	SEGPA
$_POST['accompagnement_4460_SEGPA_3']=	SEGPA
$_POST['no_anti_inject_textarea_4461_PPRE_1']=	
$_POST['no_anti_inject_textarea_4461_PPRE_2']=	
$_POST['no_anti_inject_textarea_4461_PPRE_3']=	
*/


	$tab_modalites_ele=array();
	$sql="SELECT DISTINCT jmae.* FROM j_modalite_accompagnement_eleve jmae, 
				eleves e, 
				j_eleves_classes jec 
			WHERE jmae.id_eleve=e.id_eleve AND 
				jec.login=e.login AND 
				jec.id_classe='".$id_classe."' 
			ORDER BY e.nom, e.prenom;";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		if(!isset($_POST['accompagnement_'.$lig->id_eleve."_".$lig->code."_".$lig->periode])) {
			$sql="DELETE FROM j_modalite_accompagnement_eleve WHERE id_eleve='".$lig->id_eleve."' AND code='".$lig->code."' AND periode='".$lig->periode."';";
			$del=mysqli_query($mysqli, $sql);
			$nb_del++;
		}
		else {
			$tab_modalites_ele[$lig->id_eleve][$lig->code][$lig->periode]=$lig->commentaire;
		}
	}
	if($nb_del>0) {
		$msg.=$nb_del." modalité(s) d'accompagnement supprimée(s).<br />";
	}

	$tab_ele=array();
	$sql="SELECT e.*, jec.periode FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='".$id_classe."' ORDER BY jec.periode, e.nom, e.prenom;";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		//$tab_ele[]=$lig;

		foreach($tab_modalite_accompagnement["code"] as $code => $libelle) {
			if(isset($_POST['accompagnement_'.$lig->id_eleve."_".$code."_".$lig->periode])) {
				if(isset($tab_modalites_ele[$lig->id_eleve][$code][$lig->periode])) {
					// Modalité d'accompagnement déjà enregistrée sur cette période.
					// On se contente de mettre à jour le commentaire s'il y en a un
					if((isset($NON_PROTECT['textarea_'.$lig->id_eleve."_".$code."_".$lig->periode]))&&($NON_PROTECT['textarea_'.$lig->id_eleve."_".$code."_".$lig->periode]!=$tab_modalites_ele[$lig->id_eleve][$code])) {
						$sql="UPDATE j_modalite_accompagnement_eleve SET commentaire='".mysqli_real_escape_string($mysqli, $NON_PROTECT['textarea_'.$lig->id_eleve."_".$code."_".$lig->periode])."' WHERE id_eleve='".$lig->id_eleve."' AND code='".$code."' AND periode='".$lig->periode."';";

						$update=mysqli_query($mysqli, $sql);
						if(!$update) {
							$msg.="Erreur lors de la mise à jour de la modalité d'accompagnement $code pour ".$lig->nom." ".$lig->prenom." en période ".$lig->periode.".<br />";
						}
						else {
							$nb_maj++;
						}
					}
				}
				else {
					// On enregistre une nouvelle modalité.

					if(isset($NON_PROTECT['textarea_'.$lig->id_eleve."_".$code."_".$lig->periode])) {
						$sql="INSERT INTO j_modalite_accompagnement_eleve SET commentaire='".mysqli_real_escape_string($mysqli, $NON_PROTECT['textarea_'.$lig->id_eleve."_".$code."_".$lig->periode])."', id_eleve='".$lig->id_eleve."', code='".$code."', periode='".$lig->periode."'";
					}
					else {
						$sql="INSERT INTO j_modalite_accompagnement_eleve SET commentaire='', id_eleve='".$lig->id_eleve."', code='".$code."', periode='".$lig->periode."'";
					}

					$insert=mysqli_query($mysqli, $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'enregistrement de la modalité d'accompagnement $code pour ".$lig->nom." ".$lig->prenom." en période ".$lig->periode.".<br />";
					}
					else {
						$nb_reg++;
					}
				}
			}
		}
	}

	if($nb_reg>0) {
		$msg.=$nb_reg." modalité(s) d'accompagnement enregistrée(s).<br />";
	}
	if($nb_maj>0) {
		$msg.=$nb_maj." modalité(s) d'accompagnement mise(s) à jour.<br />";
	}

}
elseif(isset($_POST['is_posted_modalites_eleve'])) {
	check_token();

	$nb_maj=0;
	$nb_reg=0;
	$nb_del=0;

/*
$_POST['login_eleve']=	abras_j
$_POST['is_posted_modalites_eleve']=	y
$_POST['accompagnement_4459_PAI_3']=	PAI
$_POST['accompagnement_4459_PPRE_1']=	PPRE
$_POST['no_anti_inject_textarea_4459_PPRE_1']=	PPRE ABRAS T1
$_POST['accompagnement_4459_PPRE_2']=	PPRE
$_POST['no_anti_inject_textarea_4459_PPRE_2']=	PPRE ABRAS T2
$_POST['accompagnement_4459_PPRE_3']=	PPRE
$_POST['no_anti_inject_textarea_4459_PPRE_3']=	PPRE ABRAS T3
$_POST['accompagnement_4459_SEGPA_2']=	SEGPA
$_POST['accompagnement_4459_ULIS_1']=	ULIS
*/

	$tab_modalites_ele=array();
	$sql="SELECT DISTINCT jmae.* FROM j_modalite_accompagnement_eleve jmae, 
				eleves e
			WHERE jmae.id_eleve=e.id_eleve AND 
				e.login='".$login_eleve."' 
			ORDER BY jmae.periode;";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		if(!isset($_POST['accompagnement_'.$lig->id_eleve."_".$lig->code."_".$lig->periode])) {
			$sql="DELETE FROM j_modalite_accompagnement_eleve WHERE id_eleve='".$lig->id_eleve."' AND code='".$lig->code."' AND periode='".$lig->periode."';";
			$del=mysqli_query($mysqli, $sql);
			$nb_del++;
		}
		else {
			$tab_modalites_ele[$lig->id_eleve][$lig->code][$lig->periode]=$lig->commentaire;
		}
	}
	if($nb_del>0) {
		$msg.=$nb_del." modalité(s) d'accompagnement supprimée(s).<br />";
	}

	$tab_ele=array();
	$sql="SELECT e.*, jec.periode FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND e.login='".$login_eleve."' ORDER BY jec.periode;";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		//$tab_ele[]=$lig;

		foreach($tab_modalite_accompagnement["code"] as $code => $libelle) {
			if(isset($_POST['accompagnement_'.$lig->id_eleve."_".$code."_".$lig->periode])) {
				if(isset($tab_modalites_ele[$lig->id_eleve][$code][$lig->periode])) {
					// Modalité d'accompagnement déjà enregistrée sur cette période.
					// On se contente de mettre à jour le commentaire s'il y en a un
					if((isset($NON_PROTECT['textarea_'.$lig->id_eleve."_".$code."_".$lig->periode]))&&($NON_PROTECT['textarea_'.$lig->id_eleve."_".$code."_".$lig->periode]!=$tab_modalites_ele[$lig->id_eleve][$code])) {
						$sql="UPDATE j_modalite_accompagnement_eleve SET commentaire='".mysqli_real_escape_string($mysqli, $NON_PROTECT['textarea_'.$lig->id_eleve."_".$code."_".$lig->periode])."' WHERE id_eleve='".$lig->id_eleve."' AND code='".$code."' AND periode='".$lig->periode."';";

						$update=mysqli_query($mysqli, $sql);
						if(!$update) {
							$msg.="Erreur lors de la mise à jour de la modalité d'accompagnement $code pour ".$lig->nom." ".$lig->prenom." en période ".$lig->periode.".<br />";
						}
						else {
							$nb_maj++;
						}
					}
				}
				else {
					// On enregistre une nouvelle modalité.

					if(isset($NON_PROTECT['textarea_'.$lig->id_eleve."_".$code."_".$lig->periode])) {
						$sql="INSERT INTO j_modalite_accompagnement_eleve SET commentaire='".mysqli_real_escape_string($mysqli, $NON_PROTECT['textarea_'.$lig->id_eleve."_".$code."_".$lig->periode])."', id_eleve='".$lig->id_eleve."', code='".$code."', periode='".$lig->periode."'";
					}
					else {
						$sql="INSERT INTO j_modalite_accompagnement_eleve SET commentaire='', id_eleve='".$lig->id_eleve."', code='".$code."', periode='".$lig->periode."'";
					}

					$insert=mysqli_query($mysqli, $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'enregistrement de la modalité d'accompagnement $code pour ".$lig->nom." ".$lig->prenom." en période ".$lig->periode.".<br />";
					}
					else {
						$nb_reg++;
					}
				}
			}
		}
	}

	if($nb_reg>0) {
		$msg.=$nb_reg." modalité(s) d'accompagnement enregistrée(s).<br />";
	}
	if($nb_maj>0) {
		$msg.=$nb_maj." modalité(s) d'accompagnement mise(s) à jour.<br />";
	}

}

// =================================
$chaine_options_classes="
			<option value=''>---</option>";
$sql="SELECT id, classe FROM classes ORDER BY classe";
$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_class_tmp)>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;

	$cpt_classe=0;
	$num_classe=-1;

	while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
		if((isset($id_classe))&&($lig_class_tmp->id==$id_classe)) {
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="
			<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>";
			$temoin_tmp=1;
			if($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
				$chaine_options_classes.="
				<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>";
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		else {
			$chaine_options_classes.="
			<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>";
		}

		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}

		$cpt_classe++;
	}
}
// =================================

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';

//**************** EN-TETE **************************************
$titre_page = "Modalités d'enseignement";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>
	<p class='bold'>
		<a href='../eleves/index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";

if($id_class_prec!=0){
	echo "
		 | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe précédente</a>";
 }

if($chaine_options_classes!="") {

	echo "
		<script type='text/javascript'>
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
		</script>
		 | <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">".$chaine_options_classes."
		</select>\n";
}

if($id_class_suiv!=0){
	echo "
		 | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suivante</a>";
 }

echo "
	</p>
</form>\n";

$tab_mef=get_tab_mef();

if((!isset($login_eleve))&&(!isset($id_classe))) {
	// A faire: proposer une recherche sur un élève

	echo "<p class='bold'>Choisissez la classe pour laquelle saisir des modalités d'accompagnement&nbsp;:</p>";

	$tab_txt=array();
	$tab_lien=array();
	$sql=retourne_sql_mes_classes();
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tab_txt[]=$lig->classe;
		$tab_lien[]=$_SERVER["PHP_SELF"]."?id_classe=".$lig->id_classe;
	}
	$nbcol=3;
	echo tab_liste($tab_txt,$tab_lien,$nbcol);

}
elseif(isset($login_eleve)) {

	echo "<h2>Modalités d'accompagnement pour ".get_nom_prenom_eleve($login_eleve)."</h2>";

	$tab_ele[0]=get_info_eleve($login_eleve);

	$tab_modalites_ele=array();
	$sql="SELECT DISTINCT jmae.* FROM j_modalite_accompagnement_eleve jmae, 
				eleves e 
			WHERE jmae.id_eleve=e.id_eleve AND 
				e.login='".$login_eleve."';";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tab_modalites_ele[$lig->id_eleve][$lig->code][$lig->periode]=$lig->commentaire;
	}

	$rowspan=count($tab_modalite_accompagnement["indice"]);

	$max_per=0;
	$tab_ele_per=array();
	$sql="SELECT DISTINCT c.id, c.classe, jec.periode 
		FROM classes c, 
			j_eleves_classes jec 
		WHERE c.id=jec.id_classe AND 
			jec.login='$login_eleve' 
		ORDER BY jec.periode;";
	$res2=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res2)>0) {
		while($lig2=mysqli_fetch_assoc($res2)) {
			$tab_ele_per[]=$lig2["periode"];

			if($lig2["periode"]>$max_per) {
				$max_per=$lig2["periode"];
			}
		}
	}
	$nb_periode=$max_per+1;

	$chaine_js_traite_graisse_lignes="";

	$current_login=$login_eleve;
	$current_ele=get_info_eleve($login_eleve);

	$chaine_mef="";
	if(isset($tab_mef[$current_ele['mef_code']])) {
		$chaine_mef="<br /><span style='font-size:small'>".$tab_mef[$current_ele['mef_code']]["libelle_edition"]."</span>";
	}

	echo "
<form action='".$_SERVER["PHP_SELF"]."' method='post' name='form2'>
	<fieldset class='fieldset_opacite50'>
		<p><input type='submit' value='Enregistrer' /></p>
		".add_token_field()."
		<input type='hidden' name='login_eleve' value='$login_eleve' />
		<input type='hidden' name='is_posted_modalites_eleve' value='y' />

		<table class='boireaus boireaus_alt' style='margin-bottom:1em;'>
			<thead>
				<tr>
					<th>
						Accompagnements<br />
						<a href='../eleves/modify_eleve.php?eleve_login=".$current_login."' onclick=\"return confirm_abandon (this, change, '$themessage')\">".$current_ele["nom"]." ".$current_ele["prenom"]."</a> 
						<a href='../eleves/visu_eleve.php?ele_login=".$current_login."' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/ele_onglets.png' class='icone16' alt='Onglets' /></a>".$chaine_mef."
					</th>";
		for($i=1;$i<$nb_periode;$i++) {
			echo "
					<th title=\"Période $i\">P.".$i."</th>";
		}
		echo "
				</tr>
			</thead>
			<tbody>";

		foreach($tab_modalite_accompagnement["code"] as $code => $tmp_tab) {
			$style="";

			$chaine_js_traite_graisse_lignes.="traite_ligne('accompagnement_".$current_ele['id_eleve']."_".$code."');\n";

			echo "
				<tr>
					<!--
					<td><a href='#' onclick=\"coche_decoche_ligne('accompagnement_".$current_ele['id_eleve']."_".$code."');return false;\" style='text-decoration:none;color:black;'><label for='accompagnement_".$current_ele['id_eleve']."_".$code."' id='texte_accompagnement_".$current_ele['id_eleve']."_".$code."'".$style." title=\"".$tmp_tab["libelle"]."\">$code</label></a></td>
					-->
					<td><a href='#' onclick=\"coche_decoche_ligne('accompagnement_".$current_ele['id_eleve']."_".$code."');return false;\" style='text-decoration:none;color:black;' id='texte_accompagnement_".$current_ele['id_eleve']."_".$code."'".$style." title=\"".$tmp_tab["libelle"]."\">$code</a></td>";

			for($i=1;$i<$nb_periode;$i++) {

				//if(in_array($i, $tab_ele_per[$current_login])) {
				if(in_array($i, $tab_ele_per)) {
					$checked="";
					$style='';
					$textarea="";
					$chaine_textarea="";
					//		$tab_modalites_ele[$lig->id_eleve][$lig->code][$lig->periode]=$lig->commentaire;

					if(isset($tab_modalites_ele[$current_ele['id_eleve']][$code][$i])) {
						$checked=" checked";
						$style=" style='font-weight:bold'";

						$textarea=$tab_modalites_ele[$current_ele['id_eleve']][$code][$i];
					}

					if($tab_modalite_accompagnement["code"][$code]['avec_commentaire']=="y") {
						$chaine_textarea="<br /><textarea name='no_anti_inject_textarea_".$current_ele['id_eleve']."_".$code."_".$i."' id='no_anti_inject_textarea_".$current_ele['id_eleve']."_".$code."_".$i."' 
						onblur=\"if(this.value!='') {document.getElementById('accompagnement_".$current_ele['id_eleve']."_".$code."_".$i."').checked=true;traite_ligne('accompagnement_".$current_ele['id_eleve']."_".$code."')}\">".$textarea."</textarea>";
					}

					echo "
					<td>
						<input type='checkbox' name='accompagnement_".$current_ele['id_eleve']."_".$code."_".$i."' id='accompagnement_".$current_ele['id_eleve']."_".$code."_".$i."' value='$code' onchange=\"changement(); traite_ligne('accompagnement_".$current_ele['id_eleve']."_".$code."');\" ".$checked."/>".$chaine_textarea."
					</td>";
				}
				else {
					echo "
					<td></td>";
				}
			}
			echo "
				</tr>";

		}

		echo "
			</tbody>
		</table>

		<p><input type='submit' value='Enregistrer' /></p>
	</fieldset>
</form>";

	echo js_checkbox_change_style('checkbox_change', 'texte_', "y");


	echo "
<script type='text/javascript'>
	function traite_ligne(id) {
		coche=false;
		for(i=1;i<$nb_periode;i++) {
			if(document.getElementById(id+'_'+i)) {
				if(document.getElementById(id+'_'+i).checked==true) {
					//alert(id+'_'+i+' est coché.');
					coche=true;
					break;
				}
			}
		}
		//alert('coche='+coche);
		if(coche==true) {
			//alert('On met en gras texte_'+id);
			document.getElementById('texte_'+id).style.fontWeight='bold';
		}
		else {
			//alert('On met en normal texte_'+id);
			document.getElementById('texte_'+id).style.fontWeight='normal';
		}
	}

	function coche_decoche_ligne(id) {
		coche=false;
		i=1;
		if(document.getElementById(id+'_'+i)) {
			if(document.getElementById(id+'_'+i).checked==true) {
				coche=true;
			}
		}

		for(i=1;i<$nb_periode;i++) {
			if(document.getElementById(id+'_'+i)) {
				if(coche==true) {
					document.getElementById(id+'_'+i).checked=false;
				}
				else {
					document.getElementById(id+'_'+i).checked=true;
				}
			}
		}

		traite_ligne(id);
	}

$chaine_js_traite_graisse_lignes

</script>";
}
else {
	/*
	echo "<pre>";
	print_r($tab_modalite_accompagnement);
	echo "</pre>";
	*/

	echo "<h2>Modalités d'accompagnement en ".get_nom_classe($id_classe)."</h2>";

	$tab_ele=array();
	$tab_ele_per=array();
	$sql="SELECT DISTINCT e.*, jec.periode FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='".$id_classe."' ORDER BY jec.periode, e.nom, e.prenom;";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_assoc($res)) {
		$tab_ele[$lig['login']]=$lig;
		$tab_ele_per[$lig['login']][]=$lig["periode"];
	}

	$tab_modalites_ele=array();
	$sql="SELECT DISTINCT jmae.* FROM j_modalite_accompagnement_eleve jmae, 
				eleves e, 
				j_eleves_classes jec 
			WHERE jmae.id_eleve=e.id_eleve AND 
				jec.login=e.login AND 
				jec.periode=jmae.periode AND 
				jec.id_classe='".$id_classe."' 
			ORDER BY e.nom, e.prenom;";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tab_modalites_ele[$lig->id_eleve][$lig->code][$lig->periode]=$lig->commentaire;
	}

	$rowspan=count($tab_modalite_accompagnement["indice"]);

	$max_per=$nb_periode-1;

	$chaine_js_traite_graisse_lignes="";

	echo "
<form action='".$_SERVER["PHP_SELF"]."' method='post' name='form2'>
	<fieldset class='fieldset_opacite50'>
		<p><input type='submit' value='Enregistrer' /></p>
		<div class='center' id='fixe'>
			<input type='submit' value='Enregistrer' />
		</div>

		".add_token_field()."
		<input type='hidden' name='id_classe' value='$id_classe' />
		<input type='hidden' name='is_posted_modalites_classes' value='y' />";

	//for($loop=0;$loop<count($tab_ele);$loop++) {
	foreach($tab_ele as $current_login => $current_ele) {
		$chaine_mef="";
		if(isset($tab_mef[$current_ele['mef_code']])) {
			$chaine_mef="<br /><span style='font-size:small'>".$tab_mef[$current_ele['mef_code']]["libelle_edition"]."</span>";
		}
		echo "
		<!--
		<p><a href='../eleves/modify_eleve.php?eleve_login=".$current_login."' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Modifier les informations élève.\">".$current_ele["nom"]." ".$current_ele["prenom"]."</a> <a href='../eleves/visu_eleve.php?ele_login=".$current_login."' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Voir la fiche/classeur élève dans un nouvel onglet.\"><img src='../images/icons/ele_onglets.png' class='icone16' alt='Onglets' /></a></p>
		-->
		<table class='boireaus boireaus_alt' style='margin-bottom:1em;'>
			<thead>
				<tr>
					<th>
						Accompagnements<br />
						<a href='../eleves/modify_eleve.php?eleve_login=".$current_login."' onclick=\"return confirm_abandon (this, change, '$themessage')\">".$current_ele["nom"]." ".$current_ele["prenom"]."</a> 
						<a href='../eleves/visu_eleve.php?ele_login=".$current_login."' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/ele_onglets.png' class='icone16' alt='Onglets' /></a>".$chaine_mef."
					</th>";
		for($i=1;$i<$nb_periode;$i++) {
			echo "
					<th title=\"Période $i\">P.".$i."</th>";
		}
		echo "
				</tr>
			</thead>
			<tbody>";

		foreach($tab_modalite_accompagnement["code"] as $code => $tmp_tab) {
			$style="";

			$chaine_js_traite_graisse_lignes.="traite_ligne('accompagnement_".$current_ele['id_eleve']."_".$code."');\n";

			echo "
				<tr>
					<!--
					<td><a href='#' onclick=\"coche_decoche_ligne('accompagnement_".$current_ele['id_eleve']."_".$code."');return false;\" style='text-decoration:none;color:black;'><label for='accompagnement_".$current_ele['id_eleve']."_".$code."' id='texte_accompagnement_".$current_ele['id_eleve']."_".$code."'".$style." title=\"".$tmp_tab["libelle"]."\">$code</label></a></td>
					-->
					<td><a href='#' onclick=\"coche_decoche_ligne('accompagnement_".$current_ele['id_eleve']."_".$code."');return false;\" style='text-decoration:none;color:black;' id='texte_accompagnement_".$current_ele['id_eleve']."_".$code."'".$style." title=\"".$tmp_tab["libelle"]."\">$code</a></td>";

			for($i=1;$i<$nb_periode;$i++) {

				if(in_array($i, $tab_ele_per[$current_login])) {
					$checked="";
					$style='';
					$textarea="";
					$chaine_textarea="";
					//		$tab_modalites_ele[$lig->id_eleve][$lig->code][$lig->periode]=$lig->commentaire;

					if(isset($tab_modalites_ele[$current_ele['id_eleve']][$code][$i])) {
						$checked=" checked";
						$style=" style='font-weight:bold'";

						$textarea=$tab_modalites_ele[$current_ele['id_eleve']][$code][$i];
					}

					if($tab_modalite_accompagnement["code"][$code]['avec_commentaire']=="y") {
						$chaine_textarea="<br /><textarea name='no_anti_inject_textarea_".$current_ele['id_eleve']."_".$code."_".$i."' id='no_anti_inject_textarea_".$current_ele['id_eleve']."_".$code."_".$i."' 
						onblur=\"if(this.value!='') {document.getElementById('accompagnement_".$current_ele['id_eleve']."_".$code."_".$i."').checked=true;traite_ligne('accompagnement_".$current_ele['id_eleve']."_".$code."')}\">".$textarea."</textarea>";
					}

					echo "
					<td>
						<input type='checkbox' name='accompagnement_".$current_ele['id_eleve']."_".$code."_".$i."' id='accompagnement_".$current_ele['id_eleve']."_".$code."_".$i."' value='$code' onchange=\"changement(); traite_ligne('accompagnement_".$current_ele['id_eleve']."_".$code."');\" ".$checked."/>".$chaine_textarea."
					</td>";
				}
				else {
					echo "
					<td></td>";
				}
			}
			echo "
				</tr>";

		}

		echo "
			</tbody>
		</table>";
	}
	echo "
		<p><input type='submit' value='Enregistrer' /></p>
	</fieldset>
</form>";

	echo js_checkbox_change_style('checkbox_change', 'texte_', "y");

	echo "
<script type='text/javascript'>
	function traite_ligne(id) {
		coche=false;
		for(i=1;i<$nb_periode;i++) {
			if(document.getElementById(id+'_'+i)) {
				if(document.getElementById(id+'_'+i).checked==true) {
					//alert(id+'_'+i+' est coché.');
					coche=true;
					break;
				}
			}
		}
		//alert('coche='+coche);
		if(coche==true) {
			//alert('On met en gras texte_'+id);
			document.getElementById('texte_'+id).style.fontWeight='bold';
		}
		else {
			//alert('On met en normal texte_'+id);
			document.getElementById('texte_'+id).style.fontWeight='normal';
		}
	}

	function coche_decoche_ligne(id) {
		coche=false;
		i=1;
		if(document.getElementById(id+'_'+i)) {
			if(document.getElementById(id+'_'+i).checked==true) {
				coche=true;
			}
		}

		for(i=1;i<$nb_periode;i++) {
			if(document.getElementById(id+'_'+i)) {
				if(coche==true) {
					document.getElementById(id+'_'+i).checked=false;
				}
				else {
					document.getElementById(id+'_'+i).checked=true;
				}
			}
		}

		traite_ligne(id);
	}

$chaine_js_traite_graisse_lignes

</script>";
}



require("../lib/footer.inc.php");

?>
