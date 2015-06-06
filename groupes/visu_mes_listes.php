<?php
/*
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//INSERT INTO droits VALUES ('/groupes/visu_mes_listes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Accès aux listes d élèves', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//**************** EN-TETE **************************************
//$titre_page = "Gestion des groupes";
$titre_page = "Listes d'élèves";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************
//debug_var();
echo "<p class='bold'>";
echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
//echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un(e) autre classe/groupe</a>";
echo "</p>\n";

echo "<script type='text/javascript'>
	var fen;
	function ouvre_popup_visu_groupe(id_groupe,id_classe,periode_num) {
		//alert('azerty');
		eval(\"fen=window.open('popup.php?id_groupe=\"+id_groupe+\"&id_classe=\"+id_classe+\"&periode_num=\"+periode_num+\"','','width=400,height=400,menubar=yes,scrollbars=yes')\");
		setTimeout('fen.focus()',500);
	}
</script>";

//$id_classe=isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL;
$id_classe=isset($_GET['id_classe']) ? $_GET["id_classe"] : NULL;
$id_groupe=isset($_GET['id_groupe']) ? $_GET["id_groupe"] : NULL;
$ok=isset($_GET['ok']) ? $_GET["ok"] : NULL;

	echo "<h3>Mes listes d'".$gepiSettings['denomination_eleves']."</h3>\n";

	if($_SESSION['statut']=='professeur') {
		echo "<p>Sélectionnez l'enseignement et la période pour lesquels vous souhaitez visualiser la liste des ".$gepiSettings['denomination_eleves']."&nbsp;:</p>\n";
		$sql="SELECT DISTINCT g.id,g.description FROM groupes g, j_groupes_professeurs jgp WHERE
			jgp.login = '".$_SESSION['login']."' AND
			g.id=jgp.id_groupe
			ORDER BY g.description";
		$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);

		if(mysqli_num_rows($res_grp)==0) {
			echo "<p>Vous n'avez apparemment aucun enseignement.</p>\n";
			echo "</body></html>\n";
			die();
		}
		else {
			echo "<table>\n";
			while($lig_grp=mysqli_fetch_object($res_grp)) {
				echo "<tr>\n";
				unset($tabnumper);
				unset($tabnomper);
				$sql="SELECT DISTINCT c.classe,c.id FROM classes c, j_groupes_classes jgc WHERE
					jgc.id_groupe='$lig_grp->id' AND
					jgc.id_classe=c.id
					ORDER BY c.classe";
				//echo "$sql<br />\n";
				$res_class=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_class)>0) {
					$chaine_class="";
					$cpt=0;
					while($lig_class=mysqli_fetch_object($res_class)) {
						$chaine_class.=",$lig_class->classe";

						if($cpt==0) {
							$tabnumper=array();
							$tabnomper=array();
							$sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$lig_class->id' ORDER BY num_periode";
							$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_per)==0) {
								echo "<p>ERREUR: Aucune période n'est définie pour la classe $lig_class->classe</p>\n";
								echo "</body></html>\n";
								die();
							}
							else{
								while($lig_per=mysqli_fetch_object($res_per)) {
									$tabnumper[]=$lig_per->num_periode;
									$tabnomper[]=$lig_per->nom_periode;
								}
							}
						}
						$cpt++;
					}
					$chaine_class=mb_substr($chaine_class,1);

				}

				echo "<td>\n";
				echo "<b>$chaine_class</b>: ".htmlspecialchars($lig_grp->description);
				echo "</td>\n";
				for($i=0;$i<count($tabnumper);$i++) {
					if($i>0) {echo "<td> - </td>\n";}
					echo "<td>\n";
					echo "<a href='popup.php?id_groupe=$lig_grp->id&amp;periode_num=$tabnumper[$i]' onclick=\"ouvre_popup_visu_groupe('$lig_grp->id','','$tabnumper[$i]');return false;\" target='_blank'>".htmlspecialchars($tabnomper[$i])."</a>\n";
					echo "</td>\n";
				}
				echo "</tr>\n";
			}
			echo "</table>\n";
			echo "</body></html>\n";
			die();
		}

	}
	elseif($_SESSION['statut']=='cpe') {
		echo "<p>Sélectionnez la classe et la période pour lesquels vous souhaitez visualiser la liste des ".$gepiSettings['denomination_eleves']."&nbsp;:</p>\n";
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe=c.id ORDER BY c.classe";
		if(getSettingAOui('GepiAccesTouteFicheEleveCpe')) {
			$sql2="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY classe";
		}
	}
	elseif($_SESSION['statut']=='scolarite') {
		echo "<p>Sélectionnez la classe et la période pour lesquels vous souhaitez visualiser la liste des ".$gepiSettings['denomination_eleves']."&nbsp;:</p>\n";
		//$sql="SELECT id,classe FROM classes ORDER BY classe";
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
		if(getSettingAOui('GepiAccesTouteFicheEleveScolarite')) {
			$sql2="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY classe";
		}
	}
	else{
		echo "<p>Sélectionnez la classe et la période pour lesquels vous souhaitez visualiser la liste des ".$gepiSettings['denomination_eleves']."&nbsp;:</p>\n";
		//$sql="SELECT id,classe FROM classes ORDER BY classe";
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe;";
	}
	$result_classes=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_classes = mysqli_num_rows($result_classes);

	if((mysqli_num_rows($result_classes)==0)&&(isset($sql2))) {
		$result_classes=mysqli_query($GLOBALS["mysqli"], $sql2);
		$nb_classes = mysqli_num_rows($result_classes);
	}
	//echo "\$sql2=$sql2<br />";
	if(mysqli_num_rows($result_classes)==0) {
		echo "<p>Il semble qu'aucune classe n'ait encore été créée...<br />... ou alors aucune classe ne vous a été attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else {
		$tab_classes_deja=array();
		$nb_classes=mysqli_num_rows($result_classes);
		$nb_class_par_colonne=round($nb_classes/2);
		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='left'>\n";
		$cpt=0;
		//echo "<td style='padding: 0 10px 0 10px'>\n";
		echo "<td>\n";
		echo "<table border='0'>\n";
		while($lig_class=mysqli_fetch_object($result_classes)) {
			if(($cpt>0)&&(round($cpt/$nb_class_par_colonne)==$cpt/$nb_class_par_colonne)) {
				echo "</table>\n";
				echo "</td>\n";
				//echo "<td style='padding: 0 10px 0 10px'>\n";
				echo "<td>\n";
				echo "<table border='0'>\n";
			}

			$sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$lig_class->id' ORDER BY num_periode";
			$res_per=mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($res_per)==0) {
				echo "<p>ERREUR: Aucune période n'est définie pour la classe $lig_class->classe</p>\n";
				echo "</body></html>\n";
				die();
			}
			else{
				echo "<tr>\n";
				echo "<td>$lig_class->classe</td>\n";
				while($lig_per=mysqli_fetch_object($res_per)) {
					echo "<td> - <a href='popup.php?id_classe=$lig_class->id&amp;periode_num=$lig_per->num_periode' onclick=\"ouvre_popup_visu_groupe('VIE_SCOLAIRE','$lig_class->id','$lig_per->num_periode');return false;\" target='_blank'>".$lig_per->nom_periode."</a></td>\n";
				}
				echo "</tr>\n";
			}
			$tab_classes_deja[]=$lig_class->id;
			$cpt++;
		}
		echo "</table>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		if(isset($sql2)) {
			$result_classes=mysqli_query($GLOBALS["mysqli"], $sql2);
			$nb_classes = mysqli_num_rows($result_classes);

			$tab_autres_classes=array();
			while($lig_class=mysqli_fetch_object($result_classes)) {
				if(!in_array($lig_class->id, $tab_classes_deja)) {
					$tab_autres_classes[$lig_class->id]=$lig_class->classe;
				}
			}

			if(count($tab_autres_classes)>0) {
				echo "<p>Voici les autres classes (<em>celles dont vous n'êtes pas 'responsable' privilégié</em>)&nbsp;:</p>";

				$nb_class_par_colonne=round(count($tab_autres_classes)/2);
				echo "<table width='100%'>\n";
				echo "<tr valign='top' align='left'>\n";
				$cpt=0;
				echo "<td>\n";
				echo "<table border='0'>\n";
				foreach($tab_autres_classes as $current_id_classe => $current_classe) {
					if(($cpt>0)&&(round($cpt/$nb_class_par_colonne)==$cpt/$nb_class_par_colonne)) {
						echo "</table>\n";
						echo "</td>\n";
						//echo "<td style='padding: 0 10px 0 10px'>\n";
						echo "<td>\n";
						echo "<table border='0'>\n";
					}

					$sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$current_id_classe' ORDER BY num_periode";
					$res_per=mysqli_query($GLOBALS["mysqli"], $sql);

					if(mysqli_num_rows($res_per)==0) {
						echo "<p>ERREUR: Aucune période n'est définie pour la classe $current_classe</p>\n";
						echo "</body></html>\n";
						die();
					}
					else{
						echo "<tr>\n";
						echo "<td>$current_classe</td>\n";
						while($lig_per=mysqli_fetch_object($res_per)) {
							echo "<td> - <a href='popup.php?id_classe=$current_id_classe&amp;periode_num=$lig_per->num_periode' onclick=\"ouvre_popup_visu_groupe('VIE_SCOLAIRE','$current_id_classe','$lig_per->num_periode');return false;\" target='_blank'>".$lig_per->nom_periode."</a></td>\n";
						}
						echo "</tr>\n";
					}
					$cpt++;
				}
				echo "</table>\n";
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";

			}
		}

	}
//}

require("../lib/footer.inc.php");
?>
