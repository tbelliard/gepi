#**************** BASE DE DONNEES DE TEST gepi ****************
# Le : 22 07 2010 a 11h 06
# Serveur : 127.0.0.1
# Version PHP : 5.2.6
# Version mySQL : 5.0.67
# IP Client : 127.0.0.1
# Fichier SQL compatible PHPMyadmin
#
# ******* debut du fichier ********


#
# table absences_rb
#


#
# table absences_repas
#


#
# table aid
#
INSERT INTO aid  values ('0', 'Premiere activite ludique', '1', '1', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', 'n', 'n', 'n', 'n', 'n', 'n');


#
# table aid_appreciations
#


#
# table aid_config
#
INSERT INTO aid_config  values ('A.A.L', 'Aid d\'activite ludique', '20', 'b', '0', 'every', '1', '3', '', 'y', '1', 'y', 'y', 'n', 'n', 'n');


#
# table aid_familles
#
INSERT INTO aid_familles  values ('0', '10', 'Information, presse');
INSERT INTO aid_familles  values ('1', '11', 'Philosophie et psychologie, pensée');
INSERT INTO aid_familles  values ('2', '12', 'Religions');
INSERT INTO aid_familles  values ('3', '13', 'Sciences sociales, société, humanitaire');
INSERT INTO aid_familles  values ('4', '14', 'Langues, langage');
INSERT INTO aid_familles  values ('5', '15', 'Sciences (sciences dures)');
INSERT INTO aid_familles  values ('6', '16', 'Techniques, sciences appliquées, médecine, cuisine...');
INSERT INTO aid_familles  values ('7', '17', 'Arts, loisirs et sports');
INSERT INTO aid_familles  values ('8', '18', 'Littérature, théâtre, poésie');
INSERT INTO aid_familles  values ('9', '19', 'Géographie et Histoire, civilisations anciennes');
INSERT INTO aid_familles  values ('1', '11', 'Philosophie et psychologie, pensée');
INSERT INTO aid_familles  values ('3', '13', 'Sciences sociales, société, humanitaire');
INSERT INTO aid_familles  values ('6', '16', 'Techniques, sciences appliquées, médecine, cuisine...');
INSERT INTO aid_familles  values ('8', '18', 'Littérature, théâtre, poésie');
INSERT INTO aid_familles  values ('9', '19', 'Géographie et Histoire, civilisations anciennes');


#
# table aid_productions
#
INSERT INTO aid_productions  values ('1', 'Dossier papier');
INSERT INTO aid_productions  values ('2', 'Emission de radio');
INSERT INTO aid_productions  values ('3', 'Exposition');
INSERT INTO aid_productions  values ('4', 'Film');
INSERT INTO aid_productions  values ('5', 'Spectacle');
INSERT INTO aid_productions  values ('6', 'Réalisation plastique');
INSERT INTO aid_productions  values ('7', 'Réalisation technique ou scientifique');
INSERT INTO aid_productions  values ('8', 'Jeu vidéo');
INSERT INTO aid_productions  values ('9', 'Animation culturelle');
INSERT INTO aid_productions  values ('10', 'Maquette');
INSERT INTO aid_productions  values ('11', 'Site internet');
INSERT INTO aid_productions  values ('12', 'Diaporama');
INSERT INTO aid_productions  values ('13', 'Production musicale');
INSERT INTO aid_productions  values ('14', 'Production théâtrale');
INSERT INTO aid_productions  values ('15', 'Animation en milieu scolaire');
INSERT INTO aid_productions  values ('16', 'Programmation logicielle');
INSERT INTO aid_productions  values ('17', 'Journal');


#
# table aid_public
#
INSERT INTO aid_public  values ('3', '1', 'Lycéens');
INSERT INTO aid_public  values ('2', '2', 'Collègiens');
INSERT INTO aid_public  values ('1', '3', 'Ecoliers');
INSERT INTO aid_public  values ('6', '4', 'Grand public');
INSERT INTO aid_public  values ('5', '5', 'Experts (ou spécialistes)');
INSERT INTO aid_public  values ('4', '6', 'Etudiants');
INSERT INTO aid_public  values ('3', '1', 'Lycéens');
INSERT INTO aid_public  values ('2', '2', 'Collégiens');
INSERT INTO aid_public  values ('5', '5', 'Experts (ou spécialistes)');


#
# table archivage_aid_eleve
#


#
# table archivage_aids
#


#
# table archivage_appreciations_aid
#


#
# table archivage_disciplines
#


#
# table archivage_ects
#


#
# table archivage_eleves
#


#
# table archivage_eleves2
#


#
# table archivage_types_aid
#


#
# table ateliers_config
#


#
# table avis_conseil_classe
#


#
# table classes
#
INSERT INTO classes  values ('1', '5A', '5eme A', '','Chef etablissement Test',  'cni', 'n', 'n', 'n', 'n', 'n', 'n', '1', 'n', 'n', 'n', 'n', 'n', 'n', 'n', '3', '', '', '', '', '', '', '', 'n');
INSERT INTO classes  values ('9', '5B', '5eme B','', '', 'cni', 'n', 'n', 'n', 'n', 'n', 'n', '1', 'n', 'n', 'n', 'n', 'n', 'n', 'n', '3', '', '', '', '', '', '', '', 'n');
INSERT INTO classes  values ('115', '5E', 'cinquieme E', '','',  'cni', 'n', 'n', 'n', 'n', 'n', 'n', '1', 'n', 'n', 'n', 'n', 'n', 'n', 'n', '3', '', '', '', '', '', '', '', 'n');


#
# table cn_cahier_notes
#
INSERT INTO cn_cahier_notes  values ('1', '1', '1');


#
# table cn_conteneurs
#
INSERT INTO cn_conteneurs  values ('1', '1', 'Mathématiques', 'Mathématiques', '', '2', '1.0', 's1', '0.0', '0', '1', '0');


#
# table cn_devoirs
#
INSERT INTO cn_devoirs  values ('1', '1', '1', 'evaulation test', 'evaulation test', '', 'O', '2010-04-07 00:00:00', '1.0', '20', 'V', '1', '0', '2010-04-07 00:00:00');


#
# table cn_notes_conteneurs
#
INSERT INTO cn_notes_conteneurs  values ('testEleve1', '1', '14.0', 'y', '');
INSERT INTO cn_notes_conteneurs  values ('testEleve2', '1', '18.0', 'y', '');


#
# table cn_notes_devoirs
#
INSERT INTO cn_notes_devoirs  values ('testEleve1', '1', '14.0', '', '');
INSERT INTO cn_notes_devoirs  values ('testEleve2', '1', '18.0', '', '');


#
# table commentaires_types
#


#
# table commentaires_types_profs
#


#
# table communes
#


#
# table ct_devoirs_documents
#


#
# table ct_devoirs_entry
#


#
# table ct_documents
#


#
# table ct_entry
#
INSERT INTO ct_entry  values ('3', '00:00:00', '216', '1271109600', 'prof1', '0', '<p>	sdfhshdfh</p>', 'n', 'n');
INSERT INTO ct_entry  values ('2', '00:00:00', '1', '1271109600', 'prof1', '0', '<p>	deuxieme test</p>', 'n', 'n');
INSERT INTO ct_entry  values ('4', '00:00:00', '1', '1271368800', 'prof1', '0', '<p>	sdfhshdfh</p>', 'n', 'n');


#
# table ct_private_entry
#


#
# table ct_sequences
#


#
# table ct_types_documents
#
INSERT INTO ct_types_documents  values ('1', 'JPEG', 'jpg', 'oui');
INSERT INTO ct_types_documents  values ('2', 'PNG', 'png', 'oui');
INSERT INTO ct_types_documents  values ('3', 'GIF', 'gif', 'oui');
INSERT INTO ct_types_documents  values ('4', 'BMP', 'bmp', 'oui');
INSERT INTO ct_types_documents  values ('5', 'Photoshop', 'psd', 'oui');
INSERT INTO ct_types_documents  values ('6', 'TIFF', 'tif', 'oui');
INSERT INTO ct_types_documents  values ('7', 'AIFF', 'aiff', 'oui');
INSERT INTO ct_types_documents  values ('8', 'Windows Media', 'asf', 'oui');
INSERT INTO ct_types_documents  values ('9', 'Windows Media', 'avi', 'oui');
INSERT INTO ct_types_documents  values ('10', 'Midi', 'mid', 'oui');
INSERT INTO ct_types_documents  values ('12', 'QuickTime', 'mov', 'oui');
INSERT INTO ct_types_documents  values ('13', 'MP3', 'mp3', 'oui');
INSERT INTO ct_types_documents  values ('14', 'MPEG', 'mpg', 'oui');
INSERT INTO ct_types_documents  values ('15', 'Ogg', 'ogg', 'oui');
INSERT INTO ct_types_documents  values ('16', 'QuickTime', 'qt', 'oui');
INSERT INTO ct_types_documents  values ('17', 'RealAudio', 'ra', 'oui');
INSERT INTO ct_types_documents  values ('18', 'RealAudio', 'ram', 'oui');
INSERT INTO ct_types_documents  values ('19', 'RealAudio', 'rm', 'oui');
INSERT INTO ct_types_documents  values ('20', 'Flash', 'swf', 'oui');
INSERT INTO ct_types_documents  values ('21', 'WAV', 'wav', 'oui');
INSERT INTO ct_types_documents  values ('22', 'Windows Media', 'wmv', 'oui');
INSERT INTO ct_types_documents  values ('23', 'Adobe Illustrator', 'ai', 'oui');
INSERT INTO ct_types_documents  values ('24', 'BZip', 'bz2', 'oui');
INSERT INTO ct_types_documents  values ('25', 'C source', 'c', 'oui');
INSERT INTO ct_types_documents  values ('26', 'Debian', 'deb', 'oui');
INSERT INTO ct_types_documents  values ('27', 'Word', 'doc', 'oui');
INSERT INTO ct_types_documents  values ('29', 'LaTeX DVI', 'dvi', 'oui');
INSERT INTO ct_types_documents  values ('30', 'PostScript', 'eps', 'oui');
INSERT INTO ct_types_documents  values ('31', 'GZ', 'gz', 'oui');
INSERT INTO ct_types_documents  values ('32', 'C header', 'h', 'oui');
INSERT INTO ct_types_documents  values ('33', 'HTML', 'html', 'oui');
INSERT INTO ct_types_documents  values ('34', 'Pascal', 'pas', 'oui');
INSERT INTO ct_types_documents  values ('35', 'PDF', 'pdf', 'oui');
INSERT INTO ct_types_documents  values ('36', 'PowerPoint', 'ppt', 'oui');
INSERT INTO ct_types_documents  values ('37', 'PostScript', 'ps', 'oui');
INSERT INTO ct_types_documents  values ('38', 'gr', 'gr', 'oui');
INSERT INTO ct_types_documents  values ('39', 'RTF', 'rtf', 'oui');
INSERT INTO ct_types_documents  values ('40', 'StarOffice', 'sdd', 'oui');
INSERT INTO ct_types_documents  values ('41', 'StarOffice', 'sdw', 'oui');
INSERT INTO ct_types_documents  values ('42', 'Stuffit', 'sit', 'oui');
INSERT INTO ct_types_documents  values ('43', 'OpenOffice Calc', 'sxc', 'oui');
INSERT INTO ct_types_documents  values ('44', 'OpenOffice Impress', 'sxi', 'oui');
INSERT INTO ct_types_documents  values ('45', 'OpenOffice', 'sxw', 'oui');
INSERT INTO ct_types_documents  values ('46', 'LaTeX', 'tex', 'oui');
INSERT INTO ct_types_documents  values ('47', 'TGZ', 'tgz', 'oui');
INSERT INTO ct_types_documents  values ('48', 'texte', 'txt', 'oui');
INSERT INTO ct_types_documents  values ('49', 'GIMP multi-layer', 'xcf', 'oui');
INSERT INTO ct_types_documents  values ('50', 'Excel', 'xls', 'oui');
INSERT INTO ct_types_documents  values ('51', 'XML', 'xml', 'oui');
INSERT INTO ct_types_documents  values ('52', 'Zip', 'zip', 'oui');
INSERT INTO ct_types_documents  values ('53', 'Texte OpenDocument', 'odt', 'oui');
INSERT INTO ct_types_documents  values ('54', 'Classeur OpenDocument', 'ods', 'oui');
INSERT INTO ct_types_documents  values ('55', 'Présentation OpenDocument', 'odp', 'oui');
INSERT INTO ct_types_documents  values ('56', 'Dessin OpenDocument', 'odg', 'oui');
INSERT INTO ct_types_documents  values ('57', 'Base de données OpenDocument', 'odb', 'oui');


#
# table droits
#
INSERT INTO droits  values ('/mod_ooo/rapport_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : Rapport Incident', '');
INSERT INTO droits  values ('/mod_ooo/gerer_modeles_ooo.php', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'Modèle Ooo : Gérer et utiliser les modèles', '');
INSERT INTO droits  values ('/mod_ooo/ooo_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modèle Ooo : Admin', '');
INSERT INTO droits  values ('/mod_ooo/retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : Retenue', '');
INSERT INTO droits  values ('/mod_ooo/formulaire_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : formulaire retenue', '');
INSERT INTO droits  values ('/mod_ooo/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo: Index : Index', '');
INSERT INTO droits  values ('/mod_discipline/update_colonne_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Affichage d une imprimante pour le responsable d un incident', '');
INSERT INTO droits  values ('/absences/index.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', '');
INSERT INTO droits  values ('/absences/saisie_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', '');
INSERT INTO droits  values ('/accueil_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', ' ', '');
INSERT INTO droits  values ('/accueil_modules.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', '');
INSERT INTO droits  values ('/accueil.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', ' ', '');
INSERT INTO droits  values ('/accueil_professeur.php', 'V', 'V', 'F', 'F', 'F', 'F', 'V', 'F', ' ', '');
INSERT INTO droits  values ('/aid/add_aid.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Configuration des AID', '');
INSERT INTO droits  values ('/aid/config_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');
INSERT INTO droits  values ('/aid/export_csv_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');
INSERT INTO droits  values ('/aid/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');
INSERT INTO droits  values ('/aid/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');
INSERT INTO droits  values ('/aid/index2.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des AID (profs, élèves)', '');
INSERT INTO droits  values ('/aid/modify_aid.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des AID (profs, élèves)', '');
INSERT INTO droits  values ('/aid/modify_aid_new.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des AID (profs, élèves)', '');
INSERT INTO droits  values ('/lib/confirm_query.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', '');
INSERT INTO droits  values ('/bulletin/edit.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1');
INSERT INTO droits  values ('/bulletin/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1');
INSERT INTO droits  values ('/bulletin/param_bull.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1');
INSERT INTO droits  values ('/bulletin/verif_bulletins.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Vérification du remplissage des bulletins', '');
INSERT INTO droits  values ('/bulletin/verrouillage.php', 'F', 'F', 'F', 'V', 'F', 'F', 'F', 'F', '(de)Verrouillage des périodes', '');
INSERT INTO droits  values ('/cahier_notes_admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des carnets de notes', '');
INSERT INTO droits  values ('/cahier_notes/add_modif_conteneur.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');
INSERT INTO droits  values ('/cahier_notes/add_modif_dev.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');
INSERT INTO droits  values ('/cahier_notes/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');
INSERT INTO droits  values ('/cahier_notes/saisie_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');
INSERT INTO droits  values ('/cahier_notes/toutes_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');
INSERT INTO droits  values ('/cahier_notes/visu_releve_notes.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation et impression des relevés de notes', '');
INSERT INTO droits  values ('/cahier_texte_admin/admin_ct.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de textes', '');
INSERT INTO droits  values ('/cahier_texte_admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de textes', '');
INSERT INTO droits  values ('/cahier_texte_admin/modify_limites.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de textes', '');
INSERT INTO droits  values ('/cahier_texte_admin/modify_type_doc.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de textes', '');
INSERT INTO droits  values ('/cahier_texte/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte/traite_doc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/ajax_edition_compte_rendu.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/ajax_edition_notice_privee.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/ajax_duplication_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/ajax_affichage_duplication_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/ajax_deplacement_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/ajax_affichage_deplacement_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/ajax_suppression_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/ajax_enregistrement_compte_rendu.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/ajax_enregistrement_notice_privee.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/ajax_edition_devoir.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/ajax_enregistrement_devoir.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/ajax_affichages_liste_notices.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/ajax_affichage_dernieres_notices.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/traite_doc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/exportcsv.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');
INSERT INTO droits  values ('/cahier_texte_2/consultation.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation des cahiers de textes', '');
INSERT INTO droits  values ('/cahier_texte_2/see_all.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des cahiers de texte', '');
INSERT INTO droits  values ('/cahier_texte_2/creer_sequence.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de texte - s&eacute;quences', '1');
INSERT INTO droits  values ('/cahier_texte_2/creer_seq_ajax_step1.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de texte - s&eacute;quences', '1');
INSERT INTO droits  values ('/classes/classes_ajout.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');
INSERT INTO droits  values ('/classes/classes_const.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');
INSERT INTO droits  values ('/classes/cpe_resp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des CPE aux classes', '');
INSERT INTO droits  values ('/classes/duplicate_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');
INSERT INTO droits  values ('/classes/eleve_options.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');
INSERT INTO droits  values ('/classes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');
INSERT INTO droits  values ('/classes/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');
INSERT INTO droits  values ('/classes/modify_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');
INSERT INTO droits  values ('/classes/modify_nom_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');
INSERT INTO droits  values ('/classes/modify_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');
INSERT INTO droits  values ('/classes/periodes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');
INSERT INTO droits  values ('/classes/prof_suivi.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');
INSERT INTO droits  values ('/eleves/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des élèves', '');
INSERT INTO droits  values ('/eleves/import_eleves_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des élèves', '');
INSERT INTO droits  values ('/eleves/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Gestion des élèves', '');
INSERT INTO droits  values ('/eleves/modify_eleve.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Gestion des élèves', '');
INSERT INTO droits  values ('/etablissements/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', '');
INSERT INTO droits  values ('/etablissements/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', '');
INSERT INTO droits  values ('/etablissements/modify_etab.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', '');
INSERT INTO droits  values ('/gestion/accueil_sauve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Restauration, suppression et sauvegarde de la base', '');
INSERT INTO droits  values ('/gestion/gestion_base_test.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'gestion données de test', '');
INSERT INTO droits  values ('/gestion/savebackup.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Téléchargement de sauvegardes la base', '');
INSERT INTO droits  values ('/gestion/efface_base.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Restauration, suppression et sauvegarde de la base', '');
INSERT INTO droits  values ('/gestion/gestion_connect.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des connexions', '');
INSERT INTO droits  values ('/gestion/help_import.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/gestion/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', '');
INSERT INTO droits  values ('/gestion/import_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/gestion/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', '');
INSERT INTO droits  values ('/gestion/modify_impression.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des paramètres de la feuille de bienvenue', '');
INSERT INTO droits  values ('/gestion/param_gen.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration générale', '');
INSERT INTO droits  values ('/gestion/traitement_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/groupes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des groupes', '');
INSERT INTO droits  values ('/groupes/add_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajout de groupes', '');
INSERT INTO droits  values ('/groupes/edit_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition de groupes', '');
INSERT INTO droits  values ('/groupes/edit_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des élèves des groupes', '');
INSERT INTO droits  values ('/groupes/edit_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des groupes de la classe', '');
INSERT INTO droits  values ('/groupes/edit_class_grp_lot.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des matières aux professeurs', '');
INSERT INTO droits  values ('/init_csv/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');
INSERT INTO droits  values ('/init_csv/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');
INSERT INTO droits  values ('/init_csv/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');
INSERT INTO droits  values ('/init_csv/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');
INSERT INTO droits  values ('/init_csv/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');
INSERT INTO droits  values ('/init_csv/eleves_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');
INSERT INTO droits  values ('/init_csv/prof_disc_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');
INSERT INTO droits  values ('/init_csv/eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');
INSERT INTO droits  values ('/init_dbf_sts/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/init_dbf_sts/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/init_dbf_sts/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/init_dbf_sts/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/init_dbf_sts/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/init_dbf_sts/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/init_dbf_sts/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/init_dbf_sts/disciplines_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/init_dbf_sts/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/init_dbf_sts/prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/init_dbf_sts/lecture_xml_sts_emp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/init_dbf_sts/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/init_dbf_sts/save_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');
INSERT INTO droits  values ('/init_scribe/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/init_scribe/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/init_scribe/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/init_scribe/eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/init_scribe/prof_disc_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/init_scribe/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/init_lcs/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/init_lcs/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/init_lcs/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/init_lcs/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/init_lcs/affectations.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/initialisation/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/initialisation/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/initialisation/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/initialisation/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/initialisation/prof_disc_classe.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/initialisation/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/initialisation/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/initialisation/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/initialisation/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/initialisation/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');
INSERT INTO droits  values ('/matieres/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', '');
INSERT INTO droits  values ('/matieres/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', '');
INSERT INTO droits  values ('/matieres/matieres_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation des matières en CSV', '');
INSERT INTO droits  values ('/matieres/matieres_categories.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des catégories de matière', '');
INSERT INTO droits  values ('/matieres/modify_matiere.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', '');
INSERT INTO droits  values ('/matieres/matieres_param.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');
INSERT INTO droits  values ('/prepa_conseil/edit_limite.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', '');
INSERT INTO droits  values ('/prepa_conseil/help.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', '');
INSERT INTO droits  values ('/prepa_conseil/index1.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Visualisation des notes et appréciations', '1');
INSERT INTO droits  values ('/prepa_conseil/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', '');
INSERT INTO droits  values ('/prepa_conseil/index3.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', '');
INSERT INTO droits  values ('/prepa_conseil/visu_aid.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Visualisation des notes et appréciations AID', '');
INSERT INTO droits  values ('/prepa_conseil/visu_toutes_notes.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', '');
INSERT INTO droits  values ('/responsables/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration et gestion des responsables élèves', '');
INSERT INTO droits  values ('/responsables/modify_resp.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration et gestion des responsables élèves', '');
INSERT INTO droits  values ('/saisie/help.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', '');
INSERT INTO droits  values ('/saisie/import_class_csv.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', '', '');
INSERT INTO droits  values ('/saisie/import_note_app.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', '');
INSERT INTO droits  values ('/saisie/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', '');
INSERT INTO droits  values ('/saisie/saisie_aid.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des notes et appréciations AID', '');
INSERT INTO droits  values ('/saisie/saisie_appreciations.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des appréciations du bulletins', '');
INSERT INTO droits  values ('/saisie/saisie_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Saisie des avis du conseil de classe', '');
INSERT INTO droits  values ('/saisie/saisie_avis1.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Saisie des avis du conseil de classe', '');
INSERT INTO droits  values ('/saisie/saisie_avis2.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Saisie des avis du conseil de classe', '');
INSERT INTO droits  values ('/saisie/saisie_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des notes du bulletins', '');
INSERT INTO droits  values ('/saisie/traitement_csv.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des notes du bulletins', '');
INSERT INTO droits  values ('/utilisateurs/change_pwd.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');
INSERT INTO droits  values ('/utilisateurs/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');
INSERT INTO droits  values ('/utilisateurs/import_prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');
INSERT INTO droits  values ('/utilisateurs/impression_bienvenue.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');
INSERT INTO droits  values ('/utilisateurs/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');
INSERT INTO droits  values ('/utilisateurs/reset_passwords.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Réinitialisation des mots de passe', '');
INSERT INTO droits  values ('/utilisateurs/modify_user.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');
INSERT INTO droits  values ('/utilisateurs/mon_compte.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Gestion du compte (informations personnelles, mot de passe, ...)', '');
INSERT INTO droits  values ('/utilisateurs/tab_profs_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des matieres aux professeurs', '');
INSERT INTO droits  values ('/visualisation/classe_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');
INSERT INTO droits  values ('/visualisation/eleve_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');
INSERT INTO droits  values ('/visualisation/eleve_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');
INSERT INTO droits  values ('/visualisation/evol_eleve_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');
INSERT INTO droits  values ('/visualisation/evol_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');
INSERT INTO droits  values ('/visualisation/index.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');
INSERT INTO droits  values ('/visualisation/stats_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');
INSERT INTO droits  values ('/classes/classes_param.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');
INSERT INTO droits  values ('/fpdf/imprime_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', '', '');
INSERT INTO droits  values ('/etablissements/import_etab_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', '');
INSERT INTO droits  values ('/saisie/import_app_cons.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Importation csv des avis du conseil de classe', '');
INSERT INTO droits  values ('/messagerie/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Gestion de la messagerie', '');
INSERT INTO droits  values ('/absences/import_absences_gep.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', '');
INSERT INTO droits  values ('/absences/seq_gep_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', '');
INSERT INTO droits  values ('/utilitaires/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Maintenance', '');
INSERT INTO droits  values ('/gestion/contacter_admin.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', '', '');
INSERT INTO droits  values ('/mod_absences/gestion/gestion_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');
INSERT INTO droits  values ('/mod_absences/gestion/gestion_absences_liste.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');
INSERT INTO droits  values ('/mod_absences/gestion/impression_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');
INSERT INTO droits  values ('/mod_absences/gestion/select.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');
INSERT INTO droits  values ('/mod_absences/gestion/ajout_ret.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');
INSERT INTO droits  values ('/mod_absences/gestion/ajout_dip.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');
INSERT INTO droits  values ('/mod_absences/gestion/ajout_inf.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');
INSERT INTO droits  values ('/mod_absences/gestion/ajout_abs.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');
INSERT INTO droits  values ('/mod_absences/gestion/bilan_absence.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');
INSERT INTO droits  values ('/mod_absences/gestion/bilan.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');
INSERT INTO droits  values ('/mod_absences/gestion/lettre_aux_parents.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');
INSERT INTO droits  values ('/mod_absences/lib/tableau.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', '');
INSERT INTO droits  values ('/mod_absences/lib/tableau_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', '');
INSERT INTO droits  values ('/mod_absences/admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');
INSERT INTO droits  values ('/mod_absences/admin/admin_motifs_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');
INSERT INTO droits  values ('/edt_organisation/admin_periodes_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');
INSERT INTO droits  values ('/mod_absences/lib/liste_absences.php', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'F', '', '');
INSERT INTO droits  values ('/mod_absences/lib/graphiques.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', '', '');
INSERT INTO droits  values ('/mod_absences/professeurs/prof_ajout_abs.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajout des absences en classe', '');
INSERT INTO droits  values ('/mod_trombinoscopes/trombinoscopes.php', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'Visualiser le trombinoscope', '');
INSERT INTO droits  values ('/mod_trombinoscopes/trombi_impr.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualiser le trombinoscope', '');
INSERT INTO droits  values ('/mod_trombinoscopes/trombinoscopes_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '(des)activation du module trombinoscope', '');
INSERT INTO droits  values ('/groupes/visu_profs_class.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des équipes pédagogiques', '');
INSERT INTO droits  values ('/groupes/popup.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des équipes pédagogiques', '');
INSERT INTO droits  values ('/cahier_notes/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des moyennes des carnets de notes', '');
INSERT INTO droits  values ('/cahier_notes/visu_toutes_notes2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des moyennes des carnets de notes', '');
INSERT INTO droits  values ('/utilitaires/verif_groupes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Vérification des incohérences d appartenances à des groupes', '');
INSERT INTO droits  values ('/visualisation/affiche_eleve.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');
INSERT INTO droits  values ('/visualisation/draw_graphe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');
INSERT INTO droits  values ('/groupes/mes_listes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Accès aux CSV des listes d élèves', '');
INSERT INTO droits  values ('/groupes/get_csv.php', 'F', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Génération de CSV élèves', '');
INSERT INTO droits  values ('/visualisation/choix_couleurs.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Choix des couleurs des graphiques des résultats scolaires', '');
INSERT INTO droits  values ('/visualisation/couleur.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Choix d une couleur pour le graphique des résultats scolaires', '');
INSERT INTO droits  values ('/gestion/config_prefs.php', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des préférences d utilisateurs', '');
INSERT INTO droits  values ('/utilitaires/recalcul_moy_conteneurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Recalcul des moyennes des conteneurs', '');
INSERT INTO droits  values ('/classes/scol_resp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des comptes scolarité aux classes', '');
INSERT INTO droits  values ('/mod_absences/lib/fiche_eleve.php', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'Fiche du suivie de l\'élève', '');
INSERT INTO droits  values ('/mod_miseajour/utilisateur/fenetre.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des mises à jour', '');
INSERT INTO droits  values ('/mod_miseajour/admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module de mise à jour', '');
INSERT INTO droits  values ('/referencement.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Référencement de Gepi sur la base centralisée des utilisateurs de Gepi', '');
INSERT INTO droits  values ('/mod_absences/admin/admin_actions_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des actions absences', '');
INSERT INTO droits  values ('/saisie/commentaires_types.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Saisie de commentaires-types', '');
INSERT INTO droits  values ('/cahier_notes/releve_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Relevé de note au format PDF', '');
INSERT INTO droits  values ('/impression/parametres_impression_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des listes PDF; réglage des paramètres', '');
INSERT INTO droits  values ('/impression/impression_serie.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des listes (PDF) en série', '');
INSERT INTO droits  values ('/impression/impression.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression rapide d une listes (PDF) ', '');
INSERT INTO droits  values ('/impression/liste_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des listes (PDF)', '');
INSERT INTO droits  values ('/init_xml/lecture_xml_sconet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml/disciplines_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml/lecture_xml_sts_emp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml/prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml/save_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/responsables/maj_import.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour depuis Sconet', '');
INSERT INTO droits  values ('/responsables/conversion.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Conversion des données responsables', '');
INSERT INTO droits  values ('/utilisateurs/create_responsable.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Création des utilisateurs au statut responsable', '');
INSERT INTO droits  values ('/utilisateurs/create_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Création des utilisateurs au statut élève', '');
INSERT INTO droits  values ('/utilisateurs/edit_responsable.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des utilisateurs au statut responsable', '');
INSERT INTO droits  values ('/utilisateurs/edit_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des utilisateurs au statut élève', '');
INSERT INTO droits  values ('/cahier_texte/consultation.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation des cahiers de texte', '');
INSERT INTO droits  values ('/cahier_texte/see_all.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des cahiers de texte', '');
INSERT INTO droits  values ('/cahier_texte/visu_prof_jour.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Acces_a_son_cahier_de_textes_personnel', '');
INSERT INTO droits  values ('/gestion/droits_acces.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Paramétrage des droits d accès', '');
INSERT INTO droits  values ('/groupes/visu_profs_eleve.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation équipe pédagogique', '');
INSERT INTO droits  values ('/saisie/impression_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Impression des avis trimestrielles des conseils de classe.', '');
INSERT INTO droits  values ('/impression/avis_pdf.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Impression des avis trimestrielles des conseils de classe. Module PDF', '');
INSERT INTO droits  values ('/impression/parametres_impression_pdf_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Impression des avis conseil classe PDF; reglage des parametres', '');
INSERT INTO droits  values ('/utilisateurs/password_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Export des identifiants et mots de passe en csv', '');
INSERT INTO droits  values ('/impression/password_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Impression des identifiants et des mots de passe en PDF', '');
INSERT INTO droits  values ('/bulletin/buletin_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Bulletin scolaire au format PDF', '');
INSERT INTO droits  values ('/mod_absences/gestion/etiquette_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Etiquette au format PDF', '');
INSERT INTO droits  values ('/mod_absences/lib/export_csv.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Fichier d\'exportation en csv des absences', '');
INSERT INTO droits  values ('/mod_absences/gestion/statistiques.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Statistique du module vie scolaire', '1');
INSERT INTO droits  values ('/mod_absences/lib/graph_camembert.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'graphique camembert', '');
INSERT INTO droits  values ('/mod_absences/lib/graph_ligne.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'graphique camembert', '');
INSERT INTO droits  values ('/edt_organisation/admin_horaire_ouverture.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des horaires d\'ouverture de l\'établissement', '');
INSERT INTO droits  values ('/edt_organisation/admin_config_semaines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des types de semaines', '');
INSERT INTO droits  values ('/mod_absences/gestion/fiche_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Fiche récapitulatif des absences', '');
INSERT INTO droits  values ('/mod_absences/lib/graph_double_ligne.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'graphique absence et retard sur le même graphique', '');
INSERT INTO droits  values ('/bulletin/param_bull_pdf.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'page de gestion des parametres du bulletin pdf', '');
INSERT INTO droits  values ('/bulletin/bulletin_pdf_avec_modele_classe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'page generant le bulletin pdf en fonction du modele affecte a la classe ', '');
INSERT INTO droits  values ('/gestion/security_panel.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'panneau de controle des atteintes a la securite', '');
INSERT INTO droits  values ('/gestion/security_policy.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'definition des politiques de securite', '');
INSERT INTO droits  values ('/gestion/options_connect.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Options de connexions', '');
INSERT INTO droits  values ('/mod_absences/gestion/alert_suivi.php', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'système d\'alerte de suivi d\'élève', '');
INSERT INTO droits  values ('/gestion/efface_photos.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression des photos non associées à des élèves', '');
INSERT INTO droits  values ('/responsables/gerer_adr.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des adresses de responsables', '');
INSERT INTO droits  values ('/responsables/choix_adr_existante.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Choix adresse de responsable existante', '');
INSERT INTO droits  values ('/cahier_notes/export_cahier_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Export CSV/ODS du cahier de notes', '');
INSERT INTO droits  values ('/cahier_notes/import_cahier_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Import CSV du cahier de notes', '');
INSERT INTO droits  values ('/eleves/add_eleve.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Gestion des élèves', '');
INSERT INTO droits  values ('/saisie/export_class_ods.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Export ODS des notes/appréciations', '');
INSERT INTO droits  values ('/gestion/gestion_temp_dir.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des dossiers temporaires d utilisateurs', '');
INSERT INTO droits  values ('/gestion/param_couleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des couleurs pour Gepi', '');
INSERT INTO droits  values ('/utilisateurs/creer_remplacant.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'script de création d un remplaçant', '');
INSERT INTO droits  values ('/mod_absences/gestion/lettre_pdf.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Publipostage des lettres d absences PDF', '1');
INSERT INTO droits  values ('/accueil_simpl_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Page d accueil simplifiée pour les profs', '');
INSERT INTO droits  values ('/init_xml2/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml2/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml2/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml2/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml2/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml2/matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml2/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml2/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml2/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml2/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml2/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/init_xml2/clean_temp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');
INSERT INTO droits  values ('/mod_annees_anterieures/conservation_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Conservation des données antérieures', '');
INSERT INTO droits  values ('/mod_annees_anterieures/consultation_annee_anterieure.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des données d années antérieures', '');
INSERT INTO droits  values ('/mod_annees_anterieures/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Index données antérieures', '');
INSERT INTO droits  values ('/mod_annees_anterieures/popup_annee_anterieure.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des données antérieures', '');
INSERT INTO droits  values ('/mod_annees_anterieures/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Activation/désactivation du module données antérieures', '');
INSERT INTO droits  values ('/mod_annees_anterieures/nettoyer_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression de données antérieures', '');
INSERT INTO droits  values ('/mod_annees_anterieures/archivage_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches projets', '1');
INSERT INTO droits  values ('/responsables/maj_import1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour depuis Sconet', '');
INSERT INTO droits  values ('/responsables/maj_import2.php', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour depuis Sconet', '');
INSERT INTO droits  values ('/mod_annees_anterieures/corriger_ine.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction d INE dans la table annees_anterieures', '');
INSERT INTO droits  values ('/mod_annees_anterieures/liste_eleves_ajax.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Recherche d élèves', '');
INSERT INTO droits  values ('/mod_absences/lib/graph_double_ligne_fiche.php', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'F', 'Graphique de la fiche élève', '1');
INSERT INTO droits  values ('/mod_absences/admin/admin_config_calendrier.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définir les différentes périodes', '');
INSERT INTO droits  values ('/edt_organisation/index_edt.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des emplois du temps', '');
INSERT INTO droits  values ('/edt_organisation/edt_initialiser.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation des emplois du temps', '');
INSERT INTO droits  values ('/edt_organisation/effacer_cours.php', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Effacer un cours des emplois du temps', '');
INSERT INTO droits  values ('/edt_organisation/edt_calendrier.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation du calendrier', '');
INSERT INTO droits  values ('/edt_organisation/ajouter_salle.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des salles', '');
INSERT INTO droits  values ('/edt_organisation/edt_parametrer.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les paramètres EdT', '');
INSERT INTO droits  values ('/edt_organisation/voir_groupe.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Voir les groupes de Gepi', '');
INSERT INTO droits  values ('/edt_organisation/modif_edt_tempo.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modification temporaire des EdT', '');
INSERT INTO droits  values ('/edt_organisation/edt_init_xml.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation EdT par xml', '');
INSERT INTO droits  values ('/edt_organisation/edt_init_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par csv', '');
INSERT INTO droits  values ('/edt_organisation/edt_init_csv2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un autre csv', '');
INSERT INTO droits  values ('/edt_organisation/edt_init_texte.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un fichier texte', '');
INSERT INTO droits  values ('/edt_organisation/edt_init_concordance.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un fichier texte', '');
INSERT INTO droits  values ('/edt_organisation/edt_init_concordance2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un autre fichier csv', '');
INSERT INTO droits  values ('/edt_organisation/modifier_cours.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Modifier un cours', '');
INSERT INTO droits  values ('/edt_organisation/modifier_cours_popup.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Modifier un cours', '');
INSERT INTO droits  values ('/edt_organisation/edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Régler le module emploi du temps', '');
INSERT INTO droits  values ('/edt_organisation/edt_eleve.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Régler le module emploi du temps', '');
INSERT INTO droits  values ('/edt_organisation/edt_param_couleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Régler les couleurs des matières (EdT)', '');
INSERT INTO droits  values ('/edt_organisation/ajax_edtcouleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modifier les couleurs des affichages des emplois du temps.', '');
INSERT INTO droits  values ('/utilisateurs/creer_statut.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajouter et gérer des statuts personnalisés', '');
INSERT INTO droits  values ('/utilisateurs/creer_statut_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Autoriser la création des statuts personnalisés', '');
INSERT INTO droits  values ('/edt_gestion_gr/edt_aff_gr.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', '');
INSERT INTO droits  values ('/edt_gestion_gr/edt_ajax_win.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', '');
INSERT INTO droits  values ('/edt_gestion_gr/edt_liste_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', '');
INSERT INTO droits  values ('/edt_gestion_gr/edt_liste_profs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', '');
INSERT INTO droits  values ('/edt_gestion_gr/edt_win.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les groupes du module EdT', '');
INSERT INTO droits  values ('/absences/import_absences_sconet.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', '');
INSERT INTO droits  values ('/bulletin/export_modele_pdf.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'exportation en csv des modeles de bulletin pdf', '');
INSERT INTO droits  values ('/absences/consulter_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Consulter les absences', '');
INSERT INTO droits  values ('/mod_absences/professeurs/bilan_absences_professeur.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Bilan des absences pour chaque professeur', '');
INSERT INTO droits  values ('/mod_absences/professeurs/bilan_absences_classe.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Bilan des absences pour chaque professeur', '');
INSERT INTO droits  values ('/mod_absences/gestion/voir_absences_viescolaire.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences du jour', '');
INSERT INTO droits  values ('/mod_absences/gestion/bilan_absences_quotidien.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences par créneau', '');
INSERT INTO droits  values ('/mod_absences/gestion/bilan_absences_quotidien_pdf.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences par créneau en pdf', '');
INSERT INTO droits  values ('/mod_absences/gestion/bilan_absences_classe.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences par classe', '');
INSERT INTO droits  values ('/mod_absences/gestion/bilan_repas_quotidien.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter l inscription aux repas', '');
INSERT INTO droits  values ('/mod_absences/absences.php', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'F', 'Consulter les absences de son enfant', '');
INSERT INTO droits  values ('/mod_absences/admin/interface_abs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Paramétrer les interfaces des professeurs', '');
INSERT INTO droits  values ('/absences/import_absences_gepi.php', 'F', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Page d\'importation des absences de gepi mod_absences', '1');
INSERT INTO droits  values ('/saisie/ajax_appreciations.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Sauvegarde des appréciations du bulletins', '');
INSERT INTO droits  values ('/lib/change_mode_header.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Page AJAX pour changer la variable cacher_header', '1');
INSERT INTO droits  values ('/saisie/recopie_moyennes.php', 'F', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'Recopie des moyennes', '');
INSERT INTO droits  values ('/groupes/fusion_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fusionner des groupes', '');
INSERT INTO droits  values ('/gestion/security_panel_archives.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'page archive du panneau de sécurité', '');
INSERT INTO droits  values ('/responsables/corrige_ele_id.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction des ELE_ID d apres Sconet', '');
INSERT INTO droits  values ('/mod_inscription/inscription_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '(De)activation du module inscription', '');
INSERT INTO droits  values ('/mod_inscription/inscription_index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'accès au module configuration', '');
INSERT INTO droits  values ('/mod_inscription/inscription_config.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration du module inscription', '');
INSERT INTO droits  values ('/mod_inscription/help.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration du module inscription', '');
INSERT INTO droits  values ('/aid/index_fiches.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Outils complémentaires de gestion des AIDs', '');
INSERT INTO droits  values ('/aid/visu_fiches.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Outils complémentaires de gestion des AIDs', '');
INSERT INTO droits  values ('/aid/modif_fiches.php', 'V', 'V', 'V', 'F', 'V', 'V', 'F', 'F', 'Outils complémentaires de gestion des AIDs', '');
INSERT INTO droits  values ('/aid/config_aid_fiches_projet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des outils complémentaires de gestion des AIDs', '');
INSERT INTO droits  values ('/aid/config_aid_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des outils complémentaires de gestion des AIDs', '');
INSERT INTO droits  values ('/aid/config_aid_productions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des outils complémentaires de gestion des AIDs', '');
INSERT INTO droits  values ('/classes/acces_appreciations.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration de la restriction d accès aux appréciations pour les élèves et responsables', '');
INSERT INTO droits  values ('/mod_notanet/rouen/fiches_brevet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Accès aux fiches brevet', '');
INSERT INTO droits  values ('/mod_notanet/poitiers/fiches_brevet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Accès aux fiches brevet', '');
INSERT INTO droits  values ('/mod_notanet/notanet_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion du module NOTANET', '');
INSERT INTO droits  values ('/mod_notanet/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Notanet: Accueil', '');
INSERT INTO droits  values ('/mod_notanet/extract_moy.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Extraction des moyennes', '');
INSERT INTO droits  values ('/mod_notanet/corrige_extract_moy.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Extraction des moyennes', '');
INSERT INTO droits  values ('/mod_notanet/select_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Associations élèves/type de brevet', '');
INSERT INTO droits  values ('/mod_notanet/select_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Associations matières/type de brevet', '');
INSERT INTO droits  values ('/mod_notanet/saisie_app.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Saisie des appréciations', '');
INSERT INTO droits  values ('/mod_notanet/generer_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Génération de CSV', '');
INSERT INTO droits  values ('/mod_notanet/choix_generation_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Génération de CSV', '');
INSERT INTO droits  values ('/mod_notanet/verrouillage_saisie_app.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: (Dé)Verrouillage des saisies', '');
INSERT INTO droits  values ('/bulletin/bull_index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Edition des bulletins', '1');
INSERT INTO droits  values ('/cahier_notes/visu_releve_notes_bis.php', 'F', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Relevé de notes', '1');
INSERT INTO droits  values ('/cahier_notes/param_releve_html.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Paramètres du relevé de notes', '1');
INSERT INTO droits  values ('/classes/changement_eleve_classe.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Changement de classe pour un élève', '1');
INSERT INTO droits  values ('/mod_notanet/saisie_avis.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Notanet: Saisie avis chef etablissement', '');
INSERT INTO droits  values ('/mod_notanet/poitiers/param_fiche_brevet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Notanet: Paramètres d impression', '');
INSERT INTO droits  values ('/mod_notanet/saisie_b2i_a2.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Notanet: Saisie socles B2i et A2', '');
INSERT INTO droits  values ('/eleves/liste_eleves.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Lister des élèves', '');
INSERT INTO droits  values ('/eleves/visu_eleve.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Consultation_d_un_eleve', '');
INSERT INTO droits  values ('/cahier_texte_admin/rss_cdt_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les flux rss du cdt', '');
INSERT INTO droits  values ('/matieres/suppr_matiere.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression d une matiere', '');
INSERT INTO droits  values ('/eleves/import_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation bulletin élève', '');
INSERT INTO droits  values ('/eleves/export_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Exportation bulletin élève', '');
INSERT INTO droits  values ('/cahier_texte_admin/visa_ct.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Page de signature des cahiers de texte', '');
INSERT INTO droits  values ('/saisie/saisie_cmnt_type_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Saisie appréciations-types pour les profs', '');
INSERT INTO droits  values ('/saisie/export_cmnt_type_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Export des appréciations-types pour les profs', '');
INSERT INTO droits  values ('/mod_ent/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de l intégration de GEPI dans un ENT', '');
INSERT INTO droits  values ('/mod_ent/gestion_ent_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de l intégration de GEPI dans un ENT', '');
INSERT INTO droits  values ('/mod_ent/gestion_ent_profs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de l intégration de GEPI dans un ENT', '');
INSERT INTO droits  values ('/mod_ent/miseajour_ent_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de l intégration de GEPI dans un ENT', '');
INSERT INTO droits  values ('/mod_discipline/traiter_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Traitement', '');
INSERT INTO droits  values ('/mod_discipline/saisie_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie incident', '');
INSERT INTO droits  values ('/mod_discipline/occupation_lieu_heure.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Occupation lieu', '');
INSERT INTO droits  values ('/mod_discipline/liste_sanctions_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Liste', '');
INSERT INTO droits  values ('/mod_discipline/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Index', '');
INSERT INTO droits  values ('/mod_discipline/incidents_sans_protagonistes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Incidents sans protagonistes', '');
INSERT INTO droits  values ('/mod_discipline/edt_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: EDT élève', '');
INSERT INTO droits  values ('/mod_discipline/ajout_sanction.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Ajout sanction', '');
INSERT INTO droits  values ('/mod_discipline/saisie_sanction.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie sanction', '');
INSERT INTO droits  values ('/mod_discipline/definir_roles.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définition des rôles', '');
INSERT INTO droits  values ('/mod_discipline/definir_lieux.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définition des lieux', '');
INSERT INTO droits  values ('/mod_discipline/definir_mesures.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définition des mesures', '');
INSERT INTO droits  values ('/mod_discipline/sauve_role.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg rôle incident', '');
INSERT INTO droits  values ('/mod_discipline/definir_autres_sanctions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir types sanctions', '');
INSERT INTO droits  values ('/mod_discipline/liste_retenues_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Liste des retenues du jour', '');
INSERT INTO droits  values ('/mod_discipline/avertir_famille.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Avertir famille incident', '');
INSERT INTO droits  values ('/mod_discipline/avertir_famille_html.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Avertir famille incident', '');
INSERT INTO droits  values ('/mod_discipline/sauve_famille_avertie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg famille avertie', '');
INSERT INTO droits  values ('/mod_discipline/discipline_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Activation/desactivation du module', '');
INSERT INTO droits  values ('/aid/annees_anterieures_accueil.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Configuration des AID', '');
INSERT INTO droits  values ('/saisie/saisie_secours_eleve.php', 'F', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie notes/appréciations pour un élève en compte secours', '');
INSERT INTO droits  values ('/classes/classes_ajax_lib.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Page appelée via ajax.', '');
INSERT INTO droits  values ('/responsables/dedoublonnage_adresses.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Dédoublonnage des adresses responsables', '');
INSERT INTO droits  values ('/mod_ects/ects_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Module ECTS : Admin', '');
INSERT INTO droits  values ('/mod_ects/index_saisie.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Accueil saisie', '');
INSERT INTO droits  values ('/mod_ects/saisie_ects.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Saisie', '');
INSERT INTO droits  values ('/mod_ects/edition.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Edition des documents', '');
INSERT INTO droits  values ('/mod_ooo/documents_ects.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Génération des documents', '');
INSERT INTO droits  values ('/mod_ects/recapitulatif.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Recapitulatif globaux', '');
INSERT INTO droits  values ('/mod_notanet/fb_rouen_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Rouen', '');
INSERT INTO droits  values ('/mod_notanet/fb_montpellier_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Montpellier', '');
INSERT INTO droits  values ('/mod_plugins/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajouter/enlever des plugins', '');
INSERT INTO droits  values ('/mod_genese_classes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Accueil', '');
INSERT INTO droits  values ('/mod_genese_classes/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Activation/désactivation', '');
INSERT INTO droits  values ('/mod_genese_classes/select_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Choix des options', '');
INSERT INTO droits  values ('/mod_genese_classes/select_eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Choix des options des élèves', '');
INSERT INTO droits  values ('/mod_genese_classes/select_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Choix des classes', '');
INSERT INTO droits  values ('/mod_genese_classes/saisie_contraintes_opt_classe.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Saisie des contraintes options/classes', '');
INSERT INTO droits  values ('/mod_genese_classes/liste_classe_fut.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Liste des classes futures (appel ajax)', '');
INSERT INTO droits  values ('/mod_genese_classes/affiche_listes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Affichage de listes', '');
INSERT INTO droits  values ('/mod_genese_classes/genere_ods.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Génération d un fichier ODS de listes', '');
INSERT INTO droits  values ('/mod_genese_classes/affect_eleves_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Affectation des élèves', '');
INSERT INTO droits  values ('/mod_genese_classes/select_arriv_red.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Sélection des arrivants/redoublants', '');
INSERT INTO droits  values ('/mod_genese_classes/liste_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Liste des options de classes existantes', '');
INSERT INTO droits  values ('/mod_genese_classes/import_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génèse des classes: Import options depuis CSV', '');
INSERT INTO droits  values ('/eleves/import_communes.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Import des communes de naissance', '');
INSERT INTO droits  values ('/mod_notanet/fb_lille_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Lille', '');
INSERT INTO droits  values ('/mod_notanet/fb_creteil_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Creteil', '');
INSERT INTO droits  values ('/mod_discipline/disc_stat.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Statistiques', '');
INSERT INTO droits  values ('/mod_epreuve_blanche/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Epreuves blanches: Activation/désactivation du module', '');
INSERT INTO droits  values ('/mod_examen_blanc/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Examens blancs: Activation/désactivation du module', '');
INSERT INTO droits  values ('/mod_epreuve_blanche/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Accueil', '');
INSERT INTO droits  values ('/mod_epreuve_blanche/transfert_cn.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Transfert vers carnet de notes', '');
INSERT INTO droits  values ('/mod_epreuve_blanche/saisie_notes.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Saisie des notes', '');
INSERT INTO droits  values ('/mod_epreuve_blanche/genere_emargement.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Génération émargement', '');
INSERT INTO droits  values ('/mod_epreuve_blanche/definir_salles.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Définir les salles', '');
INSERT INTO droits  values ('/mod_epreuve_blanche/attribuer_copies.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Attribuer les copies aux professeurs', '');
INSERT INTO droits  values ('/mod_epreuve_blanche/bilan.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Bilan', '');
INSERT INTO droits  values ('/mod_epreuve_blanche/genere_etiquettes.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Epreuve blanche: Génération étiquettes', '');
INSERT INTO droits  values ('/mod_examen_blanc/saisie_notes.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Examen blanc: Saisie devoir hors enseignement', '');
INSERT INTO droits  values ('/mod_examen_blanc/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Examen blanc: Accueil', '');
INSERT INTO droits  values ('/mod_examen_blanc/releve.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Examen blanc: Relevé', '');
INSERT INTO droits  values ('/mod_examen_blanc/bull_exb.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Examen blanc: Bulletins', '');
INSERT INTO droits  values ('/saisie/saisie_synthese_app_classe.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Synthèse des appréciations sur le groupe classe.', '');
INSERT INTO droits  values ('/gestion/saisie_message_connexion.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Saisie de messages de connexion.', '');
INSERT INTO droits  values ('/groupes/repartition_ele_grp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Répartir des élèves dans des groupes', '');
INSERT INTO droits  values ('/prepa_conseil/edit_limite_bis.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', '');
INSERT INTO droits  values ('/prepa_conseil/index2bis.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', '');
INSERT INTO droits  values ('/prepa_conseil/index3bis.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', '');
INSERT INTO droits  values ('/prepa_conseil/visu_toutes_notes_bis.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', '');
INSERT INTO droits  values ('/utilitaires/import_pays.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Import des pays', '');
INSERT INTO droits  values ('/mod_apb/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion du module Admissions PostBac', '');
INSERT INTO droits  values ('/mod_apb/index.php', 'F', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'Export XML pour le système Admissions Post-Bac', '');
INSERT INTO droits  values ('/mod_apb/export_xml.php', 'F', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'Export XML pour le système Admissions Post-Bac', '');
INSERT INTO droits  values ('/mod_gest_aid/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestionnaire d\'AID', '');
INSERT INTO droits  values ('/saisie/ajax_edit_limite.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', '');
INSERT INTO droits  values ('/mod_discipline/check_nature_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Recherche de natures d incident', '');
INSERT INTO droits  values ('/groupes/signalement_eleves.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Groupes: signalement des erreurs d affectation élève', '');
INSERT INTO droits  values ('/bulletin/envoi_mail.php', 'F', 'F', 'F', 'V', 'F', 'F', 'V', 'F', 'Envoi de mail via ajax', '');
INSERT INTO droits  values ('/mod_discipline/destinataires_alertes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Parametrage des destinataires de mail d alerte', '');
INSERT INTO droits  values ('/init_scribe_ng/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - index', '');
INSERT INTO droits  values ('/init_scribe_ng/etape1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 1', '');
INSERT INTO droits  values ('/init_scribe_ng/etape2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 2', '');
INSERT INTO droits  values ('/init_scribe_ng/etape3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 3', '');
INSERT INTO droits  values ('/init_scribe_ng/etape4.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 4', '');
INSERT INTO droits  values ('/init_scribe_ng/etape5.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 5', '');
INSERT INTO droits  values ('/init_scribe_ng/etape6.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 6', '');
INSERT INTO droits  values ('/init_scribe_ng/etape7.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation Scribe NG - etape 7', '');
INSERT INTO droits  values ('/mod_abs2/admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');
INSERT INTO droits  values ('/mod_abs2/admin/admin_motifs_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');
INSERT INTO droits  values ('/mod_abs2/admin/admin_types_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');
INSERT INTO droits  values ('/mod_abs2/admin/admin_justifications_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');
INSERT INTO droits  values ('/mod_abs2/admin/admin_actions_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');
INSERT INTO droits  values ('/mod_abs2/index.php', 'F', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Administration du module absences', '');
INSERT INTO droits  values ('/mod_abs2/saisir_groupe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Affichage du formulaire de saisie de absences', '');
INSERT INTO droits  values ('/mod_abs2/absences_du_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Affichage des absences du jour', '');
INSERT INTO droits  values ('/mod_abs2/enregistrement_saisie_groupe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Enregistrement des saisies d un groupe', '');
INSERT INTO droits  values ('/mod_abs2/liste_saisies.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Liste des saisies', '');
INSERT INTO droits  values ('/mod_abs2/liste_traitements.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Liste des traitements', '');
INSERT INTO droits  values ('/mod_abs2/liste_notifications.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Liste des notifications', '');
INSERT INTO droits  values ('/mod_abs2/liste_saisies_selection_traitement.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Liste des saisits pour faire les traitement', '');
INSERT INTO droits  values ('/mod_abs2/visu_saisie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Visualisation d une saisies', '');
INSERT INTO droits  values ('/mod_abs2/visu_traitement.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Visualisation d une saisie', '');
INSERT INTO droits  values ('/mod_abs2/visu_notification.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Visualisation d une notification', '');
INSERT INTO droits  values ('/mod_abs2/enregistrement_modif_saisie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Modification d une saisies', '');
INSERT INTO droits  values ('/mod_abs2/enregistrement_modif_traitement.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Modification d un traitement', '');
INSERT INTO droits  values ('/mod_abs2/enregistrement_modif_notification.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Modification d une notification', '');
INSERT INTO droits  values ('/mod_abs2/generer_notification.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'generer une notification', '');
INSERT INTO droits  values ('/mod_abs2/saisir_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'V', 'Saisir l absence d un eleve', '');
INSERT INTO droits  values ('/mod_abs2/enregistrement_saisie_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'V', 'Enregistrer absence d un eleve', '');
INSERT INTO droits  values ('/mod_abs2/creation_traitement.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Crer un traitement', '');
INSERT INTO droits  values ('/mod_discipline/saisie_incident_abs2.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Saisir un incident relatif a une absence', '');
INSERT INTO droits  values ('/saisie/validation_corrections.php', 'F', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'Validation des corrections proposées par des professeurs après la cloture d une période', '');
INSERT INTO droits  values ('/gestion/param_ordre_item.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modifier l ordre des items dans les menus', '');
INSERT INTO droits  values ('/mod_discipline/definir_categories.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les catégories', '');
INSERT INTO droits  values ('/mod_discipline/stats2/index.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Module discipline: Statistiques', '');


#
# table droits_aid
#
INSERT INTO droits_aid  values ('nom', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1');
INSERT INTO droits_aid  values ('numero', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1');
INSERT INTO droits_aid  values ('perso1', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'A préciser', '1');
INSERT INTO droits_aid  values ('perso2', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'A préciser', '1');
INSERT INTO droits_aid  values ('productions', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Production', '1');
INSERT INTO droits_aid  values ('resume', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Résumé', '1');
INSERT INTO droits_aid  values ('famille', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Famille', '1');
INSERT INTO droits_aid  values ('mots_cles', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Mots clés', '1');
INSERT INTO droits_aid  values ('adresse1', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Adresse publique', '1');
INSERT INTO droits_aid  values ('adresse2', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Adresse privée', '1');
INSERT INTO droits_aid  values ('public_destinataire', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Public destinataire', '1');
INSERT INTO droits_aid  values ('contacts', 'F', 'V', 'F', 'F', 'V', 'F', 'F', 'Contacts, ressources', '1');
INSERT INTO droits_aid  values ('divers', 'F', 'V', 'F', 'F', 'V', 'F', 'F', 'Divers', '1');
INSERT INTO droits_aid  values ('matiere1', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Discipline principale', '1');
INSERT INTO droits_aid  values ('matiere2', 'V', 'V', 'F', 'F', 'V', 'F', 'F', 'Discipline secondaire', '1');
INSERT INTO droits_aid  values ('eleve_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'A préciser', '1');
INSERT INTO droits_aid  values ('cpe_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'A préciser', '1');
INSERT INTO droits_aid  values ('prof_peut_modifier', '-', '-', '-', '-', '-', '-', '-', 'A préciser', '0');
INSERT INTO droits_aid  values ('fiche_publique', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1');
INSERT INTO droits_aid  values ('affiche_adresse1', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1');
INSERT INTO droits_aid  values ('en_construction', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'A préciser', '1');
INSERT INTO droits_aid  values ('perso3', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'A préciser', '0');


#
# table droits_speciaux
#
INSERT INTO droits_speciaux  values ('69', '2', '/mod_annees_anterieures/consultation_annee_anterieure.php', 'F');
INSERT INTO droits_speciaux  values ('68', '2', '/voir_abs', 'F');
INSERT INTO droits_speciaux  values ('67', '2', '/voir_bulle', 'F');
INSERT INTO droits_speciaux  values ('66', '2', '/voir_notes', 'F');
INSERT INTO droits_speciaux  values ('65', '2', '/voir_ens', 'F');
INSERT INTO droits_speciaux  values ('64', '2', '/voir_resp', 'F');
INSERT INTO droits_speciaux  values ('63', '2', '/eleves/liste_eleves.php', 'F');
INSERT INTO droits_speciaux  values ('62', '2', '/eleves/visu_eleve.php', 'F');
INSERT INTO droits_speciaux  values ('61', '2', '/messagerie/index.php', 'F');
INSERT INTO droits_speciaux  values ('60', '2', '/tous_les_edt', 'F');
INSERT INTO droits_speciaux  values ('59', '2', '/edt_organisation/index_edt.php', 'F');
INSERT INTO droits_speciaux  values ('58', '2', '/cahier_texte_admin/visa_ct.php', 'F');
INSERT INTO droits_speciaux  values ('57', '2', '/cahier_texte/see_all.php', 'F');
INSERT INTO droits_speciaux  values ('56', '2', '/mod_absences/lib/liste_absences.php', 'F');
INSERT INTO droits_speciaux  values ('55', '2', '/mod_absences/gestion/ajout_abs.php', 'F');
INSERT INTO droits_speciaux  values ('54', '2', '/mod_absences/gestion/select.php', 'F');
INSERT INTO droits_speciaux  values ('53', '2', '/mod_absences/lib/export_csv.php', 'F');
INSERT INTO droits_speciaux  values ('52', '2', '/mod_absences/lib/tableau.php', 'F');
INSERT INTO droits_speciaux  values ('51', '2', '/mod_absences/gestion/bilan_absences_quotidien_pdf.php', 'F');
INSERT INTO droits_speciaux  values ('50', '2', '/mod_absences/gestion/bilan_absences_classe.php', 'F');
INSERT INTO droits_speciaux  values ('49', '2', '/mod_absences/gestion/bilan_absences_quotidien.php', 'F');
INSERT INTO droits_speciaux  values ('48', '2', '/mod_absences/gestion/voir_absences_viescolaire.php', 'F');
INSERT INTO droits_speciaux  values ('47', '2', '/prepa_conseil/edit_limite.php', 'F');
INSERT INTO droits_speciaux  values ('46', '2', '/prepa_conseil/index3.php', 'F');
INSERT INTO droits_speciaux  values ('45', '2', '/cahier_notes/visu_releve_notes.php', 'F');
INSERT INTO droits_speciaux  values ('44', '2', '/gestion/info_gepi.php', 'V');
INSERT INTO droits_speciaux  values ('43', '2', '/gestion/contacter_admin.php', 'V');
INSERT INTO droits_speciaux  values ('42', '2', '/utilisateurs/mon_compte.php', 'V');
INSERT INTO droits_speciaux  values ('41', '2', '/accueil.php', 'V');
INSERT INTO droits_speciaux  values ('70', '2', '/mod_annees_anterieures/popup_annee_anterieure.php', 'F');
INSERT INTO droits_speciaux  values ('71', '2', '/mod_trombinoscopes/trombinoscopes.php', 'F');
INSERT INTO droits_speciaux  values ('72', '2', '/mod_trombinoscopes/trombi_impr.php', 'F');
INSERT INTO droits_speciaux  values ('73', '2', '/mod_discipline/index.php', 'F');
INSERT INTO droits_speciaux  values ('74', '2', '/mod_discipline/saisie_incident.php', 'F');
INSERT INTO droits_speciaux  values ('75', '2', '/mod_discipline/incidents_sans_protagonistes.php', 'F');
INSERT INTO droits_speciaux  values ('76', '2', '/mod_discipline/sauve_role.php', 'F');
INSERT INTO droits_speciaux  values ('77', '2', '/mod_discipline/update_colonne_retenue.php', 'F');
INSERT INTO droits_speciaux  values ('78', '2', '/mod_discipline/traiter_incident.php', 'F');
INSERT INTO droits_speciaux  values ('79', '2', '/mod_ooo/retenue.php', 'F');
INSERT INTO droits_speciaux  values ('80', '2', '/mod_ooo/rapport_incident.php', 'F');
INSERT INTO droits_speciaux  values ('81', '2', '/mod_abs2/index.php', 'F');
INSERT INTO droits_speciaux  values ('82', '2', '/mod_abs2/saisie_absence.php', 'F');
INSERT INTO droits_speciaux  values ('83', '2', '/mod_abs2/enregistrement saisie.php', 'F');
INSERT INTO droits_speciaux  values ('84', '2', '/mod_abs2/enregistrement_modif_saisie.php', 'F');
INSERT INTO droits_speciaux  values ('85', '2', '/mod_abs2/liste_saisie.php', 'F');
INSERT INTO droits_speciaux  values ('86', '2', '/mod_abs2/visu_saisie.php', 'F');
INSERT INTO droits_speciaux  values ('87', '2', '/mod_abs2/retenue.php', 'F');
INSERT INTO droits_speciaux  values ('88', '2', '/mod_abs2/rapport_incident.php', 'F');


#
# table droits_statut
#
INSERT INTO droits_statut  values ('2', 'infirmiere');


#
# table droits_utilisateurs
#


#
# table eb_copies
#


#
# table eb_epreuves
#


#
# table eb_groupes
#


#
# table eb_profs
#


#
# table eb_salles
#


#
# table ects_credits
#


#
# table ects_global_credits
#


#
# table edt_calendrier
#
INSERT INTO edt_calendrier  values ('1', '1;9;', 'Nouvelle période', '1267401600', '1268265540', '2010-03-01', '00:00:00', '2010-03-10', '23:59:00', '1', '1', '0');


#
# table edt_classes
#


#
# table edt_cours
#
INSERT INTO edt_cours  values ('351', '2', '', 'rie', 'lundi', '2', '3', '0', '0', '0', '0', 'profAngl');
INSERT INTO edt_cours  values ('350', '216', '', 'rie', 'lundi', '1', '2', '0', 'A', '0', '0', 'prof1');
INSERT INTO edt_cours  values ('349', '1', '', 'rie', 'lundi', '1', '2', '0', 'B', '0', '0', 'prof1');
INSERT INTO edt_cours  values ('433', '1', '', 'rie', 'mardi', '2', '4', '0.5', '0', '0', '0', 'prof1');


#
# table edt_creneaux
#
INSERT INTO edt_creneaux  values ('1', 'M1', '08:00:00', '08:55:00', '1', 'cours', '');
INSERT INTO edt_creneaux  values ('2', 'M2', '08:55:00', '09:50:00', '1', 'cours', '');
INSERT INTO edt_creneaux  values ('3', 'M3', '10:05:00', '11:00:00', '1', 'cours', '');
INSERT INTO edt_creneaux  values ('4', 'M4', '11:00:00', '11:55:00', '1', 'cours', '');
INSERT INTO edt_creneaux  values ('5', 'M5', '11:55:00', '12:30:00', '1', 'cours', '');
INSERT INTO edt_creneaux  values ('6', 'S1', '13:30:00', '14:25:00', '1', 'cours', '');
INSERT INTO edt_creneaux  values ('7', 'S2', '14:25:00', '15:20:00', '1', 'cours', '');
INSERT INTO edt_creneaux  values ('8', 'S3', '15:35:00', '16:30:00', '1', 'cours', '');
INSERT INTO edt_creneaux  values ('9', 'S4', '16:30:00', '17:30:00', '1', 'cours', '');
INSERT INTO edt_creneaux  values ('10', 'S5', '17:30:00', '18:25:00', '1', 'cours', '');
INSERT INTO edt_creneaux  values ('11', 'P1', '09:50:00', '10:05:00', '1', 'pause', '');
INSERT INTO edt_creneaux  values ('12', 'P2', '15:20:00', '15:35:00', '1', 'pause', '');
INSERT INTO edt_creneaux  values ('13', 'R', '12:00:00', '13:00:00', '1', 'repas', '');
INSERT INTO edt_creneaux  values ('14', 'R1', '13:00:00', '13:30:00', '1', 'pause', '');


#
# table edt_creneaux_bis
#


#
# table edt_dates_special
#


#
# table edt_init
#


#
# table edt_semaines
#
INSERT INTO edt_semaines  values ('1', '1', 'B', '0');
INSERT INTO edt_semaines  values ('2', '2', 'A', '0');
INSERT INTO edt_semaines  values ('3', '3', 'B', '0');
INSERT INTO edt_semaines  values ('4', '4', 'A', '0');
INSERT INTO edt_semaines  values ('5', '5', 'B', '0');
INSERT INTO edt_semaines  values ('6', '6', 'A', '0');
INSERT INTO edt_semaines  values ('7', '7', 'B', '0');
INSERT INTO edt_semaines  values ('8', '8', 'A', '0');
INSERT INTO edt_semaines  values ('9', '9', 'B', '0');
INSERT INTO edt_semaines  values ('10', '10', 'A', '0');
INSERT INTO edt_semaines  values ('11', '11', 'B', '0');
INSERT INTO edt_semaines  values ('12', '12', 'A', '0');
INSERT INTO edt_semaines  values ('13', '13', 'B', '0');
INSERT INTO edt_semaines  values ('14', '14', 'A', '0');
INSERT INTO edt_semaines  values ('15', '15', 'B', '0');
INSERT INTO edt_semaines  values ('16', '16', 'A', '0');
INSERT INTO edt_semaines  values ('17', '17', 'B', '0');
INSERT INTO edt_semaines  values ('18', '18', 'A', '0');
INSERT INTO edt_semaines  values ('19', '19', 'B', '0');
INSERT INTO edt_semaines  values ('20', '20', 'A', '0');
INSERT INTO edt_semaines  values ('21', '21', 'B', '0');
INSERT INTO edt_semaines  values ('22', '22', 'A', '0');
INSERT INTO edt_semaines  values ('23', '23', 'B', '0');
INSERT INTO edt_semaines  values ('24', '24', 'A', '0');
INSERT INTO edt_semaines  values ('25', '25', 'B', '0');
INSERT INTO edt_semaines  values ('26', '26', 'A', '0');
INSERT INTO edt_semaines  values ('27', '27', 'B', '0');
INSERT INTO edt_semaines  values ('28', '28', 'A', '0');
INSERT INTO edt_semaines  values ('29', '29', 'B', '0');
INSERT INTO edt_semaines  values ('30', '30', 'A', '0');
INSERT INTO edt_semaines  values ('31', '31', 'B', '0');
INSERT INTO edt_semaines  values ('32', '32', 'A', '0');
INSERT INTO edt_semaines  values ('33', '33', 'B', '0');
INSERT INTO edt_semaines  values ('34', '34', 'A', '0');
INSERT INTO edt_semaines  values ('35', '35', 'B', '0');
INSERT INTO edt_semaines  values ('36', '36', 'A', '0');
INSERT INTO edt_semaines  values ('37', '37', 'B', '0');
INSERT INTO edt_semaines  values ('38', '38', 'A', '0');
INSERT INTO edt_semaines  values ('39', '39', 'B', '0');
INSERT INTO edt_semaines  values ('40', '40', 'A', '0');
INSERT INTO edt_semaines  values ('41', '41', 'B', '0');
INSERT INTO edt_semaines  values ('42', '42', 'A', '0');
INSERT INTO edt_semaines  values ('43', '43', 'B', '0');
INSERT INTO edt_semaines  values ('44', '44', 'A', '0');
INSERT INTO edt_semaines  values ('45', '45', 'B', '0');
INSERT INTO edt_semaines  values ('46', '46', 'A', '0');
INSERT INTO edt_semaines  values ('47', '47', 'B', '0');
INSERT INTO edt_semaines  values ('48', '48', 'A', '0');
INSERT INTO edt_semaines  values ('49', '49', 'B', '0');
INSERT INTO edt_semaines  values ('50', '50', 'A', '0');
INSERT INTO edt_semaines  values ('51', '51', 'B', '0');
INSERT INTO edt_semaines  values ('52', '52', 'A', '0');
INSERT INTO edt_semaines  values ('53', '53', '', '0');


#
# table edt_setting
#
INSERT INTO edt_setting  values ('1', 'nom_creneaux_s', '1');
INSERT INTO edt_setting  values ('2', 'edt_aff_salle', 'nom');
INSERT INTO edt_setting  values ('3', 'edt_aff_matiere', 'long');
INSERT INTO edt_setting  values ('4', 'edt_aff_creneaux', 'noms');
INSERT INTO edt_setting  values ('5', 'edt_aff_init_infos', 'oui');
INSERT INTO edt_setting  values ('6', 'edt_aff_couleur', 'nb');
INSERT INTO edt_setting  values ('7', 'edt_aff_init_infos2', 'oui');
INSERT INTO edt_setting  values ('8', 'aff_cherche_salle', 'tous');
INSERT INTO edt_setting  values ('9', 'param_menu_edt', 'mouseover');
INSERT INTO edt_setting  values ('10', 'scolarite_modif_cours', 'y');
INSERT INTO edt_setting  values ('11', 'scolarite_modif_cours', 'y');
INSERT INTO edt_setting  values ('12', 'scolarite_modif_cours', 'y');
INSERT INTO edt_setting  values ('13', 'scolarite_modif_cours', 'y');


#
# table eleves
#
INSERT INTO eleves  values ('', 'testEleve1', 'nomtestEleve1', 'prenomtestEleve1', 'M', '1995-01-01', '', '', '', 'e000000001', '', '1', '0000-00-00 00:00:00', NULL);
INSERT INTO eleves  values ('', 'testEleve3', 'nomtestEleve3', 'prenomtestEleve3', 'M', '1900-01-01', '', '', '', 'e000000003', '', '3', '0000-00-00 00:00:00', NULL);
INSERT INTO eleves  values ('', 'testEleve4', 'nomtestEleve4', 'prenomtestEleve4', 'M', '1900-01-01', '', '', '', 'e000000004', '', '4', '0000-00-00 00:00:00', NULL);
INSERT INTO eleves  values ('', 'eleve7', 'eleve7', 'Jean', 'M', '1900-01-01', '', 'eleleve7', '', 'e000000005', '', '229', '0000-00-00 00:00:00', NULL);
INSERT INTO eleves  values ('', 'eleve8', 'eleve8', 'pren8om', 'M', '1900-01-01', '', '', '', 'e000000006', '', '230', '0000-00-00 00:00:00', NULL);
INSERT INTO eleves  values ('', 'eleve9', 'eleve9', 'pré9', 'M', '1900-01-01', '', 'eleve9eleo', '', 'e000000007', '', '231', '0000-00-00 00:00:00', NULL);
INSERT INTO eleves  values ('', 'eleve10', 'eleve10', 'j-f', 'M', '1900-01-01', '', 'eleve10eleo', '', 'e000000008', '', '232', '0000-00-00 00:00:00', NULL);
INSERT INTO eleves  values ('', 'eleve11', 'eleve11', 'emma', 'M', '1900-01-01', '', 'eleauieve11', '', 'e000000009', '', '233', '0000-00-00 00:00:00', NULL);


#
# table eleves_groupes_settings
#


#
# table etablissements
#
INSERT INTO etablissements  values ('999', 'étranger', 'aucun', 'aucun', '999', '');


#
# table etiquettes_formats
#
INSERT INTO etiquettes_formats  values ('1', 'Avery - A4 - 63,5 x 33,9 mm', '2', '2', '5', '5', '63.5', '33', '3', '8');


#
# table ex_classes
#


#
# table ex_examens
#


#
# table ex_groupes
#


#
# table ex_matieres
#


#
# table ex_notes
#


#
# table gc_affichages
#


#
# table gc_divisions
#


#
# table gc_ele_arriv_red
#


#
# table gc_eleves_options
#


#
# table gc_options
#


#
# table gc_options_classes
#


#
# table gc_projets
#


#
# table groupes
#
INSERT INTO groupes  values ('1', 'Math', 'Mathématiques', 'ny');
INSERT INTO groupes  values ('2', 'LV1anglais', 'LV1 anglais', 'nyn');
INSERT INTO groupes  values ('216', 'Math', 'Mathématiques', 'nyn');


#
# table horaires_etablissement
#
INSERT INTO horaires_etablissement  values ('1', '0000-00-00', 'lundi', '08:00:00', '17:30:00', '00:45:00', '1');
INSERT INTO horaires_etablissement  values ('2', '0000-00-00', 'mardi', '08:00:00', '17:30:00', '00:45:00', '1');
INSERT INTO horaires_etablissement  values ('3', '0000-00-00', 'mercredi', '08:00:00', '12:00:00', '00:00:00', '1');
INSERT INTO horaires_etablissement  values ('4', '0000-00-00', 'jeudi', '08:00:00', '17:30:00', '00:45:00', '1');
INSERT INTO horaires_etablissement  values ('5', '0000-00-00', 'vendredi', '08:00:00', '17:30:00', '00:45:00', '1');


#
# table inscription_items
#


#
# table inscription_j_login_items
#


#
# table j_aid_eleves
#
INSERT INTO j_aid_eleves  values ('0', 'testEleve2', '1');


#
# table j_aid_eleves_resp
#


#
# table j_aid_utilisateurs
#
INSERT INTO j_aid_utilisateurs  values ('0', 'profAngl', '1');
INSERT INTO j_aid_utilisateurs  values ('0', 'prof1', '1');


#
# table j_aid_utilisateurs_gest
#


#
# table j_aidcateg_utilisateurs
#


#
# table j_eleves_classes
#
INSERT INTO j_eleves_classes  values ('testEleve1', '1', '1', '0');
INSERT INTO j_eleves_classes  values ('testEleve1', '1', '2', '0');
INSERT INTO j_eleves_classes  values ('testEleve1', '1', '3', '0');
INSERT INTO j_eleves_classes  values ('testEleve2', '1', '1', '0');
INSERT INTO j_eleves_classes  values ('testEleve2', '1', '2', '0');
INSERT INTO j_eleves_classes  values ('testEleve2', '1', '3', '0');
INSERT INTO j_eleves_classes  values ('testEleve3', '9', '3', '0');
INSERT INTO j_eleves_classes  values ('testEleve3', '9', '2', '0');
INSERT INTO j_eleves_classes  values ('eleve10', '1', '1', '0');
INSERT INTO j_eleves_classes  values ('testEleve4', '9', '2', '0');
INSERT INTO j_eleves_classes  values ('testEleve4', '9', '3', '0');
INSERT INTO j_eleves_classes  values ('eleve10', '1', '3', '0');
INSERT INTO j_eleves_classes  values ('testEleve3', '9', '1', '0');
INSERT INTO j_eleves_classes  values ('eleve10', '1', '2', '0');
INSERT INTO j_eleves_classes  values ('eleve11', '1', '1', '0');
INSERT INTO j_eleves_classes  values ('eleve11', '1', '2', '0');
INSERT INTO j_eleves_classes  values ('eleve11', '1', '3', '0');
INSERT INTO j_eleves_classes  values ('eleve7', '1', '1', '0');
INSERT INTO j_eleves_classes  values ('eleve7', '1', '2', '0');
INSERT INTO j_eleves_classes  values ('eleve7', '1', '3', '0');
INSERT INTO j_eleves_classes  values ('eleve8', '1', '1', '0');
INSERT INTO j_eleves_classes  values ('eleve8', '1', '2', '0');
INSERT INTO j_eleves_classes  values ('eleve8', '1', '3', '0');
INSERT INTO j_eleves_classes  values ('eleve9', '1', '1', '0');
INSERT INTO j_eleves_classes  values ('eleve9', '1', '2', '0');
INSERT INTO j_eleves_classes  values ('eleve9', '1', '3', '0');


#
# table j_eleves_cpe
#
INSERT INTO j_eleves_cpe  values ('eleve10', 'cpe');
INSERT INTO j_eleves_cpe  values ('eleve11', 'cpe');
INSERT INTO j_eleves_cpe  values ('eleve7', 'cpe');
INSERT INTO j_eleves_cpe  values ('eleve8', 'cpe');
INSERT INTO j_eleves_cpe  values ('eleve9', 'cpe');
INSERT INTO j_eleves_cpe  values ('testEleve1', 'cpe');
INSERT INTO j_eleves_cpe  values ('testEleve2', 'cpe');
INSERT INTO j_eleves_cpe  values ('testEleve3', 'cpe');


#
# table j_eleves_etablissements
#


#
# table j_eleves_groupes
#
INSERT INTO j_eleves_groupes  values ('eleve10', '1', '1');
INSERT INTO j_eleves_groupes  values ('eleve10', '1', '2');
INSERT INTO j_eleves_groupes  values ('eleve10', '1', '3');
INSERT INTO j_eleves_groupes  values ('eleve11', '1', '1');
INSERT INTO j_eleves_groupes  values ('eleve11', '1', '2');
INSERT INTO j_eleves_groupes  values ('eleve11', '1', '3');
INSERT INTO j_eleves_groupes  values ('eleve7', '1', '1');
INSERT INTO j_eleves_groupes  values ('eleve7', '1', '2');
INSERT INTO j_eleves_groupes  values ('eleve7', '1', '3');
INSERT INTO j_eleves_groupes  values ('eleve8', '1', '1');
INSERT INTO j_eleves_groupes  values ('eleve8', '1', '2');
INSERT INTO j_eleves_groupes  values ('eleve8', '1', '3');
INSERT INTO j_eleves_groupes  values ('eleve9', '1', '1');
INSERT INTO j_eleves_groupes  values ('eleve9', '1', '2');
INSERT INTO j_eleves_groupes  values ('eleve9', '1', '3');
INSERT INTO j_eleves_groupes  values ('testEleve1', '1', '1');
INSERT INTO j_eleves_groupes  values ('testEleve1', '1', '2');
INSERT INTO j_eleves_groupes  values ('testEleve1', '1', '3');
INSERT INTO j_eleves_groupes  values ('testEleve2', '1', '1');
INSERT INTO j_eleves_groupes  values ('testEleve2', '1', '2');
INSERT INTO j_eleves_groupes  values ('testEleve2', '1', '3');
INSERT INTO j_eleves_groupes  values ('eleve10', '2', '1');
INSERT INTO j_eleves_groupes  values ('eleve10', '2', '2');
INSERT INTO j_eleves_groupes  values ('eleve10', '2', '3');
INSERT INTO j_eleves_groupes  values ('eleve11', '2', '1');
INSERT INTO j_eleves_groupes  values ('eleve11', '2', '2');
INSERT INTO j_eleves_groupes  values ('eleve11', '2', '3');
INSERT INTO j_eleves_groupes  values ('eleve7', '2', '1');
INSERT INTO j_eleves_groupes  values ('eleve7', '2', '2');
INSERT INTO j_eleves_groupes  values ('eleve7', '2', '3');
INSERT INTO j_eleves_groupes  values ('eleve8', '2', '1');
INSERT INTO j_eleves_groupes  values ('eleve8', '2', '2');
INSERT INTO j_eleves_groupes  values ('eleve8', '2', '3');
INSERT INTO j_eleves_groupes  values ('eleve9', '2', '1');
INSERT INTO j_eleves_groupes  values ('eleve9', '2', '2');
INSERT INTO j_eleves_groupes  values ('eleve9', '2', '3');
INSERT INTO j_eleves_groupes  values ('testEleve1', '2', '1');
INSERT INTO j_eleves_groupes  values ('testEleve1', '2', '2');
INSERT INTO j_eleves_groupes  values ('testEleve1', '2', '3');
INSERT INTO j_eleves_groupes  values ('testEleve2', '2', '1');
INSERT INTO j_eleves_groupes  values ('testEleve2', '2', '2');
INSERT INTO j_eleves_groupes  values ('testEleve2', '2', '3');
INSERT INTO j_eleves_groupes  values ('testEleve3', '216', '1');
INSERT INTO j_eleves_groupes  values ('testEleve3', '216', '2');
INSERT INTO j_eleves_groupes  values ('testEleve3', '216', '3');
INSERT INTO j_eleves_groupes  values ('testEleve4', '216', '2');
INSERT INTO j_eleves_groupes  values ('testEleve4', '216', '3');


#
# table j_eleves_professeurs
#
INSERT INTO j_eleves_professeurs  values ('eleve10', 'profAngl', '1');
INSERT INTO j_eleves_professeurs  values ('eleve11', 'profAngl', '1');
INSERT INTO j_eleves_professeurs  values ('eleve7', 'profAngl', '1');
INSERT INTO j_eleves_professeurs  values ('eleve8', 'profAngl', '1');
INSERT INTO j_eleves_professeurs  values ('eleve9', 'prof1', '1');
INSERT INTO j_eleves_professeurs  values ('testEleve1', 'prof1', '1');
INSERT INTO j_eleves_professeurs  values ('testEleve2', 'prof1', '1');
INSERT INTO j_eleves_professeurs  values ('testEleve3', 'prof1', '9');
INSERT INTO j_eleves_professeurs  values ('testEleve4', 'prof1', '9');


#
# table j_eleves_regime
#
INSERT INTO j_eleves_regime  values ('testEleve1', 'R', 'd/p');
INSERT INTO j_eleves_regime  values ('testEleve2', '-', 'int.');
INSERT INTO j_eleves_regime  values ('testEleve3', '', 'ext.');
INSERT INTO j_eleves_regime  values ('testEleve4', '', 'd/p');
INSERT INTO j_eleves_regime  values ('eleve7', '-', 'd/p');
INSERT INTO j_eleves_regime  values ('eleve8', '-', 'ext.');
INSERT INTO j_eleves_regime  values ('eleve9', '-', 'd/p');
INSERT INTO j_eleves_regime  values ('eleve10', '-', 'ext.');
INSERT INTO j_eleves_regime  values ('eleve11', '-', 'int.');


#
# table j_groupes_classes
#
INSERT INTO j_groupes_classes  values ('1', '1', '0', '0.0', '1', '1', '4', '-', '');
INSERT INTO j_groupes_classes  values ('2', '1', '0', '0.0', '1', '1', '2', '-', '');
INSERT INTO j_groupes_classes  values ('216', '9', '0', '0.0', '1', '1', '3', '-', '');


#
# table j_groupes_matieres
#
INSERT INTO j_groupes_matieres  values ('1', 'Math');
INSERT INTO j_groupes_matieres  values ('2', 'LV1anglais');
INSERT INTO j_groupes_matieres  values ('216', 'Math');


#
# table j_groupes_professeurs
#
INSERT INTO j_groupes_professeurs  values ('1', 'prof1', '0');
INSERT INTO j_groupes_professeurs  values ('2', '', '0');
INSERT INTO j_groupes_professeurs  values ('216', 'prof1', '0');
INSERT INTO j_groupes_professeurs  values ('2', 'profAngl', '0');


#
# table j_matieres_categories_classes
#
INSERT INTO j_matieres_categories_classes  values ('1', '1', '5', '0');
INSERT INTO j_matieres_categories_classes  values ('1', '9', '5', '0');
INSERT INTO j_matieres_categories_classes  values ('1', '115', '5', '0');


#
# table j_notifications_resp_pers
#


#
# table j_professeurs_matieres
#
INSERT INTO j_professeurs_matieres  values ('prof1', 'Math', '1');
INSERT INTO j_professeurs_matieres  values ('profAngl', 'LV1anglais', '0');


#
# table j_scol_classes
#
INSERT INTO j_scol_classes  values ('scola', '9');
INSERT INTO j_scol_classes  values ('scola', '1');
INSERT INTO j_scol_classes  values ('scola', '115');


#
# table j_signalement
#


#
# table j_traitements_envois
#


#
# table j_traitements_saisies
#


#
# table lettres_cadres
#
INSERT INTO lettres_cadres  values ('1', 'adresse responsable', '100', '40', '100', '5', 'A l\'attention de\r\n<civilitee_court_responsable> <nom_responsable> <prenom_responsable>\r\n<adresse_responsable>\r\n<cp_responsable> <commune_responsable>\r\n', '0', '||');
INSERT INTO lettres_cadres  values ('2', 'adresse etablissement', '0', '0', '0', '0', '', '0', '');
INSERT INTO lettres_cadres  values ('3', 'datation', '0', '0', '0', '0', '', '0', '');
INSERT INTO lettres_cadres  values ('4', 'corp avertissement', '10', '70', '0', '5', '<u>Objet: </u> <g>Avertissement</g>\r\n\r\n\r\n<nom_civilitee_long>,\r\n\r\nJe me vois dans l\'obligation de donner un <b>AVERTISSEMENT</b>\r\n\r\nà <g><nom_eleve> <prenom_eleve></g> élève de la classe <g><classe_eleve></g>.\r\n\r\n\r\npour la raison suivante : <g><sujet_eleve></g>\r\n\r\n<remarque_eleve>\r\n\r\n\r\n\r\nComme le prévoit le règlement intérieur de l\'établissement, il pourra être sanctionné à partir de ce jour.\r\nSanction(s) possible(s) :\r\n\r\n\r\n\r\n\r\nJe vous remercie de me renvoyer cet exemplaire après l\'avoir daté et signé.\r\nVeuillez agréer <nom_civilitee_long> <nom_responsable> l\'assurance de ma considération distinguée.\r\n\r\n\r\n\r\nDate et signatures des parents :	', '0', '||');
INSERT INTO lettres_cadres  values ('5', 'corp blame', '10', '70', '0', '5', '<u>Objet</u>: <g>Blâme</g>\r\n\r\n\r\n<nom_civilitee_long>\r\n\r\nJe me vois dans l\'obligation de donner un BLAME \r\n\r\nà <g><nom_eleve> <prenom_eleve></g> élève de la classe <g><classe_eleve></g>.\r\n\r\nDemandé par: <g><courrier_demande_par></g>\r\n\r\npour la raison suivante: <g><raison></g>\r\n\r\n<remarque>\r\n\r\nJe vous remercie de me renvoyer cet exemplaire après l\'avoir daté et signé.\r\nVeuillez agréer <g><nom_civilitee_long> <nom_responsable></g> l\'assurance de ma considération distinguée.\r\n\r\n<u>Date et signatures des parents:</u>\r\n\r\n\r\n\r\n\r\n\r\nNous demandons un entretien avec la personne ayant demandé la sanction OUI / NON.\r\n(La prise de rendez-vous est à votre initiative)\r\n', '0', '||');
INSERT INTO lettres_cadres  values ('6', 'corp convocation parents', '10', '70', '0', '5', '<u>Objet</u>: <g>Convocation des parents</g>\r\n\r\n\r\n<nom_civilitee_long>,\r\n\r\nVous êtes prié de prendre contact avec le Conseiller Principal d\'Education dans les plus brefs délais, au sujet de <g><nom_eleve> <prenom_eleve></g> inscrit en classe de <g><classe_eleve></g>.\r\n\r\npour le motif suivant:\r\n\r\n<remarque>\r\n\r\n\r\n\r\nSans nouvelle de votre part avant le ........................................., je serai dans l\'obligation de procéder à la descolarisation de l\'élève, avec les conséquences qui en résulteront, jusqu\'à votre rencontre.\r\n\r\n\r\nVeuillez agréer <g><nom_civilitee_long> <nom_responsable></g> l\'assurance de ma considération distinguée.', '0', '||');
INSERT INTO lettres_cadres  values ('7', 'corp exclusion', '10', '70', '0', '5', '<u>Objet: </u> <g>Sanction - Exclusion de l\'établissement</g>\r\n\r\n\r\n<nom_civilitee_long>,\r\n\r\nPar la présente, je tiens à vous signaler que <nom_eleve>\r\n\r\ninscrit en classe de  <classe_eleve>\r\n\r\n\r\ns\'étant rendu coupable des faits suivants : \r\n\r\n<remarque>\r\n\r\n\r\n\r\nEst exclu de l\'établissement,\r\nà compter du: <b><date_debut></b> à <b><heure_debut></b>,\r\njusqu\'au: <b><date_fin></b> à <b><heure_fin></b>.\r\n\r\n\r\nIl devra se présenter, au bureau de la Vie Scolaire \r\n\r\nle ....................................... à ....................................... ACCOMPAGNE DE SES PARENTS.\r\n\r\n\r\n\r\n\r\nVeuillez agréer &lt;TYPEPARENT&gt; &lt;NOMPARENT&gt; l\'assurance de ma considération distinguée.', '0', '||');
INSERT INTO lettres_cadres  values ('8', 'corp demande justificatif absence', '10', '70', '0', '5', '<u>Objet: </u> <g>Demande de justificatif d\'absence</g>\r\n\r\n\r\n<civilitee_long_responsable>,\r\n\r\nJ\'ai le regret de vous informer que <b><nom_eleve> <prenom_eleve></b>, élève en classe de <b><classe_eleve></b> n\'a pas assisté au(x) cours:\r\n\r\n<liste>\r\n\r\nJe vous prie de bien vouloir me faire connaître le motif de son absence.\r\n\r\nPour permettre un contrôle efficace des présences, toute absence d\'un élève doit être justifiée par sa famille, le jour même soit par téléphone, soit par écrit, soit par fax.\r\n\r\nAvant de regagner les cours, l\'élève absent devra se présenter au bureau du Conseiller Principal d\'Education muni de son carnet de correspondance avec un justificatif signé des parents.\r\n\r\nVeuillez agréer <civilitee_long_responsable> <nom_responsable>, l\'assurance de ma considération distinguée.\r\n                                               \r\nCPE\r\n<civilitee_long_cpe> <nom_cpe> <prenom_cpe>\r\n\r\nPrière de renvoyer, par retour du courrier, le présent avis signé des parents :\r\n\r\nMotif de l\'absence : \r\n________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________\r\n\r\n\r\n\r\nDate et signatures des parents :  \r\n', '0', '||');
INSERT INTO lettres_cadres  values ('10', 'signature', '100', '180', '0', '5', '<b><courrier_signe_par_fonction></b>,\r\n<courrier_signe_par>\r\n', '0', '||');


#
# table lettres_suivis
#


#
# table lettres_tcs
#
INSERT INTO lettres_tcs  values ('1', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('2', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('3', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('4', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('5', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('6', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('7', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('8', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('9', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('10', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('11', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('12', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('13', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('14', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('15', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('16', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('17', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('18', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('19', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('20', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('21', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('22', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('23', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('24', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('25', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('26', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('27', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('28', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('29', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('30', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('31', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('32', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('33', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('34', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('35', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('36', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('37', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('38', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('39', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('40', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('41', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('42', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('43', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('44', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('45', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('46', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('47', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('48', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('49', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('50', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('51', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('52', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('53', '3', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('56', '3', '1', '100', '40', '100', '5', '0');
INSERT INTO lettres_tcs  values ('57', '3', '4', '10', '70', '0', '5', '0');
INSERT INTO lettres_tcs  values ('58', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('59', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('60', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('61', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('62', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('63', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('64', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('65', '1', '1', '100', '40', '100', '5', '0');
INSERT INTO lettres_tcs  values ('66', '1', '5', '10', '70', '0', '5', '0');
INSERT INTO lettres_tcs  values ('68', '2', '1', '100', '40', '100', '5', '0');
INSERT INTO lettres_tcs  values ('69', '2', '6', '10', '70', '0', '5', '0');
INSERT INTO lettres_tcs  values ('70', '4', '1', '100', '40', '100', '5', '0');
INSERT INTO lettres_tcs  values ('71', '4', '7', '10', '70', '0', '5', '0');
INSERT INTO lettres_tcs  values ('72', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('73', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('74', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('75', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('76', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('77', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('78', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('79', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('80', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('81', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('82', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('83', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('84', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('85', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('86', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('87', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('88', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('89', '6', '1', '100', '40', '100', '5', '0');
INSERT INTO lettres_tcs  values ('90', '6', '8', '10', '70', '0', '5', '0');
INSERT INTO lettres_tcs  values ('91', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('92', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('93', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('94', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('95', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('96', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('97', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('98', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('99', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('100', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('101', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('102', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('103', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('104', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('105', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('106', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('107', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('108', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('109', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('110', '7', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('111', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('112', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('113', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('114', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('115', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('116', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('117', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('118', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('119', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('120', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('121', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('122', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('123', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('124', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('125', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('126', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('127', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('128', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('129', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('130', '1', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('131', '2', '10', '100', '180', '0', '5', '0');
INSERT INTO lettres_tcs  values ('132', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('133', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('134', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('135', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('136', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('137', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('138', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('139', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('140', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('141', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('142', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('143', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('144', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('145', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('146', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('147', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('148', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('149', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('150', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('151', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('152', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('153', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('154', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('155', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('156', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('157', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('158', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('159', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('160', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('161', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('162', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('163', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('164', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('165', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('166', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('167', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('168', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('169', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('170', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('171', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('172', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('173', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('174', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('175', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('176', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('177', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('178', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('179', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('180', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('181', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('182', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('183', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('184', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('185', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('186', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('187', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('188', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('189', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('190', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('191', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('192', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('193', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('194', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('195', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('196', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('197', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('198', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('199', '6', '0', '0', '0', '0', '0', '0');
INSERT INTO lettres_tcs  values ('200', '6', '0', '0', '0', '0', '0', '0');


#
# table lettres_types
#
INSERT INTO lettres_types  values ('1', 'blame', 'sanction', '');
INSERT INTO lettres_types  values ('2', 'convocation des parents', 'suivi', '');
INSERT INTO lettres_types  values ('3', 'avertissement', 'sanction', '');
INSERT INTO lettres_types  values ('4', 'exclusion', 'sanction', '');
INSERT INTO lettres_types  values ('5', 'certificat de scolarité', 'suivi', '');
INSERT INTO lettres_types  values ('6', 'demande de justificatif d\'absence', 'suivi', 'oui');
INSERT INTO lettres_types  values ('7', 'demande de justificatif de retard', 'suivi', '');
INSERT INTO lettres_types  values ('8', 'rapport d\'incident', 'sanction', '');
INSERT INTO lettres_types  values ('9', 'regime de sortie', 'suivi', '');
INSERT INTO lettres_types  values ('10', 'retenue', 'sanction', '');


#
# table matieres
#
INSERT INTO matieres  values ('Math', 'Mathématiques', '0', '1', 'n', 'n');
INSERT INTO matieres  values ('LV1anglais', 'LV1 anglais', '0', '1', 'n', 'n');
INSERT INTO matieres  values ('LettreModerne', 'LettresModernes', '0', '1', 'n', 'n');
INSERT INTO matieres  values ('EPS', 'EPS', '0', '1', 'n', 'n');


#
# table matieres_app_corrections
#


#
# table matieres_appreciations
#


#
# table matieres_appreciations_acces
#
INSERT INTO matieres_appreciations_acces  values ('1', 'responsable', '1', '0000-00-00', 'y');
INSERT INTO matieres_appreciations_acces  values ('1', 'responsable', '2', '0000-00-00', 'n');
INSERT INTO matieres_appreciations_acces  values ('1', 'responsable', '3', '0000-00-00', 'n');
INSERT INTO matieres_appreciations_acces  values ('9', 'responsable', '1', '0000-00-00', 'n');
INSERT INTO matieres_appreciations_acces  values ('9', 'responsable', '2', '0000-00-00', 'n');
INSERT INTO matieres_appreciations_acces  values ('9', 'responsable', '3', '0000-00-00', 'n');
INSERT INTO matieres_appreciations_acces  values ('1', 'eleve', '1', '0000-00-00', 'y');
INSERT INTO matieres_appreciations_acces  values ('1', 'eleve', '2', '0000-00-00', 'n');
INSERT INTO matieres_appreciations_acces  values ('1', 'eleve', '3', '0000-00-00', 'n');
INSERT INTO matieres_appreciations_acces  values ('9', 'eleve', '1', '0000-00-00', 'n');
INSERT INTO matieres_appreciations_acces  values ('9', 'eleve', '2', '0000-00-00', 'n');
INSERT INTO matieres_appreciations_acces  values ('9', 'eleve', '3', '0000-00-00', 'n');


#
# table matieres_appreciations_grp
#


#
# table matieres_appreciations_tempo
#


#
# table matieres_categories
#
INSERT INTO matieres_categories  values ('1', 'Autres', 'Autres', '5');


#
# table matieres_notes
#
INSERT INTO matieres_notes  values ('testEleve1', '1', '2', '2.0', '', '0');
INSERT INTO matieres_notes  values ('testEleve2', '1', '2', '5.0', '', '0');


#
# table message_login
#
INSERT INTO message_login  values ('1', 'Base de test');


#
# table messages
#


#
# table miseajour
#


#
# table model_bulletin
#
INSERT INTO model_bulletin  values (1, 'Standard', 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'Arial', 110, 40, 5, 40, 1, 110, 5, 1, 5, 5, 72, 200, 175, 8, 8, 10, 18, 5, 5, 246.3, 5, 5, 250, 130, 37, 1, 138, 250, 67, 37, 0, 0, 'DUPLICATA INTERNET', 1, 1, 1, 1, 75, 75, 0, 1, 255, 255, 207, 1, 239, 239, 239, 1, 239, 239, 239, 1, 239, 239, 239, 'Matière', 'coef.', 'nb. n.', 'rang', 'Appréciation / Conseils', 0, 0.01, 2, 0, 1, 1, 1, 1, 0, 0, 40, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, 0, '', 0, 0, 18, 0, 1, 1, 1);
INSERT INTO model_bulletin  values (2, 'Standard avec photo', 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'Arial', 110, 40, 5, 40, 1, 110, 5, 1, 5, 5, 72, 200, 175, 8, 8, 10, 18, 5, 5, 246.3, 5, 5, 250, 130, 37, 1, 138, 250, 67, 37, 0, 0, 'DUPLICATA INTERNET', 1, 1, 1, 1, 75, 75, 0, 1, 255, 255, 207, 1, 239, 239, 239, 1, 239, 239, 239, 1, 239, 239, 239, 'Matière', 'coef.', 'nb. n.', 'rang', 'Appréciation / Conseils', 0, 0, 2, 0, 1, 1, 1, 1, 0, 0, 40, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, 0, '', 0, 0, 18, 0, 1, 1, 1);
INSERT INTO model_bulletin  values (3, 'Affiche tout', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'Arial', 110, 40, 5, 40, 1, 110, 5, 1, 5, 5, 72, 200, 175, 8, 8, 10, 16.5, 6.5, 5, 246.3, 5, 5, 250, 130, 37, 1, 138, 250, 67, 37, 1, 0, 'DUPLICATA INTERNET', 1, 1, 1, 1, 75, 75, 1, 1, 255, 255, 207, 1, 239, 239, 239, 1, 239, 239, 239, 1, 239, 239, 239, 'Matière', 'coef.', 'nb. n.', 'rang', 'Appréciation / Conseils', 1, 0.01, 2, 0, 1, 1, 2, 1, 1, 1, 40, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, 0, '', 0, 0, 18, 0, 1, 1, 1);


#
# table notanet
#


#
# table notanet_app
#


#
# table notanet_avis
#


#
# table notanet_corresp
#


#
# table notanet_ele_type
#


#
# table notanet_socles
#


#
# table notanet_verrou
#


#
# table pays
#


#
# table periodes
#
INSERT INTO periodes  values ('Premier trimestre', '1', 'O', '1', '2010-07-08 22:48:47', '2010-05-08 00:00:00');
INSERT INTO periodes  values ('Deuxieme trimestre', '2', 'N', '1', '2010-06-14 17:29:50', '0000-00-00 00:00:00');
INSERT INTO periodes  values ('Troisieme trimestre', '3', 'O', '1', '2010-04-07 11:52:04', '0000-00-00 00:00:00');
INSERT INTO periodes  values ('période 2', '2', 'N', '9', '2010-06-20 18:26:31', '0000-00-00 00:00:00');
INSERT INTO periodes  values ('période 1', '1', 'N', '9', '2010-06-20 18:25:28', '0000-00-00 00:00:00');
INSERT INTO periodes  values ('période 3', '3', 'N', '9', '2010-06-20 18:26:31', '0000-00-00 00:00:00');
INSERT INTO periodes  values ('période 1', '1', 'N', '115', '2010-07-08 22:18:33', '0000-00-00 00:00:00');
INSERT INTO periodes  values ('période 2', '2', 'N', '115', '2010-07-08 22:18:33', '0000-00-00 00:00:00');
INSERT INTO periodes  values ('période 3', '3', 'N', '115', '2010-07-08 22:18:33', '0000-00-00 00:00:00');


#
# table plugins
#


#
# table plugins_autorisations
#


#
# table plugins_menus
#


#
# table preferences
#
INSERT INTO preferences  values ('prof1', 'cdt_version', '2');


#
# table ref_wiki
#
INSERT INTO ref_wiki  values ('1', 'enseignement_invisible', 'http://www.sylogix.org/wiki/gepi/Enseignement_invisible');
INSERT INTO ref_wiki  values ('2', 'enseignement_invisible', 'http://www.sylogix.org/wiki/gepi/Enseignement_invisible');
INSERT INTO ref_wiki  values ('3', 'enseignement_invisible', 'http://www.sylogix.org/wiki/gepi/Enseignement_invisible');
INSERT INTO ref_wiki  values ('4', 'enseignement_invisible', 'http://www.sylogix.org/wiki/gepi/Enseignement_invisible');


#
# table resp_adr
#
INSERT INTO resp_adr  values ('a000000001', '58 ici et la', '', '', '', '54321', '', 'ciel');
INSERT INTO resp_adr  values ('a000000002', 'eauieaui', '', '', '', '46954', '', 'eaeauieauie');
INSERT INTO resp_adr  values ('a000000003', '5 rue du paradis', '', '', '', '11001', '', 'Kerr');
INSERT INTO resp_adr  values ('a000000004', '50 av du plot', 'vert', '', '', '99560', '', 'Willis');
INSERT INTO resp_adr  values ('a000000005', '3 bis rue Prepipene', '', '', '', '65326', '', 'Ulret');


#
# table resp_pers
#
INSERT INTO resp_pers  values ('p000000001', 'Pere', 'Pere1', 'PrenomPere', '', '', '4654879', '', 'joss', 'a000000001');
INSERT INTO resp_pers  values ('p000000002', '', 'euie', 'uieuie', '', '456', '11245', '4567', 'auie@aauieaeaeui', 'a000000002');
INSERT INTO resp_pers  values ('p000000003', '', 'respAdulte', 'prenomdeResp', '', '0612345678', '', '', '', 'a000000003');
INSERT INTO resp_pers  values ('p000000004', '', 'pere2', 'prenompere2', '', '', '069876543210', '', 'auie@auie.com', 'a000000004');
INSERT INTO resp_pers  values ('p000000005', '', 'pere3', 'prenomdupere3', '', '', '', '015632165980', '', 'a000000005');


#
# table responsables
#


#
# table responsables2
#
INSERT INTO responsables2  values ('e000000007', 'p000000005', '1', '');
INSERT INTO responsables2  values ('e000000006', 'p000000004', '1', '');
INSERT INTO responsables2  values ('e000000005', 'p000000003', '1', '');
INSERT INTO responsables2  values ('e000000001', 'p000000002', '2', '1');
INSERT INTO responsables2  values ('e000000001', 'p000000001', '1', '');


#
# table s_alerte_mail
#


#
# table s_autres_sanctions
#


#
# table s_categories
#


#
# table s_communication
#


#
# table s_exclusions
#


#
# table s_incidents
#
INSERT INTO s_incidents  values ('1', 'PROF1', '2010-04-24', '', '0', '', '', '', '', '');
INSERT INTO s_incidents  values ('2', 'PROF1', '2010-04-24', '', '0', '', '', '', '', '');
INSERT INTO s_incidents  values ('3', 'PROF1', '2010-04-24', '', '0', '', '', '', '', '');
INSERT INTO s_incidents  values ('4', 'PROF1', '2010-04-24', '', '0', '', '', '', '', '');
INSERT INTO s_incidents  values ('5', 'PROF1', '2010-04-24', '', '0', '', '', '', '', '');
INSERT INTO s_incidents  values ('6', 'PROF1', '2010-04-24', '08:00', '0', 'exclusion de cours', '', '', 'clos', '6.20100424221746.582e87');
INSERT INTO s_incidents  values ('7', 'PROF1', '2010-04-24', '08:55', '0', 'exclusion de cours', '', '', '', '');
INSERT INTO s_incidents  values ('8', 'PROF1', '2010-04-25', '15:20', '0', 'exclusion de cours', '', '', '', '');
INSERT INTO s_incidents  values ('9', 'PROF1', '2010-04-25', '15:20', '0', 'exclusion de cours', '', '', '', '');
INSERT INTO s_incidents  values ('10', 'PROF1', '2010-04-25', '15:20', '0', 'exclusion de cours', '', '', '', '');
INSERT INTO s_incidents  values ('11', 'PROF1', '2010-04-25', '15:20', '0', 'exclusion de cours', '', '', '', '');
INSERT INTO s_incidents  values ('12', 'PROF1', '2010-04-25', '10:05', '0', 'exclusion de cours', '', '', '', '');
INSERT INTO s_incidents  values ('13', 'PROF1', '2010-04-25', '08:55', '0', 'exclusion de cours', '', '', '', '');
INSERT INTO s_incidents  values ('14', 'PROF1', '2010-04-25', '09:50', '0', 'exclusion de cours', '', '', '', '');
INSERT INTO s_incidents  values ('15', 'PROF1', '2010-04-25', '08:00', '0', 'exclusion de cours', '', '', '', '');
INSERT INTO s_incidents  values ('16', 'CPE', '2010-06-23', '08:00', '0', 'exclusion de cours', '', 'test', '', '');


#
# table s_lieux_incidents
#
INSERT INTO s_lieux_incidents  values ('1', 'Classe');
INSERT INTO s_lieux_incidents  values ('2', 'Couloir');
INSERT INTO s_lieux_incidents  values ('3', 'Cour');
INSERT INTO s_lieux_incidents  values ('4', 'Réfectoire');
INSERT INTO s_lieux_incidents  values ('5', 'Autre');
INSERT INTO s_lieux_incidents  values ('6', 'Classe');
INSERT INTO s_lieux_incidents  values ('7', 'Couloir');
INSERT INTO s_lieux_incidents  values ('8', 'Cour');
INSERT INTO s_lieux_incidents  values ('9', 'Réfectoire');
INSERT INTO s_lieux_incidents  values ('10', 'Autre');
INSERT INTO s_lieux_incidents  values ('11', 'Classe');
INSERT INTO s_lieux_incidents  values ('12', 'Couloir');
INSERT INTO s_lieux_incidents  values ('13', 'Cour');
INSERT INTO s_lieux_incidents  values ('14', 'Réfectoire');
INSERT INTO s_lieux_incidents  values ('15', 'Autre');
INSERT INTO s_lieux_incidents  values ('16', 'Classe');
INSERT INTO s_lieux_incidents  values ('17', 'Couloir');
INSERT INTO s_lieux_incidents  values ('18', 'Cour');
INSERT INTO s_lieux_incidents  values ('19', 'Réfectoire');
INSERT INTO s_lieux_incidents  values ('20', 'Autre');


#
# table s_mesures
#
INSERT INTO s_mesures  values ('1', 'prise', 'Travail supplémentaire', '');
INSERT INTO s_mesures  values ('2', 'prise', 'Mot dans le carnet de liaison', '');
INSERT INTO s_mesures  values ('3', 'demandee', 'Retenue', '');
INSERT INTO s_mesures  values ('4', 'demandee', 'Exclusion', '');
INSERT INTO s_mesures  values ('5', 'prise', 'Travail supplémentaire', '');
INSERT INTO s_mesures  values ('6', 'prise', 'Mot dans le carnet de liaison', '');
INSERT INTO s_mesures  values ('7', 'demandee', 'Retenue', '');
INSERT INTO s_mesures  values ('8', 'demandee', 'Exclusion', '');
INSERT INTO s_mesures  values ('9', 'prise', 'Travail supplémentaire', '');
INSERT INTO s_mesures  values ('10', 'prise', 'Mot dans le carnet de liaison', '');
INSERT INTO s_mesures  values ('11', 'demandee', 'Retenue', '');
INSERT INTO s_mesures  values ('12', 'demandee', 'Exclusion', '');
INSERT INTO s_mesures  values ('13', 'prise', 'Travail supplémentaire', '');
INSERT INTO s_mesures  values ('14', 'prise', 'Mot dans le carnet de liaison', '');
INSERT INTO s_mesures  values ('15', 'demandee', 'Retenue', '');
INSERT INTO s_mesures  values ('16', 'demandee', 'Exclusion', '');


#
# table s_protagonistes
#
INSERT INTO s_protagonistes  values ('1', '1', 'testEleve1', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('2', '2', 'testEleve2', 'eleve', '', 'N');
INSERT INTO s_protagonistes  values ('3', '3', 'testEleve2', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('4', '4', 'testEleve1', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('5', '5', 'testEleve1', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('6', '6', 'testEleve1', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('7', '7', 'testEleve1', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('8', '8', 'testEleve1', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('9', '9', 'testEleve1', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('10', '10', 'testEleve1', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('11', '11', 'testEleve1', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('12', '12', 'testEleve1', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('13', '13', 'testEleve1', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('14', '14', 'testEleve1', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('15', '15', 'testEleve1', 'eleve', 'Responsable', 'N');
INSERT INTO s_protagonistes  values ('16', '16', 'testEleve1', 'eleve', 'Responsable', 'N');


#
# table s_qualites
#
INSERT INTO s_qualites  values ('1', 'Responsable');
INSERT INTO s_qualites  values ('2', 'Victime');
INSERT INTO s_qualites  values ('3', 'Témoin');
INSERT INTO s_qualites  values ('4', 'Autre');
INSERT INTO s_qualites  values ('5', 'Responsable');
INSERT INTO s_qualites  values ('6', 'Victime');
INSERT INTO s_qualites  values ('7', 'Témoin');
INSERT INTO s_qualites  values ('8', 'Autre');
INSERT INTO s_qualites  values ('9', 'Responsable');
INSERT INTO s_qualites  values ('10', 'Victime');
INSERT INTO s_qualites  values ('11', 'Témoin');
INSERT INTO s_qualites  values ('12', 'Autre');
INSERT INTO s_qualites  values ('13', 'Responsable');
INSERT INTO s_qualites  values ('14', 'Victime');
INSERT INTO s_qualites  values ('15', 'Témoin');
INSERT INTO s_qualites  values ('16', 'Autre');


#
# table s_retenues
#


#
# table s_sanctions
#


#
# table s_traitement_incident
#


#
# table s_travail
#


#
# table s_types_sanctions
#
INSERT INTO s_types_sanctions  values ('1', 'Avertissement travail');
INSERT INTO s_types_sanctions  values ('2', 'Avertissement comportement');
INSERT INTO s_types_sanctions  values ('3', 'Avertissement travail');
INSERT INTO s_types_sanctions  values ('4', 'Avertissement comportement');
INSERT INTO s_types_sanctions  values ('5', 'Avertissement travail');
INSERT INTO s_types_sanctions  values ('6', 'Avertissement comportement');
INSERT INTO s_types_sanctions  values ('7', 'Avertissement travail');
INSERT INTO s_types_sanctions  values ('8', 'Avertissement comportement');


#
# table salle_cours
#


#
# table setting
#
INSERT INTO setting  values ('version', 'trunk');
INSERT INTO setting  values ('versionRc', '');
INSERT INTO setting  values ('versionBeta', '');
INSERT INTO setting  values ('sessionMaxLength', '80');
INSERT INTO setting  values ('Impression', '<center><p class = \"grand\">Gestion des Elèves Par Internet</p></center>\r\n<br />\r\n<p class = \"grand\">Qu\'est-ce que GEPI ?</p>\r\n\r\n<p>Afin d\'étudier les modalités d\'informatisation des bulletins scolaires : notes et appréciations via Internet, une expérimentation (baptisée Gestion des Elèves Par Internet)a été mise en place. Cette expérimentation concerne les classes suivantes : \r\n<br />* ....\r\n<br />* ....\r\n<br />\r\n<br />\r\nCeci vous concerne car vous êtes professeur enseignant dans l\'une ou l\'autre de ces classes.\r\n<br />\r\n<br />\r\nA partir de la réception de ce document, vous pourrez remplir les bulletins informatisés :\r\n<span class = \"norme\">\r\n<UL><li>soit au lycée à partir de n\'importe quel poste connecté à Internet,\r\n<li>soit chez vous si vous disposez d\'une connexion Internet.\r\n</ul>\r\n</span>\r\n<p class = \"grand\">Comment accéder au module de saisie (notes etappréciations) :</p>\r\n<span class = \"norme\">\r\n<UL>\r\n    <LI>Se connecter à Internet\r\n    <LI>Lancer un navigateur (FireFox de préférence, Opera, Internet Explorer, ...)\r\n    <LI>Se connecter au site : https://adresse_du_site/gepi\r\n    <LI>Après quelques instants une page apparaît vous invitant à entrer un nom d\'identifiant et un mot de passe (cesinformations figurent en haut de cette page).\r\n    <br />ATTENTION : votre mot de passe est strictement confidentiel.\r\n    <br />\r\n    <br />Une fois ces informations fournies, cliquez sur le bouton \"Ok\".\r\n    <LI> Après quelques instants une page d\'accueil apparaît.<br />\r\nLa première fois, Gepi vous demande de changer votre mot de passe.\r\nChoisissez-en un facile à retenir, mais non trivial (évitez toute date\r\nde naissance, nom d\'animal familier, prénom, etc.), et contenant\r\nlettre(s), chiffre(s), et caractère(s) non alphanumérique(s).<br />\r\nLes fois suivantes, vous arriverez directement au menu général de\r\nl\'application. Pour bien prendre connaissance des possibilités de\r\nl\'application, n\'hésitez pas à essayer tous les liens disponibles !\r\n</ul></span>\r\n<p class = \"grand\">Remarque :</p>\r\n<p>GEPI est prévu pour que chaque professeur ne puisse modifier les notes ou les appréciations que dans les rubriques qui le concernent et uniquement pour ses élèves.\r\n<br />\r\nJe reste à votre disposition pour tout renseignement complémentaire.\r\n    <br />\r\n    Le proviseur adjoint\r\n</p>');
INSERT INTO setting  values ('gepiYear', '2012/2013');
INSERT INTO setting  values ('gepiSchoolName', 'Etablissement de test');
INSERT INTO setting  values ('gepiSchoolAdress1', 'Adresse');
INSERT INTO setting  values ('gepiSchoolAdress2', 'Boîte postale');
INSERT INTO setting  values ('gepiSchoolZipCode', 'Code postal');
INSERT INTO setting  values ('gepiSchoolCity', 'Ville');
INSERT INTO setting  values ('gepiAdminAdress', 'email.admin@example.com');
INSERT INTO setting  values ('titlesize', '14');
INSERT INTO setting  values ('textsize', '8');
INSERT INTO setting  values ('cellpadding', '3');
INSERT INTO setting  values ('cellspacing', '1');
INSERT INTO setting  values ('largeurtableau', '800');
INSERT INTO setting  values ('col_matiere_largeur', '150');
INSERT INTO setting  values ('begin_bookings', '1156975200');
INSERT INTO setting  values ('end_bookings', '1188511200');
INSERT INTO setting  values ('max_size', '307200');
INSERT INTO setting  values ('total_max_size', '5242880');
INSERT INTO setting  values ('col_note_largeur', '30');
INSERT INTO setting  values ('active_cahiers_texte', 'y');
INSERT INTO setting  values ('active_carnets_notes', 'y');
INSERT INTO setting  values ('active_observatoire', 'n');
INSERT INTO setting  values ('logo_etab', 'php4.gif');
INSERT INTO setting  values ('longmin_pwd', '5');
INSERT INTO setting  values ('duree_conservation_logs', '365');
INSERT INTO setting  values ('GepiRubConseilProf', 'yes');
INSERT INTO setting  values ('GepiRubConseilScol', 'yes');
INSERT INTO setting  values ('bull_ecart_entete', '0');
INSERT INTO setting  values ('gepi_prof_suivi', 'professeur principal');
INSERT INTO setting  values ('GepiProfImprBul', 'no');
INSERT INTO setting  values ('GepiProfImprBulSettings', 'no');
INSERT INTO setting  values ('GepiScolImprBulSettings', 'yes');
INSERT INTO setting  values ('GepiAdminImprBulSettings', 'no');
INSERT INTO setting  values ('GepiAccesReleveScol', 'yes');
INSERT INTO setting  values ('GepiAccesReleveCpe', 'no');
INSERT INTO setting  values ('GepiAccesReleveProf', 'no');
INSERT INTO setting  values ('GepiAccesReleveProfTousEleves', 'no');
INSERT INTO setting  values ('GepiAccesReleveProfToutesClasses', 'no');
INSERT INTO setting  values ('GepiAccesReleveProfP', 'yes');
INSERT INTO setting  values ('page_garde_imprime', 'no');
INSERT INTO setting  values ('page_garde_texte', 'Madame, Monsieur<br/><br/>Veuillez trouvez ci-joint le bulletin scolaire de votre enfant. Nous vous rappelons que la journ&eacute;e <span style=\"font-weight: bold;\">Portes ouvertes</span> du Lyc&eacute;e aura lieu samedi 20 mai entre 10 h et 17 h.<br/><br/>Veuillez agr&eacute;er, Madame, Monsieur, l\'expression de mes meilleurs sentiments.<br/><br/><div style=\"text-align: right;\">Le proviseur</div>');
INSERT INTO setting  values ('page_garde_padding_top', '4');
INSERT INTO setting  values ('page_garde_padding_left', '11');
INSERT INTO setting  values ('page_garde_padding_text', '6');
INSERT INTO setting  values ('addressblock_padding_top', '400');
INSERT INTO setting  values ('addressblock_padding_right', '200');
INSERT INTO setting  values ('addressblock_padding_text', '200');
INSERT INTO setting  values ('addressblock_length', '600');
INSERT INTO setting  values ('cnv_addressblock_dim_144', 'y');
INSERT INTO setting  values ('p_bulletin_margin', '5');
INSERT INTO setting  values ('bull_espace_avis', '5');
INSERT INTO setting  values ('change_ordre_aff_matieres', 'ok');
INSERT INTO setting  values ('disable_login', 'no');
INSERT INTO setting  values ('bull_formule_bas', 'Bulletin à conserver précieusement. Aucun duplicata ne sera délivré. - GEPI : solution libre de gestion et de suivi des résultats scolaires.');
INSERT INTO setting  values ('delai_devoirs', '7');
INSERT INTO setting  values ('active_module_absence', '2');
INSERT INTO setting  values ('active_module_absence_professeur', 'y');
INSERT INTO setting  values ('gepiSchoolTel', '00 00 10 00 00');
INSERT INTO setting  values ('gepiSchoolFax', '00 00 01 00 00');
INSERT INTO setting  values ('gepiSchoolEmail', 'ce.XXXXXXXX@ac-xxxxx.fr');
INSERT INTO setting  values ('col_boite_largeur', '120');
INSERT INTO setting  values ('bull_mention_doublant', 'no');
INSERT INTO setting  values ('bull_affiche_numero', 'no');
INSERT INTO setting  values ('nombre_tentatives_connexion', '10');
INSERT INTO setting  values ('temps_compte_verrouille', '10');
INSERT INTO setting  values ('bull_affiche_appreciations', 'y');
INSERT INTO setting  values ('bull_affiche_absences', 'y');
INSERT INTO setting  values ('bull_affiche_avis', 'y');
INSERT INTO setting  values ('bull_affiche_aid', 'y');
INSERT INTO setting  values ('bull_affiche_formule', 'y');
INSERT INTO setting  values ('bull_affiche_signature', 'y');
INSERT INTO setting  values ('l_max_aff_trombinoscopes', '120');
INSERT INTO setting  values ('h_max_aff_trombinoscopes', '160');
INSERT INTO setting  values ('l_max_imp_trombinoscopes', '70');
INSERT INTO setting  values ('h_max_imp_trombinoscopes', '100');
INSERT INTO setting  values ('active_module_msj', 'n');
INSERT INTO setting  values ('site_msj_gepi', 'http://gepi.sylogix.net/releases/');
INSERT INTO setting  values ('rc_module_msj', 'n');
INSERT INTO setting  values ('beta_module_msj', 'n');
INSERT INTO setting  values ('dossier_ftp_gepi', 'gepi');
INSERT INTO setting  values ('bull_affiche_tel', 'n');
INSERT INTO setting  values ('bull_affiche_fax', 'n');
INSERT INTO setting  values ('note_autre_que_sur_20', 'F');
INSERT INTO setting  values ('gepi_denom_boite', 'boite');
INSERT INTO setting  values ('gepi_denom_boite_genre', 'f');
INSERT INTO setting  values ('addressblock_font_size', '12');
INSERT INTO setting  values ('addressblock_logo_etab_prop', '50');
INSERT INTO setting  values ('addressblock_classe_annee', '35');
INSERT INTO setting  values ('bull_ecart_bloc_nom', '1');
INSERT INTO setting  values ('addressblock_debug', 'n');
INSERT INTO setting  values ('GepiAccesReleveEleve', 'yes');
INSERT INTO setting  values ('GepiAccesCahierTexteEleve', 'yes');
INSERT INTO setting  values ('GepiAccesReleveParent', 'yes');
INSERT INTO setting  values ('GepiAccesCahierTexteParent', 'yes');
INSERT INTO setting  values ('enable_password_recovery', 'no');
INSERT INTO setting  values ('GepiPasswordReinitProf', 'no');
INSERT INTO setting  values ('GepiPasswordReinitScolarite', 'no');
INSERT INTO setting  values ('GepiPasswordReinitCpe', 'no');
INSERT INTO setting  values ('GepiPasswordReinitAdmin', 'no');
INSERT INTO setting  values ('GepiPasswordReinitEleve', 'yes');
INSERT INTO setting  values ('GepiPasswordReinitParent', 'yes');
INSERT INTO setting  values ('cahier_texte_acces_public', 'no');
INSERT INTO setting  values ('GepiAccesEquipePedaEleve', 'yes');
INSERT INTO setting  values ('GepiAccesEquipePedaEmailEleve', 'no');
INSERT INTO setting  values ('GepiAccesEquipePedaParent', 'yes');
INSERT INTO setting  values ('GepiAccesEquipePedaEmailParent', 'no');
INSERT INTO setting  values ('GepiAccesBulletinSimpleParent', 'yes');
INSERT INTO setting  values ('GepiAccesBulletinSimpleEleve', 'yes');
INSERT INTO setting  values ('GepiAccesGraphEleve', 'yes');
INSERT INTO setting  values ('GepiAccesGraphParent', 'yes');
INSERT INTO setting  values ('choix_bulletin', '2');
INSERT INTO setting  values ('min_max_moyclas', '0');
INSERT INTO setting  values ('bull_categ_font_size_avis', '10');
INSERT INTO setting  values ('bull_police_avis', 'Times New Roman');
INSERT INTO setting  values ('bull_font_style_avis', 'Normal');
INSERT INTO setting  values ('bull_affiche_eleve_une_ligne', 'yes');
INSERT INTO setting  values ('bull_mention_nom_court', 'yes');
INSERT INTO setting  values ('option_modele_bulletin', '2');
INSERT INTO setting  values ('security_alert_email_admin', 'yes');
INSERT INTO setting  values ('security_alert_email_min_level', '2');
INSERT INTO setting  values ('security_alert1_normal_cumulated_level', '3');
INSERT INTO setting  values ('security_alert1_normal_email_admin', 'yes');
INSERT INTO setting  values ('security_alert1_normal_block_user', 'no');
INSERT INTO setting  values ('security_alert1_probation_cumulated_level', '1');
INSERT INTO setting  values ('security_alert1_probation_email_admin', 'yes');
INSERT INTO setting  values ('security_alert1_probation_block_user', 'no');
INSERT INTO setting  values ('security_alert2_normal_cumulated_level', '6');
INSERT INTO setting  values ('security_alert2_normal_email_admin', 'yes');
INSERT INTO setting  values ('security_alert2_normal_block_user', 'yes');
INSERT INTO setting  values ('security_alert2_probation_cumulated_level', '3');
INSERT INTO setting  values ('security_alert2_probation_email_admin', 'yes');
INSERT INTO setting  values ('security_alert2_probation_block_user', 'yes');
INSERT INTO setting  values ('deverouillage_auto_periode_suivante', 'n');
INSERT INTO setting  values ('bull_intitule_app', 'Appréciations / Conseils');
INSERT INTO setting  values ('GepiAccesMoyennesProf', 'yes');
INSERT INTO setting  values ('GepiAccesMoyennesProfTousEleves', 'yes');
INSERT INTO setting  values ('GepiAccesMoyennesProfToutesClasses', 'yes');
INSERT INTO setting  values ('GepiAccesBulletinSimpleProf', 'yes');
INSERT INTO setting  values ('GepiAccesBulletinSimpleProfTousEleves', 'no');
INSERT INTO setting  values ('GepiAccesBulletinSimpleProfToutesClasses', 'no');
INSERT INTO setting  values ('gepi_stylesheet', 'style');
INSERT INTO setting  values ('edt_calendrier_ouvert', 'y');
INSERT INTO setting  values ('scolarite_modif_cours', 'y');
INSERT INTO setting  values ('active_annees_anterieures', 'n');
INSERT INTO setting  values ('active_notanet', 'n');
INSERT INTO setting  values ('longmax_login', '8');
INSERT INTO setting  values ('autorise_edt_tous', 'y');
INSERT INTO setting  values ('autorise_edt_admin', 'y');
INSERT INTO setting  values ('autorise_edt_eleve', 'no');
INSERT INTO setting  values ('utiliserMenuBarre', 'yes');
INSERT INTO setting  values ('active_absences_parents', 'no');
INSERT INTO setting  values ('creneau_different', 'n');
INSERT INTO setting  values ('active_inscription', 'n');
INSERT INTO setting  values ('active_inscription_utilisateurs', 'n');
INSERT INTO setting  values ('mod_inscription_explication', '<p> <strong>Pr&eacute;sentation des dispositifs du Lyc&eacute;e dans les coll&egrave;ges qui organisent des rencontres avec les parents.</strong> <br />\r\n<br />\r\nChacun d&rsquo;entre vous conna&icirc;t la situation dans laquelle sont plac&eacute;s les &eacute;tablissements : </p>\r\n<ul>\r\n    <li>baisse d&eacute;mographique</li>\r\n    <li>r&eacute;gulation des moyens</li>\r\n    <li>- ... </li>\r\n</ul>\r\nCette ann&eacute;e encore nous devons &ecirc;tre pr&eacute;sents dans les r&eacute;unions organis&eacute;es au sein des coll&egrave;ges afin de pr&eacute;senter nos sp&eacute;cificit&eacute;s, notre valeur ajout&eacute;e, les &eacute;volution du projet, le label international, ... <br />\r\nsur cette feuille, vous avez la possibilit&eacute; de vous inscrire afin d\'intervenir dans un ou plusieurs coll&egrave;ges selon vos convenances.');
INSERT INTO setting  values ('mod_inscription_titre', 'Intervention dans les collèges');
INSERT INTO setting  values ('active_ateliers', 'n');
INSERT INTO setting  values ('GepiAccesRestrAccesAppProfP', 'no');
INSERT INTO setting  values ('l_resize_trombinoscopes', '120');
INSERT INTO setting  values ('h_resize_trombinoscopes', '160');
INSERT INTO setting  values ('multisite', 'n');
INSERT INTO setting  values ('statuts_prives', 'y');
INSERT INTO setting  values ('mod_edt_gr', 'n');
INSERT INTO setting  values ('use_ent', 'n');
INSERT INTO setting  values ('rss_cdt_eleve', 'n');
INSERT INTO setting  values ('auth_locale', 'yes');
INSERT INTO setting  values ('auth_ldap', 'no');
INSERT INTO setting  values ('auth_sso', 'none');
INSERT INTO setting  values ('ldap_write_access', 'no');
INSERT INTO setting  values ('may_import_user_profile', 'no');
INSERT INTO setting  values ('statut_utilisateur_defaut', 'professeur');
INSERT INTO setting  values ('texte_visa_cdt', 'Cahier de textes visé ce jour <br />Le Principal <br /> M. XXXXX<br />');
INSERT INTO setting  values ('visa_cdt_inter_modif_notices_visees', 'yes');
INSERT INTO setting  values ('denomination_eleve', 'élève');
INSERT INTO setting  values ('denomination_eleves', 'élèves');
INSERT INTO setting  values ('denomination_professeur', 'professeur');
INSERT INTO setting  values ('denomination_professeurs', 'professeurs');
INSERT INTO setting  values ('denomination_responsable', 'responsable légal');
INSERT INTO setting  values ('denomination_responsables', 'responsables légaux');
INSERT INTO setting  values ('delais_apres_cloture', '0');
INSERT INTO setting  values ('active_mod_ooo', 'n');
INSERT INTO setting  values ('use_only_cdt', 'n');
INSERT INTO setting  values ('edt_remplir_prof', 'n');
INSERT INTO setting  values ('active_mod_genese_classes', 'y');
INSERT INTO setting  values ('active_mod_ects', 'y');
INSERT INTO setting  values ('GepiAccesSaisieEctsPP', 'no');
INSERT INTO setting  values ('GepiAccesSaisieEctsScolarite', 'yes');
INSERT INTO setting  values ('GepiAccesEditionDocsEctsPP', 'no');
INSERT INTO setting  values ('GepiAccesEditionDocsEctsScolarite', 'yes');
INSERT INTO setting  values ('gepiSchoolStatut', 'public');
INSERT INTO setting  values ('gepiSchoolAcademie', 'Academie de test');
INSERT INTO setting  values ('note_autre_que_sur_referentiel', 'F');
INSERT INTO setting  values ('referentiel_note', '20');
INSERT INTO setting  values ('active_mod_apb', 'n');
INSERT INTO setting  values ('utiliser_mb', 'n');
INSERT INTO setting  values ('backup_directory', 'WkNgq4rg4hF2nmJqf3R6RE38rb9iF79A73GTZuqQ');
INSERT INTO setting  values ('backupdir_lastchange', '1279784745');
INSERT INTO setting  values ('GepiAccesCpePPEmailEleve', 'no');
INSERT INTO setting  values ('GepiAccesCpePPEmailParent', 'no');
INSERT INTO setting  values ('ImpressionParent', '');
INSERT INTO setting  values ('ImpressionEleve', '');
INSERT INTO setting  values ('ImpressionNombre', '1');
INSERT INTO setting  values ('ImpressionNombreParent', '1');
INSERT INTO setting  values ('ImpressionNombreEleve', '1');
INSERT INTO setting  values ('param_module_trombinoscopes', 'no_gep');
INSERT INTO setting  values ('active_module_trombinoscopes', 'y');
INSERT INTO setting  values ('conversion_j_eleves_etablissements', 'effectuee');
INSERT INTO setting  values ('sso_display_portail', 'no');
INSERT INTO setting  values ('sso_url_portail', 'https://www.example.com');
INSERT INTO setting  values ('sso_hide_logout', 'no');
INSERT INTO setting  values ('unzipped_max_filesize', '10');
INSERT INTO setting  values ('GepiCahierTexteVersion', '2');
INSERT INTO setting  values ('message_login', '1');
INSERT INTO setting  values ('pb_maj', '');
INSERT INTO setting  values ('display_users', 'tous');
INSERT INTO setting  values ('gepiSchoolRne', 'RNETEST');
INSERT INTO setting  values ('gepiSchoolPays', 'Ici');
INSERT INTO setting  values ('gepiAdminNom', '');
INSERT INTO setting  values ('gepiAdminPrenom', '');
INSERT INTO setting  values ('gepiAdminFonction', '');
INSERT INTO setting  values ('gepiAdminAdressPageLogin', 'y');
INSERT INTO setting  values ('contact_admin_mailto', 'n');
INSERT INTO setting  values ('gepiAdminAdressFormHidden', 'n');
INSERT INTO setting  values ('mode_generation_pwd_majmin', 'y');
INSERT INTO setting  values ('mode_generation_pwd_excl', 'n');
INSERT INTO setting  values ('type_bulletin_par_defaut', 'html');
INSERT INTO setting  values ('exp_imp_chgt_etab', 'no');
INSERT INTO setting  values ('ele_lieu_naissance', 'no');
INSERT INTO setting  values ('avis_conseil_classe_a_la_mano', 'n');
INSERT INTO setting  values ('num_enregistrement_cnil', '');
INSERT INTO setting  values ('mode_generation_login', 'name8');
INSERT INTO setting  values ('acces_app_ele_resp', 'manuel');
INSERT INTO setting  values ('mode_sauvegarde', 'gepi');
INSERT INTO setting  values ('active_module_absence2', 'y');
INSERT INTO setting  values ('absence_classement_top', '10');
INSERT INTO setting  values ('cahiers_texte_passwd_pub', '');
INSERT INTO setting  values ('cahiers_texte_login_pub', '');
INSERT INTO setting  values ('sso_scribe', 'no');
INSERT INTO setting  values ('abs2_saisie_prof_decale', 'y');
INSERT INTO setting  values ('abs2_saisie_prof_decale_journee', 'y');
INSERT INTO setting  values ('abs2_saisie_prof_hors_cours', 'y');
INSERT INTO setting  values ('abs2_modification_saisie_une_heure', 'y');
INSERT INTO setting  values ('abs2_modification_saisie_sans_limite', 'y');
INSERT INTO setting  values ('active_mod_discipline', 'y');
INSERT INTO setting  values ('GepiAccesSaisieEctsProf', 'no');
INSERT INTO setting  values ('GepiAccesRecapitulatifEctsProf', 'yes');
INSERT INTO setting  values ('GepiAccesRecapitulatifEctsScolarite', 'yes');
INSERT INTO setting  values ('autoriser_correction_bulletin', 'no');
INSERT INTO setting  values ('gepiAbsenceEmail', 'uuie@uie.com');
INSERT INTO setting  values ('abs2_envoi_sms', 'n');
INSERT INTO setting  values ('abs2_envoi_prestataire', '');
INSERT INTO setting  values ('abs2_sms_prestataire', '123-sms');
INSERT INTO setting  values ('abs2_sms_username', 'josselin.jacquard@gmail.com');
INSERT INTO setting  values ('abs2_sms_password', 'RVV5V3');
INSERT INTO setting  values ('abs2_sms', 'n');
INSERT INTO setting  values ('date_phase1', 'y');
INSERT INTO setting  values ('liste_absents', 'y');
INSERT INTO setting  values ('voir_fiche_eleve', 'y');
INSERT INTO setting  values ('renseigner_retard', 'y');
INSERT INTO setting  values ('module_edt', 'y');
INSERT INTO setting  values ('memorisation', 'y');
INSERT INTO setting  values ('appreciations_types_profs', 'no');
INSERT INTO setting  values ('GepiAccesVisuToutesEquipProf', 'no');
INSERT INTO setting  values ('AAProfTout', 'no');
INSERT INTO setting  values ('AAProfClasses', 'no');
INSERT INTO setting  values ('AAProfGroupes', 'no');
INSERT INTO setting  values ('GepiAccesGestElevesProf', 'yes');
INSERT INTO setting  values ('GepiAccesModifMaPhotoProfesseur', 'no');
INSERT INTO setting  values ('visuDiscProfClasses', 'no');
INSERT INTO setting  values ('visuDiscProfGroupes', 'no');
INSERT INTO setting  values ('CommentairesTypesPP', 'no');
INSERT INTO setting  values ('GepiAccesBulletinSimplePP', 'no');
INSERT INTO setting  values ('GepiAccesGestElevesProfP', 'no');
INSERT INTO setting  values ('GepiAccesGestPhotoElevesProfP', 'no');
INSERT INTO setting  values ('AAProfPrinc', 'no');
INSERT INTO setting  values ('CommentairesTypesScol', 'no');
INSERT INTO setting  values ('GepiAccesCdtScol', 'no');
INSERT INTO setting  values ('GepiAccesCdtScolRestreint', 'no');
INSERT INTO setting  values ('GepiAccesVisuToutesEquipScol', 'no');
INSERT INTO setting  values ('AAScolTout', 'no');
INSERT INTO setting  values ('AAScolResp', 'no');
INSERT INTO setting  values ('GepiAccesModifMaPhotoScolarite', 'no');
INSERT INTO setting  values ('GepiAccesCdtCpe', 'no');
INSERT INTO setting  values ('GepiAccesCdtCpeRestreint', 'no');
INSERT INTO setting  values ('GepiAccesVisuToutesEquipCpe', 'no');
INSERT INTO setting  values ('AACpeTout', 'no');
INSERT INTO setting  values ('AACpeResp', 'no');
INSERT INTO setting  values ('GepiAccesModifMaPhotoCpe', 'no');
INSERT INTO setting  values ('GepiAccesTouteFicheEleveCpe', 'yes');
INSERT INTO setting  values ('GepiAccesModifMaPhotoAdministrateur', 'no');
INSERT INTO setting  values ('GepiAccesOptionsReleveEleve', 'no');
INSERT INTO setting  values ('AAEleve', 'no');
INSERT INTO setting  values ('GepiAccesModifMaPhotoEleve', 'no');
INSERT INTO setting  values ('GepiAccesEleTrombiTousEleves', 'no');
INSERT INTO setting  values ('GepiAccesEleTrombiElevesClasse', 'no');
INSERT INTO setting  values ('GepiAccesEleTrombiPersonnels', 'no');
INSERT INTO setting  values ('GepiAccesEleTrombiProfsClasse', 'no');
INSERT INTO setting  values ('GepiAccesOptionsReleveParent', 'no');
INSERT INTO setting  values ('AAResponsable', 'no');
INSERT INTO setting  values ('GepiAccesTouteFicheEleveScolarite', 'yes');
INSERT INTO setting  values ('GepiAccesAbsTouteClasseCpe', 'yes');


#
# table suivi_eleve_cpe
#


#
# table synthese_app_classe
#


#
# table utilisateurs
#
INSERT INTO utilisateurs  values ('scola', 'nomscola', 'prenomscola', 'M.', 'cb120a6a08fac73fed2189afec368630','', '', 'no', 'scolarite', 'actif', 'n', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', '1', '0', 'SCOLA_u31j38j3892T42Nu9oxULHiRNXf69zEl7Hxz9k', '', 'gepi');
INSERT INTO utilisateurs  values ('cpe', 'nomcpe', 'prenomcpe', 'M.', 'cf5b5210da6051314a5311329a59e5d8','', '', 'no', 'cpe', 'actif', 'n', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', '7', '0', 'CPE_070DE714g9FY14AbPeI4JwUJzGJHpud546yKS7', '', 'gepi');
INSERT INTO utilisateurs  values ('prof1', 'nom profTest1', 'prenom profTest2', 'M.', 'ab992c2f7f6fedef6e9fb277674efac8','', '', 'no', 'professeur', 'actif', 'n', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', '8', '0', 'PROF1_rPq85PCcDAwvSDY0Ed7Ph13Mu96SK935udH13', '', 'gepi');
INSERT INTO utilisateurs  values ('profAngl', 'nomprofanglais', 'prenomprofanglais', 'M.', 'ab992c2f7f6fedef6e9fb277674efac8','', '', 'no', 'professeur', 'actif', 'n', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', '0', '0', 'PROFANGL_Nx6Umg6l5gz0y7GcEYxDt3rpeyK717a2Hif91', '', 'gepi');
INSERT INTO utilisateurs  values ('Pere', 'Pere1', 'PrenomPere', '', 'c8c07410beacb3cdbee5af1aa9341948','', 'joss', 'no', 'responsable', 'actif', 'n', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', '0', '0', '', '', 'gepi');
INSERT INTO utilisateurs  values ('azert', 'azert', 'azert', 'M.', 'a88d4ae7dc2a22f8473938d5e6230ec6', '','', 'no', 'professeur', 'actif', 'y', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', '2', '0', '', '', 'gepi');
INSERT INTO utilisateurs  values ('autre', 'aui', 'aui', 'M.', 'e710f1f4b31779a87562d6d8d5871c43','', 'aui@aui.com', 'no', 'autre', 'actif', 'y', '2006-01-01 00:00:00', '', '0000-00-00 00:00:00', '0', '0', '', '', 'gepi');


#
# table vs_alerts_eleves
#


#
# table vs_alerts_groupes
#


#
# table vs_alerts_types
#
#
# ******* Fin du fichier - La sauvegarde s'est terminée normalement ********
