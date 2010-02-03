<?php
if ($IE6) {
	echo "<div class=\"cadreInformation\">Votre navigateur (Internet Explorer 6) est obsolète et se comporte mal vis à vis de l'affichage des emplois du temps. Faites absolument une mise à jour vers les versions 7 ou 8 ou changez de navigateur (FireFox, Chrome, Opera, Safari)</div>";
}


// On ajoute le menu EdT
require_once("./menu.inc.php");


echo "<br />\n";
echo '<div id="lecorps">';

require_once("./menu.inc.new.php");

// ========================= AFFICHAGE DES MESSAGES

if ($message != "") {
    echo ("<div class=\"cadreInformation\">".$message."</div>");
}

// ========================= AFFICHAGE DE LA BAR DE COMMUTATION DES PERIODES

if ($DisplayPeriodBar) {
        AfficheBarCommutateurPeriodes($login_edt, $visioedt, $type_edt_2);
}

//=========================== AFFICHAGE DES MENUS DEROULANTS DE SELECTION
if (isset($visioedt)) {
    if (!$IE6) {
        echo ("<div class=\"fenetre\">\n");
        echo("<div class=\"contenu\">
		    <div class=\"coingh\"></div>
            <div class=\"coindh\"></div>
            <div class=\"partiecentralehaut\"></div>
            <div class=\"droite\"></div>
            <div class=\"gauche\"></div>
            <div class=\"coingb\"></div>
		    <div class=\"coindb\"></div>
		    <div class=\"partiecentralebas\"></div>\n");
    }        
    echo "<span class=\"legende\">".TITLE_VOIR_EDT."</span>\n";
    
    // ======================= AFFICHAGE DU SELECTEUR
    
    if (isset($visioedt) AND $visioedt == "prof1") {
	    require_once("./voir_edt_prof.php");
    }
    
    elseif (isset($visioedt) AND $visioedt == "salle1") {
	    require_once("./voir_edt_salle.php");
    }
    
    elseif (isset($visioedt) AND $visioedt == "classe1") {
	    require_once("./voir_edt_classe.php");
    }
    elseif (isset($visioedt) AND $visioedt == "eleve1") {
	    require_once("./voir_edt_eleve.php");
    }
    
    if (!$IE6) {
        echo "</div>";
        echo "</div>";
    }
}
// ========================= AFFICHAGE DES EMPLOIS DU TEMPS

if ($DisplayEDT) {
        AfficheImprimante(true);
        AfficheBascule(true, $login_edt, $visioedt, $type_edt_2);
        AfficherEDT($tab_data, $entetes, $creneaux, $type_edt, $login_edt, $_SESSION['period_id']);
}

echo '</div>';
?>