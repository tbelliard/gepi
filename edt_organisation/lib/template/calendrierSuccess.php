
<script type="text/javascript">
	var MouseDown = 0;
	var FirstDiv='';
	var IndiceFirstDiv=0;
	var CurrentDiv='';
	var IndiceCurrentDiv=0;
	var NbSelectedDays = 0;
	var cleardiv = function() {
		var i = 1;
		$$('div.calendar_cell_08').invoke("setStyle", {backgroundColor: '#eeeeee'});
		$$('div.calendar_cell_09').invoke("setStyle", {backgroundColor: '#ffffff'});
		$$('div.calendar_cell_10').invoke("setStyle", {backgroundColor: '#eeeeee'});
		$$('div.calendar_cell_11').invoke("setStyle", {backgroundColor: '#ffffff'});
		$$('div.calendar_cell_12').invoke("setStyle", {backgroundColor: '#eeeeee'});
		$$('div.calendar_cell_01').invoke("setStyle", {backgroundColor: '#ffffff'});
		$$('div.calendar_cell_02').invoke("setStyle", {backgroundColor: '#eeeeee'});
		$$('div.calendar_cell_03').invoke("setStyle", {backgroundColor: '#ffffff'});
		$$('div.calendar_cell_04').invoke("setStyle", {backgroundColor: '#eeeeee'});
		$$('div.calendar_cell_05').invoke("setStyle", {backgroundColor: '#ffffff'});
		$$('div.calendar_cell_06').invoke("setStyle", {backgroundColor: '#eeeeee'});
		$$('div.calendar_cell_07').invoke("setStyle", {backgroundColor: '#ffffff'});
		//while ($('div'+i)) {
		//	$('div'+i).setStyle({backgroundColor: 'transparent'});	
		//	i++;
		//}
	}
	var mylistener = function(div) {
		$(div).observe('mouseover', function(event) {
			if (MouseDown == 1) {
				if (FirstDiv=='') {
					FirstDiv=div;
					IndiceFirstDiv=parseInt(FirstDiv.substring(3,FirstDiv.length), 10);
					//$('info').update('TEST : '+IndiceFirstDiv);
				}
				NbSelectedDays++;
				IndiceCurrentDiv=parseInt(div.substring(3,div.length), 10);
				cleardiv();
				if (IndiceFirstDiv < IndiceCurrentDiv) {
					for (i=IndiceFirstDiv; i<=IndiceCurrentDiv;i++) {
						$('div'+i).setStyle({backgroundColor: '#95a1ff'});
					}
				}
				else {
					for (i=IndiceFirstDiv; i>=IndiceCurrentDiv;i--) {
						$('div'+i).setStyle({backgroundColor: '#95a1ff'});
					}				
				
				}
			}

		});	
		$(div).observe('mousedown', function(event) {
			MouseDown=1;
				if (FirstDiv =='') {
					FirstDiv=div;
					NbSelectedDays++;
					IndiceFirstDiv=parseInt(FirstDiv.substring(3,FirstDiv.length), 10);
					//$('info').update('TEST : '+IndiceFirstDiv);
				}
				IndiceCurrentDiv=parseInt(div.substring(3,div.length), 10);
				cleardiv();
				NbSelectedDays = 0;
				if (IndiceFirstDiv < IndiceCurrentDiv) {
					for (i=IndiceFirstDiv; i<=IndiceCurrentDiv;i++) {
						NbSelectedDays++;
						$('div'+i).setStyle({backgroundColor: '#95a1ff'});
					}
				}
				else {
					for (i=IndiceFirstDiv; i>=IndiceCurrentDiv;i--) {
						NbSelectedDays++;
						$('div'+i).setStyle({backgroundColor: '#95a1ff'});
					}				
				
				}
		});
		}
	window.onload = function() {

		$(document).observe('mousedown', function(event) {
			MouseDown=1;
			event.preventDefault();	//	empêche la propagation des events
		});
		$(document).observe('mouseup', function(event) {
			MouseDown=0;
			if (NbSelectedDays == 1) {
				cleardiv();
				$(FirstDiv).setStyle({backgroundColor: '#95a1ff'});
			}
			else {
				NbSelectedDays = 0;
				FirstDiv='';
				cleardiv();
			}
		});		
		var i = 1;
		while ($('div'+i)) {
			mylistener('div'+i);	
			i++;
		}
	}
</script>

<style type="text/css">
	.calendar_header{
		float:left;
		border:1px solid #cccccc;
		background-image:url('lib/template/images/entete.jpg');
		background-repeat:repeat-x;
		width:80px;
		height:30px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		text-align:center;
		font-family: arial;
		font-weight:bold;
		color:black;
	}
	.calendar_cell_01 {
		float:left;
		border:1px solid #cccccc;
		background-color:#ffffff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/janvier.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_02 {
		float:left;
		border:1px solid #cccccc;
		background-color:#eeeeee;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/fevrier.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_03 {
		float:left;
		border:1px solid #cccccc;
		background-color:#ffffff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/mars.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_04 {
		float:left;
		border:1px solid #cccccc;
		background-color:#eeeeee;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/avril.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_05 {
		float:left;
		border:1px solid #cccccc;
		background-color:#ffffff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/mai.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_06 {
		float:left;
		border:1px solid #cccccc;
		background-color:#eeeeee;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/juin.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_07 {
		float:left;
		border:1px solid #cccccc;
		background-color:#ffffff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/juillet.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_08 {
		float:left;
		border:1px solid #cccccc;
		background-color:#eeeeee;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/aout.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_09 {
		float:left;
		border:1px solid #cccccc;
		background-color:#ffffff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/septembre.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_10 {
		float:left;
		border:1px solid #cccccc;
		background-color:#eeeeee;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/octobre.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_11 {
		float:left;
		border:1px solid #cccccc;
		background-color:#ffffff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/novembre.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_12 {
		float:left;
		border:1px solid #cccccc;
		background-color:#eeeeee;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/decembre.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_container {
		width:490px;
		margin:30px auto;
	}
</style>
<?php include("./lib/template/mini_calendrier.php"); ?>

<div id="lecorps">

	<?php include("./lib/template/menu_edt.php"); ?>
	<div id="art-main">
        <div class="art-sheet">
            <div class="art-sheet-tl"></div>
            <div class="art-sheet-tr"></div>
            <div class="art-sheet-bl"></div>
            <div class="art-sheet-br"></div>
            <div class="art-sheet-tc"></div>
            <div class="art-sheet-bc"></div>
            <div class="art-sheet-cl"></div>
            <div class="art-sheet-cr"></div>
            <div class="art-sheet-cc"></div>
            <div class="art-sheet-body">
                <div class="art-nav">
                	<div class="l"></div>
                	<div class="r"></div>
					<?php include("menu_top.php"); ?>
                </div>
                <div class="art-content-layout">
                    <div class="art-content-layout-row">
                        <div class="art-layout-cell art-content">
                            <div class="art-post">
                                <div class="art-post-tl"></div>
                                <div class="art-post-tr"></div>
                                <div class="art-post-bl"></div>
                                <div class="art-post-br"></div>
                                <div class="art-post-tc"></div>
                                <div class="art-post-bc"></div>
                                <div class="art-post-cl"></div>
                                <div class="art-post-cr"></div>
                                <div class="art-post-cc"></div>
                                <div class="art-post-body">
									<div class="art-post-inner art-article">
                                            <div class="art-postmetadataheader">
                                                <h2 class="art-postheader">
                                                    Gestion des calendriers
                                                </h2>
                                            </div>
                                            <div class="art-postcontent">
                                                <!-- article-content -->
<div style="font-size:12px;color:white;width:60%;text-align:center;margin:10px auto;padding:10px;background-color:#9571dd;">[En cours de développement] : Permet de créer les périodes calendaires. Principe : On sélectionne avec la souris la plage que l'on souhaite, une popup apparait pour demander le nom de la période calendaire. Tout se fait avac prototype + ajax.</div> 
<div class="calendar_container">
	<div class="calendar_header">
	<div style="margin:3px;">Lun</div>
	</div>
	<div class="calendar_header">
	<div style="margin:3px;">Mar</div>
	</div>
	<div class="calendar_header">
	<div style="margin:3px;">Mer</div>
	</div>
	<div class="calendar_header">
	<div style="margin:3px;">Jeu</div>
	</div>
	<div class="calendar_header">
	<div style="margin:3px;">Ven</div>
	</div>
	<div class="calendar_header">
	<div style="margin:3px;">Sam</div>
	</div>
	<div style="clear:both;"></div>
	<?php echo $Calendrier; ?>		
</div>									


<div id="info" style="position:absolute;top:300px; left:5px;width:60px;height:60px;"></div>

                                                <div class="cleared"></div>
                                                <!-- /article-content -->
                                            </div>
                                            <div class="cleared"></div>
									</div>
								</div>
							</div>
							
						</div>						
                    </div>
                </div>
		<?php include("footer.php"); ?>
    </div>
</div>