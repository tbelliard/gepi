<?php

/*
*/
if(function_exists("mb_detect_encoding")&&function_exists("mb_convert_encoding")){
	$string = "ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕŠÛÜÙÚÝŸŽáàâäãåçéèêëîïìíñôöðòóõšûüùúýÿž";
	$encoding = mb_detect_encoding($string, "UTF-8, ISO-8859-1");
	$string = mb_convert_encoding($string, "UTF-8", $encoding);
	//$string = mb_convert_encoding($string, "ISO-8859-1", $encoding);
} else {
	$string = "";
}
//echo $string;

	$rech_nom=isset($_POST['rech_nom']) ? $_POST['rech_nom'] : (isset($_GET['rech_nom']) ? $_GET['rech_nom'] : NULL);
	$rech_prenom=isset($_POST['rech_prenom']) ? $_POST['rech_prenom'] : (isset($_GET['rech_prenom']) ? $_GET['rech_prenom'] : NULL);

	$page=isset($_POST['page']) ? $_POST['page'] : (isset($_GET['page']) ? $_GET['page'] : "");

	if(($page!="fiche_eleve.php")&&($page!="visu_eleve.php")&&($page!="export_bull_eleve.php")&&($page!="import_bull_eleve.php")&&($page!="saisie_secours_eleve.php")&&($page!="consultation_annee_anterieure.php")) {
		$page="../logout.php?auto=2";
		// Remarque: Cela n'empêche pas de bricoler l'adresse destination des liens affichés...
		echo "Accès non autorisé.";
		die();
	}

	$nb_ele=0;

	$order_by=isset($_POST['order_by']) ? $_POST['order_by'] : (isset($_GET['order_by']) ? $_GET['order_by'] : "nom,prenom");

	if(isset($rech_nom)) {
		$rech_nom=preg_replace("/[^A-Za-z$string]/","",$rech_nom);

		if($order_by=='classe') {
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec, classes c WHERE e.nom LIKE '%$rech_nom%' AND jec.login=e.login AND jec.id_classe=c.id ORDER BY c.classe, e.nom, e.prenom;";
		}
		else {
			$sql="SELECT * FROM eleves WHERE nom LIKE '%$rech_nom%' ORDER BY nom, prenom;";
		}
		//echo "$sql<br />";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
	
		$nb_ele=mysqli_num_rows($res_ele);
	
		if($nb_ele==0){
			// On ne devrait pas arriver là.
			//echo "<p>Aucun nom d'élève ne contient la chaine $rech_nom.</p>\n";
			echo "<p>Aucun nom d'&eacute;l&egrave;ve ne contient la chaine $rech_nom.</p>\n";
		}
	}
	elseif(isset($rech_prenom)) {
		$rech_prenom=preg_replace("/[^A-Za-z$string]/","",$rech_prenom);
		//echo "rech_prenom=$rech_prenom<br />";

		if($order_by=='classe') {
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec, classes c WHERE e.prenom LIKE '%$rech_prenom%' AND jec.login=e.login AND jec.id_classe=c.id ORDER BY c.classe, e.nom, e.prenom;";
		}
		else {
			$sql="SELECT * FROM eleves WHERE prenom LIKE '%$rech_prenom%' ORDER BY nom, prenom;";
		}
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
	
		$nb_ele=mysqli_num_rows($res_ele);
	
		if($nb_ele==0){
			// On ne devrait pas arriver là.
			//echo "<p>Aucun nom d'élève ne contient la chaine $rech_nom.</p>\n";
			echo "<p>Aucun pr&eacute;nom d'&eacute;l&egrave;ve ne contient la chaine $rech_prenom.</p>\n";
		}
	}

	if($nb_ele>0){
		//echo "<p>La recherche a retourné <b>$nb_ele</b> réponse(s):</p>\n";
		echo "<p>La recherche a retourn&eacute; <b>$nb_ele</b> r&eacute;ponse";
		if($nb_ele>1) {echo "s";}
		echo ":</p>\n";
		echo "<table border='1' class='boireaus' summary='Liste des élèves'>\n";
		echo "<tr>\n";
		if(($page=='saisie_secours_eleve.php')||($page=='consultation_annee_anterieure.php')) {
			echo "<th>El&egrave;ve</th>\n";

			echo "<th>Classe(s)</th>\n";
		}
		else {
			echo "<th><a href='".$page."?page=$page";
			if(isset($rech_nom)) {echo "&amp;rech_nom=$rech_nom";}
			if(isset($rech_prenom)) {echo "&amp;rech_prenom=$rech_prenom";}
			echo "&amp;Recherche_sans_js=y'>El&egrave;ve</a></th>\n";

			echo "<th><a href='".$page."?order_by=classe";
			if(isset($rech_nom)) {echo "&amp;rech_nom=$rech_nom";}
			if(isset($rech_prenom)) {echo "&amp;rech_prenom=$rech_prenom";}
			echo "&amp;page=$page&amp;Recherche_sans_js=y'>Classe(s)</a></th>\n";
		}
		echo "</tr>\n";
		$alt=1;
		while($lig_ele=mysqli_fetch_object($res_ele)) {
			$ele_login=$lig_ele->login;
			$ele_nom=$lig_ele->nom;
			$ele_prenom=$lig_ele->prenom;
			//echo "<b>$ele_nom $ele_prenom</b>";
			$alt=$alt*(-1);
			if($page=='saisie_secours_eleve.php') {
				echo "<tr class='lig$alt'>\n";

				$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_classes jec WHERE jec.login='$ele_login' AND c.id=jec.id_classe ORDER BY jec.periode;";
				$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_clas)==0) {
					echo "<td>\n";
					echo htmlspecialchars("$ele_nom $ele_prenom");
					echo "</td>\n";

					echo "<td>\n";
					echo "aucune classe";
					echo "</td>\n";
				}
				elseif(mysqli_num_rows($res_clas)==1) {
					$lig_clas=mysqli_fetch_object($res_clas);
					echo "<td>\n";
					echo "<a href='$page?ele_login=$ele_login&amp;id_classe=$lig_clas->id'>".htmlspecialchars("$ele_nom $ele_prenom")."</a>";
					echo "</td>\n";

					echo "<td>\n";
					echo "<a href='$page?ele_login=$ele_login&amp;id_classe=$lig_clas->id'>".htmlspecialchars($lig_clas->classe)."</a>";
					echo "</td>\n";
				}
				else {
					echo "<td>\n";
					echo htmlspecialchars("$ele_nom $ele_prenom");
					echo "</td>\n";

					echo "<td>\n";
					$cpt=0;
					while($lig_clas=mysqli_fetch_object($res_clas)) {
						if($cpt>0) {echo ", ";}
						echo "<a href='$page?ele_login=$ele_login&amp;id_classe=$lig_clas->id'>".htmlspecialchars($lig_clas->classe)."</a>";
						$cpt++;
					}
					echo "</td>\n";
				}
				echo "</tr>\n";
			}
			elseif($page=='consultation_annee_anterieure.php') {
				echo "<tr class='lig$alt'>\n";

				$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_classes jec WHERE jec.login='$ele_login' AND c.id=jec.id_classe ORDER BY jec.periode;";
				$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_clas)==0) {
					echo "<td>\n";
					echo htmlspecialchars("$ele_nom $ele_prenom");
					echo "</td>\n";

					echo "<td>\n";
					echo "aucune classe";
					echo "</td>\n";
				}
				else {
					$lig_clas=mysqli_fetch_object($res_clas);
					echo "<td>\n";
					echo "<a href='$page?logineleve=$ele_login&amp;id_classe=$lig_clas->id'>".htmlspecialchars("$ele_nom $ele_prenom")."</a>";
					echo "</td>\n";

					echo "<td>\n";
					echo "<a href='$page?logineleve=$ele_login&amp;id_classe=$lig_clas->id'>".htmlspecialchars($lig_clas->classe)."</a>";
					echo "</td>\n";
				}
				echo "</tr>\n";
			}
			else {
				echo "<tr class='lig$alt'>\n";
				echo "<td>\n";
				//echo "<a href='visu_eleve.php?ele_login=$ele_login'>$ele_nom $ele_prenom</a>";
				echo "<a href='$page?ele_login=$ele_login'>".htmlspecialchars("$ele_nom $ele_prenom")."</a>";

				$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_classes jec WHERE jec.login='$ele_login' AND c.id=jec.id_classe ORDER BY jec.periode;";
				$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_clas)==0) {
					//echo " (<i>";
					echo "<td>\n";
					echo "aucune classe";
					//echo "</i>)\n";
					//echo "<br />\n";
					echo "</td>\n";
				}
				else {
					//echo "(<i>";
					echo "<td>\n";
					$cpt=0;
					while($lig_clas=mysqli_fetch_object($res_clas)) {
						if($cpt>0) {echo ", ";}
						//echo $lig_clas->classe;
						echo htmlspecialchars($lig_clas->classe);
						$cpt++;
					}
					//echo "</i>)";
					//echo "<br />\n";
					echo "</td>\n";
				}
				echo "</tr>\n";
			}
		}
		echo "</table>\n";
	}
?>
