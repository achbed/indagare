<?php
/*
 * Handles all weather-related functionality
 */

include_once('utilities.php');

function get_weather($loc_string, $days = 1, $cache_length = 3600) {
		return get_weather_direct($loc_string, $days);
}

/*
 * Gets some weather data from a weather service for a given post_id.  Caches the result for future use (default is 1 hour).
 */
function get_weather_direct($loc_string, $days = 1) {
	// API Key for the weather source
//	$api_key = "1a4846f81014b54e55bbcb6ff7dd20a70ec57ff4";  // Valueplace API key
	$api_key = 'n9jv4tg2j4zz6bj8x6ejnzm4';  // Indagare API key
	
	if( empty( $post_id ) ) {
		return array();
	}
	
	$premiumurl = sprintf('http://api.worldweatheronline.com/premium/v1/weather.ashx?key=%s&q=%s&num_of_days=%s&showlocaltime=yes',
			$api_key, urlencode($loc_string), intval($num_of_days));
		
	$userurl = 'http://www.worldweatheronline.com/v2/weather.aspx?q='.$loc_string;
		
	$http_response = wp_remote_get(	$premiumurl );
	if( !in_array( wp_remote_retrieve_response_code( $http_response ), array( 200, 301, 302 ) ) ) {
		_log( 'Error returned when loading weather for location ' . $loc_string );
		_log( 'URL: ' . $premiumurl);
		_log( 'Response code: ' . wp_remote_retrieve_response_code( $http_response ) );
		_log( 'Returned Headers:' );
		_log( wp_remote_retrieve_headers( $http_response ) );
		
		return array();
	}
	
	$xml_response = wp_remote_retrieve_body( $http_response );
	if( empty( $xml_response ) ) {
		_log( 'Error returned when loading weather for location ' . $loc_string );
		_log( 'Response code: ' . wp_remote_retrieve_response_code( $http_response ) );
		_log( 'Returned Headers:' );
		_log( wp_remote_retrieve_headers( $http_response ) );
		
		return array();
	}
	
	// check that the weather service is returning valid XML
	$responsetest = @simplexml_load_string($xml_response);
	if ( empty($responsetest) ) {
		_log('Invalid XML returned when loading weather for location '.$loc_string);
		_log('Response code: '.wp_remote_retrieve_response_code($http_response));
		_log('Returned Headers:');
		_log(wp_remote_retrieve_headers($http_response));
		_log('Returned body:');
		_log($xml_response);
		
		return array();
	}

	$responsetest = simplexml_load_string($xml_response);
	$time = strtotime( $xml->time_zone->localtime );

	$cached_results = array(
		'lat' => $lat,
		'lng' => $lng,
		'current' => strval($responsetest->current_condition->temp_F),
		'min' => strval($responsetest->weather->mintempF),
		'max' => strval($responsetest->weather->maxtempF),
		'url' => $userurl,
		'localtime' => $time,
		'localtime_formatted' => date( 'g:i A', $time )
	);
		
	return $cached_results;
}


/*
 * Gets and formats the weather given either a get_weather result, or a post_id.  If no valid
 * data is found, returns an empty string.
 */
function format_weather( $w, $cache_length = 3600 ) {
	if( !is_array( $w ) ) {
		$w = get_weather( intval( $w ), $cache_length );
		if( empty( $w ) ) {
			return '';
		}
	}
	
	return theme_render_template('weather_widget', $w);
}
