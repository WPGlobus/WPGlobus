/**
 * WPGlobus Administration
 * Interface JS functions
 *
 * @since 1.9.17
 * @since 2.2.3
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
		oldLanguageSelector: false,
		languageSelectorEnabled: true,
		parseBool: function(b)  {
			return !(/^(false|0)$/i).test(b) && !!b;
		},
		isOldLanguageSelector: function() {
			return api.oldLanguageSelector;
		},
		isPostDisabled: function() {
			return WPGlobusGutenberg.__post.disabled;
		},
		init: function() {
			WPGlobusGutenberg.yoastSeo = api.parseBool(WPGlobusGutenberg.yoastSeo);
			if ( api.isPostDisabled() ) {
				return;
			}
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
			if ( WPGlobusGutenberg.tabs.length == 0 ) {
				api.initWPGlobusPlugin();
				return;
			}
			api.oldLanguageSelector = true;
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
			if ( ! api.isOldLanguageSelector() ) {
				return;
			}
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
			if ( ! api.isOldLanguageSelector() ) {
				return;
			}			
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

		},
		initWPGlobusPlugin: function(){
			// @since 2.2.3
			var language = WPGlobusGutenberg.language;
			var enabledLanguages = WPGlobusCoreData.enabled_languages;
			var languageNames = WPGlobusCoreData.en_language_name;
			var flagsUrl = WPGlobusGutenberg.flags_url;
			var ref = location.href;
			var refs = {};
			for (var key in enabledLanguages) {
				if ( -1 == ref.indexOf('language='+language) ) {
					refs[enabledLanguages[key]] = ref + '&language='+enabledLanguages[key];
				} else {
					refs[enabledLanguages[key]] = ref.replace( 'language='+language, 'language='+enabledLanguages[key] );
				}
			}

			var __ = wp.i18n.__;
			var el = wp.element.createElement;
			var Fragment = wp.element.Fragment;
			var Notice = wp.components.Notice;
			var Button = wp.components.Button;
			var LanguageSwitcher = wp.components;
			var PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem;
			var PanelBody = wp.components.PanelBody;
			var PluginSidebar = wp.editPost.PluginSidebar;
			var registerPlugin = wp.plugins.registerPlugin;

			var pinStarButton = $('.components-panel__header.edit-post-sidebar-header button.components-icon-button').eq(0);
			
			$(document).on('click', pinStarButton, function(){
				pinnedPluginButton();
			});

			function pinnedPluginButton() {
				setTimeout(function() {
					var buttons = document.getElementsByClassName('components-icon-button');
					for (var i = 0; i < buttons.length; i++) { 
						var status = buttons[i].getAttribute('aria-label');
						if ( null !== status && -1 != status.indexOf('WPGlobus') ) { 
							var done = buttons[i].dataset.done;
							if ( 'undefined' === typeof done ) {
								var content = buttons[i].innerHTML;
								buttons[i].innerHTML = content + '<img height="20px" width="20px" src="'+WPGlobusGutenberg.flags_url[WPGlobusGutenberg.language]+'" />&nbsp;' + WPGlobusAdmin.data.en_language_name[WPGlobusGutenberg.language];
								buttons[i].dataset.done = 'yes';	
							}
						}
					}
				}, 300);
			}
			
			function languageList() {
				
				if ( WPGlobusGutenberg.pagenow == WPGlobusGutenberg.postNewPage ) {
					return el(
						'div',
						{style:{marginBottom:'20px'},className: "wpglobus-block-editor-panel__switcher-notice"},
						WPGlobusGutenberg.i18n.save_post
					);		
				}
				
				return el(
					'ul',
					{className: 'language-list'},
					enabledLanguages.map(
						function(lang){
							return el( 'li', {key:lang, className:'language-item'}, 
										el('img', {style:{marginRight:'7px'},className:'wpglobus-block-editor-panel__flag', height:'20px', width:'20px', src:flagsUrl[lang]}),
										el(Button, {href:refs[lang], isSmall:true, isPrimary:true}, languageNames[lang]) 
									);
						}
					)
				);
			}
			
			function Layout() {
				return el(
					'div',
					{},
					el(
						Notice,
						{ 
						  className: 'wpglobus-block-editor-panel__notice',
						  status: 'informational',
						  isDismissible: false
						},
						__( 'Select language' )
					),
					el(
						'div',
						{ 
						  className: 'wpglobus-block-editor-panel__switcher-box' 
						},
						languageList()
					),		
					el(
						Button,
						{
						  className: 'wpglobus-block-editor-panel__button-link wpglobus-block-editor-panel__info',
						  href: WPGlobusGutenberg.store_link,
						  isLink: true,
						  target: "_blank"
						},
						__( 'WPGlobus Premium Add-ons' )
					),
					el(
						Button,
						{
						  className: 'wpglobus-block-editor-panel__button-link wpglobus-block-editor-panel__settings-link',
						  href: WPGlobusGutenberg.block_editor_tab_url,
						  isLink: true
						},
						__( 'Settings' )
					)			
				);
			}

			function Component() {
				pinnedPluginButton();
				return el(
					Fragment,
					{},
					el(
						PluginSidebarMoreMenuItem,
						{
						  target: 'wpglobus-block-editor-sidebar',
						  icon: 'admin-site',
						  //onClick: onMoreMenuItemClick
						},
						__( 'WPGlobus' )
					),
					el(
						PluginSidebar,
						{
						  name: 'wpglobus-block-editor-sidebar',
						  title: 'WPGlobus',
						  className: 'wpglobus-block-editor-components-panel',
						  //togglePin:
						},
						el(
						  PanelBody,
						  { className: 'wpglobus-block-editor-panel__body' },
						  Layout()
						)
						
					)
				);
			}
			registerPlugin( 'wpglobus-block-editor', {
				icon: '',
				render: Component,
			} );			
		}
	}
    WPGlobusGutenberg = $.extend({}, WPGlobusGutenberg, api);
    WPGlobusGutenberg.init();	
});
