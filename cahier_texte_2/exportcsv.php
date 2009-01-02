<?
// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
echo   ("Resume session") ;
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
echo   ("checkAccess") ;
    header("Location: ../logout.php?auto=1&amp;pb_checkAccess=y");
    die();
}

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
    die("Le module n'est pas activé.");
}

// Vérification : est-ce que l'utilisateur a le droit d'être ici ?
if (isset($current_group["id"]) AND ($current_group["id"] != "") ) {
    if (!check_prof_groupe($_SESSION['login'],$current_group["id"])) {
        header("Location: ../logout.php?auto=1");
        die();
    }
}

$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
if (is_numeric($id_groupe)) {
    $current_group = get_group($id_groupe);
} else {
    $current_group = false;
    die();
}

// Liste les données des tables ct_entry et ct_devoirs_entry
// -------------------------------------------
$req_notices =
    "select 'Compte rendu' type, date_ct, contenu
    from ct_entry
    where contenu != ''
    and id_groupe = '" . $current_group["id"] . "'";
$req_devoirs = 
    "select 'Devoir' type, date_ct, contenu
    from ct_devoirs_entry
    where contenu != ''
    and id_groupe = '" . $current_group["id"] ."'";
$req_union = "select * from (" . $req_notices . ") as notices UNION (" . $req_devoirs . ") order by date_ct desc";
$sql_union = mysql_query($req_union);

//html header
header("Content-Type: application/csv-tab-delimited-table");

//nom du fichier à telecharger
$str = substr($current_group["description"],0 , 4);
foreach ($current_group["classes"]["classes"] as $classe) {
    $str .= $classe["classe"];
}
header("Content-disposition: filename=".$str.date("dmY").".csv");

if (mysql_num_rows($sql_union) == 0) {
    echo("aucune donnée"); 
} else {
    // titre des colonnes
    echo ("Date,Type,Contenu");
    echo "\n";

    // données de la table
    while ($arrSelect = mysql_fetch_array($sql_union, MYSQL_ASSOC)) {
    	if ($arrSelect["date_ct"] != 0) {
        echo (strftime("%d/%m/%y", $arrSelect["date_ct"]).",");
    	} else {
    		echo "info g�n�rale ,";
    	}
        echo ($arrSelect["type"].",");
        echo ("\"".strip_tags(html_entity_decode($arrSelect["contenu"], ENT_NOQUOTES, 'UTF-8'))."\"");
       echo "\n";
    }
}
?>
