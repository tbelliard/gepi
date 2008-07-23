<?php
#
# Vous devez renommer ce fichier en config_ldap.inc.php pour qu'il soit pris en compte !
#

# Les lignes suivantes sont à modifier selon votre configuration

# adresse de l'annuaire LDAP.
# Si c'est le même que celui qui heberge les scripts, mettre "localhost"
$ldap_host="localhost";     # Exemple : localhost, 192.168.1.1

# port utilisé
$ldap_port="389";

# identifiant et mot de passe dans le cas d'un accès non anonyme
$ldap_login="";
$ldap_password="";

# chemin d'accès dans l'annuaire (= BaseDN)
# Exemple pour Eole : "o=gouv,c=fr"
$ldap_base_dn="o=gouv,c=fr";

# Complément de chemin où sont listés les utilisateurs
# Ce paramètre est placé devant le BaseDN lors des requêtes.
$ldap_people_ou = "ou=People";

# Les classes de l'entrée LDAP d'un utilisateur. Elles doivent
# être cohérentes avec les attributs utilisés.
$ldap_people_object_classes = array("top","person","inetOrgPerson");

# Différents noms de champs contenant des informations indispensables
# pour Gepi. Si certaines équivalences ne sont pas renseignées, l'information
# ne sera pas importée.
$ldap_champ_login = "uid";
$ldap_champ_prenom = "";
$ldap_champ_nom = "";
$ldap_champ_nom_complet = ""; 	# Si ce champ est renseigné, il sera utilisé en combinaison avec le champ
								# prénom ou nom pour déterminer l'attribut manquant.
$ldap_champ_email = "";
$ldap_champ_civilite = "";
$ldap_champ_statut = "";

$ldap_code_civilite_madame = "";
$ldap_code_civilite_monsieur = "";
$ldap_code_civilite_mademoiselle = "";

# Options supplémentaires
# Type de cryptage utilisé pour la génération du mot de passe (accès en écriture) :
$ldap_password_encryption = "crypt"; # clear, crypt, md5, ssha

# Les attributs ci-dessous permettent de déterminer quel
# statut donner à des utilisateurs importés à la volée
# depuis le LDAP.
# Le test est effectué sur la chaîne du DN. Ces attributs
# ne sont donc utiles que dans l'hypothèse où le DN contient
# une information fiable quant au statut de l'utilisateur.
# Remarque : ces paramètres ne sont utilisés que pour l'accès au LDAP
# en lecture (importation). L'accès en écriture ne prend en compte
# qu'un éventuel attribut statut (voir $ldap_champ_statut).
$ldap_chaine_dn_statut_professeur = "";
$ldap_chaine_dn_statut_eleve = "";
$ldap_chaine_dn_statut_responsable = "";
$ldap_chaine_dn_statut_scolarite = "";
$ldap_chaine_dn_statut_cpe = "";
?>