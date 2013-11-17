<?php

$debug_import="n";

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
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$tab=array();
	$cpt=0;
	$indice_login_prof=0;
	$l_max=0;
	while($lig=mysqli_fetch_object($res)) {
		$tab[$cpt]['login']=$lig->login;
		$tab[$cpt]['nom_prenom']=$lig->nom." ".casse_mot($lig->prenom,'majf2');
		if(mb_strlen($lig->nom." ".$lig->prenom)>$l_max) {
			$l_max=mb_strlen($lig->nom." ".$lig->prenom);
		}
		if($avec_nb_mat=='y') {
			$sql="SELECT * FROM j_professeurs_matieres WHERE id_professeur='".$lig->login."';";
			//$retour.="$sql<br />";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			$tab[$cpt]['nb_matieres']=mysqli_num_rows($res2);
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
			for($loop=0;$loop<$l_max-mb_strlen($tab[$i]['nom_prenom']);$loop++) {$retour.="&nbsp;";}
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
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$nb_corrections=0;
		$nb_erreurs=0;
		$prof_precedent="";
		while($lig=mysqli_fetch_object($res)) {
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
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
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
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$tab=array();
	$cpt=0;
	$indice_matiere=0;
	$l_max=0;
	while($lig=mysqli_fetch_object($res)) {
		$tab[$cpt]['matiere']=$lig->matiere;
		$tab[$cpt]['nom_complet']=$lig->nom_complet;
		if(mb_strlen($lig->nom_complet)>$l_max) {
			$l_max=mb_strlen($lig->nom_complet);
		}
		if($avec_nb_prof=='y') {
			$sql="SELECT jpm.* FROM j_professeurs_matieres jpm, utilisateurs u WHERE jpm.id_professeur=u.login AND jpm.id_matiere='".$lig->matiere."' AND u.etat='actif';";
			//$retour.="$sql<br />";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			$tab[$cpt]['nb_profs']=mysqli_num_rows($res2);
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
			for($loop=0;$loop<$l_max-mb_strlen($tab[$i]['nom_complet']);$loop++) {$retour.="&nbsp;";}
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

function ouinon($nombre){
	if($nombre==1){return "O";}elseif($nombre==0){return "N";}else{return "";}
}
function sexeMF($nombre){
	//if($nombre==2){return "F";}else{return "M";}
	if($nombre==2){return "F";}elseif($nombre==1){return "M";}else{return "";}
}

function affiche_debug($texte){
	// Passer à 1 la variable pour générer l'affichage des infos de debug...
	$debug=0;
	if($debug==1){
		echo "<font color='green'>".$texte."</font>";
		flush();
	}
}

function maj_min_comp($chaine){
	$tmp_tab1=explode(" ",$chaine);
	$new_chaine="";
	for($i=0;$i<count($tmp_tab1);$i++){
		$tmp_tab2=explode("-",$tmp_tab1[$i]);
		$new_chaine.=casse_mot($tmp_tab2[0],'majf2');
		for($j=1;$j<count($tmp_tab2);$j++){
			$new_chaine.="-".casse_mot($tmp_tab2[$j],'majf2');
		}
		$new_chaine.=" ";
	}
	$new_chaine=trim($new_chaine);
	return $new_chaine;
}

function maj_ini_prenom($prenom){
	$prenom2="";
	$tab1=explode("-",$prenom);
	for($i=0;$i<count($tab1);$i++){
		if($i>0){
			$prenom2.="-";
		}
		$tab2=explode(" ",$tab1[$i]);
		for($j=0;$j<count($tab2);$j++){
			if($j>0){
				$prenom2.=" ";
			}
			$prenom2.=casse_mot($tab2[$j],'majf2');
		}
	}
	return $prenom2;
}

function extr_valeur($lig){
	unset($tabtmp);
	$tabtmp=explode(">",preg_replace("/</",">",$lig));
	return trim($tabtmp[2]);
}

function info_debug($texte,$mode=0) {
	global $step;
	global $dirname;

	$debug=0;
	if($debug==1) {
		if($mode==1) {
			// On écrase le fichier s'il existait déjà
			$fich_debug=fopen("../backup/".$dirname."/debug_maj_import2.txt","w+");
			fwrite($fich_debug,"$step;$texte;".time()."\n");
			fclose($fich_debug);
		}
		elseif($mode==2) {
			// Affichage d'un lien pour accéder au fichier de debug depuis la page web
			echo "<p><a href='../backup/".$dirname."/debug_maj_import2.txt' target='_blank'>Fichier debug</a></p>";
		}
		else {
			// On complète le fichier
			//$fich_debug=fopen("/tmp/debug_maj_import2.txt","a+");
			$fich_debug=fopen("../backup/".$dirname."/debug_maj_import2.txt","a+");
			fwrite($fich_debug,"$step;$texte;".time()."\n");
			fclose($fich_debug);
		}
	}
}

?>
