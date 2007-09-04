INSERT INTO `droits` ( `id` , `administrateur` , `professeur` , `cpe` , `scolarite` , `eleve` , `responsable` , `secours` , `description` , `statut` )
VALUES (
'/edt_organisation/index_edt.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'Gestion de l''emploi du temps', ''
);
 INSERT INTO `droits` ( `id` , `administrateur` , `professeur` , `cpe` , `scolarite` , `eleve` , `responsable` , `secours` , `description` , `statut` )
VALUES (
'/edt_organisation/edt_initialiser.php?initialiser=ok', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''emploi du temps', ''
);

CREATE TABLE `salle_cours` (
`id_salle` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`numero_salle` VARCHAR( 10 ) NOT NULL ,
`nom_salle` VARCHAR( 50 ) NOT NULL
) ENGINE = MYISAM CHARACTER SET latin1 COLLATE latin1_general_ci;

CREATE TABLE `edt_cours` (
  `id_cours` int(3) NOT NULL auto_increment,
  `id_groupe` varchar(10) collate latin1_general_ci NOT NULL,
  `id_salle` varchar(3) collate latin1_general_ci NOT NULL,
  `jour_semaine` varchar(10) collate latin1_general_ci NOT NULL,
  `id_definie_periode` varchar(3) collate latin1_general_ci NOT NULL,
  `duree` varchar(10) collate latin1_general_ci NOT NULL default '2',
  `heuredeb_dec` varchar(3) collate latin1_general_ci NOT NULL default '0',
  `id_semaine` varchar(3) collate latin1_general_ci NOT NULL default '0',
  `id_calendrier` varchar(3) collate latin1_general_ci NOT NULL default '0',
  `modif_edt` varchar(3) collate latin1_general_ci NOT NULL default '0',
  PRIMARY KEY  (`id_cours`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=51 ;


CREATE TABLE `edt_setting` (
`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`reglage` VARCHAR( 30 ) NOT NULL ,
`valeur` VARCHAR( 30 ) NOT NULL
) ENGINE = MYISAM CHARACTER SET latin1 COLLATE latin1_general_ci;

--
-- Contenu de la table `edt_setting`
--

INSERT INTO `edt_setting` (`id`, `reglage`, `valeur`) VALUES
(1, 'nom_creneaux_s', '1'),
(7, 'edt_aff_salle', 'nom'),
(3, 'edt_aff_matiere', 'long'),
(4, 'edt_aff_creneaux', 'noms'),
(8, 'edt_aff_init_infos', 'oui'),
(6, 'edt_aff_couleur', 'nb'),
(9, 'edt_aff_init_infos2', 'oui');

CREATE TABLE `edt_calendrier` (
`id_calendrier` int(11) NOT NULL auto_increment,
`classe_concerne_calendrier` text NOT NULL,
`nom_calendrier` varchar(100) NOT NULL default '',
`jourdebut_calendrier` date NOT NULL default '0000-00-00',
`heuredebut_calendrier` time NOT NULL default '00:00:00',
`jourfin_calendrier` date NOT NULL default '0000-00-00',
`heurefin_calendrier` time NOT NULL default '00:00:00',
`numero_periode` tinyint(4) NOT NULL default '0',
`etabferme_calendrier` tinyint(4) NOT NULL,
`etabvacances_calendrier` tinyint(4) NOT NULL,
PRIMARY KEY (`id_calendrier`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;