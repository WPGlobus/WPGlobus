/*jslint browser: true*/
/*global jQuery, console, aaAdminGlobus */
jQuery(document).ready(function () {
    "use strict";
    window.globusAdminApp = (function (globusAdminApp, $) {

        // var params = JSON.parse(JSON.stringify(parameters));
        /* Object Constructor
         ========================*/
        globusAdminApp.App = function (config) {

            if (window.globusAdminApp !== undefined) {
                return false;
            }

            this.config = {
                debug: false,
                version: aaAdminGlobus.version
            };

            this.status = 'ok';

            if ('undefined' === aaAdminGlobus) {
                this.status = 'error';
                if (this.config.debug) {
                    console.log('Error options loading');
                }
            } else {
                if (this.config.debug) {
                    console.dir(aaAdminGlobus);
                }
            }

            this.config.disable_first_language = [
                '<div id="disable_first_language" style="display:block;" class="redux-field-errors notice-red">',
                '<strong>',
                '<span>&nbsp;</span>',
                aaAdminGlobus.i18n.cannot_disable_language,
                '</strong>',
                '</div>'
            ].join('');

            $.extend(this.config, config);

            if ('ok' === this.status) {
                this.init();
            }
        };

        globusAdminApp.App.prototype = {

            init: function () {
				if ( 'post-edit' == aaAdminGlobus.page ) {
					this.post_edit();
				} else if ( 'menu-edit' == aaAdminGlobus.page ) {
					this.nav_menus();	
				} else {
					this.start();
				}	
            },
            nav_menus: function () {

				$('#menu-to-edit .menu-item').each(function( index, li ) {

					var id = $(li).attr('id'),
						item_id = id.replace('menu-item-', '');

					if ( '' != aaAdminGlobus.data.items[item_id]['item-title'] ) {	
						$('#' + id + ' .menu-item-title').text(aaAdminGlobus.data.items[item_id]['item-title']);
					}	

					$.each(['input.edit-menu-item-title', 'input.edit-menu-item-attr-title'], function(input_index, input) { 
						var i = $('#' + id + ' ' + input);
						var p = $('#' + id + ' ' + input).parents('p');
						var height = 0;
						$.each(aaAdminGlobus.data.enabled_languages, function(index, language) {
							var new_element = $(i[0].outerHTML);
							new_element.attr('id', $(i).attr('id') + '-' + language);
							new_element.attr('name', $(i).attr('id') + '-' + language);
							new_element.attr('data-language', language);
							new_element.attr('data-item-id', item_id);
							new_element.attr('placeholder', aaAdminGlobus.data.en_language_name[language]);
							
							var classes = aaAdminGlobus.data.items[item_id][language][input]['class'];
							if ( input_index == 0 && language == aaAdminGlobus.data.default_language ) {
								new_element.attr('class', classes + ' edit-menu-item-title');
							} else {
								new_element.attr('class', classes);
							}
							
							new_element.attr('value', aaAdminGlobus.data.items[item_id][language][input]['caption']);
							new_element.css('margin-bottom', '0.6em');
							$(p).append(new_element[0].outerHTML);
							height = index;
						});
						height = (height+1) * 40;
						$(i).css('display','none').attr('class','').addClass('widefat');
						$(p).css('height', height + 'px');

					});	
				
				});
				
				$('.wpglobus-menu-item').on('blur', function(event) {
					var $this = $(this), 
								li,
								id;
								
					if ( $this.hasClass('wpglobus-item-title') ) {
						li = $this.parents('li');
						id = li.attr('id');
						var s = '', $e, item_id;
						$.each($('#' + id + ' .wpglobus-item-title'), function(index, element){
							$e = $(element);
							if ( $e.val() != '' ) {
								s = s + aaAdminGlobus.data.locale_tag_start.replace('%s',$e.data('language'))  + $e.val() + aaAdminGlobus.data.locale_tag_end;
							}
							item_id = $e.data('item-id');
						});
						$('input#edit-menu-item-title-' + item_id).val(s); 	
					}
					
					if ( $this.hasClass('wpglobus-item-attr') ) {
						li = $this.parents('li');
						id = li.attr('id');
						var s = '', $e, item_id;
						$.each($('#' + id + ' .wpglobus-item-attr'), function(index, element){
							$e = $(element);
							if ( $e.val() != '' ) {
								s = s + aaAdminGlobus.data.locale_tag_start.replace('%s',$e.data('language'))  + $e.val() + aaAdminGlobus.data.locale_tag_end;
							}
							item_id = $e.data('item-id');
						});
							console.log(s);
						$('input#edit-menu-item-attr-title-' + item_id).val(s); 	
					}					
					
				});
			},
            post_edit: function () {

				// Make post-body-content as tabs container
				$('#post-body-content').prepend($('.wpglobus-post-tabs-ul'));
				$.each(aaAdminGlobus.tabs, function( index, suffix ) {
					if ( 'default' == suffix ) {
						$('#postdivrich').wrap('<div id="tab-default"></div>');
						$($('#titlediv')).insertBefore('#postdivrich');				
					} else {
						$('#postdivrich-'+suffix).wrap('<div id="tab-'+suffix+'"></div>');
						$($('#titlediv-'+suffix)).insertBefore('#postdivrich-'+suffix);			
						
					}
				});

				// tabs on
				$('#post-body-content').tabs(); // #post-body-content
				
				// setup for default language
				$('#content').text(aaAdminGlobus.content);
				$('#title').val(aaAdminGlobus.title);
				
                $('.ui-state-default').on('click', function (event) {
					if ( 'link-tab-default' == $(this).attr('id') ) {
						$(window).scrollTop($(window).scrollTop()+1);
						$(window).scrollTop($(window).scrollTop()-1);
					}	
				});				
				
			},
            start: function () {
                var t = this;
                $('#wpglobus_flags').select2({
                    formatResult: this.format,
                    formatSelection: this.format,
                    minimumResultsForSearch: -1,
                    escapeMarkup: function (m) {
                        return m;
                    }
                });

                /** disable checked off first language */
                $('body').on('click', '#enabled_languages-list li:first input', function (event) {
                    event.preventDefault();
                    $('.redux-save-warn').css({'display': 'none'});
                    $('#enabled_languages-list').find('li:first > input').val('1');
                    if ($('#disable_first_language').length === 0) {
                        $(t.config.disable_first_language).insertAfter('#info_bar');
                    }
                    return false;
                });
            },
            format: function (language) {
                return '<img class="wpglobus_flag" src="' + aaAdminGlobus.flag_url + language.text + '"/>&nbsp;&nbsp;' + language.text;
            }
        };

        new globusAdminApp.App();
        
        return globusAdminApp;

    }(window.globusAdminApp || {}, jQuery));

});
