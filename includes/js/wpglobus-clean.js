/**
 * WPGlobus Clean
 * Interface JS functions
 *
 * @since 1.4.3
 *
 * @package WPGlobus
 * @subpackage Administration
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusAdmin, WPGlobusClean*/
jQuery(document).ready(function($) {
	"use strict";
	
	if ( typeof WPGlobusClean === 'undefined' ) {
		return;
	}
	
	if ( typeof WPGlobusAdmin === 'undefined' ) {
		return;
	}	
	
	var api =  {
		init: function() {
			api.addListeners();
		},
		addListeners: function(order){
			$( '#wpglobus-clean-activate' ).on( 'click', function(e){
				$( '#wpglobus-clean-button' ).toggleClass( 'hidden' );
			});
			$( '#wpglobus-clean-button' ).one( 'click', function(e){
				$( this ).toggleClass( 'hidden' );
				$( '#wpglobus-clean-activate' ).prop( 'checked', '' );
				api.clean();
			});	
		},	
		beforeSend: function(order){
			$( '#'+order.table+' .wpglobus-spinner' ).css({'visibility':'visible'});
		},
		clean: function() {

			var tables = WPGlobusClean.tables;
			
			var promise = $.when();
			
			$.each( WPGlobusClean.data, function( what, value ){
				
				promise = promise.then( function() {

					var order = {};
					
					if ( 'wpglobus_options' == what ) {
						order['action'] = 'wpglobus-reset';
						order['table']  = what;
					} else {	
						order['action'] = 'clean';
						order['table']  = what;
					}
					return $.ajax({
						beforeSend:function(){
							if ( typeof api.beforeSend !== 'undefined' ) api.beforeSend(order);
						},
						type: 'POST',
						url: WPGlobusAdmin.ajaxurl,
						data: { action:WPGlobusAdmin.process_ajax, order:order },
						dataType: 'json' 
					});					
				}, function(){
					/* error in promise */
					/* return $.ajax( ); */
				}).then( function( result ) {
					$( '#'+result.data.table+' .wpglobus-spinner' ).css({'visibility':'hidden'});
					if ( result.success ) {
						$( '#'+result.data.table+' .wpglobus-result' ).html( '<img src="'+WPGlobusClean.icons.success+'" />' );
					} else {
						$( '#'+result.data.table+' .wpglobus-result' ).html( '<img src="'+WPGlobusClean.icons.error+'" />' );
					}	
				});
				
			});
			
			promise.then(function(){
				$( '.wpglobus-clean-box' ).addClass( 'hidden' );
			});
			
		}
	};
	
	WPGlobusClean = $.extend({}, WPGlobusClean, api);
	WPGlobusClean.init();

});