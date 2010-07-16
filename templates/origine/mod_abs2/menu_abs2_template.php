<ul>
<?php
  foreach($onglets as $titreOnglet){
	if($titreOnglet->lien!=""){
?>
  <li class="css-tabs<?php if($titreOnglet->classe!=''){echo " ".$titreOnglet->classe ;} ?>">
	<a href='<?php echo $titreOnglet->lien; ?>' title='<?php echo $titreOnglet->expli; ?>'>
	  <?php echo $titreOnglet->texte; ?>
	</a>
  </li>
<?php
	}
  }
  unset($titreOnglet);
?>
</ul>