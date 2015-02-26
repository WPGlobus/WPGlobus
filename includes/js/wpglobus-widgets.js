/**
 * WPGlobus Administration
 * Interface JS functions
 *
 * @since 1.0.6
 *
 * @package WPGlobus
 * @subpackage Core administration
 */
window.WPGlobusWidgets;

(function($) {
	var api;
	api = WPGlobusWidgets = {
		init: function() {
			api.add_elements();	
		},	
		add_elements : function(post_id) {
			var id;
			$('.widget-liquid-right .widget .widget-content').each(function(i,e){
				var $t = $(this),
					element = $t.find('input[type="text"]'),
					clone, name;
					
				id = element.attr('id');
				
				clone = $('#'+id).clone();
				//$(element).addClass('wpglobus-dialog-field-source hidden');
				$(element).addClass('wpglobus-dialog-field-source');
				name = element.attr('name');
				$(clone).attr('id', 'wpglobus-'+id);
				$(clone).attr('name', 'wpglobus-'+name);
				$(clone).attr('data-source-id', id);
				$(clone).attr('class', 'wpglobus-dialog-field');
				$(clone).attr('style', 'width:90%;');
				$(clone).val( WPGlobusCore.TextFilter($(element).val(), WPGlobusCoreData.language) );
				$('<div style="width:20px;" data-type="control" data-source-type="textarea" data-source-id="'+id+'" class="wpglobus-widgets wpglobus_dialog_start wpglobus_dialog_icon"></div>').insertAfter(element);
				$(clone).insertAfter(element);
			});				
		}				
	};
})(jQuery);