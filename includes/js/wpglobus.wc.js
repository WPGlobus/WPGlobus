/*jslint browser: true*/
/*global jQuery, console, WPGlobusWC */
jQuery(document).ready(function ($) {

	wpglobus_wc = function() {
		if ( typeof(WPGlobusWC) === 'undefined' ) {
			return;
		}	
		
		$('#wp-excerpt-wrap').addClass('hidden');
		
		$(WPGlobusWC.excerpt_template).insertAfter('#wp-excerpt-wrap');
		
		// tabs on
		$('#wpglobus-wc-excerpt-tabs').tabs();
		
		$('#wpglobus-wc-excerpt-tabs .wp-editor-container textarea').each(function(i,e){
			var $e = $(e);
			var l = $e.attr('id').replace('excerpt-','');
			$e.addClass('wpglobus-wc-excerpt-textarea').attr('data-language',l);
		});
		
		$('body').on('blur', '.wpglobus-wc-excerpt-textarea', function(event) {
			var s = '';
			$('.wpglobus-wc-excerpt-textarea').each(function(i,e){
				var $e = $(e);
				if ( $e.val() != '' ) {
					s = s + WPGlobusWC.locale_tag_start.replace('%s',$e.data('language'))  + $e.val() + WPGlobusWC.locale_tag_end;
				}	
			});	
			$('#excerpt').eq(0).val(s);
		});
	}
	wpglobus_wc();
});