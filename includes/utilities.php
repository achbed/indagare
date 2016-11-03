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

		return $post_field;
	}
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
