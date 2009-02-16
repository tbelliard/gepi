<?php
/*
$Id$
*/

// Page incluse dans saisie_sanction.php ou appelée via ajax depuis saisie_sanction.php->ajout_sanction.php

//Configuration du calendrier
include("../lib/calendrier/calendrier.class.php");

//Variable : $dernier  on afficher le dernier créneau si $dernier='o' (paramètre pour une exclusion)
function choix_heure2($champ_heure,$selected,$dernier) {
	$sql="SELECT * FROM absences_creneaux ORDER BY heuredebut_definie_periode;";
	$res_abs_cren=mysql_query($sql);
	$num_row = mysql_num_rows($res_abs_cren); //le nombre de ligne de la requète
	if($num_row==0) {
		echo "La table absences_creneaux n'est pas renseignée!";
	}
	else {
        $cpt=1;
		echo "<select name='$champ_heure' id='$champ_heure' onchange='changement();'>\n";

		while($lig_ac=mysql_fetch_object($res_abs_cren)) {
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

if($valeur=='travail') {
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
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$lig_sanction=mysql_fetch_object($res_sanction);
			$date_retour=formate_date($lig_sanction->date_retour);
			$heure_retour=$lig_sanction->heure_retour;
			$travail=$lig_sanction->travail;
		}
	}

	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Date de retour&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='date_retour' id='date_retour' size='10' value=\"".$date_retour."\" onchange='changement();' />\n";
	echo "<a href=\"#calend\" onclick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\">\n";
	echo "<img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" />\n";
	echo "</a>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure de retour&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	choix_heure2('heure_retour',$heure_retour,'');
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Nature du travail&nbsp;: </td>\n";
	echo "<td style='text-align:left;'><textarea name='no_anti_inject_travail' cols='30' onchange='changement();'>$travail</textarea>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<td colspan='2'>\n";
	echo "<input type='submit' name='enregistrer_sanction' value='Enregistrer' />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";
}
elseif($valeur=='retenue') {

	$cal = new Calendrier("formulaire", "date_retenue");

	$annee = strftime("%Y");
	$mois = strftime("%m");
	$jour = strftime("%d");
	$date_retenue=$jour."/".$mois."/".$annee;

	$heure_debut=strftime("%H").":".strftime("%M");
	$duree_retenue=1;
	$lieu_retenue="";
	$travail="";
	if(isset($id_sanction)) {
		$sql="SELECT * FROM s_retenues WHERE id_sanction='$id_sanction';";
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$lig_sanction=mysql_fetch_object($res_sanction);
			$date_retenue=formate_date($lig_sanction->date);
			$heure_debut=$lig_sanction->heure_debut;
			$duree_retenue=$lig_sanction->duree;
			$lieu_retenue=$lig_sanction->lieu;
			$travail=$lig_sanction->travail;
		}
	}

	//echo "<div id='div_liste_retenues_jour' style='float:right; border:1px solid black;background-color: honeydew;'>\n";
	echo "<div id='div_liste_retenues_jour' style='float:right; text-align: center; border:1px solid black; margin-top: 2px; min-width: 19px;'>\n";
	echo "<a href='#' onclick=\"maj_div_liste_retenues_jour();return false;\" title='Retenues du jour'><img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Retenues du jour' /></a>";
	echo "</div>\n";

	echo "<table class='boireaus' border='1' summary='Retenue'>\n";
	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Date&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='date_retenue' id='date_retenue' value='$date_retenue' size='10' onchange='maj_div_liste_retenues_jour();changement();' onblur='maj_div_liste_retenues_jour();' />\n";
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

	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure de début&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	//echo "<input type='text' name='heure_debut' value='' />\n";
	choix_heure2('heure_debut',$heure_debut,'');
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Durée&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='duree_retenue' id='duree_retenue' size='2' value='$duree_retenue' onchange='changement();' /> en heures\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
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
	$res_lieu=mysql_query($sql);
	//$tab_lieux=array();
	//$chaine_lieux="";
	if(mysql_num_rows($res_lieu)>0) {
		echo "<select name='choix_lieu' id='choix_lieu' onchange=\"maj_lieu('lieu_retenue','choix_lieu');changement();\">\n";
		echo "<option value=''>---</option>\n";
		while($lig_lieu=mysql_fetch_object($res_lieu)) {
			echo "<option value=\"$lig_lieu->lieu\">$lig_lieu->lieu</option>\n";
			//$tab_lieux[]=urlencode($lig_lieu->lieu);
			//$chaine_lieux.=", '".urlencode($lig_lieu->lieu)."'";
		}
		echo "</select>\n";

		echo "<a href='#' onclick=\"occupation_lieu_heure('$id_sanction');return false;\"><img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Occupation du lieu pour la date/heure choisie' /></a>";
	}

	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Nature du travail&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<textarea name='no_anti_inject_travail' cols='30' onchange='changement();'>$travail</textarea>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
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
elseif($valeur=='exclusion') {
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
	if(isset($id_sanction)) {
		$sql="SELECT * FROM s_exclusions WHERE id_sanction='$id_sanction';";
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$lig_sanction=mysql_fetch_object($res_sanction);
			$date_debut=formate_date($lig_sanction->date_debut);
			$date_fin=formate_date($lig_sanction->date_fin);
			$heure_debut=$lig_sanction->heure_debut;
			$heure_fin=$lig_sanction->heure_fin;
			$lieu_exclusion=$lig_sanction->lieu;
			$travail=$lig_sanction->travail;
			$afficher_creneau_final='';
		} 
	}
	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Date de début&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='date_debut' id='date_debut' value='$date_debut' size='10' onchange='changement();' />\n";
	echo "<a href=\"#calend\" onclick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\">\n";
	echo "<img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" />\n";
	echo "</a>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure de début&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	//echo "<input type='text' name='heure_debut' value='' />\n";
	choix_heure2('heure_debut',$heure_debut,'');
	echo "</td>\n";
	echo "</tr>\n";

	$cal2 = new Calendrier("formulaire", "date_fin");

	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Date de fin&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='date_fin' id='date_fin' value='$date_fin' size='10' onchange='changement();' />\n";
	echo "<a href=\"#calend\" onclick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\">\n";
	echo "<img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" />\n";
	echo "</a>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure de fin&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	//echo "<input type='text' name='heure_debut' value='' />\n";
	choix_heure2('heure_fin',$heure_fin,$afficher_creneau_final);
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Lieu&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='lieu_exclusion' id='lieu_exclusion' value=\"$lieu_exclusion\" onchange='changement();' />\n";
	// Sélectionner parmi des lieux déjà saisis?
	$sql="SELECT DISTINCT lieu FROM s_exclusions WHERE lieu!='' ORDER BY lieu;";
	$res_lieu=mysql_query($sql);
	if(mysql_num_rows($res_lieu)>0) {
		echo "<select name='choix_lieu' id='choix_lieu' onchange=\"maj_lieu('lieu_exclusion','choix_lieu');changement();\">\n";
		echo "<option value=''>---</option>\n";
		while($lig_lieu=mysql_fetch_object($res_lieu)) {
			echo "<option value=\"$lig_lieu->lieu\">$lig_lieu->lieu</option>\n";
		}
		echo "</select>\n";
	}
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Nature du travail&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<textarea name='no_anti_inject_travail' cols='30' onchange='changement();'>$travail</textarea>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig1'>\n";
	echo "<td colspan='2'>\n";
	echo "<input type='submit' name='enregistrer_sanction' value='Enregistrer' />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";
}
else {
	$sql="SELECT * FROM s_types_sanctions WHERE id_nature='$valeur';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);

		echo "<table class='boireaus' border='1' summary=\"$lig->nature\">\n";

		$description="";

		if(isset($id_sanction)) {
			$sql="SELECT * FROM s_autres_sanctions WHERE id_sanction='$id_sanction';";
			$res_sanction=mysql_query($sql);
			if(mysql_num_rows($res_sanction)>0) {
				$lig_sanction=mysql_fetch_object($res_sanction);
				$description=$lig_sanction->description;
			}
		}

		echo "<tr>\n";
		echo "<th colspan='2'>$lig->nature</th>\n";
		echo "</tr>\n";

		echo "<tr class='lig-1'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Description&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		echo "<textarea name='no_anti_inject_description' cols='30' onchange='changement();'>$description</textarea>\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr class='lig1'>\n";
		echo "<td colspan='2'>\n";
		echo "<input type='submit' name='enregistrer_sanction' value='Enregistrer' />\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "</table>\n";
	}
	else {
		echo "<p style='color:red;'>Type de sanction inconnu.</p>\n";
	}
}
?>