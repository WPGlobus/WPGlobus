/**
 * WPGlobus Media Administration.
 * Interface JS functions
 *
 * @since 1.7.3
 *
 * @package WPGlobus
 * @subpackage Media
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusCore, WPGlobusMedia*/
(function($) {
	"use strict";

	if ( 'undefined' === typeof WPGlobusCore ) {
		return;
	}
	if ( 'undefined' === typeof WPGlobusMedia ) {
		return;	
	}
	
	var api = {
		content		 : {},
		save   		 : true,
		resetContent : true,
		init: function(args) {
			var wpglobusTabs = $('.wpglobus-post-body-tabs');
			if ( wpglobusTabs.length != 1 ) {
				return;
			}
			wpglobusTabs.insertBefore('.wp_attachment_details');
			wpglobusTabs.tabs();
			api.iniSet();
			api.setContent();
			api.attachListeners();
		},
		iniSet: function() {
			$.each( WPGlobusMedia.attachment, function( name, id ){
				api.content[id] = $('#'+id).val();
				$('#'+id).addClass('wpglobus-translatable');
			});
		},
		setContent: function( beforeSave ) {
			if ( 'undefined' === typeof beforeSave ) {
				beforeSave = false;
			}
			if ( beforeSave ) {
				$.each( WPGlobusMedia.attachment, function( name, id ){
					$('#'+id).val( api.content[id] );
				});				
			} else {
				$.each( WPGlobusMedia.attachment, function( name, id ){
					$('#'+id).val( WPGlobusCore.TextFilter( api.content[id], WPGlobusMedia.language, 'RETURN_EMPTY' ) );
				});
			}
		},
		attachListeners: function() {
			/**
			 * Switch language.
			 */
			$(document).on( 'tabsactivate', '.wpglobus-post-body-tabs', function( event, ui ) {
				WPGlobusMedia.language = ui.newTab[0].dataset.language;
				api.setContent();
			});
			
			/**
			 * Keyup event.
			 */
			$.each( WPGlobusMedia.attachment, function( name, id ){
				$(document).on( 'keyup', '#'+id, function( evnt ) {
					api.content[ evnt.currentTarget.id ] = WPGlobusCore.getString( api.content[ evnt.currentTarget.id ], evnt.currentTarget.value, WPGlobusMedia.language );
				});
			});
			
			/**
			 * Update event.
			 */			
			$(document).on( 'mouseenter', '#publish', function( event ) {
				api.setContent( api.save );
			}).on( 'mouseleave', '#publish', function( event ) {
				if ( api.resetContent ) {
					api.setContent();
				}
				api.resetContent = true;
			}).on( 'click', '#publish', function( event ) {
				api.resetContent = false;
			});			
			
			/**
			 * Before an Ajax request is sent.
			 */		
			$( document ).ajaxSend(function( event, request, settings ) {
				if ( 'undefined' === typeof settings.data ) {
					return;	
				}
				if ( -1 != settings.data.indexOf( 'action=send-attachment-to-editor' ) ) {
					settings.data = settings.data + '&wpglobusLanguageTab=' + WPGlobusAdmin.currentTab;
				}
				
			});			 
			 
		}
	};

	WPGlobusMedia = $.extend({}, WPGlobusMedia, api);
	WPGlobusMedia.init();
	
})(jQuery);	