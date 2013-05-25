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


// INSERT INTO droits VALUES('/mod_notanet/saisie_app.php','V','V','F','F','F','F','F','F','Notanet: Saisie des appréciations','');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


//$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :NULL);
$id_groupe=isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] : NULL);
$nb_tot_eleves=isset($_POST["nb_tot_eleves"]) ? $_POST["nb_tot_eleves"] : NULL;
$log_eleve=isset($_POST["log_eleve"]) ? $_POST["log_eleve"] : NULL;


if (isset($id_groupe)) {
	if((is_numeric($id_groupe))&&($id_groupe>0)) {
		$current_group = get_group($id_groupe);

		$matiere=$current_group["matiere"]["matiere"];
	}
	else {
		unset($id_groupe);
	}
}



if (isset($_POST['is_posted'])) {
	check_token();

	$pb_record="no";

	for($i=0;$i<$nb_tot_eleves;$i++) {

		if(isset($log_eleve[$i])) {
			$sql="SELECT 1=1 FROM notanet n, j_eleves_groupes jeg WHERE n.login=jeg.login AND jeg.login='$log_eleve[$i]' AND jeg.id_groupe='$id_groupe';";
			//echo "$sql<br />";
			$verif=mysql_query($sql);

			if(mysql_num_rows($verif)>0) {

				$nom_log = "app_eleve_".$i;

				//echo "\$nom_log=$nom_log<br />";

				if (isset($NON_PROTECT[$nom_log])){
					$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
				}
				else{
					$app = "";
				}

				$app=suppression_sauts_de_lignes_surnumeraires($app);

				$sql="SELECT * FROM notanet_app WHERE (login='$log_eleve[$i]' AND matiere='$matiere');";
				//echo "$sql<br />";
				$test_eleve_avis_query=mysql_query($sql);

				$test = mysql_num_rows($test_eleve_avis_query);
				if ($test != "0") {
					$sql="UPDATE notanet_app SET appreciation='$app' WHERE (login='$log_eleve[$i]' AND matiere='$matiere');";
					//echo "$sql<br />";
					$register=mysql_query($sql);
				} else {
					$sql="INSERT INTO notanet_app SET appreciation='$app', login='$log_eleve[$i]', matiere='$matiere';";
					//echo "$sql<br />";
					$register=mysql_query($sql);
				}
				if (!$register) {
					$msg = "Erreur lors de l'enregistrement des données pour $log_eleve[$i]<br />";
					//echo "ERREUR<br />";
					$pb_record = 'yes';
				}
			}
		}
	}

	if ($pb_record == 'no') { $affiche_message = 'yes';}
}


$themessage = 'Des appréciations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
//**************** EN-TETE *****************
$titre_page = "Fiches brevet | Saisie des appréciations";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

$tmp_timeout=(getSettingValue("sessionMaxLength"))*60;

?>
<script type="text/javascript" language="javascript">
change = 'no';
</script>

<p class=bold><a href="../accueil.php" onclick="return confirm_abandon(this, change, '<?php echo $themessage; ?>')"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>

<?php

if(!isset($id_groupe)) {
	echo "</p>\n";

	// En prof:
	/*
	$sql="SELECT DISTINCT g.*,c.classe FROM groupes g,
						j_groupes_classes jgc,
						j_groupes_professeurs jgp,
						j_groupes_matieres jgm,
						classes c,
						notanet n
					WHERE g.id=jgc.id_groupe AND
						jgc.id_classe=n.id_classe AND
						jgc.id_classe=c.id AND
						jgc.id_groupe=jgp.id_groupe AND
						jgp.login='".$_SESSION['login']."' AND
						jgm.id_groupe=g.id AND
						jgm.id_matiere=n.mat
					ORDER BY jgc.id_classe;";
	*/
	$sql="SELECT DISTINCT g.*,c.classe FROM groupes g,
						j_groupes_classes jgc,
						j_groupes_professeurs jgp,
						j_groupes_matieres jgm,
						classes c,
						notanet n
					WHERE g.id=jgc.id_groupe AND
						jgc.id_classe=n.id_classe AND
						jgc.id_classe=c.id AND
						jgc.id_groupe=jgp.id_groupe AND
						jgp.login='".$_SESSION['login']."' AND
						jgm.id_groupe=g.id AND
						jgm.id_matiere=n.matiere
					ORDER BY jgc.id_classe;";
	//echo "$sql<br />";
	$res_grp=mysql_query($sql);
	if(mysql_num_rows($res_grp)==0) {
		//echo "<p>Aucune de vos classes n'est concernée par le Brevet des collèges.<br />Ou alors votre administrateur n'a pas encore défini les classes/élèves concernés par le brevet.</p>\n";
		echo "<p>Aucune de vos classes n'est concernée par le Brevet des collèges.<br />Ou alors votre administrateur n'a pas encore effectué l'extraction des moyennes pour le brevet.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<p>Choisissez le groupe pour lequel vous souhaitez saisir les appréciations pour les fiches brevet: </p>\n";
	echo "<ul>\n";
	while($lig_grp=mysql_fetch_object($res_grp)) {
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_groupe=$lig_grp->id'>$lig_grp->description (<i>$lig_grp->classe</i>)</a></li>\n";
	}
	echo "</ul>\n";

	/*
	$affichage="<p>Choisissez le groupe pour lequel vous souhaitez saisir les appréciations pour les fiches brevet: </p>\n";
	$affichage.="<ul>\n";
	while($lig_grp=mysql_fetch_object($res_grp)) {



		$affichage.="<li><a href='".$_SERVER['PHP_SELF']."?id_groupe=$lig_grp-id_groupe'>$lig_grp->description (<i>$lig_grp->classe</i>)</a></li>\n";
	}
	$affichage.="</ul>\n";
	*/
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon(this, change, '$themessage')\">Choisir un autre groupe</a>\n";
	echo "</p>\n";

	$liste_eleves = $current_group["eleves"]["all"]["list"];

	//$matiere=$current_group["matiere"]["matiere"];

	echo "<p class='bold'>".$current_group['description']." (<i>".$current_group["classlist_string"]."</i>)</p>\n";

	//echo "<div id='div_photo_eleve' class='infobulle_corps' style='position: fixed; top: 220px; right: 20px; text-align:center; border:1px solid black; display:none;'></div>\n";
	$titre="Photo";
	$texte="Photo";
	$tabdiv_infobulle[]=creer_div_infobulle('div_photo_eleve',$titre,"",$texte,"",14,0,'y','y','n','n');


	echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo add_token_field();
	//echo "<table class='boireaus' width='100%'>\n";
	echo "<table class='boireaus'>\n";
	echo "<tr>\n";
	echo "<th colspan='2'>Elève</th>\n";
	if(count($current_group["classes"]["list"])>1) {
		echo "<th>Classe</th>\n";
	}
	echo "<th>Moy.<br />périodes</th>\n";
	echo "<th>Moy.<br />année</th>\n";
	echo "<th>Appréciation</th>\n";
	echo "</tr>\n";


	// Compteur pour les élèves
	$i=0;
	// Compteur de champs TEXTAREA
	$num_id=0;
	// Pour l'alternance des couleurs de lignes
	$alt=1;
	//=========================
	foreach ($liste_eleves as $eleve_login) {

		$temoin_photo="";


		if (in_array($eleve_login, $current_group["eleves"]["all"]["list"])) {
			//$num_id++;

			//
			// si l'élève appartient au groupe pour cette période
			//
			$eleve_nom = $current_group["eleves"]["all"]["users"][$eleve_login]["nom"];
			$eleve_prenom = $current_group["eleves"]["all"]["users"][$eleve_login]["prenom"];
			$eleve_classe = $current_group["classes"]["classes"][$current_group["eleves"]["all"]["users"][$eleve_login]["classe"]]["classe"];
			$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"]["all"]["users"][$eleve_login]["classe"]]["id"];

			//========================
			$sql="SELECT elenoet FROM eleves WHERE login='$eleve_login';";
			$res_ele=mysql_query($sql);
			$lig_ele=mysql_fetch_object($res_ele);
			$eleve_elenoet=$lig_ele->elenoet;

			// Photo...
			$photo=nom_photo($eleve_elenoet);
			$temoin_photo="";
			if($photo){
				$temoin_photo="y";
			}
			//========================

			$sql="SELECT na.appreciation FROM notanet_app na,
								notanet n
							WHERE (
									na.login='$eleve_login' AND
									n.matiere='$matiere' AND
									na.login=n.login AND
									na.matiere=n.matiere
								);";
			//echo "<tr><td colspan='3'>$sql</td></tr>";
			$app_query = mysql_query($sql);
			if(mysql_num_rows($app_query)>0) {
				$eleve_app = @mysql_result($app_query, 0, "appreciation");
			}
			else {
				$eleve_app="";
			}

			// Appel des notes
			//$sql="SELECT * FROM notanet n WHERE (n.login='$eleve_login' AND n.mat='$matiere');";
			$sql="SELECT n.note FROM notanet n WHERE (n.login='$eleve_login' AND n.matiere='$matiere');";
			//echo "<tr><td colspan='3'>$sql</td></tr>";
			$note_query = mysql_query($sql);

			if(mysql_num_rows($note_query)>0) {
				$eleve_note = @mysql_result($note_query, 0, "note");
			}
			else {
				$eleve_note="";
			}

			// Notes des périodes
			$sql="SELECT * FROM matieres_notes WHERE (login='$eleve_login' AND id_groupe='$id_groupe') ORDER BY periode;";
			$res_note_trim=mysql_query($sql);

			$eleve_notes_trim="";
			if(mysql_num_rows($res_note_trim)>0) {
				while($lig_note_trim=mysql_fetch_object($res_note_trim)) {
					if($lig_note_trim->statut!='') {
						$eleve_notes_trim.="P.$lig_note_trim->periode&nbsp;: <b>".$lig_note_trim->statut."</b><br />\n";
					}
					else {
						$eleve_notes_trim.="P.$lig_note_trim->periode&nbsp;: <b>".$lig_note_trim->note."</b><br />\n";
					}
				}
			}


			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";

			if($temoin_photo!="y") {
				echo "<td colspan='2'>$eleve_nom $eleve_prenom";
			}
			else {
				echo "<td>$eleve_nom $eleve_prenom</td>\n";
				echo "<td>\n";

				if(file_exists($photo)) {
					echo "<a href='#' onclick=\"afficher_div('div_photo_eleve','y',-100,20); affiche_photo('".$photo."','".addslashes(mb_strtoupper($eleve_nom)." ".ucfirst(mb_strtolower($eleve_prenom)))."');return false;\">";
					echo "<img src='../images/icons/buddy.png' alt='$eleve_nom $eleve_prenom' />";
					echo "</a>";
				}
			}
			echo "</td>\n";

			if(count($current_group["classes"]["list"])>1) {
				echo "<td>$eleve_classe</td>\n";
			}

			echo "<td style='font-size: small;'>\n";
			echo $eleve_notes_trim;
			echo "</td>\n";

			echo "<td>".$eleve_note."</td>\n";

			echo "<td>\n";

			$sql="SELECT 1=1 FROM notanet WHERE login='$eleve_login';";
			$test_notanet=mysql_query($sql);
			if(mysql_num_rows($test_notanet)>0) {
				echo "<input type='hidden' name='log_eleve[$i]' value=\"".$eleve_login."\" />\n";



				// AJOUTER UN TEST SUR UNE TABLE notanet_verrou pour proposer un TEXTAREA ou juste un affichage
				$sql="SELECT verrouillage FROM notanet_verrou nv,
										notanet n,
										notanet_ele_type net
									WHERE nv.id_classe=n.id_classe AND
										n.id_classe='$eleve_id_classe' AND
										n.login='$eleve_login' AND
										net.login=n.login AND
										nv.type_brevet=net.type_brevet
										;";
				//echo "$sql<br />";
				$test_verrouillage=mysql_query($sql);
				if(mysql_num_rows($test_verrouillage)==0) {
					$verrou="O";
				}
				else {
					$lig_verrou=mysql_fetch_object($test_verrouillage);
					$verrou=$lig_verrou->verrouillage;
				}

				if($verrou=="N") {
					echo "<textarea id=\"n".$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_eleve_".$i."\" rows='2' cols='80' wrap='virtual' onchange=\"changement()\"";

					//==================================
					// Rétablissement: boireaus 20080219
					// Pour revenir au champ suivant après validation/enregistrement:
					//echo " onfocus=\"focus_suivant(".$i.");\"";
					echo " onfocus=\"focus_suivant(".$num_id.");\"";
					//==================================

					echo ">".$eleve_app."</textarea>\n";
					$num_id++;
				}
				else {
					echo $eleve_app." <span style='color:gray; font-size:xx-small;'>(saisie verrouillée)</span>\n";
				}
			}
			else {
				echo "Extraction des moyennes non encore effectuée.";
			}
			echo  "</td>\n";

			echo "</tr>\n";


		}
		$i++;
	}
	echo "</table>\n";

	echo "<p><br /></p>\n";

	echo "<input type='hidden' name='is_posted' value='yes' />
	<input type='hidden' name='id_groupe' value='$id_groupe' />
	<input type='hidden' name='nb_tot_eleves' value='$i' />
	<center><div id='fixe'><input type='submit' value='Enregistrer' /><br />


<!-- DIV destiné à afficher un décompte du temps restant pour ne pas se faire piéger par la fin de session -->
<div id='decompte'></div>

<!-- Champ destiné à recevoir la valeur du champ suivant celui qui a le focus pour redonner le focus à ce champ après une validation -->
<input type='hidden' id='info_focus' name='champ_info_focus' value='' size='3' />
</div></center>
</form>
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
		dernier=num+".$i."
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

require("../lib/footer.inc.php");
die();
?>
