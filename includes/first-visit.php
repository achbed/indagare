<?php

function first_visit() {
global $post;

	$args = array(
		'post_type'  => 'page',
		'meta_query' => array(
			array(
				'key'   => '_wp_page_template',
				'value' => 'template-page-new.php'
			)
		)
	);

	$firstvisit = get_posts($args);
	
	foreach( $firstvisit as $post ) : setup_postdata($post);

		$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-full' );
		$image = $imageobj[0];

		echo '<div id="lightbox-first" class="lightbox white-popup mfp-hide">'."\n";
		echo '<header>'."\n";
			echo '<h2>'.get_the_title().'</h2>'."\n";
			echo $post->post_content;

			if ( $image ) {
				echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
			}
			
		echo '</header>'."\n";

		$rows = get_field('new-features');
		
		if($rows) {

			echo  '<section class="all-destinations contain" location="GGGG">'."\n";
	
				foreach($rows as $row) {
	
					$newtitle = $row['new-features-title'];
					$newcontent = $row['new-features-content'];
					
					echo  '<article>'."\n";
						echo  '<h3>'.$newtitle.'</h3>'."\n";
						echo  $newcontent;
					echo  '</article>'."\n";
	
				}
	
			echo  '</section>'."\n";

		}


		echo  '<div class="header divider">'."\n";
		
		$imageobj = get_field('new-features-callout-image');
		$imgsrc = $imageobj['sizes']['thumb-medium'];
		
		if ( $imgsrc ) {
			echo '<div class="callout calloutimg"><img src="'.$imgsrc.'" /></div><div class="callout callouttext"><strong>'.get_field('new-features-callout-content').'</strong></div>'."\n";
		} else {
			echo '<div class="callout callouttext"><strong>'.get_field('new-features-callout-content').'</strong></div>'."\n";
		}
		
		echo  '</div>'."\n";

		echo  '<div class="header divider"><h2>'.__('Enter the new Indagare now &#8211; pick an article to experience the Indagare redesign:','indagare').'</p></div>'."\n";

		$rows = get_field('new-articles');
		
		if($rows) {
		
			echo  '<section class="related-articles contain">'."\n";
	
				foreach($rows as $row) {
				
						echo  '<article>'."\n";
						echo  '<a href="'.get_permalink($row).'">'."\n";
						echo  '<h3>'.get_the_title($row).'</h3>'."\n";
						echo  '</a>'."\n";
						echo  '</article>'."\n";
						
				}
	
			echo  '</section>'."\n";

		}

		echo '<footer class="newsletter-signup-wrapper">'."\n";
			echo '<h4>'.__('Not a member yet?','indagare').'</h4>'."\n";
			echo '<a class="button primary floatright" href="/join/">'.__('Join Now','indagare').'</a>'."\n";
			echo get_field('new-cta');
		echo '</footer>'."\n";
		echo '</div><!-- #lightbox-first -->'."\n";
	
	endforeach;
	
	wp_reset_postdata();

}

?>