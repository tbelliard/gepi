<?php
/*
* $Id$
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Laurent Viénot-Hauger
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


// INSERT INTO droits VALUES('/mod_notanet/saisie_avis.php','V','F','F','F','F','F','F','F','Notanet: Saisie avis chef etablissement','');
// INSERT INTO droits VALUES('/mod_notanet/saisie_avis.php','V','F','F','V','F','F','F','F','Notanet: Saisie avis chef etablissement','');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}



$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$msg="";

if (isset($_POST['is_posted'])) {
	check_token();

	$pb_record="no";

	$ele_login=isset($_POST["ele_login"]) ? $_POST["ele_login"] : NULL;
	$favorable=isset($_POST["favorable"]) ? $_POST["favorable"] : NULL;

	for($i=0;$i<count($ele_login);$i++) {
		// Vérifier si l'élève est bien dans la classe?
		// Inutile si seul l'admin accède et qu'on ne limite pas l'accès à telle ou telle classe


		$nom_log = "app_eleve_".$i;
		//echo "\$nom_log=$nom_log<br />";
		if (isset($NON_PROTECT[$nom_log])){
			$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
		}
		else{
			$app = "";
		}
		$app=suppression_sauts_de_lignes_surnumeraires($app);

		//if((isset($fav[$i]))||(isset($b2i[$i]))||(isset($b2i[$i]))) {
		if(isset($favorable[$i])) {
			//if(($favorable[$i]=='O')||($favorable[$i]=='N')) {
			if(($favorable[$i]=='O')||($favorable[$i]=='N')||($favorable[$i]=='')) {
				$sql="SELECT 1=1 FROM notanet_avis WHERE login='".$ele_login[$i]."';";
				$res_ele=mysql_query($sql);
				if(mysql_num_rows($res_ele)==0) {
					$sql="INSERT INTO notanet_avis SET login='".$ele_login[$i]."'";
					$sql.=",favorable='".$favorable[$i]."'";
					$sql.=",avis='".$app."'";
					$sql.=";";
				}
				else {
					$sql="UPDATE notanet_avis SET favorable='".$favorable[$i]."', avis='".$app."' WHERE login='".$ele_login[$i]."';";
				}
				//echo "$sql<br />";
				$register=mysql_query($sql);
				if (!$register) {
					$msg .= "Erreur lors de l'enregistrement des données pour $ele_login[$i]<br />";
					//echo "ERREUR<br />";
					$pb_record = 'yes';
				}
			}
			else {
				$msg .= "Erreur: Vous n'avez pas coché un avis favorable ou non pour $ele_login[$i]<br />";
				$pb_record = 'yes';
			}
		}
		else {
			// Si on ne coche pas l'avis favorable... pour ne pas perdre les saisies...
			$sql="SELECT 1=1 FROM notanet_avis WHERE login='".$ele_login[$i]."';";
			$res_ele=mysql_query($sql);
			if(mysql_num_rows($res_ele)==0) {
				$sql="INSERT INTO notanet_avis SET login='".$ele_login[$i]."'";
				//$sql.=",favorable='".$favorable[$i]."'";
				$sql.=",favorable=''";
				$sql.=",avis='".$app."'";
				$sql.=";";
			}
			else {
				//$sql="UPDATE notanet_avis SET favorable='".$favorable[$i]."', avis='".$app."' WHERE login='".$ele_login[$i]."';";
				$sql="UPDATE notanet_avis SET favorable='', avis='".$app."' WHERE login='".$ele_login[$i]."';";
			}
			//echo "$sql<br />";
			$register=mysql_query($sql);
			if (!$register) {
				$msg .= "Erreur lors de l'enregistrement des données pour $ele_login[$i]<br />";
				//echo "ERREUR<br />";
				$pb_record = 'yes';
			}
		}
	}

	if ($pb_record == 'no') {
		//$affiche_message = 'yes';
		$msg="Les modifications ont été enregistrées !";
	}
}


$themessage = 'Des modifications ont été effectuées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";

//**************** EN-TETE *****************
$titre_page = "Notanet | Saisie de l'avis du chef d'établissement";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

$tmp_timeout=(getSettingValue("sessionMaxLength"))*60;

?>
<script type="text/javascript" language="javascript">
change = 'no';
</script>

<?php
echo "<p class=bold><a href='../accueil.php' onclick=\"return confirm_abandon(this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>";

echo " | <a href='index.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accueil Notanet</a>\n";

if(!isset($id_classe)) {
	echo "</p>\n";


	//$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, notanet n,notanet_ele_type net WHERE p.id_classe = c.id AND c.id=n.id_classe ORDER BY classe;";
	$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_eleves_classes jec,notanet_ele_type net WHERE c.id=jec.id_classe AND net.login=jec.login ORDER BY classe;";
	//echo "$sql<br />";
	$call_classes=mysql_query($sql);

	$nb_classes=mysql_num_rows($call_classes);
	if($nb_classes==0){
		echo "<p>Aucune classe ne semble encore définie.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}
	else{
		// Choix de la classe...
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

		// Affichage sur 3 colonnes
		$nb_classes_par_colonne=round($nb_classes/2);

		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";

		$cpt_i = 0;

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while($lig_clas=mysql_fetch_object($call_classes)) {

			//affichage 2 colonnes
			if(($cpt_i>0)&&(round($cpt_i/$nb_classes_par_colonne)==$cpt_i/$nb_classes_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}

			echo "<input type='checkbox' name='id_classe[]' id='id_classe_".$cpt_i."' value='$lig_clas->id' />";
			echo "<label for='id_classe_".$cpt_i."' style='cursor: pointer;'>";
			echo "$lig_clas->classe</label>";
			echo "<br />\n";
			$cpt_i++;
		}

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
		echo "</form>\n";
	}
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon(this, change, '$themessage')\">Choisir d'autres classes</a>\n";
	echo "</p>\n";

	$sql="CREATE TABLE IF NOT EXISTS notanet_avis (
		login VARCHAR( 50 ) NOT NULL ,
		favorable ENUM( 'O', 'N' ) NOT NULL ,
		avis TEXT NOT NULL ,
		PRIMARY KEY ( login )
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$create_table=mysql_query($sql);


	echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo add_token_field();

	/*
	$tabdiv_infobulle[]=creer_div_infobulle('MS',"","","<center>Maîtrise du socle</center>","",10,0,'y','y','n','n');
	$tabdiv_infobulle[]=creer_div_infobulle('ME',"","","<center>Maîtrise de certains éléments du socle</center>","",12,0,'y','y','n','n');
	$tabdiv_infobulle[]=creer_div_infobulle('MN',"","","<center>Maîtrise du socle non évaluée</center>","",10,0,'y','y','n','n');
	$tabdiv_infobulle[]=creer_div_infobulle('AB',"","","<center>Absent</center>","",8,0,'y','y','n','n');
	*/

	$titre="<span id='span_titre_photo'>Photo</span>";
	$texte="Photo";
	$tabdiv_infobulle[]=creer_div_infobulle('div_photo_eleve',$titre,"",$texte,"",14,0,'y','y','n','n');

	$cpt=0;
	for($i=0;$i<count($id_classe);$i++) {

		echo "<p>Classe de <b>".get_class_from_id($id_classe[$i])."</b><br />\n";
		echo "<input type='hidden' name='id_classe[$i]' value='".$id_classe[$i]."' />\n";

		$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE (jec.id_classe='".$id_classe[$i]."' AND jec.login=e.login) ORDER BY e.nom,e.prenom,e.naissance;";
		//echo "$sql<br />";
		$res_ele=mysql_query($sql);
		if(mysql_num_rows($res_ele)==0) {
			echo "Aucun élève dans cette classe.</p>\n";
		}
		else {
			echo "<table class='boireaus' border='1' summary='Saisie avis'>\n";

			//===========================
			echo "<tr>\n";
			echo "<th rowspan='3' colspan='2'>Elève</th>\n";
			echo "<th colspan='2'>Avis favorable</th>\n";
			echo "<th rowspan='2'>Avis mitigé<br />ou<br />non saisi</th>\n";
			//echo "<th rowspan='3'>Motivation d'un avis défavorable</th>\n";
			echo "<th rowspan='3'>Motivation de l'avis</th>\n";
			echo "</tr>\n";
			//===========================
			echo "<tr>\n";

			echo "<th>";
			echo "Oui\n";
			echo "</th>\n";

			echo "<th>";
			echo "Non\n";
			echo "</th>\n";

			echo "</tr>\n";
			//===========================
			echo "<tr>\n";

			echo "<th>";
			echo "<a href=\"javascript:CocheColonne('fav_O_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('fav_O_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</th>\n";

			echo "<th>";
			echo "<a href=\"javascript:CocheColonne('fav_N_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('fav_N_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</th>\n";

			echo "<th>";
			echo "<a href=\"javascript:CocheColonne('fav_X_',$i)\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne('fav_X_',$i)\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</th>\n";

			echo "</tr>\n";
			//===========================


			$alt=1;
			while($lig_ele=mysql_fetch_object($res_ele)) {

				//========================
				$sql="SELECT elenoet FROM eleves WHERE login='$lig_ele->login';";
				$res_ele2=mysql_query($sql);
				$lig_ele2=mysql_fetch_object($res_ele2);
				$eleve_elenoet=$lig_ele2->elenoet;

				// Photo...
				$photo=nom_photo($eleve_elenoet);
				$temoin_photo="";
				if($photo){
					$temoin_photo="y";
				}
				//========================

				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				/*
				echo "<td>";
				echo "<input type='hidden' name='ele_login[$cpt]' value=\"".$lig_ele->login."\" />\n";
				echo $lig_ele->nom." ".$lig_ele->prenom;
				echo "</td>\n";
				*/
				if($temoin_photo!="y") {
					echo "<td colspan='2'>".$lig_ele->nom." ".$lig_ele->prenom;
				}
				else {
					echo "<td>".$lig_ele->nom." ".$lig_ele->prenom."</td>";

					echo "<td>";
					if(file_exists($photo)) {
						echo "<a href='#' onclick=\"afficher_div('div_photo_eleve','y',-100,20); affiche_photo('".$photo."','".addslashes(mb_strtoupper($lig_ele->nom)." ".ucfirst(mb_strtolower($lig_ele->prenom)))."');return false;\">";
						echo "<img src='../images/icons/buddy.png' alt=\"$lig_ele->nom $lig_ele->prenom\" />";
						echo "</a>";
					}
				}
				echo "<input type='hidden' name='ele_login[$cpt]' value=\"".$lig_ele->login."\" />\n";
				echo "</td>\n";


				$sql="SELECT * FROM notanet_avis WHERE login='".$lig_ele->login."';";
				$res_avis=mysql_query($sql);
				if(mysql_num_rows($res_avis)==0) {
					$def_fav="";
					$def_avis="";
				}
				else {
					$lig_avis=mysql_fetch_object($res_avis);
					$def_fav=$lig_avis->favorable;
					$def_avis=$lig_avis->avis;
				}

				echo "<td><input type='radio' name='favorable[$cpt]' id='fav_O_".$cpt."_".$i."' value='O' onchange='changement();' ";
				if($def_fav=='O') {echo "checked ";}
				echo "/></td>\n";

				echo "<td><input type='radio' name='favorable[$cpt]' id='fav_N_".$cpt."_".$i."' value='N' onchange='changement();' ";
				if($def_fav=='N') {echo "checked ";}
				echo "/></td>\n";

				echo "<td><input type='radio' name='favorable[$cpt]' id='fav_X_".$cpt."_".$i."' value='' onchange='changement();' ";
				if($def_fav=='') {echo "checked ";}
				echo "/></td>\n";

				echo "<td>\n";
				echo "<textarea id=\"n".$cpt."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_eleve_".$cpt."\" rows='2' cols='80' wrap='virtual' onchange=\"changement()\"";
				//==================================
				// Pour revenir au champ suivant après validation/enregistrement:
				//echo " onfocus=\"focus_suivant(".$cpt.");";
				//echo " document.getElementById('focus_courant').value='".$cpt."';";
				echo " onfocus=\"change_photo('".$photo."','".addslashes(mb_strtoupper($lig_ele->nom)." ".ucfirst(mb_strtolower($lig_ele->prenom)))."');focus_suivant(".$cpt."); document.getElementById('focus_courant').value='".$cpt."';\"";
				echo "\"";
				//==================================
				echo ">".$def_avis."</textarea>\n";
				echo "</td>\n";

				echo "</tr>\n";
				$cpt++;
			}

			echo "</table>\n";
		}
	}

	echo "<input type='hidden' name='is_posted' value='y' />\n";
	echo "<input type='hidden' name='nb_tot_eleves' value='$cpt' />\n";
	echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";


	echo "<center><div id='fixe'>";

	if(getSettingValue('notanet_dfsp')=='y') {
		// INSERT INTO setting SET name='notanet_dfsp', value='y';
		echo "<a href=\"#\" onClick=\"document.getElementById('n'+document.getElementById('focus_courant').value).value=document.getElementById('n'+document.getElementById('focus_courant').value).value+'Doit faire ses preuves';document.getElementById('n'+document.getElementById('focus_courant').value).focus();return false;\">Dfsp</a><br />\n";
	}
	echo "<input type='submit' value='Enregistrer' /><br />

<!-- DIV destiné à afficher un décompte du temps restant pour ne pas se faire piéger par la fin de session -->
<div id='decompte'></div>
</div>
</center>


<!-- Champ destiné à recevoir la valeur du champ suivant celui qui a le focus pour redonner le focus à ce champ après une validation -->
<input type='hidden' id='info_focus' name='champ_info_focus' value='' size='3' />
<input type='hidden' id='focus_courant' name='focus_courant' value='' size='3' />
";

	echo "</form>\n";


	echo "<script type='text/javascript'>

function CocheColonne(nom_col,num_classe) {
	for (var ki=0;ki<$cpt;ki++) {
		if(document.getElementById(nom_col+ki+'_'+num_classe)){
			document.getElementById(nom_col+ki+'_'+num_classe).checked = true;
		}
	}

	changement();
}

function DecocheColonne(nom_col,num_classe) {
	for (var ki=0;ki<$cpt;ki++) {
		if(document.getElementById(nom_col+ki+'_'+num_classe)){
			document.getElementById(nom_col+ki+'_'+num_classe).checked = false;
		}
	}

	changement();
}

</script>
";




	// Il faudra permettre de n'afficher ce décompte que si l'administrateur le souhaite.
	echo "<script type='text/javascript'>
	cpt=".$tmp_timeout.";
	compte_a_rebours='y';

	function decompte(cpt){
		if(compte_a_rebours=='y'){
			document.getElementById('decompte').innerHTML=cpt;
			if(cpt>0){
				cpt--;
			}

			setTimeout(\"decompte(\"+cpt+\")\",1000);
		}
		else{
			document.getElementById('decompte').style.display='none';
		}
	}

	decompte(cpt);

	function focus_suivant(num){
		temoin='';
		// La variable 'dernier' peut dépasser de l'effectif de la classe... mais cela n'est pas dramatique
		dernier=num+".$cpt."
		// On parcourt les champs à partir de celui de l'élève en cours jusqu'à rencontrer un champ existant
		// (pour réussir à passer un élève qui ne serait plus dans la période)
		// Après validation, c'est ce champ qui obtiendra le focus si on n'était pas à la fin de la liste.
		for(i=num;i<dernier;i++){
			suivant=i+1;
			if(temoin==''){
				if(document.getElementById('n'+suivant)){
					document.getElementById('info_focus').value=suivant;
					temoin=suivant;
				}
			}
		}

		document.getElementById('info_focus').value=temoin;
	}

	function affiche_photo(photo,nom_prenom) {
		document.getElementById('div_photo_eleve_contenu_corps').innerHTML='<div style=\'text-align: center\'><img src=\"'+photo+'\" width=\"150\" alt=\"Photo\" /><br />'+nom_prenom+'</div>';
		//alert('nom_prenom='+nom_prenom);
		document.getElementById('span_titre_photo').innerHTML=nom_prenom;
	}

	function change_photo(photo,nom_prenom) {
		if(document.getElementById('div_photo_eleve').style.display=='') {
			affiche_photo(photo,nom_prenom);
			afficher_div('div_photo_eleve','y',-100,20);
		}
	}

	";

	// Après validation, on donne le focus au champ qui suivait celui qui vient d'être rempli
	if(isset($_POST['champ_info_focus'])){
		if($_POST['champ_info_focus']!=""){
			echo "// On positionne le focus...
		document.getElementById('n".$_POST['champ_info_focus']."').focus();\n";
		}
	}
	echo "</script>\n";


}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
die();
?>
