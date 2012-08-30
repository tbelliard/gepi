<?php
	@set_time_limit(0);


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

	$sql="SELECT 1=1 FROM droits WHERE id='/edt_organisation/edt_xml.php';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/edt_organisation/edt_xml.php',
	administrateur='V',
	professeur='F',
	cpe='F',
	scolarite='F',
	eleve='F',
	responsable='F',
	secours='F',
	autre='F',
	description='Import XML EDT',
	statut='';";
	$insert=mysql_query($sql);
	}

	if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
	}

	$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

	//**************** EN-TETE *****************
	$titre_page = "Import XML EDT";
	if(file_exists("../lib/header.inc.php")) {
		require_once("../lib/header.inc.php");
	}
	else {
		require_once("../lib/header.inc");
	}
	//**************** FIN EN-TETE *****************

	$debug_import="n";

	// On va uploader les fichiers XML dans le tempdir de l'utilisateur (administrateur, ou scolarité pour les màj Sconet)
	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="CREATE TABLE IF NOT EXISTS tempo5 (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	texte TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	info VARCHAR(200) NOT NULL
	) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$create_table=mysql_query($sql);

	// =======================================================
	if(isset($_GET['nettoyage'])) {
		check_token(false);
		echo "<h2>Suppression des XML et CSV</h2>\n";
		echo "<p class=bold><a href='";
		if(isset($_SESSION['ad_retour'])){
			echo $_SESSION['ad_retour'];
		}
		else{
			echo "edt_initialiser.php";
		}
		echo "'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
		echo "<a href='".$_SERVER['PHP_SELF']."'> | Autre import</a></p>\n";
		//echo "</div>\n";

		echo "<p>Si des fichiers XML et CSV existent, ils seront supprimés...</p>\n";
		$tabfich=array("edt.xml", "g_edt_2.csv");

		for($i=0;$i<count($tabfich);$i++){
			if(file_exists("../temp/".$tempdir."/$tabfich[$i]")) {
				echo "<p>Suppression de $tabfich[$i]... ";
				if(unlink("../temp/".$tempdir."/$tabfich[$i]")){
					echo "réussie.</p>\n";
				}
				else{
					echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
				}
			}
		}

		require("../lib/footer.inc.php");
		die();
	}
	// =======================================================
	else {
		echo "<center><h3 class='gepi'>Import XML EDT</h3></center>\n";

		echo "<p class=bold><a href='";
		if(isset($_SESSION['ad_retour'])){
			echo $_SESSION['ad_retour'];
		}
		else{
			echo "edt_initialiser.php";
		}
		echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
		echo " | <a href='".$_SERVER['PHP_SELF']."?nettoyage=oui".add_token_in_url()."'>Suppression des fichiers XML et CSV existants</a>";
		//echo "</p>\n";

		if(!isset($step)) {

			echo "<p class='bold'>Uploader un nouveau fichier</p>\n";
			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			echo add_token_field();
			echo "<p>Veuillez fournir le fichier EXP_COURS.xml&nbsp;:<br />\n";
			echo "<input type=\"file\" size=\"65\" name=\"edt_xml_file\" /><br />\n";
			echo "<input type='hidden' name='step' value='0' />\n";
			echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "<p><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";

			$dest_file="../temp/".$tempdir."/edt.xml";
			if(file_exists($dest_file)) {
				$sql="SELECT texte AS col1 FROM tempo5 WHERE info='groupe' OR info='classe';";
				$res_grp=mysql_query($sql);
				if(mysql_num_rows($res_grp)>0) {
					echo "<br />\n";
					echo "<p><span class='bold'>Ou</span> <a href='".$_SERVER['PHP_SELF']."?step=1'>repartir du fichier précédemment uploadé</a>.</p>\n";
				}
			}
			
			echo "<p><br /></p>\n";
			echo "<p><em>NOTES&nbsp;:</em></p>\n";
			echo "<div style='margin-left:3em'>
<p>Pour générer le fichier EXP_COURS.xml à partir du logiciel EDT de la société Index Education&nbsp;:<br />
Voir <a href='http://www.sylogix.org/projects/gepi/wiki/Edt_indexedu_udt'>http://www.sylogix.org/projects/gepi/wiki/Edt_indexedu_udt</a></p>

<br />

<p><strong>Conseils préliminaires très importants&nbsp;:</strong></p>
<ul>
	<li><p>Ce type d'export ne peut se faire que depuis la version complète d'EDT installée généralement sur le poste du chef d'établissement ou/et de l'adjoint et non depuis la version temporaire ou la version de consultation.</p></li>
	<li><p>S'assurer que les identifiants (les codes) des matières soient les mêmes dans EDT et dans Gepi (<strong>Onglet Matières</strong>) pour faciliter l'établissement des correspondances lors de l'importation dans Gepi. Faire attention en particulier aux identifiants des langues<br />(<em>ex : ALL1 utilisé dans EDT ne sera pas associé aussi facilement  par Gepi si on y utilise ALLBI au lieu d'ALL1</em>).</p></li>
	<li><p>S'assurer que les noms des groupes soient suffisamment explicites quant aux classes des élèves qui les composent (<strong>Onglet Groupes</strong>) afin de faciliter l'établissement des correspondances<br />
	(<em>ex : soit un groupe d'ESP2 avec des élèves de 3ème 1 et de 3ème 2 et un autre avec des élèves de 3ème 3 et de 3ème 4 ; EDT va dénommer ces groupes '3ESP2GR.1' et '3ESP2GR.2' par défaut ; une appellation du genre '3_1&2ESP2' et '3_3&4ESP2' sera nettement plus pratique</em>)<br />
	(<em>astuce&nbsp;: l'identification 'automatique' des classes risque même d'être meilleure avec des noms comme '3_1 3_2 ESP2' et '3_3 3_4 ESP2'</em>)</p></li>
</ul>

<p><strong>Extraction proprement dite du XML&nbsp;:</strong></p>
<ol>
<li>Se placer dans l'onglet Cours et extraire les cours que l'on veut voir figurer dans l'edt de Gepi. Si l'on veut exporter l'ensemble de l'emploi du temps, faire une extraction complète (<strong>Extraire > Tout extraire</strong>).</li>
<li>Se rendre dans <strong>Fichiers > Imports/Exports > Autres > Exporter un fichier texte</strong></li>
<li>Dans la fenêtre qui apparaît, les paramètres par défaut sont corrects mais s'assurer quand même des suivants&nbsp;:<br />
	<ul>
	<li>Type de données à exporter : <strong>Cours</strong></li>
	<li>Sélection du type d'export : <strong>Format XML (*.xml)</strong></li>
	<li>Choix de la période : <strong>Année complète</strong></li>
	<li>Cocher la case <strong>Visualiser toutes les données</strong></li>
	</ul>
</li>
<li>Cliquer sur <strong>Exporter</strong></li>
<li>EDT génère le fichier <strong>EXP_COURS.xml</strong> dont Gepi aura besoin</li>
</ol>\n";
			echo "<p><br /></p>\n";
		}
		else {
			echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour à l'upload du fichier</a>";

			check_token(false);
			$post_max_size=ini_get('post_max_size');
			$upload_max_filesize=ini_get('upload_max_filesize');
			$max_execution_time=ini_get('max_execution_time');
			$memory_limit=ini_get('memory_limit');

			if($step==0) {
				echo "</p>\n";

				$xml_file = isset($_FILES["edt_xml_file"]) ? $_FILES["edt_xml_file"] : NULL;

				if(!is_uploaded_file($xml_file['tmp_name'])) {

					echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

					echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
					echo "post_max_size=$post_max_size<br />\n";
					echo "upload_max_filesize=$upload_max_filesize<br />\n";
					echo "</p>\n";

					require("../lib/footer.inc.php");
					die();
				}
				else {
					if(!file_exists($xml_file['tmp_name'])){
						echo "<p style='color:red;'>Le fichier aurait été uploadé... mais ne serait pas présent/conservé.</p>\n";

						echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
						echo "post_max_size=$post_max_size<br />\n";
						echo "upload_max_filesize=$upload_max_filesize<br />\n";
						echo "et le volume de ".$xml_file['name']." serait<br />\n";
						echo "\$xml_file['size']=".volume_human($xml_file['size'])."<br />\n";
						echo "</p>\n";

						require("../lib/footer.inc.php");
						die();
					}

					echo "<p>Le fichier a été uploadé.</p>\n";

					$source_file=$xml_file['tmp_name'];
					$dest_file="../temp/".$tempdir."/edt.xml";
					$res_copy=copy("$source_file" , "$dest_file");

					if(!$res_copy){
						echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";

						require("../lib/footer.inc.php");
						die();
					}
					else{
						echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

						$step=1;
					}
				}
			}

			if($step==1) {
				echo "</p>\n";

				// On va lire plusieurs fois le fichier pour remplir des tables temporaires.

				$dest_file="../temp/".$tempdir."/edt.xml";

				$edt_xml=simplexml_load_file($dest_file);
				if(!$edt_xml) {
					echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				$nom_racine=$edt_xml->getName();
				if(my_strtoupper($nom_racine)!='TABLE') {
					echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML EDT.<br />Sa racine devrait être 'TABLE'.</p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				echo "<br />\n";
				// Recherche des classes
				echo "<p class='bold'>Recherche des classes et des groupes dans le fichier&nbsp;:</p>";

				$i=0;
				$tab_clas_ou_grp=array();
				$tab_semaine=array();
				foreach ($edt_xml->children() as $cours) {
					//echo("<p><b>Structure</b><br />");

					//$chaine_structures_eleve="STRUCTURES_ELEVE";
					foreach($cours->attributes() as $key => $value) {
						//echo(" Cours $key -&gt;".$value."<br />");

						$i++;
						$tab_cours[$i]=array();
						$tab_cours[$i]['attribut'][$key]=$value;
						$tab_cours[$i]['enfant']=array();

						foreach($cours->children() as $key => $value) {
							$tab_cours[$i]["enfant"][my_strtolower($key)]=trim(nettoyer_caracteres_nom(preg_replace("/ /","",preg_replace('/"/','',trim($value))), "an", "_ -"," "));
						}

						if($debug_import=='y') {
							echo "<pre style='color:green;'><b>Tableau \$tab_cours[$i]&nbsp;:</b>";
							print_r($tab_cours[$i]);
							echo "</pre>";
						}

						/*
						Jour;Heure;Div;Matière;Professeur;Salle;Groupe;Regroup;Eff;Mo;Freq;Aire;
						Lundi;8H;6 E;EPS';XXXXXXXX PIERRE;GYMNA;;;16;CG;;;
						*/

						/*
						<Cours numero="2">
						<NUMERO>2</NUMERO>
						<DUREE>1h00</DUREE>
						<FREQUENCE>H</FREQUENCE>
						<MAT_CODE>AGL1</MAT_CODE>
						<MAT_LIBELLE>ANGLAIS LV1</MAT_LIBELLE>
						<PROF_NOM>XXXXXXXXXX</PROF_NOM>
						<PROF_PRENOM>Cécile</PROF_PRENOM>
						<CLASSE>3_6</CLASSE>
						<SALLE>S.27 VP Ang.</SALLE>
						<ALT.>H</ALT.>
						<MOD.>CG</MOD.>
						<CO-ENS.>N</CO-ENS.>
						<POND.>1</POND.>
						<JOUR>mercredi</JOUR>
						<H.DEBUT>  08h00</H.DEBUT>
						<EFFECTIF>30</EFFECTIF>
						</Cours>
						*/

						if((isset($tab_cours[$i]["enfant"]["classe"]))&&($tab_cours[$i]["enfant"]["classe"]!="")) {
							if(!in_array($tab_cours[$i]["enfant"]["classe"], $tab_clas_ou_grp)) {$tab_clas_ou_grp[]=$tab_cours[$i]["enfant"]["classe"];}
						}

						if((isset($tab_cours[$i]["enfant"]["frequence"]))&&($tab_cours[$i]["enfant"]["frequence"]!="")) {
							if(!in_array($tab_cours[$i]["enfant"]["frequence"], $tab_semaine)) {$tab_semaine[]=$tab_cours[$i]["enfant"]["frequence"];}
						}
					}
				}

				sort($tab_clas_ou_grp);
				sort($tab_semaine);

				echo "<p>Il y a probablement plus de groupes que de classes.<br />Cochez tous les groupes en cliquant sur le lien <a href=\"javascript:CocheColonne('groupe');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a> des groupes, puis cochez une par une les classes dans la colonne classe.</p>\n";

				$tab_grp=array();
				//$sql="SELECT * FROM tempo2 WHERE col2='groupe';";
				$sql="SELECT texte AS col1 FROM tempo5 WHERE info='groupe';";
				$res_grp=mysql_query($sql);
				if(mysql_num_rows($res_grp)>0) {
					while($lig_grp=mysql_fetch_object($res_grp)) {
						$tab_grp[]=$lig_grp->col1;
					}
				}

				$tab_classes_base=array();
				$sql="SELECT classe FROM classes;";
				$res_classes_base=mysql_query($sql);
				if(mysql_num_rows($res_classes_base)>0) {
					while($lig_classes_base=mysql_fetch_object($res_classes_base)) {
						$tab_classes_base[]=$lig_classes_base->classe;
					}
				}

				echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
				echo "<table class='boireaus'>\n";
				$alt=1;
				echo "<tr>\n";
				echo "<th rowspan='2'>Nom trouvé</th>\n";
				echo "<th>Classe<br />";
				echo "<a href=\"javascript:CocheColonne('classe');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a>";
				echo "</th>\n";
				echo "<th>Groupe<br />";
				echo "<a href=\"javascript:CocheColonne('groupe');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a>";
				echo "</th>\n";
				echo "</tr>\n";

				echo "<tr>\n";
				//echo "<th>Nom trouvé</th>\n";
				echo "<th id='total_clas'></th>\n";
				echo "<th id='total_grp'></th>\n";
				echo "</tr>\n";

				for($i=0;$i<count($tab_clas_ou_grp);$i++) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'>\n";
					echo "<td id='ligne_$i'>".$tab_clas_ou_grp[$i];
					echo "<input type='hidden' name='clas_ou_grp[$i]' value=\"".$tab_clas_ou_grp[$i]."\" />\n";
					echo "</td>\n";
					echo "<td onmouseover=\"document.getElementById('ligne_$i').style.color='red';\" onmouseout=\"document.getElementById('ligne_$i').style.color='';\" onclick=\"document.getElementById('type_clas_$i').checked=true;mise_ligne_en_gras_ou_pas($i)\">\n";
					echo "<input type='radio' name='type[$i]' id='type_clas_$i' value='classe' ";
					$temoin_checked='n';
					if(((count($tab_grp)>0)&&(!in_array($tab_clas_ou_grp[$i], $tab_grp)))||in_array($tab_clas_ou_grp[$i], $tab_classes_base)) {
						echo "checked ";
						$temoin_checked='y';
					}
					//echo " onchange=\"if(document.getElementById('type_clas_$i').checked==true) {document.getElementById('ligne_$i').style.fontWeight='bold';} else {document.getElementById('ligne_$i').style.fontWeight='';}\" ";
					echo " onchange=\"mise_en_gras_ou_pas($i);\" ";
					echo "/></td>\n";

					echo "<td onmouseover=\"document.getElementById('ligne_$i').style.color='red';\" onmouseout=\"document.getElementById('ligne_$i').style.color='';\" onclick=\"document.getElementById('type_grp_$i').checked=true;;mise_ligne_en_gras_ou_pas($i)\"><input type='radio' name='type[$i]' id='type_grp_$i' value='groupe' ";
					//if(in_array($tab_clas_ou_grp[$i], $tab_grp)) {
					if((in_array($tab_clas_ou_grp[$i], $tab_grp))||($temoin_checked=='n')) {
						echo "checked ";
					}
					//echo " onchange=\"if(document.getElementById('type_clas_$i').checked==true) {document.getElementById('ligne_$i').style.fontWeight='bold';} else {document.getElementById('ligne_$i').style.fontWeight='';}\" ";
					echo " onchange=\"mise_en_gras_ou_pas($i);\" ";
					echo "/></td>\n";
					echo "</tr>\n";

				}
				echo "</table>\n";

				if(count($tab_semaine)>0) {

					$tab_sem=array();
					$sql="SELECT * FROM tempo5 WHERE info='type_edt_semaine';";
					$res_sem=mysql_query($sql);
					if(mysql_num_rows($res_sem)>0) {
						while($lig_sem=mysql_fetch_object($res_sem)) {
							$tab_sem[]=$lig_sem->texte;
						}
					}

					echo "<br />\n";
					echo "<p>Il est également nécessaire pour la suite d'identifier les semaines déclarées dans <a href='admin_config_semaines.php?action=visualiser' target='_blank'>Définition des types de semaines</a>.</p>\n";
					echo "<p>Quelles types de semaines particuliers faut-il retenir&nbsp;?</p>\n";
					echo "<p>";
					for($j=0;$j<count($tab_semaine);$j++) {
						echo "<input type='checkbox' name='tab_sem[]' id='tab_sem_$j' value=\"".$tab_semaine[$j]."\" ";
						if((in_array($tab_semaine[$j],$tab_sem))||(count($tab_sem)==0)) {echo "checked ";}
						echo "/><label for='tab_sem_$j'> ".$tab_semaine[$j]."</label><br />\n";
					}
					echo "</p>\n";
					echo "<p><strong>Ne pas cocher</strong> des codes correspondant à des cours ayant lieu chaque semaine.</p>\n";
				}

				echo "<input type='hidden' name='step' value='2' />\n";
				echo "<p><input type='submit' name='Valider' value='Valider' /></p>\n";
				echo add_token_field();
				echo "</form>\n";

				echo "<script type='text/javascript'>
	function CocheColonne(col) {
		if(col=='classe') {
			for(i=0;i<$i;i++) {
				if(document.getElementById('type_clas_'+i)) {
					document.getElementById('type_clas_'+i).checked=true;
				}
			}
		}
		else {
			for(i=0;i<$i;i++) {
				if(document.getElementById('type_grp_'+i)) {
					document.getElementById('type_grp_'+i).checked=true;
				}
			}
		}

		calcule_effectifs();
		mise_en_gras_ou_pas();
	}

	function mise_ligne_en_gras_ou_pas(i) {
		//alert(1);
		if((document.getElementById('ligne_'+i))&&(document.getElementById('type_clas_'+i))) {
			//alert(2);
			if(document.getElementById('type_clas_'+i).checked==true) {
				//alert(3);
				document.getElementById('ligne_'+i).style.fontWeight='bold';
				document.getElementById('ligne_'+i).style.backgroundColor='yellow';
			}
			else {
				document.getElementById('ligne_'+i).style.fontWeight='';
				document.getElementById('ligne_'+i).style.backgroundColor='';
			}
		}
		calcule_effectifs();
	}

	function calcule_effectifs() {
		var eff_clas=0;
		var eff_grp=0;
		var i;
		for(i=0;i<$i;i++) {
			if((document.getElementById('type_clas_'+i))&&(document.getElementById('type_clas_'+i).checked==true)) {
				eff_clas++;
			}
			else {
				eff_grp++;
			}
		}
		document.getElementById('total_clas').innerHTML=eff_clas;
		document.getElementById('total_grp').innerHTML=eff_grp;
	}

	function mise_en_gras_ou_pas() {
		//alert('i='+$i);
		for(i=0;i<$i;i++) {
			//if(i<10) {alert('i='+i)}
			mise_ligne_en_gras_ou_pas(i);
		}
	}

	mise_en_gras_ou_pas();
</script>\n";
				echo "<p><br /></p>\n";
			}

			if($step==2) {
				echo " | <a href='".$_SERVER['PHP_SELF']."?step=1'>Retour à la distinction groupe/classe</a>";
				echo "</p>\n";



// PB tempo2 a des col1 et col2 en VARCHAR(100)
// C'EST TROP COURT



				//$sql="TRUNCATE TABLE tempo2;";
				//$sql="TRUNCATE TABLE tempo5;";
				$sql="DELETE FROM tempo5 WHERE info='groupe' OR info='classe';";
				$vide_table = mysql_query($sql);

				$tab_clas_grp=array();
				//$sql="SELECT * FROM tempo2 WHERE col2='groupe';";
				$sql="SELECT * FROM tempo5;";
				$res_grp=mysql_query($sql);
				if(mysql_num_rows($res_grp)>0) {
					while($lig_grp=mysql_fetch_object($res_grp)) {
						$tab_clas_grp[$lig_grp->texte][]=$lig_grp->info;
					}
				}

				$chaine_clas="";
				$clas_ou_grp=isset($_POST['clas_ou_grp']) ? $_POST['clas_ou_grp'] : array();
				$type=isset($_POST['type']) ? $_POST['type'] : array();
				//echo "count(\$clas_ou_grp)=".count($clas_ou_grp)."<br />";
				$tab_grp=array();
				$tab_clas=array();
				for($i=0;$i<count($clas_ou_grp);$i++) {
					//$sql="INSERT INTO tempo2 SET col1='".mysql_real_escape_string($clas_ou_grp[$i])."', col2='".$type[$i]."';";
					$sql="INSERT INTO tempo5 SET texte='".mysql_real_escape_string($clas_ou_grp[$i])."', info='".$type[$i]."';";
					$insert=mysql_query($sql);
					if($type[$i]=='groupe') {$tab_grp[]=$clas_ou_grp[$i];}
					else {
						$tab_clas[]=$clas_ou_grp[$i];

						if($chaine_clas!="") {
							$chaine_clas.=", ";
						}
						$chaine_clas.="'".$clas_ou_grp[$i]."'";
					}
				}

				$sql="DELETE FROM tempo5 WHERE info='type_edt_semaine';";
				$menage=mysql_query($sql);

				$tab_sem=isset($_POST['tab_sem']) ? $_POST['tab_sem'] : array();
				for($i=0;$i<count($tab_sem);$i++) {
					$sql="INSERT INTO tempo5 SET texte='".mysql_real_escape_string($tab_sem[$i])."', info='type_edt_semaine';";
					$insert=mysql_query($sql);
				}

				if(count($tab_grp)>0) {
					echo "<p class='bold'>Recherche des classes associées aux groupes&nbsp;:</p>\n";

					echo "<p>Il s'agit maintenant de préciser à quelles classes sont associés les groupes.<br />Vous pouvez, si les noms des groupes reprennent le nom des classes, obtenir une <a href='javascript:detection_classes_grp()'>détection automatique</a> assez fiable.<br />Vous n'aurez qu'à contrôler et compléter.</p>\n";

					sort($tab_grp);
					sort($tab_clas);

					echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
					echo add_token_field();
					echo "<table class='boireaus'>\n";
					$alt=1;
					echo "<tr>\n";
					echo "<th>Groupe <a href='javascript:detection_classes_grp()' title=\"Tenter d'identifier les classes associées d'après le nom du groupe\">";
					//echo "<img src='../images/icons/wizard.png' />";
					echo "<img src='../images/icons/flag2.gif' /></a></th>\n";
					echo "<th>Effectif</th>";
					echo "<th>Classes associées<br />";

					echo "<a href=\"javascript:CocheClasses(true);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Cocher toutes les classes' title='Cocher toutes les classes' /></a> / <a href=\"javascript:CocheClasses(false);changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Décocher toutes les classes' title='Décocher toutes les classes' /></a>";

					echo "</th>\n";
					echo "</tr>\n";
					$cpt=0;
					$chaine_clas_num="";
					for($i=0;$i<count($tab_grp);$i++) {
						$alt=$alt*(-1);
						echo "<tr class='lig$alt white_hover'>\n";
						echo "<td>";
						echo "<a href='javascript:coche_clas_grp($i)' id='a_grp_$i'>".$tab_grp[$i]."</a>";
						echo "<input type='hidden' name='grp[$i]' value=\"".$tab_grp[$i]."\" />\n";
						echo " <a href='javascript:decoche($i)'><img src='../images/disabled.png' /></a>";
						echo "</td>\n";
						echo "<td id='td_eff_$i'>\n";
						echo "Effectif calculé dynamiquement";
						echo "</td>\n";
						echo "<td>\n";
						if($chaine_clas_num!="") {$chaine_clas_num.=", ";}
						$chaine_clas_num.="'$cpt'";
						for($j=0;$j<count($tab_clas);$j++) {
							echo "<div style='float:left; margin-right:1em;'>\n";
							echo "<input type='checkbox' name='clas_".$i."[]' id='clas_".$cpt."' value=\"".$tab_clas[$j]."\"";
							echo " onchange=\"checkbox_change_classe('clas_".$cpt."'); calcule_effectif_ligne($i); changement();\"";
							if((isset($tab_clas_grp[$tab_grp[$i]]))&&(in_array($tab_clas[$j],$tab_clas_grp[$tab_grp[$i]]))) {
								echo " checked";
							}
							echo " /><label for='clas_".$cpt."' id='texte_clas_".$cpt."'";
							if((isset($tab_clas_grp[$tab_grp[$i]]))&&(in_array($tab_clas[$j],$tab_clas_grp[$tab_grp[$i]]))) {
								echo " style='font-weight:bold;'";
							}
							echo ">".$tab_clas[$j]."</label>\n";
							echo "</div>\n";
							$cpt++;
						}
						echo "</td>\n";
						echo "</tr>\n";
					}
					if($chaine_clas_num!="") {$chaine_clas_num.=", ";}
					$chaine_clas_num.="'$cpt'";
					echo "</table>\n";
					echo "<input type='hidden' name='step' value='3' />\n";
					echo "<p><input type='submit' name='Valider' value='Valider' /></p>\n";
					echo "</form>\n";

					echo "<script type='text/javascript'>\n";
					echo js_checkbox_change_style('checkbox_change_classe');
					echo "</script>\n";


					echo "<script type='text/javascript'>
	function CocheClasses(mode) {
		var i;
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('clas_'+i)) {
				document.getElementById('clas_'+i).checked=mode;
			}
			if(document.getElementById('texte_clas_'+i)) {
				if(mode==true) {
					document.getElementById('texte_clas_'+i).style.fontWeight='bold';
					document.getElementById('texte_clas_'+i).style.backgroundColor='yellow';
				}
				else {
					document.getElementById('texte_clas_'+i).style.fontWeight='';
					document.getElementById('texte_clas_'+i).style.backgroundColor='';
				}
			}
		}

		calcule_effectifs('');
	}

	var tab_clas=new Array($chaine_clas);
	var tab_clas_num=new Array($chaine_clas_num);

	function coche_clas_grp(i) {
		if(document.getElementById('a_grp_'+i)) {
			tab=document.getElementById('a_grp_'+i).innerHTML.split(' ');
			//alert('tab.length='+tab.length)
			var j;
			for(j=0;j<tab.length;j++) {
				//alert('tab['+j+']='+tab[j])
				var k;
				for(k=tab_clas_num[i];k<tab_clas_num[i+1];k++) {
					if(tab[j]!='') {
						//if(document.getElementById('texte_clas_'+k).innerHTML==tab[j]) {alert('bingo: '+document.getElementById('texte_clas_'+k).innerHTML);}
						//if(document.getElementById('texte_clas_'+k).innerHTML.toLowerCase()==tab[j].toLowerCase()) {
						if(document.getElementById('texte_clas_'+k).innerHTML.toLowerCase().replace(/^\s+/g,'').replace(/\s+$/g,'')==tab[j].toLowerCase().replace(/^\s+/g,'').replace(/\s+$/g,'')) {
							//if(document.getElementById('texte_clas_'+k).innerHTML.toLowerCase()==tab[j].toLowerCase()) {alert('bingo 2: '+document.getElementById('texte_clas_'+k).innerHTML.toLowerCase());}

							if(document.getElementById('clas_'+k)) {
								document.getElementById('clas_'+k).checked=true;
							}
							if(document.getElementById('texte_clas_'+k)) {
								document.getElementById('texte_clas_'+k).style.fontWeight='bold';
								document.getElementById('texte_clas_'+k).style.backgroundColor='yellow';
							}
						}
					}
				}
			}
		}

		calcule_effectif_ligne(i);
		//alert('calcule_effectif_ligne('+i+')');
	}

	function decoche(i) {
		var k;
		for(k=tab_clas_num[i];k<tab_clas_num[i+1];k++) {
			if(document.getElementById('clas_'+k)) {
				document.getElementById('clas_'+k).checked=false;
			}
			if(document.getElementById('texte_clas_'+k)) {
				document.getElementById('texte_clas_'+k).style.fontWeight='';
				document.getElementById('texte_clas_'+k).style.backgroundColor='';
			}
		}

		calcule_effectif_ligne(i);
	}

	function calcule_effectifs(k) {
		if(k=='') {
			var i;
			for(i=0;i<$cpt;i++) {
				calcule_effectif_ligne(i);
			}
		}
		else{
			calcule_effectif_ligne(k);
		}
	}

	function calcule_effectif_ligne(i) {
		if(document.getElementById('td_eff_'+i)) {
			eff=0;
			var k;
			for(k=tab_clas_num[i];k<tab_clas_num[i+1];k++) {
				if(document.getElementById('clas_'+k)) {
					if(document.getElementById('clas_'+k).checked==true) {
						eff++;
					}
				}
			}
			document.getElementById('td_eff_'+i).innerHTML=eff;
		}
	}

	calcule_effectifs('');
	
	function detection_classes_grp() {
		var i;
		for(i=0;i<$i;i++) {
			coche_clas_grp(i);
		}
	}
</script>\n";
					echo "<p><br /></p>\n";

				}
				else {
					$step=4;
				}
			}

			if($step==3) {
				//debug_var();

				$tab_clas_grp=array();

				$grp=isset($_POST['grp']) ? $_POST['grp'] : array();
				for($i=0;$i<count($grp);$i++) {
					$tab_clas=isset($_POST['clas_'.$i]) ? $_POST['clas_'.$i] : array();

					$tab_clas_grp[$grp[$i]]=array();

					for($j=0;$j<count($tab_clas);$j++) {
						//$sql="INSERT INTO tempo2 SET col1='".mysql_real_escape_string($grp[$i])."', col2='".mysql_real_escape_string($tab_clas[$j])."';";
						$sql="INSERT INTO tempo5 SET texte='".mysql_real_escape_string($grp[$i])."', info='".mysql_real_escape_string($tab_clas[$j])."';";
						$insert=mysql_query($sql);
						$tab_clas_grp[$grp[$i]][]=$tab_clas[$j];
					}
				}

				if($debug_import=='y') {
					echo "<pre style='color:green;'><b>Tableau \$grp&nbsp;:</b>";
					print_r($grp);
					echo "</pre>";
					echo "<hr />";
				}

				if($debug_import=='y') {
					echo "<pre style='color:green;'><b>Tableau \$tab_clas_grp&nbsp;:</b>";
					print_r($tab_clas_grp);
					echo "</pre>";
				}

				$step=4;
			}

			if($step==4) {
				echo " | <a href='".$_SERVER['PHP_SELF']."?step=1'>Retour à la distinction groupe/classe</a>";
				echo "</p>\n";
				//==============================================================
				$dest_file="../temp/".$tempdir."/edt.xml";

				$edt_xml=simplexml_load_file($dest_file);

				echo "<p>\n";
				echo "Traitement du fichier...<br />\n";

				$tab_sem=array();
				$sql="SELECT * FROM tempo5 WHERE info='type_edt_semaine';";
				$res_sem=mysql_query($sql);
				if(mysql_num_rows($res_sem)>0) {
					while($lig_sem=mysql_fetch_object($res_sem)) {
						$tab_sem[]=$lig_sem->texte;
					}
				}
/*
echo "<pre>";
echo print_r($tab_sem);
echo "</pre>";
*/
				$nb_cours=0;
				$nb_lignes=0;
				$fich=fopen("../temp/".$tempdir."/g_edt_2.csv", "w+");
				/*
				$tab_champs_struct=array("CODE_STRUCTURE","TYPE_STRUCTURE");
				$tab_ele_id=array();
				*/
				$i=-1;
				//$objet_table=($edt_xml->TABLE);
				//foreach ($objet_table->children() as $cours) {
				foreach ($edt_xml->children() as $cours) {
					//echo("<p><b>Structure</b><br />");

					//$chaine_structures_eleve="STRUCTURES_ELEVE";
					foreach($cours->attributes() as $key => $value) {
						//echo(" Cours $key -&gt;".$value."<br />");

						$i++;
						$tab_cours[$i]=array();
						$tab_cours[$i]['attribut'][$key]=$value;
						$tab_cours[$i]['enfant']=array();

						foreach($cours->children() as $key => $value) {
							$tab_cours[$i]["enfant"][my_strtolower($key)]=trim(nettoyer_caracteres_nom(preg_replace("/ /","",preg_replace('/"/','',trim($value))), "an", "_ -"," "));
						}

						if($debug_import=='y') {
							echo "<pre style='color:green;'><b>Tableau \$tab_cours[$i]&nbsp;:</b>";
							print_r($tab_cours[$i]);
							echo "</pre>";
						}

						/*
						Jour;Heure;Div;Matière;Professeur;Salle;Groupe;Regroup;Eff;Mo;Freq;Aire;
						Lundi;8H;6 E;EPS';XXXXXXXX PIERRE;GYMNA;;;16;CG;;;
						*/

						/*
						<Cours numero="2">
						<NUMERO>2</NUMERO>
						<DUREE>1h00</DUREE>
						<FREQUENCE>H</FREQUENCE>
						<MAT_CODE>AGL1</MAT_CODE>
						<MAT_LIBELLE>ANGLAIS LV1</MAT_LIBELLE>
						<PROF_NOM>XXXXXXXXXX</PROF_NOM>
						<PROF_PRENOM>Cécile</PROF_PRENOM>
						<CLASSE>3_6</CLASSE>
						<SALLE>S.27 VP Ang.</SALLE>
						<ALT.>H</ALT.>
						<MOD.>CG</MOD.>
						<CO-ENS.>N</CO-ENS.>
						<POND.>1</POND.>
						<JOUR>mercredi</JOUR>
						<H.DEBUT>  08h00</H.DEBUT>
						<EFFECTIF>30</EFFECTIF>
						</Cours>
						*/

						if(((isset($tab_cours[$i]["enfant"]["jour"]))&&($tab_cours[$i]["enfant"]["jour"]!=""))&&
							((isset($tab_cours[$i]["enfant"]["h.debut"]))&&($tab_cours[$i]["enfant"]["h.debut"]!=""))&&
							((isset($tab_cours[$i]["enfant"]["classe"]))&&($tab_cours[$i]["enfant"]["classe"]!=""))&&
							((isset($tab_cours[$i]["enfant"]["mat_code"]))&&($tab_cours[$i]["enfant"]["mat_code"]!=""))
						) {

							// Insérer deux lignes si $tab_cours[$i]["enfant"]["duree"] 2h... pb pour les 1.5h
							$duree=1;
							if(isset($tab_cours[$i]["enfant"]["duree"])) {
								$duree=preg_replace("/^0*/","",preg_replace("/[hH].*/","",$tab_cours[$i]["enfant"]["duree"]));
								if($duree==0) {
									$duree=1;
								}
							}

							if(in_array($tab_cours[$i]["enfant"]["classe"], $grp)) {
							//if(($tab_cours[$i]["enfant"]["classe"]!='')&&(in_array($tab_cours[$i]["enfant"]["classe"], $grp))) {

								if(count($tab_clas_grp[$tab_cours[$i]["enfant"]["classe"]])>0) {
									for($j=0;$j<count($tab_clas_grp[$tab_cours[$i]["enfant"]["classe"]]);$j++) {
										for($loop=0;$loop<$duree;$loop++) {

											$ligne=$tab_cours[$i]["enfant"]["jour"].";";

											$tmp_tab=explode("h", mb_strtolower($tab_cours[$i]["enfant"]["h.debut"]));
											$heure=preg_replace("/^0*/","",$tmp_tab[0])+$loop;
											if($heure<10) {$heure="0".$heure;}
											$minute=$tmp_tab[1];
											$ligne.=$heure."h".$minute.";";


											//$ligne.=$tab_cours[$i]["enfant"]["classe"].";";
											$ligne.=$tab_clas_grp[$tab_cours[$i]["enfant"]["classe"]][$j].";";
											$ligne.=$tab_cours[$i]["enfant"]["mat_code"].";";
											if(isset($tab_cours[$i]["enfant"]["prof_nom"])) {
												$ligne.=$tab_cours[$i]["enfant"]["prof_nom"];
											}
											if(isset($tab_cours[$i]["enfant"]["prof_prenom"])) {
												$ligne.=" ".$tab_cours[$i]["enfant"]["prof_prenom"];
											}
											$ligne.=";";
											if(isset($tab_cours[$i]["enfant"]["salle"])) {
												$ligne.=$tab_cours[$i]["enfant"]["salle"];
											}
											$ligne.=";";

											// Groupe
											$ligne.=";";

											// Regroup
											$ligne.=$tab_cours[$i]["enfant"]["classe"].";";

											// Eff
											if(isset($tab_cours[$i]["enfant"]["effectif"])) {
												$ligne.=$tab_cours[$i]["enfant"]["effectif"];
											}
											$ligne.=";";

											// Mo
											if(isset($tab_cours[$i]["enfant"]["mod."])) {
												$ligne.=$tab_cours[$i]["enfant"]["mod."];
											}
											$ligne.=";";

											// Freq
											//if(isset($tab_cours[$i]["enfant"]["frequence"])) {
											if((isset($tab_cours[$i]["enfant"]["frequence"]))&&(in_array($tab_cours[$i]["enfant"]["frequence"],$tab_sem))) {
												$ligne.=$tab_cours[$i]["enfant"]["frequence"];
											}
											$ligne.=";";

											// Aire
											$ligne.=";";
											$ligne.="\n";

											fwrite($fich, $ligne);
											if($debug_import=='y') {
												echo "<span style='color:blue'>$ligne</span><br />\n";
											}
											//echo $ligne."<br />";
											$nb_lignes++;
										}
									}
								}
								else {
									// Le groupe n'a été associé à aucune classe.
									// Ce peut être le cas (commode) pour des AID.

									$ligne=$tab_cours[$i]["enfant"]["jour"].";";

									$tmp_tab=explode("h", mb_strtolower($tab_cours[$i]["enfant"]["h.debut"]));
									//$heure=preg_replace("/^0*/","",$tmp_tab[0])+$loop;
									$heure=preg_replace("/^0*/","",$tmp_tab[0]);
									if($heure<10) {$heure="0".$heure;}
									$minute=$tmp_tab[1];
									$ligne.=$heure."h".$minute.";";


									//$ligne.=$tab_cours[$i]["enfant"]["classe"].";";
									//$ligne.=$tab_clas_grp[$tab_cours[$i]["enfant"]["classe"]][$j].";";
									$ligne.=";";
									$ligne.=$tab_cours[$i]["enfant"]["mat_code"].";";
									if(isset($tab_cours[$i]["enfant"]["prof_nom"])) {
										$ligne.=$tab_cours[$i]["enfant"]["prof_nom"];
									}
									if(isset($tab_cours[$i]["enfant"]["prof_prenom"])) {
										$ligne.=" ".$tab_cours[$i]["enfant"]["prof_prenom"];
									}
									$ligne.=";";
									if(isset($tab_cours[$i]["enfant"]["salle"])) {
										$ligne.=$tab_cours[$i]["enfant"]["salle"];
									}
									$ligne.=";";

									// Groupe
									$ligne.=";";

									// Regroup
									$ligne.=$tab_cours[$i]["enfant"]["classe"].";";

									// Eff
									if(isset($tab_cours[$i]["enfant"]["effectif"])) {
										$ligne.=$tab_cours[$i]["enfant"]["effectif"];
									}
									$ligne.=";";

									// Mo
									if(isset($tab_cours[$i]["enfant"]["mod."])) {
										$ligne.=$tab_cours[$i]["enfant"]["mod."];
									}
									$ligne.=";";

									// Freq
									//if(isset($tab_cours[$i]["enfant"]["frequence"])) {
									if((isset($tab_cours[$i]["enfant"]["frequence"]))&&(in_array($tab_cours[$i]["enfant"]["frequence"],$tab_sem))) {
										$ligne.=$tab_cours[$i]["enfant"]["frequence"];
									}
									$ligne.=";";

									// Aire
									$ligne.=";";
									$ligne.="\n";

									fwrite($fich, $ligne);
									if($debug_import=='y') {
										echo "<span style='color:blue'>$ligne</span><br />\n";
									}
									//echo $ligne."<br />";
									$nb_lignes++;

								}
							}
							else {
								// Cours associés à une classe (sans qu'un groupe soit déclaré)

								for($loop=0;$loop<$duree;$loop++) {

									$ligne=$tab_cours[$i]["enfant"]["jour"].";";

									$tmp_tab=explode("h", mb_strtolower($tab_cours[$i]["enfant"]["h.debut"]));
									$heure=preg_replace("/^0*/","",$tmp_tab[0])+$loop;
									if($heure<10) {$heure="0".$heure;}
									$minute=$tmp_tab[1];
									$ligne.=$heure."h".$minute.";";


									$ligne.=$tab_cours[$i]["enfant"]["classe"].";";
									$ligne.=$tab_cours[$i]["enfant"]["mat_code"].";";
									if(isset($tab_cours[$i]["enfant"]["prof_nom"])) {
										$ligne.=$tab_cours[$i]["enfant"]["prof_nom"];
									}
									if(isset($tab_cours[$i]["enfant"]["prof_prenom"])) {
										$ligne.=" ".$tab_cours[$i]["enfant"]["prof_prenom"];
									}
									$ligne.=";";
									if(isset($tab_cours[$i]["enfant"]["salle"])) {
										$ligne.=$tab_cours[$i]["enfant"]["salle"];
									}
									$ligne.=";";

									// Groupe
									$ligne.=";";

									// Regroup
									$ligne.=";";

									// Eff
									if(isset($tab_cours[$i]["enfant"]["effectif"])) {
										$ligne.=$tab_cours[$i]["enfant"]["effectif"];
									}
									$ligne.=";";

									// Mo
									if(isset($tab_cours[$i]["enfant"]["mod."])) {
										$ligne.=$tab_cours[$i]["enfant"]["mod."];
									}
									$ligne.=";";

									// Freq
									//if(isset($tab_cours[$i]["enfant"]["frequence"])) {
									if((isset($tab_cours[$i]["enfant"]["frequence"]))&&(in_array($tab_cours[$i]["enfant"]["frequence"],$tab_sem))) {
										$ligne.=$tab_cours[$i]["enfant"]["frequence"];
									}
									$ligne.=";";

									// Aire
									$ligne.=";";
									$ligne.="\n";

									fwrite($fich, $ligne);
									if($debug_import=='y') {
										echo "<span style='color:blue'>$ligne</span><br />\n";
									}

									$nb_lignes++;
								}
							}

							$nb_cours++;
						}

					}
				}
				echo "<p>$nb_lignes lignes pour $nb_cours cours trouvés.</p>";
				fclose($fich);
				echo "<p>Fichier CSV produit au format d'un export UDT&nbsp;: <a href='../temp/".$tempdir."/g_edt_2.csv'>g_edt_2.csv</a><br />Vous pouvez fournir ce fichier dans Gepi en choisissant un import de type UDT.</p>\n";
			}
		}
	}
	require("../lib/footer.inc.php");
?>
