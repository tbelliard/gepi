<?php
/*
*/

// Page incluse dans saisie_sanction.php ou appelée via ajax depuis saisie_sanction.php->ajout_sanction.php

//Configuration du calendrier

include("../lib/calendrier/calendrier.class.php");

//Variable : $dernier  on afficher le dernier créneau si $dernier='o' (paramètre pour une exclusion)
function choix_heure2($champ_heure,$selected,$dernier) {
	$sql="SELECT * FROM edt_creneaux ORDER BY heuredebut_definie_periode;";
	$res_abs_cren=mysqli_query($GLOBALS["mysqli"], $sql);
	$num_row = mysqli_num_rows($res_abs_cren); //le nombre de ligne de la requète
	if($num_row==0) {
		echo "La table edt_creneaux n'est pas renseignée!";
	}
	else {
        $cpt=1;	
		//echo "<select name='$champ_heure' id='$champ_heure' onchange='changement();' >\n";
		echo "<select name='$champ_heure' id='$champ_heure' onchange=\"if(document.getElementById('display_heure_main')) {document.getElementById('display_heure_main').value=document.getElementById('$champ_heure').options[document.getElementById('$champ_heure').selectedIndex].value};changement();\" >\n";
		
		while($lig_ac=mysqli_fetch_object($res_abs_cren)) {
			echo "<option value='$lig_ac->nom_definie_periode'";
			if(($lig_ac->nom_definie_periode==$selected)||(($dernier=='o')&&($cpt==$num_row))) {echo " selected='selected'";}
			echo ">$lig_ac->nom_definie_periode&nbsp;: $lig_ac->heuredebut_definie_periode à $lig_ac->heurefin_definie_periode</option>\n";
			$cpt++;
		}
		echo "</select>\n";
	}
}

//if((!isset($cpt))||(!isset($valeur))) {
if(!isset($valeur)) {
	echo "<p><strong>Erreur&nbsp;:</strong> Des paramètres n'ont pas été transmis.</p>\n";
	die();
}

require_once('sanctions_func_lib.php');
//echo "\$ele_login=$ele_login<br />";
//echo "\$id_incident=$id_incident<br />";
$meme_sanction_pour_autres_protagonistes="";
if(isset($ele_login)) {
	//echo "plop";
	$texte_protagoniste_1="Sanction pour <b>".get_nom_prenom_eleve($ele_login)."</b><br />\n";
	if((isset($id_incident))&&(!isset($id_sanction))) {
		$tab_protagonistes=get_protagonistes($id_incident,array('Responsable'),array('eleve'));
		if(count($tab_protagonistes)>1) {
			//echo "plup";
			$meme_sanction_pour_autres_protagonistes.="Même ".$mod_disc_terme_sanction." pour&nbsp;:<br />\n";
			for($loop=0;$loop<count($tab_protagonistes);$loop++) {
				if($tab_protagonistes[$loop]!=$ele_login) {
					$meme_sanction_pour_autres_protagonistes.="<input type='checkbox' name='autre_protagoniste_meme_sanction[]' id='autre_protagoniste_meme_sanction_$loop' value=\"$tab_protagonistes[$loop]\" /><label for='autre_protagoniste_meme_sanction_$loop'>".get_nom_prenom_eleve($tab_protagonistes[$loop])."</label><br />\n";
				}
			}

			$meme_sanction_pour_autres_protagonistes=$texte_protagoniste_1.$meme_sanction_pour_autres_protagonistes;
		}
	}
	//elseif(isset($id_sanction)) {
	//}
}


$sql="SELECT * FROM s_types_sanctions2 WHERE id_nature='$valeur';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p style='color:red;'>Type de ".$mod_disc_terme_sanction." inconnu.</p>\n";
}
else {
	$lig=mysqli_fetch_object($res);
	$nature_sanction=$lig->nature;
	$type_sanction=$lig->type;

	if($type_sanction=='travail') {
		echo "<table class='boireaus' border='1'>\n";

		$cal = new Calendrier("formulaire", "date_retour");

		$annee = strftime("%Y");
		$mois = strftime("%m");
		$jour = strftime("%d");
		$date_retour=$jour."/".$mois."/".$annee;

		$travail="";
		$heure_retour=strftime("%H").":".strftime("%M");
		if(isset($id_sanction)) {
			$sql="SELECT * FROM s_travail WHERE id_sanction='$id_sanction';";
			$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_sanction)>0) {
				$lig_sanction=mysqli_fetch_object($res_sanction);
				$date_retour=formate_date($lig_sanction->date_retour);
				$heure_retour=$lig_sanction->heure_retour;
				$travail=$lig_sanction->travail;
			}
		}

		if(($travail=="")&&(isset($id_incident))&&(isset($ele_login))) {
			$sql="SELECT * FROM s_travail_mesure WHERE id_incident='$id_incident' AND login_ele='".$ele_login."';";
			$res_travail_mesure_demandee=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_travail_mesure_demandee)>0) {
				$lig_travail_mesure_demandee=mysqli_fetch_object($res_travail_mesure_demandee);
				$travail=$lig_travail_mesure_demandee->travail;
			}
		}

		$alt=1;
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Date de retour&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		//echo "<input type='text' name='date_retour' id='date_retour' size='10' value=\"".$date_retour."\" onchange='changement();' />\n";
		echo "<input type='text' name='date_retour' id='date_retour' size='10' value=\"".$date_retour."\" onchange='changement();' onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
		echo "<a href=\"#calend\" onclick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\">\n";
		echo "<img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" />\n";
		echo "</a>\n";
		echo "</td>\n";
		echo "</tr>\n";

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure de retour&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		choix_heure2('heure_retour',$heure_retour,'');
		echo "</td>\n";
		echo "</tr>\n";

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Nature du travail&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";

		echo "<div style='float:right;'>";
		if(isset($id_sanction)) {
			echo lien_envoi_mail_rappel($id_sanction, 0);
		}
		elseif(isset($id_incident)) {
			echo lien_envoi_mail_rappel($id_sanction, 0, $id_incident);
		}
		//echo envoi_mail_rappel_js();
		echo "</div>\n";

		echo "<textarea name='no_anti_inject_travail' id='textarea_nature_travail' cols='30' onchange='changement();'>$travail</textarea>\n";
		echo insere_lien_recherche_ajax_ele('textarea_nature_travail');

		//echo "<span style='color: red;'>Mettre un champ d'ajout de fichier.</span><br />\n";
		//echo "<span style='color: red;'>Pouvoir aussi choisir un des fichiers joints lors de la déclaration de l'incident.</span><br />\n";

		if((isset($ele_login))&&(isset($id_incident))) {
			sanction_documents_joints($id_incident, $ele_login);
		}

		echo "</td>\n";
		echo "</tr>\n";

		if($meme_sanction_pour_autres_protagonistes!="") {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Même ".$mod_disc_terme_sanction."&nbsp;: </td>\n";
			echo "<td style='text-align:left;'>\n";
			echo $meme_sanction_pour_autres_protagonistes;
			echo "</td>\n";
			echo "</tr>\n";
		}

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td colspan='2'>\n";
		echo "<input type='submit' name='enregistrer_sanction' value='Enregistrer' />\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "</table>\n";
	}
	elseif($type_sanction=='retenue') {

		$cal = new Calendrier("formulaire", "date_retenue");

		$annee = strftime("%Y");
		$mois = strftime("%m");
		$jour = strftime("%d");
		$date_retenue=$jour."/".$mois."/".$annee;

		//$heure_debut=strftime("%H").":".strftime("%M");
		$heure_debut='00:00';
		$duree_retenue=1;
		$lieu_retenue="";
		$travail="";
        $materiel="";
		if(isset($id_sanction)) {
			$sql="SELECT * FROM s_retenues WHERE id_sanction='$id_sanction';";
			$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_sanction)>0) {
				$lig_sanction=mysqli_fetch_object($res_sanction);
				$date_retenue=formate_date($lig_sanction->date);
				$heure_debut=$lig_sanction->heure_debut;
				$duree_retenue=$lig_sanction->duree;
				$lieu_retenue=$lig_sanction->lieu;
				$travail=$lig_sanction->travail;
                $materiel=$lig_sanction->materiel;
			}
		}

		if(($travail=="")&&(isset($id_incident))&&(isset($ele_login))) {
			$sql="SELECT * FROM s_travail_mesure WHERE id_incident='$id_incident' AND login_ele='".$ele_login."';";
			$res_travail_mesure_demandee=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_travail_mesure_demandee)>0) {
				$lig_travail_mesure_demandee=mysqli_fetch_object($res_travail_mesure_demandee);
				$travail=$lig_travail_mesure_demandee->travail;
                $materiel=$lig_travail_mesure_demandee->materiel;
			}
		}

		//echo "<div id='div_liste_retenues_jour' style='float:right; border:1px solid black;background-color: honeydew;'>\n";
		echo "<div id='div_liste_retenues_jour' style='float:right; text-align: center; border:1px solid black; margin-top: 2px; min-width: 19px;'>\n";
		echo "<a href='#' onclick=\"maj_div_liste_retenues_jour();return false;\" title='Retenues du jour'><img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Retenues du jour' /></a>";
		echo "</div>\n";

		echo "<table class='boireaus' border='1' summary='Retenue'>\n";
		$alt=1;
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Date&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		//echo "<input type='text' name='date_retenue' id='date_retenue' value='$date_retenue' size='10' onchange='maj_div_liste_retenues_jour();changement();' onblur='maj_div_liste_retenues_jour();' />\n";
		echo "<input type='text' name='date_retenue' id='date_retenue' value='$date_retenue' size='10' onchange='maj_div_liste_retenues_jour();changement();' onblur='maj_div_liste_retenues_jour();' onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
		echo "<a href=\"#calend\" onclick=\"$('date_retenue').focus();".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\">\n";
		echo "<img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" />\n";
		echo "</a>\n";

		// Si le module EDT est actif et si l'EDT est renseigné
		if(param_edt($_SESSION["statut"]) == 'yes') {
			//echo "<a href='#' onclick=\"edt_eleve('$id_sanction');return false;\" title='EDT élève'><img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='EDT élève' /></a>";
			echo "<a href='#' onclick=\"edt_eleve();return false;\" title='EDT élève'><img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='EDT élève' /></a>";
			//echo "<input type='hidden' name='ele_login' id='ele_login' value='$ele_login' />\n";
		}

		echo "</td>\n";
		echo "</tr>\n";

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure de début&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		//echo "<input type='text' name='heure_debut' value='' />\n";
		echo "<input type='text' name='heure_debut_main' id='display_heure_main' size='5' value=\"$heure_debut\" onKeyDown=\"clavier_heure(this.id,event);\" AutoComplete=\"off\" /> ou \n";
		choix_heure2('heure_debut',$heure_debut,'');
	
		//pour infobulle
		$texte="- 2 choix possibles pour inscrire l'heure de début de la retenue<br />Le premier grace à la liste déroulante. Vous choisissez un créneau. Dans ce cas, c'est l'heure début de crénaux HH:MM qui sera pris en compte pour l'impression de la retenue.<br/>Dans l'autre cas, vous saisissez l'heure à la place de '00:00' sous ce format.";
	
		echo "</td>\n";
		echo "</tr>\n";

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Durée&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		echo "<input type='text' name='duree_retenue' id='duree_retenue' size='2' value='$duree_retenue' onchange='changement();' /> en heures\n";
		echo "</td>\n";
		echo "</tr>\n";

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Lieu&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		echo "<input type='text' name='lieu_retenue' id='lieu_retenue' value='$lieu_retenue' onchange='changement();' />\n";
		// Sélectionner parmi des lieux déjà saisis?
		//$sql="SELECT DISTINCT lieu FROM s_retenues WHERE lieu!='' ORDER BY lieu;";
		$sql="(SELECT DISTINCT lieu FROM s_retenues WHERE lieu!='')";
		if(param_edt($_SESSION["statut"]) == 'yes') {
			$sql.=" UNION (SELECT DISTINCT nom_salle AS lieu FROM salle_cours WHERE nom_salle!='')";
		}
		$sql.=" ORDER BY lieu;";
		//echo "$sql<br />";
		$res_lieu=mysqli_query($GLOBALS["mysqli"], $sql);
		//$tab_lieux=array();
		//$chaine_lieux="";
		if(mysqli_num_rows($res_lieu)>0) {
			echo "<select name='choix_lieu' id='choix_lieu' onchange=\"maj_lieu('lieu_retenue','choix_lieu');changement();\">\n";
			echo "<option value=''>---</option>\n";
			while($lig_lieu=mysqli_fetch_object($res_lieu)) {
				echo "<option value=\"$lig_lieu->lieu\">$lig_lieu->lieu</option>\n";
				//$tab_lieux[]=urlencode($lig_lieu->lieu);
				//$chaine_lieux.=", '".urlencode($lig_lieu->lieu)."'";
			}
			echo "</select>\n";

			echo "<a href='#' onclick=\"occupation_lieu_heure('$id_sanction');return false;\"><img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Occupation du lieu pour la date/heure choisie' /></a>";
		}

		echo "</td>\n";
		echo "</tr>\n";

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Nature du travail&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		//echo "plop";
		echo "<div style='float:right;'>";
		if(isset($id_sanction)) {
			//echo "\$id_sanction=$id_sanction";
			echo lien_envoi_mail_rappel($id_sanction, 0);
		}
		elseif(isset($id_incident)) {
			//echo "\$id_incident=$id_incident";
			echo lien_envoi_mail_rappel("", 0, $id_incident);
		}
		//echo envoi_mail_rappel_js();
		echo "</div>\n";

		echo "<textarea name='no_anti_inject_travail' id='textarea_nature_travail' cols='30' onchange='changement();'>$travail</textarea>\n";
		echo insere_lien_recherche_ajax_ele('textarea_nature_travail');

		//echo "<span style='color: red;'>Mettre un champ d'ajout de fichier.</span><br />\n";
		//echo "<span style='color: red;'>Pouvoir aussi choisir un des fichiers joints lors de la déclaration de l'incident.</span><br />\n";

		if((isset($ele_login))&&(isset($id_incident))) {
			sanction_documents_joints($id_incident, $ele_login);
		}

		echo "</td>\n";
		echo "</tr>\n";
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td>\n";
        echo "Matériel à apporter\n";
		echo "</td>\n";
		echo "<td>\n";
        echo "<input type='text' name='materiel' onchange='changement();' value='$materiel' />\n";
		echo "</td>\n";
		echo "</tr>\n";
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Report&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
	
		echo "<b>Gestion d'un report :</b><br/>\n";
	
		echo "<ol>\n";
		echo "<li>Cocher cette case pour traiter un report : <input type='checkbox' name='report_demande' id='report_demande' value='OK' onchange=\"changement();\" /></li>\n";
		echo "<li>Saisir le motif du report : <select name='choix_motif_report' id='choix_motif_report' onchange=\"changement(); if(this.selectedIndex!=0) {document.getElementById('report_demande').checked=true;} else {document.getElementById('report_demande').checked=false;}\">\n";
		echo "<option value=''>---</option>\n";
		echo "<option value='absent'>Absent</option>\n";
		echo "<option value='aucun_motif'>Aucun motif</option>\n";
		echo "<option value='report_demande'>Report demandé</option>\n";
		echo "<option value='autre'>Autre</option>\n";
		echo "</select></li>\n";
		echo "<li>Modifier les données (date, heure, ...) pour le report</li>\n";
		echo "<li>Enregistrer les modifications</li>\n";
		echo "<li>Imprimer le document sur la page suivante</li>\n";
		echo "</ol>\n";
	
		if (isset($id_sanction)) {
			echo "<b>Liste des reports</b><br/>\n";
			echo afficher_tableau_des_reports($id_sanction);
		}
		echo "</td>\n";
		echo "</tr>\n";

		if($meme_sanction_pour_autres_protagonistes!="") {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Même ".$mod_disc_terme_sanction."&nbsp;: </td>\n";
			echo "<td style='text-align:left;'>\n";
			echo $meme_sanction_pour_autres_protagonistes;
			echo "</td>\n";
			echo "</tr>\n";
		}

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td colspan='2'>\n";
		echo "<input type='submit' name='enregistrer_sanction' value='Enregistrer' />\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "</table>\n";

		echo "<script type='text/javascript'>
		// Le lancement ci-dessous n'est pas pris en compte pour l'ajout d'une retenue, seulement pour la modification d'une retenue.
		// J'ai donc mis un lien dans le DIV div_liste_retenues_jour
		maj_div_liste_retenues_jour();
	</script>\n";
	}
	elseif($type_sanction=='exclusion') {
		echo "<table class='boireaus' border='1' summary='Exclusion'>\n";

		$cal1 = new Calendrier("formulaire", "date_debut");

		$annee = strftime("%Y");
		$mois = strftime("%m");
		$jour = strftime("%d");
		$date_debut=$jour."/".$mois."/".$annee;
		$date_fin=$date_debut;

		$heure_debut=strftime("%H").":".strftime("%M");
		$heure_fin=$heure_debut;
		$afficher_creneau_final = 'o';

		$lieu_exclusion="";
		$travail="";
	
		$nombre_jours="";
		$qualification_faits="";
		$numero_courrier="";
		$type_exclusion="";
		$fct_autorite="";
		$nom_autorite="";
		$fct_delegation="";
	
		if(isset($id_sanction)) {
			$sql="SELECT * FROM s_exclusions WHERE id_sanction='$id_sanction';";
			$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_sanction)>0) {
				$lig_sanction=mysqli_fetch_object($res_sanction);
				$date_debut=formate_date($lig_sanction->date_debut);
				$date_fin=formate_date($lig_sanction->date_fin);
				$heure_debut=$lig_sanction->heure_debut;
				$heure_fin=$lig_sanction->heure_fin;
				$lieu_exclusion=$lig_sanction->lieu;
				$travail=$lig_sanction->travail;
				$afficher_creneau_final='';
				$nombre_jours=$lig_sanction->nombre_jours;
				$qualification_faits=$lig_sanction->qualification_faits;
				$numero_courrier=$lig_sanction->num_courrier;
				$type_exclusion=$lig_sanction->type_exclusion;
				$signataire=$lig_sanction->id_signataire;
			} 
		}

		if(($travail=="")&&(isset($id_incident))&&(isset($ele_login))) {
			$sql="SELECT * FROM s_travail_mesure WHERE id_incident='$id_incident' AND login_ele='".$ele_login."';";
			$res_travail_mesure_demandee=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_travail_mesure_demandee)>0) {
				$lig_travail_mesure_demandee=mysqli_fetch_object($res_travail_mesure_demandee);
				$travail=$lig_travail_mesure_demandee->travail;
			}
		}

		$alt=1;
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Date de début&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		//echo "<input type='text' name='date_debut' id='date_debut' value='$date_debut' size='10' onchange='changement();' />\n";
		echo "<input type='text' name='date_debut' id='date_debut' value='$date_debut' size='10' onchange='changement();' onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
		echo "<a href=\"#calend\" onclick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\">\n";
		echo "<img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" />\n";
		echo "</a>\n";
		echo "</td>\n";
		echo "</tr>\n";

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure de début&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		//echo "<input type='text' name='heure_debut' value='' />\n";
		choix_heure2('heure_debut',$heure_debut,'');
		echo "</td>\n";
		echo "</tr>\n";

		$cal2 = new Calendrier("formulaire", "date_fin");

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Date de fin&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		//echo "<input type='text' name='date_fin' id='date_fin' value='$date_fin' size='10' onchange='changement();' />\n";
		echo "<input type='text' name='date_fin' id='date_fin' value='$date_fin' size='10' onchange='changement();' onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
		echo "<a href=\"#calend\" onclick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\">\n";
		echo "<img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" />\n";
		echo "</a>\n";
		echo "</td>\n";
		echo "</tr>\n";

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure de fin&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		//echo "<input type='text' name='heure_debut' value='' />\n";
		choix_heure2('heure_fin',$heure_fin,$afficher_creneau_final);
		echo "</td>\n";
		echo "</tr>\n";

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Lieu&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		echo "<input type='text' name='lieu_exclusion' id='lieu_exclusion' value=\"$lieu_exclusion\" onchange='changement();' />\n";
		// Sélectionner parmi des lieux déjà saisis?
		$sql="SELECT DISTINCT lieu FROM s_exclusions WHERE lieu!='' ORDER BY lieu;";
		$res_lieu=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_lieu)>0) {
			echo "<select name='choix_lieu' id='choix_lieu' onchange=\"maj_lieu('lieu_exclusion','choix_lieu');changement();\">\n";
			echo "<option value=''>---</option>\n";
			while($lig_lieu=mysqli_fetch_object($res_lieu)) {
				echo "<option value=\"$lig_lieu->lieu\">$lig_lieu->lieu</option>\n";
			}
			echo "</select>\n";
		}
		echo "</td>\n";
		echo "</tr>\n";

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Nature du travail&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";

		echo "<div style='float:right;'>";
		if(isset($id_sanction)) {
			echo lien_envoi_mail_rappel($id_sanction, 0);
		}
		elseif(isset($id_incident)) {
			echo lien_envoi_mail_rappel($id_sanction, 0, $id_incident);
		}
		//echo envoi_mail_rappel_js();
		echo "</div>\n";

		echo "<textarea name='no_anti_inject_travail' id='textarea_nature_travail' cols='30' onchange='changement();'>$travail</textarea>\n";
		echo insere_lien_recherche_ajax_ele('textarea_nature_travail');

		//echo "<span style='color: red;'>Mettre un champ d'ajout de fichier.</span><br />\n";
		//echo "<span style='color: red;'>Pouvoir aussi choisir un des fichiers joints lors de la déclaration de l'incident.</span><br />\n";

		if((isset($ele_login))&&(isset($id_incident))) {
			sanction_documents_joints($id_incident, $ele_login);
		}

		echo "</td>\n";
		echo "</tr>\n";

	// Ajout Eric génération Ooo de l'exclusion
		$alt=$alt*(-1);
		echo "<tr>\n";
		echo "<td colspan=2 style='text-align:center;'>\n";
		echo "Données à renseigner pour l'impression Open Office de l'exclusion temporaire :</td>\n";
		echo "</tr>\n";

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Numero de courrier&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		echo "<input type='text' name='numero_courrier' id='numero_courrier' value=\"$numero_courrier\" onchange='changement();' />\n";
		echo "<i>La référence du courrier dans le registre courrier départ. Ex : ADM/SD/012/11</i></td>\n";
		echo "</tr>\n";
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Type d'exclusion&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		echo "<input type='text' name='type_exclusion' id='type_exclusion' value=\"$type_exclusion\" onchange='changement();' />\n";
		echo "<select name='type_exclusion' id='type_exclusion_select' onchange=\"maj_lieu('type_exclusion','type_exclusion_select','type_exclusion');changement();\">\n";
		if ($type_exclusion=='exclusion temporaire') {
			echo "<option value=\"exclusion temporaire\" selected>Exclusion temporaire</option>\n";
		} else {
			echo "<option value=\"exclusion temporaire\">Exclusion temporaire</option>\n";
		}
		if ($type_exclusion=='exclusion-inclusion temporaire') {
			echo "<option value=\"exclusion-inclusion temporaire\" selected>Exclusion-inclusion temporaire</option>\n";
		} else {
			echo "<option value=\"exclusion-inclusion temporaire\">Exclusion-inclusion temporaire</option>\n";
		}
	
	
		echo "</select>\n";
		echo "<i>Choisir le type dans la liste.</i></td>\n";
		echo "</tr>\n";	
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Nombre de jours d'exclusion&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		echo "<input type='text' name='nombre_jours' id='nombre_jours' value=\"$nombre_jours\" onchange='changement();' />\n";
		echo "<i>en toutes lettres</i></td>\n";
		echo "</tr>\n";
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Qualification des faits&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		echo "<textarea name='no_anti_inject_qualification_faits' id='textarea_qualification_faits' cols='100' onchange='changement();'>$qualification_faits</textarea>\n";
		echo insere_lien_recherche_ajax_ele('textarea_qualification_faits');
		echo "</td>\n";
		echo "</tr>\n";
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Choix du signataire de l'exclusion&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		// Sélectionner parmi les signataires déjà saisis?
		$sql="SELECT * FROM s_delegation ORDER BY fct_autorite";
		$res_signataire=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_signataire)>0) {
			echo "<select name='signataire' id='choix_signataire' onchange=\"changement();\">\n";
			echo "<option value=''>---</option>\n";
			while($lig_signataire=mysqli_fetch_object($res_signataire)) {
				if ($signataire==$lig_signataire->id_delegation) {
				echo "<option value=\"$lig_signataire->id_delegation\" selected >$lig_signataire->fct_autorite</option>\n";
				} else {
				echo "<option value=\"$lig_signataire->id_delegation\">$lig_signataire->fct_autorite</option>\n";
				}
			}
			echo "</select>\n";
		} else {
			echo "<i>Aucun signataire n'est saisi dans la base. Demandez à votre administrateur de saisir cette liste en admin du module</i>";
		};
		echo "</td>\n";
		echo "</tr>\n";

		if($meme_sanction_pour_autres_protagonistes!="") {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Même ".$mod_disc_terme_sanction."&nbsp;: </td>\n";
			echo "<td style='text-align:left;'>\n";
			echo $meme_sanction_pour_autres_protagonistes;
			echo "</td>\n";
			echo "</tr>\n";
		}

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td colspan='2'>\n";
		echo "<input type='submit' name='enregistrer_sanction' value='Enregistrer' />\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "</table>\n";
	}
	else {
		/*
		$sql="SELECT * FROM s_types_sanctions WHERE id_nature='$valeur';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
		*/

			//echo "<table class='boireaus' border='1' summary=\"$lig->nature\">\n";
			echo "<table class='boireaus' border='1' summary=\"$nature_sanction\">\n";

			$description="";

			if(isset($id_sanction)) {
				$sql="SELECT * FROM s_autres_sanctions WHERE id_sanction='$id_sanction';";
				$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sanction)>0) {
					$lig_sanction=mysqli_fetch_object($res_sanction);
					$description=$lig_sanction->description;
				}
			}

			echo "<tr>\n";
			//echo "<th colspan='2'>$lig->nature</th>\n";
			echo "<th colspan='2'>$nature_sanction</th>\n";
			echo "</tr>\n";

			$alt=1;
			echo "<tr class='lig$alt'>\n";
			echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Description&nbsp;: </td>\n";
			echo "<td style='text-align:left;'>\n";
			echo "<textarea name='no_anti_inject_description' id='textarea_description_sanction' cols='30' onchange='changement();'>$description</textarea>\n";
			echo insere_lien_recherche_ajax_ele('textarea_description_sanction');

			if((isset($ele_login))&&(isset($id_incident))) {
				sanction_documents_joints($id_incident, $ele_login);
			}

			echo "</td>\n";
			echo "</tr>\n";

			if($meme_sanction_pour_autres_protagonistes!="") {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Même ".$mod_disc_terme_sanction."&nbsp;: </td>\n";
				echo "<td style='text-align:left;'>\n";
				echo $meme_sanction_pour_autres_protagonistes;
				echo "</td>\n";
				echo "</tr>\n";
			}

			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td colspan='2'>\n";
			echo "<input type='submit' name='enregistrer_sanction' value='Enregistrer' />\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "</table>\n";
		/*
		}
		else {
			echo "<p style='color:red;'>Type de sanction inconnu.</p>\n";
		}
		*/
	}
}
?>
