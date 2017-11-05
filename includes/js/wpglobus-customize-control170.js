/**
 * WPGlobus Customize Control
 * Interface JS functions
 *
 * @since 1.7.0
 *
 * @package WPGlobus
 * @subpackage Customize Control
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusCore, WPGlobusCoreData, WPGlobusCustomize, WPGlobusCustomizeOptions*/

jQuery(document).ready(function ($) {
    "use strict";

	var api = {
		languages: {},
		index: 0,
		length: 0,
		controlInstances: {},
		controlWidgets: {},
		//controlMenuItems: {},
		instancesKeep: false,
		widgetKeep: false,
		action: false,
		customizeSave: false,
		selectorHtml: '<span style="margin-left:5px;" class="wpglobus-icon-globe"></span><span style="font-weight:bold;">{{language}}</span>',
		settingsSectionExpand: function() {
			$(document).triggerHandler( 'wpglobus_fields_settings_section_expand', [this, api] );		
		},
		init: function(args) {

			api.setTitle();

			if ( WPGlobusCustomizeOptions.themeEnabled == 'false' ) {
				return;
			}

			$.each( WPGlobusCoreData.enabled_languages, function(i,e){
				api.languages[i] = e;
				api.length = i;
			});
			api.addLanguageSelector();
			api.setControlInstances();
			api.setFieldsSection(); /* @since 1.6.0 */
			api.attachListeners();
			/**
			 * @since 1.8.2
			 */
			api.setTinymce();
		},
		updateTinymce: function(event) {
			if ( 'tinymce' == WPGlobusCustomize.controlInstances[this.id]['type'] ) {
				WPGlobusCustomize.controlInstances[this.id]['setting'] = 
					WPGlobusCore.getString( WPGlobusCustomize.controlInstances[this.id]['setting'], event.target.textContent, WPGlobusCoreData.language );
			}			
		},
		setTinymce: function(type) {
			/**
			 * If theme uses tinymce editor then we need to add 'tinymce-editor-init' event listener.
			 */
			$(document).on('tinymce-editor-init', function( event, editor ) {
				if ( 'object' === typeof api.controlInstances[editor.id] ) {
					/**
					 * Set type of control instance as 'tinymce'.
					 */
					api.controlInstances[editor.id]['type'] = 'tinymce';
					$(editor.iframeElement).addClass('wpglobus-customize-control').css({'width':'99%'});
					editor.on( 'input', _.debounce( api.updateTinymce, 300 ) );
				}
			});
		},
		getSize: function(type) {
			if ( 'undefined' === typeof type ) {
				return _.size( api.controlInstances );
			} else if ( 'widget' == type ) {
				return _.size( api.controlWidgets );
			} else if ( 'nav_menu' == type ) {
				//return _.size( api.controlMenuItems );
			}
			return null;
		},
		setFieldsSection: function() {

			var sections = {},
				$sectionTmpl = $( '.wpglobus-fields_settings_control_box' ).data( 'section-template' ),
				sectionHtml = '',
				itemsHtml = '',
				checked   = '';

			$.each( WPGlobusCustomize.controlInstances, function(id, obj) {
				if ( typeof sections[ obj.section ] === 'undefined' ) {
					sections[ obj.section ] = {};
				}
				sections[ obj.section ][ id ] = obj;
			});

			$.each( sections, function( section, controls ) {
				itemsHtml = '<ul>';
				$.each( controls, function( id, control ) {

					if ( control.userControl.enabled ) {
						checked = ' checked ';
					} else {
						checked = '';
					}
					itemsHtml += '<li id="'+control.fieldSettings.itemID+'">';
					itemsHtml += '<input id="'+control.fieldSettings.cbID+'" data-control="'+id+'" class="wpglobus-customize-cb-control" type="checkbox"'+checked+' /> ' + '<span class="wpglobus-cb-control-title">'+control.title+'</span>';
					itemsHtml += '</li>';
				});
				itemsHtml += '</ul>';

				if ( 'undefined' === typeof wp.customize.section( section ) ) {
					/**
					 * Case when theme's author removed a panel ( $wp_customize->remove_panel )
					 * but did not remove its sections ( $wp_customize->remove_section ).
					 * The orphaned sections have "undefined" type.
					 * For example, the "About us" and "Contact us" sections in
					 * @link https://wordpress.org/themes/responsiveboat/
					 *
					 * @since 1.7.8.2
					 */
					return true;
				}

				sectionHtml = $sectionTmpl.replace( '{{section_title}}', wp.customize.section( section ).params.title );
				sectionHtml = sectionHtml.replace( /{{section}}/g, section );
				sectionHtml = sectionHtml.replace( '{{section_id}}', '"'+section+'"' );
				sectionHtml = sectionHtml.replace( '{{items}}', itemsHtml );

				$( sectionHtml ).insertBefore( $( '#' + WPGlobusCustomizeOptions.userControlSaveButton ) );
			});

			$( '#accordion-section-wpglobus_fields_settings_section' ).css({'margin-top':'15px'});
			/** add Help button */
			$( WPGlobusCustomizeOptions.helpButton ).insertAfter( $( '#accordion-section-wpglobus_fields_settings_section .customize-action' ) );
			/**
			 * Hide help by default.
			 */
			$( '#accordion-section-wpglobus_fields_settings_section .customize-section-description' ).addClass( 'hidden' );

			/**
			 * Attach an event handler for user control icon.
			 */
			$(document).on( 'click', '.'+WPGlobusCustomizeOptions.userControlIconClass, function(ev) {
				var section = $(this).data( 'section' );
				$( WPGlobusCustomizeOptions.userControlBoxSelector ).each( function( i, e ) {
					if ( section == $(e).data( 'section' ) ) {
						$(this).css({'display':'block'});
					} else {
						$(this).css({'display':'none'});
					}
				});
				wp.customize.control( 'wpglobus_fields_settings_section' ).expand({
					completeCallback: api.settingsSectionExpand
				});
			});
			
			/**
			 * Toggle help.
			 */
			$( '.wpglobus-customize-icon-help.customize-help-toggle' ).on( 'click', function(ev) {
				$( '#accordion-section-wpglobus_fields_settings_section .customize-section-description' ).toggleClass( 'hidden' );
			});

		},
		setUserControls: function( control_id, obj ) {
			var elem = obj.controlSelector + ' ' + obj.selector;
			var cbIcon = '<img class="'+WPGlobusCustomizeOptions.userControlIconClass+'" data-section="'+obj.section+'" style="position:absolute;right:0px;cursor:pointer;" src="'+WPGlobusCustomizeOptions.userControlIcon+'" />';
			$( cbIcon ).insertBefore( elem );

			if ( ! obj.userControl.enabled ) {

				if ( $( elem ).length > 1 ) {
					/**
					 * in some cases
					 * for example @see Kirki https://wordpress.org/plugins/kirki/
					 */
					$( elem ).each( function(i,e) {
						$(e).removeClass( WPGlobusCustomize.controlClass );
					});
				} else {
					$( elem ).removeClass( WPGlobusCustomize.controlClass ).val( obj.setting );
				}

			}

		},
		ctrlMenuItemsCallback: function( obj, control ) {
			return;
			/**
			 * @todo remove from $disabled_setting_mask[]
			 */
			if ( typeof control === 'undefined' ) {
				control = wp.customize.control.instance( obj );
			}

			if ( typeof api.controlMenuItems[ obj ]['control'] !== 'undefined' ) {
				return;
			}

			if ( control.elements != 0 ) {

				api.controlMenuItems[ obj ]['control'] = control;
				api.controlMenuItems[ obj ]['element'] = {};

				$.each( WPGlobusCustomize.elementSelector, function(i,e){
					var elements = control.container.find(e);
					if ( elements.length > 0 ) {
						$.each( elements, function(i, el) {
							var $e = $(el);
							if ( $e.hasClass( 'edit-menu-item-title' ) ) {

								api.controlMenuItems[ obj ]['element'][ $e.attr('id') ] = {};
								$e.addClass( 'wpglobus-customize-menu-item-control' );
								$e.attr( 'data-menu-item', obj );
								$e.attr( 'data-element', 'title' );
								api.controlMenuItems[ obj ]['element'][ $e.attr('id') ]['value']   = control.elements.title();
								api.controlMenuItems[ obj ]['element'][ $e.attr('id') ]['element']   = 'title';

								/** set menu item title */
								control.elements.title(
									WPGlobusCore.TextFilter(
										control.elements.title(),
										WPGlobusCustomize.languageAdmin
									)
								);

								/**
								 * Set menu-item-title value.
								 */
								$e.val(
									WPGlobusCore.TextFilter(
										api.controlMenuItems[ obj ]['element'][ $e.attr('id') ]['value'],
										WPGlobusCoreData.language,
										'RETURN_EMPTY'
									)
								);

							}
							if ( $e.hasClass( 'edit-menu-item-attr-title' ) ) {

								api.controlMenuItems[ obj ]['element'][ $e.attr('id') ] = {};
								$e.addClass( 'wpglobus-customize-menu-item-control' );
								$e.attr( 'data-menu-item', obj );
								$e.attr( 'data-element', 'attr_title' );
								api.controlMenuItems[ obj ]['element'][ $e.attr('id') ]['value']   = control.elements.attr_title();
								api.controlMenuItems[ obj ]['element'][ $e.attr('id') ]['element']   = 'attr_title';

								/**
								 * Set menu-item-attr-title value.
								 */
								$e.val(
									WPGlobusCore.TextFilter(
										api.controlMenuItems[ obj ]['element'][ $e.attr('id') ]['value'],
										WPGlobusCoreData.language,
										'RETURN_EMPTY'
									)
								);

							}
						});

					}
				});

			}

		},
		ctrlWidgetCallback: function( obj, control ) {

			api.action = false;

			if ( typeof api.controlWidgets[obj]['element'] !== 'undefined' ) {
				return;
			}

			if ( typeof control === 'undefined' ) {
				control = wp.customize.control.instance( obj );
			}

			api.controlWidgets[ obj ]['element'] 		= {};
			api.controlWidgets[ obj ]['control'] 		= control;
			api.controlWidgets[ obj ]['inWidgetTitle'] 	= control.container.find( '.in-widget-title' );

			api.controlWidgets[ obj ]['parent'] 		= control.selector;

			var submit  = control.container.find( 'input[type=submit]' );

			if ( submit.length != 0 ) {
				submit.css({'display':'block'});
				submit.attr( 'data-widget', obj );
				api.controlWidgets[ obj ][ 'submit' ] = submit;
				api.attachWidgetListeners( api.controlWidgets[ obj ] );
			}

			control.liveUpdateMode = false;

			if ( typeof control.setting().title === 'undefined' ) {
				api.controlWidgets[ obj ]['inWidgetTitle'].text( '' );
			} else {
				api.controlWidgets[ obj ]['inWidgetTitle'].text( ': ' + WPGlobusCore.TextFilter( control.setting().title, WPGlobusCustomize.languageAdmin ) );
			}

			$.each( WPGlobusCustomize.elementSelector, function(i,e){
				var elements = control.container.find(e);
				if ( elements.length != 0 ) {
					/**
					 * Widget can contain set of elements.
					 */
					$.each( elements, function( indx, elem ) {

						if ( 'undefined' === typeof elem.id || '' == elem.id ) {
							/**
							 * In widget some elements may don't have id
							 * e.g. https://wordpress.org/plugins/widget-context/
							 */
							return true;
						}

						var $element = $( elements[indx] );

						if ( typeof api.controlWidgets[obj]['element'][ elem.id ] === 'undefined' ) {
							api.controlWidgets[obj]['element'][ elem.id ] = {};
						}

						$element.addClass( 'wpglobus-customize-widget-control' );
						$element.attr( 'data-widget', obj );

						api.controlWidgets[obj]['element'][ elem.id ]['element']  = $element;
						api.controlWidgets[obj]['element'][ elem.id ]['setting']  = control.setting();
						api.controlWidgets[obj]['element'][ elem.id ]['selector'] = e;
						api.controlWidgets[obj]['element'][ elem.id ]['value']    = elem.defaultValue;

						$element.val(
							WPGlobusCore.TextFilter(
								elem.defaultValue,
								WPGlobusCoreData.language,
								'RETURN_EMPTY'
							)
						);

					});

				}
			});

			/**
			 * Event handler after widget was added
			 */
			$( document ).triggerHandler( 'wpglobus_customize_control_added_widget', [ obj ] );

		},
		ctrlCallback: function( context, obj, key ) {

			var dis = false;
			$.each( WPGlobusCustomize.disabledSections, function(i,e) {
				if ( context.section() == e ) {
					dis = true;
					return false;
				}
			});

			if (dis) return;

			$.each( WPGlobusCustomize.disabledSettingMask, function(i,e) {
				/** @see wp.customize.control elements */
				if ( obj.indexOf( e ) >= 0 ){
					dis = true;
					return false;
				}
			});

			if (dis) return;

			var control = wp.customize.control.instance( obj );

			/**
			 * To check for object is widget.
			 */
			if ( obj.indexOf( 'widget' ) >= 0 ) {
				if ( typeof api.controlWidgets[obj] === 'undefined' ) {
					api.controlWidgets[obj] = {};
					if ( api.action ) {
						api.ctrlWidgetCallback( obj, control );
					}
				}
				
				api.controlWidgets[ obj ]['parent'] = control.selector;

				if ( $(control.selector).length != 1 ) {
					/**
					 * Don't add MutationObserver for non-existing parent of control element.
					 * @see https://wordpress.org/themes/ascend/
					 */
					api.controlWidgets[ obj ]['observer'] = null;
				} else {
					/**
					 * Add Mutation Observer for widget.
					 */
					api.controlWidgets[ obj ]['observer'] = new MutationObserver( function( mutations ) {
						mutations.forEach( function( mutation ) {
							if ( 'class' == mutation.attributeName ) {
								if ( -1 != mutation.target.className.indexOf( 'expanding' ) ) {
									/**
									 * @todo Now are using 'expanding' class maybe need to use 'widget-rendered' with delay.
									 */
									api.ctrlWidgetCallback( obj );
								}
							}
						});
					});

					api.controlWidgets[ obj ]['observer'].observe(
						document.querySelector( api.controlWidgets[ obj ]['parent'] ),
							{ attributes: true, childList: true, characterData: true }
					);
				}
				
				return false;
			}

			/**
			if ( obj.indexOf( 'nav_menu_item' ) >= 0 ) {
				if ( typeof api.controlMenuItems[obj] === 'undefined' ) {
					api.controlMenuItems[obj] = {};
					api.ctrlMenuItemsCallback( obj, control );
				}
				api.controlMenuItems[ obj ]['parent'] = control.selector;
				return false;
			}  */

			if ( typeof api.controlInstances[ obj ] !== 'undefined' ) {
				return;
			}

			var controlEnabled = true;

			$.each( WPGlobusCustomize.elementSelector, function(i,e){
				var element = control.container.find( e );
				if ( element.length != 0 ) {

					api.controlInstances[obj] = {};
					api.controlInstances[obj]['element']  	= element;
					api.controlInstances[obj]['elementID']  = element.attr('id') ? '#'+element.attr('id') : undefined;					
					api.controlInstances[obj]['setting']  	= control.setting();
					api.controlInstances[obj]['selector'] = e;
					/**
					 * To get element in DOM @see parent li.customize-control of this control.
					 * And element with 'data-customize-setting-link' attribute.
					 */
					api.controlInstances[obj]['controlSelector'] = control.selector;
					api.controlInstances[obj]['type'] 	  = '';
					api.controlInstances[obj]['section']  = control.section();
					api.controlInstances[obj]['title']    = null;
					api.controlInstances[obj]['userControl']  = null;
					/**
					 * Field Settings for Fields Settings section in WPGlobus Settings panel.
					 * @see #accordion-section-wpglobus_fields_settings_section
					 */
					api.controlInstances[obj]['fieldSettings']  			  = {};
					api.controlInstances[obj]['fieldSettings']['itemID']  	  = 'item-wpglobus-cb-control-'+WPGlobusDialogApp.convertToId(obj);
					api.controlInstances[obj]['fieldSettings']['cbID']  	  = 'wpglobus-cb-control-'+WPGlobusDialogApp.convertToId(obj);

					
					// Let's test with Zerif Lite theme.
					// 
					// When we open yoursite/wp-admin/customize.php:
					//
					// wp.customize.control('zerif_bigtitle_redbutton_url').setting() = 
					//	  "{:en}http://wpglobus.com/hot-news-1{:}{:de}http://wpglobus.com/de/hot-news-2{:}"
					// WPGlobusCustomize.controlInstances['zerif_bigtitle_redbutton_url'].setting = 
					//    "http://wpglobus.com/hot-news-1|||http://wpglobus.com/de/hot-news-2|||null|||null"
					
					// After first saving of changeset we have:
					//
					// wp.customize.control('zerif_bigtitle_redbutton_url').setting() = 
					//	  "http://wpglobus.com/hot-news-1/"
					// WPGlobusCustomize.controlInstances['zerif_bigtitle_redbutton_url'].setting = 
					//    "http://wpglobus.com/hot-news-1/|||http://wpglobus.com/de/hot-news-2|||null|||null"				
					
					// Reload page with changeset_uuid in URL:
					//
					// wp.customize.control('zerif_bigtitle_redbutton_url').setting() = 
					//	  "http://wpglobus.com/hot-news-1/|||http://wpglobus.com/de/hot-news-2|||null|||null"
					// WPGlobusCustomize.controlInstances['zerif_bigtitle_redbutton_url'].setting = 
					//    "http://wpglobus.com/hot-news-1/|||http://wpglobus.com/de/hot-news-2|||null|||null"								
					
					// So, we must set correct URL value for default language when changeset was loaded.
					
					$.each( WPGlobusCustomize.setLinkBy, function( i, piece ) {

						if ( obj.indexOf( piece ) >= 0 ) {
							api.controlInstances[obj]['type'] = 'link';
								
							if ( '' == api.controlInstances[obj]['setting'] ) {
								/** link perhaps was set to empty value */
								api.controlInstances[obj]['setting'] = element[0].defaultValue;
							}
							
							if ( WPGlobusCustomize.changeset_uuid ) {
								/**
								 * @since 1.7.9
								 */
								var val = wp.customize.control(obj).setting();
								if ( -1 !== val.indexOf( '|||' ) ) {
									var value = api.getTranslations(val);
									wp.customize.control(obj).setting( value[WPGlobusCoreData.default_language] );
									$( 
										WPGlobusCustomize.controlInstances[obj].controlSelector + 
										' ' + 
										WPGlobusCustomize.controlInstances[obj].selector 
									).val( value[WPGlobusCoreData.default_language] );
								}
								
							}
							element.addClass( 'wpglobus-control-link' );
						}

					});

					if ( api.controlInstances[obj]['type'] === '' ) {
						if ( e == 'textarea' ) {
							api.controlInstances[obj]['type'] = 'textarea';
						} else {
							api.controlInstances[obj]['type'] = 'text';
						}
					}

					element.val( WPGlobusCore.TextFilter( api.controlInstances[obj]['setting'], WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
					element.addClass( 'wpglobus-customize-control' );

					/**
					 * Use control.selector instead of element.parents('li').attr('id') to get id parent li element
					 * element.attr( 'data-wpglobus-customize-control', element.parents('li').attr('id').replace( 'customize-control-', '') );
					 */
					element.attr( 'data-wpglobus-customize-control', api.controlInstances[obj]['controlSelector'].replace( '#customize-control-', '') );

					if ( api.controlInstances[obj]['type'] == 'link' ) {
						api.controlInstances[obj]['setting'] = api.convertString( element[0].defaultValue );
					};

					/** Get control title. */
					api.controlInstances[obj]['title'] = control.params.label;
					
					/** Enable/disable user control. */
					if ( WPGlobusCustomizeOptions.userControl !== null &&
							typeof WPGlobusCustomizeOptions.userControl[ WPGlobusCustomizeOptions.themeName ] !== 'undefined' ) {

						if ( typeof WPGlobusCustomizeOptions.userControl[ WPGlobusCustomizeOptions.themeName ][ obj ] !== 'undefined' &&
							WPGlobusCustomizeOptions.userControl[ WPGlobusCustomizeOptions.themeName ][ obj ] == 'disable' ) {

							controlEnabled = false;
						}

					}
					api.controlInstances[obj]['userControl'] = {};
					api.controlInstances[obj]['userControl']['enabled'] = controlEnabled;

					api.setUserControls( obj, api.controlInstances[obj] );
				}
			});

		},
		setControlInstances: function() {
			wp.customize.control.each( api.ctrlCallback );
		},
		setTitle: function() {
			$( WPGlobusCoreData.customize.info.element ).html( WPGlobusCoreData.customize.info.html );
		},
		convertString: function(text) {
			if ( typeof text === 'undefined' ) {
				return text;
			}
			var r = [], tr = WPGlobusCore.getTranslations( text ),
				i = 0, rE = true;
			
			if ( text == tr[WPGlobusCoreData.default_language] ) {
				/**
				 * Don't convert if text is in default language only.
				 */
				return text;
			}			
				
			$.each( tr, function(l,e) {
				if ( e == '' ) {
					r[i] = 'null';
				} else {
					rE = false;
					r[i] = e;
				}
				i++;
			});
			if ( rE ) {
				return '';
			}
			return r.join('|||');
		},
		getTranslations: function(text) {
			if ( 'undefined' === typeof(text) ) {
				return text;
			}
		
			var t = {},
				ar = text.split('|||');
			$.each(WPGlobusCoreData.enabled_languages, function(i,l){
				t[l] = typeof ar[i] === 'undefined' || ar[i] === 'null' ? '' : ar[i];
			});
			return t;
		},
		getString: function(s, newVal, lang) {
			/** using '|||' mark for correct work with url */
			if ( 'undefined' === typeof( s ) ) {
				return s;
			}
			if ( 'undefined' === typeof( newVal ) ) {
				newVal = '';
			}
			if ( 'undefined' === typeof( lang ) ) {
				lang = WPGlobusCoreData.language;
			}

			var tr = api.getTranslations(s),
				sR = [], i = 0, rE = true;
				
			$.each( tr, function(l,t){
				if ( l == lang ) {
					newVal = newVal.trim();
					sR[i] = newVal == '' ? 'null' : newVal;
				} else {
					sR[i] = t == '' ? 'null' : t;
				}
				if ( l != WPGlobusCoreData.default_language ) {
					if ( sR[i] != 'null' ) {
						rE = false;
					}
				}				
				i++;
			});
			if ( rE ) {
				/**
				 * Don't convert if sR is in default language only.
				 */				
				sR = sR[0];
			} else {
				sR = sR.join('|||');
			}

			return sR;
		},
		addLanguageSelector: function() {

			$( WPGlobusCustomize.selectorButton ).insertAfter('.customize-controls-preview-toggle');
			$('.wpglobus-customize-selector').html( api.selectorHtml.replace('{{language}}', WPGlobusCoreData.language) );

			$( document ).on( 'click', '.wpglobus-customize-selector', function(ev){
				if ( api.index > api.length-1 ) {
					api.index = 0;
				} else {
					api.index++;
				}

				WPGlobusCoreData.language = api.languages[ api.index ];

				$(this).html( api.selectorHtml.replace( '{{language}}', WPGlobusCoreData.language ) );

				/**
				 * Event after language was changed
				 */
				$( document ).triggerHandler( 'wpglobus_customize_control_language', [ WPGlobusCoreData.language ] );

				$( '.wpglobus-customize-control' ).each( function(i,e){
					var $e = $(e),
						inst = $e.data( 'customize-setting-link' );

					if ( 'undefined' === typeof WPGlobusCustomize.controlInstances[inst] ) {
						/**
						 * try get control element from attribute data-wpglobus-customize-control
						 * for example @see Blink theme, customize control element footer-text instead of blink_footer-text
						 */
						inst = $e.data( 'wpglobus-customize-control' );
						if ( 'undefined' === typeof WPGlobusCustomize.controlInstances[inst] ) {
							return;
						}
					}
				
					if ( $e.hasClass( 'wpglobus-control-link' ) ) {
						var t = api.getTranslations( WPGlobusCustomize.controlInstances[inst].setting );
						$e.val( t[ WPGlobusCoreData.language ] );
					} else {
						/**
						 * @since 1.8.2
						 */
						var t = WPGlobusCore.TextFilter( WPGlobusCustomize.controlInstances[inst].setting, WPGlobusCoreData.language, 'RETURN_EMPTY' );
						if ( 'tinymce' == WPGlobusCustomize.controlInstances[inst]['type'] ) {
							tinymce.get(inst).setContent( t, {format:'raw'} );
						}
						$e.val( WPGlobusCore.TextFilter( t, 'RETURN_EMPTY' ) );
					}
				});

				/** widgets */
				$( '.wpglobus-customize-widget-control' ).each( function(i, e){

					var $e = $(e), obj = $e.data( 'widget' );

					if ( 'undefined' === typeof $e.attr('id') ) {
						return true;
					}

					$e.val(
						WPGlobusCore.TextFilter(
							WPGlobusCustomize.controlWidgets[ obj ][ 'element' ][ $e.attr('id') ][ 'value' ],
							WPGlobusCoreData.language,
							'RETURN_EMPTY'
						)
					);

				});

				/** menu items */
				/**
				$( '.wpglobus-customize-menu-item-control' ).each( function(i, e){
					var $e = $(e);

					$e.val(
						WPGlobusCore.TextFilter(
							WPGlobusCustomize.controlMenuItems[ $e.data( 'menu-item' ) ][ 'element' ][ $e.attr('id') ]['value'],
							WPGlobusCoreData.language,
							'RETURN_EMPTY'
						)
					);

				}); */

			});

		},
		updateElements: function( force ) {
			if ( typeof force === 'undefined' ) {
				force = true;
			}

			/** updateElements simple controls */
			$.each( WPGlobusCustomize.controlInstances, function( inst, data ) {
				if ( ! data.userControl.enabled ) {
					/* next iteration */
					return true;
				}
				var control = wp.customize.control.instance( inst );
				if ( data.type == 'link' ) {
					var t = api.getTranslations( WPGlobusCustomize.controlInstances[inst].setting );
					if ( force ) {
						control.setting.set( t[ WPGlobusCoreData.language ] );
						data.element.val( control.setting() );
					} else {
						data.element.val( t[ WPGlobusCoreData.language ] );
					}
				} else if ( data.type == 'tinymce' ) {
					/**
					 * @since 1.8.2
					 * @todo check for 'force' param.
					 */
					//if ( force ) {
						var t = WPGlobusCore.TextFilter( WPGlobusCustomize.controlInstances[inst].setting, WPGlobusCoreData.language, 'RETURN_EMPTY' );
						/**
						 * First we need to set content for tinymce editor.
						 */
						tinymce.get(inst).setContent( t, {format:'raw'} );
						/**
						 * But user could choose Text mode, so we set value for textarea anyway.
						 */
						data.element.val( t );
						//data.element.val( tinymce.get(inst).getContent() );
					//} else {
						//data.element.val( WPGlobusCore.TextFilter( WPGlobusCustomize.controlInstances[inst].setting, WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
					//}
				} else {
					if ( force ) {
						control.setting.set( WPGlobusCore.TextFilter( WPGlobusCustomize.controlInstances[inst].setting, WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
						data.element.val( control.setting() );
					} else {
						data.element.val( WPGlobusCore.TextFilter( WPGlobusCustomize.controlInstances[inst].setting, WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
					}
				}
			});

			/** updateElements menu items */
			/**
			$.each( WPGlobusCustomize.controlMenuItems, function( menuItem, object ) {
				if ( typeof object.element === 'undefined' ) {
					return;
				}
				$.each( object.element, function( id, obj ) {
					$( '#' + id ).val(
						WPGlobusCore.TextFilter( obj.value, WPGlobusCoreData.language, 'RETURN_EMPTY' )
					);
				});
			}); */
		},
		onSubmitEvents: function( ev ) {

			if ( ev.type == 'mouseenter' ) {

				$.each( api.controlWidgets[ $(this).data('widget') ]['element'], function(id,e) {
					$( '#' + id ).val( e.value );
				});

			} else if ( ev.type == 'mouseleave' ) {

				if ( ! api.widgetKeep ) {
					$.each( api.controlWidgets[ $(this).data('widget') ]['element'], function(id,e) {
						$( '#' + id ).val( WPGlobusCore.TextFilter( e.value, WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
					});
				}

			} else if ( ev.type == 'click' ) {
				api.widgetKeep = true;
			}

		},
		attachWidgetListeners: function( obj ) {

			if ( typeof obj['submit'][0]['id'] !== 'undefined' ) {

				$( document ).on( 'mouseenter', '#' + obj['submit'][0]['id'], api.onSubmitEvents )
				.on( 'mouseleave', '#' + obj['submit'][0]['id'], api.onSubmitEvents )
				.on( 'click', '#' + obj['submit'][0]['id'], api.onSubmitEvents );

			}

		},
		attachListeners: function() {
			/** attachListeners: simple controls */
			$( '.wpglobus-customize-control' ).on( 'keyup', function(ev) {
				var $t = $(this),
					inst = $t.data( 'customize-setting-link' );

				if ( 'undefined' === typeof WPGlobusCustomize.controlInstances[inst] ) {
					/**
					 * try get control element from attribute data-wpglobus-customize-control
					 * for example @see Blink theme, customize control element footer-text instead of blink_footer-text
					 */
					inst = $t.data( 'wpglobus-customize-control' );
					if ( 'undefined' === typeof WPGlobusCustomize.controlInstances[inst] ) {
						/**
						 * Now don't return from callback, we have trigger.
						 * @since 1.7.10
						 */
						//return;
					}
				}

				/** 
				 * @since 1.7.10
				 */
				if ( 'undefined' !== typeof $(document).triggerHandler( 'wpglobus_customize_control_keyup', [ $t, inst ] ) ) {
					return;
				}				
				
				if ( WPGlobusCustomize.controlInstances[inst]['type'] == 'link' ) {

					WPGlobusCustomize.controlInstances[inst]['setting'] = api.getString(
						WPGlobusCustomize.controlInstances[inst]['setting'],
						$t.val(),
						WPGlobusCoreData.language
					);

				} else {

					WPGlobusCustomize.controlInstances[inst]['setting'] = WPGlobusCore.getString(
						WPGlobusCustomize.controlInstances[inst]['setting'],
						$t.val(),
						WPGlobusCoreData.language
					);

				}
			});

			/** attachListeners: widgets */
			$( document ).on( 'keyup', '.wpglobus-customize-widget-control', function(ev) {
				var $t = $(this),
					obj = $t.data( 'widget' );

				if ( 'undefined' === typeof WPGlobusCustomize.controlWidgets[ obj ] ) {
					return;
				}

				WPGlobusCustomize.controlWidgets[ obj ]['element'][ $t.attr('id') ]['value'] = WPGlobusCore.getString(
					WPGlobusCustomize.controlWidgets[ obj ]['element'][ $t.attr('id') ]['value'],
					$t.val(),
					WPGlobusCoreData.language
				);

			});

			/** attachListeners: menu items */
			/**
			$( document ).on( 'keyup', '.wpglobus-customize-menu-item-control', function(ev) {
				var $t = $(this),
					obj = $t.data( 'menu-item' );

				if ( 'undefined' === typeof WPGlobusCustomize.controlMenuItems[ obj ] ) {
					return;
				}

				WPGlobusCustomize.controlMenuItems[ obj ]['element'][ $t.attr('id') ][ 'value' ] = WPGlobusCore.getString(
					WPGlobusCustomize.controlMenuItems[ obj ]['element'][ $t.attr('id') ][ 'value' ],
					$t.val(),
					WPGlobusCoreData.language
				);

			}); */

			/** attachListeners: Save&Publish button */
			$(document).on( 'mouseenter', '#save', function( event ) {

				/** Save&Publish simple controls */
				$.each( WPGlobusCustomize.controlInstances, function( inst, data ) {
					if ( data.userControl.enabled ) {
						
						var control = wp.customize.control.instance( inst );
				
						/**
						 * @since 1.8.2
						 */
						if ( 'tinymce' == data.type ) {
							tinymce.get(inst).setContent( data.setting, {format:'raw'} );
						}
						/**
						 * But user could choose Text mode, so we set value for textarea anyway.
						 */
						control.setting.set( data.setting );
						data.element.val( control.setting() );
					}
				});

				/** Save&Publish menu items */
				/**
				$.each( WPGlobusCustomize.controlMenuItems, function( menuItem, object ) {
					if ( typeof object.element === 'undefined' ) {
						return;
					}
					var control = wp.customize.control.instance( menuItem );
					$.each( object.element, function( id, obj ) {
						$( '#' + id ).val( obj.value );
						control.elements[ obj.element ]( obj.value );
					});
				}); */

			}).on( 'mouseleave', '#save', function( event ) {
				if ( ! api.instancesKeep ) {
					api.updateElements();
					wp.customize.previewer.refresh();
				}
			});

			/**
			 * Don't use $(document) for this case.
			 */
			$('#save').on('click', function(event){
				api.instancesKeep = true;
			});

			/**
			 * AttachListeners: ajaxSend event handler.
			 */
			$(document).on( 'ajaxSend', function( ev, jqXHR, ajaxOptions ) {
				if ( typeof ajaxOptions.data === 'undefined' ) {
					return;
				}
				if ( -1 != ajaxOptions.data.indexOf( 'wp_customize=on' ) && -1 != ajaxOptions.data.indexOf( 'action=customize_save' ) ) {
					api.customizeSave = true;
				}
			});

			/**
			 * AttachListeners: ajaxComplete event handler.
			 */
			$(document).on( 'ajaxComplete', function( ev, response ) {

				if ( typeof response.responseText === 'undefined' ) {
					return;
				}
				/**
				 * @todo check adding new widget.
				 */
				if ( api.customizeSave ) {

					/** Save&Publish ajax complete */
					api.updateElements( false );
					if ( api.instancesKeep ) {
						api.instancesKeep = false;
					}

				} else {

					if ( response.responseText.indexOf( 'WP_CUSTOMIZER_SIGNATURE' ) >= 0 ) {
						api.action = 'customizerAjaxComplete';
						api.setControlInstances();
					}

					$.each( WPGlobusCustomize.controlWidgets, function( obj, data ) {
						/** Apply widget ajax complete */

						var w = obj.replace( '_', '-' );

						if ( response.responseText.indexOf( w ) >= 0 ) {

							data.submit.css({'display':'block'});
							data.control.liveUpdateMode = false;

							$.each( data['element'], function( id, e ) {
								$( '#' + id ).val( WPGlobusCore.TextFilter( e.value, WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
							});

							data.inWidgetTitle.text( ': ' + WPGlobusCore.TextFilter( data.control.setting().title, WPGlobusCustomize.languageAdmin ) );
							api.widgetKeep = false;

							return false;
						}

					});

				}

				api.customizeSave = false;
				
				wp.customize.previewer.refresh();

			});

			/**
			 * AttachListeners: Event handler for tracking clicks by widgets title.
			 */
			/**
			 * @todo remove this listener after testing with WP4.7.
			 */
			/*
			$(document).on( 'click', '.widget-title, .widget-title-action', function(ev){
				return;
				var id = $(this).parents( '.customize-control-widget_form' ).attr( 'id' );
				$.each( api.controlWidgets, function( obj, d ) {
					if ( '#'+id == d.parent ) {
						api.ctrlWidgetCallback( obj );
						return false;
					}
				});
			});  */

			/**
			 * attachListeners: Event handler for tracking clicks by menu item title.
			 */
			/**
			$(document).on( 'click', '.control-section-nav_menu .accordion-section-title', function(ev){
				$.each( api.controlMenuItems, function( obj, d ) {
					api.ctrlMenuItemsCallback( obj );
				});
			}); */


		}
	};

	WPGlobusCustomize =  $.extend( {}, WPGlobusCustomize, api );

	WPGlobusCustomize.init();

});
