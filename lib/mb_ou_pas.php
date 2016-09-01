<?php
/*
*/

//========================================


$modifier_mb="n";
$initialiser_mb="n";
$sql="SELECT value FROM setting WHERE name='utiliser_mb';";
//echo "$sql<br />";
   
$res = mysqli_query($mysqli, $sql);
if($res->num_rows == 0) {
	$initialiser_mb = "y";
}
else {
	$lig_tmp = $res->fetch_object();
	$utiliser_mb = $lig_tmp->value;
	if(($utiliser_mb != 'y') && ($utiliser_mb != 'n')) {
		$modifier_mb = "y";
	}
} 

$phpversion=phpversion();
$tab_tmp=explode(".",$phpversion);
if(($tab_tmp[0]>=5)&&($tab_tmp[1]>=3)) {
	$val_tmp='y';
}
else {
	$val_tmp='n';
}
// On pourrait aussi utiliser if(function_exists("mb_ereg")) pour le test...

if($initialiser_mb=="y") {
	$sql="INSERT INTO setting SET name='utiliser_mb', value='$val_tmp';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
elseif(($modifier_mb=="y")||($utiliser_mb!=$val_tmp)) {
	$sql="UPDATE setting SET value='$val_tmp' WHERE name='utiliser_mb';";
	$update=mysqli_query($GLOBALS["mysqli"], $sql);
}
$utiliser_mb=$val_tmp;

//========================================
function appel_test_mb() {
	test_mb();
}

function test_mb() {
	global $utiliser_mb;
	echo "<p>\$utiliser_mb=$utiliser_mb</p>\n";
}
//========================================

//mb_ereg ( string $pattern , string $string [, array $regs ] )
//(PHP 4 >= 4.2.0, PHP 5)
function my_ereg($motif,$chaine,$tableau=NULL) {
//function my_ereg($motif,$chaine,$tableau=array()) {
	global $utiliser_mb;
	//global $tableau;
	if($utiliser_mb=='y') {
		if($tableau!=NULL) {
			return mb_ereg($motif,$chaine,$tableau);
		}
		else {
			return mb_ereg($motif,$chaine);
		}
	}
	else {
		if($tableau!=NULL) {
			return ereg($motif,$chaine,$tableau);
		}
		else {
			return ereg($motif,$chaine);
		}
	}
}

/**
 * Remplace motif par remplacement
 * 
 * 
 * mb_ereg_replace ( string $pattern , string $replacement , string $string [, string $option= "msr" ] )
 * (PHP 4 >= 4.2.0, PHP 5)
 * 
 * À partir de PHP 5.7, on n'utilise plus ereg_replace qui produit une erreur
 * 
 * @global string $utiliser_mb y ou autre chose
 * @param string $motif
 * @param string $remplacement
 * @param string $chaine
 * @return string
 */
function my_ereg_replace($motif,$remplacement,$chaine) {
	global $utiliser_mb;
	if($utiliser_mb=='y' || version_compare(phpversion(), '5.6', '>')) {
		return mb_ereg_replace($motif,$remplacement,$chaine);
	}
	else {
		return ereg_replace($motif,$remplacement,$chaine);
	}
}

//mb_eregi ( string $pattern , string $string [, array $regs ] )
//(PHP 4 >= 4.2.0, PHP 5)
function my_eregi($motif,$chaine,$tableau=NULL) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		if($tableau!=NULL) {
			return mb_eregi($motif,$chaine,$tableau);
		}
		else {
			return mb_eregi($motif,$chaine);
		}
	}
	else {
		if($tableau!=NULL) {
			return eregi($motif,$chaine,$tableau);
		}
		else {
			return eregi($motif,$chaine);
		}
	}
}

//mb_eregi_replace ( string $pattern , string $replace , string $string [, string $option= "msri" ] )
//(PHP 4 >= 4.2.0, PHP 5)
function my_eregi_replace($motif,$remplacement,$chaine) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		return mb_eregi_replace($motif,$remplacement,$chaine);
	}
	else {
		return eregi_replace($motif,$remplacement,$chaine);
	}
}

//mb_strlen ( string $str [, string $encoding ] )
//(PHP 4 >= 4.0.6, PHP 5)
function my_strlen($chaine,$encodage=NULL) {
  return mb_strlen($chaine,'utf8');
}

//split ( string pattern, string string [, int limit])
//(PHP 4 >= 4.2.0, PHP 5)
function my_split($motif, $chaine, $int_limit=NULL) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		if($int_limit!=NULL) {
			return mb_split($motif, $chaine, $int_limit);
		}
		else {
			return mb_split($motif, $chaine);
		}
	}
	else {
		if($int_limit!=NULL) {
			return split($motif, $chaine, $int_limit);
		}
		else {
			return split($motif, $chaine);
		}
	}
}

//stristr ( string haystack, string needle)
//(PHP 5 >= 5.2.0)
function my_stristr($chaine,$motif) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		return mb_stristr($chaine);
	}
	else {
		return stristr($chaine);
	}
}

//mb_parse_str ( string $encoded_string [, array &$result ] )
//(PHP 4 >= 4.0.6, PHP 5)
function my_parse_str($chaine, $tableau=NULL) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		if($tableau!=NULL) {
			return mb_parse_str($chaine, $tableau);
		}
		else {
			return mb_parse_str($chaine);
		}
	}
	else {
		if($tableau!=NULL) {
			return parse_str($chaine, $tableau);
		}
		else {
			return parse_str($chaine);
		}
	}
}



//mail ( string to, string subject, string message [, string additional_headers [, string additional_parameters]])
//mail ( string $to , string $subject , string $message [, string $additional_headers [, string $additional_parameters ]] )
//(PHP 4, PHP 5)
//mb_send_mail ( string $to , string $subject , string $message [, string $additional_headers= NULL [, string $additional_parameter= NULL ]] )
//(PHP 4 >= 4.0.6, PHP 5)
function my_mail($to, $sujet, $message, $headers=NULL, $param=NULL) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		//if($headers!=NULL) {
		if(($headers!=NULL)||($param!=NULL)) {
			if($param!=NULL) {
				return mb_send_mail($to, $sujet, $message, $headers, $param);
			}
			else {
				return mb_send_mail($to, $sujet, $message, $headers);
			}
		}
		else {
			return mb_send_mail($to, $sujet, $message);
		}
	}
	else {
		//if($headers!=NULL) {
		if(($headers!=NULL)||($param!=NULL)) {
			if($param!=NULL) {
				return mail($to, $sujet, $message, $headers, $param);
			}
			else {
				return mail($to, $sujet, $message, $headers);
			}
		}
		else {
			return mail($to, $sujet, $message);
		}
	}
}

//strpos ( string haystack, string needle [, int offset])
//(PHP 4 >= 4.0.6, PHP 5)
function my_strpos($chaine, $motif, $int_offset=NULL) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		if($int_limit!=NULL) {
			return mb_strpos($chaine, $motif, $int_offset);
		}
		else {
			return mb_strpos($chaine, $motif);
		}
	}
	else {
		if($int_limit!=NULL) {
			return strpos($chaine, $motif, $int_offset);
		}
		else {
			return strpos($chaine, $motif);
		}
	}
}

//mb_stripos ( string $haystack , string $needle [, int $offset [, string $encoding ]] )
//(PHP 5 >= 5.2.0)
function my_stripos($chaine, $motif, $int_offset=NULL) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		if($int_limit!=NULL) {
			return mb_stripos($chaine, $motif, $int_offset);
		}
		else {
			return mb_stripos($chaine, $motif);
		}
	}
	else {
		if($int_limit!=NULL) {
			return stripos($chaine, $motif, $int_offset);
		}
		else {
			return stripos($chaine, $motif);
		}
	}
}

//strrchr ( string haystack, string needle)
//(PHP 5 >= 5.2.0)
function my_strrchr($chaine,$motif) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		return mb_strrchr($chaine,$motif);
	}
	else {
		return strrchr($chaine,$motif);
	}
}

//strstr ( string haystack, string needle)
//(PHP 5 >= 5.2.0)
function my_strstr($chaine,$motif) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		return mb_strstr($chaine,$motif);
	}
	else {
		return strstr($chaine,$motif);
	}
}

/*
//strtolower ( string str)
//(PHP 4 >= 4.3.0, PHP 5)
function my_strtolower($chaine) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		return mb_strtolower($chaine);
	}
	else {
		return strtolower($chaine);
	}
}

//strtoupper ( string string)
//(PHP 4 >= 4.3.0, PHP 5)
function my_strtoupper($chaine) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		return mb_strtoupper($chaine);
	}
	else {
		return strtoupper($chaine);
	}
}
*/

//substr ( string string, int start [, int length])
//(PHP 4 >= 4.0.6, PHP 5)
function my_substr($chaine, $indice, $longueur=NULL) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		if($int_limit!=NULL) {
			return mb_substr($chaine, $indice, $longueur);
		}
		else {
			return mb_substr($chaine, $indice);
		}
	}
	else {
		if($int_limit!=NULL) {
			return substr($chaine, $indice, $longueur);
		}
		else {
			return substr($chaine, $indice);
		}
	}
}

//mb_substr_count ( string $haystack , string $needle [, string $encoding ] )
//(PHP 4 >= 4.3.0, PHP 5)
function my_substr_count($chaine, $motif, $encodage=NULL) {
	global $utiliser_mb;
	if($utiliser_mb=='y') {
		if($int_limit!=NULL) {
			return mb_substr_count($chaine, $motif, $encodage);
		}
		else {
			return mb_substr_count($chaine, $motif);
		}
	}
	else {
		//if($int_limit!=NULL) {
		//	return substr_count($chaine, $motif, $encodage);
		//}
		//else {
			return substr_count($chaine, $motif);
		//}
	}
}

?>