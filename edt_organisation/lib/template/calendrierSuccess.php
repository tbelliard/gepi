
<script type="text/javascript">
	var MouseDown = 0;
	var FirstDiv='';
	var IndiceFirstDiv=0;
	var CurrentDiv='';
	var IndiceCurrentDiv=0;
	var NbSelectedDays = 0;
	var CellNotFree = false;
	var TableDaysPeriod = new Array();
	var TableFirstDaysPeriod = new Array();
	// ========================================================================================
	//
	//					Nettoyage de toutes les cellules du calendrier
	//
	// ========================================================================================
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

		$$('div.calendar_cell_period_08').invoke("setStyle", {backgroundColor: '#95a1ff'});
		$$('div.calendar_cell_period_09').invoke("setStyle", {backgroundColor: '#95a1ff'});
		$$('div.calendar_cell_period_10').invoke("setStyle", {backgroundColor: '#95a1ff'});
		$$('div.calendar_cell_period_11').invoke("setStyle", {backgroundColor: '#95a1ff'});
		$$('div.calendar_cell_period_12').invoke("setStyle", {backgroundColor: '#95a1ff'});
		$$('div.calendar_cell_period_01').invoke("setStyle", {backgroundColor: '#95a1ff'});
		$$('div.calendar_cell_period_02').invoke("setStyle", {backgroundColor: '#95a1ff'});
		$$('div.calendar_cell_period_03').invoke("setStyle", {backgroundColor: '#95a1ff'});
		$$('div.calendar_cell_period_04').invoke("setStyle", {backgroundColor: '#95a1ff'});
		$$('div.calendar_cell_period_05').invoke("setStyle", {backgroundColor: '#95a1ff'});
		$$('div.calendar_cell_period_06').invoke("setStyle", {backgroundColor: '#95a1ff'});
		$$('div.calendar_cell_period_07').invoke("setStyle", {backgroundColor: '#95a1ff'});

		$$('div.calendar_first_cell_period_08').invoke("setStyle", {backgroundColor: '#75a1ff'});
		$$('div.calendar_first_cell_period_09').invoke("setStyle", {backgroundColor: '#75a1ff'});
		$$('div.calendar_first_cell_period_10').invoke("setStyle", {backgroundColor: '#75a1ff'});
		$$('div.calendar_first_cell_period_11').invoke("setStyle", {backgroundColor: '#75a1ff'});
		$$('div.calendar_first_cell_period_12').invoke("setStyle", {backgroundColor: '#75a1ff'});
		$$('div.calendar_first_cell_period_01').invoke("setStyle", {backgroundColor: '#75a1ff'});
		$$('div.calendar_first_cell_period_02').invoke("setStyle", {backgroundColor: '#75a1ff'});
		$$('div.calendar_first_cell_period_03').invoke("setStyle", {backgroundColor: '#75a1ff'});
		$$('div.calendar_first_cell_period_04').invoke("setStyle", {backgroundColor: '#75a1ff'});
		$$('div.calendar_first_cell_period_05').invoke("setStyle", {backgroundColor: '#75a1ff'});
		$$('div.calendar_first_cell_period_06').invoke("setStyle", {backgroundColor: '#75a1ff'});
		$$('div.calendar_first_cell_period_07').invoke("setStyle", {backgroundColor: '#75a1ff'});
		while (typeof(TableDaysPeriod[i]) != "undefined") {
			$('div'+TableDaysPeriod[i]).setStyle({backgroundColor: '#95a1ff'});
			i++;
		}
		i=0;
		while (typeof(TableFirstDaysPeriod[i]) != "undefined") {
			$('div'+TableFirstDaysPeriod[i]).setStyle({backgroundColor: '#75a1ff'});
			i++;
		}
	}
	// ========================================================================================
	//
	//					Création des observers mouseover, mousedown, sur chaque cellule
	//
	// ========================================================================================
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
						$('div'+i).setStyle({backgroundColor: '#95c1ff'});
					}
				}
				else {
					for (i=IndiceFirstDiv; i>=IndiceCurrentDiv;i--) {
						$('div'+i).setStyle({backgroundColor: '#95c1ff'});
					}				
				
				}
			}

		});	
		$(div).observe('mousedown', function(event) {
			MouseDown=1;
			event.preventDefault();
				if (FirstDiv =='') {
					if ($(div).getStyle('backgroundColor') == 'rgb(149, 161, 255)') {
						CellNotFree = true;
					}
					else if ($(div).getStyle('backgroundColor') == 'rgb(117, 161, 255)') {
						CellNotFree = true;
					}
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
						$('div'+i).setStyle({backgroundColor: '#95c1ff'});
					}
				}
				else {
					for (i=IndiceFirstDiv; i>=IndiceCurrentDiv;i--) {
						NbSelectedDays++;
						$('div'+i).setStyle({backgroundColor: '#95c1ff'});
					}				
				
				}

				

		});
		}
	// ========================================================================================
	//
	//					Création des observers généraux mousedown, mouseup
	//
	// ========================================================================================
	window.onload = function() {
		
		$(document).observe('mousedown', function(event) {
			MouseDown=1;

		});
		$(document).observe('mouseup', function(event) {
			MouseDown=0;
			if (NbSelectedDays == 1) {
				cleardiv();
				$(FirstDiv).setStyle({backgroundColor: '#95c1ff'});
				if (CellNotFree) {
					var DaySelected = parseInt(FirstDiv.substring(3,FirstDiv.length), 10);	
					CellNotFree = false;
					NbSelectedDays = 0;
					FirstDiv='';
					cleardiv();	
				
					new Ajax.Request(
						'./index.php',
						{
						method:'get',
						parameters: {action: "ajaxrequest", asker: "edit_period", day: DaySelected},
						onSuccess: function(transport){
							var period = transport.responseText.evalJSON();
							if (period)
							{
								$('name_period').value = period[0].name;
								$('start_period').value = period[0].start_date;
								$('end_period').value = period[0].end_date;
								$('periode_notes').value = period[0].periode_notes;
								$('ouvert').value = period[0].ouvert;
								$('type').value = period[0].type;				
							}
							else {
								alert('gloups');								
							}
						},
						onFailure: function(){ alert('Impossible de transmettre votre requête') }
					  });
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
					$('cache_modal').setStyle({width: "100%"});
					$('cache_modal').setStyle({height: height+"px"});
					$('cache_modal').setStyle({top: y+"px"});
					$('cache_modal').setStyle({display: 'block'});
					$('cache_modal').setOpacity(0.6);
					$('edit_period').setStyle({top: y+Math.abs((height-100)/2)+"px"});
					$('edit_period').setStyle({left: Math.abs((width-300)/2)+"px"});
					$('edit_period').setStyle({display: 'block'});				
				
				
				}

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
				$('cache_modal').setStyle({width: "100%"});
				$('cache_modal').setStyle({height: height+"px"});
				$('cache_modal').setStyle({top: y+"px"});
				$('cache_modal').setStyle({display: 'block'});
				$('cache_modal').setOpacity(0.6);
				$('new_period').setStyle({top: y+Math.abs((height-100)/2)+"px"});
				$('new_period').setStyle({left: Math.abs((width-300)/2)+"px"});
				$('new_period').setStyle({display: 'block'});
				$('period_input_field').value='';
				$('period_input_field').focus();
						

				$('period_input_field').observe('keyup', function(event) {
					if (event.keyCode=='13'){  
						$('new_period').setStyle({display: 'none'});	
						$('cache_modal').setStyle({display: 'none'}); 
						if (Prototype.Browser.IE) {
							document.documentElement.scroll = "yes";
							document.documentElement.style.overflow = 'scroll';
						}
						else {
							document.body.scroll = "yes";
							document.body.style.overflow = 'scroll';				
						}
						if ($('period_input_field').value != "") {
							new Ajax.Request(
								'./index.php',
							  {
								method:'get',
								parameters: {action: "ajaxrequest", asker: "calendrier", periodname: $('period_input_field').value, firstday: IndiceFirstDiv, lastday: IndiceCurrentDiv},
								onSuccess: function(transport){
									var response = transport.responseText || "Le serveur ne répond pas";
									var message = response.substring(0,5);
									if (message == "error") {
										message = response.substring(5,response.length);
										$('message').update(message);
										Effect.ScrollTo($('bandeau'),{ duration:'0.2'});
										Effect.Appear($('message'));
										Effect.Shake($('message'),{ distance: 30, duration:'1.5'});
									}
									else {
										$('message').setStyle({display: "none"});
										if (IndiceFirstDiv < IndiceCurrentDiv) {
											firstday = IndiceFirstDiv;
											lastday = IndiceCurrentDiv;
										}
										else {
											lastday = IndiceFirstDiv;
											firstday = IndiceCurrentDiv;										
										}
										$('div'+firstday).setStyle({backgroundColor: '#75a1ff'});
										TableFirstDaysPeriod.push(firstday);
										TableDaysPeriod.push(firstday);
										for (i=firstday+1;i<=lastday;i++) {
											$('div'+i).setStyle({backgroundColor: '#95a1ff'});	
											TableDaysPeriod.push(i);
										}										
									}
								},
								onFailure: function(){ alert('Impossible de transmettre votre requête') }
							  });

							  $('period_input_field').value = "";
						}
						NbSelectedDays = 0;
						FirstDiv='';
						cleardiv();
  					}
					else if (event.keyCode=='27'){  
						$('new_period').setStyle({display: 'none'});	
						$('cache_modal').setStyle({display: 'none'}); 
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
		$('bouton_annuler').observe('mouseup', function(event) {
			$('edit_period').setStyle({display: 'none'});	
			$('cache_modal').setStyle({display: 'none'}); 
			if (Prototype.Browser.IE) {
				document.documentElement.scroll = "yes";
				document.documentElement.style.overflow = 'scroll';
			}
			else {
				document.body.scroll = "yes";
				document.body.style.overflow = 'scroll';				
			}
		});
		
	}
</script>

<?php include("./lib/template/mini_calendrier.php"); ?>


<div id="lecorps">

	<?php include("./lib/template/menu_edt.php"); ?>
	<div id="message" style="color:white;text-align:center;display:none;width:60%;margin:20px auto;padding:5px;background-color:#95a1ff;"></div>
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
<div id="cache_modal" style="display:none;position:absolute;top:0px;left:0px;background-color:#000000;width:200px;height:200px;"> &nbsp;</div>
<div id="new_period" style="width:330px;height:156px;display:none;position:absolute;
		background-image:url('lib/template/images/popup.png');
		background-repeat:no-repeat;"> 
	<p id="label_period" style="padding-left:40px;padding-top:50px;text-align:left;">Entrez le nom de la nouvelle période</p>
	<p style="padding:10px;padding-left:50px;"><input id="period_input_field" style="width:200px;" type="text"/></p> 
</div>
<div id="edit_period" style="width:481px;height:332px;display:none;position:absolute;padding-top:50px;padding-left:10px;
		background-image:url('lib/template/images/popup2.png');
		background-repeat:no-repeat;"> 
	<p><span>Nom période :</span><span> <input id="name_period" style="width:200px;" type="text"/></span></p>
	<p><span>Début période :</span><span> <input id="start_period" style="width:200px;" type="text"/></span></p> 
	<p><span>Fin période :</span><span> <input id="end_period" style="width:200px;" type="text"/></span></p> 
	<p><span>Période de note associée :</span><span> <input id="periode_notes" style="width:200px;" type="text"/></span></p> 
	<p><span>Etablissement ouvert ?</span><span> <input id="ouvert" style="width:200px;" type="text"/></span></p> 	
	<p><span>Type de période : </span><span> <input id="type" style="width:200px;" type="text"/></span></p> 
	<p style="padding-top:40px;"><span><img id="bouton_supprimer" alt="supprimer" src="lib/template/images/bouton_supprimer.png" /></span><span><img id="bouton_valider" alt="valider" src="lib/template/images/bouton_valider.png" /></span><span><img id="bouton_annuler" alt="annuler" src="lib/template/images/bouton_annuler.png" /></span></p>
</div>