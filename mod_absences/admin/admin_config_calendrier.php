<?php
/*
 *
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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
//mes fonctions
include("../lib/functions.php");

// gestion du style
$style_specifique = "mod_absences/styles/mod_absences";

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
};

// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}
// header
$titre_page = "Définition des créneaux horaires";
require_once("../../lib/header.inc.php");

// si égale 1 = ouvert si égale 2 = fermée
 function FermeeOuvert($rep)
 {
     if ( $rep === '1' ) { $reponse = 'ouvert'; }
     if ( $rep === '2' ) { $reponse = 'fermée'; }
     if ( $rep != '1' and $rep != '2') { $reponse = ''; }
     
     return $reponse;
 }

// si égale 1 = pas en période vacance si égale 2 = période de vacance
 function VacanceScolaire($rep)
 {
     if ( $rep === '0' ) { $reponse = ''; }
     if ( $rep === '1' ) { $reponse = 'période de vacance scolaire'; }
     if ( $rep != '0' and $rep != '1') { $reponse = ''; }
	     
     return $reponse;
 }

	if (empty($_GET['action_sql']) and empty($_POST['action_sql'])) {$action_sql="";}
	   else { if (isset($_GET['action_sql'])) {$action_sql=$_GET['action_sql'];} if (isset($_POST['action_sql'])) {$action_sql=$_POST['action_sql'];} }
	if (empty($_GET['action']) and empty($_POST['action'])) {exit();}
	   else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }

	if (empty($_GET['nb_ajout']) and empty($_POST['nb_ajout'])) { $nb_ajout = '1';}
	   else { if (isset($_GET['nb_ajout'])) {$nb_ajout=$_GET['nb_ajout'];} if (isset($_POST['nb_ajout'])) {$nb_ajout=$_POST['nb_ajout'];} }
	if (empty($_GET['id_calendrier']) and empty($_POST['id_calendrier'])) { $id_calendrier = ''; }
	   else { if (isset($_GET['id_calendrier'])) { $id_calendrier = $_GET['id_calendrier']; } if (isset($_POST['id_calendrier'])) { $id_calendrier = $_POST['id_calendrier']; } }

	if (empty($_GET['lajournee']) and empty($_POST['lajournee'])) { $lajournee = ''; }
	   else { if (isset($_GET['lajournee'])) { $lajournee = $_GET['lajournee']; } if (isset($_POST['lajournee'])) { $lajournee = $_POST['lajournee']; } }

	$cpt_temp = 0;
	$champs = 'classe_concerne_calendrier_'.$cpt_temp;
	while ( !empty($_POST[$champs]) )
	{
		if (empty($_GET[$champs]) and empty($_POST[$champs])) { $defchamps = ''; }
		   else { if (isset($_GET[$champs])) { $classe_concerne_calendrier_insc[$cpt_temp] = $_GET[$champs]; } if (isset($_POST[$champs])) { $classe_concerne_calendrier_insc[$cpt_temp] = $_POST[$champs]; } }

		$cpt_temp = $cpt_temp + 1;
		$champs = 'classe_concerne_calendrier_'.$cpt_temp;
	}

	if (empty($_GET['nom_calendrier']) and empty($_POST['nom_calendrier'])) { $nom_calendrier = ''; }
	   else { if (isset($_GET['nom_calendrier'])) { $nom_calendrier = $_GET['nom_calendrier']; } if (isset($_POST['nom_calendrier'])) { $nom_calendrier = $_POST['nom_calendrier']; } }
	if (empty($_GET['jourdebut_calendrier']) and empty($_POST['jourdebut_calendrier'])) { $jourdebut_calendrier = ''; }
	   else { if (isset($_GET['jourdebut_calendrier'])) { $jourdebut_calendrier = $_GET['jourdebut_calendrier']; } if (isset($_POST['jourdebut_calendrier'])) { $jourdebut_calendrier = $_POST['jourdebut_calendrier']; } }
	if (empty($_GET['heuredebut_calendrier']) and empty($_POST['heuredebut_calendrier'])) { $heuredebut_calendrier = ''; }
	   else { if (isset($_GET['heuredebut_calendrier'])) { $heuredebut_calendrier = $_GET['heuredebut_calendrier']; } if (isset($_POST['heuredebut_calendrier'])) { $heuredebut_calendrier = $_POST['heuredebut_calendrier']; } }
	if (empty($_GET['jourfin_calendrier']) and empty($_POST['jourfin_calendrier'])) { $jourfin_calendrier = ''; }
	   else { if (isset($_GET['jourfin_calendrier'])) { $jourfin_calendrier = $_GET['jourfin_calendrier']; } if (isset($_POST['jourfin_calendrier'])) { $jourfin_calendrier = $_POST['jourfin_calendrier']; } }
	if (empty($_GET['heurefin_calendrier']) and empty($_POST['heurefin_calendrier'])) { $heurefin_calendrier = ''; }
	   else { if (isset($_GET['heurefin_calendrier'])) { $heurefin_calendrier = $_GET['heurefin_calendrier']; } if (isset($_POST['heurefin_calendrier'])) { $heurefin_calendrier = $_POST['heurefin_calendrier']; } }
	if (empty($_GET['etabferme_calendrier']) and empty($_POST['etabferme_calendrier'])) { $etabferme_calendrier = ''; }
	   else { if (isset($_GET['etabferme_calendrier'])) { $etabferme_calendrier = $_GET['etabferme_calendrier']; } if (isset($_POST['etabferme_calendrier'])) { $etabferme_calendrier = $_POST['etabferme_calendrier']; } }
	if (empty($_GET['etabvacances_calendrier']) and empty($_POST['etabvacances_calendrier'])) { $etabvacances_calendrier = ''; }
	   else { if (isset($_GET['etabvacances_calendrier'])) { $etabvacances_calendrier = $_GET['etabvacances_calendrier']; } if (isset($_POST['etabvacances_calendrier'])) { $etabvacances_calendrier = $_POST['etabvacances_calendrier']; } }



$total = '0'; $verification[0] = '1'; $erreur = '0';

if ($action_sql == "ajouter" or $action_sql == "modifier")
{
   while ($total < $nb_ajout)
      {
            // Vérifcation des variable
		$classe_concerne_calendrier_ins = $classe_concerne_calendrier_insc[$total];
		$nom_calendrier_ins = $nom_calendrier[$total];
		$jourdebut_calendrier_ins = date_sql($jourdebut_calendrier[$total]);

		if ( isset($lajournee[$total]) and $lajournee[$total] === '1' ) {
			$heuredebut_calendrier_ins = '00:00:00';
			$jourfin_calendrier_ins = date_sql($jourdebut_calendrier[$total]);
			$heurefin_calendrier_ins = '23:59:59';
		} else {
			$heuredebut_calendrier_ins = $heuredebut_calendrier[$total];
			$jourfin_calendrier_ins = date_sql($jourfin_calendrier[$total]);
			$heurefin_calendrier_ins = $heurefin_calendrier[$total];
			}

		$etabferme_calendrier_ins = $etabferme_calendrier[$total];
		if ( isset($etabvacances_calendrier[$total]) ) { $etabvacances_calendrier_ins = $etabvacances_calendrier[$total]; } else { $etabvacances_calendrier_ins = ''; }

              if ($action_sql == "modifier") { $id_calendrier_ins = $id_calendrier[$total]; }

		// si des classe sont sélectionné alors on les mets au format classe;classe;classe..
		$classe_implose = '';
		if ( !empty($classe_concerne_calendrier_ins[0]) )
		{
			$cpt_classe = '0';
			while ( !empty($classe_concerne_calendrier_ins[$cpt_classe]) )
			{
				if ( $cpt_classe === '0' ) { $classe_implose = $classe_concerne_calendrier_ins[$cpt_classe]; }
				if ( $cpt_classe != '0' ) { $classe_implose = $classe_implose.';'.$classe_concerne_calendrier_ins[$cpt_classe]; }
				$cpt_classe = $cpt_classe + 1;
			}
		}

	    // vérification des champs non vide
            if( $nom_calendrier_ins != "" && $jourdebut_calendrier_ins != "" )
            {
			$test = '1';
                            if($action_sql == "modifier") { $test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."edt_calendrier WHERE id_calendrier = '$id_calendrier_ins'"),0); }
                              if ($test === '1')
                              {
					// conversion des date et heure au format timestamps
					$debut_calendrier_ts_ins = timestamps_encode($jourdebut_calendrier_ins, $heuredebut_calendrier_ins);
					$fin_calendrier_ts_ins = timestamps_encode($jourfin_calendrier_ins, $heurefin_calendrier_ins);

                                 if($action_sql == "ajouter")
                                  {

                                     // Requete d'insertion MYSQL
                                        $requete = "INSERT INTO ".$prefix_base."edt_calendrier (classe_concerne_calendrier, nom_calendrier, debut_calendrier_ts, fin_calendrier_ts, etabferme_calendrier, etabvacances_calendrier) VALUES ('".$classe_implose."', '".$nom_calendrier_ins."', '".$debut_calendrier_ts_ins."', '".$fin_calendrier_ts_ins."', '".$etabferme_calendrier_ins."', '".$etabvacances_calendrier_ins."')";
                                  }
                                 if($action_sql == "modifier")
                                  {
                                     // Requete de mise à jour MYSQL
                                        $requete = "UPDATE ".$prefix_base."edt_calendrier SET
							classe_concerne_calendrier = '".$classe_implose."',
							nom_calendrier = '".$nom_calendrier_ins."',
							debut_calendrier_ts = '".$debut_calendrier_ts_ins."',
							fin_calendrier_ts = '".$fin_calendrier_ts_ins."',
							etabferme_calendrier = '".$etabferme_calendrier_ins."',
							etabvacances_calendrier = '".$etabvacances_calendrier_ins."'
						     WHERE id_calendrier = '".$id_calendrier_ins."'";
                                  }
                                // Execution de cette requete dans la base cartouche
                                  mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$sql.'<br />'.mysqli_error($GLOBALS["mysqli"]));
                                  $verification[$total] = 1;
                              } else {
                                        // vérification = 2 - Ce créneaux horaires existe déjas
                                         $verification[$total] = 2;
                                         $erreur = 1;
                                     }
            } else {
                     // vérification = 3 - Tous les champs ne sont pas remplie
                     $verification[$total] = 3;
                     $erreur = 1;
                   }
      $total = $total + 1;
      }

      if($erreur == 0)
       {
          $action = "visualiser";
       } else {
                 $o = 0;
                 $n = 0;
                 while ($o < $nb_ajout)
                  {
                    if($verification[$o] != 1)
                     {
                        $nom_calendrier_erreur[$n] = $nom_calendrier[$o];
                        $jourdebut_calendrier_erreur[$n] = $jourdebut_calendrier[$o];
                        $heuredebut_calendrier_erreur[$n] = $heuredebut_calendrier[$o];
                        $jourfin_calendrier_erreur[$n] = $jourdebut_calendrier[$o];
                        $heurefin_calendrier_erreur[$n] = $heuredebut_calendrier[$o];
                        $etabferme_calendrier_erreur[$n] = $etabferme_calendrier[$o];
                        $etabvacances_calendrier_erreur[$n] = $etabvacances_calendrier[$o];
                        $verification_erreur[$n] = $verification[$o];
                        if ($action_sql == "modifier") { $id_calendrier_erreur[$n] = $id_calendrier[$o]; }
                        $n = $n + 1;
                     }
                     $o = $o + 1;
                  }
                  $nb_ajout = $n;
                  if ($action_sql == "ajouter") { $action = "ajouter"; }
                  if ($action_sql == "modifier") { $action = "modifier"; }
              }
}

if ($action_sql == "supprimer")
 {
     //Requete de supprersion MYSQL
     $requete = "DELETE FROM ".$prefix_base."edt_calendrier WHERE id_calendrier ='$id_calendrier'";
     // Execution de cette requete
     mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.mysqli_error($GLOBALS["mysqli"]));
 }

if ($action == "modifier")
 {
      $requete_modif_calendrier = 'SELECT * FROM '.$prefix_base.'edt_calendrier WHERE id_calendrier = "'.$id_calendrier.'"';
      $resultat_modif_calendrier = mysqli_query($GLOBALS["mysqli"], $requete_modif_calendrier) or die('Erreur SQL !'.$requete_modif_calendrier.'<br />'.mysqli_error($GLOBALS["mysqli"]));
      $data_modif_calendrier = mysqli_fetch_array($resultat_modif_calendrier);

	$id_calendrier['0'] = $data_modif_calendrier['id_calendrier'];
	$classe_concerne_calendrier['0'] = $data_modif_calendrier['classe_concerne_calendrier'];
	$nom_calendrier['0'] = $data_modif_calendrier['nom_calendrier'];
	$debut_calendrier_ts = timestamps_decode($data_modif_calendrier['debut_calendrier_ts'],'fr');
		$jourdebut_calendrier['0'] = $debut_calendrier_ts['date'];
		$heuredebut_calendrier['0'] = $debut_calendrier_ts['heure'];
	$fin_calendrier_ts = timestamps_decode($data_modif_calendrier['fin_calendrier_ts'],'fr');
		$jourfin_calendrier['0'] = $fin_calendrier_ts['date'];
		$heurefin_calendrier['0'] = $fin_calendrier_ts['heure'];
	$etabferme_calendrier['0'] = $data_modif_calendrier['etabferme_calendrier'];
	$etabvacances_calendrier['0'] = $data_modif_calendrier['etabvacances_calendrier'];

 }

 //Configuration du calendrier
         //include("../../lib/calendrier/calendrier.class.php");
         include("../../lib/calendrier/calendrier_id.class.php");

	 $i = '0';
         while ( $i<$nb_ajout )
           {
               //$cal_a[$i] = new Calendrier("form2", "jourdebut_calendrier[$i]");
               //$cal_b[$i] = new Calendrier("form2", "jourfin_calendrier[$i]");
               $cal_a[$i] = new Calendrier("form2", "jourdebut_calendrier_$i");
               $cal_b[$i] = new Calendrier("form2", "jourfin_calendrier_$i");
               $i = $i+1;
           }

?><script type="text/javascript">

function preremplis(mavar)
{
 if (document.getElementById('lajournee_'+mavar).checked===true)
 {
		document.getElementById('heuredebut_calendrier_'+mavar).value='00:00';
		document.getElementById('jourfin_calendrier_'+mavar).value=document.getElementById('jourdebut_calendrier_'+mavar).value;
		document.getElementById('heurefin_calendrier_'+mavar).value='23:59';
 }
}

function decoche(mavar)
{
		document.getElementById('lajournee_'+mavar).checked=false;
}

</script><?php

echo "<p class=bold>";
if ($action=="modifier" or $action=="ajouter") {
	echo "<a href=\"admin_config_calendrier.php?action=visualiser\">";
} else {
	echo "<a href='index.php'>";
}
echo "<img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>";
?>
<?php if ($action == "visualiser") { ?>
<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<h2>Définition du calendrier</h2>
<a href="admin_config_calendrier.php?action=ajouter"><img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter une date</a>
<br/><br/>
    <table cellpadding="0" cellspacing="1" class="tab_table">
      <tr>
        <th class="tab_th" style="width: 180px;">définition</th>
        <th class="tab_th" style="width: 100px;">du</th>
        <th class="tab_th" style="width: 80px;">à</th>
        <th class="tab_th" style="width: 100px;">au</th>
        <th class="tab_th" style="width: 80px;">à</th>
        <th class="tab_th" style="width: 80px;">étab.</th>
        <th class="tab_th" style="width: 25px;"></th>
        <th class="tab_th" style="width: 25px;"></th>
      </tr>
    <?php
    $requete_periode = 'SELECT * FROM '.$prefix_base.'edt_calendrier ORDER BY debut_calendrier_ts ASC';
    $execution_periode = mysqli_query($GLOBALS["mysqli"], $requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.mysqli_error($GLOBALS["mysqli"]));
    $i=1;
    while ( $data_periode = mysqli_fetch_array( $execution_periode ) ) {
       if ($i === '1') { $i = '2'; $couleur_cellule = 'couleur_ligne_1'; } else { $couleur_cellule = 'couleur_ligne_2'; $i = '1'; } ?>
        <tr class="<?php echo $couleur_cellule; ?>">
          <td><?php echo $data_periode['nom_calendrier']; ?></td>
          <td><?php $date_du = timestamps_decode($data_periode['debut_calendrier_ts'],'fr'); echo $date_du['date']; ?></td>
          <td><?php echo $date_du['heure']; ?></td>
          <td><?php $date_au = timestamps_decode($data_periode['fin_calendrier_ts'],'fr'); echo $date_au['date']; ?></td>
          <td><?php echo $date_au['heure']; ?></td>
          <td><?php echo FermeeOuvert($data_periode['etabferme_calendrier']); ?></td>
          <td><a href="admin_config_calendrier.php?action=modifier&amp;id_calendrier=<?php echo $data_periode['id_calendrier']; ?>"><img src="../../images/icons/configure.png" title="Modifier" border="0" alt="Modifier" /></a></td>
          <td><a href="admin_config_calendrier.php?action=visualiser&amp;action_sql=supprimer&amp;id_calendrier=<?php echo $data_periode['id_calendrier']; ?>" onClick="return confirm('Etes-vous certain de vouloir supprimer cette entrée ?')"><img src="../images/x2.png" width="22" height="22" title="Supprimer" border="0" alt="Supprimer" /></a></td>
        </tr>
     <?php } ?>
    </table>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<br/><br/>
<?php } ?>


<?php if ($action == "ajouter" or $action == "modifier") { ?>
<div style="text-align:center">
  <?php if ($action == "ajouter") { ?>

	<h2>Période du calendrier</h2>

    <form name="form1" method="post" action="admin_config_calendrier.php?action=ajouter">
      <table class="tab_table">
        <tr>
          <th class="tab_th">Nombre de date à définir</th>
        </tr>
        <tr>
          <td class="couleur_ligne_1" style="text-align: right;"><input name="nb_ajout" type="text" size="5" maxlength="5" value="<?php if(isset($nb_ajout)) { echo $nb_ajout; } else { ?>1<?php } ?>" class="input_sans_bord" />&nbsp;&nbsp;&nbsp;<input type="submit" name="Submit2" value="Mettre à jour" /></td>
        </tr>
      </table>
    </form>
	<br />
  <?php }
  
    if ($action=="modifier") {
		echo "<h2>Modifier une date</h2>";
	}
	?>
    <form action="admin_config_calendrier.php?action=visualiser&amp;action_sql=<?php if($action=="ajouter") { ?>ajouter<?php } if($action=="modifier") { ?>modifier<?php } ?>" method="post" name="form2" id="form2">
      <table cellpadding="2" cellspacing="2" class="tab_table">
        <tr>
          <th class="tab_th">définition</th>
          <th class="tab_th">datation</th>          
          <th class="tab_th">étab.</th>
          <th class="tab_th">Vacance scolaire</th>
	  <th class="tab_th">Classe concerné</th>
        </tr>
        <?php
        $i = '1';
        $nb = 0;
        while($nb < $nb_ajout) {
        if ($i === '1') { $i = '2'; $couleur_cellule = 'couleur_ligne_1'; } else { $couleur_cellule = 'couleur_ligne_2'; $i = '1'; } ?>
        <?php if (isset($verification_erreur[$nb]) and $verification_erreur[$nb] != 1) { ?>
         <tr>
          <td><img src="../images/attention.png" width="28" height="28" alt="" /></td>
          <td colspan="3" class="erreur_rouge_jaune"><b>- Erreur -<br />
          <?php if ($verification_erreur[$nb] == 2) { ?>Ce créneau horaire existe déja<?php } ?>
          <?php if ($verification_erreur[$nb] == 5) { ?>L'heure de fin n'est pas définie<?php } ?>
          <?php if ($verification_erreur[$nb] == 4) { ?>L'heure de début n'est pas définie<?php } ?>
          <?php if ($verification_erreur[$nb] == 3) { ?>Tous les champs ne sont pas remplis<?php } ?>
          <?php if ($verification_erreur[$nb] == 6) { ?>L'heure de fin ne peut pas être plus petite que l'heure de début<?php } ?>
          </b><br /></td>
         </tr>
        <?php } ?>
        <tr class="<?php echo $couleur_cellule; ?>">
          <td><input name="nom_calendrier[<?php echo $nb; ?>]" type="text" id="nom_calendrier_<?php echo $nb; ?>" size="15" maxlength="25" value="<?php if($action=="modifier") { echo $data_modif_calendrier['nom_calendrier']; } elseif (isset($nom_calendrier_erreur[$nb])) { echo $nom_calendrier_erreur[$nb]; } ?>" class="input_sans_bord" /></td>
          <td style="text-align: left;">
	  <table cellpadding="0" cellspacing="0" style="margin: auto; border: 0px;">
	     <tr style="white-space: nowrap;">
		<td>commence le</td>
		<td><input name="jourdebut_calendrier[<?php echo $nb; ?>]" type="text" id="jourdebut_calendrier_<?php echo $nb; ?>" size="10" maxlength="10" value="<?php if($action=="modifier") { echo $jourdebut_calendrier['0']; } elseif (isset($jourdebut_calendrier_erreur[$nb])) { echo $jourdebut_calendrier_erreur[$nb]; } else { ?>00/00/0000<?php } ?>" class="input_sans_bord" onchange="decoche('<?php echo $nb; ?>');" />
			<a href="#calend" onClick="<?php
	//echo $cal_a[$nb]->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170);
	echo $cal_a[$nb]->get_strPopup('../../lib/calendrier/pop.calendrier_id.php', 350, 170);
?>; decoche('<?php echo $nb; ?>');"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
		</td>
             </tr>
	     <tr><td colspan="2"><input name="lajournee[<?php echo $nb; ?>]" id="lajournee_<?php echo $nb; ?>" value="1" type="checkbox" <?php if ( isset($lajournee_erreur[$nb]) and $lajournee[$nb] === '1' ) { ?>checked="checked"<?php } ?> title="Concerne la journée entière" onclick="preremplis('<?php echo $nb; ?>')" /> toute la journée</td></tr>
             <tr><td>heure de début</td><td><input name="heuredebut_calendrier[<?php echo $nb; ?>]" type="text" id="heuredebut_calendrier_<?php echo $nb; ?>" size="5" maxlength="5" value="<?php if($action=="modifier") { echo $heuredebut_calendrier['0']; } elseif (isset($heuredebut_calendrier_erreur[$nb])) { echo $heuredebut_calendrier_erreur[$nb]; } else { ?>00:00<?php } ?>" class="input_sans_bord" /></td></tr>
             <tr><td>termine le</td><td><input name="jourfin_calendrier[<?php echo $nb; ?>]" type="text" id="jourfin_calendrier_<?php echo $nb; ?>" size="10" maxlength="10" value="<?php if($action=="modifier") { echo $jourfin_calendrier['0']; } elseif (isset($jourfin_calendrier_erreur[$nb])) { echo $jourfin_calendrier_erreur[$nb]; } else { ?>00/00/0000<?php } ?>" class="input_sans_bord" />
			<a href="#calend" onClick="<?php
	//echo $cal_b[$nb]->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170);
	echo $cal_b[$nb]->get_strPopup('../../lib/calendrier/pop.calendrier_id.php', 350, 170);
?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
		 </td>
	     </tr>
             <tr><td>heure de fin</td><td><input name="heurefin_calendrier[<?php echo $nb; ?>]" type="text" id="heurefin_calendrier_<?php echo $nb; ?>" size="5" maxlength="5" value="<?php if($action=="modifier") { echo $heurefin_calendrier['0']; } elseif (isset($heurefin_calendrier_erreur[$nb])) { echo $heurefin_calendrier_erreur[$nb]; } else { ?>00:00<?php } ?>" class="input_sans_bord" /></td></tr>
	</table>
	  </td>
	  <td><select name="etabferme_calendrier[<?php echo $nb; ?>]"><option value="1" <?php if ( ( $action === 'modifier' and $etabferme_calendrier['0'] === '1' ) or ( isset($etabferme_calendrier_erreur[$nb]) and $etabferme_calendrier_erreur[$nb] === '1') ) { ?>checked="checked"<?php } ?>>ouvert</option><option value="0" <?php if ( ( $action === 'modifier' and $etabferme_calendrier['0'] === '0' ) or ( isset($etabferme_calendrier_erreur[$nb]) and $etabferme_calendrier_erreur[$nb] === '0') ) { ?>selected="selected"<?php } ?>>fermé</option></select>
	  <td><input name="etabvacances_calendrier[<?php echo $nb; ?>]" value="1" type="checkbox" <?php if ( ( $action === 'modifier' and $etabvacances_calendrier['0'] === '1' ) or ( isset($etabvacances_calendrier_erreur[$nb]) and $etabvacances_calendrier_erreur[$nb] === '1') ) { ?>checked="checked"<?php } ?> title="Période de vacance scolaire" /></td>
	  <td>
		<?php $tab_classe['0'] = ''; if ( isset($classe_concerne_calendrier['0']) ) { $tab_classe = explode(';',$classe_concerne_calendrier['0']); } ?>
		<select name="classe_concerne_calendrier_<?php echo $nb; ?>[]" id="classe_concerne_calendrier_<?php echo $nb; ?>" size="6" multiple="multiple">
		<?php
			$requete_classe = ('SELECT * FROM '.$prefix_base.'classes c
						 ORDER BY c.classe ASC');
	                $resultat_classe = mysqli_query($GLOBALS["mysqli"], $requete_classe) or die('Erreur SQL !'.$requete_classe.'<br />'.mysqli_error($GLOBALS["mysqli"]));

                ?><option value="" <?php if ( $tab_classe['0'] === '' ) { ?>selected="selected"<?php } ?>>toute les classe</option>
                        <?php while ( $donnee_classe = mysqli_fetch_array($resultat_classe)) { ?>
                               <option value="<?php echo $donnee_classe['id']; ?>" <?php if ( in_array($donnee_classe['id'],$tab_classe, TRUE) ) { ?>selected="selected"<?php } ?>><?php echo ucwords($donnee_classe['classe']); ?></option>
                        <?php } ?>
                </select>

<?php if($action=="modifier") { ?>
              <input type="hidden" name="id_calendrier[<?php echo $nb; ?>]" value="<?php if (isset($id_calendrier_erreur[$nb])) { echo $id_calendrier_erreur[$nb]; } else { echo $id_calendrier; } ?>" />
            <?php } ?>
	  </td>
        </tr>
            

        <?php $nb = $nb + 1; } ?>
      </table>
      
      <input type="hidden" name="nb_ajout" value="<?php echo $nb_ajout; ?>" />
      <br/>
      <input type="submit" name="Submit" value="Enregistrer" />
    </form>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } 

require("../../lib/footer.inc.php");

?>

