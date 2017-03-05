/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

	CKEDITOR.stylesSet.add('gepi_styles',[
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
		{ name : 'Surligné'	, element : 'ins' },
		{ name : 'Guillemets'	, element : 'q' },

		/* Object Styles */

		{
			name : 'Image à gauche',
			element : 'img',
			attributes :
			{
				'style' : 'padding: 5px; margin-right: 5px',
				'border' : '2',
				'align' : 'left'
			}
		},

		{
			name : 'Image à droite',
			element : 'img',
			attributes :
			{
				'style' : 'padding: 5px; margin-left: 5px',
				'border' : '2',
				'align' : 'right'
			}
		},

	]);

	CKEDITOR.config.format_tags = 'p;h1;h2;h3;h4;h5;h6;pre';

	CKEDITOR.config.font_names =
	'Helvetica/Helvetica, Arial, sans-serif;' +
	'Courier/Courier, Courier New, monospace;' +
	'Times New Roman/Times New Roman, Times, serif;' +
	'Geneva/Geneva, Verdana, sans-serif';
	
}