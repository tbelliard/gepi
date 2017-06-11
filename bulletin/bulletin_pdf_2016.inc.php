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

	$param_bull2016["bull2016_cadre_visa_famille"]=getSettingValue("bull2016_cadre_visa_famille");
	if(($param_bull2016["bull2016_cadre_visa_famille"]!="y")&&($param_bull2016["bull2016_cadre_visa_famille"]!="n")) {
		$param_bull2016["bull2016_cadre_visa_famille"]="y";
	}

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

	$param_bull2016["x_cadre_eleve_app_classe"]=10;
	$param_bull2016["y_cadre_eleve_app_classe"]=45;
	$param_bull2016["hauteur_cadre_eleve_app_classe"]=25;
	$param_bull2016["largeur_cadre_eleve_app_classe"]=189;

	$param_bull2016["y_annee_scolaire_app_classe"]=$param_bull2016["y_cadre_eleve_app_classe"]+2;
	$param_bull2016["y_periode_app_classe"]=$param_bull2016["y_annee_scolaire_app_classe"]+4;

	$param_bull2016["y_pp_app_classe"]=$param_bull2016["y_cadre_eleve_app_classe"]+12;
	$param_bull2016["y_classe_app_classe"]=$param_bull2016["y_pp_app_classe"]+5;

	//+++++++++++++++++

	$param_bull2016["x_bandeau_suivi_acquis"]=3;
	$param_bull2016["y_bandeau_suivi_acquis"]=99;
	$param_bull2016["hauteur_bandeau_suivi_acquis"]=8;
	$param_bull2016["largeur_bandeau_suivi_acquis"]=202.5;

	//+++++++++++++++++

	//$param_bull2016["y_bandeau_suivi_acquis_app_classe"]=79;
	$param_bull2016["y_bandeau_suivi_acquis_app_classe"]=$param_bull2016["y_cadre_eleve_app_classe"]+$param_bull2016["hauteur_cadre_eleve_app_classe"]+5;
	//$param_bull2016["y_acquis_ligne_entete_app_classe"]=91.5;
	$param_bull2016["y_acquis_ligne_entete_app_classe"]=$param_bull2016["y_bandeau_suivi_acquis_app_classe"]+12.5;

	//+++++++++++++++++

	// Ligne entête tableau des acquis

	$param_bull2016["y_acquis_ligne_entete"]=111.5;
	$param_bull2016["hauteur_acquis_ligne_entete"]=9;

	// Colonne Noms de matières
	$param_bull2016["x_acquis_col_1"]=10;
	//$param_bull2016["largeur_acquis_col_1"]=44;
	$bull2016_largeur_acquis_col_1=getSettingValue('bull2016_largeur_acquis_col_1');
	if($bull2016_largeur_acquis_col_1=="") {
		$bull2016_largeur_acquis_col_1=44;
	}
	elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_acquis_col_1)) {
		$bull2016_largeur_acquis_col_1=44;
	}
	$param_bull2016["largeur_acquis_col_1"]=$bull2016_largeur_acquis_col_1;


	// Colonne Éléments de programmes
	$param_bull2016["x_acquis_col_2"]=$param_bull2016["x_acquis_col_1"]+$param_bull2016["largeur_acquis_col_1"]+0.5;
	//$param_bull2016["largeur_acquis_col_2"]=49;
	$bull2016_largeur_acquis_col_2=getSettingValue('bull2016_largeur_acquis_col_2');
	if($bull2016_largeur_acquis_col_2=="") {
		$bull2016_largeur_acquis_col_2=49;
	}
	elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_acquis_col_2)) {
		$bull2016_largeur_acquis_col_2=49;
	}
	$param_bull2016["largeur_acquis_col_2"]=$bull2016_largeur_acquis_col_2;


	// Colonne Appréciations: Abscisse (la largeur est calculée plus loin)
	$param_bull2016["x_acquis_col_3"]=$param_bull2016["x_acquis_col_2"]+$param_bull2016["largeur_acquis_col_2"]+0.5;
	//$param_bull2016["largeur_acquis_col_3"]=65;


	// Colonne Moyenne élève: Largeur (abscisse calculée plus loin)
	$bull2016_largeur_acquis_col_moy=getSettingValue('bull2016_largeur_acquis_col_moy');
	if($bull2016_largeur_acquis_col_moy=="") {
		$bull2016_largeur_acquis_col_moy=15;
	}
	elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_acquis_col_moy)) {
		$bull2016_largeur_acquis_col_moy=15;
	}
	$param_bull2016["largeur_acquis_col_moy"]=$bull2016_largeur_acquis_col_moy;


	// Colonne Moyenne classe: Largeur (abscisse calculée plus loin)
	$bull2016_largeur_acquis_col_moyclasse=getSettingValue('bull2016_largeur_acquis_col_moyclasse');
	if($bull2016_largeur_acquis_col_moyclasse=="") {
		$bull2016_largeur_acquis_col_moyclasse=15;
	}
	elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_acquis_col_moyclasse)) {
		$bull2016_largeur_acquis_col_moyclasse=15;
	}
	$param_bull2016["largeur_acquis_col_moyclasse"]=$bull2016_largeur_acquis_col_moyclasse;


	// Colonne Appréciations: Largeur
	$param_bull2016["largeur_acquis_col_3"]=189-$param_bull2016["largeur_acquis_col_1"]-$param_bull2016["largeur_acquis_col_2"]-$param_bull2016["largeur_acquis_col_moy"]-$param_bull2016["largeur_acquis_col_moyclasse"];


	// Colonne Moyenne élève: Abscisse
	$param_bull2016["x_acquis_col_moy"]=$param_bull2016["x_acquis_col_3"]+$param_bull2016["largeur_acquis_col_3"]+0.5;

	// Colonne Moyenne classe: Abscisse
	$param_bull2016["x_acquis_col_moyclasse"]=$param_bull2016["x_acquis_col_moy"]+$param_bull2016["largeur_acquis_col_moy"]+0.5;


	//========================================

	$param_bull2016["x_acquis_col_appreciation_app_classe"]=$param_bull2016["x_acquis_col_2"];
	$param_bull2016["x_acquis_col_moyclasse_app_classe"]=$param_bull2016["x_acquis_col_moyclasse"];
	$param_bull2016["largeur_acquis_col_appreciation_app_classe"]=189-$param_bull2016["largeur_acquis_col_1"]-$param_bull2016["largeur_acquis_col_moyclasse"];

	//========================================

	// Ligne 1 tableau des acquis

	$param_bull2016["y_acquis_ligne_1"]=121.5;
	//$param_bull2016["hauteur_acquis_ligne_1"]=18;

	//========================================

	// Hauteur Cadre Bilan des acquisitions en cycle 3
	$bull2016_hauteur_bilan_acquisitions_cycle_3=getSettingValue("bull2016_hauteur_bilan_acquisitions_cycle_3");
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_hauteur_bilan_acquisitions_cycle_3))||
	($bull2016_hauteur_bilan_acquisitions_cycle_3=="")) {
		$bull2016_hauteur_bilan_acquisitions_cycle_3=83;
	}

	// Hauteur Cadre Bilan des acquisitions en cycle 4
	$bull2016_hauteur_bilan_acquisitions_cycle_4=getSettingValue("bull2016_hauteur_bilan_acquisitions_cycle_4");
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_hauteur_bilan_acquisitions_cycle_4))||
	($bull2016_hauteur_bilan_acquisitions_cycle_4=="")) {
		$bull2016_hauteur_bilan_acquisitions_cycle_4=44;
	}

	// Hauteur Cadre Communication avec la famille
	$bull2016_hauteur_communication_famille=getSettingValue("bull2016_hauteur_communication_famille");
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_hauteur_communication_famille))||
	($bull2016_hauteur_communication_famille=="")) {
		$bull2016_hauteur_communication_famille=49.5;
	}

	// Hauteur Cadre Visa de la famille
	$bull2016_hauteur_visa_famille=getSettingValue("bull2016_hauteur_visa_famille");
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_hauteur_visa_famille))||
	($bull2016_hauteur_visa_famille=="")) {
		$bull2016_hauteur_visa_famille=18;
	}

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


// POSITION/HAUTEUR A RECALCULER

	// Ordonnées avec un espace maximal pour les AP, EPI, Parcours, en calant le visa de la famille en bas de page.
	//Cycle 3
	//$param_bull2016["y_bandeau_bilan_acquisitions_cycle_3"]=91;

	//Cycle 4
	//$param_bull2016["y_bandeau_bilan_acquisitions_cycle_4"]=129.5;
	$param_bull2016["largeur_bandeau_bilan_acquisitions"]=198;
	$param_bull2016["hauteur_bandeau_bilan_acquisitions"]=8;
	$param_bull2016["hauteur_bandeau_communication_famille"]=8;


	$param_bull2016["y_bandeau_bilan_acquisitions_cycle_3"]=HauteurPage
											-$param_bull2016["hauteur_bandeau_bilan_acquisitions"]
											-5
											-$bull2016_hauteur_bilan_acquisitions_cycle_3
											-10
											-$param_bull2016["hauteur_bandeau_communication_famille"]
											-5
											-$bull2016_hauteur_communication_famille
											-($param_bull2016["bull2016_cadre_visa_famille"]=="y" ? 1 : 0)*4
											-($param_bull2016["bull2016_cadre_visa_famille"]=="y" ? 1 : 0)*$bull2016_hauteur_visa_famille
											-10;


	$param_bull2016["y_bandeau_bilan_acquisitions_cycle_4"]=HauteurPage
											-$param_bull2016["hauteur_bandeau_bilan_acquisitions"]
											-5
											-$bull2016_hauteur_bilan_acquisitions_cycle_4
											-10
											-$param_bull2016["hauteur_bandeau_communication_famille"]
											-5
											-$bull2016_hauteur_communication_famille
											-($param_bull2016["bull2016_cadre_visa_famille"]=="y" ? 1 : 0)*4
											-($param_bull2016["bull2016_cadre_visa_famille"]=="y" ? 1 : 0)*$bull2016_hauteur_visa_famille
											-10;


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
	//$param_bull2016["hauteur_bilan_acquisitions_cycle_3"]=83;
	$param_bull2016["hauteur_bilan_acquisitions_cycle_3"]=$bull2016_hauteur_bilan_acquisitions_cycle_3;
	//Cycle 4
	//$param_bull2016["hauteur_bilan_acquisitions_cycle_4"]=44;
	$param_bull2016["hauteur_bilan_acquisitions_cycle_4"]=$bull2016_hauteur_bilan_acquisitions_cycle_4;



	// MENTIONS

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



	// MOYENNES

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


	$param_bull2016["bull2016_moyminclassemax"]=getSettingValue("bull2016_moyminclassemax");



	// APPRECIATIONS et SOUS-MATIERES

	$param_bull2016["bull2016_autorise_sous_matiere"]=getSettingValue("bull2016_autorise_sous_matiere");



	// ABSENCES, RETARDS,...

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
	//$param_bull2016["y_bandeau_communication_famille"]=197;
	$param_bull2016["largeur_bandeau_communication_famille"]=198;
	//$param_bull2016["hauteur_bandeau_communication_famille"]=8;

	$param_bull2016["y_bandeau_communication_famille"]=HauteurPage
											-$param_bull2016["hauteur_bandeau_communication_famille"]
											-5
											-$bull2016_hauteur_communication_famille
											-($param_bull2016["bull2016_cadre_visa_famille"]=="y" ? 1 : 0)*4
											-($param_bull2016["bull2016_cadre_visa_famille"]=="y" ? 1 : 0)*$bull2016_hauteur_visa_famille
											-10;

	$param_bull2016["couleur_communication_famille"]["R"]=15*16+12;
	$param_bull2016["couleur_communication_famille"]["V"]=12*16+10;
	$param_bull2016["couleur_communication_famille"]["B"]=8*16+14;
	//fcca8e
	$param_bull2016["x_communication_famille"]=10;

	//$param_bull2016["y_communication_famille"]=210;
	$param_bull2016["y_communication_famille"]=$param_bull2016["y_bandeau_communication_famille"]+$param_bull2016["hauteur_bandeau_communication_famille"]+4;

	$param_bull2016["largeur_communication_famille"]=142;

	//$param_bull2016["hauteur_communication_famille"]=49.5;
	$param_bull2016["hauteur_communication_famille"]=$bull2016_hauteur_communication_famille;

	$param_bull2016["x_signature_chef"]=$param_bull2016["x_communication_famille"]+$param_bull2016["largeur_communication_famille"]+0.5;
	//$param_bull2016["y_signature_chef"]=210;
	$param_bull2016["y_signature_chef"]=$param_bull2016["y_communication_famille"];
	$param_bull2016["largeur_signature_chef"]=47.5;
	$param_bull2016["hauteur_signature_chef"]=$param_bull2016["hauteur_communication_famille"];

	$param_bull2016["affichage_haut_responsable"]="y";
	//$param_bull2016["affiche_fonction_chef"]="y";
	//$param_bull2016["taille_texte_fonction_chef"]=9;
	$param_bull2016["taille_texte_identite_chef"]=8;

	//$param_bull2016["couleur_visa_famille"]["R"]=15*16+12;
	//$param_bull2016["couleur_visa_famille"]["V"]=12*16+10;
	//$param_bull2016["couleur_visa_famille"]["B"]=8*16+14;
	//fcca8e
	$param_bull2016["x_visa_famille"]=10;
	//$param_bull2016["y_visa_famille"]=264.5;
	$param_bull2016["y_visa_famille"]=HauteurPage
							-$bull2016_hauteur_visa_famille
							-10;
	$param_bull2016["largeur_visa_famille"]=189;
	$param_bull2016["hauteur_visa_famille"]=$bull2016_hauteur_visa_famille;

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

	$param_bull2016["bull2016_pas_espace_reserve_EPI_AP_Parcours"]=getSettingValue('bull2016_pas_espace_reserve_EPI_AP_Parcours');

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

	$bull2016_largeur_engagements=getSettingValue('bull2016_largeur_engagements');
	if($bull2016_largeur_engagements=="") {
		$bull2016_largeur_engagements=30;
	}
	elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_engagements)) {
		$bull2016_largeur_engagements=30;
	}
	$param_bull2016["largeur_engagements"]=$bull2016_largeur_engagements;

	$param_bull2016["bull2016_afficher_engagements_id"]=array();
	$sql="SELECT * FROM setting WHERE name LIKE 'bull2016_afficher_engagements_id_%';";
	$res_eng=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_eng)>0) {
		while($lig_eng=mysqli_fetch_object($res_eng)) {
			$param_bull2016["bull2016_afficher_engagements_id"][]=preg_replace("/^bull2016_afficher_engagements_id_/", "", $lig_eng->NAME);
		}
	}


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
	$param_bull2016["y_bandeau_bilan_acquisitions_cycle_3"]=HauteurPage
											-$param_bull2016["hauteur_bandeau_bilan_acquisitions"]
											-$bull2016_hauteur_bilan_acquisitions_cycle_3
											-5
											-$param_bull2016["hauteur_bandeau_bilan_acquisitions"]
											-$bull2016_hauteur_bilan_acquisitions_cycle_3
											-5
											-($param_bull2016["bull2016_cadre_visa_famille"]=="y" ? 1 : 0)*$hauteur_visa_famille
											-10;

	//Cycle 3
	$param_bull2016["y_bilan_acquisitions_cycle_3"]=$param_bull2016["y_bandeau_bilan_acquisitions_cycle_3"]+$param_bull2016["hauteur_bandeau_bilan_acquisitions"]+5;
	//Cycle 4
	$param_bull2016["y_bilan_acquisitions_cycle_4"]=$param_bull2016["y_bandeau_bilan_acquisitions_cycle_4"]+$param_bull2016["hauteur_bandeau_bilan_acquisitions"]+5;
	*/

	//========================================
	// Bilans de fin de cycle
	// Fond fffba6: 255 251 166

	$param_bull2016["couleur_fond_bilan_cycle"]["R"]=255;
	$param_bull2016["couleur_fond_bilan_cycle"]["V"]=251;
	$param_bull2016["couleur_fond_bilan_cycle"]["B"]=166;


	// Bandeau Maitrise des composantes du socle
	$param_bull2016["couleur_bandeau_maitrise_composantes_socle"]["R"]=9;
	$param_bull2016["couleur_bandeau_maitrise_composantes_socle"]["V"]=94;
	$param_bull2016["couleur_bandeau_maitrise_composantes_socle"]["B"]=221;

	$param_bull2016["x_bandeau_maitrise_composantes_socle"]=3;
	//$param_bull2016["y_bandeau_maitrise_composantes_socle"]=88;
	$param_bull2016["y_bandeau_maitrise_composantes_socle"]=99;
	$param_bull2016["largeur_bandeau_maitrise_composantes_socle"]=202.5;
	$param_bull2016["hauteur_bandeau_maitrise_composantes_socle"]=8;

	// Tableau Maitrise des composantes du socle
	$param_bull2016["x_col_domaine_maitrise_composantes_socle"]=7;
	$param_bull2016["largeur_col_domaine_maitrise_composantes_socle"]=74;
	$param_bull2016["largeur_col_niveau_maitrise_composantes_socle"]=116/4;
	//$param_bull2016["largeur_colonne_maitrise_bilan_cycle"]=26;

	$param_bull2016["hauteur_ligne_maitrise_bilan_cycle"]=7;

	//$param_bull2016["x_MI_bilan_cycle"]=80;
	$param_bull2016["x_MI_bilan_cycle"]=$param_bull2016["x_col_domaine_maitrise_composantes_socle"]+$param_bull2016["largeur_col_domaine_maitrise_composantes_socle"]+0.5;

	$param_bull2016["x_MF_bilan_cycle"]=$param_bull2016["x_MI_bilan_cycle"]+$param_bull2016["largeur_col_niveau_maitrise_composantes_socle"];
	$param_bull2016["x_MS_bilan_cycle"]=$param_bull2016["x_MF_bilan_cycle"]+$param_bull2016["largeur_col_niveau_maitrise_composantes_socle"];
	$param_bull2016["x_TBM_bilan_cycle"]=$param_bull2016["x_MS_bilan_cycle"]+$param_bull2016["largeur_col_niveau_maitrise_composantes_socle"];


	// MI ligne 1 e6f2fc
	$param_bull2016["couleur_1_MI_bilan_cycle"]["R"]=216;
	$param_bull2016["couleur_1_MI_bilan_cycle"]["V"]=242;
	$param_bull2016["couleur_1_MI_bilan_cycle"]["B"]=252;

	// MF ligne 1 cce6f8
	$param_bull2016["couleur_1_MF_bilan_cycle"]["R"]=204;
	$param_bull2016["couleur_1_MF_bilan_cycle"]["V"]=230;
	$param_bull2016["couleur_1_MF_bilan_cycle"]["B"]=248;

	// MS ligne 1 b3daf4
	$param_bull2016["couleur_1_MS_bilan_cycle"]["R"]=179;
	$param_bull2016["couleur_1_MS_bilan_cycle"]["V"]=218;
	$param_bull2016["couleur_1_MS_bilan_cycle"]["B"]=244;

	// TBM ligne 1 9acef1
	$param_bull2016["couleur_1_TBM_bilan_cycle"]["R"]=154;
	$param_bull2016["couleur_1_TBM_bilan_cycle"]["V"]=206;
	$param_bull2016["couleur_1_TBM_bilan_cycle"]["B"]=241;

	// MI ligne 2 f3f9fe
	$param_bull2016["couleur_2_MI_bilan_cycle"]["R"]=243;
	$param_bull2016["couleur_2_MI_bilan_cycle"]["V"]=249;
	$param_bull2016["couleur_2_MI_bilan_cycle"]["B"]=254;

	// MF ligne 2 d9ecfa
	$param_bull2016["couleur_2_MF_bilan_cycle"]["R"]=217;
	$param_bull2016["couleur_2_MF_bilan_cycle"]["V"]=236;
	$param_bull2016["couleur_2_MF_bilan_cycle"]["B"]=250;

	// MS ligne 2 bfe0f6
	$param_bull2016["couleur_2_MS_bilan_cycle"]["R"]=191;
	$param_bull2016["couleur_2_MS_bilan_cycle"]["V"]=224;
	$param_bull2016["couleur_2_MS_bilan_cycle"]["B"]=246;

	// TBM ligne 2 a6d4f2
	$param_bull2016["couleur_2_TBM_bilan_cycle"]["R"]=166;
	$param_bull2016["couleur_2_TBM_bilan_cycle"]["V"]=212;
	$param_bull2016["couleur_2_TBM_bilan_cycle"]["B"]=242;


	// Synthèse des acquis de fin de cycle
	$param_bull2016["couleur_bandeau_synthese_acquis_fin_cycle"]["R"]=166;
	$param_bull2016["couleur_bandeau_synthese_acquis_fin_cycle"]["V"]=13*16+8;
	$param_bull2016["couleur_bandeau_synthese_acquis_fin_cycle"]["B"]=28;

	$param_bull2016["x_bandeau_synthese_acquis_fin_cycle"]=3;
	$param_bull2016["y_bandeau_synthese_acquis_fin_cycle"]=194;
	$param_bull2016["largeur_bandeau_synthese_acquis_fin_cycle"]=202.5;
	$param_bull2016["hauteur_bandeau_synthese_acquis_fin_cycle"]=8;

	$param_bull2016["hauteur_synthese_acquis_bilan_cycle"]=41;


	// Visa
	$param_bull2016["y_visa_bilan_cycle"]=242;
	$param_bull2016["x_visa_bilan_cycle"]=7;
	$param_bull2016["x_signature_PP_bilan_cycle"]=7;

	$param_bull2016["largeur_visa_bilan_cycle"]=(210-2*7)/3;

	$param_bull2016["hauteur_signature_bilan_cycle"]=35;
	$param_bull2016["largeur_signature_PP_bilan_cycle"]=$param_bull2016["largeur_visa_bilan_cycle"];
	$param_bull2016["largeur_signature_chef_bilan_cycle"]=$param_bull2016["largeur_visa_bilan_cycle"];
	$param_bull2016["largeur_signature_parents_bilan_cycle"]=$param_bull2016["largeur_visa_bilan_cycle"];


?>
