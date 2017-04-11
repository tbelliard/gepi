/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {

	config.toolbar = [ 
		['Source'],
		['Cut','Copy','Paste','PasteText','-','Undo','Redo'],
		['Scayt'],
		['Bold','Italic','Underline','Strike','Subscript','Superscript','-','SpecialChar','EqnEditor','-','RemoveFormat'],
		['Link','Unlink'],
		['Image','HorizontalRule','Smiley'],
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

	// Désactiver ACF
	// http://docs.ckeditor.com/#!/guide/dev_acf
	// http://sdk.ckeditor.com/samples/acf.html
	config.allowedContent = true;

};
