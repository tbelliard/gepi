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
?>
<div class="bilans">
  <form action="index.php?ctrl=bilans&action=<?php echo $action_from;?>" method="post" name="select_evolution" id="select_evolution">
      <table class="boireaus">
      <tr>
        <td class="nouveau"> Choisir les filtres </td>
        <td><a href="index.php?ctrl=bilans&action=choix_filtres&action_from=<?php echo $action_from;?>"><img src="apps/img/filtres.png" alt="filtres" title="filtrer"/></a></td>
      </tr>
      </table><br />      
      <table class="boireaus">
      <tr>
         <td  class="nouveau" colspan="3">Paramètres sélectionnés (filtres et évolutions)</td>
      </tr>
      <tr>
         <th>Type</th>
         <th>Filtres</th>
         <th>Choix pour les tableaux d'évolution</th>
      </tr>
      <tr>
        <td class="nouveau"><?php if($filtres_categories) { ?><a href="index.php?ctrl=bilans&action=maj_filtre&type=categories&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Cliquez pour vider" >Catégories</a><?php } else echo'Catégories'; ?></td>
        <td>
          <?php if($filtres_categories): ?>
            <?php foreach($libelles_categories as $categorie): ?>
          <a href="index.php?ctrl=bilans&action=maj_filtre&type=categories&choix=<?php echo $categorie?>&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Cliquez pour supprimer"><?php echo $categorie,' - '; ?></a>
            <?php endforeach ;?>
          <?php else: ?>
          Aucun
          <?php endif;?>
        </td>        
        <td>
          <input type="radio" name="evolution" id="evolution" value="Catégories" <?php if ($_SESSION['choix_evolution']=='Catégories') echo 'checked';?>>
        </td>
      </tr>
      <tr>
        <td class="nouveau"><?php if($filtres_mesures) { ?><a href="index.php?ctrl=bilans&action=maj_filtre&type=mesures&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Cliquez pour vider" >Mesures prises</a><?php } else echo 'Mesures prises';?></td>
        <td>
          <?php if($filtres_mesures) {
            foreach($libelles_mesures as $mesure) {?>
          <a href="index.php?ctrl=bilans&action=maj_filtre&type=mesures&choix=<?php echo $mesure?>&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Cliquez pour supprimer"><?php echo $mesure,' - '?></a>
              <?php    }
          }else {
            echo'Aucun';
          } ?>
        </td>        
        <td>
          <input type="radio" name="evolution" id="evolution" value="Mesures prises" <?php if ($_SESSION['choix_evolution']=='Mesures prises') echo 'checked';?>>
        </td>
      </tr>
      <tr>
        <td class="nouveau"><?php if($filtres_sanctions) {?><a href="index.php?ctrl=bilans&action=maj_filtre&type=sanctions&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Cliquez pour vider" >Sanctions</a><?php } else echo 'Sanctions';?></td>
        <td>
          <?php if($filtres_sanctions) {
            foreach($filtres_sanctions as $sanction) { ?>
          <a href="index.php?ctrl=bilans&action=maj_filtre&type=sanctions&choix=<?php echo $sanction?>&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Cliquez pour supprimer"><?php echo $sanction,' - '?></a>
              <?php    }
          }else {
            echo'Aucun';
          } ?>
        </td>        
        <td>
          <input type="radio" name="evolution" id="evolution" value="Sanctions" <?php if ($_SESSION['choix_evolution']=='Sanctions') echo 'checked';?>>
        </td>
      </tr>
      <tr>
        <td class="nouveau"><?php if($filtres_roles) {?><a href="index.php?ctrl=bilans&action=maj_filtre&type=roles&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Cliquez pour vider" >Rôles</a><?php } else echo 'Rôles';?></td>
        <td><?php if($filtres_roles) {
            foreach($filtres_roles as $role) {?>
          <a href="index.php?ctrl=bilans&action=maj_filtre&type=roles&choix=<?php echo $role;?>&action_from=<?php echo $action_from;?>" class="supp_filtre" title="Cliquez pour supprimer"><?php if($role=="") echo "Aucun rôle affecté - "; else echo $role,' - ';?></a>
              <?php    }
          }else {
            echo'Aucun';
          } ?>
        </td>        
        <td>
          <input type="radio" name="evolution" id="evolution" value="Rôles" <?php if ($_SESSION['choix_evolution']=='Rôles') echo 'checked';?>>
        </td>
      </tr>
    </table>
  </form>
</div>
<div class="bilans">
    <?php if($action_from=='affiche_bilans'):?>
    <table class="boireaus">
    <tr>
      <td  class="nouveau">Choisir le mode de représentation</td>
      <td><a href="index.php?ctrl=bilans&action=affiche_details"><img src="apps/img/simple.png" alt="simple" title="simplifié"/></a>&nbsp;<a href="index.php?ctrl=bilans&action=affiche_details&value=ok"><img src="apps/img/details.png" title="détaillé" alt="détaillé"/></a>&nbsp;</td>
    </tr>
    <tr>
      <td  class="nouveau">Mode de représentation actif</td>
      <td colspan="3"><?php if($mode_detaille) {?>Détaillé<?php }else {?>Simplifié <?php }?> </td>
    </tr>
    <tr>
    </table>
    <?php endif; ?>
</div>
