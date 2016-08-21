<?php
    if (isset($_SESSION['lang'])) {
        if (file_exists("../langues/".$_SESSION['lang']."-lang.edt.php")) {
            include("../langues/".$_SESSION['lang']."-lang.edt.php");
        }
        else {
            include("../langues/fr-lang.edt.php");
        }
    }
    else {
        include("../langues/fr-lang.edt.php");
    }	
?>