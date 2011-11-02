<?php
/*
 * $Id: fiche_eleve.php 4882 2010-07-24 20:47:49Z regis $
 *
 * Copyright 2001, 2006 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

	include("../lib/functions.php");

// uid pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post===$_SESSION['uid_prime']
 if(empty($_SESSION['uid_prime'])) { $_SESSION['uid_prime']=''; }
 if(empty($_GET['uid_post']) and empty($_POST['uid_post'])) {$uid_post='';}
    else { if (isset($_GET['uid_post'])) {$uid_post=$_GET['uid_post'];} if (isset($_POST['uid_post'])) {$uid_post=$_POST['uid_post'];} }
	$uid = md5(uniqid(microtime(), 1));
	   // on remplace les %20 par des espaces
	    $uid_post = my_eregi_replace('%20',' ',$uid_post);
	if($uid_post===$_SESSION['uid_prime']) { $valide_form = 'yes'; } else { $valide_form = 'no'; }
	$_SESSION['uid_prime'] = $uid;

// fonction pour connaitre l'age de la personne par rapport à une date au format SQL AAAA-MM-JJ
function age($date_de_naissance_fr)
{
            //à partir de la date de naissance, retourne l'âge dans la variable $age

            // date de naissance (partie à modifier)
              $ddn = $date_de_naissance_fr;

            // enregistrement de la date du jour
              $DATEDUJOUR = date("Y-m-d");
              $DATEFRAN = date("d/m/Y");

            // calcul de mon age d'après la date de naissance $ddn
              $annais = substr("$ddn", 0, 4);
              $anjour = substr("$DATEFRAN", 6, 4);
              $moisnais = substr("$ddn", 4, 2);
              $moisjour = substr("$DATEFRAN", 3, 2);
              $journais = substr("$ddn", 6, 2);
              $jourjour = substr("$DATEFRAN", 0, 2);

              $age = $anjour-$annais;
              if ($moisjour<$moisnais){$age=$age-1;}
              if ($jourjour<$journais && $moisjour==$moisnais){$age=$age-1;}
              return($age);
}

// fonction pour connaitre le professeur principal de l'id d'une classe
function pp($classe_choix)
{
            global $prefix_base;
               $call_prof_classe = mysql_query("SELECT * FROM ".$prefix_base."classes, ".$prefix_base."j_eleves_professeurs, ".$prefix_base."j_eleves_classes WHERE ".$prefix_base."j_eleves_professeurs.login = ".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe = ".$prefix_base."classes.id AND ".$prefix_base."classes.classe = '".$classe_choix."'");
               $data_prof_classe = mysql_fetch_array($call_prof_classe);
               $suivi_par = $data_prof_classe['suivi_par'];
               return($suivi_par);
}

// variable non définie
	if(empty($_GET['select_fiche_eleve']) and empty($_POST['select_fiche_eleve'])) {$select_fiche_eleve="";}
	 else { if (isset($_GET['select_fiche_eleve'])) {$select_fiche_eleve=$_GET['select_fiche_eleve'];} if (isset($_POST['select_fiche_eleve'])) {$select_fiche_eleve=$_POST['select_fiche_eleve'];} }
	if(empty($_GET['action']) and empty($_POST['action'])) {$action="";}
	 else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
	if(empty($_GET['action_sql']) AND empty($_POST['action_sql'])) {$action_sql="";}
	 else { if (isset($_GET['action_sql'])) {$action_sql=$_GET['action_sql'];} if (isset($_POST['action_sql'])) {$action_sql=$_POST['action_sql'];} }
	if (empty($_GET['debut_selection_suivi']) AND empty($_POST['debut_selection_suivi'])) {$debut_selection_suivi='0';}
	 else { if (isset($_GET['debut_selection_suivi'])) {$debut_selection_suivi=$_GET['debut_selection_suivi'];} if (isset($_POST['debut_selection_suivi'])) {$debut_selection_suivi=$_POST['debut_selection_suivi'];} }


//ajout des fiche_suivi des eleve
if(($action_sql === "ajouter" or $action_sql === "modifier") and $valide_form === 'yes')
{
     // Vérifcation des variable
        $date_fiche = date('Y-m-d');
        $heure_fiche = date('H:i:s');
        $data_info_suivi = nl2br(htmlspecialchars(traitement_magic_quotes($_POST['data_info_suivi'])));
        $eleve_suivi_eleve_cpe = $_POST['eleve_suivi_eleve_cpe'];
	$niveau_urgent = $_POST['niveau_urgent'];
	$action_suivi = $_POST['action_suivi'];

        if ($action_sql === 'modifier') { $id_suivi_eleve_cpe = $_POST['id_suivi_eleve_cpe']; }

            // Vérification des champs nom et prenom (si il ne sont pas vides ?)
            if($data_info_suivi != '')
            {
                 $test = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."suivi_eleve_cpe WHERE eleve_suivi_eleve_cpe = '".$eleve_suivi_eleve_cpe."' AND date_suivi_eleve_cpe = '".$date_fiche."' AND komenti_suivi_eleve_cpe = '".$data_info_suivi."'"),0);
                 if ($test === '0')
                  {
                     if($action_sql === 'ajouter')
                      {
                            // Requete d'insertion MYSQL
                             $requete = "INSERT INTO ".$prefix_base."suivi_eleve_cpe (eleve_suivi_eleve_cpe,parqui_suivi_eleve_cpe,date_suivi_eleve_cpe,heure_suivi_eleve_cpe,komenti_suivi_eleve_cpe,niveau_message_suivi_eleve_cpe,action_suivi_eleve_cpe) VALUES ('$eleve_suivi_eleve_cpe','".$_SESSION['login']."','$date_fiche','$heure_fiche','$data_info_suivi','$niveau_urgent','$action_suivi')";
                      }
                     if($action_sql === 'modifier')
                      {
                            // Requete de mise à jour MYSQL
                              $requete = "UPDATE ".$prefix_base."suivi_eleve_cpe SET parqui_suivi_eleve_cpe='".$_SESSION['login']."', komenti_suivi_eleve_cpe = '$data_info_suivi', niveau_message_suivi_eleve_cpe = '$niveau_urgent', action_suivi_eleve_cpe = '$action_suivi' WHERE id_suivi_eleve_cpe = '".$id_suivi_eleve_cpe."'";
                      }
                            // Execution de cette requete dans la base cartouche
                             mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
                             $verification = '1';
                    } else {
                               // vérification = 2 - C'est initiale pour les motif existe déjas
                                 $verification = '2';
                                 $erreur = '1';
                            }
            } else {
                     // vérification = 3 - Tous les champs ne sont pas remplie
                     $verification = '3';
                     $erreur = '1';
                   }

}

if ($action_sql === 'supprimer' and $valide_form === 'yes')
 {
         $id_suivi_eleve_cpe = $_GET['id_suivi_eleve_cpe'];
         //Requete de suppresion MYSQL
            $requete = "DELETE FROM ".$prefix_base."suivi_eleve_cpe WHERE id_suivi_eleve_cpe ='$id_suivi_eleve_cpe'";
         // Execution de cette requete
            mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
 }

if ($action === 'modifier')
 {
      $id_suivi_eleve_cpe = $_GET['id_suivi_eleve_cpe'];
      $requete_modif_fiche = 'SELECT * FROM '.$prefix_base.'suivi_eleve_cpe WHERE id_suivi_eleve_cpe="'.$id_suivi_eleve_cpe.'"';
      $resultat_modif_fiche = mysql_query($requete_modif_fiche) or die('Erreur SQL !'.$requete_modif_fiche.'<br />'.mysql_error());
      $data_modif_fiche = mysql_fetch_array($resultat_modif_fiche);
 }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
  <title>Fiche élève</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <meta http-equiv="Pragma" CONTENT="no-cache" />
  <meta http-equiv="Cache-Control" CONTENT="no-cache" />
  <meta http-equiv="Expires" CONTENT="0" />
<?php /*  <link rel="stylesheet" type="text/css" href="../styles/mod_absences.css" /> */ ?>
<link rel="stylesheet" type="text/css" href="../styles/mod_absences.css" />
  <link rel="stylesheet" type="text/css" href="../../style.css" />
  <script type="text/javascript">
	function fermeFenetre() {
	  window.open('','_parent','');
	  window.close();
	}
	function centrerpopup(page,largeur,hauteur,options)
	{
	// les options :
	//    * left=100 : Position de la fen?tre par rapport au bord gauche de l'?cran.
	//    * top=50 : Position de la fen?tre par rapport au haut de l'?cran.
	//    * resizable=x : Indique si la fen?tre est redimensionnable.
	//    * scrollbars=x : Indique si les barres de navigations sont visibles.
	//    * menubar=x : Indique si la barre des menus est visible.
	//    * toolbar=x : Indique si la barre d'outils est visible.
	//    * directories=x : Indique si la barre d'outils personnelle est visible.
	//    * location=x : Indique si la barre d'adresse est visible.
	//    * status=x : Indique si la barre des status est visible.
	//
	// x = yes ou 1 si l'affirmation est vrai ; no ou 0 si elle est fausse.

	var top=(screen.height-hauteur)/2;
	var left=(screen.width-largeur)/2;
	window.open(page,"","top="+top+",left="+left+",width="+largeur+",height="+hauteur+",directories=no,toolbar=no,menubar=no,location=no,"+options);
	}
  </script>
</head>

<body>
  <div style="text-align: center;">
  <?php
         $requete_liste_fiche = "SELECT * FROM ".$prefix_base."eleves WHERE ".$prefix_base."eleves.login = '".$select_fiche_eleve."'";
         $execution_liste_fiche = mysql_query($requete_liste_fiche) or die('Erreur SQL !'.$requete_liste_fiche.'<br />'.mysql_error());
         while ( $data_liste_fiche = mysql_fetch_array($execution_liste_fiche))
          {
              $login_eleve = $data_liste_fiche['login'];
              $select_fiche_eleve_photo = $data_liste_fiche['elenoet'];
              $ele_id_eleve = $data_liste_fiche['ele_id'];
              $nom_eleve = strtoupper($data_liste_fiche['nom']);
              $prenom_eleve = ucfirst($data_liste_fiche['prenom']);
              $naissance_eleve = date_frl(date_sql(affiche_date_naissance($data_liste_fiche['naissance'])));
              $date_de_naissance = $data_liste_fiche['naissance'];
              $sexe_eleve = $data_liste_fiche['sexe'];
		$responsable_eleve = tel_responsable($ele_id_eleve);
          }

    ?>

<div class="couleur_ligne_3" style="width: 500px; height: 135px; margin: auto; border: solid 2px #2F4F4F;">
	<div style="background-image: url(../images/haut_tab.png); font-size: 120%; font-weight: bold; color: #E8F1F4; text-align: left;">Identité élève</div>
	<div style="width: 90px; float: right; padding: 2px; text-align: center;">
		<?php
		if ( getSettingValue("active_module_trombinoscopes")=='y' ) {
		$nom_photo = nom_photo($select_fiche_eleve_photo,"eleves",2);
	             //$photos = "../../photos/eleves/".$nom_photo;
	             $photos = $nom_photo;
	                // if (($nom_photo == "") or (!(file_exists($photos)))) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
	                 if (($nom_photo==NULL) or (!(file_exists($photos)))) { $photos = "../../mod_trombinoscopes/images/trombivide.jpg"; }
			       $valeur=redimensionne_image($photos);
	                 ?><img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /><?php
	             }
		?>
	</div>
	<div style="text-align: left; margin: 2px;">
		Nom : <?php echo $nom_eleve; ?><br />
		Prénom : <?php echo $prenom_eleve; ?><br />
		Date de naissance : <?php echo $naissance_eleve; ?><br />
		Age : <?php echo age($date_de_naissance); ?> ans <br /><br />
		Classe : <?php echo classe_de($login_eleve); ?> (Suivi par : <?php echo pp(classe_court_de($login_eleve)); ?>)
	</div>
</div>
<div class="couleur_ligne_3" style="width: 450px; margin: auto; border-bottom: 2px solid #2F4F4F; border-left: 2px solid #2F4F4F; border-right: 2px solid #2F4F4F; text-align: left;">
 	<div style="font-size: 130%; background: #555555; color: #FFFFFF;"><?php if ( !empty($responsable_eleve[1]) ) { ?>Les responsables<?php } else { ?>Le reponsable<?php } ?></div>
	<div style="margin: 5px;">
		<?php
			$cpt_responsable = 0;
			while ( !empty($responsable_eleve[$cpt_responsable]) )
			{
				echo $responsable_eleve[$cpt_responsable]['civilite'].' '.strtoupper($responsable_eleve[$cpt_responsable]['nom']).' '.ucfirst($responsable_eleve[$cpt_responsable]['prenom']).'<br />';
				$telephone = '';
					if ( !empty($responsable_eleve[$cpt_responsable]['tel_pers']) ) { $telephone = $telephone.'Tél. <strong>'.$responsable_eleve[$cpt_responsable]['tel_pers'].'</strong> '; }
					if ( !empty($responsable_eleve[$cpt_responsable]['tel_prof']) ) { $telephone = $telephone.'Prof. <strong>'.$responsable_eleve[$cpt_responsable]['tel_prof'].'</strong> '; }
					if ( !empty($responsable_eleve[$cpt_responsable]['tel_port']) ) { $telephone = $telephone.'Port. '.$responsable_eleve[$cpt_responsable]['tel_port'].'<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="13" width="13" />'; }
				echo $telephone;
				$cpt_responsable = $cpt_responsable + 1;
			}
			?>
	</div>
</div>

<br />

<table class="entete_tableau_selection" cellspacing="0" cellpadding="2">
       <tr>
           <td class="titre_tableau_gestion" colspan="2"><b>Information élève</b></td>
       </tr>
       <tr>
           <td class="td_tableau_fiche" style="width: 400px; vertical-align: top">
           <?php
	     $cpt_komenti = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."suivi_eleve_cpe WHERE ".$prefix_base."suivi_eleve_cpe.eleve_suivi_eleve_cpe = '".$login_eleve."'"),0);
             $requete_komenti = "SELECT * FROM ".$prefix_base."suivi_eleve_cpe WHERE ".$prefix_base."suivi_eleve_cpe.eleve_suivi_eleve_cpe = '".$login_eleve."' ORDER BY date_suivi_eleve_cpe DESC, heure_suivi_eleve_cpe DESC LIMIT ".$debut_selection_suivi.", 2";
             $execution_komenti = mysql_query($requete_komenti) or die('Erreur SQL !'.$requete_komenti.'<br />'.mysql_error());
              while ( $data_komenti = mysql_fetch_array($execution_komenti))
                {
			$action_pour_eleve = '';
			if(!empty($data_komenti['action_suivi_eleve_cpe']) and $data_komenti['action_suivi_eleve_cpe'] != 'A') { $action_pour_eleve = ', '.action_de($data_komenti['action_suivi_eleve_cpe']); }
		   if(!empty($data_komenti['niveau_message_suivi_eleve_cpe'])) {
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='1') { $couleur='#FFFFFF'; $couleur2='#280FFF'; $drapeau='[information]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='2') { $couleur='#FFF3DF'; $couleur2='#FF782F'; $drapeau='[important]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='3') { $couleur='#FFDFDF'; $couleur2='#FF0000'; $drapeau='[prioritaire]'; }
			  } else { $couleur='#FFFFFF'; $couleur2='#4DFF2F'; $drapeau=''; } ?>
                    <p class="info_eleve" style="background: <?php echo $couleur; ?>;"><b><?php echo date_frl($data_komenti['date_suivi_eleve_cpe']).' - '.$data_komenti['heure_suivi_eleve_cpe'].' <span style="font-weight: bold; color: '.$couleur2.';">'.$drapeau.'</span>'; ?></b><br /><?php echo $data_komenti['komenti_suivi_eleve_cpe'].$action_pour_eleve; ?><br /><br /><span class="dimi_texte">écrit par: <?php echo qui($data_komenti['parqui_suivi_eleve_cpe']); ?><br />[ <a href="fiche_eleve.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;id_suivi_eleve_cpe=<?php echo $data_komenti['id_suivi_eleve_cpe']; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi; ?>&amp;action=modifier#formulaire">modifier</a> | <a href="fiche_eleve.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;id_suivi_eleve_cpe=<?php echo $data_komenti['id_suivi_eleve_cpe']; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi; ?>&amp;action_sql=supprimer&amp;uid_post=<?php echo my_ereg_replace(' ','%20',$uid); ?>">supprimer</a> ]</span></p>
           <?php } ?>

	<div style="text-align: center;">
	  <?php if($debut_selection_suivi!='0') { ?><a href="fiche_eleve.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi-'2'; ?>">Page précédente</a><?php } ?>
	  <?php $debut_selection_suivi_suivant = $debut_selection_suivi+'2'; if($debut_selection_suivi!='0' and $debut_selection_suivi_suivant<=$cpt_komenti) { ?> | <?php } ?>
	  <?php if($debut_selection_suivi_suivant<=$cpt_komenti) { ?><a href="fiche_eleve.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi+'2'; ?>">Page suivant</a><?php } ?>
	</div>

            <a name="formulaire"></a>
            <form method="post" action="fiche_eleve.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>">
               <fieldset>
                 <legend>Ajouter un suivi</legend>
                 <select id="info_suivi" onchange="data_info_suivi.value += info_suivi.options[info_suivi.selectedIndex].text + '\n'" style="width: 210px;">
                   <option>Sélectionné un texte rapide</option>
		   <option>[Exclusion du cours] A été exclus du cours de:   à:</option>
                   <option>Rencontre avec les parents</option>
                   <option>Avertissement</option>
                 </select>
                   <input type="hidden" name="debut_selection_suivi" value="<?php echo $debut_selection_suivi; ?>" />
                   <input type="hidden" name="eleve_suivi_eleve_cpe" value="<?php echo $login_eleve; ?>" />
                   <input type="hidden" name="action_sql" value="<?php if($action == "modifier") { ?>modifier<?php } else { ?>ajouter<?php } ?>" />
                   <?php if($action == "modifier") { ?>
                      <input type="hidden" name="id_suivi_eleve_cpe" value="<?php echo $id_suivi_eleve_cpe; ?>" />
                   <?php } ?>
		   <input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
                   <input type="submit" name="submit8" value="Valider la saisie" />
                   <br />
		   <table style="border: 0px" cellspacing="1" cellpadding="1">
		   	<tr>
				<td>
				<textarea id="data_info_suivi" name="data_info_suivi" rows="3" cols="35" style="height: 70px;"><?php if($action == "modifier") { echo $data_modif_fiche['komenti_suivi_eleve_cpe']; } ?></textarea>
				</td>
				<td>
				<div style="font-family: Arial; font-size: 0.8em; background-color: #FFFFFF; border : 1px solid #0061BD; height: 70px; padding: 0px;">
				Niveau de priorité<br />
				<input name="niveau_urgent" id="nur1" value="1" type="radio" <?php if(!empty($data_modif_fiche['niveau_message_suivi_eleve_cpe']) and $data_modif_fiche['niveau_message_suivi_eleve_cpe']==='1') { ?>checked="checked"<?php } else { ?>checked="checked"<?php } ?> /><label for="nur1" style="cursor: pointer;">Information</label><br />
				<input name="niveau_urgent" id="nur2" value="2" type="radio" <?php if(!empty($data_modif_fiche['niveau_message_suivi_eleve_cpe']) and $data_modif_fiche['niveau_message_suivi_eleve_cpe']==='2') { ?>checked="checked"<?php } ?> /><label for="nur2" style="cursor: pointer;">Important</label><br />
				<input name="niveau_urgent" id="nur3" value="3" type="radio" <?php if(!empty($data_modif_fiche['niveau_message_suivi_eleve_cpe']) and $data_modif_fiche['niveau_message_suivi_eleve_cpe']==='3') { ?>checked="checked"<?php } ?> /><label for="nur3" style="cursor: pointer;">Prioritaire</label><br />
				</div>
				</td>
			</tr>
		   </table>
		   Entraine une action
			<select name="action_suivi" style="width: 250px;">
 	                <?php
			      $requete_liste_action = "SELECT init_absence_action, def_absence_action FROM ".$prefix_base."absences_actions ORDER BY init_absence_action ASC";
        	              $resultat_liste_action = mysql_query($requete_liste_action) or die('Erreur SQL !'.$requete_liste_action.'<br />'.mysql_error());
	                      while ( $data_liste_action = mysql_fetch_array ($resultat_liste_action)) { ?>
                                     <option value="<?php echo $data_liste_action['init_absence_action']; ?>" <?php if(!empty($data_modif_fiche['action_suivi_eleve_cpe']) and $data_modif_fiche['action_suivi_eleve_cpe'] === $data_liste_action['init_absence_action']) { ?>selected="selected"<?php } ?>><?php echo $data_liste_action['init_absence_action']." - ".$data_liste_action['def_absence_action']; ?></option>
                              <?php } ?>
			</select>
		   <br />
              </fieldset>
            </form>
           </td>
           <td class="td_tableau_fiche bordure_sous_menu" style="width: 100px; vertical-align: top;">
           <?php $cpt_absences = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='A'"),0);
           if($cpt_absences != 0) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=A',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Absences"><?php echo $cpt_absences; ?></a></b> Absence(s)</p>
           <?php } $cpt_retards = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='R'"),0);
           if($cpt_retards != 0) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=R',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Retards"><?php echo $cpt_retards; ?></a></b> Retards</p>
           <?php } $cpt_dispences = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='D'"),0);
           if($cpt_dispences != 0) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=D',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Dispences"><?php echo $cpt_dispences; ?></a></b> Dispences</p>
           <?php } $cpt_infirmeries = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='I'"),0);
           if($cpt_infirmeries != 0) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=I',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Infirmerie"><?php echo $cpt_infirmeries; ?></a></b> Infirmeries</p>
           <br />
           <?php } ?>
           </td>
       </tr>
    </table>
	<br /><a href="javascript:window.close();">Fermer la fenêtre</a>
  </div>
</body>

</html>
<?php mysql_close(); ?>
