<?php

/**
 * Construit le div de la barre de menu pour les admins
 *
 * @copyright 2008-2011
 * @license GNU/GPL v2
 * @package General
 * @subpackage Affichage
 */
// ====== SECURITE =======

if (!$_SESSION["login"]) {
    header("Location: ../logout.php?auto=2");
    die();
}
include ("header_barre_admin_template.php");
?>
<?php if (count($tbs_menu_admin)) : ?>
<div id="menu_barre">
	<div class="menu_barre_bottom"></div>
	<div class="menu_barre_container">
		<ul class="niveau1">
			<?php foreach ($tbs_menu_admin as $value) { if ("$value[li]"!="") { ?>
			<?php echo $value['li']; ?>
			<?php }} unset($value); ?>
		</ul>
	</div>
</div>
<?php endif ?>