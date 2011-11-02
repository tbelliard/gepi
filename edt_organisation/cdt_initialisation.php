<?php
if ( getSettingValue("autorise_edt_tous") === 'y') {
	// CSS et js particulier à l'EdT
	$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
	$ua = getenv("HTTP_USER_AGENT");
	if (strstr($ua, "MSIE 6.0")) {
		$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_edt_ie6";
	}
	else {
		$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_edt";
	}

	//ob_start( 'ob_gzhandler' );

	$visioedt=isset($_GET['visioedt']) ? $_GET['visioedt'] : (isset($_POST['visioedt']) ? $_POST['visioedt'] : NULL);

	// Pour revenir proprement, on crée le $_SESSION["retour"]
	//$_SESSION["retour"] = "cdt_index";

	// Définir dés le début le type d'EdT qu'on veut voir (prof, classe, salle)

	//===========================
	// AJOUT: boireaus
	$visioedt=isset($_GET['visioedt']) ? $_GET['visioedt'] : (isset($_POST['visioedt']) ? $_POST['visioedt'] : NULL);
	$login_edt=isset($_GET['login_edt']) ? $_GET['login_edt'] : (isset($_POST['login_edt']) ? $_POST['login_edt'] : NULL);
	$classe=isset($_GET['classe']) ? $_GET['classe'] : (isset($_POST['classe']) ? $_POST['classe'] : NULL);
	$salle=isset($_GET['salle']) ? $_GET['salle'] : (isset($_POST['salle']) ? $_POST['salle'] : NULL);
	$supprimer_cours = isset($_GET["supprimer_cours"]) ? $_GET["supprimer_cours"] : NULL;
	$identite = isset($_GET["identite"]) ? $_GET["identite"] : NULL;
	$message = isset($_SESSION["message"]) ? $_SESSION["message"] : "";
	$type_edt_2 = isset($_GET["type_edt_2"]) ? $_GET["type_edt_2"] : (isset($_POST["type_edt_2"]) ? $_POST["type_edt_2"] : NULL);
	$period_id=isset($_GET['period_id']) ? $_GET['period_id'] : (isset($_POST['period_id']) ? $_POST['period_id'] : NULL);
	$bascule_edt=isset($_GET['bascule_edt']) ? $_GET['bascule_edt'] : (isset($_POST['bascule_edt']) ? $_POST['bascule_edt'] : NULL);
	$week_min=isset($_GET['week_min']) ? $_GET['week_min'] : (isset($_POST['week_min']) ? $_POST['week_min'] : NULL);
	$week_selected=isset($_GET['week_selected']) ? $_GET['week_selected'] : (isset($_POST['week_selected']) ? $_POST['week_selected'] : NULL);
	//===========================

	$visioedt = 'prof1';
	// =============================================================================
	//
	//                                  TRAITEMENT DES DONNEES
	//		
	// =============================================================================

	$type_edt = $login_edt;

	if ($message != "") {
		$_SESSION["message"] = "";
	}
	// =================== Gérer la bascule entre emplois du temps périodes et emplois du temps semaines.

	if ($bascule_edt != NULL) {
		$_SESSION['bascule_edt'] = $bascule_edt;
	}
	if (!isset($_SESSION['bascule_edt'])) {
		$_SESSION['bascule_edt'] = 'semaine';
	}
	$DisplayPeriodBar = false;
	$DisplayWeekBar = true;
	if ($week_selected != NULL) {
		$_SESSION['week_selected'] = $week_selected;
	}
	if (!isset($_SESSION['week_selected'])) {
		$_SESSION['week_selected'] = date("W");
	}

	// =================== Forcer l'affichage d'un edt si l'utilisateur est un prof 
	if (!isset($login_edt)) {
		if (($_SESSION['statut'] == "professeur") AND ($visioedt == "prof1")) {
			$login_edt = $_SESSION['login'];
			$_GET["login_edt"] = $login_edt;
			$_GET["type_edt_2"] = "prof";
			$type_edt_2 = "prof";
			$visioedt = "prof1";
		}
	}

	if (PeriodesExistent()) {
		$_SESSION['period_id'] = ReturnIdPeriod(RecupereTimestampJour(1));
		if (!PeriodExistsInDB($_SESSION['period_id'])) {
			$_SESSION['period_id'] = ReturnFirstIdPeriod();    
		}
	}
	else {
		$_SESSION['period_id'] = 0;
	}

	// =================== Construire les emplois du temps

	if(isset($login_edt)){
		$type_edt = isset($_GET["type_edt_2"]) ? $_GET["type_edt_2"] : (isset($_POST["type_edt_2"]) ? $_POST["type_edt_2"] : NULL);
		$tab_data = ConstruireEDTProf($login_edt, $_SESSION['period_id']);
		$entetes = ConstruireEnteteEDT();
		$creneaux = ConstruireCreneauxEDT();
		$DisplayEDT = true;
		FixColumnPositions($tab_data, $entetes);		// en cours de dével
		RecupereNotices($tab_data, $entetes);
		
	}
	else {
		$DisplayEDT = false;
	}
	// =================== Tester la présence de IE6

	$ua = getenv("HTTP_USER_AGENT");
	if (strstr($ua, "MSIE 6.0")) {
		 $IE6 = true;
	}
	else {
		$IE6 = false;
	}
}


?>