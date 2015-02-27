/**
 * WPGlobus Administration ACF plugin fields
 * Interface JS functions
 *
 * @since 1.0.5
 *
 * @package WPGlobus
 * @subpackage Administration
 */
/* jslint browser: true */
/* global jQuery, console, WPGlobusCore, WPGlobusCoreData */

jQuery(document).ready(function($){
    "use strict";
	var id;
	var style = 'width:90%;';
    var element, clone, name;
	if  ( $('.acf_postbox').parents('#postbox-container-2').length == 1 ) {
		style = 'width:97%';	
	}	
	$('.acf_postbox .field').each(function(){
		var $t = $(this);
		if ( $t.hasClass('field_type-textarea') ) {
			element = $t.find('textarea');
			id = element.attr('id');
			clone = $('#'+id).clone();
			$(element).addClass('hidden');
			name = element.attr('name');
			$(clone).attr('id', 'wpglobus-'+id);
			$(clone).attr('name', 'wpglobus-'+name);
			$(clone).attr('data-source-id', id);
			$(clone).attr('class', 'wpglobus-dialog-field textarea');
			$(clone).attr('style', style);
			$(clone).val( WPGlobusCore.TextFilter($(element).val(), WPGlobusCoreData.language) );
			$(clone).insertAfter(element);
			$('<div style="width:20px;float:right;"><div style="margin:2px;" data-type="control" data-source-type="textarea" data-source-id="'+id+'" class="wpglobus_dialog_start wpglobus_dialog_icon"></div></div>').insertAfter(clone);
		} else if ( $t.hasClass('field_type-text') ) {
			element = $t.find('input');
	        id = element.attr('id');
			clone = $('#'+id).clone();
			$(element).addClass('hidden');
			name = element.attr('name');
			$(clone).attr('id', 'wpglobus-'+id);
			$(clone).attr('name', 'wpglobus-'+name);
			$(clone).attr('data-source-id', id);
			$(clone).attr('class', 'wpglobus-dialog-field text');
			$(clone).attr('style', style);
			$(clone).val( WPGlobusCore.TextFilter($(element).val(), WPGlobusCoreData.language) );
			$(clone).insertAfter(element);
			$('<div style="width:20px;float:right;"><div style="margin:2px;" data-type="control" data-source-type="textarea" data-source-id="'+id+'" class="wpglobus_dialog_start wpglobus_dialog_icon"></div></div>').insertAfter(clone);
		}
	});
});