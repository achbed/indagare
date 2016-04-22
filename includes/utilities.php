<?php
/*
 * Utility functions that have no place elsewhere.  These should be VERY 
 * generic functions that can be used from anywhere (including inside the 
 * functions.php file)
 */
 
include_once('image-utils.php');
 
/*
 * _log function to provide detailed logging when debug mode is enabled.
 * Should be called during exceptions.  Output will send a string directly
 * to the log; all other types will be passed through print_r to show
 * object/array/variable details.
 */
if(!function_exists('_log')){
  function _log( $message ) {
    if( WP_DEBUG === true ){
      if( is_string( $message ) ){
        error_log( $message );
      } else {
        error_log( print_r( $message, true ) );
      }
    }
  }
}


function _get_fields( $post_id, $force = false ) {
	if(!class_exists('SHRCache')) {
		return _get_fields_direct( $post_id );
	}
	$c = new SHRCache( 'vp-postmeta', 0, '_get_fields_direct' );
	return $c->get();
}

function _get_fields_direct( $post_id ) {
	global $wpdb;
	$sql = "
		select distinct m.meta_key, m.meta_value
			from wp_postmeta as m 
			join wp_posts as p on p.ID=m.post_id and p.post_type='property' and p.post_status='publish'
			where m.meta_key not like '\_%'
			and trim(coalesce(m.meta_value,''))<>''
			and m.post_id=:postid;";
	$sql = str_replace(array(':postid'), array(intval($post_id)), $sql);	
	$rows = $wpdb->get_results( $sql );
	$o = array();
	foreach($rows as $r) {
		$o[$r->meta_key] = @unserialize( $r->meta_value );
		if (!($r->meta_value == serialize(false) || $o[$r->meta_key] !== false)) {
			$o[$r->meta_key] = $r->meta_value;
		}
	}
	return $o;
}







if(!function_exists('_get_field')) {
	function _get_field( $field, $post_id = false, $force = false ) {
		// Hacky, but let's try to make this work
		global $post_field_cache;
		
		if( empty( $post_field_cache ) ) {
			$post_field_cache = array(array());
		}
		
		$post_id = apply_filters('acf/get_post_id', $post_id );
		
		if( !array_key_exists( $post_id, $post_field_cache )  || $force ) {
			$post_field_cache[$post_id][$field] = get_field( $field, $post_id );
		}
		if( !array_key_exists( $field, $post_field_cache[$post_id] ) ) {
			$post_field_cache[$post_id][$field] = get_field( $field, $post_id );
		}
		$post_field = $post_field_cache[$post_id][$field];
/*		
 		// This version uses built-in caching, but may be slower.
		$found = false;
		$post_fields = wp_cache_get( $post_id, 'postfields', false, $found );
		if( ( $found === false ) || ( !is_array( $post_fields ) ) || $force ) {
			// Rebuild from scratch
			$post_fields = get_fields( $post_id );
			wp_cache_set( $post_id, $post_fields, 'postfields', 5 );
		}
 */
		
		return $post_field;
	}
}

function _get_property_distance( $geocenterpair, $post_id, $force = false ) {
	$cachekey = 'getpropertydistance_results_' . $post_id . '_' . $geocenterpair;
	
	$cached_results = get_transient( $cachekey );
	if ( ( $cached_results === false ) || $force ) {
		$location = _get_field('address',$post_id);
		$lat = $location['lat'];
		$lng = $location['lng'];
	
		$latlngfrom = explode(',', $geocenterpair);
		$latfrom = $latlngfrom[0];
		$lngfrom = $latlngfrom[1];
	
		$cached_results = vincentyGreatCircleDistance($latfrom,$lngfrom,$lat,$lng);
		
		set_transient( $cachekey, $cached_results, 0);
	}
	
	return $cached_results;
}

$RHS_RoomCache = array();

function roomtype_cache($roomcode) {
	global $RHS_RoomCache;
	
	if(!empty($RHS_RoomCache[$roomcode])) {
		return $RHS_RoomCache[$roomcode];
	}
	
	$args = array(
		'posts_per_page' => 1, 
		'post_type' => 'room', 
		'meta_key' => 'roomtypeid', 
		'meta_value' => $roomcode,
		'post_status' => 'publish'
	);
              
	$rooms = get_posts($args);
	$RHS_RoomCache[$roomcode] = $rooms;
	return $rooms;
}

/*
 * Gets the value of a GET or Cookie option (in that order).  Optional callback to alter the
 * result for wierd cases.
 */
function _getorcookie($get, $cookie, $callback = null) {
	$var = false;
	if( !empty( $_GET[$get] ) ) {
		$var = $_GET[$get];
	}
	if( !empty( $_COOKIE[$cookie] ) && empty( $proplistbyid ) ) {
		$var = $_COOKIE[$cookie];
	}
	if( is_callable( $callback ) ) {
		$callback($var);
	}
	return $var;
}

/*
 * Handles reading the from and to variables from the requesting URL, or
 * uses the defaults.  Returns an array with the results.
 */
function get_dateparams() {
	$f = false;
	if( !empty( $_GET['from'] ) ) {
		$f = strtotime( $_GET['from'] );
	}
	if( ($f === false) || ($f == -1) ) {
		$f = strtotime( SHR_DEFAULT_RESERVATION_DATE );
	}

	$t = false;
	if( !empty( $_GET['to'] ) ) {
		$t = strtotime( $_GET['to'] );
	}
	if( ($t === false) || ($t == -1) ) {
		$t = strtotime( SHR_DEFAULT_RESERVATION_LENGTH, $f );
	}

	return array(
		'from' => date( "Y-m-d", $f ),
		'to' => date( "Y-m-d", $t )
	);
}

/*
 * Handles populating the global from and to variables from the URL parameters
 * or defaults.
 */
function setup_dateparams() {
	global $from, $to;
	
	$r = get_dateparams();
	$from = $r['from'];
	$to = $r['to'];
}


/*
 * Imported from Drupal.  Allows returning an HTML page with variables
 */
function theme_render_template($template_file, $variables) {
  // Extract the variables to a local namespace
  extract($variables, EXTR_SKIP);

  // Start output buffering
  ob_start();

  // Include the template file
  include SHR_THEME_FOLDER . '/templates/' . $template_file . '.tpl.php';

  // End buffering and return its contents
  return ob_get_clean();
}
