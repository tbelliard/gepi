<?php

/*
 *
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Didier Blanqui
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
// On empêche l'accès direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};

require_once("Class.Date.php");
require_once("Class.Modele.php");
require_once("Modele.Incidents.php");
require_once("Class.Filter.php");
require_once("Class.Individu.php");

class ClassIncidents {

    private $sanctions = Null;
    private $protagonistes = Null;
    private $mesures = Null;
    private $protagonistes_from_db = Null;
    private $incidents_from_db = Null;
    private $modele_incidents = Null;
    private $modele_select = Null;
    private $liste_incidents = Null;
    private $id = Null;
    private $crenaux = Null;
    private $totaux = Null;
    private $totaux_indiv = Null;
    private $totaux_par_classe = Null;
    private $incidents = Null;
    private $liste_eleves = Null;
    private $liste_eleves_par_classe = Null;
    private $infos_individus = Null;
    private $top_incidents = Null;
    private $top_sanctions = Null;
    private $top_retenues = Null;
    private $top_exclusions = Null;
    private $array_id_incidents=null;
    
    public function __construct() {

        $this->modele = new Modele();
        $this->modele_incidents = new Modele_Incidents();
        $this->modele_select = new modele_select();
    }

    // debut des accesseurs pour la classe incident

    public function get_incidents() {
        return $this->incidents;
    }

    public function get_protagonistes() {
        return $this->protagonistes;
    }

    public function get_mesures() {
        return $this->mesures;
    }

    public function get_sanctions() {
        return $this->sanctions;
    }

    public function get_totaux() {
        return $this->totaux;
    }

    public function get_liste_eleves_par_classe() {
        return $this->liste_eleves_par_classe;
    }

    public function get_infos_individus() {
        return $this->infos_individus;
    }

    public function get_totaux_indiv() {
        return $this->totaux_indiv;
    }

    public function get_totaux_par_classe() {
        return $this->totaux_par_classe;
    }

    public function get_top_incidents($du, $au, $filtres_categories=Null, $filtres_mesures=Null, $filtres_sanctions=Null, $filtres_roles=Null) {
        return $this->get_top_incidents_from_db($du, $au, $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
    }

    public function get_top_sanctions($du, $au, $filtres_categories=Null, $filtres_mesures=Null, $filtres_sanctions=Null, $filtres_roles=Null) {
        return $this->get_top_sanctions_from_db($du, $au, $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
    }

    public function get_top_retenues($du, $au, $filtres_categories=Null, $filtres_mesures=Null, $filtres_sanctions=Null, $filtres_roles=Null) {
        return $this->get_top_retenues_from_db($du, $au, $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
    }

    public function get_top_exclusions($du, $au, $filtres_categories=Null, $filtres_mesures=Null, $filtres_sanctions=Null, $filtres_roles=Null) {
        return $this->get_top_exclusions_from_db($du, $au, $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
    }

//fin des accesseurs
// On recupère les id des incidents en fonction des filtres activés et on traite les données pour affichage
    public function traite_incidents_criteres($du, $au, $filtres_categories=Null, $filtres_mesures=Null, $filtres_sanctions=Null, $filtres_roles=Null) {
        $this->traite_incidents($du, $au, $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
        $this->traite_protagonistes();
        $this->traite_mesures();
        $this->traite_sanctions();
        $this->calcule_totaux_globaux();
        $objet_individu = new ClassIndividu();
        $this->infos_individus = $objet_individu->get_infos_liste_individus($this->liste_eleves_par_classe);
        if ($this->liste_eleves_par_classe) {
            $this->calcule_totaux_indiv($this->liste_eleves_par_classe, $this->liste_incidents, $this->protagonistes, $this->mesures, $this->sanctions);
            $this->calcule_totaux_classe($this->liste_eleves_par_classe, $this->liste_incidents, $this->totaux_indiv);
        }
    }

    private function traite_incidents($du, $au, $filtres_categories=Null, $filtres_mesures=Null, $filtres_sanctions=Null, $filtres_roles=Null) {

        $this->incidents_from_db['L\'Etablissement'] = $this->modele_incidents->get_incidents('etab_all', 'L\'Etablissement', $du, $au, Null, $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
        if (!isset($this->incidents_from_db['L\'Etablissement']['error'])) {
            $this->liste_id_incidents_selected = $this->make_liste_id($this->incidents_from_db['L\'Etablissement']);
            $this->protagonistes_from_db = $this->modele_incidents->get_protagonistes($this->liste_id_incidents_selected);
            if(isset($this->protagonistes_from_db)) $this->liste_eleves_par_classe['L\'Etablissement'] = $this->make_liste_protagonistes($this->protagonistes_from_db, 'eleve',$this->array_id_incidents);
            $this->mesures_from_db = $this->modele_incidents->get_mesures($this->liste_id_incidents_selected);
            $this->sanctions_from_db = $this->modele_incidents->get_sanctions($this->liste_id_incidents_selected);
        }
        if (isset($_SESSION['eleve_all'])) {
            $this->incidents_from_db['Tous les élèves'] = $this->modele_incidents->get_incidents('eleves_all', 'Tous les élèves', $du, $au, Null, $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
            if (!isset($this->incidents_from_db['Tous les élèves']['error'])) {
                $this->liste_id_incidents_selected = $this->make_liste_id($this->incidents_from_db['Tous les élèves']);
                $this->protagonistes_from_db = $this->modele_incidents->get_protagonistes($this->liste_id_incidents_selected);
                if(isset($this->protagonistes_from_db)) $this->liste_eleves_par_classe['Tous les élèves'] = $this->make_liste_protagonistes($this->protagonistes_from_db, 'eleve',$this->array_id_incidents);
                $this->mesures_from_db = $this->modele_incidents->get_mesures($this->liste_id_incidents_selected);
                $this->sanctions_from_db = $this->modele_incidents->get_sanctions($this->liste_id_incidents_selected);
            }
        }
        if (isset($_SESSION['pers_all'])) {
            $this->incidents_from_db['Tous les personnels'] = $this->modele_incidents->get_incidents('pers_all', 'Tous les personnels', $du, $au, Null, $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
            if (!isset($this->incidents_from_db['Tous les personnels']['error'])) {
                $this->liste_id_incidents_selected = $this->make_liste_id($this->incidents_from_db['Tous les personnels']);
                $this->protagonistes_from_db = $this->modele_incidents->get_protagonistes($this->liste_id_incidents_selected);
                if(isset($this->protagonistes_from_db)) $this->liste_eleves_par_classe['Tous les personnels'] = $this->make_liste_protagonistes($this->protagonistes_from_db, 'eleve',$this->array_id_incidents);
                $this->mesures_from_db = $this->modele_incidents->get_mesures($this->liste_id_incidents_selected);
                $this->sanctions_from_db = $this->modele_incidents->get_sanctions($this->liste_id_incidents_selected);
            }
        }
        if (isset($_SESSION['stats_classes_selected'])) {
            foreach ($_SESSION['stats_classes_selected'] as $value) {
                if (isset($this->liste_eleves))
                    unset($this->liste_eleves);
                $this->liste_eleves[$value] = $this->modele_select->get_eleves_classe($value);
                $modele_select = new modele_select();
                $this->infos_classe = $modele_select->get_infos_classe($value);
                foreach ($this->liste_eleves as $this->classe) {
                    $this->liste_eleves_par_classe[$this->infos_classe[0]['classe']] = $this->classe;
                    $this->incidents_from_db[$this->infos_classe[0]['classe']] = $this->modele_incidents->get_incidents('classe', $this->infos_classe[0]['classe'], $du, $au, $this->modele->make_list_for_request_in($this->classe), $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
                    if (!isset($this->incidents_from_db[$this->infos_classe[0]['classe']]['error'])) {
                        $this->liste_id_incidents_selected = $this->make_liste_id($this->incidents_from_db[$this->infos_classe[0]['classe']]);
                        $this->protagonistes_from_db = $this->modele_incidents->get_protagonistes($this->liste_id_incidents_selected);
                        $this->mesures_from_db = $this->modele_incidents->get_mesures($this->liste_id_incidents_selected);
                        $this->sanctions_from_db = $this->modele_incidents->get_sanctions($this->liste_id_incidents_selected);
                    }
                }
            }
        }
        if (isset($_SESSION['individus'])) {
            foreach ($_SESSION['individus'] as $value) {
                if ($value[1] == 'eleves')
                    $this->liste_eleves_par_classe[$value[0]][] = $value[0];
                $this->incidents_from_db[$value[0]] = $this->modele_incidents->get_incidents('individu', $value[0], $du, $au, $value[0], $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
                if (!isset($this->incidents_from_db[$value[0]]['error'])) {
                    $this->liste_id_incidents_selected = $this->make_liste_id($this->incidents_from_db[$value[0]]);
                    $this->protagonistes_from_db = $this->modele_incidents->get_protagonistes($this->liste_id_incidents_selected);
                    $this->mesures_from_db = $this->modele_incidents->get_mesures($this->liste_id_incidents_selected);
                    $this->sanctions_from_db = $this->modele_incidents->get_sanctions($this->liste_id_incidents_selected);
                }
            }
        }
        if (isset($this->incidents_from_db))
            $this->incidents = $this->traite_donnees_incidents($this->incidents_from_db);
    }

    private function make_liste_id($array_incidents) {
        unset($this->array_id_incidents);
        foreach ($array_incidents as $incident) {
            $this->array_id_incidents[] = $incident->id_incident;
        }
        return($this->modele->make_list_for_request_in($this->array_id_incidents));
    }
    
    private function make_liste_protagonistes($protagonistes, $statut,$liste_incidents) {
        foreach ($protagonistes as $id_incident) { 
            foreach ($id_incident as $protagoniste) {
                if(!in_array($protagoniste->id_incident,$liste_incidents)){
                    continue;
                }
                if ($protagoniste->statut == $statut) {
                    $liste_protagoniste[$protagoniste->login] = $protagoniste->login;
                }
            }
        }
        return($liste_protagoniste);
    }

    private function traite_donnees_incidents($tableau_incidents) {
        foreach ($tableau_incidents as $key => $incidents_from_db) {
            foreach ($incidents_from_db as $incident_from_db) {
                if (isset($incident_from_db->date)) {
                    $incident_from_db->date = $this->traite_date_tableau($incident_from_db->date);
                    $incident_from_db->declarant = $this->traite_login_declarant($incident_from_db->declarant);
                    if (!is_null($incident_from_db->id_categorie)) {
                        $this->infos_categorie = $this->traite_categorie($incident_from_db->id_categorie);
                        $incident_from_db->libelle_categorie = $this->infos_categorie[0]->categorie;
                        $incident_from_db->sigle_categorie = $this->infos_categorie[0]->sigle;
                    }
                }
            }
        }
        return $tableau_incidents;
    }

    private function traite_date_tableau($date) {
        return(Gepi_Date::format_date_iso_fr($date));
    }

    private function traite_login_declarant($login) {

        $this->infos_utilisateur = $this->modele_select->get_db_individu_identite($login, 'personnels');
        return($this->infos_utilisateur['prenom'] . ' ' . $this->infos_utilisateur['nom']);
    }

    private function traite_categorie($id_categorie) {
        return($this->modele_incidents->get_infos_categories($id_categorie));
    }

// Fin traitement des id incidents
// On récupère les protagonistes correspondants aux incidents selectionnés et on traite les données pour affichage

    private function traite_protagonistes() {
        if (isset($this->protagonistes_from_db))
            $this->protagonistes = $this->traite_donnees_protagonistes($this->protagonistes_from_db);
    }

    private function traite_donnees_protagonistes($tableau_protagonistes) {
        foreach ($tableau_protagonistes as $incident) {
            foreach ($incident as $protagoniste) {
                if ($protagoniste !== 'pas de résultats') {
                    $this->statut = $this->test_statut($protagoniste->statut);
                    $this->infos_utilisateur = $this->modele_select->get_db_individu_identite($protagoniste->login, $this->statut);
                    $protagoniste->nom = $this->infos_utilisateur['nom'];
                    $protagoniste->prenom = $this->infos_utilisateur['prenom'];
                }
            }
        }
        return $tableau_protagonistes;
    }

    private function test_statut($statut) {
        if ($statut == 'eleve')
            return 'eleves';
        else
            return 'personnels';
    }

//fin traitement des protagonistes
// On traite les mesures correspondants aux incidents selectionnés
    private function traite_mesures() {
        if (isset($this->mesures_from_db)

            )$this->mesures = $this->traite_donnees_mesures($this->mesures_from_db);
    }

    private function traite_donnees_mesures($tableau_mesures) {
        foreach ($tableau_mesures as $id_incident) {
            foreach ($id_incident as $protagoniste) {
                foreach ($protagoniste as $id_mesure) {
                    $id_mesure->login_u = $this->traite_login_declarant($id_mesure->login_u);
                }
            }
        }
        return $tableau_mesures;
    }

// fin traitement mesures
// On traite les sanctions correspondants aux incidents selectionnés

    private function traite_sanctions() {
        if (isset($this->sanctions_from_db))
            $this->sanctions = $this->traite_donnees_sanctions($this->sanctions_from_db);
    }

    private function traite_donnees_sanctions($tableau_sanctions) {
        foreach ($tableau_sanctions as $id_incident) {
            foreach ($id_incident as $id_sanction) {
                foreach ($id_sanction as $protagoniste) {
                    if ($protagoniste->nature == 'Exclusion') {
                        $this->crenaux = $this->modele_incidents->get_crenaux();
                        if (is_null($this->crenaux) || (!isset($this->crenaux[$protagoniste->exc_heure_debut]) && !isset($this->crenaux[$protagoniste->exc_heure_fin]))) {
                            $date_debut_ex = "08:00:00";
                            $date_fin_ex = "18:00:00";
                        } else if (!isset($this->crenaux[$protagoniste->exc_heure_debut])) {
                            $date_debut_ex = "08:00:00";
                            $date_fin_ex = $this->crenaux[$protagoniste->exc_heure_fin]->heurefin_definie_periode;
                        } else if (!isset($this->crenaux[$protagoniste->exc_heure_fin])) {
                            $date_debut_ex = $this->crenaux[$protagoniste->exc_heure_debut]->heuredebut_definie_periode;
                            $date_fin_ex = "18:00:00";
                        } else {
                            $date_debut_ex = $this->crenaux[$protagoniste->exc_heure_debut]->heuredebut_definie_periode;
                            $date_fin_ex = $this->crenaux[$protagoniste->exc_heure_fin]->heurefin_definie_periode;
                        }
                        $protagoniste->exc_duree = Gepi_Date::calcule_duree_exclusion($protagoniste->exc_date_debut, $date_debut_ex, $protagoniste->exc_date_fin, $date_fin_ex);
                    }
                    if (!is_null($protagoniste->ret_date))
                        $protagoniste->ret_date = $this->traite_date_tableau($protagoniste->ret_date);
                    if (!is_null($protagoniste->exc_date_debut))
                        $protagoniste->exc_date_debut = $this->traite_date_tableau($protagoniste->exc_date_debut);
                    if (!is_null($protagoniste->exc_date_fin))
                        $protagoniste->exc_date_fin = $this->traite_date_tableau($protagoniste->exc_date_fin);
                    if (!is_null($protagoniste->trv_date_retour))
                        $protagoniste->trv_date_retour = $this->traite_date_tableau($protagoniste->trv_date_retour);
                }
            }
        }
        return $tableau_sanctions;
    }

//Fin du traitement des sanctions
// On calcule les totaux globaux

    private function calcule_totaux_globaux() {
        if (isset($this->incidents)

            )$this->liste_incidents = $this->make_liste_incidents($this->incidents);
        if ($this->liste_incidents)
            $this->totaux = $this->get_totaux_selections($this->liste_incidents, $this->sanctions);
    }

    private function make_liste_incidents($array_incidents) {
        foreach ($array_incidents as $key => $incident) {
            foreach ($incident as $value) {
                if ($value !== 'pas de résultats')
                    $this->id[$key][] = $value->id_incident;
            }
        }
        if ($this->id)
            return($this->id);
    }

    private function get_totaux_selections($liste, $sanctions) {
        foreach ($liste as $selection => $incidents) {
            $this->totaux[$selection]['incidents'] = $this->get_nbre_incidents($incidents);
            $this->totaux[$selection]['mesures_prises'] = $this->get_nbre_mesures($incidents, 'prise');
            $this->totaux[$selection]['mesures_demandees'] = $this->get_nbre_mesures($incidents, 'demandee');
            $this->totaux[$selection]['mesures'] = $this->get_nbre_mesures($incidents);
            if ($sanctions) {
                $this->totaux[$selection]['sanctions'] = $this->get_nbre_sanctions($incidents);
                $this->totaux[$selection]['heures_retenues'] = $this->get_nbre_heures_retenues($incidents, $sanctions);
                $this->totaux[$selection]['jours_exclusions'] = $this->get_nbre_jours_exclusions($incidents, $sanctions);
            } else {
                $this->totaux[$selection]['sanctions'] = 0;
                $this->totaux[$selection]['heures_retenues'] = 0;
                $this->totaux[$selection]['jours_exclusions'] = 0;
            }
        }
        return $this->totaux;
    }

    private function get_nbre_incidents($incidents) {
        return($this->nbre = $this->modele_incidents->get_nombre_total_incidents($incidents));
    }

    private function get_nbre_mesures($incidents, $type=Null) {
        return($this->nbre = $this->modele_incidents->get_nombre_total_mesures($incidents, $type));
    }

    private function get_nbre_sanctions($incidents) {
        return($this->nbre = $this->modele_incidents->get_nombre_total_sanctions($incidents));
    }

    private function get_nbre_heures_retenues($id_incidents, $sanctions) {
        $this->nbre_heures = 0;
        foreach ($id_incidents as $id_incident) {
            if (array_key_exists($id_incident, $sanctions)) {
                foreach ($sanctions[$id_incident] as $id_sanctions) {
                    foreach ($id_sanctions as $sanction) {
                        if ($sanction->nature == 'Retenue')
                            $this->nbre_heures = $this->nbre_heures + $sanction->ret_duree;
                    }
                }
            }
        }
        return $this->nbre_heures;
    }

    private function get_nbre_jours_exclusions($id_incidents, $sanctions) {
        $this->nbre_jours = 0;
        foreach ($id_incidents as $id_incident) {
            if (array_key_exists($id_incident, $sanctions)) {
                foreach ($sanctions[$id_incident] as $id_sanctions) {
                    foreach ($id_sanctions as $sanction) {
                        if ($sanction->nature == 'Exclusion')
                            $this->nbre_jours = $this->nbre_jours + $sanction->exc_duree;
                    }
                }
            }
        }
        return $this->nbre_jours;
    }

// fin du calcul des totaux globaux
// On calcule les totaux individuels 

    private function calcule_totaux_indiv($liste_eleves, $liste, $protagonistes, $mesures, $sanctions) {
        $this->totaux_indiv = Null;
        $Object_individu = New ClassIndividu();
        foreach ($liste_eleves as $classe => $eleves) {
            if (isset($liste[$classe])) {
                foreach ($eleves as $eleve) {
                    $this->info_indiv[$eleve] = $Object_individu->get_infos_individu($eleve, 'eleves');
                    $this->totaux_indiv[$eleve]['nom'] = $this->info_indiv[$eleve]['nom'];
                    $this->totaux_indiv[$eleve]['prenom'] = $this->info_indiv[$eleve]['prenom'];
                    $this->totaux_indiv[$eleve]['classe'] = $this->info_indiv[$eleve]['classe'];
                    $this->totaux_indiv[$eleve]['incidents'] = $this->get_nbre_incidents_indiv($eleve, $liste[$classe], $protagonistes);
                    $this->totaux_indiv[$eleve]['mesures'] = $this->get_nbre_mesures_indiv($eleve, $liste[$classe], $mesures);
                    if ($sanctions) {
                        $this->totaux_indiv[$eleve]['sanctions'] = $this->get_nbre_sanctions_indiv($eleve, $liste[$classe], $sanctions);
                        $this->totaux_indiv[$eleve]['heures_retenues'] = $this->get_nbre_heures_retenues_indiv($eleve, $liste[$classe], $sanctions);
                        $this->totaux_indiv[$eleve]['jours_exclusions'] = $this->get_nbre_jours_exclusions_indiv($eleve, $liste[$classe], $sanctions);
                    }
                }
            }
        }
        return $this->totaux_indiv;
    }

    private function get_nbre_incidents_indiv($eleve, $incidents, $protagonistes) {
        $this->cpt = 0;
        foreach ($incidents as $incident) {
            if (isset($protagonistes[$incident])) { //on vire les incidents sans protagonistes
                if (array_key_exists($eleve, $protagonistes[$incident]))
                    $this->cpt++;
            }
        }
        return($this->cpt);
    }

    private function get_nbre_mesures_indiv($eleve, $incidents, $mesures) {
        $this->cpt = 0;
        foreach ($incidents as $incident) {
            if (isset($mesures[$incident][$eleve])) {
                foreach ($mesures[$incident][$eleve] as $mesure) {
                    if ($mesure->type == 'prise'

                        )$this->cpt++;
                }
            }
        }
        return($this->cpt);
    }

    private function get_nbre_sanctions_indiv($eleve, $incidents, $sanctions) {
        $this->cpt = 0;
        foreach ($incidents as $incident) {
            if (isset($sanctions[$incident][$eleve])

                )$this->cpt = $this->cpt + count($sanctions[$incident][$eleve]);
        }
        return($this->cpt);
    }

    private function get_nbre_heures_retenues_indiv($eleve, $incidents, $sanctions) {
        $this->nbre_heures = 0;
        foreach ($incidents as $incident) {
            if (isset($sanctions[$incident][$eleve])) {
                foreach ($sanctions[$incident][$eleve] as $sanction) {
                    if ($sanction->nature == 'Retenue')
                        $this->nbre_heures = $this->nbre_heures + $sanction->ret_duree;
                }
            }
        }
        return($this->nbre_heures);
    }

    private function get_nbre_jours_exclusions_indiv($eleve, $incidents, $sanctions) {
        $this->nbre_jours = 0;
        foreach ($incidents as $incident) {
            if (isset($sanctions[$incident][$eleve])) {
                foreach ($sanctions[$incident][$eleve] as $sanction) {
                    if ($sanction->nature == 'Exclusion')
                        $this->nbre_jours = $this->nbre_jours + $sanction->exc_duree;
                }
            }
        }
        return($this->nbre_jours);
    }

// fin du calcul des totaux indivividuels
// On calcule les totaux par classe  

    private function calcule_totaux_classe($liste_eleves, $liste, $totaux_indiv) {

        foreach ($liste_eleves as $classe => $eleves) {
            $this->totaux_par_classe[$classe]['incidents'] = 0;
            $this->totaux_par_classe[$classe]['mesures'] = 0;
            $this->totaux_par_classe[$classe]['sanctions'] = 0;
            $this->totaux_par_classe[$classe]['heures_retenues'] = 0;
            $this->totaux_par_classe[$classe]['jours_exclusions'] = 0;

            if (isset($liste[$classe])) {
                foreach ($eleves as $eleve) {
                    if (isset($totaux_indiv[$eleve]['incidents']))
                        $this->totaux_par_classe[$classe]['incidents'] = $this->totaux_par_classe[$classe]['incidents'] + $totaux_indiv[$eleve]['incidents'];
                    if (isset($totaux_indiv[$eleve]['mesures']))
                        $this->totaux_par_classe[$classe]['mesures'] = $this->totaux_par_classe[$classe]['mesures'] + $totaux_indiv[$eleve]['mesures'];
                    if (isset($totaux_indiv[$eleve]['sanctions']))
                        $this->totaux_par_classe[$classe]['sanctions'] = $this->totaux_par_classe[$classe]['sanctions'] + $totaux_indiv[$eleve]['sanctions'];
                    if (isset($totaux_indiv[$eleve]['heures_retenues']))
                        $this->totaux_par_classe[$classe]['heures_retenues'] = $this->totaux_par_classe[$classe]['heures_retenues'] + $totaux_indiv[$eleve]['heures_retenues'];
                    if (isset($totaux_indiv[$eleve]['jours_exclusions']))
                        $this->totaux_par_classe[$classe]['jours_exclusions'] = $this->totaux_par_classe[$classe]['jours_exclusions'] + $totaux_indiv[$eleve]['jours_exclusions'];
                }
            }
        }
        return($this->totaux_par_classe);
    }

// fin du calcul des totaux par classe
    //on recupère les divers Top 10 et on les traite
    private function get_top_incidents_from_db($du, $au, $filtres_categories=Null, $filtres_mesures=Null, $filtres_sanctions=Null, $filtres_roles=Null) {

        $this->top_incidents = $this->modele_incidents->get_top_incidents($du, $au, $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
        if ($this->top_incidents) {
            foreach ($this->top_incidents as $value) {
                $this->infos_eleves = $this->modele_select->get_db_individu_identite($value->login, 'eleves');
                $value->nom = $this->infos_eleves['nom'];
                $value->prenom = $this->infos_eleves['prenom'];
                $value->classe = $this->infos_eleves['classe'];
            }
        }
        return($this->top_incidents);
    }

    private function get_top_sanctions_from_db($du, $au, $filtres_categories=Null, $filtres_mesures=Null, $filtres_sanctions=Null, $filtres_roles=Null) {

        $this->top_sanctions = $this->modele_incidents->get_top_sanctions($du, $au, $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
        if ($this->top_sanctions) {
            foreach ($this->top_sanctions as $value) {
                $this->infos_eleves = $this->modele_select->get_db_individu_identite($value->login, 'eleves');
                $value->nom = $this->infos_eleves['nom'];
                $value->prenom = $this->infos_eleves['prenom'];
                $value->classe = $this->infos_eleves['classe'];
            }
        }
        return($this->top_sanctions);
    }

    private function get_top_retenues_from_db($du, $au, $filtres_categories=Null, $filtres_mesures=Null, $filtres_sanctions=Null, $filtres_roles=Null) {
        $this->top_retenues = $this->modele_incidents->get_top_retenues($du, $au, $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
        if ($this->top_retenues) {
            foreach ($this->top_retenues as $value) {
                $this->infos_eleves = $this->modele_select->get_db_individu_identite($value->login, 'eleves');
                $value->nom = $this->infos_eleves['nom'];
                $value->prenom = $this->infos_eleves['prenom'];
                $value->classe = $this->infos_eleves['classe'];
            }
            $this->top_retenues=array_slice($this->top_retenues,0,10);
        }
        return($this->top_retenues);
    }

    private function get_top_exclusions_from_db($du, $au, $filtres_categories=Null, $filtres_mesures=Null, $filtres_sanctions=Null, $filtres_roles=Null) {
        $this->top_exclusions = $this->modele_incidents->get_top_exclusions($du, $au, $filtres_categories, $filtres_mesures, $filtres_sanctions, $filtres_roles);
        if ($this->top_exclusions) {
            foreach ($this->top_exclusions as $value) {
                $this->infos_eleves = $this->modele_select->get_db_individu_identite($value->login, 'eleves');
                $value->nom = $this->infos_eleves['nom'];
                $value->prenom = $this->infos_eleves['prenom'];
                $value->classe = $this->infos_eleves['classe'];
            }
        }
        return($this->top_exclusions);
    }

//fin des top 10
}

?>
