/*jslint browser: true*/
/*global jQuery, console, WPGlobusVendor, wpseoMetaboxL10n, yst_updateSnippet */
// don't use strict mode like original wordpress-seo\js\wp-seo-metabox.js
// "use strict";
var wpglobus_wpseo = function () {
	if (typeof wpseoMetaboxL10n === "undefined") {
		return;
	}
	
	function wpglobus_replaceVariables(str, language, callback) {
		if (typeof str === "undefined") {
			return '';
		}
		if ( typeof replacedVars === 'undefined' && jQuery('#title').size() == 0 ) {
			// WP SEO doesn't work properly when post title is disabled
			return str;	
		}			
		var post_title = '#title',
			post_excerpt = '#excerpt-' + language,
			post_content = '#content';
		if ( language != WPGlobusAdmin.data.default_language ) {
			post_title = '#title_' + language;
			post_content = '#content_' + language;
		}
		// title
		if (jQuery(post_title).length) {
			str = str.replace(/%%title%%/g, jQuery(post_title).val());
		}

		// These are added in the head for performance reasons.
		//str = str.replace(/%%sitedesc%%/g, wpseoMetaboxL10n.sitedesc);
		str = str.replace(/%%sitedesc%%/g, WPGlobusAdmin.data.blogdescription[language]);
		//str = str.replace(/%%sitename%%/g, wpseoMetaboxL10n.sitename);
		str = str.replace(/%%sitename%%/g, WPGlobusAdmin.data.blogname[language]);
		str = str.replace(/%%sep%%/g, wpseoMetaboxL10n.sep);
		str = str.replace(/%%date%%/g, wpseoMetaboxL10n.date);
		str = str.replace(/%%id%%/g, wpseoMetaboxL10n.id);
		str = str.replace(/%%page%%/g, wpseoMetaboxL10n.page);
		str = str.replace(/%%currenttime%%/g, wpseoMetaboxL10n.currenttime);
		str = str.replace(/%%currentdate%%/g, wpseoMetaboxL10n.currentdate);
		str = str.replace(/%%currentday%%/g, wpseoMetaboxL10n.currentday);
		str = str.replace(/%%currentmonth%%/g, wpseoMetaboxL10n.currentmonth);
		str = str.replace(/%%currentyear%%/g, wpseoMetaboxL10n.currentyear);

		str = str.replace(/%%focuskw%%/g, jQuery('#yoast_wpseo_focuskw' + '_' + language).val() );
		// excerpt
		var excerpt = '';
		if (jQuery(post_excerpt).length) {
			excerpt = yst_clean(jQuery(post_excerpt).val());
			str = str.replace(/%%excerpt_only%%/g, excerpt);
		}
		if ('' == excerpt && jQuery(post_content).length) {
			excerpt = jQuery(post_content).val().replace(/(<([^>]+)>)/ig,"").substring(0,wpseoMetaboxL10n.wpseo_meta_desc_length-1);
		}
		str = str.replace(/%%excerpt%%/g, excerpt);

		// parent page
		if (jQuery('#parent_id').length && jQuery('#parent_id option:selected').text() != wpseoMetaboxL10n.no_parent_text ) {
			str = str.replace(/%%parent_title%%/g, jQuery('#parent_id option:selected').text());
		}

		// remove double separators
		var esc_sep = yst_escapeFocusKw(wpseoMetaboxL10n.sep);
		var pattern = new RegExp(esc_sep + ' ' + esc_sep, 'g');
		str = str.replace(pattern, wpseoMetaboxL10n.sep);
		if (str.indexOf('%%') != -1 && str.match(/%%[a-z0-9_-]+%%/i) != null) {
			regex = /%%[a-z0-9_-]+%%/gi;
			matches = str.match(regex);
			for (i = 0; i < matches.length; i++) {
				if (replacedVars[matches[i]] != undefined) {
					str = str.replace(matches[i], replacedVars[matches[i]]);
				} else {
					replaceableVar = matches[i];
					// create the cache already, so we don't do the request twice.
					replacedVars[replaceableVar] = '';
					jQuery.post(ajaxurl, {
								action  : 'wpseo_replace_vars',
								string  : matches[i],
								post_id : jQuery('#post_ID').val(),
								_wpnonce: wpseoMetaboxL10n.wpseo_replace_vars_nonce
							}, function (data) {
								if (data) {
									replacedVars[replaceableVar] = data;
									yst_replaceVariables(str, callback);
								} else {
									yst_replaceVariables(str, callback);
								}
							}
					);
				}
			}
		}
		callback(str);
	}
	
	function wpglobus_boldKeywords(str, url, language) {
		var focuskw = yst_escapeFocusKw(jQuery.trim(jQuery('#' + wpseoMetaboxL10n.field_prefix + 'focuskw' + '_' + language).val()));
		var keywords;

		if (focuskw == '') {
			return str;
		}

		if (focuskw.search(' ') != -1) {
			keywords = focuskw.split(' ');
		} else {
			keywords = new Array(focuskw);
		}
		for (var i = 0; i < keywords.length; i++) {
			var kw = yst_clean(keywords[i]);
			var kwregex = '';
			if (url) {
				kw = kw.replace(' ', '-').toLowerCase();
				kwregex = new RegExp("([-/])(" + kw + ")([-/])?");
			} else {
				kwregex = new RegExp("(^|[ \s\n\r\t\.,'\(\"\+;!?:\-]+)(" + kw + ")($|[ \s\n\r\t\.,'\)\"\+;!?:\-]+)", 'gim');
			}
			if (str != undefined) {
				str = str.replace(kwregex, "$1<strong>$2</strong>$3");
			}
		}
		return str;
	}	
	
	var wpglobus_updateTitle = function(force,language) {
		var title = '';
		var titleElm = jQuery('#' + wpseoMetaboxL10n.field_prefix + 'title' + '_' + language);
		var titleLengthError = jQuery('#' + wpseoMetaboxL10n.field_prefix + 'title-length-warning'+'_'+language);
		var divHtml = jQuery('<div />');
		var snippetTitle = jQuery('#wpseosnippet_title'+'_'+language);

		if (titleElm.val()) {
			title = titleElm.val();
		} else {
			title = wpseoMetaboxL10n.wpseo_title_template;
			title = divHtml.html(title).text();
		}
		if (title == '') {
			snippetTitle.html('');
			titleLengthError.hide();
			return;
		}

		title = yst_clean(title);
		title = jQuery.trim(title);
		title = divHtml.text(title).html();

		if (force) {
			titleElm.val(title);
		}
							//                    !!!!!!
		title = wpglobus_replaceVariables(title, language, function (title) {
			// do the placeholder
			var placeholder_title = divHtml.html(title).text();
			titleElm.attr('placeholder', placeholder_title);

			title = yst_clean(title);

			// and now the snippet preview title
			title = wpglobus_boldKeywords(title, false, language);

			jQuery('#wpseosnippet_title'+'_'+language).html(title);

			var e = document.getElementById('wpseosnippet_title'+'_'+language);
			if (e != null) {
				if (e.scrollWidth > e.clientWidth) {
					titleLengthError.show();
				} else {
					titleLengthError.hide();
				}
			}

			wpglobus_testFocusKw(language);
		});
	};
	
	function wpglobus_updateDesc(language) {
		var desc = jQuery.trim(yst_clean(jQuery('#' + wpseoMetaboxL10n.field_prefix + 'metadesc' + '_' + language).val()));
		var divHtml = jQuery('<div />');
		var snippet = jQuery('#wpseosnippet'+'_'+language);

		if (desc == '' && wpseoMetaboxL10n.wpseo_metadesc_template != '') {
			desc = wpseoMetaboxL10n.wpseo_metadesc_template;
		}
		if (desc != '') {
			desc = yst_replaceVariables(desc, function (desc) {
				desc = divHtml.text(desc).html();
				desc = yst_clean(desc);


				var len;
				len = wpseoMetaboxL10n.wpseo_meta_desc_length - desc.length;

				if (len < 0) {
					len = '<span class="wrong">' + len + '</span>';
				}
				else {
					len = '<span class="good">' + len + '</span>';
				}

				jQuery('#' + wpseoMetaboxL10n.field_prefix + 'metadesc-length' + '_' + language).html(len);

				desc = yst_trimDesc(desc);
				desc = wpglobus_boldKeywords(desc, false);
				// Clear the autogen description.
				snippet.find('.desc span.autogen').html('');
				// Set our new one.
				snippet.find('.desc span.content').html(desc);

				wpglobus_testFocusKw(language);
			});
		} else {
			jQuery('#' + wpseoMetaboxL10n.field_prefix + 'metadesc-length' + '_' + language).html(wpseoMetaboxL10n.wpseo_meta_desc_length);
			// Clear the generated description
			snippet.find('.desc span.content').html('');
			wpglobus_testFocusKw(language);
			
			var post_content = '#content';
			if ( language != WPGlobusAdmin.data.default_language ) {
				post_content = '#content_' + language;
			}
			if (jQuery(post_content).length) {
				desc = jQuery(post_content).val();
				desc = yst_clean(desc);
			}

			var focuskw = yst_escapeFocusKw(jQuery.trim(jQuery('#' + wpseoMetaboxL10n.field_prefix + 'focuskw' + '_' + language).val()));
			if (focuskw != '') {
				var descsearch = new RegExp(focuskw, 'gim');
				if (desc.search(descsearch) != -1 && desc.length > wpseoMetaboxL10n.wpseo_meta_desc_length) {
					desc = desc.substr(desc.search(descsearch), wpseoMetaboxL10n.wpseo_meta_desc_length);
				} else {
					desc = desc.substr(0, wpseoMetaboxL10n.wpseo_meta_desc_length);
				}
			} else {
				desc = desc.substr(0, wpseoMetaboxL10n.wpseo_meta_desc_length);
			}
			
			desc = wpglobus_boldKeywords(desc, false);
			desc = yst_trimDesc(desc);
			snippet.find('.desc span.autogen').html(desc);
		}
	}	
	
	function wpglobus_testFocusKw(language) {
		// Retrieve focus keyword and trim
		var focuskw = jQuery.trim(jQuery('#' + wpseoMetaboxL10n.field_prefix + 'focuskw' + '_' + language).val());
		focuskw = yst_escapeFocusKw(focuskw).toLowerCase();

		var post_title = '#title';
		var post_content = '#content';
		if ( language != WPGlobusAdmin.data.default_language ) {
			post_title = '#title_' + language;
			post_content = '#content_' + language;
		}

		var postnamefull = jQuery(document).triggerHandler('wpglobus_post_name_full', {postnamefull:'#editable-post-name-full',language:language}) || '#editable-post-name-full'; 
		if (jQuery(postnamefull).length) {
			var postname = jQuery(postnamefull).text();
			var url = wpseoMetaboxL10n.wpseo_permalink_template.replace('%postname%', postname).replace('http://', '');
		}
		var p = new RegExp("(^|[ \s\n\r\t\.,'\(\"\+;!?:\-])" + focuskw + "($|[ \s\n\r\t.,'\)\"\+!?:;\-])", 'gim');
		//remove diacritics of a lower cased focuskw for url matching in foreign lang
		var focuskwNoDiacritics = removeLowerCaseDiacritics(focuskw);
		var p2 = new RegExp(focuskwNoDiacritics.replace(/\s+/g, "[-_\\\//]"), 'gim');

		var focuskwresults = jQuery('#focuskwresults'+'_'+language);
		var metadesc = jQuery('#wpseosnippet'+'_'+language).find('.desc span.content').text();

		if (focuskw != '') {
			var html = '<p>' + wpseoMetaboxL10n.keyword_header + '</p>';
			html += '<ul>';
			if (jQuery(post_title).length) {
				html += '<li>' + wpseoMetaboxL10n.article_header_text + ptest(jQuery(post_title).val(), p) + '</li>';
			}
			html += '<li>' + wpseoMetaboxL10n.page_title_text + ptest(jQuery('#wpseosnippet_title'+'_'+language).text(), p) + '</li>';
			html += '<li>' + wpseoMetaboxL10n.page_url_text + ptest(url, p2) + '</li>';
			if (jQuery(post_content).length) {
				html += '<li>' + wpseoMetaboxL10n.content_text + ptest(jQuery(post_content).val(), p) + '</li>';
			}
			html += '<li>' + wpseoMetaboxL10n.meta_description_text + ptest(metadesc, p) + '</li>';
			html += '</ul>';
			focuskwresults.html(html);
		} else {
			focuskwresults.html('');
		}
	}
	
	var wpglobus_updateSnippet = function(language) {
		//yst_updateURL();
		wpglobus_updateTitle(false,language);
		wpglobus_updateDesc(language);
	};
	
	var wpglobus_qtip = function() {
		jQuery(".yoast_help").qtip(
			{
				content: {
					attr: 'alt'
				},
				position: {
					my: 'bottom left',
					at: 'top center'
				},
				style   : {
					tip: {
						corner: true
					},
					classes : 'yoast-qtip qtip-rounded qtip-blue'
				},
				show    : {
					when: {
						event: 'mouseover'
					}
				},
				hide    : {
					fixed: true,
					when : {
						event: 'mouseout'
					}
				}
			}
		);
	};
	
	// start 
	
	// tabs on
    jQuery('#wpglobus-wpseo-tabs').tabs();
	
	var attrs = jQuery('#wpglobus-wpseo-attr');
	var t = jQuery('.wpseotab.general .form-table');
	var ids = attrs.data('ids');
	var names = attrs.data('names');
	var wpseosnippet_url = '';
	
	ids = ids+',' + attrs.data('qtip');
	ids = ids.split(',');
	names = names.split(',');
	
	jQuery('#wpglobus-wpseo-tabs').insertBefore(t);

	jQuery('.wpglobus-wpseo-general').each(function(i,e){
		var $e = jQuery(e);
		var l = $e.data('language');
		$e.html('<table class="form-table wpglobus-table-'+l+'" data-language="'+l+'">' + t.html() + '</table>');
		jQuery.each(names,function(i,name){
			jQuery('#'+name).attr('name',name+'_'+l);
		});
		jQuery.each(ids,function(i,id){
			var $id = jQuery('#'+id);
			if ( 'wpseosnippet' == id ) {
				$id.addClass('wpglobus-wpseosnippet');
			}
			if ( 'focuskwresults' == id ) {
				$id.addClass('wpglobus-focuskwresults');
			}
			if ( wpseoMetaboxL10n.field_prefix + 'metadesc' == id ) {
				$id.addClass('wpglobus-wpseo_metadesc').text($e.data('metadesc'));
			}
			if ( wpseoMetaboxL10n.field_prefix + 'title' == id ) {
				$id.addClass('wpglobus-wpseo_title').val($e.data('wpseotitle'));
			}
			if ( wpseoMetaboxL10n.field_prefix + 'focuskw' == id ) {
				$id.addClass('wpglobus-wpseo_focuskw').val($e.data('focuskw'));
			}			
			$id.attr('id',id+'_'+l);
			jQuery('#'+id+'_'+l).attr('data-language',l);
		});
		if ( 'complete' == $e.data('permalink') ) {
			wpseosnippet_url = $e.data('url-'+l);
		} else {
			wpseosnippet_url = $e.data('url-'+l)+jQuery('#editable-post-name-full').text()+'/';
		}	
		jQuery('#wpseosnippet_'+l+' .url').text(wpseosnippet_url);
		wpglobus_updateSnippet(l);
		
		if ( typeof jQuery().autocomplete != 'undefined' ) {
			//
			var cache = {}, lastXhr;
			jQuery('#' + wpseoMetaboxL10n.field_prefix + 'focuskw' + '_' + l).autocomplete({
				minLength   : 3,
				formatResult: function (row) {
					return jQuery('<div/>').html(row).html();
				},
				source      : function (request, response) {
					var term = request.term;
					if (term in cache) {
						response(cache[term]);
						return;
					}
					request._ajax_nonce = wpseoMetaboxL10n.wpseo_keyword_suggest_nonce;
					request.action = 'wpseo_get_suggest';

					lastXhr = jQuery.getJSON(ajaxurl, request, function (data, status, xhr) {
						cache[term] = data;
						if (xhr === lastXhr) {
							response(data);
						}
					});
				}
			});
		}	
		
		jQuery('#' + wpseoMetaboxL10n.field_prefix + 'title' + '_' + l).keyup(function () {
			wpglobus_updateTitle(false, jQuery(this).data('language'));
		});
		jQuery('#title'+'_'+l).keyup(function () {
			var l = jQuery(this).data('language') ? jQuery(this).data('language') : WPGlobusAdmin.data.default_language;
			wpglobus_updateTitle(false, l);
			wpglobus_updateDesc(l);
		});
		if ( i == 0 ) {
			jQuery('#title').keyup(function () {
				wpglobus_updateTitle(false, WPGlobusAdmin.data.default_language);
				wpglobus_updateDesc(WPGlobusAdmin.data.default_language);
			});		
		}
		if ( i == 0 ) {
			jQuery('body').on('change', '#parent_id', function () {
				jQuery.each(WPGlobusAdmin.data.enabled_languages, function(i,l){
					wpglobus_updateTitle(false, l);
					wpglobus_updateDesc(l);
				});
			});		
		}
		jQuery('#' + wpseoMetaboxL10n.field_prefix + 'metadesc' + '_' + l).keyup(function () {
			wpglobus_updateDesc(jQuery(this).data('language'));
		});
		jQuery('body').on('keyup', '#excerpt-'+l, function () {
			wpglobus_updateDesc(jQuery(this).data('language') ? jQuery(this).data('language') : WPGlobusAdmin.data.default_language);
		});
		if ( i == 0 ) {
			// #content,#content_{lang_code}
			jQuery('.wpglobus-editor').focusout(function () {
				wpglobus_updateDesc(jQuery(this).data('language'));
			});		
		}
		var focuskwhelptriggered = false;
		jQuery(document).on('change', '#' + wpseoMetaboxL10n.field_prefix + 'focuskw' + '_' + l, function () {
			var l = jQuery(this).data('language');
			var focuskwhelpElm = jQuery('#focuskwhelp'+'_'+l);
			if (jQuery('#' + wpseoMetaboxL10n.field_prefix + 'focuskw' + '_' + l).val().search(',') != -1) {
				focuskwhelpElm.click();
				focuskwhelptriggered = true;
			} else if (focuskwhelptriggered) {
				focuskwhelpElm.qtip("hide");
				focuskwhelptriggered = false;
			}
			wpglobus_updateSnippet( l );
		});		
		
	}); // end each .wpglobus-wpseo-general
	t.addClass('hidden');
	
	// description
	jQuery( 'body' ).on('change', '.wpglobus-wpseo_metadesc', function(event){
		var save_to = '#' + wpseoMetaboxL10n.field_prefix + 'metadesc',
			$t = jQuery(this);

		jQuery(save_to).val( WPGlobusCore.getString( jQuery(save_to).val(), $t.val(), $t.data('language')) );		
	});
	
	// title
	jQuery('body').on('change', '.wpglobus-wpseo_title', function(event){
		var save_to = '#' + wpseoMetaboxL10n.field_prefix + 'title',
			$t = jQuery(this);

		jQuery(save_to).val( WPGlobusCore.getString( jQuery(save_to).val(), $t.val(), $t.data('language')) );		
		
	});	
	
	// keywords
	jQuery('body').on('change', '.wpglobus-wpseo_focuskw', function(event){
		var save_to = '#' + wpseoMetaboxL10n.field_prefix + 'focuskw',
			$t = jQuery(this);

		jQuery(save_to).val( WPGlobusCore.getString( jQuery(save_to).val(), $t.val(), $t.data('language')) ); 
	});
	
	jQuery('body').on('click', '.wpglobus-post-body-tabs-list li', function(event){
		$this = jQuery(this);
		if ( $this.hasClass('wpglobus-post-tab') ) {
			jQuery('#wpglobus-wpseo-tabs').tabs('option','active',jQuery(this).data('order'));
		}	
	});	
	
	wpglobus_qtip();
	yst_updateSnippet();
		/*
							jQuery('body').on('keyup', '.wpglobus-excerpt', function () {
			var l = jQuery(this).data('language') ? jQuery(this).data('language') : WPGlobusAdmin.data.default_language;
			console.log(l);
			//wpglobus_updateDesc(l);
		});	 */
	
};
