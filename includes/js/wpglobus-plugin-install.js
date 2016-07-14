/**
 * WPGlobus Plugin Install
 * Interface JS functions
 *
 * @since 1.5.9
 *
 * @package WPGlobus
 * @subpackage Administration
 */
/*jslint browser: true*/
/*global jQuery, console*/
jQuery(document).ready(function($) {
	"use strict";
	
	if ( typeof WPGlobusPluginInstall === 'undefined' ) {
		return;
	}	
	
	var api =  {
		pluginInstalled: '<li><span class="button button-disabled">Installed</span></li>',
		columnName: '<a href="{{href}}" class=""  target="_blank">{{name}}<img src="{{img}}" class="plugin-icon"></a>',
		currentVersion: '<strong>' + WPGlobusPluginInstall.i18n.current_version + ': </strong>{{version}}',
		init: function() {
			
			$.each( WPGlobusPluginInstall.pluginCard, function(i, card) {
				var ccard = '.plugin-card-' + card; 
				$( ccard + ' .column-rating' ).css({'visibility':'hidden'});
				$( ccard + ' .column-downloaded' ).css({'visibility':'hidden'});
				
				var actions = $( ccard + ' .plugin-action-buttons li' );
				if ( actions.length == 1 ) {
					/** add Installed button if it was lost */
					if ( WPGlobusPluginInstall.pluginData[ card ].plugin_data !== null ) {
						$( ccard + ' .plugin-action-buttons' ).prepend( api.pluginInstalled );
					}	
				}
				
				$( ccard + ' .plugin-action-buttons .install-now' )
					.attr( 'href', WPGlobusPluginInstall.pluginData[ card ].extra_data.product_url )
					.attr( 'target', '_blank' );
				
				$( ccard + ' .open-plugin-details-modal' ).css({'display':'none'});
				
				var name = api.columnName.replace( '{{href}}', WPGlobusPluginInstall.pluginData[ card ].extra_data.details_url );
				name = name.replace( '{{name}}', WPGlobusPluginInstall.pluginData[ card ].card.name );
				name = name.replace( '{{img}}',  WPGlobusPluginInstall.pluginData[ card ].card.icons['1x'] );
				$( ccard + ' .column-name h3' ).html( name );
				
				if ( WPGlobusPluginInstall.pluginData[ card ].plugin_data === null ) {
					$( ccard + ' .column-updated' ).css({'visibility':'hidden'});
				} else {
					$( ccard + ' .column-updated' ).html( api.currentVersion.replace( '{{version}}', WPGlobusPluginInstall.pluginData[ card ].plugin_data.Version ) );
				}	

				$( ccard ).prepend( '<div class="plugin-card-header" style="text-align:center;height:40px;background-color:#00a0d2;padding-top:15px;"><h3 style="color:#fff;">Premium add-on</h3></div>' );
				
			});
			
			$( '.plugin-action-buttons .install-now' ).css({'background-color':'#0f0'}).text( WPGlobusPluginInstall.i18n.get_it );
		
		},
	};

	WPGlobusPluginInstall = $.extend({}, WPGlobusPluginInstall, api);
	WPGlobusPluginInstall.init();	
	
});