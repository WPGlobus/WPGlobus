/**
 * WPGlobus Customize Control
 * Interface JS functions
 *
 * @since 1.4.0
 *
 * @package WPGlobus
 * @subpackage Customize Control
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusCore, WPGlobusCoreData*/

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
		selectorHtml: '<span style="margin-left:5px;" class="wpglobus-icon-globe"></span><span style="font-weight:bold;">{{language}}</span>',
		init: function(args) {
			$.each( WPGlobusCoreData.enabled_languages, function(i,e){
				api.languages[i] = e;
				api.length = i;
			});
			api.addLanguageSelector();
			api.setTitle();
			api.setControlInstances();
			api.attachListeners();
		},
		ctrlMenuItemsCallback: function( obj, control ) {
			return;
			// @todo remove from $disabled_setting_mask[]
			
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
								
								/* set menu-item-title value */
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

								/* set menu-item-attr-title value */
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
					/** widget can contain set of elements  */	
					$.each( elements, function( indx, elem ) {
						
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
			
			/** check for obj is widget */
			if ( obj.indexOf( 'widget' ) >= 0 ) {
				if ( typeof api.controlWidgets[obj] === 'undefined' ) {
					api.controlWidgets[obj] = {}; 
					if ( api.action ) {
						api.ctrlWidgetCallback( obj, control );	
					}	
				}					
				api.controlWidgets[ obj ]['parent'] = control.selector; 
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
			} // */
			
			if ( typeof api.controlInstances[ obj ] !== 'undefined' ) {
				return;	
			}			

			$.each( WPGlobusCustomize.elementSelector, function(i,e){
				var element = control.container.find( e );
				if ( element.length != 0 ) {

					api.controlInstances[obj] = {}; 
					api.controlInstances[obj]['element']  = element; 
					api.controlInstances[obj]['setting']  = control.setting(); 
					api.controlInstances[obj]['selector'] = e; 
					api.controlInstances[obj]['controlSelector'] = control.selector; 
					api.controlInstances[obj]['type'] 	  = ''; 

					$.each( WPGlobusCustomize.setLinkBy, function( i, piece ) {
						
						if ( obj.indexOf( piece ) >= 0 ) {
							api.controlInstances[obj]['type'] = 'link';
							if ( '' == api.controlInstances[obj]['setting'] ) {
								/** link perhaps was set to empty value */
								api.controlInstances[obj]['setting'] = element[0].defaultValue;
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
					
					/** use control.selector instead of element.parents('li').attr('id') to get id parent li element */
					//element.attr( 'data-wpglobus-customize-control', element.parents('li').attr('id').replace( 'customize-control-', '') );
					element.attr( 'data-wpglobus-customize-control', api.controlInstances[obj]['controlSelector'].replace( '#customize-control-', '') );
						
					if ( api.controlInstances[obj]['type'] == 'link' ) {
						api.controlInstances[obj]['setting'] = api.convertString( element[0].defaultValue );	
					};
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
			var t = {},
				ar = text.split('|||');	
			$.each(WPGlobusCoreData.enabled_languages, function(i,l){
				t[l] = ar[i] === 'undefined' || ar[i] === 'null' ? '' : ar[i];
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
				sR = [], i = 0;
			$.each( tr, function(l,t){
				if ( l == lang ) {
					sR[i] = newVal;	
				} else {	
					sR[i] = t == '' ? 'null' : t;
				}	
				i++;
			});
			sR = sR.join('|||');
			return sR;
		},		
		addLanguageSelector: function() {
			
			$('<a style="margin-left:48px;" class="customize-controls-close wpglobus-customize-selector"><span class="wpglobus-globe"></span></a>').insertAfter('.customize-controls-preview-toggle');	
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
						$e.val( WPGlobusCore.TextFilter( WPGlobusCustomize.controlInstances[inst].setting, WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
					}	
				});
				
				/** widgets */
				$( '.wpglobus-customize-widget-control' ).each( function(i, e){

					var $e = $(e), obj = $e.data( 'widget' );
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

				});		// */		
				
			});			
			
		},
		updateElements: function( force ) {
			if ( typeof force === 'undefined' ) {
				force = true;
			}
			/** updateElements simple controls */
			$.each( WPGlobusCustomize.controlInstances, function( inst, data ) {
				var control = wp.customize.control.instance( inst );
				if ( data.type == 'link' ) {
					var t = api.getTranslations( WPGlobusCustomize.controlInstances[inst].setting );
					if ( force ) {
						control.setting.set( t[ WPGlobusCoreData.language ] );
						data.element.val( control.setting() );
					} else {	
						data.element.val( t[ WPGlobusCoreData.language ] );
					}	
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
			}); // */	
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
			/** attachListeners simple controls */
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
						return;	
					}	
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
			
			/** attachListeners widgets */
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
			
			/** attachListeners menu items */
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
				
			});	// */					
			
			/** Save&Publish button */
			$( '#save' ).on( 'mouseenter', function( event ) {
				
				/** Save&Publish simple controls */
				$.each( WPGlobusCustomize.controlInstances, function( inst, data ) {
					var control = wp.customize.control.instance( inst );
					control.setting.set( data.setting );
					data.element.val( control.setting() );
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
				});	// */			
				
			}).on( 'mouseleave', function( event ) {
				
				if ( ! api.instancesKeep ) {
					api.updateElements();
				}
				
			}).on( 'click', function(event){
				api.instancesKeep = true;
			});			
		
			/**
			 * ajaxComplete event handler
			 */
			$(document).on( 'ajaxComplete', function( ev, response ) {
				
				if ( typeof response.responseText === 'undefined' ) {
					return;	
				}
				
				if ( '{"success":true,"data":[]}' == response.responseText ) {

					/** Save&Publish ajax complete */
					api.updateElements( false );

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
				
			});
			
			/**
			 * Event handler for tracking clicks by widgets title
			 */
			$(document).on( 'click', '.widget-title, .widget-title-action', function(ev){
				var id = $(this).parents( '.customize-control-widget_form' ).attr( 'id' );
				$.each( api.controlWidgets, function( obj, d ) {
					if ( '#'+id == d.parent ) {
						api.ctrlWidgetCallback( obj );
						return false;
					}	
				});
			});			
			
			/**
			 * Event handler for tracking clicks by menu item title
			 */
			/** 
			$(document).on( 'click', '.control-section-nav_menu .accordion-section-title', function(ev){
				$.each( api.controlMenuItems, function( obj, d ) {
					api.ctrlMenuItemsCallback( obj );
				});
			});	// */				
			
			
		}
	};
	
	WPGlobusCustomize =  $.extend( {}, WPGlobusCustomize, api );	
	
	WPGlobusCustomize.init();

});	