<?php
/*
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
//==============================
// PREPARATIFS boireaus 20080422
// Pour passer à no_anti_inject comme pour les autres saisies d'appréciations
// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$mode_commentaire_20080422="";
//$mode_commentaire_20080422="no_anti_inject";

if($mode_commentaire_20080422=="no_anti_inject") {
	$variables_non_protegees = 'yes';
}
//==============================

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
	die("Le module n'est pas activé.");
}

require('cc_lib.php');

unset($id_racine);
$id_racine = isset($_POST["id_racine"]) ? $_POST["id_racine"] : (isset($_GET["id_racine"]) ? $_GET["id_racine"] : NULL);
// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
    header("Location: index.php?msg=$mess");
    die();
}

$appel_cahier_notes=mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe=mysql_result($appel_cahier_notes, 0, 'id_groupe');
$current_group=get_group($id_groupe);
$periode_num=mysql_result($appel_cahier_notes, 0, 'periode');
include "../lib/periodes.inc.php";

unset($id_dev);
$id_dev = isset($_POST["id_dev"]) ? $_POST["id_dev"] : (isset($_GET["id_dev"]) ? $_GET["id_dev"] : NULL);
if(!isset($id_dev)) {
	$mess="$nom_cc non précisé.<br />";
	header("Location: index_cc.php?id_racine=$id_racine&msg=$mess");
	die();
}

$sql="SELECT * FROM cc_dev WHERE id='$id_dev' AND id_groupe='$id_groupe';";
$query=mysqli_query($GLOBALS["mysqli"], $sql);
if($query) {
	$id_cn_dev=mysql_result($query, 0, 'id_cn_dev');
	$nom_court_dev=mysql_result($query, 0, 'nom_court');
	$nom_complet_dev=mysql_result($query, 0, 'nom_complet');
	$description_dev=mysql_result($query, 0, 'description');
}
else {
	header("Location: index.php?msg=".rawurlencode("Le numéro de devoir n est pas associé à ce groupe."));
	die();
}

unset($id_eval);
$id_eval = isset($_POST["id_eval"]) ? $_POST["id_eval"] : (isset($_GET["id_eval"]) ? $_GET["id_eval"] : NULL);
if(!isset($id_eval)) {
	header("Location: index.php?msg=".rawurlencode("Numéro d évaluation non valide."));
	die();
}

$sql="SELECT * FROM cc_eval WHERE id='$id_eval' AND id_dev='$id_dev';";
$query=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($query)==0) {
	$mess="L'évaluation n°$id_eval n'est pas associée au $nom_cc n°$id_dev.<br />";
	header("Location: index_cc.php?id_racine=$id_racine&msg=$mess");
	die();
}

$nom_court=mysql_result($query, 0, 'nom_court');
$nom_complet=mysql_result($query, 0, 'nom_complet');
$description=mysql_result($query, 0, 'description');
$display_date=mysql_result($query, 0, 'date');
$note_sur=mysql_result($query, 0, 'note_sur');

$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "classe");

if(isset($_GET['export_csv'])) {
	$csv="INFO_DEV;$id_dev;$nom_court_dev;$nom_complet_dev;;;".";\r\n";
	$csv.="INFO_EVAL;$id_eval;$nom_court;$nom_complet;".formate_date($display_date).";$note_sur;".";\r\n";

	$sql="SELECT cc.*, c.classe, e.nom, e.prenom FROM cc_notes_eval cc, classes c, eleves e, j_eleves_classes jec WHERE cc.id_eval='$id_eval' AND cc.login=e.login AND cc.login=jec.login AND jec.id_classe=c.id AND jec.periode='$periode_num' ORDER BY e.login;";
	//echo "$sql<br />";
	$res_note=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_note)>0) {
		while($lig_note=mysqli_fetch_object($res_note)) {
			if($lig_note->statut=='v') {
				$note_enr[$lig_note->login]='';
			}
			elseif($lig_note->statut!='') {
				$note_enr[$lig_note->login]=$lig_note->statut;
			}
			else {
				$note_enr[$lig_note->login]=$lig_note->note;
			}
			$commentaire[$lig_note->login]=$lig_note->comment;
			$csv.="ELEVE;$lig_note->login;$lig_note->nom;$lig_note->prenom;$lig_note->classe;".strtr($note_enr[$lig_note->login],".",",").";".preg_replace('/[\r\n]/',' ',$lig_note->comment).";\r\n";
		}
	}

	$nom_fic="cc_dev_".$id_dev."_eval_".$id_eval."_".date("dmY").".csv";
	send_file_download_headers('text/x-csv',$nom_fic);
	//echo $csv;
	echo echo_csv_encoded($csv);
	die();
}

$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];

//debug_var();
//-------------------------------------------------------------------------------------------------------------------

if (isset($_POST['notes'])) {
	check_token();

	$temp = $_POST['notes']." 1";
	//echo "<pre>";
	//echo "\$temp=$temp<br />";
	$temp = my_ereg_replace("\\\\r","\r",$temp);
	$temp = my_ereg_replace("\\\\n","\n",$temp);
	//echo "\$temp=$temp<br />";
	//echo "</pre>";
	$longueur = mb_strlen($temp);
	$i = 0;
	$fin_note = 'yes';
	$indice = $_POST['debut_import']-2;
	$tempo = '';
	if(!isset($note_sur_dev_choisi)) {$note_sur_dev_choisi=20;}
	while (($i < $longueur) and ($indice < $_POST['fin_import'])) {
		$car = mb_substr($temp, $i, 1);
		//echo "<p>\$car='$car'<br />";
		//if (my_ereg ("^[0-9\.\,\a-z\A-Z\-]{1}$", $car)) {
		if (my_ereg('^[0-9.,a-zA-Z-]{1}$', $car)) {
			if (($fin_note=='yes') or ($i == $longueur-1)) {
				$fin_note = 'no';
				if (is_numeric($tempo)) {
					//echo "is_numeric($tempo)<br />";
					if ($tempo <= $note_sur_dev_choisi) {
						$note_import[$indice] = $tempo;
						$indice++;
					} else {
						$note_import[$indice] = "0";
						$indice++;
					}
				} else {
					//echo "NO is_numeric($tempo)<br />";
					$note_import[$indice] = $tempo;
					$indice++;
				}
				$tempo = '';
			}
			$tempo=$tempo.$car;
			//echo "\$tempo='$tempo'<br />";
		} else {
			$fin_note = 'yes';
		}
		//echo "\$fin_note=$fin_note<br />";
		$i++;
	}
}



if (isset($_POST['is_posted'])) {
	check_token();

	//=========================
	// AJOUT: boireaus 20071010
	$log_eleve=$_POST['log_eleve'];
	$note_eleve=$_POST['note_eleve'];
	if($mode_commentaire_20080422!="no_anti_inject") {
		$comment_eleve=$_POST['comment_eleve'];
	}
	//=========================

	$indice_max_log_eleve=$_POST['indice_max_log_eleve'];

	//for($i=0;$i<count($log_eleve);$i++){
	for($i=0;$i<$indice_max_log_eleve;$i++){
		if(isset($log_eleve[$i])) {
			// La période est-elle ouverte? On s'en fiche: les évaluations cumul peuvent être à cheval sur plusieurs périodes avant de donner lieu à une note dans le carnet de notes
			$reg_eleve_login=$log_eleve[$i];
			if(isset($current_group["eleves"][$periode_num]["users"][$reg_eleve_login]["classe"])){
				$id_classe = $current_group["eleves"][$periode_num]["users"][$reg_eleve_login]["classe"];
				//if ($current_group["classe"]["ver_periode"][$id_classe][$periode_num] == "N") {
					$note=$note_eleve[$i];
					$elev_statut='';

					//==============================
					// PREPARATIFS boireaus 20080422
					// Pour passer à no_anti_inject comme pour les autres saisies d'appréciations
					if($mode_commentaire_20080422!="no_anti_inject") {
						// Problème: les accents sont codés en HTML...
						$comment=$comment_eleve[$i];
						// Cela fonctionne chez moi avec cette correction (accents, apostrophes et retours à la ligne):
						$comment=addslashes(my_ereg_replace('(\\\r\\\n)+',"\r\n",my_ereg_replace("&#039;","'",html_entity_decode($comment))));
					}
					else {
						if (isset($NON_PROTECT["comment_eleve".$i])){
							$comment = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["comment_eleve".$i]));
						}
						else{
							$comment = "";
						}
						//echo "$i: $comment<br />";
						// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
						$comment=my_ereg_replace('(\\\r\\\n)+',"\r\n",$comment);
					}
					//==============================

					//echo "$reg_eleve_login : $note <br />";

					if (($note == 'disp')||($note == 'd')) {
						$note = '0';
						$elev_statut = 'disp';
					}
					elseif (($note == 'abs')||($note == 'a')) {
						$note = '0';
						$elev_statut = 'abs';
					}
					elseif (($note == '-')||($note == 'n')) {
						$note = '0';
						$elev_statut = '-';
					}
					elseif (my_ereg ("^[0-9\.\,]{1,}$", $note)) {
						$note = str_replace(",", ".", "$note");
						$appel_note_sur = mysqli_query($GLOBALS["mysqli"], "SELECT note_sur FROM cc_eval WHERE id='$id_eval'");
						$note_sur_verif = mysql_result($appel_note_sur,0 ,'note_sur');
						if (($note < 0) or ($note > $note_sur_verif)) {
							$note = '';
							$elev_statut = 'v';
						}
					}
					else {
						$note = '';
						$elev_statut = 'v';
					}

					$test_eleve_note_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cc_notes_eval WHERE (login='$reg_eleve_login' AND id_eval = '$id_eval')");
					$test = mysqli_num_rows($test_eleve_note_query);
					if ($test != "0") {
						$sql="UPDATE cc_notes_eval SET comment='".$comment."', note='$note',statut='$elev_statut' WHERE (login='".$reg_eleve_login."' AND id_eval='".$id_eval."')";
						//echo "$sql<br />";
						$register = mysqli_query($GLOBALS["mysqli"], $sql);
					} else {
						$sql="INSERT INTO cc_notes_eval SET login='".$reg_eleve_login."', id_eval='".$id_eval."',note='".$note."',statut='".$elev_statut."',comment='".$comment."'";
						//echo "$sql<br />";
						$register = mysqli_query($GLOBALS["mysqli"], $sql);
					}

				//}
			}
		}
	}

	/*
    //==========================================================
    // Ajout d'un test:
    // Si on modifie un devoir alors que des notes ont été reportées sur le bulletin, il faut penser à mettre à jour la recopie vers le bulletin.
    $sql="SELECT 1=1 FROM matieres_notes WHERE periode='".$periode_num."' AND id_groupe='".$id_groupe."';";
    $test_bulletin=mysql_query($sql);
    if(mysql_num_rows($test_bulletin)>0) {
        $msg=" ATTENTION: Des notes sont présentes sur le bulletin.<br />Si vous avez modifié ou ajouté des notes, pensez à mettre à jour la recopie vers le bulletin.";
    }
    //==========================================================
	*/
	$affiche_message = 'yes';
}

$message_enregistrement = "Les modifications ont été enregistrées !";
$themessage  = 'Des notes ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Saisie des notes CC";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();

//unset($_SESSION['chemin_retour']);

?>
<script type="text/javascript" language=javascript>
chargement = false;
<?php
if (isset($_POST['debut_import'])) {
	//echo "DEBUG: \$_POST['debut_import']=".$_POST['debut_import']."<br />";
	$temp = $_POST['debut_import']-1;
	if ((isset($note_import[$temp])) and ($note_import[$temp] != '')) {echo "change = 'yes';";} else {echo "change = 'no';";}
} else {
	echo "change = 'no';";
}
?>
</script>

<?php
echo "<form enctype=\"multipart/form-data\" name= \"form0\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
echo "<p class='bold'>\n";
echo "<a href=\"index_cc.php?id_racine=$id_racine\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";

$sql="SELECT * FROM cc_eval WHERE id_dev='$id_dev' ORDER BY date, nom_court;";
$res_eval=mysqli_query($GLOBALS["mysqli"], $sql);
$id_eval_prec=-1;
$id_eval_suiv=-1;
$indice_id_eval_courant=-1;
if(mysqli_num_rows($res_eval)>1) {
	$tmp_id_eval="";
	$temoin_eval="n";
	$liste_option="";
	$cpt=0;
	while($lig_eval=mysqli_fetch_object($res_eval)) {
		if($temoin_eval=="y") {
			$id_eval_suiv=$lig_eval->id;
			$temoin_eval="n";
		}

		if($lig_eval->id==$id_eval) {
			if($tmp_id_eval!="") {
				$id_eval_prec=$tmp_id_eval;
			}
			$temoin_eval="y";

			$liste_option.="<option value='$lig_eval->id' selected='true'>$lig_eval->nom_court</option>\n";

			$indice_id_eval_courant=$cpt;
		}
		else {
			$liste_option.="<option value='$lig_eval->id'>$lig_eval->nom_court</option>\n";
		}

		$tmp_id_eval=$lig_eval->id;
		$cpt++;
	}

	echo "| Evaluation ";
	if($id_eval_prec!="-1") {
		echo " <a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$id_dev&amp;id_eval=$id_eval_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">précédente</a>";
	}
	//echo " <select name='id_eval' onchange=\"document.forms['form0'].submit()\">\n";
	echo " <select name='id_eval' id='id_eval_change' onchange=\"confirm_changement_eval(change, '$themessage');\">\n";
	echo $liste_option;
	echo "</select>\n";
	if($id_eval_suiv!="-1") {
		echo " <a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$id_dev&amp;id_eval=$id_eval_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\"> suivante</a>";
	}

	echo "<input type='hidden' name='id_dev' value='$id_dev' />\n";
	echo "<input type='hidden' name='id_racine' value='$id_racine' />\n";

}
echo " | Export <a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$id_dev&amp;id_eval=$id_eval&amp;export_csv=y'>CSV</a>";
echo "</p>\n";

echo "<script type='text/javascript'>
	// Initialisation faite plus haut
	//change='no';

	function confirm_changement_eval(thechange, themessage)
	{
		if (!(thechange)) {thechange='no';}
		if (thechange != 'yes') {
			document.forms['form0'].submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.forms['form0'].submit();
			}
			else{
				document.getElementById('id_eval_change').selectedIndex=$indice_id_eval_courant;
			}
		}
	}
</script>\n";

echo "</form>\n";

echo "<h2>".$current_group['name']." (<i>".$current_group['description']."</i>) en ".$current_group['classlist_string']."</h2>\n";

//echo "<h2>$nom_cc n°$id_dev&nbsp;: $nom_court_dev (<i>$nom_complet_dev</i>)</h2>\n";
//echo "<h3>Evaluation n°$id_eval&nbsp;: $nom_court (<i>$nom_complet</i>) sur $note_sur du ".formate_date($display_date)."</h3>\n";

echo "<h3><b>$nom_cc</b>&nbsp;: $nom_court_dev (<i>$nom_complet_dev</i>)</h3>\n";
echo "<h4><b>Evaluation</b>&nbsp;: $nom_court (<i>$nom_complet</i>) sur $note_sur du ".formate_date($display_date)."</h4>\n";

echo "<form enctype=\"multipart/form-data\" name= \"form1\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

echo add_token_field();
echo "<center><input type='submit' value='Enregistrer' /></center>\n";

// Couleurs utilisées
$couleur_devoirs = '#AAE6AA';
$couleur_moy_cont = '#96C8F0';
$couleur_moy_sous_cont = '#FAFABE';
$couleur_calcul_moy = '#AAAAE6';

$note_sur_verif = $note_sur;
/*
if ($id_eval != 0) {
        $appel_note_sur = mysql_query("SELECT NOTE_SUR FROM cn_devoirs WHERE id = '$id_eval'");
        $note_sur_verif = mysql_result($appel_note_sur,'0' ,'note_sur');
	//echo "<p class='cn'>Taper une note de 0 à 20 pour chaque élève, ou à défaut le code 'abs' pour 'absent', le code 'disp' pour 'dispensé', le code '-' pour absence de note.</p>\n";
	echo "<p class='cn'>Taper une note de 0 à ".$note_sur_verif." pour chaque élève, ou à défaut le code 'a' pour 'absent', le code 'd' pour 'dispensé', le code '-' ou 'n' pour absence de note.</p>\n";
	echo "<p class='cn'>Vous pouvez également <b>importer directement vos notes par \"copier/coller\"</b> à partir d'un tableur ou d'une autre application : voir tout en bas de cette page.</p>\n";

}
echo "<p class=cn><b>Enseignement : ".$current_group['description']." (" . $current_group["classlist_string"] . ")";
echo "</b></p>\n";
*/

//=============================================================
// MODIF: boireaus

echo "<div id='div_q_p' style='position: fixed; top: 220px; right: 200px; text-align:center;'>\n";
echo "<div id='div_photo_eleve' style='text-align:center; display:none;'></div>\n";
echo "</div>\n";

echo "
<script type='text/javascript' language='JavaScript'>

function verifcol(num_id){
	document.getElementById('n'+num_id).value=document.getElementById('n'+num_id).value.toLowerCase();
	if(document.getElementById('n'+num_id).value=='a'){
		document.getElementById('n'+num_id).value='abs';
	}
	if(document.getElementById('n'+num_id).value=='d'){
		document.getElementById('n'+num_id).value='disp';
	}
	if(document.getElementById('n'+num_id).value=='n'){
		document.getElementById('n'+num_id).value='-';
	}
	note=document.getElementById('n'+num_id).value;
	if((note!='-')&&(note!='disp')&&(note!='abs')&&(note!='')){
		//if((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0))){
		if(((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0)))||
	((note.search(/^[0-9,]+$/)!=-1)&&(note.lastIndexOf(',')==note.indexOf(',',0)))){
			if((note>".$note_sur_verif.")||(note<0)){
				couleur='red';
			}
			else{
				couleur='$couleur_devoirs';
			}
		}
		else{
			couleur='red';
		}
	}
	else{
		couleur='$couleur_devoirs';
	}
	eval('document.getElementById(\'td_'+num_id+'\').style.background=couleur');
}

function affiche_div_photo() {
	if(document.getElementById('div_photo_eleve').style.display=='none') {
		document.getElementById('div_photo_eleve').style.display='';
	}
	else {
		document.getElementById('div_photo_eleve').style.display='none';
	}
}

function affiche_photo(photo,nom_prenom) {
	document.getElementById('div_photo_eleve').innerHTML='<img src=\"'+photo+'\" width=\"150\" alt=\"Photo\" /><br />'+nom_prenom;
}

affiche_div_photo();

</script>
";
//=============================================================

/*
$i=0;
while ($i < $nb_dev) {
	$nocomment[$i]='yes';
	$i++;
}
*/
// Tableau destiner à stocker l'id du champ de saisie de note (n$num_id) correspondant à l'élève $i
$indice_ele_saisie=array();

$i = 0;
$num_id=10;
$current_displayed_line = 0;

// On commence par mettre la liste dans l'ordre souhaité
if ($order_by != "classe") {
	$liste_eleves = $current_group["eleves"][$periode_num]["users"];
} else {
	// Ici, on tri par classe
	// On va juste créer une liste des élèves pour chaque classe
	$tab_classes = array();
	foreach($current_group["classes"]["list"] as $classe_id) {
		$tab_classes[$classe_id] = array();
        //echo "\$tab_classes[$classe_id]=".$tab_classes[$classe_id]."<br />";
	}
	// On passe maintenant élève par élève et on les met dans la bonne liste selon leur classe
	foreach($current_group["eleves"][$periode_num]["list"] as $e_login) {
		$classe = $current_group["eleves"][$periode_num]["users"][$e_login]["classe"];
		$tab_classes[$classe][$e_login] = $current_group["eleves"][$periode_num]["users"][$e_login];
	}
	// On met tout ça à la suite
	$liste_eleves = array();
	foreach($current_group["classes"]["list"] as $classe_id) {
		$liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
	}
}

$prev_classe = null;

$sql="SELECT * FROM cc_notes_eval WHERE id_eval='$id_eval' ORDER BY login;";
//echo "$sql<br />";
$res_note=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_note)>0) {
	while($lig_note=mysqli_fetch_object($res_note)) {
		if($lig_note->statut=='v') {
			$note_enr[$lig_note->login]='';
		}
		elseif($lig_note->statut!='') {
			$note_enr[$lig_note->login]=$lig_note->statut;
		}
		else {
			$note_enr[$lig_note->login]=$lig_note->note;
		}
		$commentaire[$lig_note->login]=$lig_note->comment;
		//echo "DEBUG: \$note_enr[$lig_note->login]=".$note_enr[$lig_note->login]."<br />";
	}
}
/*
if(isset($note_import)) {
	foreach($note_import as $key => $value) {
		echo "\$note_import[$key]=$value<br />";
	}
}
*/
echo "<table class='boireaus' summary='Notes'>\n";
echo "<tr>\n";
echo "<th><a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$id_dev&amp;id_eval=$id_eval&amp;order_by=nom' onclick=\"return confirm_abandon (this, change,'$themessage')\">Nom Prénom</a></th>\n";
echo "<th><a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine&amp;id_dev=$id_dev&amp;id_eval=$id_eval&amp;order_by=classe' onclick=\"return confirm_abandon (this, change,'$themessage')\">Classe</a></th>\n";
echo "<th>Note</th>\n";
echo "<th>Commentaire</th>\n";
echo "</tr>\n";

// A FAIRE: AJOUTER EN ENTETE Note_sur, lien vers config de l'évaluation,...

$alt=1;
foreach ($liste_eleves as $eleve) {
	$eleve_login[$i] = $eleve["login"];
	$eleve_nom[$i] = $eleve["nom"];
	$eleve_prenom[$i] = $eleve["prenom"];
	$eleve_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["classe"];
	$eleve_id_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["id"];

	$elenoet="";
	$sql="SELECT elenoet FROM eleves WHERE login='".$eleve_login[$i]."';";
	$res_elenoet=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_elenoet)>0) {
		$tmp_lig=mysqli_fetch_object($res_elenoet);
		$elenoet=$tmp_lig->elenoet;
	}

	$alt=$alt*(-1);
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<td>";
	echo "<input type='hidden' name=\"log_eleve[$i]\" id='log_eleve_$i' value='$eleve_login[$i]' />\n";
	echo $eleve_nom[$i]." ".$eleve_prenom[$i]."</td>\n";
	echo "<td>$eleve_classe[$i]</td>\n";

	echo "<td id='td_$num_id'>\n";
	/*
	if ((isset($note_import[$current_displayed_line])) and  ($note_import[$current_displayed_line] != '')) {
		echo "\$note_import[$current_displayed_line]=$note_import[$current_displayed_line]<br />";
	}
	echo "\$eleve_login[$i]=$eleve_login[$i]<br />";
	if(isset($note_enr[$eleve_login[$i]])) {echo "\$note_enr[$eleve_login[$i]]=".$note_enr["$eleve_login[$i]"]."<br />";}
	*/

	echo "<input type='text' name='note_eleve[$i]' size='4' autocomplete='off' id=\"n".$num_id."\" onKeyDown=\"clavier(this.id,event);\" onfocus=\"javascript:this.select()";
	if($elenoet!="") {echo ";affiche_photo('".nom_photo($elenoet)."','".addslashes(my_strtoupper($eleve_nom[$i])." ".casse_mot($eleve_prenom[$i],'majf2'))."')";}
	echo "\" onchange=\"verifcol($num_id);changement();\" value='";
	if ((isset($note_import[$current_displayed_line])) and  ($note_import[$current_displayed_line] != '')) {
		echo $note_import[$current_displayed_line];
	}
	elseif(isset($note_enr[$eleve_login[$i]])) {
		echo $note_enr[$eleve_login[$i]];
	}
	echo "' />\n";
	"</td>\n";

	echo "<td>\n";
	echo "<textarea id=\"n1".$num_id."\" onKeyDown=\"clavier(this.id,event);\" name='comment_eleve[$i]' rows='1' cols='60' class='wrap' onfocus=\"javascript:this.select()";
	if($elenoet!="") {echo ";affiche_photo('".nom_photo($elenoet)."','".addslashes(my_strtoupper($eleve_nom[$i])." ".casse_mot($eleve_prenom[$i],'majf2'))."')";}
	echo "\" onchange=\"changement()\">";
	if(isset($commentaire[$eleve_login[$i]])) {echo $commentaire[$eleve_login[$i]];}
	echo "</textarea>\n";
	"</td>\n";
	echo "</tr>\n";
	$num_id++;
	$i++;
	$current_displayed_line++;
}
echo "</table>\n";
echo "<center><input type='submit' value='Enregistrer' /></center>\n";
echo "<input type='hidden' name='is_posted' value='1' />\n";
echo "<input type='hidden' name='id_dev' value='$id_dev' />\n";
echo "<input type='hidden' name='id_eval' value='$id_eval' />\n";
echo "<input type='hidden' name='id_racine' value='$id_racine' />\n";

echo "<input type='hidden' name='indice_max_log_eleve' value='$i' />\n";
echo "</form>\n";

//================================================

echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;\">\n";
echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method='post'>\n";
echo add_token_field();
echo "<h3 class='gepi'>Importation directe des notes par copier/coller à partir d'un tableur</h3>\n";
echo "<table summary=\"Tableau d'import\"><tr>\n";
echo "<td>De la ligne : ";
echo "<select name='debut_import' size='1'>\n";
$k = 1;
while ($k < $current_displayed_line+1) {
	echo "<option value='$k'>$k</option>\n";
	$k++;
}
echo "</select>\n";

echo "<br /> à la ligne : \n";
echo "<SELECT name='fin_import' size='1'>\n";
$k = 1;
while ($k < $current_displayed_line+1) {
	echo "<option value='$k'";
	if ($k == $current_displayed_line) echo " SELECTED ";
	echo ">$k</option>\n";
	$k++;
}
echo "</select>\n";
echo "</td><td>\n";
echo "Coller ci-dessous les données à importer : <br />\n";
if (isset($_POST['notes'])) {$notes=preg_replace("/\\\\n/","\n",preg_replace("/\\\\r/","\r",$_POST['notes']));} else {$notes='';}
//echo "<textarea name='notes' rows='3' cols='40' wrap='virtual'>$notes</textarea>\n";
echo "<textarea name='notes' rows='3' cols='40' class='wrap'>$notes</textarea>\n";
echo "</td></tr></table>\n";

echo "<input type='hidden' name='id_dev' value='$id_dev' />\n";
echo "<input type='hidden' name='id_eval' value='$id_eval' />\n";
echo "<input type='hidden' name='id_racine' value='$id_racine' />\n";

//=========================
// AJOUT: boireaus 20071128
echo "<input type='hidden' name='order_by' value='$order_by' />\n";
//=========================

echo "<center><input type='submit' value='Importer'  onclick=\"return confirm_abandon (this, change, '$themessage')\" /></center>\n";
echo "<p><b>Remarque importante :</b> l'importation ne prend en compte que les élèves dont le nom est affiché ci-dessus !<br />Soyez donc vigilant à ne coller que les notes de ces élèves, dans le bon ordre.</p>\n";
echo "</form></fieldset>\n";

//=======================================================
// MODIF: boireaus
// Avertissement redescendu ici pour éviter d'avoir une page web avec une section Javascript avant même la balise <html>
if (isset($_POST['notes'])) {
	echo "<script type=\"text/javascript\" language=\"javascript\">
	<!--
	alert(\"Attention, les notes importées ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton 'Enregistrer') !\");
	changement();
	//-->
	</script>\n";
}

/*
	// Ajout delineau -> fonctionnalité de copier/coller d'appréciations
  if (isset($_POST['appreciations'])) {
  	echo "<script type=\"text/javascript\" language=\"javascript\">
  	<!--
  	alert(\"Attention, les appréciations importées ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton 'Enregistrer') !\");
  	//-->
  	</script>\n";
  }
	// Fin ajout delineau -> fonctionnalité de copier/coller d'appréciations

	//=======================================================

}

// Ajout delineau -> fonctionnalité de copier/coller d'appréciations
if ($id_eval) {
	echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;\">\n";
	echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=post>\n";
	echo add_token_field();
	echo "<h3 class='gepi'>Importation directe des appréciations par copier/coller à partir d'un tableur</h3>\n";
	echo "<table summary=\"Tableau d'import\"><tr>\n";
	echo "<td>De la ligne : ";
		echo "<SELECT name='debut_import' size='1'>\n";
	$k = 1;
	while ($k < $current_displayed_line+1) {
		echo "<option value='$k'>$k</option>\n";
		$k++;
	}
	echo "</select>\n";

	echo "<br /> à la ligne : \n";
	echo "<SELECT name='fin_import' size='1'>\n";
	$k = 1;
	while ($k < $current_displayed_line+1) {
		echo "<option value='$k'";
		if ($k == $current_displayed_line) echo " SELECTED ";
		echo ">$k</option>\n";
		$k++;
	}
	echo "</select>\n";
	echo "</td><td>\n";
	echo "Coller ci-dessous les données à importer&nbsp;: <br />\n";
	//if (isset($_POST['appreciations'])) $appreciations = $_POST['appreciations']; $appreciations='';
	if (isset($_POST['appreciations'])) {$appreciations = preg_replace("/\\\\n/","\n",preg_replace("/\\\\r/","\r",$_POST['appreciations']));} else {$appreciations='';}
	echo "<textarea name='appreciations' rows='3' cols='40' class='wrap'>$appreciations</textarea>\n";
	echo "</td></tr></table>\n";
	echo "<input type='hidden' name='id_conteneur' value='$id_conteneur' />\n";
	echo "<input type='hidden' name='id_eval' value='$id_eval' />\n";
	echo "<input type='hidden' name='order_by' value='$order_by' />\n";
	echo "<center><input type='submit' value='Importer'  onclick=\"return confirm_abandon (this, change, '$themessage')\" /></center>\n";
	echo "<p><b>Remarque importante :</b> l'importation ne prend en compte que les élèves dont le nom est affiché ci-dessus !<br />Soyez donc vigilant à ne coller que les appréciations de ces élèves, dans le bon ordre.</p>\n";
	echo "</form></fieldset>\n";
}
// Fin ajout delineau -> fonctionnalité de copier/coller d'appréciations

*/
?>
<br />
* En conformité avec la CNIL, le professeur s'engage à ne faire figurer dans le carnet de notes que des notes et commentaires portés à la connaissance de l'élève (note et commentaire portés sur la copie, ...).
<script type="text/javascript" language="javascript">
chargement = true;

// La vérification ci-dessous est effectuée après le remplacement des notes supérieures à 20 par des zéros.
// Ces éventuelles erreurs de frappe ne sauteront pas aux yeux.
for(i=10;i<<?php echo $num_id; ?>;i++){
	eval("verifcol("+i+")");
}

// On donne le focus à la première cellule lors du chargement de la page:
if(document.getElementById('n10')){
	document.getElementById('n10').focus();
}

</script>
<?php require("../lib/footer.inc.php");?>
