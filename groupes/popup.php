<?php
/*
* Last modification  : 28/09/2006
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

//INSERT INTO droits VALUES ('/groupes/popup.php', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation des équipes pédagogiques', '');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}



$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL;
$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;
$msg="";

//echo "<!--\$id_classe=$id_classe-->\n";
if($id_classe==""){
	unset($id_classe);
}

if(isset($id_groupe)){

	// A FAIRE: TESTER LE CARACTERE NUMERIQUE DE $id_groupe
	if($id_groupe=="VIE_SCOLAIRE"){
		$enseignement="VIE SCOLAIRE";
	}
	else{
		if(strlen(ereg_replace("[0-9]","",$id_groupe))!=0){
			header("Location: ../accueil.php?msg=Numero_de_groupe_non_valide");
			die();
		}

		$current_group=get_group($id_groupe);
		$enseignement=$current_group['description'];
	}

	if(isset($id_classe)){

		// A FAIRE: TESTER LE CARACTERE NUMERIQUE DE $id_classe
		if(strlen(ereg_replace("[0-9]","",$id_classe))!=0){
			header("Location: ../accueil.php?msg=Numero_de_classe_non_valide");
			die();
		}

		$sql="SELECT classe FROM classes WHERE id='$id_classe'";
		$res_classe=mysql_query($sql);
		if(mysql_num_rows($res_classe)==1){
			$lig_classe=mysql_fetch_object($res_classe);
			$classe=$lig_classe->classe;
		}
		elseif(mysql_num_rows($res_classe)>1){
			$msg.="ERREUR: Plus d'une classe semble correspondre à la classe n°$id_classe";
		}
		else{
			$msg.="ERREUR: Aucune classe ne semble correspondre à la classe n°$id_classe.";
		}
	}
}
else{
	//header("Location: ../logout.php?auto=1");
	header("Location: ../accueil.php?msg=Aucun_groupe_choisi");
	die();
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php
	//$enseignement=urldecode($_GET['enseignement']);
	//$enseignement=rawurldecode($_GET['enseignement']);

	if(isset($id_classe)){
		echo "<title>Elèves de l'enseignement ".htmlentities($enseignement)." en ".htmlentities($classe)."</title>\n";
	}
	else{
		echo "<title>Elèves de l'enseignement ".htmlentities($enseignement)."</title>\n";
	}
?>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
	<meta name="author" content="Stephane Boireau, A.S. RUE de Bernay/Pont-Audemer" />
	<!--link type="text/css" rel="stylesheet" href="../styles.css" /-->
	<link type="text/css" rel="stylesheet" href="../style.css" />
</head>
<body>

<?php
	if($msg!=""){
		echo "<p align='center'>".$msg."</p>\n";
	}

	//echo "<h2>Elèves de l'enseignement $enseignement</h2>\n";
	if(isset($id_classe)){
		echo "<h2>Elèves de l'enseignement ".htmlentities($enseignement)." en ".htmlentities($classe)."</h2>\n";
	}
	else{
		echo "<h2>Elèves de l'enseignement ".htmlentities($enseignement)."</h2>\n";
	}

	//echo "<p>Effectif de l'enseignement: ".$_GET['effectif']."</p>\n";
	//echo "<p>".urldecode($_GET['chaine'])."</p>\n";
	//echo "<p>".rawurldecode($_GET['chaine'])."</p>";

	if($id_groupe=="VIE_SCOLAIRE"){
		$sql="SELECT DISTINCT e.nom,e.prenom FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe' ORDER BY e.nom,e.prenom";
		$res_eleves=mysql_query($sql);
		$nb_eleves=mysql_num_rows($res_eleves);

		echo "<p>Effectif de la classe: $nb_eleves</p>\n";
		if($nb_eleves>0){
			echo "<p>";
			while($lig_eleve=mysql_fetch_object($res_eleves)){
				echo "$lig_eleve->nom $lig_eleve->prenom<br />\n";
			}
			echo "</p>\n";
		}
	}
	elseif(isset($id_classe)){
		//$sql="SELECT DISTINCT e.nom,e.prenom FROM j_eleves_groupes jeg,eleves e WHERE jeg.login=e.login AND jeg.id_groupe='$id_groupe' ORDER BY e.nom,e.prenom";
		$sql="SELECT DISTINCT e.nom,e.prenom,c.classe FROM j_eleves_groupes jeg, eleves e, j_eleves_classes jec, j_groupes_classes jgc, classes c WHERE jeg.login=e.login AND jeg.id_groupe='$id_groupe' AND jgc.id_classe=c.id AND jgc.id_groupe=jeg.id_groupe AND jec.id_classe=c.id AND jec.login=e.login AND c.id='$id_classe' ORDER BY e.nom,e.prenom";
		$res_eleves=mysql_query($sql);
		$nb_eleves=mysql_num_rows($res_eleves);

		$sql="SELECT DISTINCT e.nom,e.prenom,c.classe FROM j_eleves_groupes jeg, eleves e, j_eleves_classes jec, j_groupes_classes jgc, classes c WHERE jeg.login=e.login AND jeg.id_groupe='$id_groupe' AND jgc.id_classe=c.id AND jgc.id_groupe=jeg.id_groupe AND jec.id_classe=c.id AND jec.login=e.login ORDER BY e.nom,e.prenom";
		$res_tous_eleves=mysql_query($sql);
		$nb_tous_eleves=mysql_num_rows($res_tous_eleves);

		echo "<p>Effectif de l'enseignement: $nb_eleves/$nb_tous_eleves</p>\n";
		if($nb_eleves>0){
			echo "<p>";
			while($lig_eleve=mysql_fetch_object($res_eleves)){
				echo "$lig_eleve->nom $lig_eleve->prenom<br />\n";
			}
			echo "</p>\n";
		}
	}
	else{
		$sql="SELECT DISTINCT e.nom,e.prenom,c.classe FROM j_eleves_groupes jeg, eleves e, j_eleves_classes jec, j_groupes_classes jgc, classes c WHERE jeg.login=e.login AND jeg.id_groupe='$id_groupe' AND jgc.id_classe=c.id AND jgc.id_groupe=jeg.id_groupe AND jec.id_classe=c.id AND jec.login=e.login";
		if(isset($_GET['orderby'])){
			if($_GET['orderby']=='nom'){
				$orderby=" ORDER BY e.nom,e.prenom";
			}
			else{
				$orderby=" ORDER BY c.classe,e.nom,e.prenom";
			}
		}
		else{
			$orderby=" ORDER BY e.nom,e.prenom";
		}
		$sql.=$orderby;
		$res_eleves=mysql_query($sql);
		$nb_eleves=mysql_num_rows($res_eleves);
		echo "<p>Effectif: $nb_eleves</p>\n";
		if($nb_eleves>0){
			echo "<table border='0'>\n";
			echo "<tr><td><a href='".$_SERVER['PHP_self']."?id_groupe=$id_groupe&amp;orderby=nom'>Elève</a></td><td><a href='".$_SERVER['PHP_self']."?id_groupe=$id_groupe&amp;orderby=classe'>Classe</a></td></tr>\n";
			while($lig_eleve=mysql_fetch_object($res_eleves)){
				echo "<tr><td>$lig_eleve->nom $lig_eleve->prenom</td><td>$lig_eleve->classe</td></tr>\n";
			}
			echo "</table>\n";
		}
	}
?>
<script language="JavaScript" type="text/javascript">
	window.focus();
</script>
</body>
</html>
