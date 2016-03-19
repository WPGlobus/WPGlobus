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
	
	if ( typeof WPGlobusAcf == 'undefined' ) {
		return;	
	}	
	
	var api = {	
		option : {
		},
		init: function(args) {
			api.option = $.extend( api.option, args );
			if ( api.option.pro ) {
				api.startAcf('.acf-field');
			} else {
				api.startAcf('.acf_postbox .field');
			}	
		},
		startAcf: function(acf_class) {
			var id;
			var style = 'width:90%;';
			var element, clone, name;
			if  ( $('.acf_postbox').parents('#postbox-container-2').length == 1 ) {
				style = 'width:97%';	
			}	
			//$('.acf_postbox .field').each(function(){
			$(acf_class).each(function(){
				var $t = $(this), id, h;
				if ( $t.hasClass('field_type-textarea') || $t.hasClass('acf-field-textarea') ) {
					id = $t.find('textarea').attr('id');
					h = $('#'+id).height() + 20;
					WPGlobusDialogApp.addElement({
						id: id,
						dialogTitle: 'Edit ACF field',
						style: 'width:97%;float:left;',
						styleTextareaWrapper: 'height:' + h + 'px;',
						sbTitle: 'Click for edit',
						onChangeClass: 'wpglobus-on-change-acf-field'
					});
				} else if ( $t.hasClass('field_type-text') || $t.hasClass('acf-field-text') ) {
					id = $t.find('input').attr('id');
					WPGlobusDialogApp.addElement({
						id: id,
						dialogTitle: 'Edit ACF field',
						style: 'width:97%;float:left;',
						sbTitle: 'Click for edit',
						onChangeClass: 'wpglobus-on-change-acf-field'
					});			
				}
			});		
		}	
	}
	
	WPGlobusAcf = $.extend({}, WPGlobusAcf, api);
	
	WPGlobusAcf.init({'pro':WPGlobusAcf.pro});	

});