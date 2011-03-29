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

	$tmp_chemin1=$gepiPath."/saisie/saisie_avis1.php";
	$tmp_chemin2=$gepiPath."/saisie/saisie_avis2.php";
	if((substr($_SERVER['REQUEST_URI'],0,strlen($tmp_chemin1))!=$tmp_chemin1)&&(substr($_SERVER['REQUEST_URI'],0,strlen($tmp_chemin2))!=$tmp_chemin2)){
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

	//echo "BBBBBBBBBBBBBB";

	if($temoin_commentaires_types=="oui"){
		//echo "<p>Ajouter un <a href='#' onClick=\"afficher_div('commentaire_type','y',30,-150);return false;\">Commentaire-type</a></p>\n";

		//echo "<input type='hidden' name='textarea_courant' id='textarea_courant' value='no_anti_inject_current_eleve_login_ap' />\n";

		$sql="select * from commentaires_types where id_classe='$id_classe' and num_periode='$periode_num' order by commentaire";
		//echo "<p>$sql</p>\n";
		$resultat_commentaire=mysql_query($sql);
		if(mysql_num_rows($resultat_commentaire)>0){

			//echo "<p>Ajouter un <a href='#' onClick=\"afficher_div('commentaire_type','y',30,-150);return false;\">Commentaire-type</a></p>\n";
			echo "<p>Ajouter un <a href='#' onClick=\"afficher_div('commentaire_type','y',30,20);return false;\">Commentaire-type</a></p>\n";

			echo "<input type='hidden' name='textarea_courant' id='textarea_courant' value='no_anti_inject_current_eleve_login_ap' />\n";

			//echo "<br />\n";

			//echo "<div id='commentaire_type' style=' background-color: lightgreen; border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; height: 100px; overflow: auto;'>\n";
			//echo "<div id='commentaire_type' style='background-color: lightgreen; border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; height: 10em 5px; width: 400px;'>\n";
			echo "<div id='commentaire_type' class='infobulle_corps' style='border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; height: 10em 5px; width: 400px;'>\n";
			//echo "<div id='commentaire_type' style=\"background-image: url('../images/background/opacite80.png'); border: 1px solid #000000; color: #000000; padding: 0px; position: absolute; height: 10em 5px; width: 400px;\">\n";

			//echo "<div style='background-color: orange; color: #ffffff; cursor: move; font-weight: bold; padding: 0px;'  onmousedown=\"dragStart(event, 'commentaire_type')\">\n";
			echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; font-weight: bold; padding: 0px;'  onmousedown=\"dragStart(event, 'commentaire_type')\">\n";
			//echo "<div style=\"background-image: url('../images/background/opacite90.png'); color: #000000; cursor: move; font-weight: bold; padding: 0px;\"  onmousedown=\"dragStart(event, 'commentaire_type')\">\n";

			echo "<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 1em;'><a href='#' onClick=\"document.getElementById('commentaire_type').style.display='none';return false;\">X</a></div>\n";

			echo "Commentaires-types";
			echo "</div>\n";

			echo "<div style='height: 9em; overflow: auto;'>\n";
			$cpt=0;
			//echo "Commentaires-types: <select name='ajout_commentaire_type' id='ajout_commentaire_type'>\n";
			while($ligne_commentaire=mysql_fetch_object($resultat_commentaire)) {
				echo "<div style='border: 1px solid black; margin: 1px; padding: 1px;'";

				if(preg_match("/firefox/i",$_SERVER['HTTP_USER_AGENT'])){
					echo " onClick=\"textarea_courant=document.getElementById('textarea_courant').value;document.getElementById(textarea_courant).value=document.getElementById(textarea_courant).value+document.getElementById('commentaire_type_'+$cpt).value;changement();document.getElementById('commentaire_type').style.display='none'; document.getElementById(textarea_courant).focus();\"";
				}
				echo ">\n";

				echo "<input type='hidden' name='commentaire_type_$cpt' id='commentaire_type_$cpt' value=\" ".htmlentities(stripslashes(trim($ligne_commentaire->commentaire)))."\" />\n";

				if(!preg_match("/firefox/i",$_SERVER['HTTP_USER_AGENT'])){
					// Avec konqueror, pour document.getElementById('textarea_courant').value, on obtient [Object INPUT]
					// En sortant, la commande du onClick et en la mettant dans une fonction javascript externe, ca passe.
					echo "<a href='#' onClick=\"complete_textarea_courant($cpt); return false;\" style='text-decoration:none; color:black;'>";
				}

				// Pour conserver le code HTML saisi dans les commentaires-type...
				if((preg_match("/</",$ligne_commentaire->commentaire))&&(preg_match("/>/",$ligne_commentaire->commentaire))){
					/* Si le commentaire contient du code HTML, on ne remplace pas les retours à la ligne par des <br> pour éviter des doubles retours à la ligne pour un code comme celui-ci:
						<p>Blabla<br>
						Blibli</p>
					*/
					echo htmlentities(stripslashes(trim($ligne_commentaire->commentaire)));
				}
				else{
					//Si le commentaire ne contient pas de code HTML, on remplace les retours à la ligne par des <br>:
					echo htmlentities(stripslashes(nl2br(trim($ligne_commentaire->commentaire))));
				}

				if(!preg_match("/firefox/i",$_SERVER['HTTP_USER_AGENT'])){
					echo "</a>";
				}

				echo "</div>\n";
				$cpt++;
			}
			echo "</div>\n";
			echo "</div>\n";
			//echo "</select>\n";
			//echo "<input type=button name=ajout value='Ajouter' onClick=\"document.getElementById('no_anti_inject_current_eleve_login_ap').value=document.getElementById('no_anti_inject_current_eleve_login_ap').value+document.getElementById('ajout_commentaire_type').value\" />\n";

			echo "<script type='text/javascript'>
	document.getElementById('commentaire_type').style.display='none';
</script>\n";


echo "<script type='text/javascript'>
// Pour konqueror...
function complete_textarea_courant(num){
	// Récupération de l'identifiant du TEXTAREA à remplir
	id_textarea_courant=document.getElementById('textarea_courant').value;
	//alert('id_textarea_courant='+id_textarea_courant);

	// Contenu initial du TEXTAREA
	contenu_courant_textarea_courant=eval(\"document.getElementById('\"+id_textarea_courant+\"').value\");
	//alert('contenu_courant_textarea_courant='+contenu_courant_textarea_courant);

	// Commentaire à ajouter
	commentaire_a_ajouter=eval(\"document.getElementById('commentaire_type_\"+num+\"').value\");
	//alert('commentaire_a_ajouter='+commentaire_a_ajouter);

	// Ajout
	textarea_courant=eval(\"document.getElementById('\"+id_textarea_courant+\"')\")
	textarea_courant.value=contenu_courant_textarea_courant+commentaire_a_ajouter;

	// On cache la liste des commentaires-types
	document.getElementById('commentaire_type').style.display='none';

	// On redonne le focus au TEXTAREA
	document.getElementById(id_textarea_courant).focus();

	changement();
}
</script>\n";

			echo "<script type='text/javascript' src='../lib/brainjar_drag.js'></script>\n";
			echo "<script type='text/javascript' src='../lib/position.js'></script>\n";
		}


	}
?>
