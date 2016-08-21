<?php
/*
 *
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
// On empêche l'accès direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};
?>
<div id="result">
  <div id="wrap">
    <h3><font class="red">Evolutions des incidents sur l'année scolaire :</font> </h3>
    <?php ClassVue::afficheVue('parametres.php',$vars) ;
    if (isset($incidents)) {
      ?>

    <div id="tableaux">
      <div id="banner">
        <ul  class="css-tabs"  id="menutabs">
            <?php  $i=0;
            foreach ($incidents as $titre=>$incidents_titre) :?>
              <?php if($titre=='L\'Etablissement') {
                if($affichage_etab) : ?>
          <li><a href="#tab<?php echo $i;?>" name="Etablissement-onglet-01" title="Evolution des incidents"><?php echo $titre;?></a></li>
                  <?php $i=$i+1;
                endif;
              } else if ($titre=='Tous les élèves' ||$titre=='Tous les personnels' ) { ?>
          <li><a href="#tab<?php echo $i;?>" name="<?php echo $titre;?>-onglet-01" title="Evolution des incidents"><?php echo $titre;?> </a></li>
                <?php  $i=$i+1;
              } else { ?>
          <li><a href="#tab<?php echo $i;?>" name="<?php echo $titre;?>-onglet-01" title="Evolution des incidents">
                    <?php if (isset($infos_individus[$titre])) {
                      echo mb_substr($infos_individus[$titre]['prenom'],0,1).'.'.$infos_individus[$titre]['nom'];
                      if (isset($infos_individus[$titre]['classe'])) echo'('.$infos_individus[$titre]['classe'].')';
                    }
                    else echo $titre;?></a>
          </li>              
                <?php   $i=$i+1;
            }
            endforeach ?>
        </ul>
      </div>
      <div class="css-panes" id="containDiv">
          <?php
          $i=0;
          foreach ($incidents as $titre=>$incidents_titre) {
            if ($titre!=='L\'Etablissement' || ($titre=='L\'Etablissement' && $affichage_etab) ) {?>
        <div class="panel" id="tab<?php echo $i;?>">
          <table class="sortable resizable">
            <thead>
              <tr>
                <th><?php echo $_SESSION['choix_evolution'];?></th>
                      <?php foreach ($months as $month) :?>
                <th>
                          <?php echo $month ?>
                </th>                
                      <?php endforeach; ?>
                <th>
                  Total
                </th>
              </tr>
            </thead>
            <tbody>
                    <?php foreach($liste_type as $type) :?>
              <tr>
                <td><?php echo $type ?></td>
                        <?php foreach ($months as $key=>$month) :?>
                <td><?php echo $evolution[$titre][$type][$key] ?></td>
                        <?php endforeach ?>
                <td><?php echo $totaux_par_type[$titre][$type] ?></td>
              </tr>
                    <?php endforeach ?>
            </tbody>
            <tfoot>
              <tr>
                <td>
                  Total
                </td>
                      <?php foreach ($months as $key=>$month) :?>
                <td><?php echo $totaux_par_mois[$titre][$key] ?></td>                
                      <?php endforeach ?>
                <td><?php echo $total_general[$titre] ?></td>
              </tr>
            </tfoot>
          </table>
          <br />
          <img src="evolutions_courbes.php?titre=<?php echo $titre;?>" alt="evolution">
        </div>
              <?php $i=$i+1;
            }
          }
        }?>
      </div>
    </div>
  </div>
</div>

