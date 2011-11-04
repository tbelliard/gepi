<?php
//ces fonctions sont utilis√©e dans la page test_serveur.php et dans les tests unitaires
function test_check_utf8()
{
    if (!check_utf8("auie")) {echo 'Èchec ligne 4 mod_serveur/test_encoding_functions.php'; return false;}     
    if (!check_utf8("b√©pow«údlj")) {echo 'Èchec ligne 5 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8(";≈ì‚Ç¨√¢≈©")) {echo 'Èchec ligne 6 mod_serveur/test_encoding_functions.php'; return false;} 
    
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
        
    if (!check_utf8("a")) {echo 'Èchec ligne 25 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8("\xc3\xb1")) {echo 'Èchec ligne 26 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xc3\x28")) {echo 'Èchec ligne 27 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xa0\xa1")) {echo 'Èchec ligne 28 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8("\xe2\x82\xa1")) {echo 'Èchec ligne 29 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xe2\x28\xa1")) {echo 'Èchec ligne 30 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xe2\x82\x28")) {echo 'Èchec ligne 31 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8("\xf0\x90\x8c\xbc")) {echo 'Èchec ligne 32 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xf0\x28\x8c\xbc")) {echo 'Èchec ligne 33 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xf0\x90\x28\xbc")) {echo 'Èchec ligne 34 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xf0\x28\x8c\x28")) {echo 'Èchec ligne 35 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8("\xf8\xa1\xa1\xa1\xa1")) {echo 'Èchec ligne 36 mod_serveur/test_encoding_functions.php'; return false;}
    if (!check_utf8("\xfc\xa1\xa1\xa1\xa1\xa1")) {echo 'Èchec ligne 37 mod_serveur/test_encoding_functions.php'; return false;}
    
    return true;
    
}
	
function test_detect_encoding()
{
    if ("UTF-8" != detect_encoding("auie")) {echo 'Èchec ligne 45 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("UTF-8" != detect_encoding("b√©pow«údlj")) {echo 'Èchec ligne 46 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("UTF-8" != detect_encoding(";≈ì‚Ç¨√¢≈©")) {echo 'Èchec ligne 47 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("UTF-8" != detect_encoding("√©")) {echo 'Èchec ligne 48 mod_serveur/test_encoding_functions.php'; return false;}
    if ("ISO-8859-15" != detect_encoding("\xe9")) {echo 'Èchec ligne 48 mod_serveur/test_encoding_functions.php'; return false;}
    if ("ISO-8859-15" != detect_encoding("\xa4")) {echo 'Èchec ligne 48 mod_serveur/test_encoding_functions.php'; return false;}
	return true;
}

function test_ensure_utf8()
{
    if ("auie" != ensure_utf8("auie", 'UTF-8')) {echo 'Èchec ligne 56 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("auie" != ensure_utf8("auie")) {echo 'Èchec ligne 57 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("b√©pow«údlj" != ensure_utf8("b√©pow«údlj")) {echo 'Èchec ligne 58 mod_serveur/test_encoding_functions.php'; return false;} 
    if (";≈ì‚Ç¨√¢≈©" != ensure_utf8(";≈ì‚Ç¨√¢≈©")) {echo 'Èchec ligne 59 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("√©" != ensure_utf8("√©")) {echo 'Èchec ligne 60 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("√©" != ensure_utf8("\xe9")) {echo 'Èchec ligne 61 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("‚Ç¨" != ensure_utf8("\xa4")) {echo 'Èchec ligne 62 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("‚Ç¨" != ensure_utf8("\xa4",'ISO-8859-15')) {echo 'Èchec ligne 63 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("¬§" != ensure_utf8("\xa4",'ISO-8859-1')) {echo 'Èchec ligne 64 mod_serveur/test_encoding_functions.php'; return false;} 
    return true;
}

function test_remplace_accents()
{
    if ("auie" != remplace_accents("auie")) {echo 'Èchec ligne 70 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("auie'\"" != remplace_accents("auie'\"")) {echo 'Èchec ligne 71 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("bepowudlj" != remplace_accents("b√©pow√ªdlj")) {echo 'Èchec ligne 72 mod_serveur/test_encoding_functions.php'; return false;}
    if ("u" != remplace_accents("«ú") && "_" != remplace_accents("«ú")) {echo 'Èchec ligne 73 mod_serveur/test_encoding_functions.php'; return false;}
    if ("e" != remplace_accents("\xe9")) {echo 'Èchec ligne 74 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("'\" ." != remplace_accents("'\" .")) {echo 'Èchec ligne 75 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("__ e" != remplace_accents("'\" √©",'all_nospace')) {echo 'Èchec ligne 76 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("___e" != remplace_accents("'\" √©",'all')) {echo 'Èchec ligne 77 mod_serveur/test_encoding_functions.php'; return false;} 
    return true;
}

function test_casse_mot() {
    if (function_exists('mb_convert_case')) {
        if ("AUIE" != casse_mot("auie",'maj')) {echo 'Èchec ligne 83 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("auie" != casse_mot("AUIE",'min')) {echo 'Èchec ligne 84 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("B√©pow√ªdlj" != casse_mot("b√©Pow√ªdlj",'majf')) {echo 'Èchec ligne 85 mod_serveur/test_encoding_functions.php'; return false;} 
        if (";≈í‚Ç¨√Ç≈®" != casse_mot(";≈ì‚Ç¨√¢≈©",'maj')) {echo 'Èchec ligne 86 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("Bonjour Je Suis L√†" != casse_mot("bonjour je suis l√†",'majf2')) {echo 'Èchec ligne 87 mod_serveur/test_encoding_functions.php'; return false;} 
    } else {
        if ("AUIE" != casse_mot("auie",'maj')) {echo 'Èchec ligne 89 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("auie" != casse_mot("AUIE",'min')) {echo 'Èchec ligne 90 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("Bepowudlj" != casse_mot("b√©pow«údlj",'majf')) {echo 'Èchec ligne 91 mod_serveur/test_encoding_functions.php'; return false;} 
        if (";AE" != casse_mot(";√¢√©",'maj')) {echo 'Èchec ligne 92 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("Bonjour Je Suis La" != casse_mot("bonjour je suis l√†",'majf2')) {echo 'Èchec ligne 93 mod_serveur/test_encoding_functions.php'; return false;}
    } 
    return true;
}

