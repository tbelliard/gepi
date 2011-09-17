<?php

// on reconstitue $gepiPath 
$gepiPath=$this->data['baseurlpath'].'/../../..'; // par précaution
if (!strpos($this->data['baseurlpath'],"lib/simplesaml/www/ "))
	$gepiPath=substr($this->data['baseurlpath'],0,strpos($this->data['baseurlpath'],"lib/simplesaml/www/")-1);


$this->data['header'] = $this->t('{login:user_pass_header}');

if (strlen($this->data['username']) > 0) {
	$this->data['autofocus'] = 'password';
} else {
	$this->data['autofocus'] = 'username';
}
$this->includeAtTemplateBase('includes/header.php');

?>

<?php
if ($this->data['errorcode'] !== NULL) {
?>
	<div style="text-align: center;">
		<!--img src="/<?php echo $this->data['baseurlpath']; ?>images/icons/experience/gtk-dialog-error.48x48.png" style="float: left; margin: 15px " />
		<h2><?php echo $this->t('{login:error_header}'); ?></h2>
		<p><b><?php echo $this->t('{errors:title_' . $this->data['errorcode'] . '}'); ?></b></p>
		<p><?php echo $this->t('{errors:descr_' . $this->data['errorcode'] . '}'); ?></p-->
		<p style="color: red;">Vous n'avez pas été authentifié.<br>Vérifiez votre identifiant/mot de passe.</p>
	</div>
<?php
}
?>
	<!--h2 style="break: both"><?php echo $this->t('{login:user_pass_header}'); ?></h2>

	<p><?php echo $this->t('{login:user_pass_text}'); ?></p-->
	
	<p style="text-align: center;">Afin d'utiliser Gepi, vous devez vous identifier.</p>

	<form action="?" method="post" name="f">
	<table style="margin: auto;">
		<tr>
			<td rowspan="2"><img src="/<?php echo $gepiPath."/images/icons/lock.png" ?>" alt="" /></td>
			<td style="padding: .3em;"><?php echo $this->t('{login:username}'); ?></td>
			<td>
<?php
if ($this->data['forceUsername']) {
	echo '<strong style="font-size: medium">' . htmlspecialchars($this->data['username']) . '</strong>';
} else {
	echo '<input type="text" id="username" tabindex="1" name="username" value="' . htmlspecialchars($this->data['username']) . '" />';
}
?>
			</td>
		</tr>
		<tr>
			<td style="padding: .3em;"><?php echo $this->t('{login:password}'); ?></td>
			<td><input id="password" type="password" tabindex="2" name="password" /></td>
		</tr>
		<tr>
			<td colspan="3" align="center"><input type="submit" tabindex="4" value="<?php echo $this->t('{login:login_button}'); ?>" /></td>
		</tr>
<?php
if (array_key_exists('organizations', $this->data)) {
	if (array_key_exists('selectedOrg', $this->data) && $this->data['selectedOrg'] != '' ) {
		//si il y a déjà une sélection, on affiche pas le formulaire
		echo '<input id="organization" type="hidden" name="organization" value="'.$this->data['selectedOrg'].'" />';
	} else if (isset($_COOKIE['RNE']) && $_COOKIE['RNE'] != '' ) {
		//si il y a déjà une sélection, on affiche pas le formulaire
		echo '<input id="organization" type="hidden" name="organization" value="'.$_COOKIE['RNE'].'" />';
	} else {
	?>
		<tr>
			<td style="padding: .3em;"><?php echo $this->t('{login:organization}'); ?></td>
			<td><select name="organization" tabindex="3">
		<?php
		$selectedOrg = NULL;
		
		foreach ($this->data['organizations'] as $orgId => $orgDesc) {
			if (is_array($orgDesc)) {
				$orgDesc = $this->t($orgDesc);
			}
		
			if ($orgId === $selectedOrg) {
				$selected = 'selected="selected" ';
			} else {
				$selected = '';
			}
		
			echo '<option ' . $selected . 'value="' . htmlspecialchars($orgId) . '">' . htmlspecialchars($orgDesc) . '</option>';
		}
		?>
					</select></td>
				</tr>
		<?php
		
	}
	
	
}
?>

	</table>

<?php
foreach ($this->data['stateparams'] as $name => $value) {
	echo('<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" />');
}
?>

	</form>
	
<p id='mot_passe' title="Cliquez pour demander un nouveau mot de passe" style="text-align: center;padding-top: 10px;">
	<a class='small' href='/<?php echo $gepiPath."/recover_password.php" ?>'>
	Mot de passe oublié ? 
	</a>
</p>

<?php
/*
if(!empty($this->data['links'])) {
	echo '<ul class="links" style="margin-top: 2em">';
	foreach($this->data['links'] AS $l) {
		echo '<li><a href="' . htmlspecialchars($l['href']) . '">' . htmlspecialchars($this->t($l['text'])) . '</a></li>';
	}
	echo '</ul>';
}




echo('<h2>' . $this->t('{login:help_header}') . '</h2>');
echo('<p>' . $this->t('{login:help_text}') . '</p>');
*/
$this->includeAtTemplateBase('includes/footer.php');
?>
<script type="text/javascript">
 document.getElementById("wrap").style.width = "27em";
</script>

