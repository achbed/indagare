<?php

function map_canvas($controls = false, $mapclass = '') {
	$output = '<div id="mapcanvas"';
	if ( !empty( $mapclass ) )
		$output .= ' class="'.$mapclass.'"';
	$output .= '></div>' . "\n";
	if($controls !== false) {
		$output .= '<div id="map-buttons">';
		$output .= '<div id="map-buttons-container">';
		$output .= '<a id="map-zoom-button" title="Zoom" onclick="return goZoom();" href="#">Zoom</a>'."\n";
		$output .= '<a id="map-modal-toggle" title="Full Screen" href="#">Full Screen</a>'."\n";
		$output .= '</div>'."\n";
		$output .= '</div>'."\n";
	}
	return $output;
}

// define map function
function map() {
	global $post;
	global $wp_query;
	
	$markers = array();

	// singular review page || archive review page
	if ( is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'hotel' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' ) 
	) {
		$page_is_singular = is_singular();
		// parse filters to use for permalinks
		parse_str($_SERVER['QUERY_STRING'], $urlvars);
		$urlvars = http_build_query($urlvars);

		$posttype = get_query_var('post_type');
		$destinationstree = destinationstaxtree();
		$dest = $destinationstree['dest'];
		$reg = $destinationstree['reg'];
		$depth = $destinationstree['depth'];
		
		$neighborhoodschecked = $_GET['destinations'];
		$benefitschecked = $_GET['benefit'];
		$editorschecked = $_GET['editorspick'];
		$mealschecked = $_GET['mealtype'];

		if ( get_query_var('post_type') == 'hotel' ) {
			$filtertype = 'hoteltype';
		} else if ( get_query_var('post_type') == 'restaurant' ) {
			$filtertype = 'restauranttype';
		} else if ( get_query_var('post_type') == 'shop' ) {
			$filtertype = 'shoptype';
		} else if ( get_query_var('post_type') == 'activity' ) {
			$filtertype = 'activitytype';
		}
		$filterschecked = $_GET[$filtertype];

		// single post - parse name
		$args = $wp_query->query;
		$args['posts_per_page'] = -1; 
		/*
		if ( $page_is_singular ) {
			$slug = $post->post_name;
			$args['name'] = $slug;
			// archive - parse post type and destination
		} else {
			if ( $neighborhoodschecked ) {
				$args['destinations'] = $neighborhoodschecked;
			} else if ( $depth == 1 ) {
				$args['destinations'] = $reg->slug;
			} else if ( $depth == 2 ) {
				$args['destinations'] = $dest->slug;
			}
			if ( $benefitschecked ) {
				$args['benefit'] = $benefitschecked;
			}
			if ( $editorschecked ) {
				$args['editorspick'] = $editorschecked;
			}
			if ( $mealschecked ) {
				$args['mealtype'] = $mealschecked;
			}
			if ( $filterschecked ) {
				if ( get_query_var('post_type') == 'hotel' ) {
					$args['hoteltype'] = $filterschecked;
				} else if ( get_query_var('post_type') == 'restaurant' ) {
					$args['restauranttype'] = $filterschecked;
				} else if ( get_query_var('post_type') == 'shop' ) {
					$args['shoptype'] = $filterschecked;
				} else if ( get_query_var('post_type') == 'activity' ) {
					$args['activitytype'] = $filterschecked;
				}
			}
		}
		*/
	
		$i = 0;
		
		$postlist = new WP_Query( $args );
		$rendered_postids = array();
		
		// create markers for initial post type
		while( $postlist->have_posts() ) {
			$postlist->the_post();
			$rendered_postids[] = $post->ID;
			$markertype = get_post_type();
			$mapaddress = get_field('address-display');
			$mapaddress2 = get_field('address-display-2');
			$location = get_field('address');
			
			// is there an address field with coordinates in its array - if not, skip it
			if ( !empty( $location['coordinates'] ) ) {
				$maptitle = get_the_title();
				$posturl = get_permalink();
				if( !empty( $urlvars ) ) {
					$posturl .= '?'.$urlvars;
				}
		
				$c_string = '<div class="markercontent" id="markercontent-'.$post->ID.'">';
				$c_string .= '<h3>';
				if ( !$page_is_singular ) {
					$c_string .= '<a class="more" target="_blank" href="'.$posturl.'">';
				}
				$c_string .= $maptitle;
				if ( !$page_is_singular ) {
					$c_string .= '</a>';
				}
				$c_string .= '</h3>';
				$c_string .= '<div class="popup-address">'.$mapaddress;
				if ( $mapaddress2 !== '' ) {
					$c_string .= ', '.$mapaddress2;
				}
				$c_string .= '</div></div>';
				
				$markeritem = array(
					'id' => $post->ID,
					'title' => addcslashes($maptitle,"'"),
					'content' => addcslashes($c_string,"'"),
					'coordinates' => addcslashes($location['coordinates'],"'"),
					'type' => 'current',
					'show' => 1
				);
							
				$markers[] = $markeritem;
			}
		} // end create markers for initial post type
		
		wp_reset_postdata();
		
		
		// marker lists for other post types
		if ( $page_is_singular ) {
		
			$destinationstree = destinationstree();
			$dest = $destinationstree['dest'];
			$reg = $destinationstree['reg'];
			$top = $destinationstree['top'];
			
			$posttypes = array( 'hotel', 'restaurant', 'shop', 'activity' );
			
			$args = array ( 
				'post_type' => $posttypes, 
				'posts_per_page' => -1,
				'destinations' => $dest->slug,
				'post_status' => 'publish',
				'post__not_in' => $rendered_postids,
				'orderby' => array( 'type' => 'DESC', 'name' => 'ASC' )
			);
			
			$postlist = new WP_Query( $args );
			
			// additional marker loop
			while( $postlist->have_posts() ) {
				$postlist->the_post();
				$markertype = get_post_type();
				$mapaddress = get_field('address-display');
				$mapaddress2 = get_field('address-display-2');
				$location = get_field('address');
				
				// is there an address field - if not, skip it
				if ( !empty( $location['coordinates'] ) ) {
					$maptitle = get_the_title();
					
					$contentstring = '<div class="markercontent" id="markercontent-'.$post->ID.'">';
					$contentstring .= '<h3><a class="more" target="_blank" href="'.get_permalink().'">'.$maptitle.'</a></h3>';
					$contentstring .= '<div class="popup-address">'.$mapaddress;
					if ( $mapaddress2 !== '' ) {
						$contentstring .= ', '.$mapaddress2;
					}
					$contentstring .= '</div></div>';
					
					$markeritem = array(
						'id' => $post->ID,
						'title' => $maptitle,
						'content' => $contentstring,
						'coordinates' => $location['coordinates'],
						'type' => $markertype,
						'show' => 0
					);
								
					$markers[] = $markeritem;
				}
			}
			wp_reset_postdata();
		}
		
		$json = json_encode($markers);
		$json = str_replace('<','&lt;', $json);
		?><div id="mapmarkerjson" style="display:none !important;"><?php print $json; ?></div><?php
	}
}
