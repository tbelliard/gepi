<?php

require("../secure/connect.inc.php");
// Il est possible de définir pour l'accès public un utilisateur de la base mysql ayant des droits moins importants
// que l'utilisateur défini dans le fichier ci-dessus.
// En effet pour l'accès public un utilisateur ayant uniquement le droit SELECT sur la base est suffisant.

//ligne suivante : le nom de l'utilisateur mysql qui a les droits de lecture sur la base (supprimer les deux premiers caractères pour rendre active la configuration)
//$dbUser="nom_utilisateur";

//ligne suivante : le mot de passe de l'utilisateur mysql ci-dessus (supprimer les deux premiers caractères pour rendre active la configuration)
//$dbPass="mot_de_passe";


?>