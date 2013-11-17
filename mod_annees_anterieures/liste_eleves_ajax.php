<?php
	header('Content-type: text/html; charset=utf-8');

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
	};

	//INSERT INTO `droits` VALUES ('/mod_annees_anterieures/liste_eleves_ajax.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Recherche d élèves', '');
	if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
	}

	// Contrôler que l'on accède pas à cette page de n'importe où?


	include("../secure/connect.inc.php");
	$mysql_db = @($GLOBALS["___mysqli_ston"] = mysqli_connect("localhost",  $dbUser,  $dbPass));
	@((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE $dbDb"));

	// CONTROLER CE QUI EST POSTé
	if((mb_strlen(my_ereg_replace("[A-Za-zÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõ¨ûüùúýÿ¸ -]","",$_POST['nom_ele']))!=0)||(mb_strlen(my_ereg_replace("[A-Za-zÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõ¨ûüùúýÿ¸ -]","",$_POST['prenom_ele']))!=0)){
		$chaine="Les caractères proposés dans la recherche doivent être des caractères alphabétiques<br />(<i>ou éventuellement le tiret '-' et l'espace ' '</i>).";
	}
	else{
		$sql="SELECT no_gep,nom,prenom,naissance FROM eleves WHERE nom LIKE '%".$_POST['nom_ele']."%' AND prenom LIKE '%".$_POST['prenom_ele']."%' ";

		$res=@mysqli_query($GLOBALS["___mysqli_ston"], $sql);

		if(mysqli_num_rows($res)==0){
			$chaine="Aucun résultat retourné.";
		}
		else{
			$chaine="<table class='table_annee_anterieure'>";
			$chaine.="<tr style='background-color: white;'>";

			$chaine.="<th>";
			$chaine.="Nom";
			$chaine.="</th>";

			$chaine.="<th>";
			$chaine.="Prenom";
			$chaine.="</th>";

			$chaine.="<th>";
			$chaine.="Naissance";
			$chaine.="</th>";

			$chaine.="<th>";
			$chaine.="INE";
			$chaine.="</th>";

			$chaine.="</tr>";

			$alt=-1;
			while($lig=mysqli_fetch_object($res)){
				//$chaine.="<tr>";

				$alt=$alt*(-1);
				$chaine.="<tr style='background-color:";
				if($alt==1){
					$chaine.="silver";
				}
				else{
					$chaine.="white";
				}
				$chaine.="; text-align: center;'>";

				$chaine.="<td>";
				$chaine.="$lig->nom";
				$chaine.="</td>";

				$chaine.="<td>";
				$chaine.="$lig->prenom";
				$chaine.="</td>";

				$chaine.="<td>";
				$chaine.=formate_date($lig->naissance);
				$chaine.="</td>";

				$chaine.="<td>";
				//$chaine.="$lig->no_gep";
				//$chaine.="<a href='#' onClick=\"document.getElementById(document.getElementById('ine_recherche').value).value='$lig->no_gep';return false;\">$lig->no_gep</a>";
				if($lig->no_gep!=""){
					$chaine.='<a href=\'#\' onClick=\"document.getElementById(document.getElementById(\'ine_recherche\').value).value=\''.$lig->no_gep.'\';cacher_div(\'div_search\');return false;\">'.$lig->no_gep.'</a>';
				}
				else{
					$chaine.="<span style='color:red'>Non renseigné</span>";
				}
				$chaine.="</td>";

				$chaine.="</tr>";
			}
			$chaine.="</table>";
			//$chaine.="$sql";

			// ATTENTION: IL NE FAUT PAS DE RETOUR A LA LIGNE DANS LA CHAINE RENVOYéE (ne pas mettre de \n donc)
			//            Et c'est vite coton de jouer avec les guillemets et apostrophes dans ce que l'on écrit.
		}
	}
	echo "document.getElementById('div_resultat').innerHTML=\"$chaine\";";

	@((is_null($___mysqli_res = mysqli_close($mysql_db))) ? false : $___mysqli_res);
?>
