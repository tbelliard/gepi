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

$resultat_session = resumeSession();
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

 if (empty($_GET['page']) AND empty($_POST['page'])) {$page="";}
    else { if (isset($_GET['page'])) {$page=$_GET['page'];} if (isset($_POST['page'])) {$page=$_POST['page'];} }
 if(empty($_POST['classe']) AND empty($_GET['classe'])) {$classe = ""; }
    else { if(isset($_GET['classe'])) {$classe = $_GET['classe']; } if(isset($_POST['classe'])) {$classe = $_POST['classe']; } }
 if (empty($_GET['id'])) {$id = ''; } else {$id=$_GET['id']; }

// sélection des élèves de la classe ou du groupe
  if($classe != "" AND $classe != "toutes")
   {
    // on prend et on regarde si c'est une classe ou un groupe
           $cestquoi = explode("-",$classe);
       if($cestquoi[0] == "c") { $classe = $cestquoi[1]; }
       if($cestquoi[0] == "g") { $groupe = $cestquoi[1]; }

      //je compte les élève si = 0 alors on redirige
           if($cestquoi[0] == "c") { $cpt_eleves = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND id = '".$classe."'"),0); }
       if($cestquoi[0] == "g") { $cpt_eleves = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."groupes, ".$prefix_base."j_eleves_groupes WHERE ".$prefix_base."j_eleves_groupes.id_groupe = ".$prefix_base."groupes.id AND ".$prefix_base."groupes.id = '".$groupe."'"),0); }
           if($cpt_eleves == 0) { $classe = ""; }
         //je recherche tous les élèves de la classe sélectionné si c'est une classe qui est sélectionné
           if($cestquoi[0] == "c") { $requete_eleve ="SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."eleves.elenoet, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.classe, ".$prefix_base."classes.id, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND id = '".$classe."' GROUP BY nom, prenom"; }
         //je recherche tous les élèves du groupe sélectionné si c'est un groupe qui est sélectionné
           if($cestquoi[0] == "g") { $requete_eleve ="SELECT ".$prefix_base."j_eleves_groupes.login, ".$prefix_base."j_eleves_groupes.id_groupe, ".$prefix_base."j_eleves_groupes.periode, ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."eleves.elenoet, ".$prefix_base."groupes.id, ".$prefix_base."groupes.name, ".$prefix_base."groupes.description FROM ".$prefix_base."eleves, ".$prefix_base."groupes, ".$prefix_base."j_eleves_groupes WHERE ".$prefix_base."j_eleves_groupes.login = ".$prefix_base."eleves.login AND ".$prefix_base."j_eleves_groupes.id_groupe = ".$prefix_base."groupes.id AND ".$prefix_base."groupes.id = '".$groupe."' GROUP BY nom, prenom"; }
           $execution_eleve = mysql_query($requete_eleve) or die('Erreur SQL !'.$requete_eleve.'<br />'.mysql_error());
           $cpt_eleve = 1;
           while ($data_eleve = mysql_fetch_array($execution_eleve))
             {
                //insertion de l'élève dans la varibale $eleve_absent
                  $nom_eleve[$cpt_eleve] = $data_eleve['nom'];
                  $prenom_eleve[$cpt_eleve] = $data_eleve['prenom'];
                  $id_eleve_photo[$cpt_eleve] = strtolower($data_eleve['elenoet']);
                  $cpt_eleve = $cpt_eleve + 1;
             }
             $total = $cpt_eleve;
   }
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
        <span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold;">
           <?php
    // on prend et on regarde si c'est une classe ou un groupe
       if($cestquoi[0] == "c") { $classe = $cestquoi[1]; }
       if($cestquoi[0] == "g") { $groupe = $cestquoi[1]; }

       if($cestquoi[0] == "c") { $requete_qui = 'SELECT '.$prefix_base.'classes.id, '.$prefix_base.'classes.nom_complet FROM '.$prefix_base.'classes WHERE '.$prefix_base.'classes.id = "'.$classe.'"'; }
       if($cestquoi[0] == "g") { $requete_qui = 'SELECT '.$prefix_base.'groupes.id, '.$prefix_base.'groupes.name FROM '.$prefix_base.'groupes WHERE '.$prefix_base.'groupes.id = "'.$groupe.'"'; }
            $execute_qui = mysql_query($requete_qui) or die('Erreur SQL !'.$requete_qui.'<br />'.mysql_error());
            $donnees_qui = mysql_fetch_array($execute_qui) or die('Erreur SQL !'.$execute_qui.'<br />'.mysql_error());
       if($cestquoi[0] == "c") { echo "Classe : ".$donnees_qui['nom_complet']; }
       if($cestquoi[0] == "g") { echo "Groupe : ".$donnees_qui['name']; }
          ?>
          </span>
      </td>
    </tr>
  </table>
  <br />
  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <?php $i = 1; while( $i < $total) {?>
    <tr align="center" valign="top">
      <td width="20%"><?php if ($i < $total) { $nom_es = Strtoupper($nom_eleve[$i]); $prenom_es = Ucfirst($prenom_eleve[$i]); $photo = "../photos/eleves/".$id_eleve_photo[$i].".jpg"; if(file_exists($photo)) { $valeur=redimensionne_image($photo); } else { $valeur[0]=76; $valeur[1]=100; } ?><img src="<?php if (file_exists($photo)) { echo $photo; } else { ?>images/trombivide.jpg<?php } ?>" width="<?php echo $valeur[0]; ?>" height="<?php echo $valeur[1]; ?>" border="0" alt="<?php echo $prenom_es." ".$nom_es; ?>" title="<?php echo $prenom_es." ".$nom_es; ?>" /><span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;"><br /><b><?php echo Strtoupper($nom_eleve[$i]); ?></b><br /><?php echo $prenom_eleve[$i]; $i = $i + 1; ?></span><?php } ?></td>
      <td width="20%"><?php if ($i < $total) { $nom_es = Strtoupper($nom_eleve[$i]); $prenom_es = Ucfirst($prenom_eleve[$i]); $photo = "../photos/eleves/".$id_eleve_photo[$i].".jpg"; if(file_exists($photo)) { $valeur=redimensionne_image($photo); } else { $valeur[0]=76; $valeur[1]=100; } ?><img src="<?php if (file_exists($photo)) { echo $photo; } else { ?>images/trombivide.jpg<?php } ?>" width="<?php echo $valeur[0]; ?>" height="<?php echo $valeur[1]; ?>" border="0" alt="<?php echo $prenom_es." ".$nom_es; ?>" title="<?php echo $prenom_es." ".$nom_es; ?>" /><span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;"><br /><b><?php echo Strtoupper($nom_eleve[$i]); ?></b><br /><?php echo $prenom_eleve[$i]; $i = $i + 1; ?></span><?php } ?></td>
      <td width="20%"><?php if ($i < $total) { $nom_es = Strtoupper($nom_eleve[$i]); $prenom_es = Ucfirst($prenom_eleve[$i]); $photo = "../photos/eleves/".$id_eleve_photo[$i].".jpg"; if(file_exists($photo)) { $valeur=redimensionne_image($photo); } else { $valeur[0]=76; $valeur[1]=100; } ?><img src="<?php if (file_exists($photo)) { echo $photo; } else { ?>images/trombivide.jpg<?php } ?>" width="<?php echo $valeur[0]; ?>" height="<?php echo $valeur[1]; ?>" border="0" alt="<?php echo $prenom_es." ".$nom_es; ?>" title="<?php echo $prenom_es." ".$nom_es; ?>" /><span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;"><br /><b><?php echo Strtoupper($nom_eleve[$i]); ?></b><br /><?php echo $prenom_eleve[$i]; $i = $i + 1; ?></span><?php } ?></td>
      <td width="20%"><?php if ($i < $total) { $nom_es = Strtoupper($nom_eleve[$i]); $prenom_es = Ucfirst($prenom_eleve[$i]); $photo = "../photos/eleves/".$id_eleve_photo[$i].".jpg"; if(file_exists($photo)) { $valeur=redimensionne_image($photo); } else { $valeur[0]=76; $valeur[1]=100; } ?><img src="<?php if (file_exists($photo)) { echo $photo; } else { ?>images/trombivide.jpg<?php } ?>" width="<?php echo $valeur[0]; ?>" height="<?php echo $valeur[1]; ?>" border="0" alt="<?php echo $prenom_es." ".$nom_es; ?>" title="<?php echo $prenom_es." ".$nom_es; ?>" /><span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;"><br /><b><?php echo Strtoupper($nom_eleve[$i]); ?></b><br /><?php echo $prenom_eleve[$i]; $i = $i + 1; ?></span><?php } ?></td>
      <td width="20%"><?php if ($i < $total) { $nom_es = Strtoupper($nom_eleve[$i]); $prenom_es = Ucfirst($prenom_eleve[$i]); $photo = "../photos/eleves/".$id_eleve_photo[$i].".jpg"; if(file_exists($photo)) { $valeur=redimensionne_image($photo); } else { $valeur[0]=76; $valeur[1]=100; } ?><img src="<?php if (file_exists($photo)) { echo $photo; } else { ?>images/trombivide.jpg<?php } ?>" width="<?php echo $valeur[0]; ?>" height="<?php echo $valeur[1]; ?>" border="0" alt="<?php echo $prenom_es." ".$nom_es; ?>" title="<?php echo $prenom_es." ".$nom_es; ?>" /><span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;"><br /><b><?php echo Strtoupper($nom_eleve[$i]); ?></b><br /><?php echo $prenom_eleve[$i]; $i = $i + 1; ?></span><?php } ?></td>
    </tr>
  <?php } ?>
  </table>
</div>
<?php mysql_close(); ?>
</body>
</html>

