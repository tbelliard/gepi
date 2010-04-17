<?php
/*
$Id$
*/

	$enregistrer_filtrage_html="n";
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

	if(($filtrage_html!='inputfilter')&&
		($filtrage_html!='htmlpurifier')&&
		($filtrage_html!='pas_de_filtrage_html')) {
		$filtrage_html='htmlpurifier';
		$enregistrer_filtrage_html="y";
	}

	if($filtrage_html=="htmlpurifier") {
		// HTMLPurifier fonctionne à partir de PHP 5.0.5
		$tab_version_php=explode(".",phpversion());
		if($tab_version_php[0]==4) {
			$filtrage_html='inputfilter';
		}
		elseif(($tab_version_php[0]==5)&&($tab_version_php[1]==0)&&($tab_version_php[2]<5)) {
			$filtrage_html='inputfilter';
		}
		$enregistrer_filtrage_html="y";
	}

	if($enregistrer_filtrage_html=="y") {
		$sql="DELETE FROM setting WHERE name='filtrage_html';";
		$del_fh=mysql_query($sql);
		$sql="INSERT INTO setting SET name='filtrage_html', value='$filtrage_html';";
		$ins_fh=mysql_query($sql);
	}

?>