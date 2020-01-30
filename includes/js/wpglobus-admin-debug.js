/**
 * WPGlobus Admin Debug.
 * Interface JS functions
 *
 * @since 1.8.1
 *
 * @package WPGlobus
 * @subpackage Debug
 */
/*jslint browser: true*/
/*global jQuery, console*/
(function($) {
	"use strict";

	if ( 'undefined' === typeof WPGlobusAdminDebug ) {
		return;
	}
	
	var api = {
		init: function(args) {
			setTimeout(function(){
				$('<a name="debug-box"></a>').appendTo('#wpwrap');
				$('#wpglobus-admin-debug-box').detach().appendTo('#wpwrap');
				$('#wpglobus-admin-debug-box').css({'display':'block'});
			}, 1000);
			
			setTimeout(function(){
				var marginTop=0,h1=0,h2=0;
				var $table1 = jQuery('#wpglobus-admin-debug-box .table1');
				if ( $table1.length == 1 ) {
					h1 = $table1.css('height').replace('px', '') * 1;
					marginTop = h1+50;
				}
				var $table2 = jQuery('#wpglobus-admin-debug-box .table2');
				if ( $table2.length == 1 ) {
					$table2.css({'margin-top':marginTop+'px'});
					h2 = $table2.css('height').replace('px', '') * 1;
					marginTop = h1+h2+100;
				}
				var $table3 = jQuery('#wpglobus-admin-debug-box .table3');
				if ( $table3.length == 1 ) {
					$table3.css({'margin-top':marginTop+'px'});
				}
			}, 1200);
		}
	};

	WPGlobusAdminDebug = $.extend({}, WPGlobusAdminDebug, api);
	WPGlobusAdminDebug.init();
	
})(jQuery);	