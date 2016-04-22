<?php
/**
 * Template Name: XML Destinations
 *
 * 
 */
 
	$destinations = get_terms( 'destinations', array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false) );

	$regions = array_filter($destinations, function ($t) {
		$destinationstree = get_ancestors( $t->term_id, 'destinations' );
		$destinationstree = array_reverse($destinationstree);
		$destdepth = count($destinationstree);
		return $destdepth == 1;
	});

	$cities = array_filter($destinations, function ($t) {
		$destinationstree = get_ancestors( $t->term_id, 'destinations' );
		$destinationstree = array_reverse($destinationstree);
		$destdepth = count($destinationstree);
		return $destdepth == 2;
	});
	
	$datadestinations = array();
	$i = 0;

	// cities
	foreach ( $cities as $term ) {
		$datadestinations[$i] = array('id' => $term->term_id, 'slug' => $term->slug, 'name' => $term->name);

		$i++;
	}
	
	$j = $i;
	
	// regions
	foreach ( $regions as $term ) {
		$datadestinations[$i] = array('id' => $term->term_id, 'slug' => $term->slug, 'name' => $term->name);

		$i++;
	}

//	print_r ($datadestinations );
	
	header('Content-type: text/xml');
	echo '<destinations>';

    foreach($datadestinations as $index => $datadestination) {
    
    	echo '<destination>';
    
      if(is_array($datadestination)) {

        foreach($datadestination as $key => $value) {
          echo '<',$key,'>';
          echo $value;
          echo '</',$key,'>';
        }
      }

    	echo '</destination>';

    }
	
	echo '</destinations>';

  /* reset query */
  wp_reset_postdata();
?>