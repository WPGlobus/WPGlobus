/*jslint browser: true*/
/*global jQuery, WPGlobusOptions*/
jQuery(document).ready(function ($) {
    "use strict";
	
	if ( 'undefined' === typeof WPGlobusOptions) {
        return;
    }	
	
	var api = {
		currentTabID: '',
		firstLanguageCb: null,
		init: function() {
			api.initTab();
			api.checkHandlers();
			api.addListeners();
			api.initSpecs();
		},
		setCurrentTabID: function(tabID) {
			api.currentTabID = tabID;
		},
		getCurrentTabID: function() {
			return api.currentTabID;
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
			api.setCurrentTabID( WPGlobusOptions.tab );
			if ( 0 == curTab.length ) {
				api.setCurrentTabID( WPGlobusOptions.defaultTab );
				curTab = $('#section-tab-'+api.currentTabID);
			}
			curTab.css({'display':'block'});
			$('#wpglobus-tab-link-'+api.currentTabID).addClass('wpglobus-tab-link-active');
			
			api.initLanguageTable();
			
			/** 
			 * Fix no JS elements.
			 * @since 2.5.3
			 */
			$('.wpglobus-options-wrap .no-inline-js').attr('onclick', 'return false;');
			
			/**
			 * Display subsection if exists.
			 * @since 2.5.3
			 */
			setTimeout(function(){
				$('#section-tab-'+WPGlobusOptions.tab+' .wpglobus-options-menu .wpglobus-tab-link-subsection').eq(0).click();
			}, 200);
			
			/**
			 * @since 2.6.1
			 */
			api.tabActions( api.getCurrentTabID() );
		},
		initLanguageTable: function() {
			var $items = $('.wpglobus-the-language-item-installed');
			if ( $items.length > 9 ) {
				return;
			}
			var rows = [];
			$items.each(function(indx){
				var order = $(this).data('order') * 1;
				rows[order] = $(this);
			});
			var ib = $('#wpglobus-options-languagesTable #the-list tr').eq(0)
			$.each(rows, function(index, row) {
				$(ib).before(row);
			});
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
				api.setCurrentTabID(tab);
				window.history.pushState("data", "Title", WPGlobusOptions.newUrl.replace('{*}', tab));
				$('.wpglobus-options-tab').css({'display':'none'});
				$('#section-tab-'+tab).css({'display':'block'});
				
				$('.wpglobus-tab-link').removeClass('wpglobus-tab-link-active');
				$('#wpglobus-tab-link-'+tab).addClass('wpglobus-tab-link-active');
				$('#wpglobus_options_current_tab').val(tab);
				
				/**
				 * Display/Set active subsection.
				 * @since 2.5.3
				 */
				if ( $('#section-tab-'+tab+' .wpglobus-tab-link-subsection.wpglobus-tab-link-active').length == 1 ) {
					$('#section-tab-'+tab+' .wpglobus-tab-link-subsection.wpglobus-tab-link-active').click();
				} else {
					if ( $('#section-tab-'+tab+' .wpglobus-tab-link-subsection').length > 0 ) {
						$('#section-tab-'+tab+' .wpglobus-tab-link-subsection').eq(0).click();
					}
				}
				
				// @since 2.6.1
				api.tabActions( $(this) );
			});
			
			/* @since 2.5.3 */
			$(document).on('click', '.wpglobus-tab-link-subsection', function(event){
				var tab = $(this).data('tab');
				$('.wpglobus-options-tab-subsection').css({'display':'none'});
				$('#subsection-tab-'+tab).css({'display':'block'});
				
				$('.wpglobus-tab-link-subsection').removeClass('wpglobus-tab-link-active');
				$('#wpglobus-tab-link-subsection-'+tab).addClass('wpglobus-tab-link-active');
			});				
		},
		tabActions: function($tab) {
			/**
			 * @since 2.6.1
			 */		
			if ( 'string' === typeof $tab ) {
				$tab = $('#wpglobus-tab-link-'+$tab).eq(0);
			}
			
			if ( $tab.length == 0 ) {
				return;
			}

			/**
			 * Hide/Show submit button.
			 * @since 2.6.1
			 */
			if ( $tab.hasClass('wpglobus-tab-hide-submit-button') ) {
				$('#form-wpglobus-options .submit').addClass('hidden');
			} else {
				$('#form-wpglobus-options .submit').removeClass('hidden');
			}
		},
		initSpecs: function() {
			$(document).on('dblclick', '#section-tab-customizer h2', function(ev){
				$('.wpglobus-theme-info-spec').removeClass('hidden');
			});
			$(document).on('dblclick', '.column-wpglobus_flag', function(ev){
				location = location.href + '&flags';
			});
		}
	};
	
	WPGlobusOptions = $.extend( {}, WPGlobusOptions, api );	
	WPGlobusOptions.init();	
});