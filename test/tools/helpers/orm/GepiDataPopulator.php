<?php

class GepiDataPopulator
{

    public static function populate($con = null)
    {
        if($con === null) {
            $con = Propel::getConnection();
        }

        $con->beginTransaction();

        // Add utilisateur records
        // ---------------------

        $lebesgue_prof = new UtilisateurProfessionnel();
        $lebesgue_prof->setLogin('Lebesgue');
        $lebesgue_prof->setStatut('professeur');
        $lebesgue_prof->setPreferenceValeur('glace_parfum','chocolat');
        $lebesgue_prof->save($con);

        $newton_prof = new UtilisateurProfessionnel();
        $newton_prof->setLogin('Newton');
        $newton_prof->setStatut('professeur');
        $newton_prof->save($con);

        $curie_prof = new UtilisateurProfessionnel();
        $curie_prof->setLogin('Curie');
        $curie_prof->setStatut('professeur');
        $curie_prof->save($con);

        $dolto_cpe = new UtilisateurProfessionnel();
        $dolto_cpe->setLogin('Dolto');
        $dolto_cpe->setStatut('cpe');
        $dolto_cpe->save($con);

        $aubert_scola = new UtilisateurProfessionnel();
        $aubert_scola->setLogin('Aubert');
        $aubert_scola->setStatut('scolarite');
        $aubert_scola->save($con);

        $florence_eleve = new Eleve();
        $florence_eleve->setLogin('Florence Michu');
        $florence_eleve->setEleId('00112233');
        $florence_eleve->save();
        $adresse = new Adresse();
        $adresse->setAdr1('13 rue du paradis');
        $adresse->setCommune('Montendre');
        $adresse->setCp('01001');
        $adresse->save();
        $adresse->setId('add id 1');
        $responsable = new ResponsableEleve();
        $responsable->setCivilite('M.');
        $responsable->setNom('Michu');
        $responsable->setMel('mail@test');
        $responsable->setPrenom('Mere');
        $responsable->setResponsableEleveId('id 1');
        $responsable->setAdresse($adresse);
        $responsable->save();
        $responsable_info = new ResponsableInformation();
        $responsable_info->setEleve($florence_eleve);
        $responsable_info->setNiveauResponsabilite(1);
        $responsable_info->setResponsableEleve($responsable);
        $responsable_info->save();
        $responsable2 = new ResponsableEleve();
        $responsable2->setCivilite('Mme.');
        $responsable2->setNom('Michudame');
        $responsable2->setMel('mail@test');
        $responsable2->setPrenom('Mere');
        $responsable2->setResponsableEleveId('id 2');
        $responsable2->setAdresse($adresse);
        $responsable2->save();
        $responsable_info2 = new ResponsableInformation();
        $responsable_info2->setEleve($florence_eleve);
        $responsable_info2->setNiveauResponsabilite(2);
        $responsable_info2->setResponsableEleve($responsable2);
        $responsable_info2->save();
        $dolto_cpe->addEleve($florence_eleve);
        $dolto_cpe->save();
        $newton_prof->addEleve($florence_eleve);
        $newton_prof->save();

        $nicolas_eleve = new Eleve();
        $nicolas_eleve->setLogin('Nicolas Dupont');
        $nicolas_eleve->setEleId('00112234');
        $nicolas_eleve->save();

        $michel_eleve = new Eleve();
        $michel_eleve->setLogin('Michel Martin');
        $michel_eleve->setEleId('00112235');
        $michel_eleve->setDateSortie('2010-12-20');
        $michel_eleve->save();

        $classe_6A = new Classe();
        $classe_6A->setNom('6ieme A');
        $classe_6A->save();
        $periode_6A_1 = new PeriodeNote();
        $periode_6A_1->setClasse($classe_6A);
        $periode_6A_1->setNumPeriode(1);
        $periode_6A_1->setVerouiller('O');
        $periode_6A_1->setNomPeriode('premier trimestre');
        $periode_6A_1->setDateFin('2010-12-01 00:00:00');
        $periode_6A_1->save();
        $periode_6A_2 = new PeriodeNote();
        $periode_6A_2->setClasse($classe_6A);
        $periode_6A_2->setNumPeriode(2);
        $periode_6A_2->setVerouiller('N');
        $periode_6A_2->setNomPeriode('deuxième trimestre');
        $periode_6A_2->setDateFin('2011-03-01 23:59:59');
        $periode_6A_2->save();

        $classe_6A->addEleve($florence_eleve,1);//florence est dans la 6A pour les deux premiers trimestres et dans la 6B pour les deux suivants
        $classe_6A->addEleve($florence_eleve,2);
        $aubert_scola->addClasse($classe_6A);
        $aubert_scola->save();

        $classe_6B = new Classe();
        $classe_6B->setNom('6ieme B');
        $classe_6B->save();
        $periode_6B_2 = new PeriodeNote();
        $periode_6B_2->setClasse($classe_6B);
        $periode_6B_2->setNumPeriode(2);
        $periode_6B_2->setVerouiller('O');
        $periode_6B_2->setNomPeriode('deuxième trimestre');
        $periode_6B_2->setDateFin('2011-03-01 23:59:59');
        $periode_6B_2->save();
        $periode_6B_3 = new PeriodeNote();
        $periode_6B_3->setClasse($classe_6B);
        $periode_6B_3->setNumPeriode(3);
        $periode_6B_3->setVerouiller('O');
        $periode_6B_3->setNomPeriode('troisième trimestre');
        $periode_6B_3->setDateFin('2011-07-01 23:59:59');
        $periode_6B_3->save();
        $classe_6B->addEleve($nicolas_eleve,1);
        $classe_6B->addEleve($nicolas_eleve,2);
        $classe_6B->addEleve($florence_eleve,3);

        $groupe_math = new Groupe();
        $groupe_math->setName('MATH6A');
        $groupe_math->addEleve($florence_eleve, 1);
        $groupe_math->addEleve($florence_eleve, 2);
        $groupe_math->addEleve($florence_eleve, 3);
        $groupe_math->addUtilisateurProfessionnel($lebesgue_prof);
        $groupe_math->addClasse($classe_6A);
        $groupe_math->addClasse($classe_6B);
        $groupe_math->save();

        $aid_1 = new AidDetails();
        $aid_1->setNom('aid 1');
        $aid_1->setId('1ai');
        $aid_1->addEleve($florence_eleve);
        $aid_1->addUtilisateurProfessionnel($newton_prof);
        $aid_1->save();

        //on va peupler les absences 2
        include_once(dirname(__FILE__).'/../../../../mod_abs2/admin/function.php');
        ajoutMotifsParDefaut();
        ajoutLieuxParDefaut();
        ajoutJustificationsParDefaut();
        ajoutTypesParDefaut();

        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2010-10-01 08:00:00');//le 2010-10-01 est un vendredi
        $saisie_1->setFinAbs('2010-10-01 09:00:00');
        $saisie_1->save();

        $saisie_2 = new AbsenceEleveSaisie();
        $saisie_2->setEleve($florence_eleve);
        $saisie_2->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_2->setDebutAbs('2010-10-02 08:00:00');//samedi : ne comptera pas comme demi journée d'absence
        $saisie_2->setFinAbs('2010-10-02 09:00:00');
        $saisie_2->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_2);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Absence scolaire')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->setAbsenceEleveJustification(AbsenceEleveJustificationQuery::create()->filterByNom('Courrier familial')->findOne());
        $traitement->save();
        $notification = new AbsenceEleveNotification();
        $notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_EN_COURS);
        $notification->setTypeNotification(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER);
        $notification->setAbsenceEleveTraitement($traitement);
        $notification->save();

        $saisie_3 = new AbsenceEleveSaisie();
        $saisie_3->setEleve($florence_eleve);
        $saisie_3->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_3->setDebutAbs('2010-10-03 08:00:00');//dimanche : ne comptera pas comme demi journée d'absence
        $saisie_3->setFinAbs('2010-10-03 08:29:00');
        $saisie_3->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_3);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Exclusion de cours')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_4 = new AbsenceEleveSaisie();
        $saisie_4->setEleve($florence_eleve);
        $saisie_4->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_4->setDebutAbs('2010-10-04 08:00:00');
        $saisie_4->setFinAbs('2010-10-04 08:29:00');//retard : ne comptera pas comme demi journée d'absence
        $saisie_4->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_4);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Retard intercours')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_4);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Absence scolaire')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->setAbsenceEleveJustification(AbsenceEleveJustificationQuery::create()->filterByNom('Courrier familial')->findOne());
        $traitement->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_4);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Erreur de saisie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();
        $notification = new AbsenceEleveNotification();
        $notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_SUCCES);
        $notification->setTypeNotification(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER);
        $notification->setAbsenceEleveTraitement($traitement);
        $notification->save();

        $saisie_5 = new AbsenceEleveSaisie();
        $saisie_5->setEleve($florence_eleve);
        $saisie_5->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_5->setDebutAbs('2010-10-05 08:00:00');
        $saisie_5->setFinAbs('2010-10-05 08:29:00');//retard : ne comptera pas comme demi journée d'absence
        $saisie_5->save();
        $saisie_51 = new AbsenceEleveSaisie();
        $saisie_51->setEleve($florence_eleve);
        $saisie_51->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_51->setDebutAbs('2010-10-05 09:00:00');
        $saisie_51->setFinAbs('2010-10-05 09:29:00');//retard : ne comptera pas comme demi journée d'absence
        $saisie_51->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_5);
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->setAbsenceEleveJustification(AbsenceEleveJustificationQuery::create()->filterByNom('Courrier familial')->findOne());
        $traitement->save();


        $saisie_6 = new AbsenceEleveSaisie();
        $saisie_6->setEleve($florence_eleve);
        $saisie_6->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_6->setDebutAbs('2010-10-06 08:00:00');
        $saisie_6->setFinAbs('2010-10-06 09:00:00');
        $saisie_6->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_6);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Retard exterieur')->findOne());//c'est le retard extérieur qui va prendre le dessus : ne comptera pas comme demi journée d'absence
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_6);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Absence scolaire')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_6);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Erreur de saisie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_7 = new AbsenceEleveSaisie();
        $saisie_7->setEleve($florence_eleve);
        $saisie_7->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_7->setDebutAbs('2010-10-07 08:00:00');
        $saisie_7->setFinAbs('2010-10-07 09:00:00');
        $saisie_7->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_7);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Erreur de saisie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_8 = new AbsenceEleveSaisie();
        $saisie_8->setEleve($florence_eleve);
        $saisie_8->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_8->setDebutAbs('2010-10-08 08:00:00');
        $saisie_8->setFinAbs('2010-10-08 09:00:00');
        $saisie_8->save();
        $saisie_81 = new AbsenceEleveSaisie();
        $saisie_81->setEleve($florence_eleve);
        $saisie_81->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_81->setDebutAbs('2010-10-08 08:00:00');
        $saisie_81->setFinAbs('2010-10-08 08:10:00');//ce retard ne sera pas décompté car il est inclus dans une absence plus globale
        $saisie_81->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_81);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Retard exterieur')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_9 = new AbsenceEleveSaisie();
        $saisie_9->setEleve($florence_eleve);
        $saisie_9->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_9->setDebutAbs('2010-10-09 08:00:00');
        $saisie_9->setFinAbs('2010-10-09 09:00:00');//samedi : ne comptera pas comme demi journée d'absence
        $saisie_9->save();
        $saisie_91 = new AbsenceEleveSaisie();
        $saisie_91->setEleve($florence_eleve);
        $saisie_91->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_91->setDebutAbs('2010-10-09 08:00:00');
        $saisie_91->setFinAbs('2010-10-09 08:10:00');
        $saisie_91->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_91);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_10 = new AbsenceEleveSaisie();
        //$saisie_9->setEleve($florence_eleve);//aucun eleve : c'est un marqueur d'appel éffectué
        $saisie_10->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_10->setDebutAbs('2010-10-10 08:00:00');
        $saisie_10->setFinAbs('2010-10-10 09:00:00');
        $saisie_10->setGroupe($groupe_math);
        $saisie_10->save();
        $saisie_101 = new AbsenceEleveSaisie();
        $saisie_101->setEleve($florence_eleve);
        $saisie_101->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_101->setDebutAbs('2010-10-10 08:00:00');
        $saisie_101->setFinAbs('2010-10-10 08:10:00');
        $saisie_101->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_101);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Retard exterieur')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_11 = new AbsenceEleveSaisie();
        //$saisie_9->setEleve($florence_eleve);//aucun eleve : c'est un marqueur d'appel éffectué
        $saisie_11->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_11->setDebutAbs('2010-10-11 08:00:00');
        $saisie_11->setFinAbs('2010-10-11 09:00:00');
        $saisie_11->setClasse($classe_6A);
        $saisie_11->save();
        $saisie_111 = new AbsenceEleveSaisie();
        $saisie_111->setEleve($florence_eleve);
        $saisie_111->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_111->setDebutAbs('2010-10-11 08:00:00');
        $saisie_111->setFinAbs('2010-10-11 08:10:00');
        $saisie_111->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_111);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Retard exterieur')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_12 = new AbsenceEleveSaisie();
        //$saisie_9->setEleve($florence_eleve);//aucun eleve : c'est un marqueur d'appel éffectué
        $saisie_12->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_12->setDebutAbs('2010-10-12 08:00:00');
        $saisie_12->setFinAbs('2010-10-12 09:00:00');
        $saisie_12->setAidDetails($aid_1);
        $saisie_12->save();
        $saisie_121 = new AbsenceEleveSaisie();
        $saisie_121->setEleve($florence_eleve);
        $saisie_121->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_121->setDebutAbs('2010-10-12 08:00:00');
        $saisie_121->setFinAbs('2010-10-12 08:10:00');
        $saisie_121->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_121);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Retard exterieur')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_13 = new AbsenceEleveSaisie();
        //$saisie_13->setEleve($florence_eleve);//aucun eleve : c'est un marqueur d'appel effectué
        $saisie_13->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_13->setDebutAbs('2010-10-13 08:00:00');
        $saisie_13->setFinAbs('2010-10-13 09:00:00');
        $saisie_13->setClasse($classe_6A);
        $saisie_13->save();
        $saisie_131 = new AbsenceEleveSaisie();
        $saisie_131->setEleve($florence_eleve);
        $saisie_131->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_131->setDebutAbs('2010-10-13 08:00:00');
        $saisie_131->setFinAbs('2010-10-13 08:10:00');
        $saisie_131->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_131);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_14 = new AbsenceEleveSaisie();
        $saisie_14->setEleve($florence_eleve);
        $saisie_14->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_14->setDebutAbs('2010-10-14 08:00:00');
        $saisie_14->setFinAbs('2010-10-14 09:00:00');
        $saisie_14->setClasse($classe_6A);
        $saisie_14->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_14);
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->setAbsenceEleveJustification(AbsenceEleveJustificationQuery::create()->filterByNom('Courrier familial')->findOne());
        $traitement->save();

        $saisie_15 = new AbsenceEleveSaisie();
        $saisie_15->setEleve($florence_eleve);
        $saisie_15->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_15->setDebutAbs('2010-10-15 08:00:00');
        $saisie_15->setFinAbs('2010-10-15 09:00:00');
        $saisie_15->setClasse($classe_6A);
        $saisie_15->save();
        $saisie_151 = new AbsenceEleveSaisie();
        $saisie_151->setEleve($florence_eleve);
        $saisie_151->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_151->setDebutAbs('2010-10-15 08:00:00');//ce retard ne sera pas décompté car il est inclus dans une absence plus globale
        $saisie_151->setFinAbs('2010-10-15 08:10:00');
        $saisie_151->setClasse($classe_6A);
        $saisie_151->save();

        $saisie_16 = new AbsenceEleveSaisie();
        //$saisie_9->setEleve($florence_eleve);//aucun eleve : c'est un marqueur d'appel éffectué
        $saisie_16->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_16->setDebutAbs('2010-10-16 08:00:00');
        $saisie_16->setFinAbs('2010-10-16 09:00:00');
        $saisie_16->setAidDetails($aid_1);
        $saisie_16->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_16);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Erreur de saisie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_17 = new AbsenceEleveSaisie();
        $saisie_17->setEleve($florence_eleve);
        $saisie_17->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_17->setDebutAbs('2010-10-17 08:00:00');
        $saisie_17->setFinAbs('2010-10-17 09:00:00');
        $saisie_17->save();
        $saisie_171 = new AbsenceEleveSaisie();
        $saisie_171->setEleve($florence_eleve);
        $saisie_171->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_171->setDebutAbs('2010-10-17 14:00:00');
        $saisie_171->setFinAbs('2010-10-17 15:00:00');
        $saisie_171->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_17);
        $traitement->addAbsenceEleveSaisie($saisie_171);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Absence scolaire')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();
        
        $saisie_18 = new AbsenceEleveSaisie();
        $saisie_18->setEleve($florence_eleve);
        $saisie_18->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_18->setDebutAbs('2010-10-18 08:00:00');
        $saisie_18->setFinAbs('2010-10-18 09:00:00');
        $saisie_18->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_18);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Retard exterieur')->findOne());//c'est le retard extérieur qui va prendre le dessus : ne comptera pas comme demi journée d'absence
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();
        $saisie_181 = new AbsenceEleveSaisie();//la saisie 181 est la même que 18 mais elle va être comptée comme une absence normale et non un retard
        $saisie_181->setEleve($florence_eleve);
        $saisie_181->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_181->setDebutAbs('2010-10-18 08:00:00');
        $saisie_181->setFinAbs('2010-10-18 09:00:00');
        $saisie_181->save();
        
        $saisie_19 = new AbsenceEleveSaisie(); //saisie sur une journée ouvrée un mardi
        $saisie_19->setEleve($florence_eleve);
        $saisie_19->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_19->setDebutAbs('2010-10-19 08:00:00');
        $saisie_19->setFinAbs('2010-10-19 16:30:00');
        $saisie_19->save();

        $saisie_20 = new AbsenceEleveSaisie(); //saisie sur plusieurs journées du jeudi 28-10 au mardi 2-11-2010, 1 seule saisie
        $saisie_20->setEleve($florence_eleve);
        $saisie_20->setUtilisateurProfessionnel($dolto_cpe);
        $saisie_20->setDebutAbs('2010-10-28 08:00:00');
        $saisie_20->setFinAbs('2010-11-2 17:00:00');
        $saisie_20->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_20);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Absence scolaire')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();
		
        $saisie_21 = new AbsenceEleveSaisie(); //saisie sur 1 journée et sortir l'élève de l'établissement
        $saisie_21->setEleve($florence_eleve);
        $saisie_21->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_21->setDebutAbs('2011-05-30 08:00:00');
        $saisie_21->setFinAbs('2011-05-30 16:30:00');
        $saisie_21->save();
        
        $saisie_22 = new AbsenceEleveSaisie();
        $saisie_22->setEleve($florence_eleve);
        $saisie_22->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_22->setDebutAbs('2011-05-31 08:01:00');
        $saisie_22->setFinAbs('2011-05-31 09:00:00');
        $saisie_22->save();
        $saisie_221 = new AbsenceEleveSaisie();
        $saisie_221->setEleve($florence_eleve);
        $saisie_221->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_221->setDebutAbs('2011-05-31 08:00:00');
        $saisie_221->setFinAbs('2011-05-31 09:10:00');
        $saisie_221->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_221);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_23 = new AbsenceEleveSaisie();
        $saisie_23->setEleve($florence_eleve);
        $saisie_23->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_23->setDebutAbs('2011-06-01 08:01:00');
        $saisie_23->setFinAbs('2011-06-01 08:10:00');
        $saisie_23->save();
        $saisie_231 = new AbsenceEleveSaisie();
        $saisie_231->setEleve($florence_eleve);
        $saisie_231->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_231->setDebutAbs('2011-06-01 08:00:00');
        $saisie_231->setFinAbs('2011-06-01 09:10:00');
        $saisie_231->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_231);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        //on ajoute des cours
        $edtCours = new EdtEmplacementCours();
        $edtCours->setGroupe($groupe_math);
        $edtCours->setDuree(2);
        $edtCours->setIdDefiniePeriode(4);
        $edtCours->setJourSemaine('vendredi');
        $edtCours->setHeuredebDec(0);
        $edtCours->setUtilisateurProfessionnel($lebesgue_prof);
        $edtCours->save();
        $edtCours = new EdtEmplacementCours();
        $edtCours->setGroupe($groupe_math);
        $edtCours->setDuree(2);
        $edtCours->setIdDefiniePeriode(1);
        $edtCours->setJourSemaine('jeudi');
        $edtCours->setHeuredebDec(0);
        $edtCours->setUtilisateurProfessionnel($lebesgue_prof);
        $edtCours->save();
        $edtCours = new EdtEmplacementCours();
        $edtCours->setAidDetails($aid_1);
        $edtCours->setDuree(2);
        $edtCours->setIdDefiniePeriode(4);
        $edtCours->setJourSemaine('vendredi');
        $edtCours->setHeuredebDec(0);
        $edtCours->setUtilisateurProfessionnel($newton_prof);
        $edtCours->save();

        //on va peupler des saisies englobantes ou identiques
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-02 08:00:00');
        $saisie_1->setFinAbs('2011-06-02 09:00:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-02 07:00:00');
        $saisie_1->setFinAbs('2011-06-02 10:00:00');
        $saisie_1->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_1);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-03 08:00:00');
        $saisie_1->setFinAbs('2011-06-03 09:00:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-03 08:00:00');
        $saisie_1->setFinAbs('2011-06-03 09:00:00');
        $saisie_1->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_1);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-06 08:00:00');
        $saisie_1->setFinAbs('2011-06-06 09:00:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-06 08:00:00');
        $saisie_1->setFinAbs('2011-06-06 09:30:00');
        $saisie_1->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_1);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-06 08:00:00');
        $saisie_1->setFinAbs('2011-06-06 10:00:00');
        $saisie_1->save();

        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-07 08:00:00');
        $saisie_1->setFinAbs('2011-06-07 09:00:00');
        $saisie_1->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_1);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-07 09:00:00');
        $saisie_1->setFinAbs('2011-06-07 09:40:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-07 08:00:00');
        $saisie_1->setFinAbs('2011-06-07 10:00:00');
        $saisie_1->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_1);
        $traitement->setAbsenceEleveJustification(AbsenceEleveJustificationQuery::create()->filterByNom('Courrier familial')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-08 08:00:00');
        $saisie_1->setFinAbs('2011-06-08 09:00:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-08 08:00:00');
        $saisie_1->setFinAbs('2011-06-08 09:00:00');
        $saisie_1->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_1);
        $traitement->setAbsenceEleveJustification(AbsenceEleveJustificationQuery::create()->filterByNom('Courrier familial')->findOne());
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-09 08:00:00');
        $saisie_1->setFinAbs('2011-06-09 08:10:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-09 08:00:00');
        $saisie_1->setFinAbs('2011-06-09 08:15:00');
        $saisie_1->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_1);
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();
        $notification = new AbsenceEleveNotification();
        $notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_EN_COURS);
        $notification->setTypeNotification(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER);
        $notification->setAbsenceEleveTraitement($traitement);
        $notification->save();
        
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-10 08:00:00');
        $saisie_1->setFinAbs('2011-06-10 08:10:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-10 08:00:00');
        $saisie_1->setFinAbs('2011-06-10 08:10:00');
        $saisie_1->save();

        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-11 08:00:00');
        $saisie_1->setFinAbs('2011-06-11 09:00:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-11 08:00:00');
        $saisie_1->setFinAbs('2011-06-11 09:00:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-11 08:00:00');
        $saisie_1->setFinAbs('2011-06-11 09:00:00');
        $saisie_1->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_1);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-11 08:00:00');
        $saisie_1->setFinAbs('2011-06-11 09:10:00');
        $saisie_1->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_1);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Erreur de saisie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-12 08:00:00');
        $saisie_1->setFinAbs('2011-06-12 09:00:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-12 07:00:00');
        $saisie_1->setFinAbs('2011-06-12 09:10:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-12 08:00:00');
        $saisie_1->setFinAbs('2011-06-12 09:11:00');
        $saisie_1->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_1);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-12 08:00:00');
        $saisie_1->setFinAbs('2011-06-12 09:10:00');
        $saisie_1->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_1);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Erreur de saisie')->findOne());
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();

        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-13 08:00:00');
        $saisie_1->setFinAbs('2011-06-13 08:10:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-13 08:00:00');
        $saisie_1->setFinAbs('2011-06-13 08:10:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-13 08:00:00');
        $saisie_1->setFinAbs('2011-06-13 08:10:00');
        $saisie_1->save();
        $saisie_1 = new AbsenceEleveSaisie();
        $saisie_1->setEleve($florence_eleve);
        $saisie_1->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie_1->setDebutAbs('2011-06-13 08:00:00');
        $saisie_1->setFinAbs('2011-06-13 09:10:00');
        $saisie_1->save();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_1);
        $traitement->setUtilisateurProfessionnel($dolto_cpe);
        $traitement->save();
        $notification = new AbsenceEleveNotification();
        $notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_SUCCES);
        $notification->setTypeNotification(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER);
        $notification->setAbsenceEleveTraitement($traitement);
        $notification->save();
        
        //on va purger les références, qui peuvent être fausses suite à des ajouts ultérieurs
        GepiDataPopulator::clearAllReferences();
        
        
        $con->commit();
    }


    public static function clearAllReferences($con = null)
    {
        $class_map = include(dirname(__FILE__).'/../../../../orm/propel-build/conf/classmap-gepi-conf.php');
        $peerClasses = array();
        foreach ($class_map as $classe => $file) {
            if (substr($classe, -4) == 'Peer') {
                $peerClasses[] = $classe;
            }
        }
         
        // free the memory from existing objects
        foreach ($peerClasses as $peerClass) {
            // $peerClass::$instances crashes on PHP 5.2, see http://www.propelorm.org/ticket/1388
            $r = new ReflectionClass($peerClass);
            $p = $r->getProperty('instances');
            foreach ($p->getValue() as $o) {
                $o->clearAllReferences();
            }
        }
    }
    
    public static function depopulate($con = null)
    {
        $class_map = include(dirname(__FILE__).'/../../../../orm/propel-build/conf/classmap-gepi-conf.php');
        $peerClasses = array();
        foreach ($class_map as $classe => $file) {
            if (substr($classe, -4) == 'Peer') {
                $peerClasses[] = $classe;
            }
        }
         
        // free the memory from existing objects
        foreach ($peerClasses as $peerClass) {
            // $peerClass::$instances crashes on PHP 5.2, see http://www.propelorm.org/ticket/1388
            $r = new ReflectionClass($peerClass);
            $p = $r->getProperty('instances');
            foreach ($p->getValue() as $o) {
                $o->clearAllReferences();
            }
        }
        // delete records from the database
        if($con === null) {
            $con = Propel::getConnection();
        }
        $con->beginTransaction();
        foreach ($peerClasses as $peerClass) {
            // $peerClass::doDeleteAll() crashes on PHP 5.2, see http://www.propelorm.org/ticket/1388
            if (method_exists ($peerClass, 'disableSoftDelete')) {
                call_user_func(array($peerClass, 'disableSoftDelete'), $con);
            }
            call_user_func(array($peerClass, 'doDeleteAll'), $con);
            if (method_exists ($peerClass, 'enableSoftDelete')) {
                call_user_func(array($peerClass, 'enableSoftDelete'), $con);
            }
        }
        $con->commit();
    }

}
