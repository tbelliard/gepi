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

function tabmatieres($type_brevet){
	switch($type_brevet){
		case 0:
			// COLLEGE, option de série LV2
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='SCIENCES DE LA VIE ET DE LA TERRE';
			$tabmatieres[105][0]='PHYSIQUE-CHIMIE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='ARTS PLASTIQUES';
			$tabmatieres[108][0]='EDUCATION MUSICALE';
			$tabmatieres[109][0]='TECHNOLOGIE';
			$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE';
			//$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE OU DECOUVERTE PROFESSIONNELLE (module de 6 heures)';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='VIE SCOLAIRE';
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
			$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";

			$tabmatieres[110]["lig_speciale"]="DEUXIEME LANGUE VIVANTE OU DECOUVERTE PROFESSIONNELLE (module de 6 heures)";
			break;
		case 1:
			// COLLEGE, option de série DP6
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='SCIENCES DE LA VIE ET DE LA TERRE';
			$tabmatieres[105][0]='PHYSIQUE-CHIMIE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='ARTS PLASTIQUES';
			$tabmatieres[108][0]='EDUCATION MUSICALE';
			$tabmatieres[109][0]='TECHNOLOGIE';
			//$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE';
			//$tabmatieres[110][0]='DEUXIEME LANGUE VIVANTE OU DECOUVERTE PROFESSIONNELLE (module de 6 heures)';
			$tabmatieres[110][0]='DECOUVERTE PROFESSIONNELLE (module de 6 heures)';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='VIE SCOLAIRE';
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
			$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";


			$tabmatieres[110]["lig_speciale"]="DEUXIEME LANGUE VIVANTE OU DECOUVERTE PROFESSIONNELLE (module de 6 heures)";
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
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			//$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE OU SCIENCES PHYSIQUES';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			$tabmatieres[104][0]='';
			$tabmatieres[105][0]='VIE SOCIALE ET PROFESSIONNELLE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION ARTISTIQUE';
			$tabmatieres[108][0]='TECHNOLOGIE';
			$tabmatieres[109][0]='';
			$tabmatieres[110][0]='';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='VIE SCOLAIRE';
			$tabmatieres[113][0]='';
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE EDUCATION CIVIQUE';
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
			$tabmatieres["fb_intitule_col"][1]="Traditionnelle";
			$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";


			// Les deux matières en une seule ligne
			$tabmatieres[103]["lig_speciale"]='PREMIERE LANGUE VIVANTE OU SCIENCES PHYSIQUES';
			$tabmatieres[104]["lig_speciale"]='PREMIERE LANGUE VIVANTE OU SCIENCES PHYSIQUES';

			// Il faudrait ajouter une ligne spéciale pour la DP6 alors que ce n'est pas compté dans cette série
			$tabmatieres[111]["lig_speciale"]="DECOUVERTE PROFESSIONNELLE<br />(module de 6 heures)";
			break;
		case 3:
			// PROFESSIONNELLE, option de série DP6
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
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
			$tabmatieres[111][0]='DECOUVERTE PROFESSIONNELLE (module 6 heures)';
			$tabmatieres[112][0]='VIE SCOLAIRE';
			$tabmatieres[113][0]='';
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE EDUCATION CIVIQUE';
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
			$tabmatieres["fb_intitule_col"][1]="Traditionnelle";
			$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";

			// Les deux matières en une seule ligne
			$tabmatieres[103]["lig_speciale"]='PREMIERE LANGUE VIVANTE OU SCIENCES PHYSIQUES';
			$tabmatieres[104]["lig_speciale"]='PREMIERE LANGUE VIVANTE OU SCIENCES PHYSIQUES';

			// Pour mettre le saut de ligne au bon niveau:
			$tabmatieres[111]["lig_speciale"]="DECOUVERTE PROFESSIONNELLE<br />(module de 6 heures)";
			break;
		case 4:
			// PROFESSIONNELLE, option de série AGRICOLE
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
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
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE EDUCATION CIVIQUE';
			//$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE';
			$tabmatieres[122][0]='';
			//$tabmatieres[122][0]='EDUCATION CIVIQUE';

			for($j=101;$j<=122;$j++){
				$tabmatieres[$j][-1]='POINTS';
			}
			$tabmatieres[121][-1]='NOTNONCA';
			$tabmatieres[122][-1]='NOTNONCA';

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
			break;
		case 5:
			// TECHNOLOGIQUE, sans option de série
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
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
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE EDUCATION CIVIQUE';
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
			$tabmatieres["fb_intitule_col"][1]="Traditionnelle";
			$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";


			$tabmatieres["num_fb_col"]=1;
			$tabmatieres["fb_intitule_col"][1]="Traditionnelle";
			$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";

			// Il faudrait ajouter une ligne spéciale pour la DP6 alors que ce n'est pas compté dans cette série
			$tabmatieres[110]["lig_speciale"]="DECOUVERTE PROFESSIONNELLE<br />(module de 6 heures)";
			break;
		case 6:
			// TECHNOLOGIQUE, option de série DP6
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
			$tabmatieres[104][0]='SCIENCES PHYSIQUES';
			$tabmatieres[105][0]='ECONOMIE FAMILIALE ET SOCIALE';
			$tabmatieres[106][0]='EDUCATION PHYSIQUE ET SPORTIVE';
			$tabmatieres[107][0]='EDUCATION ARTISTIQUE';
			$tabmatieres[108][0]='TECHNOLOGIE';
			$tabmatieres[109][0]='';
			$tabmatieres[110][0]='DECOUVERTE PROFESSIONNELLE (module 6 heures)';
			$tabmatieres[111][0]='';
			$tabmatieres[112][0]='VIE SCOLAIRE';
			$tabmatieres[113][0]='';
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE EDUCATION CIVIQUE';
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
			$num_fb_col=2;
			$fb_intitule_col[1]="Traditionnelle";
			$fb_intitule_col[2]="A module<br />découverte<br />professionnelle<br />6 heures";
			*/

			$tabmatieres["num_fb_col"]=2;
			$tabmatieres["fb_intitule_col"][1]="Traditionnelle";
			$tabmatieres["fb_intitule_col"][2]="A module<br />découverte<br />professionnelle<br />6 heures";
			break;
		case 7:
			// TECHNOLOGIQUE, option de série AGRICOLE
			$tabmatieres[101][0]='FRANCAIS';
			$tabmatieres[102][0]='MATHEMATIQUES';
			$tabmatieres[103][0]='PREMIERE LANGUE VIVANTE';
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
			$tabmatieres[114][0]='';
			$tabmatieres[115][0]='';
			$tabmatieres[116][0]='';
			$tabmatieres[117][0]='';
			$tabmatieres[118][0]='';
			$tabmatieres[119][0]='';
			$tabmatieres[120][0]='';
			$tabmatieres[121][0]='HISTOIRE-GEOGRAPHIE EDUCATION CIVIQUE';
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
			break;
	}
	return $tabmatieres;
}

?>