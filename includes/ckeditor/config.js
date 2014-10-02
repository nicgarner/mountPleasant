/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	config.height = '400';

	config.toolbar_Full =
	[
		{ name: 'clipboard', items: ['Cut','Copy','Paste','PasteText','-','SpellChecker'] },
		{ name: 'tools',     items: ['Undo','Redo','-','Find','-','RemoveFormat','-',
		                             'CreateDiv','Table','HorizontalRule'] },
		{ name: 'format',    items: ['TextColor','Bold','Italic','-','NumberedList','BulletedList','-',
		                             'Link','Unlink','-','About'] }, '/',
		{ name: 'style',     items: ['Format','Font','FontSize'] },
		{ name: 'alignment', items: ['JustifyLeft','JustifyCenter','JustifyRight'] },
    { name: 'source',    items: ['Source'] }
	] ;

	config.toolbar_Email =
	[
		[ 'Bold','Italic','-','NumberedList','BulletedList','-','Link','Unlink','-','SpellChecker','-','About' ]
	];
	
/*config.extraPlugins = 'stylesheetparser';*/
	config.contentsCss = 'fck.css';
	
	config.stylesSet = 'tiles';
	
	
	
};
