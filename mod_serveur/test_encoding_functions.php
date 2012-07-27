<?php
//ces fonctions sont utilisée dans la page test_serveur.php et dans les tests unitaires
function test_check_utf8()
{
    if (!check_utf8("auie")) {echo 'échec ligne 4 mod_serveur/test_encoding_functions.php'; return false;}     
    if (!check_utf8("bépowǜdlj")) {echo 'échec ligne 5 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8(";œ€âũ")) {echo 'échec ligne 6 mod_serveur/test_encoding_functions.php'; return false;} 
    
	    //$examples = array(
        //    "Valid ASCII" => "a",
        //    "Valid 2 Octet Sequence" => "\xc3\xb1",
        //    "Invalid 2 Octet Sequence" => "\xc3\x28",
        //    "Invalid Sequence Identifier" => "\xa0\xa1",
        //    "Valid 3 Octet Sequence" => "\xe2\x82\xa1",
        //    "Invalid 3 Octet Sequence (in 2nd Octet)" => "\xe2\x28\xa1",
        //    "Invalid 3 Octet Sequence (in 3rd Octet)" => "\xe2\x82\x28",
        //    "Valid 4 Octet Sequence" => "\xf0\x90\x8c\xbc",
        //    "Invalid 4 Octet Sequence (in 2nd Octet)" => "\xf0\x28\x8c\xbc",
        //    "Invalid 4 Octet Sequence (in 3rd Octet)" => "\xf0\x90\x28\xbc",
        //    "Invalid 4 Octet Sequence (in 4th Octet)" => "\xf0\x28\x8c\x28",
        //    "Valid 5 Octet Sequence (but not Unicode!)" => "\xf8\xa1\xa1\xa1\xa1",
        //    "Valid 6 Octet Sequence (but not Unicode!)" => "\xfc\xa1\xa1\xa1\xa1\xa1",
        //);
        
    if (!check_utf8("a")) {echo 'échec ligne 25 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8("\xc3\xb1")) {echo 'échec ligne 26 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xc3\x28")) {echo 'échec ligne 27 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xa0\xa1")) {echo 'échec ligne 28 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8("\xe2\x82\xa1")) {echo 'échec ligne 29 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xe2\x28\xa1")) {echo 'échec ligne 30 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xe2\x82\x28")) {echo 'échec ligne 31 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8("\xf0\x90\x8c\xbc")) {echo 'échec ligne 32 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xf0\x28\x8c\xbc")) {echo 'échec ligne 33 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xf0\x90\x28\xbc")) {echo 'échec ligne 34 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xf0\x28\x8c\x28")) {echo 'échec ligne 35 mod_serveur/test_encoding_functions.php'; return false;} 
    //if (!check_utf8("\xf8\xa1\xa1\xa1\xa1")) {echo 'échec ligne 36 mod_serveur/test_encoding_functions.php'; return false;}
    //if (!check_utf8("\xfc\xa1\xa1\xa1\xa1\xa1")) {echo 'échec ligne 37 mod_serveur/test_encoding_functions.php'; return false;}
    
    return true;
    
}
	
function test_detect_encoding()
{
    if ("UTF-8" != detect_encoding("auie")) {echo 'échec ligne 45 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("UTF-8" != detect_encoding("bépowǜdlj")) {echo 'échec ligne 46 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("UTF-8" != detect_encoding(";œ€âũ")) {echo 'échec ligne 47 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("UTF-8" != detect_encoding("é")) {echo 'échec ligne 48 mod_serveur/test_encoding_functions.php'; return false;}
    if ("ISO-8859-15" != detect_encoding("\xe9")) {echo 'échec ligne 48 mod_serveur/test_encoding_functions.php'; return false;}
    if ("ISO-8859-15" != detect_encoding("\xa4")) {echo 'échec ligne 48 mod_serveur/test_encoding_functions.php'; return false;}
	return true;
}

function test_ensure_utf8()
{
    if ("auie" != ensure_utf8("auie", 'UTF-8')) {echo 'échec ligne 56 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("auie" != ensure_utf8("auie")) {echo 'échec ligne 57 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("bépowǜdlj" != ensure_utf8("bépowǜdlj")) {echo 'échec ligne 58 mod_serveur/test_encoding_functions.php'; return false;} 
    if (";œ€âũ" != ensure_utf8(";œ€âũ")) {echo 'échec ligne 59 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("é" != ensure_utf8("é")) {echo 'échec ligne 60 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("é" != ensure_utf8("\xe9")) {echo 'échec ligne 61 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("€" != ensure_utf8("\xa4")) {echo 'échec ligne 62 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("€" != ensure_utf8("\xa4",'ISO-8859-15')) {echo 'échec ligne 63 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("¤" != ensure_utf8("\xa4",'ISO-8859-1')) {echo 'échec ligne 64 mod_serveur/test_encoding_functions.php'; return false;} 
    return true;
}

function test_casse_mot() {
    if (function_exists('mb_convert_case')) {
        if ("AUIE" != casse_mot("auie",'maj')) {echo 'échec ligne 83 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("auie" != casse_mot("AUIE",'min')) {echo 'échec ligne 84 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("Bépowûdlj" != casse_mot("béPowûdlj",'majf')) {echo 'échec ligne 85 mod_serveur/test_encoding_functions.php'; return false;} 
        if (";Œ€ÂŨ" != casse_mot(";œ€âũ",'maj')) {echo 'échec ligne 86 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("Bonjour Je Suis Là" != casse_mot("bonjour je suis là",'majf2')) {echo 'échec ligne 87 mod_serveur/test_encoding_functions.php'; return false;} 
    } else {
        if ("AUIE" != casse_mot("auie",'maj')) {echo 'échec ligne 89 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("auie" != casse_mot("AUIE",'min')) {echo 'échec ligne 90 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("Bepowudlj" != casse_mot("bépowǜdlj",'majf')) {echo 'échec ligne 91 mod_serveur/test_encoding_functions.php'; return false;} 
        if (";AE" != casse_mot(";âé",'maj')) {echo 'échec ligne 92 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("Bonjour Je Suis La" != casse_mot("bonjour je suis là",'majf2')) {echo 'échec ligne 93 mod_serveur/test_encoding_functions.php'; return false;}
    } 
    return true;
}

