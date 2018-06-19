/**
 * WPGlobus Administration Core, Dialog, Admin
 * Interface JS functions
 *
 * @since 1.9.0
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
			haystack = "" + haystack;
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
						/** do nothing */
					} else {
						text = '';
					}
				} else {
					/**
					 * Try RETURN_IN_DEFAULT_LANGUAGE.
					 */
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
		},
		getString: function(s, newVal, l) {
			if ( 'undefined' === typeof(s) ) {
				return s;
			}
			if ( 'undefined' === typeof(newVal) ) {
				newVal = '';
			}
			if ( 'undefined' === typeof(l) ) {
				l = WPGlobusCoreData.language;
			}

			s = api.getTranslations(s);
			s[l] = newVal;

			var cS = '';

			$.each(s, function(ln,val){
				if ( '' != val && ln != WPGlobusCoreData.default_language) {
					cS += api.addLocaleMarks(val, ln);
				}
			});

			if ( '' != s[WPGlobusCoreData.default_language] ) {
				if ( '' == cS ) {
					cS = s[WPGlobusCoreData.default_language];
				} else {
					cS = api.addLocaleMarks(s[WPGlobusCoreData.default_language], WPGlobusCoreData.default_language) + cS;
				}
			}
			return cS;
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
			dialogTitle: '',
			customData: null,
			callback: function(){},
			dialogOptions: {
				title: '',
				placeholder: '',
				formFooter: '',
				beforeOpen: function(){},
				close: function(){},
			},	
			dialog: {}
		},
		form : undefined,
		element : undefined,
		element_by : 'id',
		id : '',
		clone_id: '',
		wpglobus_id : '',
		type : 'textarea',
		source : '',
		order : {},
		value : {},
		request : 'core',
		attrs: {},
		dialogTitle: '',
		trClass: 'wpglobus-translatable',
		startButton: [
			'<span id="wpglobus-dialog-start-{{clone_id}}" ',
			'style="{{style}}" ',
			'data-type="control" data-dialog-title="{{title}}" ',
			'data-source-type="" data-source-id="{{id}}" data-source-name="{{name}}" ',
			'data-nodename="{{nodename}}"',
			'{{sbTitle}} ',
			'class="{{classes}}"></span>'
        ].join(''),
		startButtonClass : 'wpglobus_dialog_start wpglobus_dialog_icon',
		clicks: 0,
		init: function(args) {
			api.option.dialog = api.option.dialogOptions;
			api.option = $.extend(api.option, args);
			$(api.option.dialogTabs).tabs();
			api.dialogTitle = api.option.dialogTitle;
			this.attachListener();
			if ( api.option.customData != null && typeof api.option.customData.addElements != 'undefined' ) {
				$.each(api.option.customData.addElements, function(i,e) {
					api.addElement(e);
				});
			}
		},
		convertToId: function(s){
			s = s.replace(/\]/g,'');
			s = s.replace(/\[/g,'-');
			return s;
		},
		addElement: function(elem) {
			var option = {
				id: null,
				style: '',
				styleTextareaWrapper: '',
				sbTitle: '',
				onChangeClass: '',
				dialogTitle: '',
				dialog: api.option.dialogOptions
			}
			if ( 'string' == typeof(elem) ) {
				option.id = elem;
			} else if ( 'object' == typeof(elem) ) {
				option = $.extend(option, elem);
			} else {
				return;
			}

			var $element = null, id = null, name = null, node = null,
				sb = api.startButton,
				clone, v, style, nodeName = '';

			api.element_by = 'name';
			node = document.getElementsByName(option.id);

			if ( 0 == node.length ) {
				api.element_by = 'id';
				node = document.getElementById(option.id);
			}

			if ( null === node ) {
				return;
			} else {
				id = option.id;
				if ( 'id' == api.element_by ) {
					$element = $('#'+id);
				} else {
					nodeName = node[0].nodeName;
					nodeName = nodeName.toLowerCase();
					$element = $(nodeName+'[name="'+id+'"]');
				}
			}

			if ( 'undefined' === typeof $element.attr('name') || '' == $element.attr('name') ) {
				name = id;
			} else {
				name = $element.attr('name');
			}
			api.clone_id = api.convertToId(id);

			if ( -1 != name.indexOf( 'wpglobus' ) || -1 != api.clone_id.indexOf( 'wpglobus' ) ) {
				/**
				 * To prevent add element to itself.
				 */
				return false;
			}

			if ( $( '#wpglobus-'+api.clone_id ).length > 0 ) {
				/**
				 * WPGlobus element exists already.
				 */
				return false;
			}
			if ( $( nodeName+'[name="wpglobus-'+name+'"]' ).length > 0 ) {
				/**
				 * WPGlobus element exists already.
				 */
				return false;
			}

			clone = $( $element.clone() );
			//$element.addClass('hidden');
			style = $element.attr('style') || '';
			$element.attr( 'style', 'display:none;' );
			clone.attr( 'id', 'wpglobus-'+api.clone_id ).attr( 'name', 'wpglobus-'+name );

			/**
			 * Add WPGlobus translatable class.
			 */
			clone.addClass( api.trClass );

			if ( option.onChangeClass != '' ) {
				/**
				 * add class to bind 'change' event
				 */
				clone.addClass( option.onChangeClass );
			}

			if ( 'id' == api.element_by ) {
				clone.attr('data-source-id', id).attr('data-source-name', '').attr('data-source-get-by',api.element_by);
			} else {
				clone.attr('data-source-id', '').attr('data-source-name', name).attr('data-source-get-by',api.element_by);
			}

			if ( 'textarea' == nodeName ) {
				v = WPGlobusCore.getTranslations( $element.val() )[WPGlobusCoreData['language']];
				clone.val( v );
				clone.attr( 'data-nodename', 'textarea' );
				if ( '' == option.style ) {
					clone.attr( 'style', style + ';width:95%;float:left;' );
				} else {
					clone.attr( 'style', style + ';' + option.style );
				}
			} else {
				v = WPGlobusCore.getTranslations( $element.val() )[WPGlobusCoreData['language']];
				clone.attr( 'value', v );
				clone.attr( 'data-nodename', 'input' );
				if ( '' != option.style ) {
					clone.attr( 'style', style + ';' + option.style );
				}
			}
	
			/**
			 * Add dialog options.
			 * @since 1.7.12
			 */
			if ( '' != option.dialog ) {  
				clone.attr( 'data-dialog', JSON.stringify(option.dialog) );
			}
			
			sb = sb.replace(/{{clone_id}}/g, api.clone_id);
			if ( 'id' == api.element_by ) {
				sb = sb.replace(/{{id}}/g, api.clone_id);
				sb = sb.replace(/{{name}}/g, '');
				sb = sb.replace(/{{nodename}}/g, '');
			} else {
				sb = sb.replace(/{{id}}/g, '');
				sb = sb.replace(/{{name}}/g, name);
				sb = sb.replace(/{{nodename}}/g, nodeName);
			}
			sb 					   = 'textarea' == nodeName ? sb.replace( '{{style}}', 'float:left;margin-top:0;' ) : sb.replace( '{{style}}', '' );
			var startButtonClasses = 'textarea' == nodeName ? api.startButtonClass + ' wpglobus-textarea wpglobus-textarea-'+api.clone_id : api.startButtonClass;
			sb = sb.replace( '{{classes}}', startButtonClasses );
			sb = option.dialogTitle == '' ? sb.replace('{{title}}', api.dialogTitle) : sb.replace('{{title}}', option.dialogTitle);
			sb = option.sbTitle == '' ? sb.replace('{{sbTitle}}', option.sbTitle) : sb.replace('{{sbTitle}}', 'title="'+option.sbTitle+'"');

			$(sb).insertAfter($element);
			$(clone).insertAfter($element);

			if ( 'textarea' == nodeName ) {
				$('#wpglobus-'+api.clone_id).addClass( 'wpglobus-textarea-'+api.clone_id );
				$('.wpglobus-textarea-'+api.clone_id).wrapAll( '<div class="wpglobus-textarea-wrapper" style="'+option.styleTextareaWrapper+'"></div>' );
			}

			/**
			 * Bind change event
			 */
			var selector, ret = false;
			if 	( option.onChangeClass == '' ) {
				selector = '#wpglobus-' + api.clone_id;
			} else {
				selector = '.' + option.onChangeClass;
				var $events = $._data( $(document)[0], 'events' );
				if( typeof $events === 'undefined' ){
					ret = true;
				} else {
					if ( typeof $events.change !== 'undefined' ) {
						$.each( $events.change, function(i, ev){
							if ( ev.selector == selector ) {
								ret = true;
								return false;
							}
						});
					}
				}
			}

			if ( ret ) {
				/**
				 * Return because we had bound 'change' event already.
				 */
				return true;
			}

			$(document).on( 'change', selector, function() {
				var $t = $(this),
					sid = $t.data( 'source-id' );

				if ( '' == sid ) {
					sid = $t.data( 'nodename' ) + '[name="' + $t.data( 'source-name' ) + '"]';
				} else {
					sid = '#' + sid;
				}
				$(sid).val( WPGlobusCore.getString( $(sid).val(), $t.val() ) );
			});
			return true;
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
			open: function( event, ui ) {
				var title = api.dialogTitle;
				if ( typeof api.attrs.maxlength !== 'undefined' ) {
					$('.wpglobus_dialog_textarea').attr('maxlength', api.attrs.maxlength);
					title += ' | maxlength='+api.attrs.maxlength;
				}
				$('.wpglobus-dialog .ui-dialog-title').text(title);
			},
			close: function() {
				/**
				 * Close callback.
				 */	
				api.runCallback( api.option.dialog.close );
				
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
			$(document).on('click', api.option.listenClass, function(e) {
				api.element = $(this);
				api.id = api.element.data('source-id');
				if ( '' == api.id ) {
					api.id = api.element.data('nodename') + '[name="'+api.element.data('source-name')+'"]';
					api.wpglobus_id = '#wpglobus-'+api.convertToId( api.element.data('source-name') );
				} else {
					api.wpglobus_id = '#wpglobus-'+api.id;
					api.id = '#'+api.id;
				}

				api.clicks++;
				if ( api.clicks == 1 ) {
					setTimeout(function () {
						if (api.clicks == 1) {
							api.onClick(e);
						} else {
							var s = $(api.id);
							if ( s.hasClass('hidden') ) {
								s.removeClass('hidden').attr('style', 'display:block;');
							} else {
								s.addClass('hidden').attr('style', 'display:none;');
							}
						}
						api.clicks = 0;
					}, 200);
				}
			});
			api.form = api.dialog.find('form#wpglobus-dialog-form').on('submit', function( event ) {
				event.preventDefault();
				api.saveDialog();
			});
		},
		ajax : function(order) {
			return $.ajax({type:'POST', url:WPGlobusAdmin.ajaxurl, data:{action:WPGlobusAdmin.process_ajax, order:order}, dataType:'json', async:false});
		},
		onClick: function(ev) {
			if ( typeof(api.element.data('dialog-title')) == 'undefined' || '' == api.element.data('dialog-title') ) {
				api.dialogTitle = api.option.dialogTitle;
			} else {
				api.dialogTitle = api.element.data('dialog-title');
			}
			if ( typeof api.id !== 'undefined' ) {
				api.attrs['maxlength'] = $(api.id).attr('maxlength');
			}

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

			/**
			 * Get dialog form options.
			 */
			api.option.dialog = $.extend( {}, api.option.dialogOptions, $(api.wpglobus_id).data('dialog') );
			
			if ( '' != api.option.dialog.title ) {
				api.dialogTitle = api.option.dialog.title;
			}

			$.each(api.value, function(l,e){
				var $d = $('#wpglobus-dialog-'+l);
				/**
				 * Value.
				 */
				$d.val(e);
				
				/**
				 * Placeholder.
				 */				
				$d.attr( 
					'placeholder', 
					WPGlobusCore.TextFilter( api.option.dialog.placeholder, l, 'RETURN_IN_DEFAULT_LANGUAGE' )
				);				
			});
			
			/**
			 * Dialog form footer.
			 */				
			$('#wpglobus-dialog-form-footer').html(api.option.dialog.formFooter);
	
			/**
			 * Before open callback.
			 */		
			api.runCallback( api.option.dialog.beforeOpen );
			
			api.dialog.dialog('open');
		},
		runCallback: function(callback) {

			if ( 'object' === typeof callback ) {
				var k  = Object.keys(callback)[0];
				var fn = callback[Object.keys(callback)[0]]
				if ( 'window' === k ) {
					if ( 'function' === typeof window[fn] ) {
						window[fn]( callback[Object.keys(callback)[1]] );
					}
				} else if ( 'function' === typeof window[k][fn] ) {
					window[k][fn]( callback[Object.keys(callback)[1]] );
				}
			} else if ( 'string' === typeof callback ) {
				if ( 'function' === typeof window[callback] ) {
					window[callback]();
				}
			}			
	
		}
	};

})(jQuery);

jQuery(document).ready(function () {
    "use strict";
    window.WPGlobusAdminApp = (function (WPGlobusAdminApp, $) {
        /* Object Constructor
         ========================*/
        WPGlobusAdminApp.App = function (config) {

            if ( 'undefined' !== typeof window.WPGlobusAdminApp ) {
                return;
            }

            this.config = {
                debug: false,
                version: WPGlobusAdmin.version
            };

            this.status = 'ok';

            if ( 'undefined' === typeof WPGlobusAdmin ) {
                this.status = 'error';
                if (this.config.debug) {
                    console.log('WPGlobus: error options loading');
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
				WPGlobusCoreData.multisite = this.parseBool(WPGlobusCoreData.multisite);
				this.adminInit();
				$('#content').addClass('wpglobus-editor').attr('data-language',WPGlobusAdmin.data.default_language);
				$('textarea[id^=content_]').each(function(i,e){
					var l=$(e).attr('id').replace('content_','');
					$(e).attr('data-language',l);
				});
                if ('post.php' === WPGlobusAdmin.page) {
                    this.postEdit();
					this.set_dialog();
					if ( 'undefined' !== typeof WPGlobusAioseop ) {
						WPGlobusAioseop.init();
					}
                } else if ('menu-edit' === WPGlobusAdmin.page) {
					/**
					 * @since 1.9.16
					 */
					WPGlobusDialogApp.init({dialogTitle:'Edit description'});
                    this.navMenus();
                } else if ('taxonomy-edit' === WPGlobusAdmin.page) {
                    if (WPGlobusAdmin.data.tag_id) {
                        this.taxonomyEdit();
                    }
                } else if ('taxonomy-quick-edit' === WPGlobusAdmin.page) {
                    this.quickEdit('taxonomy');
                } else if ('edit.php' === WPGlobusAdmin.page) {
                    this.quickEdit('post');
					if( _.size(WPGlobusAdmin.$_get) > 0 ) {
						WPGlobusDialogApp.init();
					}
                } else if ('options-general.php' == WPGlobusAdmin.page) {
					this.optionsGeneral();
					WPGlobusDialogApp.init({customData:WPGlobusCoreData.page_custom_data});
                } else if ('widgets.php' == WPGlobusAdmin.page) {
					WPGlobusWidgets.init();
					WPGlobusDialogApp.init({dialogTitle:'Edit text'});
                } else if ('wpglobus_options' == WPGlobusAdmin.page) {
                    this.start();
                } else if ('wpglobusAdminCentral' == WPGlobusAdmin.page) {
					this.adminCentral();
                } else {
					/**
					 * Init WPGlobusDialogApp for using in a 3-party plugins.
					 */
					WPGlobusDialogApp.init({customData:WPGlobusCoreData.page_custom_data});
				}
            },
			parseBool: function(b)  {
				return !(/^(false|0)$/i).test(b) && !!b;
			},
			getCurrentTab: function() {
				return $( '.wpglobus-post-body-tabs-list .ui-tabs-active' ).data( 'language' );
			},
            adminInit: function () {
				var order = $('.wpglobus-addons-group a').data('key');
				if ( 'undefined' !== typeof order ) {
					if ( window.location.search.indexOf('page=wpglobus_options&tab='+order) >= 0 ) {
						if ( WPGlobusCoreData.multisite ) {
							window.location = WPGlobusCoreData.pluginInstallLocation.multisite;
						} else {
							window.location = WPGlobusCoreData.pluginInstallLocation.single;
						}
					} else {
						var addon = $('#toplevel_page_wpglobus_options li').eq(order+1);
						if ( WPGlobusCoreData.multisite ) {
							$(addon).find('a').attr('href',WPGlobusCoreData.pluginInstallLocation.multisite).attr('onclick',"window.location=jQuery(this).attr('href');return false;");
						} else {
							$(addon).find('a').attr('href',WPGlobusCoreData.pluginInstallLocation.single).attr('onclick',"window.location=jQuery(this).attr('href');return false;");
						}
					}
				}
			},
            optionsGeneral: function() {
				var $bn = $('#blogname'),
                    $body = $('body');

				$bn.addClass('hidden');
				$('#wpglobus-blogname').insertAfter($bn).removeClass('hidden');

				$body.on('blur', '.wpglobus-blogname', function () {
                    $('.wpglobus-blogname').each( function (i, e) {
                        var $e = $(e);
						$bn.val( WPGlobusCore.getString( $bn.val(), $e.val(), $e.data('language') ) );
                    });
                });

				var $bd = $('#blogdescription');
				$bd.addClass('hidden');
				$('#wpglobus-blogdescription').insertAfter($bd).removeClass('hidden');
                $body.on('blur', '.wpglobus-blogdesc', function () {
                    $('.wpglobus-blogdesc').each( function (i, e) {
                        var $e = $(e);
						$bd.val( WPGlobusCore.getString( $bd.val(), $e.val(), $e.data('language') ) );
                    });
                });
			},
            quickEdit: function(type) {
				/**
				 * For more info @see ajax handler 'wp_ajax_inline_save'.
				 */
                if ( 'undefined' === typeof WPGlobusAdmin.data.has_items ) {
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
							s = WPGlobusCore.getString( s, $e.val(), l );
						}
						WPGlobusAdmin.qedit_titles[ id ][ l ][ 'name' ] = $e.val();
						lang[ index ] = l;
                    });

					var so = $(document).triggerHandler( 'wpglobus_get_translations', {string:s, lang:lang, id:id} );
					if ( typeof so !== 'undefined' ) {
						s = so;
					}
                    $( 'input.ptitle' ).eq( 0 ).attr( 'value',  s ) ;
					WPGlobusAdmin.qedit_titles[ id ][ 'source' ] = s;
                });

				if ( typeof WPGlobusAdmin.data.tags !== 'undefined' ) {
					$.each( WPGlobusAdmin.data.tags, function(i,tag){
						WPGlobusAdmin.data.value[tag]['post_id'] = {};
					});
				}

				$('button.save, input#bulk_edit').on('mouseenter', function (event) {
					/**
					 * Quick edit action for the "Tags" box in edit.php page.
					 */
					if ( typeof WPGlobusAdmin.data.tags === 'undefined' ) {
                        return;
                    }

					if (event.currentTarget.id=='bulk_edit') {
						$('input#bulk_edit').unbind('click');
					} else {
						$('button.save').unbind('click');
					}

					$( 'button.save, input#bulk_edit').on('click', function (event) {
						//console.log( 'Start Updating' );
						var promise	= $.when();

						var tagsHandler = function( $elem ) {
							$elem.next('.spinner').css({'visibility':'visible'});
							if (event.currentTarget.id != 'bulk_edit') {
								$.ajaxSetup({async:false});
							}
							var p = $elem.parents('tr');
							var id = p.attr('id').replace('edit-','');
							var t,v,newTags;

							$.each( WPGlobusAdmin.data.tags, function(index,tag){
								t = p.find("textarea[name='" + WPGlobusAdmin.data.names[tag] + "']");
								if ( t.length == 0 ) {
									return true;
								}
								WPGlobusAdmin.data.value[tag]['post_id'][id] = t.val();
								v = WPGlobusAdmin.data.value[tag]['post_id'][id].split(',');
								newTags = [];
								for(var i=0; i<v.length; i++) {
									v[i] = v[i].trim(' ');
									if ( v[i] != '' ) {
										if ( typeof WPGlobusAdmin.data.tag[tag][v[i]] === 'undefined' ) {
											newTags[i] = v[i];
										} else {
											newTags[i] = WPGlobusAdmin.data.tag[tag][v[i]];
										}
									}
								}
								t.val( newTags.join(', ') );
							});
						}

						var $this = $(this);
						var start = $.Deferred();
						start.resolve( tagsHandler( $(this) ) );

						promise = promise.then(function() {
							return $.when(
								start.done()
							)
						}).then( function() {
							if (event.currentTarget.id != 'bulk_edit') {
								setTimeout(
									function() {
										inlineEditPost.save(id);
										$.ajaxSetup({async:true});
									},
									50
								);
							}
						});

					});
				});

                $( '#the-list' ).on( 'click', 'a.editinline', function () {
					var t = $(this);
					full_id = t.parents('tr').attr('id');
                    if ('post' === type) {
                        id = full_id.replace('post-', '');
                    } else if ('taxonomy' === type) {
                        id = full_id.replace('tag-', '');
                    } else {
						return;
					}

					if ( 'post' === type && 'undefined' !== typeof WPGlobusAdmin.data.tags ) {
						/**
						 * @since 1.6.6
						 */
						$.each( WPGlobusAdmin.data.tags, function(i,tag){
							if ( WPGlobusAdmin.data.value[tag] != '' ) {
								var val = $('#edit-' + id + ' textarea[name="' + WPGlobusAdmin.data.names[tag] + '"]').val(),
									currentTags;
								if ( 'undefined' !== typeof val ) {
									currentTags = val.split(',');
									$.each( currentTags, function(order,currentTag) {
										val = val.replace(currentTag, WPGlobusCore.TextFilter(currentTag, WPGlobusCoreData.language));
									});
									$('#edit-' + id + ' textarea[name="' + WPGlobusAdmin.data.names[tag] + '"]').val(val);
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

                    $( '.wpglobus-quick-edit-title' ).each(function ( i, e ) {
						var $e = $(e);
                        var l = $e.data( 'language' );
                        $e.attr( 'id', l + id );
                        if ( typeof  WPGlobusAdmin.qedit_titles[id][l] !== 'undefined' ) {
							WPGlobusAdmin.qedit_titles[id][l]['name'] = WPGlobusAdmin.qedit_titles[id][l]['name'].replace( /\\\'/g, "'" );
							WPGlobusAdmin.qedit_titles[id][l]['name'] = WPGlobusAdmin.qedit_titles[id][l]['name'].replace( /\\\"/g, '"' );
                            $e.attr( 'value', WPGlobusAdmin.qedit_titles[id][l]['name'] );
							WPGlobusAdmin.qedit_titles[id]['source'] =
								WPGlobusCore.getString(
									WPGlobusAdmin.qedit_titles[id]['source'],
									WPGlobusAdmin.qedit_titles[id][l]['name'],
									l
								);
                        }
                    });
					$( 'input.ptitle' ).eq( 0 ).attr( 'value', WPGlobusAdmin.qedit_titles[ id ][ 'source' ] );
                });

            },
            taxonomyEdit: function () {

				var elements = [];
				elements[0] = 'name';
				elements[1] = 'description';

				var make_clone = function(id,language){
					var $element = $('#'+id),
						clone = $element.clone(),
						name = $element.attr('name'),
						classes = 'wpglobus-element wpglobus-element_'+id+' wpglobus-element_'+language+' wpglobus-translatable',
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
					if ( $('.wpglobus-element_'+id).length == 0 ) {
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

                /**
				 * Make class wrap as tabs container.
                 * Tabs on.
				 */
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
				
				/**
				 * @since 1.8.1
				 * $('<span class="wpglobus-multilingual-slug wpglobus_dialog_start wpglobus_dialog_icon" title="Title"></span>').insertBefore('#slug');
				 * $('.term-slug-wrap th').css({'padding-right':'0'});
				 * $('.term-slug-wrap td').css({'padding-left':'0'});				 
				 */
				$(WPGlobusAdmin.data.multilingualSlug.title).insertAfter('.term-slug-wrap th label');
				
            },
            navMenus: function () {
                var iID, menu_size,
                    menu_item = '#menu-to-edit .menu-item';

                var timer = function () {
                    if ( menu_size !== $(menu_item).length ) {
                        clearInterval(iID);
                        $(menu_item).each(function (index, li) {
                            var $li = $(li);
                            if ($li.hasClass('wpglobus-menu-item')) {
                                return; /** the same as continue */
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
                            menu_size = $(menu_item).length;
                            iID = setInterval(timer, 500);
                        }
                    }
                });

                $(menu_item).each(function (index, li) {

                    var id = $(li).attr('id'),
                        item_id = id.replace('menu-item-', '');

                    $.each(['input.edit-menu-item-title', 'input.edit-menu-item-attr-title'], function (input_index, input) {
                        var $i = $('#' + id + ' ' + input);
						if ( $i.val() != WPGlobusAdmin.data.items[ item_id ][ input ][ 'source' ] ) {
							/**
							 * fix for case when value resets by WP core
							 */
							$i.val( WPGlobusAdmin.data.items[ item_id ][ input ][ 'source' ] );
						}

                        var p = $( '#' + id + ' ' + input ).parents('p');
                        var height = 0;

                        $.each(WPGlobusAdmin.data.open_languages, function (index, language) {
                            var new_element = $i.clone();
                            new_element.attr('id', $i.attr('id') + '-' + language);
                            new_element.attr('name', $i.attr('id') + '-' + language);
                            new_element.attr('data-language', language);
                            new_element.attr('data-item-id', item_id);
                            new_element.attr('placeholder', WPGlobusAdmin.data.en_language_name[language]);

                            var classes = WPGlobusAdmin.data.items[item_id][language][input]['class'];
                            if (input_index === 0 && language === WPGlobusAdmin.data.default_language) {
                                new_element.attr('class', classes + ' edit-menu-item-title');
                            } else {
                                new_element.attr('class', classes);
                            }

							if ( WPGlobusAdmin.data.items[ item_id ][ language ][ input ][ 'caption' ] != '' ) {
								new_element.attr('value', WPGlobusAdmin.data.items[item_id][language][input]['caption']);
							} else {
								new_element.attr('value', '');
							}
							new_element.css('margin-bottom', '0.6em');
							$(p).append( new_element );
							height = index;
                        });
                        height = (height + 1) * 40;
                        $i.css('display', 'none').attr('class', '').addClass('widefat wpglobus-hidden');
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

				/**
				 * Run the item handle title when the navigation label was loaded.
				 * @see wp-admin\js\nav-menu.js
				 */
				$('.edit-menu-item-title').trigger('change');
				wpNavMenu.refreshAdvancedAccessibility();
				wpNavMenu.menusChanged = false;

                $('.wpglobus-menu-item').on('change', function () {
                    var $this = $(this),
						item_id = $this.data('item-id'),
						s, so;
                    if ($this.hasClass('wpglobus-item-title')) {
						s = WPGlobusCore.getString( $('input#edit-menu-item-title-' + item_id).val(), $this.val(), $this.data('language') );
						so = $(document).triggerHandler('wpglobus_get_menu_translations', {string:s, lang:WPGlobusCoreData.open_languages, id:item_id, type:'input.edit-menu-item-title'});
						if ( typeof so !== 'undefined' ) {
							s = so;
						}
                        $('input#edit-menu-item-title-' + item_id).val(s);
                    }
                    if ($this.hasClass('wpglobus-item-attr')) {
						s = WPGlobusCore.getString( $('input#edit-menu-item-attr-title-' + item_id).val(), $this.val(), $this.data('language') );
						so = $(document).triggerHandler('wpglobus_get_menu_translations', {string:s, lang:WPGlobusCoreData.open_languages, id:item_id, type:'input.edit-menu-item-attr-title'});
						if ( typeof so !== 'undefined' ) {
							s = so;
						}
                        $('input#edit-menu-item-attr-title-' + item_id).val(s);
                    }

                });
				
				/**
				 * @since 1.9.16
				 */
				var menuItems = [];
				setTimeout(function () {
					$('.edit-menu-item-description').each(function() {
						var id = $(this).attr('id');
						menuItems.push(id);
						WPGlobusDialogApp.addElement(id);
					});
					$('.wpglobus_dialog_start').each(function() {
						$(this).css({'font-style':'normal'});
						$(this).after('<br />');
					});
					var order = {};
					order['action'] 	= 'getNavMenuItemsDescription';
					order['menuItems'] 	= menuItems;
					$.ajax({type:'POST', url:WPGlobusAdmin.ajaxurl, data:{action:WPGlobusAdmin.process_ajax, order:order}, dataType:'json'})
					.done(function (results) {
						if ( 'object' != typeof results ) {
							return;
						}
						$.each(results, function( id, value ) {
							if ( '' != value ) {
								$('#'+id).val(value);
							}
						});
					})
					.fail(function (error) {
					})
					.always(function (jqXHR, status) {
					});
				}, 1000 );
	
            },
            postEdit: function () {
				/**
				 * Hook into the heartbeat-send.
				 */
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
					set_title = true,
					content_tabs_id = '#post-body-content';
				if ( WPGlobusAdmin.data.support['editor'] === false ) {
					wrap_at = '#titlediv';
					set_title = false;
				}
				if ( WPGlobusAdmin.data.support['title'] === false ) {
					set_title = false;
				}
                /**
				 * Make post-body-content as tabs container.
				 */
                $(content_tabs_id).prepend($('.wpglobus-post-body-tabs-list'));
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

                /**
				 * Tabs on.
				 */
                $(content_tabs_id).addClass('wpglobus-post-body-tabs').tabs({
					beforeActivate: function( event, ui ){
						var otab = ui.oldTab[0].id.replace('link-tab-','');
						var ntab = ui.newTab[0].id.replace('link-tab-','');
						if ( 'default' == otab ) {
							otab = WPGlobusCoreData.default_language;
						}
						if ( 'default' == ntab ) {
							ntab = WPGlobusCoreData.default_language;
						}
						var a = $(document).triggerHandler('wpglobus_post_body_tabs', [ otab, ntab ]);
						if ( a || typeof a === 'undefined' ) {
							return true;
						}
						return false;
					}
				}); /** #post-body-content */

                /**
				 * Setup for default language.
				 */
                $('#title').val(WPGlobusAdmin.title);

                /**
                 * See other places with the same bookmark.
                 * @bookmark EDITOR_LINE_BREAKS
                 */
                //$('#content').text(WPGlobusAdmin.content.replace(/\n/g, "<p>"));

                $('#content').text(WPGlobusAdmin.content);

				if (typeof WPGlobusVendor !== "undefined" && WPGlobusVendor.vendor.WPSEO ) {
					if ( typeof wpglobus_wpseo !== "undefined" ) {
						wpglobus_wpseo();
					} else if ( 'undefined' !== typeof WPGlobusYoastSeo ) {
						if ( 'undefined' !== typeof WPGlobusYoastSeoPremium ) {
							/** 
							 * @since WPGlobus 1.7.2 
							 */
							if ( WPGlobusYoastSeoPremium ) { 
								WPGlobusYoastSeoPremium.init();
							}
						}
						/**
						 * @since Yoast SEO 3.0
						 */
						WPGlobusYoastSeo.init();
					}
                }

                if ( WPGlobusAdmin.data.modify_excerpt ) {
					/**
					 * Add excerpt fields from template.
					 */
					var $excerpt = $( '#excerpt' );
					$excerpt.addClass( 'hidden' ).css( {'display':'none'} );
                    $( WPGlobusAdmin.data.template ).insertAfter( $excerpt );
                    $( 'body' ).on( 'change', '.wpglobus-excerpt', function () {
						var $t = $( this );
						$excerpt.val( WPGlobusCore.getString( $excerpt.val(), $t.val(), $t.data('language') ) );
                    });
                }

				/**
				 * wp_editor word count.
				 * from WordPress 4.3 @see \wp-admin\js\post.js
				 */
				if ( typeof wp.utils !== 'undefined' && typeof wp.utils.WordCounter !== 'undefined' ) {
					WPGlobusCoreData.wordCounter = {};

					var self = this, wpglobusEditors = {};

					$.each( WPGlobusCoreData.enabled_languages, function( i, l ){
						if ( l == WPGlobusCoreData.default_language ) {
							return true;
						}
						wpglobusEditors[i] = 'content_'+l;
						
						( function( $, counter, l ) {
							WPGlobusCoreData.wordCounter[ l ] = {};
							WPGlobusCoreData.wordCounter[ l ][ 'counter' ] = counter;

							$( function() {

								WPGlobusCoreData.wordCounter[ l ][ 'content' ] = $( '#content_'+l );
								WPGlobusCoreData.wordCounter[ l ][ 'count' ]   = $( '#wp-word-count-'+l ).find( '.word-count-'+l );

								WPGlobusCoreData.wordCounter[ l ][ 'prevCount' ] = 0;

								function update( l ) {
									var text, count;

									if ( typeof l === 'object' ) {

										if ( l == 'tinymce' ) {
											/** wysiwyg editor */
											l = self.getCurrentTab();
										} else {
											/** textarea */
											l = l.target.id.replace( 'content_', '' );
										}

									}

									if ( typeof WPGlobusCoreData.wordCounter[ l ] === 'undefined' ) {
										return;
									}

									if ( ! WPGlobusCoreData.wordCounter[ l ][ 'contentEditor' ] ||
											WPGlobusCoreData.wordCounter[ l ][ 'contentEditor' ].isHidden() ) {

										text = WPGlobusCoreData.wordCounter[ l ][ 'content' ].val();

									} else {
										text = WPGlobusCoreData.wordCounter[ l ][ 'contentEditor' ].getContent( { format: 'raw' } );
									}
			
									count = WPGlobusCoreData.wordCounter[ l ][ 'counter' ].count( text );

									if ( count !== WPGlobusCoreData.wordCounter[ l ][ 'prevCount' ] ) {
										WPGlobusCoreData.wordCounter[ l ][ 'count' ].text( count );
									}

									WPGlobusCoreData.wordCounter[ l ][ 'prevCount' ] = count;
								}

								$(document).on( 'tinymce-editor-init', function( event, editor ) {

									if ( -1 == $.inArray(editor.id, wpglobusEditors) ) {
										/**
										 * Init WPGlobus editor only.
										 */
										return;
									}
									var l = editor.id.replace( 'content_', '' );

									WPGlobusCoreData.wordCounter[ l ][ 'contentEditor' ] = editor;

									editor.on( 'nodechange keyup', _.debounce( update, 1000 ) );
								} );

								WPGlobusCoreData.wordCounter[l]['content'].on( 'input keyup', _.debounce( update, 1000 ) );

								update( l );

							} );
						} )( jQuery, new wp.utils.WordCounter(), l );
					});

				}

				$(document).on('click', '#publish, #save-post', function() {
					if ( WPGlobusAdmin.data.open_languages.length > 1 ) {
						$(document).triggerHandler('wpglobus_before_save_post', {content_tabs_id:content_tabs_id});
						/**
						 * If empty title in default language make it from another titles.
						 */
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

					/**
					 * To handle taxonomy tags.
					 */
					if ( 'undefined' === typeof WPGlobusAdmin.data.tagsdiv || WPGlobusAdmin.data.tagsdiv.length < 1 ) {
						return;
					}
					$(WPGlobusAdmin.data.tagsdiv).each(function(i,tagsdiv){
                        if ($('#' + tagsdiv).length == 0) {
                            /**
							 * Next iteration.
							 */
                            return true;
                        }

						var	id = tagsdiv.replace('tagsdiv-', '');
						if ( 'undefined' === id ) {
                            return true;
                        }
						if ( $('#tax-input-'+id).length == 0 ) {
                            return true;
                        }

						var name, tags = [];

						/**
						 * Obsolete from WP 4.9
						 *  $('#tagsdiv-'+id+' .tagchecklist > span').each(function(i,e){
						 */
						$('#tagsdiv-'+id+' .tagchecklist li').each(function(i,e){
							name = $(e).html();
							name = name.replace( /<button.*<\/button>&nbsp;/, '' );
							if ( 'undefined' === typeof WPGlobusAdmin.data.tag[id][name] ) {
								tags[i] = name;
							} else {
								tags[i] = WPGlobusAdmin.data.tag[id][name];
							}
						});

						$('#tax-input-'+id).val(tags.join(', '));
					});
					/**
					 * The end to handle taxonomy tags.
					 */
				});

				/**
				 * The alignment when default tab was clicked.
				 */
                $('.ui-state-default').on('click', function () {
                    if ('link-tab-default' === $(this).attr('id')) {
                        $(window).scrollTop($(window).scrollTop() + 1);
                        $(window).scrollTop($(window).scrollTop() - 1);
                    }
                });
				
				/**
				 * Set current value after language tab of content was changed.
				 */				
				$(document).on( 'tabsactivate', content_tabs_id, function( event, ui ) {
					WPGlobusAdmin.currentTab = ui.newTab[0].dataset.language;
				});
				
				$(document).triggerHandler('wpglobus_after_post_edit');

            },
            adminCentral: function () {
				$( '.wpglobus-admin-central-tab' ).css({ 'display':'none' });

				if ( $( '.nav-tab-active' ).length > 1 ) {
					$( '.wpglobus-about-wrap .nav-tab-wrapper a' ).removeClass( 'nav-tab-active' );
				}

				var setFirstElement = true;
				if ( 0 == location.hash.indexOf( '#' ) ) {
					$( '.wpglobus-about-wrap .nav-tab-wrapper a').each( function( i, e ) {
						if ( $(e).attr( 'href' ) == location.hash ) {
							setFirstElement = false;
							$(e).addClass( 'nav-tab-active' );
						}
					});
				}
				if ( setFirstElement ) {
					$( '.wpglobus-about-wrap .nav-tab-wrapper a' ).eq(0).addClass( 'nav-tab-active' );
				}

				var activePanel = $( '.wpglobus-about-wrap .nav-tab-active' ).data( 'tab-id' );
				if ( '' != activePanel ) {
					$( '#'+activePanel ).css({'display':'block'});
				}

				$(document).on( 'click', '.wpglobus-about-wrap .nav-tab', function(event){
					var $t = $( this );
					if ( $t.hasClass( 'nav-tab-active' ) ) {
						return;
					}
					$( '.wpglobus-admin-central-tab' ).css({ 'display':'none' });
					$( '.wpglobus-about-wrap .nav-tab' ).removeClass( 'nav-tab-active' );
					$t.addClass( 'nav-tab-active' );
					if ( '' != $t.data( 'tab-id' ) ) {
						$( '#' + $t.data( 'tab-id' ) ).css({ 'display':'block' });
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

				if ( 'undefined' !== typeof WPGlobusAdmin.data.customFieldsEnabled ) {
					WPGlobusAdmin.data.customFieldsEnabled = this.parseBool( WPGlobusAdmin.data.customFieldsEnabled );
					if ( ! WPGlobusAdmin.data.customFieldsEnabled ) {
						return;
					}
				}

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
					if ( ! added_control && $('#list-table .wpglobus_dialog_start').length > 0 ) {
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

				WPGlobusDialogApp.init({dialogTitle:'Edit meta'});

			}
        };

        new WPGlobusAdminApp.App();

        return WPGlobusAdminApp;

    }(window.WPGlobusAdminApp || {}, jQuery));

});
