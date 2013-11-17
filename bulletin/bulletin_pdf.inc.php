<?php
	// Tableau des champs de model_bulletin
	// Il faudra peut-être revoir cela pour initialiser un tableau $valeur_bull_pdf[] des valeurs par défaut des champs
	// ou une autre structure $modele_bull_pdf[$i]['nom_champ']="nom_model_bulletin"
	//                        $modele_bull_pdf[$i]['valeur']="Defaut"

	// echo "show fields from model_bulletin;" | mysql -uroot -p gepitrunk|tr "\t" " "|cut -d" " -f1|while read A;do echo "\$champ_bull_pdf[]=\"$A\";";done
	//$champ_bull_pdf[]="Field";
	//$champ_bull_pdf[]="id_model_bulletin";
	$champ_bull_pdf[]="nom_model_bulletin";
	$champ_bull_pdf[]="active_bloc_datation";
	$champ_bull_pdf[]="active_bloc_eleve";
	$champ_bull_pdf[]="active_bloc_adresse_parent";

	$champ_bull_pdf[]="active_bloc_absence";
	$champ_bull_pdf[]="afficher_abs_tot";
	$champ_bull_pdf[]="afficher_abs_nj";
	$champ_bull_pdf[]="afficher_abs_ret";

	$champ_bull_pdf[]="active_bloc_note_appreciation";
	$champ_bull_pdf[]="active_bloc_avis_conseil";
	$champ_bull_pdf[]="active_bloc_chef";
	$champ_bull_pdf[]="active_photo";
	$champ_bull_pdf[]="active_coef_moyenne";
	$champ_bull_pdf[]="active_nombre_note";
	$champ_bull_pdf[]="active_nombre_note_case";
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

	$val_defaut_champ_bull_pdf["nom_model_bulletin"]="Nom du modèle";
	$val_defaut_champ_bull_pdf["active_bloc_datation"]=1;
	$val_defaut_champ_bull_pdf["active_bloc_eleve"]=1;
	$val_defaut_champ_bull_pdf["active_bloc_adresse_parent"]=1;

	$val_defaut_champ_bull_pdf["active_bloc_absence"]=1;
	$val_defaut_champ_bull_pdf["afficher_abs_tot"]=1;
	$val_defaut_champ_bull_pdf["afficher_abs_nj"]=1;
	$val_defaut_champ_bull_pdf["afficher_abs_ret"]=1;

	$val_defaut_champ_bull_pdf["active_bloc_note_appreciation"]=1;
	$val_defaut_champ_bull_pdf["active_bloc_avis_conseil"]=1;
	$val_defaut_champ_bull_pdf["active_bloc_chef"]=1;
	$val_defaut_champ_bull_pdf["active_photo"]=0;
	$val_defaut_champ_bull_pdf["active_coef_moyenne"]=0;
	$val_defaut_champ_bull_pdf["active_nombre_note"]=0;
	$val_defaut_champ_bull_pdf["active_nombre_note_case"]=0;
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



	function get_max_id_model_bulletin() {
		$sql="SELECT MAX(id_model_bulletin) AS max_id_model_bulletin FROM modele_bulletin;";
		$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res)==0) {
			// Ca ne devrait pas arriver: en 1 il doit y avoir le modèle standard
			return 2;
		}
		else {
			$lig=mysqli_fetch_object($res);
			return $lig->max_id_model_bulletin;
		}
	}
?>
