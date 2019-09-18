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
		counter: 0,
		noticeOn: false,
		initDone: false,
		languageSelectorBoxDelta: 0,
		oldLanguageSelector: false,
		languageSelectorEnabled: true,
		parseBool: function(b) {
			return !(/^(false|0)$/i).test(b) && !!b;
		},
		getCounter: function(alias) {
			return api.counter;
		},
		getOptionKey: function(alias) {
			alias = alias || '';
			return WPGlobusGutenberg.keyOption[alias];
		},
		getOptions: function() {
			return WPGlobusGutenberg.options;
		},
		isOldLanguageSelector: function() {
			return api.oldLanguageSelector;
		},
		isPostDisabled: function() {
			return WPGlobusGutenberg.__post.disabled;
		},
		isEnabledTab: function(tab) {
			if ( 'undefined' === typeof tab ) {
				tab = 'options';
			}
			var enabled = false;
			if ( 'options' == tab ) {
				if ( api.parseBool(WPGlobusGutenberg.enabledOptionsTab) ) {
					enabled = true;
				}
			}
			return enabled;
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
				api.WPGlobusSwitcherPlugin();
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
		WPGlobusSwitcherPlugin: function(){
			// @since 2.2.3
			// @since 2.2.14
			
			if ( 'undefined' === typeof wp.editPost || 'undefined' === typeof wp.plugins ) {
				// @since 2.2.15
				return;
			}
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
			
			var TabPanel = wp.components.TabPanel;
			var withState = wp.compose.withState;
			var RadioControl = wp.components.RadioControl;
			var Notice = wp.components.Notice;
			var Button = wp.components.Button;

			var PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem;
			var PanelBody = wp.components.PanelBody;
			var PluginSidebar = wp.editPost.PluginSidebar;
			var registerPlugin = wp.plugins.registerPlugin;

			var pluginStarButton = $('.components-panel__header.edit-post-sidebar-header button.components-icon-button').eq(0);
			
			var switcherButtonHTML = {
				flagOnly: '<img height="20px" width="20px" src="'+WPGlobusGutenberg.flags_url[WPGlobusGutenberg.language]+'" />',
				flagLanguage: '<img height="20px" width="20px" src="'+WPGlobusGutenberg.flags_url[WPGlobusGutenberg.language]+'" />&nbsp;' + WPGlobusAdmin.data.en_language_name[WPGlobusGutenberg.language],
				flagCode: '<img height="20px" width="20px" src="'+WPGlobusGutenberg.flags_url[WPGlobusGutenberg.language]+'" />&nbsp;' + WPGlobusGutenberg.language,
				languageOnly: '&nbsp;' + WPGlobusAdmin.data.en_language_name[WPGlobusGutenberg.language],
				languageCode: '&nbsp;' + WPGlobusGutenberg.language,
			};
			
			var switcherPluginButtonType = '';
			var promisedPluginButtonType = '';
			var optionSwitcherButtonType = api.getOptionKey('switcherButtonType');
			
			function setSwitcherButtonType(type) {
				type = type || 'flagLanguage';
				switcherPluginButtonType = type;
			}
			
			function getSwitcherButtonType() {
				return switcherPluginButtonType;
			}
			
			setSwitcherButtonType(WPGlobusGutenberg.options[optionSwitcherButtonType]);
			
			promisedPluginButtonType = getSwitcherButtonType();
			
			$(document).on('click', pluginStarButton, function(){
				setSwitcherPluginButton();
			});
			
			function rbAnimate(start) {
				if ( 'undefined' === typeof start ) {
					start = true;
				}
				var elems = document.querySelectorAll('.wpglobus-switcher-components-radio-control .components-radio-control__input');
				if ( elems.length == 0 ) {
					return;
				}
				if ( start ) {
					elems.forEach(function(elem) {
					  elem.classList.add(elem.value);
					  elem.classList.remove('wpglobus-switcher-pulsate-radio');
					});
					var elem = document.querySelector('.wpglobus-switcher-components-radio-control .'+promisedPluginButtonType);
					elem.classList.add('wpglobus-switcher-pulsate-radio');
				} else {
					elems.forEach(function(elem) {
					  elem.classList.remove('wpglobus-switcher-pulsate-radio');
					});					
				}
			}
			function notice(el, mess) {
				if ( 'undefined' === typeof el || api.noticeOn ) {
					return;
				}
				api.noticeOn = true;
				el.innerText = mess;
				el.classList.remove('hidden');
				setTimeout(function() {
					el.classList.add('hidden');
					api.noticeOn = false;
				}, 3000);
			}
			
			function setOption(option, value) {
				option = option || '';
				value  = value || '';
				if ( optionSwitcherButtonType == option ) {
					rbAnimate(true);
					saveOptions(option, value).then(function(response){
						if ('success' == response.result) {
							setSwitcherButtonType(value);
							WPGlobusGutenberg.options[optionSwitcherButtonType] = value;
							setSwitcherPluginButton(value);
						}
					})
					.fail( function(response) {
						var el = document.getElementsByClassName('wpglobus-switcher-error-message');
						if ('undefined' !== typeof el[0]) {
							if ('error' == response.result) {
								notice(el[0], response.message);
							} else if (response.status == 400) {
								notice(el[0], 'Error: '+response.status + ' (' + response.statusText + ')' );
							}
							promisedPluginButtonType = getSwitcherButtonType();
						}
						var tab = document.getElementsByClassName('wpglobus-panel-tab-options');
						if ('undefined' !== typeof tab[0]) {
							if ( -1 != tab[0].classList.value.indexOf('is-active') ) {
								tab[0].click();
							}
						}
					})
					.always(function() {
						rbAnimate(false);
					});
				}					
			}
			
			function saveOptions(option, value) {
				option = option || '';
				if ( option == '' ) {
					return;
				}
				var data = {};
				data['sender']   = 'WPGlobusGutenberg';
				data['_action']  = 'saveOption';
				data['is_admin'] = 1;
				data['counter']  = api.counter++;
				data['options']  = {};
				data['options'][option] = value;
				return wp.ajax.post(WPGlobusGutenberg.wpglobusAjax, {data:data});
			}
			
			function getSwitcherButton(type) {
				if ( 'undefined' === typeof switcherButtonHTML[type] ) {
					type = switcherPluginButtonType;
				}
				return switcherButtonHTML[type];
			}
			
			function setSwitcherPluginButton(type) {
				if ( 'undefined' === typeof type || 'undefined' === typeof switcherButtonHTML[type] ) {
					type = switcherPluginButtonType;
				}

				setTimeout(function() {
					var buttons = document.getElementsByClassName('components-icon-button');
					for (var i = 0; i < buttons.length; i++) { 
						var status = buttons[i].getAttribute('aria-label');
						if ( null !== status && -1 != status.indexOf('WPGlobus') ) { 
							var done = buttons[i].dataset.done;
							if ( 'undefined' === typeof done ) {
								var content = buttons[i].innerHTML;
								buttons[i].innerHTML = content + getSwitcherButton(type);
								buttons[i].dataset.done = 'init';	
							} else {
								buttons[i].innerHTML = getSwitcherButton(type);
								buttons[i].dataset.done = 'changed';	
							}
						}
					}
				}, 300);
			}
			
			function languageList() {
				
				if ( WPGlobusGutenberg.pagenow == WPGlobusGutenberg.postNewPage ) {
					return el(
						'div',
						{style:{marginBottom:'20px'},className: "wpglobus-switcher-panel__switcher-notice"},
						WPGlobusGutenberg.i18n.save_post
					);		
				}
				
				return el(
					'ul',
					{className: 'language-list'},
					enabledLanguages.map(
						function(lang){
							return el( 'li', {key:lang, className:'language-item'}, 
								el('img', {style:{marginRight:'7px'},className:'wpglobus-switcher-panel__flag', height:'20px', width:'20px', src:flagsUrl[lang]}),
								el(Button, {href:refs[lang], isSmall:true, isPrimary:true}, languageNames[lang]) 
							);
						}
					)
				);
			}
					
			function onTabSelect(tab) {
				if ( tab.name == 'switcher' ) {
					return SwitcherTabContent();
				} else if ( tab.name == 'options' ) {
					return OptionsTabContent();
				}	
			}

			function TabLayout() {

				var tabs = [
					{
					  name: 'switcher',
					  title: 'Languages',
					  className: 'wpglobus-panel-tab wpglobus-panel-tab-selector edit-post-sidebar__panel-tab'
					}
				];
				
				if ( WPGlobusGutenberg.isEnabledTab('options') ) {
					tabs.push(
						{
						  name: 'options',
						  title: 'Options',
						  className: 'wpglobus-panel-tab wpglobus-panel-tab-options edit-post-sidebar__panel-tab'
						} 					
					);
				}
				
				return el(
					TabPanel,
					{
					  name: 'WPGlobusSwitcherTabPanel',
					  className: 'wpglobus-tab-panel',
					  activeClass: 'is-active',
					  tabs: tabs
					},
					onTabSelect
				);
			}
			
			function SwitcherTabContent() {
				return el(
					'div',
					{
					  className: 'wpglobus-tab-content wpglobus-selector-tab-content',
					},
					el(
						Notice,
						{ 
						  className: 'wpglobus-switcher-panel__notice',
						  status: 'informational',
						  isDismissible: false
						},
						__( 'Select language' )
					),
					el(
						'div',
						{ 
						  className: 'wpglobus-switcher-panel__switcher-box' 
						},
						languageList()
					),		
					el(
						Button,
						{
						  className: 'wpglobus-switcher-panel__button-link wpglobus-switcher-panel__info',
						  href: WPGlobusGutenberg.store_link,
						  isLink: true,
						  target: "_blank"
						},
						__( 'WPGlobus Premium Add-ons' )
					),
					el(
						Button,
						{
						  className: 'wpglobus-switcher-panel__button-link wpglobus-switcher-panel__settings-link',
						  href: WPGlobusGutenberg.block_editor_tab_url,
						  isLink: true
						},
						__( 'Settings' )
					)			
				);
			}

			var SwitcherPluginButton = withState({
			  value: promisedPluginButtonType
			})(({
			  value,
			  setState
			}) => el(RadioControl, {
			  label: '',
			  help: 'Select type of switcher language button.',
			  selected: promisedPluginButtonType,
			  className: 'wpglobus-switcher-components-radio-control',
			  options: [
				{ label: 'Flag only', value: 'flagOnly' },
				{ label: 'Flag with language', value: 'flagLanguage' },
				{ label: 'Flag with language code', value: 'flagCode' },
				{ label: 'Language only', value: 'languageOnly' },
				{ label: 'Language code', value: 'languageCode' },
			  ],
			  onChange: value => {
			    promisedPluginButtonType = value;
				setState({
				  value
				});
				// @see setOption function for setSwitcherButtonType(value) and setSwitcherPluginButton(value)
				setOption(optionSwitcherButtonType, value);
			  }
			}));
		
			function OptionsTabContent() {
				return el(
					'div',
					{
					  className: 'wpglobus-tab-content wpglobus-options-tab-content',
					},
					el(
					  Notice,
					  { 
						className: 'wpglobus-switcher-panel__notice',
						status: 'informational',
						isDismissible: false
					  },
					  __( 'Select type' )
					),
					el(
						SwitcherPluginButton, 
						null
					)
				);
			}

			function Component() {
				setSwitcherPluginButton();
				return el(
					Fragment,
					{},
					el(
						PluginSidebarMoreMenuItem,
						{
						  target: 'wpglobus-switcher-sidebar',
						  icon: 'admin-site',
						  //onClick: @see wp-includes\js\dist\edit-post.js
						},
						__( 'WPGlobus' )
					),
					el(
						PluginSidebar,
						{
						  name: 'wpglobus-switcher-sidebar',
						  title: 'WPGlobus',
						  className: 'wpglobus-switcher-components-panel',
						  //togglePin: @see wp-includes\js\dist\edit-post.js
						},
						el(
						  'div',
						  { 
						    className: 'wpglobus-switcher-message wpglobus-switcher-error-message hidden',
						    style:{}
						  },
						  ''
						),
						el(
						  PanelBody,
						  { 
						    className: 'wpglobus-switcher-panel__body'
						  },
						  TabLayout()
						)
						
					)
				);
			}
			registerPlugin( 'wpglobus-switcher', {
				icon: '',
				render: Component,
			} );			
		}
	}
    WPGlobusGutenberg = $.extend({}, WPGlobusGutenberg, api);
    WPGlobusGutenberg.init();	
});
