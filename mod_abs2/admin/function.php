<?php
/*
 *
 * $Id$
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

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
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_VRAI);

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
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);

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
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_VRAI);

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
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_NON_PRECISE);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_NON_PRECISE);

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
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);

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
	$type->setSousResponsabiliteEtablissement(true);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);

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
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);

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
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);

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
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);
	$type->setTypeSaisie(AbsenceEleveType::$TYPE_SAISIE_DISCIPLINE);

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
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);

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
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);

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
    $type->setNom("Stage");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'eleve est en stage a l'exterieur de l'etablissement.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);

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
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);

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
