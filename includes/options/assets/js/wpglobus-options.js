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
			api.firstLanguageCb.on('click', function(ev){
				ev.preventDefault();
				return false;
			});			
		},
		handlerEnabled_languages: function() {
			$('.wpglobus-sortable').sortable({
				placeholder: 'ui-state-highlight',
				update: function(ev, ui){
					api.setFirstLanguageCb();
				}
			});
			$('.wpglobus-sortable').disableSelection();
			api.setFirstLanguageCb();
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
				api.currentTabID = 0;
				curTab = $('#section-tab-'+api.currentTabID);
			}
			curTab.css({'display':'block'});
			$('#wpglobus-tab-link-'+api.currentTabID).addClass('wpglobus-tab-link-active');
		},
		addListeners: function() {
			$(document).on('click', '.wpglobus-tab-link', function(event){
				var tab = $(this).data('tab');
				$('.wpglobus-options-tab').css({'display':'none'});
				$('#section-tab-'+tab).css({'display':'block'});
				
				$('.wpglobus-tab-link').removeClass('wpglobus-tab-link-active');
				$('#wpglobus-tab-link-'+tab).addClass('wpglobus-tab-link-active');
			});			
		}
	};
	
	WPGlobusOptions = $.extend( {}, WPGlobusOptions, api );	
	WPGlobusOptions.init();	
});