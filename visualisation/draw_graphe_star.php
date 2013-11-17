<?php
/*
 $Id$
*/

	header("Content-type:image/png");

	//$rapport_imageString_imagettftext=5;
	$rapport_imageString_imagettftext=2;

	// On précise de ne pas traiter les données avec la fonction anti_inject
	$traite_anti_inject = 'no';
	// En quoi cela consiste-t-il?

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

	if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
	}

	$taille_max_police=10;

	$avec_moy_classe="y";
	if((isset($_GET['avec_moy_classe']))&&($_GET['avec_moy_classe']=="n")) {
		$avec_moy_classe="n";
	}

	if((($_SESSION['statut']=='eleve')&&(!getSettingAOui('GepiAccesBulletinSimpleColonneMoyClasseEleve')))||
	(($_SESSION['statut']=='responsable')&&(!getSettingAOui('GepiAccesBulletinSimpleColonneMoyClasseResp')))) {
		$avec_moy_classe="n";
	}

	if($avec_moy_classe=="n") {
		if(isset($_GET['seriemin'])) {unset($_GET['seriemin']);}
		if(isset($_GET['seriemax'])) {unset($_GET['seriemax']);}
	}

	// Récupération des valeurs:
	//$nb_data = $_GET['nb_data'];
	$nb_series= $_GET['nb_series'];
	if((mb_strlen(preg_replace("/[0-9]/","",$nb_series))!=0)||($nb_series=="")){
		exit;
	}

	//$eleves= $_GET['eleves'];
	$id_classe=$_GET['id_classe'];
	if((mb_strlen(preg_replace("/[0-9]/","",$id_classe))!=0)||($id_classe=="")){
		exit;
	}

	for($i=1;$i<=$nb_series;$i++){
		$mgen[$i]=isset($_GET['mgen'.$i]) ? $_GET['mgen'.$i] : "";
	}


	function writinfo($chemin,$type,$chaine){
		//$debug=1;
		$debug=0;
		if($debug==1){
			$fich=fopen($chemin,$type);
			fwrite($fich,$chaine);
			fclose($fich);
		}
	}

	//============================================
	writinfo('/tmp/infos_graphe.txt','w+',"Avant la récupération des moyennes.\n");

	// Récupération des moyennes:
	$moytmp=array();
	$moyenne=array();
	//$nb_series=$nb_data-1;
	//$nb_series=2;

	for($k=1;$k<=$nb_series;$k++){
		$moytmp[$k]=array();
		$moytmp[$k]=explode("|",$_GET['temp'.$k]);
		$moyenne[$k]=array();
		// On décale pour commencer à compter à 1:
		for($i=1;$i<=count($moytmp[$k]);$i++){
			$moyenne[$k][$i]=$moytmp[$k][$i-1];
			//fwrite($fich,"\$moyenne[$k][$i]=".$moyenne[$k][$i]."\n");
			// PROBLEME: en register_global=on, les 2ème, 3ème,... séries ne sont pas récupérées.
			//           On obtient juste moyenne[2][1]=- et rien après.
			writinfo('/tmp/infos_graphe.txt','a+',"\$moyenne[$k][$i]=".$moyenne[$k][$i]."\n");
		}
	}
	//============================================


	writinfo('/tmp/infos_graphe.txt','a+',"\n");

	$periode=isset($_GET['periode']) ? $_GET['periode'] : '';

	// Valeurs en dur, à modifier par la suite...
	//$largeurTotale=700;
	//$hauteurTotale=600;

	$largeurTotale=isset($_GET['largeur_graphe']) ? $_GET['largeur_graphe'] : '700';
	if((mb_strlen(preg_replace("/[0-9]/","",$largeurTotale))!=0)||($largeurTotale=="")){
		$largeurTotale=700;
	}
	$hauteurTotale=isset($_GET['hauteur_graphe']) ? $_GET['hauteur_graphe'] : '600';
	if((mb_strlen(preg_replace("/[0-9]/","",$hauteurTotale))!=0)||($hauteurTotale=="")){
		$hauteurTotale=600;
	}

	$tronquer_nom_court=isset($_GET['tronquer_nom_court']) ? $_GET['tronquer_nom_court'] : '0';
	writinfo('/tmp/infos_graphe.txt','a+',"\$tronquer_nom_court=$tronquer_nom_court\n");
	if((!ctype_digit($tronquer_nom_court))||($tronquer_nom_court<0)||($tronquer_nom_court>10)){
		$tronquer_nom_court=0;
	}
	writinfo('/tmp/infos_graphe.txt','a+',"\$tronquer_nom_court=$tronquer_nom_court\n");

	//settype($largeurTotale,'integer');
	//settype($hauteurTotale,'integer');

	// $taille_police de 1 à 6 -> 10
	//$taille_police=3;
	$taille_police=isset($_GET['taille_police']) ? $_GET['taille_police'] : '3';
	if((mb_strlen(preg_replace("/[0-9]/","",$taille_police))!=0)||($taille_police<1)||($taille_police>$taille_max_police)||($taille_police=="")){
		$taille_police=3;
	}

	if($taille_police>1){
		$taille_police_inf=$taille_police-1;
	}
	else{
		$taille_police_inf=$taille_police;
	}

	//$epaisseur_traits=2;
	$epaisseur_traits=isset($_GET['epaisseur_traits']) ? $_GET['epaisseur_traits'] : '2';
	if((mb_strlen(preg_replace("/[0-9]/","",$epaisseur_traits))!=0)||($epaisseur_traits<1)||($epaisseur_traits>6)||($epaisseur_traits=="")){
		$epaisseur_traits=2;
	}
	writinfo('/tmp/infos_graphe.txt','a+',"\$epaisseur_traits=$epaisseur_traits\n");

	$epaisseur_axes=2;
	$epaisseur_grad=1;


	writinfo('/tmp/infos_graphe.txt','a+',"\nAvant la récupération des matières.\n");

	$eleve=array();

	$legendy = array();

	//============================================
	// Récupération des matières:
	$mattmp=explode("|", $_GET['etiquette']);
	for($i=1;$i<=count($mattmp);$i++){
		$matiere[$i]=$mattmp[$i-1];

		if(!preg_match("/^[a-zA-Z_]{1}[a-zA-Z0-9_-]{1,19}$/", $matiere[$i])) {
			$matiere[$i]=preg_replace("/[^A-Za-z0-9_-]/", "",$matiere[$i]);
			$matiere_nom_long[$i]=$matiere[$i];
		}
		else {
			$call_matiere = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT nom_complet FROM matieres WHERE matiere = '".$matiere[$i]."'");
			if(mysqli_num_rows($call_matiere)>0) {
				$matiere_nom_long[$i] = mysql_result($call_matiere, "0", "nom_complet");
			}
			else {
				$matiere_nom_long[$i]=$matiere[$i];
			}
		}
		$matiere_nom_long[$i]=remplace_accents($matiere_nom_long[$i],'simple');

		writinfo('/tmp/infos_graphe.txt','a+',"\$matiere[$i]=".$matiere[$i]."\n");
		$matiere[$i]=remplace_accents($matiere[$i],'simple');
		writinfo('/tmp/infos_graphe.txt','a+',"\$matiere[$i]=".$matiere[$i]."\n");
	}

	writinfo('/tmp/infos_graphe.txt','a+',"\nAvant les titres...\n");
	$titre = unslashes($_GET['titre']);
	$k = 1;
	//while ($k < $nb_data) {
	//while ($k<=$nb_series) {
	for($k=1;$k<=2;$k++){
		if (isset($_GET['v_legend'.$k])) {
			$legendy[$k] = unslashes($_GET['v_legend'.$k]);
		} else {
			$legendy[$k]='' ;
		}
		// $eleve peut en fait être une moyenne de classe ou même un trimestre...
		$eleve[$k]=$legendy[$k];
		writinfo('/tmp/infos_graphe.txt','a+',"\$eleve[$k]=".$eleve[$k]."\n");
		//$k++;
	}
	//============================================


	$eleve1=$_GET['v_legend1'];
	$sql="SELECT * FROM eleves WHERE login='$eleve1'";
	$resultat_infos_eleve1=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($resultat_infos_eleve1)>0) {
		$ligne=mysqli_fetch_object($resultat_infos_eleve1);
		//$nom_eleve1=$ligne->nom." ".$ligne->prenom;
		$nom_eleve[1]=$ligne->nom." ".$ligne->prenom;
	}
	else {
		$nom_eleve[1]=$eleve1;
	}
	if($periode!=''){
		$nom_eleve[1]=$nom_eleve[1]." ($periode)";
	}
	$nom_eleve[1]=remplace_accents($nom_eleve[1],'simple');

	// Variable destinée à tenir compte de la moyenne annuelle...
	$nb_series_bis=$nb_series;
	if($legendy[2]=='Toutes_les_périodes'){
		$eleve2="";

		$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode";
		$result_periode=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		$nb_periode=mysqli_num_rows($result_periode);

		$cpt=1;
		while($lign_periode=mysqli_fetch_object($result_periode)){
			$nom_periode[$cpt]=$lign_periode->nom_periode;
			$nom_periode[$cpt]=remplace_accents($nom_periode[$cpt],'simple');
			$cpt++;
		}

		// Si la moyenne annuelle est demandée, on calcule:
		if(isset($_GET['affiche_moy_annuelle'])){
			writinfo('/tmp/infos_graphe.txt','a+',"\nAvant la moyenne annuelle...\n");

			// La moyenne annuelle amène une série de plus:
			$nb_series_bis++;

			$moy_annee=array();
			for($i=1;$i<=count($matiere);$i++){
				$cpt=0;
				$total_tmp[$i]=0;
				// Boucle sur les périodes...
				for($k=1;$k<=$nb_periode;$k++){
					

					writinfo('/tmp/infos_graphe.txt','a+',"mb_strlen(preg_replace(\"/[0-9.]/\",\"\",\$moyenne[".$k."][".$i."]))=mb_strlen(preg_replace(\"/[0-9.]/\",\"\",".$moyenne[$k][$i]."))=".mb_strlen(preg_replace("/[0-9\.]/","",$moyenne[$k][$i]))."\n");

					if(($moyenne[$k][$i]!='-')&&(mb_strlen(preg_replace("/[0-9\.]/","",$moyenne[$k][$i]))==0)&&($moyenne[$k][$i]!="")){
						$total_tmp[$i]=$total_tmp[$i]+$moyenne[$k][$i];
						$cpt++;
					}
				}
				if($cpt>0){
					$moy_annee[$i]=round($total_tmp[$i]/$cpt,1);
				}
				else{
					$moy_annee[$i]="-";
				}
				$moyenne[$nb_periode+1][$i]=$moy_annee[$i];
				$indice_per_suppl=$nb_periode+1;
				writinfo('/tmp/infos_graphe.txt','a+',"\$moyenne[".$indice_per_suppl."][$i]=".$moyenne[$indice_per_suppl][$i]."\n");
			}
		}
	}
	else{
		// Récupération des noms des élèves.
		$eleve2=$_GET['v_legend2'];
		switch($eleve2){
			case 'moyclasse':
					//$nom_eleve2="Moyennes de la classe";
					$nom_eleve[2]="Moyennes de la classe";
					if($avec_moy_classe=='n') {
						$nom_eleve[2]="";
					}
				break;
			case 'moymin':
					//$nom_eleve2="Moyennes minimales";
					$nom_eleve[2]="Moyennes minimales";
					if($avec_moy_classe=='n') {
						$nom_eleve[2]="";
					}
				break;
			case 'moymax':
					//$nom_eleve2="Moyennes maximales";
					$nom_eleve[2]="Moyennes maximales";
					if($avec_moy_classe=='n') {
						$nom_eleve[2]="";
					}
				break;
			case 'rang_eleve':
					$nom_eleve[2]="Rang élève";
					/*
					if($avec_moy_classe=='n') {
						$nom_eleve[2]="";
					}
					*/
				break;
			default:
				$sql="SELECT * FROM eleves WHERE login='$eleve2'";
				$resultat_infos_eleve2=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($resultat_infos_eleve2)>0) {
					$ligne=mysqli_fetch_object($resultat_infos_eleve2);
					//$nom_eleve2=$ligne->nom." ".$ligne->prenom;
					$nom_eleve[2]=$ligne->nom." ".$ligne->prenom;
				}
				else {
					$nom_eleve[2]=$eleve2;
				}
				break;
		}
		$nom_eleve[2]=remplace_accents($nom_eleve[2],'simple');
	}


	writinfo('/tmp/infos_graphe.txt','a+',"\nAvant seriemin, seriemax,...\n");

	// Récupération des moyennes minimales et maximales
	// si elles ont été transmises:
	if(isset($_GET['seriemin'])){
		$seriemin=$_GET['seriemin'];
		$moy_min_tmp=explode("|", $_GET['seriemin']);
		// On décale pour commencer à compter à 1:
		for($i=1;$i<=count($moy_min_tmp);$i++){
			$moy_min[$i]=$moy_min_tmp[$i-1];
			writinfo('/tmp/infos_graphe.txt','a+',"\$moy_min[$i]=".$moy_min[$i]."\n");
		}
	}

	if(isset($_GET['seriemax'])){
		$seriemax=$_GET['seriemax'];
		$moy_max_tmp=explode("|", $_GET['seriemax']);
		// On décale pour commencer à compter à 1:
		for($i=1;$i<=count($moy_max_tmp);$i++){
			$moy_max[$i]=$moy_max_tmp[$i-1];
			writinfo('/tmp/infos_graphe.txt','a+',"\$moy_max[$i]=".$moy_max[$i]."\n");
		}
	}



	//============================================
	//Création de l'image:
	$img=imageCreate($largeurTotale,$hauteurTotale);
	// Epaisseur initiale des traits...
	imagesetthickness($img,2);
	//============================================

	writinfo('/tmp/infos_graphe.txt','a+',"\nAprès imageCreate, imagethickness...\n");



	//============================================
	// A récupérer d'une table MySQL... d'après un choix de l'utilisateur...

	$tab=array('Fond','Bande_1','Bande_2','Axes','Eleve_1','Eleve_2','Moyenne_classe','Periode_1','Periode_2','Periode_3');
	$comp=array('R','V','B');

	$tabcouleurs=array();
	$tabcouleurs['Fond']=array();
	$tabcouleurs['Fond']['R']=255;
	$tabcouleurs['Fond']['V']=255;
	$tabcouleurs['Fond']['B']=255;

	$tabcouleurs['Bande_1']=array();
	$tabcouleurs['Bande_1']['R']=255;
	$tabcouleurs['Bande_1']['V']=255;
	$tabcouleurs['Bande_1']['B']=255;

	$tabcouleurs['Bande_2']=array();
	$tabcouleurs['Bande_2']['R']=255;
	$tabcouleurs['Bande_2']['V']=255;
	$tabcouleurs['Bande_2']['B']=133;

	$tabcouleurs['Axes']=array();
	$tabcouleurs['Axes']['R']=0;
	$tabcouleurs['Axes']['V']=0;
	$tabcouleurs['Axes']['B']=0;

	$tabcouleurs['Eleve_1']=array();
	$tabcouleurs['Eleve_1']['R']=0;
	$tabcouleurs['Eleve_1']['V']=100;
	$tabcouleurs['Eleve_1']['B']=255;

	$tabcouleurs['Eleve_2']=array();
	$tabcouleurs['Eleve_2']['R']=0;
	$tabcouleurs['Eleve_2']['V']=255;
	$tabcouleurs['Eleve_2']['B']=0;

	$tabcouleurs['Moyenne_classe']=array();
	$tabcouleurs['Moyenne_classe']['R']=100;
	$tabcouleurs['Moyenne_classe']['V']=100;
	$tabcouleurs['Moyenne_classe']['B']=100;

	$tabcouleurs['Periode_1']=array();
	$tabcouleurs['Periode_1']['R']=0;
	$tabcouleurs['Periode_1']['V']=100;
	$tabcouleurs['Periode_1']['B']=255;

	$tabcouleurs['Periode_2']=array();
	$tabcouleurs['Periode_2']['R']=255;
	$tabcouleurs['Periode_2']['V']=0;
	$tabcouleurs['Periode_2']['B']=0;

	$tabcouleurs['Periode_3']=array();
	$tabcouleurs['Periode_3']['R']=255;
	$tabcouleurs['Periode_3']['V']=0;
	$tabcouleurs['Periode_3']['B']=0;

	for($i=0;$i<count($tab);$i++){
		for($j=0;$j<count($comp);$j++){
			$sql="SELECT value FROM setting WHERE name='couleur_".$tab[$i]."_".$comp[$j]."'";
			$res_couleur=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($res_couleur)>0){
				$tmp=mysqli_fetch_object($res_couleur);
				$tabcouleurs[$tab[$i]][$comp[$j]]=$tmp->value;
			}
		}
		$couleur[$tab[$i]]=imageColorAllocate($img,$tabcouleurs[$tab[$i]]['R'],$tabcouleurs[$tab[$i]]['V'],$tabcouleurs[$tab[$i]]['B']);
	}

	$fond=$couleur['Fond'];
	$bande1=$couleur['Bande_1'];
	$bande2=$couleur['Bande_2'];
	$couleureleve[1]=$couleur['Eleve_1'];
	$couleureleve[2]=$couleur['Eleve_2'];

	$transp=$bande1;

	if($legendy[2]=='Toutes_les_périodes'){
		$couleureleve[1]=$couleur['Periode_1'];
		$couleureleve[2]=$couleur['Periode_2'];
		$couleureleve[3]=$couleur['Periode_3'];
	}

	$i=4;
	if(($legendy[2]=='Toutes_les_périodes')&&($nb_series>=4)){
		for($i=4;$i<=$nb_series;$i++){
			for($j=0;$j<count($comp);$j++){
				$sql="SELECT value FROM setting WHERE name='couleur_Periode_".$i."_".$comp[$j]."'";
				$res_couleur=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_couleur)>0){
					$tmp=mysqli_fetch_object($res_couleur);
					$tabcouleurs["Periode_".$i][$comp[$j]]=$tmp->value;
				}
				else{
					$tabcouleurs["Periode_".$i][$comp[$j]]=0;
				}
			}
			$couleur["Periode_".$i]=imageColorAllocate($img,$tabcouleurs["Periode_".$i]['R'],$tabcouleurs["Periode_".$i]['V'],$tabcouleurs["Periode_".$i]['B']);
			$couleureleve[$i]=$couleur["Periode_".$i];
		}
	}
	$couleurmoyenne=$couleur['Moyenne_classe'];
	$axes=$couleur['Axes'];

	// IL FAUT UNE COULEUR DE PLUS POUR LA MOYENNE ANNUELLE...
	$couleureleve[$i]=$couleur['Moyenne_classe'];

	//============================================


	// On force la couleur pour les moyennes classe/min/max
	if(($eleve2=='moyclasse')||($eleve2=='moymin')||($eleve2=='moymax')||($eleve2=='rang_eleve')){
		$couleureleve[2]=$couleurmoyenne;
	}



	//===========================================
	$nbMat=count($matiere);
	//===========================================




	//===========================================
	//===========================================

	// Rayon en pixels du cercle pour aller de 0 à 20:
	//$L=200;
	//$L=round(($hauteurTotale-3*(ImageFontHeight($taille_police)+5))/2);
	//$L=round(($hauteurTotale-4*(ImageFontHeight($taille_police)+5))/2);

	//$x0=round($largeurTotale/2);
	//$y0=round($hauteurTotale/2);
	$x0=round($largeurTotale/2);
	if($legendy[2]=='Toutes_les_périodes'){
		$L=round(($hauteurTotale-6*(ImageFontHeight($taille_police)+5))/2);
		//$y0=round(3*(ImageFontHeight($taille_police))+5)+$L;
		$y0=round(4*(ImageFontHeight($taille_police))+5)+$L;
	}
	else{
		$L=round(($hauteurTotale-4*(ImageFontHeight($taille_police)+5))/2);
		$y0=round(2*(ImageFontHeight($taille_police))+5)+$L;
	}

	writinfo('/tmp/infos_graphe.txt','a+',"=====================================\n");
	writinfo('/tmp/infos_graphe.txt','a+',"\$x0=$x0\n");
	writinfo('/tmp/infos_graphe.txt','a+',"\$y0=$y0\n");
	writinfo('/tmp/infos_graphe.txt','a+',"\$L=$L\n");

	// 20130523: Revoir le calcul de $L et le décalage du centre
	$graphe_star_decalage_y=getPref($_SESSION['login'],'graphe_star_decalage_y',"");
	if(
		($graphe_star_decalage_y=="")||
		($graphe_star_decalage_y=="-")||
		((!preg_match("/^[0-9]*$/",$graphe_star_decalage_y))&&
		(!preg_match("/^-[0-9]*$/",$graphe_star_decalage_y)))
	) {
		$graphe_star_decalage_y=getSettingValue('graphe_star_decalage_y');
		if((mb_strlen(preg_replace("/[0-9]/","",$graphe_star_decalage_y))!=0)||($graphe_star_decalage_y=="")) {$graphe_star_decalage_y=0;}
	}
	writinfo('/tmp/infos_graphe.txt','a+',"\$graphe_star_decalage_y=$graphe_star_decalage_y\n");

	$y0+=$graphe_star_decalage_y;

	$graphe_star_modif_rayon=getPref($_SESSION['login'],'graphe_star_modif_rayon',"");
	if(
		($graphe_star_modif_rayon=="")||
		($graphe_star_modif_rayon=="-")||
		((!preg_match("/^[0-9]*$/",$graphe_star_modif_rayon))&&
		(!preg_match("/^-[0-9]*$/",$graphe_star_modif_rayon)))
	) {
		$graphe_star_modif_rayon=getSettingValue('graphe_star_modif_rayon');
		if((mb_strlen(preg_replace("/[0-9]/","",$graphe_star_modif_rayon))!=0)||($graphe_star_modif_rayon=="")) {$graphe_star_modif_rayon=0;}
	}
	writinfo('/tmp/infos_graphe.txt','a+',"\$graphe_star_modif_rayon=$graphe_star_modif_rayon\n");
	$L+=$graphe_star_modif_rayon;


	writinfo('/tmp/infos_graphe.txt','a+',"\$x0=$x0\n");
	writinfo('/tmp/infos_graphe.txt','a+',"\$y0=$y0\n");
	writinfo('/tmp/infos_graphe.txt','a+',"\$L=$L\n");

	$pi=pi();


	function coordcirc($note,$angle) {
		// $note sur 20 (s'assurer qu'il y a le point pour séparateur et non la virgule)
		// $angle en degrés
		global $pi;
		global $L;
		global $x0;
		global $y0;

		$x=round($note*$L*cos($angle*$pi/180)/20)+$x0;
		$y=round($note*$L*sin($angle*$pi/180)/20)+$y0;

		return array($x,$y);
	}


	//=================================
	// Epaisseur des traits
	imagesetthickness($img,1);
	//=================================


	//=================================
	// Polygone 20/20
	unset($tab20);
	$tab20=array();
	for($i=0;$i<$nbMat;$i++){
		$angle=round($i*360/$nbMat);
		//writinfo('/tmp/infos_graphe.txt','a+',"\$angle=$angle\n");
		$tab=coordcirc(20,$angle);

		$tab20[]=$tab[0];
		$tab20[]=$tab[1];
	}
	ImageFilledPolygon($img,$tab20,count($tab20)/2,$bande2);
	//=================================


	//=================================
	// Polygone 15/20
	unset($tab15);
	$tab15=array();
	for($i=0;$i<$nbMat;$i++){
		$angle=round($i*360/$nbMat);
		//writinfo('/tmp/infos_graphe.txt','a+',"\$angle=$angle\n");
		$tab=coordcirc(15,$angle);

		$tab15[]=$tab[0];
		$tab15[]=$tab[1];
	}

	ImageFilledPolygon($img,$tab15,count($tab15)/2,$bande1);
	//=================================

	//=================================
	// Polygone 10/20
	unset($tab10);
	$tab10=array();
	for($i=0;$i<$nbMat;$i++){
		$angle=round($i*360/$nbMat);
		//writinfo('/tmp/infos_graphe.txt','a+',"\$angle=$angle\n");
		$tab=coordcirc(10,$angle);

		$tab10[]=$tab[0];
		$tab10[]=$tab[1];
	}

	ImageFilledPolygon($img,$tab10,count($tab10)/2,$bande2);
	//=================================

	//=================================
	// Polygone 5/20
	unset($tab5);
	$tab5=array();
	for($i=0;$i<$nbMat;$i++){
		$angle=round($i*360/$nbMat);
		//writinfo('/tmp/infos_graphe.txt','a+',"\$angle=$angle\n");
		$tab=coordcirc(5,$angle);

		$tab5[]=$tab[0];
		$tab5[]=$tab[1];
	}

	ImageFilledPolygon($img,$tab5,count($tab5)/2,$bande1);
	//=================================


	//=================================
	// Axes
	for($i=0;$i<count($tab20)/2;$i++){
		imageline ($img,$x0,$y0,$tab20[2*$i],$tab20[2*$i+1],$axes);
		if($i>0){
			imageline ($img,$tab20[2*($i-1)],$tab20[2*($i-1)+1],$tab20[2*$i],$tab20[2*$i+1],$axes);
		}
		else{
			//imageline ($img,$tab20[2*count($tab20)/2],$tab20[2*count($tab20)/2+1],$tab20[2*$i],$tab20[2*$i+1],$axes);
		}
	}
	imageline ($img,$tab20[0],$tab20[1],$tab20[2*($i-1)],$tab20[2*($i-1)+1],$axes);
	//=================================


	$afficher_pointille=mb_substr(getPref($_SESSION['login'], 'graphe_pointille',''),0,1);
	if($afficher_pointille=='') {
		$afficher_pointille=mb_substr(getSettingValue('graphe_pointille'),0,1);
	}
	writinfo('/tmp/infos_graphe.txt','a+',"\$afficher_pointille=$afficher_pointille\n");

	//Epaisseur des traits:
	imagesetthickness($img,$epaisseur_traits);

	//=================================
	// Tracé des courbes des séries
	for($k=1;$k<=$nb_series_bis;$k++){

		$style_pointille = Array(
		$couleureleve[$k],
		$couleureleve[$k],
		$couleureleve[$k],
		$couleureleve[$k],
		$couleureleve[$k],
		$couleureleve[$k],
		$couleureleve[$k],
		$couleureleve[$k],
		IMG_COLOR_TRANSPARENT,
		IMG_COLOR_TRANSPARENT,
		IMG_COLOR_TRANSPARENT,
		IMG_COLOR_TRANSPARENT,
		IMG_COLOR_TRANSPARENT,
		IMG_COLOR_TRANSPARENT,
		IMG_COLOR_TRANSPARENT,
		IMG_COLOR_TRANSPARENT
		);

		$style_plein = Array(
		$couleureleve[$k],
		$couleureleve[$k]
		);

		//if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
		/*
		if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')||
		((isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]!="Rang élève")))
		) {
		*/


/*
		if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
			$afficher_la_serie_courante="y";
			if(($k==2)&&(isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]=="Rang élève"))) {
				$afficher_la_serie_courante="n";
			}
			if(($avec_moy_classe=='n')&&($k>1)) {
				$afficher_la_serie_courante="n";
			}

			if($afficher_la_serie_courante=="y") {
*/
		if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
			$afficher_la_serie_courante="y";
			// Le test sur le rang ne concerne que la courbe, pas les nombres affichés sous la ligne matière
			if(($k==2)&&(isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]=="Rang élève"))) {
				$afficher_la_serie_courante="n";
			}
			if(($avec_moy_classe=='n')&&($k>1)&&(isset($eleve2))&&(($eleve2=='moyclasse')||($eleve2=='moymax')||($eleve2=='moymin'))) {
				$afficher_la_serie_courante="n";
			}

			if($afficher_la_serie_courante=="y") {

			/*
			if(($k!=2)||
			(($k==2)&&(isset($nom_eleve[2]))&&($nom_eleve[2]!="Rang eleve")&&($nom_eleve[2]!="Rang élève"))) {
			*/
				$xprec="";
				$yprec="";
				$temoin_prec="";

				// On place les points
				$tab_x=array();
				$tab_y=array();
				for($i=1;$i<$nbMat+1;$i++){
					if(($moyenne[$k][$i]!="")&&($moyenne[$k][$i]!="-")&&($moyenne[$k][$i]!="N.NOT")&&($moyenne[$k][$i]!="ABS")&&($moyenne[$k][$i]!="DIS")){

						$angle=round(($i-1)*360/$nbMat);
						$tab=coordcirc($moyenne[$k][$i],$angle);

						imageFilledRectangle($img,$tab[0]-2,$tab[1]-2,$tab[0]+2,$tab[1]+2,$couleureleve[$k]);

						$tab_x[]=$tab[0];
						$tab_y[]=$tab[1];
					}
					else{
						$tab_x[]="";
						$tab_y[]="";
					}
				}


				// On joint ces points
				$xprec="";
				$yprec="";
				for($i=0;$i<count($tab_x);$i++){
					if($i==0){
						if(($tab_x[$i]!="")&&($tab_x[count($tab_x)-1]!="")){
							imageline ($img,$tab_x[$i],$tab_y[$i],$tab_x[count($tab_x)-1],$tab_y[count($tab_y)-1],$couleureleve[$k]);
						}
					}

					if($tab_x[$i]!=""){
						if((isset($tab_x[$i+1]))&&($tab_x[$i+1]!="")) {
							imageline ($img,$tab_x[$i],$tab_y[$i],$tab_x[$i+1],$tab_y[$i+1],$couleureleve[$k]);
							writinfo('/tmp/infos_graphe.txt','a+',"\nUne ligne\n");
							writinfo('/tmp/infos_graphe.txt','a+',"\$tab_x[$i]=$tab_x[$i]\n");
							writinfo('/tmp/infos_graphe.txt','a+',"\$tab_y[$i]=$tab_y[$i]\n");
							writinfo('/tmp/infos_graphe.txt','a+',"\$tab_x[$i+1]=".$tab_x[$i+1]."\n");
							writinfo('/tmp/infos_graphe.txt','a+',"\$tab_y[$i+1]=".$tab_y[$i+1]."\n");
						}
						elseif(($afficher_pointille!='n')&&(isset($tab_y[$i]))&&($tab_y[$i]!="")&&(isset($tab_y[$i+2]))&&($tab_y[$i+2]!="")) {
							writinfo('/tmp/infos_graphe.txt','a+',"\nUne ligne pointillée\n");
							writinfo('/tmp/infos_graphe.txt','a+',"\$tab_x[$i]=$tab_x[$i]\n");
							writinfo('/tmp/infos_graphe.txt','a+',"\$tab_y[$i]=$tab_y[$i]\n");
							writinfo('/tmp/infos_graphe.txt','a+',"\$tab_x[$i+2]=".$tab_x[$i+2]."\n");
							writinfo('/tmp/infos_graphe.txt','a+',"\$tab_y[$i+2]=".$tab_y[$i+2]."\n");
							imagesetstyle($img, $style_pointille);
							imageLine($img,$tab_x[$i],$tab_y[$i],$tab_x[$i+2],$tab_y[$i+2],IMG_COLOR_STYLED);
							imagesetstyle($img, $style_plein);
							//imageLine($img,$tab_x[$i],$tab_y[$i],$tab_x[$i+2],$tab_y[$i+2],$couleureleve[$k]);
						}
					}
				}
			}
		}
	}
	//=================================


	//=================================
	// Légendes Matières:
	if(getSettingValue('graphe_star_txt')=='old_way') {
		for($i=0;$i<count($tab20)/2;$i++){
			$angle=round($i*360/$nbMat);

			writinfo('/tmp/infos_graphe.txt','a+',"\$angle=$angle\n");

			//$texte=$matiere[$i+1];
			$texte=$matiere_nom_long[$i+1];

			$tmp_taille_police=$taille_police;

			writinfo('/tmp/infos_graphe.txt','a+',"\n========================================\n\$texte=$texte\n\$largeurTotale=$largeurTotale\n\$angle=$angle\n");

			if($angle==0){
				$x=$tab20[2*$i]+5;

				$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police);
				writinfo('/tmp/infos_graphe.txt','a+',"\$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police)=".$x."+".mb_strlen($texte)."*".ImageFontWidth($taille_police)."=$x+".(mb_strlen($texte)*ImageFontWidth($taille_police))."=$x_verif\n");

				if($x_verif>$largeurTotale){
					for($j=$taille_police;$j>1;$j--){
						$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j);
						writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x_verif=$x_verif\n");
						if($x_verif<=$largeurTotale){
							break;
						}
					}
					if($x_verif>$largeurTotale){
						$j=1;
					}
					$tmp_taille_police=$j;
				}

				$y=round($tab20[2*$i+1]+$angle*(ImageFontHeight($taille_police)+2)/90);
			}
			elseif(($angle>0)&&($angle<90)){
				$x=$tab20[2*$i]+5;
				$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police);
				writinfo('/tmp/infos_graphe.txt','a+',"\$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police)=".$x."+".mb_strlen($texte)."*".ImageFontWidth($taille_police)."=$x+".(mb_strlen($texte)*ImageFontWidth($taille_police))."=$x_verif\n");

				if($x_verif>$largeurTotale){
					for($j=$taille_police;$j>1;$j--){
						$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j);
						writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j\n");
						writinfo('/tmp/infos_graphe.txt','a+',"\$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j)=".$x."+".mb_strlen($texte)."*".ImageFontWidth($j)."=$x+".(mb_strlen($texte)*ImageFontWidth($j))."=$x_verif\n");
						if($x_verif<=$largeurTotale){
							break;
						}
					}
					if($x_verif>$largeurTotale){
						$j=1;
					}
					$tmp_taille_police=$j;
				}

				$y=round($tab20[2*$i+1]+$angle*(ImageFontHeight($taille_police)+2)/90);
			}
			elseif($angle==90){
				$x=round($tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($taille_police)/2);
				$y=$tab20[2*$i+1]+ImageFontHeight($taille_police)+2;
			}
			elseif(($angle>90)&&($angle<180)){
				$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($taille_police)+5);

				if($x<0){
					for($j=$taille_police;$j>1;$j--){
						$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($j)+5);
						writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x=$x\n");
						if($x>=0){
							break;
						}
					}
					if($x<0){
						$x=1;
						$j=1;
					}
					$tmp_taille_police=$j;
				}

				$y=round($tab20[2*$i+1]+($angle-90)*(ImageFontHeight($taille_police)-2)/90);
			}
			elseif($angle==180){
				$x=$tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($taille_police)-5;

				if($x<0){
					for($j=$taille_police;$j>1;$j--){
						$x=$tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($j)-5;
						writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x=$x\n");
						if($x>=0){
							break;
						}
					}
					if($x<0){
						$x=1;
						$j=1;
					}
					$tmp_taille_police=$j;
				}

				$y=round($tab20[2*$i+1]-ImageFontHeight($taille_police)/2);
			}
			elseif(($angle>180)&&($angle<270)){
				$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($taille_police)+5);

				if($x<0){
					for($j=$taille_police;$j>1;$j--){
						$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($j)+5);
						writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x=$x\n");
						if($x>=0){
							break;
						}
					}
					if($x<0){
						$x=1;
						$j=1;
					}
					$tmp_taille_police=$j;
				}

				$y=round($tab20[2*$i+1]-($angle-180)*(ImageFontHeight($taille_police)-2)/90);
			}
			elseif($angle==270){
				$x=round($tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($taille_police)/2);
				//$y=$tab20[2*$i+1]-ImageFontHeight($taille_police)-2;
				$y=$tab20[2*$i+1]-2*ImageFontHeight($taille_police)-2;
			}
			else{
				$x=$tab20[2*$i]+5;
				$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police);

				if($x_verif>$largeurTotale){
					for($j=$taille_police;$j>1;$j--){
						$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j);
						writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x_verif=$x_verif\n");
						if($x_verif<=$largeurTotale){
							break;
						}
					}
					if($x_verif>$largeurTotale){
						$j=1;
					}
					$tmp_taille_police=$j;
				}

				$y=round($tab20[2*$i+1]-(90-($angle-270))*(ImageFontHeight($taille_police)-2)/90);
			}

			writinfo('/tmp/infos_graphe.txt','a+',"\$x=$x\n");
			writinfo('/tmp/infos_graphe.txt','a+',"\$y=$y\n");
			writinfo('/tmp/infos_graphe.txt','a+',"\$tmp_taille_police=$tmp_taille_police\n");
			writinfo('/tmp/infos_graphe.txt','a+',"\$rapport_imageString_imagettftext=$rapport_imageString_imagettftext\n");

			imagettftext($img, $tmp_taille_police*$rapport_imageString_imagettftext, 0, $x, $y, $axes, dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", strtr($texte,"_"," "));


			// Ajout des notes sous le nom de matière:
			$ytmp=$y+2+ImageFontHeight($taille_police);
			//**************
			// A FAIRE:
			// Correctif à arranger... pour positionner au mieux en fonction de l'angle
			if(($angle>270)&&($angle<360)){$xtmp=$x+30;}else{$xtmp=$x;}
			//**************
			for($k=1;$k<=$nb_series_bis;$k++){
				//if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
				/*
				if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')||
				((isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]!="Rang élève")))
				) {
				*/

				$afficher_la_serie_courante="y";
				if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
					$afficher_la_serie_courante="y";
				}
				/*
				// Le test sur le rang ne concerne que la courbe, pas les nombres affichés sous la ligne matière
				if(($k==2)&&(isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]=="Rang élève"))) {
					$afficher_la_serie_courante="n";
				}
				*/
				if(($avec_moy_classe=='n')&&($k>1)&&(isset($eleve2))&&(($eleve2=='moyclasse')||($eleve2=='moymax')||($eleve2=='moymin'))) {
					$afficher_la_serie_courante="n";
				}

				if($afficher_la_serie_courante=="y") {

					if(($k!=2)||((isset($nom_eleve[2]))&&($nom_eleve[2]!="Rang eleve")&&($nom_eleve[2]!="Rang élève"))) {$texte_courant=nf($moyenne[$k][$i+1]);} else {$texte_courant=$moyenne[$k][$i+1];}

					imagettftext($img, $tmp_taille_police*$rapport_imageString_imagettftext, 0, $xtmp, $ytmp, $couleureleve[$k], dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", $texte_courant);
					$xtmp=$xtmp+mb_strlen($texte_courant." ")*ImageFontWidth($taille_police_inf);
				}
			}
		}
	}
	else {
		for($i=0;$i<count($tab20)/2;$i++){
			$angle=round($i*360/$nbMat);

			writinfo('/tmp/infos_graphe.txt','a+',"\$angle=$angle\n");

			//$texte=$matiere[$i+1];
			$texte=$matiere_nom_long[$i+1];

			$tmp_taille_police=$taille_police;

			writinfo('/tmp/infos_graphe.txt','a+',"\n========================================\n\$texte=$texte\n\$largeurTotale=$largeurTotale\n\$angle=$angle\n");

			if($angle==0){
				$x=$tab20[2*$i]+5;

				//$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police);
				//writinfo('/tmp/infos_graphe.txt','a+',"\$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police)=".$x."+".mb_strlen($texte)."*".ImageFontWidth($taille_police)."=$x+".(mb_strlen($texte)*ImageFontWidth($taille_police))."=$x_verif\n");

				$box=imagettfbbox ($taille_police*$rapport_imageString_imagettftext , 0 , dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf" , $texte );
				$x_verif=$x+abs($box[4]-$box[0]);

				if($x_verif>$largeurTotale){
					for($j=$taille_police;$j>1;$j--){
						//$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j);
						$box=imagettfbbox ($taille_police*$rapport_imageString_imagettftext , 0 , dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf" , $texte );
						$x_verif=$x+abs($box[4]-$box[0]);
						writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x_verif=$x_verif\n");
						if($x_verif<=$largeurTotale){
							break;
						}
					}
					if($x_verif>$largeurTotale){
						$j=1;
					}
					$tmp_taille_police=$j;
				}

				$y=round($tab20[2*$i+1]+$angle*(ImageFontHeight($taille_police)+2)/90);
			}
			elseif(($angle>0)&&($angle<90)){
				$x=$tab20[2*$i]+5;
				//$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police);
				//writinfo('/tmp/infos_graphe.txt','a+',"\$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police)=".$x."+".mb_strlen($texte)."*".ImageFontWidth($taille_police)."=$x+".(mb_strlen($texte)*ImageFontWidth($taille_police))."=$x_verif\n");

				$box=imagettfbbox ($taille_police*$rapport_imageString_imagettftext , 0 , dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf" , $texte );
				$x_verif=$x+abs($box[4]-$box[0]);
				//writinfo('/tmp/infos_graphe.txt','a+',"Conteneur: xbg=".$box[0]." ybg=".$box[1]." et xhd=".$box[4]." yhd=".$box[5]."\n");

				if($x_verif>$largeurTotale){
					for($j=$taille_police;$j>1;$j--){
						$box=imagettfbbox ($j*$rapport_imageString_imagettftext , 0 , dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf" , $texte );
						$x_verif=$x+abs($box[4]-$box[0]);
						writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x_verif=$x_verif\n");
						//$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j);
						//writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j\n");
						//writinfo('/tmp/infos_graphe.txt','a+',"\$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j)=".$x."+".mb_strlen($texte)."*".ImageFontWidth($j)."=$x+".(mb_strlen($texte)*ImageFontWidth($j))."=$x_verif\n");
						if($x_verif<=$largeurTotale){
							break;
						}
					}
					if($x_verif>$largeurTotale){
						$j=1;
					}
					$tmp_taille_police=$j;
				}

				//if($tmp_taille_police==1) {$tmp_taille_police=4;}

				$y=round($tab20[2*$i+1]+$angle*(ImageFontHeight($taille_police)+2)/90);
			}
			elseif($angle==90){
				$x=round($tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($taille_police)/2);
				$y=$tab20[2*$i+1]+ImageFontHeight($taille_police)+2;
			}
			elseif(($angle>90)&&($angle<180)){
				//$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($taille_police)+5);

				$box=imagettfbbox ($taille_police*$rapport_imageString_imagettftext , 0 , dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf" , $texte );
				$largeur_box=abs($box[4]-$box[0]);
				$x=$tab20[2*$i]-$largeur_box-5;

				if($x<0){
					for($j=$taille_police;$j>1;$j--){
						//$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($j)+5);

						$box=imagettfbbox ($j*$rapport_imageString_imagettftext , 0 , dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf" , $texte );
						$largeur_box=abs($box[4]-$box[0]);
						$x=$tab20[2*$i]-$largeur_box-5;

						writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x=$x\n");
						if($x>=0){
							break;
						}
					}
					if($x<0){
						$x=1;
						$j=1;
					}
					$tmp_taille_police=$j;
				}

				$y=round($tab20[2*$i+1]+($angle-90)*(ImageFontHeight($taille_police)-2)/90);
			}
			elseif($angle==180){
				//$x=$tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($taille_police)-5;

				$box=imagettfbbox ($taille_police*$rapport_imageString_imagettftext , 0 , dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf" , $texte );
				$largeur_box=abs($box[4]-$box[0]);
				$x=$tab20[2*$i]-$largeur_box-5;

				if($x<0){
					for($j=$taille_police;$j>1;$j--){
						//$x=$tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($j)-5;

						$box=imagettfbbox ($j*$rapport_imageString_imagettftext , 0 , dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf" , $texte );
						$largeur_box=abs($box[4]-$box[0]);
						$x=$tab20[2*$i]-$largeur_box-5;

						writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x=$x\n");
						if($x>=0){
							break;
						}
					}
					if($x<0){
						$x=1;
						$j=1;
					}
					$tmp_taille_police=$j;
				}

				$y=round($tab20[2*$i+1]-ImageFontHeight($taille_police)/2);
			}
			elseif(($angle>180)&&($angle<270)){
				//$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($taille_police)+5);

				$box=imagettfbbox ($taille_police*$rapport_imageString_imagettftext , 0 , dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf" , $texte );
				$largeur_box=abs($box[4]-$box[0]);
				$x=$tab20[2*$i]-$largeur_box-5;

				if($x<0){
					for($j=$taille_police;$j>1;$j--){
						//$x=$tab20[2*$i]-(mb_strlen($texte)*ImageFontWidth($j)+5);

						$box=imagettfbbox ($j*$rapport_imageString_imagettftext , 0 , dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf" , $texte );
						$largeur_box=abs($box[4]-$box[0]);
						$x=$tab20[2*$i]-$largeur_box-5;

						writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x=$x\n");
						if($x>=0){
							break;
						}
					}
					if($x<0){
						$x=1;
						$j=1;
					}
					$tmp_taille_police=$j;
				}

				$y=round($tab20[2*$i+1]-($angle-180)*(ImageFontHeight($taille_police)-2)/90);
			}
			elseif($angle==270){
				$x=round($tab20[2*$i]-mb_strlen($texte)*ImageFontWidth($taille_police)/2);
				//$y=$tab20[2*$i+1]-ImageFontHeight($taille_police)-2;
				$y=$tab20[2*$i+1]-2*ImageFontHeight($taille_police)-2;
			}
			else{
				$x=$tab20[2*$i]+5;
				//$x_verif=$x+mb_strlen($texte)*ImageFontWidth($taille_police);

				$box=imagettfbbox ($taille_police*$rapport_imageString_imagettftext , 0 , dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf" , $texte );
				$largeur_box=abs($box[4]-$box[0]);
				$x_verif=$x+$largeur_box;

				if($x_verif>$largeurTotale){
					for($j=$taille_police;$j>1;$j--){
						//$x_verif=$x+mb_strlen($texte)*ImageFontWidth($j);

						$box=imagettfbbox ($j*$rapport_imageString_imagettftext , 0 , dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf" , $texte );
						$largeur_box=abs($box[4]-$box[0]);
						$x_verif=$x+$largeur_box;
						writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x_verif=$x_verif\n");
						if($x_verif<=$largeurTotale){
							break;
						}
					}
					if($x_verif>$largeurTotale){
						$j=1;
					}
					$tmp_taille_police=$j;
				}

				$y=round($tab20[2*$i+1]-(90-($angle-270))*(ImageFontHeight($taille_police)-2)/90);
			}

			writinfo('/tmp/infos_graphe.txt','a+',"\$x=$x\n");
			writinfo('/tmp/infos_graphe.txt','a+',"\$y=$y\n");
			writinfo('/tmp/infos_graphe.txt','a+',"\$tmp_taille_police=$tmp_taille_police\n");
			writinfo('/tmp/infos_graphe.txt','a+',"\$rapport_imageString_imagettftext=$rapport_imageString_imagettftext\n");

			imagettftext($img, $tmp_taille_police*$rapport_imageString_imagettftext, 0, $x, $y, $axes, dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", strtr($texte,"_"," "));



			// Ajout des notes sous le nom de matière:
			$ytmp=$y+2+ImageFontHeight($taille_police);
			//**************
			// A FAIRE:
			// Correctif à arranger... pour positionner au mieux en fonction de l'angle
			if(($angle>270)&&($angle<360)){$xtmp=$x+30;}else{$xtmp=$x;}
			//**************
			for($k=1;$k<=$nb_series_bis;$k++){
				//if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
				/*
				if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')||
				((isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]!="Rang élève")))
				) {
				*/

				$afficher_la_serie_courante="y";
				if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
					$afficher_la_serie_courante="y";
				}

				/*
				// Le test sur le rang ne concerne que la courbe, pas les nombres affichés sous la ligne matière
				if(($k==2)&&(isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]=="Rang élève"))) {
					$afficher_la_serie_courante="n";
				}
				*/
				if(($avec_moy_classe=='n')&&($k>1)&&(isset($eleve2))&&(($eleve2=='moyclasse')||($eleve2=='moymax')||($eleve2=='moymin'))) {
					$afficher_la_serie_courante="n";
				}

				if($afficher_la_serie_courante=="y") {

					if(($k!=2)||((isset($nom_eleve[2]))&&($nom_eleve[2]!="Rang eleve")&&($nom_eleve[2]!="Rang élève"))) {$texte_courant=nf($moyenne[$k][$i+1]);} else {$texte_courant=$moyenne[$k][$i+1];}

					imagettftext($img, $tmp_taille_police*$rapport_imageString_imagettftext, 0, $xtmp, $ytmp, $couleureleve[$k], dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", $texte_courant);
					$xtmp=$xtmp+mb_strlen($texte_courant." ")*ImageFontWidth($taille_police_inf);
				}
			}
		}
	}
	//=================================



	//=================================
	// Titre de l'image,...
	if($legendy[2]=='Toutes_les_périodes'){
		$chaine=$nom_periode;
	}
	else{
		//$chaine=$eleve;
		$chaine=$nom_eleve;
	}

	// Calcul de la largeur occupée par les noms d'élèves:
	//$total_largeur_eleves=0;
	$total_largeur_chaines=0;
	//for($k=1;$k<$nb_data;$k++){
	for($k=1;$k<=$nb_series;$k++){
		//if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
		/*
		if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')||
		((isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]!="Rang élève")))
		) {
		*/
		if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
			$afficher_la_serie_courante="y";
		}
		/*
		// Le test sur le rang ne concerne que la courbe, pas les nombres affichés sous la ligne matière
		if(($k==2)&&(isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]=="Rang élève"))) {
			$afficher_la_serie_courante="n";
		}
		*/
		if(($avec_moy_classe=='n')&&($k>1)&&(isset($eleve2))&&(($eleve2=='moyclasse')||($eleve2=='moymax')||($eleve2=='moymin'))) {
			$afficher_la_serie_courante="n";
		}

		if($afficher_la_serie_courante=="y") {

			if($mgen[$k]!="") {
				$chaine_mgen=" (".nf($mgen[$k]).")";
			}
			else {
				$chaine_mgen="";
			}
			$largeur_chaine[$k] = mb_strlen($chaine[$k].$chaine_mgen) * ImageFontWidth($taille_police);

			$total_largeur_chaines=$total_largeur_chaines+$largeur_chaine[$k];
		}
	}

	// Calcul de l'espace entre ces noms d'élèves:
	// Espace équilibré comme suit:
	//     espace|Eleve1|espace|Eleve2|espace
	// Il faudrait être sûr que l'espace ne va pas devenir négatif...
	//$espace=($largeur-$total_largeur_eleves)/($nb_series+1);
	//$espace=($largeur-$total_largeur_chaines)/($nb_series+1);
	$espace=($largeurTotale-$total_largeur_chaines)/($nb_series+1);


	if($legendy[2]=='Toutes_les_périodes'){
		$chaine=$nom_periode;

		//imagettftext($img, $tmp_taille_police*$rapport_imageString_imagettftext, 0, round(($largeurTotale-mb_strlen($nom_eleve[1]) * ImageFontWidth($taille_police))/2), 5, $axes, dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", $nom_eleve[1]);
		$y_texte_courant=$tmp_taille_police*$rapport_imageString_imagettftext+2;
		imagettftext($img, $tmp_taille_police*$rapport_imageString_imagettftext, 0, round(($largeurTotale-mb_strlen($nom_eleve[1]) * ImageFontWidth($taille_police))/2), $y_texte_courant, $axes, dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", $nom_eleve[1]);

		// Positionnement des noms d'élèves:
		//$xtmp=$largeurGrad;
		$xtmp=0;
		//for($k=1;$k<$nb_data;$k++){
		for($k=1;$k<=$nb_series;$k++){
			$xtmp=$xtmp+$espace;
			if($mgen[$k]!="") {
				$chaine_mgen=" (".nf($mgen[$k]).")";
			}
			else {
				$chaine_mgen="";
			}
			//imagettftext($img, $tmp_taille_police*$rapport_imageString_imagettftext, 0, $xtmp, ImageFontHeight($taille_police)+5, $couleureleve[$k], dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", strtr($chaine[$k],"_"," ").$chaine_mgen);
			$y_texte_courant=$tmp_taille_police*$rapport_imageString_imagettftext+2+ImageFontHeight($taille_police)+2;
			imagettftext($img, $tmp_taille_police*$rapport_imageString_imagettftext, 0, $xtmp, $y_texte_courant, $couleureleve[$k], dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", strtr($chaine[$k],"_"," ").$chaine_mgen);

			$xtmp=$xtmp+$largeur_chaine[$k];
		}

	}
	else{
		//$chaine=$eleve;
		$chaine=$nom_eleve;

		// Positionnement des noms d'élèves:
		//$xtmp=$largeurGrad;
		$xtmp=0;
		//for($k=1;$k<$nb_data;$k++){
		for($k=1;$k<=$nb_series;$k++){
			//if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
			/*
			if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')||
			((isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]!="Rang élève")))
			) {
			*/

			if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
				$afficher_la_serie_courante="y";
			}
			/*
			// Le test sur le rang ne concerne que la courbe, pas les nombres affichés sous la ligne matière
			if(($k==2)&&(isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]=="Rang élève"))) {
				$afficher_la_serie_courante="n";
			}
			*/
			if(($avec_moy_classe=='n')&&($k>1)&&(isset($eleve2))&&(($eleve2=='moyclasse')||($eleve2=='moymax')||($eleve2=='moymin'))) {
				$afficher_la_serie_courante="n";
			}

			if($afficher_la_serie_courante=="y") {

				$xtmp=$xtmp+$espace;
				if($mgen[$k]!="") {
					if(($k!=2)||((isset($nom_eleve[2]))&&($nom_eleve[2]!="Rang eleve")&&($nom_eleve[2]!="Rang élève"))) {$texte_courant=nf($mgen[$k]);} else {$texte_courant=$mgen[$k];}
					$chaine_mgen=" (".$texte_courant.")";
				}
				else {
					$chaine_mgen="";
				}
				imagettftext($img, $tmp_taille_police*$rapport_imageString_imagettftext, 0, $xtmp, 12, $couleureleve[$k], dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", strtr($chaine[$k],"_"," ").$chaine_mgen);
				$xtmp=$xtmp+$largeur_chaine[$k];
			}
		}
	}
	//=================================


	imagePNG($img);

	imageDestroy($img);
	exit();
?>
