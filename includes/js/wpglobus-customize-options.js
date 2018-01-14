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
			api.setJSCodeSection();
			api.addListeners();
			api.ajaxListener();
		},
		setJSCodeSection: function() {
			var el = WPGlobusCustomizeOptions.settings['wpglobus_js_editor_section'];
			if ( 'undefined' === typeof el ) {
				return;
			}
			if ( 'undefined' === typeof el['wpglobus_customize_js_editor'] ) {
				return
			}
			$('#customize-control-wpglobus_customize_js_editor .customize-control-title')
				.css({'width':'50%'})
				.after('<span class="wpglobus-customize-js-editor-expand" style="float:right;"><a href="#">'+WPGlobusCustomizeOptions.i18n['expandShrink']+'</a></span>');
			
			/**
			 * Expand/Shrink editor.
			 */ 
			$(document).on('click', '.wpglobus-customize-js-editor-expand', function(ev){
				var $t = $(this),
					$f = $('#customize-controls');
				$t.toggleClass('expanded');
				if ( $t.hasClass('expanded') ) {
					$f.css({'width':'500px'});
				} else {
					$f.css({'width':''});
				}
			});
				
			/**
			 * Set defaults.
			 */
			$('#sub-accordion-section-wpglobus_js_editor_section .customize-section-back').on('click', function(ev){
				$('#customize-controls').css({'width':''});
				$('.wpglobus-customize-js-editor-expand').removeClass('expanded');
			});
			
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
			
			/**
			 * Init for wpglobus_js_editor_section.
			 * @since 1.9.7
			 */
			$(document).on( 
				'click',
				'#accordion-section-' + WPGlobusCustomizeOptions.sections.wpglobus_js_editor_section + ' .accordion-section-title',
				function(ev) {
					/**
					 * Fix Code Editor height.
					 */
					$('#customize-control-wpglobus_customize_js_editor .CodeMirror').css({'height':'40em'});
				}
			);				
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
		getCustomizeSaveData: function() {
			return api.customizeSaveData;
		},
		enabledUserControl: function(setting) {
			if ( 'undefined' === typeof WPGlobusCustomize.controlInstances[setting] ) { 
				return false;
			}			
			return WPGlobusCustomize.controlInstances[setting].userControl.enabled;
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
		ajax: function(ajaxAction, data) {
			
			if ( 'wpglobus_customize_save' == ajaxAction ) {
			
				var order = {};
				order['action']  = 'wpglobus_customize_save';
				order['options'] = {};
				
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
								} else if ( 'code_editor' == obj.type ) {
									var control = wp.customize.control.instance(id);
									if ( 'undefined' !== typeof control ) {
										val = control.setting();
									}
								}
								order['options'][obj.option] = val;
								
								if ( 'code_editor' == obj.type ) {
									if ( 'undefined' === typeof control ) {
										/**
										 * If control is undefined then we don't need to save code.
										 */										
										delete order['options'][obj.option];
									}
								}
							}
						}			

					});

				});
			
			}
			
			$.ajax({
				beforeSend:function(){},
				type: 'POST',
				url: WPGlobusCustomizeOptions.ajaxurl,
				data: { action:WPGlobusCustomizeOptions.process_ajax, order:order },
				dataType: 'json' 
			});		
		},
		getChangesetData: function(ajaxData) {
			/**
			 * @since 1.7.9
			 */		
			if ( 'undefined' !== typeof ajaxData ) {

				var changesetData  = /customize_changeset_data=([^&]+)/.exec(ajaxData);
				
				if ( 'undefined' === typeof changesetData[1] ) {
					return;
				}
				
				var settingsJson = decodeURIComponent( changesetData[1] );
				var settings = JSON.parse(settingsJson);
				var values, value;
				
				$.each( settings, function(setting, data) {

					if ( 'undefined' !== typeof WPGlobusCustomize.controlInstances[setting] ) { 
					
						value = ''; 
						if ( 'link' == WPGlobusCustomize.controlInstances[setting]['type'] ) {
							if ( 1 ) {
								/**
								 * In "customize changeset" post we must save URL with ||| delimiters
								 * otherwise we lost value after validating "$setting->validate( $value )"
								 * @see function post_value() in wp-includes\class-wp-customize-manager.php
								 */
								value = WPGlobusCustomize.controlInstances[setting].setting;
							} else {
								/**
								 * Using standard language marks {:en}url{:}.
								 * This is correct code and approach but need to find ability to prevent validating.
								 * @see upper comment.
								 */
								values = WPGlobusCustomize.getTranslations( WPGlobusCustomize.controlInstances[setting].setting );
								/**
								 * @todo make function to get string with language marks from object.
								 */
								$.each(WPGlobusCoreData.enabled_languages, function(i,l){
									if ( '' != values[l] && 'undefined' !== typeof values[l] ) {
										value = value + WPGlobusCore.addLocaleMarks(values[l], l);
									}
								});
							}
						} else {
							
							value = WPGlobusCustomize.controlInstances[setting].setting;
						
						}
						settings[setting]['value'] = value;
					}
				});
				
				var newChangeset = JSON.stringify(settings);
				newChangeset = encodeURIComponent(newChangeset);
				newChangeset = newChangeset.replace( /%20/g, '+' );
				
				ajaxData = ajaxData.replace( changesetData[1], newChangeset );
				
			}
			
			return ajaxData;
		},
		ajaxListener: function() {
			/**
			 * ajaxSend event handler
			 */
			$( document ).on( 'ajaxSend', function( ev, jqXHR, ajaxOptions ) {
				if ( typeof ajaxOptions.data === 'undefined' ) {
					return;	
				}
				
				if ( -1 != ajaxOptions.data.indexOf('wp_customize=on') && -1 != ajaxOptions.data.indexOf('action=customize_save') ) {
					
					if ( -1 != ajaxOptions.data.indexOf('customized=') ) {
						api.customizeSave 		= true;
						api.customizeSaveData 	= ajaxOptions.data;
					} else if ( -1 != ajaxOptions.data.indexOf('customize_changeset_data=') ) {
						/**
						 * Ajax action when are saved changeset. 
						 *
						 * @since 1.7.9
						 */
						ajaxOptions.data = api.getChangesetData(ajaxOptions.data);	
					}
					
				}	
			});			
			
			$( document ).on( 'ajaxComplete', function( ev, response, ajaxOptions ) {
				if ( typeof response.responseText === 'undefined' ) {
					return;	
				}
				if ( api.customizeSave ) {
					api.customizeSave = false;
					api.ajax('wpglobus_customize_save');				
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
					api.ajax('wpglobus_customize_save');				
				}
			});			
		}	
	};
	
	WPGlobusCustomizeOptions =  $.extend( {}, WPGlobusCustomizeOptions, api );	
	
	WPGlobusCustomizeOptions.init();

});	