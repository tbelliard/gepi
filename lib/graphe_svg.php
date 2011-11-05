<?php

	/*
	*/
	// graphe_svg.php

	header("Content-type: image/svg+xml");
	echo '<?xml version="1.0" encoding="utf-8"?>';
	echo "\n";

	echo "<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 20010904//EN\" \"http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd\">\n";

	$serie=$_GET['serie'];
	$note_sur_serie=$_GET['note_sur_serie'];
	$nb_tranches=$_GET['nb_tranches'];
	$titre=$_GET['titre'];
	$largeurTotale=$_GET['largeurTotale'];
	$hauteurTotale=$_GET['hauteurTotale'];
	$taille_police=$_GET['taille_police'];
	$epaisseur_traits=$_GET['epaisseur_traits'];
	$v_legend1=$_GET['v_legend1'];
	$v_legend2=$_GET['v_legend2'];

	$notes=explode("|",$serie);

	$fond="white";
	$axes="black";
	$barre="coral";
	$epaisseur_grad=$epaisseur_traits;

	echo "<svg width=\"$largeurTotale\" height=\"$hauteurTotale\" xml:space=\"default\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n";

	echo "\n<!-- Bordure de l'image -->\n";
	echo "<rect x=\"0\" y=\"0\" width=\"$largeurTotale\" height=\"$hauteurTotale\" style=\"fill:$fond; stroke-width:1; stroke:black\" />\n";

	$marge=30;
	$fontsizetext=8;

	$y_abscisses=$hauteurTotale-$marge;
	$x_ordonnees=$marge;

	echo "<!-- Axes -->\n";
	$x1=$marge;
	$x2=$largeurTotale-$marge;
	echo "<line x1=\"$x1\" y1=\"$y_abscisses\" x2=\"$x2\" y2=\"$y_abscisses\" style=\"stroke:$axes; stroke-width:$epaisseur_grad\"/>\n";

	$y1=$marge;
	$y2=$hauteurTotale-$marge;
	echo "<line x1=\"$x_ordonnees\" y1=\"$y1\" x2=\"$x_ordonnees\" y2=\"$y2\" style=\"stroke:$axes; stroke-width:$epaisseur_grad\"/>\n";

	$largeur_utile=$largeurTotale-2*$marge;
	$hauteur_utile=$hauteurTotale-2*$marge;

	//echo "<line x1=\"5\" y1=\"50\" x2=\"60\" y2=\"80\" style=\"stroke:red; stroke-width:$epaisseur_grad\"/>\n";

	echo "<!-- Graduations horizontales -->\n";
	$dx=$largeur_utile/$nb_tranches;
	for($i=0;$i<$nb_tranches+1;$i++) {
		$tab_tranche[$i]=0;

		$x=$marge+$dx*$i;
		$y1=$hauteur_utile+$marge-3;
		$y2=$hauteur_utile+$marge+3;
		echo "<line x1=\"$x\" y1=\"$y1\" x2=\"$x\" y2=\"$y2\" style=\"stroke:$axes; stroke-width:$epaisseur_grad\"/>\n";

		$xtext=$marge+$dx*$i-5;
		$ytext=$hauteurTotale-5;
		if ($note_sur_serie % $nb_tranches == 0) {
			//si le nombre de tranche divise le référentiel de la note (note_sur) on affiche des label entier, sinon on affiche pas de légende pour le notes.
			$val=$i*$note_sur_serie/$nb_tranches;
		} else {
			$val = "";
		}
		echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:$axes; font-size:x-small;\">$val</text>\n";
	}

	for($i=0;$i<count($notes);$i++) {
		//$v=Ceil($notes[$i]/$largeur)*$largeur;
		//$v=Floor($notes[$i]/$largeur)*$largeur;
		//$w=$v+$largeur;

		if($notes[$i]==$note_sur_serie) {
			// Modif pour faire passer les notes 20 dans la tranche [0;20[
			$notes[$i]=$note_sur_serie - 0.1;
		}

		$tab_tranche[Floor($notes[$i]*$nb_tranches/$note_sur_serie)]++;
	}

	echo "<!-- Graduations verticales -->\n";
	$eff_max=max($tab_tranche);
	$dy=$hauteur_utile/$eff_max;
	for($i=0;$i<$eff_max;$i++) {
		$x1=$marge-3;
		$x2=$marge+3;
		$y=$marge+$hauteur_utile-$dy*$i;
		echo "<line x1=\"$x1\" y1=\"$y\" x2=\"$x2\" y2=\"$y\" style=\"stroke:$axes; stroke-width:$epaisseur_grad\"/>\n";

		$xtext=10;
		$ytext=$y;
		echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:$axes; font-size:small;\">$i</text>\n";
	}

	echo "<!-- Barres de l'histogramme -->\n";
	for($i=0;$i<count($tab_tranche);$i++) {
		//$v=$dx*$i;
		//$w=$v+$dx;

		$x1=$marge+$dx*$i;
		//$x2=$marge+$dx*($i+1);
		//$y1=$hauteur_utile+$marge;
		//$y2=$hauteur_utile+$marge-$tab_tranche[$i]*$dy;
		$y1=$hauteur_utile+$marge-$tab_tranche[$i]*$dy;
		$h_barre=$tab_tranche[$i]*$dy;

		// Pour éviter de dépasser 20 (cas d'un interval qui n'est pas un diviseur de 20):
		if($x1+$dx<=$marge+$largeur_utile) {
			$l_barre=$dx;
		}
		else {
			$l_barre=$marge+$largeur_utile-$x1;
		}

		echo "<rect x=\"$x1\" y=\"$y1\" width=\"$l_barre\" height=\"$h_barre\" style=\"fill:$barre; stroke-width:1; stroke:black\" />\n";
	}

	echo "<!-- Titre du graphe -->\n";
	$xtext=round($largeurTotale/2)-round(mb_strlen($titre)*$fontsizetext/2);
	$ytext=$marge-15;
	echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:$axes; font-size:$fontsizetext;\">$titre</text>\n";


	echo "<!-- Légende en abscisses -->\n";
	$xtext=$largeur_utile+$marge;
	$ytext=$hauteur_utile+$marge+15;
	echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:$axes; font-size:x-small;\">$v_legend1</text>\n";

	echo "<!-- Légende en ordonnées -->\n";
	$xtext=5;
	$ytext=$marge-5;
	echo "<text x=\"$xtext\" y=\"$ytext\" style=\"fill:$axes; font-size:x-small;\">$v_legend2</text>\n";


	echo "</svg>\n";
	die();

?>
