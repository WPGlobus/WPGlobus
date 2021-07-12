/**
 * WPGlobus for YoastSeo v.14
 * Interface JS functions
 *
 * @since 2.4
 * @since 2.5.16 Removed unneeded code. Small tweaks.
 * @since 2.5.19 Added support multilingual fields for social tab.
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
		isBlockEditor: function(){
			if ( api.isBuilderPage() && 'gutenberg' === WPGlobusYoastSeo.builder_id ) {
				return true;
			}
			return false;
		},
		getSuggest: function(type, attrs){
			var suggest = '', className = 'wpglobus-suggest';
			attrs = attrs || {};
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
			className += ' '+'wpglobus-suggest-'+type;
			var id = '';
			if ( 'undefined' !== typeof attrs.id ) {
				if ( $('#'+attrs.id).length > 0 ) {
					return false;
				}
				id = 'id="'+attrs.id+'"';
			}
			return '<div '+id+' class="'+className+'" style="font-weight:bold;border:1px solid rgb(221, 221, 221);padding:20px 10px;">'+suggest+'</div>';
		},
		init: function() {
			if ( api.isBuilderPage() ) {
				api.start();
			}
		},
		start: function() {
			api.accessExtra  = api.parseBool(WPGlobusYoastSeo.access_extra);
			api.setMetaBoxTitle();
			api.initEditors();
			if ( ! api.isDefaultLanguage() ) {
				if ( 'inactive' == api.moduleState() || ! api.moduleState() ) {
					api.setKeywordFieldSuggest();
					api.setSeoAnalysisSuggest();
					api.setReadabilitySuggest();					
					api.setSocialSuggest();		
					api.attachListeners();					
				}
			}
		},
		setSocialSuggest: function() {
			setTimeout( function(){
				var $box = $('#wpseo-section-social');
				if ( $box.length == 1 ) {
					$box.empty().append( api.getSuggest('social',{id:'wpglobus-suggest-social-metabox'}) );
				}
			}, 500);
			
			if ( api.isBlockEditor() ) {
				// Click by header in sidebar in Block editor mode.
				var modalObserver = new MutationObserver( function( mutations ) {
					mutations.forEach( function( mutation ) {
						var $addedNodes = $(mutation.addedNodes);
						var selector = 'div.yoast-modal-content';
						var $filteredElems = $addedNodes.find(selector);
						if ( $filteredElems.length == 1 ) {
						
							if ( $filteredElems.find('#yoast-snippet-preview-container').length > 0 ) {
								return true;
							}

							var $elems = $filteredElems.find('div');
							$elems.each(function(){
								var $this = $(this);
								var className = $this.attr('class') || '';
								if ( -1 !== className.indexOf('SocialMetadataPreviewForm__') ) {
									var suggest = api.getSuggest('social',{id:'wpglobus-suggest-social-modal'});
									if ( suggest ) {
										$this.after(suggest);
									}
									$this.empty();
								}
								if ( -1 !== className.indexOf('shared__FormSection') ) {
									$this.empty();
								}								
							});
						}							
					} );
				} );
				modalObserver.observe($('body.block-editor-page')[0],{childList:true, subtree:true});
			}
		},
		setKeywordFieldSuggest: function() {
			if ( api.isBlockEditor() ) {
				setTimeout( function(){
					// Sidebar in Block editor mode.
					var box = $('#focus-keyword-input-sidebar').parent('div');
					if ( box.length == 1 ) {
						box.empty().append( api.getSuggest('keyword') );
					}
					// Metabox in Block editor mode.
					box = $('#focus-keyword-input-metabox').parent('div');
					if ( box.length == 1 ) {
						box.empty().append( api.getSuggest('keyword') );
					}					
				}, 2000);					
			} else {
				// Metabox in Standard/Classic mode.
				setTimeout( function(){
					var box = $('#focus-keyword-input-metabox').parent('div');
					if ( box.length == 1 ) {
						box.empty().append( api.getSuggest('keyword') );
					}
				}, 2000);				
			}
		},
		setReadabilitySuggest: function() {
			// Standard/Classic mode.
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
			// Standard/Classic mode.
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
		attachListeners: function() {
			if ( api.isBlockEditor() ) {
				//  Seo Analysis & Readability Analysis in sidebar.
				$(document).on('click', '.yoast.components-panel__body', function(ev){
					setTimeout( function(){
						$('div').each(function(i,e){
							var elmClass = $(e).attr('class');
							if ( 'undefined' != typeof elmClass && -1 != elmClass.indexOf('ContentAnalysis__ContentAnalysisContainer') ) {
								$(e).empty().append( api.getSuggest('analysis') );
							}
						});
					}, 200);	
				});
				// KeywordField in sidebar in Block editor mode.
				var yoastPinnedButton = document.querySelector('[aria-label="Yoast SEO"]');
				$(document).on('click', yoastPinnedButton, function(ev){
					setTimeout( function(){
						// @see setKeywordFieldSuggest function.
						var box = $('#focus-keyword-input-sidebar').parent('div');
						if ( box.length == 1 ) {
							box.empty().append( api.getSuggest('keyword') );
						}
					}, 100);	
				});				
			}			
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
		},
		initEditors: function() {
			if ('term.php' == WPGlobusYoastSeo.pagenow) {
				/**
				 * Init Yoast tinymce editor for description.
				 */
				$(document).on( 'tinymce-editor-init', function( event, editor ) {	
					if ( 'description' == editor.id ) {
						$( '#' + editor.getContainer().id ).find('iframe').addClass(WPGlobusAdmin.builder.translatableClass).css({'width':''});
					}
				});
			}				
		}
	}
	WPGlobusYoastSeo = $.extend({}, WPGlobusYoastSeo, api);	
	WPGlobusYoastSeo.init();		
});