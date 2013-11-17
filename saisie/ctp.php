<?php
	/*
	*/

	// Hauteur du DIV des appréciations-types
	// Cela conditionne le nombre d'appréciations-types que l'on peut voir simultanément
	$hauteur_div_ctp=10;

	//============================================
	// Dispositif dépendant de la présence des fonctions javascript un peu plus haut comme appliqué dans $mess[$k]:
	//$mess[$k].=" onfocus=\"focus_suivant(".$k.$num_id.");document.getElementById('focus_courant').value='".$k.$num_id."';\"";

	$sql="SELECT * FROM commentaires_types_profs WHERE login='".$_SESSION['login']."' ORDER BY app;";
	//echo "$sql<br />";
	$res_cmt=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res_cmt)>0) {

		$titre_bulle="Commentaires-types";

		//echo "\$titre_bulle=$titre_bulle<br />";

		$texte_bulle="";
		$cpt=0;
		$texte_bulle.="<div class='boireaus'>\n";
		$alt=1;
		while($lig_cmt=mysqli_fetch_object($res_cmt)) {
			$alt=$alt*(-1);
			$texte_bulle.="<div class='lig$alt' style='border:1px solid black; margin: 1px; ' onClick=\"insere_cmt($cpt)\">\n";
			$texte_bulle.=htmlspecialchars($lig_cmt->app)."\n";
			$texte_bulle.="</div>\n";

			echo "<div id='cmt_".$cpt."' style='display:none;'>".htmlspecialchars($lig_cmt->app)."</div>\n";
			$cpt++;
		}
		$texte_bulle.="</div>\n";

		//echo "\$texte_bulle=$texte_bulle<br />";

		$tabdiv_infobulle[]=creer_div_infobulle('div_commtype',$titre_bulle,"",$texte_bulle,"",30,$hauteur_div_ctp,'y','y','n','y');
	}

	echo "<script type='text/javascript'>
function insere_cmt(num) {
	id_focus_courant=document.getElementById('focus_courant').value;

	if(document.getElementById('prenom_eleve_'+id_focus_courant)) {
		prenom_eleve=document.getElementById('prenom_eleve_'+id_focus_courant).value;
	}

	login_eleve='';
	if(document.getElementById('login_eleve_'+id_focus_courant)) {
		login_eleve=document.getElementById('login_eleve_'+id_focus_courant).value;
	}

	app0=document.getElementById('n'+id_focus_courant).value;

	cmt=document.getElementById('cmt_'+num).innerHTML;
	if(document.getElementById('prenom_eleve_'+id_focus_courant)) {
		cmt=cmt.replace('_PRENOM_',prenom_eleve);
	}

	app1=app0+cmt;
	document.getElementById('n'+id_focus_courant).value=app1;

	if((id_groupe!='')&&(login_eleve!='')) {
		ajaxAppreciations(login_eleve, id_groupe, 'n'+id_focus_courant);
	}

	document.getElementById('n'+id_focus_courant).focus();
}

function div_comm_type() {
	//alert('document.getElementById(\'focus_courant\').value='+document.getElementById('focus_courant').value)
	if(document.getElementById('focus_courant').value!='') {
		//afficher_div('div_commtype','y',10,-200);
		afficher_div('div_commtype','y',-100,-250);
	}
}
</script>\n";


	//echo "<a href='#' onClick='return false;' ";
	echo "<a href='saisie_cmnt_type_prof.php' ";
	//echo "onMouseover=\"afficher_div('div_commtype','y',-10,20);\"";
	//echo "onClick='document.forms[0].submit() ;return true;' ";
	echo "onMouseover=\"div_comm_type();\"";
	echo " target='_blank'";
	echo ">";
	echo "<img src='../images/icons/saisie.png' width='16' height='16' alt='Commentaires-types' />";
	echo "</a>\n";

	//echo " - <a href='saisie_cmnt_type_prof.php' target='_blank'>**</a>\n";

	//============================================
?>
