<?php
	// On précise de ne pas traiter les données avec la fonction anti_inject
	$traite_anti_inject = 'no';

	//$rapport_imageString_imagettftext=4;
	$rapport_imageString_imagettftext=2;

	// Initialisations files
	require_once("../lib/initialisations.inc.php");
	//send_file_download_headers("Content-type:image/png", "image.png", "inline");

	/*
	$chemin="/tmp/infos_session_graphe.txt";
	$type="a+";
	$fich=fopen($chemin,$type);
	$chaine="\n".strftime("%Y%m%d %H%M%S").": Dans draw_graphe.php on recupere pour ".$_SESSION['login']." connecté\n";
	fwrite($fich,$chaine);
	fclose($fich);
	*/
	// Resume session
	$resultat_session = $session_gepi->security_check();
	/*
	$fich=fopen($chemin,$type);
	$chaine=strftime("%Y%m%d %H%M%S").": \$resultat_session=".$resultat_session."\n";
	fwrite($fich,$chaine);
	fclose($fich);
	*/
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

	header("Content-type:image/png");

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

	writinfo('/tmp/infos_graphe.txt','a+',"\n\$taille_police=$taille_police<br />\n");

	//$epaisseur_traits=2;
	$epaisseur_traits=isset($_GET['epaisseur_traits']) ? $_GET['epaisseur_traits'] : '2';
	if((mb_strlen(preg_replace("/[0-9]/","",$epaisseur_traits))!=0)||($epaisseur_traits<1)||($epaisseur_traits>6)||($epaisseur_traits=="")){
		$epaisseur_traits=2;
	}

	$epaisseur_croissante_traits_periodes=isset($_GET['epaisseur_croissante_traits_periodes']) ? $_GET['epaisseur_croissante_traits_periodes'] : 'non';
	if(($epaisseur_croissante_traits_periodes!='non')&&($epaisseur_croissante_traits_periodes!='oui')) {$epaisseur_croissante_traits_periodes="non";}

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
			$call_matiere = mysqli_query($GLOBALS["mysqli"], "SELECT nom_complet FROM matieres WHERE matiere = '".$matiere[$i]."'");
			if(mysqli_num_rows($call_matiere)>0) {
				$matiere_nom_long[$i] = old_mysql_result($call_matiere, "0", "nom_complet");
			}
			else {
				$matiere_nom_long[$i]=$matiere[$i];
			}
		}
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
	$eleve1=preg_replace("/[^A-Za-z0-9_.-]/", "", $eleve1);
	$sql="SELECT * FROM eleves WHERE login='$eleve1';";
	$resultat_infos_eleve1=mysqli_query($GLOBALS["mysqli"], $sql);
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
		$result_periode=mysqli_query($GLOBALS["mysqli"], $sql);
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


					writinfo('/tmp/infos_graphe.txt','a+',"mb_strlen(preg_replace(\"/[0-9\.]/\",\"\",\$moyenne[".$k."][".$i."]))=mb_strlen(preg_replace(\"/[0-9\.]/\",\"\",".$moyenne[$k][$i]."))=".mb_strlen(preg_replace("/[0-9\.]/","",$moyenne[$k][$i]))."\n");

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
				$eleve2=preg_replace("/[^A-Za-z0-9_.-]/", "", $eleve2);
				$sql="SELECT * FROM eleves WHERE login='$eleve2'";
				$resultat_infos_eleve2=mysqli_query($GLOBALS["mysqli"], $sql);
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
	$largeurGrad=50;
	//$largeurBandeDroite=50;
	$largeurBandeDroite=80;
	$largeur=$largeurTotale-$largeurGrad-$largeurBandeDroite;

	// Hauteur en haut pour les intitulés de matières et moyennes:
	//$hauteurMoy=50;
	//$hauteurMoy=5+($nb_series+1)*15;
	// On met en haut les noms d'élèves aussi: -> +15
	//$hauteurMoy=70;
	$hauteurMoy=5+($nb_series+2)*15;
	if(($legendy[2]=='Toutes_les_périodes')&&(isset($_GET['affiche_moy_annuelle']))){
		$hauteurMoy=$hauteurMoy+15;
	}
	// Hauteur en bas pour les noms longs de matières
	//$hauteurMat=50;
	//$hauteurMat=5+15+15;
	$hauteurMat=0;
	for($i=1;$i<count($matiere_nom_long);$i++){
		$largeur_texte_long = mb_strlen($matiere_nom_long[$i]) * ImageFontWidth($taille_police);
		if($hauteurMat<$largeur_texte_long){
			$hauteurMat=$largeur_texte_long;
		}
	}
	// Avec l'affichage à 30°, on peut réduire.
	$hauteurMat=round($hauteurMat/2);
	$hauteurMat=$hauteurMat+10;

	$hauteur=$hauteurTotale-($hauteurMoy+$hauteurMat);
	//============================================




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
	$tabcouleurs['Periode_3']['R']=0;
	$tabcouleurs['Periode_3']['V']=200;
	$tabcouleurs['Periode_3']['B']=0;

	for($i=0;$i<count($tab);$i++){
		for($j=0;$j<count($comp);$j++){
			$sql="SELECT value FROM setting WHERE name='couleur_".$tab[$i]."_".$comp[$j]."'";
			$res_couleur=mysqli_query($GLOBALS["mysqli"], $sql);
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
				$res_couleur=mysqli_query($GLOBALS["mysqli"], $sql);
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
	//$nbMat=count($titre);

	//Largeur de chaque colonne "matière":
	$largeurMat=round($largeur/$nbMat);
	//$_SESSION['graphe_largeurMat']=$largeurMat;

	//$_SESSION['graphe_x0']=$largeurGrad;
	// ZUT! Je ne récupère pas la variable...
	//===========================================




	//===========================================
	if((!isset($seriemin))||(!isset($seriemax))){
		//Bandes verticales alternées:
		for($i=1;$i<$nbMat+1;$i++){
			$x1=round($largeurGrad+($i-1)*$largeurMat);
			$x2=round($largeurGrad+$i*$largeurMat);
			if($i-2*Floor($i/2)==0){
				imageFilledRectangle($img,$x1,$hauteurMoy,$x2,$hauteur+$hauteurMoy,$bande1);
			}
			else{
				imageFilledRectangle($img,$x1,$hauteurMoy,$x2,$hauteur+$hauteurMoy,$bande2);
			}

		}
	}
	else{
		// Ou affichage des bandes min-max
		for($i=1;$i<$nbMat+1;$i++){
			// Les +2 et -2 servent à laisser un jour entre les bandes pour une meilleure lisibilité
			$x1=round($largeurGrad+($i-1)*$largeurMat)+2;
			$x2=round($largeurGrad+$i*$largeurMat)-2;
			$ordonneemin=round($hauteurMoy+$hauteur-$moy_min[$i]*$hauteur/20);
			$ordonneemax=round($hauteurMoy+$hauteur-$moy_max[$i]*$hauteur/20);
			//Note: Il faut veiller à ce que la bande2 ressorte sur le fond!
			imageFilledRectangle($img,$x1,$ordonneemax,$x2,$ordonneemin,$bande2);
		}
	}
	//===========================================




	//=============================================================================
	//Tracé des graduations et des axes:
	//Graduations:
	$pas=2; //Prendre un diviseur non nul de 20.
	for($i=0;$i<21;$i=$i+$pas){

		//Epaisseur des graduations:
		imagesetthickness($img,$epaisseur_grad);

		$x1=$largeurGrad-5;
		$x2=$largeurGrad+5;
		//$yg=round($hauteurMoy+$hauteur-$i*($hauteur/(20/$pas)));
		$yg=round($hauteurMoy+$hauteur-$i*($hauteur/20));
		imageLine($img,$x1,$yg,$x2,$yg,$axes);
		imagettftext($img, $taille_police*$rapport_imageString_imagettftext, 0, $x1-20, $yg-10, $axes, dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", $i);
		

		//imagedashedline($img,$largeurGrad,$yg,$largeur+$largeurGrad,$yg,$axes);

		//$style = array ($axes,$axes,$axes,$axes,$axes,$fond,$fond,$fond,$fond,$fond);
		$style = array ($axes,$axes,$axes,$axes,$axes,$axes,$axes,$axes,$axes,$axes,$transp,$transp,$transp,$transp,$transp,$transp,$transp,$transp,$transp,$transp);
		imagesetstyle ($img, $style);
		imageline ($img,$largeurGrad,$yg,$largeur+$largeurGrad,$yg, IMG_COLOR_STYLED);

		//imageline ($img,$largeurGrad,$yg,$largeur+$largeurGrad,$yg, $axes);

	}



	//Epaisseur des axes:
	imagesetthickness($img,$epaisseur_axes);

	//Axe des abscisses:
	imageLine($img,$largeurGrad,$hauteurMoy+$hauteur,round($largeur+$largeurGrad+$largeurBandeDroite/2),$hauteurMoy+$hauteur,$axes);

	//Axe des ordonnées:
	imageLine($img,$largeurGrad,round($hauteurMoy/2),$largeurGrad,$hauteur+$hauteurMoy,$axes);

	//Barre de la moyenne:
	imageLine($img,$largeurGrad,round($hauteurMoy+$hauteur/2),round($largeur+$largeurGrad+$largeurBandeDroite/2),round($hauteurMoy+$hauteur/2),$axes);

	//imagedashedline pour pointillés
	//imagedashedline($img,5,5,100,100,$axes);
	//imageline($img,5,5,100,100,$axes);
	//==============================================================================
















	//=============================================================================
	// Préparation des abscisses et affichage des noms de matières et valeurs des moyennes:

	//Epaisseur des traits:
	imagesetthickness($img,$epaisseur_traits);

	//imageLine($img,100,100,200,300,$couleureleve1);
	//imageLine($img,100,round($moyenne[2]*10),200,300,$couleureleve1);
	//imageDashedLine($img,100,100,200,300,$noir);
	//imagedashedline($img,100,100,200,300,$noir);

	//===================================================================================
	//Tableau des valeurs centrales de chaque bande:
	$x=array();

	for($i=1;$i<$nbMat+1;$i++){
		$x[$i]=round($largeurGrad+$i*$largeurMat-$largeurMat/2);
	}
	$x[$nbMat+1]=round($largeurGrad+$largeur+$largeurMat/2);
	//===================================================================================


	//if($_GET['temoin_image_escalier']=="oui"){
	//	$temoin_image_escalier="oui";
	//}
	$temoin_image_escalier=isset($_GET['temoin_image_escalier']) ? $_GET['temoin_image_escalier'] : "";

	//===================================================================================
	//Affichage des matières et des valeurs de moyenne:
	for($i=1;$i<$nbMat+1;$i++){
	//for($i=0;$i<$nbMat+1;$i++){

		$x1=$x[$i];
		$x2=$x[$i+1];

		//===========================================================================
		//Affichage des matières et des valeurs de moyenne dans la partie haute du graphique:
		$ytmp=20;

		if($tronquer_nom_court==0){
			$matiere_tronquee=$matiere[$i];
		}
		else{
			$matiere_tronquee=mb_substr($matiere[$i],0,$tronquer_nom_court);
		}

		writinfo('/tmp/infos_graphe.txt','a+',"\$matiere[$i]=$matiere[$i]\n");
		writinfo('/tmp/infos_graphe.txt','a+',"\$matiere_tronquee=$matiere_tronquee\n");

		$largeur_texte = mb_strlen($matiere_tronquee) * ImageFontWidth($taille_police);
		imagettftext($img, $taille_police*$rapport_imageString_imagettftext, 0, $x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $ytmp+10, $axes, dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", $matiere_tronquee);

		writinfo('/tmp/infos_graphe.txt','a+',"\$taille_police=$taille_police\n");
		writinfo('/tmp/infos_graphe.txt','a+',"\$largeur_texte=$largeur_texte\n");

		for($k=1;$k<=$nb_series_bis;$k++) {
			//if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
			/*
			if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')||
			((isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]!="Rang élève")))
			) {
			*/
			//if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
				$afficher_la_serie_courante="y";
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
					$ytmp=$ytmp+15;
					if(($k!=2)||((isset($nom_eleve[2]))&&($nom_eleve[2]!="Rang eleve")&&($nom_eleve[2]!="Rang élève"))) {$texte_courant=nf($moyenne[$k][$i]);} else {$texte_courant=$moyenne[$k][$i];}
					$largeur_texte = mb_strlen($texte_courant) * ImageFontWidth($taille_police);

					$tmp=$x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2);
					writinfo('/tmp/infos_graphe.txt','a+',"imagettftext($img, ".($taille_police*$rapport_imageString_imagettftext).", 0, ".($x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2)).", ".($ytmp+10).", $couleureleve[$k], ".dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf, ".$texte_courant.")\n");
					imagettftext($img, $taille_police*$rapport_imageString_imagettftext, 0, $x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $ytmp+10, $couleureleve[$k], dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", $texte_courant);
				}
			//}
		}
		//===========================================================================


		//===========================================================================
		if($temoin_image_escalier=="oui"){
            imagettftext($img, $taille_police*$rapport_imageString_imagettftext, -45, $x1-15, $hauteur+$hauteurMoy+5+10, $axes, dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", $matiere_nom_long[$i]);
		}
		else{
			//Affichage des matières dans la partie basse du graphique:
			$largeur_texte = mb_strlen($matiere_tronquee) * ImageFontWidth($taille_police);
            imagettftext($img, $taille_police*$rapport_imageString_imagettftext, 0, $x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $hauteur+$hauteurMoy+5+10, $axes, dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", $matiere_tronquee);
		}
	}


	if($mgen[1]!=""){
		$ytmp=20;

		$largeur_texte = mb_strlen("M.GEN") * ImageFontWidth($taille_police);
        imagettftext($img, $taille_police*$rapport_imageString_imagettftext, 0, $x1+round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $ytmp+10, $axes, dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", "M.GEN");

		$total_tmp=0;
		$cpt_tmp=0;
		//for($k=1;$k<$nb_data;$k++){
		for($k=1;$k<=$nb_series;$k++){
			/*
			if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')||
			((isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]!="Rang élève")))
			) {
			*/
			//if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
				$afficher_la_serie_courante="y";
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
					$ytmp=$ytmp+15;
					$largeur_texte = mb_strlen(nf($mgen[$k])) * ImageFontWidth($taille_police);
					imagettftext($img, $taille_police*$rapport_imageString_imagettftext, 0, $x1+round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $ytmp+10, $couleureleve[$k], dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", $mgen[$k]);

					if($mgen[$k]!="-"){
						$total_tmp=$total_tmp+$mgen[$k];
						$cpt_tmp++;
					}
				}
			//}
		}

		if(($legendy[2]=='Toutes_les_périodes')&&(isset($_GET['affiche_moy_annuelle']))){
			if($cpt_tmp>0){
				$mgen_annuelle=round($total_tmp/$cpt_tmp,1);
			}
			else{
				$mgen_annuelle="-";
			}

			$ytmp=$ytmp+15;
			$largeur_texte = mb_strlen(nf($mgen_annuelle)) * ImageFontWidth($taille_police);
            imagettftext($img, $taille_police*$rapport_imageString_imagettftext, 0, $x1+round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $ytmp+10, $couleureleve[$nb_series_bis], dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", $mgen_annuelle);
		}
	}

	//===================================================================================




	//=======================================================================
	// On positionne les noms d'élèves en haut de l'image: y=5
	// Pour en bas, ce serait: y=$hauteur+$hauteurMoy+25


	if($legendy[2]=='Toutes_les_périodes'){
		$chaine=$nom_periode;
	}
	else{
		//$chaine=$eleve;
		$chaine=$nom_eleve;
	}


	// Calcul de la largeur occupée par les noms d'élèves:
	$total_largeur_chaines=0;
	//for($k=1;$k<$nb_data;$k++){
	for($k=1;$k<=$nb_series;$k++){
		$largeur_chaine[$k] = mb_strlen($chaine[$k]) * ImageFontWidth($taille_police);
		$total_largeur_chaines=$total_largeur_chaines+$largeur_chaine[$k];
	}

	// Calcul de l'espace entre ces noms d'élèves:
	// Espace équilibré comme suit:
	//     espace|Eleve1|espace|Eleve2|espace
	// Il faudrait être sûr que l'espace ne va pas devenir négatif...
	$espace=($largeurTotale-$total_largeur_chaines)/($nb_series+1);

	// Positionnement des noms d'élèves:
	$xtmp=0;
	for($k=1;$k<=$nb_series;$k++){
		$xtmp=$xtmp+$espace;
        imagettftext($img, $taille_police*$rapport_imageString_imagettftext, 0, $xtmp, 15, $couleureleve[$k], dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", strtr($chaine[$k],"_"," "));
		$xtmp=$xtmp+$largeur_chaine[$k];
	}
	//=======================================================================



	//=====================================================================
	//Tracé des courbes:

	$afficher_pointille=mb_substr(getPref($_SESSION['login'], 'graphe_pointille',''),0,1);
	if($afficher_pointille=='') {
		$afficher_pointille=mb_substr(getSettingValue('graphe_pointille'),0,1);
	}

	//$white = imagecolorallocate($img, 255, 255, 255);

	//for($k=1;$k<=$nb_series;$k++){
	$epaisseur = $epaisseur_traits;
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
		if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
			if(($k!=2)||
			(($k==2)&&(isset($nom_eleve[2]))&&($nom_eleve[2]!="Rang eleve")&&($nom_eleve[2]!="Rang élève"))) {
		*/
		$afficher_la_serie_courante="y";
		if(($k==1)||($avec_moy_classe!='n')||($legendy[2]=='Toutes_les_périodes')) {
			$afficher_la_serie_courante="y";
		}
		// Le test sur le rang ne concerne que la courbe, pas les nombres affichés sous la ligne matière
		if(($k==2)&&(isset($nom_eleve[2]))&&(($nom_eleve[2]=="Rang eleve")||($nom_eleve[2]=="Rang élève"))) {
			$afficher_la_serie_courante="n";
		}
		if(($avec_moy_classe=='n')&&($k>1)&&(isset($eleve2))&&(($eleve2=='moyclasse')||($eleve2=='moymax')||($eleve2=='moymin'))) {
			$afficher_la_serie_courante="n";
		}

		if($afficher_la_serie_courante=="y") {

			//Placement des points de la courbe:
			for($i=1;$i<$nbMat+1;$i++){
				$x1=$x[$i];
				// C'est eleve_classe.php qui envoye 0 quand il n'y a pas de note... A CHANGER...
				//if(($moyenne[$k][$i]!="")&&($moyenne[$k][$i]!="N.NOT")&&($moyenne[$k][$i]!="ABS")&&($moyenne[$k][$i]!="DIS")){
				if(($moyenne[$k][$i]!="")&&($moyenne[$k][$i]!="-")&&($moyenne[$k][$i]!="N.NOT")&&($moyenne[$k][$i]!="ABS")&&($moyenne[$k][$i]!="DIS")){
					$y1=round($hauteurMoy+$hauteur-$moyenne[$k][$i]*$hauteur/20);
					imageFilledRectangle($img,$x1-2,$y1-2,$x1+2,$y1+2,$couleureleve[$k]);

					$ycourbe[$k][$i]=$y1;
				}
				else{
					$ycourbe[$k][$i]=-1;
				}
			}

			//Tracé de la courbe:
			//$x_prec[0]=-1;
			//$y_prec[0]=-1;
			imagesetthickness($img,$epaisseur);
			for($i=1;$i<$nbMat;$i++){
				$x1=$x[$i];
				$x2=$x[$i+1];
				if(($ycourbe[$k][$i]!=-1)&&($ycourbe[$k][$i+1]!=-1)){
					//$x_prec[$y+1]=$x2;
					//$y_prec[$y+1]=$ycourbe[$k][$i+1];
					imageLine($img,$x1,$ycourbe[$k][$i],$x2,$ycourbe[$k][$i+1],$couleureleve[$k]);
				}
				elseif(($afficher_pointille!='n')&&(isset($ycourbe[$k][$i]))&&($ycourbe[$k][$i]!=-1)&&(isset($ycourbe[$k][$i+2]))&&($ycourbe[$k][$i+2]!=-1)) {
					/*
					// imageDashedLine() est bugguée... on peut récupérer des hachures de 8mm de hauteur pour 2mm de large
					imagesetthickness($img,1);
					imageDashedLine($img,$x[$i+2],$ycourbe[$k][$i+2],$x1,$ycourbe[$k][$i],$couleureleve[$k]);
					imagesetthickness($img,$epaisseur);
					*/
					imagesetstyle($img, $style_pointille);
					imageLine($img,$x1,$ycourbe[$k][$i],$x[$i+2],$ycourbe[$k][$i+2],IMG_COLOR_STYLED);
					imagesetstyle($img, $style_plein);
				}
			}
			if($epaisseur_croissante_traits_periodes=='oui') {
				$epaisseur+=1;
			}
		}
	}

	//================================================================



	writinfo('/tmp/infos_graphe.txt','a+',"\nJuste avant imagePNG\n");

	imagePNG($img);

	imageDestroy($img);
?>
