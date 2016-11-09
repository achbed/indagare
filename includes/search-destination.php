<?php

function render_destination_term($top, $reg, $term) {
	$output = '';
	
	$termdest = 'destinations' . '_' . $term->term_id;
	
	$text = wpautop( get_field('destination-overview', $termdest ) );
	$text = substr( $text, 0, strpos( $text, '</p>' ) + 4 );
	$text = substr( $text, strpos( $text, '<p>' ), strlen($text) -3 );
	$text = strip_tags($text, '<a><strong><em><b><i>');
	$text = str_replace(']]>', ']]>', $text);
	$excerpt_length = 20; // 20 words
	$excerpt_more = apply_filters('excerpt_more', '...');
	$overview = wp_trim_words( $text, $excerpt_length, $excerpt_more );


	$imageobj = _get_firstimage('header-image', 'thumb-large', SHR_FIRSTIMAGE_ALL, false, 'destinations' . '_' . $term->term_id);
	$image = $imageobj['src'];

	if ( $overview ) { // display only if destination has custom field content, regardless of whether it has posts associated with it
		$output .= '<article>'."\n";
	  $output .= '<a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $term->slug.'/">'."\n";
		if (!empty($image)) {
			$output .= '<img src="'.$image.'" alt="'.__('Destination','indagare').'" />'."\n";
		}
		$output .= '<h3>'.$term->name.'</h3>'."\n";
		$output .= '<p class="description">'.$overview.' <span class="read-more">'.__('Read More','indagare').'</span></p>'."\n";
		$output .= '</a>'."\n";
		$output .= '</article>'."\n";
	}
	return $output;
}
