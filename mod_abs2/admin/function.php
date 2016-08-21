<?php
/*
 *
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
    $motif->setNom("Médical");
    $motif->setCommentaire("L'élève est absent pour raison médicale");
    if (AbsenceEleveMotifQuery::create()->filterByNom($motif->getNom())->find()->isEmpty()) {
	$motif->save();
    }

    $motif = new AbsenceEleveMotif();
    $motif->setNom("Familial");
    $motif->setCommentaire("L'élève est absent pour raison familiale");
    if (AbsenceEleveMotifQuery::create()->filterByNom($motif->getNom())->find()->isEmpty()) {
	$motif->save();
    }

    $motif = new AbsenceEleveMotif();
    $motif->setNom("Sportive");
    $motif->setCommentaire("L'élève est absent pour cause de compétition sportive");
    if (AbsenceEleveMotifQuery::create()->filterByNom($motif->getNom())->find()->isEmpty()) {
	$motif->save();
    }
}

function ajoutLieuxParDefaut() {
    $lieu = new AbsenceEleveLieu();
    $lieu->setNom("Etablissement");
    $lieu->setCommentaire("L'élève est dans l'enceinte de l'établissement");
    if (AbsenceEleveLieuQuery::create()->filterByNom($lieu->getNom())->find()->isEmpty()) {
	$lieu->save();
    }
}

function initLieuEtab(){
    $lieu_etab=AbsenceEleveLieuQuery::create()->filterByNom("Etablissement")->findOne();
    if(is_null($lieu_etab)){
       $lieu_etab= new AbsenceEleveLieu();
       $lieu_etab->setNom("Etablissement");
       $lieu_etab->setCommentaire("L'élève est dans l'enceinte de l'établissement");
       $lieu_etab->save();
    }
    return($lieu_etab->getId());
}

function ajoutJustificationsParDefaut() {
    $justifications = new AbsenceEleveJustification();
    $justifications->setNom("Certificat médical");
    $justifications->setCommentaire("Une justification établie par une autorité médicale");
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
    $justifications->setCommentaire("Justification émise par une administration publique");
    if (AbsenceEleveJustificationQuery::create()->filterByNom($justifications->getNom())->find()->isEmpty()) {
	$justifications->save();
    }
}

function ajoutTypesParDefaut() {
    $id_lieu_etab=initLieuEtab();
    $type = new AbsenceEleveType();
    $type->setNom("Absence scolaire");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'élève n'est pas présent pour suivre sa scolarité.");
	$type->setJustificationExigible(true);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI);

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
	$type->setCommentaire("L'élève est en retard lors de l'intercours");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);

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
    $type->setNom("Retard extérieur");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'élève est en retard lors de son arrivée dans l'établissement");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI);
	$type->setRetardBulletin(AbsenceEleveType::RETARD_BULLETIN_VRAI);
        
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
	$type->setCommentaire("Il y a probablement une erreur de saisie sur cet enregistrement. Pour être non comptabilisée,
            une saisie de type 'Erreur de saisie' ne doit être associée avec aucun autre type, mais exclusivement avec le type erreur de saisie.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_NON_PRECISE);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_NON_PRECISE);

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
    $type->setNom("Infirmerie");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'élève est à l'infirmerie.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);

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
	$type->setCommentaire("L'élève est en sortie scolaire.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(true);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);

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
    $type->setNom("Exclusion de l'établissement");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'élève est exclu de l'établissement.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);

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
	$type->setCommentaire("L'élève est exclu mais présent au sein de l'établissement.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);

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
	$type->setCommentaire("L'élève est exclu de cours.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);
	$type->setModeInterface(AbsenceEleveType::MODE_INTERFACE_DISCIPLINE);
    $type->setIdLieu($id_lieu_etab);

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
    $type->setNom("Prévoir cantine");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'élève prévoit de manger au réfectoire.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_NON_PRECISE);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_NON_PRECISE);
	$type->setModeInterface(AbsenceEleveType::MODE_INTERFACE_CHECKBOX_HIDDEN_REGIME);
    $type->setIdLieu($id_lieu_etab);

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
    $type->setNom("Inapte (élève présent)");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'élève est inapte mais présent physiquement lors de la séance.");
	$type->setJustificationExigible(true);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);

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
    $type->setNom("Inapte (élève non présent)");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'élève est inapte et non présent physiquement lors de la séance.");
	$type->setJustificationExigible(true);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);

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
	$type->setCommentaire("L'élève est en stage a l'extérieur de l'établissement.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);

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
    $type->setNom("Élève présent");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'élève est présent.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);
    
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

function check_sortable_rank_trouble($table, $type) {
	$retour="";

	$sql="SELECT 1=1 FROM $table;";
	$res_tot=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="SELECT DISTINCT sortable_rank FROM $table;";
	$res_sr=mysqli_query($GLOBALS["mysqli"], $sql);

	if(mysqli_num_rows($res_sr)!=mysqli_num_rows($res_tot)) {
		$retour="<p style='text-align:center;'><span style='color:red;'><strong>Anomalie&nbsp;:</strong> L'ordre d'affichage des $type dans la table '$table' est incohérent <br />(<em>plusieurs enregistrements ont le même rang</em>)</span><br />\n";
		$retour.="<a href='".$_SERVER['PHP_SELF']."?corriger=y".add_token_in_url()."'>Corriger</a></p>\n";
	}
	return $retour;
}
?>
