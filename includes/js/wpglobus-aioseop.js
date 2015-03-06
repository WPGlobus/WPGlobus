/**
 * WPGlobus Administration All on one seo pack
 * Interface JS functions
 *
 * @since 1.0.8
 *
 * @package WPGlobus
 * @subpackage Administration
 */
/* jslint browser: true */
/* global jQuery, console, WPGlobusCore, WPGlobusCoreData */

var WPGlobusAioseop;

(function($) {
    "use strict";
	var api;
	api = WPGlobusAioseop = {
		init: function() {
			// tabs on
			$('#wpglobus-aioseop-tabs').tabs();
			$('#wpglobus-aioseop-tabs').insertBefore($('#aiosp_snippet_wrapper'));

			api.setCounters();
			api.attachListeners();

		},
		setCounters: function() {
			$('.wpglobus_countable').each(function(i,e){
				var $e = $(e), extra = 0,
					counter = $e.data('field-count');
				if ( typeof $e.data('extra-element') !== 'undefined' ) {
					extra = $('#'+$e.data('extra-element')).data('extra-length');
				}
				$('input[name='+counter+']').val( $e.val().length+extra );	
			});				
		},	
		countChars: function($field,cntfield) {
			var extra = 0, field_size;
			
			if ( typeof $field.data('extra-element') !== 'undefined' ) {
				extra = $('#'+$field.data('extra-element')).data('extra-length');
			}
			
			field_size = $field.val().length + extra;
			$('input[name='+cntfield+']').val( field_size );
			
			return;
			
			cntfield.value = field.value.length + extra;
			if ( typeof field.size != 'undefined' ) {
				field_size = field.size;
			} else {
				field_size = field.rows * field.cols;
			}
			if ( field_size < 10 ) return;
			if ( cntfield.value > field_size ) {
				cntfield.style.color = "#fff";
				cntfield.style.backgroundColor = "#f00";
			} else {
				if ( cntfield.value > ( field_size - 6 ) ) {
					cntfield.style.color = "#515151";
					cntfield.style.backgroundColor = "#ff0";			
				} else {
					cntfield.style.color = "#515151";
					cntfield.style.backgroundColor = "#eee";			
				}
			}			
		},	
		attachListeners: function() {
			$('.wpglobus_countable').on('keyup', function(event) {
				var $t = $(this); 
				api.countChars($t, $t.data('field-count'));
			});

			$('.wpglobus-aioseop_title').on('keyup', function(event){
				var $t = $(this);
				$('#'+'aioseop_snippet_title_'+$t.data('language')).text($t.val());
			});
			
			$('body').on('change', '.wpglobus-aioseop_title', function(event){
				var save_to = 'input[name=aiosp_title]',
					s = '';

				$('.wpglobus-aioseop_title').each(function (i, e) {
					var $e = $(e);
					if ($e.val() !== '') {
						s = s + WPGlobusCore.addLocaleMarks( $e.val(), $e.data('language') );
					}
				});
				$(save_to).val(s);		
			});			
		}	
	};
})(jQuery);