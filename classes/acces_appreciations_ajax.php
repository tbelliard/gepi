<?php
	// Initialisations files
	require_once("../lib/initialisations.inc.php");

	// Ajouter des tests sur le statut du visiteur
	if(($_SESSION['statut']!='administrateur')&&($_SESSION['statut']!='scolarite')&&($_SESSION['statut']!="professeur")) {
		echo "<p style='color:red;'>Accès non autorisé.</p>";
		die();
	}

	if($_SESSION['statut']=="professeur") {
		if(getSettingValue('GepiAccesRestrAccesAppProfP')!="yes") {
			$msg="Accès interdit au paramétrage des accès aux appréciatons/avis pour les parents et élèves.";
			header("Location: ../accueil.php?msg=".rawurlencode($msg));
			die();
		}

		$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;
		$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_classes jec
					WHERE jep.professeur='".$_SESSION['login']."' AND
						jep.login=jec.login AND
						jec.id_classe='$id_classe';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0){
			$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
			$msg="Vous n'êtes pas ".$gepi_prof_suivi." de la classe choisie.<br />Vous ne devriez donc pas accéder à cette page.";
			header("Location: ../accueil.php?msg=".rawurlencode($msg));
			die();
		}
	}
	elseif($_SESSION['statut']=='scolarite') {
		$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;
		$sql="SELECT 1=1 FROM j_scol_classes jsc
						WHERE jsc.login='".$_SESSION['login']."' AND
							jsc.id_classe='$id_classe';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0){
			$msg="Vous n'êtes pas responsable de la classe choisie.<br />Vous ne devriez donc pas accéder à cette page.";
			header("Location: ../accueil.php?msg=".rawurlencode($msg));
			die();
		}
	}


	if($_GET['mode']=='manuel') {
		$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;     // entier
		$periode=isset($_GET['periode']) ? $_GET['periode'] : NULL;           // entier
		//$accessible=isset($_GET['accessible']) ? $_GET['accessible'] : NULL;  // y ou n
		$statut=isset($_GET['statut']) ? $_GET['statut'] : NULL;              // eleve ou responsable
		$id_div=isset($_GET['id_div']) ? $_GET['id_div'] : NULL;              // ...
		// A FAIRE: Vérifier que les valeurs passées sont conformes à ce qui est attendu

		if(($id_classe!=NULL)&&($periode!=NULL)) {
			$sql="SELECT acces FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$statut';";
			//echo $sql;
			$test_acces=mysql_query($sql);
			if (mysql_num_rows($test_acces)==0) {
				$sql="INSERT INTO matieres_appreciations_acces SET acces='n', id_classe='$id_classe', periode='$periode', statut='$statut';";
				//echo "$sql<br />";
				$insert=mysql_query($sql);

				echo "<div style='background-color:orangered;'>";
				echo "Manuel</div>\n";
			}
			else {
				$lig_acces=mysql_fetch_object($test_acces);

				//if($accessible=='y') {
				if($lig_acces->acces=='y') {
					$sql="UPDATE matieres_appreciations_acces SET acces='n' WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$statut';";
					//echo "$sql<br />";
					$update=mysql_query($sql);

					echo "<div style='background-color:orangered;'>";
					/*
					// Modifier le lien pour soumettre effectivement si javascript est désactivé
					//echo "<a href='#' onclick=\"g_manuel('$id_div', $id_classe, $periode,'y','eleve');return false;\">Manuel</a><br />";
					echo "<a href='#' onclick=\"g_manuel('$id_div', $id_classe, $periode,'y','$statut');return false;\"><img src='../images/icons/configure.png' width='16' height='16' alt=\"Manuel\" /></a>";
					echo " | ";
					echo "<a href='#' onclick=\"$('choix_date_id_div').value=$id_div;$('choix_date_id_classe').value=$id_classe;$('choix_date_statut').value=$statut;$('choix_date_periode').value=$periode;afficher_div('infobulle_choix_date','y',-100,20);return false;\"><img src='../images/icons/date.png' width='16' height='16' alt=\"Choix d'une date de déverrouillage\" /></a>\n";
					echo " | ";
					echo "<a href='#' onclick=\"g_periode_close('$id_div', $id_classe, $periode,'$statut');return false;\"><img src='../images/icons/securite.png' width='16' height='16' alt=\"Période close\" /></a>\n";
					echo "<br />\n";
					*/
					echo "Manuel</div>\n";
				}
				else {
					$sql="UPDATE matieres_appreciations_acces SET acces='y' WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$statut';";
					$update=mysql_query($sql);
					//echo "$sql<br />";
					echo "<div style='background-color:lightgreen;'>";
					/*
					// Modifier le lien pour soumettre effectivement si javascript est désactivé
					//echo "<a href='#' onclick=\"g_manuel('$id_div', $id_classe, $periode,'y','eleve');return false;\">Manuel</a><br />";
					echo "<a href='#' onclick=\"g_manuel('$id_div', $id_classe, $periode,'n','$statut');return false;\"><img src='../images/icons/configure.png' width='16' height='16' alt=\"Manuel\" /></a>";
					echo " | ";
					echo "<a href='#' onclick=\"$('choix_date_id_div').value=$id_div;$('choix_date_id_classe').value=$id_classe;$('choix_date_statut').value=$statut;$('choix_date_periode').value=$periode;afficher_div('infobulle_choix_date','y',-100,20);return false;\"><img src='../images/icons/date.png' width='16' height='16' alt=\"Choix d'une date de déverrouillage\" /></a>\n";
					echo " | ";
					echo "<a href='#' onclick=\"g_periode_close('$id_div', $id_classe, $periode,'$statut');return false;\"><img src='../images/icons/securite.png' width='16' height='16' alt=\"Période close\" /></a>\n";
					echo "<br />\n";
					*/
					echo "Manuel</div>\n";
				}
			}
		}
	}
	elseif($_GET['mode']=='date') {
		$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;     // entier
		$periode=isset($_GET['periode']) ? $_GET['periode'] : NULL;           // entier
		$choix_date=isset($_GET['choix_date']) ? $_GET['choix_date'] : NULL;  // Contrôler que la date est valide
		$statut=isset($_GET['statut']) ? $_GET['statut'] : NULL;              // eleve ou responsable
		$id_div=isset($_GET['id_div']) ? $_GET['id_div'] : NULL;              // ...

		$tabdate=explode("/",$choix_date);
		$jour=$tabdate[0];
		$mois=$tabdate[1];
		$annee=$tabdate[2];
		$choix_date=$annee."-".$mois."-".$jour;
		$display_date=$jour."/".$mois."/".$annee;

		if(($id_classe!=NULL)&&($periode!=NULL)) {
			$sql="UPDATE matieres_appreciations_acces SET acces='date', date='".$choix_date."' WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$statut';";
			//echo "$sql<br />";
			$update=mysql_query($sql);

			$timestamp_limite=mktime(0,0,0,$mois,$jour,$annee);
			$timestamp_courant=time();
			if($timestamp_courant>=$timestamp_limite) {
				echo "<div style='background-color:lightgreen;'>";
			}
			else {
				echo "<div style='background-color:orangered;'>";
			}

			/*
			// Modifier le lien pour soumettre effectivement si javascript est désactivé
			echo "<a href='#' onclick=\"g_manuel('$id_div', $id_classe, $periode,'n','$statut');return false;\"><img src='../images/icons/configure.png' width='16' height='16' alt=\"Manuel\" /></a>";
			echo " | ";
			echo "<a href='#' onclick=\"$('choix_date_id_div').value=$id_div;$('choix_date_id_classe').value=$id_classe;$('choix_date_statut').value=$statut;$('choix_date_periode').value=$periode;afficher_div('infobulle_choix_date','y',-100,20);return false;\"><img src='../images/icons/date.png' width='16' height='16' alt=\"Choix d'une date de déverrouillage\" /></a>\n";
			echo " | ";
			echo "<a href='#' onclick=\"g_periode_close('$id_div', $id_classe, $periode,'$statut');return false;\"><img src='../images/icons/securite.png' width='16' height='16' alt=\"Période close\" /></a>\n";
			echo "<br />\n";
			*/
			echo "Date: $display_date</div>\n";
		}
	}
	elseif($_GET['mode']=='d') {
		$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;     // entier
		$periode=isset($_GET['periode']) ? $_GET['periode'] : NULL;           // entier
		$statut=isset($_GET['statut']) ? $_GET['statut'] : NULL;              // eleve ou responsable
		$id_div=isset($_GET['id_div']) ? $_GET['id_div'] : NULL;              // ...

		if(($id_classe!=NULL)&&($periode!=NULL)) {
			$sql="UPDATE matieres_appreciations_acces SET acces='d' WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$statut';";
			//echo "$sql<br />";
			$update=mysql_query($sql);

			$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' AND num_periode='$periode';";
			$res_per=mysql_query($sql);
			$lig_per=mysql_fetch_object($res_per);
			if($lig_per->verouiller!='O') {
				$accessible="n";

				if($lig_per->verouiller='P') {$etat_periode="Période partiellement close";} else {$etat_periode="Période ouverte";}
			}
			else {
				$delais_apres_cloture=getSettingValue('delais_apres_cloture');

				$tmp_tabdate=explode(" ",$lig_per->date_verrouillage);
				$tabdate=explode("-",$tmp_tabdate[0]);
				$jour=$tabdate[2];
				$mois=$tabdate[1];
				$annee=$tabdate[0];

				$timestamp_limite=mktime(0,0,0,$mois,$jour,$annee)+$delais_apres_cloture*24*3600;
				$timestamp_courant=time();
				if($timestamp_courant>=$timestamp_limite) {
					$accessible="y";
					if($annee=='0000') {
						$etat_periode="Accessible depuis la clôture de la période";
					}
					else {
						$etat_periode="Accessible depuis<br />le $jour/$mois/$annee";
					}
				}
				else {
					$accessible="n";
					$tmp_date=getdate($timestamp_limite);
					$jour=sprintf("%02d",$tmp_date['mday']);
					$mois=sprintf("%02d",$tmp_date['mon']);
					$annee=$tmp_date['year'];
					$etat_periode="Acces possible<br />le $jour/$mois/$annee";
				}

				//$etat_periode="Période close: $jour/$mois/$annee";
			}

			if($accessible=="y") {
				echo "<div style='background-color:lightgreen;'>";
			}
			else {
				echo "<div style='background-color:orangered;'>";
			}

			/*
			// Modifier le lien pour soumettre effectivement si javascript est désactivé
			echo "<a href='#' onclick=\"g_manuel('$id_div', $id_classe, $periode,'n','$statut');return false;\"><img src='../images/icons/configure.png' width='16' height='16' alt=\"Manuel\" /></a>";
			echo " | ";
			echo "<a href='#' onclick=\"$('choix_date_id_div').value=$id_div;$('choix_date_id_classe').value=$id_classe;$('choix_date_statut').value=$statut;$('choix_date_periode').value=$periode;afficher_div('infobulle_choix_date','y',-100,20);return false;\"><img src='../images/icons/date.png' width='16' height='16' alt=\"Choix d'une date de déverrouillage\" /></a>\n";
			echo " | ";
			echo "<a href='#' onclick=\"g_periode_close('$id_div', $id_classe, $periode,'$statut');return false;\"><img src='../images/icons/securite.png' width='16' height='16' alt=\"Période close\" /></a>\n";
			echo "<br />\n";
			*/
			echo "$etat_periode</div>\n";
		}
	}
?>