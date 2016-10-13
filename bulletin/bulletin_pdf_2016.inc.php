<?php
	// Tableau des champs de model_bulletin
	// Il faudra peut-être revoir cela pour initialiser un tableau $valeur_bull_pdf[] des valeurs par défaut des champs
	// ou une autre structure $modele_bull_pdf[$i]['nom_champ']="nom_model_bulletin"
	//                        $modele_bull_pdf[$i]['valeur']="Defaut"

	// echo "show fields from model_bulletin;" | mysql -uroot -p gepitrunk|tr "\t" " "|cut -d" " -f1|while read A;do echo "\$champ_bull_pdf[]=\"$A\";";done
	//$champ_bull_pdf[]="Field";
	//$champ_bull_pdf[]="id_model_bulletin";


	//========================================

	// 20160702
	$param_bull2016=array();

	// Couleurs de polices à prévoir

	// Couleur premier carré du cycle 2, 3 ou 4 quand ce n'est pas le cycle courant
	$param_bull2016["couleur_cycle_autre"]["R"]=244;
	$param_bull2016["couleur_cycle_autre"]["V"]=244;
	$param_bull2016["couleur_cycle_autre"]["B"]=244;
	//f4f4f4

	// Couleur premier carré du cycle 2, 3 ou 4 quand c'est le cycle courant
	$param_bull2016["couleur_cycle_courant"]["cycle"]["R"]=9;
	$param_bull2016["couleur_cycle_courant"]["cycle"]["V"]=94;
	$param_bull2016["couleur_cycle_courant"]["cycle"]["B"]=221;
	//0994dd

	// Couleur du carré du niveau courant de l'élève dans le cycle courant
	$param_bull2016["couleur_cycle_courant"]["niveau"]["R"]=166;
	$param_bull2016["couleur_cycle_courant"]["niveau"]["V"]=216;
	$param_bull2016["couleur_cycle_courant"]["niveau"]["B"]=172;
	//a6d81c

	// Couleur des carrés de niveaux autres que celui de l'élève courant
	$param_bull2016["couleur_cycle_autre"]["autre"]["R"]=244;
	$param_bull2016["couleur_cycle_autre"]["autre"]["V"]=244;
	$param_bull2016["couleur_cycle_autre"]["autre"]["B"]=244;
	//f4f4f4

	//========================================

	// Cadre identité de l'élève, de la période,...
	$param_bull2016["couleur_cadre_identite"]["R"]=230;
	$param_bull2016["couleur_cadre_identite"]["V"]=242;
	$param_bull2016["couleur_cadre_identite"]["B"]=252;
	//e6f2fc

	// Bande Suivi des acquis...
	// C'est aussi la couleur du carré de cycle courant
	$param_bull2016["couleur_bandeau_suivi_acquis"]["R"]=9;
	$param_bull2016["couleur_bandeau_suivi_acquis"]["V"]=94;
	$param_bull2016["couleur_bandeau_suivi_acquis"]["B"]=221;
	//0994dd

	// Ligne d'entête du tableau des acquis scolaires...
	// C'est aussi la couleur du bandeau identité de l'élève
	$param_bull2016["couleur_acquis_ligne_entete"]["R"]=230;
	$param_bull2016["couleur_acquis_ligne_entete"]["V"]=242;
	$param_bull2016["couleur_acquis_ligne_entete"]["B"]=252;
	//e6f2fc

	// Première ligne du tableau des acquis scolaires... après l'entête
	$param_bull2016["couleur_acquis_ligne_alt1"]["R"]=243;
	$param_bull2016["couleur_acquis_ligne_alt1"]["V"]=249;
	$param_bull2016["couleur_acquis_ligne_alt1"]["B"]=254;
	//f3f9fe

	// Deuxième ligne du tableau des acquis scolaires... après l'entête
	// C'est aussi la couleur du bandeau identité de l'élève
	$param_bull2016["couleur_acquis_ligne_alt2"]["R"]=230;
	$param_bull2016["couleur_acquis_ligne_alt2"]["V"]=242;
	$param_bull2016["couleur_acquis_ligne_alt2"]["B"]=252;
	//e6f2fc

	// Ligne d'entête, colonne moyenne:
	$param_bull2016["couleur_acquis_colonne_moyenne_ligne_entete"]["R"]=204;
	$param_bull2016["couleur_acquis_colonne_moyenne_ligne_entete"]["V"]=230;
	$param_bull2016["couleur_acquis_colonne_moyenne_ligne_entete"]["B"]=248;
	//cce6f8

	// Première ligne après l'entête, colonne moyenne:
	$param_bull2016["couleur_acquis_colonne_moyenne_ligne_alt1"]["R"]=217;
	$param_bull2016["couleur_acquis_colonne_moyenne_ligne_alt1"]["V"]=236;
	$param_bull2016["couleur_acquis_colonne_moyenne_ligne_alt1"]["B"]=250;
	//d9ecfa

	// Deuxième ligne après l'entête, colonne moyenne:
	$param_bull2016["couleur_acquis_colonne_moyenne_ligne_alt2"]["R"]=204;
	$param_bull2016["couleur_acquis_colonne_moyenne_ligne_alt2"]["V"]=230;
	$param_bull2016["couleur_acquis_colonne_moyenne_ligne_alt2"]["B"]=248;
	//cce6f8

	//========================================

	$param_bull2016["bull2016_INE"]=getSettingValue("bull2016_INE");

	//========================================

	$param_bull2016["x_cadre_logo_RF"]=10;
	$param_bull2016["y_cadre_logo_RF"]=5;
	$param_bull2016["largeur_cadre_logo_RF"]=25;
	$param_bull2016["hauteur_cadre_logo_RF"]=17;

	$param_bull2016["x_logo_RF"]=$param_bull2016["x_cadre_logo_RF"]+4;
	$param_bull2016["y_logo_RF"]=$param_bull2016["y_cadre_logo_RF"]+3;
	$param_bull2016["largeur_logo_RF"]=$param_bull2016["largeur_cadre_logo_RF"]-2*4;
	$param_bull2016["hauteur_logo_RF"]=10;

	//+++++++++++++++++

	$param_bull2016["x_cadre_EN"]=10;
	$param_bull2016["y_cadre_EN"]=$param_bull2016["y_cadre_logo_RF"]+$param_bull2016["hauteur_cadre_logo_RF"];
	$param_bull2016["hauteur_cadre_EN"]=$param_bull2016["largeur_cadre_logo_RF"];
	$param_bull2016["hauteur_cadre_EN"]=19;
	// Taille de police à prévoir

	//+++++++++++++++++

	$param_bull2016["x_cadre_etab"]=37;
	$param_bull2016["largeur_cadre_etab"]=70;
	$param_bull2016["y_cadre_etab_academie"]=8;
	$param_bull2016["y_cadre_etab_college"]=14;
	$param_bull2016["y_cadre_etab_adresse_college"]=19;
	$param_bull2016["y_cadre_etab_cp_commune_college"]=23;
	$param_bull2016["y_cadre_etab_tel_college"]=28;
	$param_bull2016["y_cadre_etab_email_college"]=34;

	//+++++++++++++++++

	$param_bull2016["affiche_logo_etab"]=1;
	$param_bull2016["x_logo_etab"]=163;
	$param_bull2016["y_logo_etab"]=5;
	$param_bull2016["largeur_max_logo_etab"]=36.5;
	$param_bull2016["hauteur_max_logo_etab"]=36.5;

	//+++++++++++++++++

	$param_bull2016["x_colonne_cycle"]=111;
	$param_bull2016["y_colonne_cycle"]=5;
	$param_bull2016["cote_carre_cycle"]=11;
	$param_bull2016["ecart_carres_cycle"]=1;

	$param_bull2016["cycles_et_niveaux"][2][0]["texte"]="CP";
	$param_bull2016["cycles_et_niveaux"][2][1]["texte"]="CE1";
	$param_bull2016["cycles_et_niveaux"][2][2]["texte"]="CE2";
	$param_bull2016["cycles_et_niveaux"][3][0]["texte"]="CM1";
	$param_bull2016["cycles_et_niveaux"][3][1]["texte"]="CM2";
	$param_bull2016["cycles_et_niveaux"][3][2]["texte"]="6e";
	$param_bull2016["cycles_et_niveaux"][4][0]["texte"]="5e";
	$param_bull2016["cycles_et_niveaux"][4][1]["texte"]="4e";
	$param_bull2016["cycles_et_niveaux"][4][2]["texte"]="3e";

	$param_bull2016["cycles_et_niveaux"][2][0]["niveau"]="11";
	$param_bull2016["cycles_et_niveaux"][2][1]["niveau"]="10";
	$param_bull2016["cycles_et_niveaux"][2][2]["niveau"]="9";
	$param_bull2016["cycles_et_niveaux"][3][0]["niveau"]="8";
	$param_bull2016["cycles_et_niveaux"][3][1]["niveau"]="7";
	$param_bull2016["cycles_et_niveaux"][3][2]["niveau"]="6";
	$param_bull2016["cycles_et_niveaux"][4][0]["niveau"]="5";
	$param_bull2016["cycles_et_niveaux"][4][1]["niveau"]="4";
	$param_bull2016["cycles_et_niveaux"][4][2]["niveau"]="3";

	//+++++++++++++++++

	$param_bull2016["x_cadre_eleve"]=10;
	$param_bull2016["y_cadre_eleve"]=45;
	$param_bull2016["hauteur_cadre_eleve"]=45;
	$param_bull2016["largeur_cadre_eleve"]=189;

	$param_bull2016["afficher_cadre_adresse_resp"]=getSettingValue("bull2016_afficher_cadre_adresse_resp");
	$param_bull2016["bordure_cadre_adresse_resp"]="LRBT";
	$param_bull2016["x_cadre_adresse_resp"]=117;
	$param_bull2016["y_cadre_adresse_resp"]=$param_bull2016["y_cadre_eleve"]+3;
	$param_bull2016["largeur_cadre_adresse_resp"]=78;
	$param_bull2016["hauteur_cadre_adresse_resp"]=$param_bull2016["hauteur_cadre_eleve"]-2*3;

	$param_bull2016["y_annee_scolaire"]=$param_bull2016["y_cadre_eleve"]+5;
	$param_bull2016["y_periode"]=$param_bull2016["y_annee_scolaire"]+4;

	//$param_bull2016["y_nom_prenom_eleve"]=$param_bull2016["y_periode"]+5;
	$param_bull2016["y_nom_prenom_eleve"]=$param_bull2016["y_cadre_eleve"]+18;
	$param_bull2016["y_naissance_eleve"]=$param_bull2016["y_nom_prenom_eleve"]+5;

	$param_bull2016["y_pp"]=$param_bull2016["y_cadre_eleve"]+32;
	$param_bull2016["y_classe"]=$param_bull2016["y_pp"]+5;

	//+++++++++++++++++

	$param_bull2016["x_bandeau_suivi_acquis"]=3;
	$param_bull2016["y_bandeau_suivi_acquis"]=99;
	$param_bull2016["hauteur_bandeau_suivi_acquis"]=8;
	$param_bull2016["largeur_bandeau_suivi_acquis"]=202.5;

	//+++++++++++++++++

	// Ligne entête tableau des acquis

	$param_bull2016["y_acquis_ligne_entete"]=111.5;
	$param_bull2016["hauteur_acquis_ligne_entete"]=9;

	$param_bull2016["x_acquis_col_1"]=10;
	$param_bull2016["largeur_acquis_col_1"]=44;

	$param_bull2016["x_acquis_col_2"]=$param_bull2016["x_acquis_col_1"]+$param_bull2016["largeur_acquis_col_1"]+0.5;
	$param_bull2016["largeur_acquis_col_2"]=49;

	$param_bull2016["x_acquis_col_3"]=$param_bull2016["x_acquis_col_2"]+$param_bull2016["largeur_acquis_col_2"]+0.5;
	$param_bull2016["largeur_acquis_col_3"]=65;

	$param_bull2016["x_acquis_col_moy"]=$param_bull2016["x_acquis_col_3"]+$param_bull2016["largeur_acquis_col_3"]+0.5;
	$param_bull2016["largeur_acquis_col_moy"]=15;

	$param_bull2016["x_acquis_col_moyclasse"]=$param_bull2016["x_acquis_col_moy"]+$param_bull2016["largeur_acquis_col_moy"]+0.5;
	$param_bull2016["largeur_acquis_col_moyclasse"]=15;


	// Ligne 1 tableau des acquis

	$param_bull2016["y_acquis_ligne_1"]=121.5;
	//$param_bull2016["hauteur_acquis_ligne_1"]=18;

	//========================================
	//a:10
	//b:11
	//c:12
	//d:13
	//e:14
	//f:15

	$param_bull2016["couleur_bandeau_bilan_acquisitions"]["R"]=166;
	$param_bull2016["couleur_bandeau_bilan_acquisitions"]["V"]=13*16+8;
	$param_bull2016["couleur_bandeau_bilan_acquisitions"]["B"]=28;
	//a6d81c
	$param_bull2016["x_bandeau_bilan_acquisitions"]=6;
	//Cycle 3
	$param_bull2016["y_bandeau_bilan_acquisitions_cycle_3"]=91;
	//Cycle 4
	$param_bull2016["y_bandeau_bilan_acquisitions_cycle_4"]=129.5;
	$param_bull2016["largeur_bandeau_bilan_acquisitions"]=198;
	$param_bull2016["hauteur_bandeau_bilan_acquisitions"]=8;

	$param_bull2016["couleur_bilan_acquisitions"]["R"]=14*16+14;
	$param_bull2016["couleur_bilan_acquisitions"]["V"]=15*16+7;
	$param_bull2016["couleur_bilan_acquisitions"]["B"]=12*16+13;
	//eef7cd
	$param_bull2016["x_bilan_acquisitions"]=10;
	//Cycle 3
	$param_bull2016["y_bilan_acquisitions_cycle_3"]=$param_bull2016["y_bandeau_bilan_acquisitions_cycle_3"]+$param_bull2016["hauteur_bandeau_bilan_acquisitions"]+5;
	//Cycle 4
	$param_bull2016["y_bilan_acquisitions_cycle_4"]=$param_bull2016["y_bandeau_bilan_acquisitions_cycle_4"]+$param_bull2016["hauteur_bandeau_bilan_acquisitions"]+5;
	$param_bull2016["largeur_bilan_acquisitions"]=189;
	//Cycle 3
	$param_bull2016["hauteur_bilan_acquisitions_cycle_3"]=83;
	//Cycle 4
	$param_bull2016["hauteur_bilan_acquisitions_cycle_4"]=44;




	$bull2016_affich_mentions=getSettingValue("bull2016_affich_mentions");
	if($bull2016_affich_mentions=="") {
		$bull2016_affich_mentions="y";
	}

	$bull2016_avec_coches_mentions=getSettingValue("bull2016_avec_coches_mentions");
	if($bull2016_avec_coches_mentions=="") {
		$bull2016_avec_coches_mentions="y";
	}

	$bull2016_intitule_mentions=getSettingValue("bull2016_intitule_mentions");
	if($bull2016_intitule_mentions=="") {
		$bull2016_intitule_mentions="y";
	}

	$param_bull2016["affich_mentions"]=$bull2016_affich_mentions;
	$param_bull2016["affich_intitule_mentions"]=$bull2016_intitule_mentions;
	// Si les coches sont choisies, les deux choix précédents ne sont pas pris en compte
	$param_bull2016["avec_coches_mentions"]=$bull2016_avec_coches_mentions;





	$bull2016_arrondi=getSettingValue("bull2016_arrondi");
	if(((!preg_match("/^[0-9]{1,}$/", $bull2016_arrondi))&&
	(!preg_match("/^[0-9]{1,}\.[0-9]{1,}$/", $bull2016_arrondi)))||
	($bull2016_arrondi==0)||
	($bull2016_arrondi=="")) {
		$bull2016_arrondi=0.01;
		//echo "Correction de bull2016_arrondi à $bull2016_arrondi";
	}
	$param_bull2016["bull2016_arrondi"]=$bull2016_arrondi;

	$bull2016_nb_chiffre_virgule=getSettingValue("bull2016_nb_chiffre_virgule");
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_nb_chiffre_virgule))||
	($bull2016_nb_chiffre_virgule=="")) {
		$bull2016_nb_chiffre_virgule=1;
	}
	$param_bull2016["bull2016_nb_chiffre_virgule"]=$bull2016_nb_chiffre_virgule;

	$bull2016_chiffre_avec_zero=getSettingValue("bull2016_chiffre_avec_zero");
	if(($bull2016_chiffre_avec_zero!="0")&&($bull2016_chiffre_avec_zero!="1")) {
		$bull2016_chiffre_avec_zero=0;
	}
	$param_bull2016["bull2016_chiffre_avec_zero"]=$bull2016_chiffre_avec_zero;

	$bull2016_evolution_moyenne_periode_precedente=getSettingValue("bull2016_evolution_moyenne_periode_precedente");
	if($bull2016_evolution_moyenne_periode_precedente=="") {
		$bull2016_evolution_moyenne_periode_precedente="y";
	}
	$param_bull2016["bull2016_evolution_moyenne_periode_precedente"]=$bull2016_evolution_moyenne_periode_precedente;

	$bull2016_evolution_moyenne_periode_precedente_seuil=getSettingValue("bull2016_evolution_moyenne_periode_precedente_seuil");
	if(((!preg_match("/^[0-9]{1,}$/", $bull2016_evolution_moyenne_periode_precedente_seuil))&&
	(!preg_match("/^[0-9]{1,}\.[0-9]{1,}$/", $bull2016_evolution_moyenne_periode_precedente_seuil)))||
	($bull2016_evolution_moyenne_periode_precedente_seuil=="")) {
		$bull2016_evolution_moyenne_periode_precedente_seuil=0;
	}
	$param_bull2016["bull2016_evolution_moyenne_periode_precedente_seuil"]=$bull2016_evolution_moyenne_periode_precedente_seuil;




	//$afficher_nb_heures_perdues="n";
	$param_bull2016["bull2016_afficher_nb_heures_perdues"]="n";

	$bull2016_aff_abs_nj=getSettingValue("bull2016_aff_abs_nj");
	if($bull2016_aff_abs_nj=="") {
		$bull2016_aff_abs_nj="y";
	}
	$param_bull2016["bull2016_aff_abs_nj"]=$bull2016_aff_abs_nj;

	$bull2016_aff_abs_justifiees=getSettingValue("bull2016_aff_abs_justifiees");
	if($bull2016_aff_abs_justifiees=="") {
		$bull2016_aff_abs_justifiees="y";
	}
	$param_bull2016["bull2016_aff_abs_justifiees"]=$bull2016_aff_abs_justifiees;

	$bull2016_aff_total_abs=getSettingValue("bull2016_aff_total_abs");
	if($bull2016_aff_total_abs=="") {
		$bull2016_aff_total_abs="y";
	}
	$param_bull2016["bull2016_aff_total_abs"]=$bull2016_aff_total_abs;

	$bull2016_aff_retards=getSettingValue("bull2016_aff_retards");
	if($bull2016_aff_retards=="") {
		$bull2016_aff_retards="y";
	}
	$param_bull2016["bull2016_aff_retards"]=$bull2016_aff_retards;


	//========================================

	$param_bull2016["couleur_bandeau_communication_famille"]["R"]=15*16+6;
	$param_bull2016["couleur_bandeau_communication_famille"]["V"]=8*16+7;
	$param_bull2016["couleur_bandeau_communication_famille"]["B"]=18;
	//f68712
	$param_bull2016["x_bandeau_communication_famille"]=6;
	$param_bull2016["y_bandeau_communication_famille"]=197;
	$param_bull2016["largeur_bandeau_communication_famille"]=198;
	$param_bull2016["hauteur_bandeau_communication_famille"]=8;

	$param_bull2016["couleur_communication_famille"]["R"]=15*16+12;
	$param_bull2016["couleur_communication_famille"]["V"]=12*16+10;
	$param_bull2016["couleur_communication_famille"]["B"]=8*16+14;
	//fcca8e
	$param_bull2016["x_communication_famille"]=10;
	$param_bull2016["y_communication_famille"]=210;
	$param_bull2016["largeur_communication_famille"]=142;
	$param_bull2016["hauteur_communication_famille"]=49.5;

	$param_bull2016["x_signature_chef"]=$param_bull2016["x_communication_famille"]+$param_bull2016["largeur_communication_famille"]+0.5;
	$param_bull2016["y_signature_chef"]=210;
	$param_bull2016["largeur_signature_chef"]=47.5;
	$param_bull2016["hauteur_signature_chef"]=49.5;

	$param_bull2016["affichage_haut_responsable"]="y";
	//$param_bull2016["affiche_fonction_chef"]="y";
	//$param_bull2016["taille_texte_fonction_chef"]=9;
	$param_bull2016["taille_texte_identite_chef"]=8;

	//$param_bull2016["couleur_visa_famille"]["R"]=15*16+12;
	//$param_bull2016["couleur_visa_famille"]["V"]=12*16+10;
	//$param_bull2016["couleur_visa_famille"]["B"]=8*16+14;
	//fcca8e
	$param_bull2016["x_visa_famille"]=10;
	$param_bull2016["y_visa_famille"]=264.5;
	$param_bull2016["largeur_visa_famille"]=189;
	$param_bull2016["hauteur_visa_famille"]=18;

	//========================================

	$param_bull2016["x_EPI_AP_Parcours"]=10;
	$param_bull2016["y_EPI_AP_Parcours"]=11;
	$param_bull2016["hauteur_ligne_titre_EPI_AP_Parcours"]=7;
	$param_bull2016["largeur_EPI_AP_Parcours"]=189;
	$param_bull2016["espace_vertical_entre_sections_EPI_AP_Parcours"]=5;
	$param_bull2016["espace_vertical_avant_bandeau_Bilan"]=10;

	$param_bull2016["largeur_EPI_AP_Parcours_col_1"]=44;

	$param_bull2016["x_EPI_AP_Parcours_col_2"]=$param_bull2016["x_EPI_AP_Parcours"]+$param_bull2016["largeur_EPI_AP_Parcours_col_1"]+0.5;
	$param_bull2016["largeur_EPI_AP_Parcours_col_2"]=$param_bull2016["largeur_EPI_AP_Parcours"]-$param_bull2016["largeur_EPI_AP_Parcours_col_1"]-0.5;

	// Bandeau EPI
	$param_bull2016["couleur_bandeau_EPI"]["R"]=243;
	$param_bull2016["couleur_bandeau_EPI"]["V"]=249;
	$param_bull2016["couleur_bandeau_EPI"]["B"]=254;
	//f3f9fe

	$param_bull2016["couleur_EPI_alt1"]["R"]=230;
	$param_bull2016["couleur_EPI_alt1"]["V"]=242;
	$param_bull2016["couleur_EPI_alt1"]["B"]=252;
	//e6f2fc

	$param_bull2016["couleur_EPI_alt2"]["R"]=243;
	$param_bull2016["couleur_EPI_alt2"]["V"]=249;
	$param_bull2016["couleur_EPI_alt2"]["B"]=254;
	//f3f9fe

	// Bandeau AP
	$param_bull2016["couleur_bandeau_AP"]["R"]=243;
	$param_bull2016["couleur_bandeau_AP"]["V"]=249;
	$param_bull2016["couleur_bandeau_AP"]["B"]=254;
	//f3f9fe

	$param_bull2016["couleur_AP_alt1"]["R"]=230;
	$param_bull2016["couleur_AP_alt1"]["V"]=242;
	$param_bull2016["couleur_AP_alt1"]["B"]=252;
	//e6f2fc

	$param_bull2016["couleur_AP_alt2"]["R"]=243;
	$param_bull2016["couleur_AP_alt2"]["V"]=249;
	$param_bull2016["couleur_AP_alt2"]["B"]=254;
	//f3f9fe

	// Bandeau Parcours
	$param_bull2016["couleur_bandeau_Parcours"]["R"]=243;
	$param_bull2016["couleur_bandeau_Parcours"]["V"]=249;
	$param_bull2016["couleur_bandeau_Parcours"]["B"]=254;
	//f3f9fe

	$param_bull2016["couleur_Parcours_alt1"]["R"]=230;
	$param_bull2016["couleur_Parcours_alt1"]["V"]=242;
	$param_bull2016["couleur_Parcours_alt1"]["B"]=252;
	//e6f2fc

	$param_bull2016["couleur_Parcours_alt2"]["R"]=243;
	$param_bull2016["couleur_Parcours_alt2"]["V"]=249;
	$param_bull2016["couleur_Parcours_alt2"]["B"]=254;
	//f3f9fe

	//========================================

	//$param_bull2016["bull2016_orientation_periodes"]="2;3";
	$param_bull2016["bull2016_orientation_periodes"]=getSettingValue("bull2016_orientation_periodes");

	$param_bull2016["largeur_cadre_orientation"]=140;
	$param_bull2016["hauteur_cadre_orientation"]=15;

	$param_bull2016["X_cadre_orientation"]=11;
	$param_bull2016["Y_cadre_orientation"]=230;

	$param_bull2016["cadre_voeux_orientation"]=1;
	if(getSettingANon("bull2016_voeux_orientation")) {
		$param_bull2016["cadre_voeux_orientation"]=0;
	}
	//$param_bull2016["X_voeux_orientation"]=5;
	$param_bull2016["X_cadre_voeux_orientation"]=11;

	$param_bull2016["cadre_orientation_proposee"]=1;
	if(getSettingANon("bull2016_orientation_proposee")) {
		$param_bull2016["cadre_orientation_proposee"]=0;
	}
	$param_bull2016["X_cadre_orientation_proposee"]=60;

	$param_bull2016["titre_voeux_orientation"]=getSettingValue("bull2016_titre_voeux_orientation");
	if($param_bull2016["titre_voeux_orientation"]=="") {
		$param_bull2016["titre_voeux_orientation"]="Voeux";
	}

	$param_bull2016["titre_orientation_proposee"]=getSettingValue("bull2016_titre_orientation_proposee");
	if($param_bull2016["titre_orientation_proposee"]=="") {
		$param_bull2016["titre_orientation_proposee"]="Orientation proposée";
	}
	$param_bull2016["titre_avis_orientation_proposee"]=getSettingValue("bull2016_titre_avis_orientation_proposee");
	if($param_bull2016["titre_avis_orientation_proposee"]=="") {
		$param_bull2016["titre_avis_orientation_proposee"]="Commentaire";
	}

	$param_bull2016["bull2016_orientation_taille_police"]="9";

	//========================================

	/*
	$champ_bull_pdf[]="nom_model_bulletin";
	$champ_bull_pdf[]="active_bloc_datation";
	$champ_bull_pdf[]="active_bloc_eleve";
	$champ_bull_pdf[]="active_bloc_adresse_parent";

	$champ_bull_pdf[]="active_bloc_absence";
	$champ_bull_pdf[]="afficher_abs_tot";
	$champ_bull_pdf[]="afficher_abs_nj";
	$champ_bull_pdf[]="afficher_abs_ret";
	$champ_bull_pdf[]="afficher_abs_cpe";

	$champ_bull_pdf[]="active_bloc_note_appreciation";
	$champ_bull_pdf[]="active_bloc_avis_conseil";
	$champ_bull_pdf[]="active_bloc_chef";
	$champ_bull_pdf[]="active_photo";
	$champ_bull_pdf[]="active_coef_moyenne";
	$champ_bull_pdf[]="active_nombre_note";
	$champ_bull_pdf[]="active_nombre_note_case";

	// 20160623
	$champ_bull_pdf[]="active_colonne_Elements_Programmes";
	$champ_bull_pdf[]="largeur_Elements_Programmes";

	$champ_bull_pdf[]="active_moyenne";
	$champ_bull_pdf[]="active_moyenne_eleve";
	$champ_bull_pdf[]="active_moyenne_classe";
	$champ_bull_pdf[]="active_moyenne_min";
	$champ_bull_pdf[]="active_moyenne_max";
	$champ_bull_pdf[]="active_regroupement_cote";
	$champ_bull_pdf[]="active_entete_regroupement";
	$champ_bull_pdf[]="active_moyenne_regroupement";

	$champ_bull_pdf[]="presentation_proflist";

	$champ_bull_pdf[]="active_rang";
	$champ_bull_pdf[]="active_graphique_niveau";
	$champ_bull_pdf[]="active_appreciation";
	$champ_bull_pdf[]="affiche_doublement";
	$champ_bull_pdf[]="affiche_date_naissance";
	$champ_bull_pdf[]="affiche_lieu_naissance";
	$champ_bull_pdf[]="affiche_dp";
	$champ_bull_pdf[]="affiche_nom_court";
	$champ_bull_pdf[]="affiche_effectif_classe";
	$champ_bull_pdf[]="affiche_numero_impression";
	$champ_bull_pdf[]="caractere_utilse";
	$champ_bull_pdf[]="X_parent";
	$champ_bull_pdf[]="Y_parent";
	$champ_bull_pdf[]="X_eleve";
	$champ_bull_pdf[]="Y_eleve";
	$champ_bull_pdf[]="cadre_eleve";
	$champ_bull_pdf[]="X_datation_bul";
	$champ_bull_pdf[]="Y_datation_bul";
	$champ_bull_pdf[]="cadre_datation_bul";
	$champ_bull_pdf[]="hauteur_info_categorie";
	$champ_bull_pdf[]="X_note_app";
	$champ_bull_pdf[]="Y_note_app";
	$champ_bull_pdf[]="longeur_note_app";
	$champ_bull_pdf[]="hauteur_note_app";
	$champ_bull_pdf[]="largeur_coef_moyenne";
	$champ_bull_pdf[]="largeur_nombre_note";
	$champ_bull_pdf[]="largeur_d_une_moyenne";
	$champ_bull_pdf[]="largeur_niveau";
	$champ_bull_pdf[]="largeur_rang";

	$champ_bull_pdf[]="X_absence";
	$champ_bull_pdf[]="Y_absence";
	$champ_bull_pdf[]="largeur_cadre_absences";

	$champ_bull_pdf[]="hauteur_entete_moyenne_general";

	$champ_bull_pdf[]="X_avis_cons";
	$champ_bull_pdf[]="Y_avis_cons";
	$champ_bull_pdf[]="longeur_avis_cons";
	$champ_bull_pdf[]="hauteur_avis_cons";
	$champ_bull_pdf[]="cadre_avis_cons";
	$champ_bull_pdf[]="affich_mentions";
	$champ_bull_pdf[]="affich_intitule_mentions";
	$champ_bull_pdf[]="affich_coches_mentions";

	$champ_bull_pdf[]="X_sign_chef";
	$champ_bull_pdf[]="Y_sign_chef";
	$champ_bull_pdf[]="longeur_sign_chef";
	$champ_bull_pdf[]="hauteur_sign_chef";
	$champ_bull_pdf[]="cadre_sign_chef";
	$champ_bull_pdf[]="affiche_filigrame";
	$champ_bull_pdf[]="texte_filigrame";
	$champ_bull_pdf[]="affiche_logo_etab";
	$champ_bull_pdf[]="entente_mel";
	$champ_bull_pdf[]="entente_tel";
	$champ_bull_pdf[]="entente_fax";

	$champ_bull_pdf[]="entete_info_etab_suppl";
	$champ_bull_pdf[]="entete_info_etab_suppl_texte";
	$champ_bull_pdf[]="entete_info_etab_suppl_valeur";

	$champ_bull_pdf[]="L_max_logo";
	$champ_bull_pdf[]="H_max_logo";
	$champ_bull_pdf[]="toute_moyenne_meme_col";
	$champ_bull_pdf[]="moyennes_periodes_precedentes";
	$champ_bull_pdf[]="evolution_moyenne_periode_precedente";
	$champ_bull_pdf[]="evolution_moyenne_periode_precedente_seuil";

	$champ_bull_pdf[]="moyennes_annee";

	$champ_bull_pdf[]="active_reperage_eleve";
	$champ_bull_pdf[]="couleur_reperage_eleve1";
	$champ_bull_pdf[]="couleur_reperage_eleve2";
	$champ_bull_pdf[]="couleur_reperage_eleve3";
	$champ_bull_pdf[]="couleur_categorie_entete";
	$champ_bull_pdf[]="couleur_categorie_entete1";
	$champ_bull_pdf[]="couleur_categorie_entete2";
	$champ_bull_pdf[]="couleur_categorie_entete3";
	$champ_bull_pdf[]="couleur_categorie_cote";
	$champ_bull_pdf[]="couleur_categorie_cote1";
	$champ_bull_pdf[]="couleur_categorie_cote2";
	$champ_bull_pdf[]="couleur_categorie_cote3";
	$champ_bull_pdf[]="couleur_moy_general";
	$champ_bull_pdf[]="couleur_moy_general1";
	$champ_bull_pdf[]="couleur_moy_general2";
	$champ_bull_pdf[]="couleur_moy_general3";
	$champ_bull_pdf[]="titre_entete_matiere";
	$champ_bull_pdf[]="titre_entete_coef";
	$champ_bull_pdf[]="titre_entete_nbnote";
	$champ_bull_pdf[]="titre_entete_rang";
	$champ_bull_pdf[]="titre_entete_appreciation";
	$champ_bull_pdf[]="active_coef_sousmoyene";
	$champ_bull_pdf[]="arrondie_choix";
	$champ_bull_pdf[]="nb_chiffre_virgule";
	$champ_bull_pdf[]="chiffre_avec_zero";
	$champ_bull_pdf[]="autorise_sous_matiere";
	$champ_bull_pdf[]="affichage_haut_responsable";
	$champ_bull_pdf[]="entete_model_bulletin";
	$champ_bull_pdf[]="ordre_entete_model_bulletin";
	$champ_bull_pdf[]="affiche_etab_origine";
	$champ_bull_pdf[]="imprime_pour";
	$champ_bull_pdf[]="largeur_matiere";
	$champ_bull_pdf[]="nom_etab_gras";
	$champ_bull_pdf[]="taille_texte_date_edition";
	$champ_bull_pdf[]="taille_texte_matiere";
	$champ_bull_pdf[]="active_moyenne_general";
	$champ_bull_pdf[]="titre_bloc_avis_conseil";
	$champ_bull_pdf[]="taille_titre_bloc_avis_conseil";
	$champ_bull_pdf[]="taille_profprincipal_bloc_avis_conseil";
	$champ_bull_pdf[]="afficher_tous_profprincipaux";
	$champ_bull_pdf[]="affiche_fonction_chef";
	$champ_bull_pdf[]="taille_texte_fonction_chef";
	$champ_bull_pdf[]="taille_texte_identitee_chef";
	$champ_bull_pdf[]="tel_image";
	$champ_bull_pdf[]="tel_texte";
	$champ_bull_pdf[]="fax_image";
	$champ_bull_pdf[]="fax_texte";
	$champ_bull_pdf[]="courrier_image";
	$champ_bull_pdf[]="courrier_texte";
	$champ_bull_pdf[]="largeur_bloc_eleve";
	$champ_bull_pdf[]="hauteur_bloc_eleve";
	$champ_bull_pdf[]="largeur_bloc_adresse";
	$champ_bull_pdf[]="hauteur_bloc_adresse";
	$champ_bull_pdf[]="largeur_bloc_datation";
	$champ_bull_pdf[]="hauteur_bloc_datation";
	$champ_bull_pdf[]="taille_texte_classe";
	$champ_bull_pdf[]="type_texte_classe";
	$champ_bull_pdf[]="taille_texte_annee";
	$champ_bull_pdf[]="type_texte_annee";
	$champ_bull_pdf[]="taille_texte_periode";
	$champ_bull_pdf[]="type_texte_periode";
	$champ_bull_pdf[]="taille_texte_categorie_cote";
	$champ_bull_pdf[]="taille_texte_categorie";
	$champ_bull_pdf[]="type_texte_date_datation";
	$champ_bull_pdf[]="cadre_adresse";
	$champ_bull_pdf[]="centrage_logo";
	$champ_bull_pdf[]="Y_centre_logo";
	$champ_bull_pdf[]="ajout_cadre_blanc_photo";
	$champ_bull_pdf[]="affiche_moyenne_mini_general";
	$champ_bull_pdf[]="affiche_moyenne_maxi_general";

	$champ_bull_pdf[]="affiche_totalpoints_sur_totalcoefs";

	$champ_bull_pdf[]="affiche_date_edition";
	$champ_bull_pdf[]="affiche_ine";

	$champ_bull_pdf[]="affiche_moyenne_general_coef_1";
	
	$champ_bull_pdf[]="affiche_numero_responsable";

	//=========================
	// AJOUT: boireaus 20081224
	$champ_bull_pdf[]="affiche_nom_etab";
	$champ_bull_pdf[]="affiche_adresse_etab";
	//=========================

	//$champ_bull_pdf[]="signature_img";

	//$champ_bull_pdf[]="adresse_resp_fontsize_ligne_1";
	$champ_bull_pdf[]="adresse_resp_fontsize";

	$champ_bull_pdf[]="cell_ajustee_texte_matiere";
	$champ_bull_pdf[]="cell_ajustee_texte_matiere_ratio_min_max";

	//=========================
	// AJOUT: 20160409
	//$champ_bull_pdf[]="active_bloc_orientation";
	$champ_bull_pdf[]="orientation_periodes";

	$champ_bull_pdf[]="largeur_cadre_orientation";
	$champ_bull_pdf[]="hauteur_cadre_orientation";
	$champ_bull_pdf[]="X_cadre_orientation";
	$champ_bull_pdf[]="Y_cadre_orientation";

	$champ_bull_pdf[]="cadre_voeux_orientation";
	$champ_bull_pdf[]="X_voeux_orientation";
	
	$champ_bull_pdf[]="cadre_orientation_proposee";
	$champ_bull_pdf[]="X_cadre_orientation_proposee";

	$champ_bull_pdf[]="titre_voeux_orientation";

	$champ_bull_pdf[]="titre_orientation_proposee";
	$champ_bull_pdf[]="titre_avis_orientation_proposee";
	//=========================

	//$champ_bull_pdf[]="ligne_commentaire_orientation";
	//=========================
	*/

	/*
		mysql> show fields from modele_bulletin;
		+--------------------+--------------+------+-----+---------+-------+
		| Field              | Type         | Null | Key | Default | Extra |
		+--------------------+--------------+------+-----+---------+-------+
		| id_modele_bulletin | int(11)      | NO   |     |         |       |
		| nom                | varchar(255) | NO   |     |         |       |
		| valeur             | varchar(255) | NO   |     |         |       |
		+--------------------+--------------+------+-----+---------+-------+
		3 rows in set (0.00 sec)

		mysql> select max(id_modele_bulletin) as max_id_modele_bulletin from modele_bulletin;
		+------------------------+
		| max_id_modele_bulletin |
		+------------------------+
		|                      3 |
		+------------------------+
		1 row in set (0.05 sec)

		mysql>
	*/

	/*
	$val_defaut_champ_bull_pdf["nom_model_bulletin"]="Nom du modèle";
	$val_defaut_champ_bull_pdf["active_bloc_datation"]=1;
	$val_defaut_champ_bull_pdf["active_bloc_eleve"]=1;
	$val_defaut_champ_bull_pdf["active_bloc_adresse_parent"]=1;

	$val_defaut_champ_bull_pdf["active_bloc_absence"]=1;
	$val_defaut_champ_bull_pdf["afficher_abs_tot"]=1;
	$val_defaut_champ_bull_pdf["afficher_abs_nj"]=1;
	$val_defaut_champ_bull_pdf["afficher_abs_ret"]=1;
	$val_defaut_champ_bull_pdf["afficher_abs_cpe"]=1;

	$val_defaut_champ_bull_pdf["active_bloc_note_appreciation"]=1;
	$val_defaut_champ_bull_pdf["active_bloc_avis_conseil"]=1;
	$val_defaut_champ_bull_pdf["active_bloc_chef"]=1;
	$val_defaut_champ_bull_pdf["active_photo"]=0;
	$val_defaut_champ_bull_pdf["active_coef_moyenne"]=0;
	$val_defaut_champ_bull_pdf["active_nombre_note"]=0;
	$val_defaut_champ_bull_pdf["active_nombre_note_case"]=0;

	// 20160623
	$val_defaut_champ_bull_pdf["active_colonne_Elements_Programmes"]=1;
	$val_defaut_champ_bull_pdf["largeur_Elements_Programmes"]=50;

	$val_defaut_champ_bull_pdf["active_moyenne"]=1;
	$val_defaut_champ_bull_pdf["active_moyenne_eleve"]=1;
	$val_defaut_champ_bull_pdf["active_moyenne_classe"]=1;
	$val_defaut_champ_bull_pdf["active_moyenne_min"]=1;
	$val_defaut_champ_bull_pdf["active_moyenne_max"]=1;
	$val_defaut_champ_bull_pdf["active_regroupement_cote"]=0;
	$val_defaut_champ_bull_pdf["active_entete_regroupement"]=0;
	$val_defaut_champ_bull_pdf["active_moyenne_regroupement"]=0;

	$val_defaut_champ_bull_pdf["presentation_proflist"]=1;

	$val_defaut_champ_bull_pdf["active_rang"]=0;
	$val_defaut_champ_bull_pdf["active_graphique_niveau"]=0;
	$val_defaut_champ_bull_pdf["active_appreciation"]=1;
	$val_defaut_champ_bull_pdf["affiche_doublement"]=1;
	$val_defaut_champ_bull_pdf["affiche_date_naissance"]=1;
	$val_defaut_champ_bull_pdf["affiche_lieu_naissance"]=0;
	$val_defaut_champ_bull_pdf["affiche_dp"]=1;
	$val_defaut_champ_bull_pdf["affiche_nom_court"]=0;
	$val_defaut_champ_bull_pdf["affiche_effectif_classe"]=0;
	$val_defaut_champ_bull_pdf["affiche_numero_impression"]=0;
	$val_defaut_champ_bull_pdf["caractere_utilse"]="DejaVu";
	$val_defaut_champ_bull_pdf["X_parent"]=110;
	$val_defaut_champ_bull_pdf["Y_parent"]=40;
	$val_defaut_champ_bull_pdf["X_eleve"]=5;
	$val_defaut_champ_bull_pdf["Y_eleve"]=40;
	$val_defaut_champ_bull_pdf["cadre_eleve"]=1;
	$val_defaut_champ_bull_pdf["X_datation_bul"]=110;
	$val_defaut_champ_bull_pdf["Y_datation_bul"]=5;
	$val_defaut_champ_bull_pdf["cadre_datation_bul"]=1;
	$val_defaut_champ_bull_pdf["hauteur_info_categorie"]=5;
	$val_defaut_champ_bull_pdf["X_note_app"]=5;
	$val_defaut_champ_bull_pdf["Y_note_app"]=72;
	$val_defaut_champ_bull_pdf["longeur_note_app"]=200;
	$val_defaut_champ_bull_pdf["hauteur_note_app"]=175;
	$val_defaut_champ_bull_pdf["largeur_coef_moyenne"]=8;
	$val_defaut_champ_bull_pdf["largeur_nombre_note"]=8;
	$val_defaut_champ_bull_pdf["largeur_d_une_moyenne"]=10;
	$val_defaut_champ_bull_pdf["largeur_niveau"]=18;
	$val_defaut_champ_bull_pdf["largeur_rang"]=5;

	$val_defaut_champ_bull_pdf["X_absence"]=5;
	$val_defaut_champ_bull_pdf["Y_absence"]=246.3;
	$val_defaut_champ_bull_pdf["largeur_cadre_absences"]=200;

	$val_defaut_champ_bull_pdf["hauteur_entete_moyenne_general"]=5;

	$val_defaut_champ_bull_pdf["X_avis_cons"]=5;
	$val_defaut_champ_bull_pdf["Y_avis_cons"]=250;
	$val_defaut_champ_bull_pdf["longeur_avis_cons"]=130;
	$val_defaut_champ_bull_pdf["hauteur_avis_cons"]=37;
	$val_defaut_champ_bull_pdf["cadre_avis_cons"]=1;
	$val_defaut_champ_bull_pdf["affich_mentions"]='y';
	$val_defaut_champ_bull_pdf["affich_intitule_mentions"]='y';
	$val_defaut_champ_bull_pdf["affich_coches_mentions"]='y';

	$val_defaut_champ_bull_pdf["X_sign_chef"]=138;
	$val_defaut_champ_bull_pdf["Y_sign_chef"]=250;
	$val_defaut_champ_bull_pdf["longeur_sign_chef"]=67;
	$val_defaut_champ_bull_pdf["hauteur_sign_chef"]=37;
	$val_defaut_champ_bull_pdf["cadre_sign_chef"]=0;
	$val_defaut_champ_bull_pdf["affiche_filigrame"]=1;
	$val_defaut_champ_bull_pdf["texte_filigrame"]="DUPLICATA INTERNET";
	$val_defaut_champ_bull_pdf["affiche_logo_etab"]=1;
	$val_defaut_champ_bull_pdf["entente_mel"]=1;
	$val_defaut_champ_bull_pdf["entente_tel"]=1;
	$val_defaut_champ_bull_pdf["entente_fax"]=1;

	$val_defaut_champ_bull_pdf["entete_info_etab_suppl"]='n';
	$val_defaut_champ_bull_pdf["entete_info_etab_suppl_texte"]='Site web';
	$val_defaut_champ_bull_pdf["entete_info_etab_suppl_valeur"]='http://';

	$val_defaut_champ_bull_pdf["L_max_logo"]=75;
	$val_defaut_champ_bull_pdf["H_max_logo"]=75;
	$val_defaut_champ_bull_pdf["toute_moyenne_meme_col"]=0;
	$val_defaut_champ_bull_pdf["moyennes_periodes_precedentes"]='n';
	$val_defaut_champ_bull_pdf["evolution_moyenne_periode_precedente"]='n';
	$val_defaut_champ_bull_pdf["evolution_moyenne_periode_precedente_seuil"]=0;

	$val_defaut_champ_bull_pdf["moyennes_annee"]='n';

	$val_defaut_champ_bull_pdf["active_reperage_eleve"]=1;
	$val_defaut_champ_bull_pdf["couleur_reperage_eleve1"]=255;
	$val_defaut_champ_bull_pdf["couleur_reperage_eleve2"]=255;
	$val_defaut_champ_bull_pdf["couleur_reperage_eleve3"]=207;
	$val_defaut_champ_bull_pdf["couleur_categorie_entete"]=1;
	$val_defaut_champ_bull_pdf["couleur_categorie_entete1"]=239;
	$val_defaut_champ_bull_pdf["couleur_categorie_entete2"]=239;
	$val_defaut_champ_bull_pdf["couleur_categorie_entete3"]=239;
	$val_defaut_champ_bull_pdf["couleur_categorie_cote"]=1;
	$val_defaut_champ_bull_pdf["couleur_categorie_cote1"]=239;
	$val_defaut_champ_bull_pdf["couleur_categorie_cote2"]=239;
	$val_defaut_champ_bull_pdf["couleur_categorie_cote3"]=239;
	$val_defaut_champ_bull_pdf["couleur_moy_general"]=1;
	$val_defaut_champ_bull_pdf["couleur_moy_general1"]=239;
	$val_defaut_champ_bull_pdf["couleur_moy_general2"]=239;
	$val_defaut_champ_bull_pdf["couleur_moy_general3"]=239;
	$val_defaut_champ_bull_pdf["titre_entete_matiere"]="Matière";
	$val_defaut_champ_bull_pdf["titre_entete_coef"]="coef.";
	$val_defaut_champ_bull_pdf["titre_entete_nbnote"]="nb. n.";
	$val_defaut_champ_bull_pdf["titre_entete_rang"]="rang";
	$val_defaut_champ_bull_pdf["titre_entete_appreciation"]="Appréciation / Conseils";
	$val_defaut_champ_bull_pdf["active_coef_sousmoyene"]=0;
	$val_defaut_champ_bull_pdf["arrondie_choix"]=0.01;
	$val_defaut_champ_bull_pdf["nb_chiffre_virgule"]=2;
	$val_defaut_champ_bull_pdf["chiffre_avec_zero"]=0;
	$val_defaut_champ_bull_pdf["autorise_sous_matiere"]=1;
	$val_defaut_champ_bull_pdf["affichage_haut_responsable"]=1;
	$val_defaut_champ_bull_pdf["entete_model_bulletin"]=1;
	$val_defaut_champ_bull_pdf["ordre_entete_model_bulletin"]=1;
	$val_defaut_champ_bull_pdf["affiche_etab_origine"]=0;
	$val_defaut_champ_bull_pdf["imprime_pour"]=1;
	$val_defaut_champ_bull_pdf["largeur_matiere"]=40;
	$val_defaut_champ_bull_pdf["nom_etab_gras"]=0;
	$val_defaut_champ_bull_pdf["taille_texte_date_edition"]=0;
	$val_defaut_champ_bull_pdf["taille_texte_matiere"]=0;
	$val_defaut_champ_bull_pdf["active_moyenne_general"]=1;
	$val_defaut_champ_bull_pdf["titre_bloc_avis_conseil"]="";
	$val_defaut_champ_bull_pdf["taille_titre_bloc_avis_conseil"]=0;
	$val_defaut_champ_bull_pdf["taille_profprincipal_bloc_avis_conseil"]=0;
	$val_defaut_champ_bull_pdf["afficher_tous_profprincipaux"]=0;
	$val_defaut_champ_bull_pdf["affiche_fonction_chef"]=0;
	$val_defaut_champ_bull_pdf["taille_texte_fonction_chef"]=0;
	$val_defaut_champ_bull_pdf["taille_texte_identitee_chef"]=0;
	$val_defaut_champ_bull_pdf["tel_image"]="";
	$val_defaut_champ_bull_pdf["tel_texte"]="";
	$val_defaut_champ_bull_pdf["fax_image"]="";
	$val_defaut_champ_bull_pdf["fax_texte"]="";
	$val_defaut_champ_bull_pdf["courrier_image"]="";
	$val_defaut_champ_bull_pdf["courrier_texte"]="";
	$val_defaut_champ_bull_pdf["largeur_bloc_eleve"]=0;
	$val_defaut_champ_bull_pdf["hauteur_bloc_eleve"]=0;
	$val_defaut_champ_bull_pdf["largeur_bloc_adresse"]=0;
	$val_defaut_champ_bull_pdf["hauteur_bloc_adresse"]=0;
	$val_defaut_champ_bull_pdf["largeur_bloc_datation"]=0;
	$val_defaut_champ_bull_pdf["hauteur_bloc_datation"]=0;
	$val_defaut_champ_bull_pdf["taille_texte_classe"]=0;
	$val_defaut_champ_bull_pdf["type_texte_classe"]="";
	$val_defaut_champ_bull_pdf["taille_texte_annee"]=0;
	$val_defaut_champ_bull_pdf["type_texte_annee"]="";
	$val_defaut_champ_bull_pdf["taille_texte_periode"]=0;
	$val_defaut_champ_bull_pdf["type_texte_periode"]="";
	$val_defaut_champ_bull_pdf["taille_texte_categorie_cote"]=0;
	$val_defaut_champ_bull_pdf["taille_texte_categorie"]=0;
	$val_defaut_champ_bull_pdf["type_texte_date_datation"]="";
	$val_defaut_champ_bull_pdf["cadre_adresse"]=0;
	$val_defaut_champ_bull_pdf["centrage_logo"]=0;
	$val_defaut_champ_bull_pdf["Y_centre_logo"]=18;
	$val_defaut_champ_bull_pdf["ajout_cadre_blanc_photo"]=0;
	$val_defaut_champ_bull_pdf["affiche_moyenne_mini_general"]=1;
	$val_defaut_champ_bull_pdf["affiche_moyenne_maxi_general"]=1;

	$val_defaut_champ_bull_pdf["affiche_totalpoints_sur_totalcoefs"]=0;

	$val_defaut_champ_bull_pdf["affiche_date_edition"]=1;
	$val_defaut_champ_bull_pdf["affiche_ine"]=0;

	$val_defaut_champ_bull_pdf["affiche_moyenne_general_coef_1"]=0;
	
	$val_defaut_champ_bull_pdf["affiche_numero_responsable"]=0;

	//=========================
	// AJOUT: boireaus 20081224
	$val_defaut_champ_bull_pdf["affiche_nom_etab"]=1;
	$val_defaut_champ_bull_pdf["affiche_adresse_etab"]=1;
	//=========================

	//$val_defaut_champ_bull_pdf["signature_img"]=0;

	//$val_defaut_champ_bull_pdf["adresse_resp_fontsize_ligne_1"]=12;
	$val_defaut_champ_bull_pdf["adresse_resp_fontsize"]=10;

	$val_defaut_champ_bull_pdf["cell_ajustee_texte_matiere"]=0;
	$val_defaut_champ_bull_pdf["cell_ajustee_texte_matiere_ratio_min_max"]=3;

	//=========================
	// AJOUT: 20160409
	//$val_defaut_champ_bull_pdf["active_bloc_orientation"]=0;
	$val_defaut_champ_bull_pdf["orientation_periodes"]="";

	$val_defaut_champ_bull_pdf["largeur_cadre_orientation"]=200;
	$val_defaut_champ_bull_pdf["hauteur_cadre_orientation"]=15;
	$val_defaut_champ_bull_pdf["X_cadre_orientation"]=5;
	$val_defaut_champ_bull_pdf["Y_cadre_orientation"]=250;

	$val_defaut_champ_bull_pdf["cadre_voeux_orientation"]=1;
	$val_defaut_champ_bull_pdf["X_cadre_voeux_orientation"]=5;
	$val_defaut_champ_bull_pdf["titre_voeux_orientation"]="Voeux";

	$val_defaut_champ_bull_pdf["cadre_orientation_proposee"]=1;
	$val_defaut_champ_bull_pdf["X_cadre_orientation_proposee"]=90;
	$val_defaut_champ_bull_pdf["titre_orientation_proposee"]="Orientation proposée";
	$val_defaut_champ_bull_pdf["titre_avis_orientation_proposee"]="Commentaire";

	//$val_defaut_champ_bull_pdf["ligne_commentaire_orientation"]=1;
	//$val_defaut_champ_bull_pdf["X_ligne_commentaire_orientation"]=60;
	//=========================

	for($loop_champs=0;$loop_champs<count($champ_bull_pdf);$loop_champs++) {
		$type_champ_pdf["$champ_bull_pdf[$loop_champs]"]="numerique";
	}
	// Liste des champs non numériques
	$type_champ_pdf["nom_model_bulletin"]="texte";
	$type_champ_pdf["caractere_utilse"]="texte";
	$type_champ_pdf["texte_filigrame"]="texte";
	$type_champ_pdf["titre_entete_matiere"]="texte";
	$type_champ_pdf["titre_entete_coef"]="texte";
	$type_champ_pdf["titre_entete_nbnote"]="texte";
	$type_champ_pdf["titre_entete_rang"]="texte";
	$type_champ_pdf["titre_entete_appreciation"]="texte";
	$type_champ_pdf["titre_bloc_avis_conseil"]="texte";
	$type_champ_pdf["tel_image"]="texte";
	$type_champ_pdf["tel_texte"]="texte";
	$type_champ_pdf["fax_image"]="texte";
	$type_champ_pdf["fax_texte"]="texte";
	$type_champ_pdf["courrier_image"]="texte";
	$type_champ_pdf["courrier_texte"]="texte";
	$type_champ_pdf["type_texte_classe"]="texte";
	$type_champ_pdf["type_texte_annee"]="texte";
	$type_champ_pdf["type_texte_periode"]="texte";
	$type_champ_pdf["type_texte_date_datation"]="texte";

	// 20160409
	$type_champ_pdf["orientation_periodes"]="texte";
	$type_champ_pdf["titre_voeux_orientation"]="texte";
	$type_champ_pdf["titre_orientation_proposee"]="texte";
	$type_champ_pdf["titre_avis_orientation_proposee"]="texte";


	function get_max_id_model_bulletin() {
		$sql="SELECT MAX(id_model_bulletin) AS max_id_model_bulletin FROM modele_bulletin;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			// Ca ne devrait pas arriver: en 1 il doit y avoir le modèle standard
			return 2;
		}
		else {
			$lig=mysqli_fetch_object($res);
			return $lig->max_id_model_bulletin;
		}
	}
	*/
?>
