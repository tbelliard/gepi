<?php
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

	$sql="SELECT 1=1 FROM droits WHERE id='/classes/acces_appreciations_ajax.php';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/classes/acces_appreciations_ajax.php',
	administrateur='V',
	professeur='V',
	cpe='V',
	scolarite='V',
	eleve='F',
	responsable='F',
	secours='F',
	autre='F',
	description='Ajax: Acces aux appreciations et avis des bulletins',
	statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	}

	if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
	}


	// Ajouter des tests sur le statut du visiteur
	if(($_SESSION['statut']!='administrateur')&&($_SESSION['statut']!='scolarite')&&($_SESSION['statut']!="professeur")) {
		echo "<p style='color:red;'>Accès non autorisé.</p>";
		die();
	}

	if($_SESSION['statut']=="professeur") {
		if(getSettingValue('GepiAccesRestrAccesAppProfP')!="yes") {
			$msg="Accès interdit au paramétrage des accès aux appréciations/avis pour les parents et élèves.";
			header("Location: ../accueil.php?msg=".rawurlencode($msg));
			die();
		}

		$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;
		// A REVOIR : Si !isset($id_classe)
		$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_classes jec
					WHERE jep.professeur='".$_SESSION['login']."' AND
						jep.login=jec.login AND
						jec.id_classe='$id_classe';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0){
			$gepi_prof_suivi=retourne_denomination_pp($id_classe);
			$msg="Vous n'êtes pas ".$gepi_prof_suivi." de la classe choisie.<br />Vous ne devriez donc pas accéder à cette page.";
			header("Location: ../accueil.php?msg=".rawurlencode($msg));
			die();
		}
	}
	elseif($_SESSION['statut']=='scolarite') {
		$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;
		// A REVOIR : Si !isset($id_classe)
		$sql="SELECT 1=1 FROM j_scol_classes jsc
						WHERE jsc.login='".$_SESSION['login']."' AND
							jsc.id_classe='$id_classe';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0){
			$msg="Vous n'êtes pas responsable de la classe choisie.<br />Vous ne devriez donc pas accéder à cette page.";
			header("Location: ../accueil.php?msg=".rawurlencode($msg));
			die();
		}
	}

	//debug_var();

	check_token();

	$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;     // entier
	if(mb_strlen(preg_replace('/[0-9]/','',$id_classe))!=0) {$id_classe=NULL;}
	if($id_classe=='') {$id_classe=NULL;}

	$periode=isset($_GET['periode']) ? $_GET['periode'] : NULL;           // entier
	if(mb_strlen(preg_replace('/[0-9]/','',$periode))!=0) {$periode=NULL;}
	if($periode=='') {$periode=NULL;}

	$statut=isset($_GET['statut']) ? $_GET['statut'] : NULL;              // eleve, responsable ou ele_resp
	$tab_statuts=array('eleve','responsable','ele_resp');
	if(!in_array($statut,$tab_statuts)) {$statut='eleve';}

	if($statut=='ele_resp') {$tab_liste_statuts=array('eleve','responsable');}
	else {$tab_liste_statuts=array("$statut");}

	$id_div=isset($_GET['id_div']) ? $_GET['id_div'] : NULL;              // ...
	if(mb_strlen(preg_replace('/[0-9A-Za-z_]/','',$id_div))!=0) {$id_div=NULL;$id_classe=NULL;}

	if($_GET['mode']=='manuel') {

		if(($id_classe!=NULL)&&($periode!=NULL)) {
			for($i=0;$i<count($tab_liste_statuts);$i++) {
				$sql="SELECT acces FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$tab_liste_statuts[$i]';";
				//echo $sql;
				$test_acces=mysqli_query($GLOBALS["mysqli"], $sql);
				if (mysqli_num_rows($test_acces)==0) {
					$sql="INSERT INTO matieres_appreciations_acces SET acces='n', id_classe='$id_classe', periode='$periode', statut='$tab_liste_statuts[$i]';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	
					echo "<div style='background-color:orangered;'>";
					if($statut=='ele_resp') {
						echo "Inaccessible</div>\n";
					}
					else {
						echo "Manuel</div>\n";
					}
				}
				else {
					$lig_acces=mysqli_fetch_object($test_acces);
	
					if($lig_acces->acces=='y') {
						$sql="UPDATE matieres_appreciations_acces SET acces='n' WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$tab_liste_statuts[$i]';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);

						// On n'écrit le DIV de couleur qu'une fois
						if($i==0) {
							echo "<div style='background-color:orangered;'>";
							if($statut=='ele_resp') {
								echo "Inaccessible</div>\n";
							}
							else {
								echo "Manuel</div>\n";
							}
						}
					}
					else {
						$sql="UPDATE matieres_appreciations_acces SET acces='y' WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$tab_liste_statuts[$i]';";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						//echo "$sql<br />";

						// On n'écrit le DIV de couleur qu'une fois
						if($i==0) {
							echo "<div style='background-color:lightgreen;'>";
							if($statut=='ele_resp') {
								echo "Accessible</div>\n";
							}
							else {
								echo "Manuel</div>\n";
							}
						}
					}
				}
			}
		}
	}
	elseif($_GET['mode']=='manuel_individuel') {
		//Changer pour toute la classe ou pour une sélection d'élèves
		if(($id_classe!=NULL)&&($periode!=NULL)) {
			$tab_ele=array();
			$sql="SELECT DISTINCT login FROM j_eleves_classes jec WHERE jec.id_classe='".$id_classe."' AND
											jec.periode='$periode';";
			//echo "$sql<br />\n";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig=mysqli_fetch_object($res)) {
					$tab_ele[]=$lig->login;
				}
			}

			$sql="SELECT * FROM matieres_appreciations_acces_eleve maae, j_eleves_classes jec WHERE maae.login=jec.login AND 
											jec.id_classe='".$id_classe."' AND 
											jec.periode=maae.periode AND 
											maae.periode='$periode' AND 
											acces='y';";
			//echo "$sql<br />\n";
			$test_acces=mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($test_acces)==0) {
				// On passe toute la classe en acces='y'
				$acces="y";
			}
			else {
				// On passe toute la classe en acces='n'
				$acces="n";
			}

			$sql="DELETE FROM matieres_appreciations_acces_eleve WHERE login IN (SELECT login FROM j_eleves_classes WHERE id_classe='".$id_classe."' AND periode='$periode') AND periode='$periode';";
			//echo "$sql<br />\n";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);

			$nb_err=0;
			for($loop=0;$loop<count($tab_ele);$loop++) {
				$sql="INSERT INTO matieres_appreciations_acces_eleve SET login='".$tab_ele[$loop]."', periode='".$periode."', acces='".$acces."';";
				//echo "$sql<br />\n";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					$nb_err++;
				}
			}
			if($nb_err>0) {
				echo "<div style='background-color:red;'>Il s'est produit $nb_err erreur(s)</div>\n";
			}
			elseif($acces=="y") {
				echo "<div style='background-color:lightgreen;'>Accessible</div>\n";
			}
			else {
				echo "<div style='background-color:orangered;'>Inaccessible</div>\n";
			}
		}
	}
	elseif($_GET['mode']=='date') {
		$choix_date=isset($_GET['choix_date']) ? $_GET['choix_date'] : NULL;  // Contrôler que la date est valide

		$poursuivre="y";
		if($choix_date=='') {
			$poursuivre="n";
			//echo "<script type='text/javascript'>alert('Veuillez saisir une date valide.');</script>\n";
			echo "<span style='color:red'>Date saisie invalide</span>";
		}
		elseif(!my_ereg("[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}",$choix_date)) {
			$poursuivre="n";
			echo "<span style='color:red'>Date saisie invalide</span>";
		}
		else {
			$tabdate=explode("/",$choix_date);
			$jour=$tabdate[0];
			$mois=$tabdate[1];
			$annee=$tabdate[2];

			if(!checkdate($mois,$jour,$annee)) {
				$poursuivre="n";
				echo "<span style='color:red'>Date saisie invalide</span>";
			}
		}

		if($poursuivre=="y") {
			$choix_date=$annee."-".$mois."-".$jour;
			$display_date=$jour."/".$mois."/".$annee;
	
			if(($id_classe!=NULL)&&($periode!=NULL)) {
				for($i=0;$i<count($tab_liste_statuts);$i++) {
					$sql="SELECT acces FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$tab_liste_statuts[$i]';";
					//echo $sql;
					$test_acces=mysqli_query($GLOBALS["mysqli"], $sql);
					if (mysqli_num_rows($test_acces)==0) {
						$sql="INSERT INTO matieres_appreciations_acces SET acces='date', date='".$choix_date."', id_classe='$id_classe', periode='$periode', statut='$tab_liste_statuts[$i]';";
						//echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					}
					else {
						$sql="UPDATE matieres_appreciations_acces SET acces='date', date='".$choix_date."' WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$tab_liste_statuts[$i]';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
					}
	
					// On n'écrit le DIV de couleur qu'une fois
					if($i==0) {
						$timestamp_limite=mktime(0,0,0,$mois,$jour,$annee);
						$timestamp_courant=time();
						if($timestamp_courant>=$timestamp_limite) {
							echo "<div style='background-color:lightgreen;'>";
							if($statut=='ele_resp') {
								echo "Accessible&nbsp;: \n";
							}
							else {
								echo "Date&nbsp;: \n";
							}
						}
						else {
							echo "<div style='background-color:orangered;'>";
							if($statut=='ele_resp') {
								echo "Inaccessible&nbsp;: \n";
							}
							else {
								echo "Date&nbsp;: \n";
							}
						}
	
						echo "$display_date</div>\n";
					}
				}
			}
		}
	}
	elseif($_GET['mode']=='d') {

		if(($id_classe!=NULL)&&($periode!=NULL)) {
			for($i=0;$i<count($tab_liste_statuts);$i++) {
				$sql="UPDATE matieres_appreciations_acces SET acces='d' WHERE id_classe='$id_classe' AND periode='$periode' AND statut='$tab_liste_statuts[$i]';";
				//echo "$sql<br />";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
	
				$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' AND num_periode='$periode';";
				$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
				$lig_per=mysqli_fetch_object($res_per);
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
							//if($delais_apres_cloture>0) {echo " + ".$delais_apres_cloture."j";}
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

				// On n'écrit le DIV de couleur qu'une fois
				if($i==0) {
	
					if($accessible=="y") {
						echo "<div style='background-color:lightgreen;'>";
					}
					else {
						echo "<div style='background-color:orangered;'>";
					}
					echo "$etat_periode</div>\n";
				}
			}
		}
	}
?>
