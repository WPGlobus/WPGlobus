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
				} else if ( 'taxonomy-edit' == aaAdminGlobus.page ) {
					if ( aaAdminGlobus.data.tag_id ) {
						this.taxonomy_edit();
					}	
				} else if ( 'taxonomy-quick-edit' == aaAdminGlobus.page ) {
					this.quick_edit('taxonomy');
				} else if ( 'edit.php' == aaAdminGlobus.page ) {
					this.quick_edit('post');
				} else {
					this.start();
				}	
            },
            quick_edit: function (type = 'post') {
				var id = 0;
				$.ajaxSetup({
					beforeSend: function(jqXHR, PlainObject) {
						if ( typeof PlainObject.data === 'undefined' ) {
							return;
						}
						if ( PlainObject.data.indexOf('action=inline-save')>=0 ) {
							$(aaAdminGlobus.data.enabled_languages).each(function(i,l) {
								if ( 'undefined' !== aaAdminGlobus.qedit_titles[id][l] ) {
									aaAdminGlobus.qedit_titles[id][l] = $('#'+l+id).val();
								}	
							});
						}
					}
				});
				
				var title = {};
				$('#the-list tr').each( function(i,e) {
					var $e = $(e);
					var k  = type=='post' ? 'post-' : 'tag-';
					var id = $e.attr('id').replace(k,'');
					title[id] = {};
					if ( 'post' == type ) {
						title[id]['source'] = $e.find('.post_title').text();
					} else if ( 'taxonomy' == type ) {
						title[id]['source'] = $('#inline_' + id + ' .name').text();
					}	
				});

				var order = {};
				order['action'] = 'get_titles';
				order['type']   = type;
				order['title']  = title;
				$.ajax({type:'POST', url:aaAdminGlobus.ajaxurl,	data:{action:aaAdminGlobus.process_ajax, order:order}, dataType:'json'})
				.done(function(result){aaAdminGlobus.qedit_titles = result;})
				.fail(function(error){})
				.always(function(jqXHR, status){});				
				
				$('body').on('blur', '.wpglobus-quick-edit-title', function(event) {
					var s = '';
					$('.wpglobus-quick-edit-title').each(function(index, e){
						var $e = $(e);
						if ( $e.val() != '' ) {
							s = s + aaAdminGlobus.data.locale_tag_start.replace('%s',$e.data('lang'))  + $e.val() + aaAdminGlobus.data.locale_tag_end;
						}	
					});	
					$('input.ptitle').eq(0).val(s);
				});
				
				$('#the-list').on('click', 'a.editinline', function(event) {
					var t = $(this);
					if ( 'post' == type ) {	
						id = t.parents('tr').attr('id').replace('post-','');
					} else if ( 'taxonomy' == type ) {
						id = t.parents('tr').attr('id').replace('tag-','');
					}
					var e = $('#edit-' + id + ' input.ptitle').eq(0);
					var p = e.parents('label');
					e.addClass('hidden');
					$(aaAdminGlobus.data.template).insertAfter(p);

					$('.wpglobus-quick-edit-title').each( function(i,e) {
						var l = $(e).data('lang');
						$(e).attr('id',l+id);
						if ( 'undefined' !== aaAdminGlobus.qedit_titles[id][l] ) {
							$(e).attr('value', aaAdminGlobus.qedit_titles[id][l].replace(/\\\'/g,'\''));	
						}	
					});
				});
				
			},
            taxonomy_edit: function () {
				var t = $('.form-table').eq(0);
				$.each(aaAdminGlobus.tabs, function( index, suffix ) {
					var new_element = $(t[0].outerHTML);
					var language = suffix == 'default' ? aaAdminGlobus.data.default_language : suffix;
					new_element.attr('id', 'table-' + suffix);
					var $e = $(new_element);
					$e.find('#name').attr('value',aaAdminGlobus.data.i18n[suffix]['name']).attr('id','name-'+suffix).attr('name','name-'+suffix).addClass('wpglobus-taxonomy').attr('data-save-to','name').attr('data-language',language);
					$e.find('#slug').attr('id','slug-'+suffix).attr('name','slug-'+suffix).addClass('wpglobus-taxonomy').attr('data-save-to','slug').attr('data-language',language);
					$e.find('#parent').attr('id','parent-'+suffix).attr('name','parent-'+suffix).addClass('wpglobus-taxonomy').attr('data-save-to','parent').attr('data-language',language);
					$e.find('#description').text(aaAdminGlobus.data.i18n[suffix]['description']).attr('id','description-'+suffix).attr('name','description-'+suffix).addClass('wpglobus-taxonomy').attr('data-save-to','description').attr('data-language',language);

					if ( 'default' != suffix ) {
						$e.find('#slug-'+suffix).addClass('wpglobus-nosave').parents('tr').css('display','none');
						$e.find('#parent-'+suffix).addClass('wpglobus-nosave').parents('tr').css('display','none');
					}					
					$('#tab-' + suffix).append($e[0].outerHTML);	
				});

				$('.wpglobus-post-tabs-ul').insertAfter('#ajax-response');
				t.css('display','none');
				
				// Make class wrap as tabs container
				// tabs on
				$('.wrap').tabs();
				
				$('.wpglobus-taxonomy').on('blur', function(event) {
					var $this = $(this), 
						save_to = $this.data('save-to'), 
						s = '';
						
					if ( 'parent' == save_to ) {
						s = $this.val();
					} else {
						$('.wpglobus-taxonomy').each(function(index, element){
							var $e = $(element);
							if ( ! $e.hasClass('wpglobus-nosave') ) {
								if ( save_to == $e.data('save-to') && $e.val() != '' ) {
									s = s + aaAdminGlobus.data.locale_tag_start.replace('%s',$e.data('language'))  + $e.val() + aaAdminGlobus.data.locale_tag_end;
								}
							}
							
						});
					}	
					$('#' + save_to).val(s);
				});	
			},
            nav_menus: function () {
				var iID, menu_size,
					menu_item = '#menu-to-edit .menu-item';
				
				var timer = function(){
					if ( menu_size != $(menu_item).size() ) {
						clearInterval(iID);
						$(menu_item).each(function( index, li ) {
							var $li = $(li);
							if ( $li.hasClass('wpglobus-menu-item') ) {
								return true; // the same as continue
							}
							var id = $(li).attr('id');
							$.each(['input.edit-menu-item-title', 'input.edit-menu-item-attr-title'], function(input_index, input) { 
								var i = $('#' + id + ' ' + input);
								var $i = $(i);
								if ( ! $i.hasClass('wpglobus-hidden') ) {
									$i.addClass('wpglobus-hidden');
									$i.css('display','none');
									var l = $i.parent('label');
									var p = $i.parents('p');
									$(p).css('height', '80px')
									$(l).append('<div style="color:#f00;">' + aaAdminGlobus.i18n.save_nav_menu + '</div>');
								}
							});
							$li.addClass('wpglobus-menu-item');
						});
					}	
				}
				
				$.ajaxSetup({
					beforeSend: function(jqXHR, PlainObject) {
						if ( typeof PlainObject.data === 'undefined' ) {
							return;
						}
						if ( PlainObject.data.indexOf('action=add-menu-item') >= 0 ) {
							menu_size = $(menu_item).size();
							iID = setInterval(timer, 500);
						}
					}
				});
				
				$(menu_item).each(function( index, li ) {

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
						$(i).css('display','none').attr('class','').addClass('widefat wpglobus-hidden');
						$(p).css('height', height + 'px').addClass('wpglobus-menu-item-box');

					});	
					$(li).addClass('wpglobus-menu-item');
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
				$('#title').val(aaAdminGlobus.title);
				$('#content').text(aaAdminGlobus.content);
				// 
				$('#excerpt').addClass('hidden');
				
				if (typeof aaWPGlobusVendor !== "undefined") {				
					wpglobus_wpseo();
				}
				
				$(aaAdminGlobus.data.template).insertAfter('#excerpt');
				
				$('body').on('blur', '.wpglobus-excerpt', function(event) {
					var s = '';
					$('.wpglobus-excerpt').each(function(index, e){
						var $e = $(e);
						if ( $e.val() != '' ) {
							s = s + aaAdminGlobus.data.locale_tag_start.replace('%s',$e.data('language'))  + $e.val() + aaAdminGlobus.data.locale_tag_end;
						}	
					});	
					$('#excerpt').eq(0).val(s);
				});
								
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
