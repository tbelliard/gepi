<?php
/*
*$Id$
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

// mise à jour : 05/09/2006 16:18

// Initialisations files
require_once("../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);


// Resume session

$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}

function classe_de($id_classe_eleve)
        {
          include("../secure/connect.inc.php");
            $requete_classe_eleve ="SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.classe, ".$prefix_base."classes.id, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."eleves.login='".$id_classe_eleve."' AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id";
            $execution_classe_eleve = mysql_query($requete_classe_eleve) or die('Erreur SQL !'.$requete_classe_eleve.'<br />'.mysql_error());
            $data_classe_eleve = mysql_fetch_array($execution_classe_eleve);
            $id_classe_eleve = $data_classe_eleve['nom_complet'];
         return($id_classe_eleve);
        }

function annee_en_cours_t($date)
{
    $date = explode('-', $date);
    if (empty($annee_d)) {if ($date[1] < 8) {$annee_d = $date[0] - 1;} else {$annee_d = $date[0];}}
    if (empty($annee_f)) {if ($date[1] >= 8){$annee_f = $date[0] + 1;} else {$annee_f = $date[0];}}
    //Annee en cours
    $annee_en_cours = $annee_d."-".$annee_f;
    return($annee_en_cours);
}

function redimensionne_image($photo)
 {
    // prendre les informations sur l'image
    $info_image = getimagesize($photo);
    // largeur et hauteur de l'image d'origine
    $largeur = $info_image[0];
    $hauteur = $info_image[1];
    // largeur et/ou hauteur maximum à afficher
        if(basename($_SERVER['PHP_SELF'],".php") === "trombi_impr")
                  {
            // si pour impression
             $taille_max_largeur = getSettingValue("l_max_imp_trombinoscopes");
             $taille_max_hauteur = getSettingValue("h_max_imp_trombinoscopes");
              } else {
                // si pour l'affichage écran
                 $taille_max_largeur = getSettingValue("l_max_aff_trombinoscopes");
                 $taille_max_hauteur = getSettingValue("h_max_aff_trombinoscopes");
              }

    // calcule le ratio de redimensionnement
     $ratio_l = $largeur / $taille_max_largeur;
     $ratio_h = $hauteur / $taille_max_hauteur;
     $ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

    // définit largeur et hauteur pour la nouvelle image
     $nouvelle_largeur = $largeur / $ratio;
     $nouvelle_hauteur = $hauteur / $ratio;

    return array($nouvelle_largeur, $nouvelle_hauteur);
 }

 if (empty($_GET['page']) and empty($_POST['page'])) { $page = ''; }
    else { if (isset($_GET['page'])) {$page=$_GET['page'];} if (isset($_POST['page'])) {$page=$_POST['page'];} }
 if (empty($_GET['id'])) { $id = ''; } else { $id=$_GET['id']; }

 if (empty($_GET['classe']) and empty($_POST['classe'])) { $classe = ''; }
   else { if (isset($_GET['classe'])) { $classe = $_GET['classe']; } if (isset($_POST['classe'])) { $classe = $_POST['classe']; } }
 if (empty($_GET['groupe']) and empty($_POST['groupe'])) { $groupe = ''; }
   else { if (isset($_GET['groupe'])) { $groupe = $_GET['groupe']; } if (isset($_POST['groupe'])) { $groupe = $_POST['groupe']; } }
 if (empty($_GET['equipepeda']) and empty($_POST['equipepeda'])) { $equipepeda = ''; }
   else { if (isset($_GET['equipepeda'])) { $equipepeda = $_GET['equipepeda']; } if (isset($_POST['equipepeda'])) { $equipepeda = $_POST['equipepeda']; } }
 if (empty($_GET['discipline']) and empty($_POST['discipline'])) { $discipline = ''; }
   else { if (isset($_GET['discipline'])) { $discipline = $_GET['discipline']; } if (isset($_POST['discipline'])) { $discipline = $_POST['discipline']; } }
if (empty($_GET['statusgepi']) and empty($_POST['statusgepi'])) { $statusgepi = ''; }
   else { if (isset($_GET['statusgepi'])) { $statusgepi = $_GET['statusgepi']; } if (isset($_POST['statusgepi'])) { $statusgepi = $_POST['statusgepi']; } }
 if (empty($_GET['affdiscipline']) and empty($_POST['affdiscipline'])) { $affdiscipline = ''; }
   else { if (isset($_GET['affdiscipline'])) { $affdiscipline = $_GET['affdiscipline']; } if (isset($_POST['affdiscipline'])) { $affdiscipline = $_POST['affdiscipline']; } }



if ( $classe != 'toutes' and $groupe != 'toutes' and $discipline != 'toutes' and $equipepeda != 'toutes' and ( $classe != '' or $groupe != '' or $equipepeda != '' or $discipline != '' or $statusgepi != '' ) ) {
    // on regarde ce qui à était choisie
	// c'est une classe
	if ( $classe != '' and $groupe === '' and $equipepeda === '' and $discipline === '' and $statusgepi === '' ) { $action_affiche = 'classe'; }
	// c'est un groupe
	if ( $classe === '' and $groupe != '' and $equipepeda === '' and $discipline === '' and $statusgepi === '' ) { $action_affiche = 'groupe'; }
	// c'est une équipe pédagogique
	if ( $classe === '' and $groupe === '' and $equipepeda != '' and $discipline === '' and $statusgepi === '' ) { $action_affiche = 'equipepeda'; }
	// c'est une discipline
	if ( $classe === '' and $groupe === '' and $equipepeda === '' and $discipline != '' and $statusgepi === '' ) { $action_affiche = 'discipline'; }
	// c'est un status de gepi
	if ( $classe === '' and $groupe === '' and $equipepeda === '' and $discipline === '' and $statusgepi != '' ) { $action_affiche = 'statusgepi'; }

       if ( $action_affiche === 'classe' ) { $requete_qui = 'SELECT c.id, c.nom_complet, c.classe FROM '.$prefix_base.'classes c WHERE c.id = "'.$classe.'"'; }
       if ( $action_affiche === 'groupe' ) { $requete_qui = 'SELECT g.id, g.name FROM '.$prefix_base.'groupes g WHERE g.id = "'.$groupe.'"'; }
       if ( $action_affiche === 'equipepeda' ) { $requete_qui = 'SELECT c.id, c.nom_complet FROM '.$prefix_base.'classes c WHERE c.id = "'.$equipepeda.'"'; }
       if ( $action_affiche === 'discipline' ) { $requete_qui = 'SELECT m.matiere, m.nom_complet FROM '.$prefix_base.'matieres m WHERE m.matiere = "'.$discipline.'"'; }
       if ( $action_affiche === 'statusgepi' ) { $requete_qui = 'SELECT statut FROM '.$prefix_base.'utilisateurs u WHERE u.statut = "'.$statusgepi.'"'; }
            $execute_qui = mysql_query($requete_qui) or die('Erreur SQL !'.$requete_qui.'<br />'.mysql_error());
            $donnees_qui = mysql_fetch_array($execute_qui) or die('Erreur SQL !'.$execute_qui.'<br />'.mysql_error());
       if ( $action_affiche === 'classe' ) { $entete = "Classe : ".$donnees_qui['nom_complet']." (".$donnees_qui['classe'].")";}
       if ( $action_affiche === 'groupe' ) { $entete = "Groupe : ".$donnees_qui['name']; }
       if ( $action_affiche === 'equipepeda' ) { $entete = "Equipe pédagogique : ".$donnees_qui['nom_complet']; }
       if ( $action_affiche === 'discipline' ) { $entete = "Discipline : ".$donnees_qui['nom_complet']." (".$donnees_qui['matiere'].")"; }
       if ( $action_affiche === 'statusgepi' ) { $entete = "Status : ".$statusgepi; }

	// choix du répertoire ou chercher les photos entre professeur ou élève
       if ( $action_affiche === 'classe' ) { $repertoire = 'eleves'; }
       if ( $action_affiche === 'groupe' ) { $repertoire = 'eleves'; }
       if ( $action_affiche === 'equipepeda' ) { $repertoire = 'personnels'; }
       if ( $action_affiche === 'discipline' ) { $repertoire = 'personnels'; }
       if ( $action_affiche === 'statusgepi' ) { $repertoire = 'personnels'; }

	//je recherche les personnes concerné pour la sélection effectué
	   // élève d'une classe
           if ( $action_affiche === 'classe' ) { $requete_trombi = "SELECT e.login, e.nom, e.prenom, e.elenoet, jec.login, jec.id_classe, jec.periode, c.classe, c.id, c.nom_complet
								    FROM ".$prefix_base."eleves e, ".$prefix_base."j_eleves_classes jec, ".$prefix_base."classes c
								    WHERE e.login = jec.login
								      AND jec.id_classe = c.id
								      AND id = '".$classe."'
								    GROUP BY nom, prenom"; }
	   // élève d'un groupe
           if ( $action_affiche === 'groupe' ) { $requete_trombi = "SELECT jeg.login, jeg.id_groupe, jeg.periode, e.login, e.nom, e.prenom, e.elenoet, g.id, g.name, g.description
								    FROM ".$prefix_base."eleves e, ".$prefix_base."groupes g, ".$prefix_base."j_eleves_groupes jeg
								    WHERE jeg.login = e.login
								      AND jeg.id_groupe = g.id
								      AND g.id = '".$groupe."'
								    GROUP BY nom, prenom"; }

	   // professeurs d'une équipe pédagogique
           if ( $action_affiche === 'equipepeda' ) { $requete_trombi = 'SELECT * FROM '.$prefix_base.'utilisateurs u, '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'classes c
								        WHERE jgp.id_groupe = jgc.id_groupe
									  AND jgc.id_classe = c.id
									  AND u.login = jgp.login
								      	  AND c.id = "'.$equipepeda.'"
								        GROUP BY u.nom, u.prenom
								        ORDER BY nom ASC, prenom ASC'; }

	   // professeurs par discipline
           if ( $action_affiche === 'discipline' ) { $requete_trombi = 'SELECT * FROM '.$prefix_base.'utilisateurs u, '.$prefix_base.'j_professeurs_matieres jpm, '.$prefix_base.'matieres m
								        WHERE u.login = jpm.id_professeur
									  AND m.matiere = jpm.id_matiere
								      	  AND m.matiere = "'.$discipline.'"
								        GROUP BY u.nom, u.prenom
								        ORDER BY nom ASC, prenom ASC'; }

	   // par statut cpe ou professeur
           if ( $action_affiche === 'statusgepi' ) { $requete_trombi = 'SELECT * FROM '.$prefix_base.'utilisateurs u
								        WHERE u.statut = "'.$statusgepi.'"
								        GROUP BY u.nom, u.prenom
								        ORDER BY nom ASC, prenom ASC'; }


function matiereprof($prof, $equipepeda) {

      global $prefix_base;

	$prof_de = '';
           if ( $prof != '' ) {
		$requete_matiere = 'SELECT * FROM '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'j_groupes_matieres jgm, '.$prefix_base.'j_groupes_professeurs jgp, '.$prefix_base.'matieres m
				    WHERE jgc.id_classe = "'.$equipepeda.'"
				      AND jgc.id_groupe = jgp.id_groupe
				      AND jgm.id_matiere = m.matiere
				      AND jgp.id_groupe = jgm.id_groupe
				      AND jgp.login = "'.$prof.'"';
		$execution_matiere = mysql_query($requete_matiere) or die('Erreur SQL !'.$requete_matiere.'<br />'.mysql_error());
	        while ($donnee_matiere = mysql_fetch_array($execution_matiere))
        	{
                  $prof_de = $prof_de.'<br />'.$donnee_matiere['nom_complet'].' ';
		}
	}
	return ($prof_de);
}

           $execution_trombi = mysql_query($requete_trombi) or die('Erreur SQL !'.$requete_trombi.'<br />'.mysql_error());
           $cpt_photo = 1;
           while ($donnee_trombi = mysql_fetch_array($execution_trombi))
             {
                //insertion de l'élève dans la varibale $eleve_absent
                  $login_trombinoscope[$cpt_photo] = $donnee_trombi['login'];
                  $nom_trombinoscope[$cpt_photo] = $donnee_trombi['nom'];
                  $prenom_trombinoscope[$cpt_photo] = $donnee_trombi['prenom'];

		       if ( $action_affiche === 'classe' ) { $id_photo_trombinoscope[$cpt_photo] = strtolower($donnee_trombi['elenoet']); }
		       if ( $action_affiche === 'groupe' ) { $id_photo_trombinoscope[$cpt_photo] = strtolower($donnee_trombi['elenoet']); }
		       if ( $action_affiche === 'equipepeda' ) { $id_photo_trombinoscope[$cpt_photo] = $donnee_trombi['login']; }
		       if ( $action_affiche === 'discipline' ) { $id_photo_trombinoscope[$cpt_photo] = $donnee_trombi['login']; }
		       if ( $action_affiche === 'statusgepi' ) { $id_photo_trombinoscope[$cpt_photo] = $donnee_trombi['login']; }

		       $matiere_prof[$cpt_photo] = '';
		       if ( $action_affiche === 'equipepeda' and $affdiscipline === 'oui' ) { $matiere_prof[$cpt_photo] = matiereprof($login_trombinoscope[$cpt_photo], $equipepeda); }

                  $cpt_photo = $cpt_photo + 1;
             }
             $total = $cpt_photo;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Trombinoscope</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<div style="text-align: center;">
  <table width="100%" border="0" cellspacing="0" cellpadding="2" style="border : 1px solid #010101;" align="center">
    <tr>
      <td width="70%" height="20" style="text-align: left; ">
        <span style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">TROMBINOSCOPE <?php $datej = date('Y-m-d'); $annee_en_cours_t=annee_en_cours_t($datej); echo $annee_en_cours_t; ?></span><br />
        <span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold;"><?php echo $entete; ?></span>
      </td>
    </tr>
  </table>
  <br />
  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <?php $i = 1; while( $i < $total) {?>
    <tr align="center" valign="top">
      <?php $nom_photo = nom_photo($id_photo_trombinoscope[$i],$repertoire); ?>
      <td width="20%"><?php if ($i < $total) { $nom_es = Strtoupper($nom_trombinoscope[$i]); $prenom_es = Ucfirst($prenom_trombinoscope[$i]); $photo = "../photos/".$repertoire."/".$nom_photo; if(file_exists($photo)) { $valeur=redimensionne_image($photo); } else { $valeur[0]=76; $valeur[1]=100; } ?><img src="<?php if (file_exists($photo)) { echo $photo; } else { ?>images/trombivide.jpg<?php } ?>" style="border: 0px; width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px;" alt="<?php echo $prenom_es." ".$nom_es; ?>" title="<?php echo $prenom_es." ".$nom_es; ?>" /><span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;"><br /><b><?php echo $nom_es; ?></b><br /><?php echo $prenom_es; if ( $matiere_prof[$i] != '' ) { ?><span style=" font: normal 10pt Arial, Helvetica, sans-serif;"><?php echo $matiere_prof[$i]; ?></span> <?php } $i = $i + 1; ?></span><?php } ?></td>
      <?php $nom_photo = nom_photo($id_photo_trombinoscope[$i],$repertoire); ?>
      <td width="20%"><?php if ($i < $total) { $nom_es = Strtoupper($nom_trombinoscope[$i]); $prenom_es = Ucfirst($prenom_trombinoscope[$i]); $photo = "../photos/".$repertoire."/".$nom_photo; if(file_exists($photo)) { $valeur=redimensionne_image($photo); } else { $valeur[0]=76; $valeur[1]=100; } ?><img src="<?php if (file_exists($photo)) { echo $photo; } else { ?>images/trombivide.jpg<?php } ?>" style="border: 0px; width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px;" alt="<?php echo $prenom_es." ".$nom_es; ?>" title="<?php echo $prenom_es." ".$nom_es; ?>" /><span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;"><br /><b><?php echo $nom_es; ?></b><br /><?php echo $prenom_es; if ( $matiere_prof[$i] != '' ) { ?><span style=" font: normal 10pt Arial, Helvetica, sans-serif;"><?php echo $matiere_prof[$i]; ?></span> <?php } $i = $i + 1; ?></span><?php } ?></td>
      <?php $nom_photo = nom_photo($id_photo_trombinoscope[$i],$repertoire); ?>
      <td width="20%"><?php if ($i < $total) { $nom_es = Strtoupper($nom_trombinoscope[$i]); $prenom_es = Ucfirst($prenom_trombinoscope[$i]); $photo = "../photos/".$repertoire."/".$nom_photo; if(file_exists($photo)) { $valeur=redimensionne_image($photo); } else { $valeur[0]=76; $valeur[1]=100; } ?><img src="<?php if (file_exists($photo)) { echo $photo; } else { ?>images/trombivide.jpg<?php } ?>" style="border: 0px; width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px;" alt="<?php echo $prenom_es." ".$nom_es; ?>" title="<?php echo $prenom_es." ".$nom_es; ?>" /><span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;"><br /><b><?php echo $nom_es; ?></b><br /><?php echo $prenom_es; if ( $matiere_prof[$i] != '' ) { ?><span style=" font: normal 10pt Arial, Helvetica, sans-serif;"><?php echo $matiere_prof[$i]; ?></span> <?php } $i = $i + 1; ?></span><?php } ?></td>
      <?php $nom_photo = nom_photo($id_photo_trombinoscope[$i],$repertoire); ?>
      <td width="20%"><?php if ($i < $total) { $nom_es = Strtoupper($nom_trombinoscope[$i]); $prenom_es = Ucfirst($prenom_trombinoscope[$i]); $photo = "../photos/".$repertoire."/".$nom_photo; if(file_exists($photo)) { $valeur=redimensionne_image($photo); } else { $valeur[0]=76; $valeur[1]=100; } ?><img src="<?php if (file_exists($photo)) { echo $photo; } else { ?>images/trombivide.jpg<?php } ?>" style="border: 0px; width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px;" alt="<?php echo $prenom_es." ".$nom_es; ?>" title="<?php echo $prenom_es." ".$nom_es; ?>" /><span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;"><br /><b><?php echo $nom_es; ?></b><br /><?php echo $prenom_es; if ( $matiere_prof[$i] != '' ) { ?><span style=" font: normal 10pt Arial, Helvetica, sans-serif;"><?php echo $matiere_prof[$i]; ?></span> <?php } $i = $i + 1; ?></span><?php } ?></td>
      <?php $nom_photo = nom_photo($id_photo_trombinoscope[$i],$repertoire); ?>
      <td width="20%"><?php if ($i < $total) { $nom_es = Strtoupper($nom_trombinoscope[$i]); $prenom_es = Ucfirst($prenom_trombinoscope[$i]); $photo = "../photos/".$repertoire."/".$nom_photo; if(file_exists($photo)) { $valeur=redimensionne_image($photo); } else { $valeur[0]=76; $valeur[1]=100; } ?><img src="<?php if (file_exists($photo)) { echo $photo; } else { ?>images/trombivide.jpg<?php } ?>" style="border: 0px; width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px;" alt="<?php echo $prenom_es." ".$nom_es; ?>" title="<?php echo $prenom_es." ".$nom_es; ?>" /><span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;"><br /><b><?php echo $nom_es; ?></b><br /><?php echo $prenom_es; if ( $matiere_prof[$i] != '' ) { ?><span style=" font: normal 10pt Arial, Helvetica, sans-serif;"><?php echo $matiere_prof[$i]; ?></span> <?php } $i = $i + 1; ?></span><?php } ?></td>
    </tr>
  <?php } ?>
  </table>
</div>
<?php mysql_close(); ?>
</body>
</html>
<?php } ?>
