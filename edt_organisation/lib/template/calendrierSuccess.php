
<script type="text/javascript" src="./lib/template/js/calendrierSuccess.js"></script>
<?php echo add_token_field(true); ?>
<?php include("./lib/template/mini_calendrier.php"); ?>


<div id="lecorps">

	<?php include("./lib/template/menu_edt.php"); ?>
	<?php if ($message) { ?>	
	<div id="alert_message" style="color:white;text-align:center;width:60%;margin:20px auto;padding:5px;background-color:#95a1ff;"><?php echo $message; ?>
	</div>
	<?php } ?>
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
                                            <div class="art-postcontent">
                                                <!-- article-content -->
												<div style="font-size:12px;color:white;width:80%;text-align:center;margin:10px auto;padding:10px;background-color:#9571dd;">[Gestionnaire de périodes calendaires] : Définissez ici vos différentes périodes calendaires en cliquant directement dans le calendrier. Cliquez sur une période calendaire déjà existante pour l'éditer.</div> 
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
												<div class="fleche_gauche">
													<a href="index.php?action=calendriermanager">
													<img src="./lib/template/images/fleche_gauche.png" alt="retour aux calendriers" title="retour aux calendriers"/>
													</a>
												</div>
                                                <h2 class="art-postheader">
                                                    <?php echo $nom_calendrier; ?>
                                                </h2>
                                            </div>
                                            <div class="art-postcontent">
                                                <!-- article-content -->
												<div style="padding:10px;margin:0px;position:relative;width:100%;">
												<img style="display:block;padding:0px;margin:0 auto;border:0px;" src="./lib/template/images/bouton_vacances.png" alt="générer les périodes de vacances" />
												</div>
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
<div id="new_period" class="new_period"> 
	<p id="label_period" style="padding-left:40px;padding-top:40px;text-align:left;">Entrez le nom de la nouvelle période</p>
	<p style="padding-top:10px;padding-left:50px;"><input id="period_input_field" style="width:200px;" type="text"/></p> 
	<p style="padding-left:10px;padding-top:10px;"><img id="bouton_params" src="./lib/template/images/bouton_params.png" /></p>
</div>
<div id="params_new_period" style="padding:5px;
									text-align:left;
									position:absolute;
									display:none;
									background-color:white;
									border:1px solid black;
									width:290px;
									background-image:url('./lib/template/images/degrade_noir.png');
									background-repeat:repeat-x;
									background-position:left bottom;">
	<?php if ($periodes_notes_autorisees) { ?> 
	<p>Période de notes associée :
			<select id="params_periodes_notes" style="width:110px;" >
			<?php echo $liste_periodes; ?>
			</select>
		</p>
	
	<?php } else { ?>
	<p>Période de notes associée :
		
			<select disabled id="params_periodes_notes" style="width:130px;" >
				<option value="0">aucune période</option>
			</select>
		</p>
	 	
	<?php } ?>
	<p>	<span>Etablissement ouvert ?</span>
		<span> <input id="params_ouvert"  type="hidden"/></span>
		<span style="padding-left:30px;"> <img id="params_checkbox_open" src="./lib/template/images/checked.gif" /></span><span style="padding-left:5px;"> oui </span>
		<span style="padding-left:30px;"> <img id="params_checkbox_close" src="./lib/template/images/unchecked.gif" /></span><span style="padding-left:5px;"> non </span>
	</p> 	
	<p style="padding-bottom:20px;">
		<span>Type de période : </span>
		<span> <input id="params_type" type="hidden"/></span>
		<span style="padding-left:20px;"> <img id="params_checkbox_cours" src="./lib/template/images/checked.gif" /></span><span style="padding-left:5px;"> cours </span>
		<span style="padding-left:20px;"> <img id="params_checkbox_vacances" src="./lib/template/images/unchecked.gif" /></span><span style="padding-left:5px;"> vacances </span>
	</p> 
	</p> 
	<!-- <p style="padding-top:10px;">
		<span><img id="params_bouton_valider" alt="valider" src="lib/template/images/params_bouton_valider.png" /></span>
	</p> -->
</div>
<div id="edit_period" class="edit_period"> 
	<input type="hidden" id="id_period" />
	<input type="hidden" id="num_jour_initial" />
	<input type="hidden" id="num_jour_final" />
	<p><span>Nom de la période :</span><span> <input id="name_period" style="width:200px;" type="text"/></span></p>
	<p><span>Début de la période :</span><span> <input id="start_period" style="width:200px;" type="text"/></span></p> 
	<p><span>Fin de la période :</span><span> <input id="end_period" style="width:200px;" type="text"/></span></p> 
	<?php if ($periodes_notes_autorisees) { ?> 
	<p><span>Période de notes associée :</span>
		<span> 
			<select id="periodes_notes" style="width:200px;" >
			<?php echo $liste_periodes; ?>
			</select>
		</span>
	</p> 
	<?php } else { ?>
	<p><span>Période de notes associée :</span>
		<span> 
			<select disabled id="periodes_notes" style="width:200px;" >
				<option value="0">aucune période</option>
			</select>
		</span>
	</p> 	
	<?php } ?>
	<p>	<span>Etablissement ouvert ?</span>
		<span> <input id="ouvert"  type="hidden"/></span>
		<span style="padding-left:30px;"> <img id="checkbox_open" src="./lib/template/images/checked.gif" /></span><span style="padding-left:5px;"> oui </span>
		<span style="padding-left:30px;"> <img id="checkbox_close" src="./lib/template/images/unchecked.gif" /></span><span style="padding-left:5px;"> non </span>
	</p> 	
	<p>	<span>Type de période : </span>
		<span> <input id="type" type="hidden"/></span>
		<span style="padding-left:30px;"> <img id="checkbox_cours" src="./lib/template/images/checked.gif" /></span><span style="padding-left:5px;"> cours </span>
		<span style="padding-left:30px;"> <img id="checkbox_vacances" src="./lib/template/images/unchecked.gif" /></span><span style="padding-left:5px;"> vacances </span>
	</p> 
	</p> 
	<p style="padding-top:40px;">
		<span><img id="bouton_supprimer" alt="supprimer" src="lib/template/images/bouton_supprimer.png" /></span>
		<span><img id="bouton_valider" alt="valider" src="lib/template/images/bouton_valider.png" /></span>
		<span><img id="bouton_annuler" alt="annuler" src="lib/template/images/bouton_annuler.png" /></span>
	</p>
</div>