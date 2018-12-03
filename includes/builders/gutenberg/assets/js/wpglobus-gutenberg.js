/**
 * WPGlobus Administration
 * Interface JS functions
 *
 * @since 1.9.17
 *
 * @package WPGlobus
 * @subpackage Administration/Gutenberg
 */
/*jslint browser: true */
/*global jQuery, console, _wpGutenbergCodeEditorSettings*/

jQuery(document).ready(function ($) {
    "use strict";
	
	var api = {
		initDone: false,
		languageSelectorBoxDelta: 0,
		languageSelectorEnabled: true,
		parseBool: function(b)  {
			return !(/^(false|0)$/i).test(b) && !!b;
		},
		init: function() {
			WPGlobusGutenberg.yoastSeo = api.parseBool(WPGlobusGutenberg.yoastSeo);
			api.initListeners();
			api.setTabs();
			api.formHandler();
			api.attachListeners();
		},
		initListeners: function() {
			if ( WPGlobusGutenberg.yoastSeo && 1 == $('.yoast.wpseo-metabox').length ) {
				/**
				 * Prevent start of alert message when yoast seo is present.
				 * Check getEventListeners(window).beforeunload in Chrome console for beforeunload event.
				 * @see https://developers.google.com/web/tools/chrome-devtools/console/command-line-reference#monitoreventsobject-events
				 */
				$(window).on('beforeunload', function (event) {
					event.stopImmediatePropagation()
				});				
			}
		},
		formHandler: function() {
			
			var val = $('.metabox-base-form #referredby').attr('value');
			if ( 'undefined' !== typeof val ) {
				if( val.indexOf('language=en') == -1 ) {
					val = val+'&language='+WPGlobusGutenberg.language;
				} else {
					val = val.replace('language=en', 'language='+WPGlobusGutenberg.language);
				}
				$('.metabox-base-form #referredby').attr('value', val);
			}
			
			val = $('input[name="_wp_original_http_referer"]').attr('value');
			if ( 'undefined' !== typeof val ) {
				if( val.indexOf('language=en') == -1 ) {
					val = val+'&language='+WPGlobusGutenberg.language;
				} else {
					val = val.replace('language=en', 'language='+WPGlobusGutenberg.language);
				}			
				$('input[name="_wp_original_http_referer"]').attr('value', val);
			}			
		},
		setTabs: function() {
			var intervalID = setInterval( function() {
				/** var $toolbar = $('.edit-post-header'); **/
				var $toolbar = $('.edit-post-header__settings');
				if( $toolbar.length == 1 ) {
					$toolbar.before(WPGlobusGutenberg.tabs);
					/*
					var width = $('.edit-post-header-toolbar').css('width');
					width = width.replace('px','') * 1;
					if ( width < 50 ) {
						width = width + 5;
					} else {
						width = width + 30;
					}
					$('.wpglobus-gutenberg-selector-box').css({'margin-left':width+'px'});
					// */
					clearInterval(intervalID)
				} else {
					//console.log('Here: else');
				}
			}, 200);
		},
		setSelectorStatus: function() {
			$('.wpglobus-gutenberg-selector-box').css({'opacity':'0.2'}).attr('onclick','return false;');
			api.languageSelectorEnabled = false;
			var iID = setInterval( function() {
				if ( $('.is-saving').length == 0 ) {
					clearInterval(iID);
					if ( WPGlobusGutenberg.pagenow == WPGlobusGutenberg.postNewPage ) {
						if ( location.pathname.indexOf(WPGlobusGutenberg.postEditPage) != -1 ) {
							WPGlobusGutenberg.pagenow = WPGlobusGutenberg.postEditPage;
							$('.wpglobus-gutenberg-selector-box').css({'opacity':'1'}).attr('onclick','');
							api.reloadPage();
							return;							
						}
					}
					api.languageSelectorEnabled = true;
					$('.wpglobus-gutenberg-selector-box').css({'opacity':'1'}).attr('onclick','');
				}
			}, 400);				
		},
		reloadPage: function() {
			$('.wpglobus-selector-grid').css({'grid-template-columns':'10% 90%'}); 
			$('.wpglobus-gutenberg-selector-text').text(WPGlobusGutenberg.i18n.reload); 
			(function blink() { 
			  $('.wpglobus-gutenberg-selector').fadeOut(500).fadeIn(500, blink); 
			})();
			setTimeout( function() {
				location.reload();
			}, 500);
		},
		attachListeners: function() {
			
			/**
			 * Language selector.
			 */
			$(document).on('mouseenter', '.wpglobus-gutenberg-selector', function(ev) {
				if ( ! api.languageSelectorEnabled ) {
					return;
				}
				$('.wpglobus-gutenberg-selector-dropdown').css({'display':'block'});
				api.languageSelectorBoxDelta = ev.screenY;
				$('.edit-post-header').css({'z-index':'100000'});
				$('.wpglobus-gutenberg-selector-box').css({'z-index':'100001'});
			});
			$(document).on('mouseleave', '.wpglobus-gutenberg-selector', function(ev) {
				if ( api.languageSelectorBoxDelta != 0 && ev.screenY - api.languageSelectorBoxDelta <= 0) {
					$('.wpglobus-gutenberg-selector-dropdown').css({'display':'none'});
					$('.edit-post-header').css({'z-index':'9989'});
					$('.wpglobus-gutenberg-selector-box').css({'z-index':'100'});
				}
			});
			
			/**
			 * Dropdown list.
			 */				
			$(document).on('mouseleave', '.wpglobus-gutenberg-selector-dropdown', function(ev) {
				$('.wpglobus-gutenberg-selector-dropdown').css({'display':'none'});
				$('.edit-post-header').css({'z-index':'9989'});
				$('.wpglobus-gutenberg-selector-box').css({'z-index':'10000'});
			});			
			
			/**
			 * editor-post-save-draft.
			 */
			$(document).on('click', '.editor-post-save-draft', function() {
				api.setSelectorStatus();
			});
			
			/**
			 * editor-post-publish-button.
			 */
			$(document).on('click', '.editor-post-publish-button', function() {
				api.setSelectorStatus();
			});

		}
	}
    WPGlobusGutenberg = $.extend({}, WPGlobusGutenberg, api);
    WPGlobusGutenberg.init();	
});
