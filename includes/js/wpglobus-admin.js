/**
 * WPGlobus Administration Core, Dialog, Admin
 * Interface JS functions
 *
 * @since 1.0.0
 *
 * @package WPGlobus
 * @subpackage Administration
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusCore, WPGlobusDialogApp, WPGlobusAdmin, inlineEditPost */

var WPGlobusCore;

(function($) {
	var api;
	api = WPGlobusCore = {
		strpos: function( haystack, needle, offset){
			var i = haystack.indexOf( needle, offset );
			return i >= 0 ? i : false;
		},

		TextFilter: function(text, language, return_in){
			if ( typeof text == 'undefined' || '' === text ) { return text; }
			
			var pos_start, pos_end, possible_delimiters = [], is_local_text_found = false;;
			
			language = '' == language ? 'en' : language;
			return_in  = typeof return_in == 'undefined' || '' == return_in  ? 'RETURN_IN_DEFAULT_LANGUAGE' : return_in;

			possible_delimiters[0] = [];
			possible_delimiters[0]['start'] = WPGlobusCoreData.locale_tag_start.replace('%s', language);
			possible_delimiters[0]['end'] 	 = WPGlobusCoreData.locale_tag_end;
			
			possible_delimiters[1] = [];
			possible_delimiters[1]['start'] = '<!--:'+language+'-->';
			possible_delimiters[1]['end'] = '<!--:-->';
			
			possible_delimiters[2] = [];
			possible_delimiters[2]['start'] = '[:'+language+']';
			possible_delimiters[2]['end'] = '[:';
			


			for (var i = 0; i < 3; i++) {
				
				pos_start = api.strpos( text, possible_delimiters[i]['start'] );
				if ( pos_start === false ) {
					continue;
				}
	  
				pos_start = pos_start + possible_delimiters[i]['start'].length;

				pos_end = api.strpos( text, possible_delimiters[i]['end'], pos_start );

				if ( pos_end === false ) {
					text = text.substr( pos_start );
				} else {
					text = text.substr( pos_start, pos_end - pos_start );
				}

				is_local_text_found = true;
				break;
	  
			}
			
			if ( ! is_local_text_found ) {
				if ( return_in == 'RETURN_EMPTY' ) {
					if ( language == WPGlobusCoreData.default_language && ! /(\{:|\[:|<!--:)[a-z]{2}/.test(text) ) {
						//
					} else {
						text = '';	
					}	
				} else {
					// Try RETURN_IN_DEFAULT_LANGUAGE
					if ( language == WPGlobusCoreData.default_language ) {
						if ( /(\{:|\[:|<!--:)[a-z]{2}/.test(text) ) {
							text = '';
						}
					} else {
						text = api.TextFilter( text, WPGlobusCoreData.default_language );		
					}	
				}	
			}	
			return text;
		},
		addLocaleMarks: function(text, language) {
			return WPGlobusCoreData.locale_tag_start.replace('%s', language) + text + WPGlobusCoreData.locale_tag_end;
		},
		getTranslations: function(text) {
			var t = {},
				return_in;
			$.each(WPGlobusCoreData.enabled_languages, function(i,l){
				return_in  = l == WPGlobusCoreData.default_language  ? 'RETURN_IN_DEFAULT_LANGUAGE' : 'RETURN_EMPTY';
				t[l] = api.TextFilter(text, l, return_in);
			});
			return t;
		}
	};
})(jQuery);

var WPGlobusDialogApp;

(function($) {

	var api;
	api = WPGlobusDialogApp = {
		option : {
			listenClass : '.wpglobus_dialog_start',
			settingsClass : '.wpglobus_dialog_settings',
			dialogTabs: '#wpglobus-dialog-tabs',
			title: '',
			callback: function(){}
		},
		form : undefined,
		element : undefined,
		id : '',
		wpglobus_id : '',
		type : 'textarea',
		source : '',
		order : {},
		value : {},
		request : 'core',
		
		init : function(args) {
			api.option = $.extend(api.option, args);
			$(api.option.dialogTabs).tabs();
			this.attachListener();
		},
		saveDialog: function() {
			var s = '', sdl = '', scl = '', $e, val, l;
			$('.wpglobus_dialog_textarea').each(function(indx,e){
				$e = $(e);
				val = $e.val();
				l = $e.data('language');
				if ( l == WPGlobusAdmin.data.language ) {
					scl = val;
				}	
				if ( val != '' ) {
					s = s + WPGlobusCore.addLocaleMarks(val,l);	
					if ( l == WPGlobusCoreData.default_language ) {
						sdl = val;
					}					
				}	
			});					
			s = s.length == sdl.length + 8 ? sdl : s;
			$(api.id).val(s);
			s = scl == '' ? sdl : scl;
			$(api.wpglobus_id).val(s);
		},	
		dialog : $('#wpglobus-dialog-wrapper').dialog({
			autoOpen: false,
			//height: 250,
			width: 650,
			modal: true,
			dialogClass: 'wpglobus-dialog',
			buttons: [
				{
                    text:'Save',
                    class: 'wpglobus-button-save',
                    click:function(){api.saveDialog(); api.dialog.dialog('close');}
                },
				{
                    text:'Cancel',
                    class: 'wpglobus-button-cancel',
                    click: function(){api.dialog.dialog('close');}
                }
			],
			open: function() {
				$('.wpglobus-dialog .ui-dialog-title').text(api.option.title);
			},
			close: function() {
				api.form[0].reset();
				//allFields.removeClass( "ui-state-error" );
			}
		}),
		attachListener : function() {
			$(document).on('click', api.option.settingsClass, function() {
				if ( $('.wpglobus_dialog_options_wrapper').hasClass('hidden') ) {
					$('.wpglobus_dialog_options_wrapper').removeClass('hidden');
				} else {
					$('.wpglobus_dialog_options_wrapper').addClass('hidden');
				}	
			});	
			$(document).on('click', '.wpglobus_dialog_option', function(event) {
				var $t = $(this), r;
				var ob = $t.data('object');
				api.order['action'] = 'save_post_meta_settings';
				api.order['post_type'] = WPGlobusAdmin.data.post_type;
				api.order['checked']   = $t.prop('checked');
				api.order['id']   	   = $t.attr('id');
				api.order['meta_key']  = $t.data('meta-key');
				r = api.ajax(api.order);
				r.done(function (result) {
					if ( result.result == 'ok' ) {
						if ( result.checked == 'true' ) {
							$(ob).removeClass('wpglobus_dialog_start_hidden');
						} else {	
							$(ob).addClass('wpglobus_dialog_start_hidden');
						}	
					}	
				})
				.fail(function (error) {})
				.always(function (jqXHR, status){});
			});	
			$(document).on('click', api.option.listenClass, function() {
				api.element = $(this);
				api.id = api.element.data('source-id');
				api.wpglobus_id = '#wpglobus-'+api.id;	
				api.id = '#'+api.id;
				api.source = api.element.data('source-value');
				
				if ( typeof api.source === 'undefined' ) {
					api.source = $(api.id).val();	
					if (api.request == 'ajax') {
						// @todo revise ajax action
						//api.order['action'] = 'get_translate';
						//api.order['source'] = api.source;
						//api.ajax(api.order);
					} else {
						api.value = WPGlobusCore.getTranslations(api.source);
					}	
				}					
				$.each(api.value, function(l,e){
					$('#wpglobus-dialog-'+l).val(e);
				});
				api.dialog.dialog('open');				
			});
			/*
			$(document).on('click', '.wpglobus-control-head', function() {
				$('.wpglobus-dialog-field-source').toggleClass('hidden');
			}); */
			api.form = api.dialog.find('form#wpglobus-dialog-form').on('submit', function( event ) {
				event.preventDefault();
				api.saveDialog();
			});					
		},
		ajax : function(order) {
			return $.ajax({type:'POST', url:WPGlobusAdmin.ajaxurl, data:{action:WPGlobusAdmin.process_ajax, order:order}, dataType:'json', async:false});
		}	
	};

})(jQuery);

jQuery(document).ready(function () {
    "use strict";
    window.WPGlobusAdminApp = (function (WPGlobusAdminApp, $) {
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
			$document : $(document),
            init: function () {
				this.admin_init();
				$('#content').addClass('wpglobus-editor').attr('data-language',WPGlobusAdmin.data.default_language);
				$('textarea[id^=content_]').each(function(i,e){
					var l=$(e).attr('id').replace('content_','');
					$(e).attr('data-language',l);
				});
                if ('post-edit' === WPGlobusAdmin.page) {
                    this.post_edit();
					this.set_dialog();
					if ( typeof WPGlobusAioseop != 'undefined' ) {
						WPGlobusAioseop.init();
					}	
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
                } else if ('widgets.php' == WPGlobusAdmin.page) {
					WPGlobusWidgets.init();
					WPGlobusDialogApp.init({title:'Edit text'});
                } else {
                    this.start();
                }
            },
            admin_init: function () {
				var order = $('.wpglobus-addons-group a').data('key');
				if ( 'indefined' != typeof order ) {
					if ( window.location.search.indexOf('page=wpglobus_options&tab='+order) >= 0 ) {
						window.location = 'admin.php?page=wpglobus-addons';
					} else {	
						var addon = $('#toplevel_page_wpglobus_options li').eq(order+1);
						$(addon).find('a').attr('href','admin.php?page=wpglobus-addons').attr('onclick',"window.location=jQuery(this).attr('href');return false;");
					}	
				}
			},	
            options_general: function () {
				var $bn = $('#blogname'),
                    $body = $('body');
				$bn.addClass('hidden');
				$('#wpglobus-blogname').insertAfter($bn).removeClass('hidden');
                $body.on('blur', '.wpglobus-blogname', function () {
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
				$('#wpglobus-blogdescription').insertAfter($bd).removeClass('hidden');
                $body.on('blur', '.wpglobus-blogdesc', function () {
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
                var full_id = '', id = 0;
				
				$(document).ajaxComplete(function(event, jqxhr, settings){
					if (typeof settings.data === 'undefined') {
                        return;
                    }
					if ( full_id == '' ) {
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
                
				$('body').on('change', '.wpglobus-quick-edit-title', function () {
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
						
						$.each( WPGlobusAdmin.data.tags, function(index,tag){
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
				
                $('#the-list').on('click', 'a.editinline', function () {
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
				
				var elements = [];
				elements[0] = 'name';
				elements[1] = 'description';
				
				var make_clone = function(id,language){
					var $element = $('#'+id),
						clone = $element.clone(),
						name = $element.attr('name'),
						classes = 'wpglobus-element wpglobus-element_'+id+' wpglobus-element_'+language,
						node;
				
					node = document.getElementById(id);
					node = node.nodeName;
					$(clone).attr('id', id+'_'+language);
					$(clone).attr('name', name+'_'+language);
					if ( language !== WPGlobusCoreData.default_language ) {
						classes += ' hidden';
					}
					$(clone).attr('class', classes);
					$(clone).attr('data-save-to', id);
					$(clone).attr('data-language', language);
					if ( node == 'INPUT' ) {
						$(clone).attr('value', $('#wpglobus-link-tab-'+language).data(id));
					} else if ( node == 'TEXTAREA' ) {
						$(clone).text($('#wpglobus-link-tab-'+language).data(id));
					}	
					$element.addClass('hidden');
					if ( $('.wpglobus-element_'+id).size() == 0 ) {
						$(clone).insertAfter($element);
					} else {
						$(clone).insertAfter($('.wpglobus-element_'+id).last());
					}	
				};	
				
				$.each(WPGlobusCoreData.enabled_languages, function(i,l){
					$.each(elements, function(i,e){
						make_clone(e,l);
					});						
				});
			
                $('.wpglobus-taxonomy-tabs').insertAfter('#ajax-response');

                // Make class wrap as tabs container
                // tabs on
                $('.wrap').tabs();			
				
				$('body').on('click', '.wpglobus-taxonomy-tabs li', function(event){
					var $t = $(this);
					var language = $t.data('language');
					$('.wpglobus-element').addClass('hidden');
					$('.wpglobus-element_'+language).removeClass('hidden');
				});					
				
                $('.wpglobus-element').on('change', function () {
                    var $this = $(this),
                        save_to = $this.data('save-to'),
                        s = '';

					$('.wpglobus-element').each(function (index, element) {
						var $e = $(element),
							value = $e.val();
						if ( $e.data('save-to') == save_to && value !== '' ) {
							s = s + WPGlobusCore.addLocaleMarks(value, $e.data('language') )
						}
					});
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
				wpNavMenu.refreshAdvancedAccessibility();
				wpNavMenu.menusChanged = false;
				
                $('.wpglobus-menu-item').on('change', function () {
                    var $this = $(this),
                        li, id, so,
                        s = '', $e, item_id = '',
                        lang = [];

                    if ($this.hasClass('wpglobus-item-title')) {
                        li = $this.parents('li');
                        id = li.attr('id');
                        $.each($('#' + id + ' .wpglobus-item-title'), function (index, element) {
                            $e = $(element);
							var l = $e.data('language');
                            if ($e.val() !== '') {
                                s = s + WPGlobusAdmin.data.locale_tag_start.replace('%s', l) + $e.val() + WPGlobusAdmin.data.locale_tag_end;
                            }
							lang[index] = l;
                            item_id = $e.data('item-id');
                        });
						so = $(document).triggerHandler('wpglobus_get_menu_translations', {string:s, lang:lang, id:item_id, type:'input.edit-menu-item-title'});
						if ( typeof so !== 'undefined' ) {
							s = so;		
						}					
                        $('input#edit-menu-item-title-' + item_id).val(s);
                    }

                    if ($this.hasClass('wpglobus-item-attr')) {
                        li = $this.parents('li');
                        id = li.attr('id');
                        $.each($('#' + id + ' .wpglobus-item-attr'), function (index, element) {
                            $e = $(element);
							var l = $e.data('language');
                            if ($e.val() !== '') {
                                s = s + WPGlobusAdmin.data.locale_tag_start.replace('%s', l) + $e.val() + WPGlobusAdmin.data.locale_tag_end;
                            }
							lang[index] = l;
                            item_id = $e.data('item-id');
                        });
						so = $(document).triggerHandler('wpglobus_get_menu_translations', {string:s, lang:lang, id:item_id, type:'input.edit-menu-item-attr-title'});
						if ( typeof so !== 'undefined' ) {
							s = so;		
						}					
                        $('input#edit-menu-item-attr-title-' + item_id).val(s);
                    }

                });
            },
            post_edit: function () {
				// Hook into the heartbeat-send
				$(document).on('heartbeat-send', function(e, data) {
					if ( typeof data['wp_autosave'] !== 'undefined' ) {
						data['wpglobus_heartbeat'] = 'wpglobus';
						$.each(WPGlobusAdmin.data.open_languages, function(i,l){
							var v = $('#title_'+l).val() || '';
							v = $.trim(v);
							if ( v != '' ) {
								data['wp_autosave']['post_title_'+l] = v;
							}	
							v = $('#content_'+l).val() || '';
							v = $.trim(v);
							if ( v != '' ) {
								data['wp_autosave']['content_'+l] = v;
							}	
						});
					}	
				});				
			
				var wrap_at = '#postdivrich',
					set_title = true;	
				if ( WPGlobusAdmin.data.support['editor'] === false ) {
					wrap_at = '#titlediv';	
					set_title = false;	
				}	
				if ( WPGlobusAdmin.data.support['title'] === false ) {
					set_title = false;	
				}	
                // Make post-body-content as tabs container
                $('#post-body-content').prepend($('.wpglobus-post-body-tabs-list'));
                $.each(WPGlobusAdmin.tabs, function (index, suffix) {
                    if ('default' === suffix) {
                        $(wrap_at).wrap('<div id="tab-default"></div>');
						if ( set_title ) {
							$($('#titlediv')).insertBefore(wrap_at);
						}	
                    } else {
                        $(wrap_at+'-' + suffix).wrap('<div id="tab-' + suffix + '"></div>');
						if ( set_title ) {
							$($('#titlediv-' + suffix)).insertBefore(wrap_at+'-' + suffix);
						}
                    }
                });

                // tabs on
                $('#post-body-content').addClass('wpglobus-post-body-tabs').tabs({
					beforeActivate: function( event, ui ){
						var otab = ui.oldTab[0].id.replace('link-tab-','');
						var ntab = ui.newTab[0].id.replace('link-tab-','');
						if ( 'default' == otab ) {
							otab = WPGlobusCoreData.default_language;	
						}	
						if ( 'default' == ntab ) {
							ntab = WPGlobusCoreData.default_language;	
						}	
						$(document).triggerHandler('wpglobus_post_body_tabs', [ otab, ntab ]);
					}
				}); // #post-body-content

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

                    $('body').on('blur', '.wpglobus-excerpt', function () {
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

				// wp_editor word count
				if ( typeof(wpWordCount) != 'undefined' ) {

					var last = 0,
						ls = WPGlobusCoreData.open_languages,
						$d = this.$document,
						lsb = {};

					ls.shift();

					$.each(WPGlobusCoreData.open_languages, function(i,e){
						lsb[e] = 0;
					});

					var wpglobusWordCount = {
						settings : {
							strip : /<[a-zA-Z\/][^<>]*>/g, // strip HTML tags
							clean : /[0-9.(),;:!?%#$¿'"_+=\\/-]+/g, // regexp to remove punctuation, etc.
							w : /\S\s+/g, // word-counting regexp
							c : /\S/g // char-counting regexp for asian languages
						},
						block : lsb,
						wc : function(tx, l, type) {
							var t = this, w, tc = 0;

							if ( l == WPGlobusCoreData.default_language ) {
								w = $('.word-count');
							} else {
								w = $('.word-count-'+l);
							}

							if ( type === undefined )
								type = wordCountL10n.type;
							if ( type !== 'w' && type !== 'c' )
								type = 'w';

							if ( t.block[l] )
								return;

							t.block[l] = 1;

							setTimeout( function() {
								if ( tx ) {
									tx = tx.replace( t.settings.strip, ' ' ).replace( /&nbsp;|&#160;/gi, ' ' );
									tx = tx.replace( t.settings.clean, '' );
									tx.replace( t.settings[type], function(){tc++;} );
								}
								w.html(tc.toString());
								setTimeout( function() { t.block[l] = 0; }, 2000 );
							}, 1 );
						}
					};

					$d.bind( 'wpglobuscountwords', function(e, txt, l) {
						wpglobusWordCount.wc(txt, l);
					});

					$.each(ls, function(i,l){
						var co = $('#content_'+l);
						$d.triggerHandler('wpglobuscountwords', [ co.val(), l ]);
						co.keyup( function(e) {
							var k = e.keyCode || e.charCode;

							if ( k == last ) {
                                return true;
                            }
							if ( 13 == k || 8 == last || 46 == last )
								$d.triggerHandler('wpglobuscountwords', [ co.val(), l ]);

							last = k;
							return true;
						});
					});
					// word recount for default language
					$(document).triggerHandler('wpglobuscountwords', [ $('#content').val(), WPGlobusCoreData.default_language ]);
				}
				// end word count
				
				$('body').on('click', '#publish, #save-post', function() {
					if ( WPGlobusAdmin.data.open_languages.length > 1 ) {
						$(document).triggerHandler('wpglobus_before_save_post');
						// if empty title in default language make it from another titles
						var t = $('#title').val(),
							index, title = '', delimiter = '';

						if ( t.length == 0 ) {
							index = WPGlobusAdmin.data.open_languages.indexOf(WPGlobusAdmin.data.default_language);
							WPGlobusAdmin.data.open_languages.splice(index, 1);
							$(WPGlobusAdmin.data.open_languages).each(function(i,l){
								delimiter = i == 0 ? '' : '-';
								t = $('#title_'+l).val();
								if ( t.length > 0 ) {
									if ( title.length == 0 ) { delimiter = '';}
									title = title + delimiter + t;
								}
							});
						}	
						if ( title.length > 0 ) {
							$('#title').val(title);
						}
					}	
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
				
                $('.ui-state-default').on('click', function () {
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
            },
			set_dialog: function() {
				var ajaxify_row_id, added_control = false;
				var add_elements = function(post_id) {
					
					var id, rows, cb, _cb,
						_classes = 'wpglobus_dialog_start wpglobus_dialog_icon';
					
					_cb = [
						'<div class="wpglobus_dialog_options_wrapper hidden">',
						'<input style="width:initial;" id="wpglobus-cb-{{id}}" data-object="#wpglobus-dialog-start-{{id}}" data-meta-key="{{meta-key}}" class="wpglobus_dialog_option wpglobus_dialog_cb" type="checkbox" {{checked}} />',
						'</div>'
					].join('');
					
					if (typeof post_id == 'undefined') {
						rows = '#the-list tr';
					} else {
						rows = '#the-list tr#'+post_id;
					}	
					$(rows).each(function(){
						var $t = $(this), 
							tid = $t.attr('id'),
							element = $t.find('textarea'),
							clone, name, meta_key,
							classes = _classes;
							
						id = element.attr('id');
						if ( undefined === id ) {
							return true;
						}	
						meta_key = $('#'+tid+'-key').val();
						clone = $('#'+id).clone();
						$(element).addClass('wpglobus-dialog-field-source hidden');
						name = element.attr('name');
						$(clone).attr('id', 'wpglobus-'+id);
						$(clone).attr('name', 'wpglobus-'+name);
						$(clone).attr('data-source-id', id);
						$(clone).attr('class', 'wpglobus-dialog-field');
						$(clone).val( WPGlobusCore.TextFilter($(element).val(), WPGlobusCoreData.language) );
						$(clone).insertAfter(element);
						cb = _cb.replace(/{{id}}/g, id);
						cb =  cb.replace(/{{meta-key}}/g, meta_key);
						if ( undefined === WPGlobusAdmin.data.post_meta_settings[WPGlobusAdmin.data.post_type] ) {
							cb = cb.replace(/{{checked}}/, 'checked');
						} else {
							if ( undefined !== WPGlobusAdmin.data.post_meta_settings[WPGlobusAdmin.data.post_type][meta_key] && WPGlobusAdmin.data.post_meta_settings[WPGlobusAdmin.data.post_type][meta_key] == 'false' ) {
								cb = cb.replace(/{{checked}}/, '');
								classes = _classes+' wpglobus_dialog_start_hidden'; 
							} else {	
								cb = cb.replace(/{{checked}}/, 'checked');
								classes = _classes;
							}	
						}		
						$t.append('<td style="width:20px;"><div id="wpglobus-dialog-start-'+id+'" data-type="control" data-source-type="textarea" data-source-id="'+id+'" class="'+classes+'"></div>'+cb+'</td>');
					});
					if ( ! added_control && $('#list-table .wpglobus_dialog_start').size() > 0 ) {
						$('#list-table thead tr').append('<th class="wpglobus-control-head"><div class="wpglobus_dialog_settings wpglobus_dialog_icon"></div></th>');
						added_control = true;
					}					
				}				
				
				add_elements();				

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

				$(document).ajaxSend(function(event, jqxhr, settings){
					if ( 'add-meta' == settings.action ) {
						ajaxify_row_id = settings.element;
					}	
				});				
				$(document).ajaxComplete(function(event, jqxhr, settings){
					if ( 'add-meta' == settings.action && undefined !== jqxhr.responseXML ) {
						if ( 'newmeta' == ajaxify_row_id ) {
							add_elements('meta-'+$(jqxhr.responseXML.documentElement.outerHTML).find('meta').attr('id'));
						} else {
							add_elements(ajaxify_row_id);	
						}	
					}	
				});

				WPGlobusDialogApp.init({title:'Edit meta'}); 				
				
			}	
        };

        new WPGlobusAdminApp.App();

        return WPGlobusAdminApp;

    }(window.WPGlobusAdminApp || {}, jQuery));
	
});
