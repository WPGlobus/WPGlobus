/**
 * WPGlobus Vendor Pods.
 * Interface JS functions
 *
 * @since 2.5.17
 *
 * @package WPGlobus
 */
/*jslint browser:true*/
/*global tinymce:true */
/*global jQuery, console, WPGlobusVendorPods*/
jQuery(document).ready(function($){
	"use strict";

	if ( 'undefined' === typeof WPGlobusVendorPods ) {
		return;
	}
	
	var api = {
		start: function(){
			api.triggerHandlers();
		},
		wysiwygFieldHandler: function(attrs){
			
			if ( 'undefined' === typeof WPGlobusAdmin ) {
				return;
			}
			
			if ( 'undefined' === typeof attrs.id ) {
				return;
			}
			/**
			 * @see id = 'meta_description' => iframe id = 'pods-form-ui-pods-meta-description_ifr'
			 * @see id = 'persion_full_biography' => iframe id = 'pods-form-ui-pods-meta-persion-full-biography_ifr'	
			 */
			var id = attrs.id.replace(/_/g, '-');
		
			tinymce.PluginManager.add('wpglobus_globe', function(editor, url) {
			
				if ( -1 === editor.id.indexOf(id) ) {
					return;
				}
				
				_.delay( function() {
					$( editor.iframeElement ).addClass(WPGlobusAdmin.builder.translatableClass).css({'width':'99%'});
					$( '#' + editor.id ).addClass(WPGlobusAdmin.builder.translatableClass);
				}, 2000 );
				editor.addButton( 'wpglobus_globe', {
					text: ' '+WPGlobusAdmin.data.en_language_name[WPGlobusAdmin.currentTab],
					icon: 'wpglobus-plus-globe'
				});				
			});			
		},
		fileFieldHandler: function(attrs){
			// @since 2.5.17 @W.I.P
		},
		triggerHandlers: function(){
			$(document).triggerHandler('wpglobus_wysiwyg_field', {callback:api.wysiwygFieldHandler});
			// @since 2.5.17 @W.I.P
			//$(document).triggerHandler('wpglobus_file_field', {callback:api.fileFieldHandler});
		}
	};

	WPGlobusVendorPods = $.extend({}, WPGlobusVendorPods, api);
	WPGlobusVendorPods.start();	
});