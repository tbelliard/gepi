<?php
/*
 $Id: draw_graphe_svg.php 6729 2011-03-30 09:33:15Z crob $
*/
	// Initialisations files
	require_once("../lib/initialisations.inc.php");

	header("Content-type: image/svg+xml");
	echo '<?xml version="1.0" encoding="iso-8859-1"?>';
	echo "\n";

	// On précise de ne pas traiter les données avec la fonction anti_inject
	$traite_anti_inject = 'no';
	// En quoi cela consiste-t-il?

	// Initialisations files
	//require_once("../lib/initialisations.inc.php");

	// Récupération des valeurs:
	//$nb_data = $_GET['nb_data'];
	$nb_series= $_GET['nb_series'];
	if((strlen(preg_replace("/[0-9]/","",$nb_series))!=0)||($nb_series=="")){
		exit;
	}

	//$eleves= $_GET['eleves'];
	$id_classe=$_GET['id_classe'];
	if((strlen(preg_replace("/[0-9]/","",$id_classe))!=0)||($id_classe=="")){
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

	/*
	// Fonction déplacée vers /lib/share.inc.php avec ajout du remplacement des espaces et apostrophes par des tirets '_'
	function remplace_accents($chaine){
		//$retour=strtr(my_ereg_replace("¼","OE",my_ereg_replace("½","oe",$chaine)),"ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü","AAAEEEEIIOOUUUCcaaaeeeeiioouuu");
		//$retour=strtr(my_ereg_replace("Æ","AE",my_ereg_replace("æ","ae",my_ereg_replace("¼","OE",my_ereg_replace("½","oe","$chaine"))))," 'ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõ¨ûüùúýÿ¸","__AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz");
		$retour=strtr(my_ereg_replace("Æ","AE",my_ereg_replace("æ","ae",my_ereg_replace("¼","OE",my_ereg_replace("½","oe","$chaine")))),"ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõ¨ûüùúýÿ¸","AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz");
		return $retour;
	}
	*/




	// Conversion de composantes RVB en portion de chaine couleur HTML

	function rvb2hexa($composante){
		$hexa=array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");

		$hex1=Floor($composante/16);
		$hex2=$composante-$hex1*16;
		$chaine=$hexa["$hex1"].$hexa["$hex2"];
		$fich=fopen("/tmp/svg.txt","a+");
		/*
		fwrite($fich,"Composante: $composante\n");
		fwrite($fich,"\$hex1: $hex1\n");
		fwrite($fich,"\$hex2: $hex2\n");
		fwrite($fich,"\$chaine: $chaine\n");
		*/
		return $chaine;
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
	if((strlen(preg_replace("/[0-9]/","",$largeurTotale))!=0)||($largeurTotale=="")){
		$largeurTotale=700;
	}
	$hauteurTotale=isset($_GET['hauteur_graphe']) ? $_GET['hauteur_graphe'] : '600';
	if((strlen(preg_replace("/[0-9]/","",$hauteurTotale))!=0)||($hauteurTotale=="")){
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

	// $taille_police de 1 à 6
	//$taille_police=3;
	$taille_police=isset($_GET['taille_police']) ? $_GET['taille_police'] : '3';
	if((strlen(preg_replace("/[0-9]/","",$taille_police))!=0)||($taille_police<1)||($taille_police>6)||($taille_police=="")){
		$taille_police=3;
	}

	//$epaisseur_traits=2;
	$epaisseur_traits=isset($_GET['epaisseur_traits']) ? $_GET['epaisseur_traits'] : '2';
	if((strlen(preg_replace("/[0-9]/","",$epaisseur_traits))!=0)||($epaisseur_traits<1)||($epaisseur_traits>6)||($epaisseur_traits=="")){
		$epaisseur_traits=2;
	}

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

		$call_matiere = mysql_query("SELECT nom_complet FROM matieres WHERE matiere = '".$matiere[$i]."'");
		$matiere_nom_long[$i] = mysql_result($call_matiere, "0", "nom_complet");
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
	$resultat_infos_eleve1=mysql_query($sql);
	$ligne=mysql_fetch_object($resultat_infos_eleve1);
	//$nom_eleve1=$ligne->nom." ".$ligne->prenom;
	$nom_eleve[1]=$ligne->nom." ".$ligne->prenom;
	if($periode!=''){
		$nom_eleve[1]=$nom_eleve[1]." ($periode)";
	}
	$nom_eleve[1]=remplace_accents($nom_eleve[1],'simple');

	// Variable destinée à tenir compte de la moyenne annuelle...
	$nb_series_bis=$nb_series;
	if($legendy[2]=='Toutes_les_périodes'){
		$eleve2="";

		$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode";
		$result_periode=mysql_query($sql);
		$nb_periode=mysql_num_rows($result_periode);

		$cpt=1;
		while($lign_periode=mysql_fetch_object($result_periode)){
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
					//if((strlen(preg_replace("/[0-9]/","",$largeur_imposee_photo))!=0)||($largeur_imposee_photo=="")){$largeur_imposee_photo=100;}



					writinfo('/tmp/infos_graphe.txt','a+',"strlen(preg_replace(\"/[0-9\.]/\",\"\",\$moyenne[".$k."][".$i."]))=strlen(preg_replace(\"/[0-9\.]/\",\"\",".$moyenne[$k][$i]."))=".strlen(preg_replace("/[0-9\.]/","",$moyenne[$k][$i]))."\n");

					//if((strlen(preg_replace("/[0-9]/","",$moyenne[$k][$i]))!=0)&&($moyenne[$k][$i]!="")){
					if(($moyenne[$k][$i]!='-')&&(strlen(preg_replace("/[0-9\.]/","",$moyenne[$k][$i]))==0)&&($moyenne[$k][$i]!="")){
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
				break;
			case 'moymin':
					//$nom_eleve2="Moyennes minimales";
					$nom_eleve[2]="Moyennes minimales";
				break;
			case 'moymax':
					//$nom_eleve2="Moyennes maximales";
					$nom_eleve[2]="Moyennes maximales";
				break;
			default:
				$sql="SELECT * FROM eleves WHERE login='$eleve2'";
				$resultat_infos_eleve2=mysql_query($sql);
				$ligne=mysql_fetch_object($resultat_infos_eleve2);
				//$nom_eleve2=$ligne->nom." ".$ligne->prenom;
				$nom_eleve[2]=$ligne->nom." ".$ligne->prenom;
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
	// Hauteur de police en pixels:
	$h_txt_px=ImageFontHeight($taille_police);
	// Largeur de police en pixels:
	$l_txt_px=ImageFontWidth($taille_police);

	//$fontsizetext=Floor($taille_police*3.5);
	$fontsizetext=$h_txt_px;
	//============================================

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
		$largeur_texte_long = strlen($matiere_nom_long[$i]) * $l_txt_px;
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
	//$img=imageCreate($largeurTotale,$hauteurTotale);
	// Epaisseur initiale des traits...
	//imagesetthickness($img,2);

	echo "<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 20010904//EN\" \"http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd\">\n";

	//echo "<svg xml:space=\"default\" width=\"$largeurTotale\" height=\"$hauteurTotale\">\n";
	//echo "<svg width=\"$largeurTotale\" height=\"$hauteurTotale\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\">\n";
	echo "<svg width=\"$largeurTotale\" height=\"$hauteurTotale\" xml:space=\"default\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n";

	//xml:space="default" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"

	//echo "<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" version=\"1.1\"  width=\"$largeurTotale\" height=\"$hauteurTotale\">\n";

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
			$res_couleur=mysql_query($sql);
			if(mysql_num_rows($res_couleur)>0){
				$tmp=mysql_fetch_object($res_couleur);
				$tabcouleurs[$tab[$i]][$comp[$j]]=$tmp->value;
			}
		}
		//$couleur[$tab[$i]]=imageColorAllocate($img,$tabcouleurs[$tab[$i]]['R'],$tabcouleurs[$tab[$i]]['V'],$tabcouleurs[$tab[$i]]['B']);
		// A REVOIR POUR svg
		//$couleur[$tab[$i]]="red";
		$couleur[$tab[$i]]="#".rvb2hexa($tabcouleurs[$tab[$i]]['R']).rvb2hexa($tabcouleurs[$tab[$i]]['V']).rvb2hexa($tabcouleurs[$tab[$i]]['B']);
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
				$res_couleur=mysql_query($sql);
				if(mysql_num_rows($res_couleur)>0){
					$tmp=mysql_fetch_object($res_couleur);
					$tabcouleurs["Periode_".$i][$comp[$j]]=$tmp->value;
				}
				else{
					$tabcouleurs["Periode_".$i][$comp[$j]]=0;
				}
			}
			//$couleur["Periode_".$i]=imageColorAllocate($img,$tabcouleurs["Periode_".$i]['R'],$tabcouleurs["Periode_".$i]['V'],$tabcouleurs["Periode_".$i]['B']);
			// A REVOIR POUR svg
			//$couleur["Periode_".$i]="green";
			$couleur["Periode_".$i]="#".rvb2hexa($tabcouleurs["Periode_".$i]['R']).rvb2hexa($tabcouleurs["Periode_".$i]['V']).rvb2hexa($tabcouleurs["Periode_".$i]['B']);

			$couleureleve[$i]=$couleur["Periode_".$i];
		}
	}
	$couleurmoyenne=$couleur['Moyenne_classe'];
	$axes=$couleur['Axes'];

	// IL FAUT UNE COULEUR DE PLUS POUR LA MOYENNE ANNUELLE...
	$couleureleve[$i]=$couleur['Moyenne_classe'];

	//============================================


	// On force la couleur pour les moyennes classe/min/max
	if(($eleve2=='moyclasse')||($eleve2=='moymin')||($eleve2=='moymax')){
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


	//echo "<rect x=\"5\" y=\"15\" width=\"50\" height=\"200\" style=\"fill:yellow; stroke-width:1; stroke:black\" />";

	echo "\n<!-- Bordure de l'image -->\n";
	echo "<rect x=\"0\" y=\"0\" width=\"$largeurTotale\" height=\"$hauteurTotale\" style=\"fill:$fond; stroke-width:1; stroke:black\" />\n";

	//===========================================
	echo "\n<!-- Bandes verticales -->\n";
	if((!isset($seriemin))||(!isset($seriemax))){
		//Bandes verticales alternées:
		for($i=1;$i<$nbMat+1;$i++){
			$x1=round($largeurGrad+($i-1)*$largeurMat);
			$x2=round($largeurGrad+$i*$largeurMat);
			if($i-2*Floor($i/2)==0){
				//imageFilledRectangle($img,$x1,$hauteurMoy,$x2,$hauteur+$hauteurMoy,$bande1);
				echo "<rect x=\"$x1\" y=\"$hauteurMoy\" width=\"$largeurMat\" height=\"".$hauteur."\" style=\"fill:$bande2; stroke-width:1; stroke:$fond\" />\n";
				//echo "<rect x=\"$x1\" y=\"$hauteurMoy\" width=\"$largeurMat\" height=\"200\" style=\"fill:blue; stroke-width:1; stroke:black\" />";
			}
			/*
			else{
				imageFilledRectangle($img,$x1,$hauteurMoy,$x2,$hauteur+$hauteurMoy,$bande2);
			}
			*/


			//Textes dans la bande du bas:
			//$largeur_texte = strlen($matiere[$i]) * $l_txt_px;
			//imagestring ($img, $taille_police, $x1+round((($x2-$x1)-$largeur_texte)/2), $hauteur+$hauteurMoy+10, $matiere[$i], $noir);

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
			//imageFilledRectangle($img,$x1,$ordonneemax,$x2,$ordonneemin,$bande2);

			//echo "<rect x=\"$x1\" y=\"$hauteurMoy\" width=\"$largeurMat\" height=\"".$hauteur+$hauteurMoy."\" style=\"fill:blue; stroke-width:1; stroke:black\" />";
			//echo "<rect x=\"$x1\" y=\"$hauteurMoy\" width=\"$largeurMat\" height=\"100\" style=\"fill:blue; stroke-width:1; stroke:lime\" />";
			$hauteur_rect=$ordonneemin-$ordonneemax;
			//echo "<rect x=\"$x1\" y=\"$ordonneemax\" width=\"$largeurMat\" height=\"$hauteur_rect\" style=\"fill:blue; stroke-width:1; stroke:lime\" />\n";
			//echo "<rect x=\"$x1\" y=\"$ordonneemax\" width=\"$largeurMat\" height=\"$hauteur_rect\" style=\"fill:$bande2; stroke-width:1; stroke:lime\" />\n";
			echo "<rect x=\"$x1\" y=\"$ordonneemax\" width=\"$largeurMat\" height=\"$hauteur_rect\" style=\"fill:$bande2; stroke-width:1; stroke:$fond\" />\n";

			//$fich_tmp=fopen("/tmp/svg.txt","a+");
			//fwrite($fich_tmp,"<rect x=\"$x1\" y=\"$ordonneemax\" width=\"$largeurMat\" height=\"$hauteur_rect\" style=\"fill:blue; stroke-width:1; stroke:lime\" />\n");
			//fwrite($fich_tmp,"<rect x=\"$x1\" y=\"$ordonneemax\" width=\"$largeurMat\" height=\"$hauteur_rect\" style=\"fill:$bande2; stroke-width:1; stroke:lime\" />\n");
			//fclose($fich_tmp);

		}
	}

	//echo "<line x1='5' y1='10' x2='100' y2='150' style='stroke:black; stroke-width:$epaisseur_grad'/>\n";

	//===========================================





	//=============================================================================
	//Tracé des graduations et des axes:
	//Graduations:
	echo "\n<!-- Graduations -->\n";
	$pas=2; //Prendre un diviseur non nul de 20.
	for($i=0;$i<21;$i=$i+$pas){

		//Epaisseur des graduations:
		//imagesetthickness($img,$epaisseur_grad);

		$x1=$largeurGrad-5;
		$x2=$largeurGrad+5;
		//$yg=round($hauteurMoy+$hauteur-$i*($hauteur/(20/$pas)));
		$yg=round($hauteurMoy+$hauteur-$i*($hauteur/20));
		//imageLine($img,$x1,$yg,$x2,$yg,$axes);
		//imagestring ($img, $taille_police, $x1-20, $yg-10, "$i", $axes);

		//echo "<line x1='$x1' y1='$yg' x2='$x2' y2='$yg' style='stroke:$axes; stroke-width:$epaisseur_grad'/>\n";
		echo "<line x1=\"$x1\" y1=\"$yg\" x2=\"$x2\" y2=\"$yg\" style=\"stroke:$axes; stroke-width:$epaisseur_grad\"/>\n";
		$xtext=$x1-20;
		//$ytext=$yg-10;
		$ytext=$yg;
		//$ytext=$yg+$fontsizetext;
		//$fontsizetext=Floor($taille_police*3.5);
		echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:$axes; font-size:$fontsizetext;\">$i</text>\n";

		//imagedashedline($img,$largeurGrad,$yg,$largeur+$largeurGrad,$yg,$axes);

		//$style = array ($axes,$axes,$axes,$axes,$axes,$fond,$fond,$fond,$fond,$fond);
		/*
		$style = array ($axes,$axes,$axes,$axes,$axes,$axes,$axes,$axes,$axes,$axes,$transp,$transp,$transp,$transp,$transp,$transp,$transp,$transp,$transp,$transp);
		imagesetstyle ($img, $style);
		imageline ($img,$largeurGrad,$yg,$largeur+$largeurGrad,$yg, IMG_COLOR_STYLED);
		*/
		$xtmp=$largeur+$largeurGrad;
		echo "<line x1='$largeurGrad' y1='$yg' x2='$xtmp' y2='$yg' style='stroke:$axes; stroke-width:$epaisseur_grad; stroke-dasharray: 5, 5'/>\n";

		//stroke-dasharray: 5, 5

		//imageline ($img,$largeurGrad,$yg,$largeur+$largeurGrad,$yg, $axes);

	}



	//Epaisseur des axes:
	//imagesetthickness($img,$epaisseur_axes);

	//Axe des abscisses:
	echo "\n<!-- Axe des abscisses -->\n";
	//imageLine($img,$largeurGrad,$hauteurMoy+$hauteur,round($largeur+$largeurGrad+$largeurBandeDroite/2),$hauteurMoy+$hauteur,$axes);
	$xtmp1=$largeurGrad;
	$ytmp1=$hauteurMoy+$hauteur;
	$xtmp2=round($largeur+$largeurGrad+$largeurBandeDroite/2);
	$ytmp2=$hauteurMoy+$hauteur;
	echo "<line x1=\"$xtmp1\" y1=\"$ytmp1\" x2=\"$xtmp2\" y2=\"$ytmp2\" style=\"stroke:$axes; stroke-width:$epaisseur_axes\"/>\n";

	//Axe des ordonnées:
	echo "\n<!-- Axe des ordonnées -->\n";
	//imageLine($img,$largeurGrad,round($hauteurMoy/2),$largeurGrad,$hauteur+$hauteurMoy,$axes);
	$xtmp1=$largeurGrad;
	$ytmp1=round($hauteurMoy/2);
	$xtmp2=$largeurGrad;
	$ytmp2=$hauteur+$hauteurMoy;
	echo "<line x1=\"$xtmp1\" y1=\"$ytmp1\" x2=\"$xtmp2\" y2=\"$ytmp2\" style=\"stroke:$axes; stroke-width:$epaisseur_axes\"/>\n";

	//Barre de la moyenne:
	echo "\n<!-- Barre de la moyenne -->\n";
	//imageLine($img,$largeurGrad,round($hauteurMoy+$hauteur/2),round($largeur+$largeurGrad+$largeurBandeDroite/2),round($hauteurMoy+$hauteur/2),$axes);
	$xtmp1=$largeurGrad;
	$ytmp1=round($hauteurMoy+$hauteur/2);
	$xtmp2=round($largeur+$largeurGrad+$largeurBandeDroite/2);
	$ytmp2=round($hauteurMoy+$hauteur/2);
	echo "<line x1=\"$xtmp1\" y1=\"$ytmp1\" x2=\"$xtmp2\" y2=\"$ytmp2\" style=\"stroke:$axes; stroke-width:$epaisseur_axes\"/>\n";

	//imagedashedline pour pointillés
	//imagedashedline($img,5,5,100,100,$axes);
	//imageline($img,5,5,100,100,$axes);
	//==============================================================================













	//=============================================================================
	// Préparation des abscisses et affichage des noms de matières et valeurs des moyennes:

	//Epaisseur des traits:
	//imagesetthickness($img,$epaisseur_traits);

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
	echo "\n<!-- Noms de matières et moyennes -->\n";
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
			$matiere_tronquee=substr($matiere[$i],0,$tronquer_nom_court);
		}

		writinfo('/tmp/infos_graphe.txt','a+',"\$matiere[$i]=$matiere[$i]\n");
		writinfo('/tmp/infos_graphe.txt','a+',"\$matiere_tronquee=$matiere_tronquee\n");

		//$largeur_texte = strlen($matiere[$i]) * $l_txt_px;
		//imagestring ($img, $taille_police, $x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $ytmp, $matiere[$i], $axes);



		//$largeur_texte = strlen($matiere_tronquee) * $l_txt_px;
		//imagestring ($img, $taille_police, $x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $ytmp, $matiere_tronquee, $axes);

		//$largeur_texte=30;	// A REVOIR... COMMENT LE CALCULER EN SVG?
		//$largeur_texte=0;	// A REVOIR... COMMENT LE CALCULER EN SVG?
		$largeur_texte = strlen($matiere_tronquee) * $l_txt_px;
		$xtext=$x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2);
		//$ytext=$ytmp;
		$ytext=$ytmp+$fontsizetext;
		//$fontsizetext=Floor($taille_police*3.5);
		echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:$axes; font-size:$fontsizetext;\">$matiere_tronquee</text>\n";

		//$epaisseur_traits

		writinfo('/tmp/infos_graphe.txt','a+',"\$taille_police=$taille_police\n");
		writinfo('/tmp/infos_graphe.txt','a+',"\$largeur_texte=$largeur_texte\n");

		//for($k=1;$k<$nb_data;$k++){
		//for($k=1;$k<=$nb_series;$k++){
		for($k=1;$k<=$nb_series_bis;$k++){
			$ytmp=$ytmp+15;
			//$largeur_texte = strlen($moyenne[$k][$i]) * $l_txt_px;

			$tmp=$x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2);
			writinfo('/tmp/infos_graphe.txt','a+',"\nimagestring (\$img, $taille_police, ".$tmp.", $ytmp, ".$moyenne[$k][$i].", ".$couleureleve[$k].")\n");
			//imagestring ($img, $taille_police, $x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $ytmp, $moyenne[$k][$i], $couleureleve[$k]);

			//$largeur_texte=30;	// A REVOIR... COMMENT LE CALCULER EN SVG?
			//$largeur_texte=0;	// A REVOIR... COMMENT LE CALCULER EN SVG?
			$largeur_texte = strlen($moyenne[$k][$i]) * $l_txt_px;

			$xtext=$x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2);
			//$ytext=$ytmp;
			$ytext=$ytmp+$fontsizetext;
			//$fontsizetext=Floor($taille_police*3.5);
			echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:".$couleureleve[$k]."; font-size:$fontsizetext;\">".$moyenne[$k][$i]."</text>\n";
		}
		//===========================================================================


		//===========================================================================
		if($temoin_image_escalier=="oui"){
			//$dx=10;
			/*
			$dx=$l_txt_px+1;
			$dy=3;
			for($k=0;$k<strlen($matiere_nom_long[$i]);$k++){
				//$lettre_tmp=substr($matiere_nom_long[$i],$k,1);
				$lettre_tmp=substr(strtr($matiere_nom_long[$i],"_"," "),$k,1);

				//imagestring ($img, $taille_police, $x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2)+$k*$dx, $hauteur+$hauteurMoy+5+$k*$dy, $lettre_tmp, $axes);
				imagestring ($img, $taille_police, $x1+$k*$dx, $hauteur+$hauteurMoy+5+$k*$dy, $lettre_tmp, $axes);
			}
			*/
			//echo "<path id=\"path_esc_$i\" d=\"M $xtmp $ytmp L \"/>\n";

			$xtmp=$x1;
			//$ytmp=$hauteur+$hauteurMoy+5;
			//$ytmp=$hauteur+$hauteurMoy+10;
			$ytmp=$hauteur+$hauteurMoy+10+$fontsizetext;
			//$fontsizetext=Floor($taille_police*3.5);

			//font-family=\"Verdana\"

			// Les & posent problème...
			// ... peut-être d'autres caractères aussi?
			echo "<g transform=\"translate($xtmp,$ytmp)\">
   <g transform=\"rotate(30)\">
      <text x=\"0\" y=\"0\" font-size=\"$fontsizetext\" fill=\"$axes\" >
         ".preg_replace("/&/"," et ",$matiere_nom_long[$i])."
      </text>
   </g>
</g>\n";

/*
			echo "<g transform=\"translate($xtmp,$ytmp)\">
<g transform=\"rotate(30)\">
<text x=\"0\" y=\"0\" font-size=\"$fontsizetext\" font-family=\"Verdana\" fill=\"$axes\" >
BLA
</text>
</g>
</g>\n";
*/
			/*
			$fich=fopen("/tmp/svg_pb_nom_mat.txt","a+");
			fwrite($fich,"\$matiere_nom_long[$i]=".$matiere_nom_long[$i]."\n");
			fclose($fich);
			*/

		}
		else{
			//Affichage des matières dans la partie basse du graphique:
			//$largeur_texte = strlen($matiere[$i]) * $l_txt_px;
			//imagestring ($img, $taille_police, $x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $hauteur+$hauteurMoy+5, $matiere[$i], $axes);

			//$largeur_texte = strlen($matiere_tronquee) * $l_txt_px;
			//imagestring ($img, $taille_police, $x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $hauteur+$hauteurMoy+5, $matiere_tronquee, $axes);

			$largeur_texte = strlen($matiere_tronquee) * $l_txt_px;
			$xtext=$x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2);
			//$ytext=$hauteur+$hauteurMoy+5;
			$ytext=$hauteur+$hauteurMoy+5+$fontsizetext;
			//$fontsizetext=Floor($taille_police*3.5);
			echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:".$axes."; font-size:$fontsizetext;\">".$matiere_tronquee."</text>\n";

		}
		echo "\n";
		//===========================================================================


		//===========================================================================
		// Pour afficher les noms longs de matières à la verticale en bas d'image:



		//$largeur_texte_long=strlen($matiere_nom_long[$i]) * $l_txt_px;
		//$hauteur_texte_long=ImageFontHeight($taille_police);
		//imagestringup($img, 3, $x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $hauteur+$hauteurMoy+5+$largeur_texte_long, $matiere_nom_long[$i], $axes);


		// Essais imagerotate... PB sous Debian Sarge
		//$imgtmp=ImageCreate($largeur_texte_long,$hauteur_texte_long);
		//$couleur_fond_tmp=imageColorAllocate($imgtmp,255,255,255);
		//imagecolortransparent($imgtmp,$couleur_fond_tmp);
		//$couleur_txt_tmp=imageColorAllocate($imgtmp,0,0,0);
		//imagestring($imgtmp, $taille_police, 2, 2, $matiere_nom_long[$i], $couleur_txt_tmp);
		//$imgrotate=imagerotate($imgtmp,30,$couleur_fond_tmp);

		//$largeur_tmp=imagesx($imgrotate);
		//$hauteur_tmp=imagesy($imgrotate);
		//ImageCopy ($img, $imgtmp, $x1-round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $hauteur+$hauteurMoy+5+$largeur_texte_long, 0, 0, $largeur_tmp, $hauteur_tmp);




		//===========================================================================

	}





	if($mgen[1]!=""){
		echo "\n<!-- Colonne moyenne générale -->\n";
		$ytmp=20;

		$largeur_texte = strlen("M.GEN") * $l_txt_px;
		//imagestring ($img, $taille_police, $x1+round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $ytmp, "M.GEN", $axes);

		$xtext=$x1+round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2);
		//$ytext=$ytmp;
		$ytext=$ytmp+$fontsizetext;
		//$fontsizetext=Floor($taille_police*3.5);
		echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:".$axes."; font-size:$fontsizetext;\">M.GEN</text>\n";

		$total_tmp=0;
		$cpt_tmp=0;
		//for($k=1;$k<$nb_data;$k++){
		for($k=1;$k<=$nb_series;$k++){
			$ytmp=$ytmp+15;
			//$largeur_texte = strlen($mgen[$k]) * $l_txt_px;
			$largeur_texte = strlen(nf($mgen[$k])) * $l_txt_px;
			//imagestring ($img, $taille_police, $x1+round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $ytmp, $mgen[$k], $couleureleve[$k]);

			$xtext=$x1+round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2);
			//$ytext=$ytmp;
			$ytext=$ytmp+$fontsizetext;
			//$fontsizetext=Floor($taille_police*3.5);
			//echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:".$couleureleve[$k]."; font-size:$fontsizetext;\">".$mgen[$k]."</text>\n";
			echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:".$couleureleve[$k]."; font-size:$fontsizetext;\">".nf($mgen[$k])."</text>\n";


			if($mgen[$k]!="-"){
				$total_tmp=$total_tmp+$mgen[$k];
				$cpt_tmp++;
			}
		}

		if(($legendy[2]=='Toutes_les_périodes')&&(isset($_GET['affiche_moy_annuelle']))){
			if($cpt_tmp>0){
				$mgen_annuelle=round($total_tmp/$cpt_tmp,1);
			}
			else{
				$mgen_annuelle="-";
			}

			$ytmp=$ytmp+15;
			//$largeur_texte = strlen($mgen_annuelle) * $l_txt_px;
			$largeur_texte = strlen(nf($mgen_annuelle)) * $l_txt_px;
			//imagestring ($img, $taille_police, $x1+round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2), $ytmp, $mgen_annuelle, $couleureleve[$nb_series_bis]);


			$xtext=$x1+round($largeurMat/2)+round((($x2-$x1)-$largeur_texte)/2);
			//$ytext=$ytmp;
			$ytext=$ytmp+$fontsizetext;
			//$fontsizetext=Floor($taille_police*3.5);
			//echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:".$couleureleve[$nb_series_bis]."; font-size:$fontsizetext;\">".$mgen_annuelle."</text>\n";
			echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:".$couleureleve[$nb_series_bis]."; font-size:$fontsizetext;\">".nf($mgen_annuelle)."</text>\n";
		}

	}



	//===================================================================================




	//=======================================================================
	// On positionne les noms d'élèves en haut de l'image: y=5
	// Pour en bas, ce serait: y=$hauteur+$hauteurMoy+25

	echo "\n<!-- Informations du haut de l'image -->\n";

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
		//$largeur_eleve[$k] = strlen($eleve[$k]) * $l_txt_px;
		//$total_largeur_eleves=$total_largeur_eleves+$largeur_eleve[$k];
		$largeur_chaine[$k] = strlen($chaine[$k]) * $l_txt_px;
		$total_largeur_chaines=$total_largeur_chaines+$largeur_chaine[$k];
	}

	// Calcul de l'espace entre ces noms d'élèves:
	// Espace équilibré comme suit:
	//     espace|Eleve1|espace|Eleve2|espace
	// Il faudrait être sûr que l'espace ne va pas devenir négatif...
	//$espace=($largeur-$total_largeur_eleves)/($nb_series+1);
	//$espace=($largeur-$total_largeur_chaines)/($nb_series+1);
	$espace=($largeurTotale-$total_largeur_chaines)/($nb_series+1);

	// Positionnement des noms d'élèves:
	//$xtmp=$largeurGrad;
	$xtmp=0;
	//for($k=1;$k<$nb_data;$k++){
	for($k=1;$k<=$nb_series;$k++){
		$xtmp=$xtmp+$espace;
		//imagestring ($img, $taille_police, $xtmp, 5, $eleve[$k], $couleureleve[$k]);
		//$xtmp=$xtmp+$largeur_eleve[$k];
		//imagestring ($img, $taille_police, $xtmp, 5, $chaine[$k], $couleureleve[$k]);
		//imagestring ($img, $taille_police, $xtmp, 5, strtr($chaine[$k],"_"," "), $couleureleve[$k]);

		$xtext=$xtmp;
		//$ytext=5;
		//$ytext=10;
		$ytext=5+$fontsizetext;
		//$fontsizetext=Floor($taille_police*3.5);
		echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:".$couleureleve[$k]."; font-size:$fontsizetext;\">".strtr($chaine[$k],"_"," ")."</text>\n";


		$xtmp=$xtmp+$largeur_chaine[$k];
	}
	//=======================================================================



	//imagestring ($img, $taille_police, 50, 100, "-".$moyenne[3][1]."-", $couleureleve[3]);
	//imagestring ($img, $taille_police, 50, 100, "-".$eleves[1]."-", $couleureleve[1]);
	//imagestring ($img, $taille_police, 50, 120, "-".$eleves[2]."-", $couleureleve[2]);

	//=====================================================================
	//Tracé des courbes:

	echo "\n<!-- Tracé des courbes -->\n";

	//for($k=1;$k<=$nb_series;$k++){
	for($k=1;$k<=$nb_series_bis;$k++){
		echo "\n<!-- Courbe de la série $k -->\n";

		//Placement des points de la courbe:
		for($i=1;$i<$nbMat+1;$i++){
			$x1=$x[$i];
			// C'est eleve_classe.php qui envoye 0 quand il n'y a pas de note... A CHANGER...
			//if(($moyenne[$k][$i]!="")&&($moyenne[$k][$i]!="N.NOT")&&($moyenne[$k][$i]!="ABS")&&($moyenne[$k][$i]!="DIS")){
			if(($moyenne[$k][$i]!="")&&($moyenne[$k][$i]!="-")&&($moyenne[$k][$i]!="N.NOT")&&($moyenne[$k][$i]!="ABS")&&($moyenne[$k][$i]!="DIS")){
				$y1=round($hauteurMoy+$hauteur-$moyenne[$k][$i]*$hauteur/20);
				//imageFilledRectangle($img,$x1-2,$y1-2,$x1+2,$y1+2,$couleureleve[$k]);

				$xtmp1=$x1-2;
				$ytmp1=$y1-2;
				//echo "<rect x=\"$xtmp1\" y=\"$ytmp1\" width=\"4\" height=\"4\" style=\"fill:".$couleureleve[$k]."; stroke-width:1; stroke:black\" />";
				echo "<rect x=\"$xtmp1\" y=\"$ytmp1\" width=\"4\" height=\"4\" style=\"fill:".$couleureleve[$k].";\" />";

				$ycourbe[$k][$i]=$y1;
			}
			else{
				$ycourbe[$k][$i]=-1;
			}
		}

		//Tracé de la courbe:
		for($i=1;$i<$nbMat;$i++){
			$x1=$x[$i];
			$x2=$x[$i+1];
			if(($ycourbe[$k][$i]!=-1)&&($ycourbe[$k][$i+1]!=-1)){
				//imageLine($img,$x1,$ycourbe[$k][$i],$x2,$ycourbe[$k][$i+1],$couleureleve[$k]);

				$xtmp1=$x1;
				$ytmp1=$ycourbe[$k][$i];
				$xtmp2=$x2;
				$ytmp2=$ycourbe[$k][$i+1];
				echo "<line x1=\"$xtmp1\" y1=\"$ytmp1\" x2=\"$xtmp2\" y2=\"$ytmp2\" style=\"stroke:".$couleureleve[$k]."; stroke-width:$epaisseur_traits\"/>\n";

			}
		}
	}

	//================================================================


	echo "</svg>\n";
	die();


	writinfo('/tmp/infos_graphe.txt','a+',"\nJuste avant imagePNG\n");

	imagePNG($img);

	imageDestroy($img);
?>