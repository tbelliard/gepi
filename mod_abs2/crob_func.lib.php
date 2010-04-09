<?php
/*
 $Id$
*/

$abs2_debug=0;
function abs2_debug($texte) {
    global $abs2_debug;
    if($abs2_debug==1) {echo $texte;}
}

function formate_sec_to_heure_min($sec) {
    $h=floor($sec/3600);
    $m=max(0,floor(($sec-$h*3600)/60));
    return sprintf("%02d",$h)."H".sprintf("%02d",$m);
}

function formate_time_mysql_to_heure_min($chaine) {
    $tab=explode(":",$chaine);
    return $tab[0].":".$tab[1];
}

function jour_semaine($jour) {
    if($jour==0) {return "lundi";}
    elseif($jour==1) {return "mardi";}
    elseif($jour==2) {return "mercredi";}
    elseif($jour==3) {return "jeudi";}
    elseif($jour==4) {return "vendredi";}
    elseif($jour==5) {return "samedi";}
    elseif($jour==6) {return "dimanche";}
}

?>
