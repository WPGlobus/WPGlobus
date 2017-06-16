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
				var h = $('#wpglobus-admin-debug-box .table1').css('height').replace('px', '') * 1;
				h += 50;
				$('#wpglobus-admin-debug-box .table2').css({'margin-top':h+'px'});
			}, 1200);
		}
	};

	WPGlobusAdminDebug = $.extend({}, WPGlobusAdminDebug, api);
	WPGlobusAdminDebug.init();
	
})(jQuery);	