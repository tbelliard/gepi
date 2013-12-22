<?php

/*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireaus Régis Bouguin
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

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

$msg = "";

// $tbs_CSS = array();
// $tbs_librairies = array();
// $tbs_message_enregistrement = '';
// $Style_CSS = array();
// $tbs_aff_temoin_check_serveur = "";
$tbs_last_connection = '';
$tbs_microtime  = '';
$tbs_pmv = '';

/*
 * mysql_query est obsolète depuis PHP 5.5.0, on utilise mysqli à la place
 */

function connectMysqli ($dbHost, $dbUser, $dbPass, $dbDb) {
    $connection = new mysqli($dbHost, $dbUser, $dbPass, $dbDb);
/* Modification du jeu de résultats en utf8 */
    if (!$connection->set_charset("utf8")) {
        printf("Erreur lors du chargement du jeu de caractères utf8 : %s\n", $connection->error);
		die();
    }
    return $connection;
}

// ***** Ouverture d'une liaison à la base en utilisant mysqli *****
// $mysqli = connectMysqli ($dbHost, $dbUser, $dbPass, $dbDb);

// ***** En attendant la gestion des droits par les mises à jours *****

// On vérifie si les droits existent et au besoin, on les crée

$result = $mysqli->query("SELECT * FROM  `droits` WHERE id = '/cahier_notes/visu_cc_elv.php' ");
if (!$result->num_rows) {
    $result->close();
    $result = $mysqli->query("INSERT INTO `gepi`.`droits` (`id`, `administrateur`, `professeur`, `cpe`, `scolarite`, `eleve`, `responsable`, `secours`, `autre`, `description`, `statut`) VALUES ('/cahier_notes/visu_cc_elv.php', 'F', 'V', 'F', 'F', 'V', 'V', 'F', 'F', 'Carnet de notes - visualisation par les élèves', '');");
}

// ***** Fin en attendant la gestion des droits par les mises à jours *****
// 
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

// recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

// On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
	die("Le module carnets de notes n'est pas activé.");
}
 /*
  * Autorisation de voir les évaluations cumules
  */
if (getSettingValue("GepiAccesEvalCumulEleve")!='yes') {
	die("Les évaluations cumules ne sont pas visibles");
}

@setlocale(LC_NUMERIC,'C');

require('cc_lib.php');

$javascript_specifique[] = "lib/tablekit";


/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/
// debug_var();

$titre_page = $nom_cc;


/****************************************************************
			On récupère les évaluations
****************************************************************/

/*
 * - Si on a un parents, on récupère l'enfant qu'il suit
 * - On récupère les évaluations des groupes dont il fait parti → `cc_dev`
 * - On récupère chaque évaluation → `cc_eval`
 * - On récupère les notes → `cc_notes_eval` 
*/

 /*
  * TODO : Autorisation spécifique de voir l'évaluation
  */
 
 if ('eleve' == $utilisateur->getStatut()) {
     $eleve = clone $utilisateur;
 } elseif ('responsable' == $utilisateur->getStatut()) {
     $enfants = $utilisateur->getEleves();

     $login = isset($_POST['choixEleve']) ? $_POST['choixEleve'] : (isset($_SESSION['enfant']) ? $_SESSION['enfant'] : $enfants[0]->getLogin());
     $_SESSION['enfant'] = $login;
     $eleve = ElevePeer::retrieveByLOGIN($login);
     
 } else {
     die("Vous n'avez pas droit à cette page");
}

 /***** - On récupère les évaluations des groupes dont l'élève fait parti → `cc_dev` *****/

$tableauNotesCumules=array();
$NotesCumulesSaisies=array();
$Notes=array();
$now=new DateTime('NOW');

$query = "SELECT DISTINCT `cc_dev`.* , `jgm`.id_matiere FROM `cc_dev`
        INNER JOIN `j_eleves_groupes` jeg
            ON (cc_dev.id_groupe = jeg.id_groupe)
        INNER JOIN `j_groupes_matieres` jgm
            ON (jgm.id_groupe = jeg.id_groupe)
        WHERE jeg.login = '".$eleve->getLogin()."'
            AND `cc_dev`.vision_famille = 'yes'
        ORDER BY cc_dev.id_cn_dev ASC, cc_dev.id_groupe ASC
    ";
// echo $query;

if ($result = $mysqli->query($query)) {

    while ($obj = $result->fetch_object()) {
        
 /***** - On récupère chaque évaluation → `cc_eval` *****/
        $queryEval = "SELECT cce.* FROM `cc_eval` cce
                    WHERE cce.id_dev = '".$obj->id."'
                    ";
        if ($resultEval = $mysqli->query($queryEval)) {
            while ($objEval = $resultEval->fetch_object()) {

/***** - On récupère les notes → `cc_notes_eval` *****/
                $queryNotes = "SELECT cne.* FROM `cc_notes_eval` cne
                    WHERE cne.id_eval = $objEval->id
                        AND cne.login = '".$eleve->getLogin()."'
                    ";
                if ($resultNotes = $mysqli->query($queryNotes)) {
                    while ($objNotes = $resultNotes->fetch_object()) {
                        $d1=new DateTime($objEval->vision_famille."00:00:00");
                        if ($d1 <= $now) {
                            $Notes[] = array("nom_court"=>stripslashes($objEval->nom_court), "nom_complet"=>stripslashes($objEval->nom_complet), "description"=>stripslashes($objEval->description), "date"=>$objEval->date , "vision_famille"=>$objEval->vision_famille , "note_sur"=>$objEval->note_sur , "note"=>$objNotes->note, "statut"=>$objNotes->statut, "comment"=>stripslashes($objNotes->comment));
                        }
                    }
                }
            }
        }
        $cumulNote = $noteSur = 0;
        if (isset($Notes)) {
            foreach ($Notes as $eval) {
                if (!$eval["statut"]) {
                    $cumulNote += $eval["note"];
                    $noteSur += $eval["note_sur"];
                }
            }

            if ($obj->id_cn_dev) {
                $NotesCumulesSaisies[] = array("id"=>$obj->id , "id_cn_dev"=>$obj->id_cn_dev , "id_matiere"=>$obj->id_matiere, "id_groupe"=>$obj->id_groupe , "nom_court"=>stripslashes($obj->nom_court) , "nom_complet"=>stripslashes($obj->nom_complet) , "description"=>stripslashes($obj->description) , "arrondir"=>$obj->arrondir, "notes"=>$Notes, "total"=>$cumulNote, "note_sur"=>$noteSur);
            } else {
                $tableauNotesCumules[] = array("id"=>$obj->id , "id_cn_dev"=>$obj->id_cn_dev , "id_matiere"=>$obj->id_matiere, "id_groupe"=>$obj->id_groupe , "nom_court"=>stripslashes($obj->nom_court) , "nom_complet"=>stripslashes($obj->nom_complet) , "description"=>stripslashes($obj->description) , "arrondir"=>$obj->arrondir, "notes"=>$Notes, "total"=>$cumulNote, "note_sur"=>$noteSur);
            }

            unset($Notes);
        }
        $resultEval->close();
    }

    /* free result set */
    $result->close();
}

/****************************************************************
			Fin de la récupération des évaluations
****************************************************************/
//$mysqli->close();
/***************************************************************/

// tamporisation de sortie
ob_start(); 
?>
<?php if ('responsable' == $utilisateur->getStatut() && (count($enfants)>1)) { ?>
<form action='visu_cc_elv.php' method='post'>
	<?php echo add_token_field();?>
    <p>
        <label for="choixEleve">Élève choisi</label>
        <select id="choixEleve" 
                name="choixEleve"
                onchange="submit();"
                >
            <?php foreach ($enfants as $enfant) { ?>
            <option value="<?php echo $enfant->getLogin();?>"
                    <?php if($enfant->getLogin() == $_SESSION['enfant']) echo "selected = 'selected'"; ?>
                    >
                <?php echo $enfant->getNom();?> <?php echo $enfant->getPrenom() ;?>
            </option>
            <?php } ?>
        </select>
        <input type="submit" id="valide" name="valide" value="choisir" />
    </p>
    <script>
        $('valide').addClassName('invisible');
    </script>
    
</form>
<?php } ?>


<?php if (!count($NotesCumulesSaisies) && !count($tableauNotesCumules)) {?>
<p class="rouge center" style="font-weight:bold;">
    Aucune <?php echo $nom_cc ;?> disponible
</p>
<?php } ?>


<?php if (count($tableauNotesCumules)) { ?>
<h2 class="center">Évaluations non intégrées au carnet de notes</h2>

<?php
/*
echo '<pre>';
print_r($tableauNotesCumules);
echo '</pre>';
*/
$i=0;
?>
<?php foreach ($tableauNotesCumules as $tableauNotes) {?>
<?php if(!($i%3)) { ?>
<div class="ligne3">
<?php } ?>
<div class="col3 col3_<?php echo $i%3?> " 
     style="vertical-align:top;">
    
<table class="boireaus">
    <caption title="<?php echo $tableauNotes['nom_complet'] ;?>" style="cursor:pointer;">
        <?php echo $tableauNotes['id_matiere'] ;?><br />
            <?php echo $tableauNotes['nom_court'] ;?>
    </caption>
    <tr>
        <th title="Nom de l'évaluation"
            style="cursor:pointer;">
            Nom
        </th>
        <th title="Date de l'évaluation"
            style="cursor:pointer;">
            Date
        </th>
        <th title="Note, disp → dispensé(e), abs → absent(e), - → Non noté(e)"
            style="cursor:pointer;">
            Note
        </th>
        <th title="Notes maximale de l'évaluation"
            style="cursor:pointer;">
            Sur
        </th>
    </tr>
    
<?php $ligne = -1 ;?> 
<?php foreach ($tableauNotes['notes'] as $notes) { ?>
    <tr class='lig<?php echo $ligne ;?>'>
        <td title="<?php echo $notes['nom_complet'] ;?> → <?php echo $notes['description'] ;?>"
            style="cursor:pointer;">
            <?php echo $notes['nom_court'] ;?> 
        </td>
        <td>
            <?php echo formate_date($notes['date']) ;?>
        </td>
        <td>
            <?php
            if (!$notes['statut']) {
                echo $notes['note'];
            } else {
                echo $notes['statut'];
            }
            ?>              
        </td>
        <td>
            <?php echo $notes['note_sur'] ;?>            
        </td>
    </tr>
<?php $ligne *= -1 ;?> 
<?php } ?>
    
    <tr>
        <th>Total</th>
        <th>-</th>
        <th><?php if ($tableauNotes['note_sur']) echo $tableauNotes['total'] ;?> </th>
        <th><?php if ($tableauNotes['note_sur']) echo $tableauNotes['note_sur'] ;?> </th>
    </tr>
    <tr>
        <th>Moyenne</th>
        <th>-</th>
        <th><?php if ($tableauNotes['note_sur']) {echo precision_arrondi($tableauNotes['total']/$tableauNotes['note_sur']*20,$tableauNotes['arrondir']) ; }?> </th>
        <th><?php if ($tableauNotes['note_sur']) {echo '20' ; }?> </th>
    </tr>
    
</table>
</div>
<?php if(2==($i%3)) { ?>
</div>
<?php } ?>
<?php $i++; ?>
<?php } ?>
<?php if(($i%3)) { ?>
</div>
<?php } ?>
<?php } ?>


<?php $i=0; ?>

<?php if (count($NotesCumulesSaisies)) {?>
<h2 class="center">Évaluations intégrées dans le carnet de notes</h2>

<?php $i=0; ?>

<?php foreach ($NotesCumulesSaisies as $tableauNotes) {?>
<?php if(!($i%3)) { ?>
<div class="ligne3">
<?php } ?>
<div class="col3 col3_<?php echo $i%3?> " 
     style="vertical-align:top;">
<table class="boireaus">
    <caption title="<?php echo $tableauNotes['nom_complet'] ;?>" style="cursor:pointer;">
        <?php echo $tableauNotes['id_matiere'] ;?><br />
            <?php echo $tableauNotes['nom_court'] ;?>
    </caption>
     <tr>
        <th title="Nom de l'évaluation"
            style="cursor:pointer;">
            Nom
        </th>
        <th title="Date de l'évaluation"
            style="cursor:pointer;">
            Date
        </th>
        <th title="Note, disp → dispensé(e), abs → absent(e), - → Non noté(e)"
            style="cursor:pointer;">
            Note
        </th>
        <th title="Note maximale de l'évaluation"
            style="cursor:pointer;">
            Sur
        </th>
    </tr>
    
<?php $ligne = -1 ;?> 
<?php foreach ($tableauNotes['notes'] as $notes) {?>
    <tr class='lig<?php echo $ligne ;?>'>
        <td title="<?php echo $notes['nom_complet'] ;?> → <?php echo $notes['description'] ;?>"
            style="cursor:pointer;">
            <?php echo $notes['nom_court'] ;?> 
        </td>
        <td>
            <?php echo formate_date($notes['date']) ;?>
        </td>
        <td>
            <?php
            if (!$notes['statut']) {
                echo $notes['note'];
            } else {
                echo $notes['statut'];
            }
            ?>            
        </td>
        <td>
            <?php echo $notes['note_sur'] ;?>            
        </td>
    </tr>
<?php $ligne *= -1 ;?> 
<?php } ?>
    <tr>
        <th>Total</th>
        <th>-</th>
        <th><?php if ($tableauNotes['note_sur']) echo $tableauNotes['total'] ;?> </th>
        <th><?php if ($tableauNotes['note_sur']) echo $tableauNotes['note_sur'] ;?> </th>
    </tr>
    <tr>
        <th>Moyenne</th>
        <th>-</th>
        <th><?php if ($tableauNotes['note_sur']) {echo precision_arrondi($tableauNotes['total']/$tableauNotes['note_sur']*20,$tableauNotes['arrondir']) ; }?> </th>
        <th><?php if ($tableauNotes['note_sur']) {echo '20' ; }?> </th>
    </tr>
    
</table>
</div>
<?php if(2==($i%3)) { ?>
</div>
<?php } ?>
<?php $i++; ?>
<?php } ?>
<?php } ?>

<?php if(($i%3)) { ?>
</div>
<?php } ?>






<?php $i=0 ?>


<?php
  // Fin de la tamporisation de sortie
  $contenu = ob_get_clean();
?>

<?php

include_once("../lib/header_template.inc.php");
$tbs_statut_utilisateur = $_SESSION['statut'];

if (!suivi_ariane($_SERVER['PHP_SELF'],$nom_cc))
		echo "erreur lors de la création du fil d'ariane";?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo "$titre_page : $tbs_gepiSchoolName" ?></title>	
<!-- ================= Affichage du favicon =================== -->
	<link rel="SHORTCUT ICON" href="<?php echo $tbs_gepiPath?>/favicon.ico" />

<!-- Début des styles -->

	<?php
		if (count($tbs_CSS)) {
			foreach ($tbs_CSS as $value) {
				if ($value!="") {?>
	<link rel="<?php echo $value['rel']; ?>" 
          type="<?php echo $value['type']; ?>" 
          href="<?php echo $value['fichier']; ?>" 
          media="<?php if($value['media']) {echo $value['media'];} else {echo 'screen';} ?>" /> 
	<link rel="<?php echo $value['rel']; ?>" 
          type="<?php echo $value['type']; ?>" 
          href="<?php echo $value['fichier']; ?>" 
          media="<?php if($value['media']) {echo $value['media'];} else {echo 'screen';} ?>" />
			<?php
                }
            }
			unset($value);
		}
		
		if (isset($CSS_smartphone)) {
	?>
			<link rel="stylesheet" 
                  type="text/css" 
                  href="<?php echo $gepiPath.'/'.$CSS_smartphone; ?>.css" 
                  media="screen and (max-width: 800px)" />
	<?php
		}
		
	?>
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/bandeau.css" media="screen" />

<!-- corrections internet Exploreur -->
	<!--[if lte IE 7]> media='screen' />
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/bandeau_ie.css' media='screen' />
	<![endif]-->

<!-- Style_screen_ajout.css -->
	<?php
		if (count($Style_CSS)) {
			foreach ($Style_CSS as $value) {
				if ($value!="") {
	?>             
	<link rel="<?php echo $value['rel']; ?>" 
          type="<?php echo $value['type']; ?>" 
          href="<?php echo $value['fichier']; ?>" 
          media="<?php if($value['media']) {echo $value['media'];} else {echo 'screen';} ?>" /> 
	<?php
				}
			}
			unset($value);
		}
	?>
	
<!-- Fin des styles -->

<!-- Début des fichiers en javascript -->
	<!-- christian -->
	<script type="text/javascript">
		//<![CDATA[ 
		function ouvre_popup(url) {
				eval("window.open('/mod_miseajour/utilisateur/fenetre.php','fen','width=600,height=500,menubar=no,scrollbars=yes')");
				fen.focus();
			}
		//]]>
	</script>

	<script type="text/javascript" src="<?php echo $tbs_gepiPath ?>/lib/functions.js"></script>
	<?php
		if (count($tbs_librairies)) {
			foreach ($tbs_librairies as $value) {
				if ($value!="") {
					echo "<script type=\"text/javascript\" src=\"$value\"></script>\n";
				}
			}
			unset($value);
		}
	?>

	<!-- Variable passée à 'ok' en fin de page via le /lib/footer.inc.php -->
	<script type='text/javascript'>
		//<![CDATA[ 
			temporisation_chargement='n';
		//]]>
	</script>	
<!-- fin des fichiers en javascript -->

<?php
	if ($tbs_message_enregistrement!="") {
		echo "
			<script type='text/javascript'>
				//<![CDATA[ 
					alert(\"$tbs_message_enregistrement\");
				//]]>
			</script>
		";
	}

	//maintien_de_la_session();
?>
	<script type="text/javascript" src="<?php echo $tbs_gepiPath?>/lib/cookieClass.js"></script>
	<script type="text/javascript">
		//<![CDATA[ 
		function changement() {
			change = 'yes';
		}
		//]]>
	</script>

	<!-- Gestion de l'expiration des session - Patrick Duthilleul -->
	<script type="text/javascript">
		//<![CDATA[
		
			debut_alert = new Date()
			warn_msg1_already_displayed = false;
			warn_msg2_already_displayed = false;
			gepi_start_session = new Cookies();
			if (gepi_start_session.get('GEPI_start_session')) {
				gepi_start_session.clear('GEPI_start_session');
			}
			gepi_start_session.set('GEPI_start_session', debut_alert.getTime())
			/* =================================================
			 =
			 =
			 =
			 =================================================== */
			function display_alert(message) {
				if ($('alert_message')) {
					$('alert_message').update(message);

					if (Prototype.Browser.IE) {
						//document.documentElement.scroll = "no";
						//document.documentElement.style.overflow = 'hidden';
					}
					else {
						//document.body.scroll = "no";
						//document.body.style.overflow = 'hidden';				
					}					
					var viewport = document.viewport.getDimensions(); // Gets the viewport as an object literal
					var width = viewport.width; // Usable window width
					var height = viewport.height; // Usable window height
					if( typeof( window.pageYOffset ) == 'number' ) 
						{y = window.pageYOffset;}
					else if (typeof(document.documentElement.scrollTop) == 'number') {
						y=document.documentElement.scrollTop;
					}
					//$('alert_cache').setStyle({width: "100%"});
					//$('alert_cache').setStyle({height: height+"px"});
					//$('alert_cache').setStyle({top: y+"px"});
					//$('alert_cache').setStyle({display: 'block'});
					//$('alert_cache').setOpacity(0.5);
					play_footer_sound();
					$('alert_entete').setStyle({top: y+2+"px"});
					$('alert_entete').setStyle({left: Math.abs((width-640)/2)+"px"});
					$('alert_entete').setOpacity(1);
					$('alert_entete').setStyle({display: 'block'});
					$('alert_popup').setStyle({top: y+50+"px"});
					$('alert_popup').setStyle({left: Math.abs((width-640)/2)+"px"});
					$('alert_popup').setOpacity(1);
					$('alert_popup').setStyle({display: 'block'});
					$('alert_bouton_ok').observe('click', function(event) {
						$('alert_popup').setStyle({display: 'none'});	
						$('alert_cache').setStyle({display: 'none'});
						$('alert_entete').setStyle({display: 'none'});
						if (Prototype.Browser.IE) {
							//document.documentElement.scroll = "yes";
							//document.documentElement.style.overflow = 'scroll';
						}
						else {
							//document.body.scroll = "yes";
							//document.body.style.overflow = 'scroll';				
						}						
					
					});
					$('alert_popup').observe('mouseover', function(event) {
						//$('alert_entete').setOpacity(0.3);
						//$('alert_popup').setOpacity(0.3);						
					});
					$('alert_popup').observe('mouseout', function(event) {
						//$('alert_entete').setOpacity(1);
						//$('alert_popup').setOpacity(1);					
					});
					//$('alert_bouton_reload').observe('click', function(event) {
					//	location.reload(true); 				
					//
					//});	
				}
				else {
					alert(message);
				}
			
			}
			/* =================================================
			 =
			 =
			 =
			 =================================================== */			
			function show_message_deconnexion() {
				var seconds_before_alert = 180;
				var seconds_int_betweenn_2_msg = 30;

				<?php
					$sessionMaxLength=getSettingValue("sessionMaxLength");

					/*
					// Avec le dispositif maintien_de_la_session() de lib/share.inc.php pointant vers lib/echo.php, on devrait pouvoir ne tenir compte que de la variable Gepi: sessionMaxLength
					$session_gc_maxlifetime=ini_get("session.gc_maxlifetime");
					if($session_gc_maxlifetime!=FALSE) {
						$session_gc_maxlifetime_minutes=$session_gc_maxlifetime/60;

						if((getSettingValue("sessionMaxLength")!="")&&($session_gc_maxlifetime_minutes<getSettingValue("sessionMaxLength"))) {
							$sessionMaxLength=$session_gc_maxlifetime_minutes;
						}
					}
					*/
				?>

				if (gepi_start_session.get('GEPI_start_session')) {
					debut_alert.setTime(parseInt(gepi_start_session.get('GEPI_start_session'),10));
				}
				digital=new Date()
				seconds=(digital-debut_alert)/1000
				//if (1==1) {
				  if (seconds>=<?php echo $sessionMaxLength*60; ?>) {
				  	if (!warn_msg2_already_displayed) {
						var message = "vous avez été probablement déconnecté du serveur, votre travail ne pourra pas être enregistré dans gepi depuis cette page, merci de le sauvegarder dans un bloc note.";
						display_alert(message);				  
						warn_msg2_already_displayed = true;
					}
				  }
				else if (seconds><?php echo $sessionMaxLength*60; ?> - seconds_before_alert) {
					if (!warn_msg1_already_displayed) {
						var seconds_reste = Math.floor(<?php echo $sessionMaxLength*60; ?> - seconds);
						now=new Date()
						var hrs=now.getHours();
						var mins=now.getMinutes();
						var secs=now.getSeconds();

						var heure = hrs + " H " + mins + "' " + secs + "'' ";
						var message = "A "+ heure + ", il vous reste moins de 3 minutes avant d'être déconnecté ! \nPour éviter cela, rechargez cette page en ayant pris soin d'enregistrer votre travail !";
						display_alert(message);
						warn_msg1_already_displayed = true;
					}
				}

				setTimeout("show_message_deconnexion()",seconds_int_betweenn_2_msg*1000)
			}
		//]]>
	</script>
    
</head>

<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion();<?php if($tbs_charger_observeur) echo $tbs_charger_observeur;?>">


<!-- on inclut le bandeau -->
	<?php include('../templates/origine/bandeau_template.php');?>

<!-- fin bandeau_template.html      -->

<div id='container'>

<a id='haut_de_page'></a>

<?php echo $contenu;?>

</div>
<div id='note_bas_page' style="background: rgba(225, 225, 225, 1); padding: .3em 2em;">
    <?php echo $nom_cc ;?> : regroupement de plusieurs évaluations en une seule qui sera ensuite intégré dans le carnet de notes
</div>

<?php
			if ($tbs_microtime!="") {
				echo "
   <p class='microtime'>Page générée en ";
   			echo $tbs_microtime;
				echo " sec</p>
   			";
	}
?>

		<?php
			if ($tbs_pmv!="") {
				echo "
	<script type='text/javascript'>
		//<![CDATA[
   			";
				echo $tbs_pmv;
				echo "
		//]]>
	</script>
   			";
		}
?>

<!-- Alarme sonore -->
<?php
	echo joueAlarme();
?>
<!-- Fin alarme sonore -->

<div id="alert_cache" style="z-index:2000;
							display:none;
							position:absolute;
							top:0px;
							left:0px;
							background-color:#000000;
							width:200px;
							height:200px;"> &nbsp;</div>
<div id="alert_entete" style="z-index:2000;
								display:none;
								position:absolute;"><img   src="../images/alerte_entete.png" alt="alerte" /></div>
<div id="alert_popup" style="z-index:2000;
								text-align:justify;
								width:600px;
								height:130px;
								border:1px solid black;
								background-color:white;
								padding-top:10px;
								padding-left:20px;
								padding-right:20px;
								display:none;
								position:absolute;
								background-image:url('../images/degrade_noir.png');
								background-repeat:repeat-x;
								background-position: left bottom;">
	<div id="alert_message"></div>
	<div id="alert_button" style="margin:5px auto;width:90px;">
		<div id="alert_bouton_ok" style="float:left;"><img src="../images/bouton_continue.png" alt="ok" /></div>
	</div>
</div>


</body>
</html>

