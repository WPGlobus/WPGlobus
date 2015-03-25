/*jslint browser: true*/
/*global redux_change, jQuery */
(function ($) {
    "use strict";

    $.redux = $.redux || {};

    $(document).ready(function () {
        $.redux.post_types();
    });

    /**
     * Post types
     */
    $.redux.post_types = function () {
		var $list = $('#wpglobus_option-post_type li'),
			count = $list.length,
			clone, text, input_id, id;
			
		if ( count == 0 ) {
			return;
		}	
		$.each(wpglobus_post_types.post_type, function(index,post_type){
			clone = $($list[0]).clone();
			id = 'wpglobus-post-type-'+post_type;
			input_id = 'wpglobus_option_post_type_'+post_type+'_'+count;
			text = $(clone).find('label').text();
			clone[0].innerHTML = clone[0].innerHTML.replace(text, post_type);
			$(clone).attr('id',id).attr('class','wpglobus-post-types');
			$(clone).find('label').attr('for','wpglobus_option_post_type_'+post_type+'_'+count).val(post_type);
			$(clone).find('input.checkbox-check').attr('name','wpglobus_option[post_type]['+post_type+']');
			$(clone).find('input.checkbox').attr('id',input_id);
			$(clone).insertAfter($('#wpglobus_option-post_type li').last());
			
			if ( wpglobus_post_types.options[post_type] === undefined || wpglobus_post_types.options[post_type] == '1' ) {
				$('input[name="wpglobus_option[post_type]['+post_type+']"]').attr('value','1');	
				$('#'+input_id).prop('checked',true);
			} else {	
				$('input[name="wpglobus_option[post_type]['+post_type+']"]').attr('value','0');	
				$('#'+input_id).prop('checked',false);
			}	
			count++;
		});	
	
	};
}(jQuery));