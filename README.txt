GEPI-1.5.5
==============

Eric ABGRALL <eric.abgrall@free.fr>
Thomas BELLIARD <thomas.belliard@free.fr>
Didier BLANQUI <didier.blanqui@ac-toulouse.fr>
Stephane BOIREAU <stephane.boireau@ac-rouen.fr>
Regis BOUGUIN <regis.bouguin@laposte.net>
Laurent DELINEAU <laurent.delineau@ac-poitiers.fr>
Jerome ETHEVE <jerome.etheve@gmail.com>
Pascal FAUTRERO <admin.paulbert@gmail.com>
Josselin JACQUARD <josselin.jacquard@gmail.com>
Julien JOCAL <collegerb@free.fr>
Eric LEBRUN <eric.lebrun@ac-poitiers.fr>

http://gepi.mutualibre.org/


GEPI (Gestion des Elèves Par Internet) est une application développée en PHP/MySQL/HTML
dont les fonctions s'articulent autour d'un objectif : permettre la gestion et surtout le suivi des résultats scolaires
des élèves, et tout ce qui y est attaché, par une interface Web. Cela signifie, entre autre,

* la saisie des notes via un module "carnet de notes",
* leur consultation,
* la saisie des appréciations des professeurs,
* l'édition des bulletins scolaires,
* un module "cahier de texte",
* un outil trombinoscope
* un module de saisie des absences par les professeurs
* un module emploi du temps / calendrier

1. Installation
2. Licence
3. Remarques concernant la sécurité
4. Développements en cours
5. Documentation


1. Installation
=======================================

Pour obtenir une description complète de la procédure d'installation,
veuillez vous reporter au fichier "INSTALL.txt".

Pour une installation simplifiée, décompressez simplement cette archive sur un
serveur, et indiquez l'adresse où se trouvent les fichiers extraits dans un navigateur (ex: http://www.monsite.fr/gepi).

* Préalables pour l'installation automatisée :
- disposer d'un espace FTP sur un serveur avec PHP 5 ou supérieur, pour y transférer les fichiers
- disposer d'une base de données MySQL (adresse du serveur MySQL, login, mot
  de passe)


2. Licence
=======================================

GEPI est publié sous les termes de la GNU General Public Licence, dont le
contenu est disponible dans le fichier "COPYING.txt", en anglais.
GEPI est gratuit, vous pouvez le copier, le distribuer, et le modifier, à
condition que chaque partie de GEPI réutilisée ou modifiée reste sous licence
GNU GPL.
Par ailleurs et dans un soucis d'efficacité, merci de rester en contact avec
l'équipe de développement de GEPI pour éventuellement intégrer vos
contributions à une distribution ultérieure.

Enfin, GEPI est livré en l'état sans aucune garantie. Les auteurs de cet outil
ne pourront en aucun cas être tenus pour responsables d'éventuels bugs.


3. Remarques concernant la sécurité
=======================================

La sécurisation de GEPI est un point crucial, étant donné la sensibilité des
données enregistrées. Malheureusement la sécurisation de GEPI est dépendante
de celle du serveur. Nous vous recommandons d'utiliser un serveur Apache sous
Linux, en utilisant le protocole https (transferts de données cryptées), et en
veillant à toujours utiliser les dernières versions des logiciels impliqués
(notamment Apache et PHP). GEPI n'a pas encore été testé sur d'autres
serveurs.

L'EQUIPE DE DEVELOPPEMENT DE GEPI NE SAURAIT EN AUCUN CAS ETRE TENUE
POUR RESPONSABLE EN CAS D'INTRUSION EXTERIEURE LIEE A UNE FAIBLESSE DE GEPI OU
DE SON SUPPORT SERVEUR.

Abonnez-vous à la liste de diffusion 'gepi-users' pour être tenu informé des
mises à jours en matière de sécurité et pour participer
aux discussions relatives à l'utilisation et au développement de Gepi.
https://lists.sylogix.net/mailman/listinfo/gepi-users


4. Développements en cours
=======================================

Les développeurs de Gepi travaillent en fonction des besoins de leurs établissements
respectifs. N'hésitez pas à leur suggérer des fonctionnalités, par le biais
de la liste de diffusion des utilisateurs.


5. Documentation
=======================================

La documentation de Gepi se trouve à l'adresse suivante :
http://www.sylogix.org/projects/gepi/wiki