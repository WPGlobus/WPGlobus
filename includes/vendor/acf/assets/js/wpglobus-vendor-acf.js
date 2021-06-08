/**
 * WPGlobus Vendor Acf
 * Interface JS functions
 *
 * @since 2.6.6
 *
 * @package WPGlobus
 * @subpackage Administration/EditFieldGroup
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusVendorAcf*/
jQuery(document).ready(function($) {
	"use strict";
	
	if ( 'undefined' === typeof WPGlobusVendorAcf ) {
		return;
	}

	var api =  {
		acfFieldObjects: {},
		elements: {},
		parseBool: function(b)  {
			return !(/^(false|0)$/i).test(b) && !!b;
		},		
		getAcfFieldObjects: function() {
			return api.acfFieldObjects;
		},
		getFields: function(status) {
			var _fields = api.get('fields');
			if ( 'undefined' === typeof status ) {
				return _fields;
			}
			if ( 'undefined' === _fields[status] ) {
				return undefined;
			}
			return _fields[status];
		},		
		get: function(key) {
			if ( 'undefined' === typeof WPGlobusVendorAcf.data[key] ) {
				return undefined;
			}
			return WPGlobusVendorAcf.data[key];
		},
		isAcfPro: function() {
			if ( api.parseBool( api.get('pro') ) ) {
				return true;
			}
			return false;
		},
		getName: function(type) {
			
			if ( '' == type || 'indefined' == typeof(type) ) {
				return '';
			}
			
			var statuses, names, statusKey, statusValue;
			var fields = api.get('fields');
			
			$.each(fields, function(_statusValue, types){
				if ( ! types ) {
					return true;
				}
				if ( -1 !== types.indexOf(type) ) {
					statusValue = _statusValue;
					return false;
				}
			});
			
			statuses = api.get('fieldStatuses');
			$.each(statuses, function(_statusKey, _statusValue){
				if ( statusValue == _statusValue ) {
					statusKey = _statusKey;
					return false;
				}
			});			
			
			names = api.get('names');
			return names[statusKey];
		},
		getStatus: function(statusKey) {
			return api.get('fieldStatuses')[statusKey];
		},
		runDelayed: function() {
			setTimeout(function(){
				api.setFieldObjects();
				// api.attachListeners();
			}, 500);
		},
		setFieldObjects: function() {

			if ( $('.acf-field-list').length == 0 ) {
				return;
			}
			
			var fieldObjects =  $('.acf-field-list .acf-field-object');
			
			$.each( fieldObjects, function(indx, object){

				var $obj = $(object);
				var type = $obj.data('type');
				var name = api.getName(type);
				
				// Don't use `name` for `input` element. Maybe more than one element.
				var selector = 'input#acf_fields-'+$obj.data('id')+'-'+name;
				var $_element = $(selector);

				if ( $_element.length == 1 ) {
					api.acfFieldObjects[selector] = {};
					api.acfFieldObjects[selector]['innerElement']  = $_element;
					api.acfFieldObjects[selector]['type']   = type;
					api.acfFieldObjects[selector]['id']   	= $obj.data('id');
					api.acfFieldObjects[selector]['key']   	= $obj.data('key');
					api.acfFieldObjects[selector]['name']   = name;
					api.acfFieldObjects[selector]['status'] = null;
					api.acfFieldObjects[selector]['object'] = null;
					api.acfFieldObjects[selector]['mode']   = null;
					
					if ( -1 !== name.indexOf( api.getStatus('PRETENDER') ) ) {
						$obj.addClass( api.get('classes')['PRETENDER']['statusClass'] );
						api.acfFieldObjects[selector]['object'] = $obj;
						api.acfFieldObjects[selector]['status'] = api.getStatus('PRETENDER');
						api.acfFieldObjects[selector]['mode'] 	= 'on';
						api.setStyles(selector);
					} else {					
						if ( -1 !== name.indexOf( api.getStatus('MULTILINGUAL') ) ) {
							$obj.addClass( api.get('classes')['MULTILINGUAL']['statusClass'] );
							api.acfFieldObjects[selector]['mode'] = 'off';
							if ( $_element.prop('checked') ) {
								$obj.addClass( api.get('classes')['MULTILINGUAL']['translatableClass'] );
								api.acfFieldObjects[selector]['mode'] = 'on';
							}
							api.acfFieldObjects[selector]['object'] = $obj;
							api.acfFieldObjects[selector]['status'] = api.getStatus('MULTILINGUAL');
							api.setStyles(selector);
						}
					}

				}
			});
			api.fieldObjectsHandler()
		},
		setStyles: function(selector) {
			if ( api.getStatus('PRETENDER') == api.acfFieldObjects[selector]['status'] ) {
				$('.'+api.get('classes')['PRETENDER']['statusClass']).css({'border-left':'3px solid #6e6e6e'});
			}
			if ( api.getStatus('MULTILINGUAL') == api.acfFieldObjects[selector]['status'] ) {
				if ( 'off' == api.acfFieldObjects[selector]['mode'] ) {
					$('.'+api.get('classes')['MULTILINGUAL']['statusClass']).css({'border-left':'3px solid #ffb900'});
				}
			}
		},
		setMode: function(mode, selector) {
			if ( 'undefined' === typeof mode ) {
				return;
			}
			if ( 'on' === mode ) {
				api.acfFieldObjects[selector]['object'].addClass( api.get('translatableClass') );
				api.acfFieldObjects[selector]['mode'] = mode;
			} else if ( 'off' === mode ) {
				api.acfFieldObjects[selector]['object'].removeClass( api.get('translatableClass') );
				api.acfFieldObjects[selector]['mode'] = mode;
			}
		},
		fieldObjectsHandler: function() {
			$.each( api.getAcfFieldObjects(), function(selector, object){
				if ( object.status == api.getStatus('PRETENDER') ) {
					object['innerElement'].attr('disabled', 'disabled');
					var pDiv = object['innerElement'].parents('div.acf-true-false');
					if ( 'undefined' !== typeof api.get('l10n')['wysiwyg-pretender-tip'] ) {
						$('<span class="wpglobus-vendor-acf-tip">'+api.get('l10n')['wysiwyg-pretender-tip']+'</span>').insertAfter(pDiv);
					}
				} else {
					$(document).on('click', selector, function(event){
						if ( api.acfFieldObjects[selector]['innerElement'].prop('checked') ) {
							api.setMode('on', selector);
						} else {
							api.setMode('off', selector);
						}
						api.setStyles(selector);
					});
				}
			});
		},
		start: function() {
			api.runDelayed();
		}
	}
	
	WPGlobusVendorAcf = $.extend({}, WPGlobusVendorAcf, api);
	WPGlobusVendorAcf.start();
});	