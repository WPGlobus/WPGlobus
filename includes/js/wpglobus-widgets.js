/**
 * WPGlobus Administration Widgets
 * Interface JS functions
 *
 * @since 1.0.6
 *
 * @package WPGlobus
 * @subpackage Administration
 */
/* jslint browser: true */
/* global jQuery, console, WPGlobusCore, WPGlobusCoreData */

var WPGlobusWidgets;

(function($) {
    "use strict";
	var api;
	api = WPGlobusWidgets = {
		init: function() {
			api.add_elements();
			api.attachListeners();
		},
		wysiwygClean: function(){
			// remove wpglobus textarea and dialog start button from wysiwyg
			$('.wpglobus-dialog-field').each(function(i,e){
				var source = $(e).data('source-id');
				if (  $('#'+source+'-tmce').size() == 1 ) {
					var ds = $(e).next('.wpglobus_dialog_start');
					$(e).remove();
					$(ds).remove();
				}	
			});
		},
		add_elements : function(get_by, coid) {
			var id, elem = [], get_by_coid;
			elem[0] = 'input[type="text"]';
			elem[1] = 'textarea';
			if ( typeof get_by == 'undefined' || get_by == 'class' ) {
				get_by_coid = '.widget-liquid-right .widget .widget-content';
				$.each(elem, function(i,e){
					api.make_clone(get_by_coid, e);
				});
			} else if ( get_by == 'id' ) {
				get_by_coid = '#'+coid+' .widget-content';
				$.each(elem, function(i,e){
					api.make_clone(get_by_coid, e);
				});	
			}
		},
		make_clone: function(get_by_coid, type) {
			$(get_by_coid+' '+type).each(function(i,e){
				var element = $(e),
					clone, name, text, id;

				id = element.attr('id');
				if ( typeof id == 'undefined' || -1 != id.indexOf( '-number') ) {
					return true;
				}	

				clone = $('#'+id).clone();
				$(element).addClass('wpglobus-dialog-field-source hidden');
				name = element.attr('name');
				$(clone).attr('id', 'wpglobus-'+id);
				$(clone).attr('name', 'wpglobus-'+name);
				$(clone).attr('data-source-id', id);
				$(clone).attr('class', 'wpglobus-dialog-field');
				$(clone).attr('style', 'width:90%;');
				text = WPGlobusCore.TextFilter($(element).val(), WPGlobusCoreData.language);
				$(clone).val(text);
				$('<div style="width:20px;" data-type="control" data-source-type="" data-source-id="'+id+'" class="wpglobus-widgets wpglobus_dialog_start wpglobus_dialog_icon"></div>').insertAfter(element);
				$(clone).insertAfter(element);
				if ( 'input[type="text"]' == type && '' != text ) {
					var w_id = element.parents('.widget').attr('id');
					$('#'+w_id+' .in-widget-title').text(': '+text);
				}
			});				
		},	
		attachListeners: function() {
			$(document).ajaxComplete(function(event, jqxhr, settings){
				if ( -1 != settings.data.indexOf( 'action=save-widget') ) {
					if ( -1 != settings.data.indexOf( 'delete_widget=1' ) ) {
						// deleted widget
					} else {
						// update or added new widget
						var s = settings.data.split('widget-id=');
						s = s[1].split('&');
						$('.widget-liquid-right .widget').each(function(i,e){
							var id = $(e).attr('id');
							if ( -1 !== id.indexOf(s[0]) ) {
								api.add_elements('id', id);
								api.wysiwygClean();
							}	
						});	
					}	
				}	
			});
			$('body').on('change', '.wpglobus-dialog-field', function(){
				var $t = $(this),
					source_id = '#'+$t.data('source-id'),
					source = '', s = '', new_value;
					
				if ( typeof source_id == 'undefined' ) {
					return;	
				}	
				source = $(source_id).val();
				
				if ( ! /(\{:|\[:|<!--:)[a-z]{2}/.test(source) ) {
					$(source_id).val($t.val());
				} else {
					$.each(WPGlobusCoreData.enabled_languages, function(i,l){
						if ( l == WPGlobusCoreData.language ) {
							new_value = $t.val();
						} else {	
							new_value = WPGlobusCore.TextFilter(source,l,'RETURN_EMPTY');
						}	
						if ( '' != new_value ) {
							s = s + WPGlobusCore.addLocaleMarks(new_value,l);	
						}	
					});
					$(source_id).val(s);
				}	

			});
			$(document).on('click','.widget-title, .widget-title-action',function(ev){
				ev.preventDefault();
				api.wysiwygClean();
			});				
		}	
	};
})(jQuery);