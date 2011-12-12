<?php
$gepiPath=$this->data['baseurlpath'].'/../../../..';
?>

	</div><!-- #content -->

</div><!-- #wrap -->
			<a href="/<?php echo $gepiPath;?>/gestion/info_vie_privee.php" onclick="centrerpopup('gestion/info_vie_privee.php',700,480,'scrollbars=yes,statusbar=no,resizable=yes');return false;">
			<!-- <a href="gestion/info_vie_privee.php" id="info_vie_privee"> -->
				<img src='./images/icons/vie_privee.png' alt='' class='link' />
				Informations vie privée
			</a><br/><br/>

	<?php if(getSettingValue("gepiAdminAdressPageLogin")!='n'){
		$gepiAdminAdress=explode(",",getSettingValue("gepiAdminAdress"));?>
			<a href="mailto:<?php echo $gepiAdminAdress[0];?>?subject=GEPI">Contacter l'administrateur</a><br/><br/>
	<?php } ?>
	<div id="new_login_footer">
		<a href="http://gepi.mutualibre.org/" title="vers le site de GEPI : Gestion des Élèves Par Internet" >
			GEPI : Outil de gestion, de suivi, et de visualisation graphique des résultats scolaires (écoles, collèges, lycées)
		</a>

		<br />
		Copyright &copy; 2001-2008
		<a href="mailto:th&#111;mas%2eb%65lliard&#64;fr&#101;e&#46;%66r?subject=GEPI">Thomas Belliard</a> 
		, <a href="mailto:lau&#114;ent%2ed%65lin&#101;au&#64;ac%2dpo%69tiers&#46;fr?subject=GEPI">Laurent Delineau</a>
		, <a href="mailto:eric%2eebrun&#64;ac%2dpoitiers&#46;fr?subject=GEPI">Eric Lebrun</a>
		, <a href="mailto:st%65phane&#46;boireau%40ac%2drouen%2efr?subject=GEPI">Stéphane Boireau</a> 

		, <a href="mailto:coll%65gerb%40f&#114;ee.%66r?subject=GEPI">Julien Jocal</a>
	</div> 

</body>
</html>