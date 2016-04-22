<?php
/**
 * Plugin Name: PBD AJAX Load Posts
 * Plugin URI: http://www.problogdesign.com/
 * Description: Load the next page of posts with AJAX.
 * Version: 0.1
 * Author: Pro Blog Design
 * Author URI: http://www.problogdesign.com/
 */
 
 /**
  * Initialization. Add our script if needed on this page.
  */
 function pbd_alp_init() {
 	global $wp_query;
 
 	// Add code to index pages.
// 	if( !is_singular() ) {	
// 	if( !is_singular() || is_search() ) {	
 	if( 
 		is_post_type_archive('hotel') || 
 		is_post_type_archive('restaurant') || 
 		is_post_type_archive('shop') || 
 		is_post_type_archive('activity') || 
 		is_post_type_archive('article') || 
 		is_post_type_archive('offer') 
 	) {	
 		// Queue JS and CSS
 		wp_enqueue_script(
 			'pbd-alp-load-posts',
 			get_bloginfo('stylesheet_directory') . '/includes/ajax-load-posts/js/load-posts.js',
 			array('jquery'),
 			'1.0',
 			true
 		);
 		
 		wp_enqueue_style(
 			'pbd-alp-style',
 			get_bloginfo('stylesheet_directory') . '/includes/ajax-load-posts/css/style.css',
 			false,
 			'1.0',
 			'all'
 		);
 		
 	
 		
 		// What page are we on? And what is the pages limit?
 		$max = $wp_query->max_num_pages;
 		$paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;
 		
 		// Add some parameters for the JS.
 		wp_localize_script(
 			'pbd-alp-load-posts',
 			'pbd_alp',
 			array(
 				'startPage' => $paged,
 				'maxPages' => $max,
 				'nextLink' => next_posts($max, false)
 			)
 		);
 	}
 }
 add_action('template_redirect', 'pbd_alp_init');
 
 ?>