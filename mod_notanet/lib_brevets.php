<?php

/*
$Id$
 */

$tab_type_brevet=array();

//$tab_type_brevet[0]="COLLEGE, option de série LV2";
//$tab_type_brevet[1]="COLLEGE, option de série DP6";

// Y a-t-il des options de série? Le document sur Orion ne les mentionne pas.
$tab_type_brevet[0]="GENERALE";

//$tab_type_brevet[2]="COLLEGE, option de série TECHNOLOGIE traditionnelle";
//$tab_type_brevet[3]="COLLEGE, option de série TECHNOLOGIE DP6";
/*
$tab_type_brevet[2]="PROFESSIONNELLE, sans option de série";
$tab_type_brevet[3]="PROFESSIONNELLE, option de série DP6";
*/
$tab_type_brevet[2]="PROFESSIONNELLE";

$tab_type_brevet[4]="PROFESSIONNELLE, option AGRICOLE";

/*
// Séries technologiques supprimées dans le brevet 2013
//$tab_type_brevet[4]="TECHNOLOGIQUE, sans option de série";
//$tab_type_brevet[5]="TECHNOLOGIQUE, option de série AGRICOLE";
$tab_type_brevet[5]="TECHNOLOGIQUE, sans option de série";
$tab_type_brevet[6]="TECHNOLOGIQUE, option de série DP6";
$tab_type_brevet[7]="TECHNOLOGIQUE, option de série AGRICOLE";
*/

// Liste des indices:
$sql_indices_types_brevets=" type_brevet='0' OR type_brevet='2' OR type_brevet='4' ";
// Il faudra modifier cela pour récupérer de meilleure façon la liste des brevets pour lesquels des associations matière notanet/matière gepi ont été réalisées (en contrôlant que l'indice existe pour $tab_type_brevet)

// Indice max des matières
$indice_max_matieres=130;

$indice_premiere_matiere=5;

// 20130429
$indice_brevet_pro_lv=103;

// *****************
// A FAIRE:
// Ajouter un traitement/test pour permettre l'affichage de la ligne Option DP6 sur la fiche brevet sans exiger de saisie en 'non dispensée dans l'établissement' au niveau de notanet
// *****************


function get_classe_from_id($id){
	//$sql="SELECT * FROM classes WHERE id='$id_classe[0]'";
	$sql="SELECT * FROM classes WHERE id='$id'";
	$resultat_classe=mysql_query($sql);
	if(mysql_num_rows($resultat_classe)!=1){
		//echo "<p>ERREUR! La classe d'identifiant '$id_classe[0]' n'a pas pu être identifiée.</p>";
		echo "<p>ERREUR! La classe d'identifiant '$id' n'a pas pu être identifiée.</p>";
	}
	else{
		$ligne_classe=mysql_fetch_object($resultat_classe);
		$classe=$ligne_classe->classe;
		return $classe;
	}
}


function accent_min($texte){
	return strtr($texte,"ÂÄÀÁÃÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕÛÜÙÚÝ¾","âäàáãåçêëèéîïìíñôöòóõûüùúýÿ");
}

function accent_maj($texte){
	return strtr($texte,"âäàáãåçêëèéîïìíñôöòóõûüùúýÿ","ÂÄÀÁÃÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕÛÜÙÚÝ¾");
}
/*
function get_commune($code_commune_insee,$mode){
	$retour="";

	$sql="SELECT * FROM communes WHERE code_commune_insee='$code_commune_insee';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);
		if($mode==0) {
			$retour=$lig->commune;
		}
		elseif($mode==1) {
			$retour=$lig->commune." (<i>".$lig->departement."</i>)";
		}
		elseif($mode==2) {
			$retour=$lig->commune." (".$lig->departement.")";
		}
	}
	return $retour;
}
*/
function tabmatieres($type_brevet){
	//====================
	// AJOUT: boireaus 20080329
	global $tabmatieres;
	unset($tabmatieres);
	//====================
	global $indice_max_matieres;
	global $indice_premiere_matiere;

	switch($type_brevet){
		case 0:
			// COLLEGE, option de série LV2

			// Initialisation
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][0]='';
			}

			//$tabmatieres[5][0]='HISTOIRE DES ARTS';

			//$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='SCIENCES DE LA VIE ET DE LA TERRE';
			$tabmatieres[105][0]='PHYSIQUE-CHIMIE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='ARTS PLASTIQUES';
			$tabmatieres[108][0]='EDUCATION MUSICALE';
			$tabmatieres[109][0]='TECHNOLOGIE';
			//$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE';
			$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE';
			//$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE OU DECOUVERTE PROFESSIONNELLE (module de 6 heures)';
			//$tabmatieres[112][0]='VIE SCOLAIRE';
			//$tabmatieres[113][0]='OPTION FACULTATIVE (1)';
			$tabmatieres[113][0]='OPTION FACULTATIVE';

			//$tabmatieres[114][0]='SOCLE B2I';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE ETRANGERE'; // 20100425

			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE';
			$tabmatieres[122][0]='EDUCATION CIVIQUE';

			$tabmatieres[130][0]='NIVEAU A2 DE LANGUE REGIONALE'; // 20100425

			// Mode de calcul:
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-1]='POINTS';
			}

			//$tabmatieres[5][-1]='POINTS';

			// Optionnelle
			$tabmatieres[113][-1]='PTSUP';
			// A titre indicatif
			$tabmatieres[121][-1]='NOTNONCA';
			$tabmatieres[122][-1]='NOTNONCA';

			// Coefficients:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-2]=1;
			}

			//$tabmatieres[5][-2]=2;

			$tabmatieres[121][-2]=0;
			$tabmatieres[122][-2]=0;

			// Notes spéciales autorisées:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-3]='AB';
			}

			//$tabmatieres[5][-3]='AB';

			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI';
			$tabmatieres[107][-3]='AB DI';
			$tabmatieres[108][-3]='AB DI';
			$tabmatieres[109][-3]='AB DI';
			$tabmatieres[110][-3]='AB DI';
			$tabmatieres[113][-3]='AB DI';

			//$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			//$tabmatieres[115][-3]='MS ME MN AB';
			$tabmatieres[130][-3]='AB VA NV'; // 20140317


			// Colonnes pour les fiches brevet:
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
				$tabmatieres[$j]['fb_col'][1]=20;
				$tabmatieres[$j]['fb_col'][2]=20;
			}
			// LV2 ou DP6
			$tabmatieres[110]['fb_col'][1]=20;
			$tabmatieres[110]['fb_col'][2]=40;
			// L'option facultative en PTSUP est traitée autrement...

			/*
			$num_fb_col=1;
			$fb_intitule_col[1]="LV2";
			$fb_intitule_col[2]="A module<br />découverte<br />professionnelle<br />6 heures";
			*/

			$tabmatieres["num_fb_col"]=1;
			$tabmatieres["fb_intitule_col"][1]="LV2";
			//$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";
			$tabmatieres["fb_intitule_col"][2]="DP6h";

			//$tabmatieres[110]["lig_speciale"]="DEUXIEME LANGUE VIVANTE OU DECOUVERTE PROFESSIONNELLE (module de 6 heures)";


			// POUR NE PAS FAIRE D'ASSOCIATION AVEC DES MATIERES
			// ET NE PAS FAIRE L'EXTRATION DES MOYENNES DANS LES MEMES TABLES
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['socle']='n';
			}
			//$tabmatieres[114]['socle']='y';
			//$tabmatieres[115]['socle']='y';
			$tabmatieres[130]['socle']='y'; // 20100425


			/*
			$tabmatieres['liste_mat_fb']=array();
			$tabmatieres['liste_mat_fb'][]='FRANÇAIS';
			$tabmatieres['liste_mat_fb'][]='MATHEMATIQUES';
			$tabmatieres['liste_mat_fb'][]='LANGUE VIVANTE 1';
			$tabmatieres['liste_mat_fb'][]='SCIENCES DE LA VIE ET DE LA TERRE';
			$tabmatieres['liste_mat_fb'][]='PHYSIQUE-CHIMIE';
			$tabmatieres['liste_mat_fb'][]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres['liste_mat_fb'][]='ARTS PLASTIQUES';
			$tabmatieres['liste_mat_fb'][]='EDUCATION MUSICALE';
			$tabmatieres['liste_mat_fb'][]='TECHNOLOGIE';
			$tabmatieres['liste_mat_fb'][]='LANGUE VIVANTE 2';
			$tabmatieres['liste_mat_fb'][]='VIE SCOLAIRE';
			$tabmatieres['liste_mat_fb'][]='Découverte professionnelle 6 heures';
			//$tabmatieres['liste_mat_fb'][]='OPTION FACULTATIVE';
			$tabmatieres['liste_mat_fb'][]='Latin ou grec ou découverte professionnelle 3h';
			$tabmatieres['liste_mat_fb'][]='Latin ou grec ou langue vivante 2';
			$tabmatieres['liste_mat_fb'][]='HISTOIRE-GEOGRAPHIE';
			$tabmatieres['liste_mat_fb'][]='EDUCATION CIVIQUE';
			*/

			break;
		case 1:
			// COLLEGE, option de série DP6

			// Initialisation
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][0]='';
			}

			//$tabmatieres[5][0]='HISTOIRE DES ARTS';

			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='SCIENCES DE LA VIE ET DE LA TERRE';
			$tabmatieres[105][0]='PHYSIQUE-CHIMIE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='ARTS PLASTIQUES';
			$tabmatieres[108][0]='EDUCATION MUSICALE';
			$tabmatieres[109][0]='TECHNOLOGIE';
			//$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE';
			//$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE OU DECOUVERTE PROFESSIONNELLE (module de 6 heures)';
			//$tabmatieres[110][0]='DECOUVERTE PROFESSIONNELLE (module de 6 heures)';
			$tabmatieres[110][0]='DÉCOUVERTE PROFESSIONNELLE 6 heures';
			$tabmatieres[111][0]='';
			//$tabmatieres[112][0]='VIE SCOLAIRE';
			//$tabmatieres[113][0]='OPTION FACULTATIVE (1)';
			$tabmatieres[113][0]='OPTION FACULTATIVE';

			//$tabmatieres[114][0]='SOCLE B2I';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';

			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE';
			$tabmatieres[122][0]='EDUCATION CIVIQUE';

			$tabmatieres[130][0]='NIVEAU A2 DE LANGUE REGIONALE'; // 20100425

			// Mode de calcul:
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}

			//$tabmatieres[5][-1]='POINTS';

			$tabmatieres[113][-1]='PTSUP';
			$tabmatieres[121][-1]='NOTNONCA';
			$tabmatieres[122][-1]='NOTNONCA';

			// Coefficients:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-2]=1;
			}

			//$tabmatieres[5][-2]=2;

			// DP6
			$tabmatieres[110][-2]=2;
			// A titre indicatif
			$tabmatieres[121][-2]=0;
			$tabmatieres[122][-2]=0;

			// Notes spéciales autorisées:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-3]='AB';
			}

			//$tabmatieres[5][-3]='AB';

			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI';
			$tabmatieres[107][-3]='AB DI';
			$tabmatieres[108][-3]='AB DI';
			$tabmatieres[109][-3]='AB DI';
			$tabmatieres[110][-3]='AB DI';
			$tabmatieres[113][-3]='AB DI';

			//$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			//$tabmatieres[115][-3]='MS ME MN AB';

			$tabmatieres[130][-3]='AB VA NV'; // 20140317

			// Colonnes pour les fiches brevet:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['fb_col'][1]=20;
				$tabmatieres[$j]['fb_col'][2]=20;
			}
			// LV2 ou DP6
			$tabmatieres[110]['fb_col'][1]=20;
			$tabmatieres[110]['fb_col'][2]=40;
			// L'option facultative en PTSUP est traitée autrement...

			/*
			$num_fb_col=2;
			$fb_intitule_col[1]="LV2";
			$fb_intitule_col[2]="A module<br />découverte<br />professionnelle<br />6 heures";
			*/

			$tabmatieres["num_fb_col"]=2;
			$tabmatieres["fb_intitule_col"][1]="LV2";
			//$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";
			$tabmatieres["fb_intitule_col"][2]="DP6h";

			// POUR NE PAS FAIRE D'ASSOCIATION AVEC DES MATIERES
			// ET NE PAS FAIRE L'EXTRATION DES MOYENNES DANS LES MEMES TABLES
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['socle']='n';
			}
			//$tabmatieres[114]['socle']='y';
			//$tabmatieres[115]['socle']='y';

			$tabmatieres[130]['socle']='y'; // 20100425

			//$tabmatieres[110]["lig_speciale"]="DEUXIEME LANGUE VIVANTE OU DECOUVERTE PROFESSIONNELLE (module de 6 heures)";
			break;
/*
		case 2:
			// COLLEGE, option de série TECHNOLOGIQUE
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='SCIENCES DE LA VIE ET DE LA TERRE';
			$tabmatieres[105][0]='PHYSIQUE-CHIMIE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='ARTS PLASTIQUES';
			$tabmatieres[108][0]='EDUCATION MUSICALE';
			$tabmatieres[109][0]='TECHNOLOGIE';
			$tabmatieres[110][0]='';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='';
			$tabmatieres[113][0]='OPTION FACULTATIVE (1)';
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			$tabmatieres[122][0]='EDUCATION CIVIQUE';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[113][-1]='PTSUP';
			$tabmatieres[121][-1]='NOTNONCA';
			$tabmatieres[122][-1]='NOTNONCA';

			// PROBLEME: TECHNOLOGIE POINTS /40
			//           GEPI ne doit donner que des notes sur 20.
			//           Il faudrait donc multiplier par deux...

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			$tabmatieres[109][-2]=2;
			$tabmatieres[121][-2]=0;
			$tabmatieres[122][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI NN';
			$tabmatieres[108][-3]='AB DI NN';
			$tabmatieres[109][-3]='AB DI';
			$tabmatieres[113][-3]='AB DI';


			// Colonnes pour les fiches brevet:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j]['fb_col'][1]=20;
				$tabmatieres[$j]['fb_col'][2]=20;
			}
			// Technologie
			$tabmatieres[109]['fb_col'][1]=40;
			$tabmatieres[109]['fb_col'][2]=20;
			// DP6: Je n'ai pas le numéro pour la DP6... est-ce bien le 110
			$tabmatieres[110]['fb_col'][1]="X";
			$tabmatieres[110]['fb_col'][2]=40;
			// Pas d'option facultative

			$num_fb_col=1;
			$fb_intitule_col[1]="Traditionnelle";
			$fb_intitule_col[2]="A module<br />découverte<br />professionnelle<br />6 heures";
			break;
		case 2:
			// COLLEGE, option de série TECHNOLOGIQUE AGRICOLE
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='SCIENCES DE LA VIE ET DE LA TERRE';
			$tabmatieres[105][0]='PHYSIQUE-CHIMIE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='ARTS PLASTIQUES';
			$tabmatieres[108][0]='EDUCATION MUSICALE';
			$tabmatieres[109][0]='TECHNOLOGIE';
			$tabmatieres[110][0]='';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='';
			$tabmatieres[113][0]='OPTION FACULTATIVE (1)';
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			$tabmatieres[122][0]='EDUCATION CIVIQUE';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[113][-1]='PTSUP';
			$tabmatieres[121][-1]='NOTNONCA';
			$tabmatieres[122][-1]='NOTNONCA';

			// PROBLEME: TECHNOLOGIE POINTS /40
			//           GEPI ne doit donner que des notes sur 20.
			//           Il faudrait donc multiplier par deux...

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			$tabmatieres[109][-2]=2;
			$tabmatieres[121][-2]=0;
			$tabmatieres[122][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI NN';
			$tabmatieres[108][-3]='AB DI NN';
			$tabmatieres[109][-3]='AB DI';
			$tabmatieres[113][-3]='AB DI';


			// Colonnes pour les fiches brevet:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j]['fb_col'][1]=20;
				$tabmatieres[$j]['fb_col'][2]=20;
			}
			// Technologie
			$tabmatieres[109]['fb_col'][1]=40;
			$tabmatieres[109]['fb_col'][2]=20;
			// DP6: Je n'ai pas le numéro pour la DP6... est-ce bien le 110
			$tabmatieres[110]['fb_col'][1]="X";
			$tabmatieres[110]['fb_col'][2]=40;
			// Pas d'option facultative

			$num_fb_col=1;
			$fb_intitule_col[1]="Traditionnelle";
			$fb_intitule_col[2]="A module<br />découverte<br />professionnelle<br />6 heures";
			break;
*/
		case 2:
			// PROFESSIONNELLE, sans option de série

			// Initialisation
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][0]='';
			}

			//$tabmatieres[5][0]='HISTOIRE DES ARTS';

			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';

			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE OU ';
			$tabmatieres[103][0]='LANGUES VIVANTES';
			//$tabmatieres[103][0]='LANGUE VIVANTE 1';
			//$tabmatieres[103][0]='LANGUE VIVANTE';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE OU SCIENCES PHYSIQUES';

			//$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			//$tabmatieres[104][0]='';
			//$tabmatieres[105][0]='VIE SOCIALE ET PROFESSIONNELLE';
			$tabmatieres[105][0]='PREVENTION SANTE ENVIRONNEMENT'; //20100425
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION ARTISTIQUE';
			$tabmatieres[108][0]='SCIENCES ET TECHNOLOGIE';
			$tabmatieres[110][0]='DECOUVERTE PROFESSIONNELLE';
			//$tabmatieres[112][0]='VIE SCOLAIRE';

			//$tabmatieres[114][0]='SOCLE B2I';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE ETRANGERE'; // 20100425

			//$tabmatieres[118][0]='HISTOIRE DES ARTS'; // 20100425
			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE EDUCATION CIVIQUE';
			//$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			//$tabmatieres[122][0]='EDUCATION CIVIQUE';

			$tabmatieres[130][0]='NIVEAU A2 DE LANGUE REGIONALE'; // 20100425

			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-1]='POINTS';
			}

			//$tabmatieres[5][-1]='POINTS';

			$tabmatieres[121][-1]='NOTNONCA';
			//$tabmatieres[122][-1]='NOTNONCA';

			//$tabmatieres[118][-1]='PTSUP'; // 20100425

			// PROBLEME: TECHNOLOGIE POINTS /60
			//           GEPI ne doit donner que des notes sur 20.
			//           Il faudrait donc multiplier par trois...

			// Par ailleurs, les candidats sont inscrits soit en LV1 soit en Sciences-physiques
			// Il faudrait donc considérer les deux matières commme optionnelles et on a alors un problème pour relever une note manquante...

			// Coefficients:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-2]=1;
			}

			//$tabmatieres[5][-2]=2;

			$tabmatieres[108][-2]=2;
			$tabmatieres[110][-2]=3;
			$tabmatieres[121][-2]=0;

			// Notes spéciales autorisées:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-3]='AB';
			}

			//$tabmatieres[5][-3]='AB';

			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI';
			$tabmatieres[107][-3]='AB DI';
			$tabmatieres[108][-3]='AB DI';
			$tabmatieres[110][-3]='AB DI';

			//$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			//$tabmatieres[115][-3]='MS ME MN AB';
			$tabmatieres[130][-3]='AB VA NV'; // 20140317


			// Colonnes pour les fiches brevet:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['fb_col'][1]=20;
				$tabmatieres[$j]['fb_col'][2]=20;
			}
			// Technologie
			$tabmatieres[108]['fb_col'][1]=60;
			$tabmatieres[108]['fb_col'][2]=40;
			// DP6: Je n'ai pas le numéro pour la DP6... est-ce bien le 110
			//$tabmatieres[111]['fb_col'][1]="X";
			//$tabmatieres[111]['fb_col'][2]=60;
			// Pas d'option facultative

			/*
			$num_fb_col=1;
			$fb_intitule_col[1]="Traditionnelle";
			$fb_intitule_col[2]="A module<br />découverte<br />professionnelle<br />6 heures";
			*/

			$tabmatieres["num_fb_col"]=1;
			//$tabmatieres["fb_intitule_col"][1]="Traditionnelle";
			//$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";
			$tabmatieres["fb_intitule_col"][1]="sans option";
			$tabmatieres["fb_intitule_col"][2]="DP6h";


			// Les deux matières en une seule ligne
			// Je n'utilise finalement pas le texte correspondant... parce qu'il faut préciser la LV sous la forme:
			//       Langue vivante: Anglais
			//       ou sciences physiques
			$tabmatieres[103]["lig_speciale"]='PREMIERE LANGUE VIVANTE OU SCIENCES PHYSIQUES';
			$tabmatieres[104]["lig_speciale"]='PREMIERE LANGUE VIVANTE OU SCIENCES PHYSIQUES';

			// Il faudrait ajouter une ligne spéciale pour la DP6 alors que ce n'est pas compté dans cette série
			//$tabmatieres[111]["lig_speciale"]="DÉCOUVERTE PROFESSIONNELLE<br />(module de 6 heures)";
			//$tabmatieres[111]["lig_speciale"]="DÉCOUVERTE PROFESSIONNELLE 6 heures";


			// Intitulé de la ligne pour la fiche brevet option DP6h
			//$tabmatieres[111]['fb_lig_alt']="Découverte professionnelle 6 heures";
			// L'indice 111 n'est pas présent sinon en PROFESSIONNELLE sans option de série


			// POUR NE PAS FAIRE D'ASSOCIATION AVEC DES MATIERES
			// ET NE PAS FAIRE L'EXTRATION DES MOYENNES DANS LES MEMES TABLES
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['socle']='n';
			}
			//$tabmatieres[114]['socle']='y';
			//$tabmatieres[115]['socle']='y';

			$tabmatieres[130]['socle']='y'; //20100425

			break;
		case 3:
			// PROFESSIONNELLE, option de série DP6

			// Initialisation
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][0]='';
			}

			//$tabmatieres[5][0]='HISTOIRE DES ARTS';

			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			//$tabmatieres[103][0]='LANGUE VIVANTE 1';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE OU ';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE OU SCIENCES PHYSIQUES';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			//$tabmatieres[104][0]='';
			//$tabmatieres[105][0]='VIE SOCIALE ET PROFESSIONNELLE';
			$tabmatieres[105][0]='PREVENTION SANTE ENVIRONNEMENT'; //20100425
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION ARTISTIQUE';
			$tabmatieres[108][0]='TECHNOLOGIE';
			// DP6 A PLACER....
			$tabmatieres[111][0]='DÉCOUVERTE PROFESSIONNELLE (module 6 heures)';
			//$tabmatieres[112][0]='VIE SCOLAIRE';

			//$tabmatieres[114][0]='SOCLE B2I';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE ETRANGERE'; // 20100425

			//$tabmatieres[118][0]='HISTOIRE DES ARTS'; // 20100425

			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE EDUCATION CIVIQUE';
			//$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			//$tabmatieres[122][0]='EDUCATION CIVIQUE';

			$tabmatieres[130][0]='NIVEAU A2 DE LANGUE REGIONALE'; // 20100425

			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-1]='POINTS';
			}

			//$tabmatieres[5][-1]='POINTS';

			$tabmatieres[121][-1]='NOTNONCA';
			//$tabmatieres[122][-1]='NOTNONCA';

			//$tabmatieres[118][-1]='PTSUP'; //20100425

			// PROBLEME: TECHNOLOGIE POINTS /60
			//           GEPI ne doit donner que des notes sur 20.
			//           Il faudrait donc multiplier par trois...

			// Par ailleurs, les candidats sont inscrits soit en LV1 soit en Sciences-physiques
			// Il faudrait donc considérer les deux matières commme optionnelles et on a alors un problème pour relever une note manquante...

			// Coefficients:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-2]=1;
			}

			//$tabmatieres[5][-2]=2;

			$tabmatieres[108][-2]=2;
			$tabmatieres[111][-2]=3;

			$tabmatieres[121][-2]=0;

			// Notes spéciales autorisées:
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-3]='AB';
			}

			//$tabmatieres[5][-3]='AB';

			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI';
			$tabmatieres[107][-3]='AB DI';
			$tabmatieres[108][-3]='AB DI';
			$tabmatieres[111][-3]='AB DI';

			//$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			//$tabmatieres[115][-3]='MS ME MN AB';

			$tabmatieres[130][-3]='AB VA NV'; // 20140317

			// Colonnes pour les fiches brevet:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['fb_col'][1]=20;
				$tabmatieres[$j]['fb_col'][2]=20;
			}
			// Technologie
			$tabmatieres[108]['fb_col'][1]=60;
			$tabmatieres[108]['fb_col'][2]=40;
			// DP6:
			$tabmatieres[111]['fb_col'][1]="X";
			$tabmatieres[111]['fb_col'][2]=60;
			// Pas d'option facultative

			/*
			$num_fb_col=2;
			$fb_intitule_col[1]="Traditionnelle";
			$fb_intitule_col[2]="A module<br />découverte<br />professionnelle<br />6 heures";
			*/

			$tabmatieres["num_fb_col"]=2;
			//$tabmatieres["fb_intitule_col"][1]="Traditionnelle";
			//$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";
			$tabmatieres["fb_intitule_col"][1]="sans option";
			$tabmatieres["fb_intitule_col"][2]="DP6h";

			// Les deux matières en une seule ligne
			$tabmatieres[103]["lig_speciale"]='PREMIERE LANGUE VIVANTE OU SCIENCES PHYSIQUES';
			$tabmatieres[104]["lig_speciale"]='PREMIERE LANGUE VIVANTE OU SCIENCES PHYSIQUES';

			// Pour mettre le saut de ligne au bon niveau:
			//$tabmatieres[111]["lig_speciale"]="DÉCOUVERTE PROFESSIONNELLE<br />(module de 6 heures)";
			$tabmatieres[111]["lig_speciale"]="DÉCOUVERTE PROFESSIONNELLE 6 heures";


			// POUR NE PAS FAIRE D'ASSOCIATION AVEC DES MATIERES
			// ET NE PAS FAIRE L'EXTRATION DES MOYENNES DANS LES MEMES TABLES
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['socle']='n';
			}
			//$tabmatieres[114]['socle']='y';
			//$tabmatieres[115]['socle']='y';

			$tabmatieres[130]['socle']='y';

			break;
		case 4:
			// PROFESSIONNELLE, option de série AGRICOLE

			// Initialisation
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][0]='';
			}

			//$tabmatieres[5][0]='HISTOIRE DES ARTS';

			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			//$tabmatieres[103][0]='LANGUE VIVANTE 1';
			$tabmatieres[104][0]='';
			$tabmatieres[105][0]='PREVENTION SANTE ENVIRONNEMENT';
			//$tabmatieres[105][0]='ECONOMIE FAMILIALE ET SOCIALE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION SOCIOCULTURELLE';
			// CES TROIS Là DEVRAIENT ETRE SUR UNE MEME LIGNE POUR LES FICHES BREVET
			//$tabmatieres[108][0]='TECHNOLOGIE';
			//$tabmatieres[109][0]='SCIENCES BIOLOGIQUES';
			//$tabmatieres[110][0]='SCIENCES PHYSIQUES';
			//$tabmatieres[109][0]='TECHNOLOGIE: SCIENCES BILOGIQUES ET SCIENCES PHYSIQUES';
			$tabmatieres[109][0]='TECHNOLOGIE, SCIENCES ET DECOUVERTE DE LA VIE PROFESSIONNELLE ET DES METIERS';

			//$tabmatieres[112][0]='VIE SCOLAIRE';
			$tabmatieres[113][0]='';

			//$tabmatieres[114][0]='SOCLE B2I';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE ETRANGERE'; //20100425

			//$tabmatieres[118][0]='HISTOIRE DES ARTS'; // 20100425

			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE EDUCATION CIVIQUE';
			//$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';

			//$tabmatieres[122][0]='EDUCATION CIVIQUE';

			$tabmatieres[130][0]='NIVEAU A2 DE LANGUE REGIONALE'; // 20100425


			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-1]='POINTS';
			}

			//$tabmatieres[5][-1]='POINTS';

			$tabmatieres[121][-1]='NOTNONCA';
			//$tabmatieres[122][-1]='NOTNONCA';

			//$tabmatieres[118][-1]='PTSUP';

			// Coefficients:
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-2]=1;
			}

			//$tabmatieres[5][-2]=2;

			//$tabmatieres[108][-2]=3;
			$tabmatieres[109][-2]=3;
			//$tabmatieres[110][-2]=3;
			$tabmatieres[121][-2]=0;
			//$tabmatieres[122][-2]=0;

			// Notes spéciales autorisées:
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-3]='AB';
			}

			$tabmatieres[5][-3]='AB';

			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI';
			$tabmatieres[107][-3]='AB DI';
			//$tabmatieres[108][-3]='AB DI';
			$tabmatieres[109][-3]='AB DI';
			//$tabmatieres[110][-3]='AB DI';

			//$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			//$tabmatieres[115][-3]='MS ME MN AB';

			$tabmatieres[130][-3]='AB VA NV'; // 20140317


			$tabmatieres["num_fb_col"]=1;

			// Colonnes pour les fiches brevet:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['fb_col'][1]=20;
			}
			// Technologie
			//$tabmatieres[109]['fb_col'][1]=60;


			// Il n'y a qu'une seule colonne pour les fiches brevet en agricole
			/*
			// Colonnes pour les fiches brevet:
			for($j=$indice_premiere_matiere;$j<=122;$j++){
				$tabmatieres[$j]['fb_col'][1]=20;
				$tabmatieres[$j]['fb_col'][2]=20;
			}
			// Technologie
			$tabmatieres[109]['fb_col'][1]=40;
			$tabmatieres[109]['fb_col'][2]=20;
			// DP6: Je n'ai pas le numéro pour la DP6... est-ce bien le 110
			$tabmatieres[110]['fb_col'][1]="X";
			$tabmatieres[110]['fb_col'][2]=40;
			// Pas d'option facultative

			$num_fb_col=1;
			$fb_intitule_col[1]="Traditionnelle";
			$fb_intitule_col[2]="A module<br />découverte<br />professionnelle<br />6 heures";



			// Colonnes pour les fiches brevet:
			for($j=$indice_premiere_matiere;$j<=122;$j++){
				$tabmatieres[$j]['fb_col'][1]=20;
				$tabmatieres[$j]['fb_col'][2]=20;
			}
			// LV2 ou DP6
			$tabmatieres[110]['fb_col'][1]=20;
			$tabmatieres[110]['fb_col'][2]=40;
			// L'option facultative en PTSUP est traitée autrement...

			$tabmatieres["num_fb_col"]=1;
			$tabmatieres["fb_intitule_col"][1]="LV2";
			$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";

			$tabmatieres[110]["lig_speciale"]="DEUXIEME LANGUE VIVANTE OU DECOUVERTE PROFESSIONNELLE (module de 6 heures)";

			*/

			// POUR NE PAS FAIRE D'ASSOCIATION AVEC DES MATIERES
			// ET NE PAS FAIRE L'EXTRATION DES MOYENNES DANS LES MEMES TABLES

			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['socle']='n';
			}
			//$tabmatieres[114]['socle']='y';
			//$tabmatieres[115]['socle']='y';

			$tabmatieres[130]['socle']='y'; // 20100425

			break;
		case 5:
			// TECHNOLOGIQUE, sans option de série

			// Initialisation
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][0]='';
			}

			//$tabmatieres[5][0]='HISTOIRE DES ARTS';

			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			//$tabmatieres[103][0]='LANGUE VIVANTE 1';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			$tabmatieres[105][0]='PREVENTION SANTE ENVIRONNEMENT';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION ARTISTIQUE';
			$tabmatieres[108][0]='TECHNOLOGIE';
			//$tabmatieres[112][0]='VIE SCOLAIRE';

			//$tabmatieres[114][0]='SOCLE B2I';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE ETRANGERE'; //20100425

			//$tabmatieres[118][0]='HISTOIRE DES ARTS'; // 20100425

			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE EDUCATION CIVIQUE';
			//$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			//$tabmatieres[122][0]='EDUCATION CIVIQUE';

			$tabmatieres[130][0]='NIVEAU A2 DE LANGUE REGIONALE'; // 20100425

			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-1]='POINTS';
			}

			//$tabmatieres[5][-1]='POINTS';

			$tabmatieres[121][-1]='NOTNONCA';

			//$tabmatieres[118][-1]='PTSUP'; //20100425

			// PROBLEME: TECHNOLOGIE POINTS /40
			//           GEPI ne doit donner que des notes sur 20.
			//           Il faudrait donc multiplier par deux...

			// Coefficients:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-2]=1;
			}

			//$tabmatieres[5][-2]=2;

			$tabmatieres[108][-2]=2;
			$tabmatieres[121][-2]=0;
			//$tabmatieres[122][-2]=0;

			// Notes spéciales autorisées:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-3]='AB';
			}

			//$tabmatieres[5][-3]='AB';

			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI';
			$tabmatieres[107][-3]='AB DI';
			$tabmatieres[108][-3]='AB DI';

			//$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			//$tabmatieres[115][-3]='MS ME MN AB';

			$tabmatieres[130][-3]='AB VA NV'; // 20140317

			// Colonnes pour les fiches brevet:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['fb_col'][1]=20;
				$tabmatieres[$j]['fb_col'][2]=20;
			}
			// Technologie
			$tabmatieres[108]['fb_col'][1]=40;
			$tabmatieres[108]['fb_col'][2]=20;
			// DP6: Je n'ai pas le numéro pour la DP6... est-ce bien le 110
			$tabmatieres[110]['fb_col'][1]="X";
			$tabmatieres[110]['fb_col'][2]=40;
			// Pas d'option facultative

			/*
			$num_fb_col=1;
			$fb_intitule_col[1]="Traditionnelle";
			$fb_intitule_col[2]="A module<br />découverte<br />professionnelle<br />6 heures";
			*/

			$tabmatieres["num_fb_col"]=1;
			//$tabmatieres["fb_intitule_col"][1]="Traditionnelle";
			//$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";
			$tabmatieres["fb_intitule_col"][1]="sans option";
			$tabmatieres["fb_intitule_col"][2]="option DP6h";

			// Il faudrait ajouter une ligne spéciale pour la DP6 alors que ce n'est pas compté dans cette série
			//$tabmatieres[110]["lig_speciale"]="DÉCOUVERTE PROFESSIONNELLE<br />(module de 6 heures)";
			$tabmatieres[110]["lig_speciale"]="Découverte professionnelle 6 heures";
			$tabmatieres[110]['fb_lig_alt']="Découverte professionnelle 6 heures";

			// POUR NE PAS FAIRE D'ASSOCIATION AVEC DES MATIERES
			// ET NE PAS FAIRE L'EXTRATION DES MOYENNES DANS LES MEMES TABLES
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['socle']='n';
			}
			//$tabmatieres[114]['socle']='y';
			//$tabmatieres[115]['socle']='y';

			$tabmatieres[130]['socle']='y'; // 20100425

			break;
		case 6:
			// TECHNOLOGIQUE, option de série DP6

			// Initialisation
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][0]='';
			}

			$tabmatieres[5][0]='HISTOIRE DES ARTS';

			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			//$tabmatieres[103][0]='LANGUE VIVANTE 1';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			$tabmatieres[105][0]='PREVENTION SANTE ENVIRONNEMENT';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION ARTISTIQUE';
			$tabmatieres[108][0]='TECHNOLOGIE';
			$tabmatieres[109][0]='';
			$tabmatieres[110][0]='DÉCOUVERTE PROFESSIONNELLE (module 6 heures)';
			//$tabmatieres[112][0]='VIE SCOLAIRE';

			//$tabmatieres[114][0]='SOCLE B2I';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE ETRANGERE'; // 20100425

			//$tabmatieres[118][0]='HISTOIRE DES ARTS'; // 20100512
			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE EDUCATION CIVIQUE';
			//$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			//$tabmatieres[122][0]='EDUCATION CIVIQUE';

			$tabmatieres[130][0]='NIVEAU A2 DE LANGUE REGIONALE'; // 20100425

			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-1]='POINTS';
			}

			//$tabmatieres[5][-1]='POINTS';

			$tabmatieres[121][-1]='NOTNONCA';

			// PROBLEME: TECHNOLOGIE POINTS /40
			//           GEPI ne doit donner que des notes sur 20.
			//           Il faudrait donc multiplier par deux...

			// Coefficients:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-2]=1;
			}

			//$tabmatieres[5][-2]=2;

			//$tabmatieres[108][-2]=2;
			$tabmatieres[110][-2]=2;
			$tabmatieres[121][-2]=0;
			//$tabmatieres[122][-2]=0;

			// Notes spéciales autorisées:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-3]='AB';
			}

			//$tabmatieres[5][-3]='AB';

			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI NN';
			$tabmatieres[108][-3]='AB DI';
			$tabmatieres[110][-3]='AB DI';

			//$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			//$tabmatieres[115][-3]='MS ME MN AB';

			$tabmatieres[130][-3]='AB VA NV'; // 20140317


			// Colonnes pour les fiches brevet:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['fb_col'][1]=20;
				$tabmatieres[$j]['fb_col'][2]=20;
			}
			// Technologie
			$tabmatieres[108]['fb_col'][1]=40;
			$tabmatieres[108]['fb_col'][2]=20;
			// DP6: Je n'ai pas le numéro pour la DP6... est-ce bien le 110
			$tabmatieres[110]['fb_col'][1]="X";
			$tabmatieres[110]['fb_col'][2]=40;
			// Pas d'option facultative


			$tabmatieres[110]["lig_speciale"]="Découverte professionnelle 6 heures";


			/*
			$num_fb_col=2;
			$fb_intitule_col[1]="Traditionnelle";
			$fb_intitule_col[2]="A module<br />découverte<br />professionnelle<br />6 heures";
			*/

			$tabmatieres["num_fb_col"]=2;
			//$tabmatieres["fb_intitule_col"][1]="Traditionnelle";
			//$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";
			$tabmatieres["fb_intitule_col"][1]="sans option";
			$tabmatieres["fb_intitule_col"][2]="option DP6h";

			// POUR NE PAS FAIRE D'ASSOCIATION AVEC DES MATIERES
			// ET NE PAS FAIRE L'EXTRATION DES MOYENNES DANS LES MEMES TABLES
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['socle']='n';
			}
			//$tabmatieres[114]['socle']='y';
			//$tabmatieres[115]['socle']='y';

			$tabmatieres[130]['socle']='y'; // 20100425

			break;
		case 7:
			// TECHNOLOGIQUE, option de série AGRICOLE

			// Initialisation
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][0]='';
			}

			//$tabmatieres[5][0]='HISTOIRE DES ARTS';

			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			//$tabmatieres[103][0]='LANGUE VIVANTE 1';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			//$tabmatieres[105][0]='PREVENTION SANTE ENVIRONNEMENT';
			$tabmatieres[105][0]='ECONOMIE FAMILIALE ET SOCIALE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION SOCIOCULTURELLE';
			//$tabmatieres[108][0]='SCIENCES BIOLOGIQUES';
			$tabmatieres[109][0]='TECHNOLOGIE: SECTEUR SCIENCES BIOLOGIQUES, TECHNIQUES AGRICOLES ET AGROALIMENTAIRES, ACTIVITES TERTIAIRES';

			//$tabmatieres[112][0]='VIE SCOLAIRE';

			//$tabmatieres[114][0]='SOCLE B2I';
			//$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE ETRANGERE'; // 20100425

			//$tabmatieres[118][0]='HISTOIRE DES ARTS'; // 20100425
			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE EDUCATION CIVIQUE';
			//$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			//$tabmatieres[122][0]='EDUCATION CIVIQUE';

			$tabmatieres[130][0]='NIVEAU A2 DE LANGUE REGIONALE'; // 20100425

			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-1]='POINTS';
			}

			//$tabmatieres[5][-1]='POINTS';

			$tabmatieres[121][-1]='NOTNONCA';

			//$tabmatieres[118][-1]='PTSUP';

			// Coefficients:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-2]=1;
			}

			//$tabmatieres[5][-2]=2;

			$tabmatieres[109][-2]=2;
			$tabmatieres[121][-2]=0;

			// Notes spéciales autorisées:
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j][-3]='AB';
			}

			//$tabmatieres[5][-3]='AB';

			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI';
			$tabmatieres[107][-3]='AB DI';
			$tabmatieres[108][-3]='AB DI';
			$tabmatieres[109][-3]='AB DI';

			//$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			//$tabmatieres[115][-3]='MS ME MN AB';

			$tabmatieres[130][-3]='AB VA NV'; // 20140317

			// Colonnes pour les fiches brevet:
			// Il n'y a qu'une seule colonne pour les fiches brevet en agricole
			/*
			for($j=$indice_premiere_matiere;$j<=122;$j++){
				$tabmatieres[$j]['fb_col'][1]=20;
				$tabmatieres[$j]['fb_col'][2]=20;
			}
			// Technologie
			$tabmatieres[109]['fb_col'][1]=40;
			$tabmatieres[109]['fb_col'][2]=20;
			// DP6: Je n'ai pas le numéro pour la DP6... est-ce bien le 110
			$tabmatieres[110]['fb_col'][1]="X";
			$tabmatieres[110]['fb_col'][2]=40;
			// Pas d'option facultative

			$num_fb_col=1;
			$fb_intitule_col[1]="Traditionnelle";
			$fb_intitule_col[2]="A module<br />découverte<br />professionnelle<br />6 heures";
			*/

			// POUR NE PAS FAIRE D'ASSOCIATION AVEC DES MATIERES
			// ET NE PAS FAIRE L'EXTRATION DES MOYENNES DANS LES MEMES TABLES
			//for($j=$indice_premiere_matiere;$j<=122;$j++){
			for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){ // 20100425
				$tabmatieres[$j]['socle']='n';
			}
			//$tabmatieres[114]['socle']='y';
			//$tabmatieres[115]['socle']='y';

			$tabmatieres[130]['socle']='y';

			break;
	}

	//===============================================
	// 20140326
	// Pour gérer la note d'EPS qui ne provient pas de la moyenne des 3 trimestres:
	for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
		$tabmatieres[$j]['extraction_moyenne']='y';
	}
	$sql="SELECT id_mat FROM notanet_corresp WHERE mode='saisie';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tabmatieres[$lig->id_mat]['extraction_moyenne']='n';
		}
	}
	//===============================================

	return $tabmatieres;
}







function colore_ligne_notanet($chaine){
	$tabchaine=explode("|",$chaine);
	/*
	echo "<!--\n$chaine\n";
	for($loop=0;$loop<count($tabchaine);$loop++){
		echo "\$tabchaine[$loop]=$tabchaine[$loop]\n";
	}
	echo "-->\n";
	*/
	//$color1="red";
	$color1="blue";
	$color2="green";
	$color3="blue";
	return "<span style='color: $color1;'>$tabchaine[0]</span>|<span style='color: $color2;'>$tabchaine[1]</span>|<span style='color: $color3;'>$tabchaine[2]</span>|";
}

function formate_note_notanet($chaine){
	// Arrondir au demi-point:
	$chaine_tmp=round($chaine*2)/2;
	// Formater en AA.BB:
	//return str_pad(sprintf("%02.2f",$chaine_tmp),5,"0",STR_PAD_LEFT);
	return sprintf("%05.2f",$chaine_tmp);
}


$tab_liste_notes_non_numeriques=array("MS","ME","MN","AB","DI","NN","VA","NV");


function tab_extract_moy($tab_ele,$id_clas) {
	global $num_eleve, $classe, $tab_mat;
	global $indice_max_matieres;
	global $indice_premiere_matiere;
	global $compteur_champs_notes;
	// 20130429
	global $indice_brevet_pro_lv;

	$affiche_enregistrements_precedents="y";
	//global $affiche_enregistrements_precedents;

	//echo "\$tab_ele['type_brevet']=".$tab_ele['type_brevet']."<br />";
	$tabmatieres=tabmatieres($tab_ele['type_brevet']);
	//echo "count(\$tabmatieres)=".count($tabmatieres)."<br />";

	$id_matiere=$tab_mat[$tab_ele['type_brevet']]['id_matiere'];
	$statut_matiere=$tab_mat[$tab_ele['type_brevet']]['statut_matiere'];

	/*
	// B2I ET A2:
	$statut_matiere[114]="imposee";
	$statut_matiere[115]="imposee";
	$statut_matiere[115]="optionnelle";
	*/

	//$sql="SELECT * FROM notanet_corresp WHERE type_brevet='".$tab_ele['type_brevet']."'";

	// Témoin destiné à signaler les élèves pour lesquels une erreur se produit.
	$temoin_notanet_eleve="";
	$info_erreur="";

	echo "<p>\n";
	echo "<a name='tableau_des_moyennes_eleve_$num_eleve'></a>";
	if($tab_ele['no_gep']==""){
		echo "<b style='color:red;'>ERREUR:</b> Numéro INE non attribué: ".$tab_ele['nom']." ".$tab_ele['prenom']."<br />";
		$temoin_notanet_eleve="ERREUR";
		$info_erreur="Pas de numéro INE";
		echo "INE: <input type='text' name='INE[$num_eleve]' value='' onchange='changement()' />\n";
	}
	else{
		echo "<b>".$tab_ele['nom']." ".$tab_ele['prenom']."</b> ".$tab_ele['no_gep']."<br />\n";
		$INE=$tab_ele['no_gep'];
		echo "INE: <input type='text' name='INE[$num_eleve]' value='$INE' onchange='changement()' />\n";
	}
	// Guillemets sur la valeur à cause des apostrophes dans des noms...
	echo "<input type='hidden' name='nom_eleve[$num_eleve]' value=\"".$tab_ele['nom']." ".$tab_ele['prenom']." ($classe)\" />\n";
	echo "</p>\n";


	// Tableau destiné à présenter à gauche, le tableau des notes, moyennes,... et à droite les commentaires/erreurs et éventuellement les lignes du fichier d'export.
	//echo "<table border='1'>\n";
	echo "<table class='boireaus'>\n";
	echo "<tr>\n";
	echo "<td valign='top' style='padding-top: 2px; padding-left:2px; vertical-align:top;'>\n";

	//$TOT=0;
	//echo "<table border='1'>\n";
	$sql="SELECT DISTINCT num_periode FROM periodes WHERE id_classe='$id_clas' ORDER BY num_periode";
	//$sql="SELECT DISTINCT num_periode, verouiller FROM periodes WHERE id_classe='$id_clas' ORDER BY num_periode";
	//echo "$sql<br />";
	$resultat_periodes=mysql_query($sql);

	//$tab_ver_per=array();
	echo "<table class='boireaus'>\n";
	echo "<tr style='font-weight: bold; text-align:center;'>\n";
	echo "<th>Id</th>\n";
	echo "<th>Matière</th>\n";
	echo "<th>Moyenne</th>\n";
	while($ligne_periodes=mysql_fetch_object($resultat_periodes)){
		echo "<th>T $ligne_periodes->num_periode</th>\n";
		//$tab_ver_per[$ligne_periodes->num_periode]=$ligne_periodes->verouiller;
	}
	echo "<th>Moyenne</th>\n";
	echo "<th>Correction</th>\n";
	if($affiche_enregistrements_precedents=="y") {
		echo "<th>\n";
		echo "Enregistré<br />auparavant";
		echo "</th>\n";
	}
	echo "</tr>\n";

	// 20130429
	$temoin_brevet_pro_lv=0;
	$total_brevet_pro_lv=0;
	$liste_matiere_brevet_pro_lv="";
	$chaine_total_brevet_pro_lv="";

	$type_brevet_ele="";
	$sql="SELECT type_brevet FROM notanet_ele_type WHERE login='".$tab_ele['login']."';";
	//echo "$sql<br />";
	$res_ele_type=mysql_query($sql);
	if(mysql_num_rows($res_ele_type)>0) {
		$type_brevet_ele=mysql_result($res_ele_type, 0, "type_brevet");
	}
	//echo "<tr></td>\$type_brevet_ele=$type_brevet_ele</td></tr>";

	$alt=1;
	for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){

		//=================================================================
		// 20130429
		if(($type_brevet_ele==2)&&($j>$indice_brevet_pro_lv)&&($temoin_brevet_pro_lv>0)&&(!isset($ligne_lv_affichee))) {
			echo "<tr style='background-color:lightpink;' title=\"Dans le cas du brevet série professionnelle, c'est la moyenne des notes de langue vivante, qu'il faut prendre en compte.\">
				<td>$indice_brevet_pro_lv</td>
				<td>LV</td>
				<td><input type='text' name='liste_matiere_brevet_pro_lv[$num_eleve]' size='7' value='$liste_matiere_brevet_pro_lv' /></td>";
			$sql="SELECT DISTINCT num_periode, verouiller FROM periodes WHERE id_classe='$id_clas' ORDER BY num_periode";
			//echo "<td>$sql</td>";
			$resultat_periodes=mysql_query($sql);
			echo "
				<td colspan='".mysql_num_rows($resultat_periodes)."'>($chaine_total_brevet_pro_lv)/$temoin_brevet_pro_lv</td>";

			$k="0";
			echo "
				<td>".($total_brevet_pro_lv/$temoin_brevet_pro_lv)."</td>
				<td><input type='text' name='moy_$indice_brevet_pro_lv"."_".$k."[$num_eleve]' id='n".$compteur_champs_notes."' value='".$moy_NOTANET[$indice_brevet_pro_lv]."' size='6' onKeyDown=\"clavier(this.id,event);\" onchange='changement()' autocomplete=\"off\" onfocus=\"javascript:this.select()\" /></td>";

			if($affiche_enregistrements_precedents=="y") {
				echo "<td>\n";
				$sql="SELECT note FROM notanet WHERE login='".$tab_ele['login']."' AND matiere='".$liste_matiere_brevet_pro_lv."' AND notanet_mat='".$tabmatieres[$j][0]."' AND id_mat='$indice_brevet_pro_lv';";
				$enr=mysql_query($sql);
				if(mysql_num_rows($enr)>0) {
					$lig_enr=mysql_fetch_object($enr);

					if($moy_NOTANET[$indice_brevet_pro_lv]!=$lig_enr->note) {
						echo "<a href=\"#tableau_des_moyennes_eleve_$num_eleve\" onclick=\"document.getElementById('n".$compteur_champs_notes."').value='$lig_enr->note'; return false;\"><img src='../images/icons/back.png' width='16' height='16' alt='Rétablir la valeur enregistrée' /></a>";
						echo "&nbsp;";
					}

					echo "<span id='note_precedemment_enregistree_$compteur_champs_notes'>".$lig_enr->note."</span>";
				}
				else {
					echo "&nbsp;";
				}
				echo "</td>\n";
			}


			echo "
			</tr>\n";
			$ligne_lv_affichee="y";
			$compteur_champs_notes++;
		}
		//=================================================================

		// Initialisation de la moyenne pour la matière NOTANET courante.
		$moy_NOTANET[$j]="";

//echo "<tr><td colspan='5'>\$tabmatieres[$j][0]=".$tabmatieres[$j][0]."</td></tr>";
//echo "<tr><td colspan='5'>\$statut_matiere[$j]=".$statut_matiere[$j]."</td></tr>";

		// Compteur destiné à repérer des matières pour lesquelles l'élève aurait des notes dans plus d'une option.
		// On ne sait alors pas quelle valeur retenir
		$cpt=0;
		// Témoin de la liste des matières trouvées pour une même matière notanet
		// Utile pour repérer les matières Notanet associées à deux matières Gepi pour lesquelles l'élève a une note
		$liste_matieres_gepi="";
		//if($tabmatieres[$j][0]!=''){
		if(($tabmatieres[$j][0]!='')&&($statut_matiere[$j]!='non dispensee dans l etablissement')){

			if($tabmatieres[$j]['socle']=='n') {

				//$ligne_NOTANET="$INE|$j";

				//$temoin_au_moins_une_note="n";

				$moyenne=NULL;
				//echo "<p><b>".$tabmatieres[$j][0]."</b><br />\n";
				for($k=0;$k<count($id_matiere[$j]);$k++){
					$alt=$alt*(-1);
					if((($type_brevet_ele==2)&&($j!=$indice_brevet_pro_lv))||
					($type_brevet_ele!=2)) {
						echo "<tr class='lig$alt'>\n";
					}
					else {
						echo "<tr style='background-color:lightblue;' title=\"Dans le cas du brevet série professionnelle, c'est la moyenne des notes de langue vivante, qu'il faut prendre en compte.\">\n";
					}
					//echo $id_matiere[$j][$k]."<br />\n";
					// A FAIRE: REQUETE moyenne pour la matière... si non vide... (test si note!="-" aussi?)

					//$sql="SELECT round(avg(n.note),1) as moyenne FROM matieres_notes n, j_eleves_classes c WHERE (n.periode='$num_periode' AND n.matiere='$matiere[$j]' AND c.id_classe='$id_classe' AND c.login = n.login AND n.statut =''  AND c.periode='$num_periode')";

					echo "<td><span style='color:green;'>$j</span></td>\n";
					echo "<td>".$id_matiere[$j][$k]."</td>\n";

					$temoin_moyenne="";
					//======================================================================
					//$sql="SELECT round(avg(note),1) as moyenne FROM matieres_notes WHERE (matiere='".$id_matiere[$j][$k]."' AND login='$ligne->login' AND statut ='')";

					// 20140325
					if((isset($tabmatieres[$j]['extraction_moyenne']))&&($tabmatieres[$j]['extraction_moyenne']=="n")) {
						$sql="SELECT mn.note moyenne FROM notanet_saisie mn WHERE (mn.matiere='".$id_matiere[$j][$k]."' AND mn.login='".$tab_ele['login']."')";
					}
					else {
						$sql="SELECT round(avg(mn.note),1) as moyenne FROM matieres_notes mn, j_groupes_matieres jgm WHERE (jgm.id_matiere='".$id_matiere[$j][$k]."' AND mn.login='".$tab_ele['login']."' AND mn.statut ='' AND mn.id_groupe=jgm.id_groupe)";
					}

					//echo "$sql<br />\n";
					$resultat_moy=mysql_query($sql);
					if(mysql_num_rows($resultat_moy)>0){
						$ligne_moy=mysql_fetch_object($resultat_moy);
						//echo "$ligne_moy->moyenne<br />";
						$moyenne=$ligne_moy->moyenne;
						if((isset($tabmatieres[$j]['extraction_moyenne']))&&($tabmatieres[$j]['extraction_moyenne']=="n")) {
							echo "<td style='font-weight:bold; text-align:center; color:blue; font-style: italic;";
							if($moyenne=="") {
								echo " background-color:red;";
							}
							echo "' title=\"Note saisie\">$moyenne</td>\n";
						}
						else {
							echo "<td style='font-weight:bold; text-align:center;' title=\"Moyenne des trois trimestres\">$moyenne</td>\n";
						}
						//$cpt++;
						if($moyenne!=""){
							$temoin_moyenne="oui";
						}
					}
					else{
						//echo "X<br />\n";
						// On ne passe jamais par là.
						// Le calcul de la moyenne avec $resultat_moy retourne NULL et on a toujours mysql_num_rows($resultat_moy)=1
						echo "<td style='font-weight:bold; text-align:center;'>X</td>\n";
					}
					echo "<!--\$temoin_moyenne=$temoin_moyenne-->\n";
					// Cette solution donne les infos, mais ne permet pas de contrôler si tout est OK...
					//======================================================================

					if((isset($tabmatieres[$j]['extraction_moyenne']))&&($tabmatieres[$j]['extraction_moyenne']=="n")) {
						echo "<td></td><td></td><td></td>\n";

						echo "<td style='font-weight:bold; text-align:center;";
						if($moyenne=="") {
							echo " background-color:red;";
						}
						echo "'>$moyenne</td>\n";

						if($moyenne=="") {
							$moyenne_arrondie="";

							if($statut_matiere[$j]=='imposee'){
								// Si la matière est imposée, alors il y a un problème à régler...
								$temoin_notanet_eleve="ERREUR";
								if($info_erreur==""){
									$info_erreur="Pas de moyenne à une matière non optionnelle: <b>".$id_matiere[$j][0]."</b><br />(<i><span style='font-size:xx-small;'>valeurs non numériques autorisées: ".$tabmatieres[$j][-3]."</span></i>)<br />";
								}
								else{
									$info_erreur=$info_erreur."Pas de moyenne à une matière non optionnelle: <b>".$id_matiere[$j][0]."</b><br />(<i><span style='font-size:xx-small;'>valeurs non numériques autorisées: ".$tabmatieres[$j][-3]."</span></i>)<br />";
								}
							}
						}
						else {
							$moyenne_arrondie=ceil($moyenne*2)/2;
						}

						echo "<td><input type='text' name='moy_$j"."_".$k."[$num_eleve]' id='n".$compteur_champs_notes."' value='".$moyenne_arrondie."' size='6' onKeyDown=\"clavier(this.id,event);\" onchange='changement()' autocomplete=\"off\" onfocus=\"javascript:this.select()\" />";
						echo "</td>\n";

						$moy_NOTANET[$j]="$moyenne_arrondie";

						// Témoin comme quoi, il y a une note pour l'enseignement notanet en cours
						$cpt++;

					}
					else {

						$total=0;
						$nbnotes=0;
						//$sql="SELECT DISTINCT num_periode FROM periodes WHERE id_classe='$id_classe[$i]' ORDER BY num_periode";
						$sql="SELECT DISTINCT num_periode, verouiller FROM periodes WHERE id_classe='$id_clas' ORDER BY num_periode";
						//echo "<td>$sql</td>";
						$resultat_periodes=mysql_query($sql);
						while($ligne_periodes=mysql_fetch_object($resultat_periodes)){
							//$sql="SELECT * FROM matieres_notes WHERE (matiere='".$id_matiere[$j][$k]."' AND login='$ligne->login' AND statut ='') ORDER BY periode";
							//$sql="SELECT * FROM matieres_notes WHERE (matiere='".$id_matiere[$j][$k]."' AND login='$ligne->login' AND statut ='' AND periode='$ligne_periodes->num_periode')";

							//===================================================================
							// SUR LE STATUT... IL FAUDRAIT VOIR CE QUE DONNENT LES dispensés,...
							// POUR POUVOIR LES CODER DANS L'EXPORT NOTANET
							//===================================================================
							//$sql="SELECT * FROM matieres_notes WHERE (matiere='".$id_matiere[$j][$k]."' AND login='$ligne->login' AND statut ='' AND periode='$ligne_periodes->num_periode')";
							$sql="SELECT mn.* FROM matieres_notes mn, j_groupes_matieres jgm WHERE (jgm.id_matiere='".$id_matiere[$j][$k]."' AND mn.login='".$tab_ele['login']."' AND mn.statut ='' AND mn.periode='$ligne_periodes->num_periode' AND mn.id_groupe=jgm.id_groupe)";

							//echo "<!-- $sql -->\n";
							//echo "$sql<br />\n";
							$resultat_notes=mysql_query($sql);
							//echo "<!-- mysql_num_rows(\$resultat_notes)=".mysql_num_rows($resultat_notes)." -->\n";
							if(mysql_num_rows($resultat_notes)>0){
								if(mysql_num_rows($resultat_notes)>1){


									// 20130429 : A VOIR : Dans le cas brevet professionnel, pour les langues vivantes, si on a 2, c'est bon, mais il faudrait faire la moyenne.
									//            Et comment stocker la liste des matières pour la fiche brevet? Refaire une extraction des listes de matières dans fiches_brevet.php ou stocker dans une table? Ou ajouter un champ dans la table notanet... ou notanet.matiere="AGL1|ESP2" et exploser suivant | dans fiches_brevet.php

									// 20130429
									/*
									if((($type_brevet_ele==2)&&($j!=$indice_brevet_pro_lv))||
									($type_brevet_ele!=2)) {
									*/
										//$infos="Erreur? Il y a plusieurs notes/moyennes pour une même période! ";
										$infos="<p>Erreur? Il y a plusieurs notes/moyennes pour une même période!<br />";

										//$infos.="<br />$sql<br />";

										$temoin_notanet_eleve="ERREUR";
										if($info_erreur==""){
											$info_erreur="Plusieurs notes/moyennes pour une même période.";

											$info_erreur.="<br />Dans ce cas, la moyenne est la somme des moyennes affichées divisée par le nombre de moyennes.<br />La valeur est correcte, s'il y a le même nombre de moyennes sur chaque trimestre et si on donne le même poids aux différentes moyennes.<br />";

										}
										else{
											$info_erreur=$info_erreur." - Plusieurs notes/moyennes pour une même période.";
										}
										$chaine_couleur=" bgcolor='red'";
									/*
									}
									// 20130429
									elseif(($type_brevet_ele==2)&&($j==$indice_brevet_pro_lv)) {

										$chaine_couleur=" bgcolor=' lightpink'";

									}
									else {
										// On ne passe jamais ici, il me semble...
										$infos="";
										$chaine_couleur="";
									}
									*/
								}
								else {
									$infos="";
									$chaine_couleur="";
								}

								// Il ne devrait y avoir qu'une seule valeur:
								echo "<td$chaine_couleur style='text-align: center;'>\n";
								//echo "<!-- ... -->\n";
								while($ligne_notes=mysql_fetch_object($resultat_notes)){
									//echo "<td>".$infos.$ligne_notes->note."</td>\n";

									//echo $infos.$ligne_notes->note." ";
									if($infos!="") {
										echo $infos."<b>".$ligne_notes->note."</b> ";
										//echo "<div style='font-size:xx-small;'>".$infos."</div>".$ligne_notes->note." ";
										//echo "<span style='font-size:xx-small;'>".$infos."</span>".$ligne_notes->note." ";
									}
									else {
										echo $ligne_notes->note." ";
									}

									// Le test devrait toujours être vrai puisqu'on a exclu les moyennes avec un statut non vide
									if(($ligne_notes->note!="")&&($ligne_notes->note!="-")){
										// PROBLEME: S'il y a plusieurs notes pour une même période, le total est faussé et la moyenne itou...
										// ... mais cela ne devrait pas arriver, ou alors la base GEPI n'est pas nette.
										$total=$total+$ligne_notes->note;
										$nbnotes++;
										//echo "<!-- \$total=$total\n \$nbnotes=$nbnotes-->\n";
										//echo "<\$total=$total\n \$nbnotes=$nbnotes>\n";


										//$temoin_au_moins_une_note="y";

										//echo "\$temoin_au_moins_une_note=$temoin_au_moins_une_note<br />";
										//echo "\$cpt=$cpt<br />";
									}
								}
								if($ligne_periodes->verouiller=='N') {
									echo "<img src='../images/icons/ico_attention.png' width='22' height='19' alt=\"ATTENTION: La période n'est pas verrouillée ! Les notes peuvent encore changer !\" title=\"ATTENTION: La période n'est pas verrouillée ! Les notes peuvent encore changer !\" />\n";
								}
								echo "</td>\n";
							}
							else{

								if($temoin_moyenne=="oui"){
									$chaine_couleur=" bgcolor='yellow'";
								}
								else{
									$chaine_couleur="";
								}

								//echo "<td>X</td>\n";
								// S'il n'y a pas de moyenne avec statut vide, on cherche si un statut dispensé ou autre est dans la table 'matieres_notes':
								//$sql="SELECT * FROM matieres_notes WHERE (matiere='".$id_matiere[$j][$k]."' AND login='$ligne->login' AND periode='$ligne_periodes->num_periode')";
								$sql="SELECT mn.* FROM matieres_notes mn, j_groupes_matieres jgm WHERE (jgm.id_matiere='".$id_matiere[$j][$k]."' AND mn.login='".$tab_ele['login']."' AND mn.periode='$ligne_periodes->num_periode' AND mn.id_groupe=jgm.id_groupe)";
								$resultat_notes=mysql_query($sql);
								if(mysql_num_rows($resultat_notes)>0){
									$ligne_notes=mysql_fetch_object($resultat_notes);
									if($ligne_notes->statut!=""){
										$chaine_couleur=" bgcolor='red'";
									}
									echo "<td$chaine_couleur style='text-align:center;'>".$ligne_notes->note." - ".$ligne_notes->statut;
								}
								else{
									echo "<td$chaine_couleur style='text-align:center;'>X";
								}

								if($ligne_periodes->verouiller=='N') {
									echo "<img src='../images/icons/ico_attention.png' width='22' height='19' alt=\"ATTENTION: La période n'est pas verrouillée ! Les notes peuvent encore changer !\" title=\"ATTENTION: La période n'est pas verrouillée ! Les notes peuvent encore changer !\" style='float:right' />\n";
								}

								echo "</td>\n";

							}
						}

						// Initialisation
						$moyenne_arrondie="";
						if($nbnotes>0) {
							$cpt++;
							$liste_matieres_gepi.=" ".$id_matiere[$j][$k];
							$moyenne=round($total/$nbnotes,1);
							//echo "<td style='font-weight:bold; text-align:center;'>$total/$nbnotes = $moyenne</td>\n";
							echo "<td style='font-weight:bold; text-align:center;'>$moyenne</td>\n";

							$moyenne_arrondie=ceil($moyenne*2)/2;

							// 20130429
							if((($type_brevet_ele==2)&&($j!=$indice_brevet_pro_lv))||
							($type_brevet_ele!=2)) {
								// Note provenant de la moyenne
								echo "<td><input type='text' name='moy_$j"."_".$k."[$num_eleve]' id='n".$compteur_champs_notes."' value='".$moyenne_arrondie."' size='6' onKeyDown=\"clavier(this.id,event);\" onchange='changement()' autocomplete=\"off\" onfocus=\"javascript:this.select()\" />";
								//$compteur_champs_notes++;
								//echo "<input type='hidden' name='matiere_".$j."_[$num_eleve]' value='".$id_matiere[$j][$k]."' size='6' />";
								echo "</td>\n";

								//$moy_NOTANET[$j]="$moyenne";
								$moy_NOTANET[$j]="$moyenne_arrondie";
							}
							else {
								$total_brevet_pro_lv+=$moyenne;
								$temoin_brevet_pro_lv++;
								echo "<td></td>\n";

								if($liste_matiere_brevet_pro_lv!="") {$liste_matiere_brevet_pro_lv.="|";}
								$liste_matiere_brevet_pro_lv.=$id_matiere[$j][$k];

								if($chaine_total_brevet_pro_lv!="") {$chaine_total_brevet_pro_lv.="+";}
								$chaine_total_brevet_pro_lv.=$moyenne;

								// Au dernier tour dans la matière LV pour le brevet pro, le contenu est correct:
								$moy_NOTANET[$j]=ceil(($total_brevet_pro_lv/$temoin_brevet_pro_lv)*2)/2;
							}

						}
						else {
							// Pas de note.

							$sql="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_matieres jgm WHERE (jgm.id_matiere='".$id_matiere[$j][$k]."' AND jeg.login='".$tab_ele['login']."' AND jgm.id_groupe=jeg.id_groupe);";
							$test_ele_matiere=mysql_query($sql);

							//if((($statut_matiere[$j]=='imposee'))&&($k+1==count($id_matiere[$j]))&&($moy_NOTANET[$j]=="")){
							if((($statut_matiere[$j]=='imposee'))&&(mysql_num_rows($test_ele_matiere)!=0)&&($moy_NOTANET[$j]=="")) {
								$bgmoy="background-color:red";
							}
							else{
								$bgmoy="";
							}


							echo "<td style='font-weight:bold; text-align:center;$bgmoy'>X</td>\n";

							// 20130429
							if((($type_brevet_ele==2)&&($j!=$indice_brevet_pro_lv))||
							($type_brevet_ele!=2)) {
								echo "<td><input type='text' name='moy_$j"."_".$k."[$num_eleve]' id='n".$compteur_champs_notes."' value='' size='6' onKeyDown=\"clavier(this.id,event);\" onchange='changement()' autocomplete=\"off\" onfocus=\"javascript:this.select()\" /></td>\n";
							}
							else {
								echo "<td></td>\n";
							}
							//$compteur_champs_notes++;
							//echo "<td></td>\n";
						}
					}



					if($affiche_enregistrements_precedents=="y") {
						echo "<td";
						// DEBUG
						//echo " bgcolor=plum";
						echo ">\n";
						// DEBUG:
						//echo "REG_prec:";
						$sql="SELECT note FROM notanet WHERE login='".$tab_ele['login']."' AND matiere='".$id_matiere[$j][$k]."' AND notanet_mat='".$tabmatieres[$j][0]."' AND id_mat='$j';";
						$enr=mysql_query($sql);
						if(mysql_num_rows($enr)>0) {
							$lig_enr=mysql_fetch_object($enr);

							if($moyenne_arrondie!=$lig_enr->note) {
								echo "<a href=\"#tableau_des_moyennes_eleve_$num_eleve\" onclick=\"document.getElementById('n".$compteur_champs_notes."').value='$lig_enr->note'; return false;\"><img src='../images/icons/back.png' width='16' height='16' alt='Rétablir la valeur enregistrée' /></a>";
								echo "&nbsp;";
							}

							echo "<span id='note_precedemment_enregistree_$compteur_champs_notes'>".$lig_enr->note."</span>";
						}
						else {
							echo "&nbsp;";
						}
						echo "</td>\n";
					}

					$compteur_champs_notes++;
					echo "</tr>\n";
				}
				/*
				if($temoin_notanet_eleve!="ERREUR"){
					echo "<tr><td>$ligne_NOTANET</td></tr>\n";
				}
				*/
				//echo "</p>\n";

				//echo "<tr><td>\$cpt=$cpt</td><td>\$statut_matiere[$j]=$statut_matiere[$j]</td></tr>";
				if($cpt==0){
					// Pas de moyenne trouvée pour cet élève.
					if($statut_matiere[$j]=='imposee'){
						// Si la matière est imposée, alors il y a un problème à régler...
						$temoin_notanet_eleve="ERREUR";
						if($info_erreur==""){
							//$info_erreur="Pas de moyenne à une matière non optionnelle: ".$id_matiere[$j][0];
							$info_erreur="Pas de moyenne à une matière non optionnelle: <b>".$id_matiere[$j][0]."</b><br />(<i><span style='font-size:xx-small;'>valeurs non numériques autorisées: ".$tabmatieres[$j][-3]."</span></i>)<br />";
							//$tabmatieres[$j][-3]
						}
						else{
							//$info_erreur=$info_erreur." - Pas de moyenne à une matière non optionnelle: ".$id_matiere[$j][0];
							$info_erreur=$info_erreur."Pas de moyenne à une matière non optionnelle: <b>".$id_matiere[$j][0]."</b><br />(<i><span style='font-size:xx-small;'>valeurs non numériques autorisées: ".$tabmatieres[$j][-3]."</span></i>)<br />";
						}
					}
				}
			}
			else {
				// SOCLES B2I ET A2
				$note_b2i="";
				$note_a2="";

				$sql="SELECT * FROM notanet_socles WHERE login='".$tab_ele['login']."';";
				$res_soc=mysql_query($sql);
				if(mysql_num_rows($res_soc)>0) {
					$lig_soc=mysql_fetch_object($res_soc);
					$note_b2i=$lig_soc->b2i;
					$note_a2=$lig_soc->a2;
				}

				if($j==114) {

					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td><span style='color:green;'>$j</span></td>\n";
					echo "<td>".$tabmatieres[$j][0]."</td>\n";
					echo "<td>&nbsp;</td>\n";

					echo "<td>&nbsp;</td>\n";
					echo "<td>&nbsp;</td>\n";
					echo "<td>&nbsp;</td>\n";

					//if($note_b2i!="") {
					if(($note_b2i=="MS")||($note_b2i=="ME")||($note_b2i=="MN")||($note_b2i=="AB")) {
						$moy_NOTANET[$j]=$note_b2i;
						echo "<td style='font-weight:bold;'>".$note_b2i."</td>\n";
					}
					else {
						echo "<td style='font-weight:bold; background-color:red;'>&nbsp;</td>\n";
						$temoin_notanet_eleve="ERREUR";
					}
					echo "<td><input type='text' name='moy_$j"."_0[$num_eleve]' value='$note_b2i' size='6' onchange='changement()' /></td>\n";


					if($affiche_enregistrements_precedents=="y") {
						echo "<td>\n";
						$sql="SELECT note FROM notanet WHERE login='".$tab_ele['login']."' AND notanet_mat='".$tabmatieres[$j][0]."' AND id_mat='$j';";
						$enr=mysql_query($sql);
						if(mysql_num_rows($enr)>0) {
							$lig_enr=mysql_fetch_object($enr);
							echo $lig_enr->note;
						}
						else {
							echo "&nbsp;";
						}
						echo "</td>\n";
					}


					echo "</tr>\n";

				}
				elseif($j==115) {

					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td><span style='color:green;'>$j</span></td>\n";
					echo "<td>".$tabmatieres[$j][0]."</td>\n";
					echo "<td>&nbsp;</td>\n";

					echo "<td>&nbsp;</td>\n";
					echo "<td>&nbsp;</td>\n";
					echo "<td>&nbsp;</td>\n";

					//if($note_a2!="") {
					//if(($note_a2=="MS")||($note_a2=="ME")||($note_a2=="AB")) {
					if(($note_a2=="MS")||($note_a2=="ME")||($note_a2=="MN")||($note_a2=="AB")) {
						$moy_NOTANET[$j]=$note_a2;
						echo "<td style='font-weight:bold;'>".$note_a2."</td>\n";
					}
					// CELA PEUT ETRE OPTIONNEL
					else {
						echo "<td style='font-weight:bold; background-color:red;'>&nbsp;</td>\n";
						$temoin_notanet_eleve="ERREUR";
					}
					echo "<td><input type='text' name='moy_$j"."_0[$num_eleve]' value='$note_a2' size='6' onchange='changement()' /></td>\n";

					if($affiche_enregistrements_precedents=="y") {
						echo "<td>\n";
						$sql="SELECT note FROM notanet WHERE login='".$tab_ele['login']."' AND notanet_mat='".$tabmatieres[$j][0]."' AND id_mat='$j';";
						$enr=mysql_query($sql);
						if(mysql_num_rows($enr)>0) {
							$lig_enr=mysql_fetch_object($enr);
							echo $lig_enr->note;
						}
						else {
							echo "&nbsp;";
						}
						echo "</td>\n";
					}

					echo "</tr>\n";

					if($note_a2!="") {$moy_NOTANET[$j]=$note_a2;}

				}
				elseif($j==130) {

					$note_lvr="";
	
					$sql="SELECT * FROM notanet_lvr_ele WHERE login='".$tab_ele['login']."';";
					$res_lvr=mysql_query($sql);
					if(mysql_num_rows($res_lvr)>0) {
						$lig_lvr=mysql_fetch_object($res_lvr);
						$note_lvr=$lig_lvr->note;

						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td><span style='color:green;'>$j</span></td>\n";
						echo "<td>".$tabmatieres[$j][0]."</td>\n";
						echo "<td>&nbsp;</td>\n";
	
						echo "<td>&nbsp;</td>\n";
						echo "<td>&nbsp;</td>\n";
						echo "<td>&nbsp;</td>\n";
	
	
						//if($note_b2i!="") {
						if(($note_lvr=="VA")||($note_lvr=="NV")) {
							$moy_NOTANET[$j]=$note_lvr;
							echo "<td style='font-weight:bold;'>".$note_lvr."</td>\n";
						}
						else {
							echo "<td style='font-weight:bold; background-color:red;'>&nbsp;</td>\n";
							$temoin_notanet_eleve="ERREUR";
						}
						echo "<td><input type='text' name='moy_$j"."_0[$num_eleve]' value='$note_lvr' size='6' onchange='changement()' /></td>\n";
	
	
						if($affiche_enregistrements_precedents=="y") {
							echo "<td>\n";
							$sql="SELECT note FROM notanet WHERE login='".$tab_ele['login']."' AND notanet_mat='".$tabmatieres[$j][0]."' AND id_mat='$j';";
							$enr=mysql_query($sql);
							if(mysql_num_rows($enr)>0) {
								$lig_enr=mysql_fetch_object($enr);
								echo $lig_enr->note;
							}
							else {
								echo "&nbsp;";
							}
							echo "</td>\n";
						}
	
	
						echo "</tr>\n";
					}
				}
			}
		}

		// 20130429
		if(((($type_brevet_ele==2)&&($j!=$indice_brevet_pro_lv))||
		($type_brevet_ele!=2))
		&&($cpt>1)) {
			$temoin_notanet_eleve="ERREUR";
			// Un élève a des notes dans deux options d'un même choix NOTANET (par exemple AGL1 et ALL1)
			if($info_erreur==""){
				//$info_erreur="Plusieurs options d'une même matière.";
				$info_erreur="Plusieurs options d'une même matière: <b>$liste_matieres_gepi</b><br />(<span style='font-size:x-small'><i>il faudra vider le champ de formulaire correspondant à la matière à abandonner</i></span>)<br />";
			}
			else{
				//$info_erreur=$info_erreur." - Plusieurs options d'une même matière.";
				$info_erreur=$info_erreur."Plusieurs options d'une même matière: <b>$liste_matieres_gepi</b><br />(<span style='font-size:x-small'><i>il faudra vider le champ de formulaire correspondant à la matière à abandonner</i></span>)<br />";
			}
		}
	}
	echo "</table>\n";


	// Pour présenter à côté, le résultat:
	echo "</td>\n";
	echo "<td valign='top' style='vertical-align:top;'>\n";
	//echo "\$temoin_notanet_eleve=$temoin_notanet_eleve<br />";
	if($temoin_notanet_eleve=="ERREUR"){
		echo "<b style='color:red;'>ERREUR:</b> $info_erreur";
	}
	else{
		//echo "$INE|TOT|$TOT|<br />\n";
		//echo "---";
		$TOT=0;


		echo "<p>\n";
		echo "Portion de fichier générée:<br />";
		for($j=$indice_premiere_matiere;$j<=$indice_max_matieres;$j++){
			// Pour les matières NOTANET existantes:
			if($tabmatieres[$j][0]!=''){
				// Si une moyenne a été extraite
				// (c'est-à-dire si l'élève a la matière et que l'extraction a réussi (donc pas d'ERREUR))
				//echo "\$tabmatieres[$j][-1]=".$tabmatieres[$j][-1]."<br />\n";
				//echo "\$moy_NOTANET[$j]=".$moy_NOTANET[$j]."<br />\n";
				if($moy_NOTANET[$j]!=""){
					$ligne_NOTANET="$INE|".sprintf("%03d",$j);

					if($tabmatieres[$j]['socle']=="y"){
						$ligne_NOTANET=$ligne_NOTANET."|".$moy_NOTANET[$j]."|";
					}
					elseif($tabmatieres[$j][-1]=="POINTS"){
						//$ligne_NOTANET=$ligne_NOTANET."|$moy_NOTANET[$j]|";
						//$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($moy_NOTANET[$j])."|";
						// Pour les brevets dans lesquels certaines notes sont sur 40 ou 60 au lieu de 20:
						$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($moy_NOTANET[$j]*$tabmatieres[$j][-2])."|";
						//$TOT=$TOT+$moy_NOTANET[$j];
						//$TOT=$TOT+round($moy_NOTANET[$j]*2)/2;
						$TOT=$TOT+round($moy_NOTANET[$j]*$tabmatieres[$j][-2]*2)/2;
					}
					else{
						if($tabmatieres[$j][-1]=="PTSUP"){
							$ptsup=$moy_NOTANET[$j]-10;
							if($ptsup>0){
								//$ligne_NOTANET=$ligne_NOTANET."|$ptsup|";
								//$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($ptsup)."|";
								$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($ptsup*$tabmatieres[$j][-2])."|";
								//$TOT=$TOT+$ptsup;
								//$TOT=$TOT+round($ptsup*2)/2;
								$TOT=$TOT+round($ptsup*$tabmatieres[$j][-2]*2)/2;
							}
							else{
								$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet(0)."|";
							}
						}
						else{
							//$tabmatieres[$j][-1]="NOTNONCA";
							// On ne modifie pas... euh si... une ligne est insérée, mais elle n'intervient pas dans le calcul du TOTal.
							if($tabmatieres[$j][-1]=="NOTNONCA"){
								//$ligne_NOTANET=$ligne_NOTANET."|$moy_NOTANET[$j]|";
								$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($moy_NOTANET[$j])."|";
							}
						}
					}
					echo colore_ligne_notanet($ligne_NOTANET)."<br />\n";
					$tabnotanet[]=$ligne_NOTANET;
					//$fichtmp=fopen($fich_notanet,"a+");
					//fwrite($fichtmp,$ligne_NOTANET."\n");
					//fclose($fichtmp);
				}
			}
		}

		// Dans le cas brevet PRO, il ne faut retenir qu'une seule des deux matières 103 et 104
		if(($tab_ele['type_brevet']==2)||($tab_ele['type_brevet']==3)) {
			$num_matiere_LV1=103;
			$num_matiere_ScPhy=104;
			if(($moy_NOTANET[$num_matiere_LV1]!="AB")&&($moy_NOTANET[$num_matiere_LV1]!="DI")&&($moy_NOTANET[$num_matiere_LV1]!="NN")){
				if(($moy_NOTANET[$num_matiere_ScPhy]!="AB")&&($moy_NOTANET[$num_matiere_ScPhy]!="DI")&&($moy_NOTANET[$num_matiere_ScPhy]!="NN")) {
					// Il ne faut retenir qu'une seule des deux notes
					if($moy_NOTANET[$num_matiere_ScPhy]>$moy_NOTANET[$num_matiere_LV1]) {
						$TOT-=round($moy_NOTANET[$num_matiere_LV1]*$tabmatieres[$num_matiere_LV1][-2]*2)/2;
					}
					else {
						$TOT-=round($moy_NOTANET[$num_matiere_ScPhy]*$tabmatieres[$num_matiere_ScPhy][-2]*2)/2;
					}
				}
			}
		}

		//echo "$INE|TOT|$TOT|<br />\n";
		echo colore_ligne_notanet("$INE|TOT|".sprintf("%02.2f",$TOT)."|")."<br />\n";
		$tabnotanet[]="$INE|TOT|".sprintf("%02.2f",$TOT)."|";
		//$fichtmp=fopen($fich_notanet,"a+");
		// PROBLEME: $TOT peut dépasser 100... quel doit être le formatage à gauche quand on est en dessous de 100?
		//fwrite($fichtmp,"$INE|TOT|$TOT|\n");
		//fwrite($fichtmp,"$INE|TOT|".formate_note_notanet($TOT)."|\n");
		//fwrite($fichtmp,"$INE|TOT|".sprintf("%02.2f",$TOT)."|\n");
		//fclose($fichtmp);
		echo "</p>\n";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}

// Fonctions pour les Fiches Brevet PDF
function adjust_size_font($texte,$largeur_dispo,$h_max_font,$increment,$multiligne=NULL) {
	global $pdf;

	$hauteur_texte=$h_max_font;
	$pdf->SetFontSize($hauteur_texte);
	$taille_texte_total=$pdf->GetStringWidth($texte);

	if($multiligne!='y') {
		$grandeur_texte='test';
		while($grandeur_texte!='ok') {
			if($largeur_dispo<$taille_texte_total) {
				$hauteur_texte=$hauteur_texte-$increment;
				$pdf->SetFontSize($hauteur_texte);
				$taille_texte_total = $pdf->GetStringWidth($texte);
			}
			else {
				$grandeur_texte='ok';
			}
		}
		return $hauteur_texte;
	}
	else {
		//echo "<p>adjust_size_font($texte,$largeur_dispo,$h_max_font,$increment,$multiligne)<br />";
		$nb_lig=1;
		if($largeur_dispo<$taille_texte_total) {
			$nb_lig=2;
		}
		//echo "\$taille_texte_total=$taille_texte_total<br />";
		//echo "\$nb_lig=$nb_lig<br />";
		$taille_texte_total=$taille_texte_total/$nb_lig;
		//echo "\$taille_texte_total=$taille_texte_total<br />";

		$grandeur_texte='test';
		while($grandeur_texte!='ok') {
			if($largeur_dispo<$taille_texte_total) {
				$hauteur_texte=$hauteur_texte-$increment;
				$pdf->SetFontSize($hauteur_texte);
				//echo "\$hauteur_texte=$hauteur_texte<br />";
				$taille_texte_total = $pdf->GetStringWidth($texte)/$nb_lig;
				//echo "\$taille_texte_total=$taille_texte_total<br />";
			}
			else {
				$grandeur_texte='ok';
			}
		}
		//echo "On ressort avec \$hauteur_texte=$hauteur_texte</p>";
		return $hauteur_texte;
	}
}

function fs_pt2mm($fs) {
	//(proportionnalité: 100pt -> 26mm)
	return $fs*26/100;
}

/*
// Liste de codes du module OOo
	[eleves.101.0]  ->      note de l'élève non coefficientée
	[eleves.101.1]  ->      note de l'élève coefficientée
	[eleves.101.2]  ->      Nom de matière Gepi
	[eleves.101.3]  ->      moyenne de la classe
	[eleves.101.4]  ->      appréciation
	où 101 est le numéro de matière
	[eleves.totalpoints] /[eleves.totalcoef]
	[eleves.appreciation]	->	Avis du chef d'etablissement
	...
*/
?>
