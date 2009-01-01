<?php
// Pour les scripts situés à la racine de GEPI
if (isset($niveau_arbo) and ($niveau_arbo == "0")) {
   // Database configuration file
   require_once("./secure/connect.inc.php");
   //propel objects
   set_include_path("./orm/propel-build/classes" . PATH_SEPARATOR . "./orm" . PATH_SEPARATOR . get_include_path());
   require_once("propel/Propel.php");
   require_once("propel/logger/BasicFileLogger.php");
   $logger = new BasicFileLogger();
   Propel::setLogger($logger);
   Propel::init("./orm/propel-build/conf/gepi-conf.php");

// Pour les scripts situés dans un sous-répertoire à l'intérieur d'une sous-répertoire de GEPI
} else if (isset($niveau_arbo) and ($niveau_arbo == "2")) {
   // Database configuration file
   require_once("../../secure/connect.inc.php");
   //propel objects
   set_include_path("../../orm/propel-build/classes" . PATH_SEPARATOR . "../../orm/propel" . PATH_SEPARATOR . get_include_path());
   require_once("propel/Propel.php");
   require_once("propel/logger/BasicFileLogger.php");
   $logger = new BasicFileLogger();
   Propel::setLogger($logger);
   Propel::init("../../orm/propel-build/conf/gepi-conf.php");

// Pour les scripts situés dans un sous-sous-répertoire à l'intérieur d'une sous-répertoire de GEPI
} else if (isset($niveau_arbo) and ($niveau_arbo == "3")) {
   // Database configuration file
   require_once("../../../secure/connect.inc.php");
   //propel objects
   set_include_path("../../../orm/propel-build/classes" . PATH_SEPARATOR . "../../../orm/propel" . PATH_SEPARATOR . get_include_path());
   require_once("propel/Propel.php");
   require_once("propel/logger/BasicFileLogger.php");
   $logger = new BasicFileLogger();
   Propel::setLogger($logger);
   Propel::init("../../../orm/propel-build/conf/gepi-conf.php");

// Pour les scripts situés dans le sous-répertoire "public"
// Ces scripts font appel au fichier /public/secure/connect.inc et non pas /secure/connect.inc
} else if (isset($niveau_arbo) and ($niveau_arbo == "public")) {
    // Database configuration file
    require_once("../secure/connect.inc.php");
	//propel objects
    set_include_path("../orm/propel-build/classes" . PATH_SEPARATOR . "../orm/propel" . PATH_SEPARATOR . get_include_path());
    require_once("propel/Propel.php");
    require_once("propel/logger/BasicFileLogger.php");
    $logger = new BasicFileLogger();
    Propel::setLogger($logger);
    Propel::init("../orm/propel-build/conf/gepi-conf.php");

// Pour les scripts situés dans un sous-répertoire GEPI
} else {
   // Database configuration file
   require_once("../secure/connect.inc.php");
	//propel objects
   set_include_path("../orm/propel-build/classes" . PATH_SEPARATOR . "../orm/propel" . PATH_SEPARATOR . get_include_path());
   require_once("../orm/propel/Propel.php");
   require_once("../orm/propel/logger/BasicFileLogger.php");
   $logger = new BasicFileLogger();
   Propel::setLogger($logger);
   Propel::init("../orm/propel-build/conf/gepi-conf.php");
}
?>