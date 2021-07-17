/**
 * WPGlobus Administration Widgets
 * Interface JS functions
 *
 * @since 1.0.6
 * @since 2.4.17 To use WPGlobusCoreData.language instead of WPGlobusCoreData.default_language.
 *               Hide unneeded dialog icons.
 * @since 2.6.0 Code refactored for Image widgets.
 * @since 2.8.0 Don't run with WP 5.8 when the block editor enables for managing widgets.
 *
 * @package WPGlobus
 * @subpackage Administration
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusCore, WPGlobusCoreData, WPGlobusWidgets*/

(function($) {
    "use strict";
	
	if ( typeof WPGlobusWidgets === 'undefined' ) {
		return;	
	}
	
	var api = {
		saveArbitraryTextOrHTML: false,
		editor: {},
		languageBoxActive: false,
		languageBoxTimeout: null,
		imageWidgets: {},
		parseBool: function(b){return !(/^(false|0)$/i).test(b) && !!b;},		
		init: function() {
			if ( api.parseBool(WPGlobusWidgets.useWidgetsBlockEditor) ) {
				return;
			}
			api.addElements();
			api.attachListeners();
			api.arbitraryTextOrHTML();
		},
		wysiwygClean: function(){
			// remove wpglobus textarea and dialog start button from wysiwyg.
			$('.wpglobus-dialog-field').each(function(i,e){
				var source = $(e).data('source-id');
				if (  $('#'+source+'-tmce').length == 1 ) {
					var ds = $(e).next('.wpglobus_dialog_start');
					$(e).remove();
					$(ds).remove();
				}	
			});
		},
		setupWidgetControl: function(widgetName, controls, editor) {

			var ret = false;
			$.each( api.editor, function(id, data){
				if ( data['widgetName'] == widgetName ) {
					ret = true;
					return false;
				}
			});
		
			if (ret) return;

			var controlElementID = controls.$el[0].id;
			if ( '' == controlElementID ) {
				/**
				 * We have empty controls.$el[0].id @since WP 4.8.1.
				 * Fix it.
				 */
				var p = controls.$el[0].offsetParent;
				controlElementID = $(p).attr('id');
				if ( 'undefined' === typeof controlElementID ) {
					/**
					 * Case when WYSIWYG text widgets more than 1.
					 * @since 1.8.6
					 */
					$('.widget').each( function(i,e){
						var id = $(e).attr('id');
						if( -1 != id.indexOf(widgetName) ) {
							controlElementID = id;
							return false;
						}
						
					});
				}
			}
			
			if ( 'undefined' === typeof controlElementID ) {
				/**
				 * For testing purposes.
				 * Maybe there will be changes in next versions WP.
				 */
				// console.log('controlElementID: '+controlElementID); 
			}
			
			var sourceSelector 		= '#'+controlElementID+' #widget-'+widgetName+'-text';
			var sourceTitleSelector = '#'+controlElementID+' #widget-'+widgetName+'-title';

			api.editor[editor.id] = {}; 
			api.editor[editor.id]['widgetName'] 			= widgetName;
			api.editor[editor.id]['controls'] 				= controls;
			api.editor[editor.id]['sourceTitleSelector'] 	= sourceTitleSelector;
			api.editor[editor.id]['titleSelector'] 			= null;
			api.editor[editor.id]['sourceSelector'] 	 	= sourceSelector;
			api.editor[editor.id]['content'] 			 	= $(sourceSelector).val(); 
			api.editor[editor.id]['saveSelector']		 	= '#'+controlElementID+' #widget-'+widgetName+'-savewidget'; 
			api.editor[editor.id]['languageSelector']	 	= '#'+controlElementID+' .wpglobus-current-language'; 
			api.editor[editor.id]['inWidgetTitleSelector']	= '#'+controlElementID+' .in-widget-title'; 
			 
			/**
			 * Set init value for tinymce editor via textarea field.
			 */
			/**
			 * @since 2.4.17 
			 */
			var ic = WPGlobusCore.TextFilter( api.editor[editor.id]['content'], WPGlobusCoreData.language , 'RETURN_EMPTY' );
			$('#'+editor.id).val(ic);
			
			/**
			 * Language items.
			 */
			var item = '<a href="#" onclick="return false;" class="item" data-widget-id="{{widgetID}}" data-source="{{source}}" data-editor-id="{{editorID}}" data-language="{{language}}" data-widget-name="{{widgetName}}" style="text-align:center;"><span>{{item}}</span></a>';
			var newItem = '', items = '';
			$.each( WPGlobusCoreData.enabled_languages, function(i,l){
				newItem = item;
				newItem = newItem.replace('{{language}}', 	l);
				newItem = newItem.replace('{{item}}', 		WPGlobusCoreData.en_language_name[l]);
				newItem = newItem.replace('{{widgetName}}', widgetName);
				newItem = newItem.replace('{{widgetID}}',   controlElementID);
				newItem = newItem.replace('{{editorID}}',   editor.id);
				newItem = newItem.replace('{{source}}', 	sourceSelector);
				items += newItem;
			});

			/**
			 * Button for tinymce editor.
			 */
			var button = 
				'<button ' +
					'style="z-index:200000;" ' +
					'type="button" ' +
					'id="'+editor.id+'-wpglobus-button" ' +
					'data-widget-id="'+controlElementID+'"' +
					'class="wp-switch-editor switch-wpglobus-language wpglobus-icon-globe">' +
					'<span class="wpglobus-current-language" data-language="'+WPGlobusCoreData.language+'" style="">'+WPGlobusCoreData.language+'</span>' +
				'</button>' +
				'<div class="wpglobus-language-box" style="position:absolute;top:0;left:0;display:none;z-index:200100;border:1px solid #9e9e9e;border-radius:3px;background-color:#fff;padding: 2px 5px;">' +
					'<div style="display:flex;flex-direction:column;">' + items + '</div>' +
				'</div>';
			
			$('#'+controlElementID+' .wp-editor-tabs').append(button);
			
			/**
			 * Add WPGlobus dialog to title field.
			 */
			if ( WPGlobusDialogApp.addElement({id:controls.fields.title[0].id, style:'width:93%;'}) ) {
				api.editor[editor.id]['titleSelector'] = '#'+controls.fields.title[0].id;
			}

			/**
			 * Set widget title.
			 */
			api.arbitraryWidgetTitle(editor.id);

			/**
			 * Hide unneeded dialog icon.
			 * @since 2.4.17
			 */
			if ( $(sourceSelector).hasClass('hidden') ) {
				var parentElement = $(sourceSelector).parent();
				$(parentElement).find('div.wpglobus-widgets.wpglobus_dialog_start.wpglobus_dialog_icon').css({'display':'none'});
			}				
		},
		arbitraryWidgetTitle: function(editorID) {
			var title = WPGlobusCore.TextFilter( 
				$('#'+api.editor[editorID]['controls']['fields']['title'][0]['id']).val(), 
				WPGlobusCoreData.language 
			); 
			$(api.editor[editorID]['inWidgetTitleSelector']).text(': '+title);					
		},
		setBoxTimeout: function() {
			api.languageBoxTimeout = setTimeout( function(){
				api.languageBoxActive = false;
				$('.wpglobus-language-box').css({'display':'none'});
			}, 1000);
		},
		arbitraryTextOrHTML: function() {
			/**
			 * @see wp-admin\js\widgets\text-widgets.js
			 */
			 
			/**
			 * Open language selector box.
			 */
			$(document).on('click', '.switch-wpglobus-language', function(ev) {
				if ( api.languageBoxActive ) {
					return;
				}
				api.languageBoxActive = true;
				var $t = $(this); 
				var widgetID = $t.data('widget-id');
				var pos = $t.position();
				pos.top += 34;
				pos.left += 10;
				$('#'+widgetID+' .wpglobus-language-box').css({'display':'block','top':pos.top+'px','left':pos.left+'px'});
				api.setBoxTimeout();
			});
			
			/**
			 * Wait then hide language selector box. 
			 */
			$(document).on('mouseenter', '.wpglobus-language-box', function(ev) {
				clearTimeout(api.languageBoxTimeout);
			}).on('mouseleave', '.wpglobus-language-box', function(ev) {
				$('.wpglobus-language-box').css({'display':'none'});
				api.languageBoxActive = false;
			});
			
			/**
			 * Change language code for tinymce.
			 */
			$(document).on('click', '.wpglobus-language-box .item', function(ev) {
				var $t = $(this);
				var l = $t.data('language');
				var widgetID = $t.data('widget-id');
				var editorID = $t.data('editor-id');
				
				$('#'+widgetID+' .wpglobus-current-language').text(l).data('language', l);
				
				var t = WPGlobusCore.TextFilter(
					api.editor[editorID].content, 
					l, 
					'RETURN_EMPTY'
				);
				
				tinymce.get(editorID).setContent(t,{format: 'raw'});
				$('textarea#'+editorID).val(t);				
			});
			
			/**
			 * Hooked editor setup to start our setup.
			 */
			$(document).on('tinymce-editor-setup', function(ev, editor) {
				$.each(wp.textWidgets.widgetControls, function(widgetName,controls){
					api.setupWidgetControl(widgetName, controls, editor);
				});				
			});
			
			/**
			 * Hooked editor init (after editor setup).
			 */			
			$(document).on( 'tinymce-editor-init', function(ev, editor) {
				
				$(document).on('mouseenter', api.editor[editor.id]['saveSelector'], function(ev){
					/**
					 * Sync title with hidden fields.
					 */
					$(api.editor[editor.id]['sourceTitleSelector']).val( $(api.editor[editor.id]['titleSelector']).val() );
					/**
					 * Sync tinymce with hidden fields.
					 */					
					$(api.editor[editor.id]['sourceSelector']).val( api.editor[editor.id]['content'] );
				});
				
				/**
				 * Save widget.
				 */
				$(document).on('click', api.editor[editor.id]['saveSelector'], function(ev){
					api.saveArbitraryTextOrHTML = editor.id;
				});
				
				/**
				 * Hook tinymce editor.
				 */
				editor.on('nodechange keyup', _.debounce( updateEditorContent, 100 ) );
				
				/**
				 * Hook textarea.
				 * don't use 'input' event here.
				 */
				$('#'+editor.id).on('keyup', _.debounce( updateEditorContent, 100 ) );
			} );				
			
			/**
			 * Update editor content.
			 */
			function updateEditorContent(ev) {

				var id = '';
				if ( ev.type == 'keyup' && ev.target.id == 'tinymce' ) {
					id = $(ev.target).data('id');
				} else {
					id = ev.target.id;
				}

				if ( api.editor[id] ) {
					var l = $( api.editor[id]['languageSelector'] ).data('language');
					var newContent = '';
					if (  tinymce.get(id).isHidden() ) {
						newContent = $('#'+id).val();
					} else {
						newContent = tinymce.get(id).getContent({format:'raw'});
					}
					
					api.editor[id]['content'] = WPGlobusCore.getString( 
						api.editor[id]['content'], 
						newContent, 
						l
					);

					/**
					 * Sync with widget content hidden fields.
					 * @see .widget-inside .widget-content 
					 */
					$(api.editor[id]['sourceSelector']).val( api.editor[id]['content'] );
					
				}

			}
			
		},		
		addElements: function(get_by, coid) {
			var id, elem = [], get_by_coid;
			elem[0] = 'input[type="text"]';
			elem[1] = 'textarea';
			if ( typeof get_by === 'undefined' || get_by == 'class' ) {
				get_by_coid = '.widget-liquid-right .widget .widget-content';
				$.each(elem, function(i,e){
					api.makeClone(get_by_coid, e);
				});
			} else if ( get_by == 'id' ) {
				get_by_coid = '#'+coid+' .widget-content';
				$.each(elem, function(i,e){
					api.makeClone(get_by_coid, e);
				});	
			}
		},
		makeClone: function(get_by_coid, type) {
			$(get_by_coid+' '+type).each(function(i,e){
				var element = $(e),
					clone, name, text, id, dis = false;

				id = element.attr('id');
				
				if ( typeof id === 'undefined' || -1 != id.indexOf( '-number') || '' == id ) {
					return true;
				}	
				
				/**
				 * Check for disabled mask.
				 */
				_.each( WPGlobusWidgets.disabledMask, function(mask){ 
					if ( -1 != id.indexOf( mask ) ) {
						dis = true;
						return false;
					}	
				});
				 
				if ( dis )  return true;

				if ( -1 != id.indexOf('-title') ) {
					/**
					 * @since 2.5 Set multilingual field for title only.
					 */ 
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
						if ( api.saveArbitraryTextOrHTML ) {
							var content = WPGlobusCore.TextFilter( 
								api.editor[api.saveArbitraryTextOrHTML]['content'], 
								$( api.editor[api.saveArbitraryTextOrHTML]['languageSelector'] ).data('language'), 
								'RETURN_EMPTY' 
							);
							/**
							 * Set value for tinymce editor.
							 */
							if (  tinymce.get(api.saveArbitraryTextOrHTML).isHidden() ) {
								$('#'+api.saveArbitraryTextOrHTML).val(content);
								$('#' + api.saveArbitraryTextOrHTML + '-tmce').click();
							} else {
								tinymce.get(api.saveArbitraryTextOrHTML).setContent(content, { format:'raw' });
							}
							/**
							 * Set widget title.
							 */
							api.arbitraryWidgetTitle(api.saveArbitraryTextOrHTML); 
							api.saveArbitraryTextOrHTML = false;
							return;
						} 
						var s = settings.data.split('widget-id=');
						s = s[1].split('&');
						$('.widget-liquid-right .widget').each(function(i,e){
							var id = $(e).attr('id');
							if ( -1 !== id.indexOf(s[0]) ) {
								api.addElements('id', id);
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
				api.setImageWidget(this);
			});				
		},
		getImageWidgets: function(title) {
			return api.imageWidgets;
		},
		setImageWidget: function(title) {

			var wID  = $(title).parents('.widget').attr('id');
			if ( 'undefined' === typeof wID ) {
				return;
			}

			if ( -1 == wID.indexOf('media_image') ) {
				return;
			}
			
			// @since 2.6.0
			if ( 'undefined' === typeof api.imageWidgets[wID] ) {
				$('<hr /><div class="">'+WPGlobusWidgets.l10n['imageWidget']['suggest']+'</div>').appendTo('#'+wID+' .widget-inside');
				api.imageWidgets[wID] = true;
			}
		},
		__getEditorContent: function() {
			if ( Object.keys(api.editor).length == 0 ) {
				console.log('WPGlobusWidgets editors length is: 0');
				return;
			}
			console.log('WPGlobusWidgets editors length is: ', Object.keys(api.editor).length );
			
			for(var key in api.editor) {
				console.log('editor        : ', key, ' -> widgetName: ', api.editor[key].widgetName);
				console.log('source content: ', api.editor[key].content);
				$.each(WPGlobusCoreData.enabled_languages, function(i,lang){
					console.log('Language: ', lang);
					var cont = WPGlobusCore.TextFilter(api.editor[key].content, lang, 'RETURN_EMPTY');
					if ( '' == cont ) {
						console.log('empty');
					} else {
						console.log('Content: ', cont);
						
					}
				});
				console.log('--------------------');
			}
		},
		__getEditorLanguage: function() {
			if ( Object.keys(api.editor).length == 0 ) {
				console.log('WPGlobusWidgets editors length is: 0');
				return;
			}
			console.log('WPGlobusWidgets editors length is: ', Object.keys(api.editor).length );
			
			for(var key in api.editor) {
				console.log('editor           : ', key, ' -> widgetName: ', api.editor[key].widgetName);
				console.log('current language : ', $( api.editor[key]['languageSelector'] ).data('language'));
				console.log('--------------------');
			}
		}
	};
	
	WPGlobusWidgets = $.extend({}, WPGlobusWidgets, api);

})(jQuery);