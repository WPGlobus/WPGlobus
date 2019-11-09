/**
 * WPGlobus for YoastSeo 12
 * Interface JS functions
 *
 * @since 2.2.20
 *
 * @package WPGlobus
 */
/*jslint browser: true*/
/*global jQuery, console, wpseoReplaceVarsL10n, WPGlobusVendor, WPGlobusCoreData*/

jQuery(document).ready( function ($) {
	'use strict';

	if ( typeof wpseoReplaceVarsL10n === 'undefined' ) {
		return;
	}

	if ( typeof WPGlobusCoreData === 'undefined' ) {
		return;
	}

	if ( typeof WPGlobusVendor === 'undefined' ) {
		return;
	}

	var api = {
		initSeoAnalysis: false,
		initReadability: false,
		parseBool: function(b)  {
			return !(/^(false|0)$/i).test(b) && !!b;
		},
		moduleState: function(){
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
				}
			} else if( 'boolean' == typeof api.moduleState() && ! api.moduleState() ) {
				if ( 'keyword' == type ) {
					suggest = WPGlobusVendor.i18n.yoastseo_plus_meta_keywords_access;
				} else if( 'analysis' == type ) {
					suggest = WPGlobusVendor.i18n.yoastseo_plus_page_analysis_access;
				} else if( 'readability' == type ) {
					suggest = WPGlobusVendor.i18n.yoastseo_plus_readability_access;
				}				
			}
			suggest = '<div class="wpglobus-suggest" style="font-weight:bold;border:1px solid rgb(221, 221, 221);height:35px;width:90%;padding:8px 10px;">'+suggest+'</div>';
			return suggest;
		},
		init: function() {
			if ( api.isBuilderPage() ) {
				api.start();
			}
		},
		start: function() {
			if ( ! api.isDefaultLanguage() ) {
				api.setMetaBoxTitle();
				if ( 'inactive' == api.moduleState() || ! api.moduleState() ) {
					api.setKeywordField();
					api.setSeoAnalysis();
					api.setReadability();					
				}
			}
		},
		setKeywordField: function() {
			setTimeout( function(){
				var box = $('#focus-keyword-input-metabox').parent('div');
				if ( box.length == 1 ) {
					box.empty().append( api.getSuggest('keyword') );
				}
			}, 2000);
		},
		setReadability: function() {
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
		setSeoAnalysis: function() {
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
						var $elm = $(elm);
						if ( -1 !== $elm.attr('class').indexOf('SeoAnalysis__') ) {
							var _class = $elm.attr('class').split(' ')[0];
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
	WPGlobusYoastSeo = $.extend( {}, WPGlobusYoastSeo, api );	
	WPGlobusYoastSeo.init();		
});