
<?php include("./lib/template/mini_calendrier.php"); ?>


<div id="lecorps">

	<?php include("./lib/template/menu_edt.php"); ?>
	<?php if ($delete_confirmation) { ?>
	<div style="margin:0px;width:100%;position:relative;">
	<div style="position:relative;padding-top:35px;padding-left:40px;margin:0 auto;width:330px;height:126px;
		background-image:url('lib/template/images/popup_question.png'); 
		background-repeat:no-repeat;">
		<div style="width:200px;">
	<?php echo $delete_confirmation; ?>
	</div>
	</div>
	</div>
	<?php } ?>
	<?php if ($new_name) { ?>
	<div style="margin:0px;width:100%;position:relative;">
	<div style="position:relative;padding-top:60px;padding-left:40px;margin:0 auto;width:330px;height:106px;
		background-image:url('lib/template/images/popup.png'); 
		background-repeat:no-repeat;">
	<?php echo $new_name; ?>
	</div>
	</div>
	<?php } ?>
	<?php if ($message) { ?>	
	<div id="message" style="color:white;text-align:center;display:none;width:60%;margin:20px auto;padding:5px;background-color:#95a1ff;"><?php echo $message; ?>

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
												<div style="font-size:12px;color:white;width:80%;text-align:center;margin:10px auto;padding:10px;background-color:#9571dd;">[Gestionnaire de calendriers] : Définissez ici vos différents calendriers auxquels vous associez les classes concernées.</div> 
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
                                                    liste des calendriers
                                                </h2>
                                            </div>
                                            <div class="art-postcontent">
                                                <!-- article-content -->
												<?php echo $calendrier; ?>

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
                                            <div class="art-postcontent">
                                                <!-- article-content -->
												<div style="text-align:center;">
												<form id="add_calendar" action="index.php?action=calendriermanager" enctype="multipart/form-data" method="post">
												<fieldset>
													<?php echo add_token_field(false); ?>
													<input name="operation" type="hidden" value="new">
													<input name="nom_calendrier" style="width:200px;" type="text" title="Entrez le nom du nouveau calendrier">
													<input name="bouton_submit" type="submit" value="Créer un nouveau calendrier">
												</fieldset>
												</form>
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
