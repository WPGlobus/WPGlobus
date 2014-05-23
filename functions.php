<?php
/*
 * returns cleaned string and language information
*/

/**
 * Improved version, also understands $url without scheme:
 * //example.com, example.com/, and so on
 */

/*
 *
 */
function globus_getUrl($current_language = '') {
	global $WPGlobus_Config;

	$current_language = ( '' == $current_language ) ? $WPGlobus_Config->language : $current_language;
	$url = '';

	if ( $WPGlobus_Config->get_url_mode() == $WPGlobus_Config::GLOBUS_URL_PATH ) {

		$language = '/' . $current_language;
		if ( $current_language == $WPGlobus_Config->default_language && $WPGlobus_Config->hide_default_language ) {
			$language = '';
		}

		$url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER["HTTP_HOST"] . $language . $WPGlobus_Config->url_info['url'];

	} elseif  ( $WPGlobus_Config->get_url_mode() == $WPGlobus_Config::GLOBUS_URL_QUERY ) {

		if ( $current_language == $WPGlobus_Config->default_language && $WPGlobus_Config->hide_default_language ) {

			$url = '';

		} else {

			$arr = globus_extractURL( $WPGlobus_Config->url_info['url'] );

			if ( false === strpos( $arr['url'], '?' ) ) {
				$url = '?';
			} else {
				$url = '&';
			}
			$url .= 'lang=' . $current_language;

		}
		//error_log( print_r( $r, true ) );
		$url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER["HTTP_HOST"] . $WPGlobus_Config->url_info['url'] . $url;
	}

	return $url;
}

function globus_isEnabled($lang) {
	global $WPGlobus_Config;
	return in_array( $lang, $WPGlobus_Config->enabled_languages );
}

function globus_startsWith($s, $n) {
	if(strlen($n)>strlen($s)) return false;
	if($n == substr($s,0,strlen($n))) return true;
	return false;
}




function globus_parseURL($url) {

	$scheme   = '(?:(\w+)://)';
	$userpass = '(?:(\w+)\:(\w+)@)';
	$host     = '([^/:]+)';
	$port     = '(?:\:(\d*))';
	$path     = '(/[^#?]*)';
	$query    = '(?:\?([^#]+))';
	$fragment = '(?:#(.+$))';

	$r = '!' . $scheme . '?' . $userpass . '?' . $host . '?' . $port . '?' . $path . '?' . $query . '?' . $fragment . '?!i';

    preg_match ( $r, $url, $out );

    $result = @array(
        "scheme"   => $out[1],
        "host"     => $out[4].(($out[5]=='')?'':':'.$out[5]),
        "user"     => $out[2],
        "pass"     => $out[3],
        "path"     => $out[6],
        "query"    => $out[7],
        "fragment" => $out[8]
	);

	// Host can be in path in case of url with incorrect scheme. Try to find it in path
	if( empty( $result['host'] ) ) {
		$www     = '(www\.)';
		$domain  = '((?:\w+\.)+\w+)';
		
		$r2 = '!' . $www . '?' . $domain . $path . '?!i';

		if( preg_match( $r2, $url, $out2 ) ) {
			$result['host'] = $out2[1] . $out2[2];
			$result['path'] = @$out2[3];
		}
	}

    return $result;
}

function globus_extractURL($url, $host = '', $referer = '') {
	
	global $WPGlobus_Config;

	$home = globus_parseURL(get_option('home'));
	$home['path'] = trailingslashit($home['path']);
	$referer = globus_parseURL($referer);
	
	$result = array();
	$result['language'] 		= $WPGlobus_Config->default_language;
	$result['url'] 				= $url;
	$result['original_url'] 	= $url;
	$result['host'] 			= $host;
	$result['redirect'] 		= false;
	$result['internal_referer'] = false;
	$result['home'] 			= $home['path'];

	switch( $WPGlobus_Config->get_url_mode() ) {
		case WPGlobus_Config::GLOBUS_URL_PATH:
			// pre url
			$url = substr($url, strlen($home['path']));
			if($url) {
				// might have language information
				if(preg_match("#^([a-z]{2})(/.*)?$#i",$url,$match)) {
					if( globus_isEnabled($match[1]) ) {
						// found language information
						$result['language'] = $match[1];
						$result['url'] = $home['path'].substr($url, 3);
					}
				}
			}
			break;
		case WPGlobus_Config::GLOBUS_URL_DOMAIN:
			// pre domain
			if($host) {
				if(preg_match("#^([a-z]{2}).#i",$host,$match)) {
					if( globus_isEnabled($match[1]) ) {
						// found language information
						$result['language'] = $match[1];
						$result['host'] = substr($host, 3);
					}
				}
			}
			break;
	}
	
	// check if referer is internal
	if( $referer['host'] == $result['host'] && globus_startsWith( $referer['path'], $home['path'] ) ) {
		// user coming from internal link
		$result['internal_referer'] = true;
	}
	
	if( isset($_GET['lang']) && globus_isEnabled($_GET['lang']) ) {
		// language override given
		$result['language'] = $_GET['lang'];
		$result['url'] = preg_replace("#(&|\?)lang=".$result['language']."&?#i","$1",$result['url']);
		$result['url'] = preg_replace("#[\?\&]+$#i","",$result['url']);

	} elseif( $home['host'] == $result['host'] && $home['path'] == $result['url'] ) {

		if( empty($referer['host']) || !$WPGlobus_Config->hide_default_language ) {

			$result['redirect'] = true;

		} else {
			// check if activating language detection is possible
			if(preg_match("#^([a-z]{2}).#i",$referer['host'],$match)) {
				if( globus_isEnabled($match[1]) ) {
					// found language information
					$referer['host'] = substr($referer['host'], 3);
				}
			}
			if(!$result['internal_referer']) {
				// user coming from external link
				$result['redirect'] = true;
			}
		}
	}
	
	return $result;
}