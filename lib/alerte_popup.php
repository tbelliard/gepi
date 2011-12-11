<?php
/** Affiche un popup
 * 
 * 
 * @package General
 * @subpackage Alertes
 *
 */
	$image_path=null;
	
	if (isset($niveau_arbo)) {
		//echo "\$niveau_arbo=$niveau_arbo<br />";
		if("$niveau_arbo"=="public") {
			$image_path.="../";
		}
		else {
			$niveau_arbo_count = $niveau_arbo;
			while ($niveau_arbo_count != 0) {
				$image_path.="../";
				$niveau_arbo_count--;
			}
		}
	}
	else {
		$image_path = "./";
	}
?>
<div id="alert_cache" style="z-index:2000;
							display:none;
							position:absolute;
							top:0px;
							left:0px;
							background-color:#000000;
							width:200px;
							height:200px;"> &nbsp;</div>
<div id="alert_entete" style="z-index:2000;
								display:none;
								position:absolute;"><img   src="<?php echo $image_path ?>images/alerte_entete.png" alt="alerte" /></div>
<div id="alert_popup" style="z-index:2000;
								text-align:justify;
								width:600px;
								height:130px;
								border:1px solid black;
								background-color:white;
								padding-top:10px;
								padding-left:20px;
								padding-right:20px;
								display:none;
								position:absolute;
								background-image:url('<?php echo $image_path ?>images/degrade_noir.png');
								background-repeat:repeat-x;
								background-position: left bottom;">
	<div id="alert_message"></div>
	<div id="alert_button" style="margin:5px auto;width:90px;">
		<div id="alert_bouton_ok" style="float:left;"><img src="<?php echo $image_path ?>images/bouton_continue.png" alt="ok" /></div>
	</div>
</div>
