<?php

$tab_type_brevet=array();
$tab_type_brevet[0]="COLLEGE, option de série LV2";
$tab_type_brevet[1]="COLLEGE, option de série DP6";
//$tab_type_brevet[2]="COLLEGE, option de série TECHNOLOGIE traditionnelle";
//$tab_type_brevet[3]="COLLEGE, option de série TECHNOLOGIE DP6";
$tab_type_brevet[2]="PROFESSIONNELLE, sans option de série";
$tab_type_brevet[3]="PROFESSIONNELLE, option de série DP6";
$tab_type_brevet[4]="PROFESSIONNELLE, option de série AGRICOLE";
//$tab_type_brevet[4]="TECHNOLOGIQUE, sans option de série";
//$tab_type_brevet[5]="TECHNOLOGIQUE, option de série AGRICOLE";
$tab_type_brevet[5]="TECHNOLOGIQUE, sans option de série";
$tab_type_brevet[6]="TECHNOLOGIQUE, option de série DP6";
$tab_type_brevet[7]="TECHNOLOGIQUE, option de série AGRICOLE";


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

function tabmatieres($type_brevet){
	//====================
	// AJOUT: boireaus 20080329
	global $tabmatieres;
	unset($tabmatieres);
	//====================

	switch($type_brevet){
		case 0:
			// COLLEGE, option de série LV2
			//$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[103][0]='LANGUE VIVANTE 1';
			$tabmatieres[104][0]='SCIENCES DE LA VIE ET DE LA TERRE';
			$tabmatieres[105][0]='PHYSIQUE-CHIMIE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='ARTS PLASTIQUES';
			$tabmatieres[108][0]='EDUCATION MUSICALE';
			$tabmatieres[109][0]='TECHNOLOGIE';
			//$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE';
			$tabmatieres[110][0]='LANGUE VIVANTE 2';
			//$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE OU DECOUVERTE PROFESSIONNELLE (module de 6 heures)';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='VIE SCOLAIRE';
			//$tabmatieres[113][0]='OPTION FACULTATIVE (1)';
			$tabmatieres[113][0]='OPTION FACULTATIVE';

			$tabmatieres[114][0]='SOCLE B2I';
			$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';

			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE';
			$tabmatieres[122][0]='EDUCATION CIVIQUE';

			// Mode de calcul:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			// Optionnelle
			$tabmatieres[113][-1]='PTSUP';
			// A titre indicatif
			$tabmatieres[121][-1]='NOTNONCA';
			$tabmatieres[122][-1]='NOTNONCA';

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
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
			$tabmatieres[110][-3]='AB DI';
			$tabmatieres[113][-3]='AB DI';

			$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			$tabmatieres[115][-3]='MS ME MN AB';


			// Colonnes pour les fiches brevet:
			for($j=101;$j<=122;$j++){
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
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j]['socle']='n';
			}
			$tabmatieres[114]['socle']='y';
			$tabmatieres[115]['socle']='y';


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
			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[103][0]='LANGUE VIVANTE 1';
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
			$tabmatieres[112][0]='VIE SCOLAIRE';
			//$tabmatieres[113][0]='OPTION FACULTATIVE (1)';
			$tabmatieres[113][0]='OPTION FACULTATIVE';

			$tabmatieres[114][0]='SOCLE B2I';
			$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';

			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE';
			$tabmatieres[122][0]='EDUCATION CIVIQUE';

			// Mode de calcul:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[113][-1]='PTSUP';
			$tabmatieres[121][-1]='NOTNONCA';
			$tabmatieres[122][-1]='NOTNONCA';

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			// DP6
			$tabmatieres[110][-2]=2;
			// A titre indicatif
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
			$tabmatieres[110][-3]='AB DI';
			$tabmatieres[113][-3]='AB DI';

			$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			$tabmatieres[115][-3]='MS ME MN AB';


			// Colonnes pour les fiches brevet:
			for($j=101;$j<=122;$j++){
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
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j]['socle']='n';
			}
			$tabmatieres[114]['socle']='y';
			$tabmatieres[115]['socle']='y';

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
			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			//$tabmatieres[103][0]='LANGUE VIVANTE 1';
			$tabmatieres[103][0]='LANGUE VIVANTE';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE OU SCIENCES PHYSIQUES';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			//$tabmatieres[104][0]='';
			$tabmatieres[105][0]='VIE SOCIALE ET PROFESSIONNELLE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION ARTISTIQUE';
			$tabmatieres[108][0]='TECHNOLOGIE';
			$tabmatieres[109][0]='';
			$tabmatieres[110][0]='';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='VIE SCOLAIRE';
			$tabmatieres[113][0]='';

			$tabmatieres[114][0]='SOCLE B2I';
			$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';

			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE EDUCATION CIVIQUE';
			//$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			$tabmatieres[122][0]='';
			//$tabmatieres[122][0]='EDUCATION CIVIQUE';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[121][-1]='NOTNONCA';
			//$tabmatieres[122][-1]='NOTNONCA';

			// PROBLEME: TECHNOLOGIE POINTS /60
			//           GEPI ne doit donner que des notes sur 20.
			//           Il faudrait donc multiplier par trois...

			// Par ailleurs, les candidats sont inscrits soit en LV1 soit en Sciences-physiques
			// Il faudrait donc considérer les deux matières commme optionnelles et on a alors un problème pour relever une note manquante...

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			$tabmatieres[108][-2]=3;
			$tabmatieres[121][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI NN';
			$tabmatieres[108][-3]='AB DI';

			$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			$tabmatieres[115][-3]='MS ME MN AB';


			// Colonnes pour les fiches brevet:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j]['fb_col'][1]=20;
				$tabmatieres[$j]['fb_col'][2]=20;
			}
			// Technologie
			$tabmatieres[108]['fb_col'][1]=60;
			$tabmatieres[108]['fb_col'][2]=40;
			// DP6: Je n'ai pas le numéro pour la DP6... est-ce bien le 110
			$tabmatieres[111]['fb_col'][1]="X";
			$tabmatieres[111]['fb_col'][2]=60;
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
			$tabmatieres[111]["lig_speciale"]="DÉCOUVERTE PROFESSIONNELLE 6 heures";


			// Intitulé de la ligne pour la fiche brevet option DP6h
			$tabmatieres[111]['fb_lig_alt']="Découverte professionnelle 6 heures";
			// L'indice 111 n'est pas présent sinon en PROFESSIONNELLE sans option de série


			// POUR NE PAS FAIRE D'ASSOCIATION AVEC DES MATIERES
			// ET NE PAS FAIRE L'EXTRATION DES MOYENNES DANS LES MEMES TABLES
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j]['socle']='n';
			}
			$tabmatieres[114]['socle']='y';
			$tabmatieres[115]['socle']='y';

			break;
		case 3:
			// PROFESSIONNELLE, option de série DP6
			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[103][0]='LANGUE VIVANTE 1';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE OU SCIENCES PHYSIQUES';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			//$tabmatieres[104][0]='';
			$tabmatieres[105][0]='VIE SOCIALE ET PROFESSIONNELLE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION ARTISTIQUE';
			$tabmatieres[108][0]='TECHNOLOGIE';
			$tabmatieres[109][0]='';
			// DP6 A PLACER....
			$tabmatieres[110][0]='';
			$tabmatieres[111][0]='DÉCOUVERTE PROFESSIONNELLE (module 6 heures)';
			$tabmatieres[112][0]='VIE SCOLAIRE';
			$tabmatieres[113][0]='';

			$tabmatieres[114][0]='SOCLE B2I';
			$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';

			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE EDUCATION CIVIQUE';
			//$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			$tabmatieres[122][0]='';
			//$tabmatieres[122][0]='EDUCATION CIVIQUE';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[121][-1]='NOTNONCA';
			//$tabmatieres[122][-1]='NOTNONCA';

			// PROBLEME: TECHNOLOGIE POINTS /60
			//           GEPI ne doit donner que des notes sur 20.
			//           Il faudrait donc multiplier par trois...

			// Par ailleurs, les candidats sont inscrits soit en LV1 soit en Sciences-physiques
			// Il faudrait donc considérer les deux matières commme optionnelles et on a alors un problème pour relever une note manquante...

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			$tabmatieres[108][-2]=2;
			$tabmatieres[111][-2]=3;
			// DP6: $tabmatieres[???][-2]=3;
			$tabmatieres[121][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI NN';
			$tabmatieres[108][-3]='AB DI';
			$tabmatieres[111][-3]='AB DI';

			$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			$tabmatieres[115][-3]='MS ME MN AB';


			// Colonnes pour les fiches brevet:
			for($j=101;$j<=122;$j++){
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
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j]['socle']='n';
			}
			$tabmatieres[114]['socle']='y';
			$tabmatieres[115]['socle']='y';

			break;
		case 4:
			// PROFESSIONNELLE, option de série AGRICOLE
			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[103][0]='LANGUE VIVANTE 1';
			$tabmatieres[104][0]='';
			$tabmatieres[105][0]='ECONOMIE FAMILIALE ET SOCIALE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION SOCIO-CULTURELLE';
			// CES TROIS Là DEVRAIENT ETRE SUR UNE MEME LIGNE POUR LES FICHES BREVET
			$tabmatieres[108][0]='TECHNOLOGIE';
			$tabmatieres[109][0]='SCIENCES BIOLOGIQUES';
			$tabmatieres[110][0]='SCIENCES PHYSIQUES';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='VIE SCOLAIRE';
			$tabmatieres[113][0]='';

			$tabmatieres[114][0]='SOCLE B2I';
			$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';

			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE EDUCATION CIVIQUE';
			//$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			$tabmatieres[122][0]='';
			//$tabmatieres[122][0]='EDUCATION CIVIQUE';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[121][-1]='NOTNONCA';
			//$tabmatieres[122][-1]='NOTNONCA';

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			//$tabmatieres[108][-2]=3;
			//$tabmatieres[109][-2]=3;
			//$tabmatieres[110][-2]=3;
			$tabmatieres[121][-2]=0;
			//$tabmatieres[122][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI';
			$tabmatieres[108][-3]='AB DI';
			$tabmatieres[109][-3]='AB DI';
			$tabmatieres[110][-3]='AB DI';

			$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			$tabmatieres[115][-3]='MS ME MN AB';



			$tabmatieres["num_fb_col"]=1;

			// Colonnes pour les fiches brevet:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j]['fb_col'][1]=20;
			}
			// Technologie
			//$tabmatieres[109]['fb_col'][1]=60;


			// Il n'y a qu'une seule colonne pour les fiches brevet en agricole
			/*
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



			// Colonnes pour les fiches brevet:
			for($j=101;$j<=122;$j++){
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
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j]['socle']='n';
			}
			$tabmatieres[114]['socle']='y';
			$tabmatieres[115]['socle']='y';


			break;
		case 5:
			// TECHNOLOGIQUE, sans option de série
			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[103][0]='LANGUE VIVANTE 1';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			$tabmatieres[105][0]='ECONOMIE FAMILIALE ET SOCIALE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION ARTISTIQUE';
			$tabmatieres[108][0]='TECHNOLOGIE';
			$tabmatieres[109][0]='';
			$tabmatieres[110][0]='';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='VIE SCOLAIRE';
			$tabmatieres[113][0]='';

			$tabmatieres[114][0]='SOCLE B2I';
			$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';

			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE EDUCATION CIVIQUE';
			//$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			$tabmatieres[122][0]='';
			//$tabmatieres[122][0]='EDUCATION CIVIQUE';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[121][-1]='NOTNONCA';

			// PROBLEME: TECHNOLOGIE POINTS /40
			//           GEPI ne doit donner que des notes sur 20.
			//           Il faudrait donc multiplier par deux...

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			$tabmatieres[108][-2]=2;
			$tabmatieres[121][-2]=0;
			//$tabmatieres[122][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI NN';
			$tabmatieres[108][-3]='AB DI';

			$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			$tabmatieres[115][-3]='MS ME MN AB';


			// Colonnes pour les fiches brevet:
			for($j=101;$j<=122;$j++){
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
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j]['socle']='n';
			}
			$tabmatieres[114]['socle']='y';
			$tabmatieres[115]['socle']='y';

			break;
		case 6:
			// TECHNOLOGIQUE, option de série DP6
			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[103][0]='LANGUE VIVANTE 1';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			$tabmatieres[105][0]='ECONOMIE FAMILIALE ET SOCIALE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION ARTISTIQUE';
			$tabmatieres[108][0]='TECHNOLOGIE';
			$tabmatieres[109][0]='';
			$tabmatieres[110][0]='DÉCOUVERTE PROFESSIONNELLE (module 6 heures)';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='VIE SCOLAIRE';
			$tabmatieres[113][0]='';

			$tabmatieres[114][0]='SOCLE B2I';
			$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';

			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE EDUCATION CIVIQUE';
			//$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			$tabmatieres[122][0]='';
			//$tabmatieres[122][0]='EDUCATION CIVIQUE';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[121][-1]='NOTNONCA';

			// PROBLEME: TECHNOLOGIE POINTS /40
			//           GEPI ne doit donner que des notes sur 20.
			//           Il faudrait donc multiplier par deux...

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			//$tabmatieres[108][-2]=2;
			$tabmatieres[110][-2]=2;
			$tabmatieres[121][-2]=0;
			//$tabmatieres[122][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI NN';
			$tabmatieres[108][-3]='AB DI';
			$tabmatieres[110][-3]='AB DI';

			$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			$tabmatieres[115][-3]='MS ME MN AB';


			// Colonnes pour les fiches brevet:
			for($j=101;$j<=122;$j++){
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
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j]['socle']='n';
			}
			$tabmatieres[114]['socle']='y';
			$tabmatieres[115]['socle']='y';


			break;
		case 7:
			// TECHNOLOGIQUE, option de série AGRICOLE
			$tabmatieres[101][0]='FRANÇAIS';
			$tabmatieres[102][0]='MATHÉMATIQUES';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[103][0]='LANGUE VIVANTE 1';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			$tabmatieres[105][0]='ECONOMIE FAMILIALE ET SOCIALE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION SOCIOCULTURELLE';
			$tabmatieres[108][0]='SCIENCES BIOLOGIQUES';
			$tabmatieres[109][0]='TECHNO SECTEUR TECHNIQUES AGRICOLES, ACTIVITES TERTIAIRES';
			$tabmatieres[110][0]='';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='VIE SCOLAIRE';
			$tabmatieres[113][0]='';

			$tabmatieres[114][0]='SOCLE B2I';
			$tabmatieres[115][0]='SOCLE NIVEAU A2 DE LANGUE';

			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GÉOGRAPHIE EDUCATION CIVIQUE';
			//$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			$tabmatieres[122][0]='';
			//$tabmatieres[122][0]='EDUCATION CIVIQUE';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[121][-1]='NOTNONCA';

			// Coefficients:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-2]=1;
			}
			//$tabmatieres[109][-2]=2;
			$tabmatieres[121][-2]=0;

			// Notes spéciales autorisées:
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-3]='AB';
			}
			$tabmatieres[103][-3]='AB DI';
			$tabmatieres[104][-3]='AB DI';
			$tabmatieres[105][-3]='AB DI';
			$tabmatieres[106][-3]='AB DI NN';
			$tabmatieres[107][-3]='AB DI';
			$tabmatieres[108][-3]='AB DI';
			$tabmatieres[109][-3]='AB DI';

			$tabmatieres[114][-3]='MS ME MN AB';
			//$tabmatieres[115][-3]='MS ME AB';
			$tabmatieres[115][-3]='MS ME MN AB';


			// Colonnes pour les fiches brevet:
			// Il n'y a qu'une seule colonne pour les fiches brevet en agricole
			/*
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
			*/

			// POUR NE PAS FAIRE D'ASSOCIATION AVEC DES MATIERES
			// ET NE PAS FAIRE L'EXTRATION DES MOYENNES DANS LES MEMES TABLES
			for($j=101;$j<=122;$j++){
				$tabmatieres[$j]['socle']='n';
			}
			$tabmatieres[114]['socle']='y';
			$tabmatieres[115]['socle']='y';

			break;
	}
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




function tab_extract_moy($tab_ele,$id_clas) {
	global $num_eleve, $classe, $tab_mat;




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
	if($tab_ele['no_gep']==""){
		echo "<b style='color:red;'>ERREUR:</b> Numéro INE non attribué: ".$tab_ele['nom']." ".$tab_ele['prenom']."<br />";
		$temoin_notanet_eleve="ERREUR";
		$info_erreur="Pas de numéro INE";
		echo "INE: <input type='text' name='INE[$num_eleve]' value='' />\n";
	}
	else{
		echo "<b>".$tab_ele['nom']." ".$tab_ele['prenom']."</b> ".$tab_ele['no_gep']."<br />\n";
		$INE=$tab_ele['no_gep'];
		echo "INE: <input type='text' name='INE[$num_eleve]' value='$INE' />\n";
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
	//echo "$sql<br />";
	$resultat_periodes=mysql_query($sql);
	echo "<table class='boireaus'>\n";
	echo "<tr style='font-weight: bold; text-align:center;'>\n";
	echo "<th>Id</th>\n";
	echo "<th>Matière</th>\n";
	echo "<th>Moyenne</th>\n";
	while($ligne_periodes=mysql_fetch_object($resultat_periodes)){
		echo "<th>T $ligne_periodes->num_periode</th>\n";
	}
	echo "<th>Moyenne</th>\n";
	echo "<th>Correction</th>\n";
	if($affiche_enregistrements_precedents=="y") {
		echo "<th>\n";
		echo "Enregistré<br />auparavant";
		echo "</th>\n";
	}
	echo "</tr>\n";

	$alt=1;
	for($j=101;$j<=122;$j++){


		// Initialisation de la moyenne pour la matière NOTANET courante.
		$moy_NOTANET[$j]="";


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
					echo "<tr class='lig$alt'>\n";
					//echo $id_matiere[$j][$k]."<br />\n";
					// A FAIRE: REQUETE moyenne pour la matière... si non vide... (test si note!="-" aussi?)

					//$sql="SELECT round(avg(n.note),1) as moyenne FROM matieres_notes n, j_eleves_classes c WHERE (n.periode='$num_periode' AND n.matiere='$matiere[$j]' AND c.id_classe='$id_classe' AND c.login = n.login AND n.statut =''  AND c.periode='$num_periode')";

					echo "<td><span style='color:green;'>$j</span></td>\n";
					echo "<td>".$id_matiere[$j][$k]."</td>\n";

					$temoin_moyenne="";
					//======================================================================
					//$sql="SELECT round(avg(note),1) as moyenne FROM matieres_notes WHERE (matiere='".$id_matiere[$j][$k]."' AND login='$ligne->login' AND statut ='')";
					$sql="SELECT round(avg(mn.note),1) as moyenne FROM matieres_notes mn, j_groupes_matieres jgm WHERE (jgm.id_matiere='".$id_matiere[$j][$k]."' AND mn.login='".$tab_ele['login']."' AND mn.statut ='' AND mn.id_groupe=jgm.id_groupe)";
					//echo "$sql<br />\n";
					$resultat_moy=mysql_query($sql);
					if(mysql_num_rows($resultat_moy)>0){
						$ligne_moy=mysql_fetch_object($resultat_moy);
						//echo "$ligne_moy->moyenne<br />";
						echo "<td style='font-weight:bold; text-align:center;'>$ligne_moy->moyenne</td>\n";
						//$cpt++;
						if($ligne_moy->moyenne!=""){
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

					$total=0;
					$nbnotes=0;
					//$sql="SELECT DISTINCT num_periode FROM periodes WHERE id_classe='$id_classe[$i]' ORDER BY num_periode";
					$sql="SELECT DISTINCT num_periode FROM periodes WHERE id_classe='$id_clas' ORDER BY num_periode";
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
								//$infos="Erreur? Il y a plusieurs notes/moyennes pour une même période! ";
								$infos="<p>Erreur? Il y a plusieurs notes/moyennes pour une même période! <br />";

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
							}
							else{
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
								echo "<td$chaine_couleur style='text-align:center;'>".$ligne_notes->note." - ".$ligne_notes->statut."</td>\n";
							}
							else{
								echo "<td$chaine_couleur style='text-align:center;'>X</td>\n";
							}
						}
					}
					if($nbnotes>0){
						$cpt++;
						$liste_matieres_gepi.=" ".$id_matiere[$j][$k];
						$moyenne=round($total/$nbnotes,1);
						//echo "<td style='font-weight:bold; text-align:center;'>$total/$nbnotes = $moyenne</td>\n";
						echo "<td style='font-weight:bold; text-align:center;'>$moyenne</td>\n";
						//echo "<td><input type='text' name='' value='$moyenne'></td>\n";

						/*
						//if($tabmatieres[$j][-1]=="POINTS"){
						//if(($tabmatieres[$j][-1]=="POINTS")||($tabmatieres[$j][-1]=="NOTNONCA")){
						if($tabmatieres[$j][-1]=="POINTS"){
							$ligne_NOTANET=$ligne_NOTANET."|$moyenne|";
							$TOT=$TOT+$moyenne;
						}
						else{
							if($tabmatieres[$j][-1]=="PTSUP"){
								$ptsup=$moyenne-10;
								if($ptsup>0){
									$ligne_NOTANET=$ligne_NOTANET."|$ptsup|";
									$TOT=$TOT+$ptsup;
								}
							}
							else{
								//$tabmatieres[$j][-1]="NOTNONCA";
								// On ne modifie pas... euh si... une ligne est insérée, mais elle n'intervient pas dans le calcul du TOTal.
								if($tabmatieres[$j][-1]=="NOTNONCA"){
									$ligne_NOTANET=$ligne_NOTANET."|$moyenne|";
								}
							}
						}
						*/

						//$moy_NOTANET[$j]="$moyenne";

						//echo "<td><input type='text' name='moy.$j.$k[$num_eleve]' value='$moyenne' size='6'></td>\n";
						//echo "<td><input type='text' name='moy_$j"."_"."$k[$num_eleve]' value='$moyenne' size='6'></td>\n";
						//echo "<td><input type='text' name='moy_$j"."_".$k."[$num_eleve]' value='$moyenne' size='6'></td>\n";

						//$moyenne_arrondie=round($moyenne*2)/2;
						//La note globale attribuée aux élèves dans chaque discipline, à l'issue des deux classes, est calculée sur la base de la moyenne des deux notes attribuées en quatrième et en troisième. Chaque note globale est affectée du coefficient défini par l'arrêté du 18 août 1999. Les notes globales, arrondies au demi point supérieur, sont arrêtées par le conseil des professeurs du troisième trimestre.
						$moyenne_arrondie=ceil($moyenne*2)/2;
						//echo "<td><input type='text' name='moy_$j"."_".$k."[$num_eleve]' value='".$moyenne_arrondie."' size='6' /></td>\n";
						echo "<td><input type='text' name='moy_$j"."_".$k."[$num_eleve]' value='".$moyenne_arrondie."' size='6' />";
						//echo "<input type='hidden' name='matiere_".$j."_[$num_eleve]' value='".$id_matiere[$j][$k]."' size='6' />";
						echo "</td>\n";

						//$moy_NOTANET[$j]="$moyenne";
						$moy_NOTANET[$j]="$moyenne_arrondie";

					}
					else{

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
						//echo "<td><input type='text' name='moy.$j.$k[$num_eleve]' value='' size='6'></td>\n";
						//echo "<td><input type='text' name='moy_$j"."_"."$k[$num_eleve]' value='' size='6'></td>\n";
						echo "<td><input type='text' name='moy_$j"."_".$k."[$num_eleve]' value='' size='6' /></td>\n";
						//echo "<td></td>\n";
					}
					/*
					else{
						if($statut_matiere[$j]=='imposee'){
							$temoin_notanet_eleve="ERREUR";
							if($info_erreur==""){
								$info_erreur="Pas de moyenne à une matière non optionnelle.";
							}
							else{
								$info_erreur=$info_erreur." - Pas de moyenne à une matière non optionnelle.";
							}
						}
					}
					*/


					/*
					//if($temoin_notanet_eleve!="ERREUR"){
					if(($temoin_notanet_eleve!="ERREUR")&&($moyenne!="")){
						echo "<td>$ligne_NOTANET</td>\n";
					}
					*/


					if($affiche_enregistrements_precedents=="y") {
						echo "<td>\n";
						$sql="SELECT note FROM notanet WHERE login='".$tab_ele['login']."' AND matiere='".$id_matiere[$j][$k]."' AND notanet_mat='".$tabmatieres[$j][0]."' AND id_mat='$j';";
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
					echo "<td><input type='text' name='moy_$j"."_0[$num_eleve]' value='$note_b2i' size='6' /></td>\n";


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
					echo "<td><input type='text' name='moy_$j"."_0[$num_eleve]' value='$note_a2' size='6' /></td>\n";

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

			}
		}

		if($cpt>1){
			$temoin_notanet_eleve="ERREUR";
			// Un élève a des notes dans deux options d'un même choix NOTANET (par exemple AGL1 et ALL1)
			if($info_erreur==""){
				//$info_erreur="Plusieurs options d'une même matière.";
				$info_erreur="Plusieurs options d'une même matière: <b>$liste_matieres_gepi</b><br />(<span style='font-size:x-small'><i>il faudra vider le champ de formulaire correspondant à la matière à abandonner</i></span>)";
			}
			else{
				//$info_erreur=$info_erreur." - Plusieurs options d'une même matière.";
				$info_erreur=$info_erreur."Plusieurs options d'une même matière: <b>$liste_matieres_gepi</b><br />(<span style='font-size:x-small'><i>il faudra vider le champ de formulaire correspondant à la matière à abandonner</i></span>)";
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
		for($j=101;$j<=122;$j++){
			// Pour les matières NOTANET existantes:
			if($tabmatieres[$j][0]!=''){
				// Si une moyenne a été extraite
				// (c'est-à-dire si l'élève a la matière et que l'extraction a réussi (donc pas d'ERREUR))
				//echo "\$tabmatieres[$j][-1]=".$tabmatieres[$j][-1]."<br />\n";
				//echo "\$moy_NOTANET[$j]=".$moy_NOTANET[$j]."<br />\n";
				if($moy_NOTANET[$j]!=""){
					$ligne_NOTANET="$INE|$j";

					if($tabmatieres[$j]['socle']=="y"){
						$ligne_NOTANET=$ligne_NOTANET."|".$moy_NOTANET[$j]."|";
					}
					elseif($tabmatieres[$j][-1]=="POINTS"){
						//$ligne_NOTANET=$ligne_NOTANET."|$moy_NOTANET[$j]|";
						//$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($moy_NOTANET[$j])."|";
						// Pour les brevets dans lesquels certaines notes sont sur 40 ou 60 au lieu de 20:
						$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($moy_NOTANET[$j]*$tabmatieres[$j][-2])."|";
						//$TOT=$TOT+$moy_NOTANET[$j];
						$TOT=$TOT+round($moy_NOTANET[$j]*2)/2;
					}
					else{
						if($tabmatieres[$j][-1]=="PTSUP"){
							$ptsup=$moy_NOTANET[$j]-10;
							if($ptsup>0){
								//$ligne_NOTANET=$ligne_NOTANET."|$ptsup|";
								$ligne_NOTANET=$ligne_NOTANET."|".formate_note_notanet($ptsup)."|";
								//$TOT=$TOT+$ptsup;
								$TOT=$TOT+round($ptsup*2)/2;
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
				//$pdf->SetFont('Arial','',$hauteur_texte);
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
				//$pdf->SetFont('Arial','',$hauteur_texte);
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

/*
function cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align='C',$align='L',$increment=0.3,$r_interligne=0.3) {
	global $pdf;

	// $increment:     nombre dont on réduit la police à chaque essai
	// $r_interligne:  proportion de la taille de police pour les interlignes
	// $bordure:       LRBT
	// $v_align:       C(enter) ou T(op)

	$texte=trim($texte);
	$hauteur_texte=$hauteur_max_font;
	$pdf->SetFontSize($hauteur_texte);
	$taille_texte_total=$pdf->GetStringWidth($texte);

	// Ca nous donne le nombre max de lignes en hauteur avec la taille de police maxi
	// Il faudrait plutôt déterminer ce nombre d'après une taille minimale acceptable de police
	$nb_max_lig=max(1,floor($h_cell/((1+$r_interligne)*($hauteur_min_font*26/100))));
	// echo "\$nb_max_lig=$nb_max_lig<br />";

	//$ifmax=0;
	//$ifmax=1;
	$fmax=0;

	$tab_lig=array();
	for($j=1;$j<=$nb_max_lig;$j++) {
		$hauteur_texte=$hauteur_max_font;

		unset($ligne);
		$ligne=array();
	
		$tab=split(" ",$texte);
		$cpt=0;
		$i=0;
		while(true) {
			if(isset($ligne[$cpt])) {$ligne[$cpt].=" ";} else {$ligne[$cpt]="";}

			if(my_ereg("\n",$tab[$i])) {
				$tmp_tab=split("\n",$tab[$i]);

				for($k=0;$k<count($tmp_tab)-1;$k++) {
					if(!isset($ligne[$cpt])) {$ligne[$cpt]="";}
					$ligne[$cpt].=$tmp_tab[$k];
					$cpt++;
				}
				if(!isset($ligne[$cpt])) {$ligne[$cpt]="";}
				$ligne[$cpt].=$tmp_tab[$k];
			}
			else {
				if($pdf->GetStringWidth($ligne[$cpt].$tab[$i])>=$largeur_dispo) {
					$cpt++;
					$ligne[$cpt]=$tab[$i];
				}
				else {
					$ligne[$cpt].=$tab[$i];
				}
			}
			$i++;
			if(!isset($tab[$i])) {break;}
		}
	
		// Recherche de la plus longue ligne:
		$taille_texte_ligne=0;
		$num=0;
		for($i=0;$i<count($ligne);$i++) {
			// echo "\$ligne[$i]=$ligne[$i]<br />";
			$l=$pdf->GetStringWidth($ligne[$i]);
			if($taille_texte_ligne<$l) {$taille_texte_ligne=$l;$num=$i;}
		}

		// On calcule la hauteur en mm de la police (proportionnalité: 100pt -> 26mm)
		$hauteur_texte_mm=$hauteur_texte*26/100;
		// Hauteur totale: Nombre de lignes multiplié par la hauteur de police avec les marges verticales
		$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
	
		// echo "On calcule la taille de la police d'après \$ligne[$num]=".$ligne[$num]."<br/>";
		// On ajuste la taille de police avec la plus grande ligne pour que cela tienne en largeur
		// et on contrôle aussi que cela tient en hauteur, sinon on continue à réduire la police.
		$grandeur_texte='test';
		while($grandeur_texte!='ok') {
			//if($largeur_dispo<$taille_texte_ligne) {
			if(($largeur_dispo<$taille_texte_ligne)||($hauteur_totale>$h_cell)) {
				$hauteur_texte=$hauteur_texte-$increment;
				if($hauteur_texte<$hauteur_min_font) {break;}
				$hauteur_texte_mm=$hauteur_texte*26/100;
				$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
				//$pdf->SetFont('Arial','',$hauteur_texte);
				$pdf->SetFontSize($hauteur_texte);
				$taille_texte_ligne=$pdf->GetStringWidth($ligne[$num]);
				// echo "\$hauteur_texte=$hauteur_texte -&gt; \$taille_texte_ligne=".$taille_texte_ligne."<br/>";
			}
			else {
				$grandeur_texte='ok';
			}
		}

		if($grandeur_texte=='ok') {
			// Hauteur de la police en mm
			$hauteur_texte_mm=$hauteur_texte*26/100;
			$tab_lig[$j]['hauteur_texte_mm']=$hauteur_texte_mm;
			// Hauteur de la police en pt
			$tab_lig[$j]['taille_police']=$hauteur_texte;
			// Hauteur totale du texte
			$tab_lig[$j]['hauteur_totale']=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
			// Marge verticale en mm entre les lignes
			$marge_verticale=$hauteur_texte_mm*$r_interligne;
			$tab_lig[$j]['marge_verticale']=$marge_verticale;
			// Tableau des lignes
			$tab_lig[$j]['lignes']=$ligne;
	
			// On choisit la hauteur de police la plus grande possible pour laquelle les lignes tiennent en hauteur 
			// (la largeur a déjà été utilisée pour découper en lignes).
			if(($hauteur_texte>$fmax)&&($tab_lig[$j]['hauteur_totale']<=$h_cell)) {
				$ifmax=$j;
			}
		}
	}

	if((!isset($ifmax))||($tab_lig[$ifmax]['taille_police']<$hauteur_min_font)) {
		// On relance en remplaçant les retours forcés à la ligne (\n) par des espaces.

		$fmax=0;

		$tab_lig=array();
		for($j=1;$j<=$nb_max_lig;$j++) {
			$hauteur_texte=$hauteur_max_font;

			unset($ligne);
			$ligne=array();
		
			$tab=split(" ",trim(my_ereg_replace("\n"," ",$texte)));
			$cpt=0;
			$i=0;
			while(true) {
				if(isset($ligne[$cpt])) {$ligne[$cpt].=" ";} else {$ligne[$cpt]="";}

				if($pdf->GetStringWidth($ligne[$cpt].$tab[$i])>=$largeur_dispo) {
					$cpt++;
					$ligne[$cpt]=$tab[$i];
				}
				else {
					$ligne[$cpt].=$tab[$i];
				}
				$i++;
				if(!isset($tab[$i])) {break;}
			}
		
			// Recherche de la plus longue ligne:
			$taille_texte_ligne=0;
			$num=0;
			for($i=0;$i<count($ligne);$i++) {
				// echo "\$ligne[$i]=$ligne[$i]<br />";
				$l=$pdf->GetStringWidth($ligne[$i]);
				if($taille_texte_ligne<$l) {$taille_texte_ligne=$l;$num=$i;}
			}

			// On calcule la hauteur en mm de la police (proportionnalité: 100pt -> 26mm)
			$hauteur_texte_mm=$hauteur_texte*26/100;
			// Hauteur totale: Nombre de lignes multiplié par la hauteur de police avec les marges verticales
			$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
		
			// echo "On calcule la taille de la police d'après \$ligne[$num]=".$ligne[$num]."<br/>";
			// On ajuste la taille de police avec la plus grande ligne pour que cela tienne en largeur
			// et on contrôle aussi que cela tient en hauteur, sinon on continue à réduire la police.
			$grandeur_texte='test';
			while($grandeur_texte!='ok') {
				//if($largeur_dispo<$taille_texte_ligne) {
				if(($largeur_dispo<$taille_texte_ligne)||($hauteur_totale>$h_cell)) {
					$hauteur_texte=$hauteur_texte-$increment;
					if($hauteur_texte<$hauteur_min_font) {break;}
					$hauteur_texte_mm=$hauteur_texte*26/100;
					$hauteur_totale=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
					//$pdf->SetFont('Arial','',$hauteur_texte);
					$pdf->SetFontSize($hauteur_texte);
					$taille_texte_ligne=$pdf->GetStringWidth($ligne[$num]);
					// echo "\$hauteur_texte=$hauteur_texte -&gt; \$taille_texte_ligne=".$taille_texte_ligne."<br/>";
				}
				else {
					$grandeur_texte='ok';
				}
			}

			if($grandeur_texte=='ok') {
				// Hauteur de la police en mm
				$hauteur_texte_mm=$hauteur_texte*26/100;
				$tab_lig[$j]['hauteur_texte_mm']=$hauteur_texte_mm;
				// Hauteur de la police en pt
				$tab_lig[$j]['taille_police']=$hauteur_texte;
				// Hauteur totale du texte
				$tab_lig[$j]['hauteur_totale']=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
				// Marge verticale en mm entre les lignes
				$marge_verticale=$hauteur_texte_mm*$r_interligne;
				$tab_lig[$j]['marge_verticale']=$marge_verticale;
				// Tableau des lignes
				$tab_lig[$j]['lignes']=$ligne;
		
				// On choisit la hauteur de police la plus grande possible pour laquelle les lignes tiennent en hauteur 
				// (la largeur a déjà été utilisée pour découper en lignes).
				if(($hauteur_texte>$fmax)&&($tab_lig[$j]['hauteur_totale']<=$h_cell)) {
					$ifmax=$j;
				}
			}
		}


		// Si ça ne passe toujours pas, on prend $hauteur_min_font sans retours à la ligne et on tronque
		if(!isset($ifmax)) {
			
		//	$tab_lig=array();
		//	$j=1;
		//	$ifmax=$j;
		//	$hauteur_texte=$hauteur_min_font;
		//	$hauteur_texte_mm=$hauteur_texte*26/100;
		//	$tab_lig[$j]['hauteur_texte_mm']=$hauteur_texte_mm;
		//	// Hauteur de la police en pt
		//	$tab_lig[$j]['taille_police']=$hauteur_texte;
		//	// Hauteur totale du texte
		//	$tab_lig[$j]['hauteur_totale']=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
		//	// Marge verticale en mm entre les lignes
		//	$marge_verticale=$hauteur_texte_mm*$r_interligne;
		//	$tab_lig[$j]['marge_verticale']=$marge_verticale;
		//	// Tableau des lignes
		//	$tab_lig[$j]['lignes'][]="Texte trop long";
			

			$fmax=0;

			$tab_lig=array();
			$hauteur_texte=$hauteur_min_font;
			unset($ligne);
			$ligne=array();

			$tab=split(" ",trim(my_ereg_replace("\n"," ",$texte)));
			$cpt=0;
			$i=0;
			while(true) {
				if(isset($ligne[$cpt])) {$ligne[$cpt].=" ";} else {$ligne[$cpt]="";}

				if($pdf->GetStringWidth($ligne[$cpt].$tab[$i])>=$largeur_dispo) {

					if(($cpt+2)*$hauteur_texte*(1+$r_interligne)*26/100>$h_cell) {
						$d=1;
						while(($pdf->GetStringWidth(substr($ligne[$cpt],0,strlen($ligne[$cpt])-$d)."...")>=$largeur_dispo)&&($d<strlen($ligne[$cpt]))) {
							$d++;
						}
						$ligne[$cpt]=substr($ligne[$cpt],0,strlen($ligne[$cpt])-$d)."...";
						break;
					}

					$cpt++;
					$ligne[$cpt]=$tab[$i];
				}
				else {
					$ligne[$cpt].=$tab[$i];
				}
				$i++;
				if(!isset($tab[$i])) {break;} // On ne devrait pas quitter sur ça puisque le texte va être trop long
			}

			$j=1;
			$ifmax=$j;
			$hauteur_texte_mm=$hauteur_texte*26/100;
			$tab_lig[$j]['hauteur_texte_mm']=$hauteur_texte_mm;
			// Hauteur de la police en pt
			$tab_lig[$j]['taille_police']=$hauteur_texte;
			// Hauteur totale du texte
			$tab_lig[$j]['hauteur_totale']=($cpt+1)*$hauteur_texte_mm*(1+$r_interligne);
			// Marge verticale en mm entre les lignes
			$marge_verticale=$hauteur_texte_mm*$r_interligne;
			$tab_lig[$j]['marge_verticale']=$marge_verticale;
			// Tableau des lignes
			$tab_lig[$j]['lignes']=$ligne;

		}
	}

	// On trace le rectangle (vide) du cadre:
	$pdf->SetXY($x,$y);
	$pdf->Cell($largeur_dispo,$h_cell, '',$bordure,2,'');

	// On va écrire les lignes avec la taille de police optimale déterminée (cf. $ifmax)	
	//$marge_h=round(($h_cell-(count($ligne)*$hauteur_texte_mm+(count($ligne)-1)*$marge_verticale))/2);
	//$marge_h=round(($h_cell-$tab_lig[$ifmax]['hauteur_totale'])/2);
	$nb_lig=count($tab_lig[$ifmax]['lignes']);
	$h=count($tab_lig[$ifmax]['lignes'])*$tab_lig[$ifmax]['hauteur_texte_mm']*(1+$r_interligne);
	$t=$h_cell-$h;
	$bord_debug='';
	//$bord_debug='LRBT';
	for($i=0;$i<count($tab_lig[$ifmax]['lignes']);$i++) {
		
		//$pdf->SetXY(10,$y+$i*($hauteur_texte_mm+$marge_verticale)+$marge_h);
		$pdf->SetXY($x,$y+$i*($tab_lig[$ifmax]['hauteur_texte_mm']+$tab_lig[$ifmax]['marge_verticale']));

		//if($i==1) {$bord_debug='LRBT';} else {$bord_debug='';}
		//$pdf->Cell($largeur_dispo-4,$h_cell/count($tab_lig[$ifmax]['lignes']), $tab_lig[$ifmax]['lignes'][$i],$bord_debug,2,'');

		if($v_align=='T') {
			$pdf->Cell($largeur_dispo,$tab_lig[$ifmax]['hauteur_texte_mm']+2*$tab_lig[$ifmax]['marge_verticale'], $tab_lig[$ifmax]['lignes'][$i],$bord_debug,1,$align);
		}
		else {
			$pdf->Cell($largeur_dispo,$h_cell/count($tab_lig[$ifmax]['lignes']), $tab_lig[$ifmax]['lignes'][$i],$bord_debug,1,$align);
		}
	}
	//if($tab_lig[$ifmax]['taille_police']!=$hauteur_max_font) {$pdf->Cell(20,$h_cell, $tab_lig[$ifmax]['taille_police'],$bord_debug,2,'');}

}
*/
function fs_pt2mm($fs) {
	//(proportionnalité: 100pt -> 26mm)
	return $fs*26/100;
}

?>