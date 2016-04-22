<?php
/**
 * Template Name: XML Hotel URLs
 *
 * 
 */
 
	global $post;

	$args = array('numberposts' => -1, 'post_type' => 'hotel', 'orderby' => 'name', 'order' => 'ASC', 'post_status' => 'publish');
	$hotels = get_posts($args);

	$datahotelsurls = array();
	$i = 0;
	
	foreach( $hotels as $hotel ) : setup_postdata($hotel);

		$booking = get_field('booking',$hotel->ID);

		if ( $booking ) {
		
			if ( strlen($booking) < 7 ) {
				$booking = str_pad($booking, 7, "0", STR_PAD_LEFT);
			} else if ( strlen($booking) > 7 ) {
				$booking = substr($booking,-7);
			}

			$destinationstree = destinationstree($hotel->ID);
			$dest = $destinationstree['dest'];
			$reg = $destinationstree['reg'];

			$datahotelsurls[$i] = array('sabre_code' => $booking, 'hotel_url' => get_permalink($hotel->ID), 'hotel_name' => get_the_title($hotel->ID) );

			$i++;
		}
	
	endforeach;

//	print_r ($datahotelsurls );
	
	header('Content-type: text/xml');
	echo '<hotels>';

    foreach($datahotelsurls as $index => $datahotelsurl) {
    
    	echo '<hotel>';
    
      if(is_array($datahotelsurl)) {

        foreach($datahotelsurl as $key => $value) {
          echo '<',$key,'>';
          echo $value;
          echo '</',$key,'>';
        }
      }

    	echo '</hotel>';

    }
	
	echo '</hotels>';

  /* reset query */
  wp_reset_postdata();
?>