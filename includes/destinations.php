<?php

//add_action( 'save_post', 'rebuild_destinations' );
//add_action( 'transition_post_status',  'rebuild_destinations', 20, 1);

add_action('edited_destinations', 'rebuild_destinations', 20, 1);
add_action('created_destinations', 'rebuild_destinations', 20, 1);
add_action('delete_destinations', 'rebuild_destinations', 20, 1);

function rebuild_destinations() {
	make_destinations(true);
}

function make_destinations( $force = false ) {
	make_destinations_top( $force );
	make_destinations_regions( $force );
	make_destinations_details( $force );
}

function make_destinations_top( $update = false ) {
	$fn = wp_upload_dir();
	$fn = $fn['basedir'];
	$fn = $fn . '/destinations_top.json';

	if ( file_exists( $fn ) && ( $update == false ) ) {
		return;
	}
	
	$destinations = get_terms( 'destinations', array( 'hide_empty' => 0 ) );

	$regions = array_filter($destinations, function ($t) {
		$destinationstree = get_ancestors( $t->term_id, 'destinations' );
		$destdepth = count($destinationstree);
		return $destdepth == 0;
	});		
	
	$top_json = array();
	$top_json['0'] = array("all");
	
	foreach ( $regions as $region ) {
		// skip world
		if ( $region->slug !== 'world' ) {
			$top_json[ strval( $region->term_id ) ] = array( $region->slug );
		}
	}

	file_put_contents($fn, json_encode($top_json));
}

function make_destinations_regions( $update = false ) {
	$fn = wp_upload_dir();
	$fn = $fn['basedir'];
	$fn = $fn . '/destinations_regions.json';

	if ( file_exists( $fn ) && ( $update == false ) ) {
		return;
	}
	
	$destinations = get_terms( 'destinations', array( 'hide_empty' => 0 ) );

	$regions = array_filter($destinations, function ($t) {
		$destinationstree = get_ancestors( $t->term_id, 'destinations' );
		$destdepth = count($destinationstree);
		return $destdepth == 0;
	});		
	
	$regions_json = array();
	
	foreach ( $regions as $region ) {
		// skip world
		if ( $region->slug !== 'world' ) {
			
			$countries = get_terms( 'destinations', array( 'child_of' => $region->term_id, 'hide_empty' => 0 ) );
			$countriesf = array_filter($countries, function ($term) {
				$tree = get_ancestors( $term->term_id, 'destinations' );
				return count($tree) == 1;
			});
			
			foreach ( $countriesf as $country ) {
				$regions_json[ strval( $region->term_id ) ][] = implode( '|', array( $country->name, $country->slug, $country->term_id ) );
			}
		}	
	}

	file_put_contents($fn, json_encode($regions_json));
}

/**
 * Gets a ACF field, and returns a list of label/value objects
 */
function get_keyed_field($field, $post = false) {
	$labels = get_field_object($field, $post);
	$output = array();
	if(empty($labels['value'])) {
		return $output;
	}
	if(!is_array($labels['value'])) {
		$labels['value'] = array($labels['value']);
	}
	foreach($labels['value'] as $i) {
		$output[] = array( 'value' => $i, 'name' => $labels['choices'][$i] );
	}
	return $output;
}

function make_destinations_details( $update = false ) {
	$fn = wp_upload_dir();
	$fn = $fn['basedir'];
	$fn = $fn . '/destinations_details.json';

	if ( file_exists( $fn ) && ( $update == false ) ) {
		return;
	}
	
	$destinations = get_terms( 'destinations', array( 'hide_empty' => 0 ) );
	
	$destinationsf = array_filter($destinations, function ($t) {
		$destinationstree = get_ancestors( $t->term_id, 'destinations' );
		$destdepth = count($destinationstree);
		return $destdepth == 2;
	});

	$details_json = array();
	
	foreach ( $destinationsf as $destination ) {
		$destinationstree = destinationstaxtree($destination->term_id);
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];
		$season = array();
		$seasons = get_field( 'season', 'destinations' . '_' . $destination->term_id);
		if(!empty($seasons)) {
			foreach($seasons as $i) {
				$season[] = array('name' => $i->name, 'value' => $i->slug, 'term_id' => $i->term_id);
			}
		}
		$interest = array();
		$interests = get_field('interest', 'destinations' . '_' . $destination->term_id);
		if(!empty($interests)) {
			foreach($interests as $i) {
				$itm = array('name' => $i->name, 'value' => $i->slug, 'term_id' => $i->term_id);
				
				$icon = get_field( 'icon', 'destinationinterest' . '_' . $i->term_id);
				if(!empty($icon['sizes']['thumb-small'])) {
					$itm['icon'] = $icon['sizes']['thumb-small'];
				}
				
				$interest[] = $itm;
			}
		}
		$imageobj = _get_field('header-image', 'destinations' . '_' . $destination->term_id);
		$image = $imageobj['sizes']['thumb-large'];
		if ( $image ) {
			$details_json[strval($destination->term_id)] = array(
				'image' => $image,
				'url' => ( '/destinations/' . $top->slug . '/' . $reg->slug . '/' . $destination->slug . '/' ),
				'name' => $destination->name,
				'topslug' => $top->slug,
				'topid' => $top->term_id,
				'topname' => $top->name,
				'regslug' => $reg->slug,
				'regid' => $reg->term_id,
				'regname' => $reg->name,
				'slug' => $destination->slug,
				'season' => $season,
				'interest' => $interest
			);
		}
	}
	file_put_contents($fn, json_encode($details_json));
}

//add_action( 'save_post', 'clear_destinations_list' );
//add_action( 'transition_post_status',  'clear_destinations_list', 20, 1);

add_action('edited_destinations', 'clear_destinations_list', 20, 1);
add_action('created_destinations', 'clear_destinations_list', 20, 1);
add_action('delete_destinations', 'clear_destinations_list', 20, 1);

function get_destinations_list(){
	if ( ! class_exists( 'SHRCache' ) ) {
		// No cache object.  Go direct.
		return get_destinations_list_direct();
	}
	
	$c = new SHRCache('getdestlist2', 0, 'get_destinations_list_direct' );
	return $c->get();
}

function clear_destinations_list(){
	if ( ! class_exists( 'SHRCache' ) ) {
		// No cache object.  Nothing to clear.
		return;
	}
	
	$c = new SHRCache( 'getdestlist2', 0, 'get_destinations_list_direct' );
	$c->prime();
}

function get_destinations_list_direct(){
	$content = '';
		
	$destinations = get_terms( 'destinations', array( 'hide_empty' => 0 ) );
	$destinationsf = array_filter($destinations, function ($t) {
		$destinationstree = get_ancestors( $t->term_id, 'destinations' );
		$destinationstree = array_reverse($destinationstree);
		$destdepth = count($destinationstree);
		return $destdepth == 2;
	});
	
	$classes = 'article type-article status-publish hentry contain all';

	foreach ( $destinationsf as $destination ) {
		$imageobj = get_field('header-image', 'destinations' . '_' . $destination->term_id);
		$image = $imageobj['sizes']['thumb-large'];

		if ( $image ) {
			$destinationstree = destinationstaxtree($destination->term_id);
			$reg = $destinationstree['reg'];
			$top = $destinationstree['top'];

			$destclass = $classes . ' ' . $top->slug . ' ' . $reg->slug;
			if ( $destination->name == 'Other Recommended Hotels' ) {
				$destclass .= ' otherhidden';
			}

			$content .= '<article class="'. $destclass .'">'."\n";
			$content .= '<a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $destination->slug .'/">'."\n";
			$content .= '<img src="'.$image.'" alt="Article">'."\n";
			//$content .= '<img class="lazy" data-original="'.$image.'" alt="Article">'."\n";
			$content .= '<span class="info">'."\n";
			$content .= '<h3>'.$destination->name.'</h3>'."\n";
			$content .= '</span><!-- .info -->'."\n";
			$content .= '</a>'."\n";
			$content .= '</article><!-- #post -->'."\n";
		}
	}		

	return $content;
}
