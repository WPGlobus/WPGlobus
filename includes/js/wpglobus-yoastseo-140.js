/**
 * WPGlobus for YoastSeo v.14
 * Interface JS functions
 *
 * @since 2.4
 * @since 2.5.16 Removed unneeded code. Small tweaks.
 *
 * @package WPGlobus
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusVendor, WPGlobusCoreData*/

jQuery(document).ready( function ($) {
	'use strict';

	if ( typeof WPGlobusCoreData === 'undefined' ) {
		return;
	}

	if ( typeof WPGlobusVendor === 'undefined' ) {
		return;
	}

	var api = {
		initSeoAnalysis: false,
		initReadability: false,
		accessExtra: false,
		parseBool: function(b)  {
			return !(/^(false|0)$/i).test(b) && !!b;
		},
		moduleState: function(){
			if ( api.accessExtra ) {
				return true;	
			}
			if ( 'string' === typeof WPGlobusYoastSeo.plus_module ) {
				if ( '' != WPGlobusYoastSeo.plus_module ) {
					return WPGlobusYoastSeo.plus_module;
				}
			}
			return api.parseBool(WPGlobusYoastSeo.plus_access);
		},
		isPremium: function(){
			return WPGlobusVendor.vendor['WPSEO_PREMIUM'];
		},
		isDefaultLanguage: function(){
			return api.parseBool(WPGlobusYoastSeo.is_default_language);
		},
		isBuilderPage: function(){
			return api.parseBool(WPGlobusYoastSeo.builder_page);
		},
		getSuggest: function(type){
			var suggest = '';
			if ( 'undefined' === typeof type ) {
				return suggest;
			}
			if ( 'inactive' === api.moduleState() ) {
				if ( 'keyword' == type ) {
					suggest = WPGlobusVendor.i18n.yoastseo_plus_meta_keywords_inactive;
				} else if( 'analysis' == type ) {
					suggest = WPGlobusVendor.i18n.yoastseo_plus_page_analysis_inactive;
				} else if( 'readability' == type ) {
					suggest = WPGlobusVendor.i18n.yoastseo_plus_readability_inactive;
				} else if( 'social' == type ) {
					suggest = WPGlobusVendor.i18n.yoastseo_plus_social_inactive;
				}
			} else if( 'boolean' == typeof api.moduleState() && ! api.moduleState() ) {
				if ( 'keyword' == type ) {
					suggest = WPGlobusVendor.i18n.yoastseo_plus_meta_keywords_access;
				} else if( 'analysis' == type ) {
					suggest = WPGlobusVendor.i18n.yoastseo_plus_page_analysis_access;
				} else if( 'readability' == type ) {
					suggest = WPGlobusVendor.i18n.yoastseo_plus_readability_access;
				} else if( 'social' == type ) {
					suggest = WPGlobusVendor.i18n.yoastseo_plus_social_access;
				}			
			}
			suggest = '<div class="wpglobus-suggest" style="font-weight:bold;border:1px solid rgb(221, 221, 221);padding:20px 10px;">'+suggest+'</div>';
			return suggest;
		},
		init: function() {
			if ( api.isBuilderPage() ) {
				api.start();
			}
		},
		start: function() {
			api.accessExtra  = api.parseBool(WPGlobusYoastSeo.access_extra);
			api.setMetaBoxTitle();
			if ( ! api.isDefaultLanguage() ) {
				if ( 'inactive' == api.moduleState() || ! api.moduleState() ) {
					api.setKeywordFieldSuggest();
					api.setSeoAnalysisSuggest();
					api.setReadabilitySuggest();					
					// api.setSocialSuggest(); @since 2.5.16 @W.I.P					
				}
			}
		},
		setSocialSuggest: function() {
			setTimeout( function(){
				var $box = $('#wpseo-section-social');
				if ( $box.length == 1 ) {
					$box.empty().append( api.getSuggest('social') );
				}
			}, 500);
		},
		setKeywordFieldSuggest: function() {
			setTimeout( function(){
				var box = $('#focus-keyword-input-metabox').parent('div');
				if ( box.length == 1 ) {
					box.empty().append( api.getSuggest('keyword') );
				}
			}, 2000);
		},
		setReadabilitySuggest: function() {
			var selector = $('.yoast-aria-tabs li').eq(1);
			$(document).on('click', selector, function(ev) {
				if ( ! api.initReadability ) {
					setTimeout( function(){
						$('#wpseo-meta-section-readability div').each(function(i, elm){
							var $elm = $(elm);
							if ( -1 !== $elm.attr('class').indexOf('ContentAnalysis__ContentAnalysisContainer') ) {
								$elm.empty().append( api.getSuggest('readability') );
								return false;
							}
						});
						api.initReadability = true;
					}, 100);
				}
			});
		},
		setSeoAnalysisSuggest: function() {
			var container;
			setTimeout( function(){
				var containers = $('#yoast-seo-analysis-collapsible-metabox').parents('div');
				if ( 'undefined' !== typeof containers[0] ) {
					container = containers[0];
				}
			}, 500);
			$(document).on('click', container, function(ev) {
				setTimeout( function(){
					var boxAnalysis = false;
					$('#wpseo-metabox-root span').each(function(i, elm){
						var classes = $(elm).attr('class');
						if ( 'undefined' === typeof classes ) {
							return true;
						}
						if ( -1 !== classes.indexOf('SeoAnalysis__') ) {
							var _class = classes.split(' ')[0];
							boxAnalysis = $('.'+_class).next();
							return false;
						}
					});
					if ( boxAnalysis ) {
						boxAnalysis.empty().append( api.getSuggest('analysis') );
					}
				}, 300);
			});
		},
		setMetaBoxTitle: function() {
			var box = $('#wpseo_meta .hndle'); // post.php
			if ( box.length == 1 ) {
				var content = box.text();
				box.text(content+' ('+WPGlobusCoreData.en_language_name[ WPGlobusYoastSeo.language ]+')');
				return;
			}			
			box = $('#wpseo_meta > h2 > span'); // term.php
			if ( box.length == 1 ) {
				var content = box.text();
				box.text(content+' ('+WPGlobusCoreData.en_language_name[ WPGlobusYoastSeo.language ]+')');
			}				
		}
	}
	WPGlobusYoastSeo = $.extend({}, WPGlobusYoastSeo, api);	
	WPGlobusYoastSeo.init();		
});