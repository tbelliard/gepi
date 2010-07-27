<?php

function ajoutMotifsParDefaut() {
    $motif = new AbsenceEleveMotif();
    $motif->setNom("Medical");
    $motif->setCommentaire("L'eleve est absent pour raison médicale");
    if (AbsenceEleveMotifQuery::create()->filterByNom($motif->getNom())->find()->isEmpty()) {
	$motif->save();
    }

    $motif = new AbsenceEleveMotif();
    $motif->setNom("Familial");
    $motif->setCommentaire("L'eleve est absent pour raison familiale");
    if (AbsenceEleveMotifQuery::create()->filterByNom($motif->getNom())->find()->isEmpty()) {
	$motif->save();
    }

    $motif = new AbsenceEleveMotif();
    $motif->setNom("Sportive");
    $motif->setCommentaire("L'eleve est absent pour cause de competition sportive");
    if (AbsenceEleveMotifQuery::create()->filterByNom($motif->getNom())->find()->isEmpty()) {
	$motif->save();
    }
}

function ajoutJustificationsParDefaut() {
    $justifications = new AbsenceEleveJustification();
    $justifications->setNom("Certificat medical");
    $justifications->setCommentaire("Une justification etablie par une autorité medicale");
    if (AbsenceEleveJustificationQuery::create()->filterByNom($justifications->getNom())->find()->isEmpty()) {
	$justifications->save();
    }

    $justifications = new AbsenceEleveJustification();
    $justifications->setNom("Courrier familial");
    $justifications->setCommentaire("Justification par courrier de la famille");
    if (AbsenceEleveJustificationQuery::create()->filterByNom($justifications->getNom())->find()->isEmpty()) {
	$justifications->save();
    }

    $justifications = new AbsenceEleveJustification();
    $justifications->setNom("Justificatif d'une administration publique");
    $justifications->setCommentaire("Justification emise par une administration publique");
    if (AbsenceEleveJustificationQuery::create()->filterByNom($justifications->getNom())->find()->isEmpty()) {
	$justifications->save();
    }
}

function ajoutTypesParDefaut() {

    $type = new AbsenceEleveType();
    $type->setNom("Absence scolaire");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'eleve n'est pas présent pour suivre sa scolarité.");
	$type->setJustificationExigible(true);
	$type->setResponsabiliteEtablissement(false);
	//$type->setTypeSaisie('NON_PRECISE');

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Retard intercours");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'eleve est en retard lors de l'intercours");
	$type->setJustificationExigible(false);
	$type->setResponsabiliteEtablissement(true);
	//$type->setTypeSaisie('FIN_ABS');

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Retard exterieur");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'eleve est en retard lors de son arrivée dans l'etablissement");
	$type->setJustificationExigible(false);
	$type->setResponsabiliteEtablissement(false);
	//$type->setTypeSaisie('FIN_ABS');

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Erreur de saisie");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("Il y a probablement une erreur de saisie sur cet enregistrement.");
	$type->setJustificationExigible(false);
	$type->setResponsabiliteEtablissement(true);
	$type->setTypeSaisie('NON_PRECISE');

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

 	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Infirmerie");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'eleve est à l'infirmerie.");
	$type->setJustificationExigible(false);
	$type->setResponsabiliteEtablissement(true);
	//$type->setTypeSaisie('DEBUT_ET_FIN_ABS');

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Sortie scolaire");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'eleve est en sortie scolaire.");
	$type->setJustificationExigible(false);
	$type->setResponsabiliteEtablissement(true);
	$type->setTypeSaisie('NON_PRECISE');

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Exclusion");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'eleve est exclus du college.");
	$type->setJustificationExigible(false);
	$type->setResponsabiliteEtablissement(false);
	$type->setTypeSaisie('NON_PRECISE');

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Exclusion/inclusion");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'eleve est exclus mais present au sein de l'etablissement.");
	$type->setJustificationExigible(false);
	$type->setResponsabiliteEtablissement(true);
	$type->setTypeSaisie('NON_PRECISE');

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Exclusion de cours");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'eleve est exclus de cours.");
	$type->setJustificationExigible(false);
	$type->setResponsabiliteEtablissement(true);
	$type->setTypeSaisie('DISCIPLINE');

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Dispense (eleve present)");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'eleve est dispensé mais present physiquement lors de la seance.");
	$type->setJustificationExigible(true);
	$type->setResponsabiliteEtablissement(true);
	$type->setTypeSaisie('NON_PRECISE');

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Dispense (eleve non present)");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'eleve est dispensé et non present physiquement lors de la seance.");
	$type->setJustificationExigible(true);
	$type->setResponsabiliteEtablissement(false);
	$type->setTypeSaisie('NON_PRECISE');

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Eleve présent");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'eleve est présent.");
	$type->setJustificationExigible(false);
	$type->setResponsabiliteEtablissement(true);
	$type->setTypeSaisie('NON_PRECISE');

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }
}
?>
