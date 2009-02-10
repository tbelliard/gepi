<?PHP
//fonction qui renvoie l'extension du fichier
function extension_nom_fichier($nom_fichier) {
  $lng =strlen($nom_fichier);
  $ext=substr($nom_fichier,$lng-3,3);
  return $ext;
}

//fonction qui renvoie le nom de l'image à télécharger
function renvoi_nom_image ($extension) {
	switch ($extension) {
	case "odt":
	    return "stock_new-text-36.png";
	    break;
	case "odf":
	    return "stock_new-spreadsheet-36.png";
	    break;
	}
}


//$repaussi==true ~> efface aussi $rep
//retourne true si tout s'est bien passé,
//false si un fichier est resté (problème de permission ou attribut lecture sous Win
//dans tous les cas, le maximum possible est supprimé.
function deltree($rep,$repaussi=true) {
static $niv=0;
$niv++;
if (!is_dir($rep)) return false;
$handle=opendir($rep);
if (!$handle) return false;
while ($entree=readdir($handle)) {
    if (is_dir($rep.'/'.$entree)) {
        if ($entree!='.' && $entree!='..') {
            $ok=deltree($rep.'/'.$entree);
        }
        else $ok=true;
    }
    else {
        $ok=@unlink($rep.'/'.$entree);
    }
}
closedir($handle);
$niv--;
if ($niv || $repaussi) $ok &= @rmdir($rep);
return $ok;
}


function creertousrep($fic) {
//crée tous les répertoires intermédiaires s'il n'existent pas
//$fic est de la forme '../rep1/rep2/.../repn/bloub' et sont créés :
//../rep1, ../rep1/rep2, ../rep1/rep2/.../repn
//bloub peut ne pas exister ($fic se termine par / donc c'est un rép.)
$p=strpos($fic,'/');
while ($p<strlen($fic) && $p=strpos($fic,'/',$p+1)) {
    //print substr($fic,0,$p);
    if (!is_dir($fic)) @mkdir(substr($fic,0,$p),0777);
}
return $fic;
}



//Du code PHP écrit, dans le flux HTML, des lignes qui appellent des fonctions javascript.
//Ces fonctions effectuent directement ce que leur nom indique.
//L'avantage est d'éviter de s'encombrer de la quincaillerie javascript,
//du genre <script language='JavaScript'> (voir fonction js) et d'être obligé
//de "sortir" d'un script PHP pour écrire des lignes de code js

//pour plusieurs lignes de JS, on pourra écrire :
//     js_begin(); js_ins($y); ...; js_ins($d); js_end;
function js_begin() {print "\n<script language=\"JavaScript\">\n";}
function js_ins($instructionJS) {print "\t$instructionJS\n";}
function js_end() {print "</script>\n";}

//pour une seule ligne on pourra simplifier avec cet appel :
function js($instructionJS) {
    js_begin();
    print "\t".$instructionJS."\n";
    js_end();
}

//==================================
//Fonctions d'importation javascript
//==================================

//comme son homologue JS
function alert($message) {
  $message=addcslashes(addslashes($message),"\n\t");
  js("alert('$message')");
}
function goto($url) {
  js("window.location.replace('$url');");
}

function gohistory($n) {
//$n est soit un nombre entier qconque (souvent -1 mais aussi 0),
//soit une partie d'une URL de l'historique
  js("window.history.go($n);");
}

//confirm : affiche la $question et prend la décision appropriée (l'un des 2 paramètres)
//ces 2 paramètres (éventuellement vides) doivent être des instructions JS.
//pour charger une URL mettre : "window.location.href='mon_URL.php';"
//pour "ne rien faire" (c'est à dire terminer le script js) mettre "" ou "stop();"
//Penser à séparer les instructions js par des ; et TERMINER par un ; !!!
function confirm($question,$jsYes,$jsNo) {
  $question=addslashes($question);
  js_begin();
  js_ins("if (confirm('$question')) {");
  js_ins("\t $jsYes }");
  js_ins("else {");
  js_ins("\t $jsNo } ");
  js_end();
}

function lien_popup($url, $idfen, $message) {
print "<a href='javascript:void(0)' ".
         "onClick=\"window.open('$url','$idfen',".
         "'width=500,height=200, ".
         "status=0, directories=0, toolbar=0, location=0, scrollbars=0, ".
         "resizable=0');\" >".$message."</a>";
}

function close_window() {
 js_begin();
 js_ins("window.close();");
 js_end();

}

//$inverse 'o' ou 'n'
//$motif : le motif de séparation - ou /
function datemysql_to_jj_mois_aaaa ($date,$motif,$inverse) {
    if ($inverse=='o') {
	    list($annee, $mois, $jour) = explode($motif, $date);
	} else {
	    list($jour, $mois, $annee) = explode($motif, $date);
	}
	$les_mois = array("janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre");
	return $jour." ".$les_mois[$mois-1]." ".$annee;
}
?>