/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {

	config.toolbar = [ 
		['Source'],
		['Cut','Copy','Paste','PasteText','-','Undo','Redo'],
		['Bold','Italic','Underline','Strike','Subscript','Superscript','-','SpecialChar','EqnEditor','-','RemoveFormat'],
		['Link','Unlink'],
		['Image','HorizontalRule','Smiley'],
		['Table'],
		['oembed'],
		'/',
		[ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		[ 'Styles','Format','Font','FontSize'],
		['TextColor','BGColor'],
		['About']
	];

	config.stylesSet = [
		/* Block Styles */
		{ name : 'Titre bleu'		, element : 'h3', styles : { 'color' : 'Blue' } },
		{ name : 'Titre rouge'		, element : 'h3', styles : { 'color' : 'Red' } },
		/* Inline Styles */
		{ name : 'Surligné jaune'	, element : 'span', styles : { 'background-color' : 'Yellow' } },
		{ name : 'Surligné vert'	, element : 'span', styles : { 'background-color' : 'Lime' } },
		{ name : 'Gros'				, element : 'big' },
		{ name : 'Petit'			, element : 'small' },
		{ name : 'Italique'			, element : 'var' },
		{ name : 'Barré'		, element : 'del' },
		{ name : 'Surligné'		, element : 'ins' },
		{ name : 'Guillemets'	, element : 'q' },
		];

	config.font_names = 'Helvetica/Helvetica, Arial, "Liberation Sans", FreeSans, sans-serif; "DejaVu Sans"/"DejaVu Sans", Verdana, Geneva, sans-serif; "Times New Roman"/"Times New Roman", Times, serif; Courier/Courier, "Courier New", "Liberation Mono", FreeMono, monospace; Impact/Impact, Charcoal, sans-serif; Cursive/Cursive, "Comic Sans", serif;';

	config.format_tags = 'p;h1;h2;h3;h4;h5;h6;pre';

	// Ajout de FF0000,00FF00,0000FF,FFFF00,000080,008080
	CKEDITOR.config.colorButton_colors="FF0000,00FF00,0000FF,FFFF00,000080,008080,1ABC9C,2ECC71,3498DB,9B59B6,4E5F70,F1C40F,16A085,27AE60,2980B9,8E44AD,2C3E50,F39C12,E67E22,E74C3C,ECF0F1,95A5A6,DDD,FFF,D35400,C0392B,BDC3C7,7F8C8D,999,000";

	// Désactiver ACF
	// http://docs.ckeditor.com/#!/guide/dev_acf
	// http://sdk.ckeditor.com/samples/acf.html
	config.allowedContent = true;

	// Filtrage copier-coller
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-pasteFilter
	//config.pasteFilter = 'semantic-content';

	//http://ckeditor.com/forums/CKEditor/Enabling-browser-spellcheck-in-CKEditor-4.4.3
	// Pour réactiver le correcteur orthographique du navigateur il faut :
	// 1. réactiver le correcteur
	config.disableNativeSpellChecker = false;
	// 2. désactiver les plugins qui utlisent le menu contextuel (sinon pas de liste de suggestions)
	config.removePlugins = 'tabletools,contextmenu';

	// Activation des plugins CKEditor-oEmbed-Plugin,embed et autoembed
	config.extraPlugins = 'oembed,embed,autoembed';
	
	// Pour forcer le protocole http
	// config.embed_provider = 'http://ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}';
	config.embed_provider = 'http://noembed.com/embed?maxwidth=200&url={url}&callback={callback}';
};
