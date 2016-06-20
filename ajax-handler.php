<?php
/*
Copyright 2015  Sceptre Hospitality Resources, LLC, a Delaware Limited Liability Company (SHR).

This program is NOT free software; use of this theme and all related items
is permitted only with prior written agreement of SHR.
*/

include_once('app/lib/iajax.php');

/**
 * Handler to provide an ajax endpoint for loading articles
 */
function ind_ajax_posts() {
	$k = array(
		'c' => array( 'default' => 45 ),          // Lookup count (# per page)
		'p' => array( 'default' => 1 ),           // Lookup offset (page #)
		'l' => array( 'default' => '' ),          // Column taxonomy filter
		'i' => array( 'default' => '' ),          // Interest taxonomy filter
		'd' => array( 'default' => '' ),          // Destinations taxonomy filter
		's' => array( 'default' => 'publish' ),   // post status filter
		'r' => array( 'default' => 'AND' ),       // Relationship modifier
		't' => array( 'default' => 'article' )    // Post type filter
	);

	$args = _ind_getquery( $k );
	if ( $args === false ) {
		wp_send_json( array() );
	}

	$rows = ind_get_posts( $args );

	wp_send_json( $rows );
}
add_action( 'wp_ajax_ind-posts', 'ind_ajax_posts' );
add_action( 'wp_ajax_nopriv_ind-posts', 'ind_ajax_posts' );


/**
 * Utility function to strip out values from the URL request.
 *
 * @param array $keys The query keys and whether they are required, along with default values if they don't exist.
 */
function _ind_getquery( $keys ) {
	if( empty( $keys ) ) {
		return false;
	}

	$d = array();
	foreach ( $keys as $k=>$v ) {
		$n = null;

		if( isset( $_REQUEST[$k] ) ) {
			$n = $_REQUEST[$k];
		} else {
			if( array_key_exists( 'default', $v ) ) {
				$n = $v['default'];
			}
		}

		if( is_null( $n ) && isset( $v['required'] ) && ( $v['required'] == true ) ) {
				return false;
			}
		$d[$k] = $n;
	}

	return $d;
}

/**
 * Gets an array of rendered articles
 *
 * @
 */
function ind_get_posts( $a ) {
	global $post;
	$result = array();

	$defaults = array(
		'c' => array( 'default' => 9 ),           // Lookup count (# per page)
		'p' => array( 'default' => 1 ),           // Lookup offset (page #)
		'l' => array( 'default' => '' ),          // Column taxonomy filter
		'i' => array( 'default' => '' ),          // Interest taxonomy filter
		'd' => array( 'default' => '' ),          // Destinations taxonomy filter
		's' => array( 'default' => 'publish' ),   // post status filter
		'r' => array( 'default' => 'AND' ),       // Relationship modifier
		't' => array( 'default' => 'article' )    // Post type filter
	);
	$a = array_merge( $defaults, $a );

	$interests = array();
	if ( !empty( $a['i'] ) )
		$interests = explode( ',', $a['i'] );

	$destinations = array();
	if ( !empty( $a['d'] ) )
		$destinations = explode( ',', $a['d'] );

	$columns = array();
	if ( !empty( $a['l'] ) )
		$columns = explode( ',', $a['l'] );

	$args = array(
		'posts_per_page' => $a['c'],
		'paged' => $a['p'],
		'post_type' => $a['t'],
		'post_status' => $a['s'],
		'order' => 'DESC',
		'orderby' => 'ID',
		'tax_query' => array( 'relation' => $a['r'] )
	);

	if( !empty( $interests ) ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'interests',
			'field'    => 'slug',
			'terms'    => $interests,
		);
	}

  if( !empty( $destinations ) ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'destinations',
			'field'    => 'slug',
			'terms'    => $destinations,
		);
  }

  if( !empty( $columns ) ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'column',
			'field'    => 'slug',
			'terms'    => $columns,
		);
  }

	// If we didn't limit based on taxonomy, remove the entire tax_query parameter
	if ( count( $args['tax_query'] ) == 1 ) {
		unset( $args['tax_query'] );
	}

	$query = new WP_Query($args);

	while ( $query->have_posts() ) {
		$query->the_post();

		$r = '<article id="post-'. get_the_ID() . '"';
		$r .= ' class="'.implode(' ', get_post_class('contain') ) . '"';
		$r .= ">\n";
	  ob_start();
		thematic_content();
  	$r .= ob_get_clean();
		$r .= "</article>\n";

		$result[] = $r;
	}

	wp_reset_postdata();

	return $result;
}
