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
			if ( 0 == $('#aiosp_snippet_wrapper').size() ) {
				/* maybe All in One SEO Pack Pro license key is not set yet or invalid */
				return;	
			}
			// tabs on
			$('#wpglobus-aioseop-tabs').removeClass('hidden wpglobus-hidden').tabs();
			$('#wpglobus-aioseop-tabs').insertBefore($('#aiosp_snippet_wrapper'));
			$('#aiosp_snippet_wrapper, #aiosp_title_wrapper, #aiosp_description_wrapper, #aiosp_keywords_wrapper').addClass('hidden');
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
			var extra = 0, field_size,
				cntfield = 'input[name='+cntfield+']',
				max_size = $field.data('max-size');

			if ( typeof $field.data('extra-element') !== 'undefined' ) {
				extra = $('#'+$field.data('extra-element')).data('extra-length');
			}
			
			field_size = $field.val().length + extra;
			$(cntfield).val( field_size );
			if ( field_size > max_size ) {
				$(cntfield).css({'color':'#fff','background-color':'#f00'});
			} else {
				if ( field_size > max_size - 6 ) {
					$(cntfield).css({'color':'#515151','background-color':'#ff0'});
				} else {
					$(cntfield).css({'color':'#515151','background-color':'#eee'});
				}
			}	
		},	
		attachListeners: function() {
			$('.wpglobus_countable').on('keyup', function(event) {
				var $t = $(this); 
				api.countChars($t, $t.data('field-count'));
			});

			$('body').on('click', '.wpglobus-post-body-tabs-list li', function(event){
				var $t = $(this);
				if ( $t.hasClass('wpglobus-post-tab') ) {
					$('#wpglobus-aioseop-tabs').tabs('option','active', $t.data('order'));
				}	
			});				
			
			// title
			$('.wpglobus-aioseop_title').on('keyup', function(event){
				var $t = $(this);
				$('#'+'aioseop_snippet_title_'+$t.data('language')).text($t.val());
			});
			$('body').on('change', '.wpglobus-aioseop_title', function(event){
				var $t = $(this),
					save_to = 'input[name=aiosp_title]';
				$(save_to).val( WPGlobusCore.getString( $(save_to).val(), $t.val(), $t.data('language')) );		
			});		
			
			// description
			$('.wpglobus-aioseop_description').on('keyup', function(event){
				var $t = $(this);
				$('#'+'aioseop_snippet_description_'+$t.data('language')).text($t.val());
			});
			
			$('body').on('change', '.wpglobus-aioseop_description', function(event){
				var $t = $(this),
					save_to = 'textarea[name=aiosp_description]';
				$(save_to).val( WPGlobusCore.getString( $(save_to).val(), $t.val(), $t.data('language')) );		
			});					
			
			// keywords
			$('body').on('change', '.wpglobus-aioseop_keywords', function(event){
				var $t = $(this), 
					save_to = 'input[name=aiosp_keywords]';
				$(save_to).val( WPGlobusCore.getString( $(save_to).val(), $t.val(), $t.data('language')) );		
			});				
			
		}	
	};
})(jQuery);