<?php

if ( ! class_exists('WPGlobus_WP_Theme') ) :

	class WPGlobus_WP_Theme {
	
		/**
		 * WPGlobus config file
		 */
		public $wpglobus_config_file = 'wpglobus-config.json';
		
		/**
		 * Config file from wpml
		 */		
		public $wpml_config_file = 'wpml-config.xml';
		
		/**
		 * Full path to config file
		 */		
		public $config_dir_file = '';

		/**
		 * Array of paths to themes
		 */
		public $theme_dir = array();
		
		/**
		 * Config
		 */		
		public $config = array();
		
		/**
		 * Source of config
		 */
		public $config_from = '';
		
		function __construct() {
			
			/**
			 * get the absolute path to the child theme directory
			 */
			$this->theme_dir['child']  =  get_stylesheet_directory();
			
			/**
			 * in the case a child theme is being used, the absolute path to the parent theme directory will be returned
			 */
			$this->theme_dir['parent'] =  get_template_directory();  
			
			$this->get_config();

			if ( ! empty( $this->config ) ) {
				
				add_filter( 'wpglobus_localize_custom_data', array( $this, 'custom_data' ) );
			
				add_filter( 'wpglobus_enabled_pages', array( $this, 'enable_page' ) );
				
			}	
			
		}

        /**
         * Add custom fields for WPGlobusDialog
         * @param $data
         * @return array
         */
		public function custom_data( $data ) {
			
			$elements = array();
			
			if ( $this->config_from == $this->wpml_config_file ) {
				
				foreach( $this->config['wpml-config']['admin-texts']['key']['key'] as $elem ) {
					if ( empty( $elem['attr'] ) ) {
						/**
						 * single element in wpml-config.xml file
						 */
						$elements[] = $elem['name'];
					} else {	
						$elements[] = $elem['attr']['name'];
					}	
				}	
				
			} elseif ( $this->config_from == $this->wpglobus_config_file )  {
				
				foreach ( $this->config['admin_texts'] as $field ) {
					$elements[] = $field;
				}	
			
			}

			if ( ! empty( $elements ) ) {	
				if ( empty( $data['addElements'] ) ) {
					$data['addElements'] = $elements;
				} else {
					$data['addElements'] = array_merge(
						$data['addElements'],
						$elements
					);	
				}
			}		
			
			return $data;
		
		}	

		/**
		 * Get config from file
		 */
		public function get_config() {

			$config_files = array();
			
			/**
			 * First look for wpglobus config
			 */			
			$config_files[] = $this->wpglobus_config_file;
			
			/**
			 * and then look wpml config
			 */
			$config_files[] = $this->wpml_config_file;

            $config_file = '';

			foreach ( $config_files as $config_file ) :
				
				$this->config_dir_file = '';
				
				if ( $this->theme_dir['parent'] == $this->theme_dir['child'] ) {
					$file = $this->theme_dir['parent'] . '/' . $config_file;
					if ( file_exists( $file ) ) {
						$this->config_dir_file = $file;
					}	
				} else {
					foreach( $this->theme_dir as $relation=>$dir ) {
						
						$file = $dir . '/' . $config_file;
						if ( file_exists( $file ) && 'child' == $relation ) {
							/**
							 * Now config in child theme has highest priority
							 */
							$this->config_dir_file = $file;
							break;
						}		
						if ( file_exists( $file ) && 'parent' == $relation ) {
							$this->config_dir_file = $file;
						}	
					
					}	
				}
				
				if ( ! empty( $this->config_dir_file ) ) {
					break;	
				}	
				
			endforeach;
		
			switch ( $config_file ) {
			case $this->wpglobus_config_file :
				$this->config = $this->json2array( file_get_contents( $this->config_dir_file ) );
				$this->config_from = $this->wpglobus_config_file;
				break;
			case $this->wpml_config_file :
				$this->config = $this->xml2array( file_get_contents( $this->config_dir_file ) );
				$this->config_from = $this->wpml_config_file;
				break;
			};
			
		
		}

        /**
         * Enable page to load scripts and styles
         * @param $pages
         * @return array
         */
		public function enable_page( $pages ) {

			if ( empty( $this->config_dir_file ) ) {
				return $pages;	
			}
			
			if ( 'themes.php' == WPGlobus_WP::pagenow() && ! empty( $_GET['page'] ) ) {
				$pages[] = 'themes.php';
			}
			
			return $pages;
			
		}

        /**
         * Conversion json to array
         * @param $content
         * @return array
         */
		public function json2array( $content ) {
			$content = json_decode( $content, true );
			return $content;
		}

        /**
         * Conversion xml to array
         * @param $contents
         * @param int $get_attributes
         * @return array
         */
		public function xml2array($contents, $get_attributes=1) {
			if(!$contents) return array();

			if(!function_exists('xml_parser_create')) {
				//print "'xml_parser_create()' function not found!";
				return array();
			}
			//Get the XML parser of PHP - PHP must have this module for the parser to work
			$parser = xml_parser_create();
			xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
			xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
			xml_parse_into_struct( $parser, $contents, $xml_values );
			xml_parser_free( $parser );

			if(!$xml_values) return;//Hmm...

			//Initializations
			$xml_array = array();
			$parents = array();
			$opened_tags = array();
			$arr = array();

			$current = &$xml_array;

			//Go through the tags.
			foreach($xml_values as $data) {
				unset($attributes,$value);//Remove existing values, or there will be trouble

				//This command will extract these variables into the foreach scope
				// tag(string), type(string), level(int), attributes(array).
				extract($data);//We could use the array by itself, but this cooler.

				$result = '';
				if($get_attributes) {//The second argument of the function decides this.
					$result = array();
					if(isset($value)) $result['value'] = $value;

					//Set the attributes too.
					if(isset($attributes)) {
						foreach($attributes as $attr => $val) {
							if($get_attributes == 1) $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
							/**  :TODO: should we change the key name to '_attr'? Someone may use the tagname 'attr'. Same goes for 'value' too */
						}
					}
				} elseif(isset($value)) {
					$result = $value;
				}

				//See tag status and do the needed.
				if($type == "open") {//The starting of the tag '<tag>'
					$parent[$level-1] = &$current;

					if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
						$current[$tag] = $result;
						$current = &$current[$tag];

					} else { //There was another element with the same tag name
						if(isset($current[$tag][0])) {
							array_push($current[$tag], $result);
						} else {
							$current[$tag] = array($current[$tag],$result);
						}
						$last = count($current[$tag]) - 1;
						$current = &$current[$tag][$last];
					}

				} elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
					//See if the key is already taken.
					if(!isset($current[$tag])) { //New Key
						$current[$tag] = $result;

					} else { //If taken, put all things inside a list(array)
						if((is_array($current[$tag]) and $get_attributes == 0)//If it is already an array...
								or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1)) {
							array_push($current[$tag],$result); // ...push the new element into that array.
						} else { //If it is not an array...
							$current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
						}
					}

				} elseif($type == 'close') { //End of tag '</tag>'
					$current = &$parent[$level-1];
				}
			}

			return($xml_array);
		} 		

	}		

endif;