<?php
/*
$Id: lib_eb.php 3821 2009-11-27 08:33:24Z crob $
*/
/*
function get_nom_prenom_eleve($login_ele) {
	$sql="SELECT nom,prenom FROM eleves WHERE login='$login_ele';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		return "Elève inconnu";
	}
	else {
		$lig=mysql_fetch_object($res);
		return casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
	}
}
*/
function get_denomination_prof($login) {
	$sql="SELECT nom,prenom,civilite FROM utilisateurs WHERE login='$login';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		return "Utilisateur inconnu";
	}
	else {
		$lig=mysql_fetch_object($res);
		return $lig->civilite." ".casse_mot($lig->nom)." ".strtoupper(substr($lig->prenom,0,1));
	}
}

function chaine_alea($nb_alpha,$nb_num) {
	$alphabet=array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
	$random_char=array();
	$chaine="";
	for($i=0;$i<$nb_alpha;$i++) {
		$random_char[$i]=$alphabet[floor(rand(0,25))];
	}
	for($j=0;$j<$nb_num;$j++) {
		$random_char[$nb_alpha+$j]=rand(0,9);;
	}
	for($k=0;$k<count($random_char);$k++) {
		$chaine.=$random_char[$k];
	}
	return $chaine;
}

function calcul_moy_med($tableau) {
	$eff_utile=0;
	$total=0;
	$tab_valeur=array();
	$j=0;
	$n=0;
	$rang=0;

	$tab_retour=array();
	$tab_retour['moyenne']='-';
	$tab_retour['mediane']='-';
	$tab_retour['min']='-';
	$tab_retour['max']='-';
	$tab_retour['q1']='-';
	$tab_retour['q3']='-';

	for($i=0;$i<count($tableau);$i++) {
		$valeur=$tableau[$i];
		if(($valeur!='abs')&&($valeur!='disp')&&($valeur!='-')&&($valeur!='')) {
			$tab_valeur[$j]=$valeur;
			$total+=$valeur;
			$eff_utile++;
			$j++;
		}
	}
	if($eff_utile>0) {
		$tab_retour['moyenne']=round(10*$total/$eff_utile)/10;

		$tab_valeur2=sort($tab_valeur);
		$n=count($tab_valeur);
		if($n/2==round($n/2)) {
			// Les indices commencent à zéro
			$tab_retour['mediane']=((100*$tab_valeur[$n/2-1]+100*$tab_valeur[$n/2])/100)/2;
		}
		else {
			$tab_retour['mediane']=$tab_valeur[($n-1)/2];
		}

		if($eff_utile>=4) {
			$rang=ceil($eff_utile/4);
			$tab_retour['q1']=$tab_valeur[$rang-1];

			$rang=ceil(3*$eff_utile/4);
			$tab_retour['q3']=$tab_valeur[$rang-1];
		}

		$tab_retour['min']=$tab_valeur[0];
		$tab_retour['max']=$tab_valeur[$n-1];
	}

	return $tab_retour;
}

?>