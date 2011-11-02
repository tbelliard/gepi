<?php

/* $Id: init_xml_lib.php 7602 2011-08-07 14:17:29Z crob $ */

$debug_import="n";

function traite_utf8($chaine) {
	// On passe par cette fonction pour pouvoir desactiver rapidement ce traitement s'il ne se revele plus necessaire
	//$retour=$chaine;

	// mb_detect_encoding($chaine . 'a' , 'UTF-8, ISO-8859-1');

	//$retour=utf8_decode($chaine);
	// utf8_decode() va donner de l'iso-8859-1 d'ou probleme sur quelques caracteres oe et OE essentiellement (7 caracteres diffèrent).

	/*
	Différences ISO 8859-15 ? ISO 8859-1
	Position
			0xA4  0xA6  0xA8  0xB4  0xB8  0xBC  0xBD  0xBE
	8859-1
			?     ?     ?     ?     ?     ?     ?     ?
	8859-15
			¤     ¦     ¨     ´     ¸     ¼     ½     ¾
	*/

	//$retour=recode_string("utf8..iso-8859-15", $chaine);
	// recode_string est absent la plupart du temps

	$retour=utf8_decode($chaine);

	return $retour;
}

/*
//================================================
// Correspondances de caractères accentués/désaccentués
$liste_caracteres_accentues   ="ÂÄÀÁÃÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕØ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõø¨ûüùúýÿ¸";
$liste_caracteres_desaccentues="AAAAAACEEEEIIIINOOOOOOSUUUUYYZaaaaaaceeeeiiiinooooooosuuuuyyz";
//================================================

function remplace_accents($chaine) {
	global $liste_caracteres_accentues, $liste_caracteres_desaccentues;
	$retour=strtr(preg_replace("/Æ/","AE",preg_replace("/æ/","ae",preg_replace("/¼/","OE",preg_replace("/½/","oe","$chaine"))))," '$liste_caracteres_accentues","__$liste_caracteres_desaccentues");
	return $retour;
}
*/

function champ_select_prof($defaut='', $avec_nb_mat='n', $form_onchange_submit='', $nom_champ='login_prof', $etat='actif') {
	global $themessage;
	global $indice_login_prof;
	$retour="";

	$retour.="<select id='$nom_champ' name='$nom_champ'";
	//if($form_onchange_submit!="") {$retour.=" onchange=\"document.forms['form1'].submit()\"";}
	if($form_onchange_submit!="") {$retour.=" onchange=\"confirm_changement_prof(change, '$form_onchange_submit', '$themessage');\"";}
	$retour.=">\n";
	$retour.="<option value=''";
	if($defaut=='') {$retour.=" selected";}
	$retour.=">---</option>\n";
	$sql="SELECT * FROM utilisateurs WHERE statut='professeur'";
	if(($etat=='actif')||($etat=='inactif')) {$sql.=" AND etat='$etat'";}
	$sql.=" ORDER BY nom, prenom;";
	$res=mysql_query($sql);
	$tab=array();
	$cpt=0;
	$indice_login_prof=0;
	$l_max=0;
	while($lig=mysql_fetch_object($res)) {
		$tab[$cpt]['login']=$lig->login;
		$tab[$cpt]['nom_prenom']=$lig->nom." ".casse_mot($lig->prenom,'majf2');
		if(strlen($lig->nom." ".$lig->prenom)>$l_max) {
			$l_max=strlen($lig->nom." ".$lig->prenom);
		}
		if($avec_nb_mat=='y') {
			$sql="SELECT * FROM j_professeurs_matieres WHERE id_professeur='".$lig->login."';";
			//$retour.="$sql<br />";
			$res2=mysql_query($sql);
			$tab[$cpt]['nb_matieres']=mysql_num_rows($res2);
			//$retour.="\$tab[$cpt]['nb_matieres']=".$tab[$cpt]['nb_matieres']."<br />";
		}
		$cpt++;
	}
	for($i=0;$i<count($tab);$i++) {
		$retour.="<option value='".$tab[$i]['login']."'";
		if($defaut==$tab[$i]['login']) {
			$retour.=" selected";
			$indice_login_prof=$i+1;
		}
		$retour.=">".$tab[$i]['nom_prenom'];
		if($avec_nb_mat=='y') {
			$retour.=" ";
			for($loop=0;$loop<$l_max-strlen($tab[$i]['nom_prenom']);$loop++) {$retour.="&nbsp;";}
			if($tab[$i]['nb_matieres']>0) {$retour.="(".$tab[$i]['nb_matieres']." matière(s))";}
		}
		$retour.="</option>\n";
	}
	$retour.="</select>\n";

	return $retour;
}

function js_confirm_changement_prof($formulaire, $indice_login_prof) {
	$retour="<script type='text/javascript'>
	var change='no';

	function confirm_changement_prof(thechange, formulaire, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.forms['$formulaire'].submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.forms['$formulaire'].submit();
			}
			else{
				document.getElementById('login_prof_passage_autre_prof').selectedIndex=$indice_login_prof;
			}
		}
	}
</script>\n";
	return $retour;
}

function reordonner_matieres($login_prof='', $avec_echo='n') {
	$retour="";

	$sql="SELECT * FROM j_professeurs_matieres ";
	if($login_prof!='') {$sql.="WHERE id_professeur='$login_prof' ";}
	$sql.="ORDER BY id_professeur, ordre_matieres, id_matiere;";
	//echo "$sql<br />\n";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$nb_corrections=0;
		$nb_erreurs=0;
		$prof_precedent="";
		while($lig=mysql_fetch_object($res)) {
			if($lig->id_professeur!=$prof_precedent) {
				$prof_precedent=$lig->id_professeur;
				$tab_matiere=array();
				$tab_ordre_matieres=array();
				$cpt=1;
			}

			if($avec_echo=='y') {
				if(in_array($lig->ordre_matieres,$tab_ordre_matieres)) {
					$retour.="Rang $lig->ordre_matieres de matière en doublon pour $lig->id_professeur (<i>$lig->id_matiere</i>)<br />\n";
					$nb_corrections++;
				}
			}
			$tab_ordre_matieres[]=$lig->ordre_matieres;
			$sql="UPDATE j_professeurs_matieres SET ordre_matieres='$cpt' WHERE id_professeur='$lig->id_professeur' AND id_matiere='$lig->id_matiere';";
			$update=mysql_query($sql);
			if(!$update) {$nb_erreurs++;}
			$cpt++;
		}
	}

	return $retour;
}


function champ_select_matiere($defaut='', $avec_nb_prof='n', $form_onchange_submit='') {
	global $themessage;
	global $indice_matiere;
	$retour="";

	$retour.="<select name='matiere'";
	//if($form_onchange_submit!="") {$retour.=" onchange=\"document.forms['form1'].submit()\"";}
	if($form_onchange_submit!="") {$retour.=" onchange=\"confirm_changement_matiere(change, '$form_onchange_submit', '$themessage');\"";}
	$retour.=">\n";
	$retour.="<option value=''";
	if($defaut=='') {$retour.=" selected";}
	$retour.=">---</option>\n";
	$sql="SELECT * FROM matieres ORDER BY matiere;";
	$res=mysql_query($sql);
	$tab=array();
	$cpt=0;
	$indice_matiere=0;
	$l_max=0;
	while($lig=mysql_fetch_object($res)) {
		$tab[$cpt]['matiere']=$lig->matiere;
		$tab[$cpt]['nom_complet']=$lig->nom_complet;
		if(strlen($lig->nom_complet)>$l_max) {
			$l_max=strlen($lig->nom_complet);
		}
		if($avec_nb_prof=='y') {
			$sql="SELECT jpm.* FROM j_professeurs_matieres jpm, utilisateurs u WHERE jpm.id_professeur=u.login AND jpm.id_matiere='".$lig->matiere."' AND u.etat='actif';";
			//$retour.="$sql<br />";
			$res2=mysql_query($sql);
			$tab[$cpt]['nb_profs']=mysql_num_rows($res2);
		}
		$cpt++;
	}
	for($i=0;$i<count($tab);$i++) {
		$retour.="<option value='".$tab[$i]['matiere']."'";
		if($defaut==$tab[$i]['matiere']) {
			$retour.=" selected";
			$indice_matiere=$i+1;
		}
		$retour.=">".$tab[$i]['nom_complet'];
		if($avec_nb_prof=='y') {
			$retour.=" ";
			for($loop=0;$loop<$l_max-strlen($tab[$i]['nom_complet']);$loop++) {$retour.="&nbsp;";}
			if($tab[$i]['nb_profs']>0) {$retour.="(".$tab[$i]['nb_profs']." professeur(s))";}
		}
		$retour.="</option>\n";
	}
	$retour.="</select>\n";

	return $retour;
}

function js_confirm_changement_matiere($formulaire, $indice_matiere) {
	$retour="<script type='text/javascript'>
	var change='no';

	function confirm_changement_matiere(thechange, formulaire, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.forms['$formulaire'].submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.forms['$formulaire'].submit();
			}
			else{
				document.getElementById('matiere_passage_autre_matiere').selectedIndex=$indice_matiere;
			}
		}
	}
</script>\n";
	return $retour;
}

?>
