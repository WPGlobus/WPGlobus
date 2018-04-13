/*jslint browser: true*/
/*global jQuery, WPGlobusOptions*/
jQuery(document).ready(function ($) {
    "use strict";
	
	if ( 'undefined' === typeof WPGlobusOptions) {
        return;
    }	
	
	var api = {
		currentTabID: 0,
		firstLanguageCb: null,
		init: function() {
			api.initTab();
			api.checkHandlers();
			api.addListeners();
			api.initSpecs();
		},
		setFirstLanguageCb: function() {
			if ( null !== api.firstLanguageCb ) {
				api.firstLanguageCb.off('click');
			}
			$('#enabled_languages-list li input[type="checkbox"]').prop('disabled', false);
			var $elm = $('#enabled_languages-list li').eq(0);
			api.firstLanguageCb = $elm.find('input[type="checkbox"]');
			api.firstLanguageCb.prop('checked','checked');
			api.firstLanguageCb.prop('disabled','disabled');
			api.firstLanguageCb.css({'visibility':'hidden'});
			api.firstLanguageCb.on('click', function(ev){
				ev.preventDefault();
				return false;
			});			
		},
		handlerEnabled_languages: function() {
			$('.wpglobus-sortable').sortable({
				placeholder: 'ui-state-highlight',
				update: function(ev, ui){
					$('#enabled_languages-list li input[type="checkbox"]').css({'visibility':'visible'});
					api.setFirstLanguageCb();
				}
			});
			$('.wpglobus-sortable').disableSelection();
			api.setFirstLanguageCb();
		},
		handlerLanguagesTable: function() {
			var tab = $('#wpglobus-options-languagesTable').parents('.wpglobus-options-tab').data('tab');
			$('#wpglobus-options-languagesTable .manage-column.sortable a').each(function(i,e){
				var href = $(e).attr('href');
				if ( -1 != href.indexOf('tab') ) {
					if ( -1 == href.indexOf('tab-from') ) {
						href = href.replace(/tab/, 'tab-from');
						href += '&tab='+tab;
					}
				} else {
					href += '&tab='+tab;
				}
				$(e).attr('href', href)
			});
		},
		checkHandlers: function() {
			$('.wpglobus-options-field').each(function(i,e){
				if ( 'undefined' === typeof $(e).data('js-handler') ) {
					return true;
				}
				var func = $(e).data('js-handler');
				if ( 'function' === typeof api[func] ) {
					api[func]();
				}
			});
		},
		initTab: function() {
			var curTab = $('#section-tab-'+WPGlobusOptions.tab);
			api.currentTabID = WPGlobusOptions.tab;
			if ( 0 == curTab.length ) {
				api.currentTabID = WPGlobusOptions.defaultTab;
				curTab = $('#section-tab-'+api.currentTabID);
			}
			curTab.css({'display':'block'});
			$('#wpglobus-tab-link-'+api.currentTabID).addClass('wpglobus-tab-link-active');
		},
		addListeners: function() {
			$(document).on('click', 'input.wpglobus-enabled_languages', function(event){
				var checked = $(this).prop('checked');
				var id = $(this).attr('rel');
				
				if ( checked ) {
					$('#'+id).val('1');
				} else {
					$('#'+id).val('');
				}
				
			});
			$(document).on('click', '.wpglobus-tab-link', function(event){
				var tab = $(this).data('tab');
				window.history.pushState("data", "Title", WPGlobusOptions.newUrl.replace('{*}', tab));
				$('.wpglobus-options-tab').css({'display':'none'});
				$('#section-tab-'+tab).css({'display':'block'});
				
				$('.wpglobus-tab-link').removeClass('wpglobus-tab-link-active');
				$('#wpglobus-tab-link-'+tab).addClass('wpglobus-tab-link-active');
				$('#wpglobus_options_current_tab').val(tab);
			});			
		},
		initSpecs: function() {
			$(document).on('dblclick', '#section-tab-debug-info h2', function(ev){
				$('.wpglobus-debug-info-spec').removeClass('hidden');
			});
		}
	};
	
	WPGlobusOptions = $.extend( {}, WPGlobusOptions, api );	
	WPGlobusOptions.init();	
});