<?php
/*
* Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_sso_table/traite_export_csv.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_sso_table/traite_export_csv.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='SSO table : Export CSV',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$mode=isset($_POST['mode']) ? $_POST['mode'] : NULL;

if(!isset($mode)) {
	header("Location: ./index.php?ctrl=cvsent&msg=Mode non choisi");
	die();
}
elseif($mode=="enregistrer_correspondances") {
	check_token();

	//debug_var();

	$msg="";
	$assoc=isset($_POST['assoc']) ? $_POST['assoc'] : array();
	if(count($assoc)==0) {
		$msg="Aucune association n'a été cochée.<br />";
	}
	else {
		$compteur=isset($_POST['compteur']) ? $_POST['compteur'] : 0;

		$nb_reg=0;
		$nb_err=0;
		//for($loop=0;$loop<count($assoc);$loop++) {
		for($loop=0;$loop<$compteur;$loop++) {
			if(isset($assoc[$loop])) {
				$tab=explode("|", $assoc[$loop]);
				if((isset($tab[1]))&&($tab[0]!="")&&($tab[1]!="")) {
					$sql="DELETE FROM sso_table_correspondance WHERE login_gepi='".$tab[1]."';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);

					$sql="DELETE FROM sso_table_correspondance WHERE login_sso='".$tab[0]."';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);

					$sql="INSERT INTO sso_table_correspondance SET login_sso='".$tab[0]."', login_gepi='".$tab[1]."';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'enregistrement de l'association ".$tab[0]." &lt;-&gt; ".$tab[1]."<br />";
						$nb_err++;
					}
					else {
						$nb_reg++;
					}
				}
			}
		}

		if($nb_reg>0) {
			$msg.="<span style='color:green'>".$nb_reg." association(s) enregistrée(s).</span><br />";
		}
		if($nb_err>0) {
			$msg.="<span style='color:red'>".$nb_err." erreur(s) lors de l'enregistrement des associations.</span><br />";
		}
	}
}

//$javascript_specifique[] = "lib/tablekit";
//$utilisation_tablekit="ok";

//**************** EN-TETE *****************
$titre_page = "Table correspondances SSO";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='./index.php?ctrl=cvsent'>Retour</a></p>";

if($mode=="upload") {

	echo "<h2>Réception puis analyse du fichier CSV</h2>";

	if(!isset($_FILES['fichier'])) {
		echo "<p style='color:red'>Echec de l'envoi du fichier.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	if(!isset($_FILES['fichier']['tmp_name'])) {
		echo "<p style='color:red'>Echec de l'envoi du fichier ou fichier non conservé.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		$file_tmp=$_FILES['fichier']['tmp_name'];
		if ($file_tmp=='') {
			echo "<p style='color:red'>Le nom du fichier temporaire est vide.</p>";
			require("../lib/footer.inc.php");
			die();
		}

		$tempdir=get_user_temp_directory();
		if($tempdir=="") {
			echo "<p style='color:red'>Dossier temporaire de l'utilisateur non valide.</p>";
			require("../lib/footer.inc.php");
			die();
		}

		$file_name=$_FILES['fichier']['name'];
		$file_size=$_FILES['fichier']['size'];
		$file_type=$_FILES['fichier']['type'];

		if(is_uploaded_file($file_tmp)) {
			$dest_file = "../temp/".$tempdir."/fichier_export_ent.csv";

			$res_copy=copy("$file_tmp" , "$dest_file");
			if(!$res_copy) {
				echo "<p style='color:red'>Echec de la copie du fichier dans le dossier temporaire de l'utilisateur.</p>";
				require("../lib/footer.inc.php");
				die();
			}
			else {

				$fp=fopen($dest_file,"r");

				if(!$fp){
					echo "<p style='color:red'>Impossible d'ouvrir le fichier CSV !</p>";
					require("../lib/footer.inc.php");
					die();
				}

				// Civilité;Nom;Prénom;Profil;Login;Identifiant ENT;Etablissement;
				// Lire la ligne d'entête pour repérer les indices des colonnes recherchées
				//$tabchamps = array("Civilité", "Nom", "Prénom", "Prenom", "Profil", "Login", "Identifiant ENT", "Etablissement");
				$tabchamps = array("Nom", "Prénom", "Profil", "Login", "Identifiant ENT");

				// Lecture de la ligne 1 et la mettre dans $temp
				$ligne_entete=trim(fgets($fp,4096));
				if((substr($ligne_entete,0,3) == "\xEF\xBB\xBF")) {
					$ligne_entete=substr($ligne_entete,3);
				}
				//echo "$ligne_entete<br />";
				$en_tete=explode(";", $ligne_entete);

				$tabindice=array();

				// On range dans tabindice les indices des champs retenus
				for ($k = 0; $k < count($tabchamps); $k++) {
					//echo "<br /><p style='text-indent:-4em;margin-left:4em'>Recherche du champ ".$tabchamps[$k]."<br />";
					for ($i = 0; $i < count($en_tete); $i++) {
						//echo "\$en_tete[$i]=$en_tete[$i]<br />";
						//echo casse_mot(remplace_accents($en_tete[$i]),'min')."<br />";
						//echo casse_mot(remplace_accents($tabchamps[$k]), 'min')."<br />";
						if (casse_mot(remplace_accents($en_tete[$i]),'min') == casse_mot(remplace_accents($tabchamps[$k]), 'min')) {
							$tabindice[$tabchamps[$k]] = $i;
							//echo "\$tabindice[$tabchamps[$k]]=$i<br />";
						}
					}
				}
				if((!isset($tabindice['Nom']))||((!isset($tabindice['Prénom']))&&(!isset($tabindice['Prenom'])))||(!isset($tabindice['Identifiant ENT']))) {
					echo "<p style='color:red'>La ligne d'entête ne comporte pas un des champs indispensables (<em>Nom, Prénom, Identifiant ENT</em>).</p>";
					require("../lib/footer.inc.php");
					die();
				}

				$cpt=0;
				for($loop=0;$loop<count($tabchamps);$loop++) {
					if(isset($tabindice[$tabchamps[$loop]])) {
						$cpt++;
					}
				}

				// Le tri pose des problèmes avec les rowspan
				//echo "<table class='boireaus boireaus_alt resizable sortable'>
				echo "
<form action='".$_SERVER['PHP_SELF']."' method='post'>
".add_token_field()."
<input type='hidden' name='mode' value='enregistrer_correspondances' />
<p>Choisissez les correspondances à enregistrer et validez en bas de page.</p>
<table class='boireaus boireaus_alt'>
	<thead>
		<tr>
			<th colspan='$cpt'>Fichier CSV</th>
			<th rowspan='2'>
				<a href=\"javascript:tout_cocher()\" title=\"Cocher toutes les cases qui n'ont qu'une seule association possible.\"><img src='../images/enabled.png' width='20' height='20' /></a>
				/
				<a href=\"javascript:tout_decocher()\" title='Décocher toutes les cases à cocher'><img src='../images/disabled.png' width='20' height='20' /></a>
			</th>
			<th colspan='5'>Base GEPI</th>
		</tr>
		<tr>";
				for($loop=0;$loop<count($tabchamps);$loop++) {
					if(isset($tabindice[$tabchamps[$loop]])) {
						echo "
			<th>".$tabchamps[$loop]."</th>";
					}
				}
				echo "
			<th>Login</th>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Statut</th>
			<th>Infos</th>
		</tr>
	</thead>
	<tbody>";

				$compteur=0;
				while (!feof($fp)) {
					$ligne = trim(fgets($fp, 4096));
					if((substr($ligne,0,3) == "\xEF\xBB\xBF")) {
						$ligne=substr($ligne,3);
					}

					if($ligne!='') {
						$infos="";
						$tab=explode(";", ensure_utf8($ligne));

						// Faire la recherche et faire un rowspan si plusieurs réponses.
						$sql="SELECT * FROM utilisateurs WHERE nom LIKE '%".remplace_accents($tab[$tabindice['Nom']], "%")."%' AND prenom LIKE '%".remplace_accents($tab[$tabindice['Prénom']], "%")."%';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);

						if(mysqli_num_rows($res)==1) {
							$lig=mysqli_fetch_object($res);

							$sql="SELECT * FROM sso_table_correspondance WHERE login_gepi='".$lig->login."';";
							$res2=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res2)>0) {
								$lig2=mysqli_fetch_object($res2);
								if($lig2->login_sso==$tab[$tabindice['Identifiant ENT']]) {
									$infos="<span style='color:green' title=\"Il n'est pas utile de refaire l'enregistrement.\">Déjà associé à ".$lig2->login_sso."</span>";
								}
								else {
									$infos="<span style='color:red' title=\"L'identifiant ENT aurait changé ?\"><strong>Attention&nbsp;:</strong> Préalablement associé à ".$lig2->login_sso."</span>";
								}
							}

							echo "
		<tr>";
							for($loop=0;$loop<count($tabchamps);$loop++) {
								if(isset($tabindice[$tabchamps[$loop]])) {
									echo "
			<td>".$tab[$tabindice[$tabchamps[$loop]]]."</td>";
								}
							}
							echo "
			<td>
				<input type='checkbox' name='assoc[$compteur]' id='assoc_".$compteur."' value=\"".$tab[$tabindice["Identifiant ENT"]]."|".$lig->login."\" onchange=\"checkbox_change(this.id); changement();\" />
			</td>
			<td><label for='assoc_".$compteur."' id='texte_assoc_".$compteur."'>".$lig->login."</label></td>
			<td><label for='assoc_".$compteur."' id='texte_assoc_".$compteur."'>".$lig->nom."</label></td>
			<td><label for='assoc_".$compteur."' id='texte_assoc_".$compteur."'>".$lig->prenom."</label></td>
			<td>".$lig->statut."</td>
			<td>$infos</td>
		</tr>";
						}
						elseif(mysqli_num_rows($res)==0) {
							echo "
		<tr>";
							for($loop=0;$loop<count($tabchamps);$loop++) {
								if(isset($tabindice[$tabchamps[$loop]])) {
									echo "
			<td>".$tab[$tabindice[$tabchamps[$loop]]]."</td>";
								}
							}
							echo "
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>$infos</td>
		</tr>";
						}
						else {
							echo "
		<tr>";
							for($loop=0;$loop<count($tabchamps);$loop++) {
								if(isset($tabindice[$tabchamps[$loop]])) {
									echo "
			<td rowspan='".(mysqli_num_rows($res)+1)."'>".$tab[$tabindice[$tabchamps[$loop]]]."</td>";
								}
							}
								echo "
			<td>
				<input type='radio' name='assoc[$compteur]' value=\"|\" />
			</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>$infos</td>
		</tr>";

							$cpt=0;
							while($lig=mysqli_fetch_object($res)) {
								$infos="";

								$sql="SELECT * FROM sso_table_correspondance WHERE login_gepi='".$lig->login."';";
								$res2=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res2)>0) {
									$lig2=mysqli_fetch_object($res2);
									if($lig2->login_sso==$tab[$tabindice['Identifiant ENT']]) {
										$infos="<span style='color:green' title=\"Il n'est pas utile de refaire l'enregistrement.\">Déjà associé à ".$lig2->login_sso."</span>";
									}
									else {
										$infos="<span style='color:red' title=\"L'identifiant ENT aurait changé ?\"><strong>Attention&nbsp;:</strong> Préalablement associé à ".$lig2->login_sso."</span>";
									}
								}

								//if($cpt>0) {
								echo "
		<tr>";
								//}
								echo "
			<td>
				<input type='radio' name='assoc[$compteur]' id='assoc_".$compteur."_".$cpt."' value=\"".$tab[$tabindice["Identifiant ENT"]]."|".$lig->login."\" onchange=\"change_style_radio(this.id); changement();\" />
			</td>
			<td><label for='assoc_".$compteur."_".$cpt."' id='texte_assoc_".$compteur."_".$cpt."'>".$lig->login."</label></td>
			<td><label for='assoc_".$compteur."_".$cpt."' id='texte_assoc_".$compteur."_".$cpt."'>".$lig->nom."</label></td>
			<td><label for='assoc_".$compteur."_".$cpt."' id='texte_assoc_".$compteur."_".$cpt."'>".$lig->prenom."</label></td>
			<td>".$lig->statut."</td>
			<td>$infos</td>
		</tr>";
								$cpt++;
							}
						}
					}
					$compteur++;
				}

				echo "
	</tbody>
</table>
<input type='hidden' name='compteur' value='$compteur' />
<p><input type='submit' value='Enregistrer les correspondances' /></p>
</form>

<script type='text/javascript'>
".js_checkbox_change_style()."
".js_change_style_radio()."

	function tout_cocher() {
		for(i=0;i<$compteur;i++) {
			if(document.getElementById('assoc_'+i)) {
				document.getElementById('assoc_'+i).checked=true;
				checkbox_change('assoc_'+i);
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$compteur;i++) {
			if(document.getElementById('assoc_'+i)) {
				document.getElementById('assoc_'+i).checked=false;
				checkbox_change('assoc_'+i);
			}
		}
	}

</script>";
			}
		}
	}
}
elseif($mode=='enregistrer_correspondances') {
	echo "<h2>Fin du traitement demandé</h2>
<p>Bonne continuation.</p>";
}
else {
	echo "<p style='color:red'>Mode invalide.</p>";
}

require("../lib/footer.inc.php");
?>
