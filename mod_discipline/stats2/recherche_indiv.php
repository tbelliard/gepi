<?php
/*
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Didier Blanqui
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

header('Content-Type: text/html; charset=ISO-8859-1');
$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");

// En multisite, on ajoute le répertoire RNE
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			  // On récupère le RNE de l'établissement
		  $repertoire="../../photos/".$_COOKIE['RNE']."/eleves/";
		}else{
		  $repertoire="../../photos/eleves/";
		}
          

$choix=isset($_SESSION['stats_choix']) ? $_SESSION['stats_choix'] :'eleves';
if(isset($_POST['nom'])) {

  switch ($choix) {
    case('eleves'):
      $sql = "SELECT e.nom,e.prenom,e.elenoet,e.login,c.classe  FROM eleves e,classes c,j_eleves_classes jec
                WHERE nom LIKE '%".$_POST['nom']."%'
                AND e.login=jec.login AND jec.id_classe=c.id
                GROUP BY e.login";
      $req = mysql_query($sql);
      break;
    case('personnels'):
      $sql = "SELECT nom,prenom,login,statut FROM utilisateurs
                WHERE nom LIKE '%".$_POST['nom']."%'
                AND (statut='professeur' OR statut='CPE' OR statut='SCOLARITE' OR statut='AUTRE' OR statut='ADMINISTRATEUR')";
      $req = mysql_query($sql);
      break;
  }
  $i = 0;
  echo '<ul class="affichage_nom">';

  while($resultat = mysql_fetch_assoc($req)) {
    echo '<li class="affichage_nom" id="'.$resultat['login'].'"><div class="photo_trombi">';
    switch ($choix) {
      case('eleves'):
        echo '<span class="informal" style="display:none;" >'.$resultat['login'].'</span>';
        if (getSettingValue("active_module_trombinoscopes")=='y') {
          $photo=$repertoire.$resultat['elenoet'].'.jpg';
          if (!file_exists($photo) ) $photo ='../../mod_trombinoscopes/images/trombivide.jpg';
          echo '<img height="71px" src="'.$photo.'"/>';
        }
        echo '</div><div class="nom">'.$resultat['nom'].'&nbsp;'.$resultat['prenom'].'</div>
		<span class="informal"><br \>'.$resultat['classe'].'</span>
        ';
        break;
      case('personnels'):
        echo '<span class="informal" style="display:none;" id="login_cache">'.$resultat['login'].'</span>';
        if (getSettingValue("active_module_trombinoscopes")=='y') {
          $photo=$repertoire.md5(strtolower($resultat['login'])).'.jpg';
          if (!file_exists($photo)) $photo = '../../mod_trombinoscopes/images/trombivide.jpg';
          echo '<img height="71px" src="'.$photo.'"/>';
        }
        echo '</div><div class="nom">'.$resultat['nom'].'&nbsp;'.$resultat['prenom'].'</div>
		<span class="informal"><br \>'.$resultat['statut'].'</span>
        ';
        break;
    }
    echo '</li>';
    if (++$i >= 3)
      die('<li>...</li></ul>');
  }
  echo '</ul>';
  die();
}
?>