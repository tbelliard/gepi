<?php
/*
 * $Id$
*/
?>
<!-- menus général -->

<div class='div_tableau'>
	<h3 class="colonne ie_gauche">
		<a<?php if (isset($newentree['ancre'])){?> name="<?php echo $newentree['ancre']?>"<?php } ?>
		href="<?php echo $newentree['lien']?>">
			<?php echo $newentree['titre']?>
		</a>
	</h3>
	<p class="colonne ie_droite">
		<?php echo $newentree['expli']?>
	</p>
</div>


<!-- Fin menu	général -->	
