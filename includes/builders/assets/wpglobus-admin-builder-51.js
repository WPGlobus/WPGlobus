/**
 * WPGlobus Administration Core.
 * Interface JS functions
 *
 * @since 1.9.17
 *
 * @package WPGlobus
 * @subpackage Administration
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusCore*/

var WPGlobusCore;

(function($) {
	var api;
	api = WPGlobusCore = {
		strpos: function( haystack, needle, offset){
			haystack = "" + haystack;
			var i = haystack.indexOf( needle, offset );
			return i >= 0 ? i : false;
		},

		TextFilter: function(text, language, return_in){
			if ( typeof text == 'undefined' || '' === text ) { return text; }

			var pos_start, pos_end, possible_delimiters = [], is_local_text_found = false;;

			language = '' == language ? 'en' : language;
			return_in  = typeof return_in == 'undefined' || '' == return_in  ? 'RETURN_IN_DEFAULT_LANGUAGE' : return_in;

			possible_delimiters[0] = [];
			possible_delimiters[0]['start'] = WPGlobusCoreData.locale_tag_start.replace('%s', language);
			possible_delimiters[0]['end'] 	 = WPGlobusCoreData.locale_tag_end;

			possible_delimiters[1] = [];
			possible_delimiters[1]['start'] = '<!--:'+language+'-->';
			possible_delimiters[1]['end'] = '<!--:-->';

			possible_delimiters[2] = [];
			possible_delimiters[2]['start'] = '[:'+language+']';
			possible_delimiters[2]['end'] = '[:';



			for (var i = 0; i < 3; i++) {

				pos_start = api.strpos( text, possible_delimiters[i]['start'] );
				if ( pos_start === false ) {
					continue;
				}

				pos_start = pos_start + possible_delimiters[i]['start'].length;

				pos_end = api.strpos( text, possible_delimiters[i]['end'], pos_start );

				if ( pos_end === false ) {
					text = text.substr( pos_start );
				} else {
					text = text.substr( pos_start, pos_end - pos_start );
				}

				is_local_text_found = true;
				break;

			}

			if ( ! is_local_text_found ) {
				if ( return_in == 'RETURN_EMPTY' ) {
					if ( language == WPGlobusCoreData.default_language && ! /(\{:|\[:|<!--:)[a-z]{2}/.test(text) ) {
						/** do nothing */
					} else {
						text = '';
					}
				} else {
					/**
					 * Try RETURN_IN_DEFAULT_LANGUAGE.
					 */
					if ( language == WPGlobusCoreData.default_language ) {
						if ( /(\{:|\[:|<!--:)[a-z]{2}/.test(text) ) {
							text = '';
						}
					} else {
						text = api.TextFilter( text, WPGlobusCoreData.default_language );
					}
				}
			}
			return text;
		},
		addLocaleMarks: function(text, language) {
			return WPGlobusCoreData.locale_tag_start.replace('%s', language) + text + WPGlobusCoreData.locale_tag_end;
		},
		getTranslations: function(text) {
			var t = {},
				return_in;
			$.each(WPGlobusCoreData.enabled_languages, function(i,l){
				return_in  = l == WPGlobusCoreData.default_language  ? 'RETURN_IN_DEFAULT_LANGUAGE' : 'RETURN_EMPTY';
				t[l] = api.TextFilter(text, l, return_in);
			});
			return t;
		},
		getString: function(s, newVal, l) {
			if ( 'undefined' === typeof(s) ) {
				return s;
			}
			if ( 'undefined' === typeof(newVal) ) {
				newVal = '';
			}
			if ( 'undefined' === typeof(l) ) {
				l = WPGlobusCoreData.language;
			}

			s = api.getTranslations(s);
			s[l] = newVal;

			var cS = '';

			$.each(s, function(ln,val){
				if ( '' != val && ln != WPGlobusCoreData.default_language) {
					cS += api.addLocaleMarks(val, ln);
				}
			});

			if ( '' != s[WPGlobusCoreData.default_language] ) {
				if ( '' == cS ) {
					cS = s[WPGlobusCoreData.default_language];
				} else {
					cS = api.addLocaleMarks(s[WPGlobusCoreData.default_language], WPGlobusCoreData.default_language) + cS;
				}
			}
			return cS;
		}
	};
})(jQuery);

jQuery(document).ready(function ($) {
    "use strict";

	if ( 'undefined' === typeof WPGlobusAdmin ) {
		return;
	}
	
	var api = {
		languageSelectorBoxDelta: 0,
		locationPathname: '/wp-admin/post.php',
		init: function() {
			//api.setCookie();
			if ('taxonomy-edit' === WPGlobusAdmin.page) {
				api.taxonomyEdit();
			} else {
				api.start();
			}
		},
		_info: function() {
			var lang = undefined;
			if( $('#language').length > 0 ) {
				lang = $('#language').val();
			}
			console.log('Current language from hidden (#language) : ', lang);
			
			lang = undefined;
			if( $('#wpglobus-language').length > 0 ) {
				lang = $('#wpglobus-language').val();
			}
			console.log('Current language from hidden (#wpglobus-language) : ', lang);
			
			lang = undefined;
			if( $('input[name="wpglobus-language"]').length > 0 ) {
				lang = $('input[name="wpglobus-language"]').val();
			}
			console.log('Current language from hidden (input[name="wpglobus-language"]) : ', lang);
			
			lang = undefined;
			if( $('input[name="wpglobus_language"]').length > 0 ) {
				lang = $('input[name="wpglobus_language"]').val();
			}
			console.log('Current language from hidden (input[name="wpglobus_language"]) : ', lang);			
            console.log('Builder : ', WPGlobusAdmin.builder);
			
			var content = $('#content').val();
			if ( content == '' ) {
				console.log('Editor content (#content) : (empty)');
			} else {
				console.log(content);
			}
		},
		checkCookie: function() {
			if ( -1 != window.location.search.indexOf('language=') ) {
				return false;
			}
			if ( 'post.php' == WPGlobusAdmin.page ) {
				var cookie = wpCookies.get(WPGlobusAdmin.builder.languageCookie);
				
				if ( null !== cookie && history.pushState) {
					cookie = cookie.split('+');
					var language = cookie[0];
					if ('undefined' !== typeof cookie[1] && cookie[1] + 0 == WPGlobusAdmin.$_get.post ) {
						var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.search + '&language='+language;
						window.history.pushState({path:newurl},'',newurl);
						return language;
					}
				}
			}
			return false;
		},
		setCookie: function() {
			var lang = api.checkCookie();
			if ( ! lang ) {
				lang = WPGlobusAdmin.$_get.language;
				if ( 'undefined' === typeof lang ) {
					lang = WPGlobusAdmin.data.default_language;
				}				
			}
			wpCookies.set(WPGlobusAdmin.builder.languageCookie, lang+'+'+WPGlobusAdmin.$_get.post, 31536000, api.locationPathname );
		},		
		setLocationSearch: function() {
			if ( ! history.pushState || -1 != window.location.search.indexOf('language=') ) {
				return;
			}
			if ( WPGlobusAdmin.data.default_language != WPGlobusAdmin.currentTab ) {
				var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.search + '&language='+WPGlobusAdmin.currentTab;
				window.history.pushState({path:newurl},'',newurl);
			}				
		},
		taxonomyEdit: function() {
			
			$('.wpglobus-taxonomy-tabs').insertAfter('#ajax-response');

			/**
			 * Make class wrap as tabs container.
			 * Tabs on.
			 */
			$('.wrap').tabs();
			
			$(WPGlobusAdmin.data.multilingualSlug.title).insertAfter('.term-slug-wrap th label');
			
			if ( WPGlobusAdmin.currentTab !== WPGlobusAdmin.data.default_language ) {
				$('.wpglobus-tax-edit-tab').removeClass('ui-tabs-active ui-state-active');
				$('#wpglobus-link-tab-'+WPGlobusAdmin.currentTab).addClass('ui-tabs-active ui-state-active');
			}
			
			$(document).on('click', '.wpglobus-tax-edit-tab a', function(event) {
				event.preventDefault();
				var _href = $(this).data('href');
				location.href = _href;
			});			
			
			api.setMultilingualFields();
			
			/**
			 * Init Yoast tinymce editor for description.
			 */
			$(document).on( 'tinymce-editor-init', function( event, editor ) {	
				$( '#' + editor.getContainer().id ).find('iframe').addClass(WPGlobusAdmin.builder.translatableClass).css({'width':''});
			});			
		},
		start: function() {
			/**
			 *
			 */
			api.setLocationSearch();
			
			/**
			 *
			 */			 
			var wrap_at = '#postdivrich',
				set_title = true,
				content_tabs_id = '#post-body-content';
			if ( WPGlobusAdmin.data.support['editor'] === false ) {
				wrap_at = '#titlediv';
				set_title = false;
			}
			if ( WPGlobusAdmin.data.support['title'] === false ) {
				set_title = false;
			}
			
			/**
			 * Make post-body-content as tabs container.
			 */
			$(content_tabs_id).prepend($('.wpglobus-post-body-tabs-list'));
			$.each(WPGlobusAdmin.tabs, function (index, suffix) {
				if ('default' === suffix) {
					$(wrap_at).wrap('<div id="tab-default"></div>');
					if ( set_title ) {
						$($('#titlediv')).insertBefore(wrap_at);
					}
				} else {
					$(wrap_at+'-' + suffix).wrap('<div id="tab-' + suffix + '"></div>');
					if ( set_title ) {
						$($('#titlediv-' + suffix)).insertBefore(wrap_at+'-' + suffix);
					}
				}
			});

			$(content_tabs_id).addClass('wpglobus-post-body-tabs');		

			/**
			 * Set fields as multilingual.
			 */
			//setTimeout(api.setMultilingualFields(), 1000);
			api.setMultilingualFields();
			
			/**
			 *
			 */
			//api.ajaxPrefilter();

		},
		ajaxPrefilter: function() {
			/**
			 * @todo WIP.
			 */
			$.ajaxPrefilter(function( options, originalOptions, jqXHR ) {
				// Modify options, control originalOptions, store jqXHR, etc
				//console.log(options);
			});		
		},
		setMultilingualFields: function() {
			$.each(WPGlobusAdmin.builder.multilingualFields, function(indx, field) {	
				var elementBy = 'name';
				var node = document.getElementsByName(field);
				var $element;
				
				if ( 0 == node.length ) {
					elementBy = 'id';
					node = document.getElementById(field);
				}	

				if ( null === node ) {
					return;
				} else {
					if ( 'id' == elementBy ) {
						$element = $('#'+field);
					} else {
						var nodeName = node[0].nodeName;
						nodeName = nodeName.toLowerCase();
						$element = $(nodeName+'[name="'+field+'"]');
					}
					$element.addClass(WPGlobusAdmin.builder.translatableClass);
				}				
				
			});			
		}
	}
	
	WPGlobusAdmin = $.extend({}, WPGlobusAdmin, api);
    WPGlobusAdmin.init();	
});
