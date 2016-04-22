<?php

//add_action( 'save_post', 'rebuild_maplocations' );
//add_action( 'transition_post_status',  'rebuild_maplocations', 20, 1);

add_action('edited_destinations', 'rebuild_maplocations', 20, 1);
add_action('created_destinations', 'rebuild_maplocations', 20, 1);
add_action('delete_destinations', 'rebuild_maplocations', 20, 1);

function rebuild_maplocations() {
	maplocations(true);
}
// define map locations function
function maplocations( $update = false ) {
//		rebuild_maplocations();
//		return;
		
	$fn = wp_upload_dir();
	$fn = $fn['basedir'];
	$fn = $fn . '/maplocations.json';

	if( !file_exists( $fn ) || $update ) {
		file_put_contents($fn, get_maplocations() );
	}
}

function get_maplocations() {
	$destinations = get_terms( 'destinations', array( 'hide_empty' => 0 ) );
	$destinationsf = array_filter($destinations, function ($t) {
		$destinationstree = get_ancestors( $t->term_id, 'destinations' );
		$destinationstree = array_reverse($destinationstree);
		$destdepth = count($destinationstree);
		return $destdepth == 2;
	});
	
	foreach ( $destinationsf as $destination ) {

		$destinationstree = destinationstaxtree($destination->term_id);
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];

		$location = _get_field('address', 'destinations' . '_' . $destination->term_id);
		$classes = array('all');
		$classes[] = $top->slug;
		$classes[] = $reg->slug;
		
		$a = get_field('season', 'destinations' . '_' . $destination->term_id);
		if(!empty($a)) {
			if(!is_array($a)) $a = array($a);
			foreach($a as $i) {
				if(is_object($i)) {
					if(property_exists($i, 'slug')) {
						$i = $i->slug;
					}
				}
				$i = strval($i);
				$classes[] = $i;
			}
		}
		
		$a = get_field('interest', 'destinations' . '_' . $destination->term_id);
		if(!empty($a)) {
			if(!is_array($a)) $a = array($a);
			foreach($a as $i) {
				if(is_object($i)) {
					if(property_exists($i, 'slug')) {
						$i = $i->slug;
					}
				}
				$i = strval($i);
				$classes[] = $i;
			}
		}
		
		$class = implode( ' ', $classes );
		
		if ( !empty( $location['coordinates'] ) ) {
			$latlng = explode(",",$location['coordinates']);
			$v = array();
			$v[] = $destination->name;
			$v[] = $latlng[0];
			$v[] = $latlng[1];
			$v[] = '/destinations/' . $top->slug . '/' . $reg->slug . '/' . $destination->slug . '/';
			$v[] = $class;
			$values[] = $v;
		}
	}		

	return json_encode( $values );
}
