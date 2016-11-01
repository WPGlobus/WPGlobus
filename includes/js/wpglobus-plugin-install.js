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
		pluginInstalled: '<li><span class="button button-disabled">' + WPGlobusPluginInstall.i18n.installed + '</span></li>',
		columnName: '<a href="{{href}}" class=""  target="_blank">{{name}}<img src="{{img}}" class="plugin-icon"></a>',
		currentVersion: '<strong>' + WPGlobusPluginInstall.i18n.current_version + ': </strong>{{version}}',
		init: function() {

			$.each( WPGlobusPluginInstall.pluginCard.paid, function(i, card) {
				var ccard = '.plugin-card-' + card;
				$( ccard + ' .column-rating' ).css({'visibility':'hidden'});
				$( ccard + ' .column-downloaded' ).css({'visibility':'hidden'});

                /**
                 * Hide the version and the entire row
                 * because we do not have the live data anymore.
                 * @since 1.6.7
                 */
                $(ccard + ' .column-rating').css({'display': 'none'});
                $(ccard + ' .column-updated').css({'display': 'none'});


                var actions = $( ccard + ' .plugin-action-buttons li' );
				if ( actions.length == 1 ) {
					/** add Installed button if it was lost */
					if ( WPGlobusPluginInstall.pluginData[ card ].plugin_data !== null ) {
						$( ccard + ' .plugin-action-buttons' ).prepend( api.pluginInstalled );
					}
				}

				$( ccard + ' .plugin-action-buttons .button' ).each( function(i,e){
					/**
					 * Remove class 'install-now' to prevent action of standard install
					 * @since 1.6.3
					 * @see wp-admin\js\updates.js
					 */
					if ( $(e).hasClass( 'install-now' ) ) {
						$(e).removeClass( 'install-now' ).addClass( 'wpglobus-install-now' );
					}
				});

				$( ccard + ' .plugin-action-buttons .wpglobus-install-now' )
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

				$( ccard ).prepend( '<div class="plugin-card-header" style="text-align:center;height:40px;background-color:#00a0d2;padding-top:15px;"><h3 style="color:#fff;">' + WPGlobusPluginInstall.i18n.card_header + '</h3></div>' );

			});

			$( '.plugin-action-buttons .wpglobus-install-now' ).css({'background-color':'#0f0'}).text( WPGlobusPluginInstall.i18n.get_it );

			/**
			 * Fix links for WPGlobus for Black Studio TinyMCE Widget plugin
			 */
			$( '.plugin-card.plugin-card-wpglobus-for-black-studio-widget a' ).each( function( i, link ){
				var $l = $( link ), href = $l.attr( 'href' ), nHref;
				if ( -1 !== href.indexOf( 'wpglobus-for-black-studio-widget' ) && ! $l.hasClass( 'button' ) ) {
					nHref = href.replace( 'wpglobus-for-black-studio-widget', WPGlobusPluginInstall.pluginData['wpglobus-for-black-studio-widget'].extra_data.correctLink );
					$l.attr( 'href', nHref );
				}
			});

		},
	};

	WPGlobusPluginInstall = $.extend({}, WPGlobusPluginInstall, api);
	WPGlobusPluginInstall.init();

});
