/*jslint browser: true*/
/*global jQuery, WPGlobusOptions*/
jQuery(document).ready(function ($) {
    "use strict";
	
	if ( 'undefined' === typeof WPGlobusOptions) {
        return;
    }	
	
	var api = {
		currentTabID: 0,
		init: function() {
			$('.wpglobus-sortable').sortable({
				placeholder: 'ui-state-highlight'
			});
			$('.wpglobus-sortable').disableSelection();
			
			api.initTab();
			api.addListeners();
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