/**
 * WPGlobus Customize Control
 * Interface JS functions
 *
 * @since 1.2.1
 *
 * @package WPGlobus
 * @subpackage Customize Control
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusCoreData */
jQuery(document).ready(function ($) {	
    "use strict";
	if ( typeof WPGlobusCoreData.customize === 'undefined' ) {
		return;	
	}

	$(WPGlobusCoreData.customize.info.element).html(WPGlobusCoreData.customize.info.html);
	
	$.each(WPGlobusCoreData.customize.elements, function(i,e){
		$(e.id).attr('id',i).val(e.value).trigger('change');
		$('#customize-control-'+e.origin).css({'display':'none'});
		$('#customize-control-'+e.origin+' label' ).css({'display':'none'}); // from WP4.3
		$(e.id).on('change',function (ev){
			var $e = $( WPGlobusCoreData.customize.elements[$(this).data('customize-setting-link')].origin_element );
			$e.val( WPGlobusCore.getString( $e.val(), $(this).val() ) );
			//$e.trigger('change');
		});		
	});

	$(document).ajaxSend(function(event, jqxhr, settings){
		if ( 'undefined' == typeof settings.data ) {
			return;	
		}	
		if ( settings.data.indexOf('action=customize_save') >= 0 ) {
			var s=settings.data.split('&'),
				ss, source;

			$.each(s, function(i,e){
				ss = e.split('=');
				if ( 'customized' == ss[0] ) {
					source = ss[1];
					return;	
				}	
			});
			
			var q = decodeURIComponent(source);
			// ({'%7B','%22','%7D','%3A'},{'{','"','}',':'})
			/*
			var q = source.replace(/%22/g, '"');
			q = q.replace(/%7B/g, '{');
			q = q.replace(/%7D/g, '}');
			q = q.replace(/%3A/g, ':');
			q = q.replace(/%2C/g, ',');
			q = q.replace(/\+/g, ' ');
			// */
					
			q = JSON.parse(q);
			$.each(WPGlobusCoreData.customize.elements, function(elem,value){			
				if ( typeof q[elem] !== 'undefined' ) {
					q[value.origin] = $(WPGlobusCoreData.customize.elements[elem].origin_element).val();
					
					
				}	
			});
			settings.data = settings.data.replace( source, JSON.stringify(q) );
		}
	});	
});	
