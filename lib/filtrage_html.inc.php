<?php
/*
$Id: filtrage_html.inc.php 4494 2010-05-25 14:17:08Z crob $
*/
	$enregistrer_filtrage_html="n";

	// Récupération et si necessaire initialisation de la valeur de 'filtrage_html'
	$sql="SELECT value FROM setting WHERE name='filtrage_html';";
	$res_filtrage_html=mysql_query($sql);
	if(mysql_num_rows($res_filtrage_html)==0) {
		$filtrage_html='htmlpurifier';
		$enregistrer_filtrage_html="y";
	}
	else {
		$lig_fh=mysql_fetch_object($res_filtrage_html);
		$filtrage_html=$lig_fh->value;
	}

	// Test de validité de la valeur récupérée
	if(($filtrage_html!='inputfilter')&&
		($filtrage_html!='htmlpurifier')&&
		($filtrage_html!='pas_de_filtrage_html')) {
		$filtrage_html='htmlpurifier';
		$enregistrer_filtrage_html="y";
	}

	// Test de compatibilité de la valeur avec la configuration
	if($filtrage_html=="htmlpurifier") {
		// HTMLPurifier fonctionne à partir de PHP 5.0.5
		$tab_version_php=explode(".",phpversion());
		if($tab_version_php[0]==4) {
			$filtrage_html='inputfilter';
			$enregistrer_filtrage_html="y";
		}
		elseif(($tab_version_php[0]==5)&&($tab_version_php[1]==0)&&($tab_version_php[2]<5)) {
			$filtrage_html='inputfilter';
			$enregistrer_filtrage_html="y";
		}
		elseif(get_magic_quotes_gpc()) {
			// HTMLPurifier entre en conflit avec magic_quotes_gpc=y
			// Cf. http://htmlpurifier.org/docs#toclink4
			$filtrage_html='inputfilter';
			$enregistrer_filtrage_html="y";
		}
	}

	if($enregistrer_filtrage_html=="y") {
		$sql="DELETE FROM setting WHERE name='filtrage_html';";
		$del_fh=mysql_query($sql);
		$sql="INSERT INTO setting SET name='filtrage_html', value='$filtrage_html';";
		$ins_fh=mysql_query($sql);
	}

	//===========================================================


	$enregistrer_utiliser_no_php_in_img="n";
	$sql="SELECT value FROM setting WHERE name='utiliser_no_php_in_img';";
	$res_utiliser_no_php_in_img=mysql_query($sql);
	if(mysql_num_rows($res_utiliser_no_php_in_img)==0) {
		$utiliser_no_php_in_img='n';
		$enregistrer_utiliser_no_php_in_img="y";
	}
	else {
		$lig_npi=mysql_fetch_object($res_utiliser_no_php_in_img);
		$utiliser_no_php_in_img=$lig_npi->value;
	}

	if(($utiliser_no_php_in_img!='n')&&
		($utiliser_no_php_in_img!='y')) {
		$utiliser_no_php_in_img='n';
		$enregistrer_utiliser_no_php_in_img="y";
	}

	if($enregistrer_utiliser_no_php_in_img=="y") {
		$sql="DELETE FROM setting WHERE name='utiliser_no_php_in_img';";
		$del_npi=mysql_query($sql);
		$sql="INSERT INTO setting SET name='utiliser_no_php_in_img', value='$utiliser_no_php_in_img';";
		$ins_npi=mysql_query($sql);
	}

?>