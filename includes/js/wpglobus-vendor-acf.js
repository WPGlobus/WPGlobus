/**
 * WPGlobus Administration
 * Interface JS functions
 *
 * @since 1.0.5
 *
 * @package WPGlobus
 * @subpackage Vendor Administration
 */

jQuery(document).ready(function($){
	var id;
	var style = 'width:90%;';
	if  ( $('.acf_postbox').parents('#postbox-container-2').length == 1 ) {
		style = 'width:97%';	
	}	
	$('.acf_postbox .field').each(function(i,e){
		var $t = $(this);
		if ( $t.hasClass('field_type-textarea') ) {
			
			var element = $t.find('textarea'),
				clone, name;
			
			id = element.attr('id');
			
			clone = $('#'+id).clone();
			$(element).addClass('hidden');
			name = element.attr('name');
			$(clone).attr('id', 'wpglobus-'+id);
			$(clone).attr('name', 'wpglobus-'+name);
			$(clone).attr('data-source-id', id);
			$(clone).attr('class', 'wpglobus-dialog-field textarea');
			$(clone).attr('style', style);
			$(clone).val( WPGlobusCore.TextFilter($(element).val(), WPGlobusCoreData.language) );
			$(clone).insertAfter(element);
			$('<div style="width:20px;float:right;"><div style="margin:2px;" data-type="control" data-source-type="textarea" data-source-id="'+id+'" class="wpglobus_dialog_start wpglobus_dialog_icon"></div></div>').insertAfter(clone);
		} else if ( $t.hasClass('field_type-text') ) {
			var element = $t.find('input'),
				clone, name;
			
			id = element.attr('id');
			
			clone = $('#'+id).clone();
			$(element).addClass('hidden');
			name = element.attr('name');
			$(clone).attr('id', 'wpglobus-'+id);
			$(clone).attr('name', 'wpglobus-'+name);
			$(clone).attr('data-source-id', id);
			$(clone).attr('class', 'wpglobus-dialog-field text');
			$(clone).attr('style', style);
			$(clone).val( WPGlobusCore.TextFilter($(element).val(), WPGlobusCoreData.language) );
			$(clone).insertAfter(element);
			$('<div style="width:20px;float:right;"><div style="margin:2px;" data-type="control" data-source-type="textarea" data-source-id="'+id+'" class="wpglobus_dialog_start wpglobus_dialog_icon"></div></div>').insertAfter(clone);


		}	
	});
});