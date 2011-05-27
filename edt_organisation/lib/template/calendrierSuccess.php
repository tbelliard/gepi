
<script type="text/javascript">
	var MouseDown = 0;
	var FirstDiv='';
	var IndiceFirstDiv=0;
	var CurrentDiv='';
	var IndiceCurrentDiv=0;
	var NbSelectedDays = 0;
	var TableDaysPeriod = new Array();
	var cleardiv = function() {
		var i = 0;
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
		while (typeof(TableDaysPeriod[i]) != "undefined") {
			$('div'+TableDaysPeriod[i]).setStyle({backgroundColor: '#95a1ff'});
			i++;
		}
	}
	var mylistener = function(div) {
		$(div).observe('mouseover', function(event) {
		event.preventDefault();
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
			event.preventDefault();
				if (FirstDiv =='') {
					FirstDiv=div;
					NbSelectedDays++;
					IndiceFirstDiv=parseInt(FirstDiv.substring(3,FirstDiv.length), 10);
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

		});
		$(document).observe('mouseup', function(event) {
			MouseDown=0;
			if (NbSelectedDays == 1) {
				cleardiv();
				$(FirstDiv).setStyle({backgroundColor: '#95a1ff'});
			}
			else if (NbSelectedDays > 1){

				if (Prototype.Browser.IE) {
					document.documentElement.scroll = "no";
					document.documentElement.style.overflow = 'hidden';
				}
				else {
					document.body.scroll = "no";
					document.body.style.overflow = 'hidden';				
				}
				var viewport = document.viewport.getDimensions(); // Gets the viewport as an object literal
				var width = viewport.width; // Usable window width
				var height = viewport.height; // Usable window height
				if( typeof( window.pageYOffset ) == 'number' ) 
					{y = window.pageYOffset;}
				else if (typeof(document.documentElement.scrollTop) == 'number') {
					y=document.documentElement.scrollTop;
				}
				$('login_fond').setStyle({width: "100%"});
				$('login_fond').setStyle({height: height+"px"});
				$('login_fond').setStyle({top: y+"px"});
				$('login_fond').setStyle({display: 'block'});
				$('login_fond').setOpacity(0.6);
				$('login').setStyle({top: y+Math.abs((height-100)/2)+"px"});
				$('login').setStyle({left: Math.abs((width-300)/2)+"px"});
				$('login').setStyle({display: 'block'});
				$('login_input_field').value='';
				$('login_input_field').focus();
						

				$('login_input_field').observe('keyup', function(event) {
					if (event.keyCode=='13'){  
						$('login').setStyle({display: 'none'});	
						$('login_fond').setStyle({display: 'none'}); 
						if (Prototype.Browser.IE) {
							document.documentElement.scroll = "yes";
							document.documentElement.style.overflow = 'scroll';
						}
						else {
							document.body.scroll = "yes";
							document.body.style.overflow = 'scroll';				
						}
						if ($('login_input_field').value != "") {
							new Ajax.Request(
								'./index.php',
							  {
								method:'get',
								parameters: {action: "ajaxrequest", asker: "calendrier", periodname: $('login_input_field').value, firstday: IndiceFirstDiv, lastday: IndiceCurrentDiv},
								onSuccess: function(transport){
									var response = transport.responseText || "Le serveur ne répond pas";
									var message = response.substring(0,5);
									if (message == "error") {
										message = response.substring(5,response.length);
										$('message').setStyle({display: "block"});
										$('message').update(message);
									}
									else {
										if (IndiceFirstDiv < IndiceCurrentDiv) {
											firstday = IndiceFirstDiv;
											lastday = IndiceCurrentDiv;
										}
										else {
											lastday = IndiceFirstDiv;
											firstday = IndiceCurrentDiv;										
										}
										for (i=firstday;i<=lastday;i++) {
											$('div'+i).setStyle({backgroundColor: '#95a1ff'});	
											TableDaysPeriod.push(i);
										}										
									}
								},
								onFailure: function(){ alert('Impossible de transmettre votre requête') }
							  });

							  $('login_input_field').value = "";
						}
						NbSelectedDays = 0;
						FirstDiv='';
						cleardiv();
  					}
					else if (event.keyCode=='27'){  
						$('login').setStyle({display: 'none'});	
						$('login_fond').setStyle({display: 'none'}); 
						if (Prototype.Browser.IE) {
							document.documentElement.scroll = "yes";
							document.documentElement.style.overflow = 'scroll';
						}
						else {
							document.body.scroll = "yes";
							document.body.style.overflow = 'scroll';				
						}
						NbSelectedDays = 0;
						FirstDiv='';
						cleardiv();
  					}
				});
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
	.calendar_cell_period_01 {
		float:left;
		border:1px solid #cccccc;
		background-color:#95a1ff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/janvier.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_period_02 {
		float:left;
		border:1px solid #cccccc;
		background-color:#95a1ff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/fevrier.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_period_03 {
		float:left;
		border:1px solid #cccccc;
		background-color:#95a1ff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/mars.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_period_04 {
		float:left;
		border:1px solid #cccccc;
		background-color:#95a1ff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/avril.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_period_05 {
		float:left;
		border:1px solid #cccccc;
		background-color:#95a1ff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/mai.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_period_06 {
		float:left;
		border:1px solid #cccccc;
		background-color:#95a1ff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/juin.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_period_07 {
		float:left;
		border:1px solid #cccccc;
		background-color:#95a1ff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/juillet.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_period_08 {
		float:left;
		border:1px solid #cccccc;
		background-color:#95a1ff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/aout.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_period_09 {
		float:left;
		border:1px solid #cccccc;
		background-color:#95a1ff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/septembre.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_period_10 {
		float:left;
		border:1px solid #cccccc;
		background-color:#95a1ff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/octobre.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_period_11 {
		float:left;
		border:1px solid #cccccc;
		background-color:#95a1ff;
		width:80px;
		height:40px;
		margin-left:-1px;
		margin-top:-1px;
		padding:0px;
		background-image:url('lib/template/images/novembre.png');
		background-repeat:none;
		font-size:12px;
	}
	.calendar_cell_period_12 {
		float:left;
		border:1px solid #cccccc;
		background-color:#95a1ff;
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
	<div id="message" style="display:none;width:60%;background-color:#dddddd;"></div>
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
                                                <div class="cleared"></div>
                                                <!-- /article-content -->
                                            </div>
                                            <div class="cleared"></div>
									</div>
								</div>
							</div>
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
                                                    Calendrier 1
                                                </h2>
                                            </div>
                                            <div class="art-postcontent">
                                                <!-- article-content -->
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
													<div id="complete_calendar"><?php echo $Calendrier; ?></div>		
												</div>									


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
<div id="login_fond" style="display:none;position:absolute;top:0px;left:0px;background-color:#000000;width:200px;height:200px;"> &nbsp;
</div>
<div id="login" style="width:330px;height:156px;display:none;position:absolute;
		background-image:url('lib/template/images/popup.png');
		background-repeat:none;"> 
	<p id="login_message" style="padding-left:40px;padding-top:50px;text-align:left;">Entrez le nom de la nouvelle période</p>
	<p style="padding:10px;padding-left:50px;"><input id="login_input_field" style="width:200px;" type="text"/></p> 
</div>