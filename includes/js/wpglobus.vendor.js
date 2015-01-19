/*jslint browser: true*/
/*global jQuery, console, WPGlobusVendor, wpseoMetaboxL10n, yst_updateSnippet */
"use strict";
var wpglobus_wpseo = function () {
	if (typeof wpseoMetaboxL10n === "undefined") {
		return;
	}
	
	function wpglobus_replaceVariables(str, language, callback) {
		if (typeof str === "undefined") {
			return '';
		}
		var post_title = '#title',
			post_excerpt = '#excerpt-' + language,
			post_content = '#content';
		if ( language != WPGlobusAdmin.data.default_language ) {
			post_title = '#title_' + language;
			post_content = '#content-' + language;
		}
		// title
		if (jQuery(title).length) {
			str = str.replace(/%%title%%/g, jQuery(post_title).val());
		}

		// These are added in the head for performance reasons.
		str = str.replace(/%%sitedesc%%/g, wpseoMetaboxL10n.sitedesc);
		str = str.replace(/%%sitename%%/g, wpseoMetaboxL10n.sitename);
		str = str.replace(/%%sep%%/g, wpseoMetaboxL10n.sep);
		str = str.replace(/%%date%%/g, wpseoMetaboxL10n.date);
		str = str.replace(/%%id%%/g, wpseoMetaboxL10n.id);
		str = str.replace(/%%page%%/g, wpseoMetaboxL10n.page);
		str = str.replace(/%%currenttime%%/g, wpseoMetaboxL10n.currenttime);
		str = str.replace(/%%currentdate%%/g, wpseoMetaboxL10n.currentdate);
		str = str.replace(/%%currentday%%/g, wpseoMetaboxL10n.currentday);
		str = str.replace(/%%currentmonth%%/g, wpseoMetaboxL10n.currentmonth);
		str = str.replace(/%%currentyear%%/g, wpseoMetaboxL10n.currentyear);

		str = str.replace(/%%focuskw%%/g, jQuery('#yoast_wpseo_focuskw').val() );
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

		if (focuskw == '')
			return str;

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

			yst_testFocusKw();
		});
	}
	
	var wpglobus_updateSnippet = function(language) {
		//yst_updateURL();
		wpglobus_updateTitle(false,language);
		//yst_updateDesc();
	}
	
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
	// tabs on
    jQuery('#wpglobus-wpseo-tabs').tabs();
	
	var attrs = jQuery('#wpglobus-wpseo-attr');
	var t = jQuery('.wpseotab.general .form-table');
	var ids = attrs.data('ids');
	var names = attrs.data('names');
	
	ids = ids+',' + attrs.data('qtip');
	ids = ids.split(',');
	names = names.split(',');
	
	jQuery('#wpglobus-wpseo-tabs').insertBefore(t);

	jQuery('.wpglobus-wpseo-general').each(function(i,e){
		var l = jQuery(e).data('language');
		jQuery(e).html('<table class="form-table wpglobus-table-'+l+'" data-language="'+l+'">' + t.html() + '</table>');
		jQuery.each(names,function(i,name){
			jQuery('#'+name).attr('name',name+'_'+l);
		});
		jQuery.each(ids,function(i,id){
			if ( 'wpseosnippet' == id ) {
				jQuery('#'+id).addClass('wpglobus-wpseosnippet');
			}
			if ( 'focuskwresults' == id ) {
				jQuery('#'+id).addClass('wpglobus-focuskwresults');
			}
			jQuery('#'+id).attr('id',id+'_'+l);
		});
		wpglobus_updateSnippet(l);
	});
	//t.addClass('hidden');
	wpglobus_qtip();
	yst_updateSnippet();
};
