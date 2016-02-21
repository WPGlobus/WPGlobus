/**
 * WPGlobus Customize Options
 * Interface JS functions
 *
 * @since 1.4.5
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
		init: function() {
			$( '#wpglobus-sortable' ).sortable({
				update: api.sortUpdate
			});
			
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
			
			api.addListeners();
			api.ajaxListener();
			
		},
		addListeners: function() {
			
			/** open Addons page in new tab */
			$( '#accordion-section-' + WPGlobusCustomizeOptions.sections.addons + ' .accordion-section-title' ).off( 'click keydown' );
			$( 'body' ).on( 
				'click',
				'#accordion-section-' + WPGlobusCustomizeOptions.sections.addons + ' .accordion-section-title',
				function(ev) {
					window.open( WPGlobusCustomizeOptions.addonsPage, '_blank' );
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
		ajax: function() {
			
			var order = {};
			order['action'] = '';
			
			$.ajax({
				beforeSend:function(){
					//if ( typeof api.beforeSend !== 'undefined' ) api.beforeSend(order);
				},
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
				
				if ( -1 != ajaxOptions.data.indexOf('wp_customize=on') && -1 != ajaxOptions.data.indexOf('action=customize_save') ) {
					api.customizeSave = true;
				}	
		
			});			
			
			$( document ).on( 'ajaxComplete', function( ev, response, ajaxOptions ) {
				if ( typeof response.responseText === 'undefined' ) {
					return;	
				}
				if ( api.customizeSave ) {
					//console.log( ' customizeSave done ' );
					api.customizeSave = false;
					
					api.ajax();				
					
				} else {
					//console.log( ' ajax done ' );

				}
			});
		}	
	};
	
	WPGlobusCustomizeOptions =  $.extend( {}, WPGlobusCustomizeOptions, api );	
	
	WPGlobusCustomizeOptions.init();

});	