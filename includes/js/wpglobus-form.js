/*jslint browser: true*/
/*global jQuery, console */
jQuery(document).ready(function ($) {
	"use strict";
	var codeRegex = /^[a-z]{2}$/,
		code = $('#wpglobus_language_code'),
		name = $('#wpglobus_language_name'),
		en_name = $('#wpglobus_en_language_name'),
		allFields = $([]).add(code).add(name).add(en_name);
	
	function checkRegexp( o, regexp, n ) {
		if ( !( regexp.test( o.val() ) ) ) {
			o.addClass('wpglobus-state-error');
			return false;
		} else {
			return true;
		}
    }	
	
	$('#wpglobus_edit_form').on('submit', function(event){
		var valid = true;
		allFields.removeClass( "wpglobus-state-error" );
		valid = valid && checkRegexp( code, codeRegex, "" );
 
		if ( ! valid ) {	
			event.preventDefault();
		}
	});
});