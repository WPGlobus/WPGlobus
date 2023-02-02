/**
 * WPGlobus Edit Post Sidebar Administration.
 * Interface JS functions
 *
 * @since 2.10.8
 *
 * @package WPGlobus
 * @subpackage Admin/Recommendations
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusCore, wp, WPGlobusEditPostSidebar*/
jQuery(document).ready(function ($) {
  "use strict";

	if ( 
		'undefined' === typeof wp.editPost ||
		'undefined' === typeof wp.element  ||
		'undefined' === typeof wp.plugins )
	{
		return;
	}

	if ( 'undefined' === typeof WPGlobusEditPostSidebar ) {
		return;
	}

	const Br = function() {
		return wp.element.createElement(
			'br',{}
		)
	}	

	const Text = function(props) {
		let {
			tagName = 'div',
			className = '',
			style = '',
			children,
		} = props;

		let classes = 'components-text';
		if ( className !== '' ) {
			classes += ' ' + className;
		}
		
		return wp.element.createElement(
			tagName, 
			{
			  className: classes,
				style: style
			},
			children
		)
	}	

	var api = {
		parseBool: function(b){return !(/^(false|0)$/i).test(b) && !!b;},
		hasContent: function() {
			if ( '' === WPGlobusEditPostSidebar.content ) {
				return false;
			}
			return true; 			
		},
		getContent: function() {
			if ( '' ===  WPGlobusEditPostSidebar.content ) {
				return false;
			}
			return WPGlobusEditPostSidebar.content; 
		},
		start: function() {
			api.WPGlobusEditPostPlugin();
			if ( api.parseBool( WPGlobusEditPostSidebar.hideStandardMetabox ) ) {
				// We need to leave the language field on the page, so just hide metabox.
				$('#wpglobus').addClass('hidden');
			}
		},
		WPGlobusEditPostPlugin: function(){
			if ( ! api.hasContent() ) {
				return false;
			}
			var isLink = function(item) {
				if ( typeof item.linkUrl === 'string' && typeof item.linkContent === 'string' ) {
					return true;
				}
				return false;
			}
			var registerPlugin = wp.plugins.registerPlugin;
			var Link = wp.components.ExternalLink;
			var el = wp.element.createElement;
			var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	
			const getContent = () => {
				var _content = [];
				var _key = 0;
				Object.entries(api.getContent()).forEach(([itemKey, item]) => {
					var message = typeof item.message === 'undefined' ? null : item.message;
					var children = null;
					if ( isLink(item) ) {
						children = el(
							Link,
							{href:item.linkUrl,children:item.linkContent}
						);
					}
					_content.push(
						el(
							Text,
							{
								tagName: 'p',
								className: 'wpglobus-recommendation',
								style: {fontWeight:'600'},
								key: _key,
							},
							message,
							children			
						)
					)
					_key++;
				});
				return _content;
			}
	
			var Component = function() {
				return el(
					PluginDocumentSettingPanel,
					{ 
						name: 'wpglobus-sidebar-panel',
						initialOpen: false,
						icon: 'dashicons dashicons-admin-site',
						title: 'WPGlobus',
						className: 'wpglobus-sidebar-panel',
					},
					getContent()
				)
			}
			registerPlugin( 'wpglobus-edit-post-sidebar', {
				icon: '',
				render: Component,
			} );
			return true;
		}
	}
	
	WPGlobusEditPostSidebar = $.extend({}, WPGlobusEditPostSidebar, api);
	WPGlobusEditPostSidebar.start();		
});