/**
 * WPGlobus for YoastSeo 5.9
 * Interface JS functions
 *
 * @since 1.9.4
 *
 * @package WPGlobus
 */
/*jslint browser: true*/
/*global jQuery, console, wpseoReplaceVarsL10n, YoastSEO, WPGlobusVendor, WPGlobusCore, WPGlobusCoreData*/

var WPGlobusYoastSeo;
/**
 * @see wpglobus-yoastseo-premium-{{version}}.js
 */
var WPGlobusYoastSeoPremium = false;
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

	var api;

	if ( 'edit-tags.php' == WPGlobusVendor.pagenow || 'term.php' == WPGlobusVendor.pagenow ) {

		api = WPGlobusYoastSeo = {
			editorIDs: [ 'description' ],
			editor: {},
			submitId: '',
			preventChangeEditor: false,
			observer: null,
			init: function() {

				api.attachListeners();
				_.delay( api.start, 1500 );

			},
			setSubmitID: function() {
				if ( $('.edit-tag-actions .button').length == 1 ) {
					api.submitId = '.edit-tag-actions .button';
				}
				if ( $('#submit').length == 1 ) {
					api.submitId = '#submit';
				}
			},
			submit: function( event ) {

				if ( 'mouseenter' === event.type ) {

					$.each( api.editor, function( id, d ) {

						if ( ! api.editor[ id ][ 'contentEditor' ] || api.editor[ id ][ 'contentEditor' ].isHidden() ) {

							$( '#'+id ).val( api.editor[ id ].content );

						} else {

							tinymce.get( id ).setContent( api.editor[ id ].content	);
						}

					} );

				} else if ( 'mouseleave' === event.type ) {

					if ( ! api.preventChangeEditor ) {
						/** restore data for current language when submit button wasn't clicked */
						$.each( api.editor, function( id, d ) {

							if ( ! api.editor[ id ].contentEditor || api.editor[ id ].contentEditor.isHidden() ) {

								$( '#'+id ).val(
									WPGlobusCore.TextFilter( d.content, api.getCurrentTab(), 'RETURN_EMPTY' )
								);

							} else {

								tinymce.get( id ).setContent(
									WPGlobusCore.TextFilter( d.content, api.getCurrentTab(), 'RETURN_EMPTY' )
								);

							}

						} );
						api.preventChangeEditor = false;
					}

				} else if ( 'click' === event.type ) {

					api.preventChangeEditor = true;
					if ( 'tinymce' != getUserSetting( 'editor' ) ) {
						setUserSetting( 'editor', 'tinymce' );
					}

				}

			},
			attachListeners: function() {

				/**
				 * Switch language.
				 */
				$( '.wrap' ).on( 'tabsactivate', function( event, ui ) {

					$.each( api.editor, function( ID, ed ) {

						if ( ! api.editor[ ID ][ 'contentEditor' ] || api.editor[ ID ][ 'contentEditor' ].isHidden() ) {

							$( '#'+ID ).val( WPGlobusCore.TextFilter( ed.content, ui.newTab[0].dataset.language, 'RETURN_EMPTY' ) );

						} else {

							tinymce.get( ID ).setContent(
								WPGlobusCore.TextFilter(
									ed.content,
									ui.newTab[0].dataset.language,
									'RETURN_EMPTY'
								)
							);

						}

					} );

				} );

				/**
				 * Submit action.
				 */
				api.setSubmitID();
				$(document).on( 'mouseenter', api.submitId, api.submit )
					.on( 'mouseleave', api.submitId, api.submit )
					.on( 'click', api.submitId, api.submit );


				/**
				 * Yoast tinymce editors init.
S				 */
				$(document).on( 'tinymce-editor-init', function( event, editor ) {

					if ( _.indexOf( api.editorIDs, editor.id ) != -1 ) {

						var $ed = $( '#' + editor.id );

						api.editor[ editor.id ] = {};
						api.editor[ editor.id ][ 'contentEditor' ] = editor;
						api.editor[ editor.id ][ 'content' ] = $ed.val();

						editor.setContent(
							WPGlobusCore.TextFilter(
								api.editor[ editor.id ][ 'content' ],
								WPGlobusCoreData.default_language,
								'RETURN_EMPTY'
							)
						);

						$( '#' + editor.getContainer().id ).find( 'iframe' ).addClass( 'wpglobus-translatable' );
						$ed.removeClass( 'hidden' );

						/** tinymce */
						editor.on( 'nodechange keyup', _.debounce( api.update, 500 ) );

						/** textarea */
						$ed.on( 'input keyup', _.debounce( api.update, 500 ) );
					}

				} );

			},
			update: function( event ) {

				var id, text;

				if ( typeof event.target !== 'undefined' ) {
					id = event.target.id;
				} else {
					return;
				}

				if ( id == 'tinymce' ) {
					id = event.target.dataset.id;
				}

				if ( typeof api.editor[ id ][ 'contentEditor' ] === 'undefined' ) {
					return;
				}

				if ( ! api.editor[ id ][ 'contentEditor' ] || api.editor[ id ][ 'contentEditor' ].isHidden() ) {
					text = $( '#' + id ).val();
				} else {
					text = api.editor[ id ][ 'contentEditor' ].getContent( { format: 'raw' } );
				}

				api.editor[ id ][ 'content' ] = WPGlobusCore.getString( api.editor[ id ][ 'content' ], text, api.getCurrentTab() );

			},
			getCurrentTab: function() {
				return $( '.wpglobus-taxonomy-tabs-list .ui-tabs-active' ).data( 'language' );
			},
			start: function() {

				/** hide all wpglobus description field */
				$( '.wpglobus-element_description' ).css({ 'display':'none' });

			}

		}

		WPGlobusYoastSeo.init();

		var WPGlobusYoastSeoPlugin = function() {

			/**
			 * Switch language.
			 */
			$( '.wrap' ).on( 'tabsactivate', function( event, ui ) {

				if ( ui.newTab[0].dataset.language == WPGlobusCoreData.default_language ) {
					$( '#poststuff .inside' ).css({ 'display':'block' });
				} else {
					$( '#poststuff .inside' ).css({ 'display':'none' });
				}

			} );

			/**
			 * Translate strings.
			 */
			$.each(
				[
					'category_description',
					'sitedesc',
					'sitename',
					'tag_description',
					'term_description',
					'term_title'
				],
				function( i, e ) {
					wpseoReplaceVarsL10n.replace_vars[ e ] = WPGlobusCore.TextFilter(
						wpseoReplaceVarsL10n.replace_vars[ e ],
						WPGlobusCoreData.default_language
					);
				}
			);

			/**
			 * Mutation observer.
			 */
			WPGlobusYoastSeo.observer = new MutationObserver( function( mutations ) {
				mutations.forEach( function( mutation ) {
					if ( 'placeholder' == mutation.attributeName ) {
						WPGlobusYoastSeo.observer.disconnect();
						/**
						 * Update focus keyword.
						 */
						var kw = $( '#wpseo_focuskw' );
						if ( '' == kw.val() ) {
							kw.attr(
								'placeholder',
								WPGlobusCore.TextFilter(
									kw.attr( 'placeholder' ),
									WPGlobusCoreData.default_language
								)
							);
						}
						WPGlobusYoastSeo.observer.observe(
							document.querySelector('#wpseo_focuskw'),
							{ attributes: true, childList: true, characterData: true }
						);

					}
				} );
			} );

			WPGlobusYoastSeo.observer.observe(
				document.querySelector('#wpseo_focuskw'),
				{ attributes: true, childList: true, characterData: true }
			);

		}

		window.WPGlobusYoastSeoPlugin = new WPGlobusYoastSeoPlugin();

	} else {
		/**
		 * pagenow is in [ 'post.php', 'post-new.php' ]
		 */
		/**
		 * WPGlobusYoastSeo.
		 */
		api = WPGlobusYoastSeo = {
			wpseoTabSelector: '#wpglobus-wpseo-tabs',
			url		  :   '',
			attrs	  : 	$('#wpglobus-wpseo-attr'),
			iB		  : 	$('#wpseo-meta-section-content'), // insert before element
			t	      :	$('#wpseo-meta-section-content'), // source
			ids		  : 	'',
			names	  :   '',
			wpseoTab  :   WPGlobusCoreData.default_language,
			editSnippetButtonClass 	: 'wpglobus-snippet-editor__edit-button',
			editSnippetFormClass 	: 'wpglobus-snippet-editor__form',
			editSnippetSubmitClass 	: 'wpglobus-snippet-editor__submit',
			editSnippetHeadingClass : 'wpglobus-snippet-editor__heading-editor',
			yoastTitleProgress		: 'wpglobus-yoast-title-progress',
			yoastMetadescProgress	: 'wpglobus-yoast-metadesc-progress',
			/**
			 * @since 1.7.2
			 */
			yoastPremium			: WPGlobusVendor.vendor.WPSEO_PREMIUM,
			/**
			 * Index of current keywords for each language.
			 * @since 1.7.2
			 */
			keywordIndex			: {},
			/**
			 * @since 1.8
			 */			
			idsSpecial: '',
			init: function() {

				/**
				 * @see wpglobus-admin.js for init point.
				 */
				$.each( WPGlobusCoreData.enabled_languages, function(i,lang){
					api.keywordIndex[lang] = 0;
				});
				api.start();
			},
			initAddKeywordPopup: function() {
				/** @see wp-seo-metabox-302.js */
				/**
				 * Adds keyword popup if the template for it is found.
				 * If add keyword popup exists then bind it to the add keyword button.
				 */
				if ( 1 == $( '#wpseo-add-keyword-popup' ).length ) {
					$( '.wpseo-add-keyword' ).on( 'click', api.addKeywordPopup );
				} else {
					if ( WPGlobusYoastSeoPremium ) {
						WPGlobusYoastSeoPremium.addKeywordButton();
					}
				}
			},
			addKeywordPopup: function() {
				/**
				 * Shows an informational popup if someone click the add keyword button.
				 * @see wp-seo-metabox-302.js
				 */
				var title = $( '#wpseo-add-keyword-popup-button' ).text();
				tb_show( title, '#TB_inline?width=650&height=350&inlineId=wpseo-add-keyword-popup', 'group' );

				/**
				 * The container window isn't the correct size, rectify this.
				 */
				$('#TB_window').css({'width':'680px','height':'350px'});
			},
			qtip: function() {
				/** @see jQuery( '.yoast_help' ).qtip() */
				/** obsolete since yoastseo 3.2 */
				$( '.yoast_help' ).qtip(
					{
						content: {
							attr: 'alt'
						},
						position: {
							my: 'bottom left',
							at: 'top center'
						},
						style   : {
							tip: {
								corner: true
							},
							classes : 'yoast-qtip qtip-rounded qtip-blue'
						},
						show    : 'click',
						hide    : {
							fixed: true,
							delay: 500
						}
					}
				);
			},
			removeClasses: function( element, classes ) {
				_.each( classes, function( cl ){
					element.removeClass( cl );
				} );
			},
			updateProgressBar: function( element, lang ) {

				if ( typeof element === 'undefined' ) {
					return;
				}
				if ( typeof lang === 'undefined' ) {

					if ( element.data( 'language' ) !== 'undefined' ) {
						lang = element.data( 'language' );
					}

					if ( typeof lang === 'undefined' ) {
						return;
					}
				}

				var allClasses = [
					"snippet-editor__progress--bad",
					"snippet-editor__progress--ok",
					"snippet-editor__progress--good"
				];
				var text, progress, score = 0, cl = '', max = 0, warningText = false;

				text = element.val();
				score = text.length;

				if ( element.hasClass( 'wpglobus-snippet-editor-title' ) ) {
					/**
					 * "snippet-editor__progress--ok" - score 0-34
					 * "snippet-editor__progress--good" - score 35-65
					 * "snippet-editor__progress--ok" score >=66, red text
					 */
					progress = $( 'progress.' + api.yoastTitleProgress + '_' + lang );
					max = progress.attr( 'max' );
					cl = allClasses[2];
					if ( score == 0 ) {
						cl = allClasses[1];
					} else if ( score > max ) {
						cl = allClasses[1];
						warningText = true;
					} else if ( score > 0 && score < 35 ) {
						cl = allClasses[1];
					}

				} else if ( element.hasClass( 'wpglobus-snippet-editor-meta-description' ) ) {
					/**
					 * "snippet-editor__progress--ok" - score 0-120
					 * "snippet-editor__progress--good" - score 121-156
					 * "snippet-editor__progress--ok" score >=157, red text
					 */
					progress = $( 'progress.' + api.yoastMetadescProgress + '_' + lang );
					max = progress.attr( 'max' );
					cl = allClasses[2];
					if ( score == 0 ) {
						cl = allClasses[1];
					} else if ( score > max ) {
						cl = allClasses[1];
						warningText = true;
					} else if ( score > 0 && score < 121 ) {
						cl = allClasses[1];
					}
				}
				api.removeClasses( progress, allClasses );
				progress.attr( 'value', score ).addClass( cl );
				if ( warningText ) {
					element.css( 'color', '#f00' );
				} else {
					element.css( 'color', '#000' );
				}

			},
			updateWpseoKeyword: function( kw, l ) {
				/**
				 * Update focus keyword during init time.
				 */
				$( '#wpseo-meta-section-content_' + l + ' .wpseo-keyword'  ).text( kw );
			},
			setSpecial: function() {
				api.idsSpecial 	= WPGlobusYoastSeo.attrs.data('ids-premium-special');
				api.idsSpecial 	= api.idsSpecial.split(',');				
				setTimeout( function(){
					if ( $('.wpseo-cornerstone-checkbox').length > 1 ) {
						/**
						 * Special case for #_yst_is_cornerstone.
						 */
						$.each( WPGlobusCoreData.enabled_languages, function(i,lang){
							if ( lang == WPGlobusCoreData.default_language ) {
								/**
								 * Rename original '_yst_is_cornerstone'.
								 */
								var special = $('#wpseofocuskeyword input#_yst_is_cornerstone');
								$(special).attr('id', '_yst_is_cornerstone_origin').attr('name', '_yst_is_cornerstone_origin');
								$(special).addClass('wpseo-cornerstone-checkbox_origin');
								return true;
							}
							/**
							 * Remove '_yst_is_cornerstone' for extra language.
							 */
							$('#wpseo-tab-'+lang+' .wpseo-cornerstone-checkbox').remove();
							$('#wpseo-tab-'+lang+' label[for="_yst_is_cornerstone"]').remove();
						});
					}

					if ( $('.wpglobus-wpseometakeywords').length > 1 ) {
						/**
						 * Meta keywords.
						 * Special case for #wpseometakeywords box.
						 * @from 1.8.8
						 */
						/**
						 * Trigger handler.
						 */
						var _handleKeywords = $(document).triggerHandler('wpglobus_meta_keywords');
						if ( 'undefined' !== typeof _handleKeywords ) {
							if ( true === _handleKeywords ) {
								return;
							}
						}
						$.each( WPGlobusCoreData.enabled_languages, function(i,lang){
							if ( lang == WPGlobusCoreData.default_language ) {
								/**
								 * Rename original 'wpseometakeywords'.
								 */
								var special = $('#yoast_wpseo_metakeywords');
								special.attr('id', 'yoast_wpseo_metakeywords_origin').attr('name', 'yoast_wpseo_metakeywords_origin');
								special.addClass('metakeywords_origin');
								$('#wpseometakeywords').attr('id','wpseometakeywords_origin');
								
								$('#yoast_wpseo_metakeywords_'+lang).attr('name', 'yoast_wpseo_metakeywords').attr('id','yoast_wpseo_metakeywords');
								$('#wpseometakeywords_'+lang).attr('id','wpseometakeywords');
								return true;
							}
							/**
							 * Remove 'wpseometakeywords' box for extra language.
							 */
							$('#wpseo-tab-'+lang+' #wpseometakeywords_'+lang+' .yoast-metabox__description').remove();
							$('#wpseo-tab-'+lang+' #yoast_wpseo_metakeywords_'+lang).remove();
							$('#wpseometakeywords_'+lang+' .yoast-section').append('<div class="wpglobus-suggest" style="font-weight:bold;">'+WPGlobusVendor.i18n.yoastseo_plus_meta_keywords_access+'</div>');
						});							
					}
					
				}, 2000 );					
			},
			start: function() {
				/**
				 * Tabs on.
				 */
				$( api.wpseoTabSelector ).tabs();
				
				/**
				 * @since 1.8
				 */
				if ( WPGlobusYoastSeoPremium ) {
					/**
					 * reserved.
					 */
				} else {
					api.setSpecial();
				}
				
				api.ids 	= api.attrs.data('ids');
				api.names 	= api.attrs.data('names');

				api.ids 	= api.ids + ',' + api.attrs.data('qtip');
				api.ids 	= api.ids.split(',');
				api.names 	= api.names.split(',');

				$('#wpglobus-wpseo-tabs').insertBefore( api.iB );
				$('.wpseo-metabox-tabs').css({'height':'26px'});

				$('.wpglobus-wpseo-general').each(function(i,e){
					var $e = $(e);
					var l  = $e.data('language');
					var sectionID = 'wpseo-meta-section-content_'+l;

					$e.html('<div id="'+sectionID+'" class="wpseo-meta-section wpglobus-wpseo-meta-section" style="width:100%" data-language="'+l+'">' + api.t.html() + '</div>');
					$('#'+sectionID+' .wpseo-metabox-tabs').attr( 'id', 'wpseo-metabox-tabs_'+l ).attr( 'data-language', l );
					$('#'+sectionID+' .wpseotab').attr( 'id', 'wpseo_content_'+l );
					/** added since yoastseo 3.2 */
					$('#wpseo_content_'+l).css({'float':'left'});
					$('#'+sectionID).css({'display':'block'});
					$('#wpseo_meta').css({'overflow':'hidden'});

					$('#'+sectionID+' .snippet_container').addClass('wpglobus-snippet_container');

					if ( l !== WPGlobusCoreData.default_language ) {
						/**
						 * Hide plus sign.
						 */
						$('#'+sectionID+' .wpseo-add-keyword').addClass('hidden');
					}

					$.each( api.names, function(i,name) {
						$( '#'+name ).attr( 'name', name+'_'+l );
					});

					$.each( api.ids, function(i,id) {
						var $id = $('#'+id);
						if ( 'wpseosnippet' == id ) {
							$id.addClass('wpglobus-wpseosnippet');
						}
						if ( 'snippet_title' == id ) {
							$id.addClass('wpglobus-snippet_title');
						}
						if ( 'snippet_meta' == id ) {
							$id.addClass('wpglobus-snippet_meta');
						}
						/** url */
						if ( 'snippet_cite' == id ) {
							$id.addClass('wpglobus-snippet_cite');
						}
						if ( 'snippet_citeBase' == id ) {
							$id.addClass('wpglobus-snippet_citeBase');
						}
						/** @since 1.5.6 */
						if ( 'url_container' == id ) {
							$id.css( { 'height':'16px'} );
						}
						/** focuskw */
						if ( 'yoast_wpseo_focuskw_text_input' == id ) {
							$id.addClass('wpglobus-yoast_wpseo_focuskw_text_input');
							/**
							 * Add attribute to store first keyword.
							 * @since 1.7.2
							 */
							$id.attr('data-value', '');
							$id.parents( 'div#wpseofocuskeyword' ).attr( 'id', 'wpseofocuskeyword_' + l );
							/*
							 * @since 1.8
							 */							
							$('#wpseofocuskeyword_' + l).addClass('wpglobus-wpseofocuskeyword');
							/**
							 * @todo check for '#wpseo-focuskeyword-section', '#help-yoast-focuskeyword', label for="yoast_wpseo_focuskw_text_input".
							 */
						}
						/** @since 1.7.2 */
						if ( 'yoast_wpseo_focuskeywords' == id ) {
							$id.addClass('wpglobus-yoast_wpseo_focuskeywords');
						}
						/** wpseo-pageanalysis */
						if ( 'wpseo-pageanalysis' == id ) {
							$id.addClass( 'wpglobus-wpseo-pageanalysis' );
						}
						/**
						 * yoast-seo-content-analysis
						 * @since 1.5.8
						 */
						if ( 'yoast-seo-content-analysis' == id ) {
							$id.addClass( 'wpglobus-yoast-seo-content-analysis' );
						}
						/** #snippet_preview */
						if ( 'snippet_preview' == id ) {
							$id.addClass('wpglobus-snippet_preview');
						}
						/** #snippet-editor-title */
						if ( 'snippet-editor-title' == id ) {
							$id.addClass( 'wpglobus-snippet-editor-title' );
							$id.parent( 'label' ).attr( 'for', $id.parent( 'label' ).attr( 'for' ) + '_' + l );
							$id.val( $( '#wpseo-tab-' + l ).data( 'wpseotitle' ) );

							//$id.parent( 'label' ).find( 'progress' ).addClass( api.yoastTitleProgress ).addClass( api.yoastTitleProgress + '_' + l );
							/** @since yoastseo 3.3.1 */
							$id.parent( 'label' ).next( 'progress' ).addClass( api.yoastTitleProgress ).addClass( api.yoastTitleProgress + '_' + l );
							_.debounce( api.updateProgressBar( $id,	l ), 500 );
						}
						/** #snippet-editor-slug */
						if ( 'snippet-editor-slug' == id ) {
							$id.addClass('wpglobus-snippet-editor-slug');
							$id.parent( 'label' ).attr( 'for', $id.parent( 'label' ).attr( 'for' ) + '_' + l );
							if ( WPGlobusCoreData.default_language != l ) {
								/** disable slug field by default */
								$id.attr( 'disabled', 'disabled' );
							}
						}
						/** #snippet-editor-meta-description */
						if ( 'snippet-editor-meta-description' == id ) {
							$id.addClass( 'wpglobus-snippet-editor-meta-description' );
							$id.parent( 'label' ).attr( 'for', $id.parent( 'label' ).attr( 'for' ) + '_' + l );
							$id.val( $( '#wpseo-tab-' + l ).data( 'metadesc' ) );

							//$id.parent( 'label' ).find( 'progress' ).addClass( api.yoastMetadescProgress ).addClass( api.yoastMetadescProgress + '_' + l );
							// @since yoastseo 3.3.1
							$id.parent( 'label' ).next( 'progress' ).addClass( api.yoastMetadescProgress ).addClass( api.yoastMetadescProgress + '_' + l );
							_.debounce( api.updateProgressBar( $id,	l ), 500 );
						}
						/** #snippetpreview-help-toggle */
						if ( 'snippetpreview-help-toggle' == id ) {
							$id.addClass( 'wpglobus-snippetpreview-help-toggle_'+l );
							$(document).on( 'click', '.wpglobus-snippetpreview-help-toggle_'+l, function(ev){
								var $t = $(this);
								if ( $( '#snippetpreview-help_'+$t.data( 'language' ) ).css('display') === 'none' ) {
									$( '#snippetpreview-help_'+$t.data( 'language' ) ).css({'display':'block'});
								} else {
									$( '#snippetpreview-help_'+$t.data( 'language' ) ).css({'display':'none'});
								}
							});
						}
						/** #focuskw_text_input-help-toggle	*/
						if ( 'focuskw_text_input-help-toggle' == id ) {
							$id.addClass( 'wpglobus-focuskw_text_input-help-toggle_'+l );
							$(document).on( 'click', '.wpglobus-focuskw_text_input-help-toggle_'+l, function(ev){
								var $t = $(this);
								if ( $( '#focuskw_text_input-help_'+$t.data( 'language' ) ).css('display') === 'none' ) {
									$( '#focuskw_text_input-help_'+$t.data( 'language' ) ).css({'display':'block'});
								} else {
									$( '#focuskw_text_input-help_'+$t.data( 'language' ) ).css({'display':'none'});
								}
							});
						}
						/** #snippetpreview-help	*/
						if ( 'pageanalysis-help-toggle' == id ) {
							$id.addClass( 'wpglobus-pageanalysis-help-toggle_'+l );
							$(document).on( 'click', '.wpglobus-pageanalysis-help-toggle_'+l, function(ev){
								var $t = $(this);
								if ( $( '#pageanalysis-help_'+$t.data( 'language' ) ).css('display') === 'none' ) {
									$( '#pageanalysis-help_'+$t.data( 'language' ) ).css({'display':'block'});
								} else {
									$( '#pageanalysis-help_'+$t.data( 'language' ) ).css({'display':'none'});
								}
							});
						}
						
						/** 
						 * #snippet_preview
						 * @from 1.7.9
						 */
						if ( 'snippet_preview' == id ) {
							if ( ! WPGlobusYoastSeoPremium ) {
								$id.css({'margin-bottom':'20px'});
							}
						}

						/** 
						 * #wpseo-focuskeyword-section
						 * @from 1.7.9
						 */
						if ( 'wpseo-focuskeyword-section' == id ) {
							if ( ! WPGlobusYoastSeoPremium ) {							
								$id.css({'margin-bottom':'20px'});
							}
						}						
		
						/** 
						 * #pageanalysis 
						 * @since 1.7.9
						 * obsolete @since 1.9.4
						 */
						/** 
						if ( 'pageanalysis' == id ) {
							if ( ! WPGlobusYoastSeoPremium ) {
								if ( l == WPGlobusCoreData.default_language ) {
									
									setTimeout( function() {
										var h = 0, hDefault = 0;
										var l = WPGlobusCoreData.default_language;
										$('#pageanalysis_'+l+ ' #wpseo-pageanalysis_'+l+' li.score').each(function(i,e) {
											hDefault = hDefault + $(e).outerHeight(true);
										});		
										$.each( WPGlobusCoreData.enabled_languages, function(i,lang){
											h = hDefault + 50;
											$('#pageanalysis_'+lang).css({'height':h+'px'});

										});
									}, 4000 );
								}
							}
						}  // */
						
						/**
						 * #wpseo-pageanalysis-section
						 * @from 1.7.9
						 */
						if ( 'wpseo-pageanalysis-section' == id ) {
							if ( WPGlobusYoastSeoPremium ) {
								/**
								 * @from 1.8
								 */
								$id.css({'overflow':'overlay'});
							} else {
								$id.css({'height':'inherit'});
							}
						}	

						/**
						 * Meta keywords.
						 * @from 1.8.8
						 */
						if ( 'wpseometakeywords' == id ) {
							$id.css({'margin-bottom':'2em'});
							$id.addClass('wpglobus-wpseometakeywords');
						}
						if ( 'yoast_wpseo_metakeywords' == id ) {
							$id.addClass('wpglobus-metakeywords');
							$('input[name="yoast_wpseo_metakeywords_'+l+'"]').val( WPGlobusCore.TextFilter($('#yoast_wpseo_metakeywords').val(), l, 'RETURN_EMPTY') );
						}
	
						/**
						 * Generate unique id.
						 */
						$id.attr( 'id', id+'_'+l );
						$( '#'+id+'_'+l ).attr( 'data-language', l );

					});

					/**
					 * Set focus keywords for every language.
					 */
					var focuskw = WPGlobusCore.TextFilter( $('#yoast_wpseo_focuskw_text_input').val(), l, 'RETURN_EMPTY' );
					$( '#yoast_wpseo_focuskw_text_input_'+l ).val( focuskw ).data( 'value', focuskw );
					/** since yoastseo 3.2 */

					$( '#yoast_wpseo_focuskw_'+l ).val( focuskw );
					
					/**
					 * Set data-keyword for yoast seo.
					 * @since 1.7.9
					 */
					if ( ! WPGlobusYoastSeoPremium ) { 
						$('#wpseo-metabox-tabs_'+l+' .wpseo_keyword_tab .wpseo_tablink').data('keyword', focuskw);
					}
					
					/**
					 * Set min-width, min-height for keyword tab to prevent the shifting of the elements when keyword is empty.
					 * @since 1.9.4
					 */					
					$('#wpseo-metabox-tabs_'+l+' .wpseo_keyword_tab').css({'min-width':'60px','min-height':'29px'});
					
					/** since yoastseo 3.2 */
					api.updateWpseoKeyword( focuskw, l );

					if ( l !== WPGlobusCoreData.default_language ) {
						$('#'+sectionID+' #yoast_wpseo_focuskw_text_input_'+l)
							.addClass('hidden')
							.after('<div class="wpglobus-suggest" style="font-weight:bold;">'+WPGlobusVendor.i18n.yoastseo_plus_page_analysis_access+'</div>');

						$('#'+sectionID+' #wpseo-pageanalysis_'+l).addClass('hidden').css({'display':'none'});
					}

					if ( WPGlobusYoastSeoPremium ) {
						/**
						 * @since 1.7.2
						 */
						if ( l == WPGlobusCoreData.default_language ) {
							/**
							 * Do nothing.
							 */
						} else {
							$( '#wpseo-metabox-tabs_'+l+' .wpseo_keyword_tab' ).removeClass('wpseo_keyword_tab').addClass('wpglobus-wpseo_keyword_tab');
							/**
							 * Hide keywords tabs with extra keywords for extra language after page was loaded.
							 */
							$( '#wpseo-meta-section-content_'+l+' .wpseo_keyword_tab_hideable' ).css({'display':'none'}).attr('data-language',l);
						}

					}

				}); /** end each .wpglobus-wpseo-general */

				/** add wpglobus classes to 'Edit snippet' button and hidden form */
				$( '.wpglobus-wpseosnippet' ).each( function(i,e) {
					var $e = $(e);
					var l  = $e.data( 'language' );
					$e.find( 'button.snippet-editor__button.snippet-editor__edit-button' )
						.addClass( api.editSnippetButtonClass ).addClass( api.editSnippetButtonClass+'_'+l ).attr( 'data-language', l );
					$e.find( '.snippet-editor__form' ).addClass( api.editSnippetFormClass ).addClass( api.editSnippetFormClass+'_'+l );
					$e.find( '.snippet-editor__heading-editor' ).addClass( api.editSnippetHeadingClass ).addClass( api.editSnippetHeadingClass+'_'+l );
					$e.find( '.snippet-editor__submit' )
						.addClass( api.editSnippetSubmitClass ).addClass( api.editSnippetSubmitClass+'_'+l ).attr( 'data-language', l );
				});

				/**
				 * Hide original section content.
				 */
				api.iB.addClass( 'hidden' );
				api.iB.css({'height':0,'overflow':'hidden','display':'none'});

				if ( WPGlobusYoastSeoPremium ) {
					/**
					 * Class wpseo_keyword_tab using for counting of keywords in WPSEO_PREMIUM.
					 * So we need to change original .wpseo_keyword_tab class to something else.
					 * @since 1.7.2
					 */
					$('#wpseo-meta-section-content .wpseo_keyword_tab').removeClass('wpseo_keyword_tab').addClass('wpseo_keyword_tab_original');
				}

				/**
				 * Set focuskw to default language.
				 */
				var focuskw_d = WPGlobusCore.TextFilter( $('#yoast_wpseo_focuskw_text_input').val(), WPGlobusCoreData.default_language, 'RETURN_EMPTY' );
				$( '#yoast_wpseo_focuskw_text_input' ).val( focuskw_d );
				$( '#yoast_wpseo_focuskw' ).val( focuskw_d );

				/**
				 * Make switchable wpseo_generic_tab & (wpseo_keyword_tab or wpglobus-wpseo_keyword_tab).
				 * @see element #yoast-seo-content-analysis
				 * @since 1.5.10
				 */
				$(document).on( 'click', '.wpglobus-wpseo-general .wpseo_tablink', function(ev) {
					ev.preventDefault();
					var $t = $(this),
						l = $t.parents( 'ul' ).data( 'language' ),
						tab = $t.parent( 'li' );

					if ( l != WPGlobusCoreData.default_language ) {
						/**
						 * Trigger handler.
						 */
						if ( 'undefined' === typeof $(document).triggerHandler( 'wpglobus_yoast_analysis', {language:l} ) ) {
							return;
						}
					}

					if ( tab.hasClass( 'wpseo_generic_tab' ) ) {
						/**
						 * Readability tab.
						 */
						$( '#wpseo-pageanalysis_' + l ).css({'display':'none'});
						$( '#yoast-seo-content-analysis_' + l ).css({'display':'block'});
						$( '#wpseo_content_' + l + ' table tr' ).eq(0).css({'display':'none'});
						$( '#wpseo_content_' + l + ' table tr' ).eq(1).css({'display':'none'});
						$( '#yoast-seo-content-analysis_' + l + ' li a' ).css({'float':'none'});
						
						/**
						 * Hide Snippet preview box.
						 * @since 1.8
						 */
						 $('#wpseosnippet_'+l).css({'display':'none'});
		
						/**
						 * Hide Focus keyword box.
						 * @since 1.8
						 */
						 $('#wpseofocuskeyword_'+l).css({'display':'none'});		
						 
					} else if ( tab.hasClass( 'wpseo_keyword_tab' ) ) {
						
						/**
						 * Show Snippet preview box.
						 * @since 1.8
						 */
						 $('#wpseosnippet_'+l).css({'display':''});
		
						/**
						 * Show Focus keyword box.
						 * @since 1.8
						 */
						 $('#wpseofocuskeyword_'+l).css({'display':''});							
						
						/**
						 * Keyword tab with default language.
						 */
						if ( WPGlobusYoastSeoPremium ) {
							/**
							 * Reset active status before activate tab.
							 */
							$('#wpseo-meta-section-content_'+l+' .wpseo_keyword_tab').removeClass('active');

							$t.parent('.wpseo_keyword_tab').addClass('active');
							
							/**
							 * @since 1.7.9
							 */
							$( '#wpseo-pageanalysis_' + l ).css({'display':'block'});
							$( '#yoast-seo-content-analysis_' + l ).css({'display':'none'});
							$( '#wpseo_content_' + l + ' table tr' ).eq(0).css('display','');
							$( '#wpseo_content_' + l + ' table tr' ).eq(1).css('display','');
							
							/**
							 * Update keyword index.
							 * @since 1.7.2
							 */
							$( '#wpseo-metabox-tabs_'+l+ ' .wpseo_keyword_tab' ).each( function(i,e){
								if ( $(e).hasClass( 'active' ) ) {
									api.keywordIndex[l] = i;
									return false;
								}
							});
						}

						/**
						 * Correct switching Readability/Keyword tabs for extra languages in yoast seo.
						 * @since 1.7.9
						 */
						if ( ! WPGlobusYoastSeoPremium && l != WPGlobusCoreData.default_language ) {
							$( '#wpseo-pageanalysis_' + l ).css({'display':'block'});
							$( '#yoast-seo-content-analysis_' + l ).css({'display':'none'});
							$( '#wpseo_content_' + l + ' table tr' ).eq(0).css('display','');
							$( '#wpseo_content_' + l + ' table tr' ).eq(1).css('display','');							
							return;
						}
						
						var keywords = api.getKeywordsOrig();
						var keyword  = keywords[api.keywordIndex[l]]['keyword'];

						YoastSEO.app.rawData.keyword = keyword;
						$('#yoast_wpseo_focuskw_text_input').val( keyword );
						$('#yoast_wpseo_focuskw_text_input_'+l ).val( keyword );
						$('input[name="yoast_wpseo_focuskw"]').val( keyword );
						WPGlobusYoastSeoPlugin.prototype.analyze();

						$( '#wpseo-pageanalysis_' + l ).css({'display':'block'});
						$( '#yoast-seo-content-analysis_' + l ).css({'display':'none'});
						$( '#wpseo_content_' + l + ' table tr' ).eq(0).css('display','');
						$( '#wpseo_content_' + l + ' table tr' ).eq(1).css('display','');

					} else if ( tab.hasClass( 'wpglobus-wpseo_keyword_tab' ) ) {
						/**
						 * @since 1.7.9
						 */
						$( '#wpseo-pageanalysis_' + l ).css({'display':'block'});
						$( '#yoast-seo-content-analysis_' + l ).css({'display':'none'});
						$( '#wpseo_content_' + l + ' table tr' ).eq(0).css('display','');
						$( '#wpseo_content_' + l + ' table tr' ).eq(1).css('display','');						 
					}

				});

				/**
				 * wpseo-metabox-sidebar.
				 */
				$('.wpseo-metabox-sidebar .wpseo-meta-section-link').on( 'click',function(ev){
					if ( $(this).attr('href') == '#wpseo-meta-section-content' ) {
						$('#wpglobus-wpseo-tabs').css({'display':'block'});
					} else {
						$('#wpglobus-wpseo-tabs').css({'display':'none'});
					}
				});

				/**
				 * Open form to edit snippet.
				 */
				$(document).on( 'click', '.'+api.editSnippetButtonClass, function(event){
					var $t = $(this);
					var l  = $t.data( 'language' );
					var sform   = $( '.' + api.editSnippetFormClass + '_' + l );
					var sbutton = $( '.' + api.editSnippetSubmitClass + '_' + l );
					var heading = $( '.' + api.editSnippetHeadingClass+'_'+l );

					if ( sform.hasClass( 'snippet-editor--hidden' ) )  {
						sform.removeClass( 'snippet-editor--hidden' );
						heading.removeClass( 'snippet-editor--hidden' );
						sbutton.removeClass( 'snippet-editor--hidden' );
					} else {
						sform.addClass( 'snippet-editor--hidden' );
						heading.addClass( 'snippet-editor--hidden' );
					}
					$t.addClass( 'snippet-editor--hidden' );
				});

				/**
				 * To close snippet editor.
				 */
				$(document).on( 'click', '.'+api.editSnippetSubmitClass, function(event){
					var $t = $(this);
					var l  = $t.data( 'language' );
					var button  = $( '.' + api.editSnippetButtonClass + '_' + l );
					var sform  	= $( '.' + api.editSnippetFormClass + '_' + l );
					var sbutton = $( '.' + api.editSnippetSubmitClass + '_' + l );
					var heading = $( '.' + api.editSnippetHeadingClass+'_'+l );

					button.removeClass( 'snippet-editor--hidden' );
					sform.addClass( 'snippet-editor--hidden' );
					sbutton.addClass( 'snippet-editor--hidden' );
					heading.addClass( 'snippet-editor--hidden' );
				});

				/**
				 * Make title.
				 */
				$(document).on( 'keyup', 'input.wpglobus-snippet-editor-title', function(event){
					var $t = $(this),
						l = $t.data( 'language' ),
						s = WPGlobusCore.getString( $( 'input[name="yoast_wpseo_title"]' ).val(), $t.val(), l );

					$( '#snippet_title_'+l ).html( _this.replaceVariablesPlugin( $t.val() ) );

					YoastSEO.app.rawData.pageTitle = s;  /** @todo maybe set at start js ? */

					/** $('#yoast_wpseo_title').val( s );  @todo don't work with id */
					$( 'input[name="yoast_wpseo_title"]' ).val( s );
					$( '#snippet_title' ).text( s );
					_.debounce( api.updateProgressBar( $t,	l ), 500 );
				});

				/**
				 * Make slug.
				 */
				$(document).on( 'keyup', 'input.wpglobus-snippet-editor-slug', function(event){
					var $t = $(this), l = $t.data( 'language' );

					$( '#snippet_cite_' + l ).text( $t.val() + '/' );
					$( '#editable-post-name' ).text( $t.val() );
					$( '#editable-post-name-full' ).text( $t.val() );
				});

				/**
				 * @todo add doc.
				 */
				$(document).on( 'change', 'input.wpglobus-snippet-editor-slug', function(event){
					var $t = $(this);
					/**
					 * @see editPermalink() in /wp-admin/js/post.js
					 */
					$.post(ajaxurl, {
						action: 'sample-permalink',
						post_id: $('#post_ID').val() || 0,
						new_slug: $t.val(),
						new_title: $('#title').val(),
						samplepermalinknonce: $('#samplepermalinknonce').val()
					});

				});

				/**
				 * Make meta description.
				 */
				$(document).on( 'keyup', 'textarea.wpglobus-snippet-editor-meta-description', function(event){
					var $t = $(this),
						l = $t.data( 'language' );
					$( '#snippet_meta_'+l ).text( $t.val() );

					var s = WPGlobusCore.getString( $('#yoast_wpseo_metadesc').val(), $t.val(), $t.data('language') );
					$( '#yoast_wpseo_metadesc' ).val( s );
					$( '#snippet_meta' ).text( s );
					_.debounce( api.updateProgressBar( $t,	l ), 500 );
				});

				/**
				 * Make synchronization click on "Post tab" with seo tab.
				 */
				$(document).on( 'click', '.wpglobus-post-body-tabs-list li', function(event){
					var $t = $(this);
					if ( $t.hasClass('wpglobus-post-tab') ) {
						/**
						 * @see 'tabsactivate' action.
						 */
						$('#wpglobus-wpseo-tabs').tabs( 'option', 'active', $t.data('order') );

						/**
						 * Set keyword.
						 */
						var k = $( '#yoast_wpseo_focuskw_text_input_' + $t.data('language') ).val();
						YoastSEO.app.rawData.keyword = k ;
						$('#yoast_wpseo_focuskw_text_input').val( k );
						$('input[name="yoast_wpseo_focuskw"]').val( k );

						WPGlobusYoastSeoPlugin.prototype.analyze();
					}
				});
				
				/**
				 * @since 1.9.4
				 */
				$(document).on('click', '.wpglobus-wpseo-pageanalysis h4', function(event){
					$(this).next('ul').toggleClass('hidden');
				});
				/**
				$(document).on('click', '.wpglobus-yoast-seo-content-analysis h4', function(event){
					$(this).next('ul').toggleClass('hidden');
				}); 
				// */
				
				api.initAddKeywordPopup();

			},
			getKeywordsOrig: function(){
				/**
				 * Retrieves the current keywords.
				 * @see YoastMultiKeyword.prototype.getKeywords
				 */
				return $(".wpseo_keyword_tab").map(function (i, keywordTab) {
					keywordTab = $(keywordTab).find(".wpseo_tablink");

					return {
						// Convert to string to prevent errors if the keyword is "null".
						keyword: keywordTab.data("keyword") + "",
						score: keywordTab.data("score")
					};
				}).get();
			}
		} /** end WPGlobusYoastSeo */

		/********/
		var _this;
		var WPGlobusYoastSeoPlugin = function() {

			this.replaceVars 	= wpseoReplaceVarsL10n.replace_vars;
			this.language 	 	= WPGlobusCoreData.default_language;
			this.tab 	 	 	= WPGlobusCoreData.default_language;
			this.wpseoTab 	 	= WPGlobusCoreData.default_language;

			this.title_template = wpseoPostScraperL10n.title_template;

			this.focuskw		= $('#yoast_wpseo_focuskw_text_input');
			this.focuskw_hidden	= $('input[name="yoast_wpseo_focuskw"]');
			this.focuskwKeep	= false;

			this.post_slug 		= '#editable-post-name-full';

			YoastSEO.app.registerPlugin( 'wpglobusYoastSeoPlugin', {status: 'ready'} );

			/**
			* @param modification    {string}    The name of the filter
			* @param callable        {function}  The callable
			* @param pluginName      {string}    The plugin that is registering the modification.
			* @param priority        {number}    (optional) Used to specify the order in which the callables
			*                                    associated with a particular filter are called. Lower numbers
			*                                    correspond with earlier execution.
			*
			* @see wp-seo-replacevar-plugin-320.js
			*/
			YoastSEO.app.registerModification( 'content', this.contentModification, 'wpglobusYoastSeoPlugin', 0 );
			YoastSEO.app.registerModification( 'title', this.titleModification, 'wpglobusYoastSeoPlugin', 0 );

			YoastSEO.app.registerModification( 'snippet_title', this.snippetModification, 'wpglobusYoastSeoPlugin', 0 );
			YoastSEO.app.registerModification( 'snippet_meta', this.snippetModification, 'wpglobusYoastSeoPlugin', 0 );

			YoastSEO.app.registerModification( 'data_page_title', this.pageTitleModification, 'wpglobusYoastSeoPlugin', 0 );
			YoastSEO.app.registerModification( 'data_meta_desc', this.metaDescModification, 'wpglobusYoastSeoPlugin', 0 );

			WPGlobusYoastSeoPlugin.prototype.setScoreIcon();

			$(document).on( 'blur', '.wpglobus-snippet_title', function(ev){
				var $t = $(this);
				var s = WPGlobusCore.getString( $('#yoast_wpseo_title').val(), $t.text(), $t.data('language') );

				YoastSEO.app.rawData.pageTitle = s;  // @todo maybe set at start js ?

				//$('#yoast_wpseo_title').val( s );  // @todo don't work with id
				$('input[name="yoast_wpseo_title"]').val( s );
				$('#snippet_title').text( s );
			});

			$(document).on( 'blur', '.wpglobus-snippet_meta', function(ev){
				var $t = $(this);
				var s = WPGlobusCore.getString( $('#yoast_wpseo_metadesc').val(), $t.text(), $t.data('language') );
				$( '#yoast_wpseo_metadesc' ).val( s );
				$( '#snippet_meta' ).text( s );

			});

			/**
			 * Handle event of keyword's change.
			 */
			$(document).on( 'keyup', '.wpglobus-yoast_wpseo_focuskw_text_input', function(ev){
				var $t 	 = $(this);
				var s 	 = $t.val(), lang = $t.data('language');
				_this.focuskw.val( s );
				_this.focuskw_hidden.val( s );

				/**
				 * Set data-keyword for yoast seo.
				 * @since 1.7.9
				 */
				if ( ! WPGlobusYoastSeoPremium ) { 
					$('#wpseo-metabox-tabs_'+lang+' .wpseo_keyword_tab .wpseo_tablink').data('keyword', s);
				}
				
				_this.updateWpseoKeyword( s, lang );
				/**
				 * @since 1.7.2
				 */
				if ( 0 == WPGlobusYoastSeo.keywordIndex[lang] ) {
					/**
					 * Save first keyword in 'data-value' attribute.
					 */
					$( '#yoast_wpseo_focuskw_text_input_'+lang ).data('value', s);
				} else {
					if ( WPGlobusYoastSeoPremium ) {
						WPGlobusYoastSeoPremium.setFocuskeyword( lang );
					}

				}
				/**
				 * @since yoastseo 3.2.
				 */
				WPGlobusYoastSeoPlugin.prototype.analyze();
			});

			/**
			 * Pre-save action.
			 */
			$( '#publish,#save-post' ).on( 'mouseenter', function(event){
				/**
				 * Save first keywords for all languages.
				 */
				var $t, s = '';
				$('.wpglobus-yoast_wpseo_focuskw_text_input').each( function(i,e){
					$t = $(this);
					if ( $t.data('language') == WPGlobusCoreData.default_language ) {
						if ( WPGlobusYoastSeoPremium ) {
							var keywords = WPGlobusYoastSeo.getKeywordsOrig();
							s = WPGlobusCore.getString( s, keywords[0]['keyword'], $t.data('language') );
						} else {
							s = WPGlobusCore.getString( s, $t.val(), $t.data('language') );
						}
					} else {
						s = WPGlobusCore.getString( s, $t.val(), $t.data('language') );
					}
				});
				_this.focuskw.val( s );
				_this.focuskw_hidden.val( s );
			}).on( 'mouseleave', function(event) {
				if ( ! _this.focuskwKeep ) {
					_this.wpseoTab = $('.wpglobus-wpseo-tabs-list .ui-tabs-active').data('language');
					var $t = $(this);
					_this.focuskw.val( $('#yoast_wpseo_focuskw_text_input_'+_this.wpseoTab ).val() );
					_this.focuskw_hidden.val( $('#yoast_wpseo_focuskw_text_input_'+_this.wpseoTab ).val() );
				}
			}).on( 'click', function(event){
				_this.focuskwKeep = true;
			});

			/**
			 * The action when language's tab was activated in Yoast SEO or Yoast SEO Premium metabox 
			 * or synchronization was fired from "Post tab".
			 * It is fired when user come from other language tab.
			 * @see action ['click', '.wpglobus-post-body-tabs-list li']
			 */
			$(document).on( 'tabsactivate', WPGlobusYoastSeo.wpseoTabSelector, function(event, ui){
				_this.language = ui.newPanel.attr( 'data-language' );
				WPGlobusYoastSeo.wpseoTab = _this.language;
				if ( ui.newPanel.attr( 'data-language' ) === WPGlobusCoreData.default_language ) {
					/**
					 * Set keyword.
					 */
					var k;
					if ( WPGlobusYoastSeoPremium ) {
						/**
						 * @todo To use wpglobus-wpseo_keyword_tab class instead of wpseo_keyword_tab for extra languages.
						 * @since 1.7.2
						 */
						$('#wpseo-meta-section-content_'+WPGlobusYoastSeoPremium.dLang+' .wpseo_keyword_tab').removeClass('active');
						var tab = $('#wpseo-meta-section-content_'+WPGlobusYoastSeoPremium.dLang+' .wpseo_keyword_tab').eq(WPGlobusYoastSeo.keywordIndex[WPGlobusYoastSeoPremium.dLang]);
						tab.addClass('active');
						/**
						 * We could loose keyword for default language.
						 * Let's restore it.
						 */
						k = $( '#yoast_wpseo_focuskw_text_input_' + WPGlobusYoastSeoPremium.dLang ).val();
					} else {
						k = $( '#yoast_wpseo_focuskw_text_input_' + WPGlobusCoreData.default_language ).val();
					}
					YoastSEO.app.rawData.keyword = k ;
					_this.focuskw.val( k );
					_this.focuskw_hidden.val( k );
				}

				/**
				 * Update keyword index.
				 * @since 1.7.2
				 */
				$( '#wpseo-metabox-tabs_'+_this.language+ ' .wpseo_keyword_tab' ).each( function(i,e){
					if ( $(e).hasClass( 'active' ) ) {
						WPGlobusYoastSeo.keywordIndex[_this.language] = i;
						return false;
					}
				});
				WPGlobusYoastSeoPlugin.prototype.analyze();
			});

			/**
			 * An alias.
			 */
			_this = this;

		}

		WPGlobusYoastSeoPlugin.prototype.analyze = function() {

			var tab = $('.wpglobus-wpseo-tabs-list .ui-tabs-active').data('language');
			if (  tab == WPGlobusCoreData.default_language ) {
				YoastSEO.app.snippetPreview.data.urlPath = $( '#editable-post-name-full' ).text();
			} else {
				if ( $( '#editable-post-name-full-' + tab ).length != 0 ) {
					YoastSEO.app.snippetPreview.data.urlPath = $( '#editable-post-name-full-' + tab ).text();
				} else {
					YoastSEO.app.snippetPreview.data.urlPath = $( '#editable-post-name-full' ).text();
				}
			}

			YoastSEO.app.analyzeTimer( YoastSEO.app );
			WPGlobusYoastSeoPlugin.prototype.setScoreIcon();

		}

		WPGlobusYoastSeoPlugin.prototype.setScoreIcon = function() {
			var iID;
			var timer = function() {
				var l = $('.wpglobus-wpseo-tabs-list .ui-tabs-active').data('language');
				/**
				 * Get calculated scores from source ...
				 * @since 1.6.8
				 */
				var generic_tab_score = $('#wpseo-meta-section-content .wpseo_generic_tab a').data('score');
				var keyword_tab_score = $('#wpseo-meta-section-content .wpseo_keyword_tab a').data('score');

				/**
				 * ... and place them to the appropriate language.
				 * @since 1.6.8
				 */
				if ( generic_tab_score != '' ) {
					$( '#wpseo-metabox-tabs_'+l+' .wpseo_generic_tab .wpseo-score-icon' ).removeClass( 'bad ok good 100 na' );
					$( '#wpseo-metabox-tabs_'+l+' .wpseo_generic_tab .wpseo-score-icon' ).addClass( generic_tab_score );
				}
			
				/**
				 * WPGlobusYoastSeoPremium has undefined keyword_tab_score.
				 * @todo need to check class 'wpseo_keyword_tab_original'.
				 */
				if ( 'undefined' !== typeof keyword_tab_score ) {
					if ( '' != keyword_tab_score ) {
						if ( WPGlobusYoastSeoPremium && l != WPGlobusYoastSeoPremium.dLang ) {
							$( '#wpseo-metabox-tabs_'+l+' .wpglobus-wpseo_keyword_tab .wpseo-score-icon' ).removeClass( 'bad ok good 100 na' );
							$( '#wpseo-metabox-tabs_'+l+' .wpglobus-wpseo_keyword_tab .wpseo-score-icon' ).addClass( keyword_tab_score );
						} else {
							$( '#wpseo-metabox-tabs_'+l+' .wpseo_keyword_tab .wpseo-score-icon' ).removeClass( 'bad ok good 100 na' );
							$( '#wpseo-metabox-tabs_'+l+' .wpseo_keyword_tab .wpseo-score-icon' ).addClass( keyword_tab_score );						
						}
					}
				}
				clearInterval(iID);
			}
			iID = setInterval( timer, 1500 );
		}

		WPGlobusYoastSeoPlugin.prototype.getWPseoTab = function() {
			return $('.wpglobus-wpseo-tabs-list .ui-tabs-active').data('language');
		}

		WPGlobusYoastSeoPlugin.prototype.citeModification = function(l) {
			var citeBase = '#snippet_citeBase_' + l,
				cite 	 = '#snippet_cite_' + l,
				cb 		 = $( '#wpseo-tab-' + l ).data( 'yoast-cite-base' ),
				e  		 = $( '#wpseo-tab-' + l ).data( 'cite-contenteditable' );

			if ( e === false ) {
				$(cite).attr( 'contenteditable', 'false' );
			}
			$(citeBase).text( cb );
		}

		/**
		 * Page title modification for backend
		 */
		WPGlobusYoastSeoPlugin.prototype.pageTitleModification = function(data) {
			//console.log( '1. pageTitleModification: ' + _this.getWPseoTab() + ' ->' + data);

			var id = '#snippet_title_',
				text = '', tr, return_text = '', set, temp;

			if ( _this.title_template == data ) {
				/**
				 * meta key _yoast_wpseo_title is empty or doesn't exists
				 */
				$.each( WPGlobusCoreData.enabled_languages, function(i,l) {
					set = $( '#snippet-editor-title_' + l ).val();
					if ( set.length == 0 ) {
						temp = _this.title_template;
					} else {
						temp = set;
					}
					//_this.language = l;
					text = _this.replaceVariablesPlugin( temp );
					$( id+l ).text( text );
					if ( l == _this.getWPseoTab() ) {
						return_text = text;
					}
				});

			} else {

				tr = WPGlobusCore.getTranslations( data );

				if ( _this.getWPseoTab() !== WPGlobusCoreData.default_language ) {
					/**
					 * Case when we get data with extra language only ( without language marks )
					 */
					if ( tr[ WPGlobusCoreData.default_language ] == data ) {

						var l = _this.getWPseoTab();

						set = $( '#snippet-editor-title_' + l ).val();
						if ( set.length > 0 ) {
							temp = set;
						} else {
							temp = _this.title_template;
						}
						temp = _this.replaceVariablesPlugin( temp );

						$( id+l ).text( temp );

						return temp;
					}

				}

				$.each( WPGlobusCoreData.enabled_languages, function(i,l) {
					//_this.language = l;
					if ( '' === tr[l] ) {
						text = _this.replaceVariablesPlugin( _this.title_template );
					} else {
						text = _this.replaceVariablesPlugin( tr[l] );
					}
					if ( l == _this.getWPseoTab() ) {
						return_text = text;
						set = $( '#snippet-editor-title_' + l ).val();
						if ( set.length != 0 ) {
							if ( set != return_text ) {
								return_text	= _this.replaceVariablesPlugin( set );
							}
						} else {
							return_text	= _this.replaceVariablesPlugin( _this.title_template );
						}
					}
					$( id+l ).html( return_text );
				});
			}

			return return_text;
		}

		/**
		 * Page description modification for backend
		 */
		WPGlobusYoastSeoPlugin.prototype.metaDescModification = function(data) {
			//console.log( '2. metaDescModification: ' + _this.getWPseoTab() + ': ' + data );

			var id = '#snippet_meta_'; // span element
			var metaDesc = '#snippet-editor-meta-description_';
			var metaDescText = '';
			var tr = WPGlobusCore.getTranslations( data );
			var return_text = '';

			metaDescText = $( metaDesc + _this.getWPseoTab() ).val();

			if ( _this.getWPseoTab() !== WPGlobusCoreData.default_language ) {
				/**
				 * Case when we get data with extra language only (without language marks).
				 */
				if ( tr[ WPGlobusCoreData.default_language ] == data ) {
					var l = _this.getWPseoTab();
					_this.citeModification( l );

					if ( metaDescText.length == 0 ) {
						//$( id+l ).text( data );
						$( id+l ).text( '' );
					} else if ( metaDescText != data ) {
						$( id+l ).text( metaDescText );
					}

					return data;
				}

			}

			$.each( WPGlobusCoreData.enabled_languages, function(i,l) {
				$( id+l ).text( WPGlobusCore.TextFilter( data, l, 'RETURN_EMPTY' ) );
				_this.citeModification( l );
			});


			if ( metaDescText == tr[ _this.getWPseoTab() ]  ) {
				return_text = tr[ _this.getWPseoTab() ];
				if ( return_text.length == 0 ) {
					$( id + _this.getWPseoTab() ).text(
						WPGlobusCore.TextFilter(
							YoastSEO.app.snippetPreview.data.metaDesc,
							WPGlobusCoreData.default_language,
							'RETURN_EMPTY'
						)
					);
				}
			} else {
				return_text = metaDescText;
				if ( return_text.length == 0 ) {
					$( id + _this.getWPseoTab() ).text(
						WPGlobusCore.TextFilter(
							YoastSEO.app.snippetPreview.data.metaDesc,
							WPGlobusCoreData.default_language,
							'RETURN_EMPTY'
						)
					);
				} else {
					$( id + _this.getWPseoTab() ).text( return_text );
				}
			}

			$( id + _this.getWPseoTab() ).text( return_text );
			return return_text;

		}

		WPGlobusYoastSeoPlugin.prototype.snippetModification = function(data) {
			//console.log( '3. snippetModification: ' + _this.getWPseoTab() );
			return WPGlobusCore.TextFilter( data, _this.getWPseoTab(), 'RETURN_EMPTY' );
		}

		/**
		 * Adds some text to the data...
		 *
		 * @param data The data to modify
		 */
		WPGlobusYoastSeoPlugin.prototype.contentModification = function(data) {
			//console.log( '4. contentModification: ' + _this.getWPseoTab() );

			if ( _this.getWPseoTab() == WPGlobusCoreData.default_language ) {
				return data;
			}
			return $( '#content_' + _this.getWPseoTab() ).val();
		};

		WPGlobusYoastSeoPlugin.prototype.titleModification = function(data) {
			//console.log( '5. titleModification: ' + _this.getWPseoTab() );

			setTimeout( _this.updatePageAnalysis, 1000 );

			if ( _this.getWPseoTab() == WPGlobusCoreData.default_language ) {
				return data;
			}

			return $( '#title_' + _this.getWPseoTab() ).val();

		};

		/**
		 * Replace default variables with the values stored in the wpseoMetaboxL10n object.
		 *
		 * @see YoastReplaceVarPlugin.prototype.defaultReplace
		 * @since 1.4.7
		 *
		 * @param {String} textString
		 * @return {String}
		 */
		WPGlobusYoastSeoPlugin.prototype.defaultReplace = function( textString ) {
			var focusKeyword = YoastSEO.app.rawData.keyword;

			return textString.replace( /%%sitedesc%%/g, this.replaceVars.sitedesc )
				.replace( /%%sitename%%/g, WPGlobusCore.TextFilter( this.replaceVars.sitename, _this.getWPseoTab() ) )
				.replace( /%%term_title%%/g, this.replaceVars.term_title )
				.replace( /%%term_description%%/g, this.replaceVars.term_description )
				.replace( /%%category_description%%/g, this.replaceVars.category_description )
				.replace( /%%tag_description%%/g, this.replaceVars.tag_description )
				.replace( /%%searchphrase%%/g, this.replaceVars.searchphrase )
				.replace( /%%date%%/g, this.replaceVars.date )
				.replace( /%%id%%/g, this.replaceVars.id )
				.replace( /%%page%%/g, this.replaceVars.page )
				.replace( /%%currenttime%%/g, this.replaceVars.currenttime )
				.replace( /%%currentdate%%/g, this.replaceVars.currentdate )
				.replace( /%%currentday%%/g, this.replaceVars.currentday )
				.replace( /%%currentmonth%%/g, this.replaceVars.currentmonth )
				.replace( /%%currentyear%%/g, this.replaceVars.currentyear )
				.replace( /%%focuskw%%/g, focusKeyword );
		};


		/**
		 * runs the different replacements on the data-string
		 *
		 * @see YoastReplaceVarPlugin.prototype.replaceVariablesPlugin
		 *
		 * @param {String} data
		 * @returns {string}
		 */
		/*
		WPGlobusYoastSeoPlugin.prototype.replaceVariablesPlugin = function( data ) {
			if( typeof data !== 'undefined' ) {
				data = this.titleReplace( data );
				data = this.defaultReplace( data );
				//data = this.parentReplace( data );
				data = this.doubleSepReplace( data );
				//data = this.excerptReplace( data );
			}
			return data;
		};	// */

		/**
		 * runs the different replacements on the data-string
		 *
		 * @see YoastReplaceVarPlugin.prototype.replaceVariablesPlugin
		 * @since 1.4.7
		 *
		 * @param {String} data
		 * @returns {string}
		 */
		WPGlobusYoastSeoPlugin.prototype.replaceVariablesPlugin = function( data ) {
			if( typeof data !== 'undefined' ) {
				data = this.titleReplace( data );
				//data = this.termtitleReplace( data );
				data = this.defaultReplace( data );
				//data = this.parentReplace( data );
				data = this.replaceSeparators( data );
				//data = this.excerptReplace( data );
			}
			return data;
		};

		/**
		 * Replaces separators in the string.
		 *
		 * @since 1.4.7
		 * @see YoastReplaceVarPlugin.prototype.replaceSeparators
		 *
		 * @param {String} data
		 * @returns {String}
		 */
		WPGlobusYoastSeoPlugin.prototype.replaceSeparators = function( data ) {
			return data.replace( /%%sep%%(\s+%%sep%%)*/g, this.replaceVars.sep );
		};

		/**
		 * Replaces %%title%% with the title
		 *
		 * @see YoastReplaceVarPlugin.prototype.titleReplace
		 *
		 * @param {String} data
		 * @returns {string}
		 */
		WPGlobusYoastSeoPlugin.prototype.titleReplace = function( data ) {
			var title = '', t = '';
			if ( this.language == WPGlobusCoreData.default_language ) {
				title = $('#title').val();
			} else {
				title = $('#title_'+this.language).val();
			}
			if ( typeof title === 'undefined' ) {
				title = YoastSEO.app.rawData.pageTitle;
			}
			if ( this.language != WPGlobusCoreData.default_language && title.length == 0 ) {
				title = $('#title').val();
			}
			data = data.replace( /%%title%%/g, title );

			return data;
		};

		/**
		 * Removes double seperators and replaces them with a single seperator.
		 *
		 * @see YoastReplaceVarPlugin.prototype.doubleSepReplace
		 *
		 * @param {String} data
		 * @returns {String}
		 */
		WPGlobusYoastSeoPlugin.prototype.doubleSepReplace = function( data ) {
			var escaped_seperator = YoastSEO.app.stringHelper.addEscapeChars( this.replaceVars.sep );
			var pattern = new RegExp( escaped_seperator + ' ' + escaped_seperator, 'g' );
			data = data.replace( pattern, this.replaceVars.sep );
			return data;
		};

		WPGlobusYoastSeoPlugin.prototype.updateWpseoKeyword = function(kw,l) {
			if ( $( '#wpseo-meta-section-content_'+l+' .wpseo-keyword' ).length > 0 ) {
				$( '#wpseo-meta-section-content_'+l+' .wpseo-keyword' ).removeClass( 'wpseo-keyword' ).addClass( 'wpglobus-wpseo_keyword_'+l );
			}
			$( '.wpglobus-wpseo_keyword_'+l ).text( kw );
		}
		
		/**
		 * Revised @since 1.9.4
		 */
		WPGlobusYoastSeoPlugin.prototype.updatePageAnalysis = function() {

			_.delay(function(){
				/**
				 * Expand the lists.
				 */
				$('#wpseo-pageanalysis button').each(function(i,e) {
					var expanded = $(e).attr('aria-expanded');
					if ( expanded === 'false' ) {
						$(e).click();
					}
				});
				
				$('#yoast-seo-content-analysis button').each(function(i,e) {
					var expanded = $(e).attr('aria-expanded');
					if ( expanded === 'false' ) {
						$(e).click();
					}
				});	
				
				var _tab = _this.getWPseoTab();
				
				/**
				 * Copy from source.
				 */
				$( '#wpseo-pageanalysis_' + _tab ).html( $('#wpseo-pageanalysis').html() );
				$( '#yoast-seo-content-analysis_' + _tab ).html( $( '#yoast-seo-content-analysis' ).html() );
				$( '#yoast-seo-content-analysis_' + _tab + ' li a' ).css({'float':'none'});					
				
				/**
				 * Expand wrapper.
				 */
				var h = 0, hDefault = 0;
				$('#wpseo-pageanalysis_'+_tab+' ul[role="list"] li').each(function(i,e) {
					hDefault = hDefault + $(e).outerHeight(true);
				});
				h = hDefault + 170;
				$('#pageanalysis_'+_tab).css({'height':h+'px'});
				
			}, 500);

		};

		window.WPGlobusYoastSeoPlugin = new WPGlobusYoastSeoPlugin();

	} /** endif WPGlobusVendor.pagenow */

});
