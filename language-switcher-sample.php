<?php 
/**
 * WPGlobus language switcher.
 * Example 1: with using module `Publish` from WPGlobus Plus.
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
endif; 

/**
 * WPGlobus language switcher.
 * Example 2: for two languages and active language is hidden.
 */
if ( class_exists( 'WPGlobus' ) ):

	$wpglobus_language_image = array(
		'en' => array( 
			'src' => 'https://wetag.io/wp-content/plugins/language-icons-flags-switcher/img/english.png',
			'alt' => 'English',
			'title' => 'English'
		),
		'vi' => array( 
			'src' => 'https://wetag.io/wp-content/plugins/language-icons-flags-switcher/img/Vietnam.png',
			'alt' => 'Vietnam',
			'title' => 'Vietnam'
		)
	);
	echo '<div class="wpglobus-language-switcher">';
	foreach( WPGlobus::Config()->enabled_languages as $language ) {
		if ( $language == WPGlobus::Config()->language ) {
			continue;
		}
		echo '<a href="' . WPGlobus_Utils::localize_current_url( $language ). '" class="">';
		echo '<img alt="'.$wpglobus_language_image[$language]['alt'].'" title="'.$wpglobus_language_image[$language]['title'].'" src="'.$wpglobus_language_image[$language]['src'].'" />';
		echo '</a>';
	}
	echo '</div>';
	
endif;  

# --- EOF