<?php
	// Initialisations files
	require_once("../lib/initialisations.inc.php");

	/*
	// Resume session
	$resultat_session = $session_gepi->security_check();
	if ($resultat_session == 'c') {
		header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
		die();
	} else if ($resultat_session == '0') {
		header("Location: ../logout.php?auto=1");
		die();
	};
	*/

	/*
	// Check access
	// INSERT INTO droits VALUES ('/saisie/saisie_commentaires_types.php', 'V', 'V', 'V', 'V', 'F', 'V', 'Saisie de commentaires-types', '');
	// Le checkAccess ne fonctionne que si la session est valide.
	if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
	}
	*/

	//echo "<p>REQUEST_URI=".$_SERVER['REQUEST_URI']."</p>\n";
	//REQUEST_URI=/steph/gepi-cvs/saisie/saisie_avis2.php?periode_num=1&id_classe=1&fiche=y&current_eleve_login=TOTO_G&ind_eleve_login_suiv=3
	// Un accès direct à https://127.0.0.1/steph/gepi-cvs/saisie/saisie_commentaires_types.php se solde par un échec parce qu'aucun paramètre n'est initialisé pour l'accès à MySQL

	// L'appel à initialisations.inc.php est nécessaire pour récupérer le gepiPath
	//echo "<p>gepiPath=".$gepiPath."</p>\n";

	$tmp_chemin=$gepiPath."/saisie/saisie_avis2.php";
	if(substr($_SERVER['REQUEST_URI'],0,strlen($tmp_chemin))!=$tmp_chemin){
		/*
		echo "<html>\n";
		echo "<head>\n";
		echo "<title>Accès non autorisé!</title>\n";
		echo "</head>\n";
		echo "<body>\n";
		echo "<p><b>ERREUR:</b> Accès non autorisé!</p>\n";
		echo "</body>\n";
		echo "</html>\n";
		*/
		header("Location: ../logout.php?auto=1");
		die();
	}



	$sql="show tables";
	$res_tables=mysql_query($sql);
	$temoin_commentaires_types="";
	while($lig_table=mysql_fetch_array($res_tables)){
		if($lig_table[0]=='commentaires_types'){
			$temoin_commentaires_types="oui";
		}
	}

	if($temoin_commentaires_types=="oui"){
		$sql="select * from commentaires_types where id_classe='$id_classe' and num_periode='$periode_num' order by commentaire";
		//echo "<p>$sql</p>\n";
		$resultat_commentaire=mysql_query($sql);
		if(mysql_num_rows($resultat_commentaire)>0){
			echo "<br />\n";
			echo "Commentaires-types: <select name='ajout_commentaire_type' id='ajout_commentaire_type'>\n";
			while($ligne_commentaire=mysql_fetch_object($resultat_commentaire)) {
				// Pour conserver le code HTML saisi dans les commentaires-type...
				if((preg_match("/</",$ligne_commentaire->commentaire))&&(preg_match("/>/",$ligne_commentaire->commentaire))){
					/* Si le commentaire contient du code HTML, on ne remplace pas les retours à la ligne par des <br> pour éviter des doubles retours à la ligne pour un code comme celui-ci:
						<p>Blabla<br>
						Blibli</p>
					*/
					echo "<option>".htmlentities(stripslashes(trim($ligne_commentaire->commentaire)))."</option>\n";
				}
				else{
					//Si le commentaire ne contient pas de code HTML, on remplace les retours à la ligne par des <br>:
					echo "<option>".htmlentities(stripslashes(nl2br(trim($ligne_commentaire->commentaire))))."</option>\n";
				}
			}
			echo "</select>\n";
			echo "<input type=button name=ajout value='Ajouter' onClick=\"document.getElementById('no_anti_inject_current_eleve_login_ap').value=document.getElementById('no_anti_inject_current_eleve_login_ap').value+document.getElementById('ajout_commentaire_type').value\" />\n";
		}
	}
?>
