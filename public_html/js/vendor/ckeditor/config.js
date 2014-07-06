/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.filebrowserBrowseUrl = '/public_html/js/vendor/ckeditor/plugins/kcfinder-3.11/browse.php?type=files';
	config.filebrowserImageBrowseUrl ='/public_html/js/vendor/ckeditor/plugins/kcfinder-3.11/browse.php?type=images';
	config.filebrowserFlashBrowseUrl ='/public_html/js/vendor/ckeditor/plugins/kcfinder-3.11/browse.php?type=flash';
	config.filebrowserUploadUrl ='/public_html/js/vendor/ckeditor/plugins/kcfinder-3.11/upload.php?type=files';
	config.filebrowserImageUploadUrl = '/public_html/js/vendor/ckeditor/plugins/kcfinder-3.11/upload.php?type=images';
	config.filebrowserFlashUploadUrl = '/public_html/js/vendor/ckeditor/plugins/kcfinder-3.11/upload.php?type=flash';
};
