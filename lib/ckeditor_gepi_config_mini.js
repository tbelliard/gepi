/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {

	config.toolbar = [ 
		['Cut','Copy','Paste','PasteText','-','Undo','Redo'],
		['Find','Replace','Scayt'],
		['Bold','Italic','Underline','Subscript','Superscript','-','SpecialChar','-','RemoveFormat'],
		'/',
		[ 'Styles','Format','Font','FontSize'],
		['About']
	];

	config.stylesSet = [
		/* Inline Styles */
		{ name : 'Gros'				, element : 'big' },
		{ name : 'Petit'			, element : 'small' },
		{ name : 'Italique'			, element : 'var' },
		{ name : 'Barré'		, element : 'del' },
		{ name : 'Surligné'		, element : 'ins' },
		{ name : 'Guillemets'	, element : 'q' },
		];

	config.font_names = 'Helvetica/Helvetica, Arial, sans-serif; Courier/Courier, Courier New, monospace; Times New Roman/Times New Roman, Times, serif; Geneva/Geneva, Verdana, sans-serif';

	config.format_tags = 'p;h1;h2;h3;h4;h5;h6;pre';

};
