<?php 
/**
 * WPGlobus language switcher.
 */
if ( class_exists( 'WPGlobus' ) ): ?>
	<div class="wpglobus-selector-box">  <?php
		
		/**
		 * Filter that prevent using language that has `draft` status.
		 * That works with module `Publish` from WPGlobus Plus add-on.
		 */
		$enabled_languages = apply_filters( 'wpglobus_extra_languages', WPGlobus::Config()->enabled_languages, WPGlobus::Config()->language );
		
		foreach ( $enabled_languages as $language ):
			$url = null;
			$is_current = true;

			if ( $language != WPGlobus::Config()->language ) {
				$url = WPGlobus_Utils::localize_current_url( $language );
				$is_current = false;
			}
			
			$flag = '<img src="'.WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[ $language ].'" />';
			$link = $flag . '&nbsp;' . WPGlobus::Config()->en_language_name[$language] . '&nbsp;&nbsp;&nbsp;';	
			
			printf( '<a %s %s>%s</a>', ( empty( $url ) ? '' : 'href="' . esc_url( $url ) . '"' ), ( $is_current ? 'class="wpglobus-current-language"' : '' ), $link );
	  
		endforeach; ?>
	  
   </div>  <?php 
endif; ?>