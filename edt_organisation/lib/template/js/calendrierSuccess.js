	var MouseDown = 0;
	var FirstDiv='';
	var IndiceFirstDiv=0;
	var CurrentDiv='';
	var IndiceCurrentDiv=0;
	var NbSelectedDays = 0;
	var CellNotFree = false;
	// ========================================================================================
	//
	//					Valider les modifications d'une période calendaire
	//
	// ========================================================================================
	var validate_period = function() {
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

		new Ajax.Request(
			'./index.php',
			{
			method:'get',
			parameters: {action: "ajaxrequest", asker: "validate_period", 
						id_period: $('id_period').value,
						name_period:$('name_period').value,
						start_period:$('start_period').value,
						end_period:$('end_period').value,
						periode_notes:($$('select#periodes_notes option').find(function(ele){return ele.selected})).value,
						ouvert:$('ouvert').value,
						type:$('type').value,
						num_jour_initial:$('num_jour_initial').value,
						num_jour_final:$('num_jour_final').value,
						csrf_alea:$('csrf_alea').value
						},
			onSuccess: function(transport){
				var period = transport.responseText.evalJSON();

				if (period)
				{

					if  (period[0].code == "success")
					{ 
						var i = parseInt(period[0].old_start,10);
						while (i <= parseInt(period[0].old_end,10)) {
							$('div'+i).className = 'calendar_cell';
							i++;
						}
						// ======= afficher la période dans son nouvel état
						//

						var i = parseInt(period[0].new_start,10);
						while (i < parseInt(period[0].new_end,10)) {
							$('div'+i).className = 'calendar_cell_period';
							i++;
						}
						repaint_cells();
					// ======= effacer la période dans son état initial


					}
					else
					{
						message = period[0].message;
						$('message').update(message);
						Effect.ScrollTo($('bandeau'),{ duration:'0.2'});
						Effect.Appear($('message'));
						Effect.Shake($('message'),{ distance: 30, duration:'1.5'});					
					}
				}
				else {
					alert('gloups');								
				}
			},
			onFailure: function(){ alert('Impossible de transmettre votre requête') }
		  });	
	
	}
	// ========================================================================================
	//
	//					Edition d'une période du calendrier
	//
	// ========================================================================================
	var edit_period = function() {
		var DaySelected = parseInt(FirstDiv.substring(3,FirstDiv.length), 10);	
		CellNotFree = false;
		NbSelectedDays = 0;
		FirstDiv='';
		repaint_cells();	
	
		new Ajax.Request(
			'./index.php',
			{
			method:'get',
			parameters: {action: "ajaxrequest",
						asker: "edit_period",
						day: DaySelected,
						id_calendar:$('id_calendar').value,
						csrf_alea:$('csrf_alea').value},
			onSuccess: function(transport){
				var period = transport.responseText.evalJSON();
				if (period)
				{
					$('id_period').value = period[0].id;
					$('name_period').value = period[0].name;
					$('start_period').value = period[0].start_date;
					$('end_period').value = period[0].end_date;
					$$('select#periodes_notes option').each(function(o) {
						o.selected = o.readAttribute('value') == period[0].periode_notes;
					});

					$('ouvert').value = period[0].ouvert;
					$('type').value = period[0].type;				
					$('num_jour_initial').value = period[0].num_jour_initial;
					$('num_jour_final').value = period[0].num_jour_final;
					if ($('ouvert').value == 1) {
						$('checkbox_open').setAttribute("src", "./lib/template/images/checked.gif");
						$('checkbox_close').setAttribute("src", "./lib/template/images/unchecked.gif");
					}
					else {
						$('checkbox_open').setAttribute("src", "./lib/template/images/unchecked.gif");
						$('checkbox_close').setAttribute("src", "./lib/template/images/checked.gif");					
					}
					if ($('type').value == 0) {
						$('checkbox_cours').setAttribute("src", "./lib/template/images/checked.gif");
						$('checkbox_vacances').setAttribute("src", "./lib/template/images/unchecked.gif");
					}
					else {
						$('checkbox_cours').setAttribute("src", "./lib/template/images/unchecked.gif");
						$('checkbox_vacances').setAttribute("src", "./lib/template/images/checked.gif");					
					}
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
		$('edit_period').setStyle({top: y+Math.abs((height-332)/2)+"px"});
		$('edit_period').setStyle({left: Math.abs((width-481)/2)+"px"});
		$('edit_period').setStyle({display: 'block'});		
	
	
	
	}
	// ========================================================================================
	//
	//					Suppression d'une période du calendrier
	//
	// ========================================================================================
	var delete_period = function() {
		
		new Ajax.Request(
			'./index.php',
		  {
			method:'get',
			parameters: {action: "ajaxrequest", 
						asker: "delete_period", 
						periodid: $('id_period').value,
						csrf_alea:$('csrf_alea').value},
			onSuccess: function(transport){
			
				var info = transport.responseText.evalJSON();
				if (info)
				{
					var i = 0;
					for (i = parseInt($('num_jour_initial').value,10);i <= parseInt($('num_jour_final').value,10);i++) {
						$('div'+i).className = "calendar_cell";
					}
					repaint_cells();
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
				}
				else {
					alert('gloups');								
				}
			},
			onFailure: function(){ alert('Impossible de transmettre votre requête') }
		  });
	
	
	
	}
	// ========================================================================================
	//
	//					Nettoyage de toutes les cellules du calendrier
	//
	// ========================================================================================
	var repaint_cells = function() {

		$$('div.month_08').each(function(s){$(s).up().setStyle({backgroundColor: '#eeeeee'});});
		$$('div.month_09').each(function(s){$(s).up().setStyle({backgroundColor: '#ffffff'});});
		$$('div.month_10').each(function(s){$(s).up().setStyle({backgroundColor: '#eeeeee'});});
		$$('div.month_11').each(function(s){$(s).up().setStyle({backgroundColor: '#ffffff'});});
		$$('div.month_12').each(function(s){$(s).up().setStyle({backgroundColor: '#eeeeee'});});
		$$('div.month_01').each(function(s){$(s).up().setStyle({backgroundColor: '#ffffff'});});
		$$('div.month_02').each(function(s){$(s).up().setStyle({backgroundColor: '#eeeeee'});});	
		$$('div.month_03').each(function(s){$(s).up().setStyle({backgroundColor: '#ffffff'});});
		$$('div.month_04').each(function(s){$(s).up().setStyle({backgroundColor: '#eeeeee'});});	
		$$('div.month_05').each(function(s){$(s).up().setStyle({backgroundColor: '#ffffff'});});	
		$$('div.month_06').each(function(s){$(s).up().setStyle({backgroundColor: '#eeeeee'});});
		$$('div.month_07').each(function(s){$(s).up().setStyle({backgroundColor: '#ffffff'});});
		
		$$('div.calendar_cell_period').invoke("setStyle", {backgroundColor: '#95a1ff'});
		$$('div.calendar_first_cell_period').invoke("setStyle", {backgroundColor: '#75a1ff'});
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
				}
				NbSelectedDays++;
				IndiceCurrentDiv=parseInt(div.substring(3,div.length), 10);
				repaint_cells();
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
				repaint_cells();
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
				repaint_cells();
				$(FirstDiv).setStyle({backgroundColor: '#95c1ff'});
				if (CellNotFree) {
					edit_period();				
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
				$('params_new_period').setStyle({top: 170+y+Math.abs((height-100)/2)+"px"});
				$('params_new_period').setStyle({left: Math.abs((width-300)/2)+"px"});
				$('period_input_field').value='';
				$('period_input_field').focus();
						

				$('period_input_field').observe('keyup', function(event) {
					if (event.keyCode=='13'){  
						$('new_period').setStyle({display: 'none'});	
						$('cache_modal').setStyle({display: 'none'}); 
						$('params_new_period').setStyle({display: 'none'});

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
								parameters: {action: "ajaxrequest", 
											asker: "calendrier", 
											periodname: $('period_input_field').value, 
											firstday: IndiceFirstDiv, 
											lastday: IndiceCurrentDiv, 
											id_calendar: $('id_calendar').value,
											periode_notes:($$('select#params_periodes_notes option').find(function(ele){return ele.selected})).value,
											ouvert:$('params_ouvert').value,
											type:$('params_type').value,
											csrf_alea:$('csrf_alea').value},
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
										$('div'+firstday).className = "calendar_first_cell_period";
										for (i=firstday+1;i<=lastday;i++) {
											$('div'+i).setStyle({backgroundColor: '#95a1ff'});	
											$('div'+i).className = "calendar_cell_period";
										}										
									}
								},
								onFailure: function(){ alert('Impossible de transmettre votre requête') }
							  });

							  $('period_input_field').value = "";
						}
						$$('select#params_periodes_notes option').each(function(o) {
							o.selected = o.readAttribute('value') == "0";
						});
						$('params_ouvert').value = "1";
						$('params_checkbox_open').setAttribute("src", "./lib/template/images/checked.gif");
						$('params_checkbox_close').setAttribute("src", "./lib/template/images/unchecked.gif");
						$('params_type').value = "0";
						$('params_checkbox_cours').setAttribute("src", "./lib/template/images/checked.gif");
						$('params_checkbox_vacances').setAttribute("src", "./lib/template/images/unchecked.gif");
						NbSelectedDays = 0;
						FirstDiv='';
						repaint_cells();
  					}
					else if (event.keyCode=='27'){  
						$('new_period').setStyle({display: 'none'});	
						$('cache_modal').setStyle({display: 'none'}); 
						$('params_new_period').setStyle({display: 'none'});
						$$('select#params_periodes_notes option').each(function(o) {
							o.selected = o.readAttribute('value') == "0";
						});
						$('params_ouvert').value = "1";
						$('params_checkbox_open').setAttribute("src", "./lib/template/images/checked.gif");
						$('params_checkbox_close').setAttribute("src", "./lib/template/images/unchecked.gif");
						$('params_type').value = "0";
						$('params_checkbox_cours').setAttribute("src", "./lib/template/images/checked.gif");
						$('params_checkbox_vacances').setAttribute("src", "./lib/template/images/unchecked.gif");
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
						repaint_cells();
  					}
				});
			}
			else {
				NbSelectedDays = 0;
				FirstDiv='';
				repaint_cells();
			}
		});		
		var i = 1;
		while ($('div'+i)) {
			mylistener('div'+i);	
			i++;
		}
		repaint_cells();
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
			$('id_period').value = "";
			$('name_period').value = "";
			$('start_period').value = "";
			$('end_period').value = "";
			$('ouvert').value = "";
			$('type').value = "";	
		});
		$('bouton_supprimer').observe('mouseup', function(event) {
			delete_period();
		});
		$('bouton_valider').observe('mouseup', function(event) {
			validate_period();
		});		
		$('checkbox_open').observe('click', function(event) {
			$('ouvert').value = "1";
			$('checkbox_open').setAttribute("src", "./lib/template/images/checked.gif");
			$('checkbox_close').setAttribute("src", "./lib/template/images/unchecked.gif");
		});
		$('checkbox_close').observe('click', function(event) {
			$('ouvert').value = "2";
			$('checkbox_open').setAttribute("src", "./lib/template/images/unchecked.gif");
			$('checkbox_close').setAttribute("src", "./lib/template/images/checked.gif");
		});
		$('checkbox_cours').observe('click', function(event) {
			$('type').value = "0";
			$('checkbox_cours').setAttribute("src", "./lib/template/images/checked.gif");
			$('checkbox_vacances').setAttribute("src", "./lib/template/images/unchecked.gif");
		});
		$('checkbox_vacances').observe('click', function(event) {
			$('type').value = "1";
			$('checkbox_cours').setAttribute("src", "./lib/template/images/unchecked.gif");
			$('checkbox_vacances').setAttribute("src", "./lib/template/images/checked.gif");
		});

		
		$('params_checkbox_open').observe('click', function(event) {
			$('params_ouvert').value = "1";
			$('params_checkbox_open').setAttribute("src", "./lib/template/images/checked.gif");
			$('params_checkbox_close').setAttribute("src", "./lib/template/images/unchecked.gif");
		});
		$('params_checkbox_close').observe('click', function(event) {
			$('params_ouvert').value = "2";
			$('params_checkbox_open').setAttribute("src", "./lib/template/images/unchecked.gif");
			$('params_checkbox_close').setAttribute("src", "./lib/template/images/checked.gif");
		});
		$('params_checkbox_cours').observe('click', function(event) {
			$('params_type').value = "0";
			$('params_checkbox_cours').setAttribute("src", "./lib/template/images/checked.gif");
			$('params_checkbox_vacances').setAttribute("src", "./lib/template/images/unchecked.gif");
		});
		$('params_checkbox_vacances').observe('click', function(event) {
			$('params_type').value = "1";
			$('params_checkbox_cours').setAttribute("src", "./lib/template/images/unchecked.gif");
			$('params_checkbox_vacances').setAttribute("src", "./lib/template/images/checked.gif");
		});
		$('bouton_params').observe('click', function(event) {
			var viewport = document.viewport.getDimensions(); // Gets the viewport as an object literal
			var width = viewport.width; // Usable window width
			var height = viewport.height; // Usable window height
			if( typeof( window.pageYOffset ) == 'number' ) 
				{y = window.pageYOffset;}
			else if (typeof(document.documentElement.scrollTop) == 'number') {
				y=document.documentElement.scrollTop;
			}
			$('params_new_period').setStyle({top: 170+y+Math.abs((height-100)/2)+"px"});
			$('params_new_period').setStyle({left: Math.abs((width-300)/2)+"px"});
			if ($('params_new_period').getStyle('display') == 'none') {
				$('params_new_period').appear();
			}
			else {
				$('params_new_period').setStyle({display: 'none'});
			}
		});
	}