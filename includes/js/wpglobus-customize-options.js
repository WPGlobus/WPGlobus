/**
 * WPGlobus Customize Options
 * Interface JS functions
 *
 * @since 1.4.6
 *
 * @package WPGlobus
 * @subpackage Customize Options
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusCore, WPGlobusCoreData, WPGlobusCustomizeOptions*/
jQuery(document).ready(function ($) {	
    "use strict";
	
	var api = {
		listID: '#wpglobus-sortable',
		customizeSave: false,
		customizeSaveData: '',
		init: function() {
			$( '#wpglobus-sortable' ).sortable({
				update: api.sortUpdate
			});
			
			api.addListeners();
			api.ajaxListener();
			
		},
		addListeners: function() {
			
			$( 'body' ).on( 'change', '.wpglobus-listen-change', function(ev){
				api.setState( false );
			});	

			$( 'body' ).on( 'change', '#wpglobus-sortable input.wpglobus-language-item', function(ev){
				var $t = $( this );
				if ( ! $t.prop( 'checked' ) ) {
					api.removeLanguage( $t );	
				}	
			});	
			
			$( '#customize-control-wpglobus_add_languages_select_box select' ).on(
				'change',
				function(event){
					api.addLanguage( event, this );
				}
			);
		
			/** open Addons page in new tab */
			$( '#accordion-section-' + WPGlobusCustomizeOptions.sections.wpglobus_addons_section + ' .accordion-section-title' ).off( 'click keydown' );
			$( 'body' ).on( 
				'click',
				'#accordion-section-' + WPGlobusCustomizeOptions.sections.wpglobus_addons_section + ' .accordion-section-title',
				function(ev) {
					window.open( WPGlobusCustomizeOptions.addonsPage, '_blank' );
				}
			);
			
			/** Save Fields Control settings & Reload customize page */
			$( document ).on( 'click', '#' + WPGlobusCustomizeOptions.userControlSaveButton, function(){ api.userControlAjax( this ) } );
			
		},	
		removeLanguage: function( t ) {
			var l = t.data( 'language' ),
				e = $( '#customize-control-wpglobus_add_languages_select_box select option' ).eq(0);
			$( '<option value="'+l+'">' + 
				WPGlobusCustomizeOptions.config.language_name[l] + ' (' + WPGlobusCustomizeOptions.config.en_language_name[l] + ') ' +
				'</option>' ).insertAfter( e );	
			t.parent('li').remove();	
		},	
		addLanguage: function( event, t ) {
			var code = $(t).attr( 'value' ),
				s = $( '#wpglobus-item-skeleton' ).html(),
				item = '',
				li_class = $( api.listID + ' li').attr( 'class' );
			
			if ( code == 'select' ) return;
			
			item = s.replace( 
				'{{flag}}', 
				'src="' +WPGlobusCustomizeOptions.config.flags_url + WPGlobusCustomizeOptions.config.flag[code] + '"'
			);
			item = item.replace( '{{name}}', 				code );
			item = item.replace( '{{id}}', 					code );
			item = item.replace( 'checked="{{checked}}"', 	'checked="checked"' );
			item = item.replace( 'disabled="{{disabled}}"',	'' );
			item = item.replace( '{{item}}', 				WPGlobusCustomizeOptions.config.en_language_name[ code ] + ' (' +code+ ') ' );
			item = item.replace( '{{order}}', 				'#' );
			item = item.replace( '{{language}}', 			code );
			item = item.replace( '{{edit-link}}', 			WPGlobusCustomizeOptions.editLink.replace( '{{language}}', code ) );
			$( '<li class="' + li_class + '">' + item + '</li>' ).appendTo( api.listID );
			api.setOrder();
			
			var opts = $(t).find( 'option' );
			$.each( opts, function(i, e) {
				if ( $(e).attr('value') == code ) {
					$(e).remove();
				}	
			});
			
		},	
		sortUpdate: function( event, ui ) {
			api.setState( false );
			api.setOrder();
		},
		setOrder: function() {

			$( '#wpglobus-sortable input.wpglobus-language-item' ).each( function( i, e ){
				var $e = $(e);
				if ( i == 0 ) {
					$e.prop( 'disabled', 'disabled' ).prop( 'checked', 'checked' );	
				} else {
					$e.removeProp( 'disabled' );	
				}	
				$e.data( 'order', i );
			} );
			
		},	
		setState: function( state ) {
			wp.customize.state( 'saved' ).set( state );	
		},
		userControlAjax: function( btn ) {
			
			$( btn ).prop( 'disabled', true );
			
			var order = {};
			order[ 'action' ]   = 'cb-controls-save';
			order[ 'controls' ] = {};
			$( '.wpglobus-customize-cb-control' ).each( function(i, cb){
				var $t = $( cb );
				if ( $t.prop( 'checked' ) ) {
					// do nothing
				} else {
					var ctrl = $t.data( 'control' );
					ctrl = ctrl.replace( '[', '{{');
					ctrl = ctrl.replace( ']', '}}');
					order[ 'controls' ][ ctrl ] = 'disable';
				}	
			});

			$.ajax({
				beforeSend:function(){},
				type: 'POST',
				url: WPGlobusCustomizeOptions.ajaxurl,
				data: { action:WPGlobusCustomizeOptions.process_ajax, order:order },
				dataType: 'json' 
			})
			.always(function() {
				location.reload(true);
			});
			
		},	
		ajax: function() {
			
			var order = {};
			order[ 'action' ]  = 'wpglobus_customize_save';
			order[ 'options' ] = {};
			
			$.each( WPGlobusCustomizeOptions.settings, function( section, el ) {

				$.each( el, function( id, obj ) {
					
					if ( id == 'wpglobus_customize_enabled_languages' ) {
						
						order[ 'options' ][ obj.option ] = {};
						$( '#wpglobus-sortable input.wpglobus-language-item' ).each( function( i, e ) {
							order[ 'options' ][ obj.option ][ $(this).data('language') ] = '1';
						});
						
						return true;
					}

					if ( -1 != api.customizeSaveData.indexOf( 'wpglobus_customize_post_type_' ) &&
							-1 != id.indexOf( 'wpglobus_customize_post_type_' ) ) {

						if ( typeof order[ 'options' ][ obj.option ] === 'undefined' ) {
							order[ 'options' ][ obj.option ] = {};
						}	
						order[ 'options' ][ obj.option ][ id.replace( 'wpglobus_customize_post_type_', '' ) ] = 
							$( '#customize-control-' + id + ' input' ).prop( 'checked' ) ? 1 : 0;
						
					} else {	
					
						if ( -1 != api.customizeSaveData.indexOf( id ) ) {
						
							var s = $( '#customize-control-' + id + ' ' + obj.type ),
								val = '';
							
							if ( 'textarea' == obj.type ) {
								val = s.val();
							} else if ( 'wpglobus_checkbox' == obj.type ) {
								s = $( '#customize-control-' + id + ' input' );
								if ( id == 'wpglobus_customize_selector_wp_list_pages' ) {
									val = s.prop( 'checked' ) ? 1 : 0;
								} else {	
									val = s.prop( 'checked' ) ? 1 : '';
								}	
							} else if ( 'checkbox' == obj.type ) {
								val = s.prop( 'checked' ) ? 1 : '';
							} else if ( 'select' == obj.type ) {
								val = s.val();
							}		
							order[ 'options' ][ obj.option ] = val;
							
						}
					}			

				});
				

			});
			
			$.ajax({
				beforeSend:function(){},
				type: 'POST',
				url: WPGlobusCustomizeOptions.ajaxurl,
				data: { action:WPGlobusCustomizeOptions.process_ajax, order:order },
				dataType: 'json' 
			});		
		},
		ajaxListener: function() {
			/**
			 * ajaxSend event handler
			 */
			$( document ).on( 'ajaxSend', function( ev, jqXHR, ajaxOptions ) {
				if ( typeof ajaxOptions.data === 'undefined' ) {
					return;	
				}
				
				if ( -1 != ajaxOptions.data.indexOf( 'wp_customize=on' ) && -1 != ajaxOptions.data.indexOf( 'action=customize_save' ) ) {
					api.customizeSave = true;
					api.customizeSaveData = ajaxOptions.data;
				}	
		
			});			
			
			$( document ).on( 'ajaxComplete', function( ev, response, ajaxOptions ) {
				if ( typeof response.responseText === 'undefined' ) {
					return;	
				}
				if ( api.customizeSave ) {
					api.customizeSave = false;
					api.ajax();				
				}
			});
			
			$( document ).on( 'ajaxStop', function() {
				/**
				 * We need to use ajaxStop (together with ajaxComplete) event to make save options in Customizer
				 * cause is Redux Framework makes unbind ajaxComplete event
				 * @see https://github.com/reduxframework/redux-framework/issues/2896
				 */
				if ( api.customizeSave ) {
					api.customizeSave = false;
					api.ajax();				
				}
			});			
		}	
	};
	
	WPGlobusCustomizeOptions =  $.extend( {}, WPGlobusCustomizeOptions, api );	
	
	WPGlobusCustomizeOptions.init();

});	