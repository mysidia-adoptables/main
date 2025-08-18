/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function(config){
	// Define changes to default configuration here.
	// For complete reference see:
	// https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_config.html

	// The toolbar groups arrangement, optimized for two toolbar rows.

	// The toolbar groups arrangement, optimized for a single toolbar row.
	config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'spellchecker' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		'/',
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'styles' },
        { name: 'tools' },
		{ name: 'about' }
	];

    config.removeButtons = 'Save,NewPage,ExportPdf,Preview,Print,CreateDiv,Flash,Smiley,PageBreak,Iframe,Font,FontSize,ShowBlocks';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';
};