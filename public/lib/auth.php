<?php
/*
    Réalisation     : BIOT Nicolas pour PHPindex
    Contact        : BIOT Nicolas <nicolas@globalis-ms.com>

    ----------------------------------------------------------
    Fichier        : auth.inc.php
    Description    : Script d'authentification
    Date création    : 14/05/2001
    Date de modif    : 10/11/2001 Antoine Bajolet
*/
$user = getSettingValue("cahiers_texte_login_pub");
$pwd = getSettingValue("cahiers_texte_passwd_pub");

// PHP en mode CGI
// les lignes qui suivent ne sont pas nécessaires en PHP 5.3
// voir : http://www.php.net/manual/fr/features.http-auth.php#106285
/*
//set http auth headers for apache+php-cgi work around
if (isset($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/Basic\s+(.*)$/i', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
    list($name, $password) = explode(':', base64_decode($matches[1]));
    $_SERVER['PHP_AUTH_USER'] = strip_tags($name);
    $_SERVER['PHP_AUTH_PW'] = strip_tags($password);
}

//set http auth headers for apache+php-cgi work around if variable gets renamed by apache
if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && preg_match('/Basic\s+(.*)$/i', $_SERVER['REDIRECT_HTTP_AUTHORIZATION'], $matches)) {
    list($name, $password) = explode(':', base64_decode($matches[1]));
    $_SERVER['PHP_AUTH_USER'] = strip_tags($name);
    $_SERVER['PHP_AUTH_PW'] = strip_tags($password);
*/

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $PHP_AUTH_USER = $_SERVER['PHP_AUTH_USER'];
}
if (isset($_SERVER['PHP_AUTH_PW'])) {
    $PHP_AUTH_PW = $_SERVER['PHP_AUTH_PW'];
}

function phpdigAuth(){
    Header("WWW-Authenticate: Basic realm=\"Cahiers de texte\"");
    Header("HTTP/1.0 401 Unauthorized");

    require("secure/connect.inc.php");
    //**************** EN-TETE *****************
    $titre_page = "Cahiers de texte";
    require_once("lib/header_public.inc.php");

    //**************** FIN EN-TETE *****************
    echo "<H3><center>En raison du caractère personnel du contenu, ce site est soumis à des restrictions utilisateurs.
    <br />Pour accéder aux cahiers de texte, vous devez demander auprès de l'administrateur,
    <br />le nom d'utilisateur et le mot de passe.</center></H3>";
    echo "</body></html>";
    exit();
}

if (($user!='') and ($pwd!=''))
{
if( !isset($PHP_AUTH_USER) && !isset($PHP_AUTH_PW) ) {
    phpdigAuth();
}
else {
    if( $PHP_AUTH_USER==$user && $PHP_AUTH_PW==$pwd ) {
        // la suite du script sera exécutée
    }
    else{
        // rappel de la fonction d'identification
        phpdigAuth();
    }
}
}
?>