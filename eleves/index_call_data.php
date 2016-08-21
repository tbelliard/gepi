<?php

	if(((isset($_POST['mode_rech_prenom']))&&($_POST['mode_rech_prenom']=='contient'))||
			((isset($_GET['mode_rech_prenom']))&&($_GET['mode_rech_prenom']=='contient'))) {
		$mode_rech_prenom="contient";
	}

	if(((isset($_POST['mode_rech_nom']))&&($_POST['mode_rech_nom']=='contient'))||
			((isset($_GET['mode_rech_nom']))&&($_GET['mode_rech_nom']=='contient'))) {
		$mode_rech_nom="contient";
	}

	if(((isset($_POST['mode_rech_elenoet']))&&($_POST['mode_rech_elenoet']=='contient'))||
			((isset($_GET['mode_rech_elenoet']))&&($_GET['mode_rech_elenoet']=='contient'))) {
		$mode_rech_elenoet="contient";
	}

	if(((isset($_POST['mode_rech_ele_id']))&&($_POST['mode_rech_ele_id']=='contient'))||
			((isset($_GET['mode_rech_ele_id']))&&($_GET['mode_rech_ele_id']=='contient'))) {
		$mode_rech_ele_id="contient";
	}

	if(((isset($_POST['mode_rech_no_gep']))&&($_POST['mode_rech_no_gep']=='contient'))||
			((isset($_GET['mode_rech_no_gep']))&&($_GET['mode_rech_no_gep']=='contient'))) {
		$mode_rech_no_gep="contient";
	}

	$motif_rech_mef=isset($_POST['motif_rech_mef']) ? $_POST['motif_rech_mef'] : (isset($_GET['motif_rech_mef']) ? $_GET['motif_rech_mef'] : NULL);
	$motif_rech_etab=isset($_POST['motif_rech_etab']) ? $_POST['motif_rech_etab'] : (isset($_GET['motif_rech_etab']) ? $_GET['motif_rech_etab'] : NULL);

	//echo "quelles_classes=$quelles_classes<br />";
	//echo "motif_rech_etab=$motif_rech_etab<br />";

	if($_SESSION['statut'] == 'professeur') {
		/*
		$calldata = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_professeurs jep
		WHERE (
		jep.login=e.login AND
		jep.professeur='".$_SESSION['login']."' AND
		jep.id_classe='$quelles_classes'
		)
		ORDER BY $order_type");
		*/
		if((getSettingAOui('GepiAccesPPTousElevesDeLaClasse'))&&(is_pp($_SESSION['login'], $quelles_classes))) {
			$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, j_eleves_regime jer, j_eleves_professeurs jep
			WHERE (
			jep.login=e.login AND
			jer.login=e.login AND
			jep.id_classe='$quelles_classes'
			)
			ORDER BY $order_type;";
		}
		else {
			$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, j_eleves_professeurs jep, j_eleves_regime jer
			WHERE (
			jep.login=e.login AND
			jer.login=e.login AND
			jep.professeur='".$_SESSION['login']."' AND
			jep.id_classe='$quelles_classes'
			)
			ORDER BY $order_type;";
		}
		$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

		if((!isset($page_courante))||($page_courante!="modify_eleve")) {
			echo "<p align='center'>Liste des élèves de la classe choisie.</p>\n";
		}
		else {
			// Message alternatif depuis modify_eleve.php
		}
	}
	else{
		if ($quelles_classes == 'certaines') {
			/*
			$calldata = mysql_query("SELECT DISTINCT e.* FROM eleves e, tempo t, j_eleves_classes j, classes cl
			WHERE (t.num = '".SESSION_ID()."' AND
				t.id_classe = j.id_classe and
				j.login = e.login AND
				cl.id=t.id_classe and
				j.periode=t.max_periode
				)
			ORDER BY $order_type");
			*/
			$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, tempo t, j_eleves_classes j, classes cl, j_eleves_regime jer
			WHERE (t.num = '".SESSION_ID()."' AND
				t.id_classe = j.id_classe and
				j.login = e.login AND
				jer.login = e.login AND
				cl.id=t.id_classe and
				j.periode=t.max_periode
				)
			ORDER BY $order_type;";
			$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves de la ou des classes choisies.</p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}

		} else if ($quelles_classes == 'toutes') {
			if ($order_type == "classe,nom,prenom") {
				/*
				$calldata = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j, classes cl
				WHERE (
				j.login = e.login AND
				j.id_classe =cl.id
				)
				ORDER BY $order_type");
				*/
				$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, j_eleves_classes j, classes cl, j_eleves_regime jer
				WHERE (
				j.login = e.login AND
				jer.login = e.login AND
				j.id_classe =cl.id
				)
				ORDER BY $order_type;";
				$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			} else {
				//$calldata = mysql_query("SELECT * FROM eleves ORDER BY $order_type");
				$calldata = mysqli_query($GLOBALS["mysqli"], "SELECT e.*, jer.* FROM eleves e, j_eleves_regime jer WHERE jer.login=e.login ORDER BY $order_type");
			}

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste de tous les élèves.</p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}

		} else if ($quelles_classes == 'na') {
			/*
			$calldata = mysql_query("select e.* from eleves e
			LEFT JOIN j_eleves_classes c ON c.login=e.login
			where c.login is NULL
			ORDER BY $order_type
			");
			*/
			/*
			if(mb_substr($order_type,0,6)=='regime') {
				$tmp_order_type=my_ereg_replace('^regime,','',$order_type);
			}
			else {
				$tmp_order_type=$order_type;
			}

			$sql="select e.* FROM eleves e
			LEFT JOIN j_eleves_classes c ON c.login=e.login
			where c.login is NULL
			ORDER BY $tmp_order_type;";
			$calldata = mysql_query($sql);

			if(mysql_num_rows($calldata)!=0){
				$tab_eleve=array();
				$i=0;
				while($lig_tmp=mysql_fetch_object($calldata)) {
					$tab_eleve[$i]=array();
					$tab_eleve[$i]['login']=$lig_tmp->login;
					$tab_eleve[$i]['nom']=$lig_tmp->nom;
					$tab_eleve[$i]['prenom']=$lig_tmp->prenom;
					$tab_eleve[$i]['sexe']=$lig_tmp->sexe;
					$tab_eleve[$i]['naissance']=$lig_tmp->naissance;
					$tab_eleve[$i]['elenoet']=$lig_tmp->elenoet;

					$sql="SELECT * FROM j_eleves_regime WHERE login='$lig_tmp->login';";
					$res_regime=mysql_query($sql);
					if(mysql_num_rows($res_regime)==0) {
						$tab_eleve[$i]['regime']='-';
						$tab_eleve[$i]['doublant']='N';
					}
					else {
						$lig_reg=mysql_fetch_object($res_regime);
						$tab_eleve[$i]['regime']=$lig_reg->regime;
						$tab_eleve[$i]['doublant']=$lig_reg->doublant;
					}
					$i++;
				}
			}
			*/
			// TRI A FAIRE SI ON A CHOISI regime


			$sql="select e.*,jer.* FROM j_eleves_regime jer, eleves e
			LEFT JOIN j_eleves_classes c ON c.login=e.login
			WHERE c.login is NULL AND jer.login=e.login
			ORDER BY $order_type;";
			//echo "$sql<br />";
			$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves non affectés dans une classe.</p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}

		} else if ($quelles_classes == 'compte_user_manquant') {
			$sql="select e.*,jer.* FROM j_eleves_regime jer, eleves e
			LEFT JOIN utilisateurs u ON u.login=e.login
			WHERE u.login is NULL AND jer.login=e.login
			ORDER BY $order_type;";
			//echo "$sql<br />";
			$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves ne disposant pas de compte d'utilisateur.</p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}

		} else if ($quelles_classes == 'compte_inactif') {
			$sql="select e.*,jer.* FROM j_eleves_regime jer, eleves e, utilisateurs u
			WHERE u.login=e.login AND jer.login=e.login AND u.etat='inactif'
			ORDER BY $order_type;";
			//echo "$sql<br />";
			$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves dont le compte d'utilisateur est inactif.</p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}

		} else if ($quelles_classes == 'incomplet') {
			/*
			$calldata = mysql_query("SELECT e.* FROM eleves e WHERE elenoet='' OR no_gep=''
			ORDER BY $order_type
			");
			*/
			if(preg_match('/classe/',$order_type)){
				$sql="SELECT DISTINCT e.*, jer.* FROM eleves e, classes c, j_eleves_classes jec, j_eleves_regime jer
					WHERE (e.elenoet='' OR e.no_gep='') AND
							jer.login=e.login AND
							jec.login=e.login AND
							c.id=jec.id_classe
					ORDER BY $order_type;";
			}
			else{
				$sql="SELECT e.*, jer.* FROM eleves e, j_eleves_regime jer WHERE (elenoet='' OR no_gep='') AND
							jer.login=e.login
						ORDER BY $order_type;";
			}
			//echo "$sql<br />\n";
			$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves dont l'Elenoet ou le Numéro national (INE) n'est pas renseigné.</p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}


		} else if ($quelles_classes == 'email_vide') {
			/*
			$calldata = mysql_query("SELECT e.* FROM eleves e WHERE elenoet='' OR no_gep=''
			ORDER BY $order_type
			");
			*/
			if(preg_match('/classe/',$order_type)){
				$sql="SELECT DISTINCT e.*, jer.* FROM eleves e, classes c, j_eleves_classes jec, j_eleves_regime jer
					WHERE e.email='' AND
							jer.login=e.login AND
							jec.login=e.login AND
							c.id=jec.id_classe
					ORDER BY $order_type;";
			}
			else{
				$sql="SELECT e.*, jer.* FROM eleves e, j_eleves_regime jer WHERE e.email='' AND
							jer.login=e.login
						ORDER BY $order_type;";
			}
			//echo "$sql<br />\n";
			$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves dont l'email n'est pas renseigné.</p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}


		} else if ($quelles_classes == 'photo') {
			//$sql="SELECT elenoet FROM eleves WHERE elenoet!='';";
			if(isset($order_type)) {
				$sql="SELECT DISTINCT e.*, jer.* FROM eleves e, j_eleves_classes jec, classes c, j_eleves_regime jer WHERE e.elenoet!='' AND e.login=jec.login AND e.login=jer.login AND jec.id_classe=c.id ORDER BY $order_type;";
			}
			else {
				$sql="SELECT e.*, jer.* FROM eleves e, j_eleves_regime jer WHERE e.elenoet!='' AND e.login=jer.login;";
			}
			//echo "$sql<br />";
			$test_elenoet_ok=mysqli_query($GLOBALS["mysqli"], $sql);
			$tab_eleve=array();
			if(mysqli_num_rows($test_elenoet_ok)!=0){
				//$chaine_photo_manquante="";
				$i=0;
				while($lig_tmp=mysqli_fetch_object($test_elenoet_ok)) {
					$test_photo=nom_photo($lig_tmp->elenoet);
					if($test_photo==""){
						//if($chaine_photo_manquante!=""){$chaine_photo_manquante.=" OR ";}
						//$chaine_photo_manquante.="elenoet='$lig_tmp->elenoet'";
						$tab_eleve[$i]=array();
						$tab_eleve[$i]['login']=$lig_tmp->login;
						$tab_eleve[$i]['nom']=$lig_tmp->nom;
						$tab_eleve[$i]['prenom']=$lig_tmp->prenom;
						$tab_eleve[$i]['sexe']=$lig_tmp->sexe;
						$tab_eleve[$i]['naissance']=$lig_tmp->naissance;
						$tab_eleve[$i]['elenoet']=$lig_tmp->elenoet;
						$tab_eleve[$i]['regime']=$lig_tmp->regime;
						$tab_eleve[$i]['doublant']=$lig_tmp->doublant;
						$tab_eleve[$i]['date_sortie']=$lig_tmp->date_sortie;
						$tab_eleve[$i]['mef_code']=$lig_tmp->mef_code;
						$i++;
					}
				}
				/*
				$calldata = mysql_query("SELECT e.* FROM eleves e WHERE $chaine_photo_manquante
				ORDER BY $order_type
				");
				*/
			}

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves sans photo.</p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}

		} else if ($quelles_classes == 'no_cpe') {
			if(preg_match('/classe/',$order_type)){
				$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, j_eleves_classes jec, classes c, j_eleves_regime jer
						WHERE e.login=jec.login AND
							e.login=jer.login AND
							jec.id_classe=c.id AND
							e.login NOT IN (SELECT e_login FROM j_eleves_cpe) ORDER BY $order_type;";
				$calldata=mysqli_query($GLOBALS["mysqli"], $sql);
			}
			else{
				$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, j_eleves_classes jec, j_eleves_regime jer
						WHERE e.login=jec.login AND
							e.login=jer.login AND
							e.login NOT IN (SELECT e_login FROM j_eleves_cpe) ORDER BY $order_type;";
				$calldata=mysqli_query($GLOBALS["mysqli"], $sql);
			}

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves sans CPE.</p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}

		} else if ($quelles_classes == 'no_regime') {

			if(preg_match('/classe/',$order_type)){
				$sql="SELECT DISTINCT e.* FROM eleves e, classes c, j_eleves_classes jec
					LEFT JOIN j_eleves_regime jer ON jec.login=jer.login
					WHERE jer.login is null AND e.login=jec.login AND c.id=jec.id_classe ORDER BY $order_type;";
			}
			else{
				$sql="SELECT DISTINCT e.* FROM eleves e
					LEFT JOIN j_eleves_regime jer ON e.login=jer.login
					WHERE jer.login is null ORDER BY $order_type;";
			}
			//echo "$sql<br />";
			$calldata=mysqli_query($GLOBALS["mysqli"], $sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves dont le régime n'est pas renseigné.</p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}


		} else if ($quelles_classes == 'no_pp') {
			if(preg_match('/classe/',$order_type)){
				$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, j_eleves_classes jec, classes c, j_eleves_regime jer
						WHERE e.login=jec.login AND
							e.login=jer.login AND
							jec.id_classe=c.id AND
							e.login NOT IN (SELECT login FROM j_eleves_professeurs) ORDER BY $order_type;";
				$calldata=mysqli_query($GLOBALS["mysqli"], $sql);

			}
			else{
				$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, j_eleves_classes jec, j_eleves_regime jer
						WHERE e.login=jec.login AND
							e.login=jer.login AND
							e.login NOT IN (SELECT login FROM j_eleves_professeurs) ORDER BY $order_type;";
				$calldata=mysqli_query($GLOBALS["mysqli"], $sql);

			}

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves sans ".getSettingValue('gepi_prof_suivi')."</p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}

		} else if ($quelles_classes == 'no_resp') {
			if(preg_match('/classe/',$order_type)){

				$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, j_eleves_classes jec, classes c, j_eleves_regime jer
						WHERE e.login=jec.login AND
							e.login=jer.login AND
							jec.id_classe=c.id AND
							e.ele_id NOT IN (SELECT ele_id FROM responsables2) ORDER BY $order_type;";
				//echo "$sql<br />\n";
				$calldata=mysqli_query($GLOBALS["mysqli"], $sql);

				if((!isset($page_courante))||($page_courante!="modify_eleve")) {
					echo "<p align='center'>Liste des élèves dans une classe, mais sans responsable.</p>\n";
				}
				else {
					// Message alternatif depuis modify_eleve.php
				}

			}
			else{

				$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, j_eleves_classes jec, j_eleves_regime jer
						WHERE e.login=jec.login AND
							e.login=jer.login AND
							e.ele_id NOT IN (SELECT ele_id FROM responsables2) ORDER BY $order_type;";
				//echo "$sql<br />\n";
				$calldata=mysqli_query($GLOBALS["mysqli"], $sql);

				if((!isset($page_courante))||($page_courante!="modify_eleve")) {
					echo "<p align='center'>Liste des élèves sans responsable.</p>\n";
				}
				else {
					// Message alternatif depuis modify_eleve.php
				}

			}

		} else if ($quelles_classes == 'rech_prenom') {
			if(isset($motif_rech_p)) {
				$motif_rech=$motif_rech_p;
			}

			$pref_motif="";
			$texte_motif="commence par";
			if((isset($mode_rech_prenom))&&($mode_rech_prenom=='contient')) {
				$pref_motif="%";
				$texte_motif="contient";
				$mode_rech_prenom="contient";

				$mode_rech="contient";
			}
			/*
			$calldata = mysql_query("SELECT e.* FROM eleves e WHERE nom like '".$motif_rech."%'
			ORDER BY $order_type
			");
			*/
			if(preg_match('/classe/',$order_type)){
				$sql="SELECT DISTINCT e.*, jer.* FROM eleves e, classes c, j_eleves_classes jec, j_eleves_regime jer
					WHERE prenom like '".$pref_motif.$motif_rech."%' AND
							e.login=jer.login AND
							jec.login=e.login AND
							c.id=jec.id_classe
					ORDER BY $order_type";
			}
			else{
				$sql="SELECT e.*, jer.* FROM eleves e, j_eleves_regime jer WHERE prenom like '".$pref_motif.$motif_rech."%' AND
									e.login=jer.login
								ORDER BY $order_type";
			}
			//echo "$sql<br />\n";
			$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves dont le prenom $texte_motif <b>$motif_rech</b></p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}

			//$motif_rech=$pref_motif.$motif_rech;

		} else if ($quelles_classes == 'recherche') {
			$pref_motif="";
			$texte_motif="commence par";
			if((isset($mode_rech_nom))&&($mode_rech_nom=='contient')) {
				$pref_motif="%";
				$texte_motif="contient";
				$mode_rech_nom="contient";

				$mode_rech="contient";
			}

			/*
			$calldata = mysql_query("SELECT e.* FROM eleves e WHERE nom like '".$motif_rech."%'
			ORDER BY $order_type
			");
			*/
			if(preg_match('/classe/',$order_type)){
				$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, classes c, j_eleves_classes jec, j_eleves_regime jer
					WHERE nom like '".$pref_motif.$motif_rech."%' AND
							e.login=jer.login AND
							jec.login=e.login AND
							c.id=jec.id_classe
					ORDER BY $order_type";
			}
			else{
				$sql="SELECT e.*,jer.* FROM eleves e, j_eleves_regime jer WHERE nom like '".$pref_motif.$motif_rech."%' AND
							e.login=jer.login
					ORDER BY $order_type";
			}
			//echo "$sql<br />\n";
			$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves dont le nom $texte_motif <b>$motif_rech</b></p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}

			//$motif_rech=$pref_motif.$motif_rech;
		}
		/*
		else if ($quelles_classes == 'rech_champ') {
			$pref_motif="";
			$texte_motif="commence par";
			if(((isset($_POST['mode_rech_champ']))&&($_POST['mode_rech_champ']=='contient'))||
			((isset($_GET['mode_rech_champ']))&&($_GET['mode_rech_champ']=='contient'))) {
				$pref_motif="%";
				$texte_motif="contient";
				$mode_rech_champ="contient";
			}

			if(preg_match('/classe/',$order_type)){
				$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, classes c, j_eleves_classes jec, j_eleves_regime jer
					WHERE $champ_rech like '".$pref_motif.$motif_rech."%' AND
							e.login=jer.login AND
							jec.login=e.login AND
							c.id=jec.id_classe
					ORDER BY $order_type";
			}
			else{
				$sql="SELECT e.*,jer.* FROM eleves e, j_eleves_regime jer WHERE $champ_rech like '".$pref_motif.$motif_rech."%' AND
							e.login=jer.login
					ORDER BY $order_type";
			}
			//echo "$sql<br />\n";
			$calldata = mysql_query($sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves dont le $champ_rech $texte_motif <b>$motif_rech</b></p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}
		}
		*/
		else if ($quelles_classes == 'rech_elenoet') {
			if(isset($motif_rech_elenoet)) {
				$motif_rech=$motif_rech_elenoet;
			}

			$pref_motif="";
			$texte_motif="commence par";
			if((isset($mode_rech_elenoet))&&($mode_rech_elenoet=='contient')) {
				$pref_motif="%";
				$texte_motif="contient";
				$mode_rech_elenoet="contient";

				$mode_rech="contient";
			}

			if(preg_match('/classe/',$order_type)){
				$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, classes c, j_eleves_classes jec, j_eleves_regime jer
					WHERE elenoet like '".$pref_motif.$motif_rech."%' AND
							e.login=jer.login AND
							jec.login=e.login AND
							c.id=jec.id_classe
					ORDER BY $order_type";
			}
			else{
				$sql="SELECT e.*,jer.* FROM eleves e, j_eleves_regime jer WHERE elenoet like '".$pref_motif.$motif_rech."%' AND
							e.login=jer.login
					ORDER BY $order_type";
			}
			//echo "$sql<br />\n";
			$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves dont l'elenoet $texte_motif <b>$motif_rech</b></p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}

			//$motif_rech=$pref_motif.$motif_rech;
		}
		else if ($quelles_classes == 'rech_ele_id') {
			if(isset($motif_rech_ele_id)) {
				$motif_rech=$motif_rech_ele_id;
			}

			$pref_motif="";
			$texte_motif="commence par";
			if((isset($mode_rech_ele_id))&&($mode_rech_ele_id=='contient')) {
				$pref_motif="%";
				$texte_motif="contient";
				$mode_rech_ele_id="contient";

				$mode_rech="contient";
			}

			if(preg_match('/classe/',$order_type)){
				$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, classes c, j_eleves_classes jec, j_eleves_regime jer
					WHERE ele_id like '".$pref_motif.$motif_rech."%' AND
							e.login=jer.login AND
							jec.login=e.login AND
							c.id=jec.id_classe
					ORDER BY $order_type";
			}
			else{
				$sql="SELECT e.*,jer.* FROM eleves e, j_eleves_regime jer WHERE ele_id like '".$pref_motif.$motif_rech."%' AND
							e.login=jer.login
					ORDER BY $order_type";
			}
			//echo "$sql<br />\n";
			$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves dont l'ele_id $texte_motif <b>$motif_rech</b></p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}

			//$motif_rech=$pref_motif.$motif_rech;
		}
		else if ($quelles_classes == 'rech_no_gep') {
			if(isset($motif_rech_no_gep)) {
				$motif_rech=$motif_rech_no_gep;
			}

			$pref_motif="";
			$texte_motif="commence par";
			if((isset($mode_rech_no_gep))&&($mode_rech_no_gep=='contient')) {
				$pref_motif="%";
				$texte_motif="contient";
				$mode_rech_no_gep="contient";

				$mode_rech="contient";
			}

			if(preg_match('/classe/',$order_type)){
				$sql="SELECT DISTINCT e.*,jer.* FROM eleves e, classes c, j_eleves_classes jec, j_eleves_regime jer
					WHERE no_gep like '".$pref_motif.$motif_rech."%' AND
							e.login=jer.login AND
							jec.login=e.login AND
							c.id=jec.id_classe
					ORDER BY $order_type";
			}
			else{
				$sql="SELECT e.*,jer.* FROM eleves e, j_eleves_regime jer WHERE no_gep like '".$pref_motif.$motif_rech."%' AND
							e.login=jer.login
					ORDER BY $order_type";
			}
			//echo "$sql<br />\n";
			$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves dont l'identifiant national $texte_motif <b>$motif_rech</b></p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}

			//$motif_rech=$pref_motif.$motif_rech;
		}
		else if ($quelles_classes == 'dse') { //Elève ayant une date de sortie renseignée.
			$sql="SELECT e.*, jer.* FROM eleves e
					LEFT JOIN j_eleves_regime jer ON e.login=jer.login
					WHERE jer.login =e.login AND e.date_sortie<>0 ORDER BY $order_type;";
			//echo "$sql<br />";
			$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			echo "<p align='center'>Liste des élèves ayant une date de sortie renseignée.</p>\n";
		}
		else if ($quelles_classes == 'dse_anomalie') { //Elève ayant une date de sortie renseignée mais néanmoins dans des classes
			$sql="SELECT e.*, jer.* FROM eleves e
					LEFT JOIN j_eleves_regime jer ON e.login=jer.login
					WHERE jer.login =e.login AND e.date_sortie<>0 AND e.login IN (SELECT DISTINCT login FROM j_eleves_classes) ORDER BY $order_type;";
			//echo "$sql<br />";
			$calldata = mysqli_query($GLOBALS["mysqli"], $sql);

			if((!isset($page_courante))||($page_courante!="modify_eleve")) {
				echo "<p align='center'>Liste des élèves ayant une date de sortie renseignée, mais qui sont néanmoins inscrits dans une classe.</p>\n";
			}
			else {
				// Message alternatif depuis modify_eleve.php
			}
		}
		elseif ($quelles_classes == 'no_etab') {
			if(preg_match('/classe/',$order_type)){
				//$sql="SELECT distinct e.*,c.classe FROM j_eleves_classes jec, classes c, eleves e LEFT JOIN j_eleves_etablissements jee ON jee.id_eleve=e.elenoet where jee.id_eleve is NULL and jec.login=e.login and c.id=jec.id_classe ORDER BY $order_type;";
				$sql="SELECT distinct e.*,c.classe,jer.* FROM j_eleves_classes jec, classes c, j_eleves_regime jer, eleves e LEFT JOIN j_eleves_etablissements jee ON jee.id_eleve=e.elenoet where jee.id_eleve is NULL and jec.login=e.login and jer.login=e.login and c.id=jec.id_classe ORDER BY $order_type;";
				//echo "$sql<br />\n";
				$calldata=mysqli_query($GLOBALS["mysqli"], $sql);
			}
			else{
				/*
				$sql="SELECT e.* FROM eleves e
					LEFT JOIN j_eleves_etablissements jee ON jee.id_eleve=e.elenoet
					where jee.id_eleve is NULL ORDER BY $order_type;";
				*/
				$sql="SELECT e.*, jer.* FROM j_eleves_regime jer, eleves e
					LEFT JOIN j_eleves_etablissements jee ON jee.id_eleve=e.elenoet
					where jee.id_eleve is NULL AND jer.login=e.login ORDER BY $order_type;";
				//echo "$sql<br />\n";
				$calldata=mysqli_query($GLOBALS["mysqli"], $sql);
			}
		}
		// 20130607
		elseif ($quelles_classes == 'rech_mef') {
			if(isset($motif_rech_mef)) {
				if($motif_rech_mef!="") {
					if(preg_match('/classe/',$order_type)) {
						$sql="SELECT distinct e.*,c.classe,jer.* FROM j_eleves_classes jec, 
																	classes c, 
																	j_eleves_regime jer, 
																	eleves e 
																WHERE jec.id_classe=c.id AND 
																	jec.login=e.login AND
																	jer.login=e.login AND
																	e.mef_code='$motif_rech_mef'
																ORDER BY $order_type;";
						//echo "$sql<br />\n";
						$calldata=mysqli_query($GLOBALS["mysqli"], $sql);
					}
					else {
						$sql="SELECT distinct e.*,jer.* FROM j_eleves_regime jer, 
															eleves e 
														WHERE jer.login=e.login AND
															e.mef_code='$motif_rech_mef'
														ORDER BY $order_type;";
						//echo "$sql<br />\n";
						$calldata=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}
				else {
					if(preg_match('/classe/',$order_type)) {
						$sql="SELECT distinct e.*,c.classe,jer.* FROM j_eleves_classes jec, 
																	classes c, 
																	j_eleves_regime jer, 
																	eleves e 
																WHERE jec.id_classe=c.id AND 
																	jec.login=e.login AND
																	jer.login=e.login AND
																	e.mef_code NOT IN (SELECT mef_code FROM mef)
																ORDER BY $order_type;";
						//echo "$sql<br />\n";
						$calldata=mysqli_query($GLOBALS["mysqli"], $sql);
					}
					else {
						$sql="SELECT distinct e.*,jer.* FROM j_eleves_regime jer, 
															eleves e 
														WHERE jer.login=e.login AND
															e.mef_code NOT IN (SELECT mef_code FROM mef)
														ORDER BY $order_type;";
						//echo "$sql<br />\n";
						$calldata=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}
			}
		}
		elseif ($quelles_classes == 'rech_etab') {
			if(isset($motif_rech_etab)) {
				if($motif_rech_etab!="") {
					if(preg_match('/classe/',$order_type)) {
						$sql="SELECT distinct e.*,c.classe,jer.* FROM j_eleves_classes jec, 
														classes c, 
														j_eleves_regime jer, 
														j_eleves_etablissements jee, 
														eleves e 
													WHERE jec.id_classe=c.id AND 
														jec.login=e.login AND 
														jer.login=e.login AND 
														jee.id_eleve=e.elenoet AND 
														jee.id_etablissement='$motif_rech_etab'
													ORDER BY $order_type;";
						//echo "$sql<br />\n";
						$calldata=mysqli_query($GLOBALS["mysqli"], $sql);
					}
					else {
						$sql="SELECT distinct e.*,jer.* FROM j_eleves_regime jer, 
														eleves e, 
														j_eleves_etablissements jee
												WHERE jer.login=e.login AND
													jee.id_eleve=e.elenoet AND 
													jee.id_etablissement='$motif_rech_etab'
												ORDER BY $order_type;";
						//echo "$sql<br />\n";
						$calldata=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}
				else {
					if(preg_match('/classe/',$order_type)) {
						$sql="(SELECT distinct e.*,c.classe,jer.* FROM j_eleves_classes jec, 
														classes c, 
														j_eleves_regime jer, 
														eleves e, 
														j_eleves_etablissements jee
													WHERE jec.id_classe=c.id AND 
														jec.login=e.login AND 
														jer.login=e.login AND 
														jee.id_eleve=e.elenoet AND 
														jee.id_etablissement NOT IN (SELECT id FROM etablissements))
							UNION (SELECT distinct e.*,c.classe,jer.* FROM j_eleves_classes jec, 
														classes c, 
														j_eleves_regime jer, 
														eleves e
													LEFT JOIN j_eleves_etablissements jee ON jee.id_eleve=e.elenoet
													WHERE jee.id_eleve is NULL AND 
														jer.login=e.login AND 
														jec.id_classe=c.id AND 
														jec.login=e.login AND 
														jer.login=e.login)
													ORDER BY $order_type;";
						//echo "$sql<br />\n";
						$calldata=mysqli_query($GLOBALS["mysqli"], $sql);
					}
					else {
						$sql="SELECT distinct e.*,jer.* FROM j_eleves_regime jer, 
												eleves e 
											LEFT JOIN j_eleves_etablissements jee ON jee.id_eleve=e.elenoet
											WHERE jee.id_eleve is NULL AND 
												jer.login=e.login 
											ORDER BY $order_type;";
						//echo "$sql<br />\n";
						$calldata=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}
			}
		}
	}
?>
