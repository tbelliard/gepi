<?php

/* $Id$ */

$debug_import="n";

function traite_utf8($chaine) {
	// On passe par cette fonction pour pouvoir desactiver rapidement ce traitement s'il ne se revele plus necessaire
	//$retour=$chaine;

	// mb_detect_encoding($chaine . 'a' , 'UTF-8, ISO-8859-1');

	//$retour=utf8_decode($chaine);
	// utf8_decode() va donner de l'iso-8859-1 d'ou probleme sur quelques caracteres oe et OE essentiellement (7 caracteres diffèrent).

	/*
	Différences ISO 8859-15 ? ISO 8859-1
	Position
			0xA4  0xA6  0xA8  0xB4  0xB8  0xBC  0xBD  0xBE
	8859-1
			?     ?     ?     ?     ?     ?     ?     ?
	8859-15
			¤     ¦     ¨     ´     ¸     ¼     ½     ¾
	*/

	//$retour=recode_string("utf8..iso-8859-15", $chaine);
	// recode_string est absent la plupart du temps

	$retour=utf8_decode($chaine);

	return $retour;
}

/*
//================================================
// Correspondances de caractères accentués/désaccentués
$liste_caracteres_accentues   ="ÂÄÀÁÃÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕØ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõø¨ûüùúýÿ¸";
$liste_caracteres_desaccentues="AAAAAACEEEEIIIINOOOOOOSUUUUYYZaaaaaaceeeeiiiinooooooosuuuuyyz";
//================================================

function remplace_accents($chaine) {
	global $liste_caracteres_accentues, $liste_caracteres_desaccentues;
	$retour=strtr(preg_replace("/Æ/","AE",preg_replace("/æ/","ae",preg_replace("/¼/","OE",preg_replace("/½/","oe","$chaine"))))," '$liste_caracteres_accentues","__$liste_caracteres_desaccentues");
	return $retour;
}
*/

?>
