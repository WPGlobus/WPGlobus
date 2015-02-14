/*jslint browser: true*/
/*global jQuery, console, WPGlobusAdmin, inlineEditPost */
jQuery(document).ready(function () {
    "use strict";
    window.WPGlobusAdminApp = (function (WPGlobusAdminApp, $) {

        // var params = JSON.parse(JSON.stringify(parameters));
        /* Object Constructor
         ========================*/
        WPGlobusAdminApp.App = function (config) {

            if (window.WPGlobusAdminApp !== undefined) {
                return;
            }

            this.config = {
                debug: false,
                version: WPGlobusAdmin.version
            };

            this.status = 'ok';

            if ('undefined' === WPGlobusAdmin) {
                this.status = 'error';
                if (this.config.debug) {
                    console.log('Error options loading');
                }
            } else {
                if (this.config.debug) {
                    console.dir(WPGlobusAdmin);
                }
            }

            this.config.disable_first_language = [
                '<div id="disable_first_language" style="display:block;" class="redux-field-errors notice-red">',
                '<strong>',
                '<span>&nbsp;</span>',
                WPGlobusAdmin.i18n.cannot_disable_language,
                '</strong>',
                '</div>'
            ].join('');

            $.extend(this.config, config);

            if ('ok' === this.status) {
                this.init();
            }
        };

        WPGlobusAdminApp.App.prototype = {
            init: function () {
				$('#content').addClass('wpglobus-editor').attr('data-language',WPGlobusAdmin.data.default_language);
				$('textarea[id^=content_]').each(function(i,e){
					var l=$(e).attr('id').replace('content_','');
					$(e).attr('data-language',l);
				});
                if ('post-edit' === WPGlobusAdmin.page) {
                    this.post_edit();
                } else if ('menu-edit' === WPGlobusAdmin.page) {
                    this.nav_menus();
                } else if ('taxonomy-edit' === WPGlobusAdmin.page) {
                    if (WPGlobusAdmin.data.tag_id) {
                        this.taxonomy_edit();
                    }
                } else if ('taxonomy-quick-edit' === WPGlobusAdmin.page) {
                    this.quick_edit('taxonomy');
                } else if ('edit.php' === WPGlobusAdmin.page) {
                    this.quick_edit('post');
                } else if ('options-general.php' == WPGlobusAdmin.page) {
					this.options_general();	
                } else {
                    this.start();
                }
            },
            options_general: function () {
				var $bn = $('#blogname');
				$bn.addClass('hidden');
				$('#wpglobus-blogname').insertAfter($bn);
                $('body').on('blur', '.wpglobus-blogname', function (event) {
                    var s = '';
                    $('.wpglobus-blogname').each(function (index, e) {
                        var $e = $(e);
						var l = $e.data('language');
                        if ($e.val() !== '') {
                            s = s + WPGlobusAdmin.data.locale_tag_start.replace('%s', l) + $e.val() + WPGlobusAdmin.data.locale_tag_end;
                        }
                    });
					$bn.val(s);
                });
				
				var $bd = $('#blogdescription');
				$bd.addClass('hidden');
				$('#wpglobus-blogdescription').insertAfter($bd);
                $('body').on('blur', '.wpglobus-blogdesc', function (event) {
                    var s = '';
                    $('.wpglobus-blogdesc').each(function (index, e) {
                        var $e = $(e);
						var l = $e.data('language');
                        if ($e.val() !== '') {
                            s = s + WPGlobusAdmin.data.locale_tag_start.replace('%s', l) + $e.val() + WPGlobusAdmin.data.locale_tag_end;
                        }
                    });
					$bd.val(s);
                });
			},	
            quick_edit: function (type) {
                if (typeof WPGlobusAdmin.data.has_items === 'undefined') {
                    return;
                }
                if (!WPGlobusAdmin.data.has_items) {
                    return;
                }
                var full_id = 0, id = 0;
				
				$(document).ajaxComplete(function(ev, jqxhr, settings){
					if (typeof settings.data === 'undefined') {
                        return;
                    }
					if ( full_id == 0 ) {
                        return;
                    }
					if (settings.data.indexOf('action=inline-save-tax&') >= 0) {
						$('#'+full_id+' a.row-title').text(WPGlobusAdmin.qedit_titles[id][WPGlobusAdmin.data.language]['name']);
						$('#'+full_id+' .description').text(WPGlobusAdmin.qedit_titles[id][WPGlobusAdmin.data.language]['description']);
					}
				});
				
                var title = {};
                $('#the-list tr').each(function (i, e) {
                    var $e = $(e);
                    var k = ( type === 'post' ? 'post-' : 'tag-' );
                    id = $e.attr('id').replace(k, ''); /* don't need var with id, see line 109 */
                    title[id] = {};
                    if ('post' === type) {
                        title[id]['source'] = $e.find('.post_title').text();
                    } else if ('taxonomy' === type) {
                        title[id]['source'] = $('#inline_' + id + ' .name').text();
                    }
                });

                var order = {};
                order['action'] 	 = 'get_titles';
                order['type'] 		 = type;
                order['taxonomy'] 	 = typeof WPGlobusAdmin.data.taxonomy === 'undefined' ? false : WPGlobusAdmin.data.taxonomy;
                order['title'] 		 = title;
                $.ajax({type:'POST', url:WPGlobusAdmin.ajaxurl, data:{action:WPGlobusAdmin.process_ajax, order:order}, dataType:'json'})
                    .done(function (result) {
                        WPGlobusAdmin.qedit_titles = result.qedit_titles;
						$.each(result.bulkedit_post_titles, function(id, obj){
							$('#inline_'+id+' .post_title').text(obj[WPGlobusAdmin.data.language]['name']);
						});
                    })
                    .fail(function (error) {
                    })
                    .always(function (jqXHR, status) {
                    });
                
				$('body').on('change', '.wpglobus-quick-edit-title', function (event) {
                    var s = '';
					var lang = [];
                    $('.wpglobus-quick-edit-title').each(function (index, e) {
                        var $e = $(e);
						var l = $e.data('language');
                        if ($e.val() !== '') {
                            s = s + WPGlobusAdmin.data.locale_tag_start.replace('%s', l) + $e.val() + WPGlobusAdmin.data.locale_tag_end;
                        }
						WPGlobusAdmin.qedit_titles[id][l]['name'] = $e.val();
						lang[index] = l;
                    });

					var so = $(document).triggerHandler('wpglobus_get_translations', {string:s, lang:lang, id:id});
					if ( typeof so !== 'undefined' ) {
						s = so;		
					}
                    $('input.ptitle').eq(0).val(s);
					WPGlobusAdmin.qedit_titles[id]['source'] = s; 
                });
				
				if ( typeof WPGlobusAdmin.data.tags !== 'undefined' ) {
					$.each( WPGlobusAdmin.data.tags, function(i,tag){
						WPGlobusAdmin.data.value[tag]['post_id'] = {};
					});	
				}

				$('a.save, input#bulk_edit').on('mouseenter', function (event) {
					if ( typeof WPGlobusAdmin.data.tags === 'undefined' ) {
                        return;
                    }
					if (event.currentTarget.id=='bulk_edit') {
						$('input#bulk_edit').unbind('click');
					} else {
						$('a.save').unbind('click');
					}	
					
					$('a.save, input#bulk_edit').click(function (event) {
						if (event.currentTarget.id != 'bulk_edit') {	
							$.ajaxSetup({async:false});
						}	
						var p = $(this).parents('tr');
						var id = p.attr('id').replace('edit-','');
						var t,v,new_tags;
						
						$.each( WPGlobusAdmin.data.tags, function(i,tag){
							t = p.find("textarea[name='" + WPGlobusAdmin.data.names[tag] + "']");
							if ( t.size() == 0 ) {
                                return true;
                            }
							WPGlobusAdmin.data.value[tag]['post_id'][id] = t.val();
							v = WPGlobusAdmin.data.value[tag]['post_id'][id].split(',');
							new_tags = [];
							for(var i=0; i<v.length; i++) {
								v[i] = v[i].trim(' ');
								if ( v[i] != '' ) {
									if ( typeof WPGlobusAdmin.data.tag[tag][v[i]] === 'undefined' ) {									
										new_tags[i] = v[i];
									} else {
										new_tags[i] = WPGlobusAdmin.data.tag[tag][v[i]];
									}
								}	
							}
							t.val(new_tags.join(', '));
						});
						if (event.currentTarget.id != 'bulk_edit') {						
							inlineEditPost.save(id);
							$.ajaxSetup({async:true});
						}						
						
					});					
				});				
				
                $('#the-list').on('click', 'a.editinline', function (event) {
					var t = $(this);
					full_id = t.parents('tr').attr('id');
                    if ('post' === type) {
                        id = full_id.replace('post-', '');
                    } else if ('taxonomy' === type) {
                        id = full_id.replace('tag-', '');
                    } else {
						return;
					}
					
					if ('post' === type && typeof WPGlobusAdmin.data.tags !== 'undefined') {
						$.each( WPGlobusAdmin.data.tags, function(i,tag){
							if ( WPGlobusAdmin.data.value[tag] != '' ) {
								if (typeof WPGlobusAdmin.data.value[tag]['post_id'][id] !== 'undefined') {
									$('#edit-' + id + ' textarea[name="' + WPGlobusAdmin.data.names[tag] + '"]').val(WPGlobusAdmin.data.value[tag]['post_id'][id]);
								}
							}	
						});	
					}
					
                    var e = $('#edit-' + id + ' input.ptitle').eq(0);
                    var p = e.parents('label');
					e.val(WPGlobusAdmin.qedit_titles[id].source);
					e.addClass('hidden');
                    $(WPGlobusAdmin.data.template).insertAfter(p);

					if ( typeof WPGlobusAdmin.qedit_titles[id] === 'undefined' ) {
						WPGlobusAdmin.qedit_titles[id] = {};
						WPGlobusAdmin.qedit_titles[id]['source'] = $('#'+full_id+' .name a.row-title').text();
						$(WPGlobusAdmin.data.enabled_languages).each(function(i,l){
							WPGlobusAdmin.qedit_titles[id][l] = {};
							if ( l == WPGlobusAdmin.data.default_language ) {
								WPGlobusAdmin.qedit_titles[id][l]['name'] = WPGlobusAdmin.qedit_titles[id]['source'];
							} else {
								WPGlobusAdmin.qedit_titles[id][l]['name'] = '';
							}
							WPGlobusAdmin.qedit_titles[id][l]['description'] = '';							
						});
					}
					
                    $('.wpglobus-quick-edit-title').each(function (i, e) {
                        var l = $(e).data('language');
                        $(e).attr('id', l + id);
                        if (typeof  WPGlobusAdmin.qedit_titles[id][l] !== 'undefined') {
                            $(e).attr('value', WPGlobusAdmin.qedit_titles[id][l]['name'].replace(/\\\'/g, '\''));
                        }
                    });
                });

            },
            taxonomy_edit: function () {
                var t = $('.form-table').eq(0);
                $.each(WPGlobusAdmin.tabs, function (index, suffix) {
                    var new_element = $(t[0].outerHTML);
                    var language = suffix === 'default' ? WPGlobusAdmin.data.default_language : suffix;
                    new_element.attr('id', 'table-' + suffix);
                    var $e = $(new_element);
                    $e.find('#name').attr('value', WPGlobusAdmin.data.i18n[suffix]['name']).attr('id', 'name-' + suffix).attr('name', 'name-' + suffix).addClass('wpglobus-taxonomy').attr('data-save-to', 'name').attr('data-language', language);
                    $e.find('#slug').attr('id', 'slug-' + suffix).attr('name', 'slug-' + suffix).addClass('wpglobus-taxonomy').attr('data-save-to', 'slug').attr('data-language', language);
                    $e.find('#parent').attr('id', 'parent-' + suffix).attr('name', 'parent-' + suffix).addClass('wpglobus-taxonomy').attr('data-save-to', 'parent').attr('data-language', language);
                    $e.find('#description').text(WPGlobusAdmin.data.i18n[suffix]['description']).attr('id', 'description-' + suffix).attr('name', 'description-' + suffix).addClass('wpglobus-taxonomy').attr('data-save-to', 'description').attr('data-language', language);

                    if ('default' !== suffix) {
                        $e.find('#slug-' + suffix).addClass('wpglobus-nosave').parents('tr').css('display', 'none');
                        $e.find('#parent-' + suffix).addClass('wpglobus-nosave').parents('tr').css('display', 'none');
                    }
                    $('#tab-' + suffix).append($e[0].outerHTML);
                });

                $('.wpglobus-taxonomy-tabs-ul').insertAfter('#ajax-response');
                t.css('display', 'none');

                // Make class wrap as tabs container
                // tabs on
                $('.wrap').tabs();

                $('.wpglobus-taxonomy').on('blur', function (event) {
                    var $this = $(this),
                        save_to = $this.data('save-to'),
                        s = '';

                    if ('parent' === save_to || 'slug' === save_to) {
                        s = $this.val();
                    } else {
                        $('.wpglobus-taxonomy').each(function (index, element) {
                            var $e = $(element);
                            if (!$e.hasClass('wpglobus-nosave')) {
                                if (save_to === $e.data('save-to') && $e.val() !== '') {
                                    s = s + WPGlobusAdmin.data.locale_tag_start.replace('%s', $e.data('language')) + $e.val() + WPGlobusAdmin.data.locale_tag_end;
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

                var timer = function () {
                    if (menu_size !== $(menu_item).size()) {
                        clearInterval(iID);
                        $(menu_item).each(function (index, li) {
                            var $li = $(li);
                            if ($li.hasClass('wpglobus-menu-item')) {
                                return; // the same as continue
                            }
                            var id = $(li).attr('id');
                            $.each(['input.edit-menu-item-title', 'input.edit-menu-item-attr-title'], function (input_index, input) {
                                var i = $('#' + id + ' ' + input);
                                var $i = $(i);
                                if (!$i.hasClass('wpglobus-hidden')) {
                                    $i.addClass('wpglobus-hidden');
                                    $i.css('display', 'none');
                                    var l = $i.parent('label');
                                    var p = $i.parents('p');
                                    $(p).css('height', '80px');
                                    $(l).append('<div style="color:#f00;">' + WPGlobusAdmin.i18n.save_nav_menu + '</div>');
                                }
                            });
                            $li.addClass('wpglobus-menu-item');
                        });
                    }
                };

                $.ajaxSetup({
                    beforeSend: function (jqXHR, PlainObject) {
                        if (typeof PlainObject.data === 'undefined') {
                            return;
                        }
                        if (PlainObject.data.indexOf('action=add-menu-item') >= 0) {
                            menu_size = $(menu_item).size();
                            iID = setInterval(timer, 500);
                        }
                    }
                });

                $(menu_item).each(function (index, li) {

                    var id = $(li).attr('id'),
                        item_id = id.replace('menu-item-', '');

                    $.each(['input.edit-menu-item-title', 'input.edit-menu-item-attr-title'], function (input_index, input) {
                        var i = $('#' + id + ' ' + input);
                        var p = $('#' + id + ' ' + input).parents('p');
                        var height = 0;

                        $.each(WPGlobusAdmin.data.open_languages, function (index, language) {
                            var new_element = $(i[0].outerHTML);
                            new_element.attr('id', $(i).attr('id') + '-' + language);
                            new_element.attr('name', $(i).attr('id') + '-' + language);
                            new_element.attr('data-language', language);
                            new_element.attr('data-item-id', item_id);
                            new_element.attr('placeholder', WPGlobusAdmin.data.en_language_name[language]);

                            var classes = WPGlobusAdmin.data.items[item_id][language][input]['class'];
                            if (input_index === 0 && language === WPGlobusAdmin.data.default_language) {
                                new_element.attr('class', classes + ' edit-menu-item-title');
                            } else {
                                new_element.attr('class', classes);
                            }

                            new_element.attr('value', WPGlobusAdmin.data.items[item_id][language][input]['caption']);
                            new_element.css('margin-bottom', '0.6em');
                            $(p).append(new_element[0].outerHTML);
                            height = index;
                        });
                        height = (height + 1) * 40;
                        $(i).css('display', 'none').attr('class', '').addClass('widefat wpglobus-hidden');
                        $(p).css('height', height + 'px').addClass('wpglobus-menu-item-box');

                    });
                    $(li).addClass('wpglobus-menu-item');
                });

				$('.menus-move-left, .menus-move-right').each(function(index,e) {
					var $e = $(e), new_title;
					var item_id = $e.parents('li').attr('id').replace('menu-item-', '');
					var title = $e.attr('title');
					if ( typeof title !== 'undefined' ) {
						$.each(WPGlobusAdmin.data.post_titles, function(post_title, item_title) {
							if ( title.indexOf(post_title) >= 0 ) {
								new_title = title.replace(post_title, item_title);
								$e.attr('title', new_title);
								$e.text(new_title);
							}	
						});	
					}	
				});
				
				// Run the item handle title when the navigation label was loaded.
				// @see wp-admin\js\nav-menu.js:537
				$('.edit-menu-item-title').trigger('change');
				wpNavMenu.menusChanged = false;
				
                $('.wpglobus-menu-item').on('change', function (event) {
                    var $this = $(this),
                        li,
                        id,
                        s = '', $e, item_id = '';

                    if ($this.hasClass('wpglobus-item-title')) {
                        li = $this.parents('li');
                        id = li.attr('id');
						var lang = [];
                        $.each($('#' + id + ' .wpglobus-item-title'), function (index, element) {
                            $e = $(element);
							var l = $e.data('language');
                            if ($e.val() !== '') {
                                s = s + WPGlobusAdmin.data.locale_tag_start.replace('%s', l) + $e.val() + WPGlobusAdmin.data.locale_tag_end;
                            }
							lang[index] = l;
                            item_id = $e.data('item-id');
                        });
						var so = $(document).triggerHandler('wpglobus_get_menu_translations', {string:s, lang:lang, id:item_id, type:'input.edit-menu-item-title'});
						if ( typeof so !== 'undefined' ) {
							s = so;		
						}					
                        $('input#edit-menu-item-title-' + item_id).val(s);
                    }

                    if ($this.hasClass('wpglobus-item-attr')) {
                        li = $this.parents('li');
                        id = li.attr('id');
						var lang = [];
                        $.each($('#' + id + ' .wpglobus-item-attr'), function (index, element) {
                            $e = $(element);
							var l = $e.data('language');
                            if ($e.val() !== '') {
                                s = s + WPGlobusAdmin.data.locale_tag_start.replace('%s', l) + $e.val() + WPGlobusAdmin.data.locale_tag_end;
                            }
							lang[index] = l;
                            item_id = $e.data('item-id');
                        });
						var so = $(document).triggerHandler('wpglobus_get_menu_translations', {string:s, lang:lang, id:item_id, type:'input.edit-menu-item-attr-title'});
						if ( typeof so !== 'undefined' ) {
							s = so;		
						}					
                        $('input#edit-menu-item-attr-title-' + item_id).val(s);
                    }

                });
            },
            post_edit: function () {
				$.ajaxPrefilter(function( options ) {
					if ( options.data.indexOf('wp_autosave') >= 0 ) {
						$.each(WPGlobusAdmin.data.open_languages, function(i,l){
							var v = $('#title_'+l).val() || '';
							v = $.trim(v);
							if ( v != '' ) {
								options.data = options.data + '&data%5Bwp_autosave%5D%5Bpost_title_'+l+'%5D='+v;
							}	
							v = $('#content_'+l).val() || '';
							v = $.trim(v);
							if ( v != '' ) {
								options.data = options.data + '&data%5Bwp_autosave%5D%5Bcontent_'+l+'%5D='+v;
							}	
						});
					}
				});					
			
                // Make post-body-content as tabs container
                $('#post-body-content').prepend($('.wpglobus-post-tabs-ul'));
                $.each(WPGlobusAdmin.tabs, function (index, suffix) {
                    if ('default' === suffix) {
                        $('#postdivrich').wrap('<div id="tab-default"></div>');
                        $($('#titlediv')).insertBefore('#postdivrich');
                    } else {
                        $('#postdivrich-' + suffix).wrap('<div id="tab-' + suffix + '"></div>');
                        $($('#titlediv-' + suffix)).insertBefore('#postdivrich-' + suffix);

                    }
                });

                // tabs on
                $('#post-body-content').tabs(); // #post-body-content

                // setup for default language
                $('#title').val(WPGlobusAdmin.title);

                /**
                 * See other places with the same bookmark.
                 * @bookmark EDITOR_LINE_BREAKS
                 */
                //$('#content').text(WPGlobusAdmin.content.replace(/\n/g, "<p>"));

                $('#content').text(WPGlobusAdmin.content);
                $('#excerpt').addClass('hidden');
				
                if (typeof WPGlobusVendor !== "undefined") {
                    wpglobus_wpseo();
                }

                if (WPGlobusAdmin.data.modify_excerpt) {
                    $(WPGlobusAdmin.data.template).insertAfter('#excerpt');

                    $('body').on('blur', '.wpglobus-excerpt', function (event) {
                        var s = '';
                        $('.wpglobus-excerpt').each(function (index, e) {
                            var $e = $(e);
                            if ($e.val() !== '') {
                                s = s + WPGlobusAdmin.data.locale_tag_start.replace('%s', $e.data('language')) + $e.val() + WPGlobusAdmin.data.locale_tag_end;
                            }
                        });
                        $('#excerpt').eq(0).val(s);
                    });
                }

				$('#publish').click(function(ev) {
					if ( typeof WPGlobusAdmin.data.tagsdiv === 'undefined' || WPGlobusAdmin.data.tagsdiv.length < 1 ) {
						return;
					}
					$(WPGlobusAdmin.data.tagsdiv).each(function(i,tagsdiv){
                        if ($('#' + tagsdiv).size() == 0) {
                            /* next iteration */
                            return true;
                        }

						var	id = tagsdiv.replace('tagsdiv-', '');
						if ( 'undefined' === id ) {
                            return true;
                        }
						if ( $('#tax-input-'+id).size() == 0 ) {
                            return true;
                        }
						
						var name, tags = [];
						
						$('#tagsdiv-'+id+' .tagchecklist span').each(function(i,e){
							name = $(e).text();
							name = name.replace('X', '').trim(' ');
							if ( typeof WPGlobusAdmin.data.tag[id][name] === 'undefined' ) {
								tags[i] = name;	
							} else {	
								tags[i] = WPGlobusAdmin.data.tag[id][name];
							}	
						});
						$('#tax-input-'+id).val(tags.join(', '));
					});	
				});	
				
                $('.ui-state-default').on('click', function (event) {
                    if ('link-tab-default' === $(this).attr('id')) {
                        $(window).scrollTop($(window).scrollTop() + 1);
                        $(window).scrollTop($(window).scrollTop() - 1);
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
                return '<img class="wpglobus_flag" src="' + WPGlobusAdmin.flag_url + language.text + '"/>&nbsp;&nbsp;' + language.text;
            }
        };

        new WPGlobusAdminApp.App();

        return WPGlobusAdminApp;

    }(window.WPGlobusAdminApp || {}, jQuery));

});
