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
		positionSet: false,
		controlInstances: {},
		//widgetElement: {},
		instancesKeep: false,
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
			
			//api.setElements();
			//api.addListeners();
		},
		ctrlCallback: function( context, obj, key ) {
			
			var dis = false;
			$.each( WPGlobusCustomize.disabledInstanceMask, function(i,e) {
				if ( obj.indexOf(e) >= 0 ){
					dis = true;
					return false;
				}	
			});
			
			if (dis) return;
			
			var control = wp.customize.control.instance( obj );

			$.each( WPGlobusCustomize.elementSelector, function(i,e){
				var element = control.container.find( e );
				if ( element.length != 0 ) {
					if ( typeof api.controlInstances[obj] === 'undefined' ) {
						api.controlInstances[obj] = {}; 
					}	
					api.controlInstances[obj]['element']  = element; 
					api.controlInstances[obj]['setting']  = control.setting(); 
					api.controlInstances[obj]['selector'] = e; 
					api.controlInstances[obj]['type'] 	  = ''; 

					$.each( WPGlobusCustomize.findLinkBy, function(i,piece) {
						
						if ( obj.indexOf(piece) >= 0 ) {
							api.controlInstances[obj]['type'] = 'link';
							if ( '' == api.controlInstances[obj]['setting'] ) {
								// link perhaps was set to empty value 
								api.controlInstances[obj]['setting'] = element[0].defaultValue;
							}	
							element.addClass( 'wpglobus-control-link' );
						}
					});
					if ( api.controlInstances[obj]['type'] === '' ) {
						if ( e == 'textarea' ) { 
							api.controlInstances[obj]['type'] = 'textarea';
						} else {
							// @todo check for link	
							api.controlInstances[obj]['type'] = 'text';
						}	
					}
					element.val( WPGlobusCore.TextFilter( api.controlInstances[obj]['setting'], WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
					element.addClass( 'wpglobus-customize-control' );
					if ( api.controlInstances[obj]['type'] == 'link' ) {
						api.controlInstances[obj]['setting'] = api.convertString( element[0].defaultValue );	
					};
				}	
			});
			
		},
		setControlInstances: function() {
			wp.customize.control.each( api.ctrlCallback );
		},	
		settingSet: function( cname, newValue ) {
			var control = wp.customize.control.instance( cname ),
				element = control.container.find('#widget-wpglobus-5-title');
				 
			//console.log( newValue );	 
			//control.setting.set( newValue );
			//element.val( control.setting() );
			element.val( newValue );
			//console.log( element.val() );	 
			return;
		},
		setWidgets: function() {
			
			$(document).on( 'click', '.widget-title, .widget-title-action', function(ev){
				$('.customize-control-widget_form .widget-content textarea, .customize-control-widget_form .widget-content input[type=text]').each(function(i,e){
					var $t = $(this),
						id = $t.attr('id');
						
					if ( ! $t.hasClass('wpglobus-customize-widget-control') ) {
						//WPGlobusDialogApp.addElement( $t.attr('id') );
						//$( '#wpglobus-dialog-start-' + $t.attr('id') ).css({'display':'none'});
						//$( '#wpglobus-' + $t.attr('id') ).addClass( 'wpglobus-customize-widget-control' );
						
						$( $t ).addClass( 'wpglobus-customize-widget-control' );
						
						api.widgetElement[id] = $t.val(); 
						
						$t.val( WPGlobusCore.TextFilter( $t.val(), WPGlobusCoreData.language, 'RETURN_EMPTY' ) ); 
						
					
					}	
				});				
			});
			
			$(document).on( 'keyup', '.wpglobus-customize-widget-control', function(e) {
				var $t = $( this );
				api.widgetElement[$t.attr('id')] = WPGlobusCore.getString( api.widgetElement[$t.attr('id')], $t.val(), WPGlobusCoreData.language );
			});
			
			$('#save').on( 'mouseenter', function(event){
				$( 'input.wpglobus-customize-widget-control' ).each( function(i,e){
					var $e = $(e);
					$e.val( api.widgetElement[$e.attr('id')] );
					$e.trigger( 'change' );	
				});
			}).on( 'mouseleave', function(event) {
				if ( ! api.widgetElementsKeep ) {
					$( '.wpglobus-customize-widget-control' ).each( function(i,e){
						var $e = $(e);
						$e.val( WPGlobusCore.TextFilter( api.widgetElement[$e.attr('id')], WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
						//$e.trigger( 'change' );	
					});
				}	
			}).on( 'click', function(event){
				api.widgetElementsKeep = true;
				//$( '.wpglobus-customize-widget-control' ).each( function(i,e){
					//$( this ).trigger('change');	
				//});	
			});
			
			/*
			$(document).on( 'change', '.wpglobus-customize-widget-control', function(e) {
				var $t = $(this),
					sid = $t.data('source-id');

				if ( '' == sid ) {		
					sid = $t.data('nodename') + '[name="' + $t.data('source-name') + '"]';
				} else {
					sid = '#'+sid;	
				}
				$(sid).val( WPGlobusCore.getString( $(sid).val(), $t.val() ) );
			}); // */
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
			// using '|||' mark for correct work with url
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
		setElements: function() {
			//api.setTitle();
			var value;
			$.each(WPGlobusCoreData.customize.addElements, function(i,e){
				var $e = $(e.element);
				$e.attr('id',i).val(e.value).trigger('change');
				if ( e.type == 'textarea' ) {
					if ( typeof e.textarea_attrs !== 'undefined' ) {
						$e.addClass( e.textarea_attrs.class );
					}
				}	
				if ( typeof e.options !== 'undefined' ) {
					if ( typeof e.options.setValue !== 'undefined' && e.options.setValue ) {
						value = $(e.origin_element).val();
						$e.data( 'source', value );
						$e.val( WPGlobusCore.TextFilter( value, WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
						if ( $e.hasClass('wpglobus-control-url') ) {
							$(e.origin_element).val( api.getString( value ) );	
						}	
					}
					if ( typeof e.options.setLabel !== 'undefined' && e.options.setLabel ) {
						$(e.title).text( $(e.origin_title).text() );
						$(e.description).text( $(e.origin_description).text() );
					}
				}	
				$e.on('change',function (ev){
					var $t = $(this),
						$el = $( WPGlobusCoreData.customize.addElements[$(this).data('customize-setting-link')].origin_element );
					
					$t.data( 'source', WPGlobusCore.getString( $t.data('source'), $t.val() ) );
					if ( $t.hasClass('wpglobus-control-url') ) {
						$el.val( api.getString( $t.data('source') ) );
					} else {
						$el.val( WPGlobusCore.getString( $el.val(), $t.val() ) );
					}
					if ( ! $t.hasClass('wpglobus-not-trigger-change') ) {
						$el.trigger('change');
					}	
				});		
			});		
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

				WPGlobusCoreData.language = api.languages[api.index];
				
				$(this).html( api.selectorHtml.replace('{{language}}', WPGlobusCoreData.language) );
				
				$( '.wpglobus-customize-control' ).each( function(i,e){
					var $e = $(e), inst = $e.data( 'customize-setting-link' );
					if ( 'undefined' === typeof WPGlobusCustomize.controlInstances[inst] ) {
						return;		
					}
					if ( $e.hasClass('wpglobus-control-link') ) {
						var t = api.getTranslations( WPGlobusCustomize.controlInstances[inst].setting );
						//console.log( t[ WPGlobusCoreData.language ]  );
						$e.val( t[ WPGlobusCoreData.language ]  );			
					} else {
						$e.val( WPGlobusCore.TextFilter( WPGlobusCustomize.controlInstances[inst].setting, WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
					}	
				});
				
				// widgets
				$( '.wpglobus-customize-widget-control' ).each( function(i,e){
					var $e = $(e);
					//console.log( WPGlobusCore.TextFilter( $( '#' + $e.data( 'source-id') ).val(), WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
					//$e.val( WPGlobusCore.TextFilter( $( '#' + $e.data( 'source-id') ).val(), WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
					//$e.val( WPGlobusCore.TextFilter( api.widgetElement[$e.attr('id')], WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
				});
				
			});			
			
		},
		updateElements: function( force ) {
			if ( typeof force === 'undefined' ) {
				force = true;
			}
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
		},	
		attachListeners: function() {
			$( '.wpglobus-customize-control' ).on( 'keyup', function(ev) {
				var $t = $(this),
					inst = $t.data( 'customize-setting-link' );
				if ( 'undefined' === typeof WPGlobusCustomize.controlInstances[inst] ) {
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
			
			$('#save').on( 'mouseenter', function(event){
				$.each( WPGlobusCustomize.controlInstances, function( inst, data ) {
					var control = wp.customize.control.instance( inst );
					control.setting.set( data.setting );
					data.element.val( control.setting() );
				});
			}).on( 'mouseleave', function(event) {
				if ( ! api.instancesKeep ) {
					api.updateElements();
				}	
			}).on( 'click', function(event){
				api.instancesKeep = true;
			});			
		
			$(document).on( 'ajaxComplete', function( ev, response ) {
				if ( typeof response.responseText !== 'undefined' ) {
					if ( '{"success":true,"data":[]}' == response.responseText ) {
						api.updateElements( false );
					}	
				}
			});
			
		},	
		setPosition: function(e) {
			if ( typeof e.options.setPosition !== 'undefined' && e.options.setPosition ) {
				var el = $(e.parent).detach();
				el.insertBefore( e.origin_parent );
				$(e.parent).css({'display':'block'});
			}
		},	
		addListeners: function() {
			$(document).on('click','.control-section', function(ev){
				if ( api.positionSet ) {
					return;
				}	
				api.positionSet = true;
				$.each(WPGlobusCoreData.customize.addElements, function(i,e){
					$(e.origin_parent).css({'display':'none'});
					$(e.origin_parent+' label' ).css({'display':'none'}); // from WP4.3				
					if ( typeof e.options !== 'undefined' ) {
						api.setPosition(e);
					}	
				});
			});			

			$(document).ajaxSend(function(event, jqxhr, settings){
				if ( 'undefined' == typeof settings.data ) {
					return;	
				}	
				if ( settings.data.indexOf('action=customize_save') >= 0 ) {
					var s=settings.data.split('&'),
						ss, source;

					$.each(s, function(i,e){
						ss = e.split('=');
						if ( 'customized' == ss[0] ) {
							source = ss[1];
							return;	
						}	
					});
					
					var q = decodeURIComponent(source);
					q = JSON.parse(q);
					$.each(WPGlobusCoreData.customize.addElements, function(elem,value){			
						if ( typeof q[elem] !== 'undefined' ) {
							q[value.origin] = $(WPGlobusCoreData.customize.addElements[elem].origin_element).val();
						}	
					});
					settings.data = settings.data.replace( source, JSON.stringify(q) );
				}
			});	

			api.setWidgets();	
		}	
	};
	
	WPGlobusCustomize =  $.extend( {}, WPGlobusCustomize, api );	
	
	WPGlobusCustomize.init();

});	